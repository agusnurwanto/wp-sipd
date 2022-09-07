<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;

function button_edit_monev($class=false){
	$ret = ' <span style="display: none;" data-id="'.$class.'" class="edit-monev"><i class="dashicons dashicons-edit"></i></span>';
	return $ret;
}

function get_target($target, $satuan){
	if(empty($satuan)){
		return $target;
	}else{
		$target = explode($satuan, $target);
		return $target[0];
	}
}

function parsing_nama_kode($nama_kode){
	$nama_kodes = explode('||', $nama_kode);
	$nama = $nama_kodes[0];
	unset($nama_kodes[0]);
	return $nama.'<span class="debug-kode">||'.implode('||', $nama_kodes).'</span>';
}

$api_key = get_option('_crb_api_key_extension' );

$awal_rpjpd = 2018;
$akhir_rpjpd = $awal_rpjpd+20;
$nama_pemda = get_option('_crb_daerah');

$current_user = wp_get_current_user();
$bulan = date('m');
$body_monev = '';

$data_all = array(
	'data' => array()
);
$bulan = date('m');

$visi_ids = array();
$misi_ids = array();
$sasaran_ids = array();
$kebijakan_ids = array();
$isu_ids = array();

$sql = "
	select 
		* 
	from data_rpjpd_visi
";
$visi_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($visi_all as $visi) {
	if(empty($data_all['data'][$visi['id']])){
		$data_all['data'][$visi['id']] = array(
			'nama' => $visi['visi_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data'][$visi['id']]['detail'][] = $visi;

	$visi_ids[$visi['id']] = "'".$visi['id']."'";
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjpd_misi
		where id_visi=%s
	", $visi['id']);
	$misi_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($misi_all as $misi) {
		if(empty($data_all['data'][$visi['id']]['data'][$misi['id']])){
			$data_all['data'][$visi['id']]['data'][$misi['id']] = array(
				'nama' => $misi['misi_teks'],
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data'][$visi['id']]['data'][$misi['id']]['detail'][] = $misi;

		$misi_ids[$misi['id']] = "'".$misi['id']."'";
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjpd_sasaran
			where id_misi=%s
		", $misi['id']);
		$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($sasaran_all as $sasaran) {
			$sasaran_ids[$sasaran['id']] = "'".$sasaran['id']."'";
			if(empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']])){
				$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']] = array(
					'nama' => $sasaran['saspok_teks'],
					'detail' => array(),
					'data' => array()
				);
			}
			$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['detail'][] = $sasaran;

			$sql = $wpdb->prepare("
				select 
					* 
				from data_rpjpd_kebijakan
				where id_saspok=%s
			", $sasaran['id']);
			$kebijakan_all = $wpdb->get_results($sql, ARRAY_A);
			foreach ($kebijakan_all as $kebijakan) {
				$kebijakan_ids[$kebijakan['id']] = "'".$kebijakan['id']."'";
				if(empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']])){
					$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']] = array(
						'nama' => $kebijakan['kebijakan_teks'],
						'detail' => array(),
						'data' => array()
					);
				}
				$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['detail'][] = $kebijakan;

				$sql = $wpdb->prepare("
					select 
						* 
					from data_rpjpd_isu
					where id_kebijakan=%s
				", $kebijakan['id']);
				$isu_all = $wpdb->get_results($sql, ARRAY_A);
				foreach ($isu_all as $isu) {
					$isu_ids[$isu['id']] = "'".$isu['id']."'";
					if(empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']])){
						$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']] = array(
							'nama' => $isu['isu_teks'],
							'detail' => array(),
							'data' => array()
						);
					}
					$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']]['data'][$kebijakan['id']]['data'][$isu['id']]['detail'][] = $isu;
				}
			}
		}
	}
}

// buat array data kosong
if(empty($data_all['data']['visi_kosong'])){
	$data_all['data']['visi_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'detail' => array(),
		'data' => array()
	);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong'])){
	$data_all['data']['visi_kosong']['data']['misi_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'detail' => array(),
		'data' => array()
	);
}

// select visi yang belum terselect
if(!empty($visi_ids)){
	$sql = "
		select 
			* 
		from data_rpjpd_visi
		where id not in (".implode(',', $visi_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpjpd_visi
	";
}
$visi_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($visi_all_kosong as $visi) {
	if(empty($data_all['data'][$visi['id']])){
		$data_all['data'][$visi['id']] = array(
			'nama' => $visi['visi_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data'][$visi['id']]['detail'][] = $visi;
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjpd_misi
		where kode_visi=%s
	", $visi['id']);
	$misi_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($misi_all as $misi) {
		$misi_ids[$misi['id']] = "'".$misi['id']."'";
		if(empty($data_all['data'][$visi['id']]['data'][$misi['id']])){
			$data_all['data'][$visi['id']]['data'][$misi['id']] = array(
				'nama' => $misi['misi_teks'],
				'detail' => array(),
				'data' => array()
			);
		}
		$data_all['data'][$visi['id']]['data'][$misi['id']]['detail'][] = $misi;
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjpd_sasaran
			where kode_misi=%s
		", $misi['id']);
		$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($sasaran_all as $sasaran) {
			$sasaran_ids[$sasaran['id']] = "'".$sasaran['id']."'";
			if(empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']])){
				$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$sasaran['id']] = array(
					'nama' => $sasaran['sasaran_teks'],
					'detail' => array(),
					'data' => array()
				);
			}
		}
	}
}

// select misi yang belum terselect
if(!empty($misi_ids)){
	$sql = "
		select 
			* 
		from data_rpjpd_misi
		where id not in (".implode(',', $misi_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpjpd_misi
	";
}
$misi_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($misi_all_kosong as $misi) {
	if(empty($data_all['data']['visi_kosong']['data'][$misi['id']])){
		$data_all['data']['visi_kosong']['data'][$misi['id']] = array(
			'nama' => $misi['misi_teks'],
			'detail' => array(),
			'data' => array()
		);
	}
	$data_all['data']['visi_kosong']['data'][$misi['id']]['detail'][] = $misi;
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjpd_sasaran
		where kode_misi=%s
	", $misi['id']);
	$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($sasaran_all as $sasaran) {
		$sasaran_ids[$sasaran['id']] = "'".$sasaran['id']."'";
		if(empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$sasaran['id']])){
			$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$sasaran['id']] = array(
				'nama' => $sasaran['sasaran_teks'],
				'detail' => array(),
				'data' => array()
			);
		}
	}
}

// select sasaran yang belum terselect
if(!empty($sasaran_ids)){
	$sql = "
		select 
			* 
		from data_rpjpd_sasaran
		where id not in (".implode(',', $sasaran_ids).")
	";
}else{
	$sql = "
		select 
			* 
		from data_rpjpd_sasaran
	";
}
$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($sasaran_all as $sasaran) {
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$sasaran['id']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$sasaran['id']] = array(
			'nama' => $sasaran['nama_sasaran'],
			'detail' => array(),
			'data' => array()
		);
	}
}

// hapus array jika data dengan key kosong tidak ada datanya
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'])){
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']);
}
if(empty($data_all['data']['visi_kosong']['data'])){
	unset($data_all['data']['visi_kosong']);
}

$body = '';
$no_visi = 0;
foreach ($data_all['data'] as $visi) {
	$no_visi++;
	$indikator_visi = '';
	foreach($visi['detail'] as $k => $v){
		if(!empty($v['indikator_teks'])){
			$indikator_visi .= '<div class="indikator_sasaran">'.$v['indikator_teks'].'</div>';
		}
	}
	$body .= '
		<tr class="tr-visi">
			<td class="kiri atas kanan bawah">'.$no_visi.'</td>
			<td class="atas kanan bawah">'.parsing_nama_kode($visi['nama']).'</td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah">'.$indikator_visi.'</td>
			<td class="atas kanan bawah"></td>
		</tr>
	';
	$no_misi = 0;
	foreach ($visi['data'] as $misi) {
		$no_misi++;
		$indikator_misi = '';
		foreach($misi['detail'] as $k => $v){
			if(!empty($v['indikator_teks'])){
				$indikator_misi .= '<div class="indikator_sasaran">'.$v['indikator_teks'].'</div>';
			}
		}
		$body .= '
			<tr class="tr-misi">
				<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'</td>
				<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
				<td class="atas kanan bawah">'.parsing_nama_kode($misi['nama']).'</td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah">'.$indikator_misi.'</td>
				<td class="atas kanan bawah"></td>
			</tr>
		';
		$no_sasaran = 0;
		foreach ($misi['data'] as $sasaran) {
			$no_sasaran++;
			$text_indikator = array();
			$text_indikator = implode('', $text_indikator);
			$body .= '
				<tr class="tr-sasaran" data-kode-skpd="'.$sasaran['kode_skpd'].'">
					<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'.'.$no_sasaran.'</td>
					<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
					<td class="atas kanan bawah"><span class="debug-misi">'.$misi['nama'].'</span></td>
					<td class="atas kanan bawah">'.parsing_nama_kode($sasaran['nama']).'</td>
					<td class="atas kanan bawah">'.$text_indikator.'</td>
					<td class="atas kanan bawah">'.$sasaran['kode_skpd'].' '.$sasaran['nama_skpd'].'</td>
				</tr>
			';
		}
	}
}
?>
<style type="text/css">
	.debug-visi, .debug-misi, .debug-visi, .debug-misi, .debug-kode { display: none; }
	.indikator_sasaran { min-height: 40px; }
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitoring dan Evaluasi RPJPD (Rencana Pembangunan Jangka Panjang Daerah) <br><?php echo $nama_pemda; ?></h4>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; height: 80vh;">
	<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<tr>
				<th style="width: 85px;" class="atas kiri kanan bawah text_tengah text_blok">No</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Visi</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Misi</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Sasaran</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Kebijakan</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Isu</th>
			</tr>
			<tr>
				<th class='atas kiri kanan bawah text_tengah text_blok'>0</th>
				<th class='atas kanan bawah text_tengah text_blok'>1</th>
				<th class='atas kanan bawah text_tengah text_blok'>2</th>
				<th class='atas kanan bawah text_tengah text_blok'>3</th>
				<th class='atas kanan bawah text_tengah text_blok'>4</th>
				<th class='atas kanan bawah text_tengah text_blok'>5</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
	</table>
</div>
<div class="modal fade" id="modal-monev" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data RPJPD</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<nav>
				  	<div class="nav nav-tabs" id="nav-tab" role="tablist">
					    <a class="nav-item nav-link active" id="nav-visi-tab" data-toggle="tab" href="#nav-visi" role="tab" aria-controls="nav-visi" aria-selected="false">visi</a>
					    <a class="nav-item nav-link" id="nav-misi-tab" data-toggle="tab" href="#nav-misi" role="tab" aria-controls="nav-misi" aria-selected="false">misi</a>
					    <a class="nav-item nav-link" id="nav-sasaran-tab" data-toggle="tab" href="#nav-sasaran" role="tab" aria-controls="nav-sasaran" aria-selected="false">sasaran</a>
					    <a class="nav-item nav-link" id="nav-kebijakan-tab" data-toggle="tab" href="#nav-kebijakan" role="tab" aria-controls="nav-kebijakan" aria-selected="false">kebijakan</a>
					    <a class="nav-item nav-link" id="nav-isu-tab" data-toggle="tab" href="#nav-isu" role="tab" aria-controls="nav-isu" aria-selected="false">isu</a>
				  	</div>
				</nav>
				<div class="tab-content" id="nav-tabContent">
				  	<div class="tab-pane fade" id="nav-visi" role="tabpanel" aria-labelledby="nav-visi-tab">...</div>
				  	<div class="tab-pane fade" id="nav-misi" role="tabpanel" aria-labelledby="nav-misi-tab">...</div>
				  	<div class="tab-pane fade" id="nav-sasaran" role="tabpanel" aria-labelledby="nav-sasaran-tab">...</div>
				  	<div class="tab-pane fade" id="nav-kebijakan" role="tabpanel" aria-labelledby="nav-kebijakan-tab">...</div>
				  	<div class="tab-pane fade" id="nav-isu" role="tabpanel" aria-labelledby="nav-isu-tab">...</div>
				</div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	run_download_excel();
	let data_all = <?php echo json_encode($data_all); ?>;

	var aksi = ''
		+'<a style="margin-left: 10px;" id="singkron-sipd" onclick="return false;" href="#" class="btn btn-danger">Ambil data dari SIPD lokal</a>'
		+'<a style="margin-left: 10px;" id="tambah-data" onclick="return false;" href="#" class="btn btn-success">Tambah Data RPJPD</a>'
		+'<h3 style="margin-top: 20px;">SETTING</h3>'
		+'<label><input type="checkbox" onclick="tampilkan_edit(this);"> Edit Data RPJPD</label>'
		+'<label style="margin-left: 20px;"><input type="checkbox" onclick="show_debug(this);"> Debug Cascading RPJPD</label>'
		+'<label style="margin-left: 20px;">'
			+'Sembunyikan Baris '
			+'<select id="sembunyikan-baris" onchange="sembunyikan_baris(this);" style="padding: 5px 10px; min-width: 200px;">'
				+'<option value="">Pilih Baris</option>'
				+'<option value="tr-misi">misi</option>'
				+'<option value="tr-sasaran">sasaran</option>'
			+'</select>'
		+'</label>';
	jQuery('#action-sipd').append(aksi);
	function filter_skpd(that){
		var tr_sasaran = jQuery('.tr-sasaran');
		var val = jQuery(that).val();
		if(val == ''){
			tr_sasaran.show();
		}else{
			tr_sasaran.hide();
			jQuery('.tr-sasaran[data-kode-skpd="'+val+'"]').show();
		}
	}
	function sembunyikan_baris(that){
		var val = jQuery(that).val();
		var tr_misi = jQuery('.tr-misi');
		var tr_sasaran = jQuery('.tr-sasaran');
		tr_misi.show();
		tr_visi.show();
		tr_misi.show();
		tr_sasaran.show();
		if(val == 'tr-misi'){
			tr_misi.hide();
			tr_visi.hide();
			tr_misi.hide();
			tr_sasaran.hide();
		}else if(val == 'tr-visi'){
			tr_visi.hide();
			tr_misi.hide();
			tr_sasaran.hide();
		}else if(val == 'tr-misi'){
			tr_misi.hide();
			tr_sasaran.hide();
		}else if(val == 'tr-sasaran'){
			tr_sasaran.hide();
		}
	}
	function show_debug(that){
		if(jQuery(that).is(':checked')){
			jQuery('.debug-visi').show();
			jQuery('.debug-misi').show();
			jQuery('.debug-visi').show();
			jQuery('.debug-misi').show();
			jQuery('.debug-kode').show();
		}else{
			jQuery('.debug-visi').hide();
			jQuery('.debug-misi').hide();
			jQuery('.debug-visi').hide();
			jQuery('.debug-misi').hide();
			jQuery('.debug-kode').hide();
		}
	}
	function tampilkan_edit(that){
		if(jQuery(that).is(':checked')){
			jQuery('.edit-monev').show();
		}else{
			jQuery('.edit-monev').hide();
		}
	}
	jQuery('.edit-monev').on('click', function(){
		jQuery('#wrap-loading').show();
		jQuery('#mod-monev').modal('show');
		jQuery('#wrap-loading').hide();
	});
	jQuery('#singkron-sipd').on('click', function(){
		if(confirm('Apakah anda yakin untuk mengambil data dari SIPD lokal? data lama akan diupdate!')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "singkron_rpjpd_sipd_lokal",
	          		"api_key": "<?php echo $api_key; ?>",
	          		"user": "<?php echo $current_user->display_name; ?>"
	          	},
	          	dataType: "json",
	          	success: function(res){
					jQuery('#wrap-loading').hide();
					alert(res.message);
	          	}
	        });
		}
	});
	jQuery('#tambah-data').on('click', function(){
		jQuery('#wrap-loading').show();
		jQuery('#modal-monev').modal('show');
		jQuery.ajax({
			url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_visi_rpjpd",
          		"api_key": "<?php echo $api_key; ?>",
          		"type": 1
          	},
          	dataType: "json",
          	success: function(res){
				jQuery('#wrap-loading').hide();
          	}
        });
	});
</script>