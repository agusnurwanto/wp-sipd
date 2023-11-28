<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$input = shortcode_atts( array(
	'tahun_anggaran' => get_option('_crb_tahun_anggaran_sipd')
), $atts );
if(!empty($_GET) && !empty($_GET['tahun'])){
	$input['tahun_anggaran'] = $_GET['tahun'];
}

global $wpdb;

$nama_komponen = $_GET['nama_komponen'];
$spek_komponen = $_GET['spek_komponen'];
$harga_satuan = $_GET['harga_satuan'];
$satuan = $_GET['satuan'];

$where_skpd = '';
if(!empty($_GET['id_skpd'])){
	$where_skpd = $wpdb->prepare("AND dskb.id_sub_skpd=%d", $_GET['id_skpd']);
}

if(!empty($spek_komponen)){
	$where_skpd .= $wpdb->prepare(" AND dr.spek_komponen=%s", $spek_komponen);
}else{
	$where_skpd .= " AND ( dr.spek_komponen IS NULL OR dr.spek_komponen='' )";
}

if(!empty($nama_komponen)){
	$where_skpd .= $wpdb->prepare(" AND dr.nama_komponen=%s", $nama_komponen);
}else{
	$where_skpd .= " AND ( dr.nama_komponen IS NULL OR dr.nama_komponen='' )";
}

if(!empty($harga_satuan)){
	$where_skpd .= $wpdb->prepare(" AND dr.harga_satuan=%d", $harga_satuan);
}else{
	$where_skpd .= " AND ( dr.harga_satuan IS NULL OR dr.harga_satuan='' )";
}

if(!empty($satuan)){
	$where_skpd .= $wpdb->prepare(" AND dr.satuan=%s", $satuan);
}else{
	$where_skpd .= " AND ( dr.satuan IS NULL OR dr.satuan='' )";
}

$sql = $wpdb->prepare("
	SELECT 
		du.kode_skpd,
		du.nama_skpd,
		dskb.nama_giat,
		dskb.kode_giat,
		dskb.kode_sub_giat,
		dskb.nama_sub_giat,
		dskb.pagu as pagu_rka,
		dr.nama_komponen,
		dr.spek_komponen,
		dr.harga_satuan,
		dr.satuan,
		SUM(dr.volume) as volume,
		SUM(dr.total_harga) as total,
		dr.kode_sbl 
		FROM `data_rka` as dr 
		INNER JOIN data_sub_keg_bl as dskb ON dr.kode_sbl = dskb.kode_sbl 
			and dskb.active = dr.active
			and dskb.tahun_anggaran = dr.tahun_anggaran
		INNER JOIN data_unit as du ON dskb.id_sub_skpd = du.id_skpd 
			and du.active = dr.active
			and du.tahun_anggaran = dr.tahun_anggaran
		WHERE dr.active=1 
			and dr.tahun_anggaran=%d
			$where_skpd
		GROUP by dr.kode_sbl
		ORDER BY total DESC, dr.nama_komponen asc",
		$input['tahun_anggaran']
	);
$queryRecords = $wpdb->get_results($sql, ARRAY_A);

$data_sub_komponen = [
	'all_total_ssh' => 0,
	'all_total_volume' => 0,
	'all_total_rka' => 0,
	'satuan' => 0,
	'data' => []
];
foreach ($queryRecords as $key => $value) {
	if(empty($data_sub_komponen['data'][$value['kode_skpd']])){
		$data_sub_komponen['data'][$value['kode_skpd']] = [
			'nama_skpd' 	=> $value['nama_skpd'],
			'kode_skpd'		=> $value['kode_skpd'],
			'total_rka'		=> 0,
			'total_volume'	=> 0,
			'total_ssh'		=> 0,
			'data'			=> [],
		];
	}
	$data_sub_komponen['data'][$value['kode_skpd']]['total_rka'] += $value['pagu_rka'];
	$data_sub_komponen['data'][$value['kode_skpd']]['total_volume'] += $value['volume'];
	$data_sub_komponen['data'][$value['kode_skpd']]['total_ssh'] += $value['total'];
	$data_sub_komponen['all_total_ssh'] += $value['total'];
	$data_sub_komponen['all_total_volume'] += $value['volume'];
	$data_sub_komponen['all_total_rka'] += $value['pagu_rka'];

	if(empty($data_sub_komponen['data'][$value['kode_skpd']]['data'][$value['kode_sub_giat']])){
		$data_sub_komponen['data'][$value['kode_skpd']]['data'][$value['kode_sub_giat']] = [
			'pagu_rka'		=> $value['pagu_rka'],
			'nama_giat'		=> $value['nama_giat'],
			'kode_giat'		=> $value['kode_giat'],
			'nama_sub_giat' => $value['nama_sub_giat'],
			'kode_sub_giat' => $value['kode_sub_giat'],
			'volume'		=> $value['volume'],
			'total_harga'	=> $value['total']
		];
	}
}

$nama_page_menu_ssh = 'Data Standar Satuan Harga SIPD | '.$input['tahun_anggaran'];
$custom_post = get_page_by_title($nama_page_menu_ssh, OBJECT, 'page');
$url_data_ssh = $this->get_link_post($custom_post);

$body_komponen = '';
$no_all = 0;
foreach($data_sub_komponen['data'] as $kd_skpd => $val_komponen){
	$no_all++;
	$body_komponen .= '
		<tr>
			<td class="text-center" style="font-weight: bold;">'.$no_all.'</td>
			<td style="font-weight: bold;">'.$kd_skpd.' '.$val_komponen['nama_skpd'].'</td>
			<td style="font-weight: bold;text-align:right;">'.number_format($val_komponen['total_rka'],0,'','.').'</td>
			<td style="font-weight: bold;text-align:right;">'.number_format($val_komponen['total_volume'],0,'','.').'</td>
			<td style="font-weight: bold;text-align:right;">'.number_format($val_komponen['total_ssh'],0,'','.').'</td>
		</tr>
	';

	$no_sub = 0;
	foreach($val_komponen['data'] as $key_sub_komponen => $val_sub_komponen ){
		$no_sub++;
		$nama_page = $input['tahun_anggaran'] . ' | ' . $val_komponen['kode_skpd'] . ' | ' . $val_sub_komponen['kode_giat'] . ' | ' . $val_sub_komponen['nama_giat'];
    	$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
    	$link = 'style="color: red;" title="'.$nama_page.'"';
    	if(!empty($custom_post)){
            $link = 'href="'.$this->get_link_post($custom_post).'"';
    	}

		$body_komponen .= '
			<tr class="sub_keg" style="display:none;">
				<td class="text-center">'.$no_all.'.'.$no_sub.'</td>
				<td style="padding-left: 2rem;"><a '.$link.' target="_blank">'.$val_sub_komponen['kode_sub_giat'].' '.substr($val_sub_komponen['nama_sub_giat'],16).'</a></td>
				<td style="text-align:right;">'.number_format($val_sub_komponen['pagu_rka'],0,'','.').'</td>
				<td style="text-align:right;">'.number_format($val_sub_komponen['volume'],0,'','.').'</td>
				<td style="text-align:right;">'.number_format($val_sub_komponen['total_harga'],0,'','.').'</td>
			</tr>
		';
	}
}

?>
<style type="text/css">
	#data_header th {
		vertical-align: top;
		display: none-important;
	}
</style>
<div class="cetak">
	<div style="padding: 10px;margin:0 0 3rem 0;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
		<h2 class="text-center title_detail_ssh">Data Total Nilai Satuan Harga Per SKPD dan Sub Kegiatan<br>Nama Komponen: <?php echo $nama_komponen; ?><br><?php echo $spek_komponen; ?><br>Satuan: <?php echo $satuan; ?><br>Harga Satuan: <?php echo number_format($harga_satuan,0,'','.'); ?><br>Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h2>
		<table id="data_ssh_komponen" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center" style="width:2rem;">No</th>
					<th class="text-center">SKPD/Sub Kegiatan</th>
					<th class="text-center">RKA SIPD</th>
					<th class="text-center">Volume</th>
					<th class="text-center">Nilai SSH</th>
				</tr>
			</thead>
			<tbody id="data_body" class="data_body_ssh">
				<?php echo $body_komponen ?>
			</tbody>
			<tfoot>
				<tr>
					<td style="font-weight: bold;" class="text-center" colspan="2">Jumlah Total</td>
					<td style="font-weight: bold;" class="text_kanan"><?php echo number_format($data_sub_komponen['all_total_rka'],0,'','.'); ?></td>
					<td style="font-weight: bold;" class="text_kanan"><?php echo number_format($data_sub_komponen['all_total_volume'],0,'','.'); ?></td>
					<td style="font-weight: bold;" class="text_kanan"><?php echo number_format($data_sub_komponen['all_total_ssh'],0,'','.'); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<script type="text/javascript">
	function show_sub_keg(that){
		var checked = jQuery(that).is(':checked');
		if(checked){
			jQuery('tr.sub_keg').show();
		}else{
			jQuery('tr.sub_keg').hide();
		}
	}
	jQuery(document).ready(function(){
		
		globalThis.tahun = <?php echo $input['tahun_anggaran']; ?>;
		extend_action = ''
			+'<h3 style="margin-top: 20px;text-align:center;">SETTING</h3>'
			+'<label style="display:block;text-align:center;"><input type="checkbox" onclick="show_sub_keg(this);"> Tampilkan Sub Kegiatan</label>';
		jQuery('.title_detail_ssh').after(extend_action);
	})

</script> 
