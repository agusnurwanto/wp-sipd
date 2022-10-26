<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => get_option('_crb_tahun_anggaran_sipd')
), $atts );

function button_edit_monev($class=false){
	$ret = ' <span style="display: none;" data-id="'.$class.'" class="edit-monev"><i class="dashicons dashicons-edit"></i></span>';
	return $ret;
}

function get_target($target, $satuan){
	if(empty($satuan)){
		return $target;
	}else{
		$target = explode($satuan, $target);
		return $target[0];
	}
}

function parsing_nama_kode($nama_kode){
	$nama_kodes = explode('||', $nama_kode);
	$nama = $nama_kodes[0];
	unset($nama_kodes[0]);
	return $nama.'<span class="debug-kode">||'.implode('||', $nama_kodes).'</span>';
}

$api_key = get_option('_crb_api_key_extension' );
$tahun_anggaran = $input['tahun_anggaran'];

$awal_renstra = 0;
$namaJadwal = '-';
$mulaiJadwal = '-';
$selesaiJadwal = '-';

$jadwal_lokal = $wpdb->get_results("SELECT * from data_jadwal_lokal where id_jadwal_lokal = (select max(id_jadwal_lokal) from data_jadwal_lokal where id_tipe=2)", ARRAY_A);
if(!empty($jadwal_lokal)){
	$awal_renstra = $jadwal_lokal[0]['tahun_anggaran'];
	$namaJadwal = $jadwal_lokal[0]['nama'];
	$mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
	$selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];
}

$akhir_renstra = $awal_renstra+5;
$urut = $tahun_anggaran-$awal_renstra;

$timezone = get_option('timezone_string');

$rumus_indikator_db = $wpdb->get_results("SELECT * from data_rumus_indikator where active=1 and tahun_anggaran=".$tahun_anggaran, ARRAY_A);
$rumus_indikator = '';
foreach ($rumus_indikator_db as $k => $v){
	$rumus_indikator .= '<option value="'.$v['id'].'">'.$v['rumus'].'</option>';
}

$where_skpd = '';
if(!empty($input['id_skpd'])){
	$where_skpd = "and id_skpd =".$input['id_skpd'];
}

$sql = $wpdb->prepare("
	select 
		* 
	from data_unit 
	where tahun_anggaran=%d
		".$where_skpd."
		and active=1
	order by id_skpd ASC
", $tahun_anggaran);

$unit = $wpdb->get_results($sql, ARRAY_A);

$judul_skpd = '';
if(!empty($input['id_skpd'])){
	$judul_skpd = $unit[0]['kode_skpd'].'&nbsp;'.$unit[0]['nama_skpd'].'<br>';
}
$nama_pemda = get_option('_crb_daerah');

$current_user = wp_get_current_user();

$body = '';
$bulan = date('m');
$data_all = array(
	'data' => array()
);

$tujuan_ids = array();
$sasaran_ids = array();
$program_ids = array();
$kegiatan_ids = array();
$skpd_filter = array();
$nama_pemda = get_option('_crb_daerah');

ksort($skpd_filter);
$skpd_filter_html = '<option value="">Pilih SKPD</option>';
foreach ($skpd_filter as $kode_skpd => $nama_skpd) {
	$skpd_filter_html .= '<option value="'.$kode_skpd.'">'.$kode_skpd.' '.$nama_skpd.'</option>';
}

?>
<style type="text/css">
	.debug-tujuan, .debug-sasaran, .debug-program, .debug-kegiatan, .debug-kode { display: none; }
	.indikator_program { min-height: 40px; }
	.indikator_kegiatan { min-height: 40px; }
	.modal {overflow-y:auto;}
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitoring dan Evaluasi RENSTRA (Rencana Strategis) <br><?php echo $judul_skpd.'Tahun '.$awal_renstra.' - '.$akhir_renstra.' '.$nama_pemda; ?></h4>
<div id="cetak" title="Laporan MONEV RENSTRA" style="padding: 5px; overflow: auto; height: 80vh;">
	<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<tr>
				<th style="width: 85px;" class="atas kiri kanan bawah text_tengah text_blok">No</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Bidang Urusan</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Tujuan</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Sasaran</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Program</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Kegiatan</th>
				<th style="width: 400px;" class="atas kanan bawah text_tengah text_blok">Indikator</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Awal</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Tahun 1</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Tahun 2</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Tahun 3</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Tahun 4</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Tahun 5</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Akhir</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Satuan</th>
				<th style="width: 150px;" class="atas kanan bawah text_tengah text_blok">Keterangan</th>
			</tr>
			<tr>
				<th class='atas kiri kanan bawah text_tengah text_blok'>0</th>
				<th class='atas kanan bawah text_tengah text_blok'>1</th>
				<th class='atas kanan bawah text_tengah text_blok'>2</th>
				<th class='atas kanan bawah text_tengah text_blok'>3</th>
				<th class='atas kanan bawah text_tengah text_blok'>4</th>
				<th class='atas kanan bawah text_tengah text_blok'>5</th>
				<th class='atas kanan bawah text_tengah text_blok'>6</th>
				<th class='atas kanan bawah text_tengah text_blok'>7</th>
				<th class='atas kanan bawah text_tengah text_blok'>8</th>
				<th class='atas kanan bawah text_tengah text_blok'>9</th>
				<th class='atas kanan bawah text_tengah text_blok'>10</th>
				<th class='atas kanan bawah text_tengah text_blok'>11</th>
				<th class='atas kanan bawah text_tengah text_blok'>12</th>
				<th class='atas kanan bawah text_tengah text_blok'>13</th>
				<th class='atas kanan bawah text_tengah text_blok'>14</th>
				<th class='atas kanan bawah text_tengah text_blok'>15</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
	</table>
</div>

<script type="text/javascript">
	run_download_excel();
	let data_all = <?php echo json_encode($data_all); ?>;

	var mySpace = '<div style="padding:3rem;"></div>';
	
	jQuery('body').prepend(mySpace);

	var dataHitungMundur = {
		'namaJadwal' : '<?php echo ucwords($namaJadwal)  ?>',
		'mulaiJadwal' : '<?php echo $mulaiJadwal  ?>',
		'selesaiJadwal' : '<?php echo $selesaiJadwal  ?>',
		'thisTimeZone' : '<?php echo $timezone ?>'
	}

	penjadwalanHitungMundur(dataHitungMundur);

	var aksi = ''
		+'<a style="margin-left: 10px;" id="singkron-sipd" onclick="return false;" href="#" class="btn btn-danger">Ambil data dari SIPD lokal</a>'
		+'<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RENSTRA</a>'
		+'<h3 style="margin-top: 20px;">SETTING</h3>'
		+'<label><input type="checkbox" onclick="tampilkan_edit(this);"> Edit Data RENSTRA</label>'
		+'<label style="margin-left: 20px;"><input type="checkbox" onclick="show_debug(this);"> Debug Cascading RPJM</label>'
		+'<label style="margin-left: 20px;">'
			+'Sembunyikan Baris '
			+'<select id="sembunyikan-baris" onchange="sembunyikan_baris(this);" style="padding: 5px 10px; min-width: 200px;">'
				+'<option value="">Pilih Baris</option>'
				+'<option value="tr-sasaran">Sasaran</option>'
				+'<option value="tr-program">Program</option>'
				+'<option value="tr-kegiatan">Misi</option>'
			+'</select>'
		+'</label>'
		+'<label style="margin-left: 20px;">'
			+'Filter SKPD '
			+'<select onchange="filter_skpd(this);" style="padding: 5px 10px; min-width: 200px; max-width: 400px;">'
				+'<?php echo $skpd_filter_html; ?>'
			+'</select>'
		+'</label>';
	jQuery('#action-sipd').append(aksi);
</script>