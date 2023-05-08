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
                ", $newData['desa'], $newData['kecamatan'], $newData['kegiatan'], $newData['alamat']));
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
}