<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;
global $total_belanja_murni;
global $total_belanja;
global $total_pendapatan_murni;
global $total_pendapatan;

if(empty($_GET['id_unit'])){
    die('<h1 class="text-center">ID SKPD tidak boleh kosong!</h1>');
}

$total_belanja_murni = 0;
$total_belanja = 0;
$total_pendapatan_murni = 0;
$total_pendapatan = 0;

function generate_body($rek_pendapatan, $nama_table, $type='murni'){
    global $wpdb;
    global $total_belanja_murni;
    global $total_belanja;
    global $total_pendapatan_murni;
    global $total_pendapatan;

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
        if(empty($v['nama_bidang_urusan'])){
            $kode_urusan = array('0', '00');
        }else{
            $kode_urusan = explode('.', $v['kode_bidang_urusan']);
        }
        if(empty($data_pendapatan['data'][$kode_akun])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'kode_skpd' => $skpd['kode_skpd'],
                'kode_urusan' => $kode_urusan[0],
                'kode_bidang' => $kode_urusan[1],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']])){
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']] = array(
                'nama' => $v['nama_bidang_urusan'],
                'data' => array(),
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']])){
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']] = array(
                'data' => array(),
                'nama' => $v['nama_program'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']])){
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']] = array(
                'data' => array(),
                'nama' => $v['nama_giat'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']])){
            $skpd_sub = $wpdb->get_row('SELECT kode_skpd, nama_skpd FROM `data_unit` where id_skpd='.$v['id_skpd'].' and tahun_anggaran='.$v['tahun_anggaran'].' and active=1', ARRAY_A);
            // echo $skpd_sub['nama_skpd'].' - '.$skpd_sub['kode_skpd'].' - '.$v['id_skpd'].'<br>';
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']] = array(
                'data' => array(),
                'kode_skpd' => $skpd_sub['kode_skpd'],
                'nama_skpd' => $skpd_sub['nama_skpd'],
                'nama' => $v['nama_sub_giat'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun1 = $kode_akun.'.'.$rek[1];
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun1."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun2 = $kode_akun1.'.'.$rek[2];
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun2."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun3 = $kode_akun2.'.'.$rek[3];
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun3."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun4 = $kode_akun3.'.'.$rek[4];
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun4."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun5 = $kode_akun4.'.'.$rek[5];
        if(empty($data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun5."'", ARRAY_A);
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5] = array(
                'data' => array(),
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['data'][] = $v;

        $data_pendapatan['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['total'] += $v['total'];

        // if(!empty($v['totalmurni'])){
            $data_pendapatan['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$kode_akun]['data'][$v['kode_bidang_urusan']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['totalmurni'] += $v['totalmurni'];
        // }
    }
    // print_r($data_pendapatan); die();

    $body_urusan = '';

    $kode_skpd = explode('.', $skpd['kode_skpd']);
    $body_pendapatan = '';
    $total = 0;
    $padding = 15;
    foreach ($data_pendapatan['data'] as $k => $v) {
        $murni = '';
        $selisih = '';
        if($type == 'pergeseran'){
            $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($v['totalmurni'],0,",",".")."</td>";
            $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($v['total']-$v['totalmurni']),0,",",".")."</td>";
        }
        $body_pendapatan .= "
        <tr data-akun='".$k."'>
            <td class='kiri kanan bawah text_blok'>".$v['kode_urusan']."</td>
            <td class='kiri kanan bawah text_blok'>".$v['kode_bidang']."</td>
            <td class='kiri kanan bawah text_blok'>".$v['kode_skpd']."</td>
            <td class='kiri kanan bawah text_blok'>00</td>
            <td class='kiri kanan bawah text_blok'>0.00</td>
            <td class='kiri kanan bawah text_blok'>00</td>
            <td class='kiri kanan bawah text_blok'>".$k."</td>
            <td class='kiri kanan bawah'></td>
            <td class='kiri kanan bawah'></td>
            <td class='kiri kanan bawah'></td>
            <td class='kiri kanan bawah rincian_objek'></td>
            <td class='kiri kanan bawah sub_rincian_objek'></td>
            <td class='kanan bawah text_blok'>".$v['nama']."</td>
            ".$murni."
            <td class='kanan bawah text_kanan text_blok'>".number_format($v['total'],0,",",".")."</td>
            ".$selisih."
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
        </tr>";
        // urusan
        foreach ($v['data'] as $nn => $mm) {
            if(!empty($mm['nama'])){
                $murni = '';
                $selisih = '';
                if($type == 'pergeseran'){
                    $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($mm['totalmurni'],0,",",".")."</td>";
                    $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($mm['total']-$mm['totalmurni']),0,",",".")."</td>";
                }
                $body_pendapatan .= "
                <tr data-akun='".$nn."'>
                    <td class='kiri kanan bawah text_blok'>".$v['kode_urusan']."</td>
                    <td class='kiri kanan bawah text_blok'>".$v['kode_bidang']."</td>
                    <td class='kiri kanan bawah text_blok'>".$v['kode_skpd']."</td>
                    <td class='kiri kanan bawah text_blok'>00</td>
                    <td class='kiri kanan bawah text_blok'>0.00</td>
                    <td class='kiri kanan bawah text_blok'>00</td>
                    <td class='kiri kanan bawah'></td>
                    <td class='kiri kanan bawah'></td>
                    <td class='kiri kanan bawah'></td>
                    <td class='kiri kanan bawah'></td>
                    <td class='kiri kanan bawah rincian_objek'></td>
                    <td class='kiri kanan bawah sub_rincian_objek'></td>
                    <td class='kanan bawah text_blok' style='padding-left:".($padding*1)."px;'>".$mm['nama']."</td>
                    ".$murni."
                    <td class='kanan bawah text_kanan text_blok'>".number_format($mm['total'],0,",",".")."</td>
                    ".$selisih."
                    <td class='kanan bawah'></td>
                    <td class='kanan bawah'></td>
                </tr>";
            }
            // program
            foreach ($mm['data'] as $nnn => $mmm) {
                if(!empty($mmm['nama'])){
                    $murni = '';
                    $selisih = '';
                    if($type == 'pergeseran'){
                        $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($mmm['totalmurni'],0,",",".")."</td>";
                        $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($mmm['total']-$mmm['totalmurni']),0,",",".")."</td>";
                    }
                    $body_pendapatan .= "
                    <tr data-akun='".$nnn."'>
                        <td class='kiri kanan bawah text_blok'>".$v['kode_urusan']."</td>
                        <td class='kiri kanan bawah text_blok'>".$v['kode_bidang']."</td>
                        <td class='kiri kanan bawah text_blok'>".$v['kode_skpd']."</td>
                        <td class='kiri kanan bawah text_blok'>".$nnn."</td>
                        <td class='kiri kanan bawah text_blok'>0.00</td>
                        <td class='kiri kanan bawah text_blok'>00</td>
                        <td class='kiri kanan bawah'></td>
                        <td class='kiri kanan bawah'></td>
                        <td class='kiri kanan bawah'></td>
                        <td class='kiri kanan bawah'></td>
                        <td class='kiri kanan bawah rincian_objek'></td>
                        <td class='kiri kanan bawah sub_rincian_objek'></td>
                        <td class='kanan bawah text_blok' style='padding-left:".($padding*2)."px;'>".$mmm['nama']."</td>
                        ".$murni."
                        <td class='kanan bawah text_kanan text_blok'>".number_format($mmm['total'],0,",",".")."</td>
                        ".$selisih."
                        <td class='kanan bawah'></td>
                        <td class='kanan bawah'></td>
                    </tr>";
                }
                // kegiatan
                foreach ($mmm['data'] as $nnnn => $mmmm) {
                    if(!empty($mmmm['nama'])){
                        $murni = '';
                        $selisih = '';
                        if($type == 'pergeseran'){
                            $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($mmmm['totalmurni'],0,",",".")."</td>";
                            $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($mmmm['total']-$mmmm['totalmurni']),0,",",".")."</td>";
                        }
                        $body_pendapatan .= "
                        <tr data-akun='".$nnnn."'>
                            <td class='kiri kanan bawah text_blok'>".$v['kode_urusan']."</td>
                            <td class='kiri kanan bawah text_blok'>".$v['kode_bidang']."</td>
                            <td class='kiri kanan bawah text_blok'>".$v['kode_skpd']."</td>
                            <td class='kiri kanan bawah text_blok'>".$nnn."</td>
                            <td class='kiri kanan bawah text_blok'>".$nnnn."</td>
                            <td class='kiri kanan bawah text_blok'>00</td>
                            <td class='kiri kanan bawah'></td>
                            <td class='kiri kanan bawah'></td>
                            <td class='kiri kanan bawah'></td>
                            <td class='kiri kanan bawah'></td>
                            <td class='kiri kanan bawah rincian_objek'></td>
                            <td class='kiri kanan bawah sub_rincian_objek'></td>
                            <td class='kanan bawah text_blok' style='padding-left:".($padding*3)."px;'>".$mmmm['nama']."</td>
                            ".$murni."
                            <td class='kanan bawah text_kanan text_blok'>".number_format($mmmm['total'],0,",",".")."</td>
                            ".$selisih."
                            <td class='kanan bawah'></td>
                            <td class='kanan bawah'></td>
                        </tr>";
                    }
                    // sub kegiatan
                    foreach ($mmmm['data'] as $nnnnn => $mmmmm) {
                        $v['kode_skpd'] = $mmmmm['kode_skpd'];
                        if(!empty($mmmmm['nama'])){
                            $murni = '';
                            $selisih = '';
                            if($type == 'pergeseran'){
                                $murni = "<td class='kanan bawah text_kanan'>".number_format($mmmmm['totalmurni'],0,",",".")."</td>";
                                $selisih = "<td class='kanan bawah text_kanan'>".number_format(($mmmmm['total']-$mmmmm['totalmurni']),0,",",".")."</td>";
                            }
                            $sub_giat = explode(' ', $mmmmm['nama']);
                            $kd_sub_giat = $sub_giat[0];
                            unset($sub_giat[0]);
                            $sub_giat = implode(' ', $sub_giat);

                            // if($kd_sub_giat == '1.02.02.2.01.01'){
                            //     print_r($mmmmm); die();
                            // }

                            $body_pendapatan .= "
                            <tr data-akun='".$kd_sub_giat."' data-skpd='".$mmmmm['kode_skpd']."' data-nama-skpd='".$mmmmm['nama_skpd']."'>
                                <td class='kiri kanan bawah'>".$v['kode_urusan']."</td>
                                <td class='kiri kanan bawah'>".$v['kode_bidang']."</td>
                                <td class='kiri kanan bawah'>".$v['kode_skpd']."</td>
                                <td class='kiri kanan bawah'>".$nnn."</td>
                                <td class='kiri kanan bawah'>".$nnnn."</td>
                                <td class='kiri kanan bawah'>".$nnnnn."</td>
                                <td class='kiri kanan bawah'></td>
                                <td class='kiri kanan bawah'></td>
                                <td class='kiri kanan bawah'></td>
                                <td class='kiri kanan bawah'></td>
                                <td class='kiri kanan bawah rincian_objek'></td>
                                <td class='kiri kanan bawah sub_rincian_objek'></td>
                                <td class='kanan bawah' style='padding-left:".($padding*4)."px;'>".$sub_giat."</td>
                                ".$murni."
                                <td class='kanan bawah text_kanan'>".number_format($mmmmm['total'],0,",",".")."</td>
                                ".$selisih."
                                <td class='kanan bawah'></td>
                                <td class='kanan bawah'></td>
                            </tr>";
                        }
                        foreach ($mmmmm['data'] as $kk => $vv) {
                            $text_blok = '';
                            if(empty($mmmmm['nama'])){
                                $text_blok = 'text_blok';
                            }
                            $kode = explode('.', $kk);
                            $murni = '';
                            $selisih = '';
                            if($type == 'pergeseran'){
                                $murni = "<td class='kanan bawah ".$text_blok." text_kanan'>".number_format($vv['totalmurni'],0,",",".")."</td>";
                                $selisih = "<td class='kanan bawah ".$text_blok." text_kanan'>".number_format(($vv['total']-$vv['totalmurni']),0,",",".")."</td>";
                            }
                            $body_pendapatan .= "
                            <tr data-akun='".$kk."'>
                                <td class='kiri kanan bawah ".$text_blok."'>".$v['kode_urusan']."</td>
                                <td class='kiri kanan bawah ".$text_blok."'>".$v['kode_bidang']."</td>
                                <td class='kiri kanan bawah ".$text_blok."'>".$v['kode_skpd']."</td>
                                <td class='kiri kanan bawah ".$text_blok."'>".$nnn."</td>
                                <td class='kiri kanan bawah ".$text_blok."'>".$nnnn."</td>
                                <td class='kiri kanan bawah ".$text_blok."'>".$nnnnn."</td>
                                <td class='kiri kanan bawah ".$text_blok."'>".$kode[0]."</td>
                                <td class='kiri kanan bawah ".$text_blok."'>".$kode[1]."</td>
                                <td class='kiri kanan bawah ".$text_blok."'></td>
                                <td class='kiri kanan bawah ".$text_blok."'></td>
                                <td class='kiri kanan bawah ".$text_blok." rincian_objek'></td>
                                <td class='kiri kanan bawah ".$text_blok." sub_rincian_objek'></td>
                                <td class='kanan bawah ".$text_blok."'  style='padding-left:".($padding*5)."px;'>".$vv['nama']."</td>
                                ".$murni."
                                <td class='kanan bawah ".$text_blok." text_kanan'>".number_format($vv['total'],0,",",".")."</td>
                                ".$selisih."
                                <td class='kanan bawah ".$text_blok."'></td>
                                <td class='kanan bawah ".$text_blok."'></td>
                            </tr>";
                            foreach ($vv['data'] as $kkk => $vvv) {
                                $kode = explode('.', $kkk);
                                $murni = '';
                                $selisih = '';
                                if($type == 'pergeseran'){
                                    $murni = "<td class='kanan bawah text_kanan'>".number_format($vvv['totalmurni'],0,",",".")."</td>";
                                    $selisih = "<td class='kanan bawah text_kanan'>".number_format(($vvv['total']-$vvv['totalmurni']),0,",",".")."</td>";
                                }
                                $body_pendapatan .= "
                                <tr data-akun='".$kkk."'>
                                    <td class='kiri kanan bawah'>".$v['kode_urusan']."</td>
                                    <td class='kiri kanan bawah'>".$v['kode_bidang']."</td>
                                    <td class='kiri kanan bawah'>".$v['kode_skpd']."</td>
                                    <td class='kiri kanan bawah'>".$nnn."</td>
                                    <td class='kiri kanan bawah'>".$nnnn."</td>
                                    <td class='kiri kanan bawah'>".$nnnnn."</td>
                                    <td class='kiri kanan bawah'>".$kode[0]."</td>
                                    <td class='kiri kanan bawah'>".$kode[1]."</td>
                                    <td class='kiri kanan bawah'>".$kode[2]."</td>
                                    <td class='kiri kanan bawah'></td>
                                    <td class='kiri kanan bawah rincian_objek'></td>
                                    <td class='kiri kanan bawah sub_rincian_objek'></td>
                                    <td class='kanan bawah'  style='padding-left:".($padding*6)."px;'>".$vvv['nama']."</td>
                                    ".$murni."
                                    <td class='kanan bawah text_kanan'>".number_format($vvv['total'],0,",",".")."</td>
                                    ".$selisih."
                                    <td class='kanan bawah'></td>
                                    <td class='kanan bawah'></td>
                                </tr>";
                                foreach ($vvv['data'] as $kkkk => $vvvv) {
                                    $kode = explode('.', $kkkk);
                                    $murni = '';
                                    $selisih = '';
                                    if($type == 'pergeseran'){
                                        $murni = "<td class='kanan bawah text_kanan'>".number_format($vvvv['totalmurni'],0,",",".")."</td>";
                                        $selisih = "<td class='kanan bawah text_kanan'>".number_format(($vvvv['total']-$vvvv['totalmurni']),0,",",".")."</td>";
                                    }
                                    $body_pendapatan .= "
                                    <tr data-akun='".$kkkk."'>
                                        <td class='kiri kanan bawah'>".$v['kode_urusan']."</td>
                                        <td class='kiri kanan bawah'>".$v['kode_bidang']."</td>
                                        <td class='kiri kanan bawah'>".$v['kode_skpd']."</td>
                                        <td class='kiri kanan bawah'>".$nnn."</td>
                                        <td class='kiri kanan bawah'>".$nnnn."</td>
                                        <td class='kiri kanan bawah'>".$nnnnn."</td>
                                        <td class='kiri kanan bawah'>".$kode[0]."</td>
                                        <td class='kiri kanan bawah'>".$kode[1]."</td>
                                        <td class='kiri kanan bawah'>".$kode[2]."</td>
                                        <td class='kiri kanan bawah'>".$kode[3]."</td>
                                        <td class='kiri kanan bawah rincian_objek'></td>
                                        <td class='kiri kanan bawah sub_rincian_objek'></td>
                                        <td class='kanan bawah'  style='padding-left:".($padding*7)."px;'>".$vvvv['nama']."</td>
                                        ".$murni."
                                        <td class='kanan bawah text_kanan'>".number_format($vvvv['total'],0,",",".")."</td>
                                        ".$selisih."
                                        <td class='kanan bawah'></td>
                                        <td class='kanan bawah'></td>
                                    </tr>";
                                    foreach ($vvvv['data'] as $kkkkk => $vvvvv) {
                                        $kode = explode('.', $kkkkk);
                                        $murni = '';
                                        $selisih = '';
                                        if($type == 'pergeseran'){
                                            $murni = "<td class='kanan bawah text_kanan'>".number_format($vvvvv['totalmurni'],0,",",".")."</td>";
                                            $selisih = "<td class='kanan bawah text_kanan'>".number_format(($vvvvv['total']-$vvvvv['totalmurni']),0,",",".")."</td>";
                                        }
                                        $body_pendapatan .= "
                                        <tr data-akun='".$kkkkk."' class='rincian_objek'>
                                            <td class='kiri kanan bawah'>".$v['kode_urusan']."</td>
                                            <td class='kiri kanan bawah'>".$v['kode_bidang']."</td>
                                            <td class='kiri kanan bawah'>".$v['kode_skpd']."</td>
                                            <td class='kiri kanan bawah'>".$nnn."</td>
                                            <td class='kiri kanan bawah'>".$nnnn."</td>
                                            <td class='kiri kanan bawah'>".$nnnnn."</td>
                                            <td class='kiri kanan bawah'>".$kode[0]."</td>
                                            <td class='kiri kanan bawah'>".$kode[1]."</td>
                                            <td class='kiri kanan bawah'>".$kode[2]."</td>
                                            <td class='kiri kanan bawah'>".$kode[3]."</td>
                                            <td class='kiri kanan bawah rincian_objek'>".$kode[4]."</td>
                                            <td class='kiri kanan bawah sub_rincian_objek'></td>
                                            <td class='kanan bawah'  style='padding-left:".($padding*8)."px;'>".$vvvvv['nama']."</td>
                                            ".$murni."
                                            <td class='kanan bawah text_kanan'>".number_format($vvvvv['total'],0,",",".")."</td>
                                            ".$selisih."
                                            <td class='kanan bawah'></td>
                                            <td class='kanan bawah'></td>
                                        </tr>";
                                        foreach ($vvvvv['data'] as $kkkkkk => $vvvvvv) {
                                            $all_skpd = array();
                                            foreach ($vvvvvv['data'] as $kkkkkkk => $vvvvvvv) {
                                                $all_skpd[] = array($vvvvvvv['id_skpd'], $vvvvvvv['totalmurni'], $vvvvvvv['total']);
                                            }
                                            $kode = explode('.', $kkkkkk);
                                            $murni = '';
                                            $selisih = '';
                                            if($type == 'pergeseran'){
                                                $murni = "<td class='kanan bawah text_kanan'>".number_format($vvvvvv['totalmurni'],0,",",".")."</td>";
                                                $selisih = "<td class='kanan bawah text_kanan'>".number_format(($vvvvvv['total']-$vvvvvv['totalmurni']),0,",",".")."</td>";
                                            }
                                            $body_pendapatan .= "
                                            <tr class='sub_rincian_objek' data-akun='".$kkkkkk."' data-skpd='".json_encode($all_skpd)."'>
                                                <td class='kiri kanan bawah'>".$v['kode_urusan']."</td>
                                                <td class='kiri kanan bawah'>".$v['kode_bidang']."</td>
                                                <td class='kiri kanan bawah'>".$v['kode_skpd']."</td>
                                                <td class='kiri kanan bawah'>".$nnn."</td>
                                                <td class='kiri kanan bawah'>".$nnnn."</td>
                                                <td class='kiri kanan bawah'>".$nnnnn."</td>
                                                <td class='kiri kanan bawah'>".$kode[0]."</td>
                                                <td class='kiri kanan bawah'>".$kode[1]."</td>
                                                <td class='kiri kanan bawah'>".$kode[2]."</td>
                                                <td class='kiri kanan bawah'>".$kode[3]."</td>
                                                <td class='kiri kanan bawah rincian_objek'>".$kode[4]."</td>
                                                <td class='kiri kanan bawah sub_rincian_objek'>".$kode[5]."</td>
                                                <td class='kanan bawah'  style='padding-left:".($padding*9)."px;'>".$vvvvvv['nama']."</td>
                                                ".$murni."
                                                <td class='kanan bawah text_kanan'>".number_format($vvvvvv['total'],0,",",".")."</td>
                                                ".$selisih."
                                                <td class='kanan bawah'></td>
                                                <td class='kanan bawah'></td>
                                            </tr>";
                                        }
                                    }
                                }
                            }
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
        $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($data_pendapatan['total']-$data_pendapatan['totalmurni']),0,",",".")."</td>";
    }
    $body_pendapatan .= "
    <tr>
        <td class='kiri kanan bawah text_kanan text_blok colspan_kurang' colspan='13'>Jumlah ".$nama_table."</td>
        ".$murni."
        <td class='kanan bawah text_kanan text_blok'>".number_format($data_pendapatan['total'],0,",",".")."</td>
        ".$selisih."
        <td class='kanan bawah'></td>
        <td class='kanan bawah'></td>
    </tr>";

    if($nama_table == 'Pendapatan'){
        $total_pendapatan = $data_pendapatan['total'];
        $total_pendapatan_murni = $data_pendapatan['totalmurni'];
    }else if($nama_table == 'Belanja'){
        $total_belanja = $data_pendapatan['total'];
        $total_belanja_murni = $data_pendapatan['totalmurni'];
    }
    return $body_pendapatan;
}

$skpd = $wpdb->get_row($wpdb->prepare('
    SELECT 
        * 
    FROM `data_unit` 
    where tahun_anggaran=%d
        and id_skpd=1
        and active=1
', $input['tahun_anggaran'], $_GET['id_unit']), ARRAY_A);
$kode = explode('.', $skpd['kode_skpd']);
$type = 'murni';
if(!empty($_GET) && !empty($_GET['type'])){
    $type = $_GET['type'];
}
$id_skpd_all = array();
if($skpd['is_skpd'] == 1){
    $skpd_induk = $wpdb->get_results('
        SELECT 
            id_skpd 
        FROM `data_unit` 
        where (
                idinduk='.$_GET['id_unit'].' 
                or id_unit='.$_GET['id_unit'].' 
            )
            and tahun_anggaran='.$input['tahun_anggaran'].' 
            and active=1
    ', ARRAY_A);
    foreach ($skpd_induk as $k => $v) {
        $id_skpd_all[] = $v['id_skpd'];
    }
}else{
    $id_skpd_all[0] = $_GET['id_unit'];
}

$sql = $wpdb->prepare("
    select 
        tahun_anggaran,
        id_skpd,
        '' as nama_bidang_urusan,
        '' as nama_program,
        '' as nama_giat,
        '' as nama_sub_giat,
        '".$kode[0].".".$kode[1]."' as kode_bidang_urusan,
        '00' as no_program,
        '0.00' as no_giat,
        '00' as no_sub_giat,
        kode_akun,
        nama_akun,
        sum(total) as total,
        sum(nilaimurni) as totalmurni
    from data_pendapatan
    where tahun_anggaran=%d
        and id_skpd IN (".implode(',', $id_skpd_all).")
        and active=1
    group by id_skpd, kode_akun
    order by id_skpd ASC, kode_akun ASC
", $input['tahun_anggaran']);
$kode_urusan = $wpdb->get_results($sql, ARRAY_A);

$body_urusan = generate_body($kode_urusan, 'Urusan', $type, $skpd);

$sql = $wpdb->prepare("
    select 
        tahun_anggaran,
        id_skpd,
        '' as nama_bidang_urusan,
        '' as nama_program,
        '' as nama_giat,
        '' as nama_sub_giat,
        '".$kode[0].".".$kode[1]."' as kode_bidang_urusan,
        '00' as no_program,
        '0.00' as no_giat,
        '00' as no_sub_giat,
        kode_akun,
        nama_akun,
        sum(total) as total,
        sum(nilaimurni) as totalmurni
    from data_pendapatan
    where tahun_anggaran=%d
        and id_skpd IN (".implode(',', $id_skpd_all).")
        and active=1
    group by id_skpd, kode_akun
    order by id_skpd ASC, kode_akun ASC
", $input['tahun_anggaran']);
$rek_pendapatan = $wpdb->get_results($sql, ARRAY_A);
// print_r($rek_pendapatan); die($sql);

$body_pendapatan = generate_body($rek_pendapatan, 'Pendapatan', $type, $skpd);

$sql = $wpdb->prepare("
    select 
        r.tahun_anggaran,
        s.id_sub_skpd as id_skpd,
        s.kode_bidang_urusan,
        s.nama_bidang_urusan,
        s.nama_program,
        s.nama_giat,
        s.nama_sub_giat,
        s.no_program,
        s.no_giat,
        s.no_sub_giat,
        r.kode_akun,
        r.nama_akun,
        sum(r.rincian) as total,
        sum(r.rincian_murni) as totalmurni
    from data_rka r
        left join data_sub_keg_bl s ON r.kode_sbl=s.kode_sbl AND s.tahun_anggaran=r.tahun_anggaran
    where r.tahun_anggaran=%d
        and s.id_sub_skpd IN (".implode(',', $id_skpd_all).")
        and r.active=1
        and s.active=1
    group by s.kode_sbl, r.kode_akun
    order by s.kode_sub_giat ASC, r.kode_akun ASC
", $input['tahun_anggaran']);
$rek_belanja = $wpdb->get_results($sql, ARRAY_A);

$body_belanja = generate_body($rek_belanja, 'Belanja', $type, $skpd);

$sql = $wpdb->prepare("
    select 
        tahun_anggaran,
        id_skpd,
        '' as nama_bidang_urusan,
        '' as nama_program,
        '' as nama_giat,
        '' as nama_sub_giat,
        '".$kode[0].".".$kode[1]."' as kode_bidang_urusan,
        '00' as no_program,
        '0.00' as no_giat,
        '00' as no_sub_giat,
        kode_akun,
        nama_akun,
        sum(total) as total,
        sum(nilaimurni) as totalmurni
    from data_pembiayaan
    where tahun_anggaran=%d
        and id_skpd IN (".implode(',', $id_skpd_all).")
        and active=1
    group by id_skpd, kode_akun
    order by id_skpd ASC, kode_akun ASC
", $input['tahun_anggaran']);
$rek_pembiayaan = $wpdb->get_results($sql, ARRAY_A);

$body_pembiayaan = generate_body($rek_pembiayaan, 'Pembiayaan', $type, $skpd);
$urusan = $wpdb->get_row('SELECT nama_bidang_urusan FROM `data_prog_keg` where kode_bidang_urusan=\''.$kode[0].'.'.$kode[1].'\' and tahun_anggaran='.$input['tahun_anggaran'], ARRAY_A);
?>
<div id="cetak" title="Laporan APBD PENJABARAN Lampiran 2 Tahun Anggaran <?php echo $input['tahun_anggaran']; ?>" style="padding: 5px;">
    <table align="right" class="table-header no-border no-padding" cellspacing="0" cellpadding="0" style="width:280px; font-size: 12px;">
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
    <h4 class="table-header" style="text-align: center; font-size: 13px; margin: 10px auto; font-weight: bold; text-transform: uppercase;"><?php echo get_option('_crb_daerah'); ?> <br>RINGKASAN APBD YANG DIKLASIFIKASIKAN MENURUT URUSAN PEMERINTAHAN DAERAH DAN ORGANISASI<br>TAHUN ANGGARAN <?php echo $input['tahun_anggaran']; ?></h4>
    <!-- <table cellspacing="2" cellpadding="0" class="apbd-penjabaran no-border no-padding">
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
    </table> -->
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok colspan_kurang" colspan="3" rowspan="2">Kode</td>
                <td class="atas kanan bawah text_tengah text_blok" rowspan="2">Urusan Pemerintah Daerah</td>
                <td class="atas kanan bawah text_tengah text_blok" rowspan="2">Pendapatan</td>
                <td class="atas kanan bawah text_tengah text_blok" colspan="5">Belanja</td>
                <?php if($type == 'murni'): ?>
                    <!-- <td class="atas kanan bawah text_tengah text_blok" width="150px">Jumlah</td> -->
                <?php else: ?>
                    <!-- <td class="atas kanan bawah text_tengah text_blok" width="150px">Sebelum Perubahan</td>
                    <td class="atas kanan bawah text_tengah text_blok" width="150px">Sesudah Perubahan</td>
                    <td class="atas kanan bawah text_tengah text_blok" width="150px">Bertambah/(Berkurang)</td> -->
                <?php endif; ?>
                <!-- <td class="atas kanan bawah text_tengah text_blok" width="180px">Penjelasan</td>
                <td class="atas kanan bawah text_tengah text_blok" width="180px">Keterangan</td> -->
            </tr>
            <tr>
                <td class="atas kanan bawah text_tengah text_blok">Operasi</td>
                <td class="atas kanan bawah text_tengah text_blok">Modal</td>                
                <td class="atas kanan bawah text_tengah text_blok">Tidak Terduga</td>
                <td class="atas kanan bawah text_tengah text_blok">Transfer</td>
                <td class="atas kanan bawah text_tengah text_blok">Jumlah Belanja</td>
            </tr>
        </thead>
        <tbody>
            <?php 
                //echo $body_urusan; 
            ?>
            <!-- <?php 
                echo $body_pendapatan; 
                echo $body_belanja;
                $selisih_belanja = $total_pendapatan-$total_belanja;
                $selisih_belanja_murni = $total_pendapatan_murni-$total_belanja_murni;
                if($type == 'murni'){
                    $kolom_jml = '
                        <td class="kanan bawah text_blok text_kanan">'.number_format($selisih_belanja,0,",",".").'</td>
                    ';
                    $kolom_batas = '
                        <td class="kanan bawah text_blok text_kanan" style="height: 2em;padding:0em;margin:0em;"></td>
                    ';
                }else{
                    $kolom_jml = '
                        <td class="kanan bawah text_blok text_kanan">'.number_format($selisih_belanja,0,",",".").'</td>
                        <td class="kanan bawah text_blok text_kanan">'.number_format($selisih_belanja_murni,0,",",".").'</td>
                        <td class="kanan bawah text_blok text_kanan">'.number_format(($selisih_belanja-$selisih_belanja_murni),0,",",".").'</td>
                    ';
                    $kolom_batas = '
                        <td class="kanan bawah text_blok text_kanan" style="height: 2em;padding:0em;margin:0em;"></td>
                        <td class="kanan bawah text_blok text_kanan" style="height: 2em;padding:0em;margin:0em;"></td>
                        <td class="kanan bawah text_blok text_kanan" style="height: 2em;padding:0em;margin:0em;"></td>
                    ';
                }
                echo '
                    </tr>
                        <td colspan="13" class="kiri kanan bawah text_blok text_kanan colspan_kurang">Total Surplus/(Defisit)</td>
                        '.$kolom_jml.'
                        <td class="kanan bawah"></td>
                        <td class="kanan bawah"></td>
                    </tr>
                    <tr>
                        <td colspan="13" class="kiri kanan bawah text_blok colspan_kurang" style="height: 2em;padding:0em;margin:0em;"></td>
                        '.$kolom_batas.'
                        <td class="kanan bawah"></td>
                        <td class="kanan bawah"></td>
                    </tr>
                ';
                echo $body_pembiayaan; 
            ?> -->
        </tbody>
    </table>
    <table width="25%" class="table-ttd no-border no-padding" align="right" cellpadding="2" cellspacing="0" style="width:280px; font-size: 12px;">
        <tr><td colspan="3" class="text_tengah" height="20px"></td></tr>
        <tr><td colspan="3" class="text_tengah text_15" contenteditable="true">Bupati XXXX  </td></tr>
        <tr><td colspan="3" height="80">&nbsp;</td></tr>
        <tr><td colspan="3" class="text_tengah" contenteditable="true">XXXXXXXXXXX</td></tr>
        <tr><td colspan="3" class="text_tengah"></td></tr>
    </table>
</div>

<script type="text/javascript">
    run_download_excel();
    function hide_header_ttd(that, type){
        var checked = jQuery(that).is(':checked');
        var hide2 = jQuery('#hide2').is(':checked');
        var hide3 = jQuery('#hide3').is(':checked');
        jQuery('.table-ttd').show();
        jQuery('.table-header').show();
        if(checked){
            if(type == 1){
                jQuery('#hide2').prop('checked', false);
                jQuery('#hide3').prop('checked', false);
                jQuery('.table-ttd').hide();
                jQuery('.table-header').hide();
            }else if(type == 2){
                jQuery('#hide1').prop('checked', false);
                if(hide3){
                    jQuery('.table-ttd').hide();
                }
                jQuery('.table-header').hide();
            }else if(type == 3){
                jQuery('#hide1').prop('checked', false);
                if(hide2){
                    jQuery('.table-header').hide();
                }
                jQuery('.table-ttd').hide();
            }
        }
    }
    function hide_rekening_objek(that){
        var checked = jQuery(that).is(':checked');
        if(checked){
            jQuery('.rincian_objek').hide();
            jQuery('.sub_rincian_objek').hide();
            jQuery('.colspan_kurang').map(function(i, b){
                var colspan = +jQuery(b).attr('colspan');
                jQuery(b).attr('colspan', colspan-2);
            });
        }else{
            jQuery('.rincian_objek').show();
            jQuery('.sub_rincian_objek').show();
            jQuery('.colspan_kurang').map(function(i, b){
                var colspan = +jQuery(b).attr('colspan');
                jQuery(b).attr('colspan', colspan+2);
            });
        }
    }

    var _url = window.location.href;
    var url = new URL(_url);
    _url = url.origin+url.pathname+'?key='+url.searchParams.get('key');
    var type = url.searchParams.get("type");
    if(type && type=='pergeseran'){
        var extend_action = '<a class="button button-primary" target="_blank" href="'+_url+'" style="margin-left: 10px;">Print APBD Lampiran 2</a>';
    }else{
        var extend_action = '<a class="button button-primary" target="_blank" href="'+_url+'&type=pergeseran" style="margin-left: 10px;">Print Pergeseran/Perubahan APBD Lampiran 2</a>';
    }
    extend_action += ''
        +'<div style="margin-top: 15px">'
            +'<label><input id="hide1" type="checkbox" onclick="hide_header_ttd(this, 1)"> Sembunyikan header & TTD</label>'
            +'<label style="margin-left: 25px;"><input id="hide2" type="checkbox" onclick="hide_header_ttd(this, 2)"> Sembunyikan header</label>'
            +'<label style="margin-left: 25px;"><input id="hide3" type="checkbox" onclick="hide_header_ttd(this, 3)"> Sembunyikan TTD</label>'
            +'<label style="margin-left: 25px;"><input type="checkbox" onclick="hide_rekening_objek(this)"> Sembunyikan Rekening Objek & Sub Rekening Objek</label>'
        +'</div>';
    jQuery('#action-sipd').append(extend_action);
</script>