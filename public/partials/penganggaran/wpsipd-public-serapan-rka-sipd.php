<?php
global $wpdb;
$input = shortcode_atts(array(
    'kode_sbl' => '',
    'tahun_anggaran' => '2021'
), $atts);

if (
    !empty($input['tahun_anggaran'])
    && !empty($input['kode_sbl'])
) {
    $tahun_anggaran = $input['tahun_anggaran'];
    $kode_sbl = $input['kode_sbl'];
} else {
    die('<h1 class="text_tengah">Tahun Anggaran dan Kode Sub Kegiatan tidak boleh kosong!</h1>');
}

$api_key = get_option('_crb_api_key_extension');

$data_rfk = $wpdb->get_row($wpdb->prepare('
	SELECT 
		s.*,
		u.nama_skpd as nama_sub_skpd_asli,
		u.kode_skpd as kode_sub_skpd_asli,
		uu.nama_skpd as nama_skpd_asli,
		uu.kode_skpd as kode_skpd_asli
	FROM data_sub_keg_bl s
	INNER JOIN data_unit u on s.id_sub_skpd=u.id_skpd
		AND u.active=s.active
		AND u.tahun_anggaran=s.tahun_anggaran
	INNER JOIN data_unit uu on uu.id_skpd=u.id_unit
		AND uu.active=s.active
		AND uu.tahun_anggaran=s.tahun_anggaran
	WHERE s.kode_sbl = %s 
		AND s.active = 1
		AND s.tahun_anggaran = %d', $kode_sbl, $tahun_anggaran), ARRAY_A);

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
    $pagu_sub_kegiatan = number_format($data_rfk['pagu'], 0, ",", ".");
    $id_sub_skpd = $data_rfk['id_sub_skpd'];
} else {
    die('<h1 class="text_tengah">Sub Kegiatan tidak ditemukan!</h1>');
}

$set_bulan = date('m');
if (
    !empty($_GET)
    && !empty($_GET['bulan'])
) {
    $set_bulan = $_GET['bulan'] * 1;
}
$nama_bulan = $this->get_bulan($set_bulan);

if ($set_bulan <= 9) {
    $set_bulan = '0' . $set_bulan;
}
$bulan_terpilih = $input['tahun_anggaran'] . '-' . $set_bulan . '-31 23:59:59';
$bulan_terpilih_2 = date('Y') . '-' . $set_bulan . '-31 23:59:59';

$rinc = $wpdb->get_results($wpdb->prepare("
    SELECT 
        r.*
    from data_rka r 
    where r.kode_sbl=%s
        AND r.tahun_anggaran=%d
        AND r.active=1
    Order by r.kode_akun ASC, r.subs_bl_teks ASC, r.ket_bl_teks ASC, r.id_rinci_sub_bl ASC
", $kode_sbl, $tahun_anggaran), ARRAY_A);

$akun_all = array();
foreach ($rinc as $key => $item) {
    if (empty($item['kode_akun'])) {
        continue;
    }
    if (empty($akun_all[$item['kode_akun']])) {
        $nama_akun = str_replace($item['kode_akun'], '', $item['nama_akun']);
        $bku = $wpdb->get_results($wpdb->prepare("
            SELECT
                *
            FROM data_buku_kas_umum_pembantu
            WHERE active=1
                AND tahun_anggaran=%d
                AND kode_sbl=%s
                AND kode_rekening=%s
                AND (
                    tanggal_bkup <= %s
                    OR tanggal_bkup <= %s
                )
            ORDER BY tanggal_bkup
        ", $tahun_anggaran, $kode_sbl, $item['kode_akun'], $bulan_terpilih, $bulan_terpilih_2), ARRAY_A);
        $akun_all[$item['kode_akun']] = array(
            'total' => 0,
            'total_murni' => 0,
            'realisasi' => 0,
            'status' => 0,
            'kode_akun' => $item['kode_akun'],
            'nama_akun' => $nama_akun,
            'bku' => array(),
            'rinci' => array()
        );
        foreach ($bku as $bukti) {
            $akun_all[$item['kode_akun']]['realisasi'] += $bukti['pagu'];
            if (empty($akun_all[$item['kode_akun']]['bku'][$bukti['id_rinci_sub_bl']])) {
                $akun_all[$item['kode_akun']]['bku'][$bukti['id_rinci_sub_bl']] = array();
            }
            $akun_all[$item['kode_akun']]['bku'][$bukti['id_rinci_sub_bl']][] = $bukti;
        }
    }
    $akun_all[$item['kode_akun']]['total'] += $item['total_harga'];
    $akun_all[$item['kode_akun']]['total_murni'] += $item['rincian_murni'];
    if (empty($akun_all[$item['kode_akun']]['rinci'][$item['id_rinci_sub_bl']])) {
        $akun_all[$item['kode_akun']]['rinci'][$item['id_rinci_sub_bl']] = array(
            'val' => $item,
            'bukti' => array()
        );
    }
    if (!empty($akun_all[$item['kode_akun']]['bku'][$item['id_rinci_sub_bl']])) {
        $akun_all[$item['kode_akun']]['rinci'][$item['id_rinci_sub_bl']]['bukti'] = $akun_all[$item['kode_akun']]['bku'][$item['id_rinci_sub_bl']];
        unset($akun_all[$item['kode_akun']]['bku'][$item['id_rinci_sub_bl']]);
    }
}

$body = '';
$total_anggaran = 0;
$total_sisa = 0;
$total_realisasi = 0;
foreach ($akun_all as $akun) {
    $sisa_akun = $akun['total'] - $akun['realisasi'];
    $body .= '
    <tr style="font-weight: 600;">
        <td class="atas kanan bawah kiri text_tengah">' . $akun['kode_akun'] . '</td>
        <td class="atas kanan bawah kiri">' . $akun['nama_akun'] . '</td>
        <td class="atas kanan bawah kiri text_kanan">' . number_format($akun['total'], 0, ",", ".") . '</td>
        <td class="atas kanan bawah kiri"></td>
        <td class="atas kanan bawah kiri"></td>
        <td class="atas kanan bawah kiri"></td>
        <td class="atas kanan bawah kiri"></td>
        <td class="atas kanan bawah kiri text_kanan">' . number_format($akun['realisasi'], 0, ",", ".") . '</td>
        <td class="atas kanan bawah kiri text_kanan">' . number_format($sisa_akun, 0, ",", ".") . '</td>
    </tr>';

    // tampilkan rincian RKA
    foreach ($akun['rinci'] as $key => $item) {
        $alamat_array = $this->get_alamat($input, $item['val']);
        if (!empty($alamat_array['keterangan'])) {
            $keterangan_alamat[] = $alamat_array['keterangan'];
        }
        $alamat = $alamat_array['alamat'];
        $lokus_akun_teks = $alamat_array['lokus_akun_teks_decode'];

        // jika alamat kosong maka cek id penerima bantuan
        if (empty($alamat)) {
            $alamat = array();
            if (!empty($item['val']['id_lurah_penerima'])) {
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=" . $item['val']['id_lurah_penerima'] . " and is_kel=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            if (!empty($item['val']['id_camat_penerima'])) {
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=" . $item['val']['id_camat_penerima'] . " and is_kec=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            if (!empty($item['val']['id_kokab_penerima'])) {
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=" . $item['val']['id_kokab_penerima'] . " and is_kab=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            if (!empty($item['val']['id_prop_penerima'])) {
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=" . $item['val']['id_prop_penerima'] . " and is_prov=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            $profile_penerima = implode(', ', $alamat);
        } else {

            // jika lokus akun teks ada di nama komponen
            if (
                strpos($item['val']['nama_komponen'], $lokus_akun_teks) !== false
                || $lokus_akun_teks == $alamat
            ) {
                $profile_penerima = $alamat;
            } else {
                $profile_penerima = $lokus_akun_teks . ', ' . $alamat;
            }
        }

        // tampilkan bku yang sudah terkoneksi ke rincian belanja
        if (!empty($item['bukti'])) {
            $realisasi = 0;
            foreach ($item['bukti'] as $k => $bukti) {
                $realisasi += $bukti['pagu'];
                if ($k == 0) {
                    $vol = 0;
                    if (!empty($bukti['koefisien'])) {
                        $vol = explode(' ', $bukti['koefisien']);
                        $vol = $vol[0];
                    }
                    $body .= '
                    <tr data-id="' . $item['val']['id_rinci_sub_bl'] . '" data-id-bukti="' . $bukti['id'] . '">
                        <td class="atas kanan bawah kiri"></td>
                        <td class="atas kanan bawah kiri">
                            <div>' . $item['val']['nama_komponen'] . '</div>
                            <div>' . $item['val']['spek_komponen'] . '</div>
                            <div>' . $profile_penerima . '</div>
                        </td>
                        <td class="atas kanan bawah kiri text_kanan">' . number_format($item['val']['total_harga'], 0, ",", ".") . '</td>
                        <td class="atas kanan bawah kiri text_tengah">' . $vol . '</td>
                        <td class="atas kanan bawah kiri text_tengah">' . $item['val']['satuan'] . '</td>
                        <td class="atas kanan bawah kiri text_tengah">' . $bukti['nomor_bukti'] . '</td>
                        <td class="atas kanan bawah kiri text_tengah">' . $bukti['uraian'] . '</td>
                        <td class="atas kanan bawah kiri text_kanan">' . number_format($bukti['pagu'], 0, ",", ".") . '</td>
                        <td class="atas kanan bawah kiri text_kanan">' . number_format($item['val']['total_harga'] - $realisasi, 0, ",", ".") . '</td>
                    </tr>
                    ';
                } else {
                    $body .= '
                    <tr data-id="' . $item['val']['id_rinci_sub_bl'] . '" data-id-bukti="' . $bukti['id'] . '">
                        <td class="atas kanan bawah kiri"></td>
                        <td class="atas kanan bawah kiri"></td>
                        <td class="atas kanan bawah kiri text_kanan"></td>
                        <td class="atas kanan bawah kiri text_tengah"></td>
                        <td class="atas kanan bawah kiri text_tengah"></td>
                        <td class="atas kanan bawah kiri text_tengah">' . $bukti['nomor_bukti'] . '</td>
                        <td class="atas kanan bawah kiri text_tengah">' . $bukti['uraian'] . '</td>
                        <td class="atas kanan bawah kiri text_kanan">' . number_format($bukti['pagu'], 0, ",", ".") . '</td>
                        <td class="atas kanan bawah kiri text_kanan">' . number_format($item['val']['total_harga'] - $realisasi, 0, ",", ".") . '</td>
                    </tr>
                    ';
                }
                $total_realisasi += $bukti['pagu'];
            }

            // tampilkan rincian belanja yang belum ada BKU nya
        } else {
            $vol = 0;
            if (!empty($item['val']['koefisien'])) {
                $vol = explode(' ', $item['val']['koefisien']);
                $vol = $vol[0];
            }
            $body .= '
            <tr data-id="' . $item['val']['id_rinci_sub_bl'] . '">
                <td class="atas kanan bawah kiri"></td>
                <td class="atas kanan bawah kiri">
                    <div>' . $item['val']['nama_komponen'] . '</div>
                    <div>' . $item['val']['spek_komponen'] . '</div>
                    <div>' . $profile_penerima . '</div>
                </td>
                <td class="atas kanan bawah kiri text_kanan">' . number_format($item['val']['total_harga'], 0, ",", ".") . '</td>
                <td class="atas kanan bawah kiri text_tengah">' . $vol . '</td>
                <td class="atas kanan bawah kiri text_tengah">' . $item['val']['satuan'] . '</td>
                <td class="atas kanan bawah kiri"></td>
                <td class="atas kanan bawah kiri"></td>
                <td class="atas kanan bawah kiri text_kanan">0</td>
                <td class="atas kanan bawah kiri text_kanan">' . number_format($item['val']['total_harga'], 0, ",", ".") . '</td>
            </tr>
            ';
        }
        $total_anggaran += $item['val']['total_harga'];
    }

    // tampilkan bku yang belum terkoneksi ke rincian belanja
    foreach ($akun['bku'] as $bukti_rinci) {
        foreach ($bukti_rinci as $bukti) {
            $body .= '
            <tr data-id-bukti="' . $bukti['id'] . '">
                <td class="atas kanan bawah kiri"></td>
                <td class="atas kanan bawah kiri" style="background-color : #FFADAD">Rincian bukti tidak terkoneksi ke RKA/DPA</td>
                <td class="atas kanan bawah kiri text_kanan"></td>
                <td class="atas kanan bawah kiri text_tengah"></td>
                <td class="atas kanan bawah kiri text_tengah"></td>
                <td class="atas kanan bawah kiri">' . $bukti['nomor_bukti'] . '</td>
                <td class="atas kanan bawah kiri">' . $bukti['uraian'] . '</td>
                <td class="atas kanan bawah kiri text_kanan">' . number_format($bukti['pagu'], 0, ",", ".") . '</td>
                <td class="atas kanan bawah kiri text_kanan">' . number_format(0 - $bukti['pagu'], 0, ",", ".") . '</td>
            </tr>
            ';
            $total_realisasi += $bukti['pagu'];
        }
    }
}

$total_sisa = $total_anggaran - $total_realisasi;
?>
<style>
    #tabel_detail_nota,
    #tabel_detail_nota td,
    #tabel_detail_nota th {
        border: 0;
    }

    .sticky-header {
        position: sticky;
        top: 0;
        background-color: #007bff;
        color: white;
        z-index: 2;
        text-align: center;
    }

    #table-sticky {
        width: 100%;
        border-collapse: collapse;
    }

    /* Sticky header */
    #table-sticky thead th {
        position: sticky;
        top: 0;
        background-color: #007bff;
        color: white;
        z-index: 2;
        text-align: center;
        vertical-align: center;
    }

    /* Sticky footer */
    #table-sticky tfoot th {
        position: sticky;
        bottom: 0;
        background-color: #007bff;
        color: white;
        z-index: 1;
        text-align: center;
    }

    #table-sticky thead tr:nth-child(1) th,
    #table-sticky thead tr:nth-child(2) th {
        position: sticky;
        top: 0;
        background-color: #007bff;
        color: white;
        z-index: 3;
    }

    #table-sticky thead tr:nth-child(2) th {
        top: 38px;
        z-index: 2;
    }

    .text_tengah {
        text-align: center;
    }

    .text_kanan {
        text-align: right;
    }

    .text_kiri {
        text-align: left;
    }

    .kiri {
        border-left: 1px solid black;
    }

    .kanan {
        border-right: 1px solid black;
    }

    .atas {
        border-top: 1px solid black;
    }

    .bawah {
        border-bottom: 1px solid black;
    }

    @media print {

        /* Hilangkan semua efek sticky */
        #table-sticky thead th,
        #table-sticky tfoot th,
        #table-sticky thead tr:nth-child(1) th,
        #table-sticky thead tr:nth-child(2) th {
            position: static !important;
            background-color: transparent !important;
            color: black !important;
        }

        /* Pastikan seluruh tabel muncul saat print */
        div {
            max-height: none !important;
            overflow: visible !important;
        }

        #action-sipd {
            display: none;
        }
    }
</style>
<div id="action-sipd"></div>
<div style="padding: 15px;">
    <h1 class="text_tengah" >LAPORAN SERAPAN RINCI<br>Bulan <?php echo $nama_bulan; ?> Tahun <?php echo $tahun_anggaran; ?></h1>
    <table id="tabel_detail_nota">
        <tbody>
            <tr>
                <td>Urusan</td>
                <td>:</td>
                <td><?php echo $kode_urusan . '  ' . $nama_urusan ?></td>
            </tr>
            <tr>
                <td>Bidang Urusan</td>
                <td>:</td>
                <td><?php echo $kode_bidang_urusan . '  ' . $nama_bidang_urusan ?></td>
            </tr>
            <tr>
                <td>Program</td>
                <td>:</td>
                <td><?php echo $kode_program . '  ' . $nama_program ?></td>
            </tr>
            <tr>
                <td>Kegiatan</td>
                <td>:</td>
                <td><?php echo $kode_kegiatan . '  ' . $nama_kegiatan ?></td>
            </tr>
            <tr>
                <td>Sub Kegiatan</td>
                <td>:</td>
                <td><?php echo $kode_sub_kegiatan . '  ' . str_replace($kode_sub_kegiatan, '', $nama_sub_kegiatan); ?></td>
            </tr>
            <tr>
                <td>Pagu Belanja</td>
                <td>:</td>
                <td>Rp <?php echo $pagu_sub_kegiatan; ?></td>
            </tr>
        </tbody>
    </table>
    <div style="max-height: 600px; overflow: auto;">
        <table id="table-sticky">
            <thead>
                <tr>
                    <th class="atas kanan bawah kiri text_tengah" style="vertical-align: middle;" width="120px" rowspan="2">Kode Rekening</th>
                    <th class="atas kanan bawah text_tengah" style="vertical-align: middle;" rowspan="2">Uraian</th>
                    <th class="atas kanan bawah text_tengah" style="vertical-align: middle;" width="140px" rowspan="2">Anggaran DPA</th>
                    <th class="atas kanan bawah text_tengah" width="100px" colspan="2">Volume</th>
                    <th class="atas kanan bawah text_tengah" width="700px" colspan="3">Periode Sampai Bulan <?php echo $nama_bulan; ?></th>
                    <th class="atas kanan bawah text_tengah" style="vertical-align: middle;" width="140px" rowspan="2">Sisa</th>
                </tr>
                <tr>
                    <th class="atas kanan bawah kiri text_tengah">Jumlah</th>
                    <th class="atas kanan bawah kiri text_tengah">Satuan</th>
                    <th class="atas kanan bawah kiri text_tengah">Nomor Bukti</th>
                    <th class="atas kanan bawah kiri text_tengah">Uraian</th>
                    <th class="atas kanan bawah kiri text_tengah">Realisasi</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $body; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="atas kanan bawah kiri text_tengah" colspan="2">Total</th>
                    <th class="atas kanan bawah text_kanan"><?php echo number_format($total_anggaran, 0, ",", "."); ?></th>
                    <th class="atas kanan bawah text_kanan" colspan="4"></th>
                    <th class="atas kanan bawah text_kanan"><?php echo number_format($total_realisasi, 0, ",", "."); ?></th>
                    <th class="atas kanan bawah text_kanan"><?php echo number_format($total_sisa, 0, ",", "."); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script>
    jQuery(document).ready(() => {
        var extend_action = '';
        extend_action += '<button class="btn btn-info m-2" id="print_laporan" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Cetak Laporan</button><br>';
        extend_action += '</div>';
        jQuery('#action-sipd').append(extend_action);
    });
</script>