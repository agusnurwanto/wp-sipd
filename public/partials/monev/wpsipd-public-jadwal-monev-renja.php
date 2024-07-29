<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

global $wpdb;
$input = shortcode_atts(array(
    'tahun_anggaran' => ''
), $atts);
if (empty($input['tahun_anggaran'])) {
    die("Tahun Anggaran Kosong");
}
$renstra_options = '';

$sqlTipe = $wpdb->get_results(
    $wpdb->prepare("
        SELECT * 
        FROM `data_tipe_perencanaan` 
        WHERE nama_tipe=%s
    ", 'monev_renstra'),
    ARRAY_A
);
$data_renstra = $wpdb->get_results(
    $wpdb->prepare('
        SELECT *
        FROM data_jadwal_lokal
        WHERE status=0
          AND id_tipe=%d
    ', $sqlTipe[0]['id']),
    ARRAY_A
);

if (!empty($data_renstra)) {
    foreach ($data_renstra as $renstra) {
        $tipe = [];
        foreach ($sqlTipe as $val_tipe) {
            $tipe[$val_tipe['id']] = strtoupper($val_tipe['nama_tipe']);
        }
        $renstra_options .=
            '<option value="' . $renstra['id_jadwal_lokal'] . '">
            ' . $tipe[$renstra['id_tipe']] . ' | ' . $renstra['nama'] . ' | 
            ' . $renstra['tahun_anggaran'] . ' - ' . $renstra['tahun_akhir_anggaran'] .
            '</option>';
    }
}

$cek_jadwal = $wpdb->get_results(
    $wpdb->prepare('
        SELECT *
        FROM data_jadwal_lokal
        WHERE status = %d
          AND id_tipe = %d
    ', 0, 17)
);
if (!empty($cek_jadwal)) {
    $cek_jadwal = false;
} else {
    $cek_jadwal = true;
}
?>
<style>
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="cetak">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
        <h1 class="text-center" style="margin:3rem;">Jadwal Monev RENJA<br> Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
        <?php if ($cek_jadwal) : ?>
            <div style="margin-bottom: 25px;">
                <button class="btn btn-primary" onclick="tambah_jadwal();"><span class="dashicons dashicons-plus"></span>Tambah Jadwal</button>
            </div>
        <?php endif; ?>
        <table id="data_penjadwalan_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead id="data_header">
                <tr>
                    <th class="text-center">Nama Tahapan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Jadwal RENSTRA</th>
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
                <div class="form-group">
                    <label for="jadwal_renstra">Pilih Jadwal RENSTRA</label>
                    <select id="jadwal_renstra" class="form-control" required>
                        <option value="">Pilih RENSTRA</option>
                        <?php echo $renstra_options; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for='jadwal_nama'>Nama Tahapan</label>
                    <input type='text' id='jadwal_nama' class="form-control" placeholder='Nama Tahapan' required>
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
        globalThis.tipePerencanaan = 'monev_renja'
        globalThis.this_tahun_anggaran = '<?php echo $input['tahun_anggaran']; ?>'
        globalThis.thisAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>"
        get_data_penjadwalan();
    });

    function get_data_penjadwalan() {
        jQuery("#wrap-loading").show();
        globalThis.penjadwalanTable = jQuery('#data_penjadwalan_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: thisAjaxUrl,
                type: "POST",
                data: {
                    'action': "get_data_penjadwalan",
                    'api_key': jQuery("#api_key").val(),
                    'tipe_perencanaan': tipePerencanaan,
                    'tahun_anggaran': "<?php echo $input['tahun_anggaran']; ?>"
                }
            },
            "initComplete": function(settings, JSON) {
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

    function tambah_jadwal() {
        afterSubmitForm()
        jQuery("#modalTambahJadwal .modal-title").html("Tambah Penjadwalan");
        jQuery("#modalTambahJadwal .submitBtn")
            .attr("onclick", 'submitTambahJadwalForm()')
            .attr("disabled", false)
            .text("Simpan");
        jQuery('#modalTambahJadwal').modal('show');
    }

    function submitTambahJadwalForm() {
        jQuery("#wrap-loading").show()
        let nama = jQuery('#jadwal_nama').val()
        let relasi_perencanaan = jQuery("#jadwal_renstra").val()
        if (nama.trim() == '' || this_tahun_anggaran == '' || relasi_perencanaan == '') {
            jQuery("#wrap-loading").hide()
            alert("Ada yang kosong, Harap diisi semua")
            return false
        } else {
            jQuery.ajax({
                url: thisAjaxUrl,
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'action': 'submit_add_schedule',
                    'api_key': jQuery("#api_key").val(),
                    'nama': nama,
                    'tahun_anggaran': this_tahun_anggaran,
                    'tipe_perencanaan': tipePerencanaan,
                    'relasi_perencanaan': relasi_perencanaan,
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
                    jQuery("#jadwal_renstra").val('')
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
            type: "POST",
            data: {
                'action': "get_data_jadwal_by_id",
                'api_key': jQuery("#api_key").val(),
                'id_jadwal_lokal': id_jadwal_lokal
            },
            dataType: "JSON",
            success: function(response) {
                jQuery('#wrap-loading').hide();
                jQuery("#jadwal_nama").val(response.data.nama);
                jQuery("#jadwal_renstra").val(response.data.relasi_perencanaan).change();
            }
        })
    }

    function submitEditJadwalForm(id_jadwal_lokal) {
        jQuery("#wrap-loading").show()
        let nama = jQuery('#jadwal_nama').val()
        let relasi_perencanaan = jQuery("#jadwal_renstra").val()
        if (nama.trim() == '' || this_tahun_anggaran == '') {
            jQuery("#wrap-loading").hide()
            alert("Ada yang kosong, Harap diisi semua")
            return false
        } else {
            jQuery.ajax({
                url: thisAjaxUrl,
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'action': 'submit_edit_schedule',
                    'api_key': jQuery("#api_key").val(),
                    'nama': nama,
                    'id_jadwal_lokal': id_jadwal_lokal,
                    'tahun_anggaran': this_tahun_anggaran,
                    'tipe_perencanaan': tipePerencanaan,
                    'relasi_perencanaan': relasi_perencanaan,
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
                    jQuery("#jadwal_renstra").val('')
                }
            })
        }
        jQuery('#modalTambahJadwal').modal('hide');
    }

    function afterSubmitForm() {
        jQuery("#jadwal_nama").val("")
        jQuery("#jadwal_renstra").val("")
    }

    function hapus_data_penjadwalan(id_jadwal_lokal) {
        let confirmDelete = confirm("Apakah anda yakin akan menghapus penjadwalan?");
        if (confirmDelete) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: thisAjaxUrl,
                type: 'POST',
                data: {
                    'action': 'submit_delete_schedule',
                    'api_key': jQuery("#api_key").val(),
                    'id_jadwal_lokal': id_jadwal_lokal
                },
                dataType: 'JSON',
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

    function report(id_jadwal_lokal) {
        return (alert("Sementera laporan belum bisa dilakukan"));

        all_skpd();

        // let modal = `
        //     <div class="modal fade" id="modal-report" tab-index="-1" role="dialog" aria-labelledby="modal-indikator-renstra-label" aria-hidden="true">
        //     <div class="modal-dialog modal-lg" role="document" style="min-width:1450px">
        //         <div class="modal-content">
        //         <div class="modal-header">
        //             <h5 class="modal-title">Export Data</h5>
        //             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        //             <span aria-hidden="true">&times;</span>
        //             </button>
        //         </div>

        //         <div class="modal-body">
        //             <div class="container-fluid">
        //                 <div class="row">
        //                     <div class="col-md-2">Unit Kerja</div>
        //                     <div class="col-md-6">
        //                         <select class="form-control list_opd" id="list_opd"></select>
        //                     </div>
        //                 </div></br>
        //                 <div class="row">
        //                     <div class="col-md-2">Jenis Laporan</div>
        //                     <div class="col-md-6">
        //                         <select class="form-control jenis" id="jenis" onchange="jenisLaporan(this)">
        //                             <option value="-">Pilih Jenis</option>
        //                             <option value="rekap">Format Rekap Renstra</option>
        //                             <option value="tc27">Format TC 27</option>
        //                             <option value="pagu_akumulasi">Format Pagu Akumulasi Per Unit Kerja</option>
        //                             <option value="total_prog_keg">Total Program Kegiatan</option>
        //                         </select>
        //                     </div>
        //                 </div></br>
        //                 <div class="row">
        //                     <div class="col-md-2">Pagu</div>
        //                     <div class="col-md-6">
        //                         <select class="jenis_pagu" id="jenis_pagu" disabled>
        //                             <option value="-">Pilih jenis pagu</option>
        //                             <option value="0">Usulan</option>
        //                             <option value="1">Penetapan</option>
        //                         </select>
        //                     </div>
        //                 </div></br>
        //                 <div class="row">
        //                     <div class="col-md-2"></div>
        //                     <div class="col-md-6">
        //                         <button type="button" class="btn btn-success btn-preview" onclick="preview('${id_jadwal_lokal}')" data-jadwal="${id_jadwal_lokal}">Preview</button>
        //                         <button type="button" class="btn btn-primary export-excel" onclick="exportExcel()" disabled>Export Excel</button>
        //                     </div>
        //                 </div></br>
        //             </div>
        //         </div>

        //         <div class="modal-preview" style="padding:10px"></div>

        //         </div>
        //     </div>
        //     </div>`;

        // jQuery("body .report").html(modal);
        // jQuery("#modal-report").modal('show');
        // jQuery('.jenis').select2({
        //     width: '100%'
        // });
    }

    function all_skpd() {
        return (alert("Sementera laporan belum bisa dilakukan"));

        // jQuery('#wrap-loading').show();
        // jQuery.ajax({
        //     url: ajax.url,
        //     type: 'post',
        //     dataType: 'json',
        //     data: {
        //         action: 'get_list_skpd',
        //         tahun_anggaran: tahunAnggaran
        //     },
        //     success: function(response) {
        //         let list_opd = `<option value="">Pilih Unit Kerja</option><option value="all">Semua Unit Kerja</option>`;
        //         response.map(function(v, i) {
        //             list_opd += `<option value="${v.id_skpd}">${v.nama_skpd}</option>`;
        //         });
        //         jQuery("#list_opd").html(list_opd);
        //         jQuery('.list_opd').select2({
        //             width: '100%'
        //         });
        //         jQuery('.jenis_pagu').select2({
        //             width: '100%'
        //         });
        //         jQuery('#wrap-loading').hide();
        //     }
        // })
    }
</script>