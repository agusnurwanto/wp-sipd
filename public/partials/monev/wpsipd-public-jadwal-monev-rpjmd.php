<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
global $wpdb;
// $cek_jadwal = $wpdb->get_results(
//     $wpdb->prepare('
//         SELECT *
//         FROM data_jadwal_lokal
//         WHERE status = %d
//           AND id_tipe = %d
//     ', 0, 17)
// );
// if (!empty($cek_jadwal)) {
//     $cek_jadwal = false;
// } else {
//     $cek_jadwal = true;
// }
?>
<style>
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="cetak">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
        <h1 class="text-center" style="margin:3rem;">Jadwal Monev RPJMD</h1>
            <div style="margin-bottom: 25px;">
                <button class="btn btn-primary" onclick="tambah_jadwal();"><span class="dashicons dashicons-plus"></span>Tambah Jadwal</button>
            </div>
        <table id="data_penjadwalan_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead id="data_header">
                <tr>
                    <th class="text-center">Nama Tahapan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Jenis Jadwal</th>
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
                <div class="form-group">
                    <label for='jadwal_nama'>Nama Tahapan</label>
                    <input type='text' id='jadwal_nama' class="form-control" placeholder='Nama Tahapan'>
                </div>
                <div class="form-group">
                    <label for='jenis_rpjmd_rpd'>Pilih Jenis Jadwal</label>
                    <select id='jenis_rpjmd_rpd' name="jenis_rpjmd_rpd" class="form-control">
                        <option value="" selected disabled>Pilih Jenis RPJM/RPD</option>
                        <option value="rpjmd">RPJMD</option>
                        <option value="rpd">RPD</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for='tahun_mulai_anggaran'>Tahun Mulai Anggaran</label>
                        <input type="number" id='tahun_mulai_anggaran' name="tahun_mulai_anggaran" class="form-control" placeholder="Tahun Mulai Anggaran" />
                    </div>
                    <div class="form-group col-md-4">
                        <label for='tahun_akhir_anggaran'>Tahun Akhir Anggaran</label>
                        <input type="number" id='tahun_akhir_anggaran' name="tahun_akhir_anggaran" class="form-control" placeholder="Tahun Akhir Anggaran" />
                    </div>
                    <div class="form-group col-md-4">
                        <label for='lama_pelaksanaan'>Lama Pelaksanaan</label>
                        <div class="input-group">
                            <input type="number" id="lama_pelaksanaan" name="lama_pelaksanaan" class="form-control" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">Tahun</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning" role="alert" id="alert-edit">
                    Perubahan detail Jadwal Monev RPJMD juga akan mempengaruhi detail Jadwal Monev RENSTRA. Mohon pastikan perubahan dilakukan dengan teliti.
                </div>
                <div class="alert alert-info" role="alert" id="alert-tambah">
                    Menambahkan Jadwal Monev RPJMD baru juga akan menambahkan Jadwal Monev RENSTRA baru. Pastikan data yang Anda masukkan benar.
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
                    "data": "jenis_jadwal",
                    className: "text-center"
                },
                {
                    "data": "tahun_anggaran",
                    className: "text-center"
                },
                {
                    "data": "tahun_akhir_anggaran",
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
        jQuery("#alert-tambah").show()
        jQuery("#alert-edit").hide()
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
        let this_jenis_jadwal = jQuery("#jenis_rpjmd_rpd").val()
        let this_tahun_akhir_anggaran = jQuery("#tahun_akhir_anggaran").val()
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
                    'jenis_jadwal': this_jenis_jadwal,
                    'tahun_akhir_anggaran': this_tahun_akhir_anggaran,
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
        jQuery("#alert-tambah").hide()
        jQuery("#alert-edit").show()
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
                jQuery("#tahun_akhir_anggaran").val(response.data.tahun_akhir_anggaran);
                jQuery("#jenis_rpjmd_rpd").val(response.data.jenis_jadwal);
                jQuery("#lama_pelaksanaan").val(response.data.lama_pelaksanaan);
            }
        })
    }

    function submitEditJadwalForm(id_jadwal_lokal) {
        jQuery("#wrap-loading").show()
        let nama = jQuery('#jadwal_nama').val()
        let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
        let this_jenis_jadwal = jQuery("#jenis_rpjmd_rpd").val()
        let this_tahun_akhir_anggaran = jQuery("#tahun_akhir_anggaran").val()
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
                    'jenis_jadwal': this_jenis_jadwal,
                    'tahun_akhir_anggaran': this_tahun_akhir_anggaran,
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
        jQuery("#jenis_rpjmd_rpd").val("")
        jQuery("#lama_pelaksanaan").val("")
        jQuery("#tahun_akhir_anggaran").val("")
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
        // let confirmLocked = confirm("Apakah anda yakin akan mengunci penjadwalan?");
        // if (confirmLocked) {
        //     jQuery('#wrap-loading').show();
        //     jQuery.ajax({
        //         url: thisAjaxUrl,
        //         type: 'post',
        //         data: {
        //             'action': 'submit_lock_schedule_rpjpd',
        //             'api_key': jQuery("#api_key").val(),
        //             'id_jadwal_lokal': id_jadwal_lokal
        //         },
        //         dataType: 'json',
        //         success: function(response) {
        //             jQuery('#wrap-loading').hide();
        //             if (response.status == 'success') {
        //                 alert('Data berhasil dikunci!.');
        //                 penjadwalanTable.ajax.reload();
        //             } else {
        //                 alert(`GAGAL! \n${response.message}`);
        //             }
        //         }
        //     });
        // }
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