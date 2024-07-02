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

$api_key = get_option('_crb_api_key_extension' );

$sql = $wpdb->prepare("
	select 
		* 
	from data_unit 
	where tahun_anggaran=%d
		and active=1
	order by nama_skpd ASC
", $input['tahun_anggaran']);
$unit = $wpdb->get_results($sql, ARRAY_A);

$nama_pemda = get_option('_crb_daerah');

$current_user = wp_get_current_user();

$bulan = date('m');

$body_monev = '';

$program_triwulan_1 = 0;
$program_triwulan_2 = 0;
$program_triwulan_3 = 0;
$program_triwulan_4 = 0;

$giat_triwulan_1 = 0;
$giat_triwulan_2 = 0;
$giat_triwulan_3 = 0;
$giat_triwulan_4 = 0;

$sub_giat_triwulan_1 = 0;
$sub_giat_triwulan_2 = 0;
$sub_giat_triwulan_3 = 0;
$sub_giat_triwulan_4 = 0;

$total_triwulan_1 = 0;
$total_triwulan_2 = 0;
$total_triwulan_3 = 0;
$total_triwulan_4 = 0;

$total_all_triwulan = 0;
$no = 0;

foreach($unit as $sub_unit){ 
	$subkeg = $wpdb->get_results($wpdb->prepare("
			select 
				k.*,
				k.id as id_sub_keg, 
				r.rak,
				r.realisasi_anggaran, 
				r.id as id_rfk, 
				r.realisasi_fisik
			from data_sub_keg_bl k
				left join data_rfk r on k.kode_sbl=r.kode_sbl
					AND k.tahun_anggaran=r.tahun_anggaran
					AND k.id_sub_skpd=r.id_skpd
					AND r.bulan=%d
			where k.tahun_anggaran=%d
				and k.active=1
				and k.id_sub_skpd=%d
			order by kode_sub_giat ASC
		", $bulan, $input['tahun_anggaran'], $sub_unit['id_skpd']), ARRAY_A);
	$data_all = array(
		'triwulan_1' => 0,
		'triwulan_2' => 0,
		'triwulan_3' => 0,
		'triwulan_4' => 0,
		'data' => array()
	);
	foreach ($subkeg as $kk => $sub) {

		$total_pagu = $sub['pagu'];
		$rfk_all = $wpdb->get_results($wpdb->prepare("
			select 
				realisasi_anggaran,
				bulan
			from data_rfk
			where tahun_anggaran=%d
				and id_skpd=%d
				and kode_sbl=%s
			order by bulan ASC
		", $input['tahun_anggaran'], $sub_unit['id_skpd'], $sub['kode_sbl']), ARRAY_A);
		$triwulan_1 = 0;
		$triwulan_2 = 0;
		$triwulan_3 = 0;
		$triwulan_4 = 0;
		foreach ($rfk_all as $k => $v) {
			if($v['bulan'] <= 3){
				$triwulan_1 = $v['realisasi_anggaran'];
			}else if($v['bulan'] <= 6){
				$triwulan_2 = $v['realisasi_anggaran']-$triwulan_1;
			}else if($v['bulan'] <= 9){
				$triwulan_3 = $v['realisasi_anggaran']-$triwulan_2;
			}else if($v['bulan'] <= 12){
				$triwulan_4 = $v['realisasi_anggaran']-$triwulan_3;
			}
		}

		$kode_sbl_s = explode('.', $sub['kode_sbl']);
		if(empty($data_all['data'][$sub['kode_urusan']])){
			$data_all['data'][$sub['kode_urusan']] = array(
				'triwulan_1' => 0,
				'triwulan_2' => 0,
				'triwulan_3' => 0,
				'triwulan_4' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])){
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
				'triwulan_1' => 0,
				'triwulan_2' => 0,
				'triwulan_3' => 0,
				'triwulan_4' => 0,
				'data'	=> array()
			);
		}

		$nama = explode(' ', $sub['nama_sub_giat']);
		if($nama[0] !== $sub['kode_sub_giat']){
			$kode_sub_giat_asli = explode('.', $sub['kode_sub_giat']);
		}else{
			$kode_sub_giat_asli = explode('.', $nama[0]);
		}

		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']])){
			$capaian_prog = $wpdb->get_results($wpdb->prepare("
				select 
					* 
				from data_capaian_prog_sub_keg 
				where tahun_anggaran=%d
					and active=1
					and kode_sbl=%s
					and capaianteks != ''
				order by id ASC
			", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

			$kode_sbl = $kode_sbl_s[0].'.'.$kode_sbl_s[1].'.'.$kode_sbl_s[2];
			$realisasi_renja = $wpdb->get_results($wpdb->prepare("
				select
					id_indikator,
					id_rumus_indikator,
					id_unik_indikator_renstra,
					realisasi_bulan_1,
					realisasi_bulan_2,
					realisasi_bulan_3,
					realisasi_bulan_4,
					realisasi_bulan_5,
					realisasi_bulan_6,
					realisasi_bulan_7,
					realisasi_bulan_8,
					realisasi_bulan_9,
					realisasi_bulan_10,
					realisasi_bulan_11,
					realisasi_bulan_12
				from data_realisasi_renja
				where tahun_anggaran=%d
					and tipe_indikator=%d
					and kode_sbl=%s
			", $input['tahun_anggaran'], 3, $kode_sbl), ARRAY_A);
			// echo $wpdb->last_query;
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']] = array(
				'triwulan_1' => 0,
				'triwulan_2' => 0,
				'triwulan_3' => 0,
				'triwulan_4' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']])){
			$output_giat = $wpdb->get_results($wpdb->prepare("
				select 
					* 
				from data_output_giat_sub_keg 
				where tahun_anggaran=%d
					and active=1
					and kode_sbl=%s
				order by id ASC
			", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

			$kode_sbl = $kode_sbl_s[0].'.'.$kode_sbl_s[1].'.'.$kode_sbl_s[2].'.'.$kode_sbl_s[3];
			$realisasi_renja = $wpdb->get_results($wpdb->prepare("
				select
					id_indikator,
					id_rumus_indikator,
					id_unik_indikator_renstra,
					realisasi_bulan_1,
					realisasi_bulan_2,
					realisasi_bulan_3,
					realisasi_bulan_4,
					realisasi_bulan_5,
					realisasi_bulan_6,
					realisasi_bulan_7,
					realisasi_bulan_8,
					realisasi_bulan_9,
					realisasi_bulan_10,
					realisasi_bulan_11,
					realisasi_bulan_12
				from data_realisasi_renja
				where tahun_anggaran=%d
					and tipe_indikator=%d
					and kode_sbl=%s
			", $input['tahun_anggaran'], 2, $kode_sbl), ARRAY_A);
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']] = array(
				'triwulan_1' => 0,
				'triwulan_2' => 0,
				'triwulan_3' => 0,
				'triwulan_4' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']])){
			$output_sub_giat = $wpdb->get_results($wpdb->prepare("
				select 
					* 
				from data_sub_keg_indikator
				where tahun_anggaran=%d
					and active=1
					and kode_sbl=%s
				order by id DESC
			", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

			$realisasi_renja = $wpdb->get_results($wpdb->prepare("
				select
					id_indikator,
					id_rumus_indikator,
					id_unik_indikator_renstra,
					realisasi_bulan_1,
					realisasi_bulan_2,
					realisasi_bulan_3,
					realisasi_bulan_4,
					realisasi_bulan_5,
					realisasi_bulan_6,
					realisasi_bulan_7,
					realisasi_bulan_8,
					realisasi_bulan_9,
					realisasi_bulan_10,
					realisasi_bulan_11,
					realisasi_bulan_12
				from data_realisasi_renja
				where tahun_anggaran=%d
					and tipe_indikator=%d
					and kode_sbl=%s
			", $input['tahun_anggaran'], 1, $sub['kode_sbl']), ARRAY_A);
			$nama = explode(' ', $sub['nama_sub_giat']);
			unset($nama[0]);
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']] = array(
				'triwulan_1' => 0,
				'triwulan_2' => 0,
				'triwulan_3' => 0,
				'triwulan_4' => 0,
				'data'	=> $sub
			);
		}

		$data_all['triwulan_1'] += $triwulan_1;
		$data_all['data'][$sub['kode_urusan']]['triwulan_1'] += $triwulan_1;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_1'] += $triwulan_1;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_1'] += $triwulan_1;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['triwulan_1'] += $triwulan_1;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['triwulan_1'] += $triwulan_1;

		$data_all['triwulan_2'] += $triwulan_2;
		$data_all['data'][$sub['kode_urusan']]['triwulan_2'] += $triwulan_2;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_2'] += $triwulan_2;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_2'] += $triwulan_2;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['triwulan_2'] += $triwulan_2;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['triwulan_2'] += $triwulan_2;

		$data_all['triwulan_3'] += $triwulan_3;
		$data_all['data'][$sub['kode_urusan']]['triwulan_3'] += $triwulan_3;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_3'] += $triwulan_3;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_3'] += $triwulan_3;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['triwulan_3'] += $triwulan_3;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['triwulan_3'] += $triwulan_3;

		$data_all['triwulan_4'] += $triwulan_4;
		$data_all['data'][$sub['kode_urusan']]['triwulan_4'] += $triwulan_4;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_4'] += $triwulan_4;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_4'] += $triwulan_4;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['triwulan_4'] += $triwulan_4;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['triwulan_4'] += $triwulan_4;
	}
	foreach ($data_all['data'] as $kd_urusan => $urusan) {
		foreach ($urusan['data'] as $kd_bidang => $bidang) {
			foreach ($bidang['data'] as $kd_program_asli => $program) {
				$program_triwulan_1 = $program['triwulan_1'];
				$program_triwulan_2 = $program['triwulan_2'];
				$program_triwulan_3 = $program['triwulan_3'];
				$program_triwulan_4 = $program['triwulan_4'];
			}
			foreach ($program['data'] as $kd_giat1 => $giat) {
				$giat_triwulan_1 = $giat['triwulan_1'];
				$giat_triwulan_2 = $giat['triwulan_2'];
				$giat_triwulan_3 = $giat['triwulan_3'];
				$giat_triwulan_4 = $giat['triwulan_4'];
			}
			foreach ($giat['data'] as $kd_sub_giat1 => $sub_giat) {
				$sub_giat_triwulan_1 += $sub_giat['triwulan_1'];
				$sub_giat_triwulan_2 += $sub_giat['triwulan_2'];
				$sub_giat_triwulan_3 += $sub_giat['triwulan_3'];
				$sub_giat_triwulan_4 += $sub_giat['triwulan_4'];
			}
		}
	}
	$total_triwulan_1 = $program_triwulan_1 + $giat_triwulan_1 + $sub_giat_triwulan_1;
	$total_triwulan_2 = $program_triwulan_2 + $giat_triwulan_2 + $sub_giat_triwulan_2;
	$total_triwulan_3 = $program_triwulan_3 + $giat_triwulan_3 + $sub_giat_triwulan_3;
	$total_triwulan_4 = $program_triwulan_4 + $giat_triwulan_4 + $sub_giat_triwulan_4;

	$total_all_triwulan = $total_triwulan_1 + $total_triwulan_2 + $total_triwulan_3 + $total_triwulan_4;
	$no++;
	$body_monev .= '
		<tr>
			<td class="atas kanan bawah kiri text_kanan">'.$no.'</td>
			<td class="atas kanan bawah kiri text_kanan">'.$sub_unit['nama_skpd'].'</td>
			<td class="atas kanan bawah kiri text_kanan"></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_1"><span>'.number_format($total_triwulan_1,0,",",".").'</span></td>
			<td class="atas kanan bawah kiri text_kanan"></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_2"><span>'.number_format($total_triwulan_2,0,",",".").'</span></td>
			<td class="atas kanan bawah kiri text_kanan"></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_3"><span>'.number_format($total_triwulan_3,0,",",".").'</span></td>
			<td class="atas kanan bawah kiri text_kanan"></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_4"><span>'.number_format($total_triwulan_4,0,",",".").'</span></td>
			<td class="atas kanan bawah kiri text_kanan"></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_4"><span>'.number_format($total_all_triwulan,0,",",".").'</span></td>
	    </tr>
	';
}

?>
<input type="hidden" value="<?php echo get_option('_crb_api_key_extension' ); ?>" id="api_key">
<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
<input type="hidden" value="<?php echo $sub_unit['id_skpd']; ?>" id="id_skpd">
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitor Monev Renja<br><?php echo 'Tahun '.$input['tahun_anggaran'].' '.$nama_pemda; ?></h4>
<div id="cetak" title="Monitor Monev Renja" style="padding: 5px; overflow: auto; max-height: 80vh;">
	<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
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
	</table>
</div>

<script type="text/javascript">
	run_download_excel();
</script>
