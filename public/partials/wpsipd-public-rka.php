<?php
$input = shortcode_atts( array(
	'kode_bl' => '',
	'tahun_anggaran' => '2021'
), $atts );
global $wpdb;

if(empty($input['kode_bl'])){
	echo "<h1 style='text-align: center;'>kode_bl tidak boleh kosong!</h1>"; exit;
}

$type = 'rka_murni';
if(!empty($_GET) && !empty($_GET['type'])){
    $type = $_GET['type'];
}

$judul_rincian = 'Rincian Anggaran Belanja Kegiatan Satuan Kerja Perangkat Daerah';
$judul = '
	<td class="kiri atas kanan bawah text_blok text_tengah">RENCANA KERJA DAN ANGGARAN<br/>SATUAN KERJA PERANGKAT DAERAH</td>
	<td class="kiri atas kanan bawah text_blok text_tengah" style="vertical-align: middle;" rowspan="2">Formulir<br/>RKA - RINCIAN BELANJA SKPD</td>
';
if($type == 'rka_perubahan'){
	$judul_rincian = 'Rincian Perubahan Anggaran Belanja Kegiatan Satuan Kerja Perangkat Daerah';
	$judul = '
		<td class="kiri atas kanan bawah text_blok text_tengah">RENCANA KERJA DAN PERUBAHAN ANGGARAN<br/>SATUAN KERJA PERANGKAT DAERAH</td>
		<td class="kiri atas kanan bawah text_blok text_tengah" style="vertical-align: middle;" rowspan="2">Formulir<br/>RKPA - RINCIAN BELANJA SKPD</td>
	';
}else if($type == 'dpa_murni'){
	$judul_rincian = 'Rincian Anggaran Belanja Kegiatan Satuan Kerja Perangkat Daerah';
	$judul = '
		<td class="kiri atas kanan bawah text_blok text_tengah">DOKUMEN PELAKSANAAN ANGGARAN<br/>SATUAN KERJA PERANGKAT DAERAH</td>
		<td class="kiri atas kanan bawah text_blok text_tengah" style="vertical-align: middle;" rowspan="2">Formulir<br/>DPA-RINCIAN BELANJA SKPD</td>
	';
}else if($type == 'dpa_perubahan'){
	$judul_rincian = 'Rincian Perubahan Anggaran Belanja Kegiatan Satuan Kerja Perangkat Daerah';
	$judul = '
		<td class="kiri atas kanan bawah text_blok text_tengah">DOKUMEN PELAKSANAAN PERGESERAN ANGGARAN<br/>SATUAN KERJA PERANGKAT DAERAH</td>
		<td class="kiri atas kanan bawah text_blok text_tengah" style="vertical-align: middle;" rowspan="2">Formulir<br/>DPPA-RINCIAN BELANJA SKPD</td>
	';
}

$bl = $wpdb->get_results("
	SELECT 
		* 
	from data_sub_keg_bl 
	where kode_bl='".$input['kode_bl']."'"."
		AND tahun_anggaran=".$input['tahun_anggaran']."
		AND active=1"
		// ." limit 2"
, ARRAY_A);

$id_skpd = $bl[0]['id_sub_skpd'];
if(empty($id_skpd)){
	$id_skpd = $bl[0]['id_skpd'];
}
$unit = $wpdb->get_results("
	SELECT 
		* 
	from data_unit 
	where id_skpd=".$id_skpd."
		AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
		AND active=1"
, ARRAY_A);

$data_renstra = $wpdb->get_results("
	SELECT 
		* 
	from data_renstra 
	where id_unit=".$bl[0]['id_sub_skpd']."
		AND id_sub_giat=".$bl[0]['id_sub_giat']."
		AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
		AND active=1"
, ARRAY_A);

$sasaran = '';
foreach ($data_renstra as $k => $v) {
	$sasaran = str_replace('Sasaran : ', '', $v['sasaran_teks']);
}

// print_r($bl); die("
// 	SELECT 
// 		* 
// 	from data_renstra 
// 	where id_unit=".$bl[0]['id_sub_skpd']."
// 		AND id_sub_giat=".$bl[0]['id_sub_giat']."
// 		AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
// 		AND active=1");

$ind_output_db = $wpdb->get_results("
	SELECT 
		* 
	from data_output_giat_sub_keg 
	where kode_sbl='".$bl[0]['kode_sbl']."' 
		AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
		AND active=1"
, ARRAY_A);

$ind_output_murni = [];
$target_ind_output_murni = [];
$ind_output = [];
$target_ind_output = [];
foreach ($ind_output_db as $k => $v) {
	$ind_output[] = '
		<tr>
            <td width="495">'.$v['outputteks'].'</td>
        </tr>
	';
	$target_ind_output[] = '
		<tr>
            <td width="495">'.$v['targetoutputteks'].'</td>
        </tr>
	';
}

$ind_hasil_db = $wpdb->get_results("
	SELECT 
		* 
	from data_keg_indikator_hasil 
	where kode_sbl='".$bl[0]['kode_sbl']."' 
		AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
		AND active=1"
, ARRAY_A);

$ind_hasil_murni = [];
$target_ind_hasil_murni = [];
$ind_hasil = [];
$target_ind_hasil = [];
foreach ($ind_hasil_db as $k => $v) {
	$ind_hasil[] = '
		<tr>
            <td width="495">'.$v['hasilteks'].'</td>
        </tr>
	';
	$target_ind_hasil[] = '
		<tr>
            <td width="495">'.$v['targethasilteks'].'</td>
        </tr>
	';
}

$ind_prog_db = $wpdb->get_results("
	SELECT 
		* 
	from data_capaian_prog_sub_keg 
	where kode_sbl='".$bl[0]['kode_sbl']."' 
		AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
		AND active=1"
, ARRAY_A);

$ind_capaian_kegiatan_murni = array();
$target_ind_capaian_kegiatan_murni = array();
$ind_capaian_kegiatan = array();
$target_ind_capaian_kegiatan = array();
$ind_prog = array();
foreach ($ind_prog_db as $k => $v) {
	// print_r($v); die();
	$ind_prog[] = '
		<tr>
            <td width="495">'.$v['capaianteks'].'</td>
            <td width="495">'.$v['targetcapaianteks'].'</td>
        </tr>
	';
	$ind_capaian_kegiatan[] = '
		<tr>
            <td width="495">'.$v['capaianteks'].'</td>
        </tr>
	';
	$target_ind_capaian_kegiatan[] = '
		<tr>
            <td width="495">'.$v['targetcapaianteks'].'</td>
        </tr>
	';
}

// print_r($bl); die();
$bulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

$tahun_anggaran = $bl[0]['tahun_anggaran'];
$pagu_murni = 0;
$pagu = 0;
$pagu_n_lalu = 0;
$pagu_n_depan = 0;
$rin_sub = '';
$total_pagu_murni = 0;
$total_pagu = 0;
foreach ($bl as $k => $sub_bl) {
	$total_pagu_murni += $sub_bl['pagumurni'];
	$total_pagu += $sub_bl['pagu'];
	$pagu += $sub_bl['pagu'];
	$pagu_n_lalu += $sub_bl['pagu_n_lalu'];
	$pagu_n_depan += $sub_bl['pagu_n_depan'];

	$sql = "
		SELECT 
			* 
		from data_dana_sub_keg 
		where kode_sbl='".$sub_bl['kode_sbl']."'
			AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
			AND active=1";
	$sd_sub_keg = $wpdb->get_results($sql, ARRAY_A);
	$sd_sub = array();
	foreach ($sd_sub_keg as $key => $sd) {
		$new_sd = explode(' - ', $sd['namadana']);
		if(!empty($new_sd[1])){
			$sd_sub[] = $new_sd[1];
		}
	}

	$sql = "
		SELECT 
			* 
		from data_sub_keg_indikator 
		where kode_sbl='".$sub_bl['kode_sbl']."'
			AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
			AND active=1";
	$indikator_sub_keg = $wpdb->get_results($sql, ARRAY_A);
	// print_r($indikator_sub_keg); die($wpdb->last_query);
	$indikator_sub_murni = '';
	$indikator_sub = '';
	foreach ($indikator_sub_keg as $key => $ind) {
		$indikator_sub .= '
			<tr>
                <td>'.$ind['outputteks'].'</td>
                <td>'.$ind['targetoutputteks'].'</td>
            </tr>
		';
	}

	$sql = "
		SELECT 
			* 
		from data_lokasi_sub_keg 
		where kode_sbl='".$sub_bl['kode_sbl']."'
			AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
			AND active=1";
	$lokasi_sub_keg = $wpdb->get_results($sql, ARRAY_A);
	$lokasi_sub = array();
	foreach ($lokasi_sub_keg as $key => $lok) {
		if(!empty($lok['idkabkota'])){
			$lokasi_sub[] = $lok['daerahteks'];
		}
		if(!empty($lok['idcamat'])){
			$lokasi_sub[] = $lok['camatteks'];
		}
		if(!empty($lok['idlurah'])){
			$lokasi_sub[] = $lok['lurahteks'];
		}
	}

	$table_ind_perubahan_murni = '';
	if(
		$type == 'rka_perubahan'
		|| $type == 'dpa_perubahan'
	){
		$table_ind_perubahan_murni = '
			<table width="100%" border="0" style="border-spacing: 0px;">
                <tr>
                	<td width="495">Indikator (Sebelum Perubahan)</td>
                	<td width="495">Target (Sebelum Perubahan)</td>
                </tr>
                '.$indikator_sub_murni.'
            </table>';

		$header_sub = '
			<tr>
	            <td class="kiri kanan bawah text_tengah text_blok" rowspan="3" style="vertical-align: middle;">Kode Rekening</td>
	            <td class="kanan bawah text_tengah text_blok" rowspan="3" style="vertical-align: middle;">Uraian</td>
	            <td class="kanan bawah text_tengah text_blok" colspan="5">Sebelum Perubahan</td>
	            <td class="kanan bawah text_tengah text_blok" colspan="5">Setelah Perubahan</td>
	            <td class="kanan bawah text_tengah text_blok" rowspan="3" style="vertical-align: middle;">Bertambah/ (Berkurang)</td>
	        </tr>
			<tr>
	            <td class="kanan bawah text_tengah text_blok" colspan="4">Rincian Perhitungan</td>
	            <td class="kanan bawah text_tengah text_blok" rowspan="2" style="vertical-align: middle;">Jumlah</td>
	            <td class="kanan bawah text_tengah text_blok" colspan="4">Rincian Perhitungan</td>
	            <td class="kanan bawah text_tengah text_blok" rowspan="2" style="vertical-align: middle;">Jumlah</td>
	        </tr>
	        <tr>
	            <td class="kanan bawah text_tengah text_blok">Koefisien</td>
	            <td class="kanan bawah text_tengah text_blok">Satuan</td>
	            <td class="kanan bawah text_tengah text_blok">Harga</td>
	            <td class="kanan bawah text_tengah text_blok">PPN</td>
	            <td class="kanan bawah text_tengah text_blok">Koefisien</td>
	            <td class="kanan bawah text_tengah text_blok">Satuan</td>
	            <td class="kanan bawah text_tengah text_blok">Harga</td>
	            <td class="kanan bawah text_tengah text_blok">PPN</td>
	        </tr>
		';
	}else{
		$header_sub = '
			<tr>
	            <td class="kiri kanan bawah text_tengah text_blok" rowspan="2">Kode Rekening</td>
	            <td class="kanan bawah text_tengah text_blok" rowspan="2">Uraian</td>
	            <td class="kanan bawah text_tengah text_blok" colspan="4">Rincian Perhitungan</td>
	            <td class="kanan bawah text_tengah text_blok" rowspan="2">Jumlah</td>
	        </tr>
	        <tr>
	            <td class="kanan bawah text_tengah text_blok">Koefisien</td>
	            <td class="kanan bawah text_tengah text_blok">Satuan</td>
	            <td class="kanan bawah text_tengah text_blok">Harga</td>
	            <td class="kanan bawah text_tengah text_blok">PPN</td>
	        </tr>
		';
	}
	$rin_sub .= '
		<tr>
            <td class="kiri kanan bawah" colspan="13">
                <table>
                    <tr>
                        <td width="130">Sub Kegiatan</td>
                        <td width="10">:</td>
                        <td>'.$sub_bl['nama_sub_giat'].'</td>
                    </tr>
                    <tr>
                        <td width="130">Sumber Pendanaan</td>
                        <td width="10">:</td>
                        <td>'.implode(', ', $sd_sub).'</td>
                    </tr>
                    <tr>
                        <td width="130">Lokasi</td>
                        <td width="10">:</td>
                        <td>'.implode(', ', $lokasi_sub).'</td>
                    </tr>
                    <tr>
                        <td width="130">Waktu Pelaksanaan</td>
                        <td width="10">:</td>
                        <td>'.$bulan[$sub_bl['waktu_awal']-1].' s.d. '.$bulan[$sub_bl['waktu_akhir']-1].'</td>
                    </tr>
                    <tr valign="top">
                        <td width="150">Keluaran Sub Kegiatan</td>
                        <td width="10">:</td>
                        <td>
                        	'.$table_ind_perubahan_murni.'
                            <table width="100%" border="0" style="border-spacing: 0px;">
                                <tr>
                                	<td width="495">Indikator</td>
                                	<td width="495">Target</td>
                                </tr>
                                '.$indikator_sub.'
                            </table>
                         </td>
                    </tr>
                </table>
            </td>
    	</tr>
    	'.$header_sub.'
	';
	$rinc = $wpdb->get_results("
		SELECT 
			* 
		from data_rka 
		where kode_sbl='".$sub_bl['kode_sbl']."'
			AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
			AND active=1
		Order by kode_akun ASC"
	, ARRAY_A);
	// print_r($rinc); die();
	$rin_sub_item = '';
	$total_sub_rinc = 0;
	$total_sub_rinc_murni = 0;
	$akun = array();
	$total_subs_bl_teks = array();
	foreach ($rinc as $key => $item) {
		if(empty($item['kode_akun'])){
			continue;
		}
		$akun_all = explode('.', $item['kode_akun']);
		$akun_1 = $akun_all[0].'.'.$akun_all[1];
		$akun_2 = $akun_1.'.'.$akun_all[2];
		$akun_3 = $akun_2.'.'.$akun_all[3];
		$akun_4 = $akun_3.'.'.$akun_all[4];
		$akun_1_db = $wpdb->get_results("
			SELECT 
				kode_akun,
				nama_akun 
			from data_akun 
			where kode_akun='".$akun_1."'"
		, ARRAY_A);
		$akun_2_db = $wpdb->get_results("
			SELECT 
				kode_akun,
				nama_akun 
			from data_akun 
			where kode_akun='".$akun_2."'"
		, ARRAY_A);
		$akun_3_db = $wpdb->get_results("
			SELECT 
				kode_akun,
				nama_akun 
			from data_akun 
			where kode_akun='".$akun_3."'"
		, ARRAY_A);
		$akun_4_db = $wpdb->get_results("
			SELECT 
				kode_akun,
				nama_akun 
			from data_akun 
			where kode_akun='".$akun_4."'"
		, ARRAY_A);
		if(empty($akun[$akun_1_db[0]['kode_akun']])){
			$akun[$akun_1_db[0]['kode_akun']] = array(
				'total' => 0,
				'total_murni' => 0,
				'status' => 0,
				'kode_akun' => $akun_1_db[0]['kode_akun'],
				'nama_akun' => $akun_1_db[0]['nama_akun']
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']])){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']] = array(
				'total' => 0,
				'total_murni' => 0,
				'status' => 0,
				'kode_akun' => $akun_2_db[0]['kode_akun'],
				'nama_akun' => $akun_2_db[0]['nama_akun']
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']])){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']] = array(
				'total' => 0,
				'total_murni' => 0,
				'status' => 0,
				'kode_akun' => $akun_3_db[0]['kode_akun'],
				'nama_akun' => $akun_3_db[0]['nama_akun']
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']])){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']] = array(
				'total' => 0,
				'total_murni' => 0,
				'status' => 0,
				'kode_akun' => $akun_4_db[0]['kode_akun'],
				'nama_akun' => $akun_4_db[0]['nama_akun']
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']])){
			$nama_akun = str_replace($item['kode_akun'], '', $item['nama_akun']);
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']] = array(
				'total' => 0,
				'total_murni' => 0,
				'status' => 0,
				'kode_akun' => $item['kode_akun'],
				'nama_akun' => $nama_akun
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']])){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']] = array(
				'total' => 0,
				'total_murni' => 0,
				'status' => 0,
				'kode_akun' => '&nbsp;',
				'nama_akun' => $item['subs_bl_teks']
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']])){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']] = array(
				'total' => 0,
				'total_murni' => 0,
				'status' => 0,
				'kode_akun' => '&nbsp;',
				'nama_akun' => $item['ket_bl_teks']
			);
		}
		$akun[$akun_1_db[0]['kode_akun']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']]['total_murni'] += $item['rincian_murni'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['total_murni'] += $item['rincian_murni'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['total_murni'] += $item['rincian_murni'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['total_murni'] += $item['rincian_murni'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['total_murni'] += $item['rincian_murni'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['total_murni'] += $item['rincian_murni'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['total_murni'] += $item['rincian_murni'];
	}
	// print_r($akun); die();
	foreach ($rinc as $key => $item) {
		if(empty($item['kode_akun'])){
			continue;
		}
		$alamat_array = $this->get_alamat($bl[0], $item);
        $alamat = $alamat_array['alamat'];
        $lokus_akun_teks = $alamat_array['lokus_akun_teks'];
		if(empty($alamat)){
			$alamat = array();
            if(!empty($item['id_lurah_penerima'])){
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['id_lurah_penerima']." and is_kel=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            if(!empty($item['id_camat_penerima'])){
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['id_camat_penerima']." and is_kec=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            if(!empty($item['id_kokab_penerima'])){
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['id_kokab_penerima']." and is_kab=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            if(!empty($item['id_prop_penerima'])){
                $db_alamat = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$item['id_prop_penerima']." and is_prov=1", ARRAY_A);
                $alamat[] = $db_alamat['nama'];
            }
            $profile_penerima = implode(', ', $alamat);
		}else{
			$profile_penerima = $lokus_akun_teks.', '.$alamat;
		}

		$akun_all = explode('.', $item['kode_akun']);
		$akun_1 = $akun_all[0].'.'.$akun_all[1];
		$akun_2 = $akun_1.'.'.$akun_all[2];
		$akun_3 = $akun_2.'.'.$akun_all[3];
		$akun_4 = $akun_3.'.'.$akun_all[4];
		$akun_1_db = $wpdb->get_results("
			SELECT 
				kode_akun,
				nama_akun 
			from data_akun 
			where kode_akun='".$akun_1."'"
		, ARRAY_A);
		$akun_2_db = $wpdb->get_results("
			SELECT 
				kode_akun,
				nama_akun 
			from data_akun 
			where kode_akun='".$akun_2."'"
		, ARRAY_A);
		$akun_3_db = $wpdb->get_results("
			SELECT 
				kode_akun,
				nama_akun 
			from data_akun 
			where kode_akun='".$akun_3."'"
		, ARRAY_A);
		$akun_4_db = $wpdb->get_results("
			SELECT 
				kode_akun,
				nama_akun 
			from data_akun 
			where kode_akun='".$akun_4."'"
		, ARRAY_A);
		if($akun[$akun_1_db[0]['kode_akun']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']]['status'] = 1;
			$rin_murni = '';
			$selisih_murni = '';
			if(
        		$type == 'rka_perubahan'
        		|| $type == 'dpa_perubahan'
        	){
				$rin_murni = '
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']]['total_murni'],0,",",".").'</td>
                    <td colspan="4" class="kanan bawah text_blok"></td>
				';
				$selisih_murni = '
					<td class="kanan bawah text_blok text_kanan">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']]['total_murni']-$akun[$akun_1_db[0]['kode_akun']]['total'],0,",",".").'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']]['nama_akun'].'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['status'] = 1;
			$rin_murni = '';
			$selisih_murni = '';
			if(
        		$type == 'rka_perubahan'
        		|| $type == 'dpa_perubahan'
        	){
				$rin_murni = '
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['total_murni'],0,",",".").'</td>
                    <td colspan="4" class="kanan bawah text_blok"></td>
				';
				$selisih_murni = '
					<td class="kanan bawah text_blok text_kanan">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['total_murni']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['total'],0,",",".").'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['nama_akun'].'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['status'] = 1;
			$rin_murni = '';
			$selisih_murni = '';
			if(
        		$type == 'rka_perubahan'
        		|| $type == 'dpa_perubahan'
        	){
				$rin_murni = '
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['total_murni'],0,",",".").'</td>
                    <td colspan="4" class="kanan bawah text_blok"></td>
				';
				$selisih_murni = '
					<td class="kanan bawah text_blok text_kanan">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['total_murni']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['total'],0,",",".").'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['nama_akun'].'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['status'] = 1;
			$rin_murni = '';
			$selisih_murni = '';
			if(
        		$type == 'rka_perubahan'
        		|| $type == 'dpa_perubahan'
        	){
				$rin_murni = '
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['total_murni'],0,",",".").'</td>
                    <td colspan="4" class="kanan bawah text_blok"></td>
				';
				$selisih_murni = '
					<td class="kanan bawah text_blok text_kanan">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['total_murni']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['total'],0,",",".").'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['nama_akun'].'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}
		// print_r($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]);
		// print_r($item['nama_akun']);
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['status'] = 1;
			$rin_murni = '';
			$selisih_murni = '';
			if(
        		$type == 'rka_perubahan'
        		|| $type == 'dpa_perubahan'
        	){
				$rin_murni = '
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['total_murni'],0,",",".").'</td>
                    <td colspan="4" class="kanan bawah text_blok"></td>
				';
				$selisih_murni = '
					<td class="kanan bawah text_blok text_kanan">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['total_murni']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['total'],0,",",".").'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['nama_akun'].'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['status'] = 1;
			$rin_murni = '';
			$selisih_murni = '';
			if(
        		$type == 'rka_perubahan'
        		|| $type == 'dpa_perubahan'
        	){
				$rin_murni = '
                    <td class="kanan bawah text_kanan text_blok nilai_kelompok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['total_murni'],0,",",".").'</td>
                    <td colspan="4" class="kanan bawah text_blok"></td>
				';
				$selisih_murni = '
					<td class="kanan bawah text_blok text_kanan nilai_kelompok">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['total_murni']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['total'],0,",",".").'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['nama_akun'].'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok nilai_kelompok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['status'] = 1;
			$rin_murni = '';
			$selisih_murni = '';
			if(
        		$type == 'rka_perubahan'
        		|| $type == 'dpa_perubahan'
        	){
				$rin_murni = '
                    <td class="kanan bawah text_kanan text_blok nilai_keterangan" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['total_murni'],0,",",".").'</td>
                    <td colspan="4" class="kanan bawah text_blok"></td>
				';
				$selisih_murni = '
					<td class="kanan bawah text_blok text_kanan nilai_keterangan">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['total_murni']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['total'],0,",",".").'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['nama_akun'].'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok nilai_keterangan" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}
		$rin_murni = '';
		$selisih_murni = '';
		if(
    		$type == 'rka_perubahan'
    		|| $type == 'dpa_perubahan'
    	){
			$rin_murni = '
                <td class="kanan bawah" style="vertical-align: middle;">'.$item['koefisien_murni'].'</td>
                <td class="kanan bawah" style="vertical-align: middle;">'.$item['satuan'].'</td>
                <td class="kanan bawah text_kanan" style="vertical-align: middle;">'.number_format($item['harga_satuan'],0,",",".").'</td>
                <td class="kanan bawah text_kanan" style="vertical-align: middle;">'.number_format($item['pajak_murni'],0,",",".").'</td>
                <td class="kanan bawah text_kanan" style="vertical-align: middle;white-space:nowrap">Rp. '.number_format($item['rincian_murni'],0,",",".").'</td>
			';
			$selisih_murni = '
				<td class="kanan bawah text_kanan">Rp. '.number_format($item['rincian_murni']-$item['total_harga'],0,",",".").'</td>
			';
		}
		$rin_sub_item .= '
			<tr>
				<td class="kiri kanan bawah text_blok">&nbsp;</td>
                <td class="kanan bawah">
                    <div>'.$item['nama_komponen'].'</div>
                    <div style="margin-left: 20px">'.$item['spek_komponen'].'</div>
                    <div style="margin-left: 40px" class="profile-penerima" id-profile="'.$item['id_penerima'].'" id-prop="'.$item['id_prop_penerima'].'" id-kokab="'.$item['id_kokab_penerima'].'" id-camat="'.$item['id_camat_penerima'].'" id-lurah="'.$item['id_lurah_penerima'].'">'.$profile_penerima.'</div>
                </td>
                '.$rin_murni.'
                <td class="kanan bawah" style="vertical-align: middle;">'.$item['koefisien'].'</td>
                <td class="kanan bawah" style="vertical-align: middle;">'.$item['satuan'].'</td>
                <td class="kanan bawah text_kanan" style="vertical-align: middle;">'.number_format($item['harga_satuan_murni'],0,",",".").'</td>
                <td class="kanan bawah text_kanan" style="vertical-align: middle;">'.number_format($item['totalpajak'],0,",",".").'</td>
                <td class="kanan bawah text_kanan" style="vertical-align: middle;white-space:nowrap">Rp. '.number_format($item['total_harga'],0,",",".").'</td>
                '.$selisih_murni.'
            </tr>
		';
		$total_sub_rinc += $item['total_harga'];
		$total_sub_rinc_murni += $item['rincian_murni'];
	}
	$rin_murni = '';
	$selisih_murni = '';
	$colspan = 6;
	if(
		$type == 'rka_perubahan'
		|| $type == 'dpa_perubahan'
	){
		$colspan = 4;
		$rin_murni = '
			<td colspan="6" class="kiri kanan bawah text_kanan text_blok">Jumlah Anggaran Sub Kegiatan :</td>
            <td class="kanan bawah text_blok text_kanan" style="white-space:nowrap">Rp. '.number_format($total_sub_rinc_murni,0,",",".").'</td>
		';
		$selisih_murni = '
			<td class="kanan bawah text_blok text_kanan" style="white-space:nowrap">Rp. '.number_format($total_sub_rinc_murni - $total_sub_rinc,0,",",".").'</td>
		';
	}
	$rin_sub_item .= '
		<tr>
            '.$rin_murni.'
            <td colspan="'.$colspan.'" class="kiri kanan bawah text_kanan text_blok">Jumlah Anggaran Sub Kegiatan :</td>
            <td class="kanan bawah text_blok text_kanan" style="white-space:nowrap">Rp. '.number_format($total_sub_rinc,0,",",".").'</td>
            '.$selisih_murni.'
        </tr>
	';
	$rin_sub .= $rin_sub_item;
}

?>

<style type="text/css">
	.nilai_kelompok, .nilai_keterangan {
		color: #fff;
	}
	.cellpadding_1 td, .cellpadding_1 th {
		padding: 1px;
	}
	.cellpadding_2 td, .cellpadding_2 th {
		padding: 2px;
	}
	.cellpadding_3 td, .cellpadding_3 th {
		padding: 3px;
	}
	.cellpadding_4 td, .cellpadding_4 th {
		padding: 4px;
	}
	.cellpadding_5 td, .cellpadding_5 th {
		padding: 5px;
	}
	.no_padding>td {
		padding: 0;
	}
	td, th {
		text-align: inherit;
		padding: inherit;
		display: table-cell;
    	vertical-align: inherit;
	}
	table, td, th {
		border: 0; 
	}
	body {
		display: block;
		margin: 8px;
	    font-family: 'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
	    padding: 0;
	    font-size: 13px;
	}
	table {
	    display: table;
	    border-collapse: separate;
	    margin: 0;
	}
    .cetak{
        font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
        padding:0;
        margin:0;
        font-size:13px;
    }
    @media  print {
        @page  {
            size:auto;
            margin: 11mm 15mm 15mm 15mm;
        }
        body {
            width: 210mm;
            height: 297mm;
        }
        /*.footer { position: fixed; bottom: 0; font-size:11px; display:block; }
        .pagenum:after { counter-increment: page; content: counter(page); }*/
    }

    .profile-penerima {
    	display: none;
    }
    header, nav {
    	display: none;
    }
    .td_v_middle td {
    	vertical-align: middle;
    }
</style>
<div class="cetak">
	<table width="100%" class="cellpadding_1" style="border-spacing: 2px;">
	    <tr>
	        <td colspan="2">
	            <table width="100%" class="cellpadding_5" style="border-spacing: 1px;" class="text_tengah text_15">
	                <tr>
	                    <?php echo $judul; ?>
	                </tr>
	                <tr>
	                    <td class="kiri atas kanan bawah text_tengah">Pemerintah <?php echo carbon_get_theme_option('crb_daerah'); ?> Tahun Anggaran <?php echo $tahun_anggaran; ?></td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	    <tr>
	        <td colspan="2">
	            <table width="100%" class="cellpadding_2" style="border-spacing: 1px;">
	                <tr>
	                    <td width="150">Urusan Pemerintahan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_urusan']; ?> <?php echo $bl[0]['nama_urusan']; ?></td>
	                </tr>
	                <tr>
	                    <td width="150">Bidang Urusan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_bidang_urusan']; ?> <?php echo $bl[0]['nama_bidang_urusan']; ?></td>
	                </tr>
	                <tr>
	                    <td width="150">Program</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_program']; ?> <?php echo $bl[0]['nama_program']; ?></td>
	                </tr>
	                <tr>
	                    <td width="150">Sasaran Program</td>
	                    <td width="10">:</td>
	                    <td><?php echo $sasaran; ?></td>
	                </tr>
	                <tr valign="top" class="no_padding">
	                    <td width="150">Capaian Program</td>
	                    <td width="10">:</td>
	                    <td>
	                        <table width="50%" border="0" style="border-spacing: 0px;">
	                            <tr>
	                            	<td>Indikator</td>
	                            	<td>Target</td>
	                            </tr>
								<?php echo implode('', $ind_prog); ?>
	                        </table>
	                </td>
	                </tr>
	                <tr>
	                    <td width="150">Kegiatan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_giat']; ?> <?php echo $bl[0]['nama_giat']; ?></td>
	                </tr>
	                <tr>
	                    <td width="150">Organisasi</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_skpd']; ?>&nbsp;<?php echo $bl[0]['nama_skpd']; ?></td>
	                </tr>
	                <tr>
	                    <td width="150">Unit</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_sub_skpd']; ?>&nbsp;<?php echo $bl[0]['nama_sub_skpd']; ?></td>
	                </tr>
	                <tr>
	                    <td width="150">Alokasi Tahun <?php echo $tahun_anggaran-1; ?></td>
	                    <td width="10">:</td>
	                    <td>Rp. <?php echo number_format($pagu_n_lalu,0,",","."); ?></td>
	                </tr>
	                <tr>
	                    <td width="150">Alokasi Tahun <?php echo $tahun_anggaran; ?></td>
	                    <td width="10">:</td>
	                    <td>Rp. <?php echo number_format($pagu,0,",","."); ?></td>
	                </tr>
	                <tr>
	                    <td width="150">Alokasi Tahun <?php echo $tahun_anggaran+1; ?></td>
	                    <td width="10">:</td>
	                    <td>Rp. <?php echo number_format($pagu_n_depan,0,",","."); ?></td>
	                </tr>
	            </table>
	        </td>            
	    </tr>
	    <tr>
	        <td class="atas kanan bawah kiri text_15 text_tengah" colspan="2">Indikator &amp; Tolok Ukur Kinerja Kegiatan</td>
	    </tr>        
	    <tr>
	        <td class="kiri kanan atas bawah" colspan="2">
	            <table width="100%" class="cellpadding_5 td_v_middle" style="border-spacing: 2px;">
	            <?php 
            		$capaian_kegiatan_murni = '';
            		$masukan_kegiatan_murni = '';
            		$keluaran_kegiatan_murni = '';
	            	if(
	            		$type == 'rka_murni'
	            		|| $type == 'dpa_murni'
	            	){
		                echo '
			                <tr>
			                    <td width="130" class="text_tengah kiri atas kanan bawah">Indikator</td>
			                    <td class="text_tengah kiri atas kanan bawah">Tolok Ukur Kinerja</td>
			                    <td width="123" class="text_tengah kiri atas kanan bawah">Target Kinerja</td>
			                </tr>';
			        }else{
	            		$capaian_kegiatan_murni = '
	            			<td class="kiri kanan atas bawah">
			                    <table width="100%" border="0" style="border-spacing: 0px;">
			                        '.implode('', $ind_capaian_kegiatan_murni).'
			                    </table>
			                </td>
			                <td class="kiri kanan atas bawah">
			                    <table width="100%" border="0" style="border-spacing: 0px;">
			                        '.implode('', $target_ind_capaian_kegiatan_murni).'
			                    </table>
			                </td>';
	            		$masukan_kegiatan_murni = '
		            		<td class="kiri kanan atas bawah">
		                        <table width="100%" border="0" style="border-spacing: 0px;">
			                        <tr>
			                          <td width="495">Dana yang dibutuhkan</td>
			                        </tr>
		                        </table>
		                    </td>
		                    <td class="kiri kanan atas bawah">
		                        <table width="100%" border="0" style="border-spacing: 0px;">
			                        <tr>
			                          <td width="495">Rp. '.number_format($pagu_murni,0,",",".").'</td>
			                        </tr>
		                        </table>
		                    </td>';
	            		$keluaran_kegiatan_murni = '
	            			<td class="kiri kanan atas bawah">
		                        <table width="100%" border="0" style="border-spacing: 0px;">
		                            '.implode('', $ind_output_murni).'
		                    	</table>
		                    </td>
		                    <td class="kiri kanan atas bawah">
		                        <table width="100%" border="0" style="border-spacing: 0px;">
		                            '.implode('', $target_ind_output_murni).'
		                        </table>
		                    </td>
	            		';
	            		$hasil_kegiatan_murni = '
		                    <td class="kiri kanan atas bawah">
		                        <table width="100%" border="0" style="border-spacing: 0px;">
		                        	'.implode('', $ind_hasil_murni).'
		                        </table>
		                    </td>
		                    <td class="kiri kanan atas bawah">
		                        <table width="100%" border="0" style="border-spacing: 0px;">
		                        	'.implode('', $target_ind_hasil_murni).'
		                        </table>
		                    </td>
	            		';
			        	echo '
			                <tr>
			                    <td width="130" rowspan="2" class="text_tengah kiri atas kanan bawah">Indikator</td>
			                	<td colspan="2" class="text_tengah kiri atas kanan bawah">Sebelum Perubahan</td>
			                	<td colspan="2" class="text_tengah kiri atas kanan bawah">Setelah Perubahan</td>
			                </tr>
			                <tr>
			                    <td class="text_tengah kiri atas kanan bawah">Tolok Ukur Kinerja</td>
			                    <td width="123" class="text_tengah kiri atas kanan bawah">Target Kinerja</td>
			                    <td class="text_tengah kiri atas kanan bawah">Tolok Ukur Kinerja</td>
			                    <td width="123" class="text_tengah kiri atas kanan bawah">Target Kinerja</td>
			                </tr>';
			        }

	            ?>
	                <tr>
	                    <td width="130" class="kiri kanan atas bawah">Capaian Kegiatan</td>
		                <?php echo $capaian_kegiatan_murni; ?>
	                    <td class="kiri kanan atas bawah">
		                    <table width="100%" border="0" style="border-spacing: 0px;">
		                        <?php echo implode('', $ind_capaian_kegiatan); ?>
		                    </table>
		                </td>
		                <td class="kiri kanan atas bawah">
		                    <table width="100%" border="0" style="border-spacing: 0px;">
		                        <?php echo implode('', $target_ind_capaian_kegiatan); ?>
		                    </table>
		                </td>
	                </tr>
	                <tr class="no_padding">
	                    <td width="130" class="kiri kanan atas bawah">Masukan</td>
		                <?php echo $masukan_kegiatan_murni; ?>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
		                        <tr>
		                          <td width="495">Dana yang dibutuhkan</td>
		                        </tr>
	                        </table>
	                    </td>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
		                        <tr>
		                          <td width="495">Rp. <?php echo number_format($pagu,0,",","."); ?></td>
		                        </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td width="130" class="kiri kanan atas bawah">Keluaran</td>
		                <?php echo $keluaran_kegiatan_murni; ?>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                            <?php echo implode('', $ind_output); ?>
	                    	</table>
	                    </td>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                            <?php echo implode('', $target_ind_output); ?>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td width="130" class="kiri kanan atas bawah">Hasil</td>
		                <?php echo $hasil_kegiatan_murni; ?>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                        	<?php echo implode('', $ind_hasil); ?>
	                        </table>
	                    </td>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                        	<?php echo implode('', $target_ind_hasil); ?>
	                        </table>
	                    </td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	    <tr>
	        <td width="150" colspan="2">Kelompok Sasaran Kegiatan : <?php echo $bl[0]['sasaran'];?></td>
	    </tr>
	    <tr>
	        <td width="150" colspan="2">&nbsp;</td>
	    </tr>
	    <tr>
	        <td class="atas kanan bawah kiri text_tengah text_15" colspan="2">
	            <table width="100%" class="cellpadding_5" style="border-spacing: 0px;" >
	                <tr>
	                	<td><?php echo $judul_rincian; ?></td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	    <tr>
	        <td colspan="2">
	            <table width="100%" class="cellpadding_5" style="border-spacing: 0px;">
	                <tbody>
                    <?php 
	                    echo $rin_sub; 
	                    $rin_murni = '';
						$selisih_murni = '';
						$colspan = 6;
						if(
							$type == 'rka_perubahan'
							|| $type == 'dpa_perubahan'
						){
							$colspan = 4;
							$rin_murni = '
		                        <td colspan="6" class="kiri kanan bawah text_kanan text_blok">Jumlah Total Anggaran Kegiatan :</td>
		                    	<td class="kanan bawah text_blok text_kanan" style="white-space:nowrap">Rp. '.number_format($total_pagu_murni,0,",",".").'</td>
							';
							$selisih_murni = '
								<td class="kanan bawah text_blok text_kanan">'.number_format($total_pagu_murni - $total_pagu,0,",",".").'</td>
							';
						}
	                ?>
	                    <tr class="">
	                    	<?php echo $rin_murni; ?>
	                        <td colspan="<?php echo $colspan; ?>" class="kiri kanan bawah text_kanan text_blok">Jumlah Total Anggaran Kegiatan :</td>
	                    	<td class="kanan bawah text_blok text_kanan" style="white-space:nowrap">Rp. <?php echo number_format($total_pagu,0,",","."); ?></td>
	                    	<?php echo $selisih_murni; ?>
	                    </tr>
	                
	                </tbody>

	            </table>
	                        </td>
	    </tr>
	    <tr>
	        <td class="kiri kanan atas bawah" width="350" valign="top">
	        &nbsp;
	        </td>
	        <td class="kiri kanan atas bawah" width="250" valign="top">
	            <table width="100%" class="cellpadding_2" style="border-spacing: 0px;">
	                <tr><td colspan="3" class="text_tengah"><?php echo carbon_get_theme_option('crb_daerah'); ?> , Tanggal </td></tr>
                    <tr><td colspan="3" class="text_tengah text_15">Kepala&nbsp;<?php echo $unit[0]['namaunit']; ?></td></tr>
	                <tr><td colspan="3" height="80">&nbsp;</td></tr>
	                <tr><td colspan="3" class="text_tengah"><?php echo $unit[0]['namakepala']; ?></td></tr>
	                <tr><td colspan="3" class="text_tengah"><?php echo $unit[0]['nipkepala']; ?></td></tr>
                </table>
	        </td>
	    </tr>
	    <tr>
	        <td colspan="2">
	            <table width="100%" class="cellpadding_5" style="border-spacing: 0px;">
	                <tr><td width="160" class="kiri atas bawah">Keterangan</td><td width="10" class="atas bawah">:</td><td class="atas bawah kanan">&nbsp;</td></tr>
	                <tr><td width="160" class="kiri bawah">Tanggal Pembahasan</td><td width="10" class="bawah">:</td><td class="bawah kanan">&nbsp;</td></tr>
	                <tr><td width="160" class="kiri bawah">Catatan Hasil Pembahasan</td><td width="10" class="bawah">:</td><td class="bawah kanan">&nbsp;</td></tr>
	                <tr><td class="kiri bawah kanan" colspan="3">1.&nbsp;</td></tr>
	                <tr><td class="kiri bawah kanan" colspan="3">2.&nbsp;</td></tr>
	                <tr><td class="kiri bawah kanan" colspan="3">3.&nbsp;</td></tr>
	                <tr><td class="kiri bawah kanan" colspan="3">4.&nbsp;</td></tr>
	                <tr><td class="kiri bawah kanan" colspan="3">5.&nbsp;</td></tr>
	            </table>
	        </td>
	    </tr>
	    <tr>
	        <td colspan="2">
	            <table width="100%" class="cellpadding_5" style="border-spacing: 0px;">
	                <tr><td colspan="5" class="kiri kanan atas bawah text_tengah">Tim Anggaran Pemerintah Daerah</td></tr>
	                <tr class="text_tengah">
	                    <td width="10" class="kiri kanan bawah">No.</td>
	                    <td class="kanan bawah">Nama</td>
	                    <td width="120" class="bawah kanan">NIP</td>
	                    <td width="150" class="bawah kanan">Jabatan</td>
	                    <td width="100" class="bawah kanan">Tanda Tangan</td>
	                </tr>                    
	                <tr>
	                    <td width="10" class="kiri kanan bawah">1.</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                </tr>
	                <tr>
	                    <td width="10" class="kiri kanan bawah">2.</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                </tr>
	                <tr>
	                    <td width="10" class="kiri kanan bawah">3.</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                </tr>
	                <tr>
	                    <td width="10" class="kiri kanan bawah">4.</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                </tr>
	                <tr>
	                    <td width="10" class="kiri kanan bawah">5.</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                </tr>
	                <tr>
	                    <td width="10" class="kiri kanan bawah">6.</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                </tr>
	                <tr>
	                    <td width="10" class="kiri kanan bawah">7.</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                </tr>
	                <tr>
	                    <td width="10" class="kiri kanan bawah">8.</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                    <td class="bawah kanan">&nbsp;</td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	</table>
</div>    
<script type="text/javascript">
	function tampil_nilai(that, _class){
		if(jQuery(that).is(':checked')){
			jQuery(_class).css('color', 'inherit');
		}else{
			jQuery(_class).css('color', '#fff');
		}
	}
	var body = ''
		+'<h3>SETTING</h3>'
		+'<label><input type="checkbox" onclick="tampil_rinci(this);"> Tampilkan Rinci Profile Penerima Bantuan</label>'
		+'<label style="margin-left: 20px;"><input type="checkbox" onclick="tampil_nilai(this, \'.nilai_kelompok\');"> Tampilkan Nilai Kelompok</label>'
		+'<label style="margin-left: 20px;"><input type="checkbox" onclick="tampil_nilai(this, \'.nilai_keterangan\');"> Tampilkan Nilai Keterangan</label>'
		+'<label style="margin-left: 20px;"> Pilih format Laporan '
	    	+'<select class="" style="min-width: 300px;" id="type_laporan">'
	    		+'<option>-- format --</option>'
	    		+'<option value="rka_murni">RKA Murni</option>'
	    		+'<option value="rka_perubahan">RKA Perubahan</option>'
	    		+'<option value="dpa_murni">DPA Murni</option>'
	    		+'<option value="dpa_perubahan">DPA Perubahan</option>'
	    	+'</select>'
	    +'</label>';
	var aksi = ''
		+'<div id="action-sipd" class="hide-print">'
			+body
		+'</div>';
	jQuery('body').prepend(aksi);
	function tampil_rinci(that){
		if(jQuery(that).is(':checked')){
			jQuery('.profile-penerima').show();
		}else{
			jQuery('.profile-penerima').hide();
		}
	}

	var _url = window.location.href;
    var url = new URL(_url);
    _url = url.origin+url.pathname+'?key='+url.searchParams.get('key');
    var type = url.searchParams.get("type");
    jQuery('#type_laporan').val('rka_murni');
    if(type){
    	jQuery('#type_laporan').val(type);
    }
    jQuery('#type_laporan').on('change', function(){
    	window.open(_url+'&type='+jQuery(this).val(), '_blank');
    });
</script>