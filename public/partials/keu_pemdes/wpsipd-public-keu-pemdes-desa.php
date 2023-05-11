<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$input = shortcode_atts( array(
    'tahun_anggaran' => '2022',
    'id_kec' => '',
    'id_kel' => ''
), $atts );

$body = $wpdb->get_row($wpdb->prepare('
    SELECT 
        desa, 
        sum(total) as total 
    from data_bkk_desa 
    WHERE tahun_anggaran=%d
        and active=1
        and id_desa=%d
    group by desa
', $input['tahun_anggaran'], $input['id_kel'],$input['id_kec']), ARRAY_A);


$total_all = 0;
$belum_all = 0;
$realisasi_all = 0;
$persen_all = 0;

?>


<h1 class="text-center">Laporan<br>Desa <!-- <?php echo $input['id_kel'] ?> --> Kecamatan <!-- <?php echo $input['id_kec'] ?> --><br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
<div class="cetak">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="atas kanan bawah kiri text_tengah text_blok" style="width: 30px;">No</th>
                <th class="atas kanan bawah text_tengah text_blok" >Uraian</th>
                <th class="atas kanan bawah text_tengah text_blok">Anggaran</th>
                <th class="atas kanan bawah text_tengah text_blok">Realisasi</th>
                <th class="atas kanan bawah text_tengah text_blok">Belum Realisasi</th>
                <th class="atas kanan bawah text_tengah text_blok">% Realisasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>BHPD</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-center">0%</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>BHRD</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-center">0%</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>DD</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-center">0%</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>ADD</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-center">0%</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>BKK Infrastruktur</td>
                <td class="text-right"><?php echo number_format($body['total'],0,",","."); ?></td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-center">0%</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>BKK PILKADES</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-center">0%</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">JUMLAH</th>
                <th class="text-right"><?php echo number_format($body['total'],0,",","."); ?></th>
                <th class="text-right"><?php echo number_format($realisasi_all,0,",","."); ?></th>
                <th class="text-right"><?php echo number_format($belum_all,0,",","."); ?></th>
                <th class="text-center"><?php echo $persen_all; ?>%</th>
            </tr>
        </tfoot>
    </table>
</div>