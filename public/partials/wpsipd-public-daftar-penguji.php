<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

global $wpdb;

$input = shortcode_atts( array(
    'id_skpd' => '',
    'tahun_anggaran' => '2024'
), $atts );

$tanggal = $this->tanggalan(date("Y-m-d"));
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
        font-family: 'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
        padding: 0;
        font-size: 13px;
    }
    table {
        display: table;
        border-collapse: collapse;
        margin: 0;
    }
    .cetak{
        font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
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
<div class="cetak" contenteditable="true">
    <tr class="no_padding no_break">
        <td class="no_break">
            <table width="100%" class="cellpadding_5 no_break" style="border-spacing: 0px;">
                <tbody>
                    <tr>
                        <th class="atas bawah kiri">
                            <img style="margin: 12px 12px 12px 20px;" src="<?php 
                            if(!empty(get_option('_crb_logo_dashboard'))){
                                echo get_option('_crb_logo_dashboard');
                            }else{
                                echo 'http://via.placeholder.com/350x350';
                            }
                        ?>" width="70px">

                        </th>
                        <td class="atas bawah kanan text_tengah">
                            <h4 class="tengah" style="text-transform: uppercase;">Pemerintah <?php echo get_option('_crb_daerah') ?><br>Daftar Penguji<br></h4>
                            <p class="jarak-atas">Nomor: Tanggal: <?php echo $tanggal; ?></p>
                        </td>
                    </tr>
                    <tr >
                        <th style="margin-left: 10px;" class="atas kiri">Bank</th>
                        <th class="atas kanan" >:</th>
                    </tr>
                    <tr >
                        <th style="width: 160px;" class="kiri">No Rekening</th>
                        <th class="kanan " colspan="2">:</th>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</div>
<div class="cetak" contenteditable="true">
    <tr class="no_padding no_break">
        <td colspan="11" class="no_break">
            <table width="100%" class="cellpadding_5 no_break" style="border-spacing: 0px;">
                <thead>
                    <tr class="text_tengah">
                        <td width="15" class="atas kiri kanan bawah" rowspan="2">No.</td>
                        <td class="atas kanan bawah " rowspan="2">Tanggal</td>
                        <td class="atas bawah kanan " rowspan="2">NO SP2D</td>
                        <td class="atas bawah kanan " rowspan="2">Bruto</td>
                        <td class="atas bawah kanan " colspan="4">Potongan</td>
                        <td class="atas bawah kanan " rowspan="2">Netto</td>
                        <td class="atas bawah kanan " rowspan="2">NAMA BENDAHARA / REKANAN / PIHAK KE-3</td>
                        <td class="atas bawah kanan " rowspan="2">NO REKENING / BANK</td>
                    </tr> 
                    <tr>
                        <td class="kanan bawah text_tengah text_blok">PPN</td>
                        <td class="kanan bawah text_tengah text_blok">PPH</td>
                        <td class="kanan bawah text_tengah text_blok">Lainnya</td>
                        <td class="kanan bawah text_tengah text_blok">Harga</td>
                    </tr>
                    <tfoot>
                        <tr>
                            <th class="atas bawah kanan kiri" colspan="3"></th>
                            <th class="atas bawah kanan text-center" >&nbsp</th>
                            <th class="atas bawah kanan text-right" >&nbsp</th>
                            <th class="atas bawah kanan text-center" >&nbsp</th>
                            <th class="atas bawah kanan text-center" >&nbsp</th>
                            <th class="atas bawah kanan text-center" >&nbsp</th>
                            <th class="atas bawah kanan text-center" >&nbsp</th>
                            <th class="atas bawah kanan text-center" >&nbsp</th>
                            <th class="atas bawah kanan text-center" >&nbsp</th>
                        </tr>
                        <tr>
                            <th class="atas bawah kanan kiri" colspan="11">Daftar Penguji ( Dicetak pada <?php echo $tanggal; ?>)</th>
                        </tr>
                    </tfoot>
                </thead>
                <tbody>
                </tbody>
            </table>
        </td>
    </tr>
</div>
<script type="text/javascript">