<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
global $wpdb;
$input = shortcode_atts(array(
	'id_skpd' => '',
	'id_jadwal' => '',
), $atts);
// die(print_r($input['id_jadwal']));
if (empty($input['id_skpd'])) {
	die('<h1>SKPD tidak ditemukan!</h1>');
}

$api_key = get_option('_crb_api_key_extension');
$tahun_anggaran_sipd = get_option(WPSIPD_TAHUN_ANGGARAN);

$data_jadwal = $wpdb->get_row(
	$wpdb->prepare("
        SELECT *
        FROM data_jadwal_lokal
        WHERE id_jadwal_lokal = %d
		  AND status = %d
          AND id_tipe = %d
    ",$input['id_jadwal'], 0, 15),
	ARRAY_A
);
$data_jadwal_relasi = $wpdb->get_row(
	$wpdb->prepare("
		SELECT *
		FROM data_jadwal_lokal
		WHERE id_jadwal_lokal = %d
		  AND status = %d
	", $data_jadwal['relasi_perencanaan'], 0),
	ARRAY_A
);
// die(print_r($data_jadwal_relasi));


if (empty($data_jadwal) || empty($data_jadwal_relasi)) {
	die("<h1>Jadwal Tidak Tersedia</h1>");
}
$nama_jadwal = $data_jadwal['nama'];
$tahun_awal_jadwal = $data_jadwal['tahun_anggaran'];
$tahun_akhir_jadwal = $data_jadwal['tahun_akhir_anggaran'];

$lama_pelaksanaan = $data_jadwal_relasi['lama_pelaksanaan'];
$jenis_jadwal_relasi = $data_jadwal_relasi['jenis_jadwal'];
$awal_rpjmd = $data_jadwal_relasi['tahun_anggaran'];
$tahun_anggaran_renstra = array();
$akhir_rpjmd = $data_jadwal_relasi['tahun_akhir_anggaran'];
for ($i = 0; $i < $lama_pelaksanaan; $i++) {
	$tahun_anggaran_renstra[$i + 1] = $awal_rpjmd + $i;
}

function button_edit_monev($class = false)
{
	$ret = ' <span style="display: none;" data-id="' . $class . '" class="edit-monev"><i class="dashicons dashicons-edit"></i></span>';
	return $ret;
}

$rumus_indikator_db = $wpdb->get_results("SELECT * FROM data_rumus_indikator WHERE active=1", ARRAY_A);
$rumus_indikator = '';
$keterangan_indikator_html = '';
foreach ($rumus_indikator_db as $k => $v) {
	$rumus_indikator .= '<option value="' . $v['id'] . '">' . $v['rumus'] . '</option>';
	$keterangan_indikator_html .= '<li data-id="' . $v['id'] . '" style="display: none;">' . $v['keterangan'] . '</li>';
}
$sql = $wpdb->prepare("
	SELECT 
		* 
	FROM data_unit 
	WHERE id_skpd = %d
	  AND tahun_anggaran=%d
	  AND active=1
	ORDER BY id_skpd ASC
", $input['id_skpd'], $tahun_anggaran_sipd);
$unit = $wpdb->get_results($sql, ARRAY_A);

$nama_pemda = get_option('_crb_daerah');

$bulan = date('m');
$body_monev = '';
$data_all = array(
	'data' => array(),
	'pagu_1' => 0,
	'pagu_2' => 0,
	'pagu_3' => 0,
	'pagu_4' => 0,
	'pagu_5' => 0,
	'realisasi_pagu_1' => 0,
	'realisasi_pagu_2' => 0,
	'realisasi_pagu_3' => 0,
	'realisasi_pagu_4' => 0,
	'realisasi_pagu_5' => 0,
	'isRenstraLokal' => false
);

$tujuan = $wpdb->get_results(
	$wpdb->prepare("
		SELECT 
			* 
		FROM data_renstra_tujuan
		WHERE id_unit=%d
		  AND id_jadwal=%d 
		  AND active=1
		ORDER BY id
	", $input['id_skpd'], $input['id_jadwal']),
	ARRAY_A
);

if (!empty($tujuan)) {
	foreach ($tujuan as $t => $tujuan_value) {
		$tujuan_key = $tujuan_value['id_bidang_urusan'] . "-" . $tujuan_value['id_unik'];
		if (empty($data_all['data'][$tujuan_key])) {
			$status_rpjmd = '';
			if (!empty($tujuan_value['kode_sasaran_rpjm'])) {
				$cek_status_rpjmd = $wpdb->get_results(
					$wpdb->prepare("
						SELECT 
							id 
						FROM data_rpjmd_sasaran 
						WHERE active=1 
						  AND id_unik=%s 
					", $tujuan_value['kode_sasaran_rpjm']),
					ARRAY_A
				);

				if (!empty($cek_status_rpjmd)) {
					$status_rpjmd = 'TERKONEKSI';
				}
			}

			$nama = explode("||", $tujuan_value['tujuan_teks']);
			$nama_bidang_urusan = explode("||", $tujuan_value['nama_bidang_urusan']);
			$data_all['data'][$tujuan_key] = array(
				'id_unit' => $tujuan_value['id_unit'],
				'status_rpjmd' => $status_rpjmd,
				'status' => '1',
				'nama' => $tujuan_value['tujuan_teks'],
				'nama_teks' => $nama[0],
				'id_unik' => $tujuan_value['id_unik'],
				'kode_sasaran_rpjm' => $tujuan_value['kode_sasaran_rpjm'],
				'kode_tujuan' => $tujuan_value['id_unik'],
				'urut_tujuan' => $tujuan_value['urut_tujuan'],
				'id_bidang_urusan' => $tujuan_value['id_bidang_urusan'],
				'kode_bidang_urusan' => $tujuan_value['kode_bidang_urusan'],
				'nama_bidang_urusan' => $tujuan_value['nama_bidang_urusan'],
				'nama_bidang_urusan_teks' => $nama_bidang_urusan[0],
				'pagu_1' => 0,
				'pagu_2' => 0,
				'pagu_3' => 0,
				'pagu_4' => 0,
				'pagu_5' => 0,
				'realisasi_pagu_1' => 0,
				'realisasi_pagu_2' => 0,
				'realisasi_pagu_3' => 0,
				'realisasi_pagu_4' => 0,
				'realisasi_pagu_5' => 0,
				'indikator' => array(),
				'data' => array()
			);

			$sasaran = $wpdb->get_results(
				$wpdb->prepare("
					SELECT 
						* 
					FROM data_renstra_sasaran 
					WHERE active=1 
					  AND id_jadwal=%d
					  AND id_unit=%d 
					  AND kode_tujuan=%s 
					  AND id_bidang_urusan=%d 
					  AND urut_tujuan=%d 
					ORDER BY id
				", $input['id_jadwal'], $input['id_skpd'], $tujuan_value['id_unik'], $tujuan_value['id_bidang_urusan'], $tujuan_value['urut_tujuan']),
				ARRAY_A
			);

			if (!empty($sasaran)) {
				foreach ($sasaran as $s => $sasaran_value) {
					if ($data_all['isRenstraLokal']) {
						$sasaran_key = $sasaran_value['id_unik'];
						$nama[0] = $sasaran_value['sasaran_teks'];
						$nama[2] = $sasaran_value['id_unik'];
					} else {
						$nama = explode("||", $sasaran_value['sasaran_teks']);
						if (isset($nama[2])) {
							$sasaran_key = $nama[2];
						} else {
							$sasaran_key = null;
						}
					}

					if (empty($data_all['data'][$tujuan_key]['data'][$sasaran_key])) {
						$nama_bidang_urusan = explode("||", $sasaran_value['nama_bidang_urusan']);
						$data_all['data'][$tujuan_key]['data'][$sasaran_key] = array(
							'id' => $sasaran_value['id'],
							'id_unit' => $sasaran_value['id_unit'],
							'status' => '1',
							'nama' => $sasaran_value['sasaran_teks'],
							'nama_teks' => $nama[0],
							'id_unik' => $sasaran_value['id_unik'],
							'kode_sasaran' => $sasaran_key,
							'urut_sasaran' => $sasaran_value['urut_sasaran'],
							'kode_tujuan' => $sasaran_value['kode_tujuan'],
							'urut_tujuan' => $sasaran_value['urut_tujuan'],
							'id_bidang_urusan' => $sasaran_value['id_bidang_urusan'],
							'kode_bidang_urusan' => $sasaran_value['kode_bidang_urusan'],
							'nama_bidang_urusan' => $sasaran_value['nama_bidang_urusan'],
							'nama_bidang_urusan_teks' => $nama_bidang_urusan[0],
							'id_misi' => $sasaran_value['id_misi'],
							'id_visi' => $sasaran_value['id_visi'],
							'pagu' => 0,
							'realisasi' => 0,
							'capaian' => 0,
							'pagu_1' => 0,
							'pagu_2' => 0,
							'pagu_3' => 0,
							'pagu_4' => 0,
							'pagu_5' => 0,
							'realisasi_pagu_1' => 0,
							'realisasi_pagu_2' => 0,
							'realisasi_pagu_3' => 0,
							'realisasi_pagu_4' => 0,
							'realisasi_pagu_5' => 0,
							'indikator' => array(),
							'data' => array()
						);

						$program = $wpdb->get_results(
							$wpdb->prepare("
								SELECT * 
								FROM data_renstra_program 
								WHERE active=1 
								  AND id_jadwal=%d
								  AND id_unit=%d 
								  AND kode_sasaran=%s
								ORDER BY id
								", $input['id_jadwal'], $input['id_skpd'], $sasaran_value['id_unik']),
							ARRAY_A
						);

						if (!empty($program)) {
							foreach ($program as $p => $p_value) {

								if (empty($data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']])) {
									$nama = explode("||", $p_value['nama_program']);
									$nama_bidang_urusan = explode("||", $p_value['nama_bidang_urusan']);
									$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']] = array(
										'id' => $p_value['id'],
										'id_unit' => $p_value['id_unit'],
										'status' => '1',
										'nama' => $p_value['nama_program'],
										'nama_teks' => $nama[0],
										'id_unik' => $p_value['id_unik'],
										'kode_program' => $p_value['kode_program'],
										'kode_sasaran' => $p_value['kode_sasaran'],
										'urut_sasaran' => $p_value['urut_sasaran'],
										'kode_tujuan' => $p_value['kode_tujuan'],
										'urut_tujuan' => $p_value['urut_tujuan'],
										'id_bidang_urusan' => $p_value['id_bidang_urusan'],
										'kode_bidang_urusan' => $p_value['kode_bidang_urusan'],
										'nama_bidang_urusan' => $p_value['nama_bidang_urusan'],
										'nama_bidang_urusan_teks' => $nama_bidang_urusan[0],
										'id_misi' => $p_value['id_misi'],
										'id_visi' => $p_value['id_visi'],
										'pagu_1' => 0,
										'pagu_2' => 0,
										'pagu_3' => 0,
										'pagu_4' => 0,
										'pagu_5' => 0,
										'realisasi_pagu_1' => 0,
										'realisasi_pagu_2' => 0,
										'realisasi_pagu_3' => 0,
										'realisasi_pagu_4' => 0,
										'realisasi_pagu_5' => 0,
										'indikator' => array(),
										'data' => array()
									);

									$kegiatan = $wpdb->get_results(
										$wpdb->prepare("
											SELECT * 
											FROM data_renstra_kegiatan 
											WHERE active=1 
											  AND id_jadwal=%d
											  AND id_unit=%d 
											  AND (
													kode_program=%s
													OR kode_unik_program=%s
												  )
											ORDER BY id
										", $input['id_jadwal'], $input['id_skpd'], $p_value['id_unik'], $p_value['id_unik']),
										ARRAY_A
									);

									if (!empty($kegiatan)) {
										foreach ($kegiatan as $k => $k_value) {
											if (empty($data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']])) {
												$nama = explode("||", $k_value['nama_giat']);
												$nama_bidang_urusan = explode("||", $k_value['nama_bidang_urusan']);
												$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']] = array(
													'id' => $k_value['id'],
													'id_unit' => $k_value['id_unit'],
													'status' => '1',
													'nama' => $k_value['nama_giat'],
													'nama_teks' => $nama[0],
													'id_giat' => $k_value['id_giat'],
													'kode_giat' => $k_value['kode_giat'],
													'kode_program' => $k_value['kode_program'],
													'kode_sasaran' => $k_value['kode_sasaran'],
													'urut_sasaran' => $k_value['urut_sasaran'],
													'kode_tujuan' => $k_value['kode_tujuan'],
													'urut_tujuan' => $k_value['urut_tujuan'],
													'id_bidang_urusan' => $k_value['id_bidang_urusan'],
													'kode_bidang_urusan' => $k_value['kode_bidang_urusan'],
													'nama_bidang_urusan' => $k_value['nama_bidang_urusan'],
													'nama_bidang_urusan_teks' => $nama_bidang_urusan[0],
													'id_misi' => $k_value['id_misi'],
													'id_visi' => $k_value['id_visi'],
													'pagu_1' => 0,
													'pagu_2' => 0,
													'pagu_3' => 0,
													'pagu_4' => 0,
													'pagu_5' => 0,
													'realisasi_pagu_1' => 0,
													'realisasi_pagu_2' => 0,
													'realisasi_pagu_3' => 0,
													'realisasi_pagu_4' => 0,
													'realisasi_pagu_5' => 0,
													'indikator' => array(),
													'data' => array()
												);

												$sub_kegiatan = $wpdb->get_results(
													$wpdb->prepare("
														SELECT * 
														FROM data_renstra_sub_kegiatan 
														WHERE id_unit=%d 
														  AND id_jadwal=%d
														  AND kode_kegiatan=%s
														  AND active=1
														ORDER BY id
													", $input['id_skpd'], $input['id_jadwal'], $k_value['id_unik']),
													ARRAY_A
												);

												if (!empty($sub_kegiatan)) {
													foreach ($sub_kegiatan as $k => $sk_value) {

														if (empty($data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['data'][$sk_value['kode_sub_giat']])) {

															$nama = explode("||", $sk_value['nama_sub_giat']);
															$nama_bidang_urusan = explode("||", $sk_value['nama_bidang_urusan']);
															$pagu_1 = !empty($sk_value['pagu_1']) ? $sk_value['pagu_1'] : 0;
															$pagu_2 = !empty($sk_value['pagu_2']) ? $sk_value['pagu_2'] : 0;
															$pagu_3 = !empty($sk_value['pagu_3']) ? $sk_value['pagu_3'] : 0;
															$pagu_4 = !empty($sk_value['pagu_4']) ? $sk_value['pagu_4'] : 0;
															$pagu_5 = !empty($sk_value['pagu_5']) ? $sk_value['pagu_5'] : 0;

															$realisasi_pagu_1 = !empty($sk_value['realisasi_pagu_1']) ? $sk_value['realisasi_pagu_1'] : 0;
															$realisasi_pagu_2 = !empty($sk_value['realisasi_pagu_2']) ? $sk_value['realisasi_pagu_2'] : 0;
															$realisasi_pagu_3 = !empty($sk_value['realisasi_pagu_3']) ? $sk_value['realisasi_pagu_3'] : 0;
															$realisasi_pagu_4 = !empty($sk_value['realisasi_pagu_4']) ? $sk_value['realisasi_pagu_4'] : 0;
															$realisasi_pagu_5 = !empty($sk_value['realisasi_pagu_5']) ? $sk_value['realisasi_pagu_5'] : 0;

															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['data'][$sk_value['kode_sub_giat']] = array(
																'id_unit' => $sk_value['id_unit'],
																'status' => "1",
																'nama' => $sk_value['nama_sub_giat'],
																'nama_teks' => $nama[0],
																'id_sub_giat' => $sk_value['id_sub_giat'],
																'kode_sub_giat' => $sk_value['kode_sub_giat'],
																'kode_giat' => $sk_value['kode_giat'],
																'kode_program' => $sk_value['kode_program'],
																'kode_sasaran' => $sk_value['kode_sasaran'],
																'urut_sasaran' => $sk_value['urut_sasaran'],
																'kode_tujuan' => $sk_value['kode_tujuan'],
																'urut_tujuan' => $sk_value['urut_tujuan'],
																'id_bidang_urusan' => $sk_value['id_bidang_urusan'],
																'kode_bidang_urusan' => $sk_value['kode_bidang_urusan'],
																'nama_bidang_urusan' => $sk_value['nama_bidang_urusan'],
																'nama_bidang_urusan_teks' => $nama_bidang_urusan[0],
																'id_misi' => $sk_value['id_misi'],
																'id_visi' => $sk_value['id_visi'],
																'pagu_1' => $pagu_1,
																'pagu_2' => $pagu_2,
																'pagu_3' => $pagu_3,
																'pagu_4' => $pagu_4,
																'pagu_5' => $pagu_5,
																'realisasi_pagu_1' => $realisasi_pagu_1,
																'realisasi_pagu_2' => $realisasi_pagu_2,
																'realisasi_pagu_3' => $realisasi_pagu_3,
																'realisasi_pagu_4' => $realisasi_pagu_4,
																'realisasi_pagu_5' => $realisasi_pagu_5,
																'indikator' => array()
															);
															$pagu_all = $pagu_1 + $pagu_2 + $pagu_3 + $pagu_4 + $pagu_5;
															$realisasi_all = $realisasi_pagu_1 + $realisasi_pagu_2 + $realisasi_pagu_3 + $realisasi_pagu_4 + $realisasi_pagu_5;

															$data_all['pagu_1'] += $pagu_1;
															$data_all['pagu_2'] += $pagu_2;
															$data_all['pagu_3'] += $pagu_3;
															$data_all['pagu_4'] += $pagu_4;
															$data_all['pagu_5'] += $pagu_5;
															$data_all['realisasi_pagu_1'] += $realisasi_pagu_1;
															$data_all['realisasi_pagu_2'] += $realisasi_pagu_2;
															$data_all['realisasi_pagu_3'] += $realisasi_pagu_3;
															$data_all['realisasi_pagu_4'] += $realisasi_pagu_4;
															$data_all['realisasi_pagu_5'] += $realisasi_pagu_5;

															$data_all['data'][$tujuan_key]['pagu_1'] += $pagu_1;
															$data_all['data'][$tujuan_key]['pagu_2'] += $pagu_2;
															$data_all['data'][$tujuan_key]['pagu_3'] += $pagu_3;
															$data_all['data'][$tujuan_key]['pagu_4'] += $pagu_4;
															$data_all['data'][$tujuan_key]['pagu_5'] += $pagu_5;
															$data_all['data'][$tujuan_key]['realisasi_pagu_1'] += $realisasi_pagu_1;
															$data_all['data'][$tujuan_key]['realisasi_pagu_2'] += $realisasi_pagu_2;
															$data_all['data'][$tujuan_key]['realisasi_pagu_3'] += $realisasi_pagu_3;
															$data_all['data'][$tujuan_key]['realisasi_pagu_4'] += $realisasi_pagu_4;
															$data_all['data'][$tujuan_key]['realisasi_pagu_5'] += $realisasi_pagu_5;

															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['pagu'] += $pagu_all;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['realisasi'] += $realisasi_all;

															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['pagu_1'] += $pagu_1;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['pagu_2'] += $pagu_2;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['pagu_3'] += $pagu_3;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['pagu_4'] += $pagu_4;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['pagu_5'] += $pagu_5;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['realisasi_pagu_1'] += $realisasi_pagu_1;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['realisasi_pagu_2'] += $realisasi_pagu_2;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['realisasi_pagu_3'] += $realisasi_pagu_3;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['realisasi_pagu_4'] += $realisasi_pagu_4;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['realisasi_pagu_5'] += $realisasi_pagu_5;

															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['pagu_1'] += $pagu_1;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['pagu_2'] += $pagu_2;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['pagu_3'] += $pagu_3;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['pagu_4'] += $pagu_4;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['pagu_5'] += $pagu_5;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['realisasi_pagu_1'] += $realisasi_pagu_1;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['realisasi_pagu_2'] += $realisasi_pagu_2;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['realisasi_pagu_3'] += $realisasi_pagu_3;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['realisasi_pagu_4'] += $realisasi_pagu_4;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['realisasi_pagu_5'] += $realisasi_pagu_5;

															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['pagu_1'] += $pagu_1;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['pagu_2'] += $pagu_2;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['pagu_3'] += $pagu_3;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['pagu_4'] += $pagu_4;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['pagu_5'] += $pagu_5;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['realisasi_pagu_1'] += $realisasi_pagu_1;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['realisasi_pagu_2'] += $realisasi_pagu_2;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['realisasi_pagu_3'] += $realisasi_pagu_3;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['realisasi_pagu_4'] += $realisasi_pagu_4;
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['realisasi_pagu_5'] += $realisasi_pagu_5;
														}


														if (!empty($sk_value['id_unik_indikator']) && empty($data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['data'][$sk_value['kode_sub_giat']]['indikator'][$sk_value['id_unik_indikator']])) {

															$keterangan = array();
															if (!empty($sk_value['keterangan_1'])) {
																$keterangan[] = $sk_value['keterangan_1'];
															}
															if (!empty($sk_value['keterangan_2'])) {
																$keterangan[] = $sk_value['keterangan_2'];
															}
															if (!empty($sk_value['keterangan_3'])) {
																$keterangan[] = $sk_value['keterangan_3'];
															}
															if (!empty($sk_value['keterangan_4'])) {
																$keterangan[] = $sk_value['keterangan_4'];
															}
															if (!empty($sk_value['keterangan_5'])) {
																$keterangan[] = $sk_value['keterangan_5'];
															}
															$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['data'][$sk_value['kode_sub_giat']]['indikator'][$sk_value['id_unik_indikator']] = array(
																'id' => $sk_value['id'],
																'id_unik_indikator' => $sk_value['id_unik_indikator'],
																'indikator' => !empty($sk_value['indikator']) ? $sk_value['indikator'] : '-',
																'satuan' => !empty($sk_value['satuan']) ? $sk_value['satuan'] : "",
																'target_1' => !empty($sk_value['target_1']) ? $sk_value['target_1'] : "",
																'target_2' => !empty($sk_value['target_2']) ? $sk_value['target_2'] : "",
																'target_3' => !empty($sk_value['target_3']) ? $sk_value['target_3'] : "",
																'target_4' => !empty($sk_value['target_4']) ? $sk_value['target_4'] : "",
																'target_5' => !empty($sk_value['target_5']) ? $sk_value['target_5'] : "",
																'realisasi_target_1' => !empty($sk_value['realisasi_target_1']) ? $sk_value['realisasi_target_1'] : "",
																'realisasi_target_2' => !empty($sk_value['realisasi_target_2']) ? $sk_value['realisasi_target_2'] : "",
																'realisasi_target_3' => !empty($sk_value['realisasi_target_3']) ? $sk_value['realisasi_target_3'] : "",
																'realisasi_target_4' => !empty($sk_value['realisasi_target_4']) ? $sk_value['realisasi_target_4'] : "",
																'realisasi_target_5' => !empty($sk_value['realisasi_target_5']) ? $sk_value['realisasi_target_5'] : "",
																'target_awal' => !empty($sk_value['target_awal']) ? $sk_value['target_awal'] : "",
																'target_akhir' => !empty($sk_value['target_akhir']) ? $sk_value['target_akhir'] : "",
																'keterangan' => implode(',', $keterangan),
															);
														}
													}
												}
											}

											if (!empty($k_value['id_unik_indikator']) && empty($data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['indikator'][$k_value['id_unik_indikator']])) {

												$keterangan = array();
												if (!empty($k_value['keterangan_1'])) {
													$keterangan[] = $k_value['keterangan_1'];
												}
												if (!empty($k_value['keterangan_2'])) {
													$keterangan[] = $k_value['keterangan_2'];
												}
												if (!empty($k_value['keterangan_3'])) {
													$keterangan[] = $k_value['keterangan_3'];
												}
												if (!empty($k_value['keterangan_4'])) {
													$keterangan[] = $k_value['keterangan_4'];
												}
												if (!empty($k_value['keterangan_5'])) {
													$keterangan[] = $k_value['keterangan_5'];
												}
												$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['data'][$k_value['kode_giat']]['indikator'][$k_value['id_unik_indikator']] = array(
													'id' => $k_value['id'],
													'id_unik_indikator' => $k_value['id_unik_indikator'],
													'indikator' => !empty($k_value['indikator']) ? $k_value['indikator'] : '-',
													'satuan' => !empty($k_value['satuan']) ? $k_value['satuan'] : "",
													'target_1' => !empty($k_value['target_1']) ? $k_value['target_1'] : "",
													'target_2' => !empty($k_value['target_2']) ? $k_value['target_2'] : "",
													'target_3' => !empty($k_value['target_3']) ? $k_value['target_3'] : "",
													'target_4' => !empty($k_value['target_4']) ? $k_value['target_4'] : "",
													'target_5' => !empty($k_value['target_5']) ? $k_value['target_5'] : "",
													'realisasi_target_1' => !empty($k_value['realisasi_target_1']) ? $k_value['realisasi_target_1'] : "",
													'realisasi_target_2' => !empty($k_value['realisasi_target_2']) ? $k_value['realisasi_target_2'] : "",
													'realisasi_target_3' => !empty($k_value['realisasi_target_3']) ? $k_value['realisasi_target_3'] : "",
													'realisasi_target_4' => !empty($k_value['realisasi_target_4']) ? $k_value['realisasi_target_4'] : "",
													'realisasi_target_5' => !empty($k_value['realisasi_target_5']) ? $k_value['realisasi_target_5'] : "",
													'target_awal' => !empty($k_value['target_awal']) ? $k_value['target_awal'] : "",
													'target_akhir' => !empty($k_value['target_akhir']) ? $k_value['target_akhir'] : "",
													'keterangan' => implode(',', $keterangan),
												);
											}
										}
									}
								}

								if (!empty($p_value['id_unik_indikator']) && empty($data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['indikator'][$p_value['id_unik_indikator']])) {
									$keterangan = array();
									if (!empty($p_value['keterangan_1'])) {
										$keterangan[] = $p_value['keterangan_1'];
									}
									if (!empty($p_value['keterangan_2'])) {
										$keterangan[] = $p_value['keterangan_2'];
									}
									if (!empty($p_value['keterangan_3'])) {
										$keterangan[] = $p_value['keterangan_3'];
									}
									if (!empty($p_value['keterangan_4'])) {
										$keterangan[] = $p_value['keterangan_4'];
									}
									if (!empty($p_value['keterangan_5'])) {
										$keterangan[] = $p_value['keterangan_5'];
									}
									$data_all['data'][$tujuan_key]['data'][$sasaran_key]['data'][$p_value['kode_program']]['indikator'][$p_value['id_unik_indikator']] = array(
										'id' => $p_value['id'],
										'id_unik_indikator' => $p_value['id_unik_indikator'],
										'indikator' => !empty($p_value['indikator']) ? $p_value['indikator'] : '-',
										'satuan' => !empty($p_value['satuan']) ? $p_value['satuan'] : "",
										'target_1' => !empty($p_value['target_1']) ? $p_value['target_1'] : "",
										'target_2' => !empty($p_value['target_2']) ? $p_value['target_2'] : "",
										'target_3' => !empty($p_value['target_3']) ? $p_value['target_3'] : "",
										'target_4' => !empty($p_value['target_4']) ? $p_value['target_4'] : "",
										'target_5' => !empty($p_value['target_5']) ? $p_value['target_5'] : "",
										'realisasi_target_1' => !empty($p_value['realisasi_target_1']) ? $p_value['realisasi_target_1'] : "",
										'realisasi_target_2' => !empty($p_value['realisasi_target_2']) ? $p_value['realisasi_target_2'] : "",
										'realisasi_target_3' => !empty($p_value['realisasi_target_3']) ? $p_value['realisasi_target_3'] : "",
										'realisasi_target_4' => !empty($p_value['realisasi_target_4']) ? $p_value['realisasi_target_4'] : "",
										'realisasi_target_5' => !empty($p_value['realisasi_target_5']) ? $p_value['realisasi_target_5'] : "",
										'target_awal' => !empty($p_value['target_awal']) ? $p_value['target_awal'] : "",
										'target_akhir' => !empty($p_value['target_akhir']) ? $p_value['target_akhir'] : "",
										'keterangan' => implode(',', $keterangan),
									);
								}
							}
						}
					}

					if (!empty($sasaran_value['id_unik_indikator']) && empty($data_all['data'][$tujuan_key]['data'][$sasaran_key]['indikator'][$sasaran_value['id_unik_indikator']])) {
						$keterangan = array();
						if (!empty($sasaran_value['keterangan_1'])) {
							$keterangan[] = $sasaran_value['keterangan_1'];
						}
						if (!empty($sasaran_value['keterangan_2'])) {
							$keterangan[] = $sasaran_value['keterangan_2'];
						}
						if (!empty($sasaran_value['keterangan_3'])) {
							$keterangan[] = $sasaran_value['keterangan_3'];
						}
						if (!empty($sasaran_value['keterangan_4'])) {
							$keterangan[] = $sasaran_value['keterangan_4'];
						}
						if (!empty($sasaran_value['keterangan_5'])) {
							$keterangan[] = $sasaran_value['keterangan_5'];
						}
						$data_all['data'][$tujuan_key]['data'][$sasaran_key]['indikator'][$sasaran_value['id_unik_indikator']] = array(
							'id' => $sasaran_value['id'],
							'id_unik_indikator' => $sasaran_value['id_unik_indikator'],
							'indikator' => !empty($sasaran_value['indikator_teks']) ? $sasaran_value['indikator_teks'] : '-',
							'satuan' => !empty($sasaran_value['satuan']) ? $sasaran_value['satuan'] : "",
							'target_1' => !empty($sasaran_value['target_1']) ? $sasaran_value['target_1'] : "",
							'target_2' => !empty($sasaran_value['target_2']) ? $sasaran_value['target_2'] : "",
							'target_3' => !empty($sasaran_value['target_3']) ? $sasaran_value['target_3'] : "",
							'target_4' => !empty($sasaran_value['target_4']) ? $sasaran_value['target_4'] : "",
							'target_5' => !empty($sasaran_value['target_5']) ? $sasaran_value['target_5'] : "",
							'realisasi_target_1' => !empty($sasaran_value['realisasi_target_1']) ? $sasaran_value['realisasi_target_1'] : "",
							'realisasi_target_2' => !empty($sasaran_value['realisasi_target_2']) ? $sasaran_value['realisasi_target_2'] : "",
							'realisasi_target_3' => !empty($sasaran_value['realisasi_target_3']) ? $sasaran_value['realisasi_target_3'] : "",
							'realisasi_target_4' => !empty($sasaran_value['realisasi_target_4']) ? $sasaran_value['realisasi_target_4'] : "",
							'realisasi_target_5' => !empty($sasaran_value['realisasi_target_5']) ? $sasaran_value['realisasi_target_5'] : "",
							'target_awal' => !empty($sasaran_value['target_awal']) ? $sasaran_value['target_awal'] : "",
							'target_akhir' => !empty($sasaran_value['target_akhir']) ? $sasaran_value['target_akhir'] : "",
							'keterangan' => implode(',', $keterangan),
						);
					}
				}
			}
		}

		if (!empty($tujuan_value['id_unik_indikator']) && empty($data_all['data'][$tujuan_key]['indikator'][$tujuan_value['id_unik_indikator']])) {
			$keterangan = array();
			if (!empty($tujuan_value['keterangan_1'])) {
				$keterangan[] = $tujuan_value['keterangan_1'];
			}
			if (!empty($tujuan_value['keterangan_2'])) {
				$keterangan[] = $tujuan_value['keterangan_2'];
			}
			if (!empty($tujuan_value['keterangan_3'])) {
				$keterangan[] = $tujuan_value['keterangan_3'];
			}
			if (!empty($tujuan_value['keterangan_4'])) {
				$keterangan[] = $tujuan_value['keterangan_4'];
			}
			if (!empty($tujuan_value['keterangan_5'])) {
				$keterangan[] = $tujuan_value['keterangan_5'];
			}
			$data_all['data'][$tujuan_key]['indikator'][$tujuan_value['id_unik_indikator']] = array(
				'id' => $tujuan_value['id'],
				'id_unik_indikator' => $tujuan_value['id_unik_indikator'],
				'indikator' => !empty($tujuan_value['indikator_teks']) ? $tujuan_value['indikator_teks'] : '-',
				'satuan' => !empty($tujuan_value['satuan']) ? $tujuan_value['satuan'] : "",
				'target_1' => !empty($tujuan_value['target_1']) ? $tujuan_value['target_1'] : "",
				'target_2' => !empty($tujuan_value['target_2']) ? $tujuan_value['target_2'] : "",
				'target_3' => !empty($tujuan_value['target_3']) ? $tujuan_value['target_3'] : "",
				'target_4' => !empty($tujuan_value['target_4']) ? $tujuan_value['target_4'] : "",
				'target_5' => !empty($tujuan_value['target_5']) ? $tujuan_value['target_5'] : "",
				'realisasi_target_1' => !empty($tujuan_value['realisasi_target_1']) ? $tujuan_value['realisasi_target_1'] : "",
				'realisasi_target_2' => !empty($tujuan_value['realisasi_target_2']) ? $tujuan_value['realisasi_target_2'] : "",
				'realisasi_target_3' => !empty($tujuan_value['realisasi_target_3']) ? $tujuan_value['realisasi_target_3'] : "",
				'realisasi_target_4' => !empty($tujuan_value['realisasi_target_4']) ? $tujuan_value['realisasi_target_4'] : "",
				'realisasi_target_5' => !empty($tujuan_value['realisasi_target_5']) ? $tujuan_value['realisasi_target_5'] : "",
				'target_awal' => !empty($tujuan_value['target_awal']) ? $tujuan_value['target_awal'] : "",
				'target_akhir' => !empty($tujuan_value['target_akhir']) ? $tujuan_value['target_akhir'] : "",
				'keterangan' => implode(', ', $keterangan),
			);
		}
	}
}

$bidur_skpd_db = $this->get_skpd_db($input['id_skpd']);
$bidur_skpd = $bidur_skpd_db['skpd'][0]['bidur_1'];

// echo '<pre>';print_r($data_all['data']);echo '</pre>'; die();
$no_tujuan = 0;
$no_sasaran = 0;
$no_program = 0;
$no_kegiatan = 0;
$no_sub_kegiatan = 0;
foreach ($data_all['data'] as $key => $tujuan) {
	$no_tujuan++;
	// echo '<pre>';print_r($tujuan);echo '</pre>'; die();
	$target_1 = '';
	$target_2 = '';
	$target_3 = '';
	$target_4 = '';
	$target_5 = '';
	$realisasi_target_1 = '';
	$realisasi_target_2 = '';
	$realisasi_target_3 = '';
	$realisasi_target_4 = '';
	$realisasi_target_5 = '';
	$pagu_1 = '<div class="pagu">' . $this->_number_format($tujuan['pagu_1']) . '</div>';
	$pagu_2 = '<div class="pagu">' . $this->_number_format($tujuan['pagu_2']) . '</div>';
	$pagu_3 = '<div class="pagu">' . $this->_number_format($tujuan['pagu_3']) . '</div>';
	$pagu_4 = '<div class="pagu">' . $this->_number_format($tujuan['pagu_4']) . '</div>';
	$pagu_5 = '<div class="pagu">' . $this->_number_format($tujuan['pagu_5']) . '</div>';
	$realisasi_pagu_1 = '<div class="realisasi-pagu">' . $this->_number_format($tujuan['realisasi_pagu_1']) . '</div>';
	$realisasi_pagu_2 = '<div class="realisasi-pagu">' . $this->_number_format($tujuan['realisasi_pagu_2']) . '</div>';
	$realisasi_pagu_3 = '<div class="realisasi-pagu">' . $this->_number_format($tujuan['realisasi_pagu_3']) . '</div>';
	$realisasi_pagu_4 = '<div class="realisasi-pagu">' . $this->_number_format($tujuan['realisasi_pagu_4']) . '</div>';
	$realisasi_pagu_5 = '<div class="realisasi-pagu">' . $this->_number_format($tujuan['realisasi_pagu_5']) . '</div>';
	$indikator_all = '';
	$satuan = '';
	$target_awal = '';
	$target_akhir = '';
	$keterangan = '';
	foreach ($tujuan['indikator'] as $k => $v) {
		$indikator_teks = $v['indikator'] . button_edit_monev($input['id_jadwal'] . '-' . $input['id_skpd'] . '-' . $v['id'] . '-1');
		$target_1 .= '<div class="indikator target-1">' . $v['target_1'] . '</div>';
		$target_2 .= '<div class="indikator target-2">' . $v['target_2'] . '</div>';
		$target_3 .= '<div class="indikator target-3">' . $v['target_3'] . '</div>';
		$target_4 .= '<div class="indikator target-4">' . $v['target_4'] . '</div>';
		$target_5 .= '<div class="indikator target-5">' . $v['target_5'] . '</div>';

		$realisasi_target_1 .= '<div class="indikator realisasi-target-1">' . $v['realisasi_target_1'] . '</div>';
		$realisasi_target_2 .= '<div class="indikator realisasi-target-2">' . $v['realisasi_target_2'] . '</div>';
		$realisasi_target_3 .= '<div class="indikator realisasi-target-3">' . $v['realisasi_target_3'] . '</div>';
		$realisasi_target_4 .= '<div class="indikator realisasi-target-4">' . $v['realisasi_target_4'] . '</div>';
		$realisasi_target_5 .= '<div class="indikator realisasi-target-5">' . $v['realisasi_target_5'] . '</div>';

		$indikator_all .= '<div class="indikator indikator_teks">' . $indikator_teks . '</div>';
		$satuan .= '<div class="indikator satuan">' . $v['satuan'] . '</div>';
		$target_awal .= '<div class="indikator target_awal">' . $v['target_awal'] . '</div>';
		$target_akhir .= '<div class="indikator target_akhir">' . $v['target_akhir'] . '</div>';
		$keterangan .= '<div class="indikator keterangan">' . $v['keterangan'] . '</div>';
	}

	$target_arr = array(
		$target_1,
		$target_2,
		$target_3,
		$target_4,
		$target_5
	);
	$realisasi_target_arr = array(
		$realisasi_target_1,
		$realisasi_target_2,
		$realisasi_target_3,
		$realisasi_target_4,
		$realisasi_target_5
	);
	$pagu_arr = array(
		$pagu_1,
		$pagu_2,
		$pagu_3,
		$pagu_4,
		$pagu_5
	);
	$realisasi_pagu_arr = array(
		$realisasi_pagu_1,
		$realisasi_pagu_2,
		$realisasi_pagu_3,
		$realisasi_pagu_4,
		$realisasi_pagu_5
	);

	$status_rpjmd = !empty($tujuan['status_rpjmd']) ? '<a href="javascript:void(0)" onclick="show_rpjm(\'' . $input['id_jadwal'] . '\', \'' . $input['id_skpd'] . '\', \'' . $tujuan['kode_sasaran_rpjm'] . '\')">
	            	' . $tujuan['status_rpjmd'] . '
	            	</a>' : $tujuan['status_rpjmd'];

	$backgroundColor = !empty($tujuan['status']) ? '' : '#ffdbdb';
	$backgroundColor = !empty($tujuan['status_rpjmd']) ? '' : '#f7d2a1';

	if (strpos($tujuan['nama_bidang_urusan'], 'X.XX') !== false) {
		$tujuan['nama_bidang_urusan'] = str_replace('X.XX', 'Bidang Urusan Penunjang', $tujuan['nama_bidang_urusan']);
		$tujuan['nama_bidang_urusan_teks'] = str_replace('X.XX', 'Bidang Urusan Penunjang', $tujuan['nama_bidang_urusan_teks']);
	}
	$body_monev .= '
		<tr class="tujuan tr-tujuan" data-kode="" style="background-color:' . $backgroundColor . '">
            <td class="kiri kanan bawah text_blok">' . $no_tujuan . '</td>
            <td class="kiri kanan bawah text_blok">' . $status_rpjmd . '</td>
            <td class="kiri kanan bawah text_blok">
            	<span class="debug-renstra">' . $tujuan['nama_bidang_urusan'] . '</span>
            	<span class="nondebug-renstra">' . $tujuan['nama_bidang_urusan_teks'] . '</span>
            </td>
            <td class="text_kiri kanan bawah text_blok data-tujuan">
            	<span class="debug-renstra data-renstra">' . $tujuan['nama'] . '</span>
            	<span class="nondebug-renstra">' . $tujuan['nama_teks'] . '</span>
            </td>
            <td class="kanan bawah text_blok data-sasaran"></td>
            <td class="kanan bawah text_blok data-program"></td>
            <td class="kanan bawah text_blok data-kegiatan"></td>
            <td class="kanan bawah text_blok data-sub-kegiatan"></td>
            <td class="kanan bawah text_blok indikator rumus_indikator">' . $indikator_all . '</td>
            <td class="text_tengah kanan bawah text_blok total_renstra">' . $target_awal . '</td>';

	for ($i = 0; $i < $lama_pelaksanaan; $i++) {
		$body_monev .= '<td class="kanan bawah text_tengah">' . $target_arr[$i] . '</td>';
		$body_monev .= '<td class="kanan bawah text_tengah">' . $realisasi_target_arr[$i] . '</td>';
		$body_monev .= '<td class="kanan bawah text_kanan">' . $pagu_arr[$i] . '</td>';
		$body_monev .= '<td class="kanan bawah text_kanan">' . $realisasi_pagu_arr[$i] . '</td>';
	}

	$body_monev .= '
            <td class="text_tengah kanan bawah text_blok total_renstra">' . $target_akhir . '</td>
            <td class="text_tengah kanan bawah text_blok total_renstra">' . $satuan . '</td>
    		<td class="kanan bawah text_blok">' . $unit[0]['nama_skpd'] . '</td>
    		<td class="kanan bawah keterangan">' . $keterangan . '</td>
        </tr>';
	foreach ($tujuan['data'] as $key => $sasaran) {
		$no_sasaran++;

		$target_1 = '';
		$target_2 = '';
		$target_3 = '';
		$target_4 = '';
		$target_5 = '';
		$realisasi_target_1 = '';
		$realisasi_target_2 = '';
		$realisasi_target_3 = '';
		$realisasi_target_4 = '';
		$realisasi_target_5 = '';
		$pagu_1 = '<div class="pagu">' . $this->_number_format($sasaran['pagu_1']) . '</div>';
		$pagu_2 = '<div class="pagu">' . $this->_number_format($sasaran['pagu_2']) . '</div>';
		$pagu_3 = '<div class="pagu">' . $this->_number_format($sasaran['pagu_3']) . '</div>';
		$pagu_4 = '<div class="pagu">' . $this->_number_format($sasaran['pagu_4']) . '</div>';
		$pagu_5 = '<div class="pagu">' . $this->_number_format($sasaran['pagu_5']) . '</div>';
		$realisasi_pagu_1 = '<div class="realisasi-pagu">' . $this->_number_format($sasaran['realisasi_pagu_1']) . '</div>';
		$realisasi_pagu_2 = '<div class="realisasi-pagu">' . $this->_number_format($sasaran['realisasi_pagu_2']) . '</div>';
		$realisasi_pagu_3 = '<div class="realisasi-pagu">' . $this->_number_format($sasaran['realisasi_pagu_3']) . '</div>';
		$realisasi_pagu_4 = '<div class="realisasi-pagu">' . $this->_number_format($sasaran['realisasi_pagu_4']) . '</div>';
		$realisasi_pagu_5 = '<div class="realisasi-pagu">' . $this->_number_format($sasaran['realisasi_pagu_5']) . '</div>';
		$indikator_all = '';
		$satuan = '';
		$target_awal = '';
		$target_akhir = '';
		$keterangan = '';
		foreach ($sasaran['indikator'] as $k => $v) {
			$indikator_teks = $v['indikator'] . button_edit_monev($input['id_jadwal'] . '-' . $input['id_skpd'] . '-' . $v['id'] . '-2');
			$target_1 .= '<div class="indikator target-1">' . $v['target_1'] . '</div>';
			$target_2 .= '<div class="indikator target-2">' . $v['target_2'] . '</div>';
			$target_3 .= '<div class="indikator target-3">' . $v['target_3'] . '</div>';
			$target_4 .= '<div class="indikator target-4">' . $v['target_4'] . '</div>';
			$target_5 .= '<div class="indikator target-5">' . $v['target_5'] . '</div>';

			$realisasi_target_1 .= '<div class="indikator realisasi-target-1">' . $v['realisasi_target_1'] . '</div>';
			$realisasi_target_2 .= '<div class="indikator realisasi-target-2">' . $v['realisasi_target_2'] . '</div>';
			$realisasi_target_3 .= '<div class="indikator realisasi-target-3">' . $v['realisasi_target_3'] . '</div>';
			$realisasi_target_4 .= '<div class="indikator realisasi-target-4">' . $v['realisasi_target_4'] . '</div>';
			$realisasi_target_5 .= '<div class="indikator realisasi-target-5">' . $v['realisasi_target_5'] . '</div>';

			$indikator_all .= '<div class="indikator indikator_teks">' . $indikator_teks . '</div>';
			$satuan .= '<div class="indikator satuan">' . $v['satuan'] . '</div>';
			$target_awal .= '<div class="indikator target_awal">' . $v['target_awal'] . '</div>';
			$target_akhir .= '<div class="indikator target_akhir">' . $v['target_akhir'] . '</div>';
			$keterangan .= '<div class="indikator keterangan">' . $v['keterangan'] . '</div>';
		}

		$target_arr = array(
			$target_1,
			$target_2,
			$target_3,
			$target_4,
			$target_5
		);
		$realisasi_target_arr = array(
			$realisasi_target_1,
			$realisasi_target_2,
			$realisasi_target_3,
			$realisasi_target_4,
			$realisasi_target_5
		);
		$pagu_arr = array(
			$pagu_1,
			$pagu_2,
			$pagu_3,
			$pagu_4,
			$pagu_5
		);
		$realisasi_pagu_arr = array(
			$realisasi_pagu_1,
			$realisasi_pagu_2,
			$realisasi_pagu_3,
			$realisasi_pagu_4,
			$realisasi_pagu_5
		);
		$backgroundColor = !empty($sasaran['status']) ? '' : '#ffdbdb';

		if (strpos($sasaran['nama_bidang_urusan'], 'X.XX') !== false) {
			$sasaran['nama_bidang_urusan'] = str_replace('X.XX', 'Bidang Urusan Penunjang', $sasaran['nama_bidang_urusan']);
		}
		$body_monev .= '
			<tr class="sasaran tr-sasaran" data-kode="" style="background-color:' . $backgroundColor . '">
	            <td class="kiri kanan bawah text_blok">' . $no_tujuan . "." . $no_sasaran . '</td>
	            <td class="kiri kanan bawah text_blok"></td>
	            <td class="kiri kanan bawah text_blok">
	            	<span class="debug-renstra">' . $sasaran['nama_bidang_urusan'] . '</span>
	            </td>
	            <td class="text_kiri kanan bawah text_blok">
	            	<span class="debug-renstra">' . $tujuan['nama'] . '</span>
	            </td>
	            <td class="text_kiri kanan bawah text_blok">
	            	<span class="debug-renstra data-renstra">' . $sasaran['nama'] . '</span>
	            	<span class="nondebug-renstra">' . $sasaran['nama_teks'] . '</span>
	            </td>
	            <td class="kanan bawah text_blok program"></td>
	            <td class="kanan bawah text_blok kegiatan"></td>
	            <td class="kanan bawah text_blok sub-kegiatan"></td>
	            <td class="kanan bawah text_blok indikator rumus_indikator">' . $indikator_all . '</td>
	            <td class="text_tengah kanan bawah text_blok total_renstra">' . $target_awal . '</td>';

		for ($i = 0; $i < $lama_pelaksanaan; $i++) {
			$body_monev .= '<td class="kanan bawah text_tengah">' . $target_arr[$i] . '</td>';
			$body_monev .= '<td class="kanan bawah text_tengah">' . $realisasi_target_arr[$i] . '</td>';
			$body_monev .= '<td class="kanan bawah text_kanan">' . $pagu_arr[$i] . '</td>';
			$body_monev .= '<td class="kanan bawah text_kanan">' . $realisasi_pagu_arr[$i] . '</td>';
		}

		$body_monev .= '
	            <td class="text_tengah kanan bawah text_blok total_renstra">' . $target_akhir . '</td>
	            <td class="text_tengah kanan bawah text_blok total_renstra">' . $satuan . '</td>
        		<td class="kanan bawah text_blok">' . $unit[0]['nama_skpd'] . '</td>
        		<td class="kanan bawah keterangan">' . $keterangan . '</td>
	        </tr>';

		foreach ($sasaran['data'] as $key => $program) {
			$no_program++;

			// update pagu dan realisasi untuk ditampilkan di monev renja
			$wpdb->update('data_renstra_program', array(
				'pagu_1' => $program['pagu_1'],
				'pagu_2' => $program['pagu_2'],
				'pagu_3' => $program['pagu_3'],
				'pagu_4' => $program['pagu_4'],
				'pagu_5' => $program['pagu_5'],
				'realisasi_pagu_1' => $program['realisasi_pagu_1'],
				'realisasi_pagu_2' => $program['realisasi_pagu_2'],
				'realisasi_pagu_3' => $program['realisasi_pagu_3'],
				'realisasi_pagu_4' => $program['realisasi_pagu_4'],
				'realisasi_pagu_5' => $program['realisasi_pagu_5'],
			), array('id' => $program['id']));

			$target_1 = '';
			$target_2 = '';
			$target_3 = '';
			$target_4 = '';
			$target_5 = '';
			$realisasi_target_1 = '';
			$realisasi_target_2 = '';
			$realisasi_target_3 = '';
			$realisasi_target_4 = '';
			$realisasi_target_5 = '';
			$pagu_1 = '<div class="pagu">' . $this->_number_format($program['pagu_1']) . '</div>';
			$pagu_2 = '<div class="pagu">' . $this->_number_format($program['pagu_2']) . '</div>';
			$pagu_3 = '<div class="pagu">' . $this->_number_format($program['pagu_3']) . '</div>';
			$pagu_4 = '<div class="pagu">' . $this->_number_format($program['pagu_4']) . '</div>';
			$pagu_5 = '<div class="pagu">' . $this->_number_format($program['pagu_5']) . '</div>';
			$realisasi_pagu_1 = '<div class="realisasi-pagu">' . $this->_number_format($program['realisasi_pagu_1']) . '</div>';
			$realisasi_pagu_2 = '<div class="realisasi-pagu">' . $this->_number_format($program['realisasi_pagu_2']) . '</div>';
			$realisasi_pagu_3 = '<div class="realisasi-pagu">' . $this->_number_format($program['realisasi_pagu_3']) . '</div>';
			$realisasi_pagu_4 = '<div class="realisasi-pagu">' . $this->_number_format($program['realisasi_pagu_4']) . '</div>';
			$realisasi_pagu_5 = '<div class="realisasi-pagu">' . $this->_number_format($program['realisasi_pagu_5']) . '</div>';
			$indikator_all = '';
			$satuan = '';
			$target_awal = '';
			$target_akhir = '';
			$keterangan = '';
			foreach ($program['indikator'] as $k => $v) {
				$indikator_teks = $v['indikator'] . button_edit_monev($input['id_jadwal'] . '-' . $input['id_skpd'] . '-' . $v['id'] . '-3');
				$target_1 .= '<div class="indikator target-1">' . $v['target_1'] . '</div>';
				$target_2 .= '<div class="indikator target-2">' . $v['target_2'] . '</div>';
				$target_3 .= '<div class="indikator target-3">' . $v['target_3'] . '</div>';
				$target_4 .= '<div class="indikator target-4">' . $v['target_4'] . '</div>';
				$target_5 .= '<div class="indikator target-5">' . $v['target_5'] . '</div>';

				$realisasi_target_1 .= '<div class="indikator realisasi-target-1">' . $v['realisasi_target_1'] . '</div>';
				$realisasi_target_2 .= '<div class="indikator realisasi-target-2">' . $v['realisasi_target_2'] . '</div>';
				$realisasi_target_3 .= '<div class="indikator realisasi-target-3">' . $v['realisasi_target_3'] . '</div>';
				$realisasi_target_4 .= '<div class="indikator realisasi-target-4">' . $v['realisasi_target_4'] . '</div>';
				$realisasi_target_5 .= '<div class="indikator realisasi-target-5">' . $v['realisasi_target_5'] . '</div>';

				$indikator_all .= '<div class="indikator indikator_teks">' . $indikator_teks . '</div>';
				$satuan .= '<div class="indikator satuan">' . $v['satuan'] . '</div>';
				$target_awal .= '<div class="indikator target_awal">' . $v['target_awal'] . '</div>';
				$target_akhir .= '<div class="indikator target_akhir">' . $v['target_akhir'] . '</div>';
				$keterangan .= '<div class="indikator keterangan">' . $v['keterangan'] . '</div>';
			}

			$target_arr = array(
				$target_1,
				$target_2,
				$target_3,
				$target_4,
				$target_5
			);
			$realisasi_target_arr = array(
				$realisasi_target_1,
				$realisasi_target_2,
				$realisasi_target_3,
				$realisasi_target_4,
				$realisasi_target_5
			);
			$pagu_arr = array(
				$pagu_1,
				$pagu_2,
				$pagu_3,
				$pagu_4,
				$pagu_5
			);
			$realisasi_pagu_arr = array(
				$realisasi_pagu_1,
				$realisasi_pagu_2,
				$realisasi_pagu_3,
				$realisasi_pagu_4,
				$realisasi_pagu_5
			);
			$backgroundColor = !empty($program['status']) ? '' : '#ffdbdb';

			if (strpos($program['nama_bidang_urusan'], 'X.XX') !== false) {
				$program['nama_bidang_urusan'] = str_replace('X.XX', 'Bidang Urusan Penunjang', $program['nama_bidang_urusan']);
			}
			if (strpos($program['nama'], 'X.XX') !== false) {
				$program['nama'] = str_replace('X.XX', $bidur_skpd, $program['nama']);
				$program['nama_teks'] = str_replace('X.XX', $bidur_skpd, $program['nama_teks']);
			}
			$body_monev .= '
				<tr class="program tr-program" data-kode="' . $input['id_jadwal'] . '-' . $input['id_skpd'] . '-' . $program['kode_tujuan'] . '-' . $program['kode_sasaran'] . '-' . $program['kode_program'] . '" style="background-color:' . $backgroundColor . '">
		            <td class="kiri kanan bawah text_blok">' . $no_tujuan . "." . $no_sasaran . "." . $no_program . '</td>
	            	<td class="kiri kanan bawah text_blok"></td>
		            <td class="text_kiri kanan bawah text_blok">
		            	<span class="debug-renstra">' . $program['nama_bidang_urusan'] . '</span>
		            </td>
		            <td class="text_kiri kanan bawah text_blok">
		            	<span class="debug-renstra">' . $tujuan['nama'] . '</span>
		            </td>
		            <td class="text_kiri kanan bawah text_blok">
		            	<span class="debug-renstra">' . $sasaran['nama'] . '</span>
		            </td>
		            <td class="kanan bawah text_blok program" nama>
		            	<span class="debug-renstra data-renstra">' . $program['nama'] . '</span>
		            	<span class="nondebug-renstra">' . $program['nama_teks'] . '</span>
		            </td>
		            <td class="kanan bawah text_blok kegiatan"></td>
		            <td class="kanan bawah text_blok sub-kegiatan"></td>
		            <td class="kanan bawah text_blok indikator rumus_indikator">' . $indikator_all . '</td>
		            <td class="text_tengah kanan bawah text_blok total_renstra">' . $target_awal . '</td>';

			for ($i = 0; $i < $lama_pelaksanaan; $i++) {
				$body_monev .= '<td class="kanan bawah text_tengah">' . $target_arr[$i] . '</td>';
				$body_monev .= '<td class="kanan bawah text_tengah">' . $realisasi_target_arr[$i] . '</td>';
				$body_monev .= '<td class="kanan bawah text_kanan">' . $pagu_arr[$i] . '</td>';
				$body_monev .= '<td class="kanan bawah text_kanan">' . $realisasi_pagu_arr[$i] . '</td>';
			}

			$body_monev .= '
		            <td class="text_tengah kanan bawah text_blok total_renstra">' . $target_akhir . '</td>
		            <td class="text_tengah kanan bawah text_blok total_renstra">' . $satuan . '</td>
	        		<td class="kanan bawah text_blok">' . $unit[0]['nama_skpd'] . '</td>
	        		<td class="kanan bawah keterangan">' . $keterangan . '</td>
		        </tr>';

			foreach ($program['data'] as $key => $kegiatan) {
				$no_kegiatan++;

				// update pagu dan realisasi untuk ditampilkan di monev renja
				$wpdb->update('data_renstra_kegiatan', array(
					'pagu_1' => $kegiatan['pagu_1'],
					'pagu_2' => $kegiatan['pagu_2'],
					'pagu_3' => $kegiatan['pagu_3'],
					'pagu_4' => $kegiatan['pagu_4'],
					'pagu_5' => $kegiatan['pagu_5'],
					'realisasi_pagu_1' => $kegiatan['realisasi_pagu_1'],
					'realisasi_pagu_2' => $kegiatan['realisasi_pagu_2'],
					'realisasi_pagu_3' => $kegiatan['realisasi_pagu_3'],
					'realisasi_pagu_4' => $kegiatan['realisasi_pagu_4'],
					'realisasi_pagu_5' => $kegiatan['realisasi_pagu_5'],
				), array('id' => $kegiatan['id']));

				$target_1 = '';
				$target_2 = '';
				$target_3 = '';
				$target_4 = '';
				$target_5 = '';
				$realisasi_target_1 = '';
				$realisasi_target_2 = '';
				$realisasi_target_3 = '';
				$realisasi_target_4 = '';
				$realisasi_target_5 = '';
				$pagu_1 = '<div class="pagu">' . $this->_number_format($kegiatan['pagu_1']) . '</div>';
				$pagu_2 = '<div class="pagu">' . $this->_number_format($kegiatan['pagu_2']) . '</div>';
				$pagu_3 = '<div class="pagu">' . $this->_number_format($kegiatan['pagu_3']) . '</div>';
				$pagu_4 = '<div class="pagu">' . $this->_number_format($kegiatan['pagu_4']) . '</div>';
				$pagu_5 = '<div class="pagu">' . $this->_number_format($kegiatan['pagu_5']) . '</div>';
				$realisasi_pagu_1 = '<div class="realisasi-pagu">' . $this->_number_format($kegiatan['realisasi_pagu_1']) . '</div>';
				$realisasi_pagu_2 = '<div class="realisasi-pagu">' . $this->_number_format($kegiatan['realisasi_pagu_2']) . '</div>';
				$realisasi_pagu_3 = '<div class="realisasi-pagu">' . $this->_number_format($kegiatan['realisasi_pagu_3']) . '</div>';
				$realisasi_pagu_4 = '<div class="realisasi-pagu">' . $this->_number_format($kegiatan['realisasi_pagu_4']) . '</div>';
				$realisasi_pagu_5 = '<div class="realisasi-pagu">' . $this->_number_format($kegiatan['realisasi_pagu_5']) . '</div>';
				$indikator_all = '';
				$satuan = '';
				$target_awal = '';
				$target_akhir = '';
				$keterangan = '';
				foreach ($kegiatan['indikator'] as $k => $v) {
					$indikator_teks = $v['indikator'] . button_edit_monev($input['id_jadwal'] . '-' . $input['id_skpd'] . '-' . $v['id'] . '-4');
					$target_1 .= '<div class="indikator target-1">' . $v['target_1'] . '</div>';
					$target_2 .= '<div class="indikator target-2">' . $v['target_2'] . '</div>';
					$target_3 .= '<div class="indikator target-3">' . $v['target_3'] . '</div>';
					$target_4 .= '<div class="indikator target-4">' . $v['target_4'] . '</div>';
					$target_5 .= '<div class="indikator target-5">' . $v['target_5'] . '</div>';

					$realisasi_target_1 .= '<div class="indikator realisasi-target-1">' . $v['realisasi_target_1'] . '</div>';
					$realisasi_target_2 .= '<div class="indikator realisasi-target-2">' . $v['realisasi_target_2'] . '</div>';
					$realisasi_target_3 .= '<div class="indikator realisasi-target-3">' . $v['realisasi_target_3'] . '</div>';
					$realisasi_target_4 .= '<div class="indikator realisasi-target-4">' . $v['realisasi_target_4'] . '</div>';
					$realisasi_target_5 .= '<div class="indikator realisasi-target-5">' . $v['realisasi_target_5'] . '</div>';

					$indikator_all .= '<div class="indikator indikator_teks">' . $indikator_teks . '</div>';
					$satuan .= '<div class="indikator satuan">' . $v['satuan'] . '</div>';
					$target_awal .= '<div class="indikator target_awal">' . $v['target_awal'] . '</div>';
					$target_akhir .= '<div class="indikator target_akhir">' . $v['target_akhir'] . '</div>';
					$keterangan .= '<div class="indikator keterangan">' . $v['keterangan'] . '</div>';
				}

				$target_arr = array(
					$target_1,
					$target_2,
					$target_3,
					$target_4,
					$target_5
				);
				$realisasi_target_arr = array(
					$realisasi_target_1,
					$realisasi_target_2,
					$realisasi_target_3,
					$realisasi_target_4,
					$realisasi_target_5
				);
				$pagu_arr = array(
					$pagu_1,
					$pagu_2,
					$pagu_3,
					$pagu_4,
					$pagu_5
				);
				$realisasi_pagu_arr = array(
					$realisasi_pagu_1,
					$realisasi_pagu_2,
					$realisasi_pagu_3,
					$realisasi_pagu_4,
					$realisasi_pagu_5
				);
				$backgroundColor = !empty($kegiatan['status']) ? '' : '#ffdbdb';

				if (strpos($kegiatan['nama_bidang_urusan'], 'X.XX') !== false) {
					$kegiatan['nama_bidang_urusan'] = str_replace('X.XX', 'Bidang Urusan Penunjang', $kegiatan['nama_bidang_urusan']);
				}
				if (strpos($kegiatan['nama'], 'X.XX') !== false) {
					$kegiatan['nama'] = str_replace('X.XX', $bidur_skpd, $kegiatan['nama']);
					$kegiatan['nama_teks'] = str_replace('X.XX', $bidur_skpd, $kegiatan['nama_teks']);
				}
				$body_monev .= '
					<tr class="kegiatan tr-kegiatan" data-kode="" style="background-color:' . $backgroundColor . '">
			            <td class="kiri kanan bawah">' . $no_tujuan . "." . $no_sasaran . "." . $no_program . "." . $no_kegiatan . '</td>
	            		<td class="kiri kanan bawah"></td>
			            <td class="kiri kanan bawah">
			            	<span class="debug-renstra">' . $kegiatan['nama_bidang_urusan'] . '</span>
			            </td>
			            <td class="text_kiri kanan bawah">
			            	<span class="debug-renstra">' . $tujuan['nama'] . '</span>
			            </td>
			            <td class="text_kiri kanan bawah">
			            	<span class="debug-renstra">' . $sasaran['nama'] . '</span>
			            </td>
			            <td class="kanan bawah program">
			            	<span class="debug-renstra">' . $program['nama'] . '</span>
			            </td>
			            <td class="kanan bawah nama kegiatan">
			            	<span class="debug-renstra data-renstra">' . $kegiatan['nama'] . '</span>
			            	<span class="nondebug-renstra">' . $kegiatan['nama_teks'] . '</span>
			            </td>
			            <td class="kanan bawah sub-kegiatan"></td>
			            <td class="kanan bawah indikator rumus_indikator">' . $indikator_all . '</td>
			            <td class="text_tengah kanan bawah total_renstra">' . $target_awal . '</td>';

				for ($i = 0; $i < $lama_pelaksanaan; $i++) {
					$body_monev .= '<td class="kanan bawah text_tengah">' . $target_arr[$i] . '</td>';
					$body_monev .= '<td class="kanan bawah text_tengah">' . $realisasi_target_arr[$i] . '</td>';
					$body_monev .= '<td class="kanan bawah text_kanan">' . $pagu_arr[$i] . '</td>';
					$body_monev .= '<td class="kanan bawah text_kanan">' . $realisasi_pagu_arr[$i] . '</td>';
				}

				$body_monev .= '
			            <td class="text_tengah kanan bawah total_renstra">' . $target_akhir . '</td>
			            <td class="text_tengah kanan bawah total_renstra">' . $satuan . '</td>
		        		<td class="kanan bawah">' . $unit[0]['nama_skpd'] . '</td>
		        		<td class="kanan bawah keterangan">' . $keterangan . '</td>
			        </tr>';

				foreach ($kegiatan['data'] as $key => $sub_kegiatan) {
					$no_sub_kegiatan++;

					$target_1 = '';
					$target_2 = '';
					$target_3 = '';
					$target_4 = '';
					$target_5 = '';
					$realisasi_target_1 = '';
					$realisasi_target_2 = '';
					$realisasi_target_3 = '';
					$realisasi_target_4 = '';
					$realisasi_target_5 = '';
					$pagu_1 = $this->_number_format($sub_kegiatan['pagu_1']);
					$pagu_2 = $this->_number_format($sub_kegiatan['pagu_2']);
					$pagu_3 = $this->_number_format($sub_kegiatan['pagu_3']);
					$pagu_4 = $this->_number_format($sub_kegiatan['pagu_4']);
					$pagu_5 = $this->_number_format($sub_kegiatan['pagu_5']);
					$realisasi_pagu_1 = $this->_number_format($sub_kegiatan['realisasi_pagu_1']);
					$realisasi_pagu_2 = $this->_number_format($sub_kegiatan['realisasi_pagu_2']);
					$realisasi_pagu_3 = $this->_number_format($sub_kegiatan['realisasi_pagu_3']);
					$realisasi_pagu_4 = $this->_number_format($sub_kegiatan['realisasi_pagu_4']);
					$realisasi_pagu_5 = $this->_number_format($sub_kegiatan['realisasi_pagu_5']);
					$indikator_all = '';
					$satuan = '';
					$target_awal = '';
					$target_akhir = '';
					$keterangan = '';
					foreach ($sub_kegiatan['indikator'] as $k => $v) {
						$indikator_teks = $v['indikator'] . button_edit_monev($input['id_jadwal'] . '-' . $input['id_skpd'] . '-' . $v['id'] . '-5');
						$target_1 .= '<div class="indikator target-1">' . $v['target_1'] . '</div>';
						$target_2 .= '<div class="indikator target-2">' . $v['target_2'] . '</div>';
						$target_3 .= '<div class="indikator target-3">' . $v['target_3'] . '</div>';
						$target_4 .= '<div class="indikator target-4">' . $v['target_4'] . '</div>';
						$target_5 .= '<div class="indikator target-5">' . $v['target_5'] . '</div>';

						$realisasi_target_1 .= '<div class="indikator realisasi-target-1">' . $v['realisasi_target_1'] . '</div>';
						$realisasi_target_2 .= '<div class="indikator realisasi-target-2">' . $v['realisasi_target_2'] . '</div>';
						$realisasi_target_3 .= '<div class="indikator realisasi-target-3">' . $v['realisasi_target_3'] . '</div>';
						$realisasi_target_4 .= '<div class="indikator realisasi-target-4">' . $v['realisasi_target_4'] . '</div>';
						$realisasi_target_5 .= '<div class="indikator realisasi-target-5">' . $v['realisasi_target_5'] . '</div>';

						$indikator_all .= '<div class="indikator indikator_teks">' . $indikator_teks . '</div>';
						$satuan .= '<div class="indikator satuan">' . $v['satuan'] . '</div>';
						$target_awal .= '<div class="indikator target_awal">' . $v['target_awal'] . '</div>';
						$target_akhir .= '<div class="indikator target_akhir">' . $v['target_akhir'] . '</div>';
						$keterangan .= '<div class="indikator keterangan">' . $v['keterangan'] . '</div>';
					}

					$target_arr = array(
						$target_1,
						$target_2,
						$target_3,
						$target_4,
						$target_5
					);
					$realisasi_target_arr = array(
						$realisasi_target_1,
						$realisasi_target_2,
						$realisasi_target_3,
						$realisasi_target_4,
						$realisasi_target_5
					);
					$pagu_arr = array(
						$pagu_1,
						$pagu_2,
						$pagu_3,
						$pagu_4,
						$pagu_5
					);
					$realisasi_pagu_arr = array(
						$realisasi_pagu_1,
						$realisasi_pagu_2,
						$realisasi_pagu_3,
						$realisasi_pagu_4,
						$realisasi_pagu_5
					);
					$backgroundColor = !empty($sub_kegiatan['status']) ? '' : '#ffdbdb';

					if (strpos($sub_kegiatan['nama_bidang_urusan'], 'X.XX') !== false) {
						$sub_kegiatan['nama_bidang_urusan'] = str_replace('X.XX', 'Bidang Urusan Penunjang', $sub_kegiatan['nama_bidang_urusan']);
					}
					if (strpos($sub_kegiatan['nama'], 'X.XX') !== false) {
						$sub_kegiatan['nama'] = str_replace('X.XX', $bidur_skpd, $sub_kegiatan['nama']);
						$sub_kegiatan['nama_teks'] = str_replace('X.XX', $bidur_skpd, $sub_kegiatan['nama_teks']);
					}
					$body_monev .= '
						<tr class="sub-kegiatan tr-sub-kegiatan" data-kode="" style="background-color:' . $backgroundColor . '">
				            <td class="kiri kanan bawah">' . $no_tujuan . "." . $no_sasaran . "." . $no_program . "." . $no_kegiatan . "." . $no_sub_kegiatan . '</td>
	            			<td class="kiri kanan bawah"></td>
				            <td class="kiri kanan bawah">
				            	<span class="debug-renstra">' . $sub_kegiatan['nama_bidang_urusan'] . '</span>
				            </td>
				            <td class="text_kiri kanan bawah">
				            	<span class="debug-renstra">' . $tujuan['nama'] . '</span>
				            </td>
				            <td class="text_kiri kanan bawah">
				            	<span class="debug-renstra">' . $sasaran['nama'] . '</span>
				            </td>
				            <td class="kanan bawah program">
				            	<span class="debug-renstra">' . $program['nama'] . '</span>
				            </td>
				            <td class="kanan bawah nama kegiatan">
				            	<span class="debug-renstra">' . $kegiatan['nama'] . '</span>
				            </td>
				            <td class="kanan bawah sub-kegiatan">
				            	<span class="debug-renstra data-renstra">' . $sub_kegiatan['nama'] . '</span>
				            	<span class="nondebug-renstra">' . $sub_kegiatan['nama_teks'] . '</span>
				            </td>
				            <td class="kanan bawah indikator rumus_indikator">' . $indikator_all . '</td>
				            <td class="text_tengah kanan bawah total_renstra">' . $target_awal . '</td>';

					for ($i = 0; $i < $lama_pelaksanaan; $i++) {
						$body_monev .= '<td class="kanan bawah text_tengah">' . $target_arr[$i] . '</td>';
						$body_monev .= '<td class="kanan bawah text_tengah">' . $realisasi_target_arr[$i] . '</td>';
						$body_monev .= '<td class="kanan bawah text_kanan">' . $pagu_arr[$i] . '</td>';
						$body_monev .= '<td class="kanan bawah text_kanan">' . $realisasi_pagu_arr[$i] . '</td>';
					}

					$body_monev .= '
				            <td class="text_tengah kanan bawah total_renstra">' . $target_akhir . '</td>
				            <td class="text_tengah kanan bawah total_renstra">' . $satuan . '</td>
			        		<td class="kanan bawah">' . $unit[0]['nama_skpd'] . '</td>
			        		<td class="kanan bawah keterangan">' . $keterangan . '</td>
				        </tr>';
				}
			}
		}
	}
}

$html_pagu = '';
$html_realisasi_pagu = '';
$html_capaian = '';
$data_all['lama_pelaksanaan'] = $lama_pelaksanaan;
$data_all['total'] = 0;
$data_all['realisasi'] = 0;
$data_all_js = array(array('Tahun', 'Anggaran', 'Realisasi'));
for ($i = 0; $i < $lama_pelaksanaan; $i++) {
	$no_urut = $i + 1;
	$data_all['total'] += $data_all['pagu_' . $no_urut];
	$data_all['realisasi'] += $data_all['realisasi_pagu_' . $no_urut];
	$html_pagu .= '
		<tr>
			<td><h4 class="font-weight-bolder text-warning py-1 m-0">' . $tahun_anggaran_renstra[$no_urut] . '</h4></td>
			<td><h4 class="font-weight-bolder text-warning py-1 m-0">:</h4></td>
			<td class="text-end text-right"><h4 class="font-weight-bolder text-warning py-1 m-0">' . number_format($data_all['pagu_' . $no_urut], 0, ",", ".") . '</h4></td>
		</tr>
	';
	$html_realisasi_pagu .= '
		<tr>
			<td><h4 class="font-weight-bolder text-primary py-1 m-0">' . $tahun_anggaran_renstra[$no_urut] . '</h4></td>
			<td><h4 class="font-weight-bolder text-primary py-1 m-0">:</h4></td>
			<td class="text-end text-right"><h4 class="font-weight-bolder text-primary py-1 m-0">' . number_format($data_all['realisasi_pagu_' . $no_urut], 0, ",", ".") . '</h4></td>
		</tr>
	';
	$capaian = 0;
	if (!empty($data_all['pagu_' . $no_urut]) && !empty($data_all['realisasi_pagu_' . $no_urut])) {
		$capaian = ($data_all['realisasi_pagu_' . $no_urut] / $data_all['pagu_' . $no_urut]) * 100;
	}
	$html_capaian .= '
		<tr>
			<td><h4 class="font-weight-bolder text-success py-1 m-0">' . $tahun_anggaran_renstra[$no_urut] . '</h4></td>
			<td><h4 class="font-weight-bolder text-success py-1 m-0">:</h4></td>
			<td class="text-end text-center"><h4 class="font-weight-bolder text-success py-1 m-0">' . $this->pembulatan($capaian) . '%</h4></td>
		</tr>
	';
	$data_all_js[] = array(strval($tahun_anggaran_renstra[$no_urut]), $data_all['pagu_' . $no_urut], $data_all['realisasi_pagu_' . $no_urut]);
}
$capaian_all = 0;
if (!empty($data_all['total']) && !empty($data_all['realisasi'])) {
	$capaian_all = ($data_all['realisasi'] / $data_all['total']) * 100;
}
?>

<style type="text/css">
	table th,
	#modal-monev th {
		vertical-align: middle;
	}

	body {
		overflow: auto;
	}

	td[contenteditable="true"] {
		background: #ff00002e;
	}

	.terkoneksi_rpjmd {
		background-color: aqua;
	}

	.debug-renstra,
	.data-renstra {
		display: none;
	}

	.action-checkbox {
		margin-left: 20px;
	}

	#table-renstra {
		font-family: \'Open Sans\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', sans-serif;
		border-collapse: collapse;
		font-size: 70%;
		border: 0;
		table-layout: fixed;
	}

	#table-renstra thead {
		position: sticky;
		top: -6px;
		background: #ffc491;
	}

	.tr-total-pagu-opd {
		background: #83efef;
	}

	.peringatan {
		background: #f5c9c9;
	}
</style>
<input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
<input type="hidden" value="<?php echo $tahun_anggaran_sipd; ?>" id="tahun_anggaran">
<input type="hidden" value="<?php echo $unit[0]['id_skpd']; ?>" id="id_skpd">
<h1 class="text-center">Monitoring dan Evaluasi Rencana Strategis <br><?php echo $unit[0]['kode_skpd'] . '&nbsp;' . $unit[0]['nama_skpd'] . '<br> ' . $nama_jadwal . ' ( ' . $tahun_awal_jadwal . ' - ' . $tahun_akhir_jadwal . ' ) </br>' . $nama_pemda; ?></h1>
<div class="content flex-row-fluid" style="max-width: 1500px; margin:auto; padding: 10px;">
	<div class="row gy-5 g-xl-8 mb-5">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div class="card-title">
						<h4 style="margin: 0;"><i class="dashicons dashicons-chart-bar" style="font-size: x-large; padding-top: 2px;"></i> Dashboard Anggaran dan Realisasi</h4>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div id="chart" style="padding: 30px; height: 500px;"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="row">
								<div class="col-md-12">
									<h2 class="font-weight-bolder text-white p-5 bg-warning rounded m-0 text-center">Anggaran</h2>
								</div>
							</div>
							<div class="d-flex align-items-center mb-9 bg-light-warning rounded" style="margin-top: 3rem;">
								<!--begin::Title-->
								<div class="col-md-12">
									<table class="table">
										<tr>
											<td style="width:20px;">
												<h2 class="font-weight-bolder text-warning py-1 m-0">Total</h2>
											</td>
											<td style="width:2px;">
												<h2 class="font-weight-bolder text-warning py-1 m-0">:</h2>
											</td>
											<td class="text-end text-right">
												<h2 class="font-weight-bolder text-warning py-1 m-0"><?php echo number_format($data_all['total'], 0, ",", "."); ?></h2>
											</td>
										</tr>
										<?php echo $html_pagu; ?>
									</table>
								</div>
								<!--end::Title-->
							</div>
						</div>

						<div class="col-md-4">
							<div class="row">
								<div class="col-md-12">
									<h2 class="font-weight-bolder text-white p-5 bg-primary rounded m-0 text-center">Realisasi</h2>
								</div>
							</div>
							<div class="d-flex align-items-center mb-9 bg-light-primary rounded" style="margin-top: 3rem;">
								<!--begin::Title-->
								<div class="col-md-12">
									<table class="table">
										<tr>
											<td style="width:20px;">
												<h2 class="font-weight-bolder text-primary py-1 m-0">Total</h2>
											</td>
											<td style="width:2px;">
												<h2 class="font-weight-bolder text-primary py-1 m-0">:</h2>
											</td>
											<td class="text-end text-right">
												<h2 class="font-weight-bolder text-primary py-1 m-0"><?php echo number_format($data_all['realisasi'], 0, ",", "."); ?></h2>
											</td>
										</tr>
										<?php echo $html_realisasi_pagu; ?>
									</table>
								</div>
								<!--end::Title-->
							</div>
						</div>
						<div class="col-md-4">
							<div class="row">
								<div class="col-md-12">
									<h2 class="font-weight-bolder text-white p-5 bg-success rounded m-0 text-center">Persentase</h2>
								</div>
							</div>
							<div class="d-flex align-items-center mb-9 bg-light-success rounded p-5">
								<!--begin::Title-->
								<div class="col-md-12">
									<table class="table">
										<tr>
											<td style="width:20px;">
												<h2 class="font-weight-bolder text-success py-1 m-0">Total</h2>
											</td>
											<td style="width:2px;">
												<h2 class="font-weight-bolder text-success py-1 m-0">:</h2>
											</td>
											<td class="text-end text-center">
												<h2 class="font-weight-bolder text-success py-1 m-0"><?php echo $this->pembulatan($capaian_all); ?>%</h2>
											</td>
										</tr>
										<?php echo $html_capaian; ?>
									</table>
								</div>
								<!--end::Title-->
							</div>
						</div>
					</div>
					<div class="row mb-5">
						<div class="col-md-6 offset-md-3 offset-sm-0">
							<div class="card card-primary" style="box-shadow: 1px 1px 5px #666;">
								<div class="card-header bg-primary text-white p-5">
									<div class="col-12 text-center">
										<h2 class="m-0 p-0 col-md-12 lh-lg text-white">Nomenklatur Rencana Strategis</h2>
									</div>
								</div>
								<div class="card-body">
									<div class="row mb-5">
										<div class="col-6 text-center" style="font-size:1.3em; border-right:1px solid #666;">
											<p>Tujuan</p>
											<p><?php echo $no_tujuan; ?></p>
										</div>
										<div class="col-6 text-center" style="font-size:1.3em;">
											<p>Sasaran</p>
											<p><?php echo $no_sasaran; ?></p>
										</div>
									</div>
									<div class="row mb-5">
										<div class="col-4 text-center" style="font-size:1.3em; border-right:1px solid #666;">
											<p>Program</p>
											<p><?php echo $no_program; ?></p>
										</div>
										<div class="col-4 text-center" style="font-size:1.3em; border-right:1px solid #666;">
											<p>Kegiatan</p>
											<p><?php echo $no_kegiatan; ?></p>
										</div>
										<div class="col-4 text-center" style="font-size:1.3em;">
											<p>Sub Kegiatan</p>
											<p><?php echo $no_sub_kegiatan; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row" id="chart-sasaran"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id='aksi-wp-sipd'></div>
<div id="cetak" title="Laporan MONEV RENSTRA" style="padding: 5px; overflow: auto; height: 80vh;">
	<table cellpadding="2" cellspacing="0" id="table-renstra" contenteditable="false">
		<thead>
			<?php
			$row_head = '<tr>
				<th style="width: 85px;" rowspan="2" class="row_head_1 atas kiri kanan bawah text_tengah text_blok">No</th>
				<th style="width: 200px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Sasaran ' . $jenis_jadwal_relasi . '</th>
				<th style="width: 200px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Bidang Urusan</th>
				<th style="width: 200px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Tujuan</th>
				<th style="width: 200px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Sasaran</th>
				<th style="width: 200px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Program</th>
				<th style="width: 200px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Kegiatan</th>
				<th style="width: 200px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Sub Kegiatan</th>
				<th style="width: 300px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Indikator</th>
				<th style="width: 100px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Target Awal</th>';
			for ($i = 1; $i <= $lama_pelaksanaan; $i++) {
				$row_head .= '<th style="width: 300px;" colspan="4" class="row_head_1_tahun atas kanan bawah text_tengah text_blok">Tahun ' . $tahun_anggaran_renstra[$i] . '</th>';
			}
			$row_head .= '
				<th style="width: 100px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Target Akhir</th>
				<th style="width: 100px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Satuan</th>
				<th style="width: 150px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">Sub Unit Pelaksana</th>
				<th style="width: 150px;" rowspan="2" class="row_head_1 atas kanan bawah text_tengah text_blok">keterangan</th>
			</tr>
			<tr>';
			for ($i = 1; $i <= $lama_pelaksanaan; $i++) {
				$row_head .= '
					<th class="row_head_2 atas kanan bawah text_tengah text_blok">Target</th>
					<th class="row_head_2 atas kanan bawah text_tengah text_blok">Realisasi Target</th>
					<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Pagu</th>
					<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Realisasi Anggaran</th>';
			}
			echo $row_head;
			?>
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
				<?php
				$target_temp = 10;
				for ($i = 1; $i <= $lama_pelaksanaan; $i++) {
					if ($i != 1) {
						$target_temp = $pagu_temp + 1;
					}
					$pagu_temp = $target_temp + 1;
				?>
					<th class='atas kanan bawah text_tengah text_blok'><?php echo $target_temp ?></th>
					<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp ?></th>
				<?php
				}
				?>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 1 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 2 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 3 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 4 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 5 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 6 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 7 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 8 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 9 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 10 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 11 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 12 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 13 ?></th>
				<th class='atas kanan bawah text_tengah text_blok'><?php echo $pagu_temp + 14 ?></th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body_monev; ?>
		</tbody>
	</table>
</div>

<div class="hide-print" id="catatan_dokumentasi" style="max-width: 1200px; margin: auto;">
	<h4 style="margin: 30px 0 10px; font-weight: bold;">Catatan Dokumentasi:</h4>
	<ul>
		<li>Background warna biru adalah baris tujuan.</li>
		<li>Background warna kuning adalah baris sasaran.</li>
		<li>Background warna hijau adalah baris program.</li>
		<li>Background warna biru muda adalah baris kegiatan</li>
		<li>Background warna putih adalah baris sub kegiatan</li>
		<li>Data dengan latar belakang warna orange menandakan Tujuan Renstra tidak terhubung dengan sasaran RPJM.</li>
		<li>Data dengan latar belakang warna merah menandakan Tujuan Renstra atau Sasaran Renstra tidak terhubung.</li>
		<li>Debug Cascading Renstra digunakan untuk menampilkan detail data dari TUJUAN hingga SUB KEGIATAN.</li>
		<li>Status Koneksi RPJM menunjukan keterkaitan antara TUJUAN RENSTRA dengan SASARAN RPJM dan dapat diakses untuk melihat detail hierarkinya.</li>
	</ul>
</div>

<div class="modal fade" id="modal-monev" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
	<div class="modal-dialog modal-lg" role="document" style="min-width: 1000px;">
		<div class="modal-content" style="min-width: 850px" ;>
			<div class="modal-header bgpanel-theme">
				<h4 style="margin: 0;" class="modal-title" id="">Edit MONEV Indikator RENSTRA Per Tahun Anggaran</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
			</div>
			<div class="modal-body">
				<form>
					<input type="hidden" id="id_indikator">
					<input type="hidden" id="type_indikator">
					<input type="hidden" id="target_indikator">
					<div class="form-group">
						<table class="table table-bordered">
							<tbody>
								<tr>
									<th style="width: 200px;" id="jenis-indikator">Tujuan / Sasaran / Program / Kegiatan</th>
									<td id="monev-nama"></td>
								</tr>
								<tr>
									<th style="width: 200px;">Indikator</th>
									<td id="indikator-nama"></td>
								</tr>
								<tr>
									<th style="width: 200px;">Satuan</th>
									<td id="satuan-nama"></td>
								</tr>
								<tr>
									<th style="width: 200px;">Target Awal</th>
									<td id="target-awal-nama"></td>
								</tr>
								<tr>
									<th style="width: 200px;">Target Akhir</th>
									<td id="target-akhir-nama"></td>
								</tr>
								<tr>
									<td colspan="2">
										<table class="display-indikator-renstra">
											<thead>
												<tr>
													<th class="text_tengah" style="width: 50px;">Tahun</th>
													<th class="text_tengah" style="width: 140px;">Anggaran</th>
													<th class="text_tengah" style="width: 140px;">Realisasi</th>
													<th class="text_tengah" style="width: 50px;">Capaian Penyerapan</th>
													<th class="text_tengah" style="width: 50px;">Target</th>
													<th class="text_tengah" style="width: 50px;">Realisasi Target</th>
													<th class="text_tengah" style="width: 50px;">Capaian Target</th>
													<th class="text_tengah">Keteragan</th>
												</tr>
												<tr>
													<th class="text_tengah">1</th>
													<th class="text_tengah">2</th>
													<th class="text_tengah">3</th>
													<th class="text_tengah">4=(3/2)*100</th>
													<th class="text_tengah">5</th>
													<th class="text_tengah">6</th>
													<th class="text_tengah">7=(6/5)*100</th>
													<th class="text_tengah">8</th>
												</tr>
											</thead>
											<tbody id="monev-body-renstra">
											</tbody>
											<tfoot>
												<tr>
													<th class="text_tengah">Total</th>
													<th class="text_kanan" id="total_pagu">0</th>
													<th class="text_kanan" id="total_realisasi_pagu">0</th>
													<th class="text_tengah" id="total_capaian_pagu">0</th>
													<th class="text_tengah" colspan="4"></th>
												</tr>
											</tfoot>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success simpan-monev-renstra">Simpan</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-rpjmd" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
	<div class="modal-dialog" style="min-width:1200px" role="document">
		<div class="modal-content">
			<div class="modal-header bgpanel-theme">
				<h5 class="modal-title" id="exampleModalLabel" style="margin: 0 auto; text-align:center; font-weight: bold"></h5>
			</div>
			<div class="modal-body">
				<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
					<thead>
						<tr>
							<th style="width: 100px;" class='atas kanan bawah text_tengah text_blok'>Visi</th>
							<th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Misi</th>
							<th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Tujuan</th>
							<th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Sasaran</th>
							<th style="width: 250px;" class='atas kanan bawah text_tengah text_blok'>Program</th>
							<th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Indikator RPJMD (Tujuan, Sasaran, Program)</th>
						</tr>
					</thead>
					<tbody id="body-rpjmd">
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	run_download_excel('', '#aksi-wp-sipd');
	var data_all = <?php echo json_encode($data_all); ?>;

	jQuery(document).on('ready', function() {
		var aksi = '' +
			'<a style="margin-left: 10px;" onclick="copy_renstra_local(); return false;" href="#" class="btn btn-danger">Copy Data Renstra Lokal</a>' +
			'<h3 style="margin-top: 20px;">SETTING</h3>' +
			'<label class="action-checkbox"><input type="checkbox" onclick="edit_monev_indikator(this);"> Edit Monev indikator</label>&nbsp;' +
			'<label class="action-checkbox"><input type="checkbox" onclick="debug_renstra(this);" id="debug-renstra"> Debug Cascading Renstra</label>' +
			'<label class="action-checkbox">' +
			'Sembunyikan Baris ' +
			'<select id="sembunyikan-baris" onchange="sembunyikan_baris(this);" style="padding: 5px 10px; min-width: 200px;">' +
			'<option value="">Pilih Baris</option>' +
			'<option value="tr-tujuan">Tujuan</option>' +
			'<option value="tr-sasaran">Sasaran</option>' +
			'<option value="tr-program">Program</option>' +
			'<option value="tr-kegiatan">Kegiatan</option>' +
			'</select>' +
			'</label>'
		jQuery('#action-sipd').append(aksi);
		jQuery('#debug-renstra').click();
		jQuery('.edit-monev').on('click', function() {
			jQuery('#wrap-loading').show();
			var tr = jQuery(this).closest('tr');
			var nama = tr.find('span.data-renstra').text();
			var kode = jQuery(this).attr("data-id");
			var rinc_kode = kode.split('-');

			jQuery("#table_rumus_indikator").show();
			jQuery("#monev-body-renstra").html('');
			jQuery.ajax({
				url: '<?php echo admin_url("admin-ajax.php") ?>',
				type: 'post',
				data: {
					'action': 'get_monev_renstra',
					"api_key": "<?php echo $api_key; ?>",
					'tahun_anggaran': rinc_kode[0],
					'id_skpd': rinc_kode[1],
					'id': rinc_kode[2],
					'type_indikator': rinc_kode[3],
					'lama_pelaksanaan': <?php echo $lama_pelaksanaan; ?>,
					'tahun_awal': <?php echo $awal_rpjmd; ?>
				},
				dataType: 'json',
				success: function(res) {
					if (res.status == 'success') {
						jQuery("#monev-nama").html(nama);
						jQuery("#monev-body-renstra").html(res.body_renstra);
						jQuery("#id_indikator").val(rinc_kode[2]);
						jQuery("#type_indikator").val(rinc_kode[3]);
						var jenis_indikator = 'Tujuan';
						if (rinc_kode[3] == 2) {
							jenis_indikator = 'Sasaran';
						} else if (rinc_kode[3] == 3) {
							jenis_indikator = 'Program';
						} else if (rinc_kode[3] == 4) {
							jenis_indikator = 'Kegiatan';
						} else if (rinc_kode[3] == 5) {
							jenis_indikator = 'Sub Kegiatan';
						}
						jQuery("#jenis-indikator").html(jenis_indikator);
						jQuery("#indikator-nama").html(res.indikator);
						jQuery("#satuan-nama").html(res.satuan);
						jQuery("#target-awal-nama").html(res.target_awal + ' ' + res.satuan);
						jQuery("#target-akhir-nama").html(res.target_akhir + ' ' + res.satuan);
						setTotalRealisasi();
						jQuery('#modal-monev').modal('show');
					} else {
						alert(res.message);
					}
					jQuery('#wrap-loading').hide();
				}
			})

		});

		jQuery(".simpan-monev-renstra").on('click', function() {
			jQuery('#wrap-loading').show();
			var realisasi_anggaran = {};
			var realisasi_target = {};
			var keterangan = {};

			for (var i = 1; i <= <?php echo $lama_pelaksanaan; ?>; i++) {
				realisasi_anggaran[i] = to_number(jQuery('.realisasi_pagu_' + i).html());
				realisasi_target[i] = to_number(jQuery('.realisasi_target_' + i).html());
				keterangan[i] = jQuery('.keterangan_' + i).html();
			}

			jQuery.ajax({
				url: '<?php echo admin_url("admin-ajax.php") ?>',
				type: 'post',
				data: {
					'action': 'save_monev_renstra',
					"api_key": "<?php echo $api_key; ?>",
					'id_indikator': jQuery("#id_indikator").val(),
					'type_indikator': jQuery("#type_indikator").val(),
					'realisasi_anggaran': realisasi_anggaran,
					'realisasi_target': realisasi_target,
					'keterangan': keterangan,
					'id_jadwal': "<?php echo $input['id_jadwal']; ?>"
				},
				dataType: 'json',
				success: function(result) {
					alert(result.message);
					jQuery('#wrap-loading').hide();
					if (result.status == 'success') {
						jQuery('#modal-monev').modal('hide');
					}
				}
			});
		});

		google.charts.load('current', {
			packages: ['corechart', 'bar']
		});
		google.charts.setOnLoadCallback(drawColColors);
	});

	function drawColColors() {
		var data_cart = <?php echo json_encode($data_all_js); ?>;

		var data = new google.visualization.arrayToDataTable(data_cart);

		var options = {
			title: 'ANGGARAN DAN REALISASI',
			colors: ['#ffc107', '#007bff'],
			hAxis: {
				title: 'TAHUN',
				minValue: 0
			},
			vAxis: {
				title: 'Rp'
			}
		};

		var chart = new google.visualization.ColumnChart(document.getElementById('chart'));
		chart.draw(data, options);

		var no = 0;
		for (var i in data_all['data']) {
			for (var ii in data_all['data'][i]['data']) {
				var sasaran = data_all['data'][i]['data'][ii];
				no++;
				var id_cart = 'chart-sasaran-' + no;
				var html = '<div id="' + id_cart + '" style="margin-buttom: 20px; min-height: 400px;" class="col-md-6"></div>';
				var id_cart_indikator = {};
				for (var iii in sasaran.indikator) {
					var id_indikator = sasaran.indikator[iii].id
					id_cart_indikator[id_indikator] = id_cart + '-indikator-' + id_indikator;
					html += '<div id="' + id_cart_indikator[id_indikator] + '" style="margin-buttom: 20px; min-height: 400px;" class="col-md-6"></div>';
				};
				jQuery('#chart-sasaran').append('<div class="col-md-12"><h3 class="text-center" style="margin-top: 30px;">' + sasaran.nama + '</h3></div>' + html);
				var data_cart = [
					['Tahun', 'Anggaran', 'Realisasi']
				];
				for (var t = 1; t <= data_all.lama_pelaksanaan; t++) {
					data_cart.push(['Tahun  ' + t, sasaran['pagu_' + t], sasaran['realisasi_pagu_' + t]]);
				}

				var data = new google.visualization.arrayToDataTable(data_cart);

				var capaian = 0;
				if (sasaran.pagu > 0 && sasaran.realisasi > 0) {
					capaian = Math.round((sasaran.realisasi / sasaran.pagu) * 100);
				}
				var options = {
					title: 'Pagu sasaran: ' + formatRupiah(sasaran.pagu) + ', Realisasi: ' + formatRupiah(sasaran.realisasi) + ', Capaian: ' + capaian + '%',
					colors: ['#9575cd', '#33ac71'],
					hAxis: {
						title: 'Anggaran dan Realisasi Per Tahun',
						minValue: 0
					},
					vAxis: {
						title: 'Rp'
					}
				};
				var chart = new google.visualization.ColumnChart(document.getElementById(id_cart));
				chart.draw(data, options);

				for (var iii in sasaran.indikator) {
					var id_indikator = sasaran.indikator[iii].id;
					var data_cart = [
						['Tahun', 'Target', 'Realisasi']
					];
					for (var t = 1; t <= data_all.lama_pelaksanaan; t++) {
						data_cart.push(['Tahun  ' + t, +sasaran.indikator[iii]['target_' + t], +sasaran.indikator[iii]['realisasi_target_' + t]])
					}
					var data = new google.visualization.arrayToDataTable(data_cart);
					var options = {
						title: 'Indikator: ' + sasaran.indikator[iii].indikator + ', ' + 'Target Awal: ' + sasaran.indikator[iii].target_awal + ' ' + sasaran.indikator[iii].satuan + ', Target Akhir: ' + sasaran.indikator[iii].target_akhir + ' ' + sasaran.indikator[iii].satuan,
						hAxis: {
							title: 'Target dan Realisasi Per Tahun',
							minValue: 0
						},
						vAxis: {
							title: sasaran.indikator[iii].satuan
						}
					};
					console.log('data_cart', data_cart, options);
					var chart = new google.visualization.ColumnChart(document.getElementById(id_cart_indikator[id_indikator]));
					chart.draw(data, options);
				};
			};
		};
	}

	function edit_monev_indikator(that) {
		if (jQuery(that).is(':checked')) {
			jQuery('.edit-monev').show();
		} else {
			jQuery('.edit-monev').hide();
		}
	}

	function sembunyikan_baris(that) {
		var val = jQuery(that).val();
		var tr_tujuan = jQuery('.tr-tujuan');
		var tr_sasaran = jQuery('.tr-sasaran');
		var tr_program = jQuery('.tr-program');
		var tr_kegiatan = jQuery('.tr-kegiatan');
		tr_tujuan.show();
		tr_sasaran.show();
		tr_program.show();
		tr_kegiatan.show();
		if (val == 'tr-tujuan') {
			tr_tujuan.hide();
			tr_sasaran.hide();
			tr_program.hide();
			tr_kegiatan.hide();
		} else if (val == 'tr-sasaran') {
			tr_sasaran.hide();
			tr_program.hide();
			tr_kegiatan.hide();
		} else if (val == 'tr-program') {
			tr_program.hide();
			tr_kegiatan.hide();
		} else if (val == 'tr-kegiatan') {
			tr_kegiatan.hide();
		}
	}

	function debug_renstra(that) {
		if (jQuery(that).is(':checked')) {
			jQuery('.debug-renstra').show();
			jQuery('.nondebug-renstra').hide();
		} else {
			jQuery('.debug-renstra').hide();
			jQuery('.nondebug-renstra').show();
		}
	}

	function show_rpjm(id_jadwal_rpjm, id_unit, kode_sasaran_rpjm) {
		jQuery('#wrap-loading').show();
		var modal = jQuery("#modal-rpjmd");
		jQuery.ajax({

			url: "<?php echo admin_url("admin-ajax.php") ?>",
			type: "post",
			data: {
				"action": "get_data_rpjm",
				"api_key": "<?php echo $api_key; ?>",
				"id_jadwal_rpjm": "<?php echo $input['id_jadwal']; ?>",
				"id_unit": id_unit,
				"kode_sasaran_rpjm": kode_sasaran_rpjm
			},
			dataType: "json", 
			success: function(response) {
				if (response.status == 1) {
					modal.find("#body-rpjmd").html('');
					modal.find("#body-rpjmd").html(response.body_rpjm);
					modal.find('.modal-title').html('RPJMD <br> <?php echo $unit[0]['kode_skpd'] . '&nbsp;' . $unit[0]['nama_skpd'] . '<br>Tahun ' . $tahun_anggaran_sipd . ' <br> ' . $nama_pemda; ?>');
				}
				modal.modal('show');
				jQuery('#wrap-loading').hide();
			}
		});

	}

	function copy_renstra_local() {
		if (confirm('Copy data Tujuan dan Sasaran Renstra Lokal?')) {
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: "<?php echo admin_url("admin-ajax.php") ?>",
				type: "post",
				data: {
					"action": "copy_data_renstra_lokal",
					"api_key": "<?php echo $api_key; ?>",
					'id_jadwal': "<?php echo $input['id_jadwal']; ?>",
					"id_unit": jQuery("#id_skpd").val(),
				},
				dataType: "json",
				success: function(response) {
					alert(response.message);
					location.reload();
				}
			});
		}
	}

	function setTotalRealisasi() {
		var total_pagu = 0;
		var total_realisasi_pagu = 0;
		jQuery('#monev-body-renstra tr').map(function(i, b) {
			var tr = jQuery(b);
			var no = i + 1;
			var pagu = to_number(tr.find('.pagu_' + no).html());
			var realisasi_pagu = to_number(tr.find('.realisasi_pagu_' + no).html());
			var capaian_pagu = 0;
			if (pagu >= 1 && realisasi_pagu >= 1) {
				capaian_pagu = Math.round((realisasi_pagu / pagu) * 100);
			}
			tr.find('.capaian_pagu_' + no).html(capaian_pagu);
			total_pagu += +pagu;
			total_realisasi_pagu += +realisasi_pagu;

			var target = to_number(tr.find('.target_' + no).html());
			var realisasi_target = to_number(tr.find('.realisasi_target_' + no).html());
			var capaian_target = 0;
			if (target >= 1 && realisasi_target >= 1) {
				capaian_target = Math.round((+realisasi_target / +target) * 100);
			}
			tr.find('.capaian_target_' + no).html(capaian_target);
		});
		var total_capaian_pagu = 0;
		if (total_realisasi_pagu >= 1 && total_pagu >= 1) {
			total_capaian_pagu = Math.round((total_realisasi_pagu / total_pagu) * 100);
		}
		jQuery('#total_pagu').html(formatRupiah(total_pagu));
		jQuery('#total_realisasi_pagu').html(formatRupiah(total_realisasi_pagu));
		jQuery('#total_capaian_pagu').html(total_capaian_pagu);
	}
</script>