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

$nama_pemda = get_option('_crb_daerah');
$bulan = date('m');

$sql = $wpdb->prepare("
	SELECT 
		*
	FROM data_unit
	WHERE tahun_anggaran = %d
	  AND active = 1
	  AND is_skpd = 1
	ORDER BY kode_skpd ASC
", $input['tahun_anggaran']);

$unit = $wpdb->get_results($sql, ARRAY_A);

$tahun_awal_jadwal = 2024;
$tahun_akhir_jadwal = 2026;
$lama_pelaksanaan = 3;

$no_opd = 0;
$no_sub_opd = 0;
$no_program = $wpdb->get_results($wpdb->prepare("
    SELECT 
        COUNT(k.id)
    FROM data_sub_keg_bl k
    WHERE k.tahun_anggaran = %d
      AND k.active = 1
      AND k.pagu > 0
    GROUP by k.kode_program
", $input['tahun_anggaran']), ARRAY_A);
$no_program = count($no_program);
$no_kegiatan = $wpdb->get_results($wpdb->prepare("
    SELECT 
        count(k.id)
    FROM data_sub_keg_bl k
    WHERE k.tahun_anggaran = %d
      AND k.active = 1
      AND k.pagu > 0
    GROUP by k.kode_giat
", $input['tahun_anggaran']), ARRAY_A);
$no_kegiatan = count($no_kegiatan);
$no_sub_kegiatan = $wpdb->get_results($wpdb->prepare("
    SELECT 
        count(k.id)
    FROM data_sub_keg_bl k
    WHERE k.tahun_anggaran = %d
      AND k.active = 1
      AND k.pagu > 0
    GROUP by k.kode_sub_giat
", $input['tahun_anggaran']), ARRAY_A);
$no_sub_kegiatan = count($no_sub_kegiatan);

$string_hari_ini = date('H:i, d') . ' ' . $this->get_bulan() . ' ' . date('Y');
$no = 1;
$total_all_pagu_all_skpd = 0;
$total_all_realisasi_pagu_all_skpd = 0;

$total_all_pagu_1 = 0;
$total_all_pagu_2 = 0;
$total_all_pagu_3 = 0;
$total_all_pagu_4 = 0;
$total_all_pagu_5 = 0;

$total_all_realisasi_pagu_1 = 0;
$total_all_realisasi_pagu_2 = 0;
$total_all_realisasi_pagu_3 = 0;
$total_all_realisasi_pagu_4 = 0;
$total_all_realisasi_pagu_5 = 0;

$total_all_selisih = 0;

$body_monev = '';
foreach ($unit as $skpd) {
    $total_all_pagu_skpd = 0;
    $total_all_realisasi_pagu_skpd = 0;
    $selisih = 0;
    $persen = 0;

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

    $pagu_1 = $data_renstra_sub_kegiatan['pagu_1'] ?? 0;
    $pagu_2 = $data_renstra_sub_kegiatan['pagu_2'] ?? 0;
    $pagu_3 = $data_renstra_sub_kegiatan['pagu_3'] ?? 0;
    $pagu_4 = $data_renstra_sub_kegiatan['pagu_4'] ?? 0;
    $pagu_5 = $data_renstra_sub_kegiatan['pagu_5'] ?? 0;

    $realisasi_pagu_1 = $data_renstra_sub_kegiatan['realisasi_pagu_1'] ?? 0;
    $realisasi_pagu_2 = $data_renstra_sub_kegiatan['realisasi_pagu_2'] ?? 0;
    $realisasi_pagu_3 = $data_renstra_sub_kegiatan['realisasi_pagu_3'] ?? 0;
    $realisasi_pagu_4 = $data_renstra_sub_kegiatan['realisasi_pagu_4'] ?? 0;
    $realisasi_pagu_5 = $data_renstra_sub_kegiatan['realisasi_pagu_5'] ?? 0;

    $total_all_pagu_skpd = $pagu_1 + $pagu_2 + $pagu_3 + $pagu_4 + $pagu_5;
    $total_all_realisasi_pagu_skpd = $realisasi_pagu_1 + $realisasi_pagu_2 + $realisasi_pagu_3 + $realisasi_pagu_4 + $realisasi_pagu_5;
    $persen = $total_all_pagu_skpd > 0 ? round($total_all_realisasi_pagu_skpd / $total_all_pagu_skpd * 100, 2) : 0;
    $selisih = $total_all_pagu_skpd - $total_all_realisasi_pagu_skpd;

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

    $warning = '';
    if ($persen > 100) {
        $warning = 'bg-danger';
    }

    $url_skpd = $this->generatePage('MONEV ' . $skpd['nama_skpd'] . ' ' . $skpd['kode_skpd'] . ' | ' . $input['tahun_anggaran'], $input['tahun_anggaran'], '[monitor_monev_renstra tahun_anggaran="' . $input['tahun_anggaran'] . '" id_skpd="' . $skpd['id_skpd'] . '"]');
    $body_monev .= '
        <tr class="' . $warning . '">
            <td class="atas kanan bawah kiri text_tengah">' . $no++ . '</td>
            <td class="atas kanan bawah kiri text_kiri"><a target="_blank" href="' . $url_skpd . '">' . $skpd['nama_skpd'] . '</a></td>';

    for ($i = 0; $i < $lama_pelaksanaan; $i++) {
        $body_monev .= '<td class="kanan bawah text_kanan">' . $this->_number_format($pagu_arr[$i]) . '</td>';
        $body_monev .= '<td class="kanan bawah text_kanan">' .  $this->_number_format($realisasi_pagu_arr[$i]) . '</td>';
    }
    $body_monev .= '
            <td class="kanan bawah text_kanan">' .  $this->_number_format($total_all_pagu_skpd) . '</td>
            <td class="kanan bawah text_kanan">' .  $this->_number_format($total_all_realisasi_pagu_skpd) . '</td>
            <td class="kanan bawah text_kanan">' .  $this->_number_format($selisih) . '</td>
            <td class="kanan bawah text_kanan">' .  $this->pembulatan($persen) . '%</td>
        </tr>
    ';

    $total_all_pagu_all_skpd += $total_all_pagu_skpd;
    $total_all_realisasi_pagu_all_skpd += $total_all_realisasi_pagu_skpd;

    $total_all_pagu_1 += $pagu_1;
    $total_all_pagu_2 += $pagu_2;
    $total_all_pagu_3 += $pagu_3;
    $total_all_pagu_4 += $pagu_4;
    $total_all_pagu_5 += $pagu_5;

    $total_all_realisasi_pagu_1 += $realisasi_pagu_1;
    $total_all_realisasi_pagu_2 += $realisasi_pagu_2;
    $total_all_realisasi_pagu_3 += $realisasi_pagu_3;
    $total_all_realisasi_pagu_4 += $realisasi_pagu_4;
    $total_all_realisasi_pagu_5 += $realisasi_pagu_5;

    $total_all_selisih += $selisih;
    $persen_all = $total_all_pagu_all_skpd > 0 ? round($total_all_realisasi_pagu_all_skpd / $total_all_pagu_all_skpd * 100, 2) : 0;


	$persen_1 = 0;
	$persen_2 = 0;
	$persen_3 = 0;
	if (!empty($total_all_pagu_1) && !empty($total_all_realisasi_pagu_1)) {
		$persen_1 = ($total_all_realisasi_pagu_1 / $total_all_pagu_1) * 100;
	}
	if (!empty($total_all_pagu_2) && !empty($total_all_realisasi_pagu_2)) {
		$persen_2 = ($total_all_realisasi_pagu_2 / $total_all_pagu_2) * 100;
	}
	if (!empty($total_all_pagu_3) && !empty($total_all_realisasi_pagu_3)) {
		$persen_3 = ($total_all_realisasi_pagu_3 / $total_all_pagu_3) * 100;
	}
}
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
<div class="content flex-row-fluid" style="max-width: 1500px; margin:auto; padding: 10px;">
    <div class="row gy-5 g-xl-8 mb-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h4 style="margin: 0;"><i class="dashicons dashicons-chart-bar" style="font-size: x-large; padding-top: 2px;"></i> Dashboard Anggaran dan Realisasi</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="chart" style="padding: 30px; height: 500px;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <h2 class="font-weight-bolder text-white p-5 bg-warning rounded m-0 text-center">Anggaran</h2>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9 bg-light-warning rounded" style="margin-top: 3rem;">
                                <!--begin::Title-->
                                <div class="col-md-12">
                                    <table class="table">
                                        <tr>
                                            <td style="width:20px;">
                                                <h2 class="font-weight-bolder text-warning py-1 m-0">Total</h2>
                                            </td>
                                            <td style="width:2px;">
                                                <h2 class="font-weight-bolder text-warning py-1 m-0">:</h2>
                                            </td>
                                            <td class="text-end text-right">
                                                <h2 class="font-weight-bolder text-warning py-1 m-0"><?php echo number_format($total_all_pagu_all_skpd, 0, ",", "."); ?></h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 class="font-weight-bolder text-warning py-1 m-0">Tahun 1</h4>
                                            </td>
                                            <td>
                                                <h4 class="font-weight-bolder text-warning py-1 m-0">:</h4>
                                            </td>
                                            <td class="text-end text-right">
                                                <h4 class="font-weight-bolder text-warning py-1 m-0"><?php echo number_format($total_all_pagu_1, 0, ",", "."); ?></h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 class="font-weight-bolder text-warning py-1 m-0">Tahun 2</h4>
                                            </td>
                                            <td>
                                                <h4 class="font-weight-bolder text-warning py-1 m-0">:</h4>
                                            </td>
                                            <td class="text-end text-right">
                                                <h4 class="font-weight-bolder text-warning py-1 m-0"><?php echo number_format($total_all_pagu_2, 0, ",", "."); ?></h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 class="font-weight-bolder text-warning py-1 m-0">Tahun 3</h4>
                                            </td>
                                            <td>
                                                <h4 class="font-weight-bolder text-warning py-1 m-0">:</h4>
                                            </td>
                                            <td class="text-end text-right">
                                                <h4 class="font-weight-bolder text-warning py-1 m-0"><?php echo number_format($total_all_pagu_3, 0, ",", "."); ?></h4>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <!--end::Title-->
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <h2 class="font-weight-bolder text-white p-5 bg-primary rounded m-0 text-center">Realisasi</h2>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9 bg-light-primary rounded" style="margin-top: 3rem;">
                                <!--begin::Title-->
                                <div class="col-md-12">
                                    <table class="table">
                                        <tr>
                                            <td style="width:20px;">
                                                <h2 class="font-weight-bolder text-primary py-1 m-0">Total</h2>
                                            </td>
                                            <td style="width:2px;">
                                                <h2 class="font-weight-bolder text-primary py-1 m-0">:</h2>
                                            </td>
                                            <td class="text-end text-right">
                                                <h2 class="font-weight-bolder text-primary py-1 m-0"><?php echo number_format($total_all_realisasi_pagu_all_skpd, 0, ",", "."); ?></h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 class="font-weight-bolder text-primary py-1 m-0">Tahun 1</h4>
                                            </td>
                                            <td>
                                                <h4 class="font-weight-bolder text-primary py-1 m-0">:</h4>
                                            </td>
                                            <td class="text-end text-right">
                                                <h4 class="font-weight-bolder text-primary py-1 m-0"><?php echo number_format($total_all_realisasi_pagu_1, 0, ",", "."); ?></h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 class="font-weight-bolder text-primary py-1 m-0">Tahun 2</h4>
                                            </td>
                                            <td>
                                                <h4 class="font-weight-bolder text-primary py-1 m-0">:</h4>
                                            </td>
                                            <td class="text-end text-right">
                                                <h4 class="font-weight-bolder text-primary py-1 m-0"><?php echo number_format($total_all_realisasi_pagu_2, 0, ",", "."); ?></h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 class="font-weight-bolder text-primary py-1 m-0">Tahun 3</h4>
                                            </td>
                                            <td>
                                                <h4 class="font-weight-bolder text-primary py-1 m-0">:</h4>
                                            </td>
                                            <td class="text-end text-right">
                                                <h4 class="font-weight-bolder text-primary py-1 m-0"><?php echo number_format($total_all_realisasi_pagu_3, 0, ",", "."); ?></h4>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <!--end::Title-->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <h2 class="font-weight-bolder text-white p-5 bg-success rounded m-0 text-center">Persentase</h2>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9 bg-light-success rounded p-5">
                                <!--begin::Title-->
                                <div class="col-md-12">
                                    <table class="table">
                                        <tr>
                                            <td style="width:20px;">
                                                <h2 class="font-weight-bolder text-success py-1 m-0">Total</h2>
                                            </td>
                                            <td style="width:2px;">
                                                <h2 class="font-weight-bolder text-success py-1 m-0">:</h2>
                                            </td>
                                            <td class="text-end text-center">
                                                <h2 class="font-weight-bolder text-success py-1 m-0"><?php echo $this->pembulatan(($total_all_realisasi_pagu_all_skpd / $total_all_pagu_all_skpd) * 100); ?>%</h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 class="font-weight-bolder text-success py-1 m-0">Tahun 1</h4>
                                            </td>
                                            <td>
                                                <h4 class="font-weight-bolder text-success py-1 m-0">:</h4>
                                            </td>
                                            <td class="text-end text-center">
                                                <h4 class="font-weight-bolder text-success py-1 m-0"><?php echo $this->pembulatan($persen_1); ?>%</h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 class="font-weight-bolder text-success py-1 m-0">Tahun 2</h4>
                                            </td>
                                            <td>
                                                <h4 class="font-weight-bolder text-success py-1 m-0">:</h4>
                                            </td>
                                            <td class="text-end text-center">
                                                <h4 class="font-weight-bolder text-success py-1 m-0"><?php echo $this->pembulatan($persen_2); ?>%</h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 class="font-weight-bolder text-success py-1 m-0">Tahun 3</h4>
                                            </td>
                                            <td>
                                                <h4 class="font-weight-bolder text-success py-1 m-0">:</h4>
                                            </td>
                                            <td class="text-end text-center">
                                                <h4 class="font-weight-bolder text-success py-1 m-0"><?php echo $this->pembulatan($persen_3); ?>%</h4>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <!--end::Title-->
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-md-6 offset-md-3 offset-sm-0">
                            <div class="card card-primary" style="box-shadow: 1px 1px 5px #666;">
                                <div class="card-header bg-primary text-white p-5">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-2">
                                                <i class="fas fa-money-bill-wave-alt fa-3x lh-lg"></i>
                                            </div>
                                            <div class="col">
                                                <h2 class="m-0 p-0 col-md-12 lh-lg text-white">Nomenklatur Rencana Strategis</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-5">
                                        <div class="col-12 text-center" style="font-size:1.3em;">
                                            <p>Perangkat Daerah</p>
                                            <p><?php echo $no; ?></p>
                                        </div>
                                        <div class="col-4 text-center mt-3" style="font-size:1.3em; border-right:1px solid #666;">
                                            <p>Program</p>
                                            <p><?php echo $no_program; ?></p>
                                        </div>
                                        <div class="col-4 text-center mt-3" style="font-size:1.3em; border-right:1px solid #666;">
                                            <p>Kegiatan</p>
                                            <p><?php echo $no_kegiatan; ?></p>
                                        </div>
                                        <div class="col-4 text-center mt-3" style="font-size:1.3em;">
                                            <p>Sub Kegiatan</p>
                                            <p><?php echo $no_sub_kegiatan; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
                <th class='atas kanan bawah kiri text_tengah text_blok' colspan="2">Total</th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo $this->_number_format($total_all_pagu_1); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo $this->_number_format($total_all_realisasi_pagu_1); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo $this->_number_format($total_all_pagu_2); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo $this->_number_format($total_all_realisasi_pagu_2); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo $this->_number_format($total_all_pagu_3); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo $this->_number_format($total_all_realisasi_pagu_3); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo $this->_number_format($total_all_pagu_all_skpd); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo $this->_number_format($total_all_realisasi_pagu_all_skpd); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo $this->_number_format($total_all_selisih); ?></th>
                <td class="atas kanan bawah kiri text_kanan"><span><?php echo $persen_all; ?>%</span></td>
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
        google.charts.setOnLoadCallback(drawColColors);
        run_download_excel('', '#aksi-wp-sipd');
    });

    function drawColColors() {
        var data_cart =  [
			['Tahun', 'Anggaran', 'Realisasi'],
			[' 1', <?php echo $total_all_pagu_1; ?>, <?php echo $total_all_realisasi_pagu_1; ?>],
			[' 2', <?php echo $total_all_pagu_2; ?>, <?php echo $total_all_realisasi_pagu_2; ?>],
			[' 3', <?php echo $total_all_pagu_3; ?>, <?php echo $total_all_realisasi_pagu_3; ?>],
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