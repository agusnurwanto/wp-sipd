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
    .document-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }

    .main-table {
        width: 100%;
    }

    .main-table td {
        border: 1px solid #000;
        padding: 8px;
        vertical-align: top;
    }

    .number-column {
        width: 5%;
        text-align: center;
        font-weight: bold;
    }

    .left-section {
        width: 45%;
    }

    .right-section {
        width: 45%;
    }

    .left-section table {
        border: none;
    }

    .right-section table {
        border: none;
    }

    .detail-table {
        width: 100%;
        margin: 0;
    }

    .detail-table td {
        border: none;
        padding: 2px;
    }

    .colon-spacing {
        width: 15px;
        text-align: center;
    }

    .signature-section {
        text-align: center;
        margin-top: 20px;
    }

    .full-width {
        width: 100%;
    }

    .notice-text {
        text-align: justify;
        padding: 8px;
    }

    .signature-name {
        margin-top: 20px;
        text-align: center;
    }

    .signature-field {
        height: 60px;
    }
    .signature-field-2 {
        height: 140px;
    }

    .input-field {
        width: 250px;
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
    <div class="document-container" contenteditable="true" id="cetak">
        <table class="main-table">
            <!-- Section I -->
            <tr>
                <td class="number-column">I</td>
                <td class="left-section"></td>
                <td class="right-section">
                    <table class="detail-table">
                        <tr>
                            <td>Berangkat dari</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field berangkat"></td>
                        </tr>
                        <tr>
                            <td>Ke</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field tujuan"></td>
                        </tr>
                        <tr>
                            <td>Pada tanggal</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field" id="tglBerangkat"></td>
                        </tr>
                    </table>
                    <div class="signature-section">
                        <div class="namaSkpd">xxxxxxxxxxxxxxxxxxxxxxxxxx</div>
                        <div>Selaku Pengguna Anggaran</div>
                        <br><br>
                        <div class="signature-name namaKepala">xxxxxxxxxxxxxxxxxxxxxxxxxx</div>
                        <div class="pangkatKepala">xxxxxxxxxxxxxxxxxxxx</div>
                        <div>NIP. <span class="nipKepala">xxxxxxxxxxxxxxxxxxxxxxxxxx</span></div>
                    </div>
                </td>
            </tr>

            <!-- Section II -->
            <tr>
                <td class="number-column">II</td>
                <td class="left-section">
                    <table class="detail-table">
                        <tr>
                            <td>Tiba di</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field tujuan"></td>
                        </tr>
                        <tr>
                            <td>Pada tanggal</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field" id="tglSampai"></td>
                        </tr>
                        <tr>
                            <td class="signature-field-2"></td>
                        </tr>
                    </table>
                </td>
                <td class="right-section">
                    <table class="detail-table">
                        <tr>
                            <td>Berangkat dari</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td>Ke</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td>Pada tanggal</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td class="signature-field-2"></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Section III -->
            <tr>
                <td class="number-column">III</td>
                <td class="left-section">
                    <table class="detail-table">
                        <tr>
                            <td>Tiba di</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td>Pada tanggal</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td class="signature-field-2"></td>
                        </tr>
                    </table>
                </td>
                <td class="right-section">
                    <table class="detail-table">
                        <tr>
                            <td>Berangkat dari</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td>Ke</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td>Pada tanggal</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td class="signature-field-2"></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Section IV-->
            <tr>
                <td class="number-column">IV</td>
                <td class="left-section">
                    <table class="detail-table">
                        <tr>
                            <td>Tiba di</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td>Pada tanggal</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td class="signature-field-2"></td>
                        </tr>
                    </table>
                </td>
                <td class="right-section">
                    <table class="detail-table">
                        <tr>
                            <td>Berangkat dari</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td>Ke</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td>Pada tanggal</td>
                            <td class="colon-spacing">:</td>
                            <td class="input-field"></td>
                        </tr>
                        <tr>
                            <td class="signature-field-2"></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Section V -->
            <tr>
                <td class="number-column">V</td>
                <td colspan="2">
                    <div>Tiba kembali di : <span id="tglKembali"></span> </div>
                    <div>(tempat kedudukan) : <span class="berangkat"></span> </div>
                    <div>Telah diperiksa dengan keterangan bahwa perjalanan tersebut diatas benar dilakukan atas perintahnya dan semata-mata untuk kepentingan jabatan dalam waktu yang sesingkat-singkatnya.</div>
                    <table class="full-width mt-4">
                        <tr>
                            <td style="width: 50%; text-align: center;">
                                <div class="namaSkpd">xxxxxxxxxxxxxxxxxxxxxxxxxx</div>
                                <div>Selaku Pengguna Anggaran</div>
                                <br><br>
                                <div class="signature-name namaKepala">xxxxxxxxxxxxxxxxxxxxxxxxxx</div>
                                <div class="pangkatKepala">xxxxxxxxxxxxxxxx</div>
                                <div>NIP. <span class="nipKepala">xxxxxxxxxxxxxxxxxxxxxxxxxx</span></div>
                            </td>
                            <td style="width: 50%; text-align: center;">
                                <div class="namaSkpd">xxxxxxxxxxxxxxxxxxxxxxxxxx</div>
                                <div>Selaku Pengguna Anggaran</div>
                                <br><br>
                                <div class="signature-name namaKepala">xxxxxxxxxxxxxxxxxxxxxxxxxx</div>
                                <div class="pangkatKepala">xxxxxxxxxxxxxxxx</div>
                                <div>NIP. <span class="nipKepala">xxxxxxxxxxxxxxxxxxxxxxxxxx</span></div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Section VI -->
            <tr>
                <td class="number-column">VI</td>
                <td colspan="2">Catatan lain - lain : </td>
            </tr>

            <!-- Section VII -->
            <tr>
                <td class="number-column">VII</td>
                <td colspan="2" class="notice-text">
                    <div class="font-weight-bold">PERHATIAN</div>
                    <div>Pejabat yang berwenang menerbitkan SPPD, pegawai yang melakukan perjalanan dinas, para pejabat yang mengesahkan tanggal berangkat/tiba serta bendaharawan bertanggung jawab berdasarkan peraturan-peraturan keuangan negara, apabila negara menderita rugi akibat kesalahan, kelaiaian dan kealpaannya.</div>
                </td>
            </tr>
        </table>
    </div>
</body>
<script>
    jQuery(document).ready(function() {
        get_sppd()
        var extend_action = '';
        extend_action += '<button class="btn btn-info m-2" id="print_laporan" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Cetak Surat</button>';
        extend_action += '</div>';
        jQuery('#action-sipd').append(extend_action);
    })

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
                if (response.status === 'success') {

                    window.nomor_spt = response.data.nomor_spt
                    get_spt_by_nomor()

                  
                    jQuery('.berangkat').text(response.data.tempat_berangkat)
                    jQuery('.tujuan').text(response.data.tempat_tujuan)
                    jQuery('#tglBerangkat').text(formatTanggal(response.data.tgl_berangkat))
                    jQuery('#tglSampai').text(formatTanggal(response.data.tgl_sampai))
                    jQuery('#tglKembali').text(formatTanggal(response.data.tgl_kembali))

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

    function get_spt_by_nomor() {
        jQuery.ajax({
            url: ajax.url,
            type: 'POST',
            data: {
                action: 'get_data_spt_by_nomor',
                api_key: ajax.api_key,
                nomor_spt: nomor_spt,
                tahun_anggaran: '<?php echo $_GET['tahun_anggaran']; ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    jQuery('.namaKepala').text(response.data.namakepala)
                    jQuery('.namaSkpd').text(response.data.nama_skpd)
                    jQuery('.nipKepala').text(response.data.nipkepala)
                    jQuery('.pangkatKepala').text(response.data.pangkatkepala)
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