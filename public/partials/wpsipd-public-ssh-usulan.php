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
	ul.td-aksi {
	    margin: 0;
	    width: 100px;
	}
	ul.td-aksi li {
	    list-style: none;
	    margin: 2px;
	    display: inline-block;
	}
	.kol-keterangan{
		max-width: 300px;
	}
	td.kol-keterangan{
		padding: 0!important;
	}
	td.in-kol-keterangan{
		max-width: 300px;
	}
	.medium-bold{
		font-weight: 600;
		color: #50575e;
	}
	.medium-bold-2{
		font-weight: 600;
		color: #212529;
	}
	ul.keterangan{
		margin-bottom: 0!important;
	}
	td.spek-satuan{
		padding: 0!important;
	}

	td.show_status{
		padding: 0!important;
	}
	td.show-komponen{
		padding: 0!important;
	}
	.toolbar {
		float: left;
	}
	.bulk-action {
		padding: .45rem;
		border-color: #eaeaea;
		vertical-align: middle;
	}
	.verify-ssh{
		margin-right: .5rem;
	}
</style>
<div class="cetak">
	<div style="padding: 10px;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
		<h1 class="text-center">Data usulan satuan standar harga (SSH)<br>tahun anggaran <?php echo $input['tahun_anggaran']; ?></h1>
		<div style="margin-bottom: 25px;">
			<button class="btn btn-primary tambah_ssh" disabled onclick="tambah_new_ssh(<?php echo $input['tahun_anggaran']; ?>);">Tambah Item SSH</button>
			<button class="btn btn-primary tambah_new_ssh" disabled onclick="get_data_by_name_komponen_ssh('harga',<?php echo $input['tahun_anggaran']; ?>)">Tambah Harga SSH</button>
			<button class="btn btn-primary tambah_new_ssh" disabled onclick="get_data_by_name_komponen_ssh('akun',<?php echo $input['tahun_anggaran']; ?>)">Tambah Akun SSH</button>
		</div>
		<table id="usulan_ssh_table" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center"><input type="checkbox" id="checkall"></th>
					<th class="text-center">Kode Komponen</th>
					<th class="text-center">Uraian Komponen</th>
					<th class="text-center">Spesifikasi Satuan</th>
					<th class="text-center">Harga Satuan</th>
					<th class="text-center">Keterangan</th>
					<th class="text-center">Status</th>
					<th class="text-right">Aksi</th>
				</tr>
			</thead>
			<tbody id="data_body" class="data_body_ssh">
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="tambahUsulanSsh" role="dialog" data-backdrop="static" aria-hidden="true">
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

<!-- Modal tambah harga usulan -->
<div class="modal fade" id="tambahUsulanHargaByKompSSH" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="tambahUsulanHargaByKompSSHLabel">Tambah Harga usulan SSH</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div>
					<label for="tambah_harga_komp_kategori" style="display:inline-block">Kategori</label>
					<input type="text" id="tambah_harga_komp_kategori" style="display:block;width:100%;" placeholder="Kategori" disabled>
				</div>
				<div>
					<label for="tambah_harga_komp_nama_komponent" style="display:inline-block">Nama Komponen</label>
					<select id="tambah_harga_komp_nama_komponent" class="js-example-basic-single" style="display:block;width:100%;" placeholder="Nama Komponen"></select>
					<input type="text" id="tambah_harga_show_komp_nama" class="hide" style="width:100%;" placeholder="Nama Komponen" disabled>
				</div>
				<div>
					<label for="tambah_harga_komp_spesifikasi" style="display:inline-block">Spesifikasi</label>
					<input type="text" id="tambah_harga_komp_spesifikasi" style="display:block;width:100%;" placeholder="Spesifikasi" disabled>
				</div>
				<div>
					<label for="tambah_harga_komp_satuan" style="display:inline-block">Satuan</label>
					<input type="text" id="tambah_harga_komp_satuan" style="display:block;width:100%;" placeholder="Satuan" disabled>
				</div>
				<div>
					<label for="tambah_harga_komp_harga_satuan" style="display:inline-block">Harga Satuan</label>
					<input type="text" id="tambah_harga_komp_harga_satuan" style="display:block;width:100%;" placeholder="Harga Satuan">
				</div>
				<div>
					<label for="tambah_harga_komp_akun" style="display:inline-block">Rekening Akun</label>
					<textarea type="text" id="tambah_harga_komp_akun" style="display:block;width:100%;" placeholder="Rekening Akun" disabled></textarea>
				</div>
				<div>
					<label for="tambah_harga_komp_keterangan_lampiran" style="display:inline-block">Keterangan</label>
					<input type="text" id="tambah_harga_komp_keterangan_lampiran" style="display:block;width:100%;" placeholder="Link Google Drive Keterangan">
					<small>*Masukkan link Google Drive berisikan lampiran minimal 3 harga toko beserta gambar.</small>
				</div>
			</div> 
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="submitUsulanTambahHargaSshForm(<?php echo $input['tahun_anggaran']; ?>)">Simpan</button>
				<button type="button" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal tambah akun usulan -->
<div class="modal fade" id="tambahUsulanAkunByKompSSH" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="tambahUsulanAkunByKompSSHLabel">Tambah Rekening Akun usulan SSH</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div>
					<label for="tambah_akun_komp_kategori" style="display:inline-block">Kategori</label>
					<input type="text" id="tambah_akun_komp_kategori" style="display:block;width:100%;" placeholder="Kategori" disabled>
				</div>
				<div><label for="tambah_akun_komp_nama_komponent" style="display:inline-block">Nama Komponen</label>
					<select id="tambah_akun_komp_nama_komponent" class="js-example-basic-single" style="display:block;width:100%;" placeholder="Nama Komponen"></select>
					<input type="text" id="tambah_akun_show_komp_nama" class="hide" style="width:100%;" placeholder="Nama Komponen" disabled>
				</div>
				<div><label for="tambah_akun_komp_spesifikasi" style="display:inline-block">Spesifikasi</label>
					<input type="text" id="tambah_akun_komp_spesifikasi" style="display:block;width:100%;" placeholder="Spesifikasi" disabled>
				</div>
				<div><label for="tambah_akun_komp_satuan" style="display:inline-block">Satuan</label>
					<input type="text" id="tambah_akun_komp_satuan" style="display:block;width:100%;" placeholder="Satuan" disabled>
				</div>
				<div><label for="tambah_akun_komp_harga_satuan" style="display:inline-block">Harga Satuan</label>
					<input type="text" id="tambah_akun_komp_harga_satuan" style="display:block;width:100%;" placeholder="Harga Satuan" disabled>
				</div>
				<div><label for="tambah_akun_komp_akun" style="display:inline-block">Rekening Akun SIPD</label>
					<textarea type="text" id="tambah_akun_komp_akun" style="display:block;width:100%;" placeholder="Rekening Akun" disabled></textarea>
				</div>
				<div><label for="tambah_new_akun_komp" id_standar_harga="" style="display:inline-block">Rekening Akun Usulan</label>
					<select id="tambah_new_akun_komp" name="states[]" multiple="multiple" style="display:block;width:100%;"></select>
				</div>
				<div id="tambah_akun_lampiran"><label for="tambah_akun_komp_keterangan_lampiran" style="display:inline-block">Keterangan</label>
					<input type="text" id="tambah_akun_komp_keterangan_lampiran" style="display:block;width:100%;" placeholder="Link Google Drive Keterangan" disabled>
					<small>*Masukkan link Google Drive berisikan lampiran minimal 3 harga toko beserta gambar.</small>
				</div>
			</div> 
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="submitUsulanTambahAkunSshForm(<?php echo $input['tahun_anggaran']; ?>)">Simpan</button>
				<button type="button" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal tambah usulan ssh -->
<div class="modal fade" id="tambahUsulanSshModal" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Tambah usulan SSH</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div>
					<label for='u_kategori' style='display:inline-block'>Kategori</label>
					<select id='u_kategori' style='display:block;width:100%;'></select>
				</div>
				<div>
					<label for='u_nama_komponen' style='display:inline-block'>Nama Komponen</label>
					<input type='text' id='u_nama_komponen' style='display:block;width:100%;' placeholder='Nama Komponen'>
				</div>
				<div>
					<label for='u_spesifikasi' style='display:inline-block'>Spesifikasi</label>
					<input type='text' id='u_spesifikasi' style='display:block;width:100%;' placeholder='Spesifikasi'>
				</div>
				<div>
					<label for='u_satuan' style='display:inline-block'>Satuan</label>
					<select id='u_satuan' style='display:block;width:100%;'></select>
				</div>
				<div>
					<label for='u_harga_satuan' style='display:inline-block'>Harga Satuan</label>
					<input type='number' id='u_harga_satuan' style='display:block;width:100%;' placeholder='Harga Satuan'>
				</div>
				<div>
					<label for='u_akun' style='display:inline-block'>Rekening Akun</label>
					<select id='u_akun' name='states[]' multiple='multiple' style='display:block;width:100%;'></select>
				</div>
				<div>
					<label for='u_keterangan_lampiran' style='display:inline-block'>Keterangan</label>
					<input type='text' id='u_keterangan_lampiran' style='display:block;width:100%;' placeholder='Link Google Drive Keterangan'>
					<small>*Masukkan link Google Drive berisikan lampiran minimal 3 harga toko beserta gambar.</small>
				</div>
			</div> 
			<div class="modal-footer">
				<button class='btn btn-primary submitBtn' onclick='submitUsulanSshForm(<?php echo $input['tahun_anggaran']; ?>)'>Simpan</button>
                <button type="button" class="components-button btn btn-default" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function(){
		globalThis.tahun = <?php echo $input['tahun_anggaran']; ?>;
		get_data_ssh(tahun)
		.then(function(){
			jQuery('#wrap-loading').show();
            get_data_satuan_ssh(tahun);
            get_data_nama_ssh(tahun);
			jQuery("#usulan_ssh_table_wrapper div:first").addClass("h-100 align-items-center");
			let html_filter = "<select class='ml-3 bulk-action' id='multi_select_action'>"+
				"<option value='0'>Tindakan Massal</option>"+
				"<option value='approve'>Setuju</option>"+
				"<option value='notapprove'>Tolak</option>"+
				"<option value='delete'>Hapus</option></select>"+
			"<button type='submit' class='ml-1 btn btn-secondary' onclick='action_check_data_usulan_ssh()'>Terapkan</button>"+
			"<select class='ml-3 bulk-action' id='search_filter_action'>"+
				"<option value=''>Pilih Filter</option>"+
				"<option value='diterima'>Diterima</option>"+
				"<option value='ditolak'>Ditolak</option>"+
				"<option value='menunggu'>Menunggu</option>"+
				"<option value='sudah_upload_sipd'>Sudah upload SIPD</option>"+
				"<option value='belum_upload_sipd'>Belum upload SIPD</option>"+
			"</select>"+
			"<button type='button' class='ml-1 btn btn-secondary' onclick='action_filter_data_usulan_ssh()'>Saring</button>"
			jQuery("#usulan_ssh_table_length").append(html_filter);
		});
		let get_data = 1;
		jQuery('#tambahUsulanSsh').on('hidden.bs.modal', function () {
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-lg");
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-xl");
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-sm");
			jQuery("#tambahUsulanSsh .modal-body").html("");
			jQuery("#tambahUsulanSsh .modal-footer").removeClass("");
		});
		jQuery('#tambahUsulanSshModal').on('hidden.bs.modal', function () {
			jQuery("#u_kategori").val("").trigger('change');
			jQuery("#u_nama_komponen").val("");
			jQuery("#u_spesifikasi").val("");
			jQuery("#u_satuan").val("").trigger('change');
			jQuery("#u_harga_satuan").val("");
			jQuery("#u_akun").val("").trigger('change');
			jQuery("#u_keterangan_lampiran").val("");
			jQuery("#tambahUsulanSshModal .modal-title").html("");
			jQuery("#tambahUsulanSshModal .submitBtn").text("");
			jQuery("#tambahUsulanSshModal .submitBtn").attr("onclick", '');
		})
		jQuery('#tambahUsulanHargaByKompSSH').on('hidden.bs.modal', function () {
			jQuery("#tambah_harga_komp_kategori").val("");
			jQuery("#tambah_harga_komp_nama_komponent").val("").trigger('change');
			jQuery("#tambah_harga_komp_nama_komponent").next(".select2-container").removeClass("hide");
			jQuery("#tambah_harga_show_komp_nama").addClass("hide");
			jQuery("#tambah_harga_komp_spesifikasi").val("");
			jQuery("#tambah_harga_komp_satuan").val("");
			jQuery("#tambah_harga_komp_harga_satuan").val("");
			jQuery("#tambah_harga_komp_akun").html("");
			jQuery("#tambah_harga_komp_keterangan_lampiran").val("");
			jQuery("#tambahUsulanHargaByKompSSH .modal-title").html("");
			jQuery("#tambahUsulanHargaByKompSSH .submitBtn").text("");
			jQuery("#tambahUsulanHargaByKompSSH .submitBtn").attr("onclick", '');
			jQuery("#tambah_harga_komp_nama_komponent").attr("disabled", false);
		})
		jQuery('#tambahUsulanAkunByKompSSH').on('hidden.bs.modal', function () {
			jQuery("#tambah_akun_komp_kategori").val("");
			jQuery("#tambah_akun_komp_nama_komponent").val("").trigger('change');
			jQuery("#tambah_akun_komp_nama_komponent").next(".select2-container").removeClass("hide");
			jQuery("#tambah_akun_show_komp_nama").addClass("hide");
			jQuery("#tambah_akun_komp_spesifikasi").val("");
			jQuery("#tambah_akun_komp_satuan").val("");
			jQuery("#tambah_akun_komp_harga_satuan").val("");
			jQuery("#tambah_akun_komp_akun").html("");
			jQuery("#tambah_new_akun_komp").val("").trigger('change');
			jQuery("#tambah_new_akun_komp").attr('id_standar_harga', '');;
			jQuery("#tambah_akun_komp_keterangan_lampiran").val("");
			jQuery("#tambah_akun_lampiran").show();
			jQuery("#tambahUsulanAkunByKompSSH .modal-title").html("");
			jQuery("#tambahUsulanAkunByKompSSH .submitBtn").text("");
			jQuery("#tambahUsulanAkunByKompSSH .submitBtn").attr("onclick", '');
			
		})
		jQuery('#checkall').click(function(){
			if(jQuery(this).is(':checked')){
				jQuery('.delete_check').prop('checked', true);
			}else{
				jQuery('.delete_check').prop('checked', false);
			}
		})
	});

	function get_data_ssh(tahun){
		jQuery("#wrap-loading").show();
		return new Promise(function(resolve, reject){
			globalThis.usulanSSHTable = jQuery('#usulan_ssh_table')
			.on('preXhr.dt', function ( e, settings, data ) {
				data.filter = jQuery("#search_filter_action").val();
			} )
			.DataTable({
				"processing": true,
        		"serverSide": true,
		        "ajax": {
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:"post",
					data:{
						'action' : "get_data_usulan_ssh",
						'api_key' : jQuery("#api_key").val(),
						'tahun_anggaran' : tahun
					}
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
		            	"data": "show_kode_komponen",
		            	className: "text-left show-komponen",
						"targets": "no-sort",
						"orderable": false
		            },
		            { "data": "nama_standar_harga" },
		            { "data": "spek_satuan",
		            	className: "text-left spek-satuan",
						"targets": "no-sort",
						"orderable": false
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
		            	"data": "show_keterangan",
		            	className: "text-left kol-keterangan",
						"targets": "no-sort",
						"orderable": false
		            },
					{ 
		            	"data": "show_status",
		            	className: "text-left show_status",
						"targets": "no-sort",
						"orderable": false
		            },
					{ 
		            	"data": "aksi",
		            	className: "text-center",
						"targets": "no-sort",
						"orderable": false
		            }
		        ],
				"initComplete":function( settings, json){
					jQuery("#wrap-loading").hide();
					resolve();
				}
			});
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
				enable_button();
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
				enable_button();
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
				enable_button();
			}
		})
	}
	
	function enable_button(){
		if(
			typeof dataSatuanSsh != 'undefined'
		){
			
			jQuery("#u_satuan").html(dataSatuanSsh.table_content);
			jQuery('#u_satuan').select2();
			var ajax_nama_komponen = {
			  	ajax: {
				    url: "<?php echo admin_url('admin-ajax.php'); ?>",
				    type: 'post',
				    dataType: 'json',
				    data: function (params) {
				      	var query = {
				        	search: params.term,
					        page: params.page || 0,
					        action: 'get_komponen_and_id_kel_ssh',
					        api_key : jQuery("#api_key").val(),
							tahun_anggaran : tahun
				      	}
				      	return query;
				    },
				    processResults: function (data, params) {
				    	console.log('data', data);
				      	return {
					        results: data.results,
					        pagination: {
					          	more: data.pagination.more
					        }
				      	};
				    }
			  	},
			    placeholder: 'Cari komponen',
			    minimumInputLength: 3
			};
			var ajax_kategori = {
			  	ajax: {
				    url: "<?php echo admin_url('admin-ajax.php'); ?>",
				    type: 'post',
				    dataType: 'json',
				    data: function (params) {
				      	var query = {
				        	search: params.term,
					        page: params.page || 0,
					        action: 'get_data_kategori_ssh',
					        api_key : jQuery("#api_key").val(),
							tahun_anggaran : tahun
				      	}
				      	return query;
				    },
				    processResults: function (data, params) {
				    	console.log('data', data);
				      	return {
					        results: data.results,
					        pagination: {
					          	more: data.pagination.more
					        }
				      	};
				    }
			  	},
			    placeholder: 'Cari kategori',
			    minimumInputLength: 3
			};
			var ajax_akun = {
			  	ajax: {
				    url: "<?php echo admin_url('admin-ajax.php'); ?>",
				    type: 'post',
				    dataType: 'json',
				    data: function (params) {
				      	var query = {
				        	search: params.term,
					        page: params.page || 0,
					        action: 'get_data_akun_ssh',
					        api_key : jQuery("#api_key").val(),
					        id_standar_harga : jQuery("#tambah_new_akun_komp").attr('id_standar_harga'),
							tahun_anggaran : tahun
				      	}
				      	return query;
				    },
				    processResults: function (data, params) {
				    	console.log('data', data);
				      	return {
					        results: data.results,
					        pagination: {
					          	more: data.pagination.more
					        }
				      	};
				    }
			  	},
			    placeholder: 'Cari komponen',
			    minimumInputLength: 3
			};
			jQuery('#tambah_harga_komp_nama_komponent').select2(ajax_nama_komponen);
			jQuery('#tambah_akun_komp_nama_komponent').select2(ajax_nama_komponen);
			jQuery('#u_kategori').select2(ajax_kategori);
			jQuery("#u_akun").select2(ajax_akun);
			jQuery("#tambah_new_akun_komp").select2(ajax_akun);

			jQuery('.tambah_ssh').attr('disabled', false);
			jQuery('.tambah_new_ssh').attr('disabled', false);
			jQuery('#wrap-loading').hide();
		}
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

	function tambah_new_ssh(tahun){
		jQuery("#tambahUsulanSshModal .modal-title").html("Tambah usulan SSH");
		jQuery("#tambahUsulanSshModal .submitBtn")
			.attr("onclick", 'submitUsulanSshForm('+tahun+')')
			.attr("disabled", false)
			.text("Simpan");
		jQuery('#tambahUsulanSshModal').modal('show');
	}

	/** Menampilkan data SSH sesuai komponen */
	function get_data_by_name_komponen_ssh(jenis, tahun){
		if(jenis === 'harga'){
			jQuery("#tambah_harga_komp_nama_komponent").on("change", function(){
				var id_standar_harga = jQuery(this).val();
				if(id_standar_harga != null){
					get_data_usulan_ssh_by_komponen('harga',id_standar_harga)
				}
			});
			jQuery('#tambahUsulanHargaByKompSSH').modal('show');
			jQuery("#tambahUsulanHargaByKompSSH .modal-title").html('Tambah Harga usulan SSH');
			jQuery("#tambahUsulanHargaByKompSSH .submitBtn")
				.attr("onclick", 'submitUsulanTambahHargaSshForm('+tahun+')')
				.attr("disabled", false)
				.text("Simpan");
		}else if(jenis === 'akun'){
			jQuery("#tambah_akun_komp_nama_komponent").on("change", function(){
				var id_standar_harga = jQuery(this).val();
				if(id_standar_harga != null){
					get_data_usulan_ssh_by_komponen('akun',id_standar_harga)
				}
			});
			jQuery('#tambahUsulanAkunByKompSSH').modal('show');
			jQuery("#tambahUsulanAkunByKompSSH .modal-title").html('Tambah Rekening Akun usulan SSH');
			jQuery("#tambahUsulanAkunByKompSSH .submitBtn")
				.attr("onclick", 'submitUsulanTambahAkunSshForm('+tahun+')')
				.attr("disabled", false)
				.text("Simpan");
		}
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
				enable_button();
			}
		})
	}

	/** Ambil data detail ssh sesuai komponen */
	function get_data_usulan_ssh_by_komponen(jenis, id_standar_harga){
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
				if(jenis === 'harga'){
					jQuery("#tambah_harga_komp_kategori").val(response.data_ssh_usulan_by_id.kode_kel_standar_harga+" "+response.data_ssh_usulan_by_id.nama_kel_standar_harga);
					jQuery("#tambah_harga_komp_spesifikasi").val(response.data_ssh_usulan_by_id.spek);
					jQuery("#tambah_harga_komp_satuan").val(response.data_ssh_usulan_by_id.satuan);
					jQuery("#tambah_harga_komp_akun").html(response.table_content);

				}else if(jenis === 'akun'){
					jQuery("#tambah_akun_komp_kategori").val(response.data_ssh_usulan_by_id.kode_kel_standar_harga+" "+response.data_ssh_usulan_by_id.nama_kel_standar_harga);
					jQuery("#tambah_akun_komp_spesifikasi").val(response.data_ssh_usulan_by_id.spek);
					jQuery("#tambah_akun_komp_satuan").val(response.data_ssh_usulan_by_id.satuan);
					jQuery("#tambah_akun_komp_harga_satuan").val(response.data_ssh_usulan_by_id.harga);
					jQuery("#tambah_akun_komp_keterangan_lampiran").val(response.data_ssh_usulan_by_id.keterangan_lampiran);
					jQuery("#tambah_akun_komp_akun").html(response.table_content);
				}
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
		if(kategori.trim() == '' || nama_komponen.trim() == '' || spesifikasi.trim() == '' || satuan.trim() == '' || harga_satuan.trim() == '' || akun == '' || keterangan_lampiran.trim() == '' ){
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
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
					}else{
						alert(response.message);
					}
					jQuery('#tambahUsulanSshModal').modal('hide');
					jQuery("#wrap-loading").hide();
					usulanSSHTable.ajax.reload();	
				}
			});
		}
	}

	//edit akun ssh usulan
	// function edit_akun_ssh_usulan(id_standar_harga){
		// jQuery('#tambahUsulanSsh').modal('show');
		// jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-xl modal-sm");
		// jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-lg");
		// jQuery("#tambahUsulanSshLabel").html("Tambah Rekening Akun");
		// jQuery("#tambahUsulanSsh .modal-body").html("<div class=\'akun-ssh-desc\'><table>"+
		// 			"<tr><td class=\'first-desc\'>Kategori</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_kategori\'></span></td></tr>"+
		// 			"<tr><td class=\'first-desc\'>Nama Komponen</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_nama_komponen\'></span></td></tr>"+
		// 			"<tr><td class=\'first-desc\'>Spesifikasi</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_spesifikasi\'></span></td></tr>"+
		// 			"<tr><td class=\'first-desc\'>Satuan</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_satuan\'></span></td></tr>"+
		// 			"<tr><td class=\'first-desc\'>Harga Satuan</td><td class=\'sec-desc\'>:</td><td><span id=\'u_data_harga_satuan\'></span></td></tr>"+
		// 			"<tr><td class=\'first-desc\'>Rekening Akun</td><td class=\'sec-desc\'>:</td><td class=\'pt-0\'><div class=\'ul-desc-akun\'></div></td></tr></table>"+
		// 			"<div class=\'add_akun_to_ssh\' style=\'display:none;\'><label for=\'u_data_akun\' style=\'display:inline-block\'>Tambah Rekening Akun</label>"+
		// 			"<input type=\'hidden\' id=\'u_data_add_ssh_akun_id\'>"+
		// 			"<select id=\'u_data_akun\' class=\'select2-multiple\' name=\'states[]\' multiple=\'multiple\' style=\'display:block;width:100%;\' required></select>"+
		// 			"<button style=\'margin: 1rem 0 2rem 0;border-radius:0.2rem;\' class=\'submitBtn btn_add_akun_to_ssh btn-success\' onclick=\'save_add_akun_to_ssh("+tahun+")\'>Simpan Rekening</button></div></div>");
		// jQuery("#tambahUsulanSsh .modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'btn_add_akun_to_ssh\' onclick=\'field_add_akun_to_ssh()\'>Tambah Rekening</button>");
		// jQuery("#u_data_akun").html(dataAkunSsh.table_content);
		// jQuery('.select2-multiple').select2({
		// 	dropdownParent: jQuery('#tambahUsulanSsh')
		// });
		// let iconX = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16" style="color:red;"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';

		// jQuery.ajax({
		// 	url: "<?php echo admin_url('admin-ajax.php'); ?>",
		// 	type:"post",
		// 	data:{
		// 		'action' : "get_data_ssh_usulan_by_id",
		// 		'api_key' : jQuery("#api_key").val(),
		// 		'id_standar_harga' : id_standar_harga,
		// 		'tahun_anggaran'	: tahun
		// 	},
		// 	dataType: "json",
		// 	success:function(response){
		// 		jQuery("#u_data_kategori").html(response.data.kode_standar_harga);
		// 		jQuery("#u_data_nama_komponen").html(response.data.nama_standar_harga);
		// 		jQuery("#u_data_spesifikasi").html(response.data.spek);
		// 		jQuery("#u_data_satuan").html(response.data.satuan);
		// 		jQuery("#u_data_harga_satuan").html(response.data.harga);
		// 		jQuery("#u_data_add_ssh_akun_id").val(response.data.id_standar_harga);
		// 		var data_akun = response.data_akun;
		// 		jQuery.each( data_akun, function( key, value ) {
		// 			jQuery(".ul-desc-akun").append(`<div class="row" id="rek_akun_${value.id}" style="border-bottom: 1px solid #ebebeb;padding: 6px;">
		// 			<div class="col-10">${value.nama_akun}</div>
		// 			<div class="col-2 text-center"><a href="#" onclick="return delete_akun_ssh_usulan('${id_standar_harga}','${value.id}');" title="Delete rekening akun usulan SSH">${iconX}</a></div></div>`);
		// 		});
		// 	}
		// })
	// }

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
		jQuery("#tambahUsulanSsh .modal-body").html("<div class=\'akun-ssh-verify\'><table>"+
					"<tr><td><input class=\'verify-ssh\' id=\'verify-ssh-yes\' name=\'verify_ssh\' value=\'1\' type=\'radio\' checked><label for=\'verify-ssh-yes\'>Terima</label></td>"+
					"<td><input class=\'verify-ssh\' id=\'verify-ssh-no\' name=\'verify_ssh\' value=\'0\' type=\'radio\'><label for=\'verify-ssh-no\'>Tolak</label></td></tr>"+
					"<tr class=\'add-desc-verify-ssh\' style=\'display:none;\'><td colspan=\'2\'><label for=\'alasan_verify_ssh\' style=\'display:inline-block;\'>Alasan</label><textarea id=\'alasan_verify_ssh\'></textarea></td></tr></div>");
		jQuery("#tambahUsulanSsh .modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'btn_submit_verify_ssh\' onclick=\'submit_verify_ssh("+id_standar_ssh+")\'>Simpan</button>");
		jQuery("#verify-ssh-no").on("click", function() {
			jQuery(".add-desc-verify-ssh").show();
		})
		jQuery("#verify-ssh-yes").on("click", function() {
			jQuery(".add-desc-verify-ssh").hide();
		})
	}

	/** submit verifikasi usulan ssh */
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

	/** submit tambah usulan harga ssh */
	function submitUsulanTambahHargaSshForm(tahun){
		var id_standar_harga = jQuery('#tambah_harga_komp_nama_komponent').val();
		var harga_satuan = jQuery('#tambah_harga_komp_harga_satuan').val();
		var keterangan_lampiran = jQuery('#tambah_harga_komp_keterangan_lampiran').val();
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
					jQuery('#tambahUsulanHargaByKompSSH').modal('hide')
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery('.modal-body').css('opacity', '');
					jQuery("#wrap-loading").hide();
					usulanSSHTable.ajax.reload();
				}
			});
		}
	}

	/** Submit tombol usulan akun rekening */
	function submitUsulanTambahAkunSshForm(tahun){
		var id_standar_harga = jQuery('#tambah_akun_komp_nama_komponent').val();
		var new_akun = jQuery('#tambah_new_akun_komp').val();
		jQuery("#wrap-loading").show();
		if(new_akun == '' || id_standar_harga.trim() == ''){
			jQuery("#wrap-loading").hide();
			alert('Harap diisi semua, tidak ada yang kosong.');
			return false;
		}else{
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action' : 'submit_tambah_akun_ssh',
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
					'id_standar_harga' : id_standar_harga,
					'new_akun' : new_akun
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
					jQuery('#tambahUsulanAkunByKompSSH').modal('hide')
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery('.modal-body').css('opacity', '');
					jQuery("#wrap-loading").hide();
					usulanSSHTable.ajax.reload();
				}
			});
		}
	}

	/** edit akun ssh usulan */
	function edit_ssh_usulan(status_jenis_usulan,id_standar_harga){
		jQuery('#wrap-loading').show();

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
				jQuery('#wrap-loading').hide();
				if(status_jenis_usulan === 'tambah_akun'){
					jQuery('#tambahUsulanAkunByKompSSH').modal('show');
					jQuery("#tambahUsulanAkunByKompSSH .modal-title").html('Edit Tambah Akun Usulan SSH');
					jQuery("#tambah_akun_komp_kategori").val(response.data.kode_kel_standar_harga+" "+response.data.nama_kel_standar_harga);
					jQuery("#tambah_akun_komp_nama_komponent").next(".select2-container").addClass("hide");
					jQuery("#tambah_akun_show_komp_nama").removeClass("hide");
					jQuery("#tambah_akun_show_komp_nama").val(response.data.nama_standar_harga);
					jQuery("#tambah_akun_komp_spesifikasi").val(response.data.spek);
					jQuery("#tambah_akun_komp_satuan").val(response.data.satuan);
					jQuery("#tambah_akun_komp_harga_satuan").val(response.data.harga);
					jQuery("#tambah_akun_komp_keterangan_lampiran").val(response.data.keterangan_lampiran);
					jQuery("#tambah_akun_komp_akun").html(response.table_content_akun);
					jQuery("#tambah_new_akun_komp").attr('id_standar_harga', id_standar_harga);
					response.data_akun_usulan.map(function(b, i){
						var myText = b.id_akun+" "+b.nama_akun;
						var option = new Option(myText,b.id_akun, true, true);
						jQuery("#tambah_new_akun_komp").append(option).trigger('change');
						jQuery("#tambah_new_akun_komp").trigger({
							type: 'select2:select',
							params: {
								data: b.id_akun
							}
						});
					});
					jQuery("#tambah_akun_lampiran").hide();
					jQuery("#tambahUsulanAkunByKompSSH .submitBtn")
						.attr('onclick', 'submitEditTambahAkunUsulanSshForm('+id_standar_harga+', '+tahun+')')
						.attr("disabled", false)
						.text("Simpan");
				}else if(status_jenis_usulan === 'tambah_harga'){
					jQuery('#tambahUsulanHargaByKompSSH').modal('show');
					jQuery("#tambahUsulanHargaByKompSSH .modal-title").html('Edit Tambah Harga Usulan SSH');
					jQuery("#tambah_harga_komp_kategori").val(response.data.kode_kel_standar_harga+" "+response.data.nama_kel_standar_harga);
					jQuery("#tambah_harga_komp_nama_komponent").next(".select2-container").addClass("hide");
					jQuery("#tambah_harga_show_komp_nama").removeClass("hide");
					jQuery("#tambah_harga_show_komp_nama").val(response.data.nama_standar_harga);
					jQuery("#tambah_harga_komp_spesifikasi").val(response.data.spek);
					jQuery("#tambah_harga_komp_satuan").val(response.data.satuan);
					jQuery("#tambah_harga_komp_harga_satuan").val(response.data.harga);
					jQuery("#tambah_harga_komp_keterangan_lampiran").val(response.data.keterangan_lampiran);
					jQuery("#tambah_harga_komp_akun").html(response.table_content_akun);
					jQuery("#tambahUsulanHargaByKompSSH .submitBtn")
						.attr('onclick', 'submitEditTambahHargaUsulanSshForm('+id_standar_harga+', '+tahun+')')
						.attr("disabled", false)
						.text("Simpan");
				}else if(status_jenis_usulan == 'tambah_baru'){
					jQuery('#tambahUsulanSshModal').modal('show');
					jQuery("#tambahUsulanSshModal .modal-title").html('Edit Tambah Usulan SSH');
					var myText = response.data_kel_standar_harga_by_id.tipe_kelompok+" "+response.data_kel_standar_harga_by_id.kode_kategori+" "+response.data_kel_standar_harga_by_id.uraian_kategori;
					var option = new Option(myText,response.data_kel_standar_harga_by_id.id_kategori, true, true);	
					jQuery("#u_kategori").append(option).trigger('change');
					jQuery("#u_kategori").trigger({
						type: 'select2:select',
						params: {
							data: response.data_kel_standar_harga_by_id.id_kategori
						}
					});
					jQuery('#u_satuan').val(response.data.satuan).trigger('change');
					jQuery("#u_nama_komponen").val(response.data.nama_standar_harga);
					jQuery("#u_spesifikasi").val(response.data.spek);
					jQuery("#u_harga_satuan").val(response.data.harga);
					jQuery("#u_keterangan_lampiran").val(response.data.keterangan_lampiran);
					response.data_akun_usulan.map(function(b, i){
						var myText = b.id_akun+" "+b.nama_akun;
						var option = new Option(myText,b.id_akun, true, true);
						jQuery("#u_akun").append(option).trigger('change');
						jQuery("#u_akun").trigger({
							type: 'select2:select',
							params: {
								data: b.id_akun
							}
						});
					});
					jQuery("#tambahUsulanSshModal .submitBtn")
						.attr('onclick', 'submitEditUsulanSshForm('+id_standar_harga+', '+tahun+')')
						.attr('disabled', false)
						.text('Simpan');
				}
			}
		})
	}

	function cannot_change_ssh_usulan(option,jenis){
		if(jenis == 'usulan'){
			alert('Tidak bisa '+option+' usulan SSH karena sudah ditahap verifikasi');
		}else if(jenis == 'upload'){
			alert('Tidak bisa '+option+' usulan SSH karena sudah ditahap upload SIPD');
		}
	}

	function submitEditUsulanSshForm(id_standar_harga,tahun){
		var kategori = jQuery('#u_kategori').val();
		var nama_komponen = jQuery('#u_nama_komponen').val();
		var spesifikasi = jQuery('#u_spesifikasi').val();
		var satuan = jQuery('#u_satuan').val();
		var harga_satuan = jQuery('#u_harga_satuan').val();
		var keterangan_lampiran = jQuery('#u_keterangan_lampiran').val();
		var akun = jQuery('#u_akun').val();
		jQuery("#wrap-loading").show();
		if(
			kategori.trim() == ''
			|| nama_komponen.trim() == ''
			|| spesifikasi.trim() == ''
			|| satuan.trim() == ''
			|| harga_satuan.trim() == ''
		){
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
					'akun' 					: akun,
					'tahun_anggaran'		: tahun,
					'keterangan_lampiran'	: keterangan_lampiran,
				},
				dataType: 'json',
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
					}else{
						alert(`GAGAL! ${response.message}`);
					}
					jQuery("#wrap-loading").hide();
					jQuery('#tambahUsulanSshModal').modal('hide')
					usulanSSHTable.ajax.reload();	
				}
			});
		}
	}

	/** submit edit tambah usulan harga ssh */
	function submitEditTambahHargaUsulanSshForm(id_standar_harga,tahun){
		var harga_satuan = jQuery('#tambah_harga_komp_harga_satuan').val();
		var keterangan_lampiran = jQuery('#tambah_harga_komp_keterangan_lampiran').val();
		jQuery("#wrap-loading").show();
		if(harga_satuan.trim() == '' || keterangan_lampiran.trim() == '' || id_standar_harga == ''){
			jQuery("#wrap-loading").hide();
			alert('Harap diisi semua, tidak ada yang kosong.');
			return false;
		}else{
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action' : 'submit_edit_tambah_harga_ssh',
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
					jQuery('#tambahUsulanHargaByKompSSH').modal('hide')
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery('.modal-body').css('opacity', '');
					jQuery("#wrap-loading").hide();
					usulanSSHTable.ajax.reload();
				}
			});
		}
	}

	/** submit edit tambah akun ssh */
	function submitEditTambahAkunUsulanSshForm(id_standar_harga,tahun){
		var new_akun = jQuery('#tambah_new_akun_komp').val();
		jQuery("#wrap-loading").show();
		if(new_akun == ''){
			jQuery("#wrap-loading").hide();
			alert('Harap diisi semua, tidak ada yang kosong.');
			return false;
		}else{
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action' : 'submit_edit_tambah_akun_ssh',
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
					'id_standar_harga' : id_standar_harga,
					'new_akun' : new_akun
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
					jQuery('#tambahUsulanAkunByKompSSH').modal('hide')
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery('.modal-body').css('opacity', '');
					jQuery("#wrap-loading").hide();
					usulanSSHTable.ajax.reload();
				}
			});
		}
	}

	function delete_ssh_usulan(id_standar_harga){
		let confirmDelete = confirm("Apakah anda yakin akan menghapus usulan SSH?");
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
		let confirmDelete = confirm("Apakah anda yakin akan menghapus rekening akun usulan SSH?");
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

	// Multi action
	function action_check_data_usulan_ssh(){
		var data_action = jQuery('#multi_select_action').val();

		var check_id_arr = [];

		jQuery("input:checkbox[class=delete_check]:checked").each(function () {
			check_id_arr.push(jQuery(this).val());
		});
		jQuery("#wrap-loading").show();

		if(check_id_arr.length > 0){

			if(data_action == 'delete'){
				var confirmdelete = confirm("Apakah kamu yakin hapus data?");
				if (confirmdelete == true) {
					jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type:"post",
						data:{
							'action' : "submit_delete_check_usulan_ssh",
							'api_key' : jQuery("#api_key").val(),
							'tahun_anggaran' : tahun,
							'check_id_arr'	: check_id_arr
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
				}else{
					jQuery("#wrap-loading").hide();
				}
			}else if(data_action == 'approve'){
				var confirmapprove = confirm("Apakah kamu yakin menyetujui data?");
				if (confirmapprove == true) {
					jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type:"post",
						data:{
							'action' : "submit_approve_check_usulan_ssh",
							'api_key' : jQuery("#api_key").val(),
							'tahun_anggaran' : tahun,
							'check_id_arr'	: check_id_arr,
							'alasan': '',
							'status': data_action
						},
						dataType: "json",
						success:function(response){
							jQuery("#wrap-loading").hide();
							if(response.status == 'success'){
								alert('Data berhasil disetujui.');
							}else{
								alert(`GAGAL! ${response.message}`);
							}
							usulanSSHTable.ajax.reload();
						}
					})
				}else{
					jQuery("#wrap-loading").hide();
				}
			}else if(data_action == 'notapprove'){
				var reason = prompt("Alasan:", "");
				if(reason != null || reason != ""){
					var confirmnotapprove = confirm("Apakah kamu yakin menolak data?");
					if (confirmnotapprove == true) {
						jQuery.ajax({
							url: "<?php echo admin_url('admin-ajax.php'); ?>",
							type:"post",
							data:{
								'action' : "submit_approve_check_usulan_ssh",
								'api_key' : jQuery("#api_key").val(),
								'tahun_anggaran' : tahun,
								'check_id_arr'	: check_id_arr,
								'alasan': reason,
								'status': data_action
							},
							dataType: "json",
							success:function(response){
								jQuery("#wrap-loading").hide();
								if(response.status == 'success'){
									alert('Data berhasil ditolak.');
								}else{
									alert(`GAGAL! ${response.message}`);
								}
								usulanSSHTable.ajax.reload();
							}
						})
					}
				}
				jQuery("#wrap-loading").hide();
			}else{
				jQuery("#wrap-loading").hide();
				alert('Tindakan massal tidak dipilih!');
			}

		}else if(check_id_arr.length == 0){
			jQuery("#wrap-loading").hide();
			alert('Tidak ada data dipilih!');
		}
	}

	function action_filter_data_usulan_ssh(){
		usulanSSHTable.draw();
	}

	function readMore(btn){
		let post = btn.parentElement;
		post.querySelector(".dots").classList.toggle("hide");
		post.querySelector(".more").classList.toggle("hide");
		btn.textContent == "more" ? btn.textContent = "less" : btn.textContent = "more";
	}

</script> 