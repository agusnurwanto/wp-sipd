<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
global $wpdb;

if (empty($_GET['tahun'])) {
	die('<h1>Tahun Anggaran tidak boleh kosong!</h1>');
} else if (empty($_GET['status'])) {
	die('<h1>Status tidak boleh kosong!</h1>');
}

$where = '';
$tahun_anggaran = $wpdb->prepare('%d', $_GET['tahun']);
$tipe_laporan = 1;
/*
	1 = Laporan upload SIPD
	2 = Laporan format WP-SIPD
*/
if (!empty($_GET['tipe_laporan'])) {
	$tipe_laporan = $_GET['tipe_laporan'];
}

$status = $_GET['status'];
if ($status == 'diterima') {
	$where .= " AND status = 'approved' ";
} else if ($status == 'ditolak') {
	$where .= " AND status = 'rejected' ";
} else if ($status == 'menunggu') {
	$where .= " AND status = 'waiting' ";
} else if ($status == 'sudah_upload_sipd') {
	$where .= " AND status_upload_sipd = '1' ";
} else if ($status == 'belum_upload_sipd') {
	$where .= " AND status_upload_sipd != '1' OR status_upload_sipd IS NULL ";
} else if ($status == 'diterima_admin') {
	$where .= " AND status_by_admin = 'approved' ";
} else if ($status == 'ditolak_admin') {
	$where .= " AND status_by_admin = 'rejected' ";
} else if ($status == 'diterima_tapdkeu') {
	$where .= " AND status_by_tapdkeu = 'approved' ";
} else if ($status == 'ditolak_tapdkeu') {
	$where .= " AND status_by_tapdkeu = 'rejected' ";
}

if (!empty($_GET['id_skpd'])) {
	$id_skpd = $wpdb->prepare('%d', $_GET['id_skpd']);
	$where .= " AND id_sub_skpd = " . $id_skpd;
}
if (!empty($_GET['no_surat'])) {
	$no_surat = $wpdb->prepare('%s', $_GET['no_surat']);
	$where .= " AND no_surat_usulan = " . $no_surat;
}
if (!empty($_GET['nota_dinas'])) {
	$nota_dinas = $wpdb->prepare('%s', $_GET['nota_dinas']);
	$where .= " AND no_nota_dinas = " . $nota_dinas;
}

$ssh = $wpdb->get_results($wpdb->prepare("
	SELECT
		h.*
	FROM data_ssh_usulan as h
	WHERE h.tahun_anggaran=%d
		and status_jenis_usulan != 'tambah_akun'
		$where
", $tahun_anggaran), ARRAY_A);

$body_html = "";

if (empty($ssh)) {
	echo "<span style='display: none'>$wpdb->last_query</span>";
	if ($tipe_laporan == 1) {
		$body_html .= "
		<tr>
			<td colspan='17' class='text-center'>Data tidak ditemukan!</td>
		</tr>
		";
	} else {
		$body_html .= "
		<tr>
			<td colspan='9' class='text-center'>Data tidak ditemukan!</td>
		</tr>
		";
	}
}

foreach ($ssh as $k => $val) {
	$no = $k + 1;
	$akun_belanja = "";
	if (!empty($val['kode_standar_harga_sipd'])) {
		$akun_db = $wpdb->get_results("
			SELECT
				r.kode_akun,
				r.nama_akun
			FROM data_ssh_rek_belanja r
			inner join data_ssh s on s.id_standar_harga=r.id_standar_harga 
			WHERE s.kode_standar_harga='$val[kode_standar_harga_sipd]'
		", ARRAY_A);
	} else {
		$akun_db = $wpdb->get_results("
			SELECT
				kode_akun,
				nama_akun
			FROM data_ssh_rek_belanja_usulan
			WHERE id_standar_harga=$val[id]
		", ARRAY_A);
	}
	if ($tipe_laporan == 1) {
		for ($i = 0; $i < 10; $i++) {
			if (!empty($akun_db[$i])) {
				$akun_belanja .= "<td>" . $akun_db[$i]['kode_akun'] . "</td>";
			} else {
				$akun_belanja .= "<td></td>";
			}
		}
		$body_html .= "
		<tr>
			<td>$val[kode_kel_standar_harga]</td>
			<td>$val[nama_standar_harga]</td>
			<td>$val[spek]</td>
			<td>$val[satuan]</td>
			<td>$val[harga]</td>
			$akun_belanja
			<td>$val[kelompok]</td>
			<td>$val[tkdn]</td>
		</tr>
		";
	} else if ($tipe_laporan == 2) {
		foreach ($akun_db as $kk => $akun) {
			$akun_belanja .= "<li>$akun[nama_akun]</li>";
		}
		$akun_belanja .= "</ul>";
		$nama_skpd = $wpdb->get_row("
			SELECT
				kode_skpd,
				nama_skpd
			FROM data_unit
			WHERE id_skpd=$val[id_sub_skpd]
		", ARRAY_A);
		$nama_skpd = $nama_skpd['kode_skpd'] . ' ' . $nama_skpd['nama_skpd'];
		$harga = $this->_number_format($val['harga']);
		$body_html .= "
		<tr>
			<td>$no</td>
			<td>$val[kode_kel_standar_harga]</td>
			<td>$val[nama_kel_standar_harga]</td>
			<td>$val[nama_standar_harga]</td>
			<td>$val[spek]</td>
			<td>$val[satuan]</td>
			<td class='text-right'>$harga</td>
			<td>$val[keterangan_lampiran]</td>
			<td>$nama_skpd</td>
		</tr>
		";
	} else if ($tipe_laporan == 3) {
		foreach ($akun_db as $kk => $akun) {
			$akun_belanja .= "<li>$akun[nama_akun]</li>";
		}
		$akun_belanja .= "</ul>";
		$nama_skpd = $wpdb->get_row("
			SELECT
				kode_skpd,
				nama_skpd
			FROM data_unit
			WHERE id_skpd=$val[id_sub_skpd]
		", ARRAY_A);
		$nama_skpd = $nama_skpd['kode_skpd'] . ' ' . $nama_skpd['nama_skpd'];
		$harga = $this->_number_format($val['harga']);
		$body_html .= "
		<tr>
			<td>$no</td>
			<td>$val[kode_kel_standar_harga]</td>
			<td>$val[nama_kel_standar_harga]</td>
			<td>$val[nama_standar_harga]</td>
			<td>$val[spek]</td>
			<td>$val[satuan]</td>
			<td class='text-right'>$harga</td>
			<td>$val[keterangan_lampiran]</td>
			<td>$nama_skpd</td>
			<td></td>
			<td></td>
		</tr>
		";
	}
}
?>
<style type="text/css">
	@media print {
		.break-print {
			page-break-after: always;
		}
	}

	.surat-usulan {
		width: 900px;
		margin: 0 auto 30px;
		font-size: 20px;
	}

	.tengah {
		text-align: center;
	}

	.jarak-atas {
		margin-top: -20px;
	}

	.alamat {
		text-align: center;
		font-size: 19px;
	}
</style>
<div id="cetak">
	<div style="padding: 10px;">
		<?php if ($tipe_laporan == 2) : ?>
			<h1 class="text-center">Daftar Usulan Standar Harga</h1>
		<?php endif; ?>
		<?php if ($tipe_laporan == 3) : ?>
			<h1 class="text-center">Lembar Verifikasi Usulan Standar Harga</h1>
		<?php endif; ?>
		<table id="surat_usulan_ssh_table" class="table table-bordered">
			<thead>
				<?php if ($tipe_laporan == 1) : ?>
					<tr>
						<th class="text-center">Kode</th>
						<th class="text-center">Uraian</th>
						<th class="text-center">Spek</th>
						<th class="text-center">Satuan</th>
						<th class="text-center">Harga</th>
						<th class="text-center">Rekening 1</th>
						<th class="text-center">Rekening 2</th>
						<th class="text-center">Rekening 3</th>
						<th class="text-center">Rekening 4</th>
						<th class="text-center">Rekening 5</th>
						<th class="text-center">Rekening 6</th>
						<th class="text-center">Rekening 7</th>
						<th class="text-center">Rekening 8</th>
						<th class="text-center">Rekening 9</th>
						<th class="text-center">Rekening 10</th>
						<th class="text-center">Kelompok</th>
						<th class="text-center">Nilai TKDN</th>
					</tr>
				<?php elseif ($tipe_laporan == 2) : ?>
					<tr>
						<th class="text-center">NO</th>
						<th class="text-center">KODE KELOMPOK BARANG</th>
						<th class="text-center">NAMA KODE KELOMPOK BARANG</th>
						<th class="text-center">URAIAN</th>
						<th class="text-center">SPESIFIKASI</th>
						<th class="text-center">SATUAN</th>
						<th class="text-center">HARGA SATUAN</th>
						<th class="text-center">KETERANGAN</th>
						<th class="text-center">SKPD Pengusul</th>
					</tr>
					<tr>
						<th class="text-center">1</th>
						<th class="text-center">2</th>
						<th class="text-center">3</th>
						<th class="text-center">4</th>
						<th class="text-center">5</th>
						<th class="text-center">6</th>
						<th class="text-center">7</th>
						<th class="text-center">8</th>
						<th class="text-center">9</th>
					</tr>
				<?php elseif ($tipe_laporan == 3) : ?>
					<tr>
						<th class="text-center">NO</th>
						<th class="text-center">KODE KELOMPOK BARANG</th>
						<th class="text-center">NAMA KODE KELOMPOK BARANG</th>
						<th class="text-center">URAIAN</th>
						<th class="text-center">SPESIFIKASI</th>
						<th class="text-center">SATUAN</th>
						<th class="text-center">HARGA SATUAN</th>
						<th class="text-center">KETERANGAN</th>
						<th class="text-center">SKPD Pengusul</th>
						<th class="text-center">Iya</th>
						<th class="text-center">Tidak</th>
					</tr>
					<tr>
						<th class="text-center">1</th>
						<th class="text-center">2</th>
						<th class="text-center">3</th>
						<th class="text-center">4</th>
						<th class="text-center">5</th>
						<th class="text-center">6</th>
						<th class="text-center">7</th>
						<th class="text-center">8</th>
						<th class="text-center">9</th>
						<th class="text-center">10</th>
						<th class="text-center">11</th>
					</tr>
				<?php endif; ?>

			</thead>
			<tbody>
				<?php echo $body_html; ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		run_download_excel();
	});
</script>