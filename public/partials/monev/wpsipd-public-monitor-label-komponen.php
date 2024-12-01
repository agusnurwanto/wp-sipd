<?php
// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}
$input = shortcode_atts(array(
    'id_label' => '',
    'tahun_anggaran' => '2022'
), $atts);

if (empty($input['id_label'])) {
    die('<h1>ID Label tidak boleh kosong!</h1>');
}

global $wpdb;
$type = 'murni';
if (!empty($_GET) && !empty($_GET['type'])) {
    $type = $_GET['type'];
}

$label_db = $wpdb->get_row(
    $wpdb->prepare("
        SELECT
            *
        FROM data_label_komponen
        WHERE active=1
          AND tahun_anggaran=%d
          AND id=%d
    ", $input['tahun_anggaran'], $input['id_label']),
    ARRAY_A
);
if (empty($label_db)) {
    die('<h1>ID Label ' . $input['id_label'] . $wpdb->last_query . ' tidak ditemukan di database!</h1>');
}

$data_label = array();
$where_skpd = '';
$inner_skpd = '';
$options    = '';
$query_params = '';
if (!empty($_GET) && !empty($_GET['id_skpd'])) {
    $inner_skpd = '
        INNER JOIN data_sub_keg_bl s 
                ON s.kode_sbl=r.kode_sbl
          AND s.active = r.active
          AND s.tahun_anggaran=r.tahun_anggaran';
    $where_skpd = 'AND s.id_sub_skpd=' . $_GET['id_skpd'];

    $data_skpd = $wpdb->get_row(
        $wpdb->prepare("
            SELECT 
                id_skpd,
                nama_skpd,
                kode_skpd 
            FROM data_unit 
            WHERE id_skpd = %d 
              AND tahun_anggaran = %d 
              AND active = 1
        ", $_GET['id_skpd'], $input['tahun_anggaran']),
        ARRAY_A
    );
    $query_params = '&id_skpd=' . $_GET['id_skpd'];

    $options .= '<option value="' . $data_skpd['id_skpd'] . '" selected>' . $data_skpd['kode_skpd'] . ' ' . $data_skpd['nama_skpd'] . '</option>';
} else {
    $data_skpd = $wpdb->get_results(
        $wpdb->prepare("
            SELECT 
                id_skpd,
                nama_skpd,
                kode_skpd 
            FROM data_unit 
            WHERE tahun_anggaran = %d 
              AND active = 1
            ORDER BY kode_skpd ASC
        ", $input['tahun_anggaran']),
        ARRAY_A
    );
    foreach ($data_skpd as $v) {
        $options .= '<option value="' . $v['id_skpd'] . '">' . $v['kode_skpd'] . ' ' . $v['nama_skpd'] . '</option>';
    }
}
$sql = $wpdb->prepare("
    SELECT 
        r.*,
        rr.realisasi 
    FROM `data_rka` r
        " . $inner_skpd . "
    INNER JOIN data_mapping_label m 
            ON m.active=r.active
           AND m.tahun_anggaran=r.tahun_anggaran
           AND m.id_rinci_sub_bl=r.id_rinci_sub_bl
    LEFT JOIN data_realisasi_rincian rr 
           ON rr.active=r.active
          AND rr.tahun_anggaran=r.tahun_anggaran
          AND rr.id_rinci_sub_bl=r.id_rinci_sub_bl
    WHERE r.active=1 
      AND r.tahun_anggaran=%d
      AND m.id_label_komponen=%d
        " . $where_skpd . "
    ORDER BY r.kode_sbl ASC
", $input['tahun_anggaran'], $input['id_label']);
$data = $wpdb->get_results($sql, ARRAY_A);
if (!empty($data)) {
    $data_label = $data;
}

$data_label_shorted = array(
    'data'        => array(),
    'realisasi'   => 0,
    'total_murni' => 0,
    'total'       => 0
);
foreach ($data_label as $k => $v) {
    $kode = explode('.', $v['kode_sbl']);
    $idskpd = $kode[1];
    $skpd = $wpdb->get_row(
        $wpdb->prepare("
            SELECT 
                id_skpd,
                nama_skpd,
                kode_skpd 
            FROM data_unit 
            WHERE id_skpd=%d 
              AND tahun_anggaran = %d 
              AND active = 1
        ", $idskpd, $input['tahun_anggaran']),
        ARRAY_A
    );
    if (empty($data_label_shorted['data'][$skpd['kode_skpd']])) {
        $data_label_shorted['data'][$skpd['kode_skpd']] = array(
            'nama'        => $skpd['nama_skpd'],
            'realisasi'   => 0,
            'total_murni' => 0,
            'total'       => 0,
            'data'        => array()
        );
    }
    $sub_keg = $wpdb->get_row(
        $wpdb->prepare("
            SELECT 
                kode_giat,
                nama_giat,
                nama_sub_giat
            FROM data_sub_keg_bl 
            WHERE kode_sbl = %s
              AND tahun_anggaran = %d
              AND active = 1
            ", $v['kode_sbl'], $input['tahun_anggaran']),
        ARRAY_A
    );
    if (empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']])) {
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']] = array(
            'kode_giat'         => $sub_keg['kode_giat'],
            'nama_giat'         => $sub_keg['nama_giat'],
            'kode_sbl'          => $v['kode_sbl'],
            'id_skpd'           => $skpd['id_skpd'],
            'nama_sub_giat'     => $sub_keg['nama_sub_giat'],
            'realisasi'         => 0,
            'total_murni'       => 0,
            'total'             => 0,
            'data'              => array()
        );
    }
    $kelompok = $v['idsubtitle'] . '||' . $v['subs_bl_teks'];
    if (empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok])) {
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok] = array(
            'nama'        => $v['subs_bl_teks'],
            'realisasi'   => 0,
            'total_murni' => 0,
            'total'       => 0,
            'data'        => array()
        );
    }
    $keterangan = $v['idketerangan'] . '||' . $v['ket_bl_teks'];
    if (empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan])) {
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan] = array(
            'nama'        => $v['ket_bl_teks'],
            'realisasi'   => 0,
            'total_murni' => 0,
            'total'       => 0,
            'data'        => array()
        );
    }
    if (empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']])) {
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']] = array(
            'nama'        => $v['nama_akun'],
            'realisasi'   => 0,
            'total_murni' => 0,
            'total'       => 0,
            'data'        => array()
        );
    }
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['data'][] = $v;
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['total'] += $v['rincian'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['total'] += $v['rincian'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['total'] += $v['rincian'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total'] += $v['rincian'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['total'] += $v['rincian'];
    $data_label_shorted['total'] += $v['rincian'];

    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['total_murni'] += $v['rincian_murni'];

    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['realisasi'] += $v['realisasi'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['realisasi'] += $v['realisasi'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['realisasi'] += $v['realisasi'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['realisasi'] += $v['realisasi'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['realisasi'] += $v['realisasi'];
    $data_label_shorted['realisasi'] += $v['realisasi'];
}
ksort($data_label_shorted['data']);

$jumlah_rincian = 0;
$body_label = '';
foreach ($data_label_shorted['data'] as $k => $skpd) {
    $murni = '';
    $selisih = '';
    if ($type == 'pergeseran') {
        $murni_value = $skpd['total_murni'] ?? 0;
        $selisih_value = ($skpd['total'] ?? 0) - $murni_value;
        $murni = "<td class='kanan bawah text_kanan text_blok'>" . number_format($murni_value, 0, ",", ".") . "</td>";
        $selisih = "<td class='kanan bawah text_kanan text_blok'>" . number_format($selisih_value, 0, ",", ".") . "</td>";
    }
    $penyerapan = 0;
    $penyerapan = (!empty($skpd['total']) && $skpd['total'] != 0)
        ? $this->pembulatan(($skpd['realisasi'] / $skpd['total']) * 100)
        : 0;
    $nama_page = 'RFK ' . $skpd['nama'] . ' ' . $k . ' | ' . $v['tahun_anggaran'];
    $custom_post = get_page_by_title($nama_page, OBJECT, 'page');
    $body_label .= '
        <tr>
            <td class="kanan bawah kiri text_tengah text_blok"></td>
            <td class="kanan bawah text_blok" colspan="2"><a href="' . get_permalink($custom_post) . '?key=' . $this->gen_key() . '&pagu_dpa=sipd" target="_blank">' . $k . ' ' . $skpd['nama'] . '</a></td>
            <td class="kanan bawah kiri text_tengah text_blok actionBtn"></td>
            ' . $murni . '
            <td class="kanan bawah text_blok text_kanan">' . number_format($skpd['total'] ?? 0, 0, ",", ".") . '</td>
            ' . $selisih . '
            <td class="kanan bawah text_blok text_kanan">' . number_format($skpd['realisasi'] ?? 0, 0, ",", ".") . '</td>
            <td class="kanan bawah text_blok text_kanan">' . $penyerapan . '</td>
            <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
        </tr>
    ';
    foreach ($skpd['data'] as $sub_keg) {
        $murni = '';
        $selisih = '';
        $btn_edit = '<span class="actionBtn edit-monev ml-2" onclick="edit_data_subkeg(\'' . $sub_keg['kode_sbl'] . '\', ' . $sub_keg['id_skpd'] . ');" title="Edit Sub Kegiatan"><i class="dashicons dashicons-edit"></i></span>';
        if ($type == 'pergeseran') {
            $murni_value = $sub_keg['total_murni'] ?? 0;
            $selisih_value = ($sub_keg['total'] ?? 0) - $murni_value;
            $murni = "<td class='kanan bawah text_kanan text_blok'>" . number_format($murni_value, 0, ",", ".") . "</td>";
            $selisih = "<td class='kanan bawah text_kanan text_blok'>" . number_format($selisih_value, 0, ",", ".") . "</td>";
        }
        $penyerapan = 0;
        $penyerapan = (!empty($sub_keg['total']) && $sub_keg['total'] != 0)
            ? $this->pembulatan(($sub_keg['realisasi'] / $sub_keg['total']) * 100)
            : 0;
        $nama_page = $input['tahun_anggaran'] . ' | ' . $k . ' | ' . $sub_keg['kode_giat'] . ' | ' . $sub_keg['nama_giat'];
        $custom_post = get_page_by_title($nama_page, OBJECT, 'post');
        $link = $this->get_link_post($custom_post);
        $body_label .= '
            <tr class="sub_keg">
                <td class="kanan bawah kiri text_tengah text_blok"></td>
                <td class="kanan bawah text_blok" colspan="2" style="padding-left: 20px;"><a href="' . $link . '" target="_blank">' . $sub_keg['nama_sub_giat'] . '</a></td>
                <td class="kanan bawah kiri text_tengah text_blok actionBtn">' . $btn_edit . '</td>
                ' . $murni . '
                <td class="kanan bawah text_blok text_kanan">' . number_format($sub_keg['total'] ?? 0, 0, ",", ".") . '</td>
                ' . $selisih . '
                <td class="kanan bawah text_blok text_kanan">' . number_format($sub_keg['realisasi'] ?? 0, 0, ",", ".") . '</td>
                <td class="kanan bawah text_blok text_kanan">' . $penyerapan . '</td>
                <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
            </tr>
        ';
        foreach ($sub_keg['data'] as $kel) {
            $murni = '';
            $selisih = '';
            if ($type == 'pergeseran') {
                $murni = "<td class='kanan bawah text_kanan text_blok'>" . number_format($kel['total_murni'] ?? 0, 0, ",", ".") . "</td>";
                $selisih = "<td class='kanan bawah text_kanan text_blok'>" . number_format(($kel['total'] - $kel['total_murni']) ?? 0, 0, ",", ".") . "</td>";
            }
            $penyerapan = 0;
            $penyerapan = (!empty($kel['total']) && $kel['total'] != 0)
                ? $this->pembulatan(($kel['realisasi'] / $kel['total']) * 100)
                : 0;
            $body_label .= '
                <tr class="kelompok">
                    <td class="kanan bawah kiri text_tengah text_blok"></td>
                    <td class="kanan bawah text_blok" colspan="2" style="padding-left: 40px;">' . $kel['nama'] . '</td>
                    <td class="kanan bawah kiri text_tengah text_blok actionBtn"></td>
                    ' . $murni . '
                    <td class="kanan bawah text_blok text_kanan">' . number_format($kel['total'] ?? 0, 0, ",", ".") . '</td>
                    ' . $selisih . '
                    <td class="kanan bawah text_blok text_kanan">' . number_format($kel['realisasi'] ?? 0, 0, ",", ".") . '</td>
                    <td class="kanan bawah text_blok text_kanan">' . $penyerapan . '</td>
                    <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
                </tr>
            ';
            foreach ($kel['data'] as $ket) {
                $murni = '';
                $selisih = '';
                if ($type == 'pergeseran') {
                    $murni = "<td class='kanan bawah text_kanan text_blok'>" . number_format($ket['total_murni'] ?? 0, 0, ",", ".") . "</td>";
                    $selisih = "<td class='kanan bawah text_kanan text_blok'>" . number_format(($ket['total'] - $ket['total_murni']) ?? 0, 0, ",", ".") . "</td>";
                }
                $penyerapan = 0;
                $penyerapan = (!empty($ket['total']) && $ket['total'] != 0)
                    ? $this->pembulatan(($ket['realisasi'] / $ket['total']) * 100)
                    : 0;
                $body_label .= '
                    <tr class="keterangan">
                        <td class="kanan bawah kiri text_tengah text_blok"></td>
                        <td class="kanan bawah text_blok" colspan="2" style="padding-left: 60px;">' . $ket['nama'] . '</td>
                        <td class="kanan bawah kiri text_tengah text_blok actionBtn"></td>
                        ' . $murni . '
                        <td class="kanan bawah text_blok text_kanan">' . number_format($ket['total'] ?? 0, 0, ",", ".") . '</td>
                        ' . $selisih . '
                        <td class="kanan bawah text_blok text_kanan">' . number_format($ket['realisasi'] ?? 0, 0, ",", ".") . '</td>
                        <td class="kanan bawah text_blok text_kanan">' . $penyerapan . '</td>
                        <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
                    </tr>
                ';
                ksort($ket['data']);
                foreach ($ket['data'] as $akun) {
                    $murni = '';
                    $selisih = '';
                    if ($type == 'pergeseran') {
                        $murni = "<td class='kanan bawah text_kanan text_blok'>" . number_format($akun['total_murni'] ?? 0, 0, ",", ".") . "</td>";
                        $selisih = "<td class='kanan bawah text_kanan text_blok'>" . number_format(($akun['total'] - $akun['total_murni']) ?? 0, 0, ",", ".") . "</td>";
                    }
                    $penyerapan = 0;
                    $penyerapan = (!empty($akun['total']) && $akun['total'] != 0)
                        ? $this->pembulatan(($akun['realisasi'] / $akun['total']) * 100)
                        : 0;
                    $body_label .= '
                        <tr class="rekening">
                            <td class="kanan bawah kiri text_tengah text_blok"></td>
                            <td class="kanan bawah text_blok" colspan="2" style="padding-left: 80px;">' . $akun['nama'] . '</td>
                            <td class="kanan bawah kiri text_tengah text_blok actionBtn"></td>
                            ' . $murni . '
                            <td class="kanan bawah text_blok text_kanan">' . number_format($akun['total'] ?? 0, 0, ",", ".") . '</td>
                            ' . $selisih . '
                            <td class="kanan bawah text_blok text_kanan">' . number_format($akun['realisasi'] ?? 0, 0, ",", ".") . '</td>
                            <td class="kanan bawah text_blok text_kanan">' . $penyerapan . '</td>
                            <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
                        </tr>
                    ';
                    $no = 0;
                    foreach ($akun['data'] as $rincian) {
                        $no++;
                        $jumlah_rincian++;
                        $btn_delete = '<span class="actionBtn delete-monev ml-2" onclick="hapus_data_rincian(' . $rincian['id_rinci_sub_bl'] . ');" title="Hapus Rincian Belanja"><i class="dashicons dashicons-trash"></i></span>';
                        $alamat_array = $this->get_alamat($input, $rincian);
                        $alamat = $alamat_array['alamat'];
                        $lokus_akun_teks = $alamat_array['lokus_akun_teks'];
                        $murni = '';
                        $selisih = '';
                        if ($type == 'pergeseran') {
                            $murni = "<td class='kanan bawah text_kanan'>" . number_format($rincian['rincian_murni'] ?? 0, 0, ",", ".") . "</td>";
                            $selisih = "<td class='kanan bawah text_kanan'>" . number_format(($rincian['rincian'] - $rincian['rincian_murni']) ?? 0, 0, ",", ".") . "</td>";
                        }
                        $penyerapan = 0;
                        $penyerapan = (!empty($rincian['rincian']) && $rincian['rincian'] != 0)
                            ? $this->pembulatan(($rincian['realisasi'] / $rincian['rincian']) * 100)
                            : 0;

                        $body_label .= '
                            <tr class="rincian" data-db="' . $rincian['id_rinci_sub_bl'] . '|' . $rincian['kode_sbl'] . '" data-lokus-teks="' . $lokus_akun_teks . '">
                                <td class="kanan bawah kiri text_tengah">' . $no . '</td>
                                <td class="kanan bawah" style="padding-left: 100px;">' . $rincian['lokus_akun_teks'] . $rincian['nama_komponen'] . '</td>
                                <td class="kanan bawah">' . $alamat . $rincian['spek_komponen'] . '</td>
                                <td class="kanan bawah kiri text_tengah text_blok actionBtn">' . $btn_delete . '</td>
                                ' . $murni . '
                                <td class="kanan bawah text_kanan">' . number_format($rincian['rincian'] ?? 0, 0, ",", ".") . '</td>
                                ' . $selisih . '
                                <td class="kanan bawah text_kanan">' . number_format($rincian['realisasi'] ?? 0, 0, ",", ".") . '</td>
                                <td class="kanan bawah text_kanan">' . $penyerapan . '</td>
                                <td class="kanan bawah text_tengah">' . $rincian['volume'] . '</td>
                                <td class="kanan bawah text_tengah">' . $rincian['satuan'] . '</td>
                            </tr>
                        ';
                    }
                }
            }
        }
        $murni = '';
        $selisih = '';
        if ($type == 'pergeseran') {
            $murni = "<td class='kanan bawah text_kanan text_blok'>" . number_format($sub_keg['total_murni'] ?? 0, 0, ",", ".") . "</td>";
            $selisih = "<td class='kanan bawah text_kanan text_blok'>" . number_format(($sub_keg['total'] - $sub_keg['total_murni']) ?? 0, 0, ",", ".") . "</td>";
        }
        $penyerapan = 0;
        $penyerapan = (!empty($sub_keg['total']) && $sub_keg['total'] != 0)
            ? $this->pembulatan(($sub_keg['realisasi'] / $sub_keg['total']) * 100)
            : 0;

        $body_label .= '
            <tr>
                <td class="kanan bawah kiri text_tengah text_blok">&nbsp;</td>
                <td class="kanan bawah text_blok text_kanan" colspan="2">Jumlah Pada Sub Kegiatan</td>
                <td class="kanan bawah text_blok text_kanan actionBtn"></td>
                ' . $murni . '
                <td class="kanan bawah text_blok text_kanan">' . number_format($sub_keg['total'] ?? 0, 0, ",", ".") . '</td>
                ' . $selisih . '
                <td class="kanan bawah text_blok text_kanan">' . number_format($sub_keg['realisasi'] ?? 0, 0, ",", ".") . '</td>
                <td class="kanan bawah text_blok text_kanan">' . $penyerapan . '</td>
                <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
            </tr>
        ';
    }
    $murni = '';
    $selisih = '';
    if ($type == 'pergeseran') {
        $murni = "<td class='kanan bawah text_kanan text_blok'>" . number_format($skpd['total_murni'] ?? 0, 0, ",", ".") . "</td>";
        $selisih = "<td class='kanan bawah text_kanan text_blok'>" . number_format(($skpd['total'] - $skpd['total_murni']) ?? 0, 0, ",", ".") . "</td>";
    }
    $penyerapan = 0;
    $penyerapan = (!empty($skpd['total']) && $skpd['total'] != 0)
        ? $this->pembulatan(($skpd['realisasi'] / $skpd['total']) * 100)
        : 0;

    $body_label .= '
        <tr>
            <td class="kanan bawah kiri text_tengah text_blok">&nbsp;</td>
            <td class="kanan bawah text_blok text_kanan" colspan="2">Jumlah Pada SKPD</td>
            <td class="kanan bawah kiri text_tengah text_blok actionBtn"></td>
            ' . $murni . '
            <td class="kanan bawah text_blok text_kanan">' . number_format($skpd['total'] ?? 0, 0, ",", ".") . '</td>
            ' . $selisih . '
            <td class="kanan bawah text_blok text_kanan">' . number_format($skpd['realisasi'] ?? 0, 0, ",", ".") . '</td>
            <td class="kanan bawah text_blok text_kanan">' . $penyerapan . '</td>
            <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
        </tr>
    ';
}
$murni = '';
$selisih = '';
if ($type == 'pergeseran') {
    $murni = "<td class='kanan bawah text_kanan text_blok'>" . number_format($data_label_shorted['total_murni'] ?? 0, 0, ",", ".") . "</td>";
    $selisih = "<td class='kanan bawah text_kanan text_blok'>" . number_format(($data_label_shorted['total'] - $data_label_shorted['total_murni']) ?? 0, 0, ",", ".") . "</td>";
}
$penyerapan = 0;
$penyerapan = (!empty($data_label_shorted['total']) && $data_label_shorted['total'] != 0)
    ? $this->pembulatan(($data_label_shorted['realisasi'] / $data_label_shorted['total']) * 100)
    : 0;
$body_label .= '
    <tr>
        <td class="kiri kanan bawah text_blok text_kanan" colspan="3">Jumlah Total</td>
        <td class="kiri kanan bawah text_blok text_kanan actionBtn"></td>
        ' . $murni . '
        <td class="kanan bawah text_blok text_kanan">' . number_format($data_label_shorted['total'] ?? 0, 0, ",", ".") . '</td>
        ' . $selisih . '
        <td class="kanan bawah text_blok text_kanan">' . number_format($data_label_shorted['realisasi'] ?? 0, 0, ",", ".") . '</td>
        <td class="kanan bawah text_blok text_kanan">' . $penyerapan . '</td>
        <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
    </tr>
';
//set bg if greater than
$style_color = '';
if ($label_db['rencana_pagu'] < $data_label_shorted['total']) {
    $style_color = 'background-color : #FFADAD;';
}
?>
<style>
    /* Level 1 */
    .akun-row td {
        background-color: #adf7b6;
        color: #212529;
        font-weight: bold;
    }

    /* Level 2 */
    .subs-row td {
        background-color: #ffee93;
        color: #212529;
    }

    /* Level 3 */
    .ket-row td {
        background-color: #ffc09f;
        color: #212529;
    }

    /* Level 4 */
    .rinci-row td {
        background-color: #f8f9fa;
        color: #212529;
    }

    /* Total di setiap level */
    .akun-total,
    .subs-total,
    .ket-total,
    .rinci-total {
        font-weight: bold;
    }


    @media print {
        #cetak {
            max-width: auto !important;
            height: auto !important;
        }

        #action-sipd,
        .btnTambah,
        .actionBtn,
        header {
            display: none;
        }
    }
</style>
<div id="cetak" title="Monitoring dan Evaluasi Label Komponen <?php echo $label_db['nama']; ?> Tahun Anggaran <?php echo $input['tahun_anggaran']; ?>" style="padding: 5px;">
    <div class="text-center mx-auto my-3" style="min-width: 450px; max-width: 570px;">
        <h4 class="font-weight-bold mb-2">
            Monitoring dan Evaluasi Label Komponen
        </h4>
        <h5 class="font-weight-bold mb-2">
            Tahun Anggaran <?php echo htmlspecialchars($input['tahun_anggaran']); ?>
        </h5>
    </div>
    <div class="m-4 text-center btnTambah">
        <button class="btn btn-primary" onclick="showModalTambah();">
            <span class="dashicons dashicons-insert"></span> Tambah Data
        </button>
    </div>

    <div class="table-responsive" style="overflow-x: auto;">
        <table style="width: 100%; border: 1px solid #dee2e6; border-collapse: collapse;">
            <thead style="background-color: #bde0fe; color: #212529; text-align: center;">
                <tr>
                    <th class="atas kanan bawah kiri text_tengah" style="padding: 8px;" colspan="2">Nama Label</th>
                    <th class="atas kanan bawah kiri text_tengah" style="padding: 8px;" colspan="2">Keterangan Label</th>
                </tr>
            </thead>
            <tbody>
                <tr style="text-align: center;">
                    <td class="bg-light atas kanan bawah kiri text_tengah" style="background-color: #f8f9fa; padding: 8px;" colspan="2"><?php echo $label_db['nama']; ?></td>
                    <td class="bg-light atas kanan bawah kiri text_tengah" style="background-color: #f8f9fa; padding: 8px;" colspan="2"><?php echo $label_db['keterangan']; ?></td>
                </tr>
            </tbody>
            <thead style="background-color: #bde0fe; color: #212529; text-align: center;">
                <tr>
                    <th class="atas kanan bawah kiri text_tengah" style="padding: 8px;">Rencana Pagu</th>
                    <th class="atas kanan bawah kiri text_tengah" style="padding: 8px;">Total Pagu Rincian</th>
                    <th class="atas kanan bawah kiri text_tengah" style="padding: 8px;">Jumlah Rincian</th>
                    <th class="atas kanan bawah kiri text_tengah" style="padding: 8px;">Total Realisasi</th>
                </tr>
            </thead>
            <tbody>
                <tr style="text-align: center;">
                    <td class="atas kanan bawah kiri text_tengah" style="padding: 8px; <?php echo $style_color; ?>"><?php echo number_format($label_db['rencana_pagu'] ?? 0, 0, ",", "."); ?></td>
                    <td class="atas kanan bawah kiri text_tengah" style="padding: 8px; <?php echo $style_color; ?>"><?php echo number_format($data_label_shorted['total'] ?? 0, 0, ",", "."); ?></td>
                    <td class="bg-light atas kanan bawah kiri text_tengah" style="background-color: #f8f9fa; padding: 8px;"><?php echo $jumlah_rincian; ?></td>
                    <td class="bg-light atas kanan bawah kiri text_tengah" style="background-color: #f8f9fa; padding: 8px;"><?php echo number_format($data_label_shorted['realisasi'] ?? 0, 0, ",", "."); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td rowspan="2" class="atas kanan bawah kiri text_tengah text_blok">No</td>
                <td rowspan="2" class="atas kanan bawah text_tengah text_blok">SKPD / Sub Kegiatan / Komponen</td>
                <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Keterangan</td>
                <td rowspan="2" class="atas kanan bawah text_tengah text_blok actionBtn">Aksi</td>
                <?php if ($type == 'murni'): ?>
                    <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Anggaran</td>
                <?php else: ?>
                    <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Sebelum Perubahan</td>
                    <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Sesudah Perubahan</td>
                    <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Bertambah/(Berkurang)</td>
                <?php endif; ?>
                <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Realisasi</td>
                <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Penyerapan</td>
                <td colspan="2" class="atas kanan bawah text_tengah text_blok">Capaian Output</td>
            </tr>
            <tr>
                <td class="atas kanan bawah text_tengah text_blok">Volume</td>
                <td class="atas kanan bawah text_tengah text_blok">Satuan</td>
            </tr>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok">1</td>
                <td class="atas kanan bawah text_tengah text_blok">2</td>
                <td class="atas kanan bawah text_tengah text_blok">3</td>
                <td class="atas kanan bawah text_tengah text_blok actionBtn">-</td>
                <?php if ($type == 'murni'): ?>
                    <td class="atas kanan bawah text_tengah text_blok">4</td>
                    <td class="atas kanan bawah text_tengah text_blok">5</td>
                    <td class="atas kanan bawah text_tengah text_blok">6=(5/4)*100</td>
                    <td class="atas kanan bawah text_tengah text_blok">7</td>
                    <td class="atas kanan bawah text_tengah text_blok">8</td>
                <?php else: ?>
                    <td class="atas kanan bawah text_tengah text_blok">4</td>
                    <td class="atas kanan bawah text_tengah text_blok">5</td>
                    <td class="atas kanan bawah text_tengah text_blok">6=(5-4)</td>
                    <td class="atas kanan bawah text_tengah text_blok">7</td>
                    <td class="atas kanan bawah text_tengah text_blok">8=(7/5)*100</td>
                    <td class="atas kanan bawah text_tengah text_blok">9</td>
                    <td class="atas kanan bawah text_tengah text_blok">10</td>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_label; ?>
        </tbody>
    </table>
</div>

<!-- modal tambah data -->
<div class="modal fade mt-4" id="modalTambahData" tabindex="-1" role="dialog" aria-labelledby="modalTambahData" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-label"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_data" name="id_data">

                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="labelTag">Label / Tag Rincian Belanja</label>
                                <input type="text" name="labelTag" class="form-control" id="labelTag" value="<?php echo $label_db['nama']; ?>" disabled>
                            </div>
                        </div>
                        <div class="form-row" id="idSkpdSelect">
                            <div class="form-group col-md-12">
                                <label for="idSkpd">Pilih SKPD</label>
                                <select name="idSkpd" class="form-control" id="idSkpd" onchange="handleSkpdChange()">
                                    <option value="">Pilih SKPD</option>
                                    <?php echo $options; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row" id="idSkpdTeks">
                            <div class="form-group col-md-12">
                                <label for="idSkpdText">Nama SKPD</label>
                                <input type="text" name="idSkpdText" class="form-control" id="idSkpdText" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="program">Program</label>
                                <input type="text" name="program" class="form-control" id="program" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="kegiatan">Kegiatan</label>
                                <input type="text" name="kegiatan" class="form-control" id="kegiatan" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="subKegiatan">Pilih Sub Kegiatan</label>
                                <select name="subKegiatan" class="form-control" id="subKegiatan" onchange="handleSubkegChange(this)" disabled>
                                </select>
                            </div>
                        </div>
                        <div class="mt-2" style="text-align: right;">
                            <button class="btn btn-warning" id="btnPreviewData" onclick="handleViewRinciBtn()">
                                <span class="dashicons dashicons-visibility"></span> Lihat Rincian Belanja
                            </button>
                        </div>

                        <table id="tableRincian" class="mt-2 table table-hover" style="display: none;">
                            <thead style="background-color: #343a40; color: #fff; text-align: center;">
                                <tr>
                                    <th scope="col" class="text-center">
                                        <input type="checkbox" value="" id="flexCheckDefault">
                                    </th>
                                    <th scope="col" class="text-center">Nama Akun / Rincian Belanja</th>
                                    <th scope="col" class="text-center">Volume</th>
                                    <th scope="col" class="text-center">Satuan</th>
                                    <th scope="col" class="text-center">Anggaran</th>
                                    <th scope="col" class="text-center">Realisasi</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" onclick="simpanTagRinciBl()">Simpan</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(() => {
        run_download_excel('apbd');
        window.data_changed = false

        var _url = window.location.href;
        var url = new URL(_url);
        var baseUrl = url.origin + url.pathname + '?key=' + url.searchParams.get('key');
        var type = url.searchParams.get("type");
        var id_skpd = url.searchParams.get("id_skpd");

        if (id_skpd) {
            baseUrl += '&id_skpd=' + id_skpd;
        }

        if (type && type === 'pergeseran') {
            var extend_action = '<a class="btn btn-primary m-2" target="_blank" href="' + baseUrl + '"><span class="dashicons dashicons-controls-back"></span> Halaman Monev Murni</a>';
        } else {
            var extend_action = '<a class="btn btn-primary m-2" target="_blank" href="' + baseUrl + '&type=pergeseran"><span class="dashicons dashicons-controls-forward"></span> Halaman Monev Pergeseran/Perubahan</a>';
        }

        extend_action += '<button class="btn btn-info m-2" id="print_laporan" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Cetak Laporan</button>';

        jQuery('#action-sipd #excel').after(extend_action);

        jQuery('#idSkpd').select2({
            width: '100%',
            dropdownParent: jQuery('#modalTambahData') // Tentukan modal sebagai parent dropdown agar select2 search tidak error
        });

        jQuery("#tableRincian").on("input", ".realisasiRincian", function() {
            let value = jQuery(this).val().replace(/[^0-9]/g, "");

            let formattedValue = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            jQuery(this).val(formattedValue);
        });

        jQuery('#modalTambahData').on('hidden.bs.modal', function() {
            // Tampilkan konfirmasi setelah modal tertutup dan ada data realisasi berubah
            if (window.data_changed === true) {
                if (confirm('Data realisasi telah berubah. Apakah Anda ingin merefresh halaman?')) {
                    location.reload(); // Refresh halaman
                }
            }
        });


    });

    function handleViewRinciBtn() {
        return new Promise((resolve, reject) => {
            const kode_sbl = jQuery("#subKegiatan").val();
            if (kode_sbl) {
                jQuery("#wrap-loading").show();

                jQuery.ajax({
                    url: ajax.url,
                    type: "POST",
                    data: {
                        action: "get_sub_keg_rka_sipd",
                        api_key: ajax.api_key,
                        tahun_anggaran: '<?php echo $input["tahun_anggaran"]; ?>',
                        kode_sbl: kode_sbl,
                        id_label: '<?php echo $input["id_label"]; ?>' //mapping checked
                    },
                    dataType: "json",
                    success: function(response) {
                        jQuery("#wrap-loading").hide();
                        console.log(response);

                        if (response.status === "success") {
                            const data = response.data;

                            const tableBody = jQuery("#tableRincian tbody");
                            tableBody.empty();

                            function formatNumber(value) {
                                return new Intl.NumberFormat("id-ID").format(value);
                            }

                            // Group data by kode_akun
                            const groupedData = {};
                            data.forEach((item) => {
                                const namaAkun = item.nama_akun.replace(/^\S+(\.\S+)*\s/, ""); // 
                                const kodeAkun = item.kode_akun;
                                const subsBl = item.subs_bl_teks;
                                const ketBl = item.ket_bl_teks;
                                const totalHarga = parseFloat(item.total_harga) || 0;
                                const totalRealisasi = parseFloat(item.realisasi_rincian) || 0;

                                if (!groupedData[kodeAkun]) {
                                    groupedData[kodeAkun] = {
                                        namaAkun: namaAkun,
                                        total: 0,
                                        total_realisasi: 0,
                                        subs: {},
                                    };
                                }

                                if (!groupedData[kodeAkun].subs[subsBl]) {
                                    groupedData[kodeAkun].subs[subsBl] = {
                                        total: 0,
                                        total_realisasi: 0,
                                        ket: {},
                                    };
                                }

                                if (!groupedData[kodeAkun].subs[subsBl].ket[ketBl]) {
                                    groupedData[kodeAkun].subs[subsBl].ket[ketBl] = {
                                        total: 0,
                                        total_realisasi: 0,
                                        rincian: [],
                                    };
                                }

                                groupedData[kodeAkun].subs[subsBl].ket[ketBl].rincian.push(item);
                                groupedData[kodeAkun].subs[subsBl].ket[ketBl].total += totalHarga;
                                groupedData[kodeAkun].subs[subsBl].total += totalHarga;
                                groupedData[kodeAkun].total += totalHarga;

                                groupedData[kodeAkun].subs[subsBl].ket[ketBl].total_realisasi += totalRealisasi;
                                groupedData[kodeAkun].subs[subsBl].total_realisasi += totalRealisasi;
                                groupedData[kodeAkun].total_realisasi += totalRealisasi;
                            });


                            Object.keys(groupedData).forEach((kodeAkun) => {
                                const akunData = groupedData[kodeAkun];

                                tableBody.append(`
                                    <tr class="akun-row" data-id="${kodeAkun}">
                                        <td class="text-center">
                                            <input class="akun-checkbox" type="checkbox" value="${kodeAkun}">
                                        </td>
                                        <td colspan="3" class="font-weight-bold text-left">${kodeAkun} ${akunData.namaAkun}
                                        </td>
                                        <td class="font-weight-bold text-right akun-total akun-row">
                                            ${formatNumber(akunData.total)}
                                        </td>
                                        <td class="text-right akun-total akun-row"> ${formatNumber(akunData.total_realisasi)}</td>
                                        <td>
                                        </td>
                                    </tr>
                                `);


                                // Baris subs_bl_teks (level 2)
                                Object.keys(akunData.subs).forEach((subsBl) => {
                                    const subsData = akunData.subs[subsBl];

                                    tableBody.append(`
                                        <tr class="subs-row" data-parent-id="${kodeAkun}" data-id="${subsBl}">
                                            <td class="text-center">
                                                <input class="subs-checkbox" type="checkbox" value="${subsBl}">
                                            </td>
                                            <td class="font-weight-bold text-left" colspan="3" class="subs-total">
                                                ${subsBl} 
                                            </td>
                                            <td class="font-weight-bold text-right">
                                                ${formatNumber(subsData.total)}
                                            </td>
                                            <td class="subs-total text-right">
                                                ${formatNumber(subsData.total_realisasi)}
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    `);


                                    // Baris ket_bl_teks (level 3)
                                    Object.keys(subsData.ket).forEach((ketBl) => {
                                        const ketData = subsData.ket[ketBl];

                                        tableBody.append(`
                                            <tr class="ket-row" data-parent-id="${subsBl}" data-id="${ketBl}" data-grandparent-id="${kodeAkun}">
                                                <td class="text-center">
                                                    <input class="ket-checkbox" type="checkbox" value="${ketBl}">
                                                </td>
                                                <td colspan="3" class="font-weight-bold text-left ket-total text-dark">
                                                    ${ketBl} 
                                                </td>
                                                <td class="font-weight-bold text-right">
                                                    ${formatNumber(ketData.total)}
                                                </td>
                                                <td class="text-right ket-total text-dark">
                                                    ${formatNumber(ketData.total_realisasi)}
                                                </td>
                                                <td>
                                                </td>
                                            </tr>
                                        `);


                                        // Baris id_rinci_sub_bl (level 4)
                                        ketData.rincian.forEach((rinci) => {
                                            const isChecked = rinci.is_checked ? "checked" : "";
                                            const realisasiValue = rinci.realisasi_rincian ? parseInt(rinci.realisasi_rincian, 10) : 0;

                                            tableBody.append(`
                                                <tr class="rinci-row" data-id="${rinci.id_rinci_sub_bl}" data-parent-id="${ketBl}" data-grandparent-id="${subsBl}" data-greatgrandparent-id="${kodeAkun}">
                                                    <td class="text-center">
                                                        <input class="rinci-checkbox" type="checkbox" value="${rinci.id_rinci_sub_bl}" ${isChecked}>
                                                    </td>
                                                    <td class="text-left">${rinci.nama_komponen}</td>
                                                    <td class="text-right">${rinci.volume}</td>
                                                    <td class="text-right">${rinci.satuan}</td>
                                                    <td class="text-right rinci-total bg-light text-dark">
                                                        ${formatNumber(rinci.total_harga)}
                                                    </td>
                                                    <td class="text-right rinci-total bg-light text-dark">
                                                        <input type="text" class="realisasiRincian" value="${formatNumber(realisasiValue)}" id="realisasiRincian${rinci.id_rinci_sub_bl}">
                                                    </td>
                                                    <td class="text-center rinci-total bg-light text-dark">
                                                        <button class="btn btn-primary" onclick="simpanRealisasi(${rinci.id_rinci_sub_bl})">
                                                            <span class="dashicons dashicons-saved" title="Simpan Realisasi Rincian"></span>
                                                        </button>
                                                    </td>
                                                </tr>
                                            `);

                                            if (rinci.is_checked) {
                                                jQuery(`#checkbox-${rinci.id_rinci_sub_bl}`).prop('checked', true);
                                            }
                                        });
                                    });
                                });
                            });

                            jQuery("#tableRincian").show();

                            // Handle Checkbox Logic
                            handleCheckboxLogic();
                            resolve()
                        } else {
                            alert(response.message);
                            reject(new Error(response.message));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        jQuery("#wrap-loading").hide();
                        alert("Terjadi kesalahan saat memuat rincian data!");
                        reject(new Error("Terjadi kesalahan saat mengirim data!"));
                    },
                });
            }
        })
    }

    function showModalTambah() {
        jQuery("#idSkpdSelect").show();
        jQuery("#idSkpdTeks").hide();
        jQuery('#idSkpd').val('').trigger('change')
        jQuery("#program").val('')
        jQuery("#kegiatan").val('')
        jQuery('#subKegiatan').empty('').prop("disabled", true)
        jQuery('#rincianBelanja').val('')
        jQuery("#tableRincian").hide()
        jQuery("#title-label").text('Tambah Tagging / Label Rincian Belanja')
        jQuery('#modalTambahData').modal('show')
    }

    function simpanTagRinciBl() {
        // Ambil semua checkbox rincian yang dicheck
        let checkedRinci = [];
        jQuery(".rinci-checkbox:checked").each(function() {
            checkedRinci.push(jQuery(this).val());
        });

        if (checkedRinci.length === 0) {
            return alert("Harap pilih rincian belanja yang akan ditag!");
        }

        let id_label = <?php echo $input['id_label']; ?>;
        let tahun_anggaran = <?php echo $input['tahun_anggaran']; ?>;
        let kode_sbl = jQuery('#subKegiatan').val();

        const tempData = new FormData();
        tempData.append("action", "tambah_label_rinci_bl");
        tempData.append("api_key", ajax.api_key);
        tempData.append("rincian_belanja_ids", JSON.stringify(checkedRinci));
        tempData.append("id_label", id_label);
        tempData.append("tahun_anggaran", tahun_anggaran);
        tempData.append("kode_sbl", kode_sbl);

        jQuery("#wrap-loading").show();

        jQuery.ajax({
            method: "post",
            url: ajax.url,
            dataType: "json",
            data: tempData,
            processData: false,
            contentType: false,
            cache: false,
            success: function(res) {
                alert(res.message);
                jQuery("#wrap-loading").hide();
                if (res.status === "success") {
                    location.reload();
                    jQuery('#modalTambahData').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                jQuery("#wrap-loading").hide();
                alert("Terjadi kesalahan saat mengirim data!");
            },
        });
    }


    function handleCheckboxLogic() {
        // Checkbox utama (select all)
        jQuery("#flexCheckDefault").on("change", function() {
            const isChecked = jQuery(this).is(":checked");
            jQuery(".akun-checkbox, .subs-checkbox, .ket-checkbox, .rinci-checkbox").prop("checked", isChecked);
        });

        // Akun ke subs_bl_teks
        jQuery(".akun-checkbox").on("change", function() {
            const isChecked = jQuery(this).is(":checked"); //bool
            const akunId = jQuery(this).val();
            jQuery(`.subs-row[data-parent-id="${akunId}"] .subs-checkbox`).prop("checked", isChecked);
            jQuery(`.ket-row[data-grandparent-id="${akunId}"] .ket-checkbox`).prop("checked", isChecked);
            jQuery(`.rinci-row[data-greatgrandparent-id="${akunId}"] .rinci-checkbox`).prop("checked", isChecked);

            updateSelectAllState();
        });

        // Subs_bl_teks ke ket_bl_teks
        jQuery(".subs-checkbox").on("change", function() {
            const isChecked = jQuery(this).is(":checked");
            const subsId = jQuery(this).val();
            const parentId = jQuery(this).closest(".subs-row").data("parent-id");

            jQuery(`.ket-row[data-parent-id="${subsId}"] .ket-checkbox`).prop("checked", isChecked);
            jQuery(`.rinci-row[data-grandparent-id="${subsId}"] .rinci-checkbox`).prop("checked", isChecked);

            updateSelectAllStateAkun(parentId) //akun

            updateParentCheckbox(parentId, ".akun-checkbox", ".subs-checkbox");
            updateSelectAllState();
        });

        // Ket_bl_teks ke id_rinci_sub_bl
        jQuery(".ket-checkbox").on("change", function() {
            const isChecked = jQuery(this).is(":checked");
            const ketId = jQuery(this).val();
            const parentId = jQuery(this).closest(".ket-row").data("parent-id");
            const grandParentId = jQuery(`.subs-row[data-id="${parentId}"]`).data("parent-id");

            jQuery(`.rinci-row[data-parent-id="${ketId}"] .rinci-checkbox`).prop("checked", isChecked);

            updateSelectAllStateKelompok(parentId) //kelompok
            updateSelectAllStateAkun(grandParentId) //akun

            updateParentCheckbox(parentId, ".subs-checkbox", ".ket-checkbox");
            updateSelectAllState();
        });

        // Id_rinci_sub_bl ke ket_bl_teks
        jQuery(".rinci-checkbox").on("change", function() {
            const parentId = jQuery(this).closest(".rinci-row").data("parent-id"); //keterangan
            const grandParentId = jQuery(`.ket-row[data-id="${parentId}"]`).data("parent-id"); //kelompok
            const greatGrandParentId = jQuery(`.subs-row[data-id="${grandParentId}"]`).data("parent-id"); //akun

            updateSelectAllStateKeterangan(parentId)
            updateSelectAllStateKelompok(grandParentId)
            updateSelectAllStateAkun(greatGrandParentId) //akun

            updateParentCheckbox(parentId, ".ket-checkbox", ".rinci-checkbox");
            updateParentCheckbox(grandParentId, ".subs-checkbox", ".ket-checkbox");
            updateParentCheckbox(greatGrandParentId, ".akun-checkbox", ".subs-checkbox");

            updateSelectAllState();
        });

        // Update state of "select all" checkbox
        function updateSelectAllState() {
            const allChecked = jQuery(".rinci-checkbox").length === jQuery(".rinci-checkbox:checked").length;
            jQuery("#flexCheckDefault").prop("checked", allChecked);
        }

        function updateSelectAllStateAkun(akunId) {
            const allChildren = jQuery(`.subs-row[data-parent-id="${akunId}"] .subs-checkbox`);
            const allChecked = allChildren.length === allChildren.filter(":checked").length;

            jQuery(`.akun-checkbox[value="${akunId}"]`).prop("checked", allChecked);
        }

        function updateSelectAllStateKelompok(kelompokId) {
            const allChildren = jQuery(`.ket-row[data-parent-id="${kelompokId}"] .ket-checkbox`);
            const allChecked = allChildren.length === allChildren.filter(":checked").length;

            jQuery(`.subs-checkbox[value="${kelompokId}"]`).prop("checked", allChecked);
        }

        function updateSelectAllStateKeterangan(keteranganId) {
            const allChildren = jQuery(`.rinci-row[data-parent-id="${keteranganId}"] .rinci-checkbox`);
            const allChecked = allChildren.length === allChildren.filter(":checked").length;

            jQuery(`.ket-checkbox[value="${keteranganId}"]`).prop("checked", allChecked);
        }


        // Update parent checkbox
        function updateParentCheckbox(parentId, parentSelector, childSelector) {
            const allChildren = jQuery(`${childSelector}[data-parent-id="${parentId}"]`);
            const parentCheckbox = jQuery(`${parentSelector}[data-id="${parentId}"]`);
            parentCheckbox.prop("checked", allChildren.length === allChildren.filter(":checked").length);
        }
    }


    function hapus_data_rincian(idSubBl) {
        let confirmDelete = confirm("Apakah anda yakin akan menghapus rincian ini?");
        if (confirmDelete) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: ajax.url,
                type: 'post',
                data: {
                    'action': 'hapus_rincian_from_label_by_id',
                    'api_key': ajax.api_key,
                    'id_rincian': idSubBl,
                    'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                    'id_label': <?php echo $input['id_label']; ?>,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    jQuery("#wrap-loading").hide();
                    alert("Terjadi kesalahan saat mengirim data!");
                }
            });
        }
    }

    async function edit_data_subkeg(kodeSbl, idSkpd) {
        // Set nilai idSkpd tanpa memicu onchange
        jQuery("#idSkpd").off("change").val(idSkpd); // Hilangkan sementara event onchange
        await handleSkpdChange(idSkpd, true); // Manual trigger untuk load subkegiatan

        // Setelah selesai, set value subKegiatan
        jQuery("#subKegiatan").val(kodeSbl).trigger('change');
        jQuery("#subKegiatan").prop('disabled', true)

        // Kembalikan event onchange setelah manual trigger selesai
        jQuery("#idSkpd").on("change", function() {
            handleSkpdChange();
        });

        // Tampilkan modal
        await handleViewRinciBtn();
        jQuery("#title-label").text('Edit Tagging / Label Rincian Belanja')
        jQuery('#modalTambahData').modal('show');
    }

    function handleSkpdChange(idSkpd = null, isManual = false) {
        return new Promise((resolve, reject) => {
            // Gunakan parameter idSkpd jika manual, atau ambil dari select jika onchange
            let id_skpd = isManual ? idSkpd : jQuery("#idSkpd").val();

            if (isManual) {
                let selectedText = jQuery("#idSkpd option:selected").text();
                jQuery("#idSkpdSelect").hide();
                jQuery("#idSkpdText").val(selectedText);
                jQuery("#idSkpdTeks").show();
            }

            jQuery("#subKegiatan").empty().append('<option value="">Pilih Sub Kegiatan</option>');
            jQuery("#program").val('');
            jQuery("#kegiatan").val('');
            jQuery("#tableRincian").hide();

            if (id_skpd) {
                jQuery("#wrap-loading").show();

                jQuery.ajax({
                    url: ajax.url,
                    type: "POST",
                    data: {
                        action: "get_sub_keg_sipd",
                        api_key: ajax.api_key,
                        tahun_anggaran: '<?php echo $input["tahun_anggaran"]; ?>',
                        tipe: 'simple',
                        id_skpd: id_skpd,
                    },
                    dataType: "json",
                    success: function(response) {
                        jQuery("#wrap-loading").hide();

                        if (response.status === "success") {
                            const data = response.data;
                            jQuery('#subKegiatan').select2({
                                width: '100%',
                                dropdownParent: jQuery('#modalTambahData .modal-content')
                            });

                            data.forEach(function(item) {
                                const namaSubGiat = item.nama_sub_kegiatan.replace(/^\S+(\.\S+)*\s/, "");

                                const paguFormatted = new Intl.NumberFormat('id-ID', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }).format(item.pagu_anggaran);

                                jQuery("#subKegiatan").append(
                                    `<option value="${item.kode_sbl}" data-program="${item.kode_program} ${item.nama_program}" data-kegiatan="${item.kode_kegiatan} ${item.nama_kegiatan}">
                                    ${item.kode_sub_kegiatan} ${namaSubGiat} (Pagu: ${paguFormatted})
                                    </option>`
                                );
                            });

                            jQuery("#subKegiatan").prop("disabled", false);
                            resolve();
                        } else {
                            alert(response.message);
                            reject(new Error(response.message));
                        }
                    },
                    error: function(xhr, status, error) {
                        jQuery("#wrap-loading").hide();
                        alert("Terjadi kesalahan saat mengirim data!");
                        reject(new Error("Terjadi kesalahan saat mengirim data!"));
                    },
                });
            } else {
                jQuery("#subKegiatan").empty().append('<option value="">Pilih Sub Kegiatan</option>').prop("disabled", true);
                jQuery("#program, #kegiatan").val("");
                resolve();
            }
        });
    }

    function handleSubkegChange(that) {
        return new Promise((resolve, reject) => {
            let selectedOption = jQuery(that).find(":selected");
            const program = selectedOption.data("program");
            const kegiatan = selectedOption.data("kegiatan");

            jQuery("#tableRincian").hide();
            jQuery("#program").val(program || "");
            jQuery("#kegiatan").val(kegiatan || "");


            jQuery("#btnPreviewData").prop("disabled", !selectedOption.val());
        });
    }

    async function simpanRealisasi(idRinciSubBl) {
        let realisasiRincian = jQuery(`#realisasiRincian${idRinciSubBl}`).val();
        let formatedRincian = realisasiRincian.replace(/\./g, "");

        const tempData = new FormData();
        tempData.append("action", "simpan_realisasi_rinci_bl");
        tempData.append("api_key", ajax.api_key);
        tempData.append("id_rinci_sub_bl", idRinciSubBl);
        tempData.append("realisasi", formatedRincian);
        tempData.append("tahun_anggaran", <?php echo $input['tahun_anggaran']; ?>);

        jQuery("#wrap-loading").show();

        try {
            const res = await jQuery.ajax({
                method: "post",
                url: ajax.url,
                dataType: "json",
                data: tempData,
                processData: false,
                contentType: false,
                cache: false,
            });

            alert(res.message);

            if (res.status === "success") {
                await handleViewRinciBtn(); // Refresh tabel
                window.data_changed = true;
            }
        } catch (error) {
            console.error(error.responseText || error);
            alert("Terjadi kesalahan saat mengirim data!");
        } finally {
            jQuery("#wrap-loading").hide();
        }
    }
</script>