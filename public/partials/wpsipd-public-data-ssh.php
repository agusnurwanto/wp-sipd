<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$input = shortcode_atts( array(
	'tahun_anggaran' => '2022'
), $atts );

global $wpdb;
$body = '';
?>

	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.25/datatables.min.css"/>

	<div class="cetak">
		<div style="padding: 10px;">
			<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
			<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
			<h1 class="text-center" style="margin:3rem;">Data satuan standar harga (SSH) SIPD tahun anggaran <?php echo $input['tahun_anggaran']; ?></h1>
			<table id="data_ssh_sipd_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
				<thead id="data_header">
					<tr>
						<th class="text-center">ID Komponen</th>
						<th class="text-center">Kode Komponen</th>
						<th class="text-center">Uraian Komponen</th>
						<th class="text-center">Spesifikasi</th>
						<th class="text-center">Satuan</th>
						<th class="text-center">Harga Satuan</th>
					</tr>
				</thead>
				<tbody id="data_body" class="data_body_ssh">
				</tbody>
			</table>
		</div>
	</div>

	<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.25/datatables.min.js"></script>
	<script>
		jQuery(document).ready(function(){
			
			globalThis.tahun = <?php echo $input['tahun_anggaran']; ?>;
			
			get_data_ssh_sipd(tahun);
		})

		function get_data_ssh_sipd(tahun){
			jQuery("#wrap-loading").show();
			jQuery('#data_ssh_sipd_table').DataTable({
				"processing": true,
        		"serverSide": true,
		        "ajax": {
		        	url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:"post",
					data:{
						'action' : "get_data_ssh_sipd",
						'api_key' : jQuery("#api_key").val(),
						'tahun_anggaran' : tahun,
					}
				},
				"initComplete":function( settings, json){
					jQuery("#wrap-loading").hide();
				},
				"columns": [
		            { 
		            	"data": "id_standar_harga",
		            	className: "text-center"
		            },
		            { 
		            	"data": "kode_standar_harga",
		            	className: "text-center"
		            },
		            { "data": "nama_standar_harga" },
		            { "data": "spek" },
		            { 
		            	"data": "satuan",
		            	className: "text-center"
		            },
		            { 
		            	"data": "harga",
		            	className: "text-right",
		            	render: function(data, type) {
			                var number = jQuery.fn.dataTable.render.number( '.', ',', 2, ''). display(data);
			                return number;
			            }
		            }
		        ]
		    });
		}

	</script> 
