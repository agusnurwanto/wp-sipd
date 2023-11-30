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
?>
<div style="padding: 10px; margin:0 0 3rem 0;">
	<input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
	<h1 class="text-center" style="margin:3rem;">Halaman Setting Batasan Pagu Sumber Dana <?php echo $input['tahun_anggaran']; ?></h1>
	<table id="data_sumberdana_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
		<thead id="data_header">
			<tr>
				<th class="text-center">Nama Sumber Dana</th>
				<th class="text-center">Batasan Pagu</th>
				<th class="text-center">Pagu Terpakai</th>
				<th class="text-center" style="width: 250px;">Aksi</th>
			</tr>
		</thead>
		<tbody id="data_body">
		</tbody>
	</table>
</div>

<script>
	jQuery(document).ready(function() {
		window.tahun_anggaran = <?php echo $input['tahun_anggaran']; ?>;
		get_data_sumberdana();
	});

	/** get data sumberdana */
	function get_data_sumberdana() {
		jQuery("#wrap-loading").show();
		window.sumberdanaTable = jQuery('#data_sumberdana_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: ajax.url,
				type: "post",
				data: {
					'action': "get_data_sumberdana",
					'api_key': jQuery("#api_key").val(),
					'tahun_anggaran': tahun_anggaran
				}
			},
			"initComplete": function(settings, json) {
				jQuery("#wrap-loading").hide();
			// 	if (json.checkOpenedSchedule != 'undefined' && json.checkOpenedSchedule > 0) {
			// 		jQuery(".tambah_jadwal").prop('hidden', true);
			// 	} else {
			// 		jQuery(".tambah_jadwal").prop('hidden', false);
			// 	}
			},
			"columns": [{
					"data": "nama",
					className: "text-center"
				},
				{
					"data": "nama",
					className: "text-center"
				},
				{
					"data": "nama",
					className: "text-center"
				},
				{
					"data": "aksi",
					className: "text-center"
				}
			]
		});
	}
</script>