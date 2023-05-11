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
    <h1 class="text-center" style="margin:3rem;">Manajemen Data BHPD</h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_data_bhpd();"><i class="dashicons dashicons-plus"></i> Tambah Data BHPD</button>
        </div>
        <div class="wrap-table">
        <table id="management_data_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Id Kecamatan</th>
                    <th class="text-center">Id Desa</th>
                    <th class="text-center">Kecamatan</th>
                    <th class="text-center">Desa</th>
                    <th class="text-center">Total</th>
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

<div class="modal fade mt-4" id="modalTambahDataBHPD" tabindex="-1" role="dialog" aria-labelledby="modalTambahDataBHPDLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataBHPDLabel">Data BKK Infrastruktur</h5>
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
                    <label for='total' style='display:inline-block'>total</label>
                    <input type="text" id='total' name="total" class="form-control" placeholder=''/>
                </div>
                <div class="form-group">
                    <label for='tahun_anggaran' style='display:inline-block'>tahun_anggaran</label>
                    <input type="text" id='tahun_anggaran' name="tahun_anggaran" class="form-control" placeholder=''/>
                </div>
            </div> 
            <div class="modal-footer">
                <button class="btn btn-primary submitBtn" onclick="submitTambahDataFormBHPD()">Simpan</button>
                <button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>    
jQuery(document).ready(function(){
    get_data_bhpd();
});

function get_data_bhpd(){
    if(typeof databhpd == 'undefined'){
        window.databhpd = jQuery('#management_data_table').on('preXhr.dt', function(e, settings, data){
            jQuery("#wrap-loading").show();
        }).DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                data:{
                    'action': 'get_datatable_bhpd',
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
                    "data": 'total',
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
        databhpd.draw();
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
                'action' : 'hapus_data_bhpd_by_id',
                'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
                'id'     : id
            },
            dataType: 'json',
            success:function(response){
                jQuery('#wrap-loading').hide();
                if(response.status == 'success'){
                    get_data_bhpd(); 
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
            'action': 'get_data_bhpd_by_id',
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
                jQuery('#total').val(res.data.total);
                jQuery('#id_dana').val(res.data.id_dana);
                jQuery('#modalTambahDataBHPD').modal('show');
            }else{
                alert(res.message);
            }
            jQuery('#wrap-loading').hide();
        }
    });
}

//show tambah data
function tambah_data_bhpd(){
    jQuery('#id_data').val('');
    jQuery('#id_kecamatan').val('');
    jQuery('#id_desa').val('');
    jQuery('#kecamatan').val('');
    jQuery('#desa').val('');
    jQuery('#total').val('');
    jQuery('#tahun_anggaran').val('');
    jQuery('#modalTambahDataBHPD').modal('show');
}

function submitTambahDataFormBHPD(){
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
    var total = jQuery('#total').val();
    if(total == ''){
        return alert('Data total tidak boleh kosong!');
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
            'action': 'tambah_data_bhpd',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id_data': id_data,
            'id_kecamatan': id_kecamatan,
            'id_desa': id_desa,
            'kecamatan': kecamatan,
            'desa': desa,
            'total': total,
            'tahun_anggaran': tahun_anggaran,
        },
        success: function(res){
            alert(res.message);
            jQuery('#modalTambahDataBHPD').modal('hide');
            if(res.status == 'success'){
                get_data_bhpd();
            }else{
                jQuery('#wrap-loading').hide();
            }
        }
    });
}
</script>