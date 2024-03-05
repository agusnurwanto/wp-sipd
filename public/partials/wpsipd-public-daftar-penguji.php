<?php
global $wpdb;
$api_key = get_option('_crb_api_key_extension');
$url = admin_url('admin-ajax.php');
date_default_timezone_set('Asia/Jakarta');

$input = shortcode_atts(array(
    'id_skpd' => '',
    'tahun_anggaran' => ''
), $atts);

$tanggal = $this->tanggalan(date("Y-m-d"));
$waktu = date("Y-m-d H:i:s");

$body = '';

$unit = $wpdb->get_row($wpdb->prepare("
    SELECT
        *
    FROM data_unit
    WHERE id_skpd=%d
        AND tahun_anggaran=%d
        AND active=1
", $input['id_skpd'], $input['tahun_anggaran']), ARRAY_A);

$get_sp2d = $wpdb->get_results(
    $wpdb->prepare('
        SELECT 
            *
        FROM data_sp2d_sipd_detail
        WHERE tahun_anggaran = %d
    ', $input['tahun_anggaran']),
    ARRAY_A
);

$nomor = 0;
$total_bruto = 0;
$total_ppn = 0;
$total_pph = 0;
$total_lainnya = 0;
$total_netto = 0;
foreach ($get_sp2d as $k => $val) {
$terlampir = '-';
    $nomor++;
    if (!empty($val['id_sp_2_d'])) {
        $get_sp2d_detail = "
            SELECT 
                s.*,
                k.*
            FROM data_sp2d_sipd_detail s
            LEFT JOIN data_sp2d_sipd_ri k on s.id_sp_2_d=k.id_sp_2_d
                AND s.tahun_anggaran=k.tahun_anggaran
            WHERE s.active=1
        ";
    }else{
        $get_sp2d_detail = "
            SELECT 
                s.*,
                k.*
            FROM data_sp2d_sipd_detail s
            LEFT JOIN data_sp2d_sipd_ri k on s.id_sp_2_d=k.id_sp_2_d
                AND s.tahun_anggaran=k.tahun_anggaran
            WHERE s.active=1
        ";
    }
    if ($val['nama_pihak_ketiga'] != 'terlampir' && $val['nama_pihak_ketiga'] != 'Terlampir' && $val['nama_pihak_ketiga'] != 'TERLAMPIR') {
        $terlampir = $val['nama_pihak_ketiga'];
    }

    // print_r($get_sp2d_detail); die($wpdb->last_query);
    $get_tanggal = $val['tanggal_sp_2_d'];
    $tanggal_format = date("Y-m-d", strtotime($get_tanggal));

    $body .= '
        <tr>
            <td class="kiri atas kanan bawah text-center" style="width: 30px;">'.$nomor.'</td>
            <td class="atas kanan bawah text-center" style="width: 70px;">' . $tanggal_format . '</td>
            <td class="atas kanan bawah text-center" style="width: 80px;">' . $val['nomor_sp_2_d'] . '</td>
            <td class="atas kanan bawah text-right" style="width: 100px;">'.number_format($val['nilai_sp2d'],0,",",".").'</td>
            <td class="atas kanan bawah text-center" style="width: 100px;"></td>
            <td class="atas kanan bawah text-center" style="width: 100px;"></td>
            <td class="atas kanan bawah text-center" style="width: 100px;"></td>
            <td class="atas kanan bawah text-center" style="width: 100px;"></td>
            <td class="atas kanan bawah text-center" style="width: 120px;">' . $terlampir . '</td>
            <td class="atas kanan bawah text-center" style="width: 100px;">' . $val['nomor_rekening'] . ' / ' . $val['nama_bank'] . '</td>
        </tr>
    ';

    $total_bruto += $val['nilai_sp2d'];
}

?>
<style type="text/css">
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
    <label for="filter_tanggal">Pilih Tanggal : 
    <input type="date" style="margin-left: 10px; width: 250px;" name="filter_tanggal" id="filter_tanggal"></label>
    </input>
    <button style="margin: 10px 9px 10px 10px; height: 40px; width: 65px;"onclick="submit_tanggal();" class="btn btn-sm btn-primary">Cari</button>
</div>
<div class="cetak container-fluid" style="margin-top: 15px;">
    <tr class="no_padding no_break">
        <td class="no_break">
            <table width="100%" style="border-spacing: 0px;">
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
                        <td class="atas kanan" >: <?php echo $val['nama_bank']; ?></td>
                    </tr>
                    <tr >
                        <td style="width: 160px;" class="kiri">No Rekening</td>
                        <td class="kanan " colspan="2">: <?php echo $val['nomor_rekening']; ?></td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</div>
<div class="cetak container-fluid">
    <tr class="no_padding no_break">
        <td colspan="10" class="no_break">
            <table width="100%" style="border-spacing: 0px;">
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
                    <tfoot>
                        <tr>
                            <td class="atas bawah kanan kiri text_kanan" colspan="3"></td>
                            <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_bruto,0,",","."); ?></td>
                            <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_ppn,0,",","."); ?></td>
                            <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_pph,0,",","."); ?></td>
                            <td class="atas bawah kanan kiri text_kanan" ><?php echo number_format($total_lainnya,0,",","."); ?></td>
                            <td class="atas bawah kanan kiri text_kanan" ><?php echo number_format($total_netto,0,",","."); ?></td>
                            <td class="atas bawah kanan kiri text_kanan" colspan="2"></td>
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
<div class="cetak container-fluid">
    <tr class="no_padding no_break">
        <td colspan="10" class="no_break">
            <table width="100%" style="border-spacing: 0px;">
                <thead>
                    <tr>
                        <th width="15" class="kiri" colspan="2"></th>
                        <th width="90" colspan="7"><b></b></th>
                        <th width="150" class="kanan" colspan="7"><b></b></th>
                    </tr> 
                    <tr>
                        <th width="90"class="kiri text_kiri" colspan="2">TOTAL SP2D S/D DAFTAR PENGUJI YANG LALU</th>
                        <th width="90" class="text_kiri"><b>:</b></th>
                        <th class="text_kanan"></th>
                        <th class="kanan" colspan="6"></th>
                    </tr> 
                    <tr>
                        <th width="90"class="kiri text_kiri" colspan="2">TOTAL SP2D DAFTAR PENGUJI INI</th>
                        <th width="90" class="text_kiri"><b>:</b></th>
                        <th class="text_kanan"><?php echo number_format($total_bruto,0,",","."); ?></th>
                        <th class="kanan" colspan="6"></th>
                    </tr> 
                    <tr>
                        <th width="15" class="kiri text_kiri" colspan="2">TOTAL SP2D S/D DAFTAR PENGUJI INI</th>
                        <th width="90" class="text_kiri" colspan="7"><b>:</b></th>
                        <th width="150" class="kanan text_kiri" colspan="7"><b></b></th>
                    </tr> 
                </thead>
                <tbody>
                </tbody>
            </table>
        </td>
    </tr>
</div>
<div class="cetak container-fluid" contenteditable="true">
    <tr class="no_padding no_break">
        <td colspan="10" class="no_break">
            <table width="100%" style="border-spacing: 0px;">
                <thead>
                <tr class="no_break">
                    <td class="kiri bawah no_break" width="150" valign="top">
                        <table width="100%" style="border-spacing: 0px;"><br><br>
                            <tr>
                                <td colspan="3" class="text_tengah">Mengetahui,</td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text_tengah text_15">KEPALA BPPKAD</th>
                            </tr>
                            <tr>
                                <td colspan="3" height="80">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text_tengah"></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text_tengah">NIP</td>
                            </tr>
                        </table>
                    </td>
                    <td class="bawah no_break" width="150" valign="top">
                        <table width="100%" style="border-spacing: 0px;"><br><br>
                            <tr>
                                <td colspan="12"></td>
                        </table>
                    </td>
                    <td class="bawah no_break" width="250" valign="top">
                        <table width="100%" style="border-spacing: 0px;"><br><br>
                            <tr>
                                <td colspan="12"></td>
                            </tr>
                        </table>
                    </td>
                    <td class="kanan bawah no_break" width="150" valign="top">
                        <table width="100%" style="border-spacing: 0px;"><br><br>
                            <tr>
                                <td colspan="3" class="text_tengah"><?php echo get_option('_crb_daerah'); ?>, <?php echo $tanggal; ?></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text_tengah text_15">KUASA BUD</th>
                            </tr>
                            <tr>
                                <td colspan="3" height="80">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text_tengah"></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text_tengah">NIP </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot contenteditable="true">
                    <tr>
                        <th class="atas bawah kanan kiri" colspan="10"><i>DAFTAR PENGUJI ( Dicetak pada <?php echo $waktu; ?>)</th>
                    </tr>
                </tfoot>
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