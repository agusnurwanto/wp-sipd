<?php
global $wpdb;
$api_key = get_option('_crb_api_key_extension');
$url = admin_url('admin-ajax.php');
$nama_skpd = null;
$kd_nama_skpd = null;
$input = shortcode_atts(array(
    'id_skpd' => '',
    'tahun_anggaran' => ''
), $atts);
$skpd_result = $wpdb->get_row(
    $wpdb->prepare('
        SELECT 
            kode_skpd,
            nama_skpd
        FROM data_unit
        WHERE id_skpd = %s
          AND tahun_anggaran = %d
          AND active = 1
    ', $input['id_skpd'], $input['tahun_anggaran']),
    ARRAY_A
);

if ($skpd_result) {
    $kd_nama_skpd = $skpd_result['kode_skpd'] . ' ' . $skpd_result['nama_skpd'];
} else {
    echo 'Data SKPD tidak ditemukan';
}
?>
<style type="text/css">
    .wrap-table {
        overflow: auto;
        max-height: 100vh;
        width: 100%;
    }
</style>
<div class="wrap-table">
    <h1 class="text-center">Data SP2D ( Surat Perintah Pencairan Dana )<br> <?php echo $kd_nama_skpd; ?><br> Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
    <table id="table-data-sp2d" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nomor SP2D</th>
                <th class="text-center">Tanggal SP2D</th>
                <th class="text-center">Keterangan SP2D</th>
                <th class="text-center">Jenis SP2D</th>
                <th class="text-center">Keterangan SP2D</th>
                <th class="text-center">Keterangan Transfer SP2D</th>
                <th class="text-center">Keterangan Verifikasi SP2D</th>
                <th class="text-center">Kode Sub SKPD</th>
                <th class="text-center">Metode</th>
                <th class="text-center">Nama Bank</th>
                <th class="text-center">Nama BUD KBUD</th>
                <th class="text-center">Nama Rekening BP BPP</th>
                <th class="text-center">Nama SKPD</th>
                <th class="text-center">Nama Sub SKPD</th>
                <th class="text-center">Nilai Materai SP2D</th>
                <th class="text-center">Nilai SP2D</th>
                <th class="text-center">NIP BUD KBUD</th>
                <th class="text-center">Nomor Rekening BP BPP</th>
                <th class="text-center">Nomor Jurnal</th>
                <th class="text-center">Nomor SP2D</th>
                <th class="text-center">Nomor SPM</th>
                <th class="text-center">Tahun Gaji</th>
                <th class="text-center">Tahun TPP</th>
                <th class="text-center">Tanggal SP2D</th>
                <th class="text-center">Tanggal SPM</th>
                <th class="text-center">Tahun Anggaran</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal fade" id="showsp2d" tabindex="-1" role="dialog" aria-labelledby="showsp2dLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showsp2dLabel">Detail SP2D</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="wrap-table-detail">
                    <h6>ID SP2D : <span id="id_sp2d"></span></h6>
                    <table id="table-data-sp2d-detail" cellpadding="2" cellspacing="0" style="font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; border-collapse: collapse; width: 100%; overflow-wrap: break-word;" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>   
                                <th class="text-center">id_skpd</th>
                                <th class="text-center">nomor_sp_2_d</th>
                                <th class="text-center">nomor_spm</th>
                                <th class="text-center">jumlah</th>
                                <th class="text-center">kode_rekening</th>
                                <th class="text-center">total_anggaran</th>
                                <th class="text-center">uraian</th>
                                <th class="text-center">bank_pihak_ketiga</th>
                                <th class="text-center">jabatan_bud_kbud</th>
                                <th class="text-center">keterangan_sp2d</th>
                                <th class="text-center">nama_bank</th>
                                <th class="text-center">nama_bud_kbud</th>
                                <th class="text-center">nama_daerah</th>
                                <th class="text-center">nama_ibukota</th>
                                <th class="text-center">nama_pihak_ketiga</th>
                                <th class="text-center">nama_rek_pihak_ketiga</th>
                                <th class="text-center">nama_skpd</th>
                                <th class="text-center">nama_sub_skpd</th>
                                <th class="text-center">nilai_sp2d</th>
                                <th class="text-center">nip_bud_kbud</th>
                                <th class="text-center">no_rek_pihak_ketiga</th>
                                <th class="text-center">nomor_rekening</th>
                                <th class="text-center">npwp_pihak_ketiga</th>
                                <th class="text-center">tahun</th>
                                <th class="text-center">tanggal_sp_2_d</th>
                                <th class="text-center">tanggal_spm</th>
                                <th class="text-center">tipe</th>
                                <th class="text-center">tahun_anggaran</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        get_datatable_sp2d();
    });

    function get_datatable_sp2d() {
        if (typeof tableDataSp2d == 'undefined') {
            window.tableDataSp2d = jQuery('#table-data-sp2d').on('preXhr.dt', function(e, settings, data) {
                jQuery("#wrap-loading").show();
            }).DataTable({
                "processing": true,
                "serverSide": true,
                "search": {
                    return: true
                },
                "ajax": {
                    url: '<?php echo $url; ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        'action': 'get_datatable_data_sp2d_sipd',
                        'api_key': '<?php echo $api_key; ?>',
                        'id_skpd': '<?php echo $input['id_skpd']; ?>',
                        'tahun_anggaran': '<?php echo $input['tahun_anggaran']; ?>',
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
                        "data": 'id',
                        "className": "text-center",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, full, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        "data": 'nomor_sp_2_d',
                        "className": "text-center"
                    },
                    {
                        "data": 'tanggal_sp_2_d',
                        "className": "text-center"
                    },
                    {
                        "data": 'keterangan_sp_2_d',
                        "className": "text-center"
                    },
                    {
                        "data": 'jenis_sp_2_d',
                        "className": "text-center"
                    },
                    {
                        "data": 'keterangan_sp_2_d',
                        "className": "text-center"
                    },
                    {
                        "data": 'keterangan_transfer_sp_2_d',
                        "className": "text-center"
                    },
                    {
                        "data": 'keterangan_verifikasi_sp_2_d',
                        "className": "text-center"
                    },
                    {
                        "data": 'kode_sub_skpd',
                        "className": "text-center"
                    },
                    {
                        "data": 'metode',
                        "className": "text-center"
                    },
                    {
                        "data": 'nama_bank',
                        "className": "text-center"
                    },
                    {
                        "data": 'nama_bud_kbud',
                        "className": "text-center"
                    },
                    {
                        "data": 'nama_rek_bp_bpp',
                        "className": "text-center"
                    },
                    {
                        "data": 'nama_skpd',
                        "className": "text-center"
                    },
                    {
                        "data": 'nama_sub_skpd',
                        "className": "text-center"
                    },
                    {
                        "data": 'nilai_materai_sp_2_d',
                        "className": "text-center"
                    },
                    {
                        "data": 'nilai_sp_2_d',
                        "className": "text-center"
                    },
                    {
                        "data": 'nip_bud_kbud',
                        "className": "text-center"
                    },
                    {
                        "data": 'no_rek_bp_bpp',
                        "className": "text-center"
                    },
                    {
                        "data": 'nomor_jurnal',
                        "className": "text-center"
                    },
                    {
                        "data": 'nomor_sp_2_d',
                        "className": "text-center"
                    },
                    {
                        "data": 'nomor_spm',
                        "className": "text-center"
                    },
                    {
                        "data": 'tahun_gaji',
                        "className": "text-center"
                    },
                    {
                        "data": 'tahun_tpp',
                        "className": "text-center"
                    },
                    {
                        "data": 'tanggal_sp_2_d',
                        "className": "text-center"
                    },
                    {
                        "data": 'tanggal_spm',
                        "className": "text-center"
                    },
                    {
                        "data": 'tahun_anggaran',
                        "className": "text-center"
                    }
                ]
            });
        } else {
            tableDataSp2d.draw();
        }
    }

    function showsp2d(id) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo $url; ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'action': 'get_data_sp2d_sipd',
                'api_key': '<?php echo $api_key; ?>',
                'tahun_anggaran': '<?php echo $input['tahun_anggaran'] ?>',
                'id_sp_2_d': id,
            },
            success: function(res) {
                console.log(res);
                if (res.status == 'success') {
                    jQuery('#id_sp2d').html(id);
                    var html = ''; 
                    var id_skpd ='-';
                    var nomor_sp_2_d ='-';
                    var nomor_spm ='-';
                    var jumlah ='-';
                    var kode_rekening ='-';
                    var total_anggaran ='-';
                    var uraian ='-';
                    var bank_pihak_ketiga ='-';
                    var jabatan_bud_kbud ='-';
                    var keterangan_sp2d ='-';
                    var nama_bank ='-';
                    var nama_bud_kbud ='-';
                    var nama_daerah ='-';
                    var nama_ibukota ='-';
                    var nama_pihak_ketiga ='-';
                    var nama_rek_pihak_ketiga ='-';
                    var nama_skpd ='-';
                    var nama_sub_skpd ='-';
                    var nilai_sp2d ='-';
                    var nip_bud_kbud ='-';
                    var no_rek_pihak_ketiga ='-';
                    var nomor_rekening ='-';
                    var npwp_pihak_ketiga ='-';
                    var tahun ='-';
                    var tanggal_sp_2_d ='-';
                    var tanggal_spm ='-';
                    var tipe ='-';
                    var tahun_anggaran ='-';
                    res.data.detail.map(function(b, i){ 
                        if(b.id_skpd != null){ 
                            id_skpd = b.id_skpd;
                        } 

                        if(b.nomor_sp_2_d != null){ 
                            nomor_sp_2_d = b.nomor_sp_2_d;
                        } 

                        if(b.nomor_spm != null){ 
                            nomor_spm = b.nomor_spm;
                        } 

                        if(b.jumlah != null){ 
                            jumlah = b.jumlah;
                        } 

                        if(b.kode_rekening != null){ 
                            kode_rekening = b.kode_rekening;
                        } 

                        if(b.total_anggaran != null){ 
                            total_anggaran = b.total_anggaran;
                        } 

                        if(b.uraian != null){ 
                            uraian = b.uraian;
                        } 

                        if(b.bank_pihak_ketiga != null){ 
                            bank_pihak_ketiga = b.bank_pihak_ketiga;
                        } 

                        if(b.jabatan_bud_kbud != null){ 
                            jabatan_bud_kbud = b.jabatan_bud_kbud;
                        } 

                        if(b.keterangan_sp2d != null){ 
                            keterangan_sp2d = b.keterangan_sp2d;
                        } 

                        if(b.nama_bank != null){ 
                            nama_bank = b.nama_bank;
                        } 

                        if(b.nama_bud_kbud != null){ 
                            nama_bud_kbud = b.nama_bud_kbud;
                        } 

                        if(b.nama_daerah != null){ 
                            nama_daerah = b.nama_daerah;
                        } 

                        if(b.nama_ibukota != null){ 
                            nama_ibukota = b.nama_ibukota;
                        } 

                        if(b.nama_pihak_ketiga != null){ 
                            nama_pihak_ketiga = b.nama_pihak_ketiga;
                        } 

                        if(b.nama_rek_pihak_ketiga != null){ 
                            nama_rek_pihak_ketiga = b.nama_rek_pihak_ketiga;
                        } 

                        if(b.nama_skpd != null){ 
                            nama_skpd = b.nama_skpd;
                        } 

                        if(b.nama_sub_skpd != null){ 
                            nama_sub_skpd = b.nama_sub_skpd;
                        } 

                        if(b.nilai_sp2d != null){ 
                            nilai_sp2d = b.nilai_sp2d;
                        } 

                        if(b.nip_bud_kbud != null){ 
                            nip_bud_kbud = b.nip_bud_kbud;
                        } 

                        if(b.no_rek_pihak_ketiga != null){ 
                            no_rek_pihak_ketiga = b.no_rek_pihak_ketiga;
                        } 

                        if(b.nomor_rekening != null){ 
                            nomor_rekening = b.nomor_rekening;
                        } 

                        if(b.npwp_pihak_ketiga != null){ 
                            npwp_pihak_ketiga = b.npwp_pihak_ketiga;
                        } 

                        if(b.tahun != null){ 
                            tahun = b.tahun;
                        } 

                        if(b.tanggal_sp_2_d != null){ 
                            tanggal_sp_2_d = b.tanggal_sp_2_d;
                        } 

                        if(b.tanggal_spm != null){ 
                            tanggal_spm = b.tanggal_spm;
                        } 

                        if(b.tipe != null){ 
                            tipe = b.tipe;
                        } 

                        if(b.tahun_anggaran != null){ 
                            tahun_anggaran = b.tahun_anggaran;
                        } 
                        html += ''
                        +'<tr>' 
                            +'<td class="text-center">' + (i + 1) + '</td>' 
                            +'<td>'+id_skpd+ '</td>'
                            +'<td>'+nomor_sp_2_d+ '</td>'
                            +'<td>'+nomor_spm+ '</td>'
                            +'<td>'+jumlah+ '</td>'
                            +'<td>'+kode_rekening+ '</td>'
                            +'<td>'+total_anggaran+ '</td>'
                            +'<td>'+uraian+ '</td>'
                            +'<td>'+bank_pihak_ketiga+ '</td>'
                            +'<td>'+jabatan_bud_kbud+ '</td>'
                            +'<td>'+keterangan_sp2d+ '</td>'
                            +'<td>'+nama_bank+ '</td>'
                            +'<td>'+nama_bud_kbud+ '</td>'
                            +'<td>'+nama_daerah+ '</td>'
                            +'<td>'+nama_ibukota+ '</td>'
                            +'<td>'+nama_pihak_ketiga+ '</td>'
                            +'<td>'+nama_rek_pihak_ketiga+ '</td>'
                            +'<td>'+nama_skpd+ '</td>'
                            +'<td>'+nama_sub_skpd+ '</td>'
                            +'<td>'+nilai_sp2d+ '</td>'
                            +'<td>'+nip_bud_kbud+ '</td>'
                            +'<td>'+no_rek_pihak_ketiga+ '</td>'
                            +'<td>'+nomor_rekening+ '</td>'
                            +'<td>'+npwp_pihak_ketiga+ '</td>'
                            +'<td>'+tahun+ '</td>'
                            +'<td>'+tanggal_sp_2_d+ '</td>'
                            +'<td>'+tanggal_spm+ '</td>'
                            +'<td>'+tipe+ '</td>'
                            +'<td>'+tahun_anggaran+ '</td>'
                        +'</tr>';
                    });
                    jQuery('#table-data-sp2d-detail').DataTable().clear();
                    jQuery('#table-data-sp2d-detail tbody').html(html);
                    jQuery('#showsp2d').modal('show');
                    jQuery('#table-data-sp2d-detail').dataTable();
                } else {
                    alert(res.message);
                }
                jQuery('#wrap-loading').hide();
            }
        });
    }
</script>