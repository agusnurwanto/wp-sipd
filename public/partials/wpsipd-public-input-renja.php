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
$id_lokasi_prov = get_option('_crb_id_lokasi_prov');
$id_lokasi_kokab = (empty(get_option('_crb_id_lokasi_kokab'))) ? 0 : get_option('_crb_id_lokasi_kokab');

if($id_lokasi_prov == 0 || empty($id_lokasi_prov)){
    die('Setting ID lokasi Provinsi di SIPD Options tidak boleh kosong!');
}

$tahun_anggaran = '2022';
$namaJadwal = '-';
$mulaiJadwal = '-';
$selesaiJadwal = '-';
$relasi_perencanaan = '-';
$id_tipe_relasi = '-';
$id_unit = '';
$data_skpd = $wpdb->get_row($wpdb->prepare("
    select 
        nama_skpd,
        id_unit 
    from data_unit
    where 
        id_skpd=%d 
        and tahun_anggaran=%d
        and active=1
    order by id ASC
    ", $input['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
$id_unit = (!empty($data_skpd['id_unit'])) ? $data_skpd['id_unit'] : '';

$sql = "
    SELECT 
        *
    FROM data_sub_keg_bl_lokal
    WHERE id_sub_skpd=%d
        AND tahun_anggaran=%d
        AND active=1
        ORDER BY kode_giat ASC, kode_sub_giat ASC";
$subkeg = $wpdb->get_results($wpdb->prepare($sql,$input['id_skpd'], $input['tahun_anggaran']), ARRAY_A);

$cek_jadwal = $this->validasi_jadwal_perencanaan('renja');
$jadwal_lokal = $cek_jadwal['data'];
$add_renja = '';
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

    $awal = new DateTime($mulaiJadwal);
    $akhir = new DateTime($selesaiJadwal);
    $now = new DateTime(date('Y-m-d H:i:s'));

    if($now >= $awal && $now <= $akhir){
        $add_renja = '<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RENJA</a>';
    }
}

// nomor urut tahun anggaran RENJA sesuai jadwal tahun awal di RENSTRA
$urut = 0;

$timezone = get_option('timezone_string');

$bulan = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
$bulan_option = '';
foreach($bulan as $k_bulan => $v_bulan){
    $key_bulan = $k_bulan+1;
    $bulan_option .= '<option value="'.$key_bulan.'">'.$v_bulan.'</option>';
}

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
                <td class="kiri kanan bawah text_blok" colspan="20">Unit Organisasi : '.$sub_skpd['nama_skpd'].'</td>
            </tr>
            <tr>
                <td class="kiri kanan bawah text_blok"></td>
                <td class="kanan bawah text_blok" colspan="12">Sub Unit Organisasi : '.$sub_skpd['nama'].'</td>
                <td class="kanan bawah text_kanan text_blok">'.number_format($sub_skpd['total'],0,",",".").'</td>
                <td class="kanan bawah" colspan="4">&nbsp;</td>
                <td class="kanan bawah text_kanan text_blok">'.number_format($sub_skpd['total_n_plus'],0,",",".").'</td>
                <td class="kanan bawah"></td>
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
                    <td class="kanan bawah text_blok" colspan="15">'.$urusan['nama'].'</td>
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
                        <td class="kanan bawah"></td>
                    </tr>
                ';
                foreach ($bidang['data'] as $kd_program => $program) {
                    $kd_program = explode('.', $kd_program);
                    $kd_program = $kd_program[count($kd_program)-1];
                    
                    $tombol_aksi = '<button class="btn-sm btn-warning" style="margin: 1px;" onclick="edit_program(21231);" title="Edit Program"><i class="dashicons dashicons-plus"></i></button>';
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
                            <td class="kanan bawah text_tengah">'.$tombol_aksi.'</td>
                        </tr>
                    ';
                    foreach ($program['data'] as $kd_giat => $giat) {
                        $kd_giat = explode('.', $kd_giat);
                        $kd_giat = $kd_giat[count($kd_giat)-2].'.'.$kd_giat[count($kd_giat)-1];
                        
                        $tombol_aksi = '<button class="btn-sm btn-warning" style="margin: 1px;" onclick="edit_progra(21231);" title="Edit Kegiatan"><i class="dashicons dashicons-plus"></i></button>';

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
                                <td class="kanan bawah text_tengah">'.$tombol_aksi.'</td>
                            </tr>
                        ';
                        foreach ($giat['data'] as $kd_sub_giat => $sub_giat) {
                            $kode_sub_giat = $kd_sub_giat;
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

                            $kode_sbl = '';
                            if(!empty($sub_giat['output_sub_giat'])){
                                $kode_sbl = $sub_giat['output_sub_giat'][0]['kode_sbl'];
                            }

                            $url_rka_lokal = $this->generatePage('Data RKA Lokal | '.$kode_sbl.' | '.$input['tahun_anggaran'],$input['tahun_anggaran'],'[input_rka_lokal kode_sbl="'.$kode_sbl.'" tahun_anggaran="'.$input['tahun_anggaran'].'"]');
                            $tombol_aksi = '<a href="'.$url_rka_lokal.'" target="_blank"><button class="btn-sm btn-info" style="margin: 1px;" title="Detail Renja"><i class="dashicons dashicons-search"></i></button></a>';
                            $tombol_aksi .= '<button class="btn-sm btn-warning" style="margin: 1px;" onclick="edit_renja(\''.$kode_sub_giat.'\');" title="Edit Renja"><i class="dashicons dashicons-edit"></i></button>';
                            $tombol_aksi .= '<button class="btn-sm btn-danger" style="margin: 1px;" onclick="delete_renja(\''.$kode_sub_giat.'\');" title="Hapus Renja"><i class="dashicons dashicons-trash"></i></button>';
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
                                    <td class="kanan bawah text_tengah">'.$tombol_aksi.'</td>
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
                    <th style="padding: 0; border: 0; width:3.5%"></th>
                    <th style="padding: 0; border: 0; width:3.5%"></th>
                    <th style="padding: 0; border: 0; width:5.5%"></th>
                    <th style="padding: 0; border: 0; width:4.5%"></th>
                </tr>
                <tr>
                    <td class="atas kanan bawah kiri text_tengah text_blok" colspan="5" rowspan="3">Kode</td>
                    <td class="atas kanan bawah text_tengah text_blok" rowspan="3">Urusan/ Bidang Urusan Pemerintahan Daerah Dan Program/ Kegiatan</td>
                    <td class="atas kanan bawah text_tengah text_blok" colspan="3">Indikator Kinerja</td>
                    <td class="atas kanan bawah text_tengah text_blok" colspan="6">Rencana Tahun '.$input['tahun_anggaran'].'</td>
                    <td class="atas kanan bawah text_tengah text_blok" rowspan="3">Catatan Penting</td>
                    <td class="atas kanan bawah text_tengah text_blok" colspan="3">Prakiraan Maju Rencana Tahun '.($input['tahun_anggaran']+1).'</td>
                    <td class="atas kanan bawah kiri text_tengah text_blok" rowspan="3">Aksi</td>
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
                    <td class="kanan bawah"></td>
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
                <form id="form-renja">
                    <div class="form-group">
    					<label for="input_sub_unit">Sub Unit</label>
                        <select class="form-control input_select_2" name="input_sub_unit" id="input_sub_unit" onchange="get_sub_keg(this)"></select>
    				</div>
    				<div class="form-group">
    					<label for="input_prioritas_provinsi">Prioritas Pembangunan Provinsi</label>
    					<select class="form-control input_select_2" id="input_prioritas_provinsi" name="input_prioritas_provinsi">
                            <option value="">Pilih Prioritas Pembangunan Provinsi</option>
                        </select>
    				</div>
    				<div class="form-group">
    					<label for="input_prioritas_kab_kota">Prioritas Pembangunan Kabupaten/Kota</label>
    					<select class="form-control input_select_2" id="input_prioritas_kab_kota" name="input_prioritas_kab_kota">
                            <option value="">Pilih Prioritas Pembangunan Kabupaten/Kota</option>
                        </select>
    				</div>
    				<div class="form-group">
    					<label for="sub_kegiatan">Sub Kegiatan</label>
    					<select class="form-control input_select_2" name="input_sub_kegiatan" id="sub_kegiatan" onchange="get_indikator_sub_keg(this)">
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
                                        <label for="label_tag_usulan">Label (Tag) Sub Kegiatan</label>
                                        <select class="form-control input_select_2" name="input_label_sub_keg_usulan" id="label_tag_usulan">
                                            <option value="">Pilih Sub Kegiatan</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sumber_dana_usulan">Sumber Dana</label>
                                        <table class="input_sumber_dana_usulan" style="margin: 0;">
                                            <tr data-id="1">
                                                <td style="width: 60%; max-width:100px;">
                                                    <select class="form-control input_select_2 sumber_dana_usulan" id="sumber_dana_usulan_1" name="input_sumber_dana_usulan[1]">
                                                        <option value="">Pilih Sumber Dana</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control input_number" id="pagu_sumber_dana_usulan_1" type="number" name="input_pagu_sumber_dana_usulan[1]"/>
                                                </td>
                                                <td style="width: 70px" class="text-center">
                                                    <button class="btn btn-warning" onclick="tambahSumberDana(this); return false;"><i class="dashicons dashicons-plus"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                    				<div class="form-group">
                    					<label for="kabupaten_kota_usulan">Lokasi Pelaksanaan</label>
                                        <table class="input_lokasi_usulan" style="margin: 0;">
                                            <tr data-id="1">
                                                <td>
                                                    <select class="form-control kabupaten_kota_usulan" id="kabupaten_kota_usulan_1" name="input_kabupaten_kota_usulan[1]" onchange="get_data_lokasi(this.value,'kabkot','usulan')">
                                                        <option value="">Pilih Kabupaten / Kota</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control input_select_2 kecamatan_usulan" name="input_kecamatan_usulan[1]" id="kecamatan_usulan_1" onchange="get_data_lokasi(this.value,'kec','usulan')">
                                                        <option value="">Pilih Kecamatan</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control input_select_2 desa_usulan" name="input_desa_usulan[1]" id="desa_usulan_1">
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
                                        <label for="bulan_awal_usulan">Waktu Pelaksanaan</label>
                                        <table style="margin: 0;">
                                            <tr>
                                                <td style="width: 50%;">
                                                    <select class="form-control bulan_awal" name="input_bulan_awal_usulan" id="bulan_awal_usulan">
                                                        <option value="">Pilih Bulan Awal</option>
                                                        <?php echo $bulan_option; ?>
                                                    </select>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control bulan_akhir" name="input_bulan_akhir_usulan" id="bulan_akhir_usulan">
                                                        <option value="">Pilih Bulan Akhir</option>
                                                        <?php echo $bulan_option; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan_usulan">Anggaran Sub Kegiatan</label>
                                        <input class="form-control input_number" type="number" name="input_pagu_sub_keg_usulan" id="pagu_sub_kegiatan_usulan"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan_1_usulan">Anggaran Sub Kegiatan Tahun Berikutnya</label>
                                        <input class="form-control input_number" type="number" name="input_pagu_sub_keg_1_usulan" id="pagu_sub_kegiatan_1_usulan"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_ind_sub_keg_usulan">Indikator Sub Kegiatan</label>
                                        <table class="indi_sub_keg_table_usulan" style="margin: 0;">
                                            <tr data-id="1" header="1">
                                                <td colspan="2" style="max-width: 100px;">
                                                    <select class="form-control pagu_indi_sub_keg input_select_2" id="pagu_ind_sub_keg_usulan_1" name="input_indikator_sub_keg_usulan[1]">
                                                        <option value="">Pilih Nama Indikator</option>
                                                    </select>
                                                    <input type="hidden" id="ind_sub_keg_id_1" name="ind_sub_keg_id[1]" value="0">
                                                </td>
                                                <td rowspan="2" style="width: 70px; vertical-align: middle;" class="text-center">
                                                    <button class="btn btn-warning" onclick="tambahIndikator(this); return false;"><i class="dashicons dashicons-plus"></i></button>
                                                </td>
                                            </tr>
                                            <tr data-id="1">
                                                <td style="width: 50%;">
                                                    <input class="form-control input_number" type="number" id="indikator_pagu_indi_sub_keg_usulan_1" name="input_target_usulan[1]" placeholder="Target Indikator"/>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control satuan_pagu_indi_sub_keg input_select_2" id="satuan_pagu_indi_sub_keg_usulan_1" name="input_satuan_usulan[1]">
                                                        <option value="">Pilih Satuan</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="catatan_usulan">Catatan</label>
                                        <textarea class="form-control input_text" name="input_catatan_usulan" id="catatan_usulan"></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="label_tag">Label (Tag) Sub Kegiatan</label>
                                        <select class="form-control input_select_2" name="input_label_sub_keg" id="label_tag">
                                            <option value="">Pilih Sub Kegiatan</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sumber_dana">Sumber Dana</label>
                                        <table class="input_sumber_dana" style="margin: 0;">
                                            <tr data-id="1">
                                                <td style="width: 60%; max-width:100px;">
                                                    <select class="form-control input_select_2 sumber_dana" id="sumber_dana_1" name="input_sumber_dana[1]">
                                                        <option value="">Pilih Sumber Dana</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control input_number" id="pagu_sumber_dana_1" type="number" name="input_pagu_sumber_dana[1]"/>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="kabupaten_kota">Lokasi Pelaksanaan</label>
                                        <table class="input_lokasi" style="margin: 0;">
                                            <tr data-id="1">
                                                <td>
                                                    <select class="form-control kabupaten_kota" id="kabupaten_kota_1" name="input_kabupaten_kota[1]" onchange="get_data_lokasi(this.value,'kabkot','penetapan')">
                                                        <option value="">Pilih Kabupaten / Kota</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control input_select kecamatan" id="kecamatan_1" name="input_kecamatan[1]" onchange="get_data_lokasi(this.value,'kec','penetapan')">
                                                        <option value="">Pilih Kecamatan</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control input_select desa" id="desa_1" name="input_desa[1]">
                                                        <option value="">Pilih Desa</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="bulan_awal">Waktu Pelaksanaan</label>
                                        <table style="margin: 0;">
                                            <tr>
                                                <td style="width: 50%;">
                                                    <select class="form-control bulan_awal" name="input_bulan_awal" id="bulan_awal">
                                                        <option value="">Pilih Bulan Awal</option>
                                                        <?php echo $bulan_option; ?>
                                                    </select>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control bulan_akhir" name="input_bulan_akhir" id="bulan_akhir">
                                                        <option value="">Pilih Bulan Akhir</option>
                                                        <?php echo $bulan_option; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan">Anggaran Sub Kegiatan</label>
                                        <input class="form-control input_number" type="number" name="input_pagu_sub_keg" id="pagu_sub_kegiatan"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan_1">Anggaran Sub Kegiatan Tahun Berikutnya</label>
                                        <input class="form-control input_number" type="number" name="input_pagu_sub_keg_1" id="pagu_sub_kegiatan_1"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_indi_sub_keg_penetapan">Indikator Sub Kegiatan</label>
                                        <table class="indi_sub_keg_table" style="margin: 0;">
                                            <tr data-id="1" header="1">
                                                <td colspan="2" style="max-width: 100px;">
                                                    <select class="form-control pagu_indi_sub_keg input_select_2" id="pagu_indi_sub_keg_penetapan_1" name="input_indikator_sub_keg[1]">
                                                        <option value="">Pilih Nama Indikator</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr data-id="1">
                                                <td style="width: 50%;">
                                                    <input class="form-control input_number" type="number" name="input_target[1]" id="indikator_pagu_indi_sub_keg_1" placeholder="Target Indikator"/>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control satuan_pagu_indi_sub_keg input_select_2" id="satuan_pagu_indi_sub_keg_penetapan_1" name="input_satuan[1]">
                                                        <option value="">Pilih Satuan</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="catatan">Catatan</label>
                                        <textarea class="form-control input_text" name="input_catatan" id="catatan"></textarea>
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

<!-- Modal indikator renja -->
<div class="modal fade" id="modal-indikator-renja" data-backdrop="static" role="dialog" aria-labelledby="modal-indikator-renja-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<script type="text/javascript">
    let id_skpd = <?php echo $input['id_skpd']; ?>;
    let tahun_anggaran = <?php echo $input['tahun_anggaran']; ?>;
    let id_unit = <?php echo $id_unit; ?>;

    jQuery(document).ready(function(){
        run_download_excel();

    	var mySpace = '<div style="padding:3rem;"></div>';
    	jQuery('body').prepend(mySpace);

        set_waktu();
    	var dataHitungMundur = {
    		'namaJadwal' : '<?php echo ucwords($namaJadwal)  ?>',
    		'mulaiJadwal' : '<?php echo $mulaiJadwal  ?>',
    		'selesaiJadwal' : '<?php echo $selesaiJadwal  ?>',
    		'thisTimeZone' : '<?php echo $timezone ?>'
    	}
    	penjadwalanHitungMundur(dataHitungMundur);

    	var aksi = '<?php echo $add_renja; ?>';
    	jQuery('#action-sipd').append(aksi);

    <?php if(!empty($add_renja)): ?>
        jQuery('#tambah-data').on('click', function(){
            get_data_sub_unit(id_skpd)
            .then(function(){
                get_data_sumber_dana()
                .then(function(){
                    get_data_lokasi(false, 'prov')
                    .then(function(){
                        jQuery("#modalTambahRenja .modal-title").html("Tambah Sub Kegiatan");
                        jQuery("#modalTambahRenja .submitBtn")
                            .attr("onclick", 'submitTambahRenjaForm()')
                            .attr("disabled", false)
                            .text("Simpan");
                        jQuery('#modalTambahRenja').modal('show');
                        jQuery('#input_sub_unit').prop('disabled', false);
                        jQuery('#sub_kegiatan').prop('disabled', false);
                        jQuery('#input_sub_unit').val('').trigger("change");
                        jQuery('#sub_kegiatan').empty().trigger("change");
                        jQuery('.bulan_awal').val("1");
                        jQuery('.bulan_akhir').val("12");
                        jQuery('.input_number').val(0);
                        jQuery('.input_text').val('');
                        jQuery('.input_select').val('');
                        var iddf = jQuery('.indi_sub_keg_table_usulan tr').last().attr('data-id');
                        if(iddf > 1){
                            for (let index = iddf; index > 1; index--) {
                                jQuery('.indi_sub_keg_table_usulan > tbody').find('tr[data-id="'+index+'"]').remove();
                                jQuery('.indi_sub_keg_table > tbody').find('tr[data-id="'+index+'"]').remove();
                            }
                        }
                        var html = '<button class="btn btn-warning" onclick="tambahIndikator(this); return false;"><i class="dashicons dashicons-plus"></i></button>';
                        jQuery('.indi_sub_keg_table_usulan tr:first-child').find('>td').last().html(html);

                    });
                })
            });
        });
    <?php endif; ?>
    });

    function tambahIndikator(that){
        var id = +jQuery('.indi_sub_keg_table_usulan > tbody tr').last().attr('data-id');
        var newId = id+1;
        /** tambah input usulan indi_sub_keg */
        var trNewUsulan = ''
            +'<tr data-id="'+newId+'" header="1">'
                +'<td colspan="2" style="max-width: 100px;">'
                    +'<select class="form-control pagu_indi_sub_keg input_select_2" data-edit="0" id="pagu_ind_sub_keg_usulan_'+newId+'" name="input_indikator_sub_keg_usulan['+newId+']">'
                        +'<option value="">Pilih Nama Indikator</option>'
                    +'</select>'
                    +'<input type="hidden" id="ind_sub_keg_id_'+newId+'" name="ind_sub_keg_id['+newId+']" value="0">'
                +'</td>'
                +'<td style="width: 70px; vertical-align: middle;" class="text-center aksi" rowspan="2">'
                    +'<button class="btn btn-warning" onclick="tambahIndikator(this); return false;"><i class="dashicons dashicons-plus"></i></button>'
                +'</td>'
            +'</tr>'
            +'<tr data-id="'+newId+'">'
                +'<td style="width: 50%;">'
                    +'<input class="form-control input_number" type="number" id="indikator_pagu_indi_sub_keg_usulan_'+newId+'" name="input_target_usulan['+newId+']" placeholder="Target Indikator"/>'
                +'</td>'
                +'<td style="width: 50%;">'
                    +'<select class="form-control satuan_pagu_indi_sub_keg input_select_2" id="satuan_pagu_indi_sub_keg_usulan_'+newId+'" name="input_satuan_usulan['+newId+']">'
                        +'<option value="">Pilih Satuan</option>'
                    +'</select>'
                +'</td>'
            +'</tr>';
        
        var tbody = jQuery('.indi_sub_keg_table_usulan > tbody');
        tbody.append(trNewUsulan);
        var tr = tbody.find('>tr');
        var length = tr.length-2;
        tr.map(function(i, b){
            var header = jQuery(b).attr('header');
            if(header == 1){
                if(i == 0){
                    var html = '<button class="btn btn-warning" onclick="tambahIndikator(this); return false;"><i class="dashicons dashicons-plus"></i></button>';
                }else{
                    var html = '<button class="btn btn-danger" onclick="hapusIndikator(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                }
                jQuery(b).find('>td').last().html(html);
            }
        });
        /** tambah input indi_sub_keg */
        var trNewUsulan = ''
            +'<tr data-id="'+newId+'" header="1">'
                +'<td colspan="2" style="max-width: 100px;">'
                    +' <select class="form-control pagu_indi_sub_keg input_select_2" id="pagu_indi_sub_keg_penetapan_'+newId+'" name="input_indikator_sub_keg['+newId+']">'
                        +'<option value="">Pilih Nama Indikator</option>'
                    +'</select>'
                +'</td>'
            +'</tr>'
            +'<tr data-id="'+newId+'">'
                +'<td style="width: 50%;">'
                    +'<input class="form-control input_number" type="number" name="input_target['+newId+']" id="indikator_pagu_indi_sub_keg_'+newId+'" placeholder="Target Indikator"/>'
                +'</td>'
                +'<td style="width: 50%;">'
                    +'<select class="form-control satuan_pagu_indi_sub_keg input_select_2" id="satuan_pagu_indi_sub_keg_penetapan_'+newId+'" name="input_satuan['+newId+']">'
                        +'<option value="">Pilih Satuan</option>'
                    +'</select>'
                +'</td>'
            +'</tr>';

        var tbody = jQuery('.indi_sub_keg_table > tbody');
        tbody.append(trNewUsulan);
        let val_sub_keg = jQuery("#sub_kegiatan").val();
        if(val_sub_keg != undefined){
            let data = {id:newId,id_sub_keg:val_sub_keg};
            get_indikator_sub_keg_by_id(data);
        }
    }

    function hapusIndikator(that){
        var id = jQuery(that).closest('tr').attr('data-id');
        jQuery('.indi_sub_keg_table_usulan > tbody').find('tr[data-id="'+id+'"]').remove();
        jQuery('.indi_sub_keg_table > tbody').find('tr[data-id="'+id+'"]').remove();
    }

    function tambahLokasi(that){
        /** tambah input lokasi usulan */
        var id = +jQuery('.input_lokasi_usulan > tbody tr').last().attr('data-id');
        newId = id+1;
        var trNewUsulan = ''
            +'<tr data-id="'+newId+'">'
                +'<td>'
                    +'<select class="form-control" id="kabupaten_kota_usulan_'+newId+'" name="input_kabupaten_kota_usulan['+newId+']" onchange="get_data_lokasi(this.value,\'kabkot\',\'usulan\')">'
                        +'<option value="">Pilih Kabupaten / Kota</option>'
                    +'</select>'
                +'</td>'
                +'<td style="width: 30%">'
                    +'<select class="form-control input_select" id="kecamatan_usulan_'+newId+'" name="input_kecamatan_usulan['+newId+']" onchange="get_data_lokasi(this.value,\'kec\',\'usulan\')">'
                        +'<option value="">Pilih Kecamatan</option>'
                    +'</select>'
                +'</td>'
                +'<td style="width: 30%">'
                    +'<select class="form-control input_select" id="desa_usulan_'+newId+'" name="input_desa_usulan['+newId+']">'
                        +'<option value="">Pilih Desa</option>'
                    +'</select>'
                +'</td>'
                +'<td style="width: 70px" class="text-center">'
                    +'<button class="btn btn-warning" onclick="tambahLokasi(this);"><i class="dashicons dashicons-plus"></i></button>'
                +'</td>'
            +'</tr>';
        var tbody = jQuery('.input_lokasi_usulan > tbody');
        tbody.append(trNewUsulan);
        var tr = tbody.find('>tr');
        var length = tr.length-1;
        tr.map(function(i, b){
            if(i == 0){
                var html = '<button class="btn btn-warning" onclick="tambahLokasi(this); return false;"><i class="dashicons dashicons-plus"></i></button>';
            }else{
                var html = '<button class="btn btn-danger" onclick="hapusLokasi(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
            }
            jQuery(b).find('>td').last().html(html);
        });
        /** tambah input lokasi */
        var trNew = ''
            +'<tr data-id="'+newId+'">'
                +'<td>'
                    +'<select class="form-control" id="kabupaten_kota_'+newId+'" name="input_kabupaten_kota['+newId+']" onchange="get_data_lokasi(this.value,\'kabkot\',\'penetapan\')">'
                        +'<option value="">Pilih Kabupaten / Kota</option>'
                    +'</select>'
                +'</td>'
                +'<td style="width: 30%">'
                    +'<select class="form-control input_select" id="kecamatan_'+newId+'" name="input_kecamatan['+newId+']" onchange="get_data_lokasi(this.value,\'kec\',\'penetapan\')">'
                        +'<option value="">Pilih Kecamatan</option>'
                    +'</select>'
                +'</td>'
                +'<td style="width: 30%">'
                    +'<select class="form-control input_select" id="desa_'+newId+'" name="input_desa['+newId+']">'
                        +'<option value="">Pilih Desa</option>'
                    +'</select>'
                +'</td>'
            +'</tr>';
        var tbody = jQuery('.input_lokasi > tbody');
        tbody.append(trNew);
    }

    function hapusLokasi(that){
        var id = jQuery(that).closest('tr').attr('data-id');
        jQuery('.input_lokasi_usulan > tbody').find('tr[data-id="'+id+'"]').remove();
        jQuery('.input_lokasi > tbody').find('tr[data-id="'+id+'"]').remove();
    }

    function tambahSumberDana(that){
        var id = +jQuery('.input_sumber_dana_usulan > tbody tr').last().attr('data-id');
        var newId = id+1;
        var trNewUsulan = ''
            +'<tr data-id="'+newId+'">'
                +'<td style="width: 60%">'
                    +'<select class="form-control input_select_2" id="sumber_dana_usulan_'+newId+'" name="input_sumber_dana_usulan['+newId+']">'
                        +'<option value="">Pilih Sumber Dana</option>'
                    +'</select>'
                +'</td>'
                +'<td>'
                    +'<input class="form-control input_number" id="pagu_sumber_dana_usulan_'+newId+'" type="number" name="input_pagu_sumber_dana_usulan['+newId+']"/>'
                +'</td>'
                +'<td style="width: 70px" class="text-center">'
                    +'<button class="btn btn-warning" onclick="tambahSumberDana(this); return false;"><i class="dashicons dashicons-plus"></i></button>'
                +'</td>'
            +'</tr>';
        var tbody = jQuery('.input_sumber_dana_usulan > tbody');
        tbody.append(trNewUsulan);
        var tr = tbody.find('>tr');
        var length = tr.length-1;
        tr.map(function(i, b){
            if(i == 0){
                var html = '<button class="btn btn-warning" onclick="tambahSumberDana(this); return false;"><i class="dashicons dashicons-plus"></i></button>';
            }else{
                var html = '<button class="btn btn-danger" onclick="hapusSumberDana(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
            }
            jQuery(b).find('>td').last().html(html);
        });
        /** tambah input sumber dana */
        var trNew = ''
            +'<tr data-id="'+newId+'">'
                +'<td style="width: 60%">'
                    +'<select class="form-control input_select_2" id="sumber_dana_'+newId+'" name="input_sumber_dana['+newId+']">'
                        +'<option value="">Pilih Sumber Dana</option>'
                    +'</select>'
                +'</td>'
                +'<td>'
                    +'<input class="form-control input_number" id="pagu_sumber_dana_'+newId+'" type="number" name="input_pagu_sumber_dana['+newId+']"/>'
                +'</td>'
            +'</tr>';
        var tbody = jQuery('.input_sumber_dana > tbody');
        tbody.append(trNew);
    }

    function hapusSumberDana(that){
        var id = jQuery(that).closest('tr').attr('data-id');
        jQuery('.input_sumber_dana_usulan > tbody').find('tr[data-id="'+id+'"]').remove();
        jQuery('.input_sumber_dana > tbody').find('tr[data-id="'+id+'"]').remove();
    }

    function get_data_sub_unit(id_skpd){
        return new Promise(function(resolve, reject){
            if(typeof dataSubUnit == 'undefined'){
                jQuery('#wrap-loading').show();
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
                        jQuery('#wrap-loading').hide();
                        window.dataSubUnit = response;
                        jQuery("#input_sub_unit").html(dataSubUnit.table_content);
        			    jQuery('#input_sub_unit').select2({width: '100%'});
                        console.log(dataSubUnit.table_content);
        				// enable_button();
                        resolve();
        			}
        		});
            }else{
                resolve();
            }
        });
	}

    function get_sub_keg(that){
		let kode_sub_unit = jQuery(that).val();
        if(kode_sub_unit == ''){
            return;
        }
        jQuery("#wrap-loading").show();
		jQuery.ajax({
			method:'POST',
			url:"<?php echo admin_url('admin-ajax.php'); ?>",
			dataType:'json',
			data:{
				'action':'get_sub_keg_parent',
				'api_key': jQuery("#api_key").val(),
                'tahun_anggaran': tahun_anggaran,
				'kode_sub_unit': kode_sub_unit,
                'id_unit':id_unit
			},
			success:function(response){
				jQuery("#wrap-loading").hide();
				let option='<option value="">Pilih Sub Kegiatan</option>';
				response.data.map(function(value, index){
                    value.map(function(value_sub, index_sub){
                        option+='<option value="'+value_sub.id_sub_giat+'">'+value_sub.nama_sub_giat+'</option>';
                    });
                })

				jQuery("#sub_kegiatan").html(option);
                jQuery("#sub_kegiatan").select2({width: '100%'});
			}
		});
    }

    function get_indikator_sub_keg(that){
		let id_sub_keg = jQuery(that).val();
        if(id_sub_keg==''){
            return;
        }
        jQuery("#wrap-loading").show();
		jQuery.ajax({
			method:'POST',
			url:"<?php echo admin_url('admin-ajax.php'); ?>",
			dataType:'json',
			data:{
				'action':'get_indikator_sub_keg_parent',
				'api_key': jQuery("#api_key").val(),
                'tahun_anggaran': tahun_anggaran,
				'id_sub_keg' : id_sub_keg
			},
			success:function(response){
				jQuery("#wrap-loading").hide();
				let option='<option value="">Pilih Nama Indikator</option>';
				response.data.map(function(value, index){
                    option+='<option value="'+value.id_sub_keg+'">'+value.indikator+'</option>';
                })

                let optionSatuan='<option value="">Pilih Satuan</option>';
				response.data.map(function(value, index){
                    optionSatuan+='<option value="'+value.id_sub_keg+'">'+value.satuan+'</option>';
                })

				jQuery(".pagu_indi_sub_keg").html(option);
                jQuery(".pagu_indi_sub_keg").select2({width: '100%'});
                jQuery(".satuan_pagu_indi_sub_keg").html(optionSatuan);
			}
		});
    }

    function get_indikator_sub_keg_by_id(data){
		let id_sub_keg = data.id_sub_keg;
        if(id_sub_keg == ''){
            return;
        }
        jQuery("#wrap-loading").show();
        let id_input = data.id;
		jQuery.ajax({
			method:'POST',
			url:"<?php echo admin_url('admin-ajax.php'); ?>",
			dataType:'json',
			data:{
				'action':'get_indikator_sub_keg_parent',
				'api_key': jQuery("#api_key").val(),
                'tahun_anggaran': tahun_anggaran,
				'id_sub_keg' : id_sub_keg
			},
			success:function(response){
				jQuery("#wrap-loading").hide();
				let option='<option value="">Pilih Nama Indikator</option>';
				response.data.map(function(value, index){
                    option+='<option value="'+value.id_sub_keg+'">'+value.indikator+'</option>';
                })

                let optionSatuan='<option value="">Pilih Satuan</option>';
				response.data.map(function(value, index){
                    optionSatuan+='<option value="'+value.id_sub_keg+'">'+value.satuan+'</option>';
                })

				jQuery("#pagu_ind_sub_keg_usulan_"+id_input).html(option).select2({width: '100%'});
				jQuery("#pagu_indi_sub_keg_penetapan_"+id_input).html(option).select2({width: '100%'});
				jQuery("#satuan_pagu_indi_sub_keg_usulan_"+id_input).html(optionSatuan);
				jQuery("#satuan_pagu_indi_sub_keg_penetapan_"+id_input).html(optionSatuan);
			}
		});
    }

    function get_data_sumber_dana(){
        return new Promise(function(resolve, reject){
            if(typeof master_sumberdana == 'undefined'){
                jQuery("#wrap-loading").show();
                jQuery.ajax({
                    method: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    dataType: 'json',
                    data: {
                        'action': 'get_data_sumber_dana_renja',
                        'api_key': jQuery('#api_key').val(),
                        'tahun_anggaran': tahun_anggaran
                    },
                    success:function(response){
                        window.master_sumberdana = response.data;
                        jQuery("#wrap-loading").hide();
                        let option='<option value="">Pilih Sumber Dana</option>';
        				response.data.map(function(value, index){
                            option+='<option value="'+value.id_dana+'">'+value.nama_dana+'</option>';
                        })

        				jQuery(".sumber_dana_usulan").html(option);
                        jQuery(".sumber_dana_usulan").select2({width: '100%'});
                        jQuery(".sumber_dana").html(option);
                        jQuery(".sumber_dana").select2({width: '100%'});
                        resolve();
                    }
                });
            }else{
                resolve();
            }
        });
    }

    function get_data_lokasi(id_alamat,jenis_lokasi,status){
        return new Promise(function(resolve1, reject1){
            if(jenis_lokasi == ''){
                alert('Ada kesalahan,harap refresh halaman!')
                return resolve1(false);
            }
            jQuery("#wrap-loading").show();

            if(id_alamat == false){
                id_alamat = <?php echo $id_lokasi_prov; ?>;
            }

            if(status == undefined){
                status = 'all';
            }
            
            if(typeof lokasi == 'undefined'){
                window.lokasi = {};
            }
            if(typeof lokasi[jenis_lokasi] == 'undefined'){
                window.lokasi[jenis_lokasi] = {};
            }
            if(typeof lokasi[jenis_lokasi][id_alamat] == 'undefined'){
                window.lokasi[jenis_lokasi][id_alamat] = [];
            }
            new Promise(function(resolve, reject){
                if(lokasi[jenis_lokasi][id_alamat].length == 0){
                    jQuery.ajax({
                        method:'post',
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        dataType: 'json',
                        data: {
                            'action': 'get_data_lokasi_renja',
                            'api_key': jQuery('#api_key').val(),
                            'tahun_anggaran': tahun_anggaran,
                            'id_skpd': id_skpd,
                            'jenis_lokasi': jenis_lokasi,
                            'id_alamat':id_alamat
                        },
                        success: function(response){
                            window.lokasi[jenis_lokasi][id_alamat] = response;
                            resolve(lokasi[jenis_lokasi][id_alamat]);
                        }
                    });
                }else{
                    resolve(lokasi[jenis_lokasi][id_alamat]);
                }
            })
            .then(function(response){
                jQuery("#wrap-loading").hide();
                let option='<option value="">Pilih '+response.jenis_lokasi+'</option>';
                response.data.map(function(value, index){
                <?php if(!empty($id_lokasi_kokab)): ?>
                    var selected = '';
                    if(
                        value.id_alamat == <?php echo $id_lokasi_kokab; ?>
                        && jenis_lokasi == 'prov'
                    ){
                        selected = 'selected';
                        get_data_lokasi(value.id_alamat, 'kabkot');
                        option +='<option '+selected+' value="'+value.id_alamat+'">'+value.nama+'</option>';
                    }else{
                        option +='<option '+selected+' value="'+value.id_alamat+'">'+value.nama+'</option>';
                    }
                <?php else: ?>
                    option +='<option value="'+value.id_alamat+'">'+value.nama+'</option>';
                <?php endif; ?>
                });

                if(jenis_lokasi == 'kabkot'){
                    if(status == 'usulan'){
                        jQuery(".kecamatan_usulan").html(option);
                        jQuery(".kecamatan_usulan").select2({width: '100%'});
                    }else if(status == 'penetapan'){
                        jQuery(".kecamatan").html(option);
                        jQuery(".kecamatan").select2({width: '100%'});
                    }else{
                        jQuery(".kecamatan").html(option);
                        jQuery(".kecamatan").select2({width: '100%'});
                        jQuery(".kecamatan_usulan").html(option);
                        jQuery(".kecamatan_usulan").select2({width: '100%'});
                    }
                }else if(jenis_lokasi == 'kec'){
                    if(status == 'usulan'){
                        jQuery(".desa_usulan").html(option);
                        jQuery(".desa_usulan").select2({width: '100%'});
                    }else if(status == 'penetapan'){
                        jQuery(".desa").html(option);
                        jQuery(".desa").select2({width: '100%'});
                    }else{
                        jQuery(".desa").html(option);
                        jQuery(".desa").select2({width: '100%'});
                        jQuery(".desa_usulan").html(option);
                        jQuery(".desa_usulan").select2({width: '100%'});
                    }
                }else if(jenis_lokasi == 'prov'){
                    if(status == 'usulan'){
                        jQuery(".kabupaten_kota_usulan").html(option);
                        jQuery(".kabupaten_kota_usulan").select2({width: '100%'});
                    }else if(status == 'penetapan'){
                        jQuery(".kabupaten_kota").html(option);
                        jQuery(".kabupaten_kota").select2({width: '100%'});
                    }else{
                        jQuery(".kabupaten_kota").html(option);
                        jQuery(".kabupaten_kota").select2({width: '100%'});
                        jQuery(".kabupaten_kota_usulan").html(option);
                        jQuery(".kabupaten_kota_usulan").select2({width: '100%'});
                    }
                }
                return resolve1();
            });
        });
    }

    function submitTambahRenjaForm(){
        if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
            jQuery("#wrap-loading").show();
            let form = getFormData(jQuery("#form-renja"));
            jQuery.ajax({
                method:'post',
                url:'<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: {
                    'action': 'submit_tambah_renja',
                    'api_key': jQuery('#api_key').val(),
                    'data': JSON.stringify(form),
                    'tahun_anggaran': tahun_anggaran
                },
                success:function(response){
                    jQuery('#wrap-loading').hide();
                    jQuery('#modalTambahRenja').modal('hide');
                    alert(response.message);
                    refresh_page();
                }
            });
        }
    }

    function edit_renja(kode_sub_giat){
        get_data_sub_unit(id_skpd)
        .then(function(){
            get_data_sumber_dana()
            .then(function(){
                get_data_lokasi(false, 'prov')
                .then(function(){
                    jQuery('#wrap-loading').show();
                    jQuery.ajax({
                        method: 'post',
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        dataType: 'json',
                        data: {
                            'action': 'edit_renja',
                            'api_key': jQuery('#api_key').val(),
                            'kode_sub_giat': kode_sub_giat,
                            'tahun_anggaran': tahun_anggaran
                        },
                        success: function(response){
                            jQuery('#wrap-loading').hide();
                            jQuery('#pagu_sub_kegiatan_usulan').val(response.data.pagu_usulan);
                            jQuery('#pagu_sub_kegiatan_1_usulan').val(response.data.pagu_n_depan_usulan);
                            jQuery('#pagu_sub_kegiatan').val(response.data.pagu);
                            jQuery('#pagu_sub_kegiatan_1').val(response.data.pagu_n_depan);
                            jQuery('#catatan_usulan').val(response.data.catatan_usulan);
                            jQuery('#catatan').val(response.data.catatan);
                            let input_sub_unit = `<option selected value="${response.data.id_sub_skpd}">${response.data.nama_sub_skpd}</option>`;
                            jQuery('#input_sub_unit').html(input_sub_unit);
                            jQuery('#input_sub_unit').prop('disabled', true);
                            let input_sub_kegiatan = `<option selected value="${response.data.id_sub_giat}">${response.data.nama_sub_giat}</option>`;
                            jQuery('#sub_kegiatan').html(input_sub_kegiatan);
                            jQuery('#sub_kegiatan').prop('disabled', true);
                            jQuery('select[name="input_bulan_awal_usulan"] option[value="'+response.data.waktu_awal_usulan+'"]').attr("selected","selected");
                            jQuery('select[name="input_bulan_akhir_usulan"] option[value="'+response.data.waktu_akhir_usulan+'"]').attr("selected","selected");
                            jQuery('select[name="input_bulan_awal"] option[value="'+response.data.waktu_awal+'"]').attr("selected","selected");
                            jQuery('select[name="input_bulan_akhir"] option[value="'+response.data.waktu_akhir+'"]').attr("selected","selected");

                            /** Memunculkan data indikator sub kegiatan */
                            let optionIndikator='<option value="">Pilih Nama Indikator</option>';
                            response.data.master_sub_keg_indikator.map(function(value, index){
                                optionIndikator+='<option value="'+value.id_sub_keg+'">'+value.indikator+'</option>';
                            });
                            let optionSatuan='<option value="">Pilih Satuan</option>';
                            response.data.master_sub_keg_indikator.map(function(value, index){
                                optionSatuan+='<option value="'+value.id_sub_keg+'">'+value.satuan+'</option>';
                            });
                            jQuery("#pagu_ind_sub_keg_usulan_1").html(optionIndikator).select2({width: '100%'});
                            jQuery("#pagu_indi_sub_keg_penetapan_1").html(optionIndikator).select2({width: '100%'});
                            jQuery("#satuan_pagu_indi_sub_keg_usulan_1").html(optionSatuan).select2({width: '100%'});
                            jQuery("#satuan_pagu_indi_sub_keg_penetapan_1").html(optionSatuan).select2({width: '100%'});

                            response.data.indikator_sub_keg.map(function(value, index){
                                var id = index+1;
                                if(id > 1){
                                    var trNewUsulan = ''
                                    +'<tr data-id="'+id+'" header="1">'
                                        +'<td colspan="2" style="max-width: 100px;">'
                                            +'<select class="form-control pagu_indi_sub_keg input_select_2" data-edit="'+value.id+'" id="pagu_ind_sub_keg_usulan_'+id+'" name="input_indikator_sub_keg_usulan['+id+']">'
                                                +'<option value="">Pilih Nama Indikator</option>'
                                            +'</select>'
                                            +'<input type="hidden" id="ind_sub_keg_id_'+id+'" name="ind_sub_keg_id['+id+']" value="'+value.id+'">'
                                        +'</td>'
                                        +'<td style="width: 70px; vertical-align: middle;" class="text-center aksi" rowspan="2">'
                                            +'<button class="btn btn-warning" onclick="tambahIndikator(this); return false;"><i class="dashicons dashicons-plus"></i></button>'
                                        +'</td>'
                                    +'</tr>'
                                    +'<tr data-id="'+id+'">'
                                        +'<td style="width: 50%;">'
                                            +'<input class="form-control input_number" type="number" id="indikator_pagu_indi_sub_keg_usulan_'+id+'" name="input_target_usulan['+id+']" placeholder="Target Indikator"/>'
                                        +'</td>'
                                        +'<td style="width: 50%;">'
                                            +'<select class="form-control satuan_pagu_indi_sub_keg input_select_2" id="satuan_pagu_indi_sub_keg_usulan_'+id+'" name="input_satuan_usulan['+id+']">'
                                                +'<option value="">Pilih Satuan</option>'
                                            +'</select>'
                                        +'</td>'
                                    +'</tr>';
                                
                                    var tbody = jQuery('.indi_sub_keg_table_usulan > tbody');
                                    tbody.append(trNewUsulan);
                                    var tr = tbody.find('>tr');
                                    var length = tr.length-2;
                                    tr.map(function(i, b){
                                        var header = jQuery(b).attr('header');
                                        if(header == 1){
                                            if(i == 0){
                                                var html = '<button class="btn btn-warning" onclick="tambahIndikator(this); return false;"><i class="dashicons dashicons-plus"></i></button>';
                                            }else{
                                                var html = '<button class="btn btn-danger" onclick="hapusIndikator(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                                            }
                                            jQuery(b).find('>td').last().html(html);
                                        }
                                    });
                                    /** tambah input indi_sub_keg */
                                    var trNewUsulan = ''
                                        +'<tr data-id="'+id+'" header="1">'
                                            +'<td colspan="2" style="max-width: 100px;">'
                                                +' <select class="form-control pagu_indi_sub_keg input_select_2" id="pagu_indi_sub_keg_penetapan_'+id+'" name="input_indikator_sub_keg['+id+']">'
                                                    +'<option value="">Pilih Nama Indikator</option>'
                                                +'</select>'
                                            +'</td>'
                                        +'</tr>'
                                        +'<tr data-id="'+id+'">'
                                            +'<td style="width: 50%;">'
                                                +'<input class="form-control input_number" type="number" name="input_target['+id+']" id="indikator_pagu_indi_sub_keg_'+id+'" placeholder="Target Indikator"/>'
                                            +'</td>'
                                            +'<td style="width: 50%;">'
                                                +'<select class="form-control satuan_pagu_indi_sub_keg input_select_2" id="satuan_pagu_indi_sub_keg_penetapan_'+id+'" name="input_satuan['+id+']">'
                                                    +'<option value="">Pilih Satuan</option>'
                                                +'</select>'
                                            +'</td>'
                                        +'</tr>';

                                    var tbody = jQuery('.indi_sub_keg_table > tbody');
                                    tbody.append(trNewUsulan);
                                    jQuery("#pagu_ind_sub_keg_usulan_"+id).html(optionIndikator).select2({width: '100%'});
                                    jQuery("#pagu_indi_sub_keg_penetapan_"+id).html(optionIndikator).select2({width: '100%'});
                                    jQuery("#satuan_pagu_indi_sub_keg_usulan_"+id).html(optionSatuan).select2({width: '100%'});
                                    jQuery("#satuan_pagu_indi_sub_keg_penetapan_"+id).html(optionSatuan).select2({width: '100%'});
                                }

                                jQuery("#ind_sub_keg_id_"+id).val(value.id);
                                jQuery("#indikator_pagu_indi_sub_keg_usulan_"+id).val(value.targetoutput_usulan);
                                jQuery("#pagu_ind_sub_keg_usulan_"+id).val(value.id_indikator_sub_giat).trigger('change');
                                jQuery("#satuan_pagu_indi_sub_keg_usulan_"+id).val(value.id_indikator_sub_giat).trigger('change');
                                jQuery("#indikator_pagu_indi_sub_keg_"+id).val(value.targetoutput);
                                jQuery("#pagu_indi_sub_keg_penetapan_"+id).val(value.id_indikator_sub_giat).trigger('change');
                                jQuery("#satuan_pagu_indi_sub_keg_penetapan_"+id).val(value.id_indikator_sub_giat).trigger('change');
                            })
                            /** -- end -- */

                            /** Memunculkan data sumber dana */
                            let option_dana='<option value="">Pilih Sumber Dana</option>';
            				master_sumberdana.map(function(value, index){
                                option_dana+='<option value="'+value.id_dana+'">'+value.nama_dana+'</option>';
                            })
            				jQuery("#sumber_dana_usulan").html(option_dana);
                            jQuery("#sumber_dana").html(option_dana);
                            response.data.sumber_dana.map(function(value, index){
                                var id = index+1;
                                if(id > 1){
                                    var trNewUsulan = ''
                                    +'<tr data-id="'+id+'">'
                                        +'<td style="width: 60%">'
                                            +'<select class="form-control input_select_2" id="sumber_dana_usulan_'+id+'" name="input_sumber_dana_usulan['+id+']">'
                                                +'<option value="">Pilih Sumber Dana</option>'
                                            +'</select>'
                                        +'</td>'
                                        +'<td>'
                                            +'<input class="form-control input_number" id="pagu_sumber_dana_usulan_'+id+'" type="number" name="input_pagu_sumber_dana_usulan['+id+']"/>'
                                        +'</td>'
                                        +'<td style="width: 70px" class="text-center">'
                                            +'<button class="btn btn-warning" onclick="tambahSumberDana(this); return false;"><i class="dashicons dashicons-plus"></i></button>'
                                        +'</td>'
                                    +'</tr>';
                                    var tbody = jQuery('.input_sumber_dana_usulan > tbody');
                                    tbody.append(trNewUsulan);
                                    var tr = tbody.find('>tr');
                                    var length = tr.length-1;
                                    tr.map(function(i, b){
                                        if(i == 0){
                                            var html = '<button class="btn btn-warning" onclick="tambahSumberDana(this); return false;"><i class="dashicons dashicons-plus"></i></button>';
                                        }else{
                                            var html = '<button class="btn btn-danger" onclick="hapusSumberDana(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                                        }
                                        jQuery(b).find('>td').last().html(html);
                                    });
                                    var trNew = ''
                                        +'<tr data-id="'+id+'">'
                                            +'<td style="width: 60%">'
                                                +'<select class="form-control input_select_2" id="sumber_dana_'+id+'" name="input_sumber_dana['+id+']">'
                                                    +'<option value="">Pilih Sumber Dana</option>'
                                                +'</select>'
                                            +'</td>'
                                            +'<td>'
                                                +'<input class="form-control input_number" id="pagu_sumber_dana_'+id+'" type="number" name="input_pagu_sumber_dana['+id+']"/>'
                                            +'</td>'
                                        +'</tr>';
                                    var tbody = jQuery('.input_sumber_dana > tbody');
                                    tbody.append(trNew);
                                    jQuery("#sumber_dana_usulan_"+id).html(option_dana).select2({width: '100%'});
                                    jQuery("#sumber_dana_"+id).html(option_dana).select2({width: '100%'});
                                }
                                
                                jQuery("#sumber_dana_usulan_"+id).val(value.id_dana_usulan).trigger('change');
                                jQuery("#pagu_sumber_dana_usulan_"+id).val(value.pagu_dana_usulan);
                                jQuery("#sumber_dana_"+id).val(value.iddana).trigger('change');
                                jQuery("#pagu_sumber_dana_"+id).val(value.pagudana);
                            })
                            /** -- end -- */

                            /** Memunculkan data lokasi */
                            response.data.lokasi.map(function(value, index){
                                let urutan = index+1;
                                let id = value.id;
                                let option_kabkot='<option value="">Pilih Kabupaten/Kota</option>';
                                response.data.data_master_kabkot[id].map(function(value, index){
                                    option_kabkot+='<option value="'+value.id_alamat+'">'+value.nama+'</option>'
                                });
                                jQuery("#kabupaten_kota_usulan_"+urutan).html(option_kabkot);
                                jQuery("#kabupaten_kota_usulan_"+urutan).select2({width: '100%'});
                                let option_kec='<option value="">Pilih Kecamatan</option>';
                                response.data.data_master_kec[id].map(function(value, index){
                                    option_kec+='<option value="'+value.id_alamat+'">'+value.nama+'</option>'
                                });
                                jQuery("#kecamatan_usulan_"+urutan).html(option_kec);
                                jQuery("#kecamatan_usulan_"+urutan).select2({width: '100%'});
                                let option_desa='<option value="">Semua Desa</option>';
                                if(response.data.data_master_desa != undefined && response.data.data_master_desa[id] != undefined){
                                    option_desa='<option value="">Pilih Desa</option>';
                                    response.data.data_master_desa[id].map(function(value, index){
                                        option_desa+='<option value="'+value.id_alamat+'">'+value.nama+'</option>'
                                    });
                                }
                                jQuery("#desa_usulan_"+urutan).html(option_desa);
                                jQuery("#desa_usulan_"+urutan).select2({width: '100%'});
                                
                                jQuery("#kabupaten_kota_usulan_"+urutan).val(value.idkabkota_usulan).trigger('change');
                                if(value.idcamat_usulan != null){
                                    // jQuery("#kecamatan_usulan_"+urutan).val(value.idcamat_usulan).trigger('change');
                                }
                            })
                            /** -- end -- */

                            jQuery("#modalTambahRenja .modal-title").html("Edit Sub Kegiatan");
                            jQuery("#modalTambahRenja .submitBtn")
                                .attr("onclick", `submitEditRenjaForm('${kode_sub_giat}')`)
                                .attr("disabled", false)
                                .text("Simpan");
                            jQuery('#modalTambahRenja').modal('show');
                        }
                    })
                });
            });
        });
    }

    function submitEditRenjaForm(kode_sub_giat){
        if(confirm('Apakah anda yakin untuk mengubah data ini?')){
            jQuery('#wrap-loading').show();
            let form = getFormData(jQuery("#form-renja"));
            jQuery.ajax({
                method: 'post',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: {
                    'action': 'submit_edit_renja',
                    'api_key': jQuery('#api_key').val(),
                    'data': JSON.stringify(form),
                    'kode_sub_giat': kode_sub_giat,
                    'tahun_anggaran': tahun_anggaran
                },
                success: function(response){
                    jQuery('#wrap-loading').hide();
                    jQuery('#modalTambahRenja').modal('hide');
                    alert(response.message);
                    refresh_page();
                }
            })
        }
    }

    function delete_renja(kode_sub_giat){
        if(confirm('Apakah anda yakin untuk menghapus data ini?')){
            jQuery("#wrap-loading").show();
            jQuery.ajax({
                method: 'post',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: {
                    'action': 'delete_renja',
                    'api_key': jQuery('#api_key').val(),
                    'kode_sub_giat': kode_sub_giat,
                    'tahun_anggaran': tahun_anggaran
                },
                success:function(response){
                    jQuery('#wrap-loading').hide();
                    alert(response.message);
                    refresh_page();
                }
            })
        }
    }

    function edit_program(id_unik){
        jQuery("#modal-indikator-renja").find('.modal-body').html('');
        indikatorProgram(id_unik);
    }

    function indikatorProgram(data){
        jQuery('#wrap-loading').show();

        // jQuery.ajax({
        //     method: 'post',
        //     url: '<?php echo admin_url('admin-ajax.php'); ?>',
        //     dataType: 'json',
        //     data: {
        //         'action': 'get_indikator_program_renja',
        //         'api_key': jQuery('#api_key').val(),
        //         'data': data  
        //     },
        //     success:function(response){
                jQuery('#wrap-loading').hide();

                let html=""
					+'<div style="margin-top:10px">'
						+'<button type="button" class="btn btn-primary mb-2 btn-add-indikator-program" data-kodeprogram="12">'
							+'<i class="dashicons dashicons-plus" style="margin-top: 2px;"></i> Tambah Indikator'
						+'</button>'
					+'</div>'
          			+'<table class="table">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('#nama-skpd').text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Tujuan</th>'
          						+'<th>'+jQuery('#nav-tujuan tr[kodetujuan="'+jQuery("#nav-sasaran .btn-tambah-sasaran").data("kodetujuan")+'"]').find('td').eq(2).text()+'</th>'
          					+'</tr>'
          					+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Sasaran</th>'
          						+'<th>'+jQuery('#nav-sasaran tr[kodesasaran="'+jQuery("#nav-program .btn-tambah-program").data("kodesasaran")+'"]').find('td').eq(1).text()+'</th>'
          					+'</tr>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Program</th>'
	          					+'<th>'+jQuery('#nav-program tr[kodeprogram="12"]').find('td').eq(1).text()+'</th>'
	          				+'</tr>'
	          				// +'<tr>'
	          				// 	+'<th colspan=2>'
	          				// 		+'<table>'
		          			// 			+'<thead>'
				      		// 				+'<tr>'
				      		// 					+'<th class="text-center">Pagu Tahun 1212</th>'
				      		// 					+'<th class="text-center" style="width:15%">Catatan</th>'
				      		// 				+'</tr>'
		          			// 			+'</thead>'
		          			// 			+'<tbody style="font-weight: normal;">'
			  				// 				+'<tr>'
                            //                     +'<td>hai</td>'
                            //                 +'</tr>'
                            //                 +'<tr>'
                            //                     +'<td>hai</td>'
                            //                 +'</tr>'
		          			// 			+'</tbody>'
	          				// 		+'</table>'
	          				// 	+'</th>'
	          				// +'</tr>'
	          			+'</thead>'
          			+'</table>'
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center'>No</th>"
								+"<th class='text-center'>Indikator</th>"
								+"<th class='text-center'>Satuan</th>"
								+"<th class='text-center'>Awal</th>"
								+"<th class='text-center'>Akhir</th>"
								+"<th class='text-center'>Catatan</th>"
								+"<th class='text-center'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_program'>";
						// response.data.map(function(value, index){
		          		// 	for(var i in value){
		          		// 		if(
		          		// 			value[i] == 'null'
		          		// 			|| value[i] == null
		          		// 		){
		          		// 			value[i] = '';
		          		// 		}
		          		// 	}
		          			html +=''
		          				+"<tr>"
					          		+"<td class='text-center' rowspan='2'>1</td>"
					          		+"<td>xx</td>"
					          		+"<td>qw</td>"
					          		+"<td class='text-center'>wq</td>"
									+"<td class='text-center'>as</td>"
									+"<td class='text-right'>as</td>"
					          		+"<td class='text-center'>asa</td>"
					          		+"<td><b>Penetapan</b><br>asa</td>"
					          		+"<td class='text-center' rowspan='2'>"
					          			+"<a href='#' class='btn btn-success btn-edit-indikator-program' data-kodeprogram='12' data-id='12'><i class='dashicons dashicons-edit' style='margin-top: 2px;' title='Edit Indikator Program'></i></a>&nbsp"
										+"<a href='#' class='btn btn-danger btn-delete-indikator-program' data-kodeprogram='12' data-id='12' title='Hapus Indikator Program'><i class='dashicons dashicons-trash' style='margin-top: 2px;'></i></a>&nbsp;"
					          		+"</td>"
					          	+"</tr>"
		          				+"<tr>"
					          		+"<td>as</td>"
					          		+"<td>dsa</td>"
					          		+"<td class='text-center'>das</td>"
									+"<td class='text-center'>313</td>"
									+"<td class='text-right'>123</td>"
					          		+"<td class='text-center'>22</td>"
					          		+"<td><b>Usulan</b><br>ewe</td>"
					          	+"</tr>";
			          		// });
		          	html+=''
		          		+'</tbody>'
		          	+'</table>';

                jQuery('#modal-indikator-renja').find('.modal-title').html('Indikator Program');
                jQuery('#modal-indikator-renja').find('.modal-body').html(html);
                jQuery('#modal-indikator-renja').find('.modal-dialog').css('maxWidth','1250px');
                jQuery('#modal-indikator-renja').find('.modal-dialog').css('width','100%');
                jQuery('#modal-indikator-renja').modal('show');
            // }
        // })
    }

    function getFormData($form){
	    let unindexed_array = $form.serializeArray();
        var data = {};
        unindexed_array.map(function(b, i){
            data[b.name] = b.value;
        })
        console.log('data', data);
        return data;
	}

    function refresh_page(){
        if(confirm('Ada data yang berubah, apakah mau merefresh halaman ini?')){
            window.location = "";
        }
	}

    function set_waktu(){
        jQuery('.bulan_awal option[value=1]').attr('selected','selected');
        jQuery('.bulan_akhir option[value=12]').attr('selected','selected');
    }
</script>