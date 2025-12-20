<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
global $wpdb;

$select_rpd_rpjm = '';

$sqlTipe = $wpdb->get_results(
	$wpdb->prepare("
		SELECT 
			* 
		FROM `data_tipe_perencanaan` 
		WHERE nama_tipe = %s
		   OR nama_tipe = %s
		", 'rpd', 'rpjm'),
	ARRAY_A
);

$namaTipeArray = array_column($sqlTipe, 'id');
$type = implode(', ', $namaTipeArray);

$data_rpd_rpjm = $wpdb->get_results(
	"
		SELECT
			id_jadwal_lokal,
			nama,
			id_tipe,
			status,
			tahun_anggaran,
			tahun_akhir_anggaran
		FROM data_jadwal_lokal
		WHERE id_tipe IN ({$type})
	",
	ARRAY_A
);

if (!empty($data_rpd_rpjm)) {
	foreach ($data_rpd_rpjm as $val_rpd_rpjm) {
		$tipe = [];
		foreach ($sqlTipe as $val_tipe) {
			$tipe[$val_tipe['id']] = strtoupper($val_tipe['nama_tipe']);
		}

		$status = '[DIKUNCI]';
		if ($val_rpd_rpjm['status'] == 0) {
			$status = '[TERBUKA]';
		}

		// Cek apakah id_tipe == MONEV RPJMD
		if ($val_rpd_rpjm['id_tipe'] == 17) {
			$tahun = $val_rpd_rpjm['tahun_anggaran'] . ' - ' . $val_rpd_rpjm['tahun_akhir_anggaran'];
		} else {
			$tahun = $val_rpd_rpjm['tahun_anggaran'];
		}

		$select_rpd_rpjm .= '<option value="' . $val_rpd_rpjm['id_jadwal_lokal'] . '">' . $tipe[$val_rpd_rpjm['id_tipe']] . ' | ' . $val_rpd_rpjm['nama'] . ' (' . $tahun . ') ' . $status . '</option>';
	}
}


$title = 'Rekap Total Program, Kegiatan, Sub Kegiatan RENSTRA';
$shortcode = '[rekap_total_prog_keg_renstra]';
$rekap_total_prog_keg_renstra = $this->generatePage($title, false, $shortcode, false);
$is_api_ready_esakip = $this->is_api_ready_esakip();

$body = '';
?>
<style>
	.bulk-action {
		padding: .45rem;
		border-color: #eaeaea;
		vertical-align: middle;
	}
</style>
<div class="cetak">
	<div style="padding: 10px;margin:0 0 3rem 0;">
		<input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
		<h1 class="text-center" style="margin:3rem;">Jadwal Input Perencanaan RENSTRA Lokal</h1>
		<div style="margin-bottom: 25px;">
			<button class="btn btn-primary" onclick="handleAddModal();"><span class="dashicons dashicons-plus"></span>Tambah Jadwal</button>
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
					<th class="text-center">Jenis Jadwal</th>
					<th class="text-center">Jadwal RPD/RPJM</th>
					<th class="text-center" style="width: 100px;">Aksi</th>
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
				<div class="card bg-light shadow-lg mb-2">
					<div class="card-header">
						<strong>Jadwal Lokal</strong>
					</div>
					<div class="card-body">
						<div class="form-group">
							<label for='jadwal_nama'>Nama Tahapan</label>
							<input type='text' id='jadwal_nama' class="form-control" placeholder='Nama Tahapan'>
						</div>
						<div class="form-group">
							<label for="link_rpd_rpjm">Pilih Jadwal RPD atau RPJM</label>
							<select id="link_rpd_rpjm" class="form-control">
								<option value="">Pilih RPD atau RPJM</option>
								<?php echo $select_rpd_rpjm; ?>
							</select>
						</div>
						<div class="form-group">
							<label for="jenis_jadwal">Pilih Jenis Jadwal</label>
							<select id="jenis_jadwal" class="form-control">
								<option value="usulan" selected>Usulan</option>
								<option value="penetapan">Penetapan</option>
								<option value="transformasi_cascading">Transformasi Cascading</option>
							</select>
							<small class="text-text-muted">Jenis jadwal <strong>Transformasi Cascading</strong> digunakan untuk mengaktifkan jadwal di Input Transformasi Cascading saja.</small>
						</div>
						<div class="form-row">
							<div class="form-group col-md-3">
								<label for='tahun_mulai_anggaran'>Tahun Mulai Anggaran</label>
								<input type="number" class="form-control" id='tahun_mulai_anggaran' name="tahun_mulai_anggaran" placeholder="Tahun Mulai Anggaran" />
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
								<input type="text" id='jadwal_tanggal' name="jadwal_tanggal" class="form-control" />
							</div>
						</div>
					</div>
				</div>

				<div class="card bg-light shadow-md mb-2" id="card_jadwal_integrasi">
					<div class="card-header">
						<strong>Jadwal Integrasi WP-Eval-Sakip</strong>
					</div>
					<div class="card-body">
						<div class="form-group mb-2">
							<label for="id_jadwal_esakip">Pilih Jadwal RPJMD/RPD</label>
							<select id="id_jadwal_esakip" class="form-control">
							</select>
							<small class="text-muted">Data Jadwal diambil dari aplikasi <strong>WP-Eval-Sakip</strong></small>
						</div>
						<small class="text-justify">Pilih jadwal untuk fitur <strong>Integrasi data Pohon Kinerja</strong> di fitur Input Renstra Lokal (Opsional).</small>
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

<div class="report"></div>
<script>
	jQuery(document).ready(function() {
		globalThis.tipePerencanaan = 'renstra'
		globalThis.tahunAnggaran = "<?php echo get_option('_crb_tahun_anggaran_sipd'); ?>"
		globalThis.is_api_ready_esakip = <?php echo json_encode($is_api_ready_esakip); ?>;

		get_data_penjadwalan();

		if (is_api_ready_esakip) {
    		jQuery("#link_rpd_rpjm").on("change", function() {
				const id_jadwal_lokal = jQuery(this).val();
				handleJadwalSakipChange(id_jadwal_lokal);
    		});
		}
	});

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
					'api_key': jQuery("#api_key").val(),
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
					"data": "jenis_jadwal",
					className: "text-center"
				},
				{
					"data": "relasi_perencanaan_renstra",
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
			jQuery("#modalTambahJadwal .modal-title").html("Tambah Penjadwalan");
			jQuery("#modalTambahJadwal .submitBtn")
				.attr("onclick", 'submitTambahJadwalForm()')
				.attr("disabled", false)
				.text("Simpan");

			const lama_pelaksanaan = await get_data_standar_lama_pelaksanaan();

			if (is_api_ready_esakip) {
				const jadwal_rpd_esakip = await get_jadwal_rpd_esakip();
				
				let options = `<option value="">Pilih Jadwal</option>`;
				if (jadwal_rpd_esakip.status) {
					const statusMapping = ['DIHAPUS', 'AKTIF', 'DIKUNCI'];
					jadwal_rpd_esakip.data.forEach((val) => {
						options += `<option value="${val.id}">${val.nama_jadwal} [${statusMapping[val.status]}] | ${val.tahun_anggaran} - ${val.tahun_selesai_anggaran}</option>`;
					});

					jQuery(`#id_jadwal_esakip`).empty().append(options).attr('disabled', false);
				} else {
					options = `<option value="">Tidak tersedia</option>`;
					jQuery(`#id_jadwal_esakip`).empty().append(options).attr('disabled', true);
					alert(jadwal_rpd_esakip.message);
				}

				jQuery('#card_jadwal_integrasi').show();
			}

			jQuery("#jadwal_nama").val("");
			jQuery("#link_rpd_rpjm").val('');
			jQuery("#tahun_mulai_anggaran").val("");
			jQuery("#jenis_jadwal").val("usulan");
			jQuery("#lama_pelaksanaan").val(lama_pelaksanaan.data.lama_pelaksanaan);
			jQuery('#jadwal_tanggal').data('daterangepicker');

			jQuery("#wrap-loading").hide();
			jQuery('#modalTambahJadwal').modal('show');
		} catch (error) {
			jQuery("#wrap-loading").hide();
			console.error(`terjadi kesalahan saat handleAddModal = ${error}`);
			alert('terjadi kesalahan saat tambah data');
		}
	}

	async function handleJadwalSakipChange(id_jadwal_lokal) {
		if (!id_jadwal_lokal || !is_api_ready_esakip) {
			return;
		}

		jQuery("#wrap-loading").show(); 
		const data_jadwal = await get_data_jadwal_by_id(id_jadwal_lokal);

		if (data_jadwal.status && data_jadwal.data.id_jadwal_sakip) {
			jQuery("#id_jadwal_esakip").val(data_jadwal.data.id_jadwal_sakip).attr('disabled', true);
		} else {
			jQuery("#id_jadwal_esakip").attr('disabled', false);
		}
		jQuery("#wrap-loading").hide();
	}

	/** Submit tambah jadwal */
	function submitTambahJadwalForm() {
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
		let relasi_perencanaan = jQuery("#link_rpd_rpjm").val()
		let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
		let jenis_jadwal = jQuery("#jenis_jadwal").val()
		let id_jadwal_sakip = jQuery("#id_jadwal_esakip").val()
		if (nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == '' || this_lama_pelaksanaan == '' || jenis_jadwal == '') {
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
					'api_key': jQuery("#api_key").val(),
					'nama': nama,
					'jadwal_mulai': jadwalMulai,
					'jadwal_selesai': jadwalSelesai,
					'tahun_anggaran': this_tahun_anggaran,
					'tipe_perencanaan': tipePerencanaan,
					'relasi_perencanaan': relasi_perencanaan,
					'lama_pelaksanaan': this_lama_pelaksanaan,
					'jenis_jadwal': jenis_jadwal,
					'id_jadwal_sakip': id_jadwal_sakip
				},
				beforeSend: function() {
					jQuery('.submitBtn').attr('disabled', 'disabled')
				},
				success: function(response) {
					jQuery('#wrap-loading').hide()
					if (response.status == 'success') {
						jQuery('#modalTambahJadwal').modal('hide')
						alert(response.message)
						penjadwalanTable.ajax.reload()
						afterSubmitForm()
					} else {
						jQuery('.submitBtn').attr('disabled', false)
						alert(response.message)
					}
				}
			})
		}
	}

	async function edit_data_penjadwalan(id_jadwal_lokal) {
		try {
			jQuery("#wrap-loading").show();
			jQuery("#modalTambahJadwal .modal-title").html("Edit Penjadwalan");
			jQuery("#modalTambahJadwal .submitBtn")
				.attr("onclick", `submitEditJadwalForm(${id_jadwal_lokal})`)
				.attr("disabled", false)
				.text("Perbarui");

			let data_jadwal = await get_data_jadwal_by_id(id_jadwal_lokal);

			if (is_api_ready_esakip) {
				const jadwal_rpd_esakip = await get_jadwal_rpd_esakip();
				
				let options = `<option value="">Pilih Jadwal</option>`;
				if (jadwal_rpd_esakip.status) {
					const statusMapping = ['DIHAPUS', 'AKTIF', 'DIKUNCI'];
					jadwal_rpd_esakip.data.forEach((val) => {
						options += `<option value="${val.id}">${val.nama_jadwal} [${statusMapping[val.status]}] | ${val.tahun_anggaran} - ${val.tahun_selesai_anggaran}</option>`;
					});

					jQuery(`#id_jadwal_esakip`).empty().append(options).attr('disabled', false);
					jQuery(`#id_jadwal_esakip`).val(data_jadwal.data.id_jadwal_sakip);
				} else {
					options = `<option value="">Tidak tersedia</option>`;
					jQuery(`#id_jadwal_esakip`).empty().append(options).attr('disabled', true);
					alert(jadwal_rpd_esakip.message);
				}

				jQuery('#card_jadwal_integrasi').show();
			}

			if (data_jadwal.data.relasi_perencanaan) {
				jQuery("#link_rpd_rpjm").val(data_jadwal.data.relasi_perencanaan.id_jadwal_lokal);
			} else {
				jQuery("#link_rpd_rpjm").val('');
			}
			jQuery("#jadwal_nama").val(data_jadwal.data.nama);
			jQuery("#tahun_mulai_anggaran").val(data_jadwal.data.tahun_anggaran);
			jQuery("#lama_pelaksanaan").val(data_jadwal.data.lama_pelaksanaan);
			jQuery('#jadwal_tanggal').data('daterangepicker').setStartDate(moment(data_jadwal.data.waktu_awal).format('DD-MM-YYYY HH:mm'));
			jQuery('#jadwal_tanggal').data('daterangepicker').setEndDate(moment(data_jadwal.data.waktu_akhir).format('DD-MM-YYYY HH:mm'));
			jQuery("#jenis_jadwal").val(data_jadwal.data.jenis_jadwal);

			jQuery("#wrap-loading").hide();
			jQuery('#modalTambahJadwal').modal('show');
		} catch (error) {
			jQuery("#wrap-loading").hide();
			console.error(`terjadi kesalahan saat edit_data_penjadwalan = ${error}`);
			alert('terjadi kesalahan saat edit data');
		}
	}


	function submitEditJadwalForm(id_jadwal_lokal) {
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
		let relasi_perencanaan = jQuery("#link_rpd_rpjm").val()
		let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
		let jenis_jadwal = jQuery("#jenis_jadwal").val()
		let id_jadwal_sakip = jQuery("#id_jadwal_esakip").val()
		if (nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == '' || this_lama_pelaksanaan == '' || jenis_jadwal == '') {
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
					'api_key': jQuery("#api_key").val(),
					'nama': nama,
					'jadwal_mulai': jadwalMulai,
					'jadwal_selesai': jadwalSelesai,
					'id_jadwal_lokal': id_jadwal_lokal,
					'tahun_anggaran': this_tahun_anggaran,
					'tipe_perencanaan': tipePerencanaan,
					'relasi_perencanaan': relasi_perencanaan,
					'lama_pelaksanaan': this_lama_pelaksanaan,
					'jenis_jadwal': jenis_jadwal,
					'id_jadwal_sakip': id_jadwal_sakip
				},
				beforeSend: function() {
					jQuery('.submitBtn').attr('disabled', 'disabled')
				},
				success: function(response) {
					jQuery('#wrap-loading').hide()
					if (response.status == 'success') {
						jQuery('#modalTambahJadwal').modal('hide')
						alert(response.message)
						penjadwalanTable.ajax.reload()
						afterSubmitForm()
					} else {
						jQuery('.submitBtn').attr('disabled', false)
						alert(`GAGAL! \n${response.message}`)
					}
				}
			})
		}
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
					'api_key': jQuery("#api_key").val(),
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

	function lock_data_penjadwalan(id_jadwal_lokal, tahun_anggaran) {
		Swal.fire({
			title: 'Kunci Penjadwalan Renstra?',
			text: "Apakah anda yakin akan mengunci penjadwalan?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Kunci Jadwal!',
			cancelButtonText: 'Batal',
		}).then((result) => {
			if (result.isConfirmed) {
				jQuery('#wrap-loading').show();
				jQuery.ajax({
					url: ajax.url,
					type: 'post',
					data: {
						'action': 'submit_lock_schedule_renstra',
						'api_key': jQuery("#api_key").val(),
						'id_jadwal_lokal': id_jadwal_lokal,
						'tahun_anggaran': tahun_anggaran
					},
					dataType: 'json',
					success: function(response) {
						jQuery('#wrap-loading').hide();
						if (response.status == 'success') {
							Swal.fire({
								title: 'Success!',
								html: 'Data berhasil dikunci!.',
								confirmButtonText: 'Tutup',
								icon: 'success'
							})
							penjadwalanTable.ajax.reload();
						} else {
							Swal.fire({
								title: 'Oops!',
								html: response.message,
								confirmButtonText: 'Tutup',
								icon: 'error',
								width: '950px'
							})
						}
					}
				});
			}
		})
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
		jQuery("#link_rpd_rpjm").val("")
		jQuery("#jenis_jadwal").val("")
		jQuery("#lama_pelaksanaan").val("")
		jQuery("#jadwal_tanggal").val("")
	}

	function copy_usulan() {
		if (confirm('Apakah anda yakin untuk melakukan ini? data penetapan akan diupdate sama dengan data usulan.')) {
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
				type: "post",
				data: {
					"action": "copy_usulan_renstra",
					"api_key": jQuery("#api_key").val()
				},
				dataType: "json",
				success: function(res) {
					alert(res.message);
					jQuery('#wrap-loading').hide();
				}
			});
		}
	}

	function report(id_jadwal_lokal) {

		all_skpd();

		let modal = `
			<div class="modal fade" id="modal-report" tab-index="-1" role="dialog" aria-labelledby="modal-indikator-renstra-label" aria-hidden="true">
			  <div class="modal-dialog modal-xl" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title">Export Data</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      
			      <div class="modal-body">
				    <div class="container-fluid">
					    <div class="row">
						    <div class="col-md-2">Unit Kerja</div>
						    <div class="col-md-6">
						    	<select class="form-control list_opd" id="list_opd"></select>
						    </div>
					    </div></br>
					    <div class="row">
					    	<div class="col-md-2">Jenis Laporan</div>
					    	<div class="col-md-6">
					      		<select class="form-control jenis" id="jenis" onchange="jenisLaporan(this)">
					      			<option value="-">Pilih Jenis</option>
					      			<option value="rekap">Format Rekap Renstra</option>
					      			<option value="tc27">Format TC 27</option>
					      			<option value="pagu_akumulasi">Format Pagu Akumulasi Per Unit Kerja</option>
					      			<option value="total_prog_keg">Total Program Kegiatan</option>
				      			</select>
					    	</div>
					    </div></br>
					    <div class="row">
					    	<div class="col-md-2">Pagu</div>
					    	<div class="col-md-6">
					      		<select class="jenis_pagu" id="jenis_pagu" disabled>
					      			<option value="-">Pilih jenis pagu</option>
					      			<option value="0">Usulan</option>
					      			<option value="1">Penetapan</option>
				      			</select>
					    	</div>
					    </div></br>
					    <div class="row">
					    	<div class="col-md-2"></div>
					    	<div class="col-md-6">
					      		<button type="button" class="btn btn-success btn-preview" onclick="preview('${id_jadwal_lokal}')" data-jadwal="${id_jadwal_lokal}">Preview</button>
					      		<button type="button" class="btn btn-primary export-excel" onclick="exportExcel()" disabled>Export Excel</button>
					    	</div>
					    </div></br>
					</div>
			      </div>

			      <div class="modal-preview" style="padding:10px"></div>

			    </div>
			  </div>
			</div>`;

		jQuery("body .report").html(modal);
		jQuery("#modal-report").modal('show');
		jQuery('.jenis').select2({
			width: '100%'
		});
	}

	function all_skpd() {
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
			type: 'post',
			dataType: 'json',
			data: {
				action: 'get_list_skpd',
				tahun_anggaran: tahunAnggaran
			},
			success: function(response) {
				let list_opd = `<option value="">Pilih Unit Kerja</option><option value="all">Semua Unit Kerja</option>`;
				response.map(function(v, i) {
					list_opd += `<option value="${v.id_skpd}">${v.nama_skpd}</option>`;
				});
				jQuery("#list_opd").html(list_opd);
				jQuery('.list_opd').select2({
					width: '100%'
				});
				jQuery('.jenis_pagu').select2({
					width: '100%'
				});
				jQuery('#wrap-loading').hide();
			}
		})
	}

	function jenisLaporan(that) {
		if (jQuery(that).val() == 'tc27') {
			jQuery("#jenis_pagu").attr('disabled', false);
		} else {
			jQuery("#jenis_pagu").val('-').trigger('change');
			jQuery("#jenis_pagu").attr('disabled', true);
		}
	}

	function preview(id_jadwal_lokal) {

		let jenis = jQuery("#jenis").val();
		let id_unit = jQuery("#list_opd").val();

		if (id_unit == '' || id_unit == 'undefined') {
			alert('Unit kerja belum dipilih');
			return;
		}

		if (
			id_unit == 'all' &&
			jenis != 'pagu_akumulasi' &&
			jenis != 'total_prog_keg' &&
			jenis != '-'
		) {
			alert('Pilih minimal satu Unit Kerja!');
			return;
		}

		switch (jenis) {
			case 'rekap':
				generate(id_unit, id_jadwal_lokal, 'view_rekap_renstra', 'Laporan Rekap Renstra');
				break;

			case 'tc27':
				generate(id_unit, id_jadwal_lokal, 'view_laporan_tc27', 'Laporan Renstra TC 27 | Hal. Jadwal Input Renstra', jQuery("#jenis_pagu").val());
				break;

			case 'pagu_akumulasi':
				generate(id_unit, id_jadwal_lokal, 'view_pagu_akumulasi_renstra', 'Laporan Pagu Akumulasi Per Unit Kerja');
				break;

			case 'total_prog_keg':
				window.open('<?php echo $rekap_total_prog_keg_renstra; ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;

			case '-':
				alert('Jenis laporan belum dipilih');
				break;
		}
	}

	function generate(id_unit, id_jadwal_lokal, action, title, option = '', from = 'jadwal') {
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url: ajax.url,
			type: 'post',
			dataType: 'json',
			data: {
				action: action,
				option: option,
				from: from,
				id_unit: id_unit,
				id_jadwal_lokal: id_jadwal_lokal,
				tahun_anggaran: tahunAnggaran,
				api_key: jQuery("#api_key").val(),
			},
			success: function(response) {
				jQuery("#wrap-loading").hide();
				if (response.status == 'error') {
					alert(response.message);
				} else {
					jQuery("#modal-report .modal-preview").html(response.html);
					jQuery("#modal-report .modal-preview").css('overflow-x', 'auto');
					jQuery("#modal-report .modal-preview").css('margin-right', '20px');
					jQuery("#modal-report .modal-preview").css('padding', '15px');
					jQuery('#modal-report .export-excel').attr("disabled", false);
					jQuery('#modal-report .export-excel').attr("title", title);
				}
			}
		})
	}

	function exportExcel() {
		let name = jQuery('#modal-report .export-excel').attr("title");
		tableHtmlToExcel("preview", name);
	}

	function cek_pemutakhiran() {
		list_pemutakhiran({
			'unit': jQuery("#list_opd").val(),
			'tahun_anggaran': tahunAnggaran,
			'id_jadwal_lokal': jQuery('.btn-preview').data('jadwal')
		}).then(function() {
			jQuery("#list-pemutakhiran").DataTable();
		});
	}

	function list_pemutakhiran(obj) {
		return new Promise(function(resolve, reject) {
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
				type: 'post',
				dataType: 'json',
				data: {
					action: 'cek_pemutakhiran_total_renstra',
					id_unit: obj.unit,
					tahun_anggaran: obj.tahun_anggaran,
					id_jadwal_lokal: obj.id_jadwal_lokal,
					api_key: jQuery("#api_key").val(),
				},
				success: function(response) {

					jQuery('#wrap-loading').hide();
					if (response.status) {
						var html = '' +
							'<h4 class="text-center">Data Rekapitulasi Pemutakhiran</h4>' +
							'<table class="table table-bordered" id="list-pemutakhiran" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 80%; border: 0; table-layout: fixed;" contenteditable="false">' +
							'<thead>' +
							'<tr>' +
							'<th class="text-center" style="width: 85px;">No.</th>' +
							'<th class="text-left">Unit Kerja</th>' +
							'<th class="text-center">Program</th>' +
							'<th class="text-center">Kegiatan</th>' +
							'<th class="text-center">Sub Kegiatan</th>' +
							'</tr>' +
							'</thead>' +
							'<tbody>';

						let i = 1;
						Object.keys(response.data.data).forEach(key => {

							let statusProgram = 'btn-success';
							if (response.data.data[key].pemutakhiran_program > 0) {
								statusProgram = "btn-danger";
							}

							let statusKegiatan = 'btn-success';
							if (response.data.data[key].pemutakhiran_kegiatan > 0) {
								statusKegiatan = "btn-danger";
							}

							let statusSubgiat = 'btn-success';
							if (response.data.data[key].pemutakhiran_sub_kegiatan > 0) {
								statusSubgiat = "btn-danger";
							}

							html += '<tr>' +
								'<td class="text-center">' + i + '.</td>' +
								'<td class="text-left">' + response.data.data[key].nama_skpd + '</td>' +
								'<td class="text-center ' + statusProgram + '">' + response.data.data[key].pemutakhiran_program + '</td>' +
								'<td class="text-center ' + statusKegiatan + '">' + response.data.data[key].pemutakhiran_kegiatan + '</td>' +
								'<td class="text-center ' + statusSubgiat + '">' + response.data.data[key].pemutakhiran_sub_kegiatan + '</td>' +
								'</tr>';
							i++;
						});

						html += '' +
							'</tbody>' +
							'<tfoot>' +
							'<tr>' +
							'<th class="text-center" colspan="2">TOTAL</th>' +
							'<th class="text-center">' + response.data.pemutakhiran_program + '</th>' +
							'<th class="text-center">' + response.data.pemutakhiran_kegiatan + '</th>' +
							'<th class="text-center">' + response.data.pemutakhiran_sub_kegiatan + '</th>' +
							'</tr>'; +
						'</tfoot>' +
						'</table>';

						jQuery('#tabel-pemutakhiran-belanja').html(html);
					}
					resolve();
				}
			});
		});
	}

	function get_jadwal_rpd_esakip() {
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