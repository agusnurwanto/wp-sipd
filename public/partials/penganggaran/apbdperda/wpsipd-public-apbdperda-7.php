<?php
/// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
global $wpdb;

$id_unit = $_GET['id_unit'] ?? '';
$id_jadwal_lokal = $_GET['id_jadwal_lokal'] ?? '';
$type = $_GET['type'] ?? '';
$dari_simda = $_GET['dari_simda'] ?? '';

if (empty($id_jadwal_lokal)) {
    die('<h1 class="text_tengah">ID Jadwal Lokal Tidak Boleh Kosong!</h1>');
}

$input = shortcode_atts(array(
    'id_skpd' => $id_unit,
    'id_jadwal_lokal' => $id_jadwal_lokal,
    'tahun_anggaran' => ''
), $atts);

$nama_pemda = get_option('_crb_daerah');

$options_skpd = array();
$options_skpd = $wpdb->get_results($wpdb->prepare("
    select 
        s.*,
        u.kode_skpd AS kode_unit,
        u.nama_skpd AS nama_unit
    FROM data_unit s
    JOIN data_unit u on u.id_skpd = s.id_unit
        AND u.active=s.active
        AND u.tahun_anggaran=s.tahun_anggaran
    WHERE s.tahun_anggaran=%d
        and s.active=1
    order by kode_skpd ASC
", $input['tahun_anggaran']), ARRAY_A);
?>
<style>
    @media print {
        #cetak {
            max-width: auto !important;
            height: auto !important;
        }

        #print_laporan {
            display: none;
        }
    }
</style>

<body>
    <div id="cetak" title="APBD PERDA Lampiran VII" style="padding: 5px; overflow: auto;">
        <table align="right" class="no-border no-padding" style="width:280px; font-size: 12px;">
            <tr>
                <td width="80" class="align-top">Lampiran VII </td>
                <td width="10" class="align-top">:</td>
                <td colspan="3" class="align-top" contenteditable="true"> Peraturan Daerah xxxxx </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td width="10">&nbsp;</td>
                <td width="60" class="text-start">Nomor</td>
                <td width="10">:</td>
                <td class="text-start" contenteditable="true">&nbsp;xx Desember xxxx</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td width="10">&nbsp;</td>
                <td width="60" class="text-start">Tanggal</td>
                <td width="10">:</td>
                <td class="text-start" contenteditable="true">&nbsp;xx Desember xxx</td>
            </tr>
        </table>
        <h3 class="text_tengah text-uppercase">
            <?php echo htmlspecialchars($nama_pemda); ?><br>
            SINKRONISASI PROGRAM PADA RPJMD/RPD DENGAN APBD<br>
            TAHUN ANGGARAN <?php echo htmlspecialchars($input['tahun_anggaran']); ?>
        </h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text_tengah atas kiri kanan bawah text_block" colspan="3">KODE</th>
                    <th class="text_tengah atas kiri kanan bawah text_block">URAIAN</th>
                    <th class="text_tengah atas kiri kanan bawah text_block">RPJMD/RPD (Rp)</th>
                    <th class="text_tengah atas kiri kanan bawah text_block">RANCANGAN APBD (Rp)</th>
                </tr>
                <tr>
                    <th class="text_tengah kiri kanan bawah text_block" colspan="3">1</th>
                    <th class="text_tengah kiri kanan bawah text_block">2</th>
                    <th class="text_tengah kiri kanan bawah text_block">3</th>
                    <th class="text_tengah kiri kanan bawah text_block">4</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <table width="25%" class="table-ttd no-border no-padding" align="right" cellpadding="2" cellspacing="0" style="width:280px; font-size: 12px;">
            <tr>
                <td colspan="3" class="text_tengah" height="20px"></td>
            </tr>
            <tr>
                <td colspan="3" class="text_tengah text_15" contenteditable="true">Bupati XXXX </td>
            </tr>
            <tr>
                <td colspan="3" height="80">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" class="text_tengah" contenteditable="true">XXXXXXXXXXX</td>
            </tr>
            <tr>
                <td colspan="3" class="text_tengah"></td>
            </tr>
        </table>
    </div>
</body>
<script>
    jQuery(document).ready(function() {
        run_download_excel();

        var list_skpd = <?php echo json_encode($options_skpd); ?>;
        window._url = new URL(window.location.href);
        window.new_url = changeUrl({
            url: _url.href,
            key: 'key',
            value: '<?php echo $this->gen_key(); ?>'
        });

        window.type = _url.searchParams.get("type");
        window.dari_simda = _url.searchParams.get("dari_simda");
        window.id_skpd = _url.searchParams.get("id_unit");

        var extend_action = '';
        if (type && type === 'pergeseran') {
            extend_action += '<a class="btn btn-primary ml-2" target="_blank" href="' + removeTypeParam(new_url) + '"><span class="dashicons dashicons-controls-back"></span> Halaman APBD Perda Lampiran VII</a>';
        } else {
            extend_action += '<a class="btn btn-primary ml-2" target="_blank" href="' + new_url + '&type=pergeseran"><span class="dashicons dashicons-controls-forward"></span> Halaman Pergeseran/Perubahan APBD Perda Lampiran VII</a>';
        }

        var options = '<option value="">Semua SKPD</option>';
        list_skpd.map(function(b) {
            var selected = (id_skpd && id_skpd == b.id_skpd) ? 'selected' : '';
            options += '<option ' + selected + ' value="' + b.id_skpd + '">' + b.kode_skpd + ' ' + b.nama_skpd + '</option>';
        });

        extend_action += '<button class="btn btn-info m-2" id="print_laporan" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Cetak Laporan</button><br>';
        extend_action += '<label for="options_skpd" class="mr-3">Pilih Perangkat Daerah</label>';
        extend_action += '<select id="pilih_skpd" name="options_skpd" onchange="ubah_skpd();" style="width:500px; margin-left:25px;">' + options + '</select>';
        extend_action += '</div>';

        jQuery('#action-sipd').append(extend_action);
        jQuery('#pilih_skpd').select2();
    });

    function removeTypeParam(url) {
        let urlObj = new URL(url);
        urlObj.searchParams.delete("type");
        return urlObj.href;
    }


    function ubah_skpd() {
        var pilih_id_skpd = jQuery('#pilih_skpd').val();
        var updated_url = _url.href;

        if (type) {
            updated_url = changeUrl({
                url: updated_url,
                key: 'type',
                value: type
            });
        }
        if (dari_simda) {
            updated_url = changeUrl({
                url: updated_url,
                key: 'dari_simda',
                value: dari_simda
            });
        }
        updated_url = changeUrl({
            url: updated_url,
            key: 'id_unit',
            value: pilih_id_skpd
        });

        window.open(updated_url);
        jQuery('#pilih_skpd').val(id_skpd);
    }
</script>