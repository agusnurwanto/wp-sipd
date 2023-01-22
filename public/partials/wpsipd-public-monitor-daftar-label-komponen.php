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

$api_key = get_option( '_crb_api_key_extension' );
$sql = $wpdb->prepare("
	select 
		kode_skpd,
		nama_skpd
	from data_unit 
	where tahun_anggaran=%d
		and id_skpd = ".$input['id_skpd']."
		and active=1
	order by id_skpd ASC
", $input['tahun_anggaran']);
$skpd = $wpdb->get_row($sql, ARRAY_A);

$data_label_komponen = $wpdb->get_results("select id, nama, keterangan from data_label_komponen where tahun_anggaran=".$input['tahun_anggaran'], ARRAY_A);
$body = '';
foreach ($data_label_komponen as $k => $v) {
	$title = 'Laporan APBD Per Label Komponen "'.$v['nama'].'" | '.$input['tahun_anggaran'];
	$custom_post = get_page_by_title($title, OBJECT, 'page');
	$url_label = $this->get_link_post($custom_post);
	$body .= '
	<tr data-id="'.$v['id'].'">
		<td class="text_tengah">'.($k+1).'</td>
		<td><a href="'.$url_label.'&id_skpd='.$input['id_skpd'].'" target="_blank">'.$v['nama'].'</a></td>
		<td>'.$v['keterangan'].'</td>
		<td class="text_kanan pagu-rincian">-</td>
		<td class="text_kanan realisasi-rincian">-</td>
		<td class="text_kanan jml-rincian">-</td>
	</tr>
	';
}
?>
<style type="text/css">
	body {
		margin: 8px;
	}
</style>
<div class="cetak">
	<h3 class="text_tengah">Daftar Label Komponen<br><?php echo $skpd['kode_skpd'].' '.$skpd['nama_skpd']; ?><br>Tahun <?php echo $input['tahun_anggaran']; ?></h3>
	<table class="table table-bordered table-striped">
		<thead>
			<tr class="text_tengah">
				<th class="text_tengah" rowspan="2" style="vertical-align: middle;">No</th>
				<th class="text_tengah" rowspan="2" style="vertical-align: middle;">Nama Label</th>
				<th class="text_tengah" rowspan="2" style="vertical-align: middle;">Keterangan</th>
				<th class="text_tengah text_blok" colspan="3">Analisa Rincian <span style="padding: 4px;" data-id="analis-rincian" id="analisa_komponen" class="edit-mapping"><i class="dashicons dashicons-controls-repeat"></i></span></th>
			</tr>
			<tr>
				<th class="text_tengah text_blok">Pagu Rincian</th>
				<th class="text_tengah text_blok">Realisasi</th>
				<th class="text_tengah text_blok">Jumlah Rincian</th>
			</tr>
			<tr>
				<th class="text_tengah" style="width: 20px;">1</th>
				<th class="text_tengah" style="width: 300px;">2</th>
				<th class="text_tengah">3</th>
				<th class="text_tengah" style="width: 140px;">4</th>
				<th class="text_tengah" style="width: 140px;">5</th>
				<th class="text_tengah" style="width: 140px;">6</th>
			</tr>
		</thead>
		<tbody id="body_label">
			<?php echo $body; ?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	run_download_excel();
	jQuery('#analisa_komponen').on('click', function(){
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_analis_rincian_label",
          		"api_key": "<?php echo $api_key; ?>",
          		"tahun_anggaran": <?php echo $input['tahun_anggaran']; ?>
          	},
          	dataType: "json",
          	success: function(data){
				jQuery('#wrap-loading').hide();
				if(data.status == 'success'){
					window.analisa_komponen = data.data;
					analisa_komponen.map(function(b, i){
						var tr = jQuery('#body_label tr[data-id="'+b.id_label_komponen+'"]').closest('tr');
						tr.find('.pagu-rincian').text(b.pagu);
						tr.find('.realisasi-rincian').text(b.realisasi);
						tr.find('.jml-rincian').text(b.jml_rincian);
					});
				}else{
					return alert(data.message);
				}
			},
			error: function(e) {
				console.log(e);
				jQuery('#wrap-loading').hide();
				return alert(e);
			}
		});
	});
	setTimeout(function(){
		jQuery('#analisa_komponen').trigger('click');
	}, 1000);
</script>