<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
global $wpdb;
$input = shortcode_atts(array(
    'tahun_anggaran' => ''
), $atts);

$cek_jadwal = $wpdb->get_row($wpdb->prepare("
    SELECT 
        *
    FROM data_jadwal_lokal 
    WHERE id_tipe=%d 
        AND tahun_anggaran=%d 
", 20, $input['tahun_anggaran']), ARRAY_A);
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
        <h1 class="text-center" style="margin:3rem;">Jadwal Manajemen Risiko<br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
        <!-- <?php if(empty($cek_jadwal)): ?>
            <div style="margin-bottom: 25px;">
                <button class="btn btn-primary" onclick="handleAddModal();"><span class="dashicons dashicons-plus"></span>Tambah Jadwal</button>
            </div>
        <?php endif; ?> -->
        <table id="data_penjadwalan_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead id="data_header">
                <tr>
                    <th class="text-center">Nama Tahapan</th>
                    <th class="text-center">Jadwal Mulai</th>
                    <th class="text-center">Jadwal Selesai</th>
                    <th class="text-center">Jenis Jadwal</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
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
                    <label for='jadwal_nama'>Nama Tahapan</label>
                    <input type='text' id='jadwal_nama' class="form-control" placeholder='Nama Tahapan'>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="jenis_jadwal">Pilih Jenis Jadwal</label>
                        <select id="jenis_jadwal" class="form-control">
                            <option value="usulan" selected>Usulan</option>
                            <option value="penetapan">Penetapan</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for='jadwal_tanggal'>Jadwal Pelaksanaan</label>
                        <input type="text" id='jadwal_tanggal' name="jadwal_tanggal" class="form-control"/>
                    </div>
                </div>
            </div> 
            <div class="modal-footer">
                <button class="btn btn-primary submitBtn" onclick="submitTambahJadwalForm()">Simpan</button>
                <button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="report"></div>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    jQuery(document).ready(function() {
        globalThis.tipePerencanaan = 'manajemen_resiko'

        get_data_penjadwalan();
    });

    function get_data_penjadwalan() {
        jQuery("#wrap-loading").show();
        globalThis.penjadwalanTable = jQuery('#data_penjadwalan_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: ajax.url,
                type: "post",
                data: {
                    'action': "get_data_penjadwalan",
                    'api_key': jQuery("#api_key").val(),
                    'tipe_perencanaan': tipePerencanaan,
                    'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
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
                    "data": "waktu_awal",
                    className: "text-center"
                },
                {
                    "data": "waktu_akhir",
                    className: "text-center"
                },
                {
                    "data": "jenis_jadwal",
                    className: "text-center"
                },
                {
                    "data": "aksi",
                    className: "text-center"
                }
            ]
        });
    }

    async function handleAddModal() {
        try {
            jQuery("#wrap-loading").show();
            jQuery("#modalTambahJadwal .modal-title").html("Tambah Penjadwalan");
            jQuery("#modalTambahJadwal .submitBtn")
                .attr("onclick", 'submitTambahJadwalForm()')
                .attr("disabled", false)
                .text("Simpan");

            jQuery("#jadwal_nama").val("");
            jQuery("#jenis_jadwal").val("usulan");
            jQuery('#jadwal_tanggal').data('daterangepicker');

            jQuery("#wrap-loading").hide();
            jQuery('#modalTambahJadwal').modal('show');
        } catch (error) {
            jQuery("#wrap-loading").hide();
            console.error(`terjadi kesalahan saat handleAddModal = ${error}`);
            alert('terjadi kesalahan saat tambah data');
        }
    }

    /** Submit tambah jadwal */
    function submitTambahJadwalForm() {
        jQuery("#wrap-loading").show()
        let nama = jQuery('#jadwal_nama').val()
        let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
        let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
        let jenis_jadwal = jQuery("#jenis_jadwal").val()
        if (nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || jenis_jadwal == '') {
            jQuery("#wrap-loading").hide()
            alert("Ada yang kosong, Harap diisi semua")
            return false
        } else {
            jQuery.ajax({
                url: ajax.url,
                type: 'post',
                dataType: 'json',
                data: {
                    'action': 'submit_add_schedule',
                    'api_key': jQuery("#api_key").val(),
                    'nama': nama,
                    'jadwal_mulai': jadwalMulai,
                    'jadwal_selesai': jadwalSelesai,
                    'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                    'tipe_perencanaan': tipePerencanaan,
                    'jenis_jadwal': jenis_jadwal
                },
                beforeSend: function() {
                    jQuery('.submitBtn').attr('disabled', 'disabled')
                },
                success: function(response) {
                    jQuery('#wrap-loading').hide()
                    if (response.status == 'success') {
                        jQuery('#modalTambahJadwal').modal('hide')
                        alert(response.message)
                        location.reload()
                        afterSubmitForm()
                    } else {
                        jQuery('.submitBtn').attr('disabled', false)
                        alert(response.message)
                    }
                }
            })
        }
    }

    async function edit_data_penjadwalan(id_jadwal_lokal) {
        try {
            jQuery("#wrap-loading").show();
            jQuery("#modalTambahJadwal .modal-title").html("Edit Penjadwalan");
            jQuery("#modalTambahJadwal .submitBtn")
                .attr("onclick", `submitEditJadwalForm(${id_jadwal_lokal})`)
                .attr("disabled", false)
                .text("Perbarui");

            let data_jadwal = await get_data_jadwal_by_id(id_jadwal_lokal);

            jQuery("#jadwal_nama").val(data_jadwal.data.nama);
            jQuery('#jadwal_tanggal').data('daterangepicker').setStartDate(moment(data_jadwal.data.waktu_awal).format('DD-MM-YYYY HH:mm'));
            jQuery('#jadwal_tanggal').data('daterangepicker').setEndDate(moment(data_jadwal.data.waktu_akhir).format('DD-MM-YYYY HH:mm'));
            jQuery("#jenis_jadwal").val(data_jadwal.data.jenis_jadwal);

            jQuery("#wrap-loading").hide();
            jQuery('#modalTambahJadwal').modal('show');
        } catch (error) {
            jQuery("#wrap-loading").hide();
            console.error(`terjadi kesalahan saat edit_data_penjadwalan = ${error}`);
            alert('terjadi kesalahan saat edit data');
        }
    }


    function submitEditJadwalForm(id_jadwal_lokal) {
        jQuery("#wrap-loading").show()
        let nama = jQuery('#jadwal_nama').val()
        let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
        let jenis_jadwal = jQuery("#jenis_jadwal").val()
        let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
        if (nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || jenis_jadwal == '') {
            jQuery("#wrap-loading").hide()
            alert("Ada yang kosong, Harap diisi semua")
            return false
        } else {
            jQuery.ajax({
                url: ajax.url,
                type: 'post',
                dataType: 'json',
                data: {
                    'action': 'submit_edit_schedule',
                    'api_key': jQuery("#api_key").val(),
                    'nama': nama,
                    'jadwal_mulai': jadwalMulai,
                    'jadwal_selesai': jadwalSelesai,
                    'id_jadwal_lokal': id_jadwal_lokal,
                    'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                    'tipe_perencanaan': tipePerencanaan,
                    'jenis_jadwal': jenis_jadwal
                },
                beforeSend: function() {
                    jQuery('.submitBtn').attr('disabled', 'disabled')
                },
                success: function(response) {
                    jQuery('#wrap-loading').hide()
                    if (response.status == 'success') {
                        jQuery('#modalTambahJadwal').modal('hide')
                        alert(response.message)
                        penjadwalanTable.ajax.reload()
                        afterSubmitForm()
                    } else {
                        jQuery('.submitBtn').attr('disabled', false)
                        alert(`GAGAL! \n${response.message}`)
                    }
                }
            })
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

    function afterSubmitForm() {
        jQuery("#jadwal_nama").val("")
        jQuery("#jenis_jadwal").val("")
        jQuery("#jadwal_tanggal").val("")
    }    

    function get_data_jadwal_by_id(id_jadwal_lokal) {
        return jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: {
                action: "get_data_jadwal_by_id",
                api_key: ajax.api_key,
                id_jadwal_lokal: id_jadwal_lokal
            }
        });
    }
</script>