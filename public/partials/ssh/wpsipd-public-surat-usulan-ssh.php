<?php
// If this file is called directly, abort.

require_once WPSIPD_PLUGIN_PATH."/public/trait/CustomTrait.php";

global $wpdb;

if ( ! defined( 'WPINC' ) ) {
	die;
}

$input = shortcode_atts( array(
	'id_surat' => '0',
	'tahun_anggaran' => '2022'
), $atts );

if(empty($input['id_surat'])){
	die('<h1>ID Surat tidak boleh kosong!</h1>');
}

if(empty($_GET['idskpd'])){
	die('<h1>Unit kerja tidak boleh kosong!</h1>');
}

$skpd = $wpdb->get_row($wpdb->prepare("
					SELECT
						namaunit, namakepala, nipkepala, pangkatkepala 
					FROM data_unit
					WHERE id_skpd=%d
			", $_GET['idskpd']));

if(empty($skpd)){
	die('<h1>Unit kerja tidak ditemukan!</h1>');
}

$ssh = $wpdb->get_results($wpdb->prepare("
	SELECT
		h.*
	FROM data_ssh_usulan as h
	LEFT JOIN data_surat_usulan_ssh as s on s.nomor_surat=h.no_surat_usulan
		AND s.tahun_anggaran=h.tahun_anggaran
	WHERE s.id=%d
", $input['id_surat']), ARRAY_A);

$type='';
$sambung='';
if(!empty($_GET['type'])){
	$kondisi=explode(",", $_GET['type']);
	if(count($kondisi)>1){
		$sambung=' dan/atau ';
	}
	foreach ($kondisi as $key => $value) {
		if($value==1){
			$type.='survey harga pasar yang telah kami lakukan secara mandiri'.$sambung;
		}
		if($value==2){
			$type.='Petunjuk Teknis yang kami terima dari ....... (kementrian/provinsi)';
		}
	}
}

$body_html = "";

if(empty($ssh)){
	die('<h1>ID Surat '.$input['id_surat'].' tidak ditemukan!</h1>');
}

foreach($ssh as $k => $val){
	$no = $k+1;
	$akun_belanja = "";
	$akun_db = $wpdb->get_results("
		SELECT
			kode_akun,
			nama_akun
		FROM data_ssh_rek_belanja_usulan
		WHERE id_standar_harga=$val[id_standar_harga]
	", ARRAY_A);
	foreach($akun_db as $kk => $akun){
		$no2 = $kk+1;
		$akun_belanja .= "
			<tr>
				<td>$no2</td>
				<td>$akun[kode_akun]</td>
				<td>$akun[nama_akun]</td>
			</tr>
		";
	}
	$body_html .= "
	<tr>
		<td>$no</td>
		<td>$val[kode_kel_standar_harga]</td>
		<td>$val[nama_kel_standar_harga]</td>
		<td>$val[nama_standar_harga]</td>
		<td>$val[spek]</td>
		<td>$val[satuan]</td>
		<td>$val[harga]</td>
		<td style='padding: 4px;'>
			<table class='table table-bordered' style='margin: 0;'>
				<thead>
					<tr>
						<th class='text-center'>No</th>
						<th class='text-center'>Kode Akun</th>
						<th class='text-center'>Uraian</th>
					</tr>
				</thead>
				<tbody>
					$akun_belanja
				</tbody>
			</table>
		</td>
		<td>$val[ket_teks]</td>
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
</style>
<div class="mt-5 mb-5 tengah"><button onclick="Export2Word('content', 'surat-usulan-ssh')">Download Surat</button></div>
<div class="cetak">
	<div style="padding: 10px;">
		<div class="surat-usulan break-print" id="content">
			<div class="kop-surat row">
				<div class="col-3 tengah">
					<img src="https://th.bing.com/th?id=OIP.TuxWGHNl8aeDTgzN1QOs8wHaIh&w=233&h=268&c=8&rs=1&qlt=90&o=6&dpr=1.3&pid=3.1&rm=2" width="50%">
				</div>
				<div class="col-md-9">
					<h4 class="tengah">PEMERINTAH KABUPATEN MAGETAN</h4>
					<h2 class="tengah"><?php echo $skpd->namaunit ?></h2>
				</div>
			</div>
			<div class="no-surat text-center row">
				<div class="col-md-12">
					<hr style="border: 1px solid;">
					<b>SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK</b>
					<p>Nomor: <?php echo $ssh[0]['no_surat_usulan']; ?></p>
				</div>
			</div>
			<div class="body-surat row">
				<div class="col-md-12">
					<p>Yang bertanda tangan di bawah ini saya selaku Kepala <?php echo $skpd->namaunit ?> Kabupaten Magetan menyatakan dengan sesungguhnya bertanggung jawab penuh atas usulan Standar Harga Satuan yang terlampir pada surat kami kepada Kepala BPPKAD Kab. Magetan tanggal 5 Oktober 2022, nomor : <?php echo $ssh[0]['no_surat_usulan']; ?>, <?php echo $type; ?>.</p>
					<p>Kami siap menyajikan data referensi harga barang/jasa atas Standar Harga Satuan yang kami usulkan jika sewaktu-waktu dibutuhkan.</p>
					<p>Demikian Surat Pernyataan ini dibuat dengan sebenar-benarnya.</p>
				</div>
			</div>
			<div class="ttd-surat row">
				<div class="col-md-6"></div>
				<div class="col-md-6 text-center">
					<p>Magetan, <?php echo $this->tanggalan(date('Y-m-d')); ?><br>Kepala <?php echo $skpd->namaunit ?><br>Kabupaten Magetan</p><br><br><br><br><p><?php echo $skpd->namakepala ?><br><?php echo $skpd->pangkatkepala ?><br>NIP : <?php echo $skpd->nipkepala ?></p>
				</div>
			</div>
		</div>
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
					<th class="text-center">SUMBER DANA / KETERANGAN</th>
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
	function Export2Word(element, filename = ''){
    var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title></head><body>";
    var postHtml = "</body></html>";
    var html = preHtml+document.getElementById(element).innerHTML+postHtml;

    var blob = new Blob(['\ufeff', html], {
        type: 'application/msword'
    });
    
    // Specify link url
    var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
    
    // Specify file name
    filename = filename?filename+'.doc':'document.doc';
    
    // Create download link element
    var downloadLink = document.createElement("a");

    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob ){
        navigator.msSaveOrOpenBlob(blob, filename);
    }else{
        // Create a link to the file
        downloadLink.href = url;
        
        // Setting the file name
        downloadLink.download = filename;
        
        //triggering the function
        downloadLink.click();
    }
    
    document.body.removeChild(downloadLink);
}
</script>