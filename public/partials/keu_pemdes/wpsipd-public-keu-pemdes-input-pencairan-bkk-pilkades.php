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
        <h1 class="text-center" style="margin:3rem;">Pencairan BKK Pemilihan Kepala Desa</h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_data_pencairan_bkk_pilkades();"><i class="dashicons dashicons-plus"></i> Tambah Data</button>
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

<div class="modal fade mt-4" id="modalTambahDataPencairanBKKPilkades" tabindex="-1" role="dialog" aria-labelledby="modalTambahDataPencairanBKKPilkadesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataPencairanBKKPilkadesLabel">Data Pencairan BKK Pemilihan Kepala Desa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type='hidden' id='id_data' name="id_data" placeholder=''>
                <div class="form-group">
                    <label>Tahun Anggaran</label>
                    <select class="form-control" id="tahun" onchange="get_bkk_pilkades();">
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
                        <input type="hidden" class="form-control" id="id_bkk_pilkades" />
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
                    <input type="number" class="form-control" id="pagu_anggaran" onchange="validasi_pagu();" />
                </div>
                <div class="form-group">
                    <label for="">Keterangan Pencairan</label>
                    <textarea class="form-control" id="keterangan"></textarea>
                </div>
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
                <button type="submit" onclick="submitTambahDataFormPencairanBKKPilkades(this);" class="btn btn-primary send_data">Kirim</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        get_data_pencairan_bkk_pilkades();
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
                    'jenis_belanja': 'bkk_pilkades'
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        get_data_pencairan_bkk_pilkades();
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
                    jQuery('#modalTambahDataPencairanBKKPilkades .send_data').attr('tipe_verifikasi', 'verikasi_admin').show();
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
                        'jenis_belanja': 'bkk_pilkades'
                    },
                    dataType: 'json',
                    success: function(response) {
                        jQuery('#wrap-loading').hide();
                        if (response.status == 'success') {
                            jQuery('#modalTambahDataPencairanBKKPilkades').modal('hide');
                            get_data_pencairan_bkk_pilkades();
                        } else {
                            alert(`GAGAL! \n${response.message}`);
                        }
                    }
                });
            }
        }
    <?php endif; ?>

    function get_data_pencairan_bkk_pilkades() {
        if (typeof datapencairan_bkk_pilkades == 'undefined') {
            window.datapencairan_bkk_pilkades = jQuery('#management_data_table').on('preXhr.dt', function(e, settings, data) {
                jQuery("#wrap-loading").show();
            }).DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'action': 'get_datatable_data_pencairan_bkk_pilkades',
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
                        "data": 'total_pencairan',
                        className: "text-center"
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
            datapencairan_bkk_pilkades.draw();
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
                    'action': 'hapus_data_pencairan_bkk_pilkades_by_id',
                    'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                    'id': id
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        get_data_pencairan_bkk_pilkades();
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    function edit_data(_id) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_data_pencairan_bkk_pilkades_by_id',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'id': _id,
            },
            success: function(res) {
                if (res.status == 'success') {
                    jQuery('#id_data').val(res.data.id).prop('disabled', false);
                    jQuery('#tahun').val(res.data.tahun_anggaran).prop('disabled', false);
                    get_bkk_pilkades()
                        .then(function() {
                            jQuery('#kec').val(res.data.kecamatan).trigger('change').prop('disabled', false);
                            jQuery('#desa').val(res.data.desa).prop('disabled', false);
                            jQuery('#id_bkk_pilkades').val(res.data.id_bkk_pilkades).prop('disabled', false);
                            jQuery('#validasi_pagu').closest('.form-group').show().prop('disabled', false);
                            get_pagu()
                            .then(function(){
                                jQuery('#pagu_anggaran').val(res.data.total_pencairan).prop('disabled', false);
                                if (res.data.status_ver_total == 0) {
                                    jQuery('#keterangan_status_pencairan').closest('.form-group').show().prop('disabled', false);
                                    jQuery('#status_pencairan').prop('checked', false);
                                } else {
                                    jQuery('#keterangan_status_pencairan').closest('.form-group').hide().prop('disabled', false);
                                    jQuery('#status_pencairan').prop('checked', true);
                                }
                                jQuery('#keterangan_status_pencairan').val(res.data.ket_ver_total).prop('disabled', false);
                                jQuery('#keterangan').val(res.data.keterangan).prop('disabled', false);
                                jQuery('#status_pencairan').closest('.form-check').show().prop('disabled', false);
                                jQuery('#modalTambahDataPencairanBKKPilkades .send_data').show();
                                jQuery('#modalTambahDataPencairanBKKPilkades').modal('show');
                            })
                        })
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
            jQuery('#modalTambahDataPencairanBKKPilkades .send_data').attr('tipe_verifikasi', '');
            jQuery.ajax({
                method: 'post',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: {
                    'action': 'get_data_pencairan_bkk_pilkades_by_id',
                    'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                    'id': _id,
                },
                success: function(res) {
                    if (res.status == 'success') {
                        jQuery('#id_data').val(res.data.id).prop('disabled', true);
                        jQuery('#tahun').val(res.data.tahun_anggaran).prop('disabled', true);
                        get_bkk_pilkades()
                            .then(function() {
                                jQuery('#kec').val(res.data.kecamatan).trigger('change').prop('disabled', true);
                                jQuery('#desa').val(res.data.desa).trigger('change').prop('disabled', true);
                                jQuery('#uraian_kegiatan').val(res.data.kegiatan).trigger('change').prop('disabled', true);
                                jQuery('#id_bkk_pilkades').val(res.data.id_bkk_pilkades).prop('disabled', true);
                                jQuery('#alamat').val(res.data.alamat).prop('disabled', true);
                                get_pagu()
                                .then(function(){
                                    jQuery('#pagu_anggaran').val(res.data.total_pencairan).prop('disabled', true);
                                    if (res.data.status_ver_total == 0) {
                                        jQuery('#keterangan_status_pencairan').closest('.form-group').show().prop('disabled', true);
                                        jQuery('#status_pencairan').prop('checked', false).prop('disabled', true);
                                    } else {
                                        jQuery('#keterangan_status_pencairan').closest('.form-group').hide().prop('disabled', true);
                                        jQuery('#status_pencairan').prop('checked', true).prop('disabled', true);
                                    }
                                    jQuery('#keterangan_status_pencairan').val(res.data.ket_ver_total).prop('disabled', true);
                                    jQuery('#keterangan').val(res.data.keterangan).prop('disabled', true);
                                    jQuery('#status_pencairan').closest('.form-check').show().prop('disabled', true);
                                    jQuery('#modalTambahDataPencairanBKKPilkades .send_data').hide();
                                    jQuery('#modalTambahDataPencairanBKKPilkades').modal('show');
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
    function tambah_data_pencairan_bkk_pilkades() {
        jQuery('#modalTambahDataPencairanBKKPilkades .send_data').attr('tipe_verifikasi', '');
        jQuery('#id_data').val('').prop('disabled', false);
        jQuery('#tahun').val('<?php echo $input['tahun_anggaran']; ?>').prop('disabled', false);
        new Promise(function(resolve, reject) {
                if ('<?php echo $input['tahun_anggaran']; ?>' != '') {
                    get_bkk_pilkades().then(function() {
                        resolve();
                    });
                } else {
                    resolve();
                }
            })
        jQuery('#id_data').val('').prop('disabled', false);
        jQuery('#tahun').val('').prop('disabled', false);
        jQuery('#kec').val('').prop('disabled', false);
        jQuery('#desa').val('').prop('disabled', false);
        jQuery('#validasi_pagu').html('');
        jQuery('#pagu_anggaran').val('').prop('disabled', false);
        jQuery('#keterangan').val('').prop('disabled', false);
        jQuery('#status_pencairan').closest('.form-check').hide().prop('disabled', false);
        jQuery('#keterangan_status_pencairan').closest('.form-group').hide().prop('disabled', false);
        jQuery('#status_pencairan').prop('checked', false);
        jQuery('#keterangan_status_pencairan').val('').prop('disabled', false);
        jQuery('#modalTambahDataPencairanBKKPilkades .send_data').show();
        jQuery('#modalTambahDataPencairanBKKPilkades').modal('show');
    }

    function submitTambahDataFormPencairanBKKPilkades(that) {
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
        var id_bkk_pilkades = jQuery('#id_bkk_pilkades').val();
        var pagu_anggaran = jQuery('#pagu_anggaran').val();
        if (pagu_anggaran == '') {
            return alert('Pilih Pagu Anggaran Dulu!');
        }
        var status_pencairan = jQuery('#status_pencairan').val();
        if (jQuery('#status_pencairan').is(':checked') == false) {
            status_pencairan = 0;
        }
        var keterangan_status_pencairan = jQuery('#keterangan_status_pencairan').val();
        var keterangan = jQuery('#keterangan').val();
        if (keterangan == '') {
            return alert('Isi keterangan Dulu!');
        }

        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'tambah_data_pencairan_bkk_pilkades',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'id_data': id_data,
                'id_bkk_pilkades': id_bkk_pilkades,
                'pagu_anggaran': pagu_anggaran,
                'status_pencairan': status_pencairan,
                'keterangan_status_pencairan': keterangan_status_pencairan,
                'keterangan': keterangan,
            },
            success: function(res) {
                alert(res.message);
                jQuery('#modalTambahDataPencairanBKKPilkades').modal('hide');
                if (res.status == 'success') {
                    get_data_pencairan_bkk_pilkades();
                } else {
                    jQuery('#wrap-loading').hide();
                }
            }
        });
    }

    function get_bkk_pilkades() {
        return new Promise(function(resolve, reject) {
            var tahun = jQuery('#tahun').val();
            if (tahun == '' || tahun == '-1') {
                alert('Pilih tahun anggaran dulu!');
                return resolve();
            }
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "post",
                data: {
                    'action': "get_pemdes_bkk_pilkades",
                    'api_key': jQuery("#api_key").val(),
                    'tahun_anggaran': tahun,
                },
                dataType: "json",
                success: function(response) {
                    window.data_pemdes = response.data;
                    window.kecamatan_all = {};
                    data_pemdes.map(function(b, i) {
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
                    'action': "get_pencairan_pemdes_bkk_pilkades",
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
                        jQuery('#id_bkk_pilkades').val(id);
                        jQuery('#pagu_anggaran').val(global_sisa);
                    } else {
                        alert(response.message);
                    }
                    jQuery('#wrap-loading').hide();
                    resolve();
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