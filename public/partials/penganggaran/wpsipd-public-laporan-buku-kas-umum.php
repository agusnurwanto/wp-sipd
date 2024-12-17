<?php
// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}
global $wpdb;

$input = shortcode_atts(array(
    'kode_sbl' => 'undefined',
    'tahun_anggaran' => '2022'
), $atts);

$kode_npd = (!empty($_GET['kodenpd'])) ? $_GET['kodenpd'] : 0;
if (!empty($_GET['kode_sbl'])) {
    $input['kode_sbl'] = $_GET['kode_sbl'];
}
$data_sbl = explode(".", $input['kode_sbl']);

$unit = array();
$nama_skpd = "";
if (count($data_sbl) > 1) {
    $sql = $wpdb->prepare("
        select 
            * 
        from data_unit 
        where tahun_anggaran=%d
            and id_skpd=%d 
            and active=1
        order by id_skpd ASC
    ", $input['tahun_anggaran'], $data_sbl[1]);
    $unit = $wpdb->get_row($sql, ARRAY_A);
    $nama_skpd = $unit['nama_skpd'];
}

$api_key = get_option('_crb_api_key_extension');

$data_rfk = $wpdb->get_row($wpdb->prepare('
	SELECT 
		s.*,
		u.nama_skpd as nama_sub_skpd_asli,
		u.kode_skpd as kode_sub_skpd_asli,
		uu.nama_skpd as nama_skpd_asli,
		uu.kode_skpd as kode_skpd_asli
	FROM data_sub_keg_bl s
	INNER JOIN data_unit u on s.id_sub_skpd=u.id_skpd
		AND u.active=s.active
		AND u.tahun_anggaran=s.tahun_anggaran
	INNER JOIN data_unit uu on uu.id_skpd=u.id_unit
		AND uu.active=s.active
		AND uu.tahun_anggaran=s.tahun_anggaran
	WHERE s.kode_sbl = %s 
		AND s.active = 1
		AND s.tahun_anggaran = %d', $input['kode_sbl'], $input['tahun_anggaran']), ARRAY_A);

if ($data_rfk) {
    $kode_sub_skpd = $data_rfk['kode_sub_skpd_asli'];
    $nama_sub_skpd = $data_rfk['nama_sub_skpd_asli'];
    $kode_skpd = $data_rfk['kode_skpd_asli'];
    $nama_skpd = $data_rfk['nama_skpd_asli'];
    $kode_urusan = $data_rfk['kode_urusan'];
    $nama_urusan = $data_rfk['nama_urusan'];
    $kode_program = $data_rfk['kode_program'];
    $nama_program = $data_rfk['nama_program'];
    $kode_kegiatan = $data_rfk['kode_giat'];
    $nama_kegiatan = $data_rfk['nama_giat'];
    $kode_bidang_urusan = $data_rfk['kode_bidang_urusan'];
    $nama_bidang_urusan = $data_rfk['nama_bidang_urusan'];
    $nama_sub_kegiatan = $data_rfk['nama_sub_giat'];
    $kode_sub_kegiatan = $data_rfk['kode_sub_giat'];
    $nama_sub_kegiatan = str_replace('X.XX', $kode_bidang_urusan, $nama_sub_kegiatan);
    $pagu_sub_kegiatan = number_format($data_rfk['pagu'], 0, ",", ".");
    $id_sub_skpd = $data_rfk['id_sub_skpd'];
} else {
    die('<h1 class="text-center">Sub Kegiatan tidak ditemukan!</h1>');
}

$data_npd = $wpdb->get_row($wpdb->prepare("
    SELECT 
        *
    FROM data_nota_pencairan_dana
    WHERE id=%d
        AND kode_sbl = %s
        AND tahun_anggaran = %d
        AND active = 1", $kode_npd, $input['kode_sbl'], $input['tahun_anggaran']), ARRAY_A);

$nama_pemda = get_option('_crb_daerah');

$rka = $wpdb->get_results($wpdb->prepare("
    SELECT
        *
    FROM data_rka
    WHERE active=1
        AND tahun_anggaran=%d
        AND kode_sbl=%s
", $input['tahun_anggaran'], $input['kode_sbl']), ARRAY_A);
?>
<style>
    #tabel_detail_nota,
    #tabel_detail_nota td,
    #tabel_detail_nota th {
        border: 0;
        padding: 0px;
    }

    #tabel_detail_nota td:first-of-type {
        width: 10em;
    }

    #tabel_detail_nota td:nth-child(2) {
        width: 1em;
    }
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div style="padding: 15px;">
    <h1 class="text-center" style="margin-top: 50px;">BUKU KAS UMUM PEMBANTU</br><?php echo $nama_skpd; ?></br>TAHUN ANGGARAN <?php echo $input['tahun_anggaran']; ?></h1>
    <table id="tabel_detail_nota" style="margin-top: 30px;">
        <tbody>
            <tr>
                <td>Urusan</td>
                <td>:</td>
                <td><?php echo $kode_urusan . '  ' . $nama_urusan ?></td>
            </tr>
            <tr>
                <td>Bidang Urusan</td>
                <td>:</td>
                <td><?php echo $kode_bidang_urusan . '  ' . $nama_bidang_urusan ?></td>
            </tr>
            <tr>
                <td>Program</td>
                <td>:</td>
                <td><?php echo $kode_program . '  ' . $nama_program ?></td>
            </tr>
            <tr>
                <td>Kegiatan</td>
                <td>:</td>
                <td><?php echo $kode_kegiatan . '  ' . $nama_kegiatan ?></td>
            </tr>
            <tr>
                <td>Sub Kegiatan</td>
                <td>:</td>
                <td><?php echo $kode_sub_kegiatan . '  ' . str_replace($kode_sub_kegiatan, '', $nama_sub_kegiatan); ?></td>
            </tr>
            <tr>
                <td>Pagu Belanja</td>
                <td>:</td>
                <td>Rp <?php echo $pagu_sub_kegiatan; ?></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- table -->
<div style="padding: 15px;margin:0 0 3rem 0;">
    <div class="text-center" style="margin-bottom: 10px;">
        <button class="btn btn-primary" onclick="tambah_data_bku();"><i class="dashicons dashicons-plus-alt"></i> Tambah Kas Umum Pembantu</button>
    </div>

    <table id="table_daftar_bku">
        <thead>
            <tr>
                <th class="atas kanan bawah kiri text-center" width="130px">Tanggal</th>
                <th class="atas kanan bawah text-center" width="250px">Nomor Bukti</th>
                <th class="atas kanan bawah text-center">Uraian</th>
                <th class="atas kanan bawah text-center" style="width: 150px;">Penerimaan</th>
                <th class="atas kanan bawah text-center" style="width: 150px;">Pengeluaran</th>
                <th class="atas kanan bawah text-center" style="width: 150px;">Saldo</th>
                <th class="atas kanan bawah text-center" style="width: 100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="7" class="kiri bawah kanan text-center">Data kosong</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_tambah_data" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Tambah Data Buku Kas Umum Pembantu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="id_data" value="">
                    <!-- Card 1: Informasi Transaksi -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>
                                Informasi Transaksi
                            </strong>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nomor NPD</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nomor_npd_bku" name="nomor_npd_bku" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for='set_tanggal'>Pilih Tanggal</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id='set_tanggal' name="set_tanggal" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Informasi Pengeluaran -->
                    <div class="card mb-3" id="pengeluaran_card">
                        <div class="card-header">
                            <strong>
                                Informasi Pengeluaran
                            </strong>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nomor Bukti</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nomor_bukti_bku" name="nomor_bukti_bku" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Rekening Belanja</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="rekening_akun" name="rekening_akun" onchange="get_data_sisa_pagu_per_akun_npd(this.value, false);">
                                        <option value="">Pilih Rekening</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Rincian Belanja RKA</label>
                                <div class="col-sm-9">
                                    <select id="rincian_rka" name="rincian_rka" class="form-control" onchange="set_sisa_pagu_rinci();">
                                        <option value="">Pilih Rincian Belanja</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Sisa Pagu Rincian Belanja</label>
                                <div class="col-sm-9">
                                    <span id="sisa_pagu_rincian_belanja" style="font-weight: 600; font-size: 1.3em;">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Uraian BKU -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>
                                Nilai dan Uraian BKU
                            </strong>
                        </div>
                        <div class="card-body">
                            <!-- Toggle untuk memilih Dengan Rumus atau Tidak Dengan Rumus -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Metode Pengisian</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" name="metode_bku" id="tanpa_rumus" value="tanpa_rumus" checked>
                                        <label class="form-check-label" for="tanpa_rumus">Tidak Dengan Rumus</label>
                                    </div>
                                    <div class="form-check form-check-inline" style="margin-left: 30px;">
                                        <input type="radio" class="form-check-input" name="metode_bku" id="dengan_rumus" value="dengan_rumus">
                                        <label class="form-check-label" for="dengan_rumus">Dengan Rumus</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Form untuk Tidak Dengan Rumus -->
                            <div id="form_tanpa_rumus">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Nilai</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control paguRek" id="pagu_bku" name="pagu_bku">
                                    </div>
                                </div>
                            </div>

                            <!-- Form untuk Dengan Rumus -->
                            <div id="form_dengan_rumus" style="display: none;">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Masukkan Rumus</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="rumus_bku" name="rumus_bku" placeholder="Contoh: 10000 + 5000 - 2000"></textarea>
                                        <span class="badge badge-info mt-2">Gunakan <strong>*</strong> untuk kali, <strong>/</strong> untuk bagi, <strong>+</strong> untuk tambah, <strong>-</strong> untuk kurang</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Nilai BKU</label>
                                    <div class="col-sm-9">
                                        <span id="hasil_bku" style="font-weight: 600; font-size: 1.3em;">Rp 0</span>
                                        <input id="hasil_bku_asli" class="hasil_bku_asli" name="hasil_bku_asli" type="hidden" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Uraian BKU</label>
                                <div class="col-sm-9">
                                    <textarea id="uraian_bku" name="uraian_bku" rows="4" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5: Informasi Bank -->
                    <div class="card mb-3" id="bank_card">
                        <div class="card-header">
                            <strong>
                                Informasi Bank
                            </strong>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Jenis Transaksi</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" name="jenis_transkasi" id="tunai" value="1">
                                        <label class="form-check-label" for="tunai">Tunai</label>
                                    </div>
                                    <div class="form-check form-check-inline" style="margin-left: 30px;">
                                        <input type="radio" class="form-check-input" name="jenis_transkasi" id="non_tunai" value="2" checked>
                                        <label class="form-check-label" for="non_tunai">Non Tunai</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nama Pemegang Rekening Bank</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nama_pemilik_rekening_bank_bku" name="nama_pemilik_rekening_bank_bku" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nama Rekening Bank</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nama_rekening_bank_bku" name="nama_rekening_bank_bku" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">No Rekening Bank</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="no_rekening_bank_bku" name="no_rekening_bank_bku" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_data()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    jQuery(document).ready(function() {
        window.id_skpd = '<?php echo $data_sbl[1]; ?>'
        window.pagu_transaksi = 0;
        window.id_rinci_sub_bl_global = false;
        window.this_pagu_bku_global = false;
        load_data();

        jQuery(document).on('input', '.paguRek', function() {
            formatInput(this);
        });

        window.rka_all = <?php echo json_encode($rka); ?>;

        jQuery('#set_tanggal').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2000,
            maxYear: parseInt(moment().format('YYYY'), 10),
            locale: {
                format: 'DD-MM-YYYY'
            }
        });

        // Manipulasi berdasarkan jenis transaksi (tunai/non-tunai)
        jQuery('input[type="radio"][name="jenis_transkasi"]').on('click', function() {
            var jenis_transaksi = jQuery('input[type="radio"][name="jenis_transkasi"]:checked').val();
            if (jenis_transaksi == 1) { // Tunai
                toggleFormTunai(true)
            } else { // Non-Tunai
                toggleFormTunai(false)
            }
        });

        // Event listener untuk perubahan metode pengisian
        jQuery('input[name="metode_bku"]').on('change', function() {
            toggleFormBku(jQuery(this).val());
        });

        // Kalkulator sederhana untuk menghitung nilai dari rumus
        jQuery('#rumus_bku').on('input', function() {
            const rumus = jQuery(this).val();
            const hasilBkuSpan = jQuery('#hasil_bku');
            const hasilBkuAsli = jQuery('#hasil_bku_asli');

            try {
                // Evaluasi rumus dengan fungsi eval
                const hasil = eval(rumus);

                if (!isNaN(hasil)) {
                    hasilBkuSpan.text(`Rp ${hasil.toLocaleString('id-ID')}`);
                    hasilBkuAsli.val(hasil);
                } else {
                    hasilBkuSpan.text('Rp 0');
                    hasilBkuAsli.val('');
                }
            } catch (e) {
                hasilBkuSpan.text('Rp 0');
                hasilBkuAsli.val('');
            }
        });

        jQuery('#rekening_akun').select2({
            width: '100%',
            dropdownParent: jQuery('#modal_tambah_data .modal-body') // Tentukan modal sebagai parent dropdown agar select2 search tidak error
        });
        jQuery('#rincian_rka').select2({
            width: '100%',
            dropdownParent: jQuery('#modal_tambah_data .modal-body') // Tentukan modal sebagai parent dropdown agar select2 search tidak error
        });

    });

    function toggleFormTunai(isTunai) {
        if (isTunai) {
            jQuery('#nama_pemilik_rekening_bank_bku').closest('.form-group').hide();
            jQuery('#nama_rekening_bank_bku').closest('.form-group').hide();
            jQuery('#no_rekening_bank_bku').closest('.form-group').hide();
            jQuery("input[name=jenis_transkasi][value='1']").prop('checked', true).trigger("change");
        } else {
            jQuery('#nama_pemilik_rekening_bank_bku').closest('.form-group').show();
            jQuery('#nama_rekening_bank_bku').closest('.form-group').show();
            jQuery('#no_rekening_bank_bku').closest('.form-group').show();
            jQuery("input[name=jenis_transkasi][value='2']").prop('checked', true).trigger("change");
        }
    }


    // Fungsi untuk toggle form dengan rumus atau tanpa rumus
    function toggleFormBku(method) {
        const formTanpaRumus = jQuery('#form_tanpa_rumus');
        const formDenganRumus = jQuery('#form_dengan_rumus');

        if (method === 'tanpa_rumus') {
            jQuery("input[name=metode_bku][value='tanpa_rumus']").prop('checked', true);
            formTanpaRumus.show();
            formDenganRumus.hide();
        } else {
            jQuery("input[name=metode_bku][value='dengan_rumus']").prop('checked', true);
            formTanpaRumus.hide();
            formDenganRumus.show();
        }
    }

    function formatInput(element) {
        var sanitized = jQuery(element).val().replace(/[^0-9]/g, '');
        var formatted = formatRupiah(sanitized);
        jQuery(element).val(formatted);
    }

    function load_data() {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                api_key: '<?php echo $api_key; ?>',
                action: 'get_daftar_bku',
                kode_sbl: '<?php echo $input['kode_sbl']; ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                kode_npd: "<?php echo $kode_npd; ?>",
            },
            success: function(data) {
                jQuery('#wrap-loading').hide();
                const response = JSON.parse(data);
                if (response.status === 'success') {
                    jQuery('#table_daftar_bku > tbody').html(response.html);
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }

    function tambah_data_bku() {
        get_data_akun_rka_per_npd()
            .then(function() {
                jQuery('#wrap-loading').show();
                clearAllFields();
                toggleFormTunai(true);
                jQuery('#hasil_bku').text('')
                jQuery("#modal_tambah_data .modal-title").html("Tambah Pengeluaran Buku Kas Umum Pembantu");
                jQuery("#nomor_npd_bku").val("<?php echo $data_npd['nomor_npd']; ?>");
                jQuery('#modal_tambah_data').modal('show');
                jQuery('#wrap-loading').hide();
            });
    }

    function get_data_akun_rka_per_npd() {
        return new Promise(function(resolve, reject) {
            if (typeof dataAkun == 'undefined') {
                jQuery('#wrap-loading').show();
                jQuery.ajax({
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    type: "post",
                    data: {
                        "action": "get_rka_sub_keg_akun_npd",
                        "api_key": '<?php echo $api_key; ?>',
                        "tahun_anggaran": <?php echo $input['tahun_anggaran']; ?>,
                        "kode_sbl": "<?php echo $input['kode_sbl']; ?>",
                        "kode_npd": "<?php echo $kode_npd; ?>",
                    },
                    dataType: "json",
                    success: function(data) {
                        jQuery('#wrap-loading').hide();
                        window.dataAkun = data;
                        if (data.status == 'success') {
                            jQuery('#rekening_akun').html(data.data_akun_html);
                            resolve()
                        }
                    }
                });
            } else {
                jQuery('#rekening_akun').html(dataAkun.data_akun_html);
                resolve()
            }
        });
    }

    function get_data_sisa_pagu_per_akun_npd(kode_rekening) {
        return new Promise(function(resolve, reject) {
            var opsi_rincian = '<option value="">Pilih Rincian Belanja</option>';
            if (kode_rekening == '') {
                jQuery("#sisa_pagu_rekening_bku").html(formatRupiah(0, true));
                jQuery('#rincian_rka').html(opsi_rincian).trigger('change');
                return resolve()
            }
            rka_all.map(function(b, i) {
                if (b.kode_akun == kode_rekening) {
                    var selected = '';
                    if (b.id_rinci_sub_bl == id_rinci_sub_bl_global) {
                        selected = 'selected';
                    }
                    opsi_rincian +=
                        '<option value="' + b.id_rinci_sub_bl + '" ' + selected + ' nilai="' + b.rincian + '" koefisien="' + b.koefisien + '">' + b.nama_komponen + ' ' + b.spek_komponen + '</option>';
                }
            });
            window.id_rinci_sub_bl_global = false;
            jQuery('#rincian_rka').html(opsi_rincian).trigger('change');
            resolve()
        });
    }

    function submit_data() {
        if (confirm('Apakah anda yakin untuk menyimpan data ini?')) {
            let confirmUpdateRealisasi = confirm('Apakah anda ingin sekaligus mengupdate data realisasi rincian?')
            const validationRules = {
                'set_tanggal': 'Tanggal harus diisi!',
                'uraian_bku': 'Uraian BKU tidak boleh kosong!',
                'nomor_bukti_bku': 'Nomor BKU harus diisi!',
                'rekening_akun': 'Rekening harus diisi!',
                'rincian_rka': 'Rincian RKA harus diisi!',
                'jenis_transkasi': 'Harap Pilih dahulu jenis transaksi!',
                'metode_bku': 'Harap Pilih dahulu Metode pagu BKU!'
            };

            const jenis_transaksi = jQuery('input[name=jenis_transkasi]:checked').val();
            const metode_bku = jQuery('input[name=metode_bku]:checked').val();

            if (jenis_transaksi == 2) {
                validationRules['nama_pemilik_rekening_bank_bku'] = 'Nama pemilik rekening harus diisi!';
                validationRules['nama_rekening_bank_bku'] = 'Nama rekening bank harus diisi!';
                validationRules['no_rekening_bank_bku'] = 'Nomor rekening bank harus diisi!';
            }

            if (metode_bku == 'dengan_rumus') {
                validationRules['rumus_bku'] = 'Rumus Pagu harus diisi!';
                validationRules['hasil_bku_asli'] = 'Hasil rumus tidak diketahui!';
            } else {
                validationRules['pagu_bku'] = 'Pagu BKU harus diisi!';
            }

            const {
                error,
                data
            } = validateForm(validationRules);
            if (error) {
                return alert(error);
            }

            const id_data = jQuery('#id_data').val();

            const tempData = new FormData();
            tempData.append('action', 'tambah_data_bku');
            tempData.append('api_key', '<?php echo $api_key; ?>');

            tempData.append('kode_sbl', '<?php echo $input['kode_sbl']; ?>');
            tempData.append('tahun_anggaran', '<?php echo $input['tahun_anggaran']; ?>');
            tempData.append('kode_npd', '<?php echo $kode_npd; ?>');
            tempData.append('id_data', id_data);
            tempData.append('confirm_realisasi', confirmUpdateRealisasi);

            for (const [key, value] of Object.entries(data)) {
                tempData.append(key, value);
            }
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                method: 'post',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                dataType: 'json',
                data: tempData,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    console.log(response);
                    if (response.status == 'success') {
                        alert(response.message);
                        jQuery('#modal_tambah_data').modal('hide');
                        load_data();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    jQuery('#wrap-loading').hide();
                    console.error('AJAX Error:', status, error);
                }
            });
        }
    }

    function edit_data(id) {
        get_data_akun_rka_per_npd()
            .then(function() {
                jQuery('#wrap-loading').show();
                clearAllFields();
                jQuery.ajax({
                    method: 'post',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    dataType: 'json',
                    data: {
                        'action': 'get_data_buku_kas_umum_by_id',
                        'api_key': '<?php echo $api_key; ?>',
                        'id': id,
                        'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            jQuery('#id_data').val(response.data.id);
                            window.this_pagu_bku_global = response.data.pagu;
                            window.id_rinci_sub_bl_global = response.data.id_rinci_sub_bl;
                            jQuery("#rekening_akun").val(response.data.kode_rekening).trigger('change');
                            jQuery('#nomor_bukti_bku').val(response.data.nomor_bukti);
                            jQuery('#uraian_bku').val(response.data.uraian);
                            jQuery("#set_tanggal").val(response.data.tanggal_bkup);

                            jQuery("#modal_tambah_data .modal-title").html("Edit Pengeluaran Buku Kas Umum Pembantu");
                            jQuery("#nomor_npd_bku").val("<?php echo $data_npd['nomor_npd']; ?>");
                            if (response.data.jenis_cash == 1) {
                                //tunai
                                toggleFormTunai(true)
                                jQuery("#nama_pemilik_rekening_bank_bku").val('');
                                jQuery("#nama_rekening_bank_bku").val('');
                                jQuery("#no_rekening_bank_bku").val('');
                            } else if (response.data.jenis_cash == 2) {
                                //nontunai
                                toggleFormTunai(false)
                                jQuery("#nama_pemilik_rekening_bank_bku").val(response.data.nama_pemilik_rekening_bank);
                                jQuery("#nama_rekening_bank_bku").val(response.data.nama_rekening_bank);
                                jQuery("#no_rekening_bank_bku").val(response.data.no_rekening_bank);
                            } else {
                                jQuery("input[name=jenis_transkasi]").prop('checked', false).trigger("change");
                                jQuery("#nama_pemilik_rekening_bank_bku").val('');
                                jQuery("#nama_rekening_bank_bku").val('');
                                jQuery("#no_rekening_bank_bku").val('');
                            }
                            if (response.data.metode_bku == 'dengan_rumus') {
                                toggleFormBku(response.data.metode_bku)
                                jQuery('#pagu_bku').val('').trigger('input');
                                jQuery('#hasil_bku_asli').val(parseInt(response.data.pagu))
                                jQuery('#rumus_bku').val(response.data.rumus_pagu).trigger('input')
                            } else if (response.data.metode_bku == 'tanpa_rumus') {
                                toggleFormBku(response.data.metode_bku)
                                jQuery('#pagu_bku').val(parseInt(response.data.pagu)).trigger('input');
                                jQuery('#hasil_bku_asli').val('')
                                jQuery('#rumus_bku').val('').trigger('input')
                            } else {
                                jQuery("input[name=metode_bku]").prop('checked', false).trigger("change");
                                jQuery('#pagu_bku').val('').trigger('input');
                                jQuery('#hasil_bku_asli').val('')
                                jQuery('#rumus_bku').val('').trigger('input')
                            }
                            jQuery('#modal_tambah_data').modal('show');
                        } else {
                            alert(response.message);
                            jQuery('#wrap-loading').hide();
                        }
                    }
                });
            })
    }

    function delete_data(id) {
        let confirmDelete = confirm("Apakah anda yakin akan menghapus data ini?");
        if (confirmDelete) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                data: {
                    'action': 'delete_data_buku_kas_umum_pembantu',
                    'api_key': '<?php echo $api_key; ?>',
                    'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                    'id': id
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        load_data();
                        alert(`Data berhasil dihapus!`);
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    function set_sisa_pagu_rinci() {
        var id_rinci_sub_bl = jQuery('#rincian_rka').val();
        if (id_rinci_sub_bl == '') {
            jQuery('#sisa_pagu_rincian_belanja').html(formatRupiah(0, true));
            return;
        }
        var koefisien = jQuery('#rincian_rka option:checked').attr('koefisien');
        var nilai = +jQuery('#rincian_rka option:checked').attr('nilai');
        var komponen = jQuery('#rincian_rka option:checked').text();
        jQuery('#uraian_bku').val(komponen + ' ' + koefisien);
        jQuery('#wrap-loading').show();
        var id_unik = 'kode_sbl-rekening_belanja-kelompok-keterangan-' + id_rinci_sub_bl;
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'post',
            data: {
                'action': 'get_mapping',
                'api_key': '<?php echo $api_key; ?>',
                'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                'id_mapping': [id_unik],
                'realisasi_bku': true
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();
                var sisa_rincian_asli = 0;

                if (response.status == 'success') {
                    var realisasi_bku = parseFloat(response.data[0]?.realisasi_bku) || 0;
                    var pagu_bku_global = parseFloat(window.this_pagu_bku_global) || 0;
                    var nilai_fix = parseFloat(nilai) || 0;

                    // Untuk kondisi "edit" ditambahkan this_pagu_bku agar sesuai pagunya
                    if (response.data[0] && window.this_pagu_bku_global !== false) {
                        sisa_rincian_asli = nilai_fix - realisasi_bku + pagu_bku_global;
                    } else {
                        sisa_rincian_asli = nilai_fix - realisasi_bku;
                    }

                    jQuery('#sisa_pagu_rincian_belanja').html(formatRupiah(sisa_rincian_asli, true));
                } else {
                    alert(`GAGAL! \n${response.message}`);
                }
                window.this_pagu_bku_global = false;
            }

        });
    }

    function print_kwitansi(url) {
        if (typeof id_skpd === 'undefined' || !id_skpd) {
            console.error("id_skpd is not defined");
            return;
        }
        let newUrl = url + '&id_skpd=' + id_skpd;
        window.open(newUrl, '_blank');
    }
</script>