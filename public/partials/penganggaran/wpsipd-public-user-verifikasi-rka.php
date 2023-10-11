<?php
global $wpdb;

$roles = array('verifikator_bappeda', 'verifikator_bppkad', 'verifikator_pbj', 'verifikator_adbang', 'verifikator_inspektorat', 'verifikator_pupr', 'pptk');
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
    if($val != 'pptk'){
        $options_role[] = "<option value='$val'>$val</option>";
    }
}
$api_key = get_option('_crb_api_key_extension');
?>

<h1 class="text-center" style="margin-top: 50px;">Manajemen User Verifikasi RKA (Rencana Kerja dan Anggaran)</h1>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_tambah_data" style="margin-left: 15px;">
    + Tambah Data
</button>

<!-- Modal -->
<div class="modal fade" id="modal_tambah_data" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog .modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Tambah Data User Verifikasi RKA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="text" class="form-control" id="password" required>
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" class="form-control" id="nama" required>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="text" class="form-control" id="email" required>
                </div>
                <div class="form-group" required>
                    <label>Role</label>
                    <select class="form-control" id="role">
                        <?php echo implode('', $options_role); ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submit_data(this)">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- table -->
<div style="padding: 15px;margin:0 0 3rem 0;">
    <table class="table table-bordered" id="daftar-user">
        <thead>
            <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Username</th>
                <th class="text-center">Nama</th>
                <th class="text-center">E-mail</th>
                <th class="text-center">Role</th>
                <th class="text-center">Aksi</th>
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
            </tr>
        </tbody>
    </table>
</div>

<script>
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
                        "data": 'id',
                        className: "text-center"
                    },
                    {
                        "data": 'user',
                        className: "text-center"
                    },
                    {
                        "data": 'nama',
                        className: "text-center"
                    },
                    {
                        "data": 'email',
                        className: "text-right"
                    },
                    {
                        "data": 'role',
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

    jQuery(document).ready(function(){
        load_data();
    });

    function submit_data(that) {
        jQuery('#wrap-loading').show();
        const username = jQuery('#username').val();
        const password = jQuery('#password').val();
        const nama = jQuery('#nama').val();
        const email = jQuery('#email').val();
        const role = jQuery('#role').val();

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
</script>