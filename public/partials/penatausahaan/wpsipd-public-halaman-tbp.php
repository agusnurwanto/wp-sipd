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
// print_r($skpd_result); die($wpdb->last_query);
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
    <h1 class="text-center">Menampilkan Tanda Bukti Penerimaan (TBP)<br> <?php echo $kd_nama_skpd; ?><br> Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
    <table id="table-data-tbp" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nomor TBP</th>                
                <th class="text-center">Nilai TBP</th>
                <th class="text-center">Tanggal TBP</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center">Nomor Kwitansi</th>
                <th class="text-center">Jenis</th>
                <th class="text-center">Otorisasi</th>
                <th class="text-center">Validasi</th>
                <th class="text-center">Metode Penyetoran</th>
                <th class="text-center">Status</th>
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
                                <th class="text-center">Nama Penyetor</th>
                                <th class="text-center">Metode Input</th>
                                <th class="text-center">Total TBP</th>
                                <th class="text-center">Tanggal TBP</th>
                                <th class="text-center">Keterangan</th>
                                <th class="text-center">Verifikasi</th>
                                <th class="text-center">Otorisasi</th>
                                <th class="text-center">Validasi</th>
                                <th class="text-center">Metode Penyetoran</th>
                                <th class="text-center">Status</th>
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
            window.tableDatTbp = jQuery('#table-data-tbp').on('preXhr.dt', function(e, settings, data) {
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
                        "data": 'nomorTbp',
                        className: "text-center"
                    },
                    {
                        "data": 'nomorRekening',
                        className: "text-right"
                    },
                    {
                        "data": 'nilaiStbp',
                        className: "text-right"
                    },
                    {
                        "data": 'tanggalStbp',
                        className: "text-center"
                    },
                    {
                        "data": 'keteranganStbp',
                        className: "text-center"
                    },
                    {
                        "data": 'isVerifikasiStbp',
                        className: "text-center"
                    },
                    {
                        "data": 'isOtorisasiStbp',
                        className: "text-center"
                    },
                    {
                        "data": 'isValidasiStbp',
                        className: "text-center"
                    },
                    {
                        "data": 'metodePenyetoran',
                        className: "text-center"
                    },
                    {
                        "data": 'status',
                        className: "text-center"
                    },
                ]
            });
        } else {
            tableDataSpp.draw();
        }
    }

    function modalDetailTbp(id) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo $url; ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'action': 'get_data_tbp_sipd_detail',
                'api_key': '<?php echo $api_key; ?>',
                'tahun_anggaran': '<?php echo $input['tahun_anggaran']?>',
                'id_spp': id
            },
            success: function(res) {
                if (res.status == 'success') {
                    let html = "";
                    res.data.map(function(b, i) {
                        html += '' +
                            '<tr>' +
                            '<td class="text-center">' + (i + 1) + '</td>' +
                            '<td class="text-center">' + b.nama_penyetor + '</td>' +
                            '<td class="text-center">' + b.metode_input + '</td>' +
                            '<td class="text-center">' + b.nomor_tbp + '</td>' +
                            '<td class="text-center">' + b.tanggal_tbp + '</td>' +
                            '<td class="text-center">' + b.id_bank + '</td>' +
                            '<td class="text-center">' + b.nama_bank + '</td>' +
                            '<td class="text-center">' + b.no_rekening + '</td>' +
                            '<td class="text-center">' + b.nilai_tbp + '</td>' +
                            '<td class="text-center">' + b.keterangan_tbp + '</td>' +
                            '<td class="text-center">' + b.created_by + '</td>' +
                            '<td class="text-center">' + b.bendahara_penerimaan_nama + '</td>' +
                            '<td class="text-center">' + b.bendahara_penerimaan_nip + '</td>' +
                            '<td class="text-center">' + b.nama_skpd + '</td>' +
                            '<td class="text-center">' + b.kode_rekening + '</td>' +
                            '<td class="text-center">' + b.uraian + '</td>' +
                            '<td class="text-center">' + b.nilai + '</td>' +
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