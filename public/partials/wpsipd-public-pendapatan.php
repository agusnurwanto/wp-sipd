<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

global $wpdb;
$data_skpd = [];
if(!empty($input['id_skpd'])){
	$data_skpd = $wpdb->get_row($wpdb->prepare(
				'SELECT nama_skpd
				FROM data_unit
				WHERE
					id_skpd=%d
					AND tahun_anggaran=%d',
				$input['id_skpd'],
				$input['tahun_anggaran']
			), ARRAY_A);
}

$nama_skpd = (!empty($data_skpd['nama_skpd'])) ? $data_skpd['nama_skpd'] : '';


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
		<!-- <h4 style="text-align: center; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">'.$nama_laporan.'</h4> -->
		<h3 class="text-center" style="margin:3rem 0 0 0;">Halaman Pendapatan  <?php echo $nama_skpd; ?></h3>
		<h3 class="text-center" style="margin:0 0 3rem 0;">Tahun Anggaran  <?php echo $input['tahun_anggaran']; ?></h3>
		<div style="margin-bottom: 25px;">
			<button class="btn btn-primary tambah_pendapatan" onclick="tambah_pendapatan();">Tambah Pendapatan</button>
		</div>
		<table id="data_pendapatan_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center">Rekening</th>
					<th class="text-center">Uraian</th>
					<th class="text-center">Keterangan</th>
					<th class="text-center">Nilai</th>
					<th class="text-center" style="width: 250px;">Aksi</th>
				</tr>
			</thead>
			<tbody id="data_body">
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade mt-4" id="modalPendapatan" role="dialog" aria-labelledby="modalPendapatanLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalPendapatanLabel">Tambah Pendapatan</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <form id="form-pendapatan">
					<div>
						<label for='pend_perangkat_daerah' style='display:inline-block'>Perangkat Daerah</label>
						<input type='text' id='pend_perangkat_daerah' name="pend_perangkat_daerah" style='display:block;width:100%;' value="<?php echo $nama_skpd; ?>" placeholder='Perangkat Daerah'>
					</div>
					<div>
						<label for="pend_rekening" style='display:inline-block'>Rekening</label>
						<select id="pend_rekening" class="pend_rekening" name="pend_rekening">
							<option value="">Pilih Rekening</option>
						</select>
					</div>
					<div>
						<label for="pend_keterangan" style="display:inline-block">Keterangan</label>
						<textarea name="pend_keterangan" id="pend_keterangan" rows="5" style="display:inline-block;width:100%;"></textarea>
					</div>
					<div>
						<label for="pend_nilai" style='display:inline-block'>Nilai</label>
						<input type="number" id="pend_nilai" name="pend_nilai" style="display:inline-block;width:100%;">
					</div>
				</form>
			</div> 
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="submitTambahPendapatanForm()">Simpan</button>
				<button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<div class="report"></div>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
<script>
	jQuery(document).ready(function(){
		window.tahun_anggaran = <?php echo $input['tahun_anggaran']; ?>;
		window.this_ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>"
		window.id_skpd = "<?php echo $input['id_skpd']; ?>"

		get_data_pendapatan();

		// jQuery('#modalPendapatan').on('hidden.bs.modal', function () {
			
		// })
	});

	function rekening_akun(){
        return new Promise(function(resolve, reject){
            if(typeof master_rekening_akun == 'undefined'){
                jQuery("#wrap-loading").show();
                jQuery.ajax({
                    method: 'POST',
                    url: this_ajax_url,
                    dataType: 'json',
                    data: {
                        'action': 'get_data_rekening_pendapatan',
                        'api_key': jQuery('#api_key').val(),
                        'tahun_anggaran': tahun_anggaran
                    },
                    success:function(response){
                        window.master_rekening_akun = response.data;
                        jQuery("#wrap-loading").hide();
                        let option='<option value="">Pilih Rekening</option>';
        				response.data.map(function(value, index){
                            option+='<option value="'+value.kode_akun+'">'+value.kode_akun+' '+value.nama_akun+'</option>';
                        })

        				jQuery(".pend_rekening").html(option);
                        jQuery(".pend_rekening").select2({width: '100%'});
                    }
                });
            }
			resolve();
        });
	}

	/** get data pendapatan */
	function get_data_pendapatan(){
		jQuery("#wrap-loading").show();
		window.pendapatanTable = jQuery('#data_pendapatan_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: this_ajax_url,
				type:"post",
				data:{
					'action' 		: "get_data_pendapatan_renja",
					'api_key' 		: jQuery("#api_key").val(),
					'id_skpd' 		: id_skpd,
					'tahun_anggaran': tahun_anggaran
				}
			},
			"initComplete":function( settings, json){
				jQuery("#wrap-loading").hide();
			},
			"columns": [
				{ 
					"data": "kode_akun",
					className: "text-center"
				},
				{ 
					"data": "nama_akun",
					className: "text-center"
				},
				{ 
					"data": "keterangan",
					className: "text-center"
				},
				{ 
					"data": "total",
					className: "text-center"
				},
				{ 
					"data": "aksi",
					className: "text-center"
				}
			]
		});
	}

	/** show modal tambah pendapatan */
	function tambah_pendapatan(){
		rekening_akun()
		.then(function(){
			jQuery("#modalPendapatan .modal-title").html("Tambah Pendapatan");
			jQuery("#modalPendapatan .submitBtn")
				.attr("onclick", 'submitTambahPendapatanForm()')
				.attr("disabled", false)
				.text("Simpan");
			jQuery('#modalPendapatan').modal('show');
		})
	}

	/** Submit tambah pendapatan */
	function submitTambahPendapatanForm(){
		jQuery("#wrap-loading").show()
		let form = get_form_data(jQuery("#form-pendapatan"));
		if(form.id_rekening == '' || form.keterangan == '' || form.nilai == '' || tahun_anggaran == '' || id_skpd == ''){
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		}else{
			jQuery.ajax({
				url:this_ajax_url,
				method: 'post',
				dataType: 'json',
				data:{
					'action'			: 'submit_pendapatan',
					'api_key'			: jQuery("#api_key").val(),
                    'data'				: JSON.stringify(form),
					'tahun_anggaran'	: tahun_anggaran,
					'id_skpd'			: id_skpd
				},
				beforeSend: function() {
					jQuery('.submitBtn').attr('disabled','disabled')
				},
				success: function(response){
					jQuery('#modalPendapatan').modal('hide')
					jQuery('#wrap-loading').hide()
					if(response.status == 'success'){
						alert('Data berhasil ditambahkan')
						pendapatanTable.ajax.reload()
					}else{
						alert(response.message)
					}
					reset_form();
				}
			})
		}
		jQuery('#modalPendapatan').modal('hide');
	}

	function get_form_data($form){
		let unindexed_array = $form.serializeArray();
		let data = {};
        unindexed_array.map(function(b, i){
			data[b.name] = b.value;
        })
		console.log(data);
        return data;
	}

	function reset_form(){
		jQuery('.pend_rekening').val(null).trigger('change');
		jQuery("#pend_keterangan").val("")
		jQuery("#pend_keterangan").val("")
	}

	/** edit pendapatan */
	function edit_data_pendapatan(id){
		rekening_akun()
		.then(function(){
			jQuery('#modalPendapatan').modal('show');
			jQuery("#modalPendapatan .modal-title").html("Edit Pendapatan");
			jQuery("#modalPendapatan .submitBtn")
				.attr("onclick", 'submitEditPendapatan('+id+')')
				.attr("disabled", false)
				.text("Simpan");
			jQuery("#wrap-loading").show()
			jQuery.ajax({
				url: this_ajax_url,
				method: 'post',
				dataType: 'json',
				data:{
					'action' 			: "get_data_pendapatan_by_id",
					'api_key' 			: jQuery("#api_key").val(),
					'id_pendapatan' 	: id
				},
				dataType: "json",
				success:function(response){
					jQuery("#wrap-loading").hide()
					jQuery("#pend_rekening").val(response.data.kode_akun).trigger('change');
					jQuery("#pend_keterangan").val(response.data.keterangan);
					jQuery("#pend_nilai").val(response.data.total);
				}
			})
		})
	}

	function submitEditPendapatan(id_pendapatan){
		jQuery("#wrap-loading").show()
		let form = get_form_data(jQuery("#form-pendapatan"));
		if(form.id_rekening == '' || form.keterangan == '' || form.nilai == '' || tahun_anggaran == '' || id_skpd == '' || id_pendapatan == ''){
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		}else{
			jQuery.ajax({
				url:this_ajax_url,
				method: 'post',
				dataType: 'json',
				data:{
					'action'			: 'submit_edit_pendapatan',
					'api_key'			: jQuery("#api_key").val(),
                    'data'				: JSON.stringify(form),
					'tahun_anggaran'	: tahun_anggaran,
					'id_skpd'			: id_skpd,
					'id_pendapatan'		: id_pendapatan
				},
				beforeSend: function() {
					jQuery('.submitBtn').attr('disabled','disabled')
				},
				success: function(response){
					jQuery('#modalPendapatan').modal('hide')
					jQuery('#wrap-loading').hide()
					if(response.status == 'success'){
						alert('Data berhasil diperbarui')
						pendapatanTable.ajax.reload()
					}else{
						alert(`GAGAL! \n${response.message}`)
					}
					reset_form();
				}
			})
		}
		jQuery('#modalPendapatan').modal('hide');
	}

	function hapus_data_pendapatan(id_pendapatan){
		let confirmDelete = confirm("Apakah anda yakin akan menghapus pendatapan?");
		if(confirmDelete){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url:this_ajax_url,
				method: 'post',
				data:{
					'action' 			: 'submit_delete_pendapatan',
					'api_key'			: jQuery("#api_key").val(),
					'id_pendapatan'		: id_pendapatan
				},
				dataType: 'json',
				success:function(response){
					jQuery('#wrap-loading').hide();
					if(response.status == 'success'){
						alert('Data berhasil dihapus!.');
						pendapatanTable.ajax.reload();	
					}else{
						alert(`GAGAL! \n${response.message}`);
					}
				}
			});
		}
	}

</script> 
