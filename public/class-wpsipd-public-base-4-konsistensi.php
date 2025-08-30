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
}