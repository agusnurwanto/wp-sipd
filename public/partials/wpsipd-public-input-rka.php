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

$sql = "
SELECT 
    *
FROM data_sub_keg_bl_lokal
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
	from data_dana_sub_keg_lokal d
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
	}
}

$sql = "
	SELECT 
		* 
	from data_sub_keg_indikator_lokal 
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
	from data_lokasi_sub_keg_lokal 
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

<div class="modal fade" id="modal-rincian" data-backdrop="static" aria-model="true" role="dialog" aria-labelledby="modal-rincian-label" aria-hidden="true">
  	<div class="modal-dialog modal-xl" style="max-width: 1200px;" role="document">
    	<div class="modal-content">
      		<div class="modal-header">
        		<h5 class="modal-title">Modal title</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          	<span aria-hidden="true">&times;</span>
		        </button>
	      	</div>
	      	<div class="modal-body">
	      	</div>
	      	<div class="modal-footer"></div>
    	</div>
  	</div>
</div>

<div class="modal fade" id="modal-input-rincian" data-backdrop="static" role="dialog" aria-labelledby="modal-input-rincian-label" aria-hidden="true">
  	<div class="modal-dialog" role="document">
    	<div class="modal-content">
      		<div class="modal-header">
		        <h5 class="modal-title">Modal title</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          	<span aria-hidden="true">&times;</span>
		        </button>
      		</div>
	      	<div class="modal-body">
	      	</div>
	      	<div class="modal-footer"></div>
    	</div>
  	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	run_download_excel();
	var aksi = ''
		+'<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RKA</a>'
		+'</br></br><a style="margin-left: 10px;" id="copy-data" onclick="return false;" href="#" class="btn btn-danger">Copy Data RKA SIPD ke Lokal</a>';
	jQuery("#action-sipd").append(aksi);
	jQuery("#tambah-data").on('click', function(){
		let rincian = ''
			+'<table class="table table-bordered">'
			    +'<tbody>'
			        +'<tr>'
			          	+'<th style="width: 160px;">Bidang</th>'
			          	+'<th class="tex-center" style="width: 20px;">:</th>'
			          	+'<th>'+jQuery("#tabel-rincian tr[class=tr-urusan-pemerintahan]").find('>td').eq(2).html()+'</th>'
			        +'</tr>'
					+'<tr>'
				        +'<th style="width: 160px;">Sub Unit</th>'
				        +'<th class="tex-center" style="width: 20px;">:</th>'
				        +'<th>'+jQuery("#tabel-rincian tr[class=tr-unit]").find('>td').eq(2).html()+'</th>'
				    +'</tr>'
			        +'<tr>'
			          	+'<th style="width: 160px;">Program</th>'
			          	+'<th class="tex-center" style="width: 20px;">:</th>'
			          	+'<th>'+jQuery("#tabel-rincian tr[class=tr-program]").find('>td').eq(2).html()+'</th>'
			        +'</tr>'
			        +'<tr>'
			          	+'<th style="width: 160px;">Kegiatan</th>'
			          	+'<th class="tex-center" style="width: 20px;">:</th>'
			        	+'<th>'+jQuery("#tabel-rincian tr[class=tr-kegiatan]").find('>td').eq(2).html()+'</th>'
			        +'</tr>'
			        +'<tr>'
			          	+'<th style="width: 160px;">Sub Kegiatan</th>'
			          	+'<th class="tex-center" style="width: 20px;">:</th>'
			          	+'<th>'+jQuery("#table-sub-kegiatan tr[class=tr-sub-kegiatan]").find('>td').eq(2).html()+'</th>'
			        +'</tr>'
			        +'<tr>'
			        	+'<td colspan="3">'
							+'<form id="form-input-rincian">'
								+'<input type="hidden" name="bidur-all" value="">'
								+'<div class="form-group">'
									+'<label for="daftar-objek-belanja">Pilih Objek Belanja</label>'
									+'<select class="form-control select-option" id="daftar-objek-belanja" name="daftar_objek_belanja"></select>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="daftar-rekening-akun">Rekening / Akun</label>'
									+'<select class="form-control select-option" id="daftar-rekening-akun" name="daftar_rekening_akun"></select>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="pengelompokan-belanja-paket-pekerjaan">Pengelompokan Belanja / Paket Pekerjaan</label>'
									+'<select class="form-control select-option" id="pengelompokan-belanja-paket-pekerjaan" name="pengelompokan_belanja_paket_pekerjaan"></select>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="sumber_dana">Sumber Dana</label>'
									+'<select class="form-control select-option" id="sumber_dana" name="sumber_dana">'
										+'<option value="">Pilih Sumber Dana...</option>'
									+'</select>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="jenis-standar-harga">Jenis Standar Harga</label>'
									+'<select class="form-control select-option" id="jenis-standar-harga" name="jenis_standar_harga">'
										+'<option value="">Pilih Standar Harga...</option>'
										+'<option value="SSH">SSH</option>'
										+'<option value="SBU">SBU</option>'
										+'<option value="HSPK">HSPK</option>'
										+'<option value="ASB">ASB</option>'
									+'</select>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="komponen">Komponen</label>'
									+'<div class="row">'
										+'<div class="col-lg-10">'
											+'<input type="text" class="form-control" id="komponen" name="komponen">'
										+'</div>'
										+'<div class="col-lg-2">'
											+'<button class="btn btn-primary cari-ssh">Cari</button>'
										+'</div>'
									+'</div>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="tkdn">TKDN</label>'
									+'<input type="text" class="form-control" id="tkdn" name="tkdn">'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="spesifikasi-komponen">Spesifikasi Komponen</label>'
									+'<input type="text" class="form-control" id="spesifikasi-komponen" name="spesifikasi_komponen">'
								+'</div>'
								+'<div class="form-group">'
									+'<div class="row">'
										+'<div class="col-lg-6">'
											+'<label for="satuan">Satuan</label>'
											+'<input type="text" class="form-control" id="satuan" name="satuan">'
										+'</div>'
										+'<div class="col-lg-6">'
											+'<label for="satuan">Harga Satuan</label>'
											+'<input type="text" class="form-control" id="harga-satuan" name="harga_satuan">'
										+'</div>'
									+'</div>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="tambahkan-pajak"><input type="checkbox" value=""> Tambahkan Pajak</label>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="koefisien">Koefisien (Perkalian)</label>'
									+'<div class="row">'
										+'<div class="col-lg-6">'
											+'<input type="number" class="form-control" id="volume-1" name="volume_1" placeholder="Volume 1">'
										+'</div>'
										+'<div class="col-lg-6">'
											+'<select class="form-control select-option" id="satuan-volume-1" name="satuan_volume_1" placeholder="Satuan 1"></select>'
										+'</div>'
									+'</div>'
									+'<div class="row" style="margin-top: 5px;">'
										+'<div class="col-lg-6">'
											+'<input type="number" class="form-control" id="volume-2" name="volume_2" placeholder="Volume 2">'
										+'</div>'
										+'<div class="col-lg-6">'
											+'<select class="form-control select-option" id="satuan-volume-2" name="satuan_volume_2" placeholder="Satuan 2"></select>'
										+'</div>'
									+'</div>'
									+'<div class="row" style="margin-top: 5px;">'
										+'<div class="col-lg-6">'
											+'<input type="number" class="form-control" id="volume-3" name="volume_3" placeholder="Volume 3">'
										+'</div>'
										+'<div class="col-lg-6">'
											+'<select class="form-control select-option" id="satuan-volume-3" name="satuan_volume_3" placeholder="Satuan 3"></select>'
										+'</div>'
									+'</div>'
									+'<div class="row" style="margin-top: 5px;">'
										+'<div class="col-lg-6">'
											+'<input type="number" class="form-control" id="volume-4" name="volume_4" placeholder="Volume 4">'
										+'</div>'
										+'<div class="col-lg-6">'
											+'<select class="form-control select-option" id="satuan-volume-4" name="satuan_volume_4" placeholder="Satuan 4"></select>'
										+'</div>'
									+'</div>'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="volume">Volume</label>'
									+'<input type="text" class="form-control" id="volume" name="volume">'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="ket_jumlah">Koefisien (Keterangan Jumlah)</label>'
									+'<input type="text" class="form-control" id="ket_jumlah" name="ket_jumlah">'
								+'</div>'
								+'<div class="form-group">'
									+'<label for="total">Total Belanja</label>'
									+'<input type="text" class="form-control" id="total" name="total">'
								+'</div>'
							+'</form>'
						+'</td>'
					+'</tr>'
			    +'</tbody>'
			+'</table>'

		jQuery("#modal-rincian").find('.modal-title').html('Input Rincian RKA');
		jQuery("#modal-rincian").find('.modal-body').html(rincian);
		jQuery("#modal-rincian").find('.modal-footer').html(''
			+'<button type="button" class="btn btn-warning" data-dismiss="modal">Tutup'
			+'</button>'
			+'<button type="button" class="btn btn-success" id="btn-simpan-data-rka" '
				+'data-action="submit_rka" '
			+'>Simpan'
			+'</button>');
		jQuery("#modal-rincian").css('margin-top', 20);
		jQuery("#modal-rincian").modal('show');
		jQuery(".select-option").select2({width:'100%'});

		objekBelanja().then(function(){
			jenisStandarHarga();
		});
	});

	jQuery("#copy-data").on("click", function(){
		if(confirm('Apakah anda yakin untuk melakukan ini? data RKA Lokal akan diupdate sama dengan data RKA SIPD.')){
            let id_skpd = "<?php echo $id_skpd; ?>";
			let kode_sbl = "<?php echo $input['kode_sbl']; ?>";
            if(id_skpd == '' && kode_sbl==''){
                alert('Id SKPD dan kode sbl Kosong')
            }else{
                jQuery('#wrap-loading').show();
                jQuery.ajax({
                    method: 'post',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    dataType: "json",
                    data: {
                    "action": "copy_rka_sipd",
                    "api_key": "<?php echo $api_key; ?>",
                    "id_skpd": id_skpd,
					"kode_sbl": kode_sbl,
                    "tahun_anggaran": "<?php echo $input['tahun_anggaran']; ?>"
                    },
                    success: function(res){
                        jQuery('#wrap-loading').hide();
                        alert(res.message);
                        if(res.status == 'success'){
							if(confirm('Ada data yang berubah, apakah mau merefresh halaman ini?')){
								window.location = "";
							}
                        }
                    }
                });
            }
		}
	});

	jQuery(document).on('change', "#daftar-objek-belanja", function(){
		if(typeof global_akun_belanja == 'undefined'){
			global_akun_belanja = {};
		}
		let objekBelanja = jQuery("#daftar-objek-belanja").val();
		if(!global_akun_belanja[objekBelanja]){
			jQuery("#wrap-loading").show();
			jQuery.ajax({
				url:ajax.url,
				type:"post",
				data:{
					"action":"get_rekening_akun",
					"api_key":"<?php echo $api_key; ?>",
					"kode_akun":objekBelanja,
					"tahun_anggaran":"<?php echo $input['tahun_anggaran']; ?>",
				},
				dataType:"json",
				success:function(response){
					let opt = "<option value=''>Pilih Rekening / Akun</option>";
					response.items.map(function(item, index){
						opt += `<option value="${item.kode_akun}">${item.kode_akun} ${item.nama_akun}</option>`;
					});
					global_akun_belanja[objekBelanja] = opt;
					jQuery("#daftar-rekening-akun").html(global_akun_belanja[objekBelanja]).trigger('change');
					jQuery("#wrap-loading").hide();
				}
			});
		}else{
			jQuery("#daftar-rekening-akun").html(global_akun_belanja[objekBelanja]).trigger('change');
		}
	})

	jQuery(document).on('change', "#daftar-rekening-akun", function(){
				
	});

	jQuery(document).on('click', '.cari-ssh', function(){
		jQuery.ajax({
			url:ajax.url,
			type:"post",
			data:{
				"action":"get_data_ssh",
				"api_key":"<?php echo $api_key; ?>",
				"tahun_anggaran":"<?php echo $input['tahun_anggaran']; ?>",
			},
			dataType:"json",
			success:function(response){
					console.log(response);
			}
		});
	});

	get_rinc_rka_lokal('<?php echo $input['kode_sbl']; ?>');
});

function get_rinc_rka_lokal(kode_sbl){
	jQuery("#wrap-loading").show();
	jQuery.ajax({
		url:ajax.url,
		type:"post",
		data:{
			"action":"get_rinc_rka_lokal",
			"api_key":"<?php echo $api_key; ?>",
			"tahun_anggaran":"<?php echo $input['tahun_anggaran']; ?>",
			"kode_sbl":kode_sbl,
		},
		dataType:"json",
		success:function(response){
			jQuery('#tabel_rincian_sub_keg').html(response.rin_sub_item);
			jQuery("#wrap-loading").hide();
			// resolve();
		}
	});
}

function objekBelanja(){
	return new Promise(function(resolve, reject){
		if(typeof global_jenis_objek_belanja == 'undefined'){
			jQuery("#wrap-loading").show();
			jQuery.ajax({
				url:ajax.url,
				type:"post",
				data:{
					"action":"get_objek_belanja",
					"api_key":"<?php echo $api_key; ?>",
					"tahun_anggaran":"<?php echo $input['tahun_anggaran']; ?>",
				},
				dataType:"json",
				success:function(response){
					jQuery("#wrap-loading").hide();
					let opt = "<option value="-">Pilih Objek Belanja</option>";
					for( var i in response.items){
						opt += "<option value="+i+">"+response.items[i]+"</option>";
					};
					global_jenis_objek_belanja = opt;
					jQuery("#daftar-objek-belanja").html(global_jenis_objek_belanja);
					resolve();
				}
			});
		}else{
			jQuery("#daftar-objek-belanja").html(global_jenis_objek_belanja);
			resolve();
		}
	});
}

function jenisStandarHarga(){
	return new Promise(function(resolve, reject) {
		jQuery.ajax({
			url:ajax.url,
			type:"post",
			data:{
				"action":"get_jenis_standar_harga",
				"api_key":"<?php echo $api_key; ?>",
			},
			dataType:"json",
			success:function(response){
				
				let opt=`<option value="-">Pilih Jenis Standar Harga</option>`;
				response.items.map(function(value, index){
						opt+=`<option value="${value.id}">${value.jenis_standar_harga}</option>`;
				})
				jQuery("#jenis-standar-harga").html(opt);
			}
		})
	})
}

</script>