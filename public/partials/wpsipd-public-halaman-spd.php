<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
global $wpdb;
$input = shortcode_atts(array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts);

if (!empty($input['id_skpd'])) {
	$sql = $wpdb->prepare("
		select 
			* 
		from data_unit 
		where tahun_anggaran=%d
			and id_skpd IN (" . $input['id_skpd'] . ") 
			and active=1
		order by id_skpd ASC
	", $input['tahun_anggaran']);
} else {
	$sql = $wpdb->prepare("
		select 
			* 
		from data_unit 
		where tahun_anggaran=%d
			and active=1
		order by id_skpd ASC
	", $input['tahun_anggaran']);
}
$units = $wpdb->get_results($sql, ARRAY_A);
if (empty($units)) {
	die('<h1>SKPD tidak ditemukan!</h1>');
} 
foreach ($units as $k => $unit) :

    $body .= '
        <tr>
            <td class="text-center">'.$no.'</td>
            <td>'.$unit['nama_skpd'].'</td>
            <td class="text-center">'.$val->no_spd.'</td>
            <td class="text-center">'.$val->tgl_spd.'</td>
            <td class="text-right" style="'.$background.'">'.number_format($total_simda, 2, ',', '.').'</td>
        </tr>
    ';
endforeach;

?>


</style>
<div class="cetak">
    <div style="padding: 10px;">
        <input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
        <input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
        <h1 class="text-center">Halaman Data SPD Tahun <?php echo $unit['nama_skpd']; ?></h1>
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
                <th class="text-right" style="<?php echo $background_all; ?>"><?php echo number_format($total_all_simda, 2, ',', '.'); ?></th>
            </tfoot>
        </table>
    </div>
</div>