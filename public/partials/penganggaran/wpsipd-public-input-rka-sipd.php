<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

global $wpdb;

$input = shortcode_atts( array(
	'id_skpd' => '',
	'kode_sbl' => '',
	'tahun_anggaran' => get_option('_crb_tahun_anggaran_sipd')
), $atts );
$pecah_kode_sbl = explode(".",$input['kode_sbl']);
$id_skpd = (!empty($input['id_skpd'])) ?: $pecah_kode_sbl[1]; 

if(empty($input['kode_sbl'])){
	die('<h1>Kode SBL Kosong</h1>');
}

$type = 'rka_murni';
if (!empty($_GET) && !empty($_GET['type'])) {
	$type = $_GET['type'];
}
$judul_rincian = 'Rincian Anggaran Belanja Kegiatan Satuan Kerja Perangkat Daerah';
$keterangan_sub = '';
$judul = '
	<td class="kiri atas kanan bawah text_blok text_tengah">RENCANA KERJA DAN ANGGARAN<br/>SATUAN KERJA PERANGKAT DAERAH</td>
	<td class="kiri atas kanan bawah text_blok text_tengah" style="vertical-align: middle;" rowspan="2">Formulir<br/>RKA - RINCIAN BELANJA SKPD</td>
';
if ($type == 'rka_perubahan') {
	$judul_rincian = 'Rincian Perubahan Anggaran Belanja Kegiatan Satuan Kerja Perangkat Daerah';
	$judul = '
		<td class="kiri atas kanan bawah text_blok text_tengah">RENCANA KERJA DAN PERUBAHAN ANGGARAN<br/>SATUAN KERJA PERANGKAT DAERAH</td>
		<td class="kiri atas kanan bawah text_blok text_tengah" style="vertical-align: middle;" rowspan="2">Formulir<br/>RKPA - RINCIAN BELANJA SKPD</td>
	';
} else if ($type == 'dpa_murni') {
	$judul_rincian = 'Rincian Anggaran Belanja Kegiatan Satuan Kerja Perangkat Daerah';
	$judul = '
		<td class="kiri atas kanan bawah text_blok text_tengah">DOKUMEN PELAKSANAAN ANGGARAN<br/>SATUAN KERJA PERANGKAT DAERAH</td>
		<td class="kiri atas kanan bawah text_blok text_tengah" style="vertical-align: middle;" rowspan="2">Formulir<br/>DPA-RINCIAN BELANJA SKPD</td>
	';
} else if ($type == 'dpa_perubahan') {
	$judul_rincian = 'Rincian Perubahan Anggaran Belanja Kegiatan Satuan Kerja Perangkat Daerah';
	$judul = '
		<td class="kiri atas kanan bawah text_blok text_tengah">DOKUMEN PELAKSANAAN PERGESERAN ANGGARAN<br/>SATUAN KERJA PERANGKAT DAERAH</td>
		<td class="kiri atas kanan bawah text_blok text_tengah" style="vertical-align: middle;" rowspan="2">Formulir<br/>DPPA-RINCIAN BELANJA SKPD</td>
	';
}

$class_garis_table = '';
if (
	$type == 'dpa_murni'
	|| $type == 'dpa_perubahan'
) {
	$class_garis_table = 'kiri atas kanan bawah';
	$keterangan_sub = '
		<tr class="' . $class_garis_table . '">
			<td width="130">Keterangan</td>
            <td width="10">:</td>
            <td>&nbsp;</td>
        </tr>
	';
}

$current_user = wp_get_current_user();
$api_key = get_option( '_crb_api_key_extension' );
$tombol_copy_data_rincian_rka_sipd = get_option('_crb_show_copy_data_rincian_rka_settings');

$sql = "
SELECT 
    *
FROM data_sub_keg_bl
WHERE tahun_anggaran=%d
    AND kode_sbl=%s
    AND active=1
ORDER BY kode_giat ASC, kode_sub_giat ASC";
$subkeg = $wpdb->get_row($wpdb->prepare($sql, $input['tahun_anggaran'], $input['kode_sbl']), ARRAY_A);

$unit = $wpdb->get_results($wpdb->prepare("
	SELECT
		*
	FROM data_unit
	WHERE id_skpd=%d
		AND tahun_anggaran=%d
		AND active=1
", $subkeg['id_sub_skpd'], $input['tahun_anggaran']), ARRAY_A);

$sql = "
	SELECT 
		d.iddana,
		d.namadana,
		m.kode_dana
	from data_dana_sub_keg d
	left join data_sumber_dana m on d.iddana=m.id_dana
		and d.tahun_anggaran = m.tahun_anggaran
	where kode_sbl='".$input['kode_sbl']."'
		AND d.tahun_anggaran=".$input['tahun_anggaran']."
		AND d.active=1";
$sd_sub_keg = $wpdb->get_results($sql, ARRAY_A);
$sd_sub_id = array();
$sd_sub = array();
foreach ($sd_sub_keg as $key => $sd) {
	$new_sd = explode(' - ', $sd['namadana']);
	if(!empty($new_sd[1])){
		$sd_sub[] = '<span class="kode-dana">'.$sd['kode_dana'].'</span> '.$new_sd[1];
		$sd_sub_id[] = $sd['iddana'];
	}else{
		$sd_sub[] = '<span class="kode-dana">'.$sd['kode_dana'].'</span> '.$sd['namadana'];
		$sd_sub_id[] = $sd['iddana'];
	}
}

$ind_output_db = $wpdb->get_results(
	"
	SELECT 
		* 
	from data_output_giat_sub_keg 
	where kode_sbl='" . $input['kode_sbl'] . "' 
		AND tahun_anggaran=" . $input['tahun_anggaran'] . "
		AND active=1",
	ARRAY_A
);

$ind_output_murni = [];
$target_ind_output_murni = [];
$ind_output = [];
$target_ind_output = [];
foreach ($ind_output_db as $k => $v) {
	$ind_output[] = '
		<tr>
            <td width="495">' . $v['outputteks'] . '</td>
        </tr>
	';
	$target_ind_output[] = '
		<tr>
            <td width="495">' . $v['targetoutputteks'] . '</td>
        </tr>
	';
}

$ind_hasil_db = $wpdb->get_results(
	"
	SELECT 
		* 
	from data_keg_indikator_hasil 
	where kode_sbl='" . $input['kode_sbl'] . "' 
		AND tahun_anggaran=" . $input['tahun_anggaran'] . "
		AND active=1",
	ARRAY_A
);

$ind_hasil_murni = [];
$target_ind_hasil_murni = [];
$ind_hasil = [];
$target_ind_hasil = [];
foreach ($ind_hasil_db as $k => $v) {
	$ind_hasil[] = '
		<tr>
            <td width="495">' . $v['hasilteks'] . '</td>
        </tr>
	';
	$target_ind_hasil[] = '
		<tr>
            <td width="495">' . $v['targethasilteks'] . '</td>
        </tr>
	';
}

$ind_prog_db = $wpdb->get_results(
	"
	SELECT 
		* 
	from data_capaian_prog_sub_keg 
	where kode_sbl='" . $input['kode_sbl'] . "' 
		AND tahun_anggaran=" . $input['tahun_anggaran'] . "
		AND active=1",
	ARRAY_A
);

$ind_capaian_kegiatan_murni = array();
$target_ind_capaian_kegiatan_murni = array();
$ind_capaian_kegiatan = array();
$target_ind_capaian_kegiatan = array();
$ind_prog = array();
foreach ($ind_prog_db as $k => $v) {
	// print_r($v); die();
	if ($type == 'dpa_perubahan') {
		$ind_prog[] = '
			<tr>
	            <td class="kiri atas kanan bawah"></td>
	            <td class="kiri atas kanan bawah"></td>
	            <td class="kiri atas kanan bawah" width="495">' . $v['capaianteks'] . '</td>
	            <td class="kiri atas kanan bawah" width="495">' . $v['targetcapaianteks'] . '</td>
	        </tr>
		';
	} else {
		$ind_prog[] = '
			<tr>
	            <td class="kiri atas kanan bawah" width="495">' . $v['capaianteks'] . '</td>
	            <td class="kiri atas kanan bawah" width="495">' . $v['targetcapaianteks'] . '</td>
	        </tr>
		';
	}
	$ind_capaian_kegiatan[] = '
		<tr>
            <td width="495">' . $v['capaianteks'] . '</td>
        </tr>
	';
	$target_ind_capaian_kegiatan[] = '
		<tr>
            <td width="495">' . $v['targetcapaianteks'] . '</td>
        </tr>
	';
}

$sql = "
	SELECT 
		* 
	from data_sub_keg_indikator 
	where kode_sbl='".$input['kode_sbl']."'
		AND tahun_anggaran=".$input['tahun_anggaran']."
		AND active=1";
$indikator_sub_keg = $wpdb->get_results($sql, ARRAY_A);
// print_r($indikator_sub_keg); die($wpdb->last_query);
$indikator_sub = '';
foreach ($indikator_sub_keg as $key => $ind) {
	$indikator_sub_murni = '';
	if(
		$type == 'rka_perubahan'
		|| $type == 'dpa_perubahan'
	){
		$indikator_sub_murni = '
			<td class="kiri kanan bawah atas"></td>
			<td class="kiri kanan bawah atas"></td>
		';
	}
	$indikator_sub .= '
		<tr>
			'.$indikator_sub_murni.'
            <td class="kiri kanan bawah atas">'.$ind['outputteks'].'</td>
            <td class="kiri kanan bawah atas">'.$ind['targetoutputteks'].'</td>
        </tr>
	';
}

$sql = "
	SELECT 
		* 
	from data_lokasi_sub_keg 
	where kode_sbl='".$input['kode_sbl']."'
		AND tahun_anggaran=".$input['tahun_anggaran']."
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

$table_indikator_sub_keg = '';
if(
	$type == 'rka_perubahan'
	|| $type == 'dpa_perubahan'
){
	$table_indikator_sub_keg = '
		<table class="tabel-indikator" width="100%" border="0" style="border-spacing: 0px;">
			<tr>
				<th class="kiri kanan bawah atas text_tengah" colspan="2">Sebelum Perubahan</th>
				<th class="kiri kanan bawah atas text_tengah" colspan="2">Setelah Perubahan</th>
			</tr>
            <tr>
            	<th class="kiri kanan bawah atas text_tengah" width="495">Indikator</th>
            	<th class="kiri kanan bawah atas text_tengah" width="495">Target</th>
            	<th class="kiri kanan bawah atas text_tengah" width="495">Indikator</th>
            	<th class="kiri kanan bawah atas text_tengah" width="495">Target</th>
            </tr>
            '.$indikator_sub.'
        </table>';

	$header_sub = '
		<tr>
            <td class="kiri kanan bawah atas text_tengah text_blok" rowspan="3" style="vertical-align: middle;">Kode Rekening</td>
            <td class="kanan bawah atas text_tengah text_blok" rowspan="3" style="vertical-align: middle;">Uraian</td>
            <td class="kanan bawah atas text_tengah text_blok" colspan="5">Sebelum Perubahan</td>
            <td class="kanan bawah atas text_tengah text_blok" colspan="5">Setelah Perubahan</td>
            <td class="kanan bawah atas text_tengah text_blok" rowspan="3" style="vertical-align: middle;">Bertambah/ (Berkurang)</td>
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
	$table_indikator_sub_keg = '
		<table class="tabel-indikator" width="100%" border="0" style="border-spacing: 0px;">
            <tr>
            	<th class="kiri kanan bawah atas text_tengah" width="495">Indikator</th>
            	<th class="kiri kanan bawah atas text_tengah" width="495">Target</th>
            </tr>
            '.$indikator_sub.'
        </table>';
	$header_sub = '
		<tr>
            <td class="kiri kanan bawah atas text_tengah text_blok" rowspan="2">Kode Rekening</td>
            <td class="kanan bawah atas text_tengah text_blok" rowspan="2">Uraian</td>
            <td class="kanan bawah atas text_tengah text_blok" colspan="4">Rincian Perhitungan</td>
            <td class="kanan bawah atas text_tengah text_blok" rowspan="2">Jumlah</td>
        </tr>
        <tr>
            <td class="kanan bawah text_tengah text_blok">Koefisien</td>
            <td class="kanan bawah text_tengah text_blok">Satuan</td>
            <td class="kanan bawah text_tengah text_blok">Harga</td>
            <td class="kanan bawah text_tengah text_blok">PPN</td>
        </tr>
	';
}

$bulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
$bulan_awal = '';
if(!empty($subkeg['waktu_awal']) && !empty($bulan[$subkeg['waktu_awal']-1])){
	$bulan_awal = $bulan[$subkeg['waktu_awal']-1];
}
$bulan_akhir = '';
if(!empty($subkeg['waktu_akhir']) && !empty($bulan[$subkeg['waktu_akhir']-1])){
	$bulan_akhir = $bulan[$subkeg['waktu_akhir']-1];
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
	.tabel-indikator td, .tabel-indikator th {
		padding: 2px 4px;
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

    .profile-penerima, .kode-dana {
    	display: none;
    }
    header, nav {
    	display: none;
    }
    .td_v_middle td {
    	vertical-align: middle;
    }
    .no_break {
    	break-inside: inherit;
    }
</style>
<div class="cetak" contenteditable="true">
	<table width="100%" class="cellpadding_5 no_break" style="border-spacing: 2px;">
		<tbody>
		    <tr class="no_padding">
		        <td colspan="2">
		            <table width="100%" class="cellpadding_5" style="border-spacing: 1px;" class="text_tengah text_15">
		            	<tbody>
			                <tr>
			                    <?php echo $judul; ?>
			                </tr>
			                <tr>
			                    <td class="kiri atas kanan bawah text_tengah">Pemerintah <?php echo get_option('_crb_daerah'); ?><br>Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></td>
			                </tr>
			            </tbody>
		            </table>
		        </td>
		    </tr>
		    <tr class="no_padding">
		        <td colspan="2">
		            <table id="tabel-rincian" width="100%" class="cellpadding_5 no_break" style="border-spacing: 1px;">
		            	<tbody>
			            	<?php
							if (
								$type == 'dpa_murni'
								|| $type == 'dpa_perubahan'
							) {
								echo '
										<tr class="text_blok ' . $class_garis_table . '">
						                    <td width="150">Nomor DPA</td>
						                    <td width="10">:</td>
						                    <td>DPA/A.1/'.$subkeg['kode_sub_skpd'].'/001/'.$input['tahun_anggaran'].'</td>
						                </tr>
									';
							}
							?>
			                <tr class="tr-urusan-pemerintahan kiri kanan atas bawah">
			                    <td width="150">Urusan Pemerintahan</td>
			                    <td width="10">:</td>
			                    <td><?php echo $subkeg['nama_urusan']; ?></td>
			                </tr>
			                <tr class="tr-bidang-urusan kiri kanan atas bawah">
			                    <td width="150">Bidang Urusan</td>
			                    <td width="10">:</td>
			                    <td><?php echo $subkeg['nama_bidang_urusan']; ?></td>
			                </tr>
			                <tr class="tr-program kiri kanan atas bawah">
			                    <td width="150">Program</td>
			                    <td width="10">:</td>
			                    <td><?php echo $subkeg['nama_program']; ?></td>
			                </tr>
			                <tr class="tr-kegiatan kiri kanan atas bawah">
			                    <td width="150">Kegiatan</td>
			                    <td width="10">:</td>
			                    <td><?php echo $subkeg['nama_giat'] ?></td>
			                </tr>
			                <tr class="tr-organisasi kiri kanan atas bawah">
			                    <td width="150">Organisasi</td>
			                    <td width="10">:</td>
			                    <td><?php echo $subkeg['kode_sub_skpd'] . " " . $subkeg['nama_sub_skpd'];  ?></td>
			                </tr>
			                <tr class="tr-unit kiri kanan atas bawah">
			                    <td width="150">Unit</td>
			                    <td width="10">:</td>
			                    <td><?php echo $subkeg['kode_skpd'] . " " . $subkeg['nama_skpd'];  ?></td>
			                </tr>
			                <tr class="tr-alokasi-min-1 kiri kanan atas bawah">
			                    <td width="150">Alokasi Tahun - 1</td>
			                    <td width="10">:</td>
			                    <td></td>
			                </tr>
			                <tr class="tr-alokasi kiri kanan atas bawah">
			                    <td width="150">Alokasi Tahun</td>
			                    <td width="10">:</td>
			                    <td class="total_giat">Rp. <?php echo $this->_number_format($subkeg['pagu']);  ?></td>
			                </tr>
			                <tr class="tr-alokasi-plus-1 kiri kanan atas bawah">
			                    <td width="150">Alokasi Tahun + 1</td>
			                    <td width="10">:</td>
			                    <td>Rp. <?php echo $this->_number_format($subkeg['pagu_n_depan']);  ?></td>
			                </tr>
			            </tbody>
		            </table>
		        </td>            
		    </tr>
		    <tr>
				<td class="atas kanan bawah kiri text_15 text_tengah" colspan="2">Indikator &amp; Tolok Ukur Kinerja Kegiatan</td>
			</tr>
			<tr class="no_padding">
				<td colspan="2">
					<table width="100%" class="cellpadding_5 td_v_middle no_break" style="border-spacing: 2px;">
						<tbody>
							<?php
							$capaian_kegiatan_murni = '';
							$masukan_kegiatan_murni = '';
							$keluaran_kegiatan_murni = '';
							$hasil_kegiatan_murni = '';
							if (
								$type == 'rka_murni'
								|| $type == 'dpa_murni'
							) {
								echo '
					                <tr>
					                    <td width="130" class="text_tengah kiri atas kanan bawah">Indikator</td>
					                    <td class="text_tengah kiri atas kanan bawah">Tolok Ukur Kinerja</td>
					                    <td width="150" class="text_tengah kiri atas kanan bawah">Target Kinerja</td>
					                </tr>';
							} else {
								$capaian_kegiatan_murni = '
			            			<td class="kiri kanan atas bawah">
					                    <table width="100%" border="0" style="border-spacing: 0px;">
					                        ' . implode('', $ind_capaian_kegiatan_murni) . '
					                    </table>
					                </td>
					                <td class="kiri kanan atas bawah">
					                    <table width="100%" border="0" style="border-spacing: 0px;">
					                        ' . implode('', $target_ind_capaian_kegiatan_murni) . '
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
					                          <td width="495">Rp. ' . number_format($pagu_murni, 0, ",", ".") . '</td>
					                        </tr>
				                        </table>
				                    </td>';
								$keluaran_kegiatan_murni = '
			            			<td class="kiri kanan atas bawah">
				                        <table width="100%" border="0" style="border-spacing: 0px;">
				                            ' . implode('', $ind_output_murni) . '
				                    	</table>
				                    </td>
				                    <td class="kiri kanan atas bawah">
				                        <table width="100%" border="0" style="border-spacing: 0px;">
				                            ' . implode('', $target_ind_output_murni) . '
				                        </table>
				                    </td>
			            		';
								$hasil_kegiatan_murni = '
				                    <td class="kiri kanan atas bawah">
				                        <table width="100%" border="0" style="border-spacing: 0px;">
				                        	' . implode('', $ind_hasil_murni) . '
				                        </table>
				                    </td>
				                    <td class="kiri kanan atas bawah">
				                        <table width="100%" border="0" style="border-spacing: 0px;">
				                        	' . implode('', $target_ind_hasil_murni) . '
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
										<tbody>
										<?php echo implode('', $ind_capaian_kegiatan); ?>
										</tbody>
									</table>
								</td>
								<td class="kiri kanan atas bawah">
									<table width="100%" border="0" style="border-spacing: 0px;">
										<tbody>
										<?php echo implode('', $target_ind_capaian_kegiatan); ?>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td width="130" class="kiri kanan atas bawah">Masukan</td>
								<?php echo $masukan_kegiatan_murni; ?>
								<td class="kiri kanan atas bawah">
									<table width="100%" border="0" style="border-spacing: 0px;">
										<tbody>
											<tr>
												<td width="495">Dana yang dibutuhkan</td>
											</tr>
										</tbody>
									</table>
								</td>
								<td class="kiri kanan atas bawah">
									<table width="100%" border="0" style="border-spacing: 0px;">
										<tbody>
											<tr>
												<td width="495">Rp. <?php echo number_format($subkeg['pagu'], 0, ",", "."); ?></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td width="130" class="kiri kanan atas bawah">Keluaran</td>
								<?php echo $keluaran_kegiatan_murni; ?>
								<td class="kiri kanan atas bawah">
									<table width="100%" border="0" style="border-spacing: 0px;">
										<tbody>
										<?php echo implode('', $ind_output); ?>
										</tbody>
									</table>
								</td>
								<td class="kiri kanan atas bawah">
									<table width="100%" border="0" style="border-spacing: 0px;">
										<tbody>
										<?php echo implode('', $target_ind_output); ?>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td width="130" class="kiri kanan atas bawah">Hasil</td>
								<?php echo $hasil_kegiatan_murni; ?>
								<td class="kiri kanan atas bawah">
									<table width="100%" border="0" style="border-spacing: 0px;">
										<tbody>
										<?php echo implode('', $ind_hasil); ?>
										</tbody>
									</table>
								</td>
								<td class="kiri kanan atas bawah">
									<table width="100%" border="0" style="border-spacing: 0px;">
										<tbody>
										<?php echo implode('', $target_ind_hasil); ?>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td class="<?php echo $class_garis_table; ?>" width="150" colspan="2">Kelompok Sasaran Kegiatan : <?php echo $subkeg['sasaran']; ?></td>
			</tr>
			<tr>
				<td class="<?php echo $class_garis_table; ?>" width="150" colspan="2">&nbsp;</td>
			</tr>
		    <tr>
				<td class="atas kanan bawah kiri text_tengah text_15" colspan="2">
					<table width="100%" class="cellpadding_5 no_break" style="border-spacing: 0px;">
					    <tbody>
					    	<tr>
					            <td><?php echo $judul_rincian; ?></td>
					        </tr>
					    </tbody>
					</table>
				</td>
			</tr>
		  	<tr class="no_padding">
		    	<td colspan="2" style="">
		          	<table id="table-sub-kegiatan" class="cellpadding_5 no_break">
			            <tbody>
				            <tr class="tr-sub-kegiatan kiri kanan atas bawah">
				              	<td width="130">Sub Kegiatan</td>
		                  		<td width="10">:</td>
						      	<td class="subkeg" data-kdsbl=""><span class="nama_sub"><?php echo $subkeg['nama_sub_giat']; ?></span></td>
						    </tr>
						    <tr class="tr-sumber-pendanaan kiri kanan atas bawah">
						      	<td width="130">Sumber Pendanaan</td>
						      	<td width="10">:</td>
					      	<?php echo '
					      		<td class="subkeg-sumberdana" data-kdsbl="'.$input['kode_sbl'].'" data-idsumberdana="'.implode(',', $sd_sub_id).'">'.implode(', ', $sd_sub).'</td>'; 
					      	?>
						    </tr>
						    <tr class="tr-lokasi kiri kanan atas bawah">
						      	<td width="130">Lokasi</td>
						      	<td width="10">:</td>
						      	<td><?php echo implode(', ', $lokasi_sub); ?></td>
						    </tr>
						    <tr class="tr-waktu-pelaksanaan kiri kanan atas bawah">
						      	<td width="130">Waktu Pelaksanaan</td>
							    <td width="10">:</td>
							    <td><?php echo $bulan_awal.' s.d. '.$bulan_akhir; ?></td>
						    </tr>
						    <tr valign="top" class="kiri kanan atas bawah">
						        <td width="150">Keluaran Sub Kegiatan</td>
						        <td width="10">:</td>
						        <td><?php echo $table_indikator_sub_keg; ?></td>
					      	</tr>
				      	</tbody>
				    </table>
				</td>
			</tr>
		  	<tr class="no_padding no_break">
		    	<td colspan="2" class="no_break">
		            <table width="100%" class="cellpadding_5 no_break" style="border-spacing: 0px;">
		            	<thead>
							<tr>
					      		<td class="kiri kanan bawah atas text_tengah text_blok" rowspan="2">Kode Rekening</td>
					      		<td class="kanan bawah atas text_tengah text_blok" rowspan="2">Uraian</td>
					      		<td class="kanan bawah atas text_tengah text_blok" colspan="4">Rincian Perhitungan</td>
					      		<td class="kanan bawah atas text_tengah text_blok" rowspan="2">Jumlah</td>
					    	</tr>
					    	<tr>
					      		<td class="kanan bawah text_tengah text_blok">Koefisien</td>
					      		<td class="kanan bawah text_tengah text_blok">Satuan</td>
					      		<td class="kanan bawah text_tengah text_blok">Harga</td>
					      		<td class="kanan bawah text_tengah text_blok">PPN</td>
					    	</tr>
		            	</thead>
		                <tbody id="tabel_rincian_sub_keg"></tbody>
		            </table>
				</td>
			</tr>
			<?php
			$tgl_laporan = date('d ') . $this->get_bulan(date('m')) . date(' Y');
			if (
				$type == 'dpa_murni'
				|| $type == 'dpa_perubahan'
			) :
				$_POST['api_key'] = $api_key;
				$_POST['action'] = 'get_kas';
				$_POST['kode_giat'] = $subkeg['kode_giat'];
				$_POST['kode_sub_giat'] = $subkeg['kode_sub_giat'];
				$_POST['kode_skpd'] = $unit[0]['kode_skpd'];
				$_POST['tahun_anggaran'] = $input['tahun_anggaran'];
				$kas = $this->get_kas(true);
				$kas = $kas['data'];
				$user_ppkd_db = $wpdb->get_results($wpdb->prepare("
					select 
						fullName, 
						nip 
					from data_user_penatausahaan 
					where namaJabatan='BENDAHARA UMUM DAERAH'
						AND tahun=%d
				", $input['tahun_anggaran']), ARRAY_A);
				$user_ppkd = 'XXXXXX';
				$user_ppkd_nip = 'XXXXXX';
				if (!empty($user_ppkd_db)) {
					$user_ppkd = $user_ppkd_db[0]['fullName'];
					$user_ppkd_nip = $user_ppkd_db[0]['nip'];
				}
			?>
				<tr class="no_padding no_break">
					<td colspan="2" class="no_break">
						<table width="100%" style="border-collapse: collapse;" class="cellpadding_5 no_break">
							<tbody>
								<tr>
									<td class="kiri kanan atas bawah text_blok text_tengah" colspan="2">Rencana Penarikan Dana per Bulan</td>
									<td width="60%" class="kiri kanan atas bawah" rowspan="14" style="vertical-align: middle;">
										<table class="tabel-standar no_break" width="100%" cellpadding="2">
											<tbody>
												<tr>
													<td class="text_tengah"><?php echo get_option('_crb_daerah'); ?> , Tanggal <?php echo $tgl_laporan; ?></td>
												</tr>
												<tr>
													<td class="text_tengah" style="font-size: 110%;">Kepala SKPD</td>
												</tr>
												<tr>
													<td height="80">&nbsp;</td>
												</tr>
												<tr>
													<td class="text_tengah text-u"><?php echo $unit[0]['namakepala']; ?></td>
												</tr>
												<tr>
													<td class="text_tengah">NIP: <?php echo $unit[0]['nipkepala']; ?></td>
												</tr>
												<tr>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td class="text_tengah">Mengesahkan,</td>
												</tr>
												<tr>
													<td class="text_tengah">PPKD</td>
												</tr>
												<tr>
													<td height="80">&nbsp;</td>
												</tr>
												<tr>
													<td class="text_tengah text-u"><?php echo $user_ppkd; ?></td>
												</tr>
												<tr>
													<td class="text_tengah">NIP: <?php echo $user_ppkd_nip; ?></td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">Januari</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][0], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">Februari</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][1], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">Maret</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][2], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">April</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][3], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">Mei</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][4], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">Juni</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][5], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">Juli</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][6], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">Agustus</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][7], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">September</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][8], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">Oktober</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][9], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">November</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][10], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td width="20%" class="kiri kanan atas bawah">Desember</td>
									<td width="20%" class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['per_bulan'][11], 0, ",", "."); ?></td>
								</tr>
								<tr>
									<td class="kiri kanan atas bawah text_tengah">Jumlah</td>
									<td class="kiri kanan atas bawah text_kanan">Rp <?php echo number_format($kas['total'], 0, ",", "."); ?></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			<?php else : ?>
				<tr class="no_break">
					<td class="kiri kanan atas bawah no_break" width="350" valign="top">
						&nbsp;
					</td>
					<td class="kiri kanan atas bawah no_break" width="250" valign="top">
						<table width="100%" class="cellpadding_2 no_break" style="border-spacing: 0px;">
							<tr>
								<td colspan="3" class="text_tengah"><?php echo get_option('_crb_daerah'); ?> , Tanggal <?php echo $tgl_laporan; ?></td>
							</tr>
							<tr>
								<td colspan="3" class="text_tengah text_15">Kepala SKPD</td>
							</tr>
							<tr>
								<td colspan="3" height="80">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="3" class="text_tengah"><?php echo $unit[0]['namakepala']; ?></td>
							</tr>
							<tr>
								<td colspan="3" class="text_tengah">NIP: <?php echo $unit[0]['nipkepala']; ?></td>
							</tr>
						</table>
					</td>
				</tr>
			<?php endif; ?>
			<?php
			if (
				$type == 'rka_murni'
				|| $type == 'dpa_murni'
			) :
			?>
				<tr class="no_padding no_break">
					<td colspan="2" class="no_break">
						<table width="100%" class="cellpadding_5 no_break" style="border-spacing: 0px;">
							<tbody>
								<tr>
									<td width="160" class="kiri atas bawah kanan" colspan="3">Pembahasan</td>
								</tr>
								<tr>
									<td width="160" class="kiri bawah">Tanggal</td>
									<td width="10" class="bawah">:</td>
									<td class="bawah kanan">&nbsp;</td>
								</tr>
								<tr>
									<td width="160" class="kiri bawah">Catatan</td>
									<td width="10" class="bawah">:</td>
									<td class="bawah kanan">&nbsp;</td>
								</tr>
								<tr>
									<td class="kiri bawah kanan" colspan="3">1.&nbsp;</td>
								</tr>
								<tr>
									<td class="kiri bawah kanan" colspan="3">2.&nbsp;</td>
								</tr>
								<tr>
									<td class="kiri bawah kanan" colspan="3">3.&nbsp;</td>
								</tr>
								<tr>
									<td class="kiri bawah kanan" colspan="3">4.&nbsp;</td>
								</tr>
								<tr>
									<td class="kiri bawah kanan" colspan="3">5.&nbsp;</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			<?php endif; ?>
			<tr class="no_padding no_break">
				<td colspan="2" class="no_break">
					<table width="100%" class="cellpadding_5 no_break" style="border-spacing: 0px;">
						<tbody>
							<tr>
								<td colspan="5" class="kiri kanan atas bawah text_tengah">Tim Anggaran Pemerintah Daerah</td>
							</tr>
							<tr class="text_tengah">
								<td width="10" class="kiri kanan bawah">No.</td>
								<td class="kanan bawah">Nama</td>
								<td width="200" class="bawah kanan">NIP</td>
								<td width="200" class="bawah kanan">Jabatan</td>
								<td width="200" class="bawah kanan">Tanda Tangan</td>
							</tr>
							<?php
							$tapd = $wpdb->get_results(
								"
			                		select 
			                			* 
			                		from data_user_tapd_sekda 
			                		where tahun_anggaran=" . $input['tahun_anggaran'] . '
			                			and active=1
			                			and type=\'tapd\'
			                		order by no_urut',
								ARRAY_A
							);
							for ($i = 0; $i < 8; $i++) {
								$no = $i + 1;
								$nama = '&nbsp;';
								$nip = '&nbsp;';
								$jabatan = '&nbsp;';
								if (!empty($tapd[$i])) {
									$nama = $tapd[$i]['nama'];
									$nip = $tapd[$i]['nip'];
									$jabatan = $tapd[$i]['jabatan'];
								} else if (
									$type == 'dpa_perubahan'
									|| $type == 'dpa_murni'
								) {
									continue;
								}
								echo '
						                <tr>
						                    <td width="10" class="kiri kanan bawah">' . $no . '.</td>
						                    <td class="bawah kanan">' . $nama . '</td>
						                    <td class="bawah kanan">' . $nip . '</td>
						                    <td class="bawah kanan">' . $jabatan . '</td>
			                    			<td class="bawah kanan">&nbsp;</td>
						                </tr>
			                		';
							}
							?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	var _url_asli = window.location.href;
	var url = new URL(_url_asli);
	var type = url.searchParams.get("type");
<?php if(empty($_GET['type'])): ?>
	var tipe = prompt("Masukan kode format laporan! 1=RKA Murni, 2=DPA Murni, 3=RKA Perubahan, 4=DPA Perubahan", '1');
	var val = 'rka_murni';
	if(tipe == 2){
		val = 'dpa_murni';
	}else if(tipe == 3){
		val = 'rka_perubahan';
	}else if(tipe == 4){
		val = 'dpa_perubahan';
	}
	window.location = changeUrl({
		key: 'type',
		value: val,
		url: _url_asli
	});
<?php endif; ?>
	var body = '' +
		'<h3>SETTING</h3>' +
		'<label><input type="checkbox" onclick="tampil_rinci(this);"> Tampilkan Rinci Profile Penerima Bantuan</label>' +
		'<label style="margin-left: 20px;"><input type="checkbox" class="label_nilai_kelompok" onclick="tampil_nilai(this, \'.nilai_kelompok\');"> Tampilkan Nilai Kelompok</label>' +
		'<label style="margin-left: 20px;"><input type="checkbox" class="label_nilai_keterangan" onclick="tampil_nilai(this, \'.nilai_keterangan\');"> Tampilkan Nilai Keterangan</label>' +
		'<label style="margin-left: 20px;"> Pilih format Laporan ' +
			'<select class="" style="min-width: 300px;" id="type_laporan">' +
				'<option>-- format --</option>' +
				'<option value="rka_murni">RKA Murni</option>' +
				'<option value="rka_perubahan">RKA Perubahan</option>' +
				'<option value="dpa_murni">DPA Murni</option>' +
				'<option value="dpa_perubahan">DPA Perubahan</option>' +
			'</select>' +
		'</label>';
	var aksi = '' +
		'<div id="action-sipd" class="hide-print">' +
		body +
		'</div>';
	jQuery('body').prepend(aksi);
	jQuery('#type_laporan').val('rka_murni');
	if (type) {
		jQuery('#type_laporan').val(type);
	}
	jQuery('#type_laporan').on('change', function() {
		window.open(changeUrl({
			key: 'type',
			value: jQuery(this).val(),
			url: _url_asli
		}), '_blank');
	});
	get_rinc_rka('<?php echo $input['kode_sbl']; ?>');
});

function tampil_nilai(that, _class) {
	if (jQuery(that).is(':checked')) {
		jQuery(_class).css('color', 'inherit');
	} else {
		jQuery(_class).css('color', '#fff');
	}
}

function tampil_rinci(that) {
	if (jQuery(that).is(':checked')) {
		jQuery('.profile-penerima').show();
	} else {
		jQuery('.profile-penerima').hide();
	}
}

function get_rinc_rka(kode_sbl){
	jQuery("#wrap-loading").show();
	jQuery.ajax({
		url:ajax.url,
		type:"post",
		data:{
			"action":"get_rinc_rka_lokal",
			"api_key":"<?php echo $api_key; ?>",
			"tahun_anggaran":"<?php echo $input['tahun_anggaran']; ?>",
			"kode_sbl":kode_sbl,
			"sumber":'sipd',
		},
		dataType:"json",
		success:function(response){
			jQuery('#tabel_rincian_sub_keg').html(response.rin_sub_item);
			tampil_nilai(jQuery(''), );
			jQuery('.nilai_kelompok').css('color', 'inherit');
			jQuery('.nilai_keterangan').css('color', 'inherit');
			jQuery('.label_nilai_kelompok').prop('checked', true);
			jQuery('.label_nilai_keterangan').prop('checked', true);
			jQuery("#wrap-loading").hide();
		}
	});
}
</script>