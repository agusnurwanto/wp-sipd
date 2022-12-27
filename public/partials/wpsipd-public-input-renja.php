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

$tahun_anggaran = '2022';
$namaJadwal = '-';
$mulaiJadwal = '-';
$selesaiJadwal = '-';
$relasi_perencanaan = '-';
$id_tipe_relasi = '-';

$sql = "
    SELECT 
        *
    FROM data_sub_keg_bl_lokal
    WHERE id_sub_skpd=%d
        AND tahun_anggaran=%d";
$subkeg = $wpdb->get_results($wpdb->prepare($sql,$input['id_skpd'], $input['tahun_anggaran']), ARRAY_A);

$cek_jadwal = $this->validasi_jadwal_perencanaan('renja');
$jadwal_lokal = $cek_jadwal['data'];
if(!empty($jadwal_lokal)){
    if(!empty($jadwal_lokal[0]['relasi_perencanaan'])){
        $relasi = $wpdb->get_row("
            SELECT 
                id_tipe 
            FROM `data_jadwal_lokal`
            WHERE id_jadwal_lokal=".$jadwal_lokal[0]['relasi_perencanaan']);

        $relasi_perencanaan = $jadwal_lokal[0]['relasi_perencanaan'];
        $id_tipe_relasi = $relasi->id_tipe;
    }
	$tahun_anggaran = $jadwal_lokal[0]['tahun_anggaran'];
	$namaJadwal = $jadwal_lokal[0]['nama'];
	$mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
	$selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];
}

// nomor urut tahun anggaran RENJA sesuai jadwal tahun awal di RENSTRA
$urut = 0;

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
        from data_capaian_prog_sub_keg_lokal 
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
            and capaianteks != ''
        order by id ASC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

    $output_giat = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_output_giat_sub_keg_lokal 
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
        order by id ASC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

    $output_sub_giat = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_sub_keg_indikator_lokal
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
        order by id DESC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

    $lokasi_sub_giat = $wpdb->get_results($wpdb->prepare("
        select 
            * 
        from data_lokasi_sub_keg_lokal
        where tahun_anggaran=%d
            and active=1
            and kode_sbl=%s
        order by id ASC
    ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

    $nama = explode(' ', $sub['nama_sub_giat']);
    $kode_sub_giat = $nama[0];
    $data_renstra = array();
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
?>

<div class="modal fade mt-4" id="modalTambahRenja" role="dialog" aria-labelledby="modalTambahRenjaLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalTambahRenjaLabel">Tambah Sub Kegiatan</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <form>
                    <div class="form-group">
    					<label for="input_sub_unit">Sub Unit</label>
                        <select class="form-control" id="input_sub_unit"></select>
    				</div>
    				<div class="form-group">
    					<label for="input_prioritas_provinsi">Prioritas Pembangunan Provinsi</label>
    					<select class="form-control" id="input_prioritas_provinsi" name="input_prioritas_provinsi">
                            <option value="">Pilih Prioritas Pembangunan Provinsi</option>
                        </select>
    				</div>
    				<div class="form-group">
    					<label for="input_prioritas_kab_kota">Prioritas Pembangunan Kabupaten/Kota</label>
    					<select class="form-control" id="input_prioritas_kab_kota" name="input_prioritas_kab_kota">
                            <option value="">Pilih Prioritas Pembangunan Kabupaten/Kota</option>
                        </select>
    				</div>
    				<div class="form-group">
    					<label for="sub_kegiatan">Sub Kegiatan</label>
    					<select class="form-control" id="sub_kegiatan">
    						<option value="">Pilih Sub Kegiatan</option>
    					</select>
    				</div>
                    <table>
                        <thead>
                            <tr>
                                <th class="text-center" width="50%">Usulan</th>
                                <th class="text-center" width="50%">Penetapan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label for="label_tag">Label (Tag) Sub Kegiatan</label>
                                        <select class="form-control" id="label_tag">
                                            <option value="">Pilih Sub Kegiatan</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sumber_dana">Sumber Dana</label>
                                        <table style="margin: 0;">
                                            <tr>
                                                <td style="width: 40%">
                                                    <select class="form-control" name="sumber_dana">
                                                        <option value="">Pilih Sumber Dana</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="number" name="pagu_sumber_dana"/>
                                                </td>
                                                <td style="width: 70px" class="text-center">
                                                    <button class="btn btn-warning" onclick="tambahSumberDana(this); return false;"><i class="dashicons dashicons-plus"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                    				<div class="form-group">
                    					<label for="kabupaten">Lokasi Pelaksanaan</label>
                                        <table style="margin: 0;">
                                            <tr>
                                                <td>
                                                    <select class="form-control" name="kabupaten">
                                                        <option value="">Pilih Kabupaten / Kota</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control" name="kecamatan">
                                                        <option value="">Pilih Kecamatan</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control" name="desa">
                                                        <option value="">Pilih Desa</option>
                                                    </select>
                                                </td>
                                                <td style="width: 70px" class="text-center">
                                                    <button class="btn btn-warning" onclick="tambahLokasi(this); return false;"><i class="dashicons dashicons-plus"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                    				</div>
                                    <div class="form-group">
                                        <label for="bulan_awal">Waktu Pelaksanaan</label>
                                        <table style="margin: 0;">
                                            <tr>
                                                <td style="width: 50%;">
                                                    <select class="form-control" id="bulan_awal">
                                                        <option value="">Pilih Bulan Awal</option>
                                                    </select>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control" id="bulan_akhir">
                                                        <option value="">Pilih Bulan Akhir</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan">Anggaran Sub Kegiatan</label>
                                        <input class="form-control" type="number" id="pagu_sub_kegiatan"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan_1">Anggaran Sub Kegiatan Tahun Berikutnya</label>
                                        <input class="form-control" type="number" id="pagu_sub_kegiatan_1"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="bulan_awal">Indikator Sub Kegiatan</label>
                                        <table style="margin: 0;">
                                            <tr data-id="1" header="1">
                                                <td colspan="2">
                                                    <select class="form-control" name="tolak_ukur">
                                                        <option value="">Pilih Nama Indikator</option>
                                                    </select>
                                                </td>
                                                <td rowspan="2" style="width: 70px; vertical-align: middle;" class="text-center">
                                                    <button class="btn btn-warning" onclick="tambahIndikator(this); return false;"><i class="dashicons dashicons-plus"></i></button>
                                                </td>
                                            </tr>
                                            <tr data-id="1">
                                                <td style="width: 50%;">
                                                    <input class="form-control" type="number" name="target" placeholder="Target Indikator"/>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control" name="satuan">
                                                        <option value="">Pilih Satuan</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="catatan_usulan">Catatan</label>
                                        <textarea class="form-control" id="catatan_usulan"></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="label_tag">Label (Tag) Sub Kegiatan</label>
                                        <select class="form-control" id="label_tag">
                                            <option value="">Pilih Sub Kegiatan</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sumber_dana">Sumber Dana</label>
                                        <table style="margin: 0;">
                                            <tr>
                                                <td style="width: 40%">
                                                    <select class="form-control" name="sumber_dana">
                                                        <option value="">Pilih Sumber Dana</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="number" name="pagu_sumber_dana"/>
                                                </td>
                                                <td style="width: 70px" class="text-center">
                                                    <button class="btn btn-warning" onclick="tambahSumberDana(this); return false;"><i class="dashicons dashicons-plus"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="kabupaten">Lokasi Pelaksanaan</label>
                                        <table style="margin: 0;">
                                            <tr>
                                                <td>
                                                    <select class="form-control" name="kabupaten">
                                                        <option value="">Pilih Kabupaten / Kota</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control" name="kecamatan">
                                                        <option value="">Pilih Kecamatan</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control" name="desa">
                                                        <option value="">Pilih Desa</option>
                                                    </select>
                                                </td>
                                                <td style="width: 70px" class="text-center">
                                                    <button class="btn btn-warning" onclick="tambahLokasi(this); return false;"><i class="dashicons dashicons-plus"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="bulan_awal">Waktu Pelaksanaan</label>
                                        <table style="margin: 0;">
                                            <tr>
                                                <td style="width: 50%;">
                                                    <select class="form-control" id="bulan_awal">
                                                        <option value="">Pilih Bulan Awal</option>
                                                    </select>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control" id="bulan_akhir">
                                                        <option value="">Pilih Bulan Akhir</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan">Anggaran Sub Kegiatan</label>
                                        <input class="form-control" type="number" id="pagu_sub_kegiatan"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan_1">Anggaran Sub Kegiatan Tahun Berikutnya</label>
                                        <input class="form-control" type="number" id="pagu_sub_kegiatan_1"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="bulan_awal">Indikator Sub Kegiatan</label>
                                        <table style="margin: 0;">
                                            <tr data-id="1" header="1">
                                                <td colspan="2">
                                                    <select class="form-control" name="tolak_ukur">
                                                        <option value="">Pilih Nama Indikator</option>
                                                    </select>
                                                </td>
                                                <td rowspan="2" style="width: 70px; vertical-align: middle;" class="text-center">
                                                    <button class="btn btn-warning" onclick="tambahIndikator(this); return false;"><i class="dashicons dashicons-plus"></i></button>
                                                </td>
                                            </tr>
                                            <tr data-id="1">
                                                <td style="width: 50%;">
                                                    <input class="form-control" type="number" name="target" placeholder="Target Indikator"/>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control" name="satuan">
                                                        <option value="">Pilih Satuan</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="catatan">Catatan</label>
                                        <textarea class="form-control" id="catatan"></textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
			</div> 
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="submitTambahRenjaForm()">Simpan</button>
				<button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

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

    function tambahIndikator(that){
        var id = +jQuery(that).closest('tr').attr('data-id');
        var trnew = ''
            +'<tr data-id="'+(id+1)+'" header="1">'
                +'<td colspan="2">'
                    +'<select class="form-control" name="tolak_ukur">'
                        +'<option value="">Pilih Nama Indikator</option>'
                    +'</select>'
                +'</td>'
                +'<td style="width: 70px; vertical-align: middle;" class="text-center aksi" rowspan="2">'
                    +'<button class="btn btn-warning" onclick="tambahIndikator(this); return false;"><i class="dashicons dashicons-plus"></i></button>'
                +'</td>'
            +'</tr>'
            +'<tr data-id="'+(id+1)+'">'
                +'<td style="width: 50%;">'
                    +'<input class="form-control" type="number" name="target" placeholder="Target Indikator"/>'
                +'</td>'
                +'<td style="width: 50%;">'
                    +'<select class="form-control" name="satuan">'
                        +'<option value="">Pilih Satuan</option>'
                    +'</select>'
                +'</td>'
            +'</tr>';
        var tbody = jQuery(that).closest('tbody');
        tbody.append(trnew);
        var tr = tbody.find('>tr');
        var length = tr.length-2;
        tr.map(function(i, b){
            var header = jQuery(b).attr('header');
            if(header == 1){
                if(length == i){
                    var html = '<button class="btn btn-warning" onclick="tambahIndikator(this); return false;"><i class="dashicons dashicons-plus"></i></button>';
                }else{
                    var html = '<button class="btn btn-danger" onclick="hapusIndikator(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                }
                jQuery(b).find('>td').last().html(html);
            }
        });
    }

    function hapusIndikator(that){
        var id = jQuery(that).closest('tr').attr('data-id');
        jQuery(that).closest('tbody').find('tr[data-id="'+id+'"]').remove();
    }

    function tambahLokasi(that){
        var trnew = ''
            +'<tr>'
                +'<td>'
                    +'<select class="form-control" id="kabupaten">'
                        +'<option value="">Pilih Kabupaten / Kota</option>'
                    +'</select>'
                +'</td>'
                +'<td style="width: 30%">'
                    +'<select class="form-control" id="kecamatan">'
                        +'<option value="">Pilih Kecamatan</option>'
                    +'</select>'
                +'</td>'
                +'<td style="width: 30%">'
                    +'<select class="form-control" id="desa">'
                        +'<option value="">Pilih Desa</option>'
                    +'</select>'
                +'</td>'
                +'<td style="width: 70px" class="text-center">'
                    +'<button class="btn btn-warning" onclick="tambahLokasi(this);"><i class="dashicons dashicons-plus"></i></button>'
                +'</td>'
            +'</tr>';
        var tbody = jQuery(that).closest('tbody');
        tbody.append(trnew);
        var tr = tbody.find('>tr');
        var length = tr.length-1;
        tr.map(function(i, b){
            if(length == i){
                var html = '<button class="btn btn-warning" onclick="tambahLokasi(this); return false;"><i class="dashicons dashicons-plus"></i></button>';
            }else{
                var html = '<button class="btn btn-danger" onclick="hapusLokasi(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
            }
            jQuery(b).find('>td').last().html(html);
        });
    }

    function hapusLokasi(that){
        var tbody = jQuery(that).closest('tbody');
        jQuery(that).closest('tr').remove();
    }

    function tambahSumberDana(that){
        var trnew = ''
            +'<tr>'
                +'<td style="width: 60%">'
                    +'<select class="form-control" name="sumber_dana">'
                        +'<option value="">Pilih Sumber Dana</option>'
                    +'</select>'
                +'</td>'
                +'<td>'
                    +'<input class="form-control" type="number" name="pagu_sumber_dana"/>'
                +'</td>'
                +'<td style="width: 70px" class="text-center">'
                    +'<button class="btn btn-warning" onclick="tambahSumberDana(this); return false;"><i class="dashicons dashicons-plus"></i></button>'
                +'</td>'
            +'</tr>';
        var tbody = jQuery(that).closest('tbody');
        tbody.append(trnew);
        var tr = tbody.find('>tr');
        var length = tr.length-1;
        tr.map(function(i, b){
            if(length == i){
                var html = '<button class="btn btn-warning" onclick="tambahSumberDana(this); return false;"><i class="dashicons dashicons-plus"></i></button>';
            }else{
                var html = '<button class="btn btn-danger" onclick="hapusSumberDana(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
            }
            jQuery(b).find('>td').last().html(html);
        });
    }

    function hapusSumberDana(that){
        var tbody = jQuery(that).closest('tbody');
        jQuery(that).closest('tr').remove();
    }

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
			    jQuery('#input_sub_unit').select2({width: '100%'});
                console.log(dataSubUnit.table_content);
				// enable_button();
			}
		})
	}

	jQuery('#tambah-data').on('click', function(){
		jQuery("#modalTambahRenja .modal-title").html("Tambah Sub Kegiatan");
		jQuery("#modalTambahRenja .submitBtn")
			.attr("onclick", 'submitTambahRenjaForm()')
			.attr("disabled", false)
			.text("Simpan");
		jQuery('#modalTambahRenja').modal('show');
	});
</script>