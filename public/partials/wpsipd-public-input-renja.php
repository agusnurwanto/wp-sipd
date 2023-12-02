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

$nilai_pergeseran_renja = get_option('_nilai_pergeseran_renja');
$tombol_copy_data_renja_sipd = get_option('_crb_show_copy_data_renja_settings');

$tahun_anggaran = '2022';
$namaJadwal = '-';
$mulaiJadwal = '-';
$selesaiJadwal = '-';
$relasi_perencanaan = '-';
$id_tipe_relasi = '-';
$id_unit = '';

$disabled = 'readonly';
$is_admin = false;
$user_id = um_user( 'ID' );
$user_meta = get_userdata($user_id);
$js_check_admin = 0;
if(
    in_array("administrator", $user_meta->roles)
    || in_array("mitra_bappeda", $user_meta->roles)
){
	$is_admin = true;
	$disabled='';
    $js_check_admin = 1;
}

$data_skpd = $wpdb->get_row($wpdb->prepare("
    select 
        nama_skpd,
        id_unit,
        is_skpd 
    from data_unit
    where 
        id_skpd=%d 
        and tahun_anggaran=%d
        and active=1
    order by id ASC
", $input['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
$id_unit = (!empty($data_skpd['id_unit'])) ? $data_skpd['id_unit'] : '';

$url_pendapatan = $this->generatePage('Halaman Pendapatan '.$data_skpd['nama_skpd'].' | '.$input['tahun_anggaran'], $input['tahun_anggaran'], '[halaman_pendapatan id_skpd="'.$input['id_skpd'].'" tahun_anggaran="'.$input['tahun_anggaran'].'"]');
$url_pembiayaan_penerimaan = $this->generatePage('Halaman Penerimaan '.$data_skpd['nama_skpd'].' | '.$input['tahun_anggaran'], $input['tahun_anggaran'], '[halaman_pembiayaan_penerimaan id_skpd="'.$input['id_skpd'].'" tahun_anggaran="'.$input['tahun_anggaran'].'"]');
$url_pembiayaan_pengeluaran = $this->generatePage('Halaman Pengeluaran '.$data_skpd['nama_skpd'].' | '.$input['tahun_anggaran'], $input['tahun_anggaran'], '[halaman_pembiayaan_pengeluaran id_skpd="'.$input['id_skpd'].'" tahun_anggaran="'.$input['tahun_anggaran'].'"]');

$id_sub_skpd = (!empty($_GET['id_sub_skpd'])) ?: 0;
$hide_usulan = (!empty($_GET['hide_usulan'])) ?: 0;
$hide_penetapan = (!empty($_GET['hide_penetapan'])) ?: 0;

if(!empty($id_sub_skpd)){
    $sql = "
        SELECT 
            s.*,
            k.active as status_sub_keg
        FROM data_sub_keg_bl_lokal s
        LEFT JOIN data_prog_keg k on s.id_sub_giat=k.id_sub_giat
            AND s.tahun_anggaran=k.tahun_anggaran
        WHERE s.id_skpd=%d
            AND s.tahun_anggaran=%d
            AND s.active=1
            ORDER BY s.id_sub_skpd ASC, s.kode_giat ASC, s.kode_sub_giat ASC";
}else{
    $sql = "
        SELECT 
            s.*,
            k.active as status_sub_keg
        FROM data_sub_keg_bl_lokal s
        LEFT JOIN data_prog_keg k on s.id_sub_giat=k.id_sub_giat
            AND s.tahun_anggaran=k.tahun_anggaran
        WHERE s.id_sub_skpd=%d
            AND s.tahun_anggaran=%d
            AND s.active=1
            ORDER BY s.kode_giat ASC, s.kode_sub_giat ASC";
}

$subkeg = $wpdb->get_results($wpdb->prepare($sql,$input['id_skpd'], $input['tahun_anggaran']), ARRAY_A);

$cek_jadwal = $this->validasi_jadwal_perencanaan('renja',$input['tahun_anggaran']);
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
    $idJadwalRenja = $jadwal_lokal[0]['id_jadwal_lokal'];
    $jenisJadwal = $jadwal_lokal[0]['jenis_jadwal'];

    if(
        $jenisJadwal == 'penetapan' 
        && (
            in_array("administrator", $user_meta->roles)
            || in_array("mitra_bappeda", $user_meta->roles)
        )
    ){
        /** Penetapan */
        $mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
        $selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];
        $awal = new DateTime($mulaiJadwal);
        $akhir = new DateTime($selesaiJadwal);
        $now = new DateTime(date('Y-m-d H:i:s'));

        if($now >= $awal && $now <= $akhir){
            if($is_admin){
                $add_renja .='<a style="margin-left: 10px;" onclick="copy_usulan_all(); return false;" href="#" class="btn btn-danger">Copy Data Usulan ke Penetapan</a>';
                if($tombol_copy_data_renja_sipd == true){
                    $add_renja .='<a style="margin-left: 10px;" data-toggle="modal" data-target="#modal-copy-renja-sipd" href="#" class="btn btn-danger">Copy Data Renja SIPD ke Lokal</a>';
                }
            }
            $add_renja .= '<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RENJA</a>';
            if(!empty($jadwal_lokal[0]['relasi_perencanaan'])){
                $add_renja .= '<a style="margin-left: 10px;" id="copy-data-renstra-skpd" data-jadwal="'.$idJadwalRenja.'" data-skpd="'.$input['id_skpd'].'" onclick="return false;" href="#" class="btn btn-danger">Copy Data Renstra per SKPD</a>';
            }
            $add_renja .='</br></br><a style="margin-left: 10px;" target="_blank" id="tambah-data" href="'.$url_pendapatan.'" class="btn btn-info">Pendapatan</a>';
            $add_renja .='<div class="btn-group" style="margin-left: 10px;">';
            $add_renja .='<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Pembiayaan</button>';
            $add_renja .='<div class="dropdown-menu"><a class="dropdown-item" target="_blank" href="'.$url_pembiayaan_penerimaan.'">Penerimaan</a><a class="dropdown-item" target="_blank" href="'.$url_pembiayaan_pengeluaran.'">Pengeluaran</a></div>';
            $add_renja .='</div>';
        }
        /** WARNING!!! */
        /** Jika ada perubahan di bagian code penetapan harus disesuaikan dengan code di bagian usulan juga. Begitupun sebaliknya */
    }else if($jenisJadwal == 'usulan'){
        /** Usulan */
        $mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
        $selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];
        $awal = new DateTime($mulaiJadwal);
        $akhir = new DateTime($selesaiJadwal);
        $now = new DateTime(date('Y-m-d H:i:s'));
        
        if($now >= $awal && $now <= $akhir){
            if($is_admin){
                $add_renja .='<a style="margin-left: 10px;" onclick="copy_usulan_all(); return false;" href="#" class="btn btn-danger">Copy Data Usulan ke Penetapan</a>';
                if($tombol_copy_data_renja_sipd == true){
                    $add_renja .='<a style="margin-left: 10px;" data-toggle="modal" data-target="#modal-copy-renja-sipd" href="#" class="btn btn-danger">Copy Data Renja SIPD ke Lokal</a>';
                }
            }
            $add_renja .= '<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RENJA</a>';
            if(!empty($jadwal_lokal[0]['relasi_perencanaan'])){
                $add_renja .= '<a style="margin-left: 10px;" id="copy-data-renstra-skpd" data-jadwal="'.$idJadwalRenja.'" data-skpd="'.$input['id_skpd'].'" onclick="return false;" href="#" class="btn btn-danger">Copy Data Renstra per SKPD</a>';
            }
            $add_renja .='</br></br><a style="margin-left: 10px;" target="_blank" id="tambah-data" href="'.$url_pendapatan.'" class="btn btn-info">Pendapatan</a>';
            $add_renja .='<div class="btn-group" style="margin-left: 10px;">';
            $add_renja .='<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Pembiayaan</button>';
            $add_renja .='<div class="dropdown-menu"><a class="dropdown-item" target="_blank" href="'.$url_pembiayaan_penerimaan.'">Penerimaan</a><a class="dropdown-item" target="_blank" href="'.$url_pembiayaan_pengeluaran.'">Pengeluaran</a></div>';
            $add_renja .='</div>';
        }
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
$nama_sub_skpd = "";
$data_all = array(
    'total' => 0,
    'pagu_sipd' => 0,
    'total_usulan' => 0,
    'total_n_plus' => 0,
    'total_n_plus_usulan' => 0,
    'data' => array(),
    'total_pergeseran' => 0,
    'total_usulan_pergeseran' => 0
);
$data_rekap_sumber_dana = array();
$status_pergeseran_renja = '';
$thead_pergeseran = '';
$thead_pergeseran1 = 6;
$thead_pergeseran2 = '';
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

    $dana_sub_giat = $wpdb->get_results($wpdb->prepare("
        SELECT 
            dsk.*,
            dsd.kode_dana AS kode_dana_dsd
        FROM data_dana_sub_keg_lokal AS dsk
        LEFT JOIN data_sumber_dana AS dsd
        ON dsd.id_dana=dsk.iddana
        WHERE dsk.tahun_anggaran=%d
            AND dsk.active=1
            AND dsk.kode_sbl=%s
            AND dsd.tahun_anggaran=%d
            AND dsd.active=1
        ORDER BY dsk.id ASC
    ", $input['tahun_anggaran'], $sub['kode_sbl'], $input['tahun_anggaran']), ARRAY_A);

    if(!empty($dana_sub_giat)){
        foreach ($dana_sub_giat as $v_dana) {
            if(empty($data_rekap_sumber_dana['data'][$sub['id_sub_skpd']][$v_dana['iddana']])){
                $data_rekap_sumber_dana['data'][$sub['id_sub_skpd']][$v_dana['iddana']] = array(
                    'nama' => $v_dana['namadana'],
                    'iddana' => $v_dana['iddana'],
                    'kode_dana' => $v_dana['kode_dana_dsd'],
                    'total' => 0,
                    'total_usulan' => 0,
                    'data' => $v_dana
                );
            }
        
            $data_rekap_sumber_dana['data'][$sub['id_sub_skpd']][$v_dana['iddana']]['total'] += $v_dana['pagudana'];
            $data_rekap_sumber_dana['data'][$sub['id_sub_skpd']][$v_dana['iddana']]['total_usulan'] += $v_dana['pagu_dana_usulan'];
        }
    }
    
    $status_pergeseran_renja = $jadwal_lokal[0]['status_jadwal_pergeseran'];
    $pagu_pergeseran = 0;
    $pagu_usulan_pergeseran = 0;
    $thead_pergeseran = '';
    $thead_pergeseran1 = 6;
    $thead_pergeseran2 = '';
    if($status_pergeseran_renja == 'tampil'){
        $thead_pergeseran1 = 7;
        $thead_pergeseran2 = '<th style="padding: 0; border: 0; width:130px"></th>';
        $data_pagu_pergeseran = $wpdb->get_results($wpdb->prepare("
            select 
                nama_sub_giat as nama_sub_giat_pergeseran,
                pagu as pagu_pergeseran,
                pagu_usulan as pagu_usulan_pergeseran
            from data_sub_keg_bl_lokal_history 
            where tahun_anggaran=%d
                AND active=1
                AND kode_sbl=%s
                AND id_jadwal=%d
            order by id ASC
        ", $input['tahun_anggaran'], $sub['kode_sbl'], $jadwal_lokal[0]['id_jadwal_pergeseran']), ARRAY_A);
        if(!empty($data_pagu_pergeseran)){
            $pagu_pergeseran = $data_pagu_pergeseran[0]['pagu_pergeseran'];
            $pagu_usulan_pergeseran = $data_pagu_pergeseran[0]['pagu_usulan_pergeseran'];
        }
        $thead_pergeseran ='<td class="kanan bawah text_tengah text_blok" rowspan="2">Pagu Sebelum (Rp.)</td>';
    }

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
            'data'  => array(),
            'total_pergeseran' => 0,
            'total_usulan_pergeseran' => 0
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
            'data'  => array(),
            'total_pergeseran' => 0,
            'total_usulan_pergeseran' => 0
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
            'data'  => array(),
            'total_pergeseran' => 0,
            'total_usulan_pergeseran' => 0
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
            'data'  => array(),
            'total_pergeseran' => 0,
            'total_usulan_pergeseran' => 0
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
            'data'  => array(),
            'total_pergeseran' => 0,
            'total_usulan_pergeseran' => 0
        );
    }
    if(empty($data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']])){
        $nama = explode(' ', $sub['nama_sub_giat']);
        unset($nama[0]);
        $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']] = array(
            'nama'  => implode(' ', $nama),
            'total' => 0,
            'total_pergeseran' => 0,
            'total_n_plus' => 0,
            'total_usulan' => 0,
            'total_usulan_pergeseran' => 0,
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

    /** Total Dari Pergeseran */
    $data_all['total_pergeseran'] += $pagu_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['total_pergeseran'] += $pagu_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total_pergeseran'] += $pagu_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_pergeseran'] += $pagu_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_pergeseran'] += $pagu_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_pergeseran'] += $pagu_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_pergeseran'] += $pagu_pergeseran;

    $data_all['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub['id_sub_skpd']]['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_n_plus'] += $sub['pagu_n_depan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_n_plus'] += $sub['pagu_n_depan'];

    $data_all['total_usulan'] += $sub['pagu_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['total_usulan'] += $sub['pagu_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total_usulan'] += $sub['pagu_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_usulan'] += $sub['pagu_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_usulan'] += $sub['pagu_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_usulan'] += $sub['pagu_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_usulan'] += $sub['pagu_usulan'];

    $data_all['total_usulan_pergeseran'] += $pagu_usulan_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['total_usulan_pergeseran'] += $pagu_usulan_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total_usulan_pergeseran'] += $pagu_usulan_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_usulan_pergeseran'] += $pagu_usulan_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_usulan_pergeseran'] += $pagu_usulan_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_usulan_pergeseran'] += $pagu_usulan_pergeseran;
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_usulan_pergeseran'] += $pagu_usulan_pergeseran;

    $data_all['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_n_plus_usulan'] += $sub['pagu_n_depan_usulan'];

    $data_all['pagu_sipd'] += $sub_keg_sipd['pagu'];
    $data_all['data'][$sub['id_sub_skpd']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
    $data_all['data'][$sub['id_sub_skpd']]['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['pagu_sipd'] += $sub_keg_sipd['pagu'];
}

$total_sub_keg = 0;
$total_sumber_dana = '-';
$body = '';
foreach ($data_all['data'] as $sub_skpd) {
    $pagu_unit_sipd = $sub_skpd['pagu_sipd'];
    $warning = '';
    if($sub_skpd['total'] != $pagu_unit_sipd){
        $warning = 'background: #f9d9d9;';
    }
    if($status_pergeseran_renja == 'tampil'){
        $body_pergeseran1 = '<td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($sub_skpd['total_pergeseran'],0,",",".").'</span><span class="nilai_usulan">'.number_format($sub_skpd['total_usulan_pergeseran'],0,",",".").'</span></td>';
        $thead_pergeseran_unit = 20;
    }else{
        $body_pergeseran1 = '';
        $thead_pergeseran_unit = 19;
    }
    
    $body .= '
        <tr tipe="unit">
            <td class="kiri kanan bawah text_blok" colspan="'.$thead_pergeseran_unit.'">Unit Organisasi : '.$sub_skpd['nama_skpd'].'</td>
            <td class="kanan bawah hide-print"></td>
            <td class="kanan bawah hide-print"></td>
            <td colspan="3" class="kanan bawah text_tengah hide-print"></td>
        </tr>
        <tr tipe="sub_unit">
            <td class="kiri kanan bawah text_blok"></td>
            <td class="kanan bawah text_blok" colspan="12">Sub Unit Organisasi : '.$sub_skpd['nama'].'</td>
            '.$body_pergeseran1.'
            <td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($sub_skpd['total'],0,",",".").'</span><span class="nilai_usulan">'.number_format($sub_skpd['total_usulan'],0,",",".").'</span></td>
            <td class="kanan bawah" colspan="4"></td>
            <td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($sub_skpd['total_n_plus'],0,",",".").'</span><span class="nilai_usulan">'.number_format($sub_skpd['total_n_plus_usulan'],0,",",".").'</span></td>
            <td class="kanan bawah hide-print"></td>
            <td style="'.$warning.'" class="kanan bawah text_kanan hide-print">'.number_format($pagu_unit_sipd,0,",",".").'</td>
            <td colspan="3" class="kanan bawah text_tengah hide-print"></td>
        </tr>
    ';
    foreach ($sub_skpd['data'] as $kd_urusan => $urusan) {
        if($status_pergeseran_renja == 'tampil'){
            $thead_urusan_pergeseran = 15;
        }else{
            $thead_urusan_pergeseran = 14;
        }
        $body .= '
            <tr tipe="urusan" kode="'.$urusan['sub']['kode_sbl'].'">
                <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
                <td class="kanan bawah"></td>
                <td class="kanan bawah"></td>
                <td class="kanan bawah"></td>
                <td class="kanan bawah"></td>
                <td class="kanan bawah text_blok" colspan="'.$thead_urusan_pergeseran.'">'.$urusan['nama'].'</td>
                <td class="kanan bawah hide-print"></td>
                <td class="kanan bawah hide-print"></td>
                <td colspan="3" class="kanan bawah text_tengah hide-print"></td>
            </tr>
        ';
        foreach ($urusan['data'] as $kd_bidang => $bidang) {
            $kd_bidang = explode('.', $kd_bidang);
            $kd_bidang = $kd_bidang[count($kd_bidang)-1];
            $pagu_bidang_sipd = $bidang['pagu_sipd'];
            $warning = '';
            if($bidang['total'] != $pagu_bidang_sipd){
                $warning = 'background: #f9d9d9;';
            }
            $body_pergeseran2 = ($status_pergeseran_renja == 'tampil') ? '<td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($bidang['total_pergeseran'],0,",",".").'</span><span class="nilai_usulan">'.number_format($bidang['total_usulan_pergeseran'],0,",",".").'</span></td>' : '';
            $body .= '
                <tr tipe="bidang" kode="'.$bidang['sub']['kode_sbl'].'">
                    <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
                    <td class="kanan bawah text_blok">'.$kd_bidang.'</td>
                    <td class="kanan bawah"></td>
                    <td class="kanan bawah"></td>
                    <td class="kanan bawah"></td>
                    <td class="kanan bawah text_blok" colspan="8">'.$bidang['nama'].'</td>
                    '.$body_pergeseran2.'
                    <td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($bidang['total'],0,",",".").'</span><span class="nilai_usulan">'.number_format($bidang['total_usulan'],0,",",".").'</span></td>
                    <td class="kanan bawah" colspan="4"></td>
                    <td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($bidang['total_n_plus'],0,",",".").'</span><span class="nilai_usulan">'.number_format($bidang['total_n_plus_usulan'],0,",",".").'</span></td>
                    <td class="kanan bawah hide-print"></td>
                    <td style="'.$warning.'" class="kanan bawah text_kanan hide-print">'.number_format($pagu_bidang_sipd,0,",",".").'</td>
                    <td colspan="3" class="kanan bawah text_tengah hide-print"></td>
                </tr>
            ';
            foreach ($bidang['data'] as $kd_program => $program) {
                $kd_program = explode('.', $kd_program);
                $kd_program = $kd_program[count($kd_program)-1];

                $tombol_aksi = '';
                if(!empty($add_renja)){
                    $tombol_aksi = '<button class="btn-sm btn-warning" style="margin: 1px;" onclick="edit_program(\''.$program['sub']['kode_sbl'].'\');" title="Edit Program"><i class="dashicons dashicons-edit"></i></button>';
                }
                $tombol_aksi .= '<button class="btn-sm btn-primary" style="margin: 1px;" onclick="detail_program(\''.$program['sub']['kode_sbl'].'\')" title="Detail Program"><i class="dashicons dashicons-ellipsis"></i></button>';
                $data_check_program = explode('.', $program['sub']['kode_sbl']);
                $data_check_program = $data_check_program[0].'.'.$data_check_program[1].'.'.$data_check_program[2];
                $pagu_prog_sipd = $program['pagu_sipd'];
                $warning = '';
                if($program['total'] != $pagu_prog_sipd){
                    $warning = 'background: #f9d9d9;';
                }
                $body_pergeseran3 = ($status_pergeseran_renja == 'tampil') ? '<td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($program['total_pergeseran'],0,",",".").'</span><span class="nilai_usulan">'.number_format($program['total_usulan_pergeseran'],0,",",".").'</span></td>' : '';
                $body .= '
                    <tr tipe="program" kode="'.$program['sub']['kode_sbl'].'" checkprogram="'.$data_check_program.'">
                        <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
                        <td class="kanan bawah text_blok">'.$kd_bidang.'</td>
                        <td class="kanan bawah text_blok">'.$kd_program.'</td>
                        <td class="kanan bawah"></td>
                        <td class="kanan bawah"></td>
                        <td class="kanan bawah text_blok" colspan="8">'.$program['nama'].'</td>
                        '.$body_pergeseran3.'
                        <td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($program['total'],0,",",".").'</span><span class="nilai_usulan">'.number_format($program['total_usulan'],0,",",".").'</span></td>
                        <td class="kanan bawah" colspan="4"></td>
                        <td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($program['total_n_plus'],0,",",".").'</span><span class="nilai_usulan">'.number_format($program['total_n_plus_usulan'],0,",",".").'</span></td>
                        <td class="kanan bawah text_tengah hide-print">'.$tombol_aksi.'</td>
                        <td style="'.$warning.'" class="kanan bawah text_kanan hide-print">'.number_format($pagu_prog_sipd,0,",",".").'</td>
                        <td colspan="3" class="kanan bawah text_tengah hide-print"></td>
                    </tr>
                ';
                foreach ($program['data'] as $kd_giat => $giat) {
                    $kd_giat = explode('.', $kd_giat);
                    $kd_giat = $kd_giat[count($kd_giat)-2].'.'.$kd_giat[count($kd_giat)-1];
                    
                    $tombol_aksi = '';
                    if(!empty($add_renja)){
                        $tombol_aksi = '<button class="btn-sm btn-warning" style="margin: 1px;" onclick="edit_kegiatan(\''.$giat['sub']['kode_sbl'].'\');" title="Edit Kegiatan"><i class="dashicons dashicons-edit"></i></button>';
                    }
                    $tombol_aksi .= '<button class="btn-sm btn-primary" style="margin: 1px;" onclick="detail_kegiatan(\''.$giat['sub']['kode_sbl'].'\');" title="Detail Kegiatan"><i class="dashicons dashicons-ellipsis"></i></button>';

                    $pagu_keg_sipd = $giat['pagu_sipd'];
                    $warning = '';
                    if($giat['total'] != $pagu_keg_sipd){
                        $warning = 'background: #f9d9d9;';
                    }
                    $body_pergeseran4 = ($status_pergeseran_renja == 'tampil') ? '<td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($giat['total_pergeseran'],0,",",".").'</span><span class="nilai_usulan">'.number_format($giat['total_usulan_pergeseran'],0,",",".").'</span></td>' : '';
                    $body .= '
                        <tr tipe="kegiatan" kode="'.$giat['sub']['kode_sbl'].'">
                            <td class="kiri kanan bawah text_blok">'.$kd_urusan.'</td>
                            <td class="kanan bawah text_blok">'.$kd_bidang.'</td>
                            <td class="kanan bawah text_blok">'.$kd_program.'</td>
                            <td class="kanan bawah text_blok">'.$kd_giat.'</td>
                            <td class="kanan bawah"></td>
                            <td class="kanan bawah" colspan="8">'.$giat['nama'].'</td>
                            '.$body_pergeseran4.'
                            <td class="kanan bawah text_blok text_kanan"><span class="nilai_penetapan">'.number_format($giat['total'],0,",",".").'</span><span class="nilai_usulan">'.number_format($giat['total_usulan'],0,",",".").'</span></td>
                            <td class="kanan bawah" colspan="4"></td>
                            <td class="kanan bawah text_blok text_kanan"><span class="nilai_penetapan">'.number_format($giat['total_n_plus'],0,",",".").'</span><span class="nilai_usulan">'.number_format($giat['total_n_plus_usulan'],0,",",".").'</span></td>
                            <td class="kanan bawah text_tengah hide-print">'.$tombol_aksi.'</td>
                            <td style="'.$warning.'" class="kanan bawah text_kanan hide-print">'.number_format($pagu_keg_sipd,0,",",".").'</td>
                            <td colspan="3" class="kanan bawah text_tengah hide-print"></td>
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
                                $capaian_prog[] = '<span class="nilai_penetapan">'.$v_sub['capaianteks'].'</span><span class="nilai_usulan">'.$v_sub['capaianteks_usulan'].'</span>';
                                $target_capaian_prog[] = '<span class="nilai_penetapan">'.$v_sub['targetcapaianteks'].'</span><span class="nilai_usulan">'.$v_sub['targetcapaianteks_usulan'].'</span>';
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
                                $output_giat[] = '<span class="nilai_penetapan">'.$v_sub['outputteks'].'</span><span class="nilai_usulan">'.$v_sub['outputteks_usulan'].'</span>';
                                $target_output_giat[] = '<span class="nilai_penetapan">'.$v_sub['targetoutputteks'].'</span><span class="nilai_usulan">'.$v_sub['targetoutputteks_usulan'].'</span>';
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
                                $target_output_sub_giat[] = '<span class="nilai_penetapan">'.$v_sub['targetoutputteks'].'</span><span class="nilai_usulan">'.$v_sub['targetoutputteks_usulan'].'</span>';
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
                                // cek jika ada sumber dana di penetapan
                                if(!empty($v_dana['namadana'])){
                                    $dana_sub_giat = explode('] - ', $v_dana['namadana']);
                                    if(!empty($dana_sub_giat[1])){
                                        $dana_sub_giat_array[] = $dana_sub_giat[1];
                                    }else{
                                        $dana_sub_giat_array[] = $v_dana['namadana'];
                                    }
                                // cek jika ada sumber dana di usulan
                                }else if(!empty($v_dana['nama_dana_usulan'])){
                                    $dana_sub_giat = explode('] - ', $v_dana['nama_dana_usulan']);
                                    if(!empty($dana_sub_giat[1])){
                                        $dana_sub_giat_array[] = $dana_sub_giat[1];
                                    }else{
                                        $dana_sub_giat_array[] = $v_dana['nama_dana_usulan'];
                                    }
                                }
                            }
                        }
                        $dana_sub_giat = implode('<br>', $dana_sub_giat_array);

                        $catatan = '<span class="nilai_penetapan">'.$sub_giat['data']['catatan'].'</span><span class="nilai_usulan">'.$sub_giat['data']['catatan_usulan'].'</span>';
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

                        $tbody_pergeseran = ($status_pergeseran_renja == 'tampil') ? '<td class="kanan bawah text_kanan"><span class="nilai_penetapan">'.number_format($sub_giat['total_pergeseran'],0,",",".").'</span><span class="nilai_usulan">'.number_format($sub_giat['total_usulan_pergeseran'],0,",",".").'</span></td>' : '';

                        $kode_sbl = $sub_giat['data']['kode_sbl'];
                        $url_rka_lokal = $this->generatePage('Data RKA Lokal | '.$kode_sbl.' | '.$input['tahun_anggaran'],$input['tahun_anggaran'],'[input_rka_lokal id_skpd="'.$input['id_skpd'].'" kode_sbl="'.$kode_sbl.'" tahun_anggaran="'.$input['tahun_anggaran'].'"]');

                        $tombol_aksi = '<a href="'.$url_rka_lokal.'" target="_blank"><button class="btn-sm btn-info" style="margin: 1px;" title="Rincian Belanja"><i class="dashicons dashicons-search"></i></button></a>';
                        if(!empty($add_renja)){
                            if($sub_giat['data']['status_sub_keg'] != 1){
                                $tombol_aksi .= '<button class="btn-sm btn-warning" style="margin: 1px;" onclick="edit_renja_pemutakhiran(\''.$kode_sbl.'\');" title="Edit Renja"><i class="dashicons dashicons-edit"></i></button>';
                            }else{
                                $tombol_aksi .= '<button class="btn-sm btn-warning" style="margin: 1px;" onclick="edit_renja(\''.$kode_sbl.'\');" title="Edit Renja"><i class="dashicons dashicons-edit"></i></button>';
                            }
                            $tombol_aksi .= '<button class="btn-sm btn-danger" style="margin: 1px;" onclick="delete_renja(\''.$kode_sbl.'\');" title="Hapus Renja"><i class="dashicons dashicons-trash"></i></button>';
                        }
                        $tombol_aksi .= '<button class="btn-sm btn-primary" style="margin: 1px;" onclick="detail_renja(\''.$kode_sbl.'\');" title="Detail Sub Kegiatan"><i class="dashicons dashicons-ellipsis"></i></button>';
                        $pagu_sub_sipd = $sub_giat['pagu_sipd'];
                        $warning = '';
                        if($sub_giat['total'] != $pagu_sub_sipd){
                            $warning = 'background: #f9d9d9;';
                        }

                        $warning_pemutakhiran = '';
                        if($sub_giat['data']['status_sub_keg'] != 1){
                            $warning_pemutakhiran = 'mutakhirkan';
                            $total_sub_keg++;
                        }
                        $body .= '
                            <tr tipe="sub-kegiatan" kode="'.$kode_sbl.'">
                                <td class="kiri kanan bawah '.$warning_pemutakhiran.'">'.$kd_urusan.'</td>
                                <td class="kanan bawah '.$warning_pemutakhiran.'">'.$kd_bidang.'</td>
                                <td class="kanan bawah '.$warning_pemutakhiran.'">'.$kd_program.'</td>
                                <td class="kanan bawah '.$warning_pemutakhiran.'">'.$kd_giat.'</td>
                                <td class="kanan bawah '.$warning_pemutakhiran.'">'.$kd_sub_giat.'</td>
                                <td class="kanan bawah '.$warning_pemutakhiran.'">'.$sub_giat['nama'].'</td>
                                <td class="kanan bawah">'.$capaian_prog.'</td>
                                <td class="kanan bawah '.$warning_pemutakhiran.'">'.$output_sub_giat.'</td>
                                <td class="kanan bawah">'.$output_giat.'</td>
                                <td class="kanan bawah">'.$lokasi_sub_giat.'</td>
                                <td class="kanan bawah">'.$target_capaian_prog.'</td>
                                <td class="kanan bawah '.$warning_pemutakhiran.'">'.$target_output_sub_giat.'</td>
                                <td class="kanan bawah">'.$target_output_giat.'</td>
                                '.$tbody_pergeseran.'
                                <td class="kanan bawah text_kanan"><span class="nilai_penetapan">'.number_format($sub_giat['total'],0,",",".").'</span><span class="nilai_usulan">'.number_format($sub_giat['total_usulan'],0,",",".").'</span></td>
                                <td class="kanan bawah">'.$dana_sub_giat.'</td>
                                <td class="kanan bawah">'.$catatan.'</td>
                                <td class="kanan bawah">'.$ind_n_plus.'</td>
                                <td class="kanan bawah">'.$target_ind_n_plus.'</td>
                                <td class="kanan bawah text_kanan"><span class="nilai_penetapan">'.number_format($sub_giat['total_n_plus'],0,",",".").'</span><span class="nilai_usulan">'.number_format($sub_giat['total_n_plus_usulan'],0,",",".").'</span></td>
                                <td class="kanan bawah text_tengah hide-print">'.$tombol_aksi.'</td>
                                <td style="'.$warning.'" class="kanan bawah text_kanan hide-print">'.number_format($pagu_sub_sipd,0,",",".").'</td>
                                <td class="kanan bawah hide-print">'.$sub_giat['data']['label_kokab'].'</td>
                                <td class="kanan bawah hide-print">'.$sub_giat['data']['label_prov'].'</td>
                                <td class="kanan bawah hide-print">'.$sub_giat['data']['label_pusat'].'</td>
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

$nama_excel = 'INPUT RENJA '.strtoupper($nama_sub_skpd).'<br>TAHUN ANGGARAN '.$input['tahun_anggaran'].' '.strtoupper($nama_pemda);
$nama_laporan = 'INPUT RENJA '.strtoupper($nama_sub_skpd).'<br>TAHUN ANGGARAN '.$input['tahun_anggaran'].' '.strtoupper($nama_pemda);

$warning = '';
if($data_all['total'] != $data_all['pagu_sipd']){
    $warning = 'background: #f9d9d9;';
}
$warning_total_sub_keg = '';
if($total_sub_keg >= 1){
    $warning_total_sub_keg = 'bg-danger';
}
$warning_total_sumber_dana = '';
if($total_sumber_dana >= 1){
    $warning_total_sumber_dana = 'bg-danger';
}

$tfoot_pergeseran = $status_pergeseran_renja == 'tampil' ? '<td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($data_all['total_pergeseran'],0,",",".").'</span><span class="nilai_usulan">'.number_format($data_all['total_usulan_pergeseran'],0,",",".").'</span></td>' : '';
$data_per_sumber_dana = array();
$t_body_sumber_dana = '';
$total_sumber_dana_usulan = 0;
$total_sumber_dana_penetapan = 0;
if(!empty($data_rekap_sumber_dana)){
    foreach ($data_rekap_sumber_dana as $v_sumber_dana) {
        foreach ($v_sumber_dana as $v_dana) {
            foreach ($v_dana as $key => $v_sumber) {
                $total_sumber_dana_usulan += $v_sumber['total_usulan'];
                $total_sumber_dana_penetapan += $v_sumber['total'];
                $title_batasan_pagu = 'title="REKAP INPUT SUMBER DANA"';
                $warning_rekap_sumber_dana = '';

                $batasan_pagu = $wpdb->get_row($wpdb->prepare("
                                        SELECT 
                                            nilai_batasan 
                                        FROM `data_batasan_pagu_sd` 
                                        WHERE kode_dana=%s
                                            AND tahun_anggaran=%d
                                            AND active=1", $v_sumber['kode_dana'], $tahun_anggaran), ARRAY_A);
                
                $get_total_sumber_dana_all = $wpdb->get_row($wpdb->prepare("
                                                        SELECT 
                                                            SUM(pagudana) as total_pagu_all 
                                                        FROM `data_dana_sub_keg_lokal` 
                                                        WHERE tahun_anggaran=%d 
                                                        AND active=1 
                                                        AND iddana=%d;",$tahun_anggaran, $v_sumber['iddana']), ARRAY_A);
                
                if(!empty($batasan_pagu)){
                    if($get_total_sumber_dana_all['total_pagu_all'] > $batasan_pagu['nilai_batasan']){
                        $title_batasan_pagu = 'title="INPUT SUMBER DANA MELEBIHI BATASAN PAGU YANG SUDAH DITETAPKAN!"';
                        $warning_rekap_sumber_dana = 'background-color: #f9d9d9;';
                    }
                }else{
                    $title_batasan_pagu = 'title="BATASAN PAGU SUMBER DANA BELUM DITETAPKAN!"';
                    $warning_rekap_sumber_dana = 'background-color: #f9d9d9;';
                }

                $t_body_sumber_dana .= '
                        <tr '.$title_batasan_pagu.'>
                            <td class="text-kiri kanan bawah kiri" style="'.$warning_rekap_sumber_dana.'">'.$v_sumber['kode_dana'].' '.$v_sumber['nama'].'</td>
                            <td class="text_kanan kanan bawah kiri" style="'.$warning_rekap_sumber_dana.'" >'.number_format($v_sumber['total_usulan'],0,",",".").'</td>
                            <td class="text_kanan kanan bawah kiri" style="'.$warning_rekap_sumber_dana.'">'.number_format($v_sumber['total'],0,",",".").'</td>
                        </tr>';
            }
        }
    }
}
echo '
    <div id="cetak" title="'.$nama_excel.'" style="padding: 5px;">
        <input type="hidden" value="'. get_option( "_crb_api_key_extension" ) .'" id="api_key">
        <h4 class="text-center">Informasi Pemutakhiran Data</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Sub Kegiatan</th>
                    <th class="text-center">Sumber Dana</th>
                    <th class="text-center">Rekening Belanja</th>
                    <th class="text-center">Pendapatan</th>
                    <th class="text-center">Pembiayaan Penerimaan</th>
                    <th class="text-center">Pembiayaan Pengeluaran</th>
                    <th class="text-center">Komponen Belanja</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight:bold;" class="text-center '.$warning_total_sub_keg.'">'.$total_sub_keg.'</td>
                    <td style="font-weight:bold;" class="text-center '.$warning_total_sumber_dana.'">'.$total_sumber_dana.'</td>
                    <td style="font-weight:bold;" class="text-center '.$warning_total_sumber_dana.'">'.$total_sumber_dana.'</td>
                    <td style="font-weight:bold;" class="text-center '.$warning_total_sumber_dana.'">'.$total_sumber_dana.'</td>
                    <td style="font-weight:bold;" class="text-center '.$warning_total_sumber_dana.'">'.$total_sumber_dana.'</td>
                    <td style="font-weight:bold;" class="text-center '.$warning_total_sumber_dana.'">'.$total_sumber_dana.'</td>
                    <td style="font-weight:bold;" class="text-center '.$warning_total_sumber_dana.'">'.$total_sumber_dana.'</td>
                </tr>
            </tbody>
        </table>
        <h4 class="text-center">Informasi Rekap Sumber Dana</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center atas kanan bawah kiri">Sumber Dana</th>
                    <th class="text-center atas kanan bawah kiri">Total Usulan</th>
                    <th class="text-center atas kanan bawah kiri">Total Penetapan</th>
                </tr>
            </thead>
            <tbody>
                '.$t_body_sumber_dana.'
            </tbody>
            <tfoot>
                <tr>
                    <td class="text_blok text_kanan kanan bawah kiri">TOTAL PAGU SUMBER DANA</td>
                    <td class="text_blok text_kanan kanan bawah kiri">'.number_format($total_sumber_dana_usulan,0,",",".").'</td>
                    <td class="text_blok text_kanan kanan bawah kiri">'.number_format($total_sumber_dana_penetapan,0,",",".").'</td>
                </tr>
                <tr>
                    <td class="text_blok text_kanan kanan bawah kiri">TOTAL PAGU RENJA</td>
                    <td class="text_blok text_kanan kanan bawah kiri">'.number_format($data_all['total'],0,",",".").'</td>
                    <td class="text_blok text_kanan kanan bawah kiri">'.number_format($data_all['pagu_sipd'],0,",",".").'</td>
                </tr>
            </tfoot>
        </table>
        <h4 style="text-align: center; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">'.$nama_laporan.'</h4>
        <div id="wrap-table">
            <table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width: 2900px; table-layout: fixed; overflow-wrap: break-word; font-size: 60%; border: 0;">
                <thead>
                    <tr>
                        <th style="padding: 0; border: 0; width:25px"></th>
                        <th style="padding: 0; border: 0; width:30px"></th>
                        <th style="padding: 0; border: 0; width:30px"></th>
                        <th style="padding: 0; border: 0; width:40px"></th>
                        <th style="padding: 0; border: 0; width:40px"></th>
                        <th style="padding: 0; border: 0;"></th>
                        <th style="padding: 0; border: 0; width:170px"></th>
                        <th style="padding: 0; border: 0; width:170px"></th>
                        <th style="padding: 0; border: 0; width:170px"></th>
                        <th style="padding: 0; border: 0; width:100px"></th>
                        <th style="padding: 0; border: 0; width:95px"></th>
                        <th style="padding: 0; border: 0; width:95px"></th>
                        <th style="padding: 0; border: 0; width:95px"></th>
                        '.$thead_pergeseran2.'
                        <th style="padding: 0; border: 0; width:130px"></th>
                        <th style="padding: 0; border: 0; width:150px"></th>
                        <th style="padding: 0; border: 0; width:95px"></th>
                        <th style="padding: 0; border: 0; width:95px"></th>
                        <th style="padding: 0; border: 0; width:95px"></th>
                        <th style="padding: 0; border: 0; width:130px"></th>
                        <th style="padding: 0; border: 0; width:60px" class="hide-print"></th>
                        <th style="padding: 0; border: 0; width:140px" class="hide-print"></th>
                        <th style="padding: 0; border: 0; width:170px" class="hide-print"></th>
                        <th style="padding: 0; border: 0; width:170px" class="hide-print"></th>
                        <th style="padding: 0; border: 0; width:170px" class="hide-print"></th>
                    </tr>
                    <tr>
                        <td class="atas kanan bawah kiri text_tengah text_blok" colspan="5" rowspan="3">Kode</td>
                        <td class="atas kanan bawah text_tengah text_blok" rowspan="3">Urusan/ Bidang Urusan Pemerintahan Daerah Dan Program/ Kegiatan</td>
                        <td class="atas kanan bawah text_tengah text_blok" colspan="3">Indikator Kinerja</td>
                        <td class="atas kanan bawah text_tengah text_blok" colspan="'.$thead_pergeseran1.'">Rencana Tahun '.$input['tahun_anggaran'].'</td>
                        <td class="atas kanan bawah text_tengah text_blok" rowspan="3">Catatan Penting</td>
                        <td class="atas kanan bawah text_tengah text_blok" colspan="3">Prakiraan Maju Rencana Tahun '.($input['tahun_anggaran']+1).'</td>
                        <td class="atas kanan bawah text_tengah text_blok hide-print" rowspan="3">Aksi</td>
                        <td class="atas kanan bawah kiri text_tengah text_blok hide-print" rowspan="3">Pagu SIPD</td>
                        <td class="atas kanan bawah kiri text_tengah text_blok hide-print" rowspan="3">Prioritas Daerah</td>
                        <td class="atas kanan bawah kiri text_tengah text_blok hide-print" rowspan="3">Prioritas Provinsi</td>
                        <td class="atas kanan bawah kiri text_tengah text_blok hide-print" rowspan="3">Prioritas Pusat</td>
                    </tr>
                    <tr>
                        <td class="kanan bawah text_tengah text_blok" rowspan="2">Capaian Program</td>
                        <td class="kanan bawah text_tengah text_blok" rowspan="2">Keluaran Sub Kegiatan</td>
                        <td class="kanan bawah text_tengah text_blok" rowspan="2">Hasil Kegiatan</td>
                        <td class="kanan bawah text_tengah text_blok" rowspan="2">Lokasi Output Kegiatan</td>
                        <td class="kanan bawah text_tengah text_blok" colspan="3">Target Capaian Kinerja</td>
                        '.$thead_pergeseran.'
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
                        '.$tfoot_pergeseran.'
                        <td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($data_all['total'],0,",",".").'</span><span class="nilai_usulan">'.number_format($data_all['total_usulan'],0,",",".").'</span></td>
                        <td class="kanan bawah" colspan="4"></td>
                        <td class="kanan bawah text_kanan text_blok"><span class="nilai_penetapan">'.number_format($data_all['total_n_plus'],0,",",".").'</span><span class="nilai_usulan">'.number_format($data_all['total_n_plus_usulan'],0,",",".").'</span></td>
                        <td class="kanan bawah hide-print"></td>
                        <td style="'.$warning.'" class="kanan bawah text_kanan hide-print">'.number_format($data_all['pagu_sipd'],0,",",".").'</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
';
?>
<style type="text/css">
    .mutakhirkan {
        background: #f9d9d9;
    }
    .nilai_usulan:before {
        content: "( ";
    }
    .nilai_usulan:after {
        content: " )";
    }
    .nilai_usulan {
        display: block;
    }
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
<div class="modal fade mt-4" id="modalTambahRenja" role="dialog" aria-labelledby="modalTambahRenjaLabel" aria-hidden="true">
	<div class="modal-dialog" style="min-width: 90vw;" role="document">
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
                                        <select class="form-control input_select_2 label_tag_usulan" name="input_label_sub_keg_usulan[]" id="label_tag_usulan" multiple="multiple" onchange="set_penetapan_multiple(this);"></select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sumber_dana_usulan">Sumber Dana</label>
                                        <table id="sumber_dana_usulan" class="input_sumber_dana_usulan" style="margin: 0;">
                                            <tr data-id="1">
                                                <td style="width: 60%; max-width:100px;">
                                                    <select class="form-control input_select_2 sumber_dana_usulan" id="sumber_dana_usulan_1" name="input_sumber_dana_usulan[1]" onchange="set_penetapan(this);">
                                                        <option value="">Pilih Sumber Dana</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control input_number" id="pagu_sumber_dana_usulan_1" type="number" name="input_pagu_sumber_dana_usulan[1]" onkeyup="set_anggaran(this);"/>
                                                </td>
                                                <td style="width: 70px" class="text-center detail_tambah">
                                                    <button class="btn btn-warning btn-sm" onclick="tambahSumberDana(); return false;"><i class="dashicons dashicons-plus"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                    				<div class="form-group">
                    					<label for="kabupaten_kota_usulan">Lokasi Pelaksanaan</label>
                                        <table class="input_lokasi_usulan" style="margin: 0;">
                                            <tr data-id="1">
                                                <td>
                                                    <select class="form-control kabupaten_kota_usulan" id="kabupaten_kota_usulan_1" name="input_kabupaten_kota_usulan[1]" onchange="get_data_lokasi(this.value,'kabkot','usulan', true, 1); set_penetapan(this);">
                                                        <option value="">Pilih Kabupaten / Kota</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control input_select_2 kecamatan_usulan" name="input_kecamatan_usulan[1]" id="kecamatan_usulan_1" onchange="get_data_lokasi(this.value,'kec','usulan', true, 1); set_penetapan(this);">
                                                        <option value="">Pilih Kecamatan</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control input_select_2 desa_usulan" name="input_desa_usulan[1]" id="desa_usulan_1" onchange="set_penetapan(this);">
                                                        <option value="">Pilih Desa</option>
                                                    </select>
                                                </td>
                                                <td style="width: 70px" class="text-center detail_tambah">
                                                    <button class="btn btn-warning btn-sm" onclick="tambahLokasi(this); return false;"><i class="dashicons dashicons-plus"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                    				</div>
                                    <div class="form-group">
                                        <label for="bulan_awal_usulan">Waktu Pelaksanaan</label>
                                        <table style="margin: 0;">
                                            <tr>
                                                <td style="width: 50%;">
                                                    <select class="form-control bulan_awal" name="input_bulan_awal_usulan" id="bulan_awal_usulan" onchange="set_penetapan(this);">
                                                        <option value="">Pilih Bulan Awal</option>
                                                        <?php echo $bulan_option; ?>
                                                    </select>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control bulan_akhir" name="input_bulan_akhir_usulan" id="bulan_akhir_usulan" onchange="set_penetapan(this);">
                                                        <option value="">Pilih Bulan Akhir</option>
                                                        <?php echo $bulan_option; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan_usulan">Anggaran Sub Kegiatan</label>
                                        <input class="form-control input_number" type="number" name="input_pagu_sub_keg_usulan" id="pagu_sub_kegiatan_usulan" disabled />
                                        <small class="form-text text-muted">Anggaran Sub Kegiatan diambil dari akumulasi sumber dana.</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan_1_usulan">Anggaran Sub Kegiatan Tahun Berikutnya</label>
                                        <input class="form-control input_number" type="number" name="input_pagu_sub_keg_1_usulan" id="pagu_sub_kegiatan_1_usulan"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_ind_sub_keg_usulan">Indikator Sub Kegiatan</label>
                                        <table id="pagu_ind_sub_keg_usulan" class="indi_sub_keg_table_usulan" style="margin: 0;">
                                            <tr data-id="1" header="1">
                                                <td colspan="2" style="max-width: 100px;">
                                                    <select class="form-control pagu_indi_sub_keg input_select_2" id="pagu_ind_sub_keg_usulan_1" name="input_indikator_sub_keg_usulan[1]" onchange="setSatuan(this);">
                                                        <option value="">Pilih Nama Indikator</option>
                                                    </select>
                                                    <input type="hidden" id="ind_sub_keg_id_1" name="ind_sub_keg_id[1]" value="0">
                                                </td>
                                                <td rowspan="2" style="width: 70px; vertical-align: middle;" class="text-center detail_tambah">
                                                    <button class="btn btn-warning btn-sm" onclick="tambahIndikator(); return false;"><i class="dashicons dashicons-plus"></i></button>
                                                </td>
                                            </tr>
                                            <tr data-id="1">
                                                <td style="width: 50%;">
                                                    <input class="form-control input_number" type="number" id="indikator_pagu_indi_sub_keg_usulan_1" name="input_target_usulan[1]" placeholder="Target Indikator"/>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control satuan_pagu_indi_sub_keg input_select_2" id="satuan_pagu_indi_sub_keg_usulan_1" name="input_satuan_usulan[1]" disabled>
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
                                        <select class="form-control input_select_2 label_tag" name="input_label_sub_keg[]" id="label_tag" multiple="multiple"></select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sumber_dana">Sumber Dana</label>
                                        <table class="input_sumber_dana" style="margin: 0;">
                                            <tr data-id="1">
                                                <td style="width: 60%; max-width:100px;">
                                                    <select class="form-control input_select_2 sumber_dana" id="sumber_dana_1" name="input_sumber_dana[1]" disabled>
                                                        <option value="">Pilih Sumber Dana</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control input_number" id="pagu_sumber_dana_1" type="number" name="input_pagu_sumber_dana[1]"  onkeyup="set_anggaran(this);"<?php echo $disabled; ?>/>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="kabupaten_kota">Lokasi Pelaksanaan</label>
                                        <table class="input_lokasi" style="margin: 0;">
                                            <tr data-id="1">
                                                <td style="height: 52px;">
                                                    <select class="form-control kabupaten_kota" id="kabupaten_kota_1" name="input_kabupaten_kota[1]" onchange_lama="get_data_lokasi(this.value,'kabkot','penetapan', true, 1)" disabled>
                                                        <option value="">Pilih Kabupaten / Kota</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control input_select kecamatan" id="kecamatan_1" name="input_kecamatan[1]" onchange_lama="get_data_lokasi(this.value,'kec','penetapan', true, 1)" disabled>
                                                        <option value="">Pilih Kecamatan</option>
                                                    </select>
                                                </td>
                                                <td style="width: 30%">
                                                    <select class="form-control input_select desa" id="desa_1" name="input_desa[1]" disabled>
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
                                                    <select class="form-control bulan_awal" name="input_bulan_awal" id="bulan_awal" disabled>
                                                        <option value="">Pilih Bulan Awal</option>
                                                        <?php echo $bulan_option; ?>
                                                    </select>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control bulan_akhir" name="input_bulan_akhir" id="bulan_akhir" disabled>
                                                        <option value="">Pilih Bulan Akhir</option>
                                                        <?php echo $bulan_option; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan">Anggaran Sub Kegiatan</label>
                                        <input class="form-control input_number" type="number" name="input_pagu_sub_keg" id="pagu_sub_kegiatan" disabled/>
                                        <small class="form-text text-muted">Anggaran Sub Kegiatan diambil dari akumulasi sumber dana.</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_sub_kegiatan_1">Anggaran Sub Kegiatan Tahun Berikutnya</label>
                                        <input class="form-control input_number" type="number" name="input_pagu_sub_keg_1" id="pagu_sub_kegiatan_1" <?php echo $disabled; ?>/>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagu_indi_sub_keg_penetapan">Indikator Sub Kegiatan</label>
                                        <table class="indi_sub_keg_table" style="margin: 0;">
                                            <tr data-id="1" header="1">
                                                <td colspan="2" style="max-width: 100px;">
                                                    <select class="form-control pagu_indi_sub_keg input_select_2" id="pagu_indi_sub_keg_penetapan_1" name="input_indikator_sub_keg[1]" disabled>
                                                        <option value="">Pilih Nama Indikator</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr data-id="1">
                                                <td style="width: 50%;">
                                                    <input class="form-control input_number" type="number" name="input_target[1]" id="indikator_pagu_indi_sub_keg_1" placeholder="Target Indikator" <?php echo $disabled; ?>/>
                                                </td>
                                                <td style="width: 50%;">
                                                    <select class="form-control satuan_pagu_indi_sub_keg input_select_2" id="satuan_pagu_indi_sub_keg_penetapan_1" name="input_satuan[1]" disabled>
                                                        <option value="">Pilih Satuan</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label for="catatan">Catatan</label>
                                        <textarea class="form-control input_text" name="input_catatan" id="catatan" <?php echo $disabled; ?>></textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
					<?php if($is_admin): ?>
						<div class="row">
							<div class="col-md-12 text-center">
								<button id="button_copy_renja" onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">
									<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan
								</button>
							</div>
                        </div>
					<?php endif; ?>
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
            <div class="modal-footer">
                <button class="btn btn-primary submitBtn" onclick="submitIndikatorProgram()">Simpan</button>
                <button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Copy data renja -->
<div class="modal fade" id="modal-copy-renja-sipd" data-backdrop="static" role="dialog" aria-labelledby="modal-copy-renja-sipd-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tipe Copy Data RENJA ke Lokal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="checkbox" id="copyDataRka" name="copyDataSipd" value="rincian_rka">
                <label for="copyDataRka">Copy Data Rincian RKA</label><br>
                <input type="checkbox" id="copySumberDana" name="copyDataSipd" value="sumber_dana">
                <label for="copySumberDana">Copy Sumber Dana</label><br>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary submitBtn" onclick="copy_renja_sipd_to_lokal()">Simpan</button>
                <button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let id_skpd = <?php echo $input['id_skpd']; ?>;
    let tahun_anggaran = <?php echo $input['tahun_anggaran']; ?>;
    let id_unit = <?php echo $id_unit; ?>;
    let id_sub_unit = <?php echo $id_sub_skpd; ?>;
    let is_skpd = <?php echo $data_skpd['is_skpd']; ?>;
    let hide_data_usulan = <?php echo $hide_usulan;?>;
    let hide_data_penetapan = <?php echo $hide_penetapan;?>;

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
        
        let ceklist_sub_unit = '';
        if(id_sub_unit > 0){
            ceklist_sub_unit = 'checked';
        }
        let ceklist_usulan = '';
        if(hide_data_usulan > 0){
            jQuery('.nilai_usulan').remove();
            ceklist_usulan = 'checked';
        }
        let ceklist_penetapan = '';
        if(hide_data_penetapan > 0){
            jQuery('.nilai_penetapan').remove();
            ceklist_penetapan = 'checked';
        }
        var setting = ''
		    +'<h3 style="margin-top: 20px;">SETTING</h3>'
		    +'<label style="margin-left: 20px;"><input type="checkbox" onclick="hide_usulan(this);" '+ceklist_usulan+'> Sembunyikan Data Usulan</label>'
		    +'<label style="margin-left: 20px;"><input type="checkbox" onclick="hide_penetapan(this);" '+ceklist_penetapan+'> Sembunyikan Data Penetapan</label>';
            if(is_skpd == 1){
                setting +='<label style="margin-left: 20px;"><input type="checkbox" id="show_sub_unit" onclick="show_sub_unit(this);" '+ceklist_sub_unit+'> Tampilkan Data Sub Unit</input></label>';
            }

    	var aksi = '<?php echo $add_renja; ?>';
    	jQuery('#action-sipd').append(aksi);
        jQuery('#action-sipd').append(setting);

    <?php if(!empty($add_renja)): ?>
        jQuery('#tambah-data').on('click', function(){
            get_data_sub_unit(id_skpd)
            .then(function(){
                get_data_prioritas_prov()
                .then(function(){
                    get_data_prioritas_kabkot()
                    .then(function(){
                        get_data_label_tag()
                        .then(function(){
                            get_data_sumber_dana()
                            .then(function(){
                                get_data_lokasi(false, 'prov', false, false, 1)
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
                                    var html = '<button class="btn btn-warning btn-sm" onclick="tambahIndikator(); return false;"><i class="dashicons dashicons-plus"></i></button>';
                                    jQuery('.indi_sub_keg_table_usulan tr:first-child').find('>td').last().html(html);
                                    jQuery('#kecamatan_usulan_1').val('').trigger('change');
                                    jQuery('#kecamatan_1').val('').trigger('change');

                                });
                            })
                        });
                    });
                });
            });
        });
        /** Copy data renstra */
        jQuery('#copy-data-renstra-skpd').on('click', function(){
            if(confirm('Apakah anda yakin untuk melakukan ini? data RENJA akan diisi sesuai data RENSTRA per SKPD tahun berjalan!.')){
                let id_skpd = jQuery('#copy-data-renstra-skpd').data("skpd");
                let id_jadwal = jQuery('#copy-data-renstra-skpd').data("jadwal");
                if(id_skpd){
                    jQuery('#wrap-loading').show();
                    jQuery.ajax({
                        url: ajax.url,
                        type: "post",
                        data: {
                            "action": "copy_data_renstra_ke_renja",
                            "api_key": jQuery("#api_key").val(),
                            'id_jadwal': id_jadwal,
                            'id_skpd': id_skpd
                        },
                        dataType: "json",
                        success: function(res){
                            alert(res.message);
                            jQuery('#wrap-loading').hide();
                        }
                    });
                }else{
                    alert('Id SKPD Tidak Ditemukan.!')
                }
            }
        });
        /** End copy data renstra */
    <?php endif; ?>
    });

    //untuk membuka input yang defaultnya tidak "disabled"
    jQuery('#modalTambahRenja').on('hidden.bs.modal', function (e) {
        jQuery("#button_copy_renja").show();
        jQuery("#modalTambahRenja .submitBtn").show();
        jQuery(".detail_tambah").show();

        // kosongkan label provinsi dan kabupaten
        jQuery("#input_prioritas_provinsi").val('');
        jQuery("#input_prioritas_kab_kota").val('');

        // kosongkan label tag
        jQuery("#label_tag_usulan").val('').trigger('change').prop('disabled', false);
        jQuery("#label_tag").val('').trigger('change').prop('disabled', false);

        // kosongkan sumber dana
        jQuery("#sumber_dana_usulan_1").val('').trigger('change').prop('disabled', false);
        jQuery("#pagu_sumber_dana_usulan_1").val('').prop('disabled', false);
        jQuery("#sumber_dana_1").val('').trigger('change');
        jQuery("#pagu_sumber_dana_1").val('').prop('disabled', false);

        // kosongkan alamat
        jQuery("#kabupaten_kota_usulan_1").val('').prop('disabled', false);
        jQuery("#kecamatan_usulan_1").val('').prop('disabled', false);
        jQuery("#desa_usulan_1").val('').prop('disabled', false);
        
        // kosongkan pagu
        jQuery('#pagu_sub_kegiatan_1_usulan').prop('disabled', false);
        jQuery('#pagu_sub_kegiatan_1').prop('disabled', false);

        // hapus html pemutakhiran
        jQuery('#wrap-pemutakhiran').remove();

        jQuery('#catatan_usulan').prop('disabled', false);
        jQuery('#catatan').prop('disabled', false);
        jQuery('select[name="input_bulan_awal_usulan"]').prop('disabled', false);
        jQuery('select[name="input_bulan_akhir_usulan"]').prop('disabled', false);
        jQuery('#input_prioritas_provinsi').prop('disabled', false);
        jQuery('#input_prioritas_kab_kota').prop('disabled', false);
        jQuery('#pagu_ind_sub_keg_usulan_1').prop('disabled', false);
        jQuery('#indikator_pagu_indi_sub_keg_usulan_1').prop('disabled', false);
        jQuery('#indikator_pagu_indi_sub_keg_1').prop('disabled', false);
    })

    jQuery('#modal-indikator-renja').on('hidden.bs.modal', function (e) {
        jQuery('#modal-indikator-renja').find('.modal-footer .submitBtn').show();
    })

    jQuery('#modal-indikator-renja').on('hidden.bs.modal', function (e) {
        jQuery('#modal-indikator-renja').find('.modal-footer .submitBtn').show();
    })

    function tambahIndikator(){
        return new Promise(function(resolve, reject){
            var id = +jQuery('.indi_sub_keg_table_usulan > tbody tr').last().attr('data-id');
            var newId = id+1;
            /** tambah input usulan indi_sub_keg */
            var trNewUsulan = ''
                +'<tr data-id="'+newId+'" header="1">'
                    +'<td colspan="2" style="max-width: 100px;">'
                        +'<select class="form-control pagu_indi_sub_keg input_select_2" data-edit="0" id="pagu_ind_sub_keg_usulan_'+newId+'" name="input_indikator_sub_keg_usulan['+newId+']" onchange="setSatuan(this);">'
                            +'<option value="">Pilih Nama Indikator</option>'
                        +'</select>'
                        +'<input type="hidden" id="ind_sub_keg_id_'+newId+'" name="ind_sub_keg_id['+newId+']" value="0">'
                    +'</td>'
                    +'<td style="width: 70px; vertical-align: middle;" class="text-center aksi" rowspan="2">'
                        +'<button class="btn btn-warning btn-sm" onclick="tambahIndikator(); return false;"><i class="dashicons dashicons-plus"></i></button>'
                    +'</td>'
                +'</tr>'
                +'<tr data-id="'+newId+'">'
                    +'<td style="width: 50%;">'
                        +'<input class="form-control input_number" type="number" id="indikator_pagu_indi_sub_keg_usulan_'+newId+'" name="input_target_usulan['+newId+']" placeholder="Target Indikator"/>'
                    +'</td>'
                    +'<td style="width: 50%;">'
                        +'<select class="form-control satuan_pagu_indi_sub_keg input_select_2" id="satuan_pagu_indi_sub_keg_usulan_'+newId+'" name="input_satuan_usulan['+newId+']" disabled>'
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
                        var html = '<button class="btn btn-warning btn-sm" onclick="tambahIndikator(); return false;"><i class="dashicons dashicons-plus"></i></button>';
                    }else{
                        var html = '<button class="btn btn-danger btn-sm" onclick="hapusIndikator(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                    }
                    jQuery(b).find('>td').last().html(html);
                }
            });
            /** tambah input indi_sub_keg */
            var trNewUsulan = ''
                +'<tr data-id="'+newId+'" header="1">'
                    +'<td colspan="2" style="max-width: 100px;">'
                        +' <select class="form-control pagu_indi_sub_keg input_select_2" id="pagu_indi_sub_keg_penetapan_'+newId+'" name="input_indikator_sub_keg['+newId+']" disabled>'
                            +'<option value="">Pilih Nama Indikator</option>'
                        +'</select>'
                    +'</td>'
                +'</tr>'
                +'<tr data-id="'+newId+'">'
                    +'<td style="width: 50%;">'
                        +'<input class="form-control input_number" type="number" name="input_target['+newId+']" id="indikator_pagu_indi_sub_keg_'+newId+'" placeholder="Target Indikator" <?php echo $disabled; ?>/>'
                    +'</td>'
                    +'<td style="width: 50%;">'
                        +'<select class="form-control satuan_pagu_indi_sub_keg input_select_2" id="satuan_pagu_indi_sub_keg_penetapan_'+newId+'" name="input_satuan['+newId+']" disabled>'
                            +'<option value="">Pilih Satuan</option>'
                        +'</select>'
                    +'</td>'
                +'</tr>';

            var tbody = jQuery('.indi_sub_keg_table > tbody');
            tbody.append(trNewUsulan);
            let val_sub_keg = jQuery("#sub_kegiatan").val();
            if(val_sub_keg != undefined){
                get_indikator_sub_keg_by_id({
                    id: newId,
                    id_sub_keg: val_sub_keg
                })
                .then(function(){
                    resolve();
                });
            }else{
                resolve();
            }
        });
    }

    function tambahIndikatorProgram(){
        var tr = jQuery('#modal-indikator-renja #indikator_program tr');
        var tr_penetapan = tr.last();
        var id = +tr_penetapan.attr('data-id');
        var newId = id+1;
        var tr_usulan = tr.parent().find('tr[type="usulan"][data-id="'+id+'"]');

        var trNewUsulan = tr_usulan.html();
        trNewUsulan = ''
            +'<tr data-id="'+newId+'" type="usulan">'
                +trNewUsulan
            +'</tr>';
        trNewUsulan = trNewUsulan.replaceAll('_'+id+'"', '_'+newId+'"');
        trNewUsulan = trNewUsulan.replaceAll('['+id+']', '['+newId+']');
        trNewUsulan = trNewUsulan.replaceAll('>'+id+'<', '>'+newId+'<');

        var trNewPenetapan = tr_penetapan.html();
        trNewPenetapan = ''
            +'<tr data-id="'+newId+'" type="penetapan">'
                +trNewPenetapan
            +'</tr>';
        trNewPenetapan = trNewPenetapan.replaceAll('_'+id+'"', '_'+newId+'"');
        trNewPenetapan = trNewPenetapan.replaceAll('['+id+']', '['+newId+']');
        trNewPenetapan = trNewPenetapan.replaceAll('>'+id+'<', '>'+newId+'<');

        var tbody = jQuery('#modal-indikator-renja #indikator_program');
        tbody.append(trNewUsulan+trNewPenetapan);

        // kosongkan value
        tbody.find('>tr[data-id="'+newId+'"]').map(function(i, b){
            jQuery(b).find('input').val('');
            jQuery(b).find('textarea').val('');
        });

        var tr = tbody.find('>tr');
        tr.map(function(i, b){
            var tipe = jQuery(b).attr('type');
            if(tipe == 'usulan'){
                if(i == 0){
                    var html = '<button class="btn btn-warning btn-sm" onclick="tambahIndikatorProgram(); return false;"><i class="dashicons dashicons-plus"></i></button>';
                }else{
                    var html = '<button class="btn btn-danger btn-sm" onclick="hapusIndikatorProgram(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                }
                jQuery(b).find('>td').last().html(html);
            }
        });
    }

    function hapusIndikator(that){
        var id = jQuery(that).closest('tr').attr('data-id');
        jQuery('.indi_sub_keg_table_usulan > tbody').find('tr[data-id="'+id+'"]').remove();
        jQuery('.indi_sub_keg_table > tbody').find('tr[data-id="'+id+'"]').remove();
    }

    function hapusIndikatorProgram(that){
        var id = jQuery(that).closest('tr').attr('data-id');
        console.log('id', id);
        jQuery(that).closest('tbody').find('tr[data-id="'+id+'"]').map(function(i, b){
            console.log('b', b);
            jQuery(b).remove();
        });
    }

    function tambahIndikatorKegiatan(){
        var tr = jQuery('#modal-indikator-renja #indikator_kegiatan tr');
        var tr_penetapan = tr.last();
        var id = +tr_penetapan.attr('data-id');
        var newId = id+1;
        var tr_usulan = tr.parent().find('tr[type="usulan"][data-id="'+id+'"]');

        var trNewUsulan = tr_usulan.html();
        trNewUsulan = ''
            +'<tr data-id="'+newId+'" type="usulan">'
                +trNewUsulan
            +'</tr>';
        trNewUsulan = trNewUsulan.replaceAll('_'+id+'"', '_'+newId+'"');
        trNewUsulan = trNewUsulan.replaceAll('['+id+']', '['+newId+']');
        trNewUsulan = trNewUsulan.replaceAll('>'+id+'<', '>'+newId+'<');

        var trNewPenetapan = tr_penetapan.html();
        trNewPenetapan = ''
            +'<tr data-id="'+newId+'" type="penetapan">'
                +trNewPenetapan
            +'</tr>';
        trNewPenetapan = trNewPenetapan.replaceAll('_'+id+'"', '_'+newId+'"');
        trNewPenetapan = trNewPenetapan.replaceAll('['+id+']', '['+newId+']');
        trNewPenetapan = trNewPenetapan.replaceAll('>'+id+'<', '>'+newId+'<');

        var tbody = jQuery('#modal-indikator-renja #indikator_kegiatan');
        tbody.append(trNewUsulan+trNewPenetapan);
        var tr = tbody.find('>tr');
        tr.map(function(i, b){
            var tipe = jQuery(b).attr('type');
            if(tipe == 'usulan'){
                if(i == 0){
                    var html = '<button class="btn btn-warning btn-sm" onclick="tambahIndikatorKegiatan(); return false;"><i class="dashicons dashicons-plus"></i></button>';
                }else{
                    var html = '<button class="btn btn-danger btn-sm" onclick="hapusIndikatorKegiatan(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                }
                jQuery(b).find('>td').last().html(html);
            }
        });

        jQuery('#indikator_kegiatan_usulan_'+newId).val('');
        jQuery('#target_indikator_kegiatan_usulan_'+newId).val('');
        jQuery('#satuan_indikator_kegiatan_usulan_'+newId).val('');
        jQuery('#catatan_kegiatan_usulan_'+newId).val('');
        jQuery('#indikator_kegiatan_penetapan_'+newId).val('');
        jQuery('#target_indikator_kegiatan_penetapan_'+newId).val('');
        jQuery('#satuan_indikator_kegiatan_penetapan_'+newId).val('');
        jQuery('#catatan_kegiatan_penetapan_'+newId).val('');
    }

    function hapusIndikatorKegiatan(that){
        var id = jQuery(that).closest('tr').attr('data-id');
        console.log('id', id);
        jQuery(that).closest('tbody').find('tr[data-id="'+id+'"]').map(function(i, b){
            console.log('b', b);
            jQuery(b).remove();
        });
    }

    function tambahIndikatorHasilKegiatan(){
        var tr = jQuery('#modal-indikator-renja #indikator_hasil_kegiatan tr');
        var tr_penetapan = tr.last();
        var id = +tr_penetapan.attr('data-id');
        var newId = id+1;
        var tr_usulan = tr.parent().find('tr[type="usulan"][data-id="'+id+'"]');

        var trNewUsulan = tr_usulan.html();
        trNewUsulan = ''
            +'<tr data-id="'+newId+'" type="usulan">'
                +trNewUsulan
            +'</tr>';
        trNewUsulan = trNewUsulan.replaceAll('_'+id+'"', '_'+newId+'"');
        trNewUsulan = trNewUsulan.replaceAll('['+id+']', '['+newId+']');
        trNewUsulan = trNewUsulan.replaceAll('>'+id+'<', '>'+newId+'<');

        var trNewPenetapan = tr_penetapan.html();
        trNewPenetapan = ''
            +'<tr data-id="'+newId+'" type="penetapan">'
                +trNewPenetapan
            +'</tr>';
        trNewPenetapan = trNewPenetapan.replaceAll('_'+id+'"', '_'+newId+'"');
        trNewPenetapan = trNewPenetapan.replaceAll('['+id+']', '['+newId+']');
        trNewPenetapan = trNewPenetapan.replaceAll('>'+id+'<', '>'+newId+'<');

        var tbody = jQuery('#modal-indikator-renja #indikator_hasil_kegiatan');
        tbody.append(trNewUsulan+trNewPenetapan);
        var tr = tbody.find('>tr');
        tr.map(function(i, b){
            var tipe = jQuery(b).attr('type');
            if(tipe == 'usulan'){
                if(i == 0){
                    var html = '<button class="btn btn-warning btn-sm" onclick="tambahIndikatorHasilKegiatan(); return false;"><i class="dashicons dashicons-plus"></i></button>';
                }else{
                    var html = '<button class="btn btn-danger btn-sm" onclick="hapusIndikatorHasilKegiatan(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                }
                jQuery(b).find('>td').last().html(html);
            }
        });

        jQuery('#indikator_hasil_kegiatan_usulan_'+newId).val('');
        jQuery('#target_indikator_hasil_kegiatan_usulan_'+newId).val('');
        jQuery('#satuan_indikator_hasil_kegiatan_usulan_'+newId).val('');
        jQuery('#catatan_hasil_kegiatan_usulan_'+newId).val('');
        jQuery('#indikator_hasil_kegiatan_penetapan_'+newId).val('');
        jQuery('#target_indikator_hasil_kegiatan_penetapan_'+newId).val('');
        jQuery('#satuan_indikator_hasil_kegiatan_penetapan_'+newId).val('');
        jQuery('#catatan_hasil_kegiatan_penetapan_'+newId).val('');
    }

    function hapusIndikatorHasilKegiatan(that){
        var id = jQuery(that).closest('tr').attr('data-id');
        console.log('id', id);
        jQuery(that).closest('tbody').find('tr[data-id="'+id+'"]').map(function(i, b){
            console.log('b', b);
            jQuery(b).remove();
        });
    }

    function tambahLokasi(){
        return new Promise(function(resolve, reject){
            /** tambah input lokasi usulan */
            var id = +jQuery('.input_lokasi_usulan > tbody tr').last().attr('data-id');
            newId = id+1;
            var trNewUsulan = jQuery('.input_lokasi_usulan > tbody tr').last().html();
            trNewUsulan = ''
                +'<tr data-id="'+newId+'">'
                    +trNewUsulan
                +'</tr>';
            trNewUsulan = trNewUsulan.replaceAll('_'+id+'"', '_'+newId+'"');
            trNewUsulan = trNewUsulan.replaceAll('['+id+']', '['+newId+']');
            trNewUsulan = trNewUsulan.replaceAll(' '+id+')', ' '+newId+')');
            var tbody = jQuery('.input_lokasi_usulan > tbody');
            tbody.append(trNewUsulan);
            jQuery('.input_lokasi_usulan > tbody tr[data-id="'+newId+'"] .select2').remove();
            jQuery('.input_lokasi_usulan > tbody tr[data-id="'+newId+'"] select').select2({width: '100%'});
            <?php 
                if(!empty($id_lokasi_kokab)){
                    echo 'jQuery("#kabupaten_kota_usulan_"+newId).val('.$id_lokasi_kokab.').trigger("change");';
                }
            ?>

            var tr = tbody.find('>tr');
            var length = tr.length-1;
            tr.map(function(i, b){
                if(i == 0){
                    var html = '<button class="btn btn-warning btn-sm" onclick="tambahLokasi(this); return false;"><i class="dashicons dashicons-plus"></i></button>';
                }else{
                    var html = '<button class="btn btn-danger btn-sm" onclick="hapusLokasi(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                }
                jQuery(b).find('>td').last().html(html);
            });
            /** tambah input lokasi */
            var trNew = jQuery('.input_lokasi > tbody tr').last().html();
            trNew = ''
                +'<tr data-id="'+newId+'">'
                    +trNew
                +'</tr>';
            trNew = trNew.replaceAll('_'+id+'"', '_'+newId+'"');
            trNew = trNew.replaceAll('['+id+']', '['+newId+']');
            trNew = trNew.replaceAll(' '+id+')', ' '+newId+')');
            var tbody = jQuery('.input_lokasi > tbody');
            tbody.append(trNew);
            jQuery('.input_lokasi > tbody tr[data-id="'+newId+'"] .select2').remove();
            jQuery('.input_lokasi > tbody tr[data-id="'+newId+'"] select').select2({width: '100%'});
            <?php 
                if(!empty($id_lokasi_kokab)){
                    echo 'jQuery("#kabupaten_kota_"+newId).val('.$id_lokasi_kokab.').trigger("change");';
                }
            ?>
            resolve();
        });
    }

    function hapusLokasi(that){
        var id = jQuery(that).closest('tr').attr('data-id');
        jQuery('.input_lokasi_usulan > tbody').find('tr[data-id="'+id+'"]').remove();
        jQuery('.input_lokasi > tbody').find('tr[data-id="'+id+'"]').remove();
    }

    function tambahSumberDana(){
        return new Promise(function(resolve, reject){
            var id = +jQuery('.input_sumber_dana_usulan > tbody tr').last().attr('data-id');
            var newId = id+1;
            var trNewUsulan = jQuery('.input_sumber_dana_usulan > tbody tr').last().html();
            trNewUsulan = ''
                +'<tr data-id="'+newId+'">'
                    +trNewUsulan
                +'</tr>';
            trNewUsulan = trNewUsulan.replaceAll('_'+id+'"', '_'+newId+'"');
            trNewUsulan = trNewUsulan.replaceAll('['+id+']', '['+newId+']');
            var tbody = jQuery('.input_sumber_dana_usulan > tbody');
            tbody.append(trNewUsulan);
            jQuery('.input_sumber_dana_usulan > tbody tr[data-id="'+newId+'"] .select2').remove();
            jQuery('.input_sumber_dana_usulan > tbody tr[data-id="'+newId+'"] select').select2({width: '100%'});
            var tr = tbody.find('>tr');
            var length = tr.length-1;
            tr.map(function(i, b){
                if(i == 0){
                    var html = '<button class="btn btn-warning btn-sm" onclick="tambahSumberDana(); return false;"><i class="dashicons dashicons-plus"></i></button>';
                }else{
                    var html = '<button class="btn btn-danger btn-sm" onclick="hapusSumberDana(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                }
                jQuery(b).find('>td').last().html(html);
            });

            /** tambah input sumber dana */
            var id = +jQuery('.input_sumber_dana > tbody tr').last().attr('data-id');
            var newId = id+1;
            var trNew = jQuery('.input_sumber_dana > tbody tr').last().html();
            trNew = ''
                +'<tr data-id="'+newId+'">'
                    +trNew
                +'</tr>';
            trNew = trNew.replaceAll('_'+id+'"', '_'+newId+'"');
            trNew = trNew.replaceAll('['+id+']', '['+newId+']');
            var tbody = jQuery('.input_sumber_dana > tbody');
            tbody.append(trNew);
            jQuery('.input_sumber_dana > tbody tr[data-id="'+newId+'"] .select2').remove();
            jQuery('.input_sumber_dana > tbody tr[data-id="'+newId+'"] select').select2({width: '100%'});
            resolve();
        });
    }

    function tambahSubKegBaru(){
        return new Promise(function(resolve, reject){
            var id = +jQuery('#wrap-pemutakhiran > tbody tr').last().attr('data-id');
            var newId = id+1;
            var trNewUsulan = jQuery('#wrap-pemutakhiran > tbody tr').last().html();
            trNewUsulan = ''
                +'<tr data-id="'+newId+'">'
                    +'<td style="overflow: auto;">'
                        +'<div class="form-group input_sub_unit_baru">'
                            +'<label for="input_sub_unit_baru_'+newId+'">Sub Unit Baru</label>'
                            +'<select id="input_sub_unit_baru_'+newId+'" name="input_sub_unit_baru['+newId+']" onchange="get_sub_keg(this, \'sub_kegiatan_baru_'+newId+'\')">'
                                +dataSubUnit.table_content
                            +'</select>'
                        +'</div>'
                        +'<div class="form-group">'
                            +'<label for="sub_kegiatan_baru_'+newId+'">Sub Kegiatan Baru</label>'
                            +'<select id="sub_kegiatan_baru_'+newId+'" name="input_sub_kegiatan_baru['+newId+']" onchange="get_indikator_sub_keg(this, \''+newId+'\')">'
                            +'</select>'
                        +'</div>'
                        +'<div class="form-group">'
                            +'<label>Indikator Sub Kegiatan</label>'
                            +'<table class="table table-bordered">'
                                +'<tr>'
                                    +'<td>'
                                        +'<select class="form-control" name="input_indikator_sub_keg_baru['+newId+']">'
                                            +'<option value="">Nama Indikator</option>'
                                        +'</select>'
                                    +'</td>'
                                    +'<td style="width: 150px;">'
                                        +'<input class="form-control" type="number" name="input_target_baru['+newId+']" placeholder="Target Indikator"/>'
                                    +'</td>'
                                    +'<td style="width: 200px;">'
                                        +'<select class="form-control" name="input_satuan_baru['+newId+']">'
                                            +'<option value="">Satuan</option>'
                                        +'</select>'
                                    +'</td>'
                                +'</tr>'
                            +'</table>'
                        +'</div>'
                    +'</td>'
                    +'<td style="width: 65px;" class="text-center">'
                        +'<button class="btn btn-warning btn-sm" onclick="hapusSubKegBaru(this); return false;"><i class="dashicons dashicons-plus"></i></button>'
                    +'</td>'
                +'</tr>';
            var tbody = jQuery('#wrap-pemutakhiran > tbody');
            tbody.append(trNewUsulan);
            jQuery('#wrap-pemutakhiran > tbody tr[data-id="'+newId+'"] select').select2({width: '100%'});
            var tr = tbody.find('>tr');
            var length = tr.length-1;
            tr.map(function(i, b){
                if(i == 0){
                    var html = '<button class="btn btn-warning btn-sm" onclick="tambahSubKegBaru(); return false;"><i class="dashicons dashicons-plus"></i></button>';
                }else{
                    var html = '<button class="btn btn-danger btn-sm" onclick="hapusSubKegBaru(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                }
                jQuery(b).find('>td').last().html(html);
            });
            resolve();
        });
    }

    function hapusSubKegBaru(that){
        var id = jQuery(that).closest('tr').attr('data-id');
        jQuery('#wrap-pemutakhiran > tbody').find('tr[data-id="'+id+'"]').remove();
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
                jQuery("#input_sub_unit").html(dataSubUnit.table_content);
                jQuery('#input_sub_unit').select2({width: '100%'});
                resolve();
            }
        });
	}

    function get_data_prioritas_pusat(){
        return new Promise(function(resolve, reject){
            if(typeof dataPrioritasPusat == 'undefined'){
                jQuery('#wrap-loading').show();
        		jQuery.ajax({
        			url: "<?php echo admin_url('admin-ajax.php'); ?>",
        			type:"post",
        			data:{
        				'action'            : "get_prioritas_pusat",
        				'api_key'           : jQuery("#api_key").val(),
        				'tahun_anggaran'    : tahun_anggaran,
        			},
        			dataType: "json",
        			success:function(response){
                        jQuery('#wrap-loading').hide();
                        window.dataPrioritasPusat = response;
                        resolve();
        			}
        		});
            }else{
                resolve();
            }
        });
	}

    function get_data_prioritas_prov(){
        return new Promise(function(resolve, reject){
            if(typeof dataPrioritasProv == 'undefined'){
                jQuery('#wrap-loading').show();
                jQuery.ajax({
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    type:"post",
                    data:{
                        'action'            : "get_prioritas_prov",
                        'api_key'           : jQuery("#api_key").val(),
                        'tahun_anggaran'    : tahun_anggaran,
                    },
                    dataType: "json",
                    success:function(response){
                        jQuery('#wrap-loading').hide();
                        window.dataPrioritasProv = response;
                        jQuery("#input_prioritas_provinsi").html(dataPrioritasProv.table_content);
                        jQuery('#input_prioritas_provinsi').select2({width: '100%'});
                        // console.log(dataPrioritasProv.table_content);
                        // enable_button();
                        resolve();
                    }
                });
            }else{
                jQuery("#input_prioritas_provinsi").html(dataPrioritasProv.table_content);
                jQuery('#input_prioritas_provinsi').select2({width: '100%'});
                resolve();
            }
        });
    }

    function get_data_prioritas_kabkot(){
        return new Promise(function(resolve, reject){
            if(typeof dataPrioritasKabkot == 'undefined'){
                jQuery('#wrap-loading').show();
        		jQuery.ajax({
        			url: "<?php echo admin_url('admin-ajax.php'); ?>",
        			type:"post",
        			data:{
        				'action'            : "get_prioritas_kab_kot",
        				'api_key'           : jQuery("#api_key").val(),
        				'tahun_anggaran'    : tahun_anggaran,
        			},
        			dataType: "json",
        			success:function(response){
                        jQuery('#wrap-loading').hide();
                        window.dataPrioritasKabkot = response;
                        jQuery("#input_prioritas_kab_kota").html(dataPrioritasKabkot.table_content);
        			    jQuery('#input_prioritas_kab_kota').select2({width: '100%'});
                        // console.log(dataPrioritasKabkot.table_content);
        				// enable_button();
                        resolve();
        			}
        		});
            }else{
                jQuery("#input_prioritas_kab_kota").html(dataPrioritasKabkot.table_content);
                jQuery('#input_prioritas_kab_kota').select2({width: '100%'});
                resolve();
            }
        });
	}

    function get_sub_keg(that, target='sub_kegiatan'){
		let kode_sub_unit = jQuery(that).val();
        if(kode_sub_unit == ''){
            return;
        }
        if(typeof sub_keg_all == 'undefined'){
            window.sub_keg_all = {};
        }
        jQuery("#wrap-loading").show();
        new Promise(function(resolve, reject){
            if(!sub_keg_all[kode_sub_unit]){
        		jQuery.ajax({
        			method:'POST',
        			url:"<?php echo admin_url('admin-ajax.php'); ?>",
        			dataType:'json',
        			data:{
        				'action':'get_sub_keg_parent',
        				'api_key': jQuery("#api_key").val(),
                        'tahun_anggaran': tahun_anggaran,
        				'id_unit': kode_sub_unit
        			},
        			success:function(response){
                        sub_keg_all[kode_sub_unit] = response.data;
                        resolve(sub_keg_all[kode_sub_unit]);
        			}
        		});
            }else{
                resolve(sub_keg_all[kode_sub_unit]);
            }
        })
        .then(function(data){
            let option='<option value="">Pilih Sub Kegiatan</option>';
            data.map(function(value, index){
                value.map(function(value_sub, index_sub){
                    option+='<option value="'+value_sub.id_sub_giat+'">'+value_sub.nama_sub_giat+'</option>';
                });
            })

            jQuery("#"+target).html(option);
            jQuery("#"+target).select2({width: '100%'});
            jQuery("#wrap-loading").hide();
        })
    }

    function get_indikator_sub_keg(that, target=false){
		let id_sub_keg = jQuery(that).val();
        if(id_sub_keg == '' || typeof id_sub_keg == 'undefined'){
            return;
        }
        if(typeof indikator_sub_keg_all == 'undefined'){
            window.indikator_sub_keg_all = {};
        }
        jQuery("#wrap-loading").show();
        new Promise(function(resolve, reject){
            if(!indikator_sub_keg_all[id_sub_keg]){
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
                        indikator_sub_keg_all[id_sub_keg] = response.data;
                        resolve(indikator_sub_keg_all[id_sub_keg]);
        			}
        		});
            }else{
                resolve(indikator_sub_keg_all[id_sub_keg]);
            }
        })
        .then(function(data){
            let option='';
            data.map(function(value, index){
                option+='<option value="'+value.id_sub_keg+'">'+value.indikator+'</option>';
            })

            let optionSatuan='';
            data.map(function(value, index){
                optionSatuan+='<option value="'+value.id_sub_keg+'">'+value.satuan+'</option>';
            })

            if(target){
                jQuery('select[name="input_indikator_sub_keg_baru['+target+']"]').html(option);
                jQuery('select[name="input_satuan_baru['+target+']"]').html(optionSatuan);
            }else{
                jQuery(".pagu_indi_sub_keg").html(option);
                jQuery(".pagu_indi_sub_keg").select2({width: '100%'});
                jQuery(".satuan_pagu_indi_sub_keg").html(optionSatuan);
            }
            jQuery("#wrap-loading").hide();
        });
    }

    function get_indikator_sub_keg_by_id(data){
        return new Promise(function(resolve1, reject1){
    		let id_sub_keg = data.id_sub_keg;
            let id_input = data.id;
            if(id_sub_keg == '' || typeof id_sub_keg == 'undefined'){
                return;
            }
            if(typeof indikator_sub_keg_all == 'undefined'){
                window.indikator_sub_keg_all = {};
            }
            jQuery("#wrap-loading").show();
            new Promise(function(resolve, reject){
                if(!indikator_sub_keg_all[id_sub_keg]){
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
                            window.indikator_sub_keg_all[id_sub_keg] = response.data;
                            resolve(indikator_sub_keg_all[id_sub_keg]);
                        }
                    });
                }else{
                    resolve(indikator_sub_keg_all[id_sub_keg]);
                }
            })
            .then(function(data){
                let option='<option value="">Pilih Nama Indikator</option>';
                data.map(function(value, index){
                    option+='<option value="'+value.id_sub_keg+'">'+value.indikator+'</option>';
                })

                let optionSatuan='<option value="">Pilih Satuan</option>';
                data.map(function(value, index){
                    optionSatuan+='<option value="'+value.id_sub_keg+'">'+value.satuan+'</option>';
                })

                jQuery("#pagu_ind_sub_keg_usulan_"+id_input).html(option).select2({width: '100%'});
                jQuery("#pagu_indi_sub_keg_penetapan_"+id_input).html(option).select2({width: '100%'});
                jQuery("#satuan_pagu_indi_sub_keg_usulan_"+id_input).html(optionSatuan);
                jQuery("#satuan_pagu_indi_sub_keg_penetapan_"+id_input).html(optionSatuan);
                jQuery("#wrap-loading").hide();
                resolve1();
            });
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
                jQuery('.input_sumber_dana_usulan tbody tr').map(function(i, b){
                    if(i >= 1){
                        jQuery(b).remove();
                    }
                });
                jQuery('.input_sumber_dana tbody tr').map(function(i, b){
                    if(i >= 1){
                        jQuery(b).remove();
                    }
                });
                resolve();
            }
        });
    }

    function get_data_lokasi(id_alamat, jenis_lokasi, status, hide_loading=true, tr_id){
        console.log(id_alamat, jenis_lokasi, status, hide_loading, tr_id);
        return new Promise(function(resolve1, reject1){
            if(jenis_lokasi == ''){
                alert('Ada kesalahan,harap refresh halaman!')
                return resolve1(false);
            }

            if(id_alamat == false){
                if(jenis_lokasi == 'prov'){
                    id_alamat = <?php echo $id_lokasi_prov; ?>;
                    jQuery('.input_lokasi_usulan tbody tr').map(function(i, b){
                        if(i >= 1){
                            jQuery(b).remove();
                        }
                    });
                    jQuery('.input_lokasi tbody tr').map(function(i, b){
                        if(i >= 1){
                            jQuery(b).remove();
                        }
                    });
                }else{
                    console.log('id_alamat kosong!');
                    return resolve1(false);
                }
            }
            jQuery("#wrap-loading").show();

            if(
                status == undefined
                || status == false
            ){
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
                if(hide_loading){
                    jQuery("#wrap-loading").hide();
                }
                new Promise(function(resolve2, reject2){
                    let option='<option value="">Pilih '+response.jenis_lokasi+'</option>';
                    var cek_load_kec = false;
                    response.data.map(function(value, index){
                    <?php if(!empty($id_lokasi_kokab)): ?>
                        var selected = '';
                        if(
                            value.id_alamat == <?php echo $id_lokasi_kokab; ?>
                            && jenis_lokasi == 'prov'
                        ){
                            selected = 'selected';
                            option +='<option '+selected+' value="'+value.id_alamat+'">'+value.nama+'</option>';

                            cek_load_kec = true;
                            get_data_lokasi(value.id_alamat, 'kabkot', status, hide_loading, tr_id)
                            .then(function(){
                                resolve2(option);
                            });
                        }else{
                            option +='<option '+selected+' value="'+value.id_alamat+'">'+value.nama+'</option>';
                        }
                    <?php else: ?>
                        option +='<option value="'+value.id_alamat+'">'+value.nama+'</option>';
                    <?php endif; ?>
                    });
                    if(!cek_load_kec){
                        resolve2(option);
                    }
                })
                .then(function(option){
                    var tr_usulan = jQuery('.input_lokasi_usulan tbody tr[data-id="'+tr_id+'"]');
                    var tr = jQuery('.input_lokasi tbody tr[data-id="'+tr_id+'"]');
                    if(jenis_lokasi == 'kabkot'){
                        // if(status == 'usulan'){
                        //     tr_usulan.find(".kecamatan_usulan").html(option);
                        //     tr_usulan.find(".kecamatan_usulan").select2({width: '100%'});
                        // }else if(status == 'penetapan'){
                        //     tr.find(".kecamatan").html(option);
                        //     tr.find(".kecamatan").select2({width: '100%'});
                        // }else{
                            tr.find(".kecamatan").html(option);
                            tr.find(".kecamatan").select2({width: '100%'});
                            tr_usulan.find(".kecamatan_usulan").html(option);
                            tr_usulan.find(".kecamatan_usulan").select2({width: '100%'});
                        // }
                        tr.find(".desa").val('').trigger('change');
                        tr_usulan.find(".desa_usulan").val('').trigger('change');
                    }else if(jenis_lokasi == 'kec'){
                        // if(status == 'usulan'){
                        //     tr_usulan.find(".desa_usulan").html(option);
                        //     tr_usulan.find(".desa_usulan").select2({width: '100%'});
                        // }else if(status == 'penetapan'){
                        //     tr.find(".desa").html(option);
                        //     tr.find(".desa").select2({width: '100%'});
                        // }else{
                            tr.find(".desa").html(option);
                            tr.find(".desa").select2({width: '100%'});
                            tr_usulan.find(".desa_usulan").html(option);
                            tr_usulan.find(".desa_usulan").select2({width: '100%'});
                        // }
                    }else if(jenis_lokasi == 'prov'){
                        // if(status == 'usulan'){
                        //     tr_usulan.find(".kabupaten_kota_usulan").html(option);
                        //     tr_usulan.find(".kabupaten_kota_usulan").select2({width: '100%'});
                        // }else if(status == 'penetapan'){
                        //     tr.find(".kabupaten_kota").html(option);
                        //     tr.find(".kabupaten_kota").select2({width: '100%'});
                        // }else{
                            tr.find(".kabupaten_kota").html(option);
                            tr.find(".kabupaten_kota").select2({width: '100%'});
                            tr_usulan.find(".kabupaten_kota_usulan").html(option);
                            tr_usulan.find(".kabupaten_kota_usulan").select2({width: '100%'});
                        // }
                        tr.find(".desa").val('').trigger('change');
                        tr.find(".kecamatan").val('').trigger('change');
                        tr_usulan.find(".desa_usulan").val('').trigger('change');
                        tr_usulan.find(".kecamatan_usulan").val('').trigger('change');
                    }
                    return resolve1();
                });
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
                    alert(response.message);
                    if(response.status == 'success'){
                        jQuery('#modalTambahRenja').modal('hide');
                        refresh_page();
                    }
                }
            });
        }
    }

    function edit_renja(kode_sbl, pemutakhiran=0){
        get_data_sub_unit(id_skpd)
        .then(function(){
            get_data_prioritas_prov()
            .then(function(){
                get_data_prioritas_kabkot()
                .then(function(){
                    get_data_label_tag()
                    .then(function(){
                        get_data_sumber_dana()
                        .then(function(){
                            get_data_lokasi(false, 'prov', false, false, 1)
                            .then(function(){
                                jQuery('#wrap-loading').show();
                                jQuery.ajax({
                                    method: 'post',
                                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                    dataType: 'json',
                                    data: {
                                        'action': 'edit_renja',
                                        'api_key': jQuery('#api_key').val(),
                                        'kode_sbl': kode_sbl,
                                        'tahun_anggaran': tahun_anggaran
                                    },
                                    success: function(response){
                                        if(response.status == 'error'){
                                            jQuery('#wrap-loading').hide();
                                            return alert(response.message);
                                        }
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

                                        if(response.data.id_label_prov != 0){
                                            jQuery('select[name="input_prioritas_provinsi"]').val(response.data.id_label_prov).trigger('change');
                                        }
                                        if(response.data.id_label_kokab != 0){
                                            jQuery('select[name="input_prioritas_kab_kota"]').val(response.data.id_label_kokab).trigger('change');
                                        }

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
                                            let id = index+1;
                                            new Promise(function(resolve, reject){
                                                if(id > 1){
                                                    tambahIndikator()
                                                    .then(function(){
                                                        resolve(value);
                                                    })
                                                }else{
                                                    resolve(value);
                                                }
                                            })
                                            .then(function(value){
                                                jQuery("#ind_sub_keg_id_"+id).val(value.id);
                                                jQuery("#indikator_pagu_indi_sub_keg_usulan_"+id).val(value.targetoutput_usulan).prop('disabled', false);
                                                jQuery("#pagu_ind_sub_keg_usulan_"+id).val(value.id_indikator_sub_giat).trigger('change').prop('disabled', false);
                                                jQuery("#satuan_pagu_indi_sub_keg_usulan_"+id).val(value.id_indikator_sub_giat).trigger('change');
                                                jQuery("#indikator_pagu_indi_sub_keg_"+id).val(value.targetoutput).prop('disabled', false);
                                                jQuery("#pagu_indi_sub_keg_penetapan_"+id).val(value.id_indikator_sub_giat).trigger('change');
                                                jQuery("#satuan_pagu_indi_sub_keg_penetapan_"+id).val(value.id_indikator_sub_giat).trigger('change');
                                            });
                                        })
                                        /** -- end -- */

                                        /** Memunculkan data sumber dana */
                                        response.data.sumber_dana.map(function(value, index){
                                            let id = index+1;
                                            new Promise(function(resolve, reject){
                                                if(id > 1){
                                                    tambahSumberDana()
                                                    .then(function(){
                                                        resolve(value);
                                                    })
                                                }else{
                                                    resolve(value);
                                                }
                                            })
                                            .then(function(value){
                                                jQuery("#sumber_dana_usulan_"+id).val(value.id_dana_usulan).trigger('change').prop('disabled', false);
                                                jQuery("#pagu_sumber_dana_usulan_"+id).val(value.pagu_dana_usulan).prop('disabled', false);
                                                jQuery("#sumber_dana_"+id).val(value.iddana).trigger('change');
                                                jQuery("#pagu_sumber_dana_"+id).val(value.pagudana).prop('disabled', false);
                                            });
                                        })
                                        /** -- end -- */

                                        /** Memunculkan data lokasi */
                                        response.data.lokasi.map(function(value, index){
                                            let urutan = index+1;
                                            let id = value.id;
                                            new Promise(function(resolve, reject){
                                                if(urutan > 1){
                                                    tambahLokasi()
                                                    .then(function(){
                                                        resolve(value);
                                                    })
                                                }else{
                                                    resolve(value);
                                                }
                                            })
                                            .then(function(value){
                                                // setting opsi master kecamatan
                                                let option_kec='<option value="">Pilih Kecamatan</option>';
                                                response.data.data_master_kec[id].map(function(value, index){
                                                    option_kec+='<option value="'+value.id_alamat+'">'+value.nama+'</option>'
                                                });
                                                jQuery("#kecamatan_usulan_"+urutan).html(option_kec).prop('disabled', false);
                                                jQuery("#kecamatan_usulan_"+urutan).select2({width: '100%'});
                                                jQuery("#kecamatan_"+urutan).html(option_kec);
                                                jQuery("#kecamatan_"+urutan).select2({width: '100%'});

                                                // setting opsi master desa
                                                let option_desa='<option value="">Pilih Desa</option>';
                                                response.data.data_master_desa[id].map(function(value, index){
                                                    option_desa+='<option value="'+value.id_alamat+'">'+value.nama+'</option>'
                                                });
                                                jQuery("#desa_usulan_"+urutan).html(option_desa).prop('disabled', false);
                                                jQuery("#desa_usulan_"+urutan).select2({width: '100%'});
                                                jQuery("#desa_"+urutan).html(option_desa);
                                                jQuery("#desa_"+urutan).select2({width: '100%'});

                                                jQuery("#kabupaten_kota_usulan_"+urutan).prop('disabled', false);
                                                
                                                // set value edit lokasi renja
                                                if(value.idkabkota_usulan != null){
                                                    setOnchangeFalse("#kabupaten_kota_usulan_"+urutan, value.idkabkota_usulan);
                                                }
                                                if(value.idkabkota != null){
                                                    setOnchangeFalse("#kabupaten_kota_"+urutan, value.idkabkota);
                                                }
                                                if(value.idcamat_usulan != null){
                                                    setOnchangeFalse("#kecamatan_usulan_"+urutan, value.idcamat_usulan);
                                                }
                                                if(value.idcamat != null){
                                                    setOnchangeFalse("#kecamatan_"+urutan, value.idcamat);
                                                }
                                                if(value.idlurah_usulan != null){
                                                    setOnchangeFalse("#desa_usulan_"+urutan, value.idlurah_usulan);
                                                }
                                                if(value.idlurah != null){
                                                    setOnchangeFalse("#desa_"+urutan, value.idlurah);
                                                }
                                            })

                                        })
                                        /** -- end -- */

                                        /** Memunculkan data label tag */
                                        let id_label_tag_usulan = [];
                                        let id_label_tag = [];
                                        response.data.label_tag.map(function(value, index){  
                                            id_label_tag_usulan.push(value.id_label_giat_usulan);
                                            id_label_tag.push(value.id_label_giat);
                                        })
                                        jQuery('#label_tag_usulan').val(id_label_tag_usulan).trigger('change');
                                        jQuery('#label_tag').val(id_label_tag).trigger('change'); 
                                        /** -- end -- */

                                        if(pemutakhiran == 1){
                                            jQuery("#modalTambahRenja .modal-title").html("Edit Sub Kegiatan Pemutakhiran");
                                            jQuery("#modalTambahRenja .submitBtn")
                                                .attr("onclick", `submitEditRenjaPemutakhiranForm('${kode_sbl}')`)
                                                .attr("disabled", false)
                                                .text("Simpan");
                                        }else{
                                            jQuery("#modalTambahRenja .modal-title").html("Edit Sub Kegiatan");
                                            jQuery("#modalTambahRenja .submitBtn")
                                                .attr("onclick", `submitEditRenjaForm('${kode_sbl}')`)
                                                .attr("disabled", false)
                                                .text("Simpan");
                                        }
                                        jQuery('#modalTambahRenja').modal('show');
                                        jQuery('#wrap-loading').hide();
                                    }
                                });
                            });
                        });
                    });
                });
            });
        });
    }

    function edit_renja_pemutakhiran(kode_sbl){
        get_data_sub_unit(id_skpd)
        .then(function(){
            var html = ''
                +'<table id="wrap-pemutakhiran" class="table table-bordered">'
                    +'<tbody>'
                        +'<tr data-id="1">'
                            +'<td style="overflow: auto;">'
                                +'<div class="form-group input_sub_unit_baru">'
                                    +'<label for="input_sub_unit_baru_1">Sub Unit Baru</label>'
                                    +'<select id="input_sub_unit_baru_1" name="input_sub_unit_baru[1]" onchange="get_sub_keg(this, \'sub_kegiatan_baru_1\')"></select>'
                                +'</div>'
                                +'<div class="form-group">'
                                    +'<label for="sub_kegiatan_baru_1">Sub Kegiatan Baru</label>'
                                    +'<select id="sub_kegiatan_baru_1" name="input_sub_kegiatan_baru[1]" onchange="get_indikator_sub_keg(this, \'1\')">'
                                    +'</select>'
                                +'</div>'
                                +'<div class="form-group">'
                                    +'<label>Indikator Sub Kegiatan</label>'
                                    +'<table class="table table-bordered">'
                                        +'<tr>'
                                            +'<td>'
                                                +'<select class="form-control" name="input_indikator_sub_keg_baru[1]">'
                                                    +'<option value="">Indikator</option>'
                                                +'</select>'
                                            +'</td>'
                                            +'<td style="width: 150px;">'
                                                +'<input class="form-control" type="number" name="input_target_baru[1]" placeholder="Target Indikator"/>'
                                            +'</td>'
                                            +'<td style="width: 200px;">'
                                                +'<select class="form-control" name="input_satuan_baru[1]">'
                                                    +'<option value="">Satuan</option>'
                                                +'</select>'
                                            +'</td>'
                                        +'</tr>'
                                    +'</table>'
                                +'</div>'
                            +'</td>'
                            +'<td style="width: 65px;" class="text-center">'
                                +'<button class="btn btn-warning btn-sm" onclick="tambahSubKegBaru(); return false;"><i class="dashicons dashicons-plus"></i></button>'
                            +'</td>'
                        +'</tr>'
                    +'</tbody>'
                +'</table>';
            jQuery('#sub_kegiatan').closest('.form-group').after(html);
            jQuery("#input_sub_unit_baru_1").html(dataSubUnit.table_content);
            jQuery('#input_sub_unit_baru_1').select2({width: '100%'});
            jQuery('#sub_kegiatan_baru_1').select2({width: '100%'});
            edit_renja(kode_sbl, 1);
        });
    }

    function detail_renja(kode_sbl){
        get_data_sub_unit(id_skpd)
        .then(function(){
            get_data_prioritas_prov()
            .then(function(){
                get_data_prioritas_kabkot()
                .then(function(){
                    get_data_label_tag()
                    .then(function(){
                        get_data_sumber_dana()
                        .then(function(){
                            get_data_lokasi(false, 'prov', false, false, 1)
                            .then(function(){
                                jQuery('#wrap-loading').show();
                                jQuery.ajax({
                                    method: 'post',
                                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                    dataType: 'json',
                                    data: {
                                        'action': 'edit_renja',
                                        'api_key': jQuery('#api_key').val(),
                                        'kode_sbl': kode_sbl,
                                        'tahun_anggaran': tahun_anggaran
                                    },
                                    success: function(response){
                                        jQuery('#pagu_sub_kegiatan_usulan').val(response.data.pagu_usulan).prop('disabled', true);
                                        // jQuery('#pagu_sub_kegiatan_usulan').prop('disabled', true);
                                        jQuery('#pagu_sub_kegiatan_1_usulan').val(response.data.pagu_n_depan_usulan).prop('disabled', true);
                                        jQuery('#pagu_sub_kegiatan').val(response.data.pagu).prop('disabled', true);
                                        jQuery('#pagu_sub_kegiatan_1').val(response.data.pagu_n_depan).prop('disabled', true);
                                        jQuery('#catatan_usulan').val(response.data.catatan_usulan).prop('disabled', true);
                                        jQuery('#catatan').val(response.data.catatan).prop('disabled', true);
                                        let input_sub_unit = `<option selected value="${response.data.id_sub_skpd}">${response.data.nama_sub_skpd}</option>`;
                                        jQuery('#input_sub_unit').html(input_sub_unit);
                                        jQuery('#input_sub_unit').prop('disabled', true);
                                        let input_sub_kegiatan = `<option selected value="${response.data.id_sub_giat}">${response.data.nama_sub_giat}</option>`;
                                        jQuery('#sub_kegiatan').html(input_sub_kegiatan);
                                        jQuery('#sub_kegiatan').prop('disabled', true);
                                        jQuery('select[name="input_bulan_awal_usulan"] option[value="'+response.data.waktu_awal_usulan+'"]').attr("selected","selected");
                                        jQuery('select[name="input_bulan_awal_usulan"]').prop('disabled', true);
                                        jQuery('select[name="input_bulan_akhir_usulan"] option[value="'+response.data.waktu_akhir_usulan+'"]').attr("selected","selected");
                                        jQuery('select[name="input_bulan_akhir_usulan"]').prop('disabled', true);
                                        jQuery('select[name="input_bulan_awal"] option[value="'+response.data.waktu_awal+'"]').attr("selected","selected").prop('disabled', true);
                                        jQuery('select[name="input_bulan_akhir"] option[value="'+response.data.waktu_akhir+'"]').attr("selected","selected").prop('disabled', true);
                                        jQuery('#input_prioritas_provinsi').prop('disabled', true);
                                        jQuery('#input_prioritas_kab_kota').prop('disabled', true);
                                        jQuery('#label_tag_usulan').prop('disabled', true);
                                        jQuery('#label_tag').prop('disabled', true);

                                        if(response.data.id_label_prov != 0){
                                            jQuery('select[name="input_prioritas_provinsi"]').val(response.data.id_label_prov).trigger('change');
                                        }
                                        if(response.data.id_label_kokab != 0){
                                            jQuery('select[name="input_prioritas_kab_kota"]').val(response.data.id_label_kokab).trigger('change');
                                        }

                                        /** Memunculkan data indikator sub kegiatan */
                                        let optionIndikator='<option value="">Pilih Nama Indikator</option>';
                                        response.data.master_sub_keg_indikator.map(function(value, index){
                                            optionIndikator+='<option value="'+value.id_sub_keg+'">'+value.indikator+'</option>';
                                        });
                                        let optionSatuan='<option value="">Pilih Satuan</option>';
                                        response.data.master_sub_keg_indikator.map(function(value, index){
                                            optionSatuan+='<option value="'+value.id_sub_keg+'">'+value.satuan+'</option>';
                                        });
                                        jQuery("#pagu_ind_sub_keg_usulan_1").html(optionIndikator).select2({width: '100%'}).prop('disabled', true);
                                        jQuery("#pagu_indi_sub_keg_penetapan_1").html(optionIndikator).select2({width: '100%'}).prop('disabled', true);
                                        jQuery("#satuan_pagu_indi_sub_keg_usulan_1").html(optionSatuan).select2({width: '100%'}).prop('disabled', true);
                                        jQuery("#satuan_pagu_indi_sub_keg_penetapan_1").html(optionSatuan).select2({width: '100%'}).prop('disabled', true);

                                        response.data.indikator_sub_keg.map(function(value, index){
                                            let id = index+1;
                                            new Promise(function(resolve, reject){
                                                if(id > 1){
                                                    tambahIndikator()
                                                    .then(function(){
                                                        resolve(value);
                                                    })
                                                }else{
                                                    resolve(value);
                                                }
                                            })
                                            .then(function(value){
                                                jQuery("#ind_sub_keg_id_"+id).val(value.id).prop('disabled', true);
                                                jQuery("#indikator_pagu_indi_sub_keg_usulan_"+id).val(value.targetoutput_usulan).prop('disabled', true);
                                                jQuery("#pagu_ind_sub_keg_usulan_"+id).val(value.id_indikator_sub_giat).trigger('change').prop('disabled', true);
                                                jQuery("#satuan_pagu_indi_sub_keg_usulan_"+id).val(value.id_indikator_sub_giat).trigger('change').prop('disabled', true);
                                                jQuery("#indikator_pagu_indi_sub_keg_"+id).val(value.targetoutput).prop('disabled', true);
                                                jQuery("#pagu_indi_sub_keg_penetapan_"+id).val(value.id_indikator_sub_giat).trigger('change').prop('disabled', true);
                                                jQuery("#satuan_pagu_indi_sub_keg_penetapan_"+id).val(value.id_indikator_sub_giat).trigger('change').prop('disabled', true);
                                            });
                                        })
                                        /** -- end -- */

                                        /** Memunculkan data sumber dana */
                                        response.data.sumber_dana.map(function(value, index){
                                            let id = index+1;
                                            new Promise(function(resolve, reject){
                                                if(id > 1){
                                                    tambahSumberDana()
                                                    .then(function(){
                                                        resolve(value);
                                                    })
                                                }else{
                                                    resolve(value);
                                                }
                                            })
                                            .then(function(value){
                                                jQuery("#sumber_dana_usulan_"+id).val(value.id_dana_usulan).trigger('change').prop('disabled', true);
                                                jQuery("#pagu_sumber_dana_usulan_"+id).val(value.pagu_dana_usulan).prop('disabled', true);
                                                jQuery("#sumber_dana_"+id).val(value.iddana).trigger('change').prop('disabled', true);
                                                jQuery("#pagu_sumber_dana_"+id).val(value.pagudana).prop('disabled', true);
                                            });
                                        })
                                        /** -- end -- */

                                        /** Memunculkan data lokasi */
                                        response.data.lokasi.map(function(value, index){
                                            let urutan = index+1;
                                            let id = value.id;
                                            new Promise(function(resolve, reject){
                                                if(urutan > 1){
                                                    tambahLokasi()
                                                    .then(function(){
                                                        resolve(value);
                                                    })
                                                }else{
                                                    resolve(value);
                                                }
                                            })
                                            .then(function(value){
                                                // setting opsi master kecamatan
                                                let option_kec='<option value="">Pilih Kecamatan</option>';
                                                response.data.data_master_kec[id].map(function(value, index){
                                                    option_kec+='<option value="'+value.id_alamat+'">'+value.nama+'</option>'
                                                });
                                                jQuery("#kecamatan_usulan_"+urutan).html(option_kec).prop('disabled', true);
                                                jQuery("#kecamatan_usulan_"+urutan).select2({width: '100%'});
                                                jQuery("#kecamatan_"+urutan).html(option_kec).prop('disabled', true);
                                                jQuery("#kecamatan_"+urutan).select2({width: '100%'});

                                                // setting opsi master desa
                                                let option_desa='<option value="">Pilih Desa</option>';
                                                response.data.data_master_desa[id].map(function(value, index){
                                                    option_desa+='<option value="'+value.id_alamat+'">'+value.nama+'</option>'
                                                });
                                                jQuery("#desa_usulan_"+urutan).html(option_desa).prop('disabled', true);
                                                jQuery("#desa_usulan_"+urutan).select2({width: '100%'});
                                                jQuery("#desa_"+urutan).html(option_desa).prop('disabled', true);
                                                jQuery("#desa_"+urutan).select2({width: '100%'});

                                                jQuery("#kabupaten_kota_usulan_"+urutan).prop('disabled', true);
                                                
                                                // set value edit lokasi renja
                                                if(value.idkabkota_usulan != null){
                                                    setOnchangeFalse("#kabupaten_kota_usulan_"+urutan, value.idkabkota_usulan);
                                                }
                                                if(value.idkabkota != null){
                                                    setOnchangeFalse("#kabupaten_kota_"+urutan, value.idkabkota);
                                                }
                                                if(value.idcamat_usulan != null){
                                                    setOnchangeFalse("#kecamatan_usulan_"+urutan, value.idcamat_usulan);
                                                }
                                                if(value.idcamat != null){
                                                    setOnchangeFalse("#kecamatan_"+urutan, value.idcamat);
                                                }
                                                if(value.idlurah_usulan != null){
                                                    setOnchangeFalse("#desa_usulan_"+urutan, value.idlurah_usulan);
                                                }
                                                if(value.idlurah != null){
                                                    setOnchangeFalse("#desa_"+urutan, value.idlurah);
                                                }
                                            })

                                        })
                                        /** -- end -- */

                                        /** Memunculkan data label tag */
                                        let id_label_tag = [];
                                        response.data.label_tag.map(function(value, index){  
                                            id_label_tag.push(value.id_label_giat);
                                        })
                                        jQuery('#label_tag_usulan').val(id_label_tag).trigger('change');
                                        jQuery('#label_tag').val(id_label_tag).trigger('change'); 
                                        /** -- end -- */

                                        jQuery(".detail_tambah").hide();
                                        jQuery("#button_copy_renja").hide();
                                        jQuery("#modalTambahRenja .modal-title").html("Detail Sub Kegiatan");
                                        jQuery("#modalTambahRenja .submitBtn")
                                            .attr("onclick", `showEditRenjaForm('${kode_sbl}')`)
                                            .attr("disabled", false)
                                            .text("Simpan")
                                            .hide();
                                        jQuery('#modalTambahRenja').modal('show');
                                        jQuery('#wrap-loading').hide();
                                    }
                                })
                            });
                        });
                    });
                });
            });
        });
    }

    function setOnchangeFalse(id, val){
        var select = jQuery(id);
        var change = select.attr('onchange');
        select.attr('onchange', '');
        select.val(val).trigger('change');
        select.attr('onchange', change);
    }

    function submitEditRenjaForm(kode_sbl){
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
                    'kode_sbl': kode_sbl,
                    'tahun_anggaran': tahun_anggaran
                },
                success: function(response){
                    jQuery('#wrap-loading').hide();
                    alert(response.message.replace(/\\n/g,"\n"));
                    if(response.status == 'success'){
                        jQuery('#modalTambahRenja').modal('hide');
                        refresh_page();
                    }
                }
            })
        }
    }

    function submitEditRenjaPemutakhiranForm(kode_sbl){
        if(confirm('Apakah anda yakin untuk mengubah data ini?')){
            jQuery('#wrap-loading').show();
            let form = getFormData(jQuery("#form-renja"));
            jQuery.ajax({
                method: 'post',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: {
                    'action': 'submit_edit_renja_pemutakhiran',
                    'api_key': jQuery('#api_key').val(),
                    'data': JSON.stringify(form),
                    'kode_sbl': kode_sbl,
                    'pemutakhiran': 1,
                    'tahun_anggaran': tahun_anggaran
                },
                success: function(response){
                    jQuery('#wrap-loading').hide();
                    alert(response.message);
                    if(response.status == 'success'){
                        jQuery('#modalTambahRenja').modal('hide');
                        refresh_page();
                    }
                }
            })
        }
    }

    function submitIndikatorProgram(){
        if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
            jQuery('#wrap-loading').show();
            let form = getFormData(jQuery("#modal-indikator-renja form"));
            jQuery.ajax({
                method: 'post',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: {
                    'action': 'submit_indikator_program_renja',
                    'api_key': jQuery('#api_key').val(),
                    'data': JSON.stringify(form),
                    'tahun_anggaran': tahun_anggaran
                },
                success: function(response){
                    jQuery('#wrap-loading').hide();
                    alert(response.message);
                    if(response.status == 'success'){
                        jQuery('#modalTambahRenja').modal('hide');
                        refresh_page();
                    }
                }
            })
        }
    }

    function delete_renja(kode_sbl){
        if(confirm('Apakah anda yakin untuk menghapus data ini?')){
            jQuery("#wrap-loading").show();
            jQuery.ajax({
                method: 'post',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: {
                    'action': 'delete_renja',
                    'api_key': jQuery('#api_key').val(),
                    'kode_sbl': kode_sbl,
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
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_indikator_program_renja',
                'api_key': jQuery('#api_key').val(),
                'tahun_anggaran': tahun_anggaran,
                'kode_sbl': data  
            },
            success:function(response){
                let html=""
                +'<form>'
                    +'<input type="hidden" name="kode_sbl" value="'+data+'">'
          			+'<table class="table" style="margin-top:10px">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('tr[tipe="sub_unit"]').find('td').eq(1).text().replace('Sub Unit Organisasi : ', '')+'</th>'
	          				+'</tr>'
	          				+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
          						+'<th>'+jQuery('tr[tipe="bidang"]').find('td').eq(5).text()+'</th>'
          					+'</tr>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Program</th>'
	          					+'<th>'+jQuery('tr[tipe="program"][kode="'+data+'"]').find('td').eq(5).text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
                                +'<th class="text-center" style="width: 160px;">Pagu</th>'
	          					+'<th>'+jQuery('tr[tipe="program"][kode="'+data+'"]').find('td').eq(6).html()+'</th>'
	          				+'</tr>'
	          			+'</thead>'
          			+'</table>'
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center'>No</th>"
								+"<th class='text-center' style='width: 120px;'>Tipe</th>"
                                +"<th class='text-center'>Indikator</th>"
								+"<th class='text-center' style='width: 120px;'>Target</th>"
								+"<th class='text-center' style='width: 120px;'>Satuan</th>"
								+"<th class='text-center'>Catatan</th>"
                                +"<th class='text-center'>Aksi</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_program'>";

                        var tombol_tambah = ''
                            +'<button type="button" class="btn btn-warning" onclick="tambahIndikatorProgram();">'
                                +'<i class="dashicons dashicons-plus" style="margin-top: 2px;"></i>'
                            +'</button>';

                        if(response.data.length == 0){
                            html +=''
                            +"<tr data-id='1' type='usulan'>"
                                +"<td class='text-center' rowspan='2' style='vertical-align: middle;'>1</td>"
                                +"<td class='text-center'>Usulan</td>"
                                +"<td><textarea class='form-control' type='text' id='indikator_program_usulan_1' name='indikator_program_usulan[1]'></textarea></td>"
                                +"<td><input class='form-control' type='number' id='target_indikator_program_usulan_1' name='target_indikator_program_usulan[1]'></td>"
                                +"<td><input class='form-control' type='text' id='satuan_indikator_program_usulan_1' name='satuan_indikator_program_usulan[1]'></td>"
                                +"<td><textarea class='form-control' id='catatan_program_usulan_1' name='catatan_program_usulan[1]'></textarea></td>"
                                +"<td rowspan='2' class='text-center' style='vertical-align: middle;'>"+tombol_tambah+"</td>"
                            +"</tr>"
                            +"<tr data-id='1' type='penetapan'>"
                                +"<td class='text-center'>Penetapan</td>"
                                +"<td><textarea class='form-control' type='text' id='indikator_program_penetapan_1' name='indikator_program_penetapan[1]' <?php echo $disabled; ?>></textarea></td>"
                                +"<td><input class='form-control' type='number' id='target_indikator_program_penetapan_1' name='target_indikator_program_penetapan[1]' <?php echo $disabled; ?>></td>"
                                +"<td><input class='form-control' type='text' id='satuan_indikator_program_penetapan_1' name='satuan_indikator_program_penetapan[1]' <?php echo $disabled; ?>></td>"
                                +"<td><textarea class='form-control' id='catatan_program_penetapan_1' name='catatan_program_penetapan[1]' <?php echo $disabled; ?>></textarea></td>"
                            +"</tr>";
                        }else{
    						response.data.map(function(value, index){
                                for(var i in value){
                                    if(value[i] == null){
                                        value[i] = '';
                                    }
                                }

                                if(index == 0){
                                    var aksi = tombol_tambah;
                                }else{
                                    var aksi = ''
                                        +'<button type="button" class="btn btn-danger" onclick="hapusIndikatorProgram(this);">'
                                            +'<i class="dashicons dashicons-trash" style="margin-top: 2px;"></i>'
                                        +'</button>';
                                }
                                var id = index+1;
                                html +=''
                                +"<tr data-id='"+id+"' type='usulan'>"
                                    +"<td class='text-center' rowspan='2' style='vertical-align: middle;'>"+id+"</td>"
                                    +"<td class='text-center'>Usulan</td>"
                                    +"<td><textarea class='form-control' type='text' id='indikator_program_usulan_"+id+"' name='indikator_program_usulan["+id+"]'>"+value.capaianteks_usulan+"</textarea></td>"
                                    +"<td><input class='form-control' type='number' id='target_indikator_program_usulan_"+id+"' name='target_indikator_program_usulan["+id+"]' value='"+value.targetcapaian_usulan+"'></td>"
                                    +"<td><input class='form-control' type='text' id='satuan_indikator_program_usulan_"+id+"' name='satuan_indikator_program_usulan["+id+"]' value='"+value.satuancapaian_usulan+"'></td>"
                                    +"<td><textarea class='form-control' id='catatan_program_usulan_"+id+"' name='catatan_program_usulan["+id+"]'>"+value.catatan_usulan+"</textarea></td>"
                                    +"<td rowspan='2' class='text-center' style='vertical-align: middle;'>"+aksi+"</td>"
                                +"</tr>"
                                +"<tr data-id='"+id+"' type='penetapan'>"
                                    +"<td class='text-center'>Penetapan</td>"
                                    +"<td><textarea class='form-control' type='text' id='indikator_program_penetapan_"+id+"' name='indikator_program_penetapan["+id+"]' <?php echo $disabled; ?>>"+value.capaianteks+"</textarea></td>"
                                    +"<td><input class='form-control' type='number' id='target_indikator_program_penetapan_"+id+"' name='target_indikator_program_penetapan["+id+"]' value='"+value.targetcapaian+"' <?php echo $disabled; ?>></td>"
                                    +"<td><input class='form-control' type='text' id='satuan_indikator_program_penetapan_"+id+"' name='satuan_indikator_program_penetapan["+id+"]' value='"+value.satuancapaian+"' <?php echo $disabled; ?>></td>"
                                    +"<td><textarea class='form-control' id='catatan_program_penetapan_"+id+"' name='catatan_program_penetapan["+id+"]' <?php echo $disabled; ?>>"+value.catatan+"</textarea></td>"
                                +"</tr>";
    		          		});
                        }
		          	html+=''
		          		+'</tbody>'
		          	+'</table>'
					<?php if($is_admin): ?>
						+'<div class="row">'
							+'<div class="col-md-12 text-center">'
								+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
									+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
								+'</button>'
							+'</div>'
						+'</div>'
					<?php endif; ?>
                +'</form>';

                jQuery('#modal-indikator-renja').find('.modal-title').html('Indikator Program');
                jQuery('#modal-indikator-renja').find('.modal-body').html(html);
                jQuery('#modal-indikator-renja').find('.modal-footer .submitBtn').attr('onclick', 'submitIndikatorProgram()');
                jQuery('#modal-indikator-renja').find('.modal-dialog').css('maxWidth','1250px');
                jQuery('#modal-indikator-renja').find('.modal-dialog').css('width','100%');
                jQuery('#modal-indikator-renja').modal('show');
                jQuery('#wrap-loading').hide();
            }
        })
    }

    function detail_program(id_unik){
        jQuery("#modal-indikator-renja").find('.modal-body').html('');
        detailIndikatorProgram(id_unik);
    }

    function detailIndikatorProgram(data){
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_indikator_program_renja',
                'api_key': jQuery('#api_key').val(),
                'tahun_anggaran': tahun_anggaran,
                'kode_sbl': data  
            },
            success:function(response){
                let html=""
                +'<form>'
                    +'<input type="hidden" name="kode_sbl" value="'+data+'">'
          			+'<table class="table" style="margin-top:10px">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('tr[tipe="sub_unit"]').find('td').eq(1).text().replace('Sub Unit Organisasi : ', '')+'</th>'
	          				+'</tr>'
	          				+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
          						+'<th>'+jQuery('tr[tipe="bidang"]').find('td').eq(5).text()+'</th>'
          					+'</tr>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Program</th>'
	          					+'<th>'+jQuery('tr[tipe="program"][kode="'+data+'"]').find('td').eq(5).text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
                                +'<th class="text-center" style="width: 160px;">Pagu</th>'
	          					+'<th>'+jQuery('tr[tipe="program"][kode="'+data+'"]').find('td').eq(6).html()+'</th>'
	          				+'</tr>'
	          			+'</thead>'
          			+'</table>'
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center'>No</th>"
								+"<th class='text-center' style='width: 120px;'>Tipe</th>"
                                +"<th class='text-center'>Indikator</th>"
								+"<th class='text-center' style='width: 120px;'>Target</th>"
								+"<th class='text-center' style='width: 120px;'>Satuan</th>"
								+"<th class='text-center'>Catatan</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_program'>";

                        if(response.data.length == 0){
                            html +=''
                            +"<tr data-id='1' type='usulan'>"
                                +"<td class='text-center' rowspan='2' style='vertical-align: middle;'>1</td>"
                                +"<td class='text-center'>Usulan</td>"
                                +"<td><textarea class='form-control' type='text' id='indikator_program_usulan_1' name='indikator_program_usulan[1]' readonly></textarea></td>"
                                +"<td><input class='form-control' type='number' id='target_indikator_program_usulan_1' name='target_indikator_program_usulan[1]' readonly></td>"
                                +"<td><input class='form-control' type='text' id='satuan_indikator_program_usulan_1' name='satuan_indikator_program_usulan[1]' readonly></td>"
                                +"<td><textarea class='form-control' id='catatan_program_usulan_1' name='catatan_program_usulan[1]' readonly></textarea></td>"
                            +"</tr>"
                            +"<tr data-id='1' type='penetapan'>"
                                +"<td class='text-center'>Penetapan</td>"
                                +"<td><textarea class='form-control' type='text' id='indikator_program_penetapan_1' name='indikator_program_penetapan[1]' readonly></textarea></td>"
                                +"<td><input class='form-control' type='number' id='target_indikator_program_penetapan_1' name='target_indikator_program_penetapan[1]' readonly></td>"
                                +"<td><input class='form-control' type='text' id='satuan_indikator_program_penetapan_1' name='satuan_indikator_program_penetapan[1]' readonly></td>"
                                +"<td><textarea class='form-control' id='catatan_program_penetapan_1' name='catatan_program_penetapan[1]' readonly></textarea></td>"
                            +"</tr>";
                        }else{
    						response.data.map(function(value, index){
                                for(var i in value){
                                    if(value[i] == null){
                                        value[i] = '';
                                    }
                                }
                                var id = index+1;
                                html +=''
                                +"<tr data-id='"+id+"' type='usulan'>"
                                    +"<td class='text-center' rowspan='2' style='vertical-align: middle;'>"+id+"</td>"
                                    +"<td class='text-center'>Usulan</td>"
                                    +"<td><textarea class='form-control' type='text' id='indikator_program_usulan_"+id+"' name='indikator_program_usulan["+id+"]' readonly>"+value.capaianteks_usulan+"</textarea></td>"
                                    +"<td><input class='form-control' type='number' id='target_indikator_program_usulan_"+id+"' name='target_indikator_program_usulan["+id+"]' value='"+value.targetcapaian_usulan+"' readonly></td>"
                                    +"<td><input class='form-control' type='text' id='satuan_indikator_program_usulan_"+id+"' name='satuan_indikator_program_usulan["+id+"]' value='"+value.satuancapaian_usulan+"' readonly></td>"
                                    +"<td><textarea class='form-control' id='catatan_program_usulan_"+id+"' name='catatan_program_usulan["+id+"]' readonly>"+value.catatan_usulan+"</textarea></td>"
                                +"</tr>"
                                +"<tr data-id='"+id+"' type='penetapan'>"
                                    +"<td class='text-center'>Penetapan</td>"
                                    +"<td><textarea class='form-control' type='text' id='indikator_program_penetapan_"+id+"' name='indikator_program_penetapan["+id+"]' readonly>"+value.capaianteks+"</textarea></td>"
                                    +"<td><input class='form-control' type='number' id='target_indikator_program_penetapan_"+id+"' name='target_indikator_program_penetapan["+id+"]' value='"+value.targetcapaian+"' readonly></td>"
                                    +"<td><input class='form-control' type='text' id='satuan_indikator_program_penetapan_"+id+"' name='satuan_indikator_program_penetapan["+id+"]' value='"+value.satuancapaian+"' readonly></td>"
                                    +"<td><textarea class='form-control' id='catatan_program_penetapan_"+id+"' name='catatan_program_penetapan["+id+"]' readonly>"+value.catatan+"</textarea></td>"
                                +"</tr>";
    		          		});
                        }
		          	html+=''
		          		+'</tbody>'
		          	+'</table>'
                +'</form>';

                jQuery('#modal-indikator-renja').find('.modal-title').html('Detail Indikator Program');
                jQuery('#modal-indikator-renja').find('.modal-body').html(html);
                jQuery('#modal-indikator-renja').find('.modal-footer .submitBtn').attr('onclick', 'showIndikatorProgram()').hide();
                jQuery('#modal-indikator-renja').find('.modal-dialog').css('maxWidth','1250px');
                jQuery('#modal-indikator-renja').find('.modal-dialog').css('width','100%');
                jQuery('#modal-indikator-renja').modal('show');
                jQuery('#wrap-loading').hide();
            }
        })
    }

    function edit_kegiatan(id_unik){
        jQuery("#modal-indikator-renja").find('.modal-body').html('');
        indikatorKegiatan(id_unik);
    }

    function indikatorKegiatan(data){
        get_data_prioritas_pusat()
        .then(function(){
            jQuery('#wrap-loading').show();
            let checkProgram = data.split('.')
            checkProgram = checkProgram[0]+'.'+checkProgram[1]+'.'+checkProgram[2];
            jQuery.ajax({
                method: 'post',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: {
                    'action': 'get_indikator_kegiatan_renja',
                    'api_key': jQuery('#api_key').val(),
                    'tahun_anggaran': tahun_anggaran,
                    'kode_sbl': data  
                },
                success:function(response){
                    
                    let sasaran = sasaran_usulan = "";
                    if(response.data.sasaran.sasaran !=  null){
                        sasaran = response.data.sasaran.sasaran;
                    }
                    if(response.data.sasaran.sasaran_usulan != null){
                            sasaran_usulan = response.data.sasaran.sasaran_usulan;
                    }
                    let html=""
                    +'<form>'
              			+'<table class="table" style="margin-top:10px">'
    	          			+'<thead>'
    	          				+'<tr>'
    	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
    	          					+'<th>'+jQuery('tr[tipe="sub_unit"]').find('td').eq(1).text().replace('Sub Unit Organisasi : ', '')+'</th>'
    	          				+'</tr>'
    	          				+'<tr>'
              						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
              						+'<th>'+jQuery('tr[tipe="bidang"]').find('td').eq(5).text()+'</th>'
              					+'</tr>'
    	          				+'<tr>'
    	          					+'<th class="text-center" style="width: 160px;">Program</th>'
    	          					+'<th>'+jQuery('tr[tipe="program"][checkprogram="'+checkProgram+'"]').find('td').eq(5).text()+'</th>'
    	          				+'</tr>'
    	          				+'<tr>'
    	          					+'<th class="text-center" style="width: 160px;">Kegiatan</th>'
    	          					+'<th>'+jQuery('tr[tipe="kegiatan"][kode="'+data+'"]').find('td').eq(5).text()+'</th>'
    	          				+'</tr>'
    	          				+'<tr>'
                                    +'<th class="text-center" style="width: 160px;">Pagu</th>'
    	          					+'<th>'+jQuery('tr[tipe="program"][checkprogram="'+checkProgram+'"]').find('td').eq(6).html()+'</th>'
    	          				+'</tr>'
    	          			+'</thead>'
              			+'</table>'
                        +'</br><h4>Kelompok Sasaran Kegiatan</h4>'
                        +'<table class="table">'
    						+'<thead>'
    							+'<tr>'
    								+'<th class="text-center" style="width:200px;">Tipe</th>'
    								+'<th class="text-center">Kelompok Sasaran</th>'
    							+'</tr>'
    						+'</thead>'
                            +'<tbody id="kelompok_sasaran">'
                                +'<tr>'
                                    +'<td class="text-center">Usulan</td>'
                                    +'<td><textarea class="form-control" id="kelompok_sasaran_usulan" name="kelompok_sasaran_renja_usulan">'+sasaran_usulan+'</textarea></td>'
                                +'</tr>'
                                +'<tr>'
                                    +'<td class="text-center">Penetapan</td>'
                                    +'<td><textarea class="form-control" id="kelompok_sasaran_usulan" name="kelompok_sasaran_renja_penetapan" <?php echo $disabled; ?>>'+sasaran+'</textarea></td>'
                                +'</tr>'
                            +'</tbody>'
                        +'</table>'
                        +'</br><h4>Indikator Keluaran Kegiatan</h4>'
    					+"<table class='table'>"
    						+"<thead>"
    							+"<tr>"
    								+"<th class='text-center'>No</th>"
    								+"<th class='text-center'>Tipe</th>"
                                    +"<th class='text-center'>Indikator</th>"
    								+"<th class='text-center' style='width: 120px;'>Target</th>"
    								+"<th class='text-center' style='width: 120px;'>Satuan</th>"
    								+"<th class='text-center'>Catatan</th>"
                                    +"<th class='text-center'>Aksi</th>"
    							+"</tr>"
    						+"</thead>"
    						+"<tbody id='indikator_kegiatan'>";

                            var tombol_tambah = ''
                                +'<button type="button" class="btn btn-warning" onclick="tambahIndikatorKegiatan();">'
                                    +'<i class="dashicons dashicons-plus" style="margin-top: 2px;"></i>'
                                +'</button>';

                            if(response.data.indi_kegiatan.length == 0){
                                html +=''
                                    +"<tr data-id='1' type='usulan'>"
                                        +"<td class='text-center' rowspan='2' style='vertical-align: middle;'>1</td>"
                                        +"<td class='text-center'>Usulan</td>"
                                        +"<td><textarea class='form-control' type='text' id='indikator_kegiatan_usulan_1' name='indikator_kegiatan_usulan[1]'></textarea></td>"
                                        +"<td><input class='form-control' type='number' id='target_indikator_kegiatan_usulan_1' name='target_indikator_kegiatan_usulan[1]'></td>"
                                        +"<td><input class='form-control' type='text' id='satuan_indikator_kegiatan_usulan_1' name='satuan_indikator_kegiatan_usulan[1]'></td>"
                                        +"<td><textarea class='form-control' id='catatan_kegiatan_usulan_1' name='catatan_indikator_kegiatan_usulan[1]'></textarea></td>"
                                        +"<td rowspan='2' class='text-center' style='vertical-align: middle;'>"+tombol_tambah+"</td>"
                                    +"</tr>"
                                    +"<tr data-id='1' type='penetapan'>"
                                        +"<td class='text-center'>Penetapan</td>"
                                        +"<td><textarea class='form-control' type='text' id='indikator_kegiatan_penetapan_1' name='indikator_kegiatan_penetapan[1]' <?php echo $disabled; ?>></textarea></td>"
                                        +"<td><input class='form-control' type='number' id='target_indikator_kegiatan_penetapan_1' name='target_indikator_kegiatan_penetapan[1]' <?php echo $disabled; ?>></td>"
                                        +"<td><input class='form-control' type='text' id='satuan_indikator_kegiatan_penetapan_1' name='satuan_indikator_kegiatan_penetapan[1]' <?php echo $disabled; ?>></td>"
                                        +"<td><textarea class='form-control' id='catatan_kegiatan_penetapan_1' name='catatan_indikator_kegiatan_penetapan[1]' <?php echo $disabled; ?>></textarea></td>"
                                    +"</tr>";
                            }else{
        						response.data.indi_kegiatan.map(function(value, index){
                                    let id = index+1;
                                    if(index == 0){
                                        var aksi = tombol_tambah;
                                    }else{
                                        var aksi = ''
                                            +'<button type="button" class="btn btn-danger" onclick="hapusIndikatorKegiatan(this);">'
                                                +'<i class="dashicons dashicons-trash" style="margin-top: 2px;"></i>'
                                            +'</button>';
                                    }
        		          			html +=''
        		          				+"<tr data-id='"+id+"' type='usulan'>"
        					          		+"<td class='text-center' rowspan='2' style='vertical-align: middle;'>"+id+"</td>"
                                            +"<td class='text-center'>Usulan</td>"
        					          		+"<td><textarea class='form-control' type='text' id='indikator_kegiatan_usulan_"+id+"' name='indikator_kegiatan_usulan["+id+"]'>"+value.outputteks_usulan+"</textarea></td>"
        					          		+"<td><input class='form-control' type='number' id='target_indikator_kegiatan_usulan_"+id+"' name='target_indikator_kegiatan_usulan["+id+"]' value='"+value.targetoutput_usulan+"'></td>"
        					          		+"<td><input class='form-control' type='text' id='satuan_indikator_kegiatan_usulan_"+id+"' name='satuan_indikator_kegiatan_usulan["+id+"]' value='"+value.satuanoutput_usulan+"'></td>"
        									+"<td><textarea class='form-control' id='catatan_kegiatan_usulan_"+id+"' name='catatan_indikator_kegiatan_usulan["+id+"]'>"+value.catatan_usulan+"</textarea></td>"
                                            +"<td rowspan='2' class='text-center' style='vertical-align: middle;'>"+aksi+"</td>"
        					          	+"</tr>"
        		          				+"<tr data-id='"+id+"' type='penetapan'>"
                                            +"<td class='text-center'>Penetapan</td>"
                                            +"<td><textarea class='form-control' type='text' id='indikator_kegiatan_penetapan_"+id+"' name='indikator_kegiatan_penetapan["+id+"]' <?php echo $disabled; ?>>"+value.outputteks+"</textarea></td>"
                                            +"<td><input class='form-control' type='number' id='target_indikator_kegiatan_penetapan_"+id+"' name='target_indikator_kegiatan_penetapan["+id+"]' value='"+value.targetoutput+"' <?php echo $disabled; ?>></td>"
                                            +"<td><input class='form-control' type='text' id='satuan_indikator_kegiatan_penetapan_"+id+"' name='satuan_indikator_kegiatan_penetapan["+id+"]' value='"+value.satuanoutput+"' <?php echo $disabled; ?>></td>"
                                            +"<td><textarea class='form-control' id='catatan_kegiatan_penetapan_"+id+"' name='catatan_indikator_kegiatan_penetapan["+id+"]' <?php echo $disabled; ?>>"+value.catatan+"</textarea></td>"
        					          	+"</tr>";
        		          		});
                            }
    		          	html+=''
    		          		+'</tbody>'
    		          	+'</table>'
                        +'</br><h4>Indikator Hasil Kegiatan</h4>'
    					+"<table class='table'>"
    						+"<thead>"
    							+"<tr>"
    								+"<th class='text-center'>No</th>"
    								+"<th class='text-center'>Tipe</th>"
                                    +"<th class='text-center'>Indikator</th>"
    								+"<th class='text-center' style='width: 120px;'>Target</th>"
    								+"<th class='text-center' style='width: 120px;'>Satuan</th>"
    								+"<th class='text-center'>Catatan</th>"
                                    +"<th class='text-center'>Aksi</th>"
    							+"</tr>"
    						+"</thead>"
    						+"<tbody id='indikator_hasil_kegiatan'>";

                            var tombol_tambah = ''
                                +'<button type="button" class="btn btn-warning" onclick="tambahIndikatorHasilKegiatan();">'
                                    +'<i class="dashicons dashicons-plus" style="margin-top: 2px;"></i>'
                                +'</button>';

                            if(response.data.indi_kegiatan_hasil.length == 0){
                                html +=''
                                    +"<tr data-id='1' type='usulan'>"
                                        +"<td class='text-center' rowspan='2' style='vertical-align: middle;'>1</td>"
                                        +"<td class='text-center'>Usulan</td>"
                                        +"<td><textarea class='form-control' type='text' id='indikator_hasil_kegiatan_usulan_1' name='indikator_hasil_kegiatan_usulan[1]'></textarea></td>"
                                        +"<td><input class='form-control' type='number' id='target_indikator_hasil_kegiatan_usulan_1' name='target_indikator_hasil_kegiatan_usulan[1]'></td>"
                                        +"<td><input class='form-control' type='text' id='satuan_indikator_hasil_kegiatan_usulan_1' name='satuan_indikator_hasil_kegiatan_usulan[1]'></td>"
                                        +"<td><textarea class='form-control' id='catatan_hasil_kegiatan_usulan_1' name='catatan_indikator_hasil_kegiatan_usulan[1]'></textarea></td>"
                                        +"<td rowspan='2' class='text-center' style='vertical-align: middle;'>"+tombol_tambah+"</td>"
                                    +"</tr>"
                                    +"<tr data-id='1' type='penetapan'>"
                                        +"<td class='text-center'>Penetapan</td>"
                                        +"<td><textarea class='form-control' type='text' id='indikator_hasil_kegiatan_penetapan_1' name='indikator_hasil_kegiatan_penetapan[1]' <?php echo $disabled; ?>></textarea></td>"
                                        +"<td><input class='form-control' type='number' id='target_indikator_hasil_kegiatan_penetapan_1' name='target_indikator_hasil_kegiatan_penetapan[1]' <?php echo $disabled; ?>></td>"
                                        +"<td><input class='form-control' type='text' id='satuan_indikator_hasil_kegiatan_penetapan_1' name='satuan_indikator_hasil_kegiatan_penetapan[1]' <?php echo $disabled; ?>></td>"
                                        +"<td><textarea class='form-control' id='catatan_hasil_kegiatan_penetapan_1' name='catatan_indikator_hasil_kegiatan_penetapan[1]' <?php echo $disabled; ?>></textarea></td>"
                                    +"</tr>";
                            }else{
        						response.data.indi_kegiatan_hasil.map(function(value, index){
                                    let id = index+1;
                                    if(index == 0){
                                        var aksi = tombol_tambah;
                                    }else{
                                        var aksi = ''
                                            +'<button type="button" class="btn btn-danger" onclick="hapusIndikatorHasilKegiatan(this);">'
                                                +'<i class="dashicons dashicons-trash" style="margin-top: 2px;"></i>'
                                            +'</button>';
                                    }
        		          			html +=''
        		          				+"<tr data-id='"+id+"' type='usulan'>"
        					          		+"<td class='text-center' rowspan='2' style='vertical-align: middle;'>"+id+"</td>"
                                            +"<td class='text-center'>Usulan</td>"
        					          		+"<td><textarea class='form-control' type='text' id='indikator_hasil_kegiatan_usulan_"+id+"' name='indikator_hasil_kegiatan_usulan["+id+"]'>"+value.hasilteks_usulan+"</textarea></td>"
        					          		+"<td><input class='form-control' type='number' id='target_indikator_hasil_kegiatan_usulan_"+id+"' name='target_indikator_hasil_kegiatan_usulan["+id+"]' value='"+value.targethasil_usulan+"'></td>"
        					          		+"<td><input class='form-control' type='text' id='satuan_indikator_hasil_kegiatan_usulan_"+id+"' name='satuan_indikator_hasil_kegiatan_usulan["+id+"]' value='"+value.satuanhasil_usulan+"'></td>"
        									+"<td><textarea class='form-control' id='catatan_hasil_kegiatan_usulan_"+id+"' name='catatan_indikator_hasil_kegiatan_usulan["+id+"]'>"+value.catatan_usulan+"</textarea></td>"
                                            +"<td rowspan='2' class='text-center' style='vertical-align: middle;'>"+aksi+"</td>"
        					          	+"</tr>"
        		          				+"<tr data-id='"+id+"' type='penetapan'>"
                                            +"<td class='text-center'>Penetapan</td>"
                                            +"<td><textarea class='form-control' type='text' id='indikator_hasil_kegiatan_penetapan_"+id+"' name='indikator_hasil_kegiatan_penetapan["+id+"]' <?php echo $disabled; ?>>"+value.hasilteks+"</textarea></td>"
                                            +"<td><input class='form-control' type='number' id='target_indikator_hasil_kegiatan_penetapan_"+id+"' name='target_indikator_hasil_kegiatan_penetapan["+id+"]' value='"+value.targethasil+"' <?php echo $disabled; ?>></td>"
                                            +"<td><input class='form-control' type='text' id='satuan_indikator_hasil_kegiatan_penetapan_"+id+"' name='satuan_indikator_hasil_kegiatan_penetapan["+id+"]' value='"+value.satuanhasil+"' <?php echo $disabled; ?>></td>"
                                            +"<td><textarea class='form-control' id='catatan_hasil_kegiatan_penetapan_"+id+"' name='catatan_indikator_hasil_kegiatan_penetapan["+id+"]' <?php echo $disabled; ?>>"+value.catatan+"</textarea></td>"
        					          	+"</tr>";
        		          		});
                            }
    		          	html+=''
    		          		+'</tbody>'
    		          	+'</table>'
                        +'</br><h4>Prioritas Pembangunan Nasional</h4>'
                        +"<select class='form-control' name='input_prioritas_nasional' id='input_prioritas_nasional'></select>"
    					<?php if($is_admin): ?>
    						+'<div class="row">'
    							+'<div class="col-md-12 text-center">'
    								+'<button onclick="copy_usulan(this); return false;" type="button" class="btn btn-danger" style="margin-top: 20px;">'
    									+'<i class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></i> Copy Data Usulan ke Penetapan'
    								+'</button>'
    							+'</div>'
    						+'</div>'
    					<?php endif; ?>
                    +'</form>';

                    jQuery('#modal-indikator-renja').find('.modal-title').html('Indikator Kegiatan');
                    jQuery('#modal-indikator-renja').find('.modal-body').html(html);
                    jQuery('#modal-indikator-renja').find('.modal-footer .submitBtn').attr('onclick', 'submitIndikatorKegiatan("'+data+'")');
                    jQuery('#modal-indikator-renja').find('.modal-dialog').css('maxWidth','1250px');
                    jQuery('#modal-indikator-renja').find('.modal-dialog').css('width','100%');
                    jQuery('#modal-indikator-renja').modal('show');

                    jQuery("#input_prioritas_nasional").html(dataPrioritasPusat.table_content);
                    jQuery('#input_prioritas_nasional').select2({width: '100%'});
                    if(response.data.sasaran.id_label_pusat && response.data.sasaran.id_label_pusat != 0){
                        jQuery("#input_prioritas_nasional").val(response.data.sasaran.id_label_pusat).trigger('change');
                    }

                    jQuery('#wrap-loading').hide();
                }
            })
        })
    }

    function detail_kegiatan(id_unik){
        jQuery("#modal-indikator-renja").find('.modal-body').html('');
        detailIndikatorKegiatan(id_unik);
    }

    function detailIndikatorKegiatan(data){
        jQuery('#wrap-loading').show();
        let checkProgram = data.split('.')
        checkProgram = checkProgram[0]+'.'+checkProgram[1]+'.'+checkProgram[2];
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_indikator_kegiatan_renja',
                'api_key': jQuery('#api_key').val(),
                'tahun_anggaran': tahun_anggaran,
                'kode_sbl': data  
            },
            success:function(response){
                
                let sasaran = sasaran_usulan = "";
                if(response.data.sasaran.sasaran !=  null){
                    sasaran = response.data.sasaran.sasaran;
                }
                if(response.data.sasaran.sasaran_usulan != null){
                        sasaran_usulan = response.data.sasaran.sasaran_usulan;
                }
                let html=""
                +'<form>'
          			+'<table class="table" style="margin-top:10px">'
	          			+'<thead>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Perangkat Daerah</th>'
	          					+'<th>'+jQuery('tr[tipe="sub_unit"]').find('td').eq(1).text().replace('Sub Unit Organisasi : ', '')+'</th>'
	          				+'</tr>'
	          				+'<tr>'
          						+'<th class="text-center" style="width: 160px;">Bidang Urusan</th>'
          						+'<th>'+jQuery('tr[tipe="bidang"]').find('td').eq(5).text()+'</th>'
          					+'</tr>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Program</th>'
	          					+'<th>'+jQuery('tr[tipe="program"][checkprogram="'+checkProgram+'"]').find('td').eq(5).text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
	          					+'<th class="text-center" style="width: 160px;">Kegiatan</th>'
	          					+'<th>'+jQuery('tr[tipe="kegiatan"][kode="'+data+'"]').find('td').eq(5).text()+'</th>'
	          				+'</tr>'
	          				+'<tr>'
                                +'<th class="text-center" style="width: 160px;">Pagu</th>'
	          					+'<th>'+jQuery('tr[tipe="program"][checkprogram="'+checkProgram+'"]').find('td').eq(6).html()+'</th>'
	          				+'</tr>'
	          			+'</thead>'
          			+'</table>'
                    +'</br><h4>Kelompok Sasaran Kegiatan</h4>'
                    +'<table class="table">'
						+'<thead>'
							+'<tr>'
								+'<th class="text-center" style="width:200px;">Tipe</th>'
								+'<th class="text-center">Kelompok Sasaran</th>'
							+'</tr>'
						+'</thead>'
                        +'<tbody id="kelompok_sasaran">'
                            +'<tr>'
                                +'<td class="text-center">Usulan</td>'
                                +'<td><textarea class="form-control" id="kelompok_sasaran_usulan" name="kelompok_sasaran_renja_usulan" readonly>'+sasaran_usulan+'</textarea></td>'
                            +'</tr>'
                            +'<tr>'
                                +'<td class="text-center">Penetapan</td>'
                                +'<td><textarea class="form-control" id="kelompok_sasaran_usulan" name="kelompok_sasaran_renja_penetapan" readonly>'+sasaran+'</textarea></td>'
                            +'</tr>'
                        +'</tbody>'
                    +'</table>'
                    +'</br><h4>Indikator Keluaran Kegiatan</h4>'
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center'>No</th>"
								+"<th class='text-center'>Tipe</th>"
                                +"<th class='text-center'>Indikator</th>"
								+"<th class='text-center' style='width: 120px;'>Target</th>"
								+"<th class='text-center' style='width: 120px;'>Satuan</th>"
								+"<th class='text-center'>Catatan</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_kegiatan'>";

                        if(response.data.indi_kegiatan.length == 0){
                            html +=''
                                +"<tr data-id='1' type='usulan'>"
                                    +"<td class='text-center' rowspan='2' style='vertical-align: middle;'>1</td>"
                                    +"<td class='text-center'>Usulan</td>"
                                    +"<td><textarea class='form-control' type='text' id='indikator_kegiatan_usulan_1' name='indikator_kegiatan_usulan[1]' readonly></textarea></td>"
                                    +"<td><input class='form-control' type='number' id='target_indikator_kegiatan_usulan_1' name='target_indikator_kegiatan_usulan[1]' readonly></td>"
                                    +"<td><input class='form-control' type='text' id='satuan_indikator_kegiatan_usulan_1' name='satuan_indikator_kegiatan_usulan[1]' readonly></td>"
                                    +"<td><textarea class='form-control' id='catatan_kegiatan_usulan_1' name='catatan_indikator_kegiatan_usulan[1]' readonly></textarea></td>"
                                +"</tr>"
                                +"<tr data-id='1' type='penetapan'>"
                                    +"<td class='text-center'>Penetapan</td>"
                                    +"<td><textarea class='form-control' type='text' id='indikator_kegiatan_penetapan_1' name='indikator_kegiatan_penetapan[1]' readonly></textarea></td>"
                                    +"<td><input class='form-control' type='number' id='target_indikator_kegiatan_penetapan_1' name='target_indikator_kegiatan_penetapan[1]' readonly></td>"
                                    +"<td><input class='form-control' type='text' id='satuan_indikator_kegiatan_penetapan_1' name='satuan_indikator_kegiatan_penetapan[1]' readonly></td>"
                                    +"<td><textarea class='form-control' id='catatan_kegiatan_penetapan_1' name='catatan_indikator_kegiatan_penetapan[1]' readonly></textarea></td>"
                                +"</tr>";
                        }else{
    						response.data.indi_kegiatan.map(function(value, index){
                                let id = index+1;
    		          			html +=''
    		          				+"<tr data-id='"+id+"' type='usulan'>"
    					          		+"<td class='text-center' rowspan='2' style='vertical-align: middle;'>"+id+"</td>"
                                        +"<td class='text-center'>Usulan</td>"
    					          		+"<td><textarea class='form-control' type='text' id='indikator_kegiatan_usulan_"+id+"' name='indikator_kegiatan_usulan["+id+"]' readonly>"+value.outputteks_usulan+"</textarea></td>"
    					          		+"<td><input class='form-control' type='number' id='target_indikator_kegiatan_usulan_"+id+"' name='target_indikator_kegiatan_usulan["+id+"]' value='"+value.targetoutput_usulan+"' readonly></td>"
    					          		+"<td><input class='form-control' type='text' id='satuan_indikator_kegiatan_usulan_"+id+"' name='satuan_indikator_kegiatan_usulan["+id+"]' value='"+value.satuanoutput_usulan+"' readonly></td>"
    									+"<td><textarea class='form-control' id='catatan_kegiatan_usulan_"+id+"' name='catatan_indikator_kegiatan_usulan["+id+"]' readonly>"+value.catatan_usulan+"</textarea></td>"
    					          	+"</tr>"
    		          				+"<tr data-id='"+id+"' type='penetapan'>"
                                        +"<td class='text-center'>Penetapan</td>"
                                        +"<td><textarea class='form-control' type='text' id='indikator_kegiatan_penetapan_"+id+"' name='indikator_kegiatan_penetapan["+id+"]' readonly>"+value.outputteks+"</textarea></td>"
                                        +"<td><input class='form-control' type='number' id='target_indikator_kegiatan_penetapan_"+id+"' name='target_indikator_kegiatan_penetapan["+id+"]' value='"+value.targetoutput+"' readonly></td>"
                                        +"<td><input class='form-control' type='text' id='satuan_indikator_kegiatan_penetapan_"+id+"' name='satuan_indikator_kegiatan_penetapan["+id+"]' value='"+value.satuanoutput+"' readonly></td>"
                                        +"<td><textarea class='form-control' id='catatan_kegiatan_penetapan_"+id+"' name='catatan_indikator_kegiatan_penetapan["+id+"]' readonly>"+value.catatan+"</textarea></td>"
    					          	+"</tr>";
    		          		});
                        }
		          	html+=''
		          		+'</tbody>'
		          	+'</table>'
                    +'</br><h4>Indikator Hasil Kegiatan</h4>'
					+"<table class='table'>"
						+"<thead>"
							+"<tr>"
								+"<th class='text-center'>No</th>"
								+"<th class='text-center'>Tipe</th>"
                                +"<th class='text-center'>Indikator</th>"
								+"<th class='text-center' style='width: 120px;'>Target</th>"
								+"<th class='text-center' style='width: 120px;'>Satuan</th>"
								+"<th class='text-center'>Catatan</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody id='indikator_hasil_kegiatan'>";

                        if(response.data.indi_kegiatan_hasil.length == 0){
                            html +=''
                                +"<tr data-id='1' type='usulan'>"
                                    +"<td class='text-center' rowspan='2' style='vertical-align: middle;'>1</td>"
                                    +"<td class='text-center'>Usulan</td>"
                                    +"<td><textarea class='form-control' type='text' id='indikator_hasil_kegiatan_usulan_1' name='indikator_hasil_kegiatan_usulan[1]' readonly></textarea></td>"
                                    +"<td><input class='form-control' type='number' id='target_indikator_hasil_kegiatan_usulan_1' name='target_indikator_hasil_kegiatan_usulan[1]' readonly></td>"
                                    +"<td><input class='form-control' type='text' id='satuan_indikator_hasil_kegiatan_usulan_1' name='satuan_indikator_hasil_kegiatan_usulan[1]' readonly></td>"
                                    +"<td><textarea class='form-control' id='catatan_hasil_kegiatan_usulan_1' name='catatan_indikator_hasil_kegiatan_usulan[1]' readonly></textarea></td>"
                                +"</tr>"
                                +"<tr data-id='1' type='penetapan'>"
                                    +"<td class='text-center'>Penetapan</td>"
                                    +"<td><textarea class='form-control' type='text' id='indikator_hasil_kegiatan_penetapan_1' name='indikator_hasil_kegiatan_penetapan[1]' readonly></textarea></td>"
                                    +"<td><input class='form-control' type='number' id='target_indikator_hasil_kegiatan_penetapan_1' name='target_indikator_hasil_kegiatan_penetapan[1]' readonly></td>"
                                    +"<td><input class='form-control' type='text' id='satuan_indikator_hasil_kegiatan_penetapan_1' name='satuan_indikator_hasil_kegiatan_penetapan[1]' readonly></td>"
                                    +"<td><textarea class='form-control' id='catatan_hasil_kegiatan_penetapan_1' name='catatan_indikator_hasil_kegiatan_penetapan[1]' readonly></textarea></td>"
                                +"</tr>";
                        }else{
    						response.data.indi_kegiatan_hasil.map(function(value, index){
                                let id = index+1;
    		          			html +=''
    		          				+"<tr data-id='"+id+"' type='usulan'>"
    					          		+"<td class='text-center' rowspan='2' style='vertical-align: middle;'>"+id+"</td>"
                                        +"<td class='text-center'>Usulan</td>"
    					          		+"<td><textarea class='form-control' type='text' id='indikator_hasil_kegiatan_usulan_"+id+"' name='indikator_hasil_kegiatan_usulan["+id+"]' readonly>"+value.hasilteks_usulan+"</textarea></td>"
    					          		+"<td><input class='form-control' type='number' id='target_indikator_hasil_kegiatan_usulan_"+id+"' name='target_indikator_hasil_kegiatan_usulan["+id+"]' value='"+value.targethasil_usulan+"' readonly></td>"
    					          		+"<td><input class='form-control' type='text' id='satuan_indikator_hasil_kegiatan_usulan_"+id+"' name='satuan_indikator_hasil_kegiatan_usulan["+id+"]' value='"+value.satuanhasil_usulan+"' readonly></td>"
    									+"<td><textarea class='form-control' id='catatan_hasil_kegiatan_usulan_"+id+"' name='catatan_indikator_hasil_kegiatan_usulan["+id+"]' readonly>"+value.catatan_usulan+"</textarea></td>"
    					          	+"</tr>"
    		          				+"<tr data-id='"+id+"' type='penetapan'>"
                                        +"<td class='text-center'>Penetapan</td>"
                                        +"<td><textarea class='form-control' type='text' id='indikator_hasil_kegiatan_penetapan_"+id+"' name='indikator_hasil_kegiatan_penetapan["+id+"]' readonly>"+value.hasilteks+"</textarea></td>"
                                        +"<td><input class='form-control' type='number' id='target_indikator_hasil_kegiatan_penetapan_"+id+"' name='target_indikator_hasil_kegiatan_penetapan["+id+"]' value='"+value.targethasil+"' readonly></td>"
                                        +"<td><input class='form-control' type='text' id='satuan_indikator_hasil_kegiatan_penetapan_"+id+"' name='satuan_indikator_hasil_kegiatan_penetapan["+id+"]' value='"+value.satuanhasil+"' readonly></td>"
                                        +"<td><textarea class='form-control' id='catatan_hasil_kegiatan_penetapan_"+id+"' name='catatan_indikator_hasil_kegiatan_penetapan["+id+"]' readonly>"+value.catatan+"</textarea></td>"
    					          	+"</tr>";
    		          		});
                        }
                    html+=''
                        +'</tbody>'
                    +'</table>'
                    +'</br><h4>Prioritas Pembangunan Nasional</h4>'
                    +"<textarea class='form-control' id='input_prioritas_nasional' readonly>"+response.data.sasaran.label_pusat+"</textarea>"
                +'</form>';

                jQuery('#modal-indikator-renja').find('.modal-title').html('Detail Indikator Kegiatan');
                jQuery('#modal-indikator-renja').find('.modal-body').html(html);
                jQuery('#modal-indikator-renja').find('.modal-footer .submitBtn').attr('onclick', 'showIndikatorKegiatan("'+data+'")').hide();
                jQuery('#modal-indikator-renja').find('.modal-dialog').css('maxWidth','1250px');
                jQuery('#modal-indikator-renja').find('.modal-dialog').css('width','100%');
                jQuery('#modal-indikator-renja').modal('show');
                jQuery('#wrap-loading').hide();
            }
        })
    }

    function submitIndikatorKegiatan(data){
        if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
            jQuery('#wrap-loading').show();
            let form = getFormData(jQuery("#modal-indikator-renja form"));
            jQuery.ajax({
                method: 'post',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: {
                    'action': 'submit_indikator_kegiatan_renja',
                    'api_key': jQuery('#api_key').val(),
                    'data': JSON.stringify(form),
                    'kode_sbl': data,
                    'tahun_anggaran': tahun_anggaran
                },
                success: function(response){
                    jQuery('#wrap-loading').hide();
                    alert(response.message);
                    if(response.status == 'success'){
                        jQuery('#modal-indikator-renja').modal('hide');
                        refresh_page();
                    }
                }
            })
        }
    }

    function getFormData($form){
        var disabled = $form.find('[disabled]');
        disabled.map(function(i, b){
            jQuery(b).attr('disabled', false);
        });
	    let unindexed_array = $form.serializeArray();
        disabled.map(function(i, b){
            jQuery(b).attr('disabled', true);
        });
        var data = {};
        unindexed_array.map(function(b, i){
            var nama_baru = b.name.split('[');
            if(nama_baru.length > 1){
                nama_baru = nama_baru[0];
                if(!data[nama_baru]){
                    data[nama_baru] = [];
                }
                data[nama_baru].push(b.value);
            }else{
                data[b.name] = b.value;
            }
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

    function setSatuan(that){
        var val = jQuery(that).val();
        var tr_id = jQuery(that).closest('tr').attr('data-id');
        var is_admin = <?php echo $js_check_admin; ?>;
        jQuery('.satuan_pagu_indi_sub_keg[name="input_satuan_usulan['+tr_id+']"]').val(val).trigger('change');
        if(is_admin == 1){
            jQuery('.pagu_indi_sub_keg[name="input_indikator_sub_keg['+tr_id+']"]').val(val).trigger('change');
            jQuery('.satuan_pagu_indi_sub_keg[name="input_satuan['+tr_id+']"]').val(val).trigger('change');
        }
    }

    function set_penetapan(that){
        var is_admin = <?php echo $js_check_admin; ?>;
        if(is_admin == 1){
            var id_penetapan = jQuery(that).attr('id').replaceAll('_usulan', '');
            jQuery('#'+id_penetapan).val(jQuery(that).val()).trigger('change');
        }
    }

    function set_anggaran(that){
        let that_id = jQuery(that).attr('id');
        if(that_id.includes("_usulan")){
            var tbody = jQuery('.input_sumber_dana_usulan > tbody');
            var tr = tbody.find('>tr');
            let total = 0;
            tr.map(function(i, b){
                let id = i+1;
                let dana = jQuery("#pagu_sumber_dana_usulan_"+id).val()
                total = total + parseInt(dana);
            });
            jQuery("#pagu_sub_kegiatan_usulan").val(total);
        }else{
            var tbody = jQuery('.input_sumber_dana > tbody');
            var tr = tbody.find('>tr');
            let total = 0;
            tr.map(function(i, b){
                let id = i+1;
                let dana = jQuery("#pagu_sumber_dana_"+id).val()
                total = total + parseInt(dana);
            });
            jQuery("#pagu_sub_kegiatan").val(total);
        }
    }

    function set_penetapan_multiple(that){
        var is_admin = <?php echo $js_check_admin; ?>;
        if(is_admin == 1){
            var val = jQuery(that).val();
            jQuery('#label_tag').val(val);
            jQuery('#label_tag').trigger('change');
        }
    }

    function hide_usulan(that){
        if(hide_data_usulan == 0){
            location.href = URL_add_parameter(location.href, 'hide_usulan', '1');
        }else{
            location.href = URL_add_parameter(location.href, 'hide_usulan', '0');
        }
    }

    function hide_penetapan(that){
        if(hide_data_penetapan == 0){
            location.href = URL_add_parameter(location.href, 'hide_penetapan', '1');
        }else{
            location.href = URL_add_parameter(location.href, 'hide_penetapan', '0');
        }
    }

    function show_sub_unit(that){
        if(id_sub_unit == 0){
            location.href = URL_add_parameter(location.href, 'id_sub_skpd', '1');
        }else{
            location.href = URL_add_parameter(location.href, 'id_sub_skpd', '0');
        }
    }

    function URL_add_parameter(url, param, value){
        var hash       = {};
        var parser     = document.createElement('a');

        parser.href    = url;

        var parameters = parser.search.split(/\?|&/);

        for(var i=0; i < parameters.length; i++) {
            if(!parameters[i])
                continue;

            var ary      = parameters[i].split('=');
            hash[ary[0]] = ary[1];
        }

        hash[param] = value;

        var list = [];  
        Object.keys(hash).forEach(function (key) {
            list.push(key + '=' + hash[key]);
        });

        
        parser.search = '?' + list.join('&');
        console.log('modify link'+parser.href)
        return parser.href;
    }

    function copy_usulan(that){
		var modal = jQuery(that).closest('.modal-dialog');
        //program
        var total_prog = modal.find('#indikator_program tr:last-child').attr('data-id');
        total_prog = total_prog+1;
        for (let step = 1; step < total_prog; step++) {
            var usulan = modal.find('textarea[name="indikator_program_usulan['+step+']"]').val();
            modal.find('textarea[name="indikator_program_penetapan['+step+']"]').val(usulan);
            var usulan = modal.find('input[name="target_indikator_program_usulan['+step+']"]').val();
            modal.find('input[name="target_indikator_program_penetapan['+step+']"]').val(usulan);
            var usulan = modal.find('input[name="satuan_indikator_program_usulan['+step+']"]').val();
            modal.find('input[name="satuan_indikator_program_penetapan['+step+']"]').val(usulan);
            var usulan = modal.find('textarea[name="catatan_program_usulan['+step+']"]').val();
            modal.find('textarea[name="catatan_program_penetapan['+step+']"]').val(usulan);
        }
        //kegiatan
		var usulan = modal.find('textarea[name="kelompok_sasaran_renja_usulan"]').val();
		modal.find('textarea[name="kelompok_sasaran_renja_penetapan"]').val(usulan);
        var total_giat = modal.find('#indikator_kegiatan tr:last-child').attr('data-id');
        total_giat = total_giat+1;
        for (let step = 1; step < total_giat; step++) {
            var usulan = modal.find('textarea[name="indikator_kegiatan_usulan['+step+']"]').val();
            modal.find('textarea[name="indikator_kegiatan_penetapan['+step+']"]').val(usulan);
            var usulan = modal.find('input[name="target_indikator_kegiatan_usulan['+step+']"]').val();
            modal.find('input[name="target_indikator_kegiatan_penetapan['+step+']"]').val(usulan);
            var usulan = modal.find('input[name="satuan_indikator_kegiatan_usulan['+step+']"]').val();
            modal.find('input[name="satuan_indikator_kegiatan_penetapan['+step+']"]').val(usulan);
            var usulan = modal.find('textarea[name="catatan_indikator_kegiatan_usulan['+step+']"]').val();
            modal.find('textarea[name="catatan_indikator_kegiatan_penetapan['+step+']"]').val(usulan);
        }
        var total_hasil = modal.find('#indikator_kegiatan tr:last-child').attr('data-id');
        total_hasil = total_hasil+1;
        for (let step = 1; step < total_hasil; step++) {
            var usulan = modal.find('textarea[name="indikator_hasil_kegiatan_usulan['+step+']"]').val();
            modal.find('textarea[name="indikator_hasil_kegiatan_penetapan['+step+']"]').val(usulan);
            var usulan = modal.find('input[name="target_indikator_hasil_kegiatan_usulan['+step+']"]').val();
            modal.find('input[name="target_indikator_hasil_kegiatan_penetapan['+step+']"]').val(usulan);
            var usulan = modal.find('input[name="satuan_indikator_hasil_kegiatan_usulan['+step+']"]').val();
            modal.find('input[name="satuan_indikator_hasil_kegiatan_penetapan['+step+']"]').val(usulan);
            var usulan = modal.find('textarea[name="catatan_indikator_hasil_kegiatan_usulan['+step+']"]').val();
            modal.find('textarea[name="catatan_indikator_hasil_kegiatan_penetapan['+step+']"]').val(usulan);
        }
        //sub_kegiatan
        var total_sumber = modal.find('#sumber_dana_usulan tr:last-child').attr('data-id');
        total_sumber = total_sumber+1;
        for (let step = 1; step < total_sumber; step++) {
            var usulan = modal.find('input[name="input_pagu_sumber_dana_usulan['+step+']"]').val();
            modal.find('input[name="input_pagu_sumber_dana['+step+']"]').val(usulan);
        }
        var total_sub_keg = modal.find('#pagu_ind_sub_keg_usulan tr:last-child').attr('data-id');
        total_sub_keg = total_sub_keg+1;
        for (let step = 1; step < total_sub_keg; step++) {
            var usulan = modal.find('input[name="input_target_usulan['+step+']"]').val();
            modal.find('input[name="input_target['+step+']"]').val(usulan);
        }
        var usulan = modal.find('input[name="input_pagu_sub_keg_usulan"]').val();
        modal.find('input[name="input_pagu_sub_keg"]').val(usulan)
        var usulan = modal.find('input[name="input_pagu_sub_keg_1_usulan"]').val();
        modal.find('input[name="input_pagu_sub_keg_1"]').val(usulan)
        var usulan = modal.find('textarea[name="input_catatan_usulan"]').val();
        modal.find('textarea[name="input_catatan"]').val(usulan);
	}

    function copy_usulan_all(){
		if(confirm('Apakah anda yakin untuk melakukan ini? data penetapan akan diupdate sama dengan data usulan.')){
            let id_skpd = "<?php echo $input['id_skpd']; ?>";
            if(id_skpd == ''){
                alert('Id SKPD Kosong')
            }else{
                jQuery('#wrap-loading').show();
                jQuery.ajax({
                    method: 'post',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    dataType: "json",
                    data: {
                    "action": "copy_usulan_renja",
                    "api_key": jQuery('#api_key').val(),
                    "id_skpd": id_skpd,
                    "tahun_anggaran": tahun_anggaran
                    },
                    success: function(res){
                        jQuery('#wrap-loading').hide();
                        alert(res.message);
                        if(res.status == 'success'){
                            refresh_page();
                        }
                    }
                });
            }
		}
	}

    function get_data_label_tag(){
        return new Promise(function(resolve, reject){
            if(typeof master_label_tag == 'undefined'){
                jQuery("#wrap-loading").show();
                jQuery.ajax({
                    method: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    dataType: 'json',
                    data: {
                        'action': 'get_label_sub_keg',
                        'api_key': jQuery('#api_key').val(),
                        'tahun_anggaran': tahun_anggaran
                    },
                    success:function(response){
                        window.master_label_tag = response.data;
                        jQuery("#wrap-loading").hide();
                        let option='';
        				response.data.map(function(value, index){
                            option+='<option value="'+value.id_label_giat+'">'+value.nama_label+'</option>';
                        })

        				jQuery(".label_tag_usulan").html(option);
                        jQuery(".label_tag_usulan").select2({width: '100%'});
                        jQuery(".label_tag").html(option);
                        jQuery(".label_tag").select2({width: '100%'});
                        resolve();
                    }
                });
            }else{
                jQuery('.input_label_tag_usulan tbody tr').map(function(i, b){
                    if(i >= 1){
                        jQuery(b).remove();
                    }
                });
                jQuery('.input_label_tag tbody tr').map(function(i, b){
                    if(i >= 1){
                        jQuery(b).remove();
                    }
                });
                resolve();
            }
        });
    }

    function copy_renja_sipd_to_lokal(){
		if(confirm('Apakah anda yakin untuk melakukan ini? data RENJA lokal akan diupdate sama dengan data RENJA SIPD.')){
            var copy_data_option = [];
            jQuery('input[name=copyDataSipd]:checked').each(function() {
                copy_data_option.push(jQuery(this).val());
            });

            let id_skpd = "<?php echo $input['id_skpd']; ?>";
            if(id_skpd == ''){
                alert('Id SKPD Kosong')
            }else{
                jQuery('#wrap-loading').show();
                jQuery.ajax({
                    method: 'post',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    dataType: "json",
                    data: {
                        "action": "copy_renja_sipd_to_lokal",
                        "api_key": jQuery('#api_key').val(),
                        "id_skpd": id_skpd,
                        "tahun_anggaran": tahun_anggaran,
                        "copy_data_option": copy_data_option
                    },
                    success: function(res){
                        jQuery('#wrap-loading').hide();
                        alert(res.message);
                        if(res.status == 'success'){
                            refresh_page();
                        }
                    }
                });
            }
		}
	}
</script>