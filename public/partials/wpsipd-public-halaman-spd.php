<?php
// If this file is called directly, abort.

$api_key = get_option('_crb_api_key_extension');
$url = admin_url('admin-ajax.php');
if (!defined('WPINC')) {
	die;
}
global $wpdb;
$input = shortcode_atts(array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts);

$total_all_spd = 0;
$body = '';
if (!empty($input['id_skpd'])) {
	$sql = $wpdb->prepare("
		select 
			* 
		from data_unit 
		where tahun_anggaran=%d
			and id_skpd=%d 
			and active=1
		order by id_skpd ASC
	", $input['tahun_anggaran'], $input['id_skpd']);
	$unit = $wpdb->get_row($sql, ARRAY_A);
}else{
	die('<h1>SKPD tidak boleh kosong!</h1>');
}

$get_spd = $wpdb->get_results($wpdb->prepare("
	SELECT
		s.*
	FROM data_spd_sipd s
	WHERE s.tahun_anggaran=%d
	  AND s.id_skpd = %d
	  AND s.active=1
", $input['tahun_anggaran'], $input['id_skpd']), ARRAY_A);
// print_r($get_spd); die($wpdb->last_query);
if (!empty($get_spd)) {
	foreach ($get_spd as $k => $v) {
		$url_no_spd = '<a  onclick="showspd('.$v['idSpd'].'); return false;" href="#">'.$v['nomorSpd'].'</a>';
		$body .= '
			<tr>
				<td class="text-center" style="width: 100px;">' . $v['idSpd'] . '</td>
				<td class="text-center" style="width: 100px;">' . $url_no_spd . '</td>
				<td class="text-center">' . $v['keteranganSpd'] . '</td>
				<td class="text-center">' . $v['ketentuanLainnya'] . '</td>
				<td class="text-right">' . number_format($v['totalSpd'],0,",","."). '</td>
			</tr>
		';
		$total_all_spd += $v['totalSpd'];
	}
}
?>

<style type="text/css">
	th {
		vertical-align: middle !important;
	}
</style>
<div class="cetak">
	<div style="padding: 10px;">
	<input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
	<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
	<input type="hidden" value="<?php echo $unit['id_skpd']; ?>" id="id_skpd">
	<div id="TableShowSPD" title="Halaman SPD <?php echo $input['tahun_anggaran']; ?>" style="padding: 5px;">
		<h4 style="text-align: center; margin: 0; font-weight: bold;">Surat Penyediaan Dana (SPD)<br><?php echo $unit['kode_skpd']; ?>&nbsp;<?php echo $unit['nama_skpd']; ?><br>Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h4><br>
		<table id="TableShowSPD" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="atas kanan bawah text_tengah" style="width: 100px;">ID SPD</th>
					<th class="atas kanan bawah text_tengah" style="width: 100px;">Nomor SPD</th>
			        <th class="atas kanan bawah text_tengah">Keterangan SPD</th>
			        <th class="atas kanan bawah text_tengah">Ketentuan Lainnya</th>
			        <th class="atas kanan bawah text_tengah">Total SPD</th>
				</tr>
			</thead>
			<tbody id="data_body">
				<?php echo $body; ?>
			</tbody>
		</table>
	</div>
</div>


<div class="modal fade" id="showspd" tabindex="-1" role="dialog" aria-labelledby="showspdLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showspdLabel">Detail SPD</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="wrap-table-detail">
                    <h6>ID SPD : <span id="id_spd"></span><br>Nomor SPD : <span id="nomor_spd"></span></h6>
                    <table id="table-data-spd" cellpadding="2" cellspacing="0" style="font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; border-collapse: collapse; width: 100%; overflow-wrap: break-word;" class="table table-bordered">
                        <thead>
                            <tr>
								<th class="text-center">ID Detail SPD</th>
								<th class="text-center">ID Akun</th>
								<th class="text-center">ID Program</th>
								<th class="text-center">ID Kegiatan</th>
								<th class="text-center">ID Sub Kegiatan</th>
								<th class="text-center">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    function showspd(id) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo $url; ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'action': 'get_data_spd_sipd',
                'api_key': '<?php echo $api_key; ?>',
                'tahun_anggaran': '<?php echo $input['tahun_anggaran'] ?>',
                'idSpd': id,
            },
            success: function(res) {
                console.log(res);
                if (res.status == 'success') {
                	jQuery('#id_spd').html(id);
                	jQuery('#nomor_spd').html(res.data.nomorSpd);
                	var html = '';
            		var akun = '-';
            		var program = '-';
            		var giat = '-';
            		var sub_giat = '-';
            		var nilai = '-';
                	res.data.detail.map(function(b, i){
							if(b.id_akun != null){
								akun = b.id_akun;
							}
							if(b.id_program != null){
								program = b.id_program;
							}
							if(b.id_giat != null){
								giat = b.id_giat;
							}
							if(b.id_sub_giat != null){
								sub_giat = b.id_sub_giat;
							}
							if(b.nilai != null){
								nilai = b.nilai;
							}
                		html += ''
            			+'<tr>'
            				+'<td>'+b.idDetailSpd+'</td>'
            				+'<td>'+akun+'</td>'
            				+'<td>'+program+'</td>'
            				+'<td>'+giat+'</td>'
            				+'<td>'+sub_giat+'</td>'
            				+'<td class="text-right">'+nilai+'</td>'
            			+'</tr>';
                	});
                	jQuery('#table-data-spd').DataTable().clear();
                	jQuery('#table-data-spd tbody').html(html);
                	jQuery('#table-data-spd').dataTable();
                    jQuery('#showspd').modal('show');
                } else {
                    alert(res.message);
                }
                jQuery('#wrap-loading').hide();
            }
        });
    }
</script>