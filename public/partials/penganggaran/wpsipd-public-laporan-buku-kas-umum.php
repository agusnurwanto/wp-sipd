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
    $pagu_kegiatan = number_format($data_rfk['pagu'], 0, ",", ".");
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
?>
<style>
    .modal-content label:after {
        content: ' *';
        color: red;
        margin-right: 5px;
    }
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
<div style="padding: 15px;">    
    <h3 class="text-center" style="margin-top: 50px;">PEMERINTAH <?php echo strtoupper($nama_pemda) ?></br><?php echo $nama_skpd; ?></br>TAHUN ANGGARAN <?php echo $input['tahun_anggaran']; ?></h3>
    <table id="tabel_detail_nota" style="margin-top: 30px;">
        <tbody>
            <tr>
                <td>Kegiatan</td>
                <td>:</td>
                <td><?php echo $kode_kegiatan . '  ' . $nama_kegiatan ?></td>
            </tr>
            <tr>
                <td>Sub Kegiatan</td>
                <td>:</td>
                <td><?php echo $kode_sub_kegiatan . '  ' . $nama_sub_kegiatan ?></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- table -->
<div style="padding: 15px;margin:0 0 3rem 0;">

    <!-- Button trigger modal -->
    <button class="btn btn-primary m-3" onclick="tambah_data_bku();"><i class="dashicons dashicons-plus-alt"></i> Tambah Kas Umum Pembantu</button>
    
    <table id="table_daftar_bku">
        <thead>
            <tr>
                <th class="atas kanan bawah kiri text-center">Tanggal</th>
                <th class="atas kanan bawah text-center">Nomor Bukti</th>
                <th class="atas kanan bawah text-center">Uraian</th>
                <th class="atas kanan bawah text-center" style="width: 10em;">Penerimaan</th>
                <th class="atas kanan bawah text-center" style="width: 10em;">Pengeluaran</th>
                <th class="atas kanan bawah text-center" style="width: 10em;">Saldo</th>
                <th class="atas kanan bawah text-center" style="width: 12em;">Aksi</th>
            </tr>
        </thead>
        <tbody>
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
                    <div class="form-group">
                        <label>Nomor NPD</label>
                        <input type="text" class="form-control" id="nomor_npd_bku" name="nomor_npd_bku" disabled>
                    </div>
                    <div class="form-group">
                        <label class="d-block">Jenis Buku Kas Umum Pembantu</label>
                        <input type="radio" class="ml-2 jenis_bku" id="penerimaan_bku" name="set_bku" value="terima" >
                        <label for="penerimaan_bku">Penerimaan</label>
                        <input type="radio" class="jenis_bku" id="pengeluaran_bku" name="set_bku" value="keluar" checked>
                        <label for="pengeluaran_bku">Pengeluaran</label>
                    </div>
                    <div class="form-group set_keluar">
                        <label>Nomor Rekening</label>
                            <select class="form-control input_select_2 rekening_akun" id="rekening_akun" name="rekening_akun" onchange="get_data_sisa_pagu_per_akun_npd(this.value);">
                                <option value="">Pilih Rekening</option>
                            </select>
                    </div>
                    <div class="form-group set_keluar">
                        <label>Nomor Bukti</label>
                        <input type="text" class="form-control" id="nomor_bukti_bku" name="nomor_bukti_bku" required>
                    </div>
                    <div class="form-group">
                        <label>Uraian</label>
                        <textarea id="uraian_bku" name="uraian_bku" rows="4" cols="50"></textarea>
                    </div>
                    <div class="form-group set_keluar">
                        <label>Sisa Pagu Rekening NPD</label>
                        <span id="sisa_pagu_rekening_bku" style="display: block; font-weight: 600; font-size: 1.3em;">Rp. 0</span>
                    </div>
                    <div class="form-group set_keluar">
                        <label>Pagu</label>
                        <input type="number" class="form-control" id="pagu_bku" name="pagu_bku" required>
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
<script>
    jQuery(document).ready(function(){
        load_data(); 
        jQuery('.jenis_bku').click (function (){
            let val_check = jQuery("input[name='set_bku']:checked").val()
                if(val_check == 'terima'){
                    jQuery(".set_keluar").hide();
                }else{
                    jQuery(".set_keluar").show();
                }

        });
    });

    function load_data() {
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                api_key: '<?php echo $api_key; ?>',
                action: 'get_daftar_bku',
                kode_sbl: '<?php echo $input['kode_sbl']; ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
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
                    // menampilkan popup
                    if (data.status == 'success') {
                        let sisa = data.data.pagu_dana_npd - data.data.total_pagu_bku;
                        jQuery("#sisa_pagu_rekening_bku").html("Rp. "+sisa)
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

                            jQuery("#modal_tambah_data .modal-title").html("Edit Buku Kas Umum Pembantu");
                            jQuery("#modal_tambah_data .submitBtn")
                                .attr("onclick", `submitEdit('${id}')`)
                                .attr("disabled", false)
                                .text("Simpan");
                            jQuery("#rekening_akun").val(response.data.kode_rekening).trigger('change');
                            jQuery("#nomor_npd_bku").val("<?php echo $data_npd['nomor_npd']; ?>");
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
</script>
