<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$input = shortcode_atts( array(
	'tahun_anggaran' => '2022'
), $atts );

global $wpdb;
?>
<div class="cetak">
	<div style="padding: 10px;margin:0 0 3rem 0;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
		<h1 id="judul" class="text-center" style="margin:3rem;">Register Surat Perintah Pencairan Dana (SP2D) Cair FMIS<br>Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
		<table id="data_table" class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center" rowspan="2">No Urut</th>
					<th class="text-center" rowspan="2">Tanggal Terbit SP2D</th>
					<th class="text-center" rowspan="2">No. SP2D</th>
					<th class="text-center" rowspan="2">No. SPM</th>
					<th class="text-center" rowspan="2">OPD</th>
					<th class="text-center" rowspan="2">Keperluan/Ket. SP2D</th>
					<th class="text-center" rowspan="2">Kode Rekening</th>
					<th class="text-center" rowspan="2">Uraian SP2D</th>
					<th class="text-center" rowspan="2">Jumlah Kotor</th>
					<th class="text-center" colspan="2">Potongan</th>
					<th class="text-center" rowspan="2">Jumlah Bersih</th>
					<th class="text-center" rowspan="2">Bendahara Pengeluaran/Rekanan</th>
					<th class="text-center" rowspan="2">Nomor Rekening Penerima</th>
					<th class="text-center" rowspan="2">Tanggal Cair SP2D</th>
				</tr>
				<tr>
					<th class="text-center">PPn</th>
					<th class="text-center">PPh</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){	
		globalThis.tahun = <?php echo $input['tahun_anggaran']; ?>;
		get_data_register_sp2d_fmis(tahun);
	});

	function get_data_register_sp2d_fmis(tahun){
		jQuery('#data_table').on('preXhr.dt', function ( e, settings, data ) {
			jQuery("#wrap-loading").show();
	    } ).DataTable({
			"processing": true,
    		"serverSide": true,
	        "ajax": {
	        	url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:"post",
				data:{
					'action': "get_data_register_sp2d_fmis",
					'api_key': jQuery("#api_key").val(),
					'tahun_anggaran': tahun
				}
			},
			"drawCallback": function( settings ){
				jQuery("#wrap-loading").hide();
			},
	        dom: 'lBfrtip',
	        buttons: [
	            'copy', 'csv', 'excel', 'pdf', 'print'
	        ],
			lengthMenu: [
				[20, 100, 500, -1], 
				[20, 100, 500, "All"]
			]
	    });
	}
</script>