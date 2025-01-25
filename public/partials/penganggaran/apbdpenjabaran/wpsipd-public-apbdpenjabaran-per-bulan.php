<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
function ubah_minus($nilai){
    if($nilai < 0){
        $nilai = abs($nilai);
        return '('.number_format($nilai,2,",",".").')';
    }else{
        return number_format($nilai,2,",",".");
    }
}

function generate_body($rek_all, $baris_kosong=false, $type='murni', $nama_rekening){
    global $wpdb;
    global $pendapatan_1;
    global $belanja_1;
    global $pembiayaan_penerimaan_1;
    global $pembiayaan_pengeluaran_1;
    global $pendapatan_2;
    global $belanja_2;
    global $pembiayaan_penerimaan_2;
    global $pembiayaan_pengeluaran_2;
    global $pendapatan_3;
    global $belanja_3;
    global $pembiayaan_penerimaan_3;
    global $pembiayaan_pengeluaran_3;
    global $pendapatan_4;
    global $belanja_4;
    global $pembiayaan_penerimaan_4;
    global $pembiayaan_pengeluaran_4;
    global $pendapatan_5;
    global $belanja_5;
    global $pembiayaan_penerimaan_5;
    global $pembiayaan_pengeluaran_5;
    global $pendapatan_6;
    global $belanja_6;
    global $pembiayaan_penerimaan_6;
    global $pembiayaan_pengeluaran_6;
    global $pendapatan_7;
    global $belanja_7;
    global $pembiayaan_penerimaan_7;
    global $pembiayaan_pengeluaran_7;
    global $pendapatan_8;
    global $belanja_8;
    global $pembiayaan_penerimaan_8;
    global $pembiayaan_pengeluaran_8;
    global $pendapatan_9;
    global $belanja_9;
    global $pembiayaan_penerimaan_9;
    global $pembiayaan_pengeluaran_9;
    global $pendapatan_10;
    global $belanja_10;
    global $pembiayaan_penerimaan_10;
    global $pembiayaan_pengeluaran_10;
    global $pendapatan_11;
    global $belanja_11;
    global $pembiayaan_penerimaan_11;
    global $pembiayaan_pengeluaran_11;
    global $pendapatan_12;
    global $belanja_12;
    global $pembiayaan_penerimaan_12;
    global $pembiayaan_pengeluaran_12;
    global $pendapatan;
    global $belanja;
    global $pembiayaan_penerimaan;
    global $pembiayaan_pengeluaran;
    global $pendapatan_pagu;
    global $belanja_pagu;
    global $pembiayaan_penerimaan_pagu;
    global $pembiayaan_pengeluaran_pagu;
    global $pendapatan_realisasi;
    global $belanja_realisasi;
    global $pembiayaan_penerimaan_realisasi;
    global $pembiayaan_pengeluaran_realisasi;
    $data_all = array(
        'data' => array(),
        'realisasi' => 0,
        'bulan_1' => 0,
        'bulan_2' => 0,
        'bulan_3' => 0,
        'bulan_4' => 0,
        'bulan_5' => 0,
        'bulan_6' => 0,
        'bulan_7' => 0,
        'bulan_8' => 0,
        'bulan_9' => 0,
        'bulan_10' => 0,
        'bulan_11' => 0,
        'bulan_12' => 0,
        'pagu' => 0,
        'total' => 0
    );
    foreach ($rek_all as $k => $v) {
        $rek = explode('.', $v['kode_akun']);
        $kode_akun = $rek[0];
        if(!$kode_akun){
            // print_r($v); die();
            continue;
        }
        if(empty($data_all['data'][$kode_akun])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun."'", ARRAY_A);
            $data_all['data'][$kode_akun] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'bulan_1' => 0,
                'bulan_2' => 0,
                'bulan_3' => 0,
                'bulan_4' => 0,
                'bulan_5' => 0,
                'bulan_6' => 0,
                'bulan_7' => 0,
                'bulan_8' => 0,
                'bulan_9' => 0,
                'bulan_10' => 0,
                'bulan_11' => 0,
                'bulan_12' => 0,
                'pagu' => 0,
                'total' => 0
            );
        }
        $kode_akun1 = $kode_akun.'.'.$rek[1];
        if(empty($data_all['data'][$kode_akun]['data'][$kode_akun1])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun1."'", ARRAY_A);
            $data_all['data'][$kode_akun]['data'][$kode_akun1] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'bulan_1' => 0,
                'bulan_2' => 0,
                'bulan_3' => 0,
                'bulan_4' => 0,
                'bulan_5' => 0,
                'bulan_6' => 0,
                'bulan_7' => 0,
                'bulan_8' => 0,
                'bulan_9' => 0,
                'bulan_10' => 0,
                'bulan_11' => 0,
                'bulan_12' => 0,
                'pagu' => 0,
                'total' => 0
            );
        }
        $kode_akun2 = $kode_akun1.'.'.$rek[2];
        if(empty($data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun2."'", ARRAY_A);
            $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'bulan_1' => 0,
                'bulan_2' => 0,
                'bulan_3' => 0,
                'bulan_4' => 0,
                'bulan_5' => 0,
                'bulan_6' => 0,
                'bulan_7' => 0,
                'bulan_8' => 0,
                'bulan_9' => 0,
                'bulan_10' => 0,
                'bulan_11' => 0,
                'bulan_12' => 0,
                'pagu' => 0,
                'total' => 0
            );
        }
        $kode_akun3 = $kode_akun2.'.'.$rek[3];
        if(empty($data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun3."'", ARRAY_A);
            $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'bulan_1' => 0,
                'bulan_2' => 0,
                'bulan_3' => 0,
                'bulan_4' => 0,
                'bulan_5' => 0,
                'bulan_6' => 0,
                'bulan_7' => 0,
                'bulan_8' => 0,
                'bulan_9' => 0,
                'bulan_10' => 0,
                'bulan_11' => 0,
                'bulan_12' => 0,
                'pagu' => 0,
                'total' => 0
            );
        }
        $kode_akun4 = $kode_akun3.'.'.$rek[4];
        if(empty($data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun4."'", ARRAY_A);
            $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'bulan_1' => 0,
                'bulan_2' => 0,
                'bulan_3' => 0,
                'bulan_4' => 0,
                'bulan_5' => 0,
                'bulan_6' => 0,
                'bulan_7' => 0,
                'bulan_8' => 0,
                'bulan_9' => 0,
                'bulan_10' => 0,
                'bulan_11' => 0,
                'bulan_12' => 0,
                'pagu' => 0,
                'total' => 0
            );
        }
        $kode_akun5 = $kode_akun4.'.'.$rek[5];
        if(empty($data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5])){
            $nama_akun = $wpdb->get_results("SELECT nama_akun from data_akun where kode_akun='".$kode_akun5."'", ARRAY_A);
            $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5] = array(
                'data' => array(),
                'realisasi' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'bulan_1' => 0,
                'bulan_2' => 0,
                'bulan_3' => 0,
                'bulan_4' => 0,
                'bulan_5' => 0,
                'bulan_6' => 0,
                'bulan_7' => 0,
                'bulan_8' => 0,
                'bulan_9' => 0,
                'bulan_10' => 0,
                'bulan_11' => 0,
                'bulan_12' => 0,
                'pagu' => 0,
                'total' => 0
            );
        }
        if(!isset($v['bulan_1'])){
            $v['bulan_1'] = 0;
        }
        if(!isset($v['bulan_2'])){
            $v['bulan_2'] = 0;
        }
        if(!isset($v['bulan_3'])){
            $v['bulan_3'] = 0;
        }
        if(!isset($v['bulan_4'])){
            $v['bulan_4'] = 0;
        }
        if(!isset($v['bulan_5'])){
            $v['bulan_5'] = 0;
        }
        if(!isset($v['bulan_6'])){
            $v['bulan_6'] = 0;
        }
        if(!isset($v['bulan_7'])){
            $v['bulan_7'] = 0;
        }
        if(!isset($v['bulan_8'])){
            $v['bulan_8'] = 0;
        }
        if(!isset($v['bulan_9'])){
            $v['bulan_9'] = 0;
        }
        if(!isset($v['bulan_10'])){
            $v['bulan_10'] = 0;
        }
        if(!isset($v['bulan_11'])){
            $v['bulan_11'] = 0;
        }
        if(!isset($v['bulan_12'])){
            $v['bulan_12'] = 0;
        }
        if(!isset($v['total'])){
            $v['total'] = 0;
        }
        if(!isset($v['pagu'])){
            $v['pagu'] = 0;
        }
        if(!isset($v['realisasi'])){
            $v['realisasi'] = 0;
        }
        $data_all['bulan_1'] += $v['bulan_1'];
        $data_all['data'][$kode_akun]['bulan_1'] += $v['bulan_1'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_1'] += $v['bulan_1'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_1'] += $v['bulan_1'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_1'] += $v['bulan_1'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_1'] += $v['bulan_1'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_1'] += $v['bulan_1'];
        $data_all['bulan_2'] += $v['bulan_2'];
        $data_all['data'][$kode_akun]['bulan_2'] += $v['bulan_2'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_2'] += $v['bulan_2'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_2'] += $v['bulan_2'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_2'] += $v['bulan_2'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_2'] += $v['bulan_2'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_2'] += $v['bulan_2'];
        $data_all['bulan_3'] += $v['bulan_3'];
        $data_all['data'][$kode_akun]['bulan_3'] += $v['bulan_3'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_3'] += $v['bulan_3'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_3'] += $v['bulan_3'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_3'] += $v['bulan_3'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_3'] += $v['bulan_3'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_3'] += $v['bulan_3'];
        $data_all['bulan_4'] += $v['bulan_4'];
        $data_all['data'][$kode_akun]['bulan_4'] += $v['bulan_4'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_4'] += $v['bulan_4'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_4'] += $v['bulan_4'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_4'] += $v['bulan_4'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_4'] += $v['bulan_4'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_4'] += $v['bulan_4'];
        $data_all['bulan_5'] += $v['bulan_5'];
        $data_all['data'][$kode_akun]['bulan_5'] += $v['bulan_5'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_5'] += $v['bulan_5'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_5'] += $v['bulan_5'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_5'] += $v['bulan_5'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_5'] += $v['bulan_5'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_5'] += $v['bulan_5'];
        $data_all['bulan_6'] += $v['bulan_6'];
        $data_all['data'][$kode_akun]['bulan_6'] += $v['bulan_6'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_6'] += $v['bulan_6'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_6'] += $v['bulan_6'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_6'] += $v['bulan_6'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_6'] += $v['bulan_6'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_6'] += $v['bulan_6'];
        $data_all['bulan_7'] += $v['bulan_7'];
        $data_all['data'][$kode_akun]['bulan_7'] += $v['bulan_7'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_7'] += $v['bulan_7'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_7'] += $v['bulan_7'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_7'] += $v['bulan_7'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_7'] += $v['bulan_7'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_7'] += $v['bulan_7'];
        $data_all['bulan_8'] += $v['bulan_8'];
        $data_all['data'][$kode_akun]['bulan_8'] += $v['bulan_8'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_8'] += $v['bulan_8'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_8'] += $v['bulan_8'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_8'] += $v['bulan_8'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_8'] += $v['bulan_8'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_8'] += $v['bulan_8'];
        $data_all['bulan_9'] += $v['bulan_9'];
        $data_all['data'][$kode_akun]['bulan_9'] += $v['bulan_9'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_9'] += $v['bulan_9'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_9'] += $v['bulan_9'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_9'] += $v['bulan_9'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_9'] += $v['bulan_9'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_9'] += $v['bulan_9'];
        $data_all['bulan_10'] += $v['bulan_10'];
        $data_all['data'][$kode_akun]['bulan_10'] += $v['bulan_10'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_10'] += $v['bulan_10'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_10'] += $v['bulan_10'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_10'] += $v['bulan_10'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_10'] += $v['bulan_10'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_10'] += $v['bulan_10'];
        $data_all['bulan_11'] += $v['bulan_11'];
        $data_all['data'][$kode_akun]['bulan_11'] += $v['bulan_11'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_11'] += $v['bulan_11'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_11'] += $v['bulan_11'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_11'] += $v['bulan_11'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_11'] += $v['bulan_11'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_11'] += $v['bulan_11'];
        $data_all['bulan_12'] += $v['bulan_12'];
        $data_all['data'][$kode_akun]['bulan_12'] += $v['bulan_12'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['bulan_12'] += $v['bulan_12'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['bulan_12'] += $v['bulan_12'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['bulan_12'] += $v['bulan_12'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['bulan_12'] += $v['bulan_12'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['bulan_12'] += $v['bulan_12'];
        $data_all['pagu'] += $v['pagu'];
        $data_all['data'][$kode_akun]['pagu'] += $v['pagu'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['pagu'] += $v['pagu'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['pagu'] += $v['pagu'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['pagu'] += $v['pagu'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['pagu'] += $v['pagu'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['pagu'] += $v['pagu'];
        $data_all['total'] += $v['total'];
        $data_all['data'][$kode_akun]['total'] += $v['total'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['total'] += $v['total'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['total'] += $v['total'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['total'] += $v['total'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['total'] += $v['total'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['total'] += $v['total'];
        $data_all['realisasi'] += $v['realisasi'];
        $data_all['data'][$kode_akun]['realisasi'] += $v['realisasi'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['realisasi'] += $v['realisasi'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['realisasi'] += $v['realisasi'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['realisasi'] += $v['realisasi'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['realisasi'] += $v['realisasi'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['realisasi'] += $v['realisasi'];
    }

    $body_html = '';
    $total = 0;
    foreach ($data_all['data'] as $k => $v) {
        $body_html .= "
        <tr class='rek_1'>
            <td class='kiri kanan bawah text_blok'>".$k."</td>
            <td class='kanan bawah text_blok'>".$v['nama']."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_1'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_2'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_3'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_4'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_5'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_6'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_7'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_8'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_9'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_10'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_11'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['bulan_12'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['total'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['pagu'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['realisasi'])."</td>
        </tr>";
        foreach ($v['data'] as $kk => $vv) {
            $body_html .= "
            <tr class='rek_2'>
                <td class='kiri kanan bawah text_blok'>".$kk."</td>
                <td class='kanan bawah text_blok'>".$vv['nama']."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_1'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_2'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_3'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_4'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_5'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_6'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_7'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_8'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_9'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_10'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_11'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['bulan_12'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['total'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['pagu'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['realisasi'])."</td>
            </tr>";
            foreach ($vv['data'] as $kkk => $vvv) {
                $body_html .= "
                <tr class='rek_3'>
                    <td class='kiri kanan bawah'>".$kkk."</td>
                    <td class='kanan bawah'>".$vvv['nama']."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_1'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_2'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_3'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_4'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_5'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_6'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_7'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_8'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_9'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_10'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_11'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['bulan_12'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['total'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['pagu'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['realisasi'])."</td>
                </tr>";
                foreach ($vvv['data'] as $kkkk => $vvvv) {
                    $body_html .= "
                    <tr class='rek_4'>
                        <td class='kiri kanan bawah'>".$kkkk."</td>
                        <td class='kanan bawah'>".$vvvv['nama']."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_1'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_2'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_3'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_4'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_5'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_6'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_7'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_8'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_9'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_10'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_11'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['bulan_12'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['total'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['pagu'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['realisasi'])."</td>
                    </tr>";
                    foreach ($vvvv['data'] as $kkkkk => $vvvvv) {
                        $body_html .= "
                        <tr class='rek_5'>
                            <td class='kiri kanan bawah'>".$kkkkk."</td>
                            <td class='kanan bawah'>".$vvvvv['nama']."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_1'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_2'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_3'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_4'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_5'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_6'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_7'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_8'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_9'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_10'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_11'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['bulan_12'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['total'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['pagu'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['realisasi'])."</td>
                        </tr>";
                        foreach ($vvvvv['data'] as $kkkkkk => $vvvvvv) {
                            $body_html .= "
                            <tr class='rek_6'>
                                <td class='kiri kanan bawah'>".$kkkkkk."</td>
                                <td class='kanan bawah'>".$vvvvvv['nama']."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_1'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_2'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_3'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_4'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_5'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_6'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_7'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_8'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_9'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_10'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_11'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['bulan_12'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['total'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['pagu'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['realisasi'])."</td>
                            </tr>";
                        }
                    }
                }
            }
        }
    }

    $body_html .= "
    <tr>
        <td class='kiri kanan bawah'></td>
        <td class='kanan bawah text_kanan text_blok'>Jumlah ".$nama_rekening."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_1'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_2'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_3'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_4'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_5'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_6'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_7'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_8'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_9'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_10'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_11'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['bulan_12'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['total'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['pagu'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['realisasi'])."</td>
    </tr>";
    if($nama_rekening == 'Pendapatan'){
        $pendapatan_1 = $data_all['bulan_1'];
        $pendapatan_2 = $data_all['bulan_2'];
        $pendapatan_3 = $data_all['bulan_3'];
        $pendapatan_4 = $data_all['bulan_4'];
        $pendapatan_5 = $data_all['bulan_5'];
        $pendapatan_6 = $data_all['bulan_6'];
        $pendapatan_7 = $data_all['bulan_7'];
        $pendapatan_8 = $data_all['bulan_8'];
        $pendapatan_9 = $data_all['bulan_9'];
        $pendapatan_10 = $data_all['bulan_10'];
        $pendapatan_11 = $data_all['bulan_11'];
        $pendapatan_12 = $data_all['bulan_12'];
        $pendapatan = $data_all['total'];
        $pendapatan_pagu = $data_all['pagu'];
        $pendapatan_realisasi = $data_all['realisasi'];
    }else if($nama_rekening == 'Belanja'){
        $belanja_1 = $data_all['bulan_1'];
        $belanja_2 = $data_all['bulan_2'];
        $belanja_3 = $data_all['bulan_3'];
        $belanja_4 = $data_all['bulan_4'];
        $belanja_5 = $data_all['bulan_5'];
        $belanja_6 = $data_all['bulan_6'];
        $belanja_7 = $data_all['bulan_7'];
        $belanja_8 = $data_all['bulan_8'];
        $belanja_9 = $data_all['bulan_9'];
        $belanja_10 = $data_all['bulan_10'];
        $belanja_11 = $data_all['bulan_11'];
        $belanja_12 = $data_all['bulan_12'];
        $belanja = $data_all['total'];
        $belanja_pagu = $data_all['pagu'];
        $belanja_realisasi = $data_all['realisasi'];
        $body_html .= "
        <tr>
            <td class='kiri kanan bawah'></td>
            <td class='kanan bawah text_kanan text_blok'>Total Surplus/(Defisit)</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pendapatan_1-$belanja_1)."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2)-($belanja_1+$belanja_2))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3)-($belanja_1+$belanja_2+$belanja_3))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3+$pendapatan_4)-($belanja_1+$belanja_2+$belanja_3+$belanja_4))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3+$pendapatan_4+$pendapatan_5)-($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3+$pendapatan_4+$pendapatan_5+$pendapatan_6)-($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3+$pendapatan_4+$pendapatan_5+$pendapatan_6+$pendapatan_7)-($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3+$pendapatan_4+$pendapatan_5+$pendapatan_6+$pendapatan_7+$pendapatan_8)-($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7+$belanja_8))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3+$pendapatan_4+$pendapatan_5+$pendapatan_6+$pendapatan_7+$pendapatan_8+$pendapatan_9)-($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7+$belanja_8+$belanja_9))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3+$pendapatan_4+$pendapatan_5+$pendapatan_6+$pendapatan_7+$pendapatan_8+$pendapatan_9+$pendapatan_10)-($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7+$belanja_8+$belanja_9+$belanja_10))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3+$pendapatan_4+$pendapatan_5+$pendapatan_6+$pendapatan_7+$pendapatan_8+$pendapatan_9+$pendapatan_10+$pendapatan_11)-($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7+$belanja_8+$belanja_9+$belanja_10+$belanja_11))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3+$pendapatan_4+$pendapatan_5+$pendapatan_6+$pendapatan_7+$pendapatan_8+$pendapatan_9+$pendapatan_10+$pendapatan_11+$pendapatan_12)-($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7+$belanja_8+$belanja_9+$belanja_10+$belanja_11+$belanja_12))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pendapatan-$belanja)."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pendapatan_pagu-$belanja_pagu)."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pendapatan_realisasi-$belanja_realisasi)."</td>
        </tr>";
    }else if($nama_rekening == 'Penerimaan Pembiayaan'){
        $pembiayaan_penerimaan_1 = $data_all['bulan_1'];
        $pembiayaan_penerimaan_2 = $data_all['bulan_2'];
        $pembiayaan_penerimaan_3 = $data_all['bulan_3'];
        $pembiayaan_penerimaan_4 = $data_all['bulan_4'];
        $pembiayaan_penerimaan_5 = $data_all['bulan_5'];
        $pembiayaan_penerimaan_6 = $data_all['bulan_6'];
        $pembiayaan_penerimaan_7 = $data_all['bulan_7'];
        $pembiayaan_penerimaan_8 = $data_all['bulan_8'];
        $pembiayaan_penerimaan_9 = $data_all['bulan_9'];
        $pembiayaan_penerimaan_10 = $data_all['bulan_10'];
        $pembiayaan_penerimaan_11 = $data_all['bulan_11'];
        $pembiayaan_penerimaan_12 = $data_all['bulan_12'];
        $pembiayaan_penerimaan = $data_all['total'];
        $pembiayaan_penerimaan_pagu = $data_all['pagu'];
        $pembiayaan_penerimaan_realisasi = $data_all['realisasi'];
    }else if($nama_rekening == 'Pengeluaran Pembiayaan'){
        $pembiayaan_pengeluaran_1 = $data_all['bulan_1'];
        $pembiayaan_pengeluaran_2 = $data_all['bulan_2'];
        $pembiayaan_pengeluaran_3 = $data_all['bulan_3'];
        $pembiayaan_pengeluaran_4 = $data_all['bulan_4'];
        $pembiayaan_pengeluaran_5 = $data_all['bulan_5'];
        $pembiayaan_pengeluaran_6 = $data_all['bulan_6'];
        $pembiayaan_pengeluaran_7 = $data_all['bulan_7'];
        $pembiayaan_pengeluaran_8 = $data_all['bulan_8'];
        $pembiayaan_pengeluaran_9 = $data_all['bulan_9'];
        $pembiayaan_pengeluaran_10 = $data_all['bulan_10'];
        $pembiayaan_pengeluaran_11 = $data_all['bulan_11'];
        $pembiayaan_pengeluaran_12 = $data_all['bulan_12'];
        $pembiayaan_pengeluaran = $data_all['total'];
        $pembiayaan_pengeluaran_pagu = $data_all['pagu'];
        $pembiayaan_pengeluaran_realisasi = $data_all['realisasi'];
        $body_html .= "
        <tr>
            <td class='kiri kanan bawah'></td>
            <td class='kanan bawah text_kanan text_blok'>Total Surplus/(Defisit)</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pembiayaan_penerimaan_1-$pembiayaan_pengeluaran_1)."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7+$pembiayaan_penerimaan_8)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7+$pembiayaan_pengeluaran_8))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7+$pembiayaan_penerimaan_8+$pembiayaan_penerimaan_9)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7+$pembiayaan_pengeluaran_8+$pembiayaan_pengeluaran_9))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7+$pembiayaan_penerimaan_8+$pembiayaan_penerimaan_9+$pembiayaan_penerimaan_10)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7+$pembiayaan_pengeluaran_8+$pembiayaan_pengeluaran_9+$pembiayaan_pengeluaran_10))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7+$pembiayaan_penerimaan_8+$pembiayaan_penerimaan_9+$pembiayaan_penerimaan_10+$pembiayaan_penerimaan_11)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7+$pembiayaan_pengeluaran_8+$pembiayaan_pengeluaran_9+$pembiayaan_pengeluaran_10+$pembiayaan_pengeluaran_11))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7+$pembiayaan_penerimaan_8+$pembiayaan_penerimaan_9+$pembiayaan_penerimaan_10+$pembiayaan_penerimaan_11+$pembiayaan_penerimaan_12)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7+$pembiayaan_pengeluaran_8+$pembiayaan_pengeluaran_9+$pembiayaan_pengeluaran_10+$pembiayaan_pengeluaran_11+$pembiayaan_pengeluaran_12))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pembiayaan_penerimaan-$pembiayaan_pengeluaran)."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pembiayaan_penerimaan_pagu-$pembiayaan_pengeluaran_pagu)."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pembiayaan_penerimaan_realisasi-$pembiayaan_pengeluaran_realisasi)."</td>
        </tr>
        <tr>
            <td class='kiri kanan bawah text_blok'>6.3</td>
            <td class='kanan bawah text_kiri text_blok'>Sisa Lebih Pembiayaan Anggaran Daerah Tahun Berkenaan (SILPA)</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($belanja_1 - ($pembiayaan_penerimaan_1 - $pembiayaan_pengeluaran_1))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3+$belanja_4) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7+$belanja_8) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7+$pembiayaan_penerimaan_8) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7+$pembiayaan_pengeluaran_8)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7+$belanja_8+$belanja_9) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7+$pembiayaan_penerimaan_8+$pembiayaan_penerimaan_9) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7+$pembiayaan_pengeluaran_8+$pembiayaan_pengeluaran_9)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7+$belanja_8+$belanja_9+$belanja_10) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7+$pembiayaan_penerimaan_8+$pembiayaan_penerimaan_9+$pembiayaan_penerimaan_10) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7+$pembiayaan_pengeluaran_8+$pembiayaan_pengeluaran_9+$pembiayaan_pengeluaran_10)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7+$belanja_8+$belanja_9+$belanja_10+$belanja_11) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7+$pembiayaan_penerimaan_8+$pembiayaan_penerimaan_9+$pembiayaan_penerimaan_10+$pembiayaan_penerimaan_11) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7+$pembiayaan_pengeluaran_8+$pembiayaan_pengeluaran_9+$pembiayaan_pengeluaran_10+$pembiayaan_pengeluaran_11)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3+$belanja_4+$belanja_5+$belanja_6+$belanja_7+$belanja_8+$belanja_9+$belanja_10+$belanja_11+$belanja_12) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4+$pembiayaan_penerimaan_5+$pembiayaan_penerimaan_6+$pembiayaan_penerimaan_7+$pembiayaan_penerimaan_8+$pembiayaan_penerimaan_9+$pembiayaan_penerimaan_10+$pembiayaan_penerimaan_11+$pembiayaan_penerimaan_12) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4+$pembiayaan_pengeluaran_5+$pembiayaan_pengeluaran_6+$pembiayaan_pengeluaran_7+$pembiayaan_pengeluaran_8+$pembiayaan_pengeluaran_9+$pembiayaan_pengeluaran_10+$pembiayaan_pengeluaran_11+$pembiayaan_pengeluaran_12)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($belanja - ($pembiayaan_penerimaan - $pembiayaan_pengeluaran))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($belanja - ($pembiayaan_penerimaan_pagu - $pembiayaan_pengeluaran_pagu))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($belanja - ($pembiayaan_penerimaan_realisasi - $pembiayaan_pengeluaran_realisasi))."</td>
        </tr>";
    }
    if($baris_kosong){
        $body_html .= "
        <tr>
            <td class='kiri kanan bawah' style='color: #fff;'>.</td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
            <td class='kanan bawah'></td>
        </tr>";
    }
    return $body_html;
}

global $wpdb;
$GLOBALS['pendapatan_1'] = 0;
$GLOBALS['belanja_1'] = 0;
$GLOBALS['pembiayaan_penerimaan_1'] = 0;
$GLOBALS['pembiayaan_pengeluaran_1'] = 0;
$GLOBALS['pendapatan_2'] = 0;
$GLOBALS['belanja_2'] = 0;
$GLOBALS['pembiayaan_penerimaan_2'] = 0;
$GLOBALS['pembiayaan_pengeluaran_2'] = 0;
$GLOBALS['pendapatan_3'] = 0;
$GLOBALS['belanja_3'] = 0;
$GLOBALS['pembiayaan_penerimaan_3'] = 0;
$GLOBALS['pembiayaan_pengeluaran_3'] = 0;
$GLOBALS['pendapatan_4'] = 0;
$GLOBALS['belanja_4'] = 0;
$GLOBALS['pembiayaan_penerimaan_4'] = 0;
$GLOBALS['pembiayaan_pengeluaran_4'] = 0;
$GLOBALS['pendapatan_5'] = 0;
$GLOBALS['belanja_5'] = 0;
$GLOBALS['pembiayaan_penerimaan_5'] = 0;
$GLOBALS['pembiayaan_pengeluaran_5'] = 0;
$GLOBALS['pendapatan_6'] = 0;
$GLOBALS['belanja_6'] = 0;
$GLOBALS['pembiayaan_penerimaan_6'] = 0;
$GLOBALS['pembiayaan_pengeluaran_6'] = 0;
$GLOBALS['pendapatan_7'] = 0;
$GLOBALS['belanja_7'] = 0;
$GLOBALS['pembiayaan_penerimaan_7'] = 0;
$GLOBALS['pembiayaan_pengeluaran_7'] = 0;
$GLOBALS['pendapatan_8'] = 0;
$GLOBALS['belanja_8'] = 0;
$GLOBALS['pembiayaan_penerimaan_8'] = 0;
$GLOBALS['pembiayaan_pengeluaran_8'] = 0;
$GLOBALS['pendapatan_9'] = 0;
$GLOBALS['belanja_9'] = 0;
$GLOBALS['pembiayaan_penerimaan_9'] = 0;
$GLOBALS['pembiayaan_pengeluaran_9'] = 0;
$GLOBALS['pendapatan_10'] = 0;
$GLOBALS['belanja_10'] = 0;
$GLOBALS['pembiayaan_penerimaan_10'] = 0;
$GLOBALS['pembiayaan_pengeluaran_10'] = 0;
$GLOBALS['pendapatan_11'] = 0;
$GLOBALS['belanja_11'] = 0;
$GLOBALS['pembiayaan_penerimaan_11'] = 0;
$GLOBALS['pembiayaan_pengeluaran_11'] = 0;
$GLOBALS['pendapatan_12'] = 0;
$GLOBALS['belanja_12'] = 0;
$GLOBALS['pembiayaan_penerimaan_12'] = 0;
$GLOBALS['pembiayaan_pengeluaran_12'] = 0;
$GLOBALS['pendapatan'] = 0;
$GLOBALS['belanja'] = 0;
$GLOBALS['pembiayaan_penerimaan'] = 0;
$GLOBALS['pembiayaan_pengeluaran'] = 0;

if(!empty($_GET) && !empty($_GET['id_skpd'])){
    $input['id_skpd'] = $_GET['id_skpd'];
}
$type = false;

if(!empty($input['id_skpd'])){
    $sql_anggaran = $wpdb->prepare("
        select 
            0 as realisasi,
            kode_akun,
            nama_akun,
            sum(total) as total,
            sum(nilaimurni) as totalmurni
        from data_pendapatan
        where tahun_anggaran=%d
            and active=1
            and id_skpd=%d
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
    $sql = $wpdb->prepare("
        select 
            0 as pagu,
            kode_akun,
            nama_akun,
            sum(bulan_1) as bulan_1,
            sum(bulan_2) as bulan_2,
            sum(bulan_3) as bulan_3,
            sum(bulan_4) as bulan_4,
            sum(bulan_5) as bulan_5,
            sum(bulan_6) as bulan_6,
            sum(bulan_7) as bulan_7,
            sum(bulan_8) as bulan_8,
            sum(bulan_9) as bulan_9,
            sum(bulan_10) as bulan_10,
            sum(bulan_11) as bulan_11,
            sum(bulan_12) as bulan_12,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas k
        where tahun_anggaran=%d
            and active=1
            and type='pendapatan'
            and id_sub_skpd=%d
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
}else{
    $sql_anggaran = $wpdb->prepare("
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
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            0 as pagu,
            kode_akun,
            nama_akun,
            sum(bulan_1) as bulan_1,
            sum(bulan_2) as bulan_2,
            sum(bulan_3) as bulan_3,
            sum(bulan_4) as bulan_4,
            sum(bulan_5) as bulan_5,
            sum(bulan_6) as bulan_6,
            sum(bulan_7) as bulan_7,
            sum(bulan_8) as bulan_8,
            sum(bulan_9) as bulan_9,
            sum(bulan_10) as bulan_10,
            sum(bulan_11) as bulan_11,
            sum(bulan_12) as bulan_12,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas k
        where tahun_anggaran=%d
            and active=1
            and type='pendapatan'
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_anggaran = $wpdb->get_results($sql_anggaran, ARRAY_A);
$data_anggaran = array();
foreach($rek_anggaran as $k => $rek){
    $data_anggaran[$rek['kode_akun']] = $rek;
}
$rek_pendapatan = $wpdb->get_results($sql, ARRAY_A);
foreach($rek_pendapatan as $k => $rek){
    if(!empty($data_anggaran[$rek['kode_akun']])){
        $rek_pendapatan[$k]['pagu'] = $data_anggaran[$rek['kode_akun']]['total'];
    }
}
// print_r($rek_pendapatan); die($sql);

$body_pendapatan = generate_body($rek_pendapatan, true, $type, 'Pendapatan');

if(!empty($input['id_skpd'])){
    $sql_anggaran = $wpdb->prepare("
        select 
            r.kode_akun,
            r.nama_akun,
            sum(r.rincian) as total,
            sum(r.rincian_murni) as totalmurni
        from data_rka r
        inner join data_sub_keg_bl s on s.kode_sbl = r.kode_sbl
            and s.active = r.active
            and s.tahun_anggaran = r.tahun_anggaran
        where r.tahun_anggaran=%d
            and r.active=1
            and s.id_sub_skpd=%d
        group by r.kode_akun
        order by r.kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            0 as pagu,
            kode_akun,
            nama_akun,
            sum(bulan_1) as bulan_1,
            sum(bulan_2) as bulan_2,
            sum(bulan_3) as bulan_3,
            sum(bulan_4) as bulan_4,
            sum(bulan_5) as bulan_5,
            sum(bulan_6) as bulan_6,
            sum(bulan_7) as bulan_7,
            sum(bulan_8) as bulan_8,
            sum(bulan_9) as bulan_9,
            sum(bulan_10) as bulan_10,
            sum(bulan_11) as bulan_11,
            sum(bulan_12) as bulan_12,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas k
        where tahun_anggaran=%d
            and active=1
            and id_sub_skpd=%d
            and type='belanja'
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
}else{
    $sql_anggaran = $wpdb->prepare("
        select 
            r.kode_akun,
            r.nama_akun,
            sum(r.rincian) as total,
            sum(r.rincian_murni) as totalmurni
        from data_rka r
        inner join data_sub_keg_bl s on s.kode_sbl = r.kode_sbl
            and s.active = r.active
            and s.tahun_anggaran = r.tahun_anggaran
        where r.tahun_anggaran=%d
            and r.active=1
        group by r.kode_akun
        order by r.kode_akun ASC
    ", $input['tahun_anggaran']);

    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            0 as pagu,
            kode_akun,
            nama_akun,
            sum(bulan_1) as bulan_1,
            sum(bulan_2) as bulan_2,
            sum(bulan_3) as bulan_3,
            sum(bulan_4) as bulan_4,
            sum(bulan_5) as bulan_5,
            sum(bulan_6) as bulan_6,
            sum(bulan_7) as bulan_7,
            sum(bulan_8) as bulan_8,
            sum(bulan_9) as bulan_9,
            sum(bulan_10) as bulan_10,
            sum(bulan_11) as bulan_11,
            sum(bulan_12) as bulan_12,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas k
        where tahun_anggaran=%d
            and active=1
            and type='belanja'
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_anggaran = $wpdb->get_results($sql_anggaran, ARRAY_A);
$data_anggaran = array();
foreach($rek_anggaran as $k => $rek){
    $data_anggaran[$rek['kode_akun']] = $rek;
}
$rek_belanja = $wpdb->get_results($sql, ARRAY_A);
foreach($rek_belanja as $k => $rek){
    if(!empty($data_anggaran[$rek['kode_akun']])){
        $rek_belanja[$k]['pagu'] = $data_anggaran[$rek['kode_akun']]['total'];
    }
}

$body_belanja = generate_body($rek_belanja, true, $type, 'Belanja');

if(!empty($input['id_skpd'])){
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            (
                SELECT 
                    sum(r.total) 
                FROM data_pembiayaan r 
                WHERE r.tahun_anggaran=k.tahun_anggaran 
                    AND r.active=1 
                    AND r.kode_akun=k.kode_akun
                    AND r.id_skpd=k.id_sub_skpd
                    AND type='penerimaan'
            ) as pagu,
            kode_akun,
            nama_akun,
            sum(bulan_1) as bulan_1,
            sum(bulan_2) as bulan_2,
            sum(bulan_3) as bulan_3,
            sum(bulan_4) as bulan_4,
            sum(bulan_5) as bulan_5,
            sum(bulan_6) as bulan_6,
            sum(bulan_7) as bulan_7,
            sum(bulan_8) as bulan_8,
            sum(bulan_9) as bulan_9,
            sum(bulan_10) as bulan_10,
            sum(bulan_11) as bulan_11,
            sum(bulan_12) as bulan_12,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas k
        where tahun_anggaran=%d
            and type='pembiayaan-penerimaan'
            and active=1
            and id_sub_skpd=%d
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
}else{
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            (
                SELECT 
                    sum(r.total) 
                FROM data_pembiayaan r 
                WHERE r.tahun_anggaran=k.tahun_anggaran 
                    AND r.active=1 
                    AND r.kode_akun=k.kode_akun
                    AND type='penerimaan'
            ) as pagu,
            kode_akun,
            nama_akun,
            sum(bulan_1) as bulan_1,
            sum(bulan_2) as bulan_2,
            sum(bulan_3) as bulan_3,
            sum(bulan_4) as bulan_4,
            sum(bulan_5) as bulan_5,
            sum(bulan_6) as bulan_6,
            sum(bulan_7) as bulan_7,
            sum(bulan_8) as bulan_8,
            sum(bulan_9) as bulan_9,
            sum(bulan_10) as bulan_10,
            sum(bulan_11) as bulan_11,
            sum(bulan_12) as bulan_12,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas k
        where tahun_anggaran=%d
            and type='pembiayaan-penerimaan'
            and active=1
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_pembiayaan = $wpdb->get_results($sql, ARRAY_A);

$body_pembiayaan = generate_body($rek_pembiayaan, true, $type, 'Penerimaan Pembiayaan');

if(!empty($input['id_skpd'])){
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            (
                SELECT 
                    sum(r.total) 
                FROM data_pembiayaan r 
                WHERE r.tahun_anggaran=k.tahun_anggaran 
                    AND r.active=1 
                    AND r.kode_akun=k.kode_akun
                    AND r.id_skpd=k.id_sub_skpd
                    AND type='pengeluaran'
            ) as pagu,
            kode_akun,
            nama_akun,
            sum(bulan_1) as bulan_1,
            sum(bulan_2) as bulan_2,
            sum(bulan_3) as bulan_3,
            sum(bulan_4) as bulan_4,
            sum(bulan_5) as bulan_5,
            sum(bulan_6) as bulan_6,
            sum(bulan_7) as bulan_7,
            sum(bulan_8) as bulan_8,
            sum(bulan_9) as bulan_9,
            sum(bulan_10) as bulan_10,
            sum(bulan_11) as bulan_11,
            sum(bulan_12) as bulan_12,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas k
        where tahun_anggaran=%d
            and type='pembiayaan-pengeluaran'
            and active=1
            and id_sub_skpd=%d
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
}else{
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            (
                SELECT 
                    sum(r.total) 
                FROM data_pembiayaan r 
                WHERE r.tahun_anggaran=k.tahun_anggaran 
                    AND r.active=1 
                    AND r.kode_akun=k.kode_akun
                    AND type='pengeluaran'
            ) as pagu,
            kode_akun,
            nama_akun,
            sum(bulan_1) as bulan_1,
            sum(bulan_2) as bulan_2,
            sum(bulan_3) as bulan_3,
            sum(bulan_4) as bulan_4,
            sum(bulan_5) as bulan_5,
            sum(bulan_6) as bulan_6,
            sum(bulan_7) as bulan_7,
            sum(bulan_8) as bulan_8,
            sum(bulan_9) as bulan_9,
            sum(bulan_10) as bulan_10,
            sum(bulan_11) as bulan_11,
            sum(bulan_12) as bulan_12,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas k
        where tahun_anggaran=%d
            and type='pembiayaan-pengeluaran'
            and active=1
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_pembiayaan = $wpdb->get_results($sql, ARRAY_A);

$body_pembiayaan .= generate_body($rek_pembiayaan, false, $type, 'Pengeluaran Pembiayaan');

$nama_skpd = "";
$options_skpd = array();
$unit = $wpdb->get_results("
    SELECT 
        nama_skpd, 
        id_skpd, 
        kode_skpd, 
        nipkepala 
    from data_unit 
    where active=1 
        and tahun_anggaran=".$input['tahun_anggaran'].' 
        and is_skpd=1 
    order by kode_skpd ASC
', ARRAY_A);
foreach ($unit as $kk => $vv) {
    $options_skpd[] = $vv;
    $subunit = $wpdb->get_results("
        SELECT 
            nama_skpd,
            id_skpd,
            kode_skpd,
            nipkepala 
        from data_unit 
        where active=1 
            and tahun_anggaran=".$input['tahun_anggaran']." 
            and is_skpd=0 
            and id_unit=".$vv["id_skpd"]." 
        order by kode_skpd ASC
    ", ARRAY_A);
    if($input['id_skpd'] == $vv['id_skpd']){
        $nama_skpd = '<br>'.$vv['kode_skpd'].' '.$vv['nama_skpd'];
    }
    foreach ($subunit as $kkk => $vvv) {
        if($input['id_skpd'] == $vvv['id_skpd']){
            $nama_skpd = '<br>'.$vvv['kode_skpd'].' '.$vvv['nama_skpd'];
        }
        $vvv['kode_skpd'] = '-- '.$vvv['kode_skpd'];
        $options_skpd[] = $vvv;
    }
}
?>
<style type="text/css">
    .realisasi_simda { display: none; }
</style>
<div id="cetak" title="Laporan APBD Per Triwulan <?php echo $input['tahun_anggaran']; ?>">
    <h4 style="text-align: center; font-size: 13px; margin: 10px auto; min-width: 450px; max-width: 550px; font-weight: bold; text-transform: uppercase;"><?php echo get_option('_crb_daerah'); ?> <br>RINGKASAN APBD YANG DIKLASIFIKASI MENURUT KELOMPOK DAN JENIS PENDAPATAN, BELANJA, DAN
PEMBIAYAAN<?php echo $nama_skpd; ?><br>TAHUN ANGGARAN <?php echo $input['tahun_anggaran']; ?><br>Per Triwulan</h4>
    <div style="width: 100%; height: 100vh; overflow: auto;">
        <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
            <thead>
                <tr>
                    <td class="atas kanan bawah kiri text_tengah text_blok" rowspan="2">Kode</td>
                    <td class="atas kanan bawah text_tengah text_blok" rowspan="2">Uraian</td>
                    <td class="atas kanan bawah text_tengah text_blok">JANUARI</td>
                    <td class="atas kanan bawah text_tengah text_blok">FEBRUARI</td>
                    <td class="atas kanan bawah text_tengah text_blok">MARET</td>
                    <td class="atas kanan bawah text_tengah text_blok">APRIL</td>
                    <td class="atas kanan bawah text_tengah text_blok">MEI</td>
                    <td class="atas kanan bawah text_tengah text_blok">JUNI</td>
                    <td class="atas kanan bawah text_tengah text_blok">JULI</td>
                    <td class="atas kanan bawah text_tengah text_blok">AGUSTUS</td>
                    <td class="atas kanan bawah text_tengah text_blok">SEPTEMBER</td>
                    <td class="atas kanan bawah text_tengah text_blok">OKTOBER</td>
                    <td class="atas kanan bawah text_tengah text_blok">NOVEMBER</td>
                    <td class="atas kanan bawah text_tengah text_blok">DESEMBER</td>
                    <td class="atas kanan bawah text_tengah text_blok">Total KAS</td>
                    <td class="atas kanan bawah text_tengah text_blok">Pagu</td>
                    <td class="atas kanan bawah text_tengah text_blok">Realisasi</td>
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
    </div>
</div>

<script type="text/javascript">
    function ubah_skpd(){
        var pilih_id_skpd = jQuery('#pilih_skpd').val();
        _url = changeUrl({ url: _url, key: 'id_skpd', value: pilih_id_skpd });
        window.open(_url);
        jQuery('#pilih_skpd').val(id_skpd);
    }
    function tampil_rekening(that){
        var id = jQuery(that).attr('id');
        if(id == 'rek_3'){
            jQuery('#rek_2').prop('checked', true);
        }else if(id == 'rek_4'){
            jQuery('#rek_2').prop('checked', true);
            jQuery('#rek_3').prop('checked', true);
        }else if(id == 'rek_5'){
            jQuery('#rek_2').prop('checked', true);
            jQuery('#rek_3').prop('checked', true);
            jQuery('#rek_4').prop('checked', true);
        }else if(id == 'rek_6'){
            jQuery('#rek_2').prop('checked', true);
            jQuery('#rek_3').prop('checked', true);
            jQuery('#rek_4').prop('checked', true);
            jQuery('#rek_5').prop('checked', true);
        }
        var rek2 = jQuery('#rek_2').is(':checked');
        var rek3 = jQuery('#rek_3').is(':checked');
        var rek4 = jQuery('#rek_4').is(':checked');
        var rek5 = jQuery('#rek_5').is(':checked');
        var rek6 = jQuery('#rek_6').is(':checked');
        jQuery('.rek_2').show();
        jQuery('.rek_3').show();
        jQuery('.rek_4').show();
        jQuery('.rek_5').show();
        jQuery('.rek_6').show();
        if(!rek2){
            jQuery('#rek_3').prop('checked', false);
            jQuery('#rek_4').prop('checked', false);
            jQuery('#rek_5').prop('checked', false);
            jQuery('#rek_6').prop('checked', false);
            jQuery('.rek_2').hide();
            jQuery('.rek_3').hide();
            jQuery('.rek_4').hide();
            jQuery('.rek_5').hide();
            jQuery('.rek_6').hide();
        }else if(!rek3){
            jQuery('#rek_4').prop('checked', false);
            jQuery('#rek_5').prop('checked', false);
            jQuery('#rek_6').prop('checked', false);
            jQuery('.rek_3').hide();
            jQuery('.rek_4').hide();
            jQuery('.rek_5').hide();
            jQuery('.rek_6').hide();
        }else if(!rek4){
            jQuery('#rek_5').prop('checked', false);
            jQuery('#rek_6').prop('checked', false);
            jQuery('.rek_4').hide();
            jQuery('.rek_5').hide();
            jQuery('.rek_6').hide();
        }else if(!rek5){
            jQuery('#rek_6').prop('checked', false);
            jQuery('.rek_5').hide();
            jQuery('.rek_6').hide();
        }else if(!rek6){
            jQuery('.rek_6').hide();
        }
    }

    var list_skpd = <?php echo json_encode($options_skpd); ?>;
    run_download_excel();
    window._url = window.location.href;
    var url = new URL(_url);
    _url = changeUrl({ url: _url, key: 'key', value: '<?php echo $this->gen_key(); ?>' });
    window.id_skpd = url.searchParams.get("id_skpd");
    var extend_action = '<div style="margin-top: 15px">';
    var options = '<option value="">Semua SKPD</option>';
    list_skpd.map(function(b, i){
        var selected = "";
        if(id_skpd && id_skpd == b.id_skpd){
            selected = "selected";
        }
        options += '<option '+selected+' value="'+b.id_skpd+'">'+b.kode_skpd+' '+b.nama_skpd+'</option>';
    });
    extend_action += '<select id="pilih_skpd" onchange="ubah_skpd();" style="width:500px; margin-left:25px;">'+options+'</select>';
    extend_action += '<h4 style="margin-top: 10px;">Tampilkan Baris Rekening</h4>';
    extend_action += '<label><input type="checkbox" onclick="tampil_rekening(this);" checked id="rek_2"> Rekening 2</label>';
    extend_action += '<label style="margin-left:25px;"><input type="checkbox" onclick="tampil_rekening(this);" checked id="rek_3"> Rekening 3</label>';
    extend_action += '<label style="margin-left:25px;"><input type="checkbox" onclick="tampil_rekening(this);" checked id="rek_4"> Rekening 4</label>';
    extend_action += '<label style="margin-left:25px;"><input type="checkbox" onclick="tampil_rekening(this);" checked id="rek_5"> Rekening 5</label>';
    extend_action += '<label style="margin-left:25px;"><input type="checkbox" onclick="tampil_rekening(this);" checked id="rek_6"> Rekening 6</label>';
    extend_action += '</div>';
    jQuery('#action-sipd').append(extend_action);
</script>