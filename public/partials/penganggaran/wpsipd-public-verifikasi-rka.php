<?php
if(!empty($_GET) && !empty($_GET['tahun']) && !empty($_GET['kode_sbl'])){
	$tahun_anggaran = $_GET['tahun'];
	$kode_sbl = $_GET['kode_sbl'];
}else{
	die('<h1 class="text-center">Tahun Anggaran dan Kode Sub Kegiatan tidak boleh kosong!</h1>');
}
?>
<h1 class="text-center">Verifikasi Rincian Belanja</h1>