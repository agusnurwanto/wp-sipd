<?php
if (!defined('WPINC')) {
    die;
}
if (
    empty($_GET)
    || empty($_GET['id_sppd'])
    || empty($_GET['tahun_anggaran'])
) {
    die('<h1 class="text-center">Id SPPD / Tahun Anggaran Kosong</h1>');
}
?>
<style>
    @media print {
        #cetak {
            max-width: auto !important;
            height: auto !important;
        }

        #action-sipd {
            display: none;
        }
    }

    .document-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    table,
    th,
    tr,
    td {
        border: none;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    .header-table td {
        vertical-align: top;
        padding: 5px;
    }

    .logo {
        width: 80px;
    }

    .agency-name {
        text-align: center;
        font-weight: bold;
        font-size: 14px;
    }

    .main-table th,
    .main-table td {
        border: 1px solid #000;
        padding: 5px;
    }

    .main-table th {
        text-align: center;
        font-weight: bold;
    }

    .amount {
        text-align: right;
    }

    .total-row {
        font-weight: bold;
    }

    .text-center {
        text-align: center;
    }

    .bottom-line {
        border-top: 2px solid #000;
        margin: 20px 0;
    }
    
    .bottom-kop-line {
        border-top: 4px solid #000;
        margin: 20px 0;
    }

    .signature-table td {
        vertical-align: top;
        padding: 5px;
    }
</style>

<body>
    <div id="action-sipd"></div>
    <div class="document-container" contenteditable="true">
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
                    <h4 contenteditable="true" class="mb-1">xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</h4>
                    <div contenteditable="true">Jl. xxxxxxxxxxxxxxxxxxxxxxxxxxxxx</div>
                    <div contenteditable="true">Telp. xxxxxxxxxxxxxxxxxxxxxxxxx</div>
                </td>
                <td style="width: 100px;"></td>
            </tr>
            </tr>
        </table>
        <div class="bottom-kop-line"></div>

        <div class="text-center" style="font-weight: bold; margin: 20px 0;">
            RINCIAN BIAYA PERJALANAN DINAS
        </div>

        <table>
            <tr>
                <td>Lampiran SPPD Nomor</td>
                <td style="width: 2px;">:</td>
                <td style="width: 650px;"></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td style="width: 2px;">:</td>
                <td style="width: 650px;"></td>
            </tr>
        </table>

        <table class="main-table">
            <tr>
                <th style="width: 40px;">NO</th>
                <th>RINCIAN BIAYA</th>
                <th style="width: 150px;">JUMLAH</th>
                <th style="width: 250px;">KETERANGAN</th>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>Uang Harian</td>
                <td class="amount">xxxxxxxx</td>
                <td rowspan="6">Dibayarkan perjalanan dinas luar daerah/dalam daerah guna xxxxxxxxxxxxxxxxxxxxx xxxxxxxxxxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Taxi di daerah</td>
                <td class="amount">xxxxx</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Taxi di daerah tujuan</td>
                <td class="amount">xxxxx</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Hotel</td>
                <td class="amount">xxxxxxxx</td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>BBM / Transport</td>
                <td class="amount">xxxxxxx</td>
            </tr>
            <tr>
                <td class="text-center">6</td>
                <td>Uang Representasi</td>
                <td class="amount">xxxxxxxx</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" class="text-center">Jumlah</td>
                <td class="amount">xxx.xxx</td>
                <td></td>
            </tr>
        </table>

        <table style="margin: 20px 0;">
            <tr>
                <td style="width: 60%;">
                    Telah dibayar sejumlah<br>
                    Rp. xxx.xxx
                </td>
                <td>
                    <?php echo ucfirst(get_option('_crb_daerah')); ?>, xxxxx<br>
                    Telah menerima jumlah uang sebesar<br>
                    Rp. xxx.xxx
                </td>
            </tr>
        </table>

        <table class="signature-table">
            <tr>
                <td style="width: 50%;" class="text-center">
                    Bendahara Pengeluaran
                </td>
                <td style="width: 50%;" class="text-center">
                    Yang menerima
                </td>
            </tr>
            <tr>
                <td style="height: 60px;"></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">
                    <strong>xxxxxxxxxxxxxxxxxxxxx</strong>
                </td>
                <td class="text-center">
                    <strong>xxxxxxxxxxxxxxxxxxxxx</strong>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    NIP.xxxxxxxxxxxxxxxxxxxxx
                </td>
                <td class="text-center">
                    NIP. xxxxxxxxxxxxxxxxxxxxx
                </td>
            </tr>
        </table>

        <div class="bottom-line"></div>

        <div class="text-center" style="font-weight: bold; margin: 20px 0;">
            PERHITUNGAN SPPD RAMPUNG
        </div>

        <table>
            <tr>
                <td style="width: 250px;">Ditetapkan sejumlah</td>
                <td style="width: 2px;">:</td>
                <td>Rp.</td>
                <td style="text-align: right; width: 350px;">xxx.xxx</td>
                <td style="width: 400px;"></td>
            </tr>
            <tr>
                <td>Yang telah dibayar semula</td>
                <td style="width: 2px;">:</td>
                <td>Rp.</td>
                <td style="text-align: right;">xxxxxxxx</td>
            </tr>
            <tr>
                <td>Sisa kurang / lebih</td>
                <td style="width: 2px;">:</td>
                <td>Rp.</td>
                <td style="text-align: right;" class="atas">xxx.xxx</td>
            </tr>
        </table>

        <table class="signature-table" style="margin-top: 20px;">
            <tr>
                <td style="width: 50%;" class="text-center">
                    Mengetahui,<br>
                    Pengguna Anggaran
                </td>
                <td style="width: 50%;" class="text-center">
                    P P T K
                </td>
            </tr>
            <tr>
                <td style="height: 60px;"></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">
                    <strong>xxxxxxxxxxxxxxxxxxxxx</strong>
                </td>
                <td class="text-center">
                    <strong>xxxxxxxxxxxxxxxxxxxxx</strong>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    NIP. xxxxxxxxxxxxxxxxxxxxx
                </td>
                <td class="text-center">
                    NIP. xxxxxxxxxxxxxxxxxxxxx
                </td>
            </tr>
        </table>
    </div>
</body>
<script>
    jQuery(document).ready(() => {
        var extend_action = '';

        extend_action += '<button class="btn btn-info m-2" id="print_laporan" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Cetak Surat</button>';
        extend_action += '</div>';
        jQuery('#action-sipd').append(extend_action);
    })
</script>