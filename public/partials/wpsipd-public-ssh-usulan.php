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

	<style type="text/css">
		.warning {
			background: #f1a4a4;
		}
		.hide {
			display: none;
		}
		.akun-ssh-desc table, .akun-ssh-desc tr, .akun-ssh-desc td{
			border: none;
		}
		.akun-ssh-desc .first-desc{
			width: 11rem;
		}
		.akun-ssh-desc .sec-desc{
			width: 1rem;
		}
	</style>
	<div class="cetak">
		<div style="padding: 10px;margin:0 0 3rem 0;">
			<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
			<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
			<h1 class="text-center" style="margin:3rem;">Data usulan satuan standar harga (SSH) tahun anggaran <?php echo $input['tahun_anggaran']; ?></h1>
			<button style="margin: 0 0 2rem 0.5rem;border-radius:0.2rem;" class="tambah_ssh" data-toggle="modal" data-target="#tambahUsulanSsh" onclick="add_new_ssh()">Tambah Item SSH</button>
			<button style="margin: 0 0 2rem 0.5rem;border-radius:0.2rem;" class="tambah_new_ssh" data-toggle="modal" data-target="#tambahUsulanSsh" onclick="get_data_name_komponen_ssh(<?php echo $input['tahun_anggaran']; ?>)">Tambah Harga SSH</button>
			<button style="margin: 0 0 2rem 0.5rem;border-radius:0.2rem;background-color:#cf2e2e;" class="delete_new_ssh" onclick="delete_check_data_usulan_ssh()">Hapus Terpilih</button>
			<table id="usulan_ssh_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
				<thead id="data_header">
					<tr>
						<th class="text-center"><input type="checkbox" id="checkall"></th>
						<th class="text-center">ID Komponen</th>
						<th class="text-center">Kode Komponen</th>
						<th class="text-center">Uraian Komponen</th>
						<th class="text-center">Spesifikasi</th>
						<th class="text-center">Satuan</th>
						<th class="text-center">Harga Satuan</th>
						<th class="text-center">Status</th>
						<th class="text-center"">Keterangan</th>
						<th class="text-center"">Upload ke SIPD</th>
						<th class="text-right"">Aksi</th>
					</tr>
				</thead>
				<tbody id="data_body" class="data_body_ssh">
				</tbody>
			</table>
		</div>
	</div>

	<div class="modal fade mt-4" id="tambahUsulanSsh" tabindex="-1" role="dialog" aria-labelledby="tambahUsulanSshLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="tambahUsulanSshLabel">Modal title</h5>
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

	<script>
		jQuery(document).ready(function(){
			
			globalThis.tahun = <?php echo $input['tahun_anggaran']; ?>;
			
			get_data_ssh(tahun);
			let get_data = 1;

			jQuery('#tambahUsulanSsh').on('hidden.bs.modal', function () {
				jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-lg");
				jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-xl");
				jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-sm");
				jQuery("#tambahUsulanSsh .modal-body").html("");
				jQuery("#tambahUsulanSsh .modal-footer").removeClass("");
			})

            get_data_kategori_ssh(tahun);
            get_data_satuan_ssh(tahun);
            get_data_akun_ssh(tahun);
            get_data_nama_ssh(tahun);
			get_komponen_and_id_kel_ssh(tahun);

			jQuery('#checkall').click(function(){
				if(jQuery(this).is(':checked')){
					jQuery('.delete_check').prop('checked', true);
				}else{
					jQuery('.delete_check').prop('checked', false);
				}
			})

		})

		function add_new_ssh(){
				jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-xl modal-sm");
				jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-lg");
				jQuery("#tambahUsulanSshLabel").html("Tambah usulan SSH");
				jQuery(".modal-body").html("<div>"+
							"<label for=\'u_kategori\' style=\'display:inline-block\'>Kategori</label>"+
							"<select id=\'u_kategori\' class=\'js-example-basic-single\' style=\'display:block;width:100%;\'></select></div>"+
						"<div><label for=\'u_nama_komponen\' style=\'display:inline-block\'>Nama Komponen</label>"+
							"<input type=\'text\' id=\'u_nama_komponen\' style=\'display:block;width:100%;\' placeholder=\'Nama Komponen\'></div>"+
						"<div><label for=\'u_spesifikasi\' style=\'display:inline-block\'>Spesifikasi</label>"+
							"<input type=\'text\' id=\'u_spesifikasi\' style=\'display:block;width:100%;\' placeholder=\'Spesifikasi\'></div>"+
						"<div><label for=\'u_satuan\' style=\'display:inline-block\'>Satuan</label>"+
							"<select id=\'u_satuan\' class=\'js-example-basic-single\' style=\'display:block;width:100%;\'></select></div>"+
						"<div><label for=\'u_harga_satuan\' style=\'display:inline-block\'>Harga Satuan</label>"+
							"<input type=\'text\' id=\'u_harga_satuan\' style=\'display:block;width:100%;\' placeholder=\'Harga Satuan\'></div>"+
						"<div><label for=\'u_akun\' style=\'display:inline-block\'>Rekening Akun</label>"+
							"<select id=\'u_akun\' class=\'select2-multiple\' name=\'states[]\' multiple=\'multiple\' style=\'display:block;width:100%;\'></select></div>"+
						"<div><label for=\'u_keterangan_lampiran\' style=\'display:inline-block\'>Keterangan</label>"+
							"<input type=\'text\' id=\'u_keterangan_lampiran\' style=\'display:block;width:100%;\' placeholder=\'Link Google Drive Keterangan\'>"+
							"<small>*Masukkan link Google Drive berisikan lampiran minimal 3 harga toko beserta gambar.</small></div></div>");
				jQuery(".modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'submitBtn\' onclick=\'submitUsulanSshForm(<?php echo $input['tahun_anggaran']; ?>)\'>Simpan</button>");
						jQuery("#u_kategori").html(dataCategorySsh.table_content);
						jQuery("#u_satuan").html(dataSatuanSsh.table_content);
						jQuery("#u_akun").html(dataAkunSsh.table_content);
						jQuery('.js-example-basic-single').select2({
							dropdownParent: jQuery('#tambahUsulanSsh')
						});
						jQuery('.select2-multiple').select2({
							dropdownParent: jQuery('#tambahUsulanSsh')
						});
		}

		function get_data_ssh(tahun){
			jQuery("#wrap-loading").show();
			globalThis.usulanSSHTable = jQuery('#usulan_ssh_table').DataTable({
				"processing": true,
        		"serverSide": true,
		        "ajax": {
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:"post",
					data:{
						'action' : "get_data_usulan_ssh",
						'api_key' : jQuery("#api_key").val(),
						'tahun_anggaran' : tahun,
					}
				},
				"initComplete":function( settings, json){
					jQuery("#wrap-loading").hide();
				},
  				order: [0],
				"columns": [
					{ 
						"data": "deleteCheckbox",
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
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
		            	"data": "status",
		            	className: "text-center"
		            },
					{ 
		            	"data": "keterangan_status",
		            	className: "text-center"
		            },
					{ 
		            	"data": "status_upload_sipd",
		            	className: "text-center"
		            },
					{ 
		            	"data": "aksi",
		            	className: "text-center"
		            }
		        ]
		    });
		}

		function get_data_satuan_ssh(tahun){
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action' : "get_data_satuan_ssh",
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
				},
				dataType: "json",
				success:function(response){
					globalThis.dataSatuanSsh = response;
				}
			})
		}

		function get_data_kategori_ssh(tahun){
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action' : "get_data_kategori_ssh",
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
				},
				dataType: "json",
				success:function(response){
					globalThis.dataCategorySsh = response;
				}
			})
		}

		function get_data_akun_ssh(tahun){
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action' : "get_data_akun_ssh",
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
				},
				dataType: "json",
				success:function(response){
					globalThis.dataAkunSsh = response;
				}
			})
		}

		function get_data_nama_ssh(tahun){
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action' : "get_data_nama_ssh",
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
				},
				dataType: "json",
				success:function(response){
					globalThis.dataNamaSsh = response;
				}
			})
		}

		function get_data_name_komponen_ssh(tahun){
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-xl modal-sm");
			jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-lg");
			jQuery("#tambahUsulanSshLabel").html("Tambah Harga usulan SSH");
			jQuery(".modal-body").html("<div>"+
						"<label for=\'tambah_harga_kategori\' style=\'display:inline-block\'>Kategori</label>"+
						"<input type=\'text\' id=\'tambah_harga_kategori\' style=\'display:block;width:100%;\' placeholder=\'Kategori\' disabled></div>"+
					"<div><label for=\'tambah_harga_nama_komponent\' style=\'display:inline-block\'>Nama Komponen</label>"+
						"<select id=\'tambah_harga_nama_komponent\' class=\'js-example-basic-single\' style=\'display:block;width:100%;\' placeholder=\'Nama Komponen\'></select></div>"+
					"<div><label for=\'tambah_harga_spesifikasi\' style=\'display:inline-block\'>Spesifikasi</label>"+
						"<input type=\'text\' id=\'tambah_harga_spesifikasi\' style=\'display:block;width:100%;\' placeholder=\'Spesifikasi\' disabled></div>"+
					"<div><label for=\'tambah_harga_satuan\' style=\'display:inline-block\'>Satuan</label>"+
						"<input type=\'text\' id=\'tambah_harga_satuan\' style=\'display:block;width:100%;\' placeholder=\'Satuan\' disabled></div>"+
					"<div><label for=\'tambah_harga_harga_satuan\' style=\'display:inline-block\'>Harga Satuan</label>"+
						"<input type=\'text\' id=\'tambah_harga_harga_satuan\' style=\'display:block;width:100%;\' placeholder=\'Harga Satuan\'></div>"+
					"<div><label for=\'tambah_harga_akun\' style=\'display:inline-block\'>Rekening Akun</label>"+
						"<textarea type=\'text\' id=\'tambah_harga_akun\' style=\'display:block;width:100%;\' placeholder=\'Rekening Akun\' disabled></textarea></div>"+
					"<div><label for=\'u_keterangan_lampiran\' style=\'display:inline-block\'>Keterangan</label>"+
						"<input type=\'text\' id=\'u_keterangan_lampiran\' style=\'display:block;width:100%;\' placeholder=\'Link Google Drive Keterangan\'>"+
						"<small>*Masukkan link Google Drive berisikan lampiran minimal 3 harga toko beserta gambar.</small></div></div>");
			jQuery(".modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'submitBtn\' onclick=\'submitUsulanTambahHargaSshForm(<?php echo $input['tahun_anggaran']; ?>)\'>Simpan</button>");
			jQuery("#tambah_harga_nama_komponent").html(dataKomponenAndId.table_content);
			jQuery('.js-example-basic-single').select2({
				dropdownParent: jQuery('#tambahUsulanSsh')
			});
			jQuery("#tambah_harga_nama_komponent").on("change", function(){
				var id_standar_harga = jQuery(this).val();
				get_data_usulan_ssh_by_komponen(id_standar_harga)
			})
		}

		function get_komponen_and_id_kel_ssh(tahun){
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action' : "get_komponen_and_id_kel_ssh",
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
				},
				dataType: "json",
				success:function(response){
					globalThis.dataKomponenAndId = response;
				}
			})
		}

		function get_data_usulan_ssh_by_komponen(id_standar_harga){
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action' : 'get_data_usulan_ssh_by_komponen',
					'api_key' : jQuery("#api_key").val(),
					'id_standar_harga' : id_standar_harga,
				},
				dataType: 'json',
				success:function(response){
					jQuery("#tambah_harga_kategori").val(response.data_ssh_usulan_by_id.kode_kel_standar_harga+" "+response.data_ssh_usulan_by_id.nama_kel_standar_harga);
					jQuery("#tambah_harga_spesifikasi").val(response.data_ssh_usulan_by_id.spek);
					jQuery("#tambah_harga_satuan").val(response.data_ssh_usulan_by_id.satuan);
					jQuery("#tambah_harga_akun").html(response.table_content);
					if(response.status != 'success'){
						alert('Some problem occurred, please try again.');
					}
				}
			});
		}

		function submitUsulanSshForm(tahun){
			var kategori = jQuery('#u_kategori').val();
			var nama_komponen = jQuery('#u_nama_komponen').val();
			var spesifikasi = jQuery('#u_spesifikasi').val();
			var satuan = jQuery('#u_satuan').val();
			var harga_satuan = jQuery('#u_harga_satuan').val();
			var akun = jQuery('#u_akun').val();
			var keterangan_lampiran = jQuery('#u_keterangan_lampiran').val();
			jQuery("#wrap-loading").show();
			if(kategori.trim() == '' || nama_komponen.trim() == '' || spesifikasi.trim() == '' || satuan.trim() == '' || harga_satuan.trim() == '' || akun == '' || keterangan_lampiran.trim() == ''){
				jQuery("#wrap-loading").hide();
				alert('Harap diisi semua, tidak ada yang kosong.');
				return false;
			}else{
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:'post',
					data:{
						'action' : 'submit_usulan_ssh',
						'api_key' : jQuery("#api_key").val(),
						'kategori' : kategori,
						'nama_komponen' : nama_komponen,
						'spesifikasi' : spesifikasi,
						'satuan' : satuan,
						'harga_satuan' : harga_satuan,
						'akun' : akun,
						'tahun_anggaran' : tahun,
						'keterangan_lampiran' : keterangan_lampiran,
					},
					dataType: 'json',
					beforeSend: function () {
						jQuery('.submitBtn').attr("disabled","disabled");
						jQuery('.modal-body').css('opacity', '.5');
					},
					success:function(response){
						if(response.status == 'success'){
							alert('Data berhasil disimpan.');
						}else{
							alert(response.message);
						}
						jQuery('#tambahUsulanSsh').modal('hide')
						jQuery('.submitBtn').removeAttr("disabled");
						jQuery('.modal-body').css('opacity', '');
						jQuery("#wrap-loading").hide();
						usulanSSHTable.ajax.reload();	
					}
				});
			}
		}

		//edit akun ssh usulan
		function edit_akun_ssh_usulan(id_standar_harga){
			jQuery('#tambahUsulanSsh').modal('show');
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-xl modal-sm");
			jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-lg");
			jQuery("#tambahUsulanSshLabel").html("Tambah Rekening Akun");
			jQuery(".modal-body").html("<div class=\'akun-ssh-desc\'><table>"+
						"<tr><td class=\'first-desc\'>Kategori</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_kategori\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Nama Komponen</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_nama_komponen\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Spesifikasi</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_spesifikasi\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Satuan</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_satuan\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Harga Satuan</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_harga_satuan\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Rekening Akun</td><td class=\'sec-desc\'>:</td><td class=\'pt-0\'><div class=\'ul-desc-akun\'></div></td></tr></table>"+
						"<div class=\'add_akun_to_ssh\' style=\'display:none;\'><label for=\'u_data_akun\' style=\'display:inline-block\'>Tambah Rekening Akun</label>"+
						"<input type=\'hidden\' id=\'u_data_add_ssh_akun_id\'>"+
						"<select id=\'u_data_akun\' class=\'select2-multiple\' name=\'states[]\' multiple=\'multiple\' style=\'display:block;width:100%;\' required></select>"+
						"<button style=\'margin: 1rem 0 2rem 0;border-radius:0.2rem;\' class=\'submitBtn btn_add_akun_to_ssh btn-success\' onclick=\'save_add_akun_to_ssh("+tahun+")\'>Simpan Rekening</button></div></div>");
			jQuery(".modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'btn_add_akun_to_ssh\' onclick=\'field_add_akun_to_ssh()\'>Tambah Rekening</button>");
					jQuery("#u_data_akun").html(dataAkunSsh.table_content);
					jQuery('.select2-multiple').select2({
						dropdownParent: jQuery('#tambahUsulanSsh')
					});
			let iconX = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16" style="color:red;"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';

			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action' : "get_data_ssh_usulan_by_id",
					'api_key' : jQuery("#api_key").val(),
					'id_standar_harga' : id_standar_harga,
					'tahun_anggaran'	: tahun
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
						jQuery(".ul-desc-akun").append(`<div class="row" id="rek_akun_${value.id}" style="border-bottom: 1px solid #ebebeb;padding: 6px;">
						<div class="col-10">${value.nama_akun}</div>
						<div class="col-2 text-center"><a href="#" onclick="return delete_akun_ssh_usulan('${id_standar_harga}','${value.id}');" title="Delete rekening akun usulan SSH">${iconX}</a></div></div>`);
					});
				}
			})
		}

		function field_add_akun_to_ssh(){
			jQuery(".add_akun_to_ssh").toggle();
		}

		function save_add_akun_to_ssh(tahun){
			var id_standar_harga = jQuery('#u_data_add_ssh_akun_id').val();
			var data_rek_akun = jQuery('#u_data_akun').val();
			jQuery("#wrap-loading").show();
			if(id_standar_harga.trim() == '' || data_rek_akun == ''){
				jQuery("#wrap-loading").hide();
				alert('Harap diisi semua, tidak ada yang kosong.');
				return false;
			}else{
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:'post',
					data:{
						'action' : 'submit_add_new_akun_ssh_usulan',
						'api_key' : jQuery("#api_key").val(),
						'id_standar_harga' : id_standar_harga,
						'data_akun' : data_rek_akun,
						'tahun_anggaran' : tahun,
					},
					dataType: 'json',
					beforeSend: function () {
						jQuery('.submitBtn').attr("disabled","disabled");
						jQuery('.modal-body').css('opacity', '.5');
					},
					success:function(response){
						if(response.status == 'success'){
							alert('Data berhasil disimpan.');
						}else{
							alert(response.message);
						}
						jQuery('#tambahUsulanSsh').modal('hide')
						jQuery('.submitBtn').removeAttr("disabled");
						jQuery('.modal-body').css('opacity', '');
						jQuery("#wrap-loading").hide();
						usulanSSHTable.ajax.reload();
					}
				});
			}
		}

		//edit akun ssh usulan
		function verify_ssh_usulan(id_standar_ssh){
			jQuery('#tambahUsulanSsh').modal('show');
			jQuery("#tambahUsulanSshLabel").html("Verifikasi SSH");
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-lg modal-xl");
			jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-sm");
			jQuery(".modal-body").html("<div class=\'akun-ssh-verify\'><table>"+
						"<tr><td><input class=\'verify-ssh\' id=\'verify-ssh-yes\' name=\'verify_ssh\' value=\'1\' type=\'radio\' checked><label for=\'verify-ssh-yes\'>Terima</label></td>"+
						"<td><input class=\'verify-ssh\' id=\'verify-ssh-no\' name=\'verify_ssh\' value=\'0\' type=\'radio\'><label for=\'verify-ssh-no\'>Tolak</label></td></tr>"+
						"<tr class=\'add-desc-verify-ssh\' style=\'display:none;\'><td colspan=\'2\'><label for=\'alasan_verify_ssh\' style=\'display:inline-block;\'>Alasan</label><textarea id=\'alasan_verify_ssh\'></textarea></td></tr></div>");
			jQuery(".modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'btn_submit_verify_ssh\' onclick=\'submit_verify_ssh("+id_standar_ssh+")\'>Simpan</button>");
			jQuery("#verify-ssh-no").on("click", function() {
				jQuery(".add-desc-verify-ssh").show();
			})
			jQuery("#verify-ssh-yes").on("click", function() {
				jQuery(".add-desc-verify-ssh").hide();
			})
		}

		function submit_verify_ssh(id_standar_ssh){
			var verify_ssh = jQuery("input[name=\'verify_ssh\']:checked").val();
			var reason_verify_ssh = jQuery("#alasan_verify_ssh").val();
			jQuery("#wrap-loading").show();
			if(verify_ssh == 0 && reason_verify_ssh.trim() == ''){
				jQuery("#wrap-loading").hide();
				alert('Harap diisi semua, tidak ada yang kosong.');
				return false;
			}else{
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:'post',
					data:{
						'action' : 'submit_verify_ssh',
						'api_key' : jQuery("#api_key").val(),
						'verify_ssh' : verify_ssh,
						'reason_verify_ssh' : reason_verify_ssh,
						'id_ssh_verify_ssh' : id_standar_ssh,
					},
					dataType: 'json',
					beforeSend: function () {
						jQuery('.btn_submit_verify_ssh').attr("disabled","disabled");
						jQuery('.modal-body').css('opacity', '.5');
					},
					success:function(response){
						if(response.status == 'success'){
							alert('Data berhasil diverifikasi.');
						}else{
							alert("GAGAL! "+response.message);
						}
						jQuery('#tambahUsulanSsh').modal('hide')
						jQuery('.submitBtn').removeAttr("disabled");
						jQuery('.modal-body').css('opacity', '');
						jQuery("#wrap-loading").hide();
						usulanSSHTable.ajax.reload();
					}
				});
			}
		}

		function submitUsulanTambahHargaSshForm(tahun){
			var id_standar_harga = jQuery('#tambah_harga_nama_komponent').val();
			var harga_satuan = jQuery('#tambah_harga_harga_satuan').val();
			var keterangan_lampiran = jQuery('#u_keterangan_lampiran').val();
			jQuery("#wrap-loading").show();
			if(harga_satuan.trim() == '' || keterangan_lampiran.trim() == '' || id_standar_harga.trim() == ''){
				jQuery("#wrap-loading").hide();
				alert('Harap diisi semua, tidak ada yang kosong.');
				return false;
			}else{
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:'post',
					data:{
						'action' : 'submit_tambah_harga_ssh',
						'api_key' : jQuery("#api_key").val(),
						'tahun_anggaran' : tahun,
						'id_standar_harga' : id_standar_harga,
						'harga_satuan' : harga_satuan,
						'keterangan_lampiran' : keterangan_lampiran,
					},
					dataType: 'json',
					beforeSend: function () {
						jQuery('.submitBtn').attr("disabled","disabled");
						jQuery('.modal-body').css('opacity', '.5');
					},
					success:function(response){
						if(response.status == 'success'){
							alert('Data berhasil disimpan.');
						}else{
							alert(response.message);
						}
						jQuery('#tambahUsulanSsh').modal('hide')
						jQuery('.submitBtn').removeAttr("disabled");
						jQuery('.modal-body').css('opacity', '');
						jQuery("#wrap-loading").hide();
						usulanSSHTable.ajax.reload();
					}
				});
			}
		}
		/** edit akun ssh usulan */
		function edit_ssh_usulan(id_standar_harga){
			jQuery('#tambahUsulanSsh').modal('show');
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-xl modal-sm");
			jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-lg");
			jQuery("#tambahUsulanSshLabel").html("Edit usulan SSH");
			jQuery(".modal-body").html("<div>"+
						"<label for=\'edit_usulan_kategori\' style=\'display:inline-block\'>Kategori</label>"+
						"<select id=\'edit_usulan_kategori\' class=\'js-example-basic-single\' style=\'display:block;width:100%;\'></select></div>"+
					"<div><label for=\'edit_usulan_nama_komponen\' style=\'display:inline-block\'>Nama Komponen</label>"+
						"<input type=\'text\' id=\'edit_usulan_nama_komponen\' style=\'display:block;width:100%;\' placeholder=\'Nama Komponen\'></div>"+
					"<div><label for=\'edit_usulan_spesifikasi\' style=\'display:inline-block\'>Spesifikasi</label>"+
						"<input type=\'text\' id=\'edit_usulan_spesifikasi\' style=\'display:block;width:100%;\' placeholder=\'Spesifikasi\'></div>"+
					"<div><label for=\'edit_usulan_satuan\' style=\'display:inline-block\'>Satuan</label>"+
						"<select id=\'edit_usulan_satuan\' class=\'js-example-basic-single\' style=\'display:block;width:100%;\'></select></div>"+
					"<div><label for=\'edit_usulan_harga_satuan\' style=\'display:inline-block\'>Harga Satuan</label>"+
						"<input type=\'text\' id=\'edit_usulan_harga_satuan\' style=\'display:block;width:100%;\' placeholder=\'Harga Satuan\'></div>"+
					"<div><label for=\'edit_usulan_keterangan_lampiran\' style=\'display:inline-block\'>Keterangan</label>"+
						"<input type=\'text\' id=\'edit_usulan_keterangan_lampiran\' style=\'display:block;width:100%;\' placeholder=\'Link Google Drive Keterangan\'>"+
						"<small>*Masukkan link Google Drive berisikan lampiran minimal 3 harga toko beserta gambar.</small></div></div>");
			jQuery(".modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'submitBtn\' onclick=\'submitEditUsulanSshForm("+id_standar_harga+",<?php echo $input['tahun_anggaran']; ?>)\'>Simpan</button>");
					jQuery("#edit_usulan_kategori").html(dataCategorySsh.table_content);
					jQuery("#edit_usulan_satuan").html(dataSatuanSsh.table_content);
					jQuery('.js-example-basic-single').select2({
						dropdownParent: jQuery('#tambahUsulanSsh')
					});
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action'			: "get_data_ssh_usulan_by_id",
					'api_key'			: jQuery("#api_key").val(),
					'id_standar_harga'	: id_standar_harga,
					'tahun_anggaran'	: tahun
				},
				dataType: "json",
				success:function(response){
					jQuery('#edit_usulan_kategori').val(response.data_kel_standar_harga_by_id.id_kategori);
					jQuery('#edit_usulan_kategori').trigger('change');
					jQuery('#edit_usulan_satuan').val(response.data_satuan_by_id.id_satuan);
					jQuery('#edit_usulan_satuan').trigger('change');
					jQuery("#edit_usulan_nama_komponen").val(response.data.nama_standar_harga);
					jQuery("#edit_usulan_spesifikasi").val(response.data.spek);
					jQuery("#edit_usulan_harga_satuan").val(response.data.harga);
					jQuery("#edit_usulan_keterangan_lampiran").val(response.data.keterangan_lampiran);
				}
			})
		}

		function cannot_edit_ssh_usulan(){
			jQuery('#tambahUsulanSsh').modal('show');
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-xl modal-sm");
			jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-lg");
			jQuery("#tambahUsulanSshLabel").html("Edit usulan SSH");
			jQuery(".modal-body").html("<div style=\'text-align:center\';><h2>TIDAK BISA EDIT</h2><p>Tidak bisa edit usulan SSH karena sudah ditahap verifikasi</p></div>");
			jQuery(".modal-footer").html("");
		}

		function submitEditUsulanSshForm(id_standar_harga,tahun){
			var kategori = jQuery('#edit_usulan_kategori').val();
			var nama_komponen = jQuery('#edit_usulan_nama_komponen').val();
			var spesifikasi = jQuery('#edit_usulan_spesifikasi').val();
			var satuan = jQuery('#edit_usulan_satuan').val();
			var harga_satuan = jQuery('#edit_usulan_harga_satuan').val();
			var keterangan_lampiran = jQuery('#edit_usulan_keterangan_lampiran').val();
			jQuery("#wrap-loading").show();
			if(kategori.trim() == '' || nama_komponen.trim() == '' || spesifikasi.trim() == '' || satuan.trim() == '' || harga_satuan.trim() == '' || keterangan_lampiran.trim() == ''){
				jQuery("#wrap-loading").hide();
				alert('Harap diisi semua, tidak ada yang kosong.');
				return false;
			}else{
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:'post',
					data:{
						'action' 				: 'submit_edit_usulan_ssh',
						'api_key'				: jQuery("#api_key").val(),
						'id_standar_harga'		: id_standar_harga,
						'kategori'				: kategori,
						'nama_komponen'			: nama_komponen,
						'spesifikasi'			: spesifikasi,
						'satuan'				: satuan,
						'harga_satuan'			: harga_satuan,
						'tahun_anggaran'		: tahun,
						'keterangan_lampiran'	: keterangan_lampiran,
					},
					dataType: 'json',
					beforeSend: function () {
						jQuery('.submitBtn').attr("disabled","disabled");
						jQuery('.modal-body').css('opacity', '.5');
					},
					success:function(response){
						if(response.status == 'success'){
							alert('Data berhasil disimpan.');
						}else{
							alert(`GAGAL! ${response.message}`);
						}
						jQuery('.submitBtn').removeAttr("disabled");
						jQuery('.modal-body').css('opacity', '');
						jQuery(".modal-body").html("");
						jQuery(".modal-footer").html("");
						jQuery("#wrap-loading").hide();
						jQuery('#tambahUsulanSsh').modal('hide')
						usulanSSHTable.ajax.reload();	
					}
				});
			}
		}

		function delete_ssh_usulan(id_standar_harga){
			let confirmDelete = confirm("Apakah anda yakin akang menghapus usulan SSH?");
			if(confirmDelete){
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:'post',
					data:{
						'action' 				: 'submit_delete_usulan_ssh',
						'api_key'				: jQuery("#api_key").val(),
						'id_standar_harga'		: id_standar_harga,
						'tahun_anggaran'		: tahun
					},
					dataType: 'json',
					success:function(response){
						if(response.status == 'success'){
							alert('Data berhasil dihapus!.');
						}else{
							alert(`GAGAL! ${response.message}`);
						}
						usulanSSHTable.ajax.reload();	
					}
				});
			}
		}

		function delete_akun_ssh_usulan(id_standar_harga,id){
			let confirmDelete = confirm("Apakah anda yakin akang menghapus rekening akun usulan SSH?");
			if(confirmDelete){
				jQuery("#wrap-loading").show();
				if(id_standar_harga == '' || id == ''){
					jQuery("#wrap-loading").hide();
					alert("GAGAL, coba ulangi lagi!")
				}else{
					jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type:'post',
						data:{
							'action' 				: 'submit_delete_akun_usulan_ssh',
							'api_key'				: jQuery("#api_key").val(),
							'id_standar_harga'		: id_standar_harga,
							'tahun_anggaran'		: tahun,
							'id_rek_akun_usulan_ssh': id
						},
						dataType: 'json',
						success:function(response){
							jQuery("#wrap-loading").hide();
							if(response.status == 'success'){
								alert('Data berhasil dihapus!.');
								jQuery(`#rek_akun_${id}`).remove()
							}else{
								alert(`GAGAL! ${response.message}`);
							}
							usulanSSHTable.ajax.reload();	
						}
					});
				}
			}
		}

		// Checkbox checked
        function checkcheckbox(){

            var length = jQuery('.delete_check').length;

            var totalchecked = 0;
            jQuery('.delete_check').each(function(){
                if(jQuery(this).is(':checked')){
                    totalchecked+=1;
                }
            }); 

            if(totalchecked == length){
                jQuery("#checkall").prop('checked', true);
            }else{
                jQuery('#checkall').prop('checked', false);
            }
        }

		// Delete record
		function delete_check_data_usulan_ssh(){

			var deleteids_arr = [];

			jQuery("input:checkbox[class=delete_check]:checked").each(function () {
				deleteids_arr.push(jQuery(this).val());
			});
			jQuery("#wrap-loading").show();

			if(deleteids_arr.length > 0){

				var confirmdelete = confirm("Apakah kamu yakin hapus data?");
				if (confirmdelete == true) {
					jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type:"post",
						data:{
							'action' : "submit_delete_check_usulan_ssh",
							'api_key' : jQuery("#api_key").val(),
							'tahun_anggaran' : tahun,
							'deleteids_arr'	: deleteids_arr
						},
						dataType: "json",
						success:function(response){
							jQuery("#wrap-loading").hide();
							if(response.status == 'success'){
								alert('Data berhasil dihapus.');
							}else{
								alert(`GAGAL! ${response.message}`);
							}
							usulanSSHTable.ajax.reload();
						}
					})
				}
			}else if(deleteids_arr.length == 0){
				jQuery("#wrap-loading").hide();
				alert('Tidak ada data dihapus!');
			}
		}

	</script> 
