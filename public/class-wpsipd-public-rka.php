<?php

class Wpsipd_Public_RKA
{

    public function input_rka_pendapatan_sipd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-input-rka-pendapatan-sipd.php';
    }

    public function input_rka_pembiayaan_sipd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-input-rka-pembiayaan-sipd.php';
    }

    public function input_rka_sipd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-input-rka-sipd.php';
    }

    public function serapan_rka_sipd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-serapan-rka-sipd.php';
    }

    public function verifikasi_rka()
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-verifikasi-rka.php';
    }

    public function verifikasi_rka_lokal()
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }

        $tipe_rka = 'rka_lokal';

        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-verifikasi-rka.php';
    }

    public function rekap_longlist_per_jenis_belanja($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-longlist-per-jenis-belanja.php';
    }

    public function apbd_perda_lampiran_1($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda.php';
    }

    public function apbd_perda_lampiran_2($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-2.php';
    }

    public function apbd_perda_lampiran_3($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-3.php';
    }

    public function apbd_perda_lampiran_4($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-4.php';
    }

    public function apbd_perda_lampiran_5($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-5.php';
    }

    public function apbd_perda_lampiran_7($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-7.php';
    }

    public function apbd_perda_lampiran_8($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-8.php';
    }

    public function rekap_longlist_per_jenis_belanja_all_skpd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-longlist-per-jenis-belanja-all-skpd.php';
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

    public function nota_pencairan_dana_panjar()
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-nota-pencairan-dana-panjar.php';
    }

    public function daftar_nota_pencairan_dana_panjar()
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-daftar-nota-pencairan-dana-panjar.php';
    }

    public function laporan_panjar_npd($atts)
    {
        // untuk disable render shortcode di halaman edit page/post
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-laporan-panjar-npd.php';
    }

    public function print_laporan_buku_kas_umum_pembantu($atts)
    {
        // untuk disable render shortcode di halaman edit page/post
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-print-laporan-buku-kas-umum-pembantu.php';
    }

    public function print_laporan_detail_kegiatan($atts)
    {
        // untuk disable render shortcode di halaman edit page/post
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-print-laporan-detail-kegiatan.php';
    }

    public function daftar_buku_kas_umum_pembantu($atts)
    {
        // untuk disable render shortcode di halaman edit page/post
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-laporan-buku-kas-umum.php';
    }

    public function cetak_kwitansi_bku($atts)
    {
        // untuk disable render shortcode di halaman edit page/post
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-cetak-kwitansi-bku.php';
    }

    public function cetak_spt_sppd($atts)
    {
        // untuk disable render shortcode di halaman edit page/post
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/sppd/wpsipd-public-cetak-spt-sppd.php';
    }

    public function cetak_sppd($atts)
    {
        // untuk disable render shortcode di halaman edit page/post
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/sppd/wpsipd-public-cetak-sppd.php';
    }

    public function cetak_sppd_belakang($atts)
    {
        // untuk disable render shortcode di halaman edit page/post
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/sppd/wpsipd-public-cetak-sppd-belakang.php';
    }

    public function cetak_sppd_rampung($atts)
    {
        // untuk disable render shortcode di halaman edit page/post
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/sppd/wpsipd-public-cetak-sppd-rampung.php';
    }

    public function spt_sppd($atts)
    {
        // untuk disable render shortcode di halaman edit page/post
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/sppd/wpsipd-public-spt-sppd.php';
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
                } else if (empty($_POST['tipe_rka'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tipe RKA tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $kode_sbl = $_POST['kode_sbl'];
                $tahun_anggaran = $_POST['tahun_anggaran'];
                $tipe_rka = $_POST['tipe_rka'];
                $jadwal_terpilih = (!empty($_POST['jadwal_terpilih'])) ? $_POST['jadwal_terpilih'] : 0;

                if ($tipe_rka == 'rka_lokal') {
                    $prefix = '_lokal';
                    $where_rka = '';
                } else {
                    $prefix = '';
                    $where_rka = '_sipd';
                }

                $data_jadwal_terpilih = array();
                if ($jadwal_terpilih != 0) {
                    $data_jadwal_terpilih = $wpdb->get_row(
                        "SELECT *
                        FROM data_jadwal_lokal
                        WHERE id_jadwal_lokal=" . $jadwal_terpilih . "
                        AND tahun_anggaran=" . $tahun_anggaran,
                        ARRAY_A
                    );
                }

                $prefix_history = '';
                $where_history = '';
                if (!empty($data_jadwal_terpilih)) {
                    if ($data_jadwal_terpilih['status'] == 0) {
                        $cek_jadwal = $this->validasi_jadwal_perencanaan('verifikasi_rka' . $where_rka, $tahun_anggaran);
                        $jadwal_lokal = $cek_jadwal['data'];
                    } else {
                        $jadwal_lokal = $data_jadwal_terpilih;
                        $prefix_history = '_history';
                        $where_history = ' AND id_jadwal=' . $data_jadwal_terpilih['id_jadwal_lokal'];
                    }
                } else {
                    $cek_jadwal = $this->validasi_jadwal_perencanaan('verifikasi_rka' . $where_rka, $tahun_anggaran);
                    $jadwal_lokal = $cek_jadwal['data'];
                }
                $dateTime = new DateTime();
                $time_now = $dateTime->format('Y-m-d H:i:s');

                $datas = $wpdb->get_results($wpdb->prepare("
                    SELECT 
                        *
                    FROM data_verifikasi_rka" . $prefix . "" . $prefix_history . "
                    WHERE kode_sbl = %s
                        AND tahun_anggaran = %d
                        AND active = 1
                        " . $where_history . "
                    ORDER BY update_at DESC", $kode_sbl, $tahun_anggaran), ARRAY_A);
                $new_data = array();
                foreach ($datas as $data) {
                    if (empty($new_data[$data['fokus_uraian']])) {
                        $new_data[$data['fokus_uraian']] = array();
                    }
                    $new_data[$data['fokus_uraian']][] = $data;
                }
                $id_skpd_rka = explode('.', $kode_sbl);
                $id_skpd_rka = $id_skpd_rka[1];

                $current_user = wp_get_current_user();
                $btn_tanggapan = false;
                if (
                    in_array('PA', $current_user->roles)
                    || in_array('KPA', $current_user->roles)
                    || in_array('PLT', $current_user->roles)
                ) {
                    $nipkepala = get_user_meta($current_user->ID, '_nip');
                    $skpd_db = $wpdb->get_results($wpdb->prepare("
                        SELECT 
                            nama_skpd, 
                            id_skpd, 
                            kode_skpd,
                            is_skpd
                        from data_unit 
                        where nipkepala=%s 
                            and tahun_anggaran=%d
                        group by id_skpd", $nipkepala[0], $tahun_anggaran), ARRAY_A);
                    foreach ($skpd_db as $skpd) {
                        if ($skpd['id_skpd'] == $id_skpd_rka) {
                            $btn_tanggapan = true;
                        }
                    }
                } else if (in_array('pptk', $current_user->roles)) {
                    $pptk_sub_keg = $wpdb->get_row($wpdb->prepare("
                        SELECT
                            p.*
                        FROM data_pptk_sub_keg" . $prefix . "" . $prefix_history . " p
                        WHERE active=1
                            and tahun_anggaran=%d
                            and kode_sbl=%s
                    ", $tahun_anggaran, $kode_sbl), ARRAY_A);
                    if ($pptk_sub_keg['id_user'] == $current_user->ID) {
                        $btn_tanggapan = true;
                    }
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
                                <td class="aksi">';

                        if (!empty($jadwal_lokal)) {
                            if ($time_now > $jadwal_lokal[0]['waktu_awal'] && $time_now <  $jadwal_lokal[0]['waktu_akhir']) {
                                // tampilkan tombol verifikator edit dan hapus
                                if ($current_user->ID == $val['id_user']) {
                                    $ret['html'] .= '
                                        <a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $val['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                                    $ret['html'] .= '
                                        <a class="btn btn-sm btn-danger" onclick="delete_data(\'' . $val['id'] . '\'); return false;" href="#" title="delete data"><i class="dashicons dashicons-trash"></i></a>';
                                }

                                // tampilkan tombol tanggapan untuk PA dan PPTK
                                if ($btn_tanggapan) {
                                    $ret['html'] .= '<a class="btn btn-sm btn-warning" onclick="tambah_tanggapan(\'' . $val['id'] . '\'); return false;" href="#" title="Tanggapi"><i class="dashicons dashicons-editor-quote"></i></a>';
                                }
                            }
                        }

                        $ret['html'] .= '</td></tr>';
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

                if (empty($_POST['tipe_rka'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tipe rka tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $id_catatan = $_POST['id_catatan'];
                $kode_sbl = $_POST['kode_sbl'];
                $tahun_anggaran = $_POST['tahun_anggaran'];
                $fokus_uraian = $_POST['fokus_uraian'];
                $catatan_verifikasi = $_POST['catatan_verifikasi'];
                $tipe_rka = $_POST['tipe_rka'];

                if ($tipe_rka == 'rka_lokal') {
                    $prefix = '_lokal';
                } else {
                    $prefix = '';
                }

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

                $tabel_verif = 'data_verifikasi_rka' . $prefix;
                if (empty($id_catatan)) {
                    // Insert new data
                    $result = $wpdb->insert($tabel_verif, $data);
                } else {
                    // Update existing data and set update_at
                    $data['update_at'] = current_time('mysql');
                    $result = $wpdb->update($tabel_verif, $data, array('id' => $id_catatan));
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

                    $tabel_validasi = 'data_validasi_verifikasi_rka' . $prefix;

                    $existing_validasi = $wpdb->get_row($wpdb->prepare(
                        "
                        SELECT * 
                        FROM " . $tabel_validasi . " 
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
                        $result_validasi = $wpdb->update($tabel_validasi, $data_validasi, array('id' => $existing_validasi->id));
                        if ($result_validasi === false) {
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal memperbarui data validasi di database.';
                            die(json_encode($ret));
                        }
                    } else {
                        // Insert new data
                        $result_validasi = $wpdb->insert($tabel_validasi, $data_validasi);
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

    function tambah_data_tanggapan()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil simpan tanggapan PPTK!';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

                if (empty($_POST['id_catatan'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id catatan tidak boleh kosong';
                    die(json_encode($ret));
                }
                // Validasi user_pptk
                $user_pptk_status = $_POST['user_pptk_status'] ?? ''; // Gunakan null coalescing operator untuk menghindari undefined index
                if (empty($user_pptk_status) || $user_pptk_status == 'User PPTK belum disetting!') {
                    $ret['status'] = 'error';
                    $ret['message'] = 'PPTK belum di set! Harap diset terlebih dahulu!';
                    die(json_encode($ret));
                }
                if (empty($_POST['tanggapan_verifikasi'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tanggapan verifikasi harus diisi!';
                    die(json_encode($ret));
                }
                if (empty($_POST['tipe_rka'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tipe rka harus diisi!';
                    die(json_encode($ret));
                }

                $tipe_rka = $_POST['tipe_rka'];

                if ($tipe_rka == 'rka_lokal') {
                    $prefix = '_lokal';
                } else {
                    $prefix = '';
                }
                $current_user = wp_get_current_user();
                $allowed_roles = array('pptk', 'PA', 'KPA', 'PLT');
                // Periksa apakah ada perpotongan antara peran yang diizinkan dan peran pengguna saat ini.
                if (empty(array_intersect($allowed_roles, $current_user->roles))) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }
                $id_catatan = $_POST['id_catatan'];
                $tanggapan_verifikasi = $_POST['tanggapan_verifikasi'];

                // Cek apakah id_catatan ada di database
                $id_tanggapan = $wpdb->get_var($wpdb->prepare("
                    SELECT 
                        id
                    FROM data_verifikasi_rka" . $prefix . "
                    WHERE id = %d 
                    and active = 1", $id_catatan));

                if ($id_tanggapan) {
                    // Update tabel data_verifikasi_rka
                    $result = $wpdb->update(
                        'data_verifikasi_rka' . $prefix,
                        array(
                            'tanggapan_opd' => $tanggapan_verifikasi,
                            'update_at_tanggapan' => current_time('mysql')
                        ),
                        array('id' => $id_catatan) // WHERE condition
                    );

                    if ($result == false) {
                        $ret['status']  = 'error';
                        $ret['message'] = 'gagal menanggapi catatan verifikasi!';
                        die(json_encode($ret));
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
                if (empty($_POST['tipe_rka'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tipe rka tidak boleh kosong';
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
                $tipe_rka = $_POST['tipe_rka'];

                if ($tipe_rka == 'rka_lokal') {
                    $prefix = '_lokal';
                } else {
                    $prefix = '';
                }

                $data = array(
                    'kode_sbl' => $kode_sbl,
                    'tahun_anggaran' => $tahun_anggaran,
                    'id_user' => $user_id,
                    'nama_bidang' => get_usermeta($user_id, 'nama_bidang_skpd'),
                    'update_at' => current_time('mysql')
                );

                $tabel = 'data_validasi_verifikasi_rka' . $prefix;
                $cek_data = $wpdb->get_var($wpdb->prepare("
                    SELECT
                        id
                    FROM " . $tabel . "
                    WHERE kode_sbl = %s
                        AND tahun_anggaran= %d
                ", $kode_sbl, $tahun_anggaran));
                if (empty($cek_data)) {
                    $wpdb->insert($tabel, $data);
                } else {
                    $wpdb->update($tabel, $data, array(
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
                if (empty($_POST['tipe_rka'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tipe rka tidak boleh kosong';
                    die(json_encode($ret));
                }

                $tipe_rka = $_POST['tipe_rka'];

                if ($tipe_rka == 'rka_lokal') {
                    $prefix = '_lokal';
                } else {
                    $prefix = '';
                }

                $ret['data'] = $wpdb->get_row($wpdb->prepare('
                    SELECT
                        *
                    FROM data_verifikasi_rka' . $prefix . '
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
                if (empty($_POST['tipe_rka'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tipe rka tidak boleh kosong';
                    die(json_encode($ret));
                }

                $tipe_rka = $_POST['tipe_rka'];

                if ($tipe_rka == 'rka_lokal') {
                    $prefix = '_lokal';
                } else {
                    $prefix = '';
                }

                $id = intval($_POST['id']);
                $data = $wpdb->get_row($wpdb->prepare("
                    SELECT
                        id_user,
                        kode_sbl,
                        tahun_anggaran
                    FROM data_verifikasi_rka" . $prefix . "
                    WHERE id = %d", $id));

                if ($data) {
                    // Update tabel data_verifikasi_rka
                    $result = $wpdb->update(
                        'data_verifikasi_rka' . $prefix,
                        array('active' => 0),
                        array('id' => $id)
                    );

                    if ($result !== false) {
                        // Update tabel data_validasi_verifikasi_rka
                        $result_validasi = $wpdb->update(
                            'data_validasi_verifikasi_rka' . $prefix,
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
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Gagal menemukan data di database.';
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

                        $user_id = um_user('ID');
                        $user_meta = get_userdata($user_id);

                        // cek jika user SKPD, maka skpd existing lain tetap disimpan ulang
                        if (!in_array("administrator", $user_meta->roles)) {

                            // get data id skpd per tahun anggaran sesuai dengan user yang login
                            $nipkepala = get_user_meta($user_id, '_nip');
                            $data_skpd = array();
                            $idtahun = $wpdb->get_results("select distinct tahun_anggaran from data_unit order by tahun_anggaran DESC", ARRAY_A);
                            foreach ($idtahun as $val) {
                                $s = $wpdb->get_results($wpdb->prepare("
                                    SELECT 
                                        nama_skpd, 
                                        id_skpd, 
                                        kode_skpd,
                                        nipkepala,
                                        is_skpd
                                    FROM data_unit 
                                    WHERE active=1
                                        AND nipkepala=%s
                                        AND tahun_anggaran=%d
                                    group by id_skpd
                                ", $nipkepala[0], $val['tahun_anggaran']), ARRAY_A);
                                foreach ($s as $v) {
                                    if (empty($data_skpd[$val['tahun_anggaran']])) {
                                        $data_skpd[$val['tahun_anggaran']] = array();
                                    }
                                    $data_skpd[$val['tahun_anggaran']][$v['id_skpd']] = $v['kode_skpd'] . ' ' . $v['nama_skpd'];
                                }
                            }

                            // pengecekan data skpd user login dengan skpd existing user pptk
                            $skpd_pptk_existing = get_user_meta($insert_user, 'skpd');
                            foreach ($skpd_pptk_existing as $s) {
                                foreach ($s as $tahun => $id_skpd) {
                                    if (empty($data_skpd[$tahun][$id_skpd])) {
                                        $skpd[$tahun] = $id_skpd;
                                    }
                                }
                            }
                        }
                        ksort($skpd);
                        // update data skpd setelah variable $skpd disesuaikan
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
                if (!empty($params['search']['value'])) {
                    $args['search'] = $params['search']['value'];
                }

                $idtahun = $wpdb->get_results("select distinct tahun_anggaran from data_unit", ARRAY_A);
                $users = array();
                $data_skpd = array();
                $skpd_user = array();
                // get data user harus login sebagai admin
                if (in_array("administrator", $user_meta->roles)) {
                    $users = get_users($args);

                    foreach ($idtahun as $val) {
                        $skpd = $wpdb->get_results($wpdb->prepare("
                            SELECT 
                                nama_skpd, 
                                id_skpd, 
                                kode_skpd,
                                is_skpd
                            FROM data_unit 
                            WHERE active=1
                                AND tahun_anggaran=%d
                            group by id_skpd", $val['tahun_anggaran']), ARRAY_A);
                        foreach ($skpd as $v) {
                            if (empty($data_skpd[$val['tahun_anggaran']])) {
                                $data_skpd[$val['tahun_anggaran']] = array();
                            }
                            $data_skpd[$val['tahun_anggaran']][$v['id_skpd']] = $v['kode_skpd'] . ' ' . $v['nama_skpd'];
                        }
                    }
                } else if (
                    in_array("PA", $user_meta->roles)
                    || in_array("KPA", $user_meta->roles)
                    || in_array("PLT", $user_meta->roles)
                ) {
                    $args['meta_query'] = array();
                    $args['meta_query']['relation'] = 'OR';
                    $nipkepala = get_user_meta($user_id, '_nip');
                    foreach ($idtahun as $val) {
                        $skpd = $wpdb->get_results($wpdb->prepare("
                            SELECT 
                                nama_skpd, 
                                id_skpd, 
                                kode_skpd,
                                nipkepala,
                                is_skpd
                            FROM data_unit 
                            WHERE active=1
                                AND tahun_anggaran=%d
                            group by id_skpd
                        ", $val['tahun_anggaran']), ARRAY_A);
                        foreach ($skpd as $v) {
                            if (empty($data_skpd[$val['tahun_anggaran']])) {
                                $data_skpd[$val['tahun_anggaran']] = array();
                            }
                            $data_skpd[$val['tahun_anggaran']][$v['id_skpd']] = $v['kode_skpd'] . ' ' . $v['nama_skpd'];
                            if ($v['nipkepala'] == $nipkepala[0]) {
                                $args['meta_query'][] = array(
                                    'key' => 'skpd',
                                    'value' => 'i:' . $val['tahun_anggaran'] . ';s:4:"' . $v['id_skpd'] . '"',
                                    'compare' => 'LIKE'
                                );
                                $skpd_user[$v['id_skpd']] = $v['kode_skpd'] . ' ' . $v['nama_skpd'];
                            }
                        }
                    }
                    $args['meta_query'][] = array(
                        'key' => 'skpd',
                        'value' => 'i:' . date('Y') . ';s:4:',
                        'compare' => 'NOT LIKE'
                    );
                    $users = get_users($args);
                }
                $sql_user = $wpdb->last_query . ' | ' . json_encode($args['meta_query']);
                $data_user = array();
                foreach ($users as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal->ID . '\'); return false;" href="#" style="margin-right: 10px;" title="Edit Data">
                    <i class="dashicons dashicons-edit"></i></a>';

                    $skpd_html = array();
                    $skpd = get_user_meta($recVal->ID, 'skpd');
                    foreach ($skpd[0] as $tahun => $id_skpd) {
                        $skpd_html[] = $data_skpd[$tahun][$id_skpd] . ' ( ' . $tahun . ' )';
                    }
                    $data_user[$recKey]['skpd'] = implode('<br>', $skpd_html);

                    if (in_array("administrator", $user_meta->roles)) {
                        $btn .= '<a class="btn btn-sm btn-danger" onclick="delete_data(\'' . $recVal->ID . '\'); return false;" href="#" title="Hapus Data"><i class="dashicons dashicons-trash"></i></a>';
                    } else {
                        $cek_skpd_lain = false;
                        foreach ($skpd[0] as $tahun => $id_skpd) {
                            if (empty($skpd_user[$id_skpd])) {
                                $cek_skpd_lain = true;
                            }
                        }
                        if (false == $cek_skpd_lain) {
                            $btn .= '<a class="btn btn-sm btn-danger" onclick="delete_data(\'' . $recVal->ID . '\'); return false;" href="#" title="Hapus Data"><i class="dashicons dashicons-trash"></i></a>';
                        }
                    }
                    $data_user[$recKey]['aksi'] = $btn;
                    $data_user[$recKey]['user'] = $recVal->user_login;
                    $data_user[$recKey]['nama'] = $recVal->display_name;
                    $data_user[$recKey]['nomorwa'] = get_user_meta($recVal->ID, 'nomor_wa');
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval(count($data_user)),
                    "recordsFiltered" => intval(count($data_user)),
                    "data"            => $data_user,
                    "sql"             => $sql_user
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
                $allowed_roles = $this->allowed_roles_panjar();

                // Periksa apakah ada perpotongan antara peran yang diizinkan dan peran pengguna saat ini.
                if (empty(array_intersect($allowed_roles, $current_user->roles))) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }

                $prefix = '';
                if (!empty($_POST['tipe_rka'])) {
                    if ($_POST['tipe_rka'] == 'rka_lokal') {
                        $prefix = '_lokal';
                    }
                }

                $sub_keg = $wpdb->get_row($wpdb->prepare("
                SELECT
                    p.*,
                    s.nama_sub_giat,
                    s.kode_urusan
                FROM data_pptk_sub_keg" . $prefix . " p
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
                            'value' => 'i:' . $_POST['tahun_anggaran'] . ';s:4:"' . $_POST['id_skpd'] . '"',
                            'compare' => 'LIKE'
                        )
                    )
                );
                $users = get_users($args);
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
                $allowed_roles = $this->allowed_roles_panjar();
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

                $prefix = '';
                if (!empty($_POST['tipe_rka'])) {
                    if ($_POST['tipe_rka'] == 'rka_lokal') {
                        $prefix = '_lokal';
                    }
                }

                $nama_tabel = "data_pptk_sub_keg" . $prefix;
                $cek_pptk = $wpdb->get_var($wpdb->prepare("
                    SELECT
                        id
                    FROM " . $nama_tabel . "
                    WHERE active=1
                        and tahun_anggaran=%d
                        and kode_sbl=%s
                ", $tahun_anggaran, $kode_sbl));

                if (empty($cek_pptk)) {
                    $result = $wpdb->insert($nama_tabel, $data);
                } else {
                    $wpdb->update($nama_tabel, $data, array('id' => $cek_pptk));
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

    function get_data_nota_pencairan_dana()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil get data nota pencairan dana!';

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

                $data_npd = $wpdb->get_results($wpdb->prepare("
                    SELECT 
                        *
                    FROM data_nota_pencairan_dana
                    WHERE kode_sbl = %s
                        AND tahun_anggaran = %d
                        AND active = 1
                    ORDER BY kode_akun ASC", $kode_sbl, $tahun_anggaran), ARRAY_A);

                $ret['html'] = '';
                $no = 0;
                foreach ($data_npd as $val) {
                    $ret['html'] .= '
                        <tr>
                            <td>' . $no++ . '</td>
                            <td>' . $val['kode_akun'] . '</td>
                            <td>' . $val['kode_akun'] . ' ' . $val['nama_akun'] . '</td>
                            <td>0</td>
                            <td>0</td>
                            <td>' . $val['pagu_pencairan'] . '</td>
                            <td class="aksi">';
                    // tampilkan tombol edit dan hapus
                    $ret['html'] .= '
                                    <a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $val['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>
                                    <a class="btn btn-sm btn-danger" onclick="delete_data(\'' . $val['id'] . '\'); return false;" href="#" title="delete data"><i class="dashicons dashicons-trash"></i></a>';
                    $ret['html'] .= '</td></tr>';
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

    function get_daftar_panjar()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil get daftar panjar!';

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

                $data_npd = $wpdb->get_results($wpdb->prepare("
                    SELECT 
                        *
                    FROM data_nota_pencairan_dana
                    WHERE kode_sbl = %s
                        AND tahun_anggaran = %d
                        AND active = 1
                    GROUP BY nomor_npd", $kode_sbl, $tahun_anggaran), ARRAY_A);

                $data_all = array(
                    'total_pencairan_bku' => 0,
                    'total_pencairan_all' => 0,
                    'total_pencairan_all_non_panjar' => 0,
                    'data' => array()
                );

                foreach ($data_npd as $v_npd) {
                    if (empty($data_all['data'][$v_npd['nomor_npd']])) {
                        $data_all['data'][$v_npd['nomor_npd']] = array(
                            'id' => $v_npd['id'],
                            'nomor_npd' => $v_npd['nomor_npd'],
                            'jenis_panjar' => $v_npd['jenis_panjar'],
                            'id_user_pptk' => $v_npd['id_user_pptk'],
                            'total_bku' => 0,
                            'total_pagu' => 0,
                            'total_pagu_non_panjar' => 0
                        );
                    }

                    if ($v_npd['jenis_panjar'] == 'set_panjar') {
                        // Menghitung total pagu untuk 'set_panjar'
                        $total_pagu_npd = $wpdb->get_var(
                            $wpdb->prepare("
                                SELECT
                                    SUM(pagu_dana) as total_pagu
                                FROM data_rekening_nota_pencairan_dana
                                WHERE id_npd = %d
                                  AND kode_sbl = %s
                                  AND tahun_anggaran = %d
                                  AND active = 1
                            ", $v_npd['id'], $v_npd['kode_sbl'], $v_npd['tahun_anggaran'])
                        );

                        if (!empty($total_pagu_npd)) {
                            $data_all['data'][$v_npd['nomor_npd']]['total_pagu'] = $total_pagu_npd;
                            $data_all['total_pencairan_all'] += $total_pagu_npd;
                        }
                    }

                    if ($v_npd['jenis_panjar'] == 'not_set_panjar') {
                        // Menghitung total pagu untuk 'not_set_panjar'
                        $total_pagu_npd_non_panjar = $wpdb->get_var(
                            $wpdb->prepare("
                                SELECT
                                    SUM(pagu_dana) as total_pagu
                                FROM data_rekening_nota_pencairan_dana
                                WHERE id_npd = %d
                                  AND kode_sbl = %s
                                  AND tahun_anggaran = %d
                                  AND active = 1
                            ", $v_npd['id'], $v_npd['kode_sbl'], $v_npd['tahun_anggaran'])
                        );

                        // Jika tidak ada data untuk 'not_set_panjar', set total_pagu_non_panjar menjadi 0
                        if (!empty($total_pagu_npd_non_panjar)) {
                            $data_all['data'][$v_npd['nomor_npd']]['total_pagu_non_panjar'] = $total_pagu_npd_non_panjar;
                            $data_all['total_pencairan_all_non_panjar'] += $total_pagu_npd_non_panjar;
                        }
                    }

                    // Menghitung total pagu BUKU
                    $total_bku = $wpdb->get_var($wpdb->prepare("
                        SELECT 
                            SUM(bku.pagu) as total_pagu_bku
                        FROM data_buku_kas_umum_pembantu as bku
                        WHERE bku.id_npd=%d
                            AND bku.tahun_anggaran=%d
                            AND bku.active=1
                    ", $v_npd['id'], $v_npd['tahun_anggaran']));

                    if (!empty($total_bku)) {
                        $data_all['data'][$v_npd['nomor_npd']]['total_bku'] = $total_bku;
                        $data_all['total_pencairan_bku'] += $total_bku;
                    }
                }

                $ret['html'] = '';
                foreach ($data_all['data'] as $v_all_npd) {
                    $current_user = get_userdata($v_all_npd['id_user_pptk']);
                    $jenis_panjar = $v_all_npd['jenis_panjar'] == 'set_panjar' ? 'Panjar' : 'Tanpa Panjar';
                    $ret['html'] .= '
                        <tr>
                            <td class="kanan bawah kiri id-npd-' . $v_all_npd['id'] . '">' . $v_all_npd['nomor_npd'] . '</td>
                            <td class="kanan bawah text-center">' . $jenis_panjar . '</td>
                            <td class="kanan bawah text-left">' . $current_user->display_name . '</td>
                            <td class="kanan bawah text-right">' . number_format($v_all_npd['total_bku'], 0, ",", ".") . '</td>
                            <td class="kanan bawah text-right">' . number_format($v_all_npd['total_pagu'], 0, ",", ".") . '</td>
                            <td class="kanan bawah text-right">' . number_format($v_all_npd['total_pagu_non_panjar'], 0, ",", ".") . '</td>
                            <td class="kanan bawah text-right text-center">';
                    // tampilkan tombol edit dan hapus
                    $ret['html'] .= '
                                <a class="btn btn-sm btn-dark" onclick="buku_kas_umum_pembantu(' . $v_all_npd['id'] . '); return false;" href="#" title="Buku Kas Umum Pembantu"><i class="dashicons dashicons-book"></i></a>
                                <a class="btn btn-sm btn-info" onclick="print(' . $v_all_npd['id'] . '); return false;" href="#" title="Print"><i class="dashicons dashicons-printer"></i></a>
                                <a class="btn btn-sm btn-success" onclick="tambah_rekening(' . $v_all_npd['id'] . '); return false;" href="#" title="Tambah Rekening"><i class="dashicons dashicons-plus"></i></a>
                                <a class="btn btn-sm btn-warning" onclick="edit_data(' . $v_all_npd['id'] . '); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>
                                <a class="btn btn-sm btn-danger" onclick="delete_data(' . $v_all_npd['id'] . '); return false;" href="#" title="delete data"><i class="dashicons dashicons-trash"></i></a>';
                    $ret['html'] .= '</td>
                        </tr>';
                }

                $jml_pencairan = $data_all['total_pencairan_all'] + $data_all['total_pencairan_all_non_panjar'];
                $ret['html'] .= '
                <tr>
                    <td colspan="3" class="kanan bawah text-right kiri text_blok">Jumlah</td>
                    <td class="kanan bawah text_blok text-right total_all">' . number_format($data_all['total_pencairan_bku'], 0, ",", ".") . '</td>
                    <td class="kanan bawah text_blok text-right total_all">' . number_format($data_all['total_pencairan_all'], 0, ",", ".") . '</td>
                    <td class="kanan bawah text_blok text-right total_all">' . number_format($data_all['total_pencairan_all_non_panjar'], 0, ",", ".") . '</td>
                    <td class="kanan bawah text_blok text-right total_all">' . number_format($jml_pencairan, 0, ",", ".") . '</td>
                </tr>';
                $ret['data'] = $data_all;

                // rekap panjar per sub kegiatan
                $total_pagu_rka_sub_keg = 0;
                $data_rekening_html = '
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="atas kanan bawah kiri text-center" width="150px;">Kode</th>
                            <th class="atas kanan bawah text-center">Rekening</th>
                            <th class="atas kanan bawah text-center" width="150px;">Pagu</th>
                            <th class="atas kanan bawah text-center" width="150px;">Realisasi SIPD</th>
                            <th class="atas kanan bawah text-center" width="150px;">Panjar</th>
                            <th class="atas kanan bawah text-center" width="150px;">Non Panjar</th>
                            <th class="atas kanan bawah text-center" width="150px;">Sisa Pagu</th>
                        </tr>
                        <tr>
                            <th class="atas kanan bawah kiri text-center">1</th>
                            <th class="atas kanan bawah text-center">2</th>
                            <th class="atas kanan bawah text-center">3</th>
                            <th class="atas kanan bawah text-center">4</th>
                            <th class="atas kanan bawah text-center">5</th>
                            <th class="atas kanan bawah text-center">6</th>
                            <th class="atas kanan bawah text-center">7=3-(5+6)</th>
                        </tr>
                    </thead>';
                $total_realisasi_sipd = 0;
                $total_realisasi_panjar = 0;
                $total_realisasi_non_panjar = 0;
                $data_rekening = $wpdb->get_results($wpdb->prepare("
                    SELECT 
                        SUM(total_harga) as total_harga,
                        kode_akun,
                        nama_akun
                    from data_rka 
                    where kode_sbl=%s
                        AND tahun_anggaran=%d
                        AND active=1
                    GROUP by kode_akun
                ", $kode_sbl, $tahun_anggaran), ARRAY_A);
                $data_rekening_obj = array();
                foreach ($data_rekening as $k => $rek) {
                    $realisasi_panjar = $wpdb->get_var($wpdb->prepare("
                        SELECT
                            SUM(r.pagu_dana) as total_pagu
                        FROM data_rekening_nota_pencairan_dana r
                        INNER JOIN data_nota_pencairan_dana p ON r.kode_sbl=p.kode_sbl
                            AND p.active=r.active
                            AND p.tahun_anggaran=r.tahun_anggaran
                            AND p.jenis_panjar='set_panjar'
                        WHERE r.kode_rekening = %s
                            AND r.kode_sbl = %s
                            AND r.tahun_anggaran = %d
                            AND r.active = 1
                    ", $rek['kode_akun'], $kode_sbl, $tahun_anggaran));
                    $realisasi_non_panjar = $wpdb->get_var($wpdb->prepare("
                        SELECT
                            SUM(r.pagu_dana) as total_pagu
                        FROM data_rekening_nota_pencairan_dana r
                        INNER JOIN data_nota_pencairan_dana p ON r.kode_sbl=p.kode_sbl
                            AND p.active=r.active
                            AND p.tahun_anggaran=r.tahun_anggaran
                            AND p.jenis_panjar='not_set_panjar'
                        WHERE r.kode_rekening = %s
                            AND r.kode_sbl = %s
                            AND r.tahun_anggaran = %d
                            AND r.active = 1
                    ", $rek['kode_akun'], $kode_sbl, $tahun_anggaran));

                    $kode_sbls = explode('.', $kode_sbl);

                    $realisasi_sipd = $wpdb->get_var($wpdb->prepare("
                        select
                            sum(realisasi) as realisasi
                        from data_realisasi_akun_sipd
                        where active=1
                            and tahun_anggaran=%d
                            and kode_akun=%s
                            and kode_sbl=%s
                        group by kode_akun
                    ", $tahun_anggaran, $rek['kode_akun'], $kode_sbl));
                    if (empty($realisasi_sipd) && !empty($kode_sbls[4])) {
                        $realisasi_sipd = $wpdb->get_var($wpdb->prepare("
                            select
                                sum(realisasi) as realisasi
                            from data_realisasi_akun_sipd
                            where active=1
                                and tahun_anggaran=%d
                                and kode_akun=%s
                                and id_skpd=%d
                                and id_sub_skpd=%d
                                and id_program=%d
                                and id_giat=%d
                                and id_sub_giat=%d
                            group by kode_akun
                        ", $tahun_anggaran, $rek['kode_akun'], $kode_sbls[0], $kode_sbls[1], $kode_sbls[2], $kode_sbls[3], $kode_sbls[4]));
                    }

                    $total_pagu_rka_sub_keg += $rek['total_harga'];
                    $total_realisasi_panjar += $realisasi_panjar;
                    $total_realisasi_non_panjar += $realisasi_non_panjar;
                    $total_realisasi_sipd += $realisasi_sipd;
                    $sisa = $rek['total_harga'] - ($realisasi_panjar + $realisasi_non_panjar);
                    $data_rekening_html .= '
                        <tr>
                            <td class="atas kanan bawah kiri text-center">' . $rek['kode_akun'] . '</td>
                            <td class="atas kanan bawah">' . str_replace($rek['kode_akun'] . ' ', '', $rek['nama_akun']) . '</td>
                            <td class="atas kanan bawah text-right">' . number_format($rek['total_harga'], 0, ",", ".") . '</td>
                            <td class="atas kanan bawah text-right">' . number_format($realisasi_sipd, 0, ",", ".") . '</td>
                            <td class="atas kanan bawah text-right">' . number_format($realisasi_panjar, 0, ",", ".") . '</td>
                            <td class="atas kanan bawah text-right">' . number_format($realisasi_non_panjar, 0, ",", ".") . '</td>
                            <td class="atas kanan bawah text-right">' . number_format($sisa, 0, ",", ".") . '</td>
                        </tr>
                    ';
                    $data_rekening[$k]['sisa'] = $sisa;
                    $data_rekening[$k]['realisasi'] = ($realisasi_panjar + $realisasi_non_panjar);
                    $data_rekening[$k]['realisasi_sipd'] = $realisasi_sipd;
                    $data_rekening[$k]['sql_realisasi_sipd'] = $wpdb->last_query;
                    $data_rekening_obj[$rek['kode_akun']] = $data_rekening[$k];
                }
                $total_sisa = $total_pagu_rka_sub_keg - ($total_realisasi_panjar + $total_realisasi_non_panjar);
                $data_rekening_html .= '
                    <tfoot>
                        <th class="atas kanan bawah kiri text-center" colspan="2" class="text-center">Total</th>
                        <th class="atas kanan bawah text-right">' . number_format($total_pagu_rka_sub_keg, 0, ",", ".") . '</th>
                        <th class="atas kanan bawah text-right">' . number_format($total_realisasi_sipd, 0, ",", ".") . '</th>
                        <th class="atas kanan bawah text-right">' . number_format($total_realisasi_panjar, 0, ",", ".") . '</th>
                        <th class="atas kanan bawah text-right">' . number_format($total_realisasi_non_panjar, 0, ",", ".") . '</th>
                        <th class="atas kanan bawah text-right">' . number_format($total_sisa, 0, ",", ".") . '</th>
                    </tfoot>
                </table>
                ';
                $ret['detail_sub_keg'] = $data_rekening_html;
                $ret['detail_rekening'] = $data_rekening_obj;
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

    function tambah_data_panjar($return_callback = false)
    {
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'     => 'Tambah data panjar!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tahun anggaran tidak boleh kosong!';
                    die(json_encode($ret));
                } else if (empty($_POST['kode_sbl'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'kode sbl tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $tahun_anggaran = $_POST['tahun_anggaran'];
                $kode_sbl = $_POST['kode_sbl'];
                $data = json_decode(stripslashes($_POST['data']), true);
                if (empty($data['nomor_npd'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Nomor NPD tidak boleh kosong!';
                } elseif (empty($data['set_panjar'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Setting Panjar tidak boleh kosong!';
                } elseif (empty($data['set_pptk'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Setting PPTK tidak boleh kosong!';
                } elseif (empty($data['nomor_dpa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Nomor DPA tidak boleh kosong!';
                }

                // cek role user existing harus administrator atau PA, PLT, KPA
                $current_user = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_panjar();

                // Periksa apakah ada perpotongan antara peran yang diizinkan dan peran pengguna saat ini.
                if (empty(array_intersect($allowed_roles, $current_user->roles))) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }

                $jenis_panjar = ($data['set_panjar'] == 'dengan_panjar') ? 'set_panjar' : 'not_set_panjar';

                if ($ret['status'] != 'error') {
                    // $data_akun = $wpdb->get_row($wpdb->prepare('
                    // 	SELECT 
                    // 		*
                    // 	FROM data_akun
                    // 	WHERE kode_akun=%s
                    // 		AND tahun_anggaran=%d
                    // 		AND active=1
                    // ', $data['rekening_akun'], $tahun_anggaran));

                    //Cek nomor npd jika tambah baru
                    $cek_nomor = $wpdb->get_var($wpdb->prepare('
                        SELECT
                            nomor_npd
                        FROM data_nota_pencairan_dana
                        WHERE nomor_npd=%s
                            AND tahun_anggaran=%d
                            AND active=1
                    ', $data['nomor_npd'], $tahun_anggaran));

                    if (empty($_POST['id']) && !empty($cek_nomor)) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Nomor NPD sudah terpakai!';
                        die(json_encode($ret));
                    }

                    $input_options = array(
                        'kode_sbl' => $kode_sbl,
                        'nomor_npd' => $data['nomor_npd'],
                        'id_user_pptk' => $data['set_pptk'],
                        'jenis_panjar' => $jenis_panjar,
                        'nomor_dpa' => $data['nomor_dpa'],
                        'pagu_pencairan' => 0,
                        'tahun_anggaran' => $tahun_anggaran,
                        'active' => 1,
                        'created_at' => current_time('mysql'),
                        'update_at' => current_time('mysql')
                    );

                    //insert data panjar
                    if (!empty($_POST['id'])) {
                        $cek_id = $wpdb->get_var($wpdb->prepare('
                            SELECT
                                id
                            FROM data_nota_pencairan_dana
                            WHERE id=%d
                                AND tahun_anggaran=%d
                                AND active=1
                        ', $_POST['id'], $tahun_anggaran));

                        if (!$cek_id) {
                            //cek nomor npd
                            $cek_nomor = $wpdb->get_var($wpdb->prepare('
                                SELECT
                                    nomor_npd
                                FROM data_nota_pencairan_dana
                                WHERE nomor_npd=%s
                                    AND tahun_anggaran=%d
                                    AND active=1
                            ', $data['nomor_npd'], $tahun_anggaran));

                            if (!empty($cek_nomor)) {
                                $ret['status'] = 'error';
                                $ret['message'] = 'Nomor NPD sudah terpakai!';
                                die(json_encode($ret));
                            }

                            $wpdb->insert('data_nota_pencairan_dana', $input_options);
                            $ret['message'] = 'Berhasil menambahkan data panjar!';
                        } else {
                            $wpdb->update('data_nota_pencairan_dana', $input_options, array('id' => $cek_id));
                            $ret['message'] = 'Berhasil update data panjar!';
                        }
                    } else {
                        $wpdb->insert('data_nota_pencairan_dana', $input_options);
                        $ret['message'] = 'Berhasil menambahkan data panjar!';
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

        if ($return_callback) {
            return $ret;
        } else {
            die(json_encode($ret));
        }
    }

    function edit_data_panjar()
    {
        $this->tambah_data_panjar();
    }

    function delete_data_panjar()
    {
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'     => 'Hapus data panjar!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id tidak boleh kosong!';
                    die(json_encode($ret));
                }

                // cek role user existing harus administrator atau PA, PLT, KPA
                $current_user = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_panjar();

                // Periksa apakah ada perpotongan antara peran yang diizinkan dan peran pengguna saat ini.
                if (empty(array_intersect($allowed_roles, $current_user->roles))) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }

                if ($ret['status'] != 'error') {
                    $status = $wpdb->update('data_nota_pencairan_dana', array('active' => 0), array('id' => $_POST['id']));

                    if ($status === false) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Delete gagal, harap hubungi admin!';
                    } else {
                        $ret['message'] = 'Data berhasil dihapus';
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

    function get_nota_panjar_by_id()
    {
        global $wpdb;
        $ret = array(
            'status'   => 'success',
            'message'  => 'success',
            'data'     => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id npd tidak boleh kosong!';
                    die(json_encode($ret));
                } else if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tahun anggaran tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $data_nota_panjar = $wpdb->get_row(
                    $wpdb->prepare("
                        SELECT *
                        FROM data_nota_pencairan_dana
                        WHERE id = %d
                          AND tahun_anggaran=%d
                          AND active=1
                    ", $_POST['id'], $_POST['tahun_anggaran']),
                    ARRAY_A
                );

                if (!empty($data_nota_panjar)) {
                    // Get user data by user id for kwitansi page
                    $user = get_userdata($data_nota_panjar['id_user_pptk']);

                    $data_nota_panjar['jenis_panjar'] = $data_nota_panjar['jenis_panjar'] == 'set_panjar' ? 'dengan_panjar' : 'tanpa_panjar';

                    $ret['data'] = $data_nota_panjar;
                    $ret['data']['pptk_name'] = $user->display_name;
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Nota Panjar tidak ditemukan!';
                }

                if ($ret['status'] != 'error') {
                    $data_rekening_nota_panjar = $wpdb->get_results(
                        $wpdb->prepare("
                            SELECT 
                                rnpd.*, 
                                SUM(bku.pagu) as total_pagu_bku
                            FROM data_rekening_nota_pencairan_dana as rnpd
                            LEFT JOIN data_buku_kas_umum_pembantu as bku 
                                ON rnpd.kode_rekening = bku.kode_rekening
                                AND bku.id_npd = %d
                                AND bku.tahun_anggaran = %d
                                AND bku.active = 1
                            WHERE rnpd.id_npd = %d
                              AND rnpd.tahun_anggaran = %d
                              AND rnpd.active = 1
                            GROUP BY rnpd.kode_rekening
                            ORDER BY rnpd.kode_rekening ASC
                        ", $_POST['id'], $_POST['tahun_anggaran'], $_POST['id'], $_POST['tahun_anggaran'])
                    );

                    $ret['data']['rekening_npd'] = array();
                    if (!empty($data_rekening_nota_panjar)) {
                        $ret['data']['rekening_npd'] = $data_rekening_nota_panjar;
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

    function get_rka_sub_keg_akun()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil get data akun rka per sub keg!';
        $ret['data_akun_html'] = '';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tahun anggaran tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['kode_sbl'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Kode sbl tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $tahun_anggaran = $_POST['tahun_anggaran'];
                $kode_sbl = $_POST['kode_sbl'];
                // cek role user existing harus administrator atau PA, PLT, KPA
                $current_user = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_panjar();

                // Periksa apakah ada perpotongan antara peran yang diizinkan dan peran pengguna saat ini.
                if (empty(array_intersect($allowed_roles, $current_user->roles)) && (empty($_POST['jenis_data']) || $_POST['jenis_data'] !== 'sakip')) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }

                $prefix = '';
                if (!empty($_POST['tipe_rka'])) {
                    if ($_POST['tipe_rka'] == 'rka_lokal') {
                        $prefix = '_lokal';
                    }
                }

                $data_akun = $wpdb->get_results($wpdb->prepare("
					SELECT 
                        drka.kode_akun,
                        drka.nama_akun 
					FROM data_rka" . $prefix . " as drka 
					WHERE drka.kode_sbl=%s
						AND drka.tahun_anggaran=%d
						AND drka.active=1
					GROUP BY drka.kode_akun 
                    ORDER BY drka.kode_akun ASC
                ", $kode_sbl, $tahun_anggaran), ARRAY_A);

                $data_akun_options = '<option value="">Pilih Rekening</option>';
                foreach ($data_akun as $v_akun) {
                    $data_akun_options .= '<option value="' . $v_akun['kode_akun'] . '">' . $v_akun['kode_akun'] . ' ' . str_replace($v_akun['kode_akun'] . ' ', '', $v_akun['nama_akun']) . '</option>';
                }
                $ret['sql'] = $wpdb->last_query;
                $ret['data_akun_html'] = $data_akun_options;
                $ret['data_akun'] = $data_akun;
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

    function allowed_roles_panjar()
    {
        return array(
            'administrator',
            'PA',
            'KPA',
            'PLT',
            'pptk',
            'KABID',
            'kasubid',
            'staff'
        );
    }

    function tambah_data_rekening_panjar($return_callback = false)
    {
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'     => 'Tambah rekening data panjar!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $current_user = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_panjar();
                if (empty(array_intersect($allowed_roles, $current_user->roles))) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }

                if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tahun anggaran tidak boleh kosong!';
                    die(json_encode($ret));
                } else if (empty($_POST['kode_sbl'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'kode sbl tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $tahun_anggaran = $_POST['tahun_anggaran'];
                $kode_sbl = $_POST['kode_sbl'];
                $data = json_decode(stripslashes($_POST['data']), true);
                if (empty($data['id_npd_rek'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Id NPD tidak boleh kosong!';
                }

                foreach ($data['rekening_akun'] as $k_rekening => $v_rekening) {
                    if ($ret['status'] != 'error') {
                        if (
                            !isset($data['pagu_rekening'][$k_rekening])
                            || $data['pagu_rekening'][$k_rekening] == ''
                        ) {
                            $ret['status'] = 'error';
                            $ret['message'] = 'Pagu Rekening tidak boleh kosong!';
                        } else if (empty($data['rekening_akun'][$k_rekening])) {
                            $ret['status'] = 'error';
                            $ret['message'] = 'Rekening tidak boleh kosong!';
                        }
                    }
                }

                if ($ret['status'] != 'error') {
                    //insert  rekening
                    $wpdb->update(
                        'data_rekening_nota_pencairan_dana',
                        array('active' => 0),
                        array(
                            'kode_sbl' => $kode_sbl,
                            'tahun_anggaran' => $tahun_anggaran,
                            'id_npd' => $data['id_npd_rek']
                        )
                    );
                    $wpdb->update(
                        'data_buku_kas_umum_pembantu',
                        array('active' => 0),
                        array(
                            'kode_sbl' => $kode_sbl,
                            'tahun_anggaran' => $tahun_anggaran,
                            'id_npd' => $data['id_npd_rek'],
                            'tipe' => 'penerimaan'
                        )
                    );
                    foreach ($data['rekening_akun'] as $k_rek_akun => $v_rek_akun) {
                        $pagu = 0;
                        if (!empty($data['pagu_rekening'][$k_rek_akun])) {
                            $pagu = str_replace('.', '', $data['pagu_rekening'][$k_rek_akun]);
                        }
                        $data_akun = $wpdb->get_row(
                            $wpdb->prepare('
                                SELECT 
                                    *
                                FROM data_rka
                                WHERE kode_akun = %s
                                  AND kode_sbl = %s
                                  AND tahun_anggaran = %d
                                  AND active = 1
                            ', $data['rekening_akun'][$k_rek_akun], $kode_sbl, $tahun_anggaran)
                        );

                        if (!empty($data_akun)) {
                            $cek_ids = $wpdb->get_results(
                                $wpdb->prepare('
                                    SELECT 
                                        id
                                    FROM data_rekening_nota_pencairan_dana
                                    WHERE kode_sbl = %s
                                      AND id_npd = %d
                                      AND tahun_anggaran = %d
                                      AND active = 1
                                ', $kode_sbl, $data['id_npd_rek'][$k_rek_akun], $tahun_anggaran),
                                ARRAY_A
                            );

                            $opsi_rekening = array(
                                'nama_rekening' => $data_akun->nama_akun,
                                'kode_rekening' => $data_akun->kode_akun,
                                'id_rekening' => $data_akun->id_akun,
                                'pagu_dana' => $pagu,
                                'kode_sbl' => $kode_sbl,
                                'id_npd' => $data['id_npd_rek'],
                                'update_at' => current_time('mysql'),
                                'active' => 1,
                                'tahun_anggaran' => $tahun_anggaran
                            );

                            $input_pemasukan = array(
                                'kode_sbl' => $kode_sbl,
                                'tipe' => 'penerimaan',
                                'uraian' => 'Penerimaan',
                                'id_npd' => $data['id_npd_rek'],
                                'tanggal_bkup' => current_time('mysql'),
                                'tahun_anggaran' => $tahun_anggaran,
                                'active' => 1,
                                'created_at' => current_time('mysql'),
                                'update_at' => current_time('mysql')
                            );

                            if (
                                empty($cek_ids)
                                || empty($cek_ids[$k_rek_akun])
                            ) {
                                $wpdb->insert(
                                    'data_rekening_nota_pencairan_dana',
                                    $opsi_rekening
                                );
                                $wpdb->insert(
                                    'data_buku_kas_umum_pembantu',
                                    $input_pemasukan
                                );
                                $ret['message'] = 'Berhasil menambahkan rekening data NPD! Berhasil menambahkan penerimaan data BKU';
                            } else {
                                $wpdb->update(
                                    'data_rekening_nota_pencairan_dana',
                                    $opsi_rekening,
                                    array('id' => $cek_ids[$k_rek_akun]['id'])
                                );
                                $wpdb->update(
                                    'data_rekening_nota_pencairan_dana',
                                    $opsi_rekening,
                                    array('id_npd' => $cek_ids[$k_rek_akun]['id'])
                                );
                                $ret['message'] = 'Berhasil update rekening data NPD! Berhasil update penerimaan data BKU';
                            }
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = 'Data rekening ' . $data['rekening_akun'][$k_rek_akun] . ' tidak ditemukan di Sub Kegiatan! kode_sbl=' . $kode_sbl;
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

        if ($return_callback) {
            return $ret;
        } else {
            die(json_encode($ret));
        }
    }

    function get_daftar_bku()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil mendapatkan daftar buku kas umum pembantu!'
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $kode_sbl = $_POST['kode_sbl'] ?? '';
                $tahun_anggaran = $_POST['tahun_anggaran'] ?? '';
                $kode_npd = $_POST['kode_npd'] ?? '';
                if (empty($kode_sbl)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Kode SBL tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($tahun_anggaran)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Tahun anggaran tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($kode_npd)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Kode NPD tidak boleh kosong!';
                    die(json_encode($ret));
                }

                // Query data buku kas umum pembantu
                $data_bku = $wpdb->get_results(
                    $wpdb->prepare("
                        SELECT * 
                        FROM data_buku_kas_umum_pembantu 
                        WHERE kode_sbl = %s 
                          AND tahun_anggaran = %d 
                          AND active = 1 
                          AND tipe = 'pengeluaran' 
                          AND id_npd = %d
                     ", $kode_sbl, $tahun_anggaran, $kode_npd),
                    ARRAY_A
                );

                // Mendapatkan total pagu NPD
                $total_pagu_npd = $wpdb->get_var(
                    $wpdb->prepare("
                        SELECT SUM(pagu_dana) AS total_pagu 
                        FROM data_rekening_nota_pencairan_dana 
                        WHERE id_npd = %d 
                          AND kode_sbl = %s 
                          AND tahun_anggaran = %d 
                          AND active = 1
                     ", $kode_npd, $kode_sbl, $tahun_anggaran)
                );

                $total_pagu_npd = $total_pagu_npd ?: 0;
                $ret['html'] = '';

                // Tampilkan penerimaan
                $data_bku_penerimaan = $wpdb->get_row(
                    $wpdb->prepare("
                        SELECT * 
                        FROM data_buku_kas_umum_pembantu 
                        WHERE kode_sbl = %s 
                          AND tahun_anggaran = %d 
                          AND active = 1 
                          AND tipe = 'penerimaan' 
                          AND id_npd = %d
                     ", $kode_sbl, $tahun_anggaran, $kode_npd),
                    ARRAY_A
                );

                $uraian = '-';
                $id_penerimaan = 0;

                if (!empty($data_bku_penerimaan)) {
                    $uraian = $data_bku_penerimaan['uraian'];
                    $id_penerimaan = $data_bku_penerimaan['id'];
                    $tanggal = date_format(date_create($data_bku_penerimaan['tanggal_bkup']), "d/m/Y");

                    // HTML untuk penerimaan
                    $ret['html'] .= '
                        <tr>
                            <td class="kanan bawah kiri text-center id-npd">' . $tanggal . '</td>
                            <td class="kanan bawah text-center"></td>
                            <td class="kanan bawah text-left">' . $uraian . '</td>
                            <td class="kanan bawah text-right">' . number_format($total_pagu_npd, 0, ",", ".") . '</td>
                            <td class="kanan bawah text-right"></td>
                            <td class="kanan bawah text-right">' . number_format($total_pagu_npd, 0, ",", ".") . '</td>
                            <td class="kanan bawah text-center">
                                <a class="btn btn-sm btn-warning" onclick="edit_data(' . $id_penerimaan . ', \'terima\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>
                            </td>
                        </tr>';
                }

                // Tampilkan pengeluaran
                $total_pagu_npd_sekarang = $total_pagu_npd;
                $total_pengeluaran = 0;

                if (!empty($data_bku)) {
                    foreach ($data_bku as $v_bku) {
                        $kwitansi_page = $this->generatePage(
                            'Cetak Kwitansi BKU',
                            $_POST['tahun_anggaran'],
                            '[cetak_kwitansi_bku]',
                            false
                        );
                        $saldo = $total_pagu_npd_sekarang - $v_bku['pagu'];
                        $total_pagu_npd_sekarang = $saldo;
                        $tanggal = date_format(date_create($v_bku['tanggal_bkup']), "d/m/Y");

                        // HTML untuk pengeluaran
                        $ret['html'] .= '
                            <tr>
                                <td class="kanan bawah kiri text-center id-npd-' . $v_bku['id'] . '">' . $tanggal . '</td>
                                <td class="kanan bawah text-center">' . $v_bku['nomor_bukti'] . '</td>
                                <td class="kanan bawah text-left">' . $v_bku['uraian'] . '</td>
                                <td class="kanan bawah text-right"></td>
                                <td class="kanan bawah text-right">' . number_format($v_bku['pagu'], 0, ",", ".") . '</td>
                                <td class="kanan bawah text-right">' . number_format($saldo, 0, ",", ".") . '</td>
                                <td class="kanan bawah text-center">
                                    <a class="btn btn-sm btn-warning" onclick="edit_data(' . $v_bku['id'] . '); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>
                                    <a class="btn btn-sm btn-danger" onclick="delete_data(' . $v_bku['id'] . '); return false;" href="#" title="Delete Data"><i class="dashicons dashicons-trash"></i></a>
                                    <a class="btn btn-sm btn-info" onclick="print_kwitansi(\'' . $kwitansi_page . '&id_bku=' . $v_bku['id'] . '&tahun_anggaran=' . $v_bku['tahun_anggaran'] . '\')" href="#" title="Cetak Kwitansi"><i class="dashicons dashicons-printer"></i></a>
                                </td>
                            </tr>';
                        $total_pengeluaran += $v_bku['pagu'];
                    }

                    // Baris untuk total pengeluaran
                    $ret['html'] .= '
                        <tr>
                            <td colspan="3" class="kanan bawah text-right kiri text_blok">Jumlah</td>
                            <td class="kanan bawah text_blok text-right">' . number_format($total_pagu_npd, 0, ",", ".") . '</td>
                            <td class="kanan bawah text_blok text-right">' . number_format($total_pengeluaran, 0, ",", ".") . '</td>
                            <td class="kanan bawah text_blok text-right">' . number_format($saldo, 0, ",", ".") . '</td>
                            <td class="kanan bawah"></td>
                        </tr>';
                }

                // Jika tidak ada data, tampilkan pesan kosong
                if (empty($ret['html'])) {
                    $ret['html'] = '<tr><td colspan="7" class="kiri bawah kanan text-center">Data kosong</td></tr>';
                }

                $ret['data_bku'] = $data_bku;
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

    function tambah_data_bku()
    {
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'     => 'Tambah Data Buku Kas Umum Pembantu!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                //user role check
                $current_user = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_panjar();
                if (empty(array_intersect($allowed_roles, $current_user->roles))) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }

                //validate
                $postData = $_POST;
                $validationRules = [
                    'tahun_anggaran' => 'required|max:4|numeric',
                    'kode_sbl'       => 'required',
                    'kode_npd'       => 'required',
                    'uraian_bku'     => 'required',
                    'set_bku'        => 'required|in:terima,keluar',
                    'set_tanggal'    => 'required',
                ];
                // Tambahan validasi untuk pengeluaran
                if ($postData['set_bku'] == 'keluar') {
                    $validationRules['nomor_bukti_bku'] = 'required';
                    $validationRules['pagu_bku']        = 'required';
                    $validationRules['rekening_akun']   = 'required';
                    $validationRules['rincian_rka']     = 'required';
                    // Tambahan validasi untuk transaksi non-tunai
                    if ($postData['jenis_transkasi'] == '2') {
                        $validationRules['nama_pemilik_rekening_bank_bku']  = 'required';
                        $validationRules['nama_rekening_bank_bku']          = 'required';
                        $validationRules['no_rekening_bank_bku']            = 'required|numeric';
                    }
                }
                // Validate data
                $errors = $this->validate($postData, $validationRules);
                if (!empty($errors)) {
                    $ret['status'] = 'error';
                    $ret['message'] = implode(" \n ", $errors);
                    die(json_encode($ret));
                }

                //Cek nomor npd jika tambah baru
                $cek_nomor = $wpdb->get_var(
                    $wpdb->prepare('
                        SELECT
                            nomor_bukti
                        FROM data_buku_kas_umum_pembantu
                        WHERE nomor_bukti = %s
                          AND tahun_anggaran = %d
                          AND active = 1
                    ', $postData['nomor_bukti_bku'], $postData['tahun_anggaran'])
                );

                if (empty($postData['id_data']) && !empty($cek_nomor)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Nomor bukti sudah terpakai!';
                    die(json_encode($ret));
                }

                $tipe_jenis_bku = ($postData['set_bku'] == 'terima') ? 'penerimaan' : 'pengeluaran';

                if ($postData['set_bku'] == 'keluar') {
                    $pagu_bku = str_replace('.', '', $postData['pagu_bku']);
                    $data_npd = $wpdb->get_row(
                        $wpdb->prepare("
                            SELECT 
                                rnpd.*
                            FROM 
                                data_rekening_nota_pencairan_dana as rnpd
                            WHERE rnpd.id_npd = %d
                              AND rnpd.tahun_anggaran = %d
                              AND rnpd.kode_rekening = %s
                              AND rnpd.active = 1
                        ", $postData['kode_npd'], $postData['tahun_anggaran'], $postData['rekening_akun']),
                        ARRAY_A
                    );
                    $data_sisa_pagu_npd = $data_npd['pagu_dana'];
                    $data_akun = array(
                        'kode_akun' => $data_npd['kode_rekening'],
                        'nama_akun' => str_replace($data_npd['kode_rekening'], '', $data_npd['nama_rekening'])
                    );
                    if (!empty($data_akun)) {
                        $set_id_bku = !empty($postData['id_data']) ? ' AND id != ' . $postData['id_data'] : '';
                        $data_total_pagu_bku = $wpdb->get_var(
                            $wpdb->prepare("
                                SELECT 
                                    SUM(bku.pagu) as total_pagu_bku
                                FROM 
                                    data_buku_kas_umum_pembantu as bku
                                WHERE bku.id_npd = %d
                                  AND bku.tahun_anggaran = %d
                                  AND bku.kode_rekening = %s
                                  AND bku.active = 1
                                " . $set_id_bku, $postData['kode_npd'], $postData['tahun_anggaran'], $postData['rekening_akun'])
                        );
                        $total_pagu_bku = (!empty($data_total_pagu_bku)) ? $data_total_pagu_bku + $pagu_bku : $pagu_bku;

                        if ($total_pagu_bku > $data_sisa_pagu_npd) {
                            $ret['status'] = 'error';
                            $ret['message'] = 'Data pagu BKU ' . $this->_number_format($total_pagu_bku) . ' melebihi data pagu di Nota Pencairan Dana sebesar ' . $this->_number_format($data_sisa_pagu_npd) . ' di rekening ' . $data_akun['kode_akun'] . ' ' . $data_akun['nama_akun'] . '!';
                            die(json_encode($ret));
                        }

                        $tanggal = date_format(date_create($postData['set_tanggal']), "Y-m-d H:m:s");
                        $input_options = array(
                            'kode_sbl'                   => $postData['kode_sbl'],
                            'nomor_bukti'                => $postData['nomor_bukti_bku'],
                            'tipe'                       => $tipe_jenis_bku,
                            'uraian'                     => $postData['uraian_bku'],
                            'pagu'                       => $pagu_bku,
                            'kode_rekening'              => $data_akun['kode_akun'],
                            'nama_rekening'              => $data_akun['nama_akun'],
                            'id_npd'                     => $postData['kode_npd'],
                            'tanggal_bkup'               => $tanggal,
                            'tahun_anggaran'             => $postData['tahun_anggaran'],
                            'jenis_cash'                 => $postData['jenis_transkasi'],
                            'active'                     => 1,
                            'created_at'                 => current_time('mysql'),
                            'update_at'                  => current_time('mysql'),
                            'id_rinci_sub_bl'            => $postData['rincian_rka'],
                            'nama_pemilik_rekening_bank' => $postData['nama_pemilik_rekening_bank_bku'],
                            'nama_rekening_bank'         => $postData['nama_rekening_bank_bku'],
                            'no_rekening_bank'           => $postData['no_rekening_bank_bku']
                        );

                        $cek_id = $wpdb->get_var(
                            $wpdb->prepare('
                                SELECT
                                    id
                                FROM data_buku_kas_umum_pembantu
                                WHERE id = %d
                                    AND tahun_anggaran = %d
                                    AND active = 1
                            ', $postData['id_data'], $postData['tahun_anggaran'])
                        );

                        if (!$cek_id) {
                            //cek nomor npd
                            $cek_nomor = $wpdb->get_var(
                                $wpdb->prepare('
                                    SELECT
                                        nomor_bukti
                                    FROM data_buku_kas_umum_pembantu
                                    WHERE nomor_bukti = %s
                                        AND tahun_anggaran = %d
                                        AND active = 1
                                ', $postData['nomor_bukti_bku'], $postData['tahun_anggaran'])
                            );

                            if (!empty($cek_nomor)) {
                                $ret['status'] = 'error';
                                $ret['message'] = 'Nomor bukti sudah terpakai!';
                                die(json_encode($ret));
                            }

                            $wpdb->insert(
                                'data_buku_kas_umum_pembantu',
                                $input_options
                            );
                            $ret['message'] = 'Berhasil menambahkan data Buku Kas Umum Pembantu!';
                        } else {
                            $wpdb->update(
                                'data_buku_kas_umum_pembantu',
                                $input_options,
                                array('id' => $cek_id)
                            );
                            $ret['message'] = 'Berhasil update data Buku Kas Umum Pembantu!';
                        }
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Data rekening tidak ditemukan!';
                    }
                } else if ($postData['set_bku'] == 'terima') {
                    //terima hanya bisa update tanggal dan uraian
                    $tanggal = date_format(date_create($postData['set_tanggal']), "Y-m-d H:m:s");
                    $input_options = array(
                        'uraian'          => $postData['uraian_bku'],
                        'tanggal_bkup'    => $tanggal,
                        'update_at'       => current_time('mysql')
                    );

                    $cek_id = $wpdb->get_var(
                        $wpdb->prepare('
                            SELECT
                                id
                            FROM data_buku_kas_umum_pembantu
                            WHERE id = %d
                                AND tahun_anggaran = %d
                                AND active = 1
                        ', $postData['id_data'], $postData['tahun_anggaran'])
                    );

                    if ($cek_id) {
                        $wpdb->update(
                            'data_buku_kas_umum_pembantu',
                            $input_options,
                            array('id' => $cek_id)
                        );
                        $ret['message'] = 'Berhasil update data Buku Kas Umum Pembantu!';
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Data penerimaan tidak ditemukan!';
                        die(json_encode($ret));
                    }
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Set BKU tidak diketahui!';
                    die(json_encode($ret));
                }

                // update realisasi rincian belanja
                if ($ret['status'] != 'error') {
                    $total_rincian_bku = $wpdb->get_var(
                        $wpdb->prepare('
                            SELECT
                                SUM(pagu)
                            FROM data_buku_kas_umum_pembantu
                            WHERE id_rinci_sub_bl = %d
                              AND tahun_anggaran = %d
                              AND active = 1
                        ', $postData['rincian_rka'], $postData['tahun_anggaran'])
                    );
                    $input_options = array(
                        'id_rinci_sub_bl' => $postData['rincian_rka'],
                        'realisasi'       => $total_rincian_bku,
                        'user'            => $current_user->display_name,
                        'active'          => 1,
                        'update_at'       => current_time('mysql'),
                        'tahun_anggaran'  => $postData['tahun_anggaran']
                    );
                    $cek_rinci = $wpdb->get_row(
                        $wpdb->prepare('
                            SELECT
                                id,
                                realisasi
                            FROM data_realisasi_rincian
                            WHERE id_rinci_sub_bl = %d
                              AND tahun_anggaran = %d
                              AND active = 1
                        ', $postData['rincian_rka'], $postData['tahun_anggaran']),
                        ARRAY_A
                    );
                    if (!empty($cek_rinci)) {
                        $wpdb->insert(
                            'data_realisasi_rincian',
                            $input_options
                        );
                    } else {
                        $postData['realisasi'] += $cek_rinci['realisasi'];
                        $wpdb->update(
                            'data_realisasi_rincian',
                            $input_options,
                            array('id' => $cek_rinci['id'])
                        );
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

    function get_rka_sub_keg_akun_npd()
    {
        global $wpdb;
        $ret = array();
        $ret['status'] = 'success';
        $ret['message'] = 'Berhasil get data akun rka per npd!';
        $ret['data_akun_html'] = '';

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $current_user = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_panjar();
                if (empty(array_intersect($allowed_roles, $current_user->roles))) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }

                if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'tahun anggaran tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['kode_sbl'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Kode sbl tidak boleh kosong!';
                    die(json_encode($ret));
                }
                if (empty($_POST['kode_npd'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Kode npd tidak boleh kosong!';
                    die(json_encode($ret));
                }

                $tahun_anggaran = $_POST['tahun_anggaran'];
                $kode_sbl = $_POST['kode_sbl'];
                $id_npd = $_POST['kode_npd'];

                $data_akun = $wpdb->get_results(
                    $wpdb->prepare("
                        SELECT 
                            daker.kode_rekening,
                            daker.nama_rekening 
                        FROM data_rekening_nota_pencairan_dana as daker
                        WHERE daker.id_npd = %d
                          AND daker.tahun_anggaran=%d
                          AND daker.active = 1
                        GROUP BY daker.kode_rekening
                        ORDER BY daker.kode_rekening ASC
                ", $id_npd, $tahun_anggaran),
                    ARRAY_A
                );

                $data_akun_options = '<option value="">Pilih Rekening</option>';
                foreach ($data_akun as $v_akun) {
                    $v_akun['nama_rekening'] = str_replace($v_akun['kode_rekening'], '', $v_akun['nama_rekening']);
                    $data_akun_options .= '<option value="' . $v_akun['kode_rekening'] . '">' . $v_akun['kode_rekening'] . ' ' . $v_akun['nama_rekening'] . '</option>';
                }
                $ret['data_akun_html'] = $data_akun_options;
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

    function get_data_buku_kas_umum_by_id()
    {
        global $wpdb;
        $ret = array(
            'status'  => 'success',
            'message' => 'Berhasil get data buku kas umum by id!',
            'data'    => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $data_bku = $wpdb->get_row(
                    $wpdb->prepare("
                        SELECT *
                        FROM data_buku_kas_umum_pembantu
                        WHERE id = %d
                          AND tahun_anggaran = %d
                          AND active = 1
                    ", $_POST['id'], $_POST['tahun_anggaran']),
                    ARRAY_A
                );

                if (!empty($data_bku)) {
                    $ret['data'] = $data_bku;
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data BKU tidak ditemukan!';
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

    function delete_data_buku_kas_umum_pembantu()
    {
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'     => 'Berhasil hapus data buku kas umum pembantu!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'id tidak boleh kosong!';
                    die(json_encode($ret));
                }

                // cek role user existing harus administrator atau PA, PLT, KPA
                $current_user = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_panjar();

                // Periksa apakah ada perpotongan antara peran yang diizinkan dan peran pengguna saat ini.
                if (empty(array_intersect($allowed_roles, $current_user->roles))) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }

                if ($ret['status'] != 'error') {
                    $status = $wpdb->update('data_buku_kas_umum_pembantu', array('active' => 0), array('id' => $_POST['id']));

                    if ($status === false) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Delete gagal, harap hubungi admin!';
                    } else {
                        $ret['message'] = 'Data berhasil dihapus';
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

    function get_data_sisa_pagu_per_akun_npd()
    {
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'     => 'Berhasil get data sisa pagu per akun di npd!',
            'data'      => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                // cek role user panjar
                $current_user = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_panjar();
                if (empty(array_intersect($allowed_roles, $current_user->roles))) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    die(json_encode($ret));
                }

                if (empty($_POST['kode_npd'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'kode npd tidak boleh kosong!';
                    die(json_encode($ret));
                } else if (empty($_POST['kode_rekening'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'kode rekening tidak boleh kosong!';
                    die(json_encode($ret));
                }

                if ($ret['status'] != 'error') {
                    $where_edit = '';
                    if (!empty($_POST['id_data'])) {
                        $where_edit = $wpdb->prepare('AND bku.id != %d', $_POST['id_data']);
                    }

                    $data_sisa_pagu_npd = $wpdb->get_row($wpdb->prepare("
                        SELECT 
                            rnpd.pagu_dana as pagu_dana_npd, 
                            SUM(bku.pagu) as total_pagu_bku
                        FROM data_rekening_nota_pencairan_dana as rnpd
                        LEFT JOIN data_buku_kas_umum_pembantu as bku ON rnpd.kode_rekening=bku.kode_rekening
                            AND bku.id_npd=rnpd.id_npd
                            AND bku.tahun_anggaran=rnpd.tahun_anggaran
                            AND bku.active=rnpd.active
                            $where_edit
                        WHERE rnpd.id_npd=%d
                            AND rnpd.tahun_anggaran=%d
                            AND rnpd.active=1
                            AND rnpd.kode_rekening=%s
                    ", $_POST['kode_npd'], $_POST['tahun_anggaran'], $_POST['kode_rekening']));

                    if (!empty($data_sisa_pagu_npd)) {
                        $ret['data'] = $data_sisa_pagu_npd;
                    }
                    $ret['last_q'] = $wpdb->last_query;
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

    function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);

            foreach ($rulesArray as $rule) {
                if ($rule == 'required' && (!isset($data[$field]))) {
                    $errors[] = "$field is required";
                }

                if ($rule == 'string' && isset($data[$field]) && !is_string($data[$field])) {
                    $errors[] = "$field must be a string";
                }

                if ($rule == 'numeric' && isset($data[$field]) && !is_numeric($data[$field])) {
                    $errors[] = "$field must be numeric";
                }

                if (strpos($rule, 'min:') === 0) {
                    $min = (int)explode(':', $rule)[1];
                    if (isset($data[$field]) && strlen($data[$field]) < $min) {
                        $errors[] = "$field must be at least $min characters";
                    }
                }

                if (strpos($rule, 'max:') === 0) {
                    $max = (int)explode(':', $rule)[1];
                    if (isset($data[$field]) && strlen($data[$field]) > $max) {
                        $errors[] = "$field cannot exceed $max characters";
                    }
                }

                if (strpos($rule, 'in:') === 0) {
                    $allowed = explode(',', explode(':', $rule)[1]);
                    if (isset($data[$field]) && !in_array($data[$field], $allowed)) {
                        $errors[] = "$field must be one of: " . implode(', ', $allowed);
                    }
                }
            }
        }

        return $errors;
    }

    function tambah_data_spt()
    {
        global $wpdb;

        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data SPT!',
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {

                //roles validation
                $user_data = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_sppd();
                if (empty(array_intersect($allowed_roles, $user_data->roles))) {
                    return array(
                        'status'  => 'error',
                        'message' => 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!'
                    );
                }

                $postData = $_POST;

                // Define validation rules
                $validationRules = [
                    'tahun_anggaran' => 'required|numeric',
                    'idSkpd'         => 'required',
                    'tanggalSpt'     => 'required',
                    'nomorSpt'       => 'required',
                    'dasarSpt'       => 'required',
                    'tujuanSpt'      => 'required',

                ];

                // Validate data
                $errors = $this->validate($postData, $validationRules);

                if (!empty($errors)) {
                    $ret['status'] = 'error';
                    $ret['message'] = implode(" \n ", $errors);
                    die(json_encode($ret));
                }

                // Data to be saved
                $id_data = !empty($postData['id_data_spt']) ? sanitize_text_field($postData['id_data_spt']) : null;

                // Validasi nomor surat
                $nomor_check_query = $wpdb->get_var($wpdb->prepare(
                    '
                    SELECT 1 
                    FROM data_spt_sppd 
                    WHERE nomor_spt = %s 
                      AND tahun_anggaran = %d 
                      AND active = 1
                    ' . ($id_data ? ' AND id != %d' : ''),
                    $id_data ? [$postData['nomorSpt'], $postData['tahun_anggaran'], $id_data] : [$postData['nomorSpt'], $postData['tahun_anggaran']]
                ));

                if (!empty($nomor_check_query)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Nomor surat sudah digunakan!';
                    die(json_encode($ret));
                }

                $data = array(
                    'tahun_anggaran'     => sanitize_text_field($postData['tahun_anggaran']),
                    'nomor_spt'          => sanitize_text_field($postData['nomorSpt']),
                    'dasar_spt'          => sanitize_textarea_field($postData['dasarSpt']),
                    'tujuan_spt'         => sanitize_textarea_field($postData['tujuanSpt']),
                    'tgl_spt'            => sanitize_text_field($postData['tanggalSpt']),
                    'id_ka_opd'          => sanitize_text_field($postData['']),
                    'id_skpd'            => sanitize_text_field($postData['idSkpd']),
                    'active'             => 1
                );

                // Update or insert
                if ($id_data) {
                    $wpdb->update(
                        'data_spt_sppd',
                        $data,
                        array('id' => $id_data)
                    );
                    $ret['message'] = 'Berhasil update data!';
                } else {
                    $wpdb->insert(
                        'data_spt_sppd',
                        $data
                    );
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }

    function tambah_data_sppd()
    {
        global $wpdb;

        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data SPPD!',
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {

                ///roles validation
                $user_data = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_sppd();
                if (empty(array_intersect($allowed_roles, $user_data->roles))) {
                    return array(
                        'status'  => 'error',
                        'message' => 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!'
                    );
                }

                $postData = $_POST;

                // Define validation rules
                $validationRules = [
                    'tahun_anggaran'     => 'required|numeric',
                    'nomor_spt'          => 'required',
                    'tanggalSppd'        => 'required',
                    'nomorSppd'          => 'required',
                    'namaPegawai'        => 'required',
                    'nipPegawai'         => 'required',
                    'golPegawai'         => 'required',
                    'jabatanPegawai'     => 'required',
                    'tempatBerangkat'    => 'required',
                    'tanggalBerangkat'   => 'required',
                    'tempatTujuan'       => 'required',
                    'tanggalSampai'      => 'required',
                    'tanggalKembali'     => 'required',
                    'keterangan'         => 'required',
                    'maksudSppd'         => 'required',
                    'alatAngkut'         => 'required|in:Kendaraan Dinas,Kendaraan Pribadi,Kendaraan Umum',
                ];

                // Validate data
                $errors = $this->validate($postData, $validationRules);

                if (!empty($errors)) {
                    $ret['status'] = 'error';
                    $ret['message'] = implode(" \n ", $errors);
                    die(json_encode($ret));
                }

                // Data to be saved
                $id_data_sppd = !empty($postData['id_data_sppd']) ? sanitize_text_field($postData['id_data_sppd']) : null;

                // Validasi nomor surat
                $nomor_check_query = $wpdb->get_var($wpdb->prepare(
                    '
                    SELECT 1 
                    FROM data_pegawai_spt_sppd 
                    WHERE nomor_sppd = %s 
                      AND tahun_anggaran = %d 
                      AND active = 1
                    ' . ($id_data_sppd ? ' AND id != %d' : ''),
                    $id_data_sppd ? [$postData['nomorSppd'], $postData['tahun_anggaran'], $id_data_sppd] : [$postData['nomorSppd'], $postData['tahun_anggaran']]
                ));

                if (!empty($nomor_check_query)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Nomor surat sudah digunakan!';
                    die(json_encode($ret));
                }

                // incoming feature
                // cek postData[namaPegawai] sebelum masuk ke db, jika ada maka itu id pegawai
                // jika tidak ada maka itu nama pegawai custom

                $data = array(
                    'tahun_anggaran'        => sanitize_text_field($postData['tahun_anggaran']),
                    'nomor_spt'             => sanitize_text_field($postData['nomor_spt']),
                    'tgl_ttd_sppd'          => sanitize_text_field($postData['tanggalSppd']),
                    'nomor_sppd'            => sanitize_text_field($postData['nomorSppd']),
                    'id_pegawai'            => sanitize_text_field($postData['']),
                    'nama_pegawai'          => sanitize_text_field($postData['namaPegawai']),
                    'nip_pegawai'           => sanitize_text_field($postData['nipPegawai']),
                    'pangkat_gol_pegawai'   => sanitize_text_field($postData['golPegawai']),
                    'jabatan_pegawai'       => sanitize_text_field($postData['jabatanPegawai']),
                    'tempat_berangkat'      => sanitize_text_field($postData['tempatBerangkat']),
                    'tgl_berangkat'         => sanitize_text_field($postData['tanggalBerangkat']),
                    'tempat_tujuan'         => sanitize_text_field($postData['tempatTujuan']),
                    'tgl_sampai'            => sanitize_text_field($postData['tanggalSampai']),
                    'tgl_kembali'           => sanitize_text_field($postData['tanggalKembali']),
                    'alat_angkut'           => sanitize_text_field($postData['alatAngkut']),
                    'maksud_sppd'           => sanitize_text_field($postData['maksudSppd']),
                    'keterangan'            => sanitize_text_field($postData['keterangan']),
                    'active'                => 1
                );

                // Update or insert
                if ($id_data_sppd) {
                    $wpdb->update(
                        'data_pegawai_spt_sppd',
                        $data,
                        array('id' => $id_data_sppd)
                    );
                    $ret['message'] = 'Berhasil update data!';
                } else {
                    $wpdb->insert(
                        'data_pegawai_spt_sppd',
                        $data
                    );
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }

    function get_datatable_data_spt()
    {
        global $wpdb;

        $ret = [
            'status' => 'success',
            'message' => 'Berhasil get data!'
        ];

        if (!empty($_POST['api_key']) && $_POST['api_key'] === get_option(WPSIPD_API_KEY)) {
            $params = $_REQUEST;

            // Define columns
            $columns = [
                'id',
                'nomor_spt',
                'dasar_spt',
                'tujuan_spt',
                'tgl_spt',
                'id_ka_opd',
                'id_skpd',
                'tahun_anggaran',
                'active',
                'created_at',
                'update_at',
            ];

            $where = 'WHERE 1=1 AND active = 1';
            $searchValue = !empty($params['search']['value']) ? $params['search']['value'] : '';

            if (!empty($_POST['id_skpd'])) {
                $where .= $wpdb->prepare(' AND id_skpd=%s', $_POST['id_skpd']);
            }

            // Search filter
            if ($searchValue) {
                $where .= $wpdb->prepare(
                    " AND (nomor_spt LIKE %s OR dasar_spt LIKE %s OR tujuan_spt LIKE %s)",
                    "%$searchValue%",
                    "%$searchValue%",
                    "%$searchValue%"
                );
            }

            // Total records
            $sqlTot = "SELECT COUNT(id) as jml FROM data_spt_sppd $where";
            $totalRecords = $wpdb->get_var($sqlTot);

            // Sorting
            $orderBy = '';
            if (!empty($params['order'])) {
                $orderByColumnIndex = $params['order'][0]['column'];
                $orderByDirection = strtoupper($params['order'][0]['dir']);
                if ($orderByDirection === 'ASC' || $orderByDirection === 'DESC') {
                    $orderByColumn = $columns[$orderByColumnIndex] ?? 'id';
                    $orderBy = "ORDER BY $orderByColumn $orderByDirection";
                }
            }

            // Pagination
            $limit = '';
            if ($params['length'] != -1) {
                $limit = $wpdb->prepare(
                    "LIMIT %d, %d",
                    $params['start'],
                    $params['length']
                );
            }

            // Query records
            $sqlRec = "SELECT " . implode(', ', $columns) . " FROM data_spt_sppd $where $orderBy $limit";
            $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

            // Format data
            foreach ($queryRecords as $record => $recVal) {
                $spt_page = $this->generatePage(
                    'Cetak SPT (Surat Perintah Tugas)',
                    $recVal['tahun_anggaran'],
                    '[cetak_spt_sppd]',
                    false
                );
                $skpd = $wpdb->get_var(
                    $wpdb->prepare('
                        SELECT nama_skpd
                        FROM data_unit
                        WHERE id_skpd = %d
                          AND tahun_anggaran = %d
                          AND active = 1
                    ', $recVal['id_skpd'], $recVal['tahun_anggaran'])
                );

                $btn = '<a style="margin-left: 2px;" class="btn btn-sm btn-warning" onclick="edit_spt(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                $btn .= '<a style="margin-left: 2px;" class="btn btn-sm btn-danger" onclick="hapus_spt(\'' . $recVal['id'] . '\'); return false;" href="#" title="Delete Data"><i class="dashicons dashicons-trash"></i></a>';
                $btn .= '<a style="margin-left: 2px;" class="btn btn-sm btn-success" onclick="show_modal_pegawai_spt_sppd(\'' . $recVal['nomor_spt'] . '\'); return false;" href="#" title="Tambah Pegawai"><i class="dashicons dashicons-insert"></i></a>';
                $btn .= '<a style="margin-left: 2px;" class="btn btn-sm btn-info" onclick="cetak_spt(\'' . $spt_page . '&id_spt=' . $recVal['id'] . '&tahun_anggaran=' . $recVal['tahun_anggaran'] . '\'); return false;" href="#" title="Cetak SPT"><i class="dashicons dashicons-printer"></i></a>';

                $queryRecords[$record]['id_skpd'] = $skpd;
                $queryRecords[$record]['aksi'] = $btn;
            }

            $json_data = [
                "draw" => intval($params['draw']),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($totalRecords),
                "data" => $queryRecords
            ];
            die(json_encode($json_data));
        } else {
            $ret = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($ret));
    }

    function get_data_spt_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                $ret['data'] = $wpdb->get_row(
                    $wpdb->prepare('
						SELECT 
							*
						FROM data_spt_sppd
						WHERE id=%d
                	', $_POST['id']),
                    ARRAY_A
                );
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

    function get_data_sppd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                $ret['data'] = $wpdb->get_row(
                    $wpdb->prepare('
						SELECT 
							*
						FROM data_pegawai_spt_sppd
						WHERE id=%d
                	', $_POST['id']),
                    ARRAY_A
                );
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

    function hapus_data_spt_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!'
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                // Validasi roles
                $user_data = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_sppd();
                if (empty(array_intersect($allowed_roles, $user_data->roles))) {
                    return array(
                        'status'  => 'error',
                        'message' => 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!'
                    );
                }

                // Ambil nomor SPT berdasarkan ID
                $nomor_spt = $wpdb->get_var(
                    $wpdb->prepare(
                        'SELECT nomor_spt 
                         FROM data_spt_sppd 
                         WHERE id = %d 
                           AND active = 1',
                        $_POST['id']
                    )
                );

                if (!$nomor_spt) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Nomor SPT tidak ditemukan atau sudah dihapus!';
                    die(json_encode($ret));
                }

                // Cek dan hapus data terkait di pegawai
                $affected_rows_pegawai = $wpdb->update(
                    'data_pegawai_spt_sppd',
                    array('active' => 0),
                    array(
                        'nomor_spt' => $nomor_spt,
                        'active'    => 1
                    )
                );

                // Hapus data di tabel spt
                $affected_rows_spt = $wpdb->update(
                    'data_spt_sppd',
                    array('active' => 0),
                    array(
                        'id' => $_POST['id']
                    )
                );

                if ($affected_rows_spt === false || $affected_rows_pegawai === false) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Terjadi kesalahan saat menghapus data!';
                } else {
                    $ret['message'] = 'Berhasil hapus data!';
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'API key tidak ditemukan!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }


    function hapus_data_sppd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                //roles validation
                $user_data = wp_get_current_user();
                $allowed_roles = $this->allowed_roles_sppd();
                if (empty(array_intersect($allowed_roles, $user_data->roles))) {
                    return array(
                        'status'  => 'error',
                        'message' => 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!'
                    );
                }
                $ret['data'] = $wpdb->update(
                    'data_pegawai_spt_sppd',
                    array('active' => 0),
                    array(
                        'id' => $_POST['id']
                    )
                );
            } else {
                $ret['status']    = 'error';
                $ret['message']    = 'Api key tidak ditemukan!';
            }
        } else {
            $ret['status']    = 'error';
            $ret['message']    = 'Format Salah!';
        }

        die(json_encode($ret));
    }

    function get_table_pegawai_spt_sppd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get table!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                if (!empty($_POST['nomor_spt'])) {
                    $nomor_spt = $_POST['nomor_spt'];
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Nomor SPT kosong!';
                    die(json_encode($ret));
                }
                $data_pegawai_spt_sppd = $wpdb->get_results(
                    $wpdb->prepare("
						SELECT * 
						FROM data_pegawai_spt_sppd
						WHERE nomor_spt = %s
						  AND active = 1
						", $nomor_spt),
                    ARRAY_A
                );
                $tbody = '';
                $counter = 1;
                if (!empty($data_pegawai_spt_sppd)) {
                    foreach ($data_pegawai_spt_sppd as $data) {
                        $sppd_page = $this->generatePage(
                            'Cetak SPPD (Surat Perintah Perjalanan Dinas)',
                            $data['tahun_anggaran'],
                            '[cetak_sppd]',
                            false
                        );

                        $btn = '<button class="btn btn-sm btn-primary m-1" onclick="edit_sppd(\'' . $data['id'] . '\', true); return false;" href="#" title="Detail SPPD"><span class="dashicons dashicons-search"></span></button>';
                        $btn .= '<button class="btn btn-sm btn-warning m-1" onclick="edit_sppd(\'' . $data['id'] . '\', false); return false;" href="#" title="Edit SPPD"><span class="dashicons dashicons-edit"></span></button>';
                        $btn .= '<button class="btn btn-sm btn-danger m-1" onclick="hapus_sppd(\'' . $data['id'] . '\'); return false;" href="#" title="Hapus SPPD"><span class="dashicons dashicons-no-alt"></span></button>';
                        $btn .= '<button class="btn btn-sm btn-info m-1" onclick="cetak_sppd(\'' . $sppd_page . '&id_sppd=' . $data['id'] . '&tahun_anggaran=' . $data['tahun_anggaran'] . '\'); return false;" href="#" title="Cetak SPPD"><span class="dashicons dashicons-printer"></span></button>';

                        $tbody .= '<tr>';
                        $tbody .= '<td class="text-center">' . $counter++ . '</td>';

                        // incoming feature
                        // cek id pegawai apakah ada di tabel pegawai
                        // jika tidak ada maka tampilkan nama custom
                        $tbody .= '<td class="text-left">' . $data['nama_pegawai'] . '</td>';
                        $tbody .= '<td class="text-center">' . $data['jabatan_pegawai'] . '</td>';

                        $tbody .= '<td class="text-center">' . $data['tempat_berangkat'] . '</td>';
                        $tbody .= '<td class="text-center">' . $data['tempat_tujuan'] . '</td>';
                        $tbody .= '<td class="text-center">' . $btn . '</td>';
                        $tbody .= '</tr>';
                    }
                } else {
                    $tbody .= "<tr><td colspan='6' class='text-center'>Tidak ada data tersedia</td></tr>";
                }
                $ret['data'] = $tbody;
            } else {
                $ret = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $ret = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($ret));
    }

    function allowed_roles_sppd()
    {
        return array(
            'administrator',
            'PA',
            'KPA',
            'PLT',
            'PLH',
            'pptk'
        );
    }

    function user_authorization_wpsipd($page_type = '', $tahun_anggaran = '')
    {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return array(
                'status'  => 'error',
                'message' => 'Anda belum login!'
            );
        } else {
            global $wpdb;
            $user_data = wp_get_current_user();
        }

        // should not empty
        if (empty($page_type) || empty($tahun_anggaran)) {
            return array(
                'status'  => 'error',
                'message' => 'Parameter tidak sesuai!'
            );
        }

        // Page-type-based authorization logic
        switch ($page_type) {
            case 'sppd':
                // Default return structure
                $ret = array(
                    'status'    => 'error',
                    'message'   => 'User tidak diijinkan!',
                    'options'   => '',
                    'id_skpd'   => '',
                    'nama_skpd' => '',
                    'role'      => implode($user_data->roles),
                    'pegawai'   => ''
                );

                // Roles validation
                $allowed_roles = $this->allowed_roles_sppd();
                if (empty(array_intersect($allowed_roles, $user_data->roles))) {
                    $ret['message'] = 'Akses ditolak - hanya pengguna dengan peran tertentu yang dapat mengakses fitur ini!';
                    return $ret;
                }

                if (in_array('administrator', $user_data->roles)) {
                    //data skpd
                    $data_skpd = $wpdb->get_results(
                        $wpdb->prepare('
                            SELECT
                                id_skpd,
                                kode_skpd,
                                nama_skpd
                            FROM data_unit
                            WHERE active = 1
                              AND tahun_anggaran = %d
                              AND active = 1
                            ORDER BY kode_skpd ASC
                        ', $tahun_anggaran),
                        ARRAY_A
                    );
                    // $list_pegawai = array(
                    //     '01' => array(
                    //         'nama'    => 'Lorem Ipsum, S.KOM',
                    //         'nip'     => '190190191091019',
                    //         'pangkat' => 'Pelaku/VVA',
                    //         'jabatan' => 'Pemimpn Badan'
                    //     ),
                    //     '02' => array(
                    //         'nama'    => 'dolor sit amet, S.Sos',
                    //         'nip'     => '190190191091019',
                    //         'pangkat' => 'Pelaku/VVA',
                    //         'jabatan' => 'Sekretaris Badan'
                    //     ),
                    //     '03' => array(
                    //         'nama'    => 'consectetur adipisicing elit, S.H',
                    //         'nip'     => '190190191091019',
                    //         'pangkat' => 'Pelaku/VVA',
                    //         'jabatan' => 'Staff Badan'
                    //     )
                    // );

                    if (!empty($data_skpd)) {
                        $options = '<option value="">Pilih SKPD</option>';
                        foreach ($data_skpd as $data) {
                            $options .= '<option value="' . $data['id_skpd'] . '">' . $data['kode_skpd'] . ' ' . strtoupper($data['nama_skpd']) . '</option>';
                        }

                        $ret['status']  = 'success';
                        $ret['message'] = 'Access Granted! Welcome Administrator!';
                        $ret['options'] = $options;
                        $ret['pegawai'] = '';
                        return $ret;
                    } else {
                        $ret['message'] = 'Data SKPD tidak ditemukan!';
                        return $ret;
                    }
                } else if (
                    in_array('PA', $user_data->roles)
                    || in_array('KPA', $user_data->roles)
                    || in_array('PLT', $user_data->roles)
                    || in_array('PLH', $user_data->roles)
                ) {
                    $nipkepala = $user_data->user_login;
                    $data_skpd = $wpdb->get_row(
                        $wpdb->prepare('
                            SELECT
                                id_skpd,
                                kode_skpd,
                                nama_skpd
                            FROM data_unit
                            WHERE active = 1
                              AND tahun_anggaran = %d
                              AND nipkepala = %d
                              AND active = 1
                        ', $tahun_anggaran, $nipkepala),
                        ARRAY_A
                    );

                    if (!empty($data_skpd)) {
                        $options = '<option value="' . $data_skpd['id_skpd'] . '" selected>' . $data_skpd['kode_skpd'] . ' ' . strtoupper($data_skpd['nama_skpd']) . '</option>';

                        $ret['status']    = 'success';
                        $ret['message']   = 'Access Granted! Welcome SKPD!';
                        $ret['options']   = $options;
                        $ret['id_skpd']   = $data_skpd['id_skpd'];
                        $ret['nama_skpd'] = $data_skpd['nama_skpd'];
                        return $ret;
                    } else {
                        $ret['message'] = 'Data SKPD tidak ditemukan!';
                        return $ret;
                    }
                } else {
                    return $ret; // Default error message
                }
                break;

            default:
                return array(
                    'status'  => 'error',
                    'message' => 'Tipe page tidak valid!'
                );
        }

        return;
    }

    function get_data_spt_by_nomor()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                $data_spt = $wpdb->get_row(
                    $wpdb->prepare('
						SELECT 
							id_skpd
						FROM data_spt_sppd
						WHERE nomor_spt = %s
                          AND tahun_anggaran = %d
                          AND active = 1
                	', $_POST['nomor_spt'], $_POST['tahun_anggaran']),
                    ARRAY_A
                );
                if (!empty($data_spt['id_skpd'])) {
                    $data_skpd = $wpdb->get_row(
                        $wpdb->prepare("
                            SELECT 
                                nama_skpd,
                                namakepala,
                                nipkepala,
                                pangkatkepala
                            FROM data_unit 
                            WHERE id_skpd = %d 
                              AND tahun_anggaran = %d
                              AND active = 1
                        ", $data_spt['id_skpd'], $_POST['tahun_anggaran']),
                        ARRAY_A
                    );
                    $ret['data'] = $data_skpd;
                } else {
                    $ret['status']  = 'error';
                    $ret['message'] = 'SKPD tidak ditemukan!';
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

    function get_data_sppd_by_nomor()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array(
                'skpd' => '',
                'html' => ''
            )
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                $data_spt = $wpdb->get_row(
                    $wpdb->prepare('
						SELECT 
							id_skpd
						FROM data_spt_sppd
						WHERE nomor_spt = %s
                          AND tahun_anggaran = %d
                          AND active = 1
                	', $_POST['nomor_spt'], $_POST['tahun_anggaran']),
                    ARRAY_A
                );
                if (!empty($data_spt['id_skpd'])) {
                    $data_skpd = $wpdb->get_row(
                        $wpdb->prepare("
                            SELECT 
                                nama_skpd,
                                namakepala,
                                nipkepala,
                                pangkatkepala
                            FROM data_unit 
                            WHERE id_skpd = %d 
                              AND tahun_anggaran = %d
                              AND active = 1
                        ", $data_spt['id_skpd'], $_POST['tahun_anggaran']),
                        ARRAY_A
                    );
                    $ret['data']['skpd'] = $data_skpd;
                } else {
                    $ret['status']  = 'error';
                    $ret['message'] = 'SKPD tidak ditemukan!';
                    die(json_encode($ret));
                }
                $data_sppd = $wpdb->get_results(
                    $wpdb->prepare('
                        SELECT 
                            *
                        FROM data_pegawai_spt_sppd
                        WHERE nomor_spt = %s
                          AND tahun_anggaran = %d
                          AND active = 1
                	', $_POST['nomor_spt'], $_POST['tahun_anggaran']),
                    ARRAY_A
                );
                $html = '';
                $no = 1;
                foreach ($data_sppd as $v) {
                    $html .= '<table>
                            <tr>
                                <td style="width: 5%;">' . $no++ . '</td>
                                <td style="width: 25%;">Nama</td>
                                <td>: <span contenteditable="true">' . $v['nama_pegawai'] . '</span></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>NIP</td>
                                <td>: <span contenteditable="true">' . $v['nip_pegawai'] . '</span></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Pangkat/Gol.Ruang</td>
                                <td>: <span contenteditable="true">' . $v['pangkat_gol_pegawai'] . '</span></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Jabatan</td>
                                <td>: <span contenteditable="true">' . $v['jabatan_pegawai'] . '</span></td>
                            </tr>
                        </table>';
                }

                $ret['data']['html'] = $html;
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

    function tambah_label_rinci_bl()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil tag rincian belanja!'
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                $user_data = wp_get_current_user();
                $postData = $_POST;

                // Define validation rules
                $validationRules = [
                    'tahun_anggaran'      => 'required|numeric',
                    'id_label'            => 'required',
                    'kode_sbl'            => 'required'
                ];

                // Validate data
                $errors = $this->validate($postData, $validationRules);

                if (!empty($errors)) {
                    $ret['status'] = 'error';
                    $ret['message'] = implode(" \n ", $errors);
                    die(json_encode($ret));
                }

                // Decode rincian_belanja_ids JSON to array
                $rincianBelanjaIds = json_decode(stripslashes($postData['rincian_belanja_ids']), true);

                if (empty($rincianBelanjaIds) || !is_array($rincianBelanjaIds)) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Format rincian belanja tidak valid!';
                    die(json_encode($ret));
                }

                //check rka for checked feature
                $rka = $wpdb->get_results(
                    $wpdb->prepare("
                        SELECT 
                            id_rinci_sub_bl
                        FROM data_rka
                        WHERE tahun_anggaran = %d
                          AND kode_sbl = %s
                          AND kode_akun != ''
                          AND active = 1
                    ", $postData['tahun_anggaran'], $postData['kode_sbl']),
                    ARRAY_A
                );

                // delete first, then update
                if (!empty($rka)) {
                    foreach ($rka as $v) {
                        $wpdb->update(
                            'data_mapping_label',
                            array('active' => 0),
                            array(
                                'id_rinci_sub_bl'   => $v['id_rinci_sub_bl'],
                                'tahun_anggaran'    => $postData['tahun_anggaran'],
                                'id_label_komponen' => $postData['id_label']
                            )
                        );
                    }
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Rincian tidak ditemukan dalam data rka!';
                    die(json_encode($ret));
                }

                //update
                foreach ($rincianBelanjaIds as $idRinciSubBl) {
                    $data = array(
                        'tahun_anggaran'    => sanitize_text_field($postData['tahun_anggaran']),
                        'id_rinci_sub_bl'   => sanitize_text_field($idRinciSubBl),
                        'id_label_komponen' => sanitize_text_field($postData['id_label']),
                        'user'              => $user_data->display_name,
                        'active'            => 1,
                    );

                    $cek_data = $wpdb->get_var(
                        $wpdb->prepare(
                            'SELECT id 
                             FROM data_mapping_label 
                             WHERE id_rinci_sub_bl = %d 
                               AND tahun_anggaran = %d 
                               AND id_label_komponen = %d 
                               AND active = 1',
                            $idRinciSubBl,
                            $postData['tahun_anggaran'],
                            $postData['id_label']
                        )
                    );

                    // Update or insert
                    if ($cek_data) {
                        $wpdb->update(
                            'data_mapping_label',
                            $data,
                            array('id' => $cek_data)
                        );
                    } else {
                        $wpdb->insert(
                            'data_mapping_label',
                            $data
                        );
                    }
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'API key tidak ditemukan!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format salah!';
        }

        die(json_encode($ret));
    }

    function simpan_realisasi_rinci_bl()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan realisasi rincian belanja!'
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                $user_data = wp_get_current_user();
                $postData = $_POST;

                // Define validation rules
                $validationRules = [
                    'tahun_anggaran'      => 'required|numeric',
                    'id_rinci_sub_bl'     => 'required',
                ];

                // Validate data
                $errors = $this->validate($postData, $validationRules);

                if (!empty($errors)) {
                    $ret['status'] = 'error';
                    $ret['message'] = implode(" \n ", $errors);
                    die(json_encode($ret));
                }

                $data = array(
                    'tahun_anggaran'    => sanitize_text_field($postData['tahun_anggaran']),
                    'id_rinci_sub_bl'   => sanitize_text_field($postData['id_rinci_sub_bl']),
                    'realisasi'         => sanitize_text_field($postData['realisasi']),
                    'user'              => $user_data->display_name,
                    'active'            => 1,
                );
                $cek_data = $wpdb->get_var(
                    $wpdb->prepare('
                        SELECT id
                        FROM data_realisasi_rincian
                        WHERE id_rinci_sub_bl = %d
                          AND tahun_anggaran = %d
                          AND active = 1
                    ', $postData['id_rinci_sub_bl'], $postData['tahun_anggaran'])
                );

                // Update or insert
                if ($cek_data) {
                    $wpdb->update(
                        'data_realisasi_rincian',
                        $data,
                        array('id' => $cek_data)
                    );
                } else {
                    $wpdb->insert(
                        'data_realisasi_rincian',
                        $data
                    );
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'API key tidak ditemukan!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format salah!';
        }

        die(json_encode($ret));
    }

    function hapus_rincian_from_label_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus rincian dari label!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                if ($_POST['is_deleted'] == true) {
                    $rows_affected = $wpdb->update(
                        'data_mapping_label',
                        array(
                            'active' => 2,
                            'keterangan_hapus' => $_POST['keterangan_hapus']
                        ),
                        array(
                            'active' => 1,
                            'id_rinci_sub_bl' => $_POST['id_rincian'],
                            'tahun_anggaran' => $_POST['tahun_anggaran'],
                            'id_label_komponen' => $_POST['id_label']
                        )
                    );

                    // Periksa apakah ada baris yang terpengaruh
                    if ($rows_affected === false) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Terjadi kesalahan pada query database.';
                    } else {
                        $ret['data'] = $rows_affected;
                    }
                } else {
                    $rows_affected = $wpdb->update(
                        'data_mapping_label',
                        array('active' => 0),
                        array(
                            'id_rinci_sub_bl' => $_POST['id_rincian'],
                            'tahun_anggaran' => $_POST['tahun_anggaran'],
                            'id_label_komponen' => $_POST['id_label']
                        )
                    );

                    // Periksa apakah ada baris yang terpengaruh
                    if ($rows_affected === false) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Terjadi kesalahan pada query database.';
                    } else {
                        $ret['data'] = $rows_affected;
                    }
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'API key tidak ditemukan!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }

    function get_label_by_rinci()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Data label berhasil diambil!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                // Ambil data mapping label
                $labels = $wpdb->get_results(
                    $wpdb->prepare('
                        SELECT id_label_komponen, volume, realisasi, pisah
                        FROM data_mapping_label
                        WHERE id_rinci_sub_bl = %d
                          AND tahun_anggaran = %d
                          AND active = 1
                        ', $_POST['id_rinci_sub_bl'], $_POST['tahun_anggaran']),
                    ARRAY_A
                );

                if (!empty($labels)) {
                    foreach ($labels as $v) {
                        // Ambil nama label dari tabel data_label_komponen
                        $label = $wpdb->get_row(
                            $wpdb->prepare('
                                SELECT nama
                                FROM data_label_komponen
                                WHERE id = %d
                                  AND tahun_anggaran = %d
                                  AND active = 1
                                ', $v['id_label_komponen'], $_POST['tahun_anggaran']),
                            ARRAY_A
                        );

                        if ($label) {
                            $ret['data'][] = array(
                                'nama' => $label['nama'],
                                'volume' => $v['volume'],
                                'realisasi' => $v['realisasi'],
                                'pisah' => $v['pisah']
                            );
                        }
                    }
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Tidak ada data label yang ditemukan!';
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'API key tidak valid!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format request salah!';
        }

        die(json_encode($ret));
    }

    function restore_rincian_by_id_rinci() {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil kembalikan arsip!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                $rows_affected = $wpdb->update(
                    'data_mapping_label',
                    array(
                        'active' => 1,
                        'keterangan_hapus' => ''
                    ),
                    array(
                        'id_rinci_sub_bl' => $_POST['id_rincian'],
                        'tahun_anggaran' => $_POST['tahun_anggaran'],
                        'id_label_komponen' => $_POST['id_label']
                    )
                );

                // Periksa apakah ada baris yang terpengaruh
                if ($rows_affected === false) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Terjadi kesalahan pada query database.';
                } else {
                    $ret['data'] = $rows_affected;
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'API key tidak ditemukan!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }

    function simpan_pisah_rinci_bl() {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan pisah rincian!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
                $user_data = wp_get_current_user();
                $data = array(
                    'volume_pisah'      => sanitize_text_field($_POST['volume']),
                    'realisasi_pisah'   => sanitize_text_field($_POST['realisasi']),
                    'tahun_anggaran'    => sanitize_text_field($_POST['tahun_anggaran']),
                    'id_rinci_sub_bl'   => sanitize_text_field($_POST['id_rinci_sub_bl']),
                    'id_label_komponen' => sanitize_text_field($_POST['id_label']),
                    'user'              => $user_data->display_name,
                    'pisah'             => 1,
                    'active'            => 1
                );

                $current_data = $wpdb->get_var(
                    $wpdb->prepare('
                        SELECT id
                        FROM data_mapping_label
                        WHERE id_label_komponen = %d
                          AND id_rinci_sub_bl = %d
                          AND tahun_anggaran = %d
                          AND active = 1
                    ', $_POST['id_label'], $_POST['id_rinci_sub_bl'], $_POST['tahun_anggaran'])
                );

                // Update or insert
                if ($current_data) {
                    $wpdb->update(
                        'data_mapping_label',
                        $data,
                        array('id' => $current_data)
                    );
                } else {
                    $wpdb->insert(
                        'data_mapping_label',
                        $data
                    );
                }
            } else {
                $ret['status'] = 'error';
                $ret['message'] = 'API key tidak ditemukan!';
            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }
}
