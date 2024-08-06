<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
global $wpdb;

$id_unit = $_GET['id_unit'] ?? '';
$id_jadwal_lokal = $_GET['id_jadwal_lokal'] ?? '';
$type = $_GET['type'] ?? '';
$dari_simda = $_GET['dari_simda'] ?? '';

if (empty($id_jadwal_lokal)) {
    die('<h1 class="text_tengah">ID Jadwal Lokal Tidak Boleh Kosong!</h1>');
}

$input = shortcode_atts(array(
    'id_skpd' => $id_unit,
    'id_jadwal_lokal' => $id_jadwal_lokal,
    'tahun_anggaran' => ''
), $atts);

$nama_pemda = get_option('_crb_daerah');

$jadwal_lokal = $wpdb->get_row(
    $wpdb->prepare("
        SELECT 
            j.nama AS nama_jadwal,
            j.tahun_anggaran,
            j.status,
            j.status_jadwal_pergeseran,
            t.nama_tipe 
        FROM `data_jadwal_lokal` j
        INNER JOIN `data_tipe_perencanaan` t ON t.id=j.id_tipe 
        WHERE j.id_jadwal_lokal=%d
    ", $id_jadwal_lokal)
);
$_suffix_sipd = '';
if (strpos($jadwal_lokal->nama_tipe, '_sipd') == false) {
    $_suffix_sipd = '_lokal';
}

$nama_skpd = '';

if (
    !empty($input['id_skpd'])
    && $input['id_skpd'] != 'all'
) {
    $data_skpd = $wpdb->get_results($wpdb->prepare("
        SELECT 
            s.*,
            u.kode_skpd AS kode_unit,
            u.nama_skpd AS nama_unit
        FROM data_unit s
        JOIN data_unit u on u.id_skpd = s.id_unit
            AND u.active=s.active
            AND u.tahun_anggaran=s.tahun_anggaran
        WHERE s.tahun_anggaran=%d
            and s.active=1
            and s.id_skpd=%d
    ", $input['tahun_anggaran'], $input['id_skpd']), ARRAY_A);
    if (!empty($data_skpd)) {
        $nama_skpd = $data_skpd[0]['kode_skpd'] . ' ' . $data_skpd[0]['nama_skpd'];
        $nama_skpd = '<br>' . $nama_skpd;
    } else {
        die('<h1 class="text_tengah">SKPD tidak ditemukan!</h1>');
    }
} else {
    $data_skpd = $wpdb->get_results($wpdb->prepare("
    select 
        s.*,
        u.kode_skpd AS kode_unit,
        u.nama_skpd AS nama_unit
    FROM data_unit s
    JOIN data_unit u on u.id_skpd = s.id_unit
        AND u.active=s.active
        AND u.tahun_anggaran=s.tahun_anggaran
    WHERE s.tahun_anggaran=%d
        and s.active=1
    order by kode_skpd ASC
", $input['tahun_anggaran']), ARRAY_A);
}
$options_skpd = array();
$options_skpd = $wpdb->get_results($wpdb->prepare("
    select 
        s.*,
        u.kode_skpd AS kode_unit,
        u.nama_skpd AS nama_unit
    FROM data_unit s
    JOIN data_unit u on u.id_skpd = s.id_unit
        AND u.active=s.active
        AND u.tahun_anggaran=s.tahun_anggaran
    WHERE s.tahun_anggaran=%d
        and s.active=1
    order by kode_skpd ASC
", $input['tahun_anggaran']), ARRAY_A);

function ubah_minus($nilai)
{
    if ($nilai < 0) {
        $nilai = abs($nilai);
        return '(' . number_format($nilai, 0, ",", ".") . ')';
    } else {
        return number_format($nilai, 0, ",", ".");
    }
}

function generate_body($rek_pendapatan, $baris_kosong, $type, $nama_rekening, $dari_simda)
{
    global $wpdb;

    if (empty($baris_kosong)) {
        $baris_kosong = false;
    }
    if (empty($type)) {
        $type = 'murni';
    }
    if (empty($dari_simda)) {
        $dari_simda = '0';
    }

    $data_pendapatan = array(
        'data' => array(),
        'realisasi' => 0,
        'total' => 0,
        'totalmurni' => 0
    );
    foreach ($rek_pendapatan as $k => $v) {
        if ($dari_simda != 0 && !empty($v['total_simda'])) {
            $v['totalmurni'] = $v['total_simda'];
        }
        $rek = explode('.', $v['kode_akun']);
        $kode_akun = $rek[0];
        if (!$kode_akun) {
            continue;
        }
        if (empty($data_pendapatan['data'][$kode_akun])) {
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='" . $kode_akun . "'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun1 = $kode_akun . '.' . $rek[1];
        if (empty($data_pendapatan['data'][$kode_akun]['data'][$kode_akun1])) {
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='" . $kode_akun1 . "'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun2 = $kode_akun1 . '.' . $rek[2];
        if (empty($data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2])) {
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='" . $kode_akun2 . "'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun3 = $kode_akun2 . '.' . $rek[3];
        if (empty($data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3])) {
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='" . $kode_akun3 . "'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun4 = $kode_akun3 . '.' . $rek[4];
        if (empty($data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4])) {
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='" . $kode_akun4 . "'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun5 = $kode_akun4 . '.' . $rek[5];
        if (empty($data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5])) {
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='" . $kode_akun5 . "'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if (!isset($v['total'])) {
            $v['total'] = 0;
        }
        $data_pendapatan['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['total'] += $v['total'];
        $data_pendapatan['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['realisasi'] += $v['realisasi'];

        if (!empty($v['totalmurni'])) {
            $data_pendapatan['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['totalmurni'] += $v['totalmurni'];
        }
    }
    $body_pendapatan = '';

    foreach ($data_pendapatan['data'] as $k => $v) {
        $murni = '';
        $selisih = '';
        $total = '"<td class="kanan bawah text_kanan"></td>';

        if ($type == 'pergeseran' && $nama_rekening != 'Belanja') {
            $murni = "<td class='kanan bawah text_kanan'>" . ubah_minus($v['totalmurni']) . "</td>";
            $selisih = "<td class='kanan bawah text_kanan'>" . ubah_minus(($v['total'] - $v['totalmurni'])) . "</td>";
        } else if ($type == 'pergeseran' && $nama_rekening == 'Belanja') {
            $murni = "<td class='kanan bawah text_kanan'></td>";
            $selisih = "<td class='kanan bawah text_kanan'></td>";
        }
        if ($nama_rekening != 'Belanja') {
            $total = "<td class='kanan bawah text_kanan'>" . ubah_minus($v['total']) . "</td>";
        }
        $body_pendapatan .= "
            <tr class='rek_1'>
                <td class='kiri kanan bawah'>" . $k . "</td>
                <td class='kanan bawah'>" . $v['nama'] . "</td>
                " . $murni . "
                " . $total . "
                " . $selisih . "
                <td class='kanan bawah text_kanan'></td>
            </tr>";
        foreach ($v['data'] as $kk => $vv) {
            $murni = '';
            $selisih = '';
            $total = '"<td class="kanan bawah text_kanan"></td>';
            if ($type == 'pergeseran' && $nama_rekening != 'Belanja') {
                $murni = "<td class='kanan bawah text_kanan'>" . ubah_minus($vv['totalmurni']) . "</td>";
                $selisih = "<td class='kanan bawah text_kanan'>" . ubah_minus(($vv['total'] - $vv['totalmurni'])) . "</td>";
            } else if ($type == 'pergeseran' && $nama_rekening == 'Belanja') {
                $murni = "<td class='kanan bawah text_kanan'></td>";
                $selisih = "<td class='kanan bawah text_kanan'></td>";
            }
            if ($nama_rekening != 'Belanja') {
                $total = "<td class='kanan bawah text_kanan'>" . ubah_minus($vv['total']) . "</td>";
            }
            $body_pendapatan .= "
                <tr class='rek_2'>
                    <td class='kiri kanan bawah'>" . $kk . "</td>
                    <td class='kanan bawah'>" . $vv['nama'] . "</td>
                    " . $murni . "
                    " . $total . "
                    " . $selisih . "
                    <td class='kanan bawah text_kanan'></td>
                </tr>";
            foreach ($vv['data'] as $kkk => $vvv) {
                $murni = '';
                $selisih = '';
                if ($type == 'pergeseran') {
                    $murni = "<td class='kanan bawah text_kanan'>" . ubah_minus($vvv['totalmurni']) . "</td>";
                    $selisih = "<td class='kanan bawah text_kanan'>" . ubah_minus(($vvv['total'] - $vvv['totalmurni'])) . "</td>";
                }
                $body_pendapatan .= "
                    <tr class='rek_3'>
                        <td class='kiri kanan bawah'>" . $kkk . "</td>
                        <td class='kanan bawah'>" . $vvv['nama'] . "</td>
                        " . $murni . "
                        <td class='kanan bawah text_kanan'>" . ubah_minus($vvv['total']) . "</td>
                        " . $selisih . "
                        <td class='kanan bawah text_kanan'></td>
                    </tr>";
            }
        }
    }

    return $body_pendapatan;
}

$tabel_history = '';
$where_jadwal = '';
$nama_jadwal = '';
if (!empty($_GET) && !empty($_GET['id_jadwal_lokal'])) {
    $input['id_jadwal_lokal'] = $_GET['id_jadwal_lokal'];
    $cek_jadwal = $wpdb->get_row($wpdb->prepare("
        SELECT
            *
        FROM data_jadwal_lokal
        WHERE id_jadwal_lokal=%d
            AND tahun_anggaran=%d
            AND id_tipe=6
    ", $input['id_jadwal_lokal'], $input['tahun_anggaran']), ARRAY_A);
    if (!empty($cek_jadwal)) {
        if ($cek_jadwal['status'] == 1) {
            $tabel_history = '_history';
            $where_jadwal = $wpdb->prepare(' AND id_jadwal=%d', $input['id_jadwal_lokal']);
        }
        $nama_jadwal = '<h1 class="text_tengah">Jadwal: ' . $cek_jadwal['nama'] . '</h1>';
    }
}

if (
    !empty($input['id_skpd'])
    && $input['id_skpd'] != 'all'
) {
    $sql = $wpdb->prepare("
        SELECT 
            0 AS realisasi,
            kode_akun,
            nama_akun,
            SUM(total) AS total,
            SUM(nilaimurni) AS totalmurni
        FROM data_pendapatan" . $tabel_history . "
        WHERE tahun_anggaran=%d
            AND active=1
            AND id_skpd=%d
            " . $where_jadwal . "
        GROUP BY kode_akun
        ORDER BY kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
} else {
    $sql = $wpdb->prepare("
        SELECT 
            0 AS realisasi,
            kode_akun,
            nama_akun,
            SUM(total) AS total,
            SUM(nilaimurni) AS totalmurni
        FROM data_pendapatan" . $tabel_history . " 
        WHERE tahun_anggaran=%d
            AND active=1
            " . $where_jadwal . "
        GROUP BY kode_akun
        ORDER BY kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_pendapatan = $wpdb->get_results($sql, ARRAY_A);
$body_pendapatan = generate_body($rek_pendapatan, true, $type, 'Pendapatan', $dari_simda);

if (
    !empty($input['id_skpd'])
    && $input['id_skpd'] != 'all'
) {
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            kode_akun,
            nama_akun,
            sum(total) as total,
            sum(nilaimurni) as totalmurni
        from data_pembiayaan" . $tabel_history . "
        where tahun_anggaran=%d
            and type='penerimaan'
            and active=1
            and id_skpd=%d
            " . $where_jadwal . "
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
} else {
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            kode_akun,
            nama_akun,
            sum(total) as total,
            sum(nilaimurni) as totalmurni
        from data_pembiayaan" . $tabel_history . "
        where tahun_anggaran=%d
            and type='penerimaan'
            and active=1
            " . $where_jadwal . "
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_pembiayaan = $wpdb->get_results($sql, ARRAY_A);

$body_pembiayaan = generate_body($rek_pembiayaan, true, $type, 'Penerimaan Pembiayaan', $dari_simda);

if (
    !empty($input['id_skpd'])
    && $input['id_skpd'] != 'all'
) {
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            kode_akun,
            nama_akun,
            sum(total) as total,
            sum(nilaimurni) as totalmurni
        from data_pembiayaan" . $tabel_history . "
        where tahun_anggaran=%d
            and type='pengeluaran'
            and active=1
            and id_skpd=%d
            " . $where_jadwal . "
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
} else {
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            kode_akun,
            nama_akun,
            sum(total) as total,
            sum(nilaimurni) as totalmurni
        from data_pembiayaan" . $tabel_history . "
        where tahun_anggaran=%d
            and type='pengeluaran'
            and active=1
            " . $where_jadwal . "
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_pembiayaan = $wpdb->get_results($sql, ARRAY_A);

$body_pembiayaan .= generate_body($rek_pembiayaan, false, $type, 'Pengeluaran Pembiayaan', $dari_simda);

foreach ($data_skpd as $skpd) {
    $sql = "
        SELECT 
            *
        FROM data_sub_keg_bl" . $_suffix_sipd . "" . $tabel_history . "
        WHERE id_sub_skpd=%d
            AND tahun_anggaran=%d
            AND active=1
            " . $where_jadwal . "
            ORDER BY kode_giat ASC, kode_sub_giat ASC";
    $subkeg = $wpdb->get_results($wpdb->prepare($sql, $skpd['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
    foreach ($subkeg as $kk => $sub) {
        $rincian_all = $wpdb->get_results($wpdb->prepare("
            SELECT 
                r.kode_akun,
                r.nama_akun,
                r.rincian_murni,
                r.rincian,
                r.kode_akun
            FROM data_rka" . $_suffix_sipd . "" . $tabel_history . " r
            WHERE r.tahun_anggaran=%d
                AND r.active=1
                AND r.kode_sbl=%s
                " . $where_jadwal . "
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

        $dana_query = $wpdb->prepare("
            SELECT namadana
            FROM data_dana_sub_keg" . $_suffix_sipd . "" . $tabel_history . "
            WHERE kode_sbl = %s
                AND tahun_anggaran = %d
                AND active = 1
        ", $sub['kode_sbl'], $input['tahun_anggaran']);
        $dana_result = $wpdb->get_results($dana_query, ARRAY_A);

        $lokasi_result = $wpdb->get_results($wpdb->prepare("
            SELECT 
                *
            FROM data_lokasi_sub_keg" . $_suffix_sipd . "" . $tabel_history . "
            WHERE kode_sbl = %s
                AND tahun_anggaran = %d
                AND active = 1
            ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

        $indikator_program = $wpdb->get_results($wpdb->prepare("
            SELECT 
                *
            FROM data_capaian_prog_sub_keg" . $_suffix_sipd . "" . $tabel_history . "
            WHERE tahun_anggaran=%d
                AND active=1
                AND kode_sbl=%s
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

        $indikator_giat = $wpdb->get_results($wpdb->prepare("
            SELECT 
                *
            FROM data_output_giat_sub_keg" . $_suffix_sipd . "" . $tabel_history . "
            WHERE tahun_anggaran=%d
                AND active=1
                AND kode_sbl=%s
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

        $indikator_sub_giat = $wpdb->get_results($wpdb->prepare("
            SELECT 
                *
            FROM data_sub_keg_indikator" . $_suffix_sipd . "" . $tabel_history . "
            WHERE tahun_anggaran=%d
                AND active=1
                AND kode_sbl=%s
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

        foreach ($rincian_all as $rincian) {
            if (empty($data_all[$sub['id_sub_skpd']])) {
                $data_all[$sub['id_sub_skpd']] = array(
                    'id' => $sub['id_sub_skpd'],
                    'kode' => $sub['kode_sub_skpd'],
                    'nama' => $sub['nama_sub_skpd'],
                    'id_unit' => $skpd['id_unit'],
                    'kode_unit' => $skpd['kode_unit'],
                    'nama_unit' => $skpd['nama_unit'],
                    'operasi' => 0,
                    'modal' => 0,
                    'tak_terduga' => 0,
                    'transfer' => 0,
                    'total' => 0,
                    'operasi_murni' => 0,
                    'modal_murni' => 0,
                    'tak_terduga_murni' => 0,
                    'transfer_murni' => 0,
                    'total_murni' => 0,
                    'data' => array()
                );
            }
            if (empty($data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']])) {
                $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']] = array(
                    'id' => $sub['id_urusan'],
                    'kode' => $sub['kode_urusan'],
                    'nama' => $sub['nama_urusan'],
                    'operasi' => 0,
                    'modal' => 0,
                    'tak_terduga' => 0,
                    'transfer' => 0,
                    'total' => 0,
                    'operasi_murni' => 0,
                    'modal_murni' => 0,
                    'tak_terduga_murni' => 0,
                    'transfer_murni' => 0,
                    'total_murni' => 0,
                    'data' => array()
                );
            }
            if (empty($data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])) {
                $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
                    'id' => $sub['id_bidang_urusan'],
                    'kode' => $sub['kode_bidang_urusan'],
                    'nama' => $sub['nama_bidang_urusan'],
                    'operasi' => 0,
                    'modal' => 0,
                    'tak_terduga' => 0,
                    'transfer' => 0,
                    'total' => 0,
                    'operasi_murni' => 0,
                    'modal_murni' => 0,
                    'tak_terduga_murni' => 0,
                    'transfer_murni' => 0,
                    'total_murni' => 0,
                    'data' => array()
                );
            }
            if (empty($data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']])) {
                $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']] = array(
                    'id' => $sub['id_program'],
                    'kode' => $sub['kode_program'],
                    'nama' => $sub['nama_program'],
                    'operasi' => 0,
                    'modal' => 0,
                    'tak_terduga' => 0,
                    'transfer' => 0,
                    'total' => 0,
                    'operasi_murni' => 0,
                    'modal_murni' => 0,
                    'tak_terduga_murni' => 0,
                    'transfer_murni' => 0,
                    'total_murni' => 0,
                    'indikator_program' => $indikator_program,
                    'data' => array()
                );
            }
            if (empty($data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']])) {
                $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']] = array(
                    'id' => $sub['id_giat'],
                    'kode' => $sub['kode_giat'],
                    'nama' => $sub['nama_giat'],
                    'operasi' => 0,
                    'modal' => 0,
                    'tak_terduga' => 0,
                    'transfer' => 0,
                    'total' => 0,
                    'operasi_murni' => 0,
                    'modal_murni' => 0,
                    'tak_terduga_murni' => 0,
                    'transfer_murni' => 0,
                    'total_murni' => 0,
                    'indikator_giat' => $indikator_giat,
                    'data' => array()
                );
            }
            if (empty($data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']])) {
                $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']] = array(
                    'id' => $sub['id_sub_giat'],
                    'kode' => $sub['kode_sub_giat'],
                    'nama' => $sub['nama_sub_giat'],
                    'operasi' => 0,
                    'modal' => 0,
                    'tak_terduga' => 0,
                    'transfer' => 0,
                    'total' => 0,
                    'operasi_murni' => 0,
                    'modal_murni' => 0,
                    'tak_terduga_murni' => 0,
                    'transfer_murni' => 0,
                    'total_murni' => 0,
                    'nama_akun' => 0,
                    'sub' => $sub,
                    'sumber_dana' => $dana_result,
                    'indikator_sub_giat' => $indikator_sub_giat,
                    'lokasi' => $lokasi_result,
                    'data' => array()
                );
            }

            $data_all[$sub['id_sub_skpd']]['total'] += $rincian['rincian'];
            $data_all[$sub['id_sub_skpd']]['total_murni'] += $rincian['rincian_murni'];

            $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total'] += $rincian['rincian'];
            $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total_murni'] += $rincian['rincian_murni'];

            $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total'] += $rincian['rincian'];
            $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_murni'] += $rincian['rincian_murni'];

            $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total'] += $rincian['rincian'];
            $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_murni'] += $rincian['rincian_murni'];

            $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total'] += $rincian['rincian'];
            $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_murni'] += $rincian['rincian_murni'];

            $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total'] += $rincian['rincian'];
            $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_murni'] += $rincian['rincian_murni'];

            $data_all[$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['data'][] = $rincian;
        }
    }
    //colspan
    $body_urusan_subkeg = '<tr><td colspan=4 class="text_blok text_tengah kiri kanan bawah">BELANJA DAERAH</td></tr>';
    $colspan = 3;
    $colspan_header = '';
    $colspan_header_2 = 4;
    $murni = "";
    $selisih = "";

    if ($type == 'pergeseran') {
        $body_urusan_subkeg = '<tr><td colspan=6 class="text_blok text_tengah kiri kanan bawah">BELANJA DAERAH</td></tr>';
        $murni = "<td class='kanan bawah text_tengah text_blok'>JUMLAH SEBELUM</td>";
        $selisih = "<td class='kanan bawah text_tengah text_blok'>SELISIH</td>";
        $colspan = 5;
        $colspan_header = 2;
        $colspan_header_2 = 6;
    }

    $total_all = 0;
    $counter = 1;
    foreach ($data_all as $skpd) {
        foreach ($skpd['data'] as $urusan) {
            $body_urusan_subkeg .= '
            <tr data-id="' . $urusan['id'] . '">
                <td class="kiri kanan bawah">Urusan Pemerintahan</td>
                <td class="kiri kanan bawah" colspan=' . $colspan . '>' . $urusan['kode'] . ' ' . $urusan['nama'] . '</td>
            </tr>';
            foreach ($urusan['data'] as $bidang_urusan) {
                $body_urusan_subkeg .= '
                <tr data-id="' . $bidang_urusan['id'] . '">
                    <td class="kiri kanan bawah">Bidang Urusan Pemerintahan</td>
                    <td colspan=' . $colspan . ' class="align-middle kiri kanan bawah ">' . $bidang_urusan['kode'] . ' ' . $bidang_urusan['nama'] . '</td>
                </tr>';
                $body_urusan_subkeg .= '
                <tr data-id="' . $skpd['id'] . '">
                    <td class="kiri kanan bawah">Organisasi</td>
                    <td colspan=' . $colspan . ' class="align-middle kiri kanan bawah ">' . $skpd['kode_unit'] . ' ' . $skpd['nama_unit'] . '</td>
                </tr>';
                $body_urusan_subkeg .= '
                <tr data-id="' . $skpd['id_unit'] . '">
                    <td class="kiri kanan bawah">Unit Organisasi</td>
                    <td colspan=' . $colspan . ' class="align-middle kiri kanan bawah ">' . $skpd['kode'] . ' ' . $skpd['nama'] . '</td>
                </tr>';
                foreach ($bidang_urusan['data'] as $program) {
                    $indikator = array();
                    $target = array();
                    foreach ($program['indikator_program'] as $ind) {
                        $indikator[] = $ind['capaianteks'];
                        $target[] = $ind['targetcapaianteks'];
                    }
                    $indikator = implode('<br>', $indikator);
                    $target = implode('<br>', $target);
                    $body_urusan_subkeg .= '
                        <tr data-id="' . $program['id'] . '">
                            <td class="kiri kanan bawah">Program</td>
                            <td colspan=' . $colspan . ' class="align-middle kiri kanan bawah ">' . $program['kode'] . ' ' . $program['nama'] . '</td>
                        </tr>';
                    $body_urusan_subkeg .= '
                        <tr>
                            <td class="kiri kanan bawah">Indikator Hasil</td>
                            <td colspan=' . $colspan . ' class="align-middle kiri kanan bawah ">' . $indikator . '</td>
                        </tr>';
                    foreach ($program['data'] as $kegiatan) {
                        $indikator = array();
                        $target = array();
                        foreach ($kegiatan['indikator_giat'] as $ind) {
                            $indikator[] = $ind['outputteks'];
                            $target[] = $ind['targetoutputteks'];
                        }
                        $indikator = implode('<br>', $indikator);
                        $target = implode('<br>', $target);
                        $body_urusan_subkeg .= '
                        <tr data-id="' . $kegiatan['id'] . '">
                            <td class="kiri kanan bawah">Kegiatan</td>
                            <td colspan=' . $colspan . ' class="align-middle kiri kanan bawah ">' . $kegiatan['kode'] . ' ' . $kegiatan['nama'] . '</td>
                        </tr>';
                        $body_urusan_subkeg .= '
                        <tr>
                            <td class="kiri kanan bawah">Indikator Keluaran</td>
                            <td colspan=' . $colspan . ' class="align-middle kiri kanan bawah ">' . $indikator . '</td>
                        </tr>';
                        foreach ($kegiatan['data'] as $kode => $data) {
                            $total_all += $data['total'];
                            $parts = explode(' ', $data['sub']['nama_sub_giat'], 2);
                            $nama_sub_giat = $parts[1];

                            $sumber_dana = array();
                            foreach ($data['sumber_dana'] as $sd) {
                                $sumber_dana[] = $sd['namadana'];
                            }
                            $sumber_dana = implode('<br>', $sumber_dana);

                            $indikator = array();
                            $target = array();
                            foreach ($data['indikator_sub_giat'] as $ind) {
                                $indikator[] = $ind['outputteks'];
                                $target[] = $ind['targetoutputteks'];
                            }
                            $indikator = implode('<br>', $indikator);
                            $target = implode('<br>', $target);

                            $lokasi = array();
                            foreach ($data['lokasi'] as $lks) {
                                $lokasi[] = $lks['daerahteks'];
                            }
                            $lokasi = implode('<br>', $lokasi);

                            $body_urusan_subkeg .= '
                            <tr data-id="' . $data['id'] . '">
                                <td class="kiri kanan bawah">Sub Kegiatan</td>
                                <td colspan=' . $colspan . ' class="align-middle kiri kanan bawah ">' . $data['kode'] . ' ' . $nama_sub_giat . '</td>
                            </tr>';
                            $body_urusan_subkeg .= '
                            <tr>
                                <td class="kiri kanan bawah">Indikator Keluaran</td>
                                <td colspan=' . $colspan . ' class="align-middle kiri kanan bawah ">' . $indikator . '</td>
                            </tr>';
                            $body_urusan_subkeg .= '
                            <tr>
                                <td class="kiri kanan bawah text_blok text_tengah">KODE REKENING</td>
                                <td class="kiri kanan bawah text_blok text_tengah">URAIAN</td>
                                ' . $murni . '
                                <td class="kiri kanan bawah text_blok text_tengah">JUMLAH</td>
                                ' . $selisih . '
                                <td class="kiri kanan bawah text_blok text_tengah">DASAR HUKUM</td>
                            </tr>';
                            $rka_sub_all = array();
                            foreach ($data['data'] as $rincian) {
                                if (empty($rka_sub_all[$rincian['kode_akun']])) {
                                    $rka_sub_all[$rincian['kode_akun']] = array(
                                        'realisasi' => 0,
                                        'kode_akun' => $rincian['kode_akun'],
                                        'nama_akun' => $rincian['nama_akun'],
                                        'total' => 0,
                                        'totalmurni' => 0
                                    );
                                }
                                $rka_sub_all[$rincian['kode_akun']]['total'] += $rincian['rincian'];
                                $rka_sub_all[$rincian['kode_akun']]['totalmurni'] += $rincian['rincian_murni'];
                            }
                            $body_urusan_subkeg .= generate_body($rka_sub_all, true, $type, 'Belanja', $dari_simda);
                        }
                    }
                }
            }
        }
    }
}
?>
<style>
    @media print {
        #cetak {
            max-width: auto !important;
            height: auto !important;
        }

        #print_laporan {
            display: none;
        }
    }
</style>

<body>
    <div id="cetak" title="APBD PERDA Lampiran III" style="padding: 5px; overflow: auto;">
        <table align="right" class="no-border no-padding" style="width:280px; font-size: 12px;">
            <tr>
                <td width="80" class="align-top">Lampiran III </td>
                <td width="10" class="align-top">:</td>
                <td colspan="3" class="align-top" contenteditable="true"> Peraturan Daerah xxxxx </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td width="10">&nbsp;</td>
                <td width="60" class="text-start">Nomor</td>
                <td width="10">:</td>
                <td class="text-start" contenteditable="true">&nbsp;xx Desember xxxx</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td width="10">&nbsp;</td>
                <td width="60" class="text-start">Tanggal</td>
                <td width="10">:</td>
                <td class="text-start" contenteditable="true">&nbsp;xx Desember xxx</td>
            </tr>
        </table>
        <h3 class="text_tengah text-uppercase">
            <?php echo htmlspecialchars($nama_pemda, ENT_QUOTES, 'UTF-8'); ?><br>
            RINCIAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI, PROGRAM, KEGIATAN,<br>
            SUB KEGIATAN, KELOMPOK, JENIS PENDAPATAN, BELANJA, DAN PEMBIAYAAN<br>
            TAHUN ANGGARAN <?php echo htmlspecialchars($input['tahun_anggaran'], ENT_QUOTES, 'UTF-8'); ?>
        </h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text_tengah kiri kanan bawah atas" colspan="<?php echo $colspan_header_2; ?>">PENDAPATAN DAERAH</th>
                </tr>
                <tr>
                    <th class="text_tengah kiri kanan bawah text_blok">KODE REKENING</th>
                    <th class="text_tengah kiri kanan bawah text_blok">URAIAN</th>
                    <?php if ($type == 'pergeseran') : ?>
                        <th class="text_tengah kiri kanan bawah text_blok">JUMLAH SEBELUM</th>
                    <?php endif; ?>
                    <th class="text_tengah kiri kanan bawah text_blok">JUMLAH</th>
                    <?php if ($type == 'pergeseran') : ?>
                        <th class="text_tengah kiri kanan bawah text_blok">SELISIH</th>
                    <?php endif; ?>
                    <th class="text_tengah kiri kanan bawah text_blok">DASAR HUKUM</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $body_pendapatan; ?>
                <?php echo $body_pembiayaan; ?>
                <?php echo $body_urusan_subkeg; ?>
            </tbody>
        </table>
    </div>
</body>
<script>
    jQuery(document).ready(function() {
        run_download_excel();

        var list_skpd = <?php echo json_encode($options_skpd); ?>;
        window._url = new URL(window.location.href);
        window.new_url = changeUrl({
            url: _url.href,
            key: 'key',
            value: '<?php echo $this->gen_key(); ?>'
        });

        window.type = _url.searchParams.get("type");
        window.dari_simda = _url.searchParams.get("dari_simda");
        window.id_skpd = _url.searchParams.get("id_unit");

        var extend_action = '';
        if (type && type === 'pergeseran') {
            extend_action += '<a class="btn btn-primary" target="_blank" href="' + removeTypeParam(new_url) + '" style="margin-left: 10px;"><span class="dashicons dashicons-controls-back"></span> Halaman APBD Perda Lampiran III</a>';
        } else {
            extend_action += '<a class="btn btn-primary" target="_blank" href="' + new_url + '&type=pergeseran" style="margin-left: 10px;"><span class="dashicons dashicons-controls-forward"></span> Halaman Pergeseran/Perubahan APBD Perda Lampiran III</a>';
        }

        var options = '<option value="">Semua SKPD</option>';
        list_skpd.map(function(b) {
            var selected = (id_skpd && id_skpd == b.id_skpd) ? 'selected' : '';
            options += '<option ' + selected + ' value="' + b.id_skpd + '">' + b.kode_skpd + ' ' + b.nama_skpd + '</option>';
        });

        extend_action += '<button class="btn btn-info m-3" id="print_laporan" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Cetak Laporan</button><br>';
        extend_action += '<label for="options_skpd" class="mr-3">Pilih Perangkat Daerah</label>';
        extend_action += '<select id="pilih_skpd" name="options_skpd" onchange="ubah_skpd();" style="width:500px; margin-left:25px;">' + options + '</select>';
        extend_action += '</div>';

        jQuery('#action-sipd').append(extend_action);
        jQuery('#pilih_skpd').select2();
    });

    function removeTypeParam(url) {
        let urlObj = new URL(url);
        urlObj.searchParams.delete("type");
        return urlObj.href;
    }


    function ubah_skpd() {
        var pilih_id_skpd = jQuery('#pilih_skpd').val();
        var updated_url = _url.href;

        if (type) {
            updated_url = changeUrl({
                url: updated_url,
                key: 'type',
                value: type
            });
        }
        if (dari_simda) {
            updated_url = changeUrl({
                url: updated_url,
                key: 'dari_simda',
                value: dari_simda
            });
        }
        updated_url = changeUrl({
            url: updated_url,
            key: 'id_unit',
            value: pilih_id_skpd
        });

        window.open(updated_url);
        jQuery('#pilih_skpd').val(id_skpd);
    }
</script>