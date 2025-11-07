<?php
global $wpdb;

if (!defined('WPINC')) {
    die;
}

$input = shortcode_atts(array(
    'tahun_anggaran' => '2023'
), $atts);

if (!empty($_GET) && !empty($_GET['id_skpd'])) {
    $id_skpd = $_GET['id_skpd'];
}
$get_jadwal = $wpdb->get_row(
    $wpdb->prepare('
        SELECT 
            id_jadwal_lokal
        FROM data_jadwal_lokal
        WHERE id_tipe = %d
            AND tahun_anggaran = %d
    ', 20, $input['tahun_anggaran']),
    ARRAY_A
);
$data_jadwal = $this->get_data_jadwal_by_id_jadwal_lokal($get_jadwal);
$timezone = get_option('timezone_string');
$nama_pemda = get_option('_crb_daerah');
$pemda = explode(" ", $nama_pemda);
array_shift($pemda);
$pemda = implode(" ", $pemda);
$list_bulan = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
];

$bulan = $list_bulan[date('n')];
$sql_unit = $wpdb->prepare("
    SELECT 
        *
    FROM data_unit 
    WHERE tahun_anggaran=%d
        AND id_skpd =%d
        AND active=1
    order by id_skpd ASC
    ", $input['tahun_anggaran'], $id_skpd);
$unit = $wpdb->get_results($sql_unit, ARRAY_A);
$unit_utama = $unit;
if ($unit[0]['id_unit'] != $unit[0]['id_skpd']) {
    $sql_unit_utama = $wpdb->prepare("
        SELECT 
            *
        FROM data_unit
        WHERE tahun_anggaran=%d
            AND id_skpd=%d
            AND active=1
        order by id_skpd ASC
        ", $input['tahun_anggaran'], $unit[0]['id_unit']);
    $unit_utama = $wpdb->get_results($sql_unit_utama, ARRAY_A);
}

$unit = (!empty($unit)) ? $unit : array();
$nama_skpd = (!empty($unit[0]['nama_skpd'])) ? $unit[0]['nama_skpd'] : '-';
$nama_kepala = (!empty($unit[0]['namakepala'])) ? $unit[0]['namakepala'] : '-';
$nip_kepala = (!empty($unit[0]['nipkepala'])) ? $unit[0]['nipkepala'] : '-';
$pangkat_kepala = (!empty($unit[0]['pangkatkepala'])) ? $unit[0]['pangkatkepala'] : '-';
$nama_pemda = get_option('_crb_daerah');
if (empty($nama_pemda) || $nama_pemda == 'false') {
    $nama_pemda = '';
}

$cek_jadwal_renja = $wpdb->get_results(
    $wpdb->prepare('
        SELECT 
            *
        FROM data_jadwal_lokal
        WHERE status = %d
            AND id_tipe = %d
            AND tahun_anggaran = %d
    ', 0, 16, $input['tahun_anggaran']),
    ARRAY_A
);
$id_jadwal = 0;
$tahun_renja = 0;
if (!empty($cek_jadwal_renja)) {
    foreach ($cek_jadwal_renja as $jadwal_renja) {
        $id_jadwal = $jadwal_renja['relasi_perencanaan'];
        $tahun_renja = $jadwal_renja['tahun_anggaran'];

        $cek_jadwal_renstra = $wpdb->get_results(
            $wpdb->prepare('
                SELECT 
                    *
                FROM data_jadwal_lokal
                WHERE id_jadwal_lokal = %d
            ', $id_jadwal),
            ARRAY_A
        );
        $lama_pelaksanaan = 5;
        $awal_renstra = 0;
        $akhir_renstra = 0;
        if (!empty($cek_jadwal_renstra)) {
            foreach ($cek_jadwal_renstra as $jadwal_renstra) {
                $lama_pelaksanaan = $jadwal_renstra['lama_pelaksanaan'];
                $awal_renstra = $jadwal_renstra['tahun_anggaran'];
                $akhir_renstra = $awal_renstra + $lama_pelaksanaan - 1;
            }
        }
    }
}
$data_tujuan_renstra = $wpdb->get_results($wpdb->prepare("
    SELECT 
        * 
    FROM data_renstra_tujuan 
    WHERE id_unit = %d 
        AND active = 1 
        AND id_jadwal = %d 
    ORDER BY id ASC
", $id_skpd, $id_jadwal),
ARRAY_A);

$group_tujuan_teks = array();
if(!empty($data_tujuan_renstra)){
    foreach($data_tujuan_renstra as $t){
        $get_teks = trim($t['tujuan_teks']);
        if(!empty($get_teks) && !in_array($get_teks, $group_tujuan_teks)){
            $group_tujuan_teks[] = $get_teks;
        }
    }
}

$tujuan_teks = '';
if(!empty($group_tujuan_teks)){
    $tujuan_teks .= '<ol>';
    foreach($group_tujuan_teks as $tujuan){
        $tujuan_teks .= '<li>' . $tujuan . '</li>';
    }
    $tujuan_teks .= '</ol>';
}

$data_sasaran_renstra = $wpdb->get_results($wpdb->prepare("
    SELECT DISTINCT 
        s.* 
    FROM data_renstra_sasaran AS s
    INNER JOIN data_renstra_tujuan AS t
            ON s.kode_tujuan = t.id_unik
    WHERE t.active = 1
        AND s.active = 1
        AND s.id_unit = %d 
        AND s.id_jadwal = %d 
    ORDER BY s.kode_bidang_urusan, s.id_unik_indikator ASC
", $id_skpd, $id_jadwal),
ARRAY_A);

$group_sasaran_teks = array();
if(!empty($data_sasaran_renstra)){
    foreach($data_sasaran_renstra as $s){
        $get_teks = trim($s['sasaran_teks']);
        if(!empty($get_teks) && !in_array($get_teks, $group_tujuan_teks) && !in_array($get_teks, $group_sasaran_teks)){
            $group_sasaran_teks[] = $get_teks;
        }
    }
}

$sasaran_teks = '';
if(!empty($group_sasaran_teks)){
    $sasaran_teks .= '<ol>';
    foreach($group_sasaran_teks as $sasaran){
        $sasaran_teks .= '<li>' . $sasaran . '</li>';
    }
    $sasaran_teks .= '</ol>';
}

// untuk get data iku sakip

$get_iku_message = "Parameter Data Get Data IKU Ada Yang Kosong!";
$show_alert_iku = 0;

$_POST['id_skpd']   = $id_skpd;
$_POST['id_jadwal'] = $id_jadwal;
$_POST['api_key']   = get_option('_crb_api_key_extension');

$get_data_iku = $this->get_data_iku(1);
$data_iku = json_decode(json_encode($get_data_iku), true);
if (!empty($data_iku['message'])) {
    $get_iku_message = $data_iku['message'];
}
if (!empty($data_iku['is_error']) && $data_iku['is_error']) {
    $show_alert_iku  = 1;
    $get_iku_message = "Ada Error Saat Mengakses Api WP SAKIP | Pesan: " . $data_iku['message'];
}
$html = '';
if (!empty($data_iku['data'])) {
    foreach ($data_iku['data'] as $iku) {
        $id_unik_indikators = explode(",", $iku['id_unik_indikator']);

        $get_data_tujuan = $wpdb->get_results($wpdb->prepare("
            SELECT 
                *
            FROM data_renstra_tujuan
            WHERE id_unit = %d
                AND id_unik = %s
                AND id_unik_indikator IS NULL
                AND active = 1
                AND id_jadwal = %d
            ORDER BY id ASC
        ", $id_skpd, $iku['kode_sasaran'], $id_jadwal), ARRAY_A);

        foreach ($get_data_tujuan as $tujuan) {
            $get_data_program_tujuan = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    *
                FROM data_renstra_program
                WHERE id_unit = %d
                    AND kode_tujuan = %s
                    AND active = 1
                    AND id_jadwal = %d
                ORDER BY id ASC
            ", $id_skpd, $tujuan['id_unik'], $id_jadwal), ARRAY_A);

            $nama_program = array();
            foreach ($get_data_program_tujuan as $p) {
                $nama_program[] = preg_replace('/^[A-Z0-9\.]+\s+/', '', $p['nama_program']);
            }
            $nama_program = array_unique($nama_program);
            $program_iku_teks = !empty($nama_program) ? implode("; ", $nama_program) : '';

            $tujuan_iku_teks = $tujuan['tujuan_teks'] ?: '';
            $indikator_list = array();

            foreach ($id_unik_indikators as $id_unik_indikator) {
                $get_data_indikator_tujuan = $wpdb->get_results($wpdb->prepare("
                    SELECT 
                        *
                    FROM data_renstra_tujuan
                    WHERE id_unit = %d
                        AND id_unik = %s
                        AND id_unik_indikator = %s
                        AND active = 1
                        AND id_jadwal = %d
                    ORDER BY id ASC
                ", $id_skpd, $tujuan['id_unik'], $id_unik_indikator, $id_jadwal), ARRAY_A);

                foreach ($get_data_indikator_tujuan as $indikator) {
                    $indikator_list[] = $indikator;
                }
            }

            $rowspan = count($indikator_list);
            foreach ($indikator_list as $i => $indikator) {
                $indikator_teks = $indikator['indikator_teks'] ?: '';
                $target = $indikator['target_1'] ?: '';
                $realisasi = $indikator['realisasi_target_1'] ?: '';

                $html .= '<tr>';
                if ($i == 0) {
                    $html .= '<td rowspan="' . $rowspan . '">' . $tujuan_iku_teks . '</td>';
                }
                $html .= '<td>' . $indikator_teks . '</td>
                          <td>' . $target . '</td>
                          <td>' . $realisasi . '</td>';
                if ($i == 0) {
                    $html .= '<td rowspan="' . $rowspan . '">' . $program_iku_teks . '</td>';
                }
                $html .= '</tr>';
            }
        }

        $get_data_sasaran = $wpdb->get_results($wpdb->prepare("
            SELECT 
                *
            FROM data_renstra_sasaran
            WHERE id_unit = %d
                AND id_unik = %s
                AND id_unik_indikator IS NULL
                AND active = 1
                AND id_jadwal = %d
            ORDER BY id ASC
        ", $id_skpd, $iku['kode_sasaran'], $id_jadwal), ARRAY_A);

        foreach ($get_data_sasaran as $sasaran) {
            $get_data_program_sasaran = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    *
                FROM data_renstra_program
                WHERE id_unit = %d
                    AND kode_sasaran = %s
                    AND active = 1
                    AND id_jadwal = %d
                ORDER BY id ASC
            ", $id_skpd, $sasaran['id_unik'], $id_jadwal), ARRAY_A);

            $nama_program = array();
            foreach ($get_data_program_sasaran as $p) {
                $nama_program[] = preg_replace('/^[A-Z0-9\.]+\s+/', '', $p['nama_program']);
            }
            $nama_program = array_unique($nama_program);
            $program_iku_teks = !empty($nama_program) ? implode("; ", $nama_program) : '';

            $sasaran_iku_teks = $sasaran['sasaran_teks'] ?: '';
            $indikator_list = array();

            foreach ($id_unik_indikators as $id_unik_indikator) {
                $get_data_indikator_sasaran = $wpdb->get_results($wpdb->prepare("
                    SELECT 
                        *
                    FROM data_renstra_sasaran
                    WHERE id_unit = %d
                        AND id_unik = %s
                        AND id_unik_indikator = %s
                        AND active = 1
                        AND id_jadwal = %d
                    ORDER BY id ASC
                ", $id_skpd, $sasaran['id_unik'], $id_unik_indikator, $id_jadwal), ARRAY_A);

                foreach ($get_data_indikator_sasaran as $indikator) {
                    $indikator_list[] = $indikator;
                }
            }

            $rowspan = count($indikator_list);
            foreach ($indikator_list as $i => $indikator) {
                $indikator_teks = $indikator['indikator_teks'] ?: '';
                $target = $indikator['target_1'] ?: '';
                $realisasi = $indikator['realisasi_target_1'] ?: '';

                $html .= '<tr>';
                if ($i == 0) {
                    $html .= '<td rowspan="' . $rowspan . '">' . $sasaran_iku_teks . '</td>';
                }
                $html .= '<td>' . $indikator_teks . '</td>
                          <td>' . $target . '</td>
                          <td>' . $realisasi . '</td>';
                if ($i == 0) {
                    $html .= '<td rowspan="' . $rowspan . '">' . $program_iku_teks . '</td>';
                }
                $html .= '</tr>';
            }
        }
    }
} else {
    $html = '<tr><td colspan="6" class="text-center">Tidak ada data IKU/Program</td></tr>';
}
$data_program_renja = $wpdb->get_results(
    $wpdb->prepare("
        SELECT 
            *
        FROM data_sub_keg_bl 
        WHERE id_sub_skpd=%d
          AND active=1 
          AND tahun_anggaran=%d 
        GROUP BY kode_program, id_sub_skpd  
        ORDER BY kode_program
    ", $id_skpd, $tahun_renja),
    ARRAY_A
);
$program_renja_teks = '';
if(!empty($data_program_renja)){
    $program_renja_teks .= '<ol>';
    foreach($data_program_renja as $program_renja){
        $program_renja_teks .= '<li>' . $program_renja['nama_program'] . '</li>';
    }
    $program_renja_teks .= '</ol>';
}
?>
<style type="text/css">
    .wrap-table {
        overflow: auto;
        max-height: 100vh;
        width: 100%;
    }

    .btn-action-group {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btn-action-group .btn {
        margin: 0 5px;
    }

    .table_manrisk_konteks_resiko thead {
        position: sticky;
        top: -6px;
    }

    .table_manrisk_konteks_resiko thead th {
        vertical-align: middle;
    }

    .table_manrisk_konteks_resiko tfoot {
        position: sticky;
        bottom: 0;
    }
</style>
<div class="container-md">
    <div class="cetak" style="padding: 5px; overflow: auto;">
        <div style="margin:0 0 3rem 0;">
            <h1 class="text-center table-title">
                Konteks Risiko <br><?php echo $nama_skpd; ?><br>Tahun <?php echo $input['tahun_anggaran']; ?>
            </h1>            
            <div id='aksi-wpsipd'></div>
            <div class="wrap-table">
                <table id="cetak" title="Konteks Risiko SKPD" class="table_manrisk_konteks_resiko table-bordered" cellpadding="5" cellspacing="0" border="1" width="100%">
                    <thead style="background: #ffc491; text-align:center;">
                        <tr>
                            <th style="width: 20%;">Sumber Data</th>
                            <th>Perubahan Renstra <?php echo ucwords(strtolower($nama_skpd)).' '. $awal_renstra . ' - ' . $akhir_renstra; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tujuan Strategis PD</td>
                            <td><?php echo $tujuan_teks; ?></td>
                        </tr>
                        <tr>
                            <td>Sasaran Strategis PD</td>
                            <td><?php echo $sasaran_teks; ?></td>
                        </tr>
                        <tr>
                            <td>Indikator Kinerja IKU/Program</td>
                            <td>
                                <table border="1" width="100%" cellpadding="5" cellspacing="0">
                                    <thead>
                                        <tr style="background: #f2f2f2; text-align:center;">
                                            <th>IKU</th>
                                            <th>INDIKATOR IKU</th>
                                            <th style="width:120px;">TARGET</th>
                                            <th style="width:120px;">REALISASI</th>
                                            <th style="width:380px;">PROGRAM PENDUKUNG</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo $html; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>Program yang akan dilakukan penilaian risiko</td>
                            <td><?php echo $program_renja_teks; ?></td>
                        </tr>
                        <tr>
                            <td style="border:none;"></td>
                            <td style="border:none;">
                                <table border="1" width="100%" cellpadding="5" cellspacing="0" style="border:none;">
                                    <thead>                                        
                                        <tr>
                                            <th style="border:none;"></th>
                                            <th style="border:none;"></th>
                                            <th style="border:none; width:120px;"></th>
                                            <th style="border:none; width:120px;"></th>
                                            <th style="border:none; width:380px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <td style="border:none;" colspan="3"></td>
                                        <td colspan="2" style="border:none; text-align:center; background:transparent !important;"
                                            contenteditable="true">
                                            <?php echo $pemda; ?>, <?php echo $bulan . ' ' . $input['tahun_anggaran']; ?><br>Kepala <?php echo ucwords(strtolower($nama_skpd)) . '<br>' . $nama_pemda; ?><br><br><br><br><b><u><?php echo $nama_kepala; ?></u></b><br><?php echo $pangkat_kepala . '<br>NIP. ' . $nip_kepala; ?>
                                        </td>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        run_download_excel('', '#aksi-wpsipd');
    });
</script>