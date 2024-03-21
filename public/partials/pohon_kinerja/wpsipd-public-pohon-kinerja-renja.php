<?php

// If this file is called directly, abort.
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );
$nama_pemda = get_option('_crb_daerah');
$id_lokasi_prov = get_option('_crb_id_lokasi_prov');
$id_lokasi_kokab = (empty(get_option('_crb_id_lokasi_kokab'))) ? 0 : get_option('_crb_id_lokasi_kokab');

if($id_lokasi_prov == 0 || empty($id_lokasi_prov)){
    die('Setting ID lokasi Provinsi di SIPD Options tidak boleh kosong!');
}