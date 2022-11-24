<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $wpdb;

$select_rpjpd = '';

$sqlTipe = $wpdb->get_results($wpdb->prepare("
				SELECT 
					* 
				FROM 
					`data_tipe_perencanaan` 
				WHERE 
					nama_tipe=%s",
					'rpjpd'
				), ARRAY_A);
$data_rpjpd = $wpdb->get_results($wpdb->prepare('
				SELECT
					id_jadwal_lokal,
					nama
				FROM
					data_jadwal_lokal
				WHERE
					status=1
					and id_tipe=%d',
					$sqlTipe[0]['id']
				),ARRAY_A);
				
if(!empty($data_rpjpd)){
	foreach($data_rpjpd as $val_rpjpd){
		$select_rpjpd .= '<option value="'.$val_rpjpd['id_jadwal_lokal'].'">'.$val_rpjpd['nama'].'</option>';
	}
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
	<h1 class="text-center" style="margin:3rem;">Jadwal Input Perencanaan RPD Lokal</h1>
		<div style="margin-bottom: 25px;">
			<button class="btn btn-primary tambah_ssh" onclick="tambah_jadwal();">Tambah Jadwal</button>
		</div>
		<table id="data_penjadwalan_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center">Nama Tahapan</th>
					<th class="text-center">Status</th>
					<th class="text-center">Jadwal Mulai</th>
					<th class="text-center">Jadwal Selesai</th>
					<th class="text-center">Tahun Mulai Anggaran</th>
					<th class="text-center">Tahun Selesai Anggaran</th>
					<th class="text-center">Jadwal RPJPD</th>
					<th class="text-center" style="width: 150px;">Aksi</th>
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
					<label for='tahun_mulai_anggaran' style='display:inline-block'>Tahun Mulai Anggaran</label>
					<input type="number" id='tahun_mulai_anggaran' name="tahun_mulai_anggaran" style='display:block;width:100%;' placeholder="Tahun Mulai Anggaran"/>
				</div>
				<div>
					<label for='lama_pelaksanaan' style='display:inline-block'>Lama Pelaksanaan</label>
					<input type="number" id='lama_pelaksanaan' name="lama_pelaksanaan" style='display:block;width:100%;' placeholder="Lama Pelaksanaan"/>
				</div>
				<div>
					<label for='jadwal_tanggal' style='display:inline-block'>Jadwal Pelaksanaan</label>
					<input type="text" id='jadwal_tanggal' name="datetimes" style='display:block;width:100%;'/>
				</div>
				<div>
					<label for="link_rpjpd" style='display:inline-block'>Pilih Jadwal RPJPD</label>
					<select id="link_rpjpd" style='display:block;width: 100%;'>
						<option value="">Pilih RPJPD</option>
						<?php echo $select_rpjpd; ?>
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

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
	jQuery(document).ready(function(){

		globalThis.tipePerencanaan = 'rpd'
		globalThis.thisAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>"

		get_data_penjadwalan();

	});

	/** get data penjadwalan */
	function get_data_penjadwalan(){
		jQuery("#wrap-loading").show();
		globalThis.penjadwalanTable = jQuery('#data_penjadwalan_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: thisAjaxUrl,
				type:"post",
				data:{
					'action' 		: "get_data_penjadwalan",
					'api_key' 		: jQuery("#api_key").val(),
					'tipe_perencanaan' : tipePerencanaan
				}
			},
			"initComplete":function( settings, json){
				jQuery("#wrap-loading").hide();
			},
			"columns": [
				{ 
					"data": "nama",
					className: "text-center"
				},
				{ 
					"data": "status",
					className: "text-center"
				},
				{ 
					"data": "waktu_awal",
					className: "text-center"
				},
				{ 
					"data": "waktu_akhir",
					className: "text-center"
				},
				{ 
					"data": "tahun_anggaran",
					className: "text-center"
				},
				{ 
					"data": "tahun_anggaran_selesai",
					className: "text-center"
				},
				{ 
					"data": "relasi_perencanaan",
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
		jQuery.ajax({
			url: thisAjaxUrl,
			type:"post",
			data:{
				'action' 			: "get_data_standar_lama_pelaksanaan",
				'api_key' 			: jQuery("#api_key").val(),
				'tipe_perencanaan' 	: tipePerencanaan
			},
			dataType: "json",
			success:function(response){
				jQuery("#lama_pelaksanaan").val(response.data.lama_pelaksanaan);
			}
		})
	}

	/** Submit tambah jadwal */
	function submitTambahJadwalForm(){
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
		let relasi_perencanaan = jQuery("#link_rpjpd").val()
		let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
		if(nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || relasi_perencanaan == '' || this_tahun_anggaran == '' || this_lama_pelaksanaan == ''){
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		}else{
			jQuery.ajax({
				url: thisAjaxUrl,
				type: 'post',
				dataType: 'json',
				data:{
					'action'			: 'submit_add_schedule',
					'api_key'			: jQuery("#api_key").val(),
					'nama'				: nama,
					'jadwal_mulai'		: jadwalMulai,
					'jadwal_selesai'	: jadwalSelesai,
					'tahun_anggaran'	: this_tahun_anggaran,
					'tipe_perencanaan'	: tipePerencanaan,
					'relasi_perencanaan': relasi_perencanaan,
					'lama_pelaksanaan'	: this_lama_pelaksanaan
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
						afterSubmitForm()
					}else{
						alert(response.message)
					}
					jQuery('#jadwal_nama').val('')
					jQuery("#tahun_mulai_anggaran").val('')
					jQuery("#link_rpjpd").val('')
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
				jQuery("#jadwal_nama").val(response.data.nama);
				jQuery("#tahun_mulai_anggaran").val(response.data.tahun_anggaran);
				jQuery("#lama_pelaksanaan").val(response.data.lama_pelaksanaan);
				jQuery('#jadwal_tanggal').data('daterangepicker').setStartDate(moment(response.data.waktu_awal).format('DD-MM-YYYY HH:mm'));
				jQuery('#jadwal_tanggal').data('daterangepicker').setEndDate(moment(response.data.waktu_akhir).format('DD-MM-YYYY HH:mm'));
				jQuery("#link_rpjpd").val(response.data.relasi_perencanaan).change();
			}
		})
	}

	function submitEditJadwalForm(id_jadwal_lokal){
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
		let relasi_perencanaan = jQuery("#link_rpjpd").val()
		let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
		if(nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || relasi_perencanaan == '' || this_tahun_anggaran == '' || this_lama_pelaksanaan == ''){
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		}else{
			jQuery.ajax({
				url: thisAjaxUrl,
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
					'tipe_perencanaan'	: tipePerencanaan,
					'relasi_perencanaan': relasi_perencanaan,
					'lama_pelaksanaan'	: this_lama_pelaksanaan
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
						afterSubmitForm()
					}else{
						alert(`GAGAL! \n${response.message}`)
					}
					jQuery('#jadwal_nama').val('')
					jQuery("#tahun_mulai_anggaran").val('')
					jQuery("#link_rpjpd").val('')
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

	function lock_data_penjadwalan(id_jadwal_lokal){
		let confirmLocked = confirm("Apakah anda yakin akan mengunci penjadwalan?");
		if(confirmLocked){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: thisAjaxUrl,
				type:'post',
				data:{
					'action' 				: 'submit_lock_schedule_rpd',
					'api_key'				: jQuery("#api_key").val(),
					'id_jadwal_lokal'		: id_jadwal_lokal
				},
				dataType: 'json',
				success:function(response){
					jQuery('#wrap-loading').hide();
					if(response.status == 'success'){
						alert('Data berhasil dikunci!.');
						penjadwalanTable.ajax.reload();
					}else{
						alert(`GAGAL! \n${response.message}`);
					}
				}
			});
		}
	}

	jQuery(function() {
		jQuery('#jadwal_tanggal').daterangepicker({
			timePicker: true,
			timePicker24Hour: true,
			startDate: moment().startOf('hour'),
			endDate: moment().startOf('hour').add(32, 'hour'),
			locale: {
				format: 'DD-MM-YYYY HH:mm'
			}
		});
	});

	function cannot_change_schedule(jenis){
		if(jenis == 'kunci'){
			alert('Tidak bisa kunci karena penjadwalan sudah dikunci');
		}else if(jenis == 'edit'){
			alert('Tidak bisa edit karena penjadwalan sudah dikunci');
		}else if(jenis == 'hapus'){
			alert('Tidak bisa hapus karena penjadwalan sudah dikunci');
		}
	}

	function afterSubmitForm(){
		jQuery("#jadwal_nama").val("")
		jQuery("#tahun_mulai_anggaran").val("")
		jQuery("#jadwal_tanggal").val("")
	}

</script> 
