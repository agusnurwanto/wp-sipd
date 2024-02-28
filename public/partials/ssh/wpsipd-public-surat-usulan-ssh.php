<?php
// If this file is called directly, abort.

require_once WPSIPD_PLUGIN_PATH . "/public/trait/CustomTrait.php";

global $wpdb;

if (!defined('WPINC')) {
	die;
}

$input = shortcode_atts(array(
	'id_surat' => '0',
	'tahun_anggaran' => get_option('_crb_tahun_anggaran_sipd')
), $atts);

if (empty($input['id_surat'])) {
	die('<h1>ID Surat tidak boleh kosong!</h1>');
}

if (empty($_GET['idskpd'])) {
	die('<h1>Unit kerja tidak boleh kosong!</h1>');
}

$id_skpd = $wpdb->prepare('%d', $_GET['idskpd']);

$skpd = $wpdb->get_row($wpdb->prepare("
		SELECT
			namaunit, 
			namakepala, 
			nipkepala, 
			pangkatkepala 
		FROM data_unit
		WHERE id_skpd=%d
			and tahun_anggaran=%d
			and active=1
", $id_skpd, $input['tahun_anggaran']));
if (empty($skpd)) {
	die('<h1>Unit kerja tidak ditemukan!</h1>');
}

$skpd_admin_ssh = get_option('_crb_skpd_admin_ssh');
if (empty($skpd_admin_ssh)) {
	die('<h1>Unit kerja penyusun Standar Harga belum disetting!</h1>');
}
$skpd_penyusun = $wpdb->get_var($wpdb->prepare("
		SELECT
			nama_skpd
		FROM data_unit
		WHERE id_skpd=%d
			and tahun_anggaran=%d
			and active=1
", $skpd_admin_ssh, $input['tahun_anggaran']));

$ssh = $wpdb->get_results($wpdb->prepare("
	SELECT
		h.*,
		s.jenis_survey,
		s.jenis_juknis,
		s.update_at as waktu_surat
	FROM data_ssh_usulan as h
	RIGHT JOIN data_surat_usulan_ssh as s on s.nomor_surat=h.no_surat_usulan
		AND s.tahun_anggaran=h.tahun_anggaran
	WHERE s.id=%d
", $input['id_surat']), ARRAY_A);

$body_html = "";

if (empty($ssh)) {
	die('<h1>ID Surat ' . $input['id_surat'] . ' tidak ditemukan!</h1>' . $wpdb->last_query);
}

foreach ($ssh as $k => $val) {
	$no = $k + 1;
	$akun_belanja = "<ul style='margin-bottom: 0; margin-left: 20px;'>";
	$akun_db = $wpdb->get_results("
		SELECT
			kode_akun,
			nama_akun
		FROM data_ssh_rek_belanja_usulan
		WHERE id_standar_harga=$val[id]
	", ARRAY_A);
	foreach ($akun_db as $kk => $akun) {
		$akun_belanja .= "<li>$akun[nama_akun]</li>";
	}
	$akun_belanja .= "</ul>";
	$body_html .= '
	<tr>
		<td>' . $no . '</td>
		<td>' . $val['kode_kel_standar_harga'] . '</td>
		<td>' . $val['nama_kel_standar_harga'] . '</td>
		<td>' . $val['nama_standar_harga'] . '</td>
		<td>' . $val['spek'] . '</td>
		<td>' . $val['satuan'] . '</td>
		<td class="text-right">' . number_format($val['harga'], 2, ",", ".",) . '</td>
		<td>' . $akun_belanja . '</td>
		<td>' . $val['keterangan_lampiran'] . '</td>
	</tr>
	';
}

$sambung = '';
if (!empty($ssh[0]['jenis_survey']) && !empty($ssh[0]['jenis_juknis'])) {
	$sambung = ' dan/atau ';
}

$type = '';
if (!empty($ssh[0]['jenis_survey']) && $ssh[0]['jenis_survey'] == 1) {
	$type .= 'berdasarkan survei harga pasar yang telah kami lakukan secara mandiri' . $sambung;
}

if (!empty($ssh[0]['jenis_juknis']) && $ssh[0]['jenis_juknis'] == 2) {
	$type .= 'berdasarkan petunjuk teknis yang kami terima dari ....... (Kementerian/Provinsi)';
}

$waktu_surat = $this->tanggalan(date("Y-m-d", strtotime($ssh[0]['waktu_surat'])));
$alamat = get_option('_crb_skpd_alamat_' . $id_skpd);
if (empty($alamat)) {
	$alamat = 'Isi sesuai alamat unit kerja';
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

	.body-surat p {
		text-align: justify;
		text-indent: 50px;
	}
</style>
<div class="cetak">
	<div style="padding: 10px;">
		<div class="surat-usulan break-print" id="content" contenteditable="true">
			<div class="kop-surat row">
				<div class="col-3 tengah">
					<img src="<?php
								if (!empty(get_option('_crb_logo_dashboard'))) {
									echo get_option('_crb_logo_dashboard');
								} else {
									echo 'http://via.placeholder.com/350x350';
								}
								?>" width="70%">
				</div>
				<div class="col-sm-9">
					<h4 class="tengah" style="text-transform: uppercase;">Pemerintah <?php echo get_option('_crb_daerah') ?></h4>
					<h2 class="tengah jarak-atas"><?php echo $skpd->namaunit ?></h2>
					<div class="tengah jarak-atas" contenteditable="true">
						<span class="alamat"><?php echo $alamat; ?></span>
					</div>
				</div>
			</div>
			<div class="no-surat text-center row">
				<div class="col-sm-12">
					<hr style="border: 1px solid;">
					<b>SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK</b>
					<p>Nomor: <?php echo $ssh[0]['no_surat_usulan']; ?></p>
				</div>
			</div>
			<div class="body-surat row">
				<div class="col-sm-12">
					<p>Yang bertanda tangan di bawah ini saya selaku Kepala <?php echo $skpd->namaunit ?> <?php echo get_option('_crb_daerah') ?> menyatakan dengan sesungguhnya bertanggung jawab penuh atas usulan Standar Harga Satuan yang terlampir pada surat kami kepada Kepala <?php echo $skpd_penyusun; ?> <?php echo get_option('_crb_daerah') ?> tanggal <?php echo $waktu_surat; ?>, nomor: <?php echo $ssh[0]['no_surat_usulan']; ?>, <?php echo $type; ?>.</p>
					<p>Kami siap menyajikan data referensi harga barang/jasa atas Standar Harga Satuan yang kami usulkan jika sewaktu-waktu dibutuhkan.</p>
					<p>Demikian Surat Pernyataan ini dibuat dengan sebenar-benarnya.</p>
				</div>
			</div>
			<div class="ttd-surat row">
				<div class="col-sm-6"></div>
				<div class="col-sm-6 text-center">
					<p><?php echo get_option('_crb_lokasi'); ?>, <?php echo $waktu_surat; ?><br>Kepala <?php echo $skpd->namaunit ?><br><?php echo get_option('_crb_daerah') ?></p><br><br><br><br>
					<p><?php echo $skpd->namakepala ?><br><?php echo $skpd->pangkatkepala ?><br>NIP : <?php echo $skpd->nipkepala ?></p>
				</div>
			</div>
		</div>
		<h1 class="text-center">Daftar Usulan Standar Harga</h1>
		<table id="surat_usulan_ssh_table" class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">NO</th>
					<th class="text-center">KODE KELOMPOK BARANG</th>
					<th class="text-center">NAMA KODE KELOMPOK BARANG</th>
					<th class="text-center">URAIAN</th>
					<th class="text-center">SPESIFIKASI</th>
					<th class="text-center">SATUAN</th>
					<th class="text-center">HARGA SATUAN</th>
					<th class="text-center">AKUN BELANJA</th>
					<th class="text-center">KETERANGAN</th>
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
			</thead>
			<tbody>
				<?php echo $body_html; ?>
			</tbody>
		</table>
		<div contenteditable="true">
			<div class="ttd-surat row">
				<div class="col-sm-8"></div>
				<div class="col-sm-4 text-center">
					<p><?php echo get_option('_crb_lokasi'); ?>, <?php echo $waktu_surat; ?><br>Kepala <?php echo $skpd->namaunit ?><br><?php echo get_option('_crb_daerah') ?></p><br><br><br><br>
					<p><?php echo $skpd->namakepala ?><br><?php echo $skpd->pangkatkepala ?><br>NIP : <?php echo $skpd->nipkepala ?></p>
				</div>
			</div>
		</div>
	</div>
</div>