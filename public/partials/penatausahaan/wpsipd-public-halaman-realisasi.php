<?php
global $wpdb;

$input = shortcode_atts(array(
    'id_skpd' => '',
    'tahun_anggaran' => ''
), $atts);

$where_skpd = '';
if(!empty($input['id_skpd'])){
    $where_skpd = $wpdb->prepare(' AND r.id_sub_skpd=%d', $input['id_skpd']);
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
$body = '';
$total_pagu = 0;
$total_realisasi = 0;
$persen_all = '';
foreach($get_realisasi as $akun){
    $nomor++;
    $persen = 0;
    if($akun['nilai'] > 0 && $akun['realisasi'] > 0){
        $persen = round(($akun['realisasi']/$akun['nilai'])*100, 2);
    }
    $body .= '
        <tr>
            <td class="text-center">'.$nomor.'</td>
            <td class="text-left">'.$akun['kode_skpd'].' '.$akun['nama_skpd'].'</td>
            <td class="text-left">'.$akun['nama_program'].'</td>
            <td class="text-left">'.$akun['nama_giat'].'</td>
            <td class="text-left">'.$akun['nama_sub_giat'].'</td>
            <td class="text-left">'.$akun['nama_akun'].'</td>
            <td class="text-right">'.number_format($akun['nilai'],0,",",".").'</td>
            <td class="text-right">'.number_format($akun['realisasi'],0,",",".").'</td>
            <td class="text-center">'.$persen.'%</td>
        </tr>
    ';

    $total_pagu += $akun['nilai'];
    $total_realisasi += $akun['realisasi'];
}
if($total_pagu > 0 && $total_realisasi > 0){
    $persen_all = round(($total_realisasi/$total_pagu)*100, 2);
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
    <h1 class="text-center">Halaman Surat Keterangan Uang Persediaan<br>Tahun <?php  echo $input['tahun_anggaran'] ?></h1>
    <table class="table table-bordered" id="cetak">
        <thead>
            <tr>
                 <th class="text-center">No</th>
                 <th class="text-center">Nama SKPD</th>
                 <th class="text-center">Program</th>
                 <th class="text-center">Kegiatan</th>
                 <th class="text-center">Sub Kegiatan</th>
                 <th class="text-center">Akun</th>
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
                <th class="atas bawah kanan kiri text_kanan" colspan="6">Total</th>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_pagu,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_kanan"><?php echo number_format($total_realisasi,0,",","."); ?></td>
                <td class="atas bawah kanan kiri text_tengah"><?php echo $persen_all; ?>%</td>
            </tr>
        </tfoot>
    </table>      
</div>