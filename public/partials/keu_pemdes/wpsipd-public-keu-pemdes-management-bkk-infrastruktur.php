<?php
global $wpdb;

$input = shortcode_atts(array(
    'tahun_anggaran' => ''
), $atts);

$tahun_anggaran = isset($_GET['tahun_anggaran']) ? $wpdb->prepare('%d', $_GET['tahun_anggaran']) : '';

if (!empty($tahun_anggaran)) {
    $input['tahun_anggaran'] = $tahun_anggaran;
}

$idtahun = $wpdb->get_results("SELECT DISTINCT tahun_anggaran FROM data_unit", ARRAY_A);
$tahun_options = "<option value='-1'>Pilih Tahun</option>";

foreach ($idtahun as $val) {
    $selected = ($val['tahun_anggaran'] == $tahun_anggaran) ? 'selected' : '';
    $tahun_options .= "<option value='{$val['tahun_anggaran']}' $selected>{$val['tahun_anggaran']}</option>";
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
        <h1 class="text-center" style="margin:3rem;">Manajemen Data BKK Infrastruktur</h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_data_bkk();"><i class="dashicons dashicons-plus"></i> Tambah Data</button>
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
                        <th class="text-center">Total</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade mt-4" id="modalTambahDataBKK" tabindex="-1" role="dialog" aria-labelledby="modalTambahDataBKKLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataBKKLabel">Data BKK</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type='hidden' id='id_data' name="id_data" placeholder=''>
                <div class="form-group">
                    <label>Tahun Anggaran</label>
                    <select class="form-control" id="tahun_anggaran" onchange="get_kecamatan();">
                        <?php echo $tahun_options ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kecamatan</label>
                    <select class="form-control" id="kec" onchange="get_desa();">
                    </select>
                </div>
                <div class="form-group">
                    <label>Desa</label>
                    <select class="form-control" id="desa">
                    </select>
                </div>
                <div class="form-group">
                    <label>Kegiatan</label>
                    <input type="text" class="form-control" id="kegiatan" />
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" class="form-control" id="alamat" />
                </div>
                <div class="form-group">
                    <label>Sumber Dana</label>
                    <select class="form-control" id="sumber_dana">
                    </select>
                </div>
                <div class="form-group">
                    <label for='total' style='display:inline-block'>Total</label>
                    <input type="text" id='total' name="total" class="form-control" placeholder='' />
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary submitBtn" onclick="submitTambahDataFormBKK()">Simpan</button>
                <button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        get_data_bkk();
        window.alamat_global = {};
    });

    function get_data_bkk() {
        if (typeof databkk == 'undefined') {
            window.databkk = jQuery('#management_data_table').on('preXhr.dt', function(e, settings, data) {
                jQuery("#wrap-loading").show();
            }).DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'action': 'get_datatable_bkk_infrastruktur',
                        'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
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
                        "data": 'total',
                        className: "text-right"
                    },
                    {
                        "data": 'aksi',
                        className: "text-center"
                    }
                ]
            });
        } else {
            databkk.draw();
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
                    'action': 'hapus_data_bkk_infrastruktur_by_id',
                    'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                    'id': id
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        get_data_bkk();
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
                'action': 'get_data_bkk_infrastruktur_by_id',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'id': _id,
            },
            success: function(res) {
                if (res.status == 'success') {
                    jQuery('#id_data').val(res.data.id);
                    jQuery('#id_kecamatan').val(res.data.id_kecamatan);
                    jQuery('#id_desa').val(res.data.id_desa);
                    jQuery('#kecamatan').val(res.data.kecamatan);
                    jQuery('#desa').val(res.data.desa);
                    jQuery('#total').val(res.data.total);
                    jQuery('#id_dana').val(res.data.id_dana);
                    jQuery('#sumber_dana').val(res.data.sumber_dana);
                    jQuery('#alamat').val(res.data.alamat);
                    jQuery('#kegiatan').val(res.data.kegiatan);
                    jQuery('#modalTambahDataBKK').modal('show');
                } else {
                    alert(res.message);
                }
                jQuery('#wrap-loading').hide();
            }
        });
    }

    //show tambah data
    function tambah_data_bkk() {
        jQuery('#id_data').val('');
        jQuery('#id_kecamatan').val('');
        jQuery('#id_desa').val('');
        jQuery('#id_dana').val('');
        jQuery('#sumber_dana').val('');
        jQuery('#kecamatan').val('');
        jQuery('#desa').val('');
        jQuery('#total').val('');
        jQuery('#tahun_anggaran').val('');
        jQuery('#modalTambahDataBKK').modal('show');
    }

    function submitTambahDataFormBKK() {
        var id_data = jQuery('#id_data').val();

        var id_kel = jQuery('#desa').val();
        if (id_kel == '') {
            return alert('Data desa tidak boleh kosong!');
        }
        var desa = jQuery("#desa option:selected").text();

        var id_kec = jQuery('#kec').val();
        if (id_kec == '') {
            return alert('Data kecamatan tidak boleh kosong!');
        }
        var kecamatan = jQuery("#kec option:selected").text();

        var kegiatan = jQuery('#kegiatan').val();
        if (kegiatan == '') {
            return alert('Data kegiatan tidak boleh kosong!');
        }
        var alamat = jQuery('#alamat').val();
        if (alamat == '') {
            return alert('Data alamat tidak boleh kosong!');
        }
        var id_dana = jQuery('#sumber_dana').val();
        if (id_dana == '') {
            return alert('Data sumber_dana tidak boleh kosong!');
        }
        var sumber_dana = jQuery("#sumber_dana option:selected").text();

        var total = jQuery('#total').val();
        if (total == '') {
            return alert('Data total tidak boleh kosong!');
        }

        var tahun_anggaran = jQuery('#tahun_anggaran').val();
        if (tahun_anggaran == '') {
            return alert('Data tahun anggaran tidak tidak boleh kosong!');
        }

        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'tambah_data_bkk_infrastruktur',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'id_data': id_data,
                'id_kec': id_kec,
                'id_desa': id_kel,
                'kecamatan': kecamatan,
                'desa': desa,
                'kegiatan': kegiatan,
                'alamat': alamat,
                'id_dana': id_dana,
                'sumber_dana': sumber_dana,
                'total': total,
                'tahun_anggaran': tahun_anggaran,
            },
            success: function(res) {
                alert(res.message);
                if (res.status == 'success') {
                    jQuery('#modalTambahDataBKK').modal('hide');
                    get_data_bkk();
                } else {
                    jQuery('#wrap-loading').hide();
                }
            }
        });
    }

    function get_sumber_dana(tahun){
        if(typeof window.sumber_dana_global == 'undefined'){
            window.sumber_dana_global = {};
        }
        new Promise(function(resolve, reject) {
            if (!sumber_dana_global[tahun]) {
                jQuery('#wrap-loading').show();
                jQuery.ajax({
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    type: "post",
                    data: {
                        'action': "get_sumber_dana_desa",
                        'api_key': jQuery("#api_key").val(),
                        'tahun': tahun
                    },
                    dataType: "json",
                    success: function(response) {
                        sumber_dana_global[tahun] = response.data;
                        resolve();
                    }
                });
            } else {
                resolve();
            }
        })
        .then(function() {
            var html = '<option value="-1">Pilih Sumber Dana</option>';
            sumber_dana_global[tahun].map(function(b, i){
                html += '<option value="' + b.id_dana + '">' + b.kode_dana+' ' +b.nama_dana+ '</option>';
            });
            jQuery('#sumber_dana').html(html);
        });
    }

    function get_kecamatan() {
        var tahun = jQuery('#tahun_anggaran').val();
        if (tahun == '' || tahun == '-1') {
            return alert('Pilih tahun anggaran dulu!');
        }
        get_sumber_dana(tahun);
        new Promise(function(resolve, reject) {
            if (!alamat_global[tahun]) {
                jQuery('#wrap-loading').show();
                jQuery.ajax({
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    type: "post",
                    data: {
                        'action': "get_pemdes_alamat",
                        'api_key': jQuery("#api_key").val(),
                        'tahun': tahun
                    },
                    dataType: "json",
                    success: function(response) {
                        alamat_global[tahun] = response.data;
                        resolve();
                    }
                });
            } else {
                resolve();
            }
        })
        .then(function() {
            window.kecamatan_all = {};
            alamat_global[tahun].map(function(b, i) {
                kecamatan_all[b.id_kec] = {
                    nama: b.kecamatan,
                    data: b.desa
                };
            });
            var kecamatan = '<option value="-1">Pilih Kecamatan</option>';
            for (var i in kecamatan_all) {
                kecamatan += '<option value="' + i + '">' + kecamatan_all[i].nama + '</option>';
            }
            jQuery('#kec').html(kecamatan);
            jQuery('#wrap-loading').hide();
        });
    }

    function get_desa() {
        var kec = jQuery('#kec').val();
        if (kec == '' || kec == '-1') {
            return alert('Pilih kecamatan dulu!');
        }
        var desa = '<option value="-1">Pilih Desa</option>';
        var desaData = kecamatan_all[kec].data;

        for (var i in desaData) {
            desa += '<option value="' + desaData[i].id_kel + '">' + desaData[i].desa + '</option>';
        }
        jQuery('#desa').html(desa);
    }


</script>