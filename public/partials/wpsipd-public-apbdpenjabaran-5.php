<?php
global $wpdb;

$akun_hibah_uang = $wpdb->get_results('SELECT id_akun, kode_akun, nama_akun FROM `data_akun` where is_bankeu_umum=1 order by kode_akun ASC', ARRAY_A);
$data_hibah_uang = array();
foreach ($akun_hibah_uang as $k => $v) {
    $data = $wpdb->get_results("SELECT * FROM `data_rka` where kode_akun='".$v['kode_akun']."' and active=1 and tahun_anggaran=".$input['tahun_anggaran']." order by kode_sbl ASC", ARRAY_A);
    if(!empty($data)){
        $data_hibah_uang = array_merge($data, $data_hibah_uang);
    }
}

$data_hibah_uang_shorted = array(
    'data' => array(),
    'total_murni' => 0,
    'total' => 0
);
foreach ($data_hibah_uang as $k =>$v) {
    $kode = explode('.', $v['kode_sbl']);
    $idskpd = $kode[1];
    $skpd = $wpdb->get_row("SELECT nama_skpd, kode_skpd from data_unit where id_skpd=$idskpd", ARRAY_A);
    if(empty($data_hibah_uang_shorted['data'][$skpd['kode_skpd']])){
        $data_hibah_uang_shorted['data'][$skpd['kode_skpd']] = array(
            'nama' => $skpd['nama_skpd'],
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $sub_keg = $wpdb->get_row("SELECT nama_sub_giat from data_sub_keg_bl where kode_sbl='".$v['kode_sbl']."'", ARRAY_A);
    if(empty($data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']])){
        $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']] = array(
            'nama' => $sub_keg['nama_sub_giat'],
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $kelompok = $v['idsubtitle'].'||'.$v['subs_bl_teks'];
    if(empty($data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok])){
        $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok] = array(
            'nama' => $v['subs_bl_teks'],
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $keterangan = $v['idketerangan'].'||'.$v['ket_bl_teks'];
    if(empty($data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan])){
        $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan] = array(
            'nama' => $v['ket_bl_teks'],
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    if(empty($data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']])){
        $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']] = array(
            'nama' => $v['nama_akun'],
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['data'][] = $v;
    $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['total'] += $v['rincian'];
    $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['total'] += $v['rincian'];
    $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['total'] += $v['rincian'];
    $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total'] += $v['rincian'];
    $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['total'] += $v['rincian'];
    $data_hibah_uang_shorted['total'] += $v['rincian'];

    $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['total_murni'] += $v['rincian_murni'];
    $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['total_murni'] += $v['rincian_murni'];
    $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['total_murni'] += $v['rincian_murni'];
    $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total_murni'] += $v['rincian_murni'];
    $data_hibah_uang_shorted['data'][$skpd['kode_skpd']]['total_murni'] += $v['rincian_murni'];
    $data_hibah_uang_shorted['total_murni'] += $v['rincian_murni'];
}
ksort($data_hibah_uang_shorted['data']);

$body_umum = '';
foreach ($data_hibah_uang_shorted['data'] as $k => $skpd) {
    $body_umum .= '
        <tr>
            <td class="kanan bawah kiri text_tengah text_blok"></td>
            <td class="kanan bawah text_blok" colspan="2">'.$k.' '.$skpd['nama'].'</td>
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['total_murni'],0,",",".").'</td>
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['total'],0,",",".").'</td>
            <td class="kanan bawah text_blok text_kanan">'.number_format(($skpd['total']-$skpd['total_murni']),0,",",".").'</td>
        </tr>
    ';
    foreach ($skpd['data'] as $sub_keg) {
        $body_umum .= '
            <tr>
                <td class="kanan bawah kiri text_tengah text_blok"></td>
                <td class="kanan bawah text_blok" colspan="2" style="padding-left: 20px;">'.$sub_keg['nama'].'</td>
                <td class="kanan bawah text_blok text_kanan">'.number_format($sub_keg['total_murni'],0,",",".").'</td>
                <td class="kanan bawah text_blok text_kanan">'.number_format($sub_keg['total'],0,",",".").'</td>
                <td class="kanan bawah text_blok text_kanan">'.number_format(($sub_keg['total']-$sub_keg['total_murni']),0,",",".").'</td>
            </tr>
        ';
        foreach ($sub_keg['data'] as $kel) {
            $body_umum .= '
                <tr>
                    <td class="kanan bawah kiri text_tengah text_blok"></td>
                    <td class="kanan bawah text_blok" colspan="2" style="padding-left: 40px;">'.$kel['nama'].'</td>
                    <td class="kanan bawah text_blok text_kanan">'.number_format($kel['total_murni'],0,",",".").'</td>
                    <td class="kanan bawah text_blok text_kanan">'.number_format($kel['total'],0,",",".").'</td>
                    <td class="kanan bawah text_blok text_kanan">'.number_format(($kel['total']-$kel['total_murni']),0,",",".").'</td>
                </tr>
            ';
            foreach ($kel['data'] as $ket) {
                $body_umum .= '
                    <tr>
                        <td class="kanan bawah kiri text_tengah text_blok"></td>
                        <td class="kanan bawah text_blok" colspan="2" style="padding-left: 60px;">'.$ket['nama'].'</td>
                        <td class="kanan bawah text_blok text_kanan">'.number_format($ket['total_murni'],0,",",".").'</td>
                        <td class="kanan bawah text_blok text_kanan">'.number_format($ket['total'],0,",",".").'</td>
                        <td class="kanan bawah text_blok text_kanan">'.number_format(($ket['total']-$ket['total_murni']),0,",",".").'</td>
                    </tr>
                ';
                ksort($ket['data']);
                foreach ($ket['data'] as $akun) {
                    $body_umum .= '
                        <tr>
                            <td class="kanan bawah kiri text_tengah text_blok"></td>
                            <td class="kanan bawah text_blok" colspan="2" style="padding-left: 80px;">'.$akun['nama'].'</td>
                            <td class="kanan bawah text_blok text_kanan">'.number_format($akun['total_murni'],0,",",".").'</td>
                            <td class="kanan bawah text_blok text_kanan">'.number_format($akun['total'],0,",",".").'</td>
                            <td class="kanan bawah text_blok text_kanan">'.number_format(($akun['total']-$akun['total_murni']),0,",",".").'</td>
                        </tr>
                    ';
                    $no = 0;
                    foreach ($akun['data'] as $rincian) {
                        $no++;
                        $alamat = array();
                        if(!empty($rincian['id_lurah_penerima'])){
                            $alamat[] = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$rincian['id_lurah_penerima']." and is_kel=1", ARRAY_A);
                        }
                        if(!empty($rincian['id_camat_penerima'])){
                            $alamat[] = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$rincian['id_camat_penerima']." and is_kec=1", ARRAY_A);
                        }
                        if(!empty($rincian['id_kokab_penerima'])){
                            $alamat[] = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$rincian['id_kokab_penerima']." and is_kab=1", ARRAY_A);
                        }
                        if(!empty($rincian['id_prop_penerima'])){
                            $alamat[] = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$rincian['id_prop_penerima']." and is_prov=1", ARRAY_A);
                        }
                        $alamat = implode(', ', $alamat);
                        $body_umum .= '
                            <tr>
                                <td class="kanan bawah kiri text_tengah">'.$no.'</td>
                                <td class="kanan bawah" style="padding-left: 100px;">'.$rincian['lokus_akun_teks'].'</td>
                                <td class="kanan bawah">'.$alamat.'</td>
                                <td class="kanan bawah text_kanan">'.number_format($rincian['rincian_murni'],0,",",".").'</td>
                                <td class="kanan bawah text_kanan">'.number_format($rincian['rincian'],0,",",".").'</td>
                                <td class="kanan bawah text_kanan">'.number_format(($rincian['rincian']-$rincian['rincian_murni']),0,",",".").'</td>
                            </tr>
                        ';
                    }
                }
            }
        }
    }
}
$body_umum .= '
    <tr>
        <td class="kiri kanan bawah text_blok text_kanan" colspan="3">Jumlah Total</td>
        <td class="kanan bawah text_blok text_kanan">'.number_format($data_hibah_uang_shorted['total_murni'],0,",",".").'</td>
        <td class="kanan bawah text_blok text_kanan">'.number_format($data_hibah_uang_shorted['total'],0,",",".").'</td>
        <td class="kanan bawah text_blok text_kanan">'.number_format(($data_hibah_uang_shorted['total']-$data_hibah_uang_shorted['total_murni']),0,",",".").'</td>
    </tr>
';


$akun_hibah_brg = $wpdb->get_results('SELECT id_akun, kode_akun, nama_akun FROM `data_akun` where is_bankeu_khusus=1 order by kode_akun ASC', ARRAY_A);
$data_hibah_brg = array();
foreach ($akun_hibah_brg as $k => $v) {
    $data = $wpdb->get_results("SELECT * FROM `data_rka` where kode_akun='".$v['kode_akun']."' and active=1 and tahun_anggaran=".$input['tahun_anggaran']." order by kode_sbl ASC", ARRAY_A);
    if(!empty($data)){
        $data_hibah_brg = array_merge($data, $data_hibah_brg);
    }
}

$data_hibah_brg_shorted = array(
    'data' => array(),
    'total_murni' => 0,
    'total' => 0
);
foreach ($data_hibah_brg as $k =>$v) {
    $kode = explode('.', $v['kode_sbl']);
    $idskpd = $kode[1];
    $skpd = $wpdb->get_row("SELECT nama_skpd, kode_skpd from data_unit where id_skpd=$idskpd", ARRAY_A);
    if(empty($data_hibah_brg_shorted['data'][$skpd['kode_skpd']])){
        $data_hibah_brg_shorted['data'][$skpd['kode_skpd']] = array(
            'nama' => $skpd['nama_skpd'],
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $sub_keg = $wpdb->get_row("SELECT nama_sub_giat from data_sub_keg_bl where kode_sbl='".$v['kode_sbl']."'", ARRAY_A);
    if(empty($data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']])){
        $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']] = array(
            'nama' => $sub_keg['nama_sub_giat'],
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $kelompok = $v['idsubtitle'].'||'.$v['subs_bl_teks'];
    if(empty($data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok])){
        $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok] = array(
            'nama' => $v['subs_bl_teks'],
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $keterangan = $v['idketerangan'].'||'.$v['ket_bl_teks'];
    if(empty($data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan])){
        $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan] = array(
            'nama' => $v['ket_bl_teks'],
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    if(empty($data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']])){
        $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']] = array(
            'nama' => $v['nama_akun'],
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['data'][] = $v;
    $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['total'] += $v['rincian'];
    $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['total'] += $v['rincian'];
    $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['total'] += $v['rincian'];
    $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total'] += $v['rincian'];
    $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['total'] += $v['rincian'];
    $data_hibah_brg_shorted['total'] += $v['rincian'];

    $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['total_murni'] += $v['rincian_murni'];
    $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['total_murni'] += $v['rincian_murni'];
    $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['total_murni'] += $v['rincian_murni'];
    $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total_murni'] += $v['rincian_murni'];
    $data_hibah_brg_shorted['data'][$skpd['kode_skpd']]['total_murni'] += $v['rincian_murni'];
    $data_hibah_brg_shorted['total_murni'] += $v['rincian_murni'];
}
ksort($data_hibah_brg_shorted['data']);

$body_khusus = '';
foreach ($data_hibah_brg_shorted['data'] as $k => $skpd) {
    $body_khusus .= '
        <tr>
            <td class="kanan bawah kiri text_tengah text_blok"></td>
            <td class="kanan bawah text_blok" colspan="3">'.$k.' '.$skpd['nama'].'</td>
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['total_murni'],0,",",".").'</td>
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['total'],0,",",".").'</td>
            <td class="kanan bawah text_blok text_kanan">'.number_format(($skpd['total']-$skpd['total_murni']),0,",",".").'</td>
        </tr>
    ';
    foreach ($skpd['data'] as $sub_keg) {
        $body_khusus .= '
            <tr>
                <td class="kanan bawah kiri text_tengah text_blok"></td>
                <td class="kanan bawah text_blok" colspan="3" style="padding-left: 20px;">'.$sub_keg['nama'].'</td>
                <td class="kanan bawah text_blok text_kanan">'.number_format($sub_keg['total_murni'],0,",",".").'</td>
                <td class="kanan bawah text_blok text_kanan">'.number_format($sub_keg['total'],0,",",".").'</td>
                <td class="kanan bawah text_blok text_kanan">'.number_format(($sub_keg['total']-$sub_keg['total_murni']),0,",",".").'</td>
            </tr>
        ';
        foreach ($sub_keg['data'] as $kel) {
            $body_khusus .= '
                <tr>
                    <td class="kanan bawah kiri text_tengah text_blok"></td>
                    <td class="kanan bawah text_blok" colspan="3" style="padding-left: 40px;">'.$kel['nama'].'</td>
                    <td class="kanan bawah text_blok text_kanan">'.number_format($kel['total_murni'],0,",",".").'</td>
                    <td class="kanan bawah text_blok text_kanan">'.number_format($kel['total'],0,",",".").'</td>
                    <td class="kanan bawah text_blok text_kanan">'.number_format(($kel['total']-$kel['total_murni']),0,",",".").'</td>
                </tr>
            ';
            foreach ($kel['data'] as $ket) {
                $body_khusus .= '
                    <tr>
                        <td class="kanan bawah kiri text_tengah text_blok"></td>
                        <td class="kanan bawah text_blok" colspan="3" style="padding-left: 60px;">'.$ket['nama'].'</td>
                        <td class="kanan bawah text_blok text_kanan">'.number_format($ket['total_murni'],0,",",".").'</td>
                        <td class="kanan bawah text_blok text_kanan">'.number_format($ket['total'],0,",",".").'</td>
                        <td class="kanan bawah text_blok text_kanan">'.number_format(($ket['total']-$ket['total_murni']),0,",",".").'</td>
                    </tr>
                ';
                ksort($ket['data']);
                foreach ($ket['data'] as $akun) {
                    $body_khusus .= '
                        <tr>
                            <td class="kanan bawah kiri text_tengah text_blok"></td>
                            <td class="kanan bawah text_blok" colspan="3" style="padding-left: 80px;">'.$akun['nama'].'</td>
                            <td class="kanan bawah text_blok text_kanan">'.number_format($akun['total_murni'],0,",",".").'</td>
                            <td class="kanan bawah text_blok text_kanan">'.number_format($akun['total'],0,",",".").'</td>
                            <td class="kanan bawah text_blok text_kanan">'.number_format(($akun['total']-$akun['total_murni']),0,",",".").'</td>
                        </tr>
                    ';
                    $no = 0;
                    foreach ($akun['data'] as $rincian) {
                        $no++;
                        $alamat = array();
                        if(!empty($rincian['id_lurah_penerima'])){
                            $alamat[] = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$rincian['id_lurah_penerima']." and is_kel=1", ARRAY_A);
                        }
                        if(!empty($rincian['id_camat_penerima'])){
                            $alamat[] = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$rincian['id_camat_penerima']." and is_kec=1", ARRAY_A);
                        }
                        if(!empty($rincian['id_kokab_penerima'])){
                            $alamat[] = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$rincian['id_kokab_penerima']." and is_kab=1", ARRAY_A);
                        }
                        if(!empty($rincian['id_prop_penerima'])){
                            $alamat[] = $wpdb->get_row("SELECT nama from data_alamat where id_alamat=".$rincian['id_prop_penerima']." and is_prov=1", ARRAY_A);
                        }
                        $alamat = implode(', ', $alamat);
                        $body_khusus .= '
                            <tr>
                                <td class="kanan bawah kiri text_tengah">'.$no.'</td>
                                <td class="kanan bawah" style="padding-left: 100px;">'.$rincian['lokus_akun_teks'].'</td>
                                <td class="kanan bawah">'.$alamat.'</td>
                                <td class="kanan bawah">'.$rincian['nama_komponen'].'<br>'.$rincian['spek_komponen'].'</td>
                                <td class="kanan bawah text_kanan">'.number_format($rincian['rincian_murni'],0,",",".").'</td>
                                <td class="kanan bawah text_kanan">'.number_format($rincian['rincian'],0,",",".").'</td>
                                <td class="kanan bawah text_kanan">'.number_format(($rincian['rincian']-$rincian['rincian_murni']),0,",",".").'</td>
                            </tr>
                        ';
                    }
                }
            }
        }
    }
}
$body_khusus .= '
    <tr>
        <td class="kiri kanan bawah text_blok text_kanan" colspan="4">Jumlah Total</td>
        <td class="kanan bawah text_blok text_kanan">'.number_format($data_hibah_uang_shorted['total_murni'],0,",",".").'</td>
        <td class="kanan bawah text_blok text_kanan">'.number_format($data_hibah_uang_shorted['total'],0,",",".").'</td>
        <td class="kanan bawah text_blok text_kanan">'.number_format(($data_hibah_uang_shorted['total']-$data_hibah_uang_shorted['total_murni']),0,",",".").'</td>
    </tr>
';

// print_r($data_hibah_uang_shorted);
?>
<div id="cetak" title="Laporan APBD PENJABARAN Lampiran 5 Tahun Anggaran <?php echo $input['tahun_anggaran']; ?>" style="padding: 5px;">
    <table align="right" class="no-border no-padding" cellspacing="0" cellpadding="0" style="width:280px; font-size: 12px;">
        <tr>
            <td width="80" valign="top">Lampiran V </td>
            <td width="10" valign="top">:</td>
            <td colspan="3" valign="top" contenteditable="true">  Peraturan Bupati xxxx   </td>
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
    <h4 style="text-align: left; font-size: 13px; font-weight: bold;">1) UMUM</h4>
    <h4 style="text-align: center; font-size: 13px; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">DAFTAR NAMA CALON PENERIMA, ALAMAT DAN BESARAN<br>ALOKASI BANTUAN KEUANGAN BERSIFAT UMUM YANG DITERIMA SERTA SKPD PEMBERI BANTUAN KEUANGAN</h4>
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok">No</td>
                <td class="atas kanan bawah text_tengah text_blok">Nama Penerima</td>
                <td class="atas kanan bawah text_tengah text_blok">Alamat Penerima</td>
                <td class="atas kanan bawah text_tengah text_blok">Sebelum Perubahan</td>
                <td class="atas kanan bawah text_tengah text_blok">Sesudah Perubahan</td>
                <td class="atas kanan bawah text_tengah text_blok">Bertambah/(Berkurang)</td>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_umum; ?>
        </tbody>
    </table>
    <hr>
    <h4 style="text-align: left; font-size: 13px; font-weight: bold;">2) KHUSUS</h4>
    <h4 style="text-align: center; font-size: 13px; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">DAFTAR NAMA CALON PENERIMA, ALAMAT DAN BESARAN<br>ALOKASI BANTUAN KEUANGAN BERSIFAT KHUSUS YANG DITERIMA SERTA SKPD PEMBERI BANTUAN KEUANGAN</h4>
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok">No</td>
                <td class="atas kanan bawah text_tengah text_blok">Nama Penerima</td>
                <td class="atas kanan bawah text_tengah text_blok">Alamat Penerima</td>
                <td class="atas kanan bawah text_tengah text_blok">Sebelum Perubahan</td>
                <td class="atas kanan bawah text_tengah text_blok">Sesudah Perubahan</td>
                <td class="atas kanan bawah text_tengah text_blok">Bertambah/(Berkurang)</td>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_khusus; ?>
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
</script>