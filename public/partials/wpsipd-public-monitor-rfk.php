<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

$page_close = '0';
if(!empty($_GET) && !empty($_GET['page_close'])){
    $page_close = $_GET['page_close'];
}

$sumber_pagu = '1';
if(!empty($_GET) && !empty($_GET['sumber_pagu'])){
    $sumber_pagu = $_GET['sumber_pagu'];
}

$tahun_asli = date('Y');
$bulan_asli = date('m');
if(!empty($_GET) && !empty($_GET['bulan'])){
    $bulan = $_GET['bulan'];
}else{
	$bulan = $bulan_asli;
}

$nama_bulan = $this->get_bulan($bulan);

// secara default pada bulan dan tahun anggaran yg akan datang tidak bisa membuka halaman RFK. kecuali jika ada parameter debug
if(empty($_GET) || empty($_GET['debug'])){
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

	$start_rpjmd = 2018;
	if(!empty($pengaturan)){
		$start_rpjmd = $pengaturan[0]['awal_rpjmd'];
	}
	$urut = $input['tahun_anggaran']-$start_rpjmd;
}

$current_user = wp_get_current_user();

foreach ($units as $k => $unit): 
	$kd_unit_simda = explode('.', get_option('_crb_unit_'.$unit['id_skpd']));

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
				and k.id_sub_skpd=%d
			order by kode_sub_giat ASC
		", $bulan, $input['tahun_anggaran'], $unit['id_skpd']), ARRAY_A);
	}
	// echo $wpdb->last_query.'<br>';

	$data_all = array(
		'jml_sub_keg' => 0,
		'total' => 0,
		'total_simda' => 0,
		'realisasi' => 0,
		'total_rak_simda' => 0,
		'deviasi' => 0,
		'data' => array()
	);
	foreach ($subkeg as $kk => $sub) {
		if(empty($kd_unit_simda) || empty($kd_unit_simda[3])){
			continue;
		}
		$_kd_urusan = $kd_unit_simda[0];
		$_kd_bidang = $kd_unit_simda[1];
		$kd_unit = $kd_unit_simda[2];
		$kd_sub_unit = $kd_unit_simda[3];

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
		foreach ($this->simda->custom_mapping as $c_map_k => $c_map_v) {
			if(
				$unit['kode_skpd'] == $c_map_v['sipd']['kode_skpd']
				&& $sub['kode_sub_giat'] == $c_map_v['sipd']['kode_sub_keg']
			){
				$kd_unit_simda_map = explode('.', $c_map_v['simda']['kode_skpd']);
				$_kd_urusan = $kd_unit_simda_map[0];
				$_kd_bidang = $kd_unit_simda_map[1];
				$kd_unit = $kd_unit_simda_map[2];
				$kd_sub_unit = $kd_unit_simda_map[3];
				$kd_keg_simda = explode('.', $c_map_v['simda']['kode_sub_keg']);
				$kd_urusan = $kd_keg_simda[0];
				$kd_bidang = $kd_keg_simda[1];
				$kd_prog = $kd_keg_simda[2];
				$kd_keg = $kd_keg_simda[3];
			}
		}

        $id_prog = $kd_urusan.$this->simda->CekNull($kd_bidang);
		$total_pagu = 0;
		$debug_pagu = '';
		if($sumber_pagu == 1){
			$total_pagu = $sub['pagu'];
			$total_rka = $wpdb->get_row($wpdb->prepare('
				select 
					sum(rincian) as total
				from data_rka
				where tahun_anggaran=%d
					and active=1
					and kode_sbl=%s',
			$input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);
			$warning = '';
			if($sub['pagu'] != $total_rka['total']){
				$warning = "style='background: #ff00002e;'";
			}
			$debug_pagu = ' <span class="detail_simda hide-excel" '.$warning.'>'.$sub['pagu'].'=='.$total_rka['total'].'</span>';
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
		if($total_simda == 0){
			$total_rak_simda = 0;
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
				'total_simda' => 0,
				'realisasi' => 0,
				'deviasi' => 0,
				'jml_sub_keg' => 0,
				'total_rak_simda' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])){
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
				'nama'	=> $sub['nama_bidang_urusan'],
				'total' => 0,
				'total_simda' => 0,
				'realisasi' => 0,
				'deviasi' => 0,
				'jml_sub_keg' => 0,
				'total_rak_simda' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']])){
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']] = array(
				'nama'	=> $sub['nama_program'],
				'total' => 0,
				'total_simda' => 0,
				'realisasi' => 0,
				'deviasi' => 0,
				'jml_sub_keg' => 0,
				'total_rak_simda' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']])){
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']] = array(
				'nama'	=> $sub['nama_giat'],
				'total' => 0,
				'total_simda' => 0,
				'realisasi' => 0,
				'deviasi' => 0,
				'jml_sub_keg' => 0,
				'total_rak_simda' => 0,
				'data'	=> array()
			);
		}
		if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']])){
			$nama = explode(' ', $sub['nama_sub_giat']);
			unset($nama[0]);
			$detail_simda = array(
				'kd_urusan' => $_kd_urusan,
				'kd_bidang' => $_kd_bidang,
				'kd_unit' => $kd_unit,
				'kd_sub' => $kd_sub_unit,
				'kd_prog' => $kd_prog,
				'id_prog' => $id_prog,
				'kd_keg' => $kd_keg
			);
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']] = array(
				'nama'	=> implode(' ', $nama).$debug_pagu.'<span class="detail_simda hide-excel">'.json_encode($detail_simda).'</span><span class="badge badge-danger simpan-per-sub-keg hide-excel">SIMPAN</span>',
				'total' => 0,
				'total_simda' => 0,
				'realisasi' => 0,
				'deviasi' => 0,
				'jml_sub_keg' => 0,
				'total_rak_simda' => 0,
				'data'	=> $sub
			);

			$data_all['jml_sub_keg']++;
			$data_all['data'][$sub['kode_urusan']]['jml_sub_keg']++;
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['jml_sub_keg']++;
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['jml_sub_keg']++;
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['jml_sub_keg']++;
			$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['jml_sub_keg']++;
		}

		$deviasi = 0;
		if(!empty($total_simda) && !empty($total_rak_simda) && !empty($realisasi)){
			$capaian_realisasi = ($realisasi/$total_simda)*100;
			$capaian_rak = ($total_rak_simda/$total_simda)*100;
			$deviasi = (($capaian_rak-$capaian_realisasi)/$capaian_rak)*100;
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

		$data_all['total_simda'] += $total_simda;
		$data_all['data'][$sub['kode_urusan']]['total_simda'] += $total_simda;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_simda'] += $total_simda;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_simda'] += $total_simda;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_simda'] += $total_simda;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_simda'] += $total_simda;

		$data_all['total_rak_simda'] += $total_rak_simda;
		$data_all['data'][$sub['kode_urusan']]['total_rak_simda'] += $total_rak_simda;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_rak_simda'] += $total_rak_simda;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_rak_simda'] += $total_rak_simda;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_rak_simda'] += $total_rak_simda;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_rak_simda'] += $total_rak_simda;

		$data_all['deviasi'] += $deviasi;
		$data_all['data'][$sub['kode_urusan']]['deviasi'] += $deviasi;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['deviasi'] += $deviasi;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['deviasi'] += $deviasi;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['deviasi'] += $deviasi;
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['deviasi'] += $deviasi;
	}
	// print_r($data_all); die();

	$data_input = array();
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
		        <td class="kanan bawah text_blok nama_urusan" colspan="12">'.$urusan['nama'].'</td>
		    </tr>
		';
		foreach ($urusan['data'] as $kd_bidang => $bidang) {
			$kd_bidang = explode('.', $kd_bidang);
			$kd_bidang = $kd_bidang[count($kd_bidang)-1];
			$capaian = 0;
			if(!empty($bidang['total_simda'])){
				$capaian = ($bidang['realisasi']/$bidang['total_simda'])*100;
			}
			$capaian_rak = 0;
			if(!empty($bidang['total_simda'])){
				$capaian_rak = ($bidang['total_rak_simda']/$bidang['total_simda'])*100;
			}
			$deviasi_bidang = 0;
			if(!empty($capaian_rak)){
				$deviasi_bidang = (($capaian_rak-$capaian)/$capaian_rak)*100;
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
		            <td class="kanan bawah text_kanan text_blok">'.number_format($bidang['total_simda'],0,",",".").'</td>
		            <td class="kanan bawah text_kanan text_blok">'.number_format($bidang['realisasi'],0,",",".").'</td>
		            <td class="kanan bawah text_blok text_tengah">'.$this->pembulatan($capaian).'</td>
		            <td class="kanan bawah text_tengah text_blok">'.$this->pembulatan($capaian_rak).'</td>
		            <td class="kanan bawah text_tengah text_blok">'.$this->pembulatan($deviasi_bidang).'</td>
		            <td class="kanan bawah text_blok bidang-realisasi-fisik text_tengah"></td>
		            <td class="kanan bawah text_blok bidang-nilai-realisasi-fisik text_kanan"></td>
		        	<td class="kanan bawah text_kanan text_blok" colspan="3"></td>
		        </tr>
			';
			foreach ($bidang['data'] as $kd_program => $program) {
				$kd_program = explode('.', $kd_program);
				$kd_program = $kd_program[count($kd_program)-1];
				$capaian = 0;
				if(!empty($program['total_simda'])){
					$capaian = ($program['realisasi']/$program['total_simda'])*100;
				}
				$capaian_rak = 0;
				if(!empty($program['total_simda'])){
					$capaian_rak = ($program['total_rak_simda']/$program['total_simda'])*100;
				}
				$deviasi_program = 0;
				if(!empty($capaian_rak)){
					$deviasi_program = (($capaian_rak-$capaian)/$capaian_rak)*100;
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
			            <td class="kanan bawah text_kanan text_blok">'.number_format($program['total_simda'],0,",",".").'</td>
			            <td class="kanan bawah text_kanan text_blok">'.number_format($program['realisasi'],0,",",".").'</td>
			            <td class="kanan bawah text_blok text_tengah">'.$this->pembulatan($capaian).'</td>
			            <td class="kanan bawah text_tengah text_blok">'.$this->pembulatan($capaian_rak).'</td>
			            <td class="kanan bawah text_tengah text_blok">'.$this->pembulatan($deviasi_program).'</td>
			            <td class="kanan bawah text_blok program-realisasi-fisik text_tengah"></td>
			            <td class="kanan bawah text_blok program-nilai-realisasi-fisik text_kanan"></td>
		        		<td class="kanan bawah text_kanan text_blok" colspan="3"></td>
			        </tr>
				';
				foreach ($program['data'] as $kd_giat1 => $giat) {
					$kd_giat = explode('.', $kd_giat1);
					$kd_giat = $kd_giat[count($kd_giat)-2].'.'.$kd_giat[count($kd_giat)-1];
					$capaian = 0;
					if(!empty($giat['total_simda'])){
						$capaian = ($giat['realisasi']/$giat['total_simda'])*100;
					}
					$capaian_rak = 0;
					if(!empty($giat['total_simda'])){
						$capaian_rak = ($giat['total_rak_simda']/$giat['total_simda'])*100;
					}
					$deviasi_kegiatan = 0;
					if(!empty($capaian_rak)){
						$deviasi_kegiatan = (($capaian_rak-$capaian)/$capaian_rak)*100;
					}
					$nama_page = $input['tahun_anggaran'] . ' | ' . $unit['kode_skpd'] . ' | ' . $kd_giat1 . ' | ' . $giat['nama'];
					$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
					$link_kegiatan = $this->get_link_post($custom_post);
					$body .= '
				        <tr class="kegiatan" data-kode="'.$kd_urusan.'.'.$kd_bidang.'.'.$kd_program.'.'.$kd_giat.'">
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">'.$kd_urusan.'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:00;" width="5">'.$kd_bidang.'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:000;" width="5">'.$kd_program.'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">'.$kd_giat.'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle;" width="5">&nbsp;</td>
				            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;"><a href="'.$link_kegiatan.'" target="_blank">'.$giat['nama'].'</a></td>
				            <td style="border:.5pt solid #000; vertical-align:middle; text-align:right; font-weight:bold;">'.number_format($giat['total'],0,",",".").'</td>
				            <td style="border:.5pt solid #000; vertical-align:middle; text-align:right; font-weight:bold;">'.number_format($giat['total_simda'],0,",",".").'</td>
				            <td style="border:.5pt solid #000; vertical-align:middle; text-align:right; font-weight:bold;">'.number_format($giat['realisasi'],0,",",".").'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;">'.$this->pembulatan($capaian).'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;">'.$this->pembulatan($capaian_rak).'</td>
				            <td class="text_tengah" style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;">'.$this->pembulatan($deviasi_kegiatan).'</td>
				            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" class="kegiatan-realisasi-fisik text_tengah"></td>
				            <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" class="kegiatan-nilai-realisasi-fisik text_kanan"></td>
		        			<td class="kanan bawah text_kanan text_blok" colspan="3"></td>
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
						if(!empty($sub_giat['total_simda'])){
							$capaian = ($sub_giat['realisasi']/$sub_giat['total_simda'])*100;
						}
						$capaian_rak = 0;
						if(!empty($sub_giat['total_simda'])){
							$capaian_rak = ($sub_giat['total_rak_simda']/$sub_giat['total_simda'])*100;
						}
						$deviasi_sub_kegiatan = 0;
						if(!empty($capaian_rak)){
							$deviasi_sub_kegiatan = (($capaian_rak-$capaian)/$capaian_rak)*100;
						}
						$realisasi_fisik = 0;
						if(!empty($sub_giat['data']['realisasi_fisik'])){
							$realisasi_fisik = $sub_giat['data']['realisasi_fisik'];
						}
						if(empty($sub_giat['total_simda'])){
							$realisasi_fisik = '';
						}
						$edit_fisik = 'contenteditable="true"';
						$edit_masalah = 'contenteditable="true"';
						$edit_catatan = '';
						if(in_array("mitra_bappeda", $current_user->roles)){
							$edit_fisik = '';
							$edit_masalah = '';
							$edit_catatan = '';
						}else if(current_user_can('administrator')){
							$edit_fisik = '';
							$edit_masalah = '';
							$edit_catatan = 'contenteditable="true"';
						}else if(empty($sub_giat['total_simda'])){
							$edit_fisik = '';
						}
						$data_input[$sub_giat['data']['kode_sbl']] = array(
							'realisasi_fisik' => $realisasi_fisik,
							'permasalahan' => $sub_giat['data']['permasalahan'],
							'catatan_verifikator' => $sub_giat['data']['catatan_verifikator']
						);
						$body .= '
					        <tr data-kode="'.$kd_sub_giat1.'" data-kdsbl="'.$sub_giat['data']['kode_sbl'].'" data-idskpd="'.$sub_giat['data']['id_sub_skpd'].'" data-pagu="'.$sub_giat['total'].'">
					            <td class="kiri kanan bawah">'.$kd_urusan.'</td>
					            <td class="kanan bawah">'.$kd_bidang.'</td>
					            <td class="kanan bawah">'.$kd_program.'</td>
					            <td class="kanan bawah">'.$kd_giat.'</td>
					            <td class="kanan bawah">'.$kd_sub_giat.'</td>
					            <td class="kanan bawah nama_sub_giat">'.$sub_giat['nama'].'</td>
					            <td class="kanan bawah text_kanan">'.number_format($sub_giat['total'],0,",",".").'</td>
					            <td class="kanan bawah text_kanan total_simda" data-pagu="'.$sub_giat['total_simda'].'">'.number_format($sub_giat['total_simda'],0,",",".").'</td>
					            <td class="kanan bawah text_kanan">'.number_format($sub_giat['realisasi'],0,",",".").'</td>
					            <td class="kanan bawah text_tengah">'.$this->pembulatan($capaian).'</td>
					            <td class="kanan bawah text_tengah">'.$this->pembulatan($capaian_rak).'</td>
					            <td class="kanan bawah text_tengah">'.$this->pembulatan($deviasi_sub_kegiatan).'</td>
					            <td class="kanan bawah realisasi-fisik text_tengah" '.$edit_fisik.'>'.$realisasi_fisik.'</td>
					            <td class="kanan bawah nilai-realisasi-fisik text_kanan"></td>
					            <td class="kanan bawah">'.implode(',<br>', $sd_sub).'</td>
					            <td class="kanan bawah permasalahan" '.$edit_masalah.'>'.$sub_giat['data']['permasalahan'].'</td>
					            <td class="kanan bawah catatan_verifikator" '.$edit_catatan.'>'.$sub_giat['data']['catatan_verifikator'].'</td>
					        </tr>
						';
					}
				}
			}
		}
	}
	$capaian_total = 0;
	if(!empty($data_all['total_simda'])){
		$capaian_total = ($data_all['realisasi']/$data_all['total_simda'])*100;
	}
	$capaian_rak = 0;
	if(!empty($data_all['total_simda'])){
		$capaian_rak = ($data_all['total_rak_simda']/$data_all['total_simda'])*100;
	}
	$deviasi_pemkab=0;
	if(!empty($capaian_rak)){
		$deviasi_pemkab = (($capaian_rak-$capaian_total)/$capaian_rak)*100;
	}

	$catatan_ka_adbang = $wpdb->get_row('
		SELECT 
			catatan_ka_adbang 
		from data_catatan_rfk_unit 
		where bulan='.$bulan.' 
			and id_skpd='.$unit['id_skpd'].' 
			and tahun_anggaran='.$input['tahun_anggaran'], ARRAY_A
	);
	if(!empty($catatan_ka_adbang)){
		$catatan_ka_adbang = $catatan_ka_adbang['catatan_ka_adbang'];
	}

	echo '
	<style>
		.detail_simda, .simpan-per-sub-keg { display: none; }
		.simpan-per-sub-keg {
		    font-size: 10px;
		    margin-left: 10px;
		    cursor: pointer;
		}
		.tr-belum-save {
			background: #ffbc0073;
		}
	</style>
	<input type="hidden" value="'.get_option( '_crb_api_key_extension' ).'" id="api_key">
	<input type="hidden" value="'.$input['tahun_anggaran'].'" id="tahun_anggaran">
	<input type="hidden" value="'.$unit['id_skpd'].'" id="id_skpd">
	<div id="cetak" title="Laporan RFK '.$input['tahun_anggaran'].'" style="padding: 5px;">
		<h4 style="text-align: center; margin: 0; font-weight: bold;">Realisasi Fisik dan Keuangan (RFK)<br>'.$unit['kode_skpd'].'&nbsp;'.$unit['nama_skpd'].'<br>Bulan '.$nama_bulan.' Tahun '.$input['tahun_anggaran'].'</h4>
		<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word; font-size: 70%; border: 0;">
		    <thead>
		    	<tr>
		            <th style="padding: 0; border: 0; width:30px"></th>
		            <th style="padding: 0; border: 0; width:30px"></th>
		            <th style="padding: 0; border: 0; width:30px"></th>
		            <th style="padding: 0; border: 0; width:40px"></th>
		            <th style="padding: 0; border: 0; width:30px"></th>
		            <th style="padding: 0; border: 0; min-width: 300px;"></th>
		            <th style="padding: 0; border: 0; width:140px"></th>
		            <th style="padding: 0; border: 0; width:140px"></th>
		            <th style="padding: 0; border: 0; width:140px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:140px" class="thead-nilai-fisik"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:120px"></th>
		            <th style="padding: 0; border: 0; width:100px"></th>
		        </tr>
			    <tr>
			        <td colspan="16" style="vertical-align:middle; font-weight:bold; border: 0; font-size: 13px;" class="nama_skpd">
			            Unit Organisasi : '.$unit_induk[0]['kode_skpd'].'&nbsp;'.$unit_induk[0]['nama_skpd'].'<br/>
			            Sub Unit Organisasi : '.$unit['kode_skpd'].'&nbsp;'.$unit['nama_skpd'].'
			        </td>
			    </tr>
			    <tr>
			        <td class="atas kanan bawah kiri text_tengah text_blok" colspan="5">Kode</td>
			        <td class="atas kanan bawah text_tengah text_blok">Urusan/ Bidang Urusan Pemerintahan Daerah Dan Program/ Kegiatan</td>
			        <td class="atas kanan bawah text_tengah text_blok">RKA SIPD (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">DPA SIMDA (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">Realisasi Keuangan (Rp.)</td>
			        <td class="atas kanan bawah text_tengah text_blok">Capaian ( % )</td>
			        <td class="atas kanan bawah text_tengah text_blok">RAK SIMDA ( % )</td>
			        <td class="atas kanan bawah text_tengah text_blok">Deviasi ( % )</td>
			        <td class="atas kanan bawah text_tengah text_blok">Realisasi Fisik ( % )</td>
			        <td class="atas kanan bawah text_tengah text_blok thead-nilai-fisik">Nilai Realisasi Fisik ( Rp )</td>
			        <td class="atas kanan bawah text_tengah text_blok">Sumber Dana</td>
			        <td class="atas kanan bawah text_tengah text_blok">Keterangan / Permasalahan</td>
			        <td class="atas kanan bawah text_tengah text_blok">Catatan Verifikator</td>
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
			        <td class="atas kanan bawah text_tengah text_blok">9</td>
			        <td class="atas kanan bawah text_tengah text_blok">10 = (9 / 8) * 100</td>
			        <td class="atas kanan bawah text_tengah text_blok">11</td>
			        <td class="atas kanan bawah text_tengah text_blok">12 = ((11-10)/11) * 100</td>
			        <td class="atas kanan bawah text_tengah text_blok">13 = (14 / 8) * 100</td>
			        <td class="atas kanan bawah text_tengah text_blok thead-nilai-fisik">14 = (8 * 13) / 100</td>
			        <td class="atas kanan bawah text_tengah text_blok">15</td>
			        <td class="atas kanan bawah text_tengah text_blok">16</td>
			        <td class="atas kanan bawah text_tengah text_blok">17</td>
			    </tr>
		    </thead>
		    <tbody>
		        '.$body.'
				<tr>
			        <td class="kiri kanan bawah text_blok text_kanan" colspan="6">TOTAL dan CATATAN KESIMPULAN KABAG ADBANG</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($data_all['total'],0,",",".").'</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($data_all['total_simda'],0,",",".").'</td>
			        <td class="kanan bawah text_kanan text_blok">'.number_format($data_all['realisasi'],0,",",".").'</td>
			        <td class="kanan bawah text_tengah text_blok">'.$this->pembulatan($capaian_total).'</td>
			        <td class="kanan bawah text_tengah text_blok" data="'.$data_all['total_rak_simda'].'">'.$this->pembulatan($capaian_rak).'</td>
			        <td class="kanan bawah text_tengah text_blok">'.$this->pembulatan($deviasi_pemkab).'</td>
			        <td class="kanan bawah text_blok total-realisasi-fisik text_tengah"></td>
			        <td class="kanan bawah text_blok total-nilai-realisasi-fisik text_kanan"></td>
			        <td class="kanan bawah text_kiri text_blok" colspan="3">'.$catatan_ka_adbang.'</td>
			    </tr>
		    </tbody>
		</table>
		<div style="page-break-after:always;"></div>
</div>';
endforeach; 

$reset_rfk = '';
if(!current_user_can('administrator')){
	$reset_rfk = '<button style="margin-left: 20px;" class="button button-default" id="reset-rfk">Reset RFK Bulan Sebelumnya</button>';
}else{
	$reset_rfk = '<button style="margin-left: 20px;" class="button button-default" id="reset-verifikator-rfk">Reset Catatan Verifikator RFK Bulan Sebelumnya</button>';
}
?>

<div class="hide-print" id="catatan_dokumentasi" style="max-width: 1200px; margin: auto;">
	<h4 style="margin: 30px 0 10px; font-weight: bold;">Catatan Dokumentasi:</h4>
	<ul>
		<li>Laporan RFK secara default menampilkan data pada bulan berjalan</li>
		<li>Tombol <b>DOWNLOAD EXCEL</b> digunakan untuk mendownload tabel laporan RFK ke format excel</li>
		<li>Pilihan <b>Bulan Realisasi</b> digunakan untuk menampilkan laporan RFK sesuai bulan yang dipilih</li>
		<li>Tombol <b>Simpan Data</b> digunakan untuk menyimpan data yang sudah diinput atau diedit oleh user SKPD dan user verfikator</li>
		<li>Tombol <b>Reset RFK bulan sebelumnya</b> digunakan untuk mengupdate data input sesuai dengan data di bulan sebelumnya. Fitur ini mempermudah user untuk menginput data pada awal bulan, agar tidak perlu menginput satu per satu data mulai dari awal</li>
		<li><b>CATATAN KESIMPULAN KABAG ADBANG</b> adalah catatan yang diisi oleh KABAG ADBANG, berisi kesimpulan dari catatan verfikator</li>
		<li>Tombol <b>SIMPAN</b> berwarna merah pada sub kegiatan akan muncul, jika ada data yang belum disimpan oleh user SKPD ataupun user verifikator</li>
		<li style="display: none;">Perhitungan <b>total realisasi fisik</b> adalah akumulasi realiasi fisik seluruh sub kegiatan dibagi jumlah sub kegiatan yang ada nilai pagu simdanya</li>
		<li style="display: none;">Untuk menampilkan detail akumulasi realisasi fisik per kegiatan, program dan bidang urusan klik pada kotak checkbox <b>Tampilkan Detail Realisasi Fisik</b>. Secara default akumulasi tidak ditampilkan agar tidak membuat bingung user dalam memahami nilai total realisasi fisik.</li>
	</ul>
</div>
<script type="text/javascript">
	var data_input = <?php echo json_encode($data_input); ?>;
	run_download_excel();
    function generate_total(){
    	window.total_parent = {};
    	window.total_simda = 0;
    	window.total_nilai = 0;
    	window.total = 0;
    	window.total_s = 0;
    	jQuery('.realisasi-fisik').map(function(i, b){
    		var tr = jQuery(b).closest('tr');
    		var pagu_simda = +tr.find('.total_simda').attr('data-pagu');
    		var kode_sub = tr.attr('data-kode').split('.');
    		var kode_kegiatan = kode_sub[0]+'.'+kode_sub[1]+'.'+kode_sub[2]+'.'+kode_sub[3]+'.'+kode_sub[4];
    		var kode_program = kode_sub[0]+'.'+kode_sub[1]+'.'+kode_sub[2];
    		var kode_bidang = kode_sub[0]+'.'+kode_sub[1];
    		var val = jQuery(b).text();
    		var nilai_fisik = 0;
    		if(!isNaN(val) && +val >= 0 && +val <= 100){
    			nilai_fisik = (val*pagu_simda)/100;
    			if(typeof total_parent[kode_bidang] == 'undefined'){
    				total_parent[kode_bidang] = {
    					total_simda : 0,
    					total_bidang : 0,
    					total_nilai_bidang : 0,
	    				total_bidang_s : 0
    				}
    			}
    			if(typeof total_parent[kode_program] == 'undefined'){
    				total_parent[kode_program] = {
				    	total_simda : 0,
				    	total_program : 0,
				    	total_nilai_program : 0,
				    	total_program_s : 0
    				}
    			}
    			if(typeof total_parent[kode_kegiatan] == 'undefined'){
    				total_parent[kode_kegiatan] = {
				    	total_simda : 0,
				    	total_kegiatan : 0,
				    	total_nilai_kegiatan : 0,
				    	total_kegiatan_s : 0
    				}
    			}
    			if(pagu_simda == 0){ return; }
    			total += +val;
    			total_nilai += nilai_fisik;
    			total_simda += pagu_simda;
    			total_s++;
    			total_parent[kode_bidang].total_simda += pagu_simda;
    			total_parent[kode_bidang].total_bidang += +val;
    			total_parent[kode_bidang].total_nilai_bidang += nilai_fisik;
    			total_parent[kode_bidang].total_bidang_s++;
    			total_parent[kode_program].total_simda += pagu_simda;
    			total_parent[kode_program].total_program += +val;
    			total_parent[kode_program].total_nilai_program += nilai_fisik;
    			total_parent[kode_program].total_program_s++;
    			total_parent[kode_kegiatan].total_simda += pagu_simda;
    			total_parent[kode_kegiatan].total_kegiatan += +val;
    			total_parent[kode_kegiatan].total_nilai_kegiatan += nilai_fisik;
    			total_parent[kode_kegiatan].total_kegiatan_s++;
    		}
    		tr.find('.nilai-realisasi-fisik').text(formatRupiah(nilai_fisik));
    	});
    	for(var i in total_parent){
    		if(typeof(total_parent[i].total_bidang) != 'undefined'){
    			var total_bidang = 0;
    			if(total_parent[i].total_bidang_s != 0){
	    			// total_bidang = Math.round((total_parent[i].total_bidang/total_parent[i].total_bidang_s)*100)/100;
	    			total_bidang = Math.round((total_parent[i].total_nilai_bidang/total_parent[i].total_simda)*10000)/100;
    			}
	    		jQuery('tr[data-kode="'+i+'"]').find('.bidang-realisasi-fisik').html('<span>'+total_bidang+'</span>');
	    		jQuery('tr[data-kode="'+i+'"]').find('.bidang-nilai-realisasi-fisik').html('<span>'+formatRupiah(Math.round(total_parent[i].total_nilai_bidang))+'</span>');
	    	}else if(typeof(total_parent[i].total_program) != 'undefined'){
    			var total_program = 0;
    			if(total_parent[i].total_program_s != 0){
	    			// total_program = Math.round((total_parent[i].total_program/total_parent[i].total_program_s)*100)/100;
	    			total_program = Math.round((total_parent[i].total_nilai_program/total_parent[i].total_simda)*10000)/100;
	    		}
	    		jQuery('tr[data-kode="'+i+'"]').find('.program-realisasi-fisik').html('<span>'+total_program+'</span>');
	    		jQuery('tr[data-kode="'+i+'"]').find('.program-nilai-realisasi-fisik').html('<span>'+formatRupiah(Math.round(total_parent[i].total_nilai_program))+'</span>');
	    	}else if(typeof(total_parent[i].total_kegiatan) != 'undefined'){
    			var total_kegiatan = 0;
    			if(total_parent[i].total_kegiatan_s != 0){
	    			// total_kegiatan = Math.round((total_parent[i].total_kegiatan/total_parent[i].total_kegiatan_s)*100)/100;
	    			total_kegiatan = Math.round((total_parent[i].total_nilai_kegiatan/total_parent[i].total_simda)*10000)/100;
	    		}
	    		jQuery('tr[data-kode="'+i+'"]').find('.kegiatan-realisasi-fisik').html('<span>'+total_kegiatan+'</span>');
	    		jQuery('tr[data-kode="'+i+'"]').find('.kegiatan-nilai-realisasi-fisik').html('<span>'+formatRupiah(Math.round(total_parent[i].total_nilai_kegiatan))+'</span>');
	    	}
    	}
    	var end = 0;
    	if(total_s != 0){
    		// end = Math.round((total/total_s)*100)/100;
    		end = Math.round((total_nilai/total_simda)*10000)/100;
    	}
    	jQuery('.total-realisasi-fisik').text(end);
    	jQuery('.total-nilai-realisasi-fisik').text(formatRupiah(Math.round(total_nilai)));
    }
	var _url_asli = window.location.href;
    var url = new URL(_url_asli);
    _url_asli = changeUrl({ url: _url_asli, key: 'key', value: '<?php echo $this->gen_key(); ?>' });
    _url_asli = changeUrl({ url: _url_asli, key: 'page_id', value: url.searchParams.get('page_id') });

<?php
	$all_bulan = array();
	$all_bulan[1] = '<option value="1">Januari</option>';
	$all_bulan[2] = '<option value="2">Februari</option>';
	$all_bulan[3] = '<option value="3">Maret</option>';
	$all_bulan[4] = '<option value="4">April</option>';
	$all_bulan[5] = '<option value="5">Mei</option>';
	$all_bulan[6] = '<option value="6">Juni</option>';
	$all_bulan[7] = '<option value="7">Juli</option>';
	$all_bulan[8] = '<option value="8">Agustus</option>';
	$all_bulan[9] = '<option value="9">September</option>';
	$all_bulan[10] = '<option value="10">Oktober</option>';
	$all_bulan[11] = '<option value="11">November</option>';
	$all_bulan[12] = '<option value="12">Desember</option>';
	$opsi_bulan = '';
	for($i=1; $i<=$bulan_asli; $i++){
		$opsi_bulan .= $all_bulan[$i];
	}
?>
	function tampil_detail_fisik(){
		if(jQuery('#tampil-detail-fisik').is(':checked')){
    		jQuery('.kegiatan-realisasi-fisik span').show();
    		jQuery('.program-realisasi-fisik span').show();
    		jQuery('.bidang-realisasi-fisik span').show();
    		jQuery('.kegiatan-nilai-realisasi-fisik span').show();
    		jQuery('.program-nilai-realisasi-fisik span').show();
    		jQuery('.bidang-nilai-realisasi-fisik span').show();
    	}else{
    		jQuery('.kegiatan-realisasi-fisik span').hide();
    		jQuery('.program-realisasi-fisik span').hide();
    		jQuery('.bidang-realisasi-fisik span').hide();
    		jQuery('.kegiatan-nilai-realisasi-fisik span').hide();
    		jQuery('.program-nilai-realisasi-fisik span').hide();
    		jQuery('.bidang-nilai-realisasi-fisik span').hide();
    	}
	}

	function tampil_nilai_fisik(){
		if(jQuery('#tampil-nilai-fisik').is(':checked')){
    		jQuery('.nama_urusan').attr('colspan', 12);
    		jQuery('.nama_skpd').attr('colspan', 16);
    		jQuery('.thead-nilai-fisik').show();
    		jQuery('.total-nilai-realisasi-fisik').show();
    		jQuery('.nilai-realisasi-fisik').show();
    		jQuery('.kegiatan-nilai-realisasi-fisik').show();
    		jQuery('.program-nilai-realisasi-fisik').show();
    		jQuery('.bidang-nilai-realisasi-fisik').show();
    	}else{
    		jQuery('.nama_urusan').attr('colspan', 11);
    		jQuery('.nama_skpd').attr('colspan', 15);
    		jQuery('.thead-nilai-fisik').hide();
    		jQuery('.total-nilai-realisasi-fisik').hide();
    		jQuery('.nilai-realisasi-fisik').hide();
    		jQuery('.kegiatan-nilai-realisasi-fisik').hide();
    		jQuery('.program-nilai-realisasi-fisik').hide();
    		jQuery('.bidang-nilai-realisasi-fisik').hide();
    	}
	}

	var extend_action = ''
		+'<div style="margin-top: 20px;">'
			+'<label style="display:none;">Sumber Pagu Indikatif: '
				+'<select id="pilih_sumber_pagu" style="padding: 5px;">'
					+'<option value="1">RKA SIPD</option>'
					+'<option value="4">APBD SIMDA</option>'
					+'<option value="5">APBD Pergeseran</option>'
					+'<option value="6">APBD Perubahan</option>'
				+'</select>'
			+'</label>'
			+'<label style="margin-left: 20px;">Bulan Realisasi: '
				+'<select id="pilih_bulan" style="padding: 5px;" data-bulan-asli="<?php echo $bulan_asli; ?>">'
					+'<option value="0">-- Bulan --</option>'
					+'<?php echo $opsi_bulan; ?>'
				+'</select>'
			+'</label>'
			+'<button style="margin-left: 20px;" class="button button-primary" id="simpan-rfk">Simpan Data</button>'
			+'<?php echo $reset_rfk; ?>'
			+'<label style="margin-left: 20px;"><input type="checkbox" id="tampil-detail-fisik" checked onclick="tampil_detail_fisik();"> Tampilkan Detail Realisasi Fisik</label>'
			+'<label style="margin-left: 20px;"><input type="checkbox" id="tampil-nilai-fisik" onclick="tampil_nilai_fisik();"> Tampilkan Nilai Realisasi Fisik</label>'
		+'</div>';
	jQuery(document).ready(function(){
	    jQuery('#action-sipd').append(extend_action);
	    jQuery('#pilih_sumber_pagu').val(+<?php echo $sumber_pagu; ?>);
	    jQuery('#pilih_bulan').val(+<?php echo $bulan; ?>);
		
		setTimeout(function(){
			tampil_nilai_fisik();
		}, 1000);
	    jQuery('#pilih_sumber_pagu').on('change', function(){
	    	var val = +jQuery(this).val();
	    	if(val > 0){
	    		window.open(_url_asli+'&sumber_pagu='+val,'_blank');
	    	}
	    	jQuery('#pilih_sumber_pagu').val(+<?php echo $sumber_pagu; ?>);
	    });
	    jQuery('#pilih_bulan').on('change', function(){
	    	var val = +jQuery(this).val();
	    	if(val > 0){
	    		window.open(_url_asli+'&bulan='+val,'_blank');
	    	}
	    	jQuery('#pilih_bulan').val(+<?php echo $bulan; ?>);
	    });
	    jQuery('.realisasi-fisik').on('input', function(){
	    	generate_total();
	    	var val = jQuery(this).text();
	    	if(isNaN(+val) || +val > 100 || +val < 0){
	    		alert('Input realisasi fisik harus dalam format angka antaran 0-100!');
	    	}else{
		    	var tr = jQuery(this).closest('tr');
		    	tr.removeClass('tr-belum-save');
		    	var kd_sbl = tr.attr('data-kdsbl');
		    	if(data_input[kd_sbl].realisasi_fisik != val){
		    		tr.find('.simpan-per-sub-keg').show();
		    		tr.addClass('tr-belum-save');
		    	}else{
		    		tr.find('.simpan-per-sub-keg').hide();
		    	}
		    }
	    });
	    jQuery('.permasalahan').on('input', function(){
	    	generate_total();
	    	var val = jQuery(this).text();
	    	var tr = jQuery(this).closest('tr');
	    	tr.removeClass('tr-belum-save');
	    	var kd_sbl = tr.attr('data-kdsbl');
	    	if(data_input[kd_sbl].permasalahan != val){
	    		tr.find('.simpan-per-sub-keg').show();
	    		tr.addClass('tr-belum-save');
	    	}else{
	    		tr.find('.simpan-per-sub-keg').hide();
	    	}
	    });
	    jQuery('.catatan_verifikator').on('input', function(){
	    	generate_total();
	    	var val = jQuery(this).text();
	    	var tr = jQuery(this).closest('tr');
	    	tr.removeClass('tr-belum-save');
	    	var kd_sbl = tr.attr('data-kdsbl');
	    	if(data_input[kd_sbl].catatan_verifikator != val){
	    		tr.find('.simpan-per-sub-keg').show();
	    		tr.addClass('tr-belum-save');
	    	}else{
	    		tr.find('.simpan-per-sub-keg').hide();
	    	}
	    });
	    jQuery('.simpan-per-sub-keg').on('click', function(){
	    	var tr = jQuery(this).closest('tr');
	    	var val = tr.find('.realisasi-fisik').text();
	    	var cek = false;
			if(isNaN(+val) || +val > 100 || +val < 0){
	    		cek = tr.find('.nama_sub_giat').text();
	    	}
    		if(cek){
		    	alert('Input realisasi fisik sub kegiatan "'+cek+'" harus dalam format angka antaran 0-100! Realisasi tidak tersimpan.');
    			return;
    		}else{
	    		jQuery('#wrap-loading').show();
	    		var realisasi_fisik = val;
				var permasalahan = tr.find('.permasalahan').text();
				var catatan_verifikator = tr.find('.catatan_verifikator').text();
				var kd_sbl = tr.attr('data-kdsbl');
		    	jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
		          	type: "post",
		          	data: {
		          		"action": "simpan_rfk",
		          		"api_key": jQuery('#api_key').val(),
		          		"tahun_anggaran": jQuery('#tahun_anggaran').val(),
		          		"bulan": jQuery('#pilih_bulan').val(),
		          		"user": "<?php echo $current_user->display_name; ?>",
		          		"data": [{
		    				realisasi_fisik: realisasi_fisik,
		    				permasalahan: permasalahan,
		    				catatan_verifikator: catatan_verifikator,
		    				id_skpd: tr.attr('data-idskpd'),
		    				kode_sbl: kd_sbl,
			          		user_edit: "<?php echo $current_user->display_name; ?>"
		    			}]
		          	},
		          	dataType: "json",
		          	success: function(data){
		          		data_input[kd_sbl].realisasi_fisik = realisasi_fisik;
		          		data_input[kd_sbl].permasalahan = permasalahan;
		          		data_input[kd_sbl].catatan_verifikator = catatan_verifikator;
						tr.removeClass('tr-belum-save');
						tr.find('.simpan-per-sub-keg').hide();
						jQuery('#wrap-loading').hide();
					}
				});
		    }
	    });
	    jQuery('#reset-rfk').on('click', function(){
	    	if(confirm('Apakah anda yakin untuk reset data RFK sesuai bulan sebelumnya? Data RFK saat ini akan disamakan dengan bulan sebelumnya!')){
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
	    jQuery('#reset-verifikator-rfk').on('click', function(){
	    	if(confirm('Apakah anda yakin untuk reset catatan verifikasi RFK sesuai bulan sebelumnya? Data saat ini akan disamakan dengan bulan sebelumnya!')){
	    		jQuery('#wrap-loading').show();
	    		var id_skpd = jQuery('#id_skpd').val();
	    		jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
		          	type: "post",
		          	data: {
		          		"action": "reset_catatan_verifkator_rfk",
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
	    				tr: i,
	    				realisasi_fisik: val,
	    				permasalahan: tr.find('.permasalahan').text(),
	    				catatan_verifikator: tr.find('.catatan_verifikator').text(),
	    				id_skpd: tr.attr('data-idskpd'),
	    				kode_sbl: tr.attr('data-kdsbl'),
		          		user_edit: "<?php echo $current_user->display_name; ?>"
	    			});
	    			if(i>0 && i%20==0){
	    				r_fisik.push(r_fisik_s);
	    				r_fisik_s = [];
	    			}
	    		});
	    		if(cek){
			    	alert('Input realisasi fisik sub kegiatan "'+cek+'" harus dalam format angka antaran 0-100! Realisasi tidak tersimpan.');
	    			return;
	    		}else{
		    		jQuery('#wrap-loading').show();
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
						          		current_data.map(function(b, i){
		            						var no_tr = b.tr;
			            					var tr = jQuery('.realisasi-fisik').eq(no_tr).closest('tr');
											tr.removeClass('tr-belum-save');
											tr.find('.simpan-per-sub-keg').hide();
											var kd_sbl = b.kode_sbl;
							          		data_input[kd_sbl].realisasi_fisik = b.realisasi_fisik;
							          		data_input[kd_sbl].permasalahan = b.permasalahan;
							          		data_input[kd_sbl].catatan_verifikator = b.catatan_verifikator;
						          		});
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
	    	}
	    });
	    generate_total();

	    <?php 
	    if($page_close==1){ ?>
	    	window.close();
	    <?php } ?>
	});
</script>