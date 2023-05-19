<?php
global $wpdb;
$idtahun = $wpdb->get_results("select distinct tahun_anggaran from data_unit", ARRAY_A);
$tahun = "<option value='-1'>Pilih Tahun</option>";
foreach($idtahun as $val){
    $tahun .= "<option value='$val[tahun_anggaran]'>$val[tahun_anggaran]</option>";
}
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
        <h1 class="text-center" style="margin:3rem;">Halaman Input Pencairan BKK</h1>
        <div style="margin-bottom: 25px;">
            <form id="formid" onsubmit="return false; " style="width: 500px; margin: auto;">
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
                </div>
                <div class="form-group">
                    <label>Pagu Anggaran</label>
                        <input type="number" class="form-control" id="pagu_anggaran" />
                </div>
                <div class="form-group">
                    <label for="">Proposal BKK Infrastruktur</label>
                    <input type="file" class="form-control-file" id="">
                </div>
                  <button type="submit" onclick="kirim_form_bkk();" class="btn btn-primary">Kirim</button>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function get_bkk(){
        var tahun = jQuery('#tahun').val();
        if(tahun == '' || tahun == '-1'){
            return alert('Pilih tahun anggaran dulu!');
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
        jQuery('#pagu_anggaran').val(kecamatan_all[kec][desa][kegiatan][alamat][0].total);
    }
</script>