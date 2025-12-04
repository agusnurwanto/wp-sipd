<?php
global $wpdb;

if (!defined('WPINC')) {
    die;
}

$input = shortcode_atts(array(
    'tahun_anggaran' => '2023'
), $atts);

// Tujuan Sasaran
$data_unit = $wpdb->get_results(
    $wpdb->prepare("
        SELECT 
            *   
        FROM data_unit 
        WHERE active=1 
          AND tahun_anggaran=%d
          AND is_skpd = 1
        ORDER BY kode_skpd ASC
    ", $input['tahun_anggaran']),
    ARRAY_A
);

$get_pemilik_resiko = $this->pemilik_resiko_manrisk();
$get_sumber_sebab = $this->sumber_sebab_manrisk();
$get_pihak_terdampak = $this->pihak_terdampak_manrisk();

$data_pemilik_resiko = array();
foreach ($get_pemilik_resiko as $item) {
    $data_pemilik_resiko[$item['id']] = $item['nama'];
}

$data_sumber_sebab = array();
foreach ($get_sumber_sebab as $item) {
    $data_sumber_sebab[$item['id']] = $item['nama'];
}

$data_pihak_terdampak = array();
foreach ($get_pihak_terdampak as $item) {
    $data_pihak_terdampak[$item['id']] = $item['nama'];
}

$all_rows = array();

foreach ($data_unit as $unit) {
    $id_skpd = $unit['id_skpd'];
    $nama_skpd = $unit['nama_skpd'];
    
    $get_data_manrisk = $wpdb->get_results($wpdb->prepare("
        SELECT 
            * 
        FROM data_tujuan_sasaran_manrisk_sesudah
        WHERE tahun_anggaran = %d
            AND id_skpd = %d
            AND active = 1
        ORDER BY tipe ASC, id_tujuan_sasaran ASC, id_indikator ASC, id ASC
    ", $input['tahun_anggaran'], $id_skpd), ARRAY_A);
    
    if (!empty($get_data_manrisk)) {
        $filter_data = array();
        foreach ($get_data_manrisk as $data) {
            $filter_data[] = $data;
        }
        
        if (!empty($filter_data)) {
            foreach ($filter_data as $data) {
                $tingkat_resiko = $data['skala_dampak'] * $data['skala_kemungkinan'];
                
                $urut = 5;
                if ($tingkat_resiko == 0) {
                    $urut = 6; // Kosong
                } elseif ($tingkat_resiko >= 1 && $tingkat_resiko <= 3) {
                    $urut = 5; // Sangat Rendah
                } elseif ($tingkat_resiko >= 4 && $tingkat_resiko <= 8) {
                    $urut = 4; // Rendah
                } elseif ($tingkat_resiko >= 9 && $tingkat_resiko <= 17) {
                    $urut = 3; // Sedang
                } elseif ($tingkat_resiko >= 18 && $tingkat_resiko <= 22) {
                    $urut = 2; // Tinggi
                } elseif ($tingkat_resiko >= 23 && $tingkat_resiko <= 25) {
                    $urut = 1; // Sangat Tinggi
                }
                
                $nama_tujuan_sasaran = '';
                $label_tipe = '';
                
                if ($data['tipe'] == 0) {
                    $get_tujuan = $wpdb->get_row($wpdb->prepare("
                        SELECT 
                            tujuan_teks
                        FROM data_renstra_tujuan
                        WHERE id_unik = %s
                            AND id_unit = %d
                            AND active = 1
                        LIMIT 1
                    ", $data['id_tujuan_sasaran'], $id_skpd), ARRAY_A);
                    
                    if (!empty($get_tujuan)) {
                        $label_tipe = '<b>Tujuan:</b><br>';
                        $nama_tujuan_sasaran = $get_tujuan['tujuan_teks'];
                    }
                } elseif ($data['tipe'] == 1) {
                    $get_sasaran = $wpdb->get_row($wpdb->prepare("
                        SELECT 
                            sasaran_teks
                        FROM data_renstra_sasaran
                        WHERE id_unik = %s
                            AND id_unit = %d
                            AND active = 1
                        LIMIT 1
                    ", $data['id_tujuan_sasaran'], $id_skpd), ARRAY_A);
                    
                    if (!empty($get_sasaran)) {
                        $label_tipe = '<b>Sasaran:</b><br>';
                        $nama_tujuan_sasaran = $get_sasaran['sasaran_teks'];
                    }
                }
                
                $indikator_text = '';
                
                if ($data['tipe'] == 0) {
                    $get_indikator = $wpdb->get_row($wpdb->prepare("
                        SELECT 
                            indikator_teks
                        FROM data_renstra_tujuan
                        WHERE id_unik = %s
                            AND id_unik_indikator = %s
                            AND id_unit = %d
                            AND active = 1
                    ", $data['id_tujuan_sasaran'], $data['id_indikator'], $id_skpd), ARRAY_A);
                    
                    if (!empty($get_indikator)) {
                        $indikator_text = $get_indikator['indikator_teks'];
                    }
                } elseif ($data['tipe'] == 1) {
                    $get_indikator = $wpdb->get_row($wpdb->prepare("
                        SELECT 
                            indikator_teks
                        FROM data_renstra_sasaran
                        WHERE id_unik = %s
                            AND id_unik_indikator = %s
                            AND id_unit = %d
                            AND active = 1
                    ", $data['id_tujuan_sasaran'], $data['id_indikator'], $id_skpd), ARRAY_A);
                    
                    if (!empty($get_indikator)) {
                        $indikator_text = $get_indikator['indikator_teks'];
                    }
                }
                
                if (empty($indikator_text)) {
                    $indikator_text = '-';
                }
                
                $controllable = '';
                if ($data['controllable'] == 0) {
                    $controllable = 'Controllable';
                } elseif ($data['controllable'] == 1) {
                    $controllable = 'Uncontrollable';
                }
                
                $pemilik_resiko = isset($data_pemilik_resiko[$data['pemilik_resiko']]) ? $data_pemilik_resiko[$data['pemilik_resiko']] : '';
                $sumber_sebab = isset($data_sumber_sebab[$data['sumber_sebab']]) ? $data_sumber_sebab[$data['sumber_sebab']] : '';
                $pihak_terkena = isset($data_pihak_terdampak[$data['pihak_terkena']]) ? $data_pihak_terdampak[$data['pihak_terkena']] : '';
                
                $row = array();
                $row['tingkat_resiko_urut'] = $urut;
                $row['html'] = array();
                
                $row['html'][] = '<tr class="resiko-row" data-tingkat="' . $urut . '">';
                $row['html'][] = '<td class="text-left">' . $nama_skpd . '</td>';
                $row['html'][] = '<td class="text-left">' . $label_tipe . $nama_tujuan_sasaran . '</td>';
                $row['html'][] = '<td class="text-left">' . $indikator_text . '</td>';
                $row['html'][] = '<td class="text-left">' . $data['uraian_resiko'] . '</td>';
                $row['html'][] = '<td class="text-left">' . $data['kode_resiko'] . '</td>';
                $row['html'][] = '<td class="text-left">' . $pemilik_resiko . '</td>';
                $row['html'][] = '<td class="text-left">' . $data['uraian_sebab'] . '</td>';
                $row['html'][] = '<td class="text-left">' . $sumber_sebab . '</td>';
                $row['html'][] = '<td class="text-left">' . $controllable . '</td>';
                $row['html'][] = '<td class="text-left">' . $data['uraian_dampak'] . '</td>';
                $row['html'][] = '<td class="text-left">' . $pihak_terkena . '</td>';
                $row['html'][] = '<td class="text-left">' . $data['rencana_tindak_pengendalian'] . '</td>';
                $row['html'][] = '<td class="text-center">' . $data['skala_dampak'] . '</td>';
                $row['html'][] = '<td class="text-center">' . $data['skala_kemungkinan'] . '</td>';
                $row['html'][] = '<td class="text-center nilai-resiko" data-nilai="' . $tingkat_resiko . '">' . $tingkat_resiko . '</td>';
                $row['html'][] = '<td class="text-left tingkat-resiko" data-nilai="' . $tingkat_resiko . '"></td>';
                $row['html'][] = '</tr>';
                
                $all_rows[] = $row;
            }
        } else {
            $row = array();
            $row['tingkat_resiko_urut'] = 7; // Belum ada data
            $row['html'] = array();
            $row['html'][] = '<tr class="resiko-row" data-tingkat="6">';
            $row['html'][] = '<td class="text-left">' . $nama_skpd . '</td>';
            $row['html'][] = '<td class="text-center" colspan="15">Belum ada data</td>';
            $row['html'][] = '</tr>';
            $all_rows[] = $row;
        }
    } else {
        $row = array();
        $row['tingkat_resiko_urut'] = 7; // Belum ada data
        $row['html'] = array();
        $row['html'][] = '<tr class="resiko-row" data-tingkat="6">';
        $row['html'][] = '<td class="text-left">' . $nama_skpd . '</td>';
        $row['html'][] = '<td class="text-center" colspan="15">Belum ada data</td>';
        $row['html'][] = '</tr>';
        $all_rows[] = $row;
    }
}

// Urutkan semua baris berdasarkan tingkat_resiko_urut
// 1=Sangat Tinggi, 2=Tinggi, 3=Sedang, 4=Rendah, 5=Sangat Rendah, 6=Kosong, 7=Belum ada data
usort($all_rows, function($a, $b) {
    return $a['tingkat_resiko_urut'] - $b['tingkat_resiko_urut'];
});

$body = '';
foreach ($all_rows as $row) {
    $body .= implode('', $row['html']);
}

// Program Kegiatan Sub kegiatan
$data_unit = $wpdb->get_results(
    $wpdb->prepare("
        SELECT 
            *   
        FROM data_unit 
        WHERE active=1 
          AND tahun_anggaran=%d
        ORDER BY kode_skpd ASC
    ", $input['tahun_anggaran']),
    ARRAY_A
);

$all_rows_2 = array();

foreach ($data_unit as $unit) {
    $id_skpd = $unit['id_skpd'];
    $nama_skpd = $unit['nama_skpd'];
    
    $get_data_manrisk = $wpdb->get_results($wpdb->prepare("
        SELECT 
            * 
        FROM data_program_kegiatan_manrisk_sesudah
        WHERE tahun_anggaran = %d
            AND id_skpd = %d
            AND active = 1
        ORDER BY tipe ASC, id_program_kegiatan ASC, id_indikator ASC, id ASC
    ", $input['tahun_anggaran'], $id_skpd), ARRAY_A);
    
    if (!empty($get_data_manrisk)) {
        $filter_data = array();
        foreach ($get_data_manrisk as $data) {
            $filter_data[] = $data;
        }
        
        if (!empty($filter_data)) {
            foreach ($filter_data as $data) {
                $tingkat_resiko = $data['skala_dampak'] * $data['skala_kemungkinan'];
                
                $urut = 5;
                if ($tingkat_resiko == 0) {
                    $urut = 6; // Kosong
                } elseif ($tingkat_resiko >= 1 && $tingkat_resiko <= 3) {
                    $urut = 5; // Sangat Rendah
                } elseif ($tingkat_resiko >= 4 && $tingkat_resiko <= 8) {
                    $urut = 4; // Rendah
                } elseif ($tingkat_resiko >= 9 && $tingkat_resiko <= 17) {
                    $urut = 3; // Sedang
                } elseif ($tingkat_resiko >= 18 && $tingkat_resiko <= 22) {
                    $urut = 2; // Tinggi
                } elseif ($tingkat_resiko >= 23 && $tingkat_resiko <= 25) {
                    $urut = 1; // Sangat Tinggi
                }
                
                $nama_program_kegiatan = '';
                $label_tipe = '';
                
                if ($data['tipe'] == 0) {
                    $get_data_program = $wpdb->get_row($wpdb->prepare("
                        SELECT 
                            nama_program,
                            kode_program
                        FROM data_sub_keg_bl 
                        WHERE kode_program = %s
                            AND id_sub_skpd = %d
                            AND tahun_anggaran = %d
                            AND active = 1
                        LIMIT 1
                    ", $data['id_program_kegiatan'], $id_skpd, $input['tahun_anggaran']), ARRAY_A);
                    
                    if (!empty($get_data_program)) {
                        $nama_program_kegiatan = $get_data_program['kode_program'] . ' ' . $get_data_program['nama_program'];
                        $label_tipe = '<b>Program OPD:</b><br>';
                    }
                } elseif ($data['tipe'] == 1) {
                    $get_data_kegiatan = $wpdb->get_row($wpdb->prepare("
                        SELECT 
                            nama_giat,
                            kode_giat
                        FROM data_sub_keg_bl 
                        WHERE kode_giat = %s
                            AND id_sub_skpd = %d
                            AND tahun_anggaran = %d
                            AND active = 1
                        LIMIT 1
                    ", $data['id_program_kegiatan'], $id_skpd, $input['tahun_anggaran']), ARRAY_A);
                    
                    if (!empty($get_data_kegiatan)) {
                        $nama_program_kegiatan =  $get_data_kegiatan['kode_giat'] . ' ' . $get_data_kegiatan['nama_giat'];
                        $label_tipe = '<b>Kegiatan OPD:</b><br>';
                    }
                } elseif ($data['tipe'] == 2) {
                    $get_data_sub_kegiatan = $wpdb->get_row($wpdb->prepare("
                        SELECT 
                            nama_sub_giat
                        FROM data_sub_keg_bl 
                        WHERE kode_sub_giat = %s
                            AND id_sub_skpd = %d
                            AND tahun_anggaran = %d
                            AND active = 1
                        LIMIT 1
                    ", $data['id_program_kegiatan'], $id_skpd, $input['tahun_anggaran']), ARRAY_A);
                    
                    if (!empty($get_data_sub_kegiatan)) {
                        $nama_program_kegiatan = $get_data_sub_kegiatan['nama_sub_giat'];
                        $label_tipe = '<b>Sub Kegiatan OPD:</b><br>';
                    }
                }
                
                $controllable = '';
                if ($data['controllable'] == 0) {
                    $controllable = 'Controllable';
                } elseif ($data['controllable'] == 1) {
                    $controllable = 'Uncontrollable';
                }
                
                $pemilik_resiko = isset($data_pemilik_resiko[$data['pemilik_resiko']]) ? $data_pemilik_resiko[$data['pemilik_resiko']] : '';
                $sumber_sebab = isset($data_sumber_sebab[$data['sumber_sebab']]) ? $data_sumber_sebab[$data['sumber_sebab']] : '';
                $pihak_terkena = isset($data_pihak_terdampak[$data['pihak_terkena']]) ? $data_pihak_terdampak[$data['pihak_terkena']] : '';
                
                $indikator_text = !empty($data['capaian_teks']) ? $data['capaian_teks'] : '-';
                
                $row = array();
                $row['tingkat_resiko_urut'] = $urut;
                $row['html'] = array();
                
                $row['html'][] = '<tr class="resiko-row-2" data-tingkat="' . $urut . '">';
                $row['html'][] = '<td class="text-left">' . $nama_skpd . '</td>';
                $row['html'][] = '<td class="text-left">' . $label_tipe . $nama_program_kegiatan . '</td>';
                $row['html'][] = '<td class="text-left">' . $indikator_text . '</td>';
                $row['html'][] = '<td class="text-left">' . $data['uraian_resiko'] . '</td>';
                $row['html'][] = '<td class="text-left">' . $data['kode_resiko'] . '</td>';
                $row['html'][] = '<td class="text-left">' . $pemilik_resiko . '</td>';
                $row['html'][] = '<td class="text-left">' . $data['uraian_sebab'] . '</td>';
                $row['html'][] = '<td class="text-left">' . $sumber_sebab . '</td>';
                $row['html'][] = '<td class="text-left">' . $controllable . '</td>';
                $row['html'][] = '<td class="text-left">' . $data['uraian_dampak'] . '</td>';
                $row['html'][] = '<td class="text-left">' . $pihak_terkena . '</td>';
                $row['html'][] = '<td class="text-left">' . $data['rencana_tindak_pengendalian'] . '</td>';
                $row['html'][] = '<td class="text-center">' . $data['skala_dampak'] . '</td>';
                $row['html'][] = '<td class="text-center">' . $data['skala_kemungkinan'] . '</td>';
                $row['html'][] = '<td class="text-center nilai-resiko" data-nilai="' . $tingkat_resiko . '">' . $tingkat_resiko . '</td>';
                $row['html'][] = '<td class="text-left tingkat-resiko" data-nilai="' . $tingkat_resiko . '"></td>';
                $row['html'][] = '</tr>';
                
                $all_rows_2[] = $row;
            }
        } else {
            $row = array();
            $row['tingkat_resiko_urut'] = 7;
            $row['html'] = array();
            $row['html'][] = '<tr class="resiko-row-2" data-tingkat="6">';
            $row['html'][] = '<td class="text-left">' . $nama_skpd . '</td>';
            $row['html'][] = '<td class="text-center" colspan="15">Belum ada data</td>';
            $row['html'][] = '</tr>';
            $all_rows_2[] = $row;
        }
    } else {
        $row = array();
        $row['tingkat_resiko_urut'] = 7;
        $row['html'] = array();
        $row['html'][] = '<tr class="resiko-row-2" data-tingkat="6">';
        $row['html'][] = '<td class="text-left">' . $nama_skpd . '</td>';
        $row['html'][] = '<td class="text-center" colspan="15">Belum ada data</td>';
        $row['html'][] = '</tr>';
        $all_rows_2[] = $row;
    }
}

// Urutkan semua baris berdasarkan tingkat_resiko_urut
usort($all_rows_2, function($a, $b) {
    return $a['tingkat_resiko_urut'] - $b['tingkat_resiko_urut'];
});

$body_2 = '';
foreach ($all_rows_2 as $row) {
    $body_2 .= implode('', $row['html']);
}
?>
<style type="text/css">
    .wrap-table {
        overflow: auto;
        max-height: 100vh;
        width: 100%;
    }

    .btn-action-group {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btn-action-group .btn {
        margin: 0 5px;
    }

    .table_monitor_skpd thead {
        position: sticky;
        top: -6px;
    }

    .table_monitor_skpd thead th {
        vertical-align: middle;
    }

    .table_monitor_skpd tfoot {
        position: sticky;
        bottom: 0;
    }

    .filter-resiko {
        margin: 20px auto;
        padding: 15px 25px;
        border-radius: 5px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-resiko label {
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 500;
        cursor: pointer;
    }

    .filter-resiko input[type="checkbox"] {
        cursor: pointer;
        vertical-align: middle;
        transform: scale(1.1);
    }

</style>
<div class="container-md">
    <div class="cetak">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h1 class="text-center table-title">Monitoring Risiko Tujuan / Sasaran<br>Perangkat Daerah<br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
            <div id='aksi-wpsipd'></div>
            
            <div class="filter-resiko">
                <label><input type="checkbox" class="filter-tingkat" value="1" checked> Sangat Tinggi</label>
                <label><input type="checkbox" class="filter-tingkat" value="2" checked> Tinggi</label>
                <label><input type="checkbox" class="filter-tingkat" value="3" checked> Sedang</label>
                <label><input type="checkbox" class="filter-tingkat" value="4" checked> Rendah</label>
                <label><input type="checkbox" class="filter-tingkat" value="5" checked> Sangat Rendah</label>
                <label><input type="checkbox" class="filter-tingkat" value="6" checked> Kosong / Belum Ada Data</label>
            </div>
            
            <div class="wrap-table">
                <table id="cetak" title="Monitoring Risiko Tujuan / Sasaran SKPD" class="table table-bordered table_monitor_skpd">
                     <thead style="background: #ffc491; text-align:center;">
                        <tr>
                            <th rowspan="2">Nama Perangkat Daerah</th>
                            <th rowspan="2">Tujuan Strategis / Sasaran Strategis OPD</th>
                            <th rowspan="2">Indikator Kinerja</th>
                            <th colspan="3">Risiko</th>
                            <th colspan="2">Sebab</th>
                            <th rowspan="2">Controllable / Uncontrollable</th>
                            <th colspan="2">Dampak</th>
                            <th rowspan="2">Rencana Tindak Pengendalian</th>
                            <th rowspan="2">Skala Dampak</th>
                            <th rowspan="2">Skala Kemungkinan</th>
                            <th rowspan="2">Nilai Risiko (13x14)</th>
                            <th rowspan="2">Tingkat Risiko</th>
                        </tr>
                        <tr>
                            <th>Uraian</th>
                            <th>Kode Risiko</th>
                            <th>Pemilik Risiko</th>
                            <th>Uraian</th>
                            <th>Sumber</th>
                            <th>Uraian</th>
                            <th>Pihak yang Terkena</th>
                        </tr>
                        <tr>
                            <th>(1)</th>
                            <th>(2)</th>
                            <th>(3)</th>
                            <th>(4)</th>
                            <th>(5)</th>
                            <th>(6)</th>
                            <th>(7)</th>
                            <th>(8)</th>
                            <th>(9)</th>
                            <th>(10)</th>
                            <th>(11)</th>
                            <th>(12)</th>
                            <th>(13)</th>
                            <th>(14)</th>
                            <th>(15)</th>
                            <th>(16)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $body; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="container-md">
    <div class="cetak-2">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h1 class="text-center table-title">Monitoring Risiko Program / Kegiatan / Sub Kegiatan <br>Perangkat Daerah<br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
            <div id='aksi-wpsipd-2'></div>
            
            <div class="wrap-table">
                <table id="cetak-2" title="Monitoring Risiko Program / Kegiatan / Sub Kegiatan SKPD" class="table table-bordered table_monitor_skpd">
                     <thead style="background: #ffc491; text-align:center;">
                        <tr>
                            <th rowspan="2">Nama Perangkat Daerah</th>
                            <th rowspan="2">Program / Kegiatan / Sub Kegiatan OPD</th>
                            <th rowspan="2">Indikator Kinerja</th>
                            <th colspan="3">Risiko</th>
                            <th colspan="2">Sebab</th>
                            <th rowspan="2">Controllable / Uncontrollable</th>
                            <th colspan="2">Dampak</th>
                            <th rowspan="2">Rencana Tindak Pengendalian</th>
                            <th rowspan="2">Skala Dampak</th>
                            <th rowspan="2">Skala Kemungkinan</th>
                            <th rowspan="2">Nilai Risiko (13x14)</th>
                            <th rowspan="2">Tingkat Risiko</th>
                        </tr>
                        <tr>
                            <th>Uraian</th>
                            <th>Kode Risiko</th>
                            <th>Pemilik Risiko</th>
                            <th>Uraian</th>
                            <th>Sumber</th>
                            <th>Uraian</th>
                            <th>Pihak yang Terkena</th>
                        </tr>
                        <tr>
                            <th>(1)</th>
                            <th>(2)</th>
                            <th>(3)</th>
                            <th>(4)</th>
                            <th>(5)</th>
                            <th>(6)</th>
                            <th>(7)</th>
                            <th>(8)</th>
                            <th>(9)</th>
                            <th>(10)</th>
                            <th>(11)</th>
                            <th>(12)</th>
                            <th>(13)</th>
                            <th>(14)</th>
                            <th>(15)</th>
                            <th>(16)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $body_2; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        jQuery('.tingkat-resiko').each(function() {
            const nilairesiko = jQuery(this).data('nilai');
            const tingkatresiko = getTingkatRisiko(nilairesiko);
            
            jQuery(this).text(tingkatresiko.label);
            if (tingkatresiko.bg_color) {
                jQuery(this).css('background-color', tingkatresiko.bg_color);
            }
        });
        
        jQuery('.filter-tingkat').on('change', function() {
            filter_all_data();
        });
        
        run_download_excel('', '#aksi-wpsipd');
        run_download_excel_2('', '#aksi-wpsipd-2');
    });

    function getTingkatRisiko(nilairesiko) {
        let data = {
            label: '',
            bg_color: '',
            urut: 0
        };
        
        if (nilairesiko == 0) {
            data.label = 'Kosong';
            data.bg_color = '';
            data.urut = 6;
        } else if (nilairesiko >= 1 && nilairesiko <= 3) {
            data.label = 'Sangat Rendah';
            data.bg_color = '#e8ffef';
            data.urut = 5;
        } else if (nilairesiko >= 4 && nilairesiko <= 8) {
            data.label = 'Rendah';
            data.bg_color = '#cfffdd';
            data.urut = 4;
        } else if (nilairesiko >= 9 && nilairesiko <= 17) {
            data.label = 'Sedang';
            data.bg_color = '#ffeec9';
            data.urut = 3;
        } else if (nilairesiko >= 18 && nilairesiko <= 22) {
            data.label = 'Tinggi';
            data.bg_color = '#ffb499';
            data.urut = 2;
        } else if (nilairesiko >= 23 && nilairesiko <= 25) {
            data.label = 'Sangat Tinggi';
            data.bg_color = '#ff6e4e';
            data.urut = 1;
        }
        
        return data;
    }
    
    function filter_all_data() {
        var checked = [];
        jQuery('.filter-tingkat:checked').each(function() {
            checked.push(jQuery(this).val());
        });
        
        // Filter tabel Tujuan/Sasaran
        jQuery('.resiko-row').each(function() {
            var tingkat = jQuery(this).data('tingkat').toString();
            if (checked.indexOf(tingkat) !== -1) {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        });
        
        // Filter tabel Program/Kegiatan/Sub Kegiatan
        jQuery('.resiko-row-2').each(function() {
            var tingkat = jQuery(this).data('tingkat').toString();
            if (checked.indexOf(tingkat) !== -1) {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        });
    }
</script>