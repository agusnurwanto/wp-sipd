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
    order by total ASC
", $input['tahun_anggaran']), ARRAY_A);
$body = '';
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
                <th><?php echo number_format($total_all,0,",","."); ?></th>
                <th><?php echo number_format($realisasi_all,0,",","."); ?></th>
                <th><?php echo number_format($belum_all,0,",","."); ?></th>
                <th><?php echo $persen_all; ?>%</th>
            </tr>
        </tfoot>
    </table>
</div>