<?php
function ubah_minus($nilai){
    if($nilai < 0){
        $nilai = abs($nilai);
        return '('.number_format($nilai,0,",",".").')';
    }else{
        return number_format($nilai,0,",",".");
    }
}

function generate_body($rek_pendapatan, $baris_kosong=false, $type='murni', $nama_rekening, $dari_simda=0){
    global $wpdb;
    $data_pendapatan = array(
        'data' => array(),
        'total' => 0,
        'totalmurni' => 0
    );
    foreach ($rek_pendapatan as $k => $v) {
        if($dari_simda!=0 && !empty($v['total_simda'])){
            $v['totalmurni'] = $v['total_simda'];
        }
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
            $murni = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['totalmurni'])."</td>";
            $selisih = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus(($v['total']-$v['totalmurni']))."</td>";
        }
        $body_pendapatan .= "
        <tr>
            <td class='kiri kanan bawah text_blok'>".$k."</td>
            <td class='kanan bawah text_blok'>".$v['nama']."</td>
            ".$murni."
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['total'])."</td>
            ".$selisih."
        </tr>";
        foreach ($v['data'] as $kk => $vv) {
            $murni = '';
            $selisih = '';
            if($type == 'pergeseran'){
                $murni = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['totalmurni'])."</td>";
                $selisih = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus(($vv['total']-$vv['totalmurni']))."</td>";
            }
            $body_pendapatan .= "
            <tr>
                <td class='kiri kanan bawah text_blok'>".$kk."</td>
                <td class='kanan bawah text_blok'>".$vv['nama']."</td>
                ".$murni."
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['total'])."</td>
                ".$selisih."
            </tr>";
            foreach ($vv['data'] as $kkk => $vvv) {
                $murni = '';
                $selisih = '';
                if($type == 'pergeseran'){
                    $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvv['totalmurni'])."</td>";
                    $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvv['total']-$vvv['totalmurni']))."</td>";
                }
                $body_pendapatan .= "
                <tr>
                    <td class='kiri kanan bawah'>".$kkk."</td>
                    <td class='kanan bawah'>".$vvv['nama']."</td>
                    ".$murni."
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['total'])."</td>
                    ".$selisih."
                </tr>";
                foreach ($vvv['data'] as $kkkk => $vvvv) {
                    $murni = '';
                    $selisih = '';
                    if($type == 'pergeseran'){
                        $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvvv['totalmurni'])."</td>";
                        $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvvv['total']-$vvvv['totalmurni']))."</td>";
                    }
                    $body_pendapatan .= "
                    <tr>
                        <td class='kiri kanan bawah'>".$kkkk."</td>
                        <td class='kanan bawah'>".$vvvv['nama']."</td>
                        ".$murni."
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['total'])."</td>
                        ".$selisih."
                    </tr>";
                    foreach ($vvvv['data'] as $kkkkk => $vvvvv) {
                        $murni = '';
                        $selisih = '';
                        if($type == 'pergeseran'){
                            $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['totalmurni'])."</td>";
                            $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvvvv['total']-$vvvvv['totalmurni']))."</td>";
                        }
                        $body_pendapatan .= "
                        <tr>
                            <td class='kiri kanan bawah'>".$kkkkk."</td>
                            <td class='kanan bawah'>".$vvvvv['nama']."</td>
                            ".$murni."
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['total'])."</td>
                            ".$selisih."
                        </tr>";
                        foreach ($vvvvv['data'] as $kkkkkk => $vvvvvv) {
                            $murni = '';
                            $selisih = '';
                            if($type == 'pergeseran'){
                                $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['totalmurni'])."</td>";
                                $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvvvvv['total']-$vvvvvv['totalmurni']))."</td>";
                            }
                            $body_pendapatan .= "
                            <tr>
                                <td class='kiri kanan bawah'>".$kkkkkk."</td>
                                <td class='kanan bawah'>".$vvvvvv['nama']."</td>
                                ".$murni."
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['total'])."</td>
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
        $murni = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_pendapatan['totalmurni'])."</td>";
        $selisih = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus(($data_pendapatan['totalmurni']-$data_pendapatan['total']))."</td>";
    }
    $body_pendapatan .= "
    <tr>
        <td class='kiri kanan bawah'></td>
        <td class='kanan bawah text_kanan text_blok'>Jumlah ".$nama_rekening."</td>
        ".$murni."
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_pendapatan['total'])."</td>
        ".$selisih."
    </tr>";
    if($nama_rekening == 'Belanja'){
        $body_pendapatan .= "
        <tr>
            <td class='kiri kanan bawah'></td>
            <td class='kanan bawah text_kanan text_blok'>Total Surplus/(Defisit)</td>
            <td class='kanan bawah text_kanan text_blok'>-</td>
            <td class='kanan bawah text_kanan text_blok'>-</td>
            <td class='kanan bawah text_kanan text_blok'>-</td>
        </tr>";
    }
    if($baris_kosong){
        $body_pendapatan .= "
        <tr>
            <td class='kiri kanan bawah' style='color: #fff;'>.</td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
        </tr>";
    }
    return $body_pendapatan;
}

global $wpdb;
$GLOBALS['simdadb'] = $this->simda;
$type = 'murni';
if(!empty($_GET) && !empty($_GET['type'])){
    $type = $_GET['type'];
}
$dari_simda = 0;
if(!empty($_GET) && !empty($_GET['dari_simda'])){
    $dari_simda = $_GET['dari_simda'];
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

$body_pendapatan = generate_body($rek_pendapatan, true, $type, 'Pendapatan');

$sql = $wpdb->prepare("
    select 
        kode_akun,
        nama_akun,
        sum(rincian) as total,
        sum(rincian_murni) as totalmurni
    from data_rka
    where tahun_anggaran=%d
        and active=1
    group by kode_akun
    order by kode_akun ASC
", $input['tahun_anggaran']);
$rek_belanja = $wpdb->get_results($sql, ARRAY_A);

if($dari_simda != 0){
    $options = array(
        'query' => $wpdb->prepare("
            select 
                kd_rek_1,
                kd_rek_2,
                kd_rek_3,
                kd_rek_4,
                kd_rek_5,
                sum(total) as total_simda
            from ta_rask_arsip
            where tahun=%d
                and kd_perubahan=4
                and kd_rek_1=5
            group by kd_rek_1, kd_rek_2, kd_rek_3, kd_rek_4, kd_rek_5
            order by  kd_rek_1 ASC, kd_rek_2 ASC, kd_rek_3 ASC, kd_rek_4 ASC, kd_rek_5 ASC
        ", $input['tahun_anggaran'])
    );
    $rek_belanja_simda = $this->simda->CurlSimda($options);
    $rek_belanja_simda2 = array();
    foreach ($rek_belanja_simda as $k => $v) {
        $options = array(
            'query' => "
                select 
                    kd_rek90_1,
                    kd_rek90_2,
                    kd_rek90_3,
                    kd_rek90_4,
                    kd_rek90_5,
                    kd_rek90_6
                from ref_rek_mapping
                where kd_rek_1=".$v->kd_rek_1."
                    and kd_rek_2=".$v->kd_rek_2."
                    and kd_rek_3=".$v->kd_rek_3."
                    and kd_rek_4=".$v->kd_rek_4."
                    and kd_rek_5=".$v->kd_rek_5."
            ");
        $akun_sipd = $this->simda->CurlSimda($options);
        $v = (array) $v;
        $v['kode_akun'] = $akun_sipd[0]->kd_rek90_1.'.'.$akun_sipd[0]->kd_rek90_2.'.'.$this->simda->CekNull($akun_sipd[0]->kd_rek90_3).'.'.$this->simda->CekNull($akun_sipd[0]->kd_rek90_4).'.'.$this->simda->CekNull($akun_sipd[0]->kd_rek90_5).'.'.$this->simda->CekNull($akun_sipd[0]->kd_rek90_6, 4);
        $rek_belanja_simda2[$v['kode_akun']] = $v;
    }
    // print_r($rek_belanja_simda2); die();
    foreach ($rek_belanja as $k => $v) {
        if(!empty($rek_belanja_simda2[$v['kode_akun']])){
            $rek_belanja_simda2[$v['kode_akun']]['total'] = $v['total'];
            $rek_belanja_simda2[$v['kode_akun']]['totalmurni'] = $v['totalmurni'];
        }else{
            $rek_belanja_simda2[$v['kode_akun']] = $v;
        }
    }
    $rek_belanja = $rek_belanja_simda2;
}

$body_belanja = generate_body($rek_belanja, true, $type, 'Belanja', $dari_simda);

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

$body_pembiayaan = generate_body($rek_pembiayaan, false, $type, 'Pembiayaan');
?>

<div id="cetak" title="Laporan APBD PENJABARAN Lampiran 1 Tahun Anggaran <?php echo $input['tahun_anggaran']; ?>">
    <table align="right" class="no-border no-padding" cellspacing="0" cellpadding="0" style="width:280px; font-size: 12px;">
        <tr>
            <td width="80" valign="top">Lampiran I </td>
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
        <tr><td colspan="3" class="text_tengah text_15" contenteditable="true">Bupati XXXX  </td></tr>
        <tr><td colspan="3" height="80">&nbsp;</td></tr>
        <tr><td colspan="3" class="text_tengah" contenteditable="true">XXXXXXXXXXX</td></tr>
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