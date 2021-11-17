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

if(empty($units)){
	die('<h1>SKPD tidak ditemukan!</h1>');
}else{
	$opt_tahun = '<option value="">Pilih Tahun</option>';
	$pengaturan = $wpdb->get_results($wpdb->prepare("select distinct(tahun_anggaran) tahun from data_unit where active=%d", 1), ARRAY_A);
	foreach ($pengaturan as $key => $value) {
		$selected='';
		if($value['tahun']==$tahun_asli){
			$selected='selected';
		}
		$opt_tahun.='<option value="'.$value['tahun'].'" '.$selected.'>'.$value['tahun'].'</option>';
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
				<select style="margin-bottom: 15px; width: 200px;" id="pilih_tahun" onchange="tahun_sumberdana(this);">
					<?php echo $opt_tahun; ?>
				</select>
			</label>
			<br>
			<label>
				<input type="radio" id="format_1" name="format-sd" format-id="1" onclick="format_sumberdana(this);"> Format Per Sumber Dana SIPD
			</label>
			<label style="margin-left: 25px;">
				<input type="radio" id="format_3" name="format-sd" format-id="3" onclick="format_sumberdana(this);"> Format Kombinasi Sumber Dana SIPD
			</label>
			<label style="margin-left: 25px;">
				<input type="radio" id="format_2" name="format-sd" format-id="2" onclick="format_sumberdana(this);"> Format Per Sumber Dana Mapping
			</label>
		</div>
	</div>

	<div style="padding:10px">
		<table class="wp-list-table widefat fixed striped tabel-sumber-dana"></table>
	</div>
</div>

<script>
	run_download_excel();
	jQuery(document).ready(function(){

		var format_sumber_dana = <?php echo $format_sumber_dana; ?>;
		var tahun = <?php echo $input['tahun_anggaran']; ?>;
		
		jQuery("#pilih_tahun").val(tahun);
		if(format_sumber_dana==1){
			jQuery("#format_1").prop("checked",true);
		}else if(format_sumber_dana==2){
			jQuery("#format_2").prop("checked",true);
		}else{
			jQuery("#format_3").prop("checked",true);
		}
		get_sumber_dana(format_sumber_dana, tahun);
	})

	function get_sumber_dana(format_sumber_dana, tahun){
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:"post",
			data:{
				'action' : "get_sumber_dana_mapping",
				'api_key' : jQuery("#api_key").val(),
				'tahun_anggaran' : tahun,
				'id_skpd' : <?php echo $input['id_skpd']; ?>,
				'format_sumber_dana' : format_sumber_dana,
			},
			dataType: "json",
			success:function(response){
				jQuery("#wrap-loading").hide();
				jQuery(".tabel-sumber-dana").html(response.table_content);
			}
		})
	}

	function format_sumberdana(that){
		var format = jQuery(that).attr('format-id');
		var tahun = jQuery("#pilih_tahun").val();
		get_sumber_dana(format, tahun);
	}

	function tahun_sumberdana(that){
		var tahun = jQuery(that).val();
		if(tahun != "" && tahun > 0 && tahun != undefined){
			var format = jQuery("input[type=radio]:checked").attr("format-id");
			get_sumber_dana(format, tahun);
		}
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