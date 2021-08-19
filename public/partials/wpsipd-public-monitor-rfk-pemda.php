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
			$current_user = wp_get_current_user();

		    foreach($units as $unit){
		    	if($unit['is_skpd']==1){
					$subkeg = $wpdb->get_results($wpdb->prepare("
						select 
							k.*
						from data_sub_keg_bl k
						where k.tahun_anggaran=%d
							and k.active=1
							and k.id_skpd=%d
							and k.id_sub_skpd=%d
						order by k.kode_sub_giat ASC
					", $input['tahun_anggaran'], $unit['id_skpd'], $unit['id_skpd']), ARRAY_A);

					$total_pagu_unit = 0;
					$total_simda_unit = 0;
					$total_rak_simda_unit = 0;
					$realisasi_unit = 0;
					$realisasi_fisik_unit = array();
					$realisasi_fisik_rata = 0;
					$capaian_arr = array();
					$capaian_rata = 0;

					foreach ($subkeg as $kk => $sub) {
						if($sumber_pagu == 1){
							$total_pagu = $sub['pagu'];
							$total_pagu_unit += $total_pagu;
						}
						$total_simda_unit += isset($sub['pagu_simda']) ? $sub['pagu_simda'] : 0;
						$realisasi = $this->get_realisasi_local(array(
							'id_skpd' => $sub['id_skpd'],
							'kode_sbl' => $sub['kode_sbl'],
							'bulan' => $bulan,
							'tahun_anggaran' => $input['tahun_anggaran'] 
						));
						$realisasi_unit += $realisasi['realisasi_anggaran'];
						$total_rak_simda_unit += $realisasi['rak'];
						$realisasi_fisik_unit[] = $sub['realisasi_fisik'];
					}

					if($total_simda_unit != 0){
						$capaian_rata = $this->pembulatan($realisasi_unit/$total_simda_unit*100);
					}
					if(array_sum($realisasi_fisik_unit) != 0){
						$realisasi_fisik_rata = array_sum($realisasi_fisik_unit)/count($realisasi_fisik_unit);
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
				        <td class="atas kanan bawah text_tengah">'.$capaian_rata.'</td>
				        <td class="atas kanan bawah text_tengah">'.number_format($total_rak_simda_unit,0,",",".").'</td>
				        <td class="atas kanan bawah text_tengah">'.number_format($realisasi_fisik_rata,0,",",".").'</td>
				    </tr>
			    	';

			    	$total_pagu_pemkab +=$total_pagu_unit;
			    	$total_simda_pemkab +=$total_simda_unit;
			    	$realisasi_pemkab +=$realisasi_unit;
			    	$total_rak_simda_pemkab +=$total_rak_simda_unit;
				}

		    	$subunits = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd from data_unit where active=1 and tahun_anggaran=".$input['tahun_anggaran']." and is_skpd=0 and id_unit=".$unit["id_skpd"]." order by nama_skpd ASC", ARRAY_A);
		    	if(!empty($subunits)){
		    		foreach ($subunits as $key => $subunit) {
		    			$nama_page = 'RFK '.$subunit['nama_skpd'].' '.$subunit['kode_skpd'].' | '.$input['tahun_anggaran'];
						$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
		    			$body.='
					    	<tr>
						    	<td class="atas kanan bawah kiri text_tengah" colspan="5">'.$subunit['kode_skpd'].'</td>
						        <td class="atas kanan bawah text_kiri"><span style="margin-left:10px"><a href="'.get_permalink($custom_post) . '?key=' . $this->gen_key().'" target="_blank">'.$subunit['nama_skpd'].'</a></span></td>
						        <td class="atas kanan bawah text_tengah"></td>
						        <td class="atas kanan bawah text_tengah"></td>
						        <td class="atas kanan bawah text_tengah"></td>
						        <td class="atas kanan bawah text_tengah"></td>
						        <td class="atas kanan bawah text_tengah"></td>
						        <td class="atas kanan bawah text_tengah"></td>
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
			        <td class="kanan bawah text_tengah text_blok">'.number_format($total_rak_simda_pemkab,0,",",".").'</td>
			        <td class="kanan bawah text_blok total-realisasi-fisik text_tengah"></td>
			    </tr>
		    </tbody>
		</table>
	</div>';
	
	echo $body;
