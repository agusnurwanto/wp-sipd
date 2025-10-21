<?php
if (!defined('WPINC')) {
    die;
}
global $wpdb;

$input = shortcode_atts(array(
    'tahun_anggaran' => '2023'
), $atts);

if (!empty($_GET) && !empty($_GET['id_skpd'])) {
    $id_skpd = $_GET['id_skpd'];
}

?>
<div class="container mt-5" id="cetak" title="">
    <div class="text-center mb-5">
        <h3>PERTlMBANGAN MANAJEMEN: ANGGARAN TAHUN <?php echo $input['tahun_anggaran']; ?><br>
            PEMERINTAH <?php echo strtoupper(get_option('_crb_daerah')); ?></h3>
    </div>

    <table id="table-anggaran">
        <thead>
            <tr>
                <th class="kiri kanan atas bawah text_tengah text_blok" style="height: 10vh; vertical-align: middle;">No</th>
                <th class="kiri kanan atas bawah text_tengah text_blok" style="height: 10vh; vertical-align: middle;">Nama Program</th>
                <th class="kiri kanan atas bawah text_tengah text_blok" style="height: 10vh; vertical-align: middle;">Nama OPD</th>
                <th class="kiri kanan atas bawah text_tengah text_blok" style="height: 10vh; vertical-align: middle;">Anggaran</th>
            </tr>
            <tr>
                <td class="kiri kanan atas bawah text_tengah">(1)</td>
                <td class="kiri kanan atas bawah text_tengah">(2)</td>
                <td class="kiri kanan atas bawah text_tengah">(3)</td>
                <td class="kiri kanan atas bawah text_tengah">(4)</td>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>
<script>
    jQuery(document).ready(() => {
        run_download_excel();

        let actionBtn = `<button class="btn btn-info ml-2" onclick="window.print();"><span class="dashicons dashicons-printer"></span> Cetak</button>`;
        jQuery("#action-sipd").append(actionBtn)
        getTable();
    })

    function getRenstraProgram() {
        return jQuery.ajax({
            method: 'post',
            url: ajax.url,
            dataType: 'json',
            data: {
                'action': 'get_program_manrisk_anggaran',
                'api_key': ajax.api_key,
                'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                'id_skpd': <?php echo $_GET['id_skpd']; ?>
            },
        });
    }

    async function getTable() {
        jQuery(`#wrap-loading`).show();

        const allPrograms = await getRenstraProgram();

        let tbody = ``;
        if (!allPrograms.data) {
            tbody += `
            <tr>
                <td colspan="4">Data tidak tersedia.</td>
            </tr>`;
        }

        const dataUnit = allPrograms.data.unit;
        let no = 1;
        let totalPagu = 0;

        allPrograms.data.data.forEach(data => {
            tbody += `
            <tr>
                <td class="kiri kanan atas bawah text_tengah">${no++}</td>
                <td class="kiri kanan atas bawah kiri">${data.kode_program} ${data.nama_program}</td>
                <td class="kiri kanan atas bawah kiri">${dataUnit.nama_skpd}</td>
                <td class="kiri kanan atas bawah kanan text_kanan">${formatRupiah(data.total_pagu)}</td>
            </tr>`;

            totalPagu += Number(data.total_pagu);
        });

        jQuery(`#table-anggaran tbody`).html(tbody);
        jQuery(`#table-anggaran tfoot`).html(`
            <tr>
                <td class="kiri kanan atas bawah kanan text_tengah" colspan="3">Total</td>
                <td class="kiri kanan atas bawah kanan text_kanan">${formatRupiah(totalPagu)}</td>
            </tr>
        `);
        jQuery(`#cetak`).attr("title", `Pertimbangan Manajemen Anggaran | ${dataUnit.nama_skpd} - ${dataUnit.tahun_anggaran}`);

        jQuery(`#wrap-loading`).hide();
    }
</script>