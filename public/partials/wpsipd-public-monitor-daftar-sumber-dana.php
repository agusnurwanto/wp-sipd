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

$tahun_asli = date('Y');
if(empty($_GET) || empty($_GET['debug'])){
	if($input['tahun_anggaran'] > $tahun_asli){
		die('<h1>Halaman Sumber Dana tahun '.$input['tahun_anggaran'].' tidak ditemukan!</h1>');
	}
}
if(!empty($input['id_skpd'])){
	$sql = $wpdb->prepare("
		select 
			kodeunit, namaunit 
		from data_unit 
		where tahun_anggaran=%d
			and id_skpd IN (".$input['id_skpd'].") 
			and active=1
		order by id_skpd ASC
	", $input['tahun_anggaran']);
}else{
	$sql = $wpdb->prepare("
		select 
			kodeunit, namaunit 
		from data_unit 
		where tahun_anggaran=%d
			and active=1
		order by id_skpd ASC
	", $input['tahun_anggaran']);
}
$units = $wpdb->get_row($sql, ARRAY_A);

$opt_tahun = '<option value="">Pilih Tahun</option>';
if(empty($units)){
	die('<h1>SKPD tidak ditemukan!</h1>');
}else{
	$pengaturan = $wpdb->get_row($wpdb->prepare("
		select 
			awal_rpjmd, akhir_rpjmd 
		from data_pengaturan_sipd 
		where tahun_anggaran=%d and id=1
	", $input['tahun_anggaran']), ARRAY_A);
	
	for ($i=$pengaturan['awal_rpjmd']; $i <= $pengaturan['akhir_rpjmd']; $i++) {
		if($i<=$input['tahun_anggaran']){
			$opt_tahun.='<option value="'.$i.'">'.$i.'</option>';
		} 
	}
}
$format_sumber_dana = get_option('_crb_kunci_sumber_dana_mapping');
?>

<style type="text/css">
	.tabel-sumber-dana {
		font-size: small;
	}
</style>

<div id="cetak" title="Daftar Sumber Dana <?php echo $units['namaunit'] ." Tahun ".$input['tahun_anggaran'];?>">
	<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
	<div class="text_tengah" style="padding: 10px;">
		<h4 class="text_tengah" style="text-align: center; margin: 0; font-weight: bold;">
			Daftar Sumber Dana <br>
			<?php echo $units['kodeunit'].' '.$units['namaunit']; ?>
		</h4>
		<br>
		<div class="hide-excel">
			<label>
				Tahun
				<select style="margin-bottom: 15px; width: 200px;" id="pilih_tahun" onchange="tahun_sumberdana();">
					<?php echo $opt_tahun; ?>
				</select>
			</label>
			<br>
			<label>
				<input type="radio" id="format_1" name="format-sd" format-id="1" onclick="format_sumberdana(this);"> Format Per Sumber Dana SIPD
			</label>
			<label style="margin-left: 25px;">
				<input type="radio" id="format_2" name="format-sd" format-id="2" onclick="format_sumberdana(this);"> Format Per Sumber Dana Mapping
			</label>
			<label style="margin-left: 25px;">
				<input type="radio" id="format_3" name="format-sd" format-id="3" onclick="format_sumberdana(this);"> Format Kombinasi Sumber Dana SIPD
			</label>
		</div>
	</div>

	<div style="padding:10px">
		<table class="wp-list-table widefat fixed striped tabel-sumber-dana">
			<thead>
				<tr class="text_tengah">
					<th class="atas kanan bawah kiri text_tengah" style="width: 20px; vertical-align: middle;">No</th>
					<th class="atas kanan bawah text_tengah" style="width: 100px; vertical-align: middle;">Kode</th>
					<th class="atas kanan bawah text_tengah" style="vertical-align: middle;">Sumber Dana</th>
					<th class="atas kanan bawah text_tengah" style="vertical-align: middle;">Total Pagu Sumber Dana(Rp.)</th>
					<th class="atas kanan bawah text_tengah" style="width:50px; vertical-align: middle;">Jumlah Sub Kegiatan</th>
					<th class="atas kanan bawah text_tengah" style="width: 50px; vertical-align: middle;">ID Dana</th>
					<th class="atas kanan bawah text_tengah" style="width: 110px; vertical-align: middle;">Tahun Anggaran</th>
				</tr>
			</thead>
			<tbody id="body-sumber-dana">
			</tbody>
		</table>
	</div>
</div>

<script>
	run_download_excel();
	var format_sumber_dana = <?php echo $format_sumber_dana; ?> 
	jQuery(document).ready(function(){
		jQuery("#pilih_tahun").val(<?php echo $input['tahun_anggaran']; ?>);
		if(format_sumber_dana==1){
			jQuery("#format_1").prop("checked",true);
		}else if(format_sumber_dana==2){
			jQuery("#format_2").prop("checked",true);
		}else{
			jQuery("#format_3").prop("checked",true);
		}
		get_sumber_dana(format_sumber_dana);
	})

	function get_sumber_dana(format_sumber_dana){
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:"post",
			data:{
				'action' : "get_sumber_dana_mapping",
				'api_key' : jQuery("#api_key").val(),
				'tahun_anggaran' : <?php echo $input['tahun_anggaran']; ?>,
				'id_skpd' : <?php echo $input['id_skpd']; ?>,
				'format_sumber_dana' : format_sumber_dana,
			},
			dataType: "json",
			success:function(response){
				jQuery("#wrap-loading").hide();
				jQuery("#body-sumber-dana").html(response.body);
			}
		})
	}

	function format_sumberdana(that){
		var format = jQuery(that).attr('format-id');
		get_sumber_dana(format);
	}

	function tahun_sumberdana(){
		
	}

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