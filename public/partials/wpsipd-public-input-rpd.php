<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;

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

$jadwal_lokal = $wpdb->get_results("SELECT * from data_jadwal_lokal where id_jadwal_lokal = (select max(id_jadwal_lokal) from data_jadwal_lokal where id_tipe=3)", ARRAY_A);
if(!empty($jadwal_lokal)){
	$tahun_anggaran = $jadwal_lokal[0]['tahun_anggaran'];
	$namaJadwal = $jadwal_lokal[0]['nama'];
	$mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
	$selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];
}else{
	$tahun_anggaran = '2022';
	$namaJadwal = '-';
	$mulaiJadwal = '-';
	$selesaiJadwal = '-';
}

$timezone = get_option('timezone_string');

$awal_rpd = 2018;
$akhir_rpd = $awal_rpd+5;
$nama_pemda = get_option('_crb_daerah');

$current_user = wp_get_current_user();
$bulan = date('m');
$body_monev = '';

$data_all = array(
	'data' => array()
);
$bulan = date('m');

$tujuan_ids = array();
$sasaran_ids = array();
$program_ids = array();
$skpd_filter = array();

$sql = "
	select 
		* 
	from data_rpd_tujuan_lokal
";
$tujuan_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($tujuan_all as $tujuan) {
	if(empty($data_all['data'][$tujuan['id_unik']])){
		$data_all['data'][$tujuan['id_unik']] = array(
			'nama' => $tujuan['tujuan_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data'][$tujuan['id_unik']]['detail'][] = $tujuan;

	$tujuan_ids[$tujuan['id_unik']] = "'".$tujuan['id_unik']."'";
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpd_sasaran_lokal
		where kode_tujuan=%s
	", $tujuan['id_unik']);
	$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($sasaran_all as $sasaran) {
		if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
			$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
				'nama' => $sasaran['sasaran_teks'],
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;

		$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpd_program_lokal
			where kode_sasaran=%s
		", $sasaran['id_unik']);
		$program_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($program_all as $program) {
			$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
			if(empty($program['kode_skpd'])){
				$program['kode_skpd'] = '00';
				$program['nama_skpd'] = 'SKPD Kosong';
			}
			$skpd_filter[$program['kode_skpd']] = $program['nama_skpd'];
			if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
					'nama' => $program['nama_program'],
					'kode_skpd' => $program['kode_skpd'],
					'nama_skpd' => $program['nama_skpd'],
					'data' => array()
				);
			}
			if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
					'nama' => $program['indikator'],
					'data' => $program
				);
			}
		}
	}
}

// buat array data kosong
if(empty($data_all['data']['tujuan_kosong'])){
	$data_all['data']['tujuan_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'detail' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong'])){
	$data_all['data']['tujuan_kosong']['data']['sasaran_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'detail' => array(),
		'data' => array()
	);
}

// select tujuan yang belum terselect
if(!empty($tujuan_ids)){
	$sql = "
		select 
			* 
		from data_rpd_tujuan_lokal
		where id_unik not in (".implode(',', $tujuan_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpd_tujuan_lokal
	";
}
$tujuan_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($tujuan_all_kosong as $tujuan) {
	if(empty($data_all['data'][$tujuan['id_unik']])){
		$data_all['data'][$tujuan['id_unik']] = array(
			'nama' => $tujuan['tujuan_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data'][$tujuan['id_unik']]['detail'][] = $tujuan;
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpd_sasaran_lokal
		where kode_tujuan=%s
	", $tujuan['id_unik']);
	$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($sasaran_all as $sasaran) {
		$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
		if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
			$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
				'nama' => $sasaran['sasaran_teks'],
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpd_program_lokal
			where kode_sasaran=%s
		", $sasaran['id_unik']);
		$program_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($program_all as $program) {
			$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
			if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
					'nama' => $program['nama_program'],
					'kode_skpd' => $program['kode_skpd'],
					'nama_skpd' => $program['nama_skpd'],
					'data' => array()
				);
			}
			if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
					'nama' => $program['indikator'],
					'data' => array()
				);
			}
		}
	}
}

// select sasaran yang belum terselect
if(!empty($sasaran_ids)){
	$sql = "
		select 
			* 
		from data_rpd_sasaran_lokal
		where id_unik not in (".implode(',', $sasaran_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpd_sasaran_lokal
	";
}
$sasaran_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($sasaran_all_kosong as $sasaran) {
	if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']])){
		$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']] = array(
			'nama' => $sasaran['sasaran_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpd_program_lokal
		where kode_sasaran=%s
	", $sasaran['id_unik']);
	$program_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($program_all as $program) {
		$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
		if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
				'nama' => $program['nama_program'],
				'kode_skpd' => $program['kode_skpd'],
				'nama_skpd' => $program['nama_skpd'],
				'data' => array()
			);
		}
		if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
				'nama' => $program['indikator'],
				'data' => array()
			);
		}
	}
}

// select program yang belum terselect
if(!empty($program_ids)){
	$sql = "
		select 
			* 
		from data_rpd_program_lokal
		where id_unik not in (".implode(',', $program_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpd_program_lokal
	";
}
$program_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($program_all as $program) {
	if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']])){
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']] = array(
			'nama' => $program['nama_program'],
			'kode_skpd' => $program['kode_skpd'],
			'nama_skpd' => $program['nama_skpd'],
			'data' => array()
		);
	}
	if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
			'nama' => $program['indikator'],
			'data' => array()
		);
	}
}

// hapus array jika data dengan key kosong tidak ada datanya
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'])){
	unset($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']);
}
if(empty($data_all['data']['tujuan_kosong']['data'])){
	unset($data_all['data']['tujuan_kosong']);
}

$body = '';
$no_tujuan = 0;
foreach ($data_all['data'] as $tujuan) {
	$no_tujuan++;
	$indikator_tujuan = '';
	$target_awal = '';
	$target_1 = '';
	$target_2 = '';
	$target_3 = '';
	$target_4 = '';
	$target_5 = '';
	$target_akhir = '';
	$satuan = '';
	foreach($tujuan['detail'] as $k => $v){
		if(!empty($v['indikator_teks'])){
			$indikator_tujuan .= '<div class="indikator_program">'.$v['indikator_teks'].'</div>';
			$target_awal .= '<div class="indikator_program">'.$v['target_awal'].'</div>';
			$target_1 .= '<div class="indikator_program">'.$v['target_1'].'</div>';
			$target_2 .= '<div class="indikator_program">'.$v['target_2'].'</div>';
			$target_3 .= '<div class="indikator_program">'.$v['target_3'].'</div>';
			$target_4 .= '<div class="indikator_program">'.$v['target_4'].'</div>';
			$target_5 .= '<div class="indikator_program">'.$v['target_5'].'</div>';
			$target_akhir .= '<div class="indikator_program">'.$v['target_akhir'].'</div>';
			$satuan .= '<div class="indikator_program">'.$v['satuan'].'</div>';
		}
	}
	$body .= '
		<tr class="tr-tujuan">
			<td class="kiri atas kanan bawah">'.$no_tujuan.'</td>
			<td class="atas kanan bawah">'.parsing_nama_kode($tujuan['nama']).'</td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah">'.$indikator_tujuan.'</td>
			<td class="atas kanan bawah">'.$target_awal.'</td>
			<td class="atas kanan bawah">'.$target_1.'</td>
			<td class="atas kanan bawah">'.$target_2.'</td>
			<td class="atas kanan bawah">'.$target_3.'</td>
			<td class="atas kanan bawah">'.$target_4.'</td>
			<td class="atas kanan bawah">'.$target_5.'</td>
			<td class="atas kanan bawah">'.$target_akhir.'</td>
			<td class="atas kanan bawah">'.$satuan.'</td>
			<td class="atas kanan bawah"></td>
		</tr>
	';
	$no_sasaran = 0;
	foreach ($tujuan['data'] as $sasaran) {
		$no_sasaran++;
		$indikator_sasaran = '';
		$target_awal = '';
		$target_1 = '';
		$target_2 = '';
		$target_3 = '';
		$target_4 = '';
		$target_5 = '';
		$target_akhir = '';
		$satuan = '';
		foreach($sasaran['detail'] as $k => $v){
			if(!empty($v['indikator_teks'])){
				$indikator_sasaran .= '<div class="indikator_program">'.$v['indikator_teks'].'</div>';
				$target_awal .= '<div class="indikator_program">'.$v['target_awal'].'</div>';
				$target_1 .= '<div class="indikator_program">'.$v['target_1'].'</div>';
				$target_2 .= '<div class="indikator_program">'.$v['target_2'].'</div>';
				$target_3 .= '<div class="indikator_program">'.$v['target_3'].'</div>';
				$target_4 .= '<div class="indikator_program">'.$v['target_4'].'</div>';
				$target_5 .= '<div class="indikator_program">'.$v['target_5'].'</div>';
				$target_akhir .= '<div class="indikator_program">'.$v['target_akhir'].'</div>';
				$satuan .= '<div class="indikator_program">'.$v['satuan'].'</div>';
			}
		}
		$body .= '
			<tr class="tr-sasaran">
				<td class="kiri atas kanan bawah">'.$no_tujuan.'.'.$no_sasaran.'</td>
				<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['nama'].'</span></td>
				<td class="atas kanan bawah">'.parsing_nama_kode($sasaran['nama']).'</td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah">'.$indikator_sasaran.'</td>
				<td class="atas kanan bawah">'.$target_awal.'</td>
				<td class="atas kanan bawah">'.$target_1.'</td>
				<td class="atas kanan bawah">'.$target_2.'</td>
				<td class="atas kanan bawah">'.$target_3.'</td>
				<td class="atas kanan bawah">'.$target_4.'</td>
				<td class="atas kanan bawah">'.$target_5.'</td>
				<td class="atas kanan bawah">'.$target_akhir.'</td>
				<td class="atas kanan bawah">'.$satuan.'</td>
				<td class="atas kanan bawah"></td>
			</tr>
		';
		$no_program = 0;
		foreach ($sasaran['data'] as $program) {
			$no_program++;
			$text_indikator = array();
			$target_awal = array();
			$target_1 = array();
			$target_2 = array();
			$target_3 = array();
			$target_4 = array();
			$target_5 = array();
			$target_akhir = array();
			$satuan = array();
			foreach ($program['data'] as $indikator_program) {
				$text_indikator[] = '<div class="indikator_program">'.$indikator_program['nama'].'</div>';
				$target_awal[] = '<div class="indikator_program">'.get_target($indikator_program['data']['target_awal'], $indikator_program['data']['satuan']).'</div>';
				$target_1[] = '<div class="indikator_program">'.get_target($indikator_program['data']['target_1'], $indikator_program['data']['satuan']).'</div>';
				$target_2[] = '<div class="indikator_program">'.get_target($indikator_program['data']['target_2'], $indikator_program['data']['satuan']).'</div>';
				$target_3[] = '<div class="indikator_program">'.get_target($indikator_program['data']['target_3'], $indikator_program['data']['satuan']).'</div>';
				$target_4[] = '<div class="indikator_program">'.get_target($indikator_program['data']['target_4'], $indikator_program['data']['satuan']).'</div>';
				$target_5[] = '<div class="indikator_program">'.get_target($indikator_program['data']['target_5'], $indikator_program['data']['satuan']).'</div>';
				$target_akhir[] = '<div class="indikator_program">'.get_target($indikator_program['data']['target_akhir'], $indikator_program['data']['satuan']).'</div>';
				$satuan[] = '<div class="indikator_program">'.$indikator_program['data']['satuan'].'</div>';
			}
			$text_indikator = implode('', $text_indikator);
			$target_awal = implode('', $target_awal);
			$target_1 = implode('', $target_1);
			$target_2 = implode('', $target_2);
			$target_3 = implode('', $target_3);
			$target_4 = implode('', $target_4);
			$target_5 = implode('', $target_5);
			$target_akhir = implode('', $target_akhir);
			$satuan = implode('', $satuan);
			$body .= '
				<tr class="tr-program" data-kode-skpd="'.$program['kode_skpd'].'">
					<td class="kiri atas kanan bawah">'.$no_tujuan.'.'.$no_sasaran.'.'.$no_program.'</td>
					<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['nama'].'</span></td>
					<td class="atas kanan bawah"><span class="debug-sasaran">'.$sasaran['nama'].'</span></td>
					<td class="atas kanan bawah">'.parsing_nama_kode($program['nama']).'</td>
					<td class="atas kanan bawah">'.$text_indikator.'</td>
					<td class="atas kanan bawah text_tengah">'.$target_awal.'</td>
					<td class="atas kanan bawah text_tengah">'.$target_1.'</td>
					<td class="atas kanan bawah text_tengah">'.$target_2.'</td>
					<td class="atas kanan bawah text_tengah">'.$target_3.'</td>
					<td class="atas kanan bawah text_tengah">'.$target_4.'</td>
					<td class="atas kanan bawah text_tengah">'.$target_5.'</td>
					<td class="atas kanan bawah text_tengah">'.$target_akhir.'</td>
					<td class="atas kanan bawah text_tengah">'.$satuan.'</td>
					<td class="atas kanan bawah">'.$program['kode_skpd'].' '.$program['nama_skpd'].'</td>
				</tr>
			';
		}
	}
}

ksort($skpd_filter);
$skpd_filter_html = '<option value="">Pilih SKPD</option>';
foreach ($skpd_filter as $kode_skpd => $nama_skpd) {
	$skpd_filter_html .= '<option value="'.$kode_skpd.'">'.$kode_skpd.' '.$nama_skpd.'</option>';
}
?>
<style type="text/css">
	.debug-visi, .debug-misi, .debug-tujuan, .debug-sasaran, .debug-kode { 
		display: none; 
	}
	.indikator_program { 
		min-height: 40px; 
	}
	.aksi button {
		margin: 3px;
	}
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitoring dan Evaluasi RPD (Rencana Pembangunan Daerah) <br><?php echo $nama_pemda; ?></h4>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; height: 80vh;">
	<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<tr>
				<th style="width: 85px;" class="atas kiri kanan bawah text_tengah text_blok">No</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Tujuan</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Sasaran</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Program</th>
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
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
	</table>
</div>
<div class="modal fade" id="modal-monev" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPD</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<nav>
				  	<div class="nav nav-tabs" id="nav-tab" role="tablist">
					    <a class="nav-item nav-link" id="nav-tujuan-tab" data-toggle="tab" href="#nav-tujuan" role="tab" aria-controls="nav-tujuan" aria-selected="false">Tujuan</a>
					    <a class="nav-item nav-link" id="nav-sasaran-tab" data-toggle="tab" href="#nav-sasaran" role="tab" aria-controls="nav-sasaran" aria-selected="false">Sasaran</a>
					    <a class="nav-item nav-link" id="nav-program-tab" data-toggle="tab" href="#nav-program" role="tab" aria-controls="nav-program" aria-selected="false">Program</a>
				  	</div>
				</nav>
				<div class="tab-content" id="nav-tabContent">
				  	<div class="tab-pane fade" id="nav-tujuan" role="tabpanel" aria-labelledby="nav-tujuan-tab">...</div>
				  	<div class="tab-pane fade" id="nav-sasaran" role="tabpanel" aria-labelledby="nav-sasaran-tab">...</div>
				  	<div class="tab-pane fade" id="nav-program" role="tabpanel" aria-labelledby="nav-program-tab">...</div>
				</div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-tujuan" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPD Tujuan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<form>
				  	<div class="form-group">
				    	<label>Visi RPJPD</label>
				    	<select class="form-control" id="visi-teks"></select>
				  	</div>
				  	<div class="form-group">
				    	<label>Misi RPJPD</label>
				    	<select class="form-control" id="misi-teks"></select>
				  	</div>
				  	<div class="form-group">
				    	<label>Sasaran Pokok RPJPD</label>
				    	<select class="form-control" id="saspok-teks"></select>
				  	</div>
				  	<div class="form-group">
				    	<label>Kebijakan RPJPD</label>
				    	<select class="form-control" id="kebijakan-teks"></select>
				  	</div>
				  	<div class="form-group">
				    	<label>Isu RPJPD</label>
				    	<select class="form-control" id="isu-teks"></select>
				  	</div>
				  	<div class="form-group">
				    	<label>Tujuan Teks</label>
				    	<textarea class="form-control" id="tujuan-teks"></textarea>
				    	<small class="form-text text-muted">Input teks tujuan RPJPD.</small>
				  	</div>
				</form>
            </div>
            <div class="modal-footer">
            	<button class="btn btn-primary" onclick="simpan_tujuan();">Simpan</button>
            	<button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-tujuan-indikator" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPD Indikator Tujuan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<table class="table table-bordered">
            		<tbody>
            			<tr>
            				<th style="width: 175px;">Visi RPJPD</th>
            				<td class="text-center">:</td>
            				<td id="visi-teks-indikator"></td>
            			</tr>
            			<tr>
            				<th>Misi RPJPD</th>
            				<td class="text-center">:</td>
            				<td id="misi-teks-indikator"></td>
            			</tr>
            			<tr>
            				<th>Sasaran Pokok RPJPD</th>
            				<td class="text-center">:</td>
            				<td id="saspok-teks-indikator"></td>
            			</tr>
            			<tr>
            				<th>Kebijakan RPJPD</th>
            				<td class="text-center">:</td>
            				<td id="kebijakan-teks-indikator"></td>
            			</tr>
            			<tr>
            				<th>Isu RPJPD</th>
            				<td class="text-center">:</td>
            				<td id="isu-teks-indikator"></td>
            			</tr>
            			<tr>
            				<th>Tujuan RPD</th>
            				<td class="text-center">:</td>
            				<td id="tujuan-teks-indikator"></td>
            			</tr>
            		</tbody>
            	</table>
            	<form>
				  	<div class="form-group">
				    	<label>Indikator Teks</label>
				    	<input class="form-control" id="indikator-teks-tujuan" type="text">
				  	</div>
				  	<div class="form-row">
					  	<div class="form-group col-md-6">
					    	<label>Target Awal</label>
					    	<input class="form-control" id="indikator-teks-tujuan-vol-awal" type="text">
					  	</div>
					  	<div class="form-group col-md-6">
					    	<label>Satuan</label>
					    	<input class="form-control" id="indikator-teks-tujuan-satuan-awal" type="text">
					  	</div>
					</div>
				  	<div class="form-row">
					  	<div class="form-group col-md-6">
					    	<label>Target 1</label>
					    	<input class="form-control" id="indikator-teks-tujuan-vol-1" type="text">
					  	</div>
					  	<div class="form-group col-md-6">
					    	<label>Satuan</label>
					    	<input class="form-control" id="indikator-teks-tujuan-satuan-1" type="text">
					  	</div>
					</div>
				  	<div class="form-row">
					  	<div class="form-group col-md-6">
					    	<label>Target 2</label>
					    	<input class="form-control" id="indikator-teks-tujuan-vol-2" type="text">
					  	</div>
					  	<div class="form-group col-md-6">
					    	<label>Satuan</label>
					    	<input class="form-control" id="indikator-teks-tujuan-satuan-2" type="text">
					  	</div>
					</div>
				  	<div class="form-row">
					  	<div class="form-group col-md-6">
					    	<label>Target 3</label>
					    	<input class="form-control" id="indikator-teks-tujuan-vol-3" type="text">
					  	</div>
					  	<div class="form-group col-md-6">
					    	<label>Satuan</label>
					    	<input class="form-control" id="indikator-teks-tujuan-satuan-3" type="text">
					  	</div>
					</div>
				  	<div class="form-row">
					  	<div class="form-group col-md-6">
					    	<label>Target 4</label>
					    	<input class="form-control" id="indikator-teks-tujuan-vol-4" type="text">
					  	</div>
					  	<div class="form-group col-md-6">
					    	<label>Satuan</label>
					    	<input class="form-control" id="indikator-teks-tujuan-satuan-4" type="text">
					  	</div>
					</div>
				  	<div class="form-row">
					  	<div class="form-group col-md-6">
					    	<label>Target 5</label>
					    	<input class="form-control" id="indikator-teks-tujuan-vol-5" type="text">
					  	</div>
					  	<div class="form-group col-md-6">
					    	<label>Satuan</label>
					    	<input class="form-control" id="indikator-teks-tujuan-satuan-5" type="text">
					  	</div>
					</div>
				  	<div class="form-row">
					  	<div class="form-group col-md-6">
					    	<label>Target Akhir</label>
					    	<input class="form-control" id="indikator-teks-tujuan-vol-akhir" type="text">
					  	</div>
					  	<div class="form-group col-md-6">
					    	<label>Satuan</label>
					    	<input class="form-control" id="indikator-teks-tujuan-satuan-akhir" type="text">
					  	</div>
					</div>
				</form>
            </div>
            <div class="modal-footer">
            	<button class="btn btn-primary" onclick="simpan_tujuan_indikator();">Simpan</button>
            	<button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
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
		+'<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RPD</a>'
		+'<h3 style="margin-top: 20px;">SETTING</h3>'
		+'<label><input type="checkbox" onclick="tampilkan_edit(this);"> Edit Data RPD</label>'
		+'<label style="margin-left: 20px;"><input type="checkbox" onclick="show_debug(this);"> Debug Cascading RPD</label>'
		+'<label style="margin-left: 20px;">'
			+'Sembunyikan Baris '
			+'<select id="sembunyikan-baris" onchange="sembunyikan_baris(this);" style="padding: 5px 10px; min-width: 200px;">'
				+'<option value="">Pilih Baris</option>'
				+'<option value="tr-sasaran">Sasaran</option>'
				+'<option value="tr-program">Program</option>'
			+'</select>'
		+'</label>'
		+'<label style="margin-left: 20px;">'
			+'Filter SKPD '
			+'<select onchange="filter_skpd(this);" style="padding: 5px 10px; min-width: 200px; max-width: 400px;">'
				+'<?php echo $skpd_filter_html; ?>'
			+'</select>'
		+'</label>';
	jQuery('#action-sipd').append(aksi);
	function filter_skpd(that){
		var tr_program = jQuery('.tr-program');
		var val = jQuery(that).val();
		if(val == ''){
			tr_program.show();
		}else{
			tr_program.hide();
			jQuery('.tr-program[data-kode-skpd="'+val+'"]').show();
		}
	}
	function sembunyikan_baris(that){
		var val = jQuery(that).val();
		var tr_sasaran = jQuery('.tr-sasaran');
		var tr_program = jQuery('.tr-program');
		tr_misi.show();
		tr_tujuan.show();
		tr_sasaran.show();
		tr_program.show();
		if(val == 'tr-misi'){
			tr_misi.hide();
			tr_tujuan.hide();
			tr_sasaran.hide();
			tr_program.hide();
		}else if(val == 'tr-tujuan'){
			tr_tujuan.hide();
			tr_sasaran.hide();
			tr_program.hide();
		}else if(val == 'tr-sasaran'){
			tr_sasaran.hide();
			tr_program.hide();
		}else if(val == 'tr-program'){
			tr_program.hide();
		}
	}
	function show_debug(that){
		if(jQuery(that).is(':checked')){
			jQuery('.debug-visi').show();
			jQuery('.debug-misi').show();
			jQuery('.debug-tujuan').show();
			jQuery('.debug-sasaran').show();
			jQuery('.debug-kode').show();
		}else{
			jQuery('.debug-visi').hide();
			jQuery('.debug-misi').hide();
			jQuery('.debug-tujuan').hide();
			jQuery('.debug-sasaran').hide();
			jQuery('.debug-kode').hide();
		}
	}
	function tampilkan_edit(that){
		if(jQuery(that).is(':checked')){
			jQuery('.edit-monev').show();
		}else{
			jQuery('.edit-monev').hide();
		}
	}
	jQuery('.edit-monev').on('click', function(){
		jQuery('#wrap-loading').show();
		jQuery('#mod-monev').modal('show');
		jQuery('#wrap-loading').hide();
	});
	jQuery('#singkron-sipd').on('click', function(){
		if(confirm('Apakah anda yakin untuk mengambil data dari SIPD lokal? data lama akan diupdate!')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "singkron_rpd_sipd_lokal",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"user": "<?php echo $current_user->display_name; ?>"
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					alert(res.message);
	          	}
	        });
		}
	});
	jQuery('#tambah-data').on('click', function(){
		tampil_detail_popup();
	});

	function tampil_detail_popup(cb){
		jQuery('#wrap-loading').show();
		jQuery('#modal-monev').modal('show');
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": "<?php echo $api_key; ?>",
          		"table": "data_rpd_tujuan_lokal",
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
				console.log('res', res);
				jQuery('#wrap-loading').hide();
				var data_html = ""
					+'<button class="btn-sm btn-primary" style="margin-top: 10px;" onclick="tambah_tujuan();"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah tujuan</button>'
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center' style='width: 45px;'>No</th>"
								+"<th class='text-center'>Tujuan</th>"
								+"<th class='text-center' style='width: 195px;'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody>";
				var no = 0;
				for(var b in res.data_all){
					no++;
					data_html += ''
					+'<tr id-tujuan="'+res.data_all[b].id_unik+'">'
						+'<td class="text-center">'+(no)+'</td>'
						+'<td>'+res.data_all[b].nama+'</td>'
						+'<td class="text-center aksi">'
							+'<button class="btn-sm btn-primary" onclick="detail_tujuan(\''+res.data_all[b].id_unik+'\');"><i class="dashicons dashicons-search"></i></button>'
							+'<button class="btn-sm btn-primary" onclick="tambah_tujuan_indikator(\''+res.data_all[b].id_unik+'\');"><i class="dashicons dashicons-plus"></i></button>'
							+'<button class="btn-sm btn-warning" onclick="edit_tujuan(\''+res.data_all[b].id_unik+'\');"><i class="dashicons dashicons-edit"></i></button>'
							+'<button class="btn-sm btn-danger" onclick="hapus_tujuan(\''+res.data_all[b].id_unik+'\');"><i class="dashicons dashicons-trash"></i></button>'
						+'</td>'
					+'</tr>';

					res.data_all[b].detail.map(function(bb, i){
						data_html += ''
						+'<tr id-tujuan-indikator="'+bb.id_unik_indikator+'" style="background: #8000001f;">'
							+'<td class="text-center">'+no+'.'+(i+1)+'</td>'
							+'<td>'+bb.indikator_teks+'</td>'
							+'<td class="text-center aksi">'
								+'<button class="btn-sm btn-warning" onclick="edit_tujuan_indikator(\''+bb.id_unik_indikator+'\');"><i class="dashicons dashicons-edit"></i></button>'
								+'<button class="btn-sm btn-danger" onclick="hapus_tujuan_indikator(\''+bb.id_unik_indikator+'\');"><i class="dashicons dashicons-trash"></i></button>'
							+'</td>'
						+'</tr>';
					});
				};
				data_html += ""
						+"</tbody>";
					+"</table>";
				jQuery('#nav-tujuan').html(data_html);
          		jQuery('.nav-tabs a[href="#nav-tujuan"]').tab('show');
		        if(typeof cb == 'function'){
		        	cb();
		        }
          	}
        });
	}

	function tambah_tujuan_indikator(id_tujuan){
		jQuery('#wrap-loading').show();
  		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": "<?php echo $api_key; ?>",
          		"table": "data_rpd_tujuan_lokal",
          		"id_unik_tujuan": id_tujuan,
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
      			jQuery('#visi-teks-indikator').html('');
      			jQuery('#misi-teks-indikator').html('');
	  			jQuery('#saspok-teks-indikator').html('');
	  			jQuery('#kebijakan-teks-indikator').html('');
	  			jQuery('#isu-teks-indikator').html('');
				jQuery('#modal-tujuan-indikator').modal('show');
				for(var b in res.data_all){
          			res.data_all[b].rpjpd.visi.data.data.map(function(bb, ii){
          				if(bb.id == res.data_all[b].rpjpd.visi.id){
          					jQuery('#visi-teks-indikator').html(bb.visi_teks);
          				}
          			});

		  			res.data_all[b].rpjpd.misi.data.data.map(function(bb, ii){
		  				if(bb.id == res.data_all[b].rpjpd.misi.id){
          					jQuery('#misi-teks-indikator').html(bb.misi_teks);
		  				}
		  			});

		  			res.data_all[b].rpjpd.sasaran.data.data.map(function(bb, ii){
		  				if(bb.id == res.data_all[b].rpjpd.sasaran.id){
		  					jQuery('#saspok-teks-indikator').html(bb.saspok_teks);
		  				}
		  			});

		  			res.data_all[b].rpjpd.kebijakan.data.data.map(function(bb, ii){
		  				var selected = '';
		  				if(bb.id == res.data_all[b].rpjpd.kebijakan.id){
		  					jQuery('#kebijakan-teks-indikator').html(bb.kebijakan_teks);
		  				}
		  			});

		  			res.data_all[b].rpjpd.isu.data.data.map(function(bb, ii){
		  				if(bb.id == res.data_all[b].rpjpd.isu.id){
		  					jQuery('#isu-teks-indikator').html(bb.isu_teks);
		  				}
		  			});

					jQuery('#tujuan-teks-indikator').html(res.data_all[b].nama);
					jQuery('#modal-tujuan-indikator').attr('id-tujuan', res.data_all[b].id_unik);
				}
				jQuery('#wrap-loading').hide();
			}
		});
	}

	function tambah_tujuan(){
  		get_rpjpd('data_rpjpd_visi')
  		.then(function(visi){
  			var visi_html = '<option value="">Pilih visi RPJPD</option>';
  			visi.map(function(b, i){
  				visi_html += '<option value="'+b.id+'">'+b.visi_teks+'</option>';
  			});
  			jQuery('#visi-teks').html(visi_html);
  			jQuery('#misi-teks').html('');
  			jQuery('#saspok-teks').html('');
  			jQuery('#kebijakan-teks').html('');
  			jQuery('#isu-teks').html('');
			jQuery('#modal-tujuan').attr('data-id', '');
			jQuery('#modal-tujuan').modal('show');
			jQuery('#tujuan-teks').val('');
		});
	}

	function edit_tujuan(id_unik_tujuan){
		jQuery('#wrap-loading').show();
  		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": "<?php echo $api_key; ?>",
          		"table": "data_rpd_tujuan_lokal",
          		"id_unik_tujuan": id_unik_tujuan,
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
          		jQuery('#wrap-loading').hide();
          		for(var b in res.data_all){
          			var visi_html = '<option value="">Pilih visi RPJPD</option>';
          			res.data_all[b].rpjpd.visi.data.data.map(function(bb, ii){
          				var selected = '';
          				if(bb.id == res.data_all[b].rpjpd.visi.id){
          					selected = 'selected';
          				}
		  				visi_html += '<option '+selected+' value="'+bb.id+'">'+bb.visi_teks+'</option>';
          			});
		  			jQuery('#visi-teks').html(visi_html);

          			var misi_html = '<option value="">Pilih misi RPJPD</option>';
		  			res.data_all[b].rpjpd.misi.data.data.map(function(bb, ii){
		  				var selected = '';
		  				if(bb.id == res.data_all[b].rpjpd.misi.id){
		  					selected = 'selected';
		  				}
		  				misi_html += '<option '+selected+' value="'+bb.id+'">'+bb.misi_teks+'</option>';
		  			});
		  			jQuery('#misi-teks').html(misi_html);

          			var saspok_html = '<option value="">Pilih sasaran RPJPD</option>';
		  			res.data_all[b].rpjpd.sasaran.data.data.map(function(bb, ii){
		  				var selected = '';
		  				if(bb.id == res.data_all[b].rpjpd.sasaran.id){
		  					selected = 'selected';
		  				}
		  				saspok_html += '<option '+selected+' value="'+bb.id+'">'+bb.saspok_teks+'</option>';
		  			});
		  			jQuery('#saspok-teks').html(saspok_html);

          			var kebijakan_html = '<option value="">Pilih kebijakan RPJPD</option>';
		  			res.data_all[b].rpjpd.kebijakan.data.data.map(function(bb, ii){
		  				var selected = '';
		  				if(bb.id == res.data_all[b].rpjpd.kebijakan.id){
		  					selected = 'selected';
		  				}
		  				kebijakan_html += '<option '+selected+' value="'+bb.id+'">'+bb.kebijakan_teks+'</option>';
		  			});
		  			jQuery('#kebijakan-teks').html(kebijakan_html);

          			var isu_html = '<option value="">Pilih isu RPJPD</option>';
		  			res.data_all[b].rpjpd.isu.data.data.map(function(bb, ii){
		  				var selected = '';
		  				if(bb.id == res.data_all[b].rpjpd.isu.id){
		  					selected = 'selected';
		  				}
		  				isu_html += '<option '+selected+' value="'+bb.id+'">'+bb.isu_teks+'</option>';
		  			});
		  			jQuery('#isu-teks').html(isu_html);

					jQuery('#tujuan-teks').val(res.data_all[b].nama);
		  		}
				jQuery('#modal-tujuan').attr('data-id', id_unik_tujuan);
				jQuery('#modal-tujuan').modal('show');
			}
		});
	}

	function simpan_tujuan(){
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			var tujuan_teks = jQuery('#tujuan-teks').val();
			if(tujuan_teks == ''){
				return alert('Tujuan tidak boleh kosong!');
			}
			var id_isu = jQuery('#isu-teks').val();
			if(id_isu == ''){
				return alert('Isu RPJPD tidak boleh kosong!');
			}
			var id_tujuan = jQuery('#modal-tujuan').attr('data-id');
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpd_tujuan_lokal',
	          		"data": tujuan_teks,
	          		"id_isu": id_isu,
	          		"id": id_tujuan
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						jQuery('#modal-tujuan').modal('hide');
						jQuery('#tambah-data').click();
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function hapus_tujuan(id_tujuan_unik){
		if(confirm('Apakah anda yakin untuk menghapus data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "hapus_rpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpd_tujuan_lokal',
	          		"id": id_tujuan_unik
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						jQuery('#modal-tujuan').modal('hide');
						jQuery('#tambah-data').click();
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function simpan_tujuan_indikator() {
		var id_tujuan = jQuery('#modal-tujuan-indikator').attr('id-tujuan');
		if(id_tujuan == ''){
			return alert('ID tujuan tidak ditemukan!');
		}
		var tujuan_teks_indikator = jQuery('#indikator-teks-tujuan').val();
		if(tujuan_teks_indikator == ''){
			return alert('Indikator tujuan tidak boleh kosong!');
		}
		var vol_awal = jQuery('#indikator-teks-tujuan-vol-awal').val();
		if(vol_awal == ''){
			return alert('Volume awal indikator tujuan tidak boleh kosong!');
		}
		var satuan_awal = jQuery('#indikator-teks-tujuan-satuan-awal').val();
		if(satuan_awal == ''){
			return alert('Satuan awal indikator tujuan tidak boleh kosong!');
		}
		var vol_1 = jQuery('#indikator-teks-tujuan-vol-1').val();
		if(vol_1 == ''){
			return alert('Volume 1 indikator tujuan tidak boleh kosong!');
		}
		var satuan_1 = jQuery('#indikator-teks-tujuan-satuan-1').val();
		if(satuan_1 == ''){
			return alert('Satuan 1 indikator tujuan tidak boleh kosong!');
		}
		var vol_2 = jQuery('#indikator-teks-tujuan-vol-2').val();
		if(vol_2 == ''){
			return alert('Volume 2 indikator tujuan tidak boleh kosong!');
		}
		var satuan_2 = jQuery('#indikator-teks-tujuan-satuan-2').val();
		if(satuan_2 == ''){
			return alert('Satuan 2 indikator tujuan tidak boleh kosong!');
		}
		var vol_3 = jQuery('#indikator-teks-tujuan-vol-3').val();
		if(vol_3 == ''){
			return alert('Volume 3 indikator tujuan tidak boleh kosong!');
		}
		var satuan_3 = jQuery('#indikator-teks-tujuan-satuan-3').val();
		if(satuan_3 == ''){
			return alert('Satuan 3 indikator tujuan tidak boleh kosong!');
		}
		var vol_4 = jQuery('#indikator-teks-tujuan-vol-4').val();
		if(vol_4 == ''){
			return alert('Volume 4 indikator tujuan tidak boleh kosong!');
		}
		var satuan_4 = jQuery('#indikator-teks-tujuan-satuan-4').val();
		if(satuan_4 == ''){
			return alert('Satuan 4 indikator tujuan tidak boleh kosong!');
		}
		var vol_5 = jQuery('#indikator-teks-tujuan-vol-5').val();
		if(vol_5 == ''){
			return alert('Volume 5 indikator tujuan tidak boleh kosong!');
		}
		var satuan_5 = jQuery('#indikator-teks-tujuan-satuan-5').val();
		if(satuan_5 == ''){
			return alert('Satuan 5 indikator tujuan tidak boleh kosong!');
		}
		var vol_akhir = jQuery('#indikator-teks-tujuan-vol-akhir').val();
		if(vol_akhir == ''){
			return alert('Volume akhir indikator tujuan tidak boleh kosong!');
		}
		var satuan_akhir = jQuery('#indikator-teks-tujuan-satuan-akhir').val();
		if(satuan_akhir == ''){
			return alert('Satuan akhir indikator tujuan tidak boleh kosong!');
		}
		var id_indikator = jQuery('#modal-tujuan-indikator').attr('data-id');
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpd_tujuan_lokal',
	          		"data": tujuan_teks_indikator,
	          		"id_tujuan": id_tujuan,
	          		"vol_awal": vol_awal,
	          		"satuan_awal": satuan_awal,
	          		"vol_1": vol_1,
	          		"satuan_1": satuan_1,
	          		"vol_2": vol_2,
	          		"satuan_2": satuan_2,
	          		"vol_3": vol_3,
	          		"satuan_3": satuan_3,
	          		"vol_4": vol_4,
	          		"satuan_4": satuan_4,
	          		"vol_5": vol_5,
	          		"satuan_5": satuan_5,
	          		"vol_akhir": vol_akhir,
	          		"satuan_akhir": satuan_akhir,
	          		"id": id_indikator
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						jQuery('#modal-tujuan-indikator').modal('hide');
						jQuery('#tambah-data').click();
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function get_rpjpd(table, id){
		jQuery('#wrap-loading').show();
		return new Promise(function(resolve, reject){
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "get_rpjpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": table,
	          		"id": id
	          	},
	          	dataType: "json",
	          	success: function(res){
	          		jQuery('#wrap-loading').hide();
	          		resolve(res.data);
	          	}
	        });
		});
	}

	jQuery('#visi-teks').on('change', function(){
		var id_visi = jQuery(this).val();
		if(id_visi){
			get_rpjpd('data_rpjpd_misi', id_visi)
	  		.then(function(data){
	  			var html = '<option value="">Pilih misi RPJPD</option>';
	  			data.map(function(b, i){
	  				html += '<option value="'+b.id+'">'+b.misi_teks+'</option>';
	  			});
	  			jQuery('#misi-teks').html(html);
	  			jQuery('#saspok-teks').html('');
	  			jQuery('#kebijakan-teks').html('');
	  			jQuery('#isu-teks').html('');
	  		});
	  	}else{
  			jQuery('#saspok-teks').html('');
  			jQuery('#kebijakan-teks').html('');
	  		jQuery('#isu-teks').html('');
	  	}
	});

	jQuery('#misi-teks').on('change', function(){
		var id_misi = jQuery(this).val();
		if(id_misi){
			get_rpjpd('data_rpjpd_sasaran', id_misi)
	  		.then(function(data){
	  			var html = '<option value="">Pilih sasaran pokok RPJPD</option>';
	  			data.map(function(b, i){
	  				html += '<option value="'+b.id+'">'+b.saspok_teks+'</option>';
	  			});
	  			jQuery('#saspok-teks').html(html);
	  			jQuery('#kebijakan-teks').html('');
	  			jQuery('#isu-teks').html('');
	  		});
	  	}else{
	  		jQuery('#kebijakan-teks').html('');
	  		jQuery('#isu-teks').html('');
	  	}
	});

	jQuery('#saspok-teks').on('change', function(){
		var id_saspok = jQuery(this).val();
		if(id_saspok){
			get_rpjpd('data_rpjpd_kebijakan', id_saspok)
	  		.then(function(data){
	  			var html = '<option value="">Pilih kebijakan RPJPD</option>';
	  			data.map(function(b, i){
	  				html += '<option value="'+b.id+'">'+b.kebijakan_teks+'</option>';
	  			});
	  			jQuery('#kebijakan-teks').html(html);
	  			jQuery('#isu-teks').html('');
	  		});
	  	}else{
	  		jQuery('#isu-teks').html('');
	  	}
	});

	jQuery('#kebijakan-teks').on('change', function(){
		var id_kebijakan = jQuery(this).val();
		if(id_kebijakan){
			get_rpjpd('data_rpjpd_isu', id_kebijakan)
	  		.then(function(data){
	  			var html = '<option value="">Pilih isu RPJPD</option>';
	  			data.map(function(b, i){
	  				html += '<option value="'+b.id+'">'+b.isu_teks+'</option>';
	  			});
	  			jQuery('#isu-teks').html(html);
	  		});
	  	}
	});
</script>