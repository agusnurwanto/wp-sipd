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

$namaJadwal = '-';
$mulaiJadwal = '-';
$selesaiJadwal = '-';

$timezone = get_option('timezone_string');

$cek_jadwal = $this->validasi_jadwal_perencanaan('renja',$input['tahun_anggaran']);
$jadwal_lokal = $cek_jadwal['data'];
$add_renja = '';
if(!empty($jadwal_lokal)){
    if(!empty($jadwal_lokal[0]['relasi_perencanaan'])){
        $relasi = $wpdb->get_row("
            SELECT 
                id_tipe 
            FROM `data_jadwal_lokal`
            WHERE id_jadwal_lokal=".$jadwal_lokal[0]['relasi_perencanaan']);

        $relasi_perencanaan = $jadwal_lokal[0]['relasi_perencanaan'];
        $id_tipe_relasi = $relasi->id_tipe;
    }
	$namaJadwal = $jadwal_lokal[0]['nama'];
	$mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
	$selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];
}

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
		<h3 class="text-center" style="margin:3rem 0;">Halaman Pengeluaran  </br><?php echo $nama_skpd; ?> </br>Tahun Anggaran  <?php echo $input['tahun_anggaran']; ?></h3>
		<div style="margin-bottom: 25px;">
			<button class="btn btn-primary tambah_pengeluaran" onclick="tambah_pengeluaran();">Tambah Pengeluaran</button>
		</div>
		<table id="data_pengeluaran_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
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

<div class="modal fade mt-4" id="modalPengeluaran" role="dialog" aria-labelledby="modalPengeluaranLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalPengeluaranLabel">Tambah Pengeluaran</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <form id="form-pengeluaran">
					<div>
						<label for='pend_perangkat_daerah' style='display:inline-block'>Perangkat Daerah</label>
						<input type='text' id='pend_perangkat_daerah' name="pend_perangkat_daerah" style='display:block;width:100%;' value="<?php echo $nama_skpd; ?>" placeholder='Perangkat Daerah'>
					</div>
					<div>
						<label for="pend_rekening" style='display:inline-block'>Rekening</label>
						<select id="pend_rekening" class="pend_rekening" style='display:block;width:100%;' name="pend_rekening"></select>
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
				<button class="btn btn-primary submitBtn" onclick="submitTambahPengeluaranForm()">Simpan</button>
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

    	var mySpace = '<div style="padding:3rem;"></div>';
    	jQuery('body').prepend(mySpace);

    	var dataHitungMundur = {
    		'namaJadwal' : '<?php echo ucwords($namaJadwal)  ?>',
    		'mulaiJadwal' : '<?php echo $mulaiJadwal  ?>',
    		'selesaiJadwal' : '<?php echo $selesaiJadwal  ?>',
    		'thisTimeZone' : '<?php echo $timezone ?>'
    	}
    	penjadwalanHitungMundur(dataHitungMundur);

		get_data_pengeluaran();

		// jQuery('#modalPengeluaran').on('hidden.bs.modal', function () {
			
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
                        'action': 'get_data_rekening_pengeluaran',
                        'api_key': jQuery('#api_key').val(),
                        'tahun_anggaran': tahun_anggaran
                    },
                    success:function(response){
                        window.master_rekening_akun = response;
                        jQuery("#wrap-loading").hide();

						enable_button();

						resolve();
                    }
                });
            }else{
				console.log('data rekening sudah ada')
				enable_button();
				resolve();
			}
        });
	}

	function enable_button(){
		if(
			typeof master_rekening_akun != 'undefined'
		){
			var ajax_kategori = {
				ajax: {
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type: 'post',
					dataType: 'json',
					data: function (params) {
							var query = {
							search: params.term,
							page: params.page || 0,
							action: 'get_data_rekening_pengeluaran',
							api_key : jQuery("#api_key").val(),
							tahun_anggaran : tahun_anggaran
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
				placeholder: 'Cari Rekening',
				minimumInputLength: 3,
				width: '100%'
			};
			jQuery('.pend_rekening').select2(ajax_kategori);
		}
	}

	/** get data pengeluaran */
	function get_data_pengeluaran(){
		jQuery("#wrap-loading").show();
		window.pengeluaranTable = jQuery('#data_pengeluaran_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: this_ajax_url,
				type:"post",
				data:{
					'action' 		: "get_data_pengeluaran_renja",
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
			],
			drawCallback: function(settings) {
				var pagination = jQuery(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
				pagination.toggle(this.api().page.info().pages > 1);

				let data_ajax = this.api().data();
				let total_nilai = 0;
				data_ajax.map(function(b, i){
					total_nilai = total_nilai + parseInt(b.total)
				})

				jQuery("#data_pengeluaran_table .total_nilai").remove()
				jQuery("#data_pengeluaran_table").append('<tfoot class="total_nilai"><tr><th class="text-center" colspan="3">total nilai</th><th class="text-center">'+total_nilai+'</th><th class="text-center"></th></tr></tfoot>');
			}
		});
	}

	/** show modal tambah pengeluaran */
	function tambah_pengeluaran(){
		rekening_akun()
		.then(function(){
			jQuery("#modalPengeluaran .modal-title").html("Tambah Pengeluaran");
			jQuery("#modalPengeluaran .submitBtn")
				.attr("onclick", 'submitTambahPengeluaranForm()')
				.attr("disabled", false)
				.text("Simpan");
			jQuery('#modalPengeluaran').modal('show');
		})
	}

	/** Submit tambah pengeluaran */
	function submitTambahPengeluaranForm(){
		jQuery("#wrap-loading").show()
		let form = get_form_data(jQuery("#form-pengeluaran"));
		if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
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
						'action'			: 'submit_pengeluaran',
						'api_key'			: jQuery("#api_key").val(),
						'data'				: JSON.stringify(form),
						'tahun_anggaran'	: tahun_anggaran,
						'id_skpd'			: id_skpd
					},
					beforeSend: function() {
						jQuery('.submitBtn').attr('disabled','disabled')
					},
					success: function(response){
						jQuery('#modalPengeluaran').modal('hide')
						jQuery('#wrap-loading').hide()
						if(response.status == 'success'){
							alert('Data berhasil ditambahkan')
							pengeluaranTable.ajax.reload()
						}else{
							alert(response.message)
						}
						reset_form();
					}
				})
			}
		}
		jQuery('#modalPengeluaran').modal('hide');
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
		jQuery("#pend_nilai").val("")
	}

	/** edit pengeluaran */
	function edit_data_pengeluaran(id){
		rekening_akun()
		.then(function(){
			jQuery('#modalPengeluaran').modal('show');
			jQuery("#modalPengeluaran .modal-title").html("Edit Pengeluaran");
			jQuery("#modalPengeluaran .submitBtn")
				.attr("onclick", 'submitEditPengeluaran('+id+')')
				.attr("disabled", false)
				.text("Simpan");
			jQuery("#wrap-loading").show()
			jQuery.ajax({
				url: this_ajax_url,
				method: 'post',
				dataType: 'json',
				data:{
					'action' 			: "get_data_pengeluaran_by_id",
					'api_key' 			: jQuery("#api_key").val(),
					'id_pengeluaran' 	: id
				},
				dataType: "json",
				success:function(response){
					jQuery("#wrap-loading").hide()
					jQuery("#pend_keterangan").val(response.data.keterangan);
					jQuery("#pend_nilai").val(response.data.total);
					response.data_akun.map(function(b, i){
						var myText = b.kode_akun+" "+b.nama_akun;
						var option = new Option(myText,b.kode_akun, true, true);
						jQuery("#pend_rekening").append(option).trigger('change');
						jQuery("#pend_rekening").val(response.data.kode_akun).trigger('change');
					});
				}
			})
		})
	}

	function submitEditPengeluaran(id_pengeluaran){
		jQuery("#wrap-loading").show()
		let form = get_form_data(jQuery("#form-pengeluaran"));
		if(confirm('Apakah anda yakin untuk mengubah data ini?')){
			if(form.id_rekening == '' || form.keterangan == '' || form.nilai == '' || tahun_anggaran == '' || id_skpd == '' || id_pengeluaran == ''){
				jQuery("#wrap-loading").hide()
				alert("Ada yang kosong, Harap diisi semua")
				return false
			}else{
				jQuery.ajax({
					url:this_ajax_url,
					method: 'post',
					dataType: 'json',
					data:{
						'action'			: 'submit_edit_pengeluaran',
						'api_key'			: jQuery("#api_key").val(),
						'data'				: JSON.stringify(form),
						'tahun_anggaran'	: tahun_anggaran,
						'id_skpd'			: id_skpd,
						'id_pengeluaran'		: id_pengeluaran
					},
					beforeSend: function() {
						jQuery('.submitBtn').attr('disabled','disabled')
					},
					success: function(response){
						jQuery('#modalPengeluaran').modal('hide')
						jQuery('#wrap-loading').hide()
						if(response.status == 'success'){
							alert('Data berhasil diperbarui')
							pengeluaranTable.ajax.reload()
						}else{
							alert(`GAGAL! \n${response.message}`)
						}
						reset_form();
					}
				})
			}
		}
		jQuery('#modalPengeluaran').modal('hide');
	}

	function hapus_data_pengeluaran(id_pengeluaran){
		let confirmDelete = confirm("Apakah anda yakin akan menghapus data pendatapan?");
		if(confirmDelete){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url:this_ajax_url,
				method: 'post',
				data:{
					'action' 			: 'submit_delete_pengeluaran',
					'api_key'			: jQuery("#api_key").val(),
					'id_pengeluaran'		: id_pengeluaran
				},
				dataType: 'json',
				success:function(response){
					jQuery('#wrap-loading').hide();
					if(response.status == 'success'){
						alert('Data berhasil dihapus!.');
						pengeluaranTable.ajax.reload();	
					}else{
						alert(`GAGAL! \n${response.message}`);
					}
				}
			});
		}
	}

</script> 
