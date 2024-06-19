<?php
global $wpdb;

$input = shortcode_atts(array(
    'id_skpd' => '',
    'tahun_anggaran' => ''
), $atts);

$where_skpd = '';
$nama_skpd = '';
if(!empty($input['id_skpd'])){
    $where_skpd = $wpdb->prepare(' AND r.id_sub_skpd=%d', $input['id_skpd']);
    $unit = $wpdb->get_row($wpdb->prepare("
        SELECT 
            nama_skpd, 
            id_skpd, 
            kode_skpd, 
            nipkepala 
        from data_unit 
        where active=1 
            and id_skpd=%d 
            and tahun_anggaran=%d
        order by kode_skpd ASC
    ", $input['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
    if(!empty($unit)){
        $nama_skpd = '<br>'.$unit['kode_skpd'].' '.$unit['nama_skpd'];
    }
}

$get_realisasi = $wpdb->get_results($wpdb->prepare('
    SELECT 
        r.*,
        u.kode_skpd,
        u.nama_skpd,
        p.nama_urusan,
        p.nama_program,
        p.nama_giat,
        p.nama_sub_giat
    from data_realisasi_akun_sipd r
    left join data_unit u on r.id_sub_skpd=u.id_skpd
        AND u.active=r.active
        AND u.tahun_anggaran=r.tahun_anggaran
    left join data_prog_keg p on p.id_sub_giat=r.id_sub_giat
        AND p.active=r.active
        AND p.tahun_anggaran=r.tahun_anggaran
    WHERE r.tahun_anggaran=%d
        and r.active=1
        '.$where_skpd.'
', $input['tahun_anggaran']), ARRAY_A);
// die($wpdb->last_query);

$nomor = 0;
$nomor_pendapatan = 0;
$nomor_pembiayaan = 0;
$body = '';
$body_pendapatan = '';
$body_pembiayaan = '';
$total_pagu = 0;
$total_realisasi = 0;
$persen_all = '';
$total_pagu_pendapatan = 0;
$total_realisasi_pendapatan = 0;
$persen_all_pendapatan = '';
$total_pagu_pembiayaan = 0;
$total_realisasi_pembiayaan = 0;
$persen_all_pembiayaan = '';
foreach($get_realisasi as $akun){
    $persen = 0;
    if($akun['nilai'] > 0 && $akun['realisasi'] > 0){
        $persen = round(($akun['realisasi']/$akun['nilai'])*100, 2);
    }
    if($akun['type'] == 'pendapatan'){
        $nomor_pendapatan++;
        $body_pendapatan .= '
            <tr>
                <td class="text-center">'.$nomor_pendapatan.'</td>
                <td class="text-left">'.$akun['kode_skpd'].' '.$akun['nama_skpd'].'</td>
                <td class="text-left">'.$akun['kode_akun'].'</td>
                <td class="text-left">'.$akun['nama_akun'].'</td>
                <td class="text-right">'.number_format($akun['nilai'],0,",",".").'</td>
                <td class="text-right">'.number_format($akun['realisasi'],2,",",".").'</td>
                <td class="text-center">'.$persen.'%</td>
            </tr>
        ';

        $total_pagu_pendapatan += $akun['nilai'];
        $total_realisasi_pendapatan += $akun['realisasi'];
    }else if($akun['type'] == 'pembiayaan'){
        $nomor_pembiayaan++;
        $body_pembiayaan .= '
            <tr>
                <td class="text-center">'.$nomor_pembiayaan.'</td>
                <td class="text-left">'.$akun['kode_skpd'].' '.$akun['nama_skpd'].'</td>
                <td class="text-left">'.$akun['kode_akun'].'</td>
                <td class="text-left">'.$akun['nama_akun'].'</td>
                <td class="text-right">'.number_format($akun['nilai'],0,",",".").'</td>
                <td class="text-right">'.number_format($akun['realisasi'],2,",",".").'</td>
                <td class="text-center">'.$persen.'%</td>
            </tr>
        ';

        $total_pagu_pembiayaan += $akun['nilai'];
        $total_realisasi_pembiayaan += $akun['realisasi'];
    }else{
        $nomor++;
        $body .= '
            <tr>
                <td class="text-center">'.$nomor.'</td>
                <td class="text-left">'.$akun['kode_skpd'].' '.$akun['nama_skpd'].'</td>
                <td class="text-left">'.$akun['nama_program'].'</td>
                <td class="text-left">'.$akun['nama_giat'].'</td>
                <td class="text-left">'.$akun['nama_sub_giat'].'</td>
                <td class="text-left">'.$akun['kode_akun'].'</td>
                <td class="text-left">'.$akun['nama_akun'].'</td>
                <td class="text-right">'.number_format($akun['nilai'],0,",",".").'</td>
                <td class="text-right">'.number_format($akun['realisasi'],2,",",".").'</td>
                <td class="text-center">'.$persen.'%</td>
            </tr>
        ';

        $total_pagu += $akun['nilai'];
        $total_realisasi += $akun['realisasi'];
    }
}
if($total_pagu > 0 && $total_realisasi > 0){
    $persen_all = round(($total_realisasi/$total_pagu)*100, 2);
}
if($total_pagu_pendapatan > 0 && $total_realisasi_pendapatan > 0){
    $persen_all_pendapatan = round(($total_realisasi_pendapatan/$total_pagu_pendapatan)*100, 2);
}
if($total_pagu_pembiayaan > 0 && $total_realisasi_pembiayaan > 0){
    $persen_all_pembiayaan = round(($total_realisasi_pembiayaan/$total_pagu_pembiayaan)*100, 2);
}
?>

<style type="text/css">
    .wrap-table {
        overflow: auto;
        max-height: 100vh;
        width: 100%;
    }
</style>

<div class="cetak container-fluid">
    <h1 class="text-center">Halaman Realisasi Per Akun Per Perangkat Daerah<?php echo $nama_skpd; ?><br>Tahun <?php  echo $input['tahun_anggaran'] ?></h1>
    <h2 class="text-center">Pendapatan</h2>
    <table class="table table-bordered" id="cetak1">
        <thead>
            <tr>
                 <th class="text-center">No</th>
                 <th class="text-center">Nama SKPD</th>
                 <th class="text-center">Kode Akun</th>
                 <th class="text-center">Nama Akun</th>
                 <th class="text-center">Pagu</th>
                 <th class="text-center">Realisasi</th>
                 <th class="text-center">Persen</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_pendapatan; ?>
        </tbody>
        <tfoot>
            <tr>
                <th class="atas bawah kanan kiri text_kanan" colspan="4">Total</th>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_pagu_pendapatan,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_realisasi_pendapatan,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_tengah"><?php echo $persen_all_pendapatan; ?>%</td>
            </tr>
        </tfoot>
    </table>
    <h2 class="text-center">Belanja</h2>
    <table class="table table-bordered" id="cetak">
        <thead>
            <tr>
                 <th class="text-center">No</th>
                 <th class="text-center">Nama SKPD</th>
                 <th class="text-center">Program</th>
                 <th class="text-center">Kegiatan</th>
                 <th class="text-center">Sub Kegiatan</th>
                 <th class="text-center">Kode Akun</th>
                 <th class="text-center">Nama Akun</th>
                 <th class="text-center">Pagu</th>
                 <th class="text-center">Realisasi</th>
                 <th class="text-center">Persen</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $body; ?>
        </tbody>
        <tfoot>
            <tr>
                <th class="atas bawah kanan kiri text_kanan" colspan="7">Total</th>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_pagu,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_realisasi,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_tengah"><?php echo $persen_all; ?>%</td>
            </tr>
        </tfoot>
    </table>
    <h2 class="text-center">Pembiayaan</h2>
    <table class="table table-bordered" id="cetak2">
        <thead>
            <tr>
                 <th class="text-center">No</th>
                 <th class="text-center">Nama SKPD</th>
                 <th class="text-center">Kode Akun</th>
                 <th class="text-center">Nama Akun</th>
                 <th class="text-center">Pagu</th>
                 <th class="text-center">Realisasi</th>
                 <th class="text-center">Persen</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_pembiayaan; ?>
        </tbody>
        <tfoot>
            <tr>
                <th class="atas bawah kanan kiri text_kanan" colspan="4">Total</th>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_pagu_pembiayaan,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_realisasi_pembiayaan,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_tengah"><?php echo $persen_all_pembiayaan; ?>%</td>
            </tr>
        </tfoot>
    </table>
</div>