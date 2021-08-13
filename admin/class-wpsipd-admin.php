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
		return esc_url( get_permalink($custom_post));
	}

	// https://docs.carbonfields.net/#/containers/theme-options
	public function crb_attach_sipd_options(){
		global $wpdb;
		$sinergi_link = $this->generate_sinergi_page();
		$sirup_link = $this->generate_sirup_page();
		$sibangda_link = $this->generate_sibangda_page();
		$simda_link = $this->generate_simda_page();
		$siencang_link = $this->generate_siencang_page();
		$options_basic = array(
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

		$rfk_pemda = array();
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran order by tahun_anggaran ASC', ARRAY_A);
		foreach ($tahun as $k => $v) {
			$url = $this->generatePage('Monitoring Update Data SIPD lokal Berdasar Waktu Terakhir Melakukan Singkronisasi Data | '.$v['tahun_anggaran'], $v['tahun_anggaran']);
			$options_basic[] = Field::make( 'html', 'crb_monitor_update_'.$k )
            	->set_html( '<a target="_blank" href="'.$url.'">Halaman Monitor Update Data Lokal SIPD Merah Tahun '.$v['tahun_anggaran'].'</a>' );
			
			$url_pemda = $this->generatePage('Realisasi Fisik dan Keuangan Pemerintah Daerah | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rfk tahun_anggaran="'.$v['tahun_anggaran'].'"]');
            $unit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd from data_unit where active=1 and tahun_anggaran=".$v['tahun_anggaran'].' and is_skpd=1 order by nama_skpd ASC', ARRAY_A);
            $body_pemda = '<ul style="margin-left: 20px;">';
            foreach ($unit as $kk => $vv) {
				$url_skpd = $this->generatePage('RFK '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rfk tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vv['id_skpd'].'"]');
            	$body_pemda .= '<li><a target="_blank" href="'.$url_skpd.'">Halaman RFK '.$vv['nama_skpd'].' '.$v['tahun_anggaran'].'</a>';
            	$subunit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd from data_unit where active=1 and tahun_anggaran=".$v['tahun_anggaran']." and is_skpd=0 and id_unit=".$vv["id_skpd"]." order by nama_skpd ASC", ARRAY_A);
            	if(!empty($subunit)){
            		$body_pemda .= '<ul style="margin-left: 20px;">';
            	}
            	foreach ($subunit as $kkk => $vvv) {
					$url_skpd = $this->generatePage('RFK '.$vvv['nama_skpd'].' '.$vvv['kode_skpd'].' | '.$v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rfk tahun_anggaran="'.$v['tahun_anggaran'].'" id_skpd="'.$vvv['id_skpd'].'"]');
            		$body_pemda .= '<li><a target="_blank" href="'.$url_skpd.'">Halaman RFK '.$vvv['nama_skpd'].' '.$v['tahun_anggaran'].'</a></li>';
            	}
            	if(!empty($subunit)){
            		$body_pemda .= '</ul>';
            	}
            	$body_pemda .= '</li>';
            }
            $body_pemda .= '</ul>';
			$rfk_pemda[] = Field::make( 'html', 'crb_rfk_pemda_'.$k )
            	->set_html( '<a style="font-weight: bold;" target="_blank" href="'.$url_pemda.'">Halaman Realisasi Fisik dan Keuangan Pemerintah Daerah Tahun '.$v['tahun_anggaran'].'</a>'.$body_pemda );
		}

		$basic_options_container = Container::make( 'theme_options', __( 'SIPD Options' ) )
			->set_page_menu_position( 4 )
	        ->add_fields( $options_basic );

		$unit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd from data_unit where active=1 and tahun_anggaran=".carbon_get_theme_option('crb_tahun_anggaran_sipd').' order by id_skpd ASC', ARRAY_A);
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

	    Container::make( 'theme_options', __( 'SIMDA Setting' ) )
		    ->set_page_parent( $basic_options_container )
		    ->add_fields( $mapping_unit );

	    $monev = Container::make( 'theme_options', __( 'MONEV SIPD' ) )
			->set_page_menu_position( 4 )
		    ->add_fields( $rfk_pemda );

	    Container::make( 'theme_options', __( 'RFK' ) )
		    ->set_page_parent( $monev )
		    ->add_fields( $rfk_pemda );

	    Container::make( 'theme_options', __( 'Indikator RENJA' ) )
		    ->set_page_parent( $monev );

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
		    ->set_page_parent( $laporan );

	    Container::make( 'theme_options', __( 'RPJM & RENSTRA' ) )
		    ->set_page_parent( $laporan );
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
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran', ARRAY_A);
		$master_sumberdana = '';
		$no = 0;
		foreach ($tahun as $k => $v) {
			$sumberdana = $wpdb->get_results('
				select 
					iddana,
					kodedana,
					namadana 
				from data_dana_sub_keg 
				where tahun_anggaran='.$v['tahun_anggaran'].'
					and active=1
				group by iddana
				order by kodedana ASC
			', ARRAY_A);
			foreach ($sumberdana as $key => $val) {
				$no++;
				$title = 'Laporan APBD Per Sumber Dana '.$val['kodedana'].' '.$val['namadana'].' | '.$v['tahun_anggaran'];
				$shortcode = '[monitor_sumber_dana tahun_anggaran="'.$v['tahun_anggaran'].'" id_sumber_dana="'.$val['iddana'].'"]';
				$update = false;
				$url_skpd = $this->generatePage($title, $v['tahun_anggaran'], $shortcode, $update);
				if(empty($val['kodedana'])){
					$val['kodedana'] = '';
					$val['namadana'] = 'Belum Di Setting';
				}
				$master_sumberdana .= '
					<tr>
						<td>'.$no.'</td>
						<td>'.$val['kodedana'].'</td>
						<td><a href="'.$url_skpd.'" target="_blank">'.$val['namadana'].'</a></td>
						<td>'.$v['tahun_anggaran'].'</td>
					</tr>
				';
			}
		}
		$label = array(
			Field::make( 'html', 'crb_daftar_label_komponen' )
            	->set_html( '
            		<style>
            			.postbox-container { display: none; }
            			#poststuff #post-body.columns-2 { margin: 0 !important; }
            		</style>
            		<h3 class="text_tengah">Daftar Sumber Dana</h3>
            		<table class="wp-list-table widefat fixed striped">
            			<thead>
            				<tr class="text_tengah">
            					<th class="text_tengah" style="width: 20px">No</th>
            					<th class="text_tengah" style="width: 100px">Kode</th>
            					<th class="text_tengah">Sumber Dana</th>
            					<th class="text_tengah" style="width: 100px">Tahun Anggaran</th>
            				</tr>
            			</thead>
            			<tbody>
            				'.$master_sumberdana.'
            			</tbody>
            		</table>
        		' )
        );
        return $label;
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
            		<h3 class="text_tengah">Daftar Label Komponen</h3>
            		<table class="wp-list-table widefat fixed striped">
            			<thead>
            				<tr class="text_tengah">
            					<th class="text_tengah" style="width: 20px">No</th>
            					<th class="text_tengah" style="width: 300px">Nama Label</th>
            					<th class="text_tengah">Keterangan</th>
            					<th class="text_tengah" style="width: 100px">Aksi</th>
            				</tr>
            			</thead>
            			<tbody id="body_label">
            			</tbody>
            		</table>
        		' )
        );
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

    function get_label_komponen(){
    	global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> '<tr><td colspan="4" style="text-align: center;">Data Label Komponen kosong</td></tr>',
			'data'		=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				$data_label_komponen = $wpdb->get_results("select id, nama, keterangan from data_label_komponen where tahun_anggaran=".$_POST['tahun_anggaran'], ARRAY_A);
				$body = '';
				foreach ($data_label_komponen as $k => $v) {
					$title = 'Laporan APBD Per Label Komponen "'.$v['nama'].'" | '.$_POST['tahun_anggaran'];
					$shortcode = '[monitor_label_komponen tahun_anggaran="'.$_POST['tahun_anggaran'].'" id_label="'.$v['id'].'"]';
					$update = false;
					$url_label = $this->generatePage($title, $_POST['tahun_anggaran'], $shortcode, $update);
					$body .= '
					<tr>
						<td class="text-tengah">'.($k+1).'</td>
						<td><a href="'.$url_label.'" target="_blank">'.$v['nama'].'</a></td>
						<td>'.$v['keterangan'].'</td>
						<td class="text-tengah"><span style="" data-id="'.$v['id'].'" class="edit-label"><i class="dashicons dashicons-edit"></i></span> | <span style="" data-id="'.$v['id'].'" class="hapus-label"><i class="dashicons dashicons-no-alt"></i></span></td>
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

    function simpan_data_label_komponen(){
    	global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil simpan data label komponen!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				$cek_exist = $wpdb->get_var($wpdb->prepare('
					SELECT 
						id 
					from data_label_komponen 
					where tahun_anggaran=%d 
						and active=1 
						and nama=%s
					', $_POST['tahun_anggaran'], $_POST['nama'])
				);
				if(!$cek_exist){
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
						$where .= ' and idsubtitle='.$kelompok;
					}
					if(isset($ids[3])){
						$keterangan = $ids[3];
						$where .= ' and idketerangan='.$keterangan;
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
									id_rinci_sub_bl,
									kode_akun,
									idsubtitle,
									idketerangan
								from data_rka
								where tahun_anggaran=%d
									and kode_sbl='%s'
									and kode_akun like %s
									".$where,
								$_POST['tahun_anggaran'], $kd_sbl, $ids[1].'%'
							), ARRAY_A
						);
						foreach ($data_rinci as $k => $v) {
							$ids_rinci[] = array(
								'kode_akun' => $v['kode_akun'],
								'id_rinci' => $v['id_rinci_sub_bl'],
								'kelompok' => $v['idsubtitle'],
								'keterangan' => $v['idketerangan']
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
}
