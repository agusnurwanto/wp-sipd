<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
$input = shortcode_atts( array(
	'kode_rek' => '',
	'tahun_anggaran' => '2021'
), $atts );
$api_key = get_option( '_crb_api_key_extension' );

$body = '';
$_POST['tahun_anggaran'] = $input['tahun_anggaran'];
$_POST['api_key'] = $api_key;
$_POST['action'] = '';
$spd = $this->get_spd(true);
$no = 0;
$total_all_fmis = 0;
$total_all_simda = 0;
foreach ($spd['data'] as $val) {
	// print_r($val); die();
	$no++;
	$total_fmis = $wpdb->get_var($wpdb->prepare("
		SELECT 
			sum(nilai) as nilai
		FROM data_spd_rinci 
		where tahun_anggaran=%d
			and no_spd=%s
			and active=1
		", 
		$input['tahun_anggaran'], 
		$val->no_spd
	));
	if(!empty($total_fmis)){
		$total_fmis = $total_fmis;
	}else{
		$total_fmis = 0;
	}

	$sql = $wpdb->prepare("
		SELECT 
			sum(nilai) as nilai
		FROM ta_spd_rinc 
		where tahun=%d
			and no_spd=%s
		", 
		$input['tahun_anggaran'], 
		$val->no_spd
	);
	$total = $this->simda->CurlSimda(array(
		'query' => $sql,
		'debug' => 1
	));
	$total_simda = 0;
	if(!empty($total[0]->nilai)){
		$total_simda = $total[0]->nilai;
	}
	$nama_skpd = '';
	if(!empty($val->skpd) && !empty($val->skpd['kode_skpd'])){
		$nama_skpd = $val->skpd['kode_skpd'].'<br>'.$val->skpd['nama_skpd'];
		$title = 'SPD Rinci '.$val->no_spd.' '.$nama_skpd.' | '.$input['tahun_anggaran'];
		$shortcode = '[monitoring_spd_rinci tahun_anggaran="'.$input['tahun_anggaran'].'" no_spd="'.$val->no_spd.'" kd_urusan="'.$val->kd_urusan.'" kd_bidang="'.$val->kd_bidang.'" kd_unit="'.$val->kd_unit.'" kd_sub="'.$val->kd_sub.'" nama_skpd="'.$nama_skpd.'"]';
		$update = false;
		$url_skpd = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, $update);
		$nama_skpd = '<a href="'.$url_skpd.'" target="_blank">'.$nama_skpd.'</a>';
	}
	$background = '';
	if($total_fmis != $total_simda){
		$background = 'background: #ffdbdb';
	}
	$body .= '
		<tr>
			<td class="text-center">'.$no.'</td>
			<td>'.$nama_skpd.'</td>
			<td class="text-center">'.$val->no_spd.'</td>
			<td class="text-center">'.$val->tgl_spd.'</td>
			<td class="text-right" style="'.$background.'">'.number_format($total_fmis, 2, ',', '.').'</td>
			<td class="text-right" style="'.$background.'">'.number_format($total_simda, 2, ',', '.').'</td>
		</tr>
	';
	$total_all_fmis += $total_fmis;
	$total_all_simda += $total_simda;
}
$background_all = '';
if($total_all_fmis != $total_all_simda){
	$background_all = 'background: #ffdbdb';
}
?>

<style type="text/css">
	.warning {
		background: #f1a4a4;
	}
	.hide {
		display: none;
	}
</style>
<div class="cetak">
	<div style="padding: 10px;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
		<h1 class="text-center">Monitoring Data SPD Tahun <?php echo $input['tahun_anggaran']; ?></h1>
		<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center">No</th>
					<th class="text-center">Nama SKPD</th>
					<th class="text-center">No SPD</th>
					<th class="text-center">Tanggal SPD</th>
					<th class="text-center">Total FMIS</th>
					<th class="text-center">Total SIMDA</th>
				</tr>
			</thead>
			<tbody id="data_body">
				<?php echo $body; ?>
			</tbody>
			<tfoot>
				<th colspan="4" class="text-center">Total</th>
				<th class="text-right" style="<?php echo $background_all; ?>"><?php echo number_format($total_all_fmis, 2, ',', '.'); ?></th>
				<th class="text-right" style="<?php echo $background_all; ?>"><?php echo number_format($total_all_simda, 2, ',', '.'); ?></th>
			</tfoot>
		</table>
	</div>
</div>