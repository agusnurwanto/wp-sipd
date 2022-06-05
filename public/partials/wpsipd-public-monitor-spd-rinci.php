<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
$input = shortcode_atts( array(
	'no_spd' => '',
	'kd_urusan' => '',
	'kd_bidang' => '',
	'kd_unit' => '',
	'kd_sub' => '',
	'nama_skpd' => '',
	'tahun_anggaran' => '2021'
), $atts );
$api_key = get_option( '_crb_api_key_extension' );
$_POST['action'] = ''; 
$_POST['api_key'] = $api_key; 
$_POST['tahun_anggaran'] = $input['tahun_anggaran']; 
$_POST['no_spd'] = $input['no_spd']; 
$_POST['kd_urusan'] = $input['kd_urusan']; 
$_POST['kd_bidang'] = $input['kd_bidang']; 
$_POST['kd_unit'] = $input['kd_unit']; 
$_POST['kd_sub'] = $input['kd_sub'];
$spd = $this->get_spd_rinci(true);
$no = 0;
$total_all_fmis = 0;
$total_all_simda = 0;
$body = '';
foreach ($spd['data'] as $val) {
	$no++;
	$total_fmis = $wpdb->get_var($wpdb->prepare("
		SELECT 
			sum(nilai) as nilai
		FROM data_spd_rinci 
		where tahun_anggaran=%d
			and no_spd=%s
			and kdrek1=%s
			and kdrek2=%s
			and kdrek3=%s
			and kdrek4=%s
			and kdrek5=%s
			and kdrek6=%s
			and active=1
		", 
		$input['tahun_anggaran'], 
		$val->no_spd, 
		$val->kd_rek90_1, 
		$val->kd_rek90_2, 
		$val->kd_rek90_3, 
		$val->kd_rek90_4, 
		$val->kd_rek90_5, 
		$val->kd_rek90_6
	));
	if(!empty($total_fmis)){
		$total_fmis = $total_fmis;
	}else{
		$total_fmis = 0;
	}
	$total_simda = $val->nilai;
	$background = '';
	if($total_fmis != $total_simda){
		$background = 'background: #ffdbdb';
	}
	$body .= '
		<tr>
			<td class="text-center">'.$no.'</td>
			<td>'.$val->detail['nama_program'].'</td>
			<td>'.$val->detail['nama_giat'].'</td>
			<td>'.$val->detail['nama_sub_giat'].'</td>
			<td class="text-center">'.$val->detail['kode_akun'].'</td>
			<td class="text-center">'.$val->kd_rek_1.'.'.$val->kd_rek_2.'.'.$val->kd_rek_3.'.'.$val->kd_rek_4.'.'.$val->kd_rek_5.'</td>
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
		<h1 class="text-center">Monitoring Data SPD Rinci Tahun <?php echo $input['tahun_anggaran']; ?></h1>
		<h3 class="text-center"><?php echo $input['nama_skpd']; ?><br>No SPD: <?php echo $input['no_spd']; ?></h3>
		<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center">No</th>
					<th class="text-center">Program</th>
					<th class="text-center">Kegiatan</th>
					<th class="text-center">Sub Kegiatan</th>
					<th class="text-center">Kode Akun FMIS</th>
					<th class="text-center">Kode Akun Simda</th>
					<th class="text-center">Total FMIS</th>
					<th class="text-center">Total SIMDA</th>
				</tr>
			</thead>
			<tbody id="data_body">
				<?php echo $body; ?>
			</tbody>
			<tfoot>
				<th colspan="6" class="text-center">Total</th>
				<th class="text-right" style="<?php echo $background_all; ?>"><?php echo number_format($total_all_fmis, 2, ',', '.'); ?></th>
				<th class="text-right" style="<?php echo $background_all; ?>"><?php echo number_format($total_all_simda, 2, ',', '.'); ?></th>
			</tfoot>
		</table>
	</div>
</div>