<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$input = shortcode_atts( array(
    'kode_sbl' => 'undefined',
	'tahun_anggaran' => '2022'
), $atts );

$kode_npd = (!empty($_GET['kodenpd'])) ? $_GET['kodenpd'] : 0;
if(!empty($_GET['kode_sbl'])){
    $input['kode_sbl'] = $_GET['kode_sbl'];
}
$data_sbl = explode(".", $input['kode_sbl']);

$unit = array();
$nama_skpd = "";
if(count($data_sbl) > 1){
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
                <form id="tambah-bku">
                    <div class="form-group row">
                        <label class="col-sm-3">Nomor NPD</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nomor_npd_bku" name="nomor_npd_bku" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="d-block col-sm-3">Jenis Transaksi</label>
                        <div class="col-sm-9">
                            <label for="penerimaan_bku"><input type="radio" class="ml-2 jenis_bku" id="penerimaan_bku" name="set_bku" value="terima" > Penerimaan</label>
                            <label style="margin-left: 30px;" for="pengeluaran_bku"><input type="radio" class="jenis_bku" id="pengeluaran_bku" name="set_bku" value="keluar" checked> Pengeluaran</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3" for='set_tanggal' style='display:inline-block'>Pilih Tanggal</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id='set_tanggal' name="set_tanggal" style='display:block;width:100%;' />
                        </div>
                    </div>
                    <div class="form-group set_keluar row">
                        <label class="col-sm-3">Nomor Bukti</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nomor_bukti_bku" name="nomor_bukti_bku" required>
                        </div>
                    </div>
                    <div class="form-group set_keluar row">
                        <label class="col-sm-3">Rekening Belanja</label>
                        <div class="col-sm-9">
                            <select class="form-control input_select_2 rekening_akun" id="rekening_akun" name="rekening_akun" onchange="get_data_sisa_pagu_per_akun_npd(this.value);">
                                <option value="">Pilih Rekening</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group set_keluar row">
                        <label class="col-sm-3">Sisa Pagu Rekening NPD</label>
                        <div class="col-sm-9">
                            <span id="sisa_pagu_rekening_bku" style="display: block; font-weight: 600; font-size: 1.3em;">Rp 0</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3">Rincian Belanja RKA</label>
                        <div class="col-sm-9">
                            <select id="rincian_rka" name="rincian_rka" class="form-control" onchange="set_uraian_bku();">
                                <option value="">Pilih Rincian Belanja</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group set_keluar row">
                        <label class="col-sm-3">Sisa Pagu Rincian Belanja</label>
                        <div class="col-sm-9">
                            <span id="sisa_pagu_rincian_belanja" style="display: block; font-weight: 600; font-size: 1.3em;">Rp 0</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3">Uraian BKU</label>
                        <div class="col-sm-9">
                            <textarea id="uraian_bku" name="uraian_bku" rows="4" cols="50"></textarea>
                        </div>
                    </div>
                    <div class="form-group set_keluar row">
                        <label class="col-sm-3">Nilai</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="pagu_bku" name="pagu_bku" required onchange="cek_nilai();">
                        </div>
                    </div>
                    <div class="form-group set_keluar row">
                        <label class="col-sm-3">Jenis Transaksi</label>
                        <div class="col-sm-9">
                            <label><input type="radio" name="jenis_transkasi" value="1"> Tunai</label>
                            <label style="margin-left: 30px;"><input type="radio" name="jenis_transkasi" value="2" checked> Non Tunai</label>
                        </div>
                    </div>
                    <div class="form-group set_keluar row">
                        <label class="col-sm-3">Nama Pemegang Rekening Bank</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nama_pemilik_rekening_bank_bku" name="nama_pemilik_rekening_bank_bku" required>
                        </div>
                    </div>
                    <div class="form-group set_keluar row">
                        <label class="col-sm-3">Nama Rekening Bank</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nama_rekening_bank_bku" name="nama_rekening_bank_bku" required>
                        </div>
                    </div>
                    <div class="form-group set_keluar row">
                        <label class="col-sm-3">No Rekening Bank</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="no_rekening_bank_bku" name="no_rekening_bank_bku" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary submitBtn" onclick="submit_data(this)">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    jQuery(document).ready(function(){
        load_data(); 
        window.rka_all = <?php echo json_encode($rka); ?>;
        jQuery('#rincian_rka').select2({width: '100%'});
        jQuery('.jenis_bku').click (function (){
            let val_check = jQuery("input[name='set_bku']:checked").val()
                if(val_check == 'terima'){
                    jQuery(".set_keluar").hide();
                }else{
                    jQuery(".set_keluar").show();
                }

        });
        jQuery('#set_tanggal').daterangepicker({
			singleDatePicker: true,
			showDropdowns: true,
            minYear: 2000,
            maxYear: parseInt(moment().format('YYYY'),10),
			locale: {
				format: 'DD-MM-YYYY'
			}
		});

        jQuery('input[type="radio"][name="jenis_transkasi"]').on('click', function(){
            var jenis_transaksi = jQuery('input[type="radio"][name="jenis_transkasi"]:checked').val();
            if(jenis_transaksi == 1){
                jQuery('#nama_pemilik_rekening_bank_bku').closest('.set_keluar').hide();
                jQuery('#nama_rekening_bank_bku').closest('.set_keluar').hide();
                jQuery('#no_rekening_bank_bku').closest('.set_keluar').hide();
            }else{
                jQuery('#nama_pemilik_rekening_bank_bku').closest('.set_keluar').show();
                jQuery('#nama_rekening_bank_bku').closest('.set_keluar').show();
                jQuery('#no_rekening_bank_bku').closest('.set_keluar').show();
            }
        });
    });

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
            .then(function(){
                jQuery('#wrap-loading').show();
                jQuery('#modal_tambah_data').modal('show');
                jQuery("#modal_tambah_data .modal-title").html("Tambah Buku Kas Umum Pembantu");
                jQuery("#modal_tambah_data .submitBtn")
                    .attr("onclick", `submit_data()`)
                    .attr("disabled", false)
                    .text("Simpan");
                jQuery('#wrap-loading').hide();
                jQuery("#nomor_npd_bku").val("<?php echo $data_npd['nomor_npd']; ?>");
            });
    }

    function get_data_akun_rka_per_npd() {
        return new Promise(function(resolve, reject){
            if(typeof dataAkun == 'undefined'){
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
                        // menampilkan popup
                        if (data.status == 'success') {
                            jQuery('.rekening_akun').html(data.data_akun_html);
                            jQuery('.rekening_akun').select2({width: '100%'});
                            resolve()
                        }
                    }
                });
            }else{
                jQuery('#rekening_akun').html(dataAkun.data_akun_html);
                jQuery('#rekening_akun').select2({width: '100%'});
                resolve()
            }
        });
    }

    function get_data_sisa_pagu_per_akun_npd(kode_rekening) {
        var opsi_rincian = '<option value="">Pilih rincian belanja</option>';
        if(kode_rekening == ''){
            jQuery("#sisa_pagu_rekening_bku").html(formatRupiah(0, true));
            jQuery('#rincian_rka').html(opsi_rincian).trigger('change');
            return;
        }
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: "<?php echo admin_url('admin-ajax.php'); ?>",
            type: "post",
            data: {
                "action": "get_data_sisa_pagu_per_akun_npd",
                "api_key": '<?php echo $api_key; ?>',
                "tahun_anggaran": <?php echo $input['tahun_anggaran']; ?>,
                "kode_sbl": "<?php echo $input['kode_sbl']; ?>",
                "kode_npd": "<?php echo $kode_npd; ?>",
                "kode_rekening": kode_rekening
            },
            dataType: "json",
            success: function(data) {
                jQuery('#wrap-loading').hide();
                // menampilkan popup
                if (data.status == 'success') {
                    window.sisa_rekening = data.data.pagu_dana_npd - data.data.total_pagu_bku;
                    jQuery("#sisa_pagu_rekening_bku").html(formatRupiah(sisa_rekening, true));

                    rka_all.map(function(b, i){
                        if(b.kode_akun == kode_rekening){
                            opsi_rincian += '<option value="'+b.id_rinci_sub_bl+'" nilai="'+b.rincian+'" koefisien="'+b.koefisien+'">'+b.nama_komponen+' '+b.spek_komponen+'</option>';
                        }
                    });
                    jQuery('#rincian_rka').html(opsi_rincian).trigger('change');
                }
            }
        });
    }
    
    function submit_data(that) {
        if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
            jQuery('#wrap-loading').show();
            let form = getFormData(jQuery("#tambah-bku"));
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: "post",
                dataType: 'json',
                data: {
                    action: 'tambah_data_bku',
                    api_key: '<?php echo $api_key; ?>',
                    kode_sbl: '<?php echo $input['kode_sbl']; ?>',
                    tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                    kode_npd: "<?php echo $kode_npd; ?>",
                    data: JSON.stringify(form)
                    
                },
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
                    console.error('AJAX Error:', status, error);
                }
            });
        }
    }

    function edit_data(id, tipe="keluar") {
        get_data_akun_rka_per_npd()
            .then(function(){
                jQuery('#wrap-loading').show();
                if(id == undefined || id == 0){
                    alert("Gagal edit data! || Data tidak ada!");
                    return false;
                }
                if(tipe == 'terima'){
                    jQuery("input[name='set_bku'][value='terima']").prop('checked', true);
                    jQuery(".set_keluar").hide();
                }else{
                    jQuery("input[name='set_bku'][value='keluar']").prop('checked', true);
                    jQuery(".set_keluar").show();
                }
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
                            jQuery('#modal_tambah_data').modal('show');
                            jQuery('#nomor_bukti_bku').val(response.data.nomor_bukti);
                            jQuery('#uraian_bku').val(response.data.uraian);
                            jQuery('#pagu_bku').val(response.data.pagu);
                            jQuery("#set_tanggal").val(response.data.tanggal_bkup);

                            jQuery("#modal_tambah_data .modal-title").html("Edit Buku Kas Umum Pembantu");
                            jQuery("#modal_tambah_data .submitBtn")
                                .attr("onclick", `submitEdit('${id}')`)
                                .attr("disabled", false)
                                .text("Simpan");
                            jQuery("#rekening_akun").val(response.data.kode_rekening).trigger('change');
                            jQuery("#nomor_npd_bku").val("<?php echo $data_npd['nomor_npd']; ?>");
                            jQuery("#nama_pemilik_rekening_bank_bku").val(response.data.nama_pemilik_rekening_bank);
                            jQuery("#nama_rekening_bank_bku").val(response.data.nama_rekening_bank);
                            jQuery("#no_rekening_bank_bku").val(response.data.no_rekening_bank);
                        } else {
                            alert(response.message);
                        }
                        jQuery('#wrap-loading').hide();
                    }
                });
            })
    }

    function submitEdit(id) {
        if(confirm('Apakah anda yakin untuk mengubah data ini?')){
            jQuery("#wrap-loading").show();
            let form = getFormData(jQuery("#tambah-bku"));
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: "post",
                dataType: 'json',
                data: {
                    action: 'edit_data_buku_kas_umum_pembantu',
                    api_key: '<?php echo $api_key; ?>',
                    id: id,
                    kode_sbl: '<?php echo $input['kode_sbl']; ?>',
                    tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                    kode_npd: "<?php echo $kode_npd; ?>",
                    data: JSON.stringify(form)
                    
                },
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
                    console.error('AJAX Error:', status, error);
                }
            });
        }
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
                    'api_key':'<?php echo $api_key; ?>',
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

    function getFormData($form){
        var disabled = $form.find('[disabled]');
        disabled.map(function(i, b){
            jQuery(b).attr('disabled', false);
        });
	    let unindexed_array = $form.serializeArray();
        disabled.map(function(i, b){
            jQuery(b).attr('disabled', true);
        });
        var data = {};
        unindexed_array.map(function(b, i){
            var nama_baru = b.name.split('[');
            if(nama_baru.length > 1){
                nama_baru = nama_baru[0];
                if(!data[nama_baru]){
                    data[nama_baru] = [];
                }
                data[nama_baru].push(b.value);
            }else{
                data[b.name] = b.value;
            }
        })
        return data;
	}

    function set_uraian_bku(){
        var id_rinci_sub_bl = jQuery('#rincian_rka').val();
        if(id_rinci_sub_bl == ''){
            jQuery('#sisa_pagu_rincian_belanja').html(formatRupiah(0, true));
            return;
        }
        var koefisien = jQuery('#rincian_rka option:checked').attr('koefisien');
        var nilai = +jQuery('#rincian_rka option:checked').attr('nilai');
        var komponen = jQuery('#rincian_rka option:checked').text();
        jQuery('#uraian_bku').val(komponen+' '+koefisien);
        jQuery('#wrap-loading').show();
        var id_unik = 'kode_sbl-rekening_belanja-kelompok-keterangan-'+id_rinci_sub_bl;
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'post',
            data: {
                "action": "get_mapping",
                "api_key": "<?php echo $api_key; ?>",
                "tahun_anggaran": <?php echo $input['tahun_anggaran']; ?>,
                "id_mapping": [id_unik]
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#wrap-loading').hide();
                var sisa_rincian_asli = 0;
                var sisa_rincian = 0;
                if (response.status == 'success') {
                    if(response.data[0]){
                        sisa_rincian_asli = nilai-response.data[0].data_realisasi;
                        if(sisa_rincian_asli > sisa_rekening){
                            sisa_rincian = sisa_rekening;
                        }
                    }
                    jQuery('#sisa_pagu_rincian_belanja').html(formatRupiah(sisa_rincian_asli, true));
                } else {
                    alert(`GAGAL! \n${response.message}`);
                }
                jQuery('#pagu_bku').val(sisa_rincian);
            }
        });
    }

    function cek_nilai(){
        var nilai = jQuery('#pagu_bku').val();
        if(nilai > sisa_rekening){
            alert('Nilai transaksi BKU tidak boleh lebih besar dari sisa rekening belanja '+formatRupiah(sisa_rekening, true));
            jQuery('#pagu_bku').val(sisa_rekening);
        }
    }
</script>
