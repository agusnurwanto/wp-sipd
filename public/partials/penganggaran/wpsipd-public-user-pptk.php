<?php
global $wpdb;

$roles = array(
    'pptk'
);
if (empty($roles)) {
    die('<h1 class="text-center" style="margin-top: 50px;">Daftar user pptk belum diset dihalaman dashboard Verifikasi RKA!</h1>');
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
}
$api_key = get_option('_crb_api_key_extension');
$pptk_role = 'pptk';

$idtahun = $wpdb->get_results("select distinct tahun_anggaran from data_unit", ARRAY_A);
$form_tahun = "";
foreach ($idtahun as $val) {
    $skpd = $wpdb->get_results($wpdb->prepare("
        SELECT 
            nama_skpd, 
            id_skpd, 
            kode_skpd,
            is_skpd
        from data_unit 
        where active=1
            and tahun_anggaran=%d
        group by id_skpd", $val['tahun_anggaran']), ARRAY_A);
    $opsi_skpd = '<option value="">Pilih SKPD</option>';
    foreach ($skpd as $v) {
        $opsi_skpd .= '<option value="' . $val['tahun_anggaran'] . '-' . $v['id_skpd'] . '">' . $v['kode_skpd'] . ' ' . $v['nama_skpd'] . '</option>';
    }
    $form_tahun .= '
    <div class="form-group">
        <label>Pilih SKPD Tahun Anggaran ' . $val['tahun_anggaran'] . '</label>
        <select class="form-control skpd_tahun_anggaran" data-year="' . $val['tahun_anggaran'] . '">
            ' . $opsi_skpd . '
        </select>
    </div>
    ';
}
?>
<style>
    .modal-content label:after {
        content: ' *';
        color: red;
        margin-right: 5px;
    }
</style>
<h1 class="text-center" style="margin-top: 50px;">Manajemen User PPTK (Pejabat Pelaksana Teknis Kegiatan)</h1>

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
                <th class="text-center">Nama SKPD</th>
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
            </tr>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_tambah_data" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Tambah Data User PPTK</h5>
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
                    <label>Role</label>
                    <input type="text" class="form-control" id="roles" required>
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
                <?php echo $form_tahun; ?>
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
        jQuery('.skpd_tahun_anggaran').val('').prop('disabled', false);
        jQuery('#roles').val('<?php echo $pptk_role ?>').prop('disabled', true);
        jQuery('#nomorwa').val('').prop('disabled', false);
        jQuery('#edit_pass').prop('disabled', true).hide();
        jQuery('#checkbox_edit_pass').prop('disabled', true).hide();
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
                        action: 'get_user_pptk'
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
                "columns": [{
                        "data": 'user',
                        className: "text-center"
                    },
                    {
                        "data": 'nama',
                        className: "text-center"
                    },
                    {
                        "data": 'nomorwa',
                        className: "text-left"
                    },
                    {
                        "data": 'skpd',
                        className: "text-left"
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
        const roles = jQuery('#roles').val();
        const nomorwa = jQuery('#nomorwa').val();
        var skpd = {};
        jQuery('.skpd_tahun_anggaran').map(function(i, b) {
            var val = jQuery(b).val().split('-');
            if (val.length >= 2) {
                skpd[val[0]] = val[1];
            }
        });
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
                roles: roles,
                skpd: skpd,
                nomorwa: nomorwa,
                id_user: id_user,
                action: 'tambah_user_pptk'
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
                    'action': 'delete_user_pptk',
                    'api_key': '<?php echo $api_key; ?>',
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
                'action': 'get_user_pptk_by_id',
                'api_key': '<?php echo $api_key; ?>',
                'id': id,
            },
            success: function(res) {
                if (res.status == 'success') {
                    jQuery('#id_user').val(res.data.id_user);
                    jQuery('#username').val(res.data.user_login).prop('disabled', false);
                    jQuery('#password').val(res.data.password).prop('disabled', false);
                    jQuery('#nama').val(res.data.display_name).prop('disabled', false);
                    jQuery('#roles').val(res.data.roles).prop('disabled', true);
                    jQuery('#email').val(res.data.user_email).prop('disabled', false);
                    jQuery('.skpd_tahun_anggaran').val('');
                    let skpdData = res.data.skpd[0]; // fetch the first item from skpd array
                    // Loop through the years in skpdData
                    for (let year in skpdData) {
                        // Create the formatted value for the dropdown based on the year and its value
                        let formattedValue = year + "-" + skpdData[year];
                        // Target dropdown based on the year and set its value
                        jQuery(`.skpd_tahun_anggaran[data-year="${year}"]`).val(formattedValue).prop('disabled', false);
                    }
                    jQuery('#nomorwa').val(res.data.nomorwa).prop('disabled', false);
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