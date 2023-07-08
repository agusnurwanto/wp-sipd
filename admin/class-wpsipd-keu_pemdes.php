<?php

class Wpsipd_Admin_Keu_Pemdes {

    public function import_excel_bkk(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil import excel!'
        );
        if (!empty($_POST)) {
            $ret['data'] = array(
                'insert' => 0, 
                'update' => 0,
                'error' => array()
            );
            foreach ($_POST['data'] as $k => $data) {
                $newData = array();
                foreach($data as $kk => $vv){
                    $newData[trim(preg_replace('/\s+/', ' ', $kk))] = trim(preg_replace('/\s+/', ' ', $vv));
                }
                $data_db = array(
                    'id_kecamatan' => $newData['id_kecamatan'],
                    'id_desa' => $newData['id_desa'],
                    'kecamatan' => $newData['kecamatan'],
                    'desa' => $newData['desa'],
                    'kegiatan' => $newData['kegiatan'],
                    'alamat' => $newData['alamat'],
                    'total' => $newData['total'],
                    'id_dana' => $newData['id_dana'],
                    'sumber_dana' => $newData['sumber_dana'],
                    'tahun_anggaran' => $newData['tahun'],
                    'update_at' => current_time('mysql'),
                    'active' => 1
                );
                if(empty($data_db['id_kecamatan'])){

                }
                $wpdb->last_error = "";
                $cek_id = $wpdb->get_var($wpdb->prepare("
                    SELECT 
                        id 
                    from data_bkk_desa 
                    where desa=%s 
                        and kecamatan=%s
                        and kegiatan=%s
                        and alamat=%s
                        and tahun_anggaran=%d
                ", $newData['desa'], $newData['kecamatan'], $newData['kegiatan'], $newData['alamat'], $newData['tahun']));
                if(empty($cek_id)){
                    $wpdb->insert("data_bkk_desa", $data_db);
                    $ret['data']['insert']++;
                }else{
                    $wpdb->update("data_bkk_desa", $data_db, array(
                        "id" => $cek_id
                    ));
                    $ret['data']['update']++;
                }
                if(!empty($wpdb->last_error)){
                    $ret['data']['error'][] = array($wpdb->last_error, $data_db);
                };

            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    public function import_excel_bkk_pilkades(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil import excel!'
        );
        if (!empty($_POST)) {
            $ret['data'] = array(
                'insert' => 0, 
                'update' => 0,
                'error' => array()
            );
            foreach ($_POST['data'] as $k => $data) {
                $newData = array();
                foreach($data as $kk => $vv){
                    $newData[trim(preg_replace('/\s+/', ' ', $kk))] = trim(preg_replace('/\s+/', ' ', $vv));
                }
                $data_db = array(
                    'id_kecamatan' => $newData['id_kecamatan'],
                    'id_desa' => $newData['id_desa'],
                    'kecamatan' => $newData['kecamatan'],
                    'desa' => $newData['desa'],
                    'total' => $newData['total'],
                    'tahun_anggaran' => $newData['tahun'],
                    'update_at' => current_time('mysql'),
                    'active' => 1
                );
                if(empty($data_db['id_kecamatan'])){

                }
                $wpdb->last_error = "";
                $cek_id = $wpdb->get_var($wpdb->prepare("
                    SELECT 
                        id 
                    from data_bkk_pilkades_desa 
                    where desa=%s 
                        and kecamatan=%s
                        and tahun_anggaran=%d
                ", $newData['desa'], $newData['kecamatan'], $newData['tahun']));
                if(empty($cek_id)){
                    $wpdb->insert("data_bkk_pilkades_desa", $data_db);
                    $ret['data']['insert']++;
                }else{
                    $wpdb->update("data_bkk_pilkades_desa", $data_db, array(
                        "id" => $cek_id
                    ));
                    $ret['data']['update']++;
                }
                if(!empty($wpdb->last_error)){
                    $ret['data']['error'][] = array($wpdb->last_error, $data_db);
                };

            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    public function import_excel_bhpd(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil import excel!'
        );
        if (!empty($_POST)) {
            $ret['data'] = array(
                'insert' => 0, 
                'update' => 0,
                'error' => array()
            );
            foreach ($_POST['data'] as $k => $data) {
                $newData = array();
                foreach($data as $kk => $vv){
                    $newData[trim(preg_replace('/\s+/', ' ', $kk))] = trim(preg_replace('/\s+/', ' ', $vv));
                }
                $data_db = array(
                    'id_kecamatan' => $newData['id_kecamatan'],
                    'id_desa' => $newData['id_desa'],
                    'kecamatan' => $newData['kecamatan'],
                    'desa' => $newData['desa'],
                    'total' => $newData['total'],
                    'tahun_anggaran' => $newData['tahun'],
                    'update_at' => current_time('mysql'),
                    'active' => 1
                );
                if(empty($data_db['id_kecamatan'])){

                }
                $wpdb->last_error = "";
                $cek_id = $wpdb->get_var($wpdb->prepare("
                    SELECT 
                        id 
                    from data_bhpd_desa 
                    where desa=%s 
                        and kecamatan=%s
                        and tahun_anggaran=%d
                ", $newData['desa'], $newData['kecamatan'], $newData['tahun']));
                if(empty($cek_id)){
                    $wpdb->insert("data_bhpd_desa", $data_db);
                    $ret['data']['insert']++;
                }else{
                    $wpdb->update("data_bhpd_desa", $data_db, array(
                        "id" => $cek_id
                    ));
                    $ret['data']['update']++;
                }
                if(!empty($wpdb->last_error)){
                    $ret['data']['error'][] = array($wpdb->last_error, $data_db);
                };

            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    public function import_excel_bhrd(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil import excel!'
        );
        if (!empty($_POST)) {
            $ret['data'] = array(
                'insert' => 0, 
                'update' => 0,
                'error' => array()
            );
            foreach ($_POST['data'] as $k => $data) {
                $newData = array();
                foreach($data as $kk => $vv){
                    $newData[trim(preg_replace('/\s+/', ' ', $kk))] = trim(preg_replace('/\s+/', ' ', $vv));
                }
                $data_db = array(
                    'id_kecamatan' => $newData['id_kecamatan'],
                    'id_desa' => $newData['id_desa'],
                    'kecamatan' => $newData['kecamatan'],
                    'desa' => $newData['desa'],
                    'total' => $newData['total'],
                    'tahun_anggaran' => $newData['tahun'],
                    'update_at' => current_time('mysql'),
                    'active' => 1
                );
                if(empty($data_db['id_kecamatan'])){

                }
                $wpdb->last_error = "";
                $cek_id = $wpdb->get_var($wpdb->prepare("
                    SELECT 
                        id 
                    from data_bhrd_desa 
                    where desa=%s 
                        and kecamatan=%s
                        and tahun_anggaran=%d
                ", $newData['desa'], $newData['kecamatan'], $newData['tahun']));
                if(empty($cek_id)){
                    $wpdb->insert("data_bhrd_desa", $data_db);
                    $ret['data']['insert']++;
                }else{
                    $wpdb->update("data_bhrd_desa", $data_db, array(
                        "id" => $cek_id
                    ));
                    $ret['data']['update']++;
                }
                if(!empty($wpdb->last_error)){
                    $ret['data']['error'][] = array($wpdb->last_error, $data_db);
                };

            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    public function import_excel_bku_dd(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil import excel!'
        );
        if (!empty($_POST)) {
            $user_id = um_user( 'ID' );
            $user_meta = get_userdata($user_id);
            $ret['data'] = array(
                'insert' => 0, 
                'update' => 0,
                'error' => array()
            );
            foreach ($_POST['data'] as $k => $data) {
                $newData = array();
                foreach($data as $kk => $vv){
                    $newData[trim(preg_replace('/\s+/', ' ', $kk))] = trim(preg_replace('/\s+/', ' ', $vv));
                }
                if(!empty($_POST['pencairan'])){
                    $data_pagu = $wpdb->get_row($wpdb->prepare("
                        SELECT
                            id
                        FROM data_bku_dd_desa
                        WHERE tahun_anggaran=%d
                            AND active=1
                            AND kecamatan=%s
                            AND desa=%s 
                    ", $newData['tahun'], $newData['kecamatan'], $newData['desa']), ARRAY_A);
                    if(!empty($data_pagu)){
                        $data_db = array(
                            'id_bku_dd' => $data_pagu['id'],
                            'total_pencairan' => $newData['realisasi'],
                            'keterangan' => $newData['keterangan'],
                            'nama_user' => $user_meta->display_name,
                            'status_ver_total' => 1,
                            'ket_ver_total' => 'Hasil import dari file excel',
                            'update_at' => current_time('mysql'),
                            'status' => 1
                        );
                        $cek_id = $wpdb->get_var($wpdb->prepare("
                            SELECT 
                                id 
                            from data_pencairan_bku_dd_desa 
                            where id_bku_dd=%d 
                                and keterangan=%s
                        ", $data_pagu['id'], $newData['keterangan']));
                        if(empty($cek_id)){
                            $wpdb->insert("data_pencairan_bku_dd_desa", $data_db);
                            $ret['data']['insert']++;
                        }else{
                            $wpdb->update("data_pencairan_bku_dd_desa", $data_db, array(
                                "id" => $cek_id
                            ));
                            $ret['data']['update']++;
                        }
                    }else{
                        $ret['data']['error'][] = 'ID pagu tidak ditemukan! '.$wpdb->last_query;
                    }
                }else{
                    $data_db = array(
                        'id_kecamatan' => $newData['id_kecamatan'],
                        'id_desa' => $newData['id_desa'],
                        'kecamatan' => $newData['kecamatan'],
                        'desa' => $newData['desa'],
                        'total' => $newData['total'],
                        'tahun_anggaran' => $newData['tahun'],
                        'update_at' => current_time('mysql'),
                        'active' => 1
                    );
                    if(empty($data_db['id_kecamatan'])){

                    }
                    $wpdb->last_error = "";
                    $cek_id = $wpdb->get_var($wpdb->prepare("
                        SELECT 
                            id 
                        from data_bku_dd_desa 
                        where desa=%s 
                            and kecamatan=%s
                            and tahun_anggaran=%d
                    ", $newData['desa'], $newData['kecamatan'], $newData['tahun']));
                    if(empty($cek_id)){
                        $wpdb->insert("data_bku_dd_desa", $data_db);
                        $ret['data']['insert']++;
                    }else{
                        $wpdb->update("data_bku_dd_desa", $data_db, array(
                            "id" => $cek_id
                        ));
                        $ret['data']['update']++;
                    }
                }
                if(!empty($wpdb->last_error)){
                    $ret['data']['error'][] = array($wpdb->last_error, $data_db);
                };

            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    public function import_excel_bku_add(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil import excel!'
        );
        if (!empty($_POST)) {
            $ret['data'] = array(
                'insert' => 0, 
                'update' => 0,
                'error' => array()
            );
            foreach ($_POST['data'] as $k => $data) {
                $newData = array();
                foreach($data as $kk => $vv){
                    $newData[trim(preg_replace('/\s+/', ' ', $kk))] = trim(preg_replace('/\s+/', ' ', $vv));
                }
                if(!empty($_POST['pencairan'])){
                    $data_pagu = $wpdb->get_row($wpdb->prepare("
                        SELECT
                            id
                        FROM data_bku_add_desa
                        WHERE tahun_anggaran=%d
                            AND active=1
                            AND kecamatan=%s
                            AND desa=%s 
                    ", $newData['tahun'], $newData['kecamatan'], $newData['desa']), ARRAY_A);
                    if(!empty($data_pagu)){
                        $data_db = array(
                            'id_bku_add' => $data_pagu['id'],
                            'total_pencairan' => $newData['realisasi'],
                            'keterangan' => $newData['keterangan'],
                            'nama_user' => $user_meta->display_name,
                            'status_ver_total' => 1,
                            'ket_ver_total' => 'Hasil import dari file excel',
                            'update_at' => current_time('mysql'),
                            'status' => 1
                        );
                        $cek_id = $wpdb->get_var($wpdb->prepare("
                            SELECT 
                                id 
                            from data_pencairan_bku_add_desa 
                            where id_bku_add=%d 
                                and keterangan=%s
                        ", $data_pagu['id'], $newData['keterangan']));
                        if(empty($cek_id)){
                            $wpdb->insert("data_pencairan_bku_add_desa", $data_db);
                            $ret['data']['insert']++;
                        }else{
                            $wpdb->update("data_pencairan_bku_add_desa", $data_db, array(
                                "id" => $cek_id
                            ));
                            $ret['data']['update']++;
                        }
                    }else{
                        $ret['data']['error'][] = 'ID pagu tidak ditemukan! '.$wpdb->last_query;
                    }
                }else{
                    $data_db = array(
                        'id_kecamatan' => $newData['id_kecamatan'],
                        'id_desa' => $newData['id_desa'],
                        'kecamatan' => $newData['kecamatan'],
                        'desa' => $newData['desa'],
                        'total' => $newData['total'],
                        'tahun_anggaran' => $newData['tahun'],
                        'update_at' => current_time('mysql'),
                        'active' => 1
                    );
                    if(empty($data_db['id_kecamatan'])){

                    }
                    $wpdb->last_error = "";
                    $cek_id = $wpdb->get_var($wpdb->prepare("
                        SELECT 
                            id 
                        from data_bku_add_desa 
                        where desa=%s 
                            and kecamatan=%s
                            and tahun_anggaran=%d
                    ", $newData['desa'], $newData['kecamatan'], $newData['tahun']));
                    if(empty($cek_id)){
                        $wpdb->insert("data_bku_add_desa", $data_db);
                        $ret['data']['insert']++;
                    }else{
                        $wpdb->update("data_bku_add_desa", $data_db, array(
                            "id" => $cek_id
                        ));
                        $ret['data']['update']++;
                    }
                }
                if(!empty($wpdb->last_error)){
                    $ret['data']['error'][] = array($wpdb->last_error, $data_db);
                };

            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    public function singkron_bku_dd(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil import excel!'
        );
        if (!empty($_POST)) {
            $ret['data'] = array(
                'insert' => 0, 
                'update' => 0,
                'error' => array()
            );
            $where = "";
            if(!empty($_POST['kelompok'])){
                $where = $wpdb->prepare(" and subs_bl_teks=%s", $_POST['kelompok']);
            }
            $res_db = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    * 
                FROM `data_rka` 
                where kode_akun='5.4.02.05.01.0001' 
                    and active=1 
                    and tahun_anggaran=%d
                    $where
                order by kode_sbl ASC, lokus_akun_teks ASC
            ", $_POST['tahun_anggaran']), ARRAY_A);
            // print_r($res_db); die($wpdb->last_query);
            foreach ($res_db as $k => $data) {
                $alamat = $wpdb->get_row($wpdb->prepare("
                    SELECT
                        id_alamat,
                        id_kec,
                        nama
                    FROM data_alamat
                    where nama=%s
                        AND tahun=%d
                        AND is_kel=1
                ", $data['lokus_akun_teks'], $_POST['tahun_anggaran']), ARRAY_A);
                $kecamatan = $wpdb->get_var($wpdb->prepare("
                    SELECT 
                        nama 
                    from data_alamat 
                    where id_alamat=$alamat[id_kec] 
                        AND is_kec=1 
                        AND tahun=%d
                ", $_POST['tahun_anggaran']));
                $data_db = array(
                    'id_kecamatan' => $alamat['id_kec'],
                    'id_desa' => $alamat['id_alamat'],
                    'kecamatan' => $kecamatan,
                    'desa' => $data['lokus_akun_teks'],
                    'total' => $data['total_harga'],
                    'tahun_anggaran' => $_POST['tahun_anggaran'],
                    'update_at' => current_time('mysql'),
                    'active' => 1
                );
                $wpdb->last_error = "";
                $cek_id = $wpdb->get_var($wpdb->prepare("
                    SELECT 
                        id 
                    from data_bku_dd_desa 
                    where desa=%s 
                        and kecamatan=%s
                        and tahun_anggaran=%d
                ", $data['lokus_akun_teks'], $kecamatan, $_POST['tahun_anggaran']));
                if(empty($cek_id)){
                    $wpdb->insert("data_bku_dd_desa", $data_db);
                    $ret['data']['insert']++;
                }else{
                    $wpdb->update("data_bku_dd_desa", $data_db, array(
                        "id" => $cek_id
                    ));
                    $ret['data']['update']++;
                }
                if(!empty($wpdb->last_error)){
                    $ret['data']['error'][] = array($wpdb->last_error, $data_db);
                };

            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    public function singkron_bku_add(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil import excel!'
        );
        if (!empty($_POST)) {
            $ret['data'] = array(
                'insert' => 0, 
                'update' => 0,
                'error' => array()
            );
            $where = "";
            if(!empty($_POST['kelompok'])){
                $where = $wpdb->prepare(" and subs_bl_teks=%s", $_POST['kelompok']);
            }
            $res_db = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    * 
                FROM `data_rka` 
                where kode_akun='5.4.02.05.01.0001' 
                    and active=1 
                    and tahun_anggaran=%d
                    $where
                order by kode_sbl ASC, lokus_akun_teks ASC
            ", $_POST['tahun_anggaran']), ARRAY_A);
            // print_r($res_db); die($wpdb->last_query);
            foreach ($res_db as $k => $data) {
                $alamat = $wpdb->get_row($wpdb->prepare("
                    SELECT
                        id_alamat,
                        id_kec,
                        nama
                    FROM data_alamat
                    where nama=%s
                        AND tahun=%d
                        AND is_kel=1
                ", $data['lokus_akun_teks'], $_POST['tahun_anggaran']), ARRAY_A);
                $kecamatan = $wpdb->get_var($wpdb->prepare("
                    SELECT 
                        nama 
                    from data_alamat 
                    where id_alamat=$alamat[id_kec] 
                        AND is_kec=1 
                        AND tahun=%d
                ", $_POST['tahun_anggaran']));
                $data_db = array(
                    'id_kecamatan' => $alamat['id_kec'],
                    'id_desa' => $alamat['id_alamat'],
                    'kecamatan' => $kecamatan,
                    'desa' => $data['lokus_akun_teks'],
                    'total' => $data['total_harga'],
                    'tahun_anggaran' => $_POST['tahun_anggaran'],
                    'update_at' => current_time('mysql'),
                    'active' => 1
                );
                $wpdb->last_error = "";
                $cek_id = $wpdb->get_var($wpdb->prepare("
                    SELECT 
                        id 
                    from data_bku_add_desa 
                    where desa=%s 
                        and kecamatan=%s
                        and tahun_anggaran=%d
                ", $data['lokus_akun_teks'], $kecamatan, $_POST['tahun_anggaran']));
                if(empty($cek_id)){
                    $wpdb->insert("data_bku_add_desa", $data_db);
                    $ret['data']['insert']++;
                }else{
                    $wpdb->update("data_bku_add_desa", $data_db, array(
                        "id" => $cek_id
                    ));
                    $ret['data']['update']++;
                }
                if(!empty($wpdb->last_error)){
                    $ret['data']['error'][] = array($wpdb->last_error, $data_db);
                };

            }
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }

    public function set_setting_ajax(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil disimpan!'
        );
        if (!empty($_POST) && !empty($_POST['key'])) {
            update_option($_POST['key'], $_POST['val']);
        } else {
            $ret['status'] = 'error';
            $ret['message'] = 'Format Salah!';
        }
        die(json_encode($ret));
    }
}