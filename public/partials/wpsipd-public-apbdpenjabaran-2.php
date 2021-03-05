<?php
global $wpdb;
?>
<div id="cetak" title="Laporan APBD PENJABARAN Lampiran 2 Tahun Anggaran <?php echo $input['tahun_anggaran']; ?>">
    <table align="right" class="no-border no-padding" cellspacing="0" cellpadding="0" style="width:280px; font-size: 12px;">
        <tr>
            <td width="80" valign="top">Lampiran I </td>
            <td width="10" valign="top">:</td>
            <td colspan="3" valign="top">  Peraturan Bupati Magetan   </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td width="10">&nbsp;</td>
            <td width="60" class="text_kiri" style="padding-left: 0px;">Nomor</td>
            <td width="10">:</td>
            <td class="text_kiri">&nbsp;79 Tahun 2020</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td width="10">&nbsp;</td>
            <td width="60" class="text_kiri" style="padding-left: 0px;">Tanggal</td>
            <td width="10">:</td>
            <td class="text_kiri">&nbsp;28 Desember 2020</td>
        </tr>
    </table>
    <h4 style="text-align: center; font-size: 13px; margin: 10px auto; min-width: 450px; max-width: 550px; font-weight: bold;">KABUPATEN MAGETAN <br>RINCIAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI, PENDAPATAN, BELANJA DAN PEMBIAYAAN<br>TAHUN ANGGARAN <?php echo $input['tahun_anggaran']; ?></h4>
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok" rowspan="2">Kode</td>
                <td class="atas kanan bawah text_tengah text_blok" rowspan="2">Uraian</td>
                <td class="atas kanan bawah text_tengah text_blok" colspan="2">Jumlah</td>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <table width="25%" class="no-border no-padding" align="right" cellpadding="2" cellspacing="0" style="width:280px; font-size: 12px;">
        <tr><td colspan="3" class="text_tengah" height="20px"></td></tr>
        <tr><td colspan="3" class="text_tengah text_15">Bupati Magetan  </td></tr>
        <tr><td colspan="3" height="80">&nbsp;</td></tr>
        <tr><td colspan="3" class="text_tengah">SUPRAWOTO</td></tr>
        <tr><td colspan="3" class="text_tengah"></td></tr>
    </table>
</div>

<script type="text/javascript">
    run_download_excel();
</script>