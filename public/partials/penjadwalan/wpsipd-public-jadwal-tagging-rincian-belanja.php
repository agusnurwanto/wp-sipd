<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
$input = shortcode_atts(array(
    'tahun_anggaran' => '2022'
), $atts);


global $wpdb;

$sqlTipe = $wpdb->get_results(
    $wpdb->prepare("
        SELECT * 
        FROM `data_tipe_perencanaan` 
        WHERE nama_tipe=%s
    ", 'tagging_rincian'),
    ARRAY_A
);
?>
<style>
    .bulk-action {
        padding: .45rem;
        border-color: #eaeaea;
        vertical-align: middle;
    }
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="cetak">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
        <h1 class="text-center" style="margin:3rem;">Jadwal Tagging Rincian Belanja<br>Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_jadwal();"><span class="dashicons dashicons-plus"></span>Tambah Jadwal</button>
        </div>
        <table id="data_penjadwalan_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead id="data_header">
                <tr>
                    <th class="text-center">Nama Jadwal</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Jadwal Mulai</th>
                    <th class="text-center">Jadwal Selesai</th>
                    <th class="text-center" style="width: 75px;">Aksi</th>
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
                <div class="form-group">
                    <label for='jadwal_nama'>Nama Jadwal</label>
                    <input type='text' id='jadwal_nama' class="form-control" placeholder='masukan nama jadwal....'>
                </div>
                <div class="form-group">
                    <label for="jadwal_tanggal">Jadwal Pelaksanaan</label>
                    <input type="text" id="jadwal_tanggal" name="datetimes" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary submitBtn" onclick="submit_tambah_jadwal()">Simpan</button>
                <button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="report"></div>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    jQuery(document).ready(function() {

        globalThis.tahun_anggaran = <?php echo $input['tahun_anggaran']; ?>;
        globalThis.tipePerencanaan = 'tagging_rincian';
        globalThis.thisAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>";

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
                    "data": "waktu_awal",
                    className: "text-center"
                },
                {
                    "data": "waktu_akhir",
                    className: "text-center"
                },
                {
                    "data": "aksi",
                    className: "text-center"
                }
            ]
        });
    }


    //TAMBAH
    function tambah_jadwal() {
        afterSubmitForm();
        jQuery("#modalTambahJadwal .modal-title").html("Tambah Penjadwalan");
        jQuery("#modalTambahJadwal .submitBtn")
            .attr("onclick", 'submit_tambah_jadwal()')
            .attr("disabled", false)
            .text("Simpan");
        jQuery('#modalTambahJadwal').modal('show');
    }

    //SUBMIT TAMBAH
    function submit_tambah_jadwal() {
        jQuery("#wrap-loading").show()
        let nama = jQuery('#jadwal_nama').val()
        let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
        let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
        if (nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '') {
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
                    'jadwal_mulai': jadwalMulai,
                    'jadwal_selesai': jadwalSelesai,
                    'tipe_perencanaan': tipePerencanaan,
                    'tahun_anggaran': tahun_anggaran
                },
                beforeSend: function() {
                    jQuery('.submitBtn').attr('disabled', 'disabled')
                },
                success: function(response) {
                    jQuery('#modalTambahJadwal').modal('hide')
                    jQuery('#wrap-loading').hide()
                    if (response.status == 'success') {
                        alert('Jadwal berhasil ditambahkan')
                        penjadwalanTable.ajax.reload()
                        afterSubmitForm()
                    } else {
                        alert(response.message)
                    }
                    jQuery('#jadwal_nama').val('')
                    jQuery("#jadwal_tanggal").val('')
                }
            })
        }
        jQuery('#modalTambahJadwal').modal('hide');
    }

    // EDIT
    function edit_data_penjadwalan(id_jadwal_lokal) {
        jQuery("#modalTambahJadwal .modal-title").html("Edit Penjadwalan");
        jQuery("#modalTambahJadwal .submitBtn")
            .attr("onclick", 'submit_edit_jadwal(' + id_jadwal_lokal + ')')
            .attr("disabled", false)
            .text("Perbarui");
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
                afterSubmitForm();
                jQuery("#jadwal_nama").val(response.data.nama);
                jQuery('#jadwal_tanggal').data('daterangepicker').setStartDate(moment(response.data.waktu_awal).format('DD-MM-YYYY HH:mm'));
                jQuery('#jadwal_tanggal').data('daterangepicker').setEndDate(moment(response.data.waktu_akhir).format('DD-MM-YYYY HH:mm'));
                jQuery('#modalTambahJadwal').modal('show');
                jQuery('#wrap-loading').hide();
            }
        })
    }

    // SUBMIT EDIT
    function submit_edit_jadwal(id_jadwal_lokal) {
        jQuery("#wrap-loading").show()
        let nama = jQuery('#jadwal_nama').val()
        let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
        let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
        if (nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '') {
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
                    'jadwal_mulai': jadwalMulai,
                    'jadwal_selesai': jadwalSelesai,
                    'id_jadwal_lokal': id_jadwal_lokal,
                    'tipe_perencanaan': tipePerencanaan,
                },
                beforeSend: function() {
                    jQuery('.submitBtn').attr('disabled', 'disabled')
                },
                success: function(response) {
                    jQuery('#modalTambahJadwal').modal('hide')
                    jQuery('#wrap-loading').hide()
                    if (response.status == 'success') {
                        alert('Jadwal berhasil diperbarui')
                        penjadwalanTable.ajax.reload()
                        afterSubmitForm()
                    } else {
                        alert(`GAGAL! \n${response.message}`)
                    }
                }
            })
        }
        jQuery('#modalTambahJadwal').modal('hide');
    }

    //HAPUS
    function hapus_data_penjadwalan(id_jadwal_lokal) {
        let confirmDelete = confirm("Apakah anda yakin akan menghapus jadwal?");
        if (confirmDelete) {
            return alert('Coming Soon!')
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
                        alert('Data berhasil dihapus!.');
                        penjadwalanTable.ajax.reload();
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    //LOCK
    function lock_data_penjadwalan(id_jadwal_lokal) {
        let confirmLocked = confirm("Apakah anda yakin akan mengunci jadwal?");
        if (confirmLocked) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: thisAjaxUrl,
                type: 'post',
                data: {
                    'action': 'submit_lock_schedule_tagging',
                    'api_key': jQuery("#api_key").val(),
                    'id_jadwal_lokal': id_jadwal_lokal,
                    'tahun_anggaran': tahun_anggaran
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        alert(response.message);
                        penjadwalanTable.ajax.reload();
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    jQuery(function() {
        jQuery('#jadwal_tanggal').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            startDate: moment().startOf('hour'),
            endDate: moment().startOf('hour').add(32, 'hour'),
            locale: {
                format: 'DD-MM-YYYY HH:mm'
            }
        });
    });

    function cannot_change_schedule(jenis) {
        if (jenis == 'kunci') {
            alert('Tidak bisa kunci karena penjadwalan sudah dikunci');
        } else if (jenis == 'edit') {
            alert('Tidak bisa edit karena penjadwalan sudah dikunci');
        } else if (jenis == 'hapus') {
            alert('Tidak bisa hapus karena penjadwalan sudah dikunci');
        }
    }

    function afterSubmitForm() {
        jQuery("#jadwal_nama").val("")
        jQuery("#jadwal_tanggal").val("")
    }

    function report() {
       return alert('Coming Soon!')
    }
</script>