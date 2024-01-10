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
    $kode_program = $data_rfk['kode_program'];
    $nama_program = $data_rfk['nama_program'];
    $kode_kegiatan = $data_rfk['kode_giat'];
    $nama_kegiatan = $data_rfk['nama_giat'];
    $kode_bidang_urusan = $data_rfk['kode_bidang_urusan'];
    $nama_sub_kegiatan = $data_rfk['nama_sub_giat'];
    $nama_sub_kegiatan = str_replace('X.XX', $kode_bidang_urusan, $nama_sub_kegiatan);
    $pagu_kegiatan = number_format($data_rfk['pagu'], 0, ",", ".");
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
<!-- <div style="padding: 15px;">
    <h1 class="text-center" style="margin-top: 50px;">NOTA PENCAIRAN DANA</h1>
    <table id="tabel_detail_nota">
        <tbody>
            <tr>
                <td style="width: 12%;">Jenis NPD</td>
                <td style="width: 20px;">:</td>
                <td>
                    <input type="radio" id="set_panjar_true" name="set_panjar" value="set_panjar">
                    <label for="html" style="margin-bottom: 0rem;">Panjar</label>
                    <input type="radio" id="set_panjar_false" class="ml-5" name="set_panjar" value="not_set_panjar">
                    <label for="html" style="margin-bottom: 0rem;">Tanpa Panjar</label>
                </td>
            </tr>
            <tr>
                <td>PPTK</td>
                <td>:</td>
                <td>nama pptk</td>
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
                <td><?php echo $nama_sub_kegiatan ?></td>
            </tr>
            <tr>
                <td>Nomor DPA</td>
                <td>:</td>
                <td>nama Nomor DPA</td>
            </tr>
            <tr>
                <td>Tahun Anggaran</td>
                <td>:</td>
                <td><?php echo $tahun_anggaran ?></td>
            </tr>
        </tbody>
    </table>
</div> -->

<!-- table -->
<div style="padding: 15px;margin:0 0 3rem 0;">
    <h1 class="text-center" style="margin-top: 50px;">Daftar Nota Pencairan Dana</h1>

    <!-- Button trigger modal -->
    <button class="btn btn-primary m-3" onclick="tambah_data_npd()"><i class="dashicons dashicons-plus-alt"></i> Tambah Data</button>

    <table id="table_data_npd">
        <thead>
            <tr>
                <th class="atas kanan bawah kiri text-center" style="width: 6px;">No</th>
                <th class="atas kanan bawah text-center">Nomor NPD</th>
                <th class="atas kanan bawah text-center">Uraian</th>
                <th class="atas kanan bawah text-center">Anggaran</th>
                <th class="atas kanan bawah text-center">Sisa Anggaran</th>
                <th class="atas kanan bawah text-center">Pencairan</th>
                <th class="atas kanan bawah text-center" style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="kanan bawah text-right kiri text_blok">Jumlah</td>
                <td class="kanan bawah text_blok text-right">0</td>
                <td class="kanan bawah text_blok text-right">0</td>
                <td class="kanan bawah text_blok text-right">0</td>
                <td class="kanan bawah"></td>
            </tr>
        </tfoot>
    </table>
</div>
<!-- <div style="padding: 15px;margin:0 0 3rem 0;">
    <table id="table_data_npd">
        <thead>
            <tr>
                <th class="atas kanan bawah kiri text-center" style="width: 6px;">No</th>
                <th class="atas kanan bawah text-center">Kode Rekening</th>
                <th class="atas kanan bawah text-center">Uraian</th>
                <th class="atas kanan bawah text-center">Anggaran</th>
                <th class="atas kanan bawah text-center">Sisa Anggaran</th>
                <th class="atas kanan bawah text-center">Pencairan</th>
                <th class="atas kanan bawah text-center" style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="kanan bawah text-right kiri text_blok">Jumlah</td>
                <td class="kanan bawah text_blok text-right">0</td>
                <td class="kanan bawah text_blok text-right">0</td>
                <td class="kanan bawah text_blok text-right">0</td>
                <td class="kanan bawah"></td>
            </tr>
        </tfoot>
    </table>
</div> -->

<!-- Modal -->
<div class="modal fade" id="modal_tambah_data" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Tambah Data User Verifikasi RKA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="id_user">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="username" required>
                    <small class="form-text text-muted">
                        Username minimal 5 karakter
                    </small>
                </div>
                <div class="form-group" id="passForm">
                    <label>Password</label>
                    <input type="password" class="form-control" id="password">
                    <input type="checkbox" onclick="pass_visibility()" style="margin-right: 2px;"> Lihat Password
                    <small class="form-text text-muted">
                        Password minimal 8 karakter, mengandung huruf, angka, dan karakter unik
                    </small>
                </div>
                <div class="form-group" id="checkbox_edit_pass">
                    <input type="checkbox" id="edit_pass" onclick="edit_pass_visibility()" style="margin-right: 2px;" checked> Edit Password
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" class="form-control" id="nama" required>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" class="form-control" id="email" required>
                </div>
                <div class="form-group">
                    <label>Nomor WA</label>
                    <input type="text" class="form-control" id="nomorwa" required>
                    <small class="form-text text-muted">
                        Nomor WhatsApp harus dimulai dengan +62 dan berisi maksimal 15 angka.
                    </small>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select class="form-control" id="role" required>
                        <option value="">Pilih role user</option>
                        <?php echo implode('', $options_role); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Bidang atau SKPD</label>
                    <input type="text" class="form-control" id="nama_bidang_skpd" required>
                </div>
                <div class="form-group">
                    <label>Fokus uraian yang verifikasi</label>
                    <textarea class="form-control" id="fokus_uraian" required value=""></textarea>
                    <small class="form-text text-muted">Diinput bisa lebih dari satu, dipisah dengan tanda garis lurus berspasi ( | )</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_data(this)">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        // load_data();
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
        console.log("hei..")
    }

    function load_data() {
        jQuery.ajax({
			type: 'POST',
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {
                api_key: '<?php echo $api_key; ?>',
                action: 'get_data_nota_pencairan_dana',
                kode_sbl: '<?php echo $kode_sbl; ?>',
                tahun_anggaran: <?php echo $tahun_anggaran; ?>,
            },
			success: function(data) {
				jQuery('#wrap-loading').hide();
				const response = JSON.parse(data);
				if (response.status === 'success') {
					jQuery('#table_data_npd > tbody').html(response.html);
				} else {
					alert('Error: ' + response.message);
				}
			}

		});
    }


    function submit_data(that) {
        jQuery('#wrap-loading').show();
        const username = jQuery('#username').val();
        const password = jQuery('#password').val();
        const nama = jQuery('#nama').val();
        const email = jQuery('#email').val();
        const nomorwa = jQuery('#nomorwa').val();
        const role = jQuery('#role').val();
        const namaBidang = jQuery('#nama_bidang_skpd').val();
        const fokusUraian = jQuery('#fokus_uraian').val();
        const id_user = jQuery('#id_user').val();
        
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                api_key: '<?php echo $api_key; ?>',
                username: username,
                password: password,
                nama: nama,
                email: email,
                role: role,
                nomorwa: nomorwa,
                nama_bidang_skpd: namaBidang,
                fokus_uraian: fokusUraian,
                id_user: id_user,
                action: 'tambah_user_verifikator'
            },
            success: function(data) {
                jQuery('#wrap-loading').hide();
                const response = JSON.parse(data);
                if (response.status === 'success') {
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

    function delete_data(id) {
        let confirmDelete = confirm("Apakah anda yakin akan menghapus user ini?");
        if (confirmDelete) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                data: {
                    'action': 'delete_user_verifikator',
                    'api_key':'<?php echo $api_key; ?>',
                    'id': id
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    if (response.status == 'success') {
                        load_data();
                        alert(`User berhasil dihapus!`);
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    function edit_data(id) {
        jQuery('#wrap-loading').show();
        jQuery('#id_user').val('');
        jQuery.ajax({
            method: 'post',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                'action': 'get_user_verifikator_by_id',
                'api_key': '<?php echo $api_key; ?>',
                'id': id,
            },
            success: function(res) {
                if (res.status == 'success') {
                    jQuery('#id_user').val(res.data.id_user);
                    jQuery('#username').val(res.data.user_login).prop('disabled', false);
                    jQuery('#password').val(res.data.password).prop('disabled', false);
                    jQuery('#nama').val(res.data.display_name).prop('disabled', false);
                    jQuery('#email').val(res.data.user_email).prop('disabled', false);
                    jQuery('#nomorwa').val(res.data.nomorwa).prop('disabled', false);
                    jQuery('#nama_bidang_skpd').val(res.data.nama_bidang_skpd).prop('disabled', false);
                    jQuery('#fokus_uraian').val(res.data.fokus_uraian).prop('disabled', false);
                    jQuery('#role').val(res.data.roles).prop('disabled', false);
                    jQuery('#edit_pass').prop('disabled', false).show();
                    jQuery('#checkbox_edit_pass').prop('disabled', false).show();
                    jQuery('#modal_tambah_data').modal('show');
                } else {
                    alert(res.message);
                }
                jQuery('#wrap-loading').hide();
            }
        });
    }
</script>