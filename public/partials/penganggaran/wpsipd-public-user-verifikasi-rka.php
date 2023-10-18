<?php
global $wpdb;

$roles = $this->role_verifikator();
if(empty($roles)){
    die('<h1 class="text-center" style="margin-top: 50px;">Daftar user verifikator belum diset dihalaman dashboard Verifikasi RKA!</h1>');
}
$options_role = array();
foreach ($roles as $val) {
    $role = get_role($val);
    if (empty($role)) {
        add_role($val, $val, array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false
        ));
    }
    if ($val != 'pptk') {
        $options_role[] = "<option value='$val'>$val</option>";
    }
}
$api_key = get_option('_crb_api_key_extension');
$fokus_uraian = array(
    // bappeda
    'Indikator Masukan',
    'Indikator Keluaran Kegiatan',
    'Indikator Hasil',
    'Kelompok Sasaran',
    'Sub Kegiatan',
    'Sumber Pendanaan',
    'Lokasi',
    'Indikator Keluaran Sub Kegiatan',
    'Waktu Pelaksanaan',
    'Kesesuaian Anggaran Musrenbang RKPD',
    'Kesesuaian Anggaran POKIR RKPD',
    'Kesesuaian Anggaran Hibah RKPD',
    'Kesesuaian Anggaran Prioritas RKPD',
    'Kesesuaian Belanja dengan Keluaran Sub Kegiatan',
    // bpkad
    'Kesesuaian Rekening Belanja dengan Output Sub Kegiatan',
    'Belanja Rutin sudah dianggarkan selama 12 bulan',
    'Kesesuaian Belanja dengan Standar Satuan Harga (SSH)',
    'Kesesuaian Belanja dengan Standar Biaya Umum (SBU)',
    'Kesesuaian Belanja dengan Harga Satuan Pekerjaan Konstruksi (HSPK)',
    'Kesesuaian Belanja dengan Analisis Standar Belanja (ASB)',
    'Belanja Modal dianggarkan berdasarkan harga perolehan',
    'Belanja Modal masuk dalam Rencana Kebutuhan Barang Milik Daerah (RKBMD) dan merupakan Aset Pemerintah Daerah',
    'Belanja Pemeliharaan sebagai Belanja Barang dan Jasa apabila hanya mengembalikan pada kondisi semula',
    'Belanja Pemeliharaan sebagai Belanja Modal apabila menambah volume, luas, kualitas, mutu dan umur masa manfaat',
    // pbj
    'Penggunaan Produk Dalam Negri (PDN)',
    'Penganggaran Belanja Jasa Konsultan Perancang',
    'Pengadaan Wajib Menggunakan Fasilitas Katalog Konsolidasi Nasional',
    'Identifikasi Rekening Belanja yang dapat Dikonsolidasi pada Proses Pengadaan',
    'Input RUP pada Aplikasi SIRUP',
    'Penggunaan Metode E-purchasing melalui Transaksi Katalog Elektronik Lokal'
);
?>
<style>
    .modal-content label:after {
        content: ' *';
        color: red;
        margin-right: 5px;
    }
</style>
<h1 class="text-center" style="margin-top: 50px;">Manajemen User Verifikasi RKA (Rencana Kerja dan Anggaran)</h1>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" onclick="tambah_data();" style="margin-left: 15px;"><i class="dashicons dashicons-plus-alt"></i> Tambah Data</button>

<!-- table -->
<div style="padding: 15px;margin:0 0 3rem 0;">
    <table class="table table-bordered" id="daftar-user">
        <thead>
            <tr>
                <th class="text-center">Username</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Nomor WA</th>
                <th class="text-center">Role</th>
                <th class="text-center">Nama Bidang</th>
                <th class="text-center">Fokus Uraian</th>
                <th class="text-center" style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

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

    function tambah_data() {
        jQuery('#id_user').val('');
        jQuery('#username').val('').prop('disabled', false);
        jQuery('#password').val('').prop('disabled', false).show();
        jQuery('#passForm').val('').prop('disabled', false).show();
        jQuery('#nama').val('').prop('disabled', false);
        jQuery('#email').val('').prop('disabled', false);
        jQuery('#nomorwa').val('').prop('disabled', false);
        jQuery('#role').val('').prop('disabled', false);
        jQuery('#edit_pass').prop('disabled', true).hide();
        jQuery('#checkbox_edit_pass').prop('disabled', true).hide();
        jQuery('#fokus_uraian').val('<?php echo implode(' | ', $fokus_uraian); ?>');
        jQuery('#modal_tambah_data').modal('show');
    }

    function load_data() {
        if (typeof load_data_user == 'undefined') {
            window.load_data_user = jQuery('#daftar-user').on('preXhr.dt', function(e, settings, data) {
                jQuery("#wrap-loading").show();
            }).DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        api_key: '<?php echo $api_key; ?>',
                        action: 'get_user_verifikator'
                    }
                },
                "bPaginate": false,
                lengthMenu: [
                    [20, 50, 100, -1]
                    [20, 50, 100, "All"]
                ],
                order: [
                    [0, 'asc']
                ],
                "drawCallback": function(settings) {
                    jQuery("#wrap-loading").hide();
                },
                "columns": [
                    {
                        "data": 'user',
                        className: "text-center"
                    },
                    {
                        "data": 'nama',
                        className: "text-center"
                    },
                    {
                        "data": 'nomorwa',
                        className: "text-center"
                    },
                    {
                        "data": 'role',
                        className: "text-center"
                    },
                    {
                        "data": 'nama_bidang_skpd',
                        className: "text-center"
                    },
                    {
                        "data": 'fokus_uraian',
                        className: "text-center"
                    },
                    {
                        "data": 'aksi',
                        className: "text-center"
                    }
                ]
            });
        } else {
            load_data_user.draw();
        }
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

        console.log(namaBidang);
        console.log(fokusUraian);
        
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
                    'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
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
                'api_key': '<?php echo get_option('_crb_api_key_extension'); ?>',
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