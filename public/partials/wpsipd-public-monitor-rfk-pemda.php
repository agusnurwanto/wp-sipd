<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.25/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.25/datatables.min.js"></script>

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
		<table id="table-rfk" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; table-layout:fixed; overflow-wrap: break-word; font-size: 80%; border: 0;">
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
		            <th style="padding: 0; border: 0; width:100px"></th>
		            <th style="padding: 0; border: 0; width:90px"></th>
		    	</tr>
		    	<tr>
			    	<td class="atas kanan bawah kiri text_tengah text_blok" colspan="5">Kode</td>
			        <td class="atas kanan bawah text_tengah text_blok" style="padding: 0; width:140px">Nama SKPD</td>
			        <td class="atas kanan bawah text_tengah text_blok">RKA SIPD (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">DPA SIMDA (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">Realisasi Keuangan (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">Capaian ( % )</td>
			        <td class="atas kanan bawah text_tengah text_blok">RAK SIMDA (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">Realisasi Fisik ( % )</td>
			        <td class="atas kanan bawah text_tengah text_blok">Update Terakhir</td>
			    </tr>
		    </thead>
		    <tbody>';

		    $units = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd, is_skpd from data_unit where active=1 and tahun_anggaran=".$input['tahun_anggaran'].' and is_skpd=1 order by nama_skpd ASC', ARRAY_A);

			$current_user = wp_get_current_user();
			$data_all = array(
				'data' => array(),
				'total_rka_sipd' => 0,
				'total_dpa_sipd' => 0,
				'total_realisasi_keuangan' => 0,
				'capaian' => array(),
				'total_rak_simda' => 0,
				'realisasi_fisik' => array()
			);
		    foreach($units as $unit){

		    	$sub_units = $wpdb->get_results("SELECT id_skpd, idinduk, kode_skpd, nama_skpd from data_unit where active=1 and tahun_anggaran=".$input['tahun_anggaran']." and idinduk=".$unit['id_skpd']." order by nama_skpd ASC", ARRAY_A);

		    	if(count($sub_units) == 1){
		    		$data_rfk = $wpdb->get_results($wpdb->prepare("
							select 
								sum(k.pagu) pagu, 
								sum(k.pagu_simda) pagu_simda, 
								sum(d.realisasi_anggaran) realisasi_keuangan,
								IFNULL((sum(d.realisasi_anggaran)/sum(k.pagu_simda)*100),0) capaian, 
								avg(IFNULL(d.realisasi_fisik,0)) realisasi_fisik, 
								sum(d.rak) rak
							from data_sub_keg_bl k 
							left join data_rfk d 
								on d.id_skpd=k.id_sub_skpd and 
								d.kode_sbl=k.kode_sbl and 
								d.tahun_anggaran=k.tahun_anggaran 
							where 
								k.tahun_anggaran=%d and 
								k.id_sub_skpd=%d and 
								k.active=1 and 
								bulan=%d
							", 
								$input['tahun_anggaran'],
								$unit['id_skpd'],
								$bulan
					), ARRAY_A);

					foreach ($data_rfk as $key => $rfk) {
						$latest_update = $this->get_date_rfk_update(array('id_skpd'=>$unit['id_skpd'], 'tahun_anggaran' => $input['tahun_anggaran']));
						$data_all['data'][] = array(
			    			'kode_skpd' => $unit['kode_skpd'],
			    			'nama_skpd' => $unit['nama_skpd'],
			    			'rka_sipd' => $rfk['pagu'],
			    			'dpa_sipd' => $rfk['pagu_simda'],
			    			'realisasi_keuangan' => $rfk['realisasi_keuangan'],
			    			'capaian' => $rfk['capaian'],
			    			'rak' => $rfk['rak'],
			    			'realisasi_fisik' => $rfk['realisasi_fisik'],
			    			'last_update' => $latest_update,
			    		);

			    		$data_all['total_rka_sipd']+=$rfk['pagu'];
			    		$data_all['total_dpa_sipd']+=$rfk['pagu_simda'];
			    		$data_all['total_realisasi_keuangan']+=$rfk['realisasi_keuangan'];
			    		$data_all['capaian'][]=$rfk['capaian'];
			    		$data_all['total_rak_simda']+=$rfk['rak'];
			    		$data_all['realisasi_fisik'][]=$rfk['realisasi_fisik'];
					}

		    	}elseif(count($sub_units) > 1){
		    	
		    		$pagu_sub_unit=0;
		    		$pagu_simda_sub_unit=0;
		    		$realisasi_anggaran_sub_unit=0;
		    		$rak_sub_unit=0;
		    		$capaian_sub_unit=array();
		    		$realisasi_fisik_sub_unit=array();
		    		
		    		foreach ($sub_units as $key => $sub_unit) {
		    			
			    		$data_rfk = $wpdb->get_results($wpdb->prepare("
								select 
									sum(k.pagu) pagu, 
									sum(k.pagu_simda) pagu_simda, 
									sum(d.realisasi_anggaran) realisasi_keuangan,
									IFNULL((sum(d.realisasi_anggaran)/sum(k.pagu_simda)*100),0) capaian, 
									avg(IFNULL(d.realisasi_fisik,0)) realisasi_fisik, 
									sum(d.rak) rak
								from data_sub_keg_bl k 
								left join data_rfk d 
									on d.id_skpd=k.id_sub_skpd and 
									d.kode_sbl=k.kode_sbl and 
									d.tahun_anggaran=k.tahun_anggaran 
								where 
									k.tahun_anggaran=%d and 
									k.id_sub_skpd=%d and 
									k.active=1 and 
									bulan=%d
								", 
									$input['tahun_anggaran'],
									$sub_unit['id_skpd'],
									$bulan
						), ARRAY_A);

						foreach ($data_rfk as $key => $rfk) {
							$pagu_sub_unit+=$rfk['pagu'];
							$pagu_simda_sub_unit+=$rfk['pagu_simda'];
							$realisasi_anggaran_sub_unit+=$rfk['realisasi_keuangan'];
							$rak_sub_unit+=$rfk['rak'];
							$capaian_sub_unit[]=$rfk['capaian'];
							$realisasi_fisik_sub_unit[]=$rfk['realisasi_fisik'];

							$data_all['total_rka_sipd']+=$rfk['pagu'];
				    		$data_all['total_dpa_sipd']+=$rfk['pagu_simda'];
				    		$data_all['total_realisasi_keuangan']+=$rfk['realisasi_keuangan'];
				    		$data_all['total_rak_simda']+=$rfk['rak'];
						}
		    		}

		    		$latest_update = $this->get_date_rfk_update(array('id_skpd'=>$unit['id_skpd'], 'tahun_anggaran' => $input['tahun_anggaran']));
		    		$data_all['data'][] = array(
			    			'kode_skpd' => $unit['kode_skpd'],
			    			'nama_skpd' => $unit['nama_skpd'],
			    			'rka_sipd' => $pagu_sub_unit,
			    			'dpa_sipd' => $pagu_simda_sub_unit,
			    			'realisasi_keuangan' => $realisasi_anggaran_sub_unit,
			    			'capaian' => !empty($pagu_simda_sub_unit) ? ($realisasi_anggaran_sub_unit/$pagu_simda_sub_unit)*100 : 0,
			    			'rak' => $rak_sub_unit,
			    			'realisasi_fisik' => !empty($realisasi_fisik_sub_unit) ? array_sum($realisasi_fisik_sub_unit)/count($realisasi_fisik_sub_unit) : 0,
			    			'last_update' => $latest_update
			    	);
		    	}
			}

	foreach ($data_all['data'] as $key => $value) {
		$nama_page = 'RFK '.$value['nama_skpd'].' '.$value['kode_skpd'].' | '.$input['tahun_anggaran'];
		$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
		
		$body.='
		    	<tr>
				    <td class="atas kanan bawah kiri text_tengah" colspan="5">'.$value['kode_skpd'].'</td>
				    <td class="atas kanan bawah text_kiri"><a href="'.get_permalink($custom_post) . '?key=' . $this->gen_key().'" target="_blank">'.$value['nama_skpd'].'</a></td>
				    <td class="atas kanan bawah text_kanan">'.number_format($value['rka_sipd'],0,",",".").'</td>
				    <td class="atas kanan bawah text_kanan">'.number_format($value['dpa_sipd'],0,",",".").'</td>
				    <td class="atas kanan bawah text_kanan">'.number_format($value['realisasi_keuangan'],0,",",".").'</td>
				    <td class="atas kanan bawah text_tengah">'.$this->pembulatan($value['capaian']).'</td>
				    <td class="atas kanan bawah text_kanan">'.number_format($value['rak'],0,",",".").'</td>
				    <td class="atas kanan bawah text_tengah" style="width: 10px; overflow: hidden;">'.$this->pembulatan($value['realisasi_fisik']).'</td>
				    <td class="atas kanan bawah text_tengah">'.$value['last_update'].'</td>
				</tr>
		';
	}

		$body.='
				<tr>
			        <td class="kiri kanan bawah text_blok text_kanan" colspan="6">TOTAL</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($data_all['total_rka_sipd'],0,",",".").'</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($data_all['total_dpa_sipd'],0,",",".").'</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($data_all['total_realisasi_keuangan'],0,",",".").'</td>
			        <td class="kanan bawah text_tengah text_blok">'.$this->pembulatan(array_sum($data_all['capaian'])/count($data_all['capaian'])).'</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($data_all['total_rak_simda'],0,",",".").'</td>
			        <td class="kanan bawah text_blok total-realisasi-fisik text_tengah">'.$this->pembulatan(array_sum($data_all['realisasi_fisik'])/count($data_all['realisasi_fisik'])).'</td>
				    <td class="atas kanan bawah text_tengah"></td>
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

		    jQuery('#table-rfk').DataTable();
		})
</script>
