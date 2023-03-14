<?php
// If this file is called directly, abort.
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
global $wpdb;
$ssh = $wpdb->get_results($wpdb->prepare("
	SELECT
		h.*
	FROM data_ssh_usulan as h
	LEFT JOIN data_surat_usulan_ssh as s on s.nomor_surat=h.no_surat_usulan
		AND s.tahun_anggaran=h.tahun_anggaran
	WHERE s.id=%d
", $input['id_surat']), ARRAY_A);

$body_html = "";
foreach($ssh as $k => $val){
	$no = $k+1;
	$body_html .= "
	<tr>
		<td>$no</td>
		<td>$val[kode_kel_standar_harga]</td>
		<td>$val[nama_kel_standar_harga]</td>
		<td>$val[nama_standar_harga]</td>
		<td>$val[spek]</td>
		<td>$val[satuan]</td>
		<td>$val[harga]</td>
		<td></td>
		<td></td>
		<td>$val[ket_teks]</td>
	</tr>
	";
}
?>
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
			<th class="text-center">NAMA AKUN BELANJA</th>
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
			<th class="text-center">10</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $body_html; ?>
	</tbody>
</table>