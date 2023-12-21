<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;


$input = shortcode_atts( array(
    'tahun_anggaran' => '2023',
    'id_skpd' => '',
    'id_kec' => '',
    'id_kel' => ''
), $atts );


if(!empty($_GET) && !empty($_GET['tahun'])){
    $input['tahun_anggaran'] = $_GET['tahun'];
}

function generateRandomColor($k){
    $color = array('#f44336', '#9c27b0', '#2196f3', '#009688', '#4caf50', '#cddc39', '#ff9800', '#795548', '#9e9e9e', '#607d8b');
    return $color[$k%10];
}

$tampil_pilkades = get_option("_bkk_pilkades_".$input['tahun_anggaran']);
$jenis_keuangan = ''; 
/*
    1 = BKK Infrastruktur
    2 = BKK Pilkades
    3 = BHPD
    4 = BHRD
    5 = BKU ADD
    6 = BKU DD
*/
if(!empty($_GET['jenis_keuangan'])){
    $jenis_keuangan = $_GET['jenis_keuangan'];
}
$title = 'Jenis Keuangan';
$shortcode = '[desa_per_jenis_keuangan]';
$url_per_jenis_keuangan = $this->generatePage($title, false, $shortcode, false);

if(empty($input['id_kel']) && empty($input['id_skpd'])){
    die('<h1 class="text-center">id_skpd, id_kec dan id_kel tidak boleh kosong!</h1>');
}
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
}

// grafik desa
$chart_desa = array(
    'label' => array(),
    'label1' => 'Anggaran',
    'label2' => 'Realisasi',
    'data1'  => array(),
    'color1' => array(),
    'data2'  => array(),
    'color2' => array()
);

$total_all = 0;
$belum_all = 0;
$realisasi_all = 0;
$persen_all = 0;

if($jenis_keuangan == 1) {
    $nama_jenis_keuangan = '<span>Bantuan Keuangan Khusus Infrastruktur</span>';
}elseif($jenis_keuangan == 2) {
    $nama_jenis_keuangan = '<span>Bantuan Keuangan Khusus Pemilihan Kepala Desa</span>';
}elseif($jenis_keuangan == 3) {
    $nama_jenis_keuangan = '<span>Bagi Hasil Pajak Daerah</span>';
}elseif($jenis_keuangan == 4) {
    $nama_jenis_keuangan = '<span>Bagi Hasil Retribusi Daerah</span>';
}elseif($jenis_keuangan == 5) {
    $nama_jenis_keuangan = '<span">BKU Alokasi Dana Desa</span>';
}elseif($jenis_keuangan == 6) {
    $nama_jenis_keuangan = '<span>BKU Dana Desa</span>';
}

$body = '';
$no = 0;
foreach($desa as $val){
    $no++;

    $anggaran = array('total' => 0);
    $realisasi = array('total' => 0);
    $belum = 0;
    $persen = 0;
    if($jenis_keuangan == 1) {
        $anggaran = $wpdb->get_row($wpdb->prepare('
            SELECT 
                desa,
                id_desa,
                kecamatan, 
                sum(total) as total 
            from data_bkk_desa 
            WHERE tahun_anggaran=%d
                and active=1
                and (
                    id_desa=%d
                    or (desa=%s and kecamatan=%s)
                )
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($anggaran)) {
            $anggaran = array('total' => 0);
        }else if(empty($anggaran['id_desa'])){
            $wpdb->update('data_bkk_desa', array(
                'id_desa' => $val['id_alamat'],
                'id_kecamatan' => $desa['id_kec']
            ), array(
                'active' => 1,
                'tahun_anggaran' => $input['tahun_anggaran'],
                'desa' => $val['nama'],
                'kecamatan' => $unit['nama_skpd']
            ));
        }
    }elseif($jenis_keuangan == 2) {
        $anggaran = $wpdb->get_row($wpdb->prepare('
            SELECT 
                desa,
                id_desa,
                kecamatan, 
                sum(total) as total 
            from data_bkk_pilkades_desa 
            WHERE tahun_anggaran=%d
                and active=1
                and (
                    id_desa=%d
                    or (desa=%s and kecamatan=%s)
                )
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($anggaran)) {
            $anggaran = array('total' => 0);
        }else if(empty($anggaran['id_desa'])){
            $wpdb->update('data_bkk_pilkades_desa', array(
                'id_desa' => $val['id_alamat'],
                'id_kecamatan' => $desa['id_kec']
            ), array(
                'active' => 1,
                'tahun_anggaran' => $input['tahun_anggaran'],
                'desa' => $val['nama'],
                'kecamatan' => $unit['nama_skpd']
            ));
        }
    }elseif($jenis_keuangan == 3) {
        $anggaran = $wpdb->get_row($wpdb->prepare('
            SELECT 
                desa,
                id_desa,
                kecamatan, 
                sum(total) as total 
            from data_bhpd_desa 
            WHERE tahun_anggaran=%d
                and active=1
                and (
                    id_desa=%d
                    or (desa=%s and kecamatan=%s)
                )
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($anggaran)) {
            $anggaran = array('total' => 0);
        }else if(empty($anggaran['id_desa'])){
            $wpdb->update('data_bhpd_desa', array(
                'id_desa' => $val['id_alamat'],
                'id_kecamatan' => $desa['id_kec']
            ), array(
                'active' => 1,
                'tahun_anggaran' => $input['tahun_anggaran'],
                'desa' => $val['nama'],
                'kecamatan' => $unit['nama_skpd']
            ));
        }
    }elseif($jenis_keuangan == 4) {
        $anggaran = $wpdb->get_row($wpdb->prepare('
            SELECT 
                desa,
                id_desa,
                kecamatan, 
                sum(total) as total 
            from data_bhrd_desa 
            WHERE tahun_anggaran=%d
                and active=1
                and (
                    id_desa=%d
                    or (desa=%s and kecamatan=%s)
                )
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($anggaran)) {
            $anggaran = array('total' => 0);
        }else if(empty($anggaran['id_desa'])){
            $wpdb->update('data_bhrd_desa', array(
                'id_desa' => $val['id_alamat'],
                'id_kecamatan' => $desa['id_kec']
            ), array(
                'active' => 1,
                'tahun_anggaran' => $input['tahun_anggaran'],
                'desa' => $val['nama'],
                'kecamatan' => $unit['nama_skpd']
            ));
        }
    }elseif($jenis_keuangan == 5) {
        $anggaran = $wpdb->get_row($wpdb->prepare('
            SELECT 
                desa,
                id_desa,
                kecamatan, 
                sum(total) as total 
            from data_bku_add_desa 
            WHERE tahun_anggaran=%d
                and active=1
                and (
                    id_desa=%d
                    or (desa=%s and kecamatan=%s)
                )
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($anggaran)) {
            $anggaran = array('total' => 0);
        }else if(empty($anggaran['id_desa'])){
            $wpdb->update('data_bku_add_desa', array(
                'id_desa' => $val['id_alamat'],
                'id_kecamatan' => $desa['id_kec']
            ), array(
                'active' => 1,
                'tahun_anggaran' => $input['tahun_anggaran'],
                'desa' => $val['nama'],
                'kecamatan' => $unit['nama_skpd']
            ));
        }
    }elseif($jenis_keuangan == 6) {
        $anggaran = $wpdb->get_row($wpdb->prepare('
            SELECT 
                desa,
                id_desa,
                kecamatan, 
                sum(total) as total 
            from data_bku_dd_desa 
            WHERE tahun_anggaran=%d
                and active=1
                and (
                    id_desa=%d
                    or (desa=%s and kecamatan=%s)
                )
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($anggaran)) {
            $anggaran = array('total' => 0);
        }else if(empty($anggaran['id_desa'])){
            $wpdb->update('data_bku_dd_desa', array(
                'id_desa' => $val['id_alamat'],
                'id_kecamatan' => $desa['id_kec']
            ), array(
                'active' => 1,
                'tahun_anggaran' => $input['tahun_anggaran'],
                'desa' => $val['nama'],
                'kecamatan' => $unit['nama_skpd']
            ));
        }
    }

    if($jenis_keuangan == 1) {
        $realisasi = $wpdb->get_row($wpdb->prepare('
            SELECT 
                SUM(p.total_pencairan) as total
            FROM data_pencairan_bkk_desa p
            INNER JOIN data_bkk_desa b on p.id_kegiatan=b.id
                AND b.active=1
                AND b.tahun_anggaran=%d
            WHERE b.id_desa=%d
                or (b.desa=%s and b.kecamatan=%s)
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($realisasi)) {
            $realisasi = array('total' => 0);
        }
    }elseif($jenis_keuangan == 2) {
        $realisasi = $wpdb->get_row($wpdb->prepare('
            SELECT 
                SUM(p.total_pencairan) as total
            FROM data_pencairan_bkk_pilkades_desa p
            INNER JOIN data_bkk_pilkades_desa b on p.id_bkk_pilkades=b.id
                AND b.active=1
                AND b.tahun_anggaran=%d
            WHERE b.id_desa=%d
                or (b.desa=%s and b.kecamatan=%s)
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($realisasi)) {
            $realisasi = array('total' => 0);
        }
    }elseif($jenis_keuangan == 3) {
        $realisasi = $wpdb->get_row($wpdb->prepare('
            SELECT 
                SUM(p.total_pencairan) as total
            FROM data_pencairan_bhpd_desa p
            INNER JOIN data_bhpd_desa b on p.id_bhpd=b.id
                AND b.active=1
                AND b.tahun_anggaran=%d
            WHERE b.id_desa=%d
                or (b.desa=%s and b.kecamatan=%s)
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($realisasi)) {
            $realisasi = array('total' => 0);
        }
    }elseif($jenis_keuangan == 4) {
        $realisasi = $wpdb->get_row($wpdb->prepare('
            SELECT 
                SUM(p.total_pencairan) as total
            FROM data_pencairan_bhrd_desa p
            INNER JOIN data_bhrd_desa b on p.id_bhrd=b.id
                AND b.active=1
                AND b.tahun_anggaran=%d
            WHERE b.id_desa=%d
                or (b.desa=%s and b.kecamatan=%s)
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($realisasi)) {
            $realisasi = array('total' => 0);
        }
    }elseif($jenis_keuangan == 5) {
        $realisasi = $wpdb->get_row($wpdb->prepare('
            SELECT 
                SUM(p.total_pencairan) as total
            FROM data_pencairan_bku_add_desa p
            INNER JOIN data_bku_add_desa b on p.id_bku_add=b.id
                AND b.active=1
                AND b.tahun_anggaran=%d
            WHERE b.id_desa=%d
                or (b.desa=%s and b.kecamatan=%s)
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($realisasi)) {
            $realisasi = array('total' => 0);
        }
    }elseif($jenis_keuangan == 6) {
        $realisasi = $wpdb->get_row($wpdb->prepare('
            SELECT 
                SUM(p.total_pencairan) as total
            FROM data_pencairan_bku_dd_desa p
            INNER JOIN data_bku_dd_desa b on p.id_bku_dd=b.id
                AND b.active=1
                AND b.tahun_anggaran=%d
            WHERE b.id_desa=%d
                or (b.desa=%s and b.kecamatan=%s)
            group by desa
        ', $input['tahun_anggaran'], $val['id_alamat'], $val['nama'], $unit['nama_skpd']), ARRAY_A);
        if (empty($realisasi)) {
            $realisasi = array('total' => 0);
        }
    }
$belum = $anggaran['total'] - $realisasi['total'];
$persen = 0;
if($anggaran['total'] > 0 && $realisasi['total'] > 0){
    $persen = round(($realisasi['total']/$anggaran['total'])*100, 2);
}
 
$total_all += $anggaran['total'];
$realisasi_all += $realisasi['total'];
$belum_all = $total_all-$realisasi_all;
$persen_all = 0;
if($total_all > 0 && $realisasi_all > 0){
    $persen_all = round(($realisasi_all/$total_all)*100, 2);
}
    // print_r($realisasi['total']); die($wpdb->last_query);
    $url_skpd = $this->generatePage($val['nama'].' | '.$input['tahun_anggaran'],'"]');
    $body .= '
        <tr>
            <td class="text-center">'.$no.'</td>
            <td><a target="_blank" href="'.$url_skpd.'">'.$val['nama'].'</a></td>
            <td class="text-right">'.number_format($anggaran['total'], 0,",", ".").'</td>
            <td class="text-right">'.number_format($realisasi['total'], 0,",", ".").'</td>
            <td class="text-right">'.number_format($belum, 0,",", ".").'</td>
            <td class="text-center">'.$persen.'%</td>
        </tr>
    ';

    $chart_desa['label'][] = $val['nama'];
    $chart_desa['data1'][] = $anggaran['total'];
    $chart_desa['color1'][] = generateRandomColor(1);
    $chart_desa['data2'][] = $realisasi['total'];
    $chart_desa['color2'][] = generateRandomColor(2);
}  
echo '</ul>';
?>
<h1 class="text-center">Laporan <?php echo $nama_jenis_keuangan ?><br><?php echo $unit['nama_skpd']; ?><br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
<div class="cetak">
    <div style="padding: 5px;">
        <div class="row">
            <div class="col-md-12">
                <div style="width: 100%; margin:auto 5px;">
                    <canvas id="chart_per_desa"></canvas>
                </div>
            </div>
        </div>
        <h1 class="text-center">Tabel Laporan <?php echo $nama_jenis_keuangan ?><br><?php echo $unit['nama_skpd']; ?><br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
        <div class="cetak container-fluid">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="atas kanan bawah kiri text_tengah text_blok" style="width: 30px;">No</th>
                        <th class="atas kanan bawah text_tengah text_blok" >Desa</th>
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
    </div>
</div>
<script type="text/javascript">
window.desa = <?php echo json_encode($chart_desa); ?>;
window.pieChartdesa = new Chart(document.getElementById('chart_per_desa'), {
    type: 'bar',
    data: {
        labels: desa.label,
        datasets: [
            {
                label: desa.label1,
                data: desa.data1,
                backgroundColor: desa.color1
            },
            {
                label: desa.label2,
                data: desa.data2,
                backgroundColor: desa.color2
            }
        ]
    }
});
</script>
