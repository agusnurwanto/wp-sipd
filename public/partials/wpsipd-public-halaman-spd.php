<?php
// If this file is called directly, abort.
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
if (!empty($get_spd)) {
	foreach ($get_spd as $k => $v) {
		$url_no_spd = '<a  onclick="show_spd('.$v['idSpd'].'); return false;" href="#">'.$v['nomorSpd'].'</a>';
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
			<tfoot>
				<th colspan="4" class="text-center">Total</th>
                <th class="text-right"><?php echo number_format($total_all_spd,0,",","."); ?></th>
			</tfoot>
		</table>
	</div>
</div>

<div class="modal fade mt-4" id="modalShowSPD" tabindex="-1" role="dialog" aria-labelledby="modalShowSPDLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalShowSPDLabel">Data Surat Penyediaan Dana</h5>
            </div>
            <div class="modal-body">
                <input type='hidden' id='id_data' name="id_data" placeholder=''>
                <div class="form-group">
                    <label>ID SPD</label>
                    <input type="text" class="form-control" id="id_spd">
                </div>
                <div class="form-group">
                    <label>Nomor SPD</label>
                    <input type="text" class="form-control" id="nomor_spd">
                </div>
                <div class="form-group">
                    <label>Keterangan SPD</label>
                    <input type="text" class="form-control" id="nomor_spd">
                </div>
                <div class="form-group">
                    <label>Nama Akun</label>
                    <input type="text" class="form-control" id="nama_akun">
                </div>
                <div class="form-group">
                    <label>Nama Program</label>
                    <input type="text" class="form-control" id="nama_program">
                </div>
                <div class="form-group">
                    <label>Nama Kegiatan</label>
                    <input type="text" class="form-control" id="nama_giat">
                </div>
                <div class="form-group">
                    <label>Nama Sub Kegiatan</label>
                    <input type="text" class="form-control" id="nama_sub_giat">
                </div>
                <div class="form-group">
                    <label>Ketentuan Lainnya</label>
                    <input type="text" class="form-control" id="nomor_spd">
                </div>
                <div class="form-group">
                    <label>Detail SPD</label>
                    <input type="text" class="form-control" id="nomor_spd">
                </div>
                <div class="form-group">
                    <label>Total SPD</label>
                    <input type="text" class="form-control" id="nomor_spd">
                </div>
                <div class="form-group">
                    <label>Nilai</label>
                    <input type="text" class="form-control" id="nomor_spd">
                </div>
	            <div class="modal-footer">
	                <button type="submit" class="components-button btn btn-danger" data-dismiss="modal">Tutup</button>
	            </div>
            </div>
        </div>
    </div>
</div>
<script>

    function show_spd(_id) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_data_spd',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'id': _id,
            },
            success: function(res) {
                if (res.status == 'success') {
                    jQuery('#modalShowSPD').modal('show');
                } else {
                    alert(res.message);
                }
                jQuery('#wrap-loading').hide();
            }
        });
    }
</script>