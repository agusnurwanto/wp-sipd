<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;

date_default_timezone_set('Asia/Jakarta');
$timezone = get_option('timezone_string');

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
$relasi_perencanaan = '-';
$id_tipe_relasi = '-';
$lama_pelaksanaan = 5;
$disabled = 'readonly';
$disabled_admin = '';

$cek_jadwal = $this->validasi_jadwal_perencanaan('renstra');
$jadwal_lokal = $cek_jadwal['data'];
$add_renstra = '';
if(!empty($jadwal_lokal)){
	if(!empty($jadwal_lokal[0]['relasi_perencanaan'])){
		$relasi = $wpdb->get_row("
					SELECT 
						id_tipe 
					FROM `data_jadwal_lokal`
					WHERE id_jadwal_lokal=".$jadwal_lokal[0]['relasi_perencanaan']);

		$relasi_perencanaan = $jadwal_lokal[0]['relasi_perencanaan'];
		$id_tipe_relasi = $relasi->id_tipe;
	}

	$awal_renstra = $jadwal_lokal[0]['tahun_anggaran'];
	$namaJadwal = $jadwal_lokal[0]['nama'];
	$mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
	$selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];
	$lama_pelaksanaan = $jadwal_lokal[0]['lama_pelaksanaan'];

	$awal = new DateTime($mulaiJadwal);
	$akhir = new DateTime($selesaiJadwal);
	$now = new DateTime(date('Y-m-d H:i:s'));

	if($now >= $awal && $now <= $akhir){
		$add_renstra = '<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RENSTRA</a>';
	}
}

$nama_tipe_relasi = 'RPJMD / RPD';
switch ($id_tipe_relasi) {
	case '2':
			$nama_tipe_relasi = 'RPJMD';
		break;

	case '3':
			$nama_tipe_relasi = 'RPD';
		break;
}

$akhir_renstra = $awal_renstra+$lama_pelaksanaan-1;
$urut = $tahun_anggaran-$awal_renstra;
$rumus_indikator_db = $wpdb->get_results("SELECT * FROM data_rumus_indikator WHERE active=1 AND tahun_anggaran=".$tahun_anggaran, ARRAY_A);
$rumus_indikator = '';
foreach ($rumus_indikator_db as $k => $v){
	$rumus_indikator .= '<option value="'.$v['id'].'">'.$v['rumus'].'</option>';
}

$where_skpd = '';
if(!empty($input['id_skpd'])){
	$where_skpd = "and id_skpd=".$input['id_skpd'];
}

$is_admin = false;
$user_id = um_user( 'ID' );
$user_meta = get_userdata($user_id);
if(in_array("administrator", $user_meta->roles)){
	$is_admin = true;
	$disabled='';
	$disabled_admin = 'readonly';
}

$sql = $wpdb->prepare("
	SELECT 
		* 
	FROM data_unit 
	WHERE tahun_anggaran=%d
		".$where_skpd."
		AND active=1
	ORDER BY id_skpd ASC
", $tahun_anggaran);

$unit = $wpdb->get_results($sql, ARRAY_A);

if(empty($unit)){
	die('<h1>Data SKPD dengan id_skpd='.$input['id_skpd'].' dan tahun_anggaran='.$tahun_anggaran.' tidak ditemukan!</h1>');
}

$judul_skpd = '';
if(!empty($input['id_skpd'])){
	$judul_skpd = $unit[0]['kode_skpd'].'&nbsp;'.$unit[0]['nama_skpd'].'<br>';
}
$nama_pemda = get_option('_crb_daerah');

$current_user = wp_get_current_user();

$body = '';
$bulan = date('m');
$data_all = array(
	'data' => array(),
	'pagu_akumulasi_1' => 0,
	'pagu_akumulasi_2' => 0,
	'pagu_akumulasi_3' => 0,
	'pagu_akumulasi_4' => 0,
	'pagu_akumulasi_5' => 0,
	'pagu_akumulasi_1_usulan' => 0,
	'pagu_akumulasi_2_usulan' => 0,
	'pagu_akumulasi_3_usulan' => 0,
	'pagu_akumulasi_4_usulan' => 0,
	'pagu_akumulasi_5_usulan' => 0,
);

$tujuan_ids = array();
$sasaran_ids = array();
$program_ids = array();
$kegiatan_ids = array();
$sub_kegiatan_ids = array();
$nama_pemda = get_option('_crb_daerah');

$tujuan_all = $wpdb->get_results($wpdb->prepare("
	SELECT 
		* 
	FROM data_renstra_tujuan_lokal 
	WHERE 
		id_unit=%d AND 
		active=1 ORDER BY urut_tujuan
", $input['id_skpd']), ARRAY_A);

// echo '<pre>';print_r($wpdb->last_query);echo '</pre>';die();

foreach ($tujuan_all as $keyTujuan => $tujuan_value) {
	if(empty($data_all['data'][$tujuan_value['id_unik']])){
		$data_all['data'][$tujuan_value['id_unik']] = [
			'id' => $tujuan_value['id'],
			'id_bidang_urusan' => $tujuan_value['id_bidang_urusan'],
			'id_unik' => $tujuan_value['id_unik'],
			'tujuan_teks' => $tujuan_value['tujuan_teks'],
			'nama_bidang_urusan' => $tujuan_value['nama_bidang_urusan'],
			'urut_tujuan' => $tujuan_value['urut_tujuan'],
			'catatan' => $tujuan_value['catatan'],
			'catatan_usulan' => $tujuan_value['catatan_usulan'],
			'sasaran_rpjm' => '',
			'pagu_akumulasi_1' => 0,
			'pagu_akumulasi_2' => 0,
			'pagu_akumulasi_3' => 0,
			'pagu_akumulasi_4' => 0,
			'pagu_akumulasi_5' => 0,
			'pagu_akumulasi_1_usulan' => 0,
			'pagu_akumulasi_2_usulan' => 0,
			'pagu_akumulasi_3_usulan' => 0,
			'pagu_akumulasi_4_usulan' => 0,
			'pagu_akumulasi_5_usulan' => 0,
			'indikator' => array(),
			'data' => array(),
			'status_rpjm' => false
		];

		if(!empty($tujuan_value['kode_sasaran_rpjm']) && $relasi_perencanaan != '-'){
			$table = 'data_rpjmd_sasaran_lokal';
			switch ($id_tipe_relasi) {
				case '2':
						$table = 'data_rpjmd_sasaran_lokal_history';
					break;

				case '3':
						$table = 'data_rpd_sasaran_lokal_history';
					break;
			}

			$sasaran_rpjm = $wpdb->get_var("
				SELECT DISTINCT
					sasaran_teks
				FROM ".$table." 
				WHERE id_unik='{$tujuan_value['kode_sasaran_rpjm']}'
					AND active=1
			");
			if(!empty($sasaran_rpjm)){
				$data_all['data'][$tujuan_value['id_unik']]['status_rpjm'] = true;
				$data_all['data'][$tujuan_value['id_unik']]['sasaran_rpjm'] = $sasaran_rpjm;
			}
		}
	}

	$tujuan_ids[$tujuan_value['id_unik']] = "'".$tujuan_value['id_unik']."'";

	if(!empty($tujuan_value['id_unik_indikator'])){
		if(empty($data_all['data'][$tujuan_value['id_unik']]['indikator'][$tujuan_value['id_unik_indikator']])){
			$data_all['data'][$tujuan_value['id_unik']]['indikator'][$tujuan_value['id_unik_indikator']] = [
				'id_unik_indikator' => $tujuan_value['id_unik_indikator'],
				'indikator_teks' => $tujuan_value['indikator_teks'],
				'satuan' => $tujuan_value['satuan'],
				'target_1' => $tujuan_value['target_1'],
				'target_2' => $tujuan_value['target_2'],
				'target_3' => $tujuan_value['target_3'],
				'target_4' => $tujuan_value['target_4'],
				'target_5' => $tujuan_value['target_5'],
				'target_awal' => $tujuan_value['target_awal'],
				'target_akhir' => $tujuan_value['target_akhir'],
				'catatan_indikator' => $tujuan_value['catatan'],
				'indikator_teks_usulan' => $tujuan_value['indikator_teks_usulan'],
				'satuan_usulan' => $tujuan_value['satuan_usulan'],
				'target_1_usulan' => $tujuan_value['target_1_usulan'],
				'target_2_usulan' => $tujuan_value['target_2_usulan'],
				'target_3_usulan' => $tujuan_value['target_3_usulan'],
				'target_4_usulan' => $tujuan_value['target_4_usulan'],
				'target_5_usulan' => $tujuan_value['target_5_usulan'],
				'target_awal_usulan' => $tujuan_value['target_awal_usulan'],
				'target_akhir_usulan' => $tujuan_value['target_akhir_usulan'],
				'catatan_indikator_usulan' => $tujuan_value['catatan_usulan']
			];
		}
	}

	if(empty($tujuan_value['id_unik_indikator'])){
		$sasaran_all = $wpdb->get_results($wpdb->prepare("
			SELECT 
				* 
			FROM data_renstra_sasaran_lokal 
			WHERE 
				kode_tujuan=%s AND 
				active=1 ORDER BY urut_sasaran
		", $tujuan_value['id_unik']), ARRAY_A);

		foreach ($sasaran_all as $keySasaran => $sasaran_value) {
			if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']])){
				$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']] = [
					'id' => $sasaran_value['id'],
					'id_unik' => $sasaran_value['id_unik'],
					'sasaran_teks' => $sasaran_value['sasaran_teks'],
					'urut_sasaran' => $sasaran_value['urut_sasaran'],
					'catatan' => $sasaran_value['catatan'],
					'catatan_usulan' => $sasaran_value['catatan_usulan'],
					'pagu_akumulasi_1' => 0,
					'pagu_akumulasi_2' => 0,
					'pagu_akumulasi_3' => 0,
					'pagu_akumulasi_4' => 0,
					'pagu_akumulasi_5' => 0,
					'pagu_akumulasi_1_usulan' => 0,
					'pagu_akumulasi_2_usulan' => 0,
					'pagu_akumulasi_3_usulan' => 0,
					'pagu_akumulasi_4_usulan' => 0,
					'pagu_akumulasi_5_usulan' => 0,
					'indikator' => array(),
					'data' => array()
				];
			}

			$sasaran_ids[$sasaran_value['id_unik']] = "'".$sasaran_value['id_unik']."'";

			if(!empty($sasaran_value['id_unik_indikator'])){
				if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['indikator'][$sasaran_value['id_unik_indikator']])){
					$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['indikator'][$sasaran_value['id_unik_indikator']] = [
						'id_unik_indikator' => $sasaran_value['id_unik_indikator'],
						'indikator_teks' => $sasaran_value['indikator_teks'],
						'satuan' => $sasaran_value['satuan'],
						'target_1' => $sasaran_value['target_1'],
						'target_2' => $sasaran_value['target_2'],
						'target_3' => $sasaran_value['target_3'],
						'target_4' => $sasaran_value['target_4'],
						'target_5' => $sasaran_value['target_5'],
						'target_awal' => $sasaran_value['target_awal'],
						'target_akhir' => $sasaran_value['target_akhir'],
						'catatan_indikator' => $sasaran_value['catatan'],
						'indikator_teks_usulan' => $sasaran_value['indikator_teks_usulan'],
						'satuan_usulan' => $sasaran_value['satuan_usulan'],
						'target_1_usulan' => $sasaran_value['target_1_usulan'],
						'target_2_usulan' => $sasaran_value['target_2_usulan'],
						'target_3_usulan' => $sasaran_value['target_3_usulan'],
						'target_4_usulan' => $sasaran_value['target_4_usulan'],
						'target_5_usulan' => $sasaran_value['target_5_usulan'],
						'target_awal_usulan' => $sasaran_value['target_awal_usulan'],
						'target_akhir_usulan' => $sasaran_value['target_akhir_usulan'],
						'catatan_indikator_usulan' => $sasaran_value['catatan_usulan']
					];
				}
			}

			if(empty($sasaran_value['id_unik_indikator'])){

				$program_all = $wpdb->get_results($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE 
							kode_sasaran=%s AND 
							kode_tujuan=%s AND 
							active=1 ORDER BY id",
							$sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);

					foreach ($program_all as $keyProgram => $program_value) {
						if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']])){
							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']] = [
								'id' => $program_value['id'],
								'id_unik' => $program_value['id_unik'],
								'program_teks' => $program_value['nama_program'],
								'catatan' => $program_value['catatan'],
								'catatan_usulan' => $program_value['catatan_usulan'],
								'pagu_akumulasi_1' => 0,
								'pagu_akumulasi_2' => 0,
								'pagu_akumulasi_3' => 0,
								'pagu_akumulasi_4' => 0,
								'pagu_akumulasi_5' => 0,
								'pagu_akumulasi_1_usulan' => 0,
								'pagu_akumulasi_2_usulan' => 0,
								'pagu_akumulasi_3_usulan' => 0,
								'pagu_akumulasi_4_usulan' => 0,
								'pagu_akumulasi_5_usulan' => 0,
								'pagu_akumulasi_indikator_1' => 0,
								'pagu_akumulasi_indikator_2' => 0,
								'pagu_akumulasi_indikator_3' => 0,
								'pagu_akumulasi_indikator_4' => 0,
								'pagu_akumulasi_indikator_5' => 0,
								'pagu_akumulasi_indikator_1_usulan' => 0,
								'pagu_akumulasi_indikator_2_usulan' => 0,
								'pagu_akumulasi_indikator_3_usulan' => 0,
								'pagu_akumulasi_indikator_4_usulan' => 0,
								'pagu_akumulasi_indikator_5_usulan' => 0,
								'indikator' => array(),
								'data' => array()
							];
						}

					$program_ids[$program_value['id_unik']] = "'".$program_value['id_unik']."'";

					if(!empty($program_value['id_unik_indikator'])){
						if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']])){

							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_1'] += $program_value['pagu_1'];
							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_2'] += $program_value['pagu_2'];
							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_3'] += $program_value['pagu_3'];
							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_4'] += $program_value['pagu_4'];
							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_5'] += $program_value['pagu_5'];
							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_1_usulan'] += $program_value['pagu_1_usulan'];
							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_2_usulan'] += $program_value['pagu_2_usulan'];
							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_3_usulan'] += $program_value['pagu_3_usulan'];
							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_4_usulan'] += $program_value['pagu_4_usulan'];
							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_5_usulan'] += $program_value['pagu_5_usulan'];

							$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']] = [
								'id_unik_indikator' => $program_value['id_unik_indikator'],
								'indikator_teks' => $program_value['indikator'],
								'satuan' => $program_value['satuan'],
								'target_1' => $program_value['target_1'],
								'pagu_1' => $program_value['pagu_1'],
								'target_2' => $program_value['target_2'],
								'pagu_2' => $program_value['pagu_2'],
								'target_3' => $program_value['target_3'],
								'pagu_3' => $program_value['pagu_3'],
								'target_4' => $program_value['target_4'],
								'pagu_4' => $program_value['pagu_4'],
								'target_5' => $program_value['target_5'],
								'pagu_5' => $program_value['pagu_5'],
								'target_awal' => $program_value['target_awal'],
								'target_akhir' => $program_value['target_akhir'],
								'catatan_indikator' => $program_value['catatan'],
								'indikator_teks_usulan' => $program_value['indikator_usulan'],
								'satuan_usulan' => $program_value['satuan_usulan'],
								'target_1_usulan' => $program_value['target_1_usulan'],
								'pagu_1_usulan' => $program_value['pagu_1_usulan'],
								'target_2_usulan' => $program_value['target_2_usulan'],
								'pagu_2_usulan' => $program_value['pagu_2_usulan'],
								'target_3_usulan' => $program_value['target_3_usulan'],
								'pagu_3_usulan' => $program_value['pagu_3_usulan'],
								'target_4_usulan' => $program_value['target_4_usulan'],
								'pagu_4_usulan' => $program_value['pagu_4_usulan'],
								'target_5_usulan' => $program_value['target_5_usulan'],
								'pagu_5_usulan' => $program_value['pagu_5_usulan'],
								'target_awal_usulan' => $program_value['target_awal_usulan'],
								'target_akhir_usulan' => $program_value['target_akhir_usulan'],
								'catatan_indikator_usulan' => $program_value['catatan_usulan']
							];
						}
					}

					if(empty($program_value['id_unik_indikator'])){
						$kegiatan_all = $wpdb->get_results($wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_kegiatan_lokal 
							WHERE 
								kode_program=%s AND 
								kode_sasaran=%s AND 
								kode_tujuan=%s AND 
								active=1 ORDER BY id",
								$program_value['id_unik'],
								$sasaran_value['id_unik'],
								$tujuan_value['id_unik'],
							), ARRAY_A);

						foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {
										
							if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']])){

								$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']] = [
									'id' => $kegiatan_value['id'],
									'id_unik' => $kegiatan_value['id_unik'],
									'kegiatan_teks' => $kegiatan_value['nama_giat'],
									'catatan' => $kegiatan_value['catatan'],
									'catatan_usulan' => $kegiatan_value['catatan_usulan'],
									'pagu_akumulasi_1' => 0,
									'pagu_akumulasi_2' => 0,
									'pagu_akumulasi_3' => 0,
									'pagu_akumulasi_4' => 0,
									'pagu_akumulasi_5' => 0,
									'pagu_akumulasi_1_usulan' => 0,
									'pagu_akumulasi_2_usulan' => 0,
									'pagu_akumulasi_3_usulan' => 0,
									'pagu_akumulasi_4_usulan' => 0,
									'pagu_akumulasi_5_usulan' => 0,
									'pagu_akumulasi_indikator_1' => 0,
									'pagu_akumulasi_indikator_2' => 0,
									'pagu_akumulasi_indikator_3' => 0,
									'pagu_akumulasi_indikator_4' => 0,
									'pagu_akumulasi_indikator_5' => 0,
									'pagu_akumulasi_indikator_1_usulan' => 0,
									'pagu_akumulasi_indikator_2_usulan' => 0,
									'pagu_akumulasi_indikator_3_usulan' => 0,
									'pagu_akumulasi_indikator_4_usulan' => 0,
									'pagu_akumulasi_indikator_5_usulan' => 0,
									'indikator' => array(),
									'data' => array(),
								];
							}

							$kegiatan_ids[$kegiatan_value['id_unik']] = "'".$kegiatan_value['id_unik']."'";

							if(!empty($kegiatan_value['id_unik_indikator'])){
								if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])){

									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_1'] += $kegiatan_value['pagu_1'];
									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_2'] += $kegiatan_value['pagu_2'];
									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_3'] += $kegiatan_value['pagu_3'];
									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_4'] += $kegiatan_value['pagu_4'];
									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_5'] += $kegiatan_value['pagu_5'];
									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_1_usulan'] += $kegiatan_value['pagu_1_usulan'];
									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_2_usulan'] += $kegiatan_value['pagu_2_usulan'];
									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_3_usulan'] += $kegiatan_value['pagu_3_usulan'];
									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_4_usulan'] += $kegiatan_value['pagu_4_usulan'];
									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_5_usulan'] += $kegiatan_value['pagu_5_usulan'];

									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']] = [
										'id_unik_indikator' => $kegiatan_value['id_unik_indikator'],
										'indikator_teks' => $kegiatan_value['indikator'],
										'satuan' => $kegiatan_value['satuan'],
										'target_1' => $kegiatan_value['target_1'],
										'pagu_1' => $kegiatan_value['pagu_1'],
										'target_2' => $kegiatan_value['target_2'],
										'pagu_2' => $kegiatan_value['pagu_2'],
										'target_3' => $kegiatan_value['target_3'],
										'pagu_3' => $kegiatan_value['pagu_3'],
										'target_4' => $kegiatan_value['target_4'],
										'pagu_4' => $kegiatan_value['pagu_4'],
										'target_5' => $kegiatan_value['target_5'],
										'pagu_5' => $kegiatan_value['pagu_5'],
										'target_awal' => $kegiatan_value['target_awal'],
										'target_akhir' => $kegiatan_value['target_akhir'],
										'catatan_indikator' => $kegiatan_value['catatan'],
										'indikator_teks_usulan' => $kegiatan_value['indikator_usulan'],
										'satuan_usulan' => $kegiatan_value['satuan_usulan'],
										'target_1_usulan' => $kegiatan_value['target_1_usulan'],
										'pagu_1_usulan' => $kegiatan_value['pagu_1_usulan'],
										'target_2_usulan' => $kegiatan_value['target_2_usulan'],
										'pagu_2_usulan' => $kegiatan_value['pagu_2_usulan'],
										'target_3_usulan' => $kegiatan_value['target_3_usulan'],
										'pagu_3_usulan' => $kegiatan_value['pagu_3_usulan'],
										'target_4_usulan' => $kegiatan_value['target_4_usulan'],
										'pagu_4_usulan' => $kegiatan_value['pagu_4_usulan'],
										'target_5_usulan' => $kegiatan_value['target_5_usulan'],
										'pagu_5_usulan' => $kegiatan_value['pagu_5_usulan'],
										'target_awal_usulan' => $kegiatan_value['target_awal_usulan'],
										'target_akhir_usulan' => $kegiatan_value['target_akhir_usulan'],
										'catatan_indikator_usulan' => $kegiatan_value['catatan_usulan']
									];
								}
							}

							if(empty($kegiatan_value['id_unik_indikator'])){
								$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare("
									SELECT 
										* 
									FROM data_renstra_sub_kegiatan_lokal 
									WHERE 
										kode_kegiatan=%s AND 
										kode_program=%s AND 
										kode_sasaran=%s AND 
										kode_tujuan=%s AND 
										active=1 ORDER BY id",
										$kegiatan_value['id_unik'],
										$program_value['id_unik'],
										$sasaran_value['id_unik'],
										$tujuan_value['id_unik'],
									), ARRAY_A);

								foreach ($sub_kegiatan_all as $keySubKegiatan => $sub_kegiatan_value) {
									
									if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']])){

										$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
										$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
										$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
										$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
										$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
										$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];
										
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

										$data_all['pagu_akumulasi_1']+=$sub_kegiatan_value['pagu_1'];
										$data_all['pagu_akumulasi_2']+=$sub_kegiatan_value['pagu_2'];
										$data_all['pagu_akumulasi_3']+=$sub_kegiatan_value['pagu_3'];
										$data_all['pagu_akumulasi_4']+=$sub_kegiatan_value['pagu_4'];
										$data_all['pagu_akumulasi_5']+=$sub_kegiatan_value['pagu_5'];
										$data_all['pagu_akumulasi_1_usulan']+=$sub_kegiatan_value['pagu_1_usulan'];
										$data_all['pagu_akumulasi_2_usulan']+=$sub_kegiatan_value['pagu_2_usulan'];
										$data_all['pagu_akumulasi_3_usulan']+=$sub_kegiatan_value['pagu_3_usulan'];
										$data_all['pagu_akumulasi_4_usulan']+=$sub_kegiatan_value['pagu_4_usulan'];
										$data_all['pagu_akumulasi_5_usulan']+=$sub_kegiatan_value['pagu_5_usulan'];

										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']] = [
											'id' => $sub_kegiatan_value['id'],
											'id_unik' => $sub_kegiatan_value['id_unik'],
											'sub_kegiatan_teks' => $sub_kegiatan_value['nama_sub_giat'],
											'catatan' => $sub_kegiatan_value['catatan'],
											'catatan_usulan' => $sub_kegiatan_value['catatan_usulan'],
											'pagu_1' => $sub_kegiatan_value['pagu_1'],
											'pagu_2' => $sub_kegiatan_value['pagu_2'],
											'pagu_3' => $sub_kegiatan_value['pagu_3'],
											'pagu_4' => $sub_kegiatan_value['pagu_4'],
											'pagu_5' => $sub_kegiatan_value['pagu_5'],
											'pagu_1_usulan' => $sub_kegiatan_value['pagu_1_usulan'],
											'pagu_2_usulan' => $sub_kegiatan_value['pagu_2_usulan'],
											'pagu_3_usulan' => $sub_kegiatan_value['pagu_3_usulan'],
											'pagu_4_usulan' => $sub_kegiatan_value['pagu_4_usulan'],
											'pagu_5_usulan' => $sub_kegiatan_value['pagu_5_usulan'],
											'indikator' => array(),
										];
									}
									$sub_kegiatan_ids[$sub_kegiatan_value['id_unik']] = "'".$sub_kegiatan_value['id_unik']."'";

									if(!empty($sub_kegiatan_value['id_unik_indikator'])){
										if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']])){

											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']] = [

												'id_unik_indikator' => $sub_kegiatan_value['id_unik_indikator'],
												'indikator_teks' => $sub_kegiatan_value['indikator'],
												'satuan' => $sub_kegiatan_value['satuan'],
												'target_1' => $sub_kegiatan_value['target_1'],
												'target_2' => $sub_kegiatan_value['target_2'],
												'target_3' => $sub_kegiatan_value['target_3'],
												'target_4' => $sub_kegiatan_value['target_4'],
												'target_5' => $sub_kegiatan_value['target_5'],
												'target_awal' => $sub_kegiatan_value['target_awal'],
												'target_akhir' => $sub_kegiatan_value['target_akhir'],
												'catatan_indikator' => $sub_kegiatan_value['catatan'],
												'indikator_teks_usulan' => $sub_kegiatan_value['indikator_usulan'],
												'satuan_usulan' => $sub_kegiatan_value['satuan_usulan'],
												'target_1_usulan' => $sub_kegiatan_value['target_1_usulan'],
												'target_2_usulan' => $sub_kegiatan_value['target_2_usulan'],
												'target_3_usulan' => $sub_kegiatan_value['target_3_usulan'],
												'target_4_usulan' => $sub_kegiatan_value['target_4_usulan'],
												'target_5_usulan' => $sub_kegiatan_value['target_5_usulan'],
												'target_awal_usulan' => $sub_kegiatan_value['target_awal_usulan'],
												'target_akhir_usulan' => $sub_kegiatan_value['target_akhir_usulan'],
												'catatan_indikator_usulan' => $sub_kegiatan_value['catatan_usulan']
											];
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

// echo '<pre>';print_r($data_all);echo '</pre>';die();

// initial data kosong
if(empty($data_all['data']['tujuan_kosong'])){
	$data_all['data']['tujuan_kosong'] = array(
		'id' => '',
		'id_unik' => '',
		'tujuan_teks' => '<span style="color: red">kosong</span>',
		'urut_tujuan' => '',
		'catatan' => '',
		'catatan_usulan' => '',
		'pagu_akumulasi_1' => 0,
		'pagu_akumulasi_2' => 0,
		'pagu_akumulasi_3' => 0,
		'pagu_akumulasi_4' => 0,
		'pagu_akumulasi_5' => 0,
		'pagu_akumulasi_1_usulan' => 0,
		'pagu_akumulasi_2_usulan' => 0,
		'pagu_akumulasi_3_usulan' => 0,
		'pagu_akumulasi_4_usulan' => 0,
		'pagu_akumulasi_5_usulan' => 0,
		'indikator' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong'])){
	$data_all['data']['tujuan_kosong']['data']['sasaran_kosong'] = array(
		'id' => '',
		'id_unik' => '',
		'sasaran_teks' => '<span style="color: red">kosong</span>',
		'urut_sasaran' => '',
		'catatan' => '',
		'catatan_usulan' => '',
		'pagu_akumulasi_1' => 0,
		'pagu_akumulasi_2' => 0,
		'pagu_akumulasi_3' => 0,
		'pagu_akumulasi_4' => 0,
		'pagu_akumulasi_5' => 0,
		'pagu_akumulasi_1_usulan' => 0,
		'pagu_akumulasi_2_usulan' => 0,
		'pagu_akumulasi_3_usulan' => 0,
		'pagu_akumulasi_4_usulan' => 0,
		'pagu_akumulasi_5_usulan' => 0,
		'indikator' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong'])){
	$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong'] = array(
		'id' => '',
		'id_unik' => '',
		'program_teks' => '<span style="color: red">kosong</span>',
		'catatan' => '',
		'catatan_usulan' => '',
		'pagu_akumulasi_1' => 0,
		'pagu_akumulasi_2' => 0,
		'pagu_akumulasi_3' => 0,
		'pagu_akumulasi_4' => 0,
		'pagu_akumulasi_5' => 0,
		'pagu_akumulasi_1_usulan' => 0,
		'pagu_akumulasi_2_usulan' => 0,
		'pagu_akumulasi_3_usulan' => 0,
		'pagu_akumulasi_4_usulan' => 0,
		'pagu_akumulasi_5_usulan' => 0,
		'pagu_akumulasi_indikator_1' => 0,
		'pagu_akumulasi_indikator_2' => 0,
		'pagu_akumulasi_indikator_3' => 0,
		'pagu_akumulasi_indikator_4' => 0,
		'pagu_akumulasi_indikator_5' => 0,
		'pagu_akumulasi_indikator_1_usulan' => 0,
		'pagu_akumulasi_indikator_2_usulan' => 0,
		'pagu_akumulasi_indikator_3_usulan' => 0,
		'pagu_akumulasi_indikator_4_usulan' => 0,
		'pagu_akumulasi_indikator_5_usulan' => 0,
		'indikator' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data']['kegiatan_kosong'])){
	$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data']['kegiatan_kosong'] = array(
		'id' => '',
		'id_unik' => '',
		'kegiatan_teks' => '<span style="color: red">kosong</span>',
		'catatan' => '',
		'catatan_usulan' => '',
		'pagu_akumulasi_1' => 0,
		'pagu_akumulasi_2' => 0,
		'pagu_akumulasi_3' => 0,
		'pagu_akumulasi_4' => 0,
		'pagu_akumulasi_5' => 0,
		'pagu_akumulasi_1_usulan' => 0,
		'pagu_akumulasi_2_usulan' => 0,
		'pagu_akumulasi_3_usulan' => 0,
		'pagu_akumulasi_4_usulan' => 0,
		'pagu_akumulasi_5_usulan' => 0,
		'pagu_akumulasi_indikator_1' => 0,
		'pagu_akumulasi_indikator_2' => 0,
		'pagu_akumulasi_indikator_3' => 0,
		'pagu_akumulasi_indikator_4' => 0,
		'pagu_akumulasi_indikator_5' => 0,
		'pagu_akumulasi_indikator_1_usulan' => 0,
		'pagu_akumulasi_indikator_2_usulan' => 0,
		'pagu_akumulasi_indikator_3_usulan' => 0,
		'pagu_akumulasi_indikator_4_usulan' => 0,
		'pagu_akumulasi_indikator_5_usulan' => 0,
		'indikator' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data']['kegiatan_kosong'])){
	$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data']['kegiatan_kosong']['data']['sub_kegiatan_kosong'] = array(
		'id' => '',
		'id_unik' => '',
		'sub_kegiatan_teks' => '<span style="color: red">kosong</span>',
		'catatan' => '',
		'catatan_usulan' => '',
		'indikator' => array(),
		'data' => array()
	);
}

// cek sasaran yang belum terselect
if(!empty($sasaran_ids)){
	$sql = "
		SELECT 
			* 
		FROM data_renstra_sasaran_lokal
		WHERE id_unik NOT IN (".implode(',', $sasaran_ids).") 
			AND active=1
			AND id_unit=".$input['id_skpd'];
}else{
	$sql = "
		SELECT 
			* 
		FROM data_renstra_sasaran_lokal 
		WHERE active=1
			AND id_unit=".$input['id_skpd'];
}
$sasaran_all_kosong = $wpdb->get_results($sql, ARRAY_A);

foreach ($sasaran_all_kosong as $keySasaran => $sasaran_value) {
	if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']])){
		$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']] = [
			'id' => $sasaran_value['id'],
			'id_unik' => $sasaran_value['id_unik'],
			'sasaran_teks' => $sasaran_value['sasaran_teks'],
			'urut_sasaran' => $sasaran_value['urut_sasaran'],
			'catatan' => $sasaran_value['catatan'],
			'catatan_usulan' => $sasaran_value['catatan_usulan'],
			'pagu_akumulasi_1' => 0,
			'pagu_akumulasi_2' => 0,
			'pagu_akumulasi_3' => 0,
			'pagu_akumulasi_4' => 0,
			'pagu_akumulasi_5' => 0,
			'pagu_akumulasi_1_usulan' => 0,
			'pagu_akumulasi_2_usulan' => 0,
			'pagu_akumulasi_3_usulan' => 0,
			'pagu_akumulasi_4_usulan' => 0,
			'pagu_akumulasi_5_usulan' => 0,
			'indikator' => array(),
			'data' => array()
		];
	}

	if(!empty($sasaran_value['id_unik_indikator'])){
		if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['indikator'][$sasaran_value['id_unik_indikator']])){
			$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['indikator'][$sasaran_value['id_unik_indikator']] = [
				'id_unik_indikator' => $sasaran_value['id_unik_indikator'],
				'indikator_teks' => $sasaran_value['indikator_teks'],
				'satuan' => $sasaran_value['satuan'],
				'target_1' => $sasaran_value['target_1'],
				'target_2' => $sasaran_value['target_2'],
				'target_3' => $sasaran_value['target_3'],
				'target_4' => $sasaran_value['target_4'],
				'target_5' => $sasaran_value['target_5'],
				'target_awal' => $sasaran_value['target_awal'],
				'target_akhir' => $sasaran_value['target_akhir'],
				'catatan_indikator' => $sasaran_value['catatan'],
				'indikator_teks_usulan' => $sasaran_value['indikator_teks_usulan'],
				'satuan_usulan' => $sasaran_value['satuan_usulan'],
				'target_1_usulan' => $sasaran_value['target_1_usulan'],
				'target_2_usulan' => $sasaran_value['target_2_usulan'],
				'target_3_usulan' => $sasaran_value['target_3_usulan'],
				'target_4_usulan' => $sasaran_value['target_4_usulan'],
				'target_5_usulan' => $sasaran_value['target_5_usulan'],
				'target_awal_usulan' => $sasaran_value['target_awal_usulan'],
				'target_akhir_usulan' => $sasaran_value['target_akhir_usulan'],
				'catatan_indikator_usulan' => $sasaran_value['catatan_usulan'],
			];
		}
	}

	if(empty($sasaran_value['id_unik_indikator'])){

		$program_all = $wpdb->get_results($wpdb->prepare("
			SELECT 
				* 
			FROM data_renstra_program_lokal 
			WHERE 
				kode_sasaran=%s AND 
				active=1 ORDER BY id
			", $sasaran_value['id_unik']), ARRAY_A);

		foreach ($program_all as $keyProgram => $program_value) {
			if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']])){
				$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']] = [
						'id' => $program_value['id'],
						'id_unik' => $program_value['id_unik'],
						'program_teks' => $program_value['nama_program'],
						'catatan' => $program_value['catatan'],
						'catatan_usulan' => $program_value['catatan_usulan'],
						'pagu_akumulasi_1' => 0,
						'pagu_akumulasi_2' => 0,
						'pagu_akumulasi_3' => 0,
						'pagu_akumulasi_4' => 0,
						'pagu_akumulasi_5' => 0,
						'pagu_akumulasi_1_usulan' => 0,
						'pagu_akumulasi_2_usulan' => 0,
						'pagu_akumulasi_3_usulan' => 0,
						'pagu_akumulasi_4_usulan' => 0,
						'pagu_akumulasi_5_usulan' => 0,		
						'pagu_akumulasi_indikator_1' => 0,
						'pagu_akumulasi_indikator_2' => 0,
						'pagu_akumulasi_indikator_3' => 0,
						'pagu_akumulasi_indikator_4' => 0,
						'pagu_akumulasi_indikator_5' => 0,
						'pagu_akumulasi_indikator_1_usulan' => 0,
						'pagu_akumulasi_indikator_2_usulan' => 0,
						'pagu_akumulasi_indikator_3_usulan' => 0,
						'pagu_akumulasi_indikator_4_usulan' => 0,
						'pagu_akumulasi_indikator_5_usulan' => 0,
						'indikator' => array(),
						'data' => array()
				];
			}

			if(!empty($program_value['id_unik_indikator'])){
				if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']])){

					$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_1'] += $program_value['pagu_1'];
					$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_2'] += $program_value['pagu_2'];
					$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_3'] += $program_value['pagu_3'];
					$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_4'] += $program_value['pagu_4'];
					$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_5'] += $program_value['pagu_5'];
					$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_1_usulan'] += $program_value['pagu_1_usulan'];
					$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_2_usulan'] += $program_value['pagu_2_usulan'];
					$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_3_usulan'] += $program_value['pagu_3_usulan'];
					$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_4_usulan'] += $program_value['pagu_4_usulan'];
					$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_5_usulan'] += $program_value['pagu_5_usulan'];

					$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']] = [
						'id_unik_indikator' => $program_value['id_unik_indikator'],
						'indikator_teks' => $program_value['indikator'],
						'satuan' => $program_value['satuan'],
						'target_1' => $program_value['target_1'],
						'pagu_1' => $program_value['pagu_1'],
						'target_2' => $program_value['target_2'],
						'pagu_2' => $program_value['pagu_2'],
						'target_3' => $program_value['target_3'],
						'pagu_3' => $program_value['pagu_3'],
						'target_4' => $program_value['target_4'],
						'pagu_4' => $program_value['pagu_4'],
						'target_5' => $program_value['target_5'],
						'pagu_5' => $program_value['pagu_5'],
						'target_awal' => $program_value['target_awal'],
						'target_akhir' => $program_value['target_akhir'],
						'catatan_indikator' => $program_value['catatan'],
						'indikator_teks_usulan' => $program_value['indikator_usulan'],
						'satuan_usulan' => $program_value['satuan_usulan'],
						'target_1_usulan' => $program_value['target_1_usulan'],
						'pagu_1_usulan' => $program_value['pagu_1_usulan'],
						'target_2_usulan' => $program_value['target_2_usulan'],
						'pagu_2_usulan' => $program_value['pagu_2_usulan'],
						'target_3_usulan' => $program_value['target_3_usulan'],
						'pagu_3_usulan' => $program_value['pagu_3_usulan'],
						'target_4_usulan' => $program_value['target_4_usulan'],
						'pagu_4_usulan' => $program_value['pagu_4_usulan'],
						'target_5_usulan' => $program_value['target_5_usulan'],
						'pagu_5_usulan' => $program_value['pagu_5_usulan'],
						'target_awal_usulan' => $program_value['target_awal_usulan'],
						'target_akhir_usulan' => $program_value['target_akhir_usulan'],
						'catatan_indikator_usulan' => $program_value['catatan_usulan']
					];
				}
			}

			if(empty($program_value['id_unik_indikator'])){
				$kegiatan_all = $wpdb->get_results($wpdb->prepare("
					SELECT 
						* 
					FROM data_renstra_kegiatan_lokal 
					WHERE 
						kode_program=%s AND 
						kode_sasaran=%s AND
						active=1 ORDER BY id
				", $program_value['id_unik'], $sasaran_value['id_unik'] ), ARRAY_A);

				foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {
										
					if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']])){

						$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']] = [
							'id' => $kegiatan_value['id'],
							'id_unik' => $kegiatan_value['id_unik'],
							'kegiatan_teks' => $kegiatan_value['nama_giat'],
							'catatan' => $kegiatan_value['catatan'],
							'catatan_usulan' => $kegiatan_value['catatan_usulan'],
							'pagu_akumulasi_1' => 0,
							'pagu_akumulasi_2' => 0,
							'pagu_akumulasi_3' => 0,
							'pagu_akumulasi_4' => 0,
							'pagu_akumulasi_5' => 0,
							'pagu_akumulasi_1_usulan' => 0,
							'pagu_akumulasi_2_usulan' => 0,
							'pagu_akumulasi_3_usulan' => 0,
							'pagu_akumulasi_4_usulan' => 0,
							'pagu_akumulasi_5_usulan' => 0,		
							'pagu_akumulasi_indikator_1' => 0,
							'pagu_akumulasi_indikator_2' => 0,
							'pagu_akumulasi_indikator_3' => 0,
							'pagu_akumulasi_indikator_4' => 0,
							'pagu_akumulasi_indikator_5' => 0,
							'pagu_akumulasi_indikator_1_usulan' => 0,
							'pagu_akumulasi_indikator_2_usulan' => 0,
							'pagu_akumulasi_indikator_3_usulan' => 0,
							'pagu_akumulasi_indikator_4_usulan' => 0,
							'pagu_akumulasi_indikator_5_usulan' => 0,
							'indikator' => array()
						];
					}

					if(!empty($kegiatan_value['id_unik_indikator'])) {
						if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])){

							$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_1'] += $kegiatan_value['pagu_1'];
							$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_2'] += $kegiatan_value['pagu_2'];
							$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_3'] += $kegiatan_value['pagu_3'];
							$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_4'] += $kegiatan_value['pagu_4'];
							$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_5'] += $kegiatan_value['pagu_5'];
							$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_1_usulan'] += $kegiatan_value['pagu_1_usulan'];
							$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_2_usulan'] += $kegiatan_value['pagu_2_usulan'];
							$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_3_usulan'] += $kegiatan_value['pagu_3_usulan'];
							$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_4_usulan'] += $kegiatan_value['pagu_4_usulan'];
							$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_5_usulan'] += $kegiatan_value['pagu_5_usulan'];

							$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']] = [
								'id_unik_indikator' => $kegiatan_value['id_unik_indikator'],
								'indikator_teks' => $kegiatan_value['indikator'],
								'satuan' => $kegiatan_value['satuan'],
								'target_1' => $kegiatan_value['target_1'],
								'pagu_1' => $kegiatan_value['pagu_1'],
								'target_2' => $kegiatan_value['target_2'],
								'pagu_2' => $kegiatan_value['pagu_2'],
								'target_3' => $kegiatan_value['target_3'],
								'pagu_3' => $kegiatan_value['pagu_3'],
								'target_4' => $kegiatan_value['target_4'],
								'pagu_4' => $kegiatan_value['pagu_4'],
								'target_5' => $kegiatan_value['target_5'],
								'pagu_5' => $kegiatan_value['pagu_5'],
								'target_awal' => $kegiatan_value['target_awal'],
								'target_akhir' => $kegiatan_value['target_akhir'],
								'catatan_indikator' => $kegiatan_value['catatan'],
								'indikator_teks_usulan' => $kegiatan_value['indikator_usulan'],
								'satuan_usulan' => $kegiatan_value['satuan_usulan'],
								'target_1_usulan' => $kegiatan_value['target_1_usulan'],
								'pagu_1_usulan' => $kegiatan_value['pagu_1_usulan'],
								'target_2_usulan' => $kegiatan_value['target_2_usulan'],
								'pagu_2_usulan' => $kegiatan_value['pagu_2_usulan'],
								'target_3_usulan' => $kegiatan_value['target_3_usulan'],
								'pagu_3_usulan' => $kegiatan_value['pagu_3_usulan'],
								'target_4_usulan' => $kegiatan_value['target_4_usulan'],
								'pagu_4_usulan' => $kegiatan_value['pagu_4_usulan'],
								'target_5_usulan' => $kegiatan_value['target_5_usulan'],
								'pagu_5_usulan' => $kegiatan_value['pagu_5_usulan'],
								'target_awal_usulan' => $kegiatan_value['target_awal_usulan'],
								'target_akhir_usulan' => $kegiatan_value['target_akhir_usulan'],
								'catatan_indikator_usulan' => $kegiatan_value['catatan_usulan']
							];
						}
					}

					if(empty($kegiatan_value['id_unik_indikator'])){
						$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_sub_kegiatan_lokal 
								WHERE 
									kode_kegiatan=%s AND 
									kode_program=%s AND 
									kode_sasaran=%s AND 
									active=1 ORDER BY id",
									$kegiatan_value['id_unik'],
									$program_value['id_unik'],
									$sasaran_value['id_unik']
								), ARRAY_A);

						foreach ($sub_kegiatan_all as $keySubKegiatan => $sub_kegiatan_value) {
										
							if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']])){

								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

								$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']] = [
										'id' => $sub_kegiatan_value['id'],
										'id_unik' => $sub_kegiatan_value['id_unik'],
										'sub_kegiatan_teks' => $sub_kegiatan_value['nama_sub_giat'],
										'catatan' => $sub_kegiatan_value['catatan'],
										'catatan_usulan' => $sub_kegiatan_value['catatan_usulan'],
										'pagu_1' => $sub_kegiatan_value['pagu_1'],
										'pagu_2' => $sub_kegiatan_value['pagu_2'],
										'pagu_3' => $sub_kegiatan_value['pagu_3'],
										'pagu_4' => $sub_kegiatan_value['pagu_4'],
										'pagu_5' => $sub_kegiatan_value['pagu_5'],
										'pagu_1_usulan' => $sub_kegiatan_value['pagu_1_usulan'],
										'pagu_2_usulan' => $sub_kegiatan_value['pagu_2_usulan'],
										'pagu_3_usulan' => $sub_kegiatan_value['pagu_3_usulan'],
										'pagu_4_usulan' => $sub_kegiatan_value['pagu_4_usulan'],
										'pagu_5_usulan' => $sub_kegiatan_value['pagu_5_usulan'],
										'indikator' => array(),
									];
							}

							if(!empty($sub_kegiatan_value['id_unik_indikator'])){
								if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']])){

									$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']] = [

										'id_unik_indikator' => $sub_kegiatan_value['id_unik_indikator'],
										'indikator_teks' => $sub_kegiatan_value['indikator'],
										'satuan' => $sub_kegiatan_value['satuan'],
										'target_1' => $sub_kegiatan_value['target_1'],
										'target_2' => $sub_kegiatan_value['target_2'],
										'target_3' => $sub_kegiatan_value['target_3'],
										'target_4' => $sub_kegiatan_value['target_4'],
										'target_5' => $sub_kegiatan_value['target_5'],
										'target_awal' => $sub_kegiatan_value['target_awal'],
										'target_akhir' => $sub_kegiatan_value['target_akhir'],
										'catatan_indikator' => $sub_kegiatan_value['catatan'],
										'indikator_teks_usulan' => $sub_kegiatan_value['indikator_usulan'],
										'satuan_usulan' => $sub_kegiatan_value['satuan_usulan'],
										'target_1_usulan' => $sub_kegiatan_value['target_1_usulan'],
										'target_2_usulan' => $sub_kegiatan_value['target_2_usulan'],
										'target_3_usulan' => $sub_kegiatan_value['target_3_usulan'],
										'target_4_usulan' => $sub_kegiatan_value['target_4_usulan'],
										'target_5_usulan' => $sub_kegiatan_value['target_5_usulan'],
										'target_awal_usulan' => $sub_kegiatan_value['target_awal_usulan'],
										'target_akhir_usulan' => $sub_kegiatan_value['target_akhir_usulan'],
										'catatan_indikator_usulan' => $sub_kegiatan_value['catatan_usulan']
									];
								}
							}
						}
					}
				}
			}
		}
	}
}

//cek program yang belum terselect
if(!empty($program_ids)){
	$sql = "
		SELECT 
			* 
		FROM data_renstra_program_lokal
		WHERE id_unik NOT IN (".implode(',', $program_ids).") 
			AND active=1
			AND id_unit=".$input['id_skpd'];
}else{
	$sql = "
		SELECT 
			* 
		FROM data_renstra_program_lokal 
		WHERE active=1
			AND id_unit=".$input['id_skpd'];
}
$program_all_kosong = $wpdb->get_results($sql, ARRAY_A);

foreach ($program_all_kosong as $keyProgram => $program_value) {
	if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']])){
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']] = [
			'id' => $program_value['id'],
			'id_unik' => $program_value['id_unik'],
			'program_teks' => $program_value['nama_program'],
			'catatan' => $program_value['catatan'],
			'catatan_usulan' => $program_value['catatan_usulan'],
			'pagu_akumulasi_1' => 0,
			'pagu_akumulasi_2' => 0,
			'pagu_akumulasi_3' => 0,
			'pagu_akumulasi_4' => 0,
			'pagu_akumulasi_5' => 0,
			'pagu_akumulasi_1_usulan' => 0,
			'pagu_akumulasi_2_usulan' => 0,
			'pagu_akumulasi_3_usulan' => 0,
			'pagu_akumulasi_4_usulan' => 0,
			'pagu_akumulasi_5_usulan' => 0,		
			'pagu_akumulasi_indikator_1' => 0,
			'pagu_akumulasi_indikator_2' => 0,
			'pagu_akumulasi_indikator_3' => 0,
			'pagu_akumulasi_indikator_4' => 0,
			'pagu_akumulasi_indikator_5' => 0,
			'pagu_akumulasi_indikator_1_usulan' => 0,
			'pagu_akumulasi_indikator_2_usulan' => 0,
			'pagu_akumulasi_indikator_3_usulan' => 0,
			'pagu_akumulasi_indikator_4_usulan' => 0,
			'pagu_akumulasi_indikator_5_usulan' => 0,
			'indikator' => array(),
			'data' => array()
		];
	}

	if(!empty($program_value['id_unik_indikator'])){
		if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']])){

			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_1'] += $program_value['pagu_1'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_2'] += $program_value['pagu_2'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_3'] += $program_value['pagu_3'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_4'] += $program_value['pagu_4'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_5'] += $program_value['pagu_5'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_1_usulan'] += $program_value['pagu_1_usulan'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_2_usulan'] += $program_value['pagu_2_usulan'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_3_usulan'] += $program_value['pagu_3_usulan'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_4_usulan'] += $program_value['pagu_4_usulan'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_5_usulan'] += $program_value['pagu_5_usulan'];

			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']] = [
				'id_unik_indikator' => $program_value['id_unik_indikator'],
				'indikator_teks' => $program_value['indikator'],
				'satuan' => $program_value['satuan'],
				'target_1' => $program_value['target_1'],
				'pagu_1' => $program_value['pagu_1'],
				'target_2' => $program_value['target_2'],
				'pagu_2' => $program_value['pagu_2'],
				'target_3' => $program_value['target_3'],
				'pagu_3' => $program_value['pagu_3'],
				'target_4' => $program_value['target_4'],
				'pagu_4' => $program_value['pagu_4'],
				'target_5' => $program_value['target_5'],
				'pagu_5' => $program_value['pagu_5'],
				'target_awal' => $program_value['target_awal'],
				'target_akhir' => $program_value['target_akhir'],
				'catatan_indikator' => $program_value['catatan'],
				'indikator_teks_usulan' => $program_value['indikator_usulan'],
				'satuan_usulan' => $program_value['satuan_usulan'],
				'target_1_usulan' => $program_value['target_1_usulan'],
				'pagu_1_usulan' => $program_value['pagu_1_usulan'],
				'target_2_usulan' => $program_value['target_2_usulan'],
				'pagu_2_usulan' => $program_value['pagu_2_usulan'],
				'target_3_usulan' => $program_value['target_3_usulan'],
				'pagu_3_usulan' => $program_value['pagu_3_usulan'],
				'target_4_usulan' => $program_value['target_4_usulan'],
				'pagu_4_usulan' => $program_value['pagu_4_usulan'],
				'target_5_usulan' => $program_value['target_5_usulan'],
				'pagu_5_usulan' => $program_value['pagu_5_usulan'],
				'target_awal_usulan' => $program_value['target_awal_usulan'],
				'target_akhir_usulan' => $program_value['target_akhir_usulan'],
				'catatan_indikator_usulan' => $program_value['catatan_usulan']
			];
		}
	}

	if(empty($program_value['id_unik_indikator'])){
		$kegiatan_all = $wpdb->get_results($wpdb->prepare("
			SELECT 
				*  
			FROM data_renstra_kegiatan_lokal 
			WHERE 
				kode_program=%s AND 
				active=1 ORDER BY id
		", $program_value['id_unik']), ARRAY_A);

		foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {								
			if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']])){
				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']] = [
					'id' => $kegiatan_value['id'],
					'id_unik' => $kegiatan_value['id_unik'],
					'kegiatan_teks' => $kegiatan_value['nama_giat'],
					'catatan' => $kegiatan_value['catatan'],
					'catatan_usulan' => $kegiatan_value['catatan_usulan'],
					'pagu_akumulasi_1' => 0,
					'pagu_akumulasi_2' => 0,
					'pagu_akumulasi_3' => 0,
					'pagu_akumulasi_4' => 0,
					'pagu_akumulasi_5' => 0,
					'pagu_akumulasi_1_usulan' => 0,
					'pagu_akumulasi_2_usulan' => 0,
					'pagu_akumulasi_3_usulan' => 0,
					'pagu_akumulasi_4_usulan' => 0,
					'pagu_akumulasi_5_usulan' => 0,		
					'pagu_akumulasi_indikator_1' => 0,
					'pagu_akumulasi_indikator_2' => 0,
					'pagu_akumulasi_indikator_3' => 0,
					'pagu_akumulasi_indikator_4' => 0,
					'pagu_akumulasi_indikator_5' => 0,
					'pagu_akumulasi_indikator_1_usulan' => 0,
					'pagu_akumulasi_indikator_2_usulan' => 0,
					'pagu_akumulasi_indikator_3_usulan' => 0,
					'pagu_akumulasi_indikator_4_usulan' => 0,
					'pagu_akumulasi_indikator_5_usulan' => 0,
					'indikator' => array()
				];
			}

			if(!empty($kegiatan_value['id_unik_indikator'])){
				if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])){

					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_1'] += $kegiatan_value['pagu_1'];
					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_2'] += $kegiatan_value['pagu_2'];
					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_3'] += $kegiatan_value['pagu_3'];
					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_4'] += $kegiatan_value['pagu_4'];
					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_5'] += $kegiatan_value['pagu_5'];
					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_1_usulan'] += $kegiatan_value['pagu_1_usulan'];
					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_2_usulan'] += $kegiatan_value['pagu_2_usulan'];
					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_3_usulan'] += $kegiatan_value['pagu_3_usulan'];
					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_4_usulan'] += $kegiatan_value['pagu_4_usulan'];
					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_5_usulan'] += $kegiatan_value['pagu_5_usulan'];

					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']] = [
						'id_unik_indikator' => $kegiatan_value['id_unik_indikator'],
						'indikator_teks' => $kegiatan_value['indikator'],
						'satuan' => $kegiatan_value['satuan'],
						'target_1' => $kegiatan_value['target_1'],
						'pagu_1' => $kegiatan_value['pagu_1'],
						'target_2' => $kegiatan_value['target_2'],
						'pagu_2' => $kegiatan_value['pagu_2'],
						'target_3' => $kegiatan_value['target_3'],
						'pagu_3' => $kegiatan_value['pagu_3'],
						'target_4' => $kegiatan_value['target_4'],
						'pagu_4' => $kegiatan_value['pagu_4'],
						'target_5' => $kegiatan_value['target_5'],
						'pagu_5' => $kegiatan_value['pagu_5'],
						'target_awal' => $kegiatan_value['target_awal'],
						'target_akhir' => $kegiatan_value['target_akhir'],
						'catatan_indikator' => $kegiatan_value['catatan'],
						'indikator_teks_usulan' => $kegiatan_value['indikator_usulan'],
						'satuan_usulan' => $kegiatan_value['satuan_usulan'],
						'target_1_usulan' => $kegiatan_value['target_1_usulan'],
						'pagu_1_usulan' => $kegiatan_value['pagu_1_usulan'],
						'target_2_usulan' => $kegiatan_value['target_2_usulan'],
						'pagu_2_usulan' => $kegiatan_value['pagu_2_usulan'],
						'target_3_usulan' => $kegiatan_value['target_3_usulan'],
						'pagu_3_usulan' => $kegiatan_value['pagu_3_usulan'],
						'target_4_usulan' => $kegiatan_value['target_4_usulan'],
						'pagu_4_usulan' => $kegiatan_value['pagu_4_usulan'],
						'target_5_usulan' => $kegiatan_value['target_5_usulan'],
						'pagu_5_usulan' => $kegiatan_value['pagu_5_usulan'],
						'target_awal_usulan' => $kegiatan_value['target_awal_usulan'],
						'target_akhir_usulan' => $kegiatan_value['target_akhir_usulan'],
						'catatan_indikator_usulan' => $kegiatan_value['catatan_usulan']
					];
				}
			}

			if(empty($kegiatan_value['id_unik_indikator'])){
				$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare("
					SELECT 
						* 
					FROM data_renstra_sub_kegiatan_lokal 
					WHERE 
						kode_kegiatan=%s AND 
						kode_program=%s AND 
						active=1 ORDER BY id",
						$kegiatan_value['id_unik'],
						$program_value['id_unik'],
				), ARRAY_A);

				foreach ($sub_kegiatan_all as $keySubKegiatan => $sub_kegiatan_value) {
										
					if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']])){

						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']] = [
							'id' => $sub_kegiatan_value['id'],
							'id_unik' => $sub_kegiatan_value['id_unik'],
							'sub_kegiatan_teks' => $sub_kegiatan_value['nama_sub_giat'],
							'catatan' => $sub_kegiatan_value['catatan'],
							'catatan_usulan' => $sub_kegiatan_value['catatan_usulan'],
							'pagu_1' => $sub_kegiatan_value['pagu_1'],
							'pagu_2' => $sub_kegiatan_value['pagu_2'],
							'pagu_3' => $sub_kegiatan_value['pagu_3'],
							'pagu_4' => $sub_kegiatan_value['pagu_4'],
							'pagu_5' => $sub_kegiatan_value['pagu_5'],
							'pagu_1_usulan' => $sub_kegiatan_value['pagu_1_usulan'],
							'pagu_2_usulan' => $sub_kegiatan_value['pagu_2_usulan'],
							'pagu_3_usulan' => $sub_kegiatan_value['pagu_3_usulan'],
							'pagu_4_usulan' => $sub_kegiatan_value['pagu_4_usulan'],
							'pagu_5_usulan' => $sub_kegiatan_value['pagu_5_usulan'],
							'indikator' => array(),
						];
					}

					if(!empty($sub_kegiatan_value['id_unik_indikator'])){
						if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']])){

							$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']] = [

								'id_unik_indikator' => $sub_kegiatan_value['id_unik_indikator'],
								'indikator_teks' => $sub_kegiatan_value['indikator'],
								'satuan' => $sub_kegiatan_value['satuan'],
								'target_1' => $sub_kegiatan_value['target_1'],
								'target_2' => $sub_kegiatan_value['target_2'],
								'target_3' => $sub_kegiatan_value['target_3'],
								'target_4' => $sub_kegiatan_value['target_4'],
								'target_5' => $sub_kegiatan_value['target_5'],
								'target_awal' => $sub_kegiatan_value['target_awal'],
								'target_akhir' => $sub_kegiatan_value['target_akhir'],
								'catatan_indikator' => $sub_kegiatan_value['catatan'],
								'indikator_teks_usulan' => $sub_kegiatan_value['indikator_usulan'],
								'satuan_usulan' => $sub_kegiatan_value['satuan_usulan'],
								'target_1_usulan' => $sub_kegiatan_value['target_1_usulan'],
								'target_2_usulan' => $sub_kegiatan_value['target_2_usulan'],
								'target_3_usulan' => $sub_kegiatan_value['target_3_usulan'],
								'target_4_usulan' => $sub_kegiatan_value['target_4_usulan'],
								'target_5_usulan' => $sub_kegiatan_value['target_5_usulan'],
								'target_awal_usulan' => $sub_kegiatan_value['target_awal_usulan'],
								'target_akhir_usulan' => $sub_kegiatan_value['target_akhir_usulan'],
								'catatan_indikator_usulan' => $sub_kegiatan_value['catatan_usulan']
							];
						}
					}
				}
			}
		}
	}
}

// cek kegiatan yang belum terselect
if(!empty($kegiatan_ids)){
	$sql = "
		SELECT 
			* 
		FROM data_renstra_kegiatan_lokal
		WHERE id_unik NOT IN (".implode(',', $kegiatan_ids).") 
			AND active=1
			AND id_unit=".$input['id_skpd'];
}else{
	$sql = "
		SELECT 
			* 
		FROM data_renstra_kegiatan_lokal 
		WHERE active=1
			AND id_unit=".$input['id_skpd'];
}
$kegiatan_all = $wpdb->get_results($sql, ARRAY_A);

foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {									
	if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']])){
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']] = [
			'id' => $kegiatan_value['id'],
			'id_unik' => $kegiatan_value['id_unik'],
			'kegiatan_teks' => $kegiatan_value['nama_giat'],
			'catatan' => $kegiatan_value['catatan'],
			'catatan_usulan' => $kegiatan_value['catatan_usulan'],
			'pagu_akumulasi_1' => 0,
			'pagu_akumulasi_2' => 0,
			'pagu_akumulasi_3' => 0,
			'pagu_akumulasi_4' => 0,
			'pagu_akumulasi_5' => 0,
			'pagu_akumulasi_1_usulan' => 0,
			'pagu_akumulasi_2_usulan' => 0,
			'pagu_akumulasi_3_usulan' => 0,
			'pagu_akumulasi_4_usulan' => 0,
			'pagu_akumulasi_5_usulan' => 0,		
			'pagu_akumulasi_indikator_1' => 0,
			'pagu_akumulasi_indikator_2' => 0,
			'pagu_akumulasi_indikator_3' => 0,
			'pagu_akumulasi_indikator_4' => 0,
			'pagu_akumulasi_indikator_5' => 0,
			'pagu_akumulasi_indikator_1_usulan' => 0,
			'pagu_akumulasi_indikator_2_usulan' => 0,
			'pagu_akumulasi_indikator_3_usulan' => 0,
			'pagu_akumulasi_indikator_4_usulan' => 0,
			'pagu_akumulasi_indikator_5_usulan' => 0,
			'indikator' => array()
		];
	}

	if(!empty($kegiatan_value['id_unik_indikator'])){
		if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])){

			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1'] += $kegiatan_value['pagu_1'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2'] += $kegiatan_value['pagu_2'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3'] += $kegiatan_value['pagu_3'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4'] += $kegiatan_value['pagu_4'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5'] += $kegiatan_value['pagu_5'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1_usulan'] += $kegiatan_value['pagu_1_usulan'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2_usulan'] += $kegiatan_value['pagu_2_usulan'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3_usulan'] += $kegiatan_value['pagu_3_usulan'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4_usulan'] += $kegiatan_value['pagu_4_usulan'];
			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5_usulan'] += $kegiatan_value['pagu_5_usulan'];

			$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']] = [
				'id_unik_indikator' => $kegiatan_value['id_unik_indikator'],
				'indikator_teks' => $kegiatan_value['indikator'],
				'satuan' => $kegiatan_value['satuan'],
				'target_1' => $kegiatan_value['target_1'],
				'pagu_1' => $kegiatan_value['pagu_1'],
				'target_2' => $kegiatan_value['target_2'],
				'pagu_2' => $kegiatan_value['pagu_2'],
				'target_3' => $kegiatan_value['target_3'],
				'pagu_3' => $kegiatan_value['pagu_3'],
				'target_4' => $kegiatan_value['target_4'],
				'pagu_4' => $kegiatan_value['pagu_4'],
				'target_5' => $kegiatan_value['target_5'],
				'pagu_5' => $kegiatan_value['pagu_5'],
				'target_awal' => $kegiatan_value['target_awal'],
				'target_akhir' => $kegiatan_value['target_akhir'],
				'catatan_indikator' => $kegiatan_value['catatan'],
				'indikator_teks_usulan' => $kegiatan_value['indikator_usulan'],
				'satuan_usulan' => $kegiatan_value['satuan_usulan'],
				'target_1_usulan' => $kegiatan_value['target_1_usulan'],
				'pagu_1_usulan' => $kegiatan_value['pagu_1_usulan'],
				'target_2_usulan' => $kegiatan_value['target_2_usulan'],
				'pagu_2_usulan' => $kegiatan_value['pagu_2_usulan'],
				'target_3_usulan' => $kegiatan_value['target_3_usulan'],
				'pagu_3_usulan' => $kegiatan_value['pagu_3_usulan'],
				'target_4_usulan' => $kegiatan_value['target_4_usulan'],
				'pagu_4_usulan' => $kegiatan_value['pagu_4_usulan'],
				'target_5_usulan' => $kegiatan_value['target_5_usulan'],
				'pagu_5_usulan' => $kegiatan_value['pagu_5_usulan'],
				'target_awal_usulan' => $kegiatan_value['target_awal_usulan'],
				'target_akhir_usulan' => $kegiatan_value['target_akhir_usulan'],
				'catatan_indikator_usulan' => $kegiatan_value['catatan_usulan']
			];
		}
	}

	if(empty($kegiatan_value['id_unik_indikator'])){
		$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare("
			SELECT 
				* 
			FROM data_renstra_sub_kegiatan_lokal 
			WHERE 
				kode_kegiatan=%s AND 
				kode_program=%s AND 
				active=1 ORDER BY id",
				$kegiatan_value['id_unik'],
				$program_value['id_unik'],
			), ARRAY_A);

		foreach ($sub_kegiatan_all as $keySubKegiatan => $sub_kegiatan_value) {
										
			if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']])){

				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']] = [
					'id' => $sub_kegiatan_value['id'],
					'id_unik' => $sub_kegiatan_value['id_unik'],
					'sub_kegiatan_teks' => $sub_kegiatan_value['nama_sub_giat'],
					'catatan' => $sub_kegiatan_value['catatan'],
					'catatan_usulan' => $sub_kegiatan_value['catatan_usulan'],
					'pagu_1' => $sub_kegiatan_value['pagu_1'],
					'pagu_2' => $sub_kegiatan_value['pagu_2'],
					'pagu_3' => $sub_kegiatan_value['pagu_3'],
					'pagu_4' => $sub_kegiatan_value['pagu_4'],
					'pagu_5' => $sub_kegiatan_value['pagu_5'],
					'pagu_1_usulan' => $sub_kegiatan_value['pagu_1_usulan'],
					'pagu_2_usulan' => $sub_kegiatan_value['pagu_2_usulan'],
					'pagu_3_usulan' => $sub_kegiatan_value['pagu_3_usulan'],
					'pagu_4_usulan' => $sub_kegiatan_value['pagu_4_usulan'],
					'pagu_5_usulan' => $sub_kegiatan_value['pagu_5_usulan'],
					'indikator' => array(),
				];
			}

			if(!empty($sub_kegiatan_value['id_unik_indikator'])){
				if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']])){

					$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']] = [

						'id_unik_indikator' => $sub_kegiatan_value['id_unik_indikator'],
						'indikator_teks' => $sub_kegiatan_value['indikator'],
						'satuan' => $sub_kegiatan_value['satuan'],
						'target_1' => $sub_kegiatan_value['target_1'],
						'target_2' => $sub_kegiatan_value['target_2'],
						'target_3' => $sub_kegiatan_value['target_3'],
						'target_4' => $sub_kegiatan_value['target_4'],
						'target_5' => $sub_kegiatan_value['target_5'],
						'target_awal' => $sub_kegiatan_value['target_awal'],
						'target_akhir' => $sub_kegiatan_value['target_akhir'],
						'catatan_indikator' => $sub_kegiatan_value['catatan'],
						'indikator_teks_usulan' => $sub_kegiatan_value['indikator_usulan'],
						'satuan_usulan' => $sub_kegiatan_value['satuan_usulan'],
						'target_1_usulan' => $sub_kegiatan_value['target_1_usulan'],
						'target_2_usulan' => $sub_kegiatan_value['target_2_usulan'],
						'target_3_usulan' => $sub_kegiatan_value['target_3_usulan'],
						'target_4_usulan' => $sub_kegiatan_value['target_4_usulan'],
						'target_5_usulan' => $sub_kegiatan_value['target_5_usulan'],
						'target_awal_usulan' => $sub_kegiatan_value['target_awal_usulan'],
						'target_akhir_usulan' => $sub_kegiatan_value['target_akhir_usulan'],
						'catatan_indikator_usulan' => $sub_kegiatan_value['catatan_usulan']
					];
				}
			}
		}
	}
}

// hapus data kosong jika empty
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data']['kegiatan_kosong']['data']['sub_kegiatan_kosong']['data'])){
	unset($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data']['kegiatan_kosong']['sub_kegiatan_kosong']);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data']['kegiatan_kosong']['data'])){
	unset($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data']['kegiatan_kosong']);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'])){
	unset($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'])){
	unset($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']);
}
if(empty($data_all['data']['tujuan_kosong']['data'])){
	unset($data_all['data']['tujuan_kosong']);
}

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
	$catatan_indikator = '';
	$indikator_tujuan_usulan = '';
	$target_awal_usulan = '';
	$target_1_usulan = '';
	$target_2_usulan = '';
	$target_3_usulan = '';
	$target_4_usulan = '';
	$target_5_usulan = '';
	$target_akhir_usulan = '';
	$satuan_usulan = '';
	$catatan_indikator_usulan = '';

	$bg_rpjm = (!$tujuan['status_rpjm']) ? ' status-rpjm' : '';
	foreach($tujuan['indikator'] as $key => $indikator){
		$indikator_tujuan .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
		$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
		$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
		$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
		$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
		$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
		$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
		$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
		$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
		$catatan_indikator .= '<div class="indikator">'.$indikator['catatan_indikator'].'</div>';
		$indikator_tujuan_usulan .= '<div class="indikator">'.$indikator['indikator_teks_usulan'].'</div>';
		$target_awal_usulan .= '<div class="indikator">'.$indikator['target_awal_usulan'].'</div>';
		$target_1_usulan .= '<div class="indikator">'.$indikator['target_1_usulan'].'</div>';
		$target_2_usulan .= '<div class="indikator">'.$indikator['target_2_usulan'].'</div>';
		$target_3_usulan .= '<div class="indikator">'.$indikator['target_3_usulan'].'</div>';
		$target_4_usulan .= '<div class="indikator">'.$indikator['target_4_usulan'].'</div>';
		$target_5_usulan .= '<div class="indikator">'.$indikator['target_5_usulan'].'</div>';
		$target_akhir_usulan .= '<div class="indikator">'.$indikator['target_akhir_usulan'].'</div>';
		$satuan_usulan .= '<div class="indikator">'.$indikator['satuan_usulan'].'</div>';
		$catatan_indikator_usulan .= '<div class="indikator">'.$indikator['catatan_indikator_usulan'].'</div>';
	}

	$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
	$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
	$sasaran_rpjm = '';
	if(!empty($tujuan['sasaran_rpjm'])){
		$sasaran_rpjm = $tujuan['sasaran_rpjm'];
	}
	$body .= '
			<tr class="tr-tujuan">
				<td class="kiri atas kanan bawah'.$bg_rpjm.'">'.$no_tujuan.'</td>
				<td class="atas kanan bawah'.$bg_rpjm.'">'.$sasaran_rpjm.'</td>
				<td class="atas kanan bawah">'.$tujuan['nama_bidang_urusan'].'</td>
				<td class="atas kanan bawah">'.$tujuan['tujuan_teks'].'</td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah">'.$indikator_tujuan.'</td>
				<td class="atas kanan bawah text_tengah">'.$target_awal.'</td>';
				for ($i=0; $i < $lama_pelaksanaan; $i++) { 
					$body.="<td class=\"atas kanan bawah text_tengah\">".$target_arr[$i]."</td><td class=\"atas kanan bawah text_kanan\"><b>(".$this->_number_format($tujuan['pagu_akumulasi_'.($i+1)]).")</b></td>";
				}
				$body.='<td class="atas kanan bawah text_tengah">'.$target_akhir.'</td>
				<td class="atas kanan bawah">'.$satuan.'</td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah text_tengah">'.$tujuan['urut_tujuan'].'</td>
				<td class="atas kanan bawah">'.$tujuan['catatan'].'</td>
				<td class="atas kanan bawah">'.$catatan_indikator.'</td>
				<td class="atas kanan bawah td-usulan">'.$indikator_tujuan_usulan.'</td>
				<td class="atas kanan bawah text_tengah td-usulan">'.$target_awal_usulan.'</td>';
				for ($i=0; $i < $lama_pelaksanaan; $i++) { 
					$body.="<td class=\"atas kanan bawah text_tengah td-usulan\">".$target_arr_usulan[$i]."</td><td class=\"atas kanan bawah text_kanan td-usulan\"><b>(".$this->_number_format($tujuan['pagu_akumulasi_'.($i+1).'_usulan']).")</b></td>";
				}
				$body.='<td class="atas kanan bawah text_tengah td-usulan">'.$target_akhir_usulan.'</td>
				<td class="atas kanan bawah text_tengah td-usulan">'.$satuan_usulan.'</td>
				<td class="atas kanan bawah td-usulan">'.$tujuan['catatan_usulan'].'</td>
				<td class="atas kanan bawah td-usulan">'.$catatan_indikator_usulan.'</td>
			</tr>
	';

	$no_sasaran=0;
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
		$catatan_indikator = '';
		$indikator_sasaran_usulan = '';
		$target_awal_usulan = '';
		$target_1_usulan = '';
		$target_2_usulan = '';
		$target_3_usulan = '';
		$target_4_usulan = '';
		$target_5_usulan = '';
		$target_akhir_usulan = '';
		$satuan_usulan = '';
		$catatan_indikator_usulan = '';
		foreach($sasaran['indikator'] as $key => $indikator){
			$indikator_sasaran .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
			$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
			$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
			$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
			$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
			$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
			$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
			$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
			$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
			$catatan_indikator .= '<div class="indikator">'.$indikator['catatan_indikator'].'</div>';
			$indikator_sasaran_usulan .= '<div class="indikator">'.$indikator['indikator_teks_usulan'].'</div>';
			$target_awal_usulan .= '<div class="indikator">'.$indikator['target_awal_usulan'].'</div>';
			$target_1_usulan .= '<div class="indikator">'.$indikator['target_1_usulan'].'</div>';
			$target_2_usulan .= '<div class="indikator">'.$indikator['target_2_usulan'].'</div>';
			$target_3_usulan .= '<div class="indikator">'.$indikator['target_3_usulan'].'</div>';
			$target_4_usulan .= '<div class="indikator">'.$indikator['target_4_usulan'].'</div>';
			$target_5_usulan .= '<div class="indikator">'.$indikator['target_5_usulan'].'</div>';
			$target_akhir_usulan .= '<div class="indikator">'.$indikator['target_akhir_usulan'].'</div>';
			$satuan_usulan .= '<div class="indikator">'.$indikator['satuan_usulan'].'</div>';
			$catatan_indikator_usulan .= '<div class="indikator">'.$indikator['catatan_indikator_usulan'].'</div>';
		}

		$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
		$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
		$body .= '
				<tr class="tr-sasaran">
					<td class="kiri atas kanan bawah'.$bg_rpjm.'">'.$no_tujuan.".".$no_sasaran.'</td>
					<td class="atas kanan bawah'.$bg_rpjm.'"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah">'.$sasaran['sasaran_teks'].'</td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah">'.$indikator_sasaran.'</td>
					<td class="atas kanan bawah text_tengah">'.$target_awal.'</td>';
					for ($i=0; $i < $lama_pelaksanaan; $i++) { 
						$body.="<td class=\"atas kanan bawah text_tengah\">".$target_arr[$i]."</td><td class=\"atas kanan bawah text_kanan\"><b>(".$this->_number_format($sasaran['pagu_akumulasi_'.($i+1)]).")</b></td>";
					}
					$body.='<td class="atas kanan bawah text_tengah">'.$target_akhir.'</td>
					<td class="atas kanan bawah">'.$satuan.'</td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah text_tengah">'.$sasaran['urut_sasaran'].'</td>
					<td class="atas kanan bawah">'.$sasaran['catatan'].'</td>
					<td class="atas kanan bawah">'.$catatan_indikator.'</td>
					<td class="atas kanan bawah td-usulan">'.$indikator_sasaran_usulan.'</td>
					<td class="atas kanan bawah text_tengah td-usulan">'.$target_awal_usulan.'</td>';
					for ($i=0; $i < $lama_pelaksanaan; $i++) { 
						$body.="<td class=\"atas kanan bawah text_tengah td-usulan\">".$target_arr_usulan[$i]."</td><td class=\"atas kanan bawah text_kanan td-usulan\"><b>(".$this->_number_format($sasaran['pagu_akumulasi_'.($i+1).'_usulan']).")</b></td>";
					}
					$body.='<td class="atas kanan bawah text_tengah td-usulan">'.$target_akhir_usulan.'</td>
					<td class="atas kanan bawah td-usulan">'.$satuan_usulan.'</td>
					<td class="atas kanan bawah td-usulan">'.$sasaran['catatan_usulan'].'</td>
					<td class="atas kanan bawah td-usulan">'.$catatan_indikator_usulan.'</td>
				</tr>
		';
		
		$no_program=0;
		foreach ($sasaran['data'] as $program) {
			$no_program++;
			$indikator_program = '';
			$target_awal = '';
			$target_1 = '';
			$pagu_1   = '';
			$pagu_1_akumulasi = 0;
			$target_2 = '';
			$pagu_2   = '';
			$pagu_2_akumulasi = 0;
			$target_3 = '';
			$pagu_3   = '';
			$pagu_3_akumulasi = 0;
			$target_4 = '';
			$pagu_4   = '';
			$pagu_4_akumulasi = 0;
			$target_5 = '';
			$pagu_5   = '';
			$pagu_5_akumulasi = 0;
			$target_akhir = '';
			$satuan = '';
			$catatan_indikator = '';
			$indikator_program_usulan = '';
			$target_awal_usulan = '';
			$target_1_usulan = '';
			$pagu_1_usulan   = '';
			$pagu_1_usulan_akumulasi = 0;
			$target_2_usulan = '';
			$pagu_2_usulan   = '';
			$pagu_2_usulan_akumulasi = 0;
			$target_3_usulan = '';
			$pagu_3_usulan   = '';
			$pagu_3_usulan_akumulasi = 0;
			$target_4_usulan = '';
			$pagu_4_usulan   = '';
			$pagu_4_usulan_akumulasi = 0;
			$target_5_usulan = '';
			$pagu_5_usulan   = '';
			$pagu_5_usulan_akumulasi = 0;
			$target_akhir_usulan = '';
			$satuan_usulan = '';
			$catatan_indikator_usulan = '';
			foreach($program['indikator'] as $key => $indikator){
				$indikator_program .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
				$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
				$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
				$pagu_1 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_1']).'</div>';
				$pagu_1_akumulasi += $indikator['pagu_1'];
				$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
				$pagu_2 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_2']).'</div>';
				$pagu_2_akumulasi += $indikator['pagu_2'];
				$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
				$pagu_3 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_3']).'</div>';
				$pagu_3_akumulasi += $indikator['pagu_3'];
				$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
				$pagu_4 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_4']).'</div>';
				$pagu_4_akumulasi += $indikator['pagu_4'];
				$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
				$pagu_5 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_5']).'</div>';
				$pagu_5_akumulasi += $indikator['pagu_5'];
				$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
				$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
				$catatan_indikator .= '<div class="indikator">'.$indikator['catatan_indikator'].'</div>';
				$indikator_program_usulan .= '<div class="indikator">'.$indikator['indikator_teks_usulan'].'</div>';
				$target_awal_usulan .= '<div class="indikator">'.$indikator['target_awal_usulan'].'</div>';
				$target_1_usulan .= '<div class="indikator">'.$indikator['target_1_usulan'].'</div>';
				$pagu_1_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_1_usulan']).'</div>';
				$pagu_1_usulan_akumulasi += $indikator['pagu_1_usulan'];
				$target_2_usulan .= '<div class="indikator">'.$indikator['target_2_usulan'].'</div>';
				$pagu_2_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_2_usulan']).'</div>';
				$pagu_2_usulan_akumulasi += $indikator['pagu_2_usulan'];
				$target_3_usulan .= '<div class="indikator">'.$indikator['target_3_usulan'].'</div>';
				$pagu_3_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_3_usulan']).'</div>';
				$pagu_3_usulan_akumulasi += $indikator['pagu_3_usulan'];
				$target_4_usulan .= '<div class="indikator">'.$indikator['target_4_usulan'].'</div>';
				$pagu_4_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_4_usulan']).'</div>';
				$pagu_4_usulan_akumulasi += $indikator['pagu_4_usulan'];
				$target_5_usulan .= '<div class="indikator">'.$indikator['target_5_usulan'].'</div>';
				$pagu_5_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_5_usulan']).'</div>';
				$pagu_5_usulan_akumulasi += $indikator['pagu_5_usulan'];
				$target_akhir_usulan .= '<div class="indikator">'.$indikator['target_akhir_usulan'].'</div>';
				$satuan_usulan .= '<div class="indikator">'.$indikator['satuan_usulan'].'</div>';
				$catatan_indikator_usulan .= '<div class="indikator">'.$indikator['catatan_indikator_usulan'].'</div>';
			}

			$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
			$pagu_arr = [$pagu_1, $pagu_2, $pagu_3, $pagu_4, $pagu_5];
			$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
			$pagu_arr_usulan = [$pagu_1_usulan, $pagu_2_usulan, $pagu_3_usulan, $pagu_4_usulan, $pagu_5_usulan];
			$body .= '
					<tr class="tr-program">
						<td class="kiri atas kanan bawah'.$bg_rpjm.'">'.$no_tujuan.".".$no_sasaran.".".$no_program.'</td>
						<td class="atas kanan bawah'.$bg_rpjm.'"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah">'.$program['program_teks'].'</td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"><br>'.$indikator_program.'</td>
						<td class="atas kanan bawah text_tengah"><br>'.$target_awal.'</td>';
						for ($i=0; $i < $lama_pelaksanaan; $i++) { 
							$class_warning = '';
							if($program['pagu_akumulasi_'.($i+1)] != $program['pagu_akumulasi_indikator_'.($i+1)]){
								$class_warning = 'peringatan';
							}
							$body.="
							<td class=\"atas kanan bawah text_tengah\"><br>".$target_arr[$i]."</td>
							<td class=\"atas kanan bawah text_kanan $class_warning\"><b>(".$this->_number_format($program['pagu_akumulasi_'.($i+1)]).")</b><br>".$pagu_arr[$i]."</td>";
						}
						$body.='<td class="atas kanan bawah text_tengah"><br>'.$target_akhir.'</td>
						<td class="atas kanan bawah"><br>'.$satuan.'</td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah">'.$program['catatan'].'</td>
						<td class="atas kanan bawah"><br>'.$catatan_indikator.'</td>
						<td class="atas kanan bawah td-usulan"><br>'.$indikator_program_usulan.'</td>
						<td class="atas kanan bawah text_tengah td-usulan"><br>'.$target_awal_usulan.'</td>';
						for ($i=0; $i < $lama_pelaksanaan; $i++) {
							$class_warning = '';
							if($program['pagu_akumulasi_'.($i+1).'_usulan'] != $program['pagu_akumulasi_indikator_'.($i+1).'_usulan']){
								$class_warning = 'peringatan';
							} 
							$body.="
							<td class=\"atas kanan bawah text_tengah td-usulan\"><br>".$target_arr_usulan[$i]."</td>
							<td class=\"atas kanan bawah text_kanan td-usulan $class_warning\"><b>(".$this->_number_format($program['pagu_akumulasi_'.($i+1).'_usulan']).")</b><br>".$pagu_arr_usulan[$i]."</td>";
						}
						$body.='<td class="atas kanan bawah text_tengah td-usulan"><br>'.$target_akhir_usulan.'</td>
						<td class="atas kanan bawah td-usulan"><br>'.$satuan_usulan.'</td>
						<td class="atas kanan bawah td-usulan">'.$program['catatan_usulan'].'</td>
						<td class="atas kanan bawah td-usulan"><br>'.$catatan_indikator_usulan.'</td>
					</tr>
			';
			
			$no_kegiatan=0;
			foreach ($program['data'] as $kegiatan) {
				$no_kegiatan++;
				$indikator_kegiatan = '';
				$target_awal = '';
				$target_1 = '';
				$pagu_1   = '';
				$target_2 = '';
				$pagu_2   = '';
				$target_3 = '';
				$pagu_3   = '';
				$target_4 = '';
				$pagu_4   = '';
				$target_5 = '';
				$pagu_5   = '';
				$target_akhir = '';
				$satuan = '';
				$catatan_indikator = '';
				$indikator_kegiatan_usulan = '';
				$target_awal_usulan = '';
				$target_1_usulan = '';
				$pagu_1_usulan   = '';
				$target_2_usulan = '';
				$pagu_2_usulan   = '';
				$target_3_usulan = '';
				$pagu_3_usulan   = '';
				$target_4_usulan = '';
				$pagu_4_usulan   = '';
				$target_5_usulan = '';
				$pagu_5_usulan   = '';
				$target_akhir_usulan = '';
				$satuan_usulan = '';
				$catatan_indikator_usulan = '';
				foreach($kegiatan['indikator'] as $key => $indikator){
					$indikator_kegiatan .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
					$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
					$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
					$pagu_1 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_1']).'</div>';
					$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
					$pagu_2 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_2']).'</div>';
					$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
					$pagu_3 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_3']).'</div>';
					$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
					$pagu_4 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_4']).'</div>';
					$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
					$pagu_5 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_5']).'</div>';
					$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
					$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
					$catatan_indikator .= '<div class="indikator">'.$indikator['catatan_indikator'].'</div>';
					$indikator_kegiatan_usulan .= '<div class="indikator">'.$indikator['indikator_teks_usulan'].'</div>';
					$target_awal_usulan .= '<div class="indikator">'.$indikator['target_awal_usulan'].'</div>';
					$target_1_usulan .= '<div class="indikator">'.$indikator['target_1_usulan'].'</div>';
					$pagu_1_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_1_usulan']).'</div>';
					$target_2_usulan .= '<div class="indikator">'.$indikator['target_2_usulan'].'</div>';
					$pagu_2_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_2_usulan']).'</div>';
					$target_3_usulan .= '<div class="indikator">'.$indikator['target_3_usulan'].'</div>';
					$pagu_3_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_3_usulan']).'</div>';
					$target_4_usulan .= '<div class="indikator">'.$indikator['target_4_usulan'].'</div>';
					$pagu_4_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_4_usulan']).'</div>';
					$target_5_usulan .= '<div class="indikator">'.$indikator['target_5_usulan'].'</div>';
					$pagu_5_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_5_usulan']).'</div>';
					$target_akhir_usulan .= '<div class="indikator">'.$indikator['target_akhir_usulan'].'</div>';
					$satuan_usulan .= '<div class="indikator">'.$indikator['satuan_usulan'].'</div>';
					$catatan_indikator_usulan .= '<div class="indikator">'.$indikator['catatan_indikator_usulan'].'</div>';
				}

				$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
				$pagu_arr = [$pagu_1, $pagu_2, $pagu_3, $pagu_4, $pagu_5];
				$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
				$pagu_arr_usulan = [$pagu_1_usulan, $pagu_2_usulan, $pagu_3_usulan, $pagu_4_usulan, $pagu_5_usulan];
				$body .= '
						<tr class="tr-kegiatan">
							<td class="kiri atas kanan bawah'.$bg_rpjm.'">'.$no_tujuan.".".$no_sasaran.".".$no_program.".".$no_kegiatan.'</td>
							<td class="atas kanan bawah'.$bg_rpjm.'"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah">'.$kegiatan['kegiatan_teks'].'</td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah"><br>'.$indikator_kegiatan.'</td>
							<td class="atas kanan bawah text_tengah"><br>'.$target_awal.'</td>';
							for ($i=0; $i < $lama_pelaksanaan; $i++) {
								$class_warning = '';
								if($kegiatan['pagu_akumulasi_'.($i+1)] != $kegiatan['pagu_akumulasi_indikator_'.($i+1)]){
									$class_warning = 'peringatan';
								} 
								$body.="<td class=\"atas kanan bawah text_tengah\"><br>".$target_arr[$i]."</td><td class=\"atas kanan bawah text_kanan $class_warning\"><b>(".$this->_number_format($kegiatan['pagu_akumulasi_'.($i+1)]).")</b><br>".$pagu_arr[$i]."</td>";
							}
							$body.='
							<td class="atas kanan bawah text_tengah"><br>'.$target_akhir.'</td>
							<td class="atas kanan bawah"><br>'.$satuan.'</td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah">'.$kegiatan['catatan'].'</td>
							<td class="atas kanan bawah"><br>'.$catatan_indikator.'</td>
							<td class="atas kanan bawah td-usulan"><br>'.$indikator_kegiatan_usulan.'</td>
							<td class="atas kanan bawah text_tengah td-usulan"><br>'.$target_awal_usulan.'</td>';
							for ($i=0; $i < $lama_pelaksanaan; $i++) {
								$class_warning = '';
								if($kegiatan['pagu_akumulasi_'.($i+1).'_usulan'] != $kegiatan['pagu_akumulasi_indikator_'.($i+1).'_usulan']){
									$class_warning = 'peringatan';
								}
								$body.="<td class=\"atas kanan bawah text_tengah td-usulan\"><br>".$target_arr_usulan[$i]."</td><td class=\"atas kanan bawah text_kanan td-usulan $class_warning\"><b>(".$this->_number_format($kegiatan['pagu_akumulasi_'.($i+1).'_usulan']).")</b><br>".$pagu_arr_usulan[$i]."</td>";
							}
							$body.='
							<td class="atas kanan bawah text_tengah td-usulan"><br>'.$target_akhir_usulan.'</td>
							<td class="atas kanan bawah td-usulan"><br>'.$satuan_usulan.'</td>
							<td class="atas kanan bawah td-usulan"><br>'.$kegiatan['catatan_usulan'].'</td>
							<td class="atas kanan bawah td-usulan">'.$catatan_indikator_usulan.'</td>
						</tr>
				';

				$no_sub_kegiatan = 0;
				foreach ($kegiatan['data'] as $key => $sub_kegiatan) {
					
					$no_sub_kegiatan++;
					$indikator_sub_kegiatan = '';
					$target_awal = '';
					$target_1 = '';
					$pagu_1   = '';
					$target_2 = '';
					$pagu_2   = '';
					$target_3 = '';
					$pagu_3   = '';
					$target_4 = '';
					$pagu_4   = '';
					$target_5 = '';
					$pagu_5   = '';
					$target_akhir = '';
					$satuan = '';
					$catatan_indikator = '';
					$indikator_sub_kegiatan_usulan = '';
					$target_awal_usulan = '';
					$target_1_usulan = '';
					$pagu_1_usulan   = '';
					$target_2_usulan = '';
					$pagu_2_usulan   = '';
					$target_3_usulan = '';
					$pagu_3_usulan   = '';
					$target_4_usulan = '';
					$pagu_4_usulan   = '';
					$target_5_usulan = '';
					$pagu_5_usulan   = '';
					$target_akhir_usulan = '';
					$satuan_usulan = '';
					$catatan_indikator_usulan = '';

					foreach ($sub_kegiatan['indikator'] as $key => $indikator) {
						$indikator_sub_kegiatan .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
						$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
						$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
						$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
						$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
						$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
						$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
						$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
						$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
						$catatan_indikator .= '<div class="indikator">'.$indikator['catatan_indikator'].'</div>';
						$indikator_sub_kegiatan_usulan .= '<div class="indikator">'.$indikator['indikator_teks_usulan'].'</div>';
						$target_awal_usulan .= '<div class="indikator">'.$indikator['target_awal_usulan'].'</div>';
						$target_1_usulan .= '<div class="indikator">'.$indikator['target_1_usulan'].'</div>';
						$target_2_usulan .= '<div class="indikator">'.$indikator['target_2_usulan'].'</div>';
						$target_3_usulan .= '<div class="indikator">'.$indikator['target_3_usulan'].'</div>';
						$target_4_usulan .= '<div class="indikator">'.$indikator['target_4_usulan'].'</div>';
						$target_5_usulan .= '<div class="indikator">'.$indikator['target_5_usulan'].'</div>';
						$target_akhir_usulan .= '<div class="indikator">'.$indikator['target_akhir_usulan'].'</div>';
						$satuan_usulan .= '<div class="indikator">'.$indikator['satuan_usulan'].'</div>';
						$catatan_indikator_usulan .= '<div class="indikator">'.$indikator['catatan_indikator_usulan'].'</div>';
					}

					$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
					$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
					$body .= '
							<tr class="tr-sub-kegiatan">
								<td class="kiri atas kanan bawah'.$bg_rpjm.'">'.$no_tujuan.'.'.$no_sasaran.'.'.$no_program.'.'.$no_kegiatan.'.'.$no_sub_kegiatan.'</td>
								<td class="atas kanan bawah'.$bg_rpjm.'"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah">'.$sub_kegiatan['sub_kegiatan_teks'].'</td>
								<td class="atas kanan bawah"><br>'.$indikator_sub_kegiatan.'</td>
								<td class="atas kanan bawah text_tengah"><br>'.$target_awal.'</td>';
								for ($i=0; $i < $lama_pelaksanaan; $i++) {
									$body.="<td class=\"atas kanan bawah text_tengah\"><br>".$target_arr[$i]."</td><td class=\"atas kanan bawah text_kanan\">".$this->_number_format($sub_kegiatan['pagu_'.($i+1)])."</td>";
								}
								$body.='
								<td class="atas kanan bawah text_tengah"><br>'.$target_akhir.'</td>
								<td class="atas kanan bawah"><br>'.$satuan.'</td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah">'.$sub_kegiatan['catatan'].'</td>
								<td class="atas kanan bawah"><br>'.$catatan_indikator.'</td>
								<td class="atas kanan bawah td-usulan"><br>'.$indikator_sub_kegiatan_usulan.'</td>
								<td class="atas kanan bawah text_tengah td-usulan"><br>'.$target_awal_usulan.'</td>';
								for ($i=0; $i < $lama_pelaksanaan; $i++) {
									$body.="<td class=\"atas kanan bawah text_tengah td-usulan\"><br>".$target_arr_usulan[$i]."</td><td class=\"atas kanan bawah text_kanan td-usulan\">".$this->_number_format($sub_kegiatan['pagu_'.($i+1).'_usulan'])."</td>";
								}
								$body.='
								<td class="atas kanan bawah text_tengah td-usulan"><br>'.$target_akhir_usulan.'</td>
								<td class="atas kanan bawah td-usulan"><br>'.$satuan_usulan.'</td>
								<td class="atas kanan bawah td-usulan"><br>'.$kegiatan['catatan_usulan'].'</td>
								<td class="atas kanan bawah td-usulan">'.$catatan_indikator_usulan.'</td>
							</tr>
					';
				}
			}
		}
	}
}

$table='<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 80%; border: 0; table-layout: fixed;margin:30px 0px 30px 0px" contenteditable="false">
			<thead>
				<tr style="background:#ddf0a6">
					<th class="kiri atas kanan bawah text_tengah lebar1">Pagu Akumulasi Sub Kegiatan Per Tahun Anggaran</th>';
					for ($i=0; $i < $lama_pelaksanaan; $i++) {
						$table.="<th class=\"kiri atas kanan bawah text_tengah lebar2\">Tahun ".($i+1)."</th>";
					}
		$table.='</tr>
			</thead>
			<tbody>
				<tr style="background:#a2e9d1">
					<td class="kiri kanan bawah text_tengah"><b>Pagu Penetapan</b></td>';
					for ($i=0; $i < $lama_pelaksanaan; $i++) {
						$table.="<td class=\"atas kanan bawah text_kanan\">".$this->_number_format($data_all['pagu_akumulasi_'.($i+1)])."</td>";
					}
		$table.='</tr>
				<tr style="background:#b0ffb0">
					<td class="kiri kanan bawah text_tengah"><b>Pagu Usulan</b></td>';
					for ($i=0; $i < $lama_pelaksanaan; $i++) {
						$table.="<td class=\" kanan bawah text_kanan\">".$this->_number_format($data_all['pagu_akumulasi_'.($i+1).'_usulan'])."</td>";
					}
		$table.='
				</tr>
				<tr>
					<td class="kiri kanan bawah text_tengah"><b>Selisih</b></td>';
					for ($i=0; $i < $lama_pelaksanaan; $i++) {
						$selisih=($data_all['pagu_akumulasi_'.($i+1)])-($data_all['pagu_akumulasi_'.($i+1).'_usulan']);
						$table.="<td class=\"atas kanan bawah text_kanan ".(!empty($selisih) ? 'peringatan' : '')."\">".$this->_number_format($selisih)."</td>";
					}
		$table.='
				</tr>
			</tbody>
		</table>';
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.css" rel="stylesheet">
<style type="text/css">
	.debug-tujuan, .debug-sasaran, .debug-program, .debug-kegiatan, .debug-kode { display: none; }
	.indikator_program { min-height: 40px; }
	.indikator_kegiatan { min-height: 40px; }
	.modal {overflow-y:auto;}
	.tr-tujuan {
	    background: #0000ff1f;
	}
	.tr-sasaran {
	    background: #ffff0059;
	}
	.tr-program {
	    background: #baffba;
	}
	.tr-kegiatan {
	    background: #f904bd52;
	}
	.tr-total-pagu-opd{
		background: #83efef;
	}
	.peringatan {
		background: #f5c9c9;
	}
	.lebar1{
		width: 15%;
	}
	.lebar2{
		width: 20%;
	}
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">RENCANA STRATEGIS (RENSTRA) <br><?php echo $judul_skpd.'Tahun '.$awal_renstra.' - '.$akhir_renstra.' '.$nama_pemda; ?></h4>
<div id="cetak" title="Laporan MONEV RENSTRA" style="padding: 5px; overflow: auto; height: 80vh;">
	<?php echo $table; ?>
	<table id="table-renstra" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<?php
			$row_head='<tr>
				<th style="width: 85px;" class="row_head_1 atas kiri kanan bawah text_tengah text_blok">No</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Sasaran '.$nama_tipe_relasi.'</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Bidang Urusan</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Tujuan</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Sasaran</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Program</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Kegiatan</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Sub Kegiatan</th>
				<th style="width: 400px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Indikator</th>
				<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Target Awal</th>';
				for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
				$row_head.='<th style="width: 200px;" class="row_head_1_tahun atas kanan bawah text_tengah text_blok">Tahun '.$i.'</th>';
				}
			$row_head.='
				<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Target Akhir</th>
				<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Satuan</th>
				<th style="width: 150px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Keterangan</th>
				<th style="width: 50px;" class="row_head_1 atas kanan bawah text_tengah text_blok">No Urut</th>
				<th style="width: 150px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Catatan</th>
				<th style="width: 150px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Catatan Indikator</th>
				<th style="width: 400px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Indikator Usulan</th>
				<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Target Awal Usulan</th>';
				for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
				$row_head.='<th style="width: 200px;" class="row_head_1_tahun atas kanan bawah text_tengah text_blok td-usulan">Tahun '.$i.' Usulan</th>';
				}
			$row_head.='
				<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Target Akhir Usulan</th>
				<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Satuan Usulan</th>
				<th style="width: 150px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Catatan Usulan</th>
				<th style="width: 150px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Catatan Indikator Usulan</th>
			</tr>
			<tr>';
			for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
				$row_head.='
					<th style="width: 100px;" class="row_head_2 atas kanan bawah text_tengah text_blok">Target</th>
					<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Pagu</th>';
			}
			for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
				$row_head.='
					<th style="width: 100px;" class="row_head_2 atas kanan bawah text_tengah text_blok td-usulan">Target Usulan</th>
					<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok td-usulan">Pagu Usulan</th>';
			}
			$row_head.='</tr>';
			echo $row_head;
			?>

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
			<?php 
				$target_temp = 10;
				for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
					if($i!=1){
						$target_temp=$pagu_temp+1; 
					}
					$pagu_temp=$target_temp+1;
			?>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $target_temp ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp ?></th>
			<?php
				}
			?>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp+1 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp+2 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp+3 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp+4 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp+5 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp+6 ?></th>
				<th class='atas kanan bawah text_tengah text_blok td-usulan'><?php echo $pagu_temp+7 ?></th>
				<th class='atas kanan bawah text_tengah text_blok td-usulan'><?php echo $pagu_temp+8 ?></th>
			<?php 
				$target_temp += 9;
				for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
					if($i!=1){
						$target_temp=$pagu_temp+1; 
					}
					$pagu_temp=$target_temp+1;
			?>
				<th class='atas kanan bawah text_tengah text_blok td-usulan'><?php echo $target_temp ?></th>
				<th class='atas kanan bawah text_tengah text_blok td-usulan'><?php echo $pagu_temp ?></th>
			<?php
				}
			?>
				<th class='atas kanan bawah text_tengah text_blok td-usulan'><?php echo $pagu_temp+1 ?></th>
				<th class='atas kanan bawah text_tengah text_blok td-usulan'><?php echo $pagu_temp+2 ?></th>
				<th class='atas kanan bawah text_tengah text_blok td-usulan'><?php echo $pagu_temp+3 ?></th>
				<th class='atas kanan bawah text_tengah text_blok td-usulan'><?php echo $pagu_temp+4 ?></th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
	</table>
</div>
<h3>Catatan:</h3>
<ol>
	<li>Background warna biru adalah baris tujuan.</li>
	<li>Background warna kuning adalah baris sasaran.</li>
	<li>Background warna hijau adalah baris program.</li>
	<li>Background warna ungu adalah baris kegiatan</li>
	<li>Background warna putih adalah baris sub kegiatan</li>
	<li><b>Debug Cascading <?php echo $nama_tipe_relasi; ?></b> berfungsi untuk melakukan pengecekan relasi antara tujuan RENSTRA dengan sasaran di <?php echo $nama_tipe_relasi; ?>.</li>
	<li>Baris berwarna merah saat dilakukan checklist pada kotak <b>Debug Cascading <?php echo $nama_tipe_relasi; ?></b> menandakan bahwa tujuan RENSTRA belum terkoneksi dengan data di <?php echo $nama_tipe_relasi; ?>.</li>
	<li>Pagu tujuan, sasaran, program dan kegiatan adalah akumulasi dari pagu di sub kegiatan.</li>
	<li>Indikator program memiliki pagu tersendiri sesuai format SIPD. Pada kotak pagu program akan berwarna merah jika pagu akumulasi sub kegiatan dan pagu akumulasi indikator program tidak sama.</li>
	<li>Indikator kegiatan memiliki pagu tersendiri sesuai format SIPD. Pada kotak pagu kegiatan akan berwarna merah jika pagu akumulasi sub kegiatan dan pagu akumulasi indikator kegiatan tidak sama.</li>
</ol>

<div class="modal fade" id="modal-monev" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog" style="max-width: 1200px;" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data Renstra</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<nav>
				  	<div class="nav nav-tabs" id="nav-tab" role="tablist">
					    <a class="nav-item nav-link" id="nav-tujuan-tab" data-toggle="tab" href="#nav-tujuan" role="tab" aria-controls="nav-tujuan" aria-selected="false">Tujuan</a>
					    <a class="nav-item nav-link" id="nav-sasaran-tab" data-toggle="tab" href="#nav-sasaran" role="tab" aria-controls="nav-sasaran" aria-selected="false">Sasaran</a>
					    <a class="nav-item nav-link" id="nav-program-tab" data-toggle="tab" href="#nav-program" role="tab" aria-controls="nav-program" aria-selected="false">Program</a>
					    <a class="nav-item nav-link" id="nav-kegiatan-tab" data-toggle="tab" href="#nav-kegiatan" role="tab" aria-controls="nav-kegiatan" aria-selected="false">Kegiatan</a>
					    <a class="nav-item nav-link" id="nav-sub-kegiatan-tab" data-toggle="tab" href="#nav-sub-kegiatan" role="tab" aria-controls="nav-sub-kegiatan" aria-selected="false">Sub Kegiatan</a>
				  	</div>
				</nav>
				<div class="tab-content" id="nav-tabContent">
				  	<div class="tab-pane fade show active" id="nav-tujuan" role="tabpanel" aria-labelledby="nav-tujuan-tab"></div>
				  	<div class="tab-pane fade" id="nav-sasaran" role="tabpanel" aria-labelledby="nav-sasaran-tab"></div>
				  	<div class="tab-pane fade" id="nav-program" role="tabpanel" aria-labelledby="nav-program-tab"></div>
				  	<div class="tab-pane fade" id="nav-kegiatan" role="tabpanel" aria-labelledby="nav-kegiatan-tab"></div>
				  	<div class="tab-pane fade" id="nav-sub-kegiatan" role="tabpanel" aria-labelledby="nav-sub-kegiatan-tab"></div>
				</div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<!-- Modal indikator renstra -->
<div class="modal fade" id="modal-indikator-renstra" data-backdrop="static" role="dialog" aria-labelledby="modal-indikator-renstra-label" aria-hidden="true">
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

<!-- Modal crud renstra -->
<div class="modal fade" id="modal-crud-renstra" data-backdrop="static"  role="dialog" aria-labelledby="modal-crud-renstra-label" aria-hidden="true">
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

	jQuery("#table-renstra th.row_head_1").attr('rowspan',2);
	jQuery("#table-renstra th.row_head_1_tahun").attr('colspan',2);

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
	<?php if($is_admin): ?>
		+'<a style="margin-left: 10px;" id="singkron-sipd" onclick="return false;" href="#" class="btn btn-danger">Ambil data dari SIPD lokal</a>'
		+'<a style="margin-left: 10px;" onclick="copy_usulan_all(); return false;" href="#" class="btn btn-danger">Copy Data Usulan ke Penetapan</a>'
	<?php endif; ?>
		+'<?php echo $add_renstra; ?>'
		+'<div class="dropdown" style="margin:30px">'
  			+'<button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">LAPORAN</button>'
			  +'<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">'
			    +'<a class="dropdown-item" href="javascript:laporan(\'tc27\')">TC27</a>'
			  +'</div>'
		+'</div>'
		+'<h3 style="margin-top: 20px;">SETTING</h3>'
		// +'<label><input type="checkbox" onclick="tampilkan_edit(this);"> Edit Data RENSTRA</label>'
		+'<label style="margin-left: 20px;"><input type="checkbox" onclick="show_debug(this);"> Debug Cascading <?php echo $nama_tipe_relasi; ?></label>'
		+'<label style="margin-left: 20px;">'
			+'Sembunyikan Baris '
			+'<select id="sembunyikan-baris" onchange="sembunyikan_baris(this);" style="padding: 5px 10px; min-width: 200px;">'
				+'<option value="">Pilih Baris</option>'
				+'<option value="tr-sasaran">Sasaran</option>'
				+'<option value="tr-program">Program</option>'
				+'<option value="tr-kegiatan">Kegiatan</option>'
				+'<option value="tr-sub-kegiatan">Sub Kegiatan</option>'
			+'</select>'
		+'<label style="margin-left: 20px;">'
			+'Sembunyikan Kolom '
			+'<select onchange="sembunyikan_kolom(this);" style="padding: 5px 10px; min-width: 200px;">'
				+'<option value="">Pilih Kolom</option>'
				+'<option value="usulan">Usulan</option>'
			+'</select>'
		+'</label>';
	jQuery('#action-sipd').append(aksi);

	jQuery('#tambah-data').on('click', function(){
        tujuanRenstra();
	});

	jQuery(document).on('click', '.btn-tambah-tujuan', function(){

		jQuery('#wrap-loading').show();

		jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'add_tujuan_renstra',
		          	'api_key': '<?php echo $api_key; ?>',
		          	'tahun_anggaran': '<?php echo $input['tahun_anggaran']; ?>',
		          	'id_unit': '<?php echo $input['id_skpd']; ?>',
		          	'relasi_perencanaan': '<?php echo $relasi_perencanaan; ?>',
		          	'id_tipe_relasi': '<?php echo $id_tipe_relasi; ?>',
				},
				success:function(response){
						jQuery('#wrap-loading').hide();
						if(response.status){
							var bidur_opd = {};
							var html_opd = '<option value="">Pilih Perangkat Daerah</option>';
							response.skpd.map(function(b, i){
								var selected = '';
								if(b.id_skpd == <?php echo $input['id_skpd'] ?>){
									selected = 'selected';
									bidur_opd = b;
								}
								html_opd += '<option '+selected+' value="'+b.id_skpd+'">'+b.kode_skpd+' '+b.nama_skpd+'</option>';
							});
							var html_bidur = '<option value="">Pilih Bidang Urusan</option>';
							response.bidur.map(function(b, i){
								if(
									bidur_opd.bidur_1 == b.kode_bidang_urusan
									|| bidur_opd.bidur_2 == b.kode_bidang_urusan
									|| bidur_opd.bidur_3 == b.kode_bidang_urusan
									|| bidur_opd.bidur_4 == b.kode_bidang_urusan
								){
									html_bidur += '<option value="'+b.id_bidang_urusan+'" data=\''+JSON.stringify(b)+'\'>'+b.nama_bidang_urusan+'</opton>';
								}
							});
							let tujuanModal = jQuery("#modal-crud-renstra");
							let html = '<form id="form-renstra">'
											+'<input type="hidden" name="id_unit" value="'+<?php echo $input['id_skpd']; ?>+'">'
											+'<input type="hidden" name="bidur-all" value="">'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Pilih Perangkat Daerah</label>'
												+'<select class="form-control" id="daftar-skpd" name="nama_unit" disabled>'+html_opd+'</select>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Pilih Bidang Urusan</label>'
												+'<select class="form-control" id="bidang-urusan" name="bidang-urusan" onchange="setBidurAll(this);">'+html_bidur+'</select>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Tujuan <?php echo $nama_tipe_relasi; ?></label>'
												+'<select class="form-control" id="tujuan-rpjm" name="tujuan_rpjm" onchange="pilihTujuanRpjm(this)">'
													+'<option value="">Pilih Tujuan</option>';
													response.data.map(function(value, index){
														html +='<option value="'+value.id_unik+'">'+value.tujuan_teks+'</option>';
													})
												html+='</select>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Sasaran <?php echo $nama_tipe_relasi; ?></label>'
												+'<select class="form-control" id="sasaran-rpjm" name="sasaran_parent"></select>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Tujuan Renstra</label><span onclick="copySasaran();" class="btn btn-primary" style="margin-left: 20px;">Copy dari sasaran</span></label>'
								  				+'<textarea class="form-control" id="tujuan_teks" name="tujuan_teks"></textarea>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Urut Tujuan</label>'
								  				+'<input type="number" class="form-control" name="urut_tujuan" />'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="catatan">Catatan Usulan</label>'
							  					+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>></textarea>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="catatan">Catatan Penetapan</label>'
							  					+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
											+'</div>'
										+'</form>';

							tujuanModal.find('.modal-title').html('Tambah Tujuan');
							tujuanModal.find('.modal-body').html(html);
							tujuanModal.find('.modal-footer').html(''
								+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
									+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
								+'</button>'
								+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
									+'data-action="submit_tujuan_renstra" '
									+'data-view="tujuanRenstra"'
								+'>'
									+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
								+'</button>');
							tujuanModal.find('.modal-dialog').css('maxWidth','');
							tujuanModal.find('.modal-dialog').css('width','');
							tujuanModal.modal('show');
						}else{
							alert(response.message);
						}
				}
			});
	});

	jQuery(document).on('click', '.btn-edit-tujuan', function(){
		jQuery('#wrap-loading').show();

		let tujuanModal = jQuery("#modal-crud-renstra");
		let idtujuan = jQuery(this).data('id');
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_tujuan_renstra",
          		"api_key": "<?php echo $api_key; ?>",
		        'tahun_anggaran': '<?php echo $input['tahun_anggaran']; ?>',
				'id_tujuan': idtujuan, 
			    'id_unit': '<?php echo $input['id_skpd'] ?>',
			    'relasi_perencanaan': '<?php echo $relasi_perencanaan; ?>',
			    'id_tipe_relasi': '<?php echo $id_tipe_relasi; ?>',
          	},
          	dataType: "json",
          	success: function(response){
				jQuery('#wrap-loading').hide();
				if(response.status){
					var bidur_opd = {};
					var html_opd = '<option value="">Pilih Perangkat Daerah</option>';
					response.skpd.map(function(b, i){
						var selected = '';
						if(b.id_skpd == <?php echo $input['id_skpd'] ?>){
							selected = 'selected';
							bidur_opd = b;
						}
						html_opd += '<option '+selected+' value="'+b.id_skpd+'">'+b.kode_skpd+' '+b.nama_skpd+'</option>';
					});
					var html_bidur = '<option value="">Pilih Bidang Urusan</option>';
					var bidur_all_value = '';
					response.bidur.map(function(b, i){
						if(
							bidur_opd.bidur_1 == b.kode_bidang_urusan
							|| bidur_opd.bidur_2 == b.kode_bidang_urusan
							|| bidur_opd.bidur_3 == b.kode_bidang_urusan
						){
							var selected = '';
							if(response.tujuan.kode_bidang_urusan == b.kode_bidang_urusan){
								selected = 'selected';
								bidur_all_value = JSON.stringify(b);
							}
							html_bidur += '<option '+selected+' value="'+b.kode_bidang_urusan+'" data=\''+JSON.stringify(b)+'\'">'+b.nama_bidang_urusan+'</opton>';
						}
					});
					if(response.tujuan.catatan_tujuan == 'null'){
						response.tujuan.catatan_tujuan = '';
					}
					for(var i in response.tujuan){
						if(
							response.tujuan[i] == 'null'
							|| response.tujuan[i] == null
						){
							response.tujuan[i] = '';
						}
					}
					let html = ''
					+'<form id="form-renstra">'
						+'<input type="hidden" name="id" value="'+response.tujuan.id+'">'
						+'<input type="hidden" name="id_unik" value="'+response.tujuan.id_unik+'">'
						+'<input type="hidden" name="id_unit" value="'+<?php echo $input['id_skpd']; ?>+'">'
						+'<input type="hidden" name="bidur-all" value=\''+bidur_all_value+'\'>'
						+'<div class="form-group">'
							+'<label for="tujuan_teks">Pilih Perangkat Daerah</label>'
							+'<select disabled class="form-control" id="daftar-skpd" name="nama_unit">'+html_opd+'</select>'
						+'</div>'
						+'<div class="form-group">'
							+'<label for="tujuan_teks">Pilih Bidang Urusan</label>'
							+'<select class="form-control" id="bidang-urusan" name="bidang-urusan" onchange="setBidurAll(this);" disabled>'+html_bidur+'</select>'
						+'</div>'
						+'<div class="form-group">'
							+'<label for="tujuan_teks">Tujuan <?php echo $nama_tipe_relasi; ?></label>'
							+'<select class="form-control" id="tujuan-rpjm" name="tujuan_rpjm" onchange="pilihTujuanRpjm(this)">'
								+'<option value="">Pilih Tujuan</option>';
								response.tujuan_parent.map(function(value, index){
									var selected = '';
									if(
										response.tujuan_parent_selected
										&& response.tujuan_parent_selected[0]
										&& response.tujuan_parent_selected[0].id_unik == value.id_unik
									){
										selected = "selected";
									}
									html +='<option '+selected+' value="'+value.id_unik+'">'+value.tujuan_teks+'</option>';
								})
						html+='</select>'
						+'</div>'
						+'<div class="form-group">'
							+'<label for="tujuan_teks">Sasaran <?php echo $nama_tipe_relasi; ?></label>'
							+'<select class="form-control" id="sasaran-rpjm" name="sasaran_parent"></select>'
						+'</div>'

						+'<div class="form-group">'
							+'<label for="tujuan_teks">Tujuan Renstra</label><span onclick="copySasaran();" class="btn btn-primary" style="margin-left: 20px;">Copy dari sasaran</span></label>'
					  		+'<textarea class="form-control" id="tujuan_teks" name="tujuan_teks">'+response.tujuan.tujuan_teks+'</textarea>'
						+'</div>'
						+'<div class="form-group">'
							+'<label for="tujuan_teks">Urut Tujuan</label>'
					  		+'<input type="number" class="form-control" name="urut_tujuan" value="'+response.tujuan.urut_tujuan+'" />'
						+'</div>'
						+'<div class="form-group">'
							+'<label for="catatan">Catatan Usulan</label>'
				  			+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>>'+response.tujuan.catatan_usulan+'</textarea>'
						+'</div>'
						+'<div class="form-group">'
							+'<label for="catatan">Catatan Penetapan</label>'
				  			+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.tujuan.catatan+'</textarea>'
						+'</div>'
					+'</form>';

			        tujuanModal.find('.modal-title').html('Edit Tujuan');
					tujuanModal.find('.modal-body').html(html);
					tujuanModal.find('.modal-footer').html(''
						+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
							+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
						+'</button>'
						+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
							+'data-action="update_tujuan_renstra" '
							+'data-view="tujuanRenstra"'
						+'>'
							+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
						+'</button>');
					tujuanModal.find('.modal-dialog').css('maxWidth','');
					tujuanModal.find('.modal-dialog').css('width','');
					tujuanModal.modal('show');
					if(
						response.tujuan_parent_selected
						&& response.tujuan_parent_selected[0]
					){
						pilihTujuanRpjm(document.getElementById('tujuan-rpjm'), function(){
							jQuery('#sasaran-rpjm').val(response.tujuan_parent_selected[0].id_unik_sasaran);
						});
					}
				}else{
					alert(response.message);
				}
          	}
        });
	});

	jQuery(document).on('click', '.btn-hapus-tujuan', function(){
		
		if(confirm('Data akan dihapus, lanjut?')){

	        jQuery('#wrap-loading').show();

			let id_tujuan = jQuery(this).data('id');
			let id_unik = jQuery(this).data('idunik');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action':'delete_tujuan_renstra',
					'api_key':'<?php echo $api_key; ?>',
					'id_tujuan':id_tujuan,
					'id_unik':id_unik,
				},
				success:function(response){
					alert(response.message);
					if(response.status){
						tujuanRenstra();
					}
					jQuery('#wrap-loading').hide();
				}
			})
		}
	});

	jQuery(document).on('click', '.btn-kelola-indikator-tujuan', function(){
        jQuery("#modal-indikator-renstra").find('.modal-body').html('');
		indikatorTujuanRenstra({'id_unik':jQuery(this).data('idunik')});
	});

	jQuery(document).on('click', '.btn-add-indikator-tujuan', function(){

		let indikatorTujuanModal = jQuery("#modal-crud-renstra");
		let id_unik = jQuery(this).data('kodetujuan');
		let html = '<form id="form-renstra">'
					+'<input type="hidden" name="id_unik" value="'+id_unik+'">'
					+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
					+'<div class="form-group">'
						+'<div class="row">'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Usulan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks_usulan">Indikator</label>'
							  				+'<textarea class="form-control" name="indikator_teks_usulan"></textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan_usulan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan_usulan"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal_usulan"/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>_usulan"/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir_usulan"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan_usulan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>></textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Penetapan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks">Indikator</label>'
							  				+'<textarea class="form-control" name="indikator_teks" <?php echo $disabled; ?> ></textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan" <?php echo $disabled; ?> />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					<?php if($is_admin): ?>
						+'<div class="row">'
							+'<div class="col-md-12 text-center">'
								+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
									+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
								+'</button>'
							+'</div>'
						+'</div>'
					<?php endif; ?>
					+'</div>'
				+'</form>';

			indikatorTujuanModal.find('.modal-title').html('Tambah Indikator');
			indikatorTujuanModal.find('.modal-body').html(html);
			indikatorTujuanModal.find('.modal-footer').html(''
				+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
					+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
				+'</button>'
				+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
					+'data-action="submit_indikator_tujuan_renstra" '
					+'data-view="indikatorTujuanRenstra"'
				+'>'
					+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
				+'</button>');
			indikatorTujuanModal.find('.modal-dialog').css('maxWidth','950px');
			indikatorTujuanModal.find('.modal-dialog').css('width','100%');
			indikatorTujuanModal.modal('show');
	});

	jQuery(document).on('click', '.btn-edit-indikator-tujuan', function(){

		jQuery('#wrap-loading').show();

		let indikatorTujuanModal = jQuery("#modal-crud-renstra");
		let id = jQuery(this).data('id');
		let id_unik = jQuery(this).data('idunik');

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_indikator_tujuan_renstra",
          		"api_key": "<?php echo $api_key; ?>",
				'id': id
          	},
          	dataType: "json",
          	success: function(response){
          		jQuery('#wrap-loading').hide();
          		for(var i in response.data){
          			if(
          				response.data[i] == 'null'
          				|| response.data[i] == null
          			){
          				response.data[i] = '';
          			}
          		}
          		let html = '<form id="form-renstra">'
					+'<input type="hidden" name="id" value="'+id+'">'
					+'<input type="hidden" name="id_unik" value="'+id_unik+'">'
					+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
					+'<div class="form-group">'
						+'<div class="row">'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Usulan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks_usulan">Indikator</label>'
						  					+'<textarea class="form-control" name="indikator_teks_usulan">'+response.data.indikator_teks_usulan+'</textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan_usulan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan_usulan" value="'+response.data.satuan_usulan+'" />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal_usulan">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal_usulan" value="'+response.data.target_awal_usulan+'" />'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>_usulan" value="'+response.data.target_<?php echo $i; ?>_usulan+'"/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir_usulan">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir_usulan" value="'+response.data.target_akhir_usulan+'"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan_usulan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>>'+response.data.catatan_usulan+'</textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Penetapan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks">Indikator</label>'
						  					+'<textarea class="form-control" name="indikator_teks" <?php echo $disabled; ?> >'+response.data.indikator_teks+'</textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan" value="'+response.data.satuan+'" <?php echo $disabled; ?> />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal" value="'+response.data.target_awal+'" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>" value="'+response.data.target_<?php echo $i; ?>+'" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir" value="'+response.data.target_akhir+'" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.data.catatan+'</textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					<?php if($is_admin): ?>
						+'<div class="row">'
							+'<div class="col-md-12 text-center">'
								+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
									+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
								+'</button>'
							+'</div>'
						+'</div>'
					<?php endif; ?>
					+'</div>'
				  +'</form>';

				indikatorTujuanModal.find('.modal-title').html('Edit Indikator Tujuan');
				indikatorTujuanModal.find('.modal-body').html(html);
				indikatorTujuanModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
						+'data-action="update_indikator_tujuan_renstra" '
						+'data-view="indikatorTujuanRenstra"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
					+'</button>');
				indikatorTujuanModal.find('.modal-dialog').css('maxWidth','950px');
				indikatorTujuanModal.find('.modal-dialog').css('width','100%');
				indikatorTujuanModal.modal('show');
          	}
		})			
	});

	jQuery(document).on('click', '.btn-delete-indikator-tujuan', function(){

		if(confirm('Data akan dihapus, lanjut?')){
			jQuery('#wrap-loading').show();
			
			let id = jQuery(this).data('id');
			
			let id_unik = jQuery(this).data('idunik');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_indikator_tujuan_renstra',
		          	'api_key': '<?php echo $api_key; ?>',
					'id': id
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						indikatorTujuanRenstra({
							'id_unik': id_unik
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '.btn-detail-tujuan', function(){
		sasaranRenstra({
			'kode_tujuan':jQuery(this).data('kodetujuan')
		});
	});

	jQuery(document).on('click', '.btn-tambah-sasaran', function(){
		let relasi_perencanaan = '<?php echo $relasi_perencanaan; ?>';
		let id_tipe_relasi = '<?php echo $id_tipe_relasi; ?>';
		let id_unit = '<?php echo $input['id_skpd']; ?>';

		let sasaranModal = jQuery("#modal-crud-renstra");
		let kode_tujuan = jQuery(this).data('kodetujuan');
		let html = ''
			+'<form id="form-renstra">'
				+'<input type="hidden" name="kode_tujuan" value="'+kode_tujuan+'">'
				+'<input type="hidden" name="relasi_perencanaan" value="'+relasi_perencanaan+'">'
				+'<input type="hidden" name="id_tipe_relasi" value="'+id_tipe_relasi+'">'
				+'<input type="hidden" name="id_unit" value="'+id_unit+'">'
				+'<div class="form-group">'
					+'<label for="sasaran">Sasaran</label>'
						+'<textarea class="form-control" name="sasaran_teks"></textarea>'
				+'</div>'
				+'<div class="form-group">'
					+'<label for="urut_sasaran">Urut Sasaran</label>'
						+'<input type="number" class="form-control" name="urut_sasaran"/>'
				+'</div>'
				+'<div class="form-group">'
					+'<label for="catatan_usulan">Catatan Usulan</label>'
						+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>></textarea>'
				+'</div>'
				+'<div class="form-group">'
					+'<label for="catatan">Catatan Penetapan</label>'
						+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
				+'</div>'
			+'</form>';

		sasaranModal.find('.modal-body').html('');
		sasaranModal.find('.modal-title').html('Tambah Sasaran');
		sasaranModal.find('.modal-body').html(html);
		sasaranModal.find('.modal-footer').html(''
			+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
				+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
			+'</button>'
			+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
				+'data-action="submit_sasaran_renstra" '
				+'data-view="sasaranRenstra"'
			+'>'
				+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
			+'</button>');
		sasaranModal.find('.modal-dialog').css('maxWidth','');
		sasaranModal.find('.modal-dialog').css('width','');
		sasaranModal.modal('show');
	});

	jQuery(document).on('click', '.btn-edit-sasaran', function(){

		jQuery('#wrap-loading').show();

		let relasi_perencanaan = '<?php echo $relasi_perencanaan; ?>';
		let id_tipe_relasi = '<?php echo $id_tipe_relasi; ?>';
		let id_unit = '<?php echo $input['id_skpd']; ?>';
		let id_sasaran = jQuery(this).data('idsasaran');
		let sasaranModal = jQuery("#modal-crud-renstra");

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_sasaran_renstra",
          		"api_key": "<?php echo $api_key; ?>",
				'id_sasaran': id_sasaran
          	},
          	dataType: "json",
          	success: function(response){
          		for(var i in response.data){
          			if(
          				response.data[i] == 'null'
          				|| response.data[i] == null
          			){
          				response.data[i] = '';
          			}
          		}
          		jQuery('#wrap-loading').hide();
				let html = '<form id="form-renstra">'
							+'<input type="hidden" name="relasi_perencanaan" value="'+relasi_perencanaan+'">'
							+'<input type="hidden" name="id_tipe_relasi" value="'+id_tipe_relasi+'">'
							+'<input type="hidden" name="id_unit" value="'+id_unit+'">'
							+'<input type="hidden" name="kode_tujuan" value="'+response.data.kode_tujuan+'" />'
							+'<input type="hidden" name="kode_sasaran" value="'+response.data.id_unik+'" />'
							+'<div class="form-group">'
								+'<label for="sasaran">Sasaran</label>'
								+'<textarea class="form-control" name="sasaran_teks">'+response.data.sasaran_teks+'</textarea>'
							+'</div>'
							+'<div class="form-group">'
								+'<label for="urut_sasaran">Urut Sasaran</label>'
								+'<input type="number" class="form-control" name="urut_sasaran" value="'+response.data.urut_sasaran+'"/>'
							+'</div>'
							+'<div class="form-group">'
								+'<label for="catatan_usulan">Catatan Usulan</label>'
								+'<textarea class="form-control" name="catatan_usulan" value="'+response.data.catatan_usulan+'" <?php echo $disabled_admin; ?>/></textarea>'
							+'</div>'
							+'<div class="form-group">'
								+'<label for="catatan">Catatan Penetapan</label>'
								+'<textarea class="form-control" name="catatan" value="'+response.data.catatan+'" <?php echo $disabled; ?>/></textarea>'
							+'</div>'
						+'</form>';

				sasaranModal.find('.modal-title').html('Edit Sasaran');
				sasaranModal.find('.modal-body').html('');
				sasaranModal.find('.modal-body').html(html);
				sasaranModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
						+'data-action="update_sasaran_renstra" '
						+'data-view="sasaranRenstra"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
					+'</button>');
				sasaranModal.find('.modal-dialog').css('maxWidth','');
				sasaranModal.find('.modal-dialog').css('width','');
				sasaranModal.modal('show');
          	}
        });
	});

	jQuery(document).on('click', '.btn-hapus-sasaran', function(){

		if(confirm('Data akan dihapus, lanjut?')){
			
			jQuery('#wrap-loading').show();
			let id_sasaran = jQuery(this).data('idsasaran');
			let kode_sasaran = jQuery(this).data('kodesasaran');
			let kode_tujuan = jQuery(this).data('kodetujuan');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_sasaran_renstra',
		      		'api_key': '<?php echo $api_key; ?>',
					'id_sasaran': id_sasaran,
					'kode_sasaran': kode_sasaran,
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						sasaranRenstra({
							'kode_tujuan': kode_tujuan
						});
					}
					jQuery('#wrap-loading').hide();
				}
			})
		}
	});

	jQuery(document).on('click', '.btn-kelola-indikator-sasaran', function(){
		jQuery("#modal-indikator-renstra").find('.modal-body').html('');
		indikatorSasaranRenstra({'id_unik':jQuery(this).data('kodesasaran')});
	});

	jQuery(document).on('click', '.btn-add-indikator-sasaran', function(){

		let indikatorSasaranModal = jQuery("#modal-crud-renstra");
		let id_unik = jQuery(this).data('kodesasaran');
		let html = '<form id="form-renstra">'
					+'<input type="hidden" name="id_unik" value="'+id_unik+'">'
					+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
					+'<div class="form-group">'
						+'<div class="row">'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Usulan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks_usulan">Indikator</label>'
							  				+'<textarea class="form-control" name="indikator_teks_usulan"></textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan_usulan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan_usulan"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal_usulan">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal_usulan"/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>_usulan"/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir_usulan">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir_usulan"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan_usulan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>></textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Penetapan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks">Indikator</label>'
							  				+'<textarea class="form-control" name="indikator_teks" <?php echo $disabled; ?>></textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan" <?php echo $disabled; ?> />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					<?php if($is_admin): ?>
						+'<div class="row">'
							+'<div class="col-md-12 text-center">'
								+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
									+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
								+'</button>'
							+'</div>'
						+'</div>'
					<?php endif; ?>
					+'</div>'
					+'</form>';

			indikatorSasaranModal.find('.modal-title').html('Tambah Indikator');
			indikatorSasaranModal.find('.modal-body').html(html);
			indikatorSasaranModal.find('.modal-footer').html(''
				+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
					+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
				+'</button>'
				+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
					+'data-action="submit_indikator_sasaran_renstra" '
					+'data-view="indikatorSasaranRenstra"'
				+'>'
					+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
				+'</button>');
			indikatorSasaranModal.find('.modal-dialog').css('maxWidth','950px');
			indikatorSasaranModal.find('.modal-dialog').css('width','100%');
			indikatorSasaranModal.modal('show');
	});

	jQuery(document).on('click', '.btn-edit-indikator-sasaran', function(){

		jQuery('#wrap-loading').show();

		let id = jQuery(this).data('id');
		let id_unik = jQuery(this).data('idunik');
		let indikatorSasaranModal = jQuery("#modal-crud-renstra");

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_indikator_sasaran_renstra",
          		"api_key": "<?php echo $api_key; ?>",
							'id': id
          	},
          	dataType: "json",
          	success: function(response){
          		jQuery('#wrap-loading').hide();
          		for(var i in response.data){
          			if(
          				response.data[i] == 'null'
          				|| response.data[i] == null
          			){
          				response.data[i] = '';
          			}
          		}
          		let html = ''
          			+'<form id="form-renstra">'
						+'<input type="hidden" name="id" value="'+id+'">'
						+'<input type="hidden" name="id_unik" value="'+id_unik+'">'
						+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
						+'<div class="form-group">'
							+'<div class="row">'
								+'<div class="col-md-6">'
									+'<div class="card">'
										+'<div class="card-header">Usulan</div>'
										+'<div class="card-body">'
											+'<div class="form-group">'
												+'<label for="indikator_teks_usulan">Indikator</label>'
							  					+'<textarea class="form-control" name="indikator_teks_usulan">'+response.data.indikator_teks_usulan+'</textarea>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="satuan_usulan">Satuan</label>'
								  				+'<input type="text" class="form-control" name="satuan_usulan" value="'+response.data.satuan_usulan+'" />'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="target_awal_usulan">Target awal</label>'
								  				+'<input type="number" class="form-control" name="target_awal_usulan" value="'+response.data.target_awal_usulan+'" />'
											+'</div>'
											<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+'<div class="form-group">'
												+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
								  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>_usulan" value="'+response.data.target_<?php echo $i; ?>_usulan+'"/>'
											+'</div>'
											<?php }; ?>
											+'<div class="form-group">'
												+'<label for="target_akhir_usulan">Target akhir</label>'
								  				+'<input type="number" class="form-control" name="target_akhir_usulan" value="'+response.data.target_akhir_usulan+'"/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="catatan_usulan">Catatan</label>'
								  				+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>>'+response.data.catatan_usulan+'</textarea>'
											+'</div>'
										+'</div>'
									+'</div>'
								+'</div>'
								+'<div class="col-md-6">'
									+'<div class="card">'
										+'<div class="card-header">Penetapan</div>'
										+'<div class="card-body">'
											+'<div class="form-group">'
												+'<label for="indikator_teks">Indikator</label>'
							  					+'<textarea class="form-control" name="indikator_teks" <?php echo $disabled; ?>>'+response.data.indikator_teks+'</textarea>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="satuan">Satuan</label>'
								  				+'<input type="text" class="form-control" name="satuan" value="'+response.data.satuan+'" <?php echo $disabled; ?> />'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="target_awal">Target awal</label>'
								  				+'<input type="number" class="form-control" name="target_awal" value="'+response.data.target_awal+'" <?php echo $disabled; ?>/>'
											+'</div>'
											<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+'<div class="form-group">'
												+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
								  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>" value="'+response.data.target_<?php echo $i; ?>+'" <?php echo $disabled; ?>/>'
											+'</div>'
											<?php }; ?>
											+'<div class="form-group">'
												+'<label for="target_akhir">Target akhir</label>'
								  				+'<input type="number" class="form-control" name="target_akhir" value="'+response.data.target_akhir+'" <?php echo $disabled; ?>/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="catatan">Catatan</label>'
								  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.data.catatan+'</textarea>'
											+'</div>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						<?php if($is_admin): ?>
							+'<div class="row">'
								+'<div class="col-md-12 text-center">'
									+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
										+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
									+'</button>'
								+'</div>'
							+'</div>'
						<?php endif; ?>
						+'</div>'
				  	+'</form>';

				indikatorSasaranModal.find('.modal-title').html('Edit Indikator Sasaran');
				indikatorSasaranModal.find('.modal-body').html(html);
				indikatorSasaranModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
						+'data-action="update_indikator_sasaran_renstra" '
						+'data-view="indikatorSasaranRenstra"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
					+'</button>');
				indikatorSasaranModal.find('.modal-dialog').css('maxWidth','950px');
				indikatorSasaranModal.find('.modal-dialog').css('width','100%');
				indikatorSasaranModal.modal('show');
          	}
		})			
	});

	jQuery(document).on('click', '.btn-delete-indikator-sasaran', function(){

		if(confirm('Data akan dihapus, lanjut?')){
	
			jQuery('#wrap-loading').show();

			let id = jQuery(this).data('id');
			let id_unik = jQuery(this).data('idunik');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_indikator_sasaran_renstra',
		      		'api_key': '<?php echo $api_key; ?>',
					'id': id
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						indikatorSasaranRenstra({
							'id_unik': id_unik
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '.btn-detail-sasaran', function(){
		programRenstra({
			'kode_sasaran':jQuery(this).data('kodesasaran')
		});
	});

	jQuery(document).on('click', '.btn-tambah-program', function(){
		jQuery('#wrap-loading').show();
		let kode_sasaran = jQuery(this).data('kodesasaran');
		get_bidang_urusan().then(function(){
  			jQuery('#wrap-loading').hide();
			let html = ''
				+'<form id="form-renstra">'
					+'<input type="hidden" name="kode_sasaran" value="'+kode_sasaran+'"/>'
					+'<div class="form-group">'
				    	+'<label>Pilih Urusan</label>'
				    	+'<select class="form-control" name="id_urusan" id="urusan-teks" readonly></select>'
				  	+'</div>'
				  	+'<div class="form-group">'
				    	+'<label>Pilih Bidang</label>'
				    	+'<select class="form-control" name="id_bidang" id="bidang-teks" readonly></select>'
				  	+'</div>'
				  	+'<div class="form-group">'
				    	+'<label>Pilih Program</label>'
				    	+'<select class="form-control" name="id_program" id="program-teks"></select>'
				  	+'</div>'
				  	+'<div class="form-group">'
				    	+'<label>Catatan Usulan</label>'
				    	+'<textarea  <?php echo $disabled_admin; ?> class="form-control" name="catatan_usulan"></textarea>'
				  	+'</div>'
				  	+'<div class="form-group">'
				    	+'<label>Catatan Penetapan</label>'
				    	+'<textarea  <?php echo $disabled; ?> class="form-control" name="catatan"></textarea>'
				  	+'</div>'
				+'</form>';

		    jQuery("#modal-crud-renstra").find('.modal-title').html('Tambah Program');
		    jQuery("#modal-crud-renstra").find('.modal-body').html(html);
			jQuery("#modal-crud-renstra").find('.modal-footer').html(''
				+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
					+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
				+'</button>'
				+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
					+'data-action="submit_program_renstra" '
					+'data-view="programRenstra"'
				+'>'
					+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
				+'</button>');
			jQuery("#modal-crud-renstra").find('.modal-dialog').css('maxWidth','');
			jQuery("#modal-crud-renstra").find('.modal-dialog').css('width','');

			jQuery("#modal-crud-renstra").modal('show');
			get_urusan();
			get_bidang();
			get_program();
			var nm_bidang = jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text();
			jQuery('#bidang-teks').val(nm_bidang.trim()).trigger('change');
  		});	
	});

	jQuery(document).on('click', '.btn-edit-program', function(){
		
		jQuery('#wrap-loading').show();

		let programModal = jQuery("#modal-crud-renstra");
		
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_program_renstra",
          		"api_key": "<?php echo $api_key; ?>",
				'id_unik': jQuery(this).data('kodeprogram')
          	},
          	dataType: "json",
          	success: function(res){

          		let id_program = res.data.id_program;

          		get_bidang_urusan().then(function(){

			        jQuery('#wrap-loading').hide();
					for(var i in res.data){
						if(
							res.data[i] == 'null'
							|| res.data[i] == null
						){
							res.data[i] = '';						}
					}
					let html = '<form id="form-renstra">'
								+'<input type="hidden" name="id_unik" value="'+res.data.id_unik+'"/>'
								+'<input type="hidden" name="kode_sasaran" value="'+res.data.kode_sasaran+'"/>'
								+'<div class="form-group">'
							    	+'<label>Pilih Urusan</label>'
							    	+'<select class="form-control" name="id_urusan" id="urusan-teks" readonly></select>'
							  	+'</div>'
							  	+'<div class="form-group">'
							    	+'<label>Pilih Bidang</label>'
							    	+'<select class="form-control" name="id_bidang" id="bidang-teks" readonly></select>'
							  	+'</div>'
							  	+'<div class="form-group">'
							    	+'<label>Pilih Program</label>'
							    	+'<select class="form-control" name="id_program" id="program-teks"></select>'
							  	+'</div>'
							  	+'<div class="form-group">'
							    	+'<label>Catatan Usulan</label>'
							    	+'<textarea  <?php echo $disabled_admin; ?> class="form-control" name="catatan_usulan">'+res.data.catatan_usulan+'</textarea>'
							  	+'</div>'
							  	+'<div class="form-group">'
							    	+'<label>Catatan Penetapan</label>'
							    	+'<textarea  <?php echo $disabled; ?> class="form-control" name="catatan">'+res.data.catatan+'</textarea>'
							  	+'</div>'
							+'</form>';

					programModal.find('.modal-title').html('Edit Program');
				    programModal.find('.modal-body').html('');
					programModal.find('.modal-body').html(html);
					programModal.find('.modal-footer').html(''
						+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
							+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
						+'</button>'
						+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
							+'data-action="update_program_renstra" '
							+'data-view="programRenstra"'
						+'>'
							+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
						+'</button>');
					programModal.find('.modal-dialog').css('maxWidth','');
					programModal.find('.modal-dialog').css('width','');

					programModal.modal('show');

					get_urusan();
					get_bidang();
					var val_urusan = jQuery('#urusan-teks').val();
					var val_bidang = jQuery('#bidang-teks').val();
					for(var nm_urusan in all_program){
						for(var nm_bidang in all_program[nm_urusan]){
							for(var nm_program in all_program[nm_urusan][nm_bidang]){
								if(
									id_program 
									&& id_program == all_program[nm_urusan][nm_bidang][nm_program].id_program
								){
									if(val_urusan.trim() != nm_urusan){
										jQuery('#urusan-teks').val(nm_urusan).trigger('change');
									}
									if(val_bidang.trim() != nm_bidang){
										jQuery('#bidang-teks').val(nm_bidang).trigger('change');
									}
								}
							}
						}
					}
					get_program(false, id_program);
          		});
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
					'action': 'delete_program_renstra',
		      		'api_key': '<?php echo $api_key; ?>',
					'kode_program': kode_program,
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						programRenstra({
							'kode_sasaran': kode_sasaran
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '.btn-kelola-indikator-program', function(){
		jQuery("#modal-indikator-renstra").find('.modal-body').html('');
		indikatorProgramRenstra({'kode_program':jQuery(this).data('kodeprogram')});
	});

	jQuery(document).on('click', '.btn-add-indikator-program', function(){
		
		jQuery('#wrap-loading').show();
		
		let kode_program = jQuery(this).data('kodeprogram');
		
		let html = '';

		get_bidang_urusan(true).then(function(){
			
			jQuery('#wrap-loading').hide();

			html += ''
				+'<form id="form-renstra">'
					+'<input type="hidden" name="kode_program" value='+kode_program+'>'
					+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
					+'<div class="form-group">'
						+'<div class="row">'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Usulan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks_usulan">Indikator</label>'
							  			+'<textarea class="form-control" name="indikator_teks_usulan"></textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan_usulan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan_usulan"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal_usulan">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal_usulan"/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>_usulan"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="pagu_<?php echo $i; ?>_usulan">Pagu tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>_usulan"/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir_usulan">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir_usulan"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan_usulan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>></textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Penetapan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks">Indikator</label>'
							  			+'<textarea class="form-control" name="indikator_teks" <?php echo $disabled; ?>></textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan" <?php echo $disabled; ?> />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="pagu_<?php echo $i; ?>">Pagu tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					<?php if($is_admin): ?>
						+'<div class="row">'
							+'<div class="col-md-12 text-center">'
								+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
									+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
								+'</button>'
							+'</div>'
						+'</div>'
					<?php endif; ?>
					+'</div>'
				+'</form>';
				
			jQuery("#modal-crud-renstra").find('.modal-title').html('Tambah Indikator');
			jQuery("#modal-crud-renstra").find('.modal-body').html(html);
			jQuery("#modal-crud-renstra").find('.modal-footer').html(''
				+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
					+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
				+'</button>'
				+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
					+'data-action="submit_indikator_program_renstra" '
					+'data-view="indikatorProgramRenstra"'
				+'>'
					+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
				+'</button>');
			jQuery("#modal-crud-renstra").find('.modal-dialog').css('maxWidth','950px');
			jQuery("#modal-crud-renstra").find('.modal-dialog').css('width','100%');
			jQuery("#modal-crud-renstra").modal('show');
		}); 
	});

	jQuery(document).on('click', '.btn-edit-indikator-program', function(){

		jQuery('#wrap-loading').show();

		let id = jQuery(this).data('id');
		let kode_program = jQuery(this).data('kodeprogram');

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_indikator_program_renstra",
          		"api_key": "<?php echo $api_key; ?>",
				'id': id,
				'kode_program': kode_program
          	},
          	dataType: "json",
          	success: function(response){

          		jQuery('#wrap-loading').hide();
          		for(var i in response.data){
          			if(
          				response.data[i] == 'null'
          				|| response.data[i] == null
          			){
          				response.data[i] = '';
          			}
          		}

          		let html = ''
          		+'<form id="form-renstra">'
					+'<input type="hidden" name="id" value="'+id+'">'
					+'<input type="hidden" name="kode_program" value="'+kode_program+'">'
					+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
					+'<div class="form-group">'
						+'<div class="row">'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Usulan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks_usulan">Indikator</label>'
						  					+'<textarea class="form-control" name="indikator_teks_usulan">'+response.data.indikator_usulan+'</textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan_usulan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan_usulan" value="'+response.data.satuan_usulan+'" />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal_usulan">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal_usulan" value="'+response.data.target_awal_usulan+'" />'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>_usulan" value="'+response.data.target_<?php echo $i; ?>_usulan+'"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="pagu_<?php echo $i; ?>_usulan">Pagu tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>_usulan" value="'+response.data.pagu_<?php echo $i; ?>_usulan+'"/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir_usulan">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir_usulan" value="'+response.data.target_akhir_usulan+'"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan_usulan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>>'+response.data.catatan_usulan+'</textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Penetapan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks">Indikator</label>'
						  					+'<textarea class="form-control" name="indikator_teks" <?php echo $disabled; ?>>'+response.data.indikator+'</textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan" value="'+response.data.satuan+'" <?php echo $disabled; ?> />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal" value="'+response.data.target_awal+'" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>" value="'+response.data.target_<?php echo $i; ?>+'" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="pagu_<?php echo $i; ?>">Pagu tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>" value="'+response.data.pagu_<?php echo $i; ?>+'" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir" value="'+response.data.target_akhir+'" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.data.catatan+'</textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					<?php if($is_admin): ?>
						+'<div class="row">'
							+'<div class="col-md-12 text-center">'
								+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
									+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
								+'</button>'
							+'</div>'
						+'</div>'
					<?php endif; ?>
					+'</div>'
			  	+'</form>';

				jQuery("#modal-crud-renstra").find('.modal-title').html('Edit Indikator Program');
				jQuery("#modal-crud-renstra").find('.modal-body').html(html);
				jQuery("#modal-crud-renstra").find('.modal-footer').html(''
					+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
						+'data-action="update_indikator_program_renstra" '
						+'data-view="indikatorProgramRenstra"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
					+'</button>');	
				jQuery("#modal-crud-renstra").find('.modal-dialog').css('maxWidth','950px');
				jQuery("#modal-crud-renstra").find('.modal-dialog').css('width','100%');
				jQuery("#modal-crud-renstra").modal('show');
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
					'action': 'delete_indikator_program_renstra',
		      		'api_key': '<?php echo $api_key; ?>',
					'id': id,
					'kode_program': kode_program,
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						indikatorProgramRenstra({
							'kode_program': kode_program
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '.btn-detail-program', function(){
		kegiatanRenstra({
			'kode_program':jQuery(this).data('kodeprogram'),
			'id_program':jQuery(this).data('idprogram'),
		});
	});

	jQuery(document).on('click', '.btn-tambah-kegiatan', function(){

		jQuery('#wrap-loading').show();

		let kegiatanModal = jQuery("#modal-crud-renstra");
		let kode_program = jQuery(this).data('kodeprogram');
		let id_program = jQuery(this).data('idprogram');

		jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'add_kegiatan_renstra',
		      		'api_key': '<?php echo $api_key; ?>',
					'id_program': id_program,
				},
				success:function(response){

					jQuery('#wrap-loading').hide();
		  				
					let html = '<form id="form-renstra">'
								+'<input type="hidden" name="kode_program" value="'+kode_program+'"/>'
								+'<input type="hidden" name="id_program" value="'+id_program+'"/>'
								+'<input type="hidden" name="kegiatan_teks" id="kegiatan_teks"/>'
								+'<div class="form-group">'
									+'<label for="kegiatan_teks">Kegiatan</label>'
									+'<select class="form-control" id="id_kegiatan" name="id_kegiatan" onchange="setTeks(this, \'kegiatan_teks\', \'id_kegiatan\')">';
										html+='<option value="">Pilih Kegiatan</option>';
										response.data.map(function(value, index){
											html +='<option value="'+value.id+'">'+value.kegiatan_teks+'</option>';
										})
										html+=''
									+'</select>'
								+'</div>'
								+'<div class="form-group">'
									+'<label>Catatan Usulan</label>'
									+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>></textarea>'
								+'</div>'
								+'<div class="form-group">'
									+'<label>Catatan Penetapan</label>'
									+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
								+'</div>'
							+'</form>';

				    kegiatanModal.find('.modal-title').html('Tambah Kegiatan');
					kegiatanModal.find('.modal-body').html(html);
					kegiatanModal.find('.modal-footer').html(''
						+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
							+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
						+'</button>'
						+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
							+'data-action="submit_kegiatan_renstra" '
							+'data-view="kegiatanRenstra"'
						+'>'
							+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
						+'</button>');
					kegiatanModal.find('.modal-dialog').css('maxWidth','');
					kegiatanModal.find('.modal-dialog').css('width','');
					kegiatanModal.modal('show');
					jQuery("#id_kegiatan").select2({width: '100%'});
				}
		});	
	});

	jQuery(document).on('click', '.btn-edit-kegiatan', function(){
		jQuery('#wrap-loading').show();

		let kegiatanModal = jQuery("#modal-crud-renstra");
		let id_program = jQuery(this).data('idprogram');
		let id_kegiatan = jQuery(this).data('id');

		jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'edit_kegiatan_renstra',
		      		'api_key': '<?php echo $api_key; ?>',
					'id_program': id_program,
					'id_kegiatan': id_kegiatan,
				},
				success:function(response){

					jQuery('#wrap-loading').hide();
		  			for(var i in response.kegiatan){
		  				if(
		  					response.kegiatan[i] == 'null'
		  					|| response.kegiatan[i] == null
		  				){
		  					response.kegiatan[i] = '';
		  				}
		  			}
					let html = ''
						+'<form id="form-renstra">'
							+'<input type="hidden" name="id" id="id" value="'+response.kegiatan.id+'"/>'
							+'<input type="hidden" name="id_unik" id="id_unik" value="'+response.kegiatan.id_unik+'"/>'
							+'<input type="hidden" name="id_program" id="id_program" value="'+response.kegiatan.id_program+'"/>'
							+'<input type="hidden" name="kode_program" id="kode_program" value="'+response.kegiatan.kode_program+'"/>'
							+'<input type="hidden" name="kegiatan_teks" id="kegiatan_teks"/>'
						  	+'<div class="form-group">'
								+'<label for="kegiatan_teks">Kegiatan</label>'
								+'<select class="form-control" id="id_kegiatan" name="id_kegiatan" onchange="setTeks(this, \'kegiatan_teks\', \'id_kegiatan\')">';
									html+='<option value="">Pilih Kegiatan</option>';
									response.data.map(function(value, index){
										html +='<option value="'+value.id+'">'+value.kegiatan_teks+'</option>';
									});
								html+='</select>'
							+'</div>'
							+'<div class="form-group">'
								+'<label>Catatan Usulan</label>'
								+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>>'+response.kegiatan.catatan_usulan+'</textarea>'
							+'</div>'
							+'<div class="form-group">'
								+'<label>Catatan Penetapan</label>'
								+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.kegiatan.catatan+'</textarea>'
							+'</div>'
						+'</form>';

				    kegiatanModal.find('.modal-title').html('Edit Kegiatan');
				    kegiatanModal.find('.modal-body').html('');
					kegiatanModal.find('.modal-body').html(html);
					kegiatanModal.find('.modal-footer').html(''
						+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
							+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
						+'</button>'
						+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
							+'data-action="update_kegiatan_renstra" '
							+'data-view="kegiatanRenstra"'
						+'>'
							+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
						+'</button>');
					kegiatanModal.find('.modal-dialog').css('maxWidth','');
					kegiatanModal.find('.modal-dialog').css('width','');
					kegiatanModal.modal('show');
					jQuery("#id_kegiatan").val(response.kegiatan.id_giat);
					jQuery("#id_kegiatan").select2({width: '100%'});
				}
		});	
	});

	jQuery(document).on('click', '.btn-hapus-kegiatan', function(){

		if(confirm('Data akan dihapus, lanjut?')){

			jQuery('#wrap-loading').show();
			
			let id = jQuery(this).data('id');	
			let id_unik = jQuery(this).data('kodekegiatan');
			let id_program = jQuery(this).data('idprogram');
			let kode_program = jQuery(this).data('kodeprogram');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_kegiatan_renstra',
		      		'api_key': '<?php echo $api_key; ?>',
					'id': id,
					'id_unik': id_unik,
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						kegiatanRenstra({
							'id_program': id_program,
							'kode_program': kode_program
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '.btn-kelola-indikator-kegiatan', function(){
        jQuery("#modal-indikator-renstra").find('.modal-body').html('');
		indikatorKegiatanRenstra({'id_unik':jQuery(this).data('kodekegiatan')});
	});

	jQuery(document).on('click', '.btn-add-indikator-kegiatan', function(){

		let indikatorKegiatanModal = jQuery("#modal-crud-renstra");
		let id_unik = jQuery(this).data('kodekegiatan');
		let html = ''
		+'<form id="form-renstra">'
			+'<input type="hidden" name="id_unik" value="'+id_unik+'">'
			+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
			+'<div class="form-group">'
				+'<div class="row">'
					+'<div class="col-md-6">'
						+'<div class="card">'
							+'<div class="card-header">Usulan</div>'
							+'<div class="card-body">'
								+'<div class="form-group">'
									+'<label for="indikator_teks_usulan">Indikator</label>'
					  				+'<textarea class="form-control" name="indikator_teks_usulan"></textarea>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="satuan_usulan">Satuan</label>'
					  				+'<input type="text" class="form-control" name="satuan_usulan"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="target_awal_usulan">Target awal</label>'
					  				+'<input type="number" class="form-control" name="target_awal_usulan"/>'
								+'</div>'
								<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+'<div class="form-group">'
									+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
					  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>_usulan"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_<?php echo $i; ?>_usulan">Pagu <?php echo $i; ?></label>'
					  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>_usulan"/>'
								+'</div>'
								<?php }; ?>
								+'<div class="form-group">'
									+'<label for="target_akhir_usulan">Target akhir</label>'
					  				+'<input type="number" class="form-control" name="target_akhir_usulan"/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="catatan_usulan">Catatan</label>'
					  				+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>></textarea>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</div>'
					+'<div class="col-md-6">'
						+'<div class="card">'
							+'<div class="card-header">Penetapan</div>'
							+'<div class="card-body">'
								+'<div class="form-group">'
									+'<label for="indikator_teks">Indikator</label>'
					  				+'<textarea class="form-control" name="indikator_teks" <?php echo $disabled; ?>></textarea>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="satuan">Satuan</label>'
					  				+'<input type="text" class="form-control" name="satuan" <?php echo $disabled; ?> />'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="target_awal">Target awal</label>'
					  				+'<input type="number" class="form-control" name="target_awal" <?php echo $disabled; ?>/>'
								+'</div>'
								<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+'<div class="form-group">'
									+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
					  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pagu_<?php echo $i; ?>">Pagu <?php echo $i; ?></label>'
					  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
								+'</div>'
								<?php }; ?>
								+'<div class="form-group">'
									+'<label for="target_akhir">Target akhir</label>'
					  				+'<input type="number" class="form-control" name="target_akhir" <?php echo $disabled; ?>/>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="catatan">Catatan</label>'
					  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</div>'
				+'</div>'
			<?php if($is_admin): ?>
				+'<div class="row">'
					+'<div class="col-md-12 text-center">'
						+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
							+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
						+'</button>'
					+'</div>'
				+'</div>'
			<?php endif; ?>
			+'</div>'
		+'</form>';

		indikatorKegiatanModal.find('.modal-title').html('Tambah Indikator');
		indikatorKegiatanModal.find('.modal-body').html(html);
		indikatorKegiatanModal.find('.modal-footer').html(''
			+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
				+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
			+'</button>'
			+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
				+'data-action="submit_indikator_kegiatan_renstra" '
				+'data-view="indikatorKegiatanRenstra"'
			+'>'
				+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
			+'</button>');
		indikatorKegiatanModal.find('.modal-dialog').css('maxWidth','950px');
		indikatorKegiatanModal.find('.modal-dialog').css('width','100%');
		indikatorKegiatanModal.modal('show');
	});

	jQuery(document).on('click', '.btn-edit-indikator-kegiatan', function(){

		jQuery('#wrap-loading').show();

		let id = jQuery(this).data('id');
		let kode_kegiatan = jQuery(this).data('kodekegiatan');

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "edit_indikator_kegiatan_renstra",
          		"api_key": "<?php echo $api_key; ?>",
				'id': id,
				'kode_kegiatan': kode_kegiatan
          	},
          	dataType: "json",
          	success: function(response){
          		jQuery('#wrap-loading').hide();
          		for(var i in response.data){
          			if(
          				response.data[i] == 'null'
          				|| response.data[i] == null
          			){
          				response.data[i] = '';
          			}
          		}
          		let html = ''
          		+'<form id="form-renstra">'
					+'<input type="hidden" name="id" value="'+id+'">'
					+'<input type="hidden" name="id_unik" value="'+kode_kegiatan+'">'
					+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
					+'<div class="form-group">'
						+'<div class="row">'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Usulan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks_usulan">Indikator</label>'
						  					+'<textarea class="form-control" name="indikator_teks_usulan">'+response.data.indikator_usulan+'</textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan_usulan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan_usulan" value="'+response.data.satuan_usulan+'" />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal_usulan">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal_usulan" value="'+response.data.target_awal_usulan+'" />'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>_usulan" value="'+response.data.target_<?php echo $i; ?>_usulan+'"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="pagu_<?php echo $i; ?>_usulan">Pagu <?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>_usulan" value="'+response.data.pagu_<?php echo $i; ?>_usulan+'"/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir_usulan">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir_usulan" value="'+response.data.target_akhir_usulan+'"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan_usulan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>>'+response.data.catatan_usulan+'</textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Penetapan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="indikator_teks">Indikator</label>'
						  					+'<textarea class="form-control" name="indikator_teks" <?php echo $disabled; ?>>'+response.data.indikator+'</textarea>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="satuan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan" value="'+response.data.satuan+'" <?php echo $disabled; ?> />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal">Target awal</label>'
							  				+'<input type="number" class="form-control" name="target_awal" value="'+response.data.target_awal+'" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>" value="'+response.data.target_<?php echo $i; ?>+'" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="pagu_<?php echo $i; ?>">Pagu <?php echo $i; ?></label>'
							  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>" value="'+response.data.pagu_<?php echo $i; ?>+'"  <?php echo $disabled; ?>/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir">Target akhir</label>'
							  				+'<input type="number" class="form-control" name="target_akhir" value="'+response.data.target_akhir+'" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.data.catatan+'</textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					<?php if($is_admin): ?>
						+'<div class="row">'
							+'<div class="col-md-12 text-center">'
								+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
									+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
								+'</button>'
							+'</div>'
						+'</div>'
					<?php endif; ?>
					+'</div>'
			  	+'</form>';

				jQuery("#modal-crud-renstra").find('.modal-title').html('Edit Indikator Kegiatan');
				jQuery("#modal-crud-renstra").find('.modal-body').html(html);
				jQuery("#modal-crud-renstra").find('.modal-footer').html(''
					+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
					+'</button><button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
						+'data-action="update_indikator_kegiatan_renstra" '
						+'data-view="indikatorKegiatanRenstra"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
					+'</button>');	
				jQuery("#modal-crud-renstra").find('.modal-dialog').css('maxWidth','950px');
				jQuery("#modal-crud-renstra").find('.modal-dialog').css('width','100%');
				jQuery("#modal-crud-renstra").modal('show');
          	}
		})	
	});

	jQuery(document).on('click', '.btn-delete-indikator-kegiatan', function(){

		if(confirm('Data akan dihapus, lanjut?')){

			jQuery('#wrap-loading').show();
			
			let id = jQuery(this).data('id');	
			let kode_kegiatan = jQuery(this).data('kodekegiatan');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_indikator_kegiatan_renstra',
		      		'api_key': '<?php echo $api_key; ?>',
					'id': id,
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						indikatorKegiatanRenstra({
							'id_unik': kode_kegiatan
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '.btn-detail-kegiatan', function(){
		subKegiatanRenstra({
			'kode_giat':jQuery(this).data('kodegiat'),
			'kode_kegiatan':jQuery(this).data('kodekegiatan'),
			'id_kegiatan':jQuery(this).data('idkegiatan'),
		});
	});

	jQuery(document).on('click', '.btn-tambah-sub-kegiatan', function(){

		let kode_giat = jQuery(this).data('kodegiat');
		let kode_kegiatan = jQuery(this).data('kodekegiatan');

		let html = '<form id="form-renstra">'
						+'<input type="hidden" name="kode_giat" value="'+kode_giat+'"/>'
						+'<input type="hidden" name="kode_kegiatan" value="'+kode_kegiatan+'"/>'
						+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>"/>'
						+'<input type="hidden" name="sub_kegiatan_teks" id="sub_kegiatan_teks"/>'
						+'<div class="form-group">'
							+'<div class="row">'
								+'<div class="col-md-12">'
									+'<label for="sub_kegiatan_teks">Sub Kegiatan</label>'
									+'<select class="form-control" id="id_sub_kegiatan" name="id_sub_kegiatan" onchange="setTeks(this, \'sub_kegiatan_teks\', \'id_sub_kegiatan\')"></select>'
								+'</div>'
							+'</div>'
						+'</div>'
						+'<div class="form-group">'
							+'<div class="row">'
								+'<div class="col-md-6">'
									+'<div class="card">'
										+'<div class="card-header">Usulan</div>'
										+'<div class="card-body">'
											<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+'<div class="form-group">'
												+'<label for="pagu_<?php echo $i; ?>_usulan">Pagu Tahun ke-<?php echo $i; ?></label>'
								  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>_usulan"/>'
											+'</div>'
											<?php }; ?>
											+'<div class="form-group">'
												+'<label>Catatan Usulan</label>'
												+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>></textarea>'
											+'</div>'
										+'</div>'
									+'</div>'
								+'</div>'
								+'<div class="col-md-6">'
									+'<div class="card">'
										+'<div class="card-header">Penetapan</div>'
										+'<div class="card-body">'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+'<div class="form-group">'
												+'<label for="pagu_<?php echo $i; ?>">Pagu Tahun ke-<?php echo $i; ?></label>'
								  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
											+'</div>'
										<?php }; ?>
											+'<div class="form-group">'
												+'<label>Catatan Penetapan</label>'
												+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
											+'</div>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
							<?php if($is_admin): ?>
							+'<div class="row">'
								+'<div class="col-md-12 text-center">'
									+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
										+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
									+'</button>'
								+'</div>'
							+'</div>'
							<?php endif; ?>
						+'</div>'
					+'</form>';

			    jQuery("#modal-crud-renstra").find('.modal-title').html('Tambah Sub Kegiatan');
				jQuery("#modal-crud-renstra").find('.modal-body').html(html);
				jQuery("#modal-crud-renstra").find('.modal-footer').html(''
					+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
						+'data-action="submit_sub_kegiatan_renstra" '
						+'data-view="subKegiatanRenstra"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
					+'</button>');
				jQuery("#modal-crud-renstra").find('.modal-dialog').css('maxWidth','950px');
				jQuery("#modal-crud-renstra").find('.modal-dialog').css('width','100%');
				jQuery("#modal-crud-renstra").modal('show');

				get_list_sub_kegiatan({
					'kode_giat':kode_giat,
					'id_unit':'<?php echo $unit[0]['id_unit'];?>',
					'kode_unit':'<?php echo $unit[0]['kodeunit'];?>',
					'tahun_anggaran':'<?php echo $tahun_anggaran; ?>',
				}, 'id_sub_kegiatan').then(function(){
					jQuery("#id_sub_kegiatan").select2({width:'100%'});
				});
	});

	jQuery(document).on('click', '.btn-edit-sub-kegiatan', function(){

		jQuery('#wrap-loading').show();

		let id_sub_kegiatan = jQuery(this).data('id');
		let kode_giat = jQuery(this).data('kodegiat');
		let kode_kegiatan = jQuery(this).data('kodekegiatan');

		jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'edit_sub_kegiatan_renstra',
		      		'api_key': '<?php echo $api_key; ?>',
					'id_sub_kegiatan': id_sub_kegiatan,
				},
				success:function(response){

					jQuery('#wrap-loading').hide();
		  			let html = '<form id="form-renstra">'
								+'<input type="hidden" name="id" value="'+response.sub_kegiatan.id+'"/>'
								+'<input type="hidden" name="kode_sub_kegiatan" value="'+response.sub_kegiatan.id_unik+'"/>'
								+'<input type="hidden" name="id_sub_giat" value="'+response.sub_kegiatan.id_sub_giat+'"/>'
								+'<input type="hidden" name="kode_sub_giat" value="'+response.sub_kegiatan.kode_sub_giat+'"/>'
								+'<input type="hidden" name="kode_kegiatan" value="'+kode_kegiatan+'"/>'
								+'<input type="hidden" name="kode_giat" value="'+kode_giat+'"/>'
								+'<input type="hidden" name="id_giat" value="'+response.sub_kegiatan.id_giat+'"/>'
								+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>"/>'
								+'<input type="hidden" name="sub_kegiatan_teks" id="sub_kegiatan_teks"/>'
								+'<div class="form-group">'
									+'<div class="row">'
										+'<div class="col-md-12">'
											+'<label for="sub_kegiatan_teks">Sub Kegiatan</label>'
											+'<select class="form-control" id="id_sub_kegiatan" name="id_sub_kegiatan" onchange="setTeks(this, \'sub_kegiatan_teks\', \'id_sub_kegiatan\')"></select>'
										+'</div>'
									+'</div>'
								+'</div>'
								+'<div class="form-group">'
									+'<div class="row">'
										+'<div class="col-md-6">'
											+'<div class="card">'
												+'<div class="card-header">Usulan</div>'
												+'<div class="card-body">'
													<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
													+'<div class="form-group">'
														+'<label for="pagu_<?php echo $i; ?>_usulan">Pagu Tahun ke-<?php echo $i; ?></label>'
										  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>_usulan" value="'+response.sub_kegiatan.pagu_<?php echo $i; ?>_usulan+'"/>'
													+'</div>'
													<?php }; ?>
													+'<div class="form-group">'
														+'<label>Catatan Usulan</label>'
														+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>>'+response.sub_kegiatan.catatan_usulan+'</textarea>'
													+'</div>'
												+'</div>'
											+'</div>'
										+'</div>'
										+'<div class="col-md-6">'
											+'<div class="card">'
												+'<div class="card-header">Penetapan</div>'
												+'<div class="card-body">'
													<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
													+'<div class="form-group">'
														+'<label for="pagu_<?php echo $i; ?>">Pagu Tahun ke-<?php echo $i; ?></label>'
										  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>" <?php echo $disabled; ?> value="'+response.sub_kegiatan.pagu_<?php echo $i; ?>+'" />'
													+'</div>'
													<?php }; ?>
													+'<div class="form-group">'
														+'<label>Catatan Penetapan</label>'
														+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.sub_kegiatan.catatan+'</textarea>'
													+'</div>'
												+'</div>'
											+'</div>'
										+'</div>'
									+'</div>'
								<?php if($is_admin): ?>
									+'<div class="row">'
										+'<div class="col-md-12 text-center">'
											+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
												+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
											+'</button>'
										+'</div>'
									+'</div>'
								<?php endif; ?>
								+'</div>'
							+'</form>';

				    jQuery("#modal-crud-renstra").find('.modal-title').html('Edit Sub Kegiatan');
					jQuery("#modal-crud-renstra").find('.modal-body').html(html);
					jQuery("#modal-crud-renstra").find('.modal-footer').html(''
						+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
							+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
						+'</button>'
						+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
							+'data-action="update_sub_kegiatan_renstra" '
							+'data-view="subKegiatanRenstra"'
						+'>'
							+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
						+'</button>');
					jQuery("#modal-crud-renstra").find('.modal-dialog').css('maxWidth','950px');
					jQuery("#modal-crud-renstra").find('.modal-dialog').css('width','100%');
					jQuery("#modal-crud-renstra").modal('show');

					get_list_sub_kegiatan({
						'kode_giat':kode_giat,
						'id_unit':'<?php echo $unit[0]['id_unit'];?>',
						'kode_unit':'<?php echo $unit[0]['kodeunit'];?>',
						'tahun_anggaran':'<?php echo $tahun_anggaran; ?>',
					}, 'id_sub_kegiatan').then(function(){
						jQuery("#id_sub_kegiatan").val(response.sub_kegiatan.id_sub_giat);
						jQuery("#id_sub_kegiatan").select2({width:'100%'});
					});

				}
		});	
	});

	jQuery(document).on('click', '.btn-hapus-sub-kegiatan', function(){

		if(confirm('Data akan dihapus, lanjut?')){

			jQuery('#wrap-loading').show();
			
			let id = jQuery(this).data('id');	
			let id_unik = jQuery(this).data('kodesubkegiatan');
			let id_kegiatan = jQuery(this).data('idkegiatan');
			let kode_kegiatan = jQuery(this).data('kodekegiatan');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_sub_kegiatan_renstra',
		      		'api_key': '<?php echo $api_key; ?>',
					'id': id,
					'id_unik': id_unik,
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						subKegiatanRenstra({
							'id_kegiatan': id_kegiatan,
							'kode_kegiatan': kode_kegiatan
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '.btn-kelola-indikator-sub-kegiatan', function(){
		jQuery("#modal-indikator-renstra").find('.modal-body').html('');
		indikatorSubKegiatanRenstra({'id_unik':jQuery(this).data('kodesubkegiatan'), 'id_sub_giat':jQuery(this).data('idsubgiat')});
	});

	jQuery(document).on('click', '.btn-add-indikator-sub-kegiatan', function(){
		
		let id_unik = jQuery(this).data('kodesubkegiatan');
		let id_sub_giat = jQuery(this).data('idsubgiat');

        let html = ''
			+'<form id="form-renstra">'
				+'<input type="hidden" name="id_unik" value="'+id_unik+'">'
				+'<input type="hidden" name="id_sub_giat" value="'+id_sub_giat+'">'
				+'<div class="form-group">'
					+'<div class="row">'
						+'<div class="col-md-6">'
							+'<div class="card">'
								+'<div class="card-header">Usulan</div>'
								+'<div class="card-body">'
									+'<div class="form-group">'
										+'<label for="indikator_teks_usulan">Indikator</label></br>'
						  				+'<select class="form-class opt_indikator" onchange="setSatuan(this, \'satuan_usulan\')" name="id_indikator_usulan" style="width:100%"></select>'
									+'</div>'
									+'<div class="form-group">'
										+'<label for="satuan_usulan">Satuan</label>'
						  				+'<input type="text" class="form-control" name="satuan_usulan"/>'
									+'</div>'
									+'<div class="form-group">'
										+'<label for="target_awal_usulan">Target awal</label>'
						  				+'<input type="number" class="form-control" name="target_awal_usulan"/>'
									+'</div>'
									<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
									+'<div class="form-group">'
										+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
						  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>_usulan"/>'
									+'</div>'
									<?php }; ?>
									+'<div class="form-group">'
										+'<label for="target_akhir_usulan">Target akhir</label>'
						  				+'<input type="number" class="form-control" name="target_akhir_usulan"/>'
									+'</div>'
									+'<div class="form-group">'
										+'<label for="catatan_usulan">Catatan</label>'
						  				+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>></textarea>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
						+'<div class="col-md-6">'
							+'<div class="card">'
								+'<div class="card-header">Penetapan</div>'
								+'<div class="card-body">'
									+'<div class="form-group">'
										+'<label for="indikator_teks">Indikator</label></br>'
						  				+'<select class="form-class opt_indikator" onchange="setSatuan(this, \'satuan\')" name="id_indikator" <?php echo $disabled; ?> style="width:100%"></select>'
									+'</div>'
									+'<div class="form-group">'
										+'<label for="satuan">Satuan</label>'
						  				+'<input type="text" class="form-control" name="satuan" <?php echo $disabled; ?> />'
									+'</div>'
									+'<div class="form-group">'
										+'<label for="target_awal">Target awal</label>'
						  				+'<input type="number" class="form-control" name="target_awal" <?php echo $disabled; ?>/>'
									+'</div>'
									<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
									+'<div class="form-group">'
										+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
						  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
									+'</div>'
									<?php }; ?>
									+'<div class="form-group">'
										+'<label for="target_akhir">Target akhir</label>'
						  				+'<input type="number" class="form-control" name="target_akhir" <?php echo $disabled; ?>/>'
									+'</div>'
									+'<div class="form-group">'
										+'<label for="catatan">Catatan</label>'
						  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</div>'
				<?php if($is_admin): ?>
					+'<div class="row">'
						+'<div class="col-md-12 text-center">'
							+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
								+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
							+'</button>'
						+'</div>'
					+'</div>'
				<?php endif; ?>
				+'</div>'
			+'</form>';
          		
        	jQuery("#modal-crud-renstra").find('.modal-title').html('Tambah Indikator');
			jQuery("#modal-crud-renstra").find('.modal-body').html(html);
			jQuery("#modal-crud-renstra").find('.modal-footer').html(''
					+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
						+'data-action="submit_indikator_sub_kegiatan_renstra" '
						+'data-view="indikatorSubKegiatanRenstra"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
					+'</button>');
			jQuery("#modal-crud-renstra").find('.modal-dialog').css('maxWidth','950px');
			jQuery("#modal-crud-renstra").find('.modal-dialog').css('width','100%');
			jQuery("#modal-crud-renstra").modal('show');

			get_master_indikator_subgiat({
		       'id_sub_giat':id_sub_giat,
		       'tahun_anggaran':'<?php echo $tahun_anggaran; ?>',
		    }, 'opt_indikator').then(function(){
	            jQuery(".opt_indikator").select2({width: '100%'});
		    });
			
	});

	jQuery(document).on('click', '.btn-edit-indikator-sub-kegiatan', function(){
		jQuery("#wrap-loading").show();
		let id = jQuery(this).data('id');
		let id_sub_giat = jQuery(this).data('idsubgiat');
		let kode_sub_kegiatan = jQuery(this).data('kodesubkegiatan');
		jQuery.ajax({
			method:'post',
			url:ajax.url,
			dataType:'json',
			data:{
				'action':'edit_indikator_sub_kegiatan_renstra',
				'api_key':'<?php echo $api_key; ?>',
				'id':id,
				'kode_sub_kegiatan':kode_sub_kegiatan,
			},
			success:function(response){

				jQuery("#wrap-loading").hide();

				let html = ''
					+'<form id="form-renstra">'
						+'<input type="hidden" name="id" value="'+id+'">'
						+'<input type="hidden" name="id_unik" value="'+kode_sub_kegiatan+'">'
						+'<input type="hidden" name="id_sub_giat" value="'+id_sub_giat+'">'
						+'<div class="form-group">'
							+'<div class="row">'
								+'<div class="col-md-6">'
									+'<div class="card">'
										+'<div class="card-header">Usulan</div>'
										+'<div class="card-body">'
											+'<div class="form-group">'
												+'<label for="indikator_teks_usulan">Indikator</label></br>'
								  				+'<select class="form-class opt_indikator opt_indikator_usulan" onchange="setSatuan(this, \'satuan_usulan\')" name="id_indikator_usulan" style="width:100%"></select>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="satuan_usulan">Satuan</label>'
								  				+'<input type="text" class="form-control" name="satuan_usulan" value="'+response.data.satuan+'"/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="target_awal_usulan">Target awal</label>'
								  				+'<input type="number" class="form-control" name="target_awal_usulan" value="'+response.data.target_awal_usulan+'"/>'
											+'</div>'
											<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+'<div class="form-group">'
												+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
								  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>_usulan" value="'+response.data.target_<?php echo $i; ?>_usulan+'"/>'
											+'</div>'
											<?php }; ?>
											+'<div class="form-group">'
												+'<label for="target_akhir_usulan">Target akhir</label>'
								  				+'<input type="number" class="form-control" name="target_akhir_usulan" value="'+response.data.target_akhir_usulan+'"/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="catatan_usulan">Catatan</label>'
								  				+'<textarea class="form-control" name="catatan_usulan" <?php echo $disabled_admin; ?>>'+response.data.catatan_usulan+'</textarea>'
											+'</div>'
										+'</div>'
									+'</div>'
								+'</div>'
								+'<div class="col-md-6">'
									+'<div class="card">'
										+'<div class="card-header">Penetapan</div>'
										+'<div class="card-body">'
											+'<div class="form-group">'
												+'<label for="indikator_teks">Indikator</label></br>'
								  				+'<select class="form-class opt_indikator opt_indikator_penetapan" onchange="setSatuan(this, \'satuan\')" name="id_indikator" <?php echo $disabled; ?> style="width:100%"></select>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="satuan">Satuan</label>'
								  				+'<input type="text" class="form-control" name="satuan" <?php echo $disabled; ?> value="'+response.data.satuan+'"/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="target_awal">Target awal</label>'
								  				+'<input type="number" class="form-control" name="target_awal" <?php echo $disabled; ?> value="'+response.data.target_awal+'"/>'
											+'</div>'
											<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+'<div class="form-group">'
												+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
								  				+'<input type="number" class="form-control" name="target_<?php echo $i; ?>" value="'+response.data.target_<?php echo $i; ?>+'" <?php echo $disabled; ?>/>'
											+'</div>'
											<?php }; ?>
											+'<div class="form-group">'
												+'<label for="target_akhir">Target akhir</label>'
								  				+'<input type="number" class="form-control" name="target_akhir" <?php echo $disabled; ?> value="'+response.data.target_akhir+'"/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="catatan">Catatan</label>'
								  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.data.catatan+'</textarea>'
											+'</div>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						<?php if($is_admin): ?>
							+'<div class="row">'
								+'<div class="col-md-12 text-center">'
									+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
										+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
									+'</button>'
								+'</div>'
							+'</div>'
						<?php endif; ?>
						+'</div>'
					+'</form>';
		          		
		        	jQuery("#modal-crud-renstra").find('.modal-title').html('Ubah Indikator');
					jQuery("#modal-crud-renstra").find('.modal-body').html(html);
					jQuery("#modal-crud-renstra").find('.modal-footer').html(''
							+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
								+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
							+'</button>'
							+'<button type="button" class="btn btn-success" id="btn-simpan-data-renstra-lokal" '
								+'data-action="update_indikator_sub_kegiatan_renstra" '
								+'data-view="indikatorSubKegiatanRenstra"'
							+'>'
								+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Simpan'
							+'</button>');
					jQuery("#modal-crud-renstra").find('.modal-dialog').css('maxWidth','950px');
					jQuery("#modal-crud-renstra").find('.modal-dialog').css('width','100%');
					jQuery("#modal-crud-renstra").modal('show');

					get_master_indikator_subgiat({
				       'id_sub_giat':id_sub_giat,
				       'tahun_anggaran':'<?php echo $tahun_anggaran; ?>',
					}, 'opt_indikator').then(function(){
						jQuery(".opt_indikator_usulan").val(response.data.id_indikator_usulan);
						if(response.data.id_indikator!='' && response.data.id_indikator != 'undefined'){
							jQuery(".opt_indikator_penetapan").val(response.data.id_indikator);
	            			jQuery(".opt_indikator").select2({width: '100%'});
						}
					});
			}
		})
	});

	jQuery(document).on('click', '.btn-delete-indikator-sub-kegiatan', function(){
		if(confirm('Data akan dihapus, lanjut?')){

			jQuery('#wrap-loading').show();
			
			let id = jQuery(this).data('id');
			let id_sub_giat = jQuery(this).data('idsubgiat');
			let kode_sub_kegiatan = jQuery(this).data('kodesubkegiatan');

			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'delete_indikator_sub_kegiatan_renstra',
		      		'api_key': '<?php echo $api_key; ?>',
					'id': id,
				},
				success:function(response){

					alert(response.message);
					if(response.status){
						indikatorSubKegiatanRenstra({
							'id_sub_giat': id_sub_giat,
							'id_unik': kode_sub_kegiatan
						});
					}
					jQuery('#wrap-loading').hide();

				}
			})
		}
	});

	jQuery(document).on('click', '#btn-simpan-data-renstra-lokal', function(){
		
		jQuery('#wrap-loading').show();
		let renstraModal = jQuery("#modal-crud-renstra");
		let action = jQuery(this).data('action');
		let view = jQuery(this).data('view');
		let form = getFormData(jQuery("#form-renstra"));
		
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
					renstraModal.modal('hide');
				}
			}
		})
	});

	jQuery(document).on('change', '#urusan-teks', function(){
		get_bidang(jQuery(this).val());
		get_program();
	});

	jQuery(document).on('change', '#bidang-teks', function(){
		var val = jQuery(this).val();
		var val_urusan = jQuery('#urusan-teks').val();
		for(var nm_urusan in all_program){
			for(var nm_bidang in all_program[nm_urusan]){
				for(var nm_program in all_program[nm_urusan][nm_bidang]){
					if(val && nm_bidang == val){
						if(val_urusan.trim() != nm_urusan){
							console.log(val_urusan.trim(), nm_urusan);
							jQuery('#urusan-teks').val(nm_urusan).trigger('change');
							jQuery('#bidang-teks').val(val).trigger('change');
						}
					}
				}
			}
		}
		get_program(val);
	});

	jQuery(document).on('change', '#program-teks', function(){
		var val = jQuery(this).val();
		var val_urusan = jQuery('#urusan-teks').val();
		var val_bidang = jQuery('#bidang-teks').val();
		for(var nm_urusan in all_program){
			for(var nm_bidang in all_program[nm_urusan]){
				for(var nm_program in all_program[nm_urusan][nm_bidang]){
					if(val && val == all_program[nm_urusan][nm_bidang][nm_program].id_program){
						if(val_urusan.trim() != nm_urusan){
							console.log(val_urusan.trim(), nm_urusan);
							jQuery('#urusan-teks').val(nm_urusan).trigger('change');
						}
						if(val_bidang.trim() != nm_bidang){
							console.log(val_bidang.trim(), nm_bidang);
							jQuery('#bidang-teks').val(nm_bidang).trigger('change');
						}
					}
				}
			}
		}
		get_program(false, val);
	});

	function laporan(type){
		jQuery('#wrap-loading').show();

		let action='';
		let name='';
		let title='';

		switch(type){
			case 'tc27':
				action='view_laporan_tc27';
				name='Laporan Renstra TC 27';
				title='Laporan Renstra TC 27';
			break;

			default:
				alert('Jenis laporan belum dipilih!');
				break;
		}

		jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': action,
		          	'api_key': '<?php echo $api_key; ?>',
		          	'id_unit': '<?php echo $input['id_skpd']; ?>',
		          	'id_jadwal_lokal':'<?php echo $jadwal_lokal[0]['id_jadwal_lokal']; ?>',
		          	'tahun_anggaran':'<?php echo $tahun_anggaran; ?>'
				},
				success:function(response){
					
					jQuery('#wrap-loading').hide();

					jQuery("#modal-crud-renstra").find('.modal-dialog').css('maxWidth','1450px');
					jQuery("#modal-crud-renstra").find('.modal-dialog').css('width','100%');
					jQuery("#modal-crud-renstra").find('.modal-title').html(title);
					jQuery("#modal-crud-renstra").find('.modal-body').html(response.html);
					jQuery("#modal-crud-renstra").find('.modal-body').css('overflow-x', 'auto');
					jQuery("#modal-crud-renstra").find('.modal-footer').html(''
						+'<button type="button" class="btn btn-warning" data-dismiss="modal">'
							+'<i class="dashicons dashicons-no" style="margin-top: 2px;"></i> Tutup'
						+'</button>'
						+'<button type="button" class="btn btn-success" onclick=\'exportExcel("'+name+'")\'>'
							+'<i class="dashicons dashicons-yes" style="margin-top: 2px;"></i> Export Excel'
						+'</button>');
					jQuery("#modal-crud-renstra").modal('show');
				}
			});
	}

	function exportExcel(name){
		tableHtmlToExcel('preview', name);
	}

	function pilihTujuanRpjm(that, cb){
		jQuery("#wrap-loading").show();
		let kode_tujuan_rpjm = jQuery(that).val();
		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action':'get_sasaran_parent',
				'api_key': '<?php echo $api_key; ?>',
				'kode_tujuan_rpjm': kode_tujuan_rpjm,
		        'id_unit': '<?php echo $input['id_skpd']; ?>',
		        'relasi_perencanaan': '<?php echo $relasi_perencanaan; ?>',
		        'id_tipe_relasi': '<?php echo $id_tipe_relasi; ?>',
			},
			success:function(response){
				jQuery("#wrap-loading").hide();
				let option='<option>Pilih Sasaran</option>';
				response.data.map(function(value, index){
					option+='<option value="'+value.id_unik+'|'+<?php echo $relasi_perencanaan; ?>+'">'+value.sasaran_teks+'</option>';
				})
				jQuery("#sasaran-rpjm").html(option);
				if(typeof cb == 'function'){
					cb();
				}
			}
		});
	}

	function copySasaran(){
		jQuery("#tujuan_teks").val(jQuery("#sasaran-rpjm").find(':selected').text());
	}

	function setTeks(that, inputId, optionId){
		if(that.value !=""){
			jQuery("#"+inputId).val(jQuery("#"+optionId).find(':selected').text());
		}
	}

	function tujuanRenstra(){
		
		jQuery('#wrap-loading').show();
		jQuery('#nav-tujuan').html('');
		jQuery('#nav-sasaran').html('');
		jQuery('#nav-program').html('');
		jQuery('#nav-kegiatan').html('');

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_tujuan_renstra", // wpsipd-public-base-2
          		"api_key": "<?php echo $api_key; ?>",
          		"id_skpd": "<?php echo $input['id_skpd']; ?>",
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
          		jQuery('#wrap-loading').hide();

          		let tujuan = ''
	          		+'<div style="margin-top:10px"><button type="button" class="btn btn-primary mb-2 btn-tambah-tujuan"><i class="dashicons dashicons-plus" style="margin-top: 2px;"></i> Tambah Tujuan</button>'
	          		+'</div>'
          			+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th id="nama-skpd">'+res.skpd[0].kode_skpd+' '+res.skpd[0].nama_skpd+'</th>'
	          				+'</tr>'
	          			+'</thead>'
          			+'</table>'
	          		+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width:45px">No</th>'
	          					+'<th class="text-center" style="width:20%">Bidang Urusan</th>'
	          					+'<th class="text-center">Tujuan</th>'
      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
      							+'<th class="text-center" style="width:150px">Pagu Tahun <?php echo $i; ?></th>'
      						<?php } ?>
	          					+'<th class="text-center">Catatan</th>'
	          					+'<th class="text-center" style="width:185px">Aksi</th>'
	          				+'<tr>'
	          			+'</thead>'
	          			+'<tbody>';
			          		res.data.map(function(value, index){
			          			for(var i in value){
			          				if(
			          					value[i] == 'null'
			          					|| value[i] == null
			          				){
			          					value[i] = '';
			          				}
			          			}
			          			tujuan += ''
			          			+'<tr kodetujuan="'+value.id_unik+'" kode_bidang_urusan="'+value.kode_bidang_urusan+'">'
				          			+'<td class="text-center" rowspan="2">'+(index+1)+'</td>'
				          			+'<td rowspan="2">'+value.nama_bidang_urusan+'</td>'
				          			+'<td rowspan="2">'+value.tujuan_teks+'</td>'
	      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	      							+'<td class="text-right">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>)+'</td>'
	      						<?php } ?>
				          			+'<td><b>Penetapan</b><br>'+value.catatan+'</td>'
				          			+'<td class="text-center" rowspan="2">'
			          					+'<a href="javascript:void(0)" data-idtujuan="'+value.id+'" data-idunik="'+value.id_unik+'" class="btn btn-warning btn-kelola-indikator-tujuan" title="Lihat Indikator Tujuan"><i class="dashicons dashicons-menu-alt" style="margin-top: 2px;"></i></a>&nbsp;'
			          					+'<a href="javascript:void(0)" data-kodetujuan="'+value.id_unik+'" class="btn btn-primary btn-detail-tujuan" title="Lihat Sasaran"><i class="dashicons dashicons-search"></i></a>&nbsp;'
			          					+'<a href="javascript:void(0)" data-id="'+value.id+'" class="btn btn-success btn-edit-tujuan" title="Edit Tujuan"><i class="dashicons dashicons-edit"></i></a>&nbsp;'
			          					+'<a href="javascript:void(0)" data-id="'+value.id+'" data-idunik="'+value.id_unik+'" class="btn btn-danger btn-hapus-tujuan" title="Hapus Tujuan"><i class="dashicons dashicons-trash"></i></a>'
				          			+'</td>'
				          		+'</tr>'
			          			+'<tr>'
	      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	      							+'<td class="text-right">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>_usulan)+'</td>'
	      						<?php } ?>
				          			+'<td><b>Usulan</b> '+value.catatan_usulan+'</td>'
				          		+'</tr>';
			          		})
          			tujuan+='<tbody>'
          			+'</table>';

          		jQuery("#nav-tujuan").html(tujuan);
				jQuery('.nav-tabs a[href="#nav-tujuan"]').tab('show');
				jQuery('#modal-monev').modal('show');
        	}
		})
	}

	function indikatorTujuanRenstra(params){
		
		jQuery('#wrap-loading').show();

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_indikator_tujuan_renstra",
          		"api_key": "<?php echo $api_key; ?>",
				'id_unik': params.id_unik,
				'type':1
          	},
          	dataType: "json",
          	success: function(response){
          		jQuery('#wrap-loading').hide();

          		let html=""
					+'<div style="margin-top:10px">'
						+"<button type=\"button\" class=\"btn btn-primary mb-2 btn-add-indikator-tujuan\" data-kodetujuan=\""+params.id_unik+"\">"
								+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 2px;\"></i> Tambah Indikator"
						+"</button>"
					+'</div>'
          			+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('#nama-skpd').text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
	          					+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+params.id_unik+'"]').find('td').eq(1).text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Tujuan</th>'
	          					+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+params.id_unik+'"]').find('td').eq(2).text()+'</th>'
	          				+'</tr>'
	          			+'</thead>'
          			+'</table>'
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center'>No</th>"
								+"<th class='text-center'>Indikator</th>"
								+"<th class='text-center'>Satuan</th>"
								+"<th class='text-center'>Awal</th>"
								<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<th class='text-center'>Tahun <?php echo $i; ?></th>"
								<?php }; ?>
								+"<th class='text-center'>Akhir</th>"
								+"<th class='text-center'>Catatan</th>"
								+"<th class='text-center'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_tujuan'>";
						response.data.map(function(value, index){
		          			for(var i in value){
		          				if(
		          					value[i] == 'null'
		          					|| value[i] == null
		          				){
		          					value[i] = '';
		          				}
		          			}
		          			html +=''
		          				+"<tr>"
					          		+"<td class='text-center' rowspan='2'>"+(index+1)+"</td>"
					          		+"<td>"+value.indikator_teks+"</td>"
					          		+"<td>"+value.satuan+"</td>"
					          		+"<td class='text-center'>"+value.target_awal+"</td>"
					          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
									+"<td class='text-center'>"+value.target_<?php echo $i; ?>+"</td>"
									<?php }; ?>
					          		+"<td class='text-center'>"+value.target_akhir+"</td>"
					          		+"<td><b>Penetapan</b><br>"+value.catatan+"</td>"
					          		+"<td class='text-center' rowspan='2'>"
					          			+"<a href='#' class='btn btn-success btn-edit-indikator-tujuan' data-id='"+value.id+"' data-idunik='"+value.id_unik+"' title='Edit Indikator'><i class='dashicons dashicons-edit' style='margin-top: 2px;'></i></a>&nbsp"
										+"<a href='#' class='btn btn-danger btn-delete-indikator-tujuan' data-id='"+value.id+"' data-idunik='"+value.id_unik+"' title='Hapus Indikator'><i class='dashicons dashicons-trash' style='margin-top: 2px;'></i></a>&nbsp;"
					          		+"</td>"
					          	+"</tr>"
		          				+"<tr>"
					          		+"<td>"+value.indikator_teks_usulan+"</td>"
					          		+"<td>"+value.satuan_usulan+"</td>"
					          		+"<td class='text-center'>"+value.target_awal_usulan+"</td>"
					          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
									+"<td class='text-center'>"+value.target_<?php echo $i; ?>_usulan+"</td>"
									<?php }; ?>
					          		+"<td class='text-center'>"+value.target_akhir_usulan+"</td>"
					          		+"<td><b>Usulan</b><br>"+value.catatan_usulan+"</td>"
					          	+"</tr>";
		          		});
		          	html+='</tbody></table>';

		          	jQuery("#modal-indikator-renstra").find('.modal-title').html('Indikator Tujuan');
		          	jQuery("#modal-indikator-renstra").find('.modal-body').html(html)
					jQuery("#modal-indikator-renstra").find('.modal-dialog').css('maxWidth','1250px');
					jQuery("#modal-indikator-renstra").find('.modal-dialog').css('width','100%');
					jQuery("#modal-indikator-renstra").modal('show');
          	}  	
		})
	}

	function sasaranRenstra(params){

		jQuery('#wrap-loading').show();
		
		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action': 'get_sasaran_renstra',
	      		'api_key': '<?php echo $api_key; ?>',
				'kode_tujuan': params.kode_tujuan,
				'type':1
			},
			success:function(response){

          		jQuery('#wrap-loading').hide();
          		
          		let sasaran = ''
      				+'<div style="margin-top:10px"><button type="button" class="btn btn-primary mb-2 btn-tambah-sasaran" data-kodetujuan="'+params.kode_tujuan+'"><i class="dashicons dashicons-plus" style="margin-top: 2px;"></i> Tambah Sasaran</button></div>'
      				+'<table class="table">'
      					+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('#nama-skpd').text()+'</th>'
	          				+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+params.kode_tujuan+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+params.kode_tujuan+'"]').find('td').eq(2).text()+'</th>'
          					+'</tr>'
      					+'</thead>'
      				+'</table>'
      				
      				+'<table class="table">'
      					+'<thead>'
      						+'<tr>'
      							+'<th class="text-center" style="width:45px">No</th>'
      							+'<th class="text-center">Sasaran</th>'
      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
      							+'<th class="text-center" style="width:150px">Pagu Tahun <?php echo $i; ?></th>'
      						<?php } ?>
      							+'<th class="text-center" style="width:15%">Catatan</th>'
      							+'<th class="text-center" style="width:185px">Aksi</th>'
      						+'<tr>'
      					+'</thead>'
      					+'<tbody>';

  						response.data.map(function(value, index){
		          			for(var i in value){
		          				if(
		          					value[i] == 'null'
		          					|| value[i] == null
		          				){
		          					value[i] = '';
		          				}
		          			}
  							sasaran +=''
  								+'<tr kodesasaran="'+value.id_unik+'">'
          							+'<td class="text-center" rowspan="2">'+(index+1)+'</td>'
          							+'<td rowspan="2">'+value.sasaran_teks+'</td>'
	      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	      							+'<td class="text-right">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>)+'</td>'
	      						<?php } ?>
          							+'<td><b>Penetapan</b><br>'+value.catatan+'</td>'
          							+'<td class="text-center" rowspan="2">'
          								+'<a href="javascript:void(0)" data-idsasaran="'+value.id+'" data-kodesasaran="'+value.id_unik+'" class="btn btn-warning btn-kelola-indikator-sasaran" title="Lihat Indikator Sasaran"><i class="dashicons dashicons-menu-alt" style="margin-top: 2px;"></i></a>&nbsp;'
          								+'<a href="javascript:void(0)" data-kodesasaran="'+value.id_unik+'" class="btn btn-primary btn-detail-sasaran" title="Lihat Program"><i class="dashicons dashicons-search" style="margin-top: 2px;"></i></a>&nbsp;'
          								+'<a href="javascript:void(0)" data-idsasaran="'+value.id+'" class="btn btn-success btn-edit-sasaran" title="Edit Sasaran"><i class="dashicons dashicons-edit" style="margin-top: 2px;"></i></a>&nbsp;'
          								+'<a href="javascript:void(0)" data-idsasaran="'+value.id+'" data-kodesasaran="'+value.id_unik+'" data-kodetujuan="'+value.kode_tujuan+'" class="btn btn-danger btn-hapus-sasaran" title="Hapus Sasaran"><i class="dashicons dashicons-trash" style="margin-top: 2px;"></i></a>'
          							+'</td>'
          						+'</tr>'
  								+'<tr>'
	      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	      							+'<td class="text-right">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>_usulan)+'</td>'
	      						<?php } ?>
          							+'<td><b>Usulan</b> '+value.catatan_usulan+'</td>'
          						+'</tr>';
  						});
      					sasaran +='<tbody>'
      				+'</table>';

			    jQuery("#nav-sasaran").html(sasaran);
			 	jQuery('.nav-tabs a[href="#nav-sasaran"]').tab('show');
			}
		})
	}

	function indikatorSasaranRenstra(params){

		jQuery('#wrap-loading').show();

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_indikator_sasaran_renstra",
          		"api_key": "<?php echo $api_key; ?>",
				'id_unik': params.id_unik,
				"type": 1
          	},
          	dataType: "json",
          	success: function(response){
          		jQuery('#wrap-loading').hide();
          		
          		let html=""
					+'<div style="margin-top:10px">'
						+"<button type=\"button\" class=\"btn btn-primary mb-2 btn-add-indikator-sasaran\" data-kodesasaran=\""+params.id_unik+"\">"
							+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 2px;\"></i> Tambah Indikator"
						+"</button>"
					+'</div>'
          			+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('#nama-skpd').text()+'</th>'
	          				+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(2).text()+'</th>'
          					+'</tr>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Sasaran</th>'
	          					+'<th>'+jQuery('#nav-sasaran tr[kodesasaran="'+params.id_unik+'"]').find('td').eq(1).text()+'</th>'
	          				+'</tr>'
	          			+'</thead>'
          			+'</table>'
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center'>No</th>"
								+"<th class='text-center'>Indikator</th>"
								+"<th class='text-center'>Satuan</th>"
								+"<th class='text-center'>Awal</th>"
								<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<th class='text-center'>Tahun <?php echo $i; ?></th>"
								<?php }; ?>
								+"<th class='text-center'>Akhir</th>"
								+"<th class='text-center'>Catatan</th>"
								+"<th class='text-center'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_sasaran'>";
						response.data.map(function(value, index){
		          			for(var i in value){
		          				if(
		          					value[i] == 'null'
		          					|| value[i] == null
		          				){
		          					value[i] = '';
		          				}
		          			}
		          			html +=''
	          				+"<tr>"
				          		+"<td class='text-center' rowspan='2'>"+(index+1)+"</td>"
				          		+"<td>"+value.indikator_teks+"</td>"
				          		+"<td>"+value.satuan+"</td>"
				          		+"<td class='text-center'>"+value.target_awal+"</td>"
				          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<td class='text-center'>"+value.target_<?php echo $i; ?>+"</td>"
								<?php }; ?>
				          		+"<td class='text-center'>"+value.target_akhir+"</td>"
				          		+"<td><b>Penetapan</b><br>"+value.catatan+"</td>"
				          		+"<td class='text-center' rowspan='2'>"
				          			+"<a href='#' class='btn btn-success btn-edit-indikator-sasaran' data-id='"+value.id+"' data-idunik='"+value.id_unik+"' title='Edit Indikator Program'><i class='dashicons dashicons-edit' style='margin-top: 2px;'></i></a>&nbsp"
									+"<a href='#' class='btn btn-danger btn-delete-indikator-sasaran' data-id='"+value.id+"' data-idunik='"+value.id_unik+"' title='Hapus Indikator Program'><i class='dashicons dashicons-trash' style='margin-top: 2px;'></i></a>&nbsp;"
				          		+"</td>"
				          	+"</tr>"
	          				+"<tr>"
				          		+"<td>"+value.indikator_teks_usulan+"</td>"
				          		+"<td>"+value.satuan_usulan+"</td>"
				          		+"<td class='text-center'>"+value.target_awal_usulan+"</td>"
				          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<td class='text-center'>"+value.target_<?php echo $i; ?>_usulan+"</td>"
								<?php }; ?>
				          		+"<td class='text-center'>"+value.target_akhir_usulan+"</td>"
				          		+"<td><b>Usulan</b><br>"+value.catatan_usulan+"</td>"
				          	+"</tr>";
		          		});
		          	html+='</tbody></table>';

					jQuery("#modal-indikator-renstra").find('.modal-title').html('Indikator Sasaran');
				    jQuery("#modal-indikator-renstra").find('.modal-body').html(html);
					jQuery("#modal-indikator-renstra").find('.modal-dialog').css('maxWidth','1250px');
					jQuery("#modal-indikator-renstra").find('.modal-dialog').css('width','100%');
					jQuery("#modal-indikator-renstra").modal('show');
          	}
		})
	}

	function programRenstra(params){

		jQuery('#wrap-loading').show();

		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action': 'get_program_renstra',
	      		'api_key': '<?php echo $api_key; ?>',
				'kode_sasaran': params.kode_sasaran,
				'type':1
			},
			success:function(response){

          		jQuery('#wrap-loading').hide();
          		
          		let program = ''
      				+'<div style="margin-top:10px"><button type="button" class="btn btn-primary mb-2 btn-tambah-program" data-kodesasaran="'+params.kode_sasaran+'"><i class="dashicons dashicons-plus" style="margin-top: 2px;"></i> Tambah Program</button></div>'
      				+'<table class="table">'
      					+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('#nama-skpd').text()+'</th>'
	          				+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(2).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Sasaran</th>'
          						+'<th>'+jQuery('#nav-sasaran tr[kodesasaran="'+params.kode_sasaran+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
      					+'</thead>'
      				+'</table>'
      				
      				+'<table class="table">'
      					+'<thead>'
      						+'<tr>'
      							+'<th class="text-center" style="width:45px">No</th>'
      							+'<th class="text-center">Program</th>'
      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
      							+'<th class="text-center" style="width:12%">Pagu Tahun <?php echo $i; ?></th>'
      						<?php } ?>
      							+'<th class="text-center" style="width:15%">Catatan</th>'
      							+'<th class="text-center" style="width:185px">Aksi</th>'
      						+'<tr>'
      					+'</thead>'
      					+'<tbody>';
  						response.data.map(function(value, index){
		          			for(var i in value){
		          				if(
		          					value[i] == 'null'
		          					|| value[i] == null
		          				){
		          					value[i] = '';
		          				}
		          			}
		          			var peringatan = {};
		          			var peringatan_usulan = {};
  						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
  							if(value.pagu_akumulasi_<?php echo $i; ?> != value.pagu_akumulasi_<?php echo $i; ?>_program){
  								peringatan[<?php echo $i; ?>] = 'peringatan';
  							}
  							if(value.pagu_akumulasi_<?php echo $i; ?>_usulan != value.pagu_akumulasi_<?php echo $i; ?>_usulan_program){
  								peringatan_usulan[<?php echo $i; ?>] = 'peringatan';
  							}
  						<?php } ?>
  							program += ''
  								+'<tr kodeprogram="'+value.id_unik+'">'
          							+'<td class="text-center" rowspan="4">'+(index+1)+'</td>'
          							+'<td rowspan="4">'+value.nama_program+'</td>'
	      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	      							+'<td class="text-right '+peringatan[<?php echo $i; ?>]+'">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>)+'</td>'
	      						<?php } ?>
          							+'<td><b>Penetapan</b><br>'+value.catatan+'</td>'
          							+'<td class="text-center" rowspan="4">'
          								+'<a href="javascript:void(0)" data-kodeprogram="'+value.id_unik+'" class="btn btn-warning btn-kelola-indikator-program" title="Lihat Indikator Program"><i class="dashicons dashicons-menu-alt" style="margin-top: 2px;"></i></a>&nbsp;'	
          								+'<a href="javascript:void(0)" data-kodeprogram="'+value.id_unik+'" data-idprogram="'+value.id_program+'" class="btn btn-primary btn-detail-program" title="Lihat Kegiatan"><i class="dashicons dashicons-search" style="margin-top: 2px;"></i></a>&nbsp;'
          								+'<a href="javascript:void(0)" data-id="'+value.id+'" data-kodeprogram="'+value.id_unik+'" class="btn btn-success btn-edit-program" title="Edit Program"><i class="dashicons dashicons-edit" style="margin-top: 2px;"></i></a>&nbsp;'
          								+'<a href="javascript:void(0)" data-id="'+value.id+'" data-kodeprogram="'+value.id_unik+'" data-kodesasaran="'+value.kode_sasaran+'" class="btn btn-danger btn-hapus-program" title="Hapus Program"><i class="dashicons dashicons-trash" style="margin-top: 2px;"></i></a></td>'
          						+'</tr>'
  								+'<tr>'
	      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	      							+'<td class="text-right '+peringatan[<?php echo $i; ?>]+'">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>_program)+'</td>'
	      						<?php } ?>
          							+'<td>Akumulasi Penetapan Indikator Program</td>'
          						+'</tr>'
  								+'<tr>'
	      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	      							+'<td class="text-right '+peringatan_usulan[<?php echo $i; ?>]+'">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>_usulan)+'</td>'
	      						<?php } ?>
          							+'<td><b>Usulan</b> '+value.catatan_usulan+'</td>'
          						+'</tr>'
  								+'<tr>'
	      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	      							+'<td class="text-right '+peringatan_usulan[<?php echo $i; ?>]+'">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>_usulan_program)+'</td>'
	      						<?php } ?>
          							+'<td>Akumulasi Usulan Indikator Program</td>'
          						+'</tr>';
  						});
      					program += ''
      					+'<tbody>'
      				+'</table>';
			    jQuery("#nav-program").html(program);
			 	jQuery('.nav-tabs a[href="#nav-program"]').tab('show');
			}
		})
	}

	function indikatorProgramRenstra(params){

		jQuery('#wrap-loading').show();

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_indikator_program_renstra",
          		"api_key": "<?php echo $api_key; ?>",
				'kode_program': params.kode_program,
				"type": 1
          	},
          	dataType: "json",
          	success: function(response){

          		jQuery('#wrap-loading').hide();
      			for(var i in response.program){
      				if(
      					response.program[i] == 'null'
      					|| response.program[i] == null
      				){
      					response.program[i] = '';
      				}
      			}
      			var peringatan = {};
      			var peringatan_usulan = {};
			<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				peringatan[<?php echo $i; ?>] = '';
				if(response.program.pagu_akumulasi_<?php echo $i; ?> != response.program.pagu_akumulasi_<?php echo $i; ?>_program){
					peringatan[<?php echo $i; ?>] = 'peringatan';
				}
				peringatan_usulan[<?php echo $i; ?>] = '';
				if(response.program.pagu_akumulasi_<?php echo $i; ?>_usulan != response.program.pagu_akumulasi_<?php echo $i; ?>_usulan_program){
					peringatan_usulan[<?php echo $i; ?>] = 'peringatan';
				}
			<?php } ?>
          		
          		let html=""
					+'<div style="margin-top:10px">'
						+"<button type=\"button\" class=\"btn btn-primary mb-2 btn-add-indikator-program\" data-kodeprogram=\""+params.kode_program+"\">"
							+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 2px;\"></i> Tambah Indikator"
						+"</button>"
					+'</div>'
          			+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('#nama-skpd').text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(2).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Sasaran</th>'
          						+'<th>'+jQuery('#nav-sasaran tr[kodesasaran="'+jQuery("#nav-program .btn-tambah-program").data("kodesasaran")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Program</th>'
	          					+'<th>'+jQuery('#nav-program tr[kodeprogram="'+params.kode_program+'"]').find('td').eq(1).text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
	          					+'<th colspan=2>'
	          						+'<table>'
		          						+'<thead>'
				      						+'<tr>'
				      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				      							+'<th class="text-center">Pagu Tahun <?php echo $i; ?></th>'
				      						<?php } ?>
				      							+'<th class="text-center" style="width:15%">Catatan</th>'
				      						+'</tr>'
		          						+'</thead>'
		          						+'<tbody style="font-weight: normal;">'
			  								+'<tr>'
				      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				      							+'<td class="text-right '+peringatan[<?php echo $i; ?>]+'">'+formatRupiah(response.program.pagu_akumulasi_<?php echo $i; ?>)+'</td>'
				      						<?php } ?>
			          							+'<td><b>Penetapan</b><br>'+response.program.catatan+'</td>'
			          						+'</tr>'
			  								+'<tr>'
				      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				      							+'<td class="text-right '+peringatan[<?php echo $i; ?>]+'">'+formatRupiah(response.program.pagu_akumulasi_<?php echo $i; ?>_program)+'</td>'
				      						<?php } ?>
			          							+'<td>Akumulasi Penetapan Indikator Program</td>'
			          						+'</tr>'
			  								+'<tr>'
				      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				      							+'<td class="text-right '+peringatan_usulan[<?php echo $i; ?>]+'">'+formatRupiah(response.program.pagu_akumulasi_<?php echo $i; ?>_usulan)+'</td>'
				      						<?php } ?>
			          							+'<td><b>Usulan</b> '+response.program.catatan_usulan+'</td>'
			          						+'</tr>'
			  								+'<tr>'
				      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				      							+'<td class="text-right '+peringatan_usulan[<?php echo $i; ?>]+'">'+formatRupiah(response.program.pagu_akumulasi_<?php echo $i; ?>_usulan_program)+'</td>'
				      						<?php } ?>
			          							+'<td>Akumulasi Usulan Indikator Program</td>'
			          						+'</tr>'
		          						+'</tbody>'
	          						+'</table>'
	          					+'</th>'
	          				+'</tr>'
	          			+'</thead>'
          			+'</table>'
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center'>No</th>"
								+"<th class='text-center'>Indikator</th>"
								+"<th class='text-center'>Satuan</th>"
								+"<th class='text-center'>Awal</th>"
								<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<th class='text-center'>Target Tahun <?php echo $i; ?></th>"
								+"<th class='text-center'>Pagu Tahun <?php echo $i; ?></th>"
								<?php }; ?>
								+"<th class='text-center'>Akhir</th>"
								+"<th class='text-center'>Catatan</th>"
								+"<th class='text-center'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_program'>";
						response.data.map(function(value, index){
		          			for(var i in value){
		          				if(
		          					value[i] == 'null'
		          					|| value[i] == null
		          				){
		          					value[i] = '';
		          				}
		          			}
		          			html +=''
		          				+"<tr>"
					          		+"<td class='text-center' rowspan='2'>"+(index+1)+"</td>"
					          		+"<td>"+value.indikator+"</td>"
					          		+"<td>"+value.satuan+"</td>"
					          		+"<td class='text-center'>"+value.target_awal+"</td>"
					          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
									+"<td class='text-center'>"+value.target_<?php echo $i; ?>+"</td>"
									+"<td class='text-right'>"+formatRupiah(value.pagu_<?php echo $i; ?>)+"</td>"
									<?php }; ?>
					          		+"<td class='text-center'>"+value.target_akhir+"</td>"
					          		+"<td><b>Penetapan</b><br>"+value.catatan+"</td>"
					          		+"<td class='text-center' rowspan='2'>"
					          			+"<a href='#' class='btn btn-success btn-edit-indikator-program' data-kodeprogram='"+value.id_unik+"' data-id='"+value.id+"'><i class='dashicons dashicons-edit' style='margin-top: 2px;' title='Edit Indikator Program'></i></a>&nbsp"
										+"<a href='#' class='btn btn-danger btn-delete-indikator-program' data-kodeprogram='"+value.id_unik+"' data-id='"+value.id+"' title='Hapus Indikator Program'><i class='dashicons dashicons-trash' style='margin-top: 2px;'></i></a>&nbsp;"
					          		+"</td>"
					          	+"</tr>"
		          				+"<tr>"
					          		+"<td>"+value.indikator_usulan+"</td>"
					          		+"<td>"+value.satuan_usulan+"</td>"
					          		+"<td class='text-center'>"+value.target_awal_usulan+"</td>"
					          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
									+"<td class='text-center'>"+value.target_<?php echo $i; ?>_usulan+"</td>"
									+"<td class='text-right'>"+formatRupiah(value.pagu_<?php echo $i; ?>_usulan)+"</td>"
									<?php }; ?>
					          		+"<td class='text-center'>"+value.target_akhir_usulan+"</td>"
					          		+"<td><b>Usulan</b><br>"+value.catatan_usulan+"</td>"
					          	+"</tr>";
			          		});
		          	html+=''
		          		+'</tbody>'
		          	+'</table>';

					jQuery("#modal-indikator-renstra").find('.modal-title').html('Indikator Program');
			        jQuery("#modal-indikator-renstra").find('.modal-body').html(html);
					jQuery("#modal-indikator-renstra").find('.modal-dialog').css('maxWidth','1250px');
					jQuery("#modal-indikator-renstra").find('.modal-dialog').css('width','100%');
					jQuery("#modal-indikator-renstra").modal('show');
			}
		});
	}

	function kegiatanRenstra(params){

		jQuery('#wrap-loading').show();

		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action': 'get_kegiatan_renstra',
	      		'api_key': '<?php echo $api_key; ?>',
				'kode_program': params.kode_program,
				'type':1
			},
			success:function(response){

          		jQuery('#wrap-loading').hide();
          		
          		let kegiatan = ''
      				+'<div style="margin-top:10px"><button type="button" class="btn btn-primary mb-2 btn-tambah-kegiatan" data-kodeprogram="'+params.kode_program+'" data-idprogram="'+params.id_program+'"><i class="dashicons dashicons-plus" style="margin-top: 2px;"></i> Tambah Kegiatan</button></div>'
      				+'<table class="table">'
      					+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('#nama-skpd').text()+'</th>'
	          				+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(2).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Sasaran</th>'
          						+'<th>'+jQuery('#nav-sasaran tr[kodesasaran="'+jQuery("#nav-program .btn-tambah-program").data("kodesasaran")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Program</th>'
          						+'<th>'+jQuery('#nav-program tr[kodeprogram="'+params.kode_program+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
      					+'</thead>'
      				+'</table>'
      				+'<table class="table">'
      					+'<thead>'
      						+'<tr>'
      							+'<th class="text-center" style="width:45px">No</th>'
      							+'<th class="text-center">Kegiatan</th>'
      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
      							+'<th class="text-center" style="width:10%">Pagu Tahun <?php echo $i; ?></th>'
      						<?php } ?>
      							+'<th class="text-center" style="width:10%">Catatan</th>'
      							+'<th class="text-center" style="width:15%">Aksi</th>'
      						+'<tr>'
      					+'</thead>'
      					+'<tbody>';
  						response.data.map(function(value, index){
		          			for(var i in value){
		          				if(
		          					value[i] == 'null'
		          					|| value[i] == null
		          				){
		          					value[i] = '';
		          				}
		          			}
		          			var peringatan = {};
		          			var peringatan_usulan = {};
	  						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	  							if(value.pagu_akumulasi_<?php echo $i; ?> != value.pagu_akumulasi_<?php echo $i; ?>_subkegiatan){
	  								peringatan[<?php echo $i; ?>] = 'peringatan';
	  							}
	  							if(value.pagu_akumulasi_<?php echo $i; ?>_usulan != value.pagu_akumulasi_<?php echo $i; ?>_usulan_subkegiatan){
	  								peringatan_usulan[<?php echo $i; ?>] = 'peringatan';
	  							}
	  						<?php } ?>
  							kegiatan +=''
  								+'<tr kodekegiatan="'+value.id_unik+'">'
          							+'<td class="text-center" rowspan="4">'+(index+1)+'</td>'
          							+'<td rowspan="4">'+value.nama_giat+'</td>'
          						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
          							+'<td class="text-right '+peringatan[<?php echo $i; ?>]+'">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>_subkegiatan)+'</td>'
          						<?php } ?>
          							+'<td><b>Penetapan</b><br>'+value.catatan+'</td>'
          							+'<td class="text-center" rowspan="4">'
          								+'<a href="javascript:void(0)" data-kodekegiatan="'+value.id_unik+'" class="btn btn-warning btn-kelola-indikator-kegiatan" title="Lihat Indikator Kegiatan"><i class="dashicons dashicons-menu-alt" style="margin-top: 2px;"></i></a>&nbsp;'
          								+'<a href="javascript:void(0)" data-kodekegiatan="'+value.id_unik+'" data-idkegiatan="'+value.id_giat+'" data-kodegiat="'+value.kode_giat+'" class="btn btn-primary btn-detail-kegiatan" title="Lihat Sub Kegiatan"><i class="dashicons dashicons-search" style="margin-top: 2px;"></i></a>&nbsp;'	
          								+'<a href="javascript:void(0)" data-id="'+value.id+'" data-kodekegiatan="'+value.id_unik+'" data-idprogram="'+value.id_program+'" class="btn btn-success btn-edit-kegiatan" title="Edit Kegiatan"><i class="dashicons dashicons-edit" style="margin-top: 2px;"></i></a>&nbsp;'
          								+'<a href="javascript:void(0)" data-id="'+value.id+'" data-kodekegiatan="'+value.id_unik+'" data-kodeprogram="'+value.kode_program+'" data-idprogram="'+value.id_program+'" class="btn btn-danger btn-hapus-kegiatan" title="Hapus Kegiatan"><i class="dashicons dashicons-trash" style="margin-top: 2px;"></i></a>'
          							+'</td>'
          						+'</tr>'
          						+'<tr>'
	      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	      							+'<td class="text-right '+peringatan[<?php echo $i; ?>]+'">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>)+'</td>'
	      						<?php } ?>
          							+'<td>Akumulasi Penetapan Indikator Kegiatan</td>'
          						+'</tr>'
  								+'<tr>'
	      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	      							+'<td class="text-right '+peringatan_usulan[<?php echo $i; ?>]+'">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>_usulan_subkegiatan)+'</td>'
	      						<?php } ?>
          							+'<td><b>Usulan</b> '+value.catatan_usulan+'</td>'
          						+'</tr>'
  								+'<tr>'
	      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
	      							+'<td class="text-right '+peringatan_usulan[<?php echo $i; ?>]+'">'+formatRupiah(value.pagu_akumulasi_<?php echo $i; ?>_usulan)+'</td>'
	      						<?php } ?>
          							+'<td>Akumulasi Usulan Indikator Kegiatan</td>'
          						+'</tr>';
  						});
      					kegiatan +='<tbody>'
      				+'</table>';

			    jQuery("#nav-kegiatan").html(kegiatan);
			 	jQuery('.nav-tabs a[href="#nav-kegiatan"]').tab('show');
			}
		})
	}

	function indikatorKegiatanRenstra(params){

		jQuery('#wrap-loading').show();

		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_indikator_kegiatan_renstra",
          		"api_key": "<?php echo $api_key; ?>",
				'id_unik': params.id_unik,
				"type": 1
          	},
          	dataType: "json",
          	success: function(response){

          		console.log(response);

          		jQuery('#wrap-loading').hide();
          		for(var i in response.kegiatan){
      				if(
      					response.kegiatan[i] == 'null'
      					|| response.kegiatan[i] == null
      				){
      					response.kegiatan[i] = '';
      				}
      			}
      			var peringatan = {};
      			var peringatan_usulan = {};
			<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				peringatan[<?php echo $i; ?>] = '';
				if(response.kegiatan.pagu_akumulasi_<?php echo $i; ?> != response.kegiatan.pagu_akumulasi_<?php echo $i; ?>_kegiatan){
					peringatan[<?php echo $i; ?>] = 'peringatan';
				}
				peringatan_usulan[<?php echo $i; ?>] = '';
				if(response.kegiatan.pagu_akumulasi_<?php echo $i; ?>_usulan != response.kegiatan.pagu_akumulasi_<?php echo $i; ?>_usulan_kegiatan){
					peringatan_usulan[<?php echo $i; ?>] = 'peringatan';
				}
			<?php } ?>
          		
          		let html=""
					+'<div style="margin-top:10px">'
						+"<button type=\"button\" class=\"btn btn-primary mb-2 btn-add-indikator-kegiatan\" data-kodekegiatan=\""+params.id_unik+"\">"
								+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 2px;\"></i> Tambah Indikator"
						+"</button>"
					+'</div>'
          			+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('#nama-skpd').text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(2).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Sasaran</th>'
          						+'<th>'+jQuery('#nav-sasaran tr[kodesasaran="'+jQuery("#nav-program .btn-tambah-program").data("kodesasaran")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Program</th>'
          						+'<th>'+jQuery('#nav-program tr[kodeprogram="'+jQuery("#nav-kegiatan .btn-tambah-kegiatan").data("kodeprogram")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Kegiatan</th>'
	          					+'<th>'+jQuery('#nav-kegiatan tr[kodekegiatan="'+params.id_unik+'"]').find('td').eq(1).text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
	          					+'<th colspan=2>'
	          						+'<table>'
		          						+'<thead>'
				      						+'<tr>'
				      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				      							+'<th class="text-center">Pagu Tahun <?php echo $i; ?></th>'
				      						<?php } ?>
				      							+'<th class="text-center" style="width:15%">Catatan</th>'
				      						+'</tr>'
		          						+'</thead>'
		          						+'<tbody style="font-weight: normal;">'
			  								+'<tr>'
				      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				      							+'<td class="text-right '+peringatan[<?php echo $i; ?>]+'">'+formatRupiah(response.kegiatan.pagu_akumulasi_<?php echo $i; ?>)+'</td>'
				      						<?php } ?>
			          							+'<td><b>Penetapan</b><br>'+response.kegiatan.catatan+'</td>'
			          						+'</tr>'
			  								+'<tr>'
				      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				      							+'<td class="text-right '+peringatan[<?php echo $i; ?>]+'">'+formatRupiah(response.kegiatan.pagu_akumulasi_<?php echo $i; ?>_kegiatan)+'</td>'
				      						<?php } ?>
			          							+'<td>Akumulasi Penetapan Indikator Kegiatan</td>'
			          						+'</tr>'
			  								+'<tr>'
				      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				      							+'<td class="text-right '+peringatan_usulan[<?php echo $i; ?>]+'">'+formatRupiah(response.kegiatan.pagu_akumulasi_<?php echo $i; ?>_usulan)+'</td>'
				      						<?php } ?>
			          							+'<td><b>Usulan</b> '+response.kegiatan.catatan_usulan+'</td>'
			          						+'</tr>'
			  								+'<tr>'
				      						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
				      							+'<td class="text-right '+peringatan_usulan[<?php echo $i; ?>]+'">'+formatRupiah(response.kegiatan.pagu_akumulasi_<?php echo $i; ?>_usulan_kegiatan)+'</td>'
				      						<?php } ?>
			          							+'<td>Akumulasi Usulan Indikator Kegiatan</td>'
			          						+'</tr>'
		          						+'</tbody>'
	          						+'</table>'
	          					+'</th>'
	          				+'</tr>'
	          			+'</thead>'
          			+'</table>'

					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center'>No</th>"
								+"<th class='text-center'>Indikator</th>"
								+"<th class='text-center'>Satuan</th>"
								+"<th class='text-center'>Target Awal</th>"
								<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<th class='text-center'>Target Tahun <?php echo $i; ?></th>"
								+"<th class='text-center'>Pagu Tahun <?php echo $i; ?></th>"
								<?php }; ?>
								+"<th class='text-center'>Target Akhir</th>"
								+"<th class='text-center'>Catatan</th>"
								+"<th class='text-center'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_kegiatan'>";
						response.data.map(function(value, index){
		          			for(var i in value){
		          				if(
		          					value[i] == 'null'
		          					|| value[i] == null
		          				){
		          					value[i] = '';
		          				}
		          			}
			          		html +=''
			          		+"<tr>"
				          		+"<td class='text-center' rowspan='2'>"+(index+1)+"</td>"
				          		+"<td>"+value.indikator+"</td>"
				          		+"<td>"+value.satuan+"</td>"
				          		+"<td class='text-center'>"+value.target_awal+"</td>"
				          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<td class='text-center'>"+value.target_<?php echo $i; ?>+"</td>"
								+"<td class='text-right'>"+formatRupiah(value.pagu_<?php echo $i; ?>)+"</td>"
								<?php }; ?>
				          		+"<td class='text-center'>"+value.target_akhir+"</td>"
				          		+"<td><b>Penetapan</b><br>"+value.catatan+"</td>"
				          		+"<td class='text-center' rowspan='2'>"
				          			+"<a href='#' class='btn btn-success btn-edit-indikator-kegiatan' data-kodekegiatan='"+value.id_unik+"' data-id='"+value.id+"' title='Edit Indikator Indikator'><i class='dashicons dashicons-edit' style='margin-top: 2px;'></i></a>&nbsp"
									+"<a href='#' class='btn btn-danger btn-delete-indikator-kegiatan' data-kodekegiatan='"+value.id_unik+"' data-id='"+value.id+"' title='Hapus Indikator Kegiatan'><i class='dashicons dashicons-trash' style='margin-top: 2px;'></i></a>&nbsp;"
				          		+"</td>"
				          	+"</tr>"
			          		+"<tr>"
				          		+"<td>"+value.indikator_usulan+"</td>"
				          		+"<td>"+value.satuan_usulan+"</td>"
				          		+"<td class='text-center'>"+value.target_awal_usulan+"</td>"
				          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<td class='text-center'>"+value.target_<?php echo $i; ?>_usulan+"</td>"
								+"<td class='text-right'>"+formatRupiah(value.pagu_<?php echo $i; ?>_usulan)+"</td>"
								<?php }; ?>
				          		+"<td class='text-center'>"+value.target_akhir_usulan+"</td>"
				          		+"<td><b>Usulan</b><br>"+value.catatan_usulan+"</td>"
				          	+"</tr>";
				      	});
	          	html+=''
	          		+'</tbody>'
	          	+'</table>';

				jQuery("#modal-indikator-renstra").find('.modal-title').html('Indikator kegiatan');
				jQuery("#modal-indikator-renstra").find('.modal-body').html(html);
				jQuery("#modal-indikator-renstra").find('.modal-dialog').css('maxWidth','1250px');
				jQuery("#modal-indikator-renstra").find('.modal-dialog').css('width','100%');
				jQuery("#modal-indikator-renstra").modal('show');
			}
		});
	}

	function subKegiatanRenstra(params){

		jQuery('#wrap-loading').show();

		jQuery.ajax({
			method:'POST',
			url:ajax.url,
			dataType:'json',
			data:{
				'action': 'get_sub_kegiatan_renstra',
	      		'api_key': '<?php echo $api_key; ?>',
				'kode_kegiatan': params.kode_kegiatan,
				'type':1
			},
			success:function(response){

				jQuery('#wrap-loading').hide();

				let subKegiatan = ''
			      	+'<div style="margin-top:10px"><button type="button" class="btn btn-primary mb-2 btn-tambah-sub-kegiatan" data-kodekegiatan="'+params.kode_kegiatan+'" data-kodegiat="'+params.kode_giat+'"><i class="dashicons dashicons-plus" style="margin-top: 2px;"></i> Tambah Sub Kegiatan</button></div>'
			      		+'<table class="table">'
			      			+'<thead>'
				          		+'<tr>'
				          			+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
				          			+'<th>'+jQuery('#nama-skpd').text()+'</th>'
				          		+'</tr>'
			          			+'<tr>'
			          				+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
			          				+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text()+'</th>'
			          			+'</tr>'
			          			+'<tr>'
			          				+'<th class="text-center" style="width: 160px;">Tujuan</th>'
			          				+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(2).text()+'</th>'
			          			+'</tr>'
			          			+'<tr>'
			          				+'<th class="text-center" style="width: 160px;">Sasaran</th>'
			          				+'<th>'+jQuery('#nav-sasaran tr[kodesasaran="'+jQuery("#nav-program .btn-tambah-program").data("kodesasaran")+'"]').find('td').eq(1).text()+'</th>'
			          			+'</tr>'
			          			+'<tr>'
			          				+'<th class="text-center" style="width: 160px;">Program</th>'
			          				+'<th>'+jQuery('#nav-program tr[kodeprogram="'+jQuery("#nav-kegiatan .btn-tambah-kegiatan").data("kodeprogram")+'"]').find('td').eq(1).text()+'</th>'
			          			+'</tr>'
			          			+'<tr>'
			          				+'<th class="text-center" style="width: 160px;">Kegiatan</th>'
			          				+'<th>'+jQuery('#nav-kegiatan tr[kodekegiatan="'+params.kode_kegiatan+'"]').find('td').eq(1).text()+'</th>'
			          			+'</tr>'
			      			+'</thead>'
			      		+'</table>'
			      		+'<table class="table">'
			      			+'<thead>'
			      				+'<tr>'
			      					+'<th class="text-center" style="width:45px">No</th>'
			      					+'<th class="text-center">Kegiatan</th>'
			      					<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
			      					+'<th class="text-center" style="width:10%">Pagu Tahun <?php echo $i; ?></th>'
			      					<?php } ?>
			      					+'<th class="text-center" style="width:10%">Catatan</th>'
			      					+'<th class="text-center" style="width:13%">Aksi</th>'
			      				+'<tr>'
			      			+'</thead>'
			      			+'<tbody>';
			  					response.data.map(function(value, index){
					       			for(var i in value){
					       				if(
					       					value[i] == 'null'
					       					|| value[i] == null
					       				){
					     					value[i] = '';
					       				}
					       			}
			  						subKegiatan +=''
			  							+'<tr kodesubkegiatan="'+value.id_unik+'">'
			          						+'<td class="text-center" rowspan="2">'+(index+1)+'</td>'
			          						+'<td rowspan="2">'+value.nama_sub_giat+'</td>'
			          						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
			          						+'<td class="text-right">'+formatRupiah(value.pagu_<?php echo $i; ?>)+'</td>'
			          						<?php } ?>
			          						+'<td><b>Penetapan</b><br>'+value.catatan+'</td>'
			          						+'<td class="text-center" rowspan="2">'
			          							+'<a href="javascript:void(0)" data-kodesubkegiatan="'+value.id_unik+'" data-idsubgiat="'+value.id_sub_giat+'" class="btn btn-warning btn-kelola-indikator-sub-kegiatan" title="Lihat Indikator Sub Kegiatan"><i class="dashicons dashicons-menu-alt" style="margin-top: 2px;"></i></a>&nbsp;'
			          							+'<a href="javascript:void(0)" data-id="'+value.id+'" data-kodekegiatan="'+value.kode_kegiatan+'" data-kodegiat="'+value.kode_giat+'" class="btn btn-success btn-edit-sub-kegiatan" title="Edit Sub Kegiatan"><i class="dashicons dashicons-edit" style="margin-top: 2px;"></i></a>&nbsp;'
			          							+'<a href="javascript:void(0)" data-id="'+value.id+'" data-kodesubkegiatan="'+value.id_unik+'" data-kodekegiatan="'+value.kode_giat+'" data-idkegiatan="'+value.id_giat+'" class="btn btn-danger btn-hapus-sub-kegiatan" title="Hapus Sub Kegiatan"><i class="dashicons dashicons-trash" style="margin-top: 2px;"></i></a>'
			          						+'</td>'
			          					+'</tr>'
			  							+'<tr>'
			         						<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
			        						+'<td class="text-right">'+formatRupiah(value.pagu_<?php echo $i; ?>_usulan)+'</td>'
			          						<?php } ?>
			          						+'<td><b>Usulan</b> '+value.catatan_usulan+'</td>'
			          					+'</tr>';
			  						});
			      			subKegiatan +='<tbody>'
			      		+'</table>';

						jQuery("#nav-sub-kegiatan").html(subKegiatan);
						jQuery('.nav-tabs a[href="#nav-sub-kegiatan"]').tab('show');
			}
		})

	}

	function indikatorSubKegiatanRenstra(params){

		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_indikator_sub_kegiatan_renstra",
          		"api_key": "<?php echo $api_key; ?>",
				'id_unik': params.id_unik,
				"type": 1
          	},
          	dataType: "json",
          	success: function(response){

          		jQuery('#wrap-loading').hide();
          		
          		let html=""
					+'<div style="margin-top:10px">'
						+"<button type=\"button\" class=\"btn btn-primary mb-2 btn-add-indikator-sub-kegiatan\" data-kodesubkegiatan=\""+params.id_unik+"\" data-idsubgiat=\""+params.id_sub_giat+"\">"
								+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 2px;\"></i> Tambah Indikator"
						+"</button>"
					+'</div>'
          			+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('#nama-skpd').text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(2).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Sasaran</th>'
          						+'<th>'+jQuery('#nav-sasaran tr[kodesasaran="'+jQuery("#nav-program .btn-tambah-program").data("kodesasaran")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Program</th>'
          						+'<th>'+jQuery('#nav-program tr[kodeprogram="'+jQuery("#nav-kegiatan .btn-tambah-kegiatan").data("kodeprogram")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Kegiatan</th>'
	          					+'<th>'+jQuery('#nav-kegiatan tr[kodekegiatan="'+jQuery("#nav-sub-kegiatan .btn-tambah-sub-kegiatan").data('kodekegiatan')+'"]').find('td').eq(1).text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Sub Kegiatan</th>'
	          					+'<th>'+jQuery('#nav-sub-kegiatan tr[kodesubkegiatan="'+params.id_unik+'"]').find('td').eq(1).text()+'</th>'
	          				+'</tr>'
	          			+'</thead>'
          			+'</table>'

					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center'>No</th>"
								+"<th class='text-center'>Indikator</th>"
								+"<th class='text-center'>Satuan</th>"
								+"<th class='text-center'>Target Awal</th>"
								<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<th class='text-center'>Target Tahun <?php echo $i; ?></th>"
								<?php }; ?>
								+"<th class='text-center'>Target Akhir</th>"
								+"<th class='text-center'>Catatan</th>"
								+"<th class='text-center'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_sub_kegiatan'>";
						response.data.map(function(value, index){
		          			for(var i in value){
		          				if(
		          					value[i] == 'null'
		          					|| value[i] == null
		          				){
		          					value[i] = '';
		          				}
		          			}
			          		html +=''
			          		+"<tr>"
				          		+"<td class='text-center' rowspan='2'>"+(index+1)+"</td>"
				          		+"<td>"+value.indikator+"</td>"
				          		+"<td>"+value.satuan+"</td>"
				          		+"<td class='text-center'>"+value.target_awal+"</td>"
				          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<td class='text-center'>"+value.target_<?php echo $i; ?>+"</td>"
								<?php }; ?>
				          		+"<td class='text-center'>"+value.target_akhir+"</td>"
				          		+"<td><b>Penetapan</b><br>"+value.catatan+"</td>"
				          		+"<td class='text-center' rowspan='2'>"
				          			+"<a href='#' class='btn btn-success btn-edit-indikator-sub-kegiatan' data-kodesubkegiatan='"+value.id_unik+"' data-id='"+value.id+"' data-idsubgiat='"+value.id_sub_giat+"' title='Edit Indikator Sub Kegiatan'><i class='dashicons dashicons-edit' style='margin-top: 2px;'></i></a>&nbsp"
									+"<a href='#' class='btn btn-danger btn-delete-indikator-sub-kegiatan' data-kodesubkegiatan='"+value.id_unik+"' data-id='"+value.id+"' data-idsubgiat='"+value.id_sub_giat+"' title='Hapus Indikator Sub Kegiatan'><i class='dashicons dashicons-trash' style='margin-top: 2px;'></i></a>&nbsp;"
				          		+"</td>"
				          	+"</tr>"
			          		+"<tr>"
				          		+"<td>"+value.indikator_usulan+"</td>"
				          		+"<td>"+value.satuan_usulan+"</td>"
				          		+"<td class='text-center'>"+value.target_awal_usulan+"</td>"
				          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<td class='text-center'>"+value.target_<?php echo $i; ?>_usulan+"</td>"
								<?php }; ?>
				          		+"<td class='text-center'>"+value.target_akhir_usulan+"</td>"
				          		+"<td><b>Usulan</b><br>"+value.catatan_usulan+"</td>"
				          	+"</tr>";
				      	});
	          	html+=''
	          		+'</tbody>'
	          	+'</table>';

				jQuery("#modal-indikator-renstra").find('.modal-title').html('Indikator Sub Kegiatan');
				jQuery("#modal-indikator-renstra").find('.modal-body').html(html);
				jQuery("#modal-indikator-renstra").find('.modal-dialog').css('maxWidth','1250px');
				jQuery("#modal-indikator-renstra").find('.modal-dialog').css('width','100%');
				jQuery("#modal-indikator-renstra").find('.modal-footer').html('');
				jQuery("#modal-indikator-renstra").modal('show');
			}
		});
	}

	function get_urusan() {
		var html = '<option value="">Pilih Urusan</option>';
		for(var nm_urusan in all_program){
			html += '<option>'+nm_urusan+'</option>';
		}
		jQuery('#urusan-teks').html(html).select2({width: '100%', disabled: true});
	}

	function get_bidang(nm_urusan) {
		var html = '<option value="">Pilih Bidang</option>';
		if(nm_urusan){
			for(var nm_bidang in all_program[nm_urusan]){
				html += '<option>'+nm_bidang+'</option>';
			}
		}else{
			for(var nm_urusan in all_program){
				for(var nm_bidang in all_program[nm_urusan]){
					html += '<option>'+nm_bidang+'</option>';
				}
			}
		}
		jQuery('#bidang-teks').html(html).select2({width: '100%', disabled: true});
	}

	function get_program(nm_bidang, val) {
		var html = '<option value="">Pilih Program</option>';
		var current_nm_urusan = jQuery('#urusan-teks').val();
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
		jQuery('#program-teks').html(html).select2({width: '100%'});
	}

	function get_bidang_urusan(skpd){
		return new Promise(function(resolve, reject){
			if(!skpd){
				if(typeof all_program == 'undefined'){
					jQuery.ajax({
						url: ajax.url,
			          	type: "post",
			          	data: {
			          		"action": "get_bidang_urusan_renstra",
			          		"api_key": "<?php echo $api_key; ?>",
			          		"id_unit": "<?php echo $input['id_skpd']; ?>",
		        			'relasi_perencanaan': '<?php echo $relasi_perencanaan; ?>',
		        			'id_tipe_relasi': '<?php echo $id_tipe_relasi; ?>',
			          		"type": 1
			          	},
			          	dataType: "json",
			          	success: function(res){
							window.all_program = {};
							res.data.map(function(b, i){
								if(!all_program[b.nama_urusan.trim()]){
									all_program[b.nama_urusan.trim()] = {};
								}
								if(!all_program[b.nama_urusan.trim()][b.nama_bidang_urusan.trim()]){
									all_program[b.nama_urusan.trim()][b.nama_bidang_urusan.trim()] = {};
								}
								if(!all_program[b.nama_urusan.trim()][b.nama_bidang_urusan.trim()][b.nama_program.trim()]){
									all_program[b.nama_urusan.trim()][b.nama_bidang_urusan.trim()][b.nama_program.trim()] = b;
								}
							});
							resolve();
			          	}
		          });
				}else{
					resolve();
				}
			}else{
				if(typeof all_program == 'undefined'){
					jQuery.ajax({
						url: ajax.url,
			          	type: "post",
			          	data: {
			          		"action": "get_bidang_urusan",
			          		"api_key": "<?php echo $api_key; ?>",
			          		"id_unit": "<?php echo $input['id_skpd']; ?>",
		        			'relasi_perencanaan': '<?php echo $relasi_perencanaan; ?>',
		        			'id_tipe_relasi': '<?php echo $id_tipe_relasi; ?>',
			          		"type": 0
			          	},
			          	dataType: "json",
			          	success: function(res){
							window.all_skpd = {};
							res.data.map(function(b, i){
								if(!all_skpd[b.nama_urusan.trim()]){
									all_skpd[b.nama_urusan.trim()] = {};
								}
								if(!all_skpd[b.nama_urusan.trim()][b.nama_bidang_urusan.trim()]){
									all_skpd[b.nama_urusan.trim()][b.nama_bidang_urusan.trim()] = {};
								}
								if(!all_skpd[b.nama_urusan.trim()][b.nama_bidang_urusan.trim()][b.nama_skpd.trim()]){
									all_skpd[b.nama_urusan.trim()][b.nama_bidang_urusan.trim()][b.nama_skpd.trim()] = b;
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

	function sembunyikan_kolom(that){
		var val = jQuery(that).val();
		var td_usulan = jQuery('.td-usulan');
		td_usulan.show();
		if(val == 'usulan'){
			td_usulan.hide();
		}
	}

	function sembunyikan_baris(that){
		var val = jQuery(that).val();
		var tr_tujuan = jQuery('.tr-tujuan');
		var tr_sasaran = jQuery('.tr-sasaran');
		var tr_program = jQuery('.tr-program');
		var tr_kegiatan = jQuery('.tr-kegiatan');
		var tr_sub_kegiatan = jQuery('.tr-sub-kegiatan');
		tr_tujuan.show();
		tr_sasaran.show();
		tr_program.show();
		tr_kegiatan.show();
		tr_sub_kegiatan.show();
		if(val == 'tr-tujuan'){
			tr_tujuan.hide();
			tr_sasaran.hide();
			tr_program.hide();
			tr_kegiatan.hide();
			tr_sub_kegiatan.hide();
		}else if(val == 'tr-sasaran'){
			tr_sasaran.hide();
			tr_program.hide();
			tr_kegiatan.hide();
			tr_sub_kegiatan.hide();
		}else if(val == 'tr-program'){
			tr_program.hide();
			tr_kegiatan.hide();
			tr_sub_kegiatan.hide();
		}else if(val == 'tr-kegiatan'){
			tr_kegiatan.hide();
			tr_sub_kegiatan.hide();
		}else if(val == 'tr-sub-kegiatan'){
			tr_sub_kegiatan.hide();
		} 
	}

	function show_debug(that){
		if(jQuery(that).is(':checked')){
			jQuery("#table-renstra").find('.status-rpjm').css('background-color', '#f5c9c9');
		}else{
			jQuery("#table-renstra").find('.status-rpjm').css('background-color', 'transparent');
		}
	}

	function set_data_jadwal_lokal() {
		var html = '<option value="">Pilih Jadwal</option>';
		for(var jadwal_lokal in all_jadwal_lokal){
			html += '<option value="'+jadwal_lokal.id_jadwal_lokal+'">'+jadwal_lokal.nama+'</option>';
		}
		jQuery('#jadwal_lokal').html(html);
	}

	function getFormData($form) {
	    let unindexed_array = $form.serializeArray();
	    let indexed_array = {};

	    jQuery.map(unindexed_array, function (n, i) {
	    	indexed_array[n['name']] = n['value'];
	    });

	    return indexed_array;
	}

	function runFunction(name, arguments){
	    var fn = window[name];
	    if(typeof fn !== 'function')
	        return;

	    fn.apply(window, arguments);
	}

	function setBidurAll(that){
		var data = jQuery(that).find('option:selected').attr('data');
		jQuery('input[name="bidur-all"]').val(data);
	}

	function copy_usulan(that){
		var modal = jQuery(that).closest('.modal-dialog');
		var action = modal.find('.modal-footer .btn-success').attr('data-action');
		var usulan = modal.find('textarea[name="indikator_teks_usulan"]').val();
		modal.find('textarea[name="indikator_teks"]').val(usulan);
		var usulan = modal.find('select[name="id_indikator_usulan"]').val();
		modal.find('select[name="id_indikator"]').val(usulan);
		var usulan = modal.find('input[name="satuan_usulan"]').val();
		modal.find('input[name="satuan"]').val(usulan);
		var usulan = modal.find('input[name="target_awal_usulan"]').val();
		modal.find('input[name="target_awal"]').val(usulan);
		var usulan = modal.find('input[name="target_1_usulan"]').val();
		modal.find('input[name="target_1"]').val(usulan);
		var usulan = modal.find('input[name="target_2_usulan"]').val();
		modal.find('input[name="target_2"]').val(usulan);
		var usulan = modal.find('input[name="target_3_usulan"]').val();
		modal.find('input[name="target_3"]').val(usulan);
		var usulan = modal.find('input[name="target_4_usulan"]').val();
		modal.find('input[name="target_4"]').val(usulan);
		var usulan = modal.find('input[name="target_5_usulan"]').val();
		modal.find('input[name="target_5"]').val(usulan);
		var usulan = modal.find('input[name="pagu_1_usulan"]').val();
		modal.find('input[name="pagu_1"]').val(usulan);
		var usulan = modal.find('input[name="pagu_2_usulan"]').val();
		modal.find('input[name="pagu_2"]').val(usulan);
		var usulan = modal.find('input[name="pagu_3_usulan"]').val();
		modal.find('input[name="pagu_3"]').val(usulan);
		var usulan = modal.find('input[name="pagu_4_usulan"]').val();
		modal.find('input[name="pagu_4"]').val(usulan);
		var usulan = modal.find('input[name="pagu_5_usulan"]').val();
		modal.find('input[name="pagu_5"]').val(usulan);
		var usulan = modal.find('input[name="target_akhir_usulan"]').val();
		modal.find('input[name="target_akhir"]').val(usulan);
		var usulan = modal.find('textarea[name="catatan_usulan"]').val();
		modal.find('textarea[name="catatan"]').val(usulan);
	}

	function copy_usulan_all(){
		if(confirm('Apakah anda yakin untuk melakukan ini? data penetapan akan diupdate sama dengan data usulan.')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "copy_usulan_renstra",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"id_unit": "<?php echo $input['id_skpd']; ?>"
	          	},
	          	dataType: "json",
	          	success: function(res){
	          		alert(res.message);
	          		jQuery('#wrap-loading').hide();
	          	}
	        });
		}
	}

	function get_master_indikator_subgiat(params, tag){
		return new Promise(function(resolve, reject){
			jQuery.ajax({
				url: ajax.url,
			    type: "post",
			    data: {
			       		"action": "get_indikator_sub_keg_parent",
			       		"api_key": "<?php echo $api_key; ?>",
			       		"id_sub_keg": params.id_sub_giat,
			       		"tahun_anggaran": params.tahun_anggaran
			       	},
			       	dataType: "json",
			       	success: function(res){
			      		let opt = ''
			          		+'<option value="">Pilih Indikator</option>'
			          		res.data.map(function(value, index) {	
			          			opt+='<option value="'+value.id+'" data-satuan="'+value.satuan+'">'+value.indikator +' ('+ value.satuan + ')' +'</option>'
			          		});
			          	jQuery("."+tag).html(opt);
			          	resolve();
			        }
			});
		})
	}

	function get_list_sub_kegiatan(params, tag){
		return new Promise(function(resolve, reject){
			jQuery.ajax({
				url: ajax.url,
			    type: "post",
			    data: {
			       		"action": "get_sub_keg_parent",
			       		"api_key": "<?php echo $api_key; ?>",
			       		"kode_giat": params.kode_giat,
			       		"id_unit": params.id_unit,
			       		"kode_unit": params.kode_unit,
			       		"tahun_anggaran": params.tahun_anggaran
			       	},
			       	dataType: "json",
			       	success: function(res){
			          	let option='<option value="">Pilih Sub Kegiatan</option>';
						res.data.map(function(value, index){
		                    value.map(function(value_sub, index_sub){
		                        let nama = value_sub.nama_sub_giat.split(' ');
		                        let del = nama.shift();
		                        nama = nama.join(' ');
		                        option+='<option value="'+value_sub.id_sub_giat+'">'+nama+'</option>';
		                    });
		                });
		                jQuery("#"+tag).html(option);
		                resolve();
			        }
			});
		})
	}

	function setSatuan(that, input){
		jQuery(`input[name=${input}]`).val(jQuery(that).find(':selected').data('satuan'));
	}
</script>