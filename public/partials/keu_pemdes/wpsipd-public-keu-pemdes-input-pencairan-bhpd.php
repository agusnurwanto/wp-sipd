<?php
global $wpdb;

$input = shortcode_atts(array(
    'id_skpd' => '',
    'tahun_anggaran' => ''
), $atts);
if (!empty($_GET) && !empty($_GET['tahun_anggaran'])) {
    $input['tahun_anggaran'] = $wpdb->prepare('%d', $_GET['tahun_anggaran']);
}
if (!empty($_GET) && !empty($_GET['id_skpd'])) {
    $input['id_skpd'] = $wpdb->prepare('%d', $_GET['id_skpd']);
}

$idtahun = $wpdb->get_results("select distinct tahun_anggaran from data_unit", ARRAY_A);
$tahun = "<option value='-1'>Pilih Tahun</option>";
foreach ($idtahun as $val) {
    $selected = '';
    if (!empty($input['tahun_anggaran']) && $val['tahun_anggaran'] == $input['tahun_anggaran']) {
        $selected = 'selected';
    }
    $tahun .= "<option value='$val[tahun_anggaran]' $selected>$val[tahun_anggaran]</option>";
}

$nama_skpd = '';
$nama_kec = '';
$user_id = um_user('ID');
$user_meta = get_userdata($user_id);
$disabled = 'disabled';
if (in_array("administrator", $user_meta->roles)) {
    $disabled = '';
} else if (
    in_array("PA", $user_meta->roles)
    || in_array("PLT", $user_meta->roles)
    || in_array("KPA", $user_meta->roles)
) {
    if (
        empty($input['id_skpd'])
        || empty($input['tahun_anggaran'])
    ) {
        die('<h1>ID SKPD dan tahun anggaran tidak boleh kosong!</h1>');
    }
    $nipkepala = get_user_meta($user_id, '_nip');
    $skpd = $wpdb->get_row($wpdb->prepare("
        SELECT 
            nama_skpd, 
            id_skpd, 
            kode_skpd,
            is_skpd
        from data_unit 
        where id_skpd=%d
            and active=1
            and tahun_anggaran=%d
        group by id_skpd", $input['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
    $nama_skpd = '<br>' . $skpd['nama_skpd'] . '<br>Tahun ' . $input['tahun_anggaran'];
    $nama_kec = str_replace('kecamatan ', '', strtolower($skpd['nama_skpd']));
} else {
    die('<h1>Anda tidak punya akses untuk melihat halaman ini!</h1>');
}

?>
<style type="text/css">
    .wrap-table {
        overflow: auto;
        max-height: 100vh;
        width: 100%;
    }
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="cetak">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
        <h1 class="text-center" style="margin:3rem;">Pencairan Bagi Hasil Pajak Daerah ( BHPD )<?php echo $nama_skpd; ?></h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_data_pencairan_bhpd();"><i class="dashicons dashicons-plus"></i> Tambah Data</button>
        </div>
        <div class="wrap-table">
            <table id="management_data_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Tahun Anggaran</th>
                        <th class="text-center">Kecamatan</th>
                        <th class="text-center">Desa</th>
                        <th class="text-center">Pagu Anggaran</th>
                        <th class="text-center">Keterangan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade mt-4" id="modalTambahDataPencairanBHPD" tabindex="-1" role="dialog" aria-labelledby="modalTambahDataPencairanBHPDLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataPencairanBHPDLabel">Data Pencairan BHPD</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type='hidden' id='id_data' name="id_data" placeholder=''>
                <div class="form-group">
                    <label>Tahun Anggaran</label>
                    <select class="form-control" id="tahun" onchange="get_bhpd();">
                        <?php echo $tahun ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pilih Kecamatan</label>
                    <select class="form-control" id="kec" onchange="get_desa();">
                    </select>
                </div>
                <div class="form-group">
                    <label>Pilih Desa</label>
                    <select class="form-control" id="desa" onchange="get_pagu();">
                        <input type="hidden" class="form-control" id="id_bhpd" />
                    </select>
                </div>
                <div class="form-group">
                    <label>Validasi Pagu Anggaran</label>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Pagu Anggaran</th>
                                <th class="text-center" style="width: 30%;">Total Pencairan</th>
                                <th class="text-center" style="width: 30%;">Sisa</th>
                            </tr>
                        </thead>
                        <tbody id="validasi_pagu"></tbody>
                    </table>
                </div>
                <div class="form-group">
                    <label>Pagu Anggaran</label>
                    <input type="number" class="form-control" id="pagu_anggaran" onchange="validasi_pagu();"/>
                </div>
                <div class="form-group">
                    <label for="">Keterangan Pencairan</label>
                    <textarea class="form-control" id="keterangan"></textarea>
                </div>
                <div class="form-group">
                    <label for="">Nota Dinas Permohonan beserta lampirannya</label>
                    <input type="file" name="file" class="form-control-file" id="nota_dinas" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_nota_dinas_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Pernyataan Tanggung Jawab</label>
                    <input type="file" name="file" class="form-control-file" id="sptj" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_sptj_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Pakta Integritas</label>
                    <input type="file" name="file" class="form-control-file" id="pakta_integritas" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_pakta_integritas_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Permohonan Transfer</label>
                    <input type="file" name="file" class="form-control-file" id="permohonan_transfer" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_permohonan_transfer_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Rekomendasi dari Camat</label>
                    <input type="file" name="file" class="form-control-file" id="rekomendasi" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_rekomendasi_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Permohonan Penyaluran dari Kepala Desa</label>
                    <input type="file" name="file" class="form-control-file" id="permohonan_penyaluran_kades" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_permohonan_penyaluran_kades_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Pernyataan Tanggung Jawab Kepala Desa bermaterai</label>
                    <input type="file" name="file" class="form-control-file" id="sptj_kades" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_sptj_kades_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Pakta Integritas Kepala Desa bermaterai</label>
                    <input type="file" name="file" class="form-control-file" id="pakta_integritas_kades" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_pakta_integritas_kades_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Pernyataan Kepala Desa bahwa SPJ DBH Pajak Daerah telah selesai 100% bermaterai</label>
                    <input type="file" name="file" class="form-control-file" id="pernyataaan_kades_spj_dbhpd" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_pernyataaan_kades_spj_dbhpd_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Keputusan Kepala Desa tentang Pengangkatan Bendahara Desa</label>
                    <input type="file" name="file" class="form-control-file" id="sk_bendahara_desa" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_sk_bendahara_desa_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Foto Copy KTP Kepala Desa</label>
                    <input type="file" name="file" class="form-control-file" id="fc_ktp_kades" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_fc_ktp_kades_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Foto Copy Rekening Kas Desa</label>
                    <input type="file" name="file" class="form-control-file" id="fc_rek_kas_desa" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_fc_rek_kas_desa_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Laporan Realisasi Tahun Sebelumnya</label>
                    <input type="file" name="file" class="form-control-file" id="laporan_realisasi_tahun_sebelumnya" accept="application/pdf">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_laporan_realisasi_sebelumnya_existing"></a></div>
                </div>
                <div><small>Upload file maksimal 1 Mb, berformat .pdf</small></div>
                <div class="form-check form-switch">
                    <input class="form-check-input" value="1" type="checkbox" id="status_pencairan" onclick="set_keterangan(this);" <?php echo $disabled; ?>>
                    <label class="form-check-label" for="status_pencairan">Setujui pencairan</label>
                </div>
                <div class="form-group" style="display:none;">
                    <label>Keterangan status pencairan</label>
                    <textarea class="form-control" id="keterangan_status_pencairan" <?php echo $disabled; ?>></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="submitTambahDataFormPencairanBHPD(this);" class="btn btn-primary send_data">Kirim</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>

    jQuery(document).ready(function() {
        get_data_pencairan_bhpd();
        window.global_file_upload = "<?php echo WPSIPD_PLUGIN_URL . 'public/media/keu_pemdes/'; ?>";
    });

    function set_keterangan(that) {
        var id = jQuery(that).attr('id');
        if (jQuery(that).is(':checked')) {
            jQuery('#keterangan_' + id).closest('.form-group').hide();
        } else {
            jQuery('#keterangan_' + id).closest('.form-group').show();
        }
    }

    function submit_pencairan(id) {
        if (confirm("Apakah anda yakin untuk mensubmit pencairan ini?")) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                data: {
                    'action': 'verifikasi_pencairan_desa',
                    'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                    'id': id,
                    'tipe': 'submit_pencairan',
                    'jenis_belanja': 'bhpd'
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        get_data_pencairan_bhpd();
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    <?php if (in_array("administrator", $user_meta->roles)) : ?>

        function verifikasi_data(_id) {
            detail_data(_id)
                .then(function(status) {
                    if (status) {
                        jQuery('#keterangan_status_pencairan').prop('disabled', false).closest('.form-group').show();
                        jQuery('#status_pencairan').prop('disabled', false);
                        jQuery('#modalTambahDataPencairanBHPD .send_data').attr('tipe_verifikasi', 'verikasi_admin').show();
                    }
                });
        }

        function submit_verifikasi() {
            var id = jQuery('#id_data').val();
            if (id == '') {
                return alert('ID tidak boleh kosong!');
            }
            var keterangan_status_pencairan = jQuery('#keterangan_status_pencairan').val();
            var status_pencairan = 2;
            if (jQuery('#status_pencairan').is(':checked')) {
                status_pencairan = 1;
            } else if (keterangan_status_pencairan == '') {
                return alert('Keterangan status file tidak boleh kosong!');
            }
            if (confirm('Apakah anda yakin untuk memverifikasi pencairan ini?')) {
                jQuery('#wrap-loading').show();
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    data: {
                        'action': 'verifikasi_pencairan_desa',
                        'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                        'id': id,
                        'tipe': 'verifikasi_admin',
                        'status_pencairan': status_pencairan,
                        'keterangan_status_pencairan': keterangan_status_pencairan,
                        'jenis_belanja': 'bhpd'
                    },
                    dataType: 'json',
                    success: function(response) {
                        jQuery('#wrap-loading').hide();
                        if (response.status == 'success') {
                            jQuery('#modalTambahDataPencairanBHPD').modal('hide');
                            get_data_pencairan_bhpd();
                        } else {
                            alert(`GAGAL! \n${response.message}`);
                        }
                    }
                });
            }
        }
    <?php endif; ?>

    function get_data_pencairan_bhpd() {
        if (typeof datapencairan_bhpd == 'undefined') {
            window.datapencairan_bhpd = jQuery('#management_data_table').on('preXhr.dt', function(e, settings, data) {
                jQuery("#wrap-loading").show();
            }).DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'action': 'get_datatable_data_pencairan_bhpd',
                        'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                        'tahun_anggaran': '<?php echo $input['tahun_anggaran']; ?>',
                        'id_skpd': '<?php echo $input['id_skpd']; ?>'
                    }
                },
                lengthMenu: [
                    [20, 50, 100, -1]
                    [20, 50, 100, "All"]
                ],
                order: [
                    [0, 'asc']
                ],
                "drawCallback": function(settings) {
                    jQuery("#wrap-loading").hide();
                },
                "columns": [{
                        "data": 'tahun_anggaran',
                        className: "text-center"
                    },
                    {
                        "data": 'kecamatan',
                        className: "text-center"
                    },
                    {
                        "data": 'desa',
                        className: "text-center"
                    },
                    {
                        "data": 'total_pencairan',
                        className: "text-right"
                    },
                    {
                        "data": 'keterangan',
                        className: "text-center"
                    },
                    {
                        "data": 'status',
                        className: "text-center"
                    },
                    {
                        "data": 'aksi',
                        className: "text-center"
                    }
                ]
            });
        } else {
            datapencairan_bhpd.draw();
        }
    }

    function hapus_data(id) {
        let confirmDelete = confirm("Apakah anda yakin akan menghapus data ini?");
        if (confirmDelete) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                data: {
                    'action': 'hapus_data_pencairan_bhpd_by_id',
                    'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                    'id': id
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        get_data_pencairan_bhpd();
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    function edit_data(_id) {
        jQuery('#wrap-loading').show();
        jQuery('#modalTambahDataPencairanBHPD .send_data').attr('tipe_verifikasi', '');
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_data_pencairan_bhpd_by_id',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'id': _id,
            },
            success: function(res) {
                if (res.status == 'success') {
                    jQuery('#id_data').val(res.data.id).prop('disabled', false);
                    jQuery('#tahun').val(res.data.tahun_anggaran).prop('disabled', false);
                    get_bhpd()
                        .then(function() {
                            jQuery('#kec').val(res.data.kecamatan).trigger('change').prop('disabled', false);
                            jQuery('#desa').val(res.data.desa).prop('disabled', false);
                            jQuery('#id_bhpd').val(res.data.id_bhpd).prop('disabled', false);
                            jQuery('#validasi_pagu').closest('.form-group').show().prop('disabled', false);
                            get_pagu()
                                .then(function() {
                                    jQuery('#pagu_anggaran').val(res.data.total_pencairan).prop('disabled', false);
                                    if (res.data.status_ver_total == 0) {
                                        jQuery('#keterangan_status_pencairan').closest('.form-group').show().prop('disabled', false);
                                        jQuery('#status_pencairan').prop('checked', false);
                                    } else {
                                        jQuery('#keterangan_status_pencairan').closest('.form-group').hide().prop('disabled', false);
                                        jQuery('#status_pencairan').prop('checked', true);
                                    }
                                    jQuery('#file_nota_dinas_existing').attr('href', global_file_upload + res.data.file_nota_dinas).html(res.data.file_nota_dinas).show();
                                    jQuery('#nota_dinas').val('').show();
                                    jQuery('#file_sptj_existing').attr('href', global_file_upload + res.data.file_sptj).html(res.data.file_sptj).show();
                                    jQuery('#sptj').val('').show();
                                    jQuery('#file_pakta_integritas_existing').attr('href', global_file_upload + res.data.file_pakta_integritas).html(res.data.file_pakta_integritas).show();
                                    jQuery('#pakta_integritas').val('').show();
                                    jQuery('#file_permohonan_transfer_existing').attr('href', global_file_upload + res.data.file_permohonan_transfer).html(res.data.file_permohonan_transfer).show();
                                    jQuery('#permohonan_transfer').val('').show();
                                    jQuery('#file_rekomendasi_existing').attr('href', global_file_upload + res.data.file_rekomendasi).html(res.data.file_rekomendasi).show();
                                    jQuery('#rekomendasi').val('').show();
                                    jQuery('#file_permohonan_penyaluran_kades_existing').attr('href', global_file_upload + res.data.file_permohonan_penyaluran_kades).html(res.data.file_permohonan_penyaluran_kades).show();
                                    jQuery('#permohonan_penyaluran_kades').val('').show();
                                    jQuery('#file_sptj_kades_existing').attr('href', global_file_upload + res.data.file_sptj_kades).html(res.data.file_sptj_kades).show();
                                    jQuery('#sptj_kades').val('').show();
                                    jQuery('#file_pakta_integritas_kades_existing').attr('href', global_file_upload + res.data.file_pakta_integritas_kades).html(res.data.file_pakta_integritas_kades).show();
                                    jQuery('#pakta_integritas_kades').val('').show();
                                    jQuery('#file_pernyataaan_kades_spj_dbhpd_existing').attr('href', global_file_upload + res.data.file_pernyataaan_kades_spj_dbhpd).html(res.data.file_pernyataaan_kades_spj_dbhpd).show();
                                    jQuery('#pernyataaan_kades_spj_dbhpd').val('').show();
                                    jQuery('#file_sk_bendahara_desa_existing').attr('href', global_file_upload + res.data.file_sk_bendahara_desa).html(res.data.file_sk_bendahara_desa).show();
                                    jQuery('#sk_bendahara_desa').val('').show();
                                    jQuery('#file_fc_ktp_kades_existing').attr('href', global_file_upload + res.data.file_fc_ktp_kades).html(res.data.file_fc_ktp_kades).show();
                                    jQuery('#fc_ktp_kades').val('').show();
                                    jQuery('#file_fc_rek_kas_desa_existing').attr('href', global_file_upload + res.data.file_fc_rek_kas_desa).html(res.data.file_fc_rek_kas_desa).show();
                                    jQuery('#fc_rek_kas_desa').val('').show();
                                    jQuery('#file_laporan_realisasi_sebelumnya_existing').attr('href', global_file_upload + res.data.file_laporan_realisasi_sebelumnya).html(res.data.file_laporan_realisasi_sebelumnya).show();
                                    jQuery('#laporan_realisasi_sebelumnya').val('').show();
                                    jQuery('#file_pernyataaan_kades_spj_dbhpd_existing').attr('href', global_file_upload + res.data.file_pernyataaan_kades_spj_dbhpd).html(res.data.file_pernyataaan_kades_spj_dbhpd).show();
                                    jQuery('#pernyataaan_kades_spj_dbhpd').val('').show();
                                    jQuery('#file_pernyataaan_kades_spj_dbhpd_existing').attr('href', global_file_upload + res.data.file_pernyataaan_kades_spj_dbhpd).html(res.data.file_pernyataaan_kades_spj_dbhpd).show();
                                    jQuery('#pernyataaan_kades_spj_dbhpd').val('').show();
                                    jQuery('#file_pernyataaan_kades_spj_dbhpd_existing').attr('href', global_file_upload + res.data.file_pernyataaan_kades_spj_dbhpd).html(res.data.file_pernyataaan_kades_spj_dbhpd).show();
                                    jQuery('#pernyataaan_kades_spj_dbhpd').val('').show();
                                    jQuery('#file_pernyataaan_kades_spj_dbhpd_existing').attr('href', global_file_upload + res.data.file_pernyataaan_kades_spj_dbhpd).html(res.data.file_pernyataaan_kades_spj_dbhpd).show();
                                    jQuery('#pernyataaan_kades_spj_dbhpd').val('').show();

                                    jQuery('#keterangan_status_pencairan').val(res.data.ket_ver_total).prop('disabled', false);
                                    jQuery('#keterangan').val(res.data.keterangan).prop('disabled', false);
                                    jQuery('#status_pencairan').closest('.form-check').show().prop('disabled', false);
                                    jQuery('#modalTambahDataPencairanBHPD .send_data').show();
                                    jQuery('#modalTambahDataPencairanBHPD').modal('show');
                                });
                        });
                } else {
                    alert(res.message);
                    jQuery('#wrap-loading').hide();
                }
            }
        });
    }

    function detail_data(_id) {
        return new Promise(function(resolve, reject) {
            jQuery('#wrap-loading').show();
            jQuery('#modalTambahDataPencairanBHPD .send_data').attr('tipe_verifikasi', '');
            jQuery.ajax({
                method: 'post',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: {
                    'action': 'get_data_pencairan_bhpd_by_id',
                    'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                    'id': _id,
                },
                success: function(res) {
                    if (res.status == 'success') {
                        jQuery('#id_data').val(res.data.id).prop('disabled', true);
                        jQuery('#tahun').val(res.data.tahun_anggaran).prop('disabled', true);
                        get_bhpd()
                            .then(function() {
                                jQuery('#kec').val(res.data.kecamatan).trigger('change').prop('disabled', true);
                                jQuery('#desa').val(res.data.desa).trigger('change').prop('disabled', true);
                                jQuery('#uraian_kegiatan').val(res.data.kegiatan).trigger('change').prop('disabled', true);
                                jQuery('#id_bhpd').val(res.data.id_bhpd).prop('disabled', true);
                                jQuery('#alamat').val(res.data.alamat).prop('disabled', true);
                                get_pagu()
                                    .then(function() {
                                        jQuery('#pagu_anggaran').val(res.data.total_pencairan).prop('disabled', true);
                                        if (res.data.status_ver_total == 0) {
                                            jQuery('#keterangan_status_pencairan').closest('.form-group').show().prop('disabled', true);
                                            jQuery('#status_pencairan').prop('checked', false).prop('disabled', true);
                                        } else {
                                            jQuery('#keterangan_status_pencairan').closest('.form-group').hide().prop('disabled', true);
                                            jQuery('#status_pencairan').prop('checked', true).prop('disabled', true);
                                        }

                                        jQuery('#file_nota_dinas_existing').attr('href', global_file_upload + res.data.file_nota_dinas).html(res.data.file_nota_dinas);
                                        jQuery('#nota_dinas').val('').hide();
                                        jQuery('#file_sptj_existing').attr('href', global_file_upload + res.data.file_sptj).html(res.data.file_sptj);
                                        jQuery('#sptj').val('').hide();
                                        jQuery('#file_pakta_integritas_existing').attr('href', global_file_upload + res.data.file_pakta_integritas).html(res.data.file_pakta_integritas);
                                        jQuery('#pakta_integritas').val('').hide();
                                        jQuery('#file_permohonan_transfer_existing').attr('href', global_file_upload + res.data.file_permohonan_transfer).html(res.data.file_permohonan_transfer);
                                        jQuery('#permohonan_transfer').val('').hide();
                                        jQuery('#file_rekomendasi_existing').attr('href', global_file_upload + res.data.file_rekomendasi).html(res.data.file_rekomendasi);
                                        jQuery('#rekomendasi').val('').hide();
                                        jQuery('#file_permohonan_penyaluran_kades_existing').attr('href', global_file_upload + res.data.file_permohonan_penyaluran_kades).html(res.data.file_permohonan_penyaluran_kades);
                                        jQuery('#permohonan_penyaluran_kades').val('').hide();
                                        jQuery('#file_sptj_kades_existing').attr('href', global_file_upload + res.data.file_sptj_kades).html(res.data.file_sptj_kades);
                                        jQuery('#sptj_kades').val('').hide();
                                        jQuery('#file_pakta_integritas_kades_existing').attr('href', global_file_upload + res.data.file_pakta_integritas_kades).html(res.data.file_pakta_integritas_kades);
                                        jQuery('#pakta_integritas_kades').val('').hide();
                                        jQuery('#file_pernyataaan_kades_spj_dbhpd_existing').attr('href', global_file_upload + res.data.file_pernyataaan_kades_spj_dbhpd).html(res.data.file_pernyataaan_kades_spj_dbhpd);
                                        jQuery('#pernyataaan_kades_spj_dbhpd').val('').hide();
                                        jQuery('#file_sk_bendahara_desa_existing').attr('href', global_file_upload + res.data.file_sk_bendahara_desa).html(res.data.file_sk_bendahara_desa);
                                        jQuery('#sk_bendahara_desa').val('').hide();
                                        jQuery('#file_fc_ktp_kades_existing').attr('href', global_file_upload + res.data.file_fc_ktp_kades).html(res.data.file_fc_ktp_kades);
                                        jQuery('#fc_ktp_kades').val('').hide();
                                        jQuery('#file_fc_rek_kas_desa_existing').attr('href', global_file_upload + res.data.file_fc_rek_kas_desa).html(res.data.file_fc_rek_kas_desa);
                                        jQuery('#fc_rek_kas_desa').val('').hide();
                                        jQuery('#file_laporan_realisasi_sebelumnya_existing').attr('href', global_file_upload + res.data.file_laporan_realisasi_sebelumnya).html(res.data.file_laporan_realisasi_sebelumnya);
                                        jQuery('#laporan_realisasi_sebelumnya').val('').hide();

                                        jQuery('#keterangan_status_pencairan').val(res.data.ket_ver_total).prop('disabled', true);
                                        jQuery('#keterangan').val(res.data.keterangan).prop('disabled', true);
                                        jQuery('#status_pencairan').closest('.form-check').show().prop('disabled', true);
                                        jQuery('#modalTambahDataPencairanBHPD .send_data').hide();
                                        jQuery('#modalTambahDataPencairanBHPD').modal('show');
                                        resolve(true);
                                    })
                            })
                    } else {
                        alert(res.message);
                        jQuery('#wrap-loading').hide();
                        resolve(false);
                    }
                }
            });
        });
    }

    //show tambah data
    function tambah_data_pencairan_bhpd() {
        jQuery('#modalTambahDataPencairanBHPD .send_data').attr('tipe_verifikasi', '');
        jQuery('#id_data').val('').prop('disabled', false);
        jQuery('#tahun').val('<?php echo $input['tahun_anggaran']; ?>').prop('disabled', false);
        new Promise(function(resolve, reject) {
                if ('<?php echo $input['tahun_anggaran']; ?>' != '') {
                    get_bhpd().then(function() {
                        resolve();
                    });
                } else {
                    resolve();
                }
            })
            .then(function() {
                jQuery('#id_data').val('').prop('disabled', false);
                jQuery('#kec').val('-1').prop('disabled', false);
                jQuery('#desa').val('').prop('disabled', false);
                jQuery('#validasi_pagu').html('');
                jQuery('#pagu_anggaran').val('').prop('disabled', false);
                jQuery('#keterangan').val('').prop('disabled', false);
                jQuery('#status_pencairan').closest('.form-check').hide().prop('disabled', false);
                jQuery('#keterangan_status_pencairan').closest('.form-group').hide().prop('disabled', false);
                jQuery('#status_pencairan').prop('checked', false);
                jQuery('#keterangan_status_pencairan').val('').prop('disabled', false);
                jQuery('#nota_dinas').html('');
                jQuery('#sptj').html('');
                jQuery('#pakta_integritas').html('');
                jQuery('#permohonan_transfer').html('');
                jQuery('#rekomendasi').html('');
                jQuery('#sptj_kades').html('');
                jQuery('#pakta_integritas_kades').html('');
                jQuery('#pernyataaan_kades_spj_dbhpd').html('');
                jQuery('#sk_bendahara_desa').html('');
                jQuery('#fc_ktp_kades').html('');
                jQuery('#fc_rek_kas_desa').html('');
                jQuery('#laporan_realisasi_sebelumnya').html('');
                jQuery('#permohonan_penyaluran_kades').html('')
                
                jQuery('#file_nota_dinas_existing').hide();
                jQuery('#file_nota_dinas_existing').closest('.form-group').find('input').show();

                jQuery('#file_sptj_existing').hide();
                jQuery('#file_sptj_existing').closest('.form-group').find('input').show();

                
                jQuery('#file_pakta_integritas_existing').hide();
                jQuery('#file_pakta_integritas_existing').closest('.form-group').find('input').show();

                
                jQuery('#file_permohonan_transfer_existing').hide();
                jQuery('#file_permohonan_transfer_existing').closest('.form-group').find('input').show();

                
                jQuery('#file_rekomendasi_existing').hide();
                jQuery('#file_rekomendasi_existing').closest('.form-group').find('input').show();

                
                jQuery('#file_permohonan_penyaluran_kades_existing').hide();
                jQuery('#file_permohonan_penyaluran_kades_existing').closest('.form-group').find('input').show();

                
                jQuery('#file_sptj_kades_existing').hide();
                jQuery('#file_sptj_kades_existing').closest('.form-group').find('input').show();

                
                jQuery('#file_pakta_integritas_kades_existing').hide();
                jQuery('#file_pakta_integritas_kades_existing').closest('.form-group').find('input').show();

                
                jQuery('#file_pernyataaan_kades_spj_dbhpd_existing').hide();
                jQuery('#file_pernyataaan_kades_spj_dbhpd_existing').closest('.form-group').find('input').show();

                
                jQuery('#file_sk_bendahara_desa_existing').hide();
                jQuery('#file_sk_bendahara_desa_existing').closest('.form-group').find('input').show();

                
                jQuery('#file_fc_ktp_kades_existing').hide();
                jQuery('#file_fc_ktp_kades_existing').closest('.form-group').find('input').show();

                
                jQuery('#file_fc_rek_kas_desa_existing').hide();
                jQuery('#file_fc_rek_kas_desa_existing').closest('.form-group').find('input').show();

                
                jQuery('#file_laporan_realisasi_sebelumnya_existing').hide();
                jQuery('#file_laporan_realisasi_sebelumnya_existing').closest('.form-group').find('input').show();

                jQuery('#modalTambahDataPencairanBHPD .send_data').show();
                jQuery('#modalTambahDataPencairanBHPD').modal('show');
            });
    }

    function submitTambahDataFormPencairanBHPD(that) {
        var tipe_verifikasi = jQuery(that).attr('tipe_verifikasi');
        console.log('tipe_verifikasi', tipe_verifikasi);
        if (tipe_verifikasi != '') {
            return submit_verifikasi();
        }
        var id_data = jQuery('#id_data').val();
        var desa = jQuery('#desa').val();
        if (desa == '') {
            return alert('Pilih Desa Dulu!');
        }
        var kec = jQuery('#kec').val();
        if (kec == '') {
            return alert('Pilih Kecamatan Dulu!');
        }
        var tahun = jQuery('#tahun').val();
        if (tahun == '') {
            return alert('Pilih Tahun Dulu!');
        }
        var id_bhpd = jQuery('#id_bhpd').val();
        if (id_bhpd == '') {
            return alert('id_bhpd tidak boleh kosong!');
        }
        var pagu_anggaran = jQuery('#pagu_anggaran').val();
        if (pagu_anggaran == '') {
            return alert('Isi Pagu Anggaran Dulu!');
        }
        var keterangan = jQuery('#keterangan').val();
        if (keterangan == '') {
            return alert('Isi keterangan Dulu!');
        }

        var nota_dinas = jQuery('#nota_dinas')[0].files[0];;
        var sptj = jQuery('#sptj')[0].files[0];;
        var pakta_integritas = jQuery('#pakta_integritas')[0].files[0];;
        var permohonan_transfer = jQuery('#permohonan_transfer')[0].files[0];;
        var rekomendasi = jQuery('#rekomendasi')[0].files[0];;
        var permohonan_penyaluran_kades = jQuery('#permohonan_penyaluran_kades')[0].files[0];;
        var sptj_kades = jQuery('#sptj_kades')[0].files[0];;
        var pakta_integritas_kades = jQuery('#pakta_integritas_kades')[0].files[0];;
        var pernyataaan_kades_spj_dbhpd = jQuery('#pernyataaan_kades_spj_dbhpd')[0].files[0];;
        var sk_bendahara_desa = jQuery('#sk_bendahara_desa')[0].files[0];;
        var fc_ktp_kades = jQuery('#fc_ktp_kades')[0].files[0];;
        var fc_rek_kas_desa = jQuery('#fc_rek_kas_desa')[0].files[0];;
        var laporan_realisasi_tahun_sebelumnya = jQuery('#laporan_realisasi_tahun_sebelumnya')[0].files[0];;

        if (id_data == '') {
            if (typeof nota_dinas == 'undefined') {
                return alert('Upload file Nota dinas beserta kelengkapan dulu!');
            }
            if (typeof sptj == 'undefined') {
                return alert('Upload file SPTJ dulu!');
            }
            if (typeof pakta_integritas == 'undefined') {
                return alert('Upload file Pakta integritas dulu!');
            }
            if (typeof permohonan_transfer == 'undefined') {
                return alert('Upload file Surat permohonan transfer dulu!');
            }
            if (typeof rekomendasi == 'undefined') {
                return alert('Upload file Surat rekomendasi dulu!');
            }
            if (typeof permohonan_penyaluran_kades == 'undefined') {
                return alert('Upload file Surat permohonan penyaluran kades dulu!');
            }
            if (typeof sptj_kades == 'undefined') {
                return alert('Upload file SPTJ Kepala Desa dulu!');
            }
            if (typeof pakta_integritas_kades == 'undefined') {
                return alert('Upload file Pakta integritas Kepala Desa dulu!');
            }
            if (typeof pernyataaan_kades_spj_dbhpd == 'undefined') {
                return alert('Upload file Surat Pernyataan Kepala Desa bahwa SPJ DBH Pajak Daerah telah selesai 100% bermaterai dulu!');
            }
            if (typeof sk_bendahara_desa == 'undefined') {
                return alert('Upload file Surat Keputusan Bendahara Desa dulu!');
            }
            if (typeof fc_ktp_kades == 'undefined') {
                return alert('Upload file Foto copy KTP Kepala Desa dulu!');
            }
            if (typeof fc_rek_kas_desa == 'undefined') {
                return alert('Upload file Foto copy rekening kas Desa dulu!');
            }
            var total_pencairan = +jQuery('#pencairan').attr('total-pencairan');
            if (total_pencairan > 0) {
                if (typeof laporan_realisasi_tahun_sebelumnya == 'undefined') {
                    return alert('Upload file Laporan realisasi tahun sebelumnya dulu!');
                }
            }
        }

        var status_pencairan = jQuery('#status_pencairan').val();
        if (jQuery('#status_pencairan').is(':checked') == false) {
            status_pencairan = 0;
        }
        var keterangan_status_pencairan = jQuery('#keterangan_status_pencairan').val();

        let tempData = new FormData();
        tempData.append('action', 'tambah_data_pencairan_bhpd');
        tempData.append('api_key', '<?php echo get_option('_crb_api_key_extension'); ?>');
        tempData.append('id_data', id_data);
        tempData.append('tahun', tahun);
        tempData.append('id_bhpd', id_bhpd);
        tempData.append('pagu_anggaran', pagu_anggaran);
        tempData.append('status_pencairan', status_pencairan);
        tempData.append('keterangan', keterangan);
        tempData.append('keterangan_status_pencairan', keterangan_status_pencairan);
        if (typeof nota_dinas != 'undefined') {
            tempData.append('nota_dinas', nota_dinas);
        }
        if (typeof sptj != 'undefined') {
            tempData.append('sptj', sptj);
        }
        if (typeof pakta_integritas != 'undefined') {
            tempData.append('pakta_integritas', pakta_integritas);
        }
        if (typeof permohonan_transfer != 'undefined') {
            tempData.append('permohonan_transfer', permohonan_transfer);
        }
        if (typeof rekomendasi != 'undefined') {
            tempData.append('rekomendasi', rekomendasi);
        }
        if (typeof permohonan_penyaluran_kades != 'undefined') {
            tempData.append('permohonan_penyaluran_kades', permohonan_penyaluran_kades);
        }
        if (typeof sptj_kades != 'undefined') {
            tempData.append('sptj_kades', sptj_kades);
        }
        if (typeof pakta_integritas_kades != 'undefined') {
            tempData.append('pakta_integritas_kades', pakta_integritas_kades);
        }
        if (typeof pernyataaan_kades_spj_dbhpd != 'undefined') {
            tempData.append('pernyataaan_kades_spj_dbhpd', pernyataaan_kades_spj_dbhpd);
        }
        if (typeof sk_bendahara_desa != 'undefined') {
            tempData.append('sk_bendahara_desa', sk_bendahara_desa);
        }
        if (typeof fc_ktp_kades != 'undefined') {
            tempData.append('fc_ktp_kades', fc_ktp_kades);
        }
        if (typeof fc_rek_kas_desa != 'undefined') {
            tempData.append('fc_rek_kas_desa', fc_rek_kas_desa);
        }
        if (typeof laporan_realisasi_tahun_sebelumnya != 'undefined') {
            tempData.append('laporan_realisasi_tahun_sebelumnya', laporan_realisasi_tahun_sebelumnya);
        }

        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: tempData,
            processData: false,
            contentType: false,
            cache: false,
            success: function(res) {
                alert(res.message);
                if (res.status == 'success') {
                    jQuery('#modalTambahDataPencairanBHPD').modal('hide');
                    get_data_pencairan_bhpd();
                } else {
                    jQuery('#wrap-loading').hide();
                }
            }
        });
    }

    function get_bhpd() {
        return new Promise(function(resolve, reject) {
            var tahun = jQuery('#tahun').val();
            if (tahun == '' || tahun == '-1') {
                alert('Pilih tahun anggaran dulu!');
                return resolve();
            }
            if (typeof bkk_global == 'undefined') {
                window.bkk_global = {};
            }

            if (!bkk_global[tahun]) {
                jQuery('#wrap-loading').show();
                jQuery.ajax({
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    type: "post",
                    data: {
                        'action': "get_pemdes_bhpd",
                        'api_key': jQuery("#api_key").val(),
                        'tahun_anggaran': tahun,
                        'nama_kec': '<?php echo $nama_kec; ?>'
                    },
                    dataType: "json",
                    success: function(response) {
                        bkk_global[tahun] = response.data;
                        window.kecamatan_all = {};
                        bkk_global[tahun].map(function(b, i) {
                            if (!kecamatan_all[b.kecamatan]) {
                                kecamatan_all[b.kecamatan] = {};
                            }
                            if (!kecamatan_all[b.kecamatan][b.desa]) {
                                kecamatan_all[b.kecamatan][b.desa] = [];
                            }
                            kecamatan_all[b.kecamatan][b.desa].push(b);
                        });
                        var kecamatan = '<option value="-1">Pilih Kecamatan</option>';
                        for (var i in kecamatan_all) {
                            kecamatan += '<option value="' + i + '">' + i + '</option>';
                        }
                        jQuery('#kec').html(kecamatan);
                        jQuery('#wrap-loading').hide();
                        return resolve();
                    }
                });
            } else {
                return resolve();
            }
        })
    }

    function get_desa() {
        var kec = jQuery('#kec').val();
        if (kec == '' || kec == '-1') {
            return alert('Pilih kecamatan dulu!');
        }
        var desa = '<option value="-1">Pilih Desa</option>';
        for (var ii in kecamatan_all[kec]) {
            desa += '<option value="' + ii + '">' + ii + '</option>';
        }
        jQuery('#desa').html(desa);
    }

    function get_pagu() {
        return new Promise(function(resolve, reject) {
            var kec = jQuery('#kec').val();
            if (kec == '' || kec == '-1') {
                return alert('Pilih kecamatan dulu!');
            }
            var desa = jQuery('#desa').val();
            if (desa == '' || desa == '-1') {
                return alert('Pilih desa dulu!');
            }
            jQuery('#wrap-loading').show();
            var pagu = kecamatan_all[kec][desa][0].total;
            var id = kecamatan_all[kec][desa][0].id;
            var tahun = kecamatan_all[kec][desa][0].tahun_anggaran;
            jQuery.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "post",
                data: {
                    'action': "get_pencairan_pemdes_bhpd",
                    'api_key': jQuery("#api_key").val(),
                    'tahun_anggaran': tahun,
                    'id': id,
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == 'success') {
                        var total_pencairan = +response.total_pencairan;
                        window.global_sisa = pagu - total_pencairan;
                        var tbody = '' +
                            '<tr>' +
                            '<td class="text-right">' + formatRupiah(pagu) + '</td>' +
                            '<td class="text-right">' + formatRupiah(total_pencairan) + '</td>' +
                            '<td class="text-right">' + formatRupiah(global_sisa) + '</td>' +
                            '</tr>';
                        if (total_pencairan > 0) {
                            jQuery('#laporan_realisasi_tahun_sebelumnya').closest('.form-group').show();
                        } else {
                            jQuery('#laporan_realisasi_tahun_sebelumnya').closest('.form-group').hide();
                        }
                        jQuery('#validasi_pagu').html(tbody);
                        jQuery('#id_bhpd').val(id);
                        jQuery('#pagu_anggaran').val(global_sisa);
                        resolve(true);
                    } else {
                        alert(response.message);
                        resolve(false);
                    }
                    jQuery('#wrap-loading').hide();
                }
            });
        })
    }

    function validasi_pagu() {
        var pagu = jQuery('#pagu_anggaran').val();
        if (pagu > global_sisa) {
            alert('Nilai pencairan tidak boleh lebih besar dari sisa anggaran!');
            jQuery('#pagu_anggaran').val(global_sisa);
        }
    }
</script>