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

        // hilangkan angka dan titik di awal
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
                Manajemen Risiko Kecurangan <br><?php echo $nama_skpd; ?><br>Tahun <?php echo $input['tahun_anggaran']; ?>
            </h1>
            <div id='aksi-wpsipd' style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 10px; margin: 20px 0;">
                <button type="button" class="btn btn-primary" onclick="tambah_data()" style="text-align:center; margin: 20px;">
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
                <table id="cetak" title="Manajemen Risiko Kecurangan MCP SKPD" class="table_manrisk_kecurangan_mcp table-bordered" cellpadding="2" cellspacing="0" contenteditable="false">
                    <thead style="background: #ffc491; text-align:center;">
                        <tr>
                            <th rowspan="3">No</th>
                            <th colspan="7"></th>
                            <th colspan="3">Nilai Risiko</th>
                            <th rowspan="3">Rencana Tindak Pengendalian (Fraud Risk Response)</th>
                            <th rowspan="3">Target Waktu Pelaksanaan Pengendalian</th>    
                            <th rowspan="3">Pelaksanaan Pengendalian</th>    
                            <th rowspan="3" style="width:200px;">Bukti Pelaksanaan</th>    
                            <th rowspan="3">Kendala</th>    
                            <th rowspan="3">OPD Pemilik Risiko</th>    
                            <th rowspan="3">Keterangan Pengisian</th>   
                            <th rowspan="3">Aksi</th>
                        </tr>
                        <tr>
                            <th rowspan="2">Tahapan Proses Bisnis</th>
                            <th>Deskripsi Risiko Kecurangan</th>
                            <th>Pihak Terkait</th>
                            <th rowspan="2">Jenis Risiko Kecurangan</th>
                            <th>Pemilik Risko</th>
                            <th rowspan="2">Penyebab</th>
                            <th rowspan="2">Dampak</th>
                            <th rowspan="2">Kemungkinan </th>
                            <th rowspan="2">Dampak </th>
                            <th rowspan="2">Status Risiko (Nilai) (10x11) </th>
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
                <h5 class="modal-title" id="TambahResikoKecuranganModalLabel">Tambah Risiko Kecurangan </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <form id="form_resiko_kecurangan">
                    <input type="hidden" id="id_tahapan" name="id_tahapan">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="tahapan">Tahapan Proses Bisnis</label>
                        <select id="tahapan" class="form-control">
                            <option value="" selected>Pilih Tahapan Proses Bisnis</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi_resiko">Deskripsi Risiko Kecurangan</label>
                        <textarea type="text" class="form-control" id="deskripsi_resiko" name="deskripsi_resiko" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="pihak_terkait">Pihak Terkait</label>
                        <input type="text" class="form-control" id="pihak_terkait" name="pihak_terkait" required>
                    </div>
                    <div class="form-group">
                        <label for="jenis_resiko">Pilih Jenis Risiko Kecurangan</label>
                        <select id="jenis_resiko" class="form-control">
                            <option value="" selected>Pilih Jenis Risiko Kecurangan</option>
                            <option value="Konflik kepentingan">Konflik kepentingan</option>
                            <option value="Pemberian suap">Pemberian suap</option>
                            <option value="Penggelapan">Penggelapan</option>
                            <option value="Pemalsuan Data">Pemalsuan Data</option>
                            <option value="Pemerasan/ Pungutan Liar">Pemerasan/ Pungutan Liar</option>
                            <option value="Penyalahgunaan wewenang">Penyalahgunaan wewenang</option>
                            <option value="Fraud/ Korupsi">Fraud/ Korupsi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pemilik_resiko">Pilih Pemilik Risiko</label>
                        <select id="pemilik_resiko" class="form-control">
                            <option value="" selected>Pilih Pemilik Risiko</option>
                            <option value="Kepala Daerah">Kepala Daerah</option>
                            <option value="Kepala OPD">Kepala OPD</option>
                            <option value="Kepala Bidang">Kepala Bidang</option>
                            <option value="PA/PK">PA/PK</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="penyebab">Penyebab</label>
                        <textarea type="text" class="form-control" id="penyebab" name="penyebab" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="dampak">Dampak</label>
                        <textarea type="text" class="form-control" id="dampak" name="dampak" required></textarea>
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
                        <textarea type="text" class="form-control" id="tindak_pengendalian" name="tindak_pengendalian" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="target_waktu">Target Waktu Pelaksanaan Pengendalian</label>
                        <input type="text" class="form-control" id="target_waktu" name="waktu" required>
                    </div>
                    <div class="form-group">
                        <label for="pelaksanaan_pengendalian">Pelaksanaan Pengendalian</label>
                        <textarea type="text" class="form-control" id="pelaksanaan_pengendalian" name="pelaksanaan_pengendalian" required></textarea>
                    </div>                        
                    <div class="form-group">
                        <label for="bukti_pelaksanaan">Bukti Pelaksanaan</label>
                        <?php
                        $content = '';
                        $editor_id = 'bukti_pelaksanaan';
                        $settings = array(
                            'textarea_name' => 'bukti_pelaksanaan',
                            'media_buttons' => true,
                            'teeny' => false,
                            'quicktags' => true
                        );
                        wp_editor($content, $editor_id, $settings);
                        ?>
                    </div>                      
                    <div class="form-group">
                        <label for="kendala">Kendala</label>
                        <input type="text" class="form-control" id="kendala" name="kendala" required>
                    </div>                   
                    <div class="form-group">
                        <label for="opd_pemilik_resiko">OPD Pemilik Risiko</label>
                        <textarea type="text" class="form-control" id="opd_pemilik_resiko" name="opd_pemilik_resiko" required></textarea>
                    </div>      
                    <div class="form-group">
                        <label for="keterangan_pengisian">Keterangan Pengisian</label>
                        <input type="text" class="form-control" id="keterangan_pengisian" name="keterangan_pengisian" required>
                    </div>            
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpan_resiko(); return false">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        get_table_resiko_kecurangan_manrisk();
        run_download_excel('', '#aksi-wpsipd');

        jQuery(document).on('change', '#tahapan', function() {
            let selected = jQuery(this).find(':selected');
            let id_tahapan = selected.val();
            let sasaran = selected.data('sasaran') || '';

            jQuery('#id_tahapan').val(id_tahapan);
            jQuery('#nama_sasaran_area').val(sasaran);
        });
    });

    function get_table_resiko_kecurangan_manrisk() {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_table_resiko_kecurangan_manrisk',
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
                'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                'id_skpd': <?php echo $id_skpd; ?>
            },
            success: function(response) {
                jQuery('#wrap-loading').hide();
                console.log(response);
                if (response.status == 'success') {
                    jQuery('.table_manrisk_kecurangan_mcp tbody').html(response.data);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat memuat tabel!');
            }
        });
    }

    function tambah_data() {
        jQuery('#TambahResikoKecuranganModalLabel').text('Tambah Risiko Kecurangan');
        jQuery('#form_resiko_kecurangan').trigger('reset');
        jQuery('#id').val('');
        jQuery('#id_tahapan').val();
        options_tahapan()
        jQuery('#TambahResikoKecuranganModal').modal('show');
    }

    function simpan_resiko() {
        let id  = jQuery('#id').val();
        let id_tahapan  = jQuery('#id_tahapan').val();
        let deskripsi_resiko  = jQuery('#deskripsi_resiko').val();
        let pihak_terkait  = jQuery('#pihak_terkait').val();
        let jenis_resiko  = jQuery('#jenis_resiko').val();
        let pemilik_resiko  = jQuery('#pemilik_resiko').val();
        let penyebab  = jQuery('#penyebab').val();
        let dampak  = jQuery('#dampak').val();
        let skala_kemungkinan  = jQuery('#skala_kemungkinan').val();
        let skala_dampak  = jQuery('#skala_dampak').val();
        let tindak_pengendalian  = jQuery('#tindak_pengendalian').val();
        let target_waktu  = jQuery('#target_waktu').val();
        let pelaksanaan_pengendalian  = jQuery('#pelaksanaan_pengendalian').val();
        let bukti_pelaksanaan = tinymce.get('bukti_pelaksanaan') ? tinymce.get('bukti_pelaksanaan').getContent() : jQuery('#bukti_pelaksanaan').val();
        let kendala  = jQuery('#kendala').val();
        let opd_pemilik_resiko  = jQuery('#opd_pemilik_resiko').val();
        let keterangan_pengisian  = jQuery('#keterangan_pengisian').val();

        jQuery('#wrap-loading').show();

        jQuery.ajax({
            method: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'simpan_resiko_kecurangan',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
                id: id,
                id_tahapan: id_tahapan,
                deskripsi_resiko: deskripsi_resiko,
                pihak_terkait: pihak_terkait,
                jenis_resiko: jenis_resiko,
                pemilik_resiko: pemilik_resiko,
                penyebab: penyebab,
                dampak: dampak,
                skala_kemungkinan: skala_kemungkinan,
                skala_dampak: skala_dampak,
                tindak_pengendalian: tindak_pengendalian,
                target_waktu: target_waktu,
                pelaksanaan_pengendalian: pelaksanaan_pengendalian,
                bukti_pelaksanaan: bukti_pelaksanaan,
                kendala: kendala,
                opd_pemilik_resiko: opd_pemilik_resiko,
                keterangan_pengisian: keterangan_pengisian
            },
            success: function(response) {
                jQuery('#wrap-loading').hide();
                alert(response.message);
                if (response.status == 'success') {
                    jQuery('#TambahResikoKecuranganModal').modal('hide');
                    get_table_resiko_kecurangan_manrisk();
                }
            },
            error: function(xhr) {
                jQuery('#wrap-loading').hide();
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat menggirim data!');
            }
        });
    }

    function options_tahapan() {
        jQuery.ajax({
            method: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'options_tahapan',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id_skpd: <?php echo $id_skpd; ?>,
            },
            success: function(response) {
                if(response.status == 'success') {
                    let tahapan = '<option value= "">Pilih Tahapan Proses Bisnis</option>';
                    response.data.forEach(function(item) {
                        tahapan += `<option value="${item.id_tahapan}" data-sasaran="${item.nama_sasaran_area}">
                            ${item.nama_tahapan}
                        </option>`;
                    });
                    jQuery('#tahapan').html(tahapan);
                    jQuery('#tahapan').val();
                } 
            }, 
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Terjadi kesalahan!'); 
            }
        });
    }

    function hapus_resiko(id) {
        if(confirm('Apakah anda yakin untuk menghapus data ini?')) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'hapus_resiko',
                    api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                    id: id
                },
                success: function(response) {
                    console.log(response);
                    jQuery('#wrap-loading').hide();
                    if (response.status === 'success') {
                        alert(response.message);
                        get_table_resiko_kecurangan_manrisk();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    jQuery('#wrap-loading').hide();
                    alert('Terjadi kesalahan saat mengirim data!');
                }
            });
        }
    }

    function edit_resiko(id) {
        jQuery('#wrap-loading').show();
        jQuery('#TambahResikoKecuranganModalLabel').text('Edit Risiko Kecurangan MCP');
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'edit_resiko',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                id: id
            },
            success: function(response) {
                console.log(response);
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    console.log(response.message);
                    jQuery('#id').val(response.data.id);
                    jQuery('#id_tahapan').val(response.data.id_tahapan);
                     // isi nama sasaran dan nama tahapan
                    jQuery('#nama_sasaran_area').val(response.data.sasaran || '');
                    
                    // untuk select tahapan, buat opsi dan pilih sesuai data
                    let tahapanSelect = jQuery('#tahapan');
                    tahapanSelect.empty();
                    if(response.data.tahapan) {
                        tahapanSelect.append('<option value="' + response.data.id_tahapan + '" selected>' + response.data.tahapan + '</option>');
                    }
                    tahapanSelect.append('<option value="">Pilih Tahapan Proses Bisnis</option>'); // default
                    // jika mau, bisa append opsi lain dari server untuk semua tahapan

                    jQuery('#deskripsi_resiko').val(response.data.deskripsi_resiko);
                    jQuery('#pihak_terkait').val(response.data.pihak_terkait);
                    jQuery('#jenis_resiko').val(response.data.jenis_resiko);
                    jQuery('#pemilik_resiko').val(response.data.pemilik_resiko);
                    jQuery('#penyebab').val(response.data.penyebab);
                    jQuery('#dampak').val(response.data.dampak);
                    jQuery('#skala_kemungkinan').val(response.data.skala_kemungkinan);
                    jQuery('#skala_dampak').val(response.data.skala_dampak);
                    jQuery('#tindak_pengendalian').val(response.data.tindak_pengendalian);
                    jQuery('#target_waktu').val(response.data.target_waktu);
                    jQuery('#pelaksanaan_pengendalian').val(response.data.pelaksanaan_pengendalian);
                    tinymce.get('bukti_pelaksanaan').setContent(response.data.bukti_pelaksanaan);
                    jQuery('#kendala').val(response.data.kendala);
                    jQuery('#opd_pemilik_resiko').val(response.data.opd_pemilik_resiko);
                    jQuery('#keterangan_pengisian').val(response.data.keterangan_pengisian);

                    // buka modal form edit
                    jQuery('#TambahResikoKecuranganModal').modal('show');

                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                jQuery('#wrap-loading').hide();
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    }
</script>