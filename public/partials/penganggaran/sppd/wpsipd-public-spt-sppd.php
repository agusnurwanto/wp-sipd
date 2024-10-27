<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>
<style>
</style>
<div class="cetak">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <h1 class="text-center">Halaman SPT</h1>
        <h2 class="text-center">(Surat Perintah Tugas)</h2>
        <div style="margin-bottom: 25px;">
            <button class="btn btn-primary" onclick="tambah_data();"><span class="dashicons dashicons-plus"></span>Tambah Data</button>
        </div>
        <table id="table_data" cellpadding="2" cellspacing="0">
            <thead>
                <tr>
                    <th class="text-center">Nomor</th>
                    <th class="text-center">Uraian</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade mt-4" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah SPT</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary submitBtn" onclick="submitData()">Simpan</button>
                <button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>