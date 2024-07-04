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
	order by kode_skpd asc
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
$total_all_pagu = 0;
$total_all_selisih = 0;
$persen_all = 0;
$no = 0;

$all_unit_sub_skpd = array();
foreach ($unit as $skpd) {
	$all_unit_sub_skpd[] = $skpd;
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
	foreach($subunit as $sub_skpd){
		$all_unit_sub_skpd[] = $sub_skpd;
	}
}

foreach($all_unit_sub_skpd as $skpd){	
	$total_pagu_skpd = $wpdb->get_var($wpdb->prepare("
		SELECT 
			sum(k.pagu) as pagu
		FROM data_sub_keg_bl k
		WHERE k.tahun_anggaran = %d
			AND k.active = 1
			AND k.id_sub_skpd = %d
			and k.pagu > 0
	", $input['tahun_anggaran'], $skpd['id_skpd']));
	$subkeg = $wpdb->get_results($wpdb->prepare("
		SELECT 
			sum(r.rak) as rak, 
			sum(r.realisasi_anggaran) as realisasi_anggaran,
			r.bulan
		FROM data_sub_keg_bl k
		LEFT JOIN data_rfk as r on r.kode_sbl = k.kode_sbl
			AND r.tahun_anggaran = k.tahun_anggaran
		WHERE k.tahun_anggaran = %d
			AND k.active = 1
			AND k.id_sub_skpd = %d
			and k.pagu > 0
		GROUP by r.bulan
		ORDER BY r.bulan ASC
	", $input['tahun_anggaran'], $skpd['id_skpd']), ARRAY_A);

	$triwulan1 = 0;
	$triwulan2 = 0;
	$triwulan3 = 0;
	$triwulan4 = 0;
	$pagu_triwulan1 = 0;
	$pagu_triwulan2 = 0;
	$pagu_triwulan3 = 0;
	$pagu_triwulan4 = 0;
	$realisasi_bulan_all = array();
	$pagu_bulan_all = array();
	foreach ($subkeg as $sub) {
		$realisasi_bulan_all[$sub['bulan']] = $sub['realisasi_anggaran'];
		$pagu_bulan_all[$sub['bulan']] = $sub['rak'];
		if (!empty($sub['realisasi_anggaran'])) {
			if ($sub['bulan'] <= 3) {
				$triwulan1 = $sub['realisasi_anggaran'];
				$pagu_triwulan1 = $sub['rak'];
			} elseif ($sub['bulan'] <= 6 && !empty($realisasi_bulan_all[3])) {
				$triwulan2 = $sub['realisasi_anggaran'] - $realisasi_bulan_all[3];
				$pagu_triwulan2 = $sub['rak'] - $pagu_bulan_all[3];
			} elseif ($sub['bulan'] <= 9 && !empty($realisasi_bulan_all[6])) {
				$triwulan3 = $sub['realisasi_anggaran'] - $realisasi_bulan_all[6];
				$pagu_triwulan3 = $sub['rak'] - $pagu_bulan_all[6];
			} elseif ($sub['bulan'] <= 12 && !empty($realisasi_bulan_all[9])) {
				$triwulan4 = $sub['realisasi_anggaran'] - $realisasi_bulan_all[9];
				$pagu_triwulan4 = $sub['rak'] - $pagu_bulan_all[9];
			}
		}
	}

	$total_realisasi_triwulan = $triwulan1 + $triwulan2 + $triwulan3 + $triwulan4;
	$persen = $total_pagu_skpd > 0 ? round($total_realisasi_triwulan / $total_pagu_skpd * 100, 2) : 0;
	$warning = '';
	if($persen > 100){
		$warning = 'bg-danger';
	}
	$selisih = $total_pagu_skpd - $total_realisasi_triwulan;
	$no++;
	$padding_skpd = 0;
	if($skpd['is_skpd'] == 0){
		$padding_skpd = 'padding-left: 20px; background: #dedeff;';
	}

	$url_skpd = $this->generatePage('MONEV ' . $skpd['nama_skpd'] . ' ' . $skpd['kode_skpd'] . ' | ' . $input['tahun_anggaran'], $input['tahun_anggaran'], '[monitor_monev_renja tahun_anggaran="' . $input['tahun_anggaran'] . '" id_skpd="' . $skpd['id_skpd'] . '"]');
	$body_monev .= '
		<tr class="'.$warning.'">
			<td class="atas kanan bawah kiri text_tengah">'.$no.'</td>
			<td class="atas kanan bawah kiri text_kiri" style="'.$padding_skpd.'"><a target="_blank" href="'.$url_skpd.'">'.$skpd['nama_skpd'].'</a></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_1"><span>'.number_format($pagu_triwulan1,2,",",".").'</span></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_1"><span>'.number_format($triwulan1,2,",",".").'</span></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_2"><span>'.number_format($pagu_triwulan2,2,",",".").'</span></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_2"><span>'.number_format($triwulan2,2,",",".").'</span></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_3"><span>'.number_format($pagu_triwulan3,2,",",".").'</span></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_3"><span>'.number_format($triwulan3,2,",",".").'</span></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_4"><span>'.number_format($pagu_triwulan4,2,",",".").'</span></td>
	        <td class="atas kanan bawah kiri text_kanan triwulan_4"><span>'.number_format($triwulan4,2,",",".").'</span></td>
	        <td class="atas kanan bawah kiri text_kanan"><span>'.number_format($total_pagu_skpd,2,",",".").'</span></td>
	        <td class="atas kanan bawah kiri text_kanan"><span>'.number_format($total_realisasi_triwulan,2,",",".").'</span></td>
	        <td class="atas kanan bawah kiri text_kanan"><span>'.number_format($selisih,2,",",".").'</span></td>
	        <td class="atas kanan bawah kiri text_kanan"><span>'.$persen.'%</span></td>
	    </tr>
	';

	//tfoot
	$total_all_realisasi_triwulan_1 += $triwulan1;
	$total_all_realisasi_triwulan_2 += $triwulan2;
	$total_all_realisasi_triwulan_3 += $triwulan3;
	$total_all_realisasi_triwulan_4 += $triwulan4;

	$total_all_pagu_triwulan_1 += $pagu_triwulan1;
	$total_all_pagu_triwulan_2 += $pagu_triwulan2;
	$total_all_pagu_triwulan_3 += $pagu_triwulan3;
	$total_all_pagu_triwulan_4 += $pagu_triwulan4;

	$total_all_realisasi_triwulan += $total_realisasi_triwulan;
	$total_all_selisih += $selisih;
	$total_all_pagu += $total_pagu_skpd;
}

$persen_all = $total_all_pagu > 0 ? round($total_all_realisasi_triwulan / $total_all_pagu * 100, 2) : 0;

$string_hari_ini = date('H:i, d').' '.$this->get_bulan().' '.date('Y');

?>
<style type="text/css">
	#tabel-monitor-monev-renja {
		font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; 
		border-collapse: collapse; 
		font-size: 70%; 
		border: 0; 
		table-layout: fixed;
	}
	#tabel-monitor-monev-renja th{
		vertical-align: middle;
	}
	#tabel-monitor-monev-renja td, #tabel-monitor-monev-renja th{
		padding: 0.3em 0.3em;
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
<h1 class="text-center">Monitor dan Evaluasi Renja<br><?php echo 'Tahun '.$input['tahun_anggaran'].'<br>'.$nama_pemda; ?></h1>
<h4 class="text-center"><?php echo $string_hari_ini; ?></h4>
<div id="cetak" title="Monitor Monev Renja" style="padding: 5px; overflow: auto; max-height: 80vh;">
	<table id="tabel-monitor-monev-renja" cellpadding="2" cellspacing="0" contenteditable="false">
		<thead>
			<tr>
				<th rowspan ="2" class='atas kanan bawah kiri text_tengah text_blok' style="width: 40px;">No</th>
				<th rowspan ="2" class='atas kanan bawah kiri text_tengah text_blok' style="width: 250px;">Nama SKPD</th>
				<th colspan="2" class='atas kanan bawah kiri text_tengah text_blok' style="width: 218px;">Triwulan I</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok' style="width: 218px;">Triwulan II</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok' style="width: 218px;">Triwulan III</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok' style="width: 218px;">Triwulan IV</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Total Pagu</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Total Realisasi</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Selisih</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok' style="width: 60px;">%</th>
			</tr>
			<tr>
				<th class='atas kanan bawah kiri text_tengah text_blok'>RAK</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi</th>
				<th class='atas kanan bawah text_tengah text_blok'>RAK</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi</th>
				<th class='atas kanan bawah text_tengah text_blok'>RAK</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi</th>
				<th class='atas kanan bawah text_tengah text_blok'>RAK</th>
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
				<th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_all_pagu,2,",","."); ?></th>
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
