<?php
if (!defined('WPINC')) {
    die;
}
if (
    empty($_GET)
    || empty($_GET['id_spt'])
    || empty($_GET['tahun_anggaran'])
) {
    die('<h1 class="text-center">Id SPT / Tahun Anggaran Kosong</h1>');
}
?>
<style>
    .document-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    .row {
        outline: none;
        border: none;
    }

    .signature-table {
        border-collapse: collapse;
    }

    .signature-table td {
        padding: 2px;
        vertical-align: top;
    }

    .header-table {
        width: 100%;
        margin-bottom: 10px;
    }

    .header-table td {
        vertical-align: top;
        padding: 0;
    }

    table,
    th,
    tr,
    td {
        border: none;
    }

    .document-title {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .bottom-kop-line {
        border-top: 4px solid #000;
        margin: 20px 0;
    }

    @media print {
        #cetak {
            max-width: auto !important;
            height: auto !important;
        }

        #action-sipd {
            display: none;
        }
    }
</style>

<body>
    <div id="action-sipd"></div>
    <div id="cetak" class="document-container">
        <table class="header-table">
            <tr>
                <td style="width: 100px;">
                    <img
                        class="img-fluid"
                        src="<?php echo !empty(get_option('_crb_logo_dashboard')) ? get_option('_crb_logo_dashboard') : 'http://via.placeholder.com/350x350'; ?>"
                        width="100"
                        height="100" />
                </td>
                <td class="text-center">
                    <h4 contenteditable="true" class="mb-1">PEMERINTAH <?php echo strtoupper(get_option('_crb_daerah')); ?></h4>
                    <h4 contenteditable="true" class="mb-1 namaSkpd">xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</h4>
                    <div contenteditable="true">Jl. xxxxxxxxxxxxxxxxxxxxxxxxxxxxx</div>
                    <div contenteditable="true">Telp. xxxxxxxxxxxxxxxxxxxxxxxxx</div>
                </td>
            </tr>
        </table>
        <div class="bottom-kop-line"></div>
        <div style="height: 50px;"></div> <!-- Spacer -->
        <div class="text-center mt-4">
            <div class="document-title" contenteditable="true">SURAT PERINTAH TUGAS</div>
            <div contenteditable="true">Nomor : <span class="nomor_spt">xxxxxxxxxxxxxxxx</span></div>
        </div>

        <table class="mt-4">
            <tr>
                <th style="width: 15%;">DASAR</th>
                <td style="width: 10px;">:</td>
                <td>
                    <span contenteditable="true" class="dasar_spt">xxxxxxxxxxxxxxxx xxxxxxxxxxxxxxxx</span>
                </td>
            </tr>
        </table>

        <div class="text-center mt-4 mb-2">
            <strong>M E M E R I N T A H K A N</strong>
        </div>

        <table>
            <tr>
                <th style="width: 15%;">KEPADA</th>
                <td style="width: 10px;">:</td>
                <td id="list_pegawai">
                    
                </td>
            </tr>
            <tr>
                <th>UNTUK</th>
                <td style="width: 10px;">:</td>
                <td>
                    <span contenteditable="true" class="tujuan_spt">xxxxxxxxxxxxxxxx xxxxxxxxxxxxxxxx xxxxxxxxxxxxxxxx</span>
                </td>
            </tr>
        </table>

        <table class="signature-table" contenteditable="true" style="width: 100%;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="text-align: left; padding-bottom : 0;">Dikeluarkan di : <?php echo strtoupper(get_option('_crb_daerah')); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align: left; padding-top : 0;">Pada Tanggal : <span id="tanggalSppd"></span></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; padding-bottom : 0;" class="namaSkpd">xxxxxxxxxxxxxxxx</td>
                        </tr>
                        <tr>
                            <td style="text-align: center; padding-top : 0;">Selaku Pengguna Anggaran</td>
                        </tr>
                        <tr>
                            <td style="height: 80px;"></td> <!-- Space for signature -->
                        </tr>
                        <tr>
                            <td style="font-weight: 700; text-decoration: underline; text-align: center; padding-bottom : 0;" class="signature-name text_uppercase namaKepala">xxxxxxxxxxxxxxxx</td>
                        </tr>
                        <tr>
                            <td style="text-align: center; padding-bottom : 0; padding-top : 0;" class="pangkatKepala">xxxxxxxxxxxxxxxx</td>
                        </tr>
                        <tr>
                            <td style="text-align: center; padding-top : 0;">NIP. <span class="nipKepala">xxxxxxxxxxxxxx</span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
<script>
    jQuery(document).ready(function() {
        window.nomor_spt = ''
        get_spt();

        var extend_action = '';
        extend_action += '<button class="btn btn-info m-2" id="print_laporan" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Cetak Surat</button><br>';
        extend_action += '</div>';
        jQuery('#action-sipd').append(extend_action);
    })

    function get_spt() {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: ajax.url,
            type: 'POST',
            data: {
                action: 'get_data_spt_by_id',
                api_key: ajax.api_key,
                id: '<?php echo $_GET['id_spt']; ?>',
                tahun_anggaran: '<?php echo $_GET['tahun_anggaran']; ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.status === 'success') {
                    window.nomor_spt = response.data.nomor_spt
                    get_sppd()

                    jQuery('.nomor_spt').text(response.data.nomor_spt)
                    jQuery('.dasar_spt').text(response.data.dasar_spt)
                    jQuery('.tujuan_spt').text(response.data.tujuan_spt)
                    jQuery('.tgl_spt').text(formatTanggal(response.data.tgl_spt))
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                jQuery('#wrap-loading').hide();
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }

    function get_sppd() {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: ajax.url,
            type: 'POST',
            data: {
                action: 'get_data_sppd_by_nomor',
                api_key: ajax.api_key,
                nomor_spt: nomor_spt,
                tahun_anggaran: '<?php echo $_GET['tahun_anggaran']; ?>'
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();
                console.log(response);
                if (response.status === 'success') {
                    jQuery('.namaSkpd').text(response.data.skpd.nama_skpd)
                    jQuery('.namaKepala').text(response.data.skpd.namakepala)
                    jQuery('.pangkatKepala').text(response.data.skpd.pangkatkepala)
                    jQuery('.nipKepala').text(response.data.skpd.nipkepala)
                    jQuery('#list_pegawai').html(response.data.html)
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                jQuery('#wrap-loading').hide();
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }
</script>