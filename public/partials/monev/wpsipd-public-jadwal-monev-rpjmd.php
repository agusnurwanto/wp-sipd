<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

global $wpdb;

$body = '';
?>
<style>
    .bulk-action {
        padding: .45rem;
        border-color: #eaeaea;
        vertical-align: middle;
    }
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<div class="cetak">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
        <h1 class="text-center" style="margin:3rem;">Jadwal Monev RPJMD Lokal</h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary tambah_ssh" onclick="tambah_jadwal();">Tambah Jadwal</button>
        </div>
        <table id="data_penjadwalan_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead id="data_header">
                <tr>
                    <th class="text-center">Nama Tahapan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Tahun Mulai Anggaran</th>
                    <th class="text-center">Tahun Selesai Anggaran</th>
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
                <h5 class="modal-title" id="modalTambahJadwalLabel">Tambah Penjadwalan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <label for='jadwal_nama' style='display:inline-block'>Nama Tahapan</label>
                    <input type='text' id='jadwal_nama' style='display:block;width:100%;' placeholder='Nama Tahapan'>
                </div>
                <div>
                    <label for='tahun_mulai_anggaran' style='display:inline-block'>Tahun Mulai Anggaran</label>
                    <input type="number" id='tahun_mulai_anggaran' name="tahun_mulai_anggaran" style='display:block;width:100%;' placeholder="Tahun Mulai Anggaran" />
                </div>
                <div>
                    <label for='lama_pelaksanaan' style='display:inline-block'>Lama Pelaksanaan</label>
                    <input type="number" id='lama_pelaksanaan' name="lama_pelaksanaan" style='display:block;width:100%;' placeholder="Lama Pelaksanaan" />
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
        globalThis.tipePerencanaan = 'monev_rpjmd'
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
                    "data": "aksi",
                    className: "text-center"
                }
            ]
        });
    }

    function tambah_jadwal() {
        jQuery("#modalTambahJadwal .modal-title").html("Tambah Penjadwalan");
        jQuery("#modalTambahJadwal .submitBtn")
            .attr("onclick", 'submitTambahJadwalForm()')
            .attr("disabled", false)
            .text("Simpan");
        jQuery('#modalTambahJadwal').modal('show');
        jQuery.ajax({
            url: thisAjaxUrl,
            type: "post",
            data: {
                'action': "get_data_standar_lama_pelaksanaan",
                'api_key': jQuery("#api_key").val(),
                'tipe_perencanaan': tipePerencanaan
            },
            dataType: "json",
            success: function(response) {
                jQuery("#lama_pelaksanaan").val(response.data.lama_pelaksanaan);
            }
        })
    }

    function submitTambahJadwalForm() {
        jQuery("#wrap-loading").show()
        let nama = jQuery('#jadwal_nama').val()
        let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
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
                jQuery("#lama_pelaksanaan").val(response.data.lama_pelaksanaan);
            }
        })
    }

    function submitEditJadwalForm(id_jadwal_lokal) {
        jQuery("#wrap-loading").show()
        let nama = jQuery('#jadwal_nama').val()
        let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
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
                        alert(`GAGAL! \n${response.message}`)
                    }
                    jQuery('#jadwal_nama').val('')
                    jQuery("#tahun_mulai_anggaran").val('')
                }
            })
        }
        jQuery('#modalTambahJadwal').modal('hide');
    }

    function afterSubmitForm() {
        jQuery("#jadwal_nama").val("")
        jQuery("#tahun_mulai_anggaran").val("")
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
</script>