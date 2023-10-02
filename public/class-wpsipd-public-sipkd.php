<?php
require_once WPSIPD_PLUGIN_PATH."/public/class-wpsipd-public-keu_pemdes.php";

class Wpsipd_Public_Sipkd extends Wpsipd_Public_Keu_Pemdes{

    public function sipkd_akun(){
        if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/sipkd/wpsipd-public-akun.php';
    }
    public function sipkd_urusan_skpd(){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
		}
        require_once WPSIPD_PLUGIN_PATH . 'public/partials/sipkd/wpsipd-public-urusan.php';
    }
    

}