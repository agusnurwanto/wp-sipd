<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$input = shortcode_atts( array(
    'tahun_anggaran' => '2023'
), $atts );

if(!empty($_GET) && !empty($_GET['tahun'])){
    $input['tahun_anggaran'] = $_GET['tahun'];
}

function generateRandomColor($k){
    $color = array('#f44336', '#9c27b0', '#2196f3', '#009688', '#4caf50', '#cddc39', '#ff9800', '#795548', '#9e9e9e', '#607d8b');
    return $color[$k%10];
}

$user_id = um_user( 'ID' );
$user_meta = get_userdata($user_id);
$cek_login = false;
$url_all_kec = array();
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
    group by nama_skpd 
    order by kode_skpd ASC
", $input['tahun_anggaran']), ARRAY_A);
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
}

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
$body = '';
$total_all = 0;
$belum_all = 0;
$realisasi_all = 0;
$persen_all = 0;
$id_kab = get_option('_crb_id_lokasi_kokab');
foreach($unit as $i => $kec){
    $url_skpd = $this->generatePage($kec['nama_skpd'].' '.$kec['kode_skpd'].' | '.$input['tahun_anggaran'], $input['tahun_anggaran'], '[monitor_keu_pemdes tahun_anggaran="'.$input['tahun_anggaran'].'" id_skpd="'.$kec['id_skpd'].'"]');
    $nama_kec = str_replace('kecamatan ', '', strtolower($kec['nama_skpd']));
    $url_all_kec[$nama_kec] = $url_skpd;

    $bkk_infrastruktur = $wpdb->get_row($wpdb->prepare('
        SELECT 
            kecamatan, 
            sum(total) as total 
        from data_bkk_desa 
        WHERE tahun_anggaran=%d
            and active=1
            and kecamatan=%s
    ', $input['tahun_anggaran'], $nama_kec), ARRAY_A);
    if (empty($bkk_infrastruktur)) {
        $bkk_infrastruktur = array('total' => 0);
    }

    $bhpd = $wpdb->get_row($wpdb->prepare('
        SELECT 
            kecamatan, 
            sum(total) as total 
        from data_bhpd_desa 
        WHERE tahun_anggaran=%d
            and active=1
            and kecamatan=%s
    ', $input['tahun_anggaran'], $nama_kec), ARRAY_A);
    if (empty($bhpd)) {
        $bhpd = array('total' => 0);
    }

    $bhrd = $wpdb->get_row($wpdb->prepare('
        SELECT 
            kecamatan, 
            sum(total) as total 
        from data_bhrd_desa 
        WHERE tahun_anggaran=%d
            and active=1
            and kecamatan=%s
    ', $input['tahun_anggaran'], $nama_kec), ARRAY_A);
    if (empty($bhrd)) {
        $bhrd = array('total' => 0);
    }

    $bku_dd = $wpdb->get_row($wpdb->prepare('
        SELECT 
            kecamatan, 
            sum(total) as total 
        from data_bku_dd_desa 
        WHERE tahun_anggaran=%d
            and active=1
            and kecamatan=%s
    ', $input['tahun_anggaran'], $nama_kec), ARRAY_A);
    if (empty($bku_dd)) {
        $bku_dd = array('total' => 0);
    }

    $bku_add = $wpdb->get_row($wpdb->prepare('
        SELECT 
            kecamatan, 
            sum(total) as total 
        from data_bku_add_desa 
        WHERE tahun_anggaran=%d
            and active=1
            and kecamatan=%s
    ', $input['tahun_anggaran'], $nama_kec), ARRAY_A);
    if (empty($bku_add)) {
        $bku_add = array('total' => 0);
    }

    $bkk_pilkades = $wpdb->get_row($wpdb->prepare('
        SELECT 
            kecamatan, 
            sum(total) as total 
        from data_bkk_pilkades_desa 
        WHERE tahun_anggaran=%d
            and active=1
            and kecamatan=%s
    ', $input['tahun_anggaran'], $nama_kec), ARRAY_A);
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
        WHERE b.kecamatan=%s
        ", $input['tahun_anggaran'], $nama_kec), ARRAY_A);
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
        WHERE b.kecamatan=%s
        ", $input['tahun_anggaran'], $nama_kec), ARRAY_A);
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
        WHERE b.kecamatan=%s
        ", $input['tahun_anggaran'], $nama_kec), ARRAY_A);
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
        WHERE b.kecamatan=%s
        ", $input['tahun_anggaran'], $nama_kec), ARRAY_A);
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
        WHERE b.kecamatan=%s
        ", $input['tahun_anggaran'], $nama_kec), ARRAY_A);
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
        WHERE b.kecamatan=%s
        ", $input['tahun_anggaran'], $nama_kec), ARRAY_A);
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

    $nama_kec_render = $nama_kec;
    if(
        $cek_login
        && !empty($url_all_kec[$nama_kec])
    ){
        $nama_kec_render = "<a href='".$url_all_kec[$nama_kec]."' target='_blank'>".$nama_kec."</a>";
    }

    $total = $bkk_infrastruktur['total'] + $bkk_pilkades['total'] + $bhpd['total'] + $bhrd['total'] + $bku_dd['total'] + $bku_add['total'];
    $realisasi = $bkk_infrastruktur_r['total'] + $bkk_pilkades_r['total'] + $bhpd_r['total'] + $bhrd_r['total'] + $bku_dd_r['total'] + $bku_add_r['total'];
    $belum_realisasi = $total-$realisasi;
    $persen = 0;
    if($total > 0 && $realisasi > 0){
        $persen = round(($realisasi/$total)*100, 2);
    }

    $body .= '
    <tr>
        <td class="text-center">'.($i+1).'</td>
        <td>'.$nama_kec_render.'</td>
        <td class="text-right">'.number_format($total,0,",",".").'</td>
        <td class="text-right">'.number_format($realisasi,0,",",".").'</td>
        <td class="text-right">'.number_format($belum_realisasi,0,",",".").'</td>
        <td class="text-center">'.$persen.'%</td>
    </tr>
    ';

    $total_all += $total;
    $realisasi_all += $realisasi;
    $belum_all += $total_all-$realisasi_all;
    $persen_all += 0;
    if($total_all > 0 && $realisasi_all > 0){
        $persen_all = round(($realisasi_all/$total_all)*100, 2);
    }

    $chart_kec['label'][] = $nama_kec;
    $chart_kec['data1'][] = $total;
    $chart_kec['color1'][] = generateRandomColor(1);
    $chart_kec['data2'][] = $realisasi;
    $chart_kec['color2'][] = generateRandomColor(2);
}
// print_r($body);die($wpdb->last_query);
?>

<h1 class="text-center">Laporan Realisasi Keuangan Desa<br>Rekapitulasi Per Kecamatan<br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
<div class="cetak">
    <div style="padding: 5px;">
        <div class="row">
            <div class="col-md-12">
                <div style="width: 100%; margin:auto 5px;">
                    <canvas id="chart_per_kec"></canvas>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">Tabel Laporan Realisasi Keuangan Desa<br>Rekapitulasi Per Kecamatan<br>Tahun <?php echo $input['tahun_anggaran']; ?></h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="atas kanan bawah kiri text_tengah text_blok" style="width: 30px;">No</th>
                            <th class="text-center">Kecamatan</th>
                            <th class="atas kanan bawah text_tengah text_blok">Anggaran</th>
                            <th class="atas kanan bawah text_tengah text_blok">Realisasi</th>
                            <th class="atas kanan bawah text_tengah text_blok">Belum Realisasi</th>
                            <th class="atas kanan bawah text_tengah text_blok">% Realisasi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php echo $body; ?>
                    </tbody>
                    <tfoot>
                        <th colspan="2">JUMLAH</th>
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