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

    public function laporan_keu_pemdes_per_kecamatan($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-laporan-per-kecamatan.php';
    }

    public function keu_pemdes_bku_add($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bku-add.php';
    }

    public function keu_pemdes_beranda($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-beranda.php';
    }

    public function keu_pemdes_bku_dd($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bku-dd.php';
    }

    public function management_data_bkk_infrastruktur($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bkk-infrastruktur.php';
    }

    public function keu_pemdes_bkk_pilkades($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bkk-pilkades.php';
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

    public function management_data_bku_dd($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-management-bku-dd.php';
    }

    public function management_data_bku_add($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-management-bku-add.php';
    }

    public function management_data_bkk_pilkades($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-management-bkk-pilkades.php';
    }

    public function input_pencairan_bkk($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bkk.php';
    }

    public function input_pencairan_bhpd($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bhpd.php';
    }

    public function input_pencairan_bhrd($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bhrd.php';
    }

    public function input_pencairan_bku_dd($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bku-dd.php';
    }

    public function input_pencairan_bku_add($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bku-add.php';
    }

    public function input_pencairan_bkk_pilkades($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-input-pencairan-bkk-pilkades.php';
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
                    $where .=" AND ( desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");  
                    $where .=" OR kecamatan LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
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

public function get_data_bku_dd_by_id(){
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
                FROM data_bku_dd_desa
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

public function hapus_data_bku_dd_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil hapus data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->update('data_bku_dd_desa', array('active' => 0), array(
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

public function tambah_data_bku_dd(){
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
                    $wpdb->update('data_bku_dd_desa', $data, array(
                        'id' => $_POST['id_data']
                    ));
                    $ret['message'] = 'Berhasil update data!';
                }else{
                    $cek_id = $wpdb->get_row($wpdb->prepare('
                        SELECT
                            id,
                            active
                        FROM data_bku_dd_desa
                        WHERE id_bku_dd_desa=%s
                    ', $id_bku_dd_desa), ARRAY_A);
                    if(empty($cek_id)){
                        $wpdb->insert('data_bku_dd_desa', $data);
                    }else{
                        if($cek_id['active'] == 0){
                            $wpdb->update('data_bku_dd_desa', $data, array(
                                'id' => $cek_id['id']
                            ));
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal disimpan. Data bku_dd_desa dengan id_bku_dd_desa="'.$id_bku_dd_desa.'" sudah ada!';
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

public function get_datatable_bku_dd(){
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
                    $where .=" AND ( id_bku_dd_desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");  
                    $where .=" OR total LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bku_dd_desa`";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_bku_dd_desa`";
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
                    $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
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

public function get_data_bku_add_by_id(){
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
                FROM data_bku_add_desa
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

public function hapus_data_bku_add_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil hapus data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->update('data_bku_add_desa', array('active' => 0), array(
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

public function tambah_data_bku_add(){
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
                    $wpdb->update('data_bku_add_desa', $data, array(
                        'id' => $_POST['id_data']
                    ));
                    $ret['message'] = 'Berhasil update data!';
                }else{
                    $cek_id = $wpdb->get_row($wpdb->prepare('
                        SELECT
                            id,
                            active
                        FROM data_bku_add_desa
                        WHERE id_bku_add_desa=%s
                    ', $id_bku_add_desa), ARRAY_A);
                    if(empty($cek_id)){
                        $wpdb->insert('data_bku_add_desa', $data);
                    }else{
                        if($cek_id['active'] == 0){
                            $wpdb->update('data_bku_add_desa', $data, array(
                                'id' => $cek_id['id']
                            ));
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal disimpan. Data bku_add_desa dengan id_bku_add_desa="'.$id_bku_add_desa.'" sudah ada!';
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

public function get_datatable_bku_add(){
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
                    $where .=" AND ( id_bku_add_desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");  
                    $where .=" OR total LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bku_add_desa`";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_bku_add_desa`";
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

public function get_data_bkk_pilkades_by_id(){
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
                FROM data_bkk_pilkades_desa
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

public function hapus_data_bkk_pilkades_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil hapus data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->update('data_bkk_pilkades_desa', array('active' => 0), array(
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

public function tambah_data_bkk_pilkades(){
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
                    $wpdb->update('data_bkk_pilkades_desa', $data, array(
                        'id' => $_POST['id_data']
                    ));
                    $ret['message'] = 'Berhasil update data!';
                }else{
                    $cek_id = $wpdb->get_row($wpdb->prepare('
                        SELECT
                            id,
                            active
                        FROM data_bkk_pilkades_desa
                        WHERE id_bkk_pilkades_desa=%s
                    ', $id_bkk_pilkades_desa), ARRAY_A);
                    if(empty($cek_id)){
                        $wpdb->insert('data_bkk_pilkades_desa', $data);
                    }else{
                        if($cek_id['active'] == 0){
                            $wpdb->update('data_bkk_pilkades_desa', $data, array(
                                'id' => $cek_id['id']
                            ));
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal disimpan. Data bkk_pilkades_desa dengan id_bkk_pilkades_desa="'.$id_bkk_pilkades_desa.'" sudah ada!';
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

public function get_datatable_bkk_pilkades(){
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
                    $where .=" AND kecamatan LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                    $where .=" OR desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(id) as jml FROM `data_bkk_pilkades_desa`";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_bkk_pilkades_desa`";
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
                    $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
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

public function get_pencairan_pemdes_bkk($return = false){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil get data!'
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
            if(empty($ret['total_pencairan'])){
                $ret['total_pencairan'] = 0;
            }
            if(empty($ret['pagu_anggaran'])){
                $ret['pagu_anggaran'] = 0;
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }
    if(!empty($return)){
        return $ret;
    }else{
        die(json_encode($ret));
    }
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
            $ret['data'] = $wpdb->update('data_pencairan_bkk_desa', array('status' => 3), array(
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
            }elseif($ret['status'] != 'error'){
                $ret['status'] = 'error';
                $ret['message'] = 'Pagu tidak boleh kosong!';
            }
            if($ret['status'] != 'error' && !empty($_FILES['nota_dinas'])){
                $nota_dinas = $_FILES['nota_dinas'];
            }elseif($ret['status'] != 'error'){
                $ret['status'] = 'error';
                $ret['message'] = 'Nota dinas tidak boleh kosong!';
            }
            if($ret['status'] != 'error' && !empty($_FILES['sptjm'])){
                $sptjm = $_FILES['sptjm'];
            }elseif($ret['status'] != 'error'){
                $ret['status'] = 'error';
                $ret['message'] = 'SPTJM tidak boleh kosong!';
            }
            if($ret['status'] != 'error' && !empty($_FILES['pakta_integritas'])){
                $pakta_integritas = $_FILES['pakta_integritas'];
            }elseif($ret['status'] != 'error'){
                $ret['status'] = 'error';
                $ret['message'] = 'Pakta integritas tidak boleh kosong!';
            }
            if($ret['status'] != 'error' && !empty($_POST['keterangan'])){
                $keterangan = $_POST['keterangan'];
            }
            $_POST['id'] = $id_kegiatan;
            $pencairan = $this->get_pencairan_pemdes_bkk(true);
            if(($pencairan['total_pencairan']+$pagu_anggaran) > $pencairan['pagu_anggaran']){
                $ret['status'] = 'error';
                $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
            }
            $status_pagu = $_POST['status_pagu'];
            $keterangan_status_pagu = $_POST['keterangan_status_pagu'];
            $status_file = $_POST['status_file'];
            $keterangan_status_file = $_POST['keterangan_status_file'];
            if($ret['status'] != 'error'){
                if(empty($_POST['id_data'])){
                    $status = 0 ; // 0 berati belum dicek
                }else{
                    $status = 1; // 1 diterima
                    if($status_pagu == 0 || $status_file == 0){
                        $status = 2; // 2 ditolak
                    }
                }
                $data = array(
                    'id_kegiatan' => $id_kegiatan,
                    'total_pencairan' => $pagu_anggaran,
                    'status_ver_total' => $status_pagu,
                    'ket_ver_total' => $keterangan_status_pagu,
                    'status_ver_proposal' => $status_file,
                    'ket_ver_proposal' => $keterangan_status_file,
                    'file_nota_dinas' => '',
                    'file_sptjm' => '',
                    'file_pakta_integritas' => '',
                    'status' => $status,
                    'update_at' => current_time('mysql')
                );
                $path = WPSIPD_PLUGIN_PATH.'public/media/keu_pemdes/';

                $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['nota_dinas'], ['pdf']);
                if($upload['status']){
                    $data['file_nota_dinas'] = $upload['filename'];
                }else{
                    $ret['status'] = 'error';
                    $ret['message'] = $upload['message'];
                }

                $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['sptjm'], ['pdf']);
                if($upload['status']){
                    $data['file_sptjm'] = $upload['filename'];
                }else{
                    $ret['status'] = 'error';
                    $ret['message'] = $upload['message'];
                }
                
                $upload = CustomTrait::uploadFile($_POST['api_key'], $path, $_FILES['pakta_integritas'], ['pdf']);
                if($upload['status']){
                    $data['file_pakta_integritas'] = $upload['filename'];
                }else{
                    $ret['status'] = 'error';
                    $ret['message'] = $upload['message'];
                }

                if($ret['status'] != 'error'){
                    if(!empty($_POST['id_data'])){
                        $file_lama = $wpdb->get_row($wpdb->prepare('
                            SELECT
                                file_nota_dinas,
                                sptjm,
                                pakta_integritas
                            FROM data_pencairan_bkk_desa
                            WHERE id=%d
                        ', $_POST['id_data']), ARRAY_A);

                        if(
                            $file_lama['file_nota_dinas'] != $data['file_nota_dinas'] 
                            && is_file($path.$file_lama['file_nota_dinas'])
                        ){
                            unlink($path.$file_lama['file_nota_dinas']);
                        }

                        if(
                            $file_lama['file_sptjm'] != $data['file_sptjm'] 
                            && is_file($path.$file_lama['file_sptjm'])
                        ){
                            unlink($path.$file_lama['file_sptjm']);
                        }

                        if(
                            $file_lama['file_pakta_integritas'] != $data['file_pakta_integritas'] 
                            && is_file($path.$file_lama['file_pakta_integritas'])
                        ){
                            unlink($path.$file_lama['file_pakta_integritas']);
                        }

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
                    $where .=" AND d.kegiatan LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                    $where .=" AND kecamatan LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                    $where .=" AND alamat LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bkk_desa` p inner join data_bkk_desa d on p.id_kegiatan=d.id";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_pencairan_bkk_desa` p inner join data_bkk_desa d on p.id_kegiatan=d.id";
                $where_first = " WHERE 1=1 AND status != 3";
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
                    $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\''.$recVal['id'].'\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                    if ($recVal['status'] != 1) {
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    }
                    $queryRecords[$recKey]['file_proposal'] = '<a href="'.esc_url(plugin_dir_url(__DIR__).'public/media/keu_pemdes/').$recVal['file_proposal'].'" target="_blank">'.$recVal['file_proposal'].'</a>';
                    $queryRecords[$recKey]['aksi'] = $btn;
                    if($recVal['status'] == 0){
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Belum dicek</span>';
                    }elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                    }elseif ($recVal['status'] == 2) {
                        $pesan = '';
                        if ($recVal['status_ver_total' && 'status_ver_proposal'] == 0){
                            $pesan .= '<br>Keterangan Pagu: '.$recVal['ket_ver_total']; 
                            $pesan .= '<br>Keterangan File: '.$recVal['ket_ver_proposal']; 
                        }
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>'.$pesan;
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

public function get_pemdes_bhpd(){
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
                FROM data_bhpd_desa
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

public function get_pencairan_pemdes_bhpd($return = false){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil get data!'
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
            if(empty($ret['total_pencairan'])){
                $ret['total_pencairan'] = 0;
            }
            if(empty($ret['pagu_anggaran'])){
                $ret['pagu_anggaran'] = 0;
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }
    if(!empty($return)){
        return $ret;
    }else{
        die(json_encode($ret));
    }
}

public function get_data_pencairan_bhpd_by_id(){
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
                    p.id_bhpd, 
                    p.id,
                    p.keterangan,
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

public function hapus_data_pencairan_bhpd_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil hapus data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->update('data_pencairan_bhpd_desa', array('status' => 3), array(
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

public function tambah_data_pencairan_bhpd(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil simpan data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            if($ret['status'] != 'error' && !empty($_POST['id_bhpd'])){
                $id_bhpd = $_POST['id_bhpd'];
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
            if($ret['status'] != 'error' && !empty($_POST['keterangan'])){
                $keterangan = $_POST['keterangan'];
            }
            // else{
            //     $ret['status'] = 'error';
            //     $ret['message'] = 'Pagu tidak boleh kosong!';
            // }
            $_POST['id'] = $id_bhpd;
            $pencairan = $this->get_pencairan_pemdes_bhpd(true);
            if(($pencairan['total_pencairan']+$pagu_anggaran) > $pencairan['pagu_anggaran']){
                $ret['status'] = 'error';
                $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
            }
            $validasi_pagu = $_POST['validasi_pagu'];
            $status_pagu = $_POST['status_pagu'];
            $keterangan_status_pagu = $_POST['keterangan_status_pagu'];
            if($ret['status'] != 'error'){
                if(empty($_POST['id_data'])){
                    $status = 0;
                }else{
                    $status = 1;
                    if($status_pagu == 0){
                        $status = 2;
                    }
                }
                $data = array(
                    'id_bhpd' => $id_bhpd,
                    'total_pencairan' => $pagu_anggaran,
                    'status_ver_total' => $status_pagu,
                    'ket_ver_total' => $keterangan_status_pagu,
                    'keterangan' => $keterangan,
                    'status' => $status,
                    'update_at' => current_time('mysql')
                );
                if(!empty($_POST['id_data'])){
                    $wpdb->update('data_pencairan_bhpd_desa', $data, array(
                        'id' => $_POST['id_data']
                    ));
                    $ret['message'] = 'Berhasil update data!';
                }else{
                    $cek_id = $wpdb->get_row($wpdb->prepare('
                        SELECT
                            id,
                            active
                        FROM data_pencairan_bhpd_desa
                        WHERE id_bhpd_desa=%s
                    ', $id_bhpd_desa), ARRAY_A);
                    if(empty($cek_id)){
                        $wpdb->insert('data_pencairan_bhpd_desa', $data);
                    }else{
                        if($cek_id['active'] == 0){
                            $wpdb->update('data_pencairan_bhpd_desa', $data, array(
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
    
public function get_datatable_data_pencairan_bhpd(){
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
              3 => 'p.total_pencairan',
              4 => 'p.keterangan',
              5 => 'p.status',
              6 => 'p.id_bhpd',
              7 => 'p.status_ver_total',
              8 => 'p.ket_ver_total',
              9 => 'p.id'
            );
            $where = $sqlTot = $sqlRec = "";

            // check search value exist
            if( !empty($params['search']['value']) ) { 
                $where .=" OR d.desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
            }

            // getting total number records without any search
            $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bhpd_desa` p inner join data_bhpd_desa d on p.id_bhpd=d.id";
            $sql = "SELECT ".implode(', ', $columns)." FROM `data_pencairan_bhpd_desa` p inner join data_bhpd_desa d on p.id_bhpd=d.id";
            $where_first = " WHERE 1=1 AND status != 3";
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
                $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\''.$recVal['id'].'\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                if ($recVal['status'] != 1) {
                    $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                    $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                }
                $queryRecords[$recKey]['aksi'] = $btn;
                if($recVal['status'] == 0){
                    $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Belum dicek</span>';
                }elseif ($recVal['status'] == 1) {
                    $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                }elseif ($recVal['status'] == 2) {
                    $pesan = '';
                    if ($recVal['status_ver_total' && 'status_ver_proposal'] == 0){
                        $pesan .= '<br>Keterangan Pagu: '.$recVal['ket_ver_total']; 
                    }
                    $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>'.$pesan;
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

public function get_pemdes_bhrd(){
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
                FROM data_bhrd_desa
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

public function get_pencairan_pemdes_bhrd($return = false){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil get data!'
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
            if(empty($ret['total_pencairan'])){
                $ret['total_pencairan'] = 0;
            }
            if(empty($ret['pagu_anggaran'])){
                $ret['pagu_anggaran'] = 0;
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }
    if(!empty($return)){
        return $ret;
    }else{
        die(json_encode($ret));
    }
}

public function get_data_pencairan_bhrd_by_id(){
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
                    p.id_bhrd, 
                    p.id,
                    p.keterangan,
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

public function hapus_data_pencairan_bhrd_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil hapus data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->update('data_pencairan_bhrd_desa', array('status' => 3), array(
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

public function tambah_data_pencairan_bhrd(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil simpan data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            if($ret['status'] != 'error' && !empty($_POST['id_bhrd'])){
                $id_bhrd = $_POST['id_bhrd'];
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
            if($ret['status'] != 'error' && !empty($_POST['keterangan'])){
                $keterangan = $_POST['keterangan'];
            }
            // else{
            //     $ret['status'] = 'error';
            //     $ret['message'] = 'Pagu tidak boleh kosong!';
            // }
            $_POST['id'] = $id_bhrd;
            $pencairan = $this->get_pencairan_pemdes_bhrd(true);
            if(($pencairan['total_pencairan']+$pagu_anggaran) > $pencairan['pagu_anggaran']){
                $ret['status'] = 'error';
                $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
            }
            $validasi_pagu = $_POST['validasi_pagu'];
            $status_pagu = $_POST['status_pagu'];
            $keterangan_status_pagu = $_POST['keterangan_status_pagu'];
            if($ret['status'] != 'error'){
                if(empty($_POST['id_data'])){
                    $status = 0;
                }else{
                    $status = 1;
                    if($status_pagu == 0){
                        $status = 2;
                    }
                }
                $data = array(
                    'id_bhrd' => $id_bhrd,
                    'total_pencairan' => $pagu_anggaran,
                    'status_ver_total' => $status_pagu,
                    'ket_ver_total' => $keterangan_status_pagu,
                    'keterangan' => $keterangan,
                    'status' => $status,
                    'update_at' => current_time('mysql')
                );
                if(!empty($_POST['id_data'])){
                    $wpdb->update('data_pencairan_bhrd_desa', $data, array(
                        'id' => $_POST['id_data']
                    ));
                    $ret['message'] = 'Berhasil update data!';
                }else{
                    $cek_id = $wpdb->get_row($wpdb->prepare('
                        SELECT
                            id,
                            active
                        FROM data_pencairan_bhrd_desa
                        WHERE id_bhrd_desa=%s
                    ', $id_bhrd_desa), ARRAY_A);
                    if(empty($cek_id)){
                        $wpdb->insert('data_pencairan_bhrd_desa', $data);
                    }else{
                        if($cek_id['active'] == 0){
                            $wpdb->update('data_pencairan_bhrd_desa', $data, array(
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
    
public function get_datatable_data_pencairan_bhrd(){
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
                  3 => 'p.total_pencairan',
                  4 => 'p.keterangan',
                  5 => 'p.status',
                  6 => 'p.id_bhrd',
                  7 => 'p.status_ver_total',
                  8 => 'p.ket_ver_total',
                  9 => 'p.id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if( !empty($params['search']['value']) ) { 
                    $where .=" OR d.desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bhrd_desa` p inner join data_bhrd_desa d on p.id_bhrd=d.id";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_pencairan_bhrd_desa` p inner join data_bhrd_desa d on p.id_bhrd=d.id";
                $where_first = " WHERE 1=1 AND status != 3";
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
                    $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\''.$recVal['id'].'\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                    if ($recVal['status'] != 1) {
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    }
                    $queryRecords[$recKey]['aksi'] = $btn;
                    if($recVal['status'] == 0){
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Belum dicek</span>';
                    }elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                    }elseif ($recVal['status'] == 2) {
                        $pesan = '';
                        if ($recVal['status_ver_total' && 'status_ver_proposal'] == 0){
                            $pesan .= '<br>Keterangan Pagu: '.$recVal['ket_ver_total']; 
                        }
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>'.$pesan;
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

public function get_pemdes_bku_dd(){
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
                FROM data_bku_dd_desa
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

public function get_pencairan_pemdes_bku_dd($return = false){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil get data!'
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
            if(empty($ret['total_pencairan'])){
                $ret['total_pencairan'] = 0;
            }
            if(empty($ret['pagu_anggaran'])){
                $ret['pagu_anggaran'] = 0;
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }
    if(!empty($return)){
        return $ret;
    }else{
        die(json_encode($ret));
    }
}

public function get_data_pencairan_bku_dd_by_id(){
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
                    p.id_bku_dd, 
                    p.id,
                    p.keterangan,
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

public function hapus_data_pencairan_bku_dd_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil hapus data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->update('data_pencairan_bku_dd_desa', array('status' => 3), array(
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

public function tambah_data_pencairan_bku_dd(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil simpan data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            if($ret['status'] != 'error' && !empty($_POST['id_bku_dd'])){
                $id_bku_dd = $_POST['id_bku_dd'];
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
            if($ret['status'] != 'error' && !empty($_POST['keterangan'])){
                $keterangan = $_POST['keterangan'];
            }
            // else{
            //     $ret['status'] = 'error';
            //     $ret['message'] = 'Pagu tidak boleh kosong!';
            // }
            $_POST['id'] = $id_bku_dd;
            $pencairan = $this->get_pencairan_pemdes_bku_dd(true);
            if(($pencairan['total_pencairan']+$pagu_anggaran) > $pencairan['pagu_anggaran']){
                $ret['status'] = 'error';
                $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
            }
            $validasi_pagu = $_POST['validasi_pagu'];
            $status_pagu = $_POST['status_pagu'];
            $keterangan_status_pagu = $_POST['keterangan_status_pagu'];
            if($ret['status'] != 'error'){
                if(empty($_POST['id_data'])){
                    $status = 0;
                }else{
                    $status = 1;
                    if($status_pagu == 0){
                        $status = 2;
                    }
                }
                $data = array(
                    'id_bku_dd' => $id_bku_dd,
                    'total_pencairan' => $pagu_anggaran,
                    'status_ver_total' => $status_pagu,
                    'ket_ver_total' => $keterangan_status_pagu,
                    'keterangan' => $keterangan,
                    'status' => $status,
                    'update_at' => current_time('mysql')
                );
                if(!empty($_POST['id_data'])){
                    $wpdb->update('data_pencairan_bku_dd_desa', $data, array(
                        'id' => $_POST['id_data']
                    ));
                    $ret['message'] = 'Berhasil update data!';
                }else{
                    $cek_id = $wpdb->get_row($wpdb->prepare('
                        SELECT
                            id,
                            active
                        FROM data_pencairan_bku_dd_desa
                        WHERE id_bku_dd_desa=%s
                    ', $id_bku_dd_desa), ARRAY_A);
                    if(empty($cek_id)){
                        $wpdb->insert('data_pencairan_bku_dd_desa', $data);
                    }else{
                        if($cek_id['active'] == 0){
                            $wpdb->update('data_pencairan_bku_dd_desa', $data, array(
                                'id' => $cek_id['id']
                            ));
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal disimpan. Data bku_dd_desa dengan id_bku_dd_desa="'.$id_bku_dd_desa.'" sudah ada!';
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
    
public function get_datatable_data_pencairan_bku_dd(){
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
                  3 => 'p.total_pencairan',
                  4 => 'p.keterangan',
                  5 => 'p.status',
                  6 => 'p.id_bku_dd',
                  7 => 'p.status_ver_total',
                  8 => 'p.ket_ver_total',
                  9 => 'p.id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if( !empty($params['search']['value']) ) { 
                    $where .=" OR d.desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bku_dd_desa` p inner join data_bku_dd_desa d on p.id_bku_dd=d.id";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_pencairan_bku_dd_desa` p inner join data_bku_dd_desa d on p.id_bku_dd=d.id";
                $where_first = " WHERE 1=1 AND status != 3";
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
                    $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\''.$recVal['id'].'\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                    if ($recVal['status'] != 1) {
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    }
                    $queryRecords[$recKey]['aksi'] = $btn;
                    if($recVal['status'] == 0){
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Belum dicek</span>';
                    }elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                    }elseif ($recVal['status'] == 2) {
                        $pesan = '';
                        if ($recVal['status_ver_total' && 'status_ver_proposal'] == 0){
                            $pesan .= '<br>Keterangan Pagu: '.$recVal['ket_ver_total']; 
                        }
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>'.$pesan;
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

public function get_pemdes_bku_add(){
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
                FROM data_bku_add_desa
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

public function get_pencairan_pemdes_bku_add($return = false){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil get data!'
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
            if(empty($ret['total_pencairan'])){
                $ret['total_pencairan'] = 0;
            }
            if(empty($ret['pagu_anggaran'])){
                $ret['pagu_anggaran'] = 0;
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }
    if(!empty($return)){
        return $ret;
    }else{
        die(json_encode($ret));
    }
}

public function get_data_pencairan_bku_add_by_id(){
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
                    p.id_bku_add, 
                    p.id,
                    p.keterangan,
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

public function hapus_data_pencairan_bku_add_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil hapus data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->update('data_pencairan_bku_add_desa', array('status' => 3), array(
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

public function tambah_data_pencairan_bku_add(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil simpan data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            if($ret['status'] != 'error' && !empty($_POST['id_bku_add'])){
                $id_bku_add = $_POST['id_bku_add'];
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
            if($ret['status'] != 'error' && !empty($_POST['keterangan'])){
                $keterangan = $_POST['keterangan'];
            }
            // else{
            //     $ret['status'] = 'error';
            //     $ret['message'] = 'Pagu tidak boleh kosong!';
            // }
            $_POST['id'] = $id_bku_add;
            $pencairan = $this->get_pencairan_pemdes_bku_add(true);
            if(($pencairan['total_pencairan']+$pagu_anggaran) > $pencairan['pagu_anggaran']){
                $ret['status'] = 'error';
                $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
            }
            $validasi_pagu = $_POST['validasi_pagu'];
            $status_pagu = $_POST['status_pagu'];
            $keterangan_status_pagu = $_POST['keterangan_status_pagu'];
            if($ret['status'] != 'error'){
                if(empty($_POST['id_data'])){
                    $status = 0;
                }else{
                    $status = 1;
                    if($status_pagu == 0){
                        $status = 2;
                    }
                }
                $data = array(
                    'id_bku_add' => $id_bku_add,
                    'total_pencairan' => $pagu_anggaran,
                    'status_ver_total' => $status_pagu,
                    'ket_ver_total' => $keterangan_status_pagu,
                    'keterangan' => $keterangan,
                    'status' => $status,
                    'update_at' => current_time('mysql')
                );
                if(!empty($_POST['id_data'])){
                    $wpdb->update('data_pencairan_bku_add_desa', $data, array(
                        'id' => $_POST['id_data']
                    ));
                    $ret['message'] = 'Berhasil update data!';
                }else{
                    $cek_id = $wpdb->get_row($wpdb->prepare('
                        SELECT
                            id,
                            active
                        FROM data_pencairan_bku_add_desa
                        WHERE id_bku_add_desa=%s
                    ', $id_bku_add_desa), ARRAY_A);
                    if(empty($cek_id)){
                        $wpdb->insert('data_pencairan_bku_add_desa', $data);
                    }else{
                        if($cek_id['active'] == 0){
                            $wpdb->update('data_pencairan_bku_add_desa', $data, array(
                                'id' => $cek_id['id']
                            ));
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal disimpan. Data bku_add_desa dengan id_bku_add_desa="'.$id_bku_add_desa.'" sudah ada!';
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
    
public function get_datatable_data_pencairan_bku_add(){
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
                  3 => 'p.total_pencairan',
                  4 => 'p.keterangan',
                  5 => 'p.status',
                  6 => 'p.id_bku_add',
                  7 => 'p.status_ver_total',
                  8 => 'p.ket_ver_total',
                  9 => 'p.id'
                );
                $where = $sqlTot = $sqlRec = "";

                // check search value exist
                if( !empty($params['search']['value']) ) { 
                    $where .=" OR d.desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
                }

                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bku_add_desa` p inner join data_bku_add_desa d on p.id_bku_add=d.id";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_pencairan_bku_add_desa` p inner join data_bku_add_desa d on p.id_bku_add=d.id";
                $where_first = " WHERE 1=1 AND status != 3";
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
                    $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\''.$recVal['id'].'\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                    if ($recVal['status'] != 1) {
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    }
                    $queryRecords[$recKey]['aksi'] = $btn;
                    if($recVal['status'] == 0){
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Belum dicek</span>';
                    }elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                    }elseif ($recVal['status'] == 2) {
                        $pesan = '';
                        if ($recVal['status_ver_total' && 'status_ver_proposal'] == 0){
                            $pesan .= '<br>Keterangan Pagu: '.$recVal['ket_ver_total']; 
                        }
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>'.$pesan;
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

public function get_pemdes_bkk_pilkades(){
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
                FROM data_bkk_pilkades_desa
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

public function get_pencairan_pemdes_bkk_pilkades($return = false){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil get data!'
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
            if(empty($ret['total_pencairan'])){
                $ret['total_pencairan'] = 0;
            }
            if(empty($ret['pagu_anggaran'])){
                $ret['pagu_anggaran'] = 0;
            }
        }else{
            $ret['status']  = 'error';
            $ret['message'] = 'Api key tidak ditemukan!';
        }
    }else{
        $ret['status']  = 'error';
        $ret['message'] = 'Format Salah!';
    }
    if(!empty($return)){
        return $ret;
    }else{
        die(json_encode($ret));
    }
}

public function get_data_pencairan_bkk_pilkades_by_id(){
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

public function hapus_data_pencairan_bkk_pilkades_by_id(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil hapus data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            $ret['data'] = $wpdb->update('data_pencairan_bkk_pilkades_desa', array('status' => 3), array(
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

public function tambah_data_pencairan_bkk_pilkades(){
    global $wpdb;
    $ret = array(
        'status' => 'success',
        'message' => 'Berhasil simpan data!',
        'data' => array()
    );
    if(!empty($_POST)){
        if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
            if($ret['status'] != 'error' && !empty($_POST['id_bkk_pilkades'])){
                $id_bkk_pilkades = $_POST['id_bkk_pilkades'];
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
            if($ret['status'] != 'error' && !empty($_POST['keterangan'])){
                $keterangan = $_POST['keterangan'];
            }
            // else{
            //     $ret['status'] = 'error';
            //     $ret['message'] = 'Pagu tidak boleh kosong!';
            // }
            $_POST['id'] = $id_bkk_pilkades;
            $pencairan = $this->get_pencairan_pemdes_bkk_pilkades(true);
            if(($pencairan['total_pencairan']+$pagu_anggaran) > $pencairan['pagu_anggaran']){
                $ret['status'] = 'error';
                $ret['message'] = 'Total pencairan tidak boleh lebih dari sisa pencairan!';
            }
            $validasi_pagu = $_POST['validasi_pagu'];
            $status_pagu = $_POST['status_pagu'];
            $keterangan_status_pagu = $_POST['keterangan_status_pagu'];
            if($ret['status'] != 'error'){
                if(empty($_POST['id_data'])){
                    $status = 0;
                }else{
                    $status = 1;
                    if($status_pagu == 0){
                        $status = 2;
                    }
                }
                $data = array(
                    'id_bkk_pilkades' => $id_bkk_pilkades,
                    'total_pencairan' => $pagu_anggaran,
                    'status_ver_total' => $status_pagu,
                    'ket_ver_total' => $keterangan_status_pagu,
                    'keterangan' => $keterangan,
                    'status' => $status,
                    'update_at' => current_time('mysql')
                );
                if(!empty($_POST['id_data'])){
                    $wpdb->update('data_pencairan_bkk_pilkades_desa', $data, array(
                        'id' => $_POST['id_data']
                    ));
                    $ret['message'] = 'Berhasil update data!';
                }else{
                    $cek_id = $wpdb->get_row($wpdb->prepare('
                        SELECT
                            id,
                            active
                        FROM data_pencairan_bkk_pilkades_desa
                        WHERE id_bkk_pilkades_desa=%s
                    ', $id_bkk_pilkades_desa), ARRAY_A);
                    if(empty($cek_id)){
                        $wpdb->insert('data_pencairan_bkk_pilkades_desa', $data);
                    }else{
                        if($cek_id['active'] == 0){
                            $wpdb->update('data_pencairan_bkk_pilkades_desa', $data, array(
                                'id' => $cek_id['id']
                            ));
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'Gagal disimpan. Data bkk_pilkades_desa dengan id_bkk_pilkades_desa="'.$id_bkk_pilkades_desa.'" sudah ada!';
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
    
public function get_datatable_data_pencairan_bkk_pilkades(){
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
                if( !empty($params['search']['value']) ) {
                    $where .=" AND ( d.desa LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%").")";  
                }
                // getting total number records without any search
                $sql_tot = "SELECT count(p.id) as jml FROM `data_pencairan_bkk_pilkades_desa` p inner join data_bkk_pilkades_desa d on p.id_bkk_pilkades=d.id";
                $sql = "SELECT ".implode(', ', $columns)." FROM `data_pencairan_bkk_pilkades_desa` p inner join data_bkk_pilkades_desa d on p.id_bkk_pilkades=d.id";
                $where_first = " WHERE 1=1 AND status != 3";
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
                    $btn = '<a class="btn btn-sm btn-primary" onclick="detail_data(\''.$recVal['id'].'\'); return false;" href="#" title="Detail Data"><i class="dashicons dashicons-search"></i></a>';
                    if ($recVal['status'] != 1) {
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-warning" onclick="edit_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-edit"></i></a>';
                        $btn .= '<a style="margin-left: 10px;" class="btn btn-sm btn-danger" onclick="hapus_data(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Data"><i class="dashicons dashicons-trash"></i></a>';
                    }
                    $queryRecords[$recKey]['aksi'] = $btn;
                    if($recVal['status'] == 0){
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-primary btn-sm">Belum dicek</span>';
                    }elseif ($recVal['status'] == 1) {
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-success btn-sm">Diterima</span>';
                    }elseif ($recVal['status'] == 2) {
                        $pesan = '';
                        if ($recVal['status_ver_total' && 'status_ver_proposal'] == 0){
                            $pesan .= '<br>Keterangan Pagu: '.$recVal['ket_ver_total']; 
                        }
                        $queryRecords[$recKey]['status'] = '<span class="btn btn-danger btn-sm">Ditolak</span>'.$pesan;
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