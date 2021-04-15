<?php
global $wpdb;
$skpd = $wpdb->get_row('SELECT * FROM `data_unit` where id_skpd='.$input['id_skpd'].' and tahun_anggaran='.$input['tahun_anggaran'].' and active=1', ARRAY_A);

$kode = explode('.', $skpd['kode_skpd']);
$urusan = $wpdb->get_row('SELECT nama_bidang_urusan FROM `data_prog_keg` where kode_bidang_urusan=\''.$kode[0].'.'.$kode[1].'\' and tahun_anggaran='.$input['tahun_anggaran'], ARRAY_A);
?>
<div id="cetak" title="Laporan APBD PENJABARAN Lampiran 2 Tahun Anggaran <?php echo $input['tahun_anggaran']; ?>">
    <table align="right" class="no-border no-padding" cellspacing="0" cellpadding="0" style="width:280px; font-size: 12px;">
        <tr>
            <td width="80" valign="top">Lampiran II </td>
            <td width="10" valign="top">:</td>
            <td colspan="3" valign="top" contenteditable="true">  Peraturan Bupati xxxxx   </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td width="10">&nbsp;</td>
            <td width="60" class="text_kiri" style="padding-left: 0px;">Nomor</td>
            <td width="10">:</td>
            <td class="text_kiri" contenteditable="true">&nbsp;xx Desember xxxx</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td width="10">&nbsp;</td>
            <td width="60" class="text_kiri" style="padding-left: 0px;">Tanggal</td>
            <td width="10">:</td>
            <td class="text_kiri" contenteditable="true">&nbsp;xx Desember xxx</td>
        </tr>
    </table>
    <h4 style="text-align: center; font-size: 13px; margin: 10px auto; min-width: 450px; max-width: 550px; font-weight: bold;">KABUPATEN MAGETAN <br>PENJABARAN PERUBAHAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI, PROGRAM, KEGIATAN, SUB KEGIATAN, KELOMPOK, JENIS, OBJEK, RINCIAN OBJEK, SUB RINCIAN OBJEK PENDAPATAN, BELANJA, DAN PEMBIAYAAN<br>TAHUN ANGGARAN <?php echo $input['tahun_anggaran']; ?></h4>
    <table cellspacing="2" cellpadding="0">
        <tbody>
            <tr>
                <td width="150">Urusan Pemerintahan</td>
                <td width="10">:</td>
                <td><?php echo $urusan['nama_bidang_urusan']; ?></td>
            </tr>
            <tr>
                <td width="100">Organisasi</td>
                <td width="10">:</td>
                <td><?php echo $skpd['kode_skpd'].'&nbsp;'.$skpd['nama_skpd']; ?></td>
            </tr>
        </tbody>
    </table>
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok" colspan="2">Kode Rekening</td>
                <td class="atas kanan bawah text_tengah text_blok">Uraian</td>
                <td class="atas kanan bawah text_tengah text_blok">Sebelum Perubahan</td>
                <td class="atas kanan bawah text_tengah text_blok">Sesudah Perubahan</td>
                <td class="atas kanan bawah text_tengah text_blok">Bertambah/(Berkurang)</td>
                <td class="atas kanan bawah text_tengah text_blok">Penjelasan</td>
                <td class="atas kanan bawah text_tengah text_blok">Keterangan</td>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <table width="25%" class="no-border no-padding" align="right" cellpadding="2" cellspacing="0" style="width:280px; font-size: 12px;">
        <tr><td colspan="3" class="text_tengah" height="20px"></td></tr>
        <tr><td colspan="3" class="text_tengah text_15" contenteditable="true">Bupati XXXX  </td></tr>
        <tr><td colspan="3" height="80">&nbsp;</td></tr>
        <tr><td colspan="3" class="text_tengah" contenteditable="true">XXXXXXXXXXX</td></tr>
        <tr><td colspan="3" class="text_tengah"></td></tr>
    </table>
</div>

<script type="text/javascript">
    run_download_excel();
</script>