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
</script>