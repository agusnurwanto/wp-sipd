<?php 
global $wpdb;

if (!defined('WPINC')) {
    die;
}

$input = shortcode_atts(array(
    'tahun_anggaran' => '2023'
), $atts);

$sql_unit = $wpdb->prepare("
    SELECT 
        *
    FROM data_unit 
    WHERE 
        tahun_anggaran=%d
        AND active=1
    order by kode_skpd ASC
    ", $input['tahun_anggaran']);
$unit = $wpdb->get_results($sql_unit, ARRAY_A);

$sql_risiko = $wpdb->prepare("
    SELECT 
        kode_bidang_urusan,
        nama_bidang_urusan
    FROM data_prog_keg
    WHERE tahun_anggaran=%d
    GROUP BY kode_bidang_urusan, nama_bidang_urusan
    ORDER BY kode_bidang_urusan ASC
", $input['tahun_anggaran']);
$data_risiko = $wpdb->get_results($sql_risiko, ARRAY_A);

$unit = (!empty($unit)) ? $unit : array();
?>
<style type="text/css">
    .table-contoh th {
        background-color: #cfe1faff; 
        text-align: center;
        padding: 8px;
        border: 1px solid #9c9c9cff;
        font-weight: bold;
    }
    .table-keterangan th {
        background-color: #fff3cd; 
        text-align: center;
        padding: 8px;
        border: 1px solid #9c9c9cff;
        font-weight: bold;
    }
</style>
<div class="container-md">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <h1 class="text-center" style="margin:3rem;">
            KODE RISIKO<br>Tahun Anggaran <?php echo $input['tahun_anggaran']; ?>
        </h1>
        <div class="row">
            <!-- Contoh Kode Risiko -->
            <div class="col-md-7">
                <table class="table-contoh" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word;">
                    <thead>
                        <tr>
                            <th colspan="5" class="text-center">CONTOH KODE RISIKO</th>
                        </tr>
                        <tr>
                            <th class="text-center">Tingkat Risiko</th>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Jenis Risiko</th>
                            <th class="text-center">Kode Perangkat Daerah</th>
                            <th class="text-center">Kode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">RSP-</td>
                            <td class="text-center"><?php echo substr($input['tahun_anggaran'], -2); ?>-</td>
                            <td class="text-center"><?php echo $data_risiko[0]['kode_bidang_urusan']; ?>-</td>
                            <td class="text-center"><?php echo $unit[0]['kode_skpd']; ?></td>
                            <td class="text-center">RSP-<?php echo substr($input['tahun_anggaran'], -2); ?>-<?php echo $data_risiko[0]['kode_bidang_urusan']; ?>-<?php echo $unit[0]['kode_skpd']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-center">RSO-</td>
                            <td class="text-center"><?php echo substr($input['tahun_anggaran'], -2); ?>-</td>
                            <td class="text-center"><?php echo $data_risiko[0]['kode_bidang_urusan']; ?>-</td>
                            <td class="text-center"><?php echo $unit[0]['kode_skpd']; ?></td>
                            <td class="text-center">RSO-<?php echo substr($input['tahun_anggaran'], -2); ?>-<?php echo $data_risiko[0]['kode_bidang_urusan']; ?>-<?php echo $unit[0]['kode_skpd']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-center">ROO-</td>
                            <td class="text-center"><?php echo substr($input['tahun_anggaran'], -2); ?>-</td>
                            <td class="text-center"><?php echo $data_risiko[0]['kode_bidang_urusan']; ?>-</td>
                            <td class="text-center"><?php echo $unit[0]['kode_skpd']; ?></td>
                            <td class="text-center">ROO-<?php echo substr($input['tahun_anggaran'], -2); ?>-<?php echo $data_risiko[0]['kode_bidang_urusan']; ?>-<?php echo $unit[0]['kode_skpd']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Keterangan Tingkat Risiko -->
            <div class="col-md-5">
                <table class="table-keterangan" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word;">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">KETERANGAN TINGKAT RISIKO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" style="width:50%;">RSP</td>
                            <td class="text-center" style="width:50%;">Strategis Pemda</td>
                        </tr>
                        <tr>
                            <td class="text-center">RSO</td>
                            <td class="text-center">Strategis OPD</td>
                        </tr>
                        <tr>
                            <td class="text-center">ROO</td>
                            <td class="text-center">Operasional OPD</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div style="padding: 10px;margin:0 0 3rem 0;">
    <div class="container-md">
        <div class="row">
            <div class="col-md-6">
                <table cellspacing="0" cellpadding="6">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="2">JENIS RISIKO</th>
                        </tr>                
                        <tr>
                            <th class="text-center">Kode Risiko</th>
                            <th class="text-center">Penjelasan</th>
                        </tr>
                    </thead>                
                    <tbody>
                        <?php foreach ($data_risiko as $risiko) { ?>
                            <tr>
                                <td class="text-center"><?php echo $risiko['kode_bidang_urusan']; ?></td> 
                                <td><?php echo $risiko['nama_bidang_urusan']; ?></td>
                            </tr>                
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table cellspacing="0" cellpadding="6">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="2">ENTITAS YANG MENILAI</th>
                        </tr>
                        <tr>
                            <th class="text-center">Kode SKPD</th>
                            <th class="text-center">Nama SKPD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php   
                        foreach ($unit as $row) { ?>
                            <tr>
                                <td><?php echo $row['kode_skpd']; ?></td>
                                <td><?php echo $row['nama_skpd']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>