<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

global $wpdb;
$select_rpjpd = '';
$data_rpjpd = $wpdb->get_results(
	$wpdb->prepare('
		SELECT *
		FROM data_jadwal_lokal
		WHERE status = 0
		  AND id_tipe = %d
	', 1),
	ARRAY_A
);

if (!empty($data_rpjpd)) {
	foreach ($data_rpjpd as $v) {
		$tahun_akhir_anggaran = ($v['tahun_anggaran'] + $v['lama_pelaksanaan']) - 1;
		$status = ['AKTIF', 'DIHAPUS', 'DIKUNCI'];
		$select_rpjpd .= '<option value="' . $v['id_jadwal_lokal'] . '">' . $v['nama'] . ' [' . $status[$v['status']] . ' ] | ' . $v['tahun_anggaran'] . ' - ' . $tahun_akhir_anggaran . '</option>';
	}
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
		<h1 class="text-center" style="margin:3rem;">Jadwal Input Perencanaan RPD Lokal</h1>
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
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalTambahJadwalLabel">Tambah Jadwal</h5>
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
							<label for="link_rpjpd">Pilih Jadwal RPJPD</label>
							<select id="link_rpjpd" class="form-control">
								<option value="">Pilih RPJPD</option>
								<?php echo $select_rpjpd; ?>
							</select>
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
						<div class="form-group mb-2">
							<label for="id_jadwal_esakip">Pilih Jadwal RPD</label>
							<select id="id_jadwal_esakip" class="form-control">
							</select>
							<small class="text-muted">Data Jadwal diambil dari Aplikasi <strong>WP-Eval-Sakip</strong></small>
						</div>
						<small class="text-justify">Pilih jadwal untuk fitur <strong>Integrasi data RPD (Visi RPJPD, Tujuan, Sasaran, Program)</strong> (opsional).</small>
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

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
	jQuery(document).ready(function() {
		globalThis.tipePerencanaan = 'rpd'
		globalThis.tahunAnggaran = "<?php echo get_option('_crb_tahun_anggaran_sipd'); ?>"
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

				let options = `<option value="">Pilih Jadwal RPD</option>`;
				const statusMapping = ['DIHAPUS', 'AKTIF', 'DIKUNCI']

				if (jadwal_rpd_esakip.status) {
					jadwal_rpd_esakip.data.forEach((val) => {
						if (val.jenis_jadwal_khusus == 'rpd') {
							options += `<option value="${val.id}">${val.nama_jadwal} [${statusMapping[val.status]}] | ${val.tahun_anggaran} - ${val.tahun_selesai_anggaran}</option>`;
						}
					});

					jQuery(`#id_jadwal_esakip`).empty().append(options).removeAttr('disabled');
				} else {
					options = `<option value="">Tidak tersedia</option>`;
					jQuery(`#id_jadwal_esakip`).empty().append(options).attr('disabled', true);
					alert(jadwal_rpd_esakip.message);
				}

				jQuery(`#jadwal-esakip-container`).show();
			}

			jQuery("#jadwal_nama").val('');
			jQuery("#tahun_mulai_anggaran").val('');
			jQuery('#jadwal_tanggal').data('daterangepicker');
			jQuery('#jadwal_tanggal').data('daterangepicker');
			jQuery("#link_rpjpd").val('').change();
			jQuery("#lama_pelaksanaan").val(lama_pelaksanaan.data.lama_pelaksanaan);

			jQuery('#modalTambahJadwal').modal('show');
			jQuery("#wrap-loading").hide();
		} catch (error) {
			jQuery("#wrap-loading").hide();
			console.error(`terjadi kesalahan saat tambah_jadwal = ${error}`);
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
		let relasi_perencanaan = jQuery("#link_rpjpd").val()
		let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
		let id_jadwal_sakip = jQuery("#id_jadwal_esakip").val()
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
					'relasi_perencanaan': relasi_perencanaan,
					'lama_pelaksanaan': this_lama_pelaksanaan,
					'id_jadwal_sakip': id_jadwal_sakip
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
					} else {
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

	async function edit_data_penjadwalan(id_jadwal_lokal) {
		try {
			jQuery("#wrap-loading").show();
			jQuery("#modalTambahJadwal .modal-title").html("Edit Penjadwalan");
			jQuery("#modalTambahJadwal .submitBtn")
				.attr("onclick", `submitEditJadwalForm(${id_jadwal_lokal})`)
				.attr("disabled", false)
				.text("Perbarui");

			const data_jadwal = await get_data_jadwal_by_id(id_jadwal_lokal);

			if (is_api_ready_esakip) {
				const jadwal_rpd_esakip = await get_jadwal_rpd_esakip();
				console.log('aktif integrasi')

				let options = `<option value="">Pilih Jadwal RPD</option>`;
				const statusMapping = ['DIHAPUS', 'AKTIF', 'DIKUNCI']

				if (jadwal_rpd_esakip.status) {
					jadwal_rpd_esakip.data.forEach((val) => {
						if (val.jenis_jadwal_khusus == 'rpd') {
							options += `<option value="${val.id}">${val.nama_jadwal} [${statusMapping[val.status]}] | ${val.tahun_anggaran} - ${val.tahun_selesai_anggaran}</option>`;
						}
					});

					jQuery(`#id_jadwal_esakip`).empty().append(options).removeAttr('disabled');
					jQuery("#id_jadwal_esakip").val(data_jadwal.data.id_jadwal_sakip);
				} else {
					options = `<option value="">Tidak tersedia</option>`;
					jQuery(`#id_jadwal_esakip`).empty().append(options).attr('disabled', true);
					alert(jadwal_rpd_esakip.message);
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
			jQuery("#link_rpjpd").val(data_jadwal.data.relasi_perencanaan).change();

			jQuery('#modalTambahJadwal').modal('show');
			jQuery("#wrap-loading").hide();
		} catch (error) {
			jQuery("#wrap-loading").hide();
			console.error(`terjadi kesalahan saat tambah_jadwal = ${error}`);
			alert('terjadi kesalahan saat tambah data');
		}
	}

	function submitEditJadwalForm(id_jadwal_lokal) {
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
		let relasi_perencanaan = jQuery("#link_rpjpd").val()
		let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
		let id_jadwal_sakip = jQuery("#id_jadwal_esakip").val()
		if (nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == '' || this_lama_pelaksanaan == '') {
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		} else {
			jQuery("#wrap-loading").show()
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
					'relasi_perencanaan': relasi_perencanaan,
					'lama_pelaksanaan': this_lama_pelaksanaan,
					'id_jadwal_sakip': id_jadwal_sakip
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
					jQuery("#tahun_mulai_anggaran").val('')
					jQuery("#link_rpjpd").val('')
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
					'action': 'submit_lock_schedule_rpd',
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

	function report(id_jadwal_lokal) {
		all_skpd();

		let modal = `
			<div class="modal fade" id="modal-report" tab-index="-1" role="dialog" aria-labelledby="modal-indikator-renstra-label" aria-hidden="true">
			  <div class="modal-dialog modal-lg" role="document" style="min-width:1450px">
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
								<small>Pilih OPD untuk print rekap format pagu akumulasi.</small>
						    </div>
					    </div></br>
						<div class="row">
					    	<div class="col-md-2">Jenis Laporan</div>
					    	<div class="col-md-6">
					      		<select class="form-control jenis" id="jenis">
					      			<option>Pilih Jenis</option>
					      			<option value="rekap">Format RPD</option>
									<option value="pagu_akumulasi">Format Pagu Akumulasi</option>
				      			</select>
					    	</div>
					    </div></br>
					    <div class="row">
					    	<div class="col-md-2"></div>
					    	<div class="col-md-6">
					      		<button type="button" class="btn btn-success btn-preview" onclick="preview('${id_jadwal_lokal}')">Preview</button>
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
	}

	function all_skpd() {
		jQuery.ajax({
			url: ajax.url,
			type: 'post',
			dataType: 'json',
			data: {
				action: 'get_list_skpd',
				tahun_anggaran: tahunAnggaran
			},
			success: function(response) {
				let list_opd = `<option value="">Pilih OPD</option><option value="all">Semua Unit Kerja</option>`;
				response.map(function(v, i) {
					list_opd += `<option value="${v.id_skpd}">${v.nama_skpd}</option>`;
				});
				jQuery("#list_opd").html(list_opd);
				jQuery('.list_opd').select2();
				jQuery('.jenis').select2();
			}
		})
	}

	function preview(id_jadwal_lokal) {

		let jenis = jQuery("#jenis").val();

		switch (jenis) {
			case 'rekap':
				rekap(id_jadwal_lokal);
				break;

			case 'pagu_akumulasi':
				let id_unit = jQuery("#list_opd").val();
				if (id_unit == '' || id_unit == 'undefined') {
					alert('Unit kerja belum dipilih');
					return;
				}
				pagu_akumulasi(id_unit, id_jadwal_lokal);
				break;

			default:
				alert('Jenis laporan belum dipilih');
				break;
		}
	}

	function rekap(id_jadwal_lokal) {
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url: ajax.url,
			type: 'post',
			dataType: 'json',
			data: {
				action: 'view_rekap_rpd',
				id_jadwal_lokal: id_jadwal_lokal,
				api_key: ajax.api_key,
			},
			success: function(response) {
				if (response.status == 'success') {
					jQuery('#wrap-loading').hide();
					jQuery("#modal-report .modal-preview").html(response.html);
					jQuery('#modal-report .export-excel').attr("disabled", false);
					jQuery('#modal-report .export-excel').attr("title", 'Laporan RPD');
				} else {
					alert(`GAGAL! \n${response.message}`)
				}
			}
		})
	}

	function pagu_akumulasi(id_unit, id_jadwal_lokal) {
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url: ajax.url,
			type: 'post',
			dataType: 'json',
			data: {
				action: 'view_pagu_akumulasi_rpd',
				id_unit: id_unit,
				id_jadwal_lokal: id_jadwal_lokal,
				api_key: ajax.api_key,
			},
			success: function(response) {
				if (response.status == 'success') {
					jQuery('#wrap-loading').hide();
					jQuery("#modal-report .modal-preview").html(response.html);
					jQuery('#modal-report .export-excel').attr("disabled", false);
					jQuery('#modal-report .export-excel').attr("title", 'Laporan Pagu Akumulasi');
				} else {
					jQuery('#wrap-loading').hide();
					alert(`GAGAL! \n${response.message}`)
				}
			}
		})
	}

	function exportExcel() {
		let name = jQuery('#modal-report .export-excel').attr("title");
		tableHtmlToExcel("preview", name);
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