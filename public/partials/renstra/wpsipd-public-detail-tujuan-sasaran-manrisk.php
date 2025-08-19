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

$selected_tujuan_sasaran = "<option value='-1'>Pilih Tujuan / Sasaran</option>";
$id_jadwal = 0;
if (!empty($cek_jadwal_renja)) {
    foreach ($cek_jadwal_renja as $jadwal_renja) {
        $id_jadwal = $jadwal_renja['relasi_perencanaan'];
        $get_data_tujuan = $wpdb->get_results(
            $wpdb->prepare("
                SELECT 
                    id, 
                    tujuan_teks, 
                    nama_bidang_urusan
                FROM data_renstra_tujuan
                WHERE id_unit=%d
                  AND id_jadwal=%d 
                  AND id_unik_indikator IS NULL
                  AND active=1
                ORDER BY id
            ", $id_skpd, $id_jadwal),
            ARRAY_A
        );

        $sasaran = $wpdb->get_results(
            $wpdb->prepare("
                SELECT 
                    id, 
                    sasaran_teks, 
                    nama_bidang_urusan
                FROM data_renstra_sasaran 
                WHERE active=1 
                  AND id_jadwal=%d
                  AND id_unit=%d 
                  AND id_unik_indikator IS NULL
                ORDER BY id
            ", $id_jadwal, $id_skpd),
            ARRAY_A
        );

        foreach ($get_data_tujuan as $t) {
            $nama_bidang = preg_replace('/^\d+(\.\d+)*\s*/', '', $t['nama_bidang_urusan']);
            $selected_tujuan_sasaran .= "<option value='{$t['id']}' data-type='0'>Tujuan | {$t['tujuan_teks']} | {$nama_bidang}</option>";
        }

        foreach ($sasaran as $s) {
            $nama_bidang = preg_replace('/^\d+(\.\d+)*\s*/', '', $s['nama_bidang_urusan']);
            $selected_tujuan_sasaran .= "<option value='{$s['id']}' data-type='1'>Sasaran | {$s['sasaran_teks']} | {$nama_bidang}</option>";
        }

    }
}

$get_data_sesudah = $wpdb->get_results($wpdb->prepare("
    SELECT 
        * 
    FROM data_tujuan_sasaran_manrisk_sesudah
    WHERE id_skpd = %d
      AND tahun_anggaran = %d
      AND active = 1
", $id_skpd, $input['tahun_anggaran']), ARRAY_A);
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

    .table_manrisk_tujuan_sasaran thead {
        position: sticky;
        top: -6px;
    }

    .table_manrisk_tujuan_sasaran thead th {
        vertical-align: middle;
    }

    .table_manrisk_tujuan_sasaran tfoot {
        position: sticky;
        bottom: 0;
    }
</style>
<div class="container-md">
    <div class="cetak" style="padding: 5px; overflow: auto; height: 80vh;">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h1 class="text-center table-title">Manajemen Resiko Tujuan / Sasaran <br><?php echo $nama_skpd; ?><br>Tahun <?php echo $input['tahun_anggaran']; ?>
            </h1>
            <div id='aksi-wpsipd'></div>
            <div class="wrap-table">
                <table id="cetak" title="Manajemen Resiko Tujuan / Sasaran SKPD" class="table_manrisk_tujuan_sasaran table-bordered"  cellpadding="2" cellspacing="0" contenteditable="false">
                    <thead style="background: #ffc491; text-align:center;">
                        <tr>
                            <th rowspan="3">No</th>
                            <!-- sebelum evaluasi -->
                            <th colspan="13">SEBELUM EVALUASI</th>
                            <th rowspan="3">Rencana Tindak Pengendalian</th>
                            <!-- setelah evaluasi -->
                            <?php if(!empty($get_data_sesudah)): ?>
                                <th colspan="13">SETELAH EVALUASI</th>
                                <th rowspan="3">Rencana Tindak Pengendalian</th>
                            <?php endif; ?>
                            <th rowspan="3">Aksi</th>
                        </tr>
                        <tr>
                            <!-- sebelum evaluasi -->
                            <th rowspan="2">Tujuan Strategis/ Sasaran Strategis Pemda OPD</th>
                            <th rowspan="2">Indikator Kinerja</th>
                            <th colspan="3">Risiko</th>
                            <th colspan="2">Sebab</th>
                            <th rowspan="2">Controllable / Uncontrollable</th>
                            <th colspan="2">Dampak</th>
                            <th rowspan="2">Skala Dampak</th>
                            <th rowspan="2">Skala Kemungkinan</th>
                            <th rowspan="2">Nilai Risiko (12x13)</th>
                            <!-- setelah evaluasi -->
                            <?php if(!empty($get_data_sesudah)): ?>
                            <th rowspan="2">Tujuan Strategis/ Sasaran Strategis Pemda OPD</th>
                            <th rowspan="2">Indikator Kinerja</th>
                            <th colspan="3">Risiko</th>
                            <th colspan="2">Sebab</th>
                            <th rowspan="2">Controllable / Uncontrollable</th>
                            <th colspan="2">Dampak</th>
                            <th rowspan="2">Skala Dampak</th>
                            <th rowspan="2">Skala Kemungkinan</th>
                            <th rowspan="2">Nilai Risiko (12x13)</th>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <!-- sebelum evaluasi -->
                            <th>Uraian</th>
                            <th>Kode Risiko</th>
                            <th>Pemilik Risiko</th>
                            <th>Uraian</th>
                            <th>Sumber</th>
                            <th>Uraian</th>
                            <th>Pihak yang Terkena</th>
                            <!-- setelah evaluasi -->
                            <?php if(!empty($get_data_sesudah)): ?>
                            <th>Uraian</th>
                            <th>Kode Risiko</th>
                            <th>Pemilik Risiko</th>
                            <th>Uraian</th>
                            <th>Sumber</th>
                            <th>Uraian</th>
                            <th>Pihak yang Terkena</th>
                            <?php endif; ?>
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
                            <?php if(!empty($get_data_sesudah)): ?>
                            <th>(17)</th>
                            <th>(18)</th>
                            <th>(19)</th>
                            <th>(20)</th>
                            <th>(21)</th>
                            <th>(22)</th>
                            <th>(23)</th>
                            <th>(24)</th>
                            <th>(25)</th>
                            <th>(26)</th>
                            <th>(27)</th>
                            <th>(28)</th>
                            <th>(29)</th>
                            <th>(30)</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal tambah tujuan sasaran sebelum-->
<div class="modal fade" id="tambahTujuanSasaranModal" tabindex="-1" role="dialog" aria-labelledby="tambahTujuanSasaranModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 60%; margin-top: 50px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TambahTujuanSasaranModalLabel">Tambah Tujuan / Sasaran</h5>
                <h5 class="modal-title" id="editTujuanSasaranModalLabel">Edit Tujuan / Sasaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">
                <form id="formTambahTujuanSasaran">
                    <input type="hidden" value="" id="id_data">
                    <div class="form-group">
                        <label for="nama_tujuan_sasaran">Nama Tujuan / Sasaran</label>
                        <select id="nama_tujuan_sasaran" style='display:block;width: 100%;' onchange="get_indikator();">
                            <?php echo $selected_tujuan_sasaran; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="indikator_kinerja">Indikator Kinerja</label>
                        <div id="indikator_kinerja_wrapper">
                            <select id="indikator_kinerja" style="display:block;width:100%;">
                                <option value="">Pilih Indikator</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="uraian_resiko">Uraian Resiko</label>
                        <input type="text" class="form-control" id="uraian_resiko" name="uraian_resiko" required>
                    </div>
                    <div class="form-group">
                        <label for="kode_resiko">Kode Resiko</label>
                        <input type="text" class="form-control" id="kode_resiko" name="kode_resiko" required>
                    </div>
                    <div class="form-group">
                        <label for="pemilik_resiko">Pemilik Resiko</label>
                        <input type="text" class="form-control" id="pemilik_resiko" name="pemilik_resiko" required>
                    </div>
                    <div class="form-group">
                        <label for="uraian_sebab">Uraian Sebab</label>
                        <input type="text" class="form-control" id="uraian_sebab" name="uraian_sebab" required>
                    </div>
                    <div class="form-group">
                        <label for="sumber_sebab">Sumber Sebab</label>
                        <input type="text" class="form-control" id="sumber_sebab" name="sumber_sebab" required>
                    </div>
                    <div class="form-group">
                        <label class="d-block">Controllable / Uncontrollable</label>
                        <tr>
                            <td>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="controllable_status" id="controllable_status_controllable" value="0">
                                    <label class="custom-control-label" for="controllable_status_controllable">Controllable</label>
                                </div>
                            </td>
                            <td>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="controllable_status" id="controllable_status_uncontrollable" value="1">
                                    <label class="custom-control-label" for="controllable_status_uncontrollable">Uncontrollable</label>
                                </div>
                            </td>
                        </tr>
                    </div>
                    <div class="form-group">
                        <label for="uraian_dampak">Uraian Dampak</label>
                        <input type="text" class="form-control" id="uraian_dampak" name="uraian_dampak" required>
                    </div>
                    <div class="form-group">
                        <label for="pihak_terkena">Pihak yang Terkena</label>
                        <input type="text" class="form-control" id="pihak_terkena" name="pihak_terkena" required>
                    </div>
                    <div class="form-group">
                        <label for="skala_dampak">Skala Dampak</label>
                        <input type="text" class="form-control" id="skala_dampak" name="skala_dampak" required>
                    </div>
                    <div class="form-group">
                        <label for="skala_kemungkinan">Skala Kemungkinan</label>
                        <input type="text" class="form-control" id="skala_kemungkinan" name="skala_kemungkinan" required>
                    </div>
                    <div class="form-group">
                        <label for="nilai_resiko">Nilai Resiko</label>
                        <input type="text" class="form-control" id="nilai_resiko" name="nilai_resiko" required>
                    </div>
                    <div class="form-group">
                        <label for="rencana_tindak_pengendalian">Rencana Tindak Pengendalian</label>
                        <textarea type="text" class="form-control" id="rencana_tindak_pengendalian" name="rencana_tindak_pengendalian" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_tujuan_sasaran(); return false">Simpan</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal tambah tujuan sasaran sesudah-->
<div class="modal fade" id="VerifikasiModal" tabindex="-1" role="dialog" aria-labelledby="VerifikasiModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 60%; margin-top: 50px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="VerifikasiModalLabel">Verifikasi Tujuan / Sasaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">
                <form id="formTambahTujuanSasaranSesudah">
                    <input type="hidden" value="" id="id_data_sesudah">
                    <input type="hidden" value="" id="id_sebelum">
                    <input type="hidden" value="" id="tipe_sebelum">
                    <div class="form-group">
                        <label for="get_tujuan_sasaran">Tujuan / Sasaran RPD</label>
                        <select id="get_tujuan_sasaran" style='display:block;width: 100%;' onchange="get_indikator();">
                            <?php echo $selected_tujuan_sasaran; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="get_indikator_kinerja">Indikator Kinerja</label>
                        <div id="get_indikator_kinerja_wrapper">
                            <select id="get_indikator_kinerja" style="display:block;width:100%;">
                                <option value="">Pilih Indikator</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nama_tujuan_sasaran_sesudah">Tujuan / Sasaran</label><span onclick="copy_tujuan_sasaran();" class="btn btn-primary" style="margin-left: 20px;">Copy dari tujuan / sasaran</span>
                        <input type="text" class="form-control" id="nama_tujuan_sasaran_sesudah" name="nama_tujuan_sasaran_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label for="indikator_kinerja_sesudah">Indikator Kinerja</label><span onclick="copy_indikator();" class="btn btn-primary" style="margin-left: 20px;">Copy dari indikator tujuan / sasaran</span>
                        <input type="text" class="form-control" id="indikator_kinerja_sesudah" name="indikator_kinerja_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label for="uraian_resiko_sesudah">Uraian Resiko</label>
                        <input type="text" class="form-control" id="uraian_resiko_sesudah" name="uraian_resiko_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label for="kode_resiko_sesudah">Kode Resiko</label>
                        <input type="text" class="form-control" id="kode_resiko_sesudah" name="kode_resiko_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label for="pemilik_resiko_sesudah">Pemilik Resiko</label>
                        <input type="text" class="form-control" id="pemilik_resiko_sesudah" name="pemilik_resiko_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label for="uraian_sebab_sesudah">Uraian Sebab</label>
                        <input type="text" class="form-control" id="uraian_sebab_sesudah" name="uraian_sebab_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label for="sumber_sebab_sesudah">Sumber Sebab</label>
                        <input type="text" class="form-control" id="sumber_sebab_sesudah" name="sumber_sebab_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label class="d-block">Controllable / Uncontrollable</label>
                        <tr>
                            <td>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="controllable_status_sesudah" id="controllable_status_controllable_sesudah" value="0">
                                    <label class="custom-control-label" for="controllable_status_controllable_sesudah">Controllable</label>
                                </div>
                            </td>
                            <td>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="controllable_status_sesudah" id="controllable_status_uncontrollable_sesudah" value="1">
                                    <label class="custom-control-label" for="controllable_status_uncontrollable_sesudah">Uncontrollable</label>
                                </div>
                            </td>
                        </tr>
                    </div>
                    <div class="form-group">
                        <label for="uraian_dampak_sesudah">Uraian Dampak</label>
                        <input type="text" class="form-control" id="uraian_dampak_sesudah" name="uraian_dampak_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label for="pihak_terkena_sesudah">Pihak yang Terkena</label>
                        <input type="text" class="form-control" id="pihak_terkena_sesudah" name="pihak_terkena_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label for="skala_dampak_sesudah">Skala Dampak</label>
                        <input type="text" class="form-control" id="skala_dampak_sesudah" name="skala_dampak_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label for="skala_kemungkinan_sesudah">Skala Kemungkinan</label>
                        <input type="text" class="form-control" id="skala_kemungkinan_sesudah" name="skala_kemungkinan_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label for="nilai_resiko_sesudah">Nilai Resiko</label>
                        <input type="text" class="form-control" id="nilai_resiko_sesudah" name="nilai_resiko_sesudah" required>
                    </div>
                    <div class="form-group">
                        <label for="rencana_tindak_pengendalian_sesudah">Rencana Tindak Pengendalian</label>
                        <textarea type="text" class="form-control" id="rencana_tindak_pengendalian_sesudah" name="rencana_tindak_pengendalian_sesudah" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_verif_tujuan_sasaran(); return false">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        get_table_tujuan_sasaran();
        run_download_excel('', '#aksi-wpsipd');
        jQuery('#aksi-wpsipd a').first().after('<a style="margin-left: 10px;" onclick="tambah_tujuan_sasaran(); return false;" href="#" class="btn btn-primary">Tambah Tujuan / Sasaran</a>');
    });

    function get_table_tujuan_sasaran() {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_table_tujuan_sasaran',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                'id_jadwal': <?php echo $id_jadwal; ?>,
                'id_skpd': <?php echo $id_skpd; ?>
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();
                console.log(response);
                if (response.status === 'success') {
                    jQuery('.table_manrisk_tujuan_sasaran tbody').html(response.data);
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

    function tambah_tujuan_sasaran() {
        jQuery('#TambahTujuanSasaranModalLabel').show();
        jQuery('#editTujuanSasaranModalLabel').hide();
        jQuery("#nama_tujuan_sasaran").val('');
        jQuery("#indikator_kinerja").val('');
        jQuery("#uraian_resiko").val('');
        jQuery("#kode_resiko").val('');
        jQuery("#pemilik_resiko").val('');
        jQuery("#uraian_sebab").val('');
        jQuery("#sumber_sebab").val('');
        jQuery("#controllable").val('');
        jQuery("#uncontrollable").val('');
        jQuery("#uraian_dampak").val('');
        jQuery("#pihak_terkena").val('');
        jQuery("#skala_dampak").val('');
        jQuery("#skala_kemungkinan").val('');
        jQuery("#nilai_resiko").val('');
        jQuery("#rencana_tindak_pengendalian").val('');
        jQuery('#tambahTujuanSasaranModal').modal('show');
    }

    function get_indikator(selected_indikator, tipe, callback) {
        let selected = null;

        if (jQuery('#nama_tujuan_sasaran').length > 0) {
            selected = jQuery('#nama_tujuan_sasaran').val();
        }

        if (!selected || selected === '-1' || selected === '') {
            if (jQuery('#get_tujuan_sasaran').length > 0) {
                selected = jQuery('#get_tujuan_sasaran').val();
            }
        }

        if (!selected || selected === '-1' || selected === '') {
            selected = 0;
            jQuery('#indikator_kinerja, #get_indikator_kinerja').html('<option value="0">Pilih Indikator</option>');
            if (typeof callback === "function") callback();
            return;
        }

        if (!tipe) {
            tipe = jQuery('#nama_tujuan_sasaran option:selected').data('type');
            if (!tipe) {
                tipe = jQuery('#get_tujuan_sasaran option:selected').data('type');
            }
        }

        jQuery("#wrap-loading").show();

        jQuery.ajax({
            method: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'get_indikator_tujuan_sasaran',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id: selected,
                tipe: tipe,
                selected_indikator: selected_indikator || 0
            },
            success: function (response) {
                jQuery("#wrap-loading").hide();
                if (response.status === 'success') {
                    jQuery('#indikator_kinerja, #get_indikator_kinerja').html(response.html);
                    if (typeof callback === "function") callback();
                } else {
                    alert(`GAGAL!\n${response.message}`);
                }
            }
        });
    }

    function edit_tujuan_sasaran_manrisk(id, id_tujuan_sasaran, id_indikator, tipe) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'edit_tujuan_sasaran_manrisk',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id: id ,
                id_tujuan_sasaran: id_tujuan_sasaran ,
                id_indikator: id_indikator, 
                tipe: tipe 
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    let data = response.data;
                    jQuery("#id_data").val(data.id);

                    jQuery("#nama_tujuan_sasaran option").prop("selected", false);
                    jQuery(`#nama_tujuan_sasaran option[value='${data.id_tujuan_sasaran}'][data-type='${tipe}']`).prop("selected", true);

                    get_indikator(data.id_indikator, tipe, function(){
                        jQuery("#indikator_kinerja").val(data.id_indikator);
                    });

                    jQuery("#uraian_resiko").val(data.uraian_resiko);
                    jQuery("#kode_resiko").val(data.kode_resiko);
                    jQuery("#pemilik_resiko").val(data.pemilik_resiko);
                    jQuery("#uraian_sebab").val(data.uraian_sebab);
                    jQuery("#sumber_sebab").val(data.sumber_sebab);
                    jQuery(`input[name='controllable_status'][value='${data.controllable}']`).prop('checked', true);
                    jQuery("#uraian_dampak").val(data.uraian_dampak);
                    jQuery("#pihak_terkena").val(data.pihak_terkena);
                    jQuery("#skala_dampak").val(data.skala_dampak);
                    jQuery("#skala_kemungkinan").val(data.skala_kemungkinan);
                    jQuery("#nilai_resiko").val(data.nilai_resiko);
                    jQuery("#rencana_tindak_pengendalian").val(data.rencana_tindak_pengendalian);

                    jQuery('#TambahTujuanSasaranModalLabel').hide();
                    jQuery('#editTujuanSasaranModalLabel').show();
                    jQuery('#tambahTujuanSasaranModal').modal('show');
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

    function submit_tujuan_sasaran() {
        let id = jQuery('#id_data').val();
        let selected = jQuery('#nama_tujuan_sasaran').val();
        let selected_indikator = jQuery("#indikator_kinerja").val();
        let tipe = jQuery('#nama_tujuan_sasaran option:selected').data('type');
        let uraian_resiko = jQuery("#uraian_resiko").val();
        let kode_resiko = jQuery("#kode_resiko").val();
        let pemilik_resiko = jQuery("#pemilik_resiko").val();
        let uraian_sebab = jQuery("#uraian_sebab").val();
        let sumber_sebab = jQuery("#sumber_sebab").val();
        let controllable_status = jQuery("input[name='controllable_status']:checked").val();
        let uraian_dampak = jQuery("#uraian_dampak").val();
        let pihak_terkena = jQuery("#pihak_terkena").val();
        let skala_dampak = jQuery("#skala_dampak").val();
        let skala_kemungkinan = jQuery("#skala_kemungkinan").val();
        let nilai_resiko = jQuery("#nilai_resiko").val();
        let rencana_tindak_pengendalian = jQuery("#rencana_tindak_pengendalian").val();

        jQuery('#wrap-loading').show();

        jQuery.ajax({
            method: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'submit_tujuan_sasaran',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id: id, 
                id_tujuan_sasaran: selected, 
                id_indikator: selected_indikator,
                tipe: tipe,
                uraian_resiko: uraian_resiko,
                kode_resiko: kode_resiko,
                pemilik_resiko: pemilik_resiko,
                uraian_sebab: uraian_sebab,
                sumber_sebab: sumber_sebab,
                controllable_status: controllable_status, 
                uraian_dampak: uraian_dampak,
                pihak_terkena: pihak_terkena,
                skala_dampak: skala_dampak,
                skala_kemungkinan: skala_kemungkinan,
                nilai_resiko: nilai_resiko,
                rencana_tindak_pengendalian: rencana_tindak_pengendalian
            },
            success: function(response) {
                jQuery('#wrap-loading').hide();
                alert(response.message);
                if (response.status === 'success') {
                    jQuery('#tambahTujuanSasaranModal').modal('hide');
                    get_table_tujuan_sasaran();
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }

    function hapus_tujuan_sasaran_manrisk(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus data ini? Data sebelum dan sesudah evaluasi akan terhapus')) {
            return;
        }
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'hapus_tujuan_sasaran_manrisk',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                id: id
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    alert(response.message);
                    get_table_tujuan_sasaran();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                jQuery('#wrap-loading').hide();
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }

    function verif_tujuan_sasaran_manrisk(id, id_sebelum, id_tujuan_sasaran, id_indikator, tipe_sesudah) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'verif_tujuan_sasaran_manrisk',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id: id,
                id_sebelum: id_sebelum,
                id_tujuan_sasaran: id_tujuan_sasaran || 0,
                id_indikator: id_indikator || 0,
                tipe: tipe_sesudah
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    let data = response.data;
                    if (data && Object.keys(data).length > 0) {
                        jQuery("#id_data_sesudah").val(data.id);

                        jQuery("#get_tujuan_sasaran option").prop("selected", false);
                        jQuery(`#get_tujuan_sasaran option[value='${data.id_tujuan_sasaran}'][data-type='${data.tipe}']`).prop("selected", true);

                        get_indikator(data.id_indikator, data.tipe, function(){
                            jQuery("#get_indikator_kinerja").val(data.id_indikator);
                        });

                        jQuery('#nama_tujuan_sasaran_sesudah').val(data.tujuan_sasaran_teks);
                        jQuery('#indikator_kinerja_sesudah').val(data.indikator_teks);
                        jQuery("#uraian_resiko_sesudah").val(data.uraian_resiko);
                        jQuery("#kode_resiko_sesudah").val(data.kode_resiko);
                        jQuery("#pemilik_resiko_sesudah").val(data.pemilik_resiko);
                        jQuery("#uraian_sebab_sesudah").val(data.uraian_sebab);
                        jQuery("#sumber_sebab_sesudah").val(data.sumber_sebab);
                        jQuery(`input[name='controllable_status_sesudah'][value='${data.controllable}']`).prop('checked', true);
                        jQuery("#uraian_dampak_sesudah").val(data.uraian_dampak);
                        jQuery("#pihak_terkena_sesudah").val(data.pihak_terkena);
                        jQuery("#skala_dampak_sesudah").val(data.skala_dampak);
                        jQuery("#skala_kemungkinan_sesudah").val(data.skala_kemungkinan);
                        jQuery("#nilai_resiko_sesudah").val(data.nilai_resiko);
                        jQuery("#rencana_tindak_pengendalian_sesudah").val(data.rencana_tindak_pengendalian);
                    } else {
                        jQuery("#id_data_sesudah").val('');
                        jQuery("#get_tujuan_sasaran").val(0);
                        jQuery("#get_indikator_kinerja").html('<option value="0">Pilih Indikator</option>');
                        jQuery('#nama_tujuan_sasaran_sesudah').val('');
                        jQuery('#indikator_kinerja_sesudah').val('');
                        jQuery('#uraian_resiko_sesudah').val('');
                        jQuery('#kode_resiko_sesudah').val('');
                        jQuery('#pemilik_resiko_sesudah').val('');
                        jQuery('#uraian_sebab_sesudah').val('');
                        jQuery('#sumber_sebab_sesudah').val('');
                        jQuery("input[name='controllable_status_sesudah']").prop('checked', false);
                        jQuery('#uraian_dampak_sesudah').val('');
                        jQuery('#pihak_terkena_sesudah').val('');
                        jQuery('#skala_dampak_sesudah').val('');
                        jQuery('#skala_kemungkinan_sesudah').val('');
                        jQuery('#nilai_resiko_sesudah').val('');
                        jQuery('#rencana_tindak_pengendalian_sesudah').val('');
                    }

                    jQuery('#id_sebelum').val(id_sebelum);
                    jQuery('#VerifikasiModal').hide();
                    jQuery('#VerifikasiModalLabel').show();
                    jQuery('#VerifikasiModal').modal('show');
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


    function submit_verif_tujuan_sasaran() {
        let id = jQuery('#id_data_sesudah').val();
        let tipe = jQuery('#get_tujuan_sasaran option:selected').data('type');
        let id_sebelum = jQuery('#id_sebelum').val();
        let selected = jQuery('#get_tujuan_sasaran').val();
        let selected_indikator = jQuery("#get_indikator_kinerja").val();
        let tujuan_sasaran = jQuery('#nama_tujuan_sasaran_sesudah').val();
        let indikator = jQuery("#indikator_kinerja_sesudah").val();
        let uraian_resiko = jQuery("#uraian_resiko_sesudah").val();
        let kode_resiko = jQuery("#kode_resiko_sesudah").val();
        let pemilik_resiko = jQuery("#pemilik_resiko_sesudah").val();
        let uraian_sebab = jQuery("#uraian_sebab_sesudah").val();
        let sumber_sebab = jQuery("#sumber_sebab_sesudah").val();
        let controllable_status = jQuery("input[name='controllable_status_sesudah']:checked").val();
        let uraian_dampak = jQuery("#uraian_dampak_sesudah").val();
        let pihak_terkena = jQuery("#pihak_terkena_sesudah").val();
        let skala_dampak = jQuery("#skala_dampak_sesudah").val();
        let skala_kemungkinan = jQuery("#skala_kemungkinan_sesudah").val();
        let nilai_resiko = jQuery("#nilai_resiko_sesudah").val();
        let rencana_tindak_pengendalian = jQuery("#rencana_tindak_pengendalian_sesudah").val();

        jQuery('#wrap-loading').show();

        jQuery.ajax({
            method: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'submit_verif_tujuan_sasaran',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id: id,  
                id_sebelum: id_sebelum, 
                id_tujuan_sasaran: selected, 
                id_indikator: selected_indikator,
                tujuan_sasaran: tujuan_sasaran, 
                indikator: indikator,
                tipe: tipe,
                uraian_resiko: uraian_resiko,
                kode_resiko: kode_resiko,
                pemilik_resiko: pemilik_resiko,
                uraian_sebab: uraian_sebab,
                sumber_sebab: sumber_sebab,
                controllable_status: controllable_status, 
                uraian_dampak: uraian_dampak,
                pihak_terkena: pihak_terkena,
                skala_dampak: skala_dampak,
                skala_kemungkinan: skala_kemungkinan,
                nilai_resiko: nilai_resiko,
                rencana_tindak_pengendalian: rencana_tindak_pengendalian
            },
            success: function(response) {
                jQuery('#wrap-loading').hide();
                alert(response.message);
                if (response.status === 'success') {
                    jQuery('#VerifikasiModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }
    function copy_tujuan_sasaran() {
        let tujuansasaranteks = jQuery("#get_tujuan_sasaran").find(':selected').text();
        let selected_val = jQuery("#get_tujuan_sasaran").val();

        if (!selected_val || selected_val === '0' || selected_val === '-1') {
            tujuansasaranteks = '';
        } else {
            let split = tujuansasaranteks.split('|');
            if (split.length > 1) {
                tujuansasaranteks = split[1].trim();
            }
        }
        jQuery("#nama_tujuan_sasaran_sesudah").val(tujuansasaranteks);
    }

    function copy_indikator() {
        let indikatortext = jQuery("#get_indikator_kinerja").find(':selected').text();
        let selected_val = jQuery("#get_indikator_kinerja").val();

        if (!selected_val || selected_val === '0' || selected_val === '-1') {
            indikatortext = '';
        }
        jQuery("#indikator_kinerja_sesudah").val(indikatortext);
    }

</script>