<?php

class Wpsipd_Public_RKA {

    public function verifikasi_rka(){
        if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-verifikasi-rka.php';
    }

    public function user_verikasi_rka(){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
        }
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/penganggaran/wpsipd-public-user-verifikasi-rka.php';
    }
}