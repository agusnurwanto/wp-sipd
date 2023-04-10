<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$input = shortcode_atts( array(
	'tahun_anggaran' => '2022'
), $atts );

global $wpdb;
$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran order by tahun_anggaran ASC', ARRAY_A);
$select_tahun = "<option value=''>Pilih berdasarkan tahun anggaran</option>";
foreach($tahun as $tahun_value){
	$select = $tahun_value['tahun_anggaran'] == $input['tahun_anggaran'] ? 'selected' : '';
	$nama_page_menu_ssh = 'Data Standar Satuan Harga SIPD | '.$tahun_value['tahun_anggaran'];
	$custom_post = get_page_by_title($nama_page_menu_ssh, OBJECT, 'page');
	$url_data_ssh = $this->get_link_post($custom_post);
	$select_tahun .= "<option value='".$url_data_ssh."' ".$select.">Data Satuan Standar Harga (SSH) SIPD ".$tahun_value['tahun_anggaran']."</option>";
}

$body = '';
?>
	<div class="cetak">
		<div style="padding: 10px;margin:0 0 3rem 0;">
			<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
			<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
			<h1 id="judul" class="text-center" style="margin:3rem;">Data satuan standar harga (SSH) SIPD tahun anggaran <?php echo $input['tahun_anggaran']; ?></h1>
			<div class="summary mb-5" style="">
				<table>
					<thead>
						<tr>
							<th colspan="3" class="text-center">Summary Rekapitulasi SSH</th>
						</tr>
						<tr>
							<th class="text-center">Menunggu</th>
							<th class="text-center">Disetujui</th>
							<th class="text-center">Ditolak</th>
						</tr>
					</thead>
					<tbody id="summary_ssh_body" style="text-align:center;"></tbody>
				</table>
			</div>
			<div class="data-ssh">
				<table id="data_ssh_sipd_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
					<thead id="data_header">
						<tr>
							<th class="text-center">ID Komponen</th>
							<th class="text-center">Kode Komponen</th>
							<th class="text-center">Uraian Komponen</th>
							<th class="text-center">Spesifikasi</th>
							<th class="text-center">Satuan</th>
							<th class="text-center">Harga Satuan</th>
							<th class="text-center">Tipe Kelompok</th>
							<th class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody id="data_body" class="data_body_ssh">
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="modal fade mt-4" id="modalDataSSH" tabindex="-1" role="dialog" aria-labelledby="modalDataSSHLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalDataSSHLabel">Modal title</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				</div> 
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>

	<script>
		jQuery(document).ready(function(){
			
			globalThis.tahun = <?php echo $input['tahun_anggaran']; ?>;
			
			get_data_ssh_sipd(tahun);

			get_data_summary_ssh_sipd(tahun);

			let html_filter = "<div style='text-align: center; margin-bottom: 20px;'><select id='selectYears'><?php echo $select_tahun ?></select></div>"
			jQuery("#judul").after(html_filter);

			jQuery('#selectYears').on('change', function(e){
				let selectedVal = jQuery(this).find('option:selected').val();
				if(selectedVal != ''){
					window.location = selectedVal;
				}
			});
		});

		function get_data_ssh_sipd(tahun){
			jQuery('#data_ssh_sipd_table').on('preXhr.dt', function ( e, settings, data ) {
				jQuery("#wrap-loading").show();
		    } ).DataTable({
				"processing": true,
        		"serverSide": true,
		        "ajax": {
		        	url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:"post",
					data:{
						'action': "get_data_ssh_sipd",
						'api_key': jQuery("#api_key").val(),
						'tahun_anggaran': tahun
					}
				},
				"drawCallback": function( settings ){
					jQuery("#wrap-loading").hide();
				},
		        dom: 'lBfrtip',
		        buttons: [
		            'copy', 'csv', 'excel', 'pdf', 'print'
		        ],
				lengthMenu: [
					[20, 100, 500, -1], 
					[20, 100, 500, "All"]
				],
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
		            },
		            { 
		            	"data": "show_kelompok",
		            	className: "text-center"
		            },
		            { 
		            	"data": "aksi",
		            	className: "text-right"
		            }
		        ]
		    });
		}

		//data akun ssh sipd
		function data_akun_ssh_sipd(id_standar_harga){
			jQuery('#wrap-loading').show();
			jQuery('#modalDataSSH').modal('show');
			jQuery("#modalDataSSH .modal-dialog").removeClass("modal-xl modal-sm");
			jQuery("#modalDataSSH .modal-dialog").addClass("modal-lg");
			jQuery("#modalDataSSHLabel").html("Data akun");
			jQuery(".modal-body").html("<div class=\'akun-ssh-desc\'><table>"+
						"<tr><td class=\'first-desc\'>Kategori</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_kategori\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Nama Komponen</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_nama_komponen\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Spesifikasi</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_spesifikasi\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Satuan</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_satuan\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Harga Satuan</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_harga_satuan\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Akun</td><td class=\'sec-desc\'>:</td><td><ul class=\'ul-desc-akun\'></ul></td></tr></table></div></div>");

			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action' : "get_data_ssh_sipd_by_id",
					'api_key' : jQuery("#api_key").val(),
					'id_standar_harga' : id_standar_harga,
				},
				dataType: "json",
				success:function(response){
					jQuery("#u_data_kategori").html(response.data.kode_standar_harga);
					jQuery("#u_data_nama_komponen").html(response.data.nama_standar_harga);
					jQuery("#u_data_spesifikasi").html(response.data.spek);
					jQuery("#u_data_satuan").html(response.data.satuan);
					jQuery("#u_data_harga_satuan").html(response.data.harga);
					jQuery("#u_data_add_ssh_akun_id").val(response.data.id_standar_harga);
					var data_akun = response.data_akun;
					jQuery.each( data_akun, function( key, value ) {
						jQuery("ul.ul-desc-akun").append(`<li>${value.nama_akun}</li>`);
					});
					jQuery('#wrap-loading').hide();
				}
			})
		}

		function get_data_summary_ssh_sipd(tahun){
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action' : "get_data_summary_ssh_sipd",
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
				},
				dataType: "json",
				success:function(response){
					jQuery("#summary_ssh_body").html(`
						<tr>
							<td>${response.data.waiting}</td>
							<td>${response.data.approved}</td>
							<td>${response.data.rejected}</td>
						</tr>`);
				}
			})
		}

	</script> 
