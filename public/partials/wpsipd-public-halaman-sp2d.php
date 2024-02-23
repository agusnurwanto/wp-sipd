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
                <th class="text-center">Nomor Sp2d</th>
                <th class="text-center">Tanggal Sp2d</th>
                <th class="text-center">Tahun Anggaran</th>
                <th class="text-center">Keterangan Sp2d</th>
                <th class="text-center">Jenis Sp2d</th>
                <th class="text-center">Nilai Sp2d</th>
                <th class="text-center">Jenis Ls</th>
                <th class="text-center">Verifikasi Sp2d</th>
                <th class="text-center">Tanggal Verifikasi</th>
                <th class="text-center">Kunci Rekening</th>
                <th class="text-center">Bulan Gaji</th>
                <th class="text-center">Tahun Gaji</th>
                <th class="text-center">Jenis Gaji</th>
                <th class="text-center">Status Tahap</th>
                <th class="text-center">Kode Tahap</th>
                <th class="text-center">Status Aklap</th>
                <th class="text-center">Nomor Jurnal</th>
                <th class="text-center">Metode</th>
                <th class="text-center">Bulan Tpp</th>
                <th class="text-center">Tahun Tpp</th>
                <th class="text-center">Nomor Rekening Pembayar</th>
                <th class="text-center">Bank Rekening Pembayar</th>
                <th class="text-center">Nomor Spm</th>
                <th class="text-center">Tanggal Spm</th>
                <th class="text-center">Tahun Spm</th>
                <th class="text-center">Keterangan Spm</th>
                <th class="text-center">Verifikasi Spm</th>
                <th class="text-center">Tanggal Verifikasi Spm</th>
                <th class="text-center">Jenis Spm</th>
                <th class="text-center">Nilai Spm</th>
                <th class="text-center">Keterangan Verifikasi Spm</th>
                <th class="text-center">Tanggal Otorisasi</th>
                <th class="text-center">Nama Skpd</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
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
                        "data": null,
                        "className": "text-center",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, full, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        "data": 'nomorSp2d',
                        className: "text-center"
                    },
                    {
                        "data": 'nilaiSp2d',
                        className: "text-right"
                    },
                    {
                        "data": 'tanggalSp2d',
                        className: "text-center"
                    },
                    {
                        "data": 'keteranganSp2d',
                        className: "text-center"
                    },
                    {
                        "data": 'nilaiSp2d',
                        className: "text-right"
                    },
                    {
                        "data": 'tanggalVerifikasi',
                        className: "text-center"
                    },
                    {
                        "data": 'jenisSp2d',
                        className: "text-center"
                    },
                    {
                        "data": 'verifikasiSp2d',
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
                        "data": 'tahunSp2d',
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
                        "data": 'tanggalOtorisasi',
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
            tableDataSp2d.draw();
        }
    }
</script>