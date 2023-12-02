<?php

require_once WPSIPD_PLUGIN_PATH."/public/class-wpsipd-public-base-2.php";
class Wpsipd_Public_Base_1 extends Wpsipd_Public_Base_2{

    public function input_batasan_pagu_per_sumber_dana($atts){
		// untuk disable render shortcode di halaman edit page/post
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/wpsipd-public-input-batasan-pagu-per-sumber-dana.php';
	}

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
                        $wpdb->insert($table, $data);
                        $id_cek_visi = $wpdb->insert_id;
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
                        $wpdb->insert($table, $data);
                        $id_cek_misi = $wpdb->insert_id;
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
                        $wpdb->insert($table, $data);
                        $id_cek_sasaran = $wpdb->insert_id;
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
                        $wpdb->insert($table, $data);
                        $id_cek_kebijakan = $wpdb->insert_id;
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
                        $wpdb->insert($table, $data);
                        $id_cek_isu = $wpdb->insert_id;
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
            'message'   => 'Berhasil copy data RENSTRA ke RENJA!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                if (!empty($_POST['id_jadwal'])) {
                    $where_sub_keg_skpd = (!empty($_POST['id_skpd'])) ? ' AND k.id_sub_unit='.$_POST['id_skpd'] : ''; /** Untuk copy data berdasarkan skpd */

                    $data_jadwal_renja = $wpdb->get_row($wpdb->prepare(
                        'SELECT *
                        FROM data_jadwal_lokal
                        WHERE id_jadwal_lokal=%d',
                        $_POST['id_jadwal']
                    ), ARRAY_A);

                    if(empty($data_jadwal_renja) || $data_jadwal_renja['status'] == 1){
                        $ret = array(
                            'status' => 'error',
                            'message'   => 'Jadwal tidak Ditemukan!',
                        );
                        die(json_encode($ret));
                    }

                    $jadwal_renstra = $wpdb->get_row($wpdb->prepare(
                        'SELECT *
                        FROM data_jadwal_lokal
                        WHERE id_jadwal_lokal =%d',
                        $data_jadwal_renja['relasi_perencanaan']
                    ), ARRAY_A);

                    if(!empty($jadwal_renstra) && $jadwal_renstra['id_tipe'] == 4){
                        $n_tahun = array();
                        for ($tahun=0; $tahun < $jadwal_renstra['lama_pelaksanaan']; $tahun++) { 
                            $tahun_ke = $tahun+1;
                            array_push($n_tahun, $tahun_ke);
                        }
                        $tahun_renstra = $data_jadwal_renja['tahun_anggaran'] - $jadwal_renstra['tahun_anggaran'];                   
                        $tahun_ke = $n_tahun[$tahun_renstra];

                        if(!empty($tahun_ke)){
                            $data_sub_keg_renstra = $wpdb->get_results($wpdb->prepare(
                                'SELECT k.*,p.kode_urusan,p.id_urusan,p.nama_urusan,p.kode_program as kode_program_prog,u.kode_skpd as kode_sub_skpd
                                FROM data_renstra_sub_kegiatan_lokal_history k
                                LEFT JOIN data_prog_keg p
                                ON k.kode_sub_giat=p.kode_sub_giat
                                LEFT JOIN data_unit u
                                ON k.id_sub_unit=u.id_skpd
                                WHERE k.id_jadwal=%d
                                AND k.id_unik IS NOT NULL
                                AND k.active=1 '.$where_sub_keg_skpd,
                                $jadwal_renstra['id_jadwal_lokal']
                            ),ARRAY_A);

                            if(!empty($data_sub_keg_renstra)){
                                foreach ($data_sub_keg_renstra as $v_sub_keg_renstra) {
                                    $kode_bl = $v_sub_keg_renstra['id_unit'].'.'.$v_sub_keg_renstra['id_sub_unit'].'.'.$v_sub_keg_renstra['id_program'].'.'.$v_sub_keg_renstra['id_giat'];
                                    $kode_sbl = $kode_bl.'.'.$v_sub_keg_renstra['id_sub_giat'];

                                    if(empty($v_sub_keg_renstra['id_unik_indikator'])){
                                        $status_indikator_sub_kegiatan = $wpdb->update('data_sub_keg_indikator_lokal', array('active'=>0), array('kode_sbl' => $kode_sbl));

                                        $pisah_sub_unit = explode(" ",$v_sub_keg_renstra['nama_sub_unit']);
                                        array_shift($pisah_sub_unit);
                                        $nama_sub_skpd = implode(" ", $pisah_sub_unit);

                                        $opsi_sub_keg_renstra = array(
                                            'id_sub_skpd' => $v_sub_keg_renstra['id_sub_unit'],
                                            'id_sub_giat' => $v_sub_keg_renstra['id_sub_giat'],
                                            'id_skpd' => $v_sub_keg_renstra['id_unit'],
                                            'kode_bl' => $kode_bl,
                                            'kode_sbl' => $kode_sbl,
                                            'nama_skpd' => $v_sub_keg_renstra['nama_skpd'],
                                            'kode_skpd' => $v_sub_keg_renstra['kode_skpd'],
                                            'nama_sub_skpd' => $nama_sub_skpd,
                                            'kode_sub_skpd' => $v_sub_keg_renstra['kode_sub_skpd'],  
                                            'pagu' => $v_sub_keg_renstra['pagu_'.$tahun_ke],
                                            'pagu_usulan' => $v_sub_keg_renstra['pagu_'.$tahun_ke.'_usulan'],
                                            'pagu_n_depan' => $v_sub_keg_renstra['pagu_'.($tahun_ke+1)],
                                            'pagu_n_depan_usulan' => $v_sub_keg_renstra['pagu_'.($tahun_ke+1).'_usulan'],
                                            'kode_urusan' => $v_sub_keg_renstra['kode_urusan'],
                                            'id_urusan' => $v_sub_keg_renstra['id_urusan'],
                                            'nama_urusan' => $v_sub_keg_renstra['nama_urusan'],
                                            'id_bidang_urusan' => $v_sub_keg_renstra['id_bidang_urusan'],
                                            'kode_bidang_urusan' => $v_sub_keg_renstra['kode_bidang_urusan'],
                                            'nama_bidang_urusan' => $v_sub_keg_renstra['nama_bidang_urusan'],
                                            'id_program' => $v_sub_keg_renstra['id_program'],
                                            'kode_program' => $v_sub_keg_renstra['kode_program_prog'],
                                            'nama_program' => $v_sub_keg_renstra['nama_program'],
                                            'kode_giat' => $v_sub_keg_renstra['kode_giat'],
                                            'nama_giat' => $v_sub_keg_renstra['nama_giat'],
                                            'kode_sub_giat' => $v_sub_keg_renstra['kode_sub_giat'],
                                            'nama_sub_giat' => $v_sub_keg_renstra['nama_sub_giat'],
                                            'catatan' => $v_sub_keg_renstra['catatan'],
                                            'catatan_usulan' => $v_sub_keg_renstra['catatan_usulan'],
                                            'waktu_awal' => 1,
                                            'waktu_akhir' => 12,
                                            'waktu_awal_usulan' => 1,
                                            'waktu_akhir_usulan' => 12,
                                            'active' => 1,
                                            'tahun_anggaran' => $data_jadwal_renja['tahun_anggaran'],
                                            'update_at' => current_time('mysql')
                                        );
                                        
                                        $cek_data_renja = $wpdb->get_results($wpdb->prepare(
                                                'SELECT id
                                                FROM data_sub_keg_bl_lokal
                                                WHERE kode_sub_giat=%s
                                                AND kode_skpd=%s
                                                AND id_sub_skpd=%s
                                                AND tahun_anggaran=%d
                                                AND active=1',
                                                $v_sub_keg_renstra['kode_sub_giat'],$v_sub_keg_renstra['kode_skpd'],$v_sub_keg_renstra['id_sub_unit'],$data_jadwal_renja['tahun_anggaran']
                                        ), ARRAY_A);
        
                                        if(!empty($cek_data_renja)){
                                            $status_sub_keg = $wpdb->update('data_sub_keg_bl_lokal', $opsi_sub_keg_renstra, array('id' => $cek_data_renja[0]['id']));
                                        }else{
                                            $status_sub_keg = $wpdb->insert('data_sub_keg_bl_lokal', $opsi_sub_keg_renstra);
                                        }

                                        /** Insert indikator program renstra */
                                        $get_kode_program = explode(".", $v_sub_keg_renstra['kode_giat']);
                                        $kode_program = $get_kode_program[0].'.'.$get_kode_program[1].'.'.$get_kode_program[2];

                                        $data_indikator_program_renstra = $wpdb->get_results($wpdb->prepare(
                                            'SELECT *
                                            FROM data_renstra_program_lokal_history p
                                            WHERE p.id_jadwal=%d
                                            AND kode_program=%s
                                            AND p.id_unik IS NOT NULL
                                            AND p.id_unik_indikator IS NOT NULL',
                                            $jadwal_renstra['id_jadwal_lokal'],$kode_program
                                        ), ARRAY_A);

                                        if(!empty($data_indikator_program_renstra)){
                                            $status_indikator_program = $wpdb->update('data_capaian_prog_sub_keg_lokal', array('active'=>0), array('kode_sbl' => $kode_sbl));
                                            foreach($data_indikator_program_renstra as $v_prog){
                                                $opsi_indikator_renstra = array(
                                                    'satuancapaian'=> $v_prog['satuan'],
                                                    'targetcapaianteks'=> $v_prog['target_'.$tahun_ke].' '.$v_prog['satuan'],
                                                    'capaianteks'=> $v_prog['indikator'],
                                                    'targetcapaian'=> $v_prog['target_'.$tahun_ke],
                                                    'satuancapaian_usulan'=> $v_prog['satuan_usulan'],
                                                    'targetcapaianteks_usulan'=> $v_prog['target_'.$tahun_ke.'_usulan'].' '.$v_prog['satuan_usulan'],
                                                    'capaianteks_usulan'=> $v_prog['indikator_usulan'],
                                                    'targetcapaian_usulan'=> $v_prog['target_'.$tahun_ke.'_usulan'],
                                                    'catatan'=> $v_prog['catatan'],
                                                    'catatan_usulan'=> $v_prog['catatan_usulan'],
                                                    'kode_sbl'=> $kode_sbl,
                                                    'idsubbl'=> 0,
                                                    'active'=> 1,
                                                    'update_at'=> current_time('mysql'),
                                                    'tahun_anggaran'=> $data_jadwal_renja['tahun_anggaran']
                                                );
                                            
                                                $input_program_renja = $wpdb->insert('data_capaian_prog_sub_keg_lokal', $opsi_indikator_renstra);
                                            }
                                        }
                                        /** Insert indikator kegiatan renstra */
                                        $data_indikator_kegiatan_renstra = $wpdb->get_results($wpdb->prepare(
                                            'SELECT *
                                            FROM data_renstra_kegiatan_lokal_history k
                                            WHERE k.id_jadwal=%d
                                            AND kode_giat=%s
                                            AND k.id_unik IS NOT NULL
                                            AND k.id_unik_indikator IS NOT NULL',
                                            $jadwal_renstra['id_jadwal_lokal'],$v_sub_keg_renstra['kode_giat']
                                        ), ARRAY_A);

                                        if(!empty($data_indikator_kegiatan_renstra)){
                                            $status_indikator_kegiatan = $wpdb->update('data_output_giat_sub_keg_lokal', array('active'=>0), array('kode_sbl' => $kode_sbl));
                                            foreach($data_indikator_kegiatan_renstra as $v_indi){
                                                $opsi_indikator_renstra = array(
                                                    'outputteks'=> $v_indi['indikator'],
                                                    'satuanoutput'=> $v_indi['satuan'],
                                                    'targetoutput'=> $v_indi['target_'.$tahun_ke],
                                                    'targetoutputteks'=> $v_indi['target_'.$tahun_ke].' '.$v_indi['satuan'],
                                                    'outputteks_usulan'=> $v_indi['indikator_usulan'],
                                                    'satuanoutput_usulan'=> $v_indi['satuan_usulan'],
                                                    'targetoutput_usulan'=> $v_indi['target_'.$tahun_ke.'_usulan'],
                                                    'targetoutputteks_usulan'=> $v_indi['target_'.$tahun_ke.'_usulan'].' '.$v_indi['satuan_usulan'],
                                                    'catatan'=> $v_indi['catatan'],
                                                    'catatan_usulan'=> $v_indi['catatan_usulan'],
                                                    'kode_sbl'=> $kode_sbl,
                                                    'idsubbl'=> 0,
                                                    'active'=> 1,
                                                    'update_at'=> current_time('mysql'),
                                                    'tahun_anggaran'=> $data_jadwal_renja['tahun_anggaran']
                                                );
                                            
                                                $input_indi_renja = $wpdb->insert('data_output_giat_sub_keg_lokal', $opsi_indikator_renstra);
                                            }
                                        }
                                    }else{
                                        $opsi_indikator_renstra = array(
                                            'outputteks'=> $v_sub_keg_renstra['indikator'],
                                            'targetoutput'=> $v_sub_keg_renstra['target_'.$tahun_ke],
                                            'satuanoutput'=> $v_sub_keg_renstra['satuan'],
                                            'targetoutputteks'=> $v_sub_keg_renstra['target_'.$tahun_ke].' '.$v_sub_keg_renstra['satuan'],
                                            'outputteks_usulan'=> $v_sub_keg_renstra['indikator_usulan'],
                                            'targetoutput_usulan'=> $v_sub_keg_renstra['target_'.$tahun_ke.'_usulan'],
                                            'satuanoutput_usulan'=> $v_sub_keg_renstra['satuan_usulan'],
                                            'targetoutputteks_usulan'=> $v_sub_keg_renstra['target_'.$tahun_ke.'_usulan'].' '.$v_sub_keg_renstra['satuan_usulan'],
                                            'idoutputbl'=>0,
                                            'kode_sbl'=> $kode_sbl,
                                            'idsubbl'=> 0,
                                            'active'=> 1,
                                            'update_at'=> current_time('mysql'),
                                            'tahun_anggaran'=> $data_jadwal_renja['tahun_anggaran'],
                                            'id_indikator_sub_giat' => $v_sub_keg_renstra['id_sub_giat']
                                        );
                                    
                                        $input_indi_renja = $wpdb->insert('data_sub_keg_indikator_lokal', $opsi_indikator_renstra);
                                    }
                                }
                            }else{
                                $ret = array(
                                    'status' => 'error',
                                    'message'   => 'Data Tahun Ke '.$tahun_ke.' Tidak Ditemukan!'
                                );
                            } 
                        }else{
                            $ret = array(
                                'status' => 'error',
                                'message'   => 'Data Tahun Ke '.$tahun_ke.' Tidak Ditemukan!'
                            );
                        }   
                    }else{
                        $ret = array(
                            'status' => 'error',
                            'message'   => 'Data Relasi Renstra Tidak Ditemukan!'
                        );
                    }
                }else{
                    $ret = array(
                        'status' => 'error',
                        'message'   => 'Ada Data Parameter Yang Kosong!'
                    );
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

    public function copy_usulan_renja(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil copy data usulan RENJA!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                if(!empty($_POST['tahun_anggaran'])){
                    if(in_array('administrator', $this->role())){
                        $tahun_anggaran = $_POST['tahun_anggaran'];
                        if(empty($_POST['id_skpd'])){
                            $sql = $wpdb->prepare("
                                SELECT 
                                    * 
                                FROM data_sub_keg_bl_lokal 
                                WHERE active=1 
                                AND tahun_anggaran=%d
                            ", $tahun_anggaran);
                        }else{
                            $sql = $wpdb->prepare("
                                SELECT 
                                    * 
                                FROM data_sub_keg_bl_lokal 
                                WHERE id_sub_skpd=%d 
                                AND active=1
                                AND tahun_anggaran=%d
                            ", $_POST['id_skpd'], $tahun_anggaran);
                        }
                        $data_sub_keg = $wpdb->get_results($sql, ARRAY_A);
                        $ret['data'] = array();
                        foreach ($data_sub_keg as $keySubKeg => $valueSubKeg) {
                            $newData = array(
                                'pagu' => $valueSubKeg['pagu_usulan'],
                                'pagu_n_depan' => $valueSubKeg['pagu_n_depan_usulan'],
                                'waktu_awal' => $valueSubKeg['waktu_awal_usulan'],
                                'waktu_akhir' => $valueSubKeg['waktu_akhir_usulan'],
                                'catatan' => $valueSubKeg['catatan_usulan'],
                                'sasaran' => $valueSubKeg['sasaran_usulan']
                            );
                            $wpdb->update('data_sub_keg_bl_lokal', $newData, array(
                                'id' => $valueSubKeg['id']
                            ));
                            //copy indikator sub keg
                            $indi_sub_keg = $wpdb->get_results($wpdb->prepare(
                                'SELECT *
                                FROM data_sub_keg_indikator_lokal
                                WHERE kode_sbl=%s
                                AND tahun_anggaran=%d
                                AND active=1',
                                $valueSubKeg['kode_sbl'],$tahun_anggaran
                            ), ARRAY_A);
                            array_push($ret['data'],$tahun_anggaran);
                            if(!empty($indi_sub_keg)){
                                foreach ($indi_sub_keg as $k_sub => $v_sub) {
                                    $newDataSub = array(
                                        'outputteks' => $v_sub['outputteks_usulan'],
                                        'targetoutput' => $v_sub['targetoutput_usulan'],
                                        'satuanoutput' => $v_sub['satuanoutput_usulan'],
                                        'targetoutputteks' => $v_sub['targetoutputteks_usulan']
                                    );
                                    $wpdb->update('data_sub_keg_indikator_lokal', $newDataSub, array(
                                        'id' => $v_sub['id']
                                    ));
                                }
                            }

                            //copy indikator kegiatan
                            $indi_keg = $wpdb->get_results($wpdb->prepare(
                                'SELECT *
                                FROM data_output_giat_sub_keg_lokal
                                WHERE kode_sbl=%s
                                AND tahun_anggaran=%d
                                AND active=1',
                                $valueSubKeg['kode_sbl'],$tahun_anggaran
                            ),ARRAY_A);

                            if(!empty($indi_keg)){
                                foreach ($indi_keg as $k_keg => $v_keg) {
                                    $newDataKeg = array(
                                        'outputteks' => $v_keg['outputteks_usulan'],
                                        'satuanoutput' => $v_keg['satuanoutput_usulan'],
                                        'targetoutput' => $v_keg['targetoutput_usulan'],
                                        'targetoutputteks' => $v_keg['targetoutputteks_usulan'],
                                        'catatan' => $v_keg['catatan_usulan']
                                    );
                                    $wpdb->update('data_output_giat_sub_keg_lokal', $newDataKeg, array(
                                        'id' => $v_keg['id']
                                    ));
                                }
                            }

                            //copy indikator hasil
                            $indi_hasil = $wpdb->get_results($wpdb->prepare(
                                'SELECT *
                                FROM data_keg_indikator_hasil_lokal
                                WHERE kode_sbl=%s
                                AND tahun_anggaran=%d
                                AND active=1',
                                $valueSubKeg['kode_sbl'],$tahun_anggaran
                            ), ARRAY_A);

                            if(!empty($indi_hasil)){
                                foreach ($indi_hasil as $k_hasil => $v_hasil) {
                                    $newDataHasil = array(
                                        'hasilteks' => $v_hasil['hasilteks_usulan'],
                                        'satuanhasil' => $v_hasil['satuanhasil_usulan'],
                                        'targethasil' => $v_hasil['targethasil_usulan'],
                                        'targethasilteks' => $v_hasil['targethasilteks_usulan'],
                                        'catatan' => $v_hasil['catatan_usulan']
                                    );
                                    $wpdb->update('data_keg_indikator_hasil_lokal', $newDataHasil, array(
                                        'id' => $v_hasil['id']
                                    ));
                                }
                            }

                             //copy indikator program
                             $indi_prog = $wpdb->get_results($wpdb->prepare(
                                'SELECT *
                                FROM data_capaian_prog_sub_keg_lokal
                                WHERE kode_sbl=%s
                                AND tahun_anggaran=%d
                                AND active=1',
                                $valueSubKeg['kode_sbl'],$tahun_anggaran
                            ), ARRAY_A);

                            if(!empty($indi_prog)){
                                foreach ($indi_prog as $k_prog => $v_prog) {
                                    $newDataProg = array(
                                        'satuancapaian' => $v_prog['satuancapaian_usulan'],
                                        'targetcapaianteks' => $v_prog['targetcapaianteks_usulan'],
                                        'capaianteks' => $v_prog['capaianteks_usulan'],
                                        'targetcapaian' => $v_prog['targetcapaian_usulan'],
                                        'catatan' => $v_prog['catatan_usulan']
                                    );
                                    $wpdb->update('data_capaian_prog_sub_keg_lokal', $newDataProg, array(
                                        'id' => $v_prog['id']
                                    ));
                                }
                            }
                        }
                    }else{
                        $ret = array(
                            'status' => 'error',
                            'message'   => 'Anda tidak punya kewenangan untuk melakukan ini!'
                        );
                    }
                }else{
                    $ret = array(
                        'status' => 'error',
                        'message'   => 'Tahun Anggaran Kosong!'
                    );
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

    public function copy_penetapan_renja(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil copy data usulan RENJA!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                if(!empty($_POST['tahun_anggaran'])){
                    if(is_user_logged_in()){
                        $tahun_anggaran = $_POST['tahun_anggaran'];
                        if(empty($_POST['id_skpd'])){
                            $sql = $wpdb->prepare("
                                SELECT 
                                    * 
                                FROM data_sub_keg_bl_lokal 
                                WHERE active=1 
                                AND tahun_anggaran=%d
                            ", $tahun_anggaran);
                        }else{
                            $sql = $wpdb->prepare("
                                SELECT 
                                    * 
                                FROM data_sub_keg_bl_lokal 
                                WHERE id_sub_skpd=%d 
                                AND active=1
                                AND tahun_anggaran=%d
                            ", $_POST['id_skpd'], $tahun_anggaran);
                        }
                        $data_sub_keg = $wpdb->get_results($sql, ARRAY_A);
                        $ret['data'] = array();
                        foreach ($data_sub_keg as $keySubKeg => $valueSubKeg) {
                            $newData = array(
                                'pagu_usulan' => $valueSubKeg['pagu'],
                                'pagu_n_depan_usulan' => $valueSubKeg['pagu_n_depan'],
                                'waktu_awal_usulan' => $valueSubKeg['waktu_awal'],
                                'waktu_akhir_usulan' => $valueSubKeg['waktu_akhir'],
                                'catatan_usulan' => $valueSubKeg['catatan'],
                                'sasaran_usulan' => $valueSubKeg['sasaran']
                            );
                            $wpdb->update('data_sub_keg_bl_lokal', $newData, array(
                                'id' => $valueSubKeg['id']
                            ));
                            //copy indikator sub keg
                            $indi_sub_keg = $wpdb->get_results($wpdb->prepare(
                                'SELECT *
                                FROM data_sub_keg_indikator_lokal
                                WHERE kode_sbl=%s
                                AND tahun_anggaran=%d
                                AND active=1',
                                $valueSubKeg['kode_sbl'],$tahun_anggaran
                            ), ARRAY_A);
                            array_push($ret['data'],$tahun_anggaran);
                            if(!empty($indi_sub_keg)){
                                foreach ($indi_sub_keg as $k_sub => $v_sub) {
                                    $newDataSub = array(
                                        'outputteks_usulan' => $v_sub['outputteks'],
                                        'targetoutput_usulan' => $v_sub['targetoutput'],
                                        'satuanoutput_usulan' => $v_sub['satuanoutput'],
                                        'targetoutputteks_usulan' => $v_sub['targetoutputteks']
                                    );
                                    $wpdb->update('data_sub_keg_indikator_lokal', $newDataSub, array(
                                        'id' => $v_sub['id']
                                    ));
                                }
                            }

                            //copy indikator kegiatan
                            $indi_keg = $wpdb->get_results($wpdb->prepare(
                                'SELECT *
                                FROM data_output_giat_sub_keg_lokal
                                WHERE kode_sbl=%s
                                AND tahun_anggaran=%d
                                AND active=1',
                                $valueSubKeg['kode_sbl'],$tahun_anggaran
                            ),ARRAY_A);

                            if(!empty($indi_keg)){
                                foreach ($indi_keg as $k_keg => $v_keg) {
                                    $newDataKeg = array(
                                        'outputteks_usulan' => $v_keg['outputteks'],
                                        'satuanoutput_usulan' => $v_keg['satuanoutput'],
                                        'targetoutput_usulan' => $v_keg['targetoutput'],
                                        'targetoutputteks_usulan' => $v_keg['targetoutputteks'],
                                        'catatan_usulan' => $v_keg['catatan']
                                    );
                                    $wpdb->update('data_output_giat_sub_keg_lokal', $newDataKeg, array(
                                        'id' => $v_keg['id']
                                    ));
                                }
                            }

                            //copy indikator hasil
                            $indi_hasil = $wpdb->get_results($wpdb->prepare(
                                'SELECT *
                                FROM data_keg_indikator_hasil_lokal
                                WHERE kode_sbl=%s
                                AND tahun_anggaran=%d
                                AND active=1',
                                $valueSubKeg['kode_sbl'],$tahun_anggaran
                            ), ARRAY_A);

                            if(!empty($indi_hasil)){
                                foreach ($indi_hasil as $k_hasil => $v_hasil) {
                                    $newDataHasil = array(
                                        'hasilteks_usulan' => $v_hasil['hasilteks'],
                                        'satuanhasil_usulan' => $v_hasil['satuanhasil'],
                                        'targethasil_usulan' => $v_hasil['targethasil'],
                                        'targethasilteks_usulan' => $v_hasil['targethasilteks'],
                                        'catatan_usulan' => $v_hasil['catatan']
                                    );
                                    $wpdb->update('data_keg_indikator_hasil_lokal', $newDataHasil, array(
                                        'id' => $v_hasil['id']
                                    ));
                                }
                            }

                             //copy indikator program
                             $indi_prog = $wpdb->get_results($wpdb->prepare(
                                'SELECT *
                                FROM data_capaian_prog_sub_keg_lokal
                                WHERE kode_sbl=%s
                                AND tahun_anggaran=%d
                                AND active=1',
                                $valueSubKeg['kode_sbl'],$tahun_anggaran
                            ), ARRAY_A);

                            if(!empty($indi_prog)){
                                foreach ($indi_prog as $k_prog => $v_prog) {
                                    $newDataProg = array(
                                        'satuancapaian_usulan' => $v_prog['satuancapaian'],
                                        'targetcapaianteks_usulan' => $v_prog['targetcapaianteks'],
                                        'capaianteks_usulan' => $v_prog['capaianteks'],
                                        'targetcapaian_usulan' => $v_prog['targetcapaian'],
                                        'catatan_usulan' => $v_prog['catatan']
                                    );
                                    $wpdb->update('data_capaian_prog_sub_keg_lokal', $newDataProg, array(
                                        'id' => $v_prog['id']
                                    ));
                                }
                            }
                        }
                    }else{
                        $ret = array(
                            'status' => 'error',
                            'message'   => 'Anda tidak punya kewenangan untuk melakukan ini!'
                        );
                    }
                }else{
                    $ret = array(
                        'status' => 'error',
                        'message'   => 'Tahun Anggaran Kosong!'
                    );
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

    public function show_skpd_program_analisis(){
    	global $wpdb;
    	try{
            $nama_pemda = get_option('_crb_daerah');
    		$kode_program = $_POST['kode_program'];
            $id_jadwal_lokal = $_POST['id_jadwal_lokal'];
            $id_sub_skpd =  $_POST['id_sub_skpd'];
	        $tahun_anggaran = $_POST['tahun_anggaran'];

			$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
				SELECT 
					nama AS nama_jadwal,
					tahun_anggaran,
					status 
				FROM `data_jadwal_lokal` 
					WHERE id_jadwal_lokal=%d", $id_jadwal_lokal));

			$_suffix='';
			$where='';
			if($jadwal_lokal->status == 1){
				$_suffix='_history';
				$where='AND id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
			}

			$where_skpd = '';
			if(!empty($id_sub_skpd)){
				if($id_sub_skpd !='all'){
					$where_skpd = "AND id_sub_skpd=".$wpdb->prepare("%d", $id_sub_skpd);
				}
			}

			$sql = $wpdb->prepare("
                SELECT 
                    nama_program,
                    nama_sub_skpd, 
                    id_sub_skpd,
                    sum(pagu) as pagu_skpd  
                FROM data_sub_keg_bl_lokal".$_suffix."
                WHERE kode_program=%s 
                    AND tahun_anggaran=%d
                    AND active=1
                    ".$where." 
                    ".$where_skpd."
                GROUP by id_sub_skpd 
                ORDER BY id_sub_skpd ASC
			",$kode_program, $tahun_anggaran);

			$skpds = $wpdb->get_results($sql, ARRAY_A);

			$data_all = array(
				'data' => array(),
				'pagu_total' => 0
			);

            $title = !empty($skpds) ? 'Daftar SKPD Program '.$skpds[0]['nama_program'] : '';

			foreach ($skpds as $key => $skpd) {
				if(empty($data_all['data'][$skpd['id_sub_skpd']])){
				    
					$data_all['data'][$skpd['id_sub_skpd']] = [
						'id_sub_skpd' => $skpd['id_sub_skpd'],
						'nama_program' => $skpd['nama_program'],
						'nama_sub_skpd' => $skpd['nama_sub_skpd'],
						'pagu_skpd' => $skpd['pagu_skpd']
					];

					$data_all['pagu_total'] += $skpd['pagu_skpd'];
				}
			}

			/** Tabel skpd program */
			$body = '';
			$no=1;
			foreach ($data_all['data'] as $key => $skpd) {
				$body.='
					<tr data-idsubskpd="'.$skpd['id_sub_skpd'].'">
						<td class="kiri atas kanan bawah text_tengah">'.$no.'</td>
						<td class="atas kanan bawah">'.$skpd['nama_sub_skpd'].'</td>
						<td class="atas kanan bawah text_kanan">'.$this->_number_format($skpd['pagu_skpd']).'</td>
					</tr>';
					$no++;
			}

			$footer='<tr>
						<td class="kiri atas kanan bawah text_tengah" colspan="2"><b>TOTAL PAGU PROGRAM</b></td>
						<td class="atas kanan bawah text_kanan"><b>'.$this->_number_format($data_all['pagu_total']).'</b></td>
					</tr>';

			$html='<div id="preview" style="padding: 5px; overflow: auto; height: 100%;">
					<h4 style="text-align: center; margin: 0; font-weight: bold;">SKPD Per Program
					<br>Tahun '.$jadwal_lokal->tahun_anggaran.' '.$nama_pemda.'
					<br>'.$jadwal_lokal->nama_jadwal.'
					</h4>
					<br>
					<table id="table-skpd-program" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 90%; border: 0; table-layout: fixed;" contenteditable="false">
						<thead>
							<tr>
								<th style="width: 19px;" class="kiri atas kanan bawah text_tengah text_blok">No</th>
								<th class="atas kanan bawah text_tengah text_blok">Nama Sub SKPD</th>
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Program</th>
							</tr>
						</thead>
						<tbody>'.$body.'</tbody>
						<tfoot>'.$footer.'</tfoot>
					</table>';

    		echo json_encode([
				'status' => true,
				'html' => $html,
                'title' => $title
			]);exit();

    	}catch(Exception $e){
    		echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit();
    	}
    }

    public function show_skpd_kegiatan_analisis(){
    	global $wpdb;
    	try{
            $nama_pemda = get_option('_crb_daerah');
    		$kode_giat = $_POST['kode_giat'];
            $id_jadwal_lokal = $_POST['id_jadwal_lokal'];
            $id_sub_skpd =  $_POST['id_sub_skpd'];
	        $tahun_anggaran = $_POST['tahun_anggaran'];

			$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
				SELECT 
					nama AS nama_jadwal,
					tahun_anggaran,
					status 
				FROM `data_jadwal_lokal` 
					WHERE id_jadwal_lokal=%d", $id_jadwal_lokal));

			$_suffix='';
			$where='';
			if($jadwal_lokal->status == 1){
				$_suffix='_history';
				$where='AND id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
			}

			$where_skpd = '';
			if(!empty($id_sub_skpd)){
				if($id_sub_skpd !='all'){
					$where_skpd = "AND id_sub_skpd=".$wpdb->prepare("%d", $id_sub_skpd);
				}
			}

			$sql = $wpdb->prepare("
                SELECT 
                    nama_giat,
                    nama_sub_skpd, 
                    id_sub_skpd,
                    sum(pagu) as pagu_skpd  
                FROM data_sub_keg_bl_lokal".$_suffix."
                WHERE kode_giat=%s 
                    AND tahun_anggaran=%d
                    AND active=1
                    ".$where." 
                    ".$where_skpd."
                GROUP by id_sub_skpd 
                ORDER BY id_sub_skpd ASC
			",$kode_giat, $tahun_anggaran);

			$skpds = $wpdb->get_results($sql, ARRAY_A);

			$data_all = array(
				'data' => array(),
				'pagu_total' => 0
			);

            $title = !empty($skpds) ? 'Daftar SKPD Kegiatan '.$skpds[0]['nama_giat'] : '';

			foreach ($skpds as $key => $skpd) {
				if(empty($data_all['data'][$skpd['id_sub_skpd']])){
				    
					$data_all['data'][$skpd['id_sub_skpd']] = [
						'id_sub_skpd' => $skpd['id_sub_skpd'],
						'nama_giat' => $skpd['nama_giat'],
						'nama_sub_skpd' => $skpd['nama_sub_skpd'],
						'pagu_skpd' => $skpd['pagu_skpd']
					];

					$data_all['pagu_total'] += $skpd['pagu_skpd'];
				}
			}

			/** Tabel skpd kegiatan */
			$body = '';
			$no=1;
			foreach ($data_all['data'] as $key => $skpd) {
				$body.='
					<tr data-idsubskpd="'.$skpd['id_sub_skpd'].'">
						<td class="kiri atas kanan bawah text_tengah">'.$no.'</td>
						<td class="atas kanan bawah">'.$skpd['nama_sub_skpd'].'</td>
						<td class="atas kanan bawah text_kanan">'.$this->_number_format($skpd['pagu_skpd']).'</td>
					</tr>';
					$no++;
			}

			$footer='<tr>
						<td class="kiri atas kanan bawah text_tengah" colspan="2"><b>TOTAL PAGU KEGIATAN</b></td>
						<td class="atas kanan bawah text_kanan"><b>'.$this->_number_format($data_all['pagu_total']).'</b></td>
					</tr>';

			$html='<div id="preview" style="padding: 5px; overflow: auto; height: 100%;">
					<h4 style="text-align: center; margin: 0; font-weight: bold;">SKPD Per Program
					<br>Tahun '.$jadwal_lokal->tahun_anggaran.' '.$nama_pemda.'
					<br>'.$jadwal_lokal->nama_jadwal.'
					</h4>
					<br>
					<table id="table-skpd-giat" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 90%; border: 0; table-layout: fixed;" contenteditable="false">
						<thead>
							<tr>
								<th style="width: 19px;" class="kiri atas kanan bawah text_tengah text_blok">No</th>
								<th class="atas kanan bawah text_tengah text_blok">Nama Sub SKPD</th>
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Kegiatan</th>
							</tr>
						</thead>
						<tbody>'.$body.'</tbody>
						<tfoot>'.$footer.'</tfoot>
					</table>';

    		echo json_encode([
				'status' => true,
				'html' => $html,
                'title' => $title
			]);exit();

    	}catch(Exception $e){
    		echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit();
    	}
    }

    public function show_skpd_sub_giat_analisis(){
    	global $wpdb;
    	try{
            $nama_pemda = get_option('_crb_daerah');
    		$kode_sub_giat = $_POST['kode_sub_giat'];
            $id_jadwal_lokal = $_POST['id_jadwal_lokal'];
            $id_sub_skpd =  $_POST['id_sub_skpd'];
	        $tahun_anggaran = $_POST['tahun_anggaran'];

			$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
				SELECT 
					nama AS nama_jadwal,
					tahun_anggaran,
					status 
				FROM `data_jadwal_lokal` 
					WHERE id_jadwal_lokal=%d", $id_jadwal_lokal));

			$_suffix='';
			$where='';
			if($jadwal_lokal->status == 1){
				$_suffix='_history';
				$where='AND id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
			}

			$where_skpd = '';
			if(!empty($id_sub_skpd)){
				if($id_sub_skpd !='all'){
					$where_skpd = "AND id_sub_skpd=".$wpdb->prepare("%d", $id_sub_skpd);
				}
			}

			$sql = $wpdb->prepare("
                SELECT 
                    nama_sub_giat,
                    nama_sub_skpd, 
                    id_sub_skpd,
                    sum(pagu) as pagu_skpd  
                FROM data_sub_keg_bl_lokal".$_suffix."
                WHERE kode_sub_giat=%s 
                    AND tahun_anggaran=%d
                    AND active=1
                    ".$where." 
                    ".$where_skpd."
                GROUP by id_sub_skpd 
                ORDER BY id_sub_skpd ASC
			",$kode_sub_giat, $tahun_anggaran);

			$skpds = $wpdb->get_results($sql, ARRAY_A);

			$data_all = array(
				'data' => array(),
				'pagu_total' => 0
			);

            $title = !empty($skpds) ? 'Daftar SKPD Sub Kegiatan '.$skpds[0]['nama_sub_giat'] : '';

			foreach ($skpds as $key => $skpd) {
				if(empty($data_all['data'][$skpd['id_sub_skpd']])){
				    
					$data_all['data'][$skpd['id_sub_skpd']] = [
						'id_sub_skpd' => $skpd['id_sub_skpd'],
						'nama_giat' => $skpd['nama_sub_giat'],
						'nama_sub_skpd' => $skpd['nama_sub_skpd'],
						'pagu_skpd' => $skpd['pagu_skpd']
					];

					$data_all['pagu_total'] += $skpd['pagu_skpd'];
				}
			}

			/** Tabel skpd sub giat */
			$body = '';
			$no=1;
			foreach ($data_all['data'] as $key => $skpd) {
				$body.='
					<tr data-idsubskpd="'.$skpd['id_sub_skpd'].'">
						<td class="kiri atas kanan bawah text_tengah">'.$no.'</td>
						<td class="atas kanan bawah">'.$skpd['nama_sub_skpd'].'</td>
						<td class="atas kanan bawah text_kanan">'.$this->_number_format($skpd['pagu_skpd']).'</td>
					</tr>';
					$no++;
			}
			
			$footer='<tr>
						<td class="kiri atas kanan bawah text_tengah" colspan="2"><b>TOTAL PAGU SUB KEGIATAN</b></td>
						<td class="atas kanan bawah text_kanan"><b>'.$this->_number_format($data_all['pagu_total']).'</b></td>
					</tr>';

			$html='<div id="preview" style="padding: 5px; overflow: auto; height: 100%;">
					<h4 style="text-align: center; margin: 0; font-weight: bold;">SKPD Per Program
					<br>Tahun '.$jadwal_lokal->tahun_anggaran.' '.$nama_pemda.'
					<br>'.$jadwal_lokal->nama_jadwal.'
					</h4>
					<br>
					<table id="table-skpd-sub-giat" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 90%; border: 0; table-layout: fixed;" contenteditable="false">
						<thead>
							<tr>
								<th style="width: 19px;" class="kiri atas kanan bawah text_tengah text_blok">No</th>
								<th class="atas kanan bawah text_tengah text_blok">Nama Sub SKPD</th>
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Sub Kegiatan</th>
							</tr>
						</thead>
						<tbody>'.$body.'</tbody>
						<tfoot>'.$footer.'</tfoot>
					</table>';

    		echo json_encode([
				'status' => true,
				'html' => $html,
                'title' => $title
			]);exit();

    	}catch(Exception $e){
    		echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit();
    	}
    }

    public function show_skpd_bidang_urusan_analisis(){
    	global $wpdb;
    	try{
            $nama_pemda = get_option('_crb_daerah');
    		$kode_bidang_urusan = $_POST['kode_bidang_urusan'];
            $id_jadwal_lokal = $_POST['id_jadwal_lokal'];
            $id_sub_skpd =  $_POST['id_sub_skpd'];
	        $tahun_anggaran = $_POST['tahun_anggaran'];

			$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
				SELECT 
					nama AS nama_jadwal,
					tahun_anggaran,
					status 
				FROM `data_jadwal_lokal` 
					WHERE id_jadwal_lokal=%d", $id_jadwal_lokal));

			$_suffix='';
			$where='';
			if($jadwal_lokal->status == 1){
				$_suffix='_history';
				$where='AND id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
			}

			$where_skpd = '';
			if(!empty($id_sub_skpd)){
				if($id_sub_skpd !='all'){
					$where_skpd = "AND id_sub_skpd=".$wpdb->prepare("%d", $id_sub_skpd);
				}
			}

			$sql = $wpdb->prepare("
                SELECT 
                    nama_bidang_urusan,
                    nama_sub_skpd, 
                    id_sub_skpd,
                    sum(pagu) as pagu_skpd  
                FROM data_sub_keg_bl_lokal".$_suffix."
                WHERE kode_bidang_urusan=%s 
                    AND tahun_anggaran=%d
                    AND active=1
                    ".$where." 
                    ".$where_skpd."
                GROUP by id_sub_skpd 
                ORDER BY id_sub_skpd ASC
			",$kode_bidang_urusan, $tahun_anggaran);

			$skpds = $wpdb->get_results($sql, ARRAY_A);

			$data_all = array(
				'data' => array(),
				'pagu_total' => 0
			);

            $title = !empty($skpds) ? 'Daftar SKPD Bidang Urusan '.$skpds[0]['nama_bidang_urusan'] : '';

			foreach ($skpds as $key => $skpd) {
				if(empty($data_all['data'][$skpd['id_sub_skpd']])){
				    
					$data_all['data'][$skpd['id_sub_skpd']] = [
						'id_sub_skpd' => $skpd['id_sub_skpd'],
						'nama_bidang_urusan' => $skpd['nama_bidang_urusan'],
						'nama_sub_skpd' => $skpd['nama_sub_skpd'],
						'pagu_skpd' => $skpd['pagu_skpd']
					];

					$data_all['pagu_total'] += $skpd['pagu_skpd'];
				}
			}

			/** Tabel skpd bidang urusan */
			$body = '';
			$no=1;
			foreach ($data_all['data'] as $key => $skpd) {
				$body.='
					<tr data-idsubskpd="'.$skpd['id_sub_skpd'].'">
						<td class="kiri atas kanan bawah text_tengah">'.$no.'</td>
						<td class="atas kanan bawah">'.$skpd['nama_sub_skpd'].'</td>
						<td class="atas kanan bawah text_kanan">'.$this->_number_format($skpd['pagu_skpd']).'</td>
					</tr>';
					$no++;
			}
			
			$footer='<tr>
						<td class="kiri atas kanan bawah text_tengah" colspan="2"><b>TOTAL PAGU BIDANG URUSAN</b></td>
						<td class="atas kanan bawah text_kanan"><b>'.$this->_number_format($data_all['pagu_total']).'</b></td>
					</tr>';

			$html='<div id="preview" style="padding: 5px; overflow: auto; height: 100%;">
					<h4 style="text-align: center; margin: 0; font-weight: bold;">SKPD Per Program
					<br>Tahun '.$jadwal_lokal->tahun_anggaran.' '.$nama_pemda.'
					<br>'.$jadwal_lokal->nama_jadwal.'
					</h4>
					<br>
					<table id="table-skpd-bidang-urusan" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 90%; border: 0; table-layout: fixed;" contenteditable="false">
						<thead>
							<tr>
								<th style="width: 19px;" class="kiri atas kanan bawah text_tengah text_blok">No</th>
								<th class="atas kanan bawah text_tengah text_blok">Nama Sub SKPD</th>
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Bidang Urusan</th>
							</tr>
						</thead>
						<tbody>'.$body.'</tbody>
						<tfoot>'.$footer.'</tfoot>
					</table>';

    		echo json_encode([
				'status' => true,
				'html' => $html,
                'title' => $title
			]);exit();

    	}catch(Exception $e){
    		echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit();
    	}
    }

    public function show_skpd_sumber_dana_analisis(){
    	global $wpdb;
    	try{
            $nama_pemda = get_option('_crb_daerah');
    		$kode_dana = $_POST['kode_dana'];
            $id_jadwal_lokal = $_POST['id_jadwal_lokal'];
            $id_sub_skpd =  $_POST['id_sub_skpd'];
	        $tahun_anggaran = $_POST['tahun_anggaran'];
            $sub_keg = $_POST['sub_keg'];

			$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
				SELECT 
					nama AS nama_jadwal,
					tahun_anggaran,
					status 
				FROM `data_jadwal_lokal` 
					WHERE id_jadwal_lokal=%d", $id_jadwal_lokal));

			$_suffix='';
			$where_jadwal='';
            $where_jadwal_dana='';
            $where_jadwal_dana_single='';
			if($jadwal_lokal->status == 1){
				$_suffix='_history';
                $where_jadwal.=' AND sub_keg.id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
                $where_jadwal_dana=' AND dana.id_jadwal=sub_keg.id_jadwal';
                $where_jadwal_dana_single=' AND id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
			}

			$where_skpd = '';
			if(!empty($id_sub_skpd)){
				if($id_sub_skpd !='all'){
					$where_skpd = " AND sub_keg.id_sub_skpd=".$wpdb->prepare("%d", $id_sub_skpd);
				}
			}

            if($kode_dana == '...'){
                $sql = "
                    SELECT 
                        dana.id, 
                        dana.namadana, 
                        dana.kodedana, 
                        dana.pagudana, 
                        dana.kode_sbl as kode_sbl_dana, 
                        sub_keg.kode_sbl, 
                        sub_keg.nama_sub_giat, 
                        sub_keg.nama_sub_skpd, 
                        sub_keg.pagu, 
                        sub_keg.id_sub_skpd 
                    FROM data_sub_keg_bl_lokal".$_suffix." AS sub_keg 
                    LEFT JOIN data_dana_sub_keg_lokal".$_suffix." AS dana 
                        ON dana.kode_sbl = sub_keg.kode_sbl 
                        AND dana.tahun_anggaran=sub_keg.tahun_anggaran 
                        AND dana.active=sub_keg.active
                        ".$where_jadwal_dana."
                    WHERE sub_keg.tahun_anggaran=%d 
                        AND sub_keg.active=1
                        AND dana.kode_sbl IS NULL
                        ".$where_jadwal."
                        ".$where_skpd."
                    ORDER BY dana.kodedana ASC";
                 $sql = $wpdb->prepare($sql, $tahun_anggaran);
            }else if($kode_dana == '-'){
                $sql = "
                    SELECT 
                        dana.id, 
                        dana.namadana, 
                        dana.kodedana, 
                        dana.pagudana, 
                        dana.kode_sbl as kode_sbl_dana, 
                        sub_keg.kode_sbl, 
                        sub_keg.nama_sub_giat, 
                        sub_keg.nama_sub_skpd, 
                        sub_keg.pagu, 
                        sub_keg.id_sub_skpd 
                    FROM data_sub_keg_bl_lokal".$_suffix." AS sub_keg 
                    LEFT JOIN data_dana_sub_keg_lokal".$_suffix." AS dana 
                        ON dana.kode_sbl = sub_keg.kode_sbl 
                        AND dana.tahun_anggaran=sub_keg.tahun_anggaran 
                        AND dana.active=sub_keg.active
                        ".$where_jadwal_dana."
                    WHERE sub_keg.tahun_anggaran=%d 
                        AND sub_keg.active=1
                        AND dana.kodedana IS NULL
                        ".$where_jadwal."
                        ".$where_skpd."
                    ORDER BY dana.kodedana ASC";
                 $sql = $wpdb->prepare($sql, $tahun_anggaran);
            }else{
                $sql = "
                    SELECT 
                        dana.id, 
                        dana.namadana, 
                        dana.kodedana, 
                        dana.pagudana, 
                        dana.kode_sbl as kode_sbl_dana, 
                        sub_keg.kode_sbl, 
                        sub_keg.nama_sub_giat, 
                        sub_keg.kode_sub_skpd, 
                        sub_keg.nama_sub_skpd, 
                        sub_keg.pagu, 
                        sub_keg.id_sub_skpd 
                    FROM data_sub_keg_bl_lokal".$_suffix." AS sub_keg 
                    INNER JOIN data_dana_sub_keg_lokal".$_suffix." AS dana 
                        ON dana.kode_sbl = sub_keg.kode_sbl 
                        AND dana.tahun_anggaran=sub_keg.tahun_anggaran 
                        AND dana.active=sub_keg.active
                        AND dana.kodedana=%s
                        ".$where_jadwal_dana."
                    WHERE sub_keg.tahun_anggaran=%d 
                        AND sub_keg.active=1
                        ".$where_jadwal."
                        ".$where_skpd."
                    ORDER BY dana.kodedana ASC";
			     $sql = $wpdb->prepare($sql, $kode_dana, $tahun_anggaran);
            }
			$analisis_sumber_dana = $wpdb->get_results($sql, ARRAY_A);
            $sql_dana = $wpdb->last_query;

            $nama_dana = "Nama Sumber Dana tidak ditemukan!";
            if($kode_dana == '...'){
                $nama_dana = "Sumber Dana belum diset!";
            }else if($kode_dana == '-'){
                $nama_dana = "Sumber Dana belum ditetapkan!";
            }else{
                if(!empty($analisis_sumber_dana)){
                    $nama_dana = $analisis_sumber_dana[0]['namadana'];
                }
            }
            $title = 'Daftar SKPD yang menggunakan Sumber Dana ( '.$nama_dana.' )';

            $data_all = array(
                'total' => 0,
                'total_sub_keg' => 0,
                'total_sub_keg_sumber_dana' => 0,
                'data'  => array()
            );
            $cek_sub_keg = array();
            $double_sub_keg = array();
            foreach($analisis_sumber_dana as $k => $ap){
                $key = $ap['id_sub_skpd'];
                if(!empty($sub_keg)){
                    $key = $ap['kode_sbl'];
                }
                if(empty($ap['kode_sbl_dana'])){
                    $ap['kodedana'] = '...';
                    $ap['namadana'] = 'Sumber Dana belum diset!';
                }else if(empty($ap['kodedana'])){
                    $ap['kodedana'] = '-';
                    $ap['namadana'] = 'Sumber Dana belum ditetapkan!';
                }
                
                if(empty($data_all['data'][$key])){
                    $data_all['data'][$key] = $ap;
                    $data_all['data'][$key]['skpd_id'] = array();
                    $data_all['data'][$key]['sub_keg_id'] = array();
                    $data_all['data'][$key]['sub_keg'] = 0;
                    $data_all['data'][$key]['skpd'] = 0;
                    $data_all['data'][$key]['total_pagu'] = 0;
                    $data_all['data'][$key]['total_pagu_sub_keg'] = 0;
                    $data_all['data'][$key]['pagudana_all'] = 0;
                }

                if(empty($cek_sub_keg[$ap['kode_sbl']])){
                    $cek_sub_keg[$ap['kode_sbl']] = $ap;
                    $data_all['total_sub_keg'] += $ap['pagu'];
                    $data_all['data'][$key]['total_pagu_sub_keg'] += $ap['pagu'];
                }else{
                    $double_sub_keg[] = $ap;
                }

                if(empty($data_all['data'][$key]['skpd_id'][$ap['id_sub_skpd']])){
                    $data_all['data'][$key]['skpd_id'][$ap['id_sub_skpd']] = $ap['id_sub_skpd'];
                    $data_all['data'][$key]['skpd']++;
                }
                if(empty($data_all['data'][$key]['sub_keg_id'][$ap['kode_sbl']])){
                    $data_all['data'][$key]['sub_keg_id'][$ap['kode_sbl']] = $ap['kode_sbl'];
                    $data_all['data'][$key]['sub_keg']++;
                }

                $pagudana_all = $wpdb->get_results($wpdb->prepare("
                    SELECT
                        pagudana
                    FROM data_dana_sub_keg_lokal".$_suffix."
                    WHERE kode_sbl=%s
                        AND tahun_anggaran=%d
                        AND active=1
                        ".$where_jadwal_dana_single."
                ", $ap['kode_sbl'], $tahun_anggaran), ARRAY_A);
                if(empty($pagudana_all)){
                    $data_all['data'][$key]['pagudana_all'] += $ap['pagu'];
                    $data_all['total_sub_keg_sumber_dana'] += $ap['pagu'];
                }else{
                    foreach($pagudana_all as $kk => $vv){
                        $data_all['data'][$key]['pagudana_all'] += $vv['pagudana'];
                        $data_all['total_sub_keg_sumber_dana'] += $vv['pagudana'];
                    }
                }

                // jika sumber dana belum diset, maka total pagu diambil dari pagu sub kegiatan
                if(empty($ap['kode_sbl_dana'])){
                    $data_all['data'][$key]['total_pagu'] += $ap['pagu'];
                    $data_all['total'] += $ap['pagu'];
                }else{
                    $data_all['data'][$key]['total_pagu'] += $ap['pagudana'];
                    $data_all['total'] += $ap['pagudana'];
                }
            }

			/** Tabel skpd sumber dana */
			$body = '';
			$no=1;
			foreach ($data_all['data'] as $key => $skpd) {
                $warning = '';
                if($skpd['pagudana_all'] != $skpd['total_pagu_sub_keg']){
                    $warning = 'background: #f9d9d9;';
                }
                $url_skpd = $this->generatePage('Input RENJA '.$skpd['nama_sub_skpd'].' '.$skpd['kode_sub_skpd'].' | '.$tahun_anggaran, $tahun_anggaran, '[input_renja tahun_anggaran="'.$tahun_anggaran.'" id_skpd="'.$skpd['id_sub_skpd'].'"]');
                $nama_skpd = '<a href="'.$url_skpd.'" target="_blank">'.$skpd['kode_sub_skpd'].' '.$skpd['nama_sub_skpd'].'</a>';
                if(!empty($sub_keg)){
                    $td_sub_keg = '
                        <td class="atas kanan bawah">'.$skpd['nama_sub_giat'].'</td>
                    ';
                }
				$body.='
					<tr data-idsubskpd="'.$skpd['id_sub_skpd'].'">
						<td class="kiri atas kanan bawah text_tengah">'.$no.'</td>
						<td class="atas kanan bawah">'.$nama_skpd.'</td>
                        '.$td_sub_keg.'
						<td style="'.$warning.'" class="atas kanan bawah text_kanan">'.$this->_number_format($skpd['total_pagu']).'</td>
                        <td style="'.$warning.'" class="atas kanan bawah text_kanan">'.$this->_number_format($skpd['total_pagu_sub_keg']).'</td>
                        <td style="'.$warning.'" class="atas kanan bawah text_kanan">'.$this->_number_format($skpd['pagudana_all']).'</td>
					</tr>';
					$no++;
			}

            $colspan = 2;
            $judul = 'Sumber Dana per SKPD';
            $th_sub_giat = '';
            if(!empty($sub_keg)){
                $colspan = 3;
                $th_sub_giat = '<th class="atas kanan bawah text_tengah text_blok" style="width: 200px;">Sub Kegiatan</th>';
                $judul = 'Sumber Dana per Sub Kegiatan';
            }

            $warning = '';
            if($data_all['total_sub_keg_sumber_dana'] != $data_all['total_sub_keg']){
                $warning = 'background: #f9d9d9;';
            }
			$footer='
                <tr>
					<td class="kiri atas kanan bawah text_kiri" colspan="'.$colspan.'"><b>TOTAL</b></td>
					<td style="'.$warning.'" class="atas kanan bawah text_kanan"><b>'.$this->_number_format($data_all['total']).'</b></td>
                    <td style="'.$warning.'" class="atas kanan bawah text_kanan"><b>'.$this->_number_format($data_all['total_sub_keg']).'</b></td>
                    <td style="'.$warning.'" class="atas kanan bawah text_kanan"><b>'.$this->_number_format($data_all['total_sub_keg_sumber_dana']).'</b></td>
				</tr>';

			$html='<div id="preview" style="padding: 5px; overflow: auto; height: 100%;">
					<h4 style="text-align: center; margin: 0; font-weight: bold;">'.$judul.'
					<br>Tahun '.$jadwal_lokal->tahun_anggaran.' '.$nama_pemda.'
					<br>'.$jadwal_lokal->nama_jadwal.'
					</h4>
					<br>
					<table id="table-skpd-sumber-dana" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 90%; border: 0; table-layout: fixed;" contenteditable="false">
						<thead>
							<tr>
								<th style="width: 19px;" class="kiri atas kanan bawah text_tengah text_blok">No</th>
								<th class="atas kanan bawah text_tengah text_blok">Nama Sub SKPD</th>
                                '.$th_sub_giat.'
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Sumber Dana</th>
                                <th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Sub Kegiatan</th>
                                <th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Sumber Dana Dalam Satu Sub Kegiatan</th>
							</tr>
						</thead>
						<tbody>'.$body.'</tbody>
						<tfoot>'.$footer.'</tfoot>
					</table>';

    		echo json_encode([
				'status' => true,
				'html' => $html,
                'title' => $title,
                'query' => $sql_dana
			]);exit();

    	}catch(Exception $e){
    		echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit();
    	}
    }

    public function show_skpd_rekening_analisis(){
    	global $wpdb;
    	try{
            $nama_pemda = get_option('_crb_daerah');
    		$kode_akun = $_POST['kode_akun'];
            $id_jadwal_lokal = $_POST['id_jadwal_lokal'];
            $id_sub_skpd =  $_POST['id_sub_skpd'];
	        $tahun_anggaran = $_POST['tahun_anggaran'];
            $sub_keg = $_POST['sub_keg'];

			$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
				SELECT 
					j.nama AS nama_jadwal,
					j.tahun_anggaran,
					j.status ,
                    t.nama_tipe
                FROM `data_jadwal_lokal` j
                INNER JOIN `data_tipe_perencanaan` t on t.id=j.id_tipe 
                WHERE j.id_jadwal_lokal=%d", $id_jadwal_lokal));

			$_suffix='';
			$where_jadwal='';
            $where_jadwal_rekening='';
            $where_jadwal_rekening_single='';
			if($jadwal_lokal->status == 1){
				$_suffix='_history';
                $where_jadwal.=' AND sub_keg.id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
                $where_jadwal_rekening=' AND rekening.id_jadwal=sub_keg.id_jadwal';
                $where_jadwal_rekening_single=' AND id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
			}

            $_suffix_sipd='';
            if(strpos($jadwal_lokal->nama_tipe, '_sipd') == false){
                $_suffix_sipd = '_lokal';
            }

			$where_skpd = '';
			if(!empty($id_sub_skpd)){
				if($id_sub_skpd !='all'){
					$where_skpd = " AND sub_keg.id_sub_skpd=".$wpdb->prepare("%d", $id_sub_skpd);
				}
			}

            if($kode_akun == '...'){
                $sql = "
                    SELECT 
                        rekening.id, 
                        rekening.nama_akun, 
                        rekening.kode_akun, 
                        rekening.total_harga, 
                        rekening.kode_sbl as kode_sbl_rekening, 
                        sub_keg.kode_sbl, 
                        sub_keg.nama_sub_giat, 
                        sub_keg.nama_sub_skpd, 
                        sub_keg.pagu, 
                        sub_keg.id_sub_skpd 
                    FROM data_sub_keg_bl".$_suffix_sipd."".$_suffix." AS sub_keg 
                    LEFT JOIN data_rka".$_suffix_sipd."".$_suffix." AS rekening 
                        ON rekening.kode_sbl = sub_keg.kode_sbl 
                        AND rekening.tahun_anggaran=sub_keg.tahun_anggaran 
                        AND rekening.active=sub_keg.active
                        ".$where_jadwal_rekening."
                    WHERE sub_keg.tahun_anggaran=%d 
                        AND sub_keg.active=1
                        AND rekening.kode_sbl IS NULL
                        ".$where_jadwal."
                        ".$where_skpd."
                    ORDER BY rekening.kode_akun ASC";
                 $sql = $wpdb->prepare($sql, $tahun_anggaran);
            }else if($kode_akun == '-'){
                $sql = "
                    SELECT 
                        rekening.id, 
                        rekening.nama_akun, 
                        rekening.kode_akun, 
                        rekening.total_harga, 
                        rekening.kode_sbl as kode_sbl_rekening, 
                        sub_keg.kode_sbl, 
                        sub_keg.nama_sub_giat, 
                        sub_keg.nama_sub_skpd, 
                        sub_keg.pagu, 
                        sub_keg.id_sub_skpd 
                    FROM data_sub_keg_bl".$_suffix_sipd."".$_suffix." AS sub_keg 
                    LEFT JOIN data_rka".$_suffix_sipd."".$_suffix." AS rekening 
                        ON rekening.kode_sbl = sub_keg.kode_sbl 
                        AND rekening.tahun_anggaran=sub_keg.tahun_anggaran 
                        AND rekening.active=sub_keg.active
                        ".$where_jadwal_rekening."
                    WHERE sub_keg.tahun_anggaran=%d 
                        AND sub_keg.active=1
                        AND rekening.kode_akun IS NULL
                        ".$where_jadwal."
                        ".$where_skpd."
                    ORDER BY rekening.kode_akun ASC";
                 $sql = $wpdb->prepare($sql, $tahun_anggaran);
            }else{
                $sql = "
                    SELECT 
                    rekening.id, 
                    rekening.nama_akun, 
                    rekening.kode_akun, 
                    rekening.total_harga, 
                    rekening.kode_sbl as kode_sbl_rekening, 
                        sub_keg.kode_sbl, 
                        sub_keg.nama_sub_giat, 
                        sub_keg.kode_sub_skpd, 
                        sub_keg.nama_sub_skpd, 
                        sub_keg.pagu, 
                        sub_keg.id_sub_skpd 
                    FROM data_sub_keg_bl".$_suffix_sipd."".$_suffix." AS sub_keg 
                    INNER JOIN data_rka".$_suffix_sipd."".$_suffix." AS rekening 
                        ON rekening.kode_sbl = sub_keg.kode_sbl 
                        AND rekening.tahun_anggaran=sub_keg.tahun_anggaran 
                        AND rekening.active=sub_keg.active
                        AND rekening.kode_akun=%s
                        ".$where_jadwal_rekening."
                    WHERE sub_keg.tahun_anggaran=%d 
                        AND sub_keg.active=1
                        ".$where_jadwal."
                        ".$where_skpd."
                    ORDER BY rekening.kode_akun ASC";
			     $sql = $wpdb->prepare($sql, $kode_akun, $tahun_anggaran);
            }
			$analisis_rekening = $wpdb->get_results($sql, ARRAY_A);
            $sql_rekening = $wpdb->last_query;

            $nama_akun = "Nama Rekening tidak ditemukan!";
            if($kode_akun == '...'){
                $nama_akun = "Rekening belum diset!";
            }else if($kode_akun == '-'){
                $nama_akun = "Rekening belum ditetapkan!";
            }else{
                if(!empty($analisis_rekening)){
                    $nama_akun = $analisis_rekening[0]['nama_akun'];
                }
            }
            $title = 'Daftar SKPD yang menggunakan Rekening ( '.$nama_akun.' )';

            $data_all = array(
                'total' => 0,
                'total_sub_keg' => 0,
                'total_sub_keg_rekening' => 0,
                'data'  => array()
            );
            $cek_sub_keg = array();
            $double_sub_keg = array();
            foreach($analisis_rekening as $k => $ap){
                $key = $ap['id_sub_skpd'];
                if(!empty($sub_keg)){
                    $key = $ap['kode_sbl'];
                }
                if(empty($ap['kode_sbl_rekening'])){
                    $ap['kode_akun'] = '...';
                    $ap['nama_akun'] = 'Rekening belum diset!';
                }else if(empty($ap['kode_akun'])){
                    $ap['kode_akun'] = '-';
                    $ap['nama_akun'] = 'Rekening belum ditetapkan!';
                }
                
                if(empty($data_all['data'][$key])){
                    $data_all['data'][$key] = $ap;
                    $data_all['data'][$key]['skpd_id'] = array();
                    $data_all['data'][$key]['sub_keg_id'] = array();
                    $data_all['data'][$key]['sub_keg'] = 0;
                    $data_all['data'][$key]['skpd'] = 0;
                    $data_all['data'][$key]['total_pagu'] = 0;
                    $data_all['data'][$key]['total_pagu_sub_keg'] = 0;
                    $data_all['data'][$key]['pagurekening_all'] = 0;
                }

                if(empty($cek_sub_keg[$ap['kode_sbl']])){
                    $cek_sub_keg[$ap['kode_sbl']] = $ap;
                    $data_all['total_sub_keg'] += $ap['pagu'];
                    $data_all['data'][$key]['total_pagu_sub_keg'] += $ap['pagu'];
                }else{
                    $double_sub_keg[] = $ap;
                }

                if(empty($data_all['data'][$key]['skpd_id'][$ap['id_sub_skpd']])){
                    $data_all['data'][$key]['skpd_id'][$ap['id_sub_skpd']] = $ap['id_sub_skpd'];
                    $data_all['data'][$key]['skpd']++;
                }
                if(empty($data_all['data'][$key]['sub_keg_id'][$ap['kode_sbl']])){
                    $data_all['data'][$key]['sub_keg_id'][$ap['kode_sbl']] = $ap['kode_sbl'];
                    $data_all['data'][$key]['sub_keg']++;
                }

                $pagurekening_all = $wpdb->get_results($wpdb->prepare("
                    SELECT
                        total_harga
                    FROM data_rka".$_suffix_sipd."".$_suffix."
                    WHERE kode_sbl=%s
                        AND tahun_anggaran=%d
                        AND active=1
                        ".$where_jadwal_rekening_single."
                ", $ap['kode_sbl'], $tahun_anggaran), ARRAY_A);
                if(empty($pagurekening_all)){
                    $data_all['data'][$key]['pagurekening_all'] += $ap['pagu'];
                    $data_all['total_sub_keg_rekening'] += $ap['pagu'];
                }else{
                    foreach($pagurekening_all as $kk => $vv){
                        $data_all['data'][$key]['pagurekening_all'] += $vv['total_harga'];
                        $data_all['total_sub_keg_rekening'] += $vv['total_harga'];
                    }
                }

                // jika rekening belum diset, maka total pagu diambil dari pagu sub kegiatan
                if(empty($ap['kode_sbl_rekening'])){
                    $data_all['data'][$key]['total_pagu'] += $ap['pagu'];
                    $data_all['total'] += $ap['pagu'];
                }else{
                    $data_all['data'][$key]['total_pagu'] += $ap['total_harga'];
                    $data_all['total'] += $ap['total_harga'];
                }
            }

			/** Tabel skpd rekening */
			$body = '';
			$no=1;
			foreach ($data_all['data'] as $key => $skpd) {
                $warning = '';
                if($skpd['pagurekening_all'] != $skpd['total_pagu_sub_keg']){
                    $warning = 'background: #f9d9d9;';
                }
                $url_skpd = $this->generatePage('Input RENJA '.$skpd['nama_sub_skpd'].' '.$skpd['kode_sub_skpd'].' | '.$tahun_anggaran, $tahun_anggaran, '[input_renja tahun_anggaran="'.$tahun_anggaran.'" id_skpd="'.$skpd['id_sub_skpd'].'"]');
                /** Cek apakah data lokal atau sipd */
                $nama_skpd = ($_suffix_sipd == '_lokal') ? '<a href="'.$url_skpd.'" target="_blank">'.$skpd['kode_sub_skpd'].' '.$skpd['nama_sub_skpd'].'</a>' : $skpd['nama_sub_skpd'];
                if(!empty($sub_keg)){
                    $td_sub_keg = '
                        <td class="atas kanan bawah">'.$skpd['nama_sub_giat'].'</td>
                    ';
                }
				$body.='
					<tr data-idsubskpd="'.$skpd['id_sub_skpd'].'">
						<td class="kiri atas kanan bawah text_tengah">'.$no.'</td>
						<td class="atas kanan bawah">'.$nama_skpd.'</td>
                        '.$td_sub_keg.'
						<td style="'.$warning.'" class="atas kanan bawah text_kanan">'.$this->_number_format($skpd['total_pagu']).'</td>
                        <td style="'.$warning.'" class="atas kanan bawah text_kanan">'.$this->_number_format($skpd['total_pagu_sub_keg']).'</td>
                        <td style="'.$warning.'" class="atas kanan bawah text_kanan">'.$this->_number_format($skpd['pagurekening_all']).'</td>
					</tr>';
					$no++;
			}

            $colspan = 2;
            $judul = 'Rekening per SKPD';
            $th_sub_giat = '';
            if(!empty($sub_keg)){
                $colspan = 3;
                $th_sub_giat = '<th class="atas kanan bawah text_tengah text_blok" style="width: 200px;">Sub Kegiatan</th>';
                $judul = 'Rekening per Sub Kegiatan';
            }

            $warning = '';
            if($data_all['total_sub_keg_rekening'] != $data_all['total_sub_keg']){
                $warning = 'background: #f9d9d9;';
            }
			$footer='
                <tr>
					<td class="kiri atas kanan bawah text_kiri" colspan="'.$colspan.'"><b>TOTAL</b></td>
					<td style="'.$warning.'" class="atas kanan bawah text_kanan"><b>'.$this->_number_format($data_all['total']).'</b></td>
                    <td style="'.$warning.'" class="atas kanan bawah text_kanan"><b>'.$this->_number_format($data_all['total_sub_keg']).'</b></td>
                    <td style="'.$warning.'" class="atas kanan bawah text_kanan"><b>'.$this->_number_format($data_all['total_sub_keg_rekening']).'</b></td>
				</tr>';

			$html='<div id="preview" style="padding: 5px; overflow: auto; height: 100%;">
					<h4 style="text-align: center; margin: 0; font-weight: bold;">'.$judul.'
					<br>Tahun '.$jadwal_lokal->tahun_anggaran.' '.$nama_pemda.'
					<br>'.$jadwal_lokal->nama_jadwal.'
					</h4>
					<br>
					<table id="table-skpd-rekening" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 90%; border: 0; table-layout: fixed;" contenteditable="false">
						<thead>
							<tr>
								<th style="width: 19px;" class="kiri atas kanan bawah text_tengah text_blok">No</th>
								<th class="atas kanan bawah text_tengah text_blok">Nama Sub SKPD</th>
                                '.$th_sub_giat.'
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Rekening</th>
                                <th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Sub Kegiatan</th>
                                <th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Rekening Dalam Satu Sub Kegiatan</th>
							</tr>
						</thead>
						<tbody>'.$body.'</tbody>
						<tfoot>'.$footer.'</tfoot>
					</table>';

    		echo json_encode([
				'status' => true,
				'html' => $html,
                'title' => $title,
                'query' => $sql_rekening
			]);exit();

    	}catch(Exception $e){
    		echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit();
    	}
    }

    public function copy_rka_sipd(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil copy data RKA SIPD!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                if(!empty($_POST['tahun_anggaran']) && !empty($_POST['id_skpd']) && !empty($_POST['kode_sbl'])){
                    if(in_array('administrator', $this->role()) || in_array('PA', $this->role()) || in_array('PLT', $this->role())){
                        $tahun_anggaran = $_POST['tahun_anggaran'];
                        $kode_sbl = $_POST['kode_sbl'];
                        // if(empty($_POST['id_skpd'])){
                            $sql = $wpdb->prepare("
                                SELECT 
                                    * 
                                FROM data_sub_keg_bl_lokal 
                                WHERE kode_sbl='%s'
                                AND active=1 
                                AND tahun_anggaran=%d
                            ", $kode_sbl, $tahun_anggaran);
                        // }else{
                        //     $sql = $wpdb->prepare("
                        //         SELECT 
                        //             * 
                        //         FROM data_sub_keg_bl_lokal 
                        //         WHERE id_sub_skpd=%d 
                        //         AND active=1
                        //         AND tahun_anggaran=%d
                        //     ", $_POST['id_skpd'], $tahun_anggaran);
                        // }
                        $data_sub_keg = $wpdb->get_results($sql, ARRAY_A);
                        $ret['data'] = array();
                        if(!empty($data_sub_keg)){
                            $ret['data'] = $data_sub_keg;
                            $wpdb->update('data_rka_lokal', array('active'=>0), array('kode_sbl' => $kode_sbl, 'active' => 1, 'tahun_anggaran' => $tahun_anggaran));
                            foreach ($data_sub_keg as $keySubKeg => $valueSubKeg) {
                                $columns_rka = array(
                                    'created_user',
                                    'createddate',
                                    'createdtime',
                                    'harga_satuan',
                                    'harga_satuan_murni',
                                    'id_daerah',
                                    'id_rinci_sub_bl',
                                    'id_standar_nfs',
                                    'is_locked',
                                    'jenis_bl',
                                    'ket_bl_teks',
                                    'kode_akun',
                                    'koefisien',
                                    'koefisien_murni',
                                    'lokus_akun_teks',
                                    'nama_akun',
                                    'nama_komponen',
                                    'spek_komponen',
                                    'satuan',
                                    'spek',
                                    'sat1',
                                    'sat2',
                                    'sat3',
                                    'sat4',
                                    'volum1',
                                    'volum2',
                                    'volum3',
                                    'volum4',
                                    'volume',
                                    'volume_murni',
                                    'subs_bl_teks',
                                    'subtitle_teks',
                                    'kode_dana',
                                    'is_paket',
                                    'nama_dana',
                                    'id_dana',
                                    'substeks',
                                    'total_harga',
                                    'rincian',
                                    'rincian_murni',
                                    'totalpajak',
                                    'pajak',
                                    'pajak_murni',
                                    'updated_user',
                                    'updateddate',
                                    'updatedtime',
                                    'user1',
                                    'user2',
                                    'active',
                                    'update_at',
                                    'tahun_anggaran',
                                    'idbl',
                                    'idsubbl',
                                    'kode_bl',
                                    'kode_sbl',
                                    'id_prop_penerima',
                                    'id_camat_penerima',
                                    'id_kokab_penerima',
                                    'id_lurah_penerima',
                                    'id_penerima',
                                    'idkomponen',
                                    'idketerangan',
                                    'idsubtitle'
                                );
    
                                $sql_backup_data_rka =  "
                                    INSERT INTO data_rka_lokal (".implode(', ', $columns_rka).")
                                    SELECT 
                                        ".implode(', ', $columns_rka)."
                                    FROM data_rka 
                                    WHERE
                                        kode_sbl='".$valueSubKeg['kode_sbl']."' 
                                        AND tahun_anggaran='".$tahun_anggaran."' 
                                        AND active=1
                                ";
    
                                $query_backup = $wpdb->query($sql_backup_data_rka);

                                /** copy data dari sumber dana 
                                 * mencari id_rinci_sub_bl
                                */
                                $sql_sumber_dana = $wpdb->prepare("
                                    SELECT 
                                        * 
                                    FROM data_rka 
                                    WHERE 
                                        kode_sbl='%s'
                                        AND tahun_anggaran=%d
                                        AND active=1 
                                ", $valueSubKeg['kode_sbl'], $tahun_anggaran);

                                $data_copy_rka = $wpdb->get_results($sql_sumber_dana, ARRAY_A);

                                $columns_sumber_dana = array(
                                    'id_rinci_sub_bl',
                                    'id_sumber_dana',
                                    'user',
                                    'active',
                                    'update_at',
                                    'tahun_anggaran',
                                );

                                if(!empty($data_copy_rka)){
                                    foreach ($data_copy_rka as $key_rka => $value_rka) {
                                        $wpdb->update('data_mapping_sumberdana_lokal', array('active'=>0), array('id_rinci_sub_bl' => $value_rka['id_rinci_sub_bl'], 'active' => 1, 'tahun_anggaran' => $tahun_anggaran));
                                        $sql_backup_data_sumber_dana =  "
                                            INSERT INTO data_mapping_sumberdana_lokal (".implode(', ', $columns_sumber_dana).")
                                            SELECT 
                                                ".implode(', ', $columns_sumber_dana)."
                                            FROM data_mapping_sumberdana 
                                            WHERE
                                                id_rinci_sub_bl='".$value_rka['id_rinci_sub_bl']."' 
                                                AND tahun_anggaran='".$tahun_anggaran."' 
                                                AND active=1
                                        ";
            
                                        $query_backup = $wpdb->query($sql_backup_data_sumber_dana);
                                    }
                                }
                            }
                        }
                    }else{
                        $ret = array(
                            'status' => 'error',
                            'message'   => 'Anda tidak punya kewenangan untuk melakukan ini!'
                        );
                    }
                }else{
                    $ret = array(
                        'status' => 'error',
                        'message'   => 'Tahun Anggaran Kosong!'
                    );
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

    public function copy_renja_sipd_to_lokal(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil copy data RENJA SIPD ke lokal!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                if(!empty($_POST['tahun_anggaran'])){
                    if(in_array('administrator', $this->role())){
                        $tahun_anggaran = $_POST['tahun_anggaran'];
                        $id_skpd = (!empty($_POST['id_skpd'])) ? $_POST['id_skpd'] : '';
                        $copy_data_sipd = (!empty($_POST['copy_data_option'])) ? $_POST['copy_data_option'] : array();

                        if(!empty($id_skpd)){
                            $per_skpd = ' AND id_sub_skpd='.$id_skpd;
                            $sql_sub_keg_bl = $wpdb->prepare('
                                SELECT 
                                    *
                                FROM data_sub_keg_bl
                                WHERE active=1
                                AND tahun_anggaran=%d
                                AND id_sub_skpd='.$id_skpd.'
                            ', $tahun_anggaran);
                        }else{
                            $per_skpd = '';
                            $sql_sub_keg_bl = $wpdb->prepare('
                                SELECT 
                                    *
                                FROM data_sub_keg_bl
                                WHERE active=1
                                AND tahun_anggaran=%d
                            ', $tahun_anggaran);
                        }

                        $query_sub_keg_bl = $wpdb->get_results($sql_sub_keg_bl, ARRAY_A);

                        if(!empty($query_sub_keg_bl)){
                            /** copy data sub keg */
                            $update_per_skpd = (!empty($id_skpd)) ? array('id_sub_skpd' => $id_skpd,'active' => 1, 'tahun_anggaran' => $tahun_anggaran) : array('active' => 1, 'tahun_anggaran' => $tahun_anggaran); 
                            $wpdb->update('data_sub_keg_bl_lokal', array('active'=>0), $update_per_skpd);
                            
                            $columns_sub_keg = array(
                                'id_sub_skpd',
                                'id_lokasi',
                                'id_label_kokab',
                                'nama_dana',
                                'no_sub_giat',
                                'kode_giat',
                                'id_program',
                                'nama_lokasi',
                                'waktu_akhir',
                                'pagu_n_lalu',
                                'id_urusan',
                                'id_unik_sub_bl',
                                'id_sub_giat',
                                'label_prov',
                                'kode_program',
                                'kode_sub_giat',
                                'no_program',
                                'kode_urusan',
                                'kode_bidang_urusan',
                                'nama_program',
                                'target_4',
                                'target_5',
                                'id_bidang_urusan',
                                'nama_bidang_urusan',
                                'target_3',
                                'no_giat',
                                'id_label_prov',
                                'waktu_awal',
                                'pagumurni',
                                'pagu',
                                'pagu_simda',
                                'output_sub_giat',
                                'sasaran',
                                'indikator',
                                'id_dana',
                                'nama_sub_giat',
                                'pagu_n_depan',
                                'satuan',
                                'id_rpjmd',
                                'id_giat',
                                'id_label_pusat',
                                'nama_giat',
                                'kode_skpd',
                                'nama_skpd',
                                'kode_sub_skpd',
                                'id_skpd',
                                'id_sub_bl',
                                'nama_sub_skpd',
                                'target_1',
                                'nama_urusan',
                                'target_2',
                                'label_kokab',
                                'label_pusat',
                                'pagu_keg',
                                'pagu_fmis',
                                'id_bl',
                                'kode_bl',
                                'kode_sbl',
                                'active',
                                'update_at',
                                'tahun_anggaran'
                            );
    
                            $sql_copy_data_sub_keg_bl =  "
                                INSERT INTO data_sub_keg_bl_lokal (".implode(', ', $columns_sub_keg).", pagu_usulan, pagu_n_depan_usulan, waktu_awal_usulan, waktu_akhir_usulan, sasaran_usulan)
                                SELECT 
                                    ".implode(', ', $columns_sub_keg).", pagu, pagu_n_depan, waktu_awal, waktu_akhir, sasaran
                                FROM data_sub_keg_bl
                                WHERE active=1
                                AND tahun_anggaran='".$tahun_anggaran."' ".$per_skpd;
    
                            $querySubKeg = $wpdb->query($sql_copy_data_sub_keg_bl);

                            foreach ($query_sub_keg_bl as $v_sub_keg_bl) {

                                /** copy data indikator sub keg */
                                $wpdb->update('data_sub_keg_indikator_lokal', array('active'=>0), array('active' => 1, 'tahun_anggaran' => $tahun_anggaran, 'kode_sbl' => $v_sub_keg_bl['kode_sbl']));
        
                                $columns_indi_lokal = array(
                                    'outputteks',
                                    'targetoutput',
                                    'satuanoutput',
                                    'idoutputbl',
                                    'targetoutputteks',
                                    'kode_sbl',
                                    'idsubbl',
                                    'active',
                                    'update_at',
                                    'tahun_anggaran'
                                );
                                
                                /** mencari id_sub_giat untuk mengisi column id_indikator_sub_giat */
                                $id_indikator_sub_giat = '';
                                $set_id_indikator_sub_giat = '';

                                // get kode sub kegiatan dulu
                                $data_sub_giat = $wpdb->get_row($wpdb->prepare('
                                    SELECT 
                                        kode_sub_giat
                                    FROM data_prog_keg p
                                    WHERE p.id_sub_giat=%s
                                        AND p.tahun_anggaran=%d
                                    ', $v_sub_keg_bl['id_sub_giat'], $tahun_anggaran), ARRAY_A);

                                $data_master_indikator_sub_giat = $wpdb->get_results($wpdb->prepare('
                                    SELECT 
                                        i.*
                                    FROM data_master_indikator_subgiat i
                                    LEFT join data_prog_keg p on i.id_sub_keg=p.id_sub_giat
                                    WHERE p.kode_sub_giat=%s
                                        AND i.tahun_anggaran=%d
                                        AND i.active=1
                                    GROUP BY p.kode_sub_giat
                                    ', $data_sub_giat['kode_sub_giat'], $tahun_anggaran), ARRAY_A);

                                if(!empty($data_master_indikator_sub_giat)){
                                    $id_indikator_sub_giat = ", ".$data_master_indikator_sub_giat[0]['id_sub_keg'];
                                    $set_id_indikator_sub_giat = ', id_indikator_sub_giat';
                                }

                                $sql_copy_data_sub_keg_indikator =  "
                                    INSERT INTO data_sub_keg_indikator_lokal (".implode(', ', $columns_indi_lokal).", outputteks_usulan, targetoutput_usulan, satuanoutput_usulan, targetoutputteks_usulan".$set_id_indikator_sub_giat.")
                                    SELECT 
                                        ".implode(', ', $columns_indi_lokal).", outputteks, targetoutput, satuanoutput, targetoutputteks".$id_indikator_sub_giat."
                                    FROM data_sub_keg_indikator 
                                    WHERE active=1
                                    AND tahun_anggaran='".$tahun_anggaran."'
                                    AND kode_sbl='".$v_sub_keg_bl['kode_sbl']."'";

                                $queryIndikatorSubKeg = $wpdb->query($sql_copy_data_sub_keg_indikator);
                                
                                /** copy data capaian prog */
                                $wpdb->update('data_capaian_prog_sub_keg_lokal', array('active'=>0), array('active' => 1, 'tahun_anggaran' => $tahun_anggaran, 'kode_sbl' => $v_sub_keg_bl['kode_sbl']));

                                $columns_capaian_prog = array(
                                    'satuancapaian',
                                    'targetcapaianteks',
                                    'capaianteks',
                                    'targetcapaian',
                                    'kode_sbl',
                                    'idsubbl',
                                    'active',
                                    'update_at',
                                    'tahun_anggaran'
                                );

                                $sql_copy_data_capaian_prog_sub_keg =  "
                                    INSERT INTO data_capaian_prog_sub_keg_lokal (".implode(', ', $columns_capaian_prog).",satuancapaian_usulan, targetcapaianteks_usulan, capaianteks_usulan, targetcapaian_usulan)
                                    SELECT 
                                        ".implode(', ', $columns_capaian_prog).", satuancapaian, targetcapaianteks, capaianteks, targetcapaian
                                    FROM data_capaian_prog_sub_keg
                                    WHERE active=1
                                    AND tahun_anggaran='".$tahun_anggaran."'
                                    AND kode_sbl='".$v_sub_keg_bl['kode_sbl']."'";

                                $queryCapaianProg = $wpdb->query($sql_copy_data_capaian_prog_sub_keg);
                                
                                /** copy data output giat */
                                $wpdb->update('data_output_giat_sub_keg_lokal', array('active'=>0), array('active' => 1, 'tahun_anggaran' => $tahun_anggaran, 'kode_sbl' => $v_sub_keg_bl['kode_sbl']));
                                
                                $oclumns_output_giat = array(
                                    'outputteks',
                                    'satuanoutput',
                                    'targetoutput',
                                    'targetoutputteks',
                                    'kode_sbl',
                                    'idsubbl',
                                    'active',
                                    'update_at',
                                    'tahun_anggaran'
                                );

                                $sql_copy_data_output_giat_sub_keg =  "
                                    INSERT INTO data_output_giat_sub_keg_lokal (".implode(', ', $oclumns_output_giat).", outputteks_usulan, satuanoutput_usulan, targetoutput_usulan, targetoutputteks_usulan)
                                    SELECT 
                                        ".implode(', ', $oclumns_output_giat).", outputteks, satuanoutput, targetoutput, targetoutputteks
                                    FROM data_output_giat_sub_keg
                                    WHERE active=1
                                    AND tahun_anggaran='".$tahun_anggaran."'
                                    AND kode_sbl='".$v_sub_keg_bl['kode_sbl']."'";

                                $queryOutputGiat = $wpdb->query($sql_copy_data_output_giat_sub_keg);
                                
                                /** copy data sumber dana */
                                if(in_array("sumber_dana",$copy_data_sipd, TRUE)){
                                    $wpdb->update('data_dana_sub_keg_lokal', array('active'=>0), array('active' => 1, 'tahun_anggaran' => $tahun_anggaran, 'kode_sbl' => $v_sub_keg_bl['kode_sbl']));
                                    
                                    $columns_dana = array(
                                        'namadana',
                                        'kodedana',
                                        'iddana',
                                        'iddanasubbl',
                                        'pagudana',
                                        'kode_sbl',
                                        'idsubbl',
                                        'active',
                                        'update_at',
                                        'tahun_anggaran'
                                    );
    
                                    $sql_copy_data_dana_sub_keg =  "
                                        INSERT INTO data_dana_sub_keg_lokal (".implode(', ', $columns_dana).", nama_dana_usulan, kode_dana_usulan, id_dana_usulan, pagu_dana_usulan)
                                        SELECT 
                                            ".implode(', ', $columns_dana).", namadana, kodedana, iddana, pagudana
                                        FROM data_dana_sub_keg
                                        WHERE active=1
                                        AND tahun_anggaran='".$tahun_anggaran."'
                                        AND kode_sbl='".$v_sub_keg_bl['kode_sbl']."'";
    
                                    $queryDana = $wpdb->query($sql_copy_data_dana_sub_keg);
                                }

                                /** copy data lokasi */
                                $wpdb->update('data_lokasi_sub_keg_lokal', array('active'=>0), array('active' => 1, 'tahun_anggaran' => $tahun_anggaran, 'kode_sbl' => $v_sub_keg_bl['kode_sbl']));

                                $columns_lokasi = array(
									'camatteks',
									'daerahteks',
									'idcamat',
									'iddetillokasi',
									'idkabkota',
									'idlurah',
									'lurahteks',
									'kode_sbl',
									'idsubbl',
									'active',
									'update_at',
									'tahun_anggaran'
                                );

                                $sql_copy_data_lokasi_sub_keg =  "
                                    INSERT INTO data_lokasi_sub_keg_lokal (".implode(', ', $columns_lokasi).", camatteks_usulan, daerahteks_usulan, idcamat_usulan, iddetillokasi_usulan, idkabkota_usulan, idlurah_usulan, lurahteks_usulan)
                                    SELECT 
                                        ".implode(', ', $columns_lokasi).", camatteks, daerahteks, idcamat, iddetillokasi, idkabkota, idlurah, lurahteks
                                    FROM data_lokasi_sub_keg
                                    WHERE active=1
                                    AND tahun_anggaran='".$tahun_anggaran."'
                                    AND kode_sbl='".$v_sub_keg_bl['kode_sbl']."'";

                                $queryLokasi = $wpdb->query($sql_copy_data_lokasi_sub_keg);

                                /** copy rincian RKA */
                                if(in_array("rincian_rka",$copy_data_sipd, TRUE)){
                                    $wpdb->update('data_rka_lokal', array('active'=>0), array('active' => 1, 'tahun_anggaran' => $tahun_anggaran, 'kode_sbl' => $v_sub_keg_bl['kode_sbl']));

                                    $columns_rka = array(
                                        'created_user',
                                        'createddate',
                                        'createdtime',
                                        'harga_satuan',
                                        'harga_satuan_murni',
                                        'id_daerah',
                                        'id_rinci_sub_bl',
                                        'id_standar_nfs',
                                        'is_locked',
                                        'jenis_bl',
                                        'ket_bl_teks',
                                        'kode_akun',
                                        'koefisien',
                                        'koefisien_murni',
                                        'lokus_akun_teks',
                                        'nama_akun',
                                        'nama_komponen',
                                        'spek_komponen',
                                        'satuan',
                                        'spek',
                                        'sat1',
                                        'sat2',
                                        'sat3',
                                        'sat4',
                                        'volum1',
                                        'volum2',
                                        'volum3',
                                        'volum4',
                                        'volume',
                                        'volume_murni',
                                        'subs_bl_teks',
                                        'subtitle_teks',
                                        'kode_dana',
                                        'is_paket',
                                        'nama_dana',
                                        'id_dana',
                                        'substeks',
                                        'total_harga',
                                        'rincian',
                                        'rincian_murni',
                                        'totalpajak',
                                        'pajak',
                                        'pajak_murni',
                                        'updated_user',
                                        'updateddate',
                                        'updatedtime',
                                        'user1',
                                        'user2',
                                        'active',
                                        'update_at',
                                        'tahun_anggaran',
                                        'idbl',
                                        'idsubbl',
                                        'kode_bl',
                                        'kode_sbl',
                                        'id_prop_penerima',
                                        'id_camat_penerima',
                                        'id_kokab_penerima',
                                        'id_lurah_penerima',
                                        'id_penerima',
                                        'idkomponen',
                                        'idketerangan',
                                        'idsubtitle'
                                    );
        
                                    $sql_backup_data_rka =  "
                                        INSERT INTO data_rka_lokal (".implode(', ', $columns_rka).")
                                        SELECT 
                                            ".implode(', ', $columns_rka)."
                                        FROM data_rka 
                                        WHERE
                                            kode_sbl='".$v_sub_keg_bl['kode_sbl']."' 
                                            AND tahun_anggaran='".$tahun_anggaran."' 
                                            AND active=1
                                    ";
        
                                    $query_backup = $wpdb->query($sql_backup_data_rka);
    
                                    /** copy data dari sumber dana 
                                     * mencari id_rinci_sub_bl
                                    */
                                    $sql_sumber_dana = $wpdb->prepare("
                                        SELECT 
                                            * 
                                        FROM data_rka 
                                        WHERE 
                                            kode_sbl='%s'
                                            AND tahun_anggaran=%d
                                            AND active=1 
                                    ", $v_sub_keg_bl['kode_sbl'], $tahun_anggaran);
    
                                    $data_copy_rka = $wpdb->get_results($sql_sumber_dana, ARRAY_A);
    
                                    $columns_sumber_dana = array(
                                        'id_rinci_sub_bl',
                                        'id_sumber_dana',
                                        'user',
                                        'active',
                                        'update_at',
                                        'tahun_anggaran',
                                    );
    
                                    if(!empty($data_copy_rka)){
                                        foreach ($data_copy_rka as $key_rka => $value_rka) {
                                            $wpdb->update('data_mapping_sumberdana_lokal', array('active'=>0), array('id_rinci_sub_bl' => $value_rka['id_rinci_sub_bl'], 'active' => 1, 'tahun_anggaran' => $tahun_anggaran));
                                            $sql_backup_data_sumber_dana =  "
                                                INSERT INTO data_mapping_sumberdana_lokal (".implode(', ', $columns_sumber_dana).")
                                                SELECT 
                                                    ".implode(', ', $columns_sumber_dana)."
                                                FROM data_mapping_sumberdana 
                                                WHERE
                                                    id_rinci_sub_bl='".$value_rka['id_rinci_sub_bl']."' 
                                                    AND tahun_anggaran='".$tahun_anggaran."' 
                                                    AND active=1
                                            ";
                
                                            $query_backup = $wpdb->query($sql_backup_data_sumber_dana);
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $ret = array(
                            'status' => 'error',
                            'message'   => 'Anda tidak punya kewenangan untuk melakukan ini!'
                        );
                    }
                }else{
                    $ret = array(
                        'status' => 'error',
                        'message'   => 'Tahun Anggaran Kosong!'
                    );
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


public function get_data_batasan_pagu_sumberdana_by_id(){
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
                    FROM data_batasan_pagu_sd
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

    public function hapus_data_batasan_pagu_by_id(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil hapus data!',
            'data' => array()
        );
        if(!empty($_POST)){
            if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                $wpdb->update('data_batasan_pagu_sd', array('active' => 0), array(
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

    public function tambah_data_batasan_pagu_by_id(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil simpan data!',
            'data' => array()
        );
        if(!empty($_POST)){
            if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
                if($ret['status'] != 'error' && empty($_POST['id_dana'])){
                     $ret['status'] = 'error';
                     $ret['message'] = 'ID Dana tidak boleh kosong!';
                }
                if($ret['status'] != 'error' && empty($_POST['kode_dana'])){
                     $ret['status'] = 'error';
                     $ret['message'] = 'Kode Dana tidak boleh kosong!';
                }
                if($ret['status'] != 'error' && empty($_POST['nama_dana'])){
                     $ret['status'] = 'error';
                     $ret['message'] = 'Data nama_dana tidak boleh kosong!';
                }
                if($ret['status'] != 'error' && empty($_POST['nilai_batasan'])){
                     $ret['status'] = 'error';
                     $ret['message'] = 'Data nilai_batasan tidak boleh kosong!';
                }
                if($ret['status'] != 'error' && empty($_POST['tahun_anggaran'])){
                     $ret['status'] = 'error';
                     $ret['message'] = 'Data tahun_anggaran tidak boleh kosong!';
                }
                if($ret['status'] != 'error'){
                    $nilai_batasan = $_POST['nilai_batasan'];
                    $id_dana = $_POST['id_dana'];
                    $nama_dana = $_POST['nama_dana'];
                    $kode_dana = $_POST['kode_dana'];
                    $keterangan = $_POST['keterangan'];
                    $tahun_anggaran = $_POST['tahun_anggaran'];
                    $data = array(
                        'id_dana' => $id_dana,
                        'kode_dana' => $kode_dana,
                        'nama_dana' => $nama_dana,
                        'nilai_batasan' => $nilai_batasan,
                        'keterangan' => $keterangan,
                        'active' => 1,
                        'tahun_anggaran' => $tahun_anggaran
                    );
                    if(!empty($_POST['id_data'])){
                        $data['update_at'] = current_time('mysql');
                        $wpdb->update('data_batasan_pagu_sd', $data, array(
                            'id' => $_POST['id_data']
                        ));
                        $ret['message'] = 'Berhasil update data!';
                    }else{
                        $cek_id = $wpdb->get_row($wpdb->prepare('
                            SELECT
                                id,
                                active
                            FROM data_batasan_pagu_sd
                            WHERE kode_dana=%s
                        ', $kode_dana), ARRAY_A);
                        if(empty($cek_id)){
                            $data['created_at'] = current_time('mysql');
                            $wpdb->insert('data_batasan_pagu_sd', $data);
                        }else{
                            if($cek_id['active'] == 0){
                                $data['update_at'] = current_time('mysql');
                                $wpdb->update('data_batasan_pagu_sd', $data, array(
                                    'id' => $cek_id['id']
                                ));
                            }else{
                                $ret['status'] = 'error';
                                $ret['message'] = 'Gagal disimpan. Data Sumber Dana dengan kode_dana="'.$kode_dana.'" sudah ada!';
                            }
                        }
                    }
                }
                if(!empty($wpdb->last_error)){
                    $ret['status'] = 'error';
                    $ret['message'] = $wpdb->last_error;
                    $ret['sql'] = $wpdb->last_query;
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


	public function get_batasan_pagu_sumberdana(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				// if(!empty($_POST['tahun_anggaran'])){
					$params = $columns = $totalRecords = $data = array();
					$params = $_REQUEST;
					$columns = array(
                        0   => 'kode_dana',
                        1   => 'nama_dana',
                        2   => 'nilai_batasan',
                        3   => 'keterangan',
                        4   => 'id'  
					);
					$where = $sqlTot = $sqlRec = "";
                    $where .= ' WHERE active=1 AND tahun_anggaran='.$wpdb->prepare('%d', $params['tahun_anggaran']);

					// check search value exist
					if( !empty($params['search']['value']) ) {
						$where .=" AND ( s_dana.nama_dana LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%").")";
					}


					// getting total number records without any search
					$sqlTot = "SELECT count(*) as jml FROM `data_batasan_pagu_sd` as s_dana";
					$sqlRec = "SELECT ".implode(', ', $columns)." FROM `data_batasan_pagu_sd` as s_dana";
					if(isset($where) && $where != '') {
						$sqlTot .= $where;
						$sqlRec .= $where;
					}

					$sqlRec .=  $wpdb->prepare(" ORDER BY s_dana.". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT %d ,%d ", $params['start'], $params['length']);

					$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
					$totalRecords = $queryTot[0]['jml'];
					$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);
                    $ssst = $wpdb->last_query;

					$user_id = um_user( 'ID' );
					$user_meta = get_userdata($user_id);
					$js_check_admin = 0;

                    $sd_unset = array();
                    $dana_lokal = $wpdb->get_results("
                        SELECT 
                            s_dana.id_dana, 
                            s_dana.kode_dana,
                            s_dana.nama_dana, 
                            s_dana.tahun_anggaran,
                            SUM(s_lokal.pagudana) as total
                        FROM data_sumber_dana as s_dana 
                        LEFT JOIN data_dana_sub_keg_lokal as s_lokal ON (s_lokal.iddana=s_dana.id_dana) 
                        WHERE s_dana.active>=1 
                            AND s_lokal.active>=1 
                            AND s_dana.tahun_anggaran = ".$wpdb->prepare('%d', $params['tahun_anggaran'])." 
                        GROUP BY s_dana.id_dana 
                        ORDER BY s_dana.kode_dana asc;
                    ", ARRAY_A);
                    foreach($dana_lokal as $sd){
                        $sd_unset[$sd['kode_dana']] = $sd;
                    }

					foreach($queryRecords as $recKey => $recVal){
						$edit	= '';
						$delete	= '';

                        $edit	= '<a class="btn btn-sm btn-warning mr-2" style="text-decoration: none;" onclick="edit_batasan_pagu(\''.$recVal['id'].'\'); return false;" href="#" title="Edit Batasan Pagu"><i class="dashicons dashicons-edit"></i></a>';
                        $delete	= '<a class="btn btn-sm btn-danger" style="text-decoration: none;" onclick="hapus_batasan_pagu(\''.$recVal['id'].'\'); return false;" href="#" title="Hapus Batasan Pagu"><i class="dashicons dashicons-trash"></i></a>';

						$queryRecords[$recKey]['aksi'] = $edit.$delete;
						$queryRecords[$recKey]['pagu_terpakai'] = 0;
                        if($sd_unset[$recVal['kode_dana']]){
                            $queryRecords[$recKey]['pagu_terpakai'] = $this->_number_format($sd_unset[$recVal['kode_dana']]['total']);
                            unset($sd_unset[$recVal['kode_dana']]);
                        }
                        $queryRecords[$recKey]['nilai_batasan'] = $this->_number_format($recVal['nilai_batasan']);
					}

                    $sd_unset_html = "";
                    foreach($sd_unset as $sd){
                        $sd_unset_html .= "
                            <tr>
                                <td>$sd[kode_dana]</td>
                                <td>$sd[nama_dana]</td>
                                <td class='text-right'>".$this->_number_format($sd['total'])."</td>
                                <td class='text-center'><a class='btn btn-sm btn-info mr-2' style='text-decoration: none;' onclick=\"tambah_data_batasan_pagu('$sd[id_dana]', $sd[total]); return false;\" href='#' title='Tambah Batasan Pagu'><i class='dashicons dashicons-plus'></i></a></td>
                            </tr>
                        ";
                    }

					$json_data = array(
						"draw"            => intval( $params['draw'] ),  
						"recordsTotal"    => intval( $totalRecords ), 
						"recordsFiltered" => intval( $totalRecords ),
						"data"            => $queryRecords,
                        "sql"             => $ssst,
                        "sd_unset"        => $sd_unset_html
					);

					die(json_encode($json_data));
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}
}