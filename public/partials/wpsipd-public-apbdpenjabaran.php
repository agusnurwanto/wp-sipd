<?php
function generate_body($rek_pendapatan, $baris_kosong=false, $type='murni'){
    global $wpdb;
    $data_pendapatan = array(
        'data' => array(),
        'total' => 0,
        'totalmurni' => 0
    );
    foreach ($rek_pendapatan as $k => $v) {
        $rek = explode('.', $v['kode_akun']);
        $kode_akun = $rek[0];
        if(!$kode_akun){
            // print_r($v); die();
            continue;
        }
        if(empty($data_pendapatan['data'][$kode_akun])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun1 = $kode_akun.'.'.$rek[1];
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$kode_akun1])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun1."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun2 = $kode_akun1.'.'.$rek[2];
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun2."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun3 = $kode_akun2.'.'.$rek[3];
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun3."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun4 = $kode_akun3.'.'.$rek[4];
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun4."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun5 = $kode_akun4.'.'.$rek[5];
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun5."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $data_pendapatan['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['total'] += $v['total'];

        if(!empty($v['totalmurni'])){
            $data_pendapatan['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['totalmurni'] += $v['totalmurni'];
        }
    }
    $body_pendapatan = '';
    $total = 0;
    foreach ($data_pendapatan['data'] as $k => $v) {
        $murni = '';
        $selisih = '';
        if($type == 'pergeseran'){
            $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($v['totalmurni'],0,",",".")."</td>";
            $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($v['total']-$v['totalmurni']),0,",",".")."</td>";
        }
        $body_pendapatan .= "
        <tr>
            <td class='kiri kanan bawah text_blok'>".$k."</td>
            <td class='kanan bawah text_blok'>".$v['nama']."</td>
            ".$murni."
            <td class='kanan bawah text_kanan text_blok'>".number_format($v['total'],0,",",".")."</td>
            ".$selisih."
        </tr>";
        foreach ($v['data'] as $kk => $vv) {
            $murni = '';
            $selisih = '';
            if($type == 'pergeseran'){
                $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($vv['totalmurni'],0,",",".")."</td>";
                $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($vv['total']-$vv['totalmurni']),0,",",".")."</td>";
            }
            $body_pendapatan .= "
            <tr>
                <td class='kiri kanan bawah text_blok'>".$kk."</td>
                <td class='kanan bawah text_blok'>".$vv['nama']."</td>
                ".$murni."
                <td class='kanan bawah text_kanan text_blok'>".number_format($vv['total'],0,",",".")."</td>
                ".$selisih."
            </tr>";
            foreach ($vv['data'] as $kkk => $vvv) {
                $murni = '';
                $selisih = '';
                if($type == 'pergeseran'){
                    $murni = "<td class='kanan bawah text_kanan'>".number_format($vvv['totalmurni'],0,",",".")."</td>";
                    $selisih = "<td class='kanan bawah text_kanan'>".number_format(($vvv['total']-$vvv['totalmurni']),0,",",".")."</td>";
                }
                $body_pendapatan .= "
                <tr>
                    <td class='kiri kanan bawah'>".$kkk."</td>
                    <td class='kanan bawah'>".$vvv['nama']."</td>
                    ".$murni."
                    <td class='kanan bawah text_kanan'>".number_format($vvv['total'],0,",",".")."</td>
                    ".$selisih."
                </tr>";
                foreach ($vvv['data'] as $kkkk => $vvvv) {
                    $murni = '';
                    $selisih = '';
                    if($type == 'pergeseran'){
                        $murni = "<td class='kanan bawah text_kanan'>".number_format($vvvv['totalmurni'],0,",",".")."</td>";
                        $selisih = "<td class='kanan bawah text_kanan'>".number_format(($vvvv['total']-$vvvv['totalmurni']),0,",",".")."</td>";
                    }
                    $body_pendapatan .= "
                    <tr>
                        <td class='kiri kanan bawah'>".$kkkk."</td>
                        <td class='kanan bawah'>".$vvvv['nama']."</td>
                        ".$murni."
                        <td class='kanan bawah text_kanan'>".number_format($vvvv['total'],0,",",".")."</td>
                        ".$selisih."
                    </tr>";
                    foreach ($vvvv['data'] as $kkkkk => $vvvvv) {
                        $murni = '';
                        $selisih = '';
                        if($type == 'pergeseran'){
                            $murni = "<td class='kanan bawah text_kanan'>".number_format($vvvvv['totalmurni'],0,",",".")."</td>";
                            $selisih = "<td class='kanan bawah text_kanan'>".number_format(($vvvvv['total']-$vvvvv['totalmurni']),0,",",".")."</td>";
                        }
                        $body_pendapatan .= "
                        <tr>
                            <td class='kiri kanan bawah'>".$kkkkk."</td>
                            <td class='kanan bawah'>".$vvvvv['nama']."</td>
                            ".$murni."
                            <td class='kanan bawah text_kanan'>".number_format($vvvvv['total'],0,",",".")."</td>
                            ".$selisih."
                        </tr>";
                        foreach ($vvvvv['data'] as $kkkkkk => $vvvvvv) {
                            $murni = '';
                            $selisih = '';
                            if($type == 'pergeseran'){
                                $murni = "<td class='kanan bawah text_kanan'>".number_format($vvvvvv['totalmurni'],0,",",".")."</td>";
                                $selisih = "<td class='kanan bawah text_kanan'>".number_format(($vvvvvv['total']-$vvvvvv['totalmurni']),0,",",".")."</td>";
                            }
                            $body_pendapatan .= "
                            <tr>
                                <td class='kiri kanan bawah'>".$kkkkkk."</td>
                                <td class='kanan bawah'>".$vvvvvv['nama']."</td>
                                ".$murni."
                                <td class='kanan bawah text_kanan'>".number_format($vvvvvv['total'],0,",",".")."</td>
                                ".$selisih."
                            </tr>";
                        }
                    }
                }
            }
        }
    }

    $murni = '';
    $selisih = '';
    if($type == 'pergeseran'){
        $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($data_pendapatan['totalmurni'],0,",",".")."</td>";
        $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($data_pendapatan['totalmurni']-$data_pendapatan['total']),0,",",".")."</td>";
    }
    $body_pendapatan .= "
    <tr>
        <td class='kiri kanan bawah'></td>
        <td class='kanan bawah text_kanan text_blok'>Jumlah Pendapatan</td>
        ".$murni."
        <td class='kanan bawah text_kanan text_blok'>".number_format($data_pendapatan['total'],0,",",".")."</td>
        ".$selisih."
    </tr>";
    if($baris_kosong){
        $body_pendapatan .= "
        <tr>
            <td class='kiri kanan bawah' style='color: #fff;'>.</td>
            <td class='kanan bawah'></td>
            ".$murni."
            <td class='kanan bawah'></td>
            ".$selisih."
        </tr>";
    }
    return $body_pendapatan;
}

global $wpdb;
$type = 'murni';
if(!empty($_GET) && !empty($_GET['type'])){
    $type = $_GET['type'];
}

$sql = $wpdb->prepare("
    select 
        kode_akun,
        nama_akun,
        sum(total) as total,
        sum(nilaimurni) as totalmurni
    from data_pendapatan
    where tahun_anggaran=%d
        and active=1
    group by kode_akun
    order by kode_akun ASC
", $input['tahun_anggaran']);
$rek_pendapatan = $wpdb->get_results($sql, ARRAY_A);

$body_pendapatan = generate_body($rek_pendapatan, true, $type);

$sql = $wpdb->prepare("
    select 
        kode_akun,
        nama_akun,
        sum(total_harga) as total,
        0 as totalmurni
    from data_rka
    where tahun_anggaran=%d
        and active=1
    group by kode_akun
    order by kode_akun ASC
", $input['tahun_anggaran']);
$rek_belanja = $wpdb->get_results($sql, ARRAY_A);

$body_belanja = generate_body($rek_belanja, true, $type);

$sql = $wpdb->prepare("
    select 
        kode_akun,
        nama_akun,
        sum(total) as total,
        sum(nilaimurni) as totalmurni
    from data_pembiayaan
    where tahun_anggaran=%d
        and active=1
    group by kode_akun
    order by kode_akun ASC
", $input['tahun_anggaran']);
$rek_pembiayaan = $wpdb->get_results($sql, ARRAY_A);

$body_pembiayaan = generate_body($rek_pembiayaan, false, $type);
?>

<div id="cetak" title="Laporan APBD PENJABARAN Lampiran 1 Tahun Anggaran <?php echo $input['tahun_anggaran']; ?>">
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
    <h4 style="text-align: center; font-size: 13px; margin: 10px auto; min-width: 450px; max-width: 550px; font-weight: bold;">KABUPATEN MAGETAN <br>RINGKASAN  PENJABARAN  APBD YANG DIKLASIFIKASI MENURUT KELOMPOK DAN JENIS PENDAPATAN, BELANJA, DAN PEMBIAYAAN<br>TAHUN ANGGARAN <?php echo $input['tahun_anggaran']; ?></h4>
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok" rowspan="2">Kode</td>
                <td class="atas kanan bawah text_tengah text_blok" rowspan="2">Uraian</td>
            <?php if($type == 'murni'): ?>
                <td class="atas kanan bawah text_tengah text_blok">Jumlah</td>
            <?php else: ?>
                <td class="atas kanan bawah text_tengah text_blok">Sebelum Perubahan</td>
                <td class="atas kanan bawah text_tengah text_blok">Sesudah Perubahan</td>
                <td class="atas kanan bawah text_tengah text_blok">Bertambah/(Berkurang)</td>
            <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
                echo $body_pendapatan; 
                echo $body_belanja; 
                echo $body_pembiayaan; 
            ?>
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
    var _url = window.location.href;
    var url = new URL(_url);
    _url = url.origin+url.pathname+'?key='+url.searchParams.get('key');
    var type = url.searchParams.get("type");
    if(type && type=='pergeseran'){
        var extend_action = '<a class="button button-primary" target="_blank" href="'+_url+'" style="margin-left: 10px;">Print APBD Lampiran 1</a>';
    }else{
        var extend_action = '<a class="button button-primary" target="_blank" href="'+_url+'&type=pergeseran" style="margin-left: 10px;">Print Pergeseran/Perubahan APBD Lampiran 1</a>';
    }
    jQuery('#action-sipd').append(extend_action);
</script>