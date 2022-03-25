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
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

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
		<div style="padding: 10px;">
			<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
			<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
			<h1 class="text-center" style="margin:3rem;">Data usulan satuan standar harga (SSH) tahun <?php echo $input['tahun_anggaran']; ?></h1>
			<button style="margin: 0 0 2rem 0.5rem;border-radius:0.2rem;" class="tambah_ssh" data-toggle="modal" data-target="#tambahUsulanSsh" onclick="add_new_ssh()">Tambah Item SSH</button>
			<button style="margin: 0 0 2rem 0.5rem;border-radius:0.2rem;" class="tambah_new_ssh" data-toggle="modal" data-target="#tambahUsulanSsh" onclick="get_data_name_komponen_ssh(<?php echo $input['tahun_anggaran']; ?>)">Tambah Harga SSH</button>
			<table id="usulan_ssh_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
				<thead id="data_header">
					<tr>
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

	<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.25/datatables.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

            // get_data_kategori_ssh(tahun);
            // get_data_satuan_ssh(tahun);
            // get_data_akun_ssh(tahun);
            // get_data_nama_ssh(tahun);
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
					"<div><label for=\'u_akun\' style=\'display:inline-block\'>Akun</label>"+
						"<select id=\'u_akun\' class=\'select2-multiple\' name=\'states[]\' multiple=\'multiple\' style=\'display:block;width:100%;\'></select></div>"+
					"<div><label for=\'u_keterangan_lampiran\' style=\'display:inline-block\'>Keterangan</label>"+
						"<input type=\'text\' id=\'u_keterangan_lampiran\' style=\'display:block;width:100%;\' placeholder=\'Link Google Drive Keterangan\'>"+
						"<small>*Masukkan link Google Drive berisikan lampiran minimal 3 harga toko beserta gambar.</small></div></div>");
			jQuery(".modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'submitBtn\' onclick=\'submitUsulanSshForm(<?php echo $input['tahun_anggaran']; ?>,0)\'>Simpan</button>");
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
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action' : "get_data_usulan_ssh",
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
				},
				dataType: "json",
				success:function(response){
					jQuery("#wrap-loading").hide();
					jQuery(".data_body_ssh").html(response.table_content);
					jQuery('#usulan_ssh_table').DataTable();
				}
			})
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
						"<label for=\'u_kategori\' style=\'display:inline-block\'>Kategori</label>"+
						"<select id=\'u_kategori\' class=\'js-example-basic-single\' style=\'display:block;width:100%;\'></select></div>"+
					"<div><label for=\'u_nama_komponen\' style=\'display:inline-block\'>Nama Komponen</label>"+
						"<select id=\'u_nama_komponen\' class=\'js-example-basic-single\' style=\'display:block;width:100%;\' placeholder=\'Nama Komponen\'></select></div>"+
					"<div><label for=\'u_spesifikasi\' style=\'display:inline-block\'>Spesifikasi</label>"+
						"<input type=\'text\' id=\'u_spesifikasi\' style=\'display:block;width:100%;\' placeholder=\'Spesifikasi\'></div>"+
					"<div><label for=\'u_satuan\' style=\'display:inline-block\'>Satuan</label>"+
						"<select id=\'u_satuan\' class=\'js-example-basic-single\' style=\'display:block;width:100%;\'></select></div>"+
					"<div><label for=\'u_harga_satuan\' style=\'display:inline-block\'>Harga Satuan</label>"+
						"<input type=\'text\' id=\'u_harga_satuan\' style=\'display:block;width:100%;\' placeholder=\'Harga Satuan\'></div>"+
					"<div><label for=\'u_akun\' style=\'display:inline-block\'>Akun</label>"+
						"<select id=\'u_akun\' class=\'select2-multiple\' name=\'states[]\' multiple=\'multiple\' style=\'display:block;width:100%;\'></select></div>"+
					"<div><label for=\'u_keterangan_lampiran\' style=\'display:inline-block\'>Keterangan</label>"+
						"<input type=\'text\' id=\'u_keterangan_lampiran\' style=\'display:block;width:100%;\' placeholder=\'Link Google Drive Keterangan\'>"+
						"<small>*Masukkan link Google Drive berisikan lampiran minimal 3 harga toko beserta gambar.</small></div></div>");
			jQuery(".modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'submitBtn\' onclick=\'submitUsulanSshForm(<?php echo $input['tahun_anggaran']; ?>,1)\'>Simpan</button>");
					jQuery("#u_kategori").html(dataCategorySsh.table_content);
					jQuery("#u_nama_komponen").html(dataNamaSsh.table_content);
					jQuery("#u_satuan").html(dataSatuanSsh.table_content);
					jQuery("#u_akun").html(dataAkunSsh.table_content);
					jQuery('.js-example-basic-single').select2({
						dropdownParent: jQuery('#tambahUsulanSsh')
					});
					jQuery('.select2-multiple').select2({
						dropdownParent: jQuery('#tambahUsulanSsh')
					});
					jQuery("#u_nama_komponen").on("change", function(){
						var id_standar_harga = jQuery(this).val();
						get_data_usulan_ssh_by_komponen(id_standar_harga)
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
					jQuery("#u_spesifikasi").val(response.data_ssh_usulan_by_id.spek);
					jQuery("#u_harga_satuan").val(response.data_ssh_usulan_by_id.harga);
					if(response.status != 'success'){
						alert('<span style="color:red;">Some problem occurred, please try again.</span>');
					}
				}
			});
		}

		function submitUsulanSshForm(tahun,addNewPrice){
			var kategori = jQuery('#u_kategori').val();
			var nama_komponen = jQuery('#u_nama_komponen').val();
			var spesifikasi = jQuery('#u_spesifikasi').val();
			var satuan = jQuery('#u_satuan').val();
			var harga_satuan = jQuery('#u_harga_satuan').val();
			var akun = jQuery('#u_akun').val();
			var keterangan_lampiran = jQuery('#u_keterangan_lampiran').val();
			jQuery("#wrap-loading").show();
			if(kategori.trim() == '' || nama_komponen.trim() == '' || spesifikasi.trim() == '' || satuan.trim() == '' || harga_satuan.trim() == ''){
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
						'add_new_price'	: addNewPrice,
						'keterangan_lampiran' : keterangan_lampiran,
					},
					dataType: 'json',
					beforeSend: function () {
						jQuery('.submitBtn').attr("disabled","disabled");
						jQuery('.modal-body').css('opacity', '.5');
					},
					success:function(response){
						if(response.status == 'success'){
							// alert('<span style="color:green;">Thanks for contacting us, we\'ll get back to you soon.</p>');
						}else{
							alert(response.message);
						}
						jQuery('#tambahUsulanSsh').modal('hide')
						jQuery('.submitBtn').removeAttr("disabled");
						jQuery('.modal-body').css('opacity', '');
						jQuery("#wrap-loading").hide();
						get_data_ssh(tahun);
					}
				});
			}
		}

		//edit akun ssh usulan
		function edit_akun_ssh_usulan(id_ssh){
			jQuery('#tambahUsulanSsh').modal('show');
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-xl modal-sm");
			jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-lg");
			jQuery("#tambahUsulanSshLabel").html("Tambah akun");
			jQuery(".modal-body").html("<div class=\'akun-ssh-desc\'><table>"+
						"<tr><td class=\'first-desc\'>Kategori</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_kategori\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Nama Komponen</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_nama_komponen\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Spesifikasi</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_spesifikasi\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Satuan</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_satuan\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Harga Satuan</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_harga_satuan\'></span></td></tr>"+
						"<tr><td class=\'first-desc\'>Akun</td><td class=\'sec-desc\'>:</td><td><ul class=\'ul-desc-akun\'></ul></td></tr></table>"+
						"<div class=\'add_akun_to_ssh\' style=\'display:none;\'><label for=\'u_akun\' style=\'display:inline-block\'>Tambah Akun</label>"+
						"<input type=\'hidden\' id=\'u_data_add_ssh_akun_id\'>"+
						"<select id=\'u_data_akun\' class=\'select2-multiple\' name=\'states[]\' multiple=\'multiple\' style=\'display:block;width:100%;\' required></select>"+
						"<button style=\'margin: 1rem 0 2rem 0;border-radius:0.2rem;\' class=\'submitBtn btn_add_akun_to_ssh btn-success\' onclick=\'save_add_akun_to_ssh("+tahun+")\'>Simpan Rekening</button></div></div>");
			jQuery(".modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'btn_add_akun_to_ssh\' onclick=\'field_add_akun_to_ssh()\'>Tambah Rekening</button>");
					jQuery("#u_data_akun").html(dataAkunSsh.table_content);
					jQuery('.select2-multiple').select2({
						dropdownParent: jQuery('#tambahUsulanSsh')
					});

			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action' : "get_data_ssh_by_id",
					'api_key' : jQuery("#api_key").val(),
					'id_ssh' : id_ssh,
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
				}
			})
		}

		function field_add_akun_to_ssh(){
			jQuery(".add_akun_to_ssh").toggle();
		}

		function save_add_akun_to_ssh(tahun){
			var id_standar_harga = jQuery('#u_data_add_ssh_akun_id').val();
			var data_akun = jQuery('#u_data_akun').val();
			jQuery("#wrap-loading").show();
			if(id_standar_harga.trim() == ''){
				jQuery("#wrap-loading").hide();
				alert('Harap diisi semua, tidak ada yang kosong.');
				return false;
			}else{
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:'post',
					data:{
						'action' : 'submit_add_new_akun_ssh',
						'api_key' : jQuery("#api_key").val(),
						'id_standar_harga' : id_standar_harga,
						'data_akun' : data_akun,
						'tahun_anggaran' : tahun,
					},
					dataType: 'json',
					beforeSend: function () {
						jQuery('.submitBtn').attr("disabled","disabled");
						jQuery('.modal-body').css('opacity', '.5');
					},
					success:function(response){
						if(response.status != 'success'){
							alert('<span style="color:red;">Some problem occurred, please try again.</span>');
						}
						jQuery('#tambahUsulanSsh').modal('hide')
						jQuery('.submitBtn').removeAttr("disabled");
						jQuery('.modal-body').css('opacity', '');
						jQuery("#wrap-loading").hide();
						get_data_ssh(tahun);
					}
				});
			}
		}

		//edit akun ssh usulan
		function verify_ssh_usulan(id_ssh){
			jQuery('#tambahUsulanSsh').modal('show');
			jQuery("#tambahUsulanSshLabel").html("Verifikasi SSH");
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-lg modal-xl");
			jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-sm");
			jQuery(".modal-body").html("<div class=\'akun-ssh-verify\'><table>"+
						"<tr><td><input class=\'verify-ssh\' id=\'verify-ssh-yes\' name=\'verify_ssh\' value=\'1\' type=\'radio\' checked><label for=\'verify-ssh-yes\'>Terima</label></td>"+
						"<td><input class=\'verify-ssh\' id=\'verify-ssh-no\' name=\'verify_ssh\' value=\'0\' type=\'radio\'><label for=\'verify-ssh-no\'>Tolak</label></td></tr>"+
						"<tr class=\'add-desc-verify-ssh\' style=\'display:none;\'><td colspan=\'2\'><label for=\'alasan_verify_ssh\' style=\'display:inline-block;\'>Alasan</label><textarea id=\'alasan_verify_ssh\'></textarea></td></tr></div>");
			jQuery(".modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'btn_submit_verify_ssh\' onclick=\'submit_verify_ssh("+id_ssh+")\'>Simpan</button>");
			jQuery("#verify-ssh-no").on("click", function() {
				jQuery(".add-desc-verify-ssh").show();
			})
			jQuery("#verify-ssh-yes").on("click", function() {
				jQuery(".add-desc-verify-ssh").hide();
			})
		}

		function submit_verify_ssh(id_ssh){
			var verify_ssh = jQuery("input[name=\'verify_ssh\']:checked").val();
			var reason_verify_ssh = jQuery("#alasan_verify_ssh").val();
			jQuery("#wrap-loading").show();
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:'post',
					data:{
						'action' : 'submit_verify_ssh',
						'api_key' : jQuery("#api_key").val(),
						'verify_ssh' : verify_ssh,
						'reason_verify_ssh' : reason_verify_ssh,
						'id_ssh_verify_ssh' : id_ssh,
					},
					dataType: 'json',
					beforeSend: function () {
						jQuery('.btn_submit_verify_ssh').attr("disabled","disabled");
						jQuery('.modal-body').css('opacity', '.5');
					},
					success:function(response){
						if(response.status == 'success'){
							// alert('<span style="color:green;">Thanks for contacting us, we\'ll get back to you soon.</p>');
						}else{
							alert('<span style="color:red;">Some problem occurred, please try again.</span>');
						}
						jQuery('#tambahUsulanSsh').modal('hide')
						jQuery('.submitBtn').removeAttr("disabled");
						jQuery('.modal-body').css('opacity', '');
						jQuery("#wrap-loading").hide();
						get_data_ssh(tahun);
					}
				});
		}

	</script> 
