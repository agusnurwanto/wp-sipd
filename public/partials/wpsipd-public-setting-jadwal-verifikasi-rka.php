<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

$input = shortcode_atts(array(
	'tahun_anggaran' => '2022'
), $atts);

if (isset($tipe_jadwal)) {
	if ($tipe_jadwal == 'sipd') {
		$judul = 'SIPD';
		$tipe_perencanaan = 'verifikasi_rka_sipd';
	} else {
		$tipe_perencanaan = 'verifikasi_rka';
		$judul = '';
	}
} else {
	$tipe_perencanaan = 'verifikasi_rka';
	$judul = '';
}

$is_admin = false;
$user_id = um_user('ID');
$user_meta = get_userdata($user_id);

if (
	in_array("administrator", $user_meta->roles)
) {
	$is_admin = true;
}

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
		<input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
		<h1 class="text-center" style="margin:3rem;">Halaman Setting Penjadwalan Verifikasi RKA <?php echo $judul; ?> <?php echo $input['tahun_anggaran']; ?></h1>
		<?php if ($is_admin) : ?>
			<div style="margin-bottom: 25px;">
				<button class="btn btn-primary tambah_jadwal tambah_ssh" onclick="tambah_jadwal();" hidden>Tambah Jadwal</button>
			</div>
		<?php endif; ?>
		<table id="data_penjadwalan_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center">Nama Tahapan</th>
					<th class="text-center">Status</th>
					<th class="text-center">Jadwal Mulai</th>
					<th class="text-center">Jadwal Selesai</th>
					<th class="text-center">Tahun Anggaran</th>
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
					<input type="text" id='jadwal_tanggal' name="datetimes" style='display:block;width:100%;' />
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
	jQuery(document).ready(function() {
		window.tahun_anggaran = <?php echo $input['tahun_anggaran']; ?>;
		window.tipe_perencanaan = '<?php echo $tipe_perencanaan; ?>';
		window.thisAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>"

		get_data_penjadwalan();

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

	/** get data penjadwalan */
	function get_data_penjadwalan() {
		jQuery("#wrap-loading").show();
		window.penjadwalanTable = jQuery('#data_penjadwalan_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: thisAjaxUrl,
				type: "post",
				data: {
					'action': "get_data_penjadwalan",
					'api_key': jQuery("#api_key").val(),
					'tipe_perencanaan': tipe_perencanaan,
					'tahun_anggaran': tahun_anggaran
				}
			},
			"initComplete": function(settings, json) {
				jQuery("#wrap-loading").hide();
				if (json.checkOpenedSchedule != 'undefined' && json.checkOpenedSchedule > 0) {
					jQuery(".tambah_jadwal").prop('hidden', true);
				} else {
					jQuery(".tambah_jadwal").prop('hidden', false);
				}
			},
			"columns": [{
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
					"data": "aksi",
					className: "text-center"
				}
			]
		});
	}

	<?php if ($is_admin) : ?>
		/** show modal tambah jadwal */
		function tambah_jadwal() {
			jQuery("#modalTambahJadwal .modal-title").html("Tambah Jadwal");
			jQuery("#modalTambahJadwal .submitBtn")
				.attr("onclick", 'submitTambahJadwalForm()')
				.attr("disabled", false)
				.text("Simpan");
			jQuery('#modalTambahJadwal').modal('show');
		}

		/** Submit tambah jadwal */
		function submitTambahJadwalForm() {
			jQuery("#wrap-loading").show()
			let nama = jQuery('#jadwal_nama').val()
			let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
			let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
			let this_tahun_anggaran = tahun_anggaran
			if (nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == '') {
				jQuery("#wrap-loading").hide()
				alert("Ada yang kosong, Harap diisi semua")
				return false
			}else{
				jQuery.ajax({
					url: thisAjaxUrl,
					type: 'post',
					dataType: 'json',
					data: {
						'action': 'submit_add_schedule',
						'api_key': jQuery("#api_key").val(),
						'nama': nama,
						'jadwal_mulai': jadwalMulai,
						'jadwal_selesai': jadwalSelesai,
						'tahun_anggaran': this_tahun_anggaran,
						'tipe_perencanaan': tipe_perencanaan,
                        'lama_pelaksanaan':1
					},
					beforeSend: function() {
						jQuery('.submitBtn').attr('disabled', 'disabled')
					},
					success: function(response) {
						jQuery('#wrap-loading').hide()
						alert(response.message);
						if (response.status == 'success') {
							jQuery('#modalTambahJadwal').modal('hide');
							jQuery('#jadwal_nama').val('');
							penjadwalanTable.ajax.reload();
							jQuery(".tambah_jadwal").prop('hidden', true);
						}
					}

				})
			}
			jQuery('#modalTambahJadwal').modal('hide');
		}

        /** edit data penjadwalan usulan */
		function edit_data_penjadwalan(id_jadwal_lokal) {
			jQuery('#modalTambahJadwal').modal('show');
			jQuery("#modalTambahJadwal .modal-title").html("Edit Jadwal");
			jQuery("#modalTambahJadwal .submitBtn")
				.attr("onclick", 'submitEditJadwalForm(' + id_jadwal_lokal + ')')
				.attr("disabled", false)
				.text("Simpan");
			jQuery("#wrap-loading").show()
			jQuery.ajax({
				url: thisAjaxUrl,
				type: "post",
				data: {
					'action': "get_data_jadwal_by_id",
					'api_key': jQuery("#api_key").val(),
					'id_jadwal_lokal': id_jadwal_lokal
				},
				dataType: "json",
				success: function(response) {
					jQuery("#wrap-loading").hide()
					jQuery("#jadwal_nama").val(response.data.nama);
					jQuery('#jadwal_tanggal').data('daterangepicker').setStartDate(moment(response.data.waktu_awal).format('DD-MM-YYYY HH:mm'));
					jQuery('#jadwal_tanggal').data('daterangepicker').setEndDate(moment(response.data.waktu_akhir).format('DD-MM-YYYY HH:mm'));
				}
			})
		}

		function submitEditJadwalForm(id_jadwal_lokal) {
			jQuery("#wrap-loading").show()
			let nama = jQuery('#jadwal_nama').val()
			let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
			let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
			let this_tahun_anggaran = tahun_anggaran
			if (nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == '') {
				jQuery("#wrap-loading").hide()
				alert("Ada yang kosong, Harap diisi semua")
				return false
			}else{
				jQuery.ajax({
					url: thisAjaxUrl,
					type: 'post',
					dataType: 'json',
					data: {
						'action': 'submit_edit_schedule',
						'api_key': jQuery("#api_key").val(),
						'nama': nama,
						'jadwal_mulai': jadwalMulai,
						'jadwal_selesai': jadwalSelesai,
						'id_jadwal_lokal': id_jadwal_lokal,
						'tahun_anggaran': this_tahun_anggaran,
						'tipe_perencanaan': tipe_perencanaan,
                        'lama_pelaksanaan':1
					},
					beforeSend: function() {
						jQuery('.submitBtn').attr('disabled', 'disabled')
					},
					success: function(response) {
						jQuery('#modalTambahJadwal').modal('hide')
						jQuery('#wrap-loading').hide()
						if (response.status == 'success') {
							alert('Data berhasil diperbarui')
							penjadwalanTable.ajax.reload()
						} else {
							alert(`GAGAL! \n${response.message}`)
						}
						jQuery('#jadwal_nama').val('')
					}
				})
			}
			jQuery('#modalTambahJadwal').modal('hide');
		}

		function hapus_data_penjadwalan(id_jadwal_lokal) {
			let confirmDelete = confirm("Apakah anda yakin akan menghapus penjadwalan?");
			if (confirmDelete) {
				jQuery('#wrap-loading').show();
				jQuery.ajax({
					url: thisAjaxUrl,
					type: 'post',
					data: {
						'action': 'submit_delete_schedule',
						'api_key': jQuery("#api_key").val(),
						'id_jadwal_lokal': id_jadwal_lokal
					},
					dataType: 'json',
					success: function(response) {
						jQuery('#wrap-loading').hide();
						if (response.status == 'success') {
							alert('Data berhasil dihapus!.');
							penjadwalanTable.ajax.reload();
							jQuery(".tambah_jadwal").prop('hidden', false);
						} else {
							alert(`GAGAL! \n${response.message}`);
						}
					}
				});
			}
		}

		function lock_data_penjadwalan(id_jadwal_lokal) {
			let confirmLocked = confirm("Apakah anda yakin akan mengunci penjadwalan?");
			if (confirmLocked) {
				jQuery('#wrap-loading').show();
				jQuery.ajax({
					url: thisAjaxUrl,
					type: 'post',
					data: {
						'action': 'submit_lock_schedule_verif_rka',
						'api_key': jQuery("#api_key").val(),
						'id_jadwal_lokal': id_jadwal_lokal
					},
					dataType: 'json',
					success: function(response) {
						jQuery('#wrap-loading').hide();
						if (response.status == 'success') {
							alert('Data berhasil dikunci!.');
							penjadwalanTable.ajax.reload();
							jQuery(".tambah_jadwal").prop('hidden', false);
						} else {
							alert(`GAGAL! \n${response.message}`);
						}
					}
				});
			}
		}
	<?php endif; ?>

</script>
