<?php
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

$type = '';
if(!empty($_GET) && !empty($_GET['type'])){
    $type = $_GET['type'];
}

$bulan = date('m');
if(!empty($_GET) && !empty($_GET['bulan'])){
    $bulan = $_GET['bulan'];
}
$nama_bulan = $this->get_bulan($bulan);

if(!empty($input['id_skpd'])){
	$sql = $wpdb->prepare("
		select 
			* 
		from data_unit 
		where tahun_anggaran=%d
			and (
				id_skpd IN (".$input['id_skpd'].") 
				or idinduk IN(".$input['id_skpd'].")
			)
			and active=1
		order by id_skpd ASC
	", $input['tahun_anggaran']);
}else{
	$sql = $wpdb->prepare("
		select 
			* 
		from data_unit 
		where tahun_anggaran=%d
			and active=1
		order by id_skpd ASC
	", $input['tahun_anggaran']);
}
$units = $wpdb->get_results($sql, ARRAY_A);
if(empty($units)){
	die('<h1>SKPD tidak ditemukan!</h1>');
}else{
	$pengaturan = $wpdb->get_results($wpdb->prepare("
		select 
			* 
		from data_pengaturan_sipd 
		where tahun_anggaran=%d
	", $input['tahun_anggaran']), ARRAY_A);

	$start_rpjmd = 2018;
	if(!empty($pengaturan)){
		$start_rpjmd = $pengaturan[0]['awal_rpjmd'];
	}
	$urut = $input['tahun_anggaran']-$start_rpjmd;
}
foreach ($units as $k => $unit): 
	if($unit['is_skpd']==1){
		$unit_induk = array($unit);
		$subkeg = $wpdb->get_results($wpdb->prepare("
			select 
				* 
			from data_sub_keg_bl 
			where tahun_anggaran=%d
				and active=1
				and id_skpd=%d
				and id_sub_skpd=%d
			order by kode_sub_giat ASC
		", $input['tahun_anggaran'], $unit['id_skpd'], $unit['id_skpd']), ARRAY_A);
	}else{
		$unit_induk = $wpdb->get_results($wpdb->prepare("
			select 
				* 
			from data_unit 
			where tahun_anggaran=%d
				and active=1
				and id_skpd=%d
			order by id_skpd ASC
		", $input['tahun_anggaran'], $unit['idinduk']), ARRAY_A);

		$subkeg = $wpdb->get_results($wpdb->prepare("
			select 
				* 
			from data_sub_keg_bl 
			where tahun_anggaran=%d
				and active=1
				and id_sub_skpd=%d
			order by kode_sub_giat ASC
		", $input['tahun_anggaran'], $unit['id_skpd']), ARRAY_A);
	}
	// echo $wpdb->last_query.'<br>';

	$data_all = array(
		'total' => 0,
		'data' => array()
	);
	foreach ($subkeg as $kk => $sub) {
		if(empty($data_all['data'][$sub['kode_urusan']])){
			$data_all['data'][$sub['kode_urusan']] = array(
				'nama'	=> $sub['nama_urusan'],
				'total' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])){
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
				'nama'	=> $sub['nama_bidang_urusan'],
				'total' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']])){
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']] = array(
				'nama'	=> $sub['nama_program'],
				'total' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']])){
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']] = array(
				'nama'	=> $sub['nama_giat'],
				'total' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']])){
			$nama = explode(' ', $sub['nama_sub_giat']);
			unset($nama[0]);
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']] = array(
				'nama'	=> implode(' ', $nama),
				'total' => 0,
				'data'	=> $sub
			);
		}
		$data_all['total'] += $sub['pagu'];
		$data_all['data'][$sub['kode_urusan']]['total'] += $sub['pagu'];
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total'] += $sub['pagu'];
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total'] += $sub['pagu'];
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total'] += $sub['pagu'];
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total'] += $sub['pagu'];
	}

	// print_r($data_all); die();
	$body = '';
	$body_rkpd = '';
	foreach ($data_all['data'] as $kd_urusan => $urusan) {
		$body .= '
			<tr>
		        <td class="text_center kiri kanan bawah text_blok">'.$kd_urusan.'</td>
		        <td class="text_center kanan bawah">&nbsp;</td>
		        <td class="text_center kanan bawah">&nbsp;</td>
		        <td class="text_center kanan bawah">&nbsp;</td>
		        <td class="text_center kanan bawah">&nbsp;</td>
		        <td class="kanan bawah text_blok" colspan="7">'.$urusan['nama'].'</td>
		    </tr>
		';
		foreach ($urusan['data'] as $kd_bidang => $bidang) {
			$kd_bidang = explode('.', $kd_bidang);
			$kd_bidang = $kd_bidang[count($kd_bidang)-1];
			$body .= '
				<tr>
		            <td class="text_center kiri kanan bawah text_blok">'.$kd_urusan.'</td>
		            <td class="text_center kanan bawah text_blok">'.$kd_bidang.'</td>
		            <td class="text_center kanan bawah">&nbsp;</td>
		            <td class="text_center kanan bawah">&nbsp;</td>
		            <td class="text_center kanan bawah">&nbsp;</td>
		            <td class="kanan bawah text_blok">'.$bidang['nama'].'</td>
		            <td class="kanan bawah text_kanan text_blok">'.number_format($bidang['total'],0,",",".").'</td>
		            <td class="kanan bawah text_blok"></td>
		            <td class="kanan bawah text_blok"></td>
		            <td class="kanan bawah text_blok"></td>
		        	<td class="kanan bawah text_kanan text_blok" colspan="2"></td>
		        </tr>
			';
			foreach ($bidang['data'] as $kd_program => $program) {
				$kd_program = explode('.', $kd_program);
				$kd_program = $kd_program[count($kd_program)-1];
				$body .= '
					<tr>
			            <td class="text_center kiri kanan bawah text_blok">'.$kd_urusan.'</td>
			            <td class="text_center kanan bawah text_blok">'.$kd_bidang.'</td>
			            <td class="text_center kanan bawah text_blok">'.$kd_program.'</td>
			            <td class="text_center kanan bawah">&nbsp;</td>
			            <td class="text_center kanan bawah">&nbsp;</td>
			            <td class="kanan bawah text_blok">'.$program['nama'].'</td>
			            <td class="kanan bawah text_kanan text_blok">'.number_format($program['total'],0,",",".").'</td>
			            <td class="kanan bawah text_blok"></td>
			            <td class="kanan bawah text_blok"></td>
			            <td class="kanan bawah text_blok"></td>
		        		<td class="kanan bawah text_kanan text_blok" colspan="2"></td>
			        </tr>
				';
				foreach ($program['data'] as $kd_giat => $giat) {
					$kd_giat = explode('.', $kd_giat);
					$kd_giat = $kd_giat[count($kd_giat)-2].'.'.$kd_giat[count($kd_giat)-1];
					$body .= '
				        <tr>
				            <td class="text_center" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">'.$kd_urusan.'</td>
				            <td class="text_center" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:00;" width="5">'.$kd_bidang.'</td>
				            <td class="text_center" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:000;" width="5">'.$kd_program.'</td>
				            <td class="text_center" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">'.$kd_giat.'</td>
				            <td class="text_center" style="border:.5pt solid #000; vertical-align:middle;" width="5">&nbsp;</td>
				            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;">'.$giat['nama'].'</td>
				            <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">'.number_format($giat['total'],0,",",".").'</td>
				            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;"></td>
				            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;"></td>
				            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;"></td>
		        			<td class="kanan bawah text_kanan text_blok" colspan="2"></td>
				        </tr>
					';
					foreach ($giat['data'] as $kd_sub_giat => $sub_giat) {
						$kd_sub_giat = explode('.', $kd_sub_giat);
						$kd_sub_giat = $kd_sub_giat[count($kd_sub_giat)-1];
						$body .= '
					        <tr>
					            <td class="kiri kanan bawah">'.$kd_urusan.'</td>
					            <td class="kanan bawah">'.$kd_bidang.'</td>
					            <td class="kanan bawah">'.$kd_program.'</td>
					            <td class="kanan bawah">'.$kd_giat.'</td>
					            <td class="kanan bawah">'.$kd_sub_giat.'</td>
					            <td class="kanan bawah">'.$sub_giat['nama'].'</td>
					            <td class="kanan bawah text_kanan">'.number_format($sub_giat['total'],0,",",".").'</td>
					            <td class="kanan bawah"></td>
					            <td class="kanan bawah"></td>
					            <td class="kanan bawah"></td>
					            <td class="kanan bawah"></td>
					            <td class="kanan bawah"></td>
					        </tr>
						';
					}
				}
			}
		}
	}
	echo '
	<div id="cetak" title="Laporan RFK '.$input['tahun_anggaran'].'" style="padding: 5px;">
		<h4 style="text-align: center; margin: 0; font-weight: bold;">Realisasi Fisik dan Keuangan (RFK)<br>'.$unit['kode_skpd'].'&nbsp;'.$unit['nama_skpd'].'<br>Bulan '.$nama_bulan.' Tahun '.$input['tahun_anggaran'].'</h4>
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
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:200px"></th>
		        </tr>
			    <tr>
			        <td colspan="12" style="vertical-align:middle; font-weight:bold; border: 0; font-size: 13px;">
			            Unit Organisasi : '.$unit_induk[0]['kode_skpd'].'&nbsp;'.$unit_induk[0]['nama_skpd'].'<br/>
			            Sub Unit Organisasi : '.$unit['kode_skpd'].'&nbsp;'.$unit['nama_skpd'].'
			        </td>
			    </tr>
			    <tr>
			        <td class="atas kanan bawah kiri text_tengah text_blok" colspan="5">Kode</td>
			        <td class="atas kanan bawah text_tengah text_blok">Urusan/ Bidang Urusan Pemerintahan Daerah Dan Program/ Kegiatan</td>
			        <td class="atas kanan bawah text_tengah text_blok">Pagu Indikatif (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">Realisasi Keuangan (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">Capaian ( % )</td>
			        <td class="atas kanan bawah text_tengah text_blok">Realisasi Fisik ( % )</td>
			        <td class="atas kanan bawah text_tengah text_blok">Sumber Dana</td>
			        <td class="atas kanan bawah text_tengah text_blok">Permasalahan</td>
			    </tr>
			    <tr>
			        <td class="atas kanan bawah kiri text_tengah text_blok">1</td>
			        <td class="atas kanan bawah text_tengah text_blok">2</td>
			        <td class="atas kanan bawah text_tengah text_blok">3</td>
			        <td class="atas kanan bawah text_tengah text_blok">4</td>
			        <td class="atas kanan bawah text_tengah text_blok">5</td>
			        <td class="atas kanan bawah text_tengah text_blok">6</td>
			        <td class="atas kanan bawah text_tengah text_blok">7</td>
			        <td class="atas kanan bawah text_tengah text_blok">8</td>
			        <td class="atas kanan bawah text_tengah text_blok">9 = (8 / 7) * 100</td>
			        <td class="atas kanan bawah text_tengah text_blok">10</td>
			        <td class="atas kanan bawah text_tengah text_blok">11</td>
			        <td class="atas kanan bawah text_tengah text_blok">12</td>
			    </tr>
		    </thead>
		    <tbody>
		        '.$body.'
				<tr>
			        <td class="kiri kanan bawah text_blok text_kanan" colspan="6">TOTAL</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($data_all['total'],0,",",".").'</td>
			        <td class="kanan bawah text_kanan text_blok"></td>
			        <td class="kanan bawah text_kanan text_blok"></td>
			        <td class="kanan bawah text_kanan text_blok"></td>
			        <td class="kanan bawah text_kanan text_blok" colspan="2"></td>
			    </tr>
		    </tbody>
		</table>
		<div style="page-break-after:always;"></div>
</div>';
endforeach; 
?>
<script type="text/javascript">
	run_download_excel();
	var _url = window.location.href;
    var url = new URL(_url);
    _url = url.origin+url.pathname+'?key='+url.searchParams.get('key');
	var extend_action = ''
		+'<div style="margin-top: 20px;">'
			+'<label>Sumber Pagu Indikatif: '
				+'<select id="pilih_sumber_pagu" style="padding: 5px;">'
					+'<option value="1">RKA SIPD</option>'
					+'<option value="2">APBD SIMDA</option>'
					+'<option value="3">APBD Pergeseran</option>'
					+'<option value="4">APBD Perubahan</option>'
				+'</select>'
			+'</label>'
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
    jQuery('#action-sipd').append(extend_action);
    jQuery('#pilih_bulan').val(+<?php echo $bulan; ?>);
    jQuery('#pilih_bulan').on('click', function(){
    	var val = jQuery(this).val();
    	if(val != 0){
    		window.open(url+'&bulan='+val,'_blank');
    	}
    });
</script>