<?php

class Wpsipd_Public_Ssh
{
	function ssh_tidak_terpakai(){

		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/ssh/wpsipd-public-ssh-tidak-terpakai.php';
	}
}