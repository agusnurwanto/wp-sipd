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

$no = 0;
$master_sumberdana = '';
$rincian = '';
$lock_sumber_dana = get_option('_crb_kunci_sumber_dana_mapping');

if($lock_sumber_dana==1)
{
	$sumberdana = $wpdb->get_results('
		select 
			d.iddana, d.kodedana, d.namadana, sum(d.pagudana) total, count(s.kode_sbl) jml 
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
				<td><a href="'.$url_skpd.'&id_skpd='.$input['id_skpd'].'" target="_blank" data-id="'.$title.'">'.$val['namadana'].'</a></td>
				<td class="text_tengah text_kanan">'.number_format($val['total'], 0,',','.').'</td>
				<td class="text_tengah">'.$val['jml'].'</td>
				<td class="text_tengah">'.$val['iddana'].'</td>
				<td class="text_tengah">'.$input['tahun_anggaran'].'</td>
			</tr>
		';
	}
}

if($lock_sumber_dana==2)
{

	$arr_html = array(
		'data' => array()
	);
	$data_all = array(
		'data' => array()
	);
	$sub_keg_bl = $wpdb->get_results($wpdb->prepare("
		SELECT 
			kode_sbl, 
			nama_sub_giat 
		FROM data_sub_keg_bl 
		WHERE 
			active=1 AND 
			id_sub_skpd=%d AND 
			tahun_anggaran=%d", 
			$input['id_skpd'], $input['tahun_anggaran']), 
		ARRAY_A);

	foreach ($sub_keg_bl as $k1 => $s) {

		if(empty($data_all['data'][$s['kode_sbl']])){
			$data_all['data'][$s['kode_sbl']] = array(
				'kode_sub_giat' => $s['kode_sbl'],
				'nama_sub_giat' => $s['nama_sub_giat'],
				'data' => array()
			);

			$mapping = $wpdb->get_results($wpdb->prepare("
				SELECT 
					c.kode_dana, 
					a.id_sumber_dana, 
					COUNT(id_sumber_dana) jml, 
					SUM(b.total_harga) total_harga, 
					c.nama_dana 
				FROM data_mapping_sumberdana a 
					LEFT JOIN data_rka b 
						ON a.id_rinci_sub_bl=b.id_rinci_sub_bl
					LEFT JOIN data_sumber_dana c 
						ON a.id_sumber_dana=c.id_dana
				WHERE 
					b.kode_sbl=%s AND
					a.tahun_anggaran=%d AND
					b.tahun_anggaran=%d AND
					a.active=1 AND 
					b.active=1 
				GROUP BY a.id_sumber_dana 
				ORDER BY a.id DESC", 
					$s['kode_sbl'],
					$input['tahun_anggaran'],
					$input['tahun_anggaran']
			), 
			ARRAY_A);

			foreach ($mapping as $k2 => $r) {
				$data_all['data'][$s['kode_sbl']]['data'][$r['id_sumber_dana']] = array(
					'kode_dana' => $r['kode_dana'],
					'id_sumber_dana' => $r['id_sumber_dana'],
					'nama_dana' => $r['nama_dana'],
					'jumlah' => $r['jml'],
					'total_harga' => $r['total_harga']
				);
			}
		}
	}

	foreach ($data_all['data'] as $k1 => $v1) {
		if(empty($v1['data'])){
			$key=0;
			if(empty($arr_html['data'][$key])){
				$arr_html['data'][$key] = array(
					'kode_dana' => '',
					'id_sumber_dana' => 0,
					'namadana' => 'Belum disetting',
					'jml_sub_keg' => 0,
					'total_pagu' => 0
				);
			}
			$arr_html['data'][$key]['jml_sub_keg']+=1;
		}

		foreach ($v1['data'] as $k2 => $v2) {
			if(empty($arr_html['data'][$v2['id_sumber_dana']])){
				$arr_html['data'][$v2['id_sumber_dana']] = array(
					'kode_dana' => $v2['kode_dana'],
					'id_sumber_dana' => $v2['id_sumber_dana'],
					'namadana' => $v2['nama_dana'],
					'jml_sub_keg' => 0,
					'total_pagu' => 0
				);
			}
			$arr_html['data'][$v2['id_sumber_dana']]['jml_sub_keg']+=1;
			$arr_html['data'][$v2['id_sumber_dana']]['total_pagu']+=$v2['total_harga'];
		}
	}

	$no = 1;
	$total_pagu = 0;
	foreach ($arr_html['data'] as $key => $value) {
		$title = 'Laporan APBD Per Sumber Dana '.$value['kode_dana'].' '.$value['namadana'].' | '.$input['tahun_anggaran'];
		$custom_post = get_page_by_title($title, OBJECT, 'page');
		$url_skpd = $this->get_link_post($custom_post);
		/*
			<td class="atas kanan bawah"><a href="javascript:void(0)" data-id="'.$input['tahun_anggaran'].'-'.$input['id_skpd'].'-'.$value['id_sumber_dana'].'" onclick="getRincianMapping(this)">'.$value['namadana'].'</a></td>
		*/
		$master_sumberdana .= '
			<tr>
				<td class="atas kanan bawah kiri text_tengah">'.$no.'</td>
				<td class="atas kanan bawah">'.$value['kode_dana'].'</td>
				<td class="atas kanan bawah"><a href="'.$url_skpd.'&id_skpd='.$input['id_skpd'].'" data-title="'.$title.'" data-id="'.$input['tahun_anggaran'].'-'.$input['id_skpd'].'-'.$value['id_sumber_dana'].'" onclick="getRincianMapping(this)">'.$value['namadana'].'</a></td>
				<td class="atas kanan bawah text_tengah text_kanan">'.number_format($value['total_pagu'], 0,',','.').'</td>
				<td class="atas kanan bawah text_tengah">'.$value['jml_sub_keg'].'</td>
				<td class="atas kanan bawah text_tengah">'.$value['id_sumber_dana'].'</td>
				<td class="atas kanan bawah text_tengah">'.$input['tahun_anggaran'].'</td>
			</tr>
		';

		$total_pagu += $value['total_pagu'];
		$no++;
	}
	$master_sumberdana .="
		<tr>
			<td colspan='3' class='atas kanan bawah kiri text_tengah text_blok'>TOTAL</td>
			<td class='atas kanan bawah text_kanan'>".number_format($total_pagu, 0,',','.')."</td>
			<td class='atas kanan bawah'></td>
			<td class='atas kanan bawah'></td>
			<td class='atas kanan bawah'></td>
		</tr>
	";
	$rincian .='<div id="rincian" style="padding: 10px;"></div>';	
}
?>

<style type="text/css">
	
	.tabel-sumber-dana {
		font-size: small;
	}

</style>

<div style="padding: 10px;">
	<h3 class="text_tengah">DAFTAR SUMBER DANA</h3>
	<table class="wp-list-table widefat fixed striped tabel-sumber-dana">
		<thead>
			<tr class="text_tengah">
				<th class="atas kanan bawah kiri text_tengah" style="width: 20px">No</th>
				<th class="atas kanan bawah text_tengah" style="width: 100px">Kode</th>
				<th class="atas kanan bawah text_tengah">Sumber Dana</th>
				<th class="atas kanan bawah text_tengah">Total Pagu Sumber Dana(Rp.)</th>
				<th class="atas kanan bawah text_tengah" style="width:50px">Jumlah Sub Kegiatan</th>
				<th class="atas kanan bawah text_tengah" style="width: 50px">ID Dana</th>
				<th class="atas kanan bawah text_tengah" style="width: 110px">Tahun Anggaran</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $master_sumberdana; ?>
		</tbody>
	</table>
</div>
<?php echo $rincian; ?>

<script>
	function getRincianMapping(that){
		var data = jQuery(that).attr('data-id');
		var dataTemp = data.split('-');
		var tahun_anggaran = dataTemp[0];
		var id_skpd = dataTemp[1];
		var id_sumber_dana = dataTemp[2];

		jQuery("#rincian").html('');
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:"post",
			data:{
				'action' : "get_rincian_sumber_dana_mapping",
				'tahun_anggaran' : tahun_anggaran,
				'id_skpd' : id_skpd,
				'id_sumber_dana' : id_sumber_dana,
			},
			dataType: "json",
			success:function(response){
				jQuery("#wrap-loading").hide();
				jQuery("#rincian").html(response.rincian);
			}
		})
	}

</script>