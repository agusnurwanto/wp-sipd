<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$input = shortcode_atts( array(
    'tahun_anggaran' => '2022',
    'id_skpd' => '',
    'id_kec' => '',
    'id_kel' => ''
), $atts );

function link_detail($link_admin, $jenis){
    return "<a target='_blank' href='".$link_admin."?".$jenis['key']."=".$jenis['value']."'>".$jenis['label']."</a>";
}

function generateRandomColor($k){
    $color = array('#f44336', '#9c27b0', '#2196f3', '#009688', '#4caf50', '#cddc39', '#ff9800', '#795548', '#9e9e9e', '#607d8b');
    return $color[$k%10];
}

if(empty($input['id_kel']) && empty($input['id_skpd'])){
    die('<h1 class="text-center">id_skpd, id_kec dan id_kel tidak boleh kosong!</h1>');
}else if(!empty($input['id_skpd'])){
    $id_kab = get_option('_crb_id_lokasi_kokab');
    $unit = $wpdb->get_row($wpdb->prepare("
        SELECT 
            nama_skpd, 
            id_skpd, 
            kode_skpd, 
            nipkepala 
        from data_unit 
        where active=1 
            and tahun_anggaran=%d 
            and is_skpd=1 
            and id_skpd = %d 
        order by kode_skpd ASC
    ", $input['tahun_anggaran'], $input['id_skpd']), ARRAY_A);
    if(empty($unit)){
        die('<h1 class="text-center">Data id_skpd = '.$input['id_skpd'].' tidak ditemukan!</h1>'.$wpdb->last_query);
    }
    $nama_kec = str_replace('kecamatan ', '', strtolower($unit['nama_skpd']));
    $id_kec = $wpdb->get_var("
        SELECT 
            id_alamat 
        from data_alamat 
        where tahun=".$input['tahun_anggaran']." 
            and is_kec=1 
            and id_kab=".$id_kab." 
            and nama='".$nama_kec."'
    ");
    if(empty($id_kec)){
        die('<h1 class="text-center">Data kecamatan dari id_skpd = '.$input['id_skpd'].' tidak ditemukan!</h1>'.$wpdb->last_query);
    }
    $desa = $wpdb->get_results("
        SELECT 
            id_alamat,
            nama 
        from data_alamat 
        where tahun=".$input['tahun_anggaran']." 
            and is_kel=1  
            and id_kec=".$id_kec."
    ", ARRAY_A);
    if (empty($desa)) {
        die('<h1 class="text-center">Desa dengan id_kec ='.$input['id_kec'].' tidak ditemukan!</h1>'.$wpdb->last_query);
    }else{
        echo '<ul>';
        foreach($desa as $val){
            $url_skpd = $this->generatePage($val['nama'].' | '.$input['tahun_anggaran'], $input['tahun_anggaran'], '[monitor_keu_pemdes tahun_anggaran="'.$input['tahun_anggaran'].'" id_kec="'.$id_kec.'" id_kel="'.$val['id_alamat'].'"]');
            echo '<li><a target="_blank" href="'.$url_skpd.'">'.$val['nama'].' | '.$input['tahun_anggaran'].'</a>';
        }  
        echo '</ul>'; exit;
    }
}

$desa = $wpdb->get_row("
    SELECT 
        id_kec,
        nama 
    from data_alamat 
    where tahun=".$input['tahun_anggaran']." 
        and is_kel=1  
        and id_alamat=".$input['id_kel']."
", ARRAY_A);

$kecamatan = $wpdb->get_row("
    SELECT 
        nama 
    from data_alamat 
    where tahun=".$input['tahun_anggaran']." 
        and is_kec=1  
        and id_alamat=".$desa['id_kec']."
", ARRAY_A);

$bkk_infrastruktur = $wpdb->get_row($wpdb->prepare('
    SELECT 
        desa,
        kecamatan, 
        sum(total) as total 
    from data_bkk_desa 
    WHERE tahun_anggaran=%d
        and active=1
        and id_desa=%d
    group by desa
', $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bkk_infrastruktur)) {
    $bkk_infrastruktur = array('total' => 0);
}

$bhpd = $wpdb->get_row($wpdb->prepare('
    SELECT 
        desa,
        kecamatan, 
        sum(total) as total 
    from data_bhpd_desa 
    WHERE tahun_anggaran=%d
        and active=1
        and id_desa=%d
    group by desa
', $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bhpd)) {
    $bhpd = array('total' => 0);
}

$bhrd = $wpdb->get_row($wpdb->prepare('
    SELECT 
        desa,
        kecamatan, 
        sum(total) as total 
    from data_bhrd_desa 
    WHERE tahun_anggaran=%d
        and active=1
        and id_desa=%d
    group by desa
', $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bhrd)) {
    $bhrd = array('total' => 0);
}

$bku_dd = $wpdb->get_row($wpdb->prepare('
    SELECT 
        desa,
        kecamatan, 
        sum(total) as total 
    from data_bku_dd_desa 
    WHERE tahun_anggaran=%d
        and active=1
        and id_desa=%d
    group by desa
', $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bku_dd)) {
    $bku_dd = array('total' => 0);
}

$bku_add = $wpdb->get_row($wpdb->prepare('
    SELECT 
        desa,
        kecamatan, 
        sum(total) as total 
    from data_bku_add_desa 
    WHERE tahun_anggaran=%d
        and active=1
        and id_desa=%d
    group by desa
', $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bku_add)) {
    $bku_add = array('total' => 0);
}

$bkk_pilkades = $wpdb->get_row($wpdb->prepare('
    SELECT 
        desa,
        kecamatan, 
        sum(total) as total 
    from data_bkk_pilkades_desa 
    WHERE tahun_anggaran=%d
        and active=1
        and id_desa=%d
    group by desa
', $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bkk_pilkades)) {
    $bkk_pilkades = array('total' => 0);
}

$bkk_infrastruktur_r = $wpdb->get_row($wpdb->prepare("
    SELECT 
        SUM(p.total_pencairan) as total
    FROM data_pencairan_bkk_desa p
    INNER JOIN data_bkk_desa b on p.id_kegiatan=b.id
        AND b.active=1
        AND b.tahun_anggaran=%d
    WHERE b.id_desa=%d
    ", $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bkk_infrastruktur_r)) {
    $bkk_infrastruktur_r = array('total' => 0);
}

$bkk_pilkades_r = $wpdb->get_row($wpdb->prepare("
    SELECT 
        SUM(p.total_pencairan) as total
    FROM data_pencairan_bkk_pilkades_desa p
    INNER JOIN data_bkk_pilkades_desa b on p.id_bkk_pilkades=b.id
        AND b.active=1
        AND b.tahun_anggaran=%d
    WHERE b.id_desa=%d
    ", $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bkk_pilkades_r)) {
    $bkk_pilkades_r = array('total' => 0);
}

$bhpd_r = $wpdb->get_row($wpdb->prepare("
    SELECT 
        SUM(p.total_pencairan) as total
    FROM data_pencairan_bhpd_desa p
    INNER JOIN data_bhpd_desa b on p.id_bhpd=b.id
        AND b.active=1
        AND b.tahun_anggaran=%d
    WHERE b.id_desa=%d
    ", $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bhpd_r)) {
    $bhpd_r = array('total' => 0);
}

$bhrd_r = $wpdb->get_row($wpdb->prepare("
    SELECT 
        SUM(p.total_pencairan) as total
    FROM data_pencairan_bhrd_desa p
    INNER JOIN data_bhrd_desa b on p.id_bhrd=b.id
        AND b.active=1
        AND b.tahun_anggaran=%d
    WHERE b.id_desa=%d
    ", $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bhrd_r)) {
    $bhrd_r = array('total' => 0);
}

$bku_dd_r = $wpdb->get_row($wpdb->prepare("
    SELECT 
        SUM(p.total_pencairan) as total
    FROM data_pencairan_bku_dd_desa p
    INNER JOIN data_bku_dd_desa b on p.id_bku_dd=b.id
        AND b.active=1
        AND b.tahun_anggaran=%d
    WHERE b.id_desa=%d
    ", $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bku_dd_r)) {
    $bku_dd_r = array('total' => 0);
}

$bku_add_r = $wpdb->get_row($wpdb->prepare("
    SELECT 
        SUM(p.total_pencairan) as total
    FROM data_pencairan_bku_add_desa p
    INNER JOIN data_bku_add_desa b on p.id_bku_add=b.id
        AND b.active=1
        AND b.tahun_anggaran=%d
    WHERE b.id_desa=%d
    ", $input['tahun_anggaran'], $input['id_kel']), ARRAY_A);
if (empty($bku_add_r)) {
    $bku_add_r = array('total' => 0);
}

$bkk_infrastruktur_b = $bkk_infrastruktur['total'] - $bkk_infrastruktur_r['total'];
$bkk_pilkades_b = $bkk_pilkades['total'] - $bkk_pilkades_r['total'];
$bhpd_b = $bhpd['total'] - $bhpd_r['total'];
$bhrd_b = $bhrd['total'] - $bhrd_r['total'];
$bku_dd_b = $bku_dd['total'] - $bku_dd_r['total'];
$bku_add_b = $bku_add['total'] - $bku_add_r['total'];


$bkk_infrastruktur_p = 0;
if($bkk_infrastruktur['total'] > 0 && $bkk_infrastruktur_r['total'] > 0){
    $bkk_infrastruktur_p = round(($bkk_infrastruktur_r['total']/$bkk_infrastruktur['total'])*100, 2);
}
$bkk_pilkades_p = 0;
if($bkk_pilkades['total'] > 0 && $bkk_pilkades_r['total'] > 0){
    $bkk_pilkades_p = round(($bkk_pilkades_r['total']/$bkk_pilkades['total'])*100, 2);
}
$bhpd_p = 0;
if($bhpd['total'] > 0 && $bhpd_r['total'] > 0){
    $bhpd_p = round(($bhpd_r['total']/$bhpd['total'])*100, 2);
}
$bhrd_p = 0;
if($bhrd['total'] > 0 && $bhrd_r['total'] > 0){
    $bhrd_p = round(($bhrd_r['total']/$bhrd['total'])*100, 2);
}
$bku_dd_p = 0;
if($bku_dd['total'] > 0 && $bku_dd_r['total'] > 0){
    $bku_dd_p = round(($bku_dd_r['total']/$bku_dd['total'])*100, 2);
}
$bku_add_p = 0;
if($bku_add['total'] > 0 && $bku_add_r['total'] > 0){
    $bku_add_p = round(($bku_add_r['total']/$bku_add['total'])*100, 2);
}

$total_all = $bkk_infrastruktur['total'] + $bkk_pilkades['total'] + $bhpd['total'] + $bhrd['total'] + $bku_dd['total'] + $bku_add['total'];
$realisasi_all = $bkk_infrastruktur_r['total'] + $bkk_pilkades_r['total'] + $bhpd_r['total'] + $bhrd_r['total'] + $bku_dd_r['total'] + $bku_add_r['total'];
$belum_all = $total_all-$realisasi_all;
$persen_all = 0;
if($total_all > 0 && $realisasi_all > 0){
    $persen_all = round(($realisasi_all/$total_all)*100, 2);
}
// print_r($body);die($wpdb->last_query);
?>

<h1 class="text-center">Laporan<br>Desa <?php echo $desa['nama'] ?> Kecamatan <?php echo $kecamatan['nama'] ?><br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
<div class="cetak container-fluid">
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
                <td>BKK Infrastruktur</td>
                <td class="text-right"><?php echo number_format($bkk_infrastruktur['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bkk_infrastruktur_r['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bkk_infrastruktur_b,0,",","."); ?></td>
                <th class="text-center"><?php echo $bkk_infrastruktur_p; ?>%</th>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>BKK PILKADES</td>
                <td class="text-right"><?php echo number_format($bkk_pilkades['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bkk_pilkades_r['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bkk_pilkades_b,0,",","."); ?></td>
                <th class="text-center"><?php echo $bkk_pilkades_p; ?>%</th>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>BHPD</td>
                <td class="text-right"><?php echo number_format($bhpd['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bhpd_r['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bhpd_b,0,",","."); ?></td>
                <th class="text-center"><?php echo $bhpd_p; ?>%</th>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>BHRD</td>
                <td class="text-right"><?php echo number_format($bhrd['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bhrd_r['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bhrd_b,0,",","."); ?></td>
                <th class="text-center"><?php echo $bhrd_p; ?>%</th>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>BKU DD</td>
                <td class="text-right"><?php echo number_format($bku_dd['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bku_dd_r['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bku_dd_b,0,",","."); ?></td>
                <th class="text-center"><?php echo $bku_dd_p; ?>%</th>
            </tr>
            <tr>
                <td class="text-center">6</td>
                <td>BKU ADD</td>
                <td class="text-right"><?php echo number_format($bku_add['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bku_add_r['total'],0,",","."); ?></td>
                <td class="text-right"><?php echo number_format($bku_add_b,0,",","."); ?></td>
                <th class="text-center"><?php echo $bku_add_p; ?>%</th>
            </tr>
        </tbody>
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