<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$input = shortcode_atts( array(
	'tahun_anggaran' => '2021'
), $atts );

global $wpdb;
$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran order by tahun_anggaran ASC', ARRAY_A);

$body = '';
?>
<style>
.bulk-action {
	padding: .45rem;
	border-color: #eaeaea;
	vertical-align: middle;
}
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="cetak">
	<div style="padding: 10px;margin:0 0 3rem 0;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<h1 class="text-center" style="margin:3rem;">Halaman Monitoring RUP  <?php echo $input['tahun_anggaran']; ?></h1>
		<table id="data_monitoring_rup_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center">Nama OPD</th>
					<th class="text-center">Pagu SIPD</th>
					<th class="text-center">Total Pagu Non Pengadaan</th>
					<th class="text-center">Pagu Sirup</th>
					<th class="text-center">Selisih</th>
					<th class="text-center">Keterangann</th>
				</tr>
			</thead>
			<tbody id="data_body">
			</tbody>
		</table>
	</div>
</div>

<script>
	jQuery(document).ready(function(){
		globalThis.tahun_anggaran = <?php echo $input['tahun_anggaran']; ?>;
		globalThis.thisAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>"
		globalThis.id_lokasi = "<?php echo get_option('_crb_id_lokasi_sirup'); ?>"

		get_data_monitoring_rup();
	});

	/** get data monitoring rup */
	function get_data_monitoring_rup(){
		jQuery("#wrap-loading").show();
		globalThis.monitoringRup = jQuery('#data_monitoring_rup_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: thisAjaxUrl,
				type:"post",
				data:{
					'action' 		: "get_data_monitoring_rup",
					'api_key' 		: jQuery("#api_key").val(),
					'tahun_anggaran': tahun_anggaran,
					'id_lokasi'		: id_lokasi
				}
			},
			"initComplete":function( settings, json){
				jQuery("#wrap-loading").hide();
			},
			"columns": [
				{ 
					"data": "nama_skpd",
					className: "text-left"
				},
				{ 
					"data": "total_pagu_sipd",
					className: "text-right",
					"targets": "no-sort",
					"orderable": false
				},
				{
					"data":"total_pagu_non_pengadaan",
					className: "text-right",
					"targets": "no-sort",
					"orderable": false
				},
				{ 
					"data": "total_pagu_sirup",
					className: "text-right",
					"targets": "no-sort",
					"orderable": false
				},
				{ 
					"data": "selisih_pagu",
					className: "text-right",
					"targets": "no-sort",
					"orderable": false
				},
				{ 
					"data": "keterangan",
					className: "text-center",
					"targets": "no-sort",
					"orderable": false
				}
			]
		});
	}
</script> 
