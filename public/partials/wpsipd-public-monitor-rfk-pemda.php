<?php
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

$public = 0;
if(!empty($_GET) && !empty($_GET['key'])){
	$keys = $this->decode_key($_GET['key']);
	if(!empty($keys['public']) && $keys['public']==1){
		$public = 1;
	}
}

$sumber_pagu = '1';
if(!empty($_GET) && !empty($_GET['sumber_pagu'])){
    $sumber_pagu = $_GET['sumber_pagu'];
}

$bulan = date('m');
if(!empty($_GET) && !empty($_GET['bulan'])){
    $bulan = $_GET['bulan'];
}
$nama_bulan = $this->get_bulan($bulan);

$pengaturan = $wpdb->get_results($wpdb->prepare("
	select 
		* 
	from data_pengaturan_sipd 
	where tahun_anggaran=%d
", $input['tahun_anggaran']), ARRAY_A);
$nama_pemda = $pengaturan[0]['daerah'];

$body = "";
$body .='

	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.25/datatables.min.css"/>
	<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.25/datatables.min.js"></script>

	<style>
		.background-status {
			background-color: #fdf6a5;
		}
	</style>

	<!-- Modal -->
	<div class="modal fade bd-example-modal-xl" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLabel" style="margin: 0 auto; text-align:center; font-weight: bold">Modal title</h5>
	      </div>
	      <div class="modal-body">

	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
	      </div>
	    </div>
	  </div>
	</div>
	
	<div id="cetak" title="Laporan RFK" style="padding: 5px;">
		<h4 style="text-align: center; margin: 0; font-weight: bold;">REALISASI FISIK DAN KEUANGAN (RFK)<br>'.$nama_pemda.'<br>Bulan '.$nama_bulan.' Tahun '.$input['tahun_anggaran'].'</h4>
		<table id="table-rfk" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; table-layout:fixed; overflow-wrap: break-word; font-size: 70%; border: 0;">
		    <thead>
		    	<tr>
		    		<th style="padding: 0; border: 0; width:150px"></th>
		            <th style="padding: 0; border: 0;"></th>
		            <th style="padding: 0; border: 0; width:140px"></th>
		            <th style="padding: 0; border: 0; width:140px"></th>
		            <th style="padding: 0; border: 0; width:140px"></th>
		            <th style="padding: 0; border: 0; width:110px"></th>
		            <th style="padding: 0; border: 0; width:100px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:90px"></th>
		            <th style="padding: 0; border: 0; width:90px"></th>
		    	</tr>
		    	<tr>
			    	<th class="atas kanan bawah kiri text_tengah text_blok">Kode</th>
			        <th class="atas kanan bawah text_tengah text_blok">Nama SKPD</th>
			        <th class="atas kanan bawah text_tengah text_blok">RKA SIPD (Rp.)</th>
			        <th class="atas kanan bawah text_tengah text_blok">DPA SIMDA (Rp.)</th>
			        <th class="atas kanan bawah text_tengah text_blok">Realisasi Keuangan (Rp.)</th>
			        <th class="atas kanan bawah text_tengah text_blok">Capaian ( % )</th>
			        <th class="atas kanan bawah text_tengah text_blok">RAK SIMDA ( % )</th>
			        <th class="atas kanan bawah text_tengah text_blok">Deviasi ( % )</th>
			        <th class="atas kanan bawah text_tengah text_blok">Realisasi Fisik ( % )</th>
			        <th class="atas kanan bawah text_tengah text_blok">Update Terakhir</th>
			    </tr>
			    <tr>
			    	<th class="atas kanan bawah kiri text_tengah text_blok">1</th>
			        <th class="atas kanan bawah text_tengah text_blok">2</th>
			        <th class="atas kanan bawah text_tengah text_blok">3</th>
			        <th class="atas kanan bawah text_tengah text_blok">4</th>
			        <th class="atas kanan bawah text_tengah text_blok">5</th>
			        <th class="atas kanan bawah text_tengah text_blok">6 = (5 / 4) * 100</th>
			        <th class="atas kanan bawah text_tengah text_blok">7</th>
			        <th class="atas kanan bawah text_tengah text_blok">8 = ((7 - 6) / 7) * 100</th>
			        <th class="atas kanan bawah text_tengah text_blok">9</th>
			        <th class="atas kanan bawah text_tengah text_blok">10</th>
			    </tr>
		    </thead>
		    <tbody>
		    ';
		    // die($body);
		    $units = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd, is_skpd FROM data_unit WHERE active=1 AND tahun_anggaran=".$input['tahun_anggaran'].' AND is_skpd=1 ORDER BY kode_skpd ASC', ARRAY_A);
			$current_user = wp_get_current_user();
			$data_all = array(
				'data' => array(),
				'total_rka_sipd' => 0,
				'total_dpa_sipd' => 0,
				'total_realisasi_keuangan' => 0,
				'capaian' => array(),
				'total_rak_simda' => 0,
				'target_rak' => array(),
				'realisasi_fisik' => array(),
				'deviasi' => array()
			);
		    foreach($units as $unit){

		    	$sub_units = $wpdb->get_results("SELECT id_skpd, idinduk, kode_skpd, nama_skpd from data_unit where active=1 and tahun_anggaran=".$input['tahun_anggaran']." and idinduk=".$unit['id_skpd']." order by kode_skpd ASC", ARRAY_A);

		    	if(count($sub_units) == 1){
		    		$data_rfk = $wpdb->get_results($wpdb->prepare("
							SELECT 
								IFNULL(SUM(k.pagu),0) pagu, 
								IFNULL(SUM(k.pagu_simda),0) pagu_simda, 
								IFNULL(SUM(d.realisasi_anggaran),0) realisasi_keuangan,
								IFNULL((SUM(d.realisasi_anggaran)/SUM(k.pagu_simda)*100),0) capaian, 
								AVG(IFNULL(d.realisasi_fisik,0)) realisasi_fisik, 
								IFNULL(SUM(d.rak),0) rak,
								IFNULL((SUM(d.rak)/SUM(k.pagu_simda)*100),0) target_rak,
								IFNULL(((IFNULL((SUM(d.rak)/SUM(k.pagu_simda)*100),0)-IFNULL((SUM(d.realisasi_anggaran)/SUM(k.pagu_simda)*100),0)) / (IFNULL((SUM(d.rak)/SUM(k.pagu_simda)*100),0))),0) * 100 deviasi
							FROM data_sub_keg_bl k 
							LEFT JOIN data_rfk d 
								ON d.id_skpd=k.id_sub_skpd AND 
								d.kode_sbl=k.kode_sbl AND 
								d.tahun_anggaran=k.tahun_anggaran 
							WHERE 
								k.tahun_anggaran=%d AND 
								k.id_sub_skpd=%d AND 
								k.active=1 AND 
								bulan=%d
							", 
								$input['tahun_anggaran'],
								$unit['id_skpd'],
								$bulan
					), ARRAY_A);
					// die($wpdb->last_query);

					foreach ($data_rfk as $key => $rfk) {
						$nama_page = 'RFK '.$unit['nama_skpd'].' '.$unit['kode_skpd'].' | '.$input['tahun_anggaran'];
						$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
						$link = $this->get_link_post($custom_post);
						$latest_update = $this->get_date_rfk_update(array('id_skpd'=>$unit['id_skpd'], 'tahun_anggaran' => $input['tahun_anggaran'], 'bulan'=>$bulan));
						
						$data_all['data'][] = array(
			    			'id_skpd' => $unit['id_skpd'],
			    			'kode_skpd' => $unit['kode_skpd'],
			    			'nama_skpd' => $unit['nama_skpd'],
			    			'rka_sipd' => $rfk['pagu'],
			    			'dpa_sipd' => $rfk['pagu_simda'],
			    			'realisasi_keuangan' => $rfk['realisasi_keuangan'],
			    			'capaian' => $this->pembulatan($rfk['capaian']),
			    			'rak' => $rfk['rak'],
			    			'target_rak' => $this->pembulatan($rfk['target_rak']),
			    			'deviasi' => $this->pembulatan($rfk['deviasi']),
			    			'realisasi_fisik' => $this->pembulatan($rfk['realisasi_fisik']),
			    			'last_update' => $latest_update,
			    			'data_sub_unit' => '',
			    			'url_unit' => $link,
			    			'act' => ''
			    		);

			    		$data_all['total_rka_sipd']+=$rfk['pagu'];
			    		$data_all['total_dpa_sipd']+=$rfk['pagu_simda'];
			    		$data_all['total_realisasi_keuangan']+=$rfk['realisasi_keuangan'];
			    		$data_all['capaian'][]=$rfk['capaian'];
			    		$data_all['total_rak_simda']+=$rfk['rak'];
			    		$data_all['target_rak'][]=$rfk['target_rak'];
			    		$data_all['realisasi_fisik'][]=$rfk['realisasi_fisik'];
			    		$data_all['deviasi'][]=$rfk['deviasi'];
					}

		    	}elseif(count($sub_units) > 1){
		    	
		    		$pagu_sub_unit=0;
		    		$pagu_simda_sub_unit=0;
		    		$realisasi_anggaran_sub_unit=0;
		    		$rak_sub_unit=0;
		    		$realisasi_fisik_sub_unit=array();
		    		$data_all_sub_unit = array();
		    		
		    		foreach ($sub_units as $key => $sub_unit) {
		    			
			    		$data_rfk = $wpdb->get_results($wpdb->prepare("
								SELECT 
									IFNULL(SUM(k.pagu),0) pagu, 
									IFNULL(SUM(k.pagu_simda),0) pagu_simda, 
									IFNULL(SUM(d.realisasi_anggaran),0) realisasi_keuangan,
									IFNULL((SUM(d.realisasi_anggaran)/SUM(k.pagu_simda)*100),0) capaian, 
									AVG(IFNULL(d.realisasi_fisik,0)) realisasi_fisik, 
									IFNULL(SUM(d.rak),0) rak,
									IFNULL((SUM(d.rak)/SUM(k.pagu_simda)*100),0) target_rak,
									IFNULL(((IFNULL((SUM(d.rak)/SUM(k.pagu_simda)*100),0)-IFNULL((SUM(d.realisasi_anggaran)/SUM(k.pagu_simda)*100),0)) / (IFNULL((SUM(d.rak)/SUM(k.pagu_simda)*100),0))),0) * 100 deviasi
								FROM data_sub_keg_bl k 
								LEFT JOIN data_rfk d 
									ON d.id_skpd=k.id_sub_skpd AND 
									d.kode_sbl=k.kode_sbl AND 
									d.tahun_anggaran=k.tahun_anggaran 
								WHERE 
									k.tahun_anggaran=%d AND 
									k.id_sub_skpd=%d AND 
									k.active=1 AND 
									bulan=%d
								", 
									$input['tahun_anggaran'],
									$sub_unit['id_skpd'],
									$bulan
						), ARRAY_A);
						// die($wpdb->last_query);

			    		foreach ($data_rfk as $key => $rfk) {
							$pagu_sub_unit+=$rfk['pagu'];
							$pagu_simda_sub_unit+=$rfk['pagu_simda'];
							$realisasi_anggaran_sub_unit+=$rfk['realisasi_keuangan'];
							$rak_sub_unit+=$rfk['rak'];
							$realisasi_fisik_sub_unit[]=$rfk['realisasi_fisik'];

							$latest_update_sub_unit = $this->get_date_rfk_update(array('id_skpd'=>$sub_unit['id_skpd'], 'tahun_anggaran' => $input['tahun_anggaran'], 'bulan'=>$bulan, 'type'=>'sub_unit'));
							$nama_page_sub = 'RFK '.$sub_unit['nama_skpd'].' '.$sub_unit['kode_skpd'].' | '.$input['tahun_anggaran'];
							$custom_post_sub = get_page_by_title($nama_page_sub, OBJECT, 'page');
							$link = '#';
							if(empty($public)){
								$link = $this->get_link_post($custom_post_sub);
							}

							$data_all_sub_unit[] = array(
				    			'id_skpd_induk' => $unit['id_skpd'],
				    			'kode_skpd' => $sub_unit['kode_skpd'],
				    			'nama_skpd' => $sub_unit['nama_skpd'],
				    			'rka_sipd' => $rfk['pagu'],
				    			'dpa_sipd' => $rfk['pagu_simda'],
				    			'realisasi_keuangan' => $rfk['realisasi_keuangan'],
				    			'capaian' => $this->pembulatan($rfk['capaian']),
				    			'rak' => $rfk['rak'],
				    			'target_rak' => $this->pembulatan($rfk['target_rak']),
				    			'deviasi' => $this->pembulatan($rfk['deviasi']),
				    			'realisasi_fisik' => $this->pembulatan($rfk['realisasi_fisik']),
				    			'last_update' => $latest_update_sub_unit,
				    			'url_sub_unit' => $link
				    		);

							$data_all['total_rka_sipd']+=$rfk['pagu'];
				    		$data_all['total_dpa_sipd']+=$rfk['pagu_simda'];
				    		$data_all['total_realisasi_keuangan']+=$rfk['realisasi_keuangan'];
				    		$data_all['total_rak_simda']+=$rfk['rak'];
						}
		    		}

		    		$latest_update = $this->get_date_rfk_update(array('id_skpd'=>$unit['id_skpd'], 'tahun_anggaran' => $input['tahun_anggaran'], 'bulan'=>$bulan));
		    		$data_all['data'][] = array(
			    			'id_skpd' => $unit['id_skpd'],
			    			'kode_skpd' => $unit['kode_skpd'],
			    			'nama_skpd' => $unit['nama_skpd'],
			    			'rka_sipd' => $pagu_sub_unit,
			    			'dpa_sipd' => $pagu_simda_sub_unit,
			    			'realisasi_keuangan' => $realisasi_anggaran_sub_unit,
			    			'capaian' => !empty($pagu_simda_sub_unit) ? $this->pembulatan(($realisasi_anggaran_sub_unit/$pagu_simda_sub_unit)*100) : 0,
			    			'rak' => $rak_sub_unit,
			    			'target_rak' => !empty($pagu_simda_sub_unit) ? $this->pembulatan(($rak_sub_unit/$pagu_simda_sub_unit)*100) : 0,
			    			'realisasi_fisik' => !empty($realisasi_fisik_sub_unit) ? $this->pembulatan(array_sum($realisasi_fisik_sub_unit)/count($realisasi_fisik_sub_unit)) : 0,
			    			'deviasi' => !empty($pagu_simda_sub_unit) ? $this->pembulatan(
			    				(
			    					(
			    						($rak_sub_unit/$pagu_simda_sub_unit*100) - ($realisasi_anggaran_sub_unit/$pagu_simda_sub_unit)*100
			    					) / ($rak_sub_unit/$pagu_simda_sub_unit*100)
			    				) * 100
			    			) : 0,
			    			'last_update' => $latest_update,
			    			'data_sub_unit' => $data_all_sub_unit,
			    			'url_unit' => '',
			    			'act' => '<a href="javascript:void(0)" onclick="showsubunit(\''.$unit['id_skpd'].'\', \''.$bulan.'\', \''.$input['tahun_anggaran'].'\')">'.$unit['nama_skpd'].'</a>'
			    	);
			    	$data_all['realisasi_fisik'][]=!empty($realisasi_fisik_sub_unit) ? (array_sum($realisasi_fisik_sub_unit)/count($realisasi_fisik_sub_unit)) : 0;
		    	}
			}

	foreach ($data_all['data'] as $key => $value) {

		$tag = $value['nama_skpd'];
		if(empty($public)){
			$tag = '<a href="'.$value['url_unit'].'" target="_blank">'.$value['nama_skpd'].'</a> ';
		}

		$status_update = array();
		if(isset($value['act']) && $value['act'] != ''){
			$tag = $value['act'];
			foreach ($value['data_sub_unit'] as $k => $v) {
				if($v['last_update']=='-'){
					$status_update[]=$v['last_update'];
				}
			}
		}else{
			if($value['last_update']=='-'){
				$status_update[]=$value['last_update'];
			}
		}
		$background = !empty(count($status_update)) ? 'background-status' : '';

		$body.='
	    	<tr>
			    <td class="atas kanan bawah kiri text_tengah" data-search="'.$value['kode_skpd'].'">'.$value['kode_skpd'].' </td>
			    <td class="atas kanan bawah text_kiri" data-search="'.$value['nama_skpd'].'">'.$tag.'</td>
			    <td class="atas kanan bawah text_kanan" data-order="'.$value['rka_sipd'].'">'.number_format($value['rka_sipd'],0,",",".").'</td>
			    <td class="atas kanan bawah text_kanan" data-order="'.$value['dpa_sipd'].'">'.number_format($value['dpa_sipd'],0,",",".").'</td>
			    <td class="atas kanan bawah text_kanan" data-order="'.$value['realisasi_keuangan'].'">'.number_format($value['realisasi_keuangan'],0,",",".").'</td>
			    <td class="atas kanan bawah text_tengah">'.$value['capaian'].'</td>
			    <td class="atas kanan bawah text_tengah" data="'.$value['rak'].'">'.$value['target_rak'].'</td>
			    <td class="atas kanan bawah text_tengah">'.$value['deviasi'].'</td>
			    <td class="atas kanan bawah text_tengah">'.$value['realisasi_fisik'].'</td>
			    <td class="atas kanan bawah text_tengah '.$background.'">'.$value['last_update'].'</td>
			</tr>
		';
	}

	$total_rka_sipd = $data_all['total_rka_sipd'];
	$total_dpa_sipd = $data_all['total_dpa_sipd'];
	$total_realisasi_keuangan = $data_all['total_realisasi_keuangan'];
	$capaian = !empty($total_dpa_sipd) ? ($total_realisasi_keuangan/$total_dpa_sipd)*100 : 0;
	$total_rak_simda = $data_all['total_rak_simda'];
	$target_rak_simda = !empty($total_dpa_sipd) ? ($total_rak_simda/$total_dpa_sipd)*100 : 0;
	$deviasi = !empty($target_rak_simda) ? (($target_rak_simda-$capaian)/$target_rak_simda)*100 : 0;

	$body .='</tbody>
				<tfoot>
					<tr>
						<th class="kiri kanan bawah text_blok text_kanan" colspan="2">TOTAL</th>
					    <th class="kanan bawah text_kanan text_blok">'.number_format($total_rka_sipd,0,",",".").'</th>
					    <th class="kanan bawah text_kanan text_blok">'.number_format($total_dpa_sipd,0,",",".").'</th>
					    <th class="kanan bawah text_kanan text_blok">'.number_format($total_realisasi_keuangan,0,",",".").'</th>
					    <th class="kanan bawah text_tengah text_blok">'.$this->pembulatan($capaian).'</th>
					    <th class="kanan bawah text_tengah text_blok" data-rak="'.$data_all['total_rak_simda'].'">'.$this->pembulatan($target_rak_simda).'</th>
					    <th class="kanan bawah text_tengah text_blok">'.$this->pembulatan($deviasi).'</th>
					    <th class="kanan bawah text_blok total-realisasi-fisik text_tengah">'.$this->pembulatan(array_sum($data_all['realisasi_fisik'])/count($data_all['realisasi_fisik'])).'</th>
						<th class="atas kanan bawah text_tengah"></th>
					</tr>
				</tfoot>
			</table>
	</div>';
	
	echo $body;
?>

<script type="text/javascript">
	<?php if(empty($public)){ ?>
		run_download_excel();
	<?php } ?>
	var _url = window.location.href;
    var url = new URL(_url);
    var param = [];
    _url = url.origin+url.pathname+'?'+param.join('&');
	var extend_action = ''
		+'<div style="margin-top: 20px;">'
			+'<label style="margin-left: 20px;">Bulan Realisasi: '
				+'<select id="pilih_bulan" style="padding: 5px;">'
					+'<option value="0">-- Bulan --</option>'
					+'<option value="1">Januari</option>'
					+'<option value="2">Februari</option>'
					+'<option value="3">Maret</option>'
					+'<option value="4">April</option>'
					+'<option value="5">Mei</option>'
					+'<option value="6">Juni</option>'
					+'<option value="7">Juli</option>'
					+'<option value="8">Agustus</option>'
					+'<option value="9">September</option>'
					+'<option value="10">Oktober</option>'
					+'<option value="11">November</option>'
					+'<option value="12">Desember</option>'
				+'</select>'
			+'</label>'
		+'</div>';
		let data_all_rfk = <?php echo json_encode($data_all['data']); ?>;

		jQuery(document).ready(function(){
			jQuery('<a id="open_all_skpd" onclick="return false;" href="#" class="button button-primary" style="margin-left:5px">AKSES RFK ALL OPD</a>').insertAfter("#excel");
			jQuery('#action-sipd').append(extend_action);
			jQuery('#pilih_bulan').val(+<?php echo $bulan; ?>);
			jQuery('#pilih_bulan').on('change', function(){
		    	var val = +jQuery(this).val();
		    	if(val > 0){
		    		window.open(_url+'&bulan='+val,'_blank');
		    	}
		    	jQuery('#pilih_bulan').val(+<?php echo $bulan; ?>);
		    });

		    jQuery('#open_all_skpd').on('click', function(){

		    	var time = prompt("Isi jeda akses RFK semua OPD (detik)", 30);
		    	var no = 0;
		    	var length = data_all_rfk.length;
		    	var data_all = [];

		    	if(time === null){
		    		return;
		    	}

		    	jQuery("#wrap-loading").css('display','block');
		    	data_all_rfk.map(function(val){
		    		if(val.data_sub_unit.length > 0){
		    			val.data_sub_unit.map(function(val2){
		    				data_all.push(val2.url_sub_unit);
		    			})
		    		}else{
		    			data_all.push(val.url_unit);
		    		}
		    	})
		    	
		    	var interval = setInterval(function(){
		    		if(no == data_all.length-1){ 
						clearInterval(interval) 
		    			jQuery("#wrap-loading").css('display','none');
					}; 

					console.log(data_all[no]+'&page_close=1');
					window.open(data_all[no]+'&page_close=1');
					no++;
				}, time*1000);
		    })

		    jQuery('#table-rfk').DataTable();
		})
		
		function showsubunit(id_induk, bulan, tahun){
			let nama_bulan = get_bulan(bulan);
			let modal_subunit = jQuery("#exampleModal");
			modal_subunit.find('.modal-title').html('');
			modal_subunit.find('.modal-body').html('');

			let html = '';
				html+='<table id="table-rfk-sub-unit" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; table-layout:fixed; overflow-wrap: break-word; font-size: 70%; border: 0;">'
					    +'<thead>'
					    	+'<tr>'
					    		+'<th style="padding: 0; border: 0; width:125px"></th>'
					            +'<th style="padding: 0; border: 0"></th>'
					            +'<th style="padding: 0; border: 0; width:140px"></th>'
					            +'<th style="padding: 0; border: 0; width:140px"></th>'
					            +'<th style="padding: 0; border: 0; width:140px"></th>'
					            +'<th style="padding: 0; border: 0; width:110px"></th>'
					            +'<th style="padding: 0; border: 0; width:75px"></th>'
					            +'<th style="padding: 0; border: 0; width:120px"></th>'
					            +'<th style="padding: 0; border: 0; width:90px"></th>'
					            +'<th style="padding: 0; border: 0; width:80px"></th>'
					    	+'</tr>'
					    	+'<tr>'
						    	+'<th class="atas kanan bawah kiri text_tengah text_blok">Kode</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">Nama SKPD</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">RKA SIPD (Rp.)</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">DPA SIMDA (Rp.)</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">Realisasi Keuangan (Rp.)</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">Capaian ( % )</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">RAK SIMDA ( % )</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">Deviasi ( % )</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">Realisasi Fisik ( % )</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">Update Terakhir</th>'
						    +'</tr>'
						    +'<tr>'
						    	+'<th class="atas kanan bawah kiri text_tengah text_blok">1</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">2</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">3</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">4</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">5</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">6 = (5 / 4) * 100</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">7</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">8 = ((7 - 6) / 7) * 100</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">9</th>'
						        +'<th class="atas kanan bawah text_tengah text_blok">10</th>'
						    +'</tr>'
					    +'</thead>'
					    +'<tbody>';

					    let i = 0;
					    let index = 0;
					    data_all_rfk.map(function(data){
							if(data.id_skpd==id_induk){
								index = i;
								data.data_sub_unit.map(function(data_sub_unit){
									<?php if(empty($public)){ ?>
										var link_sub_unit = '<a href='+data_sub_unit.url_sub_unit+' target="_blank">'+data_sub_unit.nama_skpd+'</a>';
									<?php }else{ ?>
										var link_sub_unit = data_sub_unit.nama_skpd;
									<?php } ?>
									html += ''
										+'<tr>'
											+ '<td class="atas kanan bawah kiri text_tengah">'+data_sub_unit.kode_skpd+'</td>'
											+ '<td class="atas kanan bawah text_kiri" data-search="'+data_sub_unit.nama_skpd+'">'+link_sub_unit+'</td>'
											+ '<td class="kanan bawah text_kanan" data-order="'+data_sub_unit.rka_sipd+'">'+formatRupiah(data_sub_unit.rka_sipd)+'</td>'
											+ '<td class="kanan bawah text_kanan" data-order="'+data_sub_unit.dpa_sipd+'">'+formatRupiah(data_sub_unit.dpa_sipd)+'</td>'
											+ '<td class="kanan bawah text_kanan" data-order="'+data_sub_unit.realisasi_keuangan+'">'+formatRupiah(data_sub_unit.realisasi_keuangan)+'</td>'
											+ '<td class="kanan bawah text_tengah">'+data_sub_unit.capaian+'</td>'
											+ '<td class="kanan bawah text_tengah">'+data_sub_unit.target_rak+'</td>'
											+ '<td class="kanan bawah text_tengah">'+data_sub_unit.deviasi+'</td>'
											+ '<td class="kanan bawah text_tengah">'+data_sub_unit.realisasi_fisik+'</td>'
											+ '<td class="kanan bawah text_tengah">'+data_sub_unit.last_update+'</td>'
										+'</tr>'
								});
							}
							i++;
						});

						html +='</tbody>'
								+'<tfoot>'
									+'<tr>'
										+'<th style="border:1px solid" kiri class="kanan bawah text_blok text_kanan" colspan="2">TOTAL</th>'
										+'<th style="border:1px solid" class="kanan bawah text_kanan">'+formatRupiah(data_all_rfk[index].rka_sipd)+'</th>'
										+'<th style="border:1px solid" class="kanan bawah text_kanan">'+formatRupiah(data_all_rfk[index].dpa_sipd)+'</th>'
										+'<th style="border:1px solid" class="kanan bawah text_kanan">'+formatRupiah(data_all_rfk[index].realisasi_keuangan)+'</th>'
										+'<th style="border:1px solid" class="kanan bawah text_tengah">'+data_all_rfk[index].capaian+'</th>'
										+'<th style="border:1px solid" data-rfk="'+data_all_rfk[index].rak+'" class="kanan bawah text_tengah">'+data_all_rfk[index].target_rak+'</th>'
										+'<th style="border:1px solid" class="kanan bawah text_tengah">'+data_all_rfk[index].deviasi+'</th>'
										+'<th style="border:1px solid" class="kanan bawah text_tengah">'+data_all_rfk[index].realisasi_fisik+'</th>'
										+'<th style="border:1px solid"></th>'
									+'</tr>'
								+'</tfoot>'
						 +'</table>';

			modal_subunit.find('.modal-title').html('REALISASI FISIK DAN KEUANGAN (RFK) <br> ' + data_all_rfk[index].nama_skpd + '<br>' + ' Bulan ' + nama_bulan + ' Tahun ' + tahun);
			modal_subunit.find('.modal-body').html(html);
			modal_subunit.modal('show');

			jQuery('#table-rfk-sub-unit').DataTable();
		}

		function get_bulan(bulan) {
			let date = new Date();
			if(!bulan || bulan == '' || bulan <= 0){
				bulan = date.getMonth();
			}
			nama_bulan = [
				"Januari", 
				"Februari", 
				"Maret", 
				"April", 
				"Mei", 
				"Juni", 
				"Juli", 
				"Agustus", 
				"September", 
				"Oktober", 
				"November", 
				"Desember"
			];
			return nama_bulan[parseInt(bulan-1)];
		}
		
</script>
