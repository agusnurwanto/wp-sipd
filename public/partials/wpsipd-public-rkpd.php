<?php
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

if(!empty($input['id_skpd'])){
	$sql = $wpdb->prepare("
		select 
			* 
		from data_unit 
		where tahun_anggaran=%d
			and id_skpd IN (".$input['id_skpd'].")
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
}
?>
<div id="cetak">
	<h4 style="text-align: center; font-size: 11px; margin: 0; font-weight: bold;">Program dan Kegiatan Perangkat Daerah<br/>Kabupaten Magetan <br/>Tahun <?php echo $input['tahun_anggaran']; ?></h4>
<?php 
	foreach ($units as $k => $unit): 
		if($unit['is_skpd']==1){
			$unit_induk = array($unit);
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
		}
		$subkeg = $wpdb->get_results($wpdb->prepare("
			select 
				* 
			from data_sub_keg_bl 
			where tahun_anggaran=%d
				and active=1
				and id_skpd=%d
			order by kode_sub_giat ASC
		", $input['tahun_anggaran'], $unit['id_skpd']), ARRAY_A);

		$data_all = array(
			'total' => 0,
			'total_n_plus' => 0,
			'data' => array()
		);
		foreach ($subkeg as $kk => $sub) {
			$capaian_prog = $wpdb->get_results($wpdb->prepare("
				select 
					* 
				from data_capaian_prog_sub_keg 
				where tahun_anggaran=%d
					and active=1
					and kode_sbl=%s
				order by id ASC
			", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

			$output_giat = $wpdb->get_results($wpdb->prepare("
				select 
					* 
				from data_output_giat_sub_keg 
				where tahun_anggaran=%d
					and active=1
					and kode_sbl=%s
				order by id ASC
			", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

			$output_sub_giat = $wpdb->get_results($wpdb->prepare("
				select 
					* 
				from data_sub_keg_indikator
				where tahun_anggaran=%d
					and active=1
					and kode_sbl=%s
				order by id ASC
			", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

			$lokasi_sub_giat = $wpdb->get_results($wpdb->prepare("
				select 
					* 
				from data_lokasi_sub_keg
				where tahun_anggaran=%d
					and active=1
					and kode_sbl=%s
				order by id ASC
			", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

			$nama = explode(' ', $sub['nama_sub_giat']);
			$kode_sub_giat = $nama[0];
			$data_renstra = $wpdb->get_results($wpdb->prepare("
				select 
					* 
				from data_renstra
				where tahun_anggaran=%d
					and active=1
					and kode_sub_giat=%s
					and kode_skpd=%s
				order by id ASC
			", $input['tahun_anggaran'], $kode_sub_giat, $unit['kode_skpd']), ARRAY_A);
			// die($wpdb->last_query);

			if(empty($data_all['data'][$sub['kode_urusan']])){
				$data_all['data'][$sub['kode_urusan']] = array(
					'nama'	=> $sub['nama_urusan'],
					'total' => 0,
					'total_n_plus' => 0,
					'data'	=> array()
				);
			}
			if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])){
				$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
					'nama'	=> $sub['nama_bidang_urusan'],
					'total' => 0,
					'total_n_plus' => 0,
					'data'	=> array()
				);
			}
			if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']])){
				$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']] = array(
					'nama'	=> $sub['nama_program'],
					'total' => 0,
					'total_n_plus' => 0,
					'data'	=> array()
				);
			}
			if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']])){
				$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']] = array(
					'nama'	=> $sub['nama_giat'],
					'total' => 0,
					'total_n_plus' => 0,
					'data'	=> array()
				);
			}
			if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']])){
				$nama = explode(' ', $sub['nama_sub_giat']);
				unset($nama[0]);
				$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']] = array(
					'nama'	=> implode(' ', $nama),
					'total' => 0,
					'total_n_plus' => 0,
					'capaian_prog' => $capaian_prog,
					'output_giat' => $output_giat,
					'output_sub_giat' => $output_sub_giat,
					'lokasi_sub_giat' => $lokasi_sub_giat,
					'data_renstra' => $data_renstra,
					'data'	=> array()
				);
			}
			$data_all['total'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['total'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total'] += $sub['pagu'];

			$data_all['total_n_plus'] += $sub['pagu_n_depan'];
			$data_all['data'][$sub['kode_urusan']]['total_n_plus'] += $sub['pagu_n_depan'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_n_plus'] += $sub['pagu_n_depan'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_n_plus'] += $sub['pagu_n_depan'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_n_plus'] += $sub['pagu_n_depan'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_n_plus'] += $sub['pagu_n_depan'];
		}

		// print_r($data_all); die();
		$body = '';
		foreach ($data_all['data'] as $kd_urusan => $urusan) {
			$body .= '
				<tr>
			        <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
			        <td class="kanan bawah">&nbsp;</td>
			        <td class="kanan bawah">&nbsp;</td>
			        <td class="kanan bawah">&nbsp;</td>
			        <td class="kanan bawah">&nbsp;</td>
			        <td class="kanan bawah text_blok" colspan="14">'.$urusan['nama'].'</td>
			    </tr>
			';
			foreach ($urusan['data'] as $kd_bidang => $bidang) {
				$kd_bidang = explode('.', $kd_bidang);
				$kd_bidang = $kd_bidang[count($kd_bidang)-1];
				$body .= '
					<tr>
			            <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
			            <td class="kanan bawah text_blok">'.$kd_bidang.'</td>
			            <td class="kanan bawah">&nbsp;</td>
			            <td class="kanan bawah">&nbsp;</td>
			            <td class="kanan bawah">&nbsp;</td>
			            <td class="kanan bawah text_blok" colspan="8">'.$bidang['nama'].'</td>
			            <td class="kanan bawah text_kanan text_blok">'.number_format($bidang['total'],0,",",".").'</td>
			            <td class="kanan bawah" colspan="4">&nbsp;</td>
			            <td class="kanan bawah text_kanan text_blok">'.number_format($bidang['total_n_plus'],0,",",".").'</td>
			        </tr>
				';
				foreach ($bidang['data'] as $kd_program => $program) {
					$kd_program = explode('.', $kd_program);
					$kd_program = $kd_program[count($kd_program)-1];
					$body .= '
						<tr>
				            <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
				            <td class="kanan bawah text_blok">'.$kd_bidang.'</td>
				            <td class="kanan bawah text_blok">'.$kd_program.'</td>
				            <td class="kanan bawah">&nbsp;</td>
				            <td class="kanan bawah">&nbsp;</td>
				            <td class="kanan bawah text_blok" colspan="8">'.$program['nama'].'</td>
				            <td class="kanan bawah text_kanan text_blok">'.number_format($program['total'],0,",",".").'</td>
				            <td class="kanan bawah" colspan="4">&nbsp;</td>
				            <td class="kanan bawah text_kanan text_blok">'.number_format($program['total_n_plus'],0,",",".").'</td>
				        </tr>
					';
					foreach ($program['data'] as $kd_giat => $giat) {
						$kd_giat = explode('.', $kd_giat);
						$kd_giat = $kd_giat[count($kd_giat)-2].'.'.$kd_giat[count($kd_giat)-1];
						$body .= '
					        <tr>
					            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">'.$kd_urusan.'</td>
					            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:00;" width="5">'.$kd_bidang.'</td>
					            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:000;" width="5">'.$kd_program.'</td>
					            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">'.$kd_giat.'</td>
					            <td style="border:.5pt solid #000; vertical-align:middle;" width="5">&nbsp;</td>
					            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" colspan="8">'.$giat['nama'].'</td>
					            <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">'.number_format($giat['total'],0,",",".").'</td>
					            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" colspan="4"></td>
					            <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">'.number_format($giat['total_n_plus'],0,",",".").'</td>
					        </tr>
						';
						foreach ($giat['data'] as $kd_sub_giat => $sub_giat) {
							$kd_sub_giat = explode('.', $kd_sub_giat);
							$kd_sub_giat = $kd_sub_giat[count($kd_sub_giat)-1];
							$capaian_prog = '';
							if(!empty($sub_giat['capaian_prog'])){
								$capaian_prog = $sub_giat['capaian_prog'][0]['capaianteks'];
							}
							$target_capaian_prog = '';
							if(!empty($sub_giat['capaian_prog'])){
								$target_capaian_prog = $sub_giat['capaian_prog'][0]['targetcapaianteks'];
							}
							$output_giat = '';
							if(!empty($sub_giat['output_giat'])){
								$output_giat = $sub_giat['output_giat'][0]['outputteks'];
							}
							$target_output_giat = '';
							if(!empty($sub_giat['output_giat'])){
								$target_output_giat = $sub_giat['output_giat'][0]['targetoutputteks'];
							}
							$output_sub_giat = '';
							if(!empty($sub_giat['output_sub_giat'])){
								$output_sub_giat = $sub_giat['output_sub_giat'][0]['outputteks'];
							}
							$target_output_sub_giat = '';
							if(!empty($sub_giat['output_sub_giat'])){
								$target_output_sub_giat = $sub_giat['output_sub_giat'][0]['targetoutputteks'];
							}
							$lokasi_sub_giat = '';
							if(!empty($sub_giat['lokasi_sub_giat'])){
								$lokasi_sub_giat = $sub_giat['lokasi_sub_giat'][0]['daerahteks'].', '.$sub_giat['lokasi_sub_giat'][0]['camatteks'].', '.$sub_giat['lokasi_sub_giat'][0]['lurahteks'];
							}
							$ind_n_plus = '';
							$target_ind_n_plus = '';
							if(!empty($sub_giat['data_renstra'])){
								$ind_n_plus = $sub_giat['data_renstra'][0]['indikator_sub'];
								$start_rpjmd = 2018;
								if(!empty($pengaturan)){
									$start_rpjmd = $pengaturan[0]['awal_rpjmd'];
								}
								$urut = $input['tahun_anggaran']-$start_rpjmd;
								if($urut<=5){
									$target_ind_n_plus = $sub_giat['data_renstra'][0]['target_sub_'.($urut+1)];
								}
							}
							$body .= '
						        <tr>
						            <td class="kiri kanan bawah">'.$kd_urusan.'</td>
						            <td class="kanan bawah">'.$kd_bidang.'</td>
						            <td class="kanan bawah">'.$kd_program.'</td>
						            <td class="kanan bawah">'.$kd_giat.'</td>
						            <td class="kanan bawah">'.$kd_sub_giat.'</td>
						            <td class="kanan bawah">'.$sub_giat['nama'].'</td>
						            <td class="kanan bawah">'.$capaian_prog.'</td>
						            <td class="kanan bawah">'.$output_sub_giat.'</td>
						            <td class="kanan bawah">'.$output_giat.'</td>
						            <td class="kanan bawah">'.$lokasi_sub_giat.'</td>
						            <td class="kanan bawah">'.$target_capaian_prog.'</td>
						            <td class="kanan bawah">'.$target_output_sub_giat.'</td>
						            <td class="kanan bawah">'.$target_output_giat.'</td>
						            <td class="kanan bawah text_kanan">'.number_format($sub_giat['total'],0,",",".").'</td>
						            <td class="kanan bawah"><br/></td>
						            <td class="kanan bawah">&nbsp;</td>
						            <td class="kanan bawah">'.$ind_n_plus.'</td>
						            <td class="kanan bawah">'.$target_ind_n_plus.'</td>
						            <td class="kanan bawah text_kanan">'.number_format($sub_giat['total_n_plus'],0,",",".").'</td>
						        </tr>
							';
						}
					}
				}
			}
		}
?>
	<table cellpadding="2" cellspacing="0" style="font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; border-collapse: collapse; width:100%; table-layout:fixed; overflow-wrap: break-word; font-size: 60%; border: 0;">
	    <thead>
	    	<tr>
	            <th style="padding: 0; border: 0; width:1%"></th>
	            <th style="padding: 0; border: 0; width:1.2%"></th>
	            <th style="padding: 0; border: 0; width:1.5%"></th>
	            <th style="padding: 0; border: 0; width:1.5%"></th>
	            <th style="padding: 0; border: 0; width:1.5%"></th>
	            <th style="padding: 0; border: 0; width:7%"></th>
	            <th style="padding: 0; border: 0; width:7.5%"></th>
	            <th style="padding: 0; border: 0; width:7.5%"></th>
	            <th style="padding: 0; border: 0; width:7.5%"></th>
	            <th style="padding: 0; border: 0; width:4%"></th>
	            <th style="padding: 0; border: 0; width:3.5%"></th>
	            <th style="padding: 0; border: 0; width:3.5%"></th>
	            <th style="padding: 0; border: 0; width:3.5%"></th>
	            <th style="padding: 0; border: 0; width:5.5%"></th>
	            <th style="padding: 0; border: 0; width:3.5%"></th>
	            <th style="padding: 0; border: 0; width:4%"></th>
	            <th style="padding: 0; border: 0; width:7.5%"></th>
	            <th style="padding: 0; border: 0; width:3.5%"></th>
	            <th style="padding: 0; border: 0; width:5.5%"></th>
	        </tr>
		    <tr>
		        <td colspan="19" style="vertical-align:middle; font-weight:bold; border: 0;">
		            Unit Organisasi : <?php echo $unit_induk[0]['kode_skpd']; ?>&nbsp;<?php echo $unit_induk[0]['nama_skpd']; ?><br/>
		            Sub Unit Organisasi : <?php echo $unit['kode_skpd']; ?>&nbsp;<?php echo $unit['nama_skpd']; ?>
		        </td>
		    </tr>
		    <tr>
		        <td class="atas kanan bawah kiri text_tengah text_blok" colspan="5" rowspan="3">Kode</td>
		        <td class="atas kanan bawah text_tengah text_blok" rowspan="3">Urusan/ Bidang Urusan Pemerintahan Daerah Dan Program/ Kegiatan</td>
		        <td class="atas kanan bawah text_tengah text_blok" colspan="3">Indikator Kinerja</td>
		        <td class="atas kanan bawah text_tengah text_blok" colspan="6">Rencana Tahun <?php echo $input['tahun_anggaran']; ?></td>
		        <td class="atas kanan bawah text_tengah text_blok" rowspan="3">Catatan Penting</td>
		        <td class="atas kanan bawah text_tengah text_blok" colspan="3">Prakiraan Maju Rencana Tahun <?php echo ($input['tahun_anggaran']+1); ?></td>
		    </tr>
		    <tr>
		        <td class="kanan bawah text_tengah text_blok" rowspan="2">Capaian Program</td>
		        <td class="kanan bawah text_tengah text_blok" rowspan="2">Keluaran Sub Kegiatan</td>
		        <td class="kanan bawah text_tengah text_blok" rowspan="2">Hasil Kegiatan</td>
		        <td class="kanan bawah text_tengah text_blok" rowspan="2">Lokasi Output Kegiatan</td>
		        <td class="kanan bawah text_tengah text_blok" colspan="3">Target Capaian Kinerja</td>
		        <td class="kanan bawah text_tengah text_blok" rowspan="2">Pagu Indikatif (Rp.)</td>
		        <td class="kanan bawah text_tengah text_blok" rowspan="2">Sumber Dana</td>
		        <td class="kanan bawah text_tengah text_blok" colspan="2">Target Capaian Kinerja</td>
		        <td class="kanan bawah text_tengah text_blok" rowspan="2">Kebutuhan Dana/<br/>Pagu Indikatif (Rp.)</td>
		    </tr>
		    <tr>
		        <td class="kanan bawah text_tengah text_blok">Program</td>
		        <td class="kanan bawah text_tengah text_blok">Keluaran Sub Kegiatan</td>
		        <td class="kanan bawah text_tengah text_blok">Hasil Kegiatan</td>
		        <td class="kanan bawah text_tengah text_blok">Tolok Ukur</td>
		        <td class="kanan bawah text_tengah text_blok">Target</td>
		    </tr>
	    </thead>
	    <tbody>
	        <?php echo $body; ?>
			<tr>
		        <td class="kiri kanan bawah text_blok text_kanan" colspan="13">TOTAL</td>
		        <td class="kanan bawah text_kanan text_blok"><?php echo number_format($data_all['total'],0,",","."); ?></td>
		        <td class="kanan bawah" colspan="4">&nbsp;</td>
		        <td class="kanan bawah text_kanan text_blok"><?php echo number_format($data_all['total_n_plus'],0,",","."); ?></td>
		    </tr>
	    </tbody>
	</table>
</div>
<?php endforeach; ?>
<script type="text/javascript">
	run_download_excel();
</script>