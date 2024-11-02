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

$sppd_page = $this->generatePage(
    'Cetak SPPD Belakang (Surat Perintah Perjalanan Dinas)',
    $_GET['tahun_anggaran'],
    '[cetak_sppd_belakang]',
    false
);
?>
<style>
    .document-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    .header-table {
        width: 100%;
        margin-bottom: 10px;
    }

    .header-table td {
        vertical-align: top;
        padding: 0;
    }

    .main-table {
        width: 100%;
        border-collapse: collapse;
    }

    .main-table td,
    .main-table th {
        border: 1px solid #000;
        padding: 4px 8px;
        vertical-align: top;
    }

    .number-column {
        width: 30px;
        text-align: center;
    }

    .label-column {
        width: 40%;
    }

    .sub-row td:first-child {
        padding-left: 20px;
    }

    .document-title {
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        margin: 10px 0;
        text-decoration: underline;
    }

    .footer-text {
        text-align: center;
        margin-top: 20px;
    }

    [contenteditable="true"] {
        min-height: 18px;
    }

    .no-border {
        border: none !important;
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

    table,
    th,
    tr,
    td {
        border: none;
    }

    td[contenteditable="true"] {
        background-color: transparent;
    }

    .signature-table {
        border-collapse: collapse;
    }
</style>

<body>
    <div id="action-sipd"></div>
    <div id="cetak" class="document-container" contenteditable="true">
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
                    <h4 class="mb-1">PEMERINTAH <?php echo strtoupper(get_option('_crb_daerah')); ?></h4>
                    <h4 class="mb-1">xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</h4>
                    <div>Jl. xxxxxxxxxxxxxxxxxxxxxxxxxxxxx</div>
                    <div>Telp. xxxxxxxxxxxxxxxxxxxxxxxxx</div>
                </td>
            </tr>
        </table>

        <table class="signature-table" style="width: 100%;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="text-align: left; width: 40%;">Lembar No.</td>
                            <td style="text-align: left;">:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Kode No.</td>
                            <td style="text-align: left;">:</td>
                            <td><span></span></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Nomor</td>
                            <td style="text-align: left;">:</td>
                            <td><span id="nomorSppd"></span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="document-title">SURAT PERJALANAN DINAS</div>

        <table class="main-table">
            <tr>
                <td class="number-column">1</td>
                <td class="label-column">Pengguna Anggaran</td>
                <td id="penggunaAnggaran">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column">2</td>
                <td class="label-column">a. Nama pegawai</td>
                <td id="namaPegawai">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column"></td>
                <td class="label-column">b. NIP</td>
                <td id="nip">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column">3</td>
                <td class="label-column">a. Pangkat dan golongan ruang</td>
                <td id="pangkatGol">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column"></td>
                <td class="label-column">b. Jabatan</td>
                <td id="jabatan">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column"></td>
                <td class="label-column">c. Instansi</td>
                <td id="instansi">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column"></td>
                <td class="label-column">d. Tingkat menurut Peraturan</td>
                <td id="tingkat">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column">4</td>
                <td class="label-column">Maksud perjalanan</td>
                <td id="maksudPerjalanan">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column">5</td>
                <td class="label-column">Alat angkut yang dipergunakan</td>
                <td id="alatAngkut">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column">6</td>
                <td class="label-column">a. Tempat berangkat</td>
                <td id="tempatBerangkat">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column"></td>
                <td class="label-column">b. Tempat tujuan</td>
                <td id="tempatTujuan">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column">7</td>
                <td class="label-column">a. Lamanya perjalanan dinas</td>
                <td id="lamaPerjalanan">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column"></td>
                <td class="label-column">b. Tanggal berangkat</td>
                <td id="tanggalBerangkat">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column"></td>
                <td class="label-column">c. Tanggal harus kembali</td>
                <td id="tanggalKembali">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column">8</td>
                <td colspan="2" id="pembebananAnggaran">Pembebanan Anggaran</td>
            </tr>
            <tr>
                <td class="number-column"></td>
                <td class="label-column">a. Instansi</td>
                <td id="pembebananInstansi">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column"></td>
                <td class="label-column">b. Mata Anggaran</td>
                <td id="mataAnggaran">xxxxxxxxxxxxx</td>
            </tr>
            <tr>
                <td class="number-column">9</td>
                <td class="label-column">Keterangan lain - lain</td>
                <td id="keterangan"></td>
            </tr>
        </table>

        <table class="signature-table" style="width: 100%;">
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
                            <td style="text-align: center; padding-bottom : 0;">xxxxxxxxxxxxxxxx</td>
                        </tr>
                        <tr>
                            <td style="text-align: center; padding-top : 0;">Selaku Pengguna Anggaran</td>
                        </tr>
                        <tr>
                            <td style="height: 80px;"></td> <!-- Space for signature -->
                        </tr>
                        <tr>
                            <td style="font-weight: 700; text-decoration: underline; text-align: center; padding-bottom : 0;" class="signature-name text_uppercase">xxxxxxxxxxxxxxxx</td>
                        </tr>
                        <tr>
                            <td style="text-align: center; padding-bottom : 0; padding-top : 0;">xxxxxxxxxxxxxxxx</td>
                        </tr>
                        <tr>
                            <td style="text-align: center; padding-top : 0;">NIP. xxxxxxxxxxxxxxxx</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
<script>
    jQuery(document).ready(function() {
        window.sppdBelakangUrl = '<?php echo $sppd_page; ?>'
        window.id_sppd = '<?php echo $_GET['id_sppd']; ?>'
        window.tahun_anggaran = '<?php echo $_GET['tahun_anggaran']; ?>'

        get_sppd()

        var extend_action = '';
        extend_action += '<button class="btn btn-info m-2" id="print_laporan" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Cetak Surat</button>';
        extend_action += '<button class="btn btn-primary m-2" id="sppd_belakang" onclick="sppdBelakangPage();"><i class="dashicons dashicons-controls-forward"></i> Cetak SPPD Belakang</button><br>';
        extend_action += '</div>';
        jQuery('#action-sipd').append(extend_action);
    });

    function get_sppd() {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: ajax.url,
            type: 'POST',
            data: {
                action: 'get_data_sppd_by_id',
                api_key: ajax.api_key,
                id: '<?php echo $_GET['id_sppd']; ?>',
                tahun_anggaran: '<?php echo $_GET['tahun_anggaran']; ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    let totalHari = hitungSelisihHari(response.data.tgl_berangkat, response.data.tgl_kembali)

                    jQuery('#nomorSppd').text(response.data.nomor_sppd)
                    jQuery('#alatAngkut').text(response.data.alat_angkut)
                    jQuery('#tempatBerangkat').text(response.data.tempat_berangkat)
                    jQuery('#tempatTujuan').text(response.data.tempat_tujuan)
                    jQuery('#tanggalBerangkat').text(formatTanggal(response.data.tgl_berangkat))
                    jQuery('#tanggalKembali').text(formatTanggal(response.data.tgl_kembali))
                    jQuery('#tanggalSppd').text(formatTanggal(response.data.tgl_ttd_sppd))
                    jQuery('#keterangan').text(response.data.keterangan)
                    jQuery('#lamaPerjalanan').text(totalHari + ' Hari')

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

    function hitungSelisihHari(tanggalAwal, tanggalAkhir) {
        const satuHari = 24 * 60 * 60 * 1000;
        const tglAwal = new Date(tanggalAwal);
        const tglAkhir = new Date(tanggalAkhir);

        const selisihWaktu = tglAkhir - tglAwal;
        const selisihHari = Math.round(selisihWaktu / satuHari);

        return selisihHari;
    }

    function sppdBelakangPage() {
        let newUrl = sppdBelakangUrl + '&id_sppd=' + id_sppd + '&tahun_anggaran=' + tahun_anggaran;
        window.open(newUrl, '_blank')
    }
</script>