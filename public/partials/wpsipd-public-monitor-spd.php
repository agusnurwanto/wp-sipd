<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$input = shortcode_atts( array(
	'kode_rek' => '',
	'tahun_anggaran' => '2021'
), $atts );
$body = '';
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
					<th class="text-center" style="width: 100px;">Last Syncrone</th>
					<th class="text-center">Total FMIS</th>
					<th class="text-center">Total SIMDA</th>
					<th class="text-center">Selisih</th>
				</tr>
			</thead>
			<tbody id="data_body">
				<?php echo $body; ?>
			</tbody>
		</table>
	</div>
</div>