<?php
class Wpsipd_Public_Keu_Pemdes
{

    public function keu_pemdes_bhpd($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bhpd.php';
    }

    public function keu_pemdes_bhrd($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bhrd.php';
    }

    public function management_data_bkk_infrastruktur($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bkk-infrastuktur.php';
    }

    public function keu_pemdes_bkk_inf($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bkk-rekapitulasi.php';
    }

    public function monitor_keu_pemdes($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-desa.php';
    }

    public function management_data_bhpd($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-management-bhpd.php';
    }

    public function management_data_bhrd($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-management-bhrd.php';
    }

    public function input_pencairan_bkk($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bkk.php';
    }

    public function get_data_bkk_infrastruktur_by_id(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data' => array()
        );
        if(!empty($_POST)){
            if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                $ret['data'] = $wpdb->get_row($wpdb->prepare('
                    SELECT 
                        *
                    FROM data_bkk_desa
                    WHERE id=%d
                ', $_POST['id']), ARRAY_A);
            }else{
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }

    public function hapus_data_bkk_infrastruktur_by_id(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );
        if(!empty($_POST)){
            if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                $ret['data'] = $wpdb->update('data_bkk_desa', array('active' => 0), array(
                    'id' => $_POST['id']
                ));
            }else{
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }

    public function tambah_data_bkk_infrastruktur(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if(!empty($_POST)){
            if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                if($ret['status'] != 'error' && !empty($_POST['id_desa'])){
                    $id_desa = $_POST['id_desa'];
                }else{
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id_desa tidak boleh kosong!';
                }
                if($ret['status'] != 'error' && !empty($_POST['desa'])){
                    $desa = $_POST['desa'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data desa tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['kecamatan'])){
                    $kecamatan = $_POST['kecamatan'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data kecamatan tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['id_kecamatan'])){
                    $id_kecamatan = $_POST['id_kecamatan'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data id_kecamatan tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['kegiatan'])){
                    $kegiatan = $_POST['kegiatan'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data kegiatan tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['alamat'])){
                    $alamat = $_POST['alamat'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data alamat tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['total'])){
                    $total = $_POST['total'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data total tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['id_dana'])){
                    $id_dana = $_POST['id_dana'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data id_dana tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['sumber_dana'])){
                    $sumber_dana = $_POST['sumber_dana'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data sumber_dana tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['tahun_anggaran'])){
                    $tahun_anggaran = $_POST['tahun_anggaran'];
                 }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data tahun_anggaran tidak boleh kosong!';
                // }
                if($ret['status'] != 'error'){
                    $data = array(
                        'id_desa' => $id_desa,
                        'desa' => $desa,
                        'kecamatan' => $kecamatan,
                        'id_kecamatan' => $id_kecamatan,
                        'kegiatan' => $kegiatan,
                        'alamat' => $alamat,
                        'total' => $total,
                        'id_dana' => $id_dana,
                        'sumber_dana' => $sumber_dana,
                        'tahun_anggaran' => $tahun_anggaran,
                        'active' => 1,
                        'update_at' => current_time('mysql')
                    );
                    if(!empty($_POST['id_data'])){
                        $wpdb->update('data_bkk_desa', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    }else{
                        $cek_id = $wpdb->get_row($wpdb->prepare('
                            SELECT
                                id,
                                active
                            FROM data_bkk_desa
                            WHERE id_bkk_desa=%s
                        ', $id_bkk_desa), ARRAY_A);
                        if(empty($cek_id)){
                            $wpdb->insert('data_bkk_desa', $data);
                        }else{
                            if($cek_id['active'] == 0){
                                $wpdb->update('data_bkk_desa', $data, array(
                                    'id' => $cek_id['id']
                                ));
                            }else{
                                $ret['status'] = 'error';
                                $ret['message'] = 'Gagal disimpan. Data bkk_desa dengan id_bkk_desa="'.$id_bkk_desa.'" sudah ada!';
                            }
                        }
                    }
                }
            }else{
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }
    
    public function get_datatable_bkk_infrastruktur(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if(!empty($_POST)){
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                $user_id = um_user( 'ID' );
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
                if( !empty($params['search']['value']) ) {
                    $where .=" AND ( id_bkk_desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");  
                    $where .=" OR total LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bkk_desa`";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_bkk_desa`";
                $where_first = " WHERE 1=1 AND active = 1";
                $sqlTot .= $sql_tot.$where_first;
                $sqlRec .= $sql.$where_first;
                if(isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if($params['length'] != -1){
                    $limit = "  LIMIT ".$wpdb->prepare('%d', $params['start'])." ,".$wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir'].$limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach($queryRecords as $recKey => $recVal){
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                    $btn .= '<a class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    $queryRecords[$recKey]['aksi'] = $btn;
                }

                $json_data = array(
                    "draw"            => intval( $params['draw'] ),   
                    "recordsTotal"    => intval( $totalRecords ),  
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            }else{
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        }else{
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }  

public function get_data_bhpd_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil get data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->get_row($wpdb->prepare('
                SELECT 
                    *
                FROM data_bhpd_desa
                WHERE id=%d
            ', $_POST['id']), ARRAY_A);
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }

    die(json_encode($ret));
}

public function hapus_data_bhpd_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil hapus data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->update('data_bhpd_desa', array('active' => 0), array(
                'id' => $_POST['id']
            ));
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }

    die(json_encode($ret));
}

public function tambah_data_bhpd(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil simpan data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            if($ret['status'] != 'error' && !empty($_POST['id_desa'])){
                $id_desa = $_POST['id_desa'];
            }else{
                $ret['status'] = 'error';
                $ret['message'] = 'Data id_desa tidak boleh kosong!';
            }
            if($ret['status'] != 'error' && !empty($_POST['desa'])){
                $desa = $_POST['desa'];
            }
            // else{
            //  $ret['status'] = 'error';
            //  $ret['message'] = 'Data desa tidak boleh kosong!';
            // }
            if($ret['status'] != 'error' && !empty($_POST['kecamatan'])){
                $kecamatan = $_POST['kecamatan'];
            }
            // else{
            //  $ret['status'] = 'error';
            //  $ret['message'] = 'Data kecamatan tidak boleh kosong!';
            // }
            if($ret['status'] != 'error' && !empty($_POST['id_kecamatan'])){
                $id_kecamatan = $_POST['id_kecamatan'];
            }
            // else{
            //  $ret['status'] = 'error';
            //  $ret['message'] = 'Data id_kecamatan tidak boleh kosong!';
            // }
            if($ret['status'] != 'error' && !empty($_POST['kegiatan'])){
                $kegiatan = $_POST['kegiatan'];
            }
            if($ret['status'] != 'error' && !empty($_POST['total'])){
                $total = $_POST['total'];
            }
            // else{
            //  $ret['status'] = 'error';
            //  $ret['message'] = 'Data total tidak boleh kosong!';
            // }
            if($ret['status'] != 'error' && !empty($_POST['id_dana'])){
                $id_dana = $_POST['id_dana'];
            }
            if($ret['status'] != 'error' && !empty($_POST['tahun_anggaran'])){
                $tahun_anggaran = $_POST['tahun_anggaran'];
             }
            // else{
            //  $ret['status'] = 'error';
            //  $ret['message'] = 'Data tahun_anggaran tidak boleh kosong!';
            // }
            if($ret['status'] != 'error'){
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
                if(!empty($_POST['id_data'])){
                    $wpdb->update('data_bhpd_desa', $data, array(
                        'id' => $_POST['id_data']
                    ));
                    $ret['message'] = 'Berhasil update data!';
                }else{
                    $cek_id = $wpdb->get_row($wpdb->prepare('
                        SELECT
                            id,
                            active
                        FROM data_bhpd_desa
                        WHERE id_bhpd_desa=%s
                    ', $id_bhpd_desa), ARRAY_A);
                    if(empty($cek_id)){
                        $wpdb->insert('data_bhpd_desa', $data);
                    }else{
                        if($cek_id['active'] == 0){
                            $wpdb->update('data_bhpd_desa', $data, array(
                                'id' => $cek_id['id']
                            ));
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal disimpan. Data bhpd_desa dengan id_bhpd_desa="'.$id_bhpd_desa.'" sudah ada!';
                        }
                    }
                }
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }

    die(json_encode($ret));
}

public function get_datatable_bhpd(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if(!empty($_POST)){
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                $user_id = um_user( 'ID' );
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
                if( !empty($params['search']['value']) ) {
                    $where .=" AND ( id_bhpd_desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");  
                    $where .=" OR total LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bhpd_desa`";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_bhpd_desa`";
                $where_first = " WHERE 1=1 AND active = 1";
                $sqlTot .= $sql_tot.$where_first;
                $sqlRec .= $sql.$where_first;
                if(isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if($params['length'] != -1){
                    $limit = "  LIMIT ".$wpdb->prepare('%d', $params['start'])." ,".$wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir'].$limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach($queryRecords as $recKey => $recVal){
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                    $btn .= '<a class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    $queryRecords[$recKey]['aksi'] = $btn;
                }

                $json_data = array(
                    "draw"            => intval( $params['draw'] ),   
                    "recordsTotal"    => intval( $totalRecords ),  
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            }else{
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        }else{
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    }  
    public function get_data_bhrd_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil get data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->get_row($wpdb->prepare('
                SELECT 
                    *
                FROM data_bhrd_desa
                WHERE id=%d
            ', $_POST['id']), ARRAY_A);
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }

    die(json_encode($ret));
}

    public function hapus_data_bhrd_by_id(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );
        if(!empty($_POST)){
            if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                $ret['data'] = $wpdb->update('data_bhrd_desa', array('active' => 0), array(
                    'id' => $_POST['id']
                ));
            }else{
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }

    public function tambah_data_bhrd(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if(!empty($_POST)){
            if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                if($ret['status'] != 'error' && !empty($_POST['id_desa'])){
                    $id_desa = $_POST['id_desa'];
                }else{
                    $ret['status'] = 'error';
                    $ret['message'] = 'Data id_desa tidak boleh kosong!';
                }
                if($ret['status'] != 'error' && !empty($_POST['desa'])){
                    $desa = $_POST['desa'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data desa tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['kecamatan'])){
                    $kecamatan = $_POST['kecamatan'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data kecamatan tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['id_kecamatan'])){
                    $id_kecamatan = $_POST['id_kecamatan'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data id_kecamatan tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['kegiatan'])){
                    $kegiatan = $_POST['kegiatan'];
                }
                if($ret['status'] != 'error' && !empty($_POST['total'])){
                    $total = $_POST['total'];
                }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data total tidak boleh kosong!';
                // }
                if($ret['status'] != 'error' && !empty($_POST['id_dana'])){
                    $id_dana = $_POST['id_dana'];
                }
                if($ret['status'] != 'error' && !empty($_POST['tahun_anggaran'])){
                    $tahun_anggaran = $_POST['tahun_anggaran'];
                 }
                // else{
                //  $ret['status'] = 'error';
                //  $ret['message'] = 'Data tahun_anggaran tidak boleh kosong!';
                // }
                if($ret['status'] != 'error'){
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
                    if(!empty($_POST['id_data'])){
                        $wpdb->update('data_bhrd_desa', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    }else{
                        $cek_id = $wpdb->get_row($wpdb->prepare('
                            SELECT
                                id,
                                active
                            FROM data_bhrd_desa
                            WHERE id_bhrd_desa=%s
                        ', $id_bhrd_desa), ARRAY_A);
                        if(empty($cek_id)){
                            $wpdb->insert('data_bhrd_desa', $data);
                        }else{
                            if($cek_id['active'] == 0){
                                $wpdb->update('data_bhrd_desa', $data, array(
                                    'id' => $cek_id['id']
                                ));
                            }else{
                                $ret['status'] = 'error';
                                $ret['message'] = 'Gagal disimpan. Data bhrd_desa dengan id_bhrd_desa="'.$id_bhrd_desa.'" sudah ada!';
                            }
                        }
                    }
                }
            }else{
                $ret['status']  = 'error';
                $ret['message'] = 'Api key tidak ditemukan!';
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Format Salah!';
        }

        die(json_encode($ret));
    }

    public function get_datatable_bhrd(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if(!empty($_POST)){
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                $user_id = um_user( 'ID' );
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
                if( !empty($params['search']['value']) ) {
                    $where .=" AND ( id_bhrd_desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");  
                    $where .=" OR total LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bhrd_desa`";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_bhrd_desa`";
                $where_first = " WHERE 1=1 AND active = 1";
                $sqlTot .= $sql_tot.$where_first;
                $sqlRec .= $sql.$where_first;
                if(isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if($params['length'] != -1){
                    $limit = "  LIMIT ".$wpdb->prepare('%d', $params['start'])." ,".$wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir'].$limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach($queryRecords as $recKey => $recVal){
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                    $btn .= '<a class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    $queryRecords[$recKey]['aksi'] = $btn;
                }

                $json_data = array(
                    "draw"            => intval( $params['draw'] ),   
                    "recordsTotal"    => intval( $totalRecords ),  
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            }else{
                $ret = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        }else{
            $ret = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($ret));
    }  

    public function get_pemdes_bkk(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil get data!',
            'data'  => array()
        );

        if(!empty($_POST)){
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                $data = $wpdb->get_results($wpdb->prepare("
                    SELECT
                        *
                    FROM data_bkk_desa
                    WHERE tahun_anggaran=%d
                ", $_POST['tahun_anggaran']), ARRAY_A);
                $ret['data'] = $data;
            }else{
                $ret = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        }else{
            $ret = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($ret));
    }

public function get_data_pencairan_bkk_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil get data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $data = $wpdb->get_row($wpdb->prepare("
                select 
                    p.total_pencairan, 
                    p.id_kegiatan, 
                    p.id,
                    p.file_proposal,
                    p.status,
                    p.status_ver_total,
                    p.ket_ver_total,
                    p.status_ver_proposal,
                    p.ket_ver_proposal,
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
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }

    die(json_encode($ret));   
}

public function hapus_data_pencairan_bkk_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil hapus data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->update('data_pencairan_bkk_desa', array('status' => 0), array(
                'id' => $_POST['id']
            ));
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }

    die(json_encode($ret));
}

public function tambah_data_pencairan_bkk(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil simpan data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            if($ret['status'] != 'error' && !empty($_POST['id_kegiatan'])){
                $id_kegiatan = $_POST['id_kegiatan'];
            }else{
                $ret['status'] = 'error';
                $ret['message'] = 'Pilih Uraian Kegiatan Dulu!';
            }
            if($ret['status'] != 'error' && !empty($_POST['pagu_anggaran'])){
                $pagu_anggaran = $_POST['pagu_anggaran'];
            }else{
                $ret['status'] = 'error';
                $ret['message'] = 'Pagu tidak boleh kosong!';
            }
            if($ret['status'] != 'error' && !empty($_POST['proposal'])){
                $proposal = $_POST['proposal'];
            }
            $status_pagu = $_POST['status_pagu'];
            $keterangan_status_pagu = $_POST['keterangan_status_pagu'];
            $status_file = $_POST['status_file'];
            $keterangan_status_file = $_POST['keterangan_status_file'];
            if($ret['status'] != 'error'){
                $status = 1;
                if($status_pagu == 0 || $status_file == 0){
                    $status = 2;
                }
                $data = array(
                    'id_kegiatan' => $id_kegiatan,
                    'total_pencairan' => $pagu_anggaran,
                    'status_ver_total' => $status_pagu,
                    'ket_ver_total' => $keterangan_status_pagu,
                    'status_ver_proposal' => $status_file,
                    'ket_ver_proposal' => $keterangan_status_file,
                    'file_proposal' => '',
                    'status' => $status,
                    'update_at' => current_time('mysql')
                );
                if(!empty($_POST['id_data'])){
                    $wpdb->update('data_pencairan_bkk_desa', $data, array(
                        'id' => $_POST['id_data']
                    ));
                    $ret['message'] = 'Berhasil update data!';
                }else{
                    $cek_id = $wpdb->get_row($wpdb->prepare('
                        SELECT
                            id,
                            active
                        FROM data_pencairan_bkk_desa
                        WHERE id_bkk_desa=%s
                    ', $id_bkk_desa), ARRAY_A);
                    if(empty($cek_id)){
                        $wpdb->insert('data_pencairan_bkk_desa', $data);
                    }else{
                        if($cek_id['active'] == 0){
                            $wpdb->update('data_pencairan_bkk_desa', $data, array(
                                'id' => $cek_id['id']
                            ));
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal disimpan. Data bkk_desa dengan id_bkk_desa="'.$id_bkk_desa.'" sudah ada!';
                        }
                    }
                }
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }

    die(json_encode($ret));
}
    
public function get_datatable_data_pencairan_bkk(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil get data!',
        'data'  => array()
    );

        if(!empty($_POST)){
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                $user_id = um_user( 'ID' );
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
                  6 => 'p.file_proposal',
                  7 => 'p.status',
                  8 => 'p.id_kegiatan',
                  9 => 'p.status_ver_total',
                 10 => 'p.ket_ver_total',
                 11 => 'p.status_ver_proposal',
                 12 => 'p.ket_ver_proposal',
                 13 => 'p.id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if( !empty($params['search']['value']) ) {
                    $where .=" AND ( d.kegiatan LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");  
                    $where .=" OR d.desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                    $where .=" OR d.alamat LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bkk_desa` p inner join data_bkk_desa d on p.id_kegiatan=d.id";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_pencairan_bkk_desa` p inner join data_bkk_desa d on p.id_kegiatan=d.id";
                $where_first = " WHERE 1=1 AND status >= 1";
                $sqlTot .= $sql_tot.$where_first;
                $sqlRec .= $sql.$where_first;
                if(isset($where) && $where != '') {
                    $sqlTot .= $where;
                    $sqlRec .= $where;
                }

                $limit = '';
                if($params['length'] != -1){
                    $limit = "  LIMIT ".$wpdb->prepare('%d', $params['start'])." ,".$wpdb->prepare('%d', $params['length']);
                }
                $sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".str_replace("'", '', $wpdb->prepare('%s', $params['order'][0]['dir'])).$limit;

                $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
                $totalRecords = $queryTot[0]['jml'];
                $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

                foreach($queryRecords as $recKey => $recVal){
                    $btn = '<a class="btn btn-sm btn-warning" onclick="edit_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                    $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    $queryRecords[$recKey]['aksi'] = $btn;
                    if($recVal['status'] == 0){
                        $queryRecords[$recKey]['status'] = 'Belum dicek';
                    }elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = 'Diterima';
                    }elseif ($recVal['status'] == 2) {
                        $pesan = '';
                        if ($recVal['status_ver_total'] == 0){
                            $pesan .= '<br>Keterangan Pagu: '.$recVal['ket_ver_total']; 
                        }
                        if ($recVal['status_ver_proposal'] == 0){
                            $pesan .= '<br>Keterangan Proposal: '.$recVal['ket_ver_proposal'];
                        }
                        $queryRecords[$recKey]['status'] = 'Ditolak <br>'.$pesan;
                    }
                }

                $json_data = array(
                    "draw"            => intval( $params['draw'] ),   
                    "recordsTotal"    => intval( $totalRecords ),  
                    "recordsFiltered" => intval($totalRecords),
                    "data"            => $queryRecords,
                    "sql"             => $sqlRec
                );

                die(json_encode($json_data));
            }else{
                $return = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        }else{
            $return = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }
        die(json_encode($return));
    } 
}
