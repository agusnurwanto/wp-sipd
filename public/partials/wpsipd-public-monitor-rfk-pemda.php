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

		    $units = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd, is_skpd from data_unit where active=1 and tahun_anggaran=".$input['tahun_anggaran'].' and is_skpd=1 order by nama_skpd ASC LIMIT 5', ARRAY_A);

		    $total_pagu_pemkab = 0;
			$total_simda_pemkab = 0;
			$realisasi_pemkab = 0;
			$total_rak_simda_pemkab = 0;
			$current_user = wp_get_current_user();

		    foreach($units as $unit){

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
							k.id as id_sub_keg, 
							r.rak,
							r.realisasi_anggaran, 
							r.id as id_rfk, 
							r.realisasi_fisik, 
							r.permasalahan,
							r.catatan_verifikator
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

					$total_pagu_unit = 0;
					$total_simda_unit = 0;
					$realisasi_unit = 0;
					$total_rak_simda_unit = 0;
					$realisasi_fisik_unit = array();
					$capaian_arr = array();
					$capaian_rata = 0;
					$realisasi_fisik_rata = 0;

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
						if($sumber_pagu == 1){
							$total_pagu = $sub['pagu'];
							$total_pagu_unit += $total_pagu;
						}
						$total_simda = $this->get_pagu_simda_last(array(
							'tahun_anggaran' => $input['tahun_anggaran'],
							'pagu_simda' => $sub['pagu_simda'],
							'id_sub_keg' => $sub['id_sub_keg'],
							'kd_urusan' => $_kd_urusan,
							'kd_bidang' => $_kd_bidang,
							'kd_unit' => $kd_unit,
							'kd_sub' => $kd_sub_unit,
							'kd_prog' => $kd_prog,
							'id_prog' => $id_prog,
							'kd_keg' => $kd_keg
						));
						$total_simda_unit += $total_simda;

						$total_rak_simda = $this->get_rak_simda(array(
							'user' => $current_user->display_name,
							'id_skpd' => $input['id_skpd'],
							'kode_sbl' => $sub['kode_sbl'],
							'tahun_anggaran' => $input['tahun_anggaran'],
							'realisasi_anggaran' => $sub['rak'],
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
						$total_rak_simda_unit += $total_rak_simda;

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
						$realisasi_unit += $realisasi;
						$realisasi_fisik_unit[] = $sub['realisasi_fisik'];
					} // end foreach subkeg

					if($total_simda_unit != 0){
						$capaian_rata = $this->pembulatan($realisasi_unit/$total_simda_unit*100);
					}
					if(array_sum($realisasi_fisik_unit) != 0){
						$realisasi_fisik_rata = array_sum($realisasi_fisik_unit)/count($realisasi_fisik_unit);
					}
		    		
		    		// $url_skpd = generatePage('RFK '.$unit['nama_skpd'].' '.$unit['kode_skpd'].' | '.$input['tahun_anggaran'], $input['tahun_anggaran'], '[monitor_rfk tahun_anggaran="'.$input['tahun_anggaran'].'" id_skpd="'.$unit['id_skpd'].'"]');
		    		$url_skpd ='';
		    		$body.='
			    	<tr>
				    	<td class="atas kanan bawah kiri text_tengah text_blok" colspan="5">'.$unit['kode_skpd'].'</td>
				        <td class="atas kanan bawah text_kiri text_blok"><a href="'.$url_skpd.'" target="_blank">'.$unit['nama_skpd'].'</a></td>
				        <td class="atas kanan bawah text_kanan text_blok">'.number_format($total_pagu_unit,0,",",".").'</td>
				        <td class="atas kanan bawah text_kanan text_blok">'.number_format($total_simda_unit,0,",",".").'</td>
				        <td class="atas kanan bawah text_kanan text_blok">'.number_format($realisasi_unit,0,",",".").'</td>
				        <td class="atas kanan bawah text_tengah text_blok">'.$capaian_rata.'</td>
				        <td class="atas kanan bawah text_tengah text_blok">'.number_format($total_rak_simda_unit,0,",",".").'</td>
				        <td class="atas kanan bawah text_tengah text_blok">'.number_format($realisasi_fisik_rata,0,",",".").'</td>
				    </tr>
			    	';

			    	$total_pagu_pemkab +=$total_pagu_unit;
			    	$total_simda_pemkab +=$total_simda_unit;
			    	$realisasi_pemkab +=$realisasi_unit;
			    	$realisasi_pemkab +=$realisasi_unit;
			    	$total_rak_simda_pemkab +=$total_rak_simda_unit;
				}


		    	$subunits = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd from data_unit where active=1 and tahun_anggaran=".$input['tahun_anggaran']." and is_skpd=0 and id_unit=".$unit["id_skpd"]." order by nama_skpd ASC", ARRAY_A);
		    	if(!empty($subunits)){
		    		foreach ($subunits as $key => $subunit) {
		    			$body.='
					    	<tr>
						    	<td class="atas kanan bawah kiri text_tengah text_blok" colspan="5">'.$subunit['kode_skpd'].'</td>
						        <td class="atas kanan bawah text_kiri text_blok"><span style="margin-left:10px">'.$subunit['nama_skpd'].'</span></td>
						        <td class="atas kanan bawah text_tengah text_blok"></td>
						        <td class="atas kanan bawah text_tengah text_blok"></td>
						        <td class="atas kanan bawah text_tengah text_blok"></td>
						        <td class="atas kanan bawah text_tengah text_blok"></td>
						        <td class="atas kanan bawah text_tengah text_blok"></td>
						    </tr>
					    	';
		    		}
		    	}
		    } 

		$body.='
				<tr>
			        <td class="kiri kanan bawah text_blok text_kanan" colspan="6">TOTAL</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($total_pagu_pemkab,0,",",".").'</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($total_simda_pemkab,0,",",".").'</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($realisasi_pemkab,0,",",".").'</td>
			        <td class="kanan bawah text_kanan text_blok"></td>
			        <td class="kanan bawah text_tengah text_blok">'.number_format($realisasi_pemkab,0,",",".").'</td>
			        <td class="kanan bawah text_blok total-realisasi-fisik text_tengah"></td>
			    </tr>
		    </tbody>
		</table>
	</div>';
	echo $body;
