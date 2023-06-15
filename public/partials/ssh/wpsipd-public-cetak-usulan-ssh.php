<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;

if(empty($_GET['tahun'])){
	die('<h1>Tahun Anggaran tidak boleh kosong!</h1>');
}else if(empty($_GET['status'])){
	die('<h1>Status tidak boleh kosong!</h1>');
}

$where = '';

$tahun_anggaran = $wpdb->prepare('%d', $_GET['tahun']);

$status = $_GET['status'];
if($status == 'diterima'){
	$where .=" AND status = 'approved' ";
}else if($status == 'ditolak'){
	$where .=" AND status = 'rejected' ";
}else if($status == 'menunggu'){
	$where .=" AND status = 'waiting' ";
}else if($status == 'sudah_upload_sipd'){
	$where .=" AND status_upload_sipd = '1' ";
}else if($status == 'belum_upload_sipd'){
	$where .=" AND status_upload_sipd != '1' OR status_upload_sipd IS NULL ";
}else if($status == 'diterima_admin'){
	$where .=" AND status_by_admin = 'approved' ";
}else if($status == 'ditolak_admin'){
	$where .=" AND status_by_admin = 'rejected' ";
}else if($status == 'diterima_tapdkeu'){
	$where .=" AND status_by_tapdkeu = 'approved' ";
}else if($status == 'ditolak_tapdkeu'){
	$where .=" AND status_by_tapdkeu = 'rejected' ";
}

if(!empty($_GET['id_skpd'])){
	$id_skpd = $wpdb->prepare('%d', $_GET['id_skpd']);
	$where .=" AND id_sub_skpd = ".$id_skpd;
}
if(!empty($_GET['no_surat'])){
	$no_surat = $wpdb->prepare('%s', $_GET['no_surat']);
	$where .=" AND no_surat_usulan = ".$no_surat;
}
if(!empty($_GET['nota_dinas'])){
	$nota_dinas = $wpdb->prepare('%s', $_GET['nota_dinas']);
	$where .=" AND no_nota_dinas = ".$nota_dinas;
}

$ssh = $wpdb->get_results($wpdb->prepare("
	SELECT
		h.*
	FROM data_ssh_usulan as h
	WHERE h.tahun_anggaran=%d
		$where
", $tahun_anggaran), ARRAY_A);

$body_html = "";

if(empty($ssh)){
	echo "<span style='display: none'>$wpdb->last_query</span>";
	$body_html .= "
	<tr>
		<td colspan='9' class='text-center'>Data tidak ditemukan!</td>
	</tr>
	";
}

foreach($ssh as $k => $val){
	$no = $k+1;
	$akun_belanja = "<ul style='margin-bottom: 0; margin-left: 20px;'>";
	$akun_db = $wpdb->get_results("
		SELECT
			kode_akun,
			nama_akun
		FROM data_ssh_rek_belanja_usulan
		WHERE id_standar_harga=$val[id]
	", ARRAY_A);
	foreach($akun_db as $kk => $akun){
		$akun_belanja .= "<li>$akun[nama_akun]</li>";
	}
	$akun_belanja .="</ul>";
	$body_html .= "
	<tr>
		<td>$no</td>
		<td>$val[kode_kel_standar_harga]</td>
		<td>$val[nama_kel_standar_harga]</td>
		<td>$val[nama_standar_harga]</td>
		<td>$val[spek]</td>
		<td>$val[satuan]</td>
		<td>$val[harga]</td>
		<td>$akun_belanja</td>
		<td>$val[keterangan_lampiran]</td>
	</tr>
	";
}
?>
<style type="text/css">
	@media print {
		.break-print {
	  		page-break-after: always;
	  	}
	}
	.surat-usulan {
		width: 900px;
		margin: 0 auto 30px;
		font-size: 20px;
	}
	.tengah{
		text-align: center;
	}
	.jarak-atas{
		margin-top: -20px;
	}
	.alamat{
		text-align: center;
		font-size: 19px;
	}
</style>
<div id="cetak">
	<div style="padding: 10px;">
		<h1 class="text-center">Daftar Usulan Standar Harga</h1>
		<table id="surat_usulan_ssh_table" class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">NO</th>
					<th class="text-center">KODE KELOMPOK BARANG</th>
					<th class="text-center">NAMA KODE KELOMPOK BARANG</th>
					<th class="text-center">URAIAN</th>
					<th class="text-center">SPESIFIKASI</th>
					<th class="text-center">SATUAN</th>
					<th class="text-center">HARGA SATUAN</th>
					<th class="text-center">AKUN BELANJA</th>
					<th class="text-center">KETERANGAN</th>
				</tr>
				<tr>
					<th class="text-center">1</th>
					<th class="text-center">2</th>
					<th class="text-center">3</th>
					<th class="text-center">4</th>
					<th class="text-center">5</th>
					<th class="text-center">6</th>
					<th class="text-center">7</th>
					<th class="text-center">8</th>
					<th class="text-center">9</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $body_html; ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
        run_download_excel();
    });
</script>