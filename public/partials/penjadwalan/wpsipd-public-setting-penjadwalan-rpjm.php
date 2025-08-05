<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
$is_api_ready_esakip = $this->is_api_ready_esakip();
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
		<h1 class="text-center" style="margin:3rem;">Jadwal Input Perencanaan RPJM Lokal</h1>
		<div style="margin-bottom: 25px;">
			<button class="btn btn-primary tambah_ssh" onclick="handleAddModal();"><span class="dashicons dashicons-plus"></span>Tambah Jadwal</button>
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
					<th class="text-center" style="width: 150px;">Aksi</th>
				</tr>
			</thead>
			<tbody id="data_body">
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade mt-4" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalTambahJadwalLabel">Tambah Jadwal</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="card bg-light mb-2 shadow-lg">
					<div class="card-header">
						<strong>Jadwal Lokal</strong>
					</div>
					<div class="card-body">
						<div class="form-group">
							<label for='jadwal_nama'>Nama Tahapan</label>
							<input type='text' id='jadwal_nama' class="form-control" placeholder='Nama Tahapan'>
						</div>
						<div class="form-row">
							<div class="form-group col-md-3">
								<label for='tahun_mulai_anggaran'>Tahun Mulai Anggaran</label>
								<input type="number" id='tahun_mulai_anggaran' class="form-control" name="tahun_mulai_anggaran" placeholder="Tahun Mulai Anggaran" />
							</div>
							<div class="form-group col-md-3">
								<div class="form-group">
									<label for='lama_pelaksanaan'>Lama Pelaksanaan</label>
									<div class="input-group">
										<input type="number" id="lama_pelaksanaan" name="lama_pelaksanaan" class="form-control" aria-describedby="basic-addon2">
										<div class="input-group-append">
											<span class="input-group-text" id="basic-addon2">Tahun</span>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group col-md-6">
								<label for='jadwal_tanggal'>Jadwal Pelaksanaan</label>
								<input type="text" id='jadwal_tanggal' class="form-control" name="jadwal_tanggal" />
							</div>
						</div>
					</div>
				</div>

				<div class="card bg-light shadow-md" id="jadwal-esakip-container" style="display: none">
					<div class="card-header">
						<strong>Jadwal Integrasi WP-Eval-Sakip</strong>
					</div>
					<div class="card-body">
						<div class="form-group">
							<label for="id_jadwal_esakip">Pilih Jadwal RPJMD</label>
							<select id="id_jadwal_esakip" class="form-control">
							</select>
							<small class="text-muted">Data Jadwal diambil dari Aplikasi <strong>WP-Eval-Sakip</strong></small>
						</div>
						<div class="badge badge-info text-light d-block p-3">Pilih jadwal untuk fitur <strong>Integrasi data RPJMD (Visi RPJMD, Misi RPJMD, Tujuan, Sasaran, Program)</strong> (opsional).</div>
					</div>
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
		globalThis.tipePerencanaan = 'rpjm'
		globalThis.is_api_ready_esakip = <?php echo json_encode($is_api_ready_esakip); ?>;

		get_data_penjadwalan();

	});

	/** get data penjadwalan */
	function get_data_penjadwalan() {
		jQuery("#wrap-loading").show();
		globalThis.penjadwalanTable = jQuery('#data_penjadwalan_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: ajax.url,
				type: "post",
				data: {
					'action': "get_data_penjadwalan",
					'api_key': ajax.api_key,
					'tipe_perencanaan': tipePerencanaan
				}
			},
			"initComplete": function(settings, json) {
				jQuery("#wrap-loading").hide();
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
					"data": "tahun_anggaran_selesai",
					className: "text-center"
				},
				{
					"data": "aksi",
					className: "text-center"
				}
			]
		});
	}

	async function handleAddModal() {
		try {
			jQuery("#wrap-loading").show();
			jQuery("#modalTambahJadwal .modal-title").html("Tambah Jadwal");
			jQuery("#modalTambahJadwal .submitBtn")
				.attr("onclick", 'submitTambahJadwalForm()')
				.attr("disabled", false)
				.text("Simpan");

			const lama_pelaksanaan = await get_data_standar_lama_pelaksanaan();

			if (is_api_ready_esakip) {
				const jadwal_rpd_esakip = await get_jadwal_rpjmd_esakip();
				console.log('aktif integrasi')

				let options = `<option value="">Pilih Jadwal RPJMD</option>`;
				const statusMapping = ['DIHAPUS', 'AKTIF', 'DIKUNCI']

				if (jadwal_rpd_esakip.status) {
					jadwal_rpd_esakip.data.forEach((val) => {
						if (val.jenis_jadwal_khusus == 'rpjmd') {
							options += `<option value="${val.id}">${val.nama_jadwal} [${statusMapping[val.status]}] | ${val.tahun_anggaran} - ${val.tahun_selesai_anggaran}</option>`;
						}
					});
				}

				jQuery(`#id_jadwal_esakip`).empty().append(options);

				jQuery(`#jadwal-esakip-container`).show();
			}

			jQuery("#jadwal_nama").val('');
			jQuery("#tahun_mulai_anggaran").val('');
			jQuery("#lama_pelaksanaan").val(lama_pelaksanaan.data.lama_pelaksanaan);
			jQuery('#jadwal_tanggal').data('daterangepicker');
			jQuery('#jadwal_tanggal').data('daterangepicker');

			jQuery('#modalTambahJadwal').modal('show');
			jQuery("#wrap-loading").hide();
		} catch (error) {
			jQuery("#wrap-loading").hide();
			console.error(`terjadi kesalahan saat tambah_jadwal = ${error}`);
			alert('terjadi kesalahan saat tambah data');
		}
	}

	async function edit_data_penjadwalan(id_jadwal_lokal) {
		try {
			jQuery("#wrap-loading").show();
			jQuery("#modalTambahJadwal .modal-title").html("Edit Jadwal");
			jQuery("#modalTambahJadwal .submitBtn")
				.attr("onclick", `submitEditJadwalForm(${id_jadwal_lokal})`)
				.attr("disabled", false)
				.text("Perbarui");

			const data_jadwal = await get_data_jadwal_by_id(id_jadwal_lokal);

			if (is_api_ready_esakip) {
				const jadwal_rpd_esakip = await get_jadwal_rpjmd_esakip();
				console.log('aktif integrasi')

				let options = `<option value="">Pilih Jadwal RPJMD</option>`;
				const statusMapping = ['DIHAPUS', 'AKTIF', 'DIKUNCI']

				if (jadwal_rpd_esakip.status) {
					jadwal_rpd_esakip.data.forEach((val) => {
						if (val.jenis_jadwal_khusus == 'rpjmd') {
							options += `<option value="${val.id}">${val.nama_jadwal} [${statusMapping[val.status]}] | ${val.tahun_anggaran} - ${val.tahun_selesai_anggaran}</option>`;
						}
					});
				}


				jQuery(`#id_jadwal_esakip`).empty().append(options);
				jQuery("#id_jadwal_esakip").val(data_jadwal.data.id_jadwal_sakip);
				jQuery(`#jadwal-esakip-container`).show();
			}

			jQuery("#jadwal_nama").val(data_jadwal.data.nama);
			jQuery("#tahun_mulai_anggaran").val(data_jadwal.data.tahun_anggaran);
			jQuery("#lama_pelaksanaan").val(data_jadwal.data.lama_pelaksanaan);
			jQuery('#jadwal_tanggal').data('daterangepicker').setStartDate(moment(data_jadwal.data.waktu_awal).format('DD-MM-YYYY HH:mm'));
			jQuery('#jadwal_tanggal').data('daterangepicker').setEndDate(moment(data_jadwal.data.waktu_akhir).format('DD-MM-YYYY HH:mm'));

			jQuery('#modalTambahJadwal').modal('show');
			jQuery("#wrap-loading").hide();
		} catch (error) {
			jQuery("#wrap-loading").hide();
			console.error(`terjadi kesalahan saat edit jadwal = ${error}`);
			alert('terjadi kesalahan saat tambah data');
		}
	}

	/** Submit tambah jadwal */
	function submitTambahJadwalForm() {
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
		let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
		let id_jadwal_esakip = jQuery("#id_jadwal_esakip").val()
		if (nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == '' || this_lama_pelaksanaan == '') {
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		} else {
			jQuery.ajax({
				url: ajax.url,
				type: 'post',
				dataType: 'json',
				data: {
					'action': 'submit_add_schedule',
					'api_key': ajax.api_key,
					'nama': nama,
					'jadwal_mulai': jadwalMulai,
					'jadwal_selesai': jadwalSelesai,
					'tahun_anggaran': this_tahun_anggaran,
					'tipe_perencanaan': tipePerencanaan,
					'lama_pelaksanaan': this_lama_pelaksanaan,
					'id_jadwal_sakip': id_jadwal_esakip
				},
				beforeSend: function() {
					jQuery('.submitBtn').attr('disabled', 'disabled')
				},
				success: function(response) {
					jQuery('#modalTambahJadwal').modal('hide')
					jQuery('#wrap-loading').hide()
					if (response.status == 'success') {
						alert('Data berhasil ditambahkan')
						penjadwalanTable.ajax.reload()
						afterSubmitForm()
					} else {
						alert(response.message)
					}
				}
			})
		}
		jQuery('#modalTambahJadwal').modal('hide');
	}

	function submitEditJadwalForm(id_jadwal_lokal) {
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
		let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
		let id_jadwal_esakip = jQuery("#id_jadwal_esakip").val()
		if (nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == '' || this_lama_pelaksanaan == '') {
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		} else {
			jQuery.ajax({
				url: ajax.url,
				type: 'post',
				dataType: 'json',
				data: {
					'action': 'submit_edit_schedule',
					'api_key': ajax.api_key,
					'nama': nama,
					'jadwal_mulai': jadwalMulai,
					'jadwal_selesai': jadwalSelesai,
					'id_jadwal_lokal': id_jadwal_lokal,
					'tahun_anggaran': this_tahun_anggaran,
					'tipe_perencanaan': tipePerencanaan,
					'lama_pelaksanaan': this_lama_pelaksanaan,
					'id_jadwal_sakip': id_jadwal_esakip
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
						afterSubmitForm()
					} else {
						alert(`GAGAL! \n${response.message}`)
					}
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
				url: ajax.url,
				type: 'post',
				data: {
					'action': 'submit_delete_schedule',
					'api_key': ajax.api_key,
					'id_jadwal_lokal': id_jadwal_lokal
				},
				dataType: 'json',
				success: function(response) {
					jQuery('#wrap-loading').hide();
					if (response.status == 'success') {
						alert('Data berhasil dihapus!.');
						penjadwalanTable.ajax.reload();
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
				url: ajax.url,
				type: 'post',
				data: {
					'action': 'submit_lock_schedule_rpjm',
					'api_key': ajax.api_key,
					'id_jadwal_lokal': id_jadwal_lokal
				},
				dataType: 'json',
				success: function(response) {
					jQuery('#wrap-loading').hide();
					if (response.status == 'success') {
						alert('Data berhasil dikunci!.');
						penjadwalanTable.ajax.reload();
					} else {
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

	function cannot_change_schedule(jenis) {
		if (jenis == 'kunci') {
			alert('Tidak bisa kunci karena penjadwalan sudah dikunci');
		} else if (jenis == 'edit') {
			alert('Tidak bisa edit karena penjadwalan sudah dikunci');
		} else if (jenis == 'hapus') {
			alert('Tidak bisa hapus karena penjadwalan sudah dikunci');
		}
	}

	function afterSubmitForm() {
		jQuery("#jadwal_nama").val("")
		jQuery("#tahun_mulai_anggaran").val("")
		jQuery("#jadwal_tanggal").val("")
	}

	function get_jadwal_rpjmd_esakip() {
		return jQuery.ajax({
			url: ajax.url,
			type: 'POST',
			dataType: 'JSON',
			data: {
				action: 'get_jadwal_by_type_esakip',
				api_key: ajax.api_key,
				type: 'RPJMD'
			}
		});
	}

	function get_data_standar_lama_pelaksanaan() {
		return jQuery.ajax({
			url: ajax.url,
			type: "post",
			dataType: 'JSON',
			data: {
				action: "get_data_standar_lama_pelaksanaan",
				api_key: ajax.api_key,
				tipe_perencanaan: tipePerencanaan
			}
		});
	}

	function get_data_jadwal_by_id(id_jadwal_lokal) {
		return jQuery.ajax({
			url: ajax.url,
			type: "post",
			dataType: 'JSON',
			data: {
				action: "get_data_jadwal_by_id",
				api_key: ajax.api_key,
				id_jadwal_lokal: id_jadwal_lokal
			}
		});
	}
</script>