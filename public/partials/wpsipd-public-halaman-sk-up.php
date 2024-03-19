<?php
global $wpdb;

$input = shortcode_atts(array(
    'id_skpd' => '',
    'tahun_anggaran' => ''
), $atts);

$get_sk_up = $wpdb->get_results($wpdb->prepare('
    SELECT 
        *
    from data_up_sipd
    WHERE tahun_anggaran=%d
        and active=1
', $input['tahun_anggaran']), ARRAY_A);
// print_r($get_sk_up); die($wpdb->last_query);

$nomor = 0;
$body = '';
$total_pagu = 0;
$total_besaran_up = 0;
$besaran_up_all = 0;
$total_besaran_up_all = 0;
$total_besaran_up_kkpd = 0;
$total_besaran_up_kkpd = 0;
foreach($get_sk_up as $sk_up){
    $nomor++;
    $persen = 0;
    $besaran_up_all = $sk_up['besaran_up'] + $sk_up['besaran_up_kkpd'];
    if($sk_up['pagu'] > 0 && $besaran_up_all > 0){
        $persen = round(($besaran_up_all/$sk_up['pagu'])*100, 2);
    }
    $unit = $wpdb->get_row($wpdb->prepare("
        SELECT 
            *
        FROM data_unit
        WHERE active=1
            AND tahun_anggaran=%d
            AND id_skpd=%d
    ", $input['tahun_anggaran'], $sk_up['id_skpd']), ARRAY_A);
    $nama_skpd = '';
    $kode_skpd = '';
    if(empty(!$unit)){
        $nama_skpd = $unit['nama_skpd'];
        $kode_skpd = $unit['kode_skpd'];
    }

    $body .= '
        <tr>
            <td class="text-center">'.$nomor.'</td>
            <td class="text-center">'.$sk_up['id_skpd'].'</td>
            <td class="text-center">'.$sk_up['id_sub_skpd'].'</td>
            <td class="text-center">'.$kode_skpd.'</td>
            <td class="text-left">'.$nama_skpd.'</td>
            <td class="text-right">'.number_format($sk_up['pagu'],0,",",".").'</td>
            <td class="text-right">'.number_format($sk_up['besaran_up'],0,",",".").'</td>
            <td class="text-right">'.number_format($sk_up['besaran_up_kkpd'],0,",",".").'</td>
            <td class="text-right">'.number_format($besaran_up_all,0,",",".").'</td>
            <td class="text-center">'.$persen.'%</td>
        </tr>
    ';

    $total_pagu += $sk_up['pagu'];
    $total_besaran_up += $sk_up['besaran_up'];
    $total_besaran_up_kkpd += $sk_up['besaran_up_kkpd'];
    $total_besaran_up_all = $total_besaran_up + $total_besaran_up_kkpd;
    $persen_all = 0;
    if($total_pagu > 0 && $total_besaran_up_all > 0){
        $persen_all = round(($total_besaran_up_all/$total_pagu)*100, 2);
    }
}
?>

<style type="text/css">
    .wrap-table {
        overflow: auto;
        max-height: 100vh;
        width: 100%;
    }
</style>

<div class="cetak container-fluid">
    <h1 class="text-center">Halaman Surat Keterangan Uang Persediaan<br>Tahun <?php  echo $input['tahun_anggaran'] ?></h1>
    <table class="table table-bordered" id="cetak">
        <thead>
            <tr>
                 <th class="text-center">No</th>
                 <th class="text-center">ID SKPD</th>
                 <th class="text-center">ID Sub SKPD</th>
                 <th class="text-center">Kode SKPD</th>
                 <th class="text-center">Nama SKPD</th>
                 <th class="text-center">Pagu</th>
                 <th class="text-center">Besaran UP</th>
                 <th class="text-center">Besaran UP_kkpd</th>
                 <th class="text-center">Total Besaran UP</th>
                 <th class="text-center">Persen</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $body; ?>
        </tbody>
        <tfoot>
            <tr>
                <th class="atas bawah kanan kiri text_kanan" colspan="5">Total</th>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_pagu,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_besaran_up,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_besaran_up_kkpd,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_besaran_up_all,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_tengah"><?php echo $persen_all; ?>%</td>
            </tr>
        </tfoot>
    </table>      
</div>