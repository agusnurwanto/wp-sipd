<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );
$nama_pemda = get_option('_crb_daerah');

$sql = "
    SELECT 
        *
    FROM data_sub_keg_bl
    WHERE id_sub_skpd=%d
        AND tahun_anggaran=%d";
$subkeg = $wpdb->get_results($wpdb->prepare($sql,$input['id_skpd'], $input['tahun_anggaran']), ARRAY_A);

$jadwal_lokal = $wpdb->get_results($wpdb->prepare("SELECT * from data_jadwal_lokal where id_jadwal_lokal = (select max(id_jadwal_lokal) from data_jadwal_lokal where id_tipe=%d)",5), ARRAY_A);
if(!empty($jadwal_lokal)){
	$tahun_anggaran = $jadwal_lokal[0]['tahun_anggaran'];
	$namaJadwal = $jadwal_lokal[0]['nama'];
	$mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
	$selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];
}else{
	$tahun_anggaran = '2022';
	$namaJadwal = '-';
	$mulaiJadwal = '-';
	$selesaiJadwal = '-';
}

$timezone = get_option('timezone_string');

$nama_skpd = "";
$data_all = array(
    'total' => 0,
    'total_n_plus' => 0,
    'data' => array()
);
foreach ($subkeg as $kk => $sub) {
    $nama_skpd = $sub['nama_skpd'];
    $kode = explode('.', $sub['kode_sbl']);
    $capaian_prog = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_capaian_prog_sub_keg 
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
            and capaianteks != ''
        order by id ASC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

    $output_giat = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_output_giat_sub_keg 
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
        order by id ASC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

    $output_sub_giat = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_sub_keg_indikator
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
        order by id DESC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

    $lokasi_sub_giat = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_lokasi_sub_keg
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
        order by id ASC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

    $nama = explode(' ', $sub['nama_sub_giat']);
    $kode_sub_giat = $nama[0];
    $data_renstra = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_renstra
        where tahun_anggaran=%d
            and active=1
            and kode_sub_giat=%s
            and id_unit=%s
        order by id ASC
    ", $input['tahun_anggaran'], $kode_sub_giat, $sub['id_skpd']), ARRAY_A);
    $data_rpjmd = array();
    if(!empty($data_renstra)){
        $data_rpjmd = $wpdb->get_results($wpdb->prepare("
            select 
                * 
            from data_rpjmd
            where 
                id_rpjmd=%d 
                and tahun_anggaran=%d
            order by id ASC
        ", $data_renstra[0]['id_rpjmd'], $input['tahun_anggaran']), ARRAY_A);
        $nama_skpd = $data_renstra[0]['nama_skpd'];
    }else{
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
    }
    // die($wpdb->last_query);


    if(empty($data_all['data'][$sub['id_sub_skpd']])){
        $data_all['data'][$sub['id_sub_skpd']] = array(
            'nama'  => $sub['nama_sub_skpd'],
            'nama_skpd'  => $nama_skpd,
            'total' => 0,
            'total_n_plus' => 0,
            'data'  => array()
        );
    }

    if(empty($data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']])){
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']] = array(
            'nama'  => $sub['nama_urusan'],
            'total' => 0,
            'total_n_plus' => 0,
            'data'  => array()
        );
    }
    if(empty($data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])){
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
            'nama'  => $sub['nama_bidang_urusan'],
            'total' => 0,
            'total_n_plus' => 0,
            'data'  => array()
        );
    }
    if(empty($data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']])){
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']] = array(
            'nama'  => $sub['nama_program'],
            'total' => 0,
            'total_n_plus' => 0,
            'data'  => array()
        );
    }
    if(empty($data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']])){
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']] = array(
            'nama'  => $sub['nama_giat'],
            'total' => 0,
            'total_n_plus' => 0,
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
            'capaian_prog' => $capaian_prog,
            'output_giat' => $output_giat,
            'output_sub_giat' => $output_sub_giat,
            'lokasi_sub_giat' => $lokasi_sub_giat,
            'data_renstra' => $data_renstra,
            'data_rpjmd' => $data_rpjmd,
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
}

$body = '';
    foreach ($data_all['data'] as $sub_skpd) {
        $body .= '
            <tr>
                <td class="kiri kanan bawah text_blok" colspan="19">Unit Organisasi : '.$sub_skpd['nama_skpd'].'</td>
            </tr>
            <tr>
                <td class="kiri kanan bawah text_blok"></td>
                <td class="kanan bawah text_blok" colspan="12">Sub Unit Organisasi : '.$sub_skpd['nama'].'</td>
                <td class="kanan bawah text_kanan text_blok">'.number_format($sub_skpd['total'],0,",",".").'</td>
                <td class="kanan bawah" colspan="4">&nbsp;</td>
                <td class="kanan bawah text_kanan text_blok">'.number_format($sub_skpd['total_n_plus'],0,",",".").'</td>
            </tr>
        ';
        foreach ($sub_skpd['data'] as $kd_urusan => $urusan) {
            $body .= '
                <tr>
                    <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah">&nbsp;</td>
                    <td class="kanan bawah text_blok" colspan="14">'.$urusan['nama'].'</td>
                </tr>
            ';
            foreach ($urusan['data'] as $kd_bidang => $bidang) {
                $kd_bidang = explode('.', $kd_bidang);
                $kd_bidang = $kd_bidang[count($kd_bidang)-1];
                $body .= '
                    <tr>
                        <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
                        <td class="kanan bawah text_blok">'.$kd_bidang.'</td>
                        <td class="kanan bawah">&nbsp;</td>
                        <td class="kanan bawah">&nbsp;</td>
                        <td class="kanan bawah">&nbsp;</td>
                        <td class="kanan bawah text_blok" colspan="8">'.$bidang['nama'].'</td>
                        <td class="kanan bawah text_kanan text_blok">'.number_format($bidang['total'],0,",",".").'</td>
                        <td class="kanan bawah" colspan="4">&nbsp;</td>
                        <td class="kanan bawah text_kanan text_blok">'.number_format($bidang['total_n_plus'],0,",",".").'</td>
                    </tr>
                ';
                foreach ($bidang['data'] as $kd_program => $program) {
                    $kd_program = explode('.', $kd_program);
                    $kd_program = $kd_program[count($kd_program)-1];
                    $body .= '
                        <tr>
                            <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
                            <td class="kanan bawah text_blok">'.$kd_bidang.'</td>
                            <td class="kanan bawah text_blok">'.$kd_program.'</td>
                            <td class="kanan bawah">&nbsp;</td>
                            <td class="kanan bawah">&nbsp;</td>
                            <td class="kanan bawah text_blok" colspan="8">'.$program['nama'].'</td>
                            <td class="kanan bawah text_kanan text_blok">'.number_format($program['total'],0,",",".").'</td>
                            <td class="kanan bawah" colspan="4">&nbsp;</td>
                            <td class="kanan bawah text_kanan text_blok">'.number_format($program['total_n_plus'],0,",",".").'</td>
                        </tr>
                    ';
                    foreach ($program['data'] as $kd_giat => $giat) {
                        $kd_giat = explode('.', $kd_giat);
                        $kd_giat = $kd_giat[count($kd_giat)-2].'.'.$kd_giat[count($kd_giat)-1];
                        $body .= '
                            <tr>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">'.$kd_urusan.'</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:00;" width="5">'.$kd_bidang.'</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold; vnd.ms-excel.numberformat:000;" width="5">'.$kd_program.'</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" width="5">'.$kd_giat.'</td>
                                <td style="border:.5pt solid #000; vertical-align:middle;" width="5">&nbsp;</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" colspan="8">'.$giat['nama'].'</td>
                                <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">'.number_format($giat['total'],0,",",".").'</td>
                                <td style="border:.5pt solid #000; vertical-align:middle; font-weight:bold;" colspan="4"></td>
                                <td style="border:.5pt solid #000; vertical-align:middle;  text-align:right; font-weight:bold;">'.number_format($giat['total_n_plus'],0,",",".").'</td>
                            </tr>
                        ';
                        foreach ($giat['data'] as $kd_sub_giat => $sub_giat) {
                            $kd_sub_giat = explode('.', $kd_sub_giat);
                            $kd_sub_giat = $kd_sub_giat[count($kd_sub_giat)-1];
                            $capaian_prog = '';
                            if(!empty($sub_giat['capaian_prog'])){
                                $capaian_prog = $sub_giat['capaian_prog'][0]['capaianteks'];
                            }
                            $target_capaian_prog = '';
                            if(!empty($sub_giat['capaian_prog'])){
                                $target_capaian_prog = $sub_giat['capaian_prog'][0]['targetcapaianteks'];
                            }
                            $output_giat = '';
                            if(!empty($sub_giat['output_giat'])){
                                $output_giat = $sub_giat['output_giat'][0]['outputteks'];
                            }
                            $target_output_giat = '';
                            if(!empty($sub_giat['output_giat'])){
                                $target_output_giat = $sub_giat['output_giat'][0]['targetoutputteks'];
                            }
                            $output_sub_giat = '';
                            $target_output_sub_giat = '';
                            if(!empty($sub_giat['output_sub_giat'])){
                                $output_sub_giat = array();
                                $target_output_sub_giat = array();
                                foreach ($sub_giat['output_sub_giat'] as $k_sub => $v_sub) {
                                    $output_sub_giat[] = $v_sub['outputteks'];
                                    $target_output_sub_giat[] = $v_sub['targetoutputteks'];
                                }
                                $output_sub_giat = implode('<br>', $output_sub_giat);
                                $target_output_sub_giat = implode('<br>', $target_output_sub_giat);
                            }
                            $lokasi_sub_giat = '';
                            if(!empty($sub_giat['lokasi_sub_giat'])){
                                $lokasi_sub_giat = $sub_giat['lokasi_sub_giat'][0]['daerahteks'].', '.$sub_giat['lokasi_sub_giat'][0]['camatteks'].', '.$sub_giat['lokasi_sub_giat'][0]['lurahteks'];
                            }
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
                            $body .= '
                                <tr>
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
                                    <td class="kanan bawah"><br/></td>
                                    <td class="kanan bawah">&nbsp;</td>
                                    <td class="kanan bawah">'.$ind_n_plus.'</td>
                                    <td class="kanan bawah">'.$target_ind_n_plus.'</td>
                                    <td class="kanan bawah text_kanan">'.number_format($sub_giat['total_n_plus'],0,",",".").'</td>
                                </tr>
                            ';
                            $sasaran_text = '';
                            if(!empty($sub_giat['data_renstra'])){
                                $sasaran_text = $sub_giat['data_renstra'][0]['sasaran_teks'];
                            }
                        }
                    }
                }
            }
        }
    }

$nama_excel = 'INPUT RENJA '.strtoupper($nama_skpd).'<br>TAHUN ANGGARAN '.$input['tahun_anggaran'].' '.$nama_pemda;
$nama_laporan = 'INPUT RENJA '.strtoupper($nama_skpd).'<br>TAHUN ANGGARAN '.$input['tahun_anggaran'].' '.$nama_pemda;

echo '
    <div id="cetak" title="'.$nama_excel.'" style="padding: 5px;">
        <input type="hidden" value="'. get_option( "_crb_api_key_extension" ) .'" id="api_key">
        <h4 style="text-align: center; font-size: 13px; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">'.$nama_laporan.'</h4>
        <table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; table-layout:fixed; overflow-wrap: break-word; font-size: 60%; border: 0;">
            <thead>
                <tr>
                    <th style="padding: 0; border: 0; width:1%"></th>
                    <th style="padding: 0; border: 0; width:1.2%"></th>
                    <th style="padding: 0; border: 0; width:1.5%"></th>
                    <th style="padding: 0; border: 0; width:1.5%"></th>
                    <th style="padding: 0; border: 0; width:1.5%"></th>
                    <th style="padding: 0; border: 0; width:7%"></th>
                    <th style="padding: 0; border: 0; width:7.5%"></th>
                    <th style="padding: 0; border: 0; width:7.5%"></th>
                    <th style="padding: 0; border: 0; width:7.5%"></th>
                    <th style="padding: 0; border: 0; width:4%"></th>
                    <th style="padding: 0; border: 0; width:3.5%"></th>
                    <th style="padding: 0; border: 0; width:3.5%"></th>
                    <th style="padding: 0; border: 0; width:3.5%"></th>
                    <th style="padding: 0; border: 0; width:5.5%"></th>
                    <th style="padding: 0; border: 0; width:3.5%"></th>
                    <th style="padding: 0; border: 0; width:4%"></th>
                    <th style="padding: 0; border: 0; width:7.5%"></th>
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
                    <td class="kanan bawah text_kanan text_blok">'.number_format($data_all['total'],0,",",".").'</td>
                    <td class="kanan bawah" colspan="4">&nbsp;</td>
                    <td class="kanan bawah text_kanan text_blok">'.number_format($data_all['total_n_plus'],0,",",".").'</td>
                </tr>
            </tbody>
        </table>
    </div>
';
echo '
	<div class="modal fade mt-4" id="modalTambahRenja" tabindex="-1" role="dialog" aria-labelledby="modalTambahRenjaLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalTambahRenjaLabel">Tambah Renja</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div>
						<label for="input_sub_unit" style="display:inline-block">Sub Unit</label>
                        <select id="input_sub_unit" style="display:block;width:100%;"></select>
					</div>
					<div>
						<label for="input_prioritas_provinsi" style="display:inline-block">Prioritas Pembangunan Provinsi</label>
						<input type="number" id="input_prioritas_provinsi" name="input_prioritas_provinsi" style="display:block;width:100%;" placeholder="Prioritas Pembangunan Provinsi"/>
					</div>
					<div>
						<label for="input_prioritas_kab_kota" style="display:inline-block">Prioritas Pembangunan Kabupaten/Kota</label>
						<input type="number" id="input_prioritas_kab_kota" name="input_prioritas_kab_kota" style="display:block;width:100%;" placeholder="Prioritas Pembangunan Kabupaten/Kota"/>
					</div>
					<div>
						<label for="jadwal_tanggal" style="display:inline-block">Jadwal Pelaksanaan</label>
						<input type="text" id="jadwal_tanggal" name="datetimes" style="display:block;width:100%;"/>
					</div>
					<div>
						<label for="link_rpd_rpjm" style="display:inline-block">Pilih Jadwal RPD atau RPJM</label>
						<select id="link_rpd_rpjm" style="display:block;width: 100%;">
							<option value="">Pilih RPD atau RPJM</option>
							<?php echo $select_rpd_rpjm; ?>
						</select>
					</div>
				</div> 
				<div class="modal-footer">
					<button class="btn btn-primary submitBtn" onclick="submitTambahRenjaForm()">Simpan</button>
					<button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
				</div>
			</div>
		</div>
	</div>
';
?>

<script type="text/javascript">
    run_download_excel();
    let id_skpd = <?php echo $input['id_skpd']; ?>;
    let tahun_anggaran = <?php echo $input['tahun_anggaran']; ?>;

    get_data_sub_unit(id_skpd)

	var mySpace = '<div style="padding:3rem;"></div>';
	
	jQuery('body').prepend(mySpace);

	var dataHitungMundur = {
		'namaJadwal' : '<?php echo ucwords($namaJadwal)  ?>',
		'mulaiJadwal' : '<?php echo $mulaiJadwal  ?>',
		'selesaiJadwal' : '<?php echo $selesaiJadwal  ?>',
		'thisTimeZone' : '<?php echo $timezone ?>'
	}

	penjadwalanHitungMundur(dataHitungMundur);

	var aksi = ''
	+'<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RENJA</a>'
	+'</label>';
	jQuery('#action-sipd').append(aksi);

    function get_data_sub_unit(id_skpd){
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:"post",
			data:{
				'action'            : "get_sub_unit_by_id",
				'api_key'           : jQuery("#api_key").val(),
				'tahun_anggaran'    : tahun_anggaran,
                'id_skpd'           : id_skpd
			},
			dataType: "json",
			success:function(response){
                globalThis.dataSubUnit = response;
                jQuery("#input_sub_unit").html(dataSubUnit.table_content);
			    jQuery('#input_sub_unit').select2();
                console.log(dataSubUnit.table_content);
				// enable_button();
			}
		})
	}

	jQuery('#tambah-data').on('click', function(){
		jQuery("#modalTambahRenja .modal-title").html("Tambah Renja");
		jQuery("#modalTambahRenja .submitBtn")
			.attr("onclick", 'submitTambahRenjaForm()')
			.attr("disabled", false)
			.text("Simpan");
		jQuery('#modalTambahRenja').modal('show');
	});
</script>