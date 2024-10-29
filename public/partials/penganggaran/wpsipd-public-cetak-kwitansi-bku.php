<?php
if (!defined('WPINC')) {
    die;
}
if (empty($_GET) || empty($_GET['id_bku']) || empty($_GET['tahun_anggaran'])) {
    die('<h1 class="text-center">Id BKU / Tahun Anggaran Kosong!</h1>');
}
global $wpdb;
?>
<style>
    #tableKwitansi,
    #tableKwitansi td,
    #tableKwitansi th {
        border: 0;
        padding: 0px;
    }

    /* .kwitansi {
        background-color: #ffffff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    } */

    .signature-line {
        border-top: 1px solid #f8f9fa;
        margin-top: 50px;
    }

    .signature-table {
        width: 100%;
        margin-top: 50px;
    }

    .signature-table td {
        width: 25%;
        text-align: center;
        vertical-align: top;
        padding: 10px;
    }

    .signature-title {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .signature-role {
        margin-bottom: 5px;
    }

    .signature-name {
        font-weight: bold;
        margin-top: 10px;
    }

    .amount-box {
        background-color: #e9ecef;
        border-radius: 0.25rem;
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
    <div id="cetak" style="padding: 15px">
        <h1 class="text-center mb-4">KWITANSI</h1>
        <div class="row mb-4">
            <div class="col-12">
                <table id="tableKwitansi">
                    <tbody>
                        <tr>
                            <td class="font-weight-bold">Sudah Terima</td>
                            <td class="font-weight-bold">:</td>
                            <td>
                                <p contenteditable="true">Bendahara Pengeluaran Dinas/Badan xxxxxxxxxxxxxxxxx</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Terbilang</td>
                            <td class="font-weight-bold">:</td>
                            <td>
                                <p class="font-weight-bold terbilang"></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Buat Pembayaran</td>
                            <td class="font-weight-bold">:</td>
                            <td>
                                <p contenteditable="true" class="uraian"></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Jumlah Uang</td>
                            <td class="font-weight-bold">:</td>
                            <td>
                                <p class="font-weight-bold pagu"></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <table class="signature-table" id="tableKwitansi" contenteditable="true">
            <tr>
                <td>
                    <div class="signature-title">Mengetahui:</div>
                    <div class="signature-role">Pengguna Anggaran/ PPK</div>
                    <div class="signature-role">Nama Instansi xxxxxx</div>
                </td>
                <td>
                    <div class="signature-title">Setuju dibayar</div>
                    <div class="signature-role">PPTK</div>
                </td>
                <td>
                    <div class="signature-title">Lunas dibayar</div>
                    <div class="signature-role">Bendahara Pengeluaran</div>
                </td>
                <td>
                    <div class="signature-title">Penerima</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-name text_uppercase">xxxxxxxxxxxx</div>
                    <div>xxxxxxxxxxxx</div>
                    <div>NIP. xxxxxxxxxxxx</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-name text_uppercase pptk-name "></div>
                    <div>xxxxxxxxxxxx</div>
                    <div>NIP. xxxxxxxxxxxx</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-name text_uppercase">xxxxxxxxxxxx</div>
                    <div>xxxxxxxxxxxx</div>
                    <div>NIP. xxxxxxxxxxxx</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-name text_uppercase">xxxxxxxxxxxx</div>
                    <div>xxxxxxxxxxxx</div>
                    <div>NIP. xxxxxxxxxxxx</div>
                </td>
            </tr>
        </table>
    </div>
</body>
<script>
    jQuery(document).ready(function() {
        window.id_npd = ''

        get_bku();

        var extend_action = '';
        extend_action += '<button class="btn btn-info m-2" id="print_laporan" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Cetak Laporan</button><br>';
        extend_action += '</div>';
        jQuery('#action-sipd').append(extend_action);
    });

    function get_bku() {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'get_data_buku_kas_umum_by_id',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                id: '<?php echo $_GET['id_bku']; ?>',
                tahun_anggaran: '<?php echo $_GET['tahun_anggaran']; ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    jQuery('.terbilang').text(terbilang(parseInt(response.data.pagu)) + ' Rupiah');
                    jQuery('.pagu').text('Rp. ' + formatAngka(parseInt(response.data.pagu)));
                    jQuery('.uraian').text(response.data.uraian);

                    window.id_npd = response.data.id_npd;

                    get_pptk_by_npd();
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

    function get_pptk_by_npd() {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'get_nota_panjar_by_id',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                id: window.id_npd,
                tahun_anggaran: '<?php echo $_GET['tahun_anggaran']; ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    jQuery('.pptk-name').text(response.data.pptk_name.toUpperCase());
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
</script>