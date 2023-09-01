<?php
global $wpdb;

$input = shortcode_atts( array(
    'id_skpd' => '',
    'tahun_anggaran' => ''
), $atts );
if(!empty($_GET) && !empty($_GET['tahun_anggaran'])){
    $input['tahun_anggaran'] = $wpdb->prepare('%d', $_GET['tahun_anggaran']);
}
if(!empty($_GET) && !empty($_GET['id_skpd'])){
    $input['id_skpd'] = $wpdb->prepare('%d', $_GET['id_skpd']);
}

$idtahun = $wpdb->get_results("select distinct tahun_anggaran from data_unit", ARRAY_A);
$tahun = "<option value='-1'>Pilih Tahun</option>";
foreach ($idtahun as $val) {
    $selected = '';
    if(!empty($input['tahun_anggaran']) && $val['tahun_anggaran'] == $input['tahun_anggaran']){
        $selected = 'selected';
    }
    $tahun .= "<option value='$val[tahun_anggaran]' $selected>$val[tahun_anggaran]</option>";
}

$nama_skpd = '';
$nama_kec = '';
$user_id = um_user( 'ID' );
$user_meta = get_userdata($user_id);
$disabled = 'disabled';
if(in_array("administrator", $user_meta->roles)){
    $disabled = '';
}else if(
    in_array("PA", $user_meta->roles)
    || in_array("PLT", $user_meta->roles)
    || in_array("KPA", $user_meta->roles)
){
    if(
        empty($input['id_skpd'])
        || empty($input['tahun_anggaran'])
    ){
        die('<h1>ID SKPD dan tahun anggaran tidak boleh kosong!</h1>');
    }
    $nipkepala = get_user_meta($user_id, '_nip');
    $skpd = $wpdb->get_row($wpdb->prepare("
        SELECT 
            nama_skpd, 
            id_skpd, 
            kode_skpd,
            is_skpd
        from data_unit 
        where id_skpd=%d
            and active=1
            and tahun_anggaran=%d
        group by id_skpd", $input['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
    $nama_skpd = '<br>'.$skpd['nama_skpd'].'<br>Tahun '.$input['tahun_anggaran'];
    $nama_kec = str_replace('kecamatan ', '', strtolower($skpd['nama_skpd']));
}else{
    die('<h1>Anda tidak punya akses untuk melihat halaman ini!</h1>');
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
    <h1 class="text-center" style="margin:3rem;">Pencairan BKU Alokasi Dana Desa<?php echo $nama_skpd; ?></h1>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_data_pencairan_bku_add();"><i class="dashicons dashicons-plus"></i> Tambah Data</button>
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

<div class="modal fade mt-4" id="modalTambahDataPencairanBKUADD" tabindex="-1" role="dialog" aria-labelledby="modalTambahDataPencairanBKUADDLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataPencairanBKUADDLabel">Data Pencairan BKU Alokasi Dana Desa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type='hidden' id='id_data' name="id_data" placeholder=''>
                <div class="form-group">
                    <label>Tahun Anggaran</label>
                    <select class="form-control" id="tahun" onchange="get_bku_add();">
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
                    <input type="hidden" class="form-control" id="id_bku_add" />
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
                <div class="form-group">
                    <label for="">Nota Dinas Permohonan beserta lampirannya</label>
                    <input type="file" name="file" class="form-control-file" id="nota_dinas">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_nota_dinas_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Pernyataan Tanggung Jawab</label>
                    <input type="file" name="file" class="form-control-file" id="sptj">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_sptj_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Pakta Integritas</label>
                    <input type="file" name="file" class="form-control-file" id="pakta_integritas">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_pakta_integritas_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Surat Permohonan Transfer</label>
                    <input type="file" name="file" class="form-control-file" id="permohonan_transfer">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_permohonan_transfer_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Realisasi Tahap Sebelumnya (hanya bagi Tahap II dan III)</label>
                    <input type="file" name="file" class="form-control-file" id="realisasi_tahap_sebelumnya">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_realisasi_tahap_sebelumnya_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Dokumen Syarat Umum</label>
                    <input type="file" name="file" class="form-control-file" id="dokumen_syarat_umum">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_dokumen_syarat_umum_existing"></a></div>
                </div>
                <div class="form-group">
                    <label for="">Dokumen Syarat Khusus</label>
                    <input type="file" name="file" class="form-control-file" id="dokumen_syarat_khusus">
                    <div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_dokumen_syarat_khusus_existing"></a></div>
                </div>
                <div>File maksimal berukuran 1 Mb, berformat .pdf</div>
                <div class="form-check form-switch">
                    <input class="form-check-input" value="1" type="checkbox" id="status_pagu" onclick="set_keterangan(this);" <?php echo $disabled; ?>>
                    <label class="form-check-label" for="status_pagu">Disetujui</label>
                </div>
                <div class="form-group" style="display:none;">
                    <label>Keterangan ditolak</label>
                    <textarea class="form-control" id="keterangan_status_pagu" <?php echo $disabled; ?>></textarea>
                </div> 
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="submitTambahDataFormPencairanBKUADD();" class="btn btn-primary send_data">Kirim</button>
                 <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>    
jQuery(document).ready(function(){
    get_data_pencairan_bku_add();
    window.global_file_upload = "<?php echo WPSIPD_PLUGIN_URL.'public/media/keu_pemdes/'; ?>";
});

function set_keterangan(that){
    var id = jQuery(that).attr('id');
    if(jQuery(that).is(':checked')){
        jQuery('#keterangan_'+id).closest('.form-group').hide();
    }else{
        jQuery('#keterangan_'+id).closest('.form-group').show();
    }
}

function get_data_pencairan_bku_add(){
    if(typeof datapencairan_bku_add == 'undefined'){
        window.datapencairan_bku_add = jQuery('#management_data_table').on('preXhr.dt', function(e, settings, data){
            jQuery("#wrap-loading").show();
        }).DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                data:{
                    'action': 'get_datatable_data_pencairan_bku_add',
                    'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
                    'tahun_anggaran': '<?php echo $input['tahun_anggaran']; ?>',
                    'id_skpd': '<?php echo $input['id_skpd']; ?>'
                }
            },
            lengthMenu: [
                [20, 50, 100, -1],
                [20, 50, 100, "All"]
            ],
            order: [
                [0, 'asc']
            ],
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
        datapencairan_bku_add.draw();
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
                'action' : 'hapus_data_pencairan_bku_add_by_id',
                'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
                'id'     : id
            },
            dataType: 'json',
            success:function(response){
                jQuery('#wrap-loading').hide();
                if(response.status == 'success'){
                    get_data_pencairan_bku_add(); 
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
            'action': 'get_data_pencairan_bku_add_by_id',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id': _id,
        },
        success: function(res){
            if(res.status == 'success'){
                jQuery('#id_data').val(res.data.id).prop('disabled', false);
                jQuery('#tahun').val(res.data.tahun_anggaran).prop('disabled', false);
                get_bku_add()
                .then(function(){
                    jQuery('#kec').val(res.data.kecamatan).trigger('change').prop('disabled', false);
                    jQuery('#desa').val(res.data.desa).prop('disabled', false);
                    jQuery('#id_bku_add').val(res.data.id_bku_add).prop('disabled', false);
                    jQuery('#validasi_pagu').closest('.form-group').show().prop('disabled', false);
                    jQuery('#pagu_anggaran').val(res.data.total_pencairan).prop('disabled', false);
                    if(res.data.status_ver_total == 0){
                        jQuery('#keterangan_status_pagu').closest('.form-group').show().prop('disabled', false);
                        jQuery('#status_pagu').prop('checked', false);
                    }else{
                        jQuery('#keterangan_status_pagu').closest('.form-group').hide().prop('disabled', false);
                        jQuery('#status_pagu').prop('checked', true);
                    }

                    jQuery('#file_nota_dinas_existing').attr('href', global_file_upload+res.data.file_nota_dinas).html(res.data.file_nota_dinas);
                    jQuery('#nota_dinas').val('').show();

                    jQuery('#file_sptj_existing').attr('href', global_file_upload+res.data.file_sptj).html(res.data.file_sptj);
                    jQuery('#sptj').val('').show();

                    jQuery('#file_pakta_integritas_existing').attr('href', global_file_upload+res.data.file_pakta_integritas).html(res.data.file_pakta_integritas);
                    jQuery('#pakta_integritas').val('').show();

                    jQuery('#file_permohonan_transfer_existing').attr('href', global_file_upload+res.data.file_permohonan_transfer).html(res.data.file_permohonan_transfer);
                    jQuery('#permohonan_transfer').val('').show();

                    jQuery('#file_realisasi_tahap_sebelumnya_existing').attr('href', global_file_upload+res.data.file_realisasi_tahap_sebelumnya).html(res.data.file_realisasi_tahap_sebelumnya);
                    jQuery('#realisasi_tahap_sebelumnya').val('').show();

                    jQuery('#file_dokumen_syarat_umum_existing').attr('href', global_file_upload+res.data.file_dokumen_syarat_umum).html(res.data.file_dokumen_syarat_umum);
                    jQuery('#dokumen_syarat_umum').val('').show();

                    jQuery('#file_dokumen_syarat_khusus_existing').attr('href', global_file_upload+res.data.file_dokumen_syarat_khusus).html(res.data.file_dokumen_syarat_khusus);
                    jQuery('#dokumen_syarat_khusus').val('').show();

                    jQuery('#keterangan_status_pagu').val(res.data.ket_ver_total).prop('disabled', false);
                    jQuery('#keterangan').val(res.data.keterangan).prop('disabled', false);
                    jQuery('#status_pagu').closest('.form-check').show().prop('disabled', false);
                    jQuery('#modalTambahDataPencairanBKUADD .send_data').show();
                    jQuery('#modalTambahDataPencairanBKUADD').modal('show');
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
            'action': 'get_data_pencairan_bku_add_by_id',
            'api_key': '<?php echo get_option( '_crb_api_key_extension' ); ?>',
            'id': _id,
        },
        success: function(res){
            if(res.status == 'success'){
                jQuery('#id_data').val(res.data.id).prop('disabled', true);
                jQuery('#tahun').val(res.data.tahun_anggaran).prop('disabled', true);
                get_bku_add()
                .then(function(){
                    jQuery('#kec').val(res.data.kecamatan).trigger('change').prop('disabled', true);
                    jQuery('#desa').val(res.data.desa).trigger('change').prop('disabled', true);
                    jQuery('#uraian_kegiatan').val(res.data.kegiatan).trigger('change').prop('disabled', true);
                    jQuery('#id_bku_add').val(res.data.id_bku_add).prop('disabled', true);
                    jQuery('#alamat').val(res.data.alamat).prop('disabled', true);
                    jQuery('#pagu_anggaran').val(res.data.total_pencairan).prop('disabled', true);
                    if(res.data.status_ver_total == 0){
                        jQuery('#keterangan_status_pagu').closest('.form-group').show().prop('disabled', true);
                        jQuery('#status_pagu').prop('checked', false).prop('disabled', true);
                    }else{
                        jQuery('#keterangan_status_pagu').closest('.form-group').hide().prop('disabled', true);
                        jQuery('#status_pagu').prop('checked', true).prop('disabled', true);
                    }
                    jQuery('#file_nota_dinas_existing').attr('href', global_file_upload+res.data.file_nota_dinas).html(res.data.file_nota_dinas);
                    jQuery('#nota_dinas').val('').hide();

                    jQuery('#file_sptj_existing').attr('href', global_file_upload+res.data.file_sptj).html(res.data.file_sptj);
                    jQuery('#sptj').val('').hide(); 

                    jQuery('#file_pakta_integritas_existing').attr('href', global_file_upload+res.data.file_pakta_integritas).html(res.data.file_pakta_integritas);
                    jQuery('#pakta_integritas').val('').hide();

                    jQuery('#file_permohonan_transfer_existing').attr('href', global_file_upload+res.data.file_permohonan_transfer).html(res.data.file_permohonan_transfer);
                    jQuery('#permohonan_transfer').val('').hide(); 

                    jQuery('#file_realisasi_tahap_sebelumnya_existing').attr('href', global_file_upload+res.data.file_realisasi_tahap_sebelumnya).html(res.data.file_realisasi_tahap_sebelumnya);
                    jQuery('#realisasi_tahap_sebelumnya').val('').hide();

                    jQuery('#file_dokumen_syarat_umum_existing').attr('href', global_file_upload+res.data.file_dokumen_syarat_umum).html(res.data.file_dokumen_syarat_umum);
                    jQuery('#dokumen_syarat_umum').val('').hide(); 

                    jQuery('#file_dokumen_syarat_khusus_existing').attr('href', global_file_upload+res.data.file_dokumen_syarat_khusus).html(res.data.file_dokumen_syarat_khusus);
                    jQuery('#dokumen_syarat_khusus').val('').hide();

                    jQuery('#keterangan_status_pagu').val(res.data.ket_ver_total).prop('disabled', true);

                    jQuery('#keterangan').val(res.data.keterangan).prop('disabled', true);

                    jQuery('#status_pagu').closest('.form-check').show().prop('disabled', true);

                    jQuery('#modalTambahDataPencairanBKUADD .send_data').hide();

                    jQuery('#modalTambahDataPencairanBKUADD').modal('show');
                })
            }else{
                alert(res.message);
                jQuery('#wrap-loading').hide();
            }
        }
    });
}

//show tambah data
function tambah_data_pencairan_bku_add() {
    jQuery('#id_data').val('').prop('disabled', false);
    jQuery('#tahun').val('<?php echo $input['tahun_anggaran']; ?>').prop('disabled', false);
    new Promise(function(resolve, reject){
        if('<?php echo $input['tahun_anggaran']; ?>' != ''){
            get_bku_add().then(function(){
                resolve();
            });
        }else{
            resolve();
        }
    })
    .then(function() {
        jQuery('#id_data').val('').prop('disabled', false);
        jQuery('#kec').val('').prop('disabled', false);
        jQuery('#desa').val('').prop('disabled', false);
        jQuery('#validasi_pagu').html('');
        jQuery('#pagu_anggaran').val('').prop('disabled', false);
        jQuery('#keterangan').val('').prop('disabled', false);
        jQuery('#status_pagu').closest('.form-check').hide().prop('disabled', false);
        jQuery('#keterangan_status_pagu').closest('.form-group').hide().prop('disabled', false);
        jQuery('#status_pagu').prop('checked', false);
        jQuery('#keterangan_status_pagu').val('').prop('disabled', false);

        jQuery('#sptj').val('').show();
        jQuery('#file_sptj_existing').attr('href', '').html('');
        
        jQuery('#nota_dinas').val('').show();
        jQuery('#file_nota_dinas_existing').attr('href', '').html('');

        jQuery('#modalTambahDataPencairanBKUADD .send_data').show();
        jQuery('#modalTambahDataPencairanBKUADD').modal('show');
    });
}

function submitTambahDataFormPencairanBKUADD(){
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
    var id_bku_add = jQuery('#id_bku_add').val();
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
    var nota_dinas = jQuery('#nota_dinas')[0].files[0];;
    if (typeof nota_dinas == 'undefined') {
        return alert('Upload file nota dinas beserta kelengkapan dulu!');
    }
    var sptj = jQuery('#sptj')[0].files[0];;
    if (typeof sptj == 'undefined') {
        return alert('Upload file SPTJ dulu!');
    }
    var pakta_integritas = jQuery('#pakta_integritas')[0].files[0];;
    if (typeof pakta_integritas == 'undefined') {
        return alert('Upload file Pakta integritas dulu!');
    }
    var permohonan_transfer = jQuery('#permohonan_transfer')[0].files[0];;
    if (typeof permohonan_transfer == 'undefined') {
        return alert('Upload file Permohonan transfer dulu!');
    }
    var realisasi_tahap_sebelumnya = jQuery('#realisasi_tahap_sebelumnya')[0].files[0];;
    var total_pencairan = +jQuery('#pencairan').attr('total-pencairan');
    if(total_pencairan > 0){
        if (typeof realisasi_tahap_sebelumnya == 'undefined') {
            return alert('Upload file Realisasi tahap sebelumnya dulu!');
        }
    }
    var dokumen_syarat_umum = jQuery('#dokumen_syarat_umum')[0].files[0];;
    if (typeof dokumen_syarat_umum == 'undefined') {
        return alert('Upload file Dokumen syarat umum dulu!');
    }
    var dokumen_syarat_khusus = jQuery('#dokumen_syarat_khusus')[0].files[0];;
    if (typeof dokumen_syarat_khusus == 'undefined') {
        return alert('Upload file Dokumen syarat khusus dulu!');
    }

    let tempData = new FormData();
        tempData.append('action', 'tambah_data_pencairan_bku_add');
        tempData.append('api_key', '<?php echo get_option('_crb_api_key_extension'); ?>');
        tempData.append('id_bku_add', id_bku_add);
        tempData.append('id_data', id_data);
        tempData.append('pagu_anggaran', pagu_anggaran);
        tempData.append('status_pagu', status_pagu);
        tempData.append('keterangan_status_pagu', keterangan_status_pagu);
        tempData.append('keterangan', keterangan);
        tempData.append('nota_dinas', nota_dinas);
        tempData.append('sptj', sptj);
        tempData.append('pakta_integritas', pakta_integritas);
        tempData.append('permohonan_transfer', permohonan_transfer);
        tempData.append('realisasi_tahap_sebelumnya', realisasi_tahap_sebelumnya);
        tempData.append('dokumen_syarat_umum', dokumen_syarat_umum);
        tempData.append('dokumen_syarat_khusus', dokumen_syarat_khusus);
       
    jQuery('#wrap-loading').show();
    jQuery.ajax({
        method: 'post',
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        dataType: 'json',
        data: tempData,
        processData: false,
        contentType: false,
        cache: false,
        success: function(res){
            alert(res.message);
            if(res.status == 'success'){
            jQuery('#modalTambahDataPencairanBKUADD').modal('hide');
                get_data_pencairan_bku_add();
            }else{
                jQuery('#wrap-loading').hide();
            }
        }
    });
}

function get_bku_add(){
    return new Promise(function(resolve, reject) {
        var tahun = jQuery('#tahun').val();
        if (tahun == '' || tahun == '-1') {
            alert('Pilih tahun anggaran dulu!');
            return resolve();
        }
        if(typeof bkk_global == 'undefined'){
            window.bkk_global = {};
        }

        if(!bkk_global[tahun]){
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type:"post",
                data:{
                    'action' : "get_pemdes_bku_add",
                    'api_key' : jQuery("#api_key").val(),
                    'tahun_anggaran' : tahun,
                    'nama_kec': '<?php echo $nama_kec; ?>'
                },
                dataType: "json",
                success:function(response) {
                    bkk_global[tahun] = response.data;
                    window.kecamatan_all = {};
                    bkk_global[tahun].map(function(b, i) {
                        if (!kecamatan_all[b.kecamatan]) {
                            kecamatan_all[b.kecamatan] = {};
                        }
                        if (!kecamatan_all[b.kecamatan][b.desa]) {
                            kecamatan_all[b.kecamatan][b.desa] = [];
                        }
                        kecamatan_all[b.kecamatan][b.desa].push(b);
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
        }else{
            return resolve();
        }
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

function get_pagu() {
    var kec = jQuery('#kec').val();
    if(kec == '' || kec == '-1'){
        return alert('Pilih kecamatan dulu!');
    }
    var desa = jQuery('#desa').val();
    if(desa == '' || desa == '-1'){
        return alert('Pilih desa dulu!');
    }
    jQuery('#wrap-loading').show();
    var pagu = kecamatan_all[kec][desa][0].total;
    var id = kecamatan_all[kec][desa][0].id;
    var tahun = kecamatan_all[kec][desa][0].tahun_anggaran;
    jQuery.ajax({
        url: "<?php echo admin_url('admin-ajax.php'); ?>",
        type:"post",
        data:{
            'action' : "get_pencairan_pemdes_bku_add",
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
                        +'<td class="text-right" id="pencairan" total-pencairan="'+total_pencairan+'">'+formatRupiah(total_pencairan)+'</td>'
                        +'<td class="text-right">'+formatRupiah(global_sisa)+'</td>'
                    +'</tr>';
                if(total_pencairan > 0){
                    jQuery('#realisasi_tahap_sebelumnya').closest('.form-group').show();
                }else{
                    jQuery('#realisasi_tahap_sebelumnya').closest('.form-group').hide();
                }
                jQuery('#validasi_pagu').html(tbody);
                jQuery('#id_bku_add').val(id);
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