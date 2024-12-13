<?php
// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}
global $wpdb;

$id_npd = '';
if (!empty($_GET)) {
    if (!empty($_GET['bulan'])) {
        $set_bulan = $_GET['bulan'] * 1;
    } else {
        die('<h1 class="text-center">Ada data yang kosong!</h1>');
    }
} else {
    die('<h1 class="text-center">Ada data yang kosong!</h1>');
}

$input = shortcode_atts(array(
    'kode_sbl' => 'undefined',
    'tahun_anggaran' => '2022'
), $atts);
if (!empty($_GET['kode_sbl'])) {
    $input['kode_sbl'] = $_GET['kode_sbl'];
}

$bulan = "kosong";
if (!empty($set_bulan)) {
    $list_bulan = array(
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    $bulan = $list_bulan[$set_bulan - 1];
}

$api_key = get_option('_crb_api_key_extension');

$data_sbl = explode(".", $input['kode_sbl']);

$unit = array();
$nama_skpd = "";
if (count($data_sbl) > 1) {
    $sql = $wpdb->prepare("
        SELECT nama_skpd
        FROM data_unit 
        WHERE tahun_anggaran = %d
          AND id_skpd = %d 
          AND active = 1
        ORDER BY id_skpd ASC
    ", $input['tahun_anggaran'], $data_sbl[1]);
    $unit = $wpdb->get_var($sql);
    $nama_skpd = $unit;
}

$api_key = get_option('_crb_api_key_extension');

$data_rfk = $wpdb->get_row(
    $wpdb->prepare('
        SELECT 
            s.*,
            u.nama_skpd as nama_sub_skpd_asli,
            u.kode_skpd as kode_sub_skpd_asli,
            uu.nama_skpd as nama_skpd_asli,
            uu.kode_skpd as kode_skpd_asli
        FROM data_sub_keg_bl s
        INNER JOIN data_unit u 
                ON s.id_sub_skpd = u.id_skpd
          AND u.active=s.active
          AND u.tahun_anggaran=s.tahun_anggaran
        INNER JOIN data_unit uu 
                ON uu.id_skpd = u.id_unit
          AND uu.active = s.active
          AND uu.tahun_anggaran = s.tahun_anggaran
        WHERE s.kode_sbl = %s 
          AND s.active = 1
          AND s.tahun_anggaran = %d
    ', $input['kode_sbl'], $input['tahun_anggaran']),
    ARRAY_A
);

if ($data_rfk) {
    $kode_sub_skpd = $data_rfk['kode_sub_skpd_asli'];
    $nama_sub_skpd = $data_rfk['nama_sub_skpd_asli'];
    $kode_skpd = $data_rfk['kode_skpd_asli'];
    $nama_skpd = $data_rfk['nama_skpd_asli'];
    $kode_urusan = $data_rfk['kode_urusan'];
    $nama_urusan = $data_rfk['nama_urusan'];
    $kode_program = $data_rfk['kode_program'];
    $nama_program = $data_rfk['nama_program'];
    $kode_kegiatan = $data_rfk['kode_giat'];
    $nama_kegiatan = $data_rfk['nama_giat'];
    $kode_bidang_urusan = $data_rfk['kode_bidang_urusan'];
    $nama_bidang_urusan = $data_rfk['nama_bidang_urusan'];
    $nama_sub_kegiatan = $data_rfk['nama_sub_giat'];
    $kode_sub_kegiatan = $data_rfk['kode_sub_giat'];
    $nama_sub_kegiatan = str_replace('X.XX', $kode_bidang_urusan, $nama_sub_kegiatan);
    $pagu_kegiatan = number_format($data_rfk['pagu'], 0, ",", ".");
    $id_sub_skpd = $data_rfk['id_sub_skpd'];
} else {
    die('<h1 class="text-center">Sub Kegiatan tidak ditemukan!</h1>');
}

$total_pagu_npd = 0;
$data_pagu_npd = $wpdb->get_var(
    $wpdb->prepare("
        SELECT
            SUM(pagu_dana) as total_pagu
        FROM data_rekening_nota_pencairan_dana
        WHERE kode_sbl = %s
          AND tahun_anggaran = %d
          AND active = 1
    ", $input['kode_sbl'], $input['tahun_anggaran'])
);

if (!empty($data_pagu_npd)) {
    $total_pagu_npd = $data_pagu_npd;
}

if ($set_bulan <= 9) {
    $set_bulan = '0' . $set_bulan;
}
$bulan_terpilih = $input['tahun_anggaran'] . '-' . $set_bulan . '-%';
$bulan_terpilih_2 = date('Y') . '-' . $set_bulan . '-%';
$data_bku = $wpdb->get_results(
    $wpdb->prepare("
        SELECT *
        FROM data_buku_kas_umum_pembantu
        WHERE kode_sbl = %s
          AND tahun_anggaran = %d
          AND active = 1
          AND (
            tanggal_bkup like %s
            OR tanggal_bkup like %s
          )
        ORDER BY tanggal_bkup DESC
    ", $input['kode_sbl'], $input['tahun_anggaran'], $bulan_terpilih, $bulan_terpilih_2),
    ARRAY_A
);

$html = '';
$uraian = '-';
$id_penerimaan = 0;
$total_pagu_npd_sekarang = $total_pagu_npd;
$total_pengeluaran = 0;
if (!empty($data_bku)) {
    foreach ($data_bku as $v) {
        $tanggal =  date_format(date_create($v['tanggal_bkup']), "d/m/Y");
        $uraian = (!empty($v['uraian'])) ? $v['uraian'] : '-';
        $saldo = $total_pagu_npd_sekarang - $v['pagu'];
        $total_pagu_npd_sekarang = $saldo;
        if ($html == '') {
            $penerimaan =
                '<tr>
                <td class="kanan bawah kiri text-center">' . $tanggal . '</td>
                <td class="kanan bawah text-center"></td>
                <td class="kanan bawah text-left">Total Penerimaan</td>
                <td class="kanan bawah text-right">' . number_format($total_pagu_npd, 0, ",", ".") . '</td>
                <td class="kanan bawah text-right"></td>
                <td class="kanan bawah text-right">0</td>
            </tr>';
        } else {
            $penerimaan = '';
        }
        if ($v['tipe'] == 'pengeluaran') {
            $html .= $penerimaan;
            $html .= '
                    <tr>
                        <td class="kanan bawah kiri text-center id-npd-' . $v['id'] . '">' . $tanggal . '</td>
                        <td class="kanan bawah text-center">' . $v['nomor_bukti'] . '</td>
                        <td class="kanan bawah text-left">' . $uraian . '</td>
                        <td class="kanan bawah text-right"></td>
                        <td class="kanan bawah text-right">' . number_format($v['pagu'], 0, ",", ".") . '</td>
                        <td class="kanan bawah text-right">' . number_format($saldo, 0, ",", ".") . '</td>
                    </tr>';

            $total_pengeluaran += $v['pagu'];
        }
    }
}

$nama_pemda = get_option('_crb_daerah');
?>
<style>
    .modal-content label:after {
        content: ' *';
        color: red;
        margin-right: 5px;
    }

    #tabel_detail_nota,
    #tabel_detail_nota td,
    #tabel_detail_nota th {
        border: 0;
        padding: 0px;
    }

    #tabel_detail_nota td:first-of-type {
        width: 10em;
    }

    #tabel_detail_nota td:nth-child(2) {
        width: 1em;
    }

    #table_data_pejabat,
    #table_data_pejabat td,
    #table_data_pejabat th {
        border: 0;
        padding: 0px;
    }
</style>
<div style="padding: 15px;">
    <div>
        <h3 class="text-center">PEMERINTAH <?php echo strtoupper($nama_pemda) ?></br><?php echo $nama_skpd; ?></br>TAHUN ANGGARAN <?php echo $input['tahun_anggaran']; ?></h3>
        <h3 class="text-center">BUKU KAS UMUM PEMBANTU</br>Periode : BULAN <?php echo strtoupper($bulan); ?> <?php echo $input['tahun_anggaran']; ?></h3>
        <table id="tabel_detail_nota" style="margin-top: 30px;">
            <tbody>
                <tr>
                    <td>Kegiatan</td>
                    <td>:</td>
                    <td><?php echo $kode_kegiatan . '  ' . $nama_kegiatan ?></td>
                </tr>
                <tr>
                    <td>Sub Kegiatan</td>
                    <td>:</td>
                    <td><?php echo $kode_sub_kegiatan . '  ' . $nama_sub_kegiatan ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- table -->
    <div style="margin-top:8px;">
        <table id="table_daftar_bku">
            <thead>
                <tr>
                    <th class="atas kanan bawah kiri text-center">Tanggal</th>
                    <th class="atas kanan bawah text-center">Nomor Bukti</th>
                    <th class="atas kanan bawah text-center">Uraian</th>
                    <th class="atas kanan bawah text-center" style="width: 10em;">Penerimaan</th>
                    <th class="atas kanan bawah text-center" style="width: 10em;">Pengeluaran</th>
                    <th class="atas kanan bawah text-center" style="width: 10em;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $html; ?>
            </tbody>
        </table>
    </div>

    <!-- data pejabat -->
    <div style="margin-top:8px;">
        <table id="table_data_pejabat">
            <thead>
                <tr class="text-center">
                    <td>
                        Disetujui oleh</br>PPTK
                    </td>
                    <td>
                        Ditetapkan oleh</br>Staf Administrasi
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr style="height: 7em;">
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="text-center">
                    <td style="font-weight: 700; text-decoration:underline; width: 50%;" contenteditable="true" title="Klik untuk ganti teks!">
                        Nama PPTK
                    </td>
                    <td style="font-weight: 700; text-decoration:underline; width: 50%;" contenteditable="true" title="Klik untuk ganti teks!">
                        Nama Staf administrasi
                    </td>
                </tr>
                <tr class="text-center">
                    <td contenteditable="true" title="Klik untuk ganti teks!">
                        NIP. xxxxxxxxxxxxxxxxxx
                    </td>
                    <td contenteditable="true" title="Klik untuk ganti teks!">
                        NIP. xxxxxxxxxxxxxxxxxx
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        var action = '' +
            '<div id="action-sipd" class="hide-print">' +
            '<button class="btn btn-info ml-2" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Cetak</button>' +
            '</div>';
        jQuery('body').prepend(action);
    });
</script>