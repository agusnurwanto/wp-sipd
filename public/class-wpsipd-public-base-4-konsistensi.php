<?php

class Wpsipd_Public_Base_4_Konsistensi
{

    public function konsistensi_rpjm_rkpd_kua($atts)
    {
        if (!empty($_GET) && !empty($_GET['post'])) {
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/konsistensi/wpsipd-public-format-konsistensi.php';
    }
    public function pemilik_resiko_manrisk(){
        $nama_pemda = get_option('_crb_daerah');
        if (empty($nama_pemda) || $nama_pemda == 'false') {
            $nama_pemda = '';
        }
        return array(
            array(
                'id' => 1,
                'nama' => 'Kepala Daerah',
                'keterangan' => ''
            ),
            array(
                'id' => 2,
                'nama' => 'Kepala OPD',
                'keterangan' => ''
            ),
            array(
                'id' => 3,
                'nama' => 'Kepala Bidang',
                'keterangan' => ''
            )
        );
    }

    public function sumber_sebab_manrisk(){
        $nama_pemda = get_option('_crb_daerah');
        if (empty($nama_pemda) || $nama_pemda == 'false') {
            $nama_pemda = '';
        }
        return array(
            array(
                'id' => 1,
                'nama' => 'Internal',
                'keterangan' => ''
            ),
            array(
                'id' => 2,
                'nama' => 'Eksternal',
                'keterangan' => ''
            ),
            array(
                'id' => 3,
                'nama' => 'Internal & Eksternal',
                'keterangan' => ''
            )
        );
    }

    public function pihak_terdampak_manrisk(){
        $nama_pemda = get_option('_crb_daerah');
        if (empty($nama_pemda) || $nama_pemda == 'false') {
            $nama_pemda = '';
        }
        return array(
            array(
                'id' => 1,
                'nama' => 'Pemerintah '.$nama_pemda,
                'keterangan' => ''
            ),
            array(
                'id' => 2,
                'nama' => 'Perangkat Daerah',
                'keterangan' => ''
            ),
            array(
                'id' => 3,
                'nama' => 'Kepala OPD',
                'keterangan' => ''
            ),
            array(
                'id' => 4,
                'nama' => 'Pegawai OPD',
                'keterangan' => ''
            ),
            array(
                'id' => 5,
                'nama' => 'Masyarakat',
                'keterangan' => ''
            )
        );
    }
    public function options_manrisk(){
        global $wpdb;
        $ret = array(
            'status' => 'success',
            'message' => 'Berhasil mendapatkan data!',
            'data' => array(
                'pemilik_resiko' => $this->pemilik_resiko_manrisk(),
                'sumber_sebab' => $this->sumber_sebab_manrisk(),
                'pihak_terdampak' => $this->pihak_terdampak_manrisk()
            )
        );

        die(json_encode($ret));
    }
}