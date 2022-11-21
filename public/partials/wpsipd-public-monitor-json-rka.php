<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

$body_json = '';
$nama_pemda = get_option('_crb_daerah');

$sql_unit = $wpdb->prepare("
	SELECT 
		*
	FROM data_unit 
	WHERE 
        tahun_anggaran=%d
		AND id_skpd =%d
		AND active=1
	order by id_skpd ASC
    ", $input['tahun_anggaran'], $input['id_skpd']);
$unit = $wpdb->get_results($sql_unit, ARRAY_A);

$unit_utama = $unit;
if($unit[0]['id_unit'] != $unit[0]['id_skpd']){
    $sql_unit_utama = $wpdb->prepare("
        SELECT 
            *
        FROM data_unit
        WHERE 
            tahun_anggaran=%d
            AND id_skpd=%d
            AND active=1
        order by id_skpd ASC
        ", $input['tahun_anggaran'], $unit[0]['id_unit']);
    $unit_utama = $wpdb->get_results($sql_unit_utama, ARRAY_A);
}

$unit = (!empty($unit)) ? $unit : array();
$nama_skpd = (!empty($unit[0]['nama_skpd'])) ? $unit[0]['nama_skpd'] : '-';

$sql_anggaran = $wpdb->prepare("
    SELECT
        k.*,
        r.*,
        ms.id_dana,
        ms.nama_dana
    FROM data_sub_keg_bl as k
    INNER JOIN data_rka as r on k.kode_sbl=r.kode_sbl
        and r.active=k.active
        and r.tahun_anggaran=k.tahun_anggaran
    LEFT JOIN data_mapping_sumberdana as s on r.id_rinci_sub_bl=s.id_rinci_sub_bl
        and s.active=k.active
        and s.tahun_anggaran=k.tahun_anggaran
    LEFT JOIN data_sumber_dana as ms on ms.id_dana=s.id_sumber_dana
        and ms.tahun_anggaran=k.tahun_anggaran
    WHERE
        k.tahun_anggaran=%d
        AND k.active=1
        AND k.id_sub_skpd=%d
    ",$input["tahun_anggaran"], $input['id_skpd']);
echo $sql_anggaran;