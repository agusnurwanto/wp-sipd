<?php
global $wpdb;

if (!empty($_GET) && !empty($_GET['tahun']) && !empty($_GET['kode_sbl'])) {
	$tahun_anggaran = $_GET['tahun'];
	$kode_sbl = $_GET['kode_sbl'];
} else {
	die('<h1 class="text-center">Tahun Anggaran dan Kode Sub Kegiatan tidak boleh kosong!</h1>');
}

$namaJadwal = '-';
$mulaiJadwal = '-';
$selesaiJadwal = '-';
$timezone = get_option('timezone_string');
$cek_jadwal = $this->validasi_jadwal_perencanaan('verifikasi_rka_sipd',$tahun_anggaran);

$jadwal_lokal = $cek_jadwal['data'];
$setting_waktu = 0;
if(!empty($jadwal_lokal)){
	$tahun_anggaran = $jadwal_lokal[0]['tahun_anggaran'];
	$namaJadwal = $jadwal_lokal[0]['nama'];
	$mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
	$selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];

	$awal = new DateTime($mulaiJadwal);
	$akhir = new DateTime($selesaiJadwal);
	$now = new DateTime(date('Y-m-d H:i:s'));
	if($now >= $awal && $now <= $akhir){
		$setting_waktu = 1;
	}

}

$api_key = get_option('_crb_api_key_extension');
$user_id = um_user('ID');
$user_meta = get_userdata($user_id);

$roles = $this->role_verifikator();

$is_admin = false;
if (in_array("administrator", $user_meta->roles)) {
	$is_admin = true;
}

$is_verifikator = false;
foreach ($roles as $role) {
	if (in_array($role, $user_meta->roles)) {
		$is_verifikator = true;
	}
}

$data_rka = $wpdb->get_row($wpdb->prepare('
	SELECT 
		s.*,
		u.nama_skpd as nama_sub_skpd_asli,
		u.kode_skpd as kode_sub_skpd_asli,
		uu.nama_skpd as nama_skpd_asli,
		uu.kode_skpd as kode_skpd_asli
	FROM data_sub_keg_bl s
	INNER JOIN data_unit u on s.id_sub_skpd=u.id_skpd
		AND u.active=s.active
		AND u.tahun_anggaran=s.tahun_anggaran
	INNER JOIN data_unit uu on uu.id_skpd=u.id_unit
		AND uu.active=s.active
		AND uu.tahun_anggaran=s.tahun_anggaran
	WHERE s.kode_sbl = %s 
		AND s.active = 1
		AND s.tahun_anggaran = %d', $kode_sbl, $tahun_anggaran), ARRAY_A);

if ($data_rka) {
	$kode_sub_skpd = $data_rka['kode_sub_skpd_asli'];
	$nama_sub_skpd = $data_rka['nama_sub_skpd_asli'];
	$kode_skpd = $data_rka['kode_skpd_asli'];
	$nama_skpd = $data_rka['nama_skpd_asli'];
	$kode_program = $data_rka['kode_program'];
	$nama_program = $data_rka['nama_program'];
	$kode_kegiatan = $data_rka['kode_giat'];
	$nama_kegiatan = $data_rka['nama_giat'];
	$kode_bidang_urusan = $data_rka['kode_bidang_urusan'];
	$nama_sub_kegiatan = $data_rka['nama_sub_giat'];
	$nama_sub_kegiatan = str_replace('X.XX', $kode_bidang_urusan, $nama_sub_kegiatan);
	$pagu_kegiatan = number_format($data_rka['pagu'], 0, ",", ".");
	$sql = $wpdb->prepare("
		SELECT 
			d.iddana,
			d.namadana,
			m.kode_dana
		FROM data_dana_sub_keg d
			left join data_sumber_dana m on d.iddana=m.id_dana
				and d.tahun_anggaran = m.tahun_anggaran
		WHERE kode_sbl=%s
			AND d.tahun_anggaran=%d
			AND d.active=1", $kode_sbl, $tahun_anggaran);
	$sd_sub_keg = $wpdb->get_results($sql, ARRAY_A);
	$sd_sub = array();
	foreach ($sd_sub_keg as $key => $sd) {
		$new_sd = explode(' - ', $sd['namadana']);
		if (!empty($new_sd[1])) {
			$sd_sub[] = '<span class="kode-dana">' . $sd['kode_dana'] . '</span> ' . $new_sd[1];
		}
	}
} else {
	die('<h1 class="text-center">Sub Kegiatan tidak ditemukan!</h1>');
}

$result_verifikasi = $wpdb->get_results($wpdb->prepare("
	SELECT 
		nama_bidang,
		update_at,
		id_user
	FROM data_validasi_verifikasi_rka 
	WHERE tahun_anggaran =%d
		AND kode_sbl =%s
", $tahun_anggaran, $kode_sbl), ARRAY_A); 	

$html_ver = '';
foreach ($result_verifikasi as $ver) {
	$nama = get_userdata($ver['id_user']);
	$html_ver .= "
		<tr>
			<td style='text-transform: uppercase;'>$ver[nama_bidang]</td>
			<td>:</td>
			<td>Diverifikasi oleh $nama->display_name pada $ver[update_at]</td>
		</tr>
	";
}
$api_key = get_option('_crb_api_key_extension');

$current_user = wp_get_current_user();
$fokus_uraian = get_user_meta($current_user->ID, 'fokus_uraian', true);
$fokus_uraian_values = $fokus_uraian ? explode('|', $fokus_uraian) : array();
$nama_user = $current_user->display_name;
$id_user = $current_user->ID;

$user_pptk = 'User PPTK belum disetting!';
$pptk_sub_keg = $wpdb->get_row($wpdb->prepare("
	SELECT
		p.*
	FROM data_pptk_sub_keg p
	WHERE active=1
		and tahun_anggaran=%d
		and kode_sbl=%s
", $tahun_anggaran, $kode_sbl), ARRAY_A);
if (!empty($pptk_sub_keg)) {
	$nama = get_userdata($pptk_sub_keg['id_user']);
	$user_pptk = $nama->display_name;
}

$nama_page = $tahun_anggaran . ' | ' . $kode_sub_skpd . ' | ' . $kode_kegiatan . ' | ' . $nama_kegiatan;
$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
$link_kegiatan = $this->get_link_post($custom_post);

$sumber_pagu_dpa = get_option('_crb_default_sumber_pagu_dpa');
$url_nilai_dpa = '&pagu_dpa=simda';
if($sumber_pagu_dpa == 2){
	$url_nilai_dpa = '&pagu_dpa=fmis';
}
$nama_page = 'RFK '.$nama_sub_skpd.' '.$kode_sub_skpd.' | '.$tahun_anggaran;
$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
$url_rfk = $this->get_link_post($custom_post).$url_nilai_dpa;
?>
<style>
	#tabel_detail_sub,
	#tabel_detail_sub td,
	#tabel_detail_sub th {
		border: 0;
	}

	#tabel_verifikasi {
		border-collapse: collapse;
		width: 100%;
	}

	#tabel_verifikasi th,
	#tabel_verifikasi td {
		border: 1px solid black;
		padding: 8px;
		text-align: left;
	}

	.aksi {
		text-align: center;
		vertical-align: middle;
	}
</style>
<div style="padding: 15px;">
	<h1 class="text-center">LEMBAR ASISTENSI RKA SKPD<br>TAHUN ANGGARAN <?php echo $tahun_anggaran ?></h1>
	<table id='tabel_detail_sub'>
		<tbody>
			<tr>
				<td>ORGANISASI</td>
				<td>:</td>
				<td><?php echo $kode_skpd . '  ' . $nama_skpd ?></td>
			</tr>
			<tr>
				<td>UNIT</td>
				<td>:</td>
				<td><?php echo $kode_sub_skpd . '  ' . $nama_sub_skpd ?></td>
			</tr>
			<tr>
				<td>PROGRAM</td>
				<td>:</td>
				<td><?php echo $kode_program . '  ' . $nama_program ?></td>
			</tr>
			<tr>
				<td>KEGIATAN</td>
				<td>:</td>
				<td><?php echo $kode_kegiatan . '  ' . $nama_kegiatan ?></td>
			</tr>
			<tr>
				<td>SUB KEGIATAN</td>
				<td>:</td>
				<td><?php echo $nama_sub_kegiatan ?></td>
			</tr>
			<tr>
				<td>PAGU SUB KEGIATAN</td>
				<td>:</td>
				<td><?php echo "Rp. " . $pagu_kegiatan ?></td>
			</tr>
			<tr>
				<td>SUMBER DANA</td>
				<td>:</td>
				<td><?php echo implode(', ', $sd_sub); ?></td>
			</tr>
			<?php echo $html_ver; ?>
		</tbody>
	</table>

	<div id="aksi_page" class="text-center aksi" style="margin-bottom: 10px;">
		<?php
		if ($is_verifikator AND $setting_waktu==1) {
			echo '<button class="btn btn-sm btn-warning" onclick="tambah_catatan()" style="margin-inline: 5px;"><i class="dashicons dashicons-admin-comments"></i> Tambah Catatan</button>';
			echo '<button class="btn btn-sm btn-success" onclick="verifikasi_tanpa_catatan()"><i class="dashicons dashicons-yes"></i> Verifikasi Tanpa Catatan</button>';
		}
		?>
		<button class="btn btn-sm btn-info" onclick="jQuery('.aksi').hide(); window.print(); setTimeout(function(){ jQuery('.aksi').show(); }, 10000);"><i class="dashicons dashicons-printer"></i> Print Lembar Verifikasi</button>
		<a href="<?php echo $url_rfk; ?>" target="_blank" class="btn btn-sm btn-secondary">
			<span class="dashicons dashicons-media-default"></span>Kembali Ke Halaman RFK
		</a>
		<a class="btn btn-sm btn-secondary" href="<?php echo $link_kegiatan; ?>" target="_blank">
			<span class="dashicons dashicons-media-document"></span> Kembali Ke Halaman RKA
		</a>
	</div>

	<table id="tabel_verifikasi">
		<thead>
			<tr>
				<th class="text-center" style="vertical-align: middle;" rowspan="2">URAIAN</th>
				<th class="text-center" style="width :500px" colspan="2">CATATAN TIM ASISTENSI</th>
				<th class="text-center" style="vertical-align: middle; width: 300px;" rowspan="2">TANGGAPAN OPD</th>
				<th class="text-center aksi" style="vertical-align: middle; width: 50px;" rowspan="2">AKSI</th>
			</tr>
			<tr>
				<th class="text-center">Komentar</th>
				<th class="text-center" style="width: 200px;">Tim</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_tambah_catatan" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalScrollableTitle">Tambah Catatan Verifikasi</h5>
				<h5 class="modal-title" id="exampleModalScrollableTitle_pptk">Tambah Tanggapan Verifikasi</h5>
				<h5 class="modal-title" id="exampleModalScrollableTitle_edit">Edit Catatan Verifikasi</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" class="form-control" id="kode_sbl">
				<input type="hidden" class="form-control" id="id_user">
				<input type="hidden" class="form-control" id="tahun_anggaran">
				<input type="hidden" class="form-control" id="id_catatan">
				<input type="hidden" class="form-control" id="nama_bidang">
				<input type="hidden" id="user_pptk_status" value="<?php echo $user_pptk; ?>">
				<div class="form-group">
					<label>Sub Kegiatan</label>
					<input type="text" class="form-control" id="sub_kegiatan" value="" disabled>
				</div>
				<div class="form-group">
					<label>Nama Verifikator</label>
					<input type="text" class="form-control" id="nama_verifikator" disabled>
				</div>
				<div class="form-group">
					<label>Fokus Uraian</label>
					<select class="form-control" id="fokus_uraian">
						<option value="" selected disabled>Pilih Salah Satu</option>

						<?php foreach ($fokus_uraian_values as $value) : ?>
							<option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($value); ?></option>
						<?php endforeach; ?>

						<?php if (empty($fokus_uraian_values)) : ?>
							<option value="" disabled>Tidak ada nilai fokus uraian ditemukan.</option>
						<?php endif; ?>
					</select>
				</div>
				<div class="form-group">
					<label>Catatan Verifikasi</label>
					<textarea class="form-control" id="catatan_verifikasi" required value=""></textarea>
				</div>
				<div class="form-group">
					<label>Nama PPTK OPD</label>
					<input type="text" class="form-control" id="nama_pptk" disabled value="<?php echo $user_pptk; ?>">
				</div>
				<div class="form-group">
					<label>Tanggapan terhadap catatan</label>
					<textarea class="form-control" id="tanggapan_verifikasi" required value=""></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
				<button type="button" class="btn btn-primary" id="submit_data" onclick="submit_data(this)">Simpan</button>
				<button type="button" class="btn btn-primary" id="submit_data_tanggapan" onclick="submit_tanggapan(this)" <?php echo ($user_pptk == 'User PPTK belum disetting!') ? 'disabled' : ''; ?>>Simpan Tanggapan</button>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function() {
		load_data();

		var mySpace = '<div style="padding:3rem;"></div>';
    	jQuery('body').prepend(mySpace);

        // set_waktu();
    	var dataHitungMundur = {
    		'namaJadwal' : '<?php echo ucwords($namaJadwal)  ?>',
    		'mulaiJadwal' : '<?php echo $mulaiJadwal  ?>',
    		'selesaiJadwal' : '<?php echo $selesaiJadwal  ?>',
    		'thisTimeZone' : '<?php echo $timezone ?>'
    	}
    	penjadwalanHitungMundur(dataHitungMundur);
	});

	function load_data() {
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			type: 'POST',
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {
				api_key: '<?php echo $api_key; ?>',
				action: 'get_data_verifikasi_rka',
				kode_sbl: '<?php echo $kode_sbl; ?>',
				tahun_anggaran: <?php echo $tahun_anggaran; ?>
			},
			success: function(data) {
				jQuery('#wrap-loading').hide();
				const response = JSON.parse(data);
				if (response.status === 'success') {
					jQuery('#tabel_verifikasi > tbody').html(response.html);
					jQuery('#tabel_detail_sub');
				} else {
					alert('Error: ' + response.message);
				}
			}

		});
	}

	function verifikasi_tanpa_catatan() {
		if (confirm('Apakah anda yakin ingin memverifikasi tanpa catatan?')) {
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					api_key: '<?php echo $api_key; ?>',
					kode_sbl: '<?php echo $kode_sbl ?>',
					tahun_anggaran: '<?php echo $tahun_anggaran ?>',
					action: 'verifikasi_tanpa_catatan'
				},
				success: function(data) {
					jQuery('#wrap-loading').hide();
					const response = JSON.parse(data);
					if (response.status === 'success') {
						alert(response.message);
						jQuery('#modal_tambah_catatan').modal('hide');
						load_data();
					} else {
						alert('Error: ' + response.message);
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error:', status, error);
				}
			});
		}

	}

	function tambah_tanggapan(id) {
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			method: 'post',
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			dataType: 'json',
			data: {
				'action': 'get_catatan_verifikasi_by_id',
				'api_key': '<?php echo $api_key; ?>',
				'id': id,
			},
			success: function(res) {
				if (res.status == 'success') {
					jQuery('#kode_sbl').val('<?php echo $kode_sbl ?>').prop('disabled', true);
					jQuery('#tahun_anggaran').val('<?php echo $tahun_anggaran; ?>').prop('disabled', true);
					jQuery('#id_catatan').val(res.data.id).prop('disabled', true);

					jQuery('#sub_kegiatan').val('<?php echo $nama_sub_kegiatan; ?>').prop('disabled', true);
					jQuery('#fokus_uraian').val('').closest('.form-group').hide().prop('disabled', true);
					jQuery('#nama_verifikator').val('').closest('.form-group').hide().prop('disabled', true);
					jQuery('#catatan_verifikasi').val('').closest('.form-group').hide().prop('disabled', true);
					jQuery('#exampleModalScrollableTitle').hide();
					jQuery('#exampleModalScrollableTitle_edit').hide();
					jQuery('#submit_data').hide();

					jQuery('#nama_pptk').prop('disabled', true).closest('.form-group').show();
					jQuery('#tanggapan_verifikasi').val(res.data.tanggapan_opd).prop('disabled', false).closest('.form-group').show();
					jQuery('#exampleModalScrollableTitle_pptk').show();
					jQuery('#submit_data_tanggapan').show();
					jQuery('#modal_tambah_catatan').modal('show');
				} else {
					alert(res.message);
				}
				jQuery('#wrap-loading').hide();
			}
		});
	}

	function tambah_catatan() {
		jQuery('#kode_sbl').val('<?php echo $kode_sbl ?>').prop('disabled', true);
		jQuery('#tahun_anggaran').val('<?php echo $tahun_anggaran; ?>').prop('disabled', true);
		jQuery('#id_catatan').val('').prop('disabled', true);
		jQuery('#id_user').val('<?php echo $id_user; ?>').prop('disabled', true);

		jQuery('#sub_kegiatan').val('<?php echo $nama_sub_kegiatan; ?>').prop('disabled', true).closest('.form-group').show();
		jQuery('#nama_verifikator').val('<?php echo $nama_user; ?>').prop('disabled', true).closest('.form-group').show();
		jQuery('#fokus_uraian').val('').prop('disabled', false).closest('.form-group').show();
		jQuery('#catatan_verifikasi').val('').prop('disabled', false).closest('.form-group').show();
		jQuery('#exampleModalScrollableTitle').show();
		jQuery('#exampleModalScrollableTitle_edit').hide();
		jQuery('#submit_data').show();

		jQuery('#nama_pptk').prop('disabled', true).closest('.form-group').hide();
		jQuery('#tanggapan_verifikasi').val('').prop('disabled', true).closest('.form-group').hide();
		jQuery('#exampleModalScrollableTitle_pptk').hide();
		jQuery('#submit_data_tanggapan').hide();
		jQuery('#modal_tambah_catatan').modal('show');
	}

	function submit_data(that) {
		jQuery('#wrap-loading').show();
		const kode_sbl = jQuery('#kode_sbl').val();
		const tahun_anggaran = jQuery('#tahun_anggaran').val();
		const id_catatan = jQuery('#id_catatan').val();
		const fokus_uraian = jQuery('#fokus_uraian').val();
		const catatan_verifikasi = jQuery('#catatan_verifikasi').val();

		jQuery.ajax({
			type: 'POST',
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {
				api_key: '<?php echo $api_key; ?>',
				kode_sbl: kode_sbl,
				tahun_anggaran: tahun_anggaran,
				id_catatan: id_catatan,
				fokus_uraian: fokus_uraian,
				catatan_verifikasi: catatan_verifikasi,
				action: 'tambah_catatan_verifikator',
			},
			success: function(data) {
				jQuery('#wrap-loading').hide();
				const response = JSON.parse(data);
				if (response.status === 'success') {
					alert(response.message);
					jQuery('#modal_tambah_catatan').modal('hide');
					load_data();
				} else {
					alert('Error: ' + response.message);
				}
			},
			error: function(xhr, status, error) {
				console.error('AJAX Error:', status, error);
			}
		});
	}

	function submit_tanggapan(that) {
		const id_catatan = jQuery('#id_catatan').val();
		const tanggapan_verifikasi = jQuery('#tanggapan_verifikasi').val();
		let userPptkStatus = jQuery('#user_pptk_status').val();
		if (userPptkStatus === 'User PPTK belum disetting!') {
			return alert('Harap set user PPTK pada halaman set user PPTK');
		}

		jQuery('#wrap-loading').show();
		jQuery.ajax({
			type: 'POST',
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {
				api_key: '<?php echo $api_key; ?>',
				tanggapan_verifikasi: tanggapan_verifikasi,
				action: 'tambah_data_tanggapan',
				'id_catatan': id_catatan,
				'user_pptk_status': userPptkStatus
			},
			success: function(data) {
				jQuery('#wrap-loading').hide();
				const response = JSON.parse(data);
				if (response.status === 'success') {
					alert(response.message);
					jQuery('#modal_tambah_catatan').modal('hide');
					load_data();
				} else {
					alert('Error: ' + response.message);
				}
			},
			error: function(xhr, status, error) {
				console.error('AJAX Error:', status, error);
			}
		});
	}

	function delete_data(id) {
		let confirmDelete = confirm("Apakah anda yakin akan menghapus catatan ini?");
		if (confirmDelete) {
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				type: 'post',
				data: {
					'action': 'hapus_catatan_verifikasi',
					'api_key': '<?php echo $api_key; ?>',
					'id': id
				},
				dataType: 'json',
				success: function(response) {
					jQuery('#wrap-loading').hide();
					if (response.status == 'success') {
						load_data();
						alert(response.message);
					} else {
						alert(`GAGAL! \n${response.message}`);
					}
				}
			});
		}
	}

	function edit_data(id) {
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			method: 'post',
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			dataType: 'json',
			data: {
				'action': 'get_catatan_verifikasi_by_id',
				'api_key': '<?php echo $api_key; ?>',
				'id': id,
			},
			success: function(res) {
				if (res.status == 'success') {
					jQuery('#kode_sbl').val(res.data.kode_sbl).prop('disabled', true);
					jQuery('#tahun_anggaran').val(res.data.tahun_anggaran).prop('disabled', true);
					jQuery('#id_catatan').val(res.data.id).prop('disabled', true);
					jQuery('#id_user').val(res.data.id_user).prop('disabled', true);
					jQuery('#sub_kegiatan').val('<?php echo $nama_sub_kegiatan; ?>').prop('disabled', true).closest('.form-group').show();
					jQuery('#nama_verifikator').val(res.data.nama_verifikator).prop('disabled', true).closest('.form-group').show();
					jQuery('#fokus_uraian').val('').val(res.data.fokus_uraian).closest('.form-group').show();
					jQuery('#catatan_verifikasi').val(res.data.catatan_verifikasi).closest('.form-group').show();
					jQuery('#exampleModalScrollableTitle_edit').show();

					jQuery('#exampleModalScrollableTitle').hide();
					jQuery('#nama_pptk').prop('disabled', true).closest('.form-group').hide();
					jQuery('#exampleModalScrollableTitle_pptk').hide();
					jQuery('#tanggapan_verifikasi').val('').prop('disabled', true).closest('.form-group').hide();
					jQuery('#modal_tambah_catatan').modal('show');
				} else {
					alert(res.message);
				}
				jQuery('#wrap-loading').hide();
			}
		});
	}
</script>