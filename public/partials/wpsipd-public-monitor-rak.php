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

$body_rak = '';
$nama_pemda = get_option('_crb_daerah');

$sql_unit = $wpdb->prepare("
	SELECT 
		*
	FROM data_unit 
	WHERE 
        tahun_anggaran=%d
		AND id_skpd =%d
		AND active=1
	order by id_skpd ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
$unit = $wpdb->get_results($sql_unit, ARRAY_A);

$unit_utama = $unit;
if($unit[0]['id_unit'] != $unit[0]['id_skpd']){
    $sql_unit_utama = $wpdb->prepare("
        SELECT 
            *
        FROM data_unit
        WHERE 
            tahun_anggaran=%d
            AND id_skpd=%d
            AND active=1
        order by id_skpd ASC
        ", $input['tahun_anggaran'], $unit[0]['id_unit']);
    $unit_utama = $wpdb->get_results($sql_unit_utama, ARRAY_A);
}

$unit = (!empty($unit)) ? $unit : array();
$nama_skpd = (!empty($unit[0]['nama_skpd'])) ? $unit[0]['nama_skpd'] : '-';

$sql_anggaran = $wpdb->prepare("
    SELECT
        *
    FROM data_anggaran_kas
    WHERE
        tahun_anggaran=%d
        AND type='belanja'
        AND active=1
        AND id_sub_skpd=%d
    ",$input["tahun_anggaran"], $input['id_skpd']);
$data_anggaran = $wpdb->get_results($sql_anggaran, ARRAY_A);

$total_rincian = 0;
$total_realisasi = 0;
$total_kas = 0;
$total_bulan_1 = 0;
$total_bulan_2 = 0;
$total_bulan_3 = 0;
$total_bulan_4 = 0;
$total_bulan_5 = 0;
$total_bulan_6 = 0;
$total_bulan_7 = 0;
$total_bulan_8 = 0;
$total_bulan_9 = 0;
$total_bulan_10 = 0;
$total_bulan_11 = 0;
$total_bulan_12 = 0;
$no = 1;
if(!empty($data_anggaran)){
    foreach ($data_anggaran as $v_anggaran) {
        $kode_sbl = explode('.', $v_anggaran['kode_sbl']);
        if($input["tahun_anggaran"] >= 2024){
            unset($kode_sbl[2]);
            unset($kode_sbl[3]);
            $kode_sbl = implode('.', $kode_sbl);
        }else if(!empty($kode_sbl[6])){
            $kode_sbl = $kode_sbl[1].'.'.$kode_sbl[2].'.'.$kode_sbl[4].'.'.$kode_sbl[5].'.'.$kode_sbl[6];
        }else if(!empty($kode_sbl[5])){
            $kode_sbl = $kode_sbl[0].'.'.$kode_sbl[1].'.'.$kode_sbl[3].'.'.$kode_sbl[4].'.'.$kode_sbl[5];
        }else{
            $kode_sbl = $v_anggaran['kode_sbl'];
        }
        $sql_keg = $wpdb->prepare("
            SELECT
                k.*,
                sum(r.rincian) as total_akun,
                a.nilai as realisasi
            FROM data_sub_keg_bl as k
            INNER JOIN data_rka as r on k.kode_sbl=r.kode_sbl
                and r.active=k.active
                and r.tahun_anggaran=k.tahun_anggaran
            LEFT JOIN data_realisasi_akun_sipd as a on a.kode_sbl=k.kode_sbl
                and a.active=k.active
                and a.tahun_anggaran=k.tahun_anggaran
            WHERE
                k.tahun_anggaran=%d
                AND k.active=1
                AND k.kode_sbl=%s
                AND r.kode_akun=%s
            ",$input["tahun_anggaran"], $kode_sbl, $v_anggaran['kode_akun']);
        // die($sql_keg);
        $data_sub_keg = $wpdb->get_results($sql_keg, ARRAY_A);
        $warning = '';
        if($data_sub_keg[0]['total_akun'] != $v_anggaran['total_rincian']){
            $warning = 'background: #ffeb005e;';
        }
        $nama_akun = explode(' ', $v_anggaran['nama_akun']);
        unset($nama_akun[0]);
        $nama_akun = implode(' ', $nama_akun);
        $body_rak .= '
            <tr style="'.$warning.'" kode_sbl="'.$kode_sbl.'" kode_sbl_kas="'.$v_anggaran['kode_sbl'].'">
                <td class="kiri atas kanan bawah text_blok text_tengah">'.$no++.'</td>
                <td class="atas kanan bawah text_tengah">'.$data_sub_keg[0]['kode_urusan'].'</td>
				<td class="atas kanan bawah">'.$data_sub_keg[0]['nama_urusan'].'</td>
				<td class="atas kanan bawah text_tengah">'.$data_sub_keg[0]['kode_bidang_urusan'].'</td>
				<td class="atas kanan bawah">'.$data_sub_keg[0]['nama_bidang_urusan'].'</td>
				<td class="atas kanan bawah text_tengah">'.$unit_utama[0]['kode_skpd'].'</td>
                <td class="atas kanan bawah">'.$unit_utama[0]['nama_skpd'].'</td>
                <td class="atas kanan bawah text_tengah">'.$unit[0]['kode_skpd'].'</td>
                <td class="atas kanan bawah">'.$unit[0]['nama_skpd'].'</td>
                <td class="atas kanan bawah text_tengah">'.$data_sub_keg[0]['kode_program'].'</td>
                <td class="atas kanan bawah">'.$data_sub_keg[0]['nama_program'].'</td>
                <td class="atas kanan bawah text_tengah">'.$data_sub_keg[0]['kode_giat'].'</td>
                <td class="atas kanan bawah">'.$data_sub_keg[0]['nama_giat'].'</td>
                <td class="atas kanan bawah text_tengah">'.$data_sub_keg[0]['kode_sub_giat'].'</td>
                <td class="atas kanan bawah">'.$data_sub_keg[0]['nama_sub_giat'].'</td>
                <td class="atas kanan bawah text_tengah">'.$v_anggaran['kode_akun'].'</td>
                <td class="atas kanan bawah">'.$nama_akun.'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($data_sub_keg[0]['total_akun'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($data_sub_keg[0]['realisasi'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['total_rincian'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_1'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_2'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_3'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_4'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_5'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_6'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_7'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_8'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_9'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_10'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_11'], 0, '.', ',').'</td>
                <td class="atas kanan bawah text_kanan">'.number_format($v_anggaran['bulan_12'], 0, '.', ',').'</td>
            </tr>';

        $total_rincian += $data_sub_keg[0]['total_akun'];
        $total_realisasi += $data_sub_keg[0]['realisasi'];
        $total_kas += $v_anggaran['total_rincian'];
        $total_bulan_1 += $v_anggaran['bulan_1'];
        $total_bulan_2 += $v_anggaran['bulan_2'];
        $total_bulan_3 += $v_anggaran['bulan_3'];
        $total_bulan_4 += $v_anggaran['bulan_4'];
        $total_bulan_5 += $v_anggaran['bulan_5'];
        $total_bulan_6 += $v_anggaran['bulan_6'];
        $total_bulan_7 += $v_anggaran['bulan_7'];
        $total_bulan_8 += $v_anggaran['bulan_8'];
        $total_bulan_9 += $v_anggaran['bulan_9'];
        $total_bulan_10 += $v_anggaran['bulan_10'];
        $total_bulan_11 += $v_anggaran['bulan_11'];
        $total_bulan_12 += $v_anggaran['bulan_12'];
    }
}

?>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Rencana Anggaran Kas Data SIPD <br><?php echo $nama_skpd.'<br>Tahun '.$input['tahun_anggaran'].' '.$nama_pemda; ?></h4>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; max-height: 80vh;">
	<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<tr>
				<th style="width: 35px;" class='atas kiri kanan bawah text_tengah text_blok'>No</th>
				<th style="width: 55px;" class='atas kanan bawah text_tengah text_blok'>Kode Urusan</th>
				<th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama Urusan</th>
				<th style="width: 55px;" class='atas kanan bawah text_tengah text_blok'>Kode Bidang Urusan</th>
				<th style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Nama Bidang Urusan</th>
				<th style="width: 135px;" class='atas kanan bawah text_tengah text_blok'>Kode SKPD</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama SKPD</th>
                <th style="width: 150px;" class='atas kanan bawah text_tengah text_blok'>Kode Sub SKPD</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>nama Sub SKPD</th>
                <th style="width: 70px;" class='atas kanan bawah text_tengah text_blok'>Kode Program</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama Program</th>
                <th style="width: 80px;" class='atas kanan bawah text_tengah text_blok'>Kode Kegiatan</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama Kegiatan</th>
                <th style="width: 100px;" class='atas kanan bawah text_tengah text_blok'>Kode Sub Kegiatan</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama Sub Kegiatan</th>
                <th style="width: 110px;" class='atas kanan bawah text_tengah text_blok'>Kode Akun</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama Akun</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nilai Rincian</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nilai Realisasi</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Total RAK</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 1</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 2</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 3</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 4</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 5</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 6</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 7</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 8</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 9</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 10</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 11</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Bulan 12</th>
			</tr>
		</thead>
		<tbody>
            <?php echo $body_rak; ?>
		</tbody>
        <tfoot>
            <tr>
                <th colspan="17" class='atas kanan bawah text_tengah text_blok'>Total</th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_rincian, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_realisasi, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_kas, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_1, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_2, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_3, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_4, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_5, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_6, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_7, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_8, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_9, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_10, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_11, 0, '.', ','); ?></th>
                <th class='atas kanan bawah text_kanan text_blok'><?php echo number_format($total_bulan_12, 0, '.', ','); ?></th>
            </tr>
        </tfoot>
	</table>
</div>