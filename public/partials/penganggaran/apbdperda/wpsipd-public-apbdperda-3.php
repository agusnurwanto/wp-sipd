<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
global $wpdb;
$nama_pemda = get_option('_crb_daerah');
?>
<style>
</style>

<body>
    <div id="cetak" title="APBD PERDA Lampiran III" style="padding: 5px; overflow: auto;">
        <table align="right" class="no-border no-padding" style="width:280px; font-size: 12px;">
            <tr>
                <td width="80" class="align-top">Lampiran III </td>
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
        <h2 class="text-center text-uppercase">
            <?php echo $nama_pemda; ?><br>
            rincian apbd menurut urusan pemerintahan daerah, organisasi, program, kegiatan,<br>
            sub kegiatan, kelompok, jenis pendapatan, belanja, dan pembiayaan<br>
            tahun anggaran 2024
        </h2>
        <table class="table table-bordered">
            <tr>
                <th class="text-center text-uppercase" colspan="4">pendapatan daerah</th>
            </tr>
            <tr>
                <th class="text-center text-uppercase">kode rekening</th>
                <th class="text-center text-uppercase">uraian</th>
                <th class="text-center text-uppercase">jumlah</th>
                <th class="text-center text-uppercase">dasar hukum</th>
            </tr>
        </table>
    </div>
</body>
<script>

</script>