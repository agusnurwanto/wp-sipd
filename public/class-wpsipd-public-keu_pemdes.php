<?php
class Wpsipd_Public_Keu_Pemdes
{

    public function keu_pemdes_bhpd($atts){
        if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/keu_pemdes/wpsipd-public-keu-pemdes-bhpd.php';
    }
}