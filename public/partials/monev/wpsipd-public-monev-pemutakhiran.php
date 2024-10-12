<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

$input = shortcode_atts(array(
    'tahun_anggaran' => ''
), $atts);

global $wpdb;

$cek_sumber_dana = $wpdb->get_results($wpdb->prepare('
    SELECT 
    	b.id_dana as iddana_baru,
    	b.kode_dana as kodedana_baru,
    	b.nama_dana as namadana_baru,
    	d.*,
    	s.*
    FROM data_dana_sub_keg d
    INNER JOIN data_sub_keg_bl s ON d.kode_sbl=s.kode_sbl
    	AND s.active=1
    	AND s.tahun_anggaran=d.tahun_anggaran
    LEFT JOIN data_sumber_dana b ON b.kode_dana=d.kodedana
    	AND b.active=1
    	AND b.is_locked!=1
    	AND b.tahun_anggaran=d.tahun_anggaran
    WHERE d.is_locked=1
    	AND d.tahun_anggaran=%d
    	AND d.active=1
    ORDER BY d.kodedana ASC
', $input['tahun_anggaran']), ARRAY_A);

$body_sub = '';
$data_all = array();
$no = 0;
foreach($cek_sumber_dana as $sd){
	$no++;
	$body_sub .= '
		<tr>
			<td class="text-center">'.$no.'</td>
			<td class="text-center">'.$sd['iddana'].'</td>
			<td class="text-left">'.$sd['kodedana'].'</td>
			<td class="text-left">'.$sd['namadana'].'</td>
			<td class="text-left">'.$sd['nama_sub_giat'].'</td>
			<td class="text-left">'.$sd['nama_sub_skpd'].'</td>
			<td class="text-right">'.number_format($sd['pagudana'], 0, ",", ".").'</td>
			<td class="text-right">'.number_format($sd['pagu'], 0, ",", ".").'</td>
		</tr>
	';
	if(empty($data_all[$sd['kodedana']])){
		$data_all[$sd['kodedana']] = array(
			'data' => array(),
			'iddana' => $sd['iddana'],
			'kodedana' => $sd['kodedana'],
			'namadana' => $sd['namadana'],
			'iddana_baru' => $sd['iddana_baru'],
			'kodedana_baru' => $sd['kodedana_baru'],
			'namadana_baru' => $sd['namadana_baru'],
			'pagu_dana' => 0
		);
	}
	$data_all[$sd['kodedana']]['data'][] = $sd;
	$data_all[$sd['kodedana']]['pagu_dana'] += $sd['pagudana'];
}

$body_rekap = '';
$no = 0;
foreach($data_all as $sd){
	$no++;
	$body_rekap .= '
		<tr>
			<td class="text-center">'.$no.'</td>
			<td class="text-center">'.$sd['iddana'].'</td>
			<td class="text-left">'.$sd['kodedana'].'</td>
			<td class="text-left">'.$sd['namadana'].'</td>
			<td class="text-right">'.number_format($sd['pagu_dana'], 0, ",", ".").'</td>
			<td class="text-center">'.$sd['iddana_baru'].'</td>
			<td class="text-left">'.$sd['kodedana_baru'].'</td>
			<td class="text-left">'.$sd['namadana_baru'].'</td>
		</tr>
	';
}
?>
<div class="cetak">
    <div style="padding: 10px;margin:0 0 3rem 0;">
		<h2 class="text-center">Daftar Pemutakhiran Sumber Dana di Sub Kegiantan Tahun <?php echo $input['tahun_anggaran']; ?></h2><table id="data_sumber_dana_table" cellpadding="2" cellspacing="0">
            <thead id="data_header">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">ID Dana</th>
                    <th class="text-center">Kode</th>
                    <th class="text-center">Sumber Dana</th>
                    <th class="text-center">Pagu Sumber Dana</th>
                    <th class="text-center">ID Dana Baru</th>
                    <th class="text-center">Kode</th>
                    <th class="text-center">Sumber Dana</th>
                </tr>
            </thead>
            <tbody>
            	<?php echo $body_rekap; ?>
            </tbody>
        </table>
		<h2 class="text-center">Detail Pemutakhiran Sumber Dana di Sub Kegiantan Tahun <?php echo $input['tahun_anggaran']; ?></h2>
		<table id="data_sumber_dana_table" cellpadding="2" cellspacing="0">
            <thead id="data_header">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">ID Dana</th>
                    <th class="text-center">Kode</th>
                    <th class="text-center">Sumber Dana</th>
                    <th class="text-center">Sub Kegiatan</th>
                    <th class="text-center">Perangkat Daerah</th>
                    <th class="text-center">Pagu Sumber Dana</th>
                    <th class="text-center">Pagu Sub Kegiatan</th>
                </tr>
            </thead>
            <tbody>
            	<?php echo $body_sub; ?>
            </tbody>
        </table>
		<h2 class="text-center">Daftar Pemutakhiran Sumber Dana di Rincian Belanja Sub Kegiantan Tahun <?php echo $input['tahun_anggaran']; ?></h2>
		<table id="data_sumber_dana_table" cellpadding="2" cellspacing="0">
            <thead id="data_header">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Kode Sumber Dana</th>
                    <th class="text-center">Sumber Dana</th>
                    <th class="text-center">Sub Kegiatan</th>
                    <th class="text-center">Perangkat Daerah</th>
                    <th class="text-center">Pagu</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>