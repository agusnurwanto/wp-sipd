<?php
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2021'
), $atts );

if(empty($input['id_skpd'])){
	die('<h1>SKPD tidak ditemukan!</h1>');
}
$data_label_komponen = $wpdb->get_results("select id, nama, keterangan from data_label_komponen where tahun_anggaran=".$input['tahun_anggaran'], ARRAY_A);
$body = '';
foreach ($data_label_komponen as $k => $v) {
	$title = 'Laporan APBD Per Label Komponen "'.$v['nama'].'" | '.$input['tahun_anggaran'];
	$custom_post = get_page_by_title($title, OBJECT, 'page');
	$url_label = esc_url(get_permalink($custom_post));
	$body .= '
	<tr>
		<td class="text-tengah">'.($k+1).'</td>
		<td><a href="'.$url_label.'&key='.$this->gen_key().'&id_skpd='.$input['id_skpd'].'" target="_blank">'.$v['nama'].'</a></td>
		<td>'.$v['keterangan'].'</td>
	</tr>
	';
}
?>
<h3 class="text_tengah">Daftar Label Komponen</h3>
<table class="wp-list-table widefat fixed striped">
	<thead>
		<tr class="text_tengah">
			<th class="text_tengah" style="width: 20px">No</th>
			<th class="text_tengah" style="width: 300px">Nama Label</th>
			<th class="text_tengah">Keterangan</th>
		</tr>
	</thead>
	<tbody id="body_label">
		<?php echo $body; ?>
	</tbody>
</table>