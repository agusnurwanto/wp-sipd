<<<<<<< HEAD
On Progress:)
=======
<div class="cetak">
    <div style="padding: 10px;">
        <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
        <input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
        <h1 class="text-center">Data Surat Penyediaan Dana ( SPD )<br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
        <table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
            <thead id="data_header">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama SKPD</th>
                    <th class="text-center">No SPP</th>
                    <th class="text-center">Tanggal SPP</th>
                    <th class="text-center">Total SIMDA</th>
                </tr>
            </thead>
            <tbody id="data_body">
                <?php echo $body; ?>
            </tbody>
            <tfoot>
                <th colspan="4" class="text-center">Total</th>
                <th class="text-right"><?php echo number_format($total_all_simda, 2, ',', '.'); ?></th>
            </tfoot>
        </table>
    </div>
</div>
>>>>>>> db0dcff (add halaman spp)
