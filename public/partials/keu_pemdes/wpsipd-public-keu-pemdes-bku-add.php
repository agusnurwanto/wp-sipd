<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$input = shortcode_atts( array(
    'tahun_anggaran' => '2024'
), $atts );

$user_id = um_user( 'ID' );
$user_meta = get_userdata($user_id);
$cek_login = false;
$url_all_kec = array();
if(
    !empty($user_id)
    && (
        in_array("administrator", $user_meta->roles)
        || in_array("PLT", $user_meta->roles) 
        || in_array("PA", $user_meta->roles) 
        || in_array("KPA", $user_meta->roles)
    )
){
    $cek_login = true;
    $unit = $wpdb->get_results($wpdb->prepare("
        SELECT 
            nama_skpd, 
            id_skpd, 
            kode_skpd, 
            nipkepala 
        from data_unit 
        where active=1 
            and tahun_anggaran=%d 
            and is_skpd=1 
            and nama_skpd like 'KECAMATAN %' 
        order by kode_skpd ASC
    ", $input['tahun_anggaran']), ARRAY_A);
    foreach($unit as $kk => $vv){
        $url_skpd = $this->generatePage($vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$input['tahun_anggaran'], $input['tahun_anggaran'], '[monitor_keu_pemdes tahun_anggaran="'.$input['tahun_anggaran'].'" id_skpd="'.$vv['id_skpd'].'"]');
        $nama_kec = str_replace('kecamatan ', '', strtolower($vv['nama_skpd']));
        $url_all_kec[$nama_kec] = $url_skpd;
    }
}

$data = $wpdb->get_results($wpdb->prepare("
    SELECT 
        kecamatan, 
        sum(total) as total 
    from data_bku_add_desa 
    WHERE tahun_anggaran=%d
        and active=1
    group by kecamatan 
    order by kecamatan ASC
", $input['tahun_anggaran']), ARRAY_A);

function generateRandomColor($k){
    $color = array('#f44336', '#9c27b0', '#2196f3', '#009688', '#4caf50', '#cddc39', '#ff9800', '#795548', '#9e9e9e', '#607d8b');
    return $color[$k%10];
}

$body = '';
$desa = array();
$total_all = 0;
$belum_all = 0;
$realisasi_all = 0;
$persen_all = 0;

// grafik kec
$chart_kec = array(
    'label' => array(),
    'label1' => 'Anggaran',
    'label2' => 'Realisasi',
    'data1'  => array(),
    'color1' => array(),
    'data2'  => array(),
    'color2' => array()
);
foreach($data as $i => $val){
    $realisasi = $wpdb->get_var($wpdb->prepare("
        SELECT 
            SUM(p.total_pencairan) 
        FROM data_pencairan_bku_add_desa p
        INNER JOIN data_bku_add_desa b on p.id_bku_add=b.id
            AND b.active=1
            AND b.tahun_anggaran=%d
        WHERE b.kecamatan=%s
        ", $input['tahun_anggaran'], $val['kecamatan']));
    $belum_realisasi = $val['total'] - $realisasi;
    if($realisasi == 0){
        $persen = 0;
    }else{
        $persen = round(($realisasi/$val['total']) * 100, 2);
    }
    $nama_kec_render = $val['kecamatan'];
    if(
        $cek_login
        && !empty($url_all_kec[strtolower($val['kecamatan'])])
    ){
        $nama_kec_render = "<a href='".$url_all_kec[strtolower($val['kecamatan'])]."' target='_blank'>".$val['kecamatan']."</a>";
    }
    $body .= '
    <tr>
        <td class="text-center">'.($i+1).'</td>
        <td>'.$nama_kec_render.'</td>
        <td class="text-right">'.number_format($val['total'],0,",",".").'</td>
        <td class="text-right">'.number_format($realisasi,0,",",".").'</td>
        <td class="text-right">'.number_format($belum_realisasi,0,",",".").'</td>
        <td class="text-center">'.$persen.'%</td>
    </tr>
    ';
    $total_all += $val['total'];
    $belum_all += $belum_realisasi;
    $realisasi_all += $realisasi;

    $chart_kec['label'][] = $val['kecamatan'];
    $chart_kec['data1'][] = $val['total'];
    $chart_kec['color1'][] = generateRandomColor(1);
    $chart_kec['data2'][] = $realisasi;
    $chart_kec['color2'][] = generateRandomColor(2);
}
if($realisasi_all == 0){
    $persen_all = 0;
}else{
    $persen_all = round(($realisasi_all/$total_all)*100, 2);
}
?>

<h1 class="text-center">BKU Anggaran Dana Desa ( ADD )<br>Rekapitulasi Per Kecamatan<br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
<div class="cetak">
    <div style="padding: 10px;">
        <div class="row">
            <div class="col-md-12">
                <div style="width: 100%; margin:auto 10px;">
                    <canvas id="chart_per_kec"></canvas>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">Tabel BKU Anggaran Dana Desa ( ADD )<br>Rekapitulasi Per Kecamatan<br>Tahun <?php echo $input['tahun_anggaran']; ?></h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="atas kanan bawah kiri text_tengah text_blok" style="width: 30px;">No</th>
                            <th class="atas kanan bawah text_tengah text_blok" >Kecamatan</th>
                            <th class="atas kanan bawah text_tengah text_blok">Anggaran</th>
                            <th class="atas kanan bawah text_tengah text_blok">Realisasi</th>
                            <th class="atas kanan bawah text_tengah text_blok">Belum Realisasi</th>
                            <th class="atas kanan bawah text_tengah text_blok">% Realisasi</th>
                        </tr>
                    </thead>
                    <tbody><?php echo $body; ?></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">JUMLAH</th>
                            <th class="text-right"><?php echo number_format($total_all,0,",","."); ?></th>
                            <th class="text-right"><?php echo number_format($realisasi_all,0,",","."); ?></th>
                            <th class="text-right"><?php echo number_format($belum_all,0,",","."); ?></th>
                            <th class="text-center"><?php echo $persen_all; ?>%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
window.kec = <?php echo json_encode($chart_kec); ?>;
window.pieChartkec = new Chart(document.getElementById('chart_per_kec'), {
    type: 'bar',
    data: {
        labels: kec.label,
        datasets: [
            {
                label: kec.label1,
                data: kec.data1,
                backgroundColor: kec.color1
            },
            {
                label: kec.label2,
                data: kec.data2,
                backgroundColor: kec.color2
            }
        ]
    }
});
</script>