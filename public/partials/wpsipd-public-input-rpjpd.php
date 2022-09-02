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
	.debug-visi, .debug-misi, .debug-tujuan, .debug-sasaran, .debug-kode { display: none; }
	.indikator_program { min-height: 40px; }
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPD</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<nav>
				  	<div class="nav nav-tabs" id="nav-tab" role="tablist">
					    <a class="nav-item nav-link active" id="nav-tujuan-tab" data-toggle="tab" href="#nav-tujuan" role="tab" aria-controls="nav-tujuan" aria-selected="false">Tujuan</a>
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
<script type="text/javascript">
	run_download_excel();
	let data_all = <?php echo json_encode($data_all); ?>;

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
		jQuery('#wrap-loading').show();
		jQuery('#modal-monev').modal('show');
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_tujuan_rpd",
          		"api_key": "<?php echo $api_key; ?>",
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
				jQuery('#wrap-loading').hide();
          	}
        });
	});
</script>