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
$kode_skpd = (!empty($unit[0]['kode_skpd'])) ? $unit[0]['kode_skpd'] : '-';
$nama_pemda = get_option('_crb_daerah');
if (empty($nama_pemda) || $nama_pemda == 'false') {
    $nama_pemda = '';
}
$unit = $wpdb->get_row($wpdb->prepare("
    SELECT 
        id_skpd,
        nama_skpd,
        bidur_1,
        bidur_2,
        bidur_3
    FROM 
        data_unit
    WHERE 
        id_skpd = %d
        AND tahun_anggaran = %d
        AND active = 1
    LIMIT 1
", $id_skpd, $input['tahun_anggaran']));

$nama_bidang_urusan = '';
if (!empty($unit)) {
    $arr_bidur = array_filter(array($unit->bidur_1, $unit->bidur_2, $unit->bidur_3),
        function($v) {
            return $v !== null && $v !== ''; 
        }
    );  
    if (!empty($arr_bidur)) {
        $placeholders = implode(',', array_fill(0, count($arr_bidur), '%d'));
        $sql = $wpdb->prepare("
            SELECT nama_bidang_urusan 
            FROM data_prog_keg 
            WHERE id_bidang_urusan IN ($placeholders)
            AND tahun_anggaran = %d
            AND active = 1
            GROUP BY nama_bidang_urusan
        ", array_merge($arr_bidur, array($input['tahun_anggaran'])));

        $nama_bidur = $wpdb->get_col($sql);

        // hilangkan angka dan titik di awal, misal "1.01 " atau "2.19 "
        $nama_bersih = array();
        foreach ($nama_bidur as $n) {
            $nama_bersih[] = preg_replace('/^[0-9.]+\s*/', '', trim($n));
        }

        $nama_bidang_urusan = implode('<br>', $nama_bersih);
    }
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

$nama_periode_dinilai = '-';

if ($id_jadwal != 0) {
    $get_nama_jadwal = $wpdb->get_row(
        $wpdb->prepare('
            SELECT nama
            FROM data_jadwal_lokal
            WHERE id_jadwal_lokal = %d
        ', $id_jadwal)
    );

    if (!empty($get_nama_jadwal)) {
        $nama_periode_dinilai = $get_nama_jadwal->nama;
    }
}

$get_data_sesudah = $wpdb->get_results($wpdb->prepare("
    SELECT 
        * 
    FROM data_program_kegiatan_manrisk_sesudah
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

    .table_manrisk_program_kegiatan thead {
        position: sticky;
        top: -6px;
    }

    .table_manrisk_program_kegiatan thead th {
        vertical-align: middle;
    }

    .table_manrisk_program_kegiatan tfoot {
        position: sticky;
        bottom: 0;
    }
</style>
<div class="container-md">
    <div class="cetak" style="padding: 5px; overflow: auto; height: 80vh;">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h1 class="text-center table-title" style="padding-top: 80px">
                Manajemen Resiko Program / Kegiatan <br><?php echo $nama_skpd; ?><br>Tahun <?php echo $input['tahun_anggaran']; ?>
            </h1>
            <div id='aksi-wpsipd'></div>
            <div class="wrap-table">
            <table class="table table-bordered">
                <tr>
                    <th style="width: 20%;">Nama Pemda</th>
                    <td><strong><?php echo $nama_pemda; ?></strong></td>
                </tr>
                <tr>
                    <th>Nama OPD</th>
                    <td><strong><?php echo $nama_skpd; ?></strong></td>
                </tr>
                <tr>
                    <th>Tahun Penilaian</th>
                    <td><strong><?php echo $input['tahun_anggaran']; ?></strong></td>
                </tr>
                <tr>
                    <th>Periode yang Dinilai</th>
                    <td><strong><?php echo ($nama_periode_dinilai); ?></strong></td>
                </tr>
                <tr>
                    <th>Urusan Pemerintahan</th>
                    <td><strong><?php echo $nama_bidang_urusan; ?></strong></td>
                </tr>
            </table>
                <table id="cetak" title="Manajemen Resiko Program / Kegiatan SKPD" class="table_manrisk_program_kegiatan table-bordered" cellpadding="2" cellspacing="0" contenteditable="false">
                    <thead style="background: #ffc491; text-align:center;">
                        <tr>
                            <th rowspan="3">No</th>
                            <!-- sebelum evaluasi -->
                            <th colspan="13">SEBELUM EVALUASI</th>
                            <th rowspan="3">Rencana Tindak Pengendalian</th>
                            <!-- setelah evaluasi -->
                            <th colspan="13" class="kolom-sesudah" style="<?php echo empty($get_data_sesudah) ? 'display:none' : ''; ?>">SETELAH EVALUASI</th>
                            <th rowspan="3" class="kolom-sesudah" style="<?php echo empty($get_data_sesudah) ? 'display:none' : ''; ?>">Rencana Tindak Pengendalian</th>
                            <th rowspan="3">Aksi</th>
                        </tr>
                        <tr>
                            <!-- sebelum evaluasi -->
                            <th rowspan="2">Program / Kegiatan Pemda OPD</th>
                            <th rowspan="2">Indikator Kinerja</th>
                            <th colspan="3">Risiko</th>
                            <th colspan="2">Sebab</th>
                            <th rowspan="2">Controllable / Uncontrollable</th>
                            <th colspan="2">Dampak</th>
                            <th rowspan="2">Skala Dampak</th>
                            <th rowspan="2">Skala Kemungkinan</th>
                            <th rowspan="2">Nilai Risiko (12x13)</th>
                            <!-- setelah evaluasi -->
                            <th rowspan="2" class="kolom-sesudah">Program / Kegiatan Pemda OPD</th>
                            <th rowspan="2" class="kolom-sesudah">Indikator Kinerja</th>
                            <th colspan="3" class="kolom-sesudah">Risiko</th>
                            <th colspan="2" class="kolom-sesudah">Sebab</th>
                            <th rowspan="2" class="kolom-sesudah">Controllable / Uncontrollable</th>
                            <th colspan="2" class="kolom-sesudah">Dampak</th>
                            <th rowspan="2" class="kolom-sesudah">Skala Dampak</th>
                            <th rowspan="2" class="kolom-sesudah">Skala Kemungkinan</th>
                            <th rowspan="2" class="kolom-sesudah">Nilai Risiko (26x27)</th>
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
                            <th class="kolom-sesudah">Uraian</th>
                            <th class="kolom-sesudah">Kode Risiko</th>
                            <th class="kolom-sesudah">Pemilik Risiko</th>
                            <th class="kolom-sesudah">Uraian</th>
                            <th class="kolom-sesudah">Sumber</th>
                            <th class="kolom-sesudah">Uraian</th>
                            <th class="kolom-sesudah">Pihak yang Terkena</th>
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
                            <!-- setelah evaluasi -->
                            <th class="kolom-sesudah">(17)</th>
                            <th class="kolom-sesudah">(18)</th>
                            <th class="kolom-sesudah">(19)</th>
                            <th class="kolom-sesudah">(20)</th>
                            <th class="kolom-sesudah">(21)</th>
                            <th class="kolom-sesudah">(22)</th>
                            <th class="kolom-sesudah">(23)</th>
                            <th class="kolom-sesudah">(24)</th>
                            <th class="kolom-sesudah">(25)</th>
                            <th class="kolom-sesudah">(26)</th>
                            <th class="kolom-sesudah">(27)</th>
                            <th class="kolom-sesudah">(28)</th>
                            <th class="kolom-sesudah">(29)</th>
                            <th class="kolom-sesudah">(30)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal crud program kegiatan sebelum-->
<div class="modal fade" id="editProgramKegiatanModal" tabindex="-1" role="dialog" aria-labelledby="editProgramKegiatanModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 60%; margin-top: 50px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TambahProgramKegiatanModalLabel">Tambah Program / Kegiatan</h5>
                <h5 class="modal-title" id="editProgramKegiatanModalLabel">Edit Program / Kegiatan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">
                <form id="formTambahProgramKegiatan">
                    <input type="hidden" value="" id="id_data">
                    <input type="hidden" value="" id="id_program_kegiatan">
                    <input type="hidden" value="" id="id_indikator">
                    <input type="hidden" value="" id="tipe_sebelum">
                    <div class="form-group">
                        <label for="nama_program_kegiatan">Nama Program / Kegiatan</label>
                        <input type="text" class="form-control" id="nama_program_kegiatan" name="nama_program_kegiatan" disabled required>
                    </div>
                    <div class="form-group">
                        <label for="indikator_kinerja">Indikator Kinerja</label>
                        <input type="text" class="form-control" id="indikator_kinerja" name="indikator_kinerja" disabled required>
                    </div>
                    <div class="form-group">
                        <label for="uraian_resiko">Uraian Resiko</label>
                        <input type="text" class="form-control" id="uraian_resiko" name="uraian_resiko" required>
                        <small class="text-muted">Risiko yang menghambat pencapaian Kegiatan/IKU</small>
                    </div>
                    <div class="form-group">
                        <label for="kode_resiko">Kode Resiko</label>
                        <input type="text" class="form-control" id="kode_resiko" name="kode_resiko" disabled required>
                    </div>
                    <div class="form-group">
                        <label for="pemilik_resiko">Pilih Pemilik Resiko</label>
                        <select id="pemilik_resiko" class="form-control">
                            <option value="" selected>Pilih Pemilik Resiko</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="uraian_sebab">Uraian Sebab</label>
                        <input type="text" class="form-control" id="uraian_sebab" name="uraian_sebab" required>
                    </div>
                    <div class="form-group">
                        <label for="sumber_sebab">Pilih Sumber Sebab</label>
                        <select id="sumber_sebab" class="form-control">
                            <option value="" selected>Pilih Sumber Sebab</option>
                        </select>
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
                        <label for="pihak_terkena">Pilih Pihak Dampak yang Terkena</label>
                        <select id="pihak_terkena" class="form-control">
                            <option value="" selected>Pilih Pihak Dampak yang Terkena</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="skala_dampak">Skala Dampak</label>
                        <input type="number" class="form-control" id="skala_dampak" name="skala_dampak" required>
                    </div>
                    <div class="form-group">
                        <label for="skala_kemungkinan">Skala Kemungkinan</label>
                        <input type="number" class="form-control" id="skala_kemungkinan" name="skala_kemungkinan" required>
                    </div>
                    <div class="form-group">
                        <label for="rencana_tindak_pengendalian">Rencana Tindak Pengendalian</label>
                        <textarea type="text" class="form-control" id="rencana_tindak_pengendalian" name="rencana_tindak_pengendalian" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_program_kegiatan(); return false">Simpan</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal verif program kegiatan sesudah-->
<div class="modal fade" id="VerifikasiModal" tabindex="-1" role="dialog" aria-labelledby="VerifikasiModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 80%; margin-top: 50px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="VerifikasiModalLabel">Verifikasi Program / Kegiatan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">
                <form id="formTambahProgramKegiatanSesudah">
                    <input type="hidden" value="" id="id_data_sesudah">
                    <input type="hidden" value="" id="id_sebelum">
                    <input type="hidden" value="" id="id_program_kegiatan_sesudah">
                    <input type="hidden" value="" id="id_indikator_sesudah">
                    <input type="hidden" value="" id="tipe_sesudah">
                    <div class="form-group">
                        <label for="nama_program_kegiatan_sesudah">Program / Kegiatan</label>
                        <input type="text" class="form-control" id="nama_program_kegiatan_sesudah" name="nama_program_kegiatan_sesudah" disabled required>
                    </div>
                    <div class="form-group">
                        <label for="indikator_kinerja_sesudah">Indikator Kinerja</label>
                        <input type="text" class="form-control" id="indikator_kinerja_sesudah" name="indikator_kinerja_sesudah" disabled required>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th class="text-center" width="50%">Usulan</th>
                                <th class="text-center" width="50%">Penetapan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="d-block">Controllable / Uncontrollable</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" type="radio" name="controllable_status_usulan" id="controllable_status_controllable_usulan" value="0" disabled>
                                            <label class="custom-control-label" for="controllable_status_controllable_usulan">Controllable</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" type="radio" name="controllable_status_usulan" id="controllable_status_uncontrollable_usulan" value="1" disabled>
                                            <label class="custom-control-label" for="controllable_status_uncontrollable_usulan">Uncontrollable</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="pemilik_resiko_usulan">Pemilik Resiko</label>
                                        <input type="text" class="form-control" id="pemilik_resiko_usulan" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sumber_sebab_usulan">Sumber Sebab</label>
                                        <input type="text" class="form-control" id="sumber_sebab_usulan" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="pihak_terkena_usulan">Pihak Dampak yang Terkena</label>
                                        <input type="text" class="form-control" id="pihak_terkena_usulan" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_resiko_usulan">Uraian Resiko</label>
                                        <input type="text" class="form-control" id="uraian_resiko_usulan" name="uraian_resiko_usulan" disabled required>
                                        <small class="text-muted">Risiko yang menghambat pencapaian Kegiatan/IKU</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="kode_resiko_usulan">Kode Resiko</label>
                                        <input type="text" class="form-control" id="kode_resiko_usulan" name="kode_resiko_usulan" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_sebab_usulan">Uraian Sebab</label>
                                        <input type="text" class="form-control" id="uraian_sebab_usulan" name="uraian_sebab_usulan" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_dampak_usulan">Uraian Dampak</label>
                                        <input type="text" class="form-control" id="uraian_dampak_usulan" name="uraian_dampak_usulan" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="skala_dampak_usulan">Skala Dampak</label>
                                        <input type="number" class="form-control" id="skala_dampak_usulan" name="skala_dampak_usulan" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="skala_kemungkinan_usulan">Skala Kemungkinan</label>
                                        <input type="number" class="form-control" id="skala_kemungkinan_usulan" name="skala_kemungkinan_usulan" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rencana_tindak_pengendalian_usulan">Rencana Tindak Pengendalian</label>
                                        <textarea type="text" class="form-control" id="rencana_tindak_pengendalian_usulan" name="rencana_tindak_pengendalian_usulan" disabled required></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label class="d-block">Controllable / Uncontrollable</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" type="radio" name="controllable_status_sesudah" id="controllable_status_controllable_sesudah" value="0">
                                            <label class="custom-control-label" for="controllable_status_controllable_sesudah">Controllable</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" type="radio" name="controllable_status_sesudah" id="controllable_status_uncontrollable_sesudah" value="1">
                                            <label class="custom-control-label" for="controllable_status_uncontrollable_sesudah">Uncontrollable</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="pemilik_resiko_sesudah">Pilih Pemilik Resiko</label>
                                        <select id="pemilik_resiko_sesudah" class="form-control">
                                            <option value="" selected>Pilih Pemilik Resiko</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sumber_sebab_sesudah">Pilih Sumber Sebab</label>
                                        <select id="sumber_sebab_sesudah" class="form-control">
                                            <option value="" selected>Pilih Sumber Sebab</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pihak_terkena_sesudah">Pilih Pihak Dampak yang Terkena</label>
                                        <select id="pihak_terkena_sesudah" class="form-control">
                                            <option value="" selected>Pilih Pihak Dampak yang Terkena</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_resiko_sesudah">Uraian Resiko</label>
                                        <input type="text" class="form-control" id="uraian_resiko_sesudah" name="uraian_resiko_sesudah" required>
                                        <small class="text-muted">Risiko yang menghambat pencapaian Kegiatan/IKU</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="kode_resiko_sesudah">Kode Resiko</label>
                                        <input type="text" class="form-control" id="kode_resiko_sesudah" name="kode_resiko_sesudah" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_sebab_sesudah">Uraian Sebab</label>
                                        <input type="text" class="form-control" id="uraian_sebab_sesudah" name="uraian_sebab_sesudah" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_dampak_sesudah">Uraian Dampak</label>
                                        <input type="text" class="form-control" id="uraian_dampak_sesudah" name="uraian_dampak_sesudah" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="skala_dampak_sesudah">Skala Dampak</label>
                                        <input type="number" class="form-control" id="skala_dampak_sesudah" name="skala_dampak_sesudah" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="skala_kemungkinan_sesudah">Skala Kemungkinan</label>
                                        <input type="number" class="form-control" id="skala_kemungkinan_sesudah" name="skala_kemungkinan_sesudah" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rencana_tindak_pengendalian_sesudah">Rencana Tindak Pengendalian</label>
                                        <textarea type="text" class="form-control" id="rencana_tindak_pengendalian_sesudah" name="rencana_tindak_pengendalian_sesudah" required></textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button id="button_copy_usulan" onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">
                                <i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_verif_program_kegiatan(); return false">Simpan</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal mapping -->
<div class="modal fade" id="MappingModal" tabindex="-1" role="dialog" aria-labelledby="MappingModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 40%; margin-top: 50px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="MappingModalLabel">Mapping Data Manajemen Risiko</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formMapping">
                    <input type="hidden" id="mapping_id" name="id">
                    <input type="hidden" id="mapping_id_program_kegiatan" name="id_program_kegiatan">
                    <input type="hidden" id="mapping_id_indikator" name="id_indikator">
                    <input type="hidden" id="mapping_tipe" name="tipe">
                    
                    <div class="form-group">
                        <label for="mapping_data">Pilih Data Tujuan</label>
                        <select id="mapping_data" class="form-control" required>
                            <option value="">Pilih Data</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_mapping_program_kegiatan(); return false">Simpan Mapping</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        get_table_program_kegiatan();
        run_download_excel('', '#aksi-wpsipd');
        jQuery(document).on('change', '#nama_program_kegiatan', function() {
            get_indikator_edit();
        });
        
        jQuery(document).on('change', '#get_program_kegiatan', function() {
            get_indikator_verifikasi();
        });
        
        jQuery('#editProgramKegiatanModal').on('hidden.bs.modal', function () {
            reset_form_tambah();
        });
        
        jQuery('#VerifikasiModal').on('hidden.bs.modal', function () {
            reset_form_verifikasi();
        });

        options_manrisk();
        options_verif_manrisk();
    });
    var dataHitungMundur = {
        'namaJadwal' : '<?php echo ucwords($data_jadwal->nama); ?>',
        'mulaiJadwal' : '<?php echo $data_jadwal->waktu_awal; ?>',
        'selesaiJadwal' : '<?php echo $data_jadwal->waktu_akhir; ?>',
        'thisTimeZone' : '<?php echo $timezone ?>'
    }

    penjadwalanHitungMundur(dataHitungMundur);

    function get_table_program_kegiatan() {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_table_program_kegiatan',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                'id_jadwal': <?php echo $id_jadwal; ?>,
                'tipe_renstra': 'program_kegiatan',
                'id_skpd': <?php echo $id_skpd; ?>
            },
            success: function(response) {
                jQuery('#wrap-loading').hide();
                console.log(response);
                if (response.status === 'success') {
                    jQuery('.table_manrisk_program_kegiatan tbody').html(response.data);

                    if (response.data_sesudah) {
                        jQuery('.kolom-sesudah').show();
                    } else {
                        jQuery('.kolom-sesudah').hide();
                    }
                    
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

    function tambah_program_kegiatan_manrisk(id_program_kegiatan, id_indikator, nama_program_kegiatan, indikator_teks, tipe, kode_bidang) {
        jQuery('#TambahProgramKegiatanModalLabel').show();
        jQuery('#editProgramKegiatanModalLabel').hide();
        let tahun = <?php echo $input['tahun_anggaran']; ?>;
        let kode_skpd = '<?php echo $kode_skpd; ?>';
        let kode_resiko = 'RSO-'+ tahun.toString().slice(-2)+'-'+kode_bidang+'-'+kode_skpd;
        reset_form_tambah(id_program_kegiatan, id_indikator, nama_program_kegiatan, indikator_teks, tipe, kode_resiko);
        
        jQuery('#editProgramKegiatanModal').modal('show');
    }

    function edit_program_kegiatan_manrisk(id, id_program_kegiatan, id_indikator, tipe, kode_bidang) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'edit_program_kegiatan_manrisk',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id: id ,
                id_jadwal: <?php echo $id_jadwal; ?>,
                id_program_kegiatan: id_program_kegiatan,
                id_indikator: id_indikator, 
                tipe: tipe 
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    let data = response.data;
                    let tahun = <?php echo $input['tahun_anggaran']; ?>;
                    let kode_skpd = '<?php echo $kode_skpd; ?>';

                    if (data.kode_resiko === '' || data.kode_resiko === null || data.kode_resiko === 'NULL') {
                        kode_resiko = 'RSO-'+ tahun.toString().slice(-2)+'-'+kode_bidang+'-'+kode_skpd;
                    } else {
                        kode_resiko = data.kode_resiko;
                    }

                    jQuery("#id_data").val(data.id);        
                    jQuery('#id_program_kegiatan').val(id_program_kegiatan);
                    jQuery('#id_indikator').val(id_indikator);
                    jQuery('#tipe_sebelum').val(tipe);
                    jQuery("#nama_program_kegiatan").val(data.nama_program_kegiatan);
                    jQuery("#indikator_kinerja").val(data.capaian_teks);
                    jQuery("#uraian_resiko").val(data.uraian_resiko);
                    jQuery("#kode_resiko").val(kode_resiko);
                    jQuery("#pemilik_resiko").val(data.pemilik_resiko);
                    jQuery("#uraian_sebab").val(data.uraian_sebab);
                    jQuery("#sumber_sebab").val(data.sumber_sebab);
                    jQuery(`input[name='controllable_status'][value='${data.controllable}']`).prop('checked', true);
                    jQuery("#uraian_dampak").val(data.uraian_dampak);
                    jQuery("#pihak_terkena").val(data.pihak_terkena);
                    jQuery("#skala_dampak").val(data.skala_dampak);
                    jQuery("#skala_kemungkinan").val(data.skala_kemungkinan);
                    jQuery("#rencana_tindak_pengendalian").val(data.rencana_tindak_pengendalian);

                    jQuery('#TambahProgramKegiatanModalLabel').hide();
                    jQuery('#editProgramKegiatanModalLabel').show();
                    jQuery('#editProgramKegiatanModal').modal('show');
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

    function submit_program_kegiatan() {
        let id = jQuery('#id_data').val();
        let id_program_kegiatan = jQuery("#id_program_kegiatan").val();
        let id_indikator = jQuery("#id_indikator").val();
        let tipe_sebelum = jQuery("#tipe_sebelum").val();
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
        let rencana_tindak_pengendalian = jQuery("#rencana_tindak_pengendalian").val();

        jQuery('#wrap-loading').show();

        jQuery.ajax({
            method: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'submit_program_kegiatan',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id: id, 
                id_program_kegiatan: id_program_kegiatan,
                id_indikator: id_indikator,
                tipe: tipe_sebelum,
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
                rencana_tindak_pengendalian: rencana_tindak_pengendalian
            },
            success: function(response) {
                jQuery('#wrap-loading').hide();
                alert(response.message);
                if (response.status === 'success') {
                    jQuery('#editProgramKegiatanModal').modal('hide');
                    get_table_program_kegiatan();
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }

    function hapus_program_kegiatan_manrisk(id, tipe) {
        if (tipe == 1){            
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                return;
            }
        } else{
            if (!confirm('Apakah Anda yakin ingin menghapus data ini? Data sebelum dan sesudah evaluasi akan terhapus')) {
                return;
            }            
        }
        
        var id_data = id;
        if (tipe == 1 && typeof id === 'string' && id.includes(',')) {
            id_data = id.split(',');
        }
        
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'hapus_program_kegiatan_manrisk',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                id: id_data
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    alert(response.message);
                    get_table_program_kegiatan();
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

    function verif_program_kegiatan_manrisk(id, id_sebelum, id_program_kegiatan, id_indikator, tipe_sesudah, kode_bidang) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'verif_program_kegiatan_manrisk',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id: id,
                id_sebelum: id_sebelum,
                id_program_kegiatan: id_program_kegiatan || 0,
                id_indikator: id_indikator || 0,
                id_jadwal: <?php echo $id_jadwal; ?>,
                tipe: tipe_sesudah
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    let data = response.data;
                    let data_sebelum = response.data_sebelum;

                    let tahun = <?php echo $input['tahun_anggaran']; ?>;
                    let kode_skpd = '<?php echo $kode_skpd; ?>';
                    let kode_resiko = 'RSO-'+ tahun.toString().slice(-2)+'-'+kode_bidang+'-'+kode_skpd;

                    window.value = {
                        pemilik_resiko_id: data_sebelum.pemilik_resiko || '',
                        sumber_sebab_id: data_sebelum.sumber_sebab || '',
                        pihak_terkena_id: data_sebelum.pihak_terkena || ''
                    };

                    let pemilik_resiko_text = get_nama_by_id(response.data_master.pemilik_resiko, data_sebelum.pemilik_resiko);
                    let sumber_sebab_text   = get_nama_by_id(response.data_master.sumber_sebab, data_sebelum.sumber_sebab);
                    let pihak_terkena_text  = get_nama_by_id(response.data_master.pihak_terdampak, data_sebelum.pihak_terkena);

                    if (data_sebelum.controllable == 0 || data_sebelum.controllable == 1) {
                        jQuery(`input[name='controllable_status_usulan'][value='${data_sebelum.controllable}']`).prop('checked', true);
                    } else {
                        jQuery("input[name='controllable_status_usulan']").prop('checked', false);
                    }

                    jQuery("#uraian_resiko_usulan").val(data_sebelum.uraian_resiko || '');
                    jQuery("#kode_resiko_usulan").val(kode_resiko || '');
                    jQuery("#pemilik_resiko_usulan").val(pemilik_resiko_text || '');
                    jQuery("#uraian_sebab_usulan").val(data_sebelum.uraian_sebab || '');
                    jQuery("#sumber_sebab_usulan").val(sumber_sebab_text || '');
                    jQuery("#uraian_dampak_usulan").val(data_sebelum.uraian_dampak || '');
                    jQuery("#pihak_terkena_usulan").val(pihak_terkena_text || '');
                    jQuery("#skala_dampak_usulan").val(data_sebelum.skala_dampak || '');
                    jQuery("#skala_kemungkinan_usulan").val(data_sebelum.skala_kemungkinan || '');
                    jQuery("#rencana_tindak_pengendalian_usulan").val(data_sebelum.rencana_tindak_pengendalian || '');

                    jQuery('#id_program_kegiatan_sesudah').val(id_program_kegiatan);
                    jQuery('#id_indikator_sesudah').val(id_indikator);
                    jQuery('#tipe_sesudah').val(tipe_sesudah);
                    
                    if (data && Object.keys(data).length > 0) {
                        jQuery("#id_data_sesudah").val(data.id);
                        jQuery('#nama_program_kegiatan_sesudah').val(data.nama_program_kegiatan);
                        jQuery('#indikator_kinerja_sesudah').val(data_sebelum.capaian_teks);
                        
                        if (data.controllable == 0 || data.controllable == 1) {
                            jQuery(`input[name='controllable_status_sesudah'][value='${data.controllable}']`).prop('checked', true);
                        } else {
                            jQuery("input[name='controllable_status_sesudah']").prop('checked', false);
                        }
                        
                        jQuery("#uraian_resiko_sesudah").val(data.uraian_resiko);
                        jQuery("#kode_resiko_sesudah").val(kode_resiko);
                        jQuery("#pemilik_resiko_sesudah").val(data.pemilik_resiko);
                        jQuery("#uraian_sebab_sesudah").val(data.uraian_sebab);
                        jQuery("#sumber_sebab_sesudah").val(data.sumber_sebab);
                        jQuery("#uraian_dampak_sesudah").val(data.uraian_dampak);
                        jQuery("#pihak_terkena_sesudah").val(data.pihak_terkena);
                        jQuery("#skala_dampak_sesudah").val(data.skala_dampak);
                        jQuery("#skala_kemungkinan_sesudah").val(data.skala_kemungkinan);
                        jQuery("#rencana_tindak_pengendalian_sesudah").val(data.rencana_tindak_pengendalian);
                    } else {

                        reset_form_verifikasi(kode_resiko);
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

    function get_nama_by_id(list, id) {
        if (!list || !id) return '';
        let item = list.find(function(el) {
            return el.id == id;
        });
        return item ? item.nama : '';
    }

    function copy_usulan(button) {
        try {
            jQuery(button).prop('disabled', true);
            
            let uraian_resiko = jQuery("#uraian_resiko_usulan").val();
            let kode_resiko = jQuery("#kode_resiko_usulan").val();
            let uraian_sebab = jQuery("#uraian_sebab_usulan").val();
            let uraian_dampak = jQuery("#uraian_dampak_usulan").val();
            let skala_dampak = jQuery("#skala_dampak_usulan").val();
            let skala_kemungkinan = jQuery("#skala_kemungkinan_usulan").val();
            let rencana_tindak_pengendalian = jQuery("#rencana_tindak_pengendalian_usulan").val();
            let controllable_status = jQuery("input[name='controllable_status_usulan']:checked").val();
            
            let pemilik_resiko_id = window.value ? window.value.pemilik_resiko_id : '';
            let sumber_sebab_id = window.value ? window.value.sumber_sebab_id : '';
            let pihak_terkena_id = window.value ? window.value.pihak_terkena_id : '';
            
            jQuery("#uraian_resiko_sesudah").val(uraian_resiko);
            jQuery("#kode_resiko_sesudah").val(kode_resiko);
            jQuery("#pemilik_resiko_sesudah").val(pemilik_resiko_id);
            jQuery("#uraian_sebab_sesudah").val(uraian_sebab);
            jQuery("#sumber_sebab_sesudah").val(sumber_sebab_id);
            jQuery("#uraian_dampak_sesudah").val(uraian_dampak);
            jQuery("#pihak_terkena_sesudah").val(pihak_terkena_id);
            jQuery("#skala_dampak_sesudah").val(skala_dampak);
            jQuery("#skala_kemungkinan_sesudah").val(skala_kemungkinan);
            jQuery("#rencana_tindak_pengendalian_sesudah").val(rencana_tindak_pengendalian);
            
            jQuery("#pemilik_resiko_sesudah").trigger('change');
            jQuery("#sumber_sebab_sesudah").trigger('change');
            jQuery("#pihak_terkena_sesudah").trigger('change');
            
            jQuery(button).html('<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Data Berhasil Disalin!');
            jQuery(button).removeClass('btn-danger').addClass('btn-success');

            if (controllable_status) {
                jQuery(`input[name='controllable_status_sesudah'][value='${controllable_status}']`).prop('checked', true);
            }
            
            let penetapan = [
                "#pemilik_resiko_sesudah","#sumber_sebab_sesudah","#pihak_terkena_sesudah",
                "#controllable_status_sesudah","#uraian_resiko_sesudah", "#kode_resiko_sesudah",
                "#uraian_sebab_sesudah", "#uraian_dampak_sesudah",
                "#skala_dampak_sesudah", "#skala_kemungkinan_sesudah",
                "#rencana_tindak_pengendalian_sesudah"
            ];
            
            penetapan.forEach(function(selector) {
                jQuery(selector).addClass('field-updated');
            });
            
            setTimeout(function() {
                penetapan.forEach(function(selector) {
                    jQuery(selector).removeClass('field-updated');
                });
                jQuery(button).html('<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan');
                jQuery(button).removeClass('btn-success').addClass('btn-danger');
                jQuery(button).prop('disabled', false);
            }, 3000);
            
        } catch (error) {
            console.error('Error saat menyalin data:', error);
            alert('Terjadi kesalahan saat menyalin data. Silakan coba lagi.');
            
            jQuery(button).prop('disabled', false);
        }
    }

    function reset_form_verifikasi(kode_resiko) {
        jQuery("#id_data_sesudah").val('');
        
        jQuery('#nama_program_kegiatan_sesudah').val('');
        jQuery('#indikator_kinerja_sesudah').val('');
        
        jQuery("input[name='controllable_status_sesudah']").prop('checked', false);
        
        jQuery("#uraian_resiko_sesudah").val('');
        jQuery("#kode_resiko_sesudah").val(kode_resiko);
        jQuery("#pemilik_resiko_sesudah").val('');
        jQuery("#uraian_sebab_sesudah").val('');
        jQuery("#sumber_sebab_sesudah").val('');
        jQuery("#uraian_dampak_sesudah").val('');
        jQuery("#pihak_terkena_sesudah").val('');
        jQuery("#skala_dampak_sesudah").val('');
        jQuery("#skala_kemungkinan_sesudah").val('');
        jQuery("#rencana_tindak_pengendalian_sesudah").val('');
    }

    function reset_form_tambah(id_program_kegiatan, id_indikator, nama_program_kegiatan, indikator_teks, tipe, kode_resiko) {
        jQuery('#id_program_kegiatan').val(id_program_kegiatan);
        jQuery('#id_indikator').val(id_indikator);
        jQuery('#tipe_sebelum').val(tipe);
        jQuery('#nama_program_kegiatan').val(nama_program_kegiatan);
        jQuery('#indikator_kinerja').val(indikator_teks);
        
        jQuery("input[name='controllable_status']").prop('checked', false);
        jQuery("#id_data").val('');
        jQuery("#uraian_resiko").val('');
        jQuery("#kode_resiko").val(kode_resiko);
        jQuery("#pemilik_resiko").val('');
        jQuery("#uraian_sebab").val('');
        jQuery("#sumber_sebab").val('');
        jQuery("#controllable").val('');
        jQuery("#uncontrollable").val('');
        jQuery("#uraian_dampak").val('');
        jQuery("#pihak_terkena").val('');
        jQuery("#skala_dampak").val('');
        jQuery("#skala_kemungkinan").val('');
        jQuery("#rencana_tindak_pengendalian").val('');
    }

    function submit_verif_program_kegiatan() {
        let id = jQuery('#id_data_sesudah').val();
        let id_sebelum = jQuery('#id_sebelum').val();
        let id_program_kegiatan = jQuery("#id_program_kegiatan_sesudah").val();
        let id_indikator = jQuery("#id_indikator_sesudah").val();
        let tipe_sesudah = jQuery("#tipe_sesudah").val();
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
        let rencana_tindak_pengendalian = jQuery("#rencana_tindak_pengendalian_sesudah").val();

        jQuery('#wrap-loading').show();

        jQuery.ajax({
            method: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'submit_verif_program_kegiatan',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id: id,  
                id_sebelum: id_sebelum, 
                id_program_kegiatan: id_program_kegiatan,
                id_indikator: id_indikator,
                tipe: tipe_sesudah,
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
                rencana_tindak_pengendalian: rencana_tindak_pengendalian
            },
            success: function(response) {
                jQuery('#wrap-loading').hide();
                alert(response.message);
                if (response.status === 'success') {
                    jQuery('#VerifikasiModal').modal('hide');
                    get_table_program_kegiatan();
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }

    function mapping_program_kegiatan_manrisk(all_id, id_program_kegiatan, id_indikator, tipe) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'mapping_program_kegiatan_manrisk',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                all_id: JSON.stringify(all_id),
                id_program_kegiatan: id_program_kegiatan,
                id_indikator: id_indikator,
                tipe: tipe 
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    jQuery("#mapping_id").val(JSON.stringify(all_id));
                    jQuery("#mapping_id_program_kegiatan").val(id_program_kegiatan);
                    jQuery("#mapping_id_indikator").val(id_indikator);
                    jQuery("#mapping_tipe").val(tipe);

                    var select = jQuery('#mapping_data');
                    select.empty();

                    if (response.data && response.data.length > 0) {
                        jQuery.each(response.data, function(i, item) {
                            var option = jQuery('<option></option>')
                                .val(item.id)
                                .text(item.capaian_teks + ' (' + item.target_capaian_teks + ')');
                            select.append(option);
                        });
                        jQuery('#MappingModal').modal('show');
                    } else {
                        if (confirm("Data tujuan tidak tersedia. Apakah Anda ingin menghapus data ini?")) {
                            hapus_program_kegiatan_manrisk(all_id[0], 1);
                        }
                    }
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

    function submit_mapping_program_kegiatan() {
        var all_id = jQuery('#mapping_id').val();
        var id_program_kegiatan = jQuery('#mapping_id_program_kegiatan').val();
        var id_indikator = jQuery('#mapping_id_indikator').val();
        var tipe = jQuery('#mapping_tipe').val();
        var id_tujuan = jQuery('#mapping_data').val();
        
        if (!id_tujuan) {
            alert('Silakan pilih data terlebih dahulu!');
            return false;
        }
        
        jQuery('#wrap-loading').show();

        jQuery.ajax({
            method: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'submit_mapping_program_kegiatan',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                all_id: all_id,
                id_program_kegiatan: id_program_kegiatan,
                id_indikator: id_indikator,
                tipe: tipe,
                id_tujuan: id_tujuan
            },
            success: function(response) {
                jQuery('#wrap-loading').hide();
                alert(response.message);
                if (response.status === 'success') {
                    jQuery('#MappingModal').modal('hide');
                    get_table_program_kegiatan();
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }

    function options_manrisk() {
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'options_manrisk',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    let pemilik_resiko = '<option value="">Pilih Pemilik Resiko</option>';
                    response.data.pemilik_resiko.forEach(function(item) {
                        pemilik_resiko += `<option value="${item.id}">${item.nama}</option>`;
                    });
                    jQuery('#pemilik_resiko').html(pemilik_resiko);

                    let sumber_sebab = '<option value="">Pilih Sumber Sebab</option>';
                    response.data.sumber_sebab.forEach(function(item) {
                        sumber_sebab += `<option value="${item.id}">${item.nama}</option>`;
                    });
                    jQuery('#sumber_sebab').html(sumber_sebab);

                    let pihak_terdampak = '<option value="">Pilih Pihak Dampak yang Terkena</option>';
                    response.data.pihak_terdampak.forEach(function(item) {
                        let value = item.id;
                        pihak_terdampak += `<option value="${value}">${item.nama}</option>`;
                    });
                    jQuery('#pihak_terkena').html(pihak_terdampak);
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan!');
            }
        });
    }
    function options_verif_manrisk() {
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'options_manrisk',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    let pemilik_resiko = '<option value="">Pilih Pemilik Resiko</option>';
                    response.data.pemilik_resiko.forEach(function(item) {
                        pemilik_resiko += `<option value="${item.id}">${item.nama}</option>`;
                    });
                    jQuery('#pemilik_resiko_sesudah').html(pemilik_resiko);

                    let sumber_sebab = '<option value="">Pilih Sumber Sebab</option>';
                    response.data.sumber_sebab.forEach(function(item) {
                        sumber_sebab += `<option value="${item.id}">${item.nama}</option>`;
                    });
                    jQuery('#sumber_sebab_sesudah').html(sumber_sebab);

                    let pihak_terdampak = '<option value="">Pilih Pihak Dampak yang Terkena</option>';
                    response.data.pihak_terdampak.forEach(function(item) {
                        pihak_terdampak += `<option value="${item.id}">${item.nama}</option>`;
                    });
                    jQuery('#pihak_terkena_sesudah').html(pihak_terdampak);
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan!');
            }
        });
    }
</script>