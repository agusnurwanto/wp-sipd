<?php
// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}
global $wpdb;
$type = 'murni';
if (!empty($_GET) && !empty($_GET['type'])) {
    $type = $_GET['type'];
}

$format_rkpd = '';
if (!empty($_GET) && !empty($_GET['rkpd'])) {
    $format_rkpd = $_GET['rkpd'];
}

$format_excel = '';
if (!empty($_GET) && !empty($_GET['excel'])) {
    $format_excel = $_GET['excel'];
}

$pengaturan = $wpdb->get_results($wpdb->prepare("
    select 
        * 
    from data_pengaturan_sipd 
    where tahun_anggaran=%d
", $input['tahun_anggaran']), ARRAY_A);

$start_rpjmd = 2018;
if (!empty($pengaturan)) {
    $start_rpjmd = $pengaturan[0]['awal_rpjmd'];
}
$urut = $input['tahun_anggaran'] - $start_rpjmd;

//tematik
if (!empty($input['idlabelgiat']) && empty($_GET['tipe'])) {
    $sql = "
        SELECT 
            s.*,
            t.*,
            l.nama_label
        FROM `data_tag_sub_keg` t
        LEFT JOIN data_sub_keg_bl s 
               ON s.kode_sbl = t.kode_sbl 
              AND s.tahun_anggaran = t.tahun_anggaran
        LEFT JOIN data_label_giat l
               ON t.idlabelgiat = l.id_label_giat
              AND t.tahun_anggaran = l.tahun_anggaran
        WHERE t.idlabelgiat = %d 
          AND t.active = 1
          AND t.tahun_anggaran = %d";
    $subkeg = $wpdb->get_results($wpdb->prepare($sql, $input['idlabelgiat'], $input['tahun_anggaran']), ARRAY_A);
} else if (!empty($input['idlabelgiat']) && !empty($_GET['tipe'])) {
    //prioritas
    if ($_GET['tipe'] == 'data_prioritas_pusat') {
        $table = 'data_prioritas_pusat';
        $column = 'id_label_pusat';
    } else if ($_GET['tipe'] == 'data_prioritas_prov') {
        $table = 'data_prioritas_prov';
        $column = 'id_label_prov';
    } else if ($_GET['tipe'] == 'data_prioritas_kokab') {
        $table = 'data_prioritas_kokab';
        $column = 'id_label_kokab';
    } else {
        die('Parameter tidak valid.');
    }

    $sql = "
        SELECT
            p.*,
            s.*
        FROM $table AS p
        LEFT JOIN data_sub_keg_bl AS s 
               ON p.id_prioritas = s.$column
              AND p.tahun_anggaran = s.tahun_anggaran 
              AND p.active = s.active 
        WHERE p.tahun_anggaran = %d
          AND p.id_prioritas = %d 
          AND p.active = 1";
    $subkeg = $wpdb->get_results($wpdb->prepare($sql, $input['tahun_anggaran'], $_GET['id_prioritas']), ARRAY_A);
} else {
    //all label giat
    $sql = "
        SELECT 
            s.*,
            t.* 
        FROM `data_tag_sub_keg` t
        LEFT JOIN data_sub_keg_bl s 
               ON s.kode_sbl=t.kode_sbl 
              AND s.tahun_anggaran=t.tahun_anggaran
        WHERE t.idlabelgiat > 0 
            AND t.active=1
            AND t.tahun_anggaran=%d";
    $subkeg = $wpdb->get_results($wpdb->prepare($sql, $input['tahun_anggaran']), ARRAY_A);
}

$nama_label = array();
$data_all = array(
    'total'         => 0,
    'realisasi'     => 0,
    'total_n_plus'  => 0,
    'data'          => array()
);
foreach ($subkeg as $kk => $sub) {
    $label = 'nama_label';
    $nama_label[$sub[$label]] = $sub[$label];
    $kode = explode('.', $sub['kode_sbl']);
    $capaian_prog = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_capaian_prog_sub_keg 
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
            and capaianteks != ''
        order by id ASC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);
    if ($type == 'detail' && empty($capaian_prog)) {
        $capaian_prog = $wpdb->get_results($wpdb->prepare("
            select 
                j.indikator as capaianteks,
                j.target_" . $urut . " as targetcapaianteks 
            from data_renstra as r
                join data_rpjmd as j on r.id_rpjmd=j.id_rpjmd and r.tahun_anggaran=j.tahun_anggaran
            where r.tahun_anggaran=%d
                and r.id_program=%d
                and r.id_giat=%d
                and r.id_sub_giat=%d
                and r.kode_skpd=%s
            order by r.id ASC
        ", $input['tahun_anggaran'], $kode[2], $kode[3], $kode[4], $unit['kode_skpd']), ARRAY_A);
    }

    $output_giat = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_output_giat_sub_keg 
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
        order by id ASC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

    $output_sub_giat = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_sub_keg_indikator
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
        order by id DESC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

    $lokasi_sub_giat = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_lokasi_sub_keg
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
        order by id ASC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

    
    $kode_sbl_kas = explode('.', $sub['kode_sbl']);
    $kode_sbl_kas = $kode_sbl_kas[0].'.'.$kode_sbl_kas[0].'.'.$kode_sbl_kas[1].'.'.$sub['id_bidang_urusan'].'.'.$kode_sbl_kas[2].'.'.$kode_sbl_kas[3].'.'.$kode_sbl_kas[4];
    $realisasi = $wpdb->get_var(
        $wpdb->prepare("
            SELECT
                realisasi
            FROM data_realisasi_akun_sipd
            WHERE active = 1
              AND tahun_anggaran=%d
              AND kode_sbl = %s
        ", $input["tahun_anggaran"], $kode_sbl_kas)
    );

    if (!empty($sub['nama_sub_giat'])) {
        $nama = explode(' ', $sub['nama_sub_giat']);
        $kode_sub_giat = $nama[0];
        $data_renstra = $wpdb->get_results(
            $wpdb->prepare("
                SELECT 
                    * 
                FROM data_renstra
                WHERE tahun_anggaran=%d
                  AND active=1
                  AND kode_sub_giat=%s
                  AND id_unit=%s
                ORDER BY id ASC
            ", $input['tahun_anggaran'], $kode_sub_giat, $sub['id_skpd']),
            ARRAY_A
        );
    }

    $data_rpjmd = array();
    if (!empty($data_renstra)) {
        $data_rpjmd = $wpdb->get_results($wpdb->prepare("
            select 
                * 
            from data_rpjmd
            where 
                id_rpjmd=%d 
                and tahun_anggaran=%d
            order by id ASC
        ", $data_renstra[0]['id_rpjmd'], $input['tahun_anggaran']), ARRAY_A);
        $nama_skpd = $data_renstra[0]['nama_skpd'];
    } else {
        $_nama_skpd = $wpdb->get_row($wpdb->prepare("
            SELECT 
                nama_skpd,
                kode_skpd
            FROM data_unit
            WHERE id_skpd=%d 
              AND tahun_anggaran=%d
              AND active=1
            ORDER BY id ASC
        ", $sub['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
        if (!empty($_nama_skpd)) {
            $nama_skpd = $_nama_skpd['nama_skpd'];
        }
    }

    if (empty($data_all['data'][$sub[$label]])) {
        $data_all['data'][$sub[$label]] = array(
            'nama'          => $sub[$label],
            'realisasi'     => 0,
            'total'         => 0,
            'total_n_plus'  => 0,
            'data'          => array()
        );
    }

    if (empty($data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']])) {
        $sub_skpd = $wpdb->get_row($wpdb->prepare("
            SELECT 
                nama_skpd,
                kode_skpd
            FROM data_unit
            WHERE id_skpd=%d 
              AND tahun_anggaran=%d
              AND active=1
            ORDER BY id ASC
        ", $sub['id_sub_skpd'], $input['tahun_anggaran']), ARRAY_A);

        $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']] = array(
            'nama'           => $sub_skpd['nama_skpd'],
            'kode_sub_skpd'  => $sub_skpd['kode_skpd'],
            'nama_skpd'      => $nama_skpd ?? '',
            'kode_skpd'      => $sub['kode_skpd'],
            'realisasi'      => 0,
            'total'          => 0,
            'total_n_plus'   => 0,
            'data'           => array()
        );
    }

    if (empty($data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']])) {
        $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']] = array(
            'nama'          => $sub['nama_urusan'],
            'realisasi'     => 0,
            'total'         => 0,
            'total_n_plus'  => 0,
            'data'          => array()
        );
    }
    if (empty($data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])) {
        $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
            'nama'          => $sub['nama_bidang_urusan'],
            'realisasi'     => 0,
            'total'         => 0,
            'total_n_plus'  => 0,
            'data'          => array()
        );
    }
    if (empty($data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']])) {
        $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']] = array(
            'nama'          => $sub['nama_program'],
            'realisasi'     => 0,
            'total'         => 0,
            'total_n_plus'  => 0,
            'data'          => array()
        );
    }
    if (empty($data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']])) {
        $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']] = array(
            'nama'          => $sub['nama_giat'],
            'realisasi'     => 0,
            'total'         => 0,
            'total_n_plus'  => 0,
            'data'          => array()
        );
    }
    if (empty($data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']])) {
        if (!empty($sub['nama_sub_giat'])) {
            $nama = explode(' ', $sub['nama_sub_giat']);
            unset($nama[0]);
        } else {
            $nama = array();
        }
        $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']] = array(
            'nama'            => implode(' ', $nama),
            'realisasi'       => 0,
            'total'           => 0,
            'total_n_plus'    => 0,
            'capaian_prog'    => $capaian_prog,
            'output_giat'     => $output_giat,
            'output_sub_giat' => $output_sub_giat,
            'lokasi_sub_giat' => $lokasi_sub_giat,
            'data_renstra'    => $data_renstra ?? '',
            'data_rpjmd'      => $data_rpjmd,
            'data'            => $sub
        );
    }
    $data_all['total'] += $sub['pagu'];
    $data_all['realisasi'] += $realisasi;
    $data_all['data'][$sub[$label]]['total'] += $sub['pagu'];
    $data_all['data'][$sub[$label]]['realisasi'] += $realisasi;
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['total'] += $sub['pagu'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['realisasi'] += $realisasi;
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total'] += $sub['pagu'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['realisasi'] += $realisasi;
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total'] += $sub['pagu'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['realisasi'] += $realisasi;
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total'] += $sub['pagu'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['realisasi'] += $realisasi;
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total'] += $sub['pagu'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['realisasi'] += $realisasi;
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total'] += $sub['pagu'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['realisasi'] += $realisasi;

    $data_all['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub[$label]]['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub[$label]]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_n_plus'] += $sub['pagu_n_depan'];
}

$body = '';
$body_rkpd = '';
$body_excel = '';
$no = 1;
foreach ($data_all['data'] as $label_tag) {
    $body .= '
        <tr style="background: #f7eb64;">
            <td class="kiri kanan bawah text_blok" colspan="13">' . $label_tag['nama'] . '</td>
            <td class="kanan bawah text_kanan text_blok">' . number_format($label_tag['total'], 2, ",", ".") . '</td>
            <td class="kanan bawah" colspan="4">&nbsp;</td>
            <td class="kanan bawah text_kanan text_blok">' . number_format($label_tag['total_n_plus'], 2, ",", ".") . '</td>
        </tr>
    ';
    $body_rkpd .= '
        <tr style="background: #f7eb64;">
            <td class="kiri kanan bawah text_blok" colspan="15">' . $label_tag['nama'] . '</td>
            <td class="kanan bawah text_kanan text_blok">' . number_format($label_tag['total'], 2, ",", ".") . '</td>
            <td class="kanan bawah text_kanan text_blok">' . number_format($label_tag['total_n_plus'], 2, ",", ".") . '</td>
            <td class="kanan bawah"></td>
        </tr>
    ';
    foreach ($label_tag['data'] as $sub_skpd) {
        $body .= '
            <tr>
                <td class="kiri kanan bawah text_blok"></td>
                <td class="kanan bawah text_blok" colspan="18">Unit Organisasi : ' . $sub_skpd['nama_skpd'] . '</td>
            </tr>
            <tr style="background: #ffe2e2;">
                <td class="kiri kanan bawah text_blok"></td>
                <td class="kanan bawah">&nbsp;</td>
                <td class="kanan bawah text_blok" colspan="11">Sub Unit Organisasi : ' . $sub_skpd['nama'] . '</td>
                <td class="kanan bawah text_kanan text_blok">' . number_format($sub_skpd['total'], 2, ",", ".") . '</td>
                <td class="kanan bawah" colspan="4">&nbsp;</td>
                <td class="kanan bawah text_kanan text_blok">' . number_format($sub_skpd['total_n_plus'], 2, ",", ".") . '</td>
            </tr>
        ';
        $body_rkpd .= '
            <tr>
                <td class="kiri kanan bawah text_blok"></td>
                <td class="kanan bawah text_blok" colspan="17">Unit Organisasi : ' . $sub_skpd['nama_skpd'] . '</td>
            </tr>
            <tr style="background: #ffe2e2;">
                <td class="kiri kanan bawah text_blok"></td>
                <td class="kanan bawah">&nbsp;</td>
                <td class="kanan bawah text_blok" colspan="13">Sub Unit Organisasi : ' . $sub_skpd['nama'] . '</td>
                <td class="kanan bawah text_kanan text_blok">' . number_format($sub_skpd['total'], 2, ",", ".") . '</td>
                <td class="kanan bawah text_kanan text_blok">' . number_format($sub_skpd['total_n_plus'], 2, ",", ".") . '</td>
                <td class="kanan bawah"></td>
            </tr>
        ';
        foreach ($sub_skpd['data'] as $kd_urusan => $urusan) {
            $body .= '
                <tr>
                    <td class="kiri kanan bawah text_blok">' . $kd_urusan . '</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah text_blok" colspan="14">' . $urusan['nama'] . '</td>
                </tr>
            ';
            $body_rkpd .= '
                <tr>
                    <td class="kiri kanan bawah text_blok">' . $kd_urusan . '</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah text_blok" colspan="13">' . $urusan['nama'] . '</td>
                </tr>
            ';
            foreach ($urusan['data'] as $kd_bidang => $bidang) {
                $kd_bidang = explode('.', $kd_bidang);
                $kd_bidang = $kd_bidang[count($kd_bidang) - 1];
                $body .= '
                    <tr>
                        <td class="kiri kanan bawah text_blok">' . $kd_urusan . '</td>
                        <td class="kanan bawah text_blok">' . $kd_bidang . '</td>
                        <td class="kanan bawah">&nbsp;</td>
                        <td class="kanan bawah">&nbsp;</td>
                        <td class="kanan bawah">&nbsp;</td>
                        <td class="kanan bawah text_blok" colspan="8">' . $bidang['nama'] . '</td>
                        <td class="kanan bawah text_kanan text_blok">' . number_format($bidang['total'], 2, ",", ".") . '</td>
                        <td class="kanan bawah" colspan="4">&nbsp;</td>
                        <td class="kanan bawah text_kanan text_blok">' . number_format($bidang['total_n_plus'], 2, ",", ".") . '</td>
                    </tr>
                ';
                $body_rkpd .= '
                    <tr>
                        <td class="kiri kanan bawah text_blok">' . $kd_urusan . '</td>
                        <td class="kanan bawah text_blok">' . $kd_bidang . '</td>
                        <td class="kanan bawah">&nbsp;</td>
                        <td class="kanan bawah">&nbsp;</td>
                        <td class="kanan bawah">&nbsp;</td>
                        <td class="kanan bawah text_blok" colspan="10">' . $bidang['nama'] . '</td>
                        <td class="kanan bawah text_kanan text_blok">' . number_format($bidang['total'], 2, ",", ".") . '</td>
                        <td class="kanan bawah text_kanan text_blok">' . number_format($bidang['total_n_plus'], 2, ",", ".") . '</td>
                        <td class="kanan bawah"></td>
                    </tr>
                ';
                foreach ($bidang['data'] as $kd_program => $program) {
                    $kd_program = explode('.', $kd_program);
                    $kd_program = $kd_program[count($kd_program) - 1];
                    $body .= '
                        <tr>
                            <td class="kiri kanan bawah text_blok">' . $kd_urusan . '</td>
                            <td class="kanan bawah text_blok">' . $kd_bidang . '</td>
                            <td class="kanan bawah text_blok">' . $kd_program . '</td>
                            <td class="kanan bawah">&nbsp;</td>
                            <td class="kanan bawah">&nbsp;</td>
                            <td class="kanan bawah text_blok" colspan="8">' . $program['nama'] . '</td>
                            <td class="kanan bawah text_kanan text_blok">' . number_format($program['total'], 2, ",", ".") . '</td>
                            <td class="kanan bawah" colspan="4">&nbsp;</td>
                            <td class="kanan bawah text_kanan text_blok">' . number_format($program['total_n_plus'], 2, ",", ".") . '</td>
                        </tr>
                    ';
                    $body_rkpd .= '
                        <tr>
                            <td class="kiri kanan bawah text_blok">' . $kd_urusan . '</td>
                            <td class="kanan bawah text_blok">' . $kd_bidang . '</td>
                            <td class="kanan bawah text_blok">' . $kd_program . '</td>
                            <td class="kanan bawah">&nbsp;</td>
                            <td class="kanan bawah">&nbsp;</td>
                            <td class="kanan bawah text_blok" colspan="10">' . $program['nama'] . '</td>
                            <td class="kanan bawah text_kanan text_blok">' . number_format($program['total'], 2, ",", ".") . '</td>
                            <td class="kanan bawah text_kanan text_blok">' . number_format($program['total_n_plus'], 2, ",", ".") . '</td>
                            <td class="kanan bawah"></td>
                        </tr>
                    ';
                    foreach ($program['data'] as $kd_giat => $giat) {
                        if (!empty($kd_giat)) {
                            $kd_giat = explode('.', $kd_giat);
                            $kd_giat = $kd_giat[count($kd_giat) - 2] . '.' . $kd_giat[count($kd_giat) - 1];
                        }
                        $body .= '
                            <tr>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">' . $kd_urusan . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:00;" width="5">' . $kd_bidang . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:000;" width="5">' . $kd_program . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">' . $kd_giat . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle;" width="5">&nbsp;</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" colspan="8">' . $giat['nama'] . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">' . number_format($giat['total'], 2, ",", ".") . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" colspan="4"></td>
                                <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">' . number_format($giat['total_n_plus'], 2, ",", ".") . '</td>
                            </tr>
                        ';
                        $body_rkpd .= '
                            <tr>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">' . $kd_urusan . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:00;" width="5">' . $kd_bidang . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:000;" width="5">' . $kd_program . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">' . $kd_giat . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle;" width="5">&nbsp;</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" colspan="10">' . $giat['nama'] . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">' . number_format($giat['total'], 2, ",", ".") . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">' . number_format($giat['total_n_plus'], 2, ",", ".") . '</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;"></td>
                            </tr>
                        ';
                        foreach ($giat['data'] as $kd_sub_giat => $sub_giat) {
                            $kd_sub_giat = explode('.', $kd_sub_giat);
                            $kd_sub_giat = $kd_sub_giat[count($kd_sub_giat) - 1];
                            $capaian_prog = '';
                            if (!empty($sub_giat['capaian_prog'])) {
                                $capaian_prog = $sub_giat['capaian_prog'][0]['capaianteks'];
                            }
                            $target_capaian_prog = '';
                            if (!empty($sub_giat['capaian_prog'])) {
                                $target_capaian_prog = $sub_giat['capaian_prog'][0]['targetcapaianteks'];
                            }
                            $output_giat = '';
                            if (!empty($sub_giat['output_giat'])) {
                                $output_giat = $sub_giat['output_giat'][0]['outputteks'];
                            }
                            $target_output_giat = '';
                            if (!empty($sub_giat['output_giat'])) {
                                $target_output_giat = $sub_giat['output_giat'][0]['targetoutputteks'];
                            }
                            $output_sub_giat = '';
                            $target_output_sub_giat = '';
                            if (!empty($sub_giat['output_sub_giat'])) {
                                $output_sub_giat = array();
                                $target_output_sub_giat = array();
                                foreach ($sub_giat['output_sub_giat'] as $k_sub => $v_sub) {
                                    $output_sub_giat[] = $v_sub['outputteks'];
                                    $target_output_sub_giat[] = $v_sub['targetoutputteks'];
                                }
                                $output_sub_giat = implode('<br>', $output_sub_giat);
                                $target_output_sub_giat = implode('<br>', $target_output_sub_giat);
                            }
                            $lokasi_sub_giat = '';
                            if (!empty($sub_giat['lokasi_sub_giat'])) {
                                $lokasi_sub_giat = $sub_giat['lokasi_sub_giat'][0]['daerahteks'] . ', ' . $sub_giat['lokasi_sub_giat'][0]['camatteks'] . ', ' . $sub_giat['lokasi_sub_giat'][0]['lurahteks'];
                            }
                            $ind_n_plus = '';
                            $target_ind_n_plus = '';
                            if (!empty($sub_giat['data_rpjmd'])) {
                                $ind_n_plus = $sub_giat['data_rpjmd'][0]['indikator'];
                                if ($urut <= 5) {
                                    $target_ind_n_plus = $sub_giat['data_rpjmd'][0]['target_' . ($urut + 1)];
                                }
                            }
                            $body .= '
                                <tr>
                                    <td class="kiri kanan bawah">' . $kd_urusan . '</td>
                                    <td class="kanan bawah">' . $kd_bidang . '</td>
                                    <td class="kanan bawah">' . $kd_program . '</td>
                                    <td class="kanan bawah">' . $kd_giat . '</td>
                                    <td class="kanan bawah">' . $kd_sub_giat . '</td>
                                    <td class="kanan bawah">' . $sub_giat['nama'] . '</td>
                                    <td class="kanan bawah">' . $capaian_prog . '</td>
                                    <td class="kanan bawah">' . $output_sub_giat . '</td>
                                    <td class="kanan bawah">' . $output_giat . '</td>
                                    <td class="kanan bawah">' . $lokasi_sub_giat . '</td>
                                    <td class="kanan bawah">' . $target_capaian_prog . '</td>
                                    <td class="kanan bawah">' . $target_output_sub_giat . '</td>
                                    <td class="kanan bawah">' . $target_output_giat . '</td>
                                    <td class="kanan bawah text_kanan">' . number_format($sub_giat['total'], 2, ",", ".") . '</td>
                                    <td class="kanan bawah"><br/></td>
                                    <td class="kanan bawah">&nbsp;</td>
                                    <td class="kanan bawah">' . $ind_n_plus . '</td>
                                    <td class="kanan bawah">' . $target_ind_n_plus . '</td>
                                    <td class="kanan bawah text_kanan">' . number_format($sub_giat['total_n_plus'], 2, ",", ".") . '</td>
                                </tr>
                            ';
                            $sasaran_text = '';
                            if (!empty($sub_giat['data_renstra'])) {
                                $sasaran_text = $sub_giat['data_renstra'][0]['sasaran_teks'];
                            }
                            $body_rkpd .= '
                                <tr>
                                    <td class="kiri kanan bawah">' . $kd_urusan . '</td>
                                    <td class="kanan bawah">' . $kd_bidang . '</td>
                                    <td class="kanan bawah">' . $kd_program . '</td>
                                    <td class="kanan bawah">' . $kd_giat . '</td>
                                    <td class="kanan bawah">' . $kd_sub_giat . '</td>
                                    <td class="kanan bawah">' . $sub_giat['nama'] . '</td>
                                    <td class="kanan bawah">' . $sub_giat['data']['label_kokab'] . '</td>
                                    <td class="kanan bawah">' . $sasaran_text . '</td>
                                    <td class="kanan bawah">' . $lokasi_sub_giat . '</td>
                                    <td class="kanan bawah">' . $capaian_prog . '</td>
                                    <td class="kanan bawah">' . $target_capaian_prog . '</td>
                                    <td class="kanan bawah">' . $output_sub_giat . '</td>
                                    <td class="kanan bawah">' . $target_output_sub_giat . '</td>
                                    <td class="kanan bawah">' . $output_giat . '</td>
                                    <td class="kanan bawah">' . $target_output_giat . '</td>
                                    <td class="kanan bawah text_kanan">' . number_format($sub_giat['total'], 2, ",", ".") . '</td>
                                    <td class="kanan bawah text_kanan">' . number_format($sub_giat['total_n_plus'], 2, ",", ".") . '</td>
                                    <td class="kanan bawah"></td>
                                </tr>
                            ';
                            $kode_bidang_urusan = $kd_urusan . '.' . $kd_bidang;
                            $kode_program = $kd_urusan . '.' . $kd_bidang . '.' . $kd_program;
                            $kode_kegiatan = $kd_urusan . '.' . $kd_bidang . '.' . $kd_program . '.' . $kd_giat;
                            $kode_sub_kegiatan = $kd_urusan . '.' . $kd_bidang . '.' . $kd_program . '.' . $kd_giat . '.' . $kd_sub_giat;
                            $body_excel .= '
                                <tr>
                                    <td class="kiri kanan bawah text_tengah">' . $no++ . '</td>
                                    <td class="kanan bawah text_tengah">' . $kd_urusan . '</td>
                                    <td class="kanan bawah">' . $urusan['nama'] . '</td>
                                    <td class="kanan bawah text_tengah">' . $kode_bidang_urusan . '</td>
                                    <td class="kanan bawah">' . $bidang['nama'] . '</td>
                                    <td class="kanan bawah">' . $sub_skpd['kode_skpd'] . '</td>
                                    <td class="kanan bawah">' . $sub_skpd['nama_skpd'] . '</td>
                                    <td class="kanan bawah">' . $sub_skpd['kode_sub_skpd'] . '</td>
                                    <td class="kanan bawah">' . $sub_skpd['nama'] . '</td>
                                    <td class="kanan bawah text_tengah">' . $kode_program . '</td>
                                    <td class="kanan bawah">' . $program['nama'] . '</td>
                                    <td class="kanan bawah text_tengah">' . $kode_kegiatan . '</td>
                                    <td class="kanan bawah">' . $giat['nama'] . '</td>
                                    <td class="kanan bawah text_tengah">' . $kode_sub_kegiatan . '</td>
                                    <td class="kanan bawah">' . $sub_giat['nama'] . '</td>
                                    <td class="kanan bawah text_kanan">' . number_format($sub_giat['total'], 2, ",", ".") . '</td>
                                    <td class="kanan bawah text_kanan">' . number_format($sub_giat['realisasi'], 2, ",", ".") . '</td>
                                </tr>
                            ';
                        }
                    }
                }
            }
        }
    }
}

$nama_excel = 'LAPORAN APBD SESUAI TAG/LABEL SUB KEGIATAN ' . strtoupper(implode(', ', $nama_label)) . ' TAHUN ANGGARAN ' . $input['tahun_anggaran'];
$nama_laporan = 'LAPORAN APBD SESUAI TAG/LABEL SUB KEGIATAN<br>' . strtoupper(implode(', ', $nama_label)) . ' TAHUN ANGGARAN ' . $input['tahun_anggaran'];

if (!empty($format_rkpd)) {
    echo '
        <div id="cetak" title="' . $nama_excel . '" style="padding: 5px;">
            <h4 style="text-align: center; font-size: 13px; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">' . $nama_laporan . '</h4>
            <table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; table-layout:fixed; overflow-wrap: break-word; font-size: 60%; border: 0;">
                <thead>
                    <tr>
                        <th style="padding: 0; border: 0; width:1%"></th>
                        <th style="padding: 0; border: 0; width:1.2%"></th>
                        <th style="padding: 0; border: 0; width:1.5%"></th>
                        <th style="padding: 0; border: 0; width:1.5%"></th>
                        <th style="padding: 0; border: 0; width:1.5%"></th>
                        <th style="padding: 0; border: 0; width:7%"></th>
                        <th style="padding: 0; border: 0; width:5%"></th>
                        <th style="padding: 0; border: 0; width:7.5%"></th>
                        <th style="padding: 0; border: 0; width:4%"></th>
                        <th style="padding: 0; border: 0; width:7.5%"></th>
                        <th style="padding: 0; border: 0; width:3.5%"></th>
                        <th style="padding: 0; border: 0; width:7.5%"></th>
                        <th style="padding: 0; border: 0; width:3.5%"></th>
                        <th style="padding: 0; border: 0; width:7.5%"></th>
                        <th style="padding: 0; border: 0; width:3.5%"></th>
                        <th style="padding: 0; border: 0; width:5.5%"></th>
                        <th style="padding: 0; border: 0; width:5.5%"></th>
                        <th style="padding: 0; border: 0; width:4%"></th>
                    </tr>
                    <tr>
                        <td style="border:.5pt solid #000; vertical-align:middle; text-align:center; font-weight:bold;" colspan="5" rowspan="3">Kode</td>
                        <td style="border:.5pt solid #000; vertical-align:middle; text-align:center; font-weight:bold;" rowspan="3">Urusan/ Bidang Urusan Pemerintahan Daerah Dan Program/ Kegiatan</td>
                        <td style="border:.5pt solid #000; vertical-align:middle; text-align:center; font-weight:bold;" rowspan="3">Prioritas Daerah</td>
                        <td style="border:.5pt solid #000; vertical-align:middle; text-align:center; font-weight:bold;" rowspan="3">Sasaran Daerah</td>
                        <td style="border:.5pt solid #000; vertical-align:middle; text-align:center; font-weight:bold;" rowspan="3">Lokasi</td>
                        <td style="border:.5pt solid #000; vertical-align:middle; text-align:center; font-weight:bold;" colspan="6">Indikator Kinerja</td>
                        <td style="border:.5pt solid #000; vertical-align:middle; text-align:center; font-weight:bold;" rowspan="3">Pagu Indikatif (Rp.)</td>
                        <td style="border:.5pt solid #000; vertical-align:middle; text-align:center; font-weight:bold;" rowspan="3">Prakiraan Maju (Rp.)</td>
                        <td style="border:.5pt solid #000; vertical-align:middle; text-align:center; font-weight:bold;" colspan="1">Keterangan</td>
                    </tr>
                    <tr>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:center; font-weight:bold;" colspan="2">Capaian Program</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:center; font-weight:bold;" colspan="2">Keluaran Sub Kegiatan</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:center; font-weight:bold;" colspan="2">Hasil Kegiatan</td>
                        <!-- <td style="border:.5pt solid #000; vertical-align:middle;  text-align:center; font-weight:bold;" rowspan="2">Program Juara</td> -->
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:center; font-weight:bold;" rowspan="2">Prioritas Pembangunan Nasional</td>
                    </tr>
                    <tr>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:center; font-weight:bold;">Tolok Ukur</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:center; font-weight:bold;">Target</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:center; font-weight:bold;">Tolok Ukur</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:center; font-weight:bold;">Target</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:center; font-weight:bold;">Tolok Ukur</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:center; font-weight:bold;">Target</td>
                    </tr>
                </thead>
                <tbody>
                    ' . $body_rkpd . '
                    <tr>
                        <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; text-align:right" colspan="15">TOTAL</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">' . number_format($data_all['total'], 2, ",", ".") . '</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">' . number_format($data_all['total_n_plus'], 2, ",", ".") . '</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    ';
} else if (!empty($format_excel)) {
    echo '
        <h4 style="text-align: center; font-size: 16px; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">' . $nama_laporan . '</h4>
        <div id="cetak" title="' . $nama_excel . '" style="padding: 5px; overflow: auto; max-height: 80vh;">
            <table cellpadding="2" cellspacing="0" id="table_data_rak" contenteditable="false">
                <thead>
                    <tr>
                        <th class="atas kiri kanan bawah text_tengah text_blok">No</th>
                        <th class="atas kanan bawah text_tengah text_blok">Kode Urusan</th>
                        <th class="atas kanan bawah text_tengah text_blok">Nama Urusan</th>
                        <th class="atas kanan bawah text_tengah text_blok">Kode Bidang Urusan</th>
                        <th class="atas kanan bawah text_tengah text_blok">Nama Bidang Urusan</th>
                        <th class="atas kanan bawah text_tengah text_blok">Kode SKPD</th>
                        <th class="atas kanan bawah text_tengah text_blok">Nama SKPD</th>
                        <th class="atas kanan bawah text_tengah text_blok">Kode Sub SKPD</th>
                        <th class="atas kanan bawah text_tengah text_blok">Nama Sub SKPD</th>
                        <th class="atas kanan bawah text_tengah text_blok">Kode Program</th>
                        <th class="atas kanan bawah text_tengah text_blok">Nama Program</th>
                        <th class="atas kanan bawah text_tengah text_blok">Kode Kegiatan</th>
                        <th class="atas kanan bawah text_tengah text_blok">Nama Kegiatan</th>
                        <th class="atas kanan bawah text_tengah text_blok">Kode Sub Kegiatan</th>
                        <th class="atas kanan bawah text_tengah text_blok">Nama Sub Kegiatan</th>
                        <th class="atas kanan bawah text_tengah text_blok">Nilai Pagu</th>
                        <th class="atas kanan bawah text_tengah text_blok">Realisasi</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $body_excel . '
                </tbody>
                <tfoot>
                    <tr>
                        <td class="kiri kanan bawah text_tengah text_blok" colspan="15">Total</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">' . number_format($data_all['total'], 2, ",", ".") . '</td>
                        <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">' . number_format($data_all['realisasi'], 2, ",", ".") . '</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    ';
} else {
    echo '
        <div id="cetak" title="' . $nama_excel . '" style="padding: 5px;">
            <h4 style="text-align: center; font-size: 13px; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">' . $nama_laporan . '</h4>
            <table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; table-layout:fixed; overflow-wrap: break-word; font-size: 60%; border: 0;">
                <thead>
                    <tr>
                        <th style="padding: 0; border: 0; width:1%"></th>
                        <th style="padding: 0; border: 0; width:1.2%"></th>
                        <th style="padding: 0; border: 0; width:1.5%"></th>
                        <th style="padding: 0; border: 0; width:1.5%"></th>
                        <th style="padding: 0; border: 0; width:1.5%"></th>
                        <th style="padding: 0; border: 0; width:7%"></th>
                        <th style="padding: 0; border: 0; width:7.5%"></th>
                        <th style="padding: 0; border: 0; width:7.5%"></th>
                        <th style="padding: 0; border: 0; width:7.5%"></th>
                        <th style="padding: 0; border: 0; width:4%"></th>
                        <th style="padding: 0; border: 0; width:3.5%"></th>
                        <th style="padding: 0; border: 0; width:3.5%"></th>
                        <th style="padding: 0; border: 0; width:3.5%"></th>
                        <th style="padding: 0; border: 0; width:5.5%"></th>
                        <th style="padding: 0; border: 0; width:3.5%"></th>
                        <th style="padding: 0; border: 0; width:4%"></th>
                        <th style="padding: 0; border: 0; width:7.5%"></th>
                        <th style="padding: 0; border: 0; width:3.5%"></th>
                        <th style="padding: 0; border: 0; width:5.5%"></th>
                    </tr>
                    <tr>
                        <td class="atas kanan bawah kiri text_tengah text_blok" colspan="5" rowspan="3">Kode</td>
                        <td class="atas kanan bawah text_tengah text_blok" rowspan="3">Urusan/ Bidang Urusan Pemerintahan Daerah Dan Program/ Kegiatan</td>
                        <td class="atas kanan bawah text_tengah text_blok" colspan="3">Indikator Kinerja</td>
                        <td class="atas kanan bawah text_tengah text_blok" colspan="6">Rencana Tahun ' . $input['tahun_anggaran'] . '</td>
                        <td class="atas kanan bawah text_tengah text_blok" rowspan="3">Catatan Penting</td>
                        <td class="atas kanan bawah text_tengah text_blok" colspan="3">Prakiraan Maju Rencana Tahun ' . ($input['tahun_anggaran'] + 1) . '</td>
                    </tr>
                    <tr>
                        <td class="kanan bawah text_tengah text_blok" rowspan="2">Capaian Program</td>
                        <td class="kanan bawah text_tengah text_blok" rowspan="2">Keluaran Sub Kegiatan</td>
                        <td class="kanan bawah text_tengah text_blok" rowspan="2">Hasil Kegiatan</td>
                        <td class="kanan bawah text_tengah text_blok" rowspan="2">Lokasi Output Kegiatan</td>
                        <td class="kanan bawah text_tengah text_blok" colspan="3">Target Capaian Kinerja</td>
                        <td class="kanan bawah text_tengah text_blok" rowspan="2">Pagu Indikatif (Rp.)</td>
                        <td class="kanan bawah text_tengah text_blok" rowspan="2">Sumber Dana</td>
                        <td class="kanan bawah text_tengah text_blok" colspan="2">Target Capaian Kinerja</td>
                        <td class="kanan bawah text_tengah text_blok" rowspan="2">Kebutuhan Dana/<br/>Pagu Indikatif (Rp.)</td>
                    </tr>
                    <tr>
                        <td class="kanan bawah text_tengah text_blok">Program</td>
                        <td class="kanan bawah text_tengah text_blok">Keluaran Sub Kegiatan</td>
                        <td class="kanan bawah text_tengah text_blok">Hasil Kegiatan</td>
                        <td class="kanan bawah text_tengah text_blok">Tolok Ukur</td>
                        <td class="kanan bawah text_tengah text_blok">Target</td>
                    </tr>
                </thead>
                <tbody>
                    ' . $body . '
                    <tr>
                        <td class="kiri kanan bawah text_blok text_kanan" colspan="13">TOTAL</td>
                        <td class="kanan bawah text_kanan text_blok">' . number_format($data_all['total'], 2, ",", ".") . '</td>
                        <td class="kanan bawah" colspan="4">&nbsp;</td>
                        <td class="kanan bawah text_kanan text_blok">' . number_format($data_all['total_n_plus'], 2, ",", ".") . '</td>
                    </tr>
                </tbody>
            </table>
        </div>
    ';
}
?>

<script type="text/javascript">
    run_download_excel();
    var _url = window.location.href;
    var url = new URL(_url);
    _url = url.origin + url.pathname + '?key=' + url.searchParams.get('key');

    var rkpd = url.searchParams.get("rkpd");
    var excel = url.searchParams.get("excel");

    var id_prioritas = url.searchParams.get("id_prioritas");
    var tipe = url.searchParams.get("tipe");

    var additionalParams = "";
    if (tipe && id_prioritas) {
        additionalParams += "&tipe=" + encodeURIComponent(tipe) + "&id_prioritas=" + encodeURIComponent(id_prioritas);
    }

    if (!rkpd && !excel) {
        var type = url.searchParams.get("type");

        if (type && type === 'detail') {
            var extend_action = '<a class="btn btn-primary" target="_blank" href="' + _url + '" style="margin-left: 10px;">Sembunyikan capaian RENSTRA & RPJM</a>';
        } else {
            var extend_action = '<a class="btn btn-primary" target="_blank" href="' + _url + '&type=detail' + additionalParams + '" style="margin-left: 10px;">Tampilkan capaian RENSTRA & RPJM</a>';
        }

        extend_action += '<a class="btn btn-primary" target="_blank" href="' + _url + '&excel=1' + additionalParams + '" style="margin-left: 10px;"><span class="dashicons dashicons-controls-forward"></span> Format Excel</a>';

        extend_action += '<a class="btn btn-primary" target="_blank" href="' + _url + '&rkpd=1' + additionalParams + '" style="margin-left: 10px;"><span class="dashicons dashicons-controls-forward"></span> Format RKPD</a>';

    } else if (rkpd) {
        var extend_action = '<a class="btn btn-primary" target="_blank" href="' + _url + additionalParams + '" style="margin-left: 10px;"><span class="dashicons dashicons-controls-back"></span> Format RENJA</a>';
    } else if (excel) {
        var extend_action = '<a class="btn btn-primary" target="_blank" href="' + _url + additionalParams + '" style="margin-left: 10px;"><span class="dashicons dashicons-controls-back"></span> Format Renja</a>';

    }

    jQuery('#action-sipd #excel').after(extend_action);
</script>