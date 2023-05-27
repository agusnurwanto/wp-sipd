<?php
global $wpdb;
$idtahun = $wpdb->get_results("select distinct tahun_anggaran from data_unit", ARRAY_A);
$tahun = "<option value='-1'>Pilih Tahun</option>";
foreach($idtahun as $val){
    $tahun .= "<option value='$val[tahun_anggaran]'>$val[tahun_anggaran]</option>";
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
    <h1 class="text-center" style="margin:3rem;">Pencairan Bantuan Keuangan Khusus (BKK)</h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_data_pencairan_bkk();"><i class="dashicons dashicons-plus"></i> Tambah Data</button>
        </div>
        <div class="wrap-table">
        <table id="management_data_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Tahun Anggaran</th>
                    <th class="text-center">Kecamatan</th>
                    <th class="text-center">Desa</th>
                    <th class="text-center">Uraian Kegiatan</th>
                    <th class="text-center">Alamat</th>
                    <th class="text-center">Pagu Anggaran</th>
                    <th class="text-center">Proposal BKK</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        </div>
    </div>          
</div>

<div class="modal fade mt-4" id="modalTambahDataPencairanBKK" tabindex="-1" role="dialog" aria-labelledby="modalTambahDataPencairanBKKLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataPencairanBKKLabel">Data Pencairan BKK</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type='hidden' id='id_data' name="id_data" placeholder=''>
                <div class="form-group">
                    <label>Tahun Anggaran</label>
                    <select class="form-control" id="tahun" onchange="get_bkk();">
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
                    <select class="form-control" id="desa" onchange="get_kegiatan();">
                    </select>
                </div>
                <div class="form-group">
                    <label>Uraian Kegiatan</label>
                    <select class="form-control" id="uraian_kegiatan" onchange="get_alamat();">
                    </select>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <select class="form-control" id="alamat" onchange="get_pagu();">
                    </select>
                    <input type="hidden" class="form-control" id="id_kegiatan" />
                </div>
                <div class="form-group">
                    <label>Pagu Anggaran</label>
                    <input type="number" class="form-control" id="pagu_anggaran" />
                </div>
                <div class="form-group">
                    <label for="">Proposal BKK Infrastruktur</label>
                    <input type="file" class="form-control-file" id="proposal">
                </div>
                  <button type="submit" onclick="submitTambahDataFormPencairanBKK();" class="btn btn-primary">Kirim</button>
                  <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Tutup</button>
            </form>
        </div>
    </div>
</div>   
<script>    
jQuery(document).ready(function(){
    get_data_pencairan_bkk();
});

function get_data_pencairan_bkk(){
    if(typeof datapencairan_bkk == 'undefined'){
        window.datapencairan_bkk = jQuery('#management_data_table').on('preXhr.dt', function(e, settings, data){
            jQuery("#wrap-loading").show();
        }).DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                data:{
                    'action': 'get_datatable_data_pencairan_bkk',
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
                    "data": 'kegiatan',
                    className: "text-center"
                },
                {
                    "data": 'alamat',
                    className: "text-center"
                },
                {
                    "data": 'total_pencairan',
                    className: "text-center"
                },
                {
                    "data": 'file_proposal',
                    className: "text-center"
                },
                {
                    "data": 'aksi',
                    className: "text-center"
                }
            ]
        });
    }else{
        datapencairan_bkk.draw();
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
                'action' : 'hapus_data_pencairan_bkk_by_id',
                'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
                'id'     : id
            },
            dataType: 'json',
            success:function(response){
                jQuery('#wrap-loading').hide();
                if(response.status == 'success'){
                    get_data_pencairan_bkk(); 
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
            'action': 'get_data_pencairan_bkk_by_id',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id': _id,
        },
        success: function(res){
            if(res.status == 'success'){
                jQuery('#id_data').val(res.data.id);
                jQuery('#tahun').val(res.data.tahun_anggaran);
                get_bkk()
                .then(function(){
                    jQuery('#kec').val(res.data.kecamatan).trigger('change');
                    jQuery('#desa').val(res.data.desa).trigger('change');
                    jQuery('#uraian_kegiatan').val(res.data.kegiatan).trigger('change');
                    jQuery('#alamat').val(res.data.alamat);
                    jQuery('#pagu_anggaran').val(res.data.total_pencairan);
                    jQuery('#modalTambahDataPencairanBKK').modal('show');
                })
            }else{
                alert(res.message);
                jQuery('#wrap-loading').hide();
            }
        }
    });
}

//show tambah data
function tambah_data_pencairan_bkk(){
    jQuery('#id_data').val('');
    jQuery('#tahun').val('');
    jQuery('#kec').val('');
    jQuery('#desa').val('');
    jQuery('#uraian_kegiatan').val('');
    jQuery('#alamat').val('');
    jQuery('#pagu_anggaran').val('');
    jQuery('#proposal').val('');
    jQuery('#modalTambahDataPencairanBKK').modal('show');
}

function submitTambahDataFormPencairanBKK(){
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
    var alamat = jQuery('#alamat').val();
    if(alamat == ''){
        return alert('Pilih Alamat Dulu!');
    }
    var id_kegiatan = jQuery('#id_kegiatan').val();
    if(id_kegiatan == ''){
        return alert('Pilih Kegiatan Dulu!');
    }
    var pagu_anggaran = jQuery('#pagu_anggaran').val();
    if(pagu_anggaran == ''){
        return alert('Pilih Pagu Anggaran Dulu!');
    }
    var proposal = jQuery('#proposal').val();
    if(proposal == ''){
        // return alert('Isi Proposal Dulu!');
    }

    jQuery('#wrap-loading').show();
    jQuery.ajax({
        method: 'post',
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        dataType: 'json',
        data:{
            'action': 'tambah_data_pencairan_bkk',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id_data': id_data,
            'id_kegiatan': id_kegiatan,
            'pagu_anggaran': pagu_anggaran,
        },
        success: function(res){
            alert(res.message);
            jQuery('#modalTambahDataPencairanBKK').modal('hide');
            if(res.status == 'success'){
                get_data_pencairan_bkk();
            }else{
                jQuery('#wrap-loading').hide();
            }
        }
    });
}
 function get_bkk(){
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
                'action' : "get_pemdes_bkk",
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
    jQuery('#id_kegiatan').val(kecamatan_all[kec][desa][kegiatan][alamat][0].id);
    jQuery('#pagu_anggaran').val(kecamatan_all[kec][desa][kegiatan][alamat][0].total);
}

</script>