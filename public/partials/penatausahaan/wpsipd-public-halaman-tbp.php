<?php
global $wpdb;
$api_key = get_option('_crb_api_key_extension');
$url = admin_url('admin-ajax.php');

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

    .wrap-table-detail {
        overflow: auto;
        max-height: 100vh;
        width: 100%;
    }
</style>
<div class="wrap-table">
    <h1 class="text-center">Data TBP (Tanda Bukti Pembayaran)<br> <?php echo $kd_nama_skpd; ?><br> Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
    <table id="table-data-tbp" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nomor TBP</th>
                <th class="text-center">Nilai TBP</th>
                <th class="text-center">Tanggal TBP</th>
                <th class="text-center">Keterangan TBP</th>
                <th class="text-center">Nilai Materai</th>
                <th class="text-center">Nomor Kwitansi</th>
                <th class="text-center">Jenis TBP</th>
                <th class="text-center">Jenis LS TBP</th>
                <th class="text-center">Nomor Jurnal</th>
                <th class="text-center">Kunci Rekening</th>
                <th class="text-center">Panjar</th>
                <th class="text-center">LPJ</th>
                <th class="text-center">Rekanan Upload</th>
                <th class="text-center">Status Aklap</th>
                <th class="text-center">Metode</th>
                <th class="text-center">Total Pertanggungajawaban</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal fade" id="modalDetailTbp" tabindex="-1" role="dialog" aria-labelledby="modalDetailTbpLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailTbpLabel">Detail TBP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="wrap-table-detail">
                    <table id="table-data-tbp-detail" cellpadding="2" cellspacing="0" style="font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; border-collapse: collapse; width: 100%; overflow-wrap: break-word;" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nomor TBP</th>
                                <th class="text-center">Tanggal TBP</th>
                                <th class="text-center">Nilai TBP</th>
                                <th class="text-center">Nama Tujuan</th>
                                <th class="text-center">Alamat Perusahaan</th>
                                <th class="text-center">NPWP</th>
                                <th class="text-center">Nomor Rekening</th>                                
                                <th class="text-center">Nama Rekening</th>
                                <th class="text-center">Nama Bank</th>
                                <th class="text-center">Keterangan TBP</th>
                                <th class="text-center">Jenis Transaksi</th>
                                <th class="text-center">Nomor NPD</th>
                                <th class="text-center">Jenis Panjar</th>
                                <th class="text-center">Nama Daerah</th>
                                <th class="text-center">Nama SKPD</th>   
                                <th class="text-center">Nama PA KPA</th>
                                <th class="text-center">Nama BP BPP</th>                                
                                <th class="text-center">Jabatan PA KPA</th>
                                <th class="text-center">Jabatan BP BPP</th>
                                <th class="text-center">Nip PA KPA</th>
                                <th class="text-center">Nip BP BPP</th>
                                <th class="text-center">Jenis</th>
                                <th class="text-center">Potongan Pajak</th>
                                <th class="text-center">Jenis TBP</th>
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
        get_datatable_tbp();
    });

    function get_datatable_tbp() {
        if (typeof tableDataTbp == 'undefined') {
            window.tableDataTbp = jQuery('#table-data-tbp').on('preXhr.dt', function(e, settings, data) {
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
                        'action': 'get_datatable_data_tbp_sipd',
                        'api_key': '<?php echo $api_key; ?>',
                        'id_skpd': '<?php echo $input['id_skpd']; ?>',
                        'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
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
                        "data": null,
                        "className": "text-center",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, full, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        "data": 'nomor_tbp',
                        className: "text-center"
                    },
                    {
                        "data": 'nilai_tbp',
                        className: "text-right"
                    },
                    {
                        "data": 'tanggal_tbp',
                        className: "text-center"
                    },
                    {
                        "data": 'keterangan_tbp',
                        className: "text-center"
                    },
                    {
                        "data": 'nilai_materai_tbp',
                        className: "text-right"
                    },
                    {
                        "data": 'nomor_kwitansi',
                        className: "text-center"
                    },
                    {
                        "data": 'jenis_tbp',
                        className: "text-center"
                    },
                    {
                        "data": 'jenis_ls_tbp',
                        className: "text-center"
                    },
                    {
                        "data": 'nomor_jurnal',
                        className: "text-center"
                    },
                    {
                        "data": 'is_kunci_rekening_tbp',
                        className: "text-center"
                    },
                    {
                        "data": 'is_panjar',
                        className: "text-center"
                    },
                    {
                        "data": 'is_lpj',
                        className: "text-center"
                    },
                    {
                        "data": 'is_rekanan_upload',
                        className: "text-center"
                    },
                    {
                        "data": 'status_aklap',
                        className: "text-center"
                    },
                    {
                        "data": 'metode',
                        className: "text-center"
                    },
                    {
                        "data": 'total_pertanggungjawaban',
                        className: "text-center"
                    },
                ]
            });
        } else {
            tableDataSpp.draw();
        }
    }

    function modalDetailSpp(id) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo $url; ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'action': 'get_data_tbp_sipd_detail',
                'api_key': '<?php echo $api_key; ?>',
                'tahun_anggaran': '<?php echo $input['tahun_anggaran']?>',
                'id_tbp': id
            },
            success: function(res) {
                if (res.status == 'success') {
                    let html = "";
                    res.data.map(function(b, i) {
                        html += '' +
                            '<tr>' +
                            '<td class="text-center">' + (i + 1) + '</td>' +
                            '<td class="text-center">' + b.nomor_tbp + '</td>' +
                            '<td class="text-center">' + b.tanggal_tbp + '</td>' +
                            '<td class="text-center">' + b.nilai_tbp + '</td>' +
                            '<td class="text-center">' + b.nama_tujuan + '</td>' +                            
                            '<td class="text-center">' + b.alamat_perusahaan + '</td>' +
                            '<td class="text-center">' + b.npwp + '</td>' +
                            '<td class="text-center">' + b.nomor_rekening + '</td>' +
                            '<td class="text-center">' + b.nama_rekening + '</td>' +
                            '<td class="text-center">' + b.nama_bank + '</td>' +
                            '<td class="text-center">' + b.keterangan_tbp + '</td>' +
                            '<td class="text-center">' + b.jenis_transaksi + '</td>' +
                            '<td class="text-center">' + b.nomor_npd + '</td>' +
                            '<td class="text-center">' + b.jenis_panjar + '</td>' +
                            '<td class="text-center">' + b.nama_daerah + '</td>' +
                            '<td class="text-center">' + b.nama_skpd + '</td>' +
                            '<td class="text-center">' + b.nama_pa_kpa + '</td>' +
                            '<td class="text-center">' + b.nama_bp_bpp + '</td>' +
                            '<td class="text-center">' + b.jabatan_pa_kpa + '</td>' +
                            '<td class="text-center">' + b.jabatan_bp_bpp + '</td>' +                            
                            '<td class="text-center">' + b.nip_pa_kpa + '</td>' +    
                            '<td class="text-center">' + b.nip_bp_bpp + '</td>' +
                            '<td class="text-center">' + b.jenis + '</td>' +                                                    
                            '<td class="text-center">' + b.pajak_potongan + '</td>' +
                            '<td class="text-center">' + b.jenis_tbp + '</td>' +
                            '</tr>';
                    });
                    jQuery('#table-data-tbp-detail').DataTable().clear();
                    jQuery('#table-data-tbp-detail tbody').html(html);
                    jQuery('#modalDetailTbp').modal('show');
                    jQuery('#table-data-tbp-detail').DataTable();
                } else {
                    alert(res.message);
                }
                jQuery('#wrap-loading').hide();
            }
        });
    }
</script>