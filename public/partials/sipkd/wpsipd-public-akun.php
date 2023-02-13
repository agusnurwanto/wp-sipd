<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;
?>
<div class="container-fluid" style="margin-top:20px;">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">Akun Kepmen 050-5889 Tahun 2021</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <form id="fakun">
                                <input type="hidden" name="action" id="action">
                                <div class="form-group ">
                                        <label for="tahun_anggaran">Tahun Anggaran</label>
                                        <select name="tahun_anggaran" id="tahun_anggaran" class="form-control" id="tahun">
                                            <option value="2021">2021</option>
                                            <option value="2022">2022</option>
                                            <option value="2023">2023</option>
                                        </select>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="pendapatan" name="jenis" value="4">
                                            <label for="pendapatan">Pendapatan</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="belanja" name="jenis" value="5">
                                            <label for="belanja">Belanja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="pembiayaan" name="jenis" value="6">
                                            <label for="pembiayaan">Pembiayaan</label>
                                        </div>
                                    </div>
                                </form>
                                        <div>
                                            <button type="submit" value="refresh" class="button btn-primary" id="btn-refresh">Refresh</button> 
                                            <button class="button btn-danger" value="singkron" id="btn-singkron">Singkron ke DB Lokal</button>
                                        </div>
                                <div class="load" style="display:none;">Loading....</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <table class="table table-striped" id="listakun">
                                <thead>
                                    <tr>
                                        <th>Key</th>
                                        <th>KODE</th>
                                        <th>URAIAN</th>
                                        <th>Level</th>
                                        <th>Tipe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .load{
        color:red;
    }
    .row{
        margin-bottom:10px;
    }
</style>

<script>
    jQuery(document).ready(function(){
        jQuery('#btn-refresh').on('click',()=>{
            jQuery('#action').val('sipkd_get_akun_sipd')
            jQuery.ajax({
                url:'/wp-admin/admin-ajax.php',
                method:'post',
                dataType:'json',
                data:jQuery('#fakun').serialize(),
                success:(resp)=>{
                   let tb= jQuery('#listakun tbody');
                   tb.html('');
                    resp.data.map((v,i)=>{
                        var row='<tr><td>'+v.mtgkey+'</td><td>'+v.kdper+'</td><td>'+v.nmper+'</td><td>'+v.mtglevel+'</td><td>'+v.TYPE+'</td></tr>'
                        tb.append(row)
                    });
                }
            })
        })
        jQuery(document).on({
            ajaxStart:()=>{
                jQuery('.load').show();
            },
            ajaxStop:()=>{
                jQuery('.load').hide();
            }
        })
    })
</script>