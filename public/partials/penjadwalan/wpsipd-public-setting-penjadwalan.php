<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

$input = shortcode_atts(array(
	'tahun_anggaran' => '2022'
), $atts);

if (isset($tipe_perencanaan)) {
	if ($tipe_perencanaan == 'renja') {
		$judul = 'Renja';
	} else {
		$tipe_perencanaan = 'penganggaran_sipd';
		$judul = 'Penganggaran SIPD';
	}
} else {
	$tipe_perencanaan = 'penganggaran_sipd';
	$judul = 'Penganggaran SIPD';
}

$is_admin = false;
$user_id = um_user('ID');
$user_meta = get_userdata($user_id);
$js_check_admin = 0;
if (
	in_array("administrator", $user_meta->roles)
) {
	$is_admin = true;
}

global $wpdb;
$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran order by tahun_anggaran ASC', ARRAY_A);
$select_tahun = "<option value=''>Pilih berdasarkan tahun anggaran</option>";
foreach ($tahun as $tahun_value) {
	$select = $tahun_value['tahun_anggaran'] == $input['tahun_anggaran'] ? 'selected' : '';
	$nama_page_menu_ssh = 'Setting penjadwalan | ' . $tahun_value['tahun_anggaran'];
	$custom_post = get_page_by_title($nama_page_menu_ssh, OBJECT, 'page');
	$url_data_ssh = $this->get_link_post($custom_post);
	$select_tahun .= "<option value='" . $url_data_ssh . "' " . $select . ">Setting penjadwalan | " . $tahun_value['tahun_anggaran'] . "</option>";
}

$select_renstra = '';
$select_renja_pergeseran = "<option value=''>Pilih RENJA Pergeseran</option>";

$sqlTipe = $wpdb->get_results($wpdb->prepare(
	"
	SELECT 
		* 
	FROM 
		`data_tipe_perencanaan` 
	WHERE 
		nama_tipe=%s",
	'renstra'
), ARRAY_A);
$data_renstra = $wpdb->get_results($wpdb->prepare(
	'
	SELECT
		id_jadwal_lokal,
		nama
	FROM
		data_jadwal_lokal
	WHERE
		status=1
		and id_tipe=%d',
	$sqlTipe[0]['id']
), ARRAY_A);

if (!empty($data_renstra)) {
	foreach ($data_renstra as $val_renstra) {
		$select_renstra .= '<option value="' . $val_renstra['id_jadwal_lokal'] . '">' . $val_renstra['nama'] . '</option>';
	}
}

$data_renja_pergeseran = $wpdb->get_results($wpdb->prepare('
	SELECT
		*
	FROM
		data_jadwal_lokal
	WHERE status=1
		AND id_tipe=5
		AND tahun_anggaran=%d', $input['tahun_anggaran']), ARRAY_A);

if (!empty($data_renja_pergeseran)) {
	foreach ($data_renja_pergeseran as $val_renja) {
		$tanggal_kunci = substr($val_renja['waktu_akhir'], 0, 10);
		$select_renja_pergeseran .= "<option value='" . $val_renja["id_jadwal_lokal"] . "'>" . $val_renja["nama"] . " || " . $tanggal_kunci . "</option>";
	}
}

$title = 'RENJA SIPD Merah | ' . $input['tahun_anggaran'];
$shortcode = '[renja_sipd_merah tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_renja_merah = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'RENJA SIPD RI | ' . $input['tahun_anggaran'];
$shortcode = '[renja_sipd_ri tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_renja_ri = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Analisis Belanja per-Program | ' . $input['tahun_anggaran'];
$shortcode = '[analisis_belanja_program tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_analisis_belanja_program = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Analisis Belanja per-Kegiatan | ' . $input['tahun_anggaran'];
$shortcode = '[analisis_belanja_kegiatan tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_analisis_belanja_kegiatan = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Analisis Belanja per-Sub Kegiatan | ' . $input['tahun_anggaran'];
$shortcode = '[analisis_belanja_sub_kegiatan tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_analisis_belanja_sub_kegiatan = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Analisis Belanja per-Bidang Urusan | ' . $input['tahun_anggaran'];
$shortcode = '[analisis_belanja_bidang_urusan tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_analisis_belanja_bidang_urusan = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Analisis Belanja per-Sumber Dana | ' . $input['tahun_anggaran'];
$shortcode = '[analisis_belanja_sumber_dana tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_analisis_belanja_sumber_dana = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Analisis Belanja per-Rekening Belanja | ' . $input['tahun_anggaran'];
$shortcode = '[analisis_belanja_rekening tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_analisis_belanja_rekening = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Laporan Konsistensi RPJM | ' . $input['tahun_anggaran'];
$shortcode = '[laporan_konsistensi_rpjm tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_laporan_konsistensi_rpjm = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Rekap Sumber Dana Per SKPD | ' . $input['tahun_anggaran'];
$shortcode = '[rekap_sumber_dana_per_skpd tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_rekap_sumber_dana_per_skpd = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Rekap Sumber Dana Per Program | ' . $input['tahun_anggaran'];
$shortcode = '[rekap_sumber_dana_per_program tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_rekap_sumber_dana_per_program = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Rekap Sumber Dana Per Kegiatan | ' . $input['tahun_anggaran'];
$shortcode = '[rekap_sumber_dana_per_kegiatan tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_rekap_sumber_dana_per_kegiatan = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Rekap Sumber Dana Per Sub Kegiatan | ' . $input['tahun_anggaran'];
$shortcode = '[rekap_sumber_dana_per_sub_kegiatan tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_rekap_sumber_dana_per_sub_kegiatan = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Rekap Sumber Dana Per Rekening Belanja | ' . $input['tahun_anggaran'];
$shortcode = '[rekap_sumber_dana_per_rekening tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$url_rekap_sumber_dana_per_rekening = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Rekap Longlist Per Jenis Belanja | ' . $input['tahun_anggaran'];
$shortcode = '[rekap_longlist_per_jenis_belanja tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$rekap_longlist_per_jenis_belanja = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = $input['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 1';
$shortcode = '[apbdpenjabaran tahun_anggaran="' . $input['tahun_anggaran'] . '" lampiran="1"]';
$apbd_penjabaran_lampiran_1 = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = $input['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 2';
$shortcode = '[apbdpenjabaran tahun_anggaran="' . $input['tahun_anggaran'] . '" lampiran="2"]';
$apbd_penjabaran_lampiran_2 = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'APBD Perda Lampiran 1 | ' . $input['tahun_anggaran'];
$shortcode = '[apbd_perda_lampiran_1 tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$apbd_perda_lampiran_1 = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'APBD Perda Lampiran 2 | ' . $input['tahun_anggaran'];
$shortcode = '[apbd_perda_lampiran_2 tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$apbd_perda_lampiran_2 = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'APBD Perda Lampiran 3 | ' . $input['tahun_anggaran'];
$shortcode = '[apbd_perda_lampiran_3 tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$apbd_perda_lampiran_3 = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'APBD Perda Lampiran 4 | ' . $input['tahun_anggaran'];
$shortcode = '[apbd_perda_lampiran_4 tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$apbd_perda_lampiran_4 = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'APBD Perda Lampiran 5 | ' . $input['tahun_anggaran'];
$shortcode = '[apbd_perda_lampiran_5 tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$apbd_perda_lampiran_5 = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'APBD Perda Lampiran 7 | ' . $input['tahun_anggaran'];
$shortcode = '[apbd_perda_lampiran_7 tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$apbd_perda_lampiran_7 = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'APBD Perda Lampiran 8 | ' . $input['tahun_anggaran'];
$shortcode = '[apbd_perda_lampiran_8 tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$apbd_perda_lampiran_8 = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$title = 'Rekap Longlist Per Jenis Belanja Semua SKPD | ' . $input['tahun_anggaran'];
$shortcode = '[rekap_longlist_per_jenis_belanja_all_skpd tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
$rekap_longlist_per_jenis_belanja_all_skpd = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, false);

$body = '';

$data_skpd_db = $wpdb->get_results($wpdb->prepare(
	"
	SELECT 
		* 
	from data_unit 
	where tahun_anggaran=%d
		and active=1",
	$input['tahun_anggaran']
), ARRAY_A);

$list_skpd = "";
$skpd_json = array();
foreach($data_skpd_db as $skpd){
	$skpd_json[$skpd['id_skpd']] = $skpd;
	$list_skpd .= "
	<tr>
		<td class='text-center'><input type='checkbox' value='".$skpd['id_skpd']."'></td>
		<td>".$skpd['kode_skpd']."</td>
		<td>".$skpd['nama_skpd']."</td>
	</tr>
	";
}
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
		<h1 class="text-center" style="margin:3rem;">Halaman Setting Penjadwalan <?php echo $judul; ?> <?php echo $input['tahun_anggaran']; ?></h1>
		<?php if ($is_admin) : ?>
			<div style="margin-bottom: 25px;">
				<button class="btn btn-primary tambah_jadwal" onclick="tambah_jadwal();" hidden><span class="dashicons dashicons-plus"></span>Tambah Jadwal</button>
			</div>
		<?php endif; ?>
		<table id="data_penjadwalan_table" cellpadding="2" cellspacing="0">
			<thead id="data_header">
				<tr>
					<th class="text-center">Nama Tahapan</th>
					<th class="text-center">Status</th>
					<th class="text-center">Jadwal Mulai</th>
					<th class="text-center">Jadwal Selesai</th>
					<th class="text-center">Tahun Anggaran</th>
					<?php if ($tipe_perencanaan == 'renja') : ?>
						<th class="text-center">Jadwal RENSTRA</th>
						<th class="text-center">Jenis Jadwal</th>
					<?php endif; ?>
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
				<div class="form-group">
					<label for='jadwal_nama'>Nama Tahapan</label>
					<input type='text' id='jadwal_nama' class="form-control" placeholder='Nama Tahapan'>
				</div>
				<div class="form-group">
					<label for='jadwal_tanggal'>Jadwal Pelaksanaan</label>
					<input type="text" id='jadwal_tanggal' class="form-control" name="jadwal_tanggal" />
				</div>
				<?php if ($tipe_perencanaan == 'renja') : ?>
					<div class="form-group">
						<label for="link_renstra">Pilih Jadwal RENSTRA</label>
						<select id="link_renstra" class="form-control">
							<option value="">Pilih RENSTRA</option>
							<?php echo $select_renstra; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="jenis_jadwal">Pilih Jenis Jadwal</label>
						<select id="jenis_jadwal" class="form-control">
							<option value="usulan" selected>Usulan</option>
							<option value="penetapan">Penetapan</option>
						</select>
					</div>
					<div class="mt-3 form-input">
						<input type="checkbox" value="1" id="pergeseran_renja" onclick="set_setting_pergeseran(this);">
						<label for="pergeseran_renja">Pergeseran/Perubahan</label>
					</div>
					<div class="form-group">
						<label for="id_jadwal_pergeseran_renja" class="class_renja_pergeseran">Pilih Jadwal RENJA Pergeseran</label>
						<select id="id_jadwal_pergeseran_renja" class="class_renja_pergeseran form-control">
							<?php echo $select_renja_pergeseran; ?>
						</select>
					</div>
				<?php endif; ?>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="submitTambahJadwalForm()">Simpan</button>
				<button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Copy data renja -->
<div class="modal fade" id="modal-copy-renja-sipd" data-backdrop="static" role="dialog" aria-labelledby="modal-copy-renja-sipd-label" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Tipe Copy Data RENJA ke Lokal</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="text-center" style="margin-bottom: 20px;">
					<input type="checkbox" id="copyDataRka" name="copyDataSipd" value="rincian_rka">
					<label for="copyDataRka">Copy Data Rincian RKA</label>
					<input type="checkbox" id="copySumberDana" name="copyDataSipd" value="sumber_dana" style="margin-left: 30px;">
					<label for="copySumberDana">Copy Sumber Dana</label>
				</div>
				<table class="table table-bordered" id="table-list-skpd">
					<thead>
						<tr>
							<th class="text-center"><input type="checkbox" class="check_all"></th>
							<th class="text-center">Kode OPD</th>
							<th class="text-center" width="380px">Nama OPD</th>
						</tr>
					</thead>
					<tbody>
						<?php echo $list_skpd; ?>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="copy_renja_sipd_to_lokal_all()">Simpan</button>
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
	jQuery(document).ready(function() {
		globalThis.tahun_anggaran = <?php echo $input['tahun_anggaran']; ?>;
		globalThis.tipe_perencanaan = '<?php echo $tipe_perencanaan; ?>';
		globalThis.thisAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>"
		globalThis.list_skpd = <?php echo json_encode($skpd_json); ?>

		get_data_penjadwalan();

		jQuery('#selectYears').on('change', function(e) {
			let selectedVal = jQuery(this).find('option:selected').val();
			if (selectedVal != '') {
				window.location = selectedVal;
			}
		});

		jQuery('#modalTambahJadwal').on('hidden.bs.modal', function() {
			jQuery("#jadwal_nama").val("");
			jQuery("#link_renstra").val("");
		})

		jQuery('#jadwal_tanggal').daterangepicker({
			timePicker: true,
			timePicker24Hour: true,
			startDate: moment().startOf('hour'),
			endDate: moment().startOf('hour').add(32, 'hour'),
			locale: {
				format: 'DD-MM-YYYY HH:mm'
			}
		});

		jQuery(".class_renja_pergeseran").hide();
		jQuery('#table-list-skpd').DataTable({
			"aoColumnDefs": [
		        { "bSortable": false, "aTargets": [ 0 ] }, 
		        { "bSearchable": false, "aTargets": [ 0 ] }
		    ],
		    "order": [[1, 'asc']],
			lengthMenu: [[5, 20, 100, -1], [5, 20, 100, "All"]]
		});
		jQuery('.check_all').on('click', function(){
			jQuery(this).closest('table').find('tbody tr td input[type="checkbox"]').prop('checked', jQuery(this).is(':checked'));
		});
	});

	function get_data_penjadwalan() {
		jQuery("#wrap-loading").show();
		globalThis.penjadwalanTable = jQuery('#data_penjadwalan_table').DataTable({
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
				if (json.status == 'error') {
					alert(json.message);
				} else {
					if (json.checkOpenedSchedule != 'undefined' && json.checkOpenedSchedule > 0) {
						jQuery(".tambah_jadwal").prop('hidden', true);
					} else {
						jQuery(".tambah_jadwal").prop('hidden', false);
					}
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
				<?php if ($tipe_perencanaan == 'renja') : ?> {
						"data": "relasi_perencanaan",
						className: "text-center"
					},
					{
						"data": "jenis_jadwal",
						className: "text-center"
					},
				<?php endif; ?> {
					"data": "aksi",
					className: "text-center"
				}
			]
		});
	}

	<?php if ($is_admin) : ?>
		/** show modal tambah jadwal */
		function tambah_jadwal() {
			jQuery("#modalTambahJadwal .modal-title").html("Tambah Penjadwalan");
			jQuery("#modalTambahJadwal .submitBtn")
				.attr("onclick", 'submitTambahJadwalForm()')
				.attr("disabled", false)
				.text("Simpan");
			jQuery('#modalTambahJadwal').modal('show');

			let pergeseran = "<?php echo $select_renja_pergeseran; ?>";
			jQuery("#id_jadwal_pergeseran_renja").html(pergeseran);

		}

		/** Submit tambah jadwal */
		function submitTambahJadwalForm() {
			let this_tahun_anggaran = tahun_anggaran;
			let nama = jQuery('#jadwal_nama').val();
			if (nama.trim() == '') {
				return alert("Nama jadwal tidak boleh kosong!");
			}
			let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss');
			if (jadwalMulai == '') {
				return alert('Jadwal mulai tidak boleh kosong!');
			}
			let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss');
			if (jadwalSelesai == '') {
				return alert('Jadwal selesai tidak boleh kosong!');
			}
			let jenis_jadwal = 1;
			let relasi_perencanaan = '';
			let pergeseran_renja = '';
			let id_jadwal_pergeseran_renja = false;
			pergeseran_renja = jQuery("#pergeseran_renja").prop('checked');
			<?php if ($tipe_perencanaan == 'renja') : ?>
				jenis_jadwal = jQuery("#jenis_jadwal").val();
				if (jenis_jadwal == '') {
					return alert('Jenis jadwal tidak boleh kosong!');
				}
				relasi_perencanaan = jQuery("#link_renstra").val();
				id_jadwal_pergeseran_renja = jQuery("#id_jadwal_pergeseran_renja").val()
				if (pergeseran_renja == true && id_jadwal_pergeseran_renja == '') {
					return alert("Jadwal Renja Pergeseran harus dipilih!");
				}
			<?php endif; ?>
			<?php if ($tipe_perencanaan == 'renja') : ?>
				jenis_jadwal = jQuery("#jenis_jadwal").val();
				if (jenis_jadwal == '') {
					return alert('Jenis jadwal tidak boleh kosong!');
				}
				relasi_perencanaan = jQuery("#link_renstra").val();
				pergeseran_renja = jQuery("#pergeseran_renja").prop('checked');
				id_jadwal_pergeseran_renja = jQuery("#id_jadwal_pergeseran_renja").val()
				if (pergeseran_renja == true && id_jadwal_pergeseran_renja == '') {
					return alert("Jadwal Renja Pergeseran harus dipilih!");
				}
			<?php endif; ?>
			jQuery("#wrap-loading").show();
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
					'relasi_perencanaan': relasi_perencanaan,
					'jenis_jadwal': jenis_jadwal,
					'pergeseran_renja': pergeseran_renja,
					'id_jadwal_pergeseran_renja': id_jadwal_pergeseran_renja
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
						jQuery("#link_renstra").val('');
						penjadwalanTable.ajax.reload();
						jQuery(".tambah_jadwal").prop('hidden', true);
					} else {
						jQuery('.submitBtn').attr('disabled', false);
					}
				}

			});
		}

		/** edit akun ssh usulan */
		function edit_data_penjadwalan(id_jadwal_lokal) {
			jQuery('#modalTambahJadwal').modal('show');
			jQuery("#modalTambahJadwal .modal-title").html("Edit Penjadwalan");
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
					jQuery("#link_renstra").val(response.data.relasi_perencanaan).change();
					jQuery("#jenis_jadwal").val(response.data.jenis_jadwal).change();
					jQuery("#id_jadwal_pergeseran_renja").html(response.data.select_option_pergeseran_renja);
					if (response.data.status_jadwal_pergeseran == 'tampil') {
						jQuery("#pergeseran_renja").prop("checked", true);
						jQuery(".class_renja_pergeseran").show();
						jQuery("#id_jadwal_pergeseran_renja").val(response.data.id_jadwal_pergeseran)
					} else {
						jQuery("#pergeseran_renja").prop("checked", false);
						jQuery(".class_renja_pergeseran").hide();
					}
				}
			})
		}

		function submitEditJadwalForm(id_jadwal_lokal) {
			let this_tahun_anggaran = tahun_anggaran;
			let nama = jQuery('#jadwal_nama').val();
			if (nama.trim() == '') {
				return alert("Nama jadwal tidak boleh kosong!");
			}
			let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss');
			if (jadwalMulai == '') {
				return alert('Jadwal mulai tidak boleh kosong!');
			}
			let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss');
			if (jadwalSelesai == '') {
				return alert('Jadwal selesai tidak boleh kosong!');
			}
			let jenis_jadwal = 1;
			let relasi_perencanaan = '';
			let pergeseran_renja = '';
			let id_jadwal_pergeseran_renja = false;
			<?php if ($tipe_perencanaan == 'renja') : ?>
				jenis_jadwal = jQuery("#jenis_jadwal").val();
				if (jenis_jadwal == '') {
					return alert('Jenis jadwal tidak boleh kosong!');
				}
				relasi_perencanaan = jQuery("#link_renstra").val();
				pergeseran_renja = jQuery("#pergeseran_renja").prop('checked');
				id_jadwal_pergeseran_renja = jQuery("#id_jadwal_pergeseran_renja").val()
				if (pergeseran_renja == true && id_jadwal_pergeseran_renja == '') {
					return alert("Jadwal Renja Pergeseran harus dipilih!");
				}
			<?php endif; ?>
			jQuery('#wrap-loading').show();
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
					'relasi_perencanaan': relasi_perencanaan,
					'jenis_jadwal': jenis_jadwal,
					'pergeseran_renja': pergeseran_renja,
					'id_jadwal_pergeseran_renja': id_jadwal_pergeseran_renja
				},
				beforeSend: function() {
					jQuery('.submitBtn').attr('disabled', 'disabled');
				},
				success: function(response) {
					jQuery('#modalTambahJadwal').modal('hide');
					jQuery('#wrap-loading').hide();
					if (response.status == 'success') {
						alert('Data berhasil diperbarui');
						penjadwalanTable.ajax.reload();
					} else {
						alert(`GAGAL! \n${response.message}`);
					}
					jQuery('#jadwal_nama').val('');
					jQuery("#link_renstra").val('');
					jQuery("#jenis_jadwal").val('');
				}
			});
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
						'action': 'submit_lock_schedule',
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

		function cannot_change_schedule(jenis) {
			if (jenis == 'kunci') {
				alert('Tidak bisa kunci karena penjadwalan sudah dikunci');
			} else if (jenis == 'edit') {
				alert('Tidak bisa edit karena penjadwalan sudah dikunci');
			} else if (jenis == 'hapus') {
				alert('Tidak bisa hapus karena penjadwalan sudah dikunci');
			}
		}

		function copy_usulan() {
			if (confirm('Apakah anda yakin untuk melakukan ini? data penetapan akan diupdate sama dengan data usulan.')) {
				jQuery('#wrap-loading').show();
				jQuery.ajax({
					url: ajax.url,
					type: "post",
					data: {
						"action": "copy_usulan_renja",
						"api_key": jQuery("#api_key").val(),
						"tahun_anggaran": tahun_anggaran
					},
					dataType: "json",
					success: function(res) {
						alert(res.message);
						jQuery('#wrap-loading').hide();
					}
				});
			}
		}

		function copy_penetapan() {
			if (confirm('Apakah anda yakin untuk melakukan ini? data usulan akan diupdate sama dengan data penetapan.')) {
				jQuery('#wrap-loading').show();
				jQuery.ajax({
					url: ajax.url,
					type: "post",
					data: {
						"action": "copy_penetapan_renja",
						"api_key": jQuery("#api_key").val(),
						"tahun_anggaran": tahun_anggaran
					},
					dataType: "json",
					success: function(res) {
						alert(res.message);
						jQuery('#wrap-loading').hide();
					}
				});
			}
		}

		function copy_data_renstra(id_jadwal) {
			if (confirm('Apakah anda yakin untuk melakukan ini? data RENJA akan diisi sesuai data RENSTRA tahun berjalan!.')) {
				jQuery('#wrap-loading').show();
				jQuery.ajax({
					url: ajax.url,
					type: "post",
					data: {
						"action": "copy_data_renstra_ke_renja",
						"api_key": jQuery("#api_key").val(),
						'id_jadwal': id_jadwal
					},
					dataType: "json",
					success: function(res) {
						alert(res.message);
						jQuery('#wrap-loading').hide();
					}
				});
			}
		}
	<?php endif; ?>

	function get_jadwal_by_id(id_jadwal_lokal) {
		return new Promise(function(resolve, reject) {
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
					return resolve(response);
				}
			})
		})
	}

	function report(id_jadwal_lokal) {
		let modal = `
		<div class="modal fade" id="modal-report" tabindex="-1" role="dialog" aria-labelledby="modal-indikator-renja-label" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Laporan Jadwal Tahap Testing</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
				<div class="row mb-3">
					<div class="col-md-2 font-weight-bold">Unit Kerja</div>
					<div class="col-md-10">
					<select class="form-control list_perangkat_daerah w-100" id="list_perangkat_daerah"></select>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-md-2 font-weight-bold">Jenis Laporan</div>
					<div class="col-md-10">
					<select class="form-control jenis w-100" id="jenis" onchange="jenis_laporan(this)">
						<option value="-">Pilih Jenis</option>
						<option value="laporan_konsistensi_rpjm">Laporan Konsistensi RPJM</option>
						<option value="pagu_total">Format Pagu Total Per Unit Kerja</option>
						<option value="renja_sipd_merah">Format RENJA SIPD Merah</option>
						<option value="renja_sipd_ri">Format RENJA SIPD RI</option>
						<option value="analisis_belanja_program">Analisis Belanja Pagu per-Program</option>
						<option value="analisis_belanja_kegiatan">Analisis Belanja Pagu per-Kegiatan</option>
						<option value="analisis_belanja_sub_kegiatan">Analisis Belanja Pagu per-Sub Kegiatan</option>
						<option value="analisis_belanja_bidang_urusan">Analisis Belanja Pagu per-Bidang Urusan</option>
						<option value="analisis_belanja_sumber_dana">Analisis Belanja Pagu per-Sumber Dana</option>
						<option value="analisis_belanja_rekening">Analisis Belanja Pagu per-Rekening</option>
						<option value="rekap_sumber_dana_per_skpd">Rekap Sumber Dana Per SKPD</option>
						<option value="rekap_sumber_dana_per_program">Rekap Sumber Dana Per Program</option>
						<option value="rekap_sumber_dana_per_kegiatan">Rekap Sumber Dana Per Kegiatan</option>
						<option value="rekap_sumber_dana_per_sub_kegiatan">Rekap Sumber Dana Per Sub Kegiatan</option>
						<option value="rekap_sumber_dana_per_rekening">Rekap Sumber Dana Per Rekening</option>
						<option value="rekap_longlist_per_jenis_belanja">Rekap Longlist Per Jenis Belanja</option>
						<option value="apbd_penjabaran_lampiran_1">APBD Penjabaran Lampiran I</option>
						<option value="apbd_penjabaran_lampiran_2">APBD Penjabaran Lampiran II</option>
						<option value="apbd_perda_lampiran_1">APBD Perda Lampiran I</option>
						<option value="apbd_perda_lampiran_2">APBD Perda Lampiran II</option>
						<option value="apbd_perda_lampiran_3">APBD Perda Lampiran III</option>
						<option value="apbd_perda_lampiran_4">APBD Perda Lampiran IV</option>
						<option value="apbd_perda_lampiran_5">APBD Perda Lampiran V</option>
						<option value="apbd_perda_lampiran_7">APBD Perda Lampiran VII</option>
						<option value="apbd_perda_lampiran_8">APBD Perda Lampiran VIII</option>
						<option value="rekap_longlist_per_jenis_belanja_all_skpd">Rekap Longlist Per Jenis Belanja Semua SKPD</option>
					</select>
					</div>
				</div>
				<div class="row mb-3" style="display:none" id="opt_jenis_sumber_dana_div">
					<div class="col-md-2 font-weight-bold">Sumber Data</div>
					<div class="col-md-10">
					<select class="form-control jenis w-100" id="opt_jenis_sumber_dana">
						<option value="1">Sumber Dana dari Rincian Belanja</option>
						<option value="2">Sumber Dana dari Sub Kegiatan</option>
					</select>
					</div>
				</div>
				<div class="row mb-3" style="display:none" id="opt_jenis_jadwal_div">
					<div class="col-md-2 font-weight-bold">Jenis Jadwal</div>
					<div class="col-md-10">
					<select class="form-control jenis w-100" id="opt_jenis_jadwal"></select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2"></div>
					<div class="col-md-10 action-footer">
					<button type="button" class="btn btn-success btn-preview" onclick="preview('${id_jadwal_lokal}')">Preview</button>
					</div>
				</div>
				</div>
			</div>
			<div class="modal-preview p-3"></div>
			</div>
		</div>
		</div>`;


		jQuery("body .report").html(modal);
		get_jadwal_by_id(id_jadwal_lokal)
			.then(function(response) {
				jQuery(".modal-title").html("Laporan Jadwal " + response.data.nama)
				list_perangkat_daerah()
					.then(function() {
						jQuery("#modal-report").modal('show');
						jQuery('.jenis').select2({
							width: '100%'
						});
					})
			});
	}

	function preview(id_jadwal_lokal) {
		let jenis = jQuery("#jenis").val();
		let id_unit = jQuery("#list_perangkat_daerah").val();

		if (id_unit == '' || id_unit == 'undefined') {
			alert('Unit kerja belum dipilih');
			return;
		}

		switch (jenis) {
			case 'laporan_konsistensi_rpjm':
				let id_jadwal_rpjm = jQuery("#opt_jenis_jadwal").val() ?? '';
				window.open('<?php echo $url_laporan_konsistensi_rpjm; ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal + '&id_jadwal_rpjm=' + id_jadwal_rpjm, '_blank');
				break;
			case 'pagu_total':
				generate(id_unit, id_jadwal_lokal, 'view_pagu_total_renja', 'Laporan Pagu Akumulasi Per Unit Kerja');
				break;

			case 'renja_sipd_merah':
				window.open('<?php echo $this->add_param_get($url_renja_merah, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;

			case 'renja_sipd_ri':
				window.open('<?php echo $this->add_param_get($url_renja_ri, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;

			case 'analisis_belanja_program':
				window.open('<?php echo $this->add_param_get($url_analisis_belanja_program, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;

			case 'analisis_belanja_kegiatan':
				window.open('<?php echo $this->add_param_get($url_analisis_belanja_kegiatan, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;

			case 'analisis_belanja_sub_kegiatan':
				window.open('<?php echo $this->add_param_get($url_analisis_belanja_sub_kegiatan, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'analisis_belanja_bidang_urusan':
				window.open('<?php echo $this->add_param_get($url_analisis_belanja_bidang_urusan, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'analisis_belanja_sumber_dana':
				window.open('<?php echo $this->add_param_get($url_analisis_belanja_sumber_dana, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'analisis_belanja_rekening':
				window.open('<?php echo $this->add_param_get($url_analisis_belanja_rekening, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'rekap_sumber_dana_per_skpd':
				window.open('<?php echo $this->add_param_get($url_rekap_sumber_dana_per_skpd, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal + '&sumber_dana=' + jQuery('#opt_jenis_sumber_dana').val(), '_blank');
				break;
			case 'rekap_sumber_dana_per_program':
				window.open('<?php echo $this->add_param_get($url_rekap_sumber_dana_per_program, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal + '&sumber_dana=' + jQuery('#opt_jenis_sumber_dana').val(), '_blank');
				break;
			case 'rekap_sumber_dana_per_kegiatan':
				window.open('<?php echo $this->add_param_get($url_rekap_sumber_dana_per_kegiatan, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal + '&sumber_dana=' + jQuery('#opt_jenis_sumber_dana').val(), '_blank');
				break;
			case 'rekap_sumber_dana_per_sub_kegiatan':
				window.open('<?php echo $this->add_param_get($url_rekap_sumber_dana_per_sub_kegiatan, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal + '&sumber_dana=' + jQuery('#opt_jenis_sumber_dana').val(), '_blank');
				break;
			case 'rekap_sumber_dana_per_rekening':
				window.open('<?php echo $this->add_param_get($url_rekap_sumber_dana_per_rekening, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'rekap_longlist_per_jenis_belanja':
				window.open('<?php echo $this->add_param_get($rekap_longlist_per_jenis_belanja, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'apbd_penjabaran_lampiran_1':
				window.open('<?php echo $this->add_param_get($apbd_penjabaran_lampiran_1, '&1=1'); ?>' + '&id_skpd=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'apbd_penjabaran_lampiran_2':
				window.open('<?php echo $this->add_param_get($apbd_penjabaran_lampiran_2, '&1=1'); ?>' + '&id_skpd=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'apbd_perda_lampiran_1':
				window.open('<?php echo $this->add_param_get($apbd_perda_lampiran_1, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'apbd_perda_lampiran_2':
				window.open('<?php echo $this->add_param_get($apbd_perda_lampiran_2, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'apbd_perda_lampiran_3':
				window.open('<?php echo $this->add_param_get($apbd_perda_lampiran_3, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'apbd_perda_lampiran_4':
				window.open('<?php echo $this->add_param_get($apbd_perda_lampiran_4, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'apbd_perda_lampiran_5':
				window.open('<?php echo $this->add_param_get($apbd_perda_lampiran_5, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'apbd_perda_lampiran_7':
				window.open('<?php echo $apbd_perda_lampiran_7; ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'apbd_perda_lampiran_8':
				window.open('<?php echo $apbd_perda_lampiran_8; ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;
			case 'rekap_longlist_per_jenis_belanja_all_skpd':
				window.open('<?php echo $this->add_param_get($rekap_longlist_per_jenis_belanja_all_skpd, '&1=1'); ?>' + '&id_unit=' + id_unit + '&id_jadwal_lokal=' + id_jadwal_lokal, '_blank');
				break;

			case '-':
				alert('Jenis laporan belum dipilih');
				break;
		}
	}

	function generate(id_unit, id_jadwal_lokal, action, title) {
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url: ajax.url,
			type: 'post',
			dataType: 'json',
			data: {
				action: action,
				id_unit: id_unit,
				id_jadwal_lokal: id_jadwal_lokal,
				tahun_anggaran: tahun_anggaran,
				api_key: jQuery("#api_key").val(),
			},
			success: function(response) {
				if (response.status == 'error') {
					alert(response.message);
				} else {
					jQuery("#modal-report .modal-preview").html(response.html);
					jQuery("#modal-report .modal-preview").css('overflow-x', 'auto');
					jQuery("#modal-report .modal-preview").css('margin-right', '15px');
					jQuery("#modal-report .modal-preview").css('padding', '15px');
					jQuery('#modal-report .export-excel').attr("disabled", false);
					jQuery('#modal-report .export-excel').attr("title", title);

					window.table_renja = jQuery("#table-renja").DataTable({
						dom: 'Blfrtip',
						lengthMenu: [
							[10, 25, 50, -1],
							[10, 25, 50, 'All'],
						],
						buttons: [
							'excel'
						]
					});
					jQuery('#modal-report .action-footer .dt-buttons').remove();
					jQuery('#modal-report .action-footer').html(
						"<button type=\"button\" class=\"btn btn-success btn-preview\" onclick=\"preview('" + id_jadwal_lokal + "')\">Preview</button>");
					jQuery('#modal-report .action-footer').append(table_renja.buttons().container());
					jQuery('#modal-report .action-footer .dt-buttons').css('margin-left', '5px');
					jQuery('#modal-report .action-footer .buttons-excel').addClass('btn btn-primary');
					jQuery('#modal-report .action-footer .buttons-excel span').html('Export Excel');

					window.table_renja_pendapatan = jQuery("#table-renja-pendapatan").DataTable({
						lengthMenu: [
							[10, 25, 50, -1],
							[10, 25, 50, 'All'],
						]
					});

					window.table_renja_penerimaan = jQuery("#table-renja-penerimaan").DataTable({
						lengthMenu: [
							[10, 25, 50, -1],
							[10, 25, 50, 'All'],
						]
					});

					window.table_renja_pengeluaran = jQuery("#table-renja-pengeluaran").DataTable({
						lengthMenu: [
							[10, 25, 50, -1],
							[10, 25, 50, 'All'],
						]
					});
				}
				jQuery("#wrap-loading").hide();
			}
		})
	}

	function list_perangkat_daerah() {
		return new Promise(function(resolve, reject) {
			if (typeof list_perangkat_daerah_global == 'undefined') {
				jQuery('#wrap-loading').show();
				jQuery.ajax({
					url: ajax.url,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'list_perangkat_daerah',
						tahun_anggaran: tahun_anggaran,
						api_key: jQuery("#api_key").val(),
					},
					success: function(response) {
						jQuery('#wrap-loading').hide();
						if (response.status) {
							list_perangkat_daerah_global = response.list_skpd_options;
							jQuery("#list_perangkat_daerah").html(list_perangkat_daerah_global);
							jQuery('#list_perangkat_daerah').select2({
								width: '100%'
							});
							return resolve();
						}

						alert('Oops ada kesalahan load data Unit kerja');
						return resolve();
					}
				});
			} else {
				jQuery("#list_perangkat_daerah").html(list_perangkat_daerah_global);
				jQuery('#list_perangkat_daerah').select2({
					width: '100%'
				});
				return resolve();
			}
		})
	}

	function cek_pemutakhiran() {
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
			type: 'post',
			dataType: 'json',
			data: {
				action: 'cek_pemutakhiran_total',
				tahun_anggaran: tahun_anggaran,
				api_key: jQuery("#api_key").val(),
			},
			success: function(response) {
				jQuery('#wrap-loading').hide();
				if (response.status == 'success') {
					var total_sub_keg = 0;
					var total_sumber_dana = '-';
					response.sub_keg.map(function(b, i) {
						if (b.jml >= 1) {
							var row = table_renja.row('[data-idskpd=' + b.id_sub_skpd + ']');
							var index_row = row.index();
							var index_td = 1;
							var new_data = row.data();
							var nama_skpd = new_data[index_td].split(' <span')[0];
							new_data[index_td] = nama_skpd + ' <span class="notif badge badge-danger">' + b.jml + '</span>';
							table_renja.row(index_row).data(new_data).draw();
							jQuery(row.node()).find('>td').eq(1).css('background', '#f9d9d9');

							total_sub_keg += +(b.jml);
						}
					});
					var html = '' +
						'<h4 class="text-center">Data rekapitulasi pemutakhiran</h4>' +
						'<table class="table table-bordered">' +
						'<thead>' +
						'<tr>' +
						'<th class="text-center">Sub Kegiatan</th>' +
						'<th class="text-center">Sumber Dana</th>' +
						'<th class="text-center">Rekening Belanja</th>' +
						'<th class="text-center">Pendapatan</th>' +
						'<th class="text-center">Pembiayaan Penerimaan</th>' +
						'<th class="text-center">Pembiayaan Pengeluaran</th>' +
						'<th class="text-center">Komponen Belanja</th>' +
						'</tr>' +
						'</thead>' +
						'<tbody>' +
						'<tr>' +
						'<td class="text-center">' + total_sub_keg + '</td>' +
						'<td class="text-center">' + total_sumber_dana + '</td>' +
						'<td class="text-center">' + total_sumber_dana + '</td>' +
						'<td class="text-center">' + total_sumber_dana + '</td>' +
						'<td class="text-center">' + total_sumber_dana + '</td>' +
						'<td class="text-center">' + total_sumber_dana + '</td>' +
						'<td class="text-center">' + total_sumber_dana + '</td>' +
						'</tr>' +
						'</tbody>' +
						'</table>';
					jQuery('#tabel-pemutakhiran-belanja').html(html);
					return;
				}
				alert(response.message);
			}
		});
	}

	function set_setting_pergeseran(that) {
		let pergeseran = jQuery("#pergeseran_renja").prop('checked');
		if (pergeseran) {
			jQuery(".class_renja_pergeseran").show();
		} else {
			jQuery(".class_renja_pergeseran").hide();
		}
	}

	function jenis_laporan(that) {
		switch (jQuery(that).val()) {
			case 'rekap_sumber_dana_per_skpd':
			case 'rekap_sumber_dana_per_program':
			case 'rekap_sumber_dana_per_kegiatan':
			case 'rekap_sumber_dana_per_sub_kegiatan':
				jQuery('#opt_jenis_sumber_dana_div').show();
				break;
			case 'laporan_konsistensi_rpjm':
				jQuery('#wrap-loading').show();
				let option = '<option value="-">Pilih Jenis Jadwal</option>';
				jQuery.ajax({
					url: ajax.url,
					type: 'post',
					dataType: "json",
					data: {
						"action": "list_jadwal_rpjmd",
						"api_key": jQuery('#api_key').val(),
						"tipe": 8 // id_tipe rpjm sipd
					},
					success: function(response) {
						jQuery('#wrap-loading').hide();
						if (response.status) {
							response.data.map(function(value, index) {
								option += `<option value="${value.id_jadwal_lokal}">${value.nama}</option>`;
							});
						}
						jQuery("#opt_jenis_jadwal").html(option);
						jQuery("#opt_jenis_jadwal_div").show();
					}
				});
				break;

			default:
				jQuery("#opt_jenis_jadwal").html(null);
				jQuery("#opt_jenis_jadwal_div").hide();
				jQuery('#opt_jenis_sumber_dana_div').hide();
				jQuery('#opt_jenis_sumber_dana').val(1);
				break;
		}
	}

	function copy_renja_sipd_to_lokal_all() {
		if (confirm('Apakah anda yakin untuk melakukan ini? data RENJA lokal akan diupdate sama dengan data RENJA SIPD.')) {
			var copy_data_option = [];
			jQuery('input[name=copyDataSipd]:checked').each(function() {
				copy_data_option.push(jQuery(this).val());
			});

			var data_selected = [];
			jQuery('#table-list-skpd tbody td input[type="checkbox"]').map(function(i, b){
				if(jQuery(b).is(':checked')){
					data_selected.push(jQuery(b).val());
				}
			});
			if(data_selected.length == 0){
				return alert("Pilih OPD dulu!");
			}

			jQuery('#wrap-loading').show();
			jQuery('#persen-loading').attr('persen', 0);
			jQuery('#persen-loading').html('0%');
			var last = data_selected.length-1;
			data_selected.reduce(function(sequence, nextData){
				return sequence.then(function(id_skpd){
					return new Promise(function(resolve_reduce, reject_reduce){
						var pesan = 'Copy data SIPD ke lokal '+list_skpd[id_skpd].nama_skpd;
						console.log(pesan);
						jQuery('#pesan-loading').html(pesan);
						relayAjax({
							url: ajax.url,
							type: 'post',
							dataType: "json",
							data: {
								"action": "copy_renja_sipd_to_lokal",
								"api_key": jQuery('#api_key').val(),
								"tahun_anggaran": tahun_anggaran,
								"copy_data_option": copy_data_option,
								"id_skpd": id_skpd
							},
							success: function(res) {
								var c_persen = +jQuery('#persen-loading').attr('persen');
								c_persen++;
								jQuery('#persen-loading').attr('persen', c_persen);
								jQuery('#persen-loading').html(((c_persen/data_selected.length)*100).toFixed(2)+'%');
								resolve_reduce(nextData);
							},
							error: function(res) {
								console.log('Error ', id_skpd, res);
								resolve_reduce(nextData);
							}
						});
					})
					.catch(function(e){
						console.log(e);
						return Promise.resolve(nextData);
					});
				})
				.catch(function(e){
					console.log(e);
					return Promise.resolve(nextData);
				});
			}, Promise.resolve(data_selected[last]))
			.then(function(data_last){
				jQuery('#wrap-loading').hide();
				jQuery('#modal-copy-renja-sipd').modal('hide');
				return resolve();
			})
			.catch(function(e){
				console.log(e);
			});
		}else{
			alert(res.message);
			jQuery('#wrap-loading').hide();
		}
	}
</script>