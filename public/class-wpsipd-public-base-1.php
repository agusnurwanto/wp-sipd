<?php

require_once WPSIPD_PLUGIN_PATH."/public/class-wpsipd-public-base-2.php";
class Wpsipd_Public_Base_1 extends Wpsipd_Public_Base_2{
    public function singkron_rpjpd_sipd_lokal(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil mengambil data RPD dari data SIPD lokal!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                $sql = "
                    select 
                        * 
                    from data_rpd_tujuan
                    where active=1
                ";
                $tujuan_all = $wpdb->get_results($sql, ARRAY_A);
                foreach ($tujuan_all as $tujuan) {
                    $table = 'data_rpjpd_visi';
                    $id_cek_visi = $wpdb->get_var("
                        SELECT id from $table 
                        where visi_teks='{$tujuan['visi_teks']}'
                    ");
                    $data = array(
                        'visi_teks' => $tujuan['visi_teks'],
                        'update_at' => $tujuan['update_at']
                    );
                    if(!empty($id_cek_visi)){
                        $wpdb->update($table, $data, array('id' => $id_cek_visi));
                    }else{
                        $id_cek_visi = $wpdb->insert($table, $data);
                    }

                    $table = 'data_rpjpd_misi';
                    $id_cek_misi = $wpdb->get_var("
                        SELECT id from $table 
                        where misi_teks='{$tujuan['misi_teks']}'
                            AND id_visi='$id_cek_visi'
                    ");
                    $data = array(
                        'id_visi' => $id_cek_visi,
                        'misi_teks' => $tujuan['misi_teks'],
                        'urut_misi' => $tujuan['urut_misi'],
                        'update_at' => $tujuan['update_at']
                    );
                    if(!empty($id_cek_misi)){
                        $wpdb->update($table, $data, array('id' => $id_cek_misi));
                    }else{
                        $id_cek_misi = $wpdb->insert($table, $data);
                    }

                    $table = 'data_rpjpd_sasaran';
                    $id_cek_sasaran = $wpdb->get_var("
                        SELECT id from $table 
                        where saspok_teks='{$tujuan['saspok_teks']}'
                            AND id_misi='$id_cek_misi'
                    ");
                    $data = array(
                        'id_misi' => $id_cek_misi,
                        'saspok_teks' => $tujuan['saspok_teks'],
                        'urut_saspok' => $tujuan['urut_saspok'],
                        'update_at' => $tujuan['update_at']
                    );
                    if(!empty($id_cek_sasaran)){
                        $wpdb->update($table, $data, array('id' => $id_cek_sasaran));
                    }else{
                        $id_cek_sasaran = $wpdb->insert($table, $data);
                    }

                    $table = 'data_rpjpd_kebijakan';
                    $id_cek_kebijakan = $wpdb->get_var("
                        SELECT id from $table 
                        where kebijakan_teks='{$tujuan['kebijakan_teks']}'
                            AND id_saspok='$id_cek_sasaran'
                    ");
                    $data = array(
                        'id_saspok' => $id_cek_sasaran,
                        'kebijakan_teks' => $tujuan['kebijakan_teks'],
                        'update_at' => $tujuan['update_at']
                    );
                    if(!empty($id_cek_kebijakan)){
                        $wpdb->update($table, $data, array('id' => $id_cek_kebijakan));
                    }else{
                        $id_cek_kebijakan = $wpdb->insert($table, $data);
                    }

                    $table = 'data_rpjpd_isu';
                    $id_cek_isu = $wpdb->get_var("
                        SELECT id from $table 
                        where isu_teks='{$tujuan['isu_teks']}'
                            AND id_kebijakan='$id_cek_kebijakan'
                    ");
                    $data = array(
                        'id_kebijakan' => $id_cek_kebijakan,
                        'isu_teks' => $tujuan['isu_teks'],
                        'update_at' => $tujuan['update_at']
                    );
                    if(!empty($id_cek_isu)){
                        $wpdb->update($table, $data, array('id' => $id_cek_isu));
                    }else{
                        $id_cek_isu = $wpdb->insert($table, $data);
                    }
                }
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

    public function get_rpjpd($res=false){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil mengambil data RPJPD dari data SIPD lokal!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                $table = '';
                $where = 'where 1=1 ';
                if($_POST['table'] == 'data_rpjpd_visi'){
                    $table = 'data_rpjpd_visi';
                }else if($_POST['table'] == 'data_rpjpd_misi'){
                    $table = 'data_rpjpd_misi';
                    if(!empty($_POST['id_misi'])){
                        $id_visi = $wpdb->get_var($wpdb->prepare("
                            select id_visi
                            from $table
                            where id=%d", $_POST['id_misi']));
                        $where .= $wpdb->prepare('and id_visi=%d', $id_visi);
                    }else{
                        $where .= $wpdb->prepare('and id_visi=%d', $_POST['id']);
                    }
                }else if($_POST['table'] == 'data_rpjpd_sasaran'){
                    $table = 'data_rpjpd_sasaran';
                    if(!empty($_POST['id_saspok'])){
                        $id_misi = $wpdb->get_var($wpdb->prepare("
                            select id_misi
                            from $table
                            where id=%d", $_POST['id_saspok']));
                        $where .= $wpdb->prepare('and id_misi=%d', $id_misi);
                    }else{
                        $where .= $wpdb->prepare('and id_misi=%d', $_POST['id']);
                    }
                }else if($_POST['table'] == 'data_rpjpd_kebijakan'){
                    $table = 'data_rpjpd_kebijakan';
                    if(!empty($_POST['id_kebijakan'])){
                        $id_saspok = $wpdb->get_var($wpdb->prepare("
                            select id_saspok
                            from $table
                            where id=%d", $_POST['id_kebijakan']));
                        $where .= $wpdb->prepare('and id_saspok=%d', $id_saspok);
                    }else{
                        $where .= $wpdb->prepare('and id_saspok=%d', $_POST['id']);
                    }
                }else if($_POST['table'] == 'data_rpjpd_isu'){
                    $table = 'data_rpjpd_isu';
                    if(!empty($_POST['id_isu'])){
                        $id_kebijakan = $wpdb->get_var($wpdb->prepare("
                            select id_kebijakan
                            from $table
                            where id=%d", $_POST['id_isu']));
                        $where .= $wpdb->prepare('and id_kebijakan=%d', $id_kebijakan);
                    }else{
                        $where .= $wpdb->prepare('and id_kebijakan=%d', $_POST['id']);
                    }
                }
                $sql = "
                    select 
                        * 
                    from $table
                    $where
                ";
                $ret['data'] = $wpdb->get_results($sql, ARRAY_A);
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
        if(!empty($res)){
            return $ret;
        }else{
            die(json_encode($ret));
        }
    }

    public function simpan_rpjpd(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil simpan data RPJPD!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                $cek_jadwal = $this->validasi_jadwal_perencanaan('rpjpd');
                if($cek_jadwal['status'] == 'success'){
                    $table = '';
                    if($_POST['table'] == 'data_rpjpd_visi'){
                        $table = $_POST['table'];
                        $data = array(
                            'visi_teks' => $_POST['data'],
                            'update_at' => date('Y-m-d H:i:s')
                        );
                        if(!empty($_POST['id'])){
                            $wpdb->update($table, $data, array( "id" => $_POST['id'] ));
                            $ret['message'] = 'Berhasil update data RPJPD!';
                        }else{
                            $cek_id = $wpdb->get_var($wpdb->prepare("
                                select 
                                    id 
                                from $table
                                where visi_teks=%s
                            ", $_POST['data']));
                            if(!empty($cek_id)){
                                $ret['status'] = 'error';
                                $ret['message'] = 'Visi teks sudah ada!';
                            }else{
                                $wpdb->insert($table, $data);
                            }
                        }
                    }else if($_POST['table'] == 'data_rpjpd_misi'){
                        if(!empty($_POST['id_visi'])){
                            $table = $_POST['table'];
                            $data = array(
                                'id_visi' => $_POST['id_visi'],
                                'misi_teks' => $_POST['data'],
                                'update_at' => date('Y-m-d H:i:s')
                            );
                            if(!empty($_POST['id'])){
                                $wpdb->update($table, $data, array( "id" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPJPD!';
                            }else{
                                $cek_id = $wpdb->get_var($wpdb->prepare("
                                    select 
                                        id 
                                    from $table
                                    where misi_teks=%s
                                ", $_POST['data']));
                                if(!empty($cek_id)){
                                    $ret['status'] = 'error';
                                    $ret['message'] = 'Misi teks sudah ada!';
                                }else{
                                    $wpdb->insert($table, $data);
                                }
                            }
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'ID visi tidak boleh kosong!';
                        }
                    }else if($_POST['table'] == 'data_rpjpd_sasaran'){
                        if(!empty($_POST['id_misi'])){
                            $table = $_POST['table'];
                            $data = array(
                                'id_misi' => $_POST['id_misi'],
                                'saspok_teks' => $_POST['data'],
                                'update_at' => date('Y-m-d H:i:s')
                            );
                            if(!empty($_POST['id'])){
                                $wpdb->update($table, $data, array( "id" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPJPD!';
                            }else{
                                $cek_id = $wpdb->get_var($wpdb->prepare("
                                    select 
                                        id 
                                    from $table
                                    where saspok_teks=%s
                                ", $_POST['data']));
                                if(!empty($cek_id)){
                                    $ret['status'] = 'error';
                                    $ret['message'] = 'Sasaran teks sudah ada!';
                                }else{
                                    $wpdb->insert($table, $data);
                                }
                            }
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'ID misi tidak boleh kosong!';
                        }
                    }else if($_POST['table'] == 'data_rpjpd_kebijakan'){
                        if(!empty($_POST['id_saspok'])){
                            $table = $_POST['table'];
                            $data = array(
                                'id_saspok' => $_POST['id_saspok'],
                                'kebijakan_teks' => $_POST['data'],
                                'update_at' => date('Y-m-d H:i:s')
                            );
                            if(!empty($_POST['id'])){
                                $wpdb->update($table, $data, array( "id" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPJPD!';
                            }else{
                                $cek_id = $wpdb->get_var($wpdb->prepare("
                                    select 
                                        id 
                                    from $table
                                    where kebijakan_teks=%s
                                ", $_POST['data']));
                                if(!empty($cek_id)){
                                    $ret['status'] = 'error';
                                    $ret['message'] = 'Kebijakan teks sudah ada!';
                                }else{
                                    $wpdb->insert($table, $data);
                                }
                            }
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'ID sasaran tidak boleh kosong!';
                        }
                    }else if($_POST['table'] == 'data_rpjpd_isu'){
                        if(!empty($_POST['id_kebijakan'])){
                            $table = $_POST['table'];
                            $data = array(
                                'id_kebijakan' => $_POST['id_kebijakan'],
                                'isu_teks' => $_POST['data'],
                                'update_at' => date('Y-m-d H:i:s')
                            );
                            if(!empty($_POST['id'])){
                                $wpdb->update($table, $data, array( "id" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPJPD!';
                            }else{
                                $cek_id = $wpdb->get_var($wpdb->prepare("
                                    select 
                                        id 
                                    from $table
                                    where isu_teks=%s
                                ", $_POST['data']));
                                if(!empty($cek_id)){
                                    $ret['status'] = 'error';
                                    $ret['message'] = 'Isu teks sudah ada!';
                                }else{
                                    $wpdb->insert($table, $data);
                                }
                            }
                        }else{
                            $ret['status'] = 'error';
                            $ret['message'] = 'ID kebijakan tidak boleh kosong!';
                        }
                    }
                }else{
                    $ret['status'] = 'error';
                    $ret['message'] = 'Jadwal belum dimulai!';
                }
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

    public function hapus_rpjpd(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil hapus data RPJPD!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                $cek_jadwal = $this->validasi_jadwal_perencanaan('rpjpd');
                if($cek_jadwal['status'] == 'success'){
                    $table = '';
                    if($_POST['table'] == 'data_rpjpd_visi'){
                        $table = $_POST['table'];
                        $wpdb->delete($table, array('id' => $_POST['id']));
                    }else if($_POST['table'] == 'data_rpjpd_misi'){
                        $table = $_POST['table'];
                        $wpdb->delete($table, array('id' => $_POST['id']));
                    }else if($_POST['table'] == 'data_rpjpd_sasaran'){
                        $table = $_POST['table'];
                        $wpdb->delete($table, array('id' => $_POST['id']));
                    }else if($_POST['table'] == 'data_rpjpd_kebijakan'){
                        $table = $_POST['table'];
                        $wpdb->delete($table, array('id' => $_POST['id']));
                    }else if($_POST['table'] == 'data_rpjpd_isu'){
                        $table = $_POST['table'];
                        $wpdb->delete($table, array('id' => $_POST['id']));
                    }
                }else{
                    $ret['status'] = 'error';
                    $ret['message'] = 'Jadwal belum dimulai!';
                }
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

    public function get_rpd($cb = false){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil get data RPD!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                $table = '';
                $where = 'where 1=1 ';
                $type = $_POST['type'];
                if($type != 1){
                    $where .= 'and active=1';
                }
                if($_POST['table'] == 'data_rpd_tujuan_lokal'){
                    $table = $_POST['table'];
                    if(!empty($_POST['id_unik_tujuan'])){
                        $where .= $wpdb->prepare(' and id_unik=%s', $_POST['id_unik_tujuan']);
                    }else if(!empty($_POST['id_unik_tujuan_indikator'])){
                        $where .= $wpdb->prepare(' and id_unik_indikator=%s', $_POST['id_unik_tujuan_indikator']);
                    }
                }else if($_POST['table'] == 'data_rpd_sasaran_lokal'){
                    $table = $_POST['table'];
                    if(!empty($_POST['id_unik_tujuan'])){
                        $where .= $wpdb->prepare(' and kode_tujuan=%s', $_POST['id_unik_tujuan']);
                    }else if(!empty($_POST['id_unik_sasaran'])){
                        $where .= $wpdb->prepare(' and id_unik=%s', $_POST['id_unik_sasaran']);
                    }else if(!empty($_POST['id_unik_sasaran_indikator'])){
                        $where .= $wpdb->prepare(' and id_unik_indikator=%s', $_POST['id_unik_sasaran_indikator']);
                    }
                }else if($_POST['table'] == 'data_rpd_program_lokal'){
                    $table = $_POST['table'];
                    if(!empty($_POST['id_unik_sasaran'])){
                        $where .= $wpdb->prepare(' and kode_sasaran=%s', $_POST['id_unik_sasaran']);
                    }else if(!empty($_POST['id_unik_program'])){
                        $where .= $wpdb->prepare(' and id_unik=%s', $_POST['id_unik_program']);
                    }else if(!empty($_POST['id_unik_program_indikator'])){
                        $where .= $wpdb->prepare(' and id_unik_indikator=%s', $_POST['id_unik_program_indikator']);
                    }
                }else if($_POST['table'] == 'data_rpd_tujuan'){
                    $table = $_POST['table'];
                }
                if(!empty($table)){
                    $sql = $wpdb->prepare("
                        select 
                            * 
                        from $table
                        $where
                    ");
                    $ret['data'] = $wpdb->get_results($sql, ARRAY_A);
                    $data_all = array();
                    if($_POST['table'] == 'data_rpd_tujuan_lokal'){
                        foreach ($ret['data'] as $tujuan) {
                            if(empty($data_all[$tujuan['id_unik']])){
                                if(!empty($_POST['id_unik_tujuan_indikator'])){
                                    $_POST['id_unik_tujuan'] = $tujuan['id_unik'];
                                }
                                $data_all[$tujuan['id_unik']] = array(
                                    'id' => $tujuan['id'],
                                    'id_unik' => $tujuan['id_unik'],
                                    'nama' => $tujuan['tujuan_teks'],
                                    'rpjpd' => array(),
                                    'detail' => array()
                                );
                                if(!empty($_POST['id_unik_tujuan'])){
                                    $_POST['table'] = 'data_rpjpd_isu';
                                    $_POST['id_isu'] = $tujuan['id_isu'];
                                    $data_all[$tujuan['id_unik']]['rpjpd']['isu'] = array(
                                        'data' => $this->get_rpjpd(true),
                                        'id' => $tujuan['id_isu']
                                    );

                                    $_POST['table'] = 'data_rpjpd_kebijakan';
                                    $_POST['id_kebijakan'] = $data_all[$tujuan['id_unik']]['rpjpd']['isu']['data']['data'][0]['id_kebijakan'];
                                    $data_all[$tujuan['id_unik']]['rpjpd']['kebijakan'] = array(
                                        'data' => $this->get_rpjpd(true),
                                        'id' => $_POST['id_kebijakan']
                                    );

                                    $_POST['table'] = 'data_rpjpd_sasaran';
                                    $_POST['id_saspok'] = $data_all[$tujuan['id_unik']]['rpjpd']['kebijakan']['data']['data'][0]['id_saspok'];
                                    $data_all[$tujuan['id_unik']]['rpjpd']['sasaran'] = array(
                                        'data' => $this->get_rpjpd(true),
                                        'id' => $_POST['id_saspok']
                                    );

                                    $_POST['table'] = 'data_rpjpd_misi';
                                    $_POST['id_misi'] = $data_all[$tujuan['id_unik']]['rpjpd']['sasaran']['data']['data'][0]['id_misi'];
                                    $data_all[$tujuan['id_unik']]['rpjpd']['misi'] = array(
                                        'data' => $this->get_rpjpd(true),
                                        'id' => $_POST['id_misi']
                                    );

                                    $_POST['table'] = 'data_rpjpd_visi';
                                    $_POST['id_visi'] = $data_all[$tujuan['id_unik']]['rpjpd']['misi']['data']['data'][0]['id_visi'];
                                    $data_all[$tujuan['id_unik']]['rpjpd']['visi'] = array(
                                        'data' => $this->get_rpjpd(true),
                                        'id' => $_POST['id_visi']
                                    );
                                }
                            }
                            if(!empty($tujuan['id_unik_indikator'])){
                                $data_all[$tujuan['id_unik']]['detail'][] = $tujuan;
                            }
                        }
                    }else if($_POST['table'] == 'data_rpd_sasaran_lokal'){
                        foreach ($ret['data'] as $sasaran) {
                            if(empty($data_all[$sasaran['id_unik']])){
                                $data_all[$sasaran['id_unik']] = array(
                                    'id' => $sasaran['id'],
                                    'id_unik' => $sasaran['id_unik'],
                                    'nama' => $sasaran['sasaran_teks'],
                                    'detail' => array()
                                );
                            }
                            if(!empty($sasaran['id_unik_indikator'])){
                                $data_all[$sasaran['id_unik']]['detail'][] = $sasaran;
                            }
                        }
                    }else if($_POST['table'] == 'data_rpd_program_lokal'){
                        foreach ($ret['data'] as $program) {
                            if(empty($data_all[$program['id_unik']])){
                                $data_all[$program['id_unik']] = array(
                                    'id' => $program['id'],
                                    'id_unik' => $program['id_unik'],
                                    'id_program' => $program['id_program'],
                                    'nama' => $program['nama_program'],
                                    'detail' => array()
                                );
                            }
                            if(!empty($program['id_unik_indikator'])){
                                $data_all[$program['id_unik']]['detail'][] = $program;
                            }
                        }
                    }
                    $ret['data_all'] = $data_all;
                }else{
                    $ret['status'] = 'error';
                    $ret['message'] = 'table tidak bleh kosong!';
                }
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
        if(!empty($cb)){
            return $ret;
        }else{
            die(json_encode($ret));
        }
    }

    public function simpan_rpd(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil simpan data RPD!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                $cek_jadwal = $this->validasi_jadwal_perencanaan('rpd');
                if($cek_jadwal['status'] == 'success'){
                    $table = '';
                    if($_POST['table'] == 'data_rpd_tujuan_lokal'){
                        $table = $_POST['table'];
                        // simpan atau edit indikator tujuan
                        if(!empty($_POST['id_tujuan'])){
                            $tujuan = $wpdb->get_results($wpdb->prepare("
                                select
                                    id_isu,
                                    tujuan_teks,
                                    id_unik
                                from $table
                                where id_unik=%s
                            ", $_POST['id_tujuan']), ARRAY_A);
                            $data = array(
                                'id_isu' => $tujuan[0]['id_isu'],
                                'tujuan_teks' => $tujuan[0]['tujuan_teks'],
                                'id_unik' => $tujuan[0]['id_unik'],
                                'indikator_teks' => $_POST['data'],
                                'target_awal' => $_POST['vol_awal'].' '.$_POST['satuan_awal'],
                                'target_1' => $_POST['vol_1'].' '.$_POST['satuan_1'],
                                'target_2' => $_POST['vol_2'].' '.$_POST['satuan_2'],
                                'target_3' => $_POST['vol_3'].' '.$_POST['satuan_3'],
                                'target_4' => $_POST['vol_4'].' '.$_POST['satuan_4'],
                                'target_5' => $_POST['vol_5'].' '.$_POST['satuan_5'],
                                'target_akhir' => $_POST['vol_akhir'].' '.$_POST['satuan_akhir'],
                                'update_at' => date('Y-m-d H:i:s')
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik_indikator'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik_indikator" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik_indikator'] = time().'-'.$this->generateRandomString(5);
                                $cek_id = $wpdb->get_var($wpdb->prepare("
                                    select 
                                        id 
                                    from $table
                                    where indikator_teks=%s
                                        and id_unik=%s
                                ", $_POST['data'], $_POST['id_tujuan']));
                                if(!empty($cek_id)){
                                    $ret['status'] = 'error';
                                    $ret['message'] = 'Indikator tujuan teks sudah ada!';
                                }else{
                                    $wpdb->insert($table, $data);
                                }
                            }
                        // simpan atau edit tujuan
                        }else{
                            $data = array(
                                'id_isu' => $_POST['id_isu'],
                                'tujuan_teks' => $_POST['data'],
                                'update_at' => date('Y-m-d H:i:s')
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik'] = time().'-'.$this->generateRandomString(5);
                                $cek_id = $wpdb->get_var($wpdb->prepare("
                                    select 
                                        id 
                                    from $table
                                    where tujuan_teks=%s
                                ", $_POST['data']));
                                if(!empty($cek_id)){
                                    $ret['status'] = 'error';
                                    $ret['message'] = 'Tujuan teks sudah ada!';
                                }else{
                                    $wpdb->insert($table, $data);
                                }
                            }
                        }
                    }else if($_POST['table'] == 'data_rpd_sasaran_lokal'){
                        $table = $_POST['table'];
                        // simpan atau edit indikator sasaran
                        if(!empty($_POST['id_sasaran'])){
                            $sasaran = $wpdb->get_results($wpdb->prepare("
                                select
                                    kode_tujuan,
                                    sasaran_teks,
                                    id_unik
                                from $table
                                where id_unik=%s
                            ", $_POST['id_sasaran']), ARRAY_A);
                            $data = array(
                                'kode_tujuan' => $sasaran[0]['kode_tujuan'],
                                'sasaran_teks' => $sasaran[0]['sasaran_teks'],
                                'id_unik' => $sasaran[0]['id_unik'],
                                'indikator_teks' => $_POST['data'],
                                'target_awal' => $_POST['vol_awal'].' '.$_POST['satuan_awal'],
                                'target_1' => $_POST['vol_1'].' '.$_POST['satuan_1'],
                                'target_2' => $_POST['vol_2'].' '.$_POST['satuan_2'],
                                'target_3' => $_POST['vol_3'].' '.$_POST['satuan_3'],
                                'target_4' => $_POST['vol_4'].' '.$_POST['satuan_4'],
                                'target_5' => $_POST['vol_5'].' '.$_POST['satuan_5'],
                                'target_akhir' => $_POST['vol_akhir'].' '.$_POST['satuan_akhir'],
                                'update_at' => date('Y-m-d H:i:s')
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik_indikator'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik_indikator" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik_indikator'] = time().'-'.$this->generateRandomString(5);
                                $cek_id = $wpdb->get_var($wpdb->prepare("
                                    select 
                                        id 
                                    from $table
                                    where indikator_teks=%s
                                        and id_unik=%s
                                ", $_POST['data'], $_POST['id_sasaran']));
                                if(!empty($cek_id)){
                                    $ret['status'] = 'error';
                                    $ret['message'] = 'Indikator sasaran teks sudah ada!';
                                }else{
                                    $wpdb->insert($table, $data);
                                }
                            }
                        // simpan atau edit sasaran
                        }else{
                            $data = array(
                                'kode_tujuan' => $_POST['id_tujuan'],
                                'sasaran_teks' => $_POST['data'],
                                'update_at' => date('Y-m-d H:i:s')
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik'] = time().'-'.$this->generateRandomString(5);
                                $cek_id = $wpdb->get_var($wpdb->prepare("
                                    select 
                                        id 
                                    from $table
                                    where sasaran_teks=%s
                                ", $_POST['data']));
                                if(!empty($cek_id)){
                                    $ret['status'] = 'error';
                                    $ret['message'] = 'Sasaran teks sudah ada!';
                                }else{
                                    $wpdb->insert($table, $data);
                                }
                            }
                        }
                    }else if($_POST['table'] == 'data_rpd_program_lokal'){
                        $table = $_POST['table'];
                        // simpan atau edit indikator program
                        if(!empty($_POST['id_program'])){
                            $program = $wpdb->get_results($wpdb->prepare("
                                select
                                    kode_sasaran,
                                    nama_program,
                                    id_program,
                                    id_unik
                                from $table
                                where id_unik=%s
                            ", $_POST['id_program']), ARRAY_A);
                            $data = array(
                                'kode_sasaran' => $program[0]['kode_sasaran'],
                                'nama_program' => $program[0]['nama_program'],
                                'id_program' => $program[0]['id_program'],
                                'id_unik' => $program[0]['id_unik'],
                                'id_unit' => $_POST['id_skpd'],
                                'kode_skpd' => $_POST['kode_skpd'],
                                'nama_skpd' => $_POST['nama_skpd'],
                                'indikator' => $_POST['data'],
                                'target_awal' => $_POST['vol_awal'].' '.$_POST['satuan_awal'],
                                'target_1' => $_POST['vol_1'].' '.$_POST['satuan_1'],
                                'target_2' => $_POST['vol_2'].' '.$_POST['satuan_2'],
                                'target_3' => $_POST['vol_3'].' '.$_POST['satuan_3'],
                                'target_4' => $_POST['vol_4'].' '.$_POST['satuan_4'],
                                'target_5' => $_POST['vol_5'].' '.$_POST['satuan_5'],
                                'target_akhir' => $_POST['vol_akhir'].' '.$_POST['satuan_akhir'],
                                'update_at' => date('Y-m-d H:i:s')
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik_indikator'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik_indikator" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik_indikator'] = time().'-'.$this->generateRandomString(5);
                                $cek_id = $wpdb->get_var($wpdb->prepare("
                                    select 
                                        id 
                                    from $table
                                    where indikator=%s
                                        and id_unik=%s
                                ", $_POST['data'], $_POST['id_program']));
                                if(!empty($cek_id)){
                                    $ret['status'] = 'error';
                                    $ret['message'] = 'Indikator program sudah ada!';
                                }else{
                                    $wpdb->insert($table, $data);
                                }
                            }
                        // simpan atau edit program
                        }else{
                            $data = array(
                                'kode_sasaran' => $_POST['id_sasaran'],
                                'nama_program' => $_POST['nama_program'],
                                'id_program' => $_POST['data'],
                                'update_at' => date('Y-m-d H:i:s')
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik'] = time().'-'.$this->generateRandomString(5);
                                $cek_id = $wpdb->get_var($wpdb->prepare("
                                    select 
                                        id 
                                    from $table
                                    where nama_program=%s
                                ", $_POST['nama_program']));
                                if(!empty($cek_id)){
                                    $ret['status'] = 'error';
                                    $ret['message'] = 'Program teks sudah ada!';
                                }else{
                                    $wpdb->insert($table, $data);
                                }
                            }
                        }
                    }
                }else{
                    $ret['status'] = 'error';
                    $ret['message'] = 'Jadwal belum dimulai!';
                }
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

    public function hapus_rpd(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil hapus data RPD!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                $cek_jadwal = $this->validasi_jadwal_perencanaan('rpd');
                if($cek_jadwal['status'] == 'success'){
                    $table = '';
                    if($_POST['table'] == 'data_rpd_tujuan_lokal'){
                        $table = $_POST['table'];
                        if(!empty($_POST['id_unik_tujuan_indikator'])){
                            $wpdb->delete($table, array('id_unik_indikator' => $_POST['id_unik_tujuan_indikator']));
                        }else{
                            $wpdb->delete($table, array('id_unik' => $_POST['id']));
                        }
                    }else if($_POST['table'] == 'data_rpd_sasaran_lokal'){
                        $table = $_POST['table'];
                        if(!empty($_POST['id_unik_sasaran_indikator'])){
                            $wpdb->delete($table, array('id_unik_indikator' => $_POST['id_unik_sasaran_indikator']));
                        }else{
                            $wpdb->delete($table, array('id_unik' => $_POST['id']));
                        }
                    }else if($_POST['table'] == 'data_rpd_program_lokal'){
                        $table = $_POST['table'];
                        if(!empty($_POST['id_unik_program_indikator'])){
                            $wpdb->delete($table, array('id_unik_indikator' => $_POST['id_unik_program_indikator']));
                        }else{
                            $wpdb->delete($table, array('id_unik' => $_POST['id']));
                        }
                    }else{
                        $ret['status'] = 'error';
                        $ret['message'] = 'Param table tidak tidak boleh kosong!';
                    }
                }else{
                    $ret['status'] = 'error';
                    $ret['message'] = 'Jadwal belum dimulai!';
                }
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

    public function get_bidang_urusan(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil get data bidang urusan!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                $tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
                if(!empty($_POST['type']) && $_POST['type'] == 1){
                    $data = $wpdb->get_results("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program
                        FROM data_prog_keg as u 
                        WHERE u.tahun_anggaran=$tahun_anggaran
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ");
                }else{
                    $data = $wpdb->get_results("
                        SELECT
                            s.id_skpd,
                            s.kode_skpd,
                            s.nama_skpd,
                            u.nama_urusan,
                            u.nama_bidang_urusan
                        FROM data_prog_keg as u 
                        LEFT JOIN data_unit as s on s.kode_skpd like CONCAT('%',u.kode_bidang_urusan,'%')
                            and s.active=1
                            and s.is_skpd=1
                            and s.tahun_anggaran=u.tahun_anggaran
                        WHERE u.tahun_anggaran=$tahun_anggaran
                        GROUP BY u.nama_bidang_urusan, s.kode_skpd
                        ORDER BY u.nama_bidang_urusan ASC, s.kode_skpd ASC 
                    ");
                }
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
}