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
	            <table id="tabel-rincian" width="100%" class="cellpadding_5" style="border-spacing: 1px;">
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
			          <table id="table-sub-kegiatan" class="cellpadding_5">
				            <tbody>
					            <tr class="tr-sub-kegiatan">
					              <td width="130">Sub Kegiatan</td>
			                  <td width="10">:</td>
									      <td class="subkeg" data-kdsbl=""><span class="nama_sub"><?php echo $subkeg->nama_sub_giat; ?></span></td>
									    </tr>
									    <tr class="tr-sumber-pendanaan">
									      <td width="130">Sumber Pendanaan</td>
									      <td width="10">:</td>
									      <td class="subkeg-sumberdana" data-kdsbl="" data-idsumberdana=""><span class="kode-dana"></span></td>
									    </tr>
									    <tr class="tr-lokasi">
									      <td width="130">Lokasi</td>
									      <td width="10">:</td>
									      <td><?php echo $subkeg->nama_lokasi; ?></td>
									    </tr>
									    <tr class="tr-waktu-pelaksanaan">
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
	
	run_download_excel();
	
	var aksi = ''
		+'<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RKA</a>';

	jQuery("#action-sipd").append(aksi);

	jQuery("#tambah-data").on('click', function(){
		
		let rincian = '<table class="table" style="font-size:14px">'
			    +'<thead>'
			        +'<tr>'
			          	+'<th class="text-center" style="width: 160px;">Bidang</th>'
			          	+'<th>'+jQuery("#tabel-rincian tr[class=tr-urusan-pemerintahan]").find('>td').eq(2).html()+'</th>'
			        +'</tr>'
					+'<tr>'
				        +'<th class="text-center" style="width: 160px;">Sub Unit</th>'
				        +'<th>'+jQuery("#tabel-rincian tr[class=tr-unit]").find('>td').eq(2).html()+'</th>'
				    +'</tr>'
			        +'<tr>'
			          	+'<th class="text-center" style="width: 160px;">Program</th>'
			          	+'<th>'+jQuery("#tabel-rincian tr[class=tr-program]").find('>td').eq(2).html()+'</th>'
			        +'</tr>'
			        +'<tr>'
			          	+'<th class="text-center" style="width: 160px;">Kegiatan</th>'
			        	+'<th>'+jQuery("#tabel-rincian tr[class=tr-kegiatan]").find('>td').eq(2).html()+'</th>'
			        +'</tr>'
			        +'<tr>'
			          	+'<th class="text-center" style="width: 160px;">Sub Kegiatan</th>'
			          	+'<th>'+jQuery("#table-sub-kegiatan tr[class=tr-sub-kegiatan]").find('>td').eq(2).html()+'</th>'
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
					+'<label for="jenis-standar-harga">Jenis Standar Harga</label>'
					+'<select class="form-control select-option" id="jenis-standar-harga" name="jenis_standar_harga"></select>'
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
					+'<label for="tambahkan-pajak">Tambahkan Pajak</label>'
				+'</div>'
				+'<div class="form-group">'
					+'<label for="koefisien">Koefisien (Perkalian)</label>'
					+'<div class="row">'
						+'<div class="col-lg-6">'
							+'<input type="number" class="form-control" id="volume-1" name="volume_1">'
						+'</div>'
						+'<div class="col-lg-6">'
							+'<select class="form-control select-option" id="satuan-volume-1" name="satuan_volume_1"></select>'
						+'</div>'
					+'</div>'
					+'<div class="row">'
						+'<div class="col-lg-6">'
							+'<input type="number" class="form-control" id="volume-2" name="volume_2">'
						+'</div>'
						+'<div class="col-lg-6">'
							+'<select class="form-control select-option" id="satuan-volume-2" name="satuan_volume_2"></select>'
						+'</div>'
					+'</div>'
					+'<div class="row">'
						+'<div class="col-lg-6">'
							+'<input type="number" class="form-control" id="volume-3" name="volume_3">'
						+'</div>'
						+'<div class="col-lg-6">'
							+'<select class="form-control select-option" id="satuan-volume-3" name="satuan_volume_3"></select>'
						+'</div>'
					+'</div>'
					+'<div class="row">'
						+'<div class="col-lg-6">'
							+'<input type="number" class="form-control" id="volume-4" name="volume_4">'
						+'</div>'
						+'<div class="col-lg-6">'
							+'<select class="form-control select-option" id="satuan-volume-4" name="satuan_volume_4"></select>'
						+'</div>'
					+'</div>'
				+'</div>'
			+'</form>';

		jQuery("#modal-input-rincian").find('.modal-title').html('Input Rincian RKA');
		jQuery("#modal-input-rincian").find('.modal-body').html(form);
		jQuery("#modal-input-rincian").find('.modal-footer').html(''
			+'<button type="button" class="btn btn-warning" data-dismiss="modal">Tutup'
			+'</button>'
			+'<button type="button" class="btn btn-success" id="btn-simpan-data-rka" '
				+'data-action="submit_rka" '
			+'>Simpan'
			+'</button>');
		jQuery("#modal-input-rincian").css('margin-top', 20);
		jQuery("#modal-input-rincian").css('margin-top', 20);
		jQuery(".select-option").select2({width:'100%'});
		jQuery("#modal-input-rincian").modal('show');

		objekBelanja().then(function(){
			jenisStandarHarga();
		});
	});

	jQuery(document).on('change', "#daftar-objek-belanja", function(){
		
		jQuery("#wrap-loading").show();

		let objekBelanja = jQuery("#daftar-objek-belanja").val();
		
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

					jQuery("#wrap-loading").hide();

					let opt=`<option value="-">Pilih Rekening / Akun</option>`;
					response.items.map(function(item, index){
						opt+=`<option value="${item.kode_akun}">${item.kode_akun} ${item.nama_akun}</option>`;
					});
					jQuery("#daftar-rekening-akun").html(opt);
				}
			})
	})

	jQuery(document).on('change', "#daftar-rekening-akun", function(){
				
	})

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
			})
	})

	function objekBelanja(){
		return new Promise(function(resolve, reject){
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
					let opt=`<option value="-">Pilih Objek Belanja</option>`;
					response.items.map(function(item, index){
						opt+=`<option value="${item.kode_akun}">${item.nama_akun}</option>`;
					});
					jQuery("#daftar-objek-belanja").html(opt);
					resolve();
				}
			})
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