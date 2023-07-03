<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$id_unit = '';
if(!empty($_GET) && !empty($_GET['id_unit'])){
    $id_unit = $_GET['id_unit'];
}

$id_jadwal_lokal = '';
if(!empty($_GET) && !empty($_GET['id_jadwal_lokal'])){
    $id_jadwal_lokal = $_GET['id_jadwal_lokal'];
}

$input = shortcode_atts( array(
	'id_skpd' => $id_unit,
    'id_jadwal_lokal' => $id_jadwal_lokal,
	'tahun_anggaran' => '2022'
), $atts );

$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
    SELECT 
        nama AS nama_jadwal,
        tahun_anggaran,
        status 
    FROM `data_jadwal_lokal` 
    WHERE id_jadwal_lokal=%d", $id_jadwal_lokal));

$_suffix='';
$where_jadwal='';
if($jadwal_lokal->status == 1){
    $_suffix='_history';
    $where_jadwal=' AND id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
}

if($input['id_skpd'] == 'all'){
    $where_skpd = '';
}else{
    $where_skpd = ' AND id_sub_skpd = '.$input['id_skpd'].' ';
}

$nama_pemda = get_option('_crb_daerah');
$nama_excel = 'Analisis Belanja per-Program '.$input['tahun_anggaran'].' '.strtoupper($nama_pemda);
echo '<div id="cetak" title="'.$nama_excel.'" style="padding: 5px;">';

$sql = "
    SELECT
        kode_program,
        nama_program,
        sum(pagu) as total_pagu,
        COUNT(DISTINCT id_sub_skpd) as skpd  
    FROM data_sub_keg_bl_lokal".$_suffix."
    WHERE tahun_anggaran=%d
    AND active=1
    ".$where_jadwal." 
    ".$where_skpd."
    GROUP by kode_program
    ORDER BY kode_program ASC";
$analisis_program = $wpdb->get_results($wpdb->prepare($sql,$input['tahun_anggaran']), ARRAY_A);

$data_all = array(
    'total' => 0,
    'data'  => array()
);
if(!empty($analisis_program)){
    foreach($analisis_program as $k => $ap){
        if(empty($data_all['data'])){
            $data_all['data'] = $analisis_program;
        }
        $data_all['total'] += $ap['total_pagu'];
    }
}

$body = '';
$urut = 1;
foreach ($data_all['data'] as $k => $all_ap) {
    $body .='
    <tr>
        <td class="kiri kanan bawah text_tengah">'.$urut.'</td>
        <td class="kiri kanan bawah text_tengah">'.$all_ap['kode_program'].'</td>
        <td class="kiri kanan bawah text_kiri">'.$all_ap['nama_program'].'</td>
        <td class="kiri kanan bawah text_tengah">'.$all_ap['skpd'].'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($all_ap['total_pagu'],0,",",".").'</td>
    </tr>';
    $urut++;
}

$nama_laporan = 'ANALISIS BELANJA PAGU per-PROGRAM<br>TAHUN ANGGARAN '.$input['tahun_anggaran'].' '.strtoupper($nama_pemda);
echo '
<button type="button" style="background-color:#FFD670; text-align: center; margin: 10px auto 20px; display: block;" class="btn">Laporan Jadwal '.$jadwal_lokal->nama_jadwal.'</button>
<h4 style="text-align: center; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">'.$nama_laporan.'</h4>
<div id="wrap-table">
<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width: 100%; table-layout: fixed; overflow-wrap: break-word; font-size: 60%; border: 0;">
    <thead>
        <tr>    
            <th class="atas kiri kanan bawah text_tengah" style=" width:35px;">No</th>
            <th class="atas kiri kanan bawah text_tengah" style=" width:300px;">Kode</th>
            <th class="atas kiri kanan bawah text_tengah">Program</th>
            <th class="atas kiri kanan bawah text_tengah" style="width:100px;">SKPD</th>
            <th class="atas kiri kanan bawah text_tengah" style=" width:400px;">Pagu</th>
        </tr>
    </thead>
    <tbody>
        '.$body.'
        <tr>
            <td class="kiri kanan bawah text_kanan text_blok" colspan="4">Jumlah</td>
            <td class="kiri kanan bawah text_kanan text_blok">'.number_format($data_all['total'],0,",",".").'</td>
        </tr>
    </tbody>
</table>
</div>';
echo '</div>';
?>
<style type="text/css">
    #wrap-table {
        overflow: auto;
        height: 100vh;
    }
    @media  print {
        #wrap-table {
            overflow: none;
            height: auto;
        }
    }
</style>
<script type="text/javascript">
    jQuery(document).ready(function(){
        run_download_excel();
    });
</script>