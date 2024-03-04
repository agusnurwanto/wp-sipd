<?php
global $wpdb;
$api_key = get_option('_crb_api_key_extension');
$url = admin_url('admin-ajax.php');
date_default_timezone_set('Asia/Jakarta');

$input = shortcode_atts(array(
    'tahun_anggaran' => ''
), $atts);

$tanggal = $this->tanggalan(date("Y-m-d"));
$waktu = date("Y-m-d H:i:s");

$body = '';
$get_sp2d = $wpdb->get_results(
    $wpdb->prepare('
        SELECT 
            *
        FROM data_sp2d_sipd_ri
        WHERE tahun_anggaran = %d
    ', $input['tahun_anggaran']),
    ARRAY_A
);

$nomor = 0;
if (!empty($get_sp2d)) {
    foreach ($get_sp2d as $k => $v) {
    $get_tanggal = $v['tanggal_sp_2_d'];
    $tanggal_format = date("Y-m-d", strtotime($get_tanggal));
    $nomor++;
        $body .= '
            <tr>
                <td class="kiri atas kanan bawah text-center" style="width: 30px;">'.$nomor.'</td>
                <td class="atas kanan bawah text-center" style="width: 70px;">' . $tanggal_format . '</td>
                <td class="atas kanan bawah text-center" style="width: 80px;">' . $v['nomor_sp_2_d'] . '</td>
                <td class="atas kanan bawah text-right" style="width: 100px;">'.number_format($v['nilai_sp_2_d'],0,",",".").'</td>
                <td class="atas kanan bawah text-center" style="width: 100px;"></td>
                <td class="atas kanan bawah text-center" style="width: 100px;"></td>
                <td class="atas kanan bawah text-center" style="width: 100px;"></td>
                <td class="atas kanan bawah text-center" style="width: 100px;"></td>
                <td class="atas kanan bawah text-center" style="width: 120px;"></td>
                <td class="atas kanan bawah text-center" style="width: 100px;"></td>
            </tr>
        ';
    }
}

?>
<style type="text/css">
    .cellpadding_1 > tbody > tr > td, .cellpadding_1 > thead > tr > th {
        padding: 1px;
    }
    .cellpadding_2 > tbody > tr > td, .cellpadding_2 > thead > tr > th {
        padding: 2px;
    }
    .cellpadding_3 > tbody > tr > td, .cellpadding_3 > thead > tr > th {
        padding: 3px;
    }
    .cellpadding_4 > tbody > tr > td, .cellpadding_4 > thead > tr > th {
        padding: 4px;
    }
    .cellpadding_5 > tbody > tr > td, .cellpadding_5 > thead > tr > th {
        padding: 5px;
    }
    .no_padding, .no_padding>td {
        padding: 0 !important;
    }
    td, th {
        text-align: inherit;
        padding: inherit;
        display: table-cell;
        vertical-align: inherit;
    }
    .jarak-atas{
        margin-top: -20px;
    }
    table, td, th { 
        border: 0; 
    }
    body {
        display: block;
        margin: 8px;
        font-family: 'Arial',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
        padding: 0;
        font-size: 13px;
    }
    table {
        display: table;
        border-collapse: collapse;
        margin: 0;
    }
    .cetak{
        font-family:'Arial',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
        padding:0;
        margin:0;
        font-size:13px;
    }
    @media  print {
        @page  {
            size:auto;
            margin: 11mm 15mm 15mm 15mm;
        }
        body {
            width: 210mm;
            height: 297mm;
        }
        /*.footer { position: fixed; bottom: 0; font-size:11px; display:block; }
        .pagenum:after { counter-increment: page; content: counter(page); }*/
    }

    .no_break {
        break-inside: inherit;
    }
</style>
<div class="text-center" style="margin-top: 10px;">
    <label for="filter_tanggal">Pilih Tanggal : </label>
    <input type="date" style="margin-left: 10px;" name="filter_tanggal" id="filter_tanggal">
        
    </input>
    <button style="margin: 10px 9px 10px 10px; height: 40px; width: 65px;"onclick="submit_tanggal();" class="btn btn-sm btn-primary">Cari</button>
</div>
<div class="cetak">
    <tr class="no_padding no_break">
        <td class="no_break">
            <table width="100%" class="cellpadding_5 no_break" style="border-spacing: 0px;">
                <tbody>
                    <tr>
                        <th class="atas bawah kiri">
                            <img style="margin: 12px 12px 12px 20px;" src="
                            <?php 
                                if(!empty(get_option('_crb_logo_dashboard'))){
                                    echo get_option('_crb_logo_dashboard');
                                }else{
                                    echo 'http://via.placeholder.com/350x350';
                                }
                            ?>" width="70px">

                        </th>
                        <td class="atas bawah kanan text_tengah">
                            <h3 class="tengah" style="text-transform: uppercase;">Pemerintah <?php echo get_option('_crb_daerah') ?><br>Daftar Penguji<br></h3>
                            <p class="jarak-atas">Nomor: Tanggal: <?php echo $tanggal; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="margin-left: 10px;" class="atas kiri">Bank</td>
                        <td class="atas kanan" >: <?php echo $v['nama_rek_bp_bpp']; ?></td>
                    </tr>
                    <tr >
                        <td style="width: 160px;" class="kiri">No Rekening</td>
                        <td class="kanan " colspan="2">: <?php echo $v['no_rek_bp_bpp']; ?></td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</div>
<div class="cetak">
    <tr class="no_padding no_break">
        <td colspan="10" class="no_break">
            <table width="100%" class="cellpadding_5 no_break" style="border-spacing: 0px;">
                <thead>
                    <tr class="text_tengah">
                        <td width="15" class="atas kiri kanan bawah text_blok" rowspan="2">NO</td>
                        <td class="atas kanan bawah text_blok " rowspan="2">TANGGAL</td>
                        <td class="atas bawah text_blok kanan " rowspan="2">NO SP2D</td>
                        <td class="atas bawah text_blok kanan " rowspan="2">BRUTO</td>
                        <td class="atas bawah text_blok kanan " colspan="3">POTONGAN</td>
                        <td class="atas bawah text_blok kanan " rowspan="2">NETTO</td>
                        <td class="atas bawah text_blok kanan " rowspan="2">NAMA BENDAHARA / REKANAN / PIHAK KE-3</td>
                        <td class="atas bawah text_blok kanan " rowspan="2">NO REKENING / BANK</td>
                    </tr> 
                    <tr>
                        <td class="kanan bawah text_blok text_tengah text_blok">PPN</td>
                        <td class="kanan bawah text_blok text_tengah text_blok">PPh</td>
                        <td class="kanan bawah text_blok text_tengah text_blok">LAINNYA</td>
                    </tr>
                    <tfoot contenteditable="true">
                        <tr>
                            <th class="atas bawah kanan kiri" colspan="10"><i>DAFTAR PENGUJI ( Dicetak pada <?php echo $waktu; ?>)</th>
                        </tr>
                    </tfoot>
                </thead>
                <tbody id="data_body">
                    <?php echo $body; ?>
                </tbody>
            </table>
        </td>
    </tr>
</div>
<script type="text/javascript">
    function submit_tanggal(){
        var filter_tanggal = jQuery('#filter_tanggal').val();
        if(filter_tanggal == ''){
            return alert('Tanggal tidak boleh kosong!');
        }
        var url = window.location.href;
        url = url.split('?')[0]+'?tanggal_sp_2_d='+filter_tanggal;
        location.href = url;
    }
</script>