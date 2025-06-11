<?php

require_once WPSIPD_PLUGIN_PATH . "/public/class-wpsipd-public-rka.php";

class Wpsipd_Public_Keu_Pemdes extends Wpsipd_Public_RKA
{

    public function keu_pemdes_bhpd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bhpd.php';
    }

    public function keu_pemdes_bhrd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bhrd.php';
    }

    public function laporan_keu_pemdes_per_kecamatan($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-laporan-per-kecamatan.php';
    }

    public function keu_pemdes_bku_add($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bku-add.php';
    }

    public function keu_pemdes_beranda($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-beranda.php';
    }

    public function keu_pemdes_bku_dd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bku-dd.php';
    }

    public function management_data_bkk_infrastruktur($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-management-bkk-infrastruktur.php';
    }

    public function keu_pemdes_bkk_pilkades($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bkk-pilkades.php';
    }

    public function keu_pemdes_bkk_inf($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bkk-infrastruktur.php';
    }

    public function monitor_keu_pemdes($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-desa.php';
    }

    public function management_data_bhpd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-management-bhpd.php';
    }

    public function management_data_bhrd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-management-bhrd.php';
    }

    public function management_data_bku_dd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-management-bku-dd.php';
    }

    public function management_data_bku_add($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-management-bku-add.php';
    }

    public function management_data_bkk_pilkades($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-management-bkk-pilkades.php';
    }

    public function input_pencairan_bkk($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bkk.php';
    }

    public function input_pencairan_bhpd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bhpd.php';
    }

    public function input_pencairan_bhrd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bhrd.php';
    }

    public function input_pencairan_bku_dd($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bku-dd.php';
    }

    public function input_pencairan_bku_add($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bku-add.php';
    }

    public function input_pencairan_bkk_pilkades($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bkk-pilkades.php';
    }

    public function get_data_bkk_infrastruktur_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->get_row($wpdb->prepare('
                    SELECT 
                        *
                    FROM data_bkk_desa
                    WHERE id=%d
                ', $_POST['id']), ARRAY_A);
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

    public function hapus_data_bkk_infrastruktur_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                // Menggunakan API key yang sesuai
                $idToDelete = $_POST['id'];

                // Periksa apakah data dengan ID yang akan dihapus ada di tabel data_pencairan_bkk_desa
                $idInOtherTable = $wpdb->get_var($wpdb->prepare('
                    SELECT id_kegiatan
                    FROM data_pencairan_bkk_desa
                    WHERE id_kegiatan=%d
                ', $idToDelete));
                if ($idInOtherTable) {
                    // Jika data dengan ID yang sama ditemukan di tabel lain, tampilkan pesan
                    $ret['status'] = 'confirm';
                    $ret['message'] = 'Data dengan ID = ' . $idInOtherTable . ' sudah pernah dicairkan.';
                } else {
                    // Jika tidak ada data dengan ID yang sama di tabel lain, lanjutkan penghapusan seperti biasa
                    $ret['data'] = $wpdb->update('data_bkk_desa', array('active' => 0), array(
                        'id' => $idToDelete
                    ));
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

    public function tambah_data_bkk_infrastruktur()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id_desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id desa tidak boleh kosong!';
                } else if (empty($_POST['id_kec'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id kecamatan tidak boleh kosong!';
                } else if (empty($_POST['desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data desa tidak boleh kosong!';
                } else if (empty($_POST['kecamatan'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data kecamatan tidak boleh kosong!';
                } else if (empty($_POST['kegiatan'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data kegiatan tidak boleh kosong!';
                } else if (empty($_POST['alamat'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data alamat tidak boleh kosong!';
                } else if (empty($_POST['total'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data total tidak boleh kosong!';
                } else if (empty($_POST['id_dana'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id_dana tidak boleh kosong!';
                } else if (empty($_POST['sumber_dana'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data sumber_dana tidak boleh kosong!';
                } else {
                    $tahun_anggaran = $_POST['tahun_anggaran'];
                    $total = $_POST['total'];
                    $id_kecamatan = $_POST['id_kec'];
                    $kecamatan = $_POST['kecamatan'];
                    $desa = $_POST['desa'];
                    $kegiatan = $_POST['kegiatan'];
                    $alamat = $_POST['alamat'];
                    $id_dana = $_POST['id_dana'];
                    $sumber_dana = $_POST['sumber_dana'];
                    $id_desa = $_POST['id_desa'];
                    $data = array(
                        'id_desa' => $id_desa,
                        'id_kecamatan' => $id_kecamatan,
                        'desa' => $desa,
                        'kecamatan' => $kecamatan,
                        'total' => $total,
                        'tahun_anggaran' => $tahun_anggaran,
                        'kegiatan' => $kegiatan,
                        'alamat' => $alamat,
                        'id_dana' => $id_dana,
                        'sumber_dana' => $sumber_dana,
                        'active' => 1,
                        'update_at' => current_time('mysql')
                    );
                    if (!empty($_POST['id_data'])) {
                        $cek_id = $wpdb->get_row($wpdb->prepare("
                        SELECT 
                            id,
                            active
                        from data_bkk_desa 
                        where desa=%s 
                            and kecamatan=%s
                            and kegiatan=%s
                            and alamat=%s
                            and tahun_anggaran=%d
                            and id!=%d
                        ", $desa, $kecamatan, $kegiatan, $alamat, $tahun_anggaran, $_POST['id_data']), ARRAY_A);
                        if ($cek_id['active'] == 0) {
                            $wpdb->update('data_bkk_desa', $data, array(
                                'id' => $_POST['id_data']
                            ));
                            $ret['message'] = 'Berhasil update data!';
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal disimpan. Data bkk_infrastruktur dengan id_desa="' . $id_desa . '" kecamatan ="' . $kecamatan . '" kegiatan ="' . $kegiatan . '" alamat ="' . $alamat . '" tahun anggaran = "' . $tahun_anggaran . '"sudah ada!';
                        }
                    } else {
                        $cek_id = $wpdb->get_row($wpdb->prepare("
                        SELECT 
                            id,
                            active
                        from data_bkk_desa 
                        where desa=%s 
                            and kecamatan=%s
                            and kegiatan=%s
                            and alamat=%s
                            and tahun_anggaran=%d
                        ", $desa, $kecamatan, $kegiatan, $alamat, $tahun_anggaran), ARRAY_A);
                        if (empty($cek_id)) {
                            $wpdb->insert('data_bkk_desa', $data);
                        } else {
                            if ($cek_id['active'] == 0) {
                                $wpdb->update('data_bkk_desa', $data, array(
                                    'id' => $cek_id['id_desa']
                                ));
                            } else {
                                $ret['status'] = 'error';
                                $ret['message'] = 'Gagal disimpan. Data bkk_infrastruktur dengan id_desa="' . $id_desa . '" kecamatan ="' . $kecamatan . '" kegiatan ="' . $kegiatan . '" alamat ="' . $alamat . '" tahun anggaran = "' . $tahun_anggaran . '"sudah ada!';
                            }
                        }
                    }
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

    public function get_datatable_bkk_infrastruktur()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'id_kecamatan',
                    1 => 'id_desa',
                    2 => 'kecamatan',
                    3 => 'desa',
                    4 => 'kegiatan',
                    5 => 'alamat',
                    6 => 'total',
                    7 => 'id_dana',
                    8 => 'sumber_dana',
                    9 => 'tahun_anggaran',
                    10 => 'id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value;
                    $where .= " OR kegiatan LIKE " . $search_value;
                    $where .= " OR alamat LIKE " . $search_value . ")";
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bkk_desa`";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_bkk_desa`";
                $where_first = " WHERE 1=1 AND active = 1";
                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .= " ORDER BY update_at DESC" . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                    $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total'] = number_format($recVal['total'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            } else {
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }

    public function get_data_bhpd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->get_row($wpdb->prepare('
                    SELECT 
                        *
                    FROM data_bhpd_desa
                    WHERE id=%d
                ', $_POST['id']), ARRAY_A);
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

    public function hapus_data_bhpd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                // Menggunakan API key yang sesuai
                $idToDelete = $_POST['id'];

                // Periksa apakah data dengan ID yang akan dihapus ada di tabel data_pencairan_bhpd_desa
                $idInOtherTable = $wpdb->get_var($wpdb->prepare('
                    SELECT id_bhpd
                    FROM data_pencairan_bhpd_desa
                    WHERE id_bhpd=%d
                ', $idToDelete));
                if ($idInOtherTable) {
                    // Jika data dengan ID yang sama ditemukan di tabel lain, tampilkan pesan
                    $ret['status'] = 'confirm';
                    $ret['message'] = 'Data dengan ID = ' . $idInOtherTable . ' sudah pernah dicairkan.';
                } else {
                    // Jika tidak ada data dengan ID yang sama di tabel lain, lanjutkan penghapusan seperti biasa
                    $ret['data'] = $wpdb->update('data_bhpd_desa', array('active' => 0), array(
                        'id' => $idToDelete
                    ));
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

    public function tambah_data_bhpd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id_desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id desa tidak boleh kosong!';
                } else if (empty($_POST['id_kec'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id kecamatan tidak boleh kosong!';
                } else if (empty($_POST['desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data desa tidak boleh kosong!';
                } else if (empty($_POST['kecamatan'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data kecamatan tidak boleh kosong!';
                } else if (empty($_POST['total'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data total tidak boleh kosong!';
                } else {
                    $tahun_anggaran = $_POST['tahun_anggaran'];
                    $total = $_POST['total'];
                    $id_kecamatan = $_POST['id_kec'];
                    $kecamatan = $_POST['kecamatan'];
                    $desa = $_POST['desa'];
                    $id_desa = $_POST['id_desa'];
                    $data = array(
                        'id_desa' => $id_desa,
                        'desa' => $desa,
                        'kecamatan' => $kecamatan,
                        'id_kecamatan' => $id_kecamatan,
                        'total' => $total,
                        'tahun_anggaran' => $tahun_anggaran,
                        'active' => 1,
                        'update_at' => current_time('mysql')
                    );
                    if (!empty($_POST['id_data'])) {
                        $wpdb->update('data_bhpd_desa', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    } else {
                        $cek_id = $wpdb->get_row($wpdb->prepare('
                            SELECT
                                id_desa,
                                active
                            FROM data_bhpd_desa
                            WHERE id_desa=%s
                        ', $id_desa), ARRAY_A);
                        if (empty($cek_id)) {
                            $wpdb->insert('data_bhpd_desa', $data);
                        } else {
                            if ($cek_id['active'] == 0) {
                                $wpdb->update('data_bhpd_desa', $data, array(
                                    'id' => $cek_id['id_desa']
                                ));
                            } else {
                                $ret['status'] = 'error';
                                $ret['message'] = 'Gagal disimpan. Data bhpd_desa dengan id_desa="' . $id_desa . '" sudah ada!';
                            }
                        }
                    }
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

    public function get_pemdes_alamat()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $id_kab = get_option('_crb_id_lokasi_kokab');
                $all_kec = $wpdb->get_results($wpdb->prepare("
                    SELECT 
                        id_alamat as id_kec,
                        nama as kecamatan 
                    from data_alamat 
                    where tahun=%d 
                        and is_kec=1 
                        and id_kab=" . $id_kab . "
                ", $_POST['tahun_anggaran']), ARRAY_A);
                $data = $all_kec;
                foreach ($all_kec as $key => $kec) {
                    $desa = $wpdb->get_results($wpdb->prepare("
                        SELECT 
                            id_alamat as id_kel,
                            nama as desa
                        from data_alamat 
                        where tahun=%d
                            and is_kel=1 
                            and id_kab=" . $id_kab . " 
                            and id_kec=" . $kec['id_kec'] . "
                    ", $_POST['tahun_anggaran']), ARRAY_A);
                $ret['sql'] = $wpdb->last_query;
                    $data[$key]['desa'] = $desa;
                }
                // print_r($desa); die($wpdb->last_query);
                $ret['data'] = $data;
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

    public function get_pemdes_alamat_all()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'tipe' => 0,
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if(empty($_POST['tahun_anggaran'])){
                    $ret['status'] = 'error';
                    $ret['message'] = 'Tahun anggaran tidak boleh kosong!';
                }else{
                    $id_prov = get_option('_crb_id_lokasi_prov');
                    $id_kab = get_option('_crb_id_lokasi_kokab');
                    if(
                        empty($id_prov)
                        || empty($id_kab)
                    ){
                        $ret['status'] = 'error';
                        $ret['message'] = 'Settingan ID provinsi dan Kabupaten/Kota kosong. Harap hubungi admin!';
                    }else if(empty($id_kab)){
                        $ret['tipe'] = 1; // data desa di provinsi
                        $all_kab = $wpdb->get_results($wpdb->prepare("
                            SELECT 
                                id_alamat as id_kab,
                                nama as kabkot 
                            from data_alamat 
                            where tahun=%d 
                                and is_kab=1 
                                and id_prov=%d
                        ", $_POST['tahun_anggaran'], $id_prov), ARRAY_A);
                        $data = $all_kab;
                        foreach ($all_kab as $k => $kab) {
                            $all_kec = $wpdb->get_results($wpdb->prepare("
                                SELECT 
                                    id_alamat as id_kec,
                                    nama as kecamatan 
                                from data_alamat 
                                where tahun=%d 
                                    and is_kec=1 
                                    and id_kab=%d
                            ", $_POST['tahun_anggaran'], $id_kab), ARRAY_A);
                            $data[$k]['kecamatan'] = $all_kec;
                            foreach ($all_kec as $key => $kec) {
                                $desa = $wpdb->get_results($wpdb->prepare("
                                    SELECT 
                                        id_alamat as id_kel,
                                        nama as desa
                                    from data_alamat 
                                    where tahun=%d
                                        and is_kel=1 
                                        and id_kab=%d
                                        and id_kec=%d
                                ", $_POST['tahun_anggaran'], $id_kab, $kec['id_kec']), ARRAY_A);
                                $ret['sql'] = $wpdb->last_query;
                                $data[$k]['kecamatan'][$key]['desa'] = $desa;
                            }
                        }
                    }else{
                        $ret['tipe'] = 2; // data desa di kabkot
                        $all_kec = $wpdb->get_results($wpdb->prepare("
                            SELECT 
                                id_alamat as id_kec,
                                nama as kecamatan 
                            from data_alamat 
                            where tahun=%d 
                                and is_kec=1 
                                and id_kab=%d
                        ", $_POST['tahun_anggaran'], $id_kab), ARRAY_A);
                        $data = $all_kec;
                        foreach ($all_kec as $key => $kec) {
                            $desa = $wpdb->get_results($wpdb->prepare("
                                SELECT 
                                    id_alamat as id_kel,
                                    nama as desa
                                from data_alamat 
                                where tahun=%d
                                    and is_kel=1 
                                    and id_kab=%d
                                    and id_kec=%d
                            ", $_POST['tahun_anggaran'], $id_kab, $kec['id_kec']), ARRAY_A);
                            $ret['sql'] = $wpdb->last_query;
                            $data[$key]['desa'] = $desa;
                        }
                    }
                    // print_r($desa); die($wpdb->last_query);
                    $ret['data'] = $data;
                }
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

    public function get_datatable_bhpd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'id_kecamatan',
                    1 => 'id_desa',
                    2 => 'kecamatan',
                    3 => 'desa',
                    4 => 'total',
                    5 => 'tahun_anggaran',
                    6 => 'id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value . ")";
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bhpd_desa`";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_bhpd_desa`";
                $where_first = " WHERE 1=1 AND active = 1";
                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .= " ORDER BY update_at DESC" . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                    $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total'] = number_format($recVal['total'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            } else {
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }

    public function get_data_bhrd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->get_row($wpdb->prepare('
                    SELECT 
                        *
                    FROM data_bhrd_desa
                    WHERE id=%d
                ', $_POST['id']), ARRAY_A);
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

    public function hapus_data_bhrd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                // Menggunakan API key yang sesuai
                $idToDelete = $_POST['id'];

                // Periksa apakah data dengan ID yang akan dihapus ada di tabel data_pencairan_bhrd_desa
                $idInOtherTable = $wpdb->get_var($wpdb->prepare('
                    SELECT id_bhrd
                    FROM data_pencairan_bhrd_desa
                    WHERE id_bhrd=%d
                ', $idToDelete));
                if ($idInOtherTable) {
                    // Jika data dengan ID yang sama ditemukan di tabel lain, tampilkan pesan
                    $ret['status'] = 'confirm';
                    $ret['message'] = 'Data dengan ID = ' . $idInOtherTable . ' sudah pernah dicairkan.';
                } else {
                    // Jika tidak ada data dengan ID yang sama di tabel lain, lanjutkan penghapusan seperti biasa
                    $ret['data'] = $wpdb->update('data_bhrd_desa', array('active' => 0), array(
                        'id' => $idToDelete
                    ));
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

    public function tambah_data_bhrd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id_desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id desa tidak boleh kosong!';
                } else if (empty($_POST['id_kec'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id kecamatan tidak boleh kosong!';
                } else if (empty($_POST['desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data desa tidak boleh kosong!';
                } else if (empty($_POST['kecamatan'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data kecamatan tidak boleh kosong!';
                } else if (empty($_POST['total'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data total tidak boleh kosong!';
                } else {
                    $tahun_anggaran = $_POST['tahun_anggaran'];
                    $total = $_POST['total'];
                    $id_kecamatan = $_POST['id_kec'];
                    $kecamatan = $_POST['kecamatan'];
                    $desa = $_POST['desa'];
                    $id_desa = $_POST['id_desa'];
                    $data = array(
                        'id_desa' => $id_desa,
                        'desa' => $desa,
                        'kecamatan' => $kecamatan,
                        'id_kecamatan' => $id_kecamatan,
                        'total' => $total,
                        'tahun_anggaran' => $tahun_anggaran,
                        'active' => 1,
                        'update_at' => current_time('mysql')
                    );
                    if (!empty($_POST['id_data'])) {
                        $wpdb->update('data_bhrd_desa', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    } else {
                        $cek_id = $wpdb->get_row($wpdb->prepare('
                            SELECT
                                id_desa,
                                active
                            FROM data_bhrd_desa
                            WHERE id_desa=%s
                        ', $id_desa), ARRAY_A);
                        if (empty($cek_id)) {
                            $wpdb->insert('data_bhrd_desa', $data);
                        } else {
                            if ($cek_id['active'] == 0) {
                                $wpdb->update('data_bhrd_desa', $data, array(
                                    'id' => $cek_id['id_desa']
                                ));
                            } else {
                                $ret['status'] = 'error';
                                $ret['message'] = 'Gagal disimpan. Data bhrd_desa dengan id_bhrd_desa="' . $id_bhrd_desa . '" sudah ada!';
                            }
                        }
                    }
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

    public function get_datatable_bhrd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'id_kecamatan',
                    1 => 'id_desa',
                    2 => 'kecamatan',
                    3 => 'desa',
                    4 => 'total',
                    5 => 'tahun_anggaran',
                    6 => 'id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value . ")";
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bhrd_desa`";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_bhrd_desa`";
                $where_first = " WHERE 1=1 AND active = 1";
                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .= " ORDER BY update_at DESC" . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                    $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total'] = number_format($recVal['total'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
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

    public function get_pemdes_bkk()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (!empty($_POST['nama_kec'])) {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bkk_desa
                        WHERE tahun_anggaran=%d
                            AND kecamatan=%s
                    ", $_POST['tahun_anggaran'], $_POST['nama_kec']), ARRAY_A);
                } else {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bkk_desa
                        WHERE tahun_anggaran=%d
                    ", $_POST['tahun_anggaran']), ARRAY_A);
                }
                $ret['data'] = $data;
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

    public function get_data_bku_dd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->get_row($wpdb->prepare('
                    SELECT 
                        *
                    FROM data_bku_dd_desa
                    WHERE id=%d
                ', $_POST['id']), ARRAY_A);
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

    public function hapus_data_bku_dd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                // Menggunakan API key yang sesuai
                $idToDelete = $_POST['id'];

                // Periksa apakah data dengan ID yang akan dihapus ada di tabel data_pencairan_bku_dd_desa
                $idInOtherTable = $wpdb->get_var($wpdb->prepare('
                    SELECT id_bku_dd
                    FROM data_pencairan_bku_dd_desa
                    WHERE id_bku_dd=%d
                ', $idToDelete));
                if ($idInOtherTable) {
                    // Jika data dengan ID yang sama ditemukan di tabel lain, tampilkan pesan
                    $ret['status'] = 'confirm';
                    $ret['message'] = 'Data dengan ID = ' . $idInOtherTable . ' sudah pernah dicairkan.';
                } else {
                    // Jika tidak ada data dengan ID yang sama di tabel lain, lanjutkan penghapusan seperti biasa
                    $ret['data'] = $wpdb->update('data_bku_dd_desa', array('active' => 0), array(
                        'id' => $idToDelete
                    ));
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

    public function tambah_data_bku_dd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id_desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id desa tidak boleh kosong!';
                } else if (empty($_POST['id_kec'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id kecamatan tidak boleh kosong!';
                } else if (empty($_POST['desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data desa tidak boleh kosong!';
                } else if (empty($_POST['kecamatan'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data kecamatan tidak boleh kosong!';
                } else if (empty($_POST['total'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data total tidak boleh kosong!';
                } else if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data tahun anggaran tidak boleh kosong!';
                } else {
                    $tahun_anggaran = $_POST['tahun_anggaran'];
                    $total = $_POST['total'];
                    $id_kecamatan = $_POST['id_kec'];
                    $kecamatan = $_POST['kecamatan'];
                    $desa = $_POST['desa'];
                    $id_desa = $_POST['id_desa'];
                    $data = array(
                        'id_desa' => $id_desa,
                        'desa' => $desa,
                        'kecamatan' => $kecamatan,
                        'id_kecamatan' => $id_kecamatan,
                        'total' => $total,
                        'tahun_anggaran' => $tahun_anggaran,
                        'active' => 1,
                        'update_at' => current_time('mysql')
                    );
                    if (!empty($_POST['id_data'])) {
                        $wpdb->update('data_bku_dd_desa', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    } else {
                        $cek_id = $wpdb->get_row($wpdb->prepare('
                            SELECT
                                id_desa,
                                active
                            FROM data_bku_dd_desa
                            WHERE id_desa=%s
                        ', $id_desa), ARRAY_A);
                        if (empty($cek_id)) {
                            $wpdb->insert('data_bku_dd_desa', $data);
                        } else {
                            if ($cek_id['active'] == 0) {
                                $wpdb->update('data_bku_dd_desa', $data, array(
                                    'id' => $cek_id['id_desa']
                                ));
                            } else {
                                $ret['status'] = 'error';
                                $ret['message'] = 'Gagal disimpan. Data bku_dd_desa dengan id_desa="' . $id_desa . '" sudah ada!';
                            }
                        }
                    }
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

    public function get_datatable_bku_dd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'id_kecamatan',
                    1 => 'id_desa',
                    2 => 'kecamatan',
                    3 => 'desa',
                    4 => 'total',
                    5 => 'tahun_anggaran',
                    6 => 'id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value . ")";
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bku_dd_desa`";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_bku_dd_desa`";
                $where_first = " WHERE 1=1 AND active = 1";
                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .= " ORDER BY update_at DESC" . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                    $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total'] = number_format($recVal['total'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            } else {
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }

    public function get_data_bku_add_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->get_row($wpdb->prepare('
                    SELECT 
                        *
                    FROM data_bku_add_desa
                    WHERE id=%d
                ', $_POST['id']), ARRAY_A);
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

    public function hapus_data_bku_add_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                // Menggunakan API key yang sesuai
                $idToDelete = $_POST['id'];

                // Periksa apakah data dengan ID yang akan dihapus ada di tabel data_pencairan_bku_add_desa
                $idInOtherTable = $wpdb->get_var($wpdb->prepare('
                    SELECT id_bku_add
                    FROM data_pencairan_bku_add_desa
                    WHERE id_bku_add=%d
                ', $idToDelete));
                if ($idInOtherTable) {
                    // Jika data dengan ID yang sama ditemukan di tabel lain, tampilkan pesan
                    $ret['status'] = 'confirm';
                    $ret['message'] = 'Data dengan ID = ' . $idInOtherTable . ' sudah pernah dicairkan.';
                } else {
                    // Jika tidak ada data dengan ID yang sama di tabel lain, lanjutkan penghapusan seperti biasa
                    $ret['data'] = $wpdb->update('data_bku_add_desa', array('active' => 0), array(
                        'id' => $idToDelete
                    ));
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

    public function tambah_data_bku_add()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id_desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id desa tidak boleh kosong!';
                } else if (empty($_POST['id_kec'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id kecamatan tidak boleh kosong!';
                } else if (empty($_POST['desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data desa tidak boleh kosong!';
                } else if (empty($_POST['kecamatan'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data kecamatan tidak boleh kosong!';
                } else if (empty($_POST['total'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data total tidak boleh kosong!';
                } else if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data tahun anggaran tidak boleh kosong!';
                } else {
                    $tahun_anggaran = $_POST['tahun_anggaran'];
                    $total = $_POST['total'];
                    $id_kecamatan = $_POST['id_kec'];
                    $kecamatan = $_POST['kecamatan'];
                    $desa = $_POST['desa'];
                    $id_desa = $_POST['id_desa'];
                    $data = array(
                        'id_desa' => $id_desa,
                        'desa' => $desa,
                        'kecamatan' => $kecamatan,
                        'id_kecamatan' => $id_kecamatan,
                        'total' => $total,
                        'tahun_anggaran' => $tahun_anggaran,
                        'active' => 1,
                        'update_at' => current_time('mysql')
                    );
                    if (!empty($_POST['id_data'])) {
                        $wpdb->update('data_bku_add_desa', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    } else {
                        $cek_id = $wpdb->get_row($wpdb->prepare('
                            SELECT
                                id_desa,
                                active
                            FROM data_bku_add_desa
                            WHERE id_desa=%s
                        ', $id_desa), ARRAY_A);
                        if (empty($cek_id)) {
                            $wpdb->insert('data_bku_add_desa', $data);
                        } else {
                            if ($cek_id['active'] == 0) {
                                $wpdb->update('data_bku_add_desa', $data, array(
                                    'id' => $cek_id['id_desa']
                                ));
                            } else {
                                $ret['status'] = 'error';
                                $ret['message'] = 'Gagal disimpan. Data bku_add_desa dengan id_desa="' . $id_desa . '" sudah ada!';
                            }
                        }
                    }
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

    public function get_datatable_bku_add()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'id_kecamatan',
                    1 => 'id_desa',
                    2 => 'kecamatan',
                    3 => 'desa',
                    4 => 'total',
                    5 => 'tahun_anggaran',
                    6 => 'id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value . ")";
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bku_add_desa`";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_bku_add_desa`";
                $where_first = " WHERE 1=1 AND active = 1";
                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .= " ORDER BY update_at DESC" . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                    $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total'] = number_format($recVal['total'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            } else {
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }

    public function get_data_bkk_pilkades_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->get_row($wpdb->prepare('
                    SELECT 
                        *
                    FROM data_bkk_pilkades_desa
                    WHERE id=%d
                ', $_POST['id']), ARRAY_A);
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

    public function hapus_data_bkk_pilkades_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                // Menggunakan API key yang sesuai
                $idToDelete = $_POST['id'];

                // Periksa apakah data dengan ID yang akan dihapus ada di tabel data_pencairan_bkk_pilkades_desa
                $idInOtherTable = $wpdb->get_var($wpdb->prepare('
                    SELECT id_bkk_pilkades
                    FROM data_pencairan_bkk_pilkades_desa
                    WHERE id_bkk_pilkades=%d
                ', $idToDelete));
                if ($idInOtherTable) {
                    // Jika data dengan ID yang sama ditemukan di tabel lain, tampilkan pesan
                    $ret['status'] = 'confirm';
                    $ret['message'] = 'Data dengan ID = ' . $idInOtherTable . ' sudah pernah dicairkan.';
                } else {
                    // Jika tidak ada data dengan ID yang sama di tabel lain, lanjutkan penghapusan seperti biasa
                    $ret['data'] = $wpdb->update('data_bkk_pilkades_desa', array('active' => 0), array(
                        'id' => $idToDelete
                    ));
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

    public function tambah_data_bkk_pilkades()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (empty($_POST['id_desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id desa tidak boleh kosong!';
                } else if (empty($_POST['id_kec'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id kecamatan tidak boleh kosong!';
                } else if (empty($_POST['desa'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data desa tidak boleh kosong!';
                } else if (empty($_POST['kecamatan'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data kecamatan tidak boleh kosong!';
                } else if (empty($_POST['total'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data total tidak boleh kosong!';
                } else if (empty($_POST['tahun_anggaran'])) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data tahun anggaran tidak boleh kosong!';
                } else {
                    $tahun_anggaran = $_POST['tahun_anggaran'];
                    $total = $_POST['total'];
                    $id_kecamatan = $_POST['id_kec'];
                    $kecamatan = $_POST['kecamatan'];
                    $desa = $_POST['desa'];
                    $id_desa = $_POST['id_desa'];
                    $data = array(
                        'id_desa' => $id_desa,
                        'desa' => $desa,
                        'kecamatan' => $kecamatan,
                        'id_kecamatan' => $id_kecamatan,
                        'total' => $total,
                        'tahun_anggaran' => $tahun_anggaran,
                        'active' => 1,
                        'update_at' => current_time('mysql')
                    );
                    if (!empty($_POST['id_data'])) {
                        $wpdb->update('data_bkk_pilkades_desa', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    } else {
                        $cek_id = $wpdb->get_row($wpdb->prepare('
                            SELECT
                                id_desa,
                                active
                            FROM data_bkk_pilkades_desa
                            WHERE id_desa=%s
                        ', $id_desa), ARRAY_A);
                        if (empty($cek_id)) {
                            $wpdb->insert('data_bkk_pilkades_desa', $data);
                        } else {
                            if ($cek_id['active'] == 0) {
                                $wpdb->update('data_bkk_pilkades_desa', $data, array(
                                    'id' => $cek_id['id_desa']
                                ));
                            } else {
                                $ret['status'] = 'error';
                                $ret['message'] = 'Gagal disimpan. Data bkk_pilkades_desa dengan id_desa="' . $id_desa . '" sudah ada!';
                            }
                        }
                    }
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

    public function get_datatable_bkk_pilkades()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'id_kecamatan',
                    1 => 'id_desa',
                    2 => 'kecamatan',
                    3 => 'desa',
                    4 => 'total',
                    5 => 'tahun_anggaran',
                    6 => 'id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value . ")";
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bkk_pilkades_desa`";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_bkk_pilkades_desa`";
                $where_first = " WHERE 1=1 AND active = 1";
                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .= " ORDER BY update_at DESC" . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                    $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total'] = number_format($recVal['total'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            } else {
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }

    public function get_pencairan_pemdes_bkk($return = false)
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['total_pencairan'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        SUM(total_pencairan) 
                    FROM data_pencairan_bkk_desa
                    WHERE id_kegiatan=%d
                        AND status=1
                ', $_POST['id']));
                $ret['sql'] = $wpdb->last_query;
                $ret['pagu_anggaran'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        total 
                    FROM data_bkk_desa
                    WHERE id=%d
                        AND active=1
                ', $_POST['id']));
                if (empty($ret['total_pencairan'])) {
                    $ret['total_pencairan'] = 0;
                }
                if (empty($ret['pagu_anggaran'])) {
                    $ret['pagu_anggaran'] = 0;
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }
        if (!empty($return)) {
            return $ret;
        } else {
            die(json_encode($ret));
        }
    }

    public function get_data_pencairan_bkk_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $data = $wpdb->get_row($wpdb->prepare("
                    select 
                        p.total_pencairan, 
                        p.id_kegiatan, 
                        p.id,
                        p.file_nota_dinas,
                        p.file_sptj,
                        p.file_pakta_integritas,
                        p.file_permohonan_transfer,
                        p.file_verifikasi_rekomendasi,
                        p.file_permohonan_penyaluran_kades,
                        p.file_sptj_kades,
                        p.file_pakta_integritas_kades,
                        p.file_pakta_integritas_3_orang,
                        p.file_proposal_rencana_anggaran,
                        p.file_apbdes,
                        p.file_fc_rek_kas_desa,
                        p.status,
                        p.status_ver_total,
                        p.ket_ver_total,
                        d.tahun_anggaran,
                        d.kecamatan,
                        d.desa,
                        d.kegiatan, 
                        d.alamat
                    from data_pencairan_bkk_desa p
                    inner join data_bkk_desa d on p.id_kegiatan=d.id
                    where p.id=%d
                ", $_POST['id']), ARRAY_A);
                $ret['data'] = $data;
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

    public function hapus_data_pencairan_bkk_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->update('data_pencairan_bkk_desa', array('status' => 3), array(
                    'id' => $_POST['id']
                ));
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

    public function tambah_data_pencairan_bkk()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if ($ret['status'] != 'error' && !empty($_POST['id_kegiatan'])) {
                    $id_kegiatan = $_POST['id_kegiatan'];
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pilih Uraian Kegiatan Dulu!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['pagu_anggaran'])) {
                    $pagu_anggaran = $_POST['pagu_anggaran'];
                } elseif ($ret['status'] != 'error') {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pagu tidak boleh kosong!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['keterangan'])) {
                    $keterangan = $_POST['keterangan'];
                }
                if (empty($_POST['id_data'])) {
                    if ($ret['status'] != 'error' && !empty($_FILES['nota_dinas'])) {
                        $nota_dinas = $_FILES['nota_dinas'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Nota dinas tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['sptj'])) {
                        $sptj = $_FILES['sptj'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'SPTJ tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pakta_integritas'])) {
                        $pakta_integritas = $_FILES['pakta_integritas'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Pakta integritas tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['permohonan_transfer'])) {
                        $permohonan_transfer = $_FILES['permohonan_transfer'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat permohonan transfer tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['verifikasi_rekomendasi'])) {
                        $verifikasi_rekomendasi = $_FILES['verifikasi_rekomendasi'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat verifikasi rekomendasi tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['permohonan_penyaluran_kades'])) {
                        $permohonan_penyaluran_kades = $_FILES['permohonan_penyaluran_kades'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat permohonan penyaluran Kepala Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['sptj_kades'])) {
                        $sptj_kades = $_FILES['sptj_kades'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'SPTJ Kepala Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pakta_integritas_kades'])) {
                        $pakta_integritas_kades = $_FILES['pakta_integritas_kades'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Pakta integritas Kepala Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pakta_integritas_3_orang'])) {
                        $pakta_integritas_3_orang = $_FILES['pakta_integritas_3_orang'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Pakta integritas 3 orang tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['proposal_rencana_anggaran'])) {
                        $proposal_rencana_anggaran = $_FILES['proposal_rencana_anggaran'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Proposal rencana anggaran tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pakta_integritas'])) {
                        $pakta_integritas = $_FILES['pakta_integritas'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Pakta integritas tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['apbdes'])) {
                        $apbdes = $_FILES['apbdes'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Peraturan Desa tentang APBDesa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['fc_rek_kas_desa'])) {
                        $fc_rek_kas_desa = $_FILES['fc_rek_kas_desa'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Foto copy rekening kas desa tidak boleh kosong!';
                    }
                }

                $status_pencairan = $_POST['status_pencairan'];
                $keterangan_status_pencairan = $_POST['keterangan_status_pencairan'];

                $_POST['id'] = $id_kegiatan;
                $pencairan = $this->get_pencairan_pemdes_bkk(true);
                if (($pencairan['total_pencairan'] + $pagu_anggaran) > $pencairan['pagu_anggaran']) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
                }

                if ($ret['status'] != 'error') {
                    $data = array(
                        'id_kegiatan' => $id_kegiatan,
                        'total_pencairan' => $pagu_anggaran,
                        'status_ver_total' => $status_pencairan,
                        'ket_ver_total' => $keterangan_status_pencairan,
                        'update_at' => current_time('mysql')
                    );
                    if (empty($_POST['id_data'])) {
                        $data['status'] = 0; // 0 berati belum dicek
                    }

                    $path = WPSIPD_PLUGIN_PATH . 'public/media/keu_pemdes/';
                    $cek_file = array();

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['nota_dinas'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['nota_dinas'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_nota_dinas'] = $upload['filename'];
                            $cek_file['file_nota_dinas'] = $data['file_nota_dinas'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['sptj'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['sptj'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_sptj'] = $upload['filename'];
                            $cek_file['file_sptj'] = $data['file_sptj'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }


                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['pakta_integritas'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pakta_integritas'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_pakta_integritas'] = $upload['filename'];
                            $cek_file['file_pakta_integritas'] = $data['file_pakta_integritas'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }


                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['permohonan_transfer'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['permohonan_transfer'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_permohonan_transfer'] = $upload['filename'];
                            $cek_file['file_permohonan_transfer'] = $data['file_permohonan_transfer'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }


                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['verifikasi_rekomendasi'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['verifikasi_rekomendasi'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_verifikasi_rekomendasi'] = $upload['filename'];
                            $cek_file['file_verifikasi_rekomendasi'] = $data['file_verifikasi_rekomendasi'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }


                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['permohonan_penyaluran_kades'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['permohonan_penyaluran_kades'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_permohonan_penyaluran_kades'] = $upload['filename'];
                            $cek_file['file_permohonan_penyaluran_kades'] = $data['file_permohonan_penyaluran_kades'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }


                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['sptj_kades'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['sptj_kades'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_sptj_kades'] = $upload['filename'];
                            $cek_file['file_sptj_kades'] = $data['file_sptj_kades'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }


                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['pakta_integritas_kades'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pakta_integritas_kades'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_pakta_integritas_kades'] = $upload['filename'];
                            $cek_file['file_pakta_integritas_kades'] = $data['file_pakta_integritas_kades'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }


                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['pakta_integritas_3_orang'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pakta_integritas_3_orang'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_pakta_integritas_3_orang'] = $upload['filename'];
                            $cek_file['file_pakta_integritas_3_orang'] = $data['file_pakta_integritas_3_orang'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }


                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['proposal_rencana_anggaran'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['proposal_rencana_anggaran'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_proposal_rencana_anggaran'] = $upload['filename'];
                            $cek_file['file_proposal_rencana_anggaran'] = $data['file_proposal_rencana_anggaran'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }


                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['apbdes'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['apbdes'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_apbdes'] = $upload['filename'];
                            $cek_file['file_apbdes'] = $data['file_apbdes'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['fc_rek_kas_desa'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['fc_rek_kas_desa'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_fc_rek_kas_desa'] = $upload['filename'];
                            $cek_file['file_fc_rek_kas_desa'] = $data['file_fc_rek_kas_desa'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if ($ret['status'] == 'error') {
                        // hapus file yang sudah terlanjur upload karena ada file yg gagal upload!
                        foreach ($cek_file as $newfile) {
                            if (is_file($path . $newfile)) {
                                unlink($path . $newfile);
                            }
                        }
                    }

                    if ($ret['status'] != 'error') {
                        if (!empty($_POST['id_data'])) {
                            $file_lama = $wpdb->get_row($wpdb->prepare('
                                SELECT
                                    file_nota_dinas,
                                    file_sptj,
                                    file_pakta_integritas
                                    file_permohonan_transfer,
                                    file_verifikasi_rekomendasi,
                                    file_permohonan_penyaluran_kades,
                                    file_sptj_kades,
                                    file_pakta_integritas_kades,
                                    file_pakta_integritas_3_orang,
                                    file_proposal_rencana_anggaran,
                                    file_apbdes,
                                    file_fc_rek_kas_desa
                                FROM data_pencairan_bkk_desa
                                WHERE id=%d
                            ', $_POST['id_data']), ARRAY_A);

                            if (
                                $file_lama['file_nota_dinas'] != $data['file_nota_dinas']
                                && is_file($path . $file_lama['file_nota_dinas'])
                            ) {
                                unlink($path . $file_lama['file_nota_dinas']);
                            }

                            if (
                                $file_lama['file_sptj'] != $data['file_sptj']
                                && is_file($path . $file_lama['file_sptj'])
                            ) {
                                unlink($path . $file_lama['file_sptj']);
                            }

                            if (
                                $file_lama['file_pakta_integritas'] != $data['file_pakta_integritas']
                                && is_file($path . $file_lama['file_pakta_integritas'])
                            ) {
                                unlink($path . $file_lama['file_pakta_integritas']);
                            }

                            if (
                                $file_lama['file_permohonan_transfer'] != $data['file_permohonan_transfer']
                                && is_file($path . $file_lama['file_permohonan_transfer'])
                            ) {
                                unlink($path . $file_lama['file_permohonan_transfer']);
                            }

                            if (
                                $file_lama['file_verifikasi_rekomendasi'] != $data['file_verifikasi_rekomendasi']
                                && is_file($path . $file_lama['file_verifikasi_rekomendasi'])
                            ) {
                                unlink($path . $file_lama['file_verifikasi_rekomendasi']);
                            }

                            if (
                                $file_lama['file_permohonan_penyaluran_kades'] != $data['file_permohonan_penyaluran_kades']
                                && is_file($path . $file_lama['file_permohonan_penyaluran_kades'])
                            ) {
                                unlink($path . $file_lama['file_permohonan_penyaluran_kades']);
                            }

                            if (
                                $file_lama['file_sptj_kades'] != $data['file_sptj_kades']
                                && is_file($path . $file_lama['file_sptj_kades'])
                            ) {
                                unlink($path . $file_lama['file_sptj_kades']);
                            }

                            if (
                                $file_lama['file_pakta_integritas_kades'] != $data['file_pakta_integritas_kades']
                                && is_file($path . $file_lama['file_pakta_integritas_kades'])
                            ) {
                                unlink($path . $file_lama['file_pakta_integritas_kades']);
                            }

                            if (
                                $file_lama['file_pakta_integritas_3_orang'] != $data['file_pakta_integritas_3_orang']
                                && is_file($path . $file_lama['file_pakta_integritas_3_orang'])
                            ) {
                                unlink($path . $file_lama['file_pakta_integritas_3_orang']);
                            }

                            if (
                                $file_lama['file_proposal_rencana_anggaran'] != $data['file_proposal_rencana_anggaran']
                                && is_file($path . $file_lama['file_proposal_rencana_anggaran'])
                            ) {
                                unlink($path . $file_lama['file_proposal_rencana_anggaran']);
                            }

                            if (
                                $file_lama['file_apbdes'] != $data['file_apbdes']
                                && is_file($path . $file_lama['file_apbdes'])
                            ) {
                                unlink($path . $file_lama['file_apbdes']);
                            }

                            if (
                                $file_lama['file_fc_rek_kas_desa'] != $data['file_fc_rek_kas_desa']
                                && is_file($path . $file_lama['file_fc_rek_kas_desa'])
                            ) {
                                unlink($path . $file_lama['file_fc_rek_kas_desa']);
                            }

                            $wpdb->update('data_pencairan_bkk_desa', $data, array(
                                'id' => $_POST['id_data']
                            ));
                            $ret['message'] = 'Berhasil update data!';
                        } else {
                            $wpdb->insert('data_pencairan_bkk_desa', $data);
                        }
                    }
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

    public function get_datatable_data_pencairan_bkk()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'd.tahun_anggaran',
                    1 => 'd.kecamatan',
                    2 => 'd.desa',
                    3 => 'd.kegiatan',
                    4 => 'd.alamat',
                    5 => 'p.total_pencairan',
                    6 => 'p.file_nota_dinas',
                    7 => 'p.file_sptj',
                    8 => 'p.file_pakta_integritas',
                    9 => 'p.file_permohonan_transfer',
                    10 => 'p.file_verifikasi_rekomendasi',
                    11 => 'p.file_permohonan_penyaluran_kades',
                    12 => 'p.file_sptj_kades',
                    13 => 'p.file_pakta_integritas_kades',
                    14 => 'p.file_pakta_integritas_3_orang',
                    15 => 'p.file_proposal_rencana_anggaran',
                    16 => 'p.file_apbdes',
                    17 => 'p.file_fc_rek_kas_desa',
                    18 => 'p.status',
                    19 => 'p.id_kegiatan',
                    20 => 'p.status_ver_total',
                    21 => 'p.ket_ver_total',
                    24 => 'p.update_at',
                    25 => 'p.id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value;
                    $where .= " OR kegiatan LIKE " . $search_value;
                    $where .= " OR alamat LIKE " . $search_value . ")";
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bkk_desa` p inner join data_bkk_desa d on p.id_kegiatan=d.id";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_pencairan_bkk_desa` p inner join data_bkk_desa d on p.id_kegiatan=d.id";
                $where_first = " WHERE 1=1 AND status != 3";

                if (
                    in_array("PA", $user_meta->roles)
                    || in_array("PLT", $user_meta->roles)
                    || in_array("KPA", $user_meta->roles)
                ) {
                    $skpd_db = $wpdb->get_results($wpdb->prepare("
                        SELECT 
                            nama_skpd, 
                            id_skpd, 
                            kode_skpd,
                            is_skpd
                        from data_unit 
                        where id_skpd=%d
                            and active=1
                            and tahun_anggaran=%d
                        group by id_skpd", $params['id_skpd'], $params['tahun_anggaran']), ARRAY_A);
                    $where_array = array();
                    foreach ($skpd_db as $skpd) {
                        $nama_kec = str_replace('kecamatan ', '', strtolower($skpd['nama_skpd']));
                        $where_array[] = " d.kecamatan LIKE '" . $nama_kec . "'";
                    }
                    $where .= " AND (" . implode(' OR ', $where_array) . ")";
                }

                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .=  " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . str_replace("'", '', $wpdb->prepare('%s', $params['order'][0]['dir'])) . ",  p.update_at DESC " . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                    if ($recVal['status'] == 0) {
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Hapus Data"><i class="dashicons dashicons-trash"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-primary" onclick="submit_pencairan(\'' . $recVal['id'] . '\'); return false;" href="#" title="Submit Pencairan"><i class="dashicons dashicons-yes"></i></a>';
                        // 0 belum dicek, 1 diterima dan 2 ditolak
                        if (
                            $recVal['status_ver_total'] == 2
                        ) {
                            $pesan = '';
                            if ($recVal['status_ver_total'] == 2) {
                                $pesan .= '<br>Keterangan status pencairan: ' . $recVal['ket_ver_total'];
                            }
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>' . $pesan;
                        } else {
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Menunggu Submit</span>';
                        }
                    } elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                    } elseif ($recVal['status'] == 2) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-warning btn-sm">Diverifikasi Admin</span>';
                        if (in_array("administrator", $user_meta->roles)) {
                            $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="verifikasi_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Verifikasi Data"><i class="dashicons dashicons-yes"></i></a>';
                        }
                    }
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total_pencairan'] = number_format($recVal['total_pencairan'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            } else {
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($ret));
    }


    public function get_pemdes_bhpd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (!empty($_POST['nama_kec'])) {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bhpd_desa
                        WHERE tahun_anggaran=%d
                            AND kecamatan=%s
                    ", $_POST['tahun_anggaran'], $_POST['nama_kec']), ARRAY_A);
                } else {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bhpd_desa
                        WHERE tahun_anggaran=%d
                    ", $_POST['tahun_anggaran']), ARRAY_A);
                }
                $ret['data'] = $data;
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

    public function get_pencairan_pemdes_bhpd($return = false)
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['total_pencairan'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        SUM(total_pencairan) 
                    FROM data_pencairan_bhpd_desa
                    WHERE id_bhpd=%d
                        AND status=1
                ', $_POST['id']));
                $ret['sql'] = $wpdb->last_query;
                $ret['pagu_anggaran'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        total 
                    FROM data_bhpd_desa
                    WHERE id=%d
                        AND active=1
                ', $_POST['id']));
                if (empty($ret['total_pencairan'])) {
                    $ret['total_pencairan'] = 0;
                }
                if (empty($ret['pagu_anggaran'])) {
                    $ret['pagu_anggaran'] = 0;
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }
        if (!empty($return)) {
            return $ret;
        } else {
            die(json_encode($ret));
        }
    }

    public function get_data_pencairan_bhpd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $data = $wpdb->get_row($wpdb->prepare("
                    select 
                        p.total_pencairan, 
                        p.id_bhpd, 
                        p.id,
                        p.keterangan,
                        p.file_nota_dinas,
                        p.file_sptj,
                        p.file_pakta_integritas,
                        p.file_permohonan_transfer,
                        p.file_rekomendasi,
                        p.file_permohonan_penyaluran_kades,
                        p.file_sptj_kades,
                        p.file_pakta_integritas_kades,
                        p.file_pernyataaan_kades_spj_dbhpd,
                        p.file_sk_bendahara_desa,
                        p.file_fc_ktp_kades,
                        p.file_fc_rek_kas_desa,
                        p.file_laporan_realisasi_tahun_sebelumnya,
                        p.status,
                        p.status_ver_total,
                        p.ket_ver_total,
                        d.tahun_anggaran,
                        d.kecamatan,
                        d.desa
                    from data_pencairan_bhpd_desa p
                    inner join data_bhpd_desa d on p.id_bhpd=d.id
                    where p.id=%d
                ", $_POST['id']), ARRAY_A);
                $ret['data'] = $data;
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

    public function hapus_data_pencairan_bhpd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->update('data_pencairan_bhpd_desa', array('status' => 3), array(
                    'id' => $_POST['id']
                ));
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

    public function tambah_data_pencairan_bhpd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if ($ret['status'] != 'error' && !empty($_POST['id_bhpd'])) {
                    $id_bhpd = $_POST['id_bhpd'];
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pilih desa dulu!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['pagu_anggaran'])) {
                    $pagu_anggaran = $_POST['pagu_anggaran'];
                } elseif ($ret['status'] != 'error') {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pagu tidak boleh kosong!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['keterangan'])) {
                    $keterangan = $_POST['keterangan'];
                } elseif ($ret['status'] != 'error') {
                    $ret['status'] = 'error';
                    $ret['message'] = 'keterangan tidak boleh kosong';
                }
                if (empty($_POST['id_data'])) {
                    if ($ret['status'] != 'error' && !empty($_FILES['nota_dinas'])) {
                        $nota_dinas = $_FILES['nota_dinas'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Nota Dinas tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['sptj'])) {
                        $sptj = $_FILES['sptj'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'SPTJ tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pakta_integritas'])) {
                        $pakta_integritas = $_FILES['pakta_integritas'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Pakta integritas tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['permohonan_transfer'])) {
                        $permohonan_transfer = $_FILES['permohonan_transfer'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat permohonan transfer tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['rekomendasi'])) {
                        $rekomendasi = $_FILES['rekomendasi'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat rekomendasi tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['permohonan_penyaluran_kades'])) {
                        $permohonan_penyaluran_kades = $_FILES['permohonan_penyaluran_kades'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat permohonan penyaluran Kepala Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pakta_integritas_kades'])) {
                        $pakta_integritas_kades = $_FILES['pakta_integritas_kades'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Pakta integritas Kepala Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pernyataaan_kades_spj_dbhpd'])) {
                        $pernyataaan_kades_spj_dbhpd = $_FILES['pernyataaan_kades_spj_dbhpd'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = ' Surat Pernyataan Kepala Desa bahwa SPJ DBH Pajak Daerah telah selesai 100% bermaterai tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['sk_bendahara_desa'])) {
                        $sk_bendahara_desa = $_FILES['sk_bendahara_desa'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = ' Surat Keputusan Bendahara Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['fc_ktp_kades'])) {
                        $fc_ktp_kades = $_FILES['fc_ktp_kades'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = ' Foto copy KTP Kepala Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['fc_rek_kas_desa'])) {
                        $fc_rek_kas_desa = $_FILES['fc_rek_kas_desa'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = ' Foto copy rekening kas Desa tidak boleh kosong!';
                    }
                }

                $_POST['id'] = $id_bhpd;
                $pencairan = $this->get_pencairan_pemdes_bhpd(true);
                if (($pencairan['total_pencairan'] + $pagu_anggaran) > $pencairan['pagu_anggaran']) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
                }
                if (
                    $ret['status'] != 'error'
                    && $pencairan['total_pencairan'] > 0
                    && empty($_FILES['realisasi_tahun_sebelumnya'])
                ) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Dokumen realisasi tahun sebelumnya tidak boleh kosong!';
                }
                $status_pencairan = $_POST['status_pencairan'];
                $keterangan_status_pencairan = $_POST['keterangan_status_pencairan'];

                if ($ret['status'] != 'error') {
                    if (empty($_POST['id_data'])) {
                        $status = 0;
                    } else {
                        $status = 1;
                        if ($status == 0) {
                            $status = 2;
                        }
                    }
                }
                $data = array(
                    'id_bhpd' => $id_bhpd,
                    'total_pencairan' => $pagu_anggaran,
                    'status_ver_total' => $status_pencairan,
                    'ket_ver_total' => $keterangan_status_pencairan,
                    'keterangan' => $keterangan,
                    'update_at' => current_time('mysql')
                );
                if (empty($_POST['id_data'])) {
                    $data['status'] = 0; // 0 berati belum dicek
                }
                $path = WPSIPD_PLUGIN_PATH . 'public/media/keu_pemdes/';

                $cek_file = array();
                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['nota_dinas'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['nota_dinas'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_nota_dinas'] = $upload['filename'];
                        $cek_file['file_nota_dinas'] = $data['file_nota_dinas'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['sptj'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['sptj'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_sptj'] = $upload['filename'];
                        $cek_file['file_sptj'] = $data['file_sptj'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['pakta_integritas'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pakta_integritas'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_pakta_integritas'] = $upload['filename'];
                        $cek_file['file_pakta_integritas'] = $data['file_pakta_integritas'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['permohonan_transfer'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['permohonan_transfer'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_permohonan_transfer'] = $upload['filename'];
                        $cek_file['file_permohonan_transfer'] = $data['file_permohonan_transfer'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['rekomendasi'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['rekomendasi'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_rekomendasi'] = $upload['filename'];
                        $cek_file['file_rekomendasi'] = $data['file_rekomendasi'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['permohonan_penyaluran_kades'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['permohonan_penyaluran_kades'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_permohonan_penyaluran_kades'] = $upload['filename'];
                        $cek_file['file_permohonan_penyaluran_kades'] = $data['file_permohonan_penyaluran_kades'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['sptj_kades'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['sptj_kades'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_sptj_kades'] = $upload['filename'];
                        $cek_file['file_sptj_kades'] = $data['file_sptj_kades'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['pakta_integritas_kades'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pakta_integritas_kades'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_pakta_integritas_kades'] = $upload['filename'];
                        $cek_file['file_pakta_integritas_kades'] = $data['file_pakta_integritas_kades'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['pernyataaan_kades_spj_dbhpd'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pernyataaan_kades_spj_dbhpd'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_pernyataaan_kades_spj_dbhpd'] = $upload['filename'];
                        $cek_file['file_pernyataaan_kades_spj_dbhpd'] = $data['file_pernyataaan_kades_spj_dbhpd'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['sk_bendahara_desa'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['sk_bendahara_desa'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_sk_bendahara_desa'] = $upload['filename'];
                        $cek_file['file_sk_bendahara_desa'] = $data['file_sk_bendahara_desa'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['fc_ktp_kades'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['fc_ktp_kades'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_fc_ktp_kades'] = $upload['filename'];
                        $cek_file['file_fc_ktp_kades'] = $data['file_fc_ktp_kades'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && !empty($_FILES['fc_rek_kas_desa'])
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['fc_rek_kas_desa'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_fc_rek_kas_desa'] = $upload['filename'];
                        $cek_file['file_fc_rek_kas_desa'] = $data['file_fc_rek_kas_desa'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if (
                    $ret['status'] != 'error'
                    && $pencairan['total_pencairan'] > 0
                ) {
                    $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['laporan_realisasi_tahun_sebelumnya'], ['pdf']);
                    if ($upload['status'] == true) {
                        $data['file_laporan_realisasi_tahun_sebelumnya'] = $upload['filename'];
                        $cek_file['file_laporan_realisasi_tahun_sebelumnya'] = $data['file_laporan_realisasi_tahun_sebelumnya'];
                    } else {
                        $ret['status'] = 'error';
                        $ret['message'] = $upload['message'];
                    }
                }

                if ($ret['status'] == 'error') {
                    // hapus file yang sudah terlanjur upload karena ada file yg gagal upload!
                    foreach ($cek_file as $newfile) {
                        if (is_file($path . $newfile)) {
                            unlink($path . $newfile);
                        }
                    }
                }

                if ($ret['status'] != 'error') {
                    if (!empty($_POST['id_data'])) {
                        $file_lama = $wpdb->get_row($wpdb->prepare('
                                SELECT
                                    file_nota_dinas,
                                    file_sptj,
                                    file_pakta_integritas,
                                    file_permohonan_transfer,
                                    file_rekomendasi,
                                    file_permohonan_penyaluran_kades,
                                    file_sptj_kades,
                                    file_pakta_integritas_kades,
                                    file_pernyataaan_kades_spj_dbhpd,
                                    file_sk_bendahara_desa,
                                    file_fc_ktp_kades,
                                    file_fc_rek_kas_desa
                                FROM data_pencairan_bhpd_desa
                                WHERE id=%d
                            ', $_POST['id_data']), ARRAY_A);

                        if (
                            $file_lama['file_nota_dinas'] != $data['file_nota_dinas']
                            && is_file($path . $file_lama['file_nota_dinas'])
                        ) {
                            unlink($path . $file_lama['file_nota_dinas']);
                        }

                        if (
                            $file_lama['file_sptj'] != $data['file_sptj']
                            && is_file($path . $file_lama['file_sptj'])
                        ) {
                            unlink($path . $file_lama['file_sptj']);
                        }

                        if (
                            $file_lama['file_pakta_integritas'] != $data['file_pakta_integritas']
                            && is_file($path . $file_lama['file_pakta_integritas'])
                        ) {
                            unlink($path . $file_lama['file_pakta_integritas']);
                        }

                        if (
                            $file_lama['file_permohonan_transfer'] != $data['file_permohonan_transfer']
                            && is_file($path . $file_lama['file_permohonan_transfer'])
                        ) {
                            unlink($path . $file_lama['file_permohonan_transfer']);
                        }

                        if (
                            $file_lama['file_rekomendasi'] != $data['file_rekomendasi']
                            && is_file($path . $file_lama['file_rekomendasi'])
                        ) {
                            unlink($path . $file_lama['file_rekomendasi']);
                        }

                        if (
                            $file_lama['file_permohonan_penyaluran_kades'] != $data['file_permohonan_penyaluran_kades']
                            && is_file($path . $file_lama['file_permohonan_penyaluran_kades'])
                        ) {
                            unlink($path . $file_lama['file_permohonan_penyaluran_kades']);
                        }

                        if (
                            $file_lama['file_sptj_kades'] != $data['file_sptj_kades']
                            && is_file($path . $file_lama['file_sptj_kades'])
                        ) {
                            unlink($path . $file_lama['file_sptj_kades']);
                        }

                        if (
                            $file_lama['file_pakta_integritas_kades'] != $data['file_pakta_integritas_kades']
                            && is_file($path . $file_lama['file_pakta_integritas_kades'])
                        ) {
                            unlink($path . $file_lama['file_pakta_integritas_kades']);
                        }

                        if (
                            $file_lama['file_pernyataaan_kades_spj_dbhpd'] != $data['file_pernyataaan_kades_spj_dbhpd']
                            && is_file($path . $file_lama['file_pernyataaan_kades_spj_dbhpd'])
                        ) {
                            unlink($path . $file_lama['file_pernyataaan_kades_spj_dbhpd']);
                        }

                        if (
                            $file_lama['file_sk_bendahara_desa'] != $data['file_sk_bendahara_desa']
                            && is_file($path . $file_lama['file_sk_bendahara_desa'])
                        ) {
                            unlink($path . $file_lama['file_sk_bendahara_desa']);
                        }

                        if (
                            $file_lama['file_fc_ktp_kades'] != $data['file_fc_ktp_kades']
                            && is_file($path . $file_lama['file_fc_ktp_kades'])
                        ) {
                            unlink($path . $file_lama['file_fc_ktp_kades']);
                        }

                        if (
                            $file_lama['file_fc_rek_kas_desa'] != $data['file_fc_rek_kas_desa']
                            && is_file($path . $file_lama['file_fc_rek_kas_desa'])
                        ) {
                            unlink($path . $file_lama['file_fc_rek_kas_desa']);
                        }

                        if (
                            $file_lama['file_laporan_realisasi_tahun_sebelumnya'] != $data['file_laporan_realisasi_tahun_sebelumnya']
                            && is_file($path . $file_lama['file_laporan_realisasi_tahun_sebelumnya'])
                        ) {
                            unlink($path . $file_lama['file_laporan_realisasi_tahun_sebelumnya']);
                        }

                        $wpdb->update('data_pencairan_bhpd_desa', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    } else {
                        $wpdb->insert('data_pencairan_bhpd_desa', $data);
                    }
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

    public function get_datatable_data_pencairan_bhpd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'd.tahun_anggaran',
                    1 => 'd.kecamatan',
                    2 => 'd.desa',
                    3 => 'p.total_pencairan',
                    4 => 'p.keterangan',
                    5 => 'p.status',
                    6 => 'p.file_nota_dinas',
                    7 => 'p.file_sptj',
                    8 => 'p.file_pakta_integritas',
                    9 => 'p.file_permohonan_transfer',
                    10 => 'p.file_rekomendasi',
                    11 => 'p.file_permohonan_penyaluran_kades',
                    12 => 'p.file_sptj_kades',
                    13 => 'p.file_pakta_integritas_kades',
                    14 => 'p.file_pernyataaan_kades_spj_dbhpd',
                    15 => 'p.file_sk_bendahara_desa',
                    16 => 'p.file_fc_ktp_kades',
                    17 => 'p.file_fc_rek_kas_desa',
                    18 => 'p.file_laporan_realisasi_tahun_sebelumnya',
                    19 => 'p.id_bhpd',
                    20 => 'p.status_ver_total',
                    21 => 'p.ket_ver_total',
                    22 => 'p.id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value . ")";
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bhpd_desa` p inner join data_bhpd_desa d on p.id_bhpd=d.id";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_pencairan_bhpd_desa` p inner join data_bhpd_desa d on p.id_bhpd=d.id";
                $where_first = " WHERE 1=1 AND status != 3";

                if (
                    in_array("PA", $user_meta->roles)
                    || in_array("PLT", $user_meta->roles)
                    || in_array("KPA", $user_meta->roles)
                ) {
                    $skpd_db = $wpdb->get_results($wpdb->prepare("
                        SELECT 
                            nama_skpd, 
                            id_skpd, 
                            kode_skpd,
                            is_skpd
                        from data_unit 
                        where id_skpd=%d
                            and active=1
                            and tahun_anggaran=%d
                        group by id_skpd", $params['id_skpd'], $params['tahun_anggaran']), ARRAY_A);
                    $where_array = array();
                    foreach ($skpd_db as $skpd) {
                        $nama_kec = str_replace('kecamatan ', '', strtolower($skpd['nama_skpd']));
                        $where_array[] = " d.kecamatan LIKE '" . $nama_kec . "'";
                    }
                    $where .= " AND (" . implode(' OR ', $where_array) . ")";
                }

                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .=  " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . str_replace("'", '', $wpdb->prepare('%s', $params['order'][0]['dir'])) . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                    if ($recVal['status'] == 0) {
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Hapus Data"><i class="dashicons dashicons-trash"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-primary" onclick="submit_pencairan(\'' . $recVal['id'] . '\'); return false;" href="#" title="Submit Pencairan"><i class="dashicons dashicons-yes"></i></a>';
                        // 0 belum dicek, 1 diterima dan 2 ditolak
                        if ($recVal['status_ver_total'] == 2) {
                            $pesan = '';
                            if ($recVal['status_ver_total'] == 2) {
                                $pesan .= '<br>Keterangan status pencairan: ' . $recVal['ket_ver_total'];
                            }
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>' . $pesan;
                        } else {
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Menunggu Submit</span>';
                        }
                    } elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                    } elseif ($recVal['status'] == 2) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-warning btn-sm">Diverifikasi Admin</span>';
                        if (in_array("administrator", $user_meta->roles)) {
                            $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="verifikasi_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Verifikasi Data"><i class="dashicons dashicons-yes"></i></a>';
                        }
                    }
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total_pencairan'] = number_format($recVal['total_pencairan'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            } else {
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }

    public function get_pemdes_bhrd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (!empty($_POST['nama_kec'])) {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bhrd_desa
                        WHERE tahun_anggaran=%d
                            AND kecamatan=%s
                    ", $_POST['tahun_anggaran'], $_POST['nama_kec']), ARRAY_A);
                } else {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bhrd_desa
                        WHERE tahun_anggaran=%d
                    ", $_POST['tahun_anggaran']), ARRAY_A);
                }
                $ret['data'] = $data;
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

    public function get_pencairan_pemdes_bhrd($return = false)
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['total_pencairan'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        SUM(total_pencairan) 
                    FROM data_pencairan_bhrd_desa
                    WHERE id_bhrd=%d
                        AND status=1
                ', $_POST['id']));
                $ret['sql'] = $wpdb->last_query;
                $ret['pagu_anggaran'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        total 
                    FROM data_bhrd_desa
                    WHERE id=%d
                        AND active=1
                ', $_POST['id']));
                if (empty($ret['total_pencairan'])) {
                    $ret['total_pencairan'] = 0;
                }
                if (empty($ret['pagu_anggaran'])) {
                    $ret['pagu_anggaran'] = 0;
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }
        if (!empty($return)) {
            return $ret;
        } else {
            die(json_encode($ret));
        }
    }

    public function get_data_pencairan_bhrd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $data = $wpdb->get_row($wpdb->prepare("
                    select 
                        p.total_pencairan, 
                        p.id_bhrd, 
                        p.id,
                        p.keterangan,
                        p.file_nota_dinas,
                        p.file_sptj,
                        p.file_pakta_integritas,
                        p.file_permohonan_transfer,
                        p.file_rekomendasi,
                        p.file_permohonan_penyaluran_kades,
                        p.file_sptj_kades,
                        p.file_pakta_integritas_kades,
                        p.file_pernyataaan_kades_spj_dbhpd,
                        p.file_sk_bendahara_desa,
                        p.file_fc_ktp_kades,
                        p.file_fc_rek_kas_desa,
                        p.file_laporan_realisasi_sebelumnya,
                        p.status,
                        p.status_ver_total,
                        p.ket_ver_total,
                        d.tahun_anggaran,
                        d.kecamatan,
                        d.desa
                    from data_pencairan_bhrd_desa p
                    inner join data_bhrd_desa d on p.id_bhrd=d.id
                    where p.id=%d
                ", $_POST['id']), ARRAY_A);
                $ret['data'] = $data;
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

    public function hapus_data_pencairan_bhrd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->update('data_pencairan_bhrd_desa', array('status' => 3), array(
                    'id' => $_POST['id']
                ));
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

    public function tambah_data_pencairan_bhrd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if ($ret['status'] != 'error' && !empty($_POST['id_bhrd'])) {
                    $id_bhrd = $_POST['id_bhrd'];
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pilih Uraian Kegiatan Dulu!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['pagu_anggaran'])) {
                    $pagu_anggaran = $_POST['pagu_anggaran'];
                } elseif ($ret['status'] != 'error') {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pagu tidak boleh kosong!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['keterangan'])) {
                    $keterangan = $_POST['keterangan'];
                } elseif ($ret['status'] != 'error') {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pagu tidak boleh kosong!';
                }
                if (empty($_POST['id_data'])) {
                    if ($ret['status'] != 'error' && !empty($_FILES['nota_dinas'])) {
                        $nota_dinas = $_FILES['nota_dinas'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Nota Dinas tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['sptj'])) {
                        $sptj = $_FILES['sptj'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'SPTJ tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pakta_integritas'])) {
                        $pakta_integritas = $_FILES['pakta_integritas'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Pakta integritas tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['permohonan_transfer'])) {
                        $permohonan_transfer = $_FILES['permohonan_transfer'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat Permohonan transfer tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['rekomendasi'])) {
                        $rekomendasi = $_FILES['rekomendasi'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat Rekomendasi Camat tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['permohonan_penyaluran_kades'])) {
                        $permohonan_penyaluran_kades = $_FILES['permohonan_penyaluran_kades'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat permohonan penyaluran Kepala Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['sptj_kades'])) {
                        $sptj_kades = $_FILES['sptj_kades'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'SPTJ Kepala Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pakta_integritas_kades'])) {
                        $pakta_integritas_kades = $_FILES['pakta_integritas_kades'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Pakta interitas Kepala Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pernyataaan_kades_spj_dbhpd'])) {
                        $pernyataaan_kades_spj_dbhpd = $_FILES['pernyataaan_kades_spj_dbhpd'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat Pernyataan Kepala Desa bahwa SPJ DBH Pajak Daerah telah selesai 100% bermaterai tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['sk_bendahara_desa'])) {
                        $sk_bendahara_desa = $_FILES['sk_bendahara_desa'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat Keputusan Bendahara Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['fc_ktp_kades'])) {
                        $fc_ktp_kades = $_FILES['fc_ktp_kades'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Foto copy KTP Kepala Desa tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['fc_rek_kas_desa'])) {
                        $fc_rek_kas_desa = $_FILES['fc_rek_kas_desa'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Foto copy rekening kas Desa tidak boleh kosong!';
                    }
                }

                $_POST['id'] = $id_bhrd;
                $pencairan = $this->get_pencairan_pemdes_bhrd(true);
                if (($pencairan['total_pencairan'] + $pagu_anggaran) > $pencairan['pagu_anggaran']) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
                }
                if (
                    $ret['status'] != 'error'
                    && $pencairan['total_pencairan'] > 0
                    && empty($_FILES['realisasi_tahun_sebelumnya'])
                ) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Dokumen realisasi tahun sebelumnya tidak boleh kosong!';
                }
                $status_pencairan = $_POST['status_pencairan'];
                $keterangan_status_pencairan = $_POST['keterangan_status_pencairan'];

                if ($ret['status'] != 'error') {
                    $data = array(
                        'id_bhrd' => $id_bhrd,
                        'total_pencairan' => $pagu_anggaran,
                        'status_ver_total' => $status_pencairan,
                        'ket_ver_total' => $keterangan_status_pencairan,
                        'keterangan' => $keterangan,
                        'update_at' => current_time('mysql')
                    );
                    if (empty($_POST['id_data'])) {
                        $data['status'] = 0; // 0 berati belum dicek
                    }
                    $path = WPSIPD_PLUGIN_PATH . 'public/media/keu_pemdes/';
                    $cek_file = array();
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['nota_dinas'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['nota_dinas'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_nota_dinas'] = $upload['filename'];
                            $cek_file['file_nota_dinas'] = $data['file_nota_dinas'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['sptj'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['sptj'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_sptj'] = $upload['filename'];
                            $cek_file['file_sptj'] = $data['file_sptj'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['pakta_integritas'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pakta_integritas'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_pakta_integritas'] = $upload['filename'];
                            $cek_file['file_pakta_integritas'] = $data['file_pakta_integritas'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['permohonan_transfer'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['permohonan_transfer'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_permohonan_transfer'] = $upload['filename'];
                            $cek_file['file_permohonan_transfer'] = $data['file_permohonan_transfer'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['rekomendasi'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['rekomendasi'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_rekomendasi'] = $upload['filename'];
                            $cek_file['file_rekomendasi'] = $data['file_rekomendasi'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['permohonan_penyaluran_kades'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['permohonan_penyaluran_kades'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_permohonan_penyaluran_kades'] = $upload['filename'];
                            $cek_file['file_permohonan_penyaluran_kades'] = $data['file_permohonan_penyaluran_kades'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['sptj_kades'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['sptj_kades'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_sptj_kades'] = $upload['filename'];
                            $cek_file['file_sptj_kades'] = $data['file_sptj_kades'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['pakta_integritas_kades'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pakta_integritas_kades'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_pakta_integritas_kades'] = $upload['filename'];
                            $cek_file['file_pakta_integritas_kades'] = $data['file_pakta_integritas_kades'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['pernyataaan_kades_spj_dbhpd'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pernyataaan_kades_spj_dbhpd'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_pernyataaan_kades_spj_dbhpd'] = $upload['filename'];
                            $cek_file['file_pernyataaan_kades_spj_dbhpd'] = $data['file_pernyataaan_kades_spj_dbhpd'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['sk_bendahara_desa'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['sk_bendahara_desa'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_sk_bendahara_desa'] = $upload['filename'];
                            $cek_file['file_sk_bendahara_desa'] = $data['file_sk_bendahara_desa'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['fc_ktp_kades'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['fc_ktp_kades'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_fc_ktp_kades'] = $upload['filename'];
                            $cek_file['file_fc_ktp_kades'] = $data['file_fc_ktp_kades'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['fc_rek_kas_desa'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['fc_rek_kas_desa'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_fc_rek_kas_desa'] = $upload['filename'];
                            $cek_file['file_fc_rek_kas_desa'] = $data['file_fc_rek_kas_desa'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if (
                        $ret['status'] != 'error'
                        && $pencairan['total_pencairan'] > 0
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['laporan_realisasi_tahun_sebelumnya'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_laporan_realisasi_tahun_sebelumnya'] = $upload['filename'];
                            $cek_file['file_laporan_realisasi_tahun_sebelumnya'] = $data['file_laporan_realisasi_tahun_sebelumnya'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }
                    if ($ret['status'] == 'error') {
                        // hapus file yang sudah terlanjur upload karena ada file yg gagal upload!
                        foreach ($cek_file as $newfile) {
                            if (is_file($path . $newfile)) {
                                unlink($path . $newfile);
                            }
                        }
                    }
                    if ($ret['status'] != 'error') {
                        if (!empty($_POST['id_data'])) {
                            $file_lama = $wpdb->get_row($wpdb->prepare('
                                SELECT
                                    file_nota_dinas,
                                    file_sptj,
                                    file_pakta_integritas,
                                    file_permohonan_transfer,
                                    file_rekomendasi,
                                    file_permohonan_penyaluran_kades,
                                    file_sptj_kades,
                                    file_pakta_integritas_kades,
                                    file_pernyataaan_kades_spj_dbhpd,
                                    file_sk_bendahara_desa,
                                    file_fc_ktp_kades,
                                    file_fc_rek_kas_desa,
                                    file_laporan_realisasi_sebelumnya
                                FROM data_pencairan_bhrd_desa
                                WHERE id=%d
                            ', $_POST['id_data']), ARRAY_A);

                            if (
                                $file_lama['file_nota_dinas'] != $data['file_nota_dinas']
                                && is_file($path . $file_lama['file_nota_dinas'])
                            ) {
                                unlink($path . $file_lama['file_nota_dinas']);
                            }

                            if (
                                $file_lama['file_sptj'] != $data['file_sptj']
                                && is_file($path . $file_lama['file_sptj'])
                            ) {
                                unlink($path . $file_lama['file_sptj']);
                            }

                            if (
                                $file_lama['file_pakta_integritas'] != $data['file_pakta_integritas']
                                && is_file($path . $file_lama['file_pakta_integritas'])
                            ) {
                                unlink($path . $file_lama['file_pakta_integritas']);
                            }

                            if (
                                $file_lama['file_permohonan_transfer'] != $data['file_permohonan_transfer']
                                && is_file($path . $file_lama['file_permohonan_transfer'])
                            ) {
                                unlink($path . $file_lama['file_permohonan_transfer']);
                            }

                            if (
                                $file_lama['file_rekomendasi'] != $data['file_rekomendasi']
                                && is_file($path . $file_lama['file_rekomendasi'])
                            ) {
                                unlink($path . $file_lama['file_rekomendasi']);
                            }

                            if (
                                $file_lama['file_permohonan_penyaluran_kades'] != $data['file_permohonan_penyaluran_kades']
                                && is_file($path . $file_lama['file_permohonan_penyaluran_kades'])
                            ) {
                                unlink($path . $file_lama['file_permohonan_penyaluran_kades']);
                            }

                            if (
                                $file_lama['file_sptj_kades'] != $data['file_sptj_kades']
                                && is_file($path . $file_lama['file_sptj_kades'])
                            ) {
                                unlink($path . $file_lama['file_sptj_kades']);
                            }

                            if (
                                $file_lama['file_pakta_integritas_kades'] != $data['file_pakta_integritas_kades']
                                && is_file($path . $file_lama['file_pakta_integritas_kades'])
                            ) {
                                unlink($path . $file_lama['file_pakta_integritas_kades']);
                            }

                            if (
                                $file_lama['file_pernyataaan_kades_spj_dbhpd'] != $data['file_pernyataaan_kades_spj_dbhpd']
                                && is_file($path . $file_lama['file_pernyataaan_kades_spj_dbhpd'])
                            ) {
                                unlink($path . $file_lama['file_pernyataaan_kades_spj_dbhpd']);
                            }

                            if (
                                $file_lama['file_sk_bendahara_desa'] != $data['file_sk_bendahara_desa']
                                && is_file($path . $file_lama['file_sk_bendahara_desa'])
                            ) {
                                unlink($path . $file_lama['file_sk_bendahara_desa']);
                            }

                            if (
                                $file_lama['file_fc_ktp_kades'] != $data['file_fc_ktp_kades']
                                && is_file($path . $file_lama['file_fc_ktp_kades'])
                            ) {
                                unlink($path . $file_lama['file_fc_ktp_kades']);
                            }

                            if (
                                $file_lama['file_fc_rek_kas_desa'] != $data['file_fc_rek_kas_desa']
                                && is_file($path . $file_lama['file_fc_rek_kas_desa'])
                            ) {
                                unlink($path . $file_lama['file_fc_rek_kas_desa']);
                            }

                            if (
                                $file_lama['file_laporan_realisasi_sebelumnya'] != $data['file_laporan_realisasi_sebelumnya']
                                && is_file($path . $file_lama['file_laporan_realisasi_sebelumnya'])
                            ) {
                                unlink($path . $file_lama['file_laporan_realisasi_sebelumnya']);
                            }

                            $wpdb->update('data_pencairan_bhrd_desa', $data, array(
                                'id' => $_POST['id_data']
                            ));
                            $ret['message'] = 'Berhasil update data!';
                        } else {
                            $wpdb->insert('data_pencairan_bhrd_desa', $data);
                        }
                    }
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

    public function get_datatable_data_pencairan_bhrd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'd.tahun_anggaran',
                    1 => 'd.kecamatan',
                    2 => 'd.desa',
                    3 => 'p.total_pencairan',
                    4 => 'p.keterangan',
                    5 => 'p.file_nota_dinas',
                    6 => 'p.file_sptj',
                    7 => 'p.file_pakta_integritas',
                    8 => 'p.file_permohonan_transfer',
                    9 => 'p.file_rekomendasi',
                    10 => 'p.file_permohonan_penyaluran_kades',
                    11 => 'p.file_sptj_kades',
                    12 => 'p.file_pakta_integritas_kades',
                    13 => 'p.file_pernyataaan_kades_spj_dbhpd',
                    14 => 'p.file_sk_bendahara_desa',
                    15 => 'p.file_fc_ktp_kades',
                    16 => 'p.file_fc_rek_kas_desa',
                    17 => 'p.file_laporan_realisasi_sebelumnya',
                    18 => 'p.status',
                    19 => 'p.id_bhrd',
                    20 => 'p.status_ver_total',
                    21 => 'p.ket_ver_total',
                    22 => 'p.id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value . ")";
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bhrd_desa` p inner join data_bhrd_desa d on p.id_bhrd=d.id";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_pencairan_bhrd_desa` p inner join data_bhrd_desa d on p.id_bhrd=d.id";
                $where_first = " WHERE 1=1 AND status != 3";

                if (
                    in_array("PA", $user_meta->roles)
                    || in_array("PLT", $user_meta->roles)
                    || in_array("KPA", $user_meta->roles)
                ) {
                    $skpd_db = $wpdb->get_results($wpdb->prepare("
                        SELECT 
                            nama_skpd, 
                            id_skpd,
                            kode_skpd,
                            is_skpd
                        from data_unit 
                        where id_skpd=%d
                            and active=1
                            and tahun_anggaran=%d
                        group by id_skpd", $params['id_skpd'], $params['tahun_anggaran']), ARRAY_A);
                    $where_array = array();
                    foreach ($skpd_db as $skpd) {
                        $nama_kec = str_replace('kecamatan ', '', strtolower($skpd['nama_skpd']));
                        $where_array[] = " d.kecamatan LIKE '" . $nama_kec . "'";
                    }
                    $where .= " AND (" . implode(' OR ', $where_array) . ")";
                }

                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .=  " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . str_replace("'", '', $wpdb->prepare('%s', $params['order'][0]['dir'])) . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                    if ($recVal['status'] == 0) {
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Hapus Data"><i class="dashicons dashicons-trash"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-primary" onclick="submit_pencairan(\'' . $recVal['id'] . '\'); return false;" href="#" title="Submit Pencairan"><i class="dashicons dashicons-yes"></i></a>';
                        // 0 belum dicek, 1 diterima dan 2 ditolak
                        if ($recVal['status_ver_total'] == 2) {
                            $pesan = '';
                            if ($recVal['status_ver_total'] == 2) {
                                $pesan .= '<br>Keterangan status pencairan: ' . $recVal['ket_ver_total'];
                            }
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>' . $pesan;
                        } else {
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Menunggu Submit</span>';
                        }
                    } elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                    } elseif ($recVal['status'] == 2) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-warning btn-sm">Diverifikasi Admin</span>';
                        if (in_array("administrator", $user_meta->roles)) {
                            $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="verifikasi_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Verifikasi Data"><i class="dashicons dashicons-yes"></i></a>';
                        }
                    }
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total_pencairan'] = number_format($recVal['total_pencairan'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            } else {
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }

    public function get_pemdes_bku_dd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (!empty($_POST['nama_kec'])) {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bku_dd_desa
                        WHERE tahun_anggaran=%d
                            AND kecamatan=%s
                    ", $_POST['tahun_anggaran'], $_POST['nama_kec']), ARRAY_A);
                } else {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bku_dd_desa
                        WHERE tahun_anggaran=%d
                    ", $_POST['tahun_anggaran']), ARRAY_A);
                }
                $ret['data'] = $data;
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

    public function get_pencairan_pemdes_bku_dd($return = false)
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['total_pencairan'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        SUM(total_pencairan) 
                    FROM data_pencairan_bku_dd_desa
                    WHERE id_bku_dd=%d
                        AND status=1
                ', $_POST['id']));
                $ret['sql'] = $wpdb->last_query;
                $ret['pagu_anggaran'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        total 
                    FROM data_bku_dd_desa
                    WHERE id=%d
                        AND active=1
                ', $_POST['id']));
                if (empty($ret['total_pencairan'])) {
                    $ret['total_pencairan'] = 0;
                }
                if (empty($ret['pagu_anggaran'])) {
                    $ret['pagu_anggaran'] = 0;
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }
        if (!empty($return)) {
            return $ret;
        } else {
            die(json_encode($ret));
        }
    }

    public function get_data_pencairan_bku_dd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $data = $wpdb->get_row($wpdb->prepare("
                    select 
                        p.total_pencairan, 
                        p.id_bku_dd, 
                        p.id,
                        p.keterangan,
                        p.file_nota_dinas,
                        p.file_sptj,
                        p.file_pakta_integritas,
                        p.file_permohonan_transfer,
                        p.file_realisasi_tahap_sebelumnya,
                        p.file_dokumen_syarat_umum,
                        p.file_dokumen_syarat_khusus,
                        p.status,
                        p.status_ver_total,
                        p.ket_ver_total,
                        d.tahun_anggaran,
                        d.kecamatan,
                        d.desa
                    from data_pencairan_bku_dd_desa p
                    inner join data_bku_dd_desa d on p.id_bku_dd=d.id
                    where p.id=%d
                ", $_POST['id']), ARRAY_A);
                $ret['data'] = $data;
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

    public function hapus_data_pencairan_bku_dd_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->update('data_pencairan_bku_dd_desa', array('status' => 3), array(
                    'id' => $_POST['id']
                ));
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

    public function tambah_data_pencairan_bku_dd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if ($ret['status'] != 'error' && !empty($_POST['id_bku_dd'])) {
                    $id_bku_dd = $_POST['id_bku_dd'];
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pilih Desa Dulu!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['pagu_anggaran'])) {
                    $pagu_anggaran = $_POST['pagu_anggaran'];
                } elseif ($ret['status'] != 'error') {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pagu tidak boleh kosong!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['keterangan'])) {
                    $keterangan = $_POST['keterangan'];
                } elseif ($ret['status'] != 'error') {
                    $ret['status'] = 'error';
                    $ret['message'] = 'keterangan tidak boleh kosong!';
                }
                if (empty($_POST['id_data'])) {
                    if ($ret['status'] != 'error' && !empty($_FILES['nota_dinas'])) {
                        $nota_dinas = $_FILES['nota_dinas'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Nota dinas tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['sptj'])) {
                        $sptj = $_FILES['sptj'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'SPTJ tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pakta_integritas'])) {
                        $pakta_integritas = $_FILES['pakta_integritas'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Pakta integritas tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['permohonan_transfer'])) {
                        $permohonan_transfer = $_FILES['permohonan_transfer'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat Permohonan transfer tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['dokumen_syarat_umum'])) {
                        $dokumen_syarat_umum = $_FILES['dokumen_syarat_umum'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Dokumen syarat umum tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['dokumen_syarat_khusus'])) {
                        $dokumen_syarat_khusus = $_FILES['dokumen_syarat_khusus'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Dokumen syarat khusus tidak boleh kosong!';
                    }
                    if (
                        $ret['status'] != 'error'
                        && $pencairan['total_pencairan'] > 0
                        && !empty($_FILES['realisasi_tahap_sebelumnya'])
                    ) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Dokumen realisasi tahap sebelumnya tidak boleh kosong!';
                    }
                }

                $_POST['id'] = $id_bku_dd;
                $pencairan = $this->get_pencairan_pemdes_bku_dd(true);
                if (($pencairan['total_pencairan'] + $pagu_anggaran) > $pencairan['pagu_anggaran']) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
                }

                $status_pencairan = $_POST['status_pencairan'];
                $keterangan_status_pencairan = $_POST['keterangan_status_pencairan'];

                if ($ret['status'] != 'error') {
                    $data = array(
                        'id_bku_dd' => $id_bku_dd,
                        'total_pencairan' => $pagu_anggaran,
                        'status_ver_total' => $status_pencairan,
                        'ket_ver_total' => $keterangan_status_pencairan,
                        'keterangan' => $keterangan,
                        'update_at' => current_time('mysql')
                    );

                    if (empty($_POST['id_data'])) {
                        $data['status'] = 0; // 0 berati belum dicek
                    }

                    $path = WPSIPD_PLUGIN_PATH . 'public/media/keu_pemdes/';
                    $cek_file = array();

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['nota_dinas'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['nota_dinas'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_nota_dinas'] = $upload['filename'];
                            $cek_file['file_nota_dinas'] = $data['file_nota_dinas'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['sptj'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['sptj'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_sptj'] = $upload['filename'];
                            $cek_file['file_sptj'] = $data['file_sptj'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['pakta_integritas'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pakta_integritas'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_pakta_integritas'] = $upload['filename'];
                            $cek_file['file_pakta_integritas'] = $data['file_pakta_integritas'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['permohonan_transfer'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['permohonan_transfer'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_permohonan_transfer'] = $upload['filename'];
                            $cek_file['file_permohonan_transfer'] = $data['file_permohonan_transfer'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && $pencairan['total_pencairan'] > 0
                        && !empty($_FILES['realisasi_tahap_sebelumnya'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['realisasi_tahap_sebelumnya'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_realisasi_tahap_sebelumnya'] = $upload['filename'];
                            $cek_file['file_realisasi_tahap_sebelumnya'] = $data['file_realisasi_tahap_sebelumnya'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['dokumen_syarat_umum'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['dokumen_syarat_umum'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_dokumen_syarat_umum'] = $upload['filename'];
                            $cek_file['file_dokumen_syarat_umum'] = $data['file_dokumen_syarat_umum'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['dokumen_syarat_khusus'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['dokumen_syarat_khusus'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_dokumen_syarat_khusus'] = $upload['filename'];
                            $cek_file['file_dokumen_syarat_khusus'] = $data['file_dokumen_syarat_khusus'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if ($ret['status'] == 'error') {
                        // hapus file yang sudah terlanjur upload karena ada file yg gagal upload!
                        foreach ($cek_file as $newfile) {
                            if (is_file($path . $newfile)) {
                                unlink($path . $newfile);
                            }
                        }
                    }
                }
                if ($ret['status'] != 'error') {
                    if (!empty($_POST['id_data'])) {
                        $file_lama = $wpdb->get_row($wpdb->prepare('
                                SELECT
                                    file_nota_dinas,
                                    file_sptj,
                                    file_pakta_integritas,
                                    file_permohonan_transfer,
                                    file_realisasi_tahap_sebelumnya,
                                    file_dokumen_syarat_umum,
                                    file_dokumen_syarat_khusus
                                FROM data_pencairan_bku_dd_desa
                                WHERE id=%d
                            ', $_POST['id_data']), ARRAY_A);

                        if (
                            $file_lama['file_nota_dinas'] != $data['file_nota_dinas']
                            && is_file($path . $file_lama['file_nota_dinas'])
                        ) {
                            unlink($path . $file_lama['file_nota_dinas']);
                        }

                        if (
                            $file_lama['file_sptj'] != $data['file_sptj']
                            && is_file($path . $file_lama['file_sptj'])
                        ) {
                            unlink($path . $file_lama['file_sptj']);
                        }

                        if (
                            $file_lama['file_pakta_integritas'] != $data['file_pakta_integritas']
                            && is_file($path . $file_lama['file_pakta_integritas'])
                        ) {
                            unlink($path . $file_lama['file_pakta_integritas']);
                        }

                        if (
                            $file_lama['file_permohonan_transfer'] != $data['file_permohonan_transfer']
                            && is_file($path . $file_lama['file_permohonan_transfer'])
                        ) {
                            unlink($path . $file_lama['file_permohonan_transfer']);
                        }

                        if (
                            $file_lama['file_realisasi_tahap_sebelumnya'] != $data['file_realisasi_tahap_sebelumnya']
                            && is_file($path . $file_lama['file_realisasi_tahap_sebelumnya'])
                        ) {
                            unlink($path . $file_lama['file_realisasi_tahap_sebelumnya']);
                        }

                        if (
                            $file_lama['file_dokumen_syarat_umum'] != $data['file_dokumen_syarat_umum']
                            && is_file($path . $file_lama['file_dokumen_syarat_umum'])
                        ) {
                            unlink($path . $file_lama['file_dokumen_syarat_umum']);
                        }

                        if (
                            $file_lama['file_dokumen_syarat_khusus'] != $data['file_dokumen_syarat_khusus']
                            && is_file($path . $file_lama['file_dokumen_syarat_khusus'])
                        ) {
                            unlink($path . $file_lama['file_dokumen_syarat_khusus']);
                        }

                        $wpdb->update('data_pencairan_bku_dd_desa', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    } else {
                        $cek = $wpdb->insert('data_pencairan_bku_dd_desa', $data);
                        if (empty($cek)) {
                            $ret['status']  = 'error';
                            $ret['message'] = $wpdb->last_error;
                        }
                    }
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

    public function get_datatable_data_pencairan_bku_dd()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'd.tahun_anggaran',
                    1 => 'd.kecamatan',
                    2 => 'd.desa',
                    3 => 'p.total_pencairan',
                    4 => 'p.keterangan',
                    5 => 'p.file_nota_dinas',
                    6 => 'p.file_sptj',
                    7 => 'p.file_pakta_integritas',
                    8 => 'p.file_permohonan_transfer',
                    9 => 'p.file_realisasi_tahap_sebelumnya',
                    10 => 'p.file_dokumen_syarat_umum',
                    11 => 'p.file_dokumen_syarat_khusus',
                    12 => 'p.status',
                    13 => 'p.id_bku_dd',
                    14 => 'p.status_ver_total',
                    15 => 'p.ket_ver_total',
                    16 => 'p.id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value . ")";
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bku_dd_desa` p inner join data_bku_dd_desa d on p.id_bku_dd=d.id";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_pencairan_bku_dd_desa` p inner join data_bku_dd_desa d on p.id_bku_dd=d.id";
                $where_first = " WHERE 1=1 AND status != 3";

                if (
                    in_array("PA", $user_meta->roles)
                    || in_array("PLT", $user_meta->roles)
                    || in_array("KPA", $user_meta->roles)
                ) {
                    $skpd_db = $wpdb->get_results($wpdb->prepare("
                            SELECT 
                                nama_skpd, 
                                id_skpd, 
                                kode_skpd,
                                is_skpd
                            from data_unit 
                            where id_skpd=%d
                                and active=1
                                and tahun_anggaran=%d
                            group by id_skpd", $params['id_skpd'], $params['tahun_anggaran']), ARRAY_A);
                    $where_array = array();
                    foreach ($skpd_db as $skpd) {
                        $nama_kec = str_replace('kecamatan ', '', strtolower($skpd['nama_skpd']));
                        $where_array[] = " d.kecamatan LIKE '" . $nama_kec . "'";
                    }
                    $where .= " AND (" . implode(' OR ', $where_array) . ")";
                }

                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .=  " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . str_replace("'", '', $wpdb->prepare('%s', $params['order'][0]['dir'])) . ",  p.update_at DESC " . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                    if ($recVal['status'] == 0) {
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Hapus Data"><i class="dashicons dashicons-trash"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-primary" onclick="submit_pencairan(\'' . $recVal['id'] . '\'); return false;" href="#" title="Submit Pencairan"><i class="dashicons dashicons-yes"></i></a>';
                        // 0 belum dicek, 1 diterima dan 2 ditolak
                        if ($recVal['status_ver_total'] == 2) {
                            $pesan = '';
                            if ($recVal['status_ver_total'] == 2) {
                                $pesan .= '<br>Keterangan status pencairan: ' . $recVal['ket_ver_total'];
                            }
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>' . $pesan;
                        } else {
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Menunggu Submit</span>';
                        }
                    } elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                    } elseif ($recVal['status'] == 2) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-warning btn-sm">Diverifikasi Admin</span>';
                        if (in_array("administrator", $user_meta->roles)) {
                            $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="verifikasi_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Verifikasi Data"><i class="dashicons dashicons-yes"></i></a>';
                        }
                    }
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total_pencairan'] = number_format($recVal['total_pencairan'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            } else {
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }

    public function get_pemdes_bku_add()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (!empty($_POST['nama_kec'])) {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bku_add_desa
                        WHERE tahun_anggaran=%d
                            AND kecamatan=%s
                    ", $_POST['tahun_anggaran'], $_POST['nama_kec']), ARRAY_A);
                } else {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bku_add_desa
                        WHERE tahun_anggaran=%d
                    ", $_POST['tahun_anggaran']), ARRAY_A);
                }
                $ret['data'] = $data;
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

    public function get_pencairan_pemdes_bku_add($return = false)
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['total_pencairan'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        SUM(total_pencairan) 
                    FROM data_pencairan_bku_add_desa
                    WHERE id_bku_add=%d
                        AND status=1
                ', $_POST['id']));
                $ret['sql'] = $wpdb->last_query;
                $ret['pagu_anggaran'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        total 
                    FROM data_bku_add_desa
                    WHERE id=%d
                        AND active=1
                ', $_POST['id']));
                if (empty($ret['total_pencairan'])) {
                    $ret['total_pencairan'] = 0;
                }
                if (empty($ret['pagu_anggaran'])) {
                    $ret['pagu_anggaran'] = 0;
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }
        if (!empty($return)) {
            return $ret;
        } else {
            die(json_encode($ret));
        }
    }

    public function get_data_pencairan_bku_add_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $data = $wpdb->get_row($wpdb->prepare("
                    select 
                        p.total_pencairan, 
                        p.id_bku_add, 
                        p.id,
                        p.keterangan,
                        p.file_nota_dinas,
                        p.file_sptj,
                        p.file_pakta_integritas,
                        p.file_permohonan_transfer,
                        p.file_realisasi_tahap_sebelumnya,
                        p.file_dokumen_syarat_umum,
                        p.file_dokumen_syarat_khusus,
                        p.status,
                        p.status_ver_total,
                        p.ket_ver_total,
                        d.tahun_anggaran,
                        d.kecamatan,
                        d.desa
                    from data_pencairan_bku_add_desa p
                    inner join data_bku_add_desa d on p.id_bku_add=d.id
                    where p.id=%d
                ", $_POST['id']), ARRAY_A);
                $ret['data'] = $data;
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

    public function hapus_data_pencairan_bku_add_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->update('data_pencairan_bku_add_desa', array('status' => 3), array(
                    'id' => $_POST['id']
                ));
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

    public function tambah_data_pencairan_bku_add()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if ($ret['status'] != 'error' && !empty($_POST['id_bku_add'])) {
                    $id_bku_add = $_POST['id_bku_add'];
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pilih Desa Dulu!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['pagu_anggaran'])) {
                    $pagu_anggaran = $_POST['pagu_anggaran'];
                } elseif ($ret['status'] != 'error') {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pagu tidak boleh kosong!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['keterangan'])) {
                    $keterangan = $_POST['keterangan'];
                } elseif ($ret['status'] != 'error') {
                    $ret['status'] = 'error';
                    $ret['message'] = 'keterangan tidak boleh kosong!';
                }
                if (empty($_POST['id_data'])) {
                    if ($ret['status'] != 'error' && !empty($_FILES['nota_dinas'])) {
                        $nota_dinas = $_FILES['nota_dinas'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Nota dinas tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['sptj'])) {
                        $sptj = $_FILES['sptj'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'SPTJ tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['pakta_integritas'])) {
                        $pakta_integritas = $_FILES['pakta_integritas'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Pakta integritas tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['permohonan_transfer'])) {
                        $permohonan_transfer = $_FILES['permohonan_transfer'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Surat Permohonan transfer tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['dokumen_syarat_umum'])) {
                        $dokumen_syarat_umum = $_FILES['dokumen_syarat_umum'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Dokumen syarat umum tidak boleh kosong!';
                    }
                    if ($ret['status'] != 'error' && !empty($_FILES['dokumen_syarat_khusus'])) {
                        $dokumen_syarat_khusus = $_FILES['dokumen_syarat_khusus'];
                    } elseif ($ret['status'] != 'error') {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Dokumen syarat khusus tidak boleh kosong!';
                    }
                    if (
                        $ret['status'] != 'error'
                        && $pencairan['total_pencairan'] > 0
                        && !empty($_FILES['realisasi_tahap_sebelumnya'])
                    ) {
                        $ret['status'] = 'error';
                        $ret['message'] = 'Dokumen realisasi tahap sebelumnya tidak boleh kosong!';
                    }
                }

                $_POST['id'] = $id_bku_add;
                $pencairan = $this->get_pencairan_pemdes_bku_add(true);
                if (($pencairan['total_pencairan'] + $pagu_anggaran) > $pencairan['pagu_anggaran']) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
                }

                $status_pencairan = $_POST['status_pencairan'];
                $keterangan_status_pencairan = $_POST['keterangan_status_pencairan'];

                if ($ret['status'] != 'error') {
                    $data = array(
                        'id_bku_add' => $id_bku_add,
                        'total_pencairan' => $pagu_anggaran,
                        'status_ver_total' => $status_pencairan,
                        'ket_ver_total' => $keterangan_status_pencairan,
                        'keterangan' => $keterangan,
                        'update_at' => current_time('mysql')
                    );

                    if (empty($_POST['id_data'])) {
                        $data['status'] = 0; // 0 berati belum dicek
                    }

                    $path = WPSIPD_PLUGIN_PATH . 'public/media/keu_pemdes/';
                    $cek_file = array();

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['nota_dinas'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['nota_dinas'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_nota_dinas'] = $upload['filename'];
                            $cek_file['file_nota_dinas'] = $data['file_nota_dinas'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['sptj'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['sptj'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_sptj'] = $upload['filename'];
                            $cek_file['file_sptj'] = $data['file_sptj'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['pakta_integritas'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pakta_integritas'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_pakta_integritas'] = $upload['filename'];
                            $cek_file['file_pakta_integritas'] = $data['file_pakta_integritas'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['permohonan_transfer'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['permohonan_transfer'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_permohonan_transfer'] = $upload['filename'];
                            $cek_file['file_permohonan_transfer'] = $data['file_permohonan_transfer'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && $pencairan['total_pencairan'] > 0
                        && !empty($_FILES['realisasi_tahap_sebelumnya'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['realisasi_tahap_sebelumnya'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_realisasi_tahap_sebelumnya'] = $upload['filename'];
                            $cek_file['file_realisasi_tahap_sebelumnya'] = $data['file_realisasi_tahap_sebelumnya'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['dokumen_syarat_umum'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['dokumen_syarat_umum'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_dokumen_syarat_umum'] = $upload['filename'];
                            $cek_file['file_dokumen_syarat_umum'] = $data['file_dokumen_syarat_umum'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if (
                        $ret['status'] != 'error'
                        && !empty($_FILES['dokumen_syarat_khusus'])
                    ) {
                        $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['dokumen_syarat_khusus'], ['pdf']);
                        if ($upload['status'] == true) {
                            $data['file_dokumen_syarat_khusus'] = $upload['filename'];
                            $cek_file['file_dokumen_syarat_khusus'] = $data['file_dokumen_syarat_khusus'];
                        } else {
                            $ret['status'] = 'error';
                            $ret['message'] = $upload['message'];
                        }
                    }

                    if ($ret['status'] == 'error') {
                        // hapus file yang sudah terlanjur upload karena ada file yg gagal upload!
                        foreach ($cek_file as $newfile) {
                            if (is_file($path . $newfile)) {
                                unlink($path . $newfile);
                            }
                        }
                    }
                }
                if ($ret['status'] != 'error') {
                    if (!empty($_POST['id_data'])) {
                        $file_lama = $wpdb->get_row($wpdb->prepare('
                                SELECT
                                    file_nota_dinas,
                                    file_sptj,
                                    file_pakta_integritas,
                                    file_permohonan_transfer,
                                    file_realisasi_tahap_sebelumnya,
                                    file_dokumen_syarat_umum,
                                    file_dokumen_syarat_khusus
                                FROM data_pencairan_bku_add_desa
                                WHERE id=%d
                            ', $_POST['id_data']), ARRAY_A);

                        if (
                            $file_lama['file_nota_dinas'] != $data['file_nota_dinas']
                            && is_file($path . $file_lama['file_nota_dinas'])
                        ) {
                            unlink($path . $file_lama['file_nota_dinas']);
                        }

                        if (
                            $file_lama['file_sptj'] != $data['file_sptj']
                            && is_file($path . $file_lama['file_sptj'])
                        ) {
                            unlink($path . $file_lama['file_sptj']);
                        }

                        if (
                            $file_lama['file_pakta_integritas'] != $data['file_pakta_integritas']
                            && is_file($path . $file_lama['file_pakta_integritas'])
                        ) {
                            unlink($path . $file_lama['file_pakta_integritas']);
                        }

                        if (
                            $file_lama['file_permohonan_transfer'] != $data['file_permohonan_transfer']
                            && is_file($path . $file_lama['file_permohonan_transfer'])
                        ) {
                            unlink($path . $file_lama['file_permohonan_transfer']);
                        }

                        if (
                            $file_lama['file_realisasi_tahap_sebelumnya'] != $data['file_realisasi_tahap_sebelumnya']
                            && is_file($path . $file_lama['file_realisasi_tahap_sebelumnya'])
                        ) {
                            unlink($path . $file_lama['file_realisasi_tahap_sebelumnya']);
                        }

                        if (
                            $file_lama['file_dokumen_syarat_umum'] != $data['file_dokumen_syarat_umum']
                            && is_file($path . $file_lama['file_dokumen_syarat_umum'])
                        ) {
                            unlink($path . $file_lama['file_dokumen_syarat_umum']);
                        }

                        if (
                            $file_lama['file_dokumen_syarat_khusus'] != $data['file_dokumen_syarat_khusus']
                            && is_file($path . $file_lama['file_dokumen_syarat_khusus'])
                        ) {
                            unlink($path . $file_lama['file_dokumen_syarat_khusus']);
                        }

                        $wpdb->update('data_pencairan_bku_add_desa', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    } else {
                        $cek = $wpdb->insert('data_pencairan_bku_add_desa', $data);
                        if (empty($cek)) {
                            $ret['status']  = 'error';
                            $ret['message'] = $wpdb->last_error;
                        }
                    }
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

    public function get_datatable_data_pencairan_bku_add()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'd.tahun_anggaran',
                    1 => 'd.kecamatan',
                    2 => 'd.desa',
                    3 => 'p.total_pencairan',
                    4 => 'p.keterangan',
                    5 => 'p.file_nota_dinas',
                    6 => 'p.file_sptj',
                    7 => 'p.file_pakta_integritas',
                    8 => 'p.file_permohonan_transfer',
                    9 => 'p.file_realisasi_tahap_sebelumnya',
                    10 => 'p.file_dokumen_syarat_umum',
                    11 => 'p.file_dokumen_syarat_khusus',
                    12 => 'p.status',
                    13 => 'p.id_bku_add',
                    14 => 'p.status_ver_total',
                    15 => 'p.ket_ver_total',
                    16 => 'p.id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value . ")";
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bku_add_desa` p inner join data_bku_add_desa d on p.id_bku_add=d.id";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_pencairan_bku_add_desa` p inner join data_bku_add_desa d on p.id_bku_add=d.id";
                $where_first = " WHERE 1=1 AND status != 3";

                if (
                    in_array("PA", $user_meta->roles)
                    || in_array("PLT", $user_meta->roles)
                    || in_array("KPA", $user_meta->roles)
                ) {
                    $skpd_db = $wpdb->get_results($wpdb->prepare("
                            SELECT 
                                nama_skpd, 
                                id_skpd, 
                                kode_skpd,
                                is_skpd
                            from data_unit 
                            where id_skpd=%d
                                and active=1
                                and tahun_anggaran=%d
                            group by id_skpd", $params['id_skpd'], $params['tahun_anggaran']), ARRAY_A);
                    $where_array = array();
                    foreach ($skpd_db as $skpd) {
                        $nama_kec = str_replace('kecamatan ', '', strtolower($skpd['nama_skpd']));
                        $where_array[] = " d.kecamatan LIKE '" . $nama_kec . "'";
                    }
                    $where .= " AND (" . implode(' OR ', $where_array) . ")";
                }

                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .=  " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . str_replace("'", '', $wpdb->prepare('%s', $params['order'][0]['dir'])) . ",  p.update_at DESC " . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                    if ($recVal['status'] == 0) {
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Hapus Data"><i class="dashicons dashicons-trash"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-primary" onclick="submit_pencairan(\'' . $recVal['id'] . '\'); return false;" href="#" title="Submit Pencairan"><i class="dashicons dashicons-yes"></i></a>';
                        // 0 belum dicek, 1 diterima dan 2 ditolak
                        if ($recVal['status_ver_total'] == 2) {
                            $pesan = '';
                            if ($recVal['status_ver_total'] == 2) {
                                $pesan .= '<br>Keterangan status pencairan: ' . $recVal['ket_ver_total'];
                            }
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>' . $pesan;
                        } else {
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Menunggu Submit</span>';
                        }
                    } elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                    } elseif ($recVal['status'] == 2) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-warning btn-sm">Diverifikasi Admin</span>';
                        if (in_array("administrator", $user_meta->roles)) {
                            $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="verifikasi_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Verifikasi Data"><i class="dashicons dashicons-yes"></i></a>';
                        }
                    }
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total_pencairan'] = number_format($recVal['total_pencairan'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            } else {
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }

    public function get_pemdes_bkk_pilkades()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if (!empty($_POST['nama_kec'])) {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bkk_pilkades_desa
                        WHERE tahun_anggaran=%d
                            AND kecamatan=%s
                    ", $_POST['tahun_anggaran'], $_POST['nama_kec']), ARRAY_A);
                } else {
                    $data = $wpdb->get_results($wpdb->prepare("
                        SELECT
                            *
                        FROM data_bkk_pilkades_desa
                        WHERE tahun_anggaran=%d
                    ", $_POST['tahun_anggaran']), ARRAY_A);
                }
                $ret['data'] = $data;
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

    public function get_pencairan_pemdes_bkk_pilkades($return = false)
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['total_pencairan'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        SUM(total_pencairan) 
                    FROM data_pencairan_bkk_pilkades_desa
                    WHERE id_bkk_pilkades=%d
                        AND status=1
                ', $_POST['id']));
                $ret['sql'] = $wpdb->last_query;
                $ret['pagu_anggaran'] = $wpdb->get_var($wpdb->prepare('
                    SELECT 
                        total 
                    FROM data_bkk_pilkades_desa
                    WHERE id=%d
                        AND active=1
                ', $_POST['id']));
                if (empty($ret['total_pencairan'])) {
                    $ret['total_pencairan'] = 0;
                }
                if (empty($ret['pagu_anggaran'])) {
                    $ret['pagu_anggaran'] = 0;
                }
            } else {
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        } else {
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }
        if (!empty($return)) {
            return $ret;
        } else {
            die(json_encode($ret));
        }
    }

    public function get_data_pencairan_bkk_pilkades_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $data = $wpdb->get_row($wpdb->prepare("
                    select 
                        p.total_pencairan, 
                        p.id_bkk_pilkades, 
                        p.id,
                        p.keterangan,
                        p.status,
                        p.status_ver_total,
                        p.ket_ver_total,
                        d.tahun_anggaran,
                        d.kecamatan,
                        d.desa
                    from data_pencairan_bkk_pilkades_desa p
                    inner join data_bkk_pilkades_desa d on p.id_bkk_pilkades=d.id
                    where p.id=%d
                ", $_POST['id']), ARRAY_A);
                $ret['data'] = $data;
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

    public function hapus_data_pencairan_bkk_pilkades_by_id()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->update('data_pencairan_bkk_pilkades_desa', array('status' => 3), array(
                    'id' => $_POST['id']
                ));
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

    public function tambah_data_pencairan_bkk_pilkades()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                if ($ret['status'] != 'error' && !empty($_POST['id_bkk_pilkades'])) {
                    $id_bkk_pilkades = $_POST['id_bkk_pilkades'];
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pilih Uraian Kegiatan Dulu!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['pagu_anggaran'])) {
                    $pagu_anggaran = $_POST['pagu_anggaran'];
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pagu tidak boleh kosong!';
                }
                if ($ret['status'] != 'error' && !empty($_POST['keterangan'])) {
                    $keterangan = $_POST['keterangan'];
                } else {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Pagu tidak boleh kosong!';
                }
                $_POST['id'] = $id_bkk_pilkades;
                $pencairan = $this->get_pencairan_pemdes_bkk_pilkades(true);
                if (($pencairan['total_pencairan'] + $pagu_anggaran) > $pencairan['pagu_anggaran']) {
                    $ret['status'] = 'error';
                    $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
                }
                $validasi_pagu = $_POST['validasi_pagu'];
                $status_pencairan = $_POST['status_pencairan'];
                $keterangan_status_pencairan = $_POST['keterangan_status_pencairan'];
                if ($ret['status'] != 'error') {
                    if (empty($_POST['id_data'])) {
                        $status = 0;
                    } else {
                        $status = 1;
                        if ($status == 0) {
                            $status = 2;
                        }
                    }
                    $data = array(
                        'id_bkk_pilkades' => $id_bkk_pilkades,
                        'total_pencairan' => $pagu_anggaran,
                        'status_ver_total' => $status_pencairan,
                        'ket_ver_total' => $keterangan_status_pencairan,
                        'keterangan' => $keterangan,
                        'status' => $status,
                        'update_at' => current_time('mysql')
                    );
                    if (!empty($_POST['id_data'])) {
                        $wpdb->update('data_pencairan_bkk_pilkades_desa', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    } else {
                        $cek_id = $wpdb->get_row($wpdb->prepare('
                            SELECT
                                id,
                                active
                            FROM data_pencairan_bkk_pilkades_desa
                            WHERE id_bkk_pilkades=%s
                        ', $id_bkk_pilkades), ARRAY_A);
                        if (empty($cek_id)) {
                            $wpdb->insert('data_pencairan_bkk_pilkades_desa', $data);
                        } else {
                            if ($cek_id['active'] == 0) {
                                $wpdb->update('data_pencairan_bkk_pilkades_desa', $data, array(
                                    'id' => $cek_id['id']
                                ));
                            } else {
                                $ret['status'] = 'error';
                                $ret['message'] = 'Gagal disimpan. Data bkk_pilkades_desa dengan id_bkk_pilkades="' . $id_bkk_pilkades . '" sudah ada!';
                            }
                        }
                    }
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

    public function get_datatable_data_pencairan_bkk_pilkades()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $params = $columns = $totalRecords = $data = array();
                $params = $_REQUEST;
                $columns = array(
                    0 => 'd.tahun_anggaran',
                    1 => 'd.kecamatan',
                    2 => 'd.desa',
                    3 => 'p.total_pencairan',
                    4 => 'p.keterangan',
                    5 => 'p.status',
                    6 => 'p.id_bkk_pilkades',
                    7 => 'p.status_ver_total',
                    8 => 'p.ket_ver_total',
                    9 => 'p.id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if (!empty($params['search']['value'])) {
                    $search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
                    $where .= " AND (kecamatan LIKE " . $search_value;
                    $where .= " OR desa LIKE " . $search_value . ")";
                }
                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bkk_pilkades_desa` p inner join data_bkk_pilkades_desa d on p.id_bkk_pilkades=d.id";
                $sql = "SELECT " . implode(', ', $columns) . " FROM `data_pencairan_bkk_pilkades_desa` p inner join data_bkk_pilkades_desa d on p.id_bkk_pilkades=d.id";
                $where_first = " WHERE 1=1 AND status != 3";

                if (
                    in_array("PA", $user_meta->roles)
                    || in_array("PLT", $user_meta->roles)
                    || in_array("KPA", $user_meta->roles)
                ) {
                    $skpd_db = $wpdb->get_results($wpdb->prepare("
                        SELECT 
                            nama_skpd, 
                            id_skpd, 
                            kode_skpd,
                            is_skpd
                        from data_unit 
                        where id_skpd=%d
                            and active=1
                            and tahun_anggaran=%d
                        group by id_skpd", $params['id_skpd'], $params['tahun_anggaran']), ARRAY_A);
                    $where_array = array();
                    foreach ($skpd_db as $skpd) {
                        $nama_kec = str_replace('kecamatan ', '', strtolower($skpd['nama_skpd']));
                        $where_array[] = " d.kecamatan LIKE '" . $nama_kec . "'";
                    }
                    $where .= " AND (" . implode(' OR ', $where_array) . ")";
                }

                $sqlTot .= $sql_tot . $where_first;
                $sqlRec .= $sql . $where_first;
                if (isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if ($params['length'] != -1) {
                    $limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .=  " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . str_replace("'", '', $wpdb->prepare('%s', $params['order'][0]['dir'])) . ",  p.update_at DESC " . $limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach ($queryRecords as $recKey => $recVal) {
                    $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                    if ($recVal['status'] == 0) {
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Hapus Data"><i class="dashicons dashicons-trash"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-primary" onclick="submit_pencairan(\'' . $recVal['id'] . '\'); return false;" href="#" title="Submit Pencairan"><i class="dashicons dashicons-yes"></i></a>';
                        // 0 belum dicek, 1 diterima dan 2 ditolak
                        if ($recVal['status_ver_total'] == 2) {
                            $pesan = '';
                            if ($recVal['status_ver_total'] == 2) {
                                $pesan .= '<br>Keterangan status pencairan: ' . $recVal['ket_ver_total'];
                            }
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>' . $pesan;
                        } else {
                            $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Menunggu Submit</span>';
                        }
                    } elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                    } elseif ($recVal['status'] == 2) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-warning btn-sm">Diverifikasi Admin</span>';
                        if (in_array("administrator", $user_meta->roles)) {
                            $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="verifikasi_data(\'' . $recVal['id'] . '\'); return false;" href="#" title="Verifikasi Data"><i class="dashicons dashicons-yes"></i></a>';
                        }
                    }
                    $queryRecords[$recKey]['aksi'] = $btn;
                    $queryRecords[$recKey]['total_pencairan'] = number_format($recVal['total_pencairan'], 0, ",", ".");
                }

                $json_data = array(
                    "draw"            => intval($params['draw']),
                    "recordsTotal"    => intval($totalRecords),
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            } else {
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        } else {
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }

    public function verifikasi_pencairan_desa()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil verifikasi data!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $user_id = um_user('ID');
                $user_meta = get_userdata($user_id);
                $tipe = $_POST['tipe'];
                $jenis_belanja = $_POST['jenis_belanja'];
                $table = '';
                if ($tipe == 'submit_pencairan') {
                    if ($jenis_belanja == 'bkk') {
                        $table = 'data_pencairan_bkk_desa';
                    } else if ($jenis_belanja == 'bhpd') {
                        $table = 'data_pencairan_bhpd_desa';
                    } else if ($jenis_belanja == 'bhrd') {
                        $table = 'data_pencairan_bhrd_desa';
                    } else if ($jenis_belanja == 'bku_add') {
                        $table = 'data_pencairan_bku_add_desa';
                    } else if ($jenis_belanja == 'bku_dd') {
                        $table = 'data_pencairan_bku_dd_desa';
                    } else if ($jenis_belanja == 'bkk_pilkades') {
                        $table = 'data_pencairan_bkk_pilkades_desa';
                    } else {
                        $ret = array(
                            'status' => 'error',
                            'message'   => 'Jenis belanja tidak ditemukan!'
                        );
                    }
                    if ($ret['status'] != 'error') {
                        $wpdb->update($table, array(
                            'status' => 2, // dirubrah jadi diverifikasi admin
                            'update_at' => current_time('mysql')
                        ), array(
                            'id' => $_POST['id']
                        ));
                    }
                } else if ($tipe == 'verifikasi_admin') {
                    if (in_array("administrator", $user_meta->roles)) {
                        if ($jenis_belanja == 'bkk') {
                            $table = 'data_pencairan_bkk_desa';
                        } else if ($jenis_belanja == 'bhpd') {
                            $table = 'data_pencairan_bhpd_desa';
                        } else if ($jenis_belanja == 'bhrd') {
                            $table = 'data_pencairan_bhrd_desa';
                        } else if ($jenis_belanja == 'bku_add') {
                            $table = 'data_pencairan_bku_add_desa';
                        } else if ($jenis_belanja == 'bku_dd') {
                            $table = 'data_pencairan_bku_dd_desa';
                        } else if ($jenis_belanja == 'bkk_pilkades') {
                            $table = 'data_pencairan_bkk_pilkades_desa';
                        } else {
                            $ret = array(
                                'status' => 'error',
                                'message'   => 'Jenis belanja tidak ditemukan!'
                            );
                        }
                        if ($ret['status'] != 'error') {
                            $opsi = array(
                                'status_ver_total' => $_POST['status_pencairan'],
                                'ket_ver_total' => $_POST['keterangan_status_pencairan'],
                                'status' => 0, // status dirubah jadi menunggu submit
                                'update_at' => current_time('mysql')
                            );
                            if (
                                $_POST['status_pencairan'] == 2
                                && empty($_POST['keterangan_status_pencairan'])
                            ) {
                                $ret = array(
                                    'status' => 'error',
                                    'message'   => 'Keterangan status pencairan tidak boleh kosong!'
                                );
                            } else {
                                if ($_POST['status_pencairan'] == 1) {
                                    $opsi['status'] = 1; // status dirubah jadi diterima
                                }
                                $wpdb->update($table, $opsi, array(
                                    'id' => $_POST['id']
                                ));
                            }
                        }
                    } else {
                        $ret = array(
                            'status' => 'error',
                            'message'   => 'User tidak memiliki akses!'
                        );
                    }
                } else {
                    $ret = array(
                        'status' => 'error',
                        'message'   => 'Tipe verifikasi tidak ditemukan!'
                    );
                }
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

    public function get_sumber_dana_desa()
    {
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data sumber dana!',
            'data'  => array()
        );

        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
                $ret['data'] = $wpdb->get_results($wpdb->prepare("
                    SELECT
                        id_dana,
                        kode_dana,
                        nama_dana
                    FROM data_sumber_dana 
                    WHERE active = 1
                        AND tahun=%d
                        AND set_input='Ya'
                ", $_POST['tahun_anggaran']), ARRAY_A);
            // print_r($ret['data']); die($wpdb->last_query);
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
}
