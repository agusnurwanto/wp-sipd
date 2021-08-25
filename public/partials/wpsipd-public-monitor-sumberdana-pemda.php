<?php
global $wpdb;
$input = shortcode_atts( array(
	'id_sumber_dana' => '',
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

$current_user = wp_get_current_user();

$type = 'murni';
if(!empty($_GET) && !empty($_GET['type'])){
    $type = $_GET['type'];
}

$type_mapping = 0;
if(!empty($_GET) && !empty($_GET['mapping'])){
    $type_mapping = $_GET['mapping'];
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
$bulan = (int) date('m');

$where_skpd = '';
if(!empty($_GET) && !empty($_GET['id_skpd'])){
    $where_skpd = 'and r.id_sub_skpd='.$_GET['id_skpd'];
}
$data_sub_giat = $wpdb->get_results('
	select 
		r.*,
        f.rak,
        f.realisasi_anggaran, 
        f.id as id_rfk, 
        f.realisasi_fisik, 
        f.permasalahan
	from data_dana_sub_keg d
		inner join data_sub_keg_bl r on r.kode_sbl = d.kode_sbl
			and r.tahun_anggaran = d.tahun_anggaran
			and r.active = d.active
        left join data_rfk f on r.kode_sbl=f.kode_sbl
            and r.tahun_anggaran=f.tahun_anggaran
            and r.id_sub_skpd=f.id_skpd
            and f.bulan='.$bulan.'
	where d.active=1 
		and d.tahun_anggaran='.$input['tahun_anggaran'].'
		and (
			d.iddana='.$input['id_sumber_dana'].'
			or d.iddana is null
		)
        '.$where_skpd, ARRAY_A);
// echo $wpdb->last_query;
$judul_laporan = array('Laporan Pagu SIPD Kemendagri Per Sumber Dana',$kode_sumber_dana.' '.$nama_sumber_dana,'Tahun '.$input['tahun_anggaran']);

$data_sumberdana_shorted = array(
    'data' => array(),
    'total_simda' => 0,
    'realisasi' => 0,
    'total_sd_mapping' => 0,
    'total_murni' => 0,
    'total' => 0
);

foreach ($data_sub_giat as $k =>$v) {
    $kd_unit_simda = explode('.', get_option('_crb_unit_'.$v['id_sub_skpd']));
    $_kd_urusan = $kd_unit_simda[0];
    $_kd_bidang = $kd_unit_simda[1];
    $kd_unit = $kd_unit_simda[2];
    $kd_sub_unit = $kd_unit_simda[3];

    $kd = explode('.', $v['kode_sub_giat']);
    $kd_urusan90 = (int) $kd[0];
    $kd_bidang90 = (int) $kd[1];
    $kd_program90 = (int) $kd[2];
    $kd_kegiatan90 = ((int) $kd[3]).'.'.$kd[4];
    $kd_sub_kegiatan = (int) $kd[5];
    $nama_keg = explode(' ', $v['nama_sub_giat']);
    unset($nama_keg[0]);
    $nama_keg = implode(' ', $nama_keg);
    $mapping = $this->simda->cekKegiatanMapping(array(
        'kd_urusan90' => $kd_urusan90,
        'kd_bidang90' => $kd_bidang90,
        'kd_program90' => $kd_program90,
        'kd_kegiatan90' => $kd_kegiatan90,
        'kd_sub_kegiatan' => $kd_sub_kegiatan,
        'nama_program' => $v['nama_giat'],
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
    $total_simda = $this->get_pagu_simda_last(array(
        'tahun_anggaran' => $input['tahun_anggaran'],
        'pagu_simda' => $v['pagu_simda'],
        'id_sub_keg' => $v['id'],
        'kd_urusan' => $_kd_urusan,
        'kd_bidang' => $_kd_bidang,
        'kd_unit' => $kd_unit,
        'kd_sub' => $kd_sub_unit,
        'kd_prog' => $kd_prog,
        'id_prog' => $id_prog,
        'kd_keg' => $kd_keg
    ));
    $realisasi = $this->get_realisasi_simda(array(
        'user' => $current_user->display_name,
        'id_skpd' => $input['id_skpd'],
        'kode_sbl' => $v['kode_sbl'],
        'tahun_anggaran' => $input['tahun_anggaran'],
        'realisasi_anggaran' => $v['realisasi_anggaran'],
        'id_rfk' => $v['id_rfk'],
        'bulan' => $bulan,
        'kd_urusan' => $_kd_urusan,
        'kd_bidang' => $_kd_bidang,
        'kd_unit' => $kd_unit,
        'kd_sub' => $kd_sub_unit,
        'kd_prog' => $kd_prog,
        'id_prog' => $id_prog,
        'kd_keg' => $kd_keg
    ));

    $sd_data = array();
    $sd_text = array();
    $all_sd = $wpdb->get_results("select namadana, iddana from data_dana_sub_keg where tahun_anggaran=".$input['tahun_anggaran']." and kode_sbl='".$v['kode_sbl']."' and active=1", ARRAY_A);
    $length_sd = count($all_sd);
    foreach ($all_sd as $sd) {
        if($type_mapping == 1 && $length_sd > 1){
            $sd_mapping = $wpdb->get_results("
                select 
                    sum(r.rincian) as total
                from data_mapping_sumberdana m
                    inner join data_rka r on r.id_rinci_sub_bl=m.id_rinci_sub_bl
                        and r.active=m.active
                where r.tahun_anggaran=".$input['tahun_anggaran']."
                    and r.kode_sbl='".$v['kode_sbl']."'
                    and r.active=1
                    and m.id_sumber_dana=".$sd['iddana']."
            ", ARRAY_A);
            if(!empty($sd_mapping)){
                $sd['namadana'] .= ' ('.number_format($sd_mapping[0]['total'],0,",",".").')';
                $sd_data[$sd['iddana']] = $sd_mapping[0]['total'];
            }else{
                $sd_data[$sd['iddana']] = 0;
            }
        }
        $sd_text[] = $sd['namadana'];
    }
    $v['sd_text'] = $sd_text;
    if(empty($sd_data)){
        $sd_data[$input['id_sumber_dana']] = $v['pagu'];
    }
    $v['sd_data'] = $sd_data;

	$kode = explode('.', $v['kode_sbl']);
    $idskpd = $kode[1];
    $skpd = $wpdb->get_row("SELECT nama_skpd, kode_skpd from data_unit where id_skpd=$idskpd", ARRAY_A);
    if(empty($data_sumberdana_shorted['data'][$skpd['kode_skpd']])){
        $data_sumberdana_shorted['data'][$skpd['kode_skpd']] = array(
            'nama' => $skpd['nama_skpd'],
            'total_murni' => 0,
            'total' => 0,
            'total_simda' => 0,
            'total_sd_mapping' => 0,
            'realisasi' => 0,
            'data' => array()
        );
    }
    if(empty($data_sumberdana_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']])){
        $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']] = array(
            'nama' => $v['nama_sub_giat'],
            'total_murni' => 0,
            'total' => 0,
            'total_simda' => 0,
            'total_sd_mapping' => 0,
            'realisasi' => 0,
            'data' => $v
        );
    }
    
    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['realisasi'] += $realisasi;
    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['realisasi'] += $realisasi;
    $data_sumberdana_shorted['realisasi'] += $realisasi;
    
    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total_simda'] += $total_simda;
    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['total_simda'] += $total_simda;
    $data_sumberdana_shorted['total_simda'] += $total_simda;
    
    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total'] += $v['pagu'];
    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['total'] += $v['pagu'];
    $data_sumberdana_shorted['total'] += $v['pagu'];

    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total_murni'] += $v['pagumurni'];
    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['total_murni'] += $v['pagumurni'];
    $data_sumberdana_shorted['total_murni'] += $v['pagumurni'];

    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total_sd_mapping'] += $sd_data[$input['id_sumber_dana']];
    $data_sumberdana_shorted['data'][$skpd['kode_skpd']]['total_sd_mapping'] += $sd_data[$input['id_sumber_dana']];
    $data_sumberdana_shorted['total_sd_mapping'] += $sd_data[$input['id_sumber_dana']];
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
    $capaian = 0;
    if(!empty($skpd['total_simda'])){
        $capaian = $this->pembulatan(($skpd['realisasi']/$skpd['total_simda'])*100);
    }
    $mapping_sd = '';
    if($type_mapping == 1){
        $mapping_sd = '<td class="kanan bawah text_blok text_kanan">'.number_format($skpd['total_sd_mapping'],0,",",".").'</td>';
    }
    $body_sumberdana .= '
        <tr>
            <td class="kanan bawah kiri text_tengah text_blok"></td>
            <td class="kanan bawah text_blok" colspan="2">'.$k.' '.$skpd['nama'].'</td>
            '.$mapping_sd.'
            '.$murni.'
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['total'],0,",",".").'</td>
            '.$selisih.'
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['total_simda'],0,",",".").'</td>
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['realisasi'],0,",",".").'</td>
            <td class="kanan bawah text_blok text_tengah">'.$capaian.'</td>
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

		$nama_page = $input['tahun_anggaran'] . ' | ' . $sub_keg['data']['kode_skpd'] . ' | ' . $sub_keg['data']['kode_giat'] . ' | ' . $sub_keg['data']['nama_giat'];
		$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
		$link = 'style="color: red;" title="'.$nama_page.'"';
		if(!empty($custom_post)){
			$link = 'href="'.get_permalink($custom_post). '?key=' . $this->gen_key().'"';
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
                $link = $this->get_link_post($custom_post);
			}
		}
        $capaian = 0;
        if(!empty($sub_keg['total_simda'])){
            $capaian = $this->pembulatan(($sub_keg['realisasi']/$sub_keg['total_simda'])*100);
        }
        $mapping_sd = '';
        if($type_mapping == 1){
            $mapping_sd = '<td class="kanan bawah text_kanan">'.number_format($sub_keg['total_sd_mapping'],0,",",".").'</td>';
        }
        $body_sumberdana .= '
            <tr class="sub_keg">
                <td class="kanan bawah kiri text_tengah">'.$no.'</td>
                <td class="kanan bawah" style="padding-left: 20px;"><a '.$link.' target="_blank">'.$sub_keg['nama'].'</a></td>
                <td class="kanan bawah">'.implode(',<br>', $sub_keg['data']['sd_text']).'</td>
                '.$mapping_sd.'
                '.$murni.'
                <td class="kanan bawah text_kanan">'.number_format($sub_keg['total'],0,",",".").'</td>
                '.$selisih.'
                <td class="kanan bawah text_kanan">'.number_format($sub_keg['total_simda'],0,",",".").'</td>
                <td class="kanan bawah text_kanan">'.number_format($sub_keg['realisasi'],0,",",".").'</td>
                <td class="kanan bawah text_tengah">'.$capaian.'</td>
            </tr>
        ';
    }
}
$murni = '';
$selisih = '';
if($type == 'pergeseran'){
    $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($data_sumberdana_shorted['total_murni'],0,",",".")."</td>";
    $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($data_sumberdana_shorted['total']-$data_sumberdana_shorted['total_murni']),0,",",".")."</td>";
}
$capaian = 0;
if(!empty($data_sumberdana_shorted['total_simda'])){
    $capaian = $this->pembulatan(($data_sumberdana_shorted['realisasi']/$data_sumberdana_shorted['total_simda'])*100);
}
$mapping_sd = '';
if($type_mapping == 1){
    $mapping_sd = '<td class="kanan bawah text_blok text_kanan">'.number_format($data_sumberdana_shorted['total_sd_mapping'],0,",",".").'</td>';
}
$body_sumberdana .= '
    <tr>
        <td class="kiri kanan bawah text_blok text_kanan" colspan="3">Jumlah Total</td>
        '.$mapping_sd.'
        '.$murni.'
        <td class="kanan bawah text_blok text_kanan">'.number_format($data_sumberdana_shorted['total'],0,",",".").'</td>
        '.$selisih.'
        <td class="kanan bawah text_blok text_kanan">'.number_format($data_sumberdana_shorted['total_simda'],0,",",".").'</td>
        <td class="kanan bawah text_blok text_kanan">'.number_format($data_sumberdana_shorted['realisasi'],0,",",".").'</td>
        <td class="kanan bawah text_blok text_tengah">'.$capaian.'</td>
    </tr>
';

// print_r($data_sumberdana_shorted);
?>
<div id="cetak" title="<?php echo implode(' ', $judul_laporan); ?>" style="padding: 5px;">
	<h4 style="text-align: center; margin: 0; font-weight: bold;"><?php echo implode('<br>', $judul_laporan); ?></h4>
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok" width="20px;">No</td>
                <td class="atas kanan bawah text_tengah text_blok">SKPD/Sub Kegiatan</td>
                <td class="atas kanan bawah text_tengah text_blok" width="300px;">Sumber Dana</td>
                <?php if($type_mapping == 1):?>
                    <td class="atas kanan bawah text_tengah text_blok" style="width: 140px;">Sumber Dana Mapping (Rp.)</td>
                <?php endif; ?>
                <?php if($type == 'murni'): ?>
                    <td class="atas kanan bawah text_tengah text_blok" style="width: 140px;">RKA SIPD (Rp.)</td>
                <?php else: ?>
                    <td class="atas kanan bawah text_tengah text_blok" style="width: 140px;">RKA SIPD Sebelum Perubahan (Rp.)</td>
                    <td class="atas kanan bawah text_tengah text_blok" style="width: 140px;">RKA SIPD Sesudah Perubahan (Rp.)</td>
                    <td class="atas kanan bawah text_tengah text_blok" style="width: 140px;">RKA SIPD Bertambah/(Berkurang) (Rp.)</td>
                <?php endif; ?>
                <td class="atas kanan bawah text_tengah text_blok" style="width: 140px;">Pagu Simda (Rp.)</td>
                <td class="atas kanan bawah text_tengah text_blok" style="width: 140px;">Ralisasi Simda (Rp.)</td>
                <td class="atas kanan bawah text_tengah text_blok" style="width: 120px;">Capaian (%)</td>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_sumberdana; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    run_download_excel();
    var _url_asli = window.location.href;

    var url = new URL(_url_asli);
    _url_asli = changeUrl({ url: _url_asli, key: 'key', value: '<?php echo $this->gen_key(); ?>' });

    var type = url.searchParams.get("type");
    if(type && type=='pergeseran'){
        var extend_action = '<a class="button button-primary" target="_blank" href="'+changeUrl({ url: _url_asli, key: 'type', value: 'murni' })+'" style="margin-left: 10px;">Print APBD Murni</a>';
    }else{
        var extend_action = '<a class="button button-primary" target="_blank" href="'+changeUrl({ url: _url_asli, key: 'type', value: 'pergeseran' })+'" style="margin-left: 10px;">Print Pergeseran/Perubahan APBD</a>';
    }
    var mapping = url.searchParams.get("mapping");
    if(mapping && mapping==1){
        extend_action += '<a href="'+changeUrl({ url: _url_asli, key: 'mapping', value: 0 })+'" target="_blank" class="button button-primary" style="margin-left: 10px;">Laporan Tanpa Mapping</a>';
    }else{
        extend_action += '<a href="'+changeUrl({ url: _url_asli, key: 'mapping', value: 1 })+'" target="_blank" class="button button-primary" style="margin-left: 10px;">Laporan Sesuai Mapping Sumber Dana</a>';
    }
    jQuery('#action-sipd #excel').after(extend_action);
</script>