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
$format_sumber_dana = get_option('_crb_kunci_sumber_dana_mapping');
?>

<style type="text/css">
	
	.tabel-sumber-dana {
		font-size: small;
	}

</style>

<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
<div class="text_tengah" style="padding: 10px;">
	<h3 class="text_tengah">DAFTAR SUMBER DANA</h3>
	<select style="margin-bottom: 15px; width: 200px;" id="pilih_tahun" onchange="tahun_sumberdana();">
		<option value="0">Pilih Tahun</option>
	    <option selected="" value="2021">2021</option>
	    <option value="2022">2022</option>
	</select>
	<select style="margin-bottom: 15px; margin-left: 25px; min-width: 200px;" id="pilih_skpd">
	    <option value="2234">2.16.2.21.2.20.01.0000 DINAS KOMUNIKASI DAN INFORMATIKA</option>
	</select>
	<br>
	<label>
		<input type="radio" id="format_1" name="format-sd" format-id="1" onclick="format_sumberdana(this);"> Format Per Sumber Dana SIPD
	</label>
	<label style="margin-left: 25px;">
		<input type="radio" id="format_2" name="format-sd" format-id="2" onclick="format_sumberdana(this);"> Format Kombinasi Sumber Dana SIPD
	</label>
	<label style="margin-left: 25px;">
		<input type="radio" id="format_3" name="format-sd" format-id="3" onclick="format_sumberdana(this);"> Format Per Sumber Dana Mapping
	</label>
</div>

<div style="padding:10px">
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
			<tbody id="body-sumber-dana">
			</tbody>
		</table>
</div>

<script>

	var format_sumber_dana = <?php echo $format_sumber_dana; ?> 
	jQuery(document).ready(function(){
		if(format_sumber_dana==1){
			jQuery("#format_1").prop("checked",true);
		}else if(format_sumber_dana==2){
			jQuery("#format_3").prop("checked",true);
		}else{
			jQuery("#format_3").prop("checked",true);
		}
		// get_sumber_dana(format_sumber_dana);
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