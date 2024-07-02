<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $wpdb;
$input = shortcode_atts(array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts);

$api_key = get_option('_crb_api_key_extension');
$nama_pemda = get_option('_crb_daerah');
$current_user = wp_get_current_user();
$bulan = date('m');

$sql = $wpdb->prepare("
	select 
		*
	from data_unit
	WHERE tahun_anggaran = %d
		and active = 1
		and is_skpd = 1
	order by id_skpd asc
", $input['tahun_anggaran']);

$unit = $wpdb->get_results($sql, ARRAY_A);

$body_monev = '';

$total_all_realisasi_triwulan_1 = 0;
$total_all_realisasi_triwulan_2 = 0;
$total_all_realisasi_triwulan_3 = 0;
$total_all_realisasi_triwulan_4 = 0;

$total_all_pagu_triwulan_1 = 0;
$total_all_pagu_triwulan_2 = 0;
$total_all_pagu_triwulan_3 = 0;
$total_all_pagu_triwulan_4 = 0;

$total_pagu_triwulan = 0;
$total_all_realisasi_triwulan = 0;
$total_all_pagu_triwulan = 0;
$total_all_selisih = 0;
$persen_all = 0;
$no = 0;

foreach ($unit as $skpd) {
	$subunit = $wpdb->get_results($wpdb->prepare("
		SELECT 
			*
		FROM data_unit
		WHERE active = 1
			AND tahun_anggaran = %d
			AND is_skpd = 0
			AND id_unit = %d
		ORDER BY kode_skpd ASC
	", $input['tahun_anggaran'], $skpd['id_skpd']), ARRAY_A);
	
	$subkeg = $wpdb->get_results($wpdb->prepare("
			SELECT 
				k.*, k.id AS id_sub_keg, 
				r.rak, 
				r.realisasi_anggaran, 
				r.id AS id_rfk, 
				r.realisasi_fisik
			FROM data_sub_keg_bl k
			LEFT JOIN data_rfk r ON k.kode_sbl = r.kode_sbl
				AND k.tahun_anggaran = r.tahun_anggaran
				AND k.id_sub_skpd = r.id_skpd
				AND r.bulan = %d
			WHERE k.tahun_anggaran = %d
				AND k.active = 1
				AND k.id_sub_skpd = %d
			ORDER BY kode_sub_giat ASC
		", $bulan, $input['tahun_anggaran'], $skpd['id_skpd']), ARRAY_A);

		$data_all = array(
			'triwulan' => array_fill(1, 4, 0),
			'pagu_triwulan' => array_fill(1, 4, 0),
			'data' => array()
		);

		foreach ($subkeg as $sub) {
			$total_pagu = $sub['pagu'];
			$rfk_all = $wpdb->get_results($wpdb->prepare("
				SELECT realisasi_anggaran, 
				bulan, 
				rak
				FROM data_rfk
				WHERE tahun_anggaran = %d
					AND id_skpd = %d
					AND kode_sbl = %s
				ORDER BY bulan ASC
			", $input['tahun_anggaran'], $skpd['id_skpd'], $sub['kode_sbl']), ARRAY_A);

			$triwulans = array_fill(1, 4, 0);
			$pagu_triwulans = array_fill(1, 4, 0);
			$realisasi_bulan_all = array();
			$pagu_bulan_all = array();

			foreach ($rfk_all as $vvv) {
				$realisasi_bulan_all[$vvv['bulan']] = $vvv['realisasi_anggaran'];
				$pagu_bulan_all[$vvv['bulan']] = $vvv['rak'];
				if (!empty($vvv['realisasi_anggaran'])) {
					if ($vvv['bulan'] <= 3) {
						$triwulans[1] = $vvv['realisasi_anggaran'];
						$pagu_triwulans[1] = $vvv['rak'];
					} elseif ($vvv['bulan'] <= 6) {
						$triwulans[2] = $vvv['realisasi_anggaran'] - $realisasi_bulan_all[3];
						$pagu_triwulans[2] = $vvv['rak'] - $pagu_bulan_all[3];
					} elseif ($vvv['bulan'] <= 9) {
						$triwulans[3] = $vvv['realisasi_anggaran'] - $realisasi_bulan_all[6];
						$pagu_triwulans[3] = $vvv['rak'] - $pagu_bulan_all[6];
					} else {
						$triwulans[4] = $vvv['realisasi_anggaran'] - $realisasi_bulan_all[9];
						$pagu_triwulans[4] = $vvv['rak'] - $pagu_bulan_all[9];
					}
				}
			}

			$kode_sbl_s = explode('.', $sub['kode_sbl']);
			$kode_urusan = $sub['kode_urusan'];
			$kode_bidang = $sub['kode_bidang_urusan'];
			$kode_program = $sub['kode_program'];
			$kode_giat = $sub['kode_giat'];
			$kode_sub_giat = $sub['kode_sub_giat'];

			if (!isset($data_all['data'][$kode_urusan])) {
				$data_all['data'][$kode_urusan] = array(
					'triwulan' => array_fill(1, 4, 0),
					'pagu_triwulan' => array_fill(1, 4, 0),
					'data' => array()
				);
			}
			if (!isset($data_all['data'][$kode_urusan]['data'][$kode_bidang])) {
				$data_all['data'][$kode_urusan]['data'][$kode_bidang] = array(
					'triwulan' => array_fill(1, 4, 0),
					'pagu_triwulan' => array_fill(1, 4, 0),
					'data' => array()
				);
			}
			if (!isset($data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program])) {
				$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program] = array(
					'triwulan' => array_fill(1, 4, 0),
					'pagu_triwulan' => array_fill(1, 4, 0),
					'data' => array()
				);
			}
			if (!isset($data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat])) {
				$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat] = array(
					'triwulan' => array_fill(1, 4, 0),
					'pagu_triwulan' => array_fill(1, 4, 0),
					'data' => array()
				);
			}
			if (!isset($data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat])) {
				$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat] = array(
					'triwulan' => array_fill(1, 4, 0),
					'pagu_triwulan' => array_fill(1, 4, 0),
					'data' => $sub
				);
			}

			$data_all['data'][$kode_urusan]['triwulan'][1] += $triwulans[1];
			$data_all['data'][$kode_urusan]['triwulan'][2] += $triwulans[2];
			$data_all['data'][$kode_urusan]['triwulan'][3] += $triwulans[3];
			$data_all['data'][$kode_urusan]['triwulan'][4] += $triwulans[4];
			$data_all['data'][$kode_urusan]['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['data'][$kode_urusan]['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['data'][$kode_urusan]['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['data'][$kode_urusan]['pagu_triwulan'][4] += $pagu_triwulans[4];

			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['triwulan'][1] += $triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['triwulan'][2] += $triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['triwulan'][3] += $triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['triwulan'][4] += $triwulans[4];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['pagu_triwulan'][4] += $pagu_triwulans[4];

			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['triwulan'][1] += $triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['triwulan'][2] += $triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['triwulan'][3] += $triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['triwulan'][4] += $triwulans[4];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['pagu_triwulan'][4] += $pagu_triwulans[4];

			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['triwulan'][1] += $triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['triwulan'][2] += $triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['triwulan'][3] += $triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['triwulan'][4] += $triwulans[4];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['pagu_triwulan'][4] += $pagu_triwulans[4];

			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['triwulan'][1] += $triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['triwulan'][2] += $triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['triwulan'][3] += $triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['triwulan'][4] += $triwulans[4];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['pagu_triwulan'][4] += $pagu_triwulans[4];

			$data_all['triwulan'][1] += $triwulans[1];
			$data_all['triwulan'][2] += $triwulans[2];
			$data_all['triwulan'][3] += $triwulans[3];
			$data_all['triwulan'][4] += $triwulans[4];
			$data_all['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['pagu_triwulan'][4] += $pagu_triwulans[4];

		}

		$total_realisasi_triwulan = $data_all['triwulan'][1] + $data_all['triwulan'][2] + $data_all['triwulan'][3] + $data_all['triwulan'][4];
		$total_pagu_triwulan = $data_all['pagu_triwulan'][1] + $data_all['pagu_triwulan'][2] + $data_all['pagu_triwulan'][3] + $data_all['pagu_triwulan'][4];
		$persen = $total_pagu_triwulan > 0 ? round($total_realisasi_triwulan / $total_pagu_triwulan * 100, 2) : 0;
		$selisih = $total_pagu_triwulan - $total_realisasi_triwulan;
		$no++;
		$body_monev .= '
			<tr>
				<td class="atas kanan bawah kiri text_tengah">'.$no.'</td>
				<td class="atas kanan bawah kiri text_kiri">'.$skpd['nama_skpd'].'</td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_1"><span>'.number_format($data_all['pagu_triwulan'][1],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_1"><span>'.number_format($data_all['triwulan'][1],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_2"><span>'.number_format($data_all['pagu_triwulan'][2],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_2"><span>'.number_format($data_all['triwulan'][2],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_3"><span>'.number_format($data_all['pagu_triwulan'][3],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_3"><span>'.number_format($data_all['triwulan'][3],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_4"><span>'.number_format($data_all['pagu_triwulan'][4],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_4"><span>'.number_format($data_all['triwulan'][4],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan"><span>'.number_format($total_pagu_triwulan,2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan"><span>'.number_format($total_realisasi_triwulan,2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan"><span>'.number_format($selisih,2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan"><span>'.$persen.'%</span></td>
		    </tr>
		';
	foreach ($subunit as $sub_skpd) {
		$subkeg = $wpdb->get_results($wpdb->prepare("
			SELECT 
				k.*, k.id AS id_sub_keg, 
				r.rak, r.realisasi_anggaran, 
				r.id AS id_rfk, 
				r.realisasi_fisik
			FROM data_sub_keg_bl k
			LEFT JOIN data_rfk r ON k.kode_sbl = r.kode_sbl
				AND k.tahun_anggaran = r.tahun_anggaran
				AND k.id_sub_skpd = r.id_skpd
				AND r.bulan = %d
			WHERE k.tahun_anggaran = %d
				AND k.active = 1
				AND k.id_sub_skpd = %d
			ORDER BY kode_sub_giat ASC
		", $bulan, $input['tahun_anggaran'], $sub_skpd['id_skpd']), ARRAY_A);

		$data_all = array(
			'triwulan' => array_fill(1, 4, 0),
			'pagu_triwulan' => array_fill(1, 4, 0),
			'data' => array()
		);

		foreach ($subkeg as $sub) {
			$total_pagu = $sub['pagu'];
			$rfk_all = $wpdb->get_results($wpdb->prepare("
				SELECT realisasi_anggaran, 
				bulan, 
				rak
				FROM data_rfk
				WHERE tahun_anggaran = %d
					AND id_skpd = %d
					AND kode_sbl = %s
				ORDER BY bulan ASC
			", $input['tahun_anggaran'], $sub_skpd['id_skpd'], $sub['kode_sbl']), ARRAY_A);

			$triwulans = array_fill(1, 4, 0);
			$pagu_triwulans = array_fill(1, 4, 0);
			$realisasi_bulan_all = array();
			$pagu_bulan_all = array();

			foreach ($rfk_all as $vvv) {
				$realisasi_bulan_all[$vvv['bulan']] = $vvv['realisasi_anggaran'];
				$pagu_bulan_all[$vvv['bulan']] = $vvv['rak'];
				if (!empty($vvv['realisasi_anggaran'])) {
					if ($vvv['bulan'] <= 3) {
						$triwulans[1] = $vvv['realisasi_anggaran'];
						$pagu_triwulans[1] = $vvv['rak'];
					} elseif ($vvv['bulan'] <= 6) {
						$triwulans[2] = $vvv['realisasi_anggaran'] - $realisasi_bulan_all[3];
						$pagu_triwulans[2] = $vvv['rak'] - $pagu_bulan_all[3];
					} elseif ($vvv['bulan'] <= 9) {
						$triwulans[3] = $vvv['realisasi_anggaran'] - $realisasi_bulan_all[6];
						$pagu_triwulans[3] = $vvv['rak'] - $pagu_bulan_all[6];
					} else {
						$triwulans[4] = $vvv['realisasi_anggaran'] - $realisasi_bulan_all[9];
						$pagu_triwulans[4] = $vvv['rak'] - $pagu_bulan_all[9];
					}
				}
			}

			$kode_sbl_s = explode('.', $sub['kode_sbl']);
			$kode_urusan = $sub['kode_urusan'];
			$kode_bidang = $sub['kode_bidang_urusan'];
			$kode_program = $sub['kode_program'];
			$kode_giat = $sub['kode_giat'];
			$kode_sub_giat = $sub['kode_sub_giat'];

			if (!isset($data_all['data'][$kode_urusan])) {
				$data_all['data'][$kode_urusan] = array(
					'triwulan' => array_fill(1, 4, 0),
					'pagu_triwulan' => array_fill(1, 4, 0),
					'data' => array()
				);
			}
			if (!isset($data_all['data'][$kode_urusan]['data'][$kode_bidang])) {
				$data_all['data'][$kode_urusan]['data'][$kode_bidang] = array(
					'triwulan' => array_fill(1, 4, 0),
					'pagu_triwulan' => array_fill(1, 4, 0),
					'data' => array()
				);
			}
			if (!isset($data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program])) {
				$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program] = array(
					'triwulan' => array_fill(1, 4, 0),
					'pagu_triwulan' => array_fill(1, 4, 0),
					'data' => array()
				);
			}
			if (!isset($data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat])) {
				$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat] = array(
					'triwulan' => array_fill(1, 4, 0),
					'pagu_triwulan' => array_fill(1, 4, 0),
					'data' => array()
				);
			}
			if (!isset($data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat])) {
				$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat] = array(
					'triwulan' => array_fill(1, 4, 0),
					'pagu_triwulan' => array_fill(1, 4, 0),
					'data' => $sub
				);
			}

			$data_all['data'][$kode_urusan]['triwulan'][1] += $triwulans[1];
			$data_all['data'][$kode_urusan]['triwulan'][2] += $triwulans[2];
			$data_all['data'][$kode_urusan]['triwulan'][3] += $triwulans[3];
			$data_all['data'][$kode_urusan]['triwulan'][4] += $triwulans[4];
			$data_all['data'][$kode_urusan]['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['data'][$kode_urusan]['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['data'][$kode_urusan]['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['data'][$kode_urusan]['pagu_triwulan'][4] += $pagu_triwulans[4];

			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['triwulan'][1] += $triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['triwulan'][2] += $triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['triwulan'][3] += $triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['triwulan'][4] += $triwulans[4];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['pagu_triwulan'][4] += $pagu_triwulans[4];

			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['triwulan'][1] += $triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['triwulan'][2] += $triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['triwulan'][3] += $triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['triwulan'][4] += $triwulans[4];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['pagu_triwulan'][4] += $pagu_triwulans[4];

			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['triwulan'][1] += $triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['triwulan'][2] += $triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['triwulan'][3] += $triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['triwulan'][4] += $triwulans[4];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['pagu_triwulan'][4] += $pagu_triwulans[4];

			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['triwulan'][1] += $triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['triwulan'][2] += $triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['triwulan'][3] += $triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['triwulan'][4] += $triwulans[4];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['data'][$kode_urusan]['data'][$kode_bidang]['data'][$kode_program]['data'][$kode_giat]['data'][$kode_sub_giat]['pagu_triwulan'][4] += $pagu_triwulans[4];

			$data_all['triwulan'][1] += $triwulans[1];
			$data_all['triwulan'][2] += $triwulans[2];
			$data_all['triwulan'][3] += $triwulans[3];
			$data_all['triwulan'][4] += $triwulans[4];
			$data_all['pagu_triwulan'][1] += $pagu_triwulans[1];
			$data_all['pagu_triwulan'][2] += $pagu_triwulans[2];
			$data_all['pagu_triwulan'][3] += $pagu_triwulans[3];
			$data_all['pagu_triwulan'][4] += $pagu_triwulans[4];

		}

		$total_realisasi_triwulan = $data_all['triwulan'][1] + $data_all['triwulan'][2] + $data_all['triwulan'][3] + $data_all['triwulan'][4];
		$total_pagu_triwulan = $data_all['pagu_triwulan'][1] + $data_all['pagu_triwulan'][2] + $data_all['pagu_triwulan'][3] + $data_all['pagu_triwulan'][4];
		$persen = $total_pagu_triwulan > 0 ? round($total_realisasi_triwulan / $total_pagu_triwulan * 100, 2) : 0;
		$selisih = $total_pagu_triwulan - $total_realisasi_triwulan;
		$no++;
		$body_monev .= '
			<tr>
				<td class="atas kanan bawah kiri text_tengah">'.$no.'</td>
				<td class="atas kanan bawah kiri text_kiri">'.$sub_skpd['nama_skpd'].'</td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_1"><span>'.number_format($data_all['pagu_triwulan'][1],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_1"><span>'.number_format($data_all['triwulan'][1],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_2"><span>'.number_format($data_all['pagu_triwulan'][2],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_2"><span>'.number_format($data_all['triwulan'][2],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_3"><span>'.number_format($data_all['pagu_triwulan'][3],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_3"><span>'.number_format($data_all['triwulan'][3],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_4"><span>'.number_format($data_all['pagu_triwulan'][4],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan triwulan_4"><span>'.number_format($data_all['triwulan'][4],2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan"><span>'.number_format($total_pagu_triwulan,2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan"><span>'.number_format($total_realisasi_triwulan,2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan"><span>'.number_format($selisih,2,",",".").'</span></td>
		        <td class="atas kanan bawah kiri text_kanan"><span>'.$persen.'%</span></td>
		    </tr>
		';
	}
		//tfoot
		$total_all_realisasi_triwulan_1 += $data_all['triwulan'][1];
		$total_all_realisasi_triwulan_2 += $data_all['triwulan'][2];
		$total_all_realisasi_triwulan_3 += $data_all['triwulan'][3];
		$total_all_realisasi_triwulan_4 += $data_all['triwulan'][4];

		$total_all_pagu_triwulan_1 += $data_all['pagu_triwulan'][1];
		$total_all_pagu_triwulan_2 += $data_all['pagu_triwulan'][2];
		$total_all_pagu_triwulan_3 += $data_all['pagu_triwulan'][3];
		$total_all_pagu_triwulan_4 += $data_all['pagu_triwulan'][4];

		$total_all_realisasi_triwulan += $total_realisasi_triwulan;
		$total_all_pagu_triwulan = $total_all_pagu_triwulan_1 + $total_all_pagu_triwulan_2 + $total_all_pagu_triwulan_3 + $total_all_pagu_triwulan_4;
		$total_all_selisih += $selisih;
		$persen_all = $total_all_pagu_triwulan > 0 ? round($total_all_realisasi_triwulan / $total_all_pagu_triwulan * 100, 2) : 0;
}

?>
<style type="text/css">
	#tabel-monitor-monev-renja {
		font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; 
		border-collapse: collapse; 
		font-size: 70%; 
		border: 0; 
		table-layout: fixed;
	}
	#tabel-monitor-monev-renja thead{
	  	position: sticky;
	  	top: -6px;
	  	background: #ffc491;
	}
	#tabel-monitor-monev-renja tfoot{
	  	position: sticky;
	  	bottom: -6px;
	  	background: #ffc491;
	}
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitor Monev Renja<br><?php echo 'Tahun '.$input['tahun_anggaran'].' '.$nama_pemda; ?></h4>
<div id="cetak" title="Monitor Monev Renja" style="padding: 5px; overflow: auto; max-height: 80vh;">
	<table id="tabel-monitor-monev-renja" cellpadding="2" cellspacing="0" contenteditable="false">
		<thead>
			<tr>
				<th rowspan ="2" class='atas kanan bawah kiri text_tengah text_blok' style="width: 50px;">No</th>
				<th rowspan ="2" class='atas kanan bawah kiri text_tengah text_blok'>Nama SKPD</th>
				<th colspan="2" class='atas kanan bawah kiri text_tengah text_blok'>Triwulan I</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>Triwulan II</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>Triwulan III</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>Triwulan IV</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Total Pagu</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Total Realisasi</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Selisih</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Presentase</th>
			</tr>
			<tr>
				<th class='atas kanan bawah kiri text_tengah text_blok'>Pagu</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi</th>
				<th class='atas kanan bawah text_tengah text_blok'>Pagu</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi</th>
				<th class='atas kanan bawah text_tengah text_blok'>Pagu</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi</th>
				<th class='atas kanan bawah text_tengah text_blok'>Pagu</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body_monev; ?>
		</tbody>
		<tfoot>
			<tr>
				<th class='atas kanan bawah kiri text_tengah text_blok' colspan="2">Total</th>
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_pagu_triwulan_1,2,",","."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_realisasi_triwulan_1,2,",","."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_pagu_triwulan_2,2,",","."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_realisasi_triwulan_2,2,",","."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_pagu_triwulan_3,2,",","."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_realisasi_triwulan_3,2,",","."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_pagu_triwulan_4,2,",","."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_realisasi_triwulan_4,2,",","."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_pagu_triwulan,2,",","."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_realisasi_triwulan,2,",","."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_selisih,2,",","."); ?></th>
		        <td class="atas kanan bawah kiri text_kanan"><span><?php echo $persen_all; ?>%</span></td>
			</tr>
		</tfoot>
	</table>
</div>

<script type="text/javascript">
	run_download_excel();
</script>
