<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$input = shortcode_atts( array(
	'tahun_anggaran' => '2022'
), $atts );

global $wpdb;

$nama_page_menu_ssh = 'Data Standar Satuan Harga SIPD | '.$input['tahun_anggaran'];
$custom_post = get_page_by_title($nama_page_menu_ssh, OBJECT, 'page');
$url_data_ssh = $this->get_link_post($custom_post);

$body = '';
?>

	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.25/datatables.min.css"/>

	<div class="cetak">
		<div style="padding: 10px;">
			<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
			<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
			<h1 class="text-center" style="margin:3rem;">Data Rincian Belanja Terbesar Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
			<div class="card" style="width:100%;margin:0 0 2rem 0">
				<div class="card-body">
					<canvas id="mycanvas"></canvas>
				</div>
			</div>
			<h1 class="text-center" style="margin:3rem;">Manajemen Data SSH Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
			<div style="margin: 0 0 2rem 0;" class="text-center">
				<a href="<?php echo $url_data_ssh ?>" style="text-decoration:none;" class="button button-primary button-large tambah_ssh" target="_blank">Data SSH SIPD</a>
			</div>
			<h1 class="text-center" style="margin:3rem;">Data Rekapitulasi Rincian Belanja Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
			<table id="data_ssh_analisis" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
				<thead id="data_header">
					<tr>
						<th class="text-center">Nama Komponen</th>
						<th class="text-center">Spek Komponen</th>
						<th class="text-center">Harga Satuan</th>
						<th class="text-center">Satuan</th>
						<th class="text-center">Volume</th>
						<th class="text-center">Total</th>
					</tr>
				</thead>
				<tbody id="data_body" class="data_body_ssh">
				</tbody>
			</table>
		</div>
	</div>

	<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.25/datatables.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
	<script>
		jQuery(document).ready(function(){
			
			globalThis.tahun = <?php echo $input['tahun_anggaran']; ?>;
			
			get_data_ssh_sipd(tahun);

			show_chart_ssh(tahun);
		})

		function show_chart_ssh(tahun){
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action' : "get_data_chart_ssh",
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
				},
				dataType: "json",
				success:function(response){
					var nama = [];
					var total = [];

					for(var i in response.data) {
						var name = response.data[i].nama_komponen.substring(0, 10);
						var nama_komponen = name+"...";
						nama.push(nama_komponen);
						total.push(response.data[i].total);
					}

					var chartdata = {
						labels: nama,
						datasets : [
						{
							label: 'Total Harga',
							backgroundColor: '#49e2ff',
							borderColor: '#46d5f1',
							hoverBackgroundColor: '#CCCCCC',
							hoverBorderColor: '#666666',
							data: total
						}
						]
					};

					var ctx = jQuery("#mycanvas");

					var barGraph = new Chart(ctx, {
						type: 'bar',
						data: chartdata
					});
				}
			})
		}

		function get_data_ssh_sipd(tahun){
			jQuery("#wrap-loading").show();
			jQuery('#data_ssh_analisis').DataTable({
				"processing": true,
        		"serverSide": true,
		        "ajax": {
		        	url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:"post",
					data:{
						'action' : "get_data_ssh_analisis",
						'api_key' : jQuery("#api_key").val(),
						'tahun_anggaran' : tahun,
					}
				},
				"initComplete":function( settings, json){
					jQuery("#wrap-loading").hide();
				},
				"columns": [
		            { 
		            	"data": "nama_komponen",
		            	className: "text-left"
		            },
		            { 
		            	"data": "spek_komponen",
		            	className: "text-left"
		            },
		            { 
		            	"data": "harga_satuan",
		            	className: "text-right",
		            	render: function(data, type) {
			                var number = jQuery.fn.dataTable.render.number( '.', ',', 2, ''). display(data);
			                return number;
			            }
		            },
		            { 
						"data": "satuan",
		            	className: "text-center" },
		            { 
		            	"data": "volume",
		            	className: "text-center"
		            },
		            { 
		            	"data": "total",
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
