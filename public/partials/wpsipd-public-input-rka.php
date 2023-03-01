<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

global $wpdb;

$input = shortcode_atts( array(
	'kode_bl' => '',
	'tahun_anggaran' => get_option('_crb_tahun_anggaran_sipd')
), $atts );

$current_user = wp_get_current_user();
$api_key = get_option( '_crb_api_key_extension' );

?>
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
	            <table width="100%" class="cellpadding_5" style="border-spacing: 1px;">
	                <tr class="">
	                    <td width="150">Urusan Pemerintahan</td>
	                    <td width="10">:</td>
	                    <td></td>
	                </tr>
	                <tr class="">
	                    <td width="150">Bidang Urusan</td>
	                    <td width="10">:</td>
	                    <td></td>
	                </tr>
	                <tr class="">
	                    <td width="150">Program</td>
	                    <td width="10">:</td>
	                    <td></td>
	                </tr>
	                <tr class="">
	                    <td width="150">Sasaran Program</td>
	                    <td width="10">:</td>
	                    <td></td>
	                </tr>
	                <tr class="" valign="top">
	                    <td width="150">Capaian Program</td>
	                    <td width="10">:</td>
	                    <td>
	                        <table width="50%" border="0" style="border-spacing: 0px;" class="tabel-indikator">
	                            <tr class="text_tengah">
	                            	<th class="kiri atas kanan bawah">Indikator</th>
	                            	<th class="kiri atas kanan bawah">Target</th>
	                            </tr>
	                        </table>
	                </td>
	                </tr>
	                <tr class="">
	                    <td width="150">Kegiatan</td>
	                    <td width="10">:</td>
	                    <td></td>
	                </tr>
	                <tr class="">
	                    <td width="150">Organisasi</td>
	                    <td width="10">:</td>
	                    <td></td>
	                </tr>
	                <tr class="">
	                    <td width="150">Unit</td>
	                    <td width="10">:</td>
	                    <td></td>
	                </tr>
	                <tr class="">
	                    <td width="150">Alokasi Tahun <?php echo $input['tahun_anggaran']-1; ?></td>
	                    <td width="10">:</td>
	                    <td></td>
	                </tr>
	                <tr class="">
	                    <td width="150">Alokasi Tahun <?php echo $input['tahun_anggaran']; ?></td>
	                    <td width="10">:</td>
	                    <td class="total_giat">Rp. </td>
	                </tr>
	                <tr class="">
	                    <td width="150">Alokasi Tahun <?php echo $input['tahun_anggaran']+1; ?></td>
	                    <td width="10">:</td>
	                    <td>Rp. </td>
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
		            <tbody>
		            	<tr>
			                <td width="130" class="text_tengah kiri atas kanan bawah">Indikator</td>
			                <td class="text_tengah kiri atas kanan bawah">Tolok Ukur Kinerja</td>
			                <td width="150" class="text_tengah kiri atas kanan bawah">Target Kinerja</td>
			            </tr>
			            <tr>
	                    	<td width="130" class="kiri kanan atas bawah">Capaian Kegiatan</td>
		                	<td class="kiri kanan atas bawah">
		                    	<table width="100%" border="0" style="border-spacing: 0px;">
			                        <tbody>
										<tr>
								            <td width="495"></td>
								        </tr>
				                    </tbody>
				                </table>
		                	</td>
		                	<td class="kiri kanan atas bawah">
		                    	<table width="100%" border="0" style="border-spacing: 0px;">
									<tbody>
										<tr>
							            	<td width="495"></td>
							        	</tr>
			                    	</tbody>
			                	</table>
		                </td>
	                </tr>
	                <tr>
	                    <td width="130" class="kiri kanan atas bawah">Masukan</td>
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
		                          		<td width="495">Rp.</td>
		                        	</tr>
	                        	</tbody>
	                       	</table>
	                    </td>
	                </tr>
	                <tr>
	                    <td width="130" class="kiri kanan atas bawah">Keluaran</td>
		                <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                            <tbody>
	                            	<tr>
						            	<td width="495"></td>
						        	</tr>
		                    	</tbody>
		                    </table>
	                    </td>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                            <tbody>
	                            	<tr>
						            	<td width="495"></td>
						        	</tr>
		                        </tbody>
		                    </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td width="130" class="kiri kanan atas bawah">Hasil</td>
		                <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                        	<tbody>
		                        	<tr>
							            <td width="495"></td>
							        </tr>
		                        </tbody>
		                   	</table>
	                    </td>
	                    <td class="kiri kanan atas bawah">
	                        <table width="100%" border="0" style="border-spacing: 0px;">
	                        	<tbody>
		                        	<tr>
							            <td width="495"></td>
							        </tr>
		                        </tbody>
		                    </table>
	                    </td>
	                </tr>
	            </tbody></table>
	        </td>
	    </tr>
	    <tr>
	        <td class="" width="150" colspan="2">Kelompok Sasaran Kegiatan : </td>
	    </tr>
	    <tr>
	        <td class="" width="150" colspan="2">&nbsp;</td>
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
				            <td colspan="13">
				                <table class="cellpadding_5">
				                    <tbody>
					                    <tr class="">
					                        <td width="130">Sub Kegiatan</td>
					                        <td width="10">:</td>
					                        <td class="subkeg" data-kdsbl=""><span class="nama_sub"></span></td>
					                    </tr>
					                    <tr class="">
					                        <td width="130">Sumber Pendanaan</td>
					                        <td width="10">:</td>
					                        <td class="subkeg-sumberdana" data-kdsbl="" data-idsumberdana=""><span class="kode-dana"></span></td>
					                    </tr>
					                    <tr class="">
					                        <td width="130">Lokasi</td>
					                        <td width="10">:</td>
					                        <td></td>
					                    </tr>
					                    <tr class="">
					                        <td width="130">Waktu Pelaksanaan</td>
					                        <td width="10">:</td>
					                        <td>Januari s.d. Desember</td>
					                    </tr>
					                    <tr valign="top" class="">
					                        <td width="150">Keluaran Sub Kegiatan</td>
					                        <td width="10">:</td>
					                        <td>
					                        	<table class="tabel-indikator" width="100%" border="0" style="border-spacing: 0px;">
									                <tbody>
									                	<tr>
									                		<th class="kiri kanan bawah atas text_tengah" width="495">Indikator</th>
									                		<th class="kiri kanan bawah atas text_tengah" width="495">Target</th>
									               		</tr>
									                	<tr>
									                		<td class="kiri kanan bawah atas"></td>
									                		<td class="kiri kanan bawah atas"></td>
									            		</tr>
									            	</tbody>
									            </table>
					                         </td>
					                    </tr>
				                	</tbody>
				            	</table>
				            </td>
				    	</tr>
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
		<tr>
            <td colspan="6" class="kiri kanan bawah text_kanan text_blok">Jumlah Anggaran Sub Kegiatan :</td>
            <td class="kanan bawah text_blok text_kanan subkeg-total" style="white-space:nowrap" data-kdsbl=""></td>
        </tr>
	</table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.js"></script>
<script type="text/javascript">
	
	run_download_excel();
	
	var aksi = ''
		+'<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RKA</a>';

	jQuery("#action-sipd").append(aksi);

</script>