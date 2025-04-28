<?php
// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}
global $wpdb;
$input = shortcode_atts(array(
    'tahun_anggaran' => '2022'
), $atts);

if (!empty($_GET) && !empty($_GET['id_skpd'])) {
    $id_skpd = $_GET['id_skpd'];
}

$prefix_history = '';
$additional_condition = "AND active = 1";

if (!empty($_GET['id_jadwal'])) {
    $current_jadwal = $wpdb->get_row(
        $wpdb->prepare('
			SELECT 
				id_jadwal_lokal,
				nama
			FROM data_jadwal_lokal
			WHERE id_jadwal_lokal = %d
			  AND id_tipe = 19
			  AND tahun_anggaran = %d
			  AND status = 1
		', $_GET['id_jadwal'], $input['tahun_anggaran']),
        ARRAY_A
    );

    $prefix_history = '_history';
    $additional_condition = "";

    if (empty($current_jadwal)) {
        die('Jadwal Tidak Valid!');
    }
}
$body = '';
$body2 = '';
$nama_pemda = get_option('_crb_daerah');
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';

$sql_unit = $wpdb->prepare("
    SELECT 
        *
    FROM data_unit 
    WHERE 
        tahun_anggaran=%d
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
        WHERE 
            tahun_anggaran=%d
            AND id_skpd=%d
            AND active=1
        order by id_skpd ASC
        ", $input['tahun_anggaran'], $unit[0]['id_unit']);
    $unit_utama = $wpdb->get_results($sql_unit_utama, ARRAY_A);
}

$unit = (!empty($unit)) ? $unit : array();
$nama_skpd = (!empty($unit[0]['nama_skpd'])) ? $unit[0]['nama_skpd'] : '-';

$sql_anggaran = $wpdb->prepare("
    SELECT
        s.*,
        sum(r.total_harga) as total_rincian,
        sum(r.rincian_murni) as total_murni,
        r.nama_akun,
        r.kode_akun
    FROM data_rka r
    left join data_sub_keg_bl s on s.kode_sbl=r.kode_sbl
        AND s.tahun_anggaran=r.tahun_anggaran
        AND s.active=r.active
    WHERE r.tahun_anggaran=%d
        AND r.active=1
        AND s.id_sub_skpd=%d
    GROUP by r.kode_akun, s.kode_sbl
    ", $input["tahun_anggaran"], $id_skpd);
$data_anggaran = $wpdb->get_results($sql_anggaran, ARRAY_A);

$pagu_efisiensi = 0;
$total_all_pagu_sebelum = 0;
$total_all_pagu = 0;
$total_all_efisiensi = 0;
$total_all_selisih = 0;
$total_all_rek_pagu_sebelum = 0;
$total_all_rek_pagu = 0;
$total_all_rek_efisiensi = 0;
$total_all_rek_selisih = 0;
$no = 1;
$no2 = 1;
if (!empty($data_anggaran)) {
    $sub_keg_all = array();
    $rek_all = array();
    foreach ($data_anggaran as $v_anggaran) {
        $kode_akun = $v_anggaran['kode_akun'];
        $kode_sbl = $v_anggaran['kode_sbl'];

        $kode_sbl_kas = explode('.', $kode_sbl);
        $kode_sbl_kas = $kode_sbl_kas[0] . '.' . $kode_sbl_kas[0] . '.' . $kode_sbl_kas[1] . '.' . $v_anggaran['id_bidang_urusan'] . '.' . $kode_sbl_kas[2] . '.' . $kode_sbl_kas[3] . '.' . $kode_sbl_kas[4];
        $v_anggaran['kode_sbl'] = $kode_sbl_kas;

        //get per akun belanja
        if (empty($sub_keg_all[$kode_sbl])) {
            $sub_keg_all[$kode_sbl] = array(
                'sub_keg' => array(),
                'data' => array()
            );
            $sub_keg_all[$kode_sbl]['sub_keg'] = $v_anggaran;
        }

        $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']] = array('pagu_murni' => 0, 'realisasi' => 0, 'pagu_efisiensi' => 0, 'keterangan' => '');

        $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['pagu_murni'] = $v_anggaran['total_murni'];

        $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['realisasi'] = $wpdb->get_var($wpdb->prepare("
            SELECT
                realisasi
            FROM data_realisasi_akun_sipd
            WHERE active=1
                AND tahun_anggaran=%d
                AND kode_akun=%s
                AND (
                    kode_sbl=%s
                    or kode_sbl=%s
                )
        ", $input["tahun_anggaran"], $kode_akun, $kode_sbl, $v_anggaran['kode_sbl']));

        $efisiensi = $wpdb->get_row(
            $wpdb->prepare("
				SELECT
					pagu_efisiensi,
					keterangan
				FROM data_efisiensi_belanja{$prefix_history}
				WHERE tahun_anggaran = %d
				AND (
					kode_sbl = %s
					OR kode_sbl = %s
				)
				{$additional_condition}
				AND kode_akun = %s
    		", $input["tahun_anggaran"], $kode_sbl, $v_anggaran['kode_sbl'], $v_anggaran['kode_akun']),
            ARRAY_A
        );
        if (empty($efisiensi)) {
            $efisiensi = array(
                'pagu_efisiensi' => 0,
                'keterangan' => ''
            );
        }
        $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['pagu_efisiensi'] = $efisiensi['pagu_efisiensi'];
        $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['keterangan'] = $efisiensi['keterangan'];

        $nama_akun = explode(' ', $v_anggaran['nama_akun']);
        unset($nama_akun[0]);
        $nama_akun = implode(' ', $nama_akun);
        if ($jenis === '' || $jenis === 'sipd') {
            $realisasi = $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['realisasi'];
        } else {
            $realisasi = 0;
        }
        $selisih = $v_anggaran['total_rincian'] - $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['pagu_efisiensi'];

        if (empty($rek_all[$kode_akun])) {
            $rek_all[$kode_akun] = [
                'nama_akun' => $nama_akun,
                'pagu_murni' => 0,
                'total_rincian' => 0,
                'pagu_efisiensi' => 0,
                'realisasi' => 0,
            ];
        }

        $rek_all[$kode_akun]['pagu_murni'] += floatval($v_anggaran['total_murni']);
        $rek_all[$kode_akun]['total_rincian'] += floatval($v_anggaran['total_rincian']);
        $rek_all[$kode_akun]['pagu_efisiensi'] += floatval($efisiensi['pagu_efisiensi']);
        $rek_all[$kode_akun]['realisasi'] += floatval($sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['realisasi']);

        $nama_sub_giat = explode(' ', $v_anggaran['nama_sub_giat']);
        unset($nama_sub_giat[0]);
        $nama_sub_giat = implode(' ', $nama_sub_giat);

        $body .= '
            <tr kode_sbl="' . $kode_sbl . '" kode_sbl_kas="' . $v_anggaran['kode_sbl'] . '">
                <td class="text_tengah">' . $no++ . '</td>
                <td class="text_tengah">' . $sub_keg_all[$kode_sbl]['sub_keg']['kode_urusan'] . '</td>
                <td class="">' . $sub_keg_all[$kode_sbl]['sub_keg']['nama_urusan'] . '</td>
                <td class="text_tengah">' . $sub_keg_all[$kode_sbl]['sub_keg']['kode_bidang_urusan'] . '</td>
                <td class="">' . $sub_keg_all[$kode_sbl]['sub_keg']['nama_bidang_urusan'] . '</td>
                <td class="text_tengah">' . $unit_utama[0]['kode_skpd'] . '</td>
                <td class="">' . $unit_utama[0]['nama_skpd'] . '</td>
                <td class="text_tengah">' . $unit[0]['kode_skpd'] . '</td>
                <td class="">' . $unit[0]['nama_skpd'] . '</td>
                <td class="text_tengah">' . $sub_keg_all[$kode_sbl]['sub_keg']['kode_program'] . '</td>
                <td class="">' . $sub_keg_all[$kode_sbl]['sub_keg']['nama_program'] . '</td>
                <td class="text_tengah">' . $sub_keg_all[$kode_sbl]['sub_keg']['kode_giat'] . '</td>
                <td class="">' . $sub_keg_all[$kode_sbl]['sub_keg']['nama_giat'] . '</td>
                <td class="text_tengah">' . $sub_keg_all[$kode_sbl]['sub_keg']['kode_sub_giat'] . '</td>
                <td class="">' . $nama_sub_giat . '</td>
                <td class="text_tengah">' . $v_anggaran['kode_akun'] . '</td>
                <td class="">' . $nama_akun . '</td>
                <td class="text_kanan">' . number_format($sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['pagu_murni'] ?? 0, 0, '.', ',') . '</td>
                <td class="text_kanan">' . number_format($v_anggaran['total_rincian'], 0, '.', ',') . '</td>
                <td class="text_kanan">' . number_format($sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['pagu_efisiensi'] ?? 0, 0, '.', ',') . '</td>
                <td class="text_kanan">' . number_format($selisih, 0, '.', ',') . '</td>
                <td class="text_kanan">' . $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['keterangan'] . '</td>';
        if (empty($_GET['id_jadwal'])) {
            $body .= '<td class="text_kanan"><button class="btn btn-sm btn-warning" onclick="edit_efisiensi(\'' . $v_anggaran['kode_sbl'] . '\', \'' . $v_anggaran['kode_akun'] . '\', \'' . $v_anggaran['total_rincian'] . '\'); return false;" href="#"><span class="dashicons dashicons-edit"></span></button></td>';
        }
        $body .= '</tr>';

        $total_all_pagu += $v_anggaran['total_rincian'];
        $total_all_pagu_sebelum += $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['pagu_murni'];
        $total_all_efisiensi += $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['pagu_efisiensi'];
        $total_all_selisih += $selisih;
    }

    foreach ($rek_all as $kode_akun => $data) {
        $selisih_rek = $data['total_rincian'] - $data['pagu_efisiensi'];

        $body2 .= '
            <tr>
                <td class="text_tengah">' . $no2++ . '</td>
                <td class="text_tengah">' . $kode_akun . '</td>
                <td class="">' . $data['nama_akun'] . '</td>
                <td class="text_kanan">' . number_format($data['pagu_murni'], 0, '.', ',') . '</td>
                <td class="text_kanan">' . number_format($data['total_rincian'], 0, '.', ',') . '</td>
                <td class="text_kanan">' . number_format($data['pagu_efisiensi'], 0, '.', ',') . '</td>
                <td class="text_kanan">' . number_format($selisih_rek, 0, '.', ',') . '</td>
            </tr>';

        $total_all_rek_pagu += $data['total_rincian'];
        $total_all_rek_pagu_sebelum += $data['pagu_murni'];
        $total_all_rek_efisiensi += $data['pagu_efisiensi'];
        $total_all_rek_selisih += $selisih_rek;
    }
}

?>
<style type="text/css">
    .wrap-table {
        overflow: auto;
        max-height: 100vh;
        width: 100%;
    }

    .table_data_efisiensi thead {
        position: sticky;
        top: -6px;
    }

    .table_data_efisiensi thead th {
        vertical-align: middle;
    }

    .table_data_efisiensi tfoot {
        position: sticky;
        bottom: 0;
    }

    .table_data_efisiensi tfoot th {
        vertical-align: middle;
    }

    .table_data_efisiensi_rekening thead {
        position: sticky;
        top: -6px;
    }

    .table_data_efisiensi_rekening thead th {
        vertical-align: middle;
    }

    .table_data_efisiensi_rekening tfoot {
        position: sticky;
        bottom: 0;
    }

    .table_data_efisiensi_rekening tfoot th {
        vertical-align: middle;
    }
</style>
<div class="container-md">
    <div class="cetak">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h2 style="text-align: center; margin: 0; font-weight: bold;">Efisiensi Belanja <br><?php echo $nama_skpd . '<br>Tahun Anggaran ' . $input['tahun_anggaran']; ?></h2>
            <?php if (!empty($_GET['id_jadwal'])) : ?>
                <h2 class="text-center"><?php echo $current_jadwal['nama']; ?></h2>
            <?php endif; ?>
            <div id='aksi-efisiensi-wpsipd'></div>
            <div class="wrap-table">
                <table id="cetak" title="Label Efisiensi Belanja" class="table table-bordered table_data_efisiensi">
                    <thead style="background: #ffc491;">
                        <tr>
                            <th style="width: 35px;" class="text-center">No</th>
                            <th style="width: 55px;" class="text-center">Kode Urusan</th>
                            <th style="width: 200px;" class="text-center">Nama Urusan</th>
                            <th style="width: 55px;" class="text-center">Kode Bidang Urusan</th>
                            <th style="width: 300px;" class="text-center">Nama Bidang Urusan</th>
                            <th style="width: 135px;" class="text-center">Kode SKPD</th>
                            <th style="width: 200px;" class="text-center">Nama SKPD</th>
                            <th style="width: 150px;" class="text-center">Kode Sub SKPD</th>
                            <th style="width: 200px;" class="text-center">Nama Sub SKPD</th>
                            <th style="width: 70px;" class="text-center">Kode Program</th>
                            <th style="width: 200px;" class="text-center">Nama Program</th>
                            <th style="width: 80px;" class="text-center">Kode Kegiatan</th>
                            <th style="width: 200px;" class="text-center">Nama Kegiatan</th>
                            <th style="width: 100px;" class="text-center">Kode Sub Kegiatan</th>
                            <th style="width: 200px;" class="text-center">Nama Sub Kegiatan</th>
                            <th style="width: 110px;" class="text-center">Kode Akun</th>
                            <th style="width: 200px;" class="text-center">Nama Akun</th>
                            <th style="width: 200px;" class="text-center">Total Sebelum</th>
                            <th style="width: 200px;" class="text-center">Total</th>
                            <th style="width: 200px;" class="text-center">Efisiensi</th>
                            <th style="width: 200px;" class="text-center">Selisih</th>
                            <th style="width: 200px;" class="text-center">Keterangan</th>
                            <?php if (empty($_GET['id_jadwal'])) : ?>
                                <th style="width: 200px;" class="text-center">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $body; ?>
                    </tbody>
                    <tfoot style="background: #ffc491;">
                        <tr>
                            <th colspan="17" class="text-center">Total</th>
                            <th class="text-right"><?php echo number_format($total_all_pagu_sebelum, 0, '.', ','); ?></th>
                            <th class="text-right"><?php echo number_format($total_all_pagu, 0, '.', ','); ?></th>
                            <th class="text-right"><?php echo number_format($total_all_efisiensi, 0, '.', ','); ?></th>
                            <th class="text-right"><?php echo number_format($total_all_selisih, 0, '.', ','); ?></th>
                            <th colspan="2" class="text-center"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="container-md">
    <div class="cetak">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h2 style="text-align: center; margin: 0; font-weight: bold;">Efisiensi Belanja per Rekening Belanja<br><?php echo $nama_skpd . '<br>Tahun Anggaran ' . $input['tahun_anggaran']; ?></h2>
            <div id='aksi-efisiensi-rekening-wpsipd'></div>
            <div class="wrap-table">
                <table id="cetak" title="Label Efisiensi Belanja" class="table table-bordered table_data_efisiensi_rekening">
                    <thead style="background: #ffc491;">
                        <tr>
                            <th style="width: 35px;" class="text-center">No</th>
                            <th style="width: 110px;" class="text-center">Kode Rekening</th>
                            <th style="width: 200px;" class="text-center">Nama Rekening</th>
                            <th style="width: 200px;" class="text-center">Total Sebelum</th>
                            <th style="width: 200px;" class="text-center">Total</th>
                            <th style="width: 200px;" class="text-center">Efisiensi</th>
                            <th style="width: 200px;" class="text-center">Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $body2; ?>
                    </tbody>
                    <tfoot style="background: #ffc491;">
                        <tr>
                            <th colspan="3" class="text-center">Total</th>
                            <th class="text-right"><?php echo number_format($total_all_pagu_sebelum, 0, '.', ','); ?></th>
                            <th class="text-right"><?php echo number_format($total_all_pagu, 0, '.', ','); ?></th>
                            <th class="text-right"><?php echo number_format($total_all_efisiensi, 0, '.', ','); ?></th>
                            <th class="text-right"><?php echo number_format($total_all_selisih, 0, '.', ','); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-tambah-efisiensi" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title" id="">Data Efisiensi Belanja</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" value="" id="id_data">
                    <input type="hidden" value="" id="kode_sbl">
                    <input type="hidden" value="" id="kode_akun">
                    <div class="form-group">
                        <label for="skpd">SKPD</label>
                        <input type="text" class="form-control" id="skpd" name="skpd" value="<?php echo $nama_skpd; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="nama_urusan">Nama Urusan</label>
                        <input type="text" class="form-control" id="nama_urusan" name="nama_urusan" disabled>
                    </div>
                    <div class="form-group">
                        <label for="bidang_urusan">Nama Bidang Urusan</label>
                        <input type="text" class="form-control" id="bidang_urusan" name="bidang_urusan" disabled>
                    </div>
                    <div class="form-group">
                        <label for="program">Program</label>
                        <input type="text" class="form-control" id="program" name="program" disabled>
                    </div>
                    <div class="form-group">
                        <label for="kegiatan">Kegiatan</label>
                        <input type="text" class="form-control" id="kegiatan" name="kegiatan" disabled>
                    </div>
                    <div class="form-group">
                        <label for="sub_kegiatan">Sub Kegiatan</label>
                        <input type="text" class="form-control" id="sub_kegiatan" name="sub_kegiatan" disabled>
                    </div>
                    <div class="form-group">
                        <label for="akun_belanja">Nama Akun</label>
                        <input type="text" class="form-control" id="akun_belanja" name="akun_belanja" disabled>
                    </div>
                    <div class="form-group">
                        <label for="pagu_murni">Pagu Sebelum</label>
                        <input type="text" class="form-control format_pagu" id="pagu_murni" name="pagu_murni" disabled>
                    </div>
                    <div class="form-group">
                        <label for="pagu">Pagu</label>
                        <input type="text" class="form-control format_pagu" id="pagu" name="pagu" disabled>
                    </div>
                    <div class="form-group">
                        <label for="pagu_efisiensi">Pagu Efisiensi</label>
                        <input type="text" class="form-control format_pagu" id="pagu_efisiensi" name="pagu_efisiensi"></input>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea type="text" class="form-control" id="keterangan" name="keterangan"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="simpan_data_efisiensi();">Simpan</button>
                <button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    jQuery(document).on('ready', function() {
        run_download_excel('', '#aksi-efisiensi-wpsipd');
        jQuery('.table_data_efisiensi').dataTable({
            aLengthMenu: [
                [5, 10, 25, 100, -1],
                [5, 10, 25, 100, "All"]
            ],
            iDisplayLength: -1
        });
        jQuery('.table_data_efisiensi_rekening').dataTable({
            aLengthMenu: [
                [5, 10, 25, 100, -1],
                [5, 10, 25, 100, "All"]
            ],
            iDisplayLength: -1
        });
        jQuery('.format_pagu').on('input', function() {
            var sanitized = jQuery(this).val().replace(/[^0-9]/g, '');

            if (!sanitized || parseInt(sanitized) === 0) {
                jQuery(this).val(sanitized);
                return;
            }

            var formatted = formatRupiah(sanitized);
            jQuery(this).val(formatted);
        });
    });

    function edit_efisiensi(kodesbl, kodeakun, pagu) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'edit_efisiensi',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'kodesbl': kodesbl,
                'kodeakun': kodeakun,
                'id_skpd': <?php echo $id_skpd; ?>,
                'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>
            },
            success: function(response) {
                if (response.status == 'success') {
                    let data = response.data;
                    jQuery("#kode_sbl").val(kodesbl);
                    jQuery("#kode_akun").val(kodeakun);
                    jQuery("#nama_urusan").val(data.kode_urusan + " " + data.nama_urusan);
                    jQuery("#bidang_urusan").val(data.kode_bidang_urusan + " " + data.nama_bidang_urusan);
                    jQuery("#program").val(data.kode_program + " " + data.nama_program);
                    jQuery("#kegiatan").val(data.kode_giat + " " + data.nama_giat);
                    jQuery("#sub_kegiatan").val(data.nama_sub_giat);

                    if (data.rka && data.rka.length > 0) {
                        jQuery("#akun_belanja").val(data.rka[0].nama_akun).trigger('input');
                        jQuery("#pagu_murni").val(parseInt(data.rka[0].rincian_murni || 0)).trigger('input');
                    } else {
                        jQuery("#akun_belanja").val('');
                    }
                    jQuery("#pagu").val(pagu).trigger('input');

                    if (data.efisiensi_belanja && data.efisiensi_belanja.length > 0) {
                        jQuery("#id_data").val(data.efisiensi_belanja[0].id || '');
                        jQuery("#pagu_efisiensi").val(parseInt(data.efisiensi_belanja[0].pagu_efisiensi || '')).trigger('input');
                        jQuery("#keterangan").val(data.efisiensi_belanja[0].keterangan || '');
                    } else {
                        jQuery("#id_data").val('');
                        jQuery("#pagu_efisiensi").val('');
                        jQuery("#keterangan").val('');
                    }

                    jQuery('#modal-tambah-efisiensi').modal('show');
                    jQuery('#wrap-loading').hide();
                } else {
                    alert(response.message);
                    jQuery('#wrap-loading').hide();
                }
            }
        });
    }

    function simpan_data_efisiensi(button) {
        let id_data = jQuery('#id_data').val();
        let kodesbl = jQuery('#kode_sbl').val();
        let kodeakun = jQuery('#kode_akun').val();
        let pagu_efisiensi = jQuery('#pagu_efisiensi').val().replace(/\./g, '')
        if (pagu_efisiensi == '') {
            return alert('Pagu Efisiensi tidak boleh kosong!');
        }
        let keterangan = jQuery('#keterangan').val();
        // if(keterangan == ''){
        // 	return alert('keterangan tidak boleh kosong!');
        // }
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'simpan_data_efisiensi',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'id_data': id_data,
                'kodesbl': kodesbl,
                'kodeakun': kodeakun,
                'pagu_efisiensi': pagu_efisiensi,
                'keterangan': keterangan,
                'id_skpd': <?php echo $id_skpd; ?>,
                'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
            },
            dataType: "json",
            success: function(res) {
                jQuery('#wrap-loading').hide();
                alert(res.message);
                if (res.status === 'success') {
                    jQuery('#modal-tambah-efisiensi').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat menyimpan data!');
            }
        });
    }
</script>