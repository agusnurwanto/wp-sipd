<?php
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

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
	<div id="cetak" title="Laporan RFK" style="padding: 5px;">
		<h4 style="text-align: center; margin: 0; font-weight: bold;">Realisasi Fisik dan Keuangan (RFK)<br>'.$nama_pemda.'<br>Bulan '.$nama_bulan.' Tahun '.$input['tahun_anggaran'].'</h4>
		<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; table-layout:fixed; overflow-wrap: break-word; font-size: 70%; border: 0;">
		    <thead>
		    	<tr>
		    		<th style="padding: 0; border: 0; width:30px"></th>
		            <th style="padding: 0; border: 0; width:30px"></th>
		            <th style="padding: 0; border: 0; width:30px"></th>
		            <th style="padding: 0; border: 0; width:40px"></th>
		            <th style="padding: 0; border: 0; width:30px"></th>
		            <th style="padding: 0; border: 0"></th>
		            <th style="padding: 0; border: 0; width:140px"></th>
		            <th style="padding: 0; border: 0; width:140px"></th>
		            <th style="padding: 0; border: 0; width:140px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		    	</tr>
		    	<tr>
			    	<td class="atas kanan bawah kiri text_tengah text_blok" colspan="5">Kode</td>
			        <td class="atas kanan bawah text_tengah text_blok">Nama SKPD</td>
			        <td class="atas kanan bawah text_tengah text_blok">RKA SIPD (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">DPA SIMDA (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">Realisasi Keuangan (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">Capaian ( % )</td>
			        <td class="atas kanan bawah text_tengah text_blok">RAK SIMDA (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">Realisasi Fisik ( % )</td>
			    </tr>
		    </thead>
		    <tbody>';

		    $units = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd, is_skpd from data_unit where active=1 and tahun_anggaran=".$input['tahun_anggaran'].' and is_skpd=1 order by nama_skpd ASC', ARRAY_A);

		    $total_pagu_pemkab = 0;
			$total_simda_pemkab = 0;
			$realisasi_pemkab = 0;
			$total_rak_simda_pemkab = 0;
			$capaian_pemkab = 0;
			$realisasi_fisik_pemkab = 0;
			$realisasi_fisik_avg_pemkab = array();
			$current_user = wp_get_current_user();

			$data_all = array();
		    foreach($units as $unit){

		    	$sub_units = $wpdb->get_results("SELECT id_skpd, idinduk, kode_skpd, nama_skpd from data_unit where active=1 and tahun_anggaran=".$input['tahun_anggaran']." and idinduk=".$unit['id_skpd']." order by nama_skpd ASC", ARRAY_A);

		    	if(count($sub_units) == 1){
		    		
		    		$total_pagu_unit = 0;
		    		$total_simda_unit = 0;
		    		$realisasi_unit = 0;
		    		$total_rak_simda_unit = 0;
		    		$realisasi_fisik_unit = array();
		    		$capaian = 0;
		    		$realisasi_fisik_avg = 0;

		    		$subkegs = $wpdb->get_results($wpdb->prepare("
						select 
							k.*
						from data_sub_keg_bl k
						where k.tahun_anggaran=%d
							and k.active=1
							and k.id_skpd=%d
							and k.id_sub_skpd=%d
						order by k.kode_sub_giat ASC
					", $input['tahun_anggaran'], $unit['id_skpd'], $unit['id_skpd']), ARRAY_A);

					foreach ($subkegs as $key => $subkeg) {
						$data_rfk = $wpdb->get_results($wpdb->prepare("
							    select 
							        *
							    from data_rfk
							    where tahun_anggaran=%d
							        and bulan=%d
							        and id_skpd=%d
							        and kode_sbl=%s
							", $input['tahun_anggaran'], $bulan, $subkeg['id_skpd'], $subkeg['kode_sbl']), ARRAY_A);
						
						if(isset($data_rfk)){
							foreach($data_rfk as $key => $rfk){
								$realisasi_unit += isset($rfk['realisasi_anggaran']) ? $rfk['realisasi_anggaran'] : 0;
								$total_rak_simda_unit += isset($rfk['rak']) ? $rfk['rak'] : 0;
								$realisasi_fisik_unit[] = isset($rfk['realisasi_fisik']) ? $rfk['realisasi_fisik'] : 0;
							}
						}

						$total_pagu_unit += $subkeg['pagu'];
						$total_simda_unit += $subkeg['pagu_simda'];
					}

					if($total_simda_unit != 0){
						$capaian = $this->pembulatan($realisasi_unit/$total_simda_unit*100);
					}

					if(array_sum($realisasi_fisik_unit) != 0){
						$realisasi_fisik_avg = $this->pembulatan(array_sum($realisasi_fisik_unit)/count($realisasi_fisik_unit));
						$realisasi_fisik_avg_pemkab[] = $realisasi_fisik_avg;
					}else{
						$realisasi_fisik_avg_pemkab[] = $realisasi_fisik_avg;
					}

					$nama_page = 'RFK '.$unit['nama_skpd'].' '.$unit['kode_skpd'].' | '.$input['tahun_anggaran'];
					$custom_post = get_page_by_title($nama_page, OBJECT, 'page');

					$body.='
		    		<tr>
				    	<td class="atas kanan bawah kiri text_tengah" colspan="5">'.$unit['kode_skpd'].'</td>
				        <td class="atas kanan bawah text_kiri"><a href="'.get_permalink($custom_post) . '?key=' . $this->gen_key().'" target="_blank">'.$unit['nama_skpd'].'</a></td>
				        <td class="atas kanan bawah text_kanan">'.number_format($total_pagu_unit,0,",",".").'</td>
				        <td class="atas kanan bawah text_kanan">'.number_format($total_simda_unit,0,",",".").'</td>
				        <td class="atas kanan bawah text_kanan">'.number_format($realisasi_unit,0,",",".").'</td>
				        <td class="atas kanan bawah text_tengah">'.$capaian.'</td>
				        <td class="atas kanan bawah text_tengah">'.number_format($total_rak_simda_unit,0,",",".").'</td>
				        <td class="atas kanan bawah text_tengah">'.$realisasi_fisik_avg.'</td>
				    </tr>
			    	';

			    	$total_pagu_pemkab += $total_pagu_unit;
				    $total_simda_pemkab += $total_simda_unit;
				    $realisasi_pemkab += $realisasi_unit;
				    $total_rak_simda_pemkab += $total_rak_simda_unit;

		    	}elseif(count($sub_units) > 1){

		    		$total_pagu_unit = 0;
			    	$total_simda_unit = 0;
			    	$realisasi_unit = 0;
			    	$total_rak_simda_unit = 0;			    	
		    		$realisasi_fisik_unit = array();
		    		$capaian = 0;
		    		$realisasi_fisik_avg = 0;

		    		foreach ($sub_units as $key => $sub_unit) {
		    			
		    			$subkegs = $wpdb->get_results($wpdb->prepare("
							select 
								k.*
							from data_sub_keg_bl k
							where k.tahun_anggaran=%d
								and k.active=1
								and k.id_sub_skpd=%d
							order by k.kode_sub_giat ASC
							", $input['tahun_anggaran'], $sub_unit['id_skpd']), ARRAY_A);

						foreach ($subkegs as $key => $subkeg) {
								$data_rfk = $wpdb->get_results($wpdb->prepare("
									    select 
									        *
									    from data_rfk
									    where tahun_anggaran=%d
									        and bulan=%d
									        and id_skpd=%d
									        and kode_sbl=%s
									", $input['tahun_anggaran'], $bulan, $subkeg['id_skpd'], $subkeg['kode_sbl']), ARRAY_A);
								
								$realisasi_fisik_sub_unit=array();
								if(isset($data_rfk)){
									foreach($data_rfk as $key => $rfk){
										$total_rak_simda_unit += isset($rfk['rak']) ? $rfk['rak'] : 0; 
										$realisasi_unit += isset($rfk['realisasi_anggaran']) ? $rfk['realisasi_anggaran'] : 0;
										$realisasi_fisik_sub_unit[] = isset($rfk['realisasi_fisik']) ? $rfk['realisasi_fisik'] : 0;
									}
								}


								$total_pagu_unit += $subkeg['pagu'];
								$total_simda_unit += $subkeg['pagu_simda'];
						}

						if($total_simda_unit != 0){
							$capaian = $this->pembulatan($realisasi_unit/$total_simda_unit*100);
						}

						if(array_sum($realisasi_fisik_sub_unit) != 0){
							$realisasi_fisik_unit[] = $this->pembulatan(array_sum($realisasi_fisik_sub_unit)/count($realisasi_fisik_sub_unit));
						}

						$nama_page = 'RFK '.$unit['nama_skpd'].' '.$unit['kode_skpd'].' | '.$input['tahun_anggaran'];
						$custom_post = get_page_by_title($nama_page, OBJECT, 'page');

		    		}

		    		if(array_sum($realisasi_fisik_unit) != 0){
						$realisasi_fisik_avg = $this->pembulatan(array_sum($realisasi_fisik_unit)/count($realisasi_fisik_unit));
						$realisasi_fisik_avg_pemkab[] = $realisasi_fisik_avg;
					}else{
						$realisasi_fisik_avg_pemkab[] = $realisasi_fisik_avg;
					}

		    		$body.='
			    		<tr>
					    	<td class="atas kanan bawah kiri text_tengah" colspan="5">'.$unit['kode_skpd'].'</td>
					        <td class="atas kanan bawah text_kiri"><a href="'.get_permalink($custom_post) . '?key=' . $this->gen_key().'" target="_blank">'.$unit['nama_skpd'].'</a></td>
					        <td class="atas kanan bawah text_kanan">'.number_format($total_pagu_unit,0,",",".").'</td>
					        <td class="atas kanan bawah text_kanan">'.number_format($total_simda_unit,0,",",".").'</td>
					        <td class="atas kanan bawah text_kanan">'.number_format($realisasi_unit,0,",",".").'</td>
					        <td class="atas kanan bawah text_tengah">'.$capaian.'</td>
					        <td class="atas kanan bawah text_tengah">'.number_format($total_rak_simda_unit,0,",",".").'</td>
					        <td class="atas kanan bawah text_tengah">'.$realisasi_fisik_avg.'</td>
					    </tr>
				    	';

				    $total_pagu_pemkab += $total_pagu_unit;
				    $total_simda_pemkab += $total_simda_unit;
				    $realisasi_pemkab += $realisasi_unit;
				    $total_rak_simda_pemkab += $total_rak_simda_unit;
		    	}
		    }

		    if($total_simda_pemkab != 0){
		    	$capaian_pemkab = $this->pembulatan($realisasi_pemkab/$total_simda_pemkab*100);	
		    }  

		    if(array_sum($realisasi_fisik_avg_pemkab) != 0){
				$realisasi_fisik_pemkab = $this->pembulatan(array_sum($realisasi_fisik_avg_pemkab)/count($realisasi_fisik_avg_pemkab));
			} 

		$body.='
				<tr>
			        <td class="kiri kanan bawah text_blok text_kanan" colspan="6">TOTAL</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($total_pagu_pemkab,0,",",".").'</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($total_simda_pemkab,0,",",".").'</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($realisasi_pemkab,0,",",".").'</td>
			        <td class="kanan bawah text_tengah text_blok">'.$capaian_pemkab.'</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($total_rak_simda_pemkab,0,",",".").'</td>
			        <td class="kanan bawah text_blok total-realisasi-fisik text_tengah">'.$realisasi_fisik_pemkab.'</td>
			    </tr>
		    </tbody>
		</table>
	</div>';
	
	echo $body;
?>

<script type="text/javascript">
	run_download_excel();
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
		jQuery(document).ready(function(){
			jQuery('#action-sipd').append(extend_action);
			jQuery('#pilih_bulan').val(+<?php echo $bulan; ?>);
			jQuery('#pilih_bulan').on('change', function(){
	    	var val = +jQuery(this).val();
	    	if(val > 0){
	    		window.open(_url+'&bulan='+val,'_blank');
	    	}
	    	jQuery('#pilih_bulan').val(+<?php echo $bulan; ?>);
	    });
		})
</script>
