<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$input = shortcode_atts( array(
    'tahun_anggaran' => '2022'
), $atts );

$data = $wpdb->get_results($wpdb->prepare("
    SELECT 
        kecamatan, 
        sum(total) as total 
    from data_bkk_desa 
    WHERE tahun_anggaran=%d
        and active=1
    group by kecamatan 
    order by kecamatan ASC
", $input['tahun_anggaran']), ARRAY_A);

function link_detail($link_admin, $jenis){
    return "<a target='_blank' href='".$link_admin."?".$jenis['key']."=".$jenis['value']."'>".$jenis['label']."</a>";
}

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
foreach($data as $i => $val){
    $realisasi = 0;
    $belum_realisasi = $val['total'] - $realisasi;
    if($realisasi == 0){
        $persen = 0;
    }else{
        $persen = ($realisasi/$val['total']) * 100;
    }
    $body .= '
    <tr>
        <td class="text-center">'.($i+1).'</td>
        <td>'.$val['kecamatan'].'</td>
        <td class="text-right">'.number_format($val['total'],0,",",".").'</td>
        <td class="text-right">'.number_format($realisasi,0,",",".").'</td>
        <td class="text-right">'.number_format($belum_realisasi,0,",",".").'</td>
        <td class="text-center">'.$persen.'%</td>
    </tr>
    ';
    $total_all += $val['total'];
    $belum_all += $belum_realisasi;
    $realisasi_all += $realisasi;
}
if($realisasi_all == 0){
    $persen_all = 0;
}else{
    $persen_all = ($realisasi_all/$total_all)*100;
}

// grafik desa
$chart_desa = array(
    'label' => array(),
    'data'  => array(),
    'color' => array()
);
$total_desa = 0;
$body_desa = "";
$no = 0;
foreach($desa as $k => $v){
    if($k == 'Tidak diketahui'){
        $jenis = $k;
    }
    $no++;
    $jumlah = count($v);
    $total_desa += $jumlah;
    $nama_desa = $k;
    if(empty($nama_desa)){
        $nama_desa = 'Tidak diketahui';
    }
    $chart_desa['label'][] = $nama_desa;
    $chart_desa['data'][] = $total;
    $chart_desa['color'][] = generateRandomColor($no);
    if(true == $login){
        $nama_desa = link_detail($link_data_admin, array('key' => 'desa', 'value' => $nama_desa, 'label' => $nama_desa ));
    }
    $body_desa .= "
        <tr>
            <td style='text-transform: uppercase;'>$nama_desa</td>
            <td class='text-right'>$jumlah</td>
        </tr>
    ";
}

ksort($desa);
?>

<h1 class="text-center">Bantuan Keuangan Khusus Kepada Desa<br>Rekapitulasi Per Kecamatan<br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
<div class="cetak">
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
<div class="cetak">
    <div style="padding: 10px;">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h2 class="text-center text-white">Grafik Badan Keuangan Khusus<br> berdasarkan Lokasi Desa</h2>
                    </div>
                    <div class="card-body">
                        <div class="container counting-inner">
                            <div class="row counting-box title-row" style="margin-bottom: 20px;">
                                <div class="col-md-12 text-center animated" data-animation="fadeInBottom"
                                    data-animation-delay="200">
                                    <div style="width: 30%; margin:auto;">
                                        <canvas id="chart_per_desa"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="atas kanan bawah kiri text_tengah text_blok" style="width: 30px;">No</th>
                                    <th class="text-center">Desa</th>
                                    <th class="text-center">Kecamatan</th>
                                    <th class="atas kanan bawah text_tengah text_blok">Anggaran</th>
                                    <th class="atas kanan bawah text_tengah text_blok">Realisasi</th>
                                    <th class="atas kanan bawah text_tengah text_blok">Belum Realisasi</th>
                                    <th class="atas kanan bawah text_tengah text_blok">% Realisasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php echo $body_desa; ?>
                            </tbody>
                            <tfoot>
                                <th colspan="2">JUMLAH</th>
                                <th ></th>
                                <th class="text-right"><?php echo number_format($total_all,0,",","."); ?></th>
                                <th class="text-right"><?php echo number_format($realisasi_all,0,",","."); ?></th>
                                <th class="text-right"><?php echo number_format($belum_all,0,",","."); ?></th>
                                <th class="text-center"><?php echo $persen_all; ?>%</th>
                                </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
window.desa = <?php echo json_encode($chart_desa); ?>;
window.pieChartDesa = new Chart(document.getElementById('chart_per_desa'), {
    type: 'bar',
    data: {
        labels: desa.label,
        datasets: [
            {
                label: '',
                data: desa.data,
                backgroundColor: desa.color
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        size: 0
                    }
                }
            },
            tooltip: {
                bodyFont: {
                    size: 16
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                boxPadding: 5
            },
        }
    }
});
