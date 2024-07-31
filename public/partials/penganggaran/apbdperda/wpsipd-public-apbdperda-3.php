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
    die('<h1 class="text-center">ID Jadwal Lokal Tidak Boleh Kosong!</h1>');
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
        $nama_skpd = $data_skpd[0]['kode_skpd'].' '.$data_skpd[0]['nama_skpd'];
        $nama_skpd = '<br>' . $nama_skpd;
    } else {
        die('<h1 class="text-center">SKPD tidak ditemukan!</h1>');
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
    global $pendapatan_murni;
    global $pendapatan_pergeseran;
    global $belanja_murni;
    global $belanja_pergeseran;
    global $pembiayaan_penerimaan_murni;
    global $pembiayaan_penerimaan_pergeseran;
    global $pembiayaan_pengeluaran_murni;
    global $pembiayaan_pengeluaran_pergeseran;

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
        if ($type == 'pergeseran') {
            $murni = "<td class='kanan bawah text_kanan text_blok'></td>";
            $selisih = "<td class='kanan bawah text_kanan text_blok'></td>";
        }
        $body_pendapatan .= "
            <tr class='rek_1'>
                <td class='kiri kanan bawah text_blok'>" . $k . "</td>
                <td class='kanan bawah text_blok'>" . $v['nama'] . "</td>
                " . $murni . "
                <td class='kanan bawah text_kanan text_blok'></td>
                " . $selisih . "
                <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
            </tr>";
        foreach ($v['data'] as $kk => $vv) {
            $murni = '';
            $selisih = '';
            if ($type == 'pergeseran') {
                $murni = "<td class='kanan bawah text_kanan text_blok'>" . ubah_minus($vv['totalmurni']) . "</td>";
                $selisih = "<td class='kanan bawah text_kanan text_blok'>" . ubah_minus(($vv['total'] - $vv['totalmurni'])) . "</td>";
            }
            $body_pendapatan .= "
                <tr class='rek_2'>
                    <td class='kiri kanan bawah text_blok'>" . $kk . "</td>
                    <td class='kanan bawah text_blok'>" . $vv['nama'] . "</td>
                    " . $murni . "
                    <td class='kanan bawah text_kanan text_blok'>" . ubah_minus($vv['total']) . "</td>
                    " . $selisih . "
                    <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
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
                        <td class='kanan bawah text_kanan realisasi_simda'></td>
                    </tr>";
                foreach ($vvv['data'] as $kkkk => $vvvv) {
                    $murni = '';
                    $selisih = '';
                    if ($type == 'pergeseran') {
                        $murni = "<td class='kanan bawah text_kanan'>" . ubah_minus($vvvv['totalmurni']) . "</td>";
                        $selisih = "<td class='kanan bawah text_kanan'>" . ubah_minus(($vvvv['total'] - $vvvv['totalmurni'])) . "</td>";
                    }
                    $body_pendapatan .= "
                        <tr class='rek_4'>
                            <td class='kiri kanan bawah'>" . $kkkk . "</td>
                            <td class='kanan bawah'>" . $vvvv['nama'] . "</td>
                            " . $murni . "
                            <td class='kanan bawah text_kanan'>" . ubah_minus($vvvv['total']) . "</td>
                            " . $selisih . "
                            <td class='kanan bawah text_kanan realisasi_simda'></td>
                        </tr>";
                }
            }
        }
    }

    $murni = '';
    $selisih = '';
    if ($type == 'pergeseran') {
        $murni = "<td class='kanan bawah text_kanan text_blok'>" . ubah_minus($data_pendapatan['totalmurni']) . "</td>";
        $selisih = "<td class='kanan bawah text_kanan text_blok'>" . ubah_minus(($data_pendapatan['totalmurni'] - $data_pendapatan['total'])) . "</td>";
    }
    $body_pendapatan .= "
    <tr>
        <td class='kiri kanan bawah'></td>
        <td class='kanan bawah text_kanan text_blok'>Jumlah " . $nama_rekening . "</td>
        " . $murni . "
        <td class='kanan bawah text_kanan text_blok'>" . ubah_minus($data_pendapatan['total']) . "</td>
        " . $selisih . "
        <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
    </tr>";
    if ($nama_rekening == 'Pendapatan') {
        $pendapatan_murni = $data_pendapatan['totalmurni'];
        $pendapatan_pergeseran = $data_pendapatan['total'];
    } else if ($nama_rekening == 'Belanja') {
        $belanja_murni = $data_pendapatan['totalmurni'];
        $belanja_pergeseran = $data_pendapatan['total'];
        $murni = '';
        $selisih = '';
        if ($type == 'pergeseran') {
            $murni = "<td class='kanan bawah text_kanan text_blok'>" . ubah_minus($pendapatan_murni - $belanja_murni) . "</td>";
            $selisih = "<td class='kanan bawah text_kanan text_blok'>" . ubah_minus(($pendapatan_murni - $belanja_murni) - ($pendapatan_pergeseran - $belanja_pergeseran)) . "</td>";
        }
        $body_pendapatan .= "
        <tr>
            <td class='kiri kanan bawah'></td>
            <td class='kanan bawah text_kanan text_blok'>Total Surplus/(Defisit)</td>
            " . $murni . "
            <td class='kanan bawah text_kanan text_blok'>" . ubah_minus($pendapatan_pergeseran - $belanja_pergeseran) . "</td>
            " . $selisih . "
            <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
        </tr>";
    } else if ($nama_rekening == 'Penerimaan Pembiayaan') {
        $pembiayaan_penerimaan_murni = $data_pendapatan['totalmurni'];
        $pembiayaan_penerimaan_pergeseran = $data_pendapatan['total'];
    } else if ($nama_rekening == 'Pengeluaran Pembiayaan') {
        $pembiayaan_pengeluaran_murni = $data_pendapatan['totalmurni'];
        $pembiayaan_pengeluaran_pergeseran = $data_pendapatan['total'];
        $murni = '';
        $selisih = '';
        if ($type == 'pergeseran') {
            $murni = "<td class='kanan bawah text_kanan text_blok'>" . ubah_minus($pembiayaan_penerimaan_murni - $pembiayaan_pengeluaran_murni) . "</td>";
            $selisih = "<td class='kanan bawah text_kanan text_blok'>" . ubah_minus(($pembiayaan_penerimaan_murni - $pembiayaan_pengeluaran_murni) - ($pembiayaan_penerimaan_pergeseran - $pembiayaan_pengeluaran_pergeseran)) . "</td>";
        }
        $body_pendapatan .= "
        <tr>
            <td class='kiri kanan bawah'></td>
            <td class='kanan bawah text_kanan text_blok'>Total Surplus/(Defisit)</td>
            " . $murni . "
            <td class='kanan bawah text_kanan text_blok'>" . ubah_minus($pembiayaan_penerimaan_pergeseran - $pembiayaan_pengeluaran_pergeseran) . "</td>
            " . $selisih . "
            <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
        </tr>";
    }
    if ($baris_kosong) {
        $murni = '';
        $selisih = '';
        if ($type == 'pergeseran') {
            $murni = "<td class='kanan bawah'></td>";
            $selisih = "<td class='kanan bawah'></td>";
        }
        $body_pendapatan .= "
        <tr>
            <td class='kiri kanan bawah' style='color: #fff;'>.</td>
            <td class='kanan bawah'></td>
            " . $murni . "
            <td class='kanan bawah'></td>
            " . $selisih . "
            <td class='kanan bawah realisasi_simda'></td>
        </tr>";
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
        $nama_jadwal = '<h1 class="text-center">Jadwal: ' . $cek_jadwal['nama'] . '</h1>';
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
        $where_jadwal_new = '';
        if (!empty($where_jadwal)) {
            $where_jadwal_new = str_replace('AND id_jadwal', 'AND r.id_jadwal', $where_jadwal);
        }
        $rincian_all = $wpdb->get_results($wpdb->prepare("
            SELECT 
                r.rincian_murni,
                r.rincian,
                r.kode_akun
            FROM data_rka" . $_suffix_sipd . "" . $tabel_history . " r
            WHERE r.tahun_anggaran=%d
                AND r.active=1
                AND r.kode_sbl=%s
                " . $where_jadwal_new . "
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
                    'data' => array(),
                    'sub' => $sub,
                    'sumber_dana' => $dana_result,
                    'indikator_sub_giat' => $indikator_sub_giat,
                    'lokasi' => $lokasi_result
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

            $rek = explode('.', $rincian['kode_akun']);
            $tipe_belanja = $rek[0] . '.' . $rek[1];
        }
    }
    // die(print_r($data_all));
    $body_urusan_subkeg = '<tr><td colspan=4 class="text-bold text-center">Belanja Daerah</td></tr>';

    $total_all = 0;
    $counter = 1;
    foreach ($data_all as $skpd) {
        foreach ($skpd['data'] as $urusan) {
            $body_urusan_subkeg .= '
            <tr data-id="' . $urusan['id'] . '">
                <td>Urusan Pemerintahan</td>
                <td colspan=3>' . $urusan['kode'] . ' ' . $urusan['nama'] . '</td>
            </tr>';
            foreach ($urusan['data'] as $bidang_urusan) {
                $body_urusan_subkeg .= '
                <tr data-id="' . $bidang_urusan['id'] . '">
                    <td>Bidang Urusan Pemerintahan</td>
                    <td colspan=3>' . $bidang_urusan['kode'] . ' ' . $bidang_urusan['nama'] . '</td>
                </tr>';
                $body_urusan_subkeg .= '
                <tr data-id="' . $skpd['id'] . '">
                    <td>Organisasi</td>
                    <td colspan=3>' . $skpd['kode'] . ' ' . $skpd['nama'] . '</td>
                </tr>';
                $body_urusan_subkeg .= '
                <tr data-id="' . $skpd['id_unit'] . '">
                    <td>Unit Organisasi</td>
                    <td colspan=3>' . $skpd['kode_unit'] . ' ' . $skpd['nama_unit'] . '</td>
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
                                <td>Program</td>
                                <td colspan=3>' . $program['kode'] . ' ' . $program['nama'] . '</td>
                            </tr>';
                        $body_urusan_subkeg .= '
                            <tr data-id="' . $program['id'] . '">
                                <td>Indikator Hasil</td>
                                <td colspan=3>' . $indikator . '</td>
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
                            <td>Kegiatan</td>
                            <td colspan=3>' . $kegiatan['kode'] . ' ' . $kegiatan['nama'] . '</td>
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
                                <td>Sub Kegiatan</td>
                                <td colspan=3>' . $data['kode'] . ' ' . $data['nama'] . '</td>
                            </tr>';  
                            
                        }
                    }
                }
            }
        }
    }
}
?>
<style>
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
        <h3 class="text-center text-uppercase">
            <?php echo $nama_pemda; ?><br>
            rincian apbd menurut urusan pemerintahan daerah, organisasi, program, kegiatan,<br>
            sub kegiatan, kelompok, jenis pendapatan, belanja, dan pembiayaan<br>
            tahun anggaran <?php echo $input['tahun_anggaran']; ?>
        </h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center text-uppercase" colspan="4">pendapatan daerah</th>
                </tr>
                <tr>
                    <th class="text-center text-uppercase">kode rekening</th>
                    <th class="text-center text-uppercase">uraian</th>
                    <th class="text-center text-uppercase">jumlah</th>
                    <th class="text-center text-uppercase">dasar hukum</th>
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

</script>