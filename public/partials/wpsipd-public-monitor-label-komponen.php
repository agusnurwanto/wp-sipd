<?php
$input = shortcode_atts( array(
    'id_label' => '',
    'tahun_anggaran' => '2022'
), $atts );

if(empty($input['id_label'])){
    die('<h1>ID Label tidak boleh kosong!</h1>');
}

global $wpdb;
$type = 'murni';
if(!empty($_GET) && !empty($_GET['type'])){
    $type = $_GET['type'];
}

$label_db = $wpdb->get_results($wpdb->prepare("
        SELECT
            *
        FROM data_label_komponen
        where active=1
            and tahun_anggaran=%d
            and id=%d
    ", $input['tahun_anggaran'], $input['id_label']), ARRAY_A);
if(empty($label_db)){
    die('<h1>ID Label '.$input['id_label'].' tidak ditemukan di database!</h1>');
}

$data_label = array();
$where_skpd = '';
$inner_skpd = '';
if(!empty($_GET) && !empty($_GET['id_skpd'])){
    $inner_skpd = '
        inner join data_sub_keg_bl s on s.kode_sbl=r.kode_sbl
            and s.active=r.active
            and s.tahun_anggaran=r.tahun_anggaran';
    $where_skpd = 'and s.id_sub_skpd='.$_GET['id_skpd'];
}
$sql = $wpdb->prepare("
        SELECT 
            r.*,
            rr.realisasi 
        FROM `data_rka` r
            ".$inner_skpd."
            inner join data_mapping_label m on m.active=r.active
                and m.tahun_anggaran=r.tahun_anggaran
                and m.id_rinci_sub_bl=r.id_rinci_sub_bl
            left join data_realisasi_rincian rr on rr.active=r.active
                and rr.tahun_anggaran=r.tahun_anggaran
                and rr.id_rinci_sub_bl=r.id_rinci_sub_bl
        where r.active=1 
            and r.tahun_anggaran=%d
            and m.id_label_komponen=%d
            ".$where_skpd."
            order by r.kode_sbl ASC
    ", $input['tahun_anggaran'], $input['id_label']);
// die($sql);
$data = $wpdb->get_results($sql, ARRAY_A);
if(!empty($data)){
    $data_label = $data;
}

$data_label_shorted = array(
    'data' => array(),
    'realisasi' => 0,
    'total_murni' => 0,
    'total' => 0
);
foreach ($data_label as $k =>$v) {
    $kode = explode('.', $v['kode_sbl']);
    $idskpd = $kode[1];
    $skpd = $wpdb->get_row("SELECT nama_skpd, kode_skpd from data_unit where id_skpd=$idskpd", ARRAY_A);
    if(empty($data_label_shorted['data'][$skpd['kode_skpd']])){
        $data_label_shorted['data'][$skpd['kode_skpd']] = array(
            'nama' => $skpd['nama_skpd'],
            'realisasi' => 0,
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $sub_keg = $wpdb->get_row("SELECT nama_sub_giat, kode_giat, nama_giat from data_sub_keg_bl where kode_sbl='".$v['kode_sbl']."'", ARRAY_A);
    if(empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']])){
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']] = array(
            'kode_giat' => $sub_keg['kode_giat'],
            'nama_giat' => $sub_keg['nama_giat'],
            'nama' => $sub_keg['nama_sub_giat'],
            'realisasi' => 0,
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $kelompok = $v['idsubtitle'].'||'.$v['subs_bl_teks'];
    if(empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok])){
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok] = array(
            'nama' => $v['subs_bl_teks'],
            'realisasi' => 0,
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $keterangan = $v['idketerangan'].'||'.$v['ket_bl_teks'];
    if(empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan])){
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan] = array(
            'nama' => $v['ket_bl_teks'],
            'realisasi' => 0,
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    if(empty($data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']])){
        $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']] = array(
            'nama' => $v['nama_akun'],
            'realisasi' => 0,
            'total_murni' => 0,
            'total' => 0,
            'data' => array()
        );
    }
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['data'][] = $v;
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['total'] += $v['rincian'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['total'] += $v['rincian'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['total'] += $v['rincian'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total'] += $v['rincian'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['total'] += $v['rincian'];
    $data_label_shorted['total'] += $v['rincian'];

    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['total_murni'] += $v['rincian_murni'];
    $data_label_shorted['total_murni'] += $v['rincian_murni'];

    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['data'][$v['kode_akun']]['realisasi'] += $v['realisasi'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['data'][$keterangan]['realisasi'] += $v['realisasi'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['data'][$kelompok]['realisasi'] += $v['realisasi'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['data'][$v['kode_sbl']]['realisasi'] += $v['realisasi'];
    $data_label_shorted['data'][$skpd['kode_skpd']]['realisasi'] += $v['realisasi'];
    $data_label_shorted['realisasi'] += $v['realisasi'];
}
ksort($data_label_shorted['data']);

$body_label = '';
foreach ($data_label_shorted['data'] as $k => $skpd) {
    $murni = '';
    $selisih = '';
    if($type == 'pergeseran'){
        $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($skpd['total_murni'],0,",",".")."</td>";
        $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($skpd['total']-$skpd['total_murni']),0,",",".")."</td>";
    }
    $penyerapan = 0;
    if(!empty($skpd['total'])){
        $penyerapan = $this->pembulatan(($skpd['realisasi']/$skpd['total'])*100);
    }
    $nama_page = 'RFK '.$skpd['nama'].' '.$k.' | '.$v['tahun_anggaran'];
    $custom_post = get_page_by_title($nama_page, OBJECT, 'page');
    $body_label .= '
        <tr>
            <td class="kanan bawah kiri text_tengah text_blok"></td>
            <td class="kanan bawah text_blok" colspan="2"><a href="'.get_permalink($custom_post) . '?key=' . $this->gen_key().'" target="_blank">'.$k.' '.$skpd['nama'].'</a></td>
            '.$murni.'
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['total'],0,",",".").'</td>
            '.$selisih.'
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['realisasi'],0,",",".").'</td>
            <td class="kanan bawah text_blok text_kanan">'.$penyerapan.'</td>
            <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
        </tr>
    ';
    foreach ($skpd['data'] as $sub_keg) {
        $murni = '';
        $selisih = '';
        if($type == 'pergeseran'){
            $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($sub_keg['total_murni'],0,",",".")."</td>";
            $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($sub_keg['total']-$sub_keg['total_murni']),0,",",".")."</td>";
        }
        $penyerapan = 0;
        if(!empty($sub_keg['total'])){
            $penyerapan = $this->pembulatan(($sub_keg['realisasi']/$sub_keg['total'])*100);
        }
        $nama_page = $input['tahun_anggaran'] . ' | ' . $k . ' | ' . $sub_keg['kode_giat'] . ' | ' . $sub_keg['nama_giat'];
        $custom_post = get_page_by_title($nama_page, OBJECT, 'post');
        $link = $this->get_link_post($custom_post);
        $body_label .= '
            <tr class="sub_keg">
                <td class="kanan bawah kiri text_tengah text_blok"></td>
                <td class="kanan bawah text_blok" colspan="2" style="padding-left: 20px;"><a href="'.$link.'" target="_blank">'.$sub_keg['nama'].'</a></td>
                '.$murni.'
                <td class="kanan bawah text_blok text_kanan">'.number_format($sub_keg['total'],0,",",".").'</td>
                '.$selisih.'
                <td class="kanan bawah text_blok text_kanan">'.number_format($sub_keg['realisasi'],0,",",".").'</td>
                <td class="kanan bawah text_blok text_kanan">'.$penyerapan.'</td>
                <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
            </tr>
        ';
        foreach ($sub_keg['data'] as $kel) {
            $murni = '';
            $selisih = '';
            if($type == 'pergeseran'){
                $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($kel['total_murni'],0,",",".")."</td>";
                $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($kel['total']-$kel['total_murni']),0,",",".")."</td>";
            }
            $penyerapan = 0;
            if(!empty($kel['total'])){
                $penyerapan = $this->pembulatan(($kel['realisasi']/$kel['total'])*100);
            }
            $body_label .= '
                <tr class="kelompok">
                    <td class="kanan bawah kiri text_tengah text_blok"></td>
                    <td class="kanan bawah text_blok" colspan="2" style="padding-left: 40px;">'.$kel['nama'].'</td>
                    '.$murni.'
                    <td class="kanan bawah text_blok text_kanan">'.number_format($kel['total'],0,",",".").'</td>
                    '.$selisih.'
                    <td class="kanan bawah text_blok text_kanan">'.number_format($kel['realisasi'],0,",",".").'</td>
                    <td class="kanan bawah text_blok text_kanan">'.$penyerapan.'</td>
                    <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
                </tr>
            ';
            foreach ($kel['data'] as $ket) {
                $murni = '';
                $selisih = '';
                if($type == 'pergeseran'){
                    $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($ket['total_murni'],0,",",".")."</td>";
                    $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($ket['total']-$ket['total_murni']),0,",",".")."</td>";
                }
                $penyerapan = 0;
                if(!empty($ket['total'])){
                    $penyerapan = $this->pembulatan(($ket['realisasi']/$ket['total'])*100);
                }
                $body_label .= '
                    <tr class="keterangan">
                        <td class="kanan bawah kiri text_tengah text_blok"></td>
                        <td class="kanan bawah text_blok" colspan="2" style="padding-left: 60px;">'.$ket['nama'].'</td>
                        '.$murni.'
                        <td class="kanan bawah text_blok text_kanan">'.number_format($ket['total'],0,",",".").'</td>
                        '.$selisih.'
                        <td class="kanan bawah text_blok text_kanan">'.number_format($ket['realisasi'],0,",",".").'</td>
                        <td class="kanan bawah text_blok text_kanan">'.$penyerapan.'</td>
                        <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
                    </tr>
                ';
                ksort($ket['data']);
                foreach ($ket['data'] as $akun) {
                    $murni = '';
                    $selisih = '';
                    if($type == 'pergeseran'){
                        $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($akun['total_murni'],0,",",".")."</td>";
                        $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($akun['total']-$akun['total_murni']),0,",",".")."</td>";
                    }
                    $penyerapan = 0;
                    if(!empty($akun['total'])){
                        $penyerapan = $this->pembulatan(($akun['realisasi']/$akun['total'])*100);
                    }
                    $body_label .= '
                        <tr class="rekening">
                            <td class="kanan bawah kiri text_tengah text_blok"></td>
                            <td class="kanan bawah text_blok" colspan="2" style="padding-left: 80px;">'.$akun['nama'].'</td>
                            '.$murni.'
                            <td class="kanan bawah text_blok text_kanan">'.number_format($akun['total'],0,",",".").'</td>
                            '.$selisih.'
                            <td class="kanan bawah text_blok text_kanan">'.number_format($akun['realisasi'],0,",",".").'</td>
                            <td class="kanan bawah text_blok text_kanan">'.$penyerapan.'</td>
                            <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
                        </tr>
                    ';
                    $no = 0;
                    foreach ($akun['data'] as $rincian) {
                        $no++;
                        $alamat_array = $this->get_alamat($input, $rincian);
                        $alamat = $alamat_array['alamat'];
                        $lokus_akun_teks = $alamat_array['lokus_akun_teks'];
                        $murni = '';
                        $selisih = '';
                        if($type == 'pergeseran'){
                            $murni = "<td class='kanan bawah text_kanan'>".number_format($rincian['rincian_murni'],0,",",".")."</td>";
                            $selisih = "<td class='kanan bawah text_kanan'>".number_format(($rincian['rincian']-$rincian['rincian_murni']),0,",",".")."</td>";
                        }
                        $penyerapan = 0;
                        if(!empty($rincian['rincian'])){
                            $penyerapan = $this->pembulatan(($rincian['realisasi']/$rincian['rincian'])*100);
                        }
                        $body_label .= '
                            <tr class="rincian" data-db="'.$rincian['id_rinci_sub_bl'].'|'.$rincian['kode_sbl'].'" data-lokus-teks="'.$lokus_akun_teks.'">
                                <td class="kanan bawah kiri text_tengah">'.$no.'</td>
                                <td class="kanan bawah" style="padding-left: 100px;">'.$rincian['lokus_akun_teks'].$rincian['nama_komponen'].'</td>
                                <td class="kanan bawah">'.$alamat.$rincian['spek_komponen'].'</td>
                                '.$murni.'
                                <td class="kanan bawah text_kanan">'.number_format($rincian['rincian'],0,",",".").'</td>
                                '.$selisih.'
                                <td class="kanan bawah text_kanan">'.number_format($rincian['realisasi'],0,",",".").'</td>
                                <td class="kanan bawah text_kanan">'.$penyerapan.'</td>
                                <td class="kanan bawah text_tengah">'.$rincian['koefisien'].'</td>
                                <td class="kanan bawah text_tengah">'.$rincian['satuan'].'</td>
                            </tr>
                        ';
                    }
                }
            }
        }
        $murni = '';
        $selisih = '';
        if($type == 'pergeseran'){
            $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($sub_keg['total_murni'],0,",",".")."</td>";
            $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($sub_keg['total']-$sub_keg['total_murni']),0,",",".")."</td>";
        }
        $penyerapan = 0;
        if(!empty($sub_keg['total'])){
            $penyerapan = $this->pembulatan(($sub_keg['realisasi']/$sub_keg['total'])*100);
        }
        $body_label .= '
            <tr>
                <td class="kanan bawah kiri text_tengah text_blok">&nbsp;</td>
                <td class="kanan bawah text_blok text_kanan" colspan="2">Jumlah Pada Sub Kegiatan</td>
                '.$murni.'
                <td class="kanan bawah text_blok text_kanan">'.number_format($sub_keg['total'],0,",",".").'</td>
                '.$selisih.'
                <td class="kanan bawah text_blok text_kanan">'.number_format($sub_keg['realisasi'],0,",",".").'</td>
                <td class="kanan bawah text_blok text_kanan">'.$penyerapan.'</td>
                <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
            </tr>
        ';
    }
    $murni = '';
    $selisih = '';
    if($type == 'pergeseran'){
        $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($skpd['total_murni'],0,",",".")."</td>";
        $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($skpd['total']-$skpd['total_murni']),0,",",".")."</td>";
    }
    $penyerapan = 0;
    if(!empty($skpd['total'])){
        $penyerapan = $this->pembulatan(($skpd['realisasi']/$skpd['total'])*100);
    }
    $body_label .= '
        <tr>
            <td class="kanan bawah kiri text_tengah text_blok">&nbsp;</td>
            <td class="kanan bawah text_blok text_kanan" colspan="2">Jumlah Pada SKPD</td>
            '.$murni.'
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['total'],0,",",".").'</td>
            '.$selisih.'
            <td class="kanan bawah text_blok text_kanan">'.number_format($skpd['realisasi'],0,",",".").'</td>
            <td class="kanan bawah text_blok text_kanan">'.$penyerapan.'</td>
            <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
        </tr>
    ';
}
$murni = '';
$selisih = '';
if($type == 'pergeseran'){
    $murni = "<td class='kanan bawah text_kanan text_blok'>".number_format($data_label_shorted['total_murni'],0,",",".")."</td>";
    $selisih = "<td class='kanan bawah text_kanan text_blok'>".number_format(($data_label_shorted['total']-$data_label_shorted['total_murni']),0,",",".")."</td>";
}
$penyerapan = 0;
if(!empty($data_label_shorted['total'])){
    $penyerapan = $this->pembulatan(($data_label_shorted['realisasi']/$data_label_shorted['total'])*100);
}
$body_label .= '
    <tr>
        <td class="kiri kanan bawah text_blok text_kanan" colspan="3">Jumlah Total</td>
        '.$murni.'
        <td class="kanan bawah text_blok text_kanan">'.number_format($data_label_shorted['total'],0,",",".").'</td>
        '.$selisih.'
        <td class="kanan bawah text_blok text_kanan">'.number_format($data_label_shorted['realisasi'],0,",",".").'</td>
        <td class="kanan bawah text_blok text_kanan">'.$penyerapan.'</td>
        <td colspan="2" class="kanan bawah kiri text_tengah text_blok"></td>
    </tr>
';

?>
<div id="cetak" title="Monitoring dan Evaluasi Label Komponen <?php echo $label_db[0]['nama']; ?> Tahun Anggaran <?php echo $input['tahun_anggaran']; ?>" style="padding: 5px;">
    <h4 style="text-align: center; font-size: 20px; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">Monitoring dan Evaluasi Label Komponen<br><?php echo $label_db[0]['nama']; ?><br>Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h4>
    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
        <thead>
            <tr>
                <td rowspan="2" class="atas kanan bawah kiri text_tengah text_blok">No</td>
                <td rowspan="2" class="atas kanan bawah text_tengah text_blok">SKPD / Sub Kegiatan / Komponen</td>
                <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Keterangan</td>
                <?php if($type == 'murni'): ?>
                    <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Anggaran</td>
                <?php else: ?>
                    <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Sebelum Perubahan</td>
                    <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Sesudah Perubahan</td>
                    <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Bertambah/(Berkurang)</td>
                <?php endif; ?>
                <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Realisasi</td>
                <td rowspan="2" class="atas kanan bawah text_tengah text_blok">Penyerapan</td>
                <td colspan="2" class="atas kanan bawah text_tengah text_blok">Capaian Output</td>
            </tr>
            <tr>
                <td class="atas kanan bawah text_tengah text_blok">Volume</td>
                <td class="atas kanan bawah text_tengah text_blok">Satuan</td>
            </tr>
            <tr>
                <td class="atas kanan bawah kiri text_tengah text_blok">1</td>
                <td class="atas kanan bawah text_tengah text_blok">2</td>
                <td class="atas kanan bawah text_tengah text_blok">3</td>
                <?php if($type == 'murni'): ?>
                    <td class="atas kanan bawah text_tengah text_blok">4</td>
                    <td class="atas kanan bawah text_tengah text_blok">5</td>
                    <td class="atas kanan bawah text_tengah text_blok">6=(5/4)*100</td>
                    <td class="atas kanan bawah text_tengah text_blok">7</td>
                    <td class="atas kanan bawah text_tengah text_blok">8</td>
                <?php else: ?>
                    <td class="atas kanan bawah text_tengah text_blok">4</td>
                    <td class="atas kanan bawah text_tengah text_blok">5</td>
                    <td class="atas kanan bawah text_tengah text_blok">6=(5-4)</td>
                    <td class="atas kanan bawah text_tengah text_blok">7</td>
                    <td class="atas kanan bawah text_tengah text_blok">8=(7/5)*100</td>
                    <td class="atas kanan bawah text_tengah text_blok">9</td>
                    <td class="atas kanan bawah text_tengah text_blok">10</td>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_label; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    run_download_excel('apbd');
    var _url = window.location.href;
    var url = new URL(_url);
    _url = url.origin+url.pathname+'?key='+url.searchParams.get('key');
    var type = url.searchParams.get("type");
    if(type && type=='pergeseran'){
        var extend_action = '<a class="button button-primary" target="_blank" href="'+_url+'" style="margin-left: 10px;">Print Monev Murni</a>';
    }else{
        var extend_action = '<a class="button button-primary" target="_blank" href="'+_url+'&type=pergeseran" style="margin-left: 10px;">Print Monev Pergeseran/Perubahan</a>';
    }
    jQuery('#action-sipd #excel').after(extend_action);
</script>