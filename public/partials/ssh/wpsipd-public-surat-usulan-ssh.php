<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$input = shortcode_atts( array(
	'id_surat' => '0',
	'tahun_anggaran' => '2022'
), $atts );

if(empty($input['id_surat'])){
	die('<h1>ID Surat tidak boleh kosong!</h1>');
}
global $wpdb;
$ssh = $wpdb->get_results($wpdb->prepare("
	SELECT
		h.*
	FROM data_ssh_usulan as h
	LEFT JOIN data_surat_usulan_ssh as s on s.nomor_surat=h.no_surat_usulan
		AND s.tahun_anggaran=h.tahun_anggaran
	WHERE s.id=%d
", $input['id_surat']), ARRAY_A);

print_r($ssh); die($wpdb->last_query);
?>