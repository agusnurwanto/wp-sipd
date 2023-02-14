<?php

class Wpsipd_Public_Sipkd{

    public function sipkd_akun(){
        if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/sipkd/wpsipd-public-akun.php';
    }
    public function sipkd_urusan_skpd(){
        if(!empty($_GET) && !empty($_GET['post'])){
            return '';
		}
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/sipkd/wpsipd-public-urusan.php';
    }
    

}