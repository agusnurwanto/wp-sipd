<?php
// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}
$input = shortcode_atts(array(
    'id_label' => '',
    'tahun_anggaran' => ''
), $atts);

global $wpdb;

$label_db = $wpdb->get_row(
    $wpdb->prepare("
        SELECT
            *
        FROM data_label_komponen
        WHERE active!=0
          AND tahun_anggaran=%d
          AND id=%d
    ", $input['tahun_anggaran'], $input['id_label']),
    ARRAY_A
);

$inner_skpd = '';
$where_skpd = '';
if (!empty($_GET) && !empty($_GET['id_skpd'])) {
    $inner_skpd = '
        INNER JOIN data_sub_keg_bl s 
                ON s.kode_sbl=r.kode_sbl
               AND s.active = r.active
               AND s.tahun_anggaran=r.tahun_anggaran';
    $where_skpd = $wpdb->prepare("AND s.id_sub_skpd=%d", $_GET['id_skpd']);

    $data_skpd = $wpdb->get_row(
        $wpdb->prepare("
            SELECT 
                id_skpd,
                nama_skpd,
                kode_skpd 
            FROM data_unit 
            WHERE id_skpd = %d 
              AND tahun_anggaran = %d 
              AND active = 1
        ", $_GET['id_skpd'], $input['tahun_anggaran']),
        ARRAY_A
    );
} else {
    $data_skpd = $wpdb->get_results(
        $wpdb->prepare("
            SELECT 
                id_skpd,
                nama_skpd,
                kode_skpd 
            FROM data_unit 
            WHERE tahun_anggaran = %d 
              AND active = 1
            ORDER BY kode_skpd ASC
        ", $input['tahun_anggaran']),
        ARRAY_A
    );
}

$sql = $wpdb->prepare("
    SELECT 
        r.*,
        CASE 
            WHEN m.pisah = 1 THEN (r.rincian / r.volume) * m.volume_pisah
            ELSE r.rincian
        END AS rincian_new,
        CASE 
            WHEN m.pisah = 1 THEN m.realisasi_pisah
            ELSE rr.realisasi
        END AS realisasi_new,
        CASE 
            WHEN m.pisah = 1 THEN m.volume_pisah
            ELSE r.volume
        END AS volume_new
    FROM `data_rka` r
        " . $inner_skpd . "
    INNER JOIN data_mapping_label m 
            ON m.active = 1
           AND m.tahun_anggaran = r.tahun_anggaran
           AND m.id_rinci_sub_bl = r.id_rinci_sub_bl
    LEFT JOIN data_realisasi_rincian rr 
           ON rr.active = 1
          AND rr.tahun_anggaran = r.tahun_anggaran
          AND rr.id_rinci_sub_bl = r.id_rinci_sub_bl
    WHERE r.active != 2
      AND r.tahun_anggaran = %d
      AND m.id_label_komponen = %d
        " . $where_skpd . "
    ORDER BY r.kode_sbl ASC
", $input['tahun_anggaran'], $input['id_label']);
$data_label = $wpdb->get_results($sql, ARRAY_A);

$data_label_shorted = array(
    'data'        => array(),
    'realisasi'   => 0,
    'total_murni' => 0,
    'total'       => 0
);
foreach ($data_label as $k => $v) {
    $kode = explode('.', $v['kode_sbl']);
    $idskpd = $kode[1];
    $skpd = $wpdb->get_row(
        $wpdb->prepare("
            SELECT 
                id_skpd,
                nama_skpd,
                kode_skpd 
            FROM data_unit 
            WHERE id_skpd=%d 
              AND tahun_anggaran = %d 
              AND active = 1
        ", $idskpd, $input['tahun_anggaran']),
        ARRAY_A
    );
    if (empty($data_label_shorted['data'][$skpd['kode_skpd']])) {
        $data_label_shorted['data'][$skpd['kode_skpd']] = array(
            'nama'        => $skpd['nama_skpd'],
            'realisasi'   => 0,
            'total_murni' => 0,
            'total'       => 0,
            'data'        => array()
        );
    }
    //prog subkeg
    $sub_keg = $wpdb->get_row(
        $wpdb->prepare("
            SELECT 
                kode_program,
                nama_program,
                kode_giat,
                nama_giat,
                kode_sub_giat,
                nama_sub_giat
            FROM data_sub_keg_bl 
            WHERE kode_sbl = %s
              AND tahun_anggaran = %d
              AND active = 1
            ", $v['kode_sbl'], $input['tahun_anggaran']),
        ARRAY_A
    );
    // Query capaian program
    $capaian_prog = $wpdb->get_results(
        $wpdb->prepare("
        SELECT * 
        FROM data_capaian_prog_sub_keg 
        WHERE tahun_anggaran=%d 
          AND active=1 
          AND kode_sbl=%s 
          AND capaianteks != '' 
        ORDER BY id ASC 
    ", $input['tahun_anggaran'], $v['kode_sbl']),
        ARRAY_A
    );

    // Query output kegiatan
    $output_giat = $wpdb->get_results(
        $wpdb->prepare("
        SELECT * 
        FROM data_output_giat_sub_keg 
        WHERE tahun_anggaran=%d 
          AND kode_sbl=%s 
          AND active=1 
        ORDER BY id ASC 
    ", $input['tahun_anggaran'], $v['kode_sbl']),
        ARRAY_A
    );

    // Query output sub kegiatan
    $output_sub_giat = $wpdb->get_results(
        $wpdb->prepare("
        SELECT * 
        FROM data_sub_keg_indikator 
        WHERE tahun_anggaran=%d 
          AND active=1 
          AND kode_sbl=%s 
        ORDER BY id DESC 
    ", $input['tahun_anggaran'], $v['kode_sbl']),
        ARRAY_A
    );

    if (empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']])) {
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']] = array(
            'kode_program'      => $sub_keg['kode_program'],
            'nama_program'      => $sub_keg['nama_program'],
            'satuan_prog'       => !empty($capaian_prog)
                ? implode(', ', array_column($capaian_prog, 'satuancapaian'))
                : '',
            'target_prog'       => !empty($capaian_prog)
                ? implode(', ', array_column($capaian_prog, 'targetcapaian'))
                : '',
            'indikator_prog'    => !empty($capaian_prog)
                ? implode(', ', array_column($capaian_prog, 'capaianteks'))
                : '',
            'kode_giat'         => $sub_keg['kode_giat'],
            'nama_giat'         => $sub_keg['nama_giat'],
            'satuan_keg'        => !empty($output_giat)
                ? implode(', ', array_column($output_giat, 'satuanoutput'))
                : '',
            'target_keg'        => !empty($output_giat)
                ? implode(', ', array_column($output_giat, 'targetoutput'))
                : '',
            'indikator_keg'     => !empty($output_giat)
                ? implode(', ', array_column($output_giat, 'outputteks'))
                : '',
            'kode_sbl'          => $v['kode_sbl'],
            'id_skpd'           => $skpd['id_skpd'],
            'kode_sub_giat'     => $sub_keg['kode_sub_giat'],
            'nama_sub_giat'     => $sub_keg['nama_sub_giat'],
            'satuan_sub_keg'    => !empty($output_sub_giat)
                ? implode(', ', array_column($output_sub_giat, 'satuanoutput'))
                : '',
            'target_sub_keg'    => !empty($output_sub_giat)
                ? implode(', ', array_column($output_sub_giat, 'targetoutput'))
                : '',
            'indikator_sub_keg' => !empty($output_sub_giat)
                ? implode(', ', array_column($output_sub_giat, 'outputteks'))
                : '',
            'realisasi'         => 0,
            'total_murni'       => 0,
            'total'             => 0,
            'data'              => array()
        );
    }

    $kelompok = $v['idsubtitle'] . '||' . $v['subs_bl_teks'];
    if (empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok])) {
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok] = array(
            'nama'        => $v['subs_bl_teks'],
            'realisasi'   => 0,
            'total_murni' => 0,
            'total'       => 0,
            'data'        => array()
        );
    }
    $keterangan = $v['idketerangan'] . '||' . $v['ket_bl_teks'];
    if (empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan])) {
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan] = array(
            'nama'        => $v['ket_bl_teks'],
            'realisasi'   => 0,
            'total_murni' => 0,
            'total'       => 0,
            'data'        => array()
        );
    }
    if (empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']])) {
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']] = array(
            'nama'        => $v['nama_akun'],
            'realisasi'   => 0,
            'total_murni' => 0,
            'total'       => 0,
            'data'        => array()
        );
    }
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['data'][] = $v;
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['total'] += $v['rincian_new'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['total'] += $v['rincian_new'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['total'] += $v['rincian_new'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total'] += $v['rincian_new'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['total'] += $v['rincian_new'];
    $data_label_shorted['total'] += $v['rincian_new'];

    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['total_murni'] += $v['rincian_murni'];

    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['realisasi'] += $v['realisasi_new'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['realisasi'] += $v['realisasi_new'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['realisasi'] += $v['realisasi_new'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['realisasi'] += $v['realisasi_new'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['realisasi'] += $v['realisasi_new'];
    $data_label_shorted['realisasi'] += $v['realisasi_new'];
}
ksort($data_label_shorted['data']);

$nama_pemda = get_option('_crb_daerah');
$tbody = '';
$total_rincian = 0;
$tbody = '';
$total_rincian = 0;

if (!empty($data_label_shorted['data'])) {
    foreach ($data_label_shorted['data'] as $kode_skpd => $skpd) {
        $skpd_total = 0;
        foreach ($skpd['data'] as $kode_sbl => $sub_keg) {
            $parts = explode(" ", $sub_keg['nama_sub_giat'], 2);
            $nama_sub_giat = isset($parts[1]) ? $parts[1] : $sub_keg['nama_sub_giat'];

            $tbody .= '<tr>';
            $tbody .= '<td class="kiri kanan bawah text_kiri">' . htmlspecialchars($skpd['nama']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_kiri">' . htmlspecialchars($sub_keg['kode_program']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_kiri">' . htmlspecialchars($sub_keg['nama_program']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_kiri">' . htmlspecialchars($sub_keg['indikator_prog']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_tengah">' . htmlspecialchars($sub_keg['target_prog']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_tengah">' . htmlspecialchars($sub_keg['satuan_prog']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_kiri">' . htmlspecialchars($sub_keg['kode_giat']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_kiri">' . htmlspecialchars($sub_keg['nama_giat']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_kiri">' . htmlspecialchars($sub_keg['indikator_keg']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_tengah">' . htmlspecialchars($sub_keg['target_keg']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_tengah">' . htmlspecialchars($sub_keg['satuan_keg']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_kiri">' . htmlspecialchars($sub_keg['kode_sub_giat']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_kiri">' . htmlspecialchars($sub_keg['indikator_sub_keg']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_tengah">' . htmlspecialchars($sub_keg['target_sub_keg']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_tengah">' . htmlspecialchars($sub_keg['satuan_sub_keg']) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_kiri">' . htmlspecialchars($nama_sub_giat) . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_kanan">' . number_format($sub_keg['total'], 0, ',', '.') . '</td>';
            $tbody .= '<td class="kiri kanan bawah text_kiri"></td>';
            $tbody .= '</tr>';


            $skpd_total += $sub_keg['total'];
        }


        $tbody .= '<tr>';
        $tbody .= '<td class="kiri kanan bawah text_kanan" colspan="16"><strong>Total pada SKPD</strong></td>';
        $tbody .= '<td class="kiri kanan bawah text_kiri text_kanan" colspan="2"><strong>' . number_format($skpd_total, 0, ',', '.') . '</strong></td>';
        $tbody .= '</tr>';


        $total_rincian += $skpd_total;
    }
} else {
    $tbody = '<tr><td class="kiri kanan bawah text_tengah v-align-middle" colspan="17">tidak ada data tersedia</td></tr>';
}


?>
<style>
    body {
        padding: 20px;
    }

    .bg-table {
        background-color: #edede9;
    }

    .v-align-middle {
        vertical-align: middle;
    }

    @page {
        size: A4 landscape;
        margin: 25px;
    }

    @media print {
        body {
            margin: 0;
        }

        #cetak {
            overflow: visible;
            width: 100%;
        }
    }
</style>

<body>
    <table class="borderless-table">
        <tbody>
            <tr>
                <th style="width: 200px;">
                    Nama Pemda
                </th>
                <th style="width: 10px;">
                    :
                </th>
                <th class="text_kiri">
                    <?php echo $nama_pemda; ?>
                </th>
            </tr>
            <tr>
                <th>
                    Label Komponen
                </th>
                <th>
                    :
                </th>
                <th>
                    <?php echo $label_db['nama']; ?>
                </th>
            </tr>
            <tr>
                <th>
                    Ultimate Outcome Sektor
                </th>
                <th>
                    :
                </th>
                <th>

                </th>
            </tr>
        </tbody>

    </table>
    <div class="wrap-table" id="cetak">
        <table>
            <thead class="bg-table">
                <tr>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Nama OPD</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Kode Program</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Nama dan Narasi Program</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Indikator Program</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Target Program</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Satuan</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Kode Kegiatan</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Nama dan Narasi Kegiatan</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Indikator Kegiatan</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Target Kegiatan</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Satuan</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Kode Sub Kegiatan</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Indikator Sub Kegiatan</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Target Sub Kegiatan</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Satuan</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Nama dan Narasi Sub Kegiatan</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Anggaran (Rp)</th>
                    <th class="kiri kanan atas bawah text_tengah v-align-middle">Keterangan</th>
                </tr>
                <tr>
                    <td class="kiri kanan bawah text_tengah">1</td>
                    <td class="kiri kanan bawah text_tengah">2</td>
                    <td class="kiri kanan bawah text_tengah">3</td>
                    <td class="kiri kanan bawah text_tengah">4</td>
                    <td class="kiri kanan bawah text_tengah">5</td>
                    <td class="kiri kanan bawah text_tengah">6</td>
                    <td class="kiri kanan bawah text_tengah">7</td>
                    <td class="kiri kanan bawah text_tengah">8</td>
                    <td class="kiri kanan bawah text_tengah">9</td>
                    <td class="kiri kanan bawah text_tengah">10</td>
                    <td class="kiri kanan bawah text_tengah">11</td>
                    <td class="kiri kanan bawah text_tengah">12</td>
                    <td class="kiri kanan bawah text_tengah">13</td>
                    <td class="kiri kanan bawah text_tengah">14</td>
                    <td class="kiri kanan bawah text_tengah">15</td>
                    <td class="kiri kanan bawah text_tengah">16</td>
                    <td class="kiri kanan bawah text_tengah">17</td>
                    <td class="kiri kanan bawah text_tengah">18</td>
                </tr>
            </thead>
            <tbody>
                <?php echo $tbody; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="kiri kanan bawah" colspan="15"></td>
                    <td class="kiri kanan bawah text_tengah font-weight-bold">Total</td>
                    <td class="kiri kanan bawah text_kanan" colspan="2"><?php echo number_format($total_rincian ?? 0, 0, ",", "."); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script>
    jQuery(document).ready(() => {
        run_download_excel();
        extend_action = '';
        extend_action += '<button class="btn btn-info m-2" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Cetak Laporan</button>';

        jQuery('#action-sipd #excel').after(extend_action);
    })
</script>