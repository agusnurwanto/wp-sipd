<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$input = shortcode_atts( array(
	'tahun_anggaran' => '2022'
), $atts );

global $wpdb;

echo $this->menu_ssh($input);

?>
	<div class="cetak">
		<div style="padding: 10px;margin:0 0 3rem 0;">
			<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
			<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
			<h2 class="text-center" style="margin:3rem;">Daftar 20 Rincian Belanja Terbesar<br><?php echo get_option('_crb_daerah'); ?><br>Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h2>
			<div class="card" style="width:100%;margin:0 0 2rem 0">
				<div class="card-body">
					<canvas id="mycanvas"></canvas>
				</div>
			</div>
			<h2 class="text-center" style="margin:3rem;">Data Rekapitulasi Rincian Belanja Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h2>
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

	<script type="text/javascript">
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
						var name = response.data[i].nama_komponen.substring(0, 30);
						var nama_komponen = name;
						if(name.length < response.data[i].nama_komponen.length){
							nama_komponen += "...";
						}
						nama.push(nama_komponen);
						total.push(response.data[i].total);
					}

					var chartdata = {
						labels: nama,
						datasets : [
							{
								label: 'Total',
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
		            	"data": "link",
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
		            	className: "text-right",
		            	render: function(data, type) {
			                var number = jQuery.fn.dataTable.render.number( '.', ',', 2, ''). display(data);
			                return number;
			            }
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
