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
    <h1 class="text-center" style="margin:3rem;">Manajemen Data BKK Infrastruktur</h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_data_bkk_infrastruktur();"><i class="dashicons dashicons-plus"></i> Tambah Data</button>
        </div>
        <div class="wrap-table">
        <table id="management_data_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Id Kecamatan</th>
                    <th class="text-center">Id Desa</th>
                    <th class="text-center">Kecamatan</th>
                    <th class="text-center">Desa</th>
                    <th class="text-center">Kegiatan</th>
                    <th class="text-center">Alamat</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Id Dana</th>
                    <th class="text-center">Sumber Dana</th>
                    <th class="text-center">Tahun Anggaran</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        </div>
    </div>          
</div>

<div class="modal fade mt-4" id="modalTambahDataBKKInfrastruktur" tabindex="-1" role="dialog" aria-labelledby="modalTambahDataBKKInfrastrukturLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataBKKInfrastrukturLabel">Data BKK Infrastruktur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type='hidden' id='id_data' name="id_data" placeholder=''>
                <div class="form-group">
                    <label for='id_kecamatan' style='display:inline-block'>Id Kecamatan</label>
                    <input type="text" id='id_kecamatan' name="id_kecamatan" class="form-control" placeholder=''/>
                </div>
                <div class="form-group">
                    <label for='id_desa' style='display:inline-block'>Id Desa</label>
                    <input type='text' id='id_desa' name="id_desa" class="form-control" placeholder=''>
                </div>
                <div class="form-group">
                    <label for='kecamatan' style='display:inline-block'>Kecamatan</label>
                    <input type="text" id='kecamatan' name="kecamatan" class="form-control" placeholder=''/>
                </div>
                <div class="form-group">
                    <label for='desa' style='display:inline-block'>Desa</label>
                    <input type="text" id='desa' name="desa" class="form-control" placeholder=''/>
                </div>
                <div class="form-group">
                    <label for='kegiatan' style='display:inline-block'>Kegiatan</label>
                    <input type='text' id='kegiatan' name="kegiatan" class="form-control" placeholder=''>
                </div> 
                <div class="form-group">
                    <label for='alamat' style='display:inline-block'>Alamat</label>
                    <input type="text" id='alamat' name="alamat" class="form-control" placeholder=''/>
                </div>
                <div class="form-group">
                    <label for='total' style='display:inline-block'>Total</label>
                    <input type="text" id='total' name="total" class="form-control" placeholder=''/>
                </div>
                <div class="form-group">
                    <label for='id_dana' style='display:inline-block'>Id Dana</label>
                    <input type="text" id='id_dana' name="id_dana" class="form-control" placeholder=''/>
                </div>
                <div class="form-group">
                    <label for='sumber_dana' style='display:inline-block'>Sumber Dana</label>
                    <input type="text" id='sumber_dana' name="sumber_dana" class="form-control" placeholder=''/>
                </div>
                <div class="form-group">
                    <label for='tahun_anggaran' style='display:inline-block'>Tahun Anggaran</label>
                    <input type="text" id='tahun_anggaran' name="tahun_anggaran" class="form-control" placeholder=''/>
                </div>
            </div> 
            <div class="modal-footer">
                <button class="btn btn-primary submitBtn" onclick="submitTambahDataFormBKKInfrastruktur()">Simpan</button>
                <button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>    
jQuery(document).ready(function(){
    get_data_bkk_infrastruktur();
});

function get_data_bkk_infrastruktur(){
    if(typeof databkk_infrastruktur == 'undefined'){
        window.databkk_infrastruktur = jQuery('#management_data_table').on('preXhr.dt', function(e, settings, data){
            jQuery("#wrap-loading").show();
        }).DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                data:{
                    'action': 'get_datatable_bkk_infrastruktur',
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
                    "data": 'id_kecamatan',
                    className: "text-center"
                },
                {
                    "data": 'id_desa',
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
                    "data": 'total',
                    className: "text-center"
                },
                {
                    "data": 'id_dana',
                    className: "text-center"
                },
                {
                    "data": 'sumber_dana',
                    className: "text-center"
                },
                {
                    "data": 'tahun_anggaran',
                    className: "text-center"
                },
                {
                    "data": 'aksi',
                    className: "text-center"
                }
            ]
        });
    }else{
        databkk_infrastruktur.draw();
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
                'action' : 'hapus_data_bkk_infrastruktur_by_id',
                'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
                'id'     : id
            },
            dataType: 'json',
            success:function(response){
                jQuery('#wrap-loading').hide();
                if(response.status == 'success'){
                    get_data_bkk_infrastruktur(); 
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
            'action': 'get_data_bkk_infrastruktur_by_id',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id': _id,
        },
        success: function(res){
            if(res.status == 'success'){
                jQuery('#id_data').val(res.data.id);
                jQuery('#id_kecamatan').val(res.data.id_kecamatan);
                jQuery('#id_desa').val(res.data.id_desa);
                jQuery('#kecamatan').val(res.data.kecamatan);
                jQuery('#desa').val(res.data.desa);
                jQuery('#kegiatan').val(res.data.kegiatan);
                jQuery('#alamat').val(res.data.alamat);
                jQuery('#total').val(res.data.total);
                jQuery('#id_dana').val(res.data.id_dana);
                jQuery('#sumber_dana').val(res.data.sumber_dana);
                jQuery('#tahun_anggaran').val(res.data.tahun_anggaran);
                jQuery('#modalTambahDataBKKInfrastruktur').modal('show');
            }else{
                alert(res.message);
            }
            jQuery('#wrap-loading').hide();
        }
    });
}

//show tambah data
function tambah_data_bkk_infrastruktur(){
    jQuery('#id_data').val('');
    jQuery('#id_kecamatan').val('');
    jQuery('#id_desa').val('');
    jQuery('#kecamatan').val('');
    jQuery('#desa').val('');
    jQuery('#kegiatan').val('');
    jQuery('#alamat').val('');
    jQuery('#total').val('');
    jQuery('#sumber_dana').val('');
    jQuery('#id_dana').val('');
    jQuery('#tahun_anggaran').val('');
    jQuery('#modalTambahDataBKKInfrastruktur').modal('show');
}

function submitTambahDataFormBKKInfrastruktur(){
    var id_data = jQuery('#id_data').val();
    var id_desa = jQuery('#id_desa').val();
    if(id_desa == ''){
        return alert('Data id_desa tidak boleh kosong!');
    }
    var desa = jQuery('#desa').val();
    if(desa == ''){
        return alert('Data desa tidak boleh kosong!');
    }
    var kecamatan = jQuery('#kecamatan').val();
    if(kecamatan == ''){
        return alert('Data kecamatan tidak boleh kosong!');
    }
    var id_kecamatan = jQuery('#id_kecamatan').val();
    if(id_kecamatan == ''){
        return alert('Data id_kecamatan tidak boleh kosong!');
    }
    var alamat = jQuery('#alamat').val();
    if(alamat == ''){
        return alert('Data alamat tidak boleh kosong!');
    }
    var kegiatan = jQuery('#kegiatan').val();
    if(kegiatan == ''){
        return alert('Data kegiatan tidak boleh kosong!');
    }
    var total = jQuery('#total').val();
    if(total == ''){
        return alert('Data total tidak boleh kosong!');
    }
    var sumber_dana = jQuery('#sumber_dana').val();
    if(sumber_dana == ''){
        return alert('Data sumber_dana tidak boleh kosong!');
    }
    var id_dana = jQuery('#id_dana').val();
    if(id_dana == ''){
        return alert('Data id_dana tidak boleh kosong!');
    }
    var tahun_anggaran = jQuery('#tahun_anggaran').val();
    if(tahun_anggaran == ''){
        return alert('Data tahun_anggaran tidak boleh kosong!');
    }

    jQuery('#wrap-loading').show();
    jQuery.ajax({
        method: 'post',
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        dataType: 'json',
        data:{
            'action': 'tambah_data_bkk_infrastruktur',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id_data': id_data,
            'id_kecamatan': id_kecamatan,
            'id_desa': id_desa,
            'kecamatan': kecamatan,
            'desa': desa,
            'kegiatan': kegiatan,
            'alamat': alamat,
            'total': total,
            'sumber_dana': sumber_dana,
            'id_dana': id_dana,
            'tahun_anggaran': tahun_anggaran,
        },
        success: function(res){
            alert(res.message);
            jQuery('#modalTambahDataBKKInfrastruktur').modal('hide');
            if(res.status == 'success'){
                get_data_bkk_infrastruktur();
            }else{
                jQuery('#wrap-loading').hide();
            }
        }
    });
}
</script>