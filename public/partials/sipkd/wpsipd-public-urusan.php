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
                <div class="card-header">URUSAN/ Bidang Urusan/ SKPD</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <form id="furus">
                                <input type="hidden" name="action" id="action">
                                <div class="form-group">
                                    <label for="tahun_anggaran">Tahun Anggaran</label>
                                    <select name="tahun_anggaran" id="tahun_anggaran" class="form-control">
                                        <option value="2021">2021</option>
                                        <option value="2022">2022</option>
                                        <option value="2023" selected>2023</option>
                                    </select>
                                </div>
                            </form>
                            <div>
                               <button class="button btn-primary" id="refresh">Refresh</button> 
                               <button class="button btn-danger" id="singkron">Singkron ke DB SIPKD</button>
                               <div class="load" style="display:none;">Loading</div>
                            </div>
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
</style>

<script>
    jQuery(document).ready(()=>{
        jQuery('#refresh').on('click',()=>{
            jQuery('#action').val('sipkd_get_urus_skpd');
            jQuery.ajax({
                url:'/wp-admin/admin-ajax.php',
                method:'post',
                dataType:'json',
                data:jQuery('#furus').serialize(),
                success:(resp)=>{
                    console.log(resp)
                }
            })
        })
    })
</script>