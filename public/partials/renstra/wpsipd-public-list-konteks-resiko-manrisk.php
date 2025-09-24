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
      AND is_skpd=1
    ORDER BY kode_skpd ASC
    ", $input['tahun_anggaran']),
    ARRAY_A
);
$tbody = '';
$no = 1;
foreach ($data_unit as $id_sub_skpd => $unit) {
    $title = 'Detail Manrisk Konteks Resiko | ' . $input['tahun_anggaran'];
    $shortcode = '[detail_konteks_resiko_manrisk tahun_anggaran="' . $input['tahun_anggaran'] . '"]';
    $update = false;
    $url_skpd = $this->generatePage($title, $input['tahun_anggaran'], $shortcode, $update);
    $tbody .= "<tr>";
    $tbody .= "<td style='text-transform: uppercase;' class='text-center'>". $no++ ."</td>";
    $tbody .= "<td style='text-transform: uppercase;'><a target='_blank' href='" . $url_skpd . "&id_skpd=" . $unit['id_skpd'] . "'>" . $unit['kode_skpd'] . " " . $unit['nama_skpd'] . "</a></td>";
    $tbody .= "</tr>";
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
            <h1 class="text-center table-title">Konteks Resiko Tahun <?php echo $input['tahun_anggaran']; ?></h1>
            <div class="wrap-table">
                <table id="cetak" title="Konteks Resiko SKPD" class="table table-bordered table_dokumen_skpd">
                    <thead style="background: #ffc491;">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama Perangkat Daerah</th>
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
<script>
    jQuery(document).ready(function() {
        jQuery('.table_dokumen_skpd').dataTable({
            aLengthMenu: [
                [5, 10, 25, 100, -1],
                [5, 10, 25, 100, "All"]
            ],
            iDisplayLength: -1
        });
    });
</script>