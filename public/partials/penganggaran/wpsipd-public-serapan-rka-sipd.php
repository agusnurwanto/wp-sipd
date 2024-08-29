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

$bulan = date('m');
if(
    !empty($_GET) 
    && !empty($_GET['bulan'])
){
    $bulan = $_GET['bulan'];
}
$nama_bulan = $this->get_bulan($bulan);

$rinc = $wpdb->get_results($wpdb->prepare("
    SELECT 
        * 
    from data_rka 
    where kode_sbl=%s
        AND tahun_anggaran=%d
        AND active=1
    Order by kode_akun ASC, subs_bl_teks ASC, ket_bl_teks ASC, id_rinci_sub_bl ASC
", $kode_sbl, $tahun_anggaran), ARRAY_A);

$akun_all = array();
foreach ($rinc as $key => $item) {
    if(empty($item['kode_akun'])){
        continue;
    }
    if(empty($akun_all[$val['kode_akun']])){
        $nama_akun = str_replace($item['kode_akun'], '', $item['nama_akun']);
        $akun_all[$val['kode_akun']] = array(
            'total' => 0,
            'total_murni' => 0,
            'status' => 0,
            'kode_akun' => $item['kode_akun'],
            'nama_akun' => $nama_akun
        );
    }
    $akun_all[$val['kode_akun']]['total'] += $item['total_harga'];
    $akun_all[$val['kode_akun']]['total_murni'] += $item['rincian_murni'];
}
$body = '';
foreach ($rinc as $key => $item) {
    if(empty($item['kode_akun'])){
        continue;
    }
    $alamat_array = $this->get_alamat($input, $item);
    if(!empty($alamat_array['keterangan'])){
        $keterangan_alamat[] = $alamat_array['keterangan'];
    }
    $alamat = $alamat_array['alamat'];
    $lokus_akun_teks = $alamat_array['lokus_akun_teks_decode'];

    // jika alamat kosong maka cek id penerima bantuan
    if(empty($alamat)){
        $alamat = array();
        if(!empty($item['id_lurah_penerima'])){
            $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['id_lurah_penerima']." and is_kel=1", ARRAY_A);
            $alamat[] = $db_alamat['nama'];
        }
        if(!empty($item['id_camat_penerima'])){
            $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['id_camat_penerima']." and is_kec=1", ARRAY_A);
            $alamat[] = $db_alamat['nama'];
        }
        if(!empty($item['id_kokab_penerima'])){
            $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['id_kokab_penerima']." and is_kab=1", ARRAY_A);
            $alamat[] = $db_alamat['nama'];
        }
        if(!empty($item['id_prop_penerima'])){
            $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['id_prop_penerima']." and is_prov=1", ARRAY_A);
            $alamat[] = $db_alamat['nama'];
        }
        $profile_penerima = implode(', ', $alamat);
    }else{

        // jika lokus akun teks ada di nama komponen
        if(
            strpos($item['nama_komponen'], $lokus_akun_teks) !== false
            || $lokus_akun_teks == $alamat
        ){
            $profile_penerima = $alamat;
        }else{
            $profile_penerima = $lokus_akun_teks.', '.$alamat;
        }
    }
    if($akun_all[$val['kode_akun']]['status'] == 0){
        $body .='
        <tr>
            <td>'.$akun_all[$val['kode_akun']]['kode_akun'].'</td>
            <td>'.$akun_all[$val['kode_akun']]['nama_akun'].'</td>
        </tr>';
    }
    $body .='
    <tr>
        <td></td>
    </tr>
    ';
}
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
    <h1 class="text-center" style="margin-top: 50px;">LAPORAN SERAPAN RINCI<br>Bulan <?php echo $nama_bulan; ?></h1>
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
                <th class="atas kanan bawah text-center" width="150px" rowspan="2">Kode Rekening</th>
                <th class="atas kanan bawah text-center" rowspan="2">Uraian</th>
                <th class="atas kanan bawah text-center" width="150px" rowspan="2">Anggaran DPA</th>
                <th class="atas kanan bawah text-center" width="150px" rowspan="2">Saldo</th>
                <th class="atas kanan bawah text-center" width="150px" colspan="2">Volume</th>
                <th class="atas kanan bawah text-center" width="380px" colspan="3">Periode Bulan <?php echo $nama_bulan; ?></th>
                <th class="atas kanan bawah text-center" width="150px" rowspan="2">Realisasi</th>
                <th class="atas kanan bawah text-center" width="150px" rowspan="2">Sisa</th>
            </tr>
            <tr>
                <th class="atas kanan bawah text-center">Jumlah</th>
                <th class="atas kanan bawah text-center">Satuan</th>
                <th class="atas kanan bawah text-center">Nomor Bukti</th>
                <th class="atas kanan bawah text-center">Uraian</th>
                <th class="atas kanan bawah text-center">Rp.</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('#table_rincian').DataTable();
});
</script>