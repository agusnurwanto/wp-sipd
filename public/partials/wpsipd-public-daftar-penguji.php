<?php
global $wpdb;
$api_key = get_option('_crb_api_key_extension');
$url = admin_url('admin-ajax.php');
date_default_timezone_set('Asia/Jakarta');

$input = shortcode_atts(array(
    'id_skpd' => '',
    'tahun_anggaran' => ''
), $atts);

$filter_tanggal = (date("Y-m-d"));
if (!empty($_GET) AND !empty($_GET['tanggal_sp_2_d'])) {
    $filter_tanggal = $_GET['tanggal_sp_2_d'];
}

$tanggal = $this->tanggalan($filter_tanggal);
$waktu = date("Y-m-d H:i:s");

$body = '';
// print_r($unit); die($wpdb->last_query);

$get_sp2d = $wpdb->get_results(
    $wpdb->prepare('
        SELECT 
            *
        FROM data_sp2d_sipd_ri
        WHERE tahun_anggaran = %d
            AND tanggal_sp_2_d LIKE %s
    ', $input['tahun_anggaran'], $filter_tanggal.'%'),
    ARRAY_A
);

$nama_bank_sumber = '';
$rek_bank_sumber = '';
$nomor = 0;
$total_bruto = 0;
$total_ppn = 0;
$total_pph = 0;
$total_lainnya = 0;
$total_netto = 0;
foreach ($get_sp2d as $k => $val) {
    $nomor++;
    $potongan = $wpdb->get_results($wpdb->prepare("
        SELECT 
            *
        FROM data_sp2d_sipd_detail_potongan
        WHERE active=1
            AND tahun_anggaran=%d
            AND id_sp_2_d=%d
    ", $input['tahun_anggaran'], $val['id_sp_2_d']), ARRAY_A);
    $total_potongan = 0;
    $ppn = 0;
    $pph = 0;
    $potongan_lainnya = 0;
    $potongan_text = array();
    if(!empty($potongan)){
        foreach($potongan as $p){
            if($p['id_pajak_potongan'] == 1){
                $ppn += $p['nilai_sp2d_pajak_potongan'];
            }else if(
                $p['id_pajak_potongan'] == 2
                || $p['id_pajak_potongan'] == 3
                || $p['id_pajak_potongan'] == 6
                || $p['id_pajak_potongan'] == 11
            ){
                $pph += $p['nilai_sp2d_pajak_potongan'];
            }else{
                $potongan_lainnya += $p['nilai_sp2d_pajak_potongan'];
            }
            $potongan_text[] = $p['nama_pajak_potongan'].' = '.$p['nilai_sp2d_pajak_potongan'];
            $total_potongan += $p['nilai_sp2d_pajak_potongan'];
        }
    }
    $potongan_text = implode('<br>', $potongan_text);
    $netto = $val['nilai_sp_2_d']-$total_potongan;

    $detail = $wpdb->get_row($wpdb->prepare("
        SELECT 
            *
        FROM data_sp2d_sipd_detail
        WHERE active=1
            AND tahun_anggaran=%d
            AND id_sp_2_d=%d
    ", $input['tahun_anggaran'], $val['id_sp_2_d']), ARRAY_A);
    // print_r($detail); //die($wpdb->last_query);
    $nama_pihak_ketiga = '';
    $no_rek_pihak_ketiga = '';
    $bank_pihak_ketiga = '';
    if(empty(!$detail)){
        $nama_bank_sumber = $detail['nama_bank'];
        $rek_bank_sumber = $detail['nomor_rekening'];
        $nama_pihak_ketiga = $detail['nama_pihak_ketiga'];
        $no_rek_pihak_ketiga = $detail['no_rek_pihak_ketiga'];
        $bank_pihak_ketiga = $detail['bank_pihak_ketiga'];
    }

    // print_r($get_sp2d_detail); die($wpdb->last_query);
    $get_tanggal = $val['tanggal_sp_2_d'];
    $tanggal_format = date("Y-m-d", strtotime($get_tanggal));

    $body .= '
        <tr id_sp_2_d="'.$val['id_sp_2_d'].'">
            <td class="kiri atas kanan bawah text-center" style="width: 30px;">'.$nomor.'</td>
            <td class="atas kanan bawah text-center" style="width: 70px;">' . $tanggal_format . '</td>
            <td class="atas kanan bawah text-center" style="width: 80px;">' . $val['nomor_sp_2_d'] . '</td>
            <td class="atas kanan bawah text-right" style="width: 100px;">'.number_format($val['nilai_sp_2_d'],0,",",".").'</td>
            <td class="atas kanan bawah text-right" style="width: 100px;">'.number_format($ppn,0,",",".").'</td>
            <td class="atas kanan bawah text-right" style="width: 100px;">'.number_format($pph,0,",",".").'</td>
            <td class="atas kanan bawah text-right" style="width: 100px;">'.number_format($potongan_lainnya,0,",",".").'</td>
            <td class="atas kanan bawah text-right" style="width: 100px;">'.number_format($netto,0,",",".").'</td>
            <td class="atas kanan bawah text-center" style="width: 120px;">' . $nama_pihak_ketiga . '</td>
            <td class="atas kanan bawah text-center" style="width: 100px;">' . $no_rek_pihak_ketiga . ' / ' . $bank_pihak_ketiga . '</td>
            <td class="atas kanan bawah" style="width: 100px;">'.$potongan_text.'</td>
            <td class="atas kanan bawah" style="width: 100px;">'.$val['keterangan_sp_2_d'].'</td>
        </tr>
    ';

    $total_bruto += $val['nilai_sp_2_d'];
    $total_ppn += $ppn;
    $total_pph += $pph;
    $total_lainnya += $potongan_lainnya;
    $total_netto += $netto;
}

$filter_tanggal_kemarin = date("Y-m-d", time() - 60 * 60 * 24);
$total_kemarin = $wpdb->get_var(
    $wpdb->prepare('
        SELECT 
            sum(nilai_sp_2_d)
        FROM data_sp2d_sipd_ri
        WHERE tahun_anggaran = %d
            AND tanggal_sp_2_d BETWEEN %s AND %s
    ', $input['tahun_anggaran'], $input['tahun_anggaran'].'-01-01 00:00:00', $filter_tanggal_kemarin.' 00:00:00'));
if(empty($total_kemarin)){
    $total_kemarin = 0;
}
$total_semua = $total_kemarin+$total_bruto;
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
        .aksi-filter { display:none; }
    }

    .no_break {
        break-inside: inherit;
    }
    .no_padding > td {
        padding: 0;
    }
</style>
<div class="text-center aksi-filter" style="margin-top: 10px;">
    <label for="filter_tanggal">Pilih Tanggal : 
    <input type="date" style="margin-left: 10px; width: 250px;" name="filter_tanggal" id="filter_tanggal" value="<?php echo $filter_tanggal; ?>"></label>
    </input>
    <button style="margin: 10px 9px 10px 10px; height: 40px; width: 65px;"onclick="submit_tanggal();" class="btn btn-sm btn-primary">Cari</button>
</div>
<div class="cetak container-fluid" style="margin-top: 15px;">
    <table width="100%" style="border-spacing: 0px;">
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
                                <p class="jarak-atas" contenteditable="true">Nomor: XXXXXX, Tanggal: <?php echo $tanggal; ?></p>
                            </td>
                        </tr>
                        <tr contenteditable="true">
                            <th style="margin-left: 10px;" class="atas kiri">Bank</th>
                            <td class="atas kanan" >: <?php echo $nama_bank_sumber; ?></td>
                        </tr>
                        <tr contenteditable="true">
                            <th style="width: 160px;" class="kiri">No Rekening</th>
                            <td class="kanan " colspan="2">: <?php echo $rek_bank_sumber; ?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
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
                            <td class="atas bawah text_blok kanan " rowspan="2">DETAIL POTONGAN</td>
                            <td class="atas bawah text_blok kanan " rowspan="2">KETERANGAN</td>
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
                                <td class="atas bawah kanan kiri text_kanan" colspan="4"></td>
                            </tr>
                        </tfoot>
                    </thead>
                    <tbody id="data_body">
                        <?php echo $body; ?>
                    </tbody>
                </table>
            </td>
        </tr>
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
                            <th class="text_kanan"><?php echo number_format($total_kemarin,0,",","."); ?></th>
                            <th class="kanan" colspan="9"></th>
                        </tr> 
                        <tr>
                            <th width="90"class="kiri text_kiri" colspan="2">TOTAL SP2D DAFTAR PENGUJI INI</th>
                            <th width="90" class="text_kiri"><b>:</b></th>
                            <th class="text_kanan"><?php echo number_format($total_bruto,0,",","."); ?></th>
                            <th class="kanan" colspan="9"></th>
                        </tr> 
                        <tr>
                            <th width="90"class="kiri text_kiri" colspan="2">TOTAL SP2D S/D DAFTAR PENGUJI INI</th>
                            <th width="90" class="text_kiri"><b>:</b></th>
                            <th class="text_kanan"><?php echo number_format($total_semua,0,",","."); ?></th>
                            <th class="kanan" colspan="9"></th>
                        </tr> 
                        <tr>
                            <th width="15" class="kiri text_kiri" colspan="2"></th>
                            <th width="90" class="text_kiri" colspan="7"><b></b></th>
                            <th class="text_kanan"></th>
                            <th width="150" class="kanan text_kiri" colspan="6"><b></b></th>
                        </tr> 
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="no_padding no_break">
            <td colspan="10" class="no_break">
                <table width="100%" style="border-spacing: 0px;" contenteditable="true">
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
                                        <td colspan="3" class="text_tengah"><u>XXXXXXX</u> <br>NIP XXXXXXX</td>
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
                                        <td colspan="3" class="text_tengah"><u>XXXXXX</u> <br>NIP XXXXXX</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="atas bawah kanan kiri" colspan="10"><i>DAFTAR PENGUJI ( Dicetak pada <?php echo $waktu; ?>)</th>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>
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