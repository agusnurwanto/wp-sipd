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

$api_key = get_option('_crb_api_key_extension');
$nama_pemda = get_option('_crb_daerah');
$current_user = wp_get_current_user();
$bulan = date('m');

$sql = $wpdb->prepare("
	select 
		*
	from data_unit
	WHERE tahun_anggaran = %d
		and active = 1
		and is_skpd = 1
	order by kode_skpd asc
", $input['tahun_anggaran']);

$unit = $wpdb->get_results($sql, ARRAY_A);

$tahun_awal_jadwal = 2024;
$tahun_akhir_jadwal = 2026;
$lama_pelaksanaan = 3;

$body_monev = '';

$no_opd = 0;
$no_sub_opd = 0;
$no_program = $wpdb->get_results($wpdb->prepare("
    SELECT 
        count(k.id)
    FROM data_sub_keg_bl k
    WHERE k.tahun_anggaran = %d
        AND k.active = 1
        and k.pagu > 0
    GROUP by k.kode_program
", $input['tahun_anggaran']), ARRAY_A);
$no_program = count($no_program);
$no_kegiatan = $wpdb->get_results($wpdb->prepare("
    SELECT 
        count(k.id)
    FROM data_sub_keg_bl k
    WHERE k.tahun_anggaran = %d
        AND k.active = 1
        and k.pagu > 0
    GROUP by k.kode_giat
", $input['tahun_anggaran']), ARRAY_A);
$no_kegiatan = count($no_kegiatan);
$no_sub_kegiatan = $wpdb->get_results($wpdb->prepare("
    SELECT 
        count(k.id)
    FROM data_sub_keg_bl k
    WHERE k.tahun_anggaran = %d
        AND k.active = 1
        and k.pagu > 0
    GROUP by k.kode_sub_giat
", $input['tahun_anggaran']), ARRAY_A);
$no_sub_kegiatan = count($no_sub_kegiatan);

// $total_all_realisasi_triwulan_1 = 0;
// $total_all_realisasi_triwulan_2 = 0;
// $total_all_realisasi_triwulan_3 = 0;
// $total_all_realisasi_triwulan_4 = 0;

// $total_all_pagu_triwulan_1 = 0;
// $total_all_pagu_triwulan_2 = 0;
// $total_all_pagu_triwulan_3 = 0;
// $total_all_pagu_triwulan_4 = 0;

// $total_pagu_triwulan = 0;
// // $total_all_realisasi_triwulan = 0;
// $total_all_pagu = 0;
// $total_all_selisih = 0;
// $persen_all = 0;
$no = 0;

foreach ($unit as $skpd) {
    $total_all_tahun = [];

    $data_renstra_sub_kegiatan = $wpdb->get_row(
        $wpdb->prepare("
            SELECT 
                sum(pagu_1) as pagu_1,
                sum(pagu_2) as pagu_2,
                sum(pagu_3) as pagu_3,
                sum(pagu_4) as pagu_4,
                sum(pagu_5) as pagu_5,
                sum(realisasi_pagu_1) as realisasi_pagu_1,
                sum(realisasi_pagu_2) as realisasi_pagu_2,
                sum(realisasi_pagu_3) as realisasi_pagu_3,
                sum(realisasi_pagu_4) as realisasi_pagu_4,
                sum(realisasi_pagu_5) as realisasi_pagu_5
            FROM data_renstra_sub_kegiatan
            WHERE tahun_anggaran = %d
                AND id_unit = %d
                AND active = 1
        ", $input['tahun_anggaran'], $skpd['id_skpd']),
        ARRAY_A
    );

    $pagu_1 = $data_renstra_sub_kegiatan['pagu_1'];
    $pagu_2 = $data_renstra_sub_kegiatan['pagu_2'];
    $pagu_3 = $data_renstra_sub_kegiatan['pagu_3'];
    $pagu_4 = $data_renstra_sub_kegiatan['pagu_4'];
    $pagu_5 = $data_renstra_sub_kegiatan['pagu_5'];

    $realisasi_pagu_1 = $data_renstra_sub_kegiatan['realisasi_pagu_1'];
    $realisasi_pagu_2 = $data_renstra_sub_kegiatan['realisasi_pagu_2'];
    $realisasi_pagu_3 = $data_renstra_sub_kegiatan['realisasi_pagu_3'];
    $realisasi_pagu_4 = $data_renstra_sub_kegiatan['realisasi_pagu_4'];
    $realisasi_pagu_5 = $data_renstra_sub_kegiatan['realisasi_pagu_5'];

    $pagu_arr = array(
        $pagu_1,
        $pagu_2,
        $pagu_3,
        $pagu_4,
        $pagu_5
    );
    $realisasi_pagu_arr = array(
        $realisasi_pagu_1,
        $realisasi_pagu_2,
        $realisasi_pagu_3,
        $realisasi_pagu_4,
        $realisasi_pagu_5
    );

    $total_all_pagu = 0;
    $total_all_realisasi_pagu = 0;
    $total_all_target = 0;
    $total_all_realisasi_target = 0;

    $persen = $total_all_pagu > 0 ? round($total_all_realisasi_pagu / $total_all_pagu * 100, 2) : 0;
    $warning = '';
    if ($persen > 100) {
        $warning = 'bg-danger';
    }
    $selisih = $total_all_pagu - $total_all_realisasi_pagu;
    $no++;
    $url_skpd = $this->generatePage('MONEV ' . $skpd['nama_skpd'] . ' ' . $skpd['kode_skpd'] . ' | ' . $input['tahun_anggaran'], $input['tahun_anggaran'], '[monitor_monev_renstra tahun_anggaran="' . $input['tahun_anggaran'] . '" id_skpd="' . $skpd['id_skpd'] . '"]');

    $body_monev .= '
        <tr class="' . $warning . '">
        <td class="atas kanan bawah kiri text_tengah">' . $no . '</td>
        <td class="atas kanan bawah kiri text_kiri"><a target="_blank" href="' . $url_skpd . '">' . $skpd['nama_skpd'] . '</a></td>';
    
    for ($i = 0; $i < $lama_pelaksanaan; $i++) {
        $body_monev.='<td class="kanan bawah text_kanan">'.$pagu_arr[$i].'</td>';
        $body_monev.='<td class="kanan bawah text_kanan">'.$realisasi_pagu_arr[$i].'</td>';
    }        
    $body_monev .= '
        </tr>
    ';
}

$persen_all = $total_all_pagu > 0 ? round($total_all_realisasi_pagu / $total_all_pagu * 100, 2) : 0;

$string_hari_ini = date('H:i, d') . ' ' . $this->get_bulan() . ' ' . date('Y');

?>
<style type="text/css">
    #tabel-monitor-monev-renstra {
        font-family: \'Open Sans\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', sans-serif;
        border-collapse: collapse;
        font-size: 70%;
        border: 0;
        table-layout: fixed;
    }

    #tabel-monitor-monev-renstra th {
        vertical-align: middle;
    }

    #tabel-monitor-monev-renstra td,
    #tabel-monitor-monev-renstra th {
        padding: 0.3em 0.3em;
    }

    #tabel-monitor-monev-renstra thead {
        position: sticky;
        top: -6px;
        background: #ffc491;
    }

    #tabel-monitor-monev-renstra tfoot {
        position: sticky;
        bottom: -6px;
        background: #ffc491;
    }
</style>
<h1 class="text-center">Monitor dan Evaluasi Renstra<br><?php echo 'Tahun ' . $input['tahun_anggaran'] . '<br>' . $nama_pemda; ?></h1>
<h4 class="text-center"><?php echo $string_hari_ini; ?></h4>
<div id='aksi-wp-sipd'></div>
<h4 class="text-center">Tabel Monitoring dan Evaluasi Renstra</h4>
<div id="cetak" title="Monitor Monev Renstra" style="padding: 5px; overflow: auto; max-height: 80vh;">
    <table id="tabel-monitor-monev-renstra" cellpadding="2" cellspacing="0" contenteditable="false">
        <thead>
            <tr>
                <th rowspan="2" class='atas kanan bawah kiri text_tengah text_blok' style="width: 40px;">No</th>
                <th rowspan="2" class='atas kanan bawah kiri text_tengah text_blok' style="width: 250px;">Nama SKPD</th>
                <?php
                $row_head = '';
                for ($i = 1; $i <= $lama_pelaksanaan; $i++) {
                    $row_head .= '<th style="width: 218px;" colspan="2" class="atas kanan bawah text_tengah text_blok">Tahun ' . $i . '</th>';
                }
                ?>
                <?php echo $row_head; ?>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Total Pagu</th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Total Realisasi Pagu</th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Selisih</th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok' style="width: 60px;">%</th>
            </tr>
            <tr>
                <?php
                $row_head2 = '';
                for ($i = 1; $i <= $lama_pelaksanaan; $i++) {
                    $row_head2 .= '
                        <th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Pagu</th>
                        <th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Realisasi Anggaran</th>';
                }
                ?>
                <?php echo $row_head2; ?>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_monev; ?>
        </tbody>
        <tfoot>
            <tr>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    jQuery(document).on('ready', function() {

        google.charts.load('current', {
            packages: ['corechart', 'bar']
        });
        // google.charts.setOnLoadCallback(drawColColors);
        run_download_excel('', '#aksi-wp-sipd');
    });

    function drawColColors() {
        var data_cart = [
            ['Tahun', 'Anggaran', 'Realisasi']
        ];

        var data = new google.visualization.arrayToDataTable(data_cart);

        var options = {
            title: 'ANGGARAN DAN REALISASI',
            colors: ['#ffc107', '#007bff'],
            hAxis: {
                title: 'Tahun',
                minValue: 0
            },
            vAxis: {
                title: 'NILAI'
            }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart'));
        chart.draw(data, options);
    }
</script>