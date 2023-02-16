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
                               <div class="load" style="display:none;">Loading....</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <table class="table tbl-striped" id="tblurus">
                                <thead>
                                    <tr>
                                        <th>UNITKEY</th>
                                        <th>KDUNIT</th>
                                        <th>NMUNIT</th>
                                        <th>KDLEVEL</th>
                                        <th>TYPE</th>
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
        margin-bottom:20px;
    }
</style>

<script>
    jQuery(document).ready(()=>{
        jQuery('#refresh').on('click',()=>{
            jQuery('#action').val('sipkd_get_urus_skpd');
            var tblurus=jQuery('#tblurus tbody');
            tblurus.html('');
            jQuery.ajax({
                url:'/wp-admin/admin-ajax.php',
                method:'post',
                dataType:'json',
                data:jQuery('#furus').serialize(),
                success:(resp)=>{
                    resp.data.map((v,i)=>{
                        jQuery('#tblurus tbody').append("<tr><td>"+v.unitkey+"</td><td>"+v.kdunit+"</td><td>"+v.nmunit+"</td><td>"+v.kdlevel+"</td><td>"+v.TYPE+"</td></tr>")
                    })
                    
                }
            })
        })

        jQuery('#singkron').on('click',()=>{
            if(confirm("Apakah anda yakin untuk mensingkronkan data Urusan/Bidang Urusan/ SKPD WP-SIPD ke SIPKD?\nKetika Melakukan Singkronisasi, Data di table Daftunit akan dihapus dan diinsert dari data UNIT WP-SIPD"))
            jQuery('#action').val('sipkd_singkron_urus_skpd');
            jQuery.ajax({
                url:'/wp-admin/admin-ajax.php',
                method:'post',
                dataType:'json',
                data:jQuery('#furus').serialize(),
                success:(resp)=>{
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