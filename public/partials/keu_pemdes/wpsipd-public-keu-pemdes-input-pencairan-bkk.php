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
        <h1 class="text-center" style="margin:3rem;">Pencairan Bantuan Keuangan Khusus (BKK)<?php echo $nama_skpd; ?></h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_data_pencairan_bkk();"><i class="dashicons dashicons-plus"></i> Tambah Data</button>
        </div>
        <div class="wrap-table">
            <table id="management_data_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Tahun Anggaran</th>
                        <th class="text-center">Kecamatan</th>
                        <th class="text-center">Desa</th>
                        <th class="text-center">Uraian Kegiatan</th>
                        <th class="text-center">Alamat</th>
                        <th class="text-center">Pagu Anggaran</th>
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

<div class="modal fade mt-4" id="modalTambahDataPencairanBKK" tabindex="-1" role="dialog" aria-labelledby="modalTambahDataPencairanBKKLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataPencairanBKKLabel">Data Pencairan BKK</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type='hidden' id='id_data' name="id_data" placeholder=''>
                <div class="form-group">
                    <label>Tahun Anggaran</label>
                    <select class="form-control" id="tahun" onchange="get_bkk();">
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
                    <select class="form-control" id="desa" onchange="get_kegiatan();">
                    </select>
                </div>
                <div class="form-group">
                    <label>Uraian Kegiatan</label>
                    <select class="form-control" id="uraian_kegiatan" onchange="get_alamat();">
                    </select>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <select class="form-control" id="alamat" onchange="get_pagu();">
                    </select>
                    <input type="hidden" class="form-control" id="id_kegiatan" />
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
                    <input type="number" class="form-control" id="pagu_anggaran" onchange="validasi_pagu();" />
                </div>
                <div class="form-group">
                    <label for="">Nota Dinas Permohonan beserta lampirannya</label>
                    <input type="file" name="file" class="form-control-file" id="nota_dinas">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_nota_dinas_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Pernyataan Tanggung Jawab</label>
                    <input type="file" name="file" class="form-control-file" id="sptj">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_sptj_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Pakta Integritas</label>
                    <input type="file" name="file" class="form-control-file" id="pakta_integritas">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_pakta_integritas_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Permohonan Transfer</label>
                    <input type="file" name="file" class="form-control-file" id="permohonan_transfer">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_permohonan_transfer_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Verifikasi dan Rekomendasi</label>
                    <input type="file" name="file" class="form-control-file" id="verifikasi_rekomendasi">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_verifikasi_rekomendasi_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Permohonan Penyaluran dari Kepala Desa</label>
                    <input type="file" name="file" class="form-control-file" id="permohonan_penyaluran_kades">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_permohonan_penyaluran_kades_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Pernyataan Tanggung Jawab Kepala Desa bermaterai</label>
                    <input type="file" name="file" class="form-control-file" id="sptj_kades">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_sptj_kades_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Pakta Integritas Kepala Desa bermaterai</label>
                    <input type="file" name="file" class="form-control-file" id="pakta_integritas_kades">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_pakta_integritas_kades_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Pakta Integritas 3 Orang (Kepala Desa, BPD, Ketua Pelaksana Kegiatan)</label>
                    <input type="file" name="file" class="form-control-file" id="pakta_integritas_3_orang">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_pakta_integritas_3_orang_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Proposal dengan Rencana Anggaran BOP maksimal 2,5%</label>
                    <input type="file" name="file" class="form-control-file" id="proposal_rencana_anggaran">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_proposal_rencana_anggaran_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Peraturan Desa tentang APBDesa</label>
                    <input type="file" name="file" class="form-control-file" id="apbdes">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_apbdes_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Foto Copy Rekening Kas Desa</label>
                    <input type="file" name="file" class="form-control-file" id="fc_rek_kas_desa">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a target="_blank" id="file_fc_rek_kas_desa_existing"></a></div>
                </div>
                <div><small>Tipe file adalah .pdf dengan maksimal ukuran 1 Mb.</small></div>
                <div class="form-check form-switch">
                    <input class="form-check-input" value="1" type="checkbox" id="status_pencairan" onclick="set_keterangan(this);" <?php echo $disabled; ?>>
                    <label class="form-check-label" for="status_pencairan">Setujui Pencairan</label>
                </div>
                <div class="form-group" style="display:none;">
                    <label>Keterangan status pencairan</label>
                    <textarea class="form-control" id="keterangan_status_pencairan" <?php echo $disabled; ?>></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="submitTambahDataFormPencairanBKK(this);" class="btn btn-primary send_data">Kirim</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        get_data_pencairan_bkk();
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
                    'jenis_belanja': 'bkk'
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        get_data_pencairan_bkk();
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
                    jQuery('#modalTambahDataPencairanBKK .send_data').attr('tipe_verifikasi', 'verikasi_admin').show();
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
                return alert('Keterangan status pencairan tidak boleh kosong!');
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
                        'jenis_belanja': 'bkk'
                    },
                    dataType: 'json',
                    success: function(response) {
                        jQuery('#wrap-loading').hide();
                        if (response.status == 'success') {
                            jQuery('#modalTambahDataPencairanBKK').modal('hide');
                            get_data_pencairan_bkk();
                        } else {
                            alert(`GAGAL! \n${response.message}`);
                        }
                    }
                });
            }
        }
    <?php endif; ?>

    function get_data_pencairan_bkk() {
        if (typeof datapencairan_bkk == 'undefined') {
            window.datapencairan_bkk = jQuery('#management_data_table').on('preXhr.dt', function(e, settings, data) {
                jQuery("#wrap-loading").show();
            }).DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'action': 'get_datatable_data_pencairan_bkk',
                        'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                        'tahun_anggaran': '<?php echo $input['tahun_anggaran']; ?>',
                        'id_skpd': '<?php echo $input['id_skpd']; ?>'
                    }
                },
                lengthMenu: [
                    [20, 50, 100, -1],
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
                        "data": 'kegiatan',
                        className: "text-center"
                    },
                    {
                        "data": 'alamat',
                        className: "text-center"
                    },
                    {
                        "data": 'total_pencairan',
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
            datapencairan_bkk.draw();
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
                    'action': 'hapus_data_pencairan_bkk_by_id',
                    'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                    'id': id
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        get_data_pencairan_bkk();
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    function edit_data(_id) {
        jQuery('#wrap-loading').show();
        jQuery('#modalTambahDataPencairanBKK .send_data').attr('tipe_verifikasi', '');
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_data_pencairan_bkk_by_id',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'id': _id,
            },
            success: function(res) {
                if (res.status == 'success') {
                    jQuery('#id_data').val(res.data.id).prop('disabled', false);
                    jQuery('#tahun').val(res.data.tahun_anggaran).prop('disabled', false);
                    get_bkk()
                    .then(function() {
                        jQuery('#kec').val(res.data.kecamatan).trigger('change').prop('disabled', false);
                        jQuery('#desa').val(res.data.desa).trigger('change').prop('disabled', false);
                        jQuery('#uraian_kegiatan').val(res.data.kegiatan).trigger('change').prop('disabled', false);
                        jQuery('#id_kegiatan').val(res.data.id_kegiatan).prop('disabled', false);
                        jQuery('#alamat').val(res.data.alamat).trigger('change').prop('disabled', false);
                        get_pagu()
                        .then(function() {
                            jQuery('#pagu_anggaran').val(res.data.total_pencairan).prop('disabled', false);
                            if (res.data.status_ver_proposal == 0) {
                                jQuery('#keterangan_status_pencairan').closest('.form-group').show().prop('disabled', false);
                                jQuery('#status_pencairan').prop('checked', false);
                            } else {
                                jQuery('#keterangan_status_pencairan').closest('.form-group').hide().prop('disabled', false);
                                jQuery('#status_pencairan').prop('checked', true);
                            }

                            jQuery('#file_nota_dinas_existing').attr('href', global_file_upload + res.data.file_nota_dinas).html(res.data.file_nota_dinas);
                            jQuery('#nota_dinas').val('').show();
                            jQuery('#file_sptj_existing').attr('href', global_file_upload + res.data.file_sptj).html(res.data.file_sptj);
                            jQuery('#sptj').val('').show();
                            jQuery('#file_pakta_integritas_existing').attr('href', global_file_upload + res.data.file_pakta_integritas).html(res.data.file_pakta_integritas);
                            jQuery('#pakta_integritas').val('').show();
                            jQuery('#file_permohonan_transfer_existing').attr('href', global_file_upload + res.data.file_permohonan_transfer).html(res.data.file_permohonan_transfer);
                            jQuery('#permohonan_transfer').val('').show();
                            jQuery('#file_verifikasi_rekomendasi_existing').attr('href', global_file_upload + res.data.file_verifikasi_rekomendasi).html(res.data.file_verifikasi_rekomendasi);
                            jQuery('#verifikasi_rekomendasi').val('').show();
                            jQuery('#file_permohonan_penyaluran_kades_existing').attr('href', global_file_upload + res.data.file_permohonan_penyaluran_kades).html(res.data.file_permohonan_penyaluran_kades);
                            jQuery('#permohonan_penyaluran_kades').val('').show();
                            jQuery('#file_sptj_kades_existing').attr('href', global_file_upload + res.data.file_sptj_kades).html(res.data.file_sptj_kades);
                            jQuery('#sptj_kades').val('').show();
                            jQuery('#file_pakta_integritas_kades_existing').attr('href', global_file_upload + res.data.file_pakta_integritas_kades).html(res.data.file_pakta_integritas_kades);
                            jQuery('#pakta_integritas_kades').val('').show();
                            jQuery('#file_pakta_integritas_3_orang_existing').attr('href', global_file_upload + res.data.file_pakta_integritas_3_orang).html(res.data.file_pakta_integritas_3_orang);
                            jQuery('#pakta_integritas_3_orang').val('').show();
                            jQuery('#file_proposal_rencana_anggaran_existing').attr('href', global_file_upload + res.data.file_proposal_rencana_anggaran).html(res.data.file_proposal_rencana_anggaran);
                            jQuery('#proposal_rencana_anggaran').val('').show();
                            jQuery('#file_apbdes_existing').attr('href', global_file_upload + res.data.file_apbdes).html(res.data.file_apbdes);
                            jQuery('#apbdes').val('').show();
                            jQuery('#file_fc_rek_kas_desa_existing').attr('href', global_file_upload + res.data.file_fc_rek_kas_desa).html(res.data.file_fc_rek_kas_desa);
                            jQuery('#fc_rek_kas_desa').val('').show();

                            jQuery('#keterangan_status_pencairan').val(res.data.ket_ver_proposal).prop('disabled', false);
                            jQuery('#status_pencairan').closest('.form-check').show().prop('disabled', false);
                            jQuery('#modalTambahDataPencairanBKK .send_data').show();
                            jQuery('#modalTambahDataPencairanBKK').modal('show');
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
            jQuery('#modalTambahDataPencairanBKK .send_data').attr('tipe_verifikasi', '');
            jQuery.ajax({
                method: 'post',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: {
                    'action': 'get_data_pencairan_bkk_by_id',
                    'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                    'id': _id,
                },
                success: function(res) {
                    if (res.status == 'success') {
                        jQuery('#id_data').val(res.data.id);
                        jQuery('#tahun').val(res.data.tahun_anggaran).prop('disabled', true);
                        get_bkk()
                        .then(function() {
                            jQuery('#kec').val(res.data.kecamatan).trigger('change').prop('disabled', true);
                            jQuery('#desa').val(res.data.desa).trigger('change').prop('disabled', true);
                            jQuery('#uraian_kegiatan').val(res.data.kegiatan).trigger('change').prop('disabled', true);
                            jQuery('#id_kegiatan').val(res.data.id_kegiatan).prop('disabled', true);
                            jQuery('#alamat').val(res.data.alamat).prop('disabled', true);
                            get_pagu()
                            .then(function(){
                                jQuery('#pagu_anggaran').val(res.data.total_pencairan).prop('disabled', true);
                                if (res.data.status_ver_proposal == 0) {
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
                                jQuery('#file_verifikasi_rekomendasi_existing').attr('href', global_file_upload + res.data.file_verifikasi_rekomendasi).html(res.data.file_verifikasi_rekomendasi);
                                jQuery('#verifikasi_rekomendasi').val('').hide();
                                jQuery('#file_permohonan_penyaluran_kades_existing').attr('href', global_file_upload + res.data.file_permohonan_penyaluran_kades).html(res.data.file_permohonan_penyaluran_kades);
                                jQuery('#permohonan_penyaluran_kades').val('').hide();
                                jQuery('#file_sptj_kades_existing').attr('href', global_file_upload + res.data.file_sptj_kades).html(res.data.file_sptj_kades);
                                jQuery('#sptj_kades').val('').hide();
                                jQuery('#file_pakta_integritas_kades_existing').attr('href', global_file_upload + res.data.file_pakta_integritas_kades).html(res.data.file_pakta_integritas_kades);
                                jQuery('#pakta_integritas_kades').val('').hide();
                                jQuery('#file_pakta_integritas_3_orang_existing').attr('href', global_file_upload + res.data.file_pakta_integritas_3_orang).html(res.data.file_pakta_integritas_3_orang);
                                jQuery('#pakta_integritas_3_orang').val('').hide();
                                jQuery('#file_proposal_rencana_anggaran_existing').attr('href', global_file_upload + res.data.file_proposal_rencana_anggaran).html(res.data.file_proposal_rencana_anggaran);
                                jQuery('#proposal_rencana_anggaran').val('').hide();
                                jQuery('#file_apbdes_existing').attr('href', global_file_upload + res.data.file_apbdes).html(res.data.file_apbdes);
                                jQuery('#apbdes').val('').hide();
                                jQuery('#file_fc_rek_kas_desa_existing').attr('href', global_file_upload + res.data.file_fc_rek_kas_desa).html(res.data.file_fc_rek_kas_desa);
                                jQuery('#fc_rek_kas_desa').val('').hide();

                                jQuery('#keterangan_status_pencairan').val(res.data.ket_ver_proposal).prop('disabled', true);
                                jQuery('#status_pencairan').closest('.form-check').show().prop('disabled', true);
                                jQuery('#modalTambahDataPencairanBKK .send_data').hide();
                                jQuery('#modalTambahDataPencairanBKK').modal('show');
                                resolve(true);
                            });
                        });
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
    function tambah_data_pencairan_bkk() {
        jQuery('#modalTambahDataPencairanBKK .send_data').attr('tipe_verifikasi', '');
        jQuery('#id_data').val('').prop('disabled', false);
        jQuery('#tahun').val('<?php echo $input['tahun_anggaran']; ?>').prop('disabled', false);
        new Promise(function(resolve, reject) {
                if ('<?php echo $input['tahun_anggaran']; ?>' != '') {
                    get_bkk().then(function() {
                        resolve();
                    });
                } else {
                    resolve();
                }
            })
            .then(function() {
                jQuery('#kec').val('').prop('disabled', false);
                jQuery('#desa').val('').prop('disabled', false);
                jQuery('#uraian_kegiatan').val('').prop('disabled', false);
                jQuery('#alamat').val('').prop('disabled', false);
                jQuery('#validasi_pagu').html('');
                jQuery('#pagu_anggaran').val('').prop('disabled', false);
                jQuery('#status_pencairan').closest('.form-check').hide().prop('disabled', false);
                jQuery('#keterangan_status_pencairan').closest('.form-group').hide().prop('disabled', false);
                jQuery('#status_pencairan').prop('checked', false);
                jQuery('#keterangan_status_pencairan').val('').prop('disabled', false);
                jQuery('#modalTambahDataPencairanBKK .send_data').show();
                jQuery('#modalTambahDataPencairanBKK').modal('show');
            });
    }

    function submitTambahDataFormPencairanBKK(that) {
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
        var alamat = jQuery('#alamat').val();
        if (alamat == '') {
            return alert('Pilih Alamat Dulu!');
        }
        var id_kegiatan = jQuery('#id_kegiatan').val();
        if (id_kegiatan == '') {
            return alert('Pilih Kegiatan Dulu!');
        }
        var pagu_anggaran = jQuery('#pagu_anggaran').val();
        if (pagu_anggaran == '') {
            return alert('Pilih Pagu Anggaran Dulu!');
        }
        var nota_dinas = jQuery('#nota_dinas')[0].files[0];;
        if (typeof nota_dinas == 'undefined') {
            return alert('Upload file nota dinas beserta kelengkapan dulu!');
        }
        var sptj = jQuery('#sptj')[0].files[0];;
        if (typeof sptj == 'undefined') {
            return alert('Upload file SPTJ dulu!');
        }
        var pakta_integritas = jQuery('#pakta_integritas')[0].files[0];;
        if (typeof pakta_integritas == 'undefined') {
            return alert('Upload file Pakta Integritas dulu!');
        }
        var permohonan_transfer = jQuery('#permohonan_transfer')[0].files[0];;
        if (typeof permohonan_transfer == 'undefined') {
            return alert('Upload file permohonan transfer dulu!');
        }
        var verifikasi_rekomendasi = jQuery('#verifikasi_rekomendasi')[0].files[0];;
        if (typeof verifikasi_rekomendasi == 'undefined') {
            return alert('Upload file verifikasi rekomendasi dulu!');
        }
        var permohonan_penyaluran_kades = jQuery('#permohonan_penyaluran_kades')[0].files[0];;
        if (typeof permohonan_penyaluran_kades == 'undefined') {
            return alert('Upload file permohonan penyaluran kepala desa dulu!');
        }
        var sptj_kades = jQuery('#sptj_kades')[0].files[0];;
        if (typeof sptj_kades == 'undefined') {
            return alert('Upload file surat pernyataan tanggung jawab kepala desa dulu!');
        }
        var pakta_integritas_kades = jQuery('#pakta_integritas_kades')[0].files[0];;
        if (typeof pakta_integritas_kades == 'undefined') {
            return alert('Upload file pakta integritas kepala desa dulu!');
        }
        var pakta_integritas_3_orang = jQuery('#pakta_integritas_3_orang')[0].files[0];;
        if (typeof pakta_integritas_3_orang == 'undefined') {
            return alert('Upload file pakta integritas 3 orang dulu!');
        }
        var proposal_rencana_anggaran = jQuery('#proposal_rencana_anggaran')[0].files[0];;
        if (typeof proposal_rencana_anggaran == 'undefined') {
            return alert('Upload file Proposal dengan Rencana Anggaran BOP maksimal 2,5% dulu!');
        }
        var apbdes = jQuery('#apbdes')[0].files[0];;
        if (typeof apbdes == 'undefined') {
            return alert('Upload file Peraturan Desa tentang APBDes dulu!');
        }
        var fc_rek_kas_desa = jQuery('#fc_rek_kas_desa')[0].files[0];;
        if (typeof fc_rek_kas_desa == 'undefined') {
            return alert('Upload file rekening kas desa dulu!');
        }
        var status_pencairan = jQuery('#status_pencairan').val();
        if (jQuery('#status_pencairan').is(':checked') == false) {
            status_pencairan = 0;
        }
        var keterangan_status_pencairan = jQuery('#keterangan_status_pencairan').val();

        let tempData = new FormData();
        tempData.append('action', 'tambah_data_pencairan_bkk');
        tempData.append('api_key', '<?php echo get_option('_crb_api_key_extension'); ?>');
        tempData.append('id_data', id_data);
        tempData.append('id_kegiatan', id_kegiatan);
        tempData.append('pagu_anggaran', pagu_anggaran);
        tempData.append('status_pencairan', status_pencairan);
        tempData.append('keterangan_status_pencairan', keterangan_status_pencairan);
        tempData.append('nota_dinas', nota_dinas);
        tempData.append('sptj', sptj);
        tempData.append('pakta_integritas', pakta_integritas);
        tempData.append('permohonan_transfer', permohonan_transfer);
        tempData.append('verifikasi_rekomendasi', verifikasi_rekomendasi);
        tempData.append('permohonan_penyaluran_kades', permohonan_penyaluran_kades);
        tempData.append('sptj_kades', sptj_kades);
        tempData.append('pakta_integritas_kades', pakta_integritas_kades);
        tempData.append('pakta_integritas_3_orang', pakta_integritas_3_orang);
        tempData.append('proposal_rencana_anggaran', proposal_rencana_anggaran);
        tempData.append('apbdes', apbdes);
        tempData.append('fc_rek_kas_desa', fc_rek_kas_desa)

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
                    jQuery('#modalTambahDataPencairanBKK').modal('hide');
                    get_data_pencairan_bkk();
                } else {
                    jQuery('#wrap-loading').hide();
                }
            }
        });
    }

    function get_bkk() {
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
                        'action': "get_pemdes_bkk",
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
                                kecamatan_all[b.kecamatan][b.desa] = {};
                            }
                            if (!kecamatan_all[b.kecamatan][b.desa][b.kegiatan]) {
                                kecamatan_all[b.kecamatan][b.desa][b.kegiatan] = {};
                            }
                            if (!kecamatan_all[b.kecamatan][b.desa][b.kegiatan][b.alamat]) {
                                kecamatan_all[b.kecamatan][b.desa][b.kegiatan][b.alamat] = [];
                            }
                            kecamatan_all[b.kecamatan][b.desa][b.kegiatan][b.alamat].push(b);
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

    function get_kegiatan() {
        var kec = jQuery('#kec').val();
        if (kec == '' || kec == '-1') {
            return alert('Pilih kecamatan dulu!');
        }
        var desa = jQuery('#desa').val();
        if (desa == '' || desa == '-1') {
            return alert('Pilih desa dulu!');
        }
        var kegiatan = '<option value="-1">Pilih Kegiatan</option>';
        for (var iii in kecamatan_all[kec][desa]) {
            kegiatan += '<option value="' + iii + '">' + iii + '</option>';
        }
        jQuery('#uraian_kegiatan').html(kegiatan);
    }

    function get_alamat() {
        var kec = jQuery('#kec').val();
        if (kec == '' || kec == '-1') {
            return alert('Pilih kecamatan dulu!');
        }
        var desa = jQuery('#desa').val();
        if (desa == '' || desa == '-1') {
            return alert('Pilih desa dulu!');
        }
        var kegiatan = jQuery('#uraian_kegiatan').val();
        if (kegiatan == '' || kegiatan == '-1') {
            return alert('Pilih kegiatan dulu!');
        }
        var alamat = '<option value="-1">Pilih alamat</option>';
        for (var iiii in kecamatan_all[kec][desa][kegiatan]) {
            alamat += '<option value="' + iiii + '">' + iiii + '</option>';
        }
        jQuery('#alamat').html(alamat);
    }

    function get_pagu() {
        return new Promise(function(resolve, reject){
            var kec = jQuery('#kec').val();
            if (kec == '' || kec == '-1') {
                return alert('Pilih kecamatan dulu!');
            }
            var desa = jQuery('#desa').val();
            if (desa == '' || desa == '-1') {
                return alert('Pilih desa dulu!');
            }
            var kegiatan = jQuery('#uraian_kegiatan').val();
            if (kegiatan == '' || kegiatan == '-1') {
                return alert('Pilih kegiatan dulu!');
            }
            var alamat = jQuery('#alamat').val();
            if (alamat == '' || alamat == '-1') {
                return alert('Pilih alamat dulu!');
            }
            jQuery('#wrap-loading').show();
            var pagu = kecamatan_all[kec][desa][kegiatan][alamat][0].total;
            var id = kecamatan_all[kec][desa][kegiatan][alamat][0].id;
            var tahun = kecamatan_all[kec][desa][kegiatan][alamat][0].tahun_anggaran;
            jQuery.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "post",
                data: {
                    'action': "get_pencairan_pemdes_bkk",
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
                        jQuery('#validasi_pagu').html(tbody);
                        jQuery('#id_kegiatan').val(id);
                        jQuery('#pagu_anggaran').val(global_sisa);
                    } else {
                        alert(response.message);
                    }
                    jQuery('#wrap-loading').hide();
                    resolve();
                }
            });
        });
    }

    function validasi_pagu() {
        var pagu = jQuery('#pagu_anggaran').val();
        if (pagu > global_sisa) {
            alert('Nilai pencairan tidak boleh lebih besar dari sisa anggaran!');
            jQuery('#pagu_anggaran').val(global_sisa);
        }
    }
</script>