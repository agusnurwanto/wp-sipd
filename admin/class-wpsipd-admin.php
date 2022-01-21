<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/agusnurwanto
 * @since      1.0.0
 *
 * @package    Wpsipd
 * @subpackage Wpsipd/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpsipd
 * @subpackage Wpsipd/admin
 * @author     Agus Nurwanto <agusnurwantomuslim@gmail.com>
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Wpsipd_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $simda;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $simda ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->simda = $simda;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpsipd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpsipd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpsipd-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpsipd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpsipd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpsipd-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'wpsipd', array(
		    'api_key' => get_option( '_crb_api_key_extension' )
		));

	}

	function gen_key($key_db = false, $options = array()){
		$now = time()*1000;
		if(empty($key_db)){
			$key_db = md5(get_option( '_crb_api_key_extension' ));
		}
		$tambahan_url = '';
		if(!empty($options['custom_url'])){
			$custom_url = array();
			foreach ($options['custom_url'] as $k => $v) {
				$custom_url[] = $v['key'].'='.$v['value'];
			}
			$tambahan_url = $key_db.implode('&', $custom_url);
		}
		$key = base64_encode($now.$key_db.$now.$tambahan_url);
		return $key;
	}

	public function get_link_post($custom_post){
		$link = get_permalink($custom_post);
		$options = array();
		if(!empty($custom_post->custom_url)){
			$options['custom_url'] = $custom_post->custom_url;
		}
		if(strpos($link, '?') === false){
			$link .= '?key=' . $this->gen_key(false, $options);
		}else{
			$link .= '&key=' . $this->gen_key(false, $options);
		}
		return $link;
	}

	public function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	public function generatePage($nama_page, $tahun_anggaran, $content = false, $update = false){
		$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
		if(empty($content)){
			$content = '[monitor_sipd tahun_anggaran="'.$tahun_anggaran.'"]';
		}

		$_post = array(
			'post_title'	=> $nama_page,
			'post_content'	=> $content,
			'post_type'		=> 'page',
			'post_status'	=> 'private',
			'comment_status'	=> 'closed'
		);
		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post($_post);
			$_post['insert'] = 1;
			$_post['ID'] = $id;
			$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
			update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
			update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
			update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
			update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
			update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
			update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
			update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
			update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
		}else if($update){
			$_post['ID'] = $custom_post->ID;
			wp_update_post( $_post );
			$_post['update'] = 1;
		}
		return $this->get_link_post($custom_post);
	}

	// https://docs.carbonfields.net/#/containers/theme-options
	public function crb_attach_sipd_options(){
		if( !is_admin() ){
        	return;
        }
		$basic_options_container = Container::make( 'theme_options', __( 'SIPD Options' ) )
			->set_page_menu_position( 4 )
	        ->add_fields( $this->options_basic() );

	    Container::make( 'theme_options', __( 'SIMDA Setting' ) )
		    ->set_page_parent( $basic_options_container )
		    ->add_fields( $this->get_mapping_unit() );

	    Container::make( 'theme_options', __( 'FMIS Setting' ) )
		    ->set_page_parent( $basic_options_container )
		    ->add_fields( $this->get_setting_fmis() );

	    $monev = Container::make( 'theme_options', __( 'MONEV SIPD' ) )
			->set_page_menu_position( 4 )
		    ->add_fields( $this->get_ajax_field(array('type' => 'rfk')) );

	    Container::make( 'theme_options', __( 'RFK' ) )
		    ->set_page_parent( $monev )
		    ->add_fields( $this->get_ajax_field(array('type' => 'rfk')) );

		Container::make( 'theme_options', __( 'Indikator RPJM' ) )
		    ->set_page_parent( $monev )
		    ->add_fields( $this->get_ajax_field(array('type' => 'monev_rpjm')) );

		Container::make( 'theme_options', __( 'Indikator RENSTRA' ) )
		    ->set_page_parent( $monev )
		    ->add_fields( $this->get_ajax_field(array('type' => 'monev_renstra')) );

	    Container::make( 'theme_options', __( 'Indikator RENJA' ) )
		    ->set_page_parent( $monev )
		    ->add_fields( $this->get_ajax_field(array('type' => 'monev_renja')) );

	    Container::make( 'theme_options', __( 'Label Komponen' ) )
		    ->set_page_parent( $monev )
		    ->add_fields( $this->generate_label_komponen() );

	    Container::make( 'theme_options', __( 'Sumber Dana' ) )
		    ->set_page_parent( $monev )
		    ->add_fields( $this->generate_sumber_dana() );

	    $laporan = Container::make( 'theme_options', __( 'LAPORAN SIPD' ) )
			->set_page_menu_position( 4 )
		    ->add_fields( $this->generate_tag_sipd() );

	    Container::make( 'theme_options', __( 'Tag/Label Sub Kegiatan' ) )
		    ->set_page_parent( $laporan )
		    ->add_fields( $this->generate_tag_sipd() );

	    Container::make( 'theme_options', __( 'RKPD & RENJA' ) )
		    ->set_page_parent( $laporan );

	    Container::make( 'theme_options', __( 'APBD Penjabaran' ) )
		    ->set_page_parent( $laporan )
		    ->add_fields( $this->get_ajax_field(array('type' => 'apbdpenjabaran')) );

	    Container::make( 'theme_options', __( 'RPJM & RENSTRA' ) )
		    ->set_page_parent( $laporan );
	}

	public function options_basic(){
		global $wpdb;
		// $sinergi_link = $this->generate_sinergi_page();
		// $sirup_link = $this->generate_sirup_page();
		// $sibangda_link = $this->generate_sibangda_page();
		// $simda_link = $this->generate_simda_page();
		// $siencang_link = $this->generate_siencang_page();
		$sumber_dana_all = array();
		$sumber_dana = $wpdb->get_results("
			SELECT
				id_dana,
				kode_dana,
				nama_dana
			from data_sumber_dana
			group by id_dana
		", ARRAY_A);
		foreach ($sumber_dana as $k => $v) {
			$sumber_dana_all[$v['id_dana']] = $v['kode_dana'].' '.$v['nama_dana'].' ['.$v['id_dana'].']';
		}
		$options_basic = array(
            Field::make( 'text', 'crb_awal_rpjmd', 'Tahun Awal RPJMD' )
            	->set_default_value('2018'),
            Field::make( 'text', 'crb_tahun_anggaran_sipd', 'Tahun Anggaran SIPD' )
            	->set_default_value('2021'),
            Field::make( 'text', 'crb_daerah', 'Nama Pemda' )
            	->set_help_text('Data diambil dari halaman pengaturan SIPD menggunakan <a href="https://github.com/agusnurwanto/sipd-chrome-extension" target="_blank">SIPD chrome extension</a>.')
            	->set_default_value(get_option('_crb_daerah' ))
            	->set_attribute('readOnly', 'true'),
            Field::make( 'text', 'crb_kepala_daerah', 'Kepala Daerah' )
            	->set_default_value(get_option('_crb_kepala_daerah' ))
            	->set_attribute('readOnly', 'true'),
            Field::make( 'text', 'crb_wakil_daerah', 'Wakil Kepala Daerah' )
            	->set_default_value(get_option('_crb_wakil_daerah' ))
            	->set_attribute('readOnly', 'true'),
            Field::make( 'text', 'crb_api_key_extension', 'API KEY chrome extension' )
            	->set_default_value($this->generateRandomString())
            	->set_help_text('API KEY ini dipakai untuk <a href="https://github.com/agusnurwanto/sipd-chrome-extension" target="_blank">SIPD chrome extension</a>.'),
            Field::make( 'radio', 'crb_kunci_sumber_dana_mapping', 'Kunci pilihan Sumber Dana di Halaman Mapping Rincian' )
            	->add_options( array(
			        '1' => __( 'Ya' ),
			        '2' => __( 'Tidak' )
			    ) )
            	->set_default_value('1')
            	->set_help_text('Fitur ini untuk mengunci pilihan sumber dana sesuai yang sudah disetting di sipd.kemendagri.go.id saat melakukan mapping sumber dana. Pilih <b>Tidak</b> jika tidak ingin mengunci pilihan sumber dana.'),
            Field::make( 'radio', 'crb_cara_input_realisasi', 'Input realisasi anggaran' )
            	->add_options( array(
			        '1' => __( 'Otomatis dari database SIMDA' ),
			        '2' => __( 'Manual' )
			    ) )
            	->set_default_value('1')
            	->set_help_text('Jika dipilih manual maka SKPD perlu melakuan input manual realisasi anggaran.'),
            Field::make( 'multiselect', 'crb_daftar_tombol_user_dashboard', 'Daftar tombol di halaman dashboard user' )
            	->add_options( array(
			        '1' => __( 'MONEV RFK' ),
			        '2' => __( 'MONEV SUMBER DANA' ),
			        '3' => __( 'MONEV LABEL KOMPONEN' ),
			        '4' => __( 'MONEV INDIKATOR RENJA' ),
			        '5' => __( 'MONEV INDIKATOR RENSTRA' ),
			        '6' => __( 'MONEV INDIKATOR RPJM' )
			    ) )
            	->set_default_value(array('1','2','3','4','5'))
            	->set_help_text('Daftar fitur ini akan ditampilkan dalam bentuk tombol di halaman dasboard user setelah berhasil login.'),
            Field::make( 'select', 'crb_default_sumber_dana', 'Sumber dana default ketika sumber dana di sub kegiatan belum disetting' )
            	->add_options( $sumber_dana_all )
            	->set_default_value(1)
            	->set_help_text('Sumber dana ini akan digunakan di custom mapping sumber dana dan ketika singkron ke SIMDA'),
            Field::make( 'html', 'crb_generate_user_sipd_merah' )
            	->set_html( '<a id="generate_user_sipd_merah" onclick="return false;" href="#" class="button button-primary button-large">Generate User SIPD Merah By DB Lokal</a>' )
            	->set_help_text('Data user active yang ada di table data_dewan akan digenerate menjadi user wordpress.'),
            /*Field::make( 'html', 'crb_siencang' )
            	->set_html( '<a target="_blank" href="'.$siencang_link.'">SIPD to SIENCANG</a> | <a href="https://github.com/ganjarnugraha/perencanaan-penganggaran" target="_blank">https://github.com/ganjarnugraha/perencanaan-penganggaran</a>' ),
            Field::make( 'html', 'crb_simda' )
            	->set_html( '<a target="_blank" href="'.$simda_link.'">SIPD to SIMDA BPKP</a>' ),
            Field::make( 'html', 'crb_sibangda' )
            	->set_html( '<a target="_blank" href="'.$sibangda_link.'">SIPD to SIBANGDA Bina Pembangunan Kemendagri</a>' ),
            Field::make( 'html', 'crb_sirup' )
            	->set_html( '<a target="_blank" href="'.$sirup_link.'">SIPD to SIRUP LKPP</a>' ),
            Field::make( 'html', 'crb_sinergi' )
            	->set_html( '<a target="_blank" href="'.$sinergi_link.'">SIPD to SINERGI DJPK Kementrian Keuangan</a>' ),*/
        );
        $tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran order by tahun_anggaran ASC', ARRAY_A);
        foreach ($tahun as $k => $v) {
			$url = $this->generatePage('Monitoring Update Data SIPD lokal Berdasar Waktu Terakhir Melakukan Singkronisasi Data | '.$v['tahun_anggaran'], $v['tahun_anggaran']);
			$options_basic[] = Field::make( 'html', 'crb_monitor_update_'.$k )
            	->set_html( '<a target="_blank" href="'.$url.'">Halaman Monitor Update Data Lokal SIPD Merah Tahun '.$v['tahun_anggaran'].'</a>' );
        }
        return $options_basic;
	}

	public function get_ajax_field($options = array('type' => 'rfk')){
		$ret = array();
		$hide_sidebar = Field::make( 'html', 'crb_hide_sidebar' )
        	->set_html( '
        		<style>
        			.postbox-container { display: none; }
        			#poststuff #post-body.columns-2 { margin: 0 !important; }
        		</style>
        		<div id="load_ajax_carbon" data-type="'.$options['type'].'"></div>
        	' );
		$ret[] = $hide_sidebar;
		return $ret;
	}

	public function load_ajax_carbon(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> ''
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension' )) {
				$body_all = '';
				$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran order by tahun_anggaran ASC', ARRAY_A);
				foreach ($tahun as $k => $v) {
		            $unit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd, nipkepala from data_unit where active=1 and tahun_anggaran=".$v['tahun_anggaran'].' and is_skpd=1 order by kode_skpd ASC', ARRAY_A);
		            $body_pemda = '<ul style="margin-left: 20px;">';
		            foreach ($unit as $kk => $vv) {
		            	$subunit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd, nipkepala from data_unit where active=1 and tahun_anggaran=".$v['tahun_anggaran']." and is_skpd=0 and id_unit=".$vv["id_skpd"]." order by kode_skpd ASC", ARRAY_A);

						if($_POST['type'] == 'rfk'){
							$url_skpd = $this->generatePage('RFK '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rfk tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vv['id_skpd'].'"]');
		            		$body_pemda .= '<li><a target="_blank" href="'.$url_skpd.'">Halaman RFK '.$vv['kode_skpd'].' '.$vv['nama_skpd'].' '.$v['tahun_anggaran'].'</a> (NIP: '.$vv['nipkepala'].')';
						}else if($_POST['type'] == 'monev_renja'){
							$url_skpd = $this->generatePage('MONEV '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renja tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vv['id_skpd'].'"]');
		            		$body_pemda .= '<li><a target="_blank" href="'.$url_skpd.'">Halaman MONEV '.$vv['kode_skpd'].' '.$vv['nama_skpd'].' '.$v['tahun_anggaran'].'</a> (NIP: '.$vv['nipkepala'].')';
						}else if($_POST['type'] == 'monev_renstra'){
							$url_skpd = $this->generatePage('MONEV RENSTRA '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renstra tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vv['id_skpd'].'"]');
		            		$body_pemda .= '<li><a target="_blank" href="'.$url_skpd.'">Halaman MONEV RENSTRA '.$vv['kode_skpd'].' '.$vv['nama_skpd'].' '.$v['tahun_anggaran'].'</a> (NIP: '.$vv['nipkepala'].')';
						}else if($_POST['type'] == 'sumber_dana'){
							$this->generatePage('Sumber Dana '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_daftar_sumber_dana tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vv['id_skpd'].'"]');
						}else if($_POST['type'] == 'label_komponen'){
							$this->generatePage('Label Komponen '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_daftar_label_komponen tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vv['id_skpd'].'"]');
						}else if($_POST['type'] == 'apbdpenjabaran'){
							$url_skpd = $this->generatePage($v['tahun_anggaran'] .' | '.$vv['kode_skpd'].' | '.$vv['nama_skpd'].' | '. ' | APBD PENJABARAN Lampiran 2', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="'.$v['tahun_anggaran'].'" lampiran="2" id_skpd="'.$vv['id_skpd'].'"]');
		            		$body_pemda .= '<li><a target="_blank" href="'.$url_skpd.'">Halaman APBD PENJABARAN Lampiran 2 '.$vv['kode_skpd'].' '.$vv['nama_skpd'].' '.$v['tahun_anggaran'].'</a> (NIP: '.$vv['nipkepala'].')';
						}

		            	if(!empty($subunit)){
		            		$body_pemda .= '<ul style="margin: 5px 20px;">';
		            	}
		            	foreach ($subunit as $kkk => $vvv) {
							if($_POST['type'] == 'rfk'){
								$url_skpd = $this->generatePage('RFK '.$vvv['nama_skpd'].' '.$vvv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rfk tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vvv['id_skpd'].'"]');
			            		$body_pemda .= '<li><a target="_blank" href="'.$url_skpd.'">Halaman RFK '.$vvv['kode_skpd'].' '.$vvv['nama_skpd'].' '.$v['tahun_anggaran'].'</a> (NIP: '.$vvv['nipkepala'].')</li>';
							}else if($_POST['type'] == 'monev_renja'){
								$url_skpd = $this->generatePage('MONEV '.$vvv['nama_skpd'].' '.$vvv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renja tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vvv['id_skpd'].'"]');
			            		$body_pemda .= '<li><a target="_blank" href="'.$url_skpd.'">Halaman MONEV '.$vvv['kode_skpd'].' '.$vvv['nama_skpd'].' '.$v['tahun_anggaran'].'</a> (NIP: '.$vvv['nipkepala'].')</li>';
							}else if($_POST['type'] == 'monev_renstra'){
								$url_skpd = $this->generatePage('MONEV RENSTRA '.$vvv['nama_skpd'].' '.$vvv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renstra tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vvv['id_skpd'].'"]');
			            		$body_pemda .= '<li><a target="_blank" href="'.$url_skpd.'">Halaman MONEV RENSTRA '.$vvv['kode_skpd'].' '.$vvv['nama_skpd'].' '.$v['tahun_anggaran'].'</a> (NIP: '.$vvv['nipkepala'].')</li>';
							}else if($_POST['type'] == 'sumber_dana'){
		            			$this->generatePage('Sumber Dana '.$vvv['nama_skpd'].' '.$vvv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_daftar_sumber_dana tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vvv['id_skpd'].'"]');
							}else if($_POST['type'] == 'label_komponen'){
								$this->generatePage('Label Komponen '.$vvv['nama_skpd'].' '.$vvv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_daftar_label_komponen tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vvv['id_skpd'].'"]');
							}else if($_POST['type'] == 'apbdpenjabaran'){
								$url_skpd = $this->generatePage($v['tahun_anggaran'] .' | '.$vvv['kode_skpd'].' | '.$vvv['nama_skpd'].' | '. ' | APBD PENJABARAN Lampiran 2', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="'.$v['tahun_anggaran'].'" lampiran="2" id_skpd="'.$vvv['id_skpd'].'"]');
			            		$body_pemda .= '<li><a target="_blank" href="'.$url_skpd.'">Halaman APBD PENJABARAN Lampiran 2 '.$vv['kode_skpd'].' '.$vvv['nama_skpd'].' '.$v['tahun_anggaran'].'</a> (NIP: '.$vvv['nipkepala'].')';
							}
		            	}
		            	if(!empty($subunit)){
		            		$body_pemda .= '</ul>';
		            	}
		            	$body_pemda .= '</li>';
		            }
		            $body_pemda .= '</ul>';

					if($_POST['type'] == 'rfk'){
						$url_pemda = $this->generatePage('Realisasi Fisik dan Keuangan Pemerintah Daerah | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rfk tahun_anggaran="'.$v['tahun_anggaran'].'"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="'.$url_pemda.'">Halaman Realisasi Fisik dan Keuangan Pemerintah Daerah Tahun '.$v['tahun_anggaran'].'</a>'.$body_pemda;
					}else if($_POST['type'] == 'monev_renja'){
						$url_pemda = $this->generatePage('MONEV RENJA Pemerintah Daerah | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renja tahun_anggaran="'.$v['tahun_anggaran'].'"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="'.$url_pemda.'">Halaman MONEV RENJA Daerah Tahun '.$v['tahun_anggaran'].'</a>'.$body_pemda;
			        }else if($_POST['type'] == 'monev_renstra'){
						$url_pemda = $this->generatePage('MONEV RENSTRA Pemerintah Daerah | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renstra tahun_anggaran="'.$v['tahun_anggaran'].'"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="'.$url_pemda.'">Halaman MONEV RENSTRA Daerah Tahun '.$v['tahun_anggaran'].'</a>'.$body_pemda;
			        }else if($_POST['type'] == 'monev_rpjm'){
						$url_pemda = $this->generatePage('MONEV RPJM Pemerintah Daerah | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_rpjm tahun_anggaran="'.$v['tahun_anggaran'].'"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="'.$url_pemda.'">Halaman MONEV RPJM Daerah Tahun '.$v['tahun_anggaran'].'</a>'.$body_pemda;
			        }else if($_POST['type'] == 'apbdpenjabaran'){
			        	$url_penjabaran1 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 1', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="'.$v['tahun_anggaran'].'" lampiran="1"]');
			        	$url_penjabaran3 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 3', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="'.$v['tahun_anggaran'].'" lampiran="3"]');
			        	$url_penjabaran4 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 4', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="'.$v['tahun_anggaran'].'" lampiran="4"]');
			        	$url_penjabaran5 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 5', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="'.$v['tahun_anggaran'].'" lampiran="5"]');
			        	$url_penjabaran6 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 6', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="'.$v['tahun_anggaran'].'" lampiran="6"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="'.$url_penjabaran1.'">Halaman APBD PENJABARAN Lampiran 1 Tahun '.$v['tahun_anggaran'].'</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="'.$url_penjabaran3.'">Halaman APBD PENJABARAN Lampiran 3 Tahun '.$v['tahun_anggaran'].'</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="'.$url_penjabaran4.'">Halaman APBD PENJABARAN Lampiran 4 Tahun '.$v['tahun_anggaran'].'</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="'.$url_penjabaran5.'">Halaman APBD PENJABARAN Lampiran 5 Tahun '.$v['tahun_anggaran'].'</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="'.$url_penjabaran6.'">Halaman APBD PENJABARAN Lampiran 6 Tahun '.$v['tahun_anggaran'].'</a>';
						$body_all .= $body_pemda;
			        }
				}
				if(
					$_POST['type'] == 'rfk' 
					|| $_POST['type'] == 'monev_renja' 
					|| $_POST['type'] == 'monev_renstra'
					|| $_POST['type'] == 'monev_rpjm'
					|| $_POST['type'] == 'apbdpenjabaran'
				){
					$ret['message'] = $body_all;
				}
			}
		}
		die(json_encode($ret));
	}

	public function get_setting_fmis(){
		global $wpdb;
		$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
		$unit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd from data_unit where active=1 and tahun_anggaran=".$tahun_anggaran.' order by id_skpd ASC', ARRAY_A);
		$mapping_unit = array();
		$mapping_unit[] = Field::make( 'html', 'crb_fmis_keterangan' )
	        ->set_html( '<h3>Tahun anggaran WP-SIPD: '.$tahun_anggaran.'</h3>Informasi terkait integrasi data WP-SIPD ke FMIS bisa dicek di <a href="https://smkasiyahhomeschooling.blogspot.com/2021/12/fmis-chrome-extension-untuk-integrasi.html" target="blank">https://smkasiyahhomeschooling.blogspot.com/2021/12/fmis-chrome-extension-untuk-integrasi.html</a>.' );
		foreach ($unit as $k => $v) {
			$mapping_unit[] = Field::make( 'text', 'crb_unit_fmis_'.$tahun_anggaran.'_'.$v['id_skpd'], ($k+1).'. Kode Sub Unit FMIS untuk '.$v['kode_skpd'].' '.$v['nama_skpd'] );
		}
		return $mapping_unit;
	}

	public function get_mapping_unit(){
		global $wpdb;
		$unit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd from data_unit where active=1 and tahun_anggaran=".get_option('_crb_tahun_anggaran_sipd').' order by id_skpd ASC', ARRAY_A);
		$mapping_unit = array(
	        Field::make( 'radio', 'crb_singkron_simda', __( 'Auto Singkron ke DB SIMDA' ) )
			    ->add_options( array(
			        '1' => __( 'Ya' ),
			        '2' => __( 'Tidak' )
			    ) )
            	->set_default_value('2')
            	->set_help_text('Data SIMDA akan terupdate otomatis ketika melakukan singkron DB Lokal menggunakan chrome extension.'),
            Field::make( 'text', 'crb_url_api_simda', 'URL API SIMDA' )
            	->set_help_text('Scirpt PHP SIMDA API dibuat terpisah di <a href="https://github.com/agusnurwanto/SIMDA-API-PHP" target="_blank">SIMDA API PHP</a>.'),
            Field::make( 'text', 'crb_timeout_simda', 'MAX TIMEOUT API SIMDA' )
            	->set_default_value(10)
            	->set_help_text('Setting maksimal timout request CURL ke API SIMDA dalam hitungan detik.'),
            Field::make( 'text', 'crb_apikey_simda', 'APIKEY SIMDA' )
            	->set_default_value($this->generateRandomString()),
            Field::make( 'text', 'crb_db_simda', 'Database SIMDA' ),
	        Field::make( 'radio', 'crb_singkron_simda_debug', __( 'Debug API integrasi SIMDA' ) )
			    ->add_options( array(
			        '1' => __( 'Ya' ),
			        '2' => __( 'Tidak' )
			    ) )
            	->set_default_value('2')
            	->set_help_text('Debug API SIMDA agar notif error muncul di respon API wordpress. Untuk melakukan debug extension buka <b>halaman extension > background page > network</b>.'),
	        Field::make( 'radio', 'crb_singkron_simda_unit', __( 'Integrasi Sub Unit SIMDA sesuai SIPD' ) )
			    ->add_options( array(
			        '1' => __( 'Ya' ),
			        '2' => __( 'Tidak' )
			    ) )
            	->set_default_value('2')
            	->set_help_text('Jika sub unit simda (ref_unit dan ref_sub_unit) belum dibuat maka data akan diisi otomatis ketika melakukan integrasi perangkat daerah. Dan tanpa merubah yang sudah diisi.'),
	        Field::make( 'radio', 'crb_auto_ref_kegiatan_mapping', __( 'Otomatis insert ke ref_kegiatan dan ref_kegiatan_mapping jika tidak ada di SIMDA' ) )
			    ->add_options( array(
			        '1' => __( 'Ya' ),
			        '2' => __( 'Tidak' )
			    ) )
            	->set_default_value('2')
            	->set_help_text('Jika data di ref_program belum ada, juga akan diinput otomatis dengan kode default <b>kd_prog >= 150 dan kd_keg >= 150</b> sebagai tanda auto create by WP-SIPD.'),
	        Field::make( 'radio', 'crb_auto_ref_rek_mapping', __( 'Otomatis insert ke ref_rek_1, ref_rek_2, ref_rek_3, ref_rek_4, ref_rek_5 dan ref_rek_mapping jika tidak ada di SIMDA' ) )
			    ->add_options( array(
			        '1' => __( 'Ya' ),
			        '2' => __( 'Tidak' )
			    ) )
            	->set_default_value('2')
            	->set_help_text('Kode default <b>kd_rek_3 += 100, kd_rek_4 += 100 dan kd_rek_5 += 100</b> sebagai tanda auto create by WP-SIPD.'),
	        Field::make( 'radio', 'crb_simda_pagu', __( 'Nilai Rincian yang dikirim ke SIMDA' ) )
			    ->add_options( array(
			        '1' => __( 'Nilai Terakhir' ),
			        '2' => __( 'Sebelum Perubahan' )
			    ) )
            	->set_default_value('1')
            	->set_help_text('Pilihan ini untuk dipakai saat jadwal pergeseran atau perubahan. Jika masih jadwal APBD Murni maka pilih <b>Nilai Terakhir</b>.'),
            Field::make( 'textarea', 'crb_custom_mapping_sub_keg_simda', 'Custom Mapping Sub Kegiatan SIPD ke SIMDA' )
            	->set_help_text('Data ini untuk mengakomodir perbedaan kode sub kegiatan yang ada di SIPD dan SIMDA. Juga perbedaan mapping sub kegiatan ke Sub Unit SIMDA. Contoh pengisian data sebagai berikut 5.02.0.00.0.00.04.0000_5.02.02.2.01.05-4.04.01.02_4.04.18.08 data dipisah dengan pemisah "," (koma). Formatnya adalah <b>kodeSkpdSipd_kodeSubKeg-kodeSubUnitSimda_kodeRefKegiatan</b>.')
	    );

	    $cek_status_koneksi_simda = $this->simda->CurlSimda(array(
			'query' => 'select * from ref_setting',
			'no_debug' => true
		));
		$ket_simda = '<b style="color:red">Belum terkoneksi ke simda!</b>';
		if(!empty($cek_status_koneksi_simda[0]) && !empty($cek_status_koneksi_simda[0]->lastdbaplver)){
			$ket_simda = '<b style="color: green">Terkoneksi database SIMDA versi '.$cek_status_koneksi_simda[0]->lastdbaplver.'</b>';
		}
		$mapping_unit[] = Field::make( 'html', 'crb_status_simda' )
	            	->set_html( 'Status koneksi SQL server SIMDA: '.$ket_simda );
		
		$mapping_unit[] = Field::make( 'html', 'crb_mapping_unit_simda' )
	            	->set_html( 'Mapping kode sub unit SIPD ke SIMDA. Format kode (kd_urusan.kd_bidang.kd_unit.kd_sub) dipisah dengan titik. Contoh untuk Dinas Pendidikan (1.1.1.1). <b>Setelah melakukan mapping kode unit secara manual maka HARUS DILAKUKAN SINGKRON ULANG PERANGKAT DAERAH agar data di ta_sub_unit terisi semua!</b>.' );
		foreach ($unit as $k => $v) {
			$unit_simda = get_option('_crb_unit_'.$v['id_skpd']);
			if(empty($unit_simda)){
				$kd = explode('.', $v['kode_skpd']);
				$default_val = $this->simda->CurlSimda(array(
					'query' => 'select * from ref_bidang_mapping where kd_urusan90='.$kd[0].' and kd_bidang90='.((int)$kd[1])
				));
				$default = $default_val[0]->kd_urusan.'.'.$default_val[0]->kd_bidang;
				update_option( '_crb_unit_'.$v['id_skpd'], $default );
			}
			$mapping_unit[] = Field::make( 'text', 'crb_unit_'.$v['id_skpd'], ($k+1).'. Kode Sub Unit SIMDA untuk '.$v['kode_skpd'].' '.$v['nama_skpd'] );
		}
		return $mapping_unit;
	}

	// hook filter untuk save field carbon field
	public function crb_edit_save($save, $value, $field){
		if($field->get_name() == '_crb_label_komponen'){
			return "";
		}else{
			return $value;
		}
	}

	public function generate_sumber_dana(){
		global $wpdb;
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran order by tahun_anggaran ASC', ARRAY_A);
		$options_tahun = array();
		$tahun_skr = date('Y');
		foreach ($tahun as $k => $v) {
			$selected = '';
			if($tahun_skr == $v['tahun_anggaran']){
				$selected = 'selected';
			}
			$options_tahun[] = '<option '.$selected.' value="'.$v['tahun_anggaran'].'">'.$v['tahun_anggaran'].'</option>';
		}
		$label = array(
			Field::make( 'html', 'crb_daftar_label_komponen' )
            	->set_html( '
            		<style>
            			.postbox-container { display: none; }
            			#poststuff #post-body.columns-2 { margin: 0 !important; }
            		</style>
            		<div class="text_tengah" style="margin-bottom: 1em;">
	            		<h3>Daftar Sumber Dana</h3>
	            		<select style="margin-bottom: 15px; width: 200px;" id="pilih_tahun" onchange="format_sumberdana();">
	            			<option value="0">Pilih Tahun</option>
	            			'.implode('', $options_tahun).'
	            		</select>
	            		<select style="margin-bottom: 15px; margin-left: 25px; min-width: 200px;" id="pilih_skpd" onchange="format_sumberdana();">
	            			<option value="0">Semua SKPD</option>
	            		</select>
	            		<br>
	            		<label><input type="radio" checked name="format-sd" format-id="1" onclick="format_sumberdana();"> Format Per Sumber Dana SIPD</label>
	            		<label style="margin-left: 25px;"><input type="radio" name="format-sd" format-id="3" onclick="format_sumberdana();"> Format Kombinasi Sumber Dana SIPD</label>
	            		<label style="margin-left: 25px;"><input type="radio" name="format-sd" format-id="2" onclick="format_sumberdana();"> Format Per Sumber Dana Mapping</label>
	            	</div>
	            	<div id="tabel_monev_sumber_dana">
	            	</div>
        		' )
        );

        // walau tidak ditampilkan tapi code dibawah ini adalah untuk menggenerate page sumber dana per skpd
        $label = array_merge($label, $this->get_ajax_field(array('type' => 'sumber_dana')));
        return $label;
	}

	public function get_list_skpd(){
		global $wpdb;
		$tahun = $_POST['tahun_anggaran'];
		$options_skpd = array();
		$unit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd, nipkepala from data_unit where active=1 and tahun_anggaran=".$tahun.' and is_skpd=1 order by kode_skpd ASC', ARRAY_A);
        foreach ($unit as $kk => $vv) {
			$options_skpd[] = $vv;
        	$subunit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd, nipkepala from data_unit where active=1 and tahun_anggaran=".$tahun." and is_skpd=0 and id_unit=".$vv["id_skpd"]." order by kode_skpd ASC", ARRAY_A);
        	foreach ($subunit as $kkk => $vvv) {
        		$vvv['kode_skpd'] = '-- '.$vvv['kode_skpd'];
				$options_skpd[] = $vvv;
        	}
        }
        die(json_encode($options_skpd));
	}

	public function generate_sumber_dana_format(){
		global $wpdb;
		$format = $_POST['format'];
		$tahun = $_POST['tahun_anggaran'];
		$id_skpd = $_POST['id_skpd'];

		// sumber dana asli sipd
		if($format == 1)
		{
			if(!empty($id_skpd)){
				$sumberdana = $wpdb->get_results($wpdb->prepare('
					select 
						d.iddana, 
						sum(d.pagudana) as pagudana, 
						d.kodedana, 
						count(s.kode_sbl) as jml,
						d.namadana
					from data_dana_sub_keg d
						INNER JOIN data_sub_keg_bl s ON d.kode_sbl=s.kode_sbl
							AND s.tahun_anggaran=d.tahun_anggaran
							AND s.active=d.active
					where d.tahun_anggaran=%d
						and s.id_sub_skpd=%d
						and d.active=1
					group by d.iddana
					order by d.kodedana ASC
				', $tahun, $id_skpd), ARRAY_A);
			}else{
				$sumberdana = $wpdb->get_results($wpdb->prepare('
					select 
						iddana,
						sum(pagudana) as pagudana,
						kodedana,
						count(kodedana) as jml,
						namadana 
					from data_dana_sub_keg 
					where tahun_anggaran=%d
						and active=1
					group by iddana
					order by kodedana ASC
				', $tahun), ARRAY_A);
			}
			$no = 0;
			$total_sd = 0;
			foreach ($sumberdana as $key => $val) {
				$no++;
				$title = 'Laporan APBD Per Sumber Dana '.$val['kodedana'].' '.$val['namadana'].' | '.$tahun;
				$shortcode = '[monitor_sumber_dana tahun_anggaran="'.$tahun.'" id_sumber_dana="'.$val['iddana'].'"]';
				$update = false;
				$url_skpd = $this->generatePage($title, $tahun, $shortcode, $update);
				if(!empty($id_skpd)){
					$url_skpd .= '&id_skpd='.$id_skpd;
				}
				if(empty($val['kodedana'])){
					$val['kodedana'] = '';
					$val['namadana'] = 'Belum Di Setting';
				}
				$master_sumberdana .= '
					<tr>
						<td class="text_tengah">'.$no.'</td>
						<td>'.$val['kodedana'].'</td>
						<td><a href="'.$url_skpd.'&mapping=1" target="_blank">'.$val['namadana'].'</a></td>
						<td class="text_kanan">'.number_format($val['pagudana'],0,",",".").'</td>
						<td class="text_tengah">'.$val['jml'].'</td>
						<td class="text_tengah">'.$val['iddana'].'</td>
						<td class="text_tengah">'.$tahun.'</td>
					</tr>
				';
				$total_sd += $val['pagudana'];
			}
			if(!empty($id_skpd)){
				$total_rka = $wpdb->get_results($wpdb->prepare('
					select 
						sum(pagu) as total_rka
					from data_sub_keg_bl 
					where tahun_anggaran=%d
						and id_sub_skpd=%d
						and active=1
				', $tahun, $id_skpd), ARRAY_A);
				$skpd = $wpdb->get_results($wpdb->prepare("
					SELECT 
						nama_skpd, 
						id_skpd, 
						kode_skpd
					from data_unit 
					where active=1 
						and tahun_anggaran=%d
						and id_skpd=%d
					order by kode_skpd ASC
				", $tahun, $id_skpd), ARRAY_A);

				$title = 'RFK '.$skpd[0]['nama_skpd'].' '.$skpd[0]['kode_skpd'].' | '.$tahun;
				$shortcode = '[monitor_rfk tahun_anggaran="'.$tahun.'" id_skpd="'.$skpd[0]['id_skpd'].'"]';
				$update = false;
				$url_skpd = $this->generatePage($title, $tahun, $shortcodee, $update);
			}else{
				$total_rka = $wpdb->get_results($wpdb->prepare('
					select 
						sum(pagu) as total_rka
					from data_sub_keg_bl 
					where tahun_anggaran=%d
						and active=1
				', $tahun), ARRAY_A);
				$title = 'Realisasi Fisik dan Keuangan Pemerintah Daerah | '.$tahun;
				$shortcode = '[monitor_rfk tahun_anggaran="'.$tahun.'"]';
				$update = false;
				$url_skpd = $this->generatePage($title, $tahun, $shortcodee, $update);
			}
			$master_sumberdana .= '
				<tr class="text_blok">
					<td class="text_tengah" colspan="3">Total Pagu Sumber Dana Tahun '.$tahun.'</td>
					<td class="text_kanan">'.number_format($total_sd,0,",",".").'</td>
					<td class="text_tengah" colspan="2">Total RKA</td>
					<td class="text_tengah"><a target="_blank" href="'.$url_skpd.'">'.number_format($total_rka[0]['total_rka'],0,",",".").'</a></td>
				</tr>
			';
			$tabel = '
        		<table class="wp-list-table widefat fixed striped">
        			<thead>
        				<tr class="text_tengah">
        					<th class="text_tengah" style="width: 20px">No</th>
        					<th class="text_tengah" style="width: 100px">Kode</th>
        					<th class="text_tengah">Sumber Dana</th>
        					<th class="text_tengah" style="width: 150px">Pagu Sumber Dana (Rp.)</th>
        					<th class="text_tengah" style="width: 150px">Jumlah Sub Kegiatan</th>
        					<th class="text_tengah" style="width: 50px">ID Dana</th>
        					<th class="text_tengah" style="width: 110px">Tahun Anggaran</th>
        				</tr>
        			</thead>
        			<tbody>
        				'.$master_sumberdana.'
        			</tbody>
        		</table>
    		';
	        die($tabel);
	    }
	    // sumber dana mapping
	    else if($format == 2)
	    {
	    	$data_all = array();
	    	$total_harga = 0;
	    	$realisasi = 0;
	    	$jml_rincian = 0;
	    	if(!empty($id_skpd)){
	    		$sub_keg = $wpdb->get_results($wpdb->prepare('
	    			select
	    				kode_sbl
	    			from data_sub_keg_bl
	    			where tahun_anggaran=%d
	    				and active=1
	    				and id_sub_skpd=%d
	    		', $tahun, $id_skpd), ARRAY_A);
	    		$list_kode_sbl = array();
	    		foreach ($sub_keg as $k => $sub) {
	    			$list_kode_sbl[] = "'".$sub['kode_sbl']."'";
	    		}
	    		$rka_db = $wpdb->get_results($wpdb->prepare('
	    			select
	    				r.id_rinci_sub_bl,
	    				r.total_harga,
	    				d.realisasi
	    			from data_rka r
	    				left join data_realisasi_rincian d ON r.id_rinci_sub_bl=d.id_rinci_sub_bl
	    					and d.tahun_anggaran=r.tahun_anggaran
	    					and d.active=r.active
	    			where r.tahun_anggaran=%d
	    				and r.active=1
	    				and r.kode_sbl IN ('.implode(',', $list_kode_sbl).')
	    		', $tahun), ARRAY_A);
	    	}else{
	    		$rka_db = $wpdb->get_results($wpdb->prepare('
	    			select
	    				r.id_rinci_sub_bl,
	    				r.total_harga,
	    				d.realisasi
	    			from data_rka r
	    				left join data_realisasi_rincian d ON r.id_rinci_sub_bl=d.id_rinci_sub_bl
	    					and d.tahun_anggaran=r.tahun_anggaran
	    					and d.active=r.active
	    			where r.tahun_anggaran=%d
	    				and r.active=1
	    		', $tahun), ARRAY_A);
	    	}
    		foreach ($rka_db as $rka) {
    			if(empty($rka['realisasi'])){
    				$rka['realisasi'] = 0;
    			}
    			$mapping_db = $wpdb->get_results($wpdb->prepare('
    				select 
    					d.kode_dana,
    					d.nama_dana,
    					d.id_dana
    				from data_mapping_sumberdana s
    					inner join data_sumber_dana d ON s.id_sumber_dana=d.id_dana
    						and d.tahun_anggaran=s.tahun_anggaran
    				where s.tahun_anggaran=%d
    					and s.active=1
    					and s.id_rinci_sub_bl=%d
    			', $tahun, $rka['id_rinci_sub_bl']), ARRAY_A);
    			if(!empty($mapping_db)){
	    			foreach ($mapping_db as $mapping) {
	    				if(empty($data_all[$mapping['kode_dana']])){
	    					$data_all[$mapping['kode_dana']] = array(
	    						'id_dana' => $mapping['id_dana'],
	    						'kode_dana' => $mapping['kode_dana'],
	    						'nama_dana' => $mapping['nama_dana'],
	    						'jml_rincian' => 0,
	    						'pagu' => 0,
	    						'realisasi' => 0
		    				);
	    				}
		    			$data_all[$mapping['kode_dana']]['jml_rincian']++;
		    			$data_all[$mapping['kode_dana']]['pagu'] += $rka['total_harga'];
		    			$data_all[$mapping['kode_dana']]['realisasi'] += $rka['realisasi'];
	    			}
	    		}else{
	    			if(empty($data_all['0'])){
		    			$data_all['0'] = array(
    						'id_dana' => '',
    						'kode_dana' => '-',
    						'nama_dana' => 'Belum di mapping!',
    						'jml_rincian' => 0,
    						'pagu' => 0,
    						'realisasi' => 0
	    				);
		    		}
		    		$data_all['0']['jml_rincian']++;
		    		$data_all['0']['pagu'] += $rka['total_harga'];
		    		$data_all['0']['realisasi'] += $rka['realisasi'];
	    		}
	    		$total_harga += $rka['total_harga'];
	    		$realisasi += $rka['realisasi'];
	    		$jml_rincian++;
    		}
    		ksort($data_all);
	    	$master_sumberdana = '';
	    	$no = 0;
	    	foreach ($data_all as $k => $v) {
	    		$no++;
	    		if(empty($v['id_dana'])){
					$title = 'Laporan APBD Per Sumber Dana   | '.$tahun;
	    		}else{
					$title = 'Laporan APBD Per Sumber Dana '.$v['kode_dana'].' '.$v['nama_dana'].' | '.$tahun;
	    		}
				$shortcode = '[monitor_sumber_dana tahun_anggaran="'.$tahun.'" id_sumber_dana="'.$v['id_dana'].'"]';
				$update = false;
				$url_skpd = $this->generatePage($title, $tahun, $shortcode, $update).'&mapping=2';
				if(!empty($id_skpd)){
					$url_skpd .= '&id_skpd='.$id_skpd;
				}
	    		$master_sumberdana .= '
	    			<tr>
	    				<td class="text_tengah">'.$no.'</td>
	    				<td>'.$v['kode_dana'].'</td>
	    				<td><a href="'.$url_skpd.'" target="_blank">'.$v['nama_dana'].'</a></td>
	    				<td class="text_kanan">'.number_format($v['pagu'],0,",",".").'</td>
	    				<td class="text_kanan">'.number_format($v['realisasi'],0,",",".").'</td>
	    				<td class="text_tengah">'.number_format($v['jml_rincian'],0,",",".").'</td>
	    			</tr>
	    		';
	    	}
	    	$tabel = '
        		<table class="wp-list-table widefat fixed striped">
        			<thead>
        				<tr>
        					<th colspan="6" class="text_tengah">
        						<a class="button-primary" id="dpa_simda-to-wp_sipd" onclick="return false;" href="#" style="display: none;">Singkronisasi Sumber Dana dari DPA SIMDA ke WP-SIPD</a>
        						<a class="button" id="sipd_lokal-to-wp_sipd" onclick="return false;" href="#">Singkronisasi Sumber Dana dari DB SIPD Lokal ke WP-SIPD</a>
        						<a class="button-primary" id="wp_sipd-to-rka_simda" onclick="return false;" href="#">Singkronisasi Sumber Dana dan RKA dari WP-SIPD ke RKA SIMDA</a>
        					</th>
        				</tr>
        				<tr class="text_tengah">
        					<th class="text_tengah" style="width: 20px">No</th>
        					<th class="text_tengah" style="width: 100px">Kode</th>
        					<th class="text_tengah">Sumber Dana</th>
        					<th class="text_tengah" style="width: 150px">Pagu Sumber Dana (Rp.)</th>
        					<th class="text_tengah" style="width: 150px">Realisasi Rincian</th>
        					<th class="text_tengah" style="width: 150px">Jumlah Rincian</th>
        				</tr>
        			</thead>
        			<tbody>
        				'.$master_sumberdana.'
        				<tr class="text_blok">
		    				<td class="text_tengah" colspan="3">Total</td>
		    				<td class="text_kanan">'.number_format($total_harga,0,",",".").'</td>
		    				<td class="text_kanan">'.number_format($realisasi,0,",",".").'</td>
		    				<td class="text_tengah">'.number_format($jml_rincian,0,",",".").'</td>
		    			</tr>
        			</tbody>
        		</table>
    		';
	        die($tabel);
	    }
	    // sumber dana kombinasi
	    else if($format == 3)
	    {
	    	if(!empty($id_skpd)){
		    	$sub_keg = $wpdb->get_results($wpdb->prepare('
	    			select
	    				kode_sbl,
	    				pagu,
	    				pagu_simda
	    			from data_sub_keg_bl
	    			where tahun_anggaran=%d
	    				and active=1
	    				and id_sub_skpd=%d
	    		', $tahun, $id_skpd), ARRAY_A);
		    }else{
		    	$sub_keg = $wpdb->get_results($wpdb->prepare('
	    			select
	    				kode_sbl,
	    				pagu,
	    				pagu_simda
	    			from data_sub_keg_bl
	    			where tahun_anggaran=%d
	    				and active=1
	    		', $tahun), ARRAY_A);
		    }
		    $data_all = array();
		    foreach ($sub_keg as $k => $sub) {
			    $sumberdana = $wpdb->get_results($wpdb->prepare('
					select 
						d.iddana, 
						sum(d.pagudana) as pagudana, 
						d.kodedana, 
						d.namadana
					from data_dana_sub_keg d
					where d.tahun_anggaran=%d
						and d.active=1
						and d.kode_sbl=%s
					group by d.iddana
					order by d.kodedana ASC
				', $tahun, $sub['kode_sbl']), ARRAY_A);
				$id_dana = array();
				$pagu_dana_raw = array();
				$format_pagu_dana = array();
				$kode_sd = array();
				$nama_sd = array();
				foreach ($sumberdana as $dana) {
					$id_dana[] = $dana['iddana'];
					$pagu_dana_raw[$dana['iddana']] = $dana['pagudana'];
					$kode_sd[] = $dana['kodedana'];
					$nama_sd[] = $dana['namadana'];
				}
				$_id_dana = implode('<br>', $id_dana);
				$_kode_sd = implode('<br>', $kode_sd);
				$_nama_sd = implode('<br>', $nama_sd);
				$_kombinasi_id_sd = implode(',', $id_dana);
				$_kombinasi_kode_sd = implode('|', $kode_sd);
				$_kombinasi_nama_sd = implode('|', $nama_sd);
				if(empty($data_all[$_kode_sd])){
					$title = 'Laporan APBD Per Sumber Dana '.$_kombinasi_kode_sd.' '.$_kombinasi_nama_sd.' | '.$tahun;
					$shortcode = '[monitor_sumber_dana tahun_anggaran="'.$tahun.'" id_sumber_dana="'.$_kombinasi_id_sd.'"]';
					$update = false;
					$url_skpd = $this->generatePage($title, $tahun, $shortcode, $update);
					if(!empty($id_skpd)){
						$url_skpd .= "&id_skpd=".$id_skpd."&mapping=3";
					}else{
						$url_skpd .= "&mapping=3";
					}
					$data_all[$_kode_sd] = array(
						'title' => $title,
						'url_page' => $url_skpd,
						'id_dana' => $_id_dana,
						'kode_sd' => $_kode_sd,
						'nama_sd' => $_nama_sd,
						'raw_id_dana' => $id_dana,
						'raw_pagu_dana' => array(),
						'raw_kode_sd' => $kode_sd,
						'raw_nama_sd' => $nama_sd,
						'pagu_rka' => 0,
						'pagu_simda' => 0,
						'jml_sub' => 0
					);
				}
				if(empty($data_all[$_kode_sd]['raw_pagu_dana'][$sub['kode_sbl']])){
					$data_all[$_kode_sd]['raw_pagu_dana'][$sub['kode_sbl']] = $pagu_dana_raw;
				}
				$data_all[$_kode_sd]['pagu_rka'] += $sub['pagu'];
				$data_all[$_kode_sd]['pagu_simda'] += $sub['pagu_simda'];
				$data_all[$_kode_sd]['jml_sub']++;
		    }

		    $master_sumberdana = '';
		    $no = 0;
		    $jml_sub = 0;
		    $total_rka = 0;
		    $total_sd = 0;
		    ksort($data_all);
		    foreach ($data_all as $k => $val) {
		    	$no++;
		    	$url_skpd = '#';
		    	$pagu_dana = array();
				foreach ($val['raw_pagu_dana'] as $sub) {
					foreach ($sub as $iddana => $dana) {
						if(empty($pagu_dana[$iddana])){
							$pagu_dana[$iddana] = 0;
						}
						$pagu_dana[$iddana] += $dana;
						$total_sd += $dana;
					}
				}
				foreach ($pagu_dana as $iddana => $pagu) {
					$pagu_dana[$iddana] = number_format($pagu,0,",",".");
				}
		    	$master_sumberdana .= '
					<tr>
						<td class="text_tengah">'.$no.'</td>
						<td>'.$val['kode_sd'].'</td>
						<td><a href="'.$val['url_page'].'" data-title="'.$val['title'].'" target="_blank">'.$val['nama_sd'].'</a></td>
						<td class="text_kanan">'.implode('<br>', $pagu_dana).'</td>
						<td class="text_kanan">'.number_format($val['pagu_rka'],0,",",".").'</td>
						<td class="text_tengah">'.$val['jml_sub'].'</td>
					</tr>
				';
				$jml_sub += $val['jml_sub'];
				$total_rka += $val['pagu_rka'];
		    }
		    $title = 'Realisasi Fisik dan Keuangan Pemerintah Daerah | '.$tahun;
			$shortcode = '[monitor_rfk tahun_anggaran="'.$tahun.'"]';
			$update = false;
			$url_skpd = $this->generatePage($title, $tahun, $shortcode, $update);
			$master_sumberdana .= '
				<tr class="text_blok">
					<td class="text_tengah" colspan="3">Total</td>
					<td class="text_kanan">'.number_format($total_sd,0,",",".").'</td>
					<td class="text_kanan"><a target="_blank" href="'.$url_skpd.'">'.number_format($total_rka,0,",",".").'</a></td>
					<td class="text_tengah">'.number_format($jml_sub,0,",",".").'</td>
				</tr>
			';
			$tabel = '
        		<table class="wp-list-table widefat fixed striped">
        			<thead>
        				<tr class="text_tengah">
        					<th class="text_tengah" style="width: 20px">No</th>
        					<th class="text_tengah" style="width: 100px">Kode</th>
        					<th class="text_tengah">Sumber Dana</th>
        					<th class="text_tengah" style="width: 150px">Pagu Sumber Dana (Rp.)</th>
        					<th class="text_tengah" style="width: 150px">Pagu RKA (Rp.)</th>
        					<th class="text_tengah" style="width: 150px">Jumlah Sub Keg</th>
        				</tr>
        			</thead>
        			<tbody>
        				'.$master_sumberdana.'
        			</tbody>
        		</table>
    		';
	        die($tabel);
	    }
	    
	}

	public function generate_tag_sipd(){
		global $wpdb;
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran', ARRAY_A);
		$master_tag = '';
		$no = 0;
		foreach ($tahun as $k => $v) {
			$no++;
			$nama_page = 'Mandatory Spending | '.$v['tahun_anggaran'];
			$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
			$master_tag .= '
				<tr>
					<td class="text_tengah">'.$no.'</td>
					<td><a href="'.get_permalink($custom_post).'" target="_blank">Semua Label di tahun '.$v['tahun_anggaran'].'</a></td>
					<td class="text_tengah">'.$v['tahun_anggaran'].'</td>
				</tr>
			';
			$label_tag = $wpdb->get_results('
				select 
					idlabelgiat,
					namalabel
				from data_tag_sub_keg 
				where tahun_anggaran='.$v['tahun_anggaran'].'
					and active=1
					and idlabelgiat!=0
				group by idlabelgiat
				order by idlabelgiat ASC
			', ARRAY_A);
			foreach ($label_tag as $key => $val) {
				$no++;
				$title = 'Laporan APBD Per Tag/Label Sub Kegiatan '.$val['namalabel'].' | '.$v['tahun_anggaran'];
				$shortcode = '[apbdpenjabaran tahun_anggaran="'.$v['tahun_anggaran'].'" lampiran=99 idlabelgiat="'.$val['idlabelgiat'].'"]';
				$update = false;
				$url_tag = $this->generatePage($title, $v['tahun_anggaran'], $shortcode, $update);
				$master_tag .= '
					<tr data-idlabelgiat="'.$val['idlabelgiat'].'">
						<td class="text_tengah">'.$no.'</td>
						<td><a href="'.$url_tag.'" target="_blank" style="padding-left: 20px;">'.$val['namalabel'].'</a></td>
						<td class="text_tengah">'.$v['tahun_anggaran'].'</td>
					</tr>
				';
			}
		}
		$label = array(
			Field::make( 'html', 'crb_daftar_tag_label_sub_kegiatan' )
            	->set_html( '
            		<style>
            			.postbox-container { display: none; }
            			#poststuff #post-body.columns-2 { margin: 0 !important; }
            		</style>
            		<h3 class="text_tengah">Daftar Tag/Label Sub Kegiatan</h3>
            		<table class="wp-list-table widefat fixed striped">
            			<thead>
            				<tr class="text_tengah">
            					<th class="text_tengah" style="width: 20px">No</th>
            					<th class="text_tengah">Nama Tag/Label Sub Kegiatan</th>
            					<th class="text_tengah" style="width: 140px">Tahun Anggaran</th>
            				</tr>
            			</thead>
            			<tbody>
            				'.$master_tag.'
            			</tbody>
            		</table>
        		' )
        );
        return $label;
	}

	public function generate_label_komponen(){
		global $wpdb;
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran', ARRAY_A);
		$tahun_anggaran = array();
		foreach ($tahun as $k => $v) {
			$tahun_anggaran[$v['tahun_anggaran']] = $v['tahun_anggaran'];
		}
		$label = array(
			Field::make( 'select', 'crb_tahun_anggaran', __( 'Pilih Tahun Anggaran' ) )
			    ->add_options( $tahun_anggaran ),
			Field::make( 'html', 'crb_daftar_label_komponen' )
            	->set_html( '
            		<style>
            			.postbox-container { display: none; }
            			#poststuff #post-body.columns-2 { margin: 0 !important; }
            		</style>
            		<h3 class="text_tengah">Form Tambah dan Edit Label Komponen</h3>
            		<table class="wp-list-table widefat fixed striped">
            			<thead>
            				<tr>
            					<th class="text_tengah" style="width: 300px">Nama</th>
            					<th class="text_tengah">Keterangan</th>
            					<th class="text_tengah" style="width: 170px">Aksi</th>
            				</tr>
            			</thead>
            			<tbody>
            				<tr>
            					<td>
            						<input class="cf-text__input" type="text" id="nama_label">
            						<input type="hidden" id="id_label">
            					</td>
            					<td><textarea class="cf-text__input" type="text" id="keterangan_label"></textarea></td>
            					<td class="text_tengah"><button id="tambah_label_komponen" class="button button-primary" onclick="return false;">Simpan Label Komponen</button></td>
            				</tr>
            			</tbody>
            		</table>
            		<hr style="margin: 40px 0 0;">
            		<h3 class="text_tengah">Daftar Label Komponen</h3>
            		<table class="wp-list-table widefat fixed striped">
            			<thead>
            				<tr>
            					<th class="text_tengah text_blok" rowspan="2" style="width: 20px">No</th>
            					<th class="text_tengah text_blok" rowspan="2" style="width: 250px">Nama Label</th>
            					<th class="text_tengah text_blok" rowspan="2">Keterangan</th>
            					<th class="text_tengah text_blok" colspan="3" style="width: 400px;">Analisa Rincian <span style="" data-id="analis-rincian" id="analisa_komponen" class="edit-label"><i class="dashicons dashicons-controls-repeat"></i></span></th>
            					<th class="text_tengah text_blok" rowspan="2" style="width: 70px">Aksi</th>
            				</tr>
            				<tr>
            					<th class="text_tengah text_blok" style="width: 140px">Pagu Rincian</th>
            					<th class="text_tengah text_blok" style="width: 140px">Realisasi</th>
            					<th class="text_tengah text_blok">Jumlah Rincian</th>
            				</tr>
            			</thead>
            			<tbody id="body_label">
            			</tbody>
            		</table>
            		<h3>Dokumentasi:</h3>
            		<ul style="margin-left: 30px;" id="dokumentasi">
            			<li><b>Tahun Anggaran</b> untuk menampilkan data label komponen dalam tahun anggaran tersebut</li>
            			<li><b>Form Tambah dan Edit Label Komponen</b> digunakan untuk menambahkan label komponen baru atau melakukan update label komponen</li>
            			<li><b>Daftar Label Komponen</b> menampilkan daftar label komponen yang sudah dibuat</li>
            			<li>Tombol refresh berwarna biru pada kolom <b>Analisa Rincian</b> berfungsi untuk menampilkan data pagu, realisasi dan jumlah rincian</li>
            		</ul>
        		' )
        );
        $label = array_merge($label, $this->get_ajax_field(array('type' => 'label_komponen')));
        return $label;
	}

	public function generate_siencang_page(){
		$nama_page = 'SIPD to SIENCANG';
		$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
		// print_r($custom_post); die();

		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post(array(
				'post_title'	=> $nama_page,
				'post_content'	=> '[tampilsiencang]',
				'post_type'		=> 'page',
				'post_status'	=> 'publish'
			));
			$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
			update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
			update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
			update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
			update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
			update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
			update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
			update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
			update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
		}
		return get_permalink($custom_post->ID);
	}

	public function generate_simda_page(){
		$nama_page = 'SIPD to SIMDA BPKP';
		$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
		// print_r($custom_post); die();

		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post(array(
				'post_title'	=> $nama_page,
				'post_content'	=> '[tampilsimda]',
				'post_type'		=> 'page',
				'post_status'	=> 'publish'
			));
			$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
			update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
			update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
			update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
			update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
			update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
			update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
			update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
			update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
		}
		return get_permalink($custom_post->ID);
	}

	public function generate_sibangda_page(){
		$nama_page = 'SIPD to SIBANGDA Bina Pembangunan Kemendagri';
		$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
		// print_r($custom_post); die();

		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post(array(
				'post_title'	=> $nama_page,
				'post_content'	=> '[tampilsibangda]',
				'post_type'		=> 'page',
				'post_status'	=> 'publish'
			));
			$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
			update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
			update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
			update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
			update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
			update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
			update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
			update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
			update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
		}
		return get_permalink($custom_post->ID);
	}

	public function generate_sinergi_page(){
		$nama_page = 'SIPD to SINERGI DJPK Kementrian Keuangan';
		$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
		// print_r($custom_post); die();

		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post(array(
				'post_title'	=> $nama_page,
				'post_content'	=> '[tampilsinergi]',
				'post_type'		=> 'page',
				'post_status'	=> 'publish'
			));
			$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
			update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
			update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
			update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
			update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
			update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
			update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
			update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
			update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
		}
		return get_permalink($custom_post->ID);
	}

	public function generate_sirup_page(){
		$nama_page = 'SIPD to SIRUP LKPP';
		$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
		// print_r($custom_post); die();

		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post(array(
				'post_title'	=> $nama_page,
				'post_content'	=> '[tampilsirup]',
				'post_type'		=> 'page',
				'post_status'	=> 'publish'
			));
			$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
			update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
			update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
			update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
			update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
			update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
			update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
			update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
			update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
		}
		return get_permalink($custom_post->ID);
	}

    function allow_access_private_post(){
    	if(
    		!empty($_GET) 
    		&& !empty($_GET['key'])
    	){
    		$key = base64_decode($_GET['key']);
    		$key_db = md5(get_option( '_crb_api_key_extension' ));
    		$key = explode($key_db, $key);
    		$valid = 0;
    		if(
    			!empty($key[1]) 
    			&& $key[0] == $key[1]
    			&& is_numeric($key[1])
    		){
    			$tgl1 = new DateTime();
    			$date = substr($key[1], 0, strlen($key[1])-3);
    			$tgl2 = new DateTime(date('Y-m-d', $date));
    			$valid = $tgl2->diff($tgl1)->days+1;
    		}
    		if($valid == 1){
	    		global $wp_query;
		        // print_r($wp_query);
		        // print_r($wp_query->queried_object); die('tes');
		        if(!empty($wp_query->queried_object)){
		    		if($wp_query->queried_object->post_status == 'private'){
						wp_update_post(array(
					        'ID'    =>  $wp_query->queried_object->ID,
					        'post_status'   =>  'publish'
				        ));
				        die('<script>window.location =  window.location.href;</script>');
					}else{
						wp_update_post(array(
					        'ID'    =>  $wp_query->queried_object->ID,
					        'post_status'   =>  'private'
				        ));
					}
				}else if($wp_query->found_posts >= 1){
					global $wpdb;
					$sql = $wp_query->request;
					$post = $wpdb->get_results($sql, ARRAY_A);
					if(!empty($post)){
						if($post[0]['post_status'] == 'private'){
							wp_update_post(array(
						        'ID'    =>  $post[0]['ID'],
						        'post_status'   =>  'publish'
					        ));
					        die('<script>window.location =  window.location.href;</script>');
						}else{
							wp_update_post(array(
						        'ID'    =>  $post[0]['ID'],
						        'post_status'   =>  'private'
					        ));
						}
					}
				}
			}
    	}
    }

    function get_label_komponen(){
    	global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> '<tr><td colspan="4" style="text-align: center;">Data Label Komponen kosong</td></tr>',
			'data'		=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension' )) {
				$data_label_komponen = $wpdb->get_results("select id, nama, keterangan from data_label_komponen where tahun_anggaran=".$_POST['tahun_anggaran'], ARRAY_A);
				$body = '';
				foreach ($data_label_komponen as $k => $v) {
					$title = 'Laporan APBD Per Label Komponen "'.$v['nama'].'" | '.$_POST['tahun_anggaran'];
					$shortcode = '[monitor_label_komponen tahun_anggaran="'.$_POST['tahun_anggaran'].'" id_label="'.$v['id'].'"]';
					$update = false;
					$url_label = $this->generatePage($title, $_POST['tahun_anggaran'], $shortcode, $update);
					$body .= '
					<tr>
						<td class="text_tengah">'.($k+1).'</td>
						<td><a href="'.$url_label.'" target="_blank">'.$v['nama'].'</a></td>
						<td>'.$v['keterangan'].'</td>
						<td class="text_kanan pagu-rincian">-</td>
						<td class="text_kanan realisasi-rincian">-</td>
						<td class="text_kanan jml-rincian">-</td>
						<td class="text_tengah"><span style="" data-id="'.$v['id'].'" class="edit-label"><i class="dashicons dashicons-edit"></i></span> | <span style="" data-id="'.$v['id'].'" class="hapus-label"><i class="dashicons dashicons-no-alt"></i></span></td>
					</tr>
					';
				}
				if(!empty($body)){
					$ret['message'] = $body;
					$ret['data'] = $data_label_komponen;
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
    }

    function get_analis_rincian_label(){
    	global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil get analisa label komponen!',
			'data'		=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$data = $wpdb->get_results($wpdb->prepare('
					SELECT 
						m.id_label_komponen,
						sum(r.total_harga) as pagu,
						sum(d.realisasi) as realisasi,
						count(m.id) as jml_rincian 
					FROM data_mapping_label m
						inner join data_label_komponen l on m.id_label_komponen=l.id
							and l.active=m.active
							and l.tahun_anggaran=m.tahun_anggaran
						inner join data_rka r on m.id_rinci_sub_bl=r.id_rinci_sub_bl
							and r.active=m.active
							and r.tahun_anggaran=m.tahun_anggaran
						left join data_realisasi_rincian d on d.id_rinci_sub_bl=m.id_rinci_sub_bl
							and d.active=m.active
							and d.tahun_anggaran=m.tahun_anggaran
					where m.active=1
						and m.tahun_anggaran=%d
					group by m.id_label_komponen
					', $tahun_anggaran), ARRAY_A);
				foreach ($data as $k => $v) {
					$v['pagu'] = number_format($v['pagu'],0,",",".");
					$v['realisasi'] = number_format($v['realisasi'],0,",",".");
					$v['jml_rincian'] = number_format($v['jml_rincian'],0,",",".");
					$ret['data'][] = $v;
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
    }

    function simpan_data_label_komponen(){
    	global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil simpan data label komponen!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension' )) {
				$cek_exist = $wpdb->get_var($wpdb->prepare('
					SELECT 
						id 
					from data_label_komponen 
					where tahun_anggaran=%d 
						and active=1 
						and nama=%s
					', $_POST['tahun_anggaran'], $_POST['nama'])
				);
				// cek jika belum ada atau update label
				if(
					!$cek_exist 
					|| !empty($_POST['id_label'])
				){
					$current_user = wp_get_current_user();
					$opsi = array(
						'nama' => $_POST['nama'],
						'keterangan' => $_POST['keterangan'],
						'id_skpd' => '',
						'user' => $current_user->display_name,
						'active' => 1,
						'update_at'	=> current_time('mysql'),
						'tahun_anggaran'	=> $_POST['tahun_anggaran']
					);
					if (!empty($_POST['id_label'])) {
						$wpdb->update('data_label_komponen', $opsi, array(
							'tahun_anggaran'	=> $_POST['tahun_anggaran'],
							'id' => $_POST['id_label']
						));
					} else {
						$wpdb->insert('data_label_komponen', $opsi);
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Nama label ini sudah digunakan!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
    }

    function hapus_data_label_komponen(){
    	global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil hapus data label komponen!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension' )) {
				$wpdb->delete('data_label_komponen', array(
					'tahun_anggaran' => $_POST['tahun_anggaran'],
					'id' => $_POST['id_label']
				), array('%d', '%d'));
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
    }

    function simpan_mapping(){
    	global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil simpan data mapping sumber dana dan label komponen!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension' )) {
				$current_user = wp_get_current_user();
				if(!empty($_POST['id_mapping'])){
					$ids = explode('-', $_POST['id_mapping']);
					$kd_sbl = $ids[0];
					$rek = explode('.', $ids[1]);
					$rek_1 = $rek[0].'.'.$rek[1];
					$rek_2 = false;
					$rek_3 = false;
					$rek_4 = false;
					$rek_5 = false;
					$kelompok = 0;
					$keterangan = 0;
					$ids_rinci = array();
					if(isset($rek[2])){
						$rek_2 = $rek_1.'.'.$rek[2];
						$where = ' ';
					}
					if(isset($rek[3])){
						$rek_3 = $rek_2.'.'.$rek[3];
					}
					if(isset($rek[4])){
						$rek_4 = $rek_3.'.'.$rek[4];
					}
					if(isset($rek[5])){
						$rek_5 = $rek_4.'.'.$rek[5];
					}
					$where = '';
					if(isset($ids[2])){
						$kelompok = $ids[2];
						$subs_bl_teks = $wpdb->get_var($wpdb->prepare('
							select 
								subs_bl_teks 
							from data_rka 
							where tahun_anggaran=%d
								and id=%d', 
							$_POST['tahun_anggaran'], $kelompok
						));
						$where .= ' and subs_bl_teks=\''.$subs_bl_teks.'\'';
					}
					if(isset($ids[3])){
						$keterangan = $ids[3];
						$ket_bl_teks = $wpdb->get_var($wpdb->prepare('
							select 
								ket_bl_teks 
							from data_rka 
							where tahun_anggaran=%d
								and id=%d', 
							$_POST['tahun_anggaran'], $keterangan
						));
						$where .= ' and ket_bl_teks=\''.$ket_bl_teks.'\'';
					}
					if(isset($ids[4])){
						$ids_rinci[] = array(
							'kode_akun' => $rek_5,
							'id_rinci' => $ids[4],
							'kelompok' => $kelompok,
							'keterangan' => $keterangan
						);
						$cek = $wpdb->get_var($wpdb->prepare('
							select 
								id 
							from data_realisasi_rincian 
							where tahun_anggaran=%d
								and id_rinci_sub_bl=%d', 
							$_POST['tahun_anggaran'], $ids[4]
						));
						$opsi = array(
							'id_rinci_sub_bl' => $ids[4],
							'realisasi' => $_POST['realisasi'],
							'user' => $current_user->display_name,
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran'	=> $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_realisasi_rincian', $opsi, array(
								'tahun_anggaran'	=> $_POST['tahun_anggaran'],
								'id_rinci_sub_bl' => $ids[4]
							));
						} else {
							$wpdb->insert('data_realisasi_rincian', $opsi);
						}
					}else{
						$data_rinci = $wpdb->get_results(
							$wpdb->prepare("
								select
									id,
									id_rinci_sub_bl,
									kode_akun,
									idsubtitle,
									idsubtitle,
									subs_bl_teks,
									ket_bl_teks
								from data_rka
								where tahun_anggaran=%d
									and kode_sbl='%s'
									and kode_akun like %s
									".$where."
								Order by kode_akun ASC, subs_bl_teks ASC, ket_bl_teks ASC",
								$_POST['tahun_anggaran'], $kd_sbl, $ids[1].'%'
							), ARRAY_A
						);
						$id_kelompok = array();
						$id_keterangan = array();
						foreach ($data_rinci as $k => $v) {
							$key_ket = $v['kode_akun'].'-'.$v['subs_bl_teks'].'-'.$v['ket_bl_teks'];
							if(empty($id_kelompok[$key_ket])){
								$id_kelompok[$key_ket] = $v['id'];
							}
							if(empty($id_keterangan[$key_ket])){
								$id_keterangan[$key_ket] = $v['id'];
							}
							$ids_rinci[] = array(
								'kode_akun' => $v['kode_akun'],
								'id_rinci' => $v['id_rinci_sub_bl'],
								'kelompok' => $id_kelompok[$key_ket],
								'keterangan' => $id_keterangan[$key_ket]
							);
						}
					}

					$mapping_id = array();
					foreach ($ids_rinci as $data_rinci) {
						$id_rinci = $data_rinci['id_rinci'];
						$mapping_id[] = $kd_sbl.'-'.$data_rinci['kode_akun'].'-'.$data_rinci['kelompok'].'-'.$data_rinci['keterangan'].'-'.$id_rinci;
						$wpdb->update('data_mapping_label', 
							array(
								'user' => $current_user->display_name,
								'active' => 0,
								'update_at' => current_time('mysql')
							), array(
								'tahun_anggaran' => $_POST['tahun_anggaran'],
								'id_rinci_sub_bl' => $id_rinci
							)
						);
						foreach ($_POST['id_label'] as $k => $id_label) {
							$cek = $wpdb->get_var($wpdb->prepare('
								select 
									id 
								from data_mapping_label 
								where tahun_anggaran=%d
									and id_rinci_sub_bl=%d 
									and id_label_komponen=%d', 
								$_POST['tahun_anggaran'], $id_rinci, $id_label
							));
							$opsi = array(
								'id_rinci_sub_bl' => $id_rinci,
								'id_label_komponen' => $id_label,
								'user' => $current_user->display_name,
								'active' => 1,
								'update_at' => current_time('mysql'),
								'tahun_anggaran'	=> $_POST['tahun_anggaran']
							);
							if (!empty($cek)) {
								$wpdb->update('data_mapping_label', $opsi, array(
									'tahun_anggaran'	=> $_POST['tahun_anggaran'],
									'id_rinci_sub_bl' => $id_rinci,
									'id_label_komponen' => $id_label,
								));
							} else {
								$wpdb->insert('data_mapping_label', $opsi);
							}
						}
						$wpdb->update('data_mapping_sumberdana', 
							array(
								'user' => $current_user->display_name,
								'active' => 0,
								'update_at' => current_time('mysql')
							), array(
								'tahun_anggaran' => $_POST['tahun_anggaran'],
								'id_rinci_sub_bl' => $id_rinci
							)
						);
						foreach ($_POST['id_sumberdana'] as $k => $id_sumberdana) {
							if(empty($id_sumberdana)){
								continue;
							}
							$cek = $wpdb->get_var($wpdb->prepare('
								select 
									id 
								from data_mapping_sumberdana 
								where tahun_anggaran=%d
									and id_rinci_sub_bl=%d 
									and id_sumber_dana=%d', 
								$_POST['tahun_anggaran'], $id_rinci, $id_sumberdana
							));
							$opsi = array(
								'id_rinci_sub_bl' => $id_rinci,
								'id_sumber_dana' => $id_sumberdana,
								'user' => $current_user->display_name,
								'active' => 1,
								'update_at' => current_time('mysql'),
								'tahun_anggaran'	=> $_POST['tahun_anggaran']
							);
							if (!empty($cek)) {
								$wpdb->update('data_mapping_sumberdana', $opsi, array(
									'tahun_anggaran'	=> $_POST['tahun_anggaran'],
									'id_rinci_sub_bl' => $id_rinci,
									'id_sumber_dana' => $id_sumberdana,
								));
							} else {
								$wpdb->insert('data_mapping_sumberdana', $opsi);
							}
						}
					}
					$ret['ids_rinci'] = $mapping_id;
				}else{
					$ret['status'] = 'error';
					$ret['message'] = 'Format ID mapping tidak sesuai!';	
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
    }

    function sumberdana_sipd_lokal_ke_wp_sipd(){
    	global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkronisasi sumber dana dari SIPD Lokal ke WP-SIPD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension' )) {
				$current_user = wp_get_current_user();
				if(!empty($_POST['id_skpd'])){
					$sub_keg = $wpdb->get_results($wpdb->prepare("
						SELECT 
							kode_sbl,
							tahun_anggaran
						FROM data_sub_keg_bl
						where tahun_anggaran=%d
							and active=1
							and id_sub_skpd=%d
						group by kode_sbl
					", $_POST['tahun_anggaran'], $_POST['id_skpd']), ARRAY_A);
				}else{
					$sub_keg = $wpdb->get_results($wpdb->prepare("
						SELECT 
							kode_sbl,
							tahun_anggaran
						FROM data_sub_keg_bl
						where tahun_anggaran=%d
							and active=1
						group by kode_sbl
					", $_POST['tahun_anggaran']), ARRAY_A);
				}
				foreach ($sub_keg as $sub) {
					$dana = $wpdb->get_results($wpdb->prepare("
						SELECT 
							iddana, 
							kodedana, 
							namadana, 
							kode_sbl,
							tahun_anggaran
						FROM data_dana_sub_keg
						where tahun_anggaran=%d
							and kode_sbl=%s
							and iddana!=0
							and active=1
					", $sub['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);
					if(!empty($dana)){
						$iddana = $dana[0]['iddana'];
					}else{
						$iddana = get_option('_crb_default_sumber_dana' );
					}
					$rka = $wpdb->get_results("
						SELECT
							id_rinci_sub_bl
						FROM data_rka
						where tahun_anggaran=".$sub['tahun_anggaran']."
							and kode_sbl='".$sub['kode_sbl']."'
							and active=1
					", ARRAY_A);
					foreach ($rka as $rinci) {
						$wpdb->update('data_mapping_sumberdana', 
							array(
								'user' => $current_user->display_name,
								'active' => 0,
								'update_at' => current_time('mysql')
							), array(
								'tahun_anggaran' => $sub['tahun_anggaran'],
								'id_rinci_sub_bl' => $rinci['id_rinci_sub_bl']
							)
						);
						$cek = $wpdb->get_var($wpdb->prepare('
							select 
								id 
							from data_mapping_sumberdana 
							where tahun_anggaran=%d
								and id_rinci_sub_bl=%d 
								and id_sumber_dana=%d', 
							$sub['tahun_anggaran'], $rinci['id_rinci_sub_bl'], $iddana
						));
						$opsi = array(
							'id_rinci_sub_bl' => $rinci['id_rinci_sub_bl'],
							'id_sumber_dana' => $iddana,
							'user' => $current_user->display_name,
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran'	=> $sub['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_mapping_sumberdana', $opsi, array(
								'tahun_anggaran'	=> $sub['tahun_anggaran'],
								'id_rinci_sub_bl' => $rinci['id_rinci_sub_bl'],
								'id_sumber_dana' => $iddana,
							));
						} else {
							$wpdb->insert('data_mapping_sumberdana', $opsi);
						}
					}
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
    }

    function sumberdana_wp_sipd_ke_rka_simda(){
    	global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkronisasi sumber dana dari WP-SIPD ke RKA SIMDA!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension' )) {
				$current_user = wp_get_current_user();
				$debug = false;
				if(get_option('_crb_singkron_simda_debug') == 1){
					$debug = true;
				}
				if(!empty($_POST['id_skpd'])){
					$sub_keg = $wpdb->get_results($wpdb->prepare("
						SELECT 
							kode_sbl,
							tahun_anggaran
						FROM data_sub_keg_bl
						where tahun_anggaran=%d
							and active=1
							and id_sub_skpd=%d
						group by kode_sbl
					", $_POST['tahun_anggaran'], $_POST['id_skpd']), ARRAY_A);
				}else{
					$sub_keg = $wpdb->get_results($wpdb->prepare("
						SELECT 
							kode_sbl,
							tahun_anggaran
						FROM data_sub_keg_bl
						where tahun_anggaran=%d
							and active=1
						group by kode_sbl
					", $_POST['tahun_anggaran']), ARRAY_A);
				}
				foreach ($sub_keg as $sub) {
					$_POST['kode_sbl'] = $sub['kode_sbl'];
					$res = $this->simda->singkronSimda(array(
						'return' => false
					));
					if($res['status'] == 'error'){
						die(json_encode($res));
					}
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
    }
}
