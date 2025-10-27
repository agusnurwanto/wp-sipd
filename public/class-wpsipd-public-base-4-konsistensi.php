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
        return array(
            array(
                'id' => 1,
                'nama' => 'Kepala Daerah',
                'keterangan' => 'untuk risiko Pemda (Risiko StrategisPemda)'
            ),
            array(
                'id' => 2,
                'nama' => 'Kepala OPD',
                'keterangan' => 'untuk risiko yang menghambat tujuan OPD/ pada Program (Risiko Strategis OPD)'
            ),
            array(
                'id' => 3,
                'nama' => 'Kepala Bidang',
                'keterangan' => 'untuk risiko pada Program/Kegiatan (Risiko Operasional)'
            )
        );
    }

    public function sumber_sebab_manrisk(){
        return array(
            array(
                'id' => 1,
                'nama' => 'Internal',
                'keterangan' => 'Sumber risiko berasal dari internal'
            ),
            array(
                'id' => 2,
                'nama' => 'Eksternal',
                'keterangan' => 'Sumber risiko berasal dari eksternal'
            ),
            array(
                'id' => 3,
                'nama' => 'Internal & Eksternal',
                'keterangan' => 'Sumber risiko berasal dari internal dan eksternal'
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

    public function jenis_resiko_manrisk(){
        return array(
            array(
                'id' => 1,
                'nama' => 'Konflik kepentingan',
                'keterangan' => ''
            ),
            array(
                'id' => 2,
                'nama' => 'Pemberian suap',
                'keterangan' => ''
            ),
            array(
                'id' => 3,
                'nama' => 'Penggelapan',
                'keterangan' => ''
            ),
            array(
                'id' => 4,
                'nama' => 'Pemalsuan Data',
                'keterangan' => ''
            ),
            array(
                'id' => 5,
                'nama' => 'Pemerasan/ Pungutan Liar',
                'keterangan' => ''
            ),
            array(
                'id' => 6,
                'nama' => 'Penyalahgunaan wewenang',
                'keterangan' => ''
            ),
            array(
                'id' => 7,
                'nama' => 'Fraud/ Korupsi',
                'keterangan' => ''
            )
        );
    }

    public function resiko_kemungkinan_manrisk(){
        return array(
            array(
                'id' => 1,
                'skor' => '1',
                'keterangan' => 'Jarang Terjadi'
            ),
            array(
                'id' => 2,
                'skor' => '2',
                'keterangan' => 'Kadang Terjadi'
            ),
            array(
                'id' => 3,
                'skor' => '3',
                'keterangan' => 'Sering Terjadi'
            ),
            array(
                'id' => 4,
                'skor' => '4',
                'keterangan' => 'Sangat Sering Terjadi'
            ),
            array(
                'id' => 5,
                'skor' => '5',
                'keterangan' => 'Hampir Pasti Terjadi'
            )
        );
    }

    public function resiko_dampak_manrisk(){
        return array(
            array(
                'id' => 1,
                'skor' => '1',
                'keterangan' => 'Tidak Signifikan'
            ),
            array(
                'id' => 2,
                'skor' => '2',
                'keterangan' => 'Kecil'
            ),
            array(
                'id' => 3,
                'skor' => '3',
                'keterangan' => 'Sedang'
            ),
            array(
                'id' => 4,
                'skor' => '4',
                'keterangan' => 'Besar'
            ),
            array(
                'id' => 5,
                'skor' => '5',
                'keterangan' => 'Katastoprik'
            )
        );
    }

    public function perkalian_kemungkinan_dampak_manrisk(){
        return array(
            array(
                'id' => 1,
                'skor' => '1 s.d 3',
                'keterangan' => 'Sangat Rendah'
            ),
            array(
                'id' => 2,
                'skor' => '4 s.d 8',
                'keterangan' => 'Rendah'
            ),
            array(
                'id' => 3,
                'skor' => '9 s.d 17',
                'keterangan' => 'Sedang'
            ),
            array(
                'id' => 4,
                'skor' => '18 s.d 22',
                'keterangan' => 'Tinggi'
            ),
            array(
                'id' => 5,
                'skor' => '23 s.d 25',
                'keterangan' => 'Sangat Tinggi'
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
                'pihak_terdampak' => $this->pihak_terdampak_manrisk(),
                'jenis_resiko' => $this->jenis_resiko_manrisk(),
                'kemungkinan_resiko' => $this->resiko_kemungkinan_manrisk(),
                'dampak_resiko' => $this->resiko_dampak_manrisk(),
                'kemungkinan_dampak' => $this->perkalian_kemungkinan_dampak_manrisk()
            )
        );

        die(json_encode($ret));
    }
}