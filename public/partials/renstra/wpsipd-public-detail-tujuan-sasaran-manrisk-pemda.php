<?php
global $wpdb;

if (!defined('WPINC')) {
    die;
}

$get_jadwal = $wpdb->get_row(
    $wpdb->prepare('
        SELECT 
            id_jadwal_lokal
        FROM data_jadwal_lokal
        WHERE id_tipe = %d
          AND tahun_anggaran = %d
    ', 20, $_GET['tahun_anggaran']),
    ARRAY_A
);
$data_jadwal = $this->get_data_jadwal_by_id_jadwal_lokal($get_jadwal);
if (empty($data_jadwal)) {
    die('<div class="alert alert-danger">Jadwal kosong!</div>');
}
$timezone = get_option('timezone_string');

$nama_pemda = get_option('_crb_daerah');
if (empty($nama_pemda) || $nama_pemda == 'false') {
    $nama_pemda = '';
}

$get_data_sesudah = $wpdb->get_results($wpdb->prepare("
    SELECT 
        * 
    FROM data_tujuan_sasaran_manrisk_sesudah
    WHERE tahun_anggaran = %d
      AND active = 1
", $_GET['tahun_anggaran']), ARRAY_A);

$get_jadwal_rpjmd = $wpdb->get_row(
    $wpdb->prepare('
        SELECT 
            *
        FROM data_jadwal_lokal
        WHERE id_jadwal_lokal = %d
    ', $_GET['id_jadwal']),
    ARRAY_A
);

if ($get_jadwal_rpjmd) {
    $tahun_akhir_anggaran = $get_jadwal_rpjmd['tahun_anggaran'] + $get_jadwal_rpjmd['lama_pelaksanaan'] - 1;
    $nama_jadwal = $get_jadwal_rpjmd['nama'] . ' ' . $get_jadwal_rpjmd['tahun_anggaran'] . '-' . $tahun_akhir_anggaran;
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
    <div class="cetak" style="padding: 5px; overflow: auto;">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h1 class="text-center table-title" style="padding-top: 80px">
                Manajemen Risiko Tujuan / Sasaran <br>Pemerintah Daerah<br>Tahun <?php echo $_GET['tahun_anggaran']; ?> ( <?php echo $nama_jadwal; ?> )
            </h1>
            <div id='aksi-wpsipd'></div>
            <div class="wrap-table">
                <table id="cetak" title="Manajemen Risiko Tujuan / Sasaran Pemda" class="table_manrisk_tujuan_sasaran_pemda table-bordered" cellpadding="2" cellspacing="0" contenteditable="false">
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
                            <th rowspan="2">Tujuan Strategis/ Sasaran Strategis Pemda</th>
                            <th rowspan="2">Indikator Kinerja</th>
                            <th colspan="3">Risiko</th>
                            <th colspan="2">Sebab</th>
                            <th rowspan="2">Controllable / Uncontrollable</th>
                            <th colspan="2">Dampak</th>
                            <th rowspan="2">Skala Dampak</th>
                            <th rowspan="2">Skala Kemungkinan</th>
                            <th rowspan="2">Nilai Risiko (12x13)</th>
                            <!-- setelah evaluasi -->
                            <th rowspan="2" class="kolom-sesudah">Tujuan Strategis/ Sasaran Strategis Pemda</th>
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

<!-- Modal crud tujuan sasaran sebelum PEMDA -->
<div class="modal fade" id="editTujuanSasaranPemdaModal" tabindex="-1" role="dialog" aria-labelledby="editTujuanSasaranPemdaModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 60%; margin-top: 50px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TambahTujuanSasaranPemdaModalLabel">Tambah Tujuan / Sasaran Pemda</h5>
                <h5 class="modal-title" id="editTujuanSasaranPemdaModalLabel">Edit Tujuan / Sasaran Pemda</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambahTujuanSasaranPemda">
                    <input type="hidden" value="" id="id_data_pemda">
                    <input type="hidden" value="" id="id_tujuan_sasaran_pemda">
                    <input type="hidden" value="" id="id_indikator_pemda">
                    <input type="hidden" value="" id="tipe_sebelum_pemda">
                    <input type="hidden" value="" id="id_jadwal_pemda">
                    <div class="form-group">
                        <label for="nama_tujuan_sasaran_pemda">Nama Tujuan / Sasaran</label>
                        <input type="text" class="form-control" id="nama_tujuan_sasaran_pemda" name="nama_tujuan_sasaran_pemda" disabled required>
                    </div>
                    <div class="form-group">
                        <label for="indikator_kinerja_pemda">Indikator Kinerja</label>
                        <input type="text" class="form-control" id="indikator_kinerja_pemda" name="indikator_kinerja_pemda" disabled required>
                    </div>
                    <div class="form-group">
                        <label for="uraian_resiko_pemda">Uraian Risiko</label>
                        <input type="text" class="form-control" id="uraian_resiko_pemda" name="uraian_resiko_pemda" required>
                        <small class="text-muted">Risiko yang menghambat pencapaian Sasaran/IKU</small>
                    </div>
                    <div class="form-group">
                        <label for="kode_resiko_pemda">Kode Risiko</label>
                        <input type="text" class="form-control" id="kode_resiko_pemda" name="kode_resiko_pemda" disabled required>
                    </div>
                    <div class="form-group">
                        <label for="pemilik_resiko_pemda">Pilih Pemilik Risiko</label>
                        <select id="pemilik_resiko_pemda" class="form-control">
                            <option value="" selected>Pilih Pemilik Risiko</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="uraian_sebab_pemda">Uraian Sebab</label>
                        <input type="text" class="form-control" id="uraian_sebab_pemda" name="uraian_sebab_pemda" required>
                    </div>
                    <div class="form-group">
                        <label for="sumber_sebab_pemda">Pilih Sumber Sebab</label>
                        <select id="sumber_sebab_pemda" class="form-control">
                            <option value="" selected>Pilih Sumber Sebab</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="d-block">Controllable / Uncontrollable</label>
                        <tr>
                            <td>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="controllable_status_pemda" id="controllable_status_controllable_pemda" value="0">
                                    <label class="custom-control-label" for="controllable_status_controllable_pemda">Controllable</label>
                                </div>
                            </td>
                            <td>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="controllable_status_pemda" id="controllable_status_uncontrollable_pemda" value="1">
                                    <label class="custom-control-label" for="controllable_status_uncontrollable_pemda">Uncontrollable</label>
                                </div>
                            </td>
                        </tr>
                    </div>
                    <div class="form-group">
                        <label for="uraian_dampak_pemda">Uraian Dampak</label>
                        <input type="text" class="form-control" id="uraian_dampak_pemda" name="uraian_dampak_pemda" required>
                    </div>
                    <div class="form-group">
                        <label for="pihak_terkena_pemda">Pilih Pihak Dampak yang Terkena</label>
                        <select id="pihak_terkena_pemda" class="form-control">
                            <option value="" selected>Pilih Pihak Dampak yang Terkena</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="skala_dampak_pemda">Skala Dampak</label>
                        <input type="number" class="form-control" id="skala_dampak_pemda" name="skala_dampak_pemda" min="0" max="5" required>
                    </div>
                    <div class="form-group">
                        <label for="skala_kemungkinan_pemda">Skala Kemungkinan</label>
                        <input type="number" class="form-control" id="skala_kemungkinan_pemda" name="skala_kemungkinan_pemda" min="0" max="5" required>
                    </div>
                    <div class="form-group">
                        <label for="rencana_tindak_pengendalian_pemda">Rencana Tindak Pengendalian</label>
                        <textarea type="text" class="form-control" id="rencana_tindak_pengendalian_pemda" name="rencana_tindak_pengendalian_pemda" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_tujuan_sasaran_pemda(); return false">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal verif tujuan sasaran sesudah PEMDA -->
<div class="modal fade" id="VerifikasiPemdaModal" tabindex="-1" role="dialog" aria-labelledby="VerifikasiPemdaModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 80%; margin-top: 50px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="VerifikasiPemdaModalLabel">Verifikasi Tujuan / Sasaran Pemda</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambahTujuanSasaranSesudahPemda">
                    <input type="hidden" value="" id="id_data_sesudah_pemda">
                    <input type="hidden" value="" id="id_sebelum_pemda">
                    <input type="hidden" value="" id="id_tujuan_sasaran_sesudah_pemda">
                    <input type="hidden" value="" id="id_indikator_sesudah_pemda">
                    <input type="hidden" value="" id="tipe_sesudah_pemda">
                    <input type="hidden" value="" id="id_jadwal_sesudah_pemda">
                    <div class="form-group">
                        <label for="nama_tujuan_sasaran_sesudah_pemda">Tujuan / Sasaran</label>
                        <input type="text" class="form-control" id="nama_tujuan_sasaran_sesudah_pemda" name="nama_tujuan_sasaran_sesudah_pemda" disabled required>
                    </div>
                    <div class="form-group">
                        <label for="indikator_kinerja_sesudah_pemda">Indikator Kinerja</label>
                        <input type="text" class="form-control" id="indikator_kinerja_sesudah_pemda" name="indikator_kinerja_sesudah_pemda" disabled required>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th class="text-center" width="50%">Sebelum Evaluasi</th>
                                <th class="text-center" width="50%">Sesudah Evaluasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="d-block">Controllable / Uncontrollable</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" type="radio" name="controllable_status_usulan_pemda" id="controllable_status_controllable_usulan_pemda" value="0" disabled>
                                            <label class="custom-control-label" for="controllable_status_controllable_usulan_pemda">Controllable</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" type="radio" name="controllable_status_usulan_pemda" id="controllable_status_uncontrollable_usulan_pemda" value="1" disabled>
                                            <label class="custom-control-label" for="controllable_status_uncontrollable_usulan_pemda">Uncontrollable</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="pemilik_resiko_usulan_pemda">Pemilik Risiko</label>
                                        <input type="text" class="form-control" id="pemilik_resiko_usulan_pemda" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sumber_sebab_usulan_pemda">Sumber Sebab</label>
                                        <input type="text" class="form-control" id="sumber_sebab_usulan_pemda" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="pihak_terkena_usulan_pemda">Pihak Dampak yang Terkena</label>
                                        <input type="text" class="form-control" id="pihak_terkena_usulan_pemda" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_resiko_usulan_pemda">Uraian Risiko</label>
                                        <input type="text" class="form-control" id="uraian_resiko_usulan_pemda" name="uraian_resiko_usulan_pemda" disabled required>
                                        <small class="text-muted">Risiko yang menghambat pencapaian Sasaran/IKU</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="kode_resiko_usulan_pemda">Kode Risiko</label>
                                        <input type="text" class="form-control" id="kode_resiko_usulan_pemda" name="kode_resiko_usulan_pemda" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_sebab_usulan_pemda">Uraian Sebab</label>
                                        <input type="text" class="form-control" id="uraian_sebab_usulan_pemda" name="uraian_sebab_usulan_pemda" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_dampak_usulan_pemda">Uraian Dampak</label>
                                        <input type="text" class="form-control" id="uraian_dampak_usulan_pemda" name="uraian_dampak_usulan_pemda" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="skala_dampak_usulan_pemda">Skala Dampak</label>
                                        <input type="number" class="form-control" id="skala_dampak_usulan_pemda" name="skala_dampak_usulan_pemda" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="skala_kemungkinan_usulan_pemda">Skala Kemungkinan</label>
                                        <input type="number" class="form-control" id="skala_kemungkinan_usulan_pemda" name="skala_kemungkinan_usulan_pemda" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rencana_tindak_pengendalian_usulan_pemda">Rencana Tindak Pengendalian</label>
                                        <textarea type="text" class="form-control" id="rencana_tindak_pengendalian_usulan_pemda" name="rencana_tindak_pengendalian_usulan_pemda" disabled required></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label class="d-block">Controllable / Uncontrollable</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" type="radio" name="controllable_status_sesudah_pemda" id="controllable_status_controllable_sesudah_pemda" value="0">
                                            <label class="custom-control-label" for="controllable_status_controllable_sesudah_pemda">Controllable</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input class="custom-control-input" type="radio" name="controllable_status_sesudah_pemda" id="controllable_status_uncontrollable_sesudah_pemda" value="1">
                                            <label class="custom-control-label" for="controllable_status_uncontrollable_sesudah_pemda">Uncontrollable</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="pemilik_resiko_sesudah_pemda">Pilih Pemilik Risiko</label>
                                        <select id="pemilik_resiko_sesudah_pemda" class="form-control">
                                            <option value="" selected>Pilih Pemilik Risiko</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sumber_sebab_sesudah_pemda">Pilih Sumber Sebab</label>
                                        <select id="sumber_sebab_sesudah_pemda" class="form-control">
                                            <option value="" selected>Pilih Sumber Sebab</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pihak_terkena_sesudah_pemda">Pilih Pihak Dampak yang Terkena</label>
                                        <select id="pihak_terkena_sesudah_pemda" class="form-control">
                                            <option value="" selected>Pilih Pihak Dampak yang Terkena</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_resiko_sesudah_pemda">Uraian Risiko</label>
                                        <input type="text" class="form-control" id="uraian_resiko_sesudah_pemda" name="uraian_resiko_sesudah_pemda" required>
                                        <small class="text-muted">Risiko yang menghambat pencapaian Sasaran/IKU</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="kode_resiko_sesudah_pemda">Kode Risiko</label>
                                        <input type="text" class="form-control" id="kode_resiko_sesudah_pemda" name="kode_resiko_sesudah_pemda" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_sebab_sesudah_pemda">Uraian Sebab</label>
                                        <input type="text" class="form-control" id="uraian_sebab_sesudah_pemda" name="uraian_sebab_sesudah_pemda" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="uraian_dampak_sesudah_pemda">Uraian Dampak</label>
                                        <input type="text" class="form-control" id="uraian_dampak_sesudah_pemda" name="uraian_dampak_sesudah_pemda" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="skala_dampak_sesudah_pemda">Skala Dampak</label>
                                        <input type="number" class="form-control" id="skala_dampak_sesudah_pemda" name="skala_dampak_sesudah_pemda" min="0" max="5" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="skala_kemungkinan_sesudah_pemda">Skala Kemungkinan</label>
                                        <input type="number" class="form-control" id="skala_kemungkinan_sesudah_pemda" name="skala_kemungkinan_sesudah_pemda" min="0" max="5" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rencana_tindak_pengendalian_sesudah_pemda">Rencana Tindak Pengendalian</label>
                                        <textarea type="text" class="form-control" id="rencana_tindak_pengendalian_sesudah_pemda" name="rencana_tindak_pengendalian_sesudah_pemda" required></textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button id="button_copy_usulan_pemda" onclick="copy_usulan_pemda(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">
                                <i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Sebelum Evaluasi ke Sesudah Evaluasi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_verif_tujuan_sasaran_pemda(); return false">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        get_table_tujuan_sasaran_pemda();
        run_download_excel('', '#aksi-wpsipd');
        jQuery(document).on('change', '#nama_tujuan_sasaran', function() {
            get_indikator_edit();
        });
        
        jQuery(document).on('change', '#get_tujuan_sasaran', function() {
            get_indikator_verifikasi();
        });
        
        jQuery('#editTujuanSasaranModal').on('hidden.bs.modal', function () {
            reset_form_tambah_pemda();
        });
        
        jQuery('#VerifikasiModal').on('hidden.bs.modal', function () {
            reset_form_verifikasi_pemda();
        });

        options_manrisk_pemda();
        options_verif_manrisk_pemda();
    });
    var dataHitungMundur = {
        'namaJadwal' : '<?php echo ucwords($data_jadwal->nama); ?>',
        'mulaiJadwal' : '<?php echo $data_jadwal->waktu_awal; ?>',
        'selesaiJadwal' : '<?php echo $data_jadwal->waktu_akhir; ?>',
        'thisTimeZone' : '<?php echo $timezone ?>'
    }

    penjadwalanHitungMundur(dataHitungMundur);

    function get_table_tujuan_sasaran_pemda() {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_table_tujuan_sasaran_pemda',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'tahun_anggaran': <?php echo $_GET['tahun_anggaran']; ?>,
                'id_jadwal': <?php echo $_GET['id_jadwal']; ?>,
                'tipe_rpjmd': 'tujuan_sasaran_pemda'
            },
            success: function(response) {
                jQuery('#wrap-loading').hide();
                console.log(response);
                if (response.status === 'success') {
                    jQuery('.table_manrisk_tujuan_sasaran_pemda tbody').html(response.data);

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

    function tambah_tujuan_sasaran_manrisk_pemda(id_tujuan_sasaran, id_indikator, nama_tujuan_sasaran, indikator_teks, tipe, id_jadwal) {
        jQuery('#TambahTujuanSasaranPemdaModalLabel').show();
        jQuery('#editTujuanSasaranPemdaModalLabel').hide();
        
        let tahun = <?php echo $_GET['tahun_anggaran']; ?>;
        let kode_resiko = 'RSP-'+ tahun.toString().slice(-2);

        reset_form_tambah_pemda(id_tujuan_sasaran, id_indikator, nama_tujuan_sasaran, indikator_teks, tipe, kode_resiko, id_jadwal);
        
        jQuery('#editTujuanSasaranPemdaModal').modal('show');
    }

    function edit_tujuan_sasaran_manrisk_pemda(id, id_tujuan_sasaran, id_indikator, tipe, id_jadwal) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'edit_tujuan_sasaran_manrisk_pemda',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $_GET['tahun_anggaran']; ?>,
                id: id,
                id_tujuan_sasaran: id_tujuan_sasaran,
                id_indikator: id_indikator,
                tipe: tipe,
                id_jadwal: id_jadwal
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    let data = response.data;
                    let tahun = <?php echo $_GET['tahun_anggaran']; ?>;

                    if (data.kode_resiko === '' || data.kode_resiko === null || data.kode_resiko === 'NULL') {
                        kode_resiko = 'RSP-'+ tahun.toString().slice(-2);
                    } else {
                        kode_resiko = data.kode_resiko;
                    }

                    jQuery("#id_data_pemda").val(data.id);
                    jQuery('#id_tujuan_sasaran_pemda').val(id_tujuan_sasaran);
                    jQuery('#id_indikator_pemda').val(id_indikator);
                    jQuery('#tipe_sebelum_pemda').val(tipe);
                    jQuery('#id_jadwal_pemda').val(id_jadwal);
                    jQuery("#nama_tujuan_sasaran_pemda").val(data.nama_tujuan_sasaran);
                    jQuery("#indikator_kinerja_pemda").val(data.indikator_teks);
                    jQuery("#uraian_resiko_pemda").val(data.uraian_resiko);
                    jQuery("#kode_resiko_pemda").val(kode_resiko);
                    jQuery("#pemilik_resiko_pemda").val(data.pemilik_resiko);
                    jQuery("#uraian_sebab_pemda").val(data.uraian_sebab);
                    jQuery("#sumber_sebab_pemda").val(data.sumber_sebab);
                    jQuery(`input[name='controllable_status_pemda'][value='${data.controllable}']`).prop('checked', true);
                    jQuery("#uraian_dampak_pemda").val(data.uraian_dampak);
                    jQuery("#pihak_terkena_pemda").val(data.pihak_terkena);
                    jQuery("#skala_dampak_pemda").val(data.skala_dampak);
                    jQuery("#skala_kemungkinan_pemda").val(data.skala_kemungkinan);
                    jQuery("#rencana_tindak_pengendalian_pemda").val(data.rencana_tindak_pengendalian);

                    jQuery('#TambahTujuanSasaranPemdaModalLabel').hide();
                    jQuery('#editTujuanSasaranPemdaModalLabel').show();
                    jQuery('#editTujuanSasaranPemdaModal').modal('show');
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

    function submit_tujuan_sasaran_pemda() {
        let id = jQuery('#id_data_pemda').val();
        let id_tujuan_sasaran = jQuery("#id_tujuan_sasaran_pemda").val();
        let id_indikator = jQuery("#id_indikator_pemda").val();
        let tipe_sebelum = jQuery("#tipe_sebelum_pemda").val();
        let id_jadwal = jQuery("#id_jadwal_pemda").val();
        let uraian_resiko = jQuery("#uraian_resiko_pemda").val();
        let kode_resiko = jQuery("#kode_resiko_pemda").val();
        let pemilik_resiko = jQuery("#pemilik_resiko_pemda").val();
        let uraian_sebab = jQuery("#uraian_sebab_pemda").val();
        let sumber_sebab = jQuery("#sumber_sebab_pemda").val();
        let controllable_status = jQuery("input[name='controllable_status_pemda']:checked").val();
        let uraian_dampak = jQuery("#uraian_dampak_pemda").val();
        let pihak_terkena = jQuery("#pihak_terkena_pemda").val();
        let skala_dampak = jQuery("#skala_dampak_pemda").val();
        let skala_kemungkinan = jQuery("#skala_kemungkinan_pemda").val();
        let rencana_tindak_pengendalian = jQuery("#rencana_tindak_pengendalian_pemda").val();
        if (skala_dampak < 0) {
            alert('Skala Dampak tidak boleh kurang dari 0');
            return false;
        }
        
        if (skala_dampak > 5) {
            alert('Skala Dampak tidak boleh lebih dari 5');
            return false;
        }
        if (skala_kemungkinan < 0) {
            alert('Skala Kemungkinan tidak boleh kurang dari 0');
            return false;
        }
        
        if (skala_kemungkinan > 5) {
            alert('Skala Kemungkinan tidak boleh lebih dari 5');
            return false;
        }

        jQuery('#wrap-loading').show();

        jQuery.ajax({
            method: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'submit_tujuan_sasaran_pemda',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $_GET['tahun_anggaran']; ?>,
                id: id,
                id_tujuan_sasaran: id_tujuan_sasaran,
                id_indikator: id_indikator,
                tipe: tipe_sebelum,
                id_jadwal: id_jadwal,
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
                    jQuery('#editTujuanSasaranPemdaModal').modal('hide');
                    get_table_tujuan_sasaran_pemda();
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }

    function hapus_tujuan_sasaran_manrisk_pemda(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus data ini? Data sebelum dan sesudah evaluasi akan terhapus')) {
            return;
        }
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'hapus_tujuan_sasaran_manrisk_pemda',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                id: id
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    alert(response.message);
                    get_table_tujuan_sasaran_pemda();
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

    function verif_tujuan_sasaran_manrisk_pemda(id, id_sebelum, id_tujuan_sasaran, id_indikator, tipe_sesudah, id_jadwal) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'verif_tujuan_sasaran_manrisk_pemda',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $_GET['tahun_anggaran']; ?>,
                id: id,
                id_sebelum: id_sebelum,
                id_tujuan_sasaran: id_tujuan_sasaran || 0,
                id_indikator: id_indikator || 0,
                tipe: tipe_sesudah,
                id_jadwal: id_jadwal
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    let data = response.data;
                    let data_sebelum = response.data_sebelum;

                    let tahun = <?php echo $_GET['tahun_anggaran']; ?>;
                    let kode_resiko = 'RSP-'+ tahun.toString().slice(-2);

                    window.value_pemda = {
                        pemilik_resiko_id: data_sebelum.pemilik_resiko || '',
                        sumber_sebab_id: data_sebelum.sumber_sebab || '',
                        pihak_terkena_id: data_sebelum.pihak_terkena || ''
                    };

                    let pemilik_resiko_text = get_nama_by_id(response.data_master.pemilik_resiko, data_sebelum.pemilik_resiko);
                    let sumber_sebab_text   = get_nama_by_id(response.data_master.sumber_sebab, data_sebelum.sumber_sebab);
                    let pihak_terkena_text  = get_nama_by_id(response.data_master.pihak_terdampak, data_sebelum.pihak_terkena);

                    if (data_sebelum.controllable == 0 || data_sebelum.controllable == 1) {
                        jQuery(`input[name='controllable_status_usulan_pemda'][value='${data_sebelum.controllable}']`).prop('checked', true);
                    } else {
                        jQuery("input[name='controllable_status_usulan_pemda']").prop('checked', false);
                    }

                    jQuery("#uraian_resiko_usulan_pemda").val(data_sebelum.uraian_resiko || '');
                    jQuery("#kode_resiko_usulan_pemda").val(kode_resiko || '');
                    jQuery("#pemilik_resiko_usulan_pemda").val(pemilik_resiko_text || '');
                    jQuery("#uraian_sebab_usulan_pemda").val(data_sebelum.uraian_sebab || '');
                    jQuery("#sumber_sebab_usulan_pemda").val(sumber_sebab_text || '');
                    jQuery("#uraian_dampak_usulan_pemda").val(data_sebelum.uraian_dampak || '');
                    jQuery("#pihak_terkena_usulan_pemda").val(pihak_terkena_text || '');
                    jQuery("#skala_dampak_usulan_pemda").val(data_sebelum.skala_dampak || '');
                    jQuery("#skala_kemungkinan_usulan_pemda").val(data_sebelum.skala_kemungkinan || '');
                    jQuery("#rencana_tindak_pengendalian_usulan_pemda").val(data_sebelum.rencana_tindak_pengendalian || '');

                    jQuery('#id_tujuan_sasaran_sesudah_pemda').val(id_tujuan_sasaran);
                    jQuery('#id_indikator_sesudah_pemda').val(id_indikator);
                    jQuery('#tipe_sesudah_pemda').val(tipe_sesudah);
                    jQuery('#id_jadwal_sesudah_pemda').val(id_jadwal);
                    
                    if (data && Object.keys(data).length > 0) {
                        jQuery("#id_data_sesudah_pemda").val(data.id);
                        jQuery('#nama_tujuan_sasaran_sesudah_pemda').val(data.nama_tujuan_sasaran);
                        jQuery('#indikator_kinerja_sesudah_pemda').val(data.indikator_teks);
                        
                        if (data.controllable == 0 || data.controllable == 1) {
                            jQuery(`input[name='controllable_status_sesudah_pemda'][value='${data.controllable}']`).prop('checked', true);
                        } else {
                            jQuery("input[name='controllable_status_sesudah_pemda']").prop('checked', false);
                        }
                        
                        jQuery("#uraian_resiko_sesudah_pemda").val(data.uraian_resiko);
                        jQuery("#kode_resiko_sesudah_pemda").val(kode_resiko);
                        jQuery("#pemilik_resiko_sesudah_pemda").val(data.pemilik_resiko);
                        jQuery("#uraian_sebab_sesudah_pemda").val(data.uraian_sebab);
                        jQuery("#sumber_sebab_sesudah_pemda").val(data.sumber_sebab);
                        jQuery("#uraian_dampak_sesudah_pemda").val(data.uraian_dampak);
                        jQuery("#pihak_terkena_sesudah_pemda").val(data.pihak_terkena);
                        jQuery("#skala_dampak_sesudah_pemda").val(data.skala_dampak);
                        jQuery("#skala_kemungkinan_sesudah_pemda").val(data.skala_kemungkinan);
                        jQuery("#rencana_tindak_pengendalian_sesudah_pemda").val(data.rencana_tindak_pengendalian);
                    } else {
                        reset_form_verifikasi_pemda(kode_resiko);
                    }

                    jQuery('#id_sebelum_pemda').val(id_sebelum);
                    jQuery('#VerifikasiPemdaModal').hide();
                    jQuery('#VerifikasiPemdaModalLabel').show();
                    jQuery('#VerifikasiPemdaModal').modal('show');
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

    function copy_usulan_pemda(button) {
        try {
            jQuery(button).prop('disabled', true);
            
            let uraian_resiko = jQuery("#uraian_resiko_usulan_pemda").val();
            let kode_resiko = jQuery("#kode_resiko_usulan_pemda").val();
            let uraian_sebab = jQuery("#uraian_sebab_usulan_pemda").val();
            let uraian_dampak = jQuery("#uraian_dampak_usulan_pemda").val();
            let skala_dampak = jQuery("#skala_dampak_usulan_pemda").val();
            let skala_kemungkinan = jQuery("#skala_kemungkinan_usulan_pemda").val();
            let rencana_tindak_pengendalian = jQuery("#rencana_tindak_pengendalian_usulan_pemda").val();
            let controllable_status = jQuery("input[name='controllable_status_usulan_pemda']:checked").val();
            
            let pemilik_resiko_id = window.value_pemda ? window.value_pemda.pemilik_resiko_id : '';
            let sumber_sebab_id = window.value_pemda ? window.value_pemda.sumber_sebab_id : '';
            let pihak_terkena_id = window.value_pemda ? window.value_pemda.pihak_terkena_id : '';
            
            jQuery("#uraian_resiko_sesudah_pemda").val(uraian_resiko);
            jQuery("#kode_resiko_sesudah_pemda").val(kode_resiko);
            jQuery("#pemilik_resiko_sesudah_pemda").val(pemilik_resiko_id);
            jQuery("#uraian_sebab_sesudah_pemda").val(uraian_sebab);
            jQuery("#sumber_sebab_sesudah_pemda").val(sumber_sebab_id);
            jQuery("#uraian_dampak_sesudah_pemda").val(uraian_dampak);
            jQuery("#pihak_terkena_sesudah_pemda").val(pihak_terkena_id);
            jQuery("#skala_dampak_sesudah_pemda").val(skala_dampak);
            jQuery("#skala_kemungkinan_sesudah_pemda").val(skala_kemungkinan);
            jQuery("#rencana_tindak_pengendalian_sesudah_pemda").val(rencana_tindak_pengendalian);
            
            jQuery("#pemilik_resiko_sesudah_pemda").trigger('change');
            jQuery("#sumber_sebab_sesudah_pemda").trigger('change');
            jQuery("#pihak_terkena_sesudah_pemda").trigger('change');
            
            jQuery(button).html('<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Data Berhasil Disalin!');
            jQuery(button).removeClass('btn-danger').addClass('btn-success');

            if (controllable_status) {
                jQuery(`input[name='controllable_status_sesudah_pemda'][value='${controllable_status}']`).prop('checked', true);
            }
            
            let penetapan = [
                "#pemilik_resiko_sesudah_pemda","#sumber_sebab_sesudah_pemda","#pihak_terkena_sesudah_pemda",
                "#controllable_status_sesudah_pemda","#uraian_resiko_sesudah_pemda", "#kode_resiko_sesudah_pemda",
                "#uraian_sebab_sesudah_pemda", "#uraian_dampak_sesudah_pemda",
                "#skala_dampak_sesudah_pemda", "#skala_kemungkinan_sesudah_pemda",
                "#rencana_tindak_pengendalian_sesudah_pemda"
            ];
            
            penetapan.forEach(function(selector) {
                jQuery(selector).addClass('field-updated');
            });
            
            setTimeout(function() {
                penetapan.forEach(function(selector) {
                    jQuery(selector).removeClass('field-updated');
                });
                jQuery(button).html('<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Sebelum Evaluasi ke Sesudah Evaluasi');
                jQuery(button).removeClass('btn-success').addClass('btn-danger');
                jQuery(button).prop('disabled', false);
            }, 3000);
            
        } catch (error) {
            console.error('Error saat menyalin data:', error);
            alert('Terjadi kesalahan saat menyalin data. Silakan coba lagi.');
            
            jQuery(button).prop('disabled', false);
        }
    }

    function reset_form_verifikasi_pemda(kode_resiko) {
        jQuery("#id_data_sesudah_pemda").val('');
        
        jQuery('#nama_tujuan_sasaran_sesudah_pemda').val('');
        jQuery('#indikator_kinerja_sesudah_pemda').val('');
        
        jQuery("input[name='controllable_status_sesudah_pemda']").prop('checked', false);
        
        jQuery("#uraian_resiko_sesudah_pemda").val('');
        jQuery("#kode_resiko_sesudah_pemda").val(kode_resiko);
        jQuery("#pemilik_resiko_sesudah_pemda").val('');
        jQuery("#uraian_sebab_sesudah_pemda").val('');
        jQuery("#sumber_sebab_sesudah_pemda").val('');
        jQuery("#uraian_dampak_sesudah_pemda").val('');
        jQuery("#pihak_terkena_sesudah_pemda").val('');
        jQuery("#skala_dampak_sesudah_pemda").val('');
        jQuery("#skala_kemungkinan_sesudah_pemda").val('');
        jQuery("#rencana_tindak_pengendalian_sesudah_pemda").val('');
    }

    function reset_form_tambah_pemda(id_tujuan_sasaran, id_indikator, nama_tujuan_sasaran, indikator_teks, tipe, kode_resiko, id_jadwal) {
        jQuery('#id_tujuan_sasaran_pemda').val(id_tujuan_sasaran);
        jQuery('#id_indikator_pemda').val(id_indikator);
        jQuery('#tipe_sebelum_pemda').val(tipe);
        jQuery('#id_jadwal_pemda').val(id_jadwal);
        jQuery('#nama_tujuan_sasaran_pemda').val(nama_tujuan_sasaran);
        jQuery('#indikator_kinerja_pemda').val(indikator_teks);
        
        jQuery("input[name='controllable_status_pemda']").prop('checked', false);
        jQuery("#id_data_pemda").val('');
        jQuery("#uraian_resiko_pemda").val('');
        jQuery("#kode_resiko_pemda").val(kode_resiko);
        jQuery("#pemilik_resiko_pemda").val('');
        jQuery("#uraian_sebab_pemda").val('');
        jQuery("#sumber_sebab_pemda").val('');
        jQuery("#uraian_dampak_pemda").val('');
        jQuery("#pihak_terkena_pemda").val('');
        jQuery("#skala_dampak_pemda").val('');
        jQuery("#skala_kemungkinan_pemda").val('');
        jQuery("#rencana_tindak_pengendalian_pemda").val('');
    }

    function submit_verif_tujuan_sasaran_pemda() {
        let id = jQuery('#id_data_sesudah_pemda').val();
        let id_sebelum = jQuery('#id_sebelum_pemda').val();
        let id_tujuan_sasaran = jQuery("#id_tujuan_sasaran_sesudah_pemda").val();
        let id_indikator = jQuery("#id_indikator_sesudah_pemda").val();
        let tipe_sesudah = jQuery("#tipe_sesudah_pemda").val();
        let id_jadwal = jQuery("#id_jadwal_sesudah_pemda").val();
        let uraian_resiko = jQuery("#uraian_resiko_sesudah_pemda").val();
        let kode_resiko = jQuery("#kode_resiko_sesudah_pemda").val();
        let pemilik_resiko = jQuery("#pemilik_resiko_sesudah_pemda").val();
        let uraian_sebab = jQuery("#uraian_sebab_sesudah_pemda").val();
        let sumber_sebab = jQuery("#sumber_sebab_sesudah_pemda").val();
        let controllable_status = jQuery("input[name='controllable_status_sesudah_pemda']:checked").val();
        let uraian_dampak = jQuery("#uraian_dampak_sesudah_pemda").val();
        let pihak_terkena = jQuery("#pihak_terkena_sesudah_pemda").val();
        let skala_dampak = jQuery("#skala_dampak_sesudah_pemda").val();
        let skala_kemungkinan = jQuery("#skala_kemungkinan_sesudah_pemda").val();
        let rencana_tindak_pengendalian = jQuery("#rencana_tindak_pengendalian_sesudah_pemda").val();
        if (skala_dampak < 0) {
            alert('Skala Dampak tidak boleh kurang dari 0');
            return false;
        }
        
        if (skala_dampak > 5) {
            alert('Skala Dampak tidak boleh lebih dari 5');
            return false;
        }
        if (skala_kemungkinan < 0) {
            alert('Skala Kemungkinan tidak boleh kurang dari 0');
            return false;
        }
        
        if (skala_kemungkinan > 5) {
            alert('Skala Kemungkinan tidak boleh lebih dari 5');
            return false;
        }

        jQuery('#wrap-loading').show();

        jQuery.ajax({
            method: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'submit_verif_tujuan_sasaran_pemda',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $_GET['tahun_anggaran']; ?>,
                id: id,
                id_sebelum: id_sebelum,
                id_tujuan_sasaran: id_tujuan_sasaran,
                id_indikator: id_indikator,
                tipe: tipe_sesudah,
                id_jadwal: id_jadwal,
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
                    jQuery('#VerifikasiPemdaModal').modal('hide');
                    get_table_tujuan_sasaran_pemda();
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }

    function options_manrisk_pemda() {
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
                    let pemilik_resiko = '<option value="">Pilih Pemilik Risiko</option>';
                    response.data.pemilik_resiko.forEach(function(item) {
                        pemilik_resiko += `<option value="${item.id}">${item.nama}</option>`;
                    });
                    jQuery('#pemilik_resiko_pemda').html(pemilik_resiko);

                    let sumber_sebab = '<option value="">Pilih Sumber Sebab</option>';
                    response.data.sumber_sebab.forEach(function(item) {
                        sumber_sebab += `<option value="${item.id}">${item.nama}</option>`;
                    });
                    jQuery('#sumber_sebab_pemda').html(sumber_sebab);

                    let pihak_terdampak = '<option value="">Pilih Pihak Dampak yang Terkena</option>';
                    response.data.pihak_terdampak.forEach(function(item) {
                        let value = item.id;
                        pihak_terdampak += `<option value="${value}">${item.nama}</option>`;
                    });
                    jQuery('#pihak_terkena_pemda').html(pihak_terdampak);
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan!');
            }
        });
    }

    function options_verif_manrisk_pemda() {
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
                    let pemilik_resiko = '<option value="">Pilih Pemilik Risiko</option>';
                    response.data.pemilik_resiko.forEach(function(item) {
                        pemilik_resiko += `<option value="${item.id}">${item.nama}</option>`;
                    });
                    jQuery('#pemilik_resiko_sesudah_pemda').html(pemilik_resiko);

                    let sumber_sebab = '<option value="">Pilih Sumber Sebab</option>';
                    response.data.sumber_sebab.forEach(function(item) {
                        sumber_sebab += `<option value="${item.id}">${item.nama}</option>`;
                    });
                    jQuery('#sumber_sebab_sesudah_pemda').html(sumber_sebab);

                    let pihak_terdampak = '<option value="">Pilih Pihak Dampak yang Terkena</option>';
                    response.data.pihak_terdampak.forEach(function(item) {
                        pihak_terdampak += `<option value="${item.id}">${item.nama}</option>`;
                    });
                    jQuery('#pihak_terkena_sesudah_pemda').html(pihak_terdampak);
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