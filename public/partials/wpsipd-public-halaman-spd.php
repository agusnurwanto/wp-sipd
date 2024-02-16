<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;
$input = shortcode_atts( array(
    'tahun_anggaran' => '2021'
), $atts );
$api_key = get_option( '_crb_api_key_extension' );

$body = '';
$_POST['tahun_anggaran'] = $input['tahun_anggaran'];
$_POST['api_key'] = $api_key;
$_POST['action'] = '';
$spd = $this->get_spd(true);
$no = 0;
$total_all_simda = 0;
foreach ($spd['data'] as $val) {
    // print_r($val); die();
    $no++;

    $sql = $wpdb->prepare("
        SELECT 
            sum(nilai) as nilai
        FROM data_spd_rinci 
        where tahun=%d
            and no_spd=%s
        ", 
        $input['tahun_anggaran'], 
        $val->no_spd
    );
    // print_r($sql); die($wpdb->last_query);
    $total = $this->simda->CurlSimda(array(
        'query' => $sql,
        'debug' => 1
    ));
    $total_simda = 0;
    if(!empty($total[0]->nilai)){
        $total_simda = $total[0]->nilai;
    }
    $nama_skpd = '';
//     $sql = $wpdb->prepare(" 
//     	SELECT 
//            * 
// 		from data_unit 
// 		where tahun_anggaran=%d
// 			and id_skpd =".$input['id_skpd']."
// 			and active=1
// 		order by id_skpd ASC
// 	", $input['tahun_anggaran']);
		
// $nama_skpd = $wpdb->get_results($sql, ARRAY_A);
    if(!empty($val->skpd) && !empty($val->skpd['kode_skpd'])){
        $nama_skpd = $val->skpd['kode_skpd'].'<br>'.$val->skpd['nama_skpd'];
        $update = false;
        $url_skpd = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, $update);
        $nama_skpd = '<a href="'.$url_skpd.'" target="_blank">'.$nama_skpd.'</a>';
    }
    $body .= '
        <tr>
            <td class="text-center">'.$no.'</td>
            <td>'.$nama_skpd.'</td>
            <td class="text-center">'.$val->no_spd.'</td>
            <td class="text-center">'.$val->tgl_spd.'</td>
            <td class="text-right">'.number_format($total_simda, 2, ',', '.').'</td>
        </tr>
    ';
    $total_all_simda += $total_simda;
}
?>

</style>
<div class="cetak">
    <div style="padding: 10px;">
        <input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
        <input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
        <h1 class="text-center">Data Surat Penyediaan Dana ( SPD )<br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
        <table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead id="data_header">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama SKPD</th>
                    <th class="text-center">No SPD</th>
                    <th class="text-center">Tanggal SPD</th>
                    <th class="text-center">Total SIMDA</th>
                </tr>
            </thead>
            <tbody id="data_body">
                <?php echo $body; ?>
            </tbody>
            <tfoot>
                <th colspan="4" class="text-center">Total</th>
                <th class="text-right"><?php echo number_format($total_all_simda, 2, ',', '.'); ?></th>
            </tfoot>
        </table>
    </div>
</div>