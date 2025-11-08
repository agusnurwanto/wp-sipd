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

$jenis_jadwal = '';
if (!empty($id_jadwal)) {
    $jadwal_renstra = $wpdb->get_row($wpdb->prepare("
        SELECT 
            relasi_perencanaan
        FROM data_jadwal_lokal
        WHERE id_jadwal_lokal = %d
    ", $id_jadwal), ARRAY_A);
    
    if (!empty($jadwal_renstra['relasi_perencanaan'])) {
        $jadwal_pemda = $wpdb->get_row($wpdb->prepare("
            SELECT 
                jenis_jadwal
            FROM data_jadwal_lokal
            WHERE id_jadwal_lokal = %d
        ", $jadwal_renstra['relasi_perencanaan']), ARRAY_A);
        
        if (!empty($jadwal_pemda['jenis_jadwal'])) {
            $jenis_jadwal = strtoupper($jadwal_pemda['jenis_jadwal']);
        }
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
    <div class="cetak" style="padding: 5px; overflow: auto;">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h1 class="text-center table-title">
                Manajemen Risiko <?php echo $jenis_jadwal; ?> / RENSTRA <br><?php echo $nama_skpd; ?><br>Tahun <?php echo $input['tahun_anggaran']; ?>
            </h1>
            <div id='aksi-wpsipd'></div>
            <div class="wrap-table">
                <table id="cetak" title="<?php echo $jenis_jadwal; ?> RENSTRA SKPD" class="table_manrisk_rpjmd_renstra table-bordered" cellpadding="2" cellspacing="0" contenteditable="false">
                    <thead style="background: #ffc491; text-align:center;">
                        <tr>
                            <th colspan="7"><?php echo $jenis_jadwal; ?></th>
                            <th colspan="7">RENSTRA</th>
                            <th rowspan="2">Program Prioritas terkait di RPJMN/ Indikator Program</th>
                            <th rowspan="2">Sektor Unggulan</th>
                            <th rowspan="2">Isu Terkini</th>
                            <th rowspan="2">Aksi</th>
                        </tr>
                        <tr>
                            <th>Tujuan <?php echo $jenis_jadwal; ?></th>
                            <th>Indikator Tujuan</th>
                            <th>Sasaran <?php echo $jenis_jadwal; ?></th>
                            <th>Indikator Sasaran</th>
                            <th>Program <?php echo $jenis_jadwal; ?></th>
                            <th>Indikator Program</th>
                            <th>OPD/ Unit Pengampu</th>
                            <th>Tujuan Renstra</th>
                            <th>Indikator Tujuan</th>
                            <th>Sasaran Renstra</th>
                            <th>Indikator Sasaran</th>
                            <th>Program Renstra</th>
                            <th>Indikator Program</th>
                            <th>Anggaran Program TA <?php echo $input['tahun_anggaran']; ?></th>
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
                            <th>(17)</th>
                            <th>(18)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal crud edit-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 60%; margin-top: 50px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Tujuan / Sasaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">
                <form id="formEdit">
                    <input type="hidden" value="" id="id_data">
                    <input type="hidden" value="" id="id_program">
                    <div class="form-group">
                        <div class="alert alert-info text-sm-left" role="alert" id="alertprogram"></div>
                        <div class="alert alert-info text-sm-left" role="alert" id="alertindikatorprogram"></div>
                    </div>
                    <div class="form-group">
                        <label for="program_prioritas">Program Prioritas terkait di RPJMN/ Indikator Program</label>
                        <input type="text" class="form-control" id="program_prioritas" name="program_prioritas" required>
                    </div>
                    <div class="form-group">
                        <label for="sektor_unggulan">Sektor Unggulan</label>
                        <input type="text" class="form-control" id="sektor_unggulan" name="sektor_unggulan" required>
                    </div>
                    <div class="form-group">
                        <label for="isu_terkini">Isu Terkini</label>
                        <input type="text" class="form-control" id="isu_terkini" name="isu_terkini" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_rpjmd_renstra(); return false">Simpan</button>
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
                'nama_skpd': '<?php echo $nama_skpd; ?>',
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

    function edit_rpjmd_renstra(id, id_program) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'edit_rpjmd_renstra',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id_jadwal: <?php echo $id_jadwal; ?>,
                id: id,
                id_program: id_program
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();

                if (response.status === 'success') {
                    let data = response.data || {}; 

                    jQuery("#id_data").val(data.id ?? id);
                    jQuery("#id_program").val(data.id_program ?? id_program);
                    jQuery('#alertprogram').text('Program: ' + (data.nama_program || ''));
                    jQuery('#alertindikatorprogram').text('Indikator Program: ' + (data.indikator_program || ''));
                    jQuery('#program_prioritas').val(data.program_prioritas ?? '');
                    jQuery('#sektor_unggulan').val(data.sektor_unggulan ?? '');
                    jQuery('#isu_terkini').val(data.isu_terkini ?? '');

                    jQuery('#editModalLabel').show();
                    jQuery('#editModal').modal('show');

                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat memuat data!');
            }
        });
    }

    function submit_rpjmd_renstra() {
        let id = jQuery('#id_data').val();
        let id_program = jQuery("#id_program").val();
        let program_prioritas = jQuery("#program_prioritas").val();
        let sektor_unggulan = jQuery("#sektor_unggulan").val();
        let isu_terkini = jQuery("#isu_terkini").val();

        jQuery('#wrap-loading').show();

        jQuery.ajax({
            method: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'submit_rpjmd_renstra',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id_jadwal: <?php echo $id_jadwal; ?>,
                id: id, 
                id_program: id_program,
                program_prioritas: program_prioritas,
                sektor_unggulan: sektor_unggulan,
                isu_terkini: isu_terkini
            },
            success: function(response) {
                jQuery('#wrap-loading').hide();
                alert(response.message);
                if (response.status === 'success') {
                    jQuery('#editModal').modal('hide');
                    get_table_rpjmd_renstra();
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }
</script>