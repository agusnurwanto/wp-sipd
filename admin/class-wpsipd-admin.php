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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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

	// https://docs.carbonfields.net/#/containers/theme-options
	public function crb_attach_sipd_options(){
		$sinergi_link = $this->generate_sinergi_page();
		$sirup_link = $this->generate_sirup_page();
		$sibangda_link = $this->generate_sibangda_page();
		$simda_link = $this->generate_simda_page();
		$basic_options_container = Container::make( 'theme_options', __( 'SIPD Options' ) )
			->set_page_menu_position( 4 )
	        ->add_fields( array(
	            Field::make( 'text', 'crb_pemda', 'Nama Pemda' )
	            	->set_help_text('Data diambil dari halaman pengaturan SIPD menggunakan <a href="https://github.com/agusnurwanto/sipd-chrome-extension" target="_blank">SIPD chrome extension</a>.')
	            	->set_default_value(carbon_get_theme_option( 'crb_pemda' ))
	            	->set_attribute('readOnly', 'true'),
	            Field::make( 'text', 'crb_kepala_daerah', 'Kepala Daerah' )
	            	->set_default_value(carbon_get_theme_option( 'crb_kepala_daerah' ))
	            	->set_attribute('readOnly', 'true'),
	            Field::make( 'text', 'crb_wakil_daerah', 'Wakil Kepala Daerah' )
	            	->set_default_value(carbon_get_theme_option( 'crb_wakil_daerah' ))
	            	->set_attribute('readOnly', 'true'),
	            Field::make( 'text', 'crb_api_key_extension', 'API KEY chrome extension' )
	            	->set_default_value($this->generateRandomString())
	            	->set_help_text('API KEY ini dipakai untuk <a href="https://github.com/agusnurwanto/sipd-chrome-extension" target="_blank">SIPD chrome extension</a>.'),
	            Field::make( 'html', 'crb_simda' )
	            	->set_html( '<a target="_blank" href="'.$simda_link.'">SIPD to SIMDA BPKP</a>' ),
	            Field::make( 'html', 'crb_sibangda' )
	            	->set_html( '<a target="_blank" href="'.$sibangda_link.'">SIPD to SIBANGDA Bina Pembangunan Kemendagri</a>' ),
	            Field::make( 'html', 'crb_sirup' )
	            	->set_html( '<a target="_blank" href="'.$sirup_link.'">SIPD to SIRUP LKPP</a>' ),
	            Field::make( 'html', 'crb_sinergi' )
	            	->set_html( '<a target="_blank" href="'.$sinergi_link.'">SIPD to SINERGI DJPK Kementrian Keuangan</a>' ),
	        ) );

	    Container::make( 'theme_options', __( 'RPJM' ) )
		    ->set_page_parent( $basic_options_container )
		    ->add_fields( array(
		        Field::make( 'radio', 'crb_publik_rpjm', __( 'Publikasikan RPJM' ) )
				    ->add_options( array(
				        '1' => __( 'Ya' ),
				        '2' => __( 'Tidak Publik' )
				    ) )
	            	->set_default_value('1')
		    ) );
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
}
