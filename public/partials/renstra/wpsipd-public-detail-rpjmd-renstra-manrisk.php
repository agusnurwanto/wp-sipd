<?php
global $wpdb;

if (!defined('WPINC')) {
    die;
}

$input = shortcode_atts(array(
    'tahun_anggaran' => '2023'
), $atts);

if (!empty($_GET) && !empty($_GET['id_skpd'])) {
    $id_skpd = $_GET['id_skpd'];
}
$get_jadwal = $wpdb->get_row(
    $wpdb->prepare('
        SELECT 
            id_jadwal_lokal
        FROM data_jadwal_lokal
        WHERE id_tipe = %d
          AND tahun_anggaran = %d
    ', 20, $input['tahun_anggaran']),
    ARRAY_A
);
$data_jadwal = $this->get_data_jadwal_by_id_jadwal_lokal($get_jadwal);
$timezone = get_option('timezone_string');
$sql_unit = $wpdb->prepare("
    SELECT 
        *
    FROM data_unit 
    WHERE 
        tahun_anggaran=%d
        AND id_skpd =%d
        AND active=1
    order by id_skpd ASC
    ", $input['tahun_anggaran'], $id_skpd);
$unit = $wpdb->get_results($sql_unit, ARRAY_A);
$unit_utama = $unit;
if ($unit[0]['id_unit'] != $unit[0]['id_skpd']) {
    $sql_unit_utama = $wpdb->prepare("
        SELECT 
            *
        FROM data_unit
        WHERE 
            tahun_anggaran=%d
            AND id_skpd=%d
            AND active=1
        order by id_skpd ASC
        ", $input['tahun_anggaran'], $unit[0]['id_unit']);
    $unit_utama = $wpdb->get_results($sql_unit_utama, ARRAY_A);
}

$unit = (!empty($unit)) ? $unit : array();
$nama_skpd = (!empty($unit[0]['nama_skpd'])) ? $unit[0]['nama_skpd'] : '-';
$nama_pemda = get_option('_crb_daerah');
if (empty($nama_pemda) || $nama_pemda == 'false') {
    $nama_pemda = '';
}

$cek_jadwal_renja = $wpdb->get_results(
    $wpdb->prepare('
        SELECT 
            *
        FROM data_jadwal_lokal
        WHERE status = %d
          AND id_tipe = %d
          AND tahun_anggaran = %d
    ', 0, 16, $input['tahun_anggaran']),
    ARRAY_A
);

$id_jadwal = 0;
if (!empty($cek_jadwal_renja)) {
    foreach ($cek_jadwal_renja as $jadwal_renja) {
        $id_jadwal = $jadwal_renja['relasi_perencanaan'];
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

    .table_manrisk_rpjmd_renstra thead {
        position: sticky;
        top: -6px;
    }

    .table_manrisk_rpjmd_renstra thead th {
        vertical-align: middle;
    }

    .table_manrisk_rpjmd_renstra tfoot {
        position: sticky;
        bottom: 0;
    }
</style>
<div class="container-md">
    <div class="cetak" style="padding: 5px; overflow: auto; height: 80vh;">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h1 class="text-center table-title" style="padding-top: 80px">
                Manajemen Resiko RPJMD / RENSTRA <br><?php echo $nama_skpd; ?><br>Tahun <?php echo $input['tahun_anggaran']; ?>
            </h1>
            <div id='aksi-wpsipd'></div>
            <div class="wrap-table">
                <table id="cetak" title="RPJMD / RENSTRA SKPD" class="table_manrisk_rpjmd_renstra table-bordered" cellpadding="2" cellspacing="0" contenteditable="false">
                    <thead style="background: #ffc491; text-align:center;">
                        <tr>
                            <th colspan="7">RPD</th>
                            <th colspan="5">RENSTRA</th>
                            <th rowspan="2">Program Prioritas terkait di RPJMN/ Indikator Program</th>
                            <th rowspan="2">Sektor Unggulan</th>
                            <th rowspan="2">Isu Terkini</th>
                        </tr>
                        <tr>
                            <th>Tujuan RPD</th>
                            <th>Indikator Tujuan</th>
                            <th>Sasaran RPD</th>
                            <th>Indikator Sasaran</th>
                            <th>Program RPD</th>
                            <th>Indikator Program</th>
                            <th>OPD/ Unit Pengampu</th>
                            <th>Tujuan/ Sasaran dalam Renstra</th>
                            <th>Indikator Tujuan/ Sasaran</th>
                            <th>Program</th>
                            <th>Indikator Program</th>
                            <th>Anggaran Program TA 2024</th>
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
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        get_table_rpjmd_renstra();
        run_download_excel('', '#aksi-wpsipd');
    });

    function get_table_rpjmd_renstra() {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_table_rpjmd_renstra',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                'id_jadwal': <?php echo $id_jadwal; ?>,
                'id_skpd': <?php echo $id_skpd; ?>
            },
            success: function(response) {
                jQuery('#wrap-loading').hide();
                console.log(response);
                if (response.status === 'success') {
                    jQuery('.table_manrisk_rpjmd_renstra tbody').html(response.data);                    
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat memuat tabel!');
            }
        });
    }
</script>