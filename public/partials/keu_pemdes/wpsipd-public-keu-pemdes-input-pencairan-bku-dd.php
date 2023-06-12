<?php
global $wpdb;
$idtahun = $wpdb->get_results("select distinct tahun_anggaran from data_unit", ARRAY_A);
$tahun = "<option value='-1'>Pilih Tahun</option>";
foreach($idtahun as $val){
    $tahun .= "<option value='$val[tahun_anggaran]'>$val[tahun_anggaran]</option>";
}

$user_id = um_user( 'ID' );
$user_meta = get_userdata($user_id);
$disabled = 'disabled';
if(in_array("administrator", $user_meta->roles)){
    $disabled = '';
}

// print_r($total_pencairan); die($wpdb->last_query);
?>
<style type="text/css">
    .wrap-table{
        overflow: auto;
        max-height: 100vh; 
        width: 100%; 
}
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="cetak">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
    <h1 class="text-center" style="margin:3rem;">Pencairan BKU Dana Desa ( DD )</h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_data_pencairan_bku_dd();"><i class="dashicons dashicons-plus"></i> Tambah Data</button>
        </div>
        <div class="wrap-table">
        <table id="management_data_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Tahun Anggaran</th>
                    <th class="text-center">Kecamatan</th>
                    <th class="text-center">Desa</th>
                    <th class="text-center">Pagu Anggaran</th>
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        </div>
    </div>          
</div>

<div class="modal fade mt-4" id="modalTambahDataPencairanBKUDD" tabindex="-1" role="dialog" aria-labelledby="modalTambahDataPencairanBKUDDLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataPencairanBKUDDLabel">Data Pencairan BKU Anggaran Dana Desa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type='hidden' id='id_data' name="id_data" placeholder=''>
                <div class="form-group">
                    <label>Tahun Anggaran</label>
                    <select class="form-control" id="tahun" onchange="get_bku_dd();">
                        <?php echo $tahun ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pilih Kecamatan</label>
                    <select class="form-control" id="kec" onchange="get_desa();">
                    </select>
                </div>
                <div class="form-group">
                    <label>Pilih Desa</label>
                    <select class="form-control" id="desa" onchange="get_pagu();">
                    <input type="hidden" class="form-control" id="id_bku_dd" />
                    </select>
                </div>
                <div class="form-group">
                    <label>Validasi Pagu Anggaran</label>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Pagu Anggaran</th>
                                <th class="text-center" style="width: 30%;">Total Pencairan</th>
                                <th class="text-center" style="width: 30%;">Sisa</th>
                            </tr>
                        </thead>
                        <tbody id="validasi_pagu"></tbody>
                    </table>
                </div>
                <div class="form-group">
                    <label>Pagu Anggaran</label>
                    <input type="number" class="form-control" id="pagu_anggaran" onchange="validasi_pagu();" />
                </div>
                <div class="form-group">
                    <label for="">Keterangan Pencairan</label>
                    <textarea class="form-control" id="keterangan"></textarea>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" value="1" type="checkbox" id="status_pagu" onclick="set_keterangan(this);" <?php echo $disabled; ?>>
                    <label class="form-check-label" for="status_pagu">Disetujui</label>
                </div>
                <div class="form-group" style="display:none;">
                    <label>Keterangan ditolak</label>
                    <textarea class="form-control" id="keterangan_status_pagu" <?php echo $disabled; ?>></textarea>
                </div> 
                <button type="submit" onclick="submitTambahDataFormPencairanBKUDD();" class="btn btn-primary send_data">Kirim</button>
                 <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Tutup</button>
            </form>
        </div>
    </div>
</div>   
<script>    
jQuery(document).ready(function(){
    get_data_pencairan_bku_dd();
});

function set_keterangan(that){
    var id = jQuery(that).attr('id');
    if(jQuery(that).is(':checked')){
        jQuery('#keterangan_'+id).closest('.form-group').hide();
    }else{
        jQuery('#keterangan_'+id).closest('.form-group').show();
    }
}

function get_data_pencairan_bku_dd(){
    if(typeof datapencairan_bku_dd == 'undefined'){
        window.datapencairan_bku_dd = jQuery('#management_data_table').on('preXhr.dt', function(e, settings, data){
            jQuery("#wrap-loading").show();
        }).DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                data:{
                    'action': 'get_datatable_data_pencairan_bku_dd',
                    'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
                }
            },
            lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
            order: [[0, 'asc']],
            "drawCallback": function( settings ){
                jQuery("#wrap-loading").hide();
            },
            "columns": [
                {
                    "data": 'tahun_anggaran',
                    className: "text-center"
                },
                {
                    "data": 'kecamatan',
                    className: "text-center"
                },
                {
                    "data": 'desa',
                    className: "text-center"
                },
                {
                    "data": 'total_pencairan',
                    className: "text-center"
                },
                {
                    "data": 'keterangan',
                    className: "text-center"
                },
                {
                    "data": 'status',
                    className: "text-center"
                },
                {
                    "data": 'aksi',
                    className: "text-center"
                }
            ]
        });
    }else{
        datapencairan_bku_dd.draw();
    }
}

function hapus_data(id){
    let confirmDelete = confirm("Apakah anda yakin akan menghapus data ini?");
    if(confirmDelete){
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type:'post',
            data:{
                'action' : 'hapus_data_pencairan_bku_dd_by_id',
                'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
                'id'     : id
            },
            dataType: 'json',
            success:function(response){
                jQuery('#wrap-loading').hide();
                if(response.status == 'success'){
                    get_data_pencairan_bku_dd(); 
                }else{
                    alert(`GAGAL! \n${response.message}`);
                }
            }
        });
    }
}

function edit_data(_id){
    jQuery('#wrap-loading').show();
    jQuery.ajax({
        method: 'post',
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        dataType: 'json',
        data:{
            'action': 'get_data_pencairan_bku_dd_by_id',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id': _id,
        },
        success: function(res){
            if(res.status == 'success'){
                jQuery('#id_data').val(res.data.id).prop('disabled', false);
                jQuery('#tahun').val(res.data.tahun_anggaran).prop('disabled', false);
                get_bku_dd()
                .then(function(){
                    jQuery('#kec').val(res.data.kecamatan).trigger('change').prop('disabled', false);
                    jQuery('#desa').val(res.data.desa).prop('disabled', false);
                    jQuery('#id_bku_dd').val(res.data.id_bku_dd).prop('disabled', false);
                    jQuery('#validasi_pagu').closest('.form-group').show().prop('disabled', false);
                    jQuery('#pagu_anggaran').val(res.data.total_pencairan).prop('disabled', false);
                    if(res.data.status_ver_total == 0){
                        jQuery('#keterangan_status_pagu').closest('.form-group').show().prop('disabled', false);
                        jQuery('#status_pagu').prop('checked', false);
                    }else{
                        jQuery('#keterangan_status_pagu').closest('.form-group').hide().prop('disabled', false);
                        jQuery('#status_pagu').prop('checked', true);
                    }
                    jQuery('#keterangan_status_pagu').val(res.data.ket_ver_total).prop('disabled', false);
                    jQuery('#keterangan').val(res.data.keterangan).prop('disabled', false);
                    jQuery('#status_pagu').closest('.form-check').show().prop('disabled', false);
                    jQuery('#modalTambahDataPencairanBKUDD .send_data').show();
                    jQuery('#modalTambahDataPencairanBKUDD').modal('show');
                })
            }else{
                alert(res.message);
                jQuery('#wrap-loading').hide();
            }
        }
    });
}

function detail_data(_id){
    jQuery('#wrap-loading').show();
    jQuery.ajax({
        method: 'post',
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        dataType: 'json',
        data:{
            'action': 'get_data_pencairan_bku_dd_by_id',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id': _id,
        },
        success: function(res){
            if(res.status == 'success'){
                jQuery('#id_data').val(res.data.id).prop('disabled', true);
                jQuery('#tahun').val(res.data.tahun_anggaran).prop('disabled', true);
                get_bku_dd()
                .then(function(){
                    jQuery('#kec').val(res.data.kecamatan).trigger('change').prop('disabled', true);
                    jQuery('#desa').val(res.data.desa).trigger('change').prop('disabled', true);
                    jQuery('#uraian_kegiatan').val(res.data.kegiatan).trigger('change').prop('disabled', true);
                    jQuery('#id_bku_dd').val(res.data.id_bku_dd).prop('disabled', true);
                    jQuery('#alamat').val(res.data.alamat).prop('disabled', true);
                    jQuery('#pagu_anggaran').val(res.data.total_pencairan).prop('disabled', true);
                    if(res.data.status_ver_total == 0){
                        jQuery('#keterangan_status_pagu').closest('.form-group').show().prop('disabled', true);
                        jQuery('#status_pagu').prop('checked', false).prop('disabled', true);
                    }else{
                        jQuery('#keterangan_status_pagu').closest('.form-group').hide().prop('disabled', true);
                        jQuery('#status_pagu').prop('checked', true).prop('disabled', true);
                    }
                    jQuery('#keterangan_status_pagu').val(res.data.ket_ver_total).prop('disabled', true);
                    jQuery('#keterangan').val(res.data.keterangan).prop('disabled', true);
                    jQuery('#status_pagu').closest('.form-check').show().prop('disabled', true);
                    jQuery('#modalTambahDataPencairanBKUDD .send_data').hide();
                    jQuery('#modalTambahDataPencairanBKUDD').modal('show');
                })
            }else{
                alert(res.message);
                jQuery('#wrap-loading').hide();
            }
        }
    });
}

//show tambah data
function tambah_data_pencairan_bku_dd(){
    jQuery('#id_data').val('').prop('disabled', false);
    jQuery('#tahun').val('').prop('disabled', false);
    jQuery('#kec').val('').prop('disabled', false);
    jQuery('#desa').val('').prop('disabled', false);
    jQuery('#validasi_pagu').html('');
    jQuery('#pagu_anggaran').val('').prop('disabled', false);
    jQuery('#keterangan').val('').prop('disabled', false);
    jQuery('#status_pagu').closest('.form-check').hide().prop('disabled', false);
    jQuery('#keterangan_status_pagu').closest('.form-group').hide().prop('disabled', false);
    jQuery('#status_pagu').prop('checked', false);
    jQuery('#keterangan_status_pagu').val('').prop('disabled', false);
    jQuery('#modalTambahDataPencairanBKUDD .send_data').show();
    jQuery('#modalTambahDataPencairanBKUDD').modal('show');
}

function submitTambahDataFormPencairanBKUDD(){
    var id_data = jQuery('#id_data').val();
    var desa = jQuery('#desa').val();
    if(desa == ''){
        return alert('Pilih Desa Dulu!');
    }
    var kec = jQuery('#kec').val();
    if(kec == ''){
        return alert('Pilih Kecamatan Dulu!');
    }
    var tahun = jQuery('#tahun').val();
    if(tahun == ''){
        return alert('Pilih Tahun Dulu!');
    }
    var id_bku_dd = jQuery('#id_bku_dd').val();
    var pagu_anggaran = jQuery('#pagu_anggaran').val();
    if(pagu_anggaran == ''){
        return alert('Pilih Pagu Anggaran Dulu!');
    }
    var status_pagu = jQuery('#status_pagu').val();
    if(jQuery('#status_pagu').is(':checked') == false){
        status_pagu = 0;
    }
    var keterangan_status_pagu = jQuery('#keterangan_status_pagu').val();
    var keterangan = jQuery('#keterangan').val();
    if(keterangan == ''){
        // return alert('Isi keterangan Dulu!');
    }

    jQuery('#wrap-loading').show();
    jQuery.ajax({
        method: 'post',
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        dataType: 'json',
        data:{
            'action': 'tambah_data_pencairan_bku_dd',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id_data': id_data,
            'id_bku_dd': id_bku_dd,
            'pagu_anggaran': pagu_anggaran,
            'status_pagu': status_pagu,
            'keterangan_status_pagu': keterangan_status_pagu,
            'keterangan': keterangan,
        },
        success: function(res){
            alert(res.message);
            jQuery('#modalTambahDataPencairanBKUDD').modal('hide');
            if(res.status == 'success'){
                get_data_pencairan_bku_dd();
            }else{
                jQuery('#wrap-loading').hide();
            }
        }
    });
}
 function get_bku_dd(){
    return new Promise(function(resolve, reject){
        var tahun = jQuery('#tahun').val();
        if(tahun == '' || tahun == '-1'){
            alert('Pilih tahun anggaran dulu!');
            return resolve();
        }
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: "<?php echo admin_url('admin-ajax.php'); ?>",
            type:"post",
            data:{
                'action' : "get_pemdes_bku_dd",
                'api_key' : jQuery("#api_key").val(),
                'tahun_anggaran' : tahun,
            },
            dataType: "json",
            success:function(response){
                window.data_pemdes = response.data;
                window.kecamatan_all = {};
                data_pemdes.map(function(b, i){
                    if(!kecamatan_all[b.kecamatan]){
                        kecamatan_all[b.kecamatan] = {};
                    }
                    if(!kecamatan_all[b.kecamatan][b.desa]){
                        kecamatan_all[b.kecamatan][b.desa] = {};
                    }
                    if(!kecamatan_all[b.kecamatan][b.desa][b.kegiatan]){
                        kecamatan_all[b.kecamatan][b.desa][b.kegiatan] = {};
                    }
                    if(!kecamatan_all[b.kecamatan][b.desa][b.kegiatan][b.alamat]){
                        kecamatan_all[b.kecamatan][b.desa][b.kegiatan][b.alamat] = [];
                    }
                    kecamatan_all[b.kecamatan][b.desa][b.kegiatan][b.alamat].push(b);
                });
                var kecamatan = '<option value="-1">Pilih Kecamatan</option>';
                for(var i in kecamatan_all){
                    kecamatan += '<option value="'+i+'">'+i+'</option>';
                }
                jQuery('#kec').html(kecamatan);
                jQuery('#wrap-loading').hide();
                return resolve();
            }
        });
    })
}

function get_desa() {
    var kec = jQuery('#kec').val();
    if(kec == '' || kec == '-1'){
        return alert('Pilih kecamatan dulu!');
    }
    var desa = '<option value="-1">Pilih Desa</option>';
    for(var ii in kecamatan_all[kec]){
        desa += '<option value="'+ii+'">'+ii+'</option>';
    }
    jQuery('#desa').html(desa);
}

function get_kegiatan() {
    var kec = jQuery('#kec').val();
    if(kec == '' || kec == '-1'){
        return alert('Pilih kecamatan dulu!');
    }
    var desa = jQuery('#desa').val();
    if(desa == '' || desa == '-1'){
        return alert('Pilih desa dulu!');
    }
    var kegiatan = '<option value="-1">Pilih Kegiatan</option>';
    for(var iii in kecamatan_all[kec][desa]){
        kegiatan += '<option value="'+iii+'">'+iii+'</option>';
    }
    jQuery('#uraian_kegiatan').html(kegiatan);
}

function get_alamat() {
    var kec = jQuery('#kec').val();
    if(kec == '' || kec == '-1'){
        return alert('Pilih kecamatan dulu!');
    }
    var desa = jQuery('#desa').val();
    if(desa == '' || desa == '-1'){
        return alert('Pilih desa dulu!');
    }
    var kegiatan = jQuery('#uraian_kegiatan').val();
    if(kegiatan == '' || kegiatan == '-1'){
        return alert('Pilih kegiatan dulu!');
    }
    var alamat = '<option value="-1">Pilih alamat</option>';
    for(var iiii in kecamatan_all[kec][desa][kegiatan]){
        alamat += '<option value="'+iiii+'">'+iiii+'</option>';
    }
    jQuery('#alamat').html(alamat);
}

function get_pagu() {
    var kec = jQuery('#kec').val();
    if(kec == '' || kec == '-1'){
        return alert('Pilih kecamatan dulu!');
    }
    var desa = jQuery('#desa').val();
    if(desa == '' || desa == '-1'){
        return alert('Pilih desa dulu!');
    }
    var kegiatan = jQuery('#uraian_kegiatan').val();
    if(kegiatan == '' || kegiatan == '-1'){
        return alert('Pilih kegiatan dulu!');
    }
    var alamat = jQuery('#alamat').val();
    if(alamat == '' || alamat == '-1'){
        return alert('Pilih alamat dulu!');
    }
    jQuery('#wrap-loading').show();
    var pagu = kecamatan_all[kec][desa][kegiatan][alamat][0].total;
    var id = kecamatan_all[kec][desa][kegiatan][alamat][0].id;
    var tahun = kecamatan_all[kec][desa][kegiatan][alamat][0].tahun_anggaran;
    jQuery.ajax({
        url: "<?php echo admin_url('admin-ajax.php'); ?>",
        type:"post",
        data:{
            'action' : "get_pencairan_pemdes_bku_dd",
            'api_key' : jQuery("#api_key").val(),
            'tahun_anggaran' : tahun,
            'id' : id,
        },
        dataType: "json",
        success:function(response){
            if(response.status == 'success'){
                var total_pencairan = +response.total_pencairan;
                window.global_sisa = pagu-total_pencairan;
                var tbody = ''
                    +'<tr>'
                        +'<td class="text-right">'+formatRupiah(pagu)+'</td>'
                        +'<td class="text-right">'+formatRupiah(total_pencairan)+'</td>'
                        +'<td class="text-right">'+formatRupiah(global_sisa)+'</td>'
                    +'</tr>';
                jQuery('#validasi_pagu').html(tbody);
                jQuery('#id_bku_dd').val(id);
                jQuery('#pagu_anggaran').val(global_sisa);
            }else{
                alert(response.message);
            }
            jQuery('#wrap-loading').hide();
        }
    });
}

function validasi_pagu(){
    var pagu = jQuery('#pagu_anggaran').val();
    if(pagu > global_sisa){
        alert('Nilai pencairan tidak boleh lebih besar dari sisa anggaran!');
        jQuery('#pagu_anggaran').val(global_sisa);
    }
}
</script>