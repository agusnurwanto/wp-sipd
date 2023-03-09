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

// if(empty($input['id_skpd'])){
// 	die('<h1>Kode SKPD Kosong</h1>');
// }

if(empty($input['kode_sbl'])){
	die('<h1>Kode SBL Kosong</h1>');
}

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
$subkeg = $wpdb->get_row($wpdb->prepare($sql, $input['tahun_anggaran'], $input['kode_sbl']));

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
	                <tr class="tr-urusan-pemerintahan">
	                    <td width="150">Urusan Pemerintahan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg->nama_urusan; ?></td>
	                </tr>
	                <tr class="tr-bidang-urusan">
	                    <td width="150">Bidang Urusan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg->nama_bidang_urusan; ?></td>
	                </tr>
	                <tr class="tr-program">
	                    <td width="150">Program</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg->nama_program; ?></td>
	                </tr>
	                <tr class="tr-sasaran-program">
	                    <td width="150">Sasaran Program</td>
	                    <td width="10">:</td>
	                    <td></td>
	                </tr>
	                <tr class="tr-capaian-program" valign="top">
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
	                <tr class="tr-kegiatan">
	                    <td width="150">Kegiatan</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg->nama_giat ?></td>
	                </tr>
	                <tr class="tr-organisasi">
	                    <td width="150">Organisasi</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg->kode_sub_skpd . " " . $subkeg->nama_sub_skpd;  ?></td>
	                </tr>
	                <tr class="tr-unit">
	                    <td width="150">Unit</td>
	                    <td width="10">:</td>
	                    <td><?php echo $subkeg->kode_skpd . " " . $subkeg->nama_skpd;  ?></td>
	                </tr>
	                <tr class="tr-alokasi-min-1">
	                    <td width="150">Alokasi Tahun <?php echo $input['tahun_anggaran']-1; ?></td>
	                    <td width="10">:</td>
	                    <td></td>
	                </tr>
	                <tr class="tr-alokasi">
	                    <td width="150">Alokasi Tahun <?php echo $input['tahun_anggaran']; ?></td>
	                    <td width="10">:</td>
	                    <td class="total_giat">Rp. <?php echo $this->_number_format($subkeg->pagu_usulan);  ?></td>
	                </tr>
	                <tr class="tr-alokasi-plus-1">
	                    <td width="150">Alokasi Tahun <?php echo $input['tahun_anggaran']+1; ?></td>
	                    <td width="10">:</td>
	                    <td>Rp. <?php echo $this->_number_format($subkeg->pagu_n_depan_usulan);  ?></td>
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
	
	run_download_excel();
	
	var aksi = ''
		+'<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RKA</a>';

	jQuery("#action-sipd").append(aksi);

	jQuery("#tambah-data").on('click', function(){
		
		let rincian = '<table class="table" style="font-size:14px">'
			    +'<thead>'
			        +'<tr>'
			          	+'<th class="text-center" style="width: 160px;">Bidang</th>'
			          	+'<th></th>'
			        +'</tr>'
					+'<tr>'
				        +'<th class="text-center" style="width: 160px;">Sub Unit</th>'
				        +'<th></th>'
				    +'</tr>'
			        +'<tr>'
			          	+'<th class="text-center" style="width: 160px;">Program</th>'
			          	+'<th></th>'
			        +'</tr>'
			        +'<tr>'
			          	+'<th class="text-center" style="width: 160px;">Kegiatan</th>'
			        	+'<th></th>'
			        +'</tr>'
			        +'<tr>'
			          	+'<th class="text-center" style="width: 160px;">Sub Kegiatan</th>'
			          	+'<th></th>'
			        +'</tr>'
			        +'<tr>'
			          	+'<th class="text-center" style="width: 160px;">Total Rincian Belanja</th>'
			          	+'<th></th>'
			        +'</tr>'
			    +'</thead>'
			+'</table>'
			+'<div style="margin-top:10px"><button type="button" class="btn btn-primary mb-2 btn-tambah-rincian"><i class="dashicons dashicons-plus" style="margin-top: 2px;"></i> Tambah Rincian</button>'
			+'</div>'
			+'<table style="font-size:14px">'
				+'<thead>'
					+'<tr>'
						+'<th rowspan="2" class="text-center">URAIAN</th>'
						+'<th rowspan="2" class="text-center">SATUAN</th>'
						+'<th colspan="4" class="text-center">SEBELUM</th>'
						+'<th colspan="4" class="text-center">SESUDAH</th>'
						+'<th rowspan="2" class="text-center">AKSI</th>'
					+'</tr>'
					+'<tr>'
						+'<th class="text-center">KOEFISIEN</th>'
						+'<th class="text-center">HARGA</th>'
						+'<th class="text-center">PAJAK</th>'
						+'<th class="text-center">TOTAL</th>'
						+'<th class="text-center">KOEFISIEN</th>'
						+'<th class="text-center">HARGA</th>'
						+'<th class="text-center">PAJAK</th>'
						+'<th class="text-center">TOTAL</th>'
					+'</tr>'
				+'</thead>'
			+'</table>';

		jQuery("#modal-rincian").find('.modal-body').html(rincian);
		jQuery("#modal-rincian").find('.modal-title').html('Tambah Rincian RKA');
		jQuery("#modal-rincian").css('margin-top', 20);
		jQuery("#modal-rincian").modal('show');
	});

	jQuery(document).on('click', '.btn-tambah-rincian', function(){

		let form = ''
			+'<form id="form-input-rincian">'
				+'<input type="hidden" name="bidur-all" value="">'
				+'<div class="form-group">'
					+'<label for="tujuan_teks">Pilih Objek Belanja</label>'
					+'<select class="form-control select-option" id="daftar-objek-belanja" name="daftar-objek-belanja"></select>'
				+'</div>'
			+'</form>';

		jQuery("#modal-input-rincian").find('.modal-title').html('Input Rincian RKA');
		jQuery("#modal-input-rincian").find('.modal-body').html(form);
		jQuery("#modal-input-rincian").css('margin-top', 20);
		jQuery("#modal-input-rincian").css('margin-top', 20);
		jQuery(".select-option").select2({width:'100%'});
		jQuery("#modal-input-rincian").modal('show');

		objekBelanja(); 
	});

	function objekBelanja(){
		return new Promise(function(resolve, reject){
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
					
					let opt=`<option value="-">Pilih Objek Belanja</option>`;
					response.items.map(function(item, index){
						opt+=`<option value="${item.kode_akun}">${item.nama_akun}</option>`;
					});
					jQuery("#daftar-objek-belanja").html(opt);
				}
			})
		});
	}

</script>