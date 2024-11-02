<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
global $wpdb;

$id_unit = '';
if (!empty($_GET) && !empty($_GET['id_unit'])) {
    $id_unit = $_GET['id_unit'];
}

$id_jadwal_lokal = '';
if (!empty($_GET) && !empty($_GET['id_jadwal_lokal'])) {
    $id_jadwal_lokal = $_GET['id_jadwal_lokal'];
}

$input = shortcode_atts(array(
    'id_skpd' => $id_unit,
    'id_jadwal_lokal' => $id_jadwal_lokal,
    'tahun_anggaran' => '2022'
), $atts);

$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
    SELECT 
        j.nama AS nama_jadwal,
        j.tahun_anggaran,
        j.status,
        j.status_jadwal_pergeseran,
        t.nama_tipe 
    FROM `data_jadwal_lokal` j
    INNER JOIN `data_tipe_perencanaan` t on t.id=j.id_tipe 
    WHERE j.id_jadwal_lokal=%d", $id_jadwal_lokal));

$_suffix = '';
$where_jadwal = '';
if ($jadwal_lokal->status == 1) {
    $_suffix = '_history';
    $where_jadwal = ' AND id_jadwal=' . $wpdb->prepare("%d", $id_jadwal_lokal);
}
$input['tahun_anggaran'] = $jadwal_lokal->tahun_anggaran;

$_suffix_sipd = '';
if (strpos($jadwal_lokal->nama_tipe, '_sipd') == false) {
    $_suffix_sipd = '_lokal';
}

$nama_skpd = '';
if ($input['id_skpd'] == 'all') {
    $data_skpd = $wpdb->get_results($wpdb->prepare("
        select 
            id_skpd,
            kode_skpd,
            nama_skpd 
        from data_unit
        where tahun_anggaran=%d
            and active=1
        order by kode_skpd ASC
    ", $input['tahun_anggaran']), ARRAY_A);
} else {
    $data_skpd = array(array('id_skpd' => $input['id_skpd']));
    $nama_skpd = $wpdb->get_var($wpdb->prepare("
        SELECT 
            CONCAT(kode_skpd, ' ',nama_skpd)
        FROM data_unit
        WHERE tahun_anggaran=%d
            and active=1
            and id_skpd=%d
    ", $input['tahun_anggaran'], $input['id_skpd']));
    if (!empty($nama_skpd)) {
        $nama_skpd = '<br>' . $nama_skpd;
    } else {
        die('<h1 class="text-center">SKPD tidak ditemukan!</h1>');
    }
}
$nama_pemda = get_option('_crb_daerah');
$nama_excel = 'REKAP BELANJA PER PERANGKAT DAERAH<br>TAHUN ANGGARAN ' . $input['tahun_anggaran'] . '<br>' . strtoupper($nama_pemda) . $nama_skpd;

$body = '';
$total_operasi = 0;
$total_modal = 0;
$total_tak_terduga = 0;
$total_transfer = 0;
$total_all = 0;
$total_operasi_murni = 0;
$total_modal_murni = 0;
$total_tak_terduga_murni = 0;
$total_transfer_murni = 0;
$total_all_murni = 0;
$data_all = array();
foreach ($data_skpd as $skpd) {
    // if(count($data_all) > 10){
    //     continue;
    // }
    $sql = "
        SELECT 
            *
        FROM data_sub_keg_bl" . $_suffix_sipd . "" . $_suffix . "
        WHERE id_sub_skpd=%d
            AND tahun_anggaran=%d
            AND active=1
            " . $where_jadwal . "
            ORDER BY kode_giat ASC, kode_sub_giat ASC";
    $subkeg = $wpdb->get_results($wpdb->prepare($sql, $skpd['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
    if (empty($data_all[$skpd['id_skpd']])) {
        $data_all[$skpd['id_skpd']] = array(
            'id' => $skpd['id_skpd'],
            'kode' => $skpd['kode_skpd'],
            'nama' => $skpd['nama_skpd'],
            'operasi' => 0,
            'modal' => 0,
            'tak_terduga' => 0,
            'transfer' => 0,
            'total' => 0,
            'operasi_murni' => 0,
            'modal_murni' => 0,
            'tak_terduga_murni' => 0,
            'transfer_murni' => 0,
            'total_murni' => 0,
            'data' => array(),
            'query_sub' => $wpdb->last_query,
            'query_rinc' => array()
        );
    }
    foreach ($subkeg as $kk => $sub) {
        $where_jadwal_new = '';
        if (!empty($where_jadwal)) {
            $where_jadwal_new = str_replace('AND id_jadwal', 'AND r.id_jadwal', $where_jadwal);
        }
        $rincian_all = $wpdb->get_results($wpdb->prepare("
            SELECT 
                r.rincian_murni,
                r.rincian,
                r.kode_akun
            FROM data_rka" . $_suffix_sipd . "" . $_suffix . " r
            WHERE r.tahun_anggaran=%d
                AND r.active=1
                AND r.kode_sbl=%s
                " . $where_jadwal_new . "
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

        $data_all[$skpd['id_skpd']]['query_rinc'][] = $wpdb->last_query;

        foreach ($rincian_all as $rincian) {

            $data_all[$skpd['id_skpd']]['total'] += $rincian['rincian'];
            $data_all[$skpd['id_skpd']]['total_murni'] += $rincian['rincian_murni'];
            $rek = explode('.', $rincian['kode_akun']);
            $tipe_belanja = $rek[0] . '.' . $rek[1];
            if ($tipe_belanja == '5.1') {
                $data_all[$skpd['id_skpd']]['operasi'] += $rincian['rincian'];
                $data_all[$skpd['id_skpd']]['operasi_murni'] += $rincian['rincian_murni'];
            } else if ($tipe_belanja == '5.2') {
                $data_all[$skpd['id_skpd']]['modal'] += $rincian['rincian'];
                $data_all[$skpd['id_skpd']]['modal_murni'] += $rincian['rincian_murni'];
            } else if ($tipe_belanja == '5.3') {
                $data_all[$skpd['id_skpd']]['tak_terduga'] += $rincian['rincian'];
                $data_all[$skpd['id_skpd']]['tak_terduga_murni'] += $rincian['rincian_murni'];
            } else if ($tipe_belanja == '5.4') {
                $data_all[$skpd['id_skpd']]['transfer'] += $rincian['rincian'];
                $data_all[$skpd['id_skpd']]['transfer_murni'] += $rincian['rincian_murni'];
            }
        }
    }
}

if(!empty($_GET) && !empty($_GET['debug'])){
    print_r($data_all);
}

$counter = 1;
foreach ($data_all as $skpd) {
    if ($jadwal_lokal->status_jadwal_pergeseran == 'tidak_tampil') {
        $body .= '
            <tr data-id="' . $skpd['id'] . '">
                <td class="text-center">' . $counter . '</td>
                <td>' . $skpd['nama'] . '</td>
                <td class="text-right">' . $this->_number_format($skpd['operasi']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['modal']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['tak_terduga']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['transfer']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['total']) . '</td>
            </tr>';
            $counter++;
    } else {
        $body .= '
            <tr data-id="' . $skpd['id'] . '">
                <td class="text-center">' . $counter . '</td>
                <td>' . $skpd['nama'] . '</td>
                <td class="text-right">' . $this->_number_format($skpd['operasi_murni']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['modal_murni']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['tak_terduga_murni']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['transfer_murni']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['total_murni']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['operasi']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['modal']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['tak_terduga']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['transfer']) . '</td>
                <td class="text-right">' . $this->_number_format($skpd['total']) . '</td>
            </tr>';
        $counter++;
    }

    $total_all += $skpd['total'];
    $total_operasi += $skpd['operasi'];
    $total_modal += $skpd['modal'];
    $total_tak_terduga += $skpd['tak_terduga'];
    $total_transfer += $skpd['transfer'];
    $total_all_murni += $skpd['total_murni'];
    $total_operasi_murni += $skpd['operasi_murni'];
    $total_modal_murni += $skpd['modal_murni'];
    $total_tak_terduga_murni += $skpd['tak_terduga_murni'];
    $total_transfer_murni += $skpd['transfer_murni'];
}
?>
<div id="cetak" title="<?php echo $nama_excel; ?>" style="padding: 5px; overflow: auto;">
    <h1 class="text-center"><?php echo $nama_excel; ?></h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center align-middle" rowspan="2">No</th>
                <th class="text-center align-middle" rowspan="2">Perangkat Daerah</th>
                <?php if ($jadwal_lokal->status_jadwal_pergeseran == 'tidak_tampil') : ?>
                    <th class="text-center align-middle" colspan="5">Belanja</th>
                <?php else : ?>
                    <th class="text-center align-middle" colspan="5">Sebelum Perubahan</th>
                    <th class="text-center align-middle" colspan="5">Sesudah Perubahan</th>
                <?php endif; ?>
            </tr>
            <tr>
                <?php if ($jadwal_lokal->status_jadwal_pergeseran != 'tidak_tampil') : ?>
                    <th class="text-center align-middle">Belanja Operasi</th>
                    <th class="text-center align-middle">Belanja Modal</th>
                    <th class="text-center align-middle">Belanja Tak Terduga</th>
                    <th class="text-center align-middle">Belanja Transfer</th>
                    <th class="text-center align-middle">Jumlah Belanja</th>
                <?php endif; ?>
                <th class="text-center align-middle">Belanja Operasi</th>
                <th class="text-center align-middle">Belanja Modal</th>
                <th class="text-center align-middle">Belanja Tak Terduga</th>
                <th class="text-center align-middle">Belanja Transfer</th>
                <th class="text-center align-middle">Jumlah Belanja</th>
            </tr>
            <tr>
                <th class="text-center" style="font-size:small;line-height:0pt">1</th>
                <th class="text-center" style="font-size:small;line-height:0pt">2</th>
                <th class="text-center" style="font-size:small;line-height:0pt">3</th>
                <th class="text-center" style="font-size:small;line-height:0pt">4</th>
                <th class="text-center" style="font-size:small;line-height:0pt">5</th>
                <th class="text-center" style="font-size:small;line-height:0pt">6</th>
                <th class="text-center" style="font-size:small;line-height:0pt">7 = (3+4+5+6)</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $body; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-center">Total</th>
                <?php if ($jadwal_lokal->status_jadwal_pergeseran != 'tidak_tampil') : ?>
                    <th class="text-right"><?php echo $this->_number_format($total_operasi_murni); ?></th>
                    <th class="text-right"><?php echo $this->_number_format($total_modal_murni); ?></th>
                    <th class="text-right"><?php echo $this->_number_format($total_tak_terduga_murni); ?></th>
                    <th class="text-right"><?php echo $this->_number_format($total_transfer_murni); ?></th>
                    <th class="text-right"><?php echo $this->_number_format($total_all_murni); ?></th>
                <?php endif; ?>
                <th class="text-right"><?php echo $this->_number_format($total_operasi); ?></th>
                <th class="text-right"><?php echo $this->_number_format($total_modal); ?></th>
                <th class="text-right"><?php echo $this->_number_format($total_tak_terduga); ?></th>
                <th class="text-right"><?php echo $this->_number_format($total_transfer); ?></th>
                <th class="text-right"><?php echo $this->_number_format($total_all); ?></th>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        run_download_excel();
    });
</script>