<?php
$input = shortcode_atts( array(
	'kode_bl' => '',
	'tahun_anggaran' => '2021'
), $atts );
global $wpdb;

function button_mapping($class=false){
	$button_mapping = '<span style="display: none;" data-id="'.$class.'" class="edit-mapping"><i class="dashicons dashicons-edit"></i></span>';
	return $button_mapping;
}

if(empty($input['kode_bl'])){
	echo "<h1 style='text-align: center;'>kode_bl tidak boleh kosong!</h1>"; exit;
}

$api_key = carbon_get_theme_option( 'crb_api_key_extension' );

$data_sumber_dana = $wpdb->get_results("select id_dana, nama_dana from data_sumber_dana where tahun_anggaran=".$input['tahun_anggaran'], ARRAY_A);
$data_label_komponen = $wpdb->get_results("select id, nama from data_label_komponen where tahun_anggaran=".$input['tahun_anggaran'], ARRAY_A);

$type = 'rka_murni';
if(!empty($_GET) && !empty($_GET['type'])){
    $type = $_GET['type'];
}

$judul_rincian = 'Rincian Anggaran Belanja Kegiatan Satuan Kerja Perangkat Daerah';
$keterangan_sub = '';
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

$class_garis_table = '';
if(
	$type == 'dpa_murni'
	|| $type == 'dpa_perubahan'
){
	$class_garis_table = 'kiri atas kanan bawah';
	$keterangan_sub = '
		<tr class="'.$class_garis_table.'">
			<td width="130">Keterangan</td>
            <td width="10">:</td>
            <td>&nbsp;</td>
        </tr>
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
	$pagu_murni += $sub_bl['pagumurni'];
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
	$sd_sub_id = array();
	$sd_sub = array();
	foreach ($sd_sub_keg as $key => $sd) {
		$new_sd = explode(' - ', $sd['namadana']);
		if(!empty($new_sd[1])){
			$sd_sub[] = $new_sd[1];
			$sd_sub_id[] = $sd['iddana'];
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
		if($type == 'rka_perubahan'){
			$table_ind_perubahan_murni = '
				<table width="100%" border="0" style="border-spacing: 0px;">
	                <tr>
	                	<td width="495">Indikator (Sebelum Perubahan)</td>
	                	<td width="495">Target (Sebelum Perubahan)</td>
	                </tr>
	                '.$indikator_sub_murni.'
	            </table>';
	    }

		$header_sub = '
			<tr>
	            <td class="kiri kanan bawah atas text_tengah text_blok" rowspan="3" style="vertical-align: middle;">Kode Rekening</td>
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
	            <td class="kiri kanan bawah atas text_tengah text_blok" rowspan="2">Kode Rekening</td>
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
		<tr class="no_padding">
            <td colspan="13">
                <table class="cellpadding_5">
                    <tr class="'.$class_garis_table.'">
                        <td width="130">Sub Kegiatan</td>
                        <td width="10">:</td>
                        <td class="subkeg" data-kdsbl="'.$sub_bl['kode_sbl'].'">'.$bl[0]['kode_bidang_urusan'].substr($sub_bl['nama_sub_giat'], 4, strlen($sub_bl['nama_sub_giat'])).'</td>
                    </tr>
                    <tr class="'.$class_garis_table.'">
                        <td width="130">Sumber Pendanaan</td>
                        <td width="10">:</td>
                        <td class="subkeg-sumberdana" data-kdsbl="'.$sub_bl['kode_sbl'].'" data-idsumberdana="'.implode(',', $sd_sub_id).'">'.implode(', ', $sd_sub).'</td>
                    </tr>
                    <tr class="'.$class_garis_table.'">
                        <td width="130">Lokasi</td>
                        <td width="10">:</td>
                        <td>'.implode(', ', $lokasi_sub).'</td>
                    </tr>
                    <tr class="'.$class_garis_table.'">
                        <td width="130">Waktu Pelaksanaan</td>
                        <td width="10">:</td>
                        <td>'.$bulan[$sub_bl['waktu_awal']-1].' s.d. '.$bulan[$sub_bl['waktu_akhir']-1].'</td>
                    </tr>
                    <tr valign="top" class="'.$class_garis_table.'">
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
                    '.$keterangan_sub.'
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
        $lokus_akun_teks = $alamat_array['lokus_akun_teks_decode'];
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
			if(strpos($item['nama_komponen'], $lokus_akun_teks) !== false ){
				$profile_penerima = $alamat;
			}else{
				$profile_penerima = $lokus_akun_teks.', '.$alamat;
			}
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

		// rekening 1
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
					<td class="kanan bawah text_blok text_kanan">Rp. '.$this->ubah_minus($akun[$akun_1_db[0]['kode_akun']]['total']-$akun[$akun_1_db[0]['kode_akun']]['total_murni']).'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5"><span class="nama">'.$akun[$akun_1_db[0]['kode_akun']]['nama_akun'].'</span>'.button_mapping($sub_bl['kode_sbl'].'-'.$akun_1_db[0]['kode_akun']).'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}

		// rekening 2
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
					<td class="kanan bawah text_blok text_kanan">Rp. '.$this->ubah_minus($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['total']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['total_murni']).'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5"><span class="nama">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['nama_akun'].'</span>'.button_mapping($sub_bl['kode_sbl'].'-'.$akun_2_db[0]['kode_akun']).'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}

		// rekening 3
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
					<td class="kanan bawah text_blok text_kanan">Rp. '.$this->ubah_minus($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['total']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['total_murni']).'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5"><span class="nama">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['nama_akun'].'</span>'.button_mapping($sub_bl['kode_sbl'].'-'.$akun_3_db[0]['kode_akun']).'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}

		// rekening 4
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
					<td class="kanan bawah text_blok text_kanan">Rp. '.$this->ubah_minus($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['total']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['total_murni']).'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5"><span class="nama">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['nama_akun'].'</span>'.button_mapping($sub_bl['kode_sbl'].'-'.$akun_4_db[0]['kode_akun']).'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}

		// rekening 5
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
					<td class="kanan bawah text_blok text_kanan">Rp. '.$this->ubah_minus($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['total']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['total_murni']).'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5"><span class="nama">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['nama_akun'].'</span>'.button_mapping($sub_bl['kode_sbl'].'-'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['kode_akun']).'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}

		// kelompok / paket
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
					<td class="kanan bawah text_blok text_kanan nilai_kelompok">Rp. '.$this->ubah_minus($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['total']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['total_murni']).'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5"><span class="nama">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['nama_akun'].'</span>'.button_mapping($sub_bl['kode_sbl'].'-'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['kode_akun'].'-'.$item['idsubtitle']).'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok nilai_kelompok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}

		// keterangan
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
					<td class="kanan bawah text_blok text_kanan nilai_keterangan">Rp. '.$this->ubah_minus($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['total']-$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['total_murni']).'
				';
			}
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5"><span class="nama">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['nama_akun'].'</span>'.button_mapping($sub_bl['kode_sbl'].'-'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['kode_akun'].'-'.$item['idsubtitle'].'-'.$item['idketerangan']).'</td>
                    '.$rin_murni.'
                    <td class="kanan bawah text_kanan text_blok nilai_keterangan" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['total'],0,",",".").'</td>
                    '.$selisih_murni.'
                </tr>
			';
		}

		// kommponen
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
				<td class="kanan bawah text_kanan" style="vertical-align: middle;white-space:nowrap">Rp. '.$this->ubah_minus($item['total_harga']-$item['rincian_murni']).'</td>
			';
		}
		$rin_sub_item .= '
			<tr class="data-komponen">
				<td class="kiri kanan bawah text_blok">&nbsp;</td>
                <td class="kanan bawah">
                    <div><span class="nama">'.$item['nama_komponen'].'</span>'.button_mapping($sub_bl['kode_sbl'].'-'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['kode_akun'].'-'.$item['idsubtitle'].'-'.$item['idketerangan'].'-'.$item['id_rinci_sub_bl']).'</div>
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
			<td class="kanan bawah text_blok text_kanan" style="white-space:nowrap">Rp. '.$this->ubah_minus($total_sub_rinc - $total_sub_rinc_murni).'</td>
		';
	}
	$rin_sub_item .= '
		<tr>
            '.$rin_murni.'
            <td colspan="'.$colspan.'" class="kiri kanan bawah text_kanan text_blok">Jumlah Anggaran Sub Kegiatan :</td>
            <td class="kanan bawah text_blok text_kanan subkeg-total" style="white-space:nowrap" data-kdsbl="'.$sub_bl['kode_sbl'].'">Rp. '.number_format($total_sub_rinc,0,",",".").'</td>
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
	.cellpadding_1 > tbody > tr > td, .cellpadding_1 > thead > tr > th {
		padding: 1px;
	}
	.cellpadding_2 > tbody > tr > td, .cellpadding_2 > thead > tr > th {
		padding: 2px;
	}
	.cellpadding_3 > tbody > tr > td, .cellpadding_3 > thead > tr > th {
		padding: 3px;
	}
	.cellpadding_4 > tbody > tr > td, .cellpadding_4 > thead > tr > th {
		padding: 4px;
	}
	.cellpadding_5 > tbody > tr > td, .cellpadding_5 > thead > tr > th {
		padding: 5px;
	}
	.no_padding, .no_padding>td {
		padding: 0 !important;
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
	    border-collapse: collapse;
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
<div class="modal fade" id="mod-mapping" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true" style="z-index: 99999">'
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Mapping Label & Sumber Dana</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<form>
                  	<div class="form-group">
                  		<table class="table table-bordered">
                  			<tbody>
                  				<tr>
                  					<td width="150px;">Nama Sub Kegiatan</td>
                  					<td width="25px">:</td>
                  					<td id="mapping_nama_subkeg"></td>
                  				</tr>
                  				<tr>
                  					<td>Total Rincian</td>
                  					<td>:</td>
                  					<td id="mapping_total_rincian"></td>
                  				</tr>
                  				<tr>
                  					<td>Sumber Dana</td>
                  					<td>:</td>
                  					<td id="mapping_sumberdana_subkeg"></td>
                  				</tr>
                  				<tr id="wrap-rek_1">
                  					<td>Rekening 1</td>
                  					<td>:</td>
                  					<td id="mapping_rek_1"></td>
                  				</tr>
                  				<tr id="wrap-rek_2">
                  					<td>Rekening 2</td>
                  					<td>:</td>
                  					<td id="mapping_rek_2"></td>
                  				</tr>
                  				<tr id="wrap-rek_3">
                  					<td>Rekening 3</td>
                  					<td>:</td>
                  					<td id="mapping_rek_3"></td>
                  				</tr>
                  				<tr id="wrap-rek_4">
                  					<td>Rekening 4</td>
                  					<td>:</td>
                  					<td id="mapping_rek_4"></td>
                  				</tr>
                  				<tr id="wrap-rek_5">
                  					<td>Rekening 5</td>
                  					<td>:</td>
                  					<td id="mapping_rek_5"></td>
                  				</tr>
                  				<tr id="wrap-kelompok">
                  					<td>Kelompok/Paket</td>
                  					<td>:</td>
                  					<td id="mapping_kelompok"></td>
                  				</tr>
                  				<tr id="wrap-keterangan">
                  					<td>Keterangan</td>
                  					<td>:</td>
                  					<td id="mapping_keterangan"></td>
                  				</tr>
                  				<tr id="wrap-item">
                  					<td>Komponen/Item Rincian</td>
                  					<td>:</td>
                  					<td id="mapping_item"></td>
                  				</tr>
                  			</tbody>
                  		</table>
                  		<input type="hidden" id="mapping_id" />
                  	</div>
                  	<div class="form-group">
                  		<label class="control-label" style="display: block;">Pilih Sumber Dana</label>
                  		<select style="width: 100%;" id="mapping_sumberdana" multiple="multiple"></select>
                  	</div>
                  	<div class="form-group">
                  		<label class="control-label" style="display: block;">Pilih Label</label>
                  		<select style="width: 100%;" id="mapping_label" multiple="multiple">
                  		<?php
                  			foreach ($data_label_komponen as $k => $sd) {
                  				echo '<option value="'.$sd['id'].'">'.$sd['nama'].'</option>';
                  			}
                  		?>
                  		</select>
                  	</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="set-mapping">Simpan</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="cetak" contenteditable="true">
	<table width="100%" class="cellpadding_5" style="border-spacing: 2px;">
	    <tr class="no_padding">
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
	    <tr class="no_padding">
	        <td colspan="2">
	            <table width="100%" class="cellpadding_5" style="border-spacing: 1px;">
	            	<?php
						if(
							$type == 'dpa_murni'
							|| $type == 'dpa_perubahan'
						){
							echo '
								<tr class="text_blok '.$class_garis_table.'">
				                    <td width="150">Nomor DPA</td>
				                    <td width="10">:</td>
				                    <td>XXXXXX</td>
				                </tr>
							';
						}
					?>
	                <tr class="<?php echo $class_garis_table; ?>">
	                    <td width="150">Urusan Pemerintahan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_urusan']; ?> <?php echo $bl[0]['nama_urusan']; ?></td>
	                </tr>
	                <tr class="<?php echo $class_garis_table; ?>">
	                    <td width="150">Bidang Urusan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_bidang_urusan']; ?> <?php echo $bl[0]['nama_bidang_urusan']; ?></td>
	                </tr>
	                <tr class="<?php echo $class_garis_table; ?>">
	                    <td width="150">Program</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_program']; ?> <?php echo $bl[0]['nama_program']; ?></td>
	                </tr>
	                <tr class="<?php echo $class_garis_table; ?>">
	                    <td width="150">Sasaran Program</td>
	                    <td width="10">:</td>
	                    <td><?php echo $sasaran; ?></td>
	                </tr>
	                <tr class="<?php echo $class_garis_table; ?>" valign="top">
	                    <td width="150">Capaian Program</td>
	                    <td width="10">:</td>
	                    <td>
	                        <table width="50%" border="0" style="border-spacing: 0px;">
	                            <tr>
	                            <?php
								if(
									$type == 'dpa_murni'
									|| $type == 'dpa_perubahan'
								):
								?>
	                            	<td>(Indikator)</td>
	                            	<td>(Target)</td>
	                            <?php else: ?>
	                            	<td>Indikator</td>
	                            	<td>Target</td>
	                            <?php endif; ?>
	                            </tr>
								<?php echo implode('', $ind_prog); ?>
	                        </table>
	                </td>
	                </tr>
	                <tr class="<?php echo $class_garis_table; ?>">
	                    <td width="150">Kegiatan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_giat']; ?> <?php echo $bl[0]['nama_giat']; ?></td>
	                </tr>
	                <tr class="<?php echo $class_garis_table; ?>">
	                    <td width="150">Organisasi</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_skpd']; ?>&nbsp;<?php echo $bl[0]['nama_skpd']; ?></td>
	                </tr>
	                <tr class="<?php echo $class_garis_table; ?>">
	                    <td width="150">Unit</td>
	                    <td width="10">:</td>
	                    <td><?php echo $bl[0]['kode_sub_skpd']; ?>&nbsp;<?php echo $bl[0]['nama_sub_skpd']; ?></td>
	                </tr>
	                <tr class="<?php echo $class_garis_table; ?>">
	                    <td width="150">Alokasi Tahun <?php echo $tahun_anggaran-1; ?></td>
	                    <td width="10">:</td>
	                    <td>Rp. <?php echo number_format($pagu_n_lalu,0,",","."); ?>
                    	<?php
							if(
								$type == 'dpa_murni'
								|| $type == 'dpa_perubahan'
							){
								echo '('.$this->terbilang($pagu_n_lalu).')';
							}
						?>
						</td>
	                </tr>
	                <tr class="<?php echo $class_garis_table; ?>">
	                    <td width="150">Alokasi Tahun <?php echo $tahun_anggaran; ?></td>
	                    <td width="10">:</td>
	                    <td>Rp. <?php echo number_format($pagu,0,",","."); ?>
                    	<?php
							if(
								$type == 'dpa_murni'
								|| $type == 'dpa_perubahan'
							){
								echo '('.$this->terbilang($pagu).')';
							}
						?></td>
	                </tr>
	                <tr class="<?php echo $class_garis_table; ?>">
	                    <td width="150">Alokasi Tahun <?php echo $tahun_anggaran+1; ?></td>
	                    <td width="10">:</td>
	                    <td>Rp. <?php echo number_format($pagu_n_depan,0,",","."); ?>
                    	<?php
							if(
								$type == 'dpa_murni'
								|| $type == 'dpa_perubahan'
							){
								echo '('.$this->terbilang($pagu_n_depan).')';
							}
						?></td>
	                </tr>
	            </table>
	        </td>            
	    </tr>
	    <tr>
	        <td class="atas kanan bawah kiri text_15 text_tengah" colspan="2">Indikator &amp; Tolok Ukur Kinerja Kegiatan</td>
	    </tr>        
	    <tr class="no_padding">
	        <td colspan="2">
	            <table width="100%" class="cellpadding_5 td_v_middle" style="border-spacing: 2px;">
	            <?php 
            		$capaian_kegiatan_murni = '';
            		$masukan_kegiatan_murni = '';
            		$keluaran_kegiatan_murni = '';
            		$hasil_kegiatan_murni = '';
	            	if(
	            		$type == 'rka_murni'
	            		|| $type == 'dpa_murni'
	            	){
		                echo '
			                <tr>
			                    <td width="130" class="text_tengah kiri atas kanan bawah">Indikator</td>
			                    <td class="text_tengah kiri atas kanan bawah">Tolok Ukur Kinerja</td>
			                    <td width="150" class="text_tengah kiri atas kanan bawah">Target Kinerja</td>
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
			                    <td width="150" class="text_tengah kiri atas kanan bawah">Target Kinerja</td>
			                    <td class="text_tengah kiri atas kanan bawah">Tolok Ukur Kinerja</td>
			                    <td width="150" class="text_tengah kiri atas kanan bawah">Target Kinerja</td>
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
	                <tr>
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
	        <td class="<?php echo $class_garis_table; ?>" width="150" colspan="2">Kelompok Sasaran Kegiatan : <?php echo $bl[0]['sasaran'];?></td>
	    </tr>
	    <tr>
	        <td class="<?php echo $class_garis_table; ?>" width="150" colspan="2">&nbsp;</td>
	    </tr>
	    <?php
			if(
				$type == 'rka_murni'
				|| $type == 'rka_perubahan'
			){
				echo '
					<tr>
				        <td class="atas kanan bawah kiri text_tengah text_15" colspan="2">
				            <table width="100%" class="cellpadding_5" style="border-spacing: 0px;" >
				                <tr>
				                	<td>'.$judul_rincian.'</td>
				                </tr>
				            </table>
				        </td>
				    </tr>
				';
			}
		?>
	    <tr class="no_padding">
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
								<td class="kanan bawah text_blok text_kanan">'.$this->ubah_minus($total_pagu - $total_pagu_murni).'</td>
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
    <?php
    $tgl_laporan = date('d ').$this->get_bulan(date('m')).date(' Y');
    if(
		$type == 'dpa_murni'
		|| $type == 'dpa_perubahan'
	):
    	$_POST['api_key'] = $api_key;
    	$_POST['action'] = 'get_kas';
    	$_POST['kode_giat'] = $bl[0]['kode_giat'];
    	$_POST['kode_skpd'] = $bl[0]['kode_skpd'];
    	$_POST['tahun_anggaran'] = $input['tahun_anggaran'];
    	$kas = $this->get_kas(true);
    	$kas = $kas['data'];
    	$user_ppkd_db = $wpdb->get_results("select fullName, nip from data_user_penatausahaan where namaJabatan='BENDAHARA UMUM DAERAH'", ARRAY_A);
    	$user_ppkd = 'XXXXXX';
    	$user_ppkd_nip = 'XXXXXX';
    	if(!empty($user_ppkd_db)){
    		$user_ppkd = $user_ppkd_db[0]['fullName'];
    		$user_ppkd_nip = $user_ppkd_db[0]['nip'];
    	}
    ?>
    	<tr class="no_padding">
    		<td>
    			<table width="100%" style="border-collapse: collapse;" class="cellpadding_5">
			    	<tr>
			            <td class="kiri kanan atas bawah text_blok text_tengah" colspan="2">Rencana Penarikan Dana per Bulan</td>
			            <td width="60%" class="kiri kanan atas bawah" rowspan="14" style="vertical-align: middle;">
			                <table class="tabel-standar" width="100%" cellpadding="2">
			                    <tbody>
			                    	<tr>
			                            <td class="text_tengah"><?php echo carbon_get_theme_option('crb_daerah'); ?> , Tanggal <?php echo $tgl_laporan; ?></td>
			                        </tr>
			                        <tr><td class="text_tengah" style="font-size: 110%;">Kepala&nbsp;<?php echo $unit[0]['namaunit']; ?></td></tr>
			                        <tr><td height="80">&nbsp;</td></tr>
			                        <tr><td class="text_tengah text-u"><?php echo $unit[0]['namakepala']; ?></td></tr>
			                        <tr><td class="text_tengah">NIP: <?php echo $unit[0]['nipkepala']; ?></td></tr>
			                        <tr><td>&nbsp;</td></tr>
                                    <tr><td class="text_tengah">Mengesahkan,</td></tr>
                                    <tr><td class="text_tengah">PPKD</td></tr>
                                    <tr><td height="80">&nbsp;</td></tr>
                                    <tr><td class="text_tengah text-u"><?php echo $user_ppkd; ?></td></tr>
                                    <tr><td class="text_tengah">NIP: <?php echo $user_ppkd_nip; ?></td></tr>
			                    </tbody>
			                </table>
			            </td>
			        </tr>
				    <tr>
			            <td width="20%" class="kiri kanan atas bawah">Januari</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][0],0,",","."); ?></td>
			        </tr>
			        <tr>
			            <td width="20%" class="kiri kanan atas bawah">Februari</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][1],0,",","."); ?></td>
			        </tr>
			        <tr>
			            <td width="20%" class="kiri kanan atas bawah">Maret</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][2],0,",","."); ?></td>
			        </tr>
			        <tr>
			            <td width="20%" class="kiri kanan atas bawah">April</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][3],0,",","."); ?></td>
			        </tr>
			        <tr>
			            <td width="20%" class="kiri kanan atas bawah">Mei</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][4],0,",","."); ?></td>
			        </tr>
			        <tr>
			            <td width="20%" class="kiri kanan atas bawah">Juni</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][5],0,",","."); ?></td>
			        </tr>
			        <tr>
			            <td width="20%" class="kiri kanan atas bawah">Juli</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][6],0,",","."); ?></td>
			        </tr>
			        <tr>
			            <td width="20%" class="kiri kanan atas bawah">Agustus</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][7],0,",","."); ?></td>
			        </tr>
			        <tr>
			            <td width="20%" class="kiri kanan atas bawah">September</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][8],0,",","."); ?></td>
			        </tr>
			        <tr>
			            <td width="20%" class="kiri kanan atas bawah">Oktober</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][9],0,",","."); ?></td>
			        </tr>
			        <tr>
			            <td width="20%" class="kiri kanan atas bawah">November</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][10],0,",","."); ?></td>
			        </tr>
			        <tr>
			            <td width="20%" class="kiri kanan atas bawah">Desember</td>
			            <td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][11],0,",","."); ?></td>
			        </tr>
			        <tr>
				        <td class="kiri kanan atas bawah text_tengah">Jumlah</td>
				        <td class="kiri kanan atas bawah text_kanan">Rp  <?php echo number_format($kas['total'],0,",","."); ?></td>
				    </tr>
    			</table>
    		</td>
    	</tr>
	<?php else: ?>
	    <tr>
	        <td class="kiri kanan atas bawah" width="350" valign="top">
	        &nbsp;
	        </td>
	        <td class="kiri kanan atas bawah" width="250" valign="top">
	            <table width="100%" class="cellpadding_2" style="border-spacing: 0px;">
	                <tr><td colspan="3" class="text_tengah"><?php echo carbon_get_theme_option('crb_daerah'); ?> , Tanggal <?php echo $tgl_laporan; ?></td></tr>
                    <tr><td colspan="3" class="text_tengah text_15">Kepala&nbsp;<?php echo $unit[0]['namaunit']; ?></td></tr>
	                <tr><td colspan="3" height="80">&nbsp;</td></tr>
	                <tr><td colspan="3" class="text_tengah"><?php echo $unit[0]['namakepala']; ?></td></tr>
	                <tr><td colspan="3" class="text_tengah">NIP: <?php echo $unit[0]['nipkepala']; ?></td></tr>
                </table>
	        </td>
	    </tr>
	<?php endif; ?>
    <?php
    if(
		$type == 'rka_murni'
		|| $type == 'dpa_murni'
	):
    ?>
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
	<?php endif; ?>
	    <tr class="no_padding">
	        <td colspan="2">
	            <table width="100%" class="cellpadding_5" style="border-spacing: 0px;">
	                <tr><td colspan="5" class="kiri kanan atas bawah text_tengah">Tim Anggaran Pemerintah Daerah</td></tr>
	                <tr class="text_tengah">
	                    <td width="10" class="kiri kanan bawah">No.</td>
	                    <td class="kanan bawah">Nama</td>
	                    <td width="200" class="bawah kanan">NIP</td>
	                    <td width="200" class="bawah kanan">Jabatan</td>
	                    <td width="200" class="bawah kanan">Tanda Tangan</td>
	                </tr>
	                <?php
	                	$tapd = $wpdb->get_results("
	                		select 
	                			* 
	                		from data_user_tapd_sekda 
	                		where tahun_anggaran=".$input['tahun_anggaran'].'
	                			and active=1
	                			and type=\'tapd\'
	                		order by no_urut', ARRAY_A
	                	);
	                	for ($i=0; $i < 8; $i++) { 
	                		$no = $i+1;
	                		$nama = '&nbsp;';
	                		$nip = '&nbsp;';
	                		$jabatan = '&nbsp;';
	                		if(!empty($tapd[$i])){
	                			$nama = $tapd[$i]['nama'];
	                			$nip = $tapd[$i]['nip'];
	                			$jabatan = $tapd[$i]['jabatan'];
	                		}
	                		echo '
				                <tr>
				                    <td width="10" class="kiri kanan bawah">'.$no.'.</td>
				                    <td class="bawah kanan">'.$nama.'</td>
				                    <td class="bawah kanan">'.$nip.'</td>
				                    <td class="bawah kanan">'.$jabatan.'</td>
	                    			<td class="bawah kanan">&nbsp;</td>
				                </tr>
	                		';
	                	}
	                ?>
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
	function tampil_rinci(that){
		if(jQuery(that).is(':checked')){
			jQuery('.profile-penerima').show();
		}else{
			jQuery('.profile-penerima').hide();
		}
	}
	function mapping_label_sumberdana(that){
		jQuery('.mapping').remove();
		if(jQuery(that).is(':checked')){
			jQuery('#wrap-loading').show();
			jQuery('.cetak').attr('contenteditable', false);
			jQuery('.edit-mapping').show();
			jQuery('.edit-sumber-dana').show();
			var sendData = jQuery('.data-komponen').map(function(i, b) {
				return new Promise(function(resolve, reject){
					var mapping = jQuery(b).find('.edit-mapping');
					var id_unik = mapping.attr('data-id');
					jQuery.ajax({
						url: ajax.url,
			          	type: "post",
			          	data: {
			          		"action": "get_mapping",
			          		"api_key": "<?php echo $api_key; ?>",
			          		"id_unik": id_unik
			          	},
			          	dataType: "json",
			          	success: function(data){
							var sumberdana = ''
								+'<ul class="list-mapping">'
									+'<li><span class="badge badge-primary mapping">DAU</span></li>'
								+'</ul>';
							var label = ''
								+'<ul class="list-mapping">'
									+'<li><span class="badge badge-success mapping">Covid</span></li>'
								+'</ul>';
							mapping.before(sumberdana+label);
							return resolve(true);
						},
						error: function(e) {
							console.log(e);
							return resolve(true);
						}
					});
                })
                .catch(function(e){
                    console.log(e);
                    return Promise.resolve(true);
                });
			});

			Promise.all(sendData)
        	.then(function(val_all){
				jQuery('#wrap-loading').hide();
            })
            .catch(function(err){
                console.log('err', err);
            });
		}else{
			jQuery('.cetak').attr('contenteditable', true);
			jQuery('.edit-mapping').hide();
		}
	}
	jQuery(document).ready(function(){
		window.master_sumberdana = <?php echo json_encode($data_sumber_dana); ?>;
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
		    +'</label>'
			+'<label style="margin-left: 20px;"><input type="checkbox" onclick="mapping_label_sumberdana(this);"> Mapping Label & Sumber Dana</label>';
		var aksi = ''
			+'<div id="action-sipd" class="hide-print">'
				+body
			+'</div>';
		jQuery('body').prepend(aksi);

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
	    jQuery('.edit-mapping').on('click', function(){
	    	jQuery('#wrap-rek_2').hide();
	    	jQuery('#wrap-rek_3').hide();
	    	jQuery('#wrap-rek_4').hide();
	    	jQuery('#wrap-rek_5').hide();
	    	jQuery('#wrap-kelompok').hide();
	    	jQuery('#wrap-keterangan').hide();
	    	jQuery('#wrap-item').hide();

	    	var id = jQuery(this).attr('data-id');
	    	var ids = id.split('-');
	    	var kd_sbl = ids[0];
	    	var rek = ids[1].split('.');
	    	var rek_1 = rek[0]+'.'+rek[1];
	    	var rek_2 = rek[0]+'.'+rek[1]+'.'+rek[2];
	    	var rek_3 = rek[0]+'.'+rek[1]+'.'+rek[2]+'.'+rek[3];
	    	var rek_4 = rek[0]+'.'+rek[1]+'.'+rek[2]+'.'+rek[3]+'.'+rek[4];
	    	var rek_5 = rek[0]+'.'+rek[1]+'.'+rek[2]+'.'+rek[3]+'.'+rek[4]+'.'+rek[5];
	    	var kelompok = ids[2];
	    	var keterangan = ids[3];
	    	var id_rinci = ids[4];
	    	jQuery('#mapping_rek_1').text(rek_1+' '+jQuery('.edit-mapping[data-id="'+kd_sbl+'-'+rek_1+'"]').closest('td').find('.nama').text().trim());
	    	if(rek[2]){
	    		jQuery('#mapping_rek_2').text(rek_2+' '+jQuery('.edit-mapping[data-id="'+kd_sbl+'-'+rek_2+'"]').closest('td').find('.nama').text().trim());
	    		jQuery('#wrap-rek_2').show();
	    	}
	    	if(rek[3]){
	    		jQuery('#mapping_rek_3').text(rek_3+' '+jQuery('.edit-mapping[data-id="'+kd_sbl+'-'+rek_3+'"]').closest('td').find('.nama').text().trim());
	    		jQuery('#wrap-rek_3').show();
	    	}
	    	if(rek[4]){
	    		jQuery('#mapping_rek_4').text(rek_4+' '+jQuery('.edit-mapping[data-id="'+kd_sbl+'-'+rek_4+'"]').closest('td').find('.nama').text().trim());
	    		jQuery('#wrap-rek_4').show();
	    	}
	    	if(rek[5]){
	    		jQuery('#mapping_rek_5').text(rek_5+' '+jQuery('.edit-mapping[data-id="'+kd_sbl+'-'+rek_5+'"]').closest('td').find('.nama').text().trim());
	    		jQuery('#wrap-rek_5').show();
	    	}
	    	if(ids[2]){
	    		jQuery('#mapping_kelompok').text(jQuery('.edit-mapping[data-id="'+kd_sbl+'-'+rek_5+'-'+kelompok+'"]').closest('td').find('.nama').text().trim());
	    		jQuery('#wrap-kelompok').show();
	    	}
	    	if(ids[3]){
	    		jQuery('#mapping_keterangan').text(jQuery('.edit-mapping[data-id="'+kd_sbl+'-'+rek_5+'-'+kelompok+'-'+keterangan+'"]').closest('td').find('.nama').text().trim());
	    		jQuery('#wrap-keterangan').show();
	    	}
	    	if(ids[3]){
	    		jQuery('#mapping_item').text(jQuery('.edit-mapping[data-id="'+kd_sbl+'-'+rek_5+'-'+kelompok+'-'+keterangan+'-'+id_rinci+'"]').closest('td').find('.nama').text().trim());
	    		jQuery('#wrap-item').show();
	    	}

	    	var nama_sub_keg = jQuery('.subkeg[data-kdsbl="'+kd_sbl+'"]').text();
	    	var total_sub_keg = jQuery('.subkeg-total[data-kdsbl="'+kd_sbl+'"]').text();
	    	var sumberdana_sub_keg = jQuery('.subkeg-sumberdana[data-kdsbl="'+kd_sbl+'"]').text();
	    	var id_sumberdana_sub_keg = jQuery('.subkeg-sumberdana[data-kdsbl="'+kd_sbl+'"]').attr('data-idsumberdana').split(',');
	    	jQuery('#mapping_nama_subkeg').text(nama_sub_keg);
	    	jQuery('#mapping_total_rincian').text(total_sub_keg);
	    	jQuery('#mapping_sumberdana_subkeg').text(sumberdana_sub_keg);
	    	
	    	jQuery('#mapping_id').val(id);
	    	var option_sumber_dana = '';
	    	master_sumberdana.map(function(b, i){
	    		if(id_sumberdana_sub_keg.indexOf(b.id_dana) != -1){
	    			option_sumber_dana += '<option value="'+b.id_dana+'">'+b.nama_dana+'</option>';
	    		}
	    	});
	    	jQuery('#mapping_sumberdana').html(option_sumber_dana);
	    	jQuery('#mapping_sumberdana').val(id_sumberdana_sub_keg).trigger('change');
	    	var label = [];
	    	jQuery('#mapping_label').val(label).trigger('change');
	    	jQuery('#mod-mapping').modal('show');
	    });
	    jQuery('#mapping_sumberdana').select2();
	    jQuery('#mapping_label').select2();
	});

</script>