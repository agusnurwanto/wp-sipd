
<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.css" rel="stylesheet">

<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
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

$jadwal_lokal = $wpdb->get_results("SELECT * from data_jadwal_lokal where id_jadwal_lokal = (select max(id_jadwal_lokal) from data_jadwal_lokal where id_tipe=2)", ARRAY_A);
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
$pengaturan = $wpdb->get_results($wpdb->prepare("
	select 
		* 
	from data_pengaturan_sipd 
	where tahun_anggaran=%d
", $tahun_anggaran), ARRAY_A);

$awal_rpjmd = 2018;
$akhir_rpjmd = 2023;
if(!empty($pengaturan)){
	$awal_rpjmd = $pengaturan[0]['awal_rpjmd'];
	$akhir_rpjmd = $pengaturan[0]['akhir_rpjmd'];
}
$urut = $tahun_anggaran-$awal_rpjmd;
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
$tujuan_ids = array();
$sasaran_ids = array();
$program_ids = array();
$skpd_filter = array();

$sql = "
	select 
		* 
	from data_rpjmd_visi_lokal
";
$visi_all = $wpdb->get_results($sql, ARRAY_A);

foreach ($visi_all as $visi) {
	if(empty($data_all['data'][$visi['id']])){
		$data_all['data'][$visi['id']] = array(
			'nama' => $visi['visi_teks'],
			'data' => array()
		);
	}

	$visi_ids[$visi['id']] = "'".$visi['id']."'";
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_misi_lokal
		where id_visi=%s
	", $visi['id']);
	$misi_all = $wpdb->get_results($sql, ARRAY_A);

	foreach ($misi_all as $misi) {
		if(empty($data_all['data'][$visi['id']]['data'][$misi['id']])){
			$data_all['data'][$visi['id']]['data'][$misi['id']] = array(
				'nama' => $misi['misi_teks'],
				'data' => array()
			);
		}

		$misi_ids[$misi['id']] = "'".$misi['id']."'";
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjmd_tujuan_lokal
			where id_misi=%s
		", $misi['id']);
		$tujuan_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($tujuan_all as $tujuan) {
			if(empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']])){
				$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']] = array(
					'nama' => $tujuan['tujuan_teks'],
					'detail' => array(),
					'data' => array()
				);
			}
			$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['detail'][] = $tujuan;

			$tujuan_ids[$tujuan['id_unik']] = "'".$tujuan['id_unik']."'";
			$sql = $wpdb->prepare("
				select 
					* 
				from data_rpjmd_sasaran_lokal
				where kode_tujuan=%s
			", $tujuan['id_unik']);
			$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
			foreach ($sasaran_all as $sasaran) {
				if(empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
					$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
						'nama' => $sasaran['sasaran_teks'],
						'detail' => array(),
						'data' => array()
					);
				}
				$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;

				$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
				$sql = $wpdb->prepare("
					select 
						* 
					from data_rpjmd_program_lokal
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
					if(empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
						$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
							'nama' => $program['nama_program'],
							'kode_skpd' => $program['kode_skpd'],
							'nama_skpd' => $program['nama_skpd'],
							'data' => array()
						);
					}
					if(empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
						$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
							'nama' => $program['indikator'],
							'data' => $program
						);
					}
				}
			}
		}
	}
}

// buat array data kosong
if(empty($data_all['data']['visi_kosong'])){
	$data_all['data']['visi_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'data' => array()
	);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong'])){
	$data_all['data']['visi_kosong']['data']['misi_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'data' => array()
	);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong'])){
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'detail' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong'])){
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong'] = array(
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
		from data_rpjmd_misi_lokal
		where id not in (".implode(',', $misi_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpjmd_misi_lokal
	";
}
$misi_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($misi_all_kosong as $misi) {
	if(empty($data_all['data']['visi_kosong']['data'][$misi['id']])){
		$data_all['data']['visi_kosong']['data'][$misi['id']]['data'] = array(
			'nama' => $misi['misi_teks'],
			'data' => array()
		);
	}
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_tujuan_lokal
		where id=%s
	", $misi['id']);
	$tujuan_all_kosong = $wpdb->get_results($sql, ARRAY_A);
	foreach ($tujuan_all_kosong as $tujuan) {
		$tujuan_ids[$tujuan['id_unik']] = "'".$tujuan['id_unik']."'";
		if(empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']])){
			$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']] = array(
				'nama' => $tujuan['sasaran_teks'],
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['detail'][] = $tujuan;
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjmd_sasaran_lokal
			where kode_tujuan=%s
		", $tujuan['id_unik']);
		$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($sasaran_all as $sasaran) {
			$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
			if(empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
				$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
					'nama' => $sasaran['sasaran_teks'],
					'detail' => array(),
					'data' => array()
				);
			}
			$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
			$sql = $wpdb->prepare("
				select 
					* 
				from data_rpjmd_program_lokal
				where kode_sasaran=%s
			", $sasaran['id_unik']);
			$program_all = $wpdb->get_results($sql, ARRAY_A);
			foreach ($program_all as $program) {
				$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
				if(empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
					$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
						'nama' => $program['nama_program'],
						'kode_skpd' => $program['kode_skpd'],
						'nama_skpd' => $program['nama_skpd'],
						'data' => array()
					);
				}
				if(empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
					$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
						'nama' => $program['indikator'],
						'data' => array()
					);
				}
			}
		}
	}
}

// select tujuan yang belum terselect
if(!empty($tujuan_ids)){
	$sql = "
		select 
			* 
		from data_rpjmd_tujuan_lokal
		where id_unik not in (".implode(',', $tujuan_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpjmd_tujuan_lokal
	";
}
$tujuan_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($tujuan_all_kosong as $tujuan) {
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']] = array(
			'nama' => $tujuan['tujuan_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['detail'][] = $tujuan;
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_sasaran_lokal
		where kode_tujuan=%s
	", $tujuan['id_unik']);
	$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($sasaran_all as $sasaran) {
		$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
		if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
				'nama' => $sasaran['sasaran_teks'],
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjmd_program_lokal
			where kode_sasaran=%s
		", $sasaran['id_unik']);
		$program_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($program_all as $program) {
			$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
			if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
				$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
					'nama' => $program['nama_program'],
					'kode_skpd' => $program['kode_skpd'],
					'nama_skpd' => $program['nama_skpd'],
					'data' => array()
				);
			}
			if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
				$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
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
		from data_rpjmd_sasaran_lokal
		where id_unik not in (".implode(',', $sasaran_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpjmd_sasaran_lokal
	";
}
$sasaran_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($sasaran_all_kosong as $sasaran) {
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']] = array(
			'nama' => $sasaran['sasaran_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_program_lokal
		where kode_sasaran=%s
	", $sasaran['id_unik']);
	$program_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($program_all as $program) {
		$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
		if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
				'nama' => $program['nama_program'],
				'kode_skpd' => $program['kode_skpd'],
				'nama_skpd' => $program['nama_skpd'],
				'data' => array()
			);
		}
		if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
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
		from data_rpjmd_program_lokal
		where id_unik not in (".implode(',', $program_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpjmd_program_lokal
	";
}
$program_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($program_all as $program) {
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']] = array(
			'nama' => $program['nama_program'],
			'kode_skpd' => $program['kode_skpd'],
			'nama_skpd' => $program['nama_skpd'],
			'data' => array()
		);
	}
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
			'nama' => $program['indikator'],
			'data' => array()
		);
	}
}

// hapus array jika data dengan key kosong tidak ada datanya
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'])){
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'])){
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'])){
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']);
}
if(empty($data_all['data']['visi_kosong']['data'])){
	unset($data_all['data']['visi_kosong']);
}

// echo '<pre>';
// print_r($data_all['data']);
// echo '</pre>';
// die();

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
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
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
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
			</tr>
		';
		$no_tujuan = 0;
		foreach ($misi['data'] as $tujuan) {
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
					<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'.'.$no_tujuan.'</td>
					<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
					<td class="atas kanan bawah"><span class="debug-misi">'.$misi['nama'].'</span></td>
					<td class="atas kanan bawah">'.parsing_nama_kode($tujuan['nama']).$indikator_tujuan.'</td>
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
						<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'.'.$no_tujuan.'.'.$no_sasaran.'</td>
						<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
						<td class="atas kanan bawah"><span class="debug-misi">'.$misi['nama'].'</span></td>
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
							<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'.'.$no_tujuan.'.'.$no_sasaran.'.'.$no_program.'</td>
							<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
							<td class="atas kanan bawah"><span class="debug-misi">'.$misi['nama'].'</span></td>
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
	}
}

ksort($skpd_filter);
$skpd_filter_html = '<option value="">Pilih SKPD</option>';
foreach ($skpd_filter as $kode_skpd => $nama_skpd) {
	$skpd_filter_html .= '<option value="'.$kode_skpd.'">'.$kode_skpd.' '.$nama_skpd.'</option>';
}

$tahun_selesai = (!empty($tahun_anggaran)) ? $tahun_anggaran + 5 : '-';
?>
<style type="text/css">
	.debug-visi, .debug-misi, .debug-tujuan, .debug-sasaran, .debug-kode { display: none; }
	.indikator_program { min-height: 40px; }
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitoring dan Evaluasi RPJMD (Rencana Pembangunan Jangka Menengah Daerah) <br><?php echo $judul_skpd.'Tahun '.$tahun_anggaran.' - '.$tahun_selesai.' '.$nama_pemda; ?></h4>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; height: 80vh;">
	<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<tr>
				<th style="width: 85px;" class="atas kiri kanan bawah text_tengah text_blok">No</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Visi</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Misi</th>
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
				<th class='atas kanan bawah text_tengah text_blok'>14</th>
				<th class='atas kanan bawah text_tengah text_blok'>15</th>
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
                <h4 style="margin: 0;" class="modal-title" id="">Data RPJM</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<nav>
				  	<div class="nav nav-tabs" id="nav-tab" role="tablist">
					    <a class="nav-item nav-link active" id="nav-visi-tab" data-toggle="tab" href="#nav-visi" role="tab" aria-controls="nav-visi" aria-selected="true">Visi</a>
					    <a class="nav-item nav-link" id="nav-misi-tab" data-toggle="tab" href="#nav-misi" role="tab" aria-controls="nav-misi" aria-selected="false">Misi</a>
					    <a class="nav-item nav-link" id="nav-tujuan-tab" data-toggle="tab" href="#nav-tujuan" role="tab" aria-controls="nav-tujuan" aria-selected="false">Tujuan</a>
					    <a class="nav-item nav-link" id="nav-sasaran-tab" data-toggle="tab" href="#nav-sasaran" role="tab" aria-controls="nav-sasaran" aria-selected="false">Sasaran</a>
					    <a class="nav-item nav-link" id="nav-program-tab" data-toggle="tab" href="#nav-program" role="tab" aria-controls="nav-program" aria-selected="false">Program</a>
				  	</div>
				</nav>
				<div class="tab-content" id="nav-tabContent">
				  	<div class="tab-pane fade show active" id="nav-visi" role="tabpanel" aria-labelledby="nav-visi-tab"></div>
				  	<div class="tab-pane fade" id="nav-misi" role="tabpanel" aria-labelledby="nav-misi-tab"></div>
				  	<div class="tab-pane fade" id="nav-tujuan" role="tabpanel" aria-labelledby="nav-tujuan-tab"></div>
				  	<div class="tab-pane fade" id="nav-sasaran" role="tabpanel" aria-labelledby="nav-sasaran-tab"></div>
				  	<div class="tab-pane fade" id="nav-program" role="tabpanel" aria-labelledby="nav-program-tab"></div>
				</div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<!-- Modal indikator rpjmd -->
<div class="modal fade" id="modal-indikator-rpjm" tabindex="-1" role="dialog" aria-labelledby="modal-indikator-rpjm-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<!-- Modal crud rpjmd -->
<div class="modal fade" id="modal-crud-rpjm" tabindex="-2" role="dialog" aria-labelledby="modal-crud-rpjm-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.js"></script>

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
		+'<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RPJM</a>'
		+'<h3 style="margin-top: 20px;">SETTING</h3>'
		+'<label><input type="checkbox" onclick="tampilkan_edit(this);"> Edit Data RPJM</label>'
		+'<label style="margin-left: 20px;"><input type="checkbox" onclick="show_debug(this);"> Debug Cascading RPJM</label>'
		+'<label style="margin-left: 20px;">'
			+'Sembunyikan Baris '
			+'<select id="sembunyikan-baris" onchange="sembunyikan_baris(this);" style="padding: 5px 10px; min-width: 200px;">'
				+'<option value="">Pilih Baris</option>'
				+'<option value="tr-misi">Misi</option>'
				+'<option value="tr-tujuan">Tujuan</option>'
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
		var tr_misi = jQuery('.tr-misi');
		var tr_tujuan = jQuery('.tr-tujuan');
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
	          		"action": "singkron_rpjmd_sipd_lokal",
	          		"api_key": "<?php echo $api_key; ?>",
	      			"tahun_anggaran": <?php echo $tahun_anggaran; ?>,
	          		"user": "<?php echo $current_user->display_name; ?>"
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
	          	}
	        });
		}
	});

	jQuery('#tambah-data').on('click', function(){
        visiRpjm();
	});

	jQuery(document).on('click', '.btn-tambah-visi', function(){
		let visiModal = jQuery("#modal-crud-rpjm");
		let html = '<form id="form-rpjm">'
						+'<textarea class="form-class" name="visi_teks"></textarea>'
					+'</form>';

		visiModal.find('.modal-title').html('Tambah Visi');
		visiModal.find('.modal-body').html(html);
		visiModal.find('.modal-footer').html(''
			+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
				+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
			+'</button>'
			+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
				+'data-action="submit_visi_rpjm" '
				+'data-view="visiRpjm"'
			+'>'
				+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
			+'</button>');
		visiModal.modal('show');
	});

	jQuery(document).on('click', '.btn-edit-visi', function(){
		jQuery('#wrap-loading').show();

		let visiModal = jQuery("#modal-crud-rpjm");

		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action': 'edit_visi_rpjm',
	          	'api_key': '<?php echo $api_key; ?>',
				'id': jQuery(this).data('id')
			},
			success:function(response){

				jQuery('#wrap-loading').hide();

				let html = '<form id="form-rpjm">'
								+'<input type="hidden" name="id_visi" value="'+response.data.id+'">'
								+'<textarea class="form-class" name="visi_teks">'+response.data.visi_teks+'</textarea>'
							+'</form>';

				visiModal.find('.modal-title').html('Edit Visi');
				visiModal.find('.modal-body').html(html);
				visiModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
						+'data-action="update_visi_rpjm" '
						+'data-view="visiRpjm"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
					+'</button>');
				visiModal.modal('show');
			}
		})
	});

	jQuery(document).on('click', '.btn-hapus-visi', function(){
		
		if(confirm('Data akan dihapus, lanjut?')){

	        jQuery('#wrap-loading').show();

			let id_visi = jQuery(this).data('id');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action':'delete_visi_rpjm',
					'api_key':'<?php echo $api_key; ?>',
					'id_visi':id_visi
				},
				success:function(response){
					if(response.status){
						visiRpjm();
					}
					alert(response.message);
					jQuery('#wrap-loading').hide();
				}
			})
		}
	});

	jQuery(document).on('click', '.btn-detail-visi', function(){
		misiRpjm({'id_visi':jQuery(this).data('id')});
	});

	jQuery(document).on('click', '.btn-tambah-misi', function(){

		let misiModal = jQuery("#modal-crud-rpjm");
		let id_visi = jQuery(this).data('idvisi');
		let html = '<form id="form-rpjm">'
						+'<input type="hidden" name="id_visi" value="'+id_visi+'">'
						+'<div class="form-group">'
							+'<label for="misi">Misi</label>'
	  						+'<textarea class="form-control" name="misi_teks"></textarea>'
						+'</div>'
						+'<div class="form-group">'
							+'<label for="misi">Urut Misi</label>'
	  						+'<input type="number" class="form-control" name="urut_misi"/>'
						+'</div>'
					+'</form>';

		misiModal.find('.modal-title').html('Tambah Misi');
		misiModal.find('.modal-body').html(html);
		misiModal.find('.modal-footer').html(''
			+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
				+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
			+'</button>'
			+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
				+'data-action="submit_misi_rpjm" '
				+'data-view="misiRpjm"'
			+'>'
				+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
			+'</button>');
		misiModal.modal('show');
	});

	jQuery(document).on('click', '.btn-edit-misi', function(){
		
		jQuery('#wrap-loading').show();
		
		let misiModal = jQuery("#modal-crud-rpjm");
		
		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action': 'edit_misi_rpjm',
	          	'api_key': '<?php echo $api_key; ?>',
				'id': jQuery(this).data('id')
			},
			success:function(response){

				jQuery('#wrap-loading').hide();

				let html = '<form id="form-rpjm">'
								+'<input type="hidden" name="id_misi" value="'+response.misi.id+'" />'
								+'<input type="hidden" name="id_visi" value="'+response.misi.id_visi+'" />'
								+'<div class="form-group">'
									+'<label for="misi">Misi</label>'
	  								+'<textarea class="form-control" name="misi_teks">'+response.misi.misi_teks+'</textarea>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="misi">Urut Misi</label>'
	  								+'<input type="number" class="form-control" name="urut_misi" value="'+response.misi.urut_misi+'"/>'
								+'</div>'
							+'</form>';

		        misiModal.find('.modal-title').html('Edit Misi');
				misiModal.find('.modal-body').html(html);
				misiModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
						+'data-action="update_misi_rpjm" '
						+'data-view="misiRpjm"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
					+'</button>');
				misiModal.modal('show');
			}
		})
	});

	jQuery(document).on('click', '.btn-hapus-misi', function(){
		
		if(confirm('Data akan dihapus, lanjut?')){

	        jQuery('#wrap-loading').show();

			let id_misi = jQuery(this).data('id');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action':'delete_misi_rpjm',
					'api_key':'<?php echo $api_key; ?>',
					'id_misi':id_misi
				},
				success:function(response){
					alert(response.message);
					if(response.status){
						misiRpjm({
							'id_visi': id_visi
						});
					}
					jQuery('#wrap-loading').hide();
				}
			})
		}
	});

	jQuery(document).on('click', '.btn-detail-misi', function(){
		tujuanRpjm({'id_misi':jQuery(this).data('id')});
	});

	jQuery(document).on('click', '.btn-tambah-tujuan', function(){

		let tujuanModal = jQuery("#modal-crud-rpjm");
		let id_misi = jQuery(this).data('idmisi');
		let html = '<form id="form-rpjm">'
						+'<input type="hidden" name="id_misi" value="'+id_misi+'">'
						+'<div class="form-group">'
							+'<label for="tujuan">Tujuan</label>'
		  					+'<textarea class="form-control" name="tujuan_teks"></textarea>'
						+'</div>'
						+'<div class="form-group">'
							+'<label for="urut_tujuan">Urut Tujuan</label>'
	  						+'<input type="number" class="form-control" name="urut_tujuan"/>'
						+'</div>'
					+'</form>';

		tujuanModal.find('.modal-title').html('Tambah Tujuan');
		tujuanModal.find('.modal-body').html(html);
		tujuanModal.find('.modal-footer').html(''
			+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
				+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
			+'</button>'
			+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
				+'data-action="submit_tujuan_rpjm" '
				+'data-view="tujuanRpjm"'
			+'>'
				+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
			+'</button>');
		tujuanModal.find('.modal-dialog').css('maxWidth','950px');
		tujuanModal.find('.modal-dialog').css('width','100%');
		tujuanModal.modal('show');
	});

	jQuery(document).on('click', '.btn-edit-tujuan', function(){
		jQuery('#wrap-loading').show();

		let tujuanModal = jQuery("#modal-crud-rpjm");
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_tujuan_rpjm",
          		"api_key": "<?php echo $api_key; ?>",
				'id_tujuan': jQuery(this).data('idtujuan')
          	},
          	dataType: "json",
          	success: function(response){
          		jQuery('#wrap-loading').hide();
				let html = '<form id="form-rpjm">'
								+'<input type="hidden" name="id_tujuan" value="'+response.tujuan.id+'" />'
								+'<input type="hidden" name="id_misi" value="'+response.tujuan.id_misi+'" />'
								+'<div class="form-group">'
									+'<label for="tujuan">Tujuan</label>'
	  								+'<textarea class="form-control" name="tujuan_teks">'+response.tujuan.tujuan_teks+'</textarea>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="urut_tujuan">Urut Tujuan</label>'
	  								+'<input type="number" class="form-control" name="urut_tujuan" value="'+response.tujuan.urut_tujuan+'" />'
								+'</div>'
							+'</form>';

		        tujuanModal.find('.modal-title').html('Edit Tujuan');
				tujuanModal.find('.modal-body').html(html);
				tujuanModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
						+'data-action="update_tujuan_rpjm" '
						+'data-view="tujuanRpjm"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
					+'</button>');
				tujuanModal.find('.modal-dialog').css('maxWidth','950px');
				tujuanModal.find('.modal-dialog').css('width','100%');
				tujuanModal.modal('show');
          	}
        });
	});

	jQuery(document).on('click', '.btn-hapus-tujuan', function(){
		
		if(confirm('Data akan dihapus, lanjut?')){

	        jQuery('#wrap-loading').show();

			let id_tujuan = jQuery(this).data('idtujuan');

			let kode_tujuan = jQuery(this).data('kodetujuan');
			
			let id_misi = jQuery(this).data('idmisi');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action':'delete_tujuan_rpjm',
					'api_key':'<?php echo $api_key; ?>',
					'id_tujuan':id_tujuan,
					'kode_tujuan':kode_tujuan,
				},
				success:function(response){
					alert(response.message);
					if(response.status){
						tujuanRpjm({
							'id_misi': id_misi
						});
					}
					jQuery('#wrap-loading').hide();
				}
			})
		}
	});

	jQuery(document).on('click', '.btn-kelola-indikator-tujuan', function(){
        jQuery("#modal-indikator-rpjm").find('.modal-body').html('');
		indikatorTujuanRpjm({'id_tujuan':jQuery(this).data('idtujuan')});
	});

	jQuery(document).on('click', '.btn-add-indikator-tujuan', function(){

		let indikatorTujuanModal = jQuery("#modal-crud-rpjm");
		let id_tujuan = jQuery(this).data('idtujuan');
		let html = '<form id="form-rpjm">'
					+'<input type="hidden" name="id_tujuan" value="'+id_tujuan+'">'
					+'<div class="form-group">'
						+'<label for="indikator_teks">Indikator</label>'
		  				+'<textarea class="form-control" name="indikator_teks"></textarea>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="satuan">Satuan</label>'
		  				+'<input type="text" class="form-control" name="satuan"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_1">Target tahun ke-1</label>'
		  				+'<input type="text" class="form-control" name="target_1"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_2">Target tahun ke-2</label>'
		  				+'<input type="text" class="form-control" name="target_2"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_3">Target tahun ke-3</label>'
		  				+'<input type="text" class="form-control" name="target_3"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_4">Target tahun ke-4</label>'
		  				+'<input type="text" class="form-control" name="target_4"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_5">Target tahun ke-5</label>'
		  				+'<input type="text" class="form-control" name="target_5"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_awal">Target awal</label>'
		  				+'<input type="text" class="form-control" name="target_awal"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_akhir">Target akhir</label>'
		  				+'<input type="text" class="form-control" name="target_akhir"/>'
					+'</div>'
					+'</form>';

			indikatorTujuanModal.find('.modal-title').html('Tambah Indikator');
			indikatorTujuanModal.find('.modal-body').html(html);
			indikatorTujuanModal.find('.modal-footer').html(''
				+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
					+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
				+'</button>'
				+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
					+'data-action="submit_indikator_tujuan_rpjm" '
					+'data-view="indikatorTujuanRpjm"'
				+'>'
					+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
				+'</button>');
			indikatorTujuanModal.modal('show');
	});

	jQuery(document).on('click', '.btn-edit-indikator-tujuan', function(){

		jQuery('#wrap-loading').show();

		let indikatorTujuanModal = jQuery("#modal-crud-rpjm");

		let id = jQuery(this).data('id');

		let id_tujuan = jQuery(this).data('idtujuan');

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_indikator_tujuan_rpjm",
          		"api_key": "<?php echo $api_key; ?>",
				'id': id,
				'id_tujuan': id_tujuan
          	},
          	dataType: "json",
          	success: function(response){
          		jQuery('#wrap-loading').hide();

          		let html = '<form id="form-rpjm">'
					+'<input type="hidden" name="id" value="'+id+'">'
					+'<input type="hidden" name="id_tujuan" value="'+id_tujuan+'">'
					+'<div class="form-group">'
						+'<label for="indikator_teks">Indikator</label>'
	  					+'<textarea class="form-control" name="indikator_teks">'+response.data.indikator_teks+'</textarea>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="satuan">Satuan</label>'
	  					+'<input type="text" class="form-control" name="satuan" value="'+response.data.satuan+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_1">Target tahun ke-1</label>'
	  					+'<input type="text" class="form-control" name="target_1" value="'+response.data.target_1+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_2">Target tahun ke-2</label>'
	  					+'<input type="text" class="form-control" name="target_2" value="'+response.data.target_2+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_3">Target tahun ke-3</label>'
	  					+'<input type="text" class="form-control" name="target_3" value="'+response.data.target_3+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_4">Target tahun ke-4</label>'
	  					+'<input type="text" class="form-control" name="target_4" value="'+response.data.target_4+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_5">Target tahun ke-5</label>'
	  					+'<input type="text" class="form-control" name="target_5" value="'+response.data.target_5+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_awal">Target awal</label>'
	  					+'<input type="text" class="form-control" name="target_awal" value="'+response.data.target_awal+'"/>'
					+'</div>'
					+'<div class="form-group">'
					+'<label for="target_akhir">Target akhir</label>'
	  					+'<input type="text" class="form-control" name="target_akhir" value="'+response.data.target_akhir+'"/>'
					+'</div>'
				  +'</form>';

				indikatorTujuanModal.find('.modal-title').html('Edit Indikator Tujuan');
				indikatorTujuanModal.find('.modal-body').html(html);
				indikatorTujuanModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
						+'data-action="update_indikator_tujuan_rpjm" '
						+'data-view="indikatorTujuanRpjm"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
					+'</button>');
				indikatorTujuanModal.modal('show');
          	}
		})			
	});

	jQuery(document).on('click', '.btn-delete-indikator-tujuan', function(){

		if(confirm('Data akan dihapus, lanjut?')){
			jQuery('#wrap-loading').show();
			
			let id = jQuery(this).data('id');
			
			let id_tujuan = jQuery(this).data('idtujuan');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_indikator_tujuan_rpjm',
		          	'api_key': '<?php echo $api_key; ?>',
					'id': id,
					'id_tujuan': id_tujuan,
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						indikatorTujuanRpjm({
							'id_tujuan': id_tujuan
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '.btn-detail-tujuan', function(){
		sasaranRpjm({
			'kode_tujuan':jQuery(this).data('kode')
		});
	});

	jQuery(document).on('click', '.btn-tambah-sasaran', function(){
		
		let sasaranModal = jQuery("#modal-crud-rpjm");
		let id_tujuan = jQuery(this).data('idtujuan');
		let kode_tujuan = jQuery(this).data('kodetujuan');
		let html = '<form id="form-rpjm">'
						+'<input type="hidden" name="kode_tujuan" value="'+kode_tujuan+'">'
						+'<div class="form-group">'
							+'<label for="sasaran">Sasaran</label>'
	  						+'<textarea class="form-control" name="sasaran_teks"></textarea>'
						+'</div>'
						+'<div class="form-group">'
							+'<label for="urut_sasaran">Urut Sasaran</label>'
	  						+'<input type="number" class="form-control" name="urut_sasaran"/>'
						+'</div>'
					+'</form>';

		sasaranModal.find('.modal-title').html('Tambah Sasaran');
		sasaranModal.find('.modal-body').html(html);
		sasaranModal.find('.modal-footer').html(''
			+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
				+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
			+'</button>'
			+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
				+'data-action="submit_sasaran_rpjm" '
				+'data-view="sasaranRpjm"'
			+'>'
				+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
			+'</button>');
		sasaranModal.modal('show');
	});

	jQuery(document).on('click', '.btn-edit-sasaran', function(){

		jQuery('#wrap-loading').show();

		let sasaranModal = jQuery("#modal-crud-rpjm");
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_sasaran_rpjm",
          		"api_key": "<?php echo $api_key; ?>",
				'id_sasaran': jQuery(this).data('idsasaran')
          	},
          	dataType: "json",
          	success: function(response){
				
				jQuery('#wrap-loading').hide();
				let html = '<form id="form-rpjm">'
								+'<input type="hidden" name="kode_tujuan" value="'+response.data.kode_tujuan+'" />'
								+'<input type="hidden" name="id_sasaran" value="'+response.data.id+'" />'
								+'<div class="form-group">'
									+'<label for="sasaran">Sasaran</label>'
	  								+'<textarea class="form-control" name="sasaran_teks">'+response.data.sasaran_teks+'</textarea>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="urut_sasaran">Urut Sasaran</label>'
	  								+'<input type="number" class="form-control" name="urut_sasaran" value="'+response.data.urut_sasaran+'"/>'
								+'</div>'
							+'</form>';

		        sasaranModal.find('.modal-title').html('Edit Sasaran');
				sasaranModal.find('.modal-body').html(html);
				sasaranModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
						+'data-action="update_sasaran_rpjm" '
						+'data-view="sasaranRpjm"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
					+'</button>');
				sasaranModal.modal('show');
          	}
        });
	});

	jQuery(document).on('click', '.btn-hapus-sasaran', function(){

		if(confirm('Data akan dihapus, lanjut?')){
			
			jQuery('#wrap-loading').show();
			let kode_tujuan = jQuery(this).data('kodetujuan');
			let id_sasaran = jQuery(this).data('idsasaran');
			let kode_sasaran = jQuery(this).data('kodesasaran');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_sasaran_rpjm',
		          	'api_key': '<?php echo $api_key; ?>',
					'id_sasaran': id_sasaran,
					'kode_sasaran': kode_sasaran
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						sasaranRpjm({
							'kode_tujuan': kode_tujuan
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '.btn-kelola-indikator-sasaran', function(){
		jQuery("#modal-indikator-rpjm").find('.modal-body').html('');
		indikatorSasaranRpjm({'id_sasaran':jQuery(this).data('idsasaran')});
	});

	jQuery(document).on('click', '.btn-add-indikator-sasaran', function(){

		let indikatorSasaranModal = jQuery("#modal-crud-rpjm");
		let id_sasaran = jQuery(this).data('idsasaran');
		let html = '<form id="form-rpjm">'
					+'<input type="hidden" name="id_sasaran" value="'+id_sasaran+'">'
					+'<div class="form-group">'
						+'<label for="indikator_teks">Indikator</label>'
		  				+'<textarea class="form-control" name="indikator_teks"></textarea>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="satuan">Satuan</label>'
		  				+'<input type="text" class="form-control" name="satuan"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_1">Target tahun ke-1</label>'
		  				+'<input type="text" class="form-control" name="target_1"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_2">Target tahun ke-2</label>'
		  				+'<input type="text" class="form-control" name="target_2"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_3">Target tahun ke-3</label>'
		  				+'<input type="text" class="form-control" name="target_3"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_4">Target tahun ke-4</label>'
		  				+'<input type="text" class="form-control" name="target_4"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_5">Target tahun ke-5</label>'
		  				+'<input type="text" class="form-control" name="target_5"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_awal">Target awal</label>'
		  				+'<input type="text" class="form-control" name="target_awal"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_akhir">Target akhir</label>'
		  				+'<input type="text" class="form-control" name="target_akhir"/>'
					+'</div>'
					+'</form>';

			indikatorSasaranModal.find('.modal-title').html('Tambah Indikator');
			indikatorSasaranModal.find('.modal-body').html(html);
			indikatorSasaranModal.find('.modal-footer').html(''
				+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
					+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
				+'</button>'
				+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
					+'data-action="submit_indikator_sasaran_rpjm" '
					+'data-view="indikatorSasaranRpjm"'
				+'>'
					+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
				+'</button>');
			indikatorSasaranModal.modal('show');
	});

	jQuery(document).on('click', '.btn-edit-indikator-sasaran', function(){

		jQuery('#wrap-loading').show();

		let id = jQuery(this).data('id');
		let id_sasaran = jQuery(this).data('idsasaran');
		let indikatorSasaranModal = jQuery("#modal-crud-rpjm");

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_indikator_sasaran_rpjm",
          		"api_key": "<?php echo $api_key; ?>",
				'id': id,
				'id_sasaran': id_sasaran
          	},
          	dataType: "json",
          	success: function(response){
          		jQuery('#wrap-loading').hide();

          		let html = '<form id="form-rpjm">'
					+'<input type="hidden" name="id" value="'+id+'">'
					+'<input type="hidden" name="id_sasaran" value="'+id_sasaran+'">'
					+'<div class="form-group">'
						+'<label for="indikator_teks">Indikator</label>'
	  					+'<textarea class="form-control" name="indikator_teks">'+response.data.indikator_teks+'</textarea>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="satuan">Satuan</label>'
	  					+'<input type="text" class="form-control" name="satuan" value="'+response.data.satuan+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_1">Target tahun ke-1</label>'
	  					+'<input type="text" class="form-control" name="target_1" value="'+response.data.target_1+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_2">Target tahun ke-2</label>'
	  					+'<input type="text" class="form-control" name="target_2" value="'+response.data.target_2+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_3">Target tahun ke-3</label>'
	  					+'<input type="text" class="form-control" name="target_3" value="'+response.data.target_3+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_4">Target tahun ke-4</label>'
	  					+'<input type="text" class="form-control" name="target_4" value="'+response.data.target_4+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_5">Target tahun ke-5</label>'
	  					+'<input type="text" class="form-control" name="target_5" value="'+response.data.target_5+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_awal">Target awal</label>'
	  					+'<input type="text" class="form-control" name="target_awal" value="'+response.data.target_awal+'"/>'
					+'</div>'
					+'<div class="form-group">'
					+'<label for="target_akhir">Target akhir</label>'
	  					+'<input type="text" class="form-control" name="target_akhir" value="'+response.data.target_akhir+'"/>'
					+'</div>'
				  +'</form>';

				indikatorSasaranModal.find('.modal-title').html('Edit Indikator Sasaran');
				indikatorSasaranModal.find('.modal-body').html(html);
				indikatorSasaranModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
						+'data-action="update_indikator_sasaran_rpjm" '
						+'data-view="indikatorSasaranRpjm"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
					+'</button>');
				indikatorSasaranModal.modal('show');
          	}
		})			
	});

	jQuery(document).on('click', '.btn-delete-indikator-sasaran', function(){

		if(confirm('Data akan dihapus, lanjut?')){
	
			jQuery('#wrap-loading').show();

			let id = jQuery(this).data('id');
			let id_sasaran = jQuery(this).data('idsasaran');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_indikator_sasaran_rpjm',
		          	'api_key': '<?php echo $api_key; ?>',
					'id': id,
					'id_sasaran': id_sasaran,
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						indikatorSasaranRpjm({
							'id_sasaran': id_sasaran
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '.btn-detail-sasaran', function(){
		programRpjm({
			'kode_sasaran':jQuery(this).data('kodesasaran')
		});
	});

	jQuery(document).on('click', '.btn-tambah-program', function(){

		jQuery('#wrap-loading').show();

		let programModal = jQuery("#modal-crud-rpjm");
		let kode_sasaran = jQuery(this).data('kodesasaran');

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "add_program_rpjm",
          		"api_key": "<?php echo $api_key; ?>",
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
          		
          		jQuery('#wrap-loading').hide();
				
				let html = '<form id="form-rpjm">'
								+'<input type="hidden" name="kode_sasaran" value="'+kode_sasaran+'"/>'
								+'<div class="form-group">'
									+'<label for="program">Program</label>'
								    +'<input type="text" class="form-control" name="program_teks"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="unit_kerja">Unit Kerja</label>'
									+'<select id="id_unit" name="id_unit" multiple>'
										res.units.map(function(value, index){
									    		html +='<option value="'+value.id_unit+'">'+value.nama_skpd+'</option>';
									    });
								    html+='</select>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_1">Pagu tahun ke-1</label>'
									+'<input type="number" class="form-control" name="pagu_1"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_2">Pagu tahun ke-2</label>'
									+'<input type="number" class="form-control" name="pagu_2"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_3">Pagu tahun ke-3</label>'
									+'<input type="number" class="form-control" name="pagu_3"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_4">Pagu tahun ke-4</label>'
									+'<input type="number" class="form-control" name="pagu_4"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_5">Pagu tahun ke-5</label>'
									+'<input type="number" class="form-control" name="pagu_5"/>'
								+'</div>'
							+'</form>';

		        programModal.find('.modal-title').html('Tambah Program');
				programModal.find('.modal-body').html(html);
				programModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
						+'data-action="submit_program_rpjm" '
						+'data-view="programRpjm" '
						+'data-withunit="true"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
					+'</button>');
				// programModal.find('.modal-dialog').css('maxWidth','950px');
				// programModal.find('.modal-dialog').css('width','100%');
				programModal.modal('show');

				new SlimSelect({
					select: '#id_unit'
				})
          	}
        });
	});

	jQuery(document).on('click', '.btn-edit-program', function(){
		
		jQuery('#wrap-loading').show();

		let programModal = jQuery("#modal-crud-rpjm");
		
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_program_rpjm",
          		"api_key": "<?php echo $api_key; ?>",
				'id_unik': jQuery(this).data('kodeprogram')
          	},
          	dataType: "json",
          	success: function(res){
        
          		jQuery('#wrap-loading').hide();
				
				let html = '<form id="form-rpjm">'
								+'<input type="hidden" name="id_unik" value="'+res.data.id_unik+'"/>'
								+'<input type="hidden" name="kode_sasaran" value="'+res.data.kode_sasaran+'"/>'
								+'<div class="form-group">'
									+'<label for="program">Program</label>'
								    +'<input type="text" class="form-control" name="program_teks" value="'+res.data.program_teks+'"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="unit_kerja">Unit Kerja</label>'
									+'<select id="id_unit" name="id_unit" multiple>'
										res.units.map(function(value, index){
									    		html +='<option value="'+value.id_unit+'">'+value.nama_skpd+'</option>';
									    });
								    html+='</select>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_1">Pagu tahun ke-1</label>'
									+'<input type="number" class="form-control" name="pagu_1" value="'+res.data.pagu_1+'"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_2">Pagu tahun ke-2</label>'
									+'<input type="number" class="form-control" name="pagu_2" value="'+res.data.pagu_2+'"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_3">Pagu tahun ke-3</label>'
									+'<input type="number" class="form-control" name="pagu_3" value="'+res.data.pagu_3+'"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_4">Pagu tahun ke-4</label>'
									+'<input type="number" class="form-control" name="pagu_4" value="'+res.data.pagu_4+'"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_5">Pagu tahun ke-5</label>'
									+'<input type="number" class="form-control" name="pagu_5" value="'+res.data.pagu_5+'"/>'
								+'</div>'
							+'</form>';

		        programModal.find('.modal-title').html('Edit Program');
				programModal.find('.modal-body').html(html);
				programModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
						+'data-action="update_program_rpjm" '
						+'data-view="programRpjm" '
						+'data-withunit="true"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
					+'</button>');
				programModal.modal('show');

				const displaySelect = new SlimSelect({
					select: '#id_unit'
				});

				displaySelect.set(res.selectedUnit);

          	}
        });
	});

	jQuery(document).on('click', '.btn-hapus-program', function(){

		if(confirm('Data akan dihapus, lanjut?')){
			
			jQuery('#wrap-loading').show();
			
			let kode_program = jQuery(this).data('kodeprogram');
			let kode_sasaran = jQuery(this).data('kodesasaran');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_program_rpjm',
		          	'api_key': '<?php echo $api_key; ?>',
					'kode_program': kode_program,
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						programRpjm({
							'kode_sasaran': kode_sasaran
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '.btn-kelola-indikator-program', function(){
		jQuery("#modal-indikator-rpjm").find('.modal-body').html('');
		indikatorProgramRpjm({'kode_program':jQuery(this).data('kodeprogram')});
	})

	jQuery(document).on('click', '.btn-add-indikator-program', function(){

		let indikatorProgramModal = jQuery("#modal-crud-rpjm");
		let kode_program = jQuery(this).data('kodeprogram');
		let html = '<form id="form-rpjm">'
					+'<input type="hidden" name="kode_program" value='+kode_program+'>'
					+'<div class="form-group">'
						+'<label for="indikator_teks">Indikator</label>'
		  				+'<textarea class="form-control" name="indikator_teks"></textarea>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="satuan">Satuan</label>'
		  				+'<input type="text" class="form-control" name="satuan"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_1">Target tahun ke-1</label>'
		  				+'<input type="text" class="form-control" name="target_1"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_2">Target tahun ke-2</label>'
		  				+'<input type="text" class="form-control" name="target_2"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_3">Target tahun ke-3</label>'
		  				+'<input type="text" class="form-control" name="target_3"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_4">Target tahun ke-4</label>'
		  				+'<input type="text" class="form-control" name="target_4"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_5">Target tahun ke-5</label>'
		  				+'<input type="text" class="form-control" name="target_5"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_awal">Target awal</label>'
		  				+'<input type="text" class="form-control" name="target_awal"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_akhir">Target akhir</label>'
		  				+'<input type="text" class="form-control" name="target_akhir"/>'
					+'</div>'
					+'</form>';

			indikatorProgramModal.find('.modal-title').html('Tambah Indikator');
			indikatorProgramModal.find('.modal-body').html(html);
			indikatorProgramModal.find('.modal-footer').html(''
				+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
					+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
				+'</button>'
				+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
					+'data-action="submit_indikator_program_rpjm" '
					+'data-view="indikatorProgramRpjm"'
				+'>'
					+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
				+'</button>');
			indikatorProgramModal.modal('show');
	});

	jQuery(document).on('click', '.btn-edit-indikator-program', function(){

		jQuery('#wrap-loading').show();

		let id = jQuery(this).data('id');
		let kode_program = jQuery(this).data('kodeprogram');
		let indikatorProgramModal = jQuery("#modal-crud-rpjm");

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_indikator_program_rpjm",
          		"api_key": "<?php echo $api_key; ?>",
				'id': id,
				'kode_program': kode_program
          	},
          	dataType: "json",
          	success: function(response){

          		jQuery('#wrap-loading').hide();

          		let html = '<form id="form-rpjm">'
					+'<input type="hidden" name="id" value="'+id+'">'
					+'<input type="hidden" name="kode_program" value="'+kode_program+'">'
					+'<div class="form-group">'
						+'<label for="indikator_teks">Indikator</label>'
	  					+'<textarea class="form-control" name="indikator_teks">'+response.data.indikator+'</textarea>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="satuan">Satuan</label>'
	  					+'<input type="text" class="form-control" name="satuan" value="'+response.data.satuan+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_1">Target tahun ke-1</label>'
	  					+'<input type="text" class="form-control" name="target_1" value="'+response.data.target_1+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_2">Target tahun ke-2</label>'
	  					+'<input type="text" class="form-control" name="target_2" value="'+response.data.target_2+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_3">Target tahun ke-3</label>'
	  					+'<input type="text" class="form-control" name="target_3" value="'+response.data.target_3+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_4">Target tahun ke-4</label>'
	  					+'<input type="text" class="form-control" name="target_4" value="'+response.data.target_4+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_5">Target tahun ke-5</label>'
	  					+'<input type="text" class="form-control" name="target_5" value="'+response.data.target_5+'"/>'
					+'</div>'
					+'<div class="form-group">'
						+'<label for="target_awal">Target awal</label>'
	  					+'<input type="text" class="form-control" name="target_awal" value="'+response.data.target_awal+'"/>'
					+'</div>'
					+'<div class="form-group">'
					+'<label for="target_akhir">Target akhir</label>'
	  					+'<input type="text" class="form-control" name="target_akhir" value="'+response.data.target_akhir+'"/>'
					+'</div>'
				  +'</form>';

				indikatorProgramModal.find('.modal-title').html('Edit Indikator Program');
				indikatorProgramModal.find('.modal-body').html(html);
				indikatorProgramModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
					+'</button><button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-rpjm-lokal" '
						+'data-action="update_indikator_program_rpjm" '
						+'data-view="indikatorProgramRpjm"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
					+'</button>');
				indikatorProgramModal.modal('show');
          	}
		})	
	});

	jQuery(document).on('click', '.btn-delete-indikator-program', function(){

		if(confirm('Data akan dihapus, lanjut?')){

			jQuery('#wrap-loading').show();
			
			let id = jQuery(this).data('id');	
			let kode_program = jQuery(this).data('kodeprogram');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_indikator_program_rpjm',
		          	'api_key': '<?php echo $api_key; ?>',
					'id': id,
					'kode_program': kode_program,
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						indikatorProgramRpjm({
							'kode_program': kode_program
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '#btn-simpan-data-rpjm-lokal', function(){
		
		jQuery('#wrap-loading').show();
		let rpjmModal = jQuery("#modal-crud-rpjm");
		let action = jQuery(this).data('action');
		let view = jQuery(this).data('view');
		let withunit = jQuery(this).data('withunit');
		let form = getFormData(jQuery("#form-rpjm"));

		if(withunit){
			form['id_unit'] = Object.assign({}, jQuery('select[name=id_unit]').val());
		}
		
		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action': action,
	          	'api_key': '<?php echo $api_key; ?>',
				'data': JSON.stringify(form),
			},
			success:function(response){
				jQuery('#wrap-loading').hide();
				alert(response.message);
				if(response.status){
					runFunction(view, [form])
					rpjmModal.modal('hide');
				}
			}
		})
	});

	function visiRpjm(){
		
		jQuery('#wrap-loading').show();
		jQuery('#nav-visi').html('');
		jQuery('#nav-misi').html('');
		jQuery('#nav-tujuan').html('');
		jQuery('#nav-sasaran').html('');
		jQuery('#nav-program').html('');

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_visi_rpjm",
          		"api_key": "<?php echo $api_key; ?>",
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
          		jQuery('#wrap-loading').hide();

          		let visi = ''
	          		+'<div style="margin-top:10px"><button type="button" class="btn btn-sm btn-primary mb-2 btn-tambah-visi"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Visi</button>'
	          		+'</div>'
	          		+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th style="min-width:20px">No.</th>'
	          					+'<th style="min-width:400px">Visi</th>'
	          					+'<th>Aksi</th>'
	          				+'<tr>'
	          			+'</thead>'
	          			+'<tbody>';

          		res.data.map(function(value, index){
          			visi +='<tr idvisi="'+value.id+'">'
			          			+'<td>'+(index+1)+'.</td>'
			          			+'<td>'+value.visi_teks+'</td>'
			          			+'<td>'
			          					+'<a href="javascript:void(0)" data-id="'+value.id+'" class="btn btn-sm btn-primary btn-detail-visi"><i class="dashicons dashicons-search"></i></a>&nbsp;'
			          					+'<a href="javascript:void(0)" data-id="'+value.id+'" class="btn btn-sm btn-success btn-edit-visi"><i class="dashicons dashicons-edit"></i></a>&nbsp;'
			          					+'<a href="javascript:void(0)" data-id="'+value.id+'" class="btn btn-sm btn-danger btn-hapus-visi"><i class="dashicons dashicons-trash"></i></a>'
			          			+'</td>'
			          		+'</tr>';
          		})
          		visi+='<tbody></table>';

          		jQuery("#nav-visi").html(visi);
				jQuery('.nav-tabs a[href="#nav-visi"]').tab('show');
				jQuery('#modal-monev').modal('show');
        	}
		})
	}

	function misiRpjm(params){

		jQuery('#wrap-loading').show();
		
		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action': 'get_misi_rpjm',
	          	'api_key': '<?php echo $api_key; ?>',
				'id_visi': params.id_visi,
          		'type': 1
			},
			success:function(response){

          		jQuery('#wrap-loading').hide();
          		
          		let misi = ''
          			+'<div style="margin-top:10px"><button type="button" class="btn btn-sm btn-primary mb-2 btn-tambah-misi" data-idvisi="'+params.id_visi+'"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Misi</button>'
          			+'<table class="table table-bordered" style="margin: 10px 0;">'
						+'<tbody>'
							+'<tr>'
								+'<th class="text-center" style="width: 160px;">Visi</th>'
								+'<th>'+jQuery('#nav-visi tr[idvisi="'+params.id_visi+'"]').find('td').eq(1).text()+'</th>'
							+"</tr>"
						+"</tbody>"
					+"</table>"
	          		+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th style="min-width:20px">No.</th>'
	          					+'<th style="min-width:400px">Misi</th>'
	          					+'<th>Aksi</th>'
	          				+'<tr>'
	          			+'</thead>'
	          			+'<tbody>';
			          		response.data.map(function(value, index){
			          			misi +='<tr idmisi="'+value.id+'">'
						          		+'<td>'+(index+1)+'.</td>'
						          		+'<td>'+value.misi_teks+'</td>'
						          		+'<td>'
						          			+'<a href="javascript:void(0)" data-id="'+value.id+'" class="btn btn-sm btn-primary btn-detail-misi"><i class="dashicons dashicons-search" style="margin-top: 3px;"></i></a>&nbsp;'
						          			+'<a href="javascript:void(0)" data-id="'+value.id+'" class="btn btn-sm btn-success btn-edit-misi"><i class="dashicons dashicons-edit" style="margin-top: 3px;"></i></a>&nbsp;'
						          			+'<a href="javascript:void(0)" data-id="'+value.id+'" data-idvisi="'+value.id_visi+'" class="btn btn-sm btn-danger btn-hapus-misi"><i class="dashicons dashicons-trash" style="margin-top: 3px;"></i></a>'
						          		+'</td>'
						          	+'</tr>';
			          		});
		          	misi+='</tbody></table>';
			    jQuery("#nav-misi").html(misi);
				jQuery('.nav-tabs a[href="#nav-misi"]').tab('show');
			}
		})
	}

	function tujuanRpjm(params){

		jQuery('#wrap-loading').show();

		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action': 'get_tujuan_rpjm',
	          	'api_key': '<?php echo $api_key; ?>',
				'id_misi': params.id_misi,
				'type': 1
			},
			success:function(response){

          		jQuery('#wrap-loading').hide();
          		
          		let tujuan = ''
          				+'<div style="margin-top:10px"><button type="button" class="btn btn-sm btn-primary mb-2 btn-tambah-tujuan" data-idmisi="'+params.id_misi+'"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Tujuan</button></div>'
          				+'<table class="table">'
	          				+'<thead>'
	          					+'<tr>'
	          						+'<th style="max-width:25px">Visi</th>'
	          						+'<th>'+jQuery('#nav-visi tr[idvisi="'+jQuery("#nav-misi .btn-tambah-misi").data("idvisi")+'"]').find('td').eq(1).text()+'</th>'
	          					+'</tr>'
	          					+'<tr>'
	          						+'<th style="max-width:25px">Misi</th>'
	          						+'<th>'+jQuery('#nav-misi tr[idmisi="'+params.id_misi+'"]').find('td').eq(1).text()+'</th>'
	          					+'</tr>'
	          				+'</thead>'
          				+'</table>'
          				+'<table class="table">'
          					+'<thead>'
          						+'<tr>'
          							+'<th style="min-width:20px">No.</th>'
          							+'<th style="min-width:400px">Tujuan</th>'
          							+'<th>Aksi</th>'
          						+'<tr>'
          					+'</thead>'
          					+'<tbody>';
          						response.data.map(function(value, index){
          							tujuan+='<tr idtujuan="'+value.id+'" kodetujuan="'+value.id_unik+'">'
			          							+'<td>'+(index+1)+'.</td>'
			          							+'<td>'+value.tujuan_teks+'</td>'
			          							+'<td>'
			          								+'<a href="javascript:void(0)" data-idtujuan="'+value.id+'" class="btn btn-sm btn-warning btn-kelola-indikator-tujuan"><i class="dashicons dashicons-arrow-up-alt" style="margin-top: 3px;"></i> Indikator</a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-idtujuan="'+value.id+'" data-kode="'+value.id_unik+'" class="btn btn-sm btn-primary btn-detail-tujuan"><i class="dashicons dashicons-search" style="margin-top: 3px;"></i></a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-idtujuan="'+value.id+'" class="btn btn-sm btn-success btn-edit-tujuan"><i class="dashicons dashicons-edit" style="margin-top: 3px;"></i></a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-idtujuan="'+value.id+'" data-kodetujuan="'+value.id_unik+'" data-idmisi="'+value.id_misi+'" class="btn btn-sm btn-danger btn-hapus-tujuan"><i class="dashicons dashicons-trash" style="margin-top: 3px;"></i></a></td>'
			          						+'</tr>';
          						})
          					tujuan +='<tbody>'
          				+'</table>';

			    jQuery("#nav-tujuan").html(tujuan);
			 	jQuery('.nav-tabs a[href="#nav-tujuan"]').tab('show');
			}
		})
	}

	function indikatorTujuanRpjm(params){
		
		jQuery('#wrap-loading').show();

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_indikator_tujuan_rpjm",
          		"api_key": "<?php echo $api_key; ?>",
				'id_tujuan': params.id_tujuan,
				'type':1
          	},
          	dataType: "json",
          	success: function(response){
          		jQuery('#wrap-loading').hide();

          		let html=""
					+"<div class='row'>"
						+"<div class='col-lg-10'><h5>"+jQuery('#nav-tujuan tr[idtujuan="'+params.id_tujuan+'"]').find('td').eq(1).text()+"</h5></div>"
						+"<div class='col-lg-2 text-right'>"
							+"<button type=\"button\" class=\"btn btn-sm btn-primary mb-2 btn-add-indikator-tujuan\" data-idtujuan=\""+params.id_tujuan+"\">"
								+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 3px;\"></i> Tambah Indikator"
							+"</button>"
						+"</div>"
					+"</div>"
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th>No.</th>"
								+"<th>Indikator</th>"
								+"<th>Satuan</th>"
								+"<th>Target 1</th>"
								+"<th>Target 2</th>"
								+"<th>Target 3</th>"
								+"<th>Target 4</th>"
								+"<th>Target 5</th>"
								+"<th>Target Awal</th>"
								+"<th>Target Akhir</th>"
								+"<th>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_tujuan'>";
						response.data.map(function(value, index){
			          			html +="<tr>"
						          		+"<td>"+(index+1)+".</td>"
						          		+"<td>"+value.indikator_teks+"</td>"
						          		+"<td>"+value.satuan+"</td>"
						          		+"<td>"+value.target_1+"</td>"
						          		+"<td>"+value.target_2+"</td>"
						          		+"<td>"+value.target_3+"</td>"
						          		+"<td>"+value.target_4+"</td>"
						          		+"<td>"+value.target_5+"</td>"
						          		+"<td>"+value.target_awal+"</td>"
						          		+"<td>"+value.target_akhir+"</td>"
						          		+"<td>"
						          			+"<a href='#' class='btn btn-sm btn-success btn-edit-indikator-tujuan' data-idtujuan='"+value.id_tujuan+"' data-id='"+value.id+"'><i class='dashicons dashicons-edit' style='margin-top: 3px;'></i></a>&nbsp"
											+"<a href='#' class='btn btn-sm btn-danger btn-delete-indikator-tujuan' data-idtujuan='"+value.id_tujuan+"' data-id='"+value.id+"'><i class='dashicons dashicons-trash' style='margin-top: 3px;'></i></a>&nbsp;"
						          		+"</td>"
						          	+"</tr>";
			          		});
		          	html+='</tbody></table>';

		          	jQuery("#modal-indikator-rpjm").find('.modal-title').html('Indikator Tujuan');
		          	jQuery("#modal-indikator-rpjm").find('.modal-body').html(html)
					jQuery("#modal-indikator-rpjm").find('.modal-dialog').css('maxWidth','1250px');
					jQuery("#modal-indikator-rpjm").find('.modal-dialog').css('width','100%');
					jQuery("#modal-indikator-rpjm").modal('show');
          	}  	
		})
	}

	function sasaranRpjm(params){

		jQuery('#wrap-loading').show();
		
		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action': 'get_sasaran_rpjm',
	          	'api_key': '<?php echo $api_key; ?>',
				'kode_tujuan': params.kode_tujuan,
				'type':1
			},
			success:function(response){

          		jQuery('#wrap-loading').hide();
          		
          		let sasaran = ''
          				+'<div style="margin-top:10px"><button type="button" class="btn btn-sm btn-primary mb-2 btn-tambah-sasaran" data-kodetujuan="'+params.kode_tujuan+'"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Sasaran</button></div>'
          				+'<table class="table">'
          					+'<thead>'
	          					+'<tr>'
	          						+'<th style="max-width:25px">Visi</th>'
	          						+'<th>'+jQuery('#nav-visi tr[idvisi="'+jQuery("#nav-misi .btn-tambah-misi").data("idvisi")+'"]').find('td').eq(1).text()+'</th>'
	          					+'</tr>'
	          					+'<tr>'
	          						+'<th style="max-width:25px">Misi</th>'
	          						+'<th>'+jQuery('#nav-misi tr[idmisi="'+jQuery("#nav-tujuan .btn-tambah-tujuan").data("idmisi")+'"]').find('td').eq(1).text()+'</th>'
	          					+'</tr>'
	          					+'<tr>'
	          						+'<th style="max-width:25px">Tujuan</th>'
	          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+params.kode_tujuan+'"]').find('td').eq(1).text()+'</th>'
	          					+'</tr>'
          					+'</thead>'
          				+'</table>'
          				
          				+'<table class="table">'
          					+'<thead>'
          						+'<tr>'
          							+'<th style="min-width:20px">No.</th>'
          							+'<th style="min-width:400px">Sasaran</th>'
          							+'<th>Aksi</th>'
          						+'<tr>'
          					+'</thead>'
          					+'<tbody>';

          						response.data.map(function(value, index){
          							sasaran +='<tr idsasaran="'+value.id+'" kodesasaran="'+value.id_unik+'">'
			          							+'<td>'+(index+1)+'.</td>'
			          							+'<td>'+value.sasaran_teks+'</td>'
			          							+'<td>'
			          								+'<a href="javascript:void(0)" data-idsasaran="'+value.id+'" class="btn btn-sm btn-warning btn-kelola-indikator-sasaran"><i class="dashicons dashicons-arrow-up-alt" style="margin-top: 3px;"></i> Indikator</a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-kodesasaran="'+value.id_unik+'" class="btn btn-sm btn-primary btn-detail-sasaran"><i class="dashicons dashicons-search" style="margin-top: 3px;"></i></a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-idsasaran="'+value.id+'" class="btn btn-sm btn-success btn-edit-sasaran"><i class="dashicons dashicons-edit" style="margin-top: 3px;"></i></a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-idsasaran="'+value.id+'" data-kodesasaran="'+value.id_unik+'" data-kodetujuan="'+value.kode_tujuan+'" class="btn btn-sm btn-danger btn-hapus-sasaran"><i class="dashicons dashicons-trash" style="margin-top: 3px;"></i></a></td>'
			          						+'</tr>';
          						})
          					sasaran +='<tbody>'
          				+'</table>';

			    jQuery("#nav-sasaran").html(sasaran);
			 	jQuery('.nav-tabs a[href="#nav-sasaran"]').tab('show');
			}
		})
	}

	function indikatorSasaranRpjm(params){

		jQuery('#wrap-loading').show();

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_indikator_sasaran_rpjm",
          		"api_key": "<?php echo $api_key; ?>",
				'id_sasaran': params.id_sasaran,
				"type": 1
          	},
          	dataType: "json",
          	success: function(response){
          		jQuery('#wrap-loading').hide();
          		
          		let html=""
					+"<div class='row'>"
						+"<div class='col-lg-10'><h5>"+jQuery('#nav-sasaran tr[idsasaran="'+params.id_sasaran+'"]').find('td').eq(1).text()+"</h5></div>"
						+"<div class='col-lg-2 text-right'>"
							+"<button type=\"button\" class=\"btn btn-sm btn-primary mb-2 btn-add-indikator-sasaran\" data-idsasaran=\""+params.id_sasaran+"\">"
								+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 3px;\"></i> Tambah Indikator"
							+"</button>"
						+"</div>"
					+"</div>"
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th>No.</th>"
								+"<th>Indikator</th>"
								+"<th>Satuan</th>"
								+"<th>Target 1</th>"
								+"<th>Target 2</th>"
								+"<th>Target 3</th>"
								+"<th>Target 4</th>"
								+"<th>Target 5</th>"
								+"<th>Target Awal</th>"
								+"<th>Target Akhir</th>"
								+"<th>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_tujuan'>";
						response.data.map(function(value, index){
			          			html +="<tr>"
						          		+"<td>"+(index+1)+".</td>"
						          		+"<td>"+value.indikator_teks+"</td>"
						          		+"<td>"+value.satuan+"</td>"
						          		+"<td>"+value.target_1+"</td>"
						          		+"<td>"+value.target_2+"</td>"
						          		+"<td>"+value.target_3+"</td>"
						          		+"<td>"+value.target_4+"</td>"
						          		+"<td>"+value.target_5+"</td>"
						          		+"<td>"+value.target_awal+"</td>"
						          		+"<td>"+value.target_akhir+"</td>"
						          		+"<td>"
						          			+"<a href='#' class='btn btn-sm btn-success btn-edit-indikator-sasaran' data-idsasaran='"+value.id_sasaran+"' data-id='"+value.id+"'><i class='dashicons dashicons-edit' style='margin-top: 3px;'></i></a>&nbsp"
											+"<a href='#' class='btn btn-sm btn-danger btn-delete-indikator-sasaran' data-idsasaran='"+value.id_sasaran+"' data-id='"+value.id+"'><i class='dashicons dashicons-trash' style='margin-top: 3px;'></i></a>&nbsp;"
						          		+"</td>"
						          	+"</tr>";
			          		});
		          	html+='</tbody></table>';

				jQuery("#modal-indikator-rpjm").find('.modal-title').html('Indikator Sasaran');
		        jQuery("#modal-indikator-rpjm").find('.modal-body').html(html);
				jQuery("#modal-indikator-rpjm").find('.modal-dialog').css('maxWidth','1250px');
				jQuery("#modal-indikator-rpjm").find('.modal-dialog').css('width','100%');
				jQuery("#modal-indikator-rpjm").modal('show');
          	}
		})
	}

	function programRpjm(params){

		jQuery('#wrap-loading').show();

		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action': 'get_program_rpjm',
	          	'api_key': '<?php echo $api_key; ?>',
				'kode_sasaran': params.kode_sasaran,
				'type':1
			},
			success:function(response){

          		jQuery('#wrap-loading').hide();
          		
          		let program = ''
          				+'<div style="margin-top:10px"><button type="button" class="btn btn-sm btn-primary mb-2 btn-tambah-program" data-kodesasaran="'+params.kode_sasaran+'"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Program</button></div>'
          				+'<table class="table">'
          					+'<thead>'
	          					+'<tr>'
	          						+'<th style="max-width:25px">Visi</th>'
	          						+'<th>'+jQuery('#nav-visi tr[idvisi="'+jQuery("#nav-misi .btn-tambah-misi").data("idvisi")+'"]').find('td').eq(1).text()+'</th>'
	          					+'</tr>'
	          					+'<tr>'
	          						+'<th style="max-width:25px">Misi</th>'
	          						+'<th>'+jQuery('#nav-misi tr[idmisi="'+jQuery("#nav-tujuan .btn-tambah-tujuan").data("idmisi")+'"]').find('td').eq(1).text()+'</th>'
	          					+'</tr>'
	          					+'<tr>'
	          						+'<th style="max-width:25px">Tujuan</th>'
	          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text()+'</th>'
	          					+'</tr>'
	          					+'<tr>'
	          						+'<th style="max-width:25px">Sasaran</th>'
	          						+'<th>'+jQuery('#nav-sasaran tr[kodesasaran="'+params.kode_sasaran+'"]').find('td').eq(1).text()+'</th>'
	          					+'</tr>'
          					+'</thead>'
          				+'</table>'
          				
          				+'<table class="table">'
          					+'<thead>'
          						+'<tr>'
          							+'<th style="min-width:20px">No.</th>'
          							+'<th style="min-width:400px">Program</th>'
          							+'<th>Aksi</th>'
          						+'<tr>'
          					+'</thead>'
          					+'<tbody>';

          						response.data.map(function(value, index){
          							program +='<tr idprogram="'+value.id+'" kodeprogram="'+value.id_unik+'">'
			          							+'<td>'+(index+1)+'.</td>'
			          							+'<td>'+value.nama_program+'</td>'
			          							+'<td>'
			          								+'<a href="javascript:void(0)" data-kodeprogram="'+value.id_unik+'" class="btn btn-sm btn-warning btn-kelola-indikator-program"><i class="dashicons dashicons-arrow-up-alt" style="margin-top: 3px;"></i> Indikator</a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-kodeprogram="'+value.id_unik+'" class="btn btn-sm btn-success btn-edit-program"><i class="dashicons dashicons-edit" style="margin-top: 3px;"></i></a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-kodeprogram="'+value.id_unik+'" data-kodesasaran="'+value.kode_sasaran+'" class="btn btn-sm btn-danger btn-hapus-program"><i class="dashicons dashicons-trash" style="margin-top: 3px;"></i></a></td>'
			          						+'</tr>';
          						})

          					program +='<tbody>'
          				+'</table>';

			    jQuery("#nav-program").html(program);
			 	jQuery('.nav-tabs a[href="#nav-program"]').tab('show');
			}
		})
	}

	function indikatorProgramRpjm(params){

		jQuery('#wrap-loading').show();

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_indikator_program_rpjm",
          		"api_key": "<?php echo $api_key; ?>",
				'kode_program': params.kode_program,
				"type": 1
          	},
          	dataType: "json",
          	success: function(response){

          		jQuery('#wrap-loading').hide();
          		
          		let html=""
					+"<div class='row'>"
						+"<div class='col-lg-10'><h5>"+jQuery('#nav-program tr[kodeprogram="'+params.kode_program+'"]').find('td').eq(1).text()+"</h5></div>"
						+"<div class='col-lg-2 text-right'>"
							+"<button type=\"button\" class=\"btn btn-sm btn-primary mb-2 btn-add-indikator-program\" data-kodeprogram=\""+params.kode_program+"\">"
								+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 3px;\"></i> Tambah Indikator"
							+"</button>"
						+"</div>"
					+"</div>"
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th>No.</th>"
								+"<th>Indikator</th>"
								+"<th>Satuan</th>"
								+"<th>Target 1</th>"
								+"<th>Target 2</th>"
								+"<th>Target 3</th>"
								+"<th>Target 4</th>"
								+"<th>Target 5</th>"
								+"<th>Target Awal</th>"
								+"<th>Target Akhir</th>"
								+"<th>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_tujuan'>";
						response.data.map(function(value, index){
			          			html +="<tr>"
						          		+"<td>"+(index+1)+".</td>"
						          		+"<td>"+value.indikator+"</td>"
						          		+"<td>"+value.satuan+"</td>"
						          		+"<td>"+value.target_1+"</td>"
						          		+"<td>"+value.target_2+"</td>"
						          		+"<td>"+value.target_3+"</td>"
						          		+"<td>"+value.target_4+"</td>"
						          		+"<td>"+value.target_5+"</td>"
						          		+"<td>"+value.target_awal+"</td>"
						          		+"<td>"+value.target_akhir+"</td>"
						          		+"<td>"
						          			+"<a href='#' class='btn btn-sm btn-success btn-edit-indikator-program' data-kodeprogram='"+value.id_unik+"' data-id='"+value.id+"'><i class='dashicons dashicons-edit' style='margin-top: 3px;'></i></a>&nbsp"
											+"<a href='#' class='btn btn-sm btn-danger btn-delete-indikator-program' data-kodeprogram='"+value.id_unik+"' data-id='"+value.id+"'><i class='dashicons dashicons-trash' style='margin-top: 3px;'></i></a>&nbsp;"
						          		+"</td>"
						          	+"</tr>";
			          		});
		          	html+='</tbody></table>';

				jQuery("#modal-indikator-rpjm").find('.modal-title').html('Indikator Program');
		        jQuery("#modal-indikator-rpjm").find('.modal-body').html(html);
				jQuery("#modal-indikator-rpjm").find('.modal-dialog').css('maxWidth','1250px');
				jQuery("#modal-indikator-rpjm").find('.modal-dialog').css('width','100%');
				jQuery("#modal-indikator-rpjm").modal('show');
			}
		});
	}

	function getFormData($form) {
	    let unindexed_array = $form.serializeArray();
	    let indexed_array = {};

	    jQuery.map(unindexed_array, function (n, i) {
	    	indexed_array[n['name']] = n['value'];
	    });

	    return indexed_array;
	}

	function runFunction(name, arguments)
	{
	    var fn = window[name];
	    if(typeof fn !== 'function')
	        return;

	    fn.apply(window, arguments);
	}
</script>