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
    global $pendapatan;
    global $belanja;
    global $pembiayaan_penerimaan;
    global $pembiayaan_pengeluaran;
    $data_all = array(
        'data' => array(),
        'realisasi' => 0,
        'total_1' => 0,
        'total_2' => 0,
        'total_3' => 0,
        'total_4' => 0,
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
                'total_1' => 0,
                'total_2' => 0,
                'total_3' => 0,
                'total_4' => 0,
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
                'total_1' => 0,
                'total_2' => 0,
                'total_3' => 0,
                'total_4' => 0,
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
                'total_1' => 0,
                'total_2' => 0,
                'total_3' => 0,
                'total_4' => 0,
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
                'total_1' => 0,
                'total_2' => 0,
                'total_3' => 0,
                'total_4' => 0,
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
                'total_1' => 0,
                'total_2' => 0,
                'total_3' => 0,
                'total_4' => 0,
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
                'total_1' => 0,
                'total_2' => 0,
                'total_3' => 0,
                'total_4' => 0,
                'total' => 0
            );
        }
        if(!isset($v['total_1'])){
            $v['total_1'] = 0;
        }
        if(!isset($v['total_2'])){
            $v['total_2'] = 0;
        }
        if(!isset($v['total_3'])){
            $v['total_3'] = 0;
        }
        if(!isset($v['total_4'])){
            $v['total_4'] = 0;
        }
        if(!isset($v['total'])){
            $v['total'] = 0;
        }
        $data_all['total_1'] += $v['total_1'];
        $data_all['data'][$kode_akun]['total_1'] += $v['total_1'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['total_1'] += $v['total_1'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['total_1'] += $v['total_1'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['total_1'] += $v['total_1'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['total_1'] += $v['total_1'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['total_1'] += $v['total_1'];
        $data_all['total_2'] += $v['total_2'];
        $data_all['data'][$kode_akun]['total_2'] += $v['total_2'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['total_2'] += $v['total_2'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['total_2'] += $v['total_2'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['total_2'] += $v['total_2'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['total_2'] += $v['total_2'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['total_2'] += $v['total_2'];
        $data_all['total_3'] += $v['total_3'];
        $data_all['data'][$kode_akun]['total_3'] += $v['total_3'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['total_3'] += $v['total_3'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['total_3'] += $v['total_3'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['total_3'] += $v['total_3'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['total_3'] += $v['total_3'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['total_3'] += $v['total_3'];
        $data_all['total_4'] += $v['total_4'];
        $data_all['data'][$kode_akun]['total_4'] += $v['total_4'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['total_4'] += $v['total_4'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['total_4'] += $v['total_4'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['total_4'] += $v['total_4'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['total_4'] += $v['total_4'];
        $data_all['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['total_4'] += $v['total_4'];
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
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['total_1'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['total_2'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['total_3'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['total_4'])."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['total'])."</td>
        </tr>";
        foreach ($v['data'] as $kk => $vv) {
            $body_html .= "
            <tr class='rek_2'>
                <td class='kiri kanan bawah text_blok'>".$kk."</td>
                <td class='kanan bawah text_blok'>".$vv['nama']."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['total_1'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['total_2'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['total_3'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['total_4'])."</td>
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['total'])."</td>
            </tr>";
            foreach ($vv['data'] as $kkk => $vvv) {
                $body_html .= "
                <tr class='rek_3'>
                    <td class='kiri kanan bawah'>".$kkk."</td>
                    <td class='kanan bawah'>".$vvv['nama']."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['total_1'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['total_2'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['total_3'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['total_4'])."</td>
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['total'])."</td>
                </tr>";
                foreach ($vvv['data'] as $kkkk => $vvvv) {
                    $body_html .= "
                    <tr class='rek_4'>
                        <td class='kiri kanan bawah'>".$kkkk."</td>
                        <td class='kanan bawah'>".$vvvv['nama']."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['total_1'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['total_2'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['total_3'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['total_4'])."</td>
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['total'])."</td>
                    </tr>";
                    foreach ($vvvv['data'] as $kkkkk => $vvvvv) {
                        $body_html .= "
                        <tr class='rek_5'>
                            <td class='kiri kanan bawah'>".$kkkkk."</td>
                            <td class='kanan bawah'>".$vvvvv['nama']."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['total_1'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['total_2'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['total_3'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['total_4'])."</td>
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['total'])."</td>
                        </tr>";
                        foreach ($vvvvv['data'] as $kkkkkk => $vvvvvv) {
                            $body_html .= "
                            <tr class='rek_6'>
                                <td class='kiri kanan bawah'>".$kkkkkk."</td>
                                <td class='kanan bawah'>".$vvvvvv['nama']."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['total_1'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['total_2'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['total_3'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['total_4'])."</td>
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['total'])."</td>
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
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['total_1'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['total_2'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['total_3'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['total_4'])."</td>
        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($data_all['total'])."</td>
    </tr>";
    if($nama_rekening == 'Pendapatan'){
        $pendapatan_1 = $data_all['total_1'];
        $pendapatan_2 = $data_all['total_2'];
        $pendapatan_3 = $data_all['total_3'];
        $pendapatan_4 = $data_all['total_4'];
        $pendapatan = $data_all['total'];
    }else if($nama_rekening == 'Belanja'){
        $belanja_1 = $data_all['total_1'];
        $belanja_2 = $data_all['total_2'];
        $belanja_3 = $data_all['total_3'];
        $belanja_4 = $data_all['total_4'];
        $belanja = $data_all['total'];
        $body_html .= "
        <tr>
            <td class='kiri kanan bawah'></td>
            <td class='kanan bawah text_kanan text_blok'>Total Surplus/(Defisit)</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pendapatan_1-$belanja_1)."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2)-($belanja_1+$belanja_2))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3)-($belanja_1+$belanja_2+$belanja_3))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pendapatan_1+$pendapatan_2+$pendapatan_3+$pendapatan_4)-($belanja_1+$belanja_2+$belanja_3+$belanja_4))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pendapatan-$belanja)."</td>
        </tr>";
    }else if($nama_rekening == 'Penerimaan Pembiayaan'){
        $pembiayaan_penerimaan_1 = $data_all['total_1'];
        $pembiayaan_penerimaan_2 = $data_all['total_2'];
        $pembiayaan_penerimaan_3 = $data_all['total_3'];
        $pembiayaan_penerimaan_4 = $data_all['total_4'];
        $pembiayaan_penerimaan = $data_all['total'];
    }else if($nama_rekening == 'Pengeluaran Pembiayaan'){
        $pembiayaan_pengeluaran_1 = $data_all['total_1'];
        $pembiayaan_pengeluaran_2 = $data_all['total_2'];
        $pembiayaan_pengeluaran_3 = $data_all['total_3'];
        $pembiayaan_pengeluaran_4 = $data_all['total_4'];
        $pembiayaan_pengeluaran = $data_all['total'];
        $body_html .= "
        <tr>
            <td class='kiri kanan bawah'></td>
            <td class='kanan bawah text_kanan text_blok'>Total Surplus/(Defisit)</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pembiayaan_penerimaan_1-$pembiayaan_pengeluaran_1)."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4)-($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($pembiayaan_penerimaan-$pembiayaan_pengeluaran)."</td>
        </tr>
        <tr>
            <td class='kiri kanan bawah text_blok'>6.3</td>
            <td class='kanan bawah text_kiri text_blok'>Sisa Lebih Pembiayaan Anggaran Daerah Tahun Berkenaan (SILPA)</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($belanja_1 - ($pembiayaan_penerimaan_1 - $pembiayaan_pengeluaran_1))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus(($belanja_1+$belanja_2+$belanja_3+$belanja_4) - (($pembiayaan_penerimaan_1+$pembiayaan_penerimaan_2+$pembiayaan_penerimaan_3+$pembiayaan_penerimaan_4) - ($pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_2+$pembiayaan_pengeluaran_3+$pembiayaan_pengeluaran_4)))."</td>
            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($belanja - ($pembiayaan_penerimaan - $pembiayaan_pengeluaran))."</td>
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
$GLOBALS['pendapatan'] = 0;
$GLOBALS['belanja'] = 0;
$GLOBALS['pembiayaan_penerimaan'] = 0;
$GLOBALS['pembiayaan_pengeluaran'] = 0;

if(!empty($_GET) && !empty($_GET['id_skpd'])){
    $input['id_skpd'] = $_GET['id_skpd'];
}
$type = false;

if(!empty($input['id_skpd'])){
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            kode_akun,
            nama_akun,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3)) as total_1,
            (sum(bulan_4) + sum(bulan_5) + sum(bulan_6)) as total_2,
            (sum(bulan_7) + sum(bulan_8) + sum(bulan_9)) as total_3,
            (sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total_4,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas
        where tahun_anggaran=%d
            and active=1
            and type='pendapatan'
            and id_sub_skpd=%d
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
}else{
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            kode_akun,
            nama_akun,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3)) as total_1,
            (sum(bulan_4) + sum(bulan_5) + sum(bulan_6)) as total_2,
            (sum(bulan_7) + sum(bulan_8) + sum(bulan_9)) as total_3,
            (sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total_4,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas
        where tahun_anggaran=%d
            and active=1
            and type='pendapatan'
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_pendapatan = $wpdb->get_results($sql, ARRAY_A);

$body_pendapatan = generate_body($rek_pendapatan, true, $type, 'Pendapatan');

if(!empty($input['id_skpd'])){
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            kode_akun,
            nama_akun,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3)) as total_1,
            (sum(bulan_4) + sum(bulan_5) + sum(bulan_6)) as total_2,
            (sum(bulan_7) + sum(bulan_8) + sum(bulan_9)) as total_3,
            (sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total_4,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas
        where tahun_anggaran=%d
            and active=1
            and id_sub_skpd=%d
            and type='belanja'
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
}else{
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            kode_akun,
            nama_akun,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3)) as total_1,
            (sum(bulan_4) + sum(bulan_5) + sum(bulan_6)) as total_2,
            (sum(bulan_7) + sum(bulan_8) + sum(bulan_9)) as total_3,
            (sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total_4,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas
        where tahun_anggaran=%d
            and active=1
            and type='belanja'
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_belanja = $wpdb->get_results($sql, ARRAY_A);

$body_belanja = generate_body($rek_belanja, true, $type, 'Belanja');

if(!empty($input['id_skpd'])){
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            kode_akun,
            nama_akun,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3)) as total_1,
            (sum(bulan_4) + sum(bulan_5) + sum(bulan_6)) as total_2,
            (sum(bulan_7) + sum(bulan_8) + sum(bulan_9)) as total_3,
            (sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total_4,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas
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
            kode_akun,
            nama_akun,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3)) as total_1,
            (sum(bulan_4) + sum(bulan_5) + sum(bulan_6)) as total_2,
            (sum(bulan_7) + sum(bulan_8) + sum(bulan_9)) as total_3,
            (sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total_4,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas
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
            kode_akun,
            nama_akun,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3)) as total_1,
            (sum(bulan_4) + sum(bulan_5) + sum(bulan_6)) as total_2,
            (sum(bulan_7) + sum(bulan_8) + sum(bulan_9)) as total_3,
            (sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total_4,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas
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
            kode_akun,
            nama_akun,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3)) as total_1,
            (sum(bulan_4) + sum(bulan_5) + sum(bulan_6)) as total_2,
            (sum(bulan_7) + sum(bulan_8) + sum(bulan_9)) as total_3,
            (sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total_4,
            (sum(bulan_1) + sum(bulan_2) + sum(bulan_3) + sum(bulan_4) + sum(bulan_5) + sum(bulan_6) + sum(bulan_7) + sum(bulan_8)  + sum(bulan_9)  + sum(bulan_10) + sum(bulan_11) + sum(bulan_12)) as total
        from data_anggaran_kas
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
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok" rowspan="2">Kode</td>
                <td class="atas kanan bawah text_tengah text_blok" rowspan="2">Uraian</td>
                <td class="atas kanan bawah text_tengah text_blok">Triwulan I</td>
                <td class="atas kanan bawah text_tengah text_blok">Triwulan II</td>
                <td class="atas kanan bawah text_tengah text_blok">Triwulan III</td>
                <td class="atas kanan bawah text_tengah text_blok">Triwulan IV</td>
                <td class="atas kanan bawah text_tengah text_blok">Total</td>
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