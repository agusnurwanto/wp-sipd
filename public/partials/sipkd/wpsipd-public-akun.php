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
                                <div class="form-group ">
                                        <label for="tahun">Tahun Anggaran</label>
                                        <select name="tahun" id="tahun_anggaran" class="form-control">
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
                                        <div>
                                            <button type="submit" value="refresh" class="button btn-primary">Refresh</button> 
                                            <button class="button btn-danger" value="singkron">Singkron ke DB Lokal</button>
                                        </div>
                                </div>
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


<script>
    jQuery(document).ready(function(){
        
    })
</script>