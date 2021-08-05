<?php
global $wpdb;
$input = shortcode_atts( array(
	'id_sumber_dana' => '',
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

$type = 'murni';
if(!empty($_GET) && !empty($_GET['type'])){
    $type = $_GET['type'];
}

$kode_sumber_dana = '';
$nama_sumber_dana = '';
if(!empty($input['id_sumber_dana'])){
	$sumber_dana = $wpdb->get_results('SELECT * from data_sumber_dana where id_dana='.$input['id_sumber_dana'], ARRAY_A);
	if(!empty($sumber_dana)){
		$kode_sumber_dana = $sumber_dana[0]['kode_dana'];
		$nama_sumber_dana = $sumber_dana[0]['nama_dana'];
	}
}
$data_sub_giat = $wpdb->get_results('
	select 
		r.*
	from data_dana_sub_keg d
		join data_sub_keg_bl r on r.kode_sbl = d.kode_sbl
			and r.tahun_anggaran = d.tahun_anggaran
			and r.active = d.active
	where d.active=1 
		and d.tahun_anggaran='.$input['tahun_anggaran'].'
		and (
			d.iddana='.$input['id_sumber_dana'].'
			or d.iddana is null
		)', ARRAY_A);
$judul_laporan = array('Laporan APBD Per Sumber Dana',$kode_sumber_dana.' '.$nama_sumber_dana,'Tahun '.$input['tahun_anggaran']);

$data_sumberdana_shorted = array(
    'data' => array(),
    'total_murni' => 0,
    'total' => 0
);

foreach ($data_sub_giat as $k =>$v) {
	$kode = explode('.', $v['kode_sbl']);
    $idskpd = $kode[1];
    $skpd = $wpdb->get_row("SELECT nama_skpd, kode_skpd from data_unit where id_skpd=$idskpd", ARRAY_A);
    if(empty($data_sumberdana_shorted['data'][$skpd['kode_skpd']])){
        $data_sumberdana_shorted['data'][$skpd['kode_skpd']] = array(
            'nama' => $skpd['nama_skpd'],
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    if(empty($data_sumberdana_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']])){
        $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']] = array(
            'nama' => $v['nama_sub_giat'],
            'total_murni' => 0,
            'total' => 0,
            'data' => $v
        );
    }
    
    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total'] += $v['pagu'];
    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['total'] += $v['pagu'];
    $data_sumberdana_shorted['total'] += $v['pagu'];

    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total_murni'] += $v['pagumurni'];
    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['total_murni'] += $v['pagumurni'];
    $data_sumberdana_shorted['total_murni'] += $v['pagumurni'];
}
ksort($data_sumberdana_shorted['data']);

$body_sumberdana = '';
foreach ($data_sumberdana_shorted['data'] as $k => $skpd) {
    $murni = '';
    $selisih = '';
    if($type == 'pergeseran'){
        $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($skpd['total_murni'],0,",",".")."</td>";
        $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($skpd['total']-$skpd['total_murni']),0,",",".")."</td>";
    }
    $body_sumberdana .= '
        <tr>
            <td class="kanan bawah kiri text_tengah text_blok"></td>
            <td class="kanan bawah text_blok" colspan="2">'.$k.' '.$skpd['nama'].'</td>
            '.$murni.'
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['total'],0,",",".").'</td>
            '.$selisih.'
        </tr>
    ';
	$no = 0;
    foreach ($skpd['data'] as $sub_keg) {
    	$no++;
        $murni = '';
        $selisih = '';
        if($type == 'pergeseran'){
            $murni = "<td class='kanan bawah text_kanan'>".number_format($sub_keg['total_murni'],0,",",".")."</td>";
            $selisih = "<td class='kanan bawah text_kanan'>".number_format(($sub_keg['total']-$sub_keg['total_murni']),0,",",".")."</td>";
        }
        $sd_text = array();
        $all_sd = $wpdb->get_results("select namadana from data_dana_sub_keg where tahun_anggaran=".$input['tahun_anggaran']." and kode_sbl='".$sub_keg['data']['kode_sbl']."' and active=1", ARRAY_A);
        foreach ($all_sd as $sd) {
        	$sd_text[] = $sd['namadana'];
        }
		$nama_page = $input['tahun_anggaran'] . ' | ' . $sub_keg['data']['kode_skpd'] . ' | ' . $sub_keg['data']['kode_giat'] . ' | ' . $sub_keg['data']['nama_giat'];
		$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
		$link = 'style="color: red;" title="'.$nama_page.'"';
		if(!empty($custom_post)){
			$link = 'href="'.$custom_post->guid. '?key=' . $this->gen_key().'"';
		}else{
			$kode_skpd = $wpdb->get_var("
				SELECT 
					kode_skpd 
				from data_unit 
				where id_skpd=".$sub_keg['data']['id_sub_skpd']."
					AND tahun_anggaran=".$input['tahun_anggaran']."
					AND active=1");
			$nama_page = $input['tahun_anggaran'] . ' | ' . $kode_skpd . ' | ' . $sub_keg['data']['kode_giat'] . ' | ' . $sub_keg['data']['nama_giat'];
			$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
			$link = 'style="color: red;" title="'.$nama_page.'"';
			if(!empty($custom_post)){
				$link = 'href="'.$custom_post->guid. '?key=' . $this->gen_key().'"';
			}
		}
        $body_sumberdana .= '
            <tr class="sub_keg">
                <td class="kanan bawah kiri text_tengah">'.$no.'</td>
                <td class="kanan bawah" style="padding-left: 20px;"><a '.$link.' target="_blank">'.$sub_keg['nama'].'</a></td>
                <td class="kanan bawah">'.implode(', ', $sd_text).'</td>
                '.$murni.'
                <td class="kanan bawah text_kanan">'.number_format($sub_keg['total'],0,",",".").'</td>
                '.$selisih.'
            </tr>
        ';
        $murni = '';
        $selisih = '';
        if($type == 'pergeseran'){
            $murni = "<td class='kanan bawah text_kanan'>".number_format($sub_keg['total_murni'],0,",",".")."</td>";
            $selisih = "<td class='kanan bawah text_kanan'>".number_format(($sub_keg['total']-$sub_keg['total_murni']),0,",",".")."</td>";
        }
    }
    $murni = '';
    $selisih = '';
    if($type == 'pergeseran'){
        $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($skpd['total_murni'],0,",",".")."</td>";
        $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($skpd['total']-$skpd['total_murni']),0,",",".")."</td>";
    }
    $body_sumberdana .= '
        <tr>
            <td class="kanan bawah kiri text_tengah text_blok">&nbsp;</td>
            <td class="kanan bawah text_blok text_kanan" colspan="2">Jumlah Pada SKPD</td>
            '.$murni.'
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['total'],0,",",".").'</td>
            '.$selisih.'
        </tr>
    ';
}
$murni = '';
$selisih = '';
if($type == 'pergeseran'){
    $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($data_sumberdana_shorted['total_murni'],0,",",".")."</td>";
    $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($data_sumberdana_shorted['total']-$data_sumberdana_shorted['total_murni']),0,",",".")."</td>";
}
$body_sumberdana .= '
    <tr>
        <td class="kiri kanan bawah text_blok text_kanan" colspan="3">Jumlah Total</td>
        '.$murni.'
        <td class="kanan bawah text_blok text_kanan">'.number_format($data_sumberdana_shorted['total'],0,",",".").'</td>
        '.$selisih.'
    </tr>
';

// print_r($data_sumberdana_shorted);
?>
<div id="cetak" title="<?php echo implode(' ', $judul_laporan); ?>" style="padding: 5px;">
	<h4 style="text-align: center; margin: 0; font-weight: bold;"><?php echo implode('<br>', $judul_laporan); ?></h4>
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok">No</td>
                <td class="atas kanan bawah text_tengah text_blok">SKPD/Sub Kegiatan</td>
                <td class="atas kanan bawah text_tengah text_blok">Sumber Dana</td>
                <?php if($type == 'murni'): ?>
                    <td class="atas kanan bawah text_tengah text_blok">Jumlah</td>
                <?php else: ?>
                    <td class="atas kanan bawah text_tengah text_blok">Sebelum Perubahan</td>
                    <td class="atas kanan bawah text_tengah text_blok">Sesudah Perubahan</td>
                    <td class="atas kanan bawah text_tengah text_blok">Bertambah/(Berkurang)</td>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_sumberdana; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    run_download_excel();
    var _url = window.location.href;
    var url = new URL(_url);
    _url = url.origin+url.pathname+'?key='+url.searchParams.get('key');
    var type = url.searchParams.get("type");
    if(type && type=='pergeseran'){
        var extend_action = '<a class="button button-primary" target="_blank" href="'+_url+'" style="margin-left: 10px;">Print APBD Murni</a>';
    }else{
        var extend_action = '<a class="button button-primary" target="_blank" href="'+_url+'&type=pergeseran" style="margin-left: 10px;">Print Pergeseran/Perubahan APBD</a>';
    }
    jQuery('#action-sipd #excel').after(extend_action);
</script>