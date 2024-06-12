<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

function ubah_minus($nilai){
    if($nilai < 0){
        $nilai = abs($nilai);
        return '('.number_format($nilai,0,",",".").')';
    }else{
        return number_format($nilai,0,",",".");
    }
}

$sql = $wpdb->prepare("
        select 
            *
        from data_prog_keg
        where tahun_anggaran=%d
            and active=1
        group by kode_bidang_urusan
        order by kode_bidang_urusan ASC
    ", $input['tahun_anggaran']);
$urusan_db = $wpdb->get_results($sql, ARRAY_A);
$bidang_urusan_all = array();
foreach($urusan_db as $urusan){
	$bidang_urusan_all[$urusan['id_bidang_urusan']] = $urusan;
}

$skpd_db = $wpdb->get_results($wpdb->prepare("
    select 
        0 as realisasi,
        k.id_sub_skpd,
        k.id_bidang_urusan,
        u.nama_skpd,
        u.kode_skpd,
        (sum(k.bulan_1) + sum(k.bulan_2) + sum(k.bulan_3)) as total_1,
        (sum(k.bulan_4) + sum(k.bulan_5) + sum(k.bulan_6)) as total_2,
        (sum(k.bulan_7) + sum(k.bulan_8) + sum(k.bulan_9)) as total_3,
        (sum(k.bulan_10) + sum(k.bulan_11) + sum(k.bulan_12)) as total_4,
        (sum(k.bulan_1) + sum(k.bulan_2) + sum(k.bulan_3) + sum(k.bulan_4) + sum(k.bulan_5) + sum(k.bulan_6) + sum(k.bulan_7) + sum(k.bulan_8)  + sum(k.bulan_9)  + sum(k.bulan_10) + sum(k.bulan_11) + sum(k.bulan_12)) as total
    from data_anggaran_kas k
    inner join data_unit u on k.id_sub_skpd=u.id_skpd
    	and u.tahun_anggaran=k.tahun_anggaran
    	and u.active=1
    where k.tahun_anggaran=%d
        and k.active=1
        and k.type='belanja'
    group by k.id_bidang_urusan, k.id_sub_skpd
    order by k.id_bidang_urusan, k.id_sub_skpd ASC
", $input['tahun_anggaran']), ARRAY_A);

$skpd_all = array(
    'data' => array(),
    'realisasi' => 0,
    'total_1' => 0,
    'total_2' => 0,
    'total_3' => 0,
    'total_4' => 0,
    'total_belanja' => 0,
    'total' => 0
);
foreach($skpd_db as $skpd){
	$kode_urusan = $bidang_urusan_all[$skpd['id_bidang_urusan']]['kode_urusan'];
	$kode_bidang_urusan = explode('.', $bidang_urusan_all[$skpd['id_bidang_urusan']]['kode_bidang_urusan']);
	$kode_bidang_urusan = $kode_bidang_urusan[1];

	$nama_urusan = explode(' ', $bidang_urusan_all[$skpd['id_bidang_urusan']]['nama_urusan']);
	unset($nama_urusan[0]);
	$nama_urusan = implode(' ', $nama_urusan);

	$nama_bidang_urusan = explode(' ', $bidang_urusan_all[$skpd['id_bidang_urusan']]['nama_bidang_urusan']);
	unset($nama_bidang_urusan[0]);
	$nama_bidang_urusan = implode(' ', $nama_bidang_urusan);

	if(empty($skpd_all['data'][$kode_urusan])){
		$skpd_all['data'][$kode_urusan] = array(
			'data' => array(),
		    'realisasi' => 0,
		    'nama' => $nama_urusan,
		    'kode' => $kode_urusan,
		    'total_1' => 0,
		    'total_2' => 0,
		    'total_3' => 0,
		    'total_4' => 0,
		    'total_belanja' => 0,
		    'total' => 0
		);
	}
	if(empty($skpd_all['data'][$kode_urusan]['data'][$kode_bidang_urusan])){
		$skpd_all['data'][$kode_urusan]['data'][$kode_bidang_urusan] = array(
			'data' => array(),
		    'realisasi' => 0,
		    'nama' => $nama_bidang_urusan,
		    'kode' => $kode_bidang_urusan,
		    'total_1' => 0,
		    'total_2' => 0,
		    'total_3' => 0,
		    'total_4' => 0,
		    'total_belanja' => 0,
		    'total' => 0
		);
	}

	$total_belanja = $wpdb->get_var($wpdb->prepare("
		select
			sum(pagu)
		from data_sub_keg_bl
		where tahun_anggaran=%d
			and active=1
			and id_bidang_urusan=%d
			and id_sub_skpd=%d
	", $input['tahun_anggaran'], $skpd['id_bidang_urusan'], $skpd['id_sub_skpd']));
	$skpd['total_belanja'] = $total_belanja;

	$skpd_all['data'][$kode_urusan]['data'][$kode_bidang_urusan]['data'][$skpd['id_sub_skpd']] = $skpd;

	$skpd_all['data'][$kode_urusan]['total_1'] += $skpd['total_1'];
	$skpd_all['data'][$kode_urusan]['total_2'] += $skpd['total_2'];
	$skpd_all['data'][$kode_urusan]['total_3'] += $skpd['total_3'];
	$skpd_all['data'][$kode_urusan]['total_4'] += $skpd['total_4'];
	$skpd_all['data'][$kode_urusan]['total'] += $skpd['total'];
	$skpd_all['data'][$kode_urusan]['total_belanja'] += $total_belanja;

	$skpd_all['data'][$kode_urusan]['data'][$kode_bidang_urusan]['total_1'] += $skpd['total_1'];
	$skpd_all['data'][$kode_urusan]['data'][$kode_bidang_urusan]['total_2'] += $skpd['total_2'];
	$skpd_all['data'][$kode_urusan]['data'][$kode_bidang_urusan]['total_3'] += $skpd['total_3'];
	$skpd_all['data'][$kode_urusan]['data'][$kode_bidang_urusan]['total_4'] += $skpd['total_4'];
	$skpd_all['data'][$kode_urusan]['data'][$kode_bidang_urusan]['total'] += $skpd['total'];
	$skpd_all['data'][$kode_urusan]['data'][$kode_bidang_urusan]['total_belanja'] += $total_belanja;

	$skpd_all['total_1'] += $skpd['total_1'];
	$skpd_all['total_2'] += $skpd['total_2'];
	$skpd_all['total_3'] += $skpd['total_3'];
	$skpd_all['total_4'] += $skpd['total_4'];
	$skpd_all['total'] += $skpd['total'];
	$skpd_all['total_belanja'] += $total_belanja;
}

$body_html = '';
foreach($skpd_all['data'] as $urusan){
	$warning = '';
	if($urusan['total_belanja'] != $urusan['total']){
		$warning = 'background: #ffe6e6;';
	}
	$body_html .= "
    <tr class='urusan'>
        <td class='kiri kanan bawah text_blok'>".$urusan['kode']."</td>
        <td class='kiri kanan bawah text_blok'></td>
        <td class='kiri kanan bawah text_blok'></td>
        <td class='kanan bawah text_blok'>".$urusan['nama']."</td>
        <td class='kanan bawah text_kanan text_blok' style='$warning'>".ubah_minus($urusan['total_belanja'])."</td>
        <td class='kanan bawah text_kanan text_blok total_angkas' style='$warning'>".ubah_minus($urusan['total'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($urusan['total_1'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($urusan['total_2'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($urusan['total_3'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($urusan['total_4'])."</td>
    </tr>";
    foreach($urusan['data'] as $bidang_urusan){
		$warning = '';
		if($bidang_urusan['total_belanja'] != $bidang_urusan['total']){
			$warning = 'background: #ffe6e6;';
		}
		$body_html .= "
	    <tr class='bidang'>
	        <td class='kiri kanan bawah text_blok'>".$urusan['kode']."</td>
	        <td class='kiri kanan bawah text_blok'>".$bidang_urusan['kode']."</td>
        	<td class='kiri kanan bawah text_blok'></td>
	        <td class='kanan bawah text_blok'>".$bidang_urusan['nama']."</td>
	        <td class='kanan bawah text_kanan text_blok' style='$warning'>".ubah_minus($bidang_urusan['total_belanja'])."</td>
	        <td class='kanan bawah text_kanan text_blok total_angkas' style='$warning'>".ubah_minus($bidang_urusan['total'])."</td>
	        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($bidang_urusan['total_1'])."</td>
	        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($bidang_urusan['total_2'])."</td>
	        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($bidang_urusan['total_3'])."</td>
	        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($bidang_urusan['total_4'])."</td>
	    </tr>";
	    foreach($bidang_urusan['data'] as $skpd){
			$warning = '';
			if($skpd['total_belanja'] != $skpd['total']){
				$warning = 'background: #ffe6e6;';
			}
			$body_html .= "
		    <tr class='sub_skpd' id_bidang_urusan='".$skpd['id_bidang_urusan']."' id_sub_skpd='".$skpd['id_sub_skpd']."'>
		        <td class='kiri kanan bawah'>".$urusan['kode']."</td>
		        <td class='kiri kanan bawah'>".$bidang_urusan['kode']."</td>
		        <td class='kiri kanan bawah'>".$skpd['kode_skpd']."</td>
		        <td class='kanan bawah'>".$skpd['nama_skpd']."</td>
		        <td class='kanan bawah text_kanan' style='$warning'>".ubah_minus($skpd['total_belanja'])."</td>
		        <td class='kanan bawah text_kanan total_angkas' style='$warning'>".ubah_minus($skpd['total'])."</td>
		        <td class='kanan bawah text_kanan'>".ubah_minus($skpd['total_1'])."</td>
		        <td class='kanan bawah text_kanan'>".ubah_minus($skpd['total_2'])."</td>
		        <td class='kanan bawah text_kanan'>".ubah_minus($skpd['total_3'])."</td>
		        <td class='kanan bawah text_kanan'>".ubah_minus($skpd['total_4'])."</td>
		    </tr>";
	    }
    }
}
$body_html .= "
<tr class='total'>
    <td class='kiri kanan bawah text_tengah text_blok' colspan='4'>Total</td>
    <td class='kanan bawah text_kanan text_blok'>".ubah_minus($skpd_all['total_belanja'])."</td>
    <td class='kanan bawah text_kanan text_blok total_angkas'>".ubah_minus($skpd_all['total'])."</td>
    <td class='kanan bawah text_kanan text_blok'>".ubah_minus($skpd_all['total_1'])."</td>
    <td class='kanan bawah text_kanan text_blok'>".ubah_minus($skpd_all['total_2'])."</td>
    <td class='kanan bawah text_kanan text_blok'>".ubah_minus($skpd_all['total_3'])."</td>
    <td class='kanan bawah text_kanan text_blok'>".ubah_minus($skpd_all['total_4'])."</td>
</tr>";
?>
<div id="cetak" title="Laporan APBD Per Triwulan <?php echo $input['tahun_anggaran']; ?>">
    <h4 style="text-align: center; font-size: 13px; margin: 10px auto; min-width: 450px; max-width: 550px; font-weight: bold; text-transform: uppercase;"><?php echo get_option('_crb_daerah'); ?> <br>RINGKASAN APBD YANG DIKLASIFIKASI PER URUSAN dan PER TRIWULAN<br>TAHUN ANGGARAN <?php echo $input['tahun_anggaran']; ?></h4>
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok" rowspan="2" colspan="3">Kode</td>
                <td class="atas kanan bawah text_tengah text_blok" rowspan="2">Urusan Pemerintah Daerah</td>
                <td class="atas kanan bawah text_tengah text_blok" rowspan="2">Belanja</td>
                <td class="atas kanan bawah text_tengah text_blok total_angkas" rowspan="2">Total Anggaran Kas</td>
                <td class="atas kanan bawah text_tengah text_blok" colspan="4">Triwulan</td>
            </tr>
            <tr>
                <td class="atas kanan bawah text_tengah text_blok">I</td>
                <td class="atas kanan bawah text_tengah text_blok">II</td>
                <td class="atas kanan bawah text_tengah text_blok">III</td>
                <td class="atas kanan bawah text_tengah text_blok">IV</td>
            </tr>
        </thead>
        <tbody>
            <?php 
                echo $body_html;
            ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    run_download_excel();
</script>