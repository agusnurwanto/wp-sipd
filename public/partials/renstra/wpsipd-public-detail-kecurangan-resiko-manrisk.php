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
//$data_jadwal = $this->get_data_jadwal_by_id_jadwal_lokal($get_jadwal);
$timezone = get_option('timezone_string');
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
$nama_pemda = get_option('_crb_daerah');
if (empty($nama_pemda) || $nama_pemda == 'false') {
    $nama_pemda = '';
}

$unit = $wpdb->get_row($wpdb->prepare("
    SELECT 
        id_skpd,
        nama_skpd,
        bidur_1,
        bidur_2,
        bidur_3
    FROM 
        data_unit
    WHERE 
        id_skpd = %d
        AND tahun_anggaran = %d
        AND active = 1
    LIMIT 1
", $id_skpd, $input['tahun_anggaran']));

$nama_bidang_urusan = '';
if (!empty($unit)) {
    $arr_bidur = array_filter(array($unit->bidur_1, $unit->bidur_2, $unit->bidur_3),
        function($v) {
            return $v !== null && $v !== ''; 
        }
    );  
    if (!empty($arr_bidur)) {
        $placeholders = implode(',', array_fill(0, count($arr_bidur), '%d'));
        $sql = $wpdb->prepare("
            SELECT nama_bidang_urusan 
            FROM data_prog_keg 
            WHERE id_bidang_urusan IN ($placeholders)
            AND tahun_anggaran = %d
            AND active = 1
            GROUP BY nama_bidang_urusan
        ", array_merge($arr_bidur, array($input['tahun_anggaran'])));

        $nama_bidur = $wpdb->get_col($sql);

        // hilangkan angka dan titik di awal, misal "1.01 " atau "2.19 "
        $nama_bersih = array();
        foreach ($nama_bidur as $n) {
            $nama_bersih[] = preg_replace('/^[0-9.]+\s*/', '', trim($n));
        }

        $nama_bidang_urusan = implode('<br>', $nama_bersih);
    }
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
if (!empty($cek_jadwal_renja)) {
    foreach ($cek_jadwal_renja as $jadwal_renja) {
        $id_jadwal = $jadwal_renja['relasi_perencanaan'];
    }
}

$nama_periode_dinilai = '-';

if ($id_jadwal != 0) {
    $get_nama_jadwal = $wpdb->get_row(
        $wpdb->prepare('
            SELECT nama
            FROM data_jadwal_lokal
            WHERE id_jadwal_lokal = %d
        ', $id_jadwal)
    );

    if (!empty($get_nama_jadwal)) {
        $nama_periode_dinilai = $get_nama_jadwal->nama;
    }
}
?>
<style type="text/css">
    .btn-action-group {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btn-action-group .btn {
        margin: 0 5px;
    }
</style>
<div class="container-md">
    <div class="cetak" style="padding: 5px; overflow: auto; height: 80vh;">
        <div style="padding: 10px;margin:0 0 3rem 0;">
            <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
            <h1 class="text-center table-title" style="padding-top: 80px">
                Manajemen Resiko Kecurangan MCP <br><?php echo $nama_skpd; ?><br>Tahun <?php echo $input['tahun_anggaran']; ?>
            </h1>
            <div class="text-center" style="text-align:center; margin: 20px;">
                <button type="button" class="btn btn-primary" onclick="tambah_data()" style=" align-items:center;">
                    <span class="dashicons dashicons-plus-alt2" style="position:relative; top:-2px; vertical-align:middle;"></span>
                    Tambah Data
                </button>
            </div>
            <table class="table table-bordered">
                <tr>
                    <th style="width: 20%;">Nama Pemda</th>
                    <td><strong><?php echo $nama_pemda; ?></strong></td>
                </tr>
                <tr>
                    <th>Nama OPD</th>
                    <td><strong><?php echo $nama_skpd; ?></strong></td>
                </tr>
                <tr>
                    <th>Tahun Penilaian</th>
                    <td><strong><?php echo $input['tahun_anggaran']; ?></strong></td>
                </tr>
                <tr>
                    <th>Periode yang Dinilai</th>
                    <td><strong><?php echo ($nama_periode_dinilai); ?></strong></td>
                </tr>
                <tr>
                    <th>Urusan Pemerintahan</th>
                    <td><strong><?php echo $nama_bidang_urusan; ?></strong></td>
                </tr>
            </table>
            <div class="wrap-table">
                <table id="cetak" title="Manajemen Resiko Kecurangan MCP SKPD" class="table_manrisk_kecurangan_mcp table-bordered" cellpadding="2" cellspacing="0" contenteditable="false">
                    <thead style="background: #ffc491; text-align:center;">
                        <tr>
                            <th rowspan="3">No</th>
                            <th colspan="8"></th>
                            <th colspan="3">Nilai Risiko</th>
                            <th rowspan="3">Rencana Tindak Pengendalian (Fraud Risk Response)</th>
                            <th rowspan="3">Target Waktu Pelaksanaan Pengendalian</th>    
                            <th rowspan="3">Pelaksanaan Pengendalian</th>    
                            <th rowspan="3">Bukti Pelaksanaan</th>    
                            <th rowspan="3">Kendala</th>    
                            <th rowspan="3">OPD Pemilik Risiko</th>    
                            <th rowspan="3">Keterangan Pengisian</th>   
                            <th rowspan="3">Aksi</th>
                        </tr>
                        <tr>
                            <th rowspan="2">Sasaran Area MCP</th>
                            <th rowspan="2">Tahapan Proses Bisnis</th>
                            <th>Deskripsi Risiko Kecurangan</th>
                            <th>Pihak Terkait</th>
                            <th rowspan="2">Jenis Risiko Kecurangan</th>
                            <th>Pemilik Risko</th>
                            <th rowspan="2">Penyebab</th>
                            <th rowspan="2">Dampak</th>
                            <th rowspan="2">Kemungkinan </th>
                            <th rowspan="2">Dampak </th>
                            <th rowspan="2">Status Risiko (Nilai) </th>
                        </tr>
                        <tr>
                        </tr>
                        <tr>
                            <th>(1)</th>
                            <th>(2)</th>
                            <th>(3)</th>
                            <th>(4)</th>
                            <th>(5)</th>
                            <th>(6)</th>
                            <th>(7)</th>
                            <th>(8)</th>
                            <th>(9)</th>
                            <th>(10)</th>
                            <th>(11)</th>
                            <th>(12)</th>
                            <th>(13)</th>
                            <th>(14)</th>
                            <th>(15)</th>
                            <th>(16)</th>
                            <th>(17)</th>
                            <th>(18)</th>
                            <th>(19)</th>
                            <th>(20)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal crud -->
<div class="modal fade" id="TambahResikoKecuranganModal" tabindex="-1" role="dialog" aria-labelledby="TambahResikoKecuranganModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 60%; margin-top: 50px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TambahResikoKecuranganModalLabel">Tambah Risiko Kecurangan MCP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">
                <form id="form_resiko_kecurangan">
                    <div class="form-group">
                        <label for="nama_sasaran_area">Nama Sasaran Area MCP</label>
                        <input type="text" class="form-control" id="nama_sasaran_area" name="nama_sasaran_area"  required>
                    </div>
                    <div class="form-group">
                        <label for="tahapan">Tahapan Proses Bisnis</label>
                        <input type="text" class="form-control" id="tahapan" name="tahapan" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi_resiko">Deskripsi Risiko Kecurangan</label>
                        <input type="text" class="form-control" id="deskripsi_resiko" name="deskripsi_resiko" required>
                    </div>
                    <div class="form-group">
                        <label for="pihak_terkait">Pihak Terkait</label>
                        <input type="text" class="form-control" id="pihak_terkait" name="pihak_terkait" required>
                    </div>
                    <div class="form-group">
                        <label for="jenis_resiko">Pilih Jenis Risiko Kecurangan</label>
                        <select id="jenis_resiko" class="form-control">
                            <option value="" selected>Pilih Jenis Risiko Kecurangan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pemilik_resiko">Pilih Pemilik Resiko</label>
                        <select id="pemilik_resiko" class="form-control">
                            <option value="" selected>Pilih Pemilik Resiko</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="penyebab">Penyebab</label>
                        <input type="text" class="form-control" id="penyebab" name="penyebab" required>
                    </div>
                    <div class="form-group">
                        <label for="dampak">Dampak</label>
                        <input type="text" class="form-control" id="dampak" name="dampak" required>
                    </div>
                    <div class="form-group">
                        <label for="skala_kemungkinan">Skala Kemungkinan</label>
                        <input type="number" class="form-control" id="skala_kemungkinan" name="skala_kemungkinan" required>
                    </div>
                    <div class="form-group">
                        <label for="skala_dampak">Skala Dampak</label>
                        <input type="number" class="form-control" id="skala_dampak" name="skala_dampak" required>
                    </div>
                    <div class="form-group">
                        <label for="tindak_pengendalian">Rencana Tindak Pengendalian (Fraud Risk Response)</label>
                        <input type="text" class="form-control" id="tindak_pengendalian" name="tindak_pengendalian" required>
                    </div>
                    <div class="form-group">
                        <label for="target_waktu">Target target_Waktu Pelaksanaan Pengendalian</label>
                        <input type="text" class="form-control" id="target_waktu" name="waktu" required>
                    </div>
                    <div class="form-group">
                        <label for="pelaksanaan_pengendalian">Pelaksanaan Pengendalian</label>
                        <input type="text" class="form-control" id="pelaksanaan_pengendalian" name="pelaksanaan_pengendalian" required>
                    </div>                        
                    <div class="form-group">
                        <label for="bukti_pelaksanaan">Bukti Pelaksanaan</label>
                        <input type="text" class="form-control" id="bukti_pelaksanaan" name="bukti_pelaksanaan" required>
                    </div>                      
                    <div class="form-group">
                        <label for="kendala">Kendala</label>
                        <input type="text" class="form-control" id="kendala" name="kendala" required>
                    </div>                   
                    <div class="form-group">
                        <label for="opd_pemilik_resiko">OPD Pemilik Risiko</label>
                        <input type="text" class="form-control" id="opd_pemilik_resiko" name="opd_pemilik_resiko" required>
                    </div>      
                    <div class="form-group">
                        <label for="keterangan_pengisian">Keterangan Pengisian</label>
                        <input type="text" class="form-control" id="keterangan_pengisian" name="keterangan_pengisian" required>
                    </div>            
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_tujuan_sasaran(); return false">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script>
    function tambah_data() {
    jQuery('#TambahResikoKecuranganModalLabel').text('Tambah Risiko Kecurangan MCP');
    jQuery('#TambahResikoKecuranganModal').modal('show');
    }
</script>