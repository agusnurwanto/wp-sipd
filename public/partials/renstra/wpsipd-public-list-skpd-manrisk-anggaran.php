<?php
global $wpdb;

if (!defined('WPINC')) {
    die;
}

$input = shortcode_atts(array(
    'tahun_anggaran' => '2023'
), $atts);

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

$tbody = '';
$total_pagu = 0;
$no = 1;
foreach ($data_unit as $id_sub_skpd => $unit) {
    $title = 'Detail Pertimbangan Manajemen Anggaran | ' . $input['tahun_anggaran'];
    $shortcode = '[detail_manrisk_anggaran tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
    $update = false;
    $url_skpd = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, $update);

    $margin_left = 0;
    if ($unit['is_skpd'] != 1) {
        $margin_left = 40;
    }

    $data_program = $this->get_program($unit['id_skpd'], $input['tahun_anggaran']);
    $pagu_total_skpd = 0;
    if (!empty($data_program)) {
        foreach ($data_program as $v) {
            $pagu_total_skpd += $v['total_pagu'];
        }
    }

    $tbody .= "
    <tr>
        <td style='text-transform: uppercase;' class='text-center'>" . $no++ . "</td>
        <td style='text-transform: uppercase;'><a target='_blank' style='margin-left: " . $margin_left . "px;' href='" . $url_skpd . "&id_skpd=" . $unit['id_skpd'] . "'>" . $unit['kode_skpd'] . " " . $unit['nama_skpd'] . "</a><small class='text-muted font-weight-bold'> ( " . $unit['nipkepala'] . " " . $unit['namakepala'] .  ")</small></td>
        <td class='text-right'>{$this->_number_format($pagu_total_skpd)}</td>
    </tr>";

    $total_pagu += $pagu_total_skpd;
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

    .table_dokumen_skpd thead {
        position: sticky;
        top: -6px;
    }

    .table_dokumen_skpd thead th {
        vertical-align: middle;
    }

    .table_dokumen_skpd tfoot {
        position: sticky;
        bottom: 0;
    }
</style>
<div class="container-md">
    <div class="cetak">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h1 class="text-center table-title">Pertimbangan Manajemen Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
            <div class="wrap-table">
                <table id="cetak" title="Pertimbangan Manajemen Anggaran" class="table table-bordered table_dokumen_skpd">
                    <thead style="background: #ffc491;">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama Perangkat Daerah</th>
                            <th class="text-center">Anggaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $tbody; ?>
                    </tbody>
                    <tfoot style="background: #ffc491;">
                        <tr>
                            <td class="text-center font-weight-bold" colspan="2">Total Anggaran</td>
                            <td class="text-center font-weight-bold"><?php echo $this->_number_format($total_pagu); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>