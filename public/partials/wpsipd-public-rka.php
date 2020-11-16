<?php
$input = shortcode_atts( array(
	'kode_bl' => '',
), $atts );
global $wpdb;

if(empty($input['kode_bl'])){
	echo "<h1 style='text-align: center;'>kode_bl tidak boleh kosong!</h1>"; exit;
}

$bl = $wpdb->get_results("
	SELECT 
		* 
	from data_sub_keg_bl 
	where kode_bl='".$input['kode_bl']."'"
, ARRAY_A);

$unit = $wpdb->get_results("
	SELECT 
		* 
	from data_unit 
	where idinduk=".$bl[0]['id_sub_skpd']
, ARRAY_A);
// print_r($bl); die();
$bulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

$tahun_anggaran = $bl[0]['tahun_anggaran'];
$pagu = 0;
$pagu_n_lalu = 0;
$pagu_n_depan = 0;
$rin_sub = '';
$total_pagu = 0;
foreach ($bl as $k => $sub_bl) {
	$total_pagu += $sub_bl['pagu_keg'];
	$pagu += $sub_bl['pagu'];
	$pagu_n_lalu += $sub_bl['pagu_n_lalu'];
	$pagu_n_depan += $sub_bl['pagu_n_depan'];
	$sql = "
		SELECT 
			* 
		from data_sub_keg_indikator 
		where kode_sbl='".$sub_bl['kode_sbl']."'";
	$indikator_sub_keg = $wpdb->get_results($sql, ARRAY_A);
	$indikator_sub = '';
	foreach ($indikator_sub_keg as $key => $ind) {
		$indikator_sub .= '
			<tr>
                <td width="495">'.$ind['outputteks'].'</td>
                <td width="495">'.$ind['targetoutputteks'].'</td>
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
                        <td>'.$sub_bl['nama_dana'].'</td>
                    </tr>
                    <tr>
                        <td width="130">Lokasi</td>
                        <td width="10">:</td>
                        <td>'.$sub_bl['nama_lokasi'].'</td>
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
                            <table width="100%" border="0" style="border-spacing: 0px;">
                                <tr>
                                	<td>Indikator</td>
                                	<td>Target</td>
                                </tr>
                                '.$indikator_sub.'
                            </table>
                         </td>
                    </tr>
                </table>
            </td>
    	</tr>
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
	$rinc = $wpdb->get_results("
		SELECT 
			* 
		from data_rka 
		where kode_sbl='".$sub_bl['kode_sbl']."'
		Order by kode_akun ASC"
	, ARRAY_A);
	$rin_sub_item = '';
	$total_sub_rinc = 0;
	$akun = array();
	$total_subs_bl_teks = array();
	foreach ($rinc as $key => $item) {
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
				'status' => 0,
				'kode_akun' => $akun_1_db[0]['kode_akun'],
				'nama_akun' => $akun_1_db[0]['nama_akun']
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']])){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']] = array(
				'total' => 0,
				'status' => 0,
				'kode_akun' => $akun_2_db[0]['kode_akun'],
				'nama_akun' => $akun_2_db[0]['nama_akun']
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']])){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']] = array(
				'total' => 0,
				'status' => 0,
				'kode_akun' => $akun_3_db[0]['kode_akun'],
				'nama_akun' => $akun_3_db[0]['nama_akun']
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']])){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']] = array(
				'total' => 0,
				'status' => 0,
				'kode_akun' => $akun_4_db[0]['kode_akun'],
				'nama_akun' => $akun_4_db[0]['nama_akun']
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']])){
			$nama_akun = str_replace($item['kode_akun'], '', $item['nama_akun']);
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']] = array(
				'total' => 0,
				'status' => 0,
				'kode_akun' => $item['kode_akun'],
				'nama_akun' => $nama_akun
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']])){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']] = array(
				'total' => 0,
				'status' => 0,
				'kode_akun' => '&nbsp;',
				'nama_akun' => '[#] '.$item['subs_bl_teks']
			);
		}
		if(empty($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']])){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']] = array(
				'total' => 0,
				'status' => 0,
				'kode_akun' => '&nbsp;',
				'nama_akun' => '[-] '.$item['ket_bl_teks']
			);
		}
		$akun[$akun_1_db[0]['kode_akun']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['total'] += $item['total_harga'];
		$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['total'] += $item['total_harga'];
	}
	// print_r($akun); die();
	foreach ($rinc as $key => $item) {
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
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']]['nama_akun'].'</td>
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']]['total'],2,",",".").'</td>
                </tr>
			';
		}
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['status'] = 1;
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['nama_akun'].'</td>
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']]['total'],2,",",".").'</td>
                </tr>
			';
		}
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['status'] = 1;
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['nama_akun'].'</td>
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']]['total'],2,",",".").'</td>
                </tr>
			';
		}
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['status'] = 1;
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['nama_akun'].'</td>
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]['total'],2,",",".").'</td>
                </tr>
			';
		}
		// print_r($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']]);
		// print_r($item['nama_akun']);
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['status'] = 1;
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['nama_akun'].'</td>
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']]['total'],2,",",".").'</td>
                </tr>
			';
		}
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['status'] = 1;
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['nama_akun'].'</td>
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']]['total'],2,",",".").'</td>
                </tr>
			';
		}
		if($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['status'] == 0){
			$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['status'] = 1;
			$rin_sub_item .= '
				<tr>
	                <td class="kiri kanan bawah text_blok">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['kode_akun'].'</td>
                    <td class="kanan bawah text_blok" colspan="5">'.$akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['nama_akun'].'</td>
                    <td class="kanan bawah text_kanan text_blok" style="white-space:nowrap">Rp. '.number_format($akun[$akun_1_db[0]['kode_akun']][$akun_2_db[0]['kode_akun']][$akun_3_db[0]['kode_akun']][$akun_4_db[0]['kode_akun']][$item['nama_akun']][$item['subs_bl_teks']][$item['ket_bl_teks']]['total'],2,",",".").'</td>
                </tr>
			';
		}
		$rin_sub_item .= '
			<tr>
				<td class="kiri kanan bawah text_blok">&nbsp;</td>
                <td class="kanan bawah">
                    <div>'.$item['nama_komponen'].'</div>
                    <div style="margin-left: 20px">'.$item['spek_komponen'].'</div>
                </td>
                <td class="kanan bawah" style="vertical-align: middle;">'.$item['koefisien'].'</td>
                <td class="kanan bawah" style="vertical-align: middle;">'.$item['satuan'].'</td>
                <td class="kanan bawah text_kanan" style="vertical-align: middle;">'.number_format($item['harga_satuan'],2,",",".").'</td>
                <td class="kanan bawah text_kanan" style="vertical-align: middle;">'.number_format($item['totalpajak'],2,",",".").'</td>
                <td class="kanan bawah text_kanan" style="vertical-align: middle;white-space:nowrap">Rp. '.number_format($item['total_harga'],2,",",".").'</td>
            </tr>
		';
		$total_sub_rinc += $item['total_harga'];
	}
	$rin_sub_item .= '
		<tr>
            <td colspan="6" class="kiri kanan bawah text_kanan text_blok">Jumlah Anggaran Sub Kegiatan :</td>
            <td class="kanan bawah text_blok text_kanan" style="white-space:nowrap">Rp. '.number_format($total_sub_rinc,2,",",".").'</td>
        </tr>
	';
	$rin_sub .= $rin_sub_item;
}

?>

<style type="text/css">
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
    .bawah{
        border-bottom:1px solid #000;        
    }
    .kiri{
        border-left:1px solid #000;        
    }
    .kanan{        
        border-right:1px solid #000;
    }
    .atas{        
        border-top:1px solid #000;
    }
    .text_tengah{
        text-align: center;
    }
    .text_kiri{
        text-align: left;
    }
    .text_kanan{
        text-align: right;
    }
    .text_blok{
        font-weight: bold;
    }        
    .text_15{
        font-size: 15px;
    }
    .text_20{
        font-size: 20px;
    }
    .footer{
        display:none;
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
</style>
<div class="cetak">
	<table width="100%" class="cellpadding_1" style="border-spacing: 2px;">
	    <tr>
	        <td colspan="2">
	            <table width="100%" class="cellpadding_5" style="border-spacing: 1px;" class="text_tengah text_15">
	                <tr>
	                    <td class="kiri atas kanan bawah text_blok">RENCANA KERJA DAN ANGGARAN<br/>SATUAN KERJA PERANGKAT DAERAH</td>
	                    <td class="kiri atas kanan bawah text_blok" rowspan="2">Formulir<br/>RKA - RINCIAN BELANJA SKPD</td>
	                </tr>
	                <tr>
	                    <td class="kiri atas kanan bawah">Pemerintah Kabupaten Magetan Tahun Anggaran <?php echo $tahun_anggaran; ?></td>
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
	                    <td><?php echo $bl[0]['sasaran']; ?></td>
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
								<tr>
	                                <td width="495"></td>
	                                <td width="495"></td>
	                            </tr>
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
	                    <td>Rp. <?php echo number_format($pagu_n_lalu,2,",","."); ?></td>
	                </tr>
	                <tr>
	                    <td width="150">Alokasi Tahun <?php echo $tahun_anggaran; ?></td>
	                    <td width="10">:</td>
	                    <td>Rp. <?php echo number_format($pagu,2,",","."); ?></td>
	                </tr>
	                <tr>
	                    <td width="150">Alokasi Tahun <?php echo $tahun_anggaran+1; ?></td>
	                    <td width="10">:</td>
	                    <td>Rp. <?php echo number_format($pagu_n_depan,2,",","."); ?></td>
	                </tr>
	            </table>
	        </td>            
	    </tr>
	    <tr>
	        <td class="atas kanan bawah kiri text_15 text_tengah" colspan="2">Indikator &amp; Tolok Ukur Kinerja Kegiatan</td>
	    </tr>        
	    <tr>
	        <td class="kiri kanan atas bawah" colspan="2">                
	            <table width="100%" class="cellpadding_5" style="border-spacing: 2px;">
	                <tr>
	                    <td width="130" class="text_tengah kiri atas kanan bawah">Indikator</td>
	                    <td class="text_tengah kiri atas kanan bawah">Tolok Ukur Kinerja</td>
	                    <td width="123" class="text_tengah kiri atas kanan bawah">Target Kinerja</td>
	                </tr>
	                                    
	                <tr>
	                    <td width="130" class="kiri kanan atas bawah">Capaian Kegiatan</td>
	                    <td class="kiri kanan atas bawah">
		                    <table width="100%" border="0" style="border-spacing: 0px;">
		                        <tr>
		                        	<td width="495"></td>
		                        </tr>
		                    </table>
		                </td>
		                <td class="kiri kanan atas bawah">
		                    <table width="100%" border="0" style="border-spacing: 0px;">
		                        <tr>
		                        	<td width="495"></td>
		                        </tr>
		                    </table>
		                </td>
	                </tr>
	                <tr class="no_padding">
	                    <td width="130" class="kiri kanan atas bawah">Masukan</td>
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
		                          <td width="495">Rp. <?php echo number_format($pagu,2,",","."); ?></td>
		                        </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td width="130" class="kiri kanan atas bawah">Keluaran</td>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                            <tr>
	                          		<td width="495"></td>
                        		</tr>
	                    	</table>
	                    </td>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                            <tr>
	                          		<td width="495"></td>
	                        	</tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td width="130" class="kiri kanan atas bawah">Hasil</td>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                        </table>
	                    </td>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                        </table>
	                    </td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	    <tr>
	        <td width="150" colspan="2">Kelompok Sasaran Kegiatan : [kelompok sasaran]</td>
	    </tr>
	    <tr>
	        <td width="150" colspan="2">&nbsp;</td>
	    </tr>
	    <tr>
	        <td class="atas kanan bawah kiri text_tengah text_15" colspan="2">
	            <table width="100%" class="cellpadding_5" style="border-spacing: 0px;" >
	                <tr>
	                	<td>Rincian Anggaran Belanja Kegiatan Satuan Kerja Perangkat Daerah</td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	    <tr>
	        <td colspan="2">
	            <table width="100%" class="cellpadding_5" style="border-spacing: 0px;">
	                <tbody>
	                    <?php echo $rin_sub; ?>
	                    <tr class="">
	                        <td colspan="6" class="kiri kanan bawah text_kanan text_blok">Jumlah Total Anggaran Kegiatan :</td>
	                    	<td class="kanan bawah text_blok text_kanan" style="white-space:nowrap">Rp. <?php echo number_format($total_pagu,2,",","."); ?></td>
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
	                <tr><td colspan="3" class="text_tengah">Kabupaten Magetan , Tanggal </td></tr>
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
