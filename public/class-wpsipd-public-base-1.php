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

    public function get_rpjpd($res=false, $id_jadwal_history=false){
        global $wpdb;
        $ret = array(
            'action'    => 'get_rpjpd',
            'status'    => 'success',
            'message'   => 'Berhasil mengambil data RPJPD dari data SIPD lokal!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                $table = '';
                $where = 'where 1=1 ';

                if($_POST['table'] == 'all'){
                    $sql = "
                        select 
                            v.id as id_visi,
                            v.visi_teks,
                            m.id as id_misi,
                            m.misi_teks,
                            m.urut_misi,
                            s.id as id_sasaran,
                            s.saspok_teks,
                            s.urut_saspok,
                            k.id as id_kebijakan,
                            k.kebijakan_teks,
                            k.urut_kebijakan,
                            i.id as id_isu,
                            i.isu_teks,
                            i.urut_isu
                        from data_rpjpd_visi v
                        left join data_rpjpd_misi m on v.id=m.id_visi
                        left join data_rpjpd_sasaran s on m.id=s.id_misi
                        left join data_rpjpd_kebijakan k on s.id=k.id_saspok
                        left join data_rpjpd_isu i on k.id=i.id_kebijakan
                    ";
                    $ret['data'] = $wpdb->get_results($sql, ARRAY_A);
                }else{
                    if(!empty($id_jadwal_history)){
                        if($_POST['table'] == 'data_rpjpd_visi'){
                            $table = 'data_rpjpd_visi_history';
                            $where .= "and id_jadwal=".$id_jadwal_history." ";
                        }else if($_POST['table'] == 'data_rpjpd_misi'){
                            $table = 'data_rpjpd_misi_history';
                            $where .= "and id_jadwal=".$id_jadwal_history." ";
                            if(!empty($_POST['id_misi'])){
                                $id_visi = $wpdb->get_var($wpdb->prepare("
                                    select id_visi
                                    from $table
                                    where id_asli=%d
                                        and id_jadwal=%d", 
                                    $_POST['id_misi'],
                                    $id_jadwal_history
                                ));
                                $where .= $wpdb->prepare('and id_visi=%d', $id_visi);
                            }else{
                                $where .= $wpdb->prepare('and id_visi=%d', $_POST['id']);
                            }
                        }else if($_POST['table'] == 'data_rpjpd_sasaran'){
                            $table = 'data_rpjpd_sasaran_history';
                            $where .= "and id_jadwal=".$id_jadwal_history." ";
                            if(!empty($_POST['id_saspok'])){
                                $id_misi = $wpdb->get_var($wpdb->prepare("
                                    select id_misi
                                    from $table
                                    where id_asli=%d
                                        and id_jadwal=%d", 
                                    $_POST['id_saspok'],
                                    $id_jadwal_history
                                ));
                                $where .= $wpdb->prepare('and id_misi=%d', $id_misi);
                            }else{
                                $where .= $wpdb->prepare('and id_misi=%d', $_POST['id']);
                            }
                        }else if($_POST['table'] == 'data_rpjpd_kebijakan'){
                            $table = 'data_rpjpd_kebijakan_history';
                            $where .= "and id_jadwal=".$id_jadwal_history." ";
                            if(!empty($_POST['id_kebijakan'])){
                                $id_saspok = $wpdb->get_var($wpdb->prepare("
                                    select id_saspok
                                    from $table
                                    where id_asli=%d
                                        and id_jadwal=%d", 
                                    $_POST['id_kebijakan'],
                                    $id_jadwal_history
                                ));
                                $where .= $wpdb->prepare('and id_saspok=%d', $id_saspok);
                            }else{
                                $where .= $wpdb->prepare('and id_saspok=%d', $_POST['id']);
                            }
                        }else if($_POST['table'] == 'data_rpjpd_isu'){
                            $table = 'data_rpjpd_isu_history';
                            $where .= "and id_jadwal=".$id_jadwal_history." ";
                            if(!empty($_POST['id_isu'])){
                                $id_kebijakan = $wpdb->get_var($wpdb->prepare("
                                    select id_kebijakan
                                    from $table
                                    where id_asli=%d
                                        and id_jadwal=%d", 
                                    $_POST['id_isu'],
                                    $id_jadwal_history
                                ));
                                $where .= $wpdb->prepare('and id_kebijakan=%d', $id_kebijakan);
                            }else{
                                $where .= $wpdb->prepare('and id_kebijakan=%d', $_POST['id']);
                            }
                        }
                    }else{
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
                    }
                    $sql = "
                        select 
                            * 
                        from $table
                        $where
                    ";
                    $ret['data'] = $wpdb->get_results($sql, ARRAY_A);
                    if(!empty($id_jadwal_history)){
                        foreach($ret['data'] as $k => $data){
                            $ret['data'][$k]['id'] = $data['id_asli'];
                        }
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
            'action'    => 'get_rpd',
            'status'    => 'success',
            'message'   => 'Berhasil get data RPD!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                $id_jadwal_rpjpd = "";
                if(!empty($_POST['id_unik_tujuan'])){
                    $jadwal_rpd = $wpdb->get_results("select * from data_jadwal_lokal where id_tipe=3 and status=0", ARRAY_A);
                    if(!empty($jadwal_rpd)){
                        $id_jadwal_rpjpd = $jadwal_rpd[0]['relasi_perencanaan'];
                    }
                }
                $table = '';
                $where = 'where 1=1 and active=1';
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
                    $where .= ' ORDER BY no_urut ASC, id ASC';
                }else if($_POST['table'] == 'data_rpd_sasaran_lokal'){
                    $table = $_POST['table'];
                    if(!empty($_POST['id_unik_tujuan'])){
                        $where .= $wpdb->prepare(' and kode_tujuan=%s', $_POST['id_unik_tujuan']);
                    }else if(!empty($_POST['id_unik_sasaran'])){
                        $where .= $wpdb->prepare(' and id_unik=%s', $_POST['id_unik_sasaran']);
                    }else if(!empty($_POST['id_unik_sasaran_indikator'])){
                        $where .= $wpdb->prepare(' and id_unik_indikator=%s', $_POST['id_unik_sasaran_indikator']);
                    }
                    $where .= ' ORDER BY sasaran_no_urut ASC, id ASC';
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
                    // die($sql);
                    $ret['data'] = $wpdb->get_results($sql, ARRAY_A);
                    $data_all = array();
                    if($_POST['table'] == 'data_rpd_tujuan_lokal'){
                        foreach ($ret['data'] as $tujuan) {
                            if(empty($data_all[$tujuan['id_unik']])){
                                if(!empty($_POST['id_unik_tujuan_indikator'])){
                                    $_POST['id_unik_tujuan'] = $tujuan['id_unik'];
                                }
                                $sasaran = $wpdb->get_results($wpdb->prepare("
                                    SELECT 
                                        id_unik
                                    from data_rpd_sasaran_lokal 
                                    where id_unik_indikator IS NULL
                                        AND active=1
                                        AND kode_tujuan=%s
                                ", $tujuan['id_unik']), ARRAY_A);
                                $kd_all_sasaran = array();
                                foreach($sasaran as $sas){
                                    $kd_all_sasaran[] = "'".$sas['id_unik']."'";
                                }
                                $kd_all_sasaran =  implode(',', $kd_all_sasaran);
                                if(empty($kd_all_sasaran)){
                                    $kd_all_sasaran = 0;
                                }

                                $program = $wpdb->get_results($wpdb->prepare("
                                    SELECT 
                                        id_unik
                                    from data_rpd_program_lokal 
                                    where id_unik_indikator IS NULL
                                        AND active=1
                                        AND kode_sasaran in ($kd_all_sasaran)
                                ", $sasaran['id_unik']), ARRAY_A);
                                $kd_all_prog = array();
                                foreach($program as $prog){
                                    $kd_all_prog[] = "'".$prog['id_unik']."'";
                                }
                                $kd_all_prog =  implode(',', $kd_all_prog);
                                if(empty($kd_all_prog)){
                                    $kd_all_prog = 0;
                                }

                                $pagu = $wpdb->get_row($wpdb->prepare("
                                    SELECT 
                                        sum(pagu_1) as pagu_akumulasi_1,
                                        sum(pagu_2) as pagu_akumulasi_2,
                                        sum(pagu_3) as pagu_akumulasi_3,
                                        sum(pagu_4) as pagu_akumulasi_4,
                                        sum(pagu_5) as pagu_akumulasi_5
                                    from data_rpd_program_lokal 
                                    where id_unik_indikator IS NOT NULL
                                        AND active=1
                                        AND id_unik in ($kd_all_prog)
                                "), ARRAY_A);
                                $data_all[$tujuan['id_unik']] = array(
                                    'id' => $tujuan['id'],
                                    'id_unik' => $tujuan['id_unik'],
                                    'nama' => $tujuan['tujuan_teks'],
                                    'id_jadwal_rpjpd' => $id_jadwal_rpjpd,
                                    'pagu_akumulasi_1' => $pagu['pagu_akumulasi_1'],
                                    'pagu_akumulasi_2' => $pagu['pagu_akumulasi_2'],
                                    'pagu_akumulasi_3' => $pagu['pagu_akumulasi_3'],
                                    'pagu_akumulasi_4' => $pagu['pagu_akumulasi_4'],
                                    'pagu_akumulasi_5' => $pagu['pagu_akumulasi_5'],
                                    'rpjpd' => array(),
                                    'detail' => array(),
                                    'no_urut' => $tujuan['no_urut'],
                                    'catatan_teks_tujuan' => $tujuan['catatan_teks_tujuan'],
                                    'indikator_catatan_teks' => $tujuan['indikator_catatan_teks']
                                );
                                if(!empty($_POST['id_unik_tujuan'])){
                                    $_POST['table'] = 'data_rpjpd_isu';
                                    $_POST['id_isu'] = $tujuan['id_isu'];
                                    $data_all[$tujuan['id_unik']]['rpjpd']['isu'] = array(
                                        'data' => $this->get_rpjpd(true, $id_jadwal_rpjpd),
                                        'id' => $tujuan['id_isu']
                                    );

                                    $_POST['table'] = 'data_rpjpd_kebijakan';
                                    $_POST['id_kebijakan'] = $data_all[$tujuan['id_unik']]['rpjpd']['isu']['data']['data'][0]['id_kebijakan'];
                                    $data_all[$tujuan['id_unik']]['rpjpd']['kebijakan'] = array(
                                        'data' => $this->get_rpjpd(true, $id_jadwal_rpjpd),
                                        'id' => $_POST['id_kebijakan']
                                    );

                                    $_POST['table'] = 'data_rpjpd_sasaran';
                                    $_POST['id_saspok'] = $data_all[$tujuan['id_unik']]['rpjpd']['kebijakan']['data']['data'][0]['id_saspok'];
                                    $data_all[$tujuan['id_unik']]['rpjpd']['sasaran'] = array(
                                        'data' => $this->get_rpjpd(true, $id_jadwal_rpjpd),
                                        'id' => $_POST['id_saspok']
                                    );

                                    $_POST['table'] = 'data_rpjpd_misi';
                                    $_POST['id_misi'] = $data_all[$tujuan['id_unik']]['rpjpd']['sasaran']['data']['data'][0]['id_misi'];
                                    $data_all[$tujuan['id_unik']]['rpjpd']['misi'] = array(
                                        'data' => $this->get_rpjpd(true, $id_jadwal_rpjpd),
                                        'id' => $_POST['id_misi']
                                    );

                                    $_POST['table'] = 'data_rpjpd_visi';
                                    $_POST['id_visi'] = $data_all[$tujuan['id_unik']]['rpjpd']['misi']['data']['data'][0]['id_visi'];
                                    $data_all[$tujuan['id_unik']]['rpjpd']['visi'] = array(
                                        'data' => $this->get_rpjpd(true, $id_jadwal_rpjpd),
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
                                $program = $wpdb->get_results($wpdb->prepare("
                                    SELECT 
                                        id_unik
                                    from data_rpd_program_lokal 
                                    where id_unik_indikator IS NULL
                                        AND active=1
                                        AND kode_sasaran=%s
                                ", $sasaran['id_unik']), ARRAY_A);
                                $kd_all_prog = array();
                                foreach($program as $prog){
                                    $kd_all_prog[] = "'".$prog['id_unik']."'";
                                }
                                $kd_all_prog =  implode(',', $kd_all_prog);
                                if(empty($kd_all_prog)){
                                    $kd_all_prog = 0;
                                }
                                $pagu = $wpdb->get_row($wpdb->prepare("
                                    SELECT 
                                        sum(pagu_1) as pagu_akumulasi_1,
                                        sum(pagu_2) as pagu_akumulasi_2,
                                        sum(pagu_3) as pagu_akumulasi_3,
                                        sum(pagu_4) as pagu_akumulasi_4,
                                        sum(pagu_5) as pagu_akumulasi_5
                                    from data_rpd_program_lokal 
                                    where id_unik_indikator IS NOT NULL
                                        AND active=1
                                        AND id_unik in ($kd_all_prog)
                                "), ARRAY_A);
                                $data_all[$sasaran['id_unik']] = array(
                                    'id' => $sasaran['id'],
                                    'id_unik' => $sasaran['id_unik'],
                                    'nama' => $sasaran['sasaran_teks'],
                                    'pagu_akumulasi_1' => $pagu['pagu_akumulasi_1'],
                                    'pagu_akumulasi_2' => $pagu['pagu_akumulasi_2'],
                                    'pagu_akumulasi_3' => $pagu['pagu_akumulasi_3'],
                                    'pagu_akumulasi_4' => $pagu['pagu_akumulasi_4'],
                                    'pagu_akumulasi_5' => $pagu['pagu_akumulasi_5'],
                                    'sasaran_no_urut' => $sasaran['sasaran_no_urut'],
                                    'sasaran_catatan' => $sasaran['sasaran_catatan'],
                                    'indikator_catatan_teks'=> $sasaran['indikator_catatan_teks'],
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
                                $pagu = $wpdb->get_row($wpdb->prepare("
                                    SELECT 
                                        sum(pagu_1) as pagu_akumulasi_1,
                                        sum(pagu_2) as pagu_akumulasi_2,
                                        sum(pagu_3) as pagu_akumulasi_3,
                                        sum(pagu_4) as pagu_akumulasi_4,
                                        sum(pagu_5) as pagu_akumulasi_5
                                    from data_rpd_program_lokal 
                                    where id_unik_indikator IS NOT NULL
                                        AND active=1
                                        AND id_unik=%s
                                ", $program['id_unik']), ARRAY_A);
                                $data_all[$program['id_unik']] = array(
                                    'id' => $program['id'],
                                    'id_unik' => $program['id_unik'],
                                    'id_program' => $program['id_program'],
                                    'catatan' => $program['catatan'],
                                    'nama' => $program['nama_program'],
                                    'pagu_akumulasi_1' => $pagu['pagu_akumulasi_1'],
                                    'pagu_akumulasi_2' => $pagu['pagu_akumulasi_2'],
                                    'pagu_akumulasi_3' => $pagu['pagu_akumulasi_3'],
                                    'pagu_akumulasi_4' => $pagu['pagu_akumulasi_4'],
                                    'pagu_akumulasi_5' => $pagu['pagu_akumulasi_5'],
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
                                    id_unik,
                                    no_urut
                                from $table
                                where id_unik=%s
                            ", $_POST['id_tujuan']), ARRAY_A);
                            for($i=1; $i<=5; $i++){
                                if(empty($_POST['vol_'.$i]) && empty($_POST['satuan_'.$i])){
                                    $_POST['vol_'.$i] = '';
                                    $_POST['satuan_'.$i] = '';
                                }
                            }
                            $data = array(
                                'id_isu' => $tujuan[0]['id_isu'],
                                'tujuan_teks' => $tujuan[0]['tujuan_teks'],
                                'id_unik' => $tujuan[0]['id_unik'],
                                'indikator_teks' => $_POST['data'],
                                'target_awal' => $_POST['vol_awal'],
                                'target_1' => $_POST['vol_1'],
                                'target_2' => $_POST['vol_2'],
                                'target_3' => $_POST['vol_3'],
                                'target_4' => $_POST['vol_4'],
                                'target_5' => $_POST['vol_5'],
                                'target_akhir' => $_POST['vol_akhir'],
                                'update_at' => date('Y-m-d H:i:s'),
                                'no_urut' => $tujuan[0]['no_urut'],
                                'indikator_catatan_teks' => $_POST['indikator_catatan_teks'],
                                'satuan' => $_POST['satuan'],
                                'active' => 1
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik_indikator'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik_indikator" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik_indikator'] = $this->generateRandomString(5);
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
                                'update_at' => date('Y-m-d H:i:s'),
                                'no_urut'   => $_POST['no_urut'],
                                'catatan_teks_tujuan'   => $_POST['catatan_teks_tujuan'],
                                'active' => 1
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik'] = $this->generateRandomString(5);
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
                                    id_unik,
                                    sasaran_no_urut
                                from $table
                                where id_unik=%s
                            ", $_POST['id_sasaran']), ARRAY_A);
                            for($i=1; $i<=5; $i++){
                                if(empty($_POST['vol_'.$i]) && empty($_POST['satuan_'.$i])){
                                    $_POST['vol_'.$i] = '';
                                    $_POST['satuan_'.$i] = '';
                                }
                            }
                            $data = array(
                                'kode_tujuan' => $sasaran[0]['kode_tujuan'],
                                'sasaran_teks' => $sasaran[0]['sasaran_teks'],
                                'id_unik' => $sasaran[0]['id_unik'],
                                'indikator_teks' => $_POST['data'],
                                'target_awal' => $_POST['vol_awal'],
                                'target_1' => $_POST['vol_1'],
                                'target_2' => $_POST['vol_2'],
                                'target_3' => $_POST['vol_3'],
                                'target_4' => $_POST['vol_4'],
                                'target_5' => $_POST['vol_5'],
                                'target_akhir' => $_POST['vol_akhir'],
                                'update_at' => date('Y-m-d H:i:s'),
                                'sasaran_no_urut' => $sasaran[0]['sasaran_no_urut'],
                                'indikator_catatan_teks' => $_POST['indikator_catatan_teks'],
                                'satuan' => $_POST['satuan'],
                                'active' => 1
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik_indikator'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik_indikator" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik_indikator'] = $this->generateRandomString(5);
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
                                'update_at' => date('Y-m-d H:i:s'),
                                'sasaran_no_urut' => $_POST['sasaran_no_urut'],
                                'sasaran_catatan' => $_POST['sasaran_catatan'],
                                'active' => 1
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik'] = $this->generateRandomString(5);
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
                            for($i=1; $i<=5; $i++){
                                if(empty($_POST['vol_'.$i]) && empty($_POST['satuan_'.$i])){
                                    $_POST['vol_'.$i] = '';
                                    $_POST['satuan_'.$i] = '';
                                    $_POST['pagu_'.$i] = '';
                                }
                            }
                            $data = array(
                                'kode_sasaran' => $program[0]['kode_sasaran'],
                                'nama_program' => $program[0]['nama_program'],
                                'id_program' => $program[0]['id_program'],
                                'id_unik' => $program[0]['id_unik'],
                                'id_unit' => $_POST['id_skpd'],
                                'kode_skpd' => $_POST['kode_skpd'],
                                'nama_skpd' => $_POST['nama_skpd'],
                                'indikator' => $_POST['data'],
                                'target_awal' => $_POST['vol_awal'],
                                'target_1' => $_POST['vol_1'],
                                'pagu_1' => $_POST['pagu_1'],
                                'target_2' => $_POST['vol_2'],
                                'pagu_2' => $_POST['pagu_2'],
                                'target_3' => $_POST['vol_3'],
                                'pagu_3' => $_POST['pagu_3'],
                                'target_4' => $_POST['vol_4'],
                                'pagu_4' => $_POST['pagu_4'],
                                'target_5' => $_POST['vol_5'],
                                'pagu_5' => $_POST['pagu_5'],
                                'target_akhir' => $_POST['vol_akhir'],
                                'catatan' => $_POST['catatan'],
                                'satuan' => $_POST['satuan'],
                                'update_at' => date('Y-m-d H:i:s'),
                                'active' => 1
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik_indikator'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik_indikator" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik_indikator'] = $this->generateRandomString(5);
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
                                'catatan' => $_POST['catatan'],
                                'id_program' => $_POST['data'],
                                'update_at' => date('Y-m-d H:i:s'),
                                'active' => 1
                            );
                            if(!empty($_POST['id'])){
                                $data['id_unik'] = $_POST['id'];
                                $wpdb->update($table, $data, array( "id_unik" => $_POST['id'] ));
                                $ret['message'] = 'Berhasil update data RPD!';
                            }else{
                                $data['id_unik'] = $this->generateRandomString(5);
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
                            u.nama_program,
                            u.kode_program,
                            u.id_program,
                            u.nama_bidang_urusan
                        FROM data_prog_keg as u 
                        INNER JOIN data_unit as s on s.kode_skpd like CONCAT('%',u.kode_bidang_urusan,'%')
                            and s.active=1
                            and s.is_skpd=1
                            and s.tahun_anggaran=u.tahun_anggaran
                        WHERE u.tahun_anggaran=$tahun_anggaran
                        GROUP BY u.kode_program, s.kode_skpd
                        ORDER BY u.kode_program ASC, s.kode_skpd ASC 
                    ", ARRAY_A);
                    $data2 = $wpdb->get_results("
                        SELECT
                            s.id_skpd,
                            s.kode_skpd,
                            s.nama_skpd,
                            u.nama_urusan,
                            u.nama_program,
                            u.kode_program,
                            u.id_program,
                            u.nama_bidang_urusan
                        FROM data_prog_keg as u 
                        INNER JOIN data_unit as s on s.active=1
                            and s.is_skpd=1
                            and s.tahun_anggaran=u.tahun_anggaran
                        WHERE u.tahun_anggaran=$tahun_anggaran
                            and u.kode_bidang_urusan like 'X.XX%'
                        GROUP BY u.kode_program, s.kode_skpd
                        ORDER BY u.kode_program ASC, s.kode_skpd ASC 
                    ", ARRAY_A);
                    $data = array_merge($data, $data2);
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

    public function get_data_sub_giat(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'action'    => 'get_data_sub_giat',
            'message'   => 'Berhasil get data sub kegiatan!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                $tahun_anggaran = $_POST['tahun_anggaran'];
                $data = $wpdb->get_results($wpdb->prepare("
                    SELECT
                        u.*
                    FROM data_prog_keg as u 
                    WHERE u.tahun_anggaran=%d
                ", $tahun_anggaran), ARRAY_A);
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

    public function copy_data_renstra_ke_renja(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'action'    => 'get_data_sub_giat',
            'message'   => 'Berhasil copy data RENSTRA dengan RENJA!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                // belum selesai
                $data_sub_keg_renstra = $wpdb->get_results($wpdb->prepare(
                    'SELECT k.*,p.kode_urusan,p.id_urusan,p.nama_urusan
                    FROM data_renstra_sub_kegiatan_lokal k
                    LEFT JOIN data_prog_keg p
                    ON k.kode_sub_giat=p.kode_sub_giat
                    WHERE k.id_unik IS NOT NULL
                    AND k.id_unik_indikator IS NULL
                    AND k.active=1'
                ),ARRAY_A);

                // $id_jadwal_lokal = $_POST['id_jadwal']
                // $relasi = $wpdb->get_row($
                // 'SELECT 
                //     *
                // FROM `data_jadwal_lokal`
                // WHERE id_jadwal_lokal=".$jadwal_lokal[0]['relasi_perencanaan']);

                // if(!empty($data_sub_keg_renstra)){
                //     foreach ($data_sub_keg_renstra as $v_sub_keg_renstra) {
                //         $kode_sub_skpd = substr($v_sub_keg_renstra['nama_sub_unit'],0,22);
                //         $kode_bl = $v_sub_keg_renstra['id_unit'].'.'.$v_sub_keg_renstra['id_sub_unit'].'.'.$v_sub_keg_renstra['id_program'].'.'.$v_sub_keg_renstra['id_giat'];
                //         $kode_sbl = $kode_bl.'.'.$v_sub_keg_renstra['id_sub_giat'];

                //         $v_sub_keg_renstra['tahun_anggaran'] = 2023; //sementara manual karena di renstra '0000'
                //         $opsi_sub_keg_renstra = array(
                //             'id_sub_skpd' => $v_sub_keg_renstra['id_sub_unit'],
                //             'id_sub_giat' => $v_sub_keg_renstra['id_sub_giat'],
                //             'id_skpd' => $v_sub_keg_renstra['id_unit'],
                //             'kode_bl' => $kode_bl,
                //             'kode_sbl' => $kode_sbl,
                //             'nama_skpd' => $v_sub_keg_renstra['nama_skpd'],
                //             'kode_skpd' => $v_sub_keg_renstra['kode_skpd'],
                //             'nama_sub_skpd' => $v_sub_keg_renstra['nama_sub_unit'],
                //             'kode_sub_skpd' => $kode_sub_skpd,  
                //             'pagu' => $v_sub_keg_renstra['pagu_1'],
                //             'pagu_usulan' => $v_sub_keg_renstra['pagu_1_usulan'],
                //             'pagu_n_depan' => $v_sub_keg_renstra['pagu_2'],
                //             'pagu_n_depan_usulan' => $v_sub_keg_renstra['pagu_2_usulan'],
                //             'kode_urusan' => $v_sub_keg_renstra['kode_urusan'],
                //             'id_urusan' => $v_sub_keg_renstra['id_urusan'],
                //             'nama_urusan' => $v_sub_keg_renstra['nama_urusan'],
                //             'id_bidang_urusan' => $v_sub_keg_renstra['kode_bidang_urusan'],
                //             'kode_bidang_urusan' => $v_sub_keg_renstra['id_bidang_urusan'],
                //             'nama_bidang_urusan' => $v_sub_keg_renstra['nama_bidang_urusan'],
                //             'id_program' => $v_sub_keg_renstra['id_program'],
                //             'kode_program' => $v_sub_keg_renstra['kode_program'],
                //             'nama_program' => $v_sub_keg_renstra['nama_program'],
                //             'kode_giat' => $v_sub_keg_renstra['kode_giat'],
                //             'nama_giat' => $v_sub_keg_renstra['nama_giat'],
                //             'kode_sub_giat' => $v_sub_keg_renstra['kode_sub_giat'],
                //             'nama_sub_giat' => $v_sub_keg_renstra['nama_sub_giat'],
                //             'catatan' => $v_sub_keg_renstra['catatan'],
                //             'catatan_usulan' => $v_sub_keg_renstra['catatan_usulan'],
                //             'waktu_awal' => 1,
                //             'waktu_akhir' => 12,
                //             'waktu_awal_usulan' => 1,
                //             'waktu_akhir_usulan' => 12,
                //             'active' => 1,
                //             'tahun_anggaran' => $v_sub_keg_renstra['tahun_anggaran'],
                //             'update_at' => current_time('mysql')
                //         );
                        
                //         $cek_data_renja = $wpdb->get_results($wpdb->prepare(
                //                 'SELECT id
                //                 FROM data_sub_keg_bl_lokal
                //                 WHERE kode_sub_giat=%s
                //                 AND kode_skpd=%s
                //                 AND id_sub_skpd=%s
                //                 AND tahun_anggaran=%d
                //                 AND active=1',
                //                 $v_sub_keg_renstra['kode_sub_giat'],$v_sub_keg_renstra['kode_skpd'],$v_sub_keg_renstra['id_sub_unit'],$v_sub_keg_renstra['tahun_anggaran']
                //         ), ARRAY_A);   // tahun sementara dibuat default karen di tabel renstra masih "0000"

                //         if(!empty($cek_data_renja)){
                //             $status_sub_keg = $wpdb->update('data_sub_keg_bl_lokal', $opsi_sub_keg_renstra, array('id' => $cek_data_renja[0]['id']));
                //         }else{
                //             $status_sub_keg = $wpdb->insert('data_sub_keg_bl_lokal', $opsi_sub_keg_renstra);
                //         }
                //     }
                //     $ret['data'] = $data_sub_keg_renstra;
                // }
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

    public function copy_usulan_renja(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'action'    => 'get_data_sub_giat',
            'message'   => 'Berhasil copy data usulan RENJA!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                // belum selesai
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