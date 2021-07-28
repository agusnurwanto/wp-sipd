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
$current_user = wp_get_current_user();
foreach ($units as $k => $unit): 
	$kd_unit_simda = explode('.', carbon_get_theme_option('crb_unit_'.$unit['id_skpd']));
	$_kd_urusan = $kd_unit_simda[0];
	$_kd_bidang = $kd_unit_simda[1];
	$kd_unit = $kd_unit_simda[2];
	$kd_sub_unit = $kd_unit_simda[3];

	if($unit['is_skpd']==1){
		$unit_induk = array($unit);
		$subkeg = $wpdb->get_results($wpdb->prepare("
			select 
				k.*,
				r.rak,
				r.realisasi_anggaran, 
				r.id as id_rfk, 
				r.realisasi_fisik, 
				r.permasalahan
			from data_sub_keg_bl k
				left join data_rfk r on k.kode_sbl=r.kode_sbl
					AND k.tahun_anggaran=r.tahun_anggaran
					AND k.id_sub_skpd=r.id_skpd
					AND r.bulan=%d
			where k.tahun_anggaran=%d
				and k.active=1
				and k.id_skpd=%d
				and k.id_sub_skpd=%d
			order by k.kode_sub_giat ASC
		", $bulan, $input['tahun_anggaran'], $unit['id_skpd'], $unit['id_skpd']), ARRAY_A);
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
				k.*,
				r.rak,
				r.realisasi_anggaran, 
				r.id as id_rfk, 
				r.realisasi_fisik, 
				r.permasalahan
			from data_sub_keg_bl k
				left join data_rfk r on k.kode_sbl=r.kode_sbl
					AND k.tahun_anggaran=r.tahun_anggaran
					AND k.id_sub_skpd=r.id_skpd
					AND r.bulan=%d
			where k.tahun_anggaran=%d
				and k.active=1
				and k.id_sub_skpd=%d
			order by kode_sub_giat ASC
		", $bulan, $input['tahun_anggaran'], $unit['id_skpd']), ARRAY_A);
	}
	// echo $wpdb->last_query.'<br>';

	$data_all = array(
		'total' => 0,
		'realisasi' => 0,
		'data' => array()
	);
	foreach ($subkeg as $kk => $sub) {
		$kd = explode('.', $sub['kode_sub_giat']);
		$kd_urusan90 = (int) $kd[0];
		$kd_bidang90 = (int) $kd[1];
		$kd_program90 = (int) $kd[2];
		$kd_kegiatan90 = ((int) $kd[3]).'.'.$kd[4];
		$kd_sub_kegiatan = (int) $kd[5];
		$nama_keg = explode(' ', $sub['nama_sub_giat']);
        unset($nama_keg[0]);
        $nama_keg = implode(' ', $nama_keg);
		$mapping = $this->simda->cekKegiatanMapping(array(
			'kd_urusan90' => $kd_urusan90,
			'kd_bidang90' => $kd_bidang90,
			'kd_program90' => $kd_program90,
			'kd_kegiatan90' => $kd_kegiatan90,
			'kd_sub_kegiatan' => $kd_sub_kegiatan,
			'nama_program' => $sub['nama_giat'],
			'nama_kegiatan' => $nama_keg,
		));

		$kd_urusan = 0;
		$kd_bidang = 0;
		$kd_prog = 0;
		$kd_keg = 0;
		if(!empty($mapping[0]) && !empty($mapping[0]->kd_urusan)){
			$kd_urusan = $mapping[0]->kd_urusan;
			$kd_bidang = $mapping[0]->kd_bidang;
			$kd_prog = $mapping[0]->kd_prog;
			$kd_keg = $mapping[0]->kd_keg;
		}

        $id_prog = $kd_urusan.$this->simda->CekNull($kd_bidang);
		$total_pagu = 0;
		if($sumber_pagu == 1){
			$total_pagu = $sub['pagu'];
		}else if(
			$sumber_pagu == 4
			|| $sumber_pagu == 5
			|| $sumber_pagu == 6
		){
			$total_pagu = $this->get_pagu_simda(array(
				'tahun_anggaran' => $input['tahun_anggaran'],
				'sumber_pagu' => $sumber_pagu,
				'kd_urusan' => $_kd_urusan,
				'kd_bidang' => $_kd_bidang,
				'kd_unit' => $kd_unit,
				'kd_sub' => $kd_sub_unit,
				'kd_prog' => $kd_prog,
				'id_prog' => $id_prog,
				'kd_keg' => $kd_keg
			));
		}
		$realisasi = $this->get_realisasi_simda(array(
			'user' => $current_user->display_name,
			'id_skpd' => $input['id_skpd'],
			'kode_sbl' => $sub['kode_sbl'],
			'tahun_anggaran' => $input['tahun_anggaran'],
			'realisasi_anggaran' => $sub['realisasi_anggaran'],
			'id_rfk' => $sub['id_rfk'],
			'bulan' => $bulan,
			'kd_urusan' => $_kd_urusan,
			'kd_bidang' => $_kd_bidang,
			'kd_unit' => $kd_unit,
			'kd_sub' => $kd_sub_unit,
			'kd_prog' => $kd_prog,
			'id_prog' => $id_prog,
			'kd_keg' => $kd_keg
		));

		if(empty($data_all['data'][$sub['kode_urusan']])){
			$data_all['data'][$sub['kode_urusan']] = array(
				'nama'	=> $sub['nama_urusan'],
				'total' => 0,
				'realisasi' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])){
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
				'nama'	=> $sub['nama_bidang_urusan'],
				'total' => 0,
				'realisasi' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']])){
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']] = array(
				'nama'	=> $sub['nama_program'],
				'total' => 0,
				'realisasi' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']])){
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']] = array(
				'nama'	=> $sub['nama_giat'],
				'total' => 0,
				'realisasi' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']])){
			$nama = explode(' ', $sub['nama_sub_giat']);
			unset($nama[0]);
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']] = array(
				'nama'	=> implode(' ', $nama),
				'total' => 0,
				'realisasi' => 0,
				'data'	=> $sub
			);
		}
		$data_all['total'] += $total_pagu;
		$data_all['data'][$sub['kode_urusan']]['total'] += $total_pagu;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total'] += $total_pagu;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total'] += $total_pagu;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total'] += $total_pagu;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total'] += $total_pagu;

		$data_all['realisasi'] += $realisasi;
		$data_all['data'][$sub['kode_urusan']]['realisasi'] += $realisasi;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['realisasi'] += $realisasi;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['realisasi'] += $realisasi;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['realisasi'] += $realisasi;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['realisasi'] += $realisasi;
	}

	// print_r($data_all); die();
	$body = '';
	$body_rkpd = '';
	foreach ($data_all['data'] as $kd_urusan => $urusan) {
		$body .= '
			<tr class="urusan" data-kode="'.$kd_urusan.'">
		        <td class="text_tengah kiri kanan bawah text_blok">'.$kd_urusan.'</td>
		        <td class="text_tengah kanan bawah">&nbsp;</td>
		        <td class="text_tengah kanan bawah">&nbsp;</td>
		        <td class="text_tengah kanan bawah">&nbsp;</td>
		        <td class="text_tengah kanan bawah">&nbsp;</td>
		        <td class="kanan bawah text_blok" colspan="7">'.$urusan['nama'].'</td>
		    </tr>
		';
		foreach ($urusan['data'] as $kd_bidang => $bidang) {
			$kd_bidang = explode('.', $kd_bidang);
			$kd_bidang = $kd_bidang[count($kd_bidang)-1];
			$capaian = 0;
			if(!empty($bidang['total'])){
				$capaian = $this->pembulatan(($bidang['realisasi']/$bidang['total'])*100);
			}
			$body .= '
				<tr class="bidang" data-kode="'.$kd_urusan.'.'.$kd_bidang.'">
		            <td class="text_tengah kiri kanan bawah text_blok">'.$kd_urusan.'</td>
		            <td class="text_tengah kanan bawah text_blok">'.$kd_bidang.'</td>
		            <td class="text_tengah kanan bawah">&nbsp;</td>
		            <td class="text_tengah kanan bawah">&nbsp;</td>
		            <td class="text_tengah kanan bawah">&nbsp;</td>
		            <td class="kanan bawah text_blok">'.$bidang['nama'].'</td>
		            <td class="kanan bawah text_kanan text_blok">'.number_format($bidang['total'],0,",",".").'</td>
		            <td class="kanan bawah text_kanan text_blok">'.number_format($bidang['realisasi'],0,",",".").'</td>
		            <td class="kanan bawah text_blok text_tengah">'.$capaian.'</td>
		            <td class="kanan bawah text_blok bidang-realisasi-fisik text_tengah"></td>
		        	<td class="kanan bawah text_kanan text_blok" colspan="2"></td>
		        </tr>
			';
			foreach ($bidang['data'] as $kd_program => $program) {
				$kd_program = explode('.', $kd_program);
				$kd_program = $kd_program[count($kd_program)-1];
				$capaian = 0;
				if(!empty($program['total'])){
					$capaian = $this->pembulatan(($program['realisasi']/$program['total'])*100);
				}
				$body .= '
					<tr class="program" data-kode="'.$kd_urusan.'.'.$kd_bidang.'.'.$kd_program.'">
			            <td class="text_tengah kiri kanan bawah text_blok">'.$kd_urusan.'</td>
			            <td class="text_tengah kanan bawah text_blok">'.$kd_bidang.'</td>
			            <td class="text_tengah kanan bawah text_blok">'.$kd_program.'</td>
			            <td class="text_tengah kanan bawah">&nbsp;</td>
			            <td class="text_tengah kanan bawah">&nbsp;</td>
			            <td class="kanan bawah text_blok">'.$program['nama'].'</td>
			            <td class="kanan bawah text_kanan text_blok">'.number_format($program['total'],0,",",".").'</td>
			            <td class="kanan bawah text_kanan text_blok">'.number_format($program['realisasi'],0,",",".").'</td>
			            <td class="kanan bawah text_blok text_tengah">'.$capaian.'</td>
			            <td class="kanan bawah text_blok program-realisasi-fisik text_tengah"></td>
		        		<td class="kanan bawah text_kanan text_blok" colspan="2"></td>
			        </tr>
				';
				foreach ($program['data'] as $kd_giat1 => $giat) {
					$kd_giat = explode('.', $kd_giat1);
					$kd_giat = $kd_giat[count($kd_giat)-2].'.'.$kd_giat[count($kd_giat)-1];
					$capaian = 0;
					if(!empty($giat['total'])){
						$capaian = $this->pembulatan(($giat['realisasi']/$giat['total'])*100);
					}
					$nama_page = $input['tahun_anggaran'] . ' | ' . $unit['kode_skpd'] . ' | ' . $kd_giat1 . ' | ' . $giat['nama'];
					$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
					$body .= '
				        <tr class="kegiatan" data-kode="'.$kd_urusan.'.'.$kd_bidang.'.'.$kd_program.'.'.$kd_giat.'">
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">'.$kd_urusan.'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:00;" width="5">'.$kd_bidang.'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:000;" width="5">'.$kd_program.'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">'.$kd_giat.'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle;" width="5">&nbsp;</td>
				            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;"><a href="'.$custom_post->guid . '?key=' . $this->gen_key().'" target="_blank">'.$giat['nama'].'</a></td>
				            <td style="border:.5pt solid #000; vertical-align:middle; text-align:right; font-weight:bold;">'.number_format($giat['total'],0,",",".").'</td>
				            <td style="border:.5pt solid #000; vertical-align:middle; text-align:right; font-weight:bold;">'.number_format($giat['realisasi'],0,",",".").'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;">'.$capaian.'</td>
				            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" class="kegiatan-realisasi-fisik text_tengah"></td>
		        			<td class="kanan bawah text_kanan text_blok" colspan="2"></td>
				        </tr>
					';
					foreach ($giat['data'] as $kd_sub_giat1 => $sub_giat) {
						$sql = "
							SELECT 
								* 
							from data_dana_sub_keg 
							where kode_sbl='".$sub_giat['data']['kode_sbl']."'
								AND tahun_anggaran=".$sub_giat['data']['tahun_anggaran']."
								AND active=1";
						$sd_sub_keg = $wpdb->get_results($sql, ARRAY_A);
						$sd_sub = array();
						foreach ($sd_sub_keg as $key => $sd) {
							$new_sd = explode(' - ', $sd['namadana']);
							if(!empty($new_sd[1])){
								$sd_sub[] = $new_sd[1];
							}
						}
						$kd_sub_giat = explode('.', $kd_sub_giat1);
						$kd_sub_giat = $kd_sub_giat[count($kd_sub_giat)-1];
						$capaian = 0;
						if(!empty($sub_giat['total'])){
							$capaian = $this->pembulatan(($sub_giat['realisasi']/$sub_giat['total'])*100);
						}
						$realisasi_fisik = 0;
						if(!empty($sub_giat['data']['realisasi_fisik'])){
							$realisasi_fisik = $sub_giat['data']['realisasi_fisik'];
						}
						$body .= '
					        <tr data-kode="'.$kd_sub_giat1.'" data-kdsbl="'.$sub_giat['data']['kode_sbl'].'" data-idskpd="'.$sub_giat['data']['id_sub_skpd'].'" data-pagu="'.$sub_giat['total'].'">
					            <td class="kiri kanan bawah">'.$kd_urusan.'</td>
					            <td class="kanan bawah">'.$kd_bidang.'</td>
					            <td class="kanan bawah">'.$kd_program.'</td>
					            <td class="kanan bawah">'.$kd_giat.'</td>
					            <td class="kanan bawah">'.$kd_sub_giat.'</td>
					            <td class="kanan bawah nama_sub_giat">'.$sub_giat['nama'].'</td>
					            <td class="kanan bawah text_kanan">'.number_format($sub_giat['total'],0,",",".").'</td>
					            <td class="kanan bawah text_kanan">'.number_format($sub_giat['realisasi'],0,",",".").'</td>
					            <td class="kanan bawah text_tengah">'.$capaian.'</td>
					            <td class="kanan bawah realisasi-fisik text_tengah" contenteditable="true">'.$realisasi_fisik.'</td>
					            <td class="kanan bawah">'.implode(',<br>', $sd_sub).'</td>
					            <td class="kanan bawah permasalahan" contenteditable="true">'.$sub_giat['data']['permasalahan'].'</td>
					        </tr>
						';
					}
				}
			}
		}
	}
	$capaian_total = 0;
	if(!empty($data_all['total'])){
		$capaian_total = $this->pembulatan(($data_all['realisasi']/$data_all['total'])*100);
	}
	echo '
	<input type="hidden" value="'.carbon_get_theme_option( 'crb_api_key_extension' ).'" id="api_key">
	<input type="hidden" value="'.$input['tahun_anggaran'].'" id="tahun_anggaran">
	<input type="hidden" value="'.$unit['id_skpd'].'" id="id_skpd">
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
			        <td class="kanan bawah text_kanan text_blok">'.number_format($data_all['realisasi'],0,",",".").'</td>
			        <td class="kanan bawah text_tengah text_blok">'.$capaian_total.'</td>
			        <td class="kanan bawah text_blok total-realisasi-fisik text_tengah"></td>
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
    function generate_total(){
    	var total_parent = {};
    	var total = 0;
    	var total_s = 0;
    	jQuery('.realisasi-fisik').map(function(i, b){
    		var kode_sub = jQuery(b).closest('tr').attr('data-kode').split('.');
    		var kode_kegiatan = kode_sub[0]+'.'+kode_sub[1]+'.'+kode_sub[2]+'.'+kode_sub[3]+'.'+kode_sub[4];
    		var kode_program = kode_sub[0]+'.'+kode_sub[1]+'.'+kode_sub[2];
    		var kode_bidang = kode_sub[0]+'.'+kode_sub[1];
    		var val = jQuery(b).text();
    		if(!isNaN(val) && +val >= 0 && +val <= 100){
    			total += +val;
    			total_s++;
    			if(typeof total_parent[kode_bidang] == 'undefined'){
    				total_parent[kode_bidang] = {
    					total_bidang : 0,
	    				total_bidang_s : 0
    				}
    			}
    			if(typeof total_parent[kode_program] == 'undefined'){
    				total_parent[kode_program] = {
				    	total_program : 0,
				    	total_program_s : 0
    				}
    			}
    			if(typeof total_parent[kode_kegiatan] == 'undefined'){
    				total_parent[kode_kegiatan] = {
				    	total_kegiatan : 0,
				    	total_kegiatan_s : 0
    				}
    			}
    			total_parent[kode_bidang].total_bidang += +val;
    			total_parent[kode_bidang].total_bidang_s++;
    			total_parent[kode_program].total_program += +val;
    			total_parent[kode_program].total_program_s++;
    			total_parent[kode_kegiatan].total_kegiatan += +val;
    			total_parent[kode_kegiatan].total_kegiatan_s++;
    		}
    	});
    	for(var i in total_parent){
    		if(typeof(total_parent[i].total_bidang) != 'undefined'){
    			var total_bidang = 0;
    			if(total_parent[i].total_bidang_s != 0){
	    			total_bidang = Math.round((total_parent[i].total_bidang/total_parent[i].total_bidang_s)*100)/100;
    			}
	    		jQuery('tr[data-kode="'+i+'"]').find('.bidang-realisasi-fisik').text(total_bidang);
	    	}else if(typeof(total_parent[i].total_program) != 'undefined'){
    			var total_program = 0;
    			if(total_parent[i].total_program_s != 0){
	    			total_program = Math.round((total_parent[i].total_program/total_parent[i].total_program_s)*100)/100;
	    		}
	    		jQuery('tr[data-kode="'+i+'"]').find('.program-realisasi-fisik').text(total_program);
	    	}else if(typeof(total_parent[i].total_kegiatan) != 'undefined'){
    			var total_kegiatan = 0;
    			if(total_parent[i].total_kegiatan_s != 0){
	    			total_kegiatan = Math.round((total_parent[i].total_kegiatan/total_parent[i].total_kegiatan_s)*100)/100;
	    		}
	    		jQuery('tr[data-kode="'+i+'"]').find('.kegiatan-realisasi-fisik').text(total_kegiatan);
	    	}
    	}
    	var end = 0;
    	if(total_s != 0){
    		end = Math.round((total/total_s)*100)/100;
    	}
    	jQuery('.total-realisasi-fisik').text(end);
    }
	var _url = window.location.href;
    var url = new URL(_url);
    var param = [];
    if(url.searchParams.get('key')){
    	param.push('key='+url.searchParams.get('key'));
    }
    if(url.searchParams.get('page_id')){
    	param.push('page_id='+url.searchParams.get('page_id'));
    }
    _url = url.origin+url.pathname+'?'+param.join('&');
	var extend_action = ''
		+'<div style="margin-top: 20px;">'
			+'<label>Sumber Pagu Indikatif: '
				+'<select id="pilih_sumber_pagu" style="padding: 5px;">'
					+'<option value="1">RKA SIPD</option>'
					+'<option value="4">APBD SIMDA</option>'
					+'<option value="5">APBD Pergeseran</option>'
					+'<option value="6">APBD Perubahan</option>'
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
			+'<button style="margin-left: 20px;" class="button button-primary" id="simpan-rfk">Simpan RFK</button>'
			+'<button style="margin-left: 20px;" class="button button-default" id="reset-rfk">Reset RFK Bulan Sebelumnya</button>'
		+'</div>';
	jQuery(document).ready(function(){
	    jQuery('#action-sipd').append(extend_action);
	    jQuery('#pilih_sumber_pagu').val(+<?php echo $sumber_pagu; ?>);
	    jQuery('#pilih_bulan').val(+<?php echo $bulan; ?>);
	    jQuery('#pilih_sumber_pagu').on('change', function(){
	    	var val = +jQuery(this).val();
	    	if(val > 0){
	    		window.open(_url+'&sumber_pagu='+val,'_blank');
	    	}
	    	jQuery('#pilih_sumber_pagu').val(+<?php echo $sumber_pagu; ?>);
	    });
	    jQuery('#pilih_bulan').on('change', function(){
	    	var val = +jQuery(this).val();
	    	if(val > 0){
	    		window.open(_url+'&bulan='+val,'_blank');
	    	}
	    	jQuery('#pilih_bulan').val(+<?php echo $bulan; ?>);
	    });
	    jQuery('.realisasi-fisik').on('input', function(){
	    	generate_total();
	    	var val = jQuery(this).text();
	    	if(isNaN(+val) || +val > 100 || +val < 0){
	    		alert('Input realisasi fisik harus dalam format angka antaran 0-100!');
	    	}
	    });
	    jQuery('#reset-rfk').on('click', function(){
	    	if(confirm('Apakah anda yakin untuk data RFK sesuai bulan sebelumnya? Data RFK saat ini akan disamakan dengan bulan sebelumnya!')){
	    		jQuery('#wrap-loading').show();
	    		var id_skpd = jQuery('#id_skpd').val();
	    		jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
		          	type: "post",
		          	data: {
		          		"action": "reset_rfk",
		          		"api_key": jQuery('#api_key').val(),
		          		"tahun_anggaran": jQuery('#tahun_anggaran').val(),
		          		"bulan": jQuery('#pilih_bulan').val(),
		          		"id_skpd": id_skpd,
		          		"user": "<?php echo $current_user->display_name; ?>"
		          	},
		          	dataType: "json",
		          	success: function(data){
		    			jQuery('#wrap-loading').hide();
						alert(data.message);
						window.location.href="";
					},
					error: function(e) {
		    			jQuery('#wrap-loading').hide();
						console.log(e);
					}
				});
	    	}
	    });
	    jQuery('#simpan-rfk').on('click', function(){
	    	if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
	    		jQuery('#wrap-loading').show();
	    		var r_fisik = [];
	    		var r_fisik_s = [];
	    		var cek = false;
	    		jQuery('.realisasi-fisik').map(function(i, b){
	    			var tr = jQuery(b).closest('tr');
	    			var val = jQuery(b).text();
	    			if(isNaN(+val) || +val > 100 || +val < 0){
			    		cek = tr.find('.nama_sub_giat').text();
			    	}
	    			r_fisik_s.push({
	    				realisasi_fisik: val,
	    				permasalahan: tr.find('.permasalahan').text(),
	    				id_skpd: tr.attr('data-idskpd'),
	    				kode_sbl: tr.attr('data-kdsbl')
	    			});
	    			if(i>0 && i%20==0){
	    				r_fisik.push(r_fisik_s);
	    				r_fisik_s = [];
	    			}
	    		});
	    		if(cek){
			    	alert('Input realisasi fisik sub kegiatan "'+cek+'" harus dalam format angka antaran 0-100! Realisasi tidak tersimpan.');
	    			return;
	    		}
	    		if(r_fisik_s.length >= 1){
	    			r_fisik.push(r_fisik_s);
	    		}
	    		r_fisik.reduce(function(sequence, nextData){
	                return sequence.then(function(current_data){
	            		return new Promise(function(resolve_redurce, reject_redurce){
				    		jQuery.ajax({
								url: "<?php echo admin_url('admin-ajax.php'); ?>",
					          	type: "post",
					          	data: {
					          		"action": "simpan_rfk",
					          		"api_key": jQuery('#api_key').val(),
					          		"tahun_anggaran": jQuery('#tahun_anggaran').val(),
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
	            }, Promise.resolve(r_fisik[r_fisik.length-1]))
	            .then(function(){
					jQuery('#wrap-loading').hide();
					alert('Data berhasil disimpan!');
	            })
	            .catch(function(e){
	                console.log(e);
	            });
	    	}
	    });
	    generate_total();
	});
</script>