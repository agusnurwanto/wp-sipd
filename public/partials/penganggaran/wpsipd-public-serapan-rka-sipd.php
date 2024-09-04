<?php
global $wpdb;
$input = shortcode_atts(array(
    'kode_sbl' => '',
    'tahun_anggaran' => '2021'
), $atts);

if (
    !empty($input['tahun_anggaran']) 
    && !empty($input['kode_sbl'])
){
	$tahun_anggaran = $input['tahun_anggaran'];
	$kode_sbl = $input['kode_sbl'];
} else {
	die('<h1 class="text-center">Tahun Anggaran dan Kode Sub Kegiatan tidak boleh kosong!</h1>');
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
    die('<h1 class="text-center">Sub Kegiatan tidak ditemukan!</h1>');
}

$set_bulan = date('m');
if(
    !empty($_GET) 
    && !empty($_GET['bulan'])
){
    $set_bulan = $_GET['bulan']*1;
}
$nama_bulan = $this->get_bulan($set_bulan);

if($set_bulan <= 9){
    $set_bulan = '0'.$set_bulan;
}
$bulan_terpilih = $input['tahun_anggaran'].'-'.$set_bulan.'-31 23:59:59';
$bulan_terpilih_2 = date('Y').'-'.$set_bulan.'-31 23:59:59';

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
    if(empty($item['kode_akun'])){
        continue;
    }
    if(empty($akun_all[$item['kode_akun']])){
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
        foreach($bku as $bukti){
            $akun_all[$item['kode_akun']]['realisasi'] += $bukti['pagu'];
            if(empty($akun_all[$item['kode_akun']]['bku'][$bukti['id_rinci_sub_bl']])){
                $akun_all[$item['kode_akun']]['bku'][$bukti['id_rinci_sub_bl']] = array();
            }
            $akun_all[$item['kode_akun']]['bku'][$bukti['id_rinci_sub_bl']][] = $bukti;
        }
    }
    $akun_all[$item['kode_akun']]['total'] += $item['total_harga'];
    $akun_all[$item['kode_akun']]['total_murni'] += $item['rincian_murni'];
    if(empty($akun_all[$item['kode_akun']]['rinci'][$item['id_rinci_sub_bl']])){
        $akun_all[$item['kode_akun']]['rinci'][$item['id_rinci_sub_bl']] = array(
            'val' => $item,
            'bukti' => array()
        );
    }
    if(!empty($akun_all[$item['kode_akun']]['bku'][$item['id_rinci_sub_bl']])){
        $akun_all[$item['kode_akun']]['rinci'][$item['id_rinci_sub_bl']]['bukti'] = $akun_all[$item['kode_akun']]['bku'][$item['id_rinci_sub_bl']];
        unset($akun_all[$item['kode_akun']]['bku'][$item['id_rinci_sub_bl']]);
    }
}

$body = '';
$total_anggaran = 0;
$total_sisa = 0;
$total_realisasi = 0;
foreach ($akun_all as $akun) {
    $sisa_akun = $akun['total']-$akun['realisasi'];
    $body .='
    <tr style="font-weight: 600;">
        <td class="text-center">'.$akun['kode_akun'].'</td>
        <td>'.$akun['nama_akun'].'</td>
        <td class="text-right">'.number_format($akun['total'],0,",",".").'</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right">'.number_format($akun['realisasi'],0,",",".").'</td>
        <td class="text-right">'.number_format($sisa_akun,0,",",".").'</td>
    </tr>';

    // tampilkan rincian RKA
    foreach ($akun['rinci'] as $key => $item) {
        $alamat_array = $this->get_alamat($input, $item['val']);
        if(!empty($alamat_array['keterangan'])){
            $keterangan_alamat[] = $alamat_array['keterangan'];
        }
        $alamat = $alamat_array['alamat'];
        $lokus_akun_teks = $alamat_array['lokus_akun_teks_decode'];

        // jika alamat kosong maka cek id penerima bantuan
        if(empty($alamat)){
            $alamat = array();
            if(!empty($item['val']['id_lurah_penerima'])){
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['val']['id_lurah_penerima']." and is_kel=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            if(!empty($item['val']['id_camat_penerima'])){
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['val']['id_camat_penerima']." and is_kec=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            if(!empty($item['val']['id_kokab_penerima'])){
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['val']['id_kokab_penerima']." and is_kab=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            if(!empty($item['val']['id_prop_penerima'])){
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['val']['id_prop_penerima']." and is_prov=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            $profile_penerima = implode(', ', $alamat);
        }else{

            // jika lokus akun teks ada di nama komponen
            if(
                strpos($item['val']['nama_komponen'], $lokus_akun_teks) !== false
                || $lokus_akun_teks == $alamat
            ){
                $profile_penerima = $alamat;
            }else{
                $profile_penerima = $lokus_akun_teks.', '.$alamat;
            }
        }

        // tampilkan bku yang sudah terkoneksi ke rincian belanja
        if(!empty($item['bukti'])){
            $realisasi = 0;
            foreach($item['bukti'] as $k => $bukti){
                $realisasi += $bukti['pagu'];
                if($k == 0){
                    $vol = 0;
                    if(!empty($bukti['koefisien'])){
                        $vol = explode(' ', $bukti['koefisien']);
                        $vol = $vol[0];
                    }
                    $body .='
                    <tr data-id="'.$item['val']['id_rinci_sub_bl'].'" data-id-bukti="'.$bukti['id'].'">
                        <td></td>
                        <td>
                            <div>'.$item['val']['nama_komponen'].'</div>
                            <div>'.$item['val']['spek_komponen'].'</div>
                            <div>'.$profile_penerima.'</div>
                        </td>
                        <td class="text-right">'.number_format($item['val']['total_harga'],0,",",".").'</td>
                        <td class="text-center">'.$vol.'</td>
                        <td class="text-center">'.$item['val']['satuan'].'</td>
                        <td>'.$bukti['nomor_bukti'].'</td>
                        <td>'.$bukti['uraian'].'</td>
                        <td class="text-right">'.number_format($bukti['pagu'],0,",",".").'</td>
                        <td class="text-right">'.number_format($item['val']['total_harga']-$realisasi,0,",",".").'</td>
                    </tr>
                    ';
                }else{
                    $body .='
                    <tr data-id="'.$item['val']['id_rinci_sub_bl'].'" data-id-bukti="'.$bukti['id'].'">
                        <td></td>
                        <td></td>
                        <td class="text-right"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td>'.$bukti['nomor_bukti'].'</td>
                        <td>'.$bukti['uraian'].'</td>
                        <td class="text-right">'.number_format($bukti['pagu'],0,",",".").'</td>
                        <td class="text-right">'.number_format($item['val']['total_harga']-$realisasi,0,",",".").'</td>
                    </tr>
                    ';
                }
                $total_realisasi += $bukti['pagu'];
            }

        // tampilkan rincian belanja yang belum ada BKU nya
        }else{
            $vol = 0;
            if(!empty($item['val']['koefisien'])){
                $vol = explode(' ', $item['val']['koefisien']);
                $vol = $vol[0];
            }
            $body .='
            <tr data-id="'.$item['val']['id_rinci_sub_bl'].'">
                <td></td>
                <td>
                    <div>'.$item['val']['nama_komponen'].'</div>
                    <div>'.$item['val']['spek_komponen'].'</div>
                    <div>'.$profile_penerima.'</div>
                </td>
                <td class="text-right">'.number_format($item['val']['total_harga'],0,",",".").'</td>
                <td class="text-center">'.$vol.'</td>
                <td class="text-center">'.$item['val']['satuan'].'</td>
                <td></td>
                <td></td>
                <td class="text-right">0</td>
                <td class="text-right">'.number_format($item['val']['total_harga'],0,",",".").'</td>
            </tr>
            ';
        }
        $total_anggaran += $item['val']['total_harga'];
    }

    // tampilkan bku yang belum terkoneksi ke rincian belanja
    foreach($akun['bku'] as $bukti_rinci){
        foreach($bukti_rinci as $bukti){
            $body .='
            <tr data-id-bukti="'.$bukti['id'].'">
                <td></td>
                <td class="bg-danger">Rincian bukti tidak terkoneksi ke RKA/DPA</td>
                <td class="text-right"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td>'.$bukti['nomor_bukti'].'</td>
                <td>'.$bukti['uraian'].'</td>
                <td class="text-right">'.number_format($bukti['pagu'],0,",",".").'</td>
                <td class="text-right">'.number_format(0-$bukti['pagu'],0,",",".").'</td>
            </tr>
            ';
            $total_realisasi += $bukti['pagu'];
        }
    }
}

$total_sisa = $total_anggaran-$total_realisasi;
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
	}
    .hide-link-decoration {
        text-decoration: none !important;
    }
    .warning_color {
        background-color: #f9d9d9;
    }
    #table_rincian thead th {
        vertical-align: middle;
    }
</style>
<div style="padding: 15px;">
    <h1 class="text-center" style="margin-top: 50px;">LAPORAN SERAPAN RINCI<br>Bulan <?php echo $nama_bulan; ?> Tahun <?php echo $tahun_anggaran; ?></h1>
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

    <table id="table_rincian" class="table table-bordered">
        <thead>
            <tr>
                <th class="atas kanan bawah text-center" width="120px" rowspan="2">Kode Rekening</th>
                <th class="atas kanan bawah text-center" rowspan="2">Uraian</th>
                <th class="atas kanan bawah text-center" width="140px" rowspan="2">Anggaran DPA</th>
                <th class="atas kanan bawah text-center" width="100px" colspan="2">Volume</th>
                <th class="atas kanan bawah text-center" width="700px" colspan="3">Periode Sampai Bulan <?php echo $nama_bulan; ?></th>
                <th class="atas kanan bawah text-center" width="140px" rowspan="2">Sisa</th>
            </tr>
            <tr>
                <th class="atas kanan bawah text-center">Jumlah</th>
                <th class="atas kanan bawah text-center">Satuan</th>
                <th class="atas kanan bawah text-center">Nomor Bukti</th>
                <th class="atas kanan bawah text-center">Uraian</th>
                <th class="atas kanan bawah text-center">Realisasi</th>
            </tr>
        </thead>
        <tbody><?php echo $body; ?></tbody>
        <tfoot>
            <tr>
                <th class="atas kanan bawah text-center" colspan="3">Total</th>
                <th class="atas kanan bawah text-right"><?php echo number_format($total_anggaran,0,",","."); ?></th>
                <th class="atas kanan bawah text-right" colspan="3"></th>
                <th class="atas kanan bawah text-right"><?php echo number_format($total_realisasi,0,",","."); ?></th>
                <th class="atas kanan bawah text-right"><?php echo number_format($total_sisa,0,",","."); ?></th>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
    // jQuery('#table_rincian').DataTable();
});
</script>