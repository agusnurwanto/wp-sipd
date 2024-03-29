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

$input = shortcode_atts( array(
	'id_skpd' => $id_unit,
    'id_jadwal_lokal' => $id_jadwal_lokal,
	'tahun_anggaran' => '2022'
), $atts );

$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
    SELECT 
        j.nama AS nama_jadwal,
        j.tahun_anggaran,
        j.status,
        t.nama_tipe 
    FROM `data_jadwal_lokal` j
    INNER JOIN `data_tipe_perencanaan` t on t.id=j.id_tipe
    WHERE j.id_jadwal_lokal=%d", $id_jadwal_lokal));

$_suffix='';
$where_jadwal='';
if($jadwal_lokal->status == 1){
    $_suffix='_history';
    $where_jadwal=' AND id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
}

$_suffix_sipd='';
if(strpos($jadwal_lokal->nama_tipe, '_sipd') == false){
    $_suffix_sipd = '_lokal';
}

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
}
$nama_pemda = get_option('_crb_daerah');
$nama_excel = 'RENJA TAHUN ANGGARAN '.$input['tahun_anggaran'].' '.strtoupper($nama_pemda);
echo '<div id="cetak" title="'.$nama_excel.'" style="padding: 5px;">';

foreach($data_skpd as $skpd){
    $sql = "
        SELECT 
            *
        FROM data_sub_keg_bl".$_suffix_sipd."".$_suffix."
        WHERE id_sub_skpd=%d
            AND tahun_anggaran=%d
            AND active=1
            ".$where_jadwal."
            ORDER BY kode_giat ASC, kode_sub_giat ASC";
    $subkeg = $wpdb->get_results($wpdb->prepare($sql, $skpd['id_skpd'], $input['tahun_anggaran']), ARRAY_A);

    // nomor urut tahun anggaran RENJA sesuai jadwal tahun awal di RENSTRA
    $urut = 0;
    $nama_skpd = "";
    $nama_sub_skpd = "";
    $data_all = array(
        'total' => 0,
        'pagu_sipd' => 0,
        'total_usulan' => 0,
        'total_n_plus' => 0,
        'total_n_plus_usulan' => 0,
        'data' => array()
    );
    foreach ($subkeg as $kk => $sub) {
        $nama_skpd = $sub['nama_skpd'];
        $kode = explode('.', $sub['kode_sbl']);
        $capaian_prog = $wpdb->get_results($wpdb->prepare("
            select 
                * 
            from data_capaian_prog_sub_keg".$_suffix_sipd."".$_suffix."
            where tahun_anggaran=%d
                and active=1
                and kode_sbl=%s
                ".$where_jadwal."
            order by id ASC
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

        $output_giat = $wpdb->get_results($wpdb->prepare("
            select 
                * 
            from data_output_giat_sub_keg".$_suffix_sipd."".$_suffix."
            where tahun_anggaran=%d
                and active=1
                and kode_sbl=%s
                ".$where_jadwal."
            order by id ASC
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

        $output_sub_giat = $wpdb->get_results($wpdb->prepare("
            select 
                * 
            from data_sub_keg_indikator".$_suffix_sipd."".$_suffix."
            where tahun_anggaran=%d
                and active=1
                and kode_sbl=%s
                ".$where_jadwal."
            order by id DESC
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

        $lokasi_sub_giat = $wpdb->get_results($wpdb->prepare("
            select 
                * 
            from data_lokasi_sub_keg".$_suffix_sipd."".$_suffix."
            where tahun_anggaran=%d
                and active=1
                and kode_sbl=%s
                ".$where_jadwal."
            order by id ASC
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

        $dana_sub_giat = $wpdb->get_results($wpdb->prepare("
            select 
                * 
            from data_dana_sub_keg".$_suffix_sipd."".$_suffix."
            where tahun_anggaran=%d
                and active=1
                and kode_sbl=%s
                ".$where_jadwal."
            order by id ASC
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

        if($sub['kode_bidang_urusan'] == 'X.XX'){
            $urusan_utama_x = explode('.', $sub['kode_sub_skpd']);
            $urusan_utama = $urusan_utama_x[0].'.'.$urusan_utama_x[1];
            $sub['kode_sub_giat'] = str_replace('X.XX', $urusan_utama, $sub['kode_sub_giat']);
            $sub['kode_giat'] = str_replace('X.XX', $urusan_utama, $sub['kode_giat']);
            $sub['kode_program'] = str_replace('X.XX', $urusan_utama, $sub['kode_program']);
            $sub['kode_bidang_urusan'] = str_replace('X.XX', $urusan_utama, $sub['kode_bidang_urusan']);
            $sub['kode_urusan'] = str_replace('X', $urusan_utama_x[0], $sub['kode_urusan']);
        }
        $sub_keg_sipd = $wpdb->get_row($wpdb->prepare("
            select 
                sum(pagu) as pagu
            from data_sub_keg_bl
            where tahun_anggaran=%d
                and active=1
                and kode_sub_giat=%s
                and id_sub_skpd=%d
            order by id ASC
        ", $input['tahun_anggaran'], $sub['kode_sub_giat'], $sub['id_sub_skpd']), ARRAY_A);

        $nama = explode(' ', $sub['nama_sub_giat']);
        $kode_sub_giat = $nama[0];
        $data_renstra = array();
        $data_rpjmd = array();
        $_nama_skpd = $wpdb->get_row($wpdb->prepare("
            select 
                nama_skpd
            from data_unit
            where 
                id_skpd=%d 
                and tahun_anggaran=%d
                and active=1
            order by id ASC
        ", $sub['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
        $nama_skpd = $_nama_skpd['nama_skpd'];
        $nama_sub_skpd = $subkeg[0]['nama_sub_skpd'];
        // die($wpdb->last_query);


        if(empty($data_all['data'][$sub['id_sub_skpd']])){
            $data_all['data'][$sub['id_sub_skpd']] = array(
                'nama'  => $sub['nama_sub_skpd'],
                'nama_skpd'  => $nama_skpd,
                'total' => 0,
                'total_n_plus' => 0,
                'total_usulan' => 0,
                'total_n_plus_usulan' => 0,
                'pagu_sipd' => 0,
                'data'  => array()
            );
        }

        if(empty($data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']])){
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']] = array(
                'nama'  => $sub['nama_urusan'],
                'sub' => $sub,
                'total' => 0,
                'total_n_plus' => 0,
                'total_usulan' => 0,
                'total_n_plus_usulan' => 0,
                'pagu_sipd' => 0,
                'data'  => array()
            );
        }
        if(empty($data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])){
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
                'nama'  => $sub['nama_bidang_urusan'],
                'sub' => $sub,
                'total' => 0,
                'total_n_plus' => 0,
                'total_usulan' => 0,
                'total_n_plus_usulan' => 0,
                'pagu_sipd' => 0,
                'data'  => array()
            );
        }
        if(empty($data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']])){
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']] = array(
                'nama'  => $sub['nama_program'],
                'sub' => $sub,
                'total' => 0,
                'total_n_plus' => 0,
                'total_usulan' => 0,
                'total_n_plus_usulan' => 0,
                'pagu_sipd' => 0,
                'data'  => array()
            );
        }
        if(empty($data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']])){
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']] = array(
                'nama'  => $sub['nama_giat'],
                'sub' => $sub,
                'total' => 0,
                'total_n_plus' => 0,
                'total_usulan' => 0,
                'total_n_plus_usulan' => 0,
                'pagu_sipd' => 0,
                'data'  => array()
            );
        }
        if(empty($data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']])){
            $nama = explode(' ', $sub['nama_sub_giat']);
            unset($nama[0]);
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']] = array(
                'nama'  => implode(' ', $nama),
                'total' => 0,
                'total_n_plus' => 0,
                'total_usulan' => 0,
                'total_n_plus_usulan' => 0,
                'capaian_prog' => $capaian_prog,
                'output_giat' => $output_giat,
                'output_sub_giat' => $output_sub_giat,
                'lokasi_sub_giat' => $lokasi_sub_giat,
                'dana_sub_giat' => $dana_sub_giat,
                'data_renstra' => $data_renstra,
                'data_rpjmd' => $data_rpjmd,
                'pagu_sipd' => 0,
                'data'  => $sub
            );
        }
        $data_all['total'] += $sub['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['total'] += $sub['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total'] += $sub['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total'] += $sub['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total'] += $sub['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total'] += $sub['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total'] += $sub['pagu'];

        $data_all['total_n_plus'] += $sub['pagu_n_depan'];
        $data_all['data'][$sub['id_sub_skpd']]['total_n_plus'] += $sub['pagu_n_depan'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total_n_plus'] += $sub['pagu_n_depan'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_n_plus'] += $sub['pagu_n_depan'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_n_plus'] += $sub['pagu_n_depan'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_n_plus'] += $sub['pagu_n_depan'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_n_plus'] += $sub['pagu_n_depan'];

        if($_suffix_sipd == '_lokal'){
            $data_all['total_usulan'] += $sub['pagu_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['total_usulan'] += $sub['pagu_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total_usulan'] += $sub['pagu_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_usulan'] += $sub['pagu_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_usulan'] += $sub['pagu_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_usulan'] += $sub['pagu_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_usulan'] += $sub['pagu_usulan'];
    
            $data_all['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
            $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
        }

        $data_all['pagu_sipd'] += $sub_keg_sipd['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
    }

    $body = '';
    foreach ($data_all['data'] as $sub_skpd) {
        $body .= '
            <tr tipe="unit">
                <td class="kiri kanan bawah text_blok" colspan="19">Unit Organisasi : '.$sub_skpd['nama_skpd'].'</td>
            </tr>
            <tr tipe="sub_unit">
                <td class="kiri kanan bawah text_blok"></td>
                <td class="kanan bawah text_blok" colspan="12">Sub Unit Organisasi : '.$sub_skpd['nama'].'</td>
                <td class="kanan bawah text_kanan text_blok">'.number_format($sub_skpd['total'],0,",",".").'</td>
                <td class="kanan bawah" colspan="4"></td>
                <td class="kanan bawah text_kanan text_blok">'.number_format($sub_skpd['total_n_plus'],0,",",".").'</td>
            </tr>
        ';
        foreach ($sub_skpd['data'] as $kd_urusan => $urusan) {
            $body .= '
                <tr tipe="urusan" kode="'.$urusan['sub']['kode_sbl'].'">
                    <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
                    <td class="kanan bawah"></td>
                    <td class="kanan bawah"></td>
                    <td class="kanan bawah"></td>
                    <td class="kanan bawah"></td>
                    <td class="kanan bawah text_blok" colspan="14">'.$urusan['nama'].'</td>
                </tr>
            ';
            foreach ($urusan['data'] as $kd_bidang => $bidang) {
                $kd_bidang = explode('.', $kd_bidang);
                $kd_bidang = $kd_bidang[count($kd_bidang)-1];
                $body .= '
                    <tr tipe="bidang" kode="'.$bidang['sub']['kode_sbl'].'">
                        <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
                        <td class="kanan bawah text_blok">'.$kd_bidang.'</td>
                        <td class="kanan bawah"></td>
                        <td class="kanan bawah"></td>
                        <td class="kanan bawah"></td>
                        <td class="kanan bawah text_blok" colspan="8">'.$bidang['nama'].'</td>
                        <td class="kanan bawah text_kanan text_blok">'.number_format($bidang['total'],0,",",".").'</td>
                        <td class="kanan bawah" colspan="4"></td>
                        <td class="kanan bawah text_kanan text_blok">'.number_format($bidang['total_n_plus'],0,",",".").'</td>
                    </tr>
                ';
                foreach ($bidang['data'] as $kd_program => $program) {
                    $kd_program = explode('.', $kd_program);
                    $kd_program = $kd_program[count($kd_program)-1];
                    $data_check_program = explode('.', $program['sub']['kode_sbl']);
                    $data_check_program = $data_check_program[0].'.'.$data_check_program[1].'.'.$data_check_program[2];
                    $body .= '
                        <tr tipe="program" kode="'.$program['sub']['kode_sbl'].'" checkprogram="'.$data_check_program.'">
                            <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
                            <td class="kanan bawah text_blok">'.$kd_bidang.'</td>
                            <td class="kanan bawah text_blok">'.$kd_program.'</td>
                            <td class="kanan bawah"></td>
                            <td class="kanan bawah"></td>
                            <td class="kanan bawah text_blok" colspan="8">'.$program['nama'].'</td>
                            <td class="kanan bawah text_kanan text_blok">'.number_format($program['total'],0,",",".").'</td>
                            <td class="kanan bawah" colspan="4"></td>
                            <td class="kanan bawah text_kanan text_blok">'.number_format($program['total_n_plus'],0,",",".").'</td>
                        </tr>
                    ';
                    foreach ($program['data'] as $kd_giat => $giat) {
                        $kd_giat = explode('.', $kd_giat);
                        $kd_giat = $kd_giat[count($kd_giat)-2].'.'.$kd_giat[count($kd_giat)-1];
                        $body .= '
                            <tr tipe="kegiatan" kode="'.$giat['sub']['kode_sbl'].'">
                                <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
                                <td class="kanan bawah text_blok">'.$kd_bidang.'</td>
                                <td class="kanan bawah text_blok">'.$kd_program.'</td>
                                <td class="kanan bawah text_blok">'.$kd_giat.'</td>
                                <td class="kanan bawah"></td>
                                <td class="kanan bawah" colspan="8">'.$giat['nama'].'</td>
                                <td class="kanan bawah text_blok text_kanan">'.number_format($giat['total'],0,",",".").'</td>
                                <td class="kanan bawah" colspan="4"></td>
                                <td class="kanan bawah text_blok text_kanan">'.number_format($giat['total_n_plus'],0,",",".").'</td>
                            </tr>
                        ';
                        foreach ($giat['data'] as $kd_sub_giat => $sub_giat) {
                            $kode_sub_giat = $kd_sub_giat;
                            $kd_sub_giat = explode('.', $kd_sub_giat);
                            $kd_sub_giat = $kd_sub_giat[count($kd_sub_giat)-1];
                            $capaian_prog = '';
                            $target_capaian_prog = '';
                            if(!empty($sub_giat['capaian_prog'])){
                                $capaian_prog = array();
                                $target_capaian_prog = array();
                                foreach ($sub_giat['capaian_prog'] as $k_sub => $v_sub) {
                                    $capaian_prog[] = ''.$v_sub['capaianteks'].'';
                                    $target_capaian_prog[] = ''.$v_sub['targetcapaianteks'].'';
                                }
                                $capaian_prog = implode('<br>', $capaian_prog);
                                $target_capaian_prog = implode('<br>', $target_capaian_prog);
                            }
                            $output_giat = '';
                            $target_output_giat = '';
                            if(!empty($sub_giat['output_giat'])){
                                $output_giat = array();
                                $target_output_giat = array();
                                foreach ($sub_giat['output_giat'] as $k_sub => $v_sub) {
                                    $output_giat[] = ''.$v_sub['outputteks'].'';
                                    $target_output_giat[] = ''.$v_sub['targetoutputteks'].'';
                                }
                                $output_giat = implode('<br>', $output_giat);
                                $target_output_giat = implode('<br>', $target_output_giat);
                            }
                            $output_sub_giat = '';
                            $target_output_sub_giat = '';
                            if(!empty($sub_giat['output_sub_giat'])){
                                $output_sub_giat = array();
                                $target_output_sub_giat = array();
                                foreach ($sub_giat['output_sub_giat'] as $k_sub => $v_sub) {
                                    $output_sub_giat[] = $v_sub['outputteks'];
                                    $target_output_sub_giat[] = ''.$v_sub['targetoutputteks'].'';
                                }
                                $output_sub_giat = implode('<br>', $output_sub_giat);
                                $target_output_sub_giat = implode('<br>', $target_output_sub_giat);
                            }

                            // get lokasi sub kegiatan
                            $lokasi_sub_giat_array = array();
                            if(!empty($sub_giat['lokasi_sub_giat'])){
                                foreach($sub_giat['lokasi_sub_giat'] as $v_lokasi){
                                    $lokasi_sub_giat = $v_lokasi['daerahteks'];
                                    if(!empty($v_lokasi['camatteks'])){
                                        $lokasi_sub_giat .= ', Kec. '.$v_lokasi['camatteks'];
                                    }
                                    if(!empty($v_lokasi['lurahteks'])){
                                        $lokasi_sub_giat .= ', '.$v_lokasi['lurahteks'];
                                    }
                                    $lokasi_sub_giat_array[] = $lokasi_sub_giat;
                                }
                            }
                            $lokasi_sub_giat = implode('<br>', $lokasi_sub_giat_array);

                            // get sumber dana sub kegiatan
                            $dana_sub_giat_array = array();
                            if(!empty($sub_giat['dana_sub_giat'])){
                                foreach($sub_giat['dana_sub_giat'] as $v_dana){
                                    if(!empty($v_dana['namadana'])){
                                        $dana_sub_giat = explode('] - ', $v_dana['namadana']);
                                        if(!empty($dana_sub_giat[1])){
                                            $dana_sub_giat_array[] = $dana_sub_giat[1];
                                        }else{
                                            $dana_sub_giat_array[] = $v_dana['namadana'];
                                        }
                                    }
                                }
                            }
                            $dana_sub_giat = implode('<br>', $dana_sub_giat_array);

                            $catatan = ($_suffix_sipd == '_lokal') ? ''.$sub_giat['data']['catatan'].'' : '';
                            $ind_n_plus = '';
                            $target_ind_n_plus = '';
                            /*
                            if(!empty($sub_giat['data_renstra'])){
                                $ind_n_plus = $sub_giat['data_renstra'][0]['indikator_sub'];
                                if($urut<=5){
                                    $target_ind_n_plus = $sub_giat['data_renstra'][0]['target_sub_'.($urut+1)];
                                }
                            }
                            */
                            if(!empty($sub_giat['data_rpjmd'])){
                                $ind_n_plus = $sub_giat['data_rpjmd'][0]['indikator'];
                                if($urut<=5){
                                    $target_ind_n_plus = $sub_giat['data_rpjmd'][0]['target_'.($urut+1)];
                                }
                            }
                            $kode_sbl = $sub_giat['data']['kode_sbl'];
                            $body .= '
                                <tr tipe="sub-kegiatan" kode="'.$kode_sbl.'">
                                    <td class="kiri kanan bawah">'.$kd_urusan.'</td>
                                    <td class="kanan bawah">'.$kd_bidang.'</td>
                                    <td class="kanan bawah">'.$kd_program.'</td>
                                    <td class="kanan bawah">'.$kd_giat.'</td>
                                    <td class="kanan bawah">'.$kd_sub_giat.'</td>
                                    <td class="kanan bawah">'.$sub_giat['nama'].'</td>
                                    <td class="kanan bawah">'.$capaian_prog.'</td>
                                    <td class="kanan bawah">'.$output_sub_giat.'</td>
                                    <td class="kanan bawah">'.$output_giat.'</td>
                                    <td class="kanan bawah">'.$lokasi_sub_giat.'</td>
                                    <td class="kanan bawah">'.$target_capaian_prog.'</td>
                                    <td class="kanan bawah">'.$target_output_sub_giat.'</td>
                                    <td class="kanan bawah">'.$target_output_giat.'</td>
                                    <td class="kanan bawah text_kanan">'.number_format($sub_giat['total'],0,",",".").'</td>
                                    <td class="kanan bawah">'.$dana_sub_giat.'</td>
                                    <td class="kanan bawah">'.$catatan.'</td>
                                    <td class="kanan bawah">'.$ind_n_plus.'</td>
                                    <td class="kanan bawah">'.$target_ind_n_plus.'</td>
                                    <td class="kanan bawah text_kanan">'.number_format($sub_giat['total_n_plus'],0,",",".").'</td>
                                </tr>
                            ';
                        }
                    }
                }
            }
        }
    }

    $nama_laporan = 'RENJA '.strtoupper($nama_sub_skpd).'<br>TAHUN ANGGARAN '.$input['tahun_anggaran'].' '.strtoupper($nama_pemda);
    echo '
    <button type="button" style="background-color:#FFD670; text-align: center; margin: 10px auto; display: block;" class="btn">Laporan Jadwal '.$jadwal_lokal->nama_jadwal.'</button>
    <h4 style="text-align: center; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">'.$nama_laporan.'</h4>
    <div id="wrap-table">
        <table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width: 1800px; table-layout: fixed; overflow-wrap: break-word; font-size: 60%; border: 0;">
            <thead>
                <tr>
                    <th style="padding: 0; border: 0; width:25px"></th>
                    <th style="padding: 0; border: 0; width:30px"></th>
                    <th style="padding: 0; border: 0; width:30px"></th>
                    <th style="padding: 0; border: 0; width:35px"></th>
                    <th style="padding: 0; border: 0; width:30px"></th>
                    <th style="padding: 0; border: 0;"></th>
                    <th style="padding: 0; border: 0; width:7.5%"></th>
                    <th style="padding: 0; border: 0; width:7.5%"></th>
                    <th style="padding: 0; border: 0; width:7.5%"></th>
                    <th style="padding: 0; border: 0; width:4%"></th>
                    <th style="padding: 0; border: 0; width:3.5%"></th>
                    <th style="padding: 0; border: 0; width:3.5%"></th>
                    <th style="padding: 0; border: 0; width:3.5%"></th>
                    <th style="padding: 0; border: 0; width:5.5%"></th>
                    <th style="padding: 0; border: 0; width:6.5%"></th>
                    <th style="padding: 0; border: 0; width:4%"></th>
                    <th style="padding: 0; border: 0; width:3.5%"></th>
                    <th style="padding: 0; border: 0; width:3.5%"></th>
                    <th style="padding: 0; border: 0; width:5.5%"></th>
                </tr>
                <tr>
                    <td class="atas kanan bawah kiri text_tengah text_blok" colspan="5" rowspan="3">Kode</td>
                    <td class="atas kanan bawah text_tengah text_blok" rowspan="3">Urusan/ Bidang Urusan Pemerintahan Daerah Dan Program/ Kegiatan</td>
                    <td class="atas kanan bawah text_tengah text_blok" colspan="3">Indikator Kinerja</td>
                    <td class="atas kanan bawah text_tengah text_blok" colspan="6">Rencana Tahun '.$input['tahun_anggaran'].'</td>
                    <td class="atas kanan bawah text_tengah text_blok" rowspan="3">Catatan Penting</td>
                    <td class="atas kanan bawah text_tengah text_blok" colspan="3">Prakiraan Maju Rencana Tahun '.($input['tahun_anggaran']+1).'</td>
                </tr>
                <tr>
                    <td class="kanan bawah text_tengah text_blok" rowspan="2">Capaian Program</td>
                    <td class="kanan bawah text_tengah text_blok" rowspan="2">Keluaran Sub Kegiatan</td>
                    <td class="kanan bawah text_tengah text_blok" rowspan="2">Hasil Kegiatan</td>
                    <td class="kanan bawah text_tengah text_blok" rowspan="2">Lokasi Output Kegiatan</td>
                    <td class="kanan bawah text_tengah text_blok" colspan="3">Target Capaian Kinerja</td>
                    <td class="kanan bawah text_tengah text_blok" rowspan="2">Pagu Indikatif (Rp.)</td>
                    <td class="kanan bawah text_tengah text_blok" rowspan="2">Sumber Dana</td>
                    <td class="kanan bawah text_tengah text_blok" colspan="2">Target Capaian Kinerja</td>
                    <td class="kanan bawah text_tengah text_blok" rowspan="2">Kebutuhan Dana/<br/>Pagu Indikatif (Rp.)</td>
                </tr>
                <tr>
                    <td class="kanan bawah text_tengah text_blok">Program</td>
                    <td class="kanan bawah text_tengah text_blok">Keluaran Sub Kegiatan</td>
                    <td class="kanan bawah text_tengah text_blok">Hasil Kegiatan</td>
                    <td class="kanan bawah text_tengah text_blok">Tolok Ukur</td>
                    <td class="kanan bawah text_tengah text_blok">Target</td>
                </tr>
            </thead>
            <tbody>
                '.$body.'
                <tr>
                    <td class="kiri kanan bawah text_blok text_kanan" colspan="13">TOTAL</td>
                    <td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($data_all['total'],0,",",".").'</span></td>
                    <td class="kanan bawah" colspan="4"></td>
                    <td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($data_all['total_n_plus'],0,",",".").'</span></td>
                </tr>
            </tbody>
        </table>
    </div>';
};
echo '</div>';
?>
<style type="text/css">
    #wrap-table {
        overflow: auto;
        height: 100vh;
    }
    @media  print {
        #wrap-table {
            overflow: none;
            height: auto;
        }
    }
</style>
<script type="text/javascript">
    jQuery(document).ready(function(){
        run_download_excel();
    });
</script>