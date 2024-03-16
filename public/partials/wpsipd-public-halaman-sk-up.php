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
$total_besaran_up_kkpd = 0;
foreach($get_sk_up as $sk_up){
    $nomor++;
    $body .= '
        <tr>
            <td class="text-center">'.$nomor.'</td>
            <td class="text-center">'.$sk_up['id_skpd'].'</td>
            <td class="text-center">'.$sk_up['id_sub_skpd'].'</td>
            <td class="text-right">'.number_format($sk_up['pagu'],0,",",".").'</td>
            <td class="text-right">'.number_format($sk_up['besaran_up'],0,",",".").'</td>
            <td class="text-right">'.number_format($sk_up['besaran_up_kkpd'],0,",",".").'</td>
        </tr>
    ';

    $total_pagu += $sk_up['pagu'];
    $total_besaran_up += $sk_up['besaran_up'];
    $total_besaran_up_kkpd += $sk_up['besaran_up_kkpd'];
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
                 <th class="text-center">Pagu</th>
                 <th class="text-center">Besaran UP</th>
                 <th class="text-center">Besaran UP_kkpd</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $body; ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="atas bawah kanan kiri text_kanan" colspan="3"></td>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_pagu,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_besaran_up,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_besaran_up_kkpd,0,",","."); ?></td>
            </tr>
        </tfoot>
    </table>      
</div>