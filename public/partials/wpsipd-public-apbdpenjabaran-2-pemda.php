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

function generate_body($rek_pendapatan, $baris_kosong=false, $type='murni', $nama_rekening, $dari_simda=0){
    global $wpdb;
    global $pendapatan_murni;
    global $pendapatan_pergeseran;
    global $belanja_murni;
    global $belanja_pergeseran;
    global $pembiayaan_penerimaan_murni;
    global $pembiayaan_penerimaan_pergeseran;
    global $pembiayaan_pengeluaran_murni;
    global $pembiayaan_pengeluaran_pergeseran;
    $data_pendapatan = array(
        'data' => array(),
        'realisasi' => 0,
        'realisasi_sipd' => 0,
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
                'realisasi' => 0,
                'realisasi_sipd' => 0,
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
                'realisasi' => 0,
                'realisasi_sipd' => 0,
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
                'realisasi' => 0,
                'realisasi_sipd' => 0,
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
                'realisasi' => 0,
                'realisasi_sipd' => 0,
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
                'realisasi' => 0,
                'realisasi_sipd' => 0,
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
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'nama' => $nama_akun[0]['nama_akun'],
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(!isset($v['total'])){
            $v['total'] = 0;
        }
        $data_pendapatan['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['total'] += $v['total'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['total'] += $v['total'];
        $data_pendapatan['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['realisasi'] += $v['realisasi'];
        $data_pendapatan['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$kode_akun]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['realisasi_sipd'] += $v['realisasi_sipd'];

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

    if($baris_kosong){
        $collspan = '4';
        if($type == 'pergeseran'){
            $collspan = '6';
        }
        $nama_rekening_baru = $nama_rekening;
        if($nama_rekening == 'Penerimaan Pembiayaan'){
            $nama_rekening_baru = 'Pembiayaan';
        }
        $body_pendapatan .= "
        <tr>
            <td class='kiri kanan bawah text_tengah text_blok' colspan='".$collspan."'>".$nama_rekening_baru." Daerah</td>
            <td class='kanan bawah realisasi_simda'></td>
            <td class='kanan bawah realisasi_sipd'></td>
        </tr>";
    }

    $total = 0;
    foreach ($data_pendapatan['data'] as $k => $v) {
        $murni = '';
        $selisih = '';
        if($type == 'pergeseran'){
            $murni = "<td class='kanan bawah text_kanan text_blok'></td>";
            $selisih = "<td class='kanan bawah text_kanan text_blok'></td>";
        }
        $body_pendapatan .= "
        <tr class='rek_1'>
            <td class='kiri kanan bawah text_blok'>".$k."</td>
            <td class='kanan bawah text_blok'>".$v['nama']."</td>
            ".$murni."
            <td class='kanan bawah text_kanan text_blok'></td>
            ".$selisih."
            <td class='kanan bawah'></td>
            <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
            <td class='kanan bawah text_kanan text_blok realisasi_sipd'></td>
        </tr>";
        foreach ($v['data'] as $kk => $vv) {
            $murni = '';
            $selisih = '';
            if($type == 'pergeseran'){
                $murni = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['totalmurni'])."</td>";
                $selisih = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus(($vv['total']-$vv['totalmurni']))."</td>";
            }
            $body_pendapatan .= "
            <tr class='rek_2'>
                <td class='kiri kanan bawah text_blok'>".$kk."</td>
                <td class='kanan bawah text_blok'>".$vv['nama']."</td>
                ".$murni."
                <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['total'])."</td>
                ".$selisih."
                <td class='kanan bawah'></td>
                <td class='kanan bawah text_kanan text_blok realisasi_simda'>".ubah_minus($vv['realisasi'])."</td>
                <td class='kanan bawah text_kanan text_blok realisasi_sipd'>".ubah_minus($vv['realisasi_sipd'])."</td>
            </tr>";
            foreach ($vv['data'] as $kkk => $vvv) {
                $murni = '';
                $selisih = '';
                if($type == 'pergeseran'){
                    $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvv['totalmurni'])."</td>";
                    $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvv['total']-$vvv['totalmurni']))."</td>";
                }
                $body_pendapatan .= "
                <tr class='rek_3'>
                    <td class='kiri kanan bawah'>".$kkk."</td>
                    <td class='kanan bawah'>".$vvv['nama']."</td>
                    ".$murni."
                    <td class='kanan bawah text_kanan'>".ubah_minus($vvv['total'])."</td>
                    ".$selisih."
                    <td class='kanan bawah'></td>
                    <td class='kanan bawah text_kanan realisasi_simda'>".ubah_minus($vvv['realisasi'])."</td>
                    <td class='kanan bawah text_kanan realisasi_sipd'>".ubah_minus($vvv['realisasi_sipd'])."</td>
                </tr>";
                foreach ($vvv['data'] as $kkkk => $vvvv) {
                    $murni = '';
                    $selisih = '';
                    if($type == 'pergeseran'){
                        $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvvv['totalmurni'])."</td>";
                        $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvvv['total']-$vvvv['totalmurni']))."</td>";
                    }
                    $body_pendapatan .= "
                    <tr class='rek_4'>
                        <td class='kiri kanan bawah'>".$kkkk."</td>
                        <td class='kanan bawah'>".$vvvv['nama']."</td>
                        ".$murni."
                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['total'])."</td>
                        ".$selisih."
                        <td class='kanan bawah'></td>
                        <td class='kanan bawah text_kanan realisasi_simda'>".ubah_minus($vvvv['realisasi'])."</td>
                        <td class='kanan bawah text_kanan realisasi_sipd'>".ubah_minus($vvvv['realisasi_sipd'])."</td>
                    </tr>";
                    foreach ($vvvv['data'] as $kkkkk => $vvvvv) {
                        $murni = '';
                        $selisih = '';
                        if($type == 'pergeseran'){
                            $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['totalmurni'])."</td>";
                            $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvvvv['total']-$vvvvv['totalmurni']))."</td>";
                        }
                        $body_pendapatan .= "
                        <tr class='rek_5'>
                            <td class='kiri kanan bawah'>".$kkkkk."</td>
                            <td class='kanan bawah'>".$vvvvv['nama']."</td>
                            ".$murni."
                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['total'])."</td>
                            ".$selisih."
                            <td class='kanan bawah'></td>
                            <td class='kanan bawah text_kanan realisasi_simda'>".ubah_minus($vvvvv['realisasi'])."</td>
                            <td class='kanan bawah text_kanan realisasi_sipd'>".ubah_minus($vvvvv['realisasi_sipd'])."</td>
                        </tr>";
                        foreach ($vvvvv['data'] as $kkkkkk => $vvvvvv) {
                            $murni = '';
                            $selisih = '';
                            if($type == 'pergeseran'){
                                $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['totalmurni'])."</td>";
                                $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvvvvv['total']-$vvvvvv['totalmurni']))."</td>";
                            }
                            $body_pendapatan .= "
                            <tr class='rek_6'>
                                <td class='kiri kanan bawah'>".$kkkkkk."</td>
                                <td class='kanan bawah'>".$vvvvvv['nama']."</td>
                                ".$murni."
                                <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['total'])."</td>
                                ".$selisih."
                                <td class='kanan bawah'></td>
                                <td class='kanan bawah text_kanan realisasi_simda'>".ubah_minus($vvvvvv['realisasi'])."</td>
                                <td class='kanan bawah text_kanan realisasi_sipd'>".ubah_minus($vvvvvv['realisasi_sipd'])."</td>
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
        <td class='kanan bawah'></td>
        <td class='kanan bawah text_kanan text_blok realisasi_simda'>".ubah_minus($data_pendapatan['realisasi'])."</td>
        <td class='kanan bawah text_kanan text_blok realisasi_sipd'>".ubah_minus($data_pendapatan['realisasi_sipd'])."</td>
    </tr>";
    return $body_pendapatan;
}

function generate_body_belanja($rek_pendapatan, $baris_kosong=false, $type='murni', $nama_rekening, $dari_simda=0, $input){
    global $wpdb;
    global $pendapatan_murni;
    global $pendapatan_pergeseran;
    global $belanja_murni;
    global $belanja_pergeseran;
    global $pembiayaan_penerimaan_murni;
    global $pembiayaan_penerimaan_pergeseran;
    global $pembiayaan_pengeluaran_murni;
    global $pembiayaan_pengeluaran_pergeseran;
    $data_pendapatan = array(
        'data' => array(),
        'realisasi' => 0,
        'realisasi_sipd' => 0,
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
        if(empty($v['nama_bidang_urusan'])){
            $kode_urusan = array('0', '00');
        }else{
            $kode_urusan = explode('.', $v['kode_bidang_urusan']);
        }
        if(empty($data_pendapatan['data'][$v['kode_urusan']])){
            $data_pendapatan['data'][$v['kode_urusan']] = array(
                'nama' => $v['nama_urusan'],
                'kode' => $v['kode_urusan'],
                'data' => array(),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']])){
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']] = array(
                'nama' => $v['nama_bidang_urusan'],
                'kode' => $v['kode_bidang_urusan'],
                'data' => array(),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']])){
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']] = array(
                'nama' => $v['nama_skpd'],
                'kode' => $v['kode_skpd'],
                'data' => array(),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']])){
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']] = array(
                'nama' => $v['nama_sub_skpd'],
                'kode' => $v['kode_sub_skpd'],
                'data' => array(),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']])){
            $ind_db = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    hasilteks
                from data_keg_indikator_hasil 
                where kode_sbl=%s 
                    AND tahun_anggaran=%d
                    AND active=1
            ", $v['kode_sbl'], $input['tahun_anggaran']), ARRAY_A);
            $ind_text = array();
            foreach($ind_db as $ind){
                $ind_text[] = $ind['hasilteks'];
            }
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']] = array(
                'nama' => $v['nama_program'],
                'kode' => $v['kode_program'],
                'data' => array(),
                'indikator' => implode('<br>', $ind_text),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']])){
            $ind_db = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    capaianteks 
                from data_capaian_prog_sub_keg 
                where kode_sbl=%s 
                    AND tahun_anggaran=%d
                    AND active=1
            ", $v['kode_sbl'], $input['tahun_anggaran']), ARRAY_A);
            $ind_text = array();
            foreach($ind_db as $ind){
                $ind_text[] = $ind['capaianteks'];
            }
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']] = array(
                'nama' => $v['nama_giat'],
                'kode' => $v['kode_giat'],
                'data' => array(),
                'indikator' => implode('<br>', $ind_text),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']])){
            $ind_db = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    outputteks 
                from data_sub_keg_indikator 
                where kode_sbl=%s 
                    AND tahun_anggaran=%d
                    AND active=1
            ", $v['kode_sbl'], $input['tahun_anggaran']), ARRAY_A);
            $ind_text = array();
            foreach($ind_db as $ind){
                $ind_text[] = $ind['outputteks'];
            }
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']] = array(
                'nama' => $v['nama_sub_giat'],
                'kode' => $v['kode_sub_giat'],
                'indikator' => implode('<br>', $ind_text),
                'data' => array(),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun])){
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun] = array(
                'nama' => $v['nama_akun'],
                'kode' => $v['kode_akun'],
                'data' => array(),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun1 = $kode_akun.'.'.$rek[1];
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1])){
            $nama_akun = $wpdb->get_var($wpdb->prepare("
                SELECT 
                    nama_akun 
                from data_akun 
                where kode_akun=%s
                    AND tahun_anggaran=%d
                    AND active=1
                ",$kode_akun1, $input['tahun_anggaran']));
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1] = array(
                'nama' => $nama_akun,
                'kode' => $kode_akun1,
                'data' => array(),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun2 = $kode_akun1.'.'.$rek[2];
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2])){
            $nama_akun = $wpdb->get_var($wpdb->prepare("
                SELECT 
                    nama_akun 
                from data_akun 
                where kode_akun=%s
                    AND tahun_anggaran=%d
                    AND active=1
                ",$kode_akun2, $input['tahun_anggaran']));
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2] = array(
                'nama' => $nama_akun,
                'kode' => $kode_akun2,
                'data' => array(),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun3 = $kode_akun2.'.'.$rek[3];
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3])){
            $nama_akun = $wpdb->get_var($wpdb->prepare("
                SELECT 
                    nama_akun 
                from data_akun 
                where kode_akun=%s
                    AND tahun_anggaran=%d
                    AND active=1
                ",$kode_akun3, $input['tahun_anggaran']));
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3] = array(
                'nama' => $nama_akun,
                'kode' => $kode_akun3,
                'data' => array(),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun4 = $kode_akun3.'.'.$rek[4];
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4])){
            $nama_akun = $wpdb->get_var($wpdb->prepare("
                SELECT 
                    nama_akun 
                from data_akun 
                where kode_akun=%s
                    AND tahun_anggaran=%d
                    AND active=1
                ",$kode_akun4, $input['tahun_anggaran']));
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4] = array(
                'nama' => $nama_akun,
                'kode' => $kode_akun4,
                'data' => array(),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        $kode_akun5 = $kode_akun4.'.'.$rek[5];
        if(empty($data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5])){
            $nama_akun = $wpdb->get_var($wpdb->prepare("
                SELECT 
                    nama_akun 
                from data_akun 
                where kode_akun=%s
                    AND tahun_anggaran=%d
                    AND active=1
                ",$kode_akun5, $input['tahun_anggaran']));
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5] = array(
                'nama' => $nama_akun,
                'kode' => $kode_akun5,
                'data' => array(),
                'realisasi' => 0,
                'realisasi_sipd' => 0,
                'total' => 0,
                'totalmurni' => 0
            );
        }
        if(!isset($v['total'])){
            $v['total'] = 0;
        }
        $data_pendapatan['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['total'] += $v['total'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['total'] += $v['total'];

        $data_pendapatan['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['realisasi'] += $v['realisasi'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['realisasi'] += $v['realisasi'];

        $data_pendapatan['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['realisasi_sipd'] += $v['realisasi_sipd'];
        $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['realisasi_sipd'] += $v['realisasi_sipd'];

        if(!empty($v['totalmurni'])){
            $data_pendapatan['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['totalmurni'] += $v['totalmurni'];
            $data_pendapatan['data'][$v['kode_urusan']]['data'][$v['kode_bidang_urusan']]['data'][$v['kode_skpd']]['data'][$v['kode_sub_skpd']]['data'][$v['no_program']]['data'][$v['no_giat']]['data'][$v['no_sub_giat']]['data'][$kode_akun]['data'][$kode_akun1]['data'][$kode_akun2]['data'][$kode_akun3]['data'][$kode_akun4]['data'][$kode_akun5]['totalmurni'] += $v['totalmurni'];
        }
    }
    $body_pendapatan = '';

    if($baris_kosong){
        $collspan = '4';
        if($type == 'pergeseran'){
            $collspan = '6';
        }
        $body_pendapatan .= "
        <tr>
            <td class='kiri kanan bawah text_tengah text_blok' colspan='".$collspan."'>".$nama_rekening." Daerah</td>
            <td class='kanan bawah realisasi_simda'></td>
            <td class='kanan bawah realisasi_sipd'></td>
        </tr>";
    }

    $total = 0;
    // urusan
    foreach ($data_pendapatan['data'] as $k_urusan => $urusan) {
        $collspan = '3';
        if($type == 'pergeseran'){
            $collspan = "5";
        }
        $body_pendapatan .= "
        <tr class='urusan'>
            <td class='kiri kanan bawah text_blok'>Urusan Pemerintahan</td>
            <td class='kanan bawah text_blok' colspan='".$collspan."'>: ".$urusan['kode']." ".$urusan['nama']."</td>
            <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
            <td class='kanan bawah text_kanan text_blok realisasi_sipd'></td>
        </tr>";
        // bidang
        foreach ($urusan['data'] as $k_bidang => $bidang) {
            $body_pendapatan .= "
            <tr class='bidang'>
                <td class='kiri kanan bawah text_blok'>Bidang Urusan</td>
                <td class='kanan bawah text_blok' colspan='".$collspan."'>: ".$bidang['kode']." ".$bidang['nama']."</td>
                <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
                <td class='kanan bawah text_kanan text_blok realisasi_sipd'></td>
            </tr>";
            // skpd
            foreach ($bidang['data'] as $k_skpd => $skpd) {
                $body_pendapatan .= "
                <tr class='skpd'>
                    <td class='kiri kanan bawah text_blok'>Organisasi</td>
                    <td class='kanan bawah text_blok' colspan='".$collspan."'>: ".$skpd['kode']." ".$skpd['nama']."</td>
                    <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
                    <td class='kanan bawah text_kanan text_blok realisasi_sipd'></td>
                </tr>";
                // sub unit
                foreach ($skpd['data'] as $k_sub_unit => $sub_unit) {
                    $body_pendapatan .= "
                    <tr class='sub_unit'>
                        <td class='kiri kanan bawah text_blok'>Unit Organisasi</td>
                        <td class='kanan bawah text_blok' colspan='".$collspan."'>: ".$sub_unit['kode']." ".$sub_unit['nama']."</td>
                        <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
                        <td class='kanan bawah text_kanan text_blok realisasi_sipd'></td>
                    </tr>";
                    // program
                    foreach ($sub_unit['data'] as $k_program => $program) {
                        $body_pendapatan .= "
                        <tr class='program'>
                            <td class='kiri kanan bawah text_blok'>Program</td>
                            <td class='kanan bawah text_blok' colspan='".$collspan."'>: ".$program['kode']." ".$program['nama']."</td>
                            <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
                            <td class='kanan bawah text_kanan text_blok realisasi_sipd'></td>
                        </tr>
                        <tr class='indikator_program'>
                            <td class='kiri kanan bawah text_blok'>Indikator Hasil</td>
                            <td class='kanan bawah text_blok' colspan='".$collspan."'>: ".$program['indikator']."</td>
                            <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
                            <td class='kanan bawah text_kanan text_blok realisasi_sipd'></td>
                        </tr>";
                        // kegiatan
                        foreach ($program['data'] as $k_kegiatan => $kegiatan) {
                            $body_pendapatan .= "
                            <tr class='kegiatan'>
                                <td class='kiri kanan bawah text_blok'>Kegiatan</td>
                                <td class='kanan bawah text_blok' colspan='".$collspan."'>: ".$kegiatan['kode']." ".$kegiatan['nama']."</td>
                                <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
                                <td class='kanan bawah text_kanan text_blok realisasi_sipd'></td>
                            </tr>
                            <tr class='kegiatan'>
                                <td class='kiri kanan bawah text_blok'>Indikator Keluaran</td>
                                <td class='kanan bawah text_blok' colspan='".$collspan."'>: ".$kegiatan['indikator']."</td>
                                <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
                                <td class='kanan bawah text_kanan text_blok realisasi_sipd'></td>
                            </tr>";
                            // sub kegiatan
                            foreach ($kegiatan['data'] as $k_sub_keg => $sub_keg) {
                                $body_pendapatan .= "
                                <tr class='sub_keg'>
                                    <td class='kiri kanan bawah text_blok'>Sub Kegiatan</td>
                                    <td class='kanan bawah text_blok' colspan='".$collspan."'>: ".$sub_keg['kode']." ".$sub_keg['nama']."</td>
                                    <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
                                    <td class='kanan bawah text_kanan text_blok realisasi_sipd'></td>
                                </tr>
                                <tr class='sub_keg'>
                                    <td class='kiri kanan bawah text_blok'>Indikator Keluaran</td>
                                    <td class='kanan bawah text_blok' colspan='".$collspan."'>: ".$sub_keg['indikator']."</td>
                                    <td class='kanan bawah text_kanan text_blok realisasi_simda'></td>
                                    <td class='kanan bawah text_kanan text_blok realisasi_sipd'></td>
                                </tr>";
                                foreach ($sub_keg['data'] as $k => $v) {
                                    $murni = '';
                                    $selisih = '';
                                    if($type == 'pergeseran'){
                                        $murni = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['totalmurni'])."</td>";
                                        $selisih = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus(($v['total']-$v['totalmurni']))."</td>";
                                    }
                                    $body_pendapatan .= "
                                    <tr class='rek_1'>
                                        <td class='kiri kanan bawah text_blok'>".$k."</td>
                                        <td class='kanan bawah text_blok'>".$v['nama']."</td>
                                        ".$murni."
                                        <td class='kanan bawah text_kanan text_blok'>".ubah_minus($v['total'])."</td>
                                        ".$selisih."
                                        <td class='kanan bawah'></td>
                                        <td class='kanan bawah text_kanan text_blok realisasi_simda'>".ubah_minus($v['realisasi'])."</td>
                                        <td class='kanan bawah text_kanan text_blok realisasi_sipd'>".ubah_minus($v['realisasi_sipd'])."</td>
                                    </tr>";
                                    foreach ($v['data'] as $kk => $vv) {
                                        $murni = '';
                                        $selisih = '';
                                        if($type == 'pergeseran'){
                                            $murni = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['totalmurni'])."</td>";
                                            $selisih = "<td class='kanan bawah text_kanan text_blok'>".ubah_minus(($vv['total']-$vv['totalmurni']))."</td>";
                                        }
                                        $body_pendapatan .= "
                                        <tr class='rek_2'>
                                            <td class='kiri kanan bawah text_blok'>".$kk."</td>
                                            <td class='kanan bawah text_blok'>".$vv['nama']."</td>
                                            ".$murni."
                                            <td class='kanan bawah text_kanan text_blok'>".ubah_minus($vv['total'])."</td>
                                            ".$selisih."
                                            <td class='kanan bawah'></td>
                                            <td class='kanan bawah text_kanan text_blok realisasi_simda'>".ubah_minus($vv['realisasi'])."</td>
                                            <td class='kanan bawah text_kanan text_blok realisasi_sipd'>".ubah_minus($vv['realisasi_sipd'])."</td>
                                        </tr>";
                                        foreach ($vv['data'] as $kkk => $vvv) {
                                            $murni = '';
                                            $selisih = '';
                                            if($type == 'pergeseran'){
                                                $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvv['totalmurni'])."</td>";
                                                $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvv['total']-$vvv['totalmurni']))."</td>";
                                            }
                                            $body_pendapatan .= "
                                            <tr class='rek_3'>
                                                <td class='kiri kanan bawah'>".$kkk."</td>
                                                <td class='kanan bawah'>".$vvv['nama']."</td>
                                                ".$murni."
                                                <td class='kanan bawah text_kanan'>".ubah_minus($vvv['total'])."</td>
                                                ".$selisih."
                                                <td class='kanan bawah'></td>
                                                <td class='kanan bawah text_kanan realisasi_simda'>".ubah_minus($vvv['realisasi'])."</td>
                                                <td class='kanan bawah text_kanan realisasi_sipd'>".ubah_minus($vvv['realisasi_sipd'])."</td>
                                            </tr>";
                                            foreach ($vvv['data'] as $kkkk => $vvvv) {
                                                $murni = '';
                                                $selisih = '';
                                                if($type == 'pergeseran'){
                                                    $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvvv['totalmurni'])."</td>";
                                                    $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvvv['total']-$vvvv['totalmurni']))."</td>";
                                                }
                                                $body_pendapatan .= "
                                                <tr class='rek_4'>
                                                    <td class='kiri kanan bawah'>".$kkkk."</td>
                                                    <td class='kanan bawah'>".$vvvv['nama']."</td>
                                                    ".$murni."
                                                    <td class='kanan bawah text_kanan'>".ubah_minus($vvvv['total'])."</td>
                                                    ".$selisih."
                                                    <td class='kanan bawah'></td>
                                                    <td class='kanan bawah text_kanan realisasi_simda'>".ubah_minus($vvvv['realisasi'])."</td>
                                                    <td class='kanan bawah text_kanan realisasi_sipd'>".ubah_minus($vvvv['realisasi_sipd'])."</td>
                                                </tr>";
                                                foreach ($vvvv['data'] as $kkkkk => $vvvvv) {
                                                    $murni = '';
                                                    $selisih = '';
                                                    if($type == 'pergeseran'){
                                                        $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['totalmurni'])."</td>";
                                                        $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvvvv['total']-$vvvvv['totalmurni']))."</td>";
                                                    }
                                                    $body_pendapatan .= "
                                                    <tr class='rek_5'>
                                                        <td class='kiri kanan bawah'>".$kkkkk."</td>
                                                        <td class='kanan bawah'>".$vvvvv['nama']."</td>
                                                        ".$murni."
                                                        <td class='kanan bawah text_kanan'>".ubah_minus($vvvvv['total'])."</td>
                                                        ".$selisih."
                                                        <td class='kanan bawah'></td>
                                                        <td class='kanan bawah text_kanan realisasi_simda'>".ubah_minus($vvvvv['realisasi'])."</td>
                                                        <td class='kanan bawah text_kanan realisasi_sipd'>".ubah_minus($vvvvv['realisasi_sipd'])."</td>
                                                    </tr>";
                                                    foreach ($vvvvv['data'] as $kkkkkk => $vvvvvv) {
                                                        $murni = '';
                                                        $selisih = '';
                                                        if($type == 'pergeseran'){
                                                            $murni = "<td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['totalmurni'])."</td>";
                                                            $selisih = "<td class='kanan bawah text_kanan'>".ubah_minus(($vvvvvv['total']-$vvvvvv['totalmurni']))."</td>";
                                                        }
                                                        $body_pendapatan .= "
                                                        <tr class='rek_6'>
                                                            <td class='kiri kanan bawah'>".$kkkkkk."</td>
                                                            <td class='kanan bawah'>".$vvvvvv['nama']."</td>
                                                            ".$murni."
                                                            <td class='kanan bawah text_kanan'>".ubah_minus($vvvvvv['total'])."</td>
                                                            ".$selisih."
                                                            <td class='kanan bawah'></td>
                                                            <td class='kanan bawah text_kanan realisasi_simda'>".ubah_minus($vvvvvv['realisasi'])."</td>
                                                            <td class='kanan bawah text_kanan realisasi_sipd'>".ubah_minus($vvvvvv['realisasi_sipd'])."</td>
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
        <td class='kanan bawah'></td>
        <td class='kanan bawah text_kanan text_blok realisasi_simda'>".ubah_minus($data_pendapatan['realisasi'])."</td>
        <td class='kanan bawah text_kanan text_blok realisasi_sipd'>".ubah_minus($data_pendapatan['realisasi_sipd'])."</td>
    </tr>";
    return $body_pendapatan;
}

function get_belanja_simda($rek_belanja, $input, $kd_rek_simda, $kd_rek_simda2=false){
    global $wpdb;
    global $simdadb;

    $query_where = '';
    if(!empty($kd_rek_simda2)){
        $query_where = 'and kd_rek_2='.$kd_rek_simda2;
    }
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
                and kd_rek_1=%d
                $query_where
            group by kd_rek_1, kd_rek_2, kd_rek_3, kd_rek_4, kd_rek_5
            order by  kd_rek_1 ASC, kd_rek_2 ASC, kd_rek_3 ASC, kd_rek_4 ASC, kd_rek_5 ASC
        ", $input['tahun_anggaran'], $kd_rek_simda)
    );
    $rek_belanja_simda = $simdadb->CurlSimda($options);
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
        $akun_sipd = $simdadb->CurlSimda($options);
        $v = (array) $v;
        $v['kode_akun'] = $akun_sipd[0]->kd_rek90_1.'.'.$akun_sipd[0]->kd_rek90_2.'.'.$simdadb->CekNull($akun_sipd[0]->kd_rek90_3).'.'.$simdadb->CekNull($akun_sipd[0]->kd_rek90_4).'.'.$simdadb->CekNull($akun_sipd[0]->kd_rek90_5).'.'.$simdadb->CekNull($akun_sipd[0]->kd_rek90_6, 4);
        $rek_belanja_simda2[$v['kode_akun']] = $v;
    }
    // print_r($rek_belanja_simda2); die();
    foreach ($rek_belanja as $k => $v) {
        $v['totalmurni'] = 0;
        if(!empty($rek_belanja_simda2[$v['kode_akun']])){
            $rek_belanja_simda2[$v['kode_akun']]['total'] = $v['total'];
            $rek_belanja_simda2[$v['kode_akun']]['totalmurni'] = $v['totalmurni'];
        }else{
            $rek_belanja_simda2[$v['kode_akun']] = $v;
        }
    }
    $rek_belanja = $rek_belanja_simda2;
    return $rek_belanja;
}

global $wpdb;
$GLOBALS['simdadb'] = $this->simda;
$GLOBALS['pendapatan_murni'] = 0;
$GLOBALS['pendapatan_pergeseran'] = 0;
$GLOBALS['belanja_murni'] = 0;
$GLOBALS['belanja_pergeseran'] = 0;
$GLOBALS['pembiayaan_penerimaan_murni'] = 0;
$GLOBALS['pembiayaan_penerimaan_pergeseran'] = 0;
$GLOBALS['pembiayaan_pengeluaran_murni'] = 0;
$GLOBALS['pembiayaan_pengeluaran_pergeseran'] = 0;

$type = 'murni';
if(!empty($_GET) && !empty($_GET['type'])){
    $type = $_GET['type'];
}
$dari_simda = 0;
if(!empty($_GET) && !empty($_GET['dari_simda'])){
    $dari_simda = $_GET['dari_simda'];
}
if(!empty($_GET) && !empty($_GET['id_skpd'])){
    $input['id_skpd'] = $_GET['id_skpd'];
}

$tabel_history = '';
$where_jadwal = '';
$nama_jadwal = '';
if(!empty($_GET) && !empty($_GET['id_jadwal_lokal'])){
    $input['id_jadwal_lokal'] = $_GET['id_jadwal_lokal'];
    $cek_jadwal = $wpdb->get_row($wpdb->prepare("
        SELECT
            *
        FROM data_jadwal_lokal
        WHERE id_jadwal_lokal=%d
            AND tahun_anggaran=%d
            AND id_tipe=6
    ", $input['id_jadwal_lokal'], $input['tahun_anggaran']), ARRAY_A);
    if(!empty($cek_jadwal)){
        if($cek_jadwal['status']==1){
            $tabel_history = '_history';
            $where_jadwal = $wpdb->prepare(' AND id_jadwal=%d', $input['id_jadwal_lokal']);
        }
        $nama_jadwal = '<h1 class="text-center">Jadwal: '.$cek_jadwal['nama'].'</h1>';
    }
}
$where_jadwal_rka = str_replace('id_jadwal=', 'r.id_jadwal=', $where_jadwal);
$where_sub_keg_bl = str_replace('id_jadwal=', 's.id_jadwal=', $where_jadwal);

if(!empty($input['id_skpd'])){
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            0 as realisasi_sipd,
            kode_akun,
            nama_akun,
            sum(total) as total,
            sum(nilaimurni) as totalmurni
        from data_pendapatan".$tabel_history."
        where tahun_anggaran=%d
            and active=1
            and id_skpd=%d
            ".$where_jadwal."
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
}else{
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            0 as realisasi_sipd,
            kode_akun,
            nama_akun,
            sum(total) as total,
            sum(nilaimurni) as totalmurni
        from data_pendapatan".$tabel_history."
        where tahun_anggaran=%d
            and active=1
            ".$where_jadwal."
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_pendapatan = $wpdb->get_results($sql, ARRAY_A);

foreach ($rek_pendapatan as $k => $v) {
    if(empty($v['kode_akun'])){
        continue;
    }
    if(!empty($input['id_skpd'])){
        $sql_sipd = $wpdb->prepare("
            select
                sum(realisasi) as realisasi
            from data_realisasi_akun_sipd
            where kode_akun='".$v['kode_akun']."'
                and active=1
                and tahun_anggaran=%d
                and id_skpd=%d
                and type='pendapatan'
            group by kode_akun
        ", $input['tahun_anggaran'], $input['id_skpd']);
    }else{
        $sql_sipd = $wpdb->prepare("
            select
                sum(realisasi) as realisasi
            from data_realisasi_akun_sipd
            where kode_akun='".$v['kode_akun']."'
                and active=1
                and tahun_anggaran=%d
                and type='pendapatan'
            group by kode_akun
        ", $input['tahun_anggaran']);
    }
    $rek_pendapatan[$k]['realisasi_sipd'] = $wpdb->get_var($sql_sipd);
}

if($dari_simda != 0){
    $rek_pendapatan = get_belanja_simda($rek_pendapatan, $input, 4);
}

$body_pendapatan = generate_body($rek_pendapatan, true, $type, 'Pendapatan', $dari_simda);

$where_skpd = '';
if(!empty($input['id_skpd'])){
    $where_skpd = $wpdb->prepare('and s.id_sub_skpd=%d', $input['id_skpd']);
}
$sql = $wpdb->prepare("
    select 
        0 as realisasi,
        0 as realisasi_sipd,
        r.tahun_anggaran,
        s.id_sub_skpd as id_skpd,
        s.kode_urusan,
        s.nama_urusan,
        s.kode_bidang_urusan,
        s.nama_bidang_urusan,
        s.kode_skpd,
        s.nama_skpd,
        s.kode_sub_skpd,
        s.nama_sub_skpd,
        s.nama_program,
        s.nama_giat,
        s.nama_sub_giat,
        s.kode_program,
        s.kode_giat,
        s.kode_sub_giat,
        s.no_program,
        s.no_giat,
        s.no_sub_giat,
        r.kode_akun,
        r.nama_akun,
        r.kode_sbl,
        sum(r.rincian) as total,
        sum(r.rincian_murni) as totalmurni
    from data_rka".$tabel_history." r
    inner join data_sub_keg_bl".$tabel_history." s on s.kode_sbl = r.kode_sbl
        and s.active = r.active
        and s.tahun_anggaran = r.tahun_anggaran
    where r.tahun_anggaran=%d
        and r.active=1
        ".$where_jadwal_rka."
        ".$where_sub_keg_bl."
        ".$where_skpd."
    group by r.kode_sbl, r.kode_akun
    order by r.kode_sbl ASC, r.kode_akun ASC
", $input['tahun_anggaran']);
$rek_belanja = $wpdb->get_results($sql, ARRAY_A);

$realisasi_simda = false;
if($realisasi_simda == true){
    foreach ($rek_belanja as $k => $v) {
        if(empty($v['kode_akun'])){
            continue;
        }
        if(!empty($input['id_skpd'])){
            $sql = $wpdb->prepare("
                select
                    sum(realisasi) as realisasi
                from data_realisasi_akun
                where kode_akun='".$v['kode_akun']."'
                    and active=1
                    and tahun_anggaran=%d
                    and id_skpd=%d
                group by kode_akun
            ", $input['tahun_anggaran'], $input['id_skpd']);
            $sql_sipd = $wpdb->prepare("
                select
                    sum(realisasi) as realisasi
                from data_realisasi_akun_sipd
                where kode_akun='".$v['kode_akun']."'
                    and active=1
                    and tahun_anggaran=%d
                    and id_skpd=%d
                    and type='belanja'
                group by kode_akun
            ", $input['tahun_anggaran'], $input['id_skpd']);
        }else{
            $sql = $wpdb->prepare("
                select
                    sum(realisasi) as realisasi
                from data_realisasi_akun
                where kode_akun='".$v['kode_akun']."'
                    and active=1
                    and tahun_anggaran=%d
                group by kode_akun
            ", $input['tahun_anggaran']);
            $sql_sipd = $wpdb->prepare("
                select
                    sum(realisasi) as realisasi
                from data_realisasi_akun_sipd
                where kode_akun='".$v['kode_akun']."'
                    and active=1
                    and tahun_anggaran=%d
                    and type='belanja'
                group by kode_akun
            ", $input['tahun_anggaran']);
        }
        $rek_belanja[$k]['realisasi'] = $wpdb->get_var($sql);
        $rek_belanja[$k]['realisasi_sipd'] = $wpdb->get_var($sql_sipd);
    }
}

if($dari_simda != 0){
    $rek_belanja = get_belanja_simda($rek_belanja, $input, 5);
}

$body_belanja = generate_body_belanja($rek_belanja, true, $type, 'Belanja', $dari_simda, $input);

if(!empty($input['id_skpd'])){
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            0 as realisasi_sipd,
            kode_akun,
            nama_akun,
            sum(total) as total,
            sum(nilaimurni) as totalmurni
        from data_pembiayaan".$tabel_history."
        where tahun_anggaran=%d
            and type='penerimaan'
            and active=1
            and id_skpd=%d
            ".$where_jadwal."
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
}else{
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            0 as realisasi_sipd,
            kode_akun,
            nama_akun,
            sum(total) as total,
            sum(nilaimurni) as totalmurni
        from data_pembiayaan".$tabel_history."
        where tahun_anggaran=%d
            and type='penerimaan'
            and active=1
            ".$where_jadwal."
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_pembiayaan = $wpdb->get_results($sql, ARRAY_A);

if($dari_simda != 0){
    $rek_pembiayaan = get_belanja_simda($rek_pembiayaan, $input, 6, 1);
}

$body_pembiayaan = generate_body($rek_pembiayaan, true, $type, 'Penerimaan Pembiayaan', $dari_simda);

if(!empty($input['id_skpd'])){
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            0 as realisasi_sipd,
            kode_akun,
            nama_akun,
            sum(total) as total,
            sum(nilaimurni) as totalmurni
        from data_pembiayaan".$tabel_history."
        where tahun_anggaran=%d
            and type='pengeluaran'
            and active=1
            and id_skpd=%d
            ".$where_jadwal."
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
}else{
    $sql = $wpdb->prepare("
        select 
            0 as realisasi,
            0 as realisasi_sipd,
            kode_akun,
            nama_akun,
            sum(total) as total,
            sum(nilaimurni) as totalmurni
        from data_pembiayaan".$tabel_history."
        where tahun_anggaran=%d
            and type='pengeluaran'
            and active=1
            ".$where_jadwal."
        group by kode_akun
        order by kode_akun ASC
    ", $input['tahun_anggaran']);
}
$rek_pembiayaan = $wpdb->get_results($sql, ARRAY_A);

if($dari_simda != 0){
    $rek_pembiayaan = get_belanja_simda($rek_pembiayaan, $input, 6, 2);
}

$body_pembiayaan .= generate_body($rek_pembiayaan, false, $type, 'Pengeluaran Pembiayaan', $dari_simda);

$nama_skpd = "";
$options_skpd = array();
$unit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd, nipkepala from data_unit where active=1 and tahun_anggaran=".$input['tahun_anggaran'].' and is_skpd=1 order by kode_skpd ASC', ARRAY_A);
foreach ($unit as $kk => $vv) {
    $options_skpd[] = $vv;
    $subunit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd, nipkepala from data_unit where active=1 and tahun_anggaran=".$input['tahun_anggaran']." and is_skpd=0 and id_unit=".$vv["id_skpd"]." order by kode_skpd ASC", ARRAY_A);
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
    .realisasi_simda, .realisasi_sipd { display: none; }
</style>
<?php echo $nama_jadwal; ?>
<div id="cetak" title="Laporan APBD PENJABARAN Lampiran II Tahun Anggaran <?php echo $input['tahun_anggaran']; ?>">
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
    <h4 style="text-align: center; font-size: 13px; margin: 10px auto; min-width: 450px; max-width: 550px; font-weight: bold; text-transform: uppercase;"><?php echo get_option('_crb_daerah'); ?> <br>RINCIAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI, PROGRAM, KEGIATAN, SUB KEGIATAN, KELOMPOK, JENIS PENDAPATAN, BELANJA, DAN PEMBIAYAAN <br>TAHUN ANGGARAN <?php echo $input['tahun_anggaran']; ?></h4>
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok" rowspan="2">Kode</td>
                <td class="atas kanan bawah text_tengah text_blok" rowspan="2">Uraian</td>
            <?php if($type == 'murni'): ?>
                <td class="atas kanan bawah text_tengah text_blok">Jumlah</td>
            <?php else: ?>
                <?php if($dari_simda != 0): ?>
                    <td class="atas kanan bawah text_tengah text_blok">Sebelum Perubahan SIMDA</td>
                <?php else: ?>
                    <td class="atas kanan bawah text_tengah text_blok">Sebelum Perubahan</td>
                <?php endif; ?>
                <td class="atas kanan bawah text_tengah text_blok">Sesudah Perubahan</td>
                <td class="atas kanan bawah text_tengah text_blok">Bertambah/(Berkurang)</td>
            <?php endif; ?>
                <td class="atas kanan bawah text_tengah text_blok">Dasar Hukum</td>
                <td class="atas kanan bawah text_tengah text_blok realisasi_simda">Realisasi SIMDA</td>
                <td class="atas kanan bawah text_tengah text_blok realisasi_sipd">Realisasi SIPD</td>
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
    function ubah_skpd(){
        var pilih_id_skpd = jQuery('#pilih_skpd').val();
        if(type){
            _url = changeUrl({ url: _url, key: 'type', value: type });
        }
        if(dari_simda){
            _url = changeUrl({ url: _url, key: 'dari_simda', value: dari_simda });
        }
        _url = changeUrl({ url: _url, key: 'id_skpd', value: pilih_id_skpd });
        window.open(_url);
        jQuery('#pilih_skpd').val(id_skpd);
    }
    function tampil_realisasi(that){
        if(jQuery(that).is(':checked')){
            jQuery('.realisasi_simda').show();
        }else{
            jQuery('.realisasi_simda').hide();
        }
    }
    function tampil_realisasi_sipd(that){
        if(jQuery(that).is(':checked')){
            jQuery('.realisasi_sipd').show();
        }else{
            jQuery('.realisasi_sipd').hide();
        }
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
    window.type = url.searchParams.get("type");
    window.dari_simda = url.searchParams.get("dari_simda");
    window.id_skpd = url.searchParams.get("id_skpd");
    if(type && type=='pergeseran'){
        var extend_action = '<a class="button button-primary" target="_blank" href="'+_url+'" style="margin-left: 10px;">Print APBD Lampiran II</a>';
    }else{
        var extend_action = '<a class="button button-primary" target="_blank" href="'+_url+'&type=pergeseran" style="margin-left: 10px;">Print Pergeseran/Perubahan APBD Lampiran II</a>';
    }
    var text = 'Nilai pagu murni dari database SIMDA'
    extend_action += '<div style="margin-top: 15px">';
    if(dari_simda && dari_simda!=0){
        extend_action += '<a href="'+_url+'&type=pergeseran&dari_simda=0"><input type="checkbox" checked> '+text+'</a>';
    }else{
        extend_action += '<a href="'+_url+'&type=pergeseran&dari_simda=1"><input type="checkbox"> '+text+'</a>';
    }
    var options = '<option value="">Semua SKPD</option>';
    list_skpd.map(function(b, i){
        var selected = "";
        if(id_skpd && id_skpd == b.id_skpd){
            selected = "selected";
        }
        options += '<option '+selected+' value="'+b.id_skpd+'">'+b.kode_skpd+' '+b.nama_skpd+'</option>';
    });
    extend_action += '<select id="pilih_skpd" onchange="ubah_skpd();" style="width:500px; margin-left:25px;">'+options+'</select>';
    extend_action += '<label style="margin-left:25px;"><input type="checkbox" onclick="tampil_realisasi(this);"> Tampilkan Realisasi SIMDA</label>';
    extend_action += '<label style="margin-left:25px;"><input type="checkbox" onclick="tampil_realisasi_sipd(this);"> Tampilkan Realisasi SIPD</label>';
    extend_action += '<h4 style="margin-top: 10px;">Tampilkan Baris Rekening</h4>';
    extend_action += '<label><input type="checkbox" onclick="tampil_rekening(this);" checked id="rek_2"> Rekening 2</label>';
    extend_action += '<label style="margin-left:25px;"><input type="checkbox" onclick="tampil_rekening(this);" checked id="rek_3"> Rekening 3</label>';
    extend_action += '<label style="margin-left:25px;"><input type="checkbox" onclick="tampil_rekening(this);" checked id="rek_4"> Rekening 4</label>';
    extend_action += '<label style="margin-left:25px;"><input type="checkbox" onclick="tampil_rekening(this);" checked id="rek_5"> Rekening 5</label>';
    extend_action += '<label style="margin-left:25px;"><input type="checkbox" onclick="tampil_rekening(this);" checked id="rek_6"> Rekening 6</label>';
    extend_action += '</div>';
    jQuery('#action-sipd').append(extend_action);
</script>