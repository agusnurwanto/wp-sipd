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
		global $wpdb;
		$sinergi_link = $this->generate_sinergi_page();
		$sirup_link = $this->generate_sirup_page();
		$sibangda_link = $this->generate_sibangda_page();
		$simda_link = $this->generate_simda_page();
		$siencang_link = $this->generate_siencang_page();
		$basic_options_container = Container::make( 'theme_options', __( 'SIPD Options' ) )
			->set_page_menu_position( 4 )
	        ->add_fields( array(
	            Field::make( 'text', 'crb_tahun_anggaran_sipd', 'Tahun Anggaran SIPD' )
	            	->set_default_value('2021'),
	            Field::make( 'text', 'crb_daerah', 'Nama Pemda' )
	            	->set_help_text('Data diambil dari halaman pengaturan SIPD menggunakan <a href="https://github.com/agusnurwanto/sipd-chrome-extension" target="_blank">SIPD chrome extension</a>.')
	            	->set_default_value(carbon_get_theme_option( 'crb_daerah' ))
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
	            Field::make( 'html', 'crb_siencang' )
	            	->set_html( '<a target="_blank" href="'.$siencang_link.'">SIPD to SIENCANG</a> | <a href="https://github.com/ganjarnugraha/perencanaan-penganggaran" target="_blank">https://github.com/ganjarnugraha/perencanaan-penganggaran</a>' ),
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

		$unit = $wpdb->get_results("SELECT * from data_unit where active=1 and tahun_anggaran=".carbon_get_theme_option('crb_tahun_anggaran_sipd').' order by id_skpd ASC', ARRAY_A);
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
	    );

	    $cek_status_koneksi_simda = $this->CurlSimda(array(
			'query' => 'select * from ref_setting',
			'no_debug' => true
		));
		$ket_simda = '<b style="color:red">Belum terkoneksi ke simda!</b>';
		if(!empty($cek_status_koneksi_simda)){
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
				$default_val = $this->CurlSimda(array(
					'query' => 'select * from ref_bidang_mapping where kd_urusan90='.$kd[0].' and kd_bidang90='.((int)$kd[1])
				));
				$default = $default_val[0]->kd_urusan.'.'.$default_val[0]->kd_bidang;
				update_option( '_crb_unit_'.$v['id_skpd'], $default );
			}
			$mapping_unit[] = Field::make( 'text', 'crb_unit_'.$v['id_skpd'], ($k+1).'. Kode Sub Unit SIMDA untuk '.$v['kode_skpd'].' '.$v['nama_skpd'] );
		}

	    Container::make( 'theme_options', __( 'SIMDA Setting' ) )
		    ->set_page_parent( $basic_options_container )
		    ->add_fields( $mapping_unit );
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

	function CurlSimda($options, $debug=false){
        $query = $options['query'];
        $curl = curl_init();
        $req = array(
            'api_key' => get_option( '_crb_apikey_simda' ),
            'query' => $query,
            'db' => get_option('_crb_db_simda')
        );
        set_time_limit(0);
        $req = http_build_query($req);
        $url = get_option( '_crb_url_api_simda' );
        if(empty($url)){
        	return false;
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $req,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 10000
        ));

        $response = curl_exec($curl);
        // die($response);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err; die();
        } else {
        	if($debug){
            	print_r($response); die();
        	}
            $ret = json_decode($response);
            if(!empty($ret->error)){
            	if(empty($options['no_debug'])){
                	echo "<pre>".print_r($ret, 1)."</pre>"; die();
                }
            }else{
            	if(!empty($ret->msg)){
                	return $ret->msg;
                }
            }
        }
    }

    function allow_access_private_post(){
    	if(
    		!empty($_GET) 
    		&& !empty($_GET['key'])
    	){
    		$key = base64_decode($_GET['key']);
    		$key_db = carbon_get_theme_option( 'crb_api_key_extension' );
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
}
