<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if (!$_GET['id_jadwal_lokal']) {
	die('<h1 class="text-center">ID Jadwal Lokal tidak ditemukan!</h1>');
}
$data_jadwal = $this->get_data_jadwal_by_id_jadwal_lokal($_GET['id_jadwal_lokal']);
if (empty($data_jadwal)) {
	die('<h1 class="text-center">Jadwal tidak valid.</h1>');
}
global $wpdb;
$user_id = um_user( 'ID' );
$user_meta = get_userdata($user_id);

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
// check jadwal is running bool;
$prefix_history = '';
$is_locked_jadwal_lokal = $data_jadwal->status == 1 ? true : false;
if ($is_locked_jadwal_lokal) {
	$prefix_history = '_history';
}

$is_running_jadwal_lokal = $this->is_running_jadwal_lokal($data_jadwal->id_jadwal_lokal);
if ($is_running_jadwal_lokal) {
    if (in_array("administrator", $user_meta->roles)) {
		$add_rpd = '<a id="tambah-data" onclick="return false;" href="#" class="btn btn-primary mr-2"><span class="dashicons dashicons-plus"></span> Tambah Data RPD</a>';
    }
} else {
	$add_rpd = '<button onclick="return false;" href="#" class="btn btn-primary mr-2 disabled" disabled><span class="dashicons dashicons-plus"></span> Tambah Data RPD</button>';
}

$timezone = get_option('timezone_string');

$awal_rpd = $data_jadwal->tahun_anggaran;
$lama_pelaksanaan = $data_jadwal->lama_pelaksanaan;
$akhir_rpd = $awal_rpd + $lama_pelaksanaan - 1;
$nama_pemda = get_option('_crb_daerah');

$current_user = wp_get_current_user();
$bulan = date('m');
$body_monev = '';

$data_all = array(
	'data' => array(),
	'pemutakhiran_program' => 0
);
$bulan = date('m');

$tujuan_ids = array();
$sasaran_ids = array();
$program_ids = array();
$skpd_filter = array();

$sql = $wpdb->prepare("
	select 
		t.*,
		i.isu_teks 
	from data_rpd_tujuan_lokal{$prefix_history} t
	left join data_rpjpd_isu i on t.id_isu = i.id
	where t.active=1
	  AND tahun_anggaran = %d
	order by t.no_urut asc
", $data_jadwal->tahun_anggaran);

if(!empty($id_jadwal_rpjpd)){
	$sql = $wpdb->prepare("
		select 
			t.*,
			i.isu_teks 
		from data_rpd_tujuan_lokal{$prefix_history} t
		left join data_rpjpd_isu_history i on t.id_isu = i.id_asli
		where t.active=1
		  AND tahun_anggaran = %d
		order by t.no_urut asc
	", $data_jadwal->tahun_anggaran);
}
$tujuan_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($tujuan_all as $tujuan) {
	if(empty($data_all['data'][$tujuan['id_unik']])){
		$data_all['data'][$tujuan['id_unik']] = array(
			'nama' => $tujuan['tujuan_teks'],
			'total_akumulasi_1' => 0,
			'total_akumulasi_2' => 0,
			'total_akumulasi_3' => 0,
			'total_akumulasi_4' => 0,
			'total_akumulasi_5' => 0,
			'detail' => array(),
			'data' => array()
		);
		$tujuan_ids[$tujuan['id_unik']] = "'".$tujuan['id_unik']."'";
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpd_sasaran_lokal{$prefix_history}
			where kode_tujuan=%s
				and active=1
				AND tahun_anggaran = %d
				order by sasaran_no_urut asc
		", $tujuan['id_unik'], $data_jadwal->tahun_anggaran);
		$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($sasaran_all as $sasaran) {
			if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
					'nama' => $sasaran['sasaran_teks'],
					'total_akumulasi_1' => 0,
					'total_akumulasi_2' => 0,
					'total_akumulasi_3' => 0,
					'total_akumulasi_4' => 0,
					'total_akumulasi_5' => 0,
					'detail' => array(),
					'data' => array()
				);
				$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
				$sql = $wpdb->prepare("
					select 
						* 
					from data_rpd_program_lokal{$prefix_history}
					where kode_sasaran=%s
						and active=1
						AND tahun_anggaran = %d
						order by nama_program ASC
				", $sasaran['id_unik'], $data_jadwal->tahun_anggaran);
				$program_all = $wpdb->get_results($sql, ARRAY_A);
				foreach ($program_all as $program) {
					$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
					if(empty($program['kode_skpd']) && empty($program['nama_skpd'])){
						$program['kode_skpd'] = '';
						$program['nama_skpd'] = 'Semua Perangkat Daerah';
					}
					$skpd_filter[$program['kode_skpd']] = $program['nama_skpd'];
					if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){

						//check program
						$kode_program = explode(" ", $program['nama_program']);
						$checkProgram = $wpdb->get_row($wpdb->prepare("SELECT kode_program FROM data_prog_keg WHERE kode_program=%s AND tahun_anggaran=%d AND active=%d", $kode_program[0], $data_jadwal->tahun_anggaran, 1), ARRAY_A);

						$statusMutakhirProgram=0;
						if(empty($checkProgram['kode_program'])){
							$statusMutakhirProgram=1;
							$data_all['pemutakhiran_program']++;
						}

						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
							'id_unik' => $program['id_unik'],
							'nama' => $program['nama_program'],
							'kode_skpd' => $program['kode_skpd'],
							'nama_skpd' => $program['nama_skpd'],
							'statusMutakhirProgram' => $statusMutakhirProgram,
							'total_akumulasi_1' => 0,
							'total_akumulasi_2' => 0,
							'total_akumulasi_3' => 0,
							'total_akumulasi_4' => 0,
							'total_akumulasi_5' => 0,
							'detail' => array(),
							'data' => array()
						);
					}
					$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['detail'][] = $program;
					if(
						!empty($program['id_unik_indikator']) 
						&& empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])
					){
						$data_all['data'][$tujuan['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
						$data_all['data'][$tujuan['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
						$data_all['data'][$tujuan['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
						$data_all['data'][$tujuan['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
						$data_all['data'][$tujuan['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
						$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
							'nama' => $program['indikator'],
							'data' => $program
						);
					}
				}
			}
			$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
		}
	}
	$data_all['data'][$tujuan['id_unik']]['detail'][] = $tujuan;

}

// buat array data kosong
if(empty($data_all['data']['tujuan_kosong'])){
	$data_all['data']['tujuan_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'total_akumulasi_1' => 0,
		'total_akumulasi_2' => 0,
		'total_akumulasi_3' => 0,
		'total_akumulasi_4' => 0,
		'total_akumulasi_5' => 0,
		'detail' => array(
			array(
				'id_unik' => 'kosong',
				'isu_teks' => ''
			)
		),
		'data' => array()
	);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong'])){
	$data_all['data']['tujuan_kosong']['data']['sasaran_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'total_akumulasi_1' => 0,
		'total_akumulasi_2' => 0,
		'total_akumulasi_3' => 0,
		'total_akumulasi_4' => 0,
		'total_akumulasi_5' => 0,
		'detail' => array(
			array(
				'id_unik' => 'kosong',
				'isu_teks' => ''
			)
		),
		'data' => array()
	);
}

// select tujuan yang belum terselect
if(!empty($tujuan_ids)){
	$sql = $wpdb->prepare("
		select 
			t.*,
			i.isu_teks 
		from data_rpd_tujuan_lokal{$prefix_history} t
		left join data_rpjpd_isu i on t.id_isu = i.id
		where t.id_unik not in (".implode(',', $tujuan_ids).")
			and t.active=1
			AND t.tahun_anggaran = %d
		order by t.no_urut
	", $data_jadwal->tahun_anggaran);
	if(!empty($id_jadwal_rpjpd)){
		$sql = $wpdb->prepare("
			select 
				t.*,
				i.isu_teks 
			from data_rpd_tujuan_lokal{$prefix_history} t
			left join data_rpjpd_isu_history i on t.id_isu = i.id_asli
			where t.id_unik not in (".implode(',', $tujuan_ids).")
				and t.active=1
				AND t.tahun_anggaran = %d
			order by t.no_urut
		", $data_jadwal->tahun_anggaran);
	}
}else{
	$sql = $wpdb->prepare("
		select 
			t.*,
			i.isu_teks 
		from data_rpd_tujuan_lokal{$prefix_history} t
		left join data_rpjpd_isu i on t.id_isu = i.id
		where t.active=1
		AND t.tahun_anggaran = %d
		order by t.no_urut
	", $data_jadwal->tahun_anggaran);
	if(!empty($id_jadwal_rpjpd)){
		$sql = $wpdb->prepare("
			select 
				t.*,
				i.isu_teks 
			from data_rpd_tujuan_lokal{$prefix_history} t
			left join data_rpjpd_isu_history i on t.id_isu = i.id_asli
			where t.active=1
			AND t.tahun_anggaran = %d
			order by t.no_urut
		", $data_jadwal->tahun_anggaran);
	}
}
$tujuan_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($tujuan_all_kosong as $tujuan) {
	if(empty($data_all['data'][$tujuan['id_unik']])){
		$data_all['data'][$tujuan['id_unik']] = array(
			'nama' => $tujuan['tujuan_teks'],
			'total_akumulasi_1' => 0,
			'total_akumulasi_2' => 0,
			'total_akumulasi_3' => 0,
			'total_akumulasi_4' => 0,
			'total_akumulasi_5' => 0,
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data'][$tujuan['id_unik']]['detail'][] = $tujuan;
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpd_sasaran_lokal{$prefix_history}
		where kode_tujuan=%s
			and active=1
			AND tahun_anggaran = %d
		order by sasaran_no_urut
	", $tujuan['id_unik'], $data_jadwal->tahun_anggaran);
	$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($sasaran_all as $sasaran) {
		$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
		if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
			$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
				'nama' => $sasaran['sasaran_teks'],
				'total_akumulasi_1' => 0,
				'total_akumulasi_2' => 0,
				'total_akumulasi_3' => 0,
				'total_akumulasi_4' => 0,
				'total_akumulasi_5' => 0,
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpd_program_lokal{$prefix_history}
			where kode_sasaran=%s
				and active=1
				AND tahun_anggaran = %d
		", $sasaran['id_unik'], $data_jadwal->tahun_anggaran);
		$program_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($program_all as $program) {
			$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
			if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
					'nama' => $program['nama_program'],
					'kode_skpd' => $program['kode_skpd'],
					'nama_skpd' => $program['nama_skpd'],
					'total_akumulasi_1' => 0,
					'total_akumulasi_2' => 0,
					'total_akumulasi_3' => 0,
					'total_akumulasi_4' => 0,
					'total_akumulasi_5' => 0,
					'detail' => array(),
					'data' => array()
				);
			}
			$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['detail'][] = $program;
			if(
				!empty($program['id_unik_indikator']) 
				&& empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])
			){
				$data_all['data'][$tujuan['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
				$data_all['data'][$tujuan['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
				$data_all['data'][$tujuan['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
				$data_all['data'][$tujuan['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
				$data_all['data'][$tujuan['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
				$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
					'nama' => $program['indikator'],
					'data' => $program
				);
			}
		}
	}
}

// select sasaran yang belum terselect
if(!empty($sasaran_ids)){
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpd_sasaran_lokal{$prefix_history}
		where id_unik not in (".implode(',', $sasaran_ids).")
			and active=1
			AND tahun_anggaran = %d
		order by sasaran_no_urut
	", $data_jadwal->tahun_anggaran);
}else{
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpd_sasaran_lokal{$prefix_history}
		where active=1
			AND tahun_anggaran = %d
		order by sasaran_no_urut
	", $data_jadwal->tahun_anggaran);
}
$sasaran_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($sasaran_all_kosong as $sasaran) {
	if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']])){
		$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']] = array(
			'nama' => $sasaran['sasaran_teks'],
			'total_akumulasi_1' => 0,
			'total_akumulasi_2' => 0,
			'total_akumulasi_3' => 0,
			'total_akumulasi_4' => 0,
			'total_akumulasi_5' => 0,
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpd_program_lokal{$prefix_history}
		where kode_sasaran=%s
			and tahun_anggaran = %d
			and active=1
	", $sasaran['id_unik'], $data_jadwal->tahun_anggaran);
	$program_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($program_all as $program) {
		$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
		if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
				'nama' => $program['nama_program'],
				'kode_skpd' => $program['kode_skpd'],
				'nama_skpd' => $program['nama_skpd'],
				'total_akumulasi_1' => 0,
				'total_akumulasi_2' => 0,
				'total_akumulasi_3' => 0,
				'total_akumulasi_4' => 0,
				'total_akumulasi_5' => 0,
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['detail'][] = $program;
		if(
			!empty($program['id_unik_indikator']) 
			&& empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])
		){
			$data_all['data']['tujuan_kosong']['total_akumulasi_1'] += $program['pagu_1'];
			$data_all['data']['tujuan_kosong']['total_akumulasi_2'] += $program['pagu_2'];
			$data_all['data']['tujuan_kosong']['total_akumulasi_3'] += $program['pagu_3'];
			$data_all['data']['tujuan_kosong']['total_akumulasi_4'] += $program['pagu_4'];
			$data_all['data']['tujuan_kosong']['total_akumulasi_5'] += $program['pagu_5'];
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
			$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
				'nama' => $program['indikator'],
				'data' => $program
			);
		}
	}
}

// select program yang belum terselect
if(!empty($program_ids)){
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpd_program_lokal{$prefix_history}
		where id_unik not in (".implode(',', $program_ids).")
			and active=1
			and tahun_anggaran = %d
	", $data_jadwal->tahun_anggaran);
}else{
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpd_program_lokal{$prefix_history}
		where active=1
			and tahun_anggaran = %d
	", $data_jadwal->tahun_anggaran);
}
$program_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($program_all as $program) {
	if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']])){
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']] = array(
			'nama' => $program['nama_program'],
			'kode_skpd' => $program['kode_skpd'],
			'nama_skpd' => $program['nama_skpd'],
			'total_akumulasi_1' => 0,
			'total_akumulasi_2' => 0,
			'total_akumulasi_3' => 0,
			'total_akumulasi_4' => 0,
			'total_akumulasi_5' => 0,
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['detail'][] = $program;
	if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
		$data_all['data']['tujuan_kosong']['total_akumulasi_1'] += $program['pagu_1'];
		$data_all['data']['tujuan_kosong']['total_akumulasi_2'] += $program['pagu_2'];
		$data_all['data']['tujuan_kosong']['total_akumulasi_3'] += $program['pagu_3'];
		$data_all['data']['tujuan_kosong']['total_akumulasi_4'] += $program['pagu_4'];
		$data_all['data']['tujuan_kosong']['total_akumulasi_5'] += $program['pagu_5'];
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['total_akumulasi_1'] += $program['pagu_1'];
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['total_akumulasi_2'] += $program['pagu_2'];
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['total_akumulasi_3'] += $program['pagu_3'];
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['total_akumulasi_4'] += $program['pagu_4'];
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['total_akumulasi_5'] += $program['pagu_5'];
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
			'nama' => $program['indikator'],
			'data' => $program
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

// print_r($data_all);

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
	$indikator_catatan_tujuan = '';
	foreach($tujuan['detail'] as $k => $v){
		if(!empty($v['indikator_teks'])){
			$indikator_tujuan .= '<div class="indikator_program">'.$v['indikator_teks'].button_edit_monev($v['id_unik'].'|'.$v['id_unik_indikator']).'</div>';
			$target_awal .= '<div class="indikator_program">'.$v['target_awal'].'</div>';
			$target_1 .= '<div class="indikator_program">'.$v['target_1'].'</div>';
			$target_2 .= '<div class="indikator_program">'.$v['target_2'].'</div>';
			$target_3 .= '<div class="indikator_program">'.$v['target_3'].'</div>';
			$target_4 .= '<div class="indikator_program">'.$v['target_4'].'</div>';
			$target_5 .= '<div class="indikator_program">'.$v['target_5'].'</div>';
			$target_akhir .= '<div class="indikator_program">'.$v['target_akhir'].'</div>';
			$satuan .= '<div class="indikator_program">'.$v['satuan'].'</div>';
			$indikator_catatan_tujuan .= '<div class="indikator_program">'.$v['indikator_catatan_teks'].'</div>';
		}
	}
	$target_html = "";
	for($i=1; $i<=$lama_pelaksanaan; $i++){
		$target_html .= '<td class="atas kanan bawah text_tengah">'.${'target_'.$i}.'</td>';
		$target_html .= '<td class="atas kanan bawah text_tengah"><b>('.$this->_number_format($tujuan['total_akumulasi_'.$i]).')</b></td>';
	}
	$warning = "";
	if(empty($tujuan['detail'][0]['id_isu'])){
		$warning = "style='background: #80000014;'";
	}
	$no_urut = '';
	if(!empty($tujuan['detail'][0]['no_urut'])){
		$no_urut = $tujuan['detail'][0]['no_urut'];
	}
	$catatan_teks_tujuan = '';
	if(!empty($tujuan['detail'][0]['catatan_teks_tujuan'])){
		$catatan_teks_tujuan = $tujuan['detail'][0]['catatan_teks_tujuan'];
	}
	$body .= '
		<tr class="tr-tujuan" '.$warning.'>
			<td class="kiri atas kanan bawah">'.$no_tujuan.'</td>
			<td class="atas kanan bawah">'.$tujuan['detail'][0]['isu_teks'].'</td>
			<td class="atas kanan bawah">'.parsing_nama_kode($tujuan['nama']).button_edit_monev($tujuan['detail'][0]['id_unik']).'</td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah">'.$indikator_tujuan.'</td>
			<td class="atas kanan bawah text_tengah">'.$target_awal.'</td>
			'.$target_html.'
			<td class="atas kanan bawah text_tengah">'.$target_akhir.'</td>
			<td class="atas kanan bawah">'.$satuan.'</td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah">'.$no_urut.'</td>
			<td class="atas kanan bawah">'.$catatan_teks_tujuan.'</td>
			<td class="atas kanan bawah">'.$indikator_catatan_tujuan.'</td>
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
		$indikator_catatan_sasaran = '';
		foreach($sasaran['detail'] as $k => $v){
			if(!empty($v['indikator_teks'])){
				$indikator_sasaran .= '<div class="indikator_program">'.$v['indikator_teks'].button_edit_monev($tujuan['detail'][0]['id_unik'].'||'.$v['id_unik'].'|'.$v['id_unik_indikator']).'</div>';
				$target_awal .= '<div class="indikator_program">'.$v['target_awal'].'</div>';
				$target_1 .= '<div class="indikator_program">'.$v['target_1'].'</div>';
				$target_2 .= '<div class="indikator_program">'.$v['target_2'].'</div>';
				$target_3 .= '<div class="indikator_program">'.$v['target_3'].'</div>';
				$target_4 .= '<div class="indikator_program">'.$v['target_4'].'</div>';
				$target_5 .= '<div class="indikator_program">'.$v['target_5'].'</div>';
				$target_akhir .= '<div class="indikator_program">'.$v['target_akhir'].'</div>';
				$satuan .= '<div class="indikator_program">'.$v['satuan'].'</div>';
				$indikator_catatan_sasaran .= '<div class="indikator_program">'.$v['indikator_catatan_teks'].'</div>';
			}
		}
		$target_html = "";
		for($i=1; $i<=$lama_pelaksanaan; $i++){
			$target_html .= '<td class="atas kanan bawah text_tengah">'.${'target_'.$i}.'</td>';
			$target_html .= '<td class="atas kanan bawah text_tengah"><b>('.$this->_number_format($sasaran['total_akumulasi_'.$i]).')</b></td>';
		}
		$sasaran_no_urut = '';
		if(!empty($sasaran['detail'][0]['sasaran_no_urut'])){
			$sasaran_no_urut = $sasaran['detail'][0]['sasaran_no_urut'];
		}
		$sasaran_catatan = '';
		if(!empty($sasaran['detail'][0]['sasaran_catatan'])){
			$sasaran_catatan = $sasaran['detail'][0]['sasaran_catatan'];
		}
		$body .= '
			<tr class="tr-sasaran" '.$warning.'>
				<td class="kiri atas kanan bawah">'.$no_tujuan.'.'.$no_sasaran.'</td>
				<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['detail'][0]['isu_teks'].'</span></td>
				<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['nama'].'</span></td>
				<td class="atas kanan bawah">'.parsing_nama_kode($sasaran['nama']).button_edit_monev($tujuan['detail'][0]['id_unik'].'||'.$sasaran['detail'][0]['id_unik']).'</td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah">'.$indikator_sasaran.'</td>
				<td class="atas kanan bawah text_tengah">'.$target_awal.'</td>
				'.$target_html.'
				<td class="atas kanan bawah text_tengah">'.$target_akhir.'</td>
				<td class="atas kanan bawah">'.$satuan.'</td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah">'.$sasaran_no_urut.'</td>
				<td class="atas kanan bawah">'.$sasaran_catatan.'</td>
				<td class="atas kanan bawah">'.$indikator_catatan_sasaran.'</td>
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
			$pagu_1 = array();
			$pagu_2 = array();
			$pagu_3 = array();
			$pagu_4 = array();
			$pagu_5 = array();
			$target_akhir = array();
			$satuan = array();
			$catatan = array();
			$nama_skpd = array();
			foreach ($program['data'] as $indikator_program) {
				$text_indikator[] = '<div class="indikator_program">'.$indikator_program['nama'].button_edit_monev($tujuan['detail'][0]['id_unik'].'||'.$sasaran['detail'][0]['id_unik'].'||'.$indikator_program['data']['id_unik'].'|'.$indikator_program['data']['id_unik_indikator']).'</div>';
				$target_awal[] = '<div class="indikator_program">'.$indikator_program['data']['target_awal'].'</div>';
				$target_1[] = '<div class="indikator_program">'.$indikator_program['data']['target_1'].'</div>';
				$target_2[] = '<div class="indikator_program">'.$indikator_program['data']['target_2'].'</div>';
				$target_3[] = '<div class="indikator_program">'.$indikator_program['data']['target_3'].'</div>';
				$target_4[] = '<div class="indikator_program">'.$indikator_program['data']['target_4'].'</div>';
				$target_5[] = '<div class="indikator_program">'.$indikator_program['data']['target_5'].'</div>';
				$pagu_1[] = '<div class="indikator_program">'.$this->_number_format($indikator_program['data']['pagu_1']).'</div>';
				$pagu_2[] = '<div class="indikator_program">'.$this->_number_format($indikator_program['data']['pagu_2']).'</div>';
				$pagu_3[] = '<div class="indikator_program">'.$this->_number_format($indikator_program['data']['pagu_3']).'</div>';
				$pagu_4[] = '<div class="indikator_program">'.$this->_number_format($indikator_program['data']['pagu_4']).'</div>';
				$pagu_5[] = '<div class="indikator_program">'.$this->_number_format($indikator_program['data']['pagu_5']).'</div>';
				$target_akhir[] = '<div class="indikator_program">'.$indikator_program['data']['target_akhir'].'</div>';
				$satuan[] = '<div class="indikator_program">'.$indikator_program['data']['satuan'].'</div>';
				$nama_skpd[] = '<div class="indikator_program">'.$indikator_program['data']['kode_skpd'].' '.$indikator_program['data']['nama_skpd'].'</div>';
				$catatan[] = '<div class="indikator_program">'.$indikator_program['data']['catatan'].'</div>';
			}
			$text_indikator = implode('', $text_indikator);
			$target_awal = implode('', $target_awal);
			$target_1 = implode('', $target_1);
			$target_2 = implode('', $target_2);
			$target_3 = implode('', $target_3);
			$target_4 = implode('', $target_4);
			$target_5 = implode('', $target_5);
			$pagu_1 = implode('', $pagu_1);
			$pagu_2 = implode('', $pagu_2);
			$pagu_3 = implode('', $pagu_3);
			$pagu_4 = implode('', $pagu_4);
			$pagu_5 = implode('', $pagu_5);
			$target_akhir = implode('', $target_akhir);
			$satuan = implode('', $satuan);
			$catatan_indikator_program = implode('', $catatan);
			$nama_skpd = implode('', $nama_skpd);
			$target_html = "";
			for($i=1; $i<=$lama_pelaksanaan; $i++){
				$target_html .= '<td class="atas kanan bawah text_tengah">'.${'target_'.$i}.'</td>';
				$target_html .= '<td class="atas kanan bawah text_tengah">'.${'pagu_'.$i}.'<div class="indikator_program"><b>('.$this->_number_format($program['total_akumulasi_'.$i]).')</b></div></td>';
			}
			$catatan_program = '';
			if(!empty($program['detail'][0]['catatan'])){
				$catatan_program = $program['detail'][0]['catatan'];
			}

			$isMutakhir='';
			if(!empty($add_rpd)){
				if($program['statusMutakhirProgram']){
					$isMutakhir='<button class="btn-sm btn-warning" onclick="tampilProgram(\''.$program['id_unik'].'\')" style="margin: 1px;"><i class="dashicons dashicons-update" title="Mutakhirkan"></i></button>';
				}
			}
			
			$body .= '
				<tr class="tr-program" data-kode-skpd="'.$program['kode_skpd'].'" '.$warning.'>
					<td class="kiri atas kanan bawah">'.$no_tujuan.'.'.$no_sasaran.'.'.$no_program.'</td>
					<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['detail'][0]['isu_teks'].'</span></td>
					<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['nama'].'</span></td>
					<td class="atas kanan bawah"><span class="debug-sasaran">'.$sasaran['nama'].'</span></td>
					<td class="atas kanan bawah">'.parsing_nama_kode($program['nama']).button_edit_monev($tujuan['detail'][0]['id_unik'].'||'.$sasaran['detail'][0]['id_unik'].'||'.$program['detail'][0]['id_unik'])." ".$isMutakhir.'</td>
					<td class="atas kanan bawah">'.$text_indikator.'</td>
					<td class="atas kanan bawah text_tengah">'.$target_awal.'</td>
					'.$target_html.'
					<td class="atas kanan bawah text_tengah">'.$target_akhir.'</td>
					<td class="atas kanan bawah text_tengah">'.$satuan.'</td>
					<td class="atas kanan bawah">'.$nama_skpd.'</td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah">'.$catatan_program.'</td>
					<td class="atas kanan bawah">'.$catatan_indikator_program.'</td>
				</tr>
			';
		}
	}
}

$warning_pemutakhiran_program = 'bg-success';
if($data_all['pemutakhiran_program'] > 0){
	$warning_pemutakhiran_program = 'bg-danger';
}
$table='<h4 class="text-center" style="margin-top:30px">Informasi Pemutakhiran Data</h4>
		<table class="table table-bordered" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 80%; border: 0; table-layout: fixed;margin:30px 0px 30px 0px; width:20%; margin-left:40%" contenteditable="false">
            <thead>
                <tr>
                    <th class="text-center">Program</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight:bold;; mso-number-format:\@;color:white;font-size:20px" class="text-center '.$warning_pemutakhiran_program.'">'.$data_all['pemutakhiran_program'].'</td>
                </tr>
            </tbody>
        </table>';

ksort($skpd_filter);
$skpd_filter_html = '<option value="">Pilih SKPD</option>';
foreach ($skpd_filter as $kode_skpd => $nama_skpd) {
	$skpd_filter_html .= '<option value="'.$kode_skpd.'">'.$kode_skpd.' '.$nama_skpd.'</option>';
}

$is_jadwal_set_integration_esakip = $this->is_jadwal_rpjmd_rpd_set_integration_esakip('rpd');

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
	.tr-tujuan {
	    background: #0000ff1f;
	}
	.tr-sasaran {
	    background: #ffff0059;
	}
	.tr-program {
	    background: #baffba;
	}
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitoring dan Evaluasi RPD (Rencana Pembangunan Daerah) <br><?php echo $nama_pemda; ?><br><?php echo $awal_rpd.' - '.$akhir_rpd; ?></h4>
<?php echo $table; ?>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; height: 80vh;">
	<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<tr>
				<th style="width: 85px;" class="atas kiri kanan bawah text_tengah text_blok">No</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Isu RPJPD</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Tujuan</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Sasaran</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Program</th>
				<th style="width: 400px;" class="atas kanan bawah text_tengah text_blok">Indikator</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Awal</th>
			<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				<th style="width: 300px;" class="atas kanan bawah text_tengah text_blok" colspan="2">Tahun <?php echo $i; ?></th>
			<?php }; ?> 
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Akhir</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Satuan</th>
				<th style="width: 150px;" class="atas kanan bawah text_tengah text_blok">Keterangan</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">No. Urut</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Catatan</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Indikator Catatan</th>
			</tr>
			<tr>
				<th rowspan="2" class='atas kiri kanan bawah text_tengah text_blok'>1</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>2</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>3</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>4</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>5</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>6</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>7</th>
			<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				<th class="atas kanan bawah text_tengah text_blok" colspan="2"><?php echo 7+$i; ?></th>
			<?php }; ?>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'><?php echo $i+7; ?></th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'><?php echo $i+8; ?></th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'><?php echo $i+9; ?></th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'><?php echo $i+10; ?></th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'><?php echo $i+11; ?></th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'><?php echo $i+12; ?></th>
			</tr>
			<tr>
			<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				<th class="atas kanan bawah text_tengah text_blok">Target</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Pagu</th>
			<?php }; ?>
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
	</table>
</div>
<div class="modal hide fade" id="modal-monev" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog" style="max-width: 1500px;" role="document">
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
<div class="modal hide fade" id="modal-tujuan" role="dialog" data-backdrop="static" aria-hidden="true">'
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
				    	<small class="form-text text-muted">Input teks tujuan RPD.</small>
				  	</div>
					<div class="form-group">
						<label>No Urut</label>
						<input class="form-control" type="number" id="no-urut-teks-tujuan"></input>
					</div>
					<div class="form-group">
						<label>Catatan Teks</label>
						<textarea class="form-control" id="catatan-teks-tujuan"></textarea>
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
<div class="modal hide fade" id="modal-tujuan-indikator" role="dialog" data-backdrop="static" aria-hidden="true">'
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
					<div class="form-group">
						<label>Satuan</label>
						<input class="form-control" id="indikator-teks-tujuan-satuan" type="text">
					</div>
				  	<div class="form-group">
						<label>Target Awal</label>
						<input class="form-control" id="indikator-teks-tujuan-vol-awal" type="number">
					</div>
				<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
					<div class="form-group">
						<label>Target <?php echo $i; ?></label>
						<input class="form-control" id="indikator-teks-tujuan-vol-<?php echo $i; ?>" type="number">
					</div>
				<?php }; ?>
					<div class="form-group">
						<label>Target Akhir</label>
						<input class="form-control" id="indikator-teks-tujuan-vol-akhir" type="number">
					</div>
					<div class="form-group">
						<label>Catatan Teks</label>
						<textarea class="form-control" id="indikator-teks-tujuan-catatan" type="text"></textarea>
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
<div class="modal hide fade" id="modal-sasaran" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPD Sasaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<table class="table table-bordered">
            		<tbody>
            			<tr>
            				<th style="width: 175px;">Tujuan RPD</th>
            				<td id="tujuan-sasaran-teks"></td>
            			</tr>
            		</tbody>
            	</table>
            	<form>
				  	<div class="form-group">
				    	<label>Sasaran Teks</label>
				    	<textarea class="form-control" id="sasaran-teks"></textarea>
				    	<small class="form-text text-muted">Input teks sasaran RPD.</small>
				  	</div>
					<div class="form-group">
				    	<label>No Urut</label>
				    	<input class="form-control" id="sasaran-no-urut" type="number"></input>
				  	</div>
					<div class="form-group">
				    	<label>Catatan Teks</label>
				    	<textarea class="form-control" id="sasaran-catatan-teks"></textarea>
				  	</div>
				</form>
            </div>
            <div class="modal-footer">
            	<button class="btn btn-primary" onclick="simpan_sasaran();">Simpan</button>
            	<button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="modal hide fade" id="modal-sasaran-indikator" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPD Indikator Sasaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<table class="table table-bordered">
            		<tbody>
            			<tr>
            				<th style="width: 175px;">Tujuan RPD</th>
            				<td class="text-center">:</td>
            				<td id="tujuan-sasaran-teks-indikator"></td>
            			</tr>
            			<tr>
            				<th>Sasaran RPD</th>
            				<td class="text-center">:</td>
            				<td id="sasaran-teks-indikator"></td>
            			</tr>
            		</tbody>
            	</table>
            	<form>
				  	<div class="form-group">
				    	<label>Indikator Teks</label>
				    	<input class="form-control" id="indikator-teks-sasaran" type="text">
				  	</div>
					<div class="form-group">
						<label>Satuan</label>
						<input class="form-control" id="indikator-teks-sasaran-satuan" type="text">
					</div>
					<div class="form-group">
						<label>Target Awal</label>
						<input class="form-control" id="indikator-teks-sasaran-vol-awal" type="number">
					</div>
				<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
					<div class="form-group">
						<label>Target <?php echo $i; ?></label>
						<input class="form-control" id="indikator-teks-sasaran-vol-<?php echo $i; ?>" type="number">
					</div>
				<?php }; ?>
					<div class="form-group">
						<label>Target Akhir</label>
						<input class="form-control" id="indikator-teks-sasaran-vol-akhir" type="number">
					</div>
					<div class="form-group">
						<label>Input catatan teks</label>
						<textarea class="form-control" id="indikator-catatan-teks-sasaran" type="text"></textarea>
					</div>
				</form>
            </div>
            <div class="modal-footer">
            	<button class="btn btn-primary" onclick="simpan_sasaran_indikator();">Simpan</button>
            	<button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="modal hide fade" id="modal-program" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPD Program</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<table class="table table-bordered">
            		<tbody>
            			<tr>
            				<th style="width: 175px;">Tujuan RPD</th>
            				<td id="tujuan-program-teks"></td>
            			</tr>
            			<tr>
            				<th>Sasaran RPD</th>
            				<td id="sasaran-program-teks"></td>
            			</tr>
            		</tbody>
            	</table>
            	<form>
				  	<div class="form-group">
				    	<label>Pilih Urusan</label>
				    	<select class="form-control" id="urusan-teks"></select>
				  	</div>
				  	<div class="form-group">
				    	<label>Pilih Bidang</label>
				    	<select class="form-control" id="bidang-teks"></select>
				  	</div>
				  	<div class="form-group">
				    	<label>Pilih Program</label>
				    	<select class="form-control" id="program-teks"></select>
				  	</div>
					<div class="form-group">
						<label>Input catatan teks</label>
						<textarea class="form-control" id="catatan-teks-program"></textarea>
					</div>
				</form>
            </div>
            <div class="modal-footer">
            	<button class="btn btn-primary" onclick="simpan_program();">Simpan</button>
            	<button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="modal hide fade" id="modal-program-indikator" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPD Indikator Program</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<table class="table table-bordered">
            		<tbody>
            			<tr>
            				<th style="width: 175px;">Tujuan RPD</th>
            				<td id="program-tujuan-teks-indikator"></td>
            			</tr>
            			<tr>
            				<th>Sasaran RPD</th>
            				<td id="program-sasaran-teks-indikator"></td>
            			</tr>
            			<tr>
            				<th>Program RPD</th>
            				<td id="program-teks-indikator"></td>
            			</tr>
            		</tbody>
            	</table>
            	<form>
				  	<div class="form-group">
				    	<label>Pilih SKPD</label>
				    	<select class="form-control" id="skpd-teks"></select>
				  	</div>
				  	<div class="form-group">
				    	<label>Indikator Teks</label>
				    	<input class="form-control" id="indikator-teks-program" type="text">
				  	</div>
					<div class="form-group">
						<label>Satuan</label>
						<input class="form-control" id="indikator-teks-program-satuan" type="text">
					</div>
				  	<div class="form-group">
						<label>Target Awal</label>
						<input class="form-control" id="indikator-teks-program-vol-awal" type="number">
					</div>
				<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				  	<div class="form-row">
					  	<div class="form-group col-md-6">
					    	<label>Target <?php echo $i; ?></label>
					    	<input class="form-control" id="indikator-teks-program-vol-<?php echo $i; ?>" type="number">
					  	</div>
					  	<div class="form-group col-md-6">
					    	<label>Pagu <?php echo $i; ?></label>
					    	<input class="form-control" id="indikator-teks-program-pagu-<?php echo $i; ?>" type="number">
					  	</div>
					</div>
				<?php }; ?>
					<div class="form-group">
						<label>Target Akhir</label>
						<input class="form-control" id="indikator-teks-program-vol-akhir" type="number">
					</div>
					<div class="form-group">
						<label>Input catatan teks</label>
						<textarea class="form-control" id="indikator-catatan-teks-program"></textarea>
					</div>
				</form>
            </div>
            <div class="modal-footer">
            	<button class="btn btn-primary" onclick="simpan_program_indikator();">Simpan</button>
            	<button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-raw" tabindex="-2" role="dialog" aria-labelledby="modal-crud-rpjm-label" aria-hidden="true">
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

<script type="text/javascript">
	run_download_excel();
	let data_all = <?php echo json_encode($data_all); ?>;
	let is_jadwal_set_integration_esakip = <?php echo json_encode($is_jadwal_set_integration_esakip); ?>;

	var mySpace = '<div style="padding:3rem;"></div>';
	window.edit_val = false;
	
	jQuery('body').prepend(mySpace);

	var dataHitungMundur = {
		'namaJadwal' : '<?php echo ucwords($data_jadwal->nama); ?>',
		'mulaiJadwal' : '<?php echo $data_jadwal->waktu_awal; ?>',
		'selesaiJadwal' : '<?php echo $data_jadwal->waktu_akhir; ?>',
		'thisTimeZone' : '<?php echo $timezone ?>'
	}

	penjadwalanHitungMundur(dataHitungMundur);

	var aksi = ''
		+'<?php if($is_running_jadwal_lokal): ?><a id="singkron-sipd" onclick="return false;" href="#" class="btn btn-danger mr-2"><span class="dashicons dashicons-database-import"></span> Ambil data dari SIPD lokal</a><?php endif; ?>'
		+'<?php if($is_running_jadwal_lokal): ?><?php echo $add_rpd; ?><?php endif; ?>'
		+'<?php if($is_running_jadwal_lokal): ?><a id="generate-data-program-renstra" onclick="return false;" href="#" class="btn btn-warning mr-2"><span class="dashicons dashicons-admin-generic"></span> Generate Data Program Dari RENSTRA</a><?php endif; ?>'
		+'<?php if($is_running_jadwal_lokal && $is_jadwal_set_integration_esakip): ?><a id="generate-data-rpd-esakip" onclick="return false;" href="#" class="btn btn-success mr-2"><span class="dashicons dashicons-admin-generic"></span> Generate Data RPD dari WP-Eval-Sakip</a><?php endif; ?>'
		+'<h3 style="margin-top: 20px;">PENGATURAN</h3>'
		+'<?php if($is_running_jadwal_lokal): ?><label><input type="checkbox" onclick="tampilkan_edit(this);"> Edit Data RPD</label><?php endif; ?>'
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
		tr_sasaran.show();
		tr_program.show();
		if(val == 'tr-sasaran'){
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
		var data_id = jQuery(this).attr('data-id').split('||');
		if(data_id[2]){
			tampil_detail_popup(function(){
				detail_tujuan(data_id[0], function(){
					detail_sasaran(data_id[1], function(){
						var program = data_id[2].split('|');
						if(program[1]){
							edit_program_indikator(program[1]);
						}else{
							edit_program(program[0]);
						}
					});
				});
			});
		}else if(data_id[1]){
			tampil_detail_popup(function(){
				detail_tujuan(data_id[0], function(){
					var sasaran = data_id[1].split('|');
					if(sasaran[1]){
						edit_sasaran_indikator(sasaran[1]);
					}else{
						edit_sasaran(sasaran[0]);
					}
				});
			});
		}else if(data_id[0]){
			tampil_detail_popup(function(){
				var tujuan = data_id[0].split('|');
				if(tujuan[1]){
					edit_tujuan_indikator(tujuan[1]);
				}else{
					edit_tujuan(tujuan[0]);
				}
			});
		}
	});
	jQuery('#singkron-sipd').on('click', function(){
		if(confirm('Apakah anda yakin untuk mengambil data dari SIPD lokal? data lama akan diupdate!')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "singkron_rpd_sipd_lokal",
	          		"api_key": ajax.api_key,
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
          		"api_key": ajax.api_key,
          		"table": "data_rpd_tujuan_lokal{$prefix_history}",
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
							<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<th class='text-center'>Pagu Tahun <?php echo $i; ?></th>"
							<?php }; ?>
								+"<th class='text-center'>Catatan</th>"
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
					<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
						+"<td class='text-right'>"+formatRupiah(res.data_all[b].pagu_akumulasi_<?php echo $i; ?>)+"</td>"
					<?php }; ?>
						+'<td>'+res.data_all[b].catatan_teks_tujuan+'</td>'
						+'<td class="text-center aksi">'
							+'<button class="btn-sm btn-primary" onclick="detail_tujuan(\''+res.data_all[b].id_unik+'\');" title="Lihat Sasaran"><i class="dashicons dashicons-search"></i></button>'
							+'<button class="btn-sm btn-primary" onclick="tambah_tujuan_indikator(\''+res.data_all[b].id_unik+'\');" title="Tambah Indikator Tujuan"><i class="dashicons dashicons-plus"></i></button>'
							+'<button class="btn-sm btn-warning" onclick="edit_tujuan(\''+res.data_all[b].id_unik+'\');" title="Edit Tujuan"><i class="dashicons dashicons-edit"></i></button>'
							+'<button class="btn-sm btn-danger" onclick="hapus_tujuan(\''+res.data_all[b].id_unik+'\');" title="Hapus Tujuan"><i class="dashicons dashicons-trash"></i></button>'
						+'</td>'
					+'</tr>';

					if(res.data_all[b].detail.length > 0){
						data_html += ''
						+'<tr style="background: #80000014;">'
							+'<td colspan="<?php echo $lama_pelaksanaan+4; ?>" style="padding: 0;">'
								+"<table class='table table-bordered'>"
									+"<thead>"
										+"<tr>"
											+"<th class='text-center' style='width: 45px;'></th>"
											+"<th class='text-center'>Indikator</th>"
											+"<th class='text-center'>Awal</th>"
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+"<th class='text-center'>Tahun <?php echo $i; ?></th>"
										<?php }; ?>
											+"<th class='text-center'>Akhir</th>"
											+"<th class='text-center'>Satuan</th>"
											+"<th class='text-center'>Catatan</th>"
											+"<th class='text-center' style='width: 110px;'>Aksi</th>"
										+"</tr>"
									+"</thead>"
									+"<tbody>";
						res.data_all[b].detail.map(function(bb, i){
							var satuan = '';
							if(bb.satuan != null){
								satuan = bb.satuan;
							}
							var catatan = '';
							if(bb.indikator_catatan_teks != null){
								catatan = bb.indikator_catatan_teks;
							}
							data_html += ''
							+'<tr id-tujuan-indikator="'+bb.id_unik_indikator+'">'
								+'<td class="text-center">'+no+'.'+(i+1)+'</td>'
								+'<td>'+bb.indikator_teks+'</td>'
								+'<td class="text-center">'+bb.target_awal+'</td>'
							<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+'<td class="text-center">'+bb.target_<?php echo $i; ?>+'</td>'
							<?php }; ?>
								+'<td class="text-center">'+bb.target_akhir+'</td>'
								+'<td class="text-center">'+satuan+'</td>'
								+'<td>'+catatan+'</td>'
								+'<td class="text-center aksi">'
									+'<button class="btn-sm btn-warning" onclick="edit_tujuan_indikator(\''+bb.id_unik_indikator+'\');" title="Edit Indikator Tujuan"><i class="dashicons dashicons-edit"></i></button>'
									+'<button class="btn-sm btn-danger" onclick="hapus_tujuan_indikator(\''+bb.id_unik_indikator+'\');" title="Hapus Indikator Tujuan"><i class="dashicons dashicons-trash"></i></button>'
								+'</td>'
							+'</tr>';
						});
						data_html += ""
									+"</tbody>"
								+"</table>"
							+"</td>"
						+"</tr>";
					}
				};
				data_html += ""
						+"</tbody>"
					+"</table>";
				jQuery('#nav-tujuan').html(data_html);
          		jQuery('.nav-tabs a[href="#nav-tujuan"]').tab('show');
		        if(typeof cb == 'function'){
		        	cb();
		        }
          	}
        });
	}

	function detail_tujuan(id_unik_tujuan, cb){
		jQuery('#wrap-loading').show();
		jQuery('#modal-monev').modal('show');
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": ajax.api_key,
          		"table": "data_rpd_sasaran_lokal{$prefix_history}",
          		"id_unik_tujuan": id_unik_tujuan,
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
				console.log('res', res);
				jQuery('#wrap-loading').hide();
				var data_html = ""
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<tbody>"
							+"<tr>"
								+"<th class='text-center' style='width:175px;'>Tujuan</th>"
								+"<td>"+jQuery('tr[id-tujuan="'+id_unik_tujuan+'"] td').eq(1).html()+"</td>"
							+"</tr>"
						+"</tbody>"
					+"</table>"
					+'<button class="btn-sm btn-primary" style="margin-top: 10px;" onclick="tambah_sasaran(\''+id_unik_tujuan+'\');" id-tujuan="'+id_unik_tujuan+'" id="tambah-data-sasaran"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah sasaran</button>'
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center' style='width: 45px;'>No</th>"
								+"<th class='text-center'>Sasaran</th>"
							<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<th class='text-center'>Pagu Tahun <?php echo $i; ?></th>"
							<?php }; ?>
								+"<th class='text-center'>Catatan</th>"
								+"<th class='text-center' style='width: 195px;'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody>";
				var no = 0;
				for(var b in res.data_all){
					no++;
					data_html += ''
					+'<tr id-sasaran="'+res.data_all[b].id_unik+'">'
						+'<td class="text-center">'+(no)+'</td>'
						+'<td>'+res.data_all[b].nama+'</td>'
					<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
						+"<td class='text-right'>"+formatRupiah(res.data_all[b].pagu_akumulasi_<?php echo $i; ?>)+"</td>"
					<?php }; ?>
						+'<td>'+res.data_all[b].sasaran_catatan+'</td>'
						+'<td class="text-center aksi">'
							+'<button class="btn-sm btn-primary" onclick="detail_sasaran(\''+res.data_all[b].id_unik+'\');" title="Lihat Program"><i class="dashicons dashicons-search"></i></button>'
							+'<button class="btn-sm btn-primary" onclick="tambah_sasaran_indikator(\''+res.data_all[b].id_unik+'\');" title="Tambah Indikator Sasaran"><i class="dashicons dashicons-plus"></i></button>'
							+'<button class="btn-sm btn-warning" onclick="edit_sasaran(\''+res.data_all[b].id_unik+'\');" title="Edit Sasaran"><i class="dashicons dashicons-edit"></i></button>'
							+'<button class="btn-sm btn-danger" onclick="hapus_sasaran(\''+res.data_all[b].id_unik+'\');" title="Hapus Sasaran"><i class="dashicons dashicons-trash"></i></button>'
						+'</td>'
					+'</tr>';

					if(res.data_all[b].detail.length > 0){
						data_html += ''
						+'<tr style="background: #80000014;">'
							+'<td colspan="<?php echo $lama_pelaksanaan+4; ?>" style="padding: 0;">'
								+"<table class='table table-bordered'>"
									+"<thead>"
										+"<tr>"
											+"<th class='text-center' style='width: 45px;'></th>"
											+"<th class='text-center'>Indikator</th>"
											+"<th class='text-center'>Awal</th>"
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+"<th class='text-center'>Tahun <?php echo $i; ?></th>"
										<?php }; ?>
											+"<th class='text-center'>Akhir</th>"
											+"<th class='text-center'>Satuan</th>"
											+"<th class='text-center'>Catatan</th>"
											+"<th class='text-center' style='width: 110px;'>Aksi</th>"
										+"</tr>"
									+"</thead>"
									+"<tbody>";
						res.data_all[b].detail.map(function(bb, i){
							var satuan = '';
							if(bb.satuan != null){
								satuan = bb.satuan;
							}
							var catatan = '';
							if(bb.indikator_catatan_teks != null){
								catatan = bb.indikator_catatan_teks;
							}
							data_html += ''
							+'<tr id-sasaran-indikator="'+bb.id_unik_indikator+'">'
								+'<td class="text-center">'+no+'.'+(i+1)+'</td>'
								+'<td>'+bb.indikator_teks+'</td>'
								+'<td class="text-center">'+bb.target_awal+'</td>'
							<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+'<td class="text-center">'+bb.target_<?php echo $i; ?>+'</td>'
							<?php }; ?>
								+'<td class="text-center">'+bb.target_akhir+'</td>'
								+'<td class="text-center">'+satuan+'</td>'
								+'<td>'+catatan+'</td>'
								+'<td class="text-center aksi">'
									+'<button class="btn-sm btn-warning" onclick="edit_sasaran_indikator(\''+bb.id_unik_indikator+'\');" title="Edit Indikator Sasaran"><i class="dashicons dashicons-edit"></i></button>'
									+'<button class="btn-sm btn-danger" onclick="hapus_sasaran_indikator(\''+bb.id_unik_indikator+'\');" title="Hapus Indikator Sasaran"><i class="dashicons dashicons-trash"></i></button>'
								+'</td>'
							+'</tr>';
						});
						data_html += ""
									+"</tbody>"
								+"</table>"
							+"</td>"
						+"</tr>";
					}
				};
				data_html += ""
						+"</tbody>"
					+"</table>";
				jQuery('#nav-sasaran').html(data_html);
          		jQuery('.nav-tabs a[href="#nav-sasaran"]').tab('show');
		        if(typeof cb == 'function'){
		        	cb();
		        }
          	}
        });
	}

	function detail_sasaran(id_unik_sasaran, cb){
		jQuery('#wrap-loading').show();
		jQuery('#modal-monev').modal('show');
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": ajax.api_key,
          		"table": "data_rpd_program_lokal{$prefix_history}",
          		"id_unik_sasaran": id_unik_sasaran,
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
				console.log('res', res);
				jQuery('#wrap-loading').hide();
				var data_html = ""
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<tbody>"
							+"<tr>"
								+"<th class='text-center' style='width:175px;'>Tujuan</th>"
								+"<td>"+jQuery('tr[id-tujuan="'+jQuery('#tambah-data-sasaran').attr('id-tujuan')+'"] td').eq(1).html()+"</td>"
							+"</tr>"
							+"<tr>"
								+"<th class='text-center'>Sasaran</th>"
								+"<td>"+jQuery('tr[id-sasaran="'+id_unik_sasaran+'"] td').eq(1).html()+"</td>"
							+"</tr>"
						+"</tbody>"
					+"</table>"
					+'<button class="btn-sm btn-primary" style="margin-top: 10px;" onclick="tambah_program(\''+id_unik_sasaran+'\');" id-sasaran="'+id_unik_sasaran+'" id="tambah-data-program"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah proram</button>'
					+"<table class='table table-bordered' style='margin: 10px 0;'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center' style='width: 45px;'>No</th>"
								+"<th class='text-center'>Program</th>"
							<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<th class='text-center'>Pagu Tahun <?php echo $i; ?></th>"
							<?php }; ?>
								+"<th class='text-center'>Catatan</th>"
								+"<th class='text-center' style='width: 150px;'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody>";
				var no = 0;
				for(var b in res.data_all){
					var catatan = '';
					if(res.data_all[b].catatan != null){
						catatan = res.data_all[b].catatan;
					}
					no++;
					data_html += ''
					+'<tr id-program="'+res.data_all[b].id_unik+'" id-program-master="'+res.data_all[b].id_program+'">'
						+'<td class="text-center">'+(no)+'</td>'
						+'<td>'+res.data_all[b].nama+'</td>'
					<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
						+"<td class='text-right'>"+formatRupiah(res.data_all[b].pagu_akumulasi_<?php echo $i; ?>)+"</td>"
					<?php }; ?>
						+'<td>'+catatan+'</td>'
						+'<td class="text-center aksi">'
							+'<button class="btn-sm btn-primary" onclick="tambah_program_indikator(\''+res.data_all[b].id_unik+'\');" title="Tambah Indikator Program"><i class="dashicons dashicons-plus"></i></button>'
							+'<button class="btn-sm btn-warning" onclick="edit_program(\''+res.data_all[b].id_unik+'\');" title="Edit Program"><i class="dashicons dashicons-edit"></i></button>'
							+'<button class="btn-sm btn-danger" onclick="hapus_program(\''+res.data_all[b].id_unik+'\');" title="Hapus Program"><i class="dashicons dashicons-trash"></i></button>'
						+'</td>'
					+'</tr>';

					if(res.data_all[b].detail.length > 0){
						data_html += ''
						+'<tr style="background: #80000014;">'
							+'<td colspan="<?php echo $lama_pelaksanaan+4; ?>" style="padding: 0;">'
								+"<table class='table table-bordered' style='margin: 0;'>"
									+"<thead>"
										+"<tr>"
											+"<th rowspan='2' class='text-center' style='width: 45px;'></th>"
											+"<th rowspan='2' class='text-center'>Indikator</th>"
											+"<th rowspan='2' class='text-center'>Satuan</th>"
											+"<th rowspan='2' class='text-center'>Awal</th>"
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+"<th colspan='2' class='text-center'>Tahun <?php echo $i; ?></th>"
										<?php }; ?>
											+"<th rowspan='2' class='text-center'>Akhir</th>"
											+"<th rowspan='2' class='text-center'>SKPD</th>"
											+"<th rowspan='2' class='text-center'>Catatan</th>"
											+"<th rowspan='2' class='text-center' style='width: 110px;'>Aksi</th>"
										+"</tr>"
										+"<tr>"
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+"<th class='text-center'>Target</th>"
											+"<th class='text-center'>Pagu</th>"
										<?php }; ?>
										+"</tr>"
									+"</thead>"
									+"<tbody>";
						res.data_all[b].detail.map(function(bb, i){
							var catatan = '';
							if(bb.catatan != null){
								catatan = bb.catatan;
							}
							data_html += ''
							+'<tr id-program-indikator="'+bb.id_unik_indikator+'">'
								+'<td class="text-center">'+no+'.'+(i+1)+'</td>'
								+'<td style="width:250px;">'+bb.indikator+'</td>'
								+'<td class="text-center">'+bb.satuan+'</td>'
								+'<td class="text-center">'+bb.target_awal+'</td>'
							<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+'<td class="text-center">'+bb.target_<?php echo $i; ?>+'</td>'
								+'<td class="text-right">'+formatRupiah(bb.pagu_<?php echo $i; ?>)+'</td>'
							<?php }; ?>
								+'<td class="text-center">'+bb.target_akhir+'</td>'
								+'<td style="width:250px;">'+bb.kode_skpd+' '+bb.nama_skpd+'</td>'
								+'<td style="width:85px;">'+catatan+'</td>'
								+'<td class="text-center aksi">'
									+'<button class="btn-sm btn-warning" onclick="edit_program_indikator(\''+bb.id_unik_indikator+'\');" title="Edit Indikator Program"><i class="dashicons dashicons-edit"></i></button>'
									+'<button class="btn-sm btn-danger" onclick="hapus_program_indikator(\''+bb.id_unik_indikator+'\');" title="Hapus Indikator Program"><i class="dashicons dashicons-trash"></i></button>'
								+'</td>'
							+'</tr>';
						});
						data_html += ""
									+"</tbody>"
								+"</table>"
							+"</td>"
						+"</tr>";
					}
				};
				data_html += ""
						+"</tbody>"
					+"</table>";
				jQuery('#nav-program').html(data_html);
          		jQuery('.nav-tabs a[href="#nav-program"]').tab('show');
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
          		"api_key": ajax.api_key,
          		"table": "data_rpd_tujuan_lokal{$prefix_history}",
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
	  			jQuery('#indikator-teks-tujuan').val('');
				jQuery('#indikator-teks-tujuan-satuan').val('');
				jQuery('#indikator-teks-tujuan-vol-awal').val('');
			<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				jQuery('#indikator-teks-tujuan-vol-<?php echo $i; ?>').val('');
			<?php }; ?>
				jQuery('#indikator-teks-tujuan-vol-akhir').val('');
				jQuery('#indikator-teks-tujuan-catatan').val('');
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
					jQuery('#modal-tujuan-indikator').attr('data-id', '');
				}
				jQuery('#wrap-loading').hide();
			}
		});
	}

	function tambah_sasaran_indikator(id_sasaran){
		jQuery('#wrap-loading').show();
  		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": ajax.api_key,
          		"table": "data_rpd_sasaran_lokal{$prefix_history}",
          		"id_unik_sasaran": id_sasaran,
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
	  			jQuery('#tujuan-sasaran-teks-indikator').html(jQuery('tr[id-tujuan="'+jQuery('#tambah-data-sasaran').attr('id-tujuan')+'"] td').eq(1).html());
				jQuery('#sasaran-teks-indikator').html(res.data[0].sasaran_teks);
				jQuery('#indikator-teks-sasaran').val('');
				jQuery('#indikator-teks-sasaran-satuan').val('');
				jQuery('#indikator-teks-sasaran-vol-awal').val('');
			<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				jQuery('#indikator-teks-sasaran-vol-<?php echo $i; ?>').val('');
			<?php }; ?>
				jQuery('#indikator-teks-sasaran-vol-akhir').val('');
				jQuery('#modal-sasaran-indikator').attr('id-sasaran', id_sasaran);
				jQuery('#modal-sasaran-indikator').attr('data-id', '');
				jQuery('#modal-sasaran-indikator').modal('show');
				jQuery('#wrap-loading').hide();
			}
		});
	}

	function tambah_program_indikator(id_program){
		jQuery('#wrap-loading').show();
  		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": ajax.api_key,
          		"table": "data_rpd_program_lokal{$prefix_history}",
          		"id_unik_program": id_program,
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
	  			jQuery('#program-tujuan-teks-indikator').html(jQuery('tr[id-tujuan="'+jQuery('#tambah-data-sasaran').attr('id-tujuan')+'"] td').eq(1).html());
	  			jQuery('#program-sasaran-teks-indikator').html(jQuery('tr[id-sasaran="'+jQuery('#tambah-data-program').attr('id-sasaran')+'"] td').eq(1).html());
				jQuery('#program-teks-indikator').html(res.data[0].nama_program);
				get_bidang_urusan(true).then(function(){
					get_skpd(res.data[0].id_program);
					jQuery('#indikator-teks-program').val('');
					jQuery('#indikator-teks-program-vol-awal').val('');
					jQuery('#indikator-teks-program-satuan').val('');
					jQuery('#indikator-teks-program-satuan-awal').val('');
				<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
					jQuery('#indikator-teks-program-vol-<?php echo $i; ?>').val('');
					jQuery('#indikator-teks-program-satuan-<?php echo $i; ?>').val('');
					jQuery('#indikator-teks-program-pagu-<?php echo $i; ?>').val('');
				<?php }; ?>
					jQuery('#indikator-teks-program-vol-akhir').val('');
					jQuery('#indikator-teks-program-satuan-akhir').val('');
					jQuery('#indikator-catatan-teks-program').val('');
					jQuery('#modal-program-indikator').attr('id-program', id_program);
					jQuery('#modal-program-indikator').attr('data-id', '');
					jQuery('#modal-program-indikator').modal('show');
					jQuery('#wrap-loading').hide();
				});
			}
		});
	}

	function get_skpd(current_id_program, current_id_skpd){
		var selected = "";
		if(current_id_skpd == '*'){
			selected = "selected";
		}
		var html = ""
			+"<option value=''>Pilih SKPD</option>";
			// +"<option "+selected+" data-kode='' value='*'>Semua Perangkat Daerah</option>";
		if(current_id_program && all_skpd_program[current_id_program]){
			all_skpd_program[current_id_program].map(function(program){
				var selected = '';
				if(current_id_skpd == program.id_skpd){
					selected = 'selected';
				}
				html += "<option "+selected+" value='"+program.id_skpd+"' data-kode='"+program.kode_skpd+"'>"+program.nama_skpd+"</option>";
			})
		}else{
			for(var id_skpd in all_skpd){
				html += "<option value='"+all_skpd[id_skpd].id_skpd+"' data-kode='"+all_skpd[id_skpd].kode_skpd+"'>"+all_skpd[id_skpd].nama_skpd+"</option>";
			}
		}
		jQuery('#skpd-teks').html(html);
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
			jQuery('#no-urut-teks-tujuan').val('');
			jQuery('#catatan-teks-tujuan').val('');
		});
	}

	function tambah_sasaran(id_unik_tujuan){
		jQuery('#tujuan-sasaran-teks').html(jQuery('tr[id-tujuan="'+id_unik_tujuan+'"] td').eq(1).html());
		jQuery('#modal-sasaran').attr('id-tujuan', id_unik_tujuan);
		jQuery('#modal-sasaran').attr('data-id', '');
		jQuery('#sasaran-teks').val('');
		jQuery('#modal-sasaran').modal('show');
	}

	function tambah_program(id_unik_sasaran){
		jQuery('#wrap-loading').show();
  		get_bidang_urusan().then(function(){
			jQuery('#tujuan-program-teks').html(jQuery('tr[id-tujuan="'+jQuery('#tambah-data-sasaran').attr('id-tujuan')+'"] td').eq(1).html());
			jQuery('#sasaran-program-teks').html(jQuery('tr[id-sasaran="'+id_unik_sasaran+'"] td').eq(1).html());
			jQuery('#modal-program').attr('id-sasaran', id_unik_sasaran);
			jQuery('#modal-program').attr('data-id', '');
			get_urusan()
			get_bidang();
			get_program();
			jQuery('#catatan-teks-program').val('');
			jQuery('#modal-program').modal('show');
			jQuery('#wrap-loading').hide();
		});
	}

	jQuery('#urusan-teks').on('change', function(){
		get_bidang(jQuery(this).val());
		get_program();
	});

	jQuery('#bidang-teks').on('change', function(){
		get_program(jQuery(this).val());
	});

	function get_urusan(tag = 'urusan-teks') {
		var html = '<option value="">Pilih Urusan</option>';
		for(var nm_urusan in all_program){
			html += '<option>'+nm_urusan+'</option>';
		}
		jQuery('#'+tag).html(html).select2({width: '100%'});
	}

	function get_bidang(nm_urusan, tag = 'bidang-teks') {
		var html = '<option value="">Pilih Bidang</option>';
		if(nm_urusan){
			for(var nm_bidang in all_program[nm_urusan.trim()]){
				html += '<option>'+nm_bidang+'</option>';
			}
		}else{
			for(var nm_urusan in all_program){
				for(var nm_bidang in all_program[nm_urusan.trim()]){
					html += '<option>'+nm_bidang+'</option>';
				}
			}
		}
		jQuery('#'+tag).html(html).select2({width: '100%'});
	}

	function get_program(nm_bidang, val, tag = 'program-teks', tag2 = 'urusan-teks') {
		var html = '<option value="">Pilih Program</option>';
		var current_nm_urusan = jQuery('#'+tag2).val().trim();
		if(current_nm_urusan){
			if(nm_bidang){
				for(var nm_program in all_program[current_nm_urusan][nm_bidang]){
					var selected = '';
					if(val && val == all_program[current_nm_urusan][nm_bidang][nm_program].id_program){
						selected = 'selected';
					}
					html += '<option '+selected+' value="'+all_program[current_nm_urusan][nm_bidang][nm_program].id_program+'">'+nm_program+'</option>';
				}
			}else{
				for(var nm_bidang in all_program[current_nm_urusan]){
					for(var nm_program in all_program[current_nm_urusan][nm_bidang]){
						var selected = '';
						if(val && val == all_program[current_nm_urusan][nm_bidang][nm_program].id_program){
							selected = 'selected';
						}
						html += '<option '+selected+' value="'+all_program[current_nm_urusan][nm_bidang][nm_program].id_program+'">'+nm_program+'</option>';
					}
				}
			}
		}else{
			if(nm_bidang){
				for(var nm_urusan in all_program){
					if(all_program[nm_urusan][nm_bidang]){
						for(var nm_program in all_program[nm_urusan][nm_bidang]){
							var selected = '';
							if(val && val == all_program[nm_urusan][nm_bidang][nm_program].id_program){
								selected = 'selected';
							}
							html += '<option '+selected+' value="'+all_program[nm_urusan][nm_bidang][nm_program].id_program+'">'+nm_program+'</option>';
						}
					}
				}
			}else{
				for(var nm_urusan in all_program){
					for(var nm_bidang in all_program[nm_urusan]){
						for(var nm_program in all_program[nm_urusan][nm_bidang]){
							var selected = '';
							if(val && val == all_program[nm_urusan][nm_bidang][nm_program].id_program){
								selected = 'selected';
							}
							html += '<option '+selected+' value="'+all_program[nm_urusan][nm_bidang][nm_program].id_program+'">'+nm_program+'</option>';
						}
					}
				}
			}
		}
		jQuery('#'+tag).html(html).select2({width: '100%'});
	}

	function edit_tujuan(id_unik_tujuan){
		jQuery('#wrap-loading').show();
  		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": ajax.api_key,
          		"table": "data_rpd_tujuan_lokal{$prefix_history}",
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
					jQuery('#no-urut-teks-tujuan').val(res.data_all[b].no_urut);
					jQuery('#catatan-teks-tujuan').val(res.data_all[b].catatan_teks_tujuan);
		  		}
				jQuery('#modal-tujuan').attr('data-id', id_unik_tujuan);
				jQuery('#modal-tujuan').modal('show');
			}
		});
	}

	function edit_sasaran(id_unik_sasaran){
		jQuery('#wrap-loading').show();
  		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": ajax.api_key,
          		"table": "data_rpd_sasaran_lokal{$prefix_history}",
          		"id_unik_sasaran": id_unik_sasaran,
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
          		jQuery('#wrap-loading').hide();
				jQuery('#tujuan-sasaran-teks').html(jQuery('tr[id-tujuan="'+res.data[0].kode_tujuan+'"] td').eq(1).html());
          		for(var b in res.data_all){
					jQuery('#sasaran-teks').val(res.data_all[b].nama);
		  		}
				// console.log(res.data_all[b]);
				jQuery('#sasaran-no-urut').val(res.data_all[b].sasaran_no_urut);
				jQuery('#sasaran-catatan-teks').val(res.data_all[b].sasaran_catatan);
				jQuery('#modal-sasaran').attr('id-tujuan', res.data[0].kode_tujuan);
				jQuery('#modal-sasaran').attr('data-id', id_unik_sasaran);
				jQuery('#modal-sasaran').modal('show');
			}
		});
	}

	function get_bidang_urusan(skpd){
		return new Promise(function(resolve, reject){
			if(!skpd){
				if(typeof all_program == 'undefined'){
					jQuery.ajax({
						url: ajax.url,
			          	type: "post",
			          	data: {
			          		"action": "get_bidang_urusan",
			          		"api_key": ajax.api_key,
			          		"type": 1
			          	},
			          	dataType: "json",
			          	success: function(res){
							window.all_program = {};
							res.data.map(function(b, i){
								b.nama_urusan = b.nama_urusan.trim();
								b.nama_bidang_urusan = b.nama_bidang_urusan.trim();
								b.nama_program = b.nama_program.trim();
								if(!all_program[b.nama_urusan]){
									all_program[b.nama_urusan] = {};
								}
								if(!all_program[b.nama_urusan][b.nama_bidang_urusan]){
									all_program[b.nama_urusan][b.nama_bidang_urusan] = {};
								}
								if(!all_program[b.nama_urusan][b.nama_bidang_urusan][b.nama_program]){
									all_program[b.nama_urusan][b.nama_bidang_urusan][b.nama_program] = b;
								}
							});
							resolve();
			          	}
		          });
				}else{
					resolve();
				}
			}else{
				if(typeof all_skpd_program == 'undefined'){
					jQuery.ajax({
						url: ajax.url,
			          	type: "post",
			          	data: {
			          		"action": "get_bidang_urusan",
			          		"api_key": ajax.api_key,
			          		"type": 0
			          	},
			          	dataType: "json",
			          	success: function(res){
							window.all_skpd_program = {};
							res.data.map(function(b, i){
								if(!all_skpd_program[b.id_program]){
									all_skpd_program[b.id_program] = [];
								}
								all_skpd_program[b.id_program].push(b);
							});
							window.all_skpd = {};
							res.data.map(function(b, i){
								if(!all_skpd[b.id_skpd]){
									all_skpd[b.id_skpd] = b;
								}
							});
							resolve();
			          	}
		          });
				}else{
					resolve();
				}
			}
		});
	}

	function edit_program(id_unik_program){
		jQuery('#wrap-loading').show();
  		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": ajax.api_key,
          		"table": "data_rpd_program_lokal{$prefix_history}",
          		"id_unik_program": id_unik_program,
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
		  		get_bidang_urusan().then(function(){
					jQuery('#tujuan-program-teks').html(jQuery('tr[id-tujuan="'+jQuery('#tambah-data-sasaran').attr('id-tujuan')+'"] td').eq(1).html());
					jQuery('#sasaran-program-teks').html(jQuery('tr[id-sasaran="'+jQuery('#tambah-data-program').attr('id-sasaran')+'"] td').eq(1).html());
					jQuery('#modal-program').attr('id-sasaran', jQuery('#tambah-data-program').attr('id-sasaran'));
					jQuery('#modal-program').attr('data-id', id_unik_program);
					var id_program_master = jQuery('tr[id-program="'+id_unik_program+'"]').attr('id-program-master');
					get_urusan();
					get_bidang();
					get_program(false, id_program_master);
					jQuery('#catatan-teks-program').val(res.data[0].catatan);
					jQuery('#modal-program').modal('show');
					jQuery('#wrap-loading').hide();
				});
			}
		});
	}

	function edit_tujuan_indikator(id_unik_tujuan_indikator){
		jQuery('#wrap-loading').show();
  		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": ajax.api_key,
          		"table": "data_rpd_tujuan_lokal{$prefix_history}",
          		"id_unik_tujuan_indikator": id_unik_tujuan_indikator,
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
          		jQuery('#visi-teks-indikator').html('');
      			jQuery('#misi-teks-indikator').html('');
	  			jQuery('#saspok-teks-indikator').html('');
	  			jQuery('#kebijakan-teks-indikator').html('');
	  			jQuery('#isu-teks-indikator').html('');
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

		  			jQuery('#indikator-teks-tujuan').val(res.data_all[b].detail[0].indikator_teks);
					jQuery('#indikator-teks-tujuan-vol-awal').val(get_vol(res.data_all[b].detail[0].target_awal));
					jQuery('#indikator-teks-tujuan-satuan').val(res.data_all[b].detail[0].satuan);
				<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
					jQuery('#indikator-teks-tujuan-vol-<?php echo $i; ?>').val(get_vol(res.data_all[b].detail[0].target_<?php echo $i; ?>));
				<?php }; ?>
					jQuery('#indikator-teks-tujuan-vol-akhir').val(get_vol(res.data_all[b].detail[0].target_akhir));
					jQuery('#indikator-teks-tujuan-catatan').val(res.data_all[b].indikator_catatan_teks);
				}
				jQuery('#wrap-loading').hide();
				jQuery('#modal-tujuan-indikator').attr('data-id', id_unik_tujuan_indikator);
				jQuery('#modal-tujuan-indikator').modal('show');
			}
		});
	}

	function edit_sasaran_indikator(id_unik_sasaran_indikator){
		jQuery('#wrap-loading').show();
  		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": ajax.api_key,
          		"table": "data_rpd_sasaran_lokal{$prefix_history}",
          		"id_unik_sasaran_indikator": id_unik_sasaran_indikator,
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
          		for(var b in res.data_all){
					jQuery('#tujuan-sasaran-teks-indikator').html(jQuery('tr[id-tujuan="'+jQuery('#tambah-data-sasaran').attr('id-tujuan')+'"] td').eq(1).html());
					jQuery('#sasaran-teks-indikator').html(res.data_all[b].nama);
					jQuery('#modal-sasaran-indikator').attr('id-sasaran', res.data_all[b].id_unik);
		  			jQuery('#indikator-teks-sasaran').val(res.data_all[b].detail[0].indikator_teks);
					jQuery('#indikator-teks-sasaran-satuan').val(res.data_all[b].detail[0].satuan);
					jQuery('#indikator-teks-sasaran-vol-awal').val(get_vol(res.data_all[b].detail[0].target_awal));
				<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
					jQuery('#indikator-teks-sasaran-vol-<?php echo $i; ?>').val(get_vol(res.data_all[b].detail[0].target_<?php echo $i; ?>));
				<?php }; ?>
					jQuery('#indikator-teks-sasaran-vol-akhir').val(get_vol(res.data_all[b].detail[0].target_akhir));
					jQuery('#indikator-catatan-teks-sasaran').val(res.data_all[b].indikator_catatan_teks);
				}
				jQuery('#wrap-loading').hide();
				jQuery('#modal-sasaran-indikator').attr('data-id', id_unik_sasaran_indikator);
				jQuery('#modal-sasaran-indikator').modal('show');
			}
		});
	}

	function edit_program_indikator(id_unik_program_indikator){
		jQuery('#wrap-loading').show();
  		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"api_key": ajax.api_key,
          		"table": "data_rpd_program_lokal{$prefix_history}",
          		"id_unik_program_indikator": id_unik_program_indikator,
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
	  			jQuery('#program-tujuan-teks-indikator').html(jQuery('tr[id-tujuan="'+jQuery('#tambah-data-sasaran').attr('id-tujuan')+'"] td').eq(1).html());
	  			jQuery('#program-sasaran-teks-indikator').html(jQuery('tr[id-sasaran="'+jQuery('#tambah-data-program').attr('id-sasaran')+'"] td').eq(1).html());
				jQuery('#program-teks-indikator').html(res.data[0].nama_program);
          		get_bidang_urusan(true).then(function(){
	          		for(var b in res.data_all){
	          			get_skpd(res.data_all[b].detail[0].id_program, res.data_all[b].detail[0].id_unit);
						jQuery('#program-teks-indikator').html(res.data_all[b].nama);
						jQuery('#modal-program-indikator').attr('id-program', res.data_all[b].id_unik);
			  			jQuery('#indikator-teks-program').val(res.data_all[b].detail[0].indikator);
						jQuery('#indikator-teks-program-vol-awal').val(get_vol(res.data_all[b].detail[0].target_awal));
						jQuery('#indikator-teks-program-satuan').val(res.data_all[b].detail[0].satuan);
					<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
						jQuery('#indikator-teks-program-vol-<?php echo $i; ?>').val(get_vol(res.data_all[b].detail[0].target_<?php echo $i; ?>));
						jQuery('#indikator-teks-program-pagu-<?php echo $i; ?>').val(res.data_all[b].detail[0].pagu_<?php echo $i; ?>);
					<?php }; ?>
						jQuery('#indikator-teks-program-vol-akhir').val(get_vol(res.data_all[b].detail[0].target_akhir));
						jQuery('#indikator-catatan-teks-program').val(res.data_all[b].detail[0].catatan);
					}
					jQuery('#wrap-loading').hide();
					jQuery('#modal-program-indikator').attr('data-id', id_unik_program_indikator);
					jQuery('#modal-program-indikator').modal('show');          			
          		})
			}
		});
	}

	function get_vol(text){
		return text.split(' ')[0];
	}

	function get_sat(text){
		return text.split(' ')[1];
	}

	function simpan_tujuan(){
		var tujuan_teks = jQuery('#tujuan-teks').val();
		if(tujuan_teks == ''){
			return alert('Tujuan tidak boleh kosong!');
		}
		var id_isu = jQuery('#isu-teks').val();
	<?php if(!empty($id_jadwal_rpjpd)): ?>
		if(id_isu == ''){
			return alert('Isu RPJPD tidak boleh kosong karena jadwal RPD sudah terelasi dengan jadwal RPJPD!');
		}
	<?php endif; ?>
		var id_tujuan = jQuery('#modal-tujuan').attr('data-id');
		var no_urut = jQuery('#no-urut-teks-tujuan').val();
		if(no_urut == ''){
			jQuery('#wrap-loading').hide();
			console.log('hei');
			return alert('No urut tidak boleh kosong!');
		}
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			var catatan_teks_tujuan = jQuery('#catatan-teks-tujuan').val();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpd",
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_tujuan_lokal{$prefix_history}',
	          		"data": tujuan_teks,
	          		"id_isu": id_isu,
	          		"id": id_tujuan,
					"no_urut":no_urut,
					"catatan_teks_tujuan":catatan_teks_tujuan
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-tujuan').modal('hide');
						jQuery('#tambah-data').click();
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function simpan_sasaran(){
		var sasaran_teks = jQuery('#sasaran-teks').val();
		if(sasaran_teks == ''){
			return alert('Sasaran tidak boleh kosong!');
			jQuery('#wrap-loading').hide();
		}
		var id_unik_tujuan = jQuery('#modal-sasaran').attr('id-tujuan');
		if(id_unik_tujuan == ''){
			return alert('Id tujuan tidak boleh kosong!');
			jQuery('#wrap-loading').hide();
		}
		var id_sasaran = jQuery('#modal-sasaran').attr('data-id');
		var sasaran_no_urut = jQuery('#sasaran-no-urut').val();
		if(sasaran_no_urut == ''){
			return alert('No urut sasaran tidak boleh kosong!');
			// jQuery('#wrap-loading').hide();
			// console.log('tutup');
		}
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			var sasaran_catatan = jQuery('#sasaran-catatan-teks').val();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpd",
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_sasaran_lokal{$prefix_history}',
	          		"data": sasaran_teks,
	          		"id_tujuan": id_unik_tujuan,
	          		"id": id_sasaran,
					"sasaran_no_urut": sasaran_no_urut,
					"sasaran_catatan": sasaran_catatan
	          	},
	          	dataType: "json",
	          	success: function(res){
				console.log(res);
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-sasaran').modal('hide');
						detail_tujuan(id_unik_tujuan);
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function simpan_program(){
		var id_program_master = jQuery('#program-teks').val();
		if(id_program_master == ''){
			return alert('Program tidak boleh kosong!');
		}
		var program_teks = jQuery('#program-teks option:selected').text().split(' ');
		program_teks.shift();
		program_teks = program_teks.join(' ');
		var id_unik_sasaran = jQuery('#modal-program').attr('id-sasaran');
		if(id_unik_sasaran == ''){
			return alert('Id sasaran tidak boleh kosong!');
		}
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			var id_program = jQuery('#modal-program').attr('data-id');
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpd",
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_program_lokal{$prefix_history}',
	          		"data": id_program_master,
	          		"nama_program": program_teks,
	          		"catatan": jQuery('#catatan-teks-program').val(),
	          		"id_sasaran": id_unik_sasaran,
	          		"id": id_program
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-program').modal('hide');
						detail_sasaran(id_unik_sasaran);
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
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_tujuan_lokal{$prefix_history}',
	          		"id": id_tujuan_unik
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-tujuan').modal('hide');
						jQuery('#tambah-data').click();
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function hapus_sasaran(id_sasaran_unik){
		if(confirm('Apakah anda yakin untuk menghapus data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "hapus_rpd",
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_sasaran_lokal{$prefix_history}',
	          		"id": id_sasaran_unik
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-sasaran').modal('hide');
						detail_tujuan(jQuery('#tambah-data-sasaran').attr('id-tujuan'));
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function hapus_program(id_program_unik){
		if(confirm('Apakah anda yakin untuk menghapus data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "hapus_rpd",
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_program_lokal{$prefix_history}',
	          		"id": id_program_unik
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-program').modal('hide');
						detail_sasaran(jQuery('#tambah-data-program').attr('id-sasaran'));
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function hapus_tujuan_indikator(id_unik_tujuan_indikator){
		if(confirm('Apakah anda yakin untuk menghapus data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "hapus_rpd",
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_tujuan_lokal{$prefix_history}',
	          		"id_unik_tujuan_indikator": id_unik_tujuan_indikator
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-tujuan').modal('hide');
						jQuery('#tambah-data').click();
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function hapus_sasaran_indikator(id_unik_sasaran_indikator){
		if(confirm('Apakah anda yakin untuk menghapus data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "hapus_rpd",
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_sasaran_lokal{$prefix_history}',
	          		"id_unik_sasaran_indikator": id_unik_sasaran_indikator
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-sasaran').modal('hide');
						detail_tujuan(jQuery('#tambah-data-sasaran').attr('id-tujuan'));
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function hapus_program_indikator(id_unik_program_indikator){
		if(confirm('Apakah anda yakin untuk menghapus data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "hapus_rpd",
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_program_lokal{$prefix_history}',
	          		"id_unik_program_indikator": id_unik_program_indikator
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-program').modal('hide');
						detail_sasaran(jQuery('#tambah-data-program').attr('id-sasaran'));
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
		var satuan = jQuery('#indikator-teks-tujuan-satuan').val();
		if(satuan == ''){
			jQuery('#wrap-loading').hide();
			return alert('Satuan indikator tujuan tidak boleh kosong!');
		}
		var vol_awal = jQuery('#indikator-teks-tujuan-vol-awal').val();
		if(vol_awal == ''){
			return alert('Volume awal indikator tujuan tidak boleh kosong!');
		}
	<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
		var vol_<?php echo $i; ?> = jQuery('#indikator-teks-tujuan-vol-<?php echo $i; ?>').val();
		if(vol_<?php echo $i; ?> == ''){
			return alert('Volume <?php echo $i; ?> indikator tujuan tidak boleh kosong!');
		}
	<?php }; ?>
		var vol_akhir = jQuery('#indikator-teks-tujuan-vol-akhir').val();
		if(vol_akhir == ''){
			return alert('Volume akhir indikator tujuan tidak boleh kosong!');
		}
		var id_indikator = jQuery('#modal-tujuan-indikator').attr('data-id');
		var indikator_catatan_teks = jQuery('#indikator-teks-tujuan-catatan').val();
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpd",
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_tujuan_lokal{$prefix_history}',
	          		"data": tujuan_teks_indikator,
	          		"id_tujuan": id_tujuan,
	          		"vol_awal": vol_awal,
					"satuan": satuan,
				<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	          		"vol_<?php echo $i; ?>": vol_<?php echo $i; ?>,
				<?php }; ?>
	          		"vol_akhir": vol_akhir,
	          		"id": id_indikator,
					"indikator_catatan_teks":indikator_catatan_teks
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-tujuan-indikator').modal('hide');
						jQuery('#tambah-data').click();
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function simpan_sasaran_indikator() {
		var id_sasaran = jQuery('#modal-sasaran-indikator').attr('id-sasaran');
		if(id_sasaran == ''){
			return alert('ID sasaran tidak ditemukan!');
		}
		var sasaran_teks_indikator = jQuery('#indikator-teks-sasaran').val();
		if(sasaran_teks_indikator == ''){
			return alert('Indikator sasaran tidak boleh kosong!');
		}
		var satuan = jQuery('#indikator-teks-sasaran-satuan').val();
		if(satuan == ''){
			jQuery('#wrap-loading').hide();
			return alert('Satuan indikator sasaran tidak boleh kosong!');
		}
		var vol_awal = jQuery('#indikator-teks-sasaran-vol-awal').val();
		if(vol_awal == ''){
			return alert('Volume awal indikator sasaran tidak boleh kosong!');
		}
	<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
		var vol_<?php echo $i; ?> = jQuery('#indikator-teks-sasaran-vol-<?php echo $i; ?>').val();
		if(vol_<?php echo $i; ?> == ''){
			return alert('Volume <?php echo $i; ?> indikator sasaran tidak boleh kosong!');
		}
	<?php }; ?>
		var vol_akhir = jQuery('#indikator-teks-sasaran-vol-akhir').val();
		if(vol_akhir == ''){
			return alert('Volume akhir indikator sasaran tidak boleh kosong!');
		}
		var id_indikator = jQuery('#modal-sasaran-indikator').attr('data-id');
		var indikator_catatan_teks = jQuery('#indikator-catatan-teks-sasaran').val();
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpd",
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_sasaran_lokal{$prefix_history}',
	          		"data": sasaran_teks_indikator,
	          		"id_sasaran": id_sasaran,
	          		"satuan": satuan,
	          		"vol_awal": vol_awal,
				<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	          		"vol_<?php echo $i; ?>": vol_<?php echo $i; ?>,
				<?php }; ?>
	          		"vol_akhir": vol_akhir,
	          		"id": id_indikator,
					'indikator_catatan_teks': indikator_catatan_teks
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-sasaran-indikator').modal('hide');
						detail_tujuan(jQuery('#tambah-data-sasaran').attr('id-tujuan'));
					}
					alert(res.message);
	          	}
	        });
		}
	}

	function simpan_program_indikator() {
		var id_program = jQuery('#modal-program-indikator').attr('id-program');
		if(id_program == ''){
			return alert('ID program tidak ditemukan!');
		}
		var id_skpd = jQuery('#skpd-teks').val();
		if(id_skpd == ''){
			return alert('SKPD tidak boleh kosong!');
		}
		var nama_skpd = jQuery('#skpd-teks option:selected').text();
		var kode_skpd = jQuery('#skpd-teks option:selected').attr('data-kode');
		var program_teks_indikator = jQuery('#indikator-teks-program').val();
		if(program_teks_indikator == ''){
			return alert('Indikator program tidak boleh kosong!');
		}
		var vol_awal = jQuery('#indikator-teks-program-vol-awal').val();
		if(vol_awal == ''){
			return alert('Volume awal indikator program tidak boleh kosong!');
		}
		var satuan = jQuery('#indikator-teks-program-satuan').val();
		if(satuan == ''){
			return alert('Satuan indikator program tidak boleh kosong!');
		}
	<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
		var vol_<?php echo $i; ?> = jQuery('#indikator-teks-program-vol-<?php echo $i; ?>').val();
		if(vol_<?php echo $i; ?> == ''){
			return alert('Volume <?php echo $i; ?> indikator program tidak boleh kosong!');
		}
		var pagu_<?php echo $i; ?> = jQuery('#indikator-teks-program-pagu-<?php echo $i; ?>').val();
	<?php }; ?>
		var vol_akhir = jQuery('#indikator-teks-program-vol-akhir').val();
		if(vol_akhir == ''){
			return alert('Volume akhir indikator program tidak boleh kosong!');
		}
		var id_indikator = jQuery('#modal-program-indikator').attr('data-id');
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "simpan_rpd",
	          		"api_key": ajax.api_key,
	          		"table": 'data_rpd_program_lokal{$prefix_history}',
	          		"data": program_teks_indikator,
	          		"id_program": id_program,
	          		"id_skpd": id_skpd,
	          		"kode_skpd": kode_skpd,
	          		"nama_skpd": nama_skpd,
	          		"vol_awal": vol_awal,
	          		"satuan": satuan,
				<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	          		"vol_<?php echo $i; ?>": vol_<?php echo $i; ?>,
	          		"pagu_<?php echo $i; ?>": pagu_<?php echo $i; ?>,
				<?php } ;?>
	          		"vol_akhir": vol_akhir,
	          		"catatan": jQuery('#indikator-catatan-teks-program').val(),
	          		"id": id_indikator
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					if(res.status == 'success'){
	          			edit_val = true;
						jQuery('#modal-program-indikator').modal('hide');
						detail_sasaran(jQuery('#tambah-data-program').attr('id-sasaran'));
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
	          		"api_key": ajax.api_key,
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

	jQuery('#generate-data-program-renstra').on('click', function(){
		if(confirm("Apakah anda yakin?\nGenerate data program dari RENSTRA akan menghapus data program di RPD.")){
			generate_data_program_renstra();
		}
	});

	jQuery('#generate-data-rpd-esakip').on('click', function(){
		if(confirm("Apakah anda yakin?\nGenerate data dari aplikasi WP-Eval-Sakip dapat menimpa data aktif saat ini.")){
			generate_data_rpd_esakip();
		}
	});

	function generate_data_program_renstra() {
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url	: ajax.url,
			type : "post",
			data : {
				"action": "get_data_program_renstra",
				"api_key": ajax.api_key
			},
			dataType: "json",
			success: function(res){
				jQuery('#wrap-loading').hide();
				if(res.data.length != 0){
					edit_val = true;
					refresh_page();
				}
			}
		});
	}

	function generate_data_rpd_esakip() {
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url	: ajax.url,
			type : "post",
			data : {
				action: "sync_data_rpjmd_rpd_lokal_esakip",
				api_key: ajax.api_key,
				id_jadwal: <?php echo $data_jadwal->id_jadwal_lokal; ?>
			},
			dataType: "json",
			success: function(res){
				jQuery('#wrap-loading').hide();
				alert(res.message);
				if(res.status){
					edit_val = true;
					refresh_page();
				}
			}
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

	jQuery('#modal-monev').on('hidden.bs.modal', function () {
		refresh_page();
	});

	function refresh_page() {
		if(edit_val){
			if(confirm('Ada data yang berubah, apakah mau merefresh halaman ini?')){
	    		window.location = "";
			}
	    }
	}

	function tampilProgram(id_unik){
		jQuery('#wrap-loading').show();		
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_rpd",
          		"table": "data_rpd_program_lokal{$prefix_history}",
          		"api_key": ajax.api_key,
				'id_unik_program': id_unik,
				'type':1
          	},
          	dataType: "json",
          	success: function(res){

				jQuery("#modal-raw .modal-title").html('Mutakhirkan Program RPD');
			    jQuery("#modal-raw .modal-body").html(
		        		'<h4 style="text-align:center"><span>EXISTING</span></h4>'
		        		+'<table class="table">'
				   			+'<thead>'
					          	+'<tr>'
					          		+'<th class="text-center" style="width: 160px;">Program</th>'
					          		+'<td>'+res.data[0].nama_program+'</td>'
					          	+'</tr>'
					      	+'</thead>'
					      +'</table>'
					      +'<h4 style="text-align:center"><span>PEMUTAKHIRAN</span></h4>'
					      +'<table class="table">'
					      	+'<thead>'
					      		+'<tr>'
					        		+'<th class="text-center" style="width: 160px;">Urusan</th>'
					          		+'<td><select class="form-control" name="id_urusan" id="urusan-teks-mutakhir" readonly></select></td>'
					          	+'</tr>'
					          	+'<tr>'
					          		+'<th class="text-center" style="width: 160px;">Bidang</th>'
					          		+'<td><select class="form-control" name="id_bidang" id="bidang-teks-mutakhir" readonly></select></td>'
					          	+'</tr>'
					          	+'<tr>'
					          		+'<th class="text-center" style="width: 160px;">Program</th>'
					          		+'<td><select id="program-teks-mutakhir" name="id_program"></select></td>'
					          	+'</tr>'
					      	+'</thead>'
					    +'</table>'
					);

					jQuery("#modal-raw").find('.modal-dialog').css('maxWidth','1350px');
					jQuery("#modal-raw").find('.modal-dialog').css('width','100%');
					jQuery("#modal-raw").find('.modal-footer').html(''
							+'<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>'
							+'<button type="button" class="btn btn-success" onclick=\'mutakhirkanProgram("'+id_unik+'", "'+res.data[0].id+'")\'>Mutakhirkan</button>');
		          	jQuery("#modal-raw").modal('show');

	          		get_bidang_urusan().then(function(){
						get_urusan('urusan-teks-mutakhir')
						get_bidang(false, 'bidang-teks-mutakhir');
						get_program(false, '', 'program-teks-mutakhir', 'urusan-teks-mutakhir');
						jQuery('#wrap-loading').hide();
					});
          	}
        });
	}

	function mutakhirkanProgram(id_unik, id){
		let id_program = jQuery("#program-teks-mutakhir").val();
		if(id_program == null || id_program=="" || id_program=="undefined"){
			alert('Wajib memilih program!');
		}else{
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "mutakhirkan_program_rpd",
	          		"api_key": ajax.api_key,
	          		'id': id,
	          		'id_program': id_program,
	          		'id_unik': id_unik,
			       	'tahun_anggaran': '<?php echo $data_jadwal->tahun_anggaran; ?>'
	          	},
	          	dataType: "json",
	          	success: function(response){
	          		jQuery('#wrap-loading').hide();
	          		alert(response.message);
	          		if(response.status){
	          			location.reload();
	          		}
	          	}
	        });
		}
	}
</script>