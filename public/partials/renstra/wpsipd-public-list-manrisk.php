<?php
global $wpdb;

if (!defined('WPINC')) {
    die;
}

$input = shortcode_atts(array(
    'tahun_anggaran' => '2023',
    'id_skpd' => 0
), $atts);

$data_unit = $wpdb->get_results(
    $wpdb->prepare("
    SELECT 
        *   
    FROM data_unit 
    WHERE active=1 
      AND tahun_anggaran=%d
      AND id_skpd=%d
    ORDER BY kode_skpd ASC
    ", $input['tahun_anggaran'], $input['id_skpd']),
    ARRAY_A
);
// print_r($data_unit); die($wpdb->last_query);
$tbody = '';
foreach ($data_unit as $id_sub_skpd => $unit) {
    $nama_skpd = $unit['nama_skpd'];
    
    $pages = [
        [
            'title' => 'Halaman Detail Manrisk Konteks Resiko | ' . $input['tahun_anggaran'],
            'shortcode' => '[detail_konteks_resiko_manrisk tahun_anggaran="' . $input['tahun_anggaran'] . '"]',
            'label' => 'Konteks Resiko'
        ],
        [
            'title' => 'Halaman Detail Manrisk RPJMD | ' . $input['tahun_anggaran'],
            'shortcode' => '[detail_rpjmd_manrisk tahun_anggaran="' . $input['tahun_anggaran'] . '"]',
            'label' => 'RPJMD'
        ],
        [
            'title' => 'Halaman Detail Manrisk Tujuan Sasaran | ' . $input['tahun_anggaran'],
            'shortcode' => '[detail_tujuan_sasaran_manrisk tahun_anggaran="' . $input['tahun_anggaran'] . '"]',
            'label' => 'Tujuan Sasaran'
        ],
        [
            'title' => 'Halaman Detail Manrisk Program Kegiatan | ' . $input['tahun_anggaran'],
            'shortcode' => '[detail_program_kegiatan_manrisk tahun_anggaran="' . $input['tahun_anggaran'] . '"]',
            'label' => 'Program Kegiatan'
        ],
        [
            'title' => 'Halaman Detail Manrisk Kecurangan MCP | ' . $input['tahun_anggaran'],
            'shortcode' => '[detail_mcp_manrisk tahun_anggaran="' . $input['tahun_anggaran'] . '"]',
            'label' => 'Kecurangan MCP'
        ]
    ];
    
    $tbody .= "<tr>";
    $tbody .= "</td>";
    $tbody .= "</tr>";
    
    foreach ($pages as $page) {
        $update_page = false;
        $url_page = $this->generatePage($page['title'], $input['tahun_anggaran'], $page['shortcode'], $update_page);
        
        $tbody .= "<tr>";
        $tbody .= "<td style='text-transform: uppercase;'>";
        $tbody .= "<a target='_blank' href='" . $url_page . "&id_skpd=" . $unit['id_skpd'] . "'>";
        $tbody .= "" . $page['label'];
        $tbody .= "</a>";
        $tbody .= "</td>";
        $tbody .= "</tr>";
    }
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
    .table_manrisk {
        font-size: 14px !important;
        max-width: 1000px;
        margin: 0 auto;
    }

    .table_manrisk th,
    .table_manrisk td {
        padding: 8px 12px !important;
        font-size: 14px !important;
        vertical-align: middle;
    }

    .table_manrisk th {
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .table_manrisk {
            font-size: 12px !important;
            max-width: 100%;
        }
        
        .table_manrisk th,
        .table_manrisk td {
            padding: 6px 8px !important;
            font-size: 12px !important;
        }
    }
</style>
<div class="container-md">
    <div class="cetak">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h1 class="text-center table-title">Manajemen Resiko<br><?php echo $nama_skpd; ?><br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
            <div class="wrap-table">
                <table id="cetak" title="Manajemen Resiko Tujuan / Sasaran SKPD" class="table table-bordered table_manrisk">
                    <thead style="background: #ffc491;">
                        <tr>
                            <th class="text-center">Menu Manajemen Resiko</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $tbody; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>