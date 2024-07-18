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
    <h1 class="text-center">Menampilkan Surat Tanda Bukti Penerimaan (STBP)<br> <?php echo $kd_nama_skpd; ?><br> Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
    <table id="table-data-stbp" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nomor SPP</th>
                <th class="text-center">Nilai SPP</th>
                <th class="text-center">Tanggal SPP</th>
                <th class="text-center">Keterangan SPP</th>
                <th class="text-center">Nilai Disetujui SPP</th>
                <th class="text-center">Tanggal Disetujui SPP</th>
                <th class="text-center">Jenis SPP</th>
                <th class="text-center">Verifikasi SPP</th>
                <th class="text-center">Keterangan Verifikasi</th>
                <th class="text-center">Kunci Rekening</th>
                <th class="text-center">Alamat Penerima SPP</th>
                <th class="text-center">Bank Penerima SPP</th>
                <th class="text-center">Nomor Rekening Penerima SPP</th>
                <th class="text-center">NPWP Penerima SPP</th>
                <th class="text-center">Jenis LS</th>
                <th class="text-center">Tahun SPP</th>
                <th class="text-center">Status Perubahan</th>
                <th class="text-center">Kode Daerah</th>
                <th class="text-center">Tanggal Otorisasi</th>
                <th class="text-center">Bulan Gaji</th>
                <th class="text-center">Nama Pegawai PPTK</th>
                <th class="text-center">NIP Pegawai PPTK</th>
                <th class="text-center">Status Tahap</th>
                <th class="text-center">Kode Tahap</th>
                <th class="text-center">Bulan TPP</th>
                <th class="text-center">Nomor Pengajuan TU</th>
                <th class="text-center">Tipe</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal fade" id="modalDetailSpp" tabindex="-1" role="dialog" aria-labelledby="modalDetailSppLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailSppLabel">Detail SPP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="wrap-table-detail">
                    <table id="table-data-spp-detail" cellpadding="2" cellspacing="0" style="font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; border-collapse: collapse; width: 100%; overflow-wrap: break-word;" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nomor SPD</th>
                                <th class="text-center">Tanggal SPD</th>
                                <th class="text-center">Total SPD</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Kode Rekening</th>
                                <th class="text-center">Uraian</th>
                                <th class="text-center">Bank BP BPP</th>
                                <th class="text-center">Jabatan BP BPP</th>
                                <th class="text-center">Jabatan PA KPA</th>
                                <th class="text-center">Jenis LS SPP</th>
                                <th class="text-center">Keterangan</th>
                                <th class="text-center">Nama BP BPP</th>
                                <th class="text-center">Nama Daerah</th>
                                <th class="text-center">Nama Ibukota</th>
                                <th class="text-center">Nama PA KPA</th>
                                <th class="text-center">Nama PPTK</th>
                                <th class="text-center">Nama Rek BP BPP</th>
                                <th class="text-center">Nama SKPD</th>
                                <th class="text-center">Nama Sub SKPD</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center">Nip BP BPP</th>
                                <th class="text-center">Nip PA KPA</th>
                                <th class="text-center">Nip PPTK</th>
                                <th class="text-center">No Rek BP BPP</th>
                                <th class="text-center">Nomor Transaksi</th>
                                <th class="text-center">NPWP BP BPP</th>
                                <th class="text-center">Tahun</th>
                                <th class="text-center">Tanggal Transaksi</th>
                                <th class="text-center">Tipe</th>
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
        get_datatable_stbp();
    });

    function get_datatable_stbp() {
        if (typeof tableDataStbp == 'undefined') {
            window.tableDataStbp = jQuery('#table-data-stbp').on('preXhr.dt', function(e, settings, data) {
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
                        'action': 'get_datatable_data_stbp_sipd',
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
                        "data": 'nomorStbp',
                        className: "text-center"
                    },
                    {
                        "data": 'nomorRekening',
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
                        "data": 'nilaiDisetujuiStbp',
                        className: "text-right"
                    },
                    {
                        "data": 'tanggalDisetujuiSpp',
                        className: "text-center"
                    },
                    {
                        "data": 'jenisSpp',
                        className: "text-center"
                    },
                    {
                        "data": 'verifikasiSpp',
                        className: "text-center"
                    },
                    {
                        "data": 'keteranganVerifikasi',
                        className: "text-center"
                    },
                    {
                        "data": 'kunciRekening',
                        className: "text-center"
                    },
                    {
                        "data": 'alamatPenerimaSpp',
                        className: "text-center"
                    },
                    {
                        "data": 'bankPenerimaSpp',
                        className: "text-center"
                    },
                    {
                        "data": 'nomorRekeningPenerimaSpp',
                        className: "text-center"
                    },
                    {
                        "data": 'npwpPenerimaSpp',
                        className: "text-center"
                    },
                    {
                        "data": 'jenisLs',
                        className: "text-center"
                    },
                    {
                        "data": 'tahunSpp',
                        className: "text-center"
                    },
                    {
                        "data": 'statusPerubahan',
                        className: "text-center"
                    },
                    {
                        "data": 'kodeDaerah',
                        className: "text-center"
                    },
                    {
                        "data": 'tanggal_otorisasi',
                        className: "text-center"
                    },
                    {
                        "data": 'bulan_gaji',
                        className: "text-center"
                    },
                    {
                        "data": 'nama_pegawai_pptk',
                        className: "text-center"
                    },
                    {
                        "data": 'nip_pegawai_pptk',
                        className: "text-center"
                    },
                    {
                        "data": 'status_tahap',
                        className: "text-center"
                    },
                    {
                        "data": 'kode_tahap',
                        className: "text-center"
                    },
                    {
                        "data": 'bulan_tpp',
                        className: "text-center"
                    },
                    {
                        "data": 'nomor_pengajuan_tu',
                        className: "text-center"
                    },
                    {
                        "data": 'tipe',
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
                'action': 'get_data_spp_sipd_detail',
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
                            '<td class="text-center">' + b.nomor_spd + '</td>' +
                            '<td class="text-center">' + b.tanggal_spd + '</td>' +
                            '<td class="text-center">' + b.total_spd + '</td>' +
                            '<td class="text-center">' + b.jumlah + '</td>' +
                            '<td class="text-center">' + b.kode_rekening + '</td>' +
                            '<td class="text-center">' + b.uraian + '</td>' +
                            '<td class="text-center">' + b.bank_bp_bpp + '</td>' +
                            '<td class="text-center">' + b.jabatan_bp_bpp + '</td>' +
                            '<td class="text-center">' + b.jabatan_pa_kpa + '</td>' +
                            '<td class="text-center">' + b.jenis_ls_spp + '</td>' +
                            '<td class="text-center">' + b.keterangan + '</td>' +
                            '<td class="text-center">' + b.nama_bp_bpp + '</td>' +
                            '<td class="text-center">' + b.nama_daerah + '</td>' +
                            '<td class="text-center">' + b.nama_ibukota + '</td>' +
                            '<td class="text-center">' + b.nama_pa_kpa + '</td>' +
                            '<td class="text-center">' + b.nama_pptk + '</td>' +
                            '<td class="text-center">' + b.nama_rek_bp_bpp + '</td>' +
                            '<td class="text-center">' + b.nama_skpd + '</td>' +
                            '<td class="text-center">' + b.nama_sub_skpd + '</td>' +
                            '<td class="text-center">' + b.nilai + '</td>' +
                            '<td class="text-center">' + b.nip_bp_bpp + '</td>' +
                            '<td class="text-center">' + b.nip_pa_kpa + '</td>' +
                            '<td class="text-center">' + b.nip_pptk + '</td>' +
                            '<td class="text-center">' + b.no_rek_bp_bpp + '</td>' +
                            '<td class="text-center">' + b.nomor_transaksi + '</td>' +
                            '<td class="text-center">' + b.npwp_bp_bpp + '</td>' +
                            '<td class="text-center">' + b.tahun + '</td>' +
                            '<td class="text-center">' + b.tanggal_transaksi + '</td>' +
                            '<td class="text-center">' + b.tipe + '</td>' +
                            '</tr>';
                    });
                    jQuery('#table-data-spp-detail').DataTable().clear();
                    jQuery('#table-data-spp-detail tbody').html(html);
                    jQuery('#modalDetailSpp').modal('show');
                    jQuery('#table-data-spp-detail').DataTable();
                } else {
                    alert(res.message);
                }
                jQuery('#wrap-loading').hide();
            }
        });
    }
</script>