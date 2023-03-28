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
		<h1 class="text-center" style="margin:3rem;">Halaman Pendapatan  <?php echo $input['tahun_anggaran']; ?></h1>
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

<div class="modal fade mt-4" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalTambahJadwalLabel">Tambah Penjadwalan</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div>
					<label for='jadwal_nama' style='display:inline-block'>Nama Tahapan</label>
					<input type='text' id='jadwal_nama' style='display:block;width:100%;' placeholder='Nama Tahapan'>
				</div>
				<div>
					<label for='jadwal_tanggal' style='display:inline-block'>Jadwal Pelaksanaan</label>
					<input type="text" id='jadwal_tanggal' name="datetimes" style='display:block;width:100%;'/>
				</div>
				<div>
					<label for="link_renstra" style='display:inline-block'>Pilih Jadwal RENSTRA</label>
					<select id="link_renstra" style='display:block;width: 100%;'>
						<option value="">Pilih RENSTRA</option>
						<?php echo $select_renstra; ?>
					</select>
				</div>
			</div> 
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="submitTambahJadwalForm()">Simpan</button>
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

		jQuery('#modalTambahJadwal').on('hidden.bs.modal', function () {
			jQuery("#jadwal_nama").val("");
			jQuery("#link_renstra").val("");
		})
	});

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

	/** show modal tambah jadwal */
	function tambah_jadwal(){
		jQuery("#modalTambahJadwal .modal-title").html("Tambah Penjadwalan");
		jQuery("#modalTambahJadwal .submitBtn")
			.attr("onclick", 'submitTambahJadwalForm()')
			.attr("disabled", false)
			.text("Simpan");
		jQuery('#modalTambahJadwal').modal('show');
	}

	/** Submit tambah jadwal */
	function submitTambahJadwalForm(){
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = tahun_anggaran
		let relasi_perencanaan = jQuery("#link_renstra").val()
		if(nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == ''){
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		}else{
			jQuery.ajax({
				url:thisAjaxUrl,
				type: 'post',
				dataType: 'json',
				data:{
					'action'			: 'submit_add_schedule',
					'api_key'			: jQuery("#api_key").val(),
					'nama'				: nama,
					'jadwal_mulai'		: jadwalMulai,
					'jadwal_selesai'	: jadwalSelesai,
					'tahun_anggaran'	: this_tahun_anggaran,
					'tipe_perencanaan'	: tipe_perencanaan,
					'relasi_perencanaan': relasi_perencanaan,
				},
				beforeSend: function() {
					jQuery('.submitBtn').attr('disabled','disabled')
				},
				success: function(response){
					jQuery('#modalTambahJadwal').modal('hide')
					jQuery('#wrap-loading').hide()
					if(response.status == 'success'){
						alert('Data berhasil ditambahkan')
						penjadwalanTable.ajax.reload()
					}else{
						alert(response.message)
					}
					jQuery('#jadwal_nama').val('')
					jQuery("#link_renstra").val('')
				}
			})
		}
		jQuery('#modalTambahJadwal').modal('hide');
	}

	/** edit akun ssh usulan */
	function edit_data_penjadwalan(id_jadwal_lokal){
		jQuery('#modalTambahJadwal').modal('show');
		jQuery("#modalTambahJadwal .modal-title").html("Edit Penjadwalan");
		jQuery("#modalTambahJadwal .submitBtn")
			.attr("onclick", 'submitEditJadwalForm('+id_jadwal_lokal+')')
			.attr("disabled", false)
			.text("Simpan");
		jQuery("#wrap-loading").show()
		jQuery.ajax({
			url: thisAjaxUrl,
			type:"post",
			data:{
				'action' 			: "get_data_jadwal_by_id",
				'api_key' 			: jQuery("#api_key").val(),
				'id_jadwal_lokal' 	: id_jadwal_lokal
			},
			dataType: "json",
			success:function(response){
				jQuery("#wrap-loading").hide()
				jQuery("#jadwal_nama").val(response.data.nama);
				jQuery('#jadwal_tanggal').data('daterangepicker').setStartDate(moment(response.data.waktu_awal).format('DD-MM-YYYY HH:mm'));
				jQuery('#jadwal_tanggal').data('daterangepicker').setEndDate(moment(response.data.waktu_akhir).format('DD-MM-YYYY HH:mm'));
				jQuery("#link_renstra").val(response.data.relasi_perencanaan).change();
			}
		})
	}

	function submitEditJadwalForm(id_jadwal_lokal){
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = tahun_anggaran
		let relasi_perencanaan = jQuery("#link_renstra").val()
		if(nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == ''){
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		}else{
			jQuery.ajax({
				url:thisAjaxUrl,
				type: 'post',
				dataType: 'json',
				data:{
					'action'			: 'submit_edit_schedule',
					'api_key'			: jQuery("#api_key").val(),
					'nama'				: nama,
					'jadwal_mulai'		: jadwalMulai,
					'jadwal_selesai'	: jadwalSelesai,
					'id_jadwal_lokal'	: id_jadwal_lokal,
					'tahun_anggaran'	: this_tahun_anggaran,
					'tipe_perencanaan'	: tipe_perencanaan,
					'relasi_perencanaan': relasi_perencanaan
				},
				beforeSend: function() {
					jQuery('.submitBtn').attr('disabled','disabled')
				},
				success: function(response){
					jQuery('#modalTambahJadwal').modal('hide')
					jQuery('#wrap-loading').hide()
					if(response.status == 'success'){
						alert('Data berhasil diperbarui')
						penjadwalanTable.ajax.reload()
					}else{
						alert(`GAGAL! \n${response.message}`)
					}
					jQuery('#jadwal_nama').val('')
					jQuery("#link_renstra").val('')
				}
			})
		}
		jQuery('#modalTambahJadwal').modal('hide');
	}

	function hapus_data_penjadwalan(id_jadwal_lokal){
		let confirmDelete = confirm("Apakah anda yakin akan menghapus penjadwalan?");
		if(confirmDelete){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: thisAjaxUrl,
				type:'post',
				data:{
					'action' 				: 'submit_delete_schedule',
					'api_key'				: jQuery("#api_key").val(),
					'id_jadwal_lokal'		: id_jadwal_lokal
				},
				dataType: 'json',
				success:function(response){
					jQuery('#wrap-loading').hide();
					if(response.status == 'success'){
						alert('Data berhasil dihapus!.');
						penjadwalanTable.ajax.reload();	
					}else{
						alert(`GAGAL! \n${response.message}`);
					}
				}
			});
		}
	}

	function list_perangkat_daerah(){
		jQuery('#wrap-loading').show();
		return new Promise(function(resolve, reject){
			if(typeof list_perangkat_daerah_global == 'undefined'){
				jQuery.ajax({
					url:ajax.url,
					type:'post',
					dataType:'json',
					data:{
						action:'list_perangkat_daerah',
						tahun_anggaran:tahun_anggaran,
						api_key:jQuery("#api_key").val(),
					},
					success:function(response){
						jQuery('#wrap-loading').hide();
						if(response.status){
							list_perangkat_daerah_global = response.list_skpd_options;
							jQuery("#list_perangkat_daerah").html(list_perangkat_daerah_global);
							jQuery('#list_perangkat_daerah').select2({width: '100%'});
							return resolve();
						}

						alert('Oops ada kesalahan load data Unit kerja');
						return resolve();
					}
				})
			}else{
				jQuery("#list_perangkat_daerah").html(list_perangkat_daerah_global);
				jQuery('#list_perangkat_daerah').select2({width: '100%'});
				return resolve();
			}
		})
	}

</script> 
