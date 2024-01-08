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

$type = '';

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
</style>
<div class="cetak" contenteditable="false">
	<table width="100%" class="cellpadding_5" style="border-spacing: 2px;">
	    <tr class="no_padding">
	        <td colspan="2">
	            <table width="100%" class="cellpadding_5" style="border-spacing: 1px;" class="text_tengah text_15">
	                <tr>
	                    <td class="kiri atas kanan bawah text_blok text_tengah">RENCANA KERJA DAN ANGGARAN<br/>SATUAN KERJA PERANGKAT DAERAH</td>
						<td class="kiri atas kanan bawah text_blok text_tengah" style="vertical-align: middle;" rowspan="2">Formulir<br/>RKA - RINCIAN BELANJA SKPD</td>
	                </tr>
	                <tr>
	                    <td class="kiri atas kanan bawah text_tengah">Pemerintah <?php echo get_option('_crb_daerah'); ?> Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	    <tr class="no_padding">
	        <td colspan="2">
	            <table id="tabel-rincian" width="100%" class="cellpadding_5" style="border-spacing: 1px;">
	                <tr class="tr-urusan-pemerintahan">
	                    <td width="150">Urusan Pemerintahan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg['nama_urusan']; ?></td>
	                </tr>
	                <tr class="tr-bidang-urusan">
	                    <td width="150">Bidang Urusan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg['nama_bidang_urusan']; ?></td>
	                </tr>
	                <tr class="tr-program">
	                    <td width="150">Program</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg['nama_program']; ?></td>
	                </tr>
	                <tr class="tr-kegiatan">
	                    <td width="150">Kegiatan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg['nama_giat'] ?></td>
	                </tr>
	                <tr class="tr-organisasi">
	                    <td width="150">Organisasi</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg['kode_sub_skpd'] . " " . $subkeg['nama_sub_skpd'];  ?></td>
	                </tr>
	                <tr class="tr-unit">
	                    <td width="150">Unit</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg['kode_skpd'] . " " . $subkeg['nama_skpd'];  ?></td>
	                </tr>
	                <tr class="tr-alokasi-min-1">
	                    <td width="150">Alokasi Tahun <?php echo $input['tahun_anggaran']-1; ?></td>
	                    <td width="10">:</td>
	                    <td></td>
	                </tr>
	                <tr class="tr-alokasi">
	                    <td width="150">Alokasi Tahun <?php echo $input['tahun_anggaran']; ?></td>
	                    <td width="10">:</td>
	                    <td class="total_giat">Rp. <?php echo $this->_number_format($subkeg['pagu']);  ?></td>
	                </tr>
	                <tr class="tr-alokasi-plus-1">
	                    <td width="150">Alokasi Tahun <?php echo $input['tahun_anggaran']+1; ?></td>
	                    <td width="10">:</td>
	                    <td>Rp. <?php echo $this->_number_format($subkeg['pagu_n_depan']);  ?></td>
	                </tr>
	            </table>
	        </td>            
	    </tr>
	    <tr>
			<td class="atas kanan bawah kiri text_tengah text_15" colspan="2">
				<table width="100%" class="cellpadding_5" style="border-spacing: 0px;">
				    <tbody>
				    	<tr>
				            <td>Rincian Anggaran Belanja Kegiatan Satuan Kerja Perangkat Daerah</td>
				        </tr>
				    </tbody>
				</table>
			</td>
		</tr>
	  	<tr class="no_padding">
	    	<td colspan="2" style="">
	      		<table width="100%" class="cellpadding_5" style="border-spacing: 0px;">
	        		<tbody>
						<tr class="no_padding">
					        <td colspan="2">
					          	<table id="table-sub-kegiatan" class="cellpadding_5">
						            <tbody>
							            <tr class="tr-sub-kegiatan">
							              	<td width="130">Sub Kegiatan</td>
					                  		<td width="10">:</td>
									      	<td class="subkeg" data-kdsbl=""><span class="nama_sub"><?php echo $subkeg['nama_sub_giat']; ?></span></td>
									    </tr>
									    <tr class="tr-sumber-pendanaan">
									      	<td width="130">Sumber Pendanaan</td>
									      	<td width="10">:</td>
								      	<?php echo '
								      		<td class="subkeg-sumberdana" data-kdsbl="'.$input['kode_sbl'].'" data-idsumberdana="'.implode(',', $sd_sub_id).'">'.implode(', ', $sd_sub).'</td>'; 
								      	?>
									    </tr>
									    <tr class="tr-lokasi">
									      	<td width="130">Lokasi</td>
									      	<td width="10">:</td>
									      	<td><?php echo implode(', ', $lokasi_sub); ?></td>
									    </tr>
									    <tr class="tr-waktu-pelaksanaan">
									      	<td width="130">Waktu Pelaksanaan</td>
										    <td width="10">:</td>
										    <td><?php echo $bulan_awal.' s.d. '.$bulan_akhir; ?></td>
									    </tr>
									    <tr valign="top" class="">
									        <td width="150">Keluaran Sub Kegiatan</td>
									        <td width="10">:</td>
									        <td><?php echo $table_indikator_sub_keg; ?></td>
								      	</tr>
							      	</tbody>
							    </table>
							</td>
						</tr>
				    	<tr class="no_padding">
					        <td colspan="2">
					            <table width="100%" class="cellpadding_5" style="border-spacing: 0px;">
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
					</tbody>
				</table>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	get_rinc_rka('<?php echo $input['kode_sbl']; ?>');
});

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
			jQuery("#wrap-loading").hide();
			// resolve();
		}
	});
}
</script>