<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2021'
), $atts );

if(empty($input['id_skpd'])){
	die('<h1>SKPD tidak ditemukan!</h1>');
}

$master_sumberdana = '';
$no = 0;
$sumberdana = $wpdb->get_results('
	select 
		d.iddana,
		d.kodedana,
		d.namadana 
	from data_dana_sub_keg d
		INNER JOIN data_sub_keg_bl s ON d.kode_sbl=s.kode_sbl
			AND s.tahun_anggaran=d.tahun_anggaran
			AND s.active=d.active
	where d.tahun_anggaran='.$input['tahun_anggaran'].'
		and s.id_sub_skpd='.$input['id_skpd'].'
		and d.active=1
	group by iddana
	order by kodedana ASC
', ARRAY_A);
foreach ($sumberdana as $key => $val) {
	$no++;
	$title = 'Laporan APBD Per Sumber Dana '.$val['kodedana'].' '.$val['namadana'].' | '.$input['tahun_anggaran'];
	$custom_post = get_page_by_title($title, OBJECT, 'page');
	$url_skpd = $this->get_link_post($custom_post);
	if(empty($val['kodedana'])){
		$val['kodedana'] = '';
		$val['namadana'] = 'Belum Di Setting';
	}
	$master_sumberdana .= '
		<tr>
			<td class="text_tengah">'.$no.'</td>
			<td>'.$val['kodedana'].'</td>
			<td><a href="'.$url_skpd.'&id_skpd='.$input['id_skpd'].'" target="_blank">'.$val['namadana'].'</a></td>
			<td class="text_tengah">'.$val['iddana'].'</td>
			<td class="text_tengah">'.$input['tahun_anggaran'].'</td>
		</tr>
	';
}
?>

<h3 class="text_tengah">Daftar Sumber Dana</h3>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr class="text_tengah">
				<th class="text_tengah" style="width: 20px">No</th>
				<th class="text_tengah" style="width: 100px">Kode</th>
				<th class="text_tengah">Sumber Dana</th>
				<th class="text_tengah" style="width: 50px">ID Dana</th>
				<th class="text_tengah" style="width: 110px">Tahun Anggaran</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $master_sumberdana; ?>
		</tbody>
	</table>