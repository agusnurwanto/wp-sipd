<?php

class Wpsipd_Public_RKA
{

    public function verifikasi_rka()
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-verifikasi-rka.php';
    }

    public function user_verikasi_rka()
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-user-verifikasi-rka.php';
    }

    public function user_pptk()
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-user-pptk.php';
    }

    function tambah_user_verifikator()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil tambah user!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                //validasi tidak boleh kosong
                if (empty($_POST['username'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Username tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['nama'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'nama tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['nomorwa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'nomorwa tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['email'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'email tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['role'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'role tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['fokus_uraian'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'fokus uraian tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['nama_bidang_skpd'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'nama bidang skpd tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $password = '';
                if (
                    empty($_POST['id_user'])
                    && empty($_POST['password'])
                ) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Password tidak boleh kosong!';
                    die(json_encode($ret));
                } else {
                    $password = $_POST['password'];
                }

                //
                $username = $_POST['username'];
                $nama = $_POST['nama'];
                $nomorwa = $_POST['nomorwa'];
                $email = $_POST['email'];
                $role = $_POST['role'];
                $fokus_uraian = $_POST['fokus_uraian'];
                $nama_bidang = $_POST['nama_bidang_skpd'];

                //validasi input
                if (strlen($username) < 5) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Username harus minimal 5 karakter.';
                    die(json_encode($ret));
                }
                if (!empty($password)) {
                    if (strlen($password) < 8) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Password harus minimal 8 karakter.';
                        die(json_encode($ret));
                    }
                    if (!preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z\d]/', $password)) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Password harus mengandung angka dan karakter unik.';
                        die(json_encode($ret));
                    }
                }
                if (!is_email($email)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Format email tidak valid.';
                    die(json_encode($ret));
                }
                if (!preg_match('/^\+62\d{9,15}$/', $nomorwa)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Nomor WhatsApp harus dimulai dengan +62, masukan 9 - 15 karakter!.';
                    die(json_encode($ret));
                }

                //
                if (!empty($_POST['id_user'])) {
                    $insert_user = $_POST['id_user'];
                    $current_user = get_userdata($insert_user);
                    if (empty($current_user)) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'User dengan id=' . $insert_user . ', tidak ditemukan!';
                        die(json_encode($ret));
                    }
                } else {
                    $insert_user = username_exists($username);
                }

                $option = array(
                    'user_login' => $username,
                    'user_pass' => $password,
                    'user_email' => $email,
                    'first_name' => $nama,
                    'display_name' => $nama,
                    'role' => $role
                );
                //proses tambah user
                if (!$insert_user) {
                    $insert_user = wp_insert_user($option);
                    update_user_meta($insert_user, 'nomor_wa', $nomorwa);
                    update_user_meta($insert_user, 'fokus_uraian', $fokus_uraian);
                    update_user_meta($insert_user, 'nama_bidang_skpd', $nama_bidang);

                    if (is_wp_error($insert_user)) {
                        $ret['status'] = 'error';
                        $ret['message'] = $insert_user->get_error_message();
                    } else {
                        $ret['status'] = 'success';
                        $ret['message'] = 'User berhasil ditambahkan.';
                    }
                } else {
                    if (
                        !empty($_POST['id_user'])
                        && ($current_user->user_login == $username
                            || !username_exists($username)
                        )
                    ) {
                        // update password jika password tidak kosong
                        if (
                            empty($password)
                        ) {
                            unset($option['user_pass']);
                        } else {
                            wp_set_password($password, $_POST['id_user']);
                        }

                        // update data meta user
                        $option['ID'] = $_POST['id_user'];
                        wp_update_user($option);

                        // update username jika namanya beda
                        $new_user_login = $username;
                        if ($current_user->user_login != $username) {
                            $wpdb->update(
                                $wpdb->users,
                                array('user_login' => $new_user_login),
                                array('ID' => $_POST['id_user'])
                            );
                        }

                        // update user meta
                        update_user_meta($_POST['id_user'], 'nomor_wa', $nomorwa);
                        update_user_meta($insert_user, 'fokus_uraian', $fokus_uraian);
                        update_user_meta($insert_user, 'nama_bidang_skpd', $nama_bidang);

                        $ret['message'] = 'Berhasil update data!';
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Username sudah ada!';
                    }
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'APIKEY tidak sesuai!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    function get_user_verifikator()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil get user!';
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $roles = $this->role_verifikator();
                $args = array(
                    'role__in' => $roles,
                    'orderby' => 'user_nicename',
                    'order'   => 'ASC'
                );

                $users = array();
                // get data user harus login sebagai admin
                if (in_array("administrator", $user_meta->roles)) {
                    if (!empty($params['search']['value'])) {
                        $search_value = sanitize_text_field($params['search']['value']);
                        $args['search'] = "*{$search_value}*";
                        $users = get_users($args);
                    } else {
                        $users = get_users($args);
                    }
                }

                $data_user = array();
                foreach ($users as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal->ID . '\'); return false;" href="#" style="margin-right: 10px;" title="Edit Data">
                    <i class="dashicons dashicons-edit"></i></a>';
                    if (in_array("administrator", $user_meta->roles)) {
                        $btn .= '<a class="btn btn-sm btn-danger" onclick="delete_data(\'' . $recVal->ID . '\'); return false;" href="#" title="Hapus Data">
                    <i class="dashicons dashicons-trash"></i></a>';
                    }
                    $data_user[$recKey]['aksi'] = $btn;
                    $data_user[$recKey]['user'] = $recVal->user_login;
                    $data_user[$recKey]['nama'] = $recVal->display_name;
                    $data_user[$recKey]['nomorwa'] = get_user_meta($recVal->ID, 'nomor_wa');
                    $data_user[$recKey]['nama_bidang_skpd'] = get_user_meta($recVal->ID, 'nama_bidang_skpd');
                    $data_user[$recKey]['fokus_uraian'] = get_user_meta($recVal->ID, 'fokus_uraian');
                    $data_user[$recKey]['role'] = implode(', ', $recVal->roles);
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval(count($data_user)),
                    "recordsFiltered" => intval(count($data_user)),
                    "data"            => $data_user
                );

                die(json_encode($json_data));
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'APIKEY tidak sesuai!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    function role_verifikator()
    {
        $daftar_user = $this->get_carbon_multiselect('crb_daftar_user_verifikator');
        $daftar_user_list = array();
        foreach ($daftar_user as $v) {
            $daftar_user_list[] = $v['value'];
        }
        return $daftar_user_list;
    }

    function get_user_verifikator_by_id()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil get user by id!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id user tidak boleh kosong!';
                    die(json_encode($ret));
                }
                $user = get_userdata($_POST['id']);
                if (!empty($user)) {
                    $roles = $this->role_verifikator();
                    $cek_role = false;
                    foreach ($roles as $role) {
                        if (in_array($role, $user->roles)) {
                            $cek_role = true;
                        }
                    }
                    if ($cek_role) {
                        $new_user = array();
                        $new_user['id_user'] = $user->ID;
                        $new_user['user_login'] = $user->data->user_login;
                        $new_user['display_name'] = $user->data->display_name;
                        $new_user['user_email'] = $user->data->user_email;
                        $new_user['nomorwa'] = get_user_meta($user->ID, 'nomor_wa');
                        $new_user['nama_bidang_skpd'] = get_user_meta($user->ID, 'nama_bidang_skpd');
                        $new_user['fokus_uraian'] = get_user_meta($user->ID, 'fokus_uraian');
                        $new_user['roles'] = $user->roles;
                        $ret['data'] = $new_user;
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Grup user verifikator tidak ditemukan!';
                    }
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'User tidak ditemukan!';
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'APIKEY tidak sesuai!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    function delete_user_verifikator()
    {
        global $wpdb;

        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

                $allowed_roles = $this->role_verifikator();

                $current_user = wp_get_current_user();
                if (!in_array('administrator', $current_user->roles)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya administrator yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }

                if (empty($_POST['id'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id user tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $current_user = get_userdata($_POST['id']);
                if (!empty($current_user)) {
                    $user_roles = $current_user->roles;
                    $is_allowed = false;
                    foreach ($user_roles as $role) {
                        if (in_array($role, $allowed_roles)) {
                            $is_allowed = true;
                            break;
                        }
                    }
                    if ($is_allowed) {
                        if ($current_user->ID) {
                            wp_delete_user($current_user->ID);
                        } else {
                            $ret['status']  = 'error';
                            $ret['message'] = 'User tidak ditemukan!';
                        }
                    } else {
                        $ret['status']  = 'error';
                        $ret['message'] = 'User ini tidak dapat dihapus!';
                    }
                } else {
                    $ret['status']  = 'error';
                    $ret['message'] = 'User tidak ditemukan!';
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }

    function get_data_verifikasi_rka()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil get data verifikasi!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['kode_sbl'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'kode sbl tidak boleh kosong!';
                    die(json_encode($ret));
                } else if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tahun anggaran tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $kode_sbl = $_POST['kode_sbl'];
                $tahun_anggaran = $_POST['tahun_anggaran'];

                $datas = $wpdb->get_results($wpdb->prepare("
                    SELECT 
                        *
                    FROM data_verifikasi_rka
                    WHERE kode_sbl = %s
                        AND tahun_anggaran = %d
                        AND active = 1
                    ORDER BY update_at DESC", $kode_sbl, $tahun_anggaran), ARRAY_A);
                $new_data = array();
                foreach ($datas as $data) {
                    if (empty($new_data[$data['fokus_uraian']])) {
                        $new_data[$data['fokus_uraian']] = array();
                    }
                    $new_data[$data['fokus_uraian']][] = $data;
                }
                $ret['html'] = '';
                foreach ($new_data as $key => $data) {
                    foreach ($data as $val) {
                        $current_user_id = get_current_user_id();
                        $ret['html'] .= '
                            <tr>
                                <th>' . $key . '</th>
                                <td>' . $val['catatan_verifikasi'] . '</td>
                                <td>' . $val['nama_verifikator'] . ' ' . $val['update_at'] . '</td>
                                <td>' . $val['tanggapan_opd'] . '</td>
                                <td class="aksi">';

                        if ($current_user_id == $val['id_user']) {
                            $ret['html'] .= '
                                <a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $val['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>
                                <a class="btn btn-sm btn-danger" onclick="delete_data(\'' . $val['id'] . '\'); return false;" href="#" title="delete data"><i class="dashicons dashicons-trash"></i></a>';
                        }
                    }
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'APIKEY tidak sesuai!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    function tambah_catatan_verifikator()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil tambah data catatan verifikator!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

                $current_user = wp_get_current_user();
                if ($current_user) {
                    $nama_user = $current_user->display_name;
                    $id_user = $current_user->ID;
                    $nama_bidang = get_user_meta($current_user->ID, 'nama_bidang_skpd', true);
                }

                if (empty($_POST['kode_sbl'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'kode_sbl catatan tidak boleh kosong';
                    die(json_encode($ret));
                }

                if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tahun anggaran tidak boleh kosong!';
                    die(json_encode($ret));
                }

                if (empty($_POST['catatan_verifikasi'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'catatan verifikasi field harus diisi!';
                    die(json_encode($ret));
                }

                $id_catatan = $_POST['id_catatan'];
                $kode_sbl = $_POST['kode_sbl'];
                $tahun_anggaran = $_POST['tahun_anggaran'];
                $fokus_uraian = $_POST['fokus_uraian'];
                $catatan_verifikasi = $_POST['catatan_verifikasi'];

                $data = array(
                    'tahun_anggaran' => $tahun_anggaran,
                    'kode_sbl' => $kode_sbl,
                    'id_user' => $id_user,
                    'nama_verifikator' => $nama_user,
                    'fokus_uraian' => $fokus_uraian,
                    'catatan_verifikasi' => $catatan_verifikasi,
                    'active' => 1,
                    'create_at' => current_time('mysql')
                );

                if (empty($id_catatan)) {
                    // Insert new data
                    $result = $wpdb->insert('data_verifikasi_rka', $data);
                } else {
                    // Update existing data and set update_at
                    $data['update_at'] = current_time('mysql');
                    $result = $wpdb->update('data_verifikasi_rka', $data, array('id' => $id_catatan));
                    $ret['message'] = 'Berhasil Update Catatan Verifikasi';
                }

                if ($result !== false) {
                    // Insert or Update data in second table
                    $data_validasi = array(
                        'id_user' => $id_user,
                        'kode_sbl' => $kode_sbl,
                        'nama_bidang' => $nama_bidang,
                        'tahun_anggaran' => $tahun_anggaran
                    );

                    $existing_validasi = $wpdb->get_row($wpdb->prepare(
                        "
                        SELECT * 
                        FROM data_validasi_verifikasi_rka 
                        WHERE kode_sbl = %s 
                          AND tahun_anggaran = %d
                          AND id_user = %d",
                        $kode_sbl,
                        $tahun_anggaran,
                        $id_user
                    ));

                    if ($existing_validasi) {
                        // Update existing data
                        $data_validasi['update_at'] = current_time('mysql');
                        $result_validasi = $wpdb->update('data_validasi_verifikasi_rka', $data_validasi, array('id' => $existing_validasi->id));
                        if ($result_validasi === false) {
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal memperbarui data validasi di database.';
                            die(json_encode($ret));
                        }
                    } else {
                        // Insert new data
                        $result_validasi = $wpdb->insert('data_validasi_verifikasi_rka', $data_validasi);
                        if ($result_validasi === false) {
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal menambahkan data validasi ke database.';
                            die(json_encode($ret));
                        }
                    }
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = empty($id_catatan) ? 'Gagal menambahkan data ke database.' : 'Gagal memperbarui data di database.';
                    die(json_encode($ret));
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak valid!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }


    function verifikasi_tanpa_catatan()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil verifikasi tanpa catatan!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['kode_sbl'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'kode_sbl catatan tidak boleh kosong';
                    die(json_encode($ret));
                }
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $roles = $this->role_verifikator();
                $cek_role = false;
                foreach ($roles as $role) {
                    if (in_array($role, $user_meta->roles)) {
                        $cek_role = true;
                    }
                }
                if (!$cek_role) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Anda tidak memiliki hak akses untuk melakukan ini!';
                    die(json_encode($ret));
                }

                $kode_sbl = $_POST['kode_sbl'];
                $tahun_anggaran = $_POST['tahun_anggaran'];
                $data = array(
                    'kode_sbl' => $kode_sbl,
                    'tahun_anggaran' => $tahun_anggaran,
                    'id_user' => $user_id,
                    'nama_bidang' => get_usermeta($user_id, 'nama_bidang_skpd'),
                    'update_at' => current_time('mysql')
                );
                $cek_data = $wpdb->get_var($wpdb->prepare("
                    SELECT
                        id
                    FROM data_validasi_verifikasi_rka
                    WHERE kode_sbl = %s
                        AND tahun_anggaran= %d
                ", $kode_sbl, $tahun_anggaran));
                if (empty($cek_data)) {
                    $wpdb->insert('data_validasi_verifikasi_rka', $data);
                } else {
                    $wpdb->update('data_validasi_verifikasi_rka', $data, array(
                        'id' => $cek_data
                    ));
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak valid!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }


    function get_catatan_verifikasi_by_id()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil get catatan verifikasi!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id catatan tidak boleh kosong';
                    die(json_encode($ret));
                }
                $ret['data'] = $wpdb->get_row($wpdb->prepare('
                    SELECT
                        *
                    FROM data_verifikasi_rka
                    WHERE id=%d
                     AND active=1
                ', $_POST['id']), ARRAY_A);
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak valid!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    function hapus_catatan_verifikasi()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil hapus catatan verifikasi!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id catatan tidak boleh kosong';
                    die(json_encode($ret));
                }
                $id = intval($_POST['id']);
                $data = $wpdb->get_row($wpdb->prepare("
                SELECT
                    id_user,
                    kode_sbl,
                    tahun_anggaran
                FROM data_verifikasi_rka
                WHERE id = %d", $id));

                if ($data) {
                    // Update tabel data_verifikasi_rka
                    $result = $wpdb->update(
                        'data_verifikasi_rka',
                        array('active' => 0),
                        array('id' => $id)
                    );

                    if ($result !== false) {
                        // Update tabel data_validasi_verifikasi_rka
                        $result_validasi = $wpdb->update(
                            'data_validasi_verifikasi_rka',
                            array('update_at' => current_time('mysql')),
                            array(
                                'id_user' => $data->id_user,
                                'kode_sbl' => $data->kode_sbl,
                                'tahun_anggaran' => $data->tahun_anggaran
                            )
                        );

                        if ($result_validasi === false) {
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal hapus data di database.';
                        }
                    }
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak valid!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    function tambah_user_pptk()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil tambah user!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                //validasi tidak boleh kosong
                if (empty($_POST['username'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Username tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['nama'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'nama tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['nomorwa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'nomorwa tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['email'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'email tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['roles'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'role tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['skpd'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'pilihan SKPD tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $password = '';
                if (
                    empty($_POST['id_user'])
                    && empty($_POST['password'])
                ) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Password tidak boleh kosong!';
                    die(json_encode($ret));
                } else {
                    $password = $_POST['password'];
                }

                //
                $username = $_POST['username'];
                $nama = $_POST['nama'];
                $nomorwa = $_POST['nomorwa'];
                $email = $_POST['email'];
                $role = $_POST['roles'];
                $skpd = $_POST['skpd'];

                //validasi input
                if (strlen($username) < 5) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Username harus minimal 5 karakter.';
                    die(json_encode($ret));
                }
                if (!empty($password)) {
                    if (strlen($password) < 8) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Password harus minimal 8 karakter.';
                        die(json_encode($ret));
                    }
                    if (!preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z\d]/', $password)) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Password harus mengandung angka dan karakter unik.';
                        die(json_encode($ret));
                    }
                }
                if (!is_email($email)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Format email tidak valid.';
                    die(json_encode($ret));
                }
                if (!preg_match('/^\+62\d{9,15}$/', $nomorwa)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Nomor WhatsApp harus dimulai dengan +62, masukan 9 - 15 karakter!.';
                    die(json_encode($ret));
                }

                //
                if (!empty($_POST['id_user'])) {
                    $insert_user = $_POST['id_user'];
                    $current_user = get_userdata($insert_user);
                    if (empty($current_user)) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'User dengan id=' . $insert_user . ', tidak ditemukan!';
                        die(json_encode($ret));
                    }
                } else {
                    $insert_user = username_exists($username);
                }

                $option = array(
                    'user_login' => $username,
                    'user_pass' => $password,
                    'user_email' => $email,
                    'first_name' => $nama,
                    'display_name' => $nama,
                    'role' => $role
                );
                //proses tambah user
                if (!$insert_user) {
                    $insert_user = wp_insert_user($option);
                    update_user_meta($insert_user, 'nomor_wa', $nomorwa);
                    update_user_meta($insert_user, 'skpd', $skpd);

                    if (is_wp_error($insert_user)) {
                        $ret['status'] = 'error';
                        $ret['message'] = $insert_user->get_error_message();
                    } else {
                        $ret['status'] = 'success';
                        $ret['message'] = 'User berhasil ditambahkan.';
                    }
                } else {
                    if (
                        !empty($_POST['id_user'])
                        && ($current_user->user_login == $username
                            || !username_exists($username)
                        )
                    ) {
                        // update password jika password tidak kosong
                        if (
                            empty($password)
                        ) {
                            unset($option['user_pass']);
                        } else {
                            wp_set_password($password, $_POST['id_user']);
                        }

                        // update data meta user
                        $option['ID'] = $_POST['id_user'];
                        wp_update_user($option);

                        // update username jika namanya beda
                        $new_user_login = $username;
                        if ($current_user->user_login != $username) {
                            $wpdb->update(
                                $wpdb->users,
                                array('user_login' => $new_user_login),
                                array('ID' => $_POST['id_user'])
                            );
                        }

                        // update user meta
                        update_user_meta($_POST['id_user'], 'nomor_wa', $nomorwa);
                        update_user_meta($insert_user, 'skpd', $skpd);


                        $ret['message'] = 'Berhasil update data!';
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Username sudah ada!';
                    }
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'APIKEY tidak sesuai!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    function get_user_pptk()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil get user!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $roles = array('pptk');
                $args = array(
                    'role__in' => $roles,
                    'orderby' => 'user_nicename',
                    'order'   => 'ASC'
                );

                $users = array();
                // get data user harus login sebagai admin
                if (in_array("administrator", $user_meta->roles)) {
                    if (!empty($params['search']['value'])) {
                        $search_value = sanitize_text_field($params['search']['value']);
                        $args['search'] = "*{$search_value}*";
                        $users = get_users($args);
                    } else {
                        $users = get_users($args);
                    }
                }

                $data_user = array();

                $data_skpd = array();
                $idtahun = $wpdb->get_results("select distinct tahun_anggaran from data_unit", ARRAY_A);
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
                    foreach ($skpd as $v) {
                        if (empty($data_skpd[$val['tahun_anggaran']])) {
                            $data_skpd[$val['tahun_anggaran']] = array();
                        }
                        $data_skpd[$val['tahun_anggaran']][$v['id_skpd']] = $v['kode_skpd'] . ' ' . $v['nama_skpd'];
                    }
                }
                foreach ($users as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal->ID . '\'); return false;" href="#" style="margin-right: 10px;" title="Edit Data">
                    <i class="dashicons dashicons-edit"></i></a>';
                    if (in_array("administrator", $user_meta->roles)) {
                        $btn .= '<a class="btn btn-sm btn-danger" onclick="delete_data(\'' . $recVal->ID . '\'); return false;" href="#" title="Hapus Data">
                    <i class="dashicons dashicons-trash"></i></a>';
                    }
                    $data_user[$recKey]['aksi'] = $btn;
                    $data_user[$recKey]['user'] = $recVal->user_login;
                    $data_user[$recKey]['nama'] = $recVal->display_name;
                    $data_user[$recKey]['nomorwa'] = get_user_meta($recVal->ID, 'nomor_wa');
                    $skpd_html = array();
                    $skpd = get_user_meta($recVal->ID, 'skpd');
                    foreach ($skpd[0] as $tahun => $id_skpd) {
                        $skpd_html[] = $data_skpd[$tahun][$id_skpd] . ' ( ' . $tahun . ' )';
                    }
                    $data_user[$recKey]['skpd'] = implode('<br>', $skpd_html);
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval(count($data_user)),
                    "recordsFiltered" => intval(count($data_user)),
                    "data"            => $data_user
                );

                die(json_encode($json_data));
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'APIKEY tidak sesuai!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    function delete_user_pptk()
    {
        global $wpdb;

        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

                $allowed_roles = array('pptk');

                $current_user = wp_get_current_user();
                if (!in_array('administrator', $current_user->roles)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya administrator yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }

                if (empty($_POST['id'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id user tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $current_user = get_userdata($_POST['id']);
                if (!empty($current_user)) {
                    $user_roles = $current_user->roles;
                    $is_allowed = false;
                    foreach ($user_roles as $role) {
                        if (in_array($role, $allowed_roles)) {
                            $is_allowed = true;
                            break;
                        }
                    }
                    if ($is_allowed) {
                        if ($current_user->ID) {
                            wp_delete_user($current_user->ID);
                        } else {
                            $ret['status']  = 'error';
                            $ret['message'] = 'User tidak ditemukan!';
                        }
                    } else {
                        $ret['status']  = 'error';
                        $ret['message'] = 'User ini tidak dapat dihapus!';
                    }
                } else {
                    $ret['status']  = 'error';
                    $ret['message'] = 'User tidak ditemukan!';
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }

    function get_user_pptk_by_id()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil get user by id!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id user tidak boleh kosong!';
                    die(json_encode($ret));
                }
                $user = get_userdata($_POST['id']);
                if (!empty($user)) {
                    $roles = array('pptk');
                    $cek_role = false;
                    foreach ($roles as $role) {
                        if (in_array($role, $user->roles)) {
                            $cek_role = true;
                        }
                    }
                    if ($cek_role) {
                        $new_user = array();
                        $new_user['id_user'] = $user->ID;
                        $new_user['user_login'] = $user->data->user_login;
                        $new_user['display_name'] = $user->data->display_name;
                        $new_user['user_email'] = $user->data->user_email;
                        $new_user['nomorwa'] = get_user_meta($user->ID, 'nomor_wa');
                        $new_user['skpd'] = get_user_meta($user->ID, 'skpd');
                        $new_user['roles'] = $user->roles;
                        $ret['data'] = $new_user;
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Grup user verifikator tidak ditemukan!';
                    }
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'User tidak ditemukan!';
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'APIKEY tidak sesuai!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    function get_sub_keg_pptk()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil get user PPTK!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id_skpd'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id skpd tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tahun anggaran tidak boleh kosong!';
                    die(json_encode($ret));
                }
                // cek role user existing harus administrator atau PA, PLT, KPA
                $current_user = wp_get_current_user();
                $allowed_roles = array('administrator', 'PA', 'KPA', 'PLT');

                // Periksa apakah ada perpotongan antara peran yang diizinkan dan peran pengguna saat ini.
                if (empty(array_intersect($allowed_roles, $current_user->roles))) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }
                $sub_keg = $wpdb->get_row($wpdb->prepare("
                SELECT
                    p.*,
                    s.nama_sub_giat,
                    s.kode_urusan
                FROM data_pptk_sub_keg p
                RIGHT JOIN data_sub_keg_bl s on p.kode_sbl = s.kode_sbl
                    and p.tahun_anggaran = s.tahun_anggaran
                    and s.active=1
                WHERE s.tahun_anggaran=%d
                    and s.kode_sbl=%s
                ", $_POST['tahun_anggaran'], $_POST['kode_sbl']), ARRAY_A);
                if ($sub_keg && isset($sub_keg['kode_urusan'])) {
                    $sub_keg['nama_sub_giat'] = str_replace("X.XX", $sub_keg['kode_urusan'], $sub_keg['nama_sub_giat']);
                }
                $ret['sub_keg'] = $sub_keg;

                $data_skpd = array();
                $data_skpd[$_POST['tahun_anggaran']] = $_POST['id_skpd'];
                $args = array(
                    'role'    => 'pptk',
                    'orderby' => 'user_nicename',
                    'order'   => 'ASC',
                    'meta_query' => array(
                        array(
                            'key' => 'skpd',
                            'value' => 'i:'.$_POST['tahun_anggaran'].';s:4:"'.$_POST['id_skpd'].'"',
                            'compare' => 'LIKE'
                        )
                    )
                );
                $users = get_users($args);
                $ret['sql'] = $wpdb->last_query;
                $user_pptk_opt = '<option value="">Pilih User</option>';
                foreach ($users as $user) {
                    $selected = '';
                    if ($user->ID == $sub_keg['id_user']) {
                        $selected = 'selected';
                    }
                    $user_pptk_opt .= '<option value="' . esc_attr($user->ID) . '" ' . $selected . '>' . esc_html($user->display_name) . '</option>';
                }
                $ret['user_pptk_html'] = $user_pptk_opt;
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'APIKEY tidak sesuai!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    function simpan_sub_keg_pptk()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil simpan user PPTK!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['kode_sbl'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'kode_sbl tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['id_user'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id_user tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tahun anggaran tidak boleh kosong!';
                    die(json_encode($ret));
                }
                // cek role user existing harus administrator atau PA, PLT, KPA
                $current_user = wp_get_current_user();
                $allowed_roles = array('administrator', 'PA', 'KPA', 'PLT');
                // Periksa apakah ada perpotongan antara peran yang diizinkan dan peran pengguna saat ini.
                if (empty(array_intersect($allowed_roles, $current_user->roles))) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }
                $kode_sbl = $_POST['kode_sbl'];
                $id_user = $_POST['id_user'];
                $tahun_anggaran = $_POST['tahun_anggaran'];
                
                get_userdata($id_user);

                $data = array(
                    'kode_sbl' => $kode_sbl,
                    'id_user' => $id_user,
                    'tahun_anggaran' => $tahun_anggaran,
                    'active' => 1,
                    'update_at' => current_time('mysql')
                );

                $cek_pptk = $wpdb->get_var($wpdb->prepare("
                    SELECT
                        id
                    FROM data_pptk_sub_keg
                    WHERE active=1
                        and tahun_anggaran=%d
                        and kode_sbl=%s
                ", $tahun_anggaran, $kode_sbl));
                
                if (empty($cek_pptk)) {
                    $result = $wpdb->insert('data_pptk_sub_keg', $data);
                } else {
                    $wpdb->update('data_pptk_sub_keg', $data, array('id' => $cek_pptk));
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'APIKEY tidak sesuai!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }
}
