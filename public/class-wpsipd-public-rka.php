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
                        $ret['html'] .= '
                            <tr>
                            <th>' . $key . '</th>
                                <td>' . $val['catatan_verifikasi'] . '</td>
                                <td>' . $val['nama_verifikator'] . ' ' . $val['update_at'] . '</td>
                                <td>' . $val['tanggapan_opd'] . '</td>
                                <td class="aksi">
                                    <a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $val['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>
                                    <a class="btn btn-sm btn-danger" onclick="delete_data(\'' . $val['id'] . '\'); return false;" href="#" title="delete data"><i class="dashicons dashicons-trash"></i></a>
                                </td>
                            </tr>';
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
                if (empty($_POST['kode_sbl'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'kode sbl tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['id_user'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id user tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tahun anggaran tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['nama_verifikator'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'nama verifikator field harus diisi!';
                    die(json_encode($ret));
                }
                if (empty($_POST['fokus_uraian'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'fokus uraian field harus diisi!';
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
                $id_user = $_POST['id_user'];
                $nama_verifikator = $_POST['nama_verifikator'];
                $fokus_uraian = $_POST['fokus_uraian'];
                $catatan_verifikasi = $_POST['catatan_verifikasi'];

                $data = [
                    'tahun_anggaran' => $tahun_anggaran,
                    'kode_sbl' => $kode_sbl,
                    'id_user' => $id_user,
                    'nama_verifikator' => $nama_verifikator,
                    'fokus_uraian' => $fokus_uraian,
                    'catatan_verifikasi' => $catatan_verifikasi,
                    'create_at' => current_time('mysql')
                ];

                if (empty($id_catatan)) {
                    // Insert new data
                    $result = $wpdb->insert('data_verifikasi_rka', $data);
                } else {
                    // Update existing data and set update_at
                    $data['update_at'] = current_time('mysql');
                    $result = $wpdb->update('data_verifikasi_rka', $data, ['id' => $id_catatan]);
                    $ret['message'] = 'Berhasil Update Catatan Verifikasi';
                }

                if ($result === false) {
                    $ret['status'] = 'error';
                    $ret['message'] = empty($id_catatan) ? 'Gagal menambahkan data ke database.' : 'Gagal memperbarui data di database.';
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
                $ret['data'] = $wpdb->delete('data_verifikasi_rka', array(
                    'id' => $_POST['id']
                ));
                $ret['status']  = 'success';
                $ret['message'] = 'catatan verifikasi berhasil dihapus!';
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
                        if(empty($data_skpd[$val['tahun_anggaran']])){
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
                    foreach($skpd[0] as $tahun => $id_skpd){
                        $skpd_html[] = $data_skpd[$tahun][$id_skpd].' ( '.$tahun.' )';
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
}
