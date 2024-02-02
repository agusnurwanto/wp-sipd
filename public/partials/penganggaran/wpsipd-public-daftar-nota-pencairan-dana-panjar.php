<?php
global $wpdb;

if (!empty($_GET) && !empty($_GET['tahun']) && !empty($_GET['kode_sbl'])) {
	$tahun_anggaran = $_GET['tahun'];
	$kode_sbl = $_GET['kode_sbl'];
} else {
	die('<h1 class="text-center">Tahun Anggaran dan Kode Sub Kegiatan tidak boleh kosong!</h1>');
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
		AND s.tahun_anggaran = %d', $kode_sbl, $tahun_anggaran), ARRAY_A);

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
	}
</style>
<div style="padding: 15px;">
    <h1 class="text-center" style="margin-top: 50px;">DAFTAR NOTA PENCAIRAN DANA</h1>
    <table id="tabel_detail_nota">
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
                <td><?php echo $kode_sub_kegiatan . '  ' . $nama_sub_kegiatan ?></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- table -->
<div style="padding: 15px;margin:0 0 3rem 0;">

    <!-- Button trigger modal -->
    <button class="btn btn-primary m-3" onclick="tambah_data_npd();"><i class="dashicons dashicons-plus-alt"></i> Tambah Data</button>

    <table id="table_daftar_panjar">
        <thead>
            <tr>
                <th class="atas kanan bawah kiri text-center">Nomor NPD</th>
                <th class="atas kanan bawah text-center">Jenis NPD</th>
                <th class="atas kanan bawah text-center">PPTK</th>
                <th class="atas kanan bawah text-center">Total Pencairan</th>
                <th class="atas kanan bawah text-center" style="width: 12em;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="kanan bawah text-right kiri text_blok">Jumlah</td>
                <td class="kanan bawah text_blok text-right">0</td>
                <td class="kanan bawah"></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_tambah_data" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Tambah Nota Pencairan Dana | Panjar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tambah-panjar">
                    <div class="form-group">
                        <label>Nomor Nota Pencairan Dana | Panjar</label>
                        <input type="text" class="form-control" id="nomor_npd" name="nomor_npd" required>
                    </div>
                    <div class="form-group">
                        <label class="d-block">Jenis Nota Pencairan Dana</label>
                        <input type="radio" class="ml-2" id="set_panjar" name="set_panjar" value="dengan_panjar">
                        <label for="set_panjar">Panjar</label>
                        <input type="radio" id="set_no_panjar" name="set_panjar" value="tanpa_panjar">
                        <label for="set_no_panjar">Tanpa Panjar</label>
                    </div>
                    <div class="form-group">
                        <label>PPTK</label>
                        <select class="form-control" id="set_pptk" name="set_pptk" required>
                            <option value="">Pilih PPTK</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nomor DPA</label>
                        <input type="text" class="form-control" id="nomor_dpa" name="nomor_dpa" required>
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

<!-- Modal Tambah Rekening-->
<div class="modal fade" id="modal_tambah_rekening" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Tambah Rekening</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tambah-rekening">
                    <input type="number" class="form-control" id="id_npd_rek" name="id_npd_rek" hidden required>
                    <div class="form-group">
                        <label>Nomor Nota Pencairan Dana | Panjar</label>
                        <input type="text" class="form-control" id="nomor_npd_rek" name="nomor_npd_rek" required>
                    </div>
                    <div class="form-group">
                        <label>Pilih Rekening</label>
                        <table id="input_rekening" class="input_rekening" style="margin: 0;">
                            <tr data-id="1">
                                <td style="width: 60%; max-width:100px;">
                                    <select class="form-control input_select_2 rekening_akun" id="rekening_akun_1" name="rekening_akun[1]">
                                        <option value="">Pilih Rekening</option>
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control input_number" id="pagu_rekening_1" type="number" name="pagu_rekening[1]"/>
                                </td>
                                <td style="width: 70px" class="text-center detail_tambah">
                                    <button class="btn btn-warning btn-sm" onclick="tambahRekening(); return false;"><i class="dashicons dashicons-plus"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary submitBtn" onclick="submit_data_rekening(this)">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        load_data();
    });

    function pass_visibility() {
        let pass = jQuery("#password");
        if (pass.attr('type') === "password") {
            pass.attr('type', 'text');
        } else {
            pass.attr('type', 'password');
        }
    }

    function edit_pass_visibility() {
        let pass = jQuery("#passForm");
        let cekBox = jQuery("#edit_pass");
        if (cekBox.prop("checked")) {
            pass.prop('disabled', false).show();
        } else {
            pass.prop('disabled', true).hide();
        }
    }

    function tambah_data_npd() {
        jQuery('#wrap-loading').show();
        get_pptk()
            .then(function(){
                jQuery('#modal_tambah_data').modal('show');
                jQuery("#modal_tambah_data .modal-title").html("Tambah Nota Pencairan Dana | Panjar");
                jQuery("#modal_tambah_data .submitBtn")
                    .attr("onclick", `submit_data()`)
                    .attr("disabled", false)
                    .text("Simpan");
                jQuery('#wrap-loading').hide();
            })
    }

    function tambah_rekening(id) {
        get_data_akun_rka_per_sub_keg()
            .then(function(){
                jQuery('#wrap-loading').show();
                jQuery.ajax({
                    method: 'post',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    dataType: 'json',
                    data: {
                        'action': 'get_nota_panjar_by_id',
                        'api_key': '<?php echo $api_key; ?>',
                        'id': id,
                        'tahun_anggaran': <?php echo $tahun_anggaran; ?>,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            jQuery('#modal_tambah_rekening').modal('show');
                            jQuery('#id_npd_rek').val(id);
                            jQuery('#nomor_npd_rek').val(response.data.nomor_npd);
                            jQuery('#nomor_npd_rek').prop('disabled', true);

                            /** Start */
                            response.data.rekening_npd.map(function(value, index){
                                let id = index+1;
                                new Promise(function(resolve, reject){
                                    if(id > 1){
                                        tambahRekening()
                                        .then(function(){
                                            resolve(value);
                                        })
                                    }else{
                                        resolve(value);
                                    }
                                })
                                .then(function(value){
                                    jQuery("#rekening_akun_"+id).val(value.kode_rekening).trigger('change');
                                    jQuery("#pagu_rekening_"+id).val(value.pagu_dana);
                                });
                            })
                            /** End */

                            jQuery("#modal_tambah_rekening .modal-title").html("Tambah Rekening Nota Pencairan Dana | Panjar");
                            jQuery("#modal_tambah_rekening .submitBtn")
                                .attr("onclick", `submit_data_rekening('${id}')`)
                                .attr("disabled", false)
                                .text("Simpan");
                        } else {
                            alert(response.message);
                        }
                        jQuery('#wrap-loading').hide();
                    }
                });
            });
    }

    function tambahRekening(){
        return new Promise(function(resolve, reject){
            var id = +jQuery('.input_rekening > tbody tr').last().attr('data-id');
            var newId = id+1;
            var trNew = jQuery('.input_rekening > tbody tr').last().html();
            trNew = ''
                +'<tr data-id="'+newId+'">'
                    +trNew
                +'</tr>';
            trNew = trNew.replaceAll('_'+id+'"', '_'+newId+'"');
            trNew = trNew.replaceAll('['+id+']', '['+newId+']');
            var tbody = jQuery('.input_rekening > tbody');
            tbody.append(trNew);
            jQuery('.input_rekening > tbody tr[data-id="'+newId+'"] .select2').remove();
            jQuery('.input_rekening > tbody tr[data-id="'+newId+'"] select').select2({width: '100%'});
            var tr = tbody.find('>tr');
            var length = tr.length-1;
            tr.map(function(i, b){
                if(i == 0){
                    var html = '<button class="btn btn-warning btn-sm" onclick="tambahRekening(); return false;"><i class="dashicons dashicons-plus"></i></button>';
                }else{
                    var html = '<button class="btn btn-danger btn-sm" onclick="hapusRekening(this); return false;"><i class="dashicons dashicons-trash"></i></button>';
                }
                jQuery(b).find('>td').last().html(html);
            });
            resolve();
        });
    }

    function submit_data_rekening() {
        if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
            jQuery('#wrap-loading').show();
            let form = getFormData(jQuery("#tambah-rekening"));
            if(typeof form.id_user_rek == undefined || form.id_user_rek == ''){
                alert("Submit gagal, harap refresh halaman!");
                return false;
            }

            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: "post",
                dataType: 'json',
                data: {
                    action: 'tambah_data_rekening_panjar',
                    api_key: '<?php echo $api_key; ?>',
                    kode_sbl: '<?php echo $kode_sbl; ?>',
                    tahun_anggaran: <?php echo $tahun_anggaran; ?>,
                    data: JSON.stringify(form)
                    
                },
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    console.log(response);
                    if (response.status == 'success') {
                        alert(response.message);
                        jQuery('#modal_tambah_rekening').modal('hide');
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

    function get_pptk() {
        return new Promise(function(resolve, reject){
            if(typeof dataPptk == 'undefined'){
                jQuery.ajax({
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    type: "post",
                    data: {
                        "action": "get_sub_keg_pptk",
                        "api_key": '<?php echo $api_key; ?>',
                        "tahun_anggaran": <?php echo $tahun_anggaran; ?>,
                        "id_skpd": <?php echo $id_sub_skpd; ?>,
                        "kode_sbl": "<?php echo $kode_sbl; ?>",
                    },
                    dataType: "json",
                    success: function(data) {
                        // menampilkan popup
                        if (data.status == 'success') {
                            window.dataPptk = data;
                            jQuery('#set_pptk').html(data.user_pptk_html);
                            jQuery('#set_pptk').select2({width: '100%'});
                            resolve()
                        }
                    }
                });
            }else{
                jQuery('#set_pptk').html(dataPptk.user_pptk_html);
                jQuery('#set_pptk').select2({width: '100%'});
                resolve()
            }
        });
    }

    function get_data_akun_rka_per_sub_keg() {
        return new Promise(function(resolve, reject){
            if(typeof dataRekening == 'undefined'){
                jQuery.ajax({
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    type: "post",
                    data: {
                        "action": "get_rka_sub_keg_akun",
                        "api_key": '<?php echo $api_key; ?>',
                        "tahun_anggaran": <?php echo $tahun_anggaran; ?>,
                        "kode_sbl": "<?php echo $kode_sbl; ?>",
                    },
                    dataType: "json",
                    success: function(data) {
                        window.dataRekening = data;
                        // menampilkan popup
                        if (data.status == 'success') {
                            jQuery('.rekening_akun').html(data.data_akun_html);
                            jQuery('.rekening_akun').select2({width: '100%'});
                            resolve()
                        }
                    }
                });
            }else{
                jQuery('#rekening_akun').html(dataRekening.data_akun_html);
                jQuery('#rekening_akun').select2({width: '100%'});
                resolve()
            }
        });
    }

    function load_data() {
        jQuery.ajax({
			type: 'POST',
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {
                api_key: '<?php echo $api_key; ?>',
                action: 'get_daftar_panjar',
                kode_sbl: '<?php echo $kode_sbl; ?>',
                tahun_anggaran: <?php echo $tahun_anggaran; ?>,
            },
			success: function(data) {
				jQuery('#wrap-loading').hide();
				const response = JSON.parse(data);
				if (response.status === 'success') {
					jQuery('#table_daftar_panjar > tbody').html(response.html);
				} else {
					alert('Error: ' + response.message);
				}
			}

		});
    }

    function submit_data(that) {
        if(confirm('Apakah anda yakin untuk menyimpan data ini?')){
            jQuery('#wrap-loading').show();
            let form = getFormData(jQuery("#tambah-panjar"));
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: "post",
                dataType: 'json',
                data: {
                    action: 'tambah_data_panjar',
                    api_key: '<?php echo $api_key; ?>',
                    kode_sbl: '<?php echo $kode_sbl; ?>',
                    tahun_anggaran: <?php echo $tahun_anggaran; ?>,
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
                    'action': 'delete_data_panjar',
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

    function edit_data(id) {
        get_pptk()
            .then(function(){
                jQuery('#wrap-loading').show();
                jQuery.ajax({
                    method: 'post',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    dataType: 'json',
                    data: {
                        'action': 'get_nota_panjar_by_id',
                        'api_key': '<?php echo $api_key; ?>',
                        'id': id,
                        'tahun_anggaran': <?php echo $tahun_anggaran; ?>,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            jQuery('#modal_tambah_data').modal('show');
                            jQuery('#nomor_npd').val(response.data.nomor_npd);
                            jQuery('#nomor_dpa').val(response.data.nomor_dpa);
                            jQuery('input[name=set_panjar][value="'+response.data.jenis_panjar+'"]').prop('checked', true);

                            jQuery("#modal_tambah_data .modal-title").html("Edit Nota Pencairan Dana | Panjar");
                            jQuery("#modal_tambah_data .submitBtn")
                                .attr("onclick", `submitEdit('${id}')`)
                                .attr("disabled", false)
                                .text("Simpan");
                            jQuery('select[id="set_pptk"]').val(response.data.id_user_pptk).trigger('change');
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
            let form = getFormData(jQuery("#tambah-panjar"));
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: "post",
                dataType: 'json',
                data: {
                    action: 'edit_data_panjar',
                    api_key: '<?php echo $api_key; ?>',
                    id: id,
                    kode_sbl: '<?php echo $kode_sbl; ?>',
                    tahun_anggaran: <?php echo $tahun_anggaran; ?>,
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