<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$id_unit = '';
if(!empty($_GET) && !empty($_GET['id_unit'])){
    $id_unit = $_GET['id_unit'];
}

$id_jadwal_lokal = '';
if(!empty($_GET) && !empty($_GET['id_jadwal_lokal'])){
    $id_jadwal_lokal = $_GET['id_jadwal_lokal'];
}

$id_jadwal_rpjm = '';
if(!empty($_GET) && !empty($_GET['id_jadwal_rpjm'])){
    $id_jadwal_rpjm = $_GET['id_jadwal_rpjm'];
}

$input = shortcode_atts( array(
	'id_skpd' => $id_unit,
    'id_jadwal_lokal' => $id_jadwal_lokal,
	'tahun_anggaran' => '2023'
), $atts );

// $skpd = $wpdb->get_results($wpdb->prepare("
//     select 
//         * 
//     from data_unit 
//     where tahun_anggaran=%d
//         and active=1
//         and id_skpd=%d
//     order by id_skpd ASC
// ", $input['tahun_anggaran']), ARRAY_A);

$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
    SELECT 
        j.nama AS nama_jadwal,
        j.tahun_anggaran,
        j.status,
        t.nama_tipe 
    FROM `data_jadwal_lokal` j
    INNER JOIN `data_tipe_perencanaan` t on t.id=j.id_tipe 
    WHERE j.id_jadwal_lokal=%d", $id_jadwal_lokal));

$jadwal_rpjm = $wpdb->get_row($wpdb->prepare("
    SELECT 
        j.nama AS nama_jadwal,
        j.tahun_anggaran,
        j.status,
        j.lama_pelaksanaan,
        t.nama_tipe 
    FROM `data_jadwal_lokal` j
    INNER JOIN `data_tipe_perencanaan` t on t.id=j.id_tipe 
    WHERE j.id_jadwal_lokal=%d", $id_jadwal_rpjm));

$_suffix='';
$where_jadwal='';
if($jadwal_lokal->status == 1){
    $_suffix='_history';
    $where_jadwal=' AND s.id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
}
$input['tahun_anggaran'] = $jadwal_lokal->tahun_anggaran;

$_suffix_sipd='';
if(strpos($jadwal_lokal->nama_tipe, '_sipd') == false){
    $_suffix_sipd = '_lokal';
}

$_suffix_rpjmd='';
$where_jadwal_rpjm='';
if($jadwal_rpjm->status == 1){
    $_suffix_rpjmd='_history';
    $where_jadwal_rpjm=' AND id_jadwal='.$wpdb->prepare("%d", $id_jadwal_rpjm);
}
$input['tahun_anggaran_rpjm'] = $jadwal_rpjm->tahun_anggaran;

$_suffix_sipd_rpjm='';
$where_tahun_anggaran_rpjm='';
if(strpos($jadwal_rpjm->nama_tipe, '_sipd') == false){
    $_suffix_sipd_rpjm = '_lokal';
}else{
    $where_tahun_anggaran_rpjm = 'a.tahun_anggaran='.$input['tahun_anggaran_rpjm'].'  AND';
}

$tahun_rpjm_start = $jadwal_rpjm->tahun_anggaran;
$tahun_rpjm_end = $jadwal_rpjm->tahun_anggaran + $jadwal_rpjm->lama_pelaksanaan;

if($jadwal_lokal->tahun_anggaran < $tahun_rpjm_start || $jadwal_lokal->tahun_anggaran > $tahun_rpjm_end){
    die('Jadwal APBD tidak berada di rentang tahun RPJM!');
}

$tahun_berjalan = 1;
for ($i=$tahun_rpjm_start; $i <= $tahun_rpjm_end; $i++) { 
    if($jadwal_lokal->tahun_anggaran==$i){
        break;
    }
    $tahun_berjalan++;
}

$nama_skpd = '';
if($input['id_skpd'] == 'all'){
    $data_skpd = $wpdb->get_results($wpdb->prepare("
        select 
            id_skpd 
        from data_unit
        where tahun_anggaran=%d
            and active=1
        order by kode_skpd ASC
    ", $input['tahun_anggaran']), ARRAY_A);
}else{
    $data_skpd = array(array('id_skpd' => $input['id_skpd']));
    $nama_skpd = $wpdb->get_var($wpdb->prepare("
        SELECT 
            CONCAT(kode_skpd, ' ',nama_skpd)
        FROM data_unit
        WHERE tahun_anggaran=%d
            and active=1
            and id_skpd=%d
    ", $input['tahun_anggaran'], $input['id_skpd']));
    if(!empty($nama_skpd)){
        $nama_skpd = '<br>'.$nama_skpd;
    }
}
$nama_pemda = get_option('_crb_daerah');
$nama_excel = 'LAPORAN KONSISTENSI RPJM - RKPD<br>TAHUN ANGGARAN '.$input['tahun_anggaran'].'<br>'.strtoupper($nama_pemda).$nama_skpd;

$body = '';
$no_program = 0;
$total_all = 0;
$data_all = array(
    'jml_sub_keg_rpjm' => 0,
    'total_rpjm' => 0,
    'jml_sub_keg_rkpd' => 0,
    'total_rkpd' => 0,
    'jml_sub_keg_pkua_ppas' => 0,
    'total_pkua_ppas' => 0,
    'jml_sub_keg_papbd' => 0,
    'total_papbd' => 0,
    'data' => array()
);
foreach($data_skpd as $skpd){
    $sql = "
        SELECT 
            s.*,
            u.nama_skpd as nama_skpd_asli,
            u.kode_skpd as kode_skpd_asli
        FROM data_sub_keg_bl".$_suffix_sipd."".$_suffix." s
        INNER JOIN data_unit u on s.id_skpd=u.id_skpd
            AND u.active=s.active
            AND u.tahun_anggaran=s.tahun_anggaran
        WHERE s.id_sub_skpd=%d
            AND s.tahun_anggaran=%d
            AND s.active=1
            ".$where_jadwal."
            ORDER BY kode_giat ASC, kode_sub_giat ASC";
    $subkeg = $wpdb->get_results($wpdb->prepare($sql, $skpd['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
    // die($wpdb->last_query);

    foreach ($subkeg as $kk => $sub) {
        if(empty($data_all['data'][$sub['kode_urusan']])){
            $data_all['data'][$sub['kode_urusan']] = array(
                'nama'  => $sub['nama_urusan'],
                'jml_sub_keg_rpjm' => 0,
                'total_rpjm' => 0,
                'jml_sub_keg_rkpd' => 0,
                'total_rkpd' => 0,
                'jml_sub_keg_pkua_ppas' => 0,
                'total_pkua_ppas' => 0,
                'jml_sub_keg_papbd' => 0,
                'total_papbd' => 0,
                'data'  => array()
            );
        }
        if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])){
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
                'nama'  => $sub['nama_bidang_urusan'],
                'jml_sub_keg_rpjm' => 0,
                'total_rpjm' => 0,
                'jml_sub_keg_rkpd' => 0,
                'total_rkpd' => 0,
                'jml_sub_keg_pkua_ppas' => 0,
                'total_pkua_ppas' => 0,
                'jml_sub_keg_papbd' => 0,
                'total_papbd' => 0,
                'data'  => array()
            );
        }
        if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']])){
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']] = array(
                'nama'  => $sub['kode_skpd_asli'].' '.$sub['nama_skpd_asli'],
                'jml_sub_keg_rpjm' => 0,
                'total_rpjm' => 0,
                'jml_sub_keg_rkpd' => 0,
                'total_rkpd' => 0,
                'jml_sub_keg_pkua_ppas' => 0,
                'total_pkua_ppas' => 0,
                'jml_sub_keg_papbd' => 0,
                'total_papbd' => 0,
                'data'  => array()
            );
        }
        if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']])){
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']] = array(
                'nama'  => $sub['kode_sub_skpd'].' '.$sub['nama_sub_skpd'],
                'jml_sub_keg_rpjm' => 0,
                'total_rpjm' => 0,
                'jml_sub_keg_rkpd' => 0,
                'total_rkpd' => 0,
                'jml_sub_keg_pkua_ppas' => 0,
                'total_pkua_ppas' => 0,
                'jml_sub_keg_papbd' => 0,
                'total_papbd' => 0,
                'data'  => array()
            );
        }
        if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']])){
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']] = array(
                'nama'  => $sub['nama_program'],
                'jml_sub_keg_rpjm' => 0,
                'total_rpjm' => 0,
                'jml_sub_keg_rkpd' => 0,
                'total_rkpd' => 0,
                'jml_sub_keg_pkua_ppas' => 0,
                'total_pkua_ppas' => 0,
                'jml_sub_keg_papbd' => 0,
                'total_papbd' => 0,
                'data'  => array(),
                'data_program_rpjm' => array()
            );

            $dataRpjm = $wpdb->get_results($wpdb->prepare("
                    SELECT 
                        a.id_unik,
                        a.id_unik_indikator,
                        a.id_program, 
                        a.nama_program, 
                        a.indikator,
                        a.satuan,
                        a.target_".$tahun_berjalan.",
                        a.pagu_".$tahun_berjalan." 
                    FROM 
                        data_rpjmd_program".$_suffix_sipd."".$_suffix." a INNER JOIN data_prog_keg b
                            ON a.id_program=b.id_program AND b.tahun_anggaran=%d 
                        WHERE 
                            a.id_program=%d AND
                            ".$where_tahun_anggaran_rpjm."
                            a.active=%d AND
                            a.id_unik_indikator IS NOT NULL
                            ".$where_jadwal_rpjm."
                    ", $input['tahun_anggaran_rpjm'], $sub['id_program'], 1), ARRAY_A);

            if(!empty($dataRpjm)){
                foreach ($dataRpjm as $key => $rpjm) {
                    if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data_program_rpjm'][$rpjm['kode_program']])){
                        $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data_program_rpjm'][$rpjm['kode_program']] = [
                            'kode_program' => $rpjm['kode_program'],
                            'nama_program' => $rpjm['nama_program'],
                            'data_indikator_program_rpjm' => array(),
                        ];

                        if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data_program_rpjm'][$rpjm['kode_program']]['data_indikator_program_rpjm'][$rpjm['id_unik_indikator']])){

                            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data_program_rpjm'][$rpjm['kode_program']]['data_indikator_program_rpjm'][$rpjm['id_unik_indikator']] = [
                                'indikator' => $rpjm['indikator'],
                                'target_'.$tahun_berjalan => $rpjm['target_'.$tahun_berjalan],
                                'satuan' => $rpjm['satuan'],
                                'pagu_'.$tahun_berjalan => $rpjm['pagu_'.$tahun_berjalan],
                            ];
                        }
                    }
                }
            }
        }
        if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']])){
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']] = array(
                'nama'  => $sub['nama_giat'],
                'jml_sub_keg_rpjm' => 0,
                'total_rpjm' => 0,
                'jml_sub_keg_rkpd' => 0,
                'total_rkpd' => 0,
                'jml_sub_keg_pkua_ppas' => 0,
                'total_pkua_ppas' => 0,
                'jml_sub_keg_papbd' => 0,
                'total_papbd' => 0,
                'data'  => array()
            );
        }
        if(empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']])){
            $nama = explode(' ', $sub['nama_sub_giat']);
            unset($nama[0]);
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']] = array(
                'nama'  => implode(' ', $nama),
                'jml_sub_keg_rpjm' => 0,
                'total_rpjm' => 0,
                'jml_sub_keg_rkpd' => 0,
                'total_rkpd' => 0,
                'jml_sub_keg_pkua_ppas' => 0,
                'total_pkua_ppas' => 0,
                'jml_sub_keg_papbd' => 0,
                'total_papbd' => 0,
                'data'  => $sub
            );

            $data_all['jml_sub_keg_rkpd']++;
            $data_all['data'][$sub['kode_urusan']]['jml_sub_keg_rkpd']++;
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['jml_sub_keg_rkpd']++;
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['jml_sub_keg_rkpd']++;
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['jml_sub_keg_rkpd']++;
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['jml_sub_keg_rkpd']++;
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['jml_sub_keg_rkpd']++;
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['jml_sub_keg_rkpd']++;
        }

        $total_pagu = $sub['pagu'];
        $data_all['total_rkpd'] += $total_pagu;
        $data_all['data'][$sub['kode_urusan']]['total_rkpd'] += $total_pagu;
        $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_rkpd'] += $total_pagu;
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['total_rkpd'] += $total_pagu;
            $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['total_rkpd'] += $total_pagu;
        $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['total_rkpd'] += $total_pagu;
        $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_rkpd'] += $total_pagu;
        $data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['id_skpd']]['data'][$sub['id_sub_skpd']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_rkpd'] += $total_pagu;
    }
}

foreach($data_all['data'] as $urusan){
    // urusan
    $body .= '
        <tr>
            <td class="text-left kiri kanan bawah atas" colspan="10"><medium><b>'.$urusan['nama'].'</b></medium></td>
            <!-- <td class="text-left kiri kanan bawah atas" colspan="10"></td> -->
        </tr>
    ';

    foreach($urusan['data'] as $bidang_urusan){
        $body .= '
            <tr>
                <td class="text-left kiri kanan bawah atas">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah"><b>'.$bidang_urusan['nama'].'</b></td>
                <td class="text-right kanan bawah">'.number_format($bidang_urusan['total_rkpd'],0,",",".").'</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <!-- <td class="text-left kanan bawah"></td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td>
                <td class="text-left kanan bawah">&nbsp</td> -->
            </tr>
        ';

        foreach($bidang_urusan['data'] as $id_skpd => $skpd){
            $body .= '
                <tr id-skpd="'.$id_skpd.'">
                    <td class="text-left kiri kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah"><b>'.$skpd['nama'].'</b></td>
                    <td class="text-right kanan bawah">'.number_format($skpd['total_rkpd'],0,",",".").'</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kiri kanan bawah">&nbsp</td>
                    <!-- <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td>
                    <td class="text-left kanan bawah">&nbsp</td> -->
                </tr>
            ';

            foreach($skpd['data'] as $id_sub_skpd => $sub_skpd){
                $body .= '
                    <tr id-sub-skpd="'.$id_sub_skpd.'">
                        <td class="text-left kiri kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah atas"><b>'.$sub_skpd['nama'].'</b></td>
                        <td class="text-right kanan bawah">'.number_format($sub_skpd['total_rkpd'],0,",",".").'</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah atas">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <!-- <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td>
                        <td class="text-left kanan bawah">&nbsp</td> -->
                    </tr>
                ';

                foreach($sub_skpd['data'] as $program){
                    $no_program++;
                    $indikator_program = '';
                    $text_program_rpjm=[];
                    $text_indikator_program_rpjm=[];
                    $target_indikator_program_rpjm=[];
                    $pagu_indikator_program_rpjm=0;
                    if(!empty($program['data_program_rpjm'])){
                        foreach ($program['data_program_rpjm'] as $program_rpjm) {
                            $text_program_rpjm[]='<div class="indikator_program">'.$program_rpjm['nama_program'].'</div>';
                            foreach ($program_rpjm['data_indikator_program_rpjm'] as $indikator_rpjm) {
                                $text_indikator_program_rpjm[]='<div class="indikator_program">'.$indikator_rpjm['indikator'].'</div>';
                                $target_indikator_program_rpjm[]='<div class="indikator_program">'.$indikator_rpjm['target_'.$tahun_berjalan].'</div>';
                                $pagu_indikator_program_rpjm+=$indikator_rpjm['pagu_'.$tahun_berjalan];
                            }
                        }
                    }

                    $text_program_rpjm = implode('', $text_program_rpjm);
                    $text_indikator_program_rpjm = implode('', $text_indikator_program_rpjm);
                    $target_indikator_program_rpjm = implode('', $target_indikator_program_rpjm);

                    $body .= '
                        <tr>
                            <td class="text-center kiri kanan bawah">'.$no_program.'</td>
                            <td class="kanan bawah">'.$text_program_rpjm.'</td>
                            <td class="text-left kanan bawah">'.$pagu_indikator_program_rpjm.'</td>
                            <td class="text-left kanan bawah">'.$text_indikator_program_rpjm.'</td>
                            <td class="text-left kanan bawah">'.$target_indikator_program_rpjm.'</td>
                            <td class="kanan bawah"><b>'.$program['nama'].'</b></td>
                            <td class="text-right kanan bawah">'.number_format($program['total_rkpd'],0,",",".").'</td>
                            <td class="kanan bawah">'.$indikator_program.'</td>
                            <td class="text-left kanan bawah">&nbsp</td>
                            <td class="text-left kanan bawah atas">&nbsp</td>
                            <!-- <td class="kanan bawah"></td>
                            <td class="text-left kanan bawah">&nbsp</td>
                            <td class="text-left kanan bawah">&nbsp</td>
                            <td class="text-left kanan bawah">&nbsp</td>
                            <td class="text-left kanan bawah">&nbsp</td>
                            <td class="kanan bawah"></td>
                            <td class="text-left kanan bawah">&nbsp</td>
                            <td class="text-left kanan bawah">&nbsp</td>
                            <td class="text-left kanan bawah">&nbsp</td>
                            <td class="text-left kanan bawah">&nbsp</td> -->
                        </tr>
                    ';

                    foreach($program['data'] as $kegiatan){
                        $body .= '
                            <tr>
                                <td class="text-left kiri kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah atas">'.$kegiatan['nama'].'</td>
                                <td class="text-right kanan bawah">'.number_format($kegiatan['total_rkpd'],0,",",".").'</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <!-- <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td>
                                <td class="text-left kanan bawah">&nbsp</td> -->
                            </tr>
                        ';
                        foreach($kegiatan['data'] as $sub_giat){
                            $indikator_sub = '';
                            $body .= '
                                <tr data-kodesbl="'.$sub_giat['data']['kode_sbl'].'">
                                    <td class="text-left kiri kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah atas"><i>'.$sub_giat['nama'].'<i></td>
                                    <td class="text-right kanan bawah">'.number_format($sub_giat['total_rkpd'],0,",",".").'</td>
                                    <td class="text-left kanan bawah atas">'.$indikator_sub.'</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <!-- <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td>
                                    <td class="text-left kanan bawah">&nbsp</td> -->
                                </tr>
                            ';
                        }
                    }
                }
            }
        }
    }
}
?>
    <h4 style="text-align: center; margin: 0; font-weight: bold;"><?php echo $nama_excel; ?></h4>
        <table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word; font-size: 70%; border: 0; margin-top: 20px;">
        <thead>
            <tr>
                <th class="text-center kiri kanan bawah atas" rowspan="2">No</th>
                <th class="text-center kanan bawah atas" colspan="4">RPJMD</th>
                <th class="text-center kanan bawah atas" colspan="5">RKPD</th>
                <!-- <th class="text-center kanan bawah atas" colspan="5">PERUBAHAN KUA PPAS TA. 2022</th>
                <th class="text-center kanan bawah atas" colspan="5">RAPERDA P-APBD TA. 2022</th> -->
            </tr>
            <tr>
                <th class="text-center kanan bawah">Urusan</th>
                <th class="text-center kanan bawah">Pagu</th>
                <th class="text-center kanan bawah">Indikator</th>
                <th class="text-center kanan bawah">Target</th>
                <th class="text-center kanan bawah">Urusan</th>
                <th class="text-center kanan bawah">Pagu</th>
                <th class="text-center kanan bawah">Indikator</th>
                <th class="text-center kanan bawah">Target</th>
                <th class="text-center kanan bawah">Lokasi</th>
                <!-- <th class="text-center kanan bawah">Urusan</th>
                <th class="text-center kanan bawah">Pagu</th>
                <th class="text-center kanan bawah">Indikator</th>
                <th class="text-center kanan bawah">Target</th>
                <th class="text-center kanan bawah">Lokasi</th>
                <th class="text-center kanan bawah">Urusan</th>
                <th class="text-center kanan bawah">Pagu</th>
                <th class="text-center kanan bawah">Indikator</th>
                <th class="text-center kanan bawah">Target</th>
                <th class="text-center kanan bawah">Lokasi</th> -->
            </tr>
            <tr>
                <th class="text-center kiri kanan bawah">1</th>
                <th class="text-center kanan bawah">2</th>
                <th class="text-center kanan bawah">3</th>
                <th class="text-center kanan bawah">4</th>
                <th class="text-center kanan bawah">5</th>
                <th class="text-center kanan bawah">6</th>
                <th class="text-center kanan bawah">7</th>  
                <th class="text-center kanan bawah">8</th>
                <th class="text-center kanan bawah">9</th>
                <th class="text-center kanan bawah">10</th>
                <!-- <th class="text-center kanan bawah">11</th>
                <th class="text-center kanan bawah">12</th>
                <th class="text-center kanan bawah">13</th>
                <th class="text-center kanan bawah">14</th>
                <th class="text-center kanan bawah">15</th>
                <th class="text-center kanan bawah">16</th>
                <th class="text-center kanan bawah">17</th>
                <th class="text-center kanan bawah">18</th>
                <th class="text-center kanan bawah">19</th>
                <th class="text-center kanan bawah">20</th> -->
            </tr>
            <tr>
            </tr>
        </thead>
        <tbody>
            <?php echo $body; ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        run_download_excel();
    });
</script>