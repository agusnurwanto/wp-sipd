<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2023'
), $atts );

$select_sd = "<option value=''>Pilih Sumber Dana</option>";
$sd = $wpdb->get_results($wpdb->prepare("
	SELECT 
		id_dana,
		kode_dana,
		nama_dana
	FROM data_sumber_dana
	WHERE active=1
		AND tahun_anggaran=%d
", $input['tahun_anggaran']), ARRAY_A);
foreach($sd as $val){
	$select_sd .= "<option value='$val[id_dana]' kode_dana='$val[kode_dana]' nama_dana='$val[nama_dana]'>$val[kode_dana] $val[nama_dana]</option>";
}

?>
<div style="padding: 10px; margin:0 0 3rem 0;">
	<input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
	<h1 class="text-center" style="margin:3rem;">Halaman Setting Batasan Pagu Sumber Dana <?php echo $input['tahun_anggaran']; ?></h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_data_batasan_pagu();"><i class="dashicons dashicons-plus"></i> Tambah Data</button>
        </div>
	<table id="data_sumberdana_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
		<thead id="data_header">
			<tr>
				<th class="text-center">Kode Sumber Dana</th>
				<th class="text-center">Nama Sumber Dana</th>
				<th class="text-center">Batasan Pagu</th>
				<th class="text-center">Pagu Terpakai</th>
				<th class="text-center">Keterangan</th>
				<th class="text-center" style="width: 250px;">Aksi</th>
			</tr>
		</thead>
		<tbody id="data_body">
		</tbody>
	</table>
</div>

<div class="modal fade mt-4" id="modalTambahDataBatasanPagu" tabindex="-1" role="dialog" aria-labelledby="modalTambahDataBatasanPaguLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataBatasanPaguLabel">Data Batasan Pagu Sumber Dana</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type='hidden' id='id_data' name="id_data" placeholder=''>
                <input type='hidden' id='id_dana' name="id_dana" placeholder=''>
                <div class="form-group">
                    <label for='nama_dana'>Sumber Dana</label>
                    <select id='sumber_dana' class="form-control"><?php echo $select_sd; ?></select>
                </div>
                <div class="form-group">
                    <label for='nilai_batasan'>Batasan Pagu</label>
                    <input type="text" id='nilai_batasan' name="nilai_batasan" class="form-control niai_pagu" placeholder=''/>
                </div>
                <div class="form-group">
                    <label for='keterangan'>Keterangan</label>
                    <input type="text" id='keterangan' name="keterangan" class="form-control" placeholder=''/>
                </div>
            </div> 
            <div class="modal-footer">
                <button class="btn btn-primary submitBtn" onclick="submitTambahDataBatasanPagu()">Simpan</button>
                <button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>    
jQuery(document).ready(function(){
    get_data_batasan_pagu_sumberdana();
    jQuery('#sumber_dana').select2({
    	width: '100%',
    	dropdownParent: jQuery('#modalTambahDataBatasanPagu .modal-content')
    });
	    jQuery('.niai_pagu').on('input', function() {
	        var sanitized = jQuery(this).val().replace(/[^0-9]/g, '');
	        var formatted = formatRupiah(sanitized);
	        jQuery(this).val(formatted);
	    });
});
function get_data_batasan_pagu_sumberdana (){
    if(typeof databatasanpagu == 'undefined'){
        window.databatasanpagu = jQuery('#data_sumberdana_table').on('preXhr.dt', function(e, settings, data){
            jQuery("#wrap-loading").show();
        }).DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                data:{
                    'action': 'get_batasan_pagu_sumberdana',
                    'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
                    'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>
                }
            },
            lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
            order: [[0, 'asc']],
            "drawCallback": function( settings ){
                jQuery("#wrap-loading").hide();
            },
            columnDefs: [
            	{orderable: false, targets: 3},
            	{orderable: false, targets: 4},
            	{orderable: false, targets: 5}
            ],
            "columns": [
                {
                    "data": 'kode_dana',
                    className: "text-left"
                },
                {
                    "data": 'nama_dana',
                    className: "text-left"
                },
                {
                    "data": 'nilai_batasan',
                    className: "text-right"
                },
                {
                    "data": 'pagu_terpakai',
                    className: "text-right"
                },
                {
                    "data": 'keterangan',
                    className: "text-left"
                },
                {
                    "data": 'aksi',
                    className: "text-center"
                }
            ]
        });
    }else{
        databatasanpagu.draw();
    }
}

function hapus_batasan_pagu(id){
        let confirmDelete = confirm("Apakah anda yakin akan menghapus data ini?");
        if(confirmDelete){
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type:'post',
                data:{
                    'action' : 'hapus_data_batasan_pagu_by_id',
                    'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
                    'id'     : id
                },
                dataType: 'json',
                success:function(response){
                    jQuery('#wrap-loading').hide();
                    if(response.status == 'success'){
                        get_data_batasan_pagu_sumberdana(); 
                    }else{
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }
function formatRupiah(angka) {
    var reverse = angka.toString().split('').reverse().join('');
    var thousands = reverse.match(/\d{1,3}/g);
    var formatted = thousands.join('.').split('').reverse().join('');
    return formatted;
}
function edit_batasan_pagu(_id){
    jQuery('#wrap-loading').show();
    jQuery.ajax({
        method: 'post',
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        dataType: 'json',
        data:{
            'action': 'get_data_batasan_pagu_sumberdana_by_id',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id': _id,
        },
        success: function(res){
            if(res.status == 'success'){
                jQuery('#id_data').val(res.data.id);
                jQuery('#sumber_dana').val(res.data.id_dana).trigger('change');
                jQuery('#nilai_batasan').val(res.data.nilai_batasan).trigger('input');
                jQuery('#keterangan').val(res.data.keterangan);
                jQuery('#modalTambahDataBatasanPagu').modal('show');
            }else{
                alert(res.message);
            }
            jQuery('#wrap-loading').hide();
        }
    });
}

//show tambah data
function tambah_data_batasan_pagu(){
    jQuery('#id_data').val('');
    jQuery('#kode_dana').val('');
    jQuery('#nama_dana').val('');
    jQuery('#nilai_batasan').val('').trigger('change');
    jQuery('#pagu_terpakai').val('');
    jQuery('#keterangan').val('');
    jQuery('#modalTambahDataBatasanPagu').modal('show');
}

function submitTambahDataBatasanPagu(){
    var id_data = jQuery('#id_data').val();
    var id_dana = jQuery('#sumber_dana').val();
    if(id_dana == ''){
        return alert('Sumber Dana tidak boleh kosong!');
    }
    var kode_dana = jQuery('#sumber_dana option:selected').attr('kode_dana');
    var nama_dana = jQuery('#sumber_dana option:selected').attr('nama_dana');
    var nilai_batasan = jQuery('#nilai_batasan').val().replace(/\./g, '');
    if(nilai_batasan == ''){
        return alert('Batasan Pagu tidak boleh kosong!');
    }
    var keterangan = jQuery('#keterangan').val();

    jQuery('#wrap-loading').show();
    jQuery.ajax({
        method: 'post',
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        dataType: 'json',
        data:{
            'action': 'tambah_data_batasan_pagu_by_id',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id_data': id_data,
            'kode_dana': kode_dana,
            'nama_dana': nama_dana,
            'nilai_batasan': nilai_batasan,
            'keterangan': keterangan,
            'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
            'id_dana': id_dana
        },
        success: function(res){
            alert(res.message);
            if(res.status == 'success'){
            	jQuery('#modalTambahDataBatasanPagu').modal('hide');
                get_data_batasan_pagu_sumberdana();
            }else{
                jQuery('#wrap-loading').hide();
            }
        }
    });
}
</script>