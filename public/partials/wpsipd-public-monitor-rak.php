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
	FROM 
        data_unit 
	WHERE 
        tahun_anggaran=%d
		AND id_skpd =".$input['id_skpd']."
		AND active=1
	order by id_skpd ASC
    ", $input['tahun_anggaran']);
$unit = $wpdb->get_results($sql_unit, ARRAY_A);
$unit = (!empty($unit)) ? $unit : array();
$nama_skpd = (!empty($unit[0]['nama_skpd'])) ? $unit[0]['nama_skpd'] : '-';

$sql_anggaran = $wpdb->prepare("
    SELECT
        *
    FROM
        data_anggaran_kas
    WHERE
        tahun_anggaran=%d
        AND active=1
    ",$input["tahun_anggaran"]);
$data_anggaran = $wpdb->get_results($sql_anggaran, ARRAY_A);

$no = 0;
if(!empty($data_anggaran)){
    foreach ($data_anggaran as $v_anggaran) {

        $body_rak .= '
            <tr>
                <td class="kiri atas kanan bawah text_blok">'.$no++.'</td>
                <td class="atas kanan bawah text_blok">Kode Urusan</td>
				<td class="atas kanan bawah text_blok">Nama Urusan</td>
				<td class="atas kanan bawah text_blok">kode bidang urusan</td>
				<td class="atas kanan bawah text_blok">nama bidang urusan</td>
				<td class="atas kanan bawah text_blok">'.$unit[0]['kode_skpd'].'</td>
                <td class="atas kanan bawah text_blok">'.$unit[0]['nama_skpd'].'</td>
                <td class="atas kanan bawah text_blok">Kode Sub SKPD</td>
                <td class="atas kanan bawah text_blok">Kode Program</td>
                <td class="atas kanan bawah text_blok">Nama Program</td>
                <td class="atas kanan bawah text_blok">Kode Kegiatan</td>
                <td class="atas kanan bawah text_blok">Nama Kegiatan</td>
                <td class="atas kanan bawah text_blok">Kode Sub Kegiatan</td>
                <td class="atas kanan bawah text_blok">Nama Sub Kegiatan</td>
                <td class="atas kanan bawah text_blok">'.$v_anggaran['kode_akun'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['nama_akun'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">Nilai Rincian</td>
                <td class="atas kanan bawah text_blok">'.$v_anggaran['total_rincian'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_1'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_2'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_3'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_4'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_5'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_6'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_7'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_8'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_9'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_10'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_11'].'</td>
                <td class="atas kanan bawah text_tengah text_blok">'.$v_anggaran['bulan_12'].'</td>
            </tr>';
    }
}

?>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Rencana Anggaran Kas <br><?php echo $nama_skpd.'<br>Tahun '.$input['tahun_anggaran'].' '.$nama_pemda; ?></h4>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; max-height: 80vh;">
	<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<tr>
				<th style="width: 60px;" class='atas kiri kanan bawah text_tengah text_blok'>No</th>
				<th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Kode Urusan</th>
				<th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama Urusan</th>
				<th style="width: 100px;" class='atas kanan bawah text_tengah text_blok'>Kode Bidang Urusan</th>
				<th style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Nama Bidang Urusan</th>
				<th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Kode SKPD</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama SKPD</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Kode Sub SKPD</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Kode Program</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama Program</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Kode Kegiatan</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama Kegiatan</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Kode Sub Kegiatan</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama Sub Kegiatan</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Kode Akun</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nama Akun</th>
                <th style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Nilai Rincian</th>
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
	</table>
</div>