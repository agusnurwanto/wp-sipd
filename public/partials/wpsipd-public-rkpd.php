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
}

?>
<style type="text/css">
    .cetak{
        font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
        padding:0;
        margin:0;
        font-size:11pt;
    }
    .m-t-20{
        margin-top:20px;
    }
    .bawah{
        border-bottom:1px solid #000;        
    }
    .kiri{
        border-left:1px solid #000;        
    }
    .kanan{        
        border-right:1px solid #000;
    }
    .atas{        
        border-top:1px solid #000;
    }
    .text_tengah{
        text-align: center;
    }
    .text_kiri{
        text-align: left;
    }
    .text_kanan{
        text-align: right;
    }
    .text_blok{
        font-weight: bold;
    }        
    .text_15{
        font-size: 15px;
    }
    .text_20{
        font-size: 20px;
    }
    .logoprinter{
        position:absolute;
        top:150px;
        right:600px;
    }
    thead{
        display:table-header-group;
    }
    .setbreakafter{
        page-break-after: always;
    }
    .setbreakbefore{
        page-break-before: always;
    }
    td {
        page-break-inside: avoid;
    }
    @page  {
        size: A3 landscape;
    }
    @media  print {
        #form-table{
            font-size: 73%;
        }
        body {
            padding:0px;
        }
    }
    @media  screen {
        #form-table{
            font-size: 60%;
        }
        body {
            padding:3px;
        }        
    }
</style>

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
			order by id_sub_giat ASC
		", $input['tahun_anggaran'], $unit['id_skpd']), ARRAY_A);

		$data_all = array(
			'total' => 0,
			'total_n_plus' => 0,
			'data' => array()
		);
		foreach ($subkeg as $kk => $sub) {
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
					'data'	=> array()
				);
			}
			$data_all['total'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['total'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total'] += $sub['pagu'];

			$data_all['total_n_plus'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['total_n_plus'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_n_plus'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_n_plus'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_n_plus'] += $sub['pagu'];
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_n_plus'] += $sub['pagu'];
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
							$body .= '
						        <tr>
						            <td class="kiri kanan bawah">'.$kd_urusan.'</td>
						            <td class="kanan bawah">'.$kd_bidang.'</td>
						            <td class="kanan bawah">'.$kd_program.'</td>
						            <td class="kanan bawah">'.$kd_giat.'</td>
						            <td class="kanan bawah">'.$kd_sub_giat.'</td>
						            <td class="kanan bawah">'.$sub_giat['nama'].'</td>
						            <td class="kanan bawah">capaian program</td>
						            <td class="kanan bawah">indikator kegiatan</td>
						            <td class="kanan bawah">hasil kegiatan</td>
						            <td class="kanan bawah">lokasi output kegiatan</td>
						            <td class="kanan bawah">target program</td>
						            <td class="kanan bawah">target sub giat</td>
						            <td class="kanan bawah">target hasil giat</td>
						            <td class="kanan bawah text_kanan">'.number_format($sub_giat['total'],0,",",".").'</td>
						            <td class="kanan bawah"><br/></td>
						            <td class="kanan bawah">&nbsp;</td>
						            <td class="kanan bawah">tolak ukur n+1</td>
						            <td class="kanan bawah">target n+1</td>
						            <td class="kanan bawah text_kanan">'.number_format($sub_giat['total_n_plus'],0,",",".").'</td>
						        </tr>
							';
						}
					}
				}
			}
		}
?>
<table id="form-table" cellpadding="2" cellspacing="0" style="font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; border-collapse: collapse; width:100%; table-layout:fixed; overflow-wrap: break-word; font-size: 60%; border: 0;">
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
<?php endforeach; ?>