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
				$data_body[strtotime($update_at_tanpa_rinci['update_at']).$opd['id_skpd'].$rek.'-tanpa-rinci'] = array(
					'rek' => $rek.'-tanpa-rinci',
					'type_belanja' => $type_belanja.' Tanpa Rincian',
					'skpd' => $opd['nama_skpd'],
					'update_at' => $update_at_tanpa_rinci['update_at'],
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
					'type_belanja' => $type_belanja.' Pagu SKPD',
					'skpd' => $opd['nama_skpd'],
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
			$data = $wpdb->get_row('
				select 
					sum(nilaimurni) as total_murni, 
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
			$data_body[strtotime($update_at['update_at']).$opd['id_skpd'].$rek] = array(
				'rek' => $rek,
				'type_belanja' => $type_belanja,
				'skpd' => $opd['nama_skpd'],
				'update_at' => $update_at['update_at'],
				'total_murni' => $data['total_murni'],
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
	$body .= '
		<tr data-type-belanja="'.$data['rek'].'">
			<td class="text-center">'.$no.'</td>
			<td class="text-center">'.$data['type_belanja'].'</td>
			<td>'.$data['skpd'].'</td>
			<td class="text-center">'.$data['update_at'].'</td>
			<td class="text-right">'.number_format($data['total_murni'],0,",",".").'</td>
			<td class="text-right">'.number_format($data['total'],0,",",".").'</td>
		</tr>
	';
}

?>
<div class="cetak" style="padding: 10px;">
	<h1 class="text-center">Monitoring Data SIPD lokal Berdasar Waktu Terakhir Melakukan Singkronisasi Data Tahun <?php echo $input['tahun_anggaran']; ?></h1>
	<table>
		<thead>
			<tr>
				<th class="text-center">No</th>
				<th class="text-center">Type Belanja</th>
				<th class="text-center">Nama SKPD</th>
				<th class="text-center">Last Syncrone</th>
				<th class="text-center">Pagu Sebelum</th>
				<th class="text-center">Pagu Terkini</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
	</table>
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
		+'</div>';
	jQuery('#action-sipd #excel').after(filter);
	function tampilDataTypeBelanja(that, kd_rek){
		if(jQuery(that).is(':checked')){
			jQuery('tr[data-type-belanja="'+kd_rek+'"]').show();
		}else{
			jQuery('tr[data-type-belanja="'+kd_rek+'"]').hide();
		}
	}
</script>