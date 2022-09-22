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

$jadwal_lokal = $wpdb->get_results("SELECT * from data_jadwal_lokal where id_jadwal_lokal = (select max(id_jadwal_lokal) from data_jadwal_lokal where id_tipe=1)", ARRAY_A);
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

$awal_rpjpd = 2018;
$akhir_rpjpd = $awal_rpjpd+20;
$nama_pemda = get_option('_crb_daerah');

$current_user = wp_get_current_user();
$bulan = date('m');
$body_monev = '';

$data_all = array(
	'data' => array()
);
$bulan = date('m');

$visi_ids = array();
$misi_ids = array();
$sasaran_ids = array();
$kebijakan_ids = array();
$isu_ids = array();

$sql = "
	select 
		* 
	from data_rpjpd_visi
";
$visi_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($visi_all as $visi) {
	if(empty($data_all['data'][$visi['id']])){
		$data_all['data'][$visi['id']] = array(
			'nama' => $visi['visi_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data'][$visi['id']]['detail'][] = $visi;

	$visi_ids[$visi['id']] = "'".$visi['id']."'";
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjpd_misi
		where id_visi=%s
	", $visi['id']);
	$misi_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($misi_all as $misi) {
		if(empty($data_all['data'][$visi['id']]['data'][$misi['id']])){
			$data_all['data'][$visi['id']]['data'][$misi['id']] = array(
				'nama' => $misi['misi_teks'],
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data'][$visi['id']]['data'][$misi['id']]['detail'][] = $misi;

		$misi_ids[$misi['id']] = "'".$misi['id']."'";
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjpd_sasaran
			where id_misi=%s
		", $misi['id']);
		$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($sasaran_all as $sasaran) {
			$sasaran_ids[$sasaran['id']] = "'".$sasaran['id']."'";
			if(empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']])){
				$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']] = array(
					'nama' => $sasaran['saspok_teks'],
					'detail' => array(),
					'data' => array()
				);
			}
			$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['detail'][] = $sasaran;

			$sql = $wpdb->prepare("
				select 
					* 
				from data_rpjpd_kebijakan
				where id_saspok=%s
			", $sasaran['id']);
			$kebijakan_all = $wpdb->get_results($sql, ARRAY_A);
			foreach ($kebijakan_all as $kebijakan) {
				$kebijakan_ids[$kebijakan['id']] = "'".$kebijakan['id']."'";
				if(empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']])){
					$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']] = array(
						'nama' => $kebijakan['kebijakan_teks'],
						'detail' => array(),
						'data' => array()
					);
				}
				$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['detail'][] = $kebijakan;

				$sql = $wpdb->prepare("
					select 
						* 
					from data_rpjpd_isu
					where id_kebijakan=%s
				", $kebijakan['id']);
				$isu_all = $wpdb->get_results($sql, ARRAY_A);
				foreach ($isu_all as $isu) {
					$isu_ids[$isu['id']] = "'".$isu['id']."'";
					if(empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']])){
						$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']] = array(
							'nama' => $isu['isu_teks'],
							'detail' => array(),
							'data' => array()
						);
					}
					$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']]['detail'][] = $isu;
				}
			}
		}
	}
}

// buat array data kosong
if(empty($data_all['data']['visi_kosong'])){
	$data_all['data']['visi_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'detail' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong'])){
	$data_all['data']['visi_kosong']['data']['misi_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'detail' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong'])){
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'detail' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong'])){
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'detail' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong']['data']['isu_kosong'])){
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong']['data']['isu_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'detail' => array(),
		'data' => array()
	);
}

// select misi yang belum terselect
if(!empty($misi_ids)){
	$sql = "
		select 
			* 
		from data_rpjpd_misi
		where id not in (".implode(',', $misi_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpjpd_misi
	";
}
$misi_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($misi_all_kosong as $misi) {
	$misi_ids[$misi['id']] = "'".$misi['id']."'";
	if(empty($data_all['data']['visi_kosong']['data'][$misi['id']])){
		$data_all['data']['visi_kosong']['data'][$misi['id']] = array(
			'nama' => $misi['misi_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data']['visi_kosong']['data'][$misi['id']]['detail'][] = $misi;
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjpd_sasaran
		where id_misi=%s
	", $misi['id']);
	$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($sasaran_all as $sasaran) {
		$sasaran_ids[$sasaran['id']] = "'".$sasaran['id']."'";
		if(empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$sasaran['id']])){
			$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$sasaran['id']] = array(
				'nama' => $sasaran['saspok_teks'],
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$sasaran['id']]['detail'][] = $sasaran;
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjpd_kebijakan
			where id_sasaran=%s
		", $sasaran['id']);
		$kebijakan_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($kebijakan_all as $kebijakan) {
			$kebijakan_ids[$kebijakan['id']] = "'".$kebijakan['id']."'";
			if(empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']])){
				$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']] = array(
					'nama' => $kebijakan['kebijakan_teks'],
					'detail' => array(),
					'data' => array()
				);
			}
			$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['detail'][] = $kebijakan;
			$sql = $wpdb->prepare("
				select 
					* 
				from data_rpjpd_isu
				where id_kebijakan=%s
			", $sasaran['id']);
			$isu_all = $wpdb->get_results($sql, ARRAY_A);
			foreach ($isu_all as $isu) {
				$isu_ids[$isu['id']] = "'".$isu['id']."'";
				if(empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']])){
					$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']] = array(
						'nama' => $isu['isu_teks'],
						'detail' => array(),
						'data' => array()
					);
				}
				$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']]['detail'][] = $isu;
			}
		}
	}
}

// select sasaran yang belum terselect
if(!empty($sasaran_ids)){
	$sql = "
		select 
			* 
		from data_rpjpd_sasaran
		where id not in (".implode(',', $sasaran_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpjpd_sasaran
	";
}
$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($sasaran_all as $sasaran) {
	$sasaran_ids[$sasaran['id']] = "'".$sasaran['id']."'";
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$sasaran['id']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$sasaran['id']] = array(
			'nama' => $sasaran['saspok_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$sasaran['id']]['detail'][] = $sasaran;
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjpd_kebijakan
		where id_sasaran=%s
	", $sasaran['id']);
	$kebijakan_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($kebijakan_all as $kebijakan) {
		$kebijakan_ids[$kebijakan['id']] = "'".$kebijakan['id']."'";
		if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$sasaran['id']]['data'][$kebijakan['id']])){
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$sasaran['id']]['data'][$kebijakan['id']] = array(
				'nama' => $kebijakan['kebijakan_teks'],
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$sasaran['id']]['data'][$kebijakan['id']]['detail'][] = $kebijakan;
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjpd_isu
			where id_kebijakan=%s
		", $sasaran['id']);
		$isu_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($isu_all as $isu) {
			$isu_ids[$isu['id']] = "'".$isu['id']."'";
			if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']])){
				$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']] = array(
					'nama' => $isu['isu_teks'],
					'detail' => array(),
					'data' => array()
				);
			}
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']]['detail'][] = $isu;
		}
	}
}

// select kebijakan yang belum terselect
if(!empty($kebijakan_ids)){
	$sql = "
		select 
			* 
		from data_rpjpd_kebijakan
		where id not in (".implode(',', $kebijakan_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpjpd_kebijakan
	";
}
$kebijakan_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($kebijakan_all as $kebijakan) {
	$kebijakan_ids[$kebijakan['id']] = "'".$kebijakan['id']."'";
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data'][$kebijakan['id']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data'][$kebijakan['id']] = array(
			'nama' => $kebijakan['kebijakan_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data'][$kebijakan['id']]['detail'][] = $kebijakan;
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjpd_isu
		where id_kebijakan=%s
	", 'sasaran_kosong');
	$isu_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($isu_all as $isu) {
		$isu_ids[$isu['id']] = "'".$isu['id']."'";
		if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong']['data'][$isu['id']])){
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong']['data'][$isu['id']] = array(
				'nama' => $isu['isu_teks'],
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong']['data'][$isu['id']]['detail'][] = $isu;
	}
}

// select isu yang belum terselect
if(!empty($isu_ids)){
	$sql = "
		select 
			* 
		from data_rpjpd_isu
		where id not in (".implode(',', $isu_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpjpd_isu
	";
}
$isu_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($isu_all as $isu) {
	$isu_ids[$isu['id']] = "'".$isu['id']."'";
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong']['data'][$isu['id']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong']['data'][$isu['id']] = array(
			'nama' => $isu['isu_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong']['data'][$isu['id']]['detail'][] = $isu;
}

// hapus array jika data dengan key kosong tidak ada datanya
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong']['detail'])){
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['data']['kebijakan_kosong']);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']['detail'])){
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['sasaran_kosong']);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['detail'])){
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']);
}
if(empty($data_all['data']['visi_kosong']['detail'])){
	unset($data_all['data']['visi_kosong']);
}

$body = '';
$no_visi = 0;
foreach ($data_all['data'] as $visi) {
	$no_visi++;
	$body .= '
		<tr class="tr-visi">
			<td class="kiri atas kanan bawah">'.$no_visi.'</td>
			<td class="atas kanan bawah">'.$visi['nama'].'</td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
		</tr>
	';
	$no_misi = 0;
	foreach ($visi['data'] as $misi) {
		$no_misi++;
		$body .= '
			<tr class="tr-misi">
				<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'</td>
				<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
				<td class="atas kanan bawah">'.$misi['nama'].'</td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
			</tr>
		';
		$no_sasaran = 0;
		foreach ($misi['data'] as $sasaran) {
			$no_sasaran++;
			$body .= '
				<tr class="tr-sasaran">
					<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'.'.$no_sasaran.'</td>
					<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
					<td class="atas kanan bawah"><span class="debug-misi">'.$misi['nama'].'</span></td>
					<td class="atas kanan bawah">'.$sasaran['nama'].'</td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
				</tr>
			';
			$no_kebijakan = 0;
			foreach ($sasaran['data'] as $kebijakan) {
				$no_kebijakan++;
				$body .= '
					<tr class="tr-kebijakan">
						<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'.'.$no_sasaran.'.'.$no_kebijakan.'</td>
						<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
						<td class="atas kanan bawah"><span class="debug-misi">'.$misi['nama'].'</span></td>
						<td class="atas kanan bawah"><span class="debug-sasaran">'.$sasaran['nama'].'</span></td>
						<td class="atas kanan bawah">'.$kebijakan['nama'].'</td>
						<td class="atas kanan bawah"></td>
					</tr>
				';
				$no_isu = 0;
				foreach ($kebijakan['data'] as $isu) {
					$no_isu++;
					$body .= '
						<tr class="tr-isu">
							<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'.'.$no_sasaran.'.'.$no_kebijakan.'.'.$no_isu.'</td>
							<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
							<td class="atas kanan bawah"><span class="debug-misi">'.$misi['nama'].'</span></td>
							<td class="atas kanan bawah"><span class="debug-sasaran">'.$sasaran['nama'].'</span></td>
							<td class="atas kanan bawah"><span class="debug-kebijakan">'.$kebijakan['nama'].'</span></td>
							<td class="atas kanan bawah">'.$isu['nama'].'</td>
						</tr>
					';
				}
			}
		}
	}
}
?>
<style type="text/css">
	.debug-visi, .debug-misi, .debug-sasaran, .debug-kebijakan, .debug-isu { 
		display: none; 
	}
	.indikator_sasaran { 
		min-height: 40px; 
	}
	.aksi button {
		margin: 3px;
	}
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitoring dan Evaluasi RPJPD (Rencana Pembangunan Jangka Panjang Daerah) <br><?php echo $nama_pemda; ?></h4>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; height: 80vh;">
	<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<tr>
				<th style="width: 85px;" class="atas kiri kanan bawah text_tengah text_blok">No</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Visi</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Misi</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Sasaran</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Kebijakan</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Isu</th>
			</tr>
			<tr>
				<th class='atas kiri kanan bawah text_tengah text_blok'>0</th>
				<th class='atas kanan bawah text_tengah text_blok'>1</th>
				<th class='atas kanan bawah text_tengah text_blok'>2</th>
				<th class='atas kanan bawah text_tengah text_blok'>3</th>
				<th class='atas kanan bawah text_tengah text_blok'>4</th>
				<th class='atas kanan bawah text_tengah text_blok'>5</th>
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
                <h4 style="margin: 0;" class="modal-title" id="">Data RPJPD</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<nav>
				  	<div class="nav nav-tabs" id="nav-tab" role="tablist">
					    <a class="nav-item nav-link" id="nav-visi-tab" data-toggle="tab" href="#nav-visi" role="tab" aria-controls="nav-visi" aria-selected="false">visi</a>
					    <a class="nav-item nav-link" id="nav-misi-tab" data-toggle="tab" href="#nav-misi" role="tab" aria-controls="nav-misi" aria-selected="false">misi</a>
					    <a class="nav-item nav-link" id="nav-sasaran-tab" data-toggle="tab" href="#nav-sasaran" role="tab" aria-controls="nav-sasaran" aria-selected="false">sasaran</a>
					    <a class="nav-item nav-link" id="nav-kebijakan-tab" data-toggle="tab" href="#nav-kebijakan" role="tab" aria-controls="nav-kebijakan" aria-selected="false">kebijakan</a>
					    <a class="nav-item nav-link" id="nav-isu-tab" data-toggle="tab" href="#nav-isu" role="tab" aria-controls="nav-isu" aria-selected="false">isu</a>
				  	</div>
				</nav>
				<div class="tab-content" id="nav-tabContent">
				  	<div class="tab-pane fade" id="nav-visi" role="tabpanel" aria-labelledby="nav-visi-tab">...</div>
				  	<div class="tab-pane fade" id="nav-misi" role="tabpanel" aria-labelledby="nav-misi-tab">...</div>
				  	<div class="tab-pane fade" id="nav-sasaran" role="tabpanel" aria-labelledby="nav-sasaran-tab">...</div>
				  	<div class="tab-pane fade" id="nav-kebijakan" role="tabpanel" aria-labelledby="nav-kebijakan-tab">...</div>
				  	<div class="tab-pane fade" id="nav-isu" role="tabpanel" aria-labelledby="nav-isu-tab">...</div>
				</div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-visi" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPJPD Visi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<form>
				  	<div class="form-group">
				    	<label>Visi Teks</label>
				    	<textarea class="form-control" id="visi-teks"></textarea>
				    	<small class="form-text text-muted">Input teks visi RPJPD.</small>
				  	</div>
				</form>
            </div>
            <div class="modal-footer">
            	<button class="btn btn-primary" onclick="simpan_visi();">Simpan</button>
            	<button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-misi" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPJPD Misi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<form>
				  	<div class="form-group">
				    	<label>Visi Teks</label>
				    	<textarea class="form-control" id="visi-teks-misi" disabled></textarea>
				  	</div>
				  	<div class="form-group">
				    	<label>Misi Teks</label>
				    	<textarea class="form-control" id="misi-teks"></textarea>
				    	<small class="form-text text-muted">Input teks misi RPJPD.</small>
				  	</div>
				</form>
            </div>
            <div class="modal-footer">
            	<button class="btn btn-primary" onclick="simpan_misi();">Simpan</button>
            	<button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-saspok" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPJPD Sasaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<form>
				  	<div class="form-group">
				    	<label>Visi Teks</label>
				    	<textarea class="form-control" id="visi-teks-sasaran" disabled></textarea>
				  	</div>
				  	<div class="form-group">
				    	<label>Misi Teks</label>
				    	<textarea class="form-control" id="misi-teks-sasaran" disabled></textarea>
				  	</div>
				  	<div class="form-group">
				    	<label>Sasaran Teks</label>
				    	<textarea class="form-control" id="sasaran-teks"></textarea>
				    	<small class="form-text text-muted">Input teks sasaran RPJPD.</small>
				  	</div>
				</form>
            </div>
            <div class="modal-footer">
            	<button class="btn btn-primary" onclick="simpan_saspok();">Simpan</button>
            	<button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-kebijakan" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPJPD Kebijakan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<form>
				  	<div class="form-group">
				    	<label>Visi Teks</label>
				    	<textarea class="form-control" id="visi-teks-kebijakan" disabled></textarea>
				  	</div>
				  	<div class="form-group">
				    	<label>Misi Teks</label>
				    	<textarea class="form-control" id="misi-teks-kebijakan" disabled></textarea>
				  	</div>
				  	<div class="form-group">
				    	<label>Sasaran Teks</label>
				    	<textarea class="form-control" id="sasaran-teks-kebijakan" disabled></textarea>
				  	</div>
				  	<div class="form-group">
				    	<label>Kebijakan Teks</label>
				    	<textarea class="form-control" id="kebijakan-teks"></textarea>
				    	<small class="form-text text-muted">Input teks kebijakan RPJPD.</small>
				  	</div>
				</form>
            </div>
            <div class="modal-footer">
            	<button class="btn btn-primary" onclick="simpan_kebijakan();">Simpan</button>
            	<button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-isu" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPJPD Isu</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<form>
				  	<div class="form-group">
				    	<label>Visi Teks</label>
				    	<textarea class="form-control" id="visi-teks-isu" disabled></textarea>
				  	</div>
				  	<div class="form-group">
				    	<label>Misi Teks</label>
				    	<textarea class="form-control" id="misi-teks-isu" disabled></textarea>
				  	</div>
				  	<div class="form-group">
				    	<label>Sasaran Teks</label>
				    	<textarea class="form-control" id="sasaran-teks-isu" disabled></textarea>
				  	</div>
				  	<div class="form-group">
				    	<label>Kebijakan Teks</label>
				    	<textarea class="form-control" id="kebijakan-teks-isu" disabled></textarea>
				  	</div>
				  	<div class="form-group">
				    	<label>Isu Teks</label>
				    	<textarea class="form-control" id="isu-teks"></textarea>
				    	<small class="form-text text-muted">Input teks isu RPJPD.</small>
				  	</div>
				</form>
            </div>
            <div class="modal-footer">
            	<button class="btn btn-primary" onclick="simpan_isu();">Simpan</button>
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
		+'<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RPJPD</a>'
		+'<h3 style="margin-top: 20px;">SETTING</h3>'
		+'<label><input type="checkbox" onclick="tampilkan_edit(this);"> Edit Data RPJPD</label>'
		+'<label style="margin-left: 20px;"><input type="checkbox" onclick="show_debug(this);"> Debug Cascading RPJPD</label>'
		+'<label style="margin-left: 20px;">'
			+'Sembunyikan Baris '
			+'<select id="sembunyikan-baris" onchange="sembunyikan_baris(this);" style="padding: 5px 10px; min-width: 200px;">'
				+'<option value="">Pilih Baris</option>'
				+'<option value="tr-misi">misi</option>'
				+'<option value="tr-sasaran">sasaran</option>'
				+'<option value="tr-kebijakan">kebijakan</option>'
				+'<option value="tr-isu">isu</option>'
			+'</select>'
		+'</label>';
	jQuery('#action-sipd').append(aksi);
	function filter_skpd(that){
		var tr_sasaran = jQuery('.tr-sasaran');
		var val = jQuery(that).val();
		if(val == ''){
			tr_sasaran.show();
		}else{
			tr_sasaran.hide();
			jQuery('.tr-sasaran[data-kode-skpd="'+val+'"]').show();
		}
	}

	function sembunyikan_baris(that){
		var val = jQuery(that).val();
		var tr_misi = jQuery('.tr-misi');
		var tr_sasaran = jQuery('.tr-sasaran');
		var tr_kebijakan = jQuery('.tr-kebijakan');
		var tr_isu = jQuery('.tr-isu');
		tr_misi.show();
		tr_sasaran.show();
		tr_kebijakan.show();
		tr_isu.show();
		if(val == 'tr-misi'){
			tr_misi.hide();
			tr_sasaran.hide();
			tr_kebijakan.hide();
			tr_isu.hide();
		}else if(val == 'tr-sasaran'){
			tr_sasaran.hide();
			tr_kebijakan.hide();
			tr_isu.hide();
		}else if(val == 'tr-kebijakan'){
			tr_kebijakan.hide();
			tr_isu.hide();
		}else if(val == 'tr-isu'){
			tr_isu.hide();
		}
	}

	function show_debug(that){
		if(jQuery(that).is(':checked')){
			jQuery('.debug-visi').show();
			jQuery('.debug-misi').show();
			jQuery('.debug-sasaran').show();
			jQuery('.debug-kebijakan').show();
			jQuery('.debug-isu').show();
		}else{
			jQuery('.debug-visi').hide();
			jQuery('.debug-misi').hide();
			jQuery('.debug-sasaran').hide();
			jQuery('.debug-kebijakan').hide();
			jQuery('.debug-isu').hide();
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
	          		"action": "singkron_rpjpd_sipd_lokal",
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
          		"action": "get_rpjpd",
          		"api_key": "<?php echo $api_key; ?>",
          		"table": 'data_rpjpd_visi'
          	},
          	dataType: "json",
          	success: function(res){
          		console.log('res', res);
				jQuery('#wrap-loading').hide();
				var data_html = ""
					+'<button class="btn-sm btn-primary" style="margin-top: 10px;" onclick="tambah_visi();"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Visi</button>'
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center' style='width: 45px;'>No</th>"
								+"<th class='text-center'>Visi</th>"
								+"<th class='text-center' style='width: 160px;'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody>";
				res.data.map(function(b, i){
					data_html += ''
					+'<tr id-visi="'+b.id+'">'
						+'<td class="text-center">'+(i+1)+'</td>'
						+'<td>'+b.visi_teks+'</td>'
						+'<td class="text-center aksi">'
							+'<button class="btn-sm btn-primary" onclick="detail_visi('+b.id+');"><i class="dashicons dashicons-search"></i></button>'
							+'<button class="btn-sm btn-warning" onclick="edit_visi('+b.id+');"><i class="dashicons dashicons-edit"></i></button>'
							+'<button class="btn-sm btn-danger" onclick="hapus_visi('+b.id+');"><i class="dashicons dashicons-trash"></i></button>'
						+'</td>'
					+'</tr>';
				});
				data_html += ""
						+"</tbody>";
					+"</table>";
				jQuery('#nav-visi').html(data_html)
          		jQuery('.nav-tabs a[href="#nav-visi"]').tab('show');
          	}
        });
	});

	function tambah_visi(){
		jQuery('#modal-visi').attr('data-id', '');
		jQuery('#modal-visi').modal('show');
		jQuery('#visi-teks').val('');
	}

	function tambah_misi(){
		jQuery('#modal-misi').attr('data-id', '');
		jQuery('#modal-misi').modal('show');
		var visi_teks = jQuery('tr[id-visi="'+jQuery('#nav-misi .tambah-data').attr('id-visi')+'"]').find('td').eq(1).text();
		jQuery('#visi-teks-misi').val(visi_teks);
		jQuery('#misi-teks').val('');
	}

	function tambah_saspok(){
		jQuery('#modal-saspok').attr('data-id', '');
		jQuery('#modal-saspok').modal('show');
		var visi_teks = jQuery('tr[id-visi="'+jQuery('#nav-misi .tambah-data').attr('id-visi')+'"]').find('td').eq(1).text();
		jQuery('#visi-teks-sasaran').val(visi_teks);
		var misi_teks = jQuery('tr[id-misi="'+jQuery('#nav-sasaran .tambah-data').attr('id-misi')+'"]').find('td').eq(1).text();
		jQuery('#misi-teks-sasaran').val(misi_teks);
		jQuery('#sasaran-teks').val('');
	}

	function tambah_kebijakan(){
		jQuery('#modal-kebijakan').attr('data-id', '');
		jQuery('#modal-kebijakan').modal('show');
		var visi_teks = jQuery('tr[id-visi="'+jQuery('#nav-misi .tambah-data').attr('id-visi')+'"]').find('td').eq(1).text();
		jQuery('#visi-teks-kebijakan').val(visi_teks);
		var misi_teks = jQuery('tr[id-misi="'+jQuery('#nav-sasaran .tambah-data').attr('id-misi')+'"]').find('td').eq(1).text();
		jQuery('#misi-teks-kebijakan').val(misi_teks);
		var sasaran_teks = jQuery('tr[id-saspok="'+jQuery('#nav-kebijakan .tambah-data').attr('id-saspok')+'"]').find('td').eq(1).text();
		jQuery('#sasaran-teks-kebijakan').val(sasaran_teks);
		jQuery('#kebijakan-teks').val('');
	}

	function tambah_isu(){
		jQuery('#modal-isu').attr('data-id', '');
		jQuery('#modal-isu').modal('show');
		var visi_teks = jQuery('tr[id-visi="'+jQuery('#nav-misi .tambah-data').attr('id-visi')+'"]').find('td').eq(1).text();
		jQuery('#visi-teks-isu').val(visi_teks);
		var misi_teks = jQuery('tr[id-misi="'+jQuery('#nav-sasaran .tambah-data').attr('id-misi')+'"]').find('td').eq(1).text();
		jQuery('#misi-teks-isu').val(misi_teks);
		var sasaran_teks = jQuery('tr[id-saspok="'+jQuery('#nav-kebijakan .tambah-data').attr('id-saspok')+'"]').find('td').eq(1).text();
		jQuery('#sasaran-teks-isu').val(sasaran_teks);
		var kebijakan_teks = jQuery('tr[id-kebijakan="'+jQuery('#nav-isu .tambah-data').attr('id-kebijakan')+'"]').find('td').eq(1).text();
		jQuery('#kebijakan-teks-isu').val(kebijakan_teks);
		jQuery('#isu-teks').val('');
	}

	function edit_visi(id_visi){
		jQuery('#modal-visi').modal('show');
		jQuery('#modal-visi').attr('data-id', id_visi);
		jQuery('#visi-teks').val(jQuery('tr[id-visi="'+id_visi+'"]').find('td').eq(1).text());
	}

	function edit_misi(id_misi){
		jQuery('#modal-misi').modal('show');
		jQuery('#modal-misi').attr('data-id', id_misi);
		var visi_teks = jQuery('tr[id-visi="'+jQuery('#nav-misi .tambah-data').attr('id-visi')+'"]').find('td').eq(1).text();
		jQuery('#visi-teks-misi').val(visi_teks);
		jQuery('#misi-teks').val(jQuery('tr[id-misi="'+id_misi+'"]').find('td').eq(1).text());
	}

	function edit_saspok(id_sasaran){
		jQuery('#modal-saspok').modal('show');
		jQuery('#modal-saspok').attr('data-id', id_sasaran);
		var visi_teks = jQuery('tr[id-visi="'+jQuery('#nav-misi .tambah-data').attr('id-visi')+'"]').find('td').eq(1).text();
		jQuery('#visi-teks-sasaran').val(visi_teks);
		var misi_teks = jQuery('tr[id-misi="'+jQuery('#nav-sasaran .tambah-data').attr('id-misi')+'"]').find('td').eq(1).text();
		jQuery('#misi-teks-sasaran').val(misi_teks);
		jQuery('#sasaran-teks').val(jQuery('tr[id-saspok="'+id_sasaran+'"]').find('td').eq(1).text());
	}

	function edit_kebijakan(id_kebijakan){
		jQuery('#modal-kebijakan').modal('show');
		jQuery('#modal-kebijakan').attr('data-id', id_kebijakan);
		var visi_teks = jQuery('tr[id-visi="'+jQuery('#nav-misi .tambah-data').attr('id-visi')+'"]').find('td').eq(1).text();
		jQuery('#visi-teks-kebijakan').val(visi_teks);
		var misi_teks = jQuery('tr[id-misi="'+jQuery('#nav-sasaran .tambah-data').attr('id-misi')+'"]').find('td').eq(1).text();
		jQuery('#misi-teks-kebijakan').val(misi_teks);
		var sasaran_teks = jQuery('tr[id-visi="'+jQuery('#nav-kebijakan .tambah-data').attr('id-saspok')+'"]').find('td').eq(1).text();
		jQuery('#sasaran-teks-kebijakan').val(sasaran_teks);
		jQuery('#kebijakan-teks').val(jQuery('tr[id-kebijakan="'+id_kebijakan+'"]').find('td').eq(1).text());
	}

	function edit_isu(id_isu){
		jQuery('#modal-isu').modal('show');
		jQuery('#modal-isu').attr('data-id', id_isu);
		var visi_teks = jQuery('tr[id-visi="'+jQuery('#nav-misi .tambah-data').attr('id-visi')+'"]').find('td').eq(1).text();
		jQuery('#visi-teks-isu').val(visi_teks);
		var misi_teks = jQuery('tr[id-misi="'+jQuery('#nav-sasaran .tambah-data').attr('id-misi')+'"]').find('td').eq(1).text();
		jQuery('#misi-teks-isu').val(misi_teks);
		var sasaran_teks = jQuery('tr[id-saspok="'+jQuery('#nav-kebijakan .tambah-data').attr('id-saspok')+'"]').find('td').eq(1).text();
		jQuery('#sasaran-teks-isu').val(sasaran_teks);
		var kebijakan_teks = jQuery('tr[id-kebijakan="'+jQuery('#nav-isu .tambah-data').attr('id-kebijakan')+'"]').find('td').eq(1).text();
		jQuery('#kebijakan-teks-isu').val(kebijakan_teks);
		jQuery('#isu-teks').val(jQuery('tr[id-isu="'+id_isu+'"]').find('td').eq(1).text());
	}

	function simpan_visi(){
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			var visi_teks = jQuery('#visi-teks').val();
			if(visi_teks == ''){
				return alert('Visi tidak boleh kosong!');
			}
			var id_visi = jQuery('#modal-visi').attr('data-id');
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpjpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpjpd_visi',
	          		"data": visi_teks,
	          		"id": id_visi
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						jQuery('#modal-visi').modal('hide');
						jQuery('#tambah-data').click();
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function simpan_misi(){
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			var misi_teks = jQuery('#misi-teks').val();
			if(misi_teks == ''){
				return alert('Misi tidak boleh kosong!');
			}
			var id_visi = jQuery('#nav-misi .tambah-data').attr('id-visi');
			if(id_visi == ''){
				return alert('ID visi tidak boleh kosong!');
			}
			var id_misi = jQuery('#modal-misi').attr('data-id');
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpjpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpjpd_misi',
	          		"data": misi_teks,
	          		"id_visi": id_visi,
	          		"id": id_misi
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						jQuery('#modal-misi').modal('hide');
						detail_visi(id_visi);
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function simpan_saspok(){
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			var sasaran_teks = jQuery('#sasaran-teks').val();
			if(sasaran_teks == ''){
				return alert('Sasaran tidak boleh kosong!');
			}
			var id_misi = jQuery('#nav-sasaran .tambah-data').attr('id-misi');
			if(id_misi == ''){
				return alert('ID misi tidak boleh kosong!');
			}
			var id_sasaran = jQuery('#modal-saspok').attr('data-id');
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpjpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpjpd_sasaran',
	          		"data": sasaran_teks,
	          		"id_misi": id_misi,
	          		"id": id_sasaran
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						jQuery('#modal-saspok').modal('hide');
						detail_misi(id_misi);
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function simpan_kebijakan(){
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			var kebijakan_teks = jQuery('#kebijakan-teks').val();
			if(kebijakan_teks == ''){
				return alert('Kebijakan tidak boleh kosong!');
			}
			var id_saspok = jQuery('#nav-kebijakan .tambah-data').attr('id-saspok');
			if(id_saspok == ''){
				return alert('ID sasaran tidak boleh kosong!');
			}
			var id_kebijakan = jQuery('#modal-kebijakan').attr('data-id');
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpjpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpjpd_kebijakan',
	          		"data": kebijakan_teks,
	          		"id_saspok": id_saspok,
	          		"id": id_kebijakan
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						jQuery('#modal-kebijakan').modal('hide');
						detail_saspok(id_saspok);
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function simpan_isu(){
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			var isu_teks = jQuery('#isu-teks').val();
			if(isu_teks == ''){
				return alert('Isu tidak boleh kosong!');
			}
			var id_kebijakan = jQuery('#nav-isu .tambah-data').attr('id-kebijakan');
			if(id_kebijakan == ''){
				return alert('ID kebijakan tidak boleh kosong!');
			}
			var id_isu = jQuery('#modal-isu').attr('data-id');
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpjpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpjpd_isu',
	          		"data": isu_teks,
	          		"id_kebijakan": id_kebijakan,
	          		"id": id_isu
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						jQuery('#modal-isu').modal('hide');
						detail_kebijakan(id_kebijakan);
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function hapus_visi(id_visi){
		if(confirm('Apakah anda yakin untuk menghapus data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "hapus_rpjpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpjpd_visi',
	          		"id": id_visi
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						jQuery('#tambah-data').click();
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function hapus_misi(id_misi){
		if(confirm('Apakah anda yakin untuk menghapus data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "hapus_rpjpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpjpd_misi',
	          		"id": id_misi
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						var id_visi = jQuery('#nav-misi .tambah-data').attr('id-visi');
						detail_visi(id_visi);
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function hapus_saspok(id_sasaran){
		if(confirm('Apakah anda yakin untuk menghapus data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "hapus_rpjpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpjpd_sasaran',
	          		"id": id_sasaran
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						var id_misi = jQuery('#nav-sasaran .tambah-data').attr('id-misi');
						detail_misi(id_misi);
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function hapus_kebijakan(id_kebijakan){
		if(confirm('Apakah anda yakin untuk menghapus data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "hapus_rpjpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpjpd_kebijakan',
	          		"id": id_kebijakan
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						var id_saspok = jQuery('#nav-kebijakan .tambah-data').attr('id-saspok');
						detail_saspok(id_saspok);
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function hapus_isu(id_isu){
		if(confirm('Apakah anda yakin untuk menghapus data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "hapus_rpjpd",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"table": 'data_rpjpd_isu',
	          		"id": id_isu
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
						var id_kebijakan = jQuery('#nav-isu .tambah-data').attr('id-kebijakan');
						detail_kebijakan(id_kebijakan);
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function detail_visi(id_visi){
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpjpd",
          		"api_key": "<?php echo $api_key; ?>",
          		"table": 'data_rpjpd_misi',
          		"id": id_visi
          	},
          	dataType: "json",
          	success: function(res){
          		console.log('res', res);
				jQuery('#wrap-loading').hide();
				var data_html = ""
					+'<button class="btn-sm btn-primary tambah-data" style="margin-top: 10px;" id-visi="'+id_visi+'" onclick="tambah_misi('+id_visi+');"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Misi</button>'
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<tbody>"
							+"<tr>"
								+"<th class='text-center' style='width: 160px;'>Visi</th>"
								+"<th>"+jQuery('tr[id-visi="'+id_visi+'"]').find('td').eq(1).text()+"</th>"
							+"</tr>"
						+"</tbody>"
					+"</table>"
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center' style='width: 45px;'>No</th>"
								+"<th class='text-center'>Misi</th>"
								+"<th class='text-center' style='width: 160px;'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody>";
				res.data.map(function(b, i){
					data_html += ''
					+'<tr id-misi="'+b.id+'">'
						+'<td class="text-center">'+(i+1)+'</td>'
						+'<td>'+b.misi_teks+'</td>'
						+'<td class="text-center aksi">'
							+'<button class="btn-sm btn-primary" onclick="detail_misi('+b.id+');"><i class="dashicons dashicons-search"></i></button>'
							+'<button class="btn-sm btn-warning" onclick="edit_misi('+b.id+');"><i class="dashicons dashicons-edit"></i></button>'
							+'<button class="btn-sm btn-danger" onclick="hapus_misi('+b.id+');"><i class="dashicons dashicons-trash"></i></button>'
						+'</td>'
					+'</tr>';
				});
				data_html += ""
						+"</tbody>";
					+"</table>";
				jQuery('#nav-misi').html(data_html)
          		jQuery('.nav-tabs a[href="#nav-misi"]').tab('show');
          	}
        });
	}

	function detail_misi(id_misi){
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpjpd",
          		"api_key": "<?php echo $api_key; ?>",
          		"table": 'data_rpjpd_sasaran',
          		"id": id_misi
          	},
          	dataType: "json",
          	success: function(res){
          		console.log('res', res);
				jQuery('#wrap-loading').hide();
				var data_html = ""
					+'<button class="btn-sm btn-primary tambah-data" style="margin-top: 10px;" id-misi="'+id_misi+'" onclick="tambah_saspok('+id_misi+');"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Sasaran</button>'
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<tbody>"
							+"<tr>"
								+"<th class='text-center' style='width: 160px;'>Visi</th>"
								+"<th>"+jQuery('tr[id-visi="'+jQuery('#nav-misi .tambah-data').attr('id-visi')+'"]').find('td').eq(1).text()+"</th>"
							+"</tr>"
							+"<tr>"
								+"<th class='text-center' style='width: 160px;'>Misi</th>"
								+"<th>"+jQuery('tr[id-misi="'+id_misi+'"]').find('td').eq(1).text()+"</th>"
							+"</tr>"
						+"</tbody>"
					+"</table>"
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center' style='width: 45px;'>No</th>"
								+"<th class='text-center'>Sasaran</th>"
								+"<th class='text-center' style='width: 160px;'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody>";
				res.data.map(function(b, i){
					data_html += ''
					+'<tr id-saspok="'+b.id+'">'
						+'<td class="text-center">'+(i+1)+'</td>'
						+'<td>'+b.saspok_teks+'</td>'
						+'<td class="text-center aksi">'
							+'<button class="btn-sm btn-primary" onclick="detail_saspok('+b.id+');"><i class="dashicons dashicons-search"></i></button>'
							+'<button class="btn-sm btn-warning" onclick="edit_saspok('+b.id+');"><i class="dashicons dashicons-edit"></i></button>'
							+'<button class="btn-sm btn-danger" onclick="hapus_saspok('+b.id+');"><i class="dashicons dashicons-trash"></i></button>'
						+'</td>'
					+'</tr>';
				});
				data_html += ""
						+"</tbody>";
					+"</table>";
				jQuery('#nav-sasaran').html(data_html)
          		jQuery('.nav-tabs a[href="#nav-sasaran"]').tab('show');
          	}
        });
	}

	function detail_saspok(id_saspok){
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpjpd",
          		"api_key": "<?php echo $api_key; ?>",
          		"table": 'data_rpjpd_kebijakan',
          		"id": id_saspok
          	},
          	dataType: "json",
          	success: function(res){
          		console.log('res', res);
				jQuery('#wrap-loading').hide();
				var data_html = ""
					+'<button class="btn-sm btn-primary tambah-data" style="margin-top: 10px;" id-saspok="'+id_saspok+'" onclick="tambah_kebijakan('+id_saspok+');"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Kebijakan</button>'
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<tbody>"
							+"<tr>"
								+"<th class='text-center' style='width: 160px;'>Visi</th>"
								+"<th>"+jQuery('tr[id-visi="'+jQuery('#nav-misi .tambah-data').attr('id-visi')+'"]').find('td').eq(1).text()+"</th>"
							+"</tr>"
							+"<tr>"
								+"<th class='text-center' style='width: 160px;'>Misi</th>"
								+"<th>"+jQuery('tr[id-misi="'+jQuery('#nav-sasaran .tambah-data').attr('id-misi')+'"]').find('td').eq(1).text()+"</th>"
							+"</tr>"
							+"<tr>"
								+"<th class='text-center' style='width: 160px;'>Sasaran</th>"
								+"<th>"+jQuery('tr[id-saspok="'+id_saspok+'"]').find('td').eq(1).text()+"</th>"
							+"</tr>"
						+"</tbody>"
					+"</table>"
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center' style='width: 45px;'>No</th>"
								+"<th class='text-center'>Kebijakan</th>"
								+"<th class='text-center' style='width: 160px;'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody>";
				res.data.map(function(b, i){
					data_html += ''
					+'<tr id-kebijakan="'+b.id+'">'
						+'<td class="text-center">'+(i+1)+'</td>'
						+'<td>'+b.kebijakan_teks+'</td>'
						+'<td class="text-center aksi">'
							+'<button class="btn-sm btn-primary" onclick="detail_kebijakan('+b.id+');"><i class="dashicons dashicons-search"></i></button>'
							+'<button class="btn-sm btn-warning" onclick="edit_kebijakan('+b.id+');"><i class="dashicons dashicons-edit"></i></button>'
							+'<button class="btn-sm btn-danger" onclick="hapus_kebijakan('+b.id+');"><i class="dashicons dashicons-trash"></i></button>'
						+'</td>'
					+'</tr>';
				});
				data_html += ""
						+"</tbody>";
					+"</table>";
				jQuery('#nav-kebijakan').html(data_html)
          		jQuery('.nav-tabs a[href="#nav-kebijakan"]').tab('show');
          	}
        });
	}

	function detail_kebijakan(id_kebijakan){
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpjpd",
          		"api_key": "<?php echo $api_key; ?>",
          		"table": 'data_rpjpd_isu',
          		"id": id_kebijakan
          	},
          	dataType: "json",
          	success: function(res){
          		console.log('res', res);
				jQuery('#wrap-loading').hide();
				var data_html = ""
					+'<button class="btn-sm btn-primary tambah-data" style="margin-top: 10px;" id-kebijakan="'+id_kebijakan+'" onclick="tambah_isu('+id_kebijakan+');"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Isu</button>'
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<tbody>"
							+"<tr>"
								+"<th class='text-center' style='width: 160px;'>Visi</th>"
								+"<th>"+jQuery('tr[id-visi="'+jQuery('#nav-misi .tambah-data').attr('id-visi')+'"]').find('td').eq(1).text()+"</th>"
							+"</tr>"
							+"<tr>"
								+"<th class='text-center' style='width: 160px;'>Misi</th>"
								+"<th>"+jQuery('tr[id-misi="'+jQuery('#nav-sasaran .tambah-data').attr('id-misi')+'"]').find('td').eq(1).text()+"</th>"
							+"</tr>"
							+"<tr>"
								+"<th class='text-center' style='width: 160px;'>Sasaran</th>"
								+"<th>"+jQuery('tr[id-saspok="'+jQuery('#nav-kebijakan .tambah-data').attr('id-saspok')+'"]').find('td').eq(1).text()+"</th>"
							+"</tr>"
							+"<tr>"
								+"<th class='text-center' style='width: 160px;'>Kebijakan</th>"
								+"<th>"+jQuery('tr[id-kebijakan="'+id_kebijakan+'"]').find('td').eq(1).text()+"</th>"
							+"</tr>"
						+"</tbody>"
					+"</table>"
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center' style='width: 45px;'>No</th>"
								+"<th class='text-center'>Kebijakan</th>"
								+"<th class='text-center' style='width: 160px;'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody>";
				res.data.map(function(b, i){
					data_html += ''
					+'<tr id-isu="'+b.id+'">'
						+'<td class="text-center">'+(i+1)+'</td>'
						+'<td>'+b.isu_teks+'</td>'
						+'<td class="text-center aksi">'
							+'<button class="btn-sm btn-warning" onclick="edit_isu('+b.id+');"><i class="dashicons dashicons-edit"></i></button>'
							+'<button class="btn-sm btn-danger" onclick="hapus_isu('+b.id+');"><i class="dashicons dashicons-trash"></i></button>'
						+'</td>'
					+'</tr>';
				});
				data_html += ""
						+"</tbody>";
					+"</table>";
				jQuery('#nav-isu').html(data_html)
          		jQuery('.nav-tabs a[href="#nav-isu"]').tab('show');
          	}
        });
	}
</script>