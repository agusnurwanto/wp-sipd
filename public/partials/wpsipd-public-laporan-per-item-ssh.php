<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$input = shortcode_atts( array(
	'tahun_anggaran' => '2022'
), $atts );

global $wpdb;

$nama_komponen = $_GET['nama_komponen'];
$spek_komponen = $_GET['spek_komponen'];
$harga_satuan = $_GET['harga_satuan'];
$satuan = $_GET['satuan'];

$sql = "SELECT du.kode_skpd, du.nama_skpd,dskb.nama_giat,dskb.kode_giat,dskb.kode_sub_giat,dskb.nama_sub_giat, dr.nama_komponen, dr.spek_komponen, dr.harga_satuan, dr.satuan, SUM(dr.volume) as volume, SUM(dr.total_harga) as total, dr.kode_sbl 
		FROM `data_rka` as dr INNER JOIN data_sub_keg_bl as dskb ON dr.kode_sbl = dskb.kode_sbl 
		INNER JOIN data_unit as du ON dskb.id_skpd = du.id_skpd 
		WHERE dr.active=1 and dr.tahun_anggaran=2022 AND dr.nama_komponen='".$nama_komponen."' AND dr.spek_komponen='".$spek_komponen."' AND dr.harga_satuan=".$harga_satuan." AND dr.satuan='".$satuan."' 
		GROUP by dr.kode_sbl, dr.nama_komponen, dr.spek_komponen, dr.harga_satuan 
		ORDER BY total DESC, dr.nama_komponen asc";
$queryRecords = $wpdb->get_results($sql, ARRAY_A);

$data_sub_komponen = [
	'data' => []
];
foreach ($queryRecords as $key => $value) {
	if(empty($data_sub_komponen['data'][$value['kode_skpd']])){
		$data_sub_komponen['data'][$value['kode_skpd']] = [
			'nama_skpd' 	=> $value['nama_skpd'],
			'kode_skpd'		=> $value['kode_skpd'],
			'nama_komponen' => $value['nama_komponen'],
			'spek_komponen' => $value['spek_komponen'],
			'harga_satuan'	=> $value['harga_satuan'],
			'satuan'		=> $value['satuan'],
			'total_ssh'		=> 0,
			'data'			=> [],
		];
	}
	$data_sub_komponen['data'][$value['kode_skpd']]['total_ssh'] += $value['total'];
	$data_sub_komponen['all_total_ssh'] += $value['total'];

	if(empty($data_sub_komponen['data'][$value['kode_skpd']]['data'][$value['kode_sub_giat']])){
		$data_sub_komponen['data'][$value['kode_skpd']]['data'][$value['kode_sub_giat']] = [
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
$no_all = '';
foreach($data_sub_komponen['data'] as $key_kompenen => $val_komponen){
	$body_komponen .= '
		<tr>
			<td></td>
			<td style="font-weight: bold;">'.$val_komponen['nama_skpd'].'</td>
			<td></td>
			<td style="font-weight: bold;text-align:right;">'.number_format($val_komponen['total_ssh'],0,'','.').'</td>
		</tr>
	';

	foreach($val_komponen['data'] as $key_sub_komponen => $val_sub_komponen ){
		$no_all++;
		
		$nama_page = $input['tahun_anggaran'] . ' | ' . $val_komponen['kode_skpd'] . ' | ' . $val_sub_komponen['kode_giat'] . ' | ' . $val_sub_komponen['nama_giat'];
    	$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
    	$link = 'style="color: red;" title="'.$nama_page.'"';
    	if(!empty($custom_post)){
            $link = 'href="'.$this->get_link_post($custom_post).'"';
    	}

		$body_komponen .= '
			<tr class="sub_keg" style="display:none;">
				<td>'.$no_all.'</td>
				<td style="padding-left: 2rem;"><a '.$link.' target="_blank">'.$val_sub_komponen['kode_sub_giat'].' '.substr($val_sub_komponen['nama_sub_giat'],16).'</a></td>
				<td style="text-align:right;">asdasd</td>
				<td style="text-align:right;">'.number_format($val_sub_komponen['total_harga'],0,'','.').'</td>
			</tr>
		';
	}
}

$body_komponen .= '
	<tr class="sub_keg">
		<td style="text-align:center;font-weight:bold;" colspan="2">Jumlah Total</td>
		<td style="font-weight:bold;text-align:right;">asdasd</td>
		<td style="font-weight:bold;text-align:right;">'.number_format($data_sub_komponen['all_total_ssh'],0,'','.').'</td>
	</tr>
';
?>
	<div class="cetak">
		<div style="padding: 10px;margin:0 0 3rem 0;">
			<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
			<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
			<h2 class="text-center" style="margin:3rem 0 0 0;font-weight:bold;">Data Detail Item SSH </h2>
			<h3 class="text-center" style="margin:0;font-weight:bold;"><?php echo $nama_komponen ?></h3>
			<h4 class="text-center title_detail_ssh" style="margin:0 3rem;font-weight:bold;">Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h4>
			<table id="data_ssh_komponen" cellpadding="2" cellspacing="0" style="padding: 2px 5px;font-size:13px;font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
				<thead id="data_header">
					<tr>
						<th class="text-center" style="width:2rem;">No</th>
						<th class="text-center">SKPD/Sub Kegiatan</th>
						<th class="text-center">RKA SIPD</th>
						<th class="text-center">Pagu SSH</th>
					</tr>
				</thead>
				<tbody id="data_body" class="data_body_ssh">
					<?php echo $body_komponen ?>
				</tbody>
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
