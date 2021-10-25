<?php
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

$bulan = date('m');
$tahun_asli = date('Y');
$bulan_asli = date('m');

if(!empty($_GET) && !empty($_GET['bulan'])){
    $bulan = $_GET['bulan'];
}
$nama_bulan = $this->get_bulan($bulan);

if(empty($_GET['debug'])){
	if(
		$input['tahun_anggaran'] > $tahun_asli
		|| (
			$bulan > $bulan_asli
			&& $input['tahun_anggaran'] == $tahun_asli
		)
	){
		die('<h1>RFK Bulan '.$nama_bulan.' tahun '.$input['tahun_anggaran'].' tidak ditemukan!</h1>');
	}
}

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
		.simpan-per-unit { display: none; }
		.simpan-per-unit {
		    font-size: 10px;
		    cursor: pointer;
		    text-align: center;
		}
		table.dataTable tbody tr.odd.ubah-warna {
		 	background-color:#ffbc0073
		}
	</style>

	<!-- Modal -->
	<div class="modal fade bd-example-modal-xl" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="min-width:1250px" role="document">
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
	</div>';
	
	if(empty($public)){
		$body .='<input type="hidden" value="'.get_option( '_crb_api_key_extension' ).'" id="api_key">';
	}
	
	$body .='<div id="cetak" title="Laporan RFK" style="padding: 5px;">
		<h4 style="text-align: center; margin: 0; font-weight: bold;">REALISASI FISIK DAN KEUANGAN (RFK)<br>'.$nama_pemda.'<br>Bulan '.$nama_bulan.' Tahun '.$input['tahun_anggaran'].'</h4>
		<table id="table-rfk" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; table-layout:fixed; overflow-wrap: break-word; font-size: 70%; border: 0;">
		    <thead>
		    	<tr>
		    		<th style="padding: 0; border: 0; width:150px"></th>
		            <th style="padding: 0; border: 0;"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:110px"></th>
		            <th style="padding: 0; border: 0; width:100px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:90px"></th>
		            <th style="padding: 0; border: 0; width:90px"></th>
		            <th style="padding: 0; border: 0; width:100px"></th>
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
			        <th class="atas kanan bawah text_tengah text_blok">Catatan Ka.Adbang</th>
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
			        <th class="atas kanan bawah text_tengah text_blok">11</th>
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
								IFNULL((SUM(d.realisasi_anggaran)/SUM(k.pagu_simda)*100),100) capaian,
								e.rak,
								e.realisasi_fisik,
								f.cat_ka_adbang
							FROM data_sub_keg_bl k 
							LEFT JOIN data_rfk d 
								ON d.id_skpd=k.id_sub_skpd AND 
								d.kode_sbl=k.kode_sbl AND 
								d.tahun_anggaran=k.tahun_anggaran
							LEFT JOIN (
								SELECT 
										ds.tahun_anggaran,
										ds.id_sub_skpd,
										dr.bulan,
										SUM(IFNULL(dr.rak,0)) rak,
										AVG(IFNULL(dr.realisasi_fisik,0)) realisasi_fisik 
									FROM data_sub_keg_bl ds
									LEFT JOIN data_rfk dr
										ON dr.id_skpd=ds.id_sub_skpd AND
										dr.kode_sbl=ds.kode_sbl AND
										dr.tahun_anggaran=ds.tahun_anggaran
									WHERE
										ds.tahun_anggaran=".$input['tahun_anggaran']." AND
										ds.id_sub_skpd=".$unit['id_skpd']." AND
										dr.bulan=".$bulan." AND
										ds.pagu_simda > 0
							) e ON
									e.id_sub_skpd=k.id_sub_skpd AND 
									e.bulan=d.bulan and
									e.tahun_anggaran=k.tahun_anggaran
							LEFT JOIN (
								SELECT
									id_skpd,
									bulan,
									tahun_anggaran,
									IFNULL(catatan_ka_adbang, '') cat_ka_adbang 
								FROM data_catatan_rfk_unit
								WHERE 
									id_skpd=".$unit['id_skpd']." AND 
									bulan=".$bulan." AND 
									tahun_anggaran=".$input['tahun_anggaran']."
							) f ON
								f.id_skpd=k.id_sub_skpd AND 
								f.bulan=d.bulan AND 
								f.tahun_anggaran=k.tahun_anggaran
							WHERE 
								k.tahun_anggaran=%d AND 
								k.id_sub_skpd=%d AND 
								k.active=1 AND 
								d.bulan=%d",
								$input['tahun_anggaran'],
								$unit['id_skpd'],
								$bulan
							), ARRAY_A);

					foreach ($data_rfk as $key => $rfk) {
						$nama_page = 'RFK '.$unit['nama_skpd'].' '.$unit['kode_skpd'].' | '.$input['tahun_anggaran'];
						$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
						$link = $this->get_link_post($custom_post);
						$latest_update = $this->get_date_rfk_update(array('id_skpd'=>$unit['id_skpd'], 'tahun_anggaran' => $input['tahun_anggaran'], 'bulan'=>$bulan));

						$target_rak = !empty($rfk['pagu_simda']) ? ($rfk['rak'] / $rfk['pagu_simda']) * 100 : 100;
						$deviasi = !empty($target_rak) ? (($target_rak-$rfk['capaian'])/$target_rak) * 100 : 100;
						
						$data_all['data'][] = array(
			    			'id_skpd' => $unit['id_skpd'],
			    			'kode_skpd' => $unit['kode_skpd'],
			    			'nama_skpd' => $unit['nama_skpd'],
			    			'rka_sipd' => $rfk['pagu'],
			    			'dpa_sipd' => $rfk['pagu_simda'],
			    			'realisasi_keuangan' => $rfk['realisasi_keuangan'],
			    			'capaian' => $this->pembulatan($rfk['capaian']),
			    			'rak' => $rfk['rak'],
			    			'target_rak' => $this->pembulatan($target_rak),
			    			'deviasi' => $this->pembulatan($deviasi),
			    			'realisasi_fisik' => $this->pembulatan($rfk['realisasi_fisik']),
			    			'cat_ka_adbang' => $rfk['cat_ka_adbang'],
			    			'last_update' => $latest_update,
			    			'data_sub_unit' => '',
			    			'url_unit' => $link.'&bulan='.$bulan,
			    			'act' => ''
			    		);

			    		$data_all['total_rka_sipd']+=$rfk['pagu'];
			    		$data_all['total_dpa_sipd']+=$rfk['pagu_simda'];
			    		$data_all['total_realisasi_keuangan']+=$rfk['realisasi_keuangan'];
			    		$data_all['capaian'][]=$rfk['capaian'];
			    		$data_all['total_rak_simda']+=$rfk['rak'];
			    		$data_all['target_rak'][]=$target_rak;
			    		$data_all['realisasi_fisik'][]=$rfk['realisasi_fisik'];
			    		$data_all['deviasi'][]=$deviasi;
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
								IFNULL((SUM(d.realisasi_anggaran)/SUM(k.pagu_simda)*100),100) capaian,
								e.rak,
								e.realisasi_fisik,
								f.cat_ka_adbang
							FROM data_sub_keg_bl k 
							LEFT JOIN data_rfk d 
								ON d.id_skpd=k.id_sub_skpd AND 
								d.kode_sbl=k.kode_sbl AND 
								d.tahun_anggaran=k.tahun_anggaran
							LEFT JOIN (
								SELECT 
										ds.tahun_anggaran,
										ds.id_sub_skpd,
										dr.bulan,
										SUM(IFNULL(dr.rak,0)) rak,
										AVG(IFNULL(dr.realisasi_fisik,0)) realisasi_fisik 
									FROM data_sub_keg_bl ds
									LEFT JOIN data_rfk dr
										ON dr.id_skpd=ds.id_sub_skpd AND
										dr.kode_sbl=ds.kode_sbl AND
										dr.tahun_anggaran=ds.tahun_anggaran
									WHERE
										ds.tahun_anggaran=".$input['tahun_anggaran']." AND
										ds.id_sub_skpd=".$sub_unit['id_skpd']." AND
										dr.bulan=".$bulan." AND
										ds.pagu_simda > 0
							) e ON
									e.id_sub_skpd=k.id_sub_skpd AND 
									e.bulan=d.bulan and
									e.tahun_anggaran=k.tahun_anggaran
							LEFT JOIN (
								SELECT
									id_skpd,
									bulan,
									tahun_anggaran,
									IFNULL(catatan_ka_adbang, '') cat_ka_adbang 
								FROM data_catatan_rfk_unit
								WHERE 
									id_skpd=".$sub_unit['id_skpd']." AND 
									bulan=".$bulan." AND 
									tahun_anggaran=".$input['tahun_anggaran']."
							) f ON
								f.id_skpd=k.id_sub_skpd AND 
								f.bulan=d.bulan AND 
								f.tahun_anggaran=k.tahun_anggaran
							WHERE 
								k.tahun_anggaran=%d AND 
								k.id_sub_skpd=%d AND 
								k.active=1 AND 
								d.bulan=%d",
								$input['tahun_anggaran'],
								$sub_unit['id_skpd'],
								$bulan
							), ARRAY_A);

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

							$target_rak_sub_unit = !empty($rfk['pagu_simda']) ? ($rfk['rak'] / $rfk['pagu_simda']) * 100 : 100;
							$deviasi_sub_unit = !empty($target_rak_sub_unit) ? (($target_rak_sub_unit-$rfk['capaian'])/$target_rak_sub_unit) * 100 : 100;

							$data_all_sub_unit[] = array(
				    			'id_skpd' => $sub_unit['id_skpd'],
				    			'id_skpd_induk' => $unit['id_skpd'],
				    			'kode_skpd' => $sub_unit['kode_skpd'],
				    			'nama_skpd' => $sub_unit['nama_skpd'],
				    			'rka_sipd' => $rfk['pagu'],
				    			'dpa_sipd' => $rfk['pagu_simda'],
				    			'realisasi_keuangan' => $rfk['realisasi_keuangan'],
				    			'capaian' => $this->pembulatan($rfk['capaian']),
				    			'rak' => $rfk['rak'],
				    			'target_rak' => $this->pembulatan($target_rak_sub_unit),
				    			'deviasi' => $this->pembulatan($deviasi_sub_unit),
				    			'realisasi_fisik' => $this->pembulatan($rfk['realisasi_fisik']),
			    				'cat_ka_adbang' => $rfk['cat_ka_adbang'],
				    			'last_update' => $latest_update_sub_unit,
				    			'url_sub_unit' => $link.'&bulan='.$bulan
				    		);

							$data_all['total_rka_sipd']+=$rfk['pagu'];
				    		$data_all['total_dpa_sipd']+=$rfk['pagu_simda'];
				    		$data_all['total_realisasi_keuangan']+=$rfk['realisasi_keuangan'];
				    		$data_all['total_rak_simda']+=$rfk['rak'];
						}
		    		}


		    		if(empty($realisasi_anggaran_sub_unit) && empty($pagu_simda_sub_unit))
		    		{
		    			$capaian = 100;
		    		}
		    		elseif(
		    				(empty($realisasi_anggaran_sub_unit) && !empty($pagu_simda_sub_unit)) || 
		    				(!empty($realisasi_anggaran_sub_unit) && !empty($pagu_simda_sub_unit))
		    			)
		    		{
		    			$capaian = ($realisasi_anggaran_sub_unit/$pagu_simda_sub_unit)*100;
		    		}elseif(!empty($realisasi_anggaran_sub_unit) && empty($pagu_simda_sub_unit))
		    		{
		    			$capaian = 0;
		    		}

		    		if(empty($rak_sub_unit) && empty($pagu_simda_sub_unit))
		    		{
		    			$target_rak = 100;
		    		}
		    		elseif(
		    				(empty($rak_sub_unit) && !empty($pagu_simda_sub_unit)) || 
		    				(!empty($rak_sub_unit) && !empty($pagu_simda_sub_unit))
		    			)
		    		{
		    			$target_rak = ($rak_sub_unit/$pagu_simda_sub_unit)*100;
		    		}elseif(!empty($rak_sub_unit) && empty($pagu_simda_sub_unit))
		    		{
		    			$target_rak = 0;
		    		}

		    		$deviasi = !empty($target_rak) ? (($target_rak-$capaian)/$target_rak) * 100 : 100;

		    		$latest_update = $this->get_date_rfk_update(array('id_skpd'=>$unit['id_skpd'], 'tahun_anggaran' => $input['tahun_anggaran'], 'bulan'=>$bulan));
		    		$cat_ka_adbang = $wpdb->get_row($wpdb->prepare("
		    			select 
		    				(case 
		    					when 
		    						(
		    							select count(id) from data_rfk 
		    							where id_skpd in 
		    								(
		    									select id_skpd from data_unit where idinduk=%d) and bulan=%d and tahun_anggaran=%d
		    								)!=0 
		    					then 
		    						(select catatan_ka_adbang from data_catatan_rfk_unit where id_skpd=%d and bulan=%d and tahun_anggaran=%d) 
		    					else NULL end
		    				) 
		    				catatan_ka_adbang",

		    				$unit['id_skpd'], 
		    				$bulan, 
		    				$input['tahun_anggaran'],
		    				
		    				"-".$unit['id_skpd'], 
		    				$bulan, 
		    				$input['tahun_anggaran']
		    			), ARRAY_A);

		    		// die($wpdb->last_query);
		    		$data_all['data'][] = array(
			    			'id_skpd' => $unit['id_skpd'],
			    			'kode_skpd' => $unit['kode_skpd'],
			    			'nama_skpd' => $unit['nama_skpd'],
			    			'rka_sipd' => $pagu_sub_unit,
			    			'dpa_sipd' => $pagu_simda_sub_unit,
			    			'realisasi_keuangan' => $realisasi_anggaran_sub_unit,
			    			'capaian' => $this->pembulatan($capaian),
			    			'rak' => $rak_sub_unit,
			    			'target_rak' => $this->pembulatan($target_rak),
			    			'realisasi_fisik' => !empty($realisasi_fisik_sub_unit) ? $this->pembulatan(array_sum($realisasi_fisik_sub_unit)/count($realisasi_fisik_sub_unit)) : 0,
			    			'deviasi' => $this->pembulatan($deviasi),
			    			'last_update' => $latest_update,
			    			'data_sub_unit' => $data_all_sub_unit,
			    			'cat_ka_adbang' => $cat_ka_adbang['catatan_ka_adbang'],
			    			'url_unit' => '',
			    			'act' => '<a href="javascript:void(0)" onclick="showsubunit(\''.$unit['id_skpd'].'\', \''.$bulan.'\', \''.$input['tahun_anggaran'].'\')">'.$unit['nama_skpd'].'</a>'
			    	);
			    	$data_all['realisasi_fisik'][]=!empty($realisasi_fisik_sub_unit) ? (array_sum($realisasi_fisik_sub_unit)/count($realisasi_fisik_sub_unit)) : 0;
		    	}
			}

	foreach ($data_all['data'] as $key => $value) {

		$editable = 'false';
		$tag = $value['nama_skpd'];
		if(empty($public)){
			$tag = '<a href="'.$value['url_unit'].'" target="_blank">'.$value['nama_skpd'].'</a> ';
			$editable = 'true';
		}
		
		$status_update = array();
		$catatan_rfk_class = 'catatan_rfk_unit';	
		$idskpd = $value['id_skpd'];
		$event = "</br></br><span class='badge badge-danger simpan-per-unit hide-excel'>SIMPAN</span>";	
		if(isset($value['act']) && $value['act'] != ''){
			
			// $editable = 'false';
			// $catatan_rfk_class = '';
			// $event = '';

			$tag = $value['act'];
			$idskpd = "-".$value['id_skpd'];
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
	    	<tr data-idskpd="'.$idskpd.'">
			    <td class="atas kanan bawah kiri text_tengah" data-search="'.$value['kode_skpd'].'">'.$value['kode_skpd'].'</td>
			    <td class="atas kanan bawah text_kiri" data-search="'.$value['nama_skpd'].'">'.$tag.'</td>
			    <td class="atas kanan bawah text_kanan" data-order="'.$value['rka_sipd'].'">'.number_format($value['rka_sipd'],0,",",".").'</td>
			    <td class="atas kanan bawah text_kanan" data-order="'.$value['dpa_sipd'].'">'.number_format($value['dpa_sipd'],0,",",".").'</td>
			    <td class="atas kanan bawah text_kanan" data-order="'.$value['realisasi_keuangan'].'">'.number_format($value['realisasi_keuangan'],0,",",".").'</td>
			    <td class="atas kanan bawah text_tengah">'.$value['capaian'].'</td>
			    <td class="atas kanan bawah text_tengah" data="'.$value['rak'].'">'.$value['target_rak'].'</td>
			    <td class="atas kanan bawah text_tengah">'.$value['deviasi'].'</td>
			    <td class="atas kanan bawah text_tengah">'.$value['realisasi_fisik'].'</td>
			    <td class="atas kanan bawah text_tengah '.$background.'">'.$value['last_update'].'  '.$event.'</td>
			    <td class="atas kanan bawah text_tengah '.$catatan_rfk_class.'" data-content="'.$value['cat_ka_adbang'].'" contenteditable="'.$editable.'">'.$value['cat_ka_adbang'].'</td>
			</tr>
		';
	}

	$total_rka_sipd = $data_all['total_rka_sipd'];
	$total_dpa_sipd = $data_all['total_dpa_sipd'];
	$total_realisasi_keuangan = $data_all['total_realisasi_keuangan'];
	$capaian = !empty($total_dpa_sipd) ? ($total_realisasi_keuangan/$total_dpa_sipd)*100 : 100;
	$total_rak_simda = $data_all['total_rak_simda'];
	$target_rak_simda = !empty($total_dpa_sipd) ? ($total_rak_simda/$total_dpa_sipd)*100 : 100;
	$deviasi = !empty($target_rak_simda) ? (($target_rak_simda-$capaian)/$target_rak_simda)*100 : 100;

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
						<th class="atas kanan bawah text_tengah"></th>
					</tr>
				</tfoot>
			</table>
	</div>';
	
	echo $body;
?>

<div class="hide-print" id="catatan_dokumentasi" style="max-width: 1200px; margin: auto;">
	<h4 style="margin: 30px 0 10px; font-weight: bold;">Catatan Dokumentasi:</h4>
	<ul>
		<li>Laporan RFK secara default menampilkan data pada bulan berjalan.</li>
		<li>Catatan Ka.Adbang di bulan bejalan <b>TIDAK AKAN MUNCUL</b> jika data RFK bulan berjalan <b>BELUM PERNAH DIAKSES/DIBUKA</b> meski catatan sudah pernah diinput.</li>
		<li>Tombol <b>DOWNLOAD EXCEL</b> digunakan untuk mendownload tabel laporan RFK ke format excel.</li>
		<li>Tombol <b>AKSES RFK ALL OPD</b> digunakan untuk mengakses halaman RFK seluruh OPD sesuai dengan waktu yang ditentukan user.</li>
		<li>Pilihan <b>Bulan Realisasi</b> digunakan untuk menampilkan laporan RFK sesuai bulan yang dipilih.</li>
		<li>Tombol <b>SIMPAN CATATAN</b> digunakan untuk menyimpan catatan yang sudah diinput atau diedit oleh Kabag Adbang.</li>
		<li>Tombol <b>RESET CATATAN</b> digunakan untuk mengupdate catatan bulan berjalan sesuai dengan catatan di bulan sebelumnya.</li>
	</ul>
</div>

<script type="text/javascript">
	<?php if(empty($public)){ ?>
		run_download_excel();
	<?php } ?>
	var _url = window.location.href;
    var url = new URL(_url);
    var param = [];
    _url = url.origin+url.pathname+'?'+param.join('&');
    var date = new Date();
	var bulan = date.getMonth()+1;
    var nama_bulan = [
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
	var extend_action = ''
		+'<div style="margin-top: 20px;">'
			+'<label style="margin-left: 20px;">Bulan Realisasi: '
				+'<select id="pilih_bulan" style="padding: 5px;">'
					+'<option value="0">-- Bulan --</option>';
					nama_bulan.map(function(val, i){
						var index = i+1;
						if(index <= bulan){
							extend_action += '<option value="'+index+'">'+val+'</option>'
						}
					})
			extend_action += '</select>'
			+'</label>'
		+'</div>';
	var data_all_rfk = <?php echo json_encode($data_all['data']); ?>;
		
	jQuery(document).ready(function(){
			<?php if(empty($public)){ ?>
			jQuery('<a id="open_all_skpd" onclick="return false;" href="#" class="button button-primary" style="margin-left:5px">AKSES RFK ALL OPD</a>').insertAfter("#excel");
			jQuery('<a href="javascript:void(0)" style="margin-left: 5px; text-transform: uppercase" class="button button-primary" id="simpan-catatan-rfk-unit">Simpan Catatan</a>').insertAfter("#open_all_skpd");
			jQuery('<a href="javascript:void(0)" style="margin-left: 5px; text-transform: uppercase" class="button button-primary" id="reset-rfk-pemda">Reset Catatan</a>').insertAfter("#simpan-catatan-rfk-unit");
			<?php } ?>
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

					// console.log(data_all[no]+'&page_close=1');
					window.open(data_all[no]+'&page_close=1');
					no++;
				}, time*1000);
		    })

		    jQuery("#simpan-catatan-rfk-unit").on('click', function(){
	    		simpan_catatan_rfk('catatan_rfk_unit');
		    })

		    jQuery('#reset-rfk-pemda').on('click', function(){
		    	if(confirm('Apakah anda yakin untuk reset catatan Unit kerja sesuai bulan sebelumnya? Catatan unit kerja saat ini akan disamakan dengan bulan sebelumnya!')){
		    		jQuery('#wrap-loading').show();
		    		jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
			          	type: "post",
			          	data: {
			          		"action": "reset_rfk_pemda",
			          		"api_key": jQuery('#api_key').val(),
			          		"tahun_anggaran": <?php echo $input['tahun_anggaran'] ?>,
			          		"bulan": jQuery('#pilih_bulan').val(),
			          		"user": "<?php echo $current_user->display_name; ?>"
			          	},
			          	dataType: "json",
			          	success: function(data){
			    			jQuery('#wrap-loading').hide();
							alert(data.message);
							location.reload();
						},
						error: function(e) {
			    			jQuery('#wrap-loading').hide();
							console.log(e);
						}
					});
		    	}
		    });

		    jQuery('.catatan_rfk_unit').on('input', compareCatatan);
		    jQuery('.simpan-per-unit').on('click', function(){
		    	simpan_catatan_rfk_unit(this, 'catatan_rfk_unit');
		    })
		    jQuery('#table-rfk').DataTable();
	})

	function showsubunit(id_induk, bulan, tahun){
			var editable = 'false';
			let html = '';
			let nama_bulan = get_bulan(bulan);
			let modal_subunit = jQuery("#exampleModal");

			modal_subunit.find('.modal-title').html('');
			modal_subunit.find('.modal-body').html('');

				<?php if(empty($public)){ ?>
					html+=''
						+'<div style="margin-top:20px; margin-bottom:20px; text-align:center">'
							+'<a href="javascript:void(0)" style="margin-left: 5px; text-transform: uppercase" class="button button-primary" id="simpan-catatan-rfk-sub-unit">Simpan Catatan</a>'
						+'</div>';
					editable = true;
				<?php } ?>

				html+='<table id="table-rfk-sub-unit" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; table-layout:fixed; overflow-wrap: break-word; font-size: 70%; border: 0;">'
					    +'<thead>'
					    	+'<tr>'
					    		+'<th style="padding: 0; border: 0; width:125px"></th>'
					            +'<th style="padding: 0; border: 0"></th>'
					            +'<th style="padding: 0; border: 0; width:120px"></th>'
					            +'<th style="padding: 0; border: 0; width:120px"></th>'
					            +'<th style="padding: 0; border: 0; width:120px"></th>'
					            +'<th style="padding: 0; border: 0; width:110px"></th>'
					            +'<th style="padding: 0; border: 0; width:75px"></th>'
					            +'<th style="padding: 0; border: 0; width:110px"></th>'
					            +'<th style="padding: 0; border: 0; width:90px"></th>'
					            +'<th style="padding: 0; border: 0; width:80px"></th>'
					            +'<th style="padding: 0; border: 0; width:100px"></th>'
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
						        +'<th class="atas kanan bawah text_tengah text_blok">Catatan Ka.Adbang</th>'
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
						        +'<th class="atas kanan bawah text_tengah text_blok">11</th>'
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
										+'<tr data-idskpd="'+data_sub_unit.id_skpd+'">'
											+ '<td class="atas kanan bawah kiri text_tengah">'+data_sub_unit.kode_skpd+'</td>'
											+ '<td class="atas kanan bawah text_kiri" data-search="'+data_sub_unit.nama_skpd+'">'+link_sub_unit+'</td>'
											+ '<td class="kanan bawah text_kanan" data-order="'+data_sub_unit.rka_sipd+'">'+formatRupiah(data_sub_unit.rka_sipd)+'</td>'
											+ '<td class="kanan bawah text_kanan" data-order="'+data_sub_unit.dpa_sipd+'">'+formatRupiah(data_sub_unit.dpa_sipd)+'</td>'
											+ '<td class="kanan bawah text_kanan" data-order="'+data_sub_unit.realisasi_keuangan+'">'+formatRupiah(data_sub_unit.realisasi_keuangan)+'</td>'
											+ '<td class="kanan bawah text_tengah">'+data_sub_unit.capaian+'</td>'
											+ '<td class="kanan bawah text_tengah">'+data_sub_unit.target_rak+'</td>'
											+ '<td class="kanan bawah text_tengah">'+data_sub_unit.deviasi+'</td>'
											+ '<td class="kanan bawah text_tengah">'+data_sub_unit.realisasi_fisik+'</td>'
											+ '<td class="kanan bawah text_tengah">'+data_sub_unit.last_update+' '+'</br></br><span class="badge badge-danger simpan-per-unit hide-excel">SIMPAN</span>'+'</td>'
											+ '<td class="kanan bawah text_tengah catatan_rfk_sub_unit" contenteditable="true" data-content="'+data_sub_unit.cat_ka_adbang+'">'+data_sub_unit.cat_ka_adbang+'</td>'
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
										+'<th style="border:1px solid"></th>'
									+'</tr>'
								+'</tfoot>'
						 +'</table>';

			modal_subunit.find('.modal-title').html('REALISASI FISIK DAN KEUANGAN (RFK) <br> ' + data_all_rfk[index].nama_skpd + '<br>' + ' Bulan ' + nama_bulan + ' Tahun ' + tahun);
			modal_subunit.find('.modal-body').html(html);
			modal_subunit.modal('show');

			jQuery("#simpan-catatan-rfk-sub-unit").on('click', function(){
	    		simpan_catatan_rfk('catatan_rfk_sub_unit');
		    })
		    jQuery('.catatan_rfk_sub_unit').on('input', compareCatatan);
		    jQuery(".simpan-per-unit").on('click', function(){
		    	simpan_catatan_rfk_unit(this, 'catatan_rfk_sub_unit');
		    })
			jQuery('#table-rfk-sub-unit').DataTable();
	}

	function simpan_catatan_rfk(tag){
			if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
	    			jQuery('#wrap-loading').show();
	    			
	    			var arr_catatan_rfk_unit = [];
	    			jQuery("."+tag).map(function(i,r){
	    				var tr = jQuery(r).closest('tr');
	    				var val = jQuery(r).text();

	    				arr_catatan_rfk_unit.push({
	    					catatan_ka_adbang : val,
	    					id_skpd : tr.attr("data-idskpd")
	    				})
	    			})

	    			arr_catatan_rfk_unit.reduce(function(sequence, nextData){
		                return sequence.then(function(current_data){
		            		return new Promise(function(resolve_redurce, reject_redurce){
					    		jQuery.ajax({
									url: "<?php echo admin_url('admin-ajax.php'); ?>",
						          	type: "post",
						          	data: {
						          		"action": "simpan_catatan_rfk_unit",
						          		"api_key": jQuery('#api_key').val(),
						          		"tahun_anggaran": <?php echo $input['tahun_anggaran'] ?>,
						          		"bulan": jQuery('#pilih_bulan').val(),
						          		"user": "<?php echo $current_user->display_name; ?>",
						          		"data": current_data
						          	},
						          	dataType: "json",
						          	success: function(data){
										return resolve_redurce(nextData);
									},
									error: function(e) {
										console.log(e);
										return resolve_redurce(nextData);
									}
								});
			                })
		                    .catch(function(e){
		                        console.log(e);
		                        return Promise.resolve(nextData);
		                    });
		                })
		                .catch(function(e){
		                    console.log(e);
		                    return Promise.resolve(nextData);
		                });
		            }, Promise.resolve(arr_catatan_rfk_unit[arr_catatan_rfk_unit.length-1]))
		            .then(function(){
						jQuery('#wrap-loading').hide();
						alert('Data berhasil disimpan!');
		            })
		            .catch(function(e){
		                console.log(e);
		            });
	    		}
	}


	function simpan_catatan_rfk_unit(that, tag)
	{
			jQuery('#wrap-loading').show();
			var tr = jQuery(that).closest('tr');
			var val = tr.find('.'+tag).text();
			var id_skpd = tr.attr("data-idskpd");

			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type: "post",
				data: {
						"action": "simpan_catatan_rfk_unit",
						"api_key": jQuery('#api_key').val(),
						"tahun_anggaran": <?php echo $input['tahun_anggaran'] ?>,
						"bulan": jQuery('#pilih_bulan').val(),
						"user": "<?php echo $current_user->display_name; ?>",
						"data": {
							"id_skpd": id_skpd,
							"catatan_ka_adbang": val,
						}
					},
					dataType: "json",
					success: function(data){
						alert(data.message);
						tr.find('.'+tag).attr('data-content',val);
		    			tr.removeClass('odd ubah-warna');
						tr.find('.simpan-per-unit').hide();
						jQuery('#wrap-loading').hide();
					},
					error: function(e) {
						alert(e.message);
					}
			});
	}

	function compareCatatan(){
		    var tr = jQuery(this).closest('tr');
		    var val = jQuery(this).text();
		    var catatan = jQuery(this).attr('data-content');
		    	
		    if(val != catatan){
		    	tr.find('.simpan-per-unit').show();
		    	tr.addClass('odd ubah-warna');
		    }else{
		    	tr.find('.simpan-per-unit').hide();
		    	tr.removeClass('odd ubah-warna');
		    }
	}

	function get_bulan(bln) {
		if(!bln || bln == '' || bln <= 0){
			bln = date.getMonth();
		}
		return nama_bulan[parseInt(bln-1)];
	}
</script>
