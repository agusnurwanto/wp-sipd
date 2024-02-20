
<div class="cetak">
    <div style="padding: 10px;">
        <h1 class="text-center">Data Surat Penyediaan Dana ( SPD )<br>Tahun 2024<?php; ?></h1>
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
                <?php echo $body_spp; ?>
            </tbody>
            <tfoot>
                <th colspan="4" class="text-center">Total</th>
                <th class="text-right"></th>
            </tfoot>
        </table>
    </div>
</div>