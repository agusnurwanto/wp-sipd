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
$disabled = 'disabled';

$jadwal_lokal = $wpdb->get_results("SELECT a.*, (SELECT id_tipe FROM data_jadwal_lokal WHERE id_jadwal_lokal=a.relasi_perencanaan) id_tipe_relasi FROM data_jadwal_lokal a WHERE a.id_jadwal_lokal = (SELECT MAX(id_jadwal_lokal) FROM data_jadwal_lokal a WHERE a.id_tipe=4)", ARRAY_A);

$add_renstra = '';
if(!empty($jadwal_lokal)){
	$awal_renstra = $jadwal_lokal[0]['tahun_anggaran'];
	$namaJadwal = $jadwal_lokal[0]['nama'];
	$mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
	$selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];
	$relasi_perencanaan = $jadwal_lokal[0]['relasi_perencanaan'] ?? '-';
	$id_tipe_relasi = $jadwal_lokal[0]['id_tipe_relasi'] ?? '-';
	$lama_pelaksanaan = $jadwal_lokal[0]['lama_pelaksanaan'];

	$awal = new DateTime($mulaiJadwal);
	$akhir = new DateTime($selesaiJadwal);
	$now = new DateTime(date('Y-m-d H:i:s'));

	if($now >= $awal && $now <= $akhir){
		$add_renstra = '<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RENSTRA</a>';
	}
}

$akhir_renstra = $awal_renstra+$lama_pelaksanaan;
$urut = $tahun_anggaran-$awal_renstra;
$rumus_indikator_db = $wpdb->get_results("SELECT * FROM data_rumus_indikator WHERE active=1 AND tahun_anggaran=".$tahun_anggaran, ARRAY_A);
$rumus_indikator = '';
foreach ($rumus_indikator_db as $k => $v){
	$rumus_indikator .= '<option value="'.$v['id'].'">'.$v['rumus'].'</option>';
}

$where_skpd = '';
if(!empty($input['id_skpd'])){
	$where_skpd = "and id_skpd =".$input['id_skpd'];
}

$user_id = um_user( 'ID' );
$user_meta = get_userdata($user_id);
if(in_array("administrator", $user_meta->roles)){
	$disabled='';
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
$nama_pemda = get_option('_crb_daerah');

$tujuan_all = $wpdb->get_results($wpdb->prepare("
			SELECT 
				* 
			FROM data_renstra_tujuan_lokal 
			WHERE 
				id_unit=%d AND 
				active=1 ORDER BY urut_tujuan",
				$input['id_skpd']), ARRAY_A);

// echo '<pre>';print_r($wpdb->last_query);echo '</pre>';die();

foreach ($tujuan_all as $keyTujuan => $tujuan_value) {
	if(empty($data_all['data'][$tujuan_value['id_unik']])){
		$data_all['data'][$tujuan_value['id_unik']] = [
			'id' => $tujuan_value['id'],
			'id_bidang_urusan' => $tujuan_value['id_bidang_urusan'],
			'id_unik' => $tujuan_value['id_unik'],
			'tujuan_teks' => $tujuan_value['tujuan_teks'],
			'nama_bidang_urusan' => $tujuan_value['nama_bidang_urusan'],
			'indikator' => array(),
			'data' => array(),
			'status_rpjm' => false
		];

		if(!empty($tujuan_value['kode_sasaran_rpjm']) && $relasi_perencanaan != '-'){
			$table = 'data_rpjmd_sasaran_lokal';
			switch ($id_tipe_relasi) {
				case '2':
						$table = 'data_rpjmd_sasaran_lokal';
					break;

				case '3':
						$table = 'data_rpd_sasaran_lokal';
					break;
			}

			$id = $wpdb->get_var("SELECT id FROM ".$table." WHERE id_unik='{$tujuan_value['kode_sasaran_rpjm']}'");
			if(!empty($id)){
				$data_all['data'][$tujuan_value['id_unik']]['status_rpjm']=true;
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
				active=1 ORDER BY urut_sasaran",
				$tujuan_value['id_unik']), ARRAY_A);

		foreach ($sasaran_all as $keySasaran => $sasaran_value) {
			if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']])){
				$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']] = [
					'id' => $sasaran_value['id'],
					'id_unik' => $sasaran_value['id_unik'],
					'sasaran_teks' => $sasaran_value['sasaran_teks'],
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
								'indikator' => array(),
								'data' => array()
							];
						}

					$program_ids[$program_value['id_unik']] = "'".$program_value['id_unik']."'";

					if(!empty($program_value['id_unik_indikator'])){
						if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']])){
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
									'indikator' => array()
								];
							}

							$kegiatan_ids[$kegiatan_value['id_unik']] = "'".$kegiatan_value['id_unik']."'";

							if(!empty($kegiatan_value['id_unik_indikator'])){
								if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])){
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

// echo '<pre>';print_r($data_all);echo '</pre>';die();

// initial data kosong
if(empty($data_all['data']['tujuan_kosong'])){
	$data_all['data']['tujuan_kosong'] = array(
		'tujuan_teks' => '<span style="color: red">kosong</span>',
		'indikator' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong'])){
	$data_all['data']['tujuan_kosong']['data']['sasaran_kosong'] = array(
		'sasaran_teks' => '<span style="color: red">kosong</span>',
		'indikator' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong'])){
	$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong'] = array(
		'program_teks' => '<span style="color: red">kosong</span>',
		'indikator' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data']['kegiatan_kosong'])){
	$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data']['kegiatan_kosong'] = array(
		'kegiatan_teks' => '<span style="color: red">kosong</span>',
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
		WHERE id_unik NOT IN (".implode(',', $sasaran_ids).") AND
			id_unit=".$input['id_skpd'];
}else{
	$sql = "
		SELECT 
			* 
		FROM data_renstra_sasaran_lokal WHERE id_unit=".$input['id_skpd'];
}
$sasaran_all_kosong = $wpdb->get_results($sql, ARRAY_A);

foreach ($sasaran_all_kosong as $keySasaran => $sasaran_value) {
	if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']])){
		$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']] = [
			'id' => $sasaran_value['id'],
			'id_unik' => $sasaran_value['id_unik'],
			'sasaran_teks' => $sasaran_value['sasaran_teks'],
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
							active=1 ORDER BY id",
							$sasaran_value['id_unik']), ARRAY_A);

		foreach ($program_all as $keyProgram => $program_value) {
			if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']])){
				$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']] = [
						'id' => $program_value['id'],
						'id_unik' => $program_value['id_unik'],
						'program_teks' => $program_value['nama_program'],
						'indikator' => array(),
						'data' => array()
				];
			}

			if(!empty($program_value['id_unik_indikator'])){
				if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']])){
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
												active=1 ORDER BY id",
												$program_value['id_unik'],
												$sasaran_value['id_unik']
											), ARRAY_A);

				foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {
										
					if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']])){

						$data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']] = [
							'id' => $kegiatan_value['id'],
							'id_unik' => $kegiatan_value['id_unik'],
							'kegiatan_teks' => $kegiatan_value['nama_giat'],
							'indikator' => array()
						];
					}

					if(!empty($kegiatan_value['id_unik_indikator'])) {
						if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])){
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
							];
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
		WHERE id_unik NOT IN (".implode(',', $program_ids).") AND id_unit=".$input['id_skpd'];
}else{
	$sql = "
		SELECT 
			* 
		FROM data_renstra_program_lokal WHERE id_unit=".$input['id_skpd'];
}
$program_all_kosong = $wpdb->get_results($sql, ARRAY_A);

foreach ($program_all_kosong as $keyProgram => $program_value) {
	if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']])){
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']] = [
			'id' => $program_value['id'],
			'id_unik' => $program_value['id_unik'],
			'program_teks' => $program_value['nama_program'],
			'indikator' => array(),
			'data' => array()
		];
	}

	if(!empty($program_value['id_unik_indikator'])){
		if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']])){
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
			];
		}
	}

	if(empty($program_value['id_unik_indikator'])){
		$kegiatan_all = $wpdb->get_results($wpdb->prepare("SELECT *  FROM data_renstra_kegiatan_lokal WHERE 
												kode_program=%s AND 
												active=1 ORDER BY id",
												$program_value['id_unik']
											), ARRAY_A);

		foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {									
			if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']])){
				$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']] = [
					'id' => $kegiatan_value['id'],
					'id_unik' => $kegiatan_value['id_unik'],
					'kegiatan_teks' => $kegiatan_value['nama_giat'],
					'indikator' => array()
				];
			}

			if(!empty($kegiatan_value['id_unik_indikator'])){
				if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])){
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
					];
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
		WHERE id_unik NOT IN (".implode(',', $kegiatan_ids).") AND id_unit=".$input['id_skpd'];
}else{
	$sql = "
		SELECT 
			* 
		FROM data_renstra_kegiatan_lokal WHERE id_unit=".$input['id_skpd'];
}
$kegiatan_all = $wpdb->get_results($sql, ARRAY_A);

foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {									
	if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']])){
		$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']] = [
			'id' => $kegiatan_value['id'],
			'id_unik' => $kegiatan_value['id_unik'],
			'kegiatan_teks' => $kegiatan_value['nama_giat'],
			'indikator' => array()
		];
	}

	if(!empty($kegiatan_value['id_unik_indikator'])){
		if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data']['program_kosong']['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])){
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
			];
		}
	}
}

// hapus data kosong jika empty
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
	}

	$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
	$body .= '
			<tr class="tr-tujuan'.$bg_rpjm.'">
				<td class="kiri atas kanan bawah">'.$no_tujuan.'</td>
				<td class="kiri atas kanan bawah">'.$tujuan['nama_bidang_urusan'].'</td>
				<td class="atas kanan bawah">'.$tujuan['tujuan_teks'].'</td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah">'.$indikator_tujuan.'</td>
				<td class="atas kanan bawah">'.$target_awal.'</td>';
				for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
					$body.="<td class=\"atas kanan bawah\">".$target_arr[$i]."</td><td class=\"atas kanan bawah\"></td>";
				}
				$body.='<td class="atas kanan bawah">'.$target_akhir.'</td>
				<td class="atas kanan bawah">'.$satuan.'</td>
				<td class="atas kanan bawah"></td>
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
		}

		$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
		$body .= '
				<tr class="tr-sasaran'.$bg_rpjm.'">
					<td class="kiri atas kanan bawah">'.$no_tujuan.".".$no_sasaran.'</td>
					<td class="kiri atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah">'.$sasaran['sasaran_teks'].'</td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah">'.$indikator_sasaran.'</td>
					<td class="atas kanan bawah">'.$target_awal.'</td>';
					for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
						$body.="<td class=\"atas kanan bawah\">".$target_arr[$i]."</td><td class=\"atas kanan bawah\"></td>";
					}
					$body.='<td class="atas kanan bawah">'.$target_akhir.'</td>
					<td class="atas kanan bawah">'.$satuan.'</td>
					<td class="atas kanan bawah"></td>
				</tr>
		';
		
		$no_program=0;
		foreach ($sasaran['data'] as $program) {
			$no_program++;
			$indikator_program = '';
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
			foreach($program['indikator'] as $key => $indikator){
				$indikator_program .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
				$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
				$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
				$pagu_1 .= '<div class="indikator">'.$indikator['pagu_1'].'</div>';
				$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
				$pagu_2 .= '<div class="indikator">'.$indikator['pagu_2'].'</div>';
				$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
				$pagu_3 .= '<div class="indikator">'.$indikator['pagu_3'].'</div>';
				$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
				$pagu_4 .= '<div class="indikator">'.$indikator['pagu_4'].'</div>';
				$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
				$pagu_5 .= '<div class="indikator">'.$indikator['pagu_5'].'</div>';
				$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
				$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
			}

			$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
			$pagu_arr = [$pagu_1, $pagu_2, $pagu_3, $pagu_4, $pagu_5];
			$body .= '
					<tr class="tr-program'.$bg_rpjm.'">
						<td class="kiri atas kanan bawah">'.$no_tujuan.".".$no_sasaran.".".$no_program.'</td>
						<td class="kiri atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah">'.$program['program_teks'].'</td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah">'.$indikator_program.'</td>
						<td class="atas kanan bawah">'.$target_awal.'</td>';
						for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
							$body.="<td class=\"atas kanan bawah\">".$target_arr[$i]."</td><td class=\"atas kanan bawah\">".$pagu_arr[$i]."</td>";
						}
						$body.='<td class="atas kanan bawah">'.$target_akhir.'</td>
						<td class="atas kanan bawah">'.$satuan.'</td>
						<td class="atas kanan bawah"></td>
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
				foreach($kegiatan['indikator'] as $key => $indikator){
					$indikator_kegiatan .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
					$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
					$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
					$pagu_1 .= '<div class="indikator">'.$indikator['pagu_1'].'</div>';
					$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
					$pagu_2 .= '<div class="indikator">'.$indikator['pagu_2'].'</div>';
					$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
					$pagu_3 .= '<div class="indikator">'.$indikator['pagu_3'].'</div>';
					$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
					$pagu_4 .= '<div class="indikator">'.$indikator['pagu_4'].'</div>';
					$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
					$pagu_5 .= '<div class="indikator">'.$indikator['pagu_5'].'</div>';
					$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
					$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
				}

				$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
				$pagu_arr = [$pagu_1, $pagu_2, $pagu_3, $pagu_4, $pagu_5];
				$body .= '
						<tr class="tr-kegiatan'.$bg_rpjm.'">
							<td class="kiri atas kanan bawah">'.$no_tujuan.".".$no_sasaran.".".$no_program.".".$no_kegiatan.'</td>
							<td class="kiri atas kanan bawah"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah">'.$kegiatan['kegiatan_teks'].'</td>
							<td class="atas kanan bawah">'.$indikator_kegiatan.'</td>
							<td class="atas kanan bawah">'.$target_awal.'</td>';
							for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
								$body.="<td class=\"atas kanan bawah\">".$target_arr[$i]."</td><td class=\"atas kanan bawah\">".$pagu_arr[$i]."</td>";
							}
							$body.='
							<td class="atas kanan bawah">'.$target_akhir.'</td>
							<td class="atas kanan bawah">'.$satuan.'</td>
							<td class="atas kanan bawah"></td>
						</tr>
				';
			}
		}
	}
}
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.css" rel="stylesheet">
<style type="text/css">
	.debug-tujuan, .debug-sasaran, .debug-program, .debug-kegiatan, .debug-kode { display: none; }
	.indikator_program { min-height: 40px; }
	.indikator_kegiatan { min-height: 40px; }
	.modal {overflow-y:auto;}
	.status-rpjm{
		background-color: #ffa2a2;
	}
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">RENCANA STRATEGIS (RENSTRA) <br><?php echo $judul_skpd.'Tahun '.$awal_renstra.' - '.$akhir_renstra.' '.$nama_pemda; ?></h4>
<div id="cetak" title="Laporan MONEV RENSTRA" style="padding: 5px; overflow: auto; height: 80vh;">
	<table id="table-renstra" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<?php
			$row_head='<tr>
				<th style="width: 85px;" class="row_head_1 atas kiri kanan bawah text_tengah text_blok">No</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Bidang Urusan</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Tujuan</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Sasaran</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Program</th>
				<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Kegiatan</th>
				<th style="width: 400px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Indikator</th>
				<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Target Awal</th>';
				for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
				$row_head.='<th style="width: 100px;" class="row_head_1_tahun atas kanan bawah text_tengah text_blok">Tahun '.$i.'</th>';
				}
			$row_head.='
				<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Target Akhir</th>
				<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Satuan</th>
				<th style="width: 150px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Keterangan</th>
			</tr>
			<tr>';
			for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
			$row_head.='<th style="width: 100px;" class="row_head_2 atas kanan bawah text_tengah text_blok">Target</th><th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Pagu</th>';
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
				<?php 
				$target_temp = 8;
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
				  	</div>
				</nav>
				<div class="tab-content" id="nav-tabContent">
				  	<div class="tab-pane fade show active" id="nav-tujuan" role="tabpanel" aria-labelledby="nav-tujuan-tab"></div>
				  	<div class="tab-pane fade" id="nav-sasaran" role="tabpanel" aria-labelledby="nav-sasaran-tab"></div>
				  	<div class="tab-pane fade" id="nav-program" role="tabpanel" aria-labelledby="nav-program-tab"></div>
				  	<div class="tab-pane fade" id="nav-kegiatan" role="tabpanel" aria-labelledby="nav-kegiatan-tab"></div>
				</div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<!-- Modal indikator renstra -->
<div class="modal fade" id="modal-indikator-renstra" tabindex="-1" role="dialog" aria-labelledby="modal-indikator-renstra-label" aria-hidden="true">
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
<div class="modal fade" id="modal-crud-renstra" tabindex="-2" role="dialog" aria-labelledby="modal-crud-renstra-label" aria-hidden="true">
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
		+'<a style="margin-left: 10px;" id="singkron-sipd" onclick="return false;" href="#" class="btn btn-danger">Ambil data dari SIPD lokal</a>'
		+'<?php echo $add_renstra; ?>'
		+'<a style="margin-left: 10px;" id="cetak-renstra" onclick="return false;" href="#" class="btn btn-warning">CETAK RENSTRA</a>'
		+'<h3 style="margin-top: 20px;">SETTING</h3>'
		+'<label><input type="checkbox" onclick="tampilkan_edit(this);"> Edit Data RENSTRA</label>'
		+'<label style="margin-left: 20px;"><input type="checkbox" onclick="show_debug(this);"> Debug Cascading RPJM</label>'
		+'<label style="margin-left: 20px;">'
			+'Sembunyikan Baris '
			+'<select id="sembunyikan-baris" onchange="sembunyikan_baris(this);" style="padding: 5px 10px; min-width: 200px;">'
				+'<option value="">Pilih Baris</option>'
				+'<option value="tr-sasaran">Sasaran</option>'
				+'<option value="tr-program">Program</option>'
				+'<option value="tr-kegiatan">Kegiatan</option>'
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
		          	'id_unit': '<?php echo $input['id_skpd']; ?>',
		          	'relasi_perencanaan': '<?php echo $relasi_perencanaan; ?>',
		          	'id_tipe_relasi': '<?php echo $id_tipe_relasi; ?>',
				},
				success:function(response){
						jQuery('#wrap-loading').hide();
						if(response.status){
							let tujuanModal = jQuery("#modal-crud-renstra");
							let html = '<form id="form-renstra">'
											+'<input type="hidden" name="id_unit" value="'+<?php echo $input['id_skpd']; ?>+'">'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Sasaran Rpjm/Rpd</label>'
												+'<select class="form-control" id="sasaran-parent" name="sasaran_parent" onchange="pilihSasaranParent(this)">';
													html+='<option value="">Pilih Sasaran</option>';
													response.data.map(function(value, index){
														html +='<option value="'+value.id_unik+'|'+value.id_program+'">'+value.sasaran_teks+'</option>';
													})
												html+='</select>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Tujuan Renstra</label>'
								  				+'<textarea class="form-control" id="tujuan_teks" name="tujuan_teks"></textarea>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Urut Tujuan</label>'
								  				+'<input type="number" class="form-control" name="urut_tujuan" />'
											+'</div>'
										+'</form>';

							tujuanModal.find('.modal-title').html('Tambah Tujuan');
							tujuanModal.find('.modal-body').html(html);
							tujuanModal.find('.modal-footer').html(''
								+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
									+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
								+'</button>'
								+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
									+'data-action="submit_tujuan_renstra" '
									+'data-view="tujuanRenstra"'
								+'>'
									+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
								+'</button>');
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
							'id_tujuan': idtujuan, 
			        'id_unit': '<?php echo $input['id_skpd'] ?>',
			        'relasi_perencanaan': '<?php echo $relasi_perencanaan; ?>',
			        'id_tipe_relasi': '<?php echo $id_tipe_relasi; ?>',
          	},
          	dataType: "json",
          	success: function(response){
				jQuery('#wrap-loading').hide();
				if(response.status){
					let html = '<form id="form-renstra">'
											+'<input type="hidden" name="id" value="'+response.tujuan.id+'">'
											+'<input type="hidden" name="id_unik" value="'+response.tujuan.id_unik+'">'
											+'<input type="hidden" name="id_unit" value="'+<?php echo $input['id_skpd']; ?>+'">'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Sasaran Rpjm/Rpd</label>'
												+'<select class="form-control" id="sasaran-parent" name="sasaran_parent" onchange="pilihSasaranParent(this)">';
													html+='<option value="" selected>Pilih Sasaran</option>';
													response.sasaran_parent.map(function(value, index){
														html +='<option value="'+value.id_unik+'|'+value.id_program+'">'+value.sasaran_teks+'</option>';
													})
												html+='</select>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Tujuan Renstra</label>'
								  				+'<textarea class="form-control" id="tujuan_teks" name="tujuan_teks">'+response.tujuan.tujuan_teks+'</textarea>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="tujuan_teks">Urut Tujuan</label>'
								  				+'<input type="number" class="form-control" name="urut_tujuan" value="'+response.tujuan.urut_tujuan+'" />'
											+'</div>'
										+'</form>';

			        tujuanModal.find('.modal-title').html('Edit Tujuan');
					tujuanModal.find('.modal-body').html(html);
					tujuanModal.find('.modal-footer').html(''
						+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
							+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
						+'</button>'
						+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
							+'data-action="update_tujuan_renstra" '
							+'data-view="tujuanRenstra"'
						+'>'
							+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
						+'</button>');
					tujuanModal.modal('show');
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
						+'<label for="indikator_teks">Indikator</label>'
		  				+'<textarea class="form-control" name="indikator_teks"></textarea>'
					+'</div>'
					+'<div class="form-group">'
						+'<div class="row">'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Usulan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="satuan_usulan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan_usulan"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal">Target awal</label>'
							  				+'<input type="text" class="form-control" name="target_awal_usulan"/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>_usulan"/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir">Target akhir</label>'
							  				+'<input type="text" class="form-control" name="target_akhir_usulan"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan_usulan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan_usulan"></textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Penetapan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="satuan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan" <?php echo $disabled; ?> />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal">Target awal</label>'
							  				+'<input type="text" class="form-control" name="target_awal" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir">Target akhir</label>'
							  				+'<input type="text" class="form-control" name="target_akhir" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</div>'
				+'</form>';

			indikatorTujuanModal.find('.modal-title').html('Tambah Indikator');
			indikatorTujuanModal.find('.modal-body').html(html);
			indikatorTujuanModal.find('.modal-footer').html(''
				+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
					+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
				+'</button>'
				+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
					+'data-action="submit_indikator_tujuan_renstra" '
					+'data-view="indikatorTujuanRenstra"'
				+'>'
					+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
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

          		let html = '<form id="form-renstra">'
					+'<input type="hidden" name="id" value="'+id+'">'
					+'<input type="hidden" name="id_unik" value="'+id_unik+'">'
					+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
					+'<div class="form-group">'
						+'<label for="indikator_teks">Indikator</label>'
	  					+'<textarea class="form-control" name="indikator_teks">'+response.data.indikator_teks+'</textarea>'
					+'</div>'
					+'<div class="form-group">'
						+'<div class="row">'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Usulan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="satuan_usulan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan_usulan" value="'+response.data.satuan_usulan+'" />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal_usulan">Target awal</label>'
							  				+'<input type="text" class="form-control" name="target_awal_usulan" value="'+response.data.target_awal_usulan+'" />'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>_usulan" value="'+response.data.target_<?php echo $i; ?>_usulan+'"/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir_usulan">Target akhir</label>'
							  				+'<input type="text" class="form-control" name="target_akhir_usulan" value="'+response.data.target_akhir_usulan+'"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan_usulan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan_usulan">'+response.data.catatan_usulan+'</textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Penetapan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="satuan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan" value="'+response.data.satuan+'" <?php echo $disabled; ?> />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal">Target awal</label>'
							  				+'<input type="text" class="form-control" name="target_awal" value="'+response.data.target_awal+'" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>" value="'+response.data.target_<?php echo $i; ?>+'" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir">Target akhir</label>'
							  				+'<input type="text" class="form-control" name="target_akhir" value="'+response.data.target_akhir+'" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.data.catatan+'</textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</div>'
				  +'</form>';

				indikatorTujuanModal.find('.modal-title').html('Edit Indikator Tujuan');
				indikatorTujuanModal.find('.modal-body').html(html);
				indikatorTujuanModal.find('.modal-footer').html(''
					+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
						+'data-action="update_indikator_tujuan_renstra" '
						+'data-view="indikatorTujuanRenstra"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
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
		let html = '<form id="form-renstra">'
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
					+'</form>';

		sasaranModal.find('.modal-title').html('Tambah Sasaran');
		sasaranModal.find('.modal-body').html(html);
		sasaranModal.find('.modal-footer').html(''
			+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
				+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
			+'</button>'
			+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
				+'data-action="submit_sasaran_renstra" '
				+'data-view="sasaranRenstra"'
			+'>'
				+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
			+'</button>');
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
										+'</form>';

					        sasaranModal.find('.modal-title').html('Edit Sasaran');
									sasaranModal.find('.modal-body').html(html);
									sasaranModal.find('.modal-footer').html(''
										+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
											+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
										+'</button>'
										+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
											+'data-action="update_sasaran_renstra" '
											+'data-view="sasaranRenstra"'
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
						+'<label for="indikator_teks">Indikator</label>'
		  				+'<textarea class="form-control" name="indikator_teks"></textarea>'
					+'</div>'
					+'<div class="form-group">'
						+'<div class="row">'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Usulan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="satuan_usulan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan_usulan"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal_usulan">Target awal</label>'
							  				+'<input type="text" class="form-control" name="target_awal_usulan"/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>_usulan"/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir_usulan">Target akhir</label>'
							  				+'<input type="text" class="form-control" name="target_akhir_usulan"/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan_usulan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan_usulan"></textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
							+'<div class="col-md-6">'
								+'<div class="card">'
									+'<div class="card-header">Penetapan</div>'
									+'<div class="card-body">'
										+'<div class="form-group">'
											+'<label for="satuan">Satuan</label>'
							  				+'<input type="text" class="form-control" name="satuan" <?php echo $disabled; ?> />'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="target_awal">Target awal</label>'
							  				+'<input type="text" class="form-control" name="target_awal" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+'<div class="form-group">'
											+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
							  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
										+'</div>'
										<?php }; ?>
										+'<div class="form-group">'
											+'<label for="target_akhir">Target akhir</label>'
							  				+'<input type="text" class="form-control" name="target_akhir" <?php echo $disabled; ?>/>'
										+'</div>'
										+'<div class="form-group">'
											+'<label for="catatan">Catatan</label>'
							  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</div>'
					+'</form>';

			indikatorSasaranModal.find('.modal-title').html('Tambah Indikator');
			indikatorSasaranModal.find('.modal-body').html(html);
			indikatorSasaranModal.find('.modal-footer').html(''
				+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
					+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
				+'</button>'
				+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
					+'data-action="submit_indikator_sasaran_renstra" '
					+'data-view="indikatorSasaranRenstra"'
				+'>'
					+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
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

          		let html = '<form id="form-renstra">'
								+'<input type="hidden" name="id" value="'+id+'">'
								+'<input type="hidden" name="id_unik" value="'+id_unik+'">'
								+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
								+'<div class="form-group">'
									+'<label for="indikator_teks">Indikator</label>'
				  					+'<textarea class="form-control" name="indikator_teks">'+response.data.indikator_teks+'</textarea>'
								+'</div>'
								+'<div class="form-group">'
									+'<div class="row">'
										+'<div class="col-md-6">'
											+'<div class="card">'
												+'<div class="card-header">Usulan</div>'
												+'<div class="card-body">'
													+'<div class="form-group">'
														+'<label for="satuan_usulan">Satuan</label>'
										  				+'<input type="text" class="form-control" name="satuan_usulan" value="'+response.data.satuan_usulan+'" />'
													+'</div>'
													+'<div class="form-group">'
														+'<label for="target_awal_usulan">Target awal</label>'
										  				+'<input type="text" class="form-control" name="target_awal_usulan" value="'+response.data.target_awal_usulan+'" />'
													+'</div>'
													<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
													+'<div class="form-group">'
														+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
										  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>_usulan" value="'+response.data.target_<?php echo $i; ?>_usulan+'"/>'
													+'</div>'
													<?php }; ?>
													+'<div class="form-group">'
														+'<label for="target_akhir_usulan">Target akhir</label>'
										  				+'<input type="text" class="form-control" name="target_akhir_usulan" value="'+response.data.target_akhir_usulan+'"/>'
													+'</div>'
													+'<div class="form-group">'
														+'<label for="catatan_usulan">Catatan</label>'
										  				+'<textarea class="form-control" name="catatan_usulan">'+response.data.catatan_usulan+'</textarea>'
													+'</div>'
												+'</div>'
											+'</div>'
										+'</div>'
										+'<div class="col-md-6">'
											+'<div class="card">'
												+'<div class="card-header">Penetapan</div>'
												+'<div class="card-body">'
													+'<div class="form-group">'
														+'<label for="satuan">Satuan</label>'
										  				+'<input type="text" class="form-control" name="satuan" value="'+response.data.satuan+'" <?php echo $disabled; ?> />'
													+'</div>'
													+'<div class="form-group">'
														+'<label for="target_awal">Target awal</label>'
										  				+'<input type="text" class="form-control" name="target_awal" value="'+response.data.target_awal+'" <?php echo $disabled; ?>/>'
													+'</div>'
													<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
													+'<div class="form-group">'
														+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
										  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>" value="'+response.data.target_<?php echo $i; ?>+'" <?php echo $disabled; ?>/>'
													+'</div>'
													<?php }; ?>
													+'<div class="form-group">'
														+'<label for="target_akhir">Target akhir</label>'
										  				+'<input type="text" class="form-control" name="target_akhir" value="'+response.data.target_akhir+'" <?php echo $disabled; ?>/>'
													+'</div>'
													+'<div class="form-group">'
														+'<label for="catatan">Catatan</label>'
										  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.data.catatan+'</textarea>'
													+'</div>'
												+'</div>'
											+'</div>'
										+'</div>'
									+'</div>'
								+'</div>'
							  +'</form>';

							indikatorSasaranModal.find('.modal-title').html('Edit Indikator Sasaran');
							indikatorSasaranModal.find('.modal-body').html(html);
							indikatorSasaranModal.find('.modal-footer').html(''
								+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
									+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
								+'</button>'
								+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
									+'data-action="update_indikator_sasaran_renstra" '
									+'data-view="indikatorSasaranRenstra"'
								+'>'
									+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
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

		let programModal = jQuery("#modal-crud-renstra");
		let kode_sasaran = jQuery(this).data('kodesasaran');

  				get_bidang_urusan().then(function(){

		  			jQuery('#wrap-loading').hide();
		  				
						let html = '<form id="form-renstra">'
										+'<input type="hidden" name="kode_sasaran" value="'+kode_sasaran+'"/>'
										+'<div class="form-group">'
									    	+'<label>Pilih Urusan</label>'
									    	+'<select class="form-control" name="id_urusan" id="urusan-teks"></select>'
									  	+'</div>'
									  	+'<div class="form-group">'
									    	+'<label>Pilih Bidang</label>'
									    	+'<select class="form-control" name="id_bidang" id="bidang-teks"></select>'
									  	+'</div>'
									  	+'<div class="form-group">'
									    	+'<label>Pilih Program</label>'
									    	+'<select class="form-control" name="id_program" id="program-teks"></select>'
									  	+'</div>'
									+'</form>';

				    programModal.find('.modal-title').html('Tambah Program');
						programModal.find('.modal-body').html(html);
						programModal.find('.modal-footer').html(''
							+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
								+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
							+'</button>'
							+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
								+'data-action="submit_program_renstra" '
								+'data-view="programRenstra"'
							+'>'
								+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
							+'</button>');

						get_urusan();
						get_bidang();
						get_program();

						programModal.modal('show');
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
							
											let html = '<form id="form-renstra">'
															+'<input type="hidden" name="id_unik" value="'+res.data.id_unik+'"/>'
															+'<input type="hidden" name="kode_sasaran" value="'+res.data.kode_sasaran+'"/>'
															+'<div class="form-group">'
														    	+'<label>Pilih Urusan</label>'
														    	+'<select class="form-control" name="id_urusan" id="urusan-teks"></select>'
														  	+'</div>'
														  	+'<div class="form-group">'
														    	+'<label>Pilih Bidang</label>'
														    	+'<select class="form-control" name="id_bidang" id="bidang-teks"></select>'
														  	+'</div>'
														  	+'<div class="form-group">'
														    	+'<label>Pilih Program</label>'
														    	+'<select class="form-control" name="id_program" id="program-teks"></select>'
														  	+'</div>'
														+'</form>';

									    programModal.find('.modal-title').html('Edit Program');
											programModal.find('.modal-body').html(html);
											programModal.find('.modal-footer').html(''
												+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
													+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
												+'</button>'
												+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
													+'data-action="update_program_renstra" '
													+'data-view="programRenstra"'
												+'>'
													+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
												+'</button>');

											get_urusan();
											get_bidang();
											get_program(false, id_program);

											programModal.modal('show');

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

			html += '<form id="form-renstra">'
						+'<input type="hidden" name="kode_program" value='+kode_program+'>'
						+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
						+'<div class="form-group">'
							+'<label for="indikator_teks">Indikator</label>'
			  			+'<textarea class="form-control" name="indikator_teks"></textarea>'
						+'</div>'
						+'<div class="form-group">'
							+'<div class="row">'
								+'<div class="col-md-6">'
									+'<div class="card">'
										+'<div class="card-header">Usulan</div>'
										+'<div class="card-body">'
											+'<div class="form-group">'
												+'<label for="satuan_usulan">Satuan</label>'
								  				+'<input type="text" class="form-control" name="satuan_usulan"/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="target_awal_usulan">Target awal</label>'
								  				+'<input type="text" class="form-control" name="target_awal_usulan"/>'
											+'</div>'
											<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+'<div class="form-group">'
												+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
								  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>_usulan"/>'
											+'</div>'
											<?php }; ?>
											+'<div class="form-group">'
												+'<label for="target_akhir_usulan">Target akhir</label>'
								  				+'<input type="text" class="form-control" name="target_akhir_usulan"/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="catatan_usulan">Catatan</label>'
								  				+'<textarea class="form-control" name="catatan_usulan"></textarea>'
											+'</div>'
										+'</div>'
									+'</div>'
								+'</div>'
								+'<div class="col-md-6">'
									+'<div class="card">'
										+'<div class="card-header">Penetapan</div>'
										+'<div class="card-body">'
											+'<div class="form-group">'
												+'<label for="satuan">Satuan</label>'
								  				+'<input type="text" class="form-control" name="satuan" <?php echo $disabled; ?> />'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="target_awal">Target awal</label>'
								  				+'<input type="text" class="form-control" name="target_awal" <?php echo $disabled; ?>/>'
											+'</div>'
											<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+'<div class="form-group">'
												+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
								  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
											+'</div>'
											<?php }; ?>
											+'<div class="form-group">'
												+'<label for="target_akhir">Target akhir</label>'
								  				+'<input type="text" class="form-control" name="target_akhir" <?php echo $disabled; ?>/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="catatan">Catatan</label>'
								  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
											+'</div>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</form>';
				
				jQuery("#modal-crud-renstra").find('.modal-title').html('Tambah Indikator');
				jQuery("#modal-crud-renstra").find('.modal-body').html(html);
				jQuery("#modal-crud-renstra").find('.modal-footer').html(''
					+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
						+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
					+'</button>'
					+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
						+'data-action="submit_indikator_program_renstra" '
						+'data-view="indikatorProgramRenstra"'
					+'>'
						+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
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

	          		let html = '<form id="form-renstra">'
									+'<input type="hidden" name="id" value="'+id+'">'
									+'<input type="hidden" name="kode_program" value="'+kode_program+'">'
									+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
									+'<div class="form-group">'
										+'<label for="indikator_teks">Indikator</label>'
					  					+'<textarea class="form-control" name="indikator_teks">'+response.data.indikator+'</textarea>'
									+'</div>'
									+'<div class="form-group">'
										+'<div class="row">'
											+'<div class="col-md-6">'
												+'<div class="card">'
													+'<div class="card-header">Usulan</div>'
													+'<div class="card-body">'
														+'<div class="form-group">'
															+'<label for="satuan_usulan">Satuan</label>'
											  				+'<input type="text" class="form-control" name="satuan_usulan" value="'+response.data.satuan_usulan+'" />'
														+'</div>'
														+'<div class="form-group">'
															+'<label for="target_awal_usulan">Target awal</label>'
											  				+'<input type="text" class="form-control" name="target_awal_usulan" value="'+response.data.target_awal_usulan+'" />'
														+'</div>'
														<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
														+'<div class="form-group">'
															+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
											  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>_usulan" value="'+response.data.target_<?php echo $i; ?>_usulan+'"/>'
														+'</div>'
														<?php }; ?>
														+'<div class="form-group">'
															+'<label for="target_akhir_usulan">Target akhir</label>'
											  				+'<input type="text" class="form-control" name="target_akhir_usulan" value="'+response.data.target_akhir_usulan+'"/>'
														+'</div>'
														+'<div class="form-group">'
															+'<label for="catatan_usulan">Catatan</label>'
											  				+'<textarea class="form-control" name="catatan_usulan">'+response.data.catatan_usulan+'</textarea>'
														+'</div>'
													+'</div>'
												+'</div>'
											+'</div>'
											+'<div class="col-md-6">'
												+'<div class="card">'
													+'<div class="card-header">Penetapan</div>'
													+'<div class="card-body">'
														+'<div class="form-group">'
															+'<label for="satuan">Satuan</label>'
											  				+'<input type="text" class="form-control" name="satuan" value="'+response.data.satuan+'" <?php echo $disabled; ?> />'
														+'</div>'
														+'<div class="form-group">'
															+'<label for="target_awal">Target awal</label>'
											  				+'<input type="text" class="form-control" name="target_awal" value="'+response.data.target_awal+'" <?php echo $disabled; ?>/>'
														+'</div>'
														<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
														+'<div class="form-group">'
															+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
											  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>" value="'+response.data.target_<?php echo $i; ?>+'" <?php echo $disabled; ?>/>'
														+'</div>'
														<?php }; ?>
														+'<div class="form-group">'
															+'<label for="target_akhir">Target akhir</label>'
											  				+'<input type="text" class="form-control" name="target_akhir" value="'+response.data.target_akhir+'" <?php echo $disabled; ?>/>'
														+'</div>'
														+'<div class="form-group">'
															+'<label for="catatan">Catatan</label>'
											  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.data.catatan+'</textarea>'
														+'</div>'
													+'</div>'
												+'</div>'
											+'</div>'
										+'</div>'
									+'</div>'
								  +'</form>';

								jQuery("#modal-crud-renstra").find('.modal-title').html('Edit Indikator Program');
								jQuery("#modal-crud-renstra").find('.modal-body').html(html);
								jQuery("#modal-crud-renstra").find('.modal-footer').html(''
									+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
										+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
									+'</button><button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
										+'data-action="update_indikator_program_renstra" '
										+'data-view="indikatorProgramRenstra"'
									+'>'
										+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
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
										+'<input type="hidden" name="kegiatan_teks" id="kegiatan_teks"/>'
									  +'<div class="form-group">'
												+'<label for="kegiatan_teks">Kegiatan</label>'
												+'<select class="form-control" id="id_kegiatan" name="id_kegiatan" onchange="pilihKegiatan(this)">';
													html+='<option value="">Pilih Kegiatan</option>';
													response.data.map(function(value, index){
														html +='<option value="'+value.id+'">'+value.kegiatan_teks+'</option>';
													})
												html+='</select>'
											+'</div>'
									+'</form>';

				    kegiatanModal.find('.modal-title').html('Tambah Kegiatan');
						kegiatanModal.find('.modal-body').html(html);
						kegiatanModal.find('.modal-footer').html(''
							+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
								+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
							+'</button>'
							+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
								+'data-action="submit_kegiatan_renstra" '
								+'data-view="kegiatanRenstra"'
							+'>'
								+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
							+'</button>');

						kegiatanModal.modal('show');

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
		  				
						let html = '<form id="form-renstra">'
										+'<input type="hidden" name="id" id="id" value="'+response.kegiatan.id+'"/>'
										+'<input type="hidden" name="id_unik" id="id_unik" value="'+response.kegiatan.id_unik+'"/>'
										+'<input type="hidden" name="id_program" id="id_program" value="'+response.kegiatan.id_program+'"/>'
										+'<input type="hidden" name="kode_program" id="kode_program" value="'+response.kegiatan.kode_program+'"/>'
										+'<input type="hidden" name="kegiatan_teks" id="kegiatan_teks"/>'
									  +'<div class="form-group">'
												+'<label for="kegiatan_teks">Kegiatan</label>'
												+'<select class="form-control" id="id_kegiatan" name="id_kegiatan" onchange="pilihKegiatan(this)">';
													html+='<option value="">Pilih Kegiatan</option>';
													response.data.map(function(value, index){
														html +='<option value="'+value.id+'">'+value.kegiatan_teks+'</option>';
													})
												html+='</select>'
											+'</div>'
									+'</form>';

				    kegiatanModal.find('.modal-title').html('Tambah Kegiatan');
						kegiatanModal.find('.modal-body').html(html);
						kegiatanModal.find('.modal-footer').html(''
							+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
								+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
							+'</button>'
							+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
								+'data-action="update_kegiatan_renstra" '
								+'data-view="kegiatanRenstra"'
							+'>'
								+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
							+'</button>');

						kegiatanModal.modal('show');

						jQuery("#id_kegiatan").val(response.kegiatan.id_giat);

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
		let html = '<form id="form-renstra">'
					+'<input type="hidden" name="id_unik" value="'+id_unik+'">'
					+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
					+'<div class="form-group">'
						+'<label for="indikator_teks">Indikator</label>'
		  				+'<textarea class="form-control" name="indikator_teks"></textarea>'
					+'</div>'
					+'<div class="form-group">'
							+'<div class="row">'
								+'<div class="col-md-6">'
									+'<div class="card">'
										+'<div class="card-header">Usulan</div>'
										+'<div class="card-body">'
											+'<div class="form-group">'
												+'<label for="satuan_usulan">Satuan</label>'
								  				+'<input type="text" class="form-control" name="satuan_usulan"/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="target_awal_usulan">Target awal</label>'
								  				+'<input type="text" class="form-control" name="target_awal_usulan"/>'
											+'</div>'
											<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+'<div class="form-group">'
												+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
								  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>_usulan"/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="pagu_<?php echo $i; ?>_usulan">Pagu <?php echo $i; ?></label>'
								  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>_usulan"/>'
											+'</div>'
											<?php }; ?>
											+'<div class="form-group">'
												+'<label for="target_akhir_usulan">Target akhir</label>'
								  				+'<input type="text" class="form-control" name="target_akhir_usulan"/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="catatan_usulan">Catatan</label>'
								  				+'<textarea class="form-control" name="catatan_usulan"></textarea>'
											+'</div>'
										+'</div>'
									+'</div>'
								+'</div>'
								+'<div class="col-md-6">'
									+'<div class="card">'
										+'<div class="card-header">Penetapan</div>'
										+'<div class="card-body">'
											+'<div class="form-group">'
												+'<label for="satuan">Satuan</label>'
								  				+'<input type="text" class="form-control" name="satuan" <?php echo $disabled; ?> />'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="target_awal">Target awal</label>'
								  				+'<input type="text" class="form-control" name="target_awal" <?php echo $disabled; ?>/>'
											+'</div>'
											<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
											+'<div class="form-group">'
												+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
								  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="pagu_<?php echo $i; ?>">Pagu <?php echo $i; ?></label>'
								  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>" <?php echo $disabled; ?>/>'
											+'</div>'
											<?php }; ?>
											+'<div class="form-group">'
												+'<label for="target_akhir">Target akhir</label>'
								  				+'<input type="text" class="form-control" name="target_akhir" <?php echo $disabled; ?>/>'
											+'</div>'
											+'<div class="form-group">'
												+'<label for="catatan">Catatan</label>'
								  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>></textarea>'
											+'</div>'
										+'</div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</form>';

			indikatorKegiatanModal.find('.modal-title').html('Tambah Indikator');
			indikatorKegiatanModal.find('.modal-body').html(html);
			indikatorKegiatanModal.find('.modal-footer').html(''
				+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
					+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
				+'</button>'
				+'<button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
					+'data-action="submit_indikator_kegiatan_renstra" '
					+'data-view="indikatorKegiatanRenstra"'
				+'>'
					+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
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

	          		let html = '<form id="form-renstra">'
									+'<input type="hidden" name="id" value="'+id+'">'
									+'<input type="hidden" name="id_unik" value="'+kode_kegiatan+'">'
									+'<input type="hidden" name="lama_pelaksanaan" value="<?php echo $lama_pelaksanaan; ?>">'
									+'<div class="form-group">'
										+'<label for="indikator_teks">Indikator</label>'
					  					+'<textarea class="form-control" name="indikator_teks">'+response.data.indikator+'</textarea>'
									+'</div>'
									+'<div class="form-group">'
										+'<div class="row">'
											+'<div class="col-md-6">'
												+'<div class="card">'
													+'<div class="card-header">Usulan</div>'
													+'<div class="card-body">'
														+'<div class="form-group">'
															+'<label for="satuan_usulan">Satuan</label>'
											  				+'<input type="text" class="form-control" name="satuan_usulan" value="'+response.data.satuan_usulan+'" />'
														+'</div>'
														+'<div class="form-group">'
															+'<label for="target_awal_usulan">Target awal</label>'
											  				+'<input type="text" class="form-control" name="target_awal_usulan" value="'+response.data.target_awal_usulan+'" />'
														+'</div>'
														<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
														+'<div class="form-group">'
															+'<label for="target_<?php echo $i; ?>_usulan">Target tahun ke-<?php echo $i; ?></label>'
											  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>_usulan" value="'+response.data.target_<?php echo $i; ?>_usulan+'"/>'
														+'</div>'
														+'<div class="form-group">'
															+'<label for="pagu_<?php echo $i; ?>_usulan">Pagu <?php echo $i; ?></label>'
											  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>_usulan" value="'+response.data.pagu_<?php echo $i; ?>_usulan+'"/>'
														+'</div>'
														<?php }; ?>
														+'<div class="form-group">'
															+'<label for="target_akhir_usulan">Target akhir</label>'
											  				+'<input type="text" class="form-control" name="target_akhir_usulan" value="'+response.data.target_akhir_usulan+'"/>'
														+'</div>'
														+'<div class="form-group">'
															+'<label for="catatan_usulan">Catatan</label>'
											  				+'<textarea class="form-control" name="catatan_usulan">'+response.data.catatan_usulan+'</textarea>'
														+'</div>'
													+'</div>'
												+'</div>'
											+'</div>'
											+'<div class="col-md-6">'
												+'<div class="card">'
													+'<div class="card-header">Penetapan</div>'
													+'<div class="card-body">'
														+'<div class="form-group">'
															+'<label for="satuan">Satuan</label>'
											  				+'<input type="text" class="form-control" name="satuan" value="'+response.data.satuan+'" <?php echo $disabled; ?> />'
														+'</div>'
														+'<div class="form-group">'
															+'<label for="target_awal">Target awal</label>'
											  				+'<input type="text" class="form-control" name="target_awal" value="'+response.data.target_awal+'" <?php echo $disabled; ?>/>'
														+'</div>'
														<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
														+'<div class="form-group">'
															+'<label for="target_<?php echo $i; ?>">Target tahun ke-<?php echo $i; ?></label>'
											  				+'<input type="text" class="form-control" name="target_<?php echo $i; ?>" value="'+response.data.target_<?php echo $i; ?>+'" <?php echo $disabled; ?>/>'
														+'</div>'
														+'<div class="form-group">'
															+'<label for="pagu_<?php echo $i; ?>">Pagu <?php echo $i; ?></label>'
											  				+'<input type="number" class="form-control" name="pagu_<?php echo $i; ?>" value="'+response.data.pagu_<?php echo $i; ?>+'"/>'
														+'</div>'
														<?php }; ?>
														+'<div class="form-group">'
															+'<label for="target_akhir">Target akhir</label>'
											  				+'<input type="text" class="form-control" name="target_akhir" value="'+response.data.target_akhir+'" <?php echo $disabled; ?>/>'
														+'</div>'
														+'<div class="form-group">'
															+'<label for="catatan">Catatan</label>'
											  				+'<textarea class="form-control" name="catatan" <?php echo $disabled; ?>>'+response.data.catatan+'</textarea>'
														+'</div>'
													+'</div>'
												+'</div>'
											+'</div>'
										+'</div>'
									+'</div>'
								  +'</form>';

								jQuery("#modal-crud-renstra").find('.modal-title').html('Edit Indikator Kegiatan');
								jQuery("#modal-crud-renstra").find('.modal-body').html(html);
								jQuery("#modal-crud-renstra").find('.modal-footer').html(''
									+'<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">'
										+'<i class="dashicons dashicons-no" style="margin-top: 3px;"></i> Tutup'
									+'</button><button type="button" class="btn btn-sm btn-success" id="btn-simpan-data-renstra-lokal" '
										+'data-action="update_indikator_kegiatan_renstra" '
										+'data-view="indikatorKegiatanRenstra"'
									+'>'
										+'<i class="dashicons dashicons-yes" style="margin-top: 3px;"></i> Simpan'
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
		get_program(jQuery(this).val());
	});

	function pilihJadwal(that){
		jQuery("#wrap-loading").show();
		if(that.value != ''){
			jQuery.ajax({
				method:'POST',
				url:ajax.url,
				dataType:'json',
				data:{
					'action': 'get_sasaran_rpjm_history',
		      'api_key': '<?php echo $api_key; ?>',
		      'id_jadwal': that.value,
		      'id_unit': '<?php echo $input['id_skpd'] ?>',
				},
				success:function(response){
					jQuery("#wrap-loading").hide();
					let html = '<option value="">Pilih Sasaran Rpjm</option>';
					response.data.map(function(value, index){
						html +='<option value="'+value.id_unik+'|'+value.id_program+'">'+value.sasaran_teks+'</option>'
					});
					jQuery("#sasaran-parent").html(html);
				}
			});
		}
	}

	function pilihSasaranParent(that){
		if(that.value !=""){
			jQuery("#tujuan_teks").val(jQuery("#sasaran-parent").find(':selected').text());
		}
	}

	function pilihKegiatan(that){
		if(that.value !=""){
			jQuery("#kegiatan_teks").val(jQuery("#id_kegiatan").find(':selected').text());
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
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
          		jQuery('#wrap-loading').hide();

          		let tujuan = ''
	          		+'<div style="margin-top:10px"><button type="button" class="btn btn-sm btn-primary mb-2 btn-tambah-tujuan"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Tujuan</button>'
	          		+'</div>'
	          		+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th style="width:5%">No.</th>'
	          					+'<th style="width:75%">Tujuan</th>'
	          					+'<th style="width:25%">Aksi</th>'
	          				+'<tr>'
	          			+'</thead>'
	          			+'<tbody>';
			          		res.data.map(function(value, index){
			          			tujuan +='<tr kodetujuan="'+value.id_unik+'">'
						          			+'<td>'+(index+1)+'.</td>'
						          			+'<td>'+value.tujuan_teks+'</td>'
						          			+'<td>'
						          					+'<a href="javascript:void(0)" data-idtujuan="'+value.id+'" data-idunik="'+value.id_unik+'" class="btn btn-sm btn-warning btn-kelola-indikator-tujuan"><i class="dashicons dashicons-menu-alt" style="margin-top: 3px;"></i></a>&nbsp;'
						          					+'<a href="javascript:void(0)" data-kodetujuan="'+value.id_unik+'" class="btn btn-sm btn-primary btn-detail-tujuan"><i class="dashicons dashicons-search"></i></a>&nbsp;'
						          					+'<a href="javascript:void(0)" data-id="'+value.id+'" class="btn btn-sm btn-success btn-edit-tujuan"><i class="dashicons dashicons-edit"></i></a>&nbsp;'
						          					+'<a href="javascript:void(0)" data-id="'+value.id+'" data-idunik="'+value.id_unik+'" class="btn btn-sm btn-danger btn-hapus-tujuan"><i class="dashicons dashicons-trash"></i></a>'
						          			+'</td>'
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
						+"<button type=\"button\" class=\"btn btn-sm btn-primary mb-2 btn-add-indikator-tujuan\" data-kodetujuan=\""+params.id_unik+"\">"
								+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 3px;\"></i> Tambah Indikator"
						+"</button>"
					+'</div>'
          			+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Tujuan</th>'
	          					+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+params.id_unik+'"]').find('td').eq(1).text()+'</th>'
	          				+'</tr>'
	          			+'</thead>'
          			+'</table>'
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th>No.</th>"
								+"<th>Indikator</th>"
								+"<th>Satuan</th>"
								+"<th>Awal</th>"
								<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
								+"<th>Tahun <?php echo $i; ?></th>"
								<?php }; ?>
								+"<th>Akhir</th>"
								+"<th>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_tujuan'>";
						response.data.map(function(value, index){
			          			html +="<tr>"
						          		+"<td>"+(index+1)+".</td>"
						          		+"<td>"+value.indikator_teks+"</td>"
						          		+"<td>"+value.satuan+"</td>"
						          		+"<td>"+value.target_awal+"</td>"
						          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
										+"<td>"+value.target_<?php echo $i; ?>+"</td>"
										<?php }; ?>
						          		+"<td>"+value.target_akhir+"</td>"
						          		+"<td>"
						          			+"<a href='#' class='btn btn-sm btn-success btn-edit-indikator-tujuan' data-id='"+value.id+"' data-idunik='"+value.id_unik+"'><i class='dashicons dashicons-edit' style='margin-top: 3px;'></i></a>&nbsp"
											+"<a href='#' class='btn btn-sm btn-danger btn-delete-indikator-tujuan' data-id='"+value.id+"' data-idunik='"+value.id_unik+"'><i class='dashicons dashicons-trash' style='margin-top: 3px;'></i></a>&nbsp;"
						          		+"</td>"
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
          				+'<div style="margin-top:10px"><button type="button" class="btn btn-sm btn-primary mb-2 btn-tambah-sasaran" data-kodetujuan="'+params.kode_tujuan+'"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Sasaran</button></div>'
          				+'<table class="table">'
          					+'<thead>'
	          					+'<tr>'
	          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
	          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+params.kode_tujuan+'"]').find('td').eq(1).text()+'</th>'
	          					+'</tr>'
          					+'</thead>'
          				+'</table>'
          				
          				+'<table class="table">'
          					+'<thead>'
          						+'<tr>'
          							+'<th style="width:5%">No.</th>'
          							+'<th style="width:75%">Sasaran</th>'
          							+'<th style="width:25%">Aksi</th>'
          						+'<tr>'
          					+'</thead>'
          					+'<tbody>';

          						response.data.map(function(value, index){
          							sasaran +='<tr kodesasaran="'+value.id_unik+'">'
			          							+'<td>'+(index+1)+'.</td>'
			          							+'<td>'+value.sasaran_teks+'</td>'
			          							+'<td>'
			          								+'<a href="javascript:void(0)" data-idsasaran="'+value.id+'" data-kodesasaran="'+value.id_unik+'" class="btn btn-sm btn-warning btn-kelola-indikator-sasaran"><i class="dashicons dashicons-menu-alt" style="margin-top: 3px;"></i></a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-kodesasaran="'+value.id_unik+'" class="btn btn-sm btn-primary btn-detail-sasaran"><i class="dashicons dashicons-search" style="margin-top: 3px;"></i></a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-idsasaran="'+value.id+'" class="btn btn-sm btn-success btn-edit-sasaran"><i class="dashicons dashicons-edit" style="margin-top: 3px;"></i></a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-idsasaran="'+value.id+'" data-kodesasaran="'+value.id_unik+'" data-kodetujuan="'+value.kode_tujuan+'" class="btn btn-sm btn-danger btn-hapus-sasaran"><i class="dashicons dashicons-trash" style="margin-top: 3px;"></i></a>'
			          							+'</td>'
			          						+'</tr>';
          						})
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
										+"<button type=\"button\" class=\"btn btn-sm btn-primary mb-2 btn-add-indikator-sasaran\" data-kodesasaran=\""+params.id_unik+"\">"
												+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 3px;\"></i> Tambah Indikator"
											+"</button>"
									+'</div>'
				          			+'<table class="table">'
					          			+'<thead>'
					          				+'<tr>'
					          					+'<th class="text-center" style="width: 160px;">Sasaran</th>'
					          					+'<th>'+jQuery('#nav-sasaran tr[kodesasaran="'+params.id_unik+'"]').find('td').eq(1).text()+'</th>'
					          				+'</tr>'
					          			+'</thead>'
				          			+'</table>'
									+"<table class='table'>"
										+"<thead>"
											+"<tr>"
												+"<th>No.</th>"
												+"<th>Indikator</th>"
												+"<th>Satuan</th>"
												+"<th>Awal</th>"
												<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
												+"<th>Tahun <?php echo $i; ?></th>"
												<?php }; ?>
												+"<th>Akhir</th>"
												+"<th>Aksi</th>"
											+"</tr>"
										+"</thead>"
										+"<tbody id='indikator_sasaran'>";
										response.data.map(function(value, index){
							          			html +="<tr>"
										          		+"<td>"+(index+1)+".</td>"
										          		+"<td>"+value.indikator_teks+"</td>"
										          		+"<td>"+value.satuan+"</td>"
										          		+"<td>"+value.target_awal+"</td>"
										          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
														+"<td>"+value.target_<?php echo $i; ?>+"</td>"
														<?php }; ?>
										          		+"<td>"+value.target_akhir+"</td>"
										          		+"<td>"
										          			+"<a href='#' class='btn btn-sm btn-success btn-edit-indikator-sasaran' data-id='"+value.id+"' data-idunik='"+value.id_unik+"' ><i class='dashicons dashicons-edit' style='margin-top: 3px;'></i></a>&nbsp"
															+"<a href='#' class='btn btn-sm btn-danger btn-delete-indikator-sasaran' data-id='"+value.id+"' data-idunik='"+value.id_unik+"' ><i class='dashicons dashicons-trash' style='margin-top: 3px;'></i></a>&nbsp;"
										          		+"</td>"
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
          				+'<div style="margin-top:10px"><button type="button" class="btn btn-sm btn-primary mb-2 btn-tambah-program" data-kodesasaran="'+params.kode_sasaran+'"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Program</button></div>'
          				+'<table class="table">'
          					+'<thead>'
	          					+'<tr>'
	          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
	          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text()+'</th>'
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
          							+'<th style="width:5%">No.</th>'
          							+'<th style="width:75%">Program</th>'
          							+'<th style="width:25%">Aksi</th>'
          						+'<tr>'
          					+'</thead>'
          					+'<tbody>';

          						response.data.map(function(value, index){
          							program +='<tr kodeprogram="'+value.id_unik+'">'
			          							+'<td>'+(index+1)+'.</td>'
			          							+'<td>'+value.nama_program+'</td>'
			          							+'<td>'
			          								+'<a href="javascript:void(0)" data-kodeprogram="'+value.id_unik+'" class="btn btn-sm btn-warning btn-kelola-indikator-program"><i class="dashicons dashicons-menu-alt" style="margin-top: 3px;"></i></a>&nbsp;'	
			          								+'<a href="javascript:void(0)" data-kodeprogram="'+value.id_unik+'" data-idprogram="'+value.id_program+'" class="btn btn-sm btn-primary btn-detail-program"><i class="dashicons dashicons-search" style="margin-top: 3px;"></i></a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-id="'+value.id+'" data-kodeprogram="'+value.id_unik+'" class="btn btn-sm btn-success btn-edit-program"><i class="dashicons dashicons-edit" style="margin-top: 3px;"></i></a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-id="'+value.id+'" data-kodeprogram="'+value.id_unik+'" data-kodesasaran="'+value.kode_sasaran+'" class="btn btn-sm btn-danger btn-hapus-program"><i class="dashicons dashicons-trash" style="margin-top: 3px;"></i></a></td>'
			          						+'</tr>';
          						})

          					program +='<tbody>'
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
          		
          		let html=""
							+'<div style="margin-top:10px">'
								+"<button type=\"button\" class=\"btn btn-sm btn-primary mb-2 btn-add-indikator-program\" data-kodeprogram=\""+params.kode_program+"\">"
										+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 3px;\"></i> Tambah Indikator"
								+"</button>"
							+'</div>'
		          			+'<table class="table">'
			          			+'<thead>'
			          				+'<tr>'
			          					+'<th class="text-center" style="width: 160px;">Program</th>'
			          					+'<th>'+jQuery('#nav-program tr[kodeprogram="'+params.kode_program+'"]').find('td').eq(1).text()+'</th>'
			          				+'</tr>'
			          			+'</thead>'
		          			+'</table>'

										+"<table class='table'>"
											+"<thead>"
												+"<tr>"
													+"<th>No.</th>"
													+"<th>Indikator</th>"
													+"<th>Satuan</th>"
													+"<th>Awal</th>"
													<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
													+"<th>Tahun <?php echo $i; ?></th>"
													<?php }; ?>
													+"<th>Akhir</th>"
													+"<th>Aksi</th>"
												+"</tr>"
											+"</thead>"
											+"<tbody id='indikator_program'>";
											response.data.map(function(value, index){
								          			html +="<tr>"
											          		+"<td>"+(index+1)+".</td>"
											          		+"<td>"+value.indikator+"</td>"
											          		+"<td>"+value.satuan+"</td>"
											          		+"<td>"+value.target_awal+"</td>"
											          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
															+"<td>"+value.target_<?php echo $i; ?>+"</td>"
															<?php }; ?>
											          		+"<td>"+value.target_akhir+"</td>"
											          		+"<td>"
											          			+"<a href='#' class='btn btn-sm btn-success btn-edit-indikator-program' data-kodeprogram='"+value.id_unik+"' data-id='"+value.id+"'><i class='dashicons dashicons-edit' style='margin-top: 3px;'></i></a>&nbsp"
																+"<a href='#' class='btn btn-sm btn-danger btn-delete-indikator-program' data-kodeprogram='"+value.id_unik+"' data-id='"+value.id+"'><i class='dashicons dashicons-trash' style='margin-top: 3px;'></i></a>&nbsp;"
											          		+"</td>"
											          	+"</tr>";
								          		});
							          	html+='</tbody></table>';

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
          				+'<div style="margin-top:10px"><button type="button" class="btn btn-sm btn-primary mb-2 btn-tambah-kegiatan" data-kodeprogram="'+params.kode_program+'" data-idprogram="'+params.id_program+'"><i class="dashicons dashicons-plus" style="margin-top: 3px;"></i> Tambah Kegiatan</button></div>'
          				+'<table class="table">'
          					+'<thead>'
	          					+'<tr>'
	          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
	          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text()+'</th>'
	          					+'</tr>'

	          					+'<tr>'
	          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
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
          							+'<th style="width:5%">No.</th>'
          							+'<th style="width:75%">Kegiatan</th>'
          							+'<th style="width:25%">Aksi</th>'
          						+'<tr>'
          					+'</thead>'
          					+'<tbody>';

          						response.data.map(function(value, index){
          							kegiatan +='<tr kodekegiatan="'+value.id_unik+'">'
			          							+'<td>'+(index+1)+'.</td>'
			          							+'<td>'+value.nama_giat+'</td>'
			          							+'<td>'
			          								+'<a href="javascript:void(0)" data-kodekegiatan="'+value.id_unik+'" class="btn btn-sm btn-warning btn-kelola-indikator-kegiatan"><i class="dashicons dashicons-menu-alt" style="margin-top: 3px;"></i></a>&nbsp;'	
			          								+'<a href="javascript:void(0)" data-id="'+value.id+'" data-kodekegiatan="'+value.id_unik+'" data-idprogram="'+value.id_program+'" class="btn btn-sm btn-success btn-edit-kegiatan"><i class="dashicons dashicons-edit" style="margin-top: 3px;"></i></a>&nbsp;'
			          								+'<a href="javascript:void(0)" data-id="'+value.id+'" data-kodekegiatan="'+value.id_unik+'" data-kodeprogram="'+value.kode_program+'" data-idprogram="'+value.id_program+'" class="btn btn-sm btn-danger btn-hapus-kegiatan"><i class="dashicons dashicons-trash" style="margin-top: 3px;"></i></a></td>'
			          						+'</tr>';
          						})

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

          		jQuery('#wrap-loading').hide();
          		
          		let html=""
									+'<div style="margin-top:10px">'
										+"<button type=\"button\" class=\"btn btn-sm btn-primary mb-2 btn-add-indikator-kegiatan\" data-kodekegiatan=\""+params.id_unik+"\">"
												+"<i class=\"dashicons dashicons-plus\" style=\"margin-top: 3px;\"></i> Tambah Indikator"
										+"</button>"
									+'</div>'
				          			+'<table class="table">'
					          			+'<thead>'
					          				+'<tr>'
					          					+'<th class="text-center" style="width: 160px;">Kegiatan</th>'
					          					+'<th>'+jQuery('#nav-kegiatan tr[kodekegiatan="'+params.id_unik+'"]').find('td').eq(1).text()+'</th>'
					          				+'</tr>'
					          			+'</thead>'
				          			+'</table>'

									+"<table class='table'>"
										+"<thead>"
											+"<tr>"
												+"<th>No.</th>"
												+"<th>Indikator</th>"
												+"<th>Satuan</th>"
												+"<th>Awal</th>"
												<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
												+"<th>Tahun <?php echo $i; ?></th>"
												<?php }; ?>
												+"<th>Akhir</th>"
												+"<th>Aksi</th>"
											+"</tr>"
										+"</thead>"
										+"<tbody id='indikator_kegiatan'>";
										response.data.map(function(value, index){
							          html +="<tr>"
										          		+"<td>"+(index+1)+".</td>"
										          		+"<td>"+value.indikator+"</td>"
										          		+"<td>"+value.satuan+"</td>"
										          		+"<td>"+value.target_awal+"</td>"
										          		<?php for($i=1; $i<=$lama_pelaksanaan; $i++){ ?>
														+"<td>"+value.target_<?php echo $i; ?>+"</td>"
														<?php }; ?>
										          		+"<td>"+value.target_akhir+"</td>"
										          		+"<td>"
										          			+"<a href='#' class='btn btn-sm btn-success btn-edit-indikator-kegiatan' data-kodekegiatan='"+value.id_unik+"' data-id='"+value.id+"'><i class='dashicons dashicons-edit' style='margin-top: 3px;'></i></a>&nbsp"
															+"<a href='#' class='btn btn-sm btn-danger btn-delete-indikator-kegiatan' data-kodekegiatan='"+value.id_unik+"' data-id='"+value.id+"'><i class='dashicons dashicons-trash' style='margin-top: 3px;'></i></a>&nbsp;"
										          		+"</td>"
										          	+"</tr>";
							      });
						          html+='</tbody></table>';

						jQuery("#modal-indikator-renstra").find('.modal-title').html('Indikator kegiatan');
						jQuery("#modal-indikator-renstra").find('.modal-body').html(html);
						jQuery("#modal-indikator-renstra").find('.modal-dialog').css('maxWidth','1250px');
						jQuery("#modal-indikator-renstra").find('.modal-dialog').css('width','100%');
						jQuery("#modal-indikator-renstra").modal('show');
			}
		});
	}

	function get_urusan() {
		var html = '<option value="">Pilih Urusan</option>';
		for(var nm_urusan in all_program){
			html += '<option>'+nm_urusan+'</option>';
		}
		jQuery('#urusan-teks').html(html);
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
		jQuery('#bidang-teks').html(html);
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
		jQuery('#program-teks').html(html);
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
								if(!all_skpd[b.nama_urusan]){
									all_skpd[b.nama_urusan] = {};
								}
								if(!all_skpd[b.nama_urusan][b.nama_bidang_urusan]){
									all_skpd[b.nama_urusan][b.nama_bidang_urusan] = {};
								}
								if(!all_skpd[b.nama_urusan][b.nama_bidang_urusan][b.nama_skpd]){
									all_skpd[b.nama_urusan][b.nama_bidang_urusan][b.nama_skpd] = b;
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
</script>