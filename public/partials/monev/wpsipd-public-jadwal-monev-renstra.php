<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

global $wpdb;

$select_rpd_rpjm = '';

$sqlTipe = $wpdb->get_results(
    $wpdb->prepare("
        SELECT * 
        FROM `data_tipe_perencanaan` 
        WHERE nama_tipe=%s
    ", 'monev_rpjmd'),
    ARRAY_A
);
$data_rpd_rpjm = $wpdb->get_results(
    $wpdb->prepare('
        SELECT *
        FROM data_jadwal_lokal
        WHERE status=0
          AND id_tipe=%d
    ', $sqlTipe[0]['id']),
    ARRAY_A
);

if (!empty($data_rpd_rpjm)) {
    foreach ($data_rpd_rpjm as $val_rpd_rpjm) {
        $tipe = [];
        foreach ($sqlTipe as $val_tipe) {
            $tipe[$val_tipe['id']] = strtoupper($val_tipe['nama_tipe']);
        }
        $select_rpd_rpjm .=
            '<option value="' . $val_rpd_rpjm['id_jadwal_lokal'] . '" selected>
            ' . $tipe[$val_rpd_rpjm['id_tipe']] . ' | ' . $val_rpd_rpjm['nama'] . ' | 
            ' . $val_rpd_rpjm['tahun_anggaran'] . ' - ' . $val_rpd_rpjm['tahun_akhir_anggaran'] .
            '</option>';
    }
}

$body = '';
?>
<style>
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<div class="cetak">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
        <h1 class="text-center" style="margin:3rem;">Jadwal Monev RENSTRA</h1>
        <table id="data_penjadwalan_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead id="data_header">
                <tr>
                    <th class="text-center">Nama Tahapan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Tahun Mulai Anggaran</th>
                    <th class="text-center">Tahun Selesai Anggaran</th>
                    <th class="text-center">Jadwal RPD/RPJM</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="data_body">
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade mt-4" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahJadwalLabel"><span class="dashicons dashicons-plus"></span>Tambah Penjadwalan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="link_rpd_rpjm">Pilih Jadwal RPD atau RPJM</label>
                    <select id="link_rpd_rpjm" class="form-control" disabled>
                        <option value="">Pilih RPD atau RPJM</option>
                        <?php echo $select_rpd_rpjm; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for='tahun_mulai_anggaran'>Tahun Mulai Anggaran</label>
                        <input type="number" id='tahun_mulai_anggaran' name="tahun_mulai_anggaran" class="form-control" placeholder="Tahun Mulai Anggaran" disabled />
                    </div>
                    <div class="form-group col-md-4">
                        <label for='tahun_akhir_anggaran'>Tahun Akhir Anggaran</label>
                        <input type="number" id='tahun_akhir_anggaran' name="tahun_akhir_anggaran" class="form-control" placeholder="Tahun Akhir Anggaran" disabled />
                    </div>
                    <div class="form-group col-md-4">
                        <div class="form-group">
                            <label for='lama_pelaksanaan'>Lama Pelaksanaan</label>
                            <div class="input-group">
                                <input type="number" id="lama_pelaksanaan" name="lama_pelaksanaan" class="form-control" aria-describedby="basic-addon2" disabled>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">Tahun</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="data_monev_renstra">Pilih data Monev Renstra</label>
                    <select id="data_monev_renstra" class="form-control" name="data_monev_renstra">
                        <option value="">Pilih Data</option>
                        <option value="1">SIPD</option>
                        <option value="2">Lokal</option>
                    </select>
                </div>
                <div class="form-group">
                <div class="alert alert-info" role="alert">
                    Pilih data monev renstra untuk menentukan set data yang akan digunakan dalam fitur Monitoring dan Evaluasi Indikator Renstra </div>
                </div>
                <div class="form-group">
                    <label for='jadwal_nama'>Nama Tahapan</label>
                    <input type='text' id='jadwal_nama' class="form-control" placeholder='Nama Tahapan'>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary submitBtn" onclick="submitTambahJadwalForm()">Simpan</button>
                <button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    jQuery(document).ready(function() {
        globalThis.tipePerencanaan = 'monev_renstra'
        globalThis.thisAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>"
        globalThis.tahunAnggaran = "<?php echo get_option('_crb_tahun_anggaran_sipd'); ?>"
        get_data_penjadwalan();
    });

    /** get data penjadwalan */
    function get_data_penjadwalan() {
        jQuery("#wrap-loading").show();
        globalThis.penjadwalanTable = jQuery('#data_penjadwalan_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: thisAjaxUrl,
                type: "post",
                data: {
                    'action': "get_data_penjadwalan",
                    'api_key': jQuery("#api_key").val(),
                    'tipe_perencanaan': tipePerencanaan
                }
            },
            "initComplete": function(settings, json) {
                jQuery("#wrap-loading").hide();
            },
            "columns": [{
                    "data": "nama",
                    className: "text-center"
                },
                {
                    "data": "status",
                    className: "text-center"
                },
                {
                    "data": "tahun_anggaran",
                    className: "text-center"
                },
                {
                    "data": "tahun_anggaran_selesai",
                    className: "text-center"
                },
                {
                    "data": "relasi_perencanaan_renstra",
                    className: "text-center"
                },
                {
                    "data": "aksi",
                    className: "text-center"
                }
            ]
        });
    }

    function submitTambahJadwalForm() {
        jQuery("#wrap-loading").show()
        let nama = jQuery('#jadwal_nama').val()
        let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
        let relasi_perencanaan = jQuery("#link_rpd_rpjm").val()
        let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
        if (nama.trim() == '' || this_tahun_anggaran == '' || this_lama_pelaksanaan == '') {
            jQuery("#wrap-loading").hide()
            alert("Ada yang kosong, Harap diisi semua")
            return false
        } else {
            jQuery.ajax({
                url: thisAjaxUrl,
                type: 'post',
                dataType: 'json',
                data: {
                    'action': 'submit_add_schedule',
                    'api_key': jQuery("#api_key").val(),
                    'nama': nama,
                    'tahun_anggaran': this_tahun_anggaran,
                    'tipe_perencanaan': tipePerencanaan,
                    'relasi_perencanaan': relasi_perencanaan,
                    'lama_pelaksanaan': this_lama_pelaksanaan
                },
                beforeSend: function() {
                    jQuery('.submitBtn').attr('disabled', 'disabled')
                },
                success: function(response) {
                    jQuery('#modalTambahJadwal').modal('hide')
                    jQuery('#wrap-loading').hide()
                    if (response.status == 'success') {
                        alert(response.message)
                        penjadwalanTable.ajax.reload()
                        afterSubmitForm()
                    } else {
                        alert(response.message)
                    }
                    jQuery('#jadwal_nama').val('')
                    jQuery("#tahun_mulai_anggaran").val('')
                    jQuery("#link_rpd_rpjm").val('')
                }
            })
        }
        jQuery('#modalTambahJadwal').modal('hide');
    }

    function edit_data_penjadwalan(id_jadwal_lokal) {
        jQuery('#modalTambahJadwal').modal('show');
        jQuery("#modalTambahJadwal .modal-title").html("Edit Penjadwalan");
        jQuery("#modalTambahJadwal .submitBtn")
            .attr("onclick", 'submitEditJadwalForm(' + id_jadwal_lokal + ')')
            .attr("disabled", false)
            .text("Simpan");
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: thisAjaxUrl,
            type: "post",
            data: {
                'action': "get_data_jadwal_by_id",
                'api_key': jQuery("#api_key").val(),
                'id_jadwal_lokal': id_jadwal_lokal
            },
            dataType: "json",
            success: function(response) {
                jQuery('#wrap-loading').hide();
                jQuery("#jadwal_nama").val(response.data.nama);
                jQuery("#tahun_mulai_anggaran").val(response.data.tahun_anggaran);
                jQuery("#data_monev_renstra").val(response.data.data_monev_renstra);
                jQuery("#lama_pelaksanaan").val(response.data.lama_pelaksanaan);
                jQuery("#link_rpd_rpjm").val(response.data.relasi_perencanaan).change();
            }
        })
    }

    function submitEditJadwalForm(id_jadwal_lokal) {
        jQuery("#wrap-loading").show()
        let nama = jQuery('#jadwal_nama').val()
        let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
        let relasi_perencanaan = jQuery("#link_rpd_rpjm").val()
        let data_monev_renstra = jQuery("#data_monev_renstra").val()
        let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
        if (nama.trim() == '' || this_tahun_anggaran == '' || this_lama_pelaksanaan == '') {
            jQuery("#wrap-loading").hide()
            alert("Ada yang kosong, Harap diisi semua")
            return false
        } else {
            jQuery.ajax({
                url: thisAjaxUrl,
                type: 'post',
                dataType: 'json',
                data: {
                    'action': 'submit_edit_schedule',
                    'api_key': jQuery("#api_key").val(),
                    'nama': nama,
                    'id_jadwal_lokal': id_jadwal_lokal,
                    'tahun_anggaran': this_tahun_anggaran,
                    'tipe_perencanaan': tipePerencanaan,
                    'relasi_perencanaan': relasi_perencanaan,
                    'lama_pelaksanaan': this_lama_pelaksanaan,
                    'data_monev_renstra': data_monev_renstra
                },
                beforeSend: function() {
                    jQuery('.submitBtn').attr('disabled', 'disabled')
                },
                success: function(response) {
                    jQuery('#modalTambahJadwal').modal('hide')
                    jQuery('#wrap-loading').hide()
                    if (response.status == 'success') {
                        alert(response.message)
                        penjadwalanTable.ajax.reload()
                        afterSubmitForm()
                    } else {
                        alert(`GAGAL! \n${response.message}`)
                    }
                    jQuery('#jadwal_nama').val('')
                    jQuery("#tahun_mulai_anggaran").val('')
                    jQuery("#link_rpd_rpjm").val('')
                }
            })
        }
        jQuery('#modalTambahJadwal').modal('hide');
    }

    function afterSubmitForm() {
        jQuery("#jadwal_nama").val("")
        jQuery("#tahun_mulai_anggaran").val("")
        jQuery("#link_rpd_rpjm").val("")
        jQuery("#lama_pelaksanaan").val("")
    }

    function hapus_data_penjadwalan(id_jadwal_lokal) {
        let confirmDelete = confirm("Apakah anda yakin akan menghapus penjadwalan?");
        if (confirmDelete) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: thisAjaxUrl,
                type: 'post',
                data: {
                    'action': 'submit_delete_schedule',
                    'api_key': jQuery("#api_key").val(),
                    'id_jadwal_lokal': id_jadwal_lokal
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        alert(response.message)
                        penjadwalanTable.ajax.reload();
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    function lock_data_penjadwalan(id_jadwal_lokal) {
        return (alert("Sementera kunci jadwal belum bisa dilakukan"));
        let confirmLocked = confirm("Apakah anda yakin akan mengunci penjadwalan?");
        if (confirmLocked) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: thisAjaxUrl,
                type: 'post',
                data: {
                    'action': 'submit_lock_schedule_rpjpd',
                    'api_key': jQuery("#api_key").val(),
                    'id_jadwal_lokal': id_jadwal_lokal
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        alert('Data berhasil dikunci!.');
                        penjadwalanTable.ajax.reload();
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    function report(id_jadwal_lokal) {
        return (alert("Sementera laporan belum bisa dilakukan"));

        all_skpd();
    }

    function all_skpd() {
        return (alert("Sementera laporan belum bisa dilakukan"));
    }
</script>