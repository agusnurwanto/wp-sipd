<?php
global $wpdb;

if (!empty($_GET) && !empty($_GET['tahun']) && !empty($_GET['kode_sbl'])) {
	$tahun_anggaran = $_GET['tahun'];
	$kode_sbl = $_GET['kode_sbl'];
} else {
	die('<h1 class="text-center">Tahun Anggaran dan Kode Sub Kegiatan tidak boleh kosong!</h1>');
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
	$pagu_kegiatan = number_format($data_rka['pagu'],0,",",".");
	$sql = $wpdb->prepare("
		SELECT 
			d.iddana,
			d.namadana,
			m.kode_dana
		from data_dana_sub_keg d
			left join data_sumber_dana m on d.iddana=m.id_dana
				and d.tahun_anggaran = m.tahun_anggaran
		where kode_sbl=%s
			AND d.tahun_anggaran=%d
			AND d.active=1", $kode_sbl, $tahun_anggaran);
	$sd_sub_keg = $wpdb->get_results($sql, ARRAY_A);
	$sd_sub = array();
	foreach ($sd_sub_keg as $key => $sd) {
		$new_sd = explode(' - ', $sd['namadana']);
		if(!empty($new_sd[1])){
			$sd_sub[] = '<span class="kode-dana">'.$sd['kode_dana'].'</span> '.$new_sd[1];
		}
	}
} else {
	die('<h1 class="text-center">Sub Kegiatan tidak ditemukan!</h1>');
}
?>
<style>
	#tabel_detail_sub, #tabel_detail_sub td, #tabel_detail_sub th {
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
</style>

<h1 class="text-center">LEMBAR ASISTENSI RKA SKPD<br>TAHUN ANGGARAN <?php echo $tahun_anggaran ?></h1>
<div class="container">
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
			<tr>
				<td>BAPPEDA LITBANG</td>
				<td>:</td>
				<td><?php echo "Diverifikasi oleh pada"  ?></td>
			</tr>
			<tr>
				<td>BPPKAD</td>
				<td>:</td>
				<td><?php echo "Diverifikasi oleh pada"  ?></td>
			</tr>
			<tr>
				<td>BAGIAN ADBANG</td>
				<td>:</td>
				<td><?php echo "Diverifikasi oleh pada"  ?></td>
			</tr>
			<tr>
				<td>DINAS PUPR</td>
				<td>:</td>
				<td><?php echo "Diverifikasi oleh pada"  ?></td>
			</tr>
			<tr>
				<td>BAGIAN PBJ</td>
				<td>:</td>
				<td><?php echo "Diverifikasi oleh pada"  ?></td>
			</tr>
			<tr>
				<td>INSPEKTORAT</td>
				<td>:</td>
				<td><?php echo "Diverifikasi oleh pada"  ?></td>
			</tr>
			<tr>
				<td>PPTK OPD</td>
				<td>:</td>
				<td><?php echo "Ditanggapi oleh pada"  ?></td>
			</tr>
		</tbody>
	</table>

	<table id="tabel_verifikasi">
		<thead>
			<tr>
				<th class="text-center" style="vertical-align: middle;" rowspan="2">URAIAN</th>
				<th class="text-center" style="width :250px" colspan="2">CATATAN TIM ASISTENSI</th>
				<th class="text-center" style="vertical-align: middle;" rowspan="2">CATATAN OPD</th>
			</tr>
			<tr>
				<th class="text-center">Komentar</th>
				<th class="text-center" style="width: 100px;">Tim</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th colspan="4">Indikator</th>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<th colspan="4">Kesesuaian</th>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<th colspan="4">Rekening</th>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<th colspan="4">Keseluruhan</th>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
</div>