<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$input = shortcode_atts( array(
	'kode_rek' => '',
	'tahun_anggaran' => '2021'
), $atts );

global $wpdb;
$kode_rek = array();
if(!empty($input['kode_rek'])){
	$rek = explode(',', $input['kode_rek']);
	foreach ($rek as $k => $v) {
		$v = trim($v);
		$kode_rek[$v] = $v;
	}
}else{
	$kode_rek = array(
		'4' => '4', 
		'5' => '5', 
		'6.1' => '6.1', 
		'6.2' => '6.2'
	);
}

$dpa_rfk = get_option('_crb_default_sumber_pagu_dpa');

$skpd = $wpdb->get_results('select * from data_unit where tahun_anggaran='.$input['tahun_anggaran'].' and active=1', 
	ARRAY_A);
$data_body = array();

foreach ($kode_rek as $rek) {
	foreach ($skpd as $k => $opd) {
		$type_belanja = '';
		$table = '';
		$table_tanpa_rinci = '';
		if($rek == '4'){
			$type_belanja = 'Pendapatan';
			$table = 'data_pendapatan';
		}else if($rek == '5'){
			$type_belanja = 'Belanja';
			$table = 'data_rka';
			$table_tanpa_rinci = 'data_sub_keg_bl';
			$table_pagu_unit = 'data_unit_pagu';
		}else if($rek == '6.1'){
			$table = 'data_pembiayaan';
			$type_belanja = 'Pembiayaan Pengeluaran';
		}else if($rek == '6.2'){
			$table = 'data_pembiayaan';
			$type_belanja = 'Pembiayaan Penerimaan';
		}else{
			continue;
		}
		if($table == 'data_rka'){
			$where = '
				tahun_anggaran='.$input['tahun_anggaran'].' 
					and active=1 
					and kode_bl LIKE \''.$opd['idinduk'].'.'.$opd['id_skpd'].'.%\'
			';
			$data = $wpdb->get_row('
				select 
					sum(rincian_murni) as total_murni, 
					sum(rincian) as total,
					0 as total_fmis,
					count(update_at) as jml 
				from '.$table.' 
				where '.$where
			, ARRAY_A);

			$update_at = $wpdb->get_row('
				select 
					update_at
				from '.$table.' 
				where '.$where.'
				order by update_at ASC'
			, ARRAY_A);

			$where = '
				tahun_anggaran='.$input['tahun_anggaran'].' 
					and active=1 
					and id_sub_skpd='.$opd['id_skpd'].'
			';
			$data_tanpa_rinci = $wpdb->get_row('
				select 
					sum(pagumurni) as total_murni, 
					sum(pagu) as total,
					sum(pagu_fmis) as total_fmis,
					count(update_at) as jml 
				from '.$table_tanpa_rinci.' 
				where '.$where
			, ARRAY_A);
			$update_at_tanpa_rinci = $wpdb->get_row('
				select 
					update_at
				from '.$table_tanpa_rinci.' 
				where '.$where.'
				order by update_at ASC'
			, ARRAY_A);

			if($data_tanpa_rinci['jml']>=1){
				$warning = 0;
				if($data_tanpa_rinci['total'] != $data['total']){
					$warning = 1;
				}
				$warning_fmis = 0;
				if($data_tanpa_rinci['total_fmis'] != $data['total']){
					$warning_fmis = 1;
				}
				$data_body[strtotime($update_at_tanpa_rinci['update_at']).$opd['id_skpd'].$rek.'-tanpa-rinci'] = array(
					'warning' => $warning,
					'warning_fmis' => $warning_fmis,
					'rek' => $rek.'-tanpa-rinci',
					'type_belanja' => $type_belanja.' Tanpa Rincian <span class="debug hide">'.$wpdb->last_query.'</span>',
					'skpd' => $opd['nama_skpd'],
					'id_skpd' => $opd['id_skpd'],
					'kode_skpd' => $opd['kode_skpd'],
					'update_at' => $update_at_tanpa_rinci['update_at'],
					'total_fmis' => $data_tanpa_rinci['total_fmis'],
					'total_murni' => $data_tanpa_rinci['total_murni'],
					'total' => $data_tanpa_rinci['total'],
				);
			}

			$where = '
				tahun_anggaran='.$input['tahun_anggaran'].' 
					and kode_skpd=\''.$opd['kode_skpd'].'\'
			';
			$data_pagu_unit = $wpdb->get_row('
				select 
					sum(nilaipagumurni) as total_murni, 
					sum(nilaipagu) as total,
					count(update_at) as jml 
				from '.$table_pagu_unit.' 
				where '.$where
			, ARRAY_A);
			$update_at_pagu_unit = $wpdb->get_row('
				select 
					update_at
				from '.$table_pagu_unit.' 
				where '.$where.'
				order by update_at ASC'
			, ARRAY_A);

			if($data_pagu_unit['jml']>=1){
				$data_body[strtotime($update_at_pagu_unit['update_at']).$opd['id_skpd'].$rek.'-pagu-skpd'] = array(
					'rek' => $rek.'-pagu-skpd',
					'type_belanja' => $type_belanja.' Pagu SKPD <span class="debug hide">'.$wpdb->last_query.'</span>',
					'skpd' => $opd['nama_skpd'],
					'id_skpd' => $opd['id_skpd'],
					'kode_skpd' => $opd['kode_skpd'],
					'update_at' => $update_at_pagu_unit['update_at'],
					'total_murni' => $data_pagu_unit['total_murni'],
					'total' => $data_pagu_unit['total'],
				);
			}
		}else{
			$where = '
				tahun_anggaran='.$input['tahun_anggaran'].' 
					and active=1 
					and id_skpd='.$opd['id_skpd'].'
			';
			if($type_belanja == 'Pembiayaan Pengeluaran'){
				$where .= " and type='pengeluaran'";
			}else if($type_belanja == 'Pembiayaan Penerimaan'){
				$where .= " and type='penerimaan'";
			}
			$data = $wpdb->get_row('
				select 
					sum(nilaimurni) as total_murni, 
					sum(pagu_fmis) as total_fmis,
					sum(total) as total,
					count(update_at) as jml
				from '.$table.' 
				where '.$where
			, ARRAY_A);
			$update_at = $wpdb->get_row('
				select 
					update_at 
				from '.$table.' 
				where '.$where.'
				order by update_at ASC'
			, ARRAY_A);
		}
		if($data['jml']>=1){
			$warning_fmis = 0;
			if(
				$table != 'data_rka'
				&& $data['total_fmis'] != $data['total']
			){
				$warning_fmis = 1;
			}
			$data_body[strtotime($update_at['update_at']).$opd['id_skpd'].$rek] = array(
				'rek' => $rek,
				'type_belanja' => $type_belanja.' <span class="debug hide">'.$wpdb->last_query.'</span>',
				'skpd' => $opd['nama_skpd'],
				'id_skpd' => $opd['id_skpd'],
				'kode_skpd' => $opd['kode_skpd'],
				'update_at' => $update_at['update_at'],
				'total_murni' => $data['total_murni'],
				'warning_fmis' => $warning_fmis,
				'total_fmis' => $data['total_fmis'],
				'total' => $data['total'],
			);
		}
	}
}

ksort($data_body);
$body = '';
$no = 0;
foreach ($data_body as $k => $data) {
	$no++;
	$nama_page = 'RFK '.$data['skpd'].' '.$data['kode_skpd'].' | '.$input['tahun_anggaran'];
	$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
	$link = $this->get_link_post($custom_post);
	if($dpa_rfk == 2){
		$link .= '&pagu_dpa=fmis';
	}
	$warning = '';
	if(!empty($data['warning'])){
		$warning = 'background: #ff00002e;';
	}
	$warning_fmis = '';
	if(!empty($data['warning_fmis'])){
		$warning_fmis = 'background: #ff00002e;';
	}
	if(empty($data['total_fmis'])){
		$data['total_fmis'] = 0;
	}
	$body .= '
		<tr data-type-belanja="'.$data['rek'].'" data-id-skpd="'.$data['id_skpd'].'">
			<td class="text-center">'.$no.'</td>
			<td class="text-center">'.$data['type_belanja'].'</td>
			<td><a href="'.$link.'" target="_blank">'.$data['skpd'].'</a></td>
			<td class="text-center">'.$data['update_at'].'</td>
			<td class="text-right" style="'.$warning_fmis.'">'.number_format($data['total_fmis'],2,",",".").'</td>
			<td class="text-right">'.number_format($data['total_murni'],2,",",".").'</td>
			<td class="text-right pagu_total" style="'.$warning.'">'.number_format($data['total'],2,",",".").'</td>
			<td class="text-right rka_simda" style="display: none;"></td>
			<td class="text-right dpa_simda" style="display: none;"></td>
		</tr>
	';
}

?>
<style type="text/css">
	.warning {
		background: #f1a4a4;
	}
	.hide {
		display: none;
	}
</style>
<div class="cetak">
	<div style="padding: 10px;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
		<h1 class="text-center">Monitoring Data SIPD lokal Berdasar Waktu Terakhir Melakukan Singkronisasi Data Tahun <?php echo $input['tahun_anggaran']; ?></h1>
		<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center">No</th>
					<th class="text-center">Type Belanja</th>
					<th class="text-center">Nama SKPD</th>
					<th class="text-center" style="width: 100px;">Last Syncrone</th>
					<th class="text-center">Pagu FMIS</th>
					<th class="text-center">Pagu Sebelum</th>
					<th class="text-center">Pagu Terkini</th>
					<th class="text-center rka_simda" style="display: none;">Pagu RKA SIMDA</th>
					<th class="text-center dpa_simda" style="display: none;">Pagu DPA SIMDA</th>
				</tr>
			</thead>
			<tbody id="data_body">
				<?php echo $body; ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
	run_download_excel();
	var filter = ''
		+'<div style="padding-top: 20px;">'
			+'<label><input type="checkbox" checked="true" onclick="tampilDataTypeBelanja(this, \'4\')"> Tampil Pendapatan</label>'
			+'<label style="margin-left: 10px;"><input type="checkbox" checked="true" onclick="tampilDataTypeBelanja(this, \'5\')"> Tampil Belanja</label>'
			+'<label style="margin-left: 10px;"><input type="checkbox" checked="true" onclick="tampilDataTypeBelanja(this, \'5-tanpa-rinci\')"> Tampil Belanja Tanpa Rincian</label>'
			+'<label style="margin-left: 10px;"><input type="checkbox" checked="true" onclick="tampilDataTypeBelanja(this, \'5-pagu-skpd\')"> Tampil Belanja Pagu SKPD</label>'
			+'<label style="margin-left: 10px;"><input type="checkbox" checked="true" onclick="tampilDataTypeBelanja(this, \'6.1\')"> Tampil Pembiayaan Pengeluaran</label>'
			+'<label style="margin-left: 10px;"><input type="checkbox" checked="true" onclick="tampilDataTypeBelanja(this, \'6.2\')"> Tampil Pembiayaan Penerimaan</label>'
			+'<label style="margin-left: 10px;"><input type="checkbox" onclick="tampilDataRkaSimda(this)"> Tampil RKA SIMDA</label>'
			+'<label style="margin-left: 10px;"><input type="checkbox" onclick="tampilDataDpaSimda(this)"> Tampil DPA SIMDA</label>'
		+'</div>';
	jQuery('#action-sipd #excel').after(filter);
	function tampilDataTypeBelanja(that, kd_rek){
		if(jQuery(that).is(':checked')){
			jQuery('tr[data-type-belanja="'+kd_rek+'"]').show();
		}else{
			jQuery('tr[data-type-belanja="'+kd_rek+'"]').hide();
		}
	}
	function tampilDataRkaSimda(that){
		if(jQuery(that).is(':checked')){
			jQuery('.rka_simda').show();
			jQuery('#wrap-loading').show();
			var data_id_skpd = [];
			jQuery('#data_body tr').map(function(i, b){
				var tr = jQuery(b);
				var type_rek = tr.attr('data-type-belanja');
				if(type_rek == 5){
					data_id_skpd.push(tr.attr('data-id-skpd'));
				}
			});
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
	          	type: "post",
	          	data: {
	          		"action": "get_rka_simda",
	          		"api_key": jQuery('#api_key').val(),
	          		"tahun_anggaran": jQuery('#tahun_anggaran').val(),
	          		"id_skpd": data_id_skpd
	          	},
	          	dataType: "json",
	          	success: function(data){
					jQuery('#wrap-loading').hide();
					var data_blm_singkron = [];
					for(var id_skpd in data.data_blm_singkron){
						data_blm_singkron.push(data.data_blm_singkron[id_skpd].nm_sub_unit);
					}
	          		if(data_blm_singkron.length >= 1){
	          			alert('SKPD yang belum tersingkronisasi dari SIMDA: '+data_blm_singkron.join(', '));
	          		}
	          		for(var id_skpd in data.data){
	          			var tr = jQuery('#data_body tr[data-id-skpd="'+id_skpd+'"][data-type-belanja="5"]');
	          			var pagu_total = tr.find('.pagu_total').text().trim();
	          			var rka_simda = tr.find('td.rka_simda');
	          			rka_simda.text(data.data[id_skpd]);
	          			rka_simda.removeClass('warning');
	          			if(pagu_total != data.data[id_skpd]){
	          				rka_simda.addClass('warning');
	          			}
	          		}
				},
				error: function(e) {
					console.log(e);
					jQuery('#wrap-loading').hide();
					return;
				}
			});
		}else{
			jQuery('.rka_simda').hide();
		}
	}
	function tampilDataDpaSimda(that){
		if(jQuery(that).is(':checked')){
			jQuery('.dpa_simda').show();
			jQuery('#wrap-loading').show();
			var data_id_skpd = [];
			jQuery('#data_body tr').map(function(i, b){
				var tr = jQuery(b);
				var type_rek = tr.attr('data-type-belanja');
				if(type_rek == 5){
					data_id_skpd.push(tr.attr('data-id-skpd'));
				}
			});
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
	          	type: "post",
	          	data: {
	          		"action": "get_dpa_simda",
	          		"api_key": jQuery('#api_key').val(),
	          		"tahun_anggaran": jQuery('#tahun_anggaran').val(),
	          		"id_skpd": data_id_skpd
	          	},
	          	dataType: "json",
	          	success: function(data){
					jQuery('#wrap-loading').hide();
					var data_blm_singkron = [];
					for(var id_skpd in data.data_blm_singkron){
						data_blm_singkron.push(data.data_blm_singkron[id_skpd].nm_sub_unit);
					}
	          		if(data_blm_singkron.length >= 1){
	          			alert('SKPD yang belum tersingkronisasi dari SIMDA: '+data_blm_singkron.join(', '));
	          		}
	          		for(var id_skpd in data.data){
	          			var tr = jQuery('#data_body tr[data-id-skpd="'+id_skpd+'"][data-type-belanja="5"]');
	          			var pagu_total = tr.find('.pagu_total').text().trim();
	          			var dpa_simda = tr.find('td.dpa_simda');
	          			dpa_simda.text(data.data[id_skpd]);
	          			dpa_simda.removeClass('warning');
	          			if(pagu_total != data.data[id_skpd]){
	          				dpa_simda.addClass('warning');
	          			}
	          		}
				},
				error: function(e) {
					console.log(e);
					jQuery('#wrap-loading').hide();
					return;
				}
			});
		}else{
			jQuery('.dpa_simda').hide();
		}
	}
</script>
