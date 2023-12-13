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
        t.nama_tipe,
        j.lama_pelaksanaan 
    FROM `data_jadwal_lokal` j
    INNER JOIN `data_tipe_perencanaan` t on t.id=j.id_tipe
    WHERE j.id_jadwal_lokal=%d", $id_jadwal_lokal));

$input['tahun_anggaran'] = get_option('_crb_tahun_anggaran_sipd');

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
    $where_skpd = '';
    $nama_skpd = '';
}else{
    $where_skpd = $wpdb->prepare(' AND id_unit = %d ', $input['id_skpd']);
    $nama_skpd_tunggal = $wpdb->get_row($wpdb->prepare("
    SELECT 
        nama_skpd
    FROM `data_unit` 
    WHERE id_skpd=%d
        AND tahun_anggaran=%d", $input['id_skpd'], $input['tahun_anggaran']));
    $nama_skpd = '<br>'.$nama_skpd_tunggal->nama_skpd;
}

$nama_pemda = get_option('_crb_daerah');
$nama_excel = 'TOTAL PROGRAM, KEGIATAN, SUB KEGIATAN RENSTRA '.$jadwal_lokal->tahun_anggaran.'-'.($jadwal_lokal->tahun_anggaran+$jadwal_lokal->lama_pelaksanaan).' '.strtoupper($nama_pemda);
$nama_laporan = 'TOTAL PROGRAM, KEGIATAN, SUB KEGIATAN<br>RENSTRA '.$nama_skpd.'<br>TAHUN '.$jadwal_lokal->tahun_anggaran.'-'.($jadwal_lokal->tahun_anggaran+$jadwal_lokal->lama_pelaksanaan).'<br>'.strtoupper($nama_pemda);
echo '<div id="cetak" title="'.$nama_excel.'" style="padding: 5px;">';

$data_all_renstra = array();
$sql = "
    SELECT 
        * 
    FROM data_renstra_tujuan".$_suffix_sipd."".$_suffix."
    WHERE id_unik IS NOT NULL
        $where_skpd AND
        id_unik_indikator IS NULL AND
        active=1 
    ORDER BY urut_tujuan";
$tujuan = $wpdb->get_results($sql, ARRAY_A);
foreach($tujuan as $k => $tuj){
    $sasaran = $wpdb->get_results($wpdb->prepare("
        SELECT 
            id_unik
        from data_renstra_sasaran".$_suffix_sipd."".$_suffix." 
        where id_unik_indikator IS NULL
            AND active=1
            AND kode_tujuan=%s
    ", $tuj['id_unik']), ARRAY_A);

    foreach($sasaran as $sas){
        
        $program = $wpdb->get_results($wpdb->prepare("
            SELECT 
                id_unik,
                nama_program,
                kode_program
            from data_renstra_program".$_suffix_sipd."".$_suffix." 
            where id_unik_indikator IS NULL
                AND active=1
                AND kode_sasaran=%s
        ", $sas['id_unik']), ARRAY_A);
        foreach($program as $prog){
            if(empty($data_all_renstra[$prog['nama_program']])){
                $data_all_renstra[$prog['nama_program']] = array(
                    'kode' => $prog['kode_program'],
                    'nama' => $prog['nama_program'],
                    'total_pagu_1' => 0,
                    'total_pagu_2' => 0,
                    'total_pagu_3' => 0,
                    'total_pagu_4' => 0,
                    'total_pagu_5' => 0,
                    'total_pagu_1_usulan' => 0,
                    'total_pagu_2_usulan' => 0,
                    'total_pagu_3_usulan' => 0,
                    'total_pagu_4_usulan' => 0,
                    'total_pagu_5_usulan' => 0,
                    'jumlah' => 0,
                    'data' => array()
                );
            }
            $data_all_renstra[$prog['nama_program']]['jumlah']++;

            $kegiatan = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    id_unik,
                    nama_giat,
                    kode_giat
                from data_renstra_kegiatan".$_suffix_sipd."".$_suffix." 
                where id_unik_indikator IS NULL
                    AND active=1
                    AND kode_program=%s
            ", $prog['id_unik']), ARRAY_A);
            foreach ($kegiatan as $keg) {
                if(empty($data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']])){
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']] = array(
                        'kode' => $keg['kode_giat'],
                        'nama' => $keg['nama_giat'],
                        'total_pagu_1' => 0,
                        'total_pagu_2' => 0,
                        'total_pagu_3' => 0,
                        'total_pagu_4' => 0,
                        'total_pagu_5' => 0,
                        'total_pagu_1_usulan' => 0,
                        'total_pagu_2_usulan' => 0,
                        'total_pagu_3_usulan' => 0,
                        'total_pagu_4_usulan' => 0,
                        'total_pagu_5_usulan' => 0,
                        'jumlah' => 0,
                        'data' => array()
                    );
                }
                $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['jumlah']++;

                $sub_keg = $wpdb->get_results($wpdb->prepare("
                    SELECT 
                        pagu_1,
                        pagu_2,
                        pagu_3,
                        pagu_4,
                        pagu_5,
                        pagu_1_usulan,
                        pagu_2_usulan,
                        pagu_3_usulan,
                        pagu_4_usulan,
                        pagu_5_usulan,
                        pagu_5_usulan,
                        nama_sub_giat,
                        kode_sub_giat
                    from data_renstra_sub_kegiatan".$_suffix_sipd."".$_suffix." 
                    where id_unik_indikator IS NULL
                        AND active=1
                        AND kode_kegiatan=%s
                ", $keg['id_unik']), ARRAY_A);
                foreach ($sub_keg as $sub) {
                    if(empty($data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']])){
                        $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']] = array(
                            'kode' => $sub['kode_sub_giat'],
                            'nama' => $sub['nama_sub_giat'],
                            'total_pagu_1' => 0,
                            'total_pagu_2' => 0,
                            'total_pagu_3' => 0,
                            'total_pagu_4' => 0,
                            'total_pagu_5' => 0,
                            'total_pagu_1_usulan' => 0,
                            'total_pagu_2_usulan' => 0,
                            'total_pagu_3_usulan' => 0,
                            'total_pagu_4_usulan' => 0,
                            'total_pagu_5_usulan' => 0,
                            'jumlah' => 0,
                            'data' => array()
                        );
                    }

                    $data_all_renstra[$prog['nama_program']]['total_pagu_1'] += $sub['pagu_1'];
                    $data_all_renstra[$prog['nama_program']]['total_pagu_2'] += $sub['pagu_2'];
                    $data_all_renstra[$prog['nama_program']]['total_pagu_3'] += $sub['pagu_3'];
                    $data_all_renstra[$prog['nama_program']]['total_pagu_4'] += $sub['pagu_4'];
                    $data_all_renstra[$prog['nama_program']]['total_pagu_5'] += $sub['pagu_5'];
                    $data_all_renstra[$prog['nama_program']]['total_pagu_1_usulan'] += $sub['pagu_1_usulan'];
                    $data_all_renstra[$prog['nama_program']]['total_pagu_2_usulan'] += $sub['pagu_2_usulan'];
                    $data_all_renstra[$prog['nama_program']]['total_pagu_3_usulan'] += $sub['pagu_3_usulan'];
                    $data_all_renstra[$prog['nama_program']]['total_pagu_4_usulan'] += $sub['pagu_4_usulan'];
                    $data_all_renstra[$prog['nama_program']]['total_pagu_5_usulan'] += $sub['pagu_5_usulan'];

                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['total_pagu_1'] += $sub['pagu_1'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['total_pagu_2'] += $sub['pagu_2'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['total_pagu_3'] += $sub['pagu_3'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['total_pagu_4'] += $sub['pagu_4'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['total_pagu_5'] += $sub['pagu_5'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['total_pagu_1_usulan'] += $sub['pagu_1_usulan'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['total_pagu_2_usulan'] += $sub['pagu_2_usulan'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['total_pagu_3_usulan'] += $sub['pagu_3_usulan'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['total_pagu_4_usulan'] += $sub['pagu_4_usulan'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['total_pagu_5_usulan'] += $sub['pagu_5_usulan'];

                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['jumlah']++;
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['data'][] = $sub;
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['total_pagu_1'] += $sub['pagu_1'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['total_pagu_2'] += $sub['pagu_2'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['total_pagu_3'] += $sub['pagu_3'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['total_pagu_4'] += $sub['pagu_4'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['total_pagu_5'] += $sub['pagu_5'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['total_pagu_1_usulan'] += $sub['pagu_1_usulan'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['total_pagu_2_usulan'] += $sub['pagu_2_usulan'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['total_pagu_3_usulan'] += $sub['pagu_3_usulan'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['total_pagu_4_usulan'] += $sub['pagu_4_usulan'];
                    $data_all_renstra[$prog['nama_program']]['data'][$keg['nama_giat']]['data'][$sub['nama_sub_giat']]['total_pagu_5_usulan'] += $sub['pagu_5_usulan'];
                }
            }
        }
    }
}

ksort($data_all_renstra);

$body_program = '';
$urut_program = 1;
$body_kegiatan = '';
$urut_kegiatan = 1;
$body_sub_kegiatan = '';
$urut_sub_kegiatan = 1;
foreach ($data_all_renstra as $program) {
    $jumlah_program   = '<a style="text-decoration: none;" onclick="show_analisis_program(\''.$program['nama'].'\'); return false;" href="#" title="Menampilkan SKPD pengguna Program">'.number_format($program['jumlah'],0,",",".").'</a>';
    $body_program .='
    <tr>
        <td class="kiri kanan bawah text_tengah">'.$urut_program.'</td>
        <td class="kiri kanan bawah">'.$program['nama'].'</td>
        <td class="kiri kanan bawah text_kanan">'.$jumlah_program.'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($program['total_pagu_1'],0,",",".").'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($program['total_pagu_2'],0,",",".").'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($program['total_pagu_3'],0,",",".").'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($program['total_pagu_4'],0,",",".").'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($program['total_pagu_5'],0,",",".").'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($program['total_pagu_1_usulan'],0,",",".").'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($program['total_pagu_2_usulan'],0,",",".").'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($program['total_pagu_3_usulan'],0,",",".").'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($program['total_pagu_4_usulan'],0,",",".").'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($program['total_pagu_5_usulan'],0,",",".").'</td>
    </tr>';
    $urut_program++;

    ksort($program['data']);
    foreach ($program['data'] as $kegiatan) {
        $jumlah_kegiatan   = '<a style="text-decoration: none;" onclick="show_analisis_keg(\''.$kegiatan['nama'].'\'); return false;" href="#" title="Menampilkan SKPD pengguna Kegiatan">'.number_format($kegiatan['jumlah'],0,",",".").'</a>';
        $body_kegiatan .='
        <tr>
            <td class="kiri kanan bawah text_tengah">'.$urut_kegiatan.'</td>
            <td class="kiri kanan bawah">'.$kegiatan['nama'].'</td>
            <td class="kiri kanan bawah text_kanan">'.$jumlah_kegiatan.'</td>
            <td class="kiri kanan bawah text_kanan">'.number_format($kegiatan['total_pagu_1'],0,",",".").'</td>
            <td class="kiri kanan bawah text_kanan">'.number_format($kegiatan['total_pagu_2'],0,",",".").'</td>
            <td class="kiri kanan bawah text_kanan">'.number_format($kegiatan['total_pagu_3'],0,",",".").'</td>
            <td class="kiri kanan bawah text_kanan">'.number_format($kegiatan['total_pagu_4'],0,",",".").'</td>
            <td class="kiri kanan bawah text_kanan">'.number_format($kegiatan['total_pagu_5'],0,",",".").'</td>
            <td class="kiri kanan bawah text_kanan">'.number_format($kegiatan['total_pagu_1_usulan'],0,",",".").'</td>
            <td class="kiri kanan bawah text_kanan">'.number_format($kegiatan['total_pagu_2_usulan'],0,",",".").'</td>
            <td class="kiri kanan bawah text_kanan">'.number_format($kegiatan['total_pagu_3_usulan'],0,",",".").'</td>
            <td class="kiri kanan bawah text_kanan">'.number_format($kegiatan['total_pagu_4_usulan'],0,",",".").'</td>
            <td class="kiri kanan bawah text_kanan">'.number_format($kegiatan['total_pagu_5_usulan'],0,",",".").'</td>
        </tr>';
        $urut_kegiatan++;

        ksort($kegiatan['data']);
        foreach ($kegiatan['data'] as $sub_kegiatan) {
            $jumlah_sub_kegiatan   = '<a style="text-decoration: none;" onclick="show_analisis_sub_keg(\''.$sub_kegiatan['kode'].'\'); return false;" href="#" title="Menampilkan SKPD pengguna Sub Kegiatan">'.number_format($sub_kegiatan['jumlah'],0,",",".").'</a>';
            $body_sub_kegiatan .='
            <tr>
                <td class="kiri kanan bawah text_tengah">'.$urut_sub_kegiatan.'</td>
                <td class="kiri kanan bawah">'.$sub_kegiatan['nama'].'</td>
                <td class="kiri kanan bawah text_kanan">'.$jumlah_sub_kegiatan.'</td>
                <td class="kiri kanan bawah text_kanan">'.number_format($sub_kegiatan['total_pagu_1'],0,",",".").'</td>
                <td class="kiri kanan bawah text_kanan">'.number_format($sub_kegiatan['total_pagu_2'],0,",",".").'</td>
                <td class="kiri kanan bawah text_kanan">'.number_format($sub_kegiatan['total_pagu_3'],0,",",".").'</td>
                <td class="kiri kanan bawah text_kanan">'.number_format($sub_kegiatan['total_pagu_4'],0,",",".").'</td>
                <td class="kiri kanan bawah text_kanan">'.number_format($sub_kegiatan['total_pagu_5'],0,",",".").'</td>
                <td class="kiri kanan bawah text_kanan">'.number_format($sub_kegiatan['total_pagu_1_usulan'],0,",",".").'</td>
                <td class="kiri kanan bawah text_kanan">'.number_format($sub_kegiatan['total_pagu_2_usulan'],0,",",".").'</td>
                <td class="kiri kanan bawah text_kanan">'.number_format($sub_kegiatan['total_pagu_3_usulan'],0,",",".").'</td>
                <td class="kiri kanan bawah text_kanan">'.number_format($sub_kegiatan['total_pagu_4_usulan'],0,",",".").'</td>
                <td class="kiri kanan bawah text_kanan">'.number_format($sub_kegiatan['total_pagu_5_usulan'],0,",",".").'</td>
            </tr>';
            $urut_sub_kegiatan++;
        }
    }
}


?>
<h1 class="text_tengah"><?php echo $nama_laporan; ?></h1>
<h2 class="text_tengah">Jumlah Program yang digunakan: <?php echo $urut_program-1; ?> Program</h2>
<div class="wrap-table">
    <table cellpadding="2" cellspacing="0" style="font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; border-collapse: collapse; width: 2100px; table-layout: fixed; overflow-wrap: break-word; font-size: 100%; border: 0;">
        <thead>
            <tr>    
                <th class="atas kiri kanan bawah text_tengah" style="width:40px;">No</th>
                <th class="atas kiri kanan bawah text_tengah">Nama Program</th>
                <th class="atas kiri kanan bawah text_tengah" style="width:70px;">Jumlah</th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+1; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+2; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+3; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+4; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+1; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+2; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+3; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+4; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_program; ?>
        </tbody>
    </table>
</div>
<h2 class="text_tengah">Jumlah Kegiatan yang digunakan: <?php echo $urut_kegiatan-1; ?> Kegiatan</h2>
<div class="wrap-table">
    <table cellpadding="2" cellspacing="0" style="font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; border-collapse: collapse; width: 2100px; table-layout: fixed; overflow-wrap: break-word; font-size: 100%; border: 0;">
        <thead>
            <tr>    
                <th class="atas kiri kanan bawah text_tengah" style="width:40px;">No</th>
                <th class="atas kiri kanan bawah text_tengah">Nama Kegiatan</th>
                <th class="atas kiri kanan bawah text_tengah" style="width:70px;">Jumlah</th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+1; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+2; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+3; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+4; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+1; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+2; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+3; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+4; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_kegiatan; ?>
        </tbody>
    </table>
</div>
<h2 class="text_tengah">Jumlah Sub Kegiatan yang digunakan: <?php echo $urut_sub_kegiatan-1; ?> Sub Kegiatan</h2>
<div class="wrap-table">
    <table cellpadding="2" cellspacing="0" style="font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; border-collapse: collapse; width: 2100px; table-layout: fixed; overflow-wrap: break-word; font-size: 100%; border: 0;">
        <thead>
            <tr>    
                <th class="atas kiri kanan bawah text_tengah" style="width:40px;">No</th>
                <th class="atas kiri kanan bawah text_tengah">Nama Sub Kegiatan</th>
                <th class="atas kiri kanan bawah text_tengah" style="width:70px;">Jumlah</th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+1; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+2; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+3; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Tahun <?php echo $jadwal_lokal->tahun_anggaran+4; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+1; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+2; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+3; ?></th>
                <th class="atas kiri kanan bawah text_tengah" style="width:140px;">Pagu Usulan Tahun <?php echo $jadwal_lokal->tahun_anggaran+4; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_sub_kegiatan; ?>
        </tbody>
    </table>
</div>

<div class="modal fade mt-4" id="modalAnalisis" tabindex="-1" role="dialog" aria-labelledby="modalmodalAnalisisLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document" style="min-width:1140px">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalmodalAnalisisLabel">Laporan Skpd Program</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div> 
			<div class="modal-footer">
				<button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
    .wrap-table {
        overflow: auto;
        max-height: 100vh;
        margin-bottom: 25px;
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

    /** modal menampilkan analisis program */
	function show_analisis_program(kode_program){
		alert('dalam pengembangan!');
    }
</script>