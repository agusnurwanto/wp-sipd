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

require_once WPSIPD_PLUGIN_PATH . "admin/class-wpsipd-keu_pemdes.php";

class Wpsipd_Admin extends Wpsipd_Admin_Keu_Pemdes
{

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
	private $sipkd;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version, $simda, $sipkd)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->simda = $simda;
		$this->sipkd = $sipkd;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wpsipd-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

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

		wp_enqueue_script($this->plugin_name . 'jszip', plugin_dir_url(__FILE__) . 'js/jszip.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . 'xlsx', plugin_dir_url(__FILE__) . 'js/xlsx.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wpsipd-admin.js', array('jquery'), $this->version, false);
		wp_localize_script($this->plugin_name, 'wpsipd', array(
			'api_key' => get_option('_crb_api_key_extension')
		));
	}

	function gen_key($key_db = false, $options = array())
	{
		$now = time() * 1000;
		if (empty($key_db)) {
			$key_db = md5(get_option('_crb_api_key_extension'));
		}
		$tambahan_url = '';
		if (!empty($options['custom_url'])) {
			$custom_url = array();
			foreach ($options['custom_url'] as $k => $v) {
				$custom_url[] = $v['key'] . '=' . $v['value'];
			}
			$tambahan_url = $key_db . implode('&', $custom_url);
		}
		$key = base64_encode($now . $key_db . $now . $tambahan_url);
		return $key;
	}

	public function get_link_post($custom_post)
	{
		$link = get_permalink($custom_post);
		$site_url = get_site_url();
		if (
			$link == $site_url
			|| $link == $site_url . '/'
		) {
			return $link;
		}
		$options = array();
		if (!empty($custom_post->custom_url)) {
			$options['custom_url'] = $custom_post->custom_url;
		}
		if (strpos($link, '?') === false) {
			$link .= '?key=' . $this->gen_key(false, $options);
		} else {
			$link .= '&key=' . $this->gen_key(false, $options);
		}
		return $link;
	}

	public function generateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		$randomString = time() . '-' . $randomString;
		return $randomString;
	}

	public function generatePage($nama_page, $tahun_anggaran, $content = false, $update = false, $post_status = 'private')
	{
		$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
		if (empty($content)) {
			$content = '[monitor_sipd tahun_anggaran="' . $tahun_anggaran . '"]';
		}

		$_post = array(
			'post_title'	=> $nama_page,
			'post_content'	=> $content,
			'post_type'		=> 'page',
			'post_status'	=> $post_status,
			'comment_status'	=> 'closed'
		);
		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post($_post);
			$_post['insert'] = 1;
			$_post['ID'] = $id;
			$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
			update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
			update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
			update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
			update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
			update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
			update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
			update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
			update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
		} else if ($update) {
			$_post['ID'] = $custom_post->ID;
			wp_update_post($_post);
			$_post['update'] = 1;
		}
		if ($custom_post->post_status == 'publish') {
			return get_permalink($custom_post);
		} else {
			return $this->get_link_post($custom_post);
		}
	}

	function wp_sipd_admin_notice()
	{
		$versi = get_option('_wp_sipd_db_version');
		if ($versi !== $this->version) {
			$url_sql_migrate = $this->generatePage('Monitoring SQL migrate WP-SIPD', false, '[monitoring_sql_migrate]');
			echo '
        		<div class="notice notice-warning is-dismissible">
	        		<p>Versi database WP-SIPD tidak sesuai! harap dimutakhirkan. Versi saat ini=<b>' . $this->version . '</b> dan versi WP-SIPD kamu=<b>' . $versi . '</b>. Silahkan update di halaman <a href="' . $url_sql_migrate . '" class="button button-primary button-large">SQL Migrate WP-SIPD</a></p>
	         	</div>
	         ';
		}
	}

	// https://docs.carbonfields.net/#/containers/theme-options
	public function crb_attach_sipd_options()
	{
		if (!is_admin()) {
			return;
		}
		$basic_options_container = Container::make('theme_options', __('WP-SIPD Settings'))
			->set_page_menu_position(4)
			->add_fields($this->options_basic());

		if (get_option('_crb_show_menu_wpsipd_api_settings') != true) {
			Container::make('theme_options', __('API Setting'))
				->set_page_parent($basic_options_container)
				->add_fields($this->get_api_setting());
		}

		if (get_option('_crb_show_menu_wpsipd_skpd_settings') != true) {
			Container::make('theme_options', __('SKPD Setting'))
				->set_page_parent($basic_options_container)
				->add_fields($this->get_skpd_settings());
		}

		if (get_option('_crb_show_menu_wpsipd_simda_settings') != true) {
			Container::make('theme_options', __('SIMDA Setting'))
				->set_page_parent($basic_options_container)
				->add_fields($this->get_mapping_unit());
		}

		if (get_option('_crb_show_menu_wpsipd_fmis_settings') != true) {
			Container::make('theme_options', __('FMIS Setting'))
				->set_page_parent($basic_options_container)
				->add_fields($this->get_setting_fmis());
		}

		if (get_option('_crb_show_menu_wpsipd_sipkd_settings') != true) {
			Container::make('theme_options', __('SIPKD Setting'))
				->set_page_parent($basic_options_container)
				->add_fields($this->get_setting_sipkd());
		}

		if (get_option('_crb_show_menu_wpsipd_sirup_settings') != true) {
			Container::make('theme_options', __('SIRUP Setting'))
				->set_page_parent($basic_options_container)
				->add_fields($this->get_sirup_setting());
		}

		Container::make('theme_options', __('WP-SIPD Menu Setting'))
			->set_page_parent($basic_options_container)
			->add_fields($this->get_wpsipd_menu_setting());

		$show_monev_sipd_menu = get_option('_crb_show_menu_monev_monev_sipd_settings');

		if ($show_monev_sipd_menu != true) {
			$monev = Container::make('theme_options', __('MONEV SIPD'))
				->set_page_menu_position(4)
				->add_fields($this->get_ajax_field(array('type' => 'rfk')));

			if (get_option('_crb_show_menu_monev_rfk_settings') != true) {
				Container::make('theme_options', __('RFK'))
					->set_page_parent($monev)
					->add_fields($this->get_ajax_field(array('type' => 'rfk')));
			}

			if (get_option('_crb_show_menu_monev_indi_rpjm_settings') != true) {
				Container::make('theme_options', __('Indikator RPJM'))
					->set_page_parent($monev)
					->add_fields($this->get_ajax_field(array('type' => 'monev_rpjm')));
			}

			if (get_option('_crb_show_menu_monev_indi_renstra_settings') != true) {
				Container::make('theme_options', __('Indikator RENSTRA'))
					->set_page_parent($monev)
					->add_fields($this->get_ajax_field(array('type' => 'monev_renstra')));
			}

			if (get_option('_crb_show_menu_monev_indi_renja_settings') != true) {
				Container::make('theme_options', __('Indikator RENJA'))
					->set_page_parent($monev)
					->add_fields($this->get_ajax_field(array('type' => 'monev_renja')));
			}

			if (get_option('_crb_show_menu_monev_lab_komponen_settings') != true) {
				Container::make('theme_options', __('Label Komponen'))
					->set_page_parent($monev)
					->add_fields($this->generate_label_komponen());
			}

			if (get_option('_crb_show_menu_monev_sumber_dana_settings') != true) {
				Container::make('theme_options', __('Sumber Dana'))
					->set_page_parent($monev)
					->add_fields($this->generate_sumber_dana());
			}

			if (get_option('_crb_show_menu_monev_monev_rak_settings') != true) {
				Container::make('theme_options', __('Monev RAK'))
					->set_page_parent($monev)
					->add_fields($this->get_ajax_field(array('type' => 'monev_rak')));
			}

			if (get_option('_crb_show_menu_spd') != true) {
				Container::make('theme_options', __('Halaman SPD dan SK UP'))
					->set_page_parent($monev)
					->add_fields($this->get_ajax_field(array('type' => 'spd')));
			}

			if (get_option('_crb_show_menu_spp') != true) {
				Container::make('theme_options', __('Halaman SPP'))
					->set_page_parent($monev)
					->add_fields($this->get_ajax_field(array('type' => 'spp')));
			}

			if (get_option('_crb_show_menu_spm') != true) {
				Container::make('theme_options', __('Halaman SPM'))
					->set_page_parent($monev)
					->add_fields($this->get_ajax_field(array('type' => 'spm')));
			}

			if (get_option('_crb_show_menu_sp2d') != true) {
				Container::make('theme_options', __('Halaman SP2D'))
					->set_page_parent($monev)
					->add_fields($this->get_ajax_field(array('type' => 'sp2d')));
			}

			if (get_option('_crb_show_menu_monev_json_rka_settings') != true) {
				Container::make('theme_options', __('Data JSON RKA'))
					->set_page_parent($monev)
					->add_fields($this->get_ajax_field(array('type' => 'monev_json_rka')));
			}
		}
		$show_laporan_sipd_menu = get_option('_crb_show_menu_laporan_sipd_settings');

		if ($show_laporan_sipd_menu != true) {
			$laporan = Container::make('theme_options', __('LAPORAN SIPD'))
				->set_page_menu_position(4)
				->add_fields($this->generate_tag_sipd());

			if (get_option('_crb_show_menu_laporan_label_subkeg_settings') != true) {
				Container::make('theme_options', __('Tag/Label Sub Kegiatan'))
					->set_page_parent($laporan)
					->add_fields($this->generate_tag_sipd());
			}

			if (get_option('_crb_show_menu_laporan_rkpd_settings') != true) {
				Container::make('theme_options', __('RKPD & RENJA'))
					->set_page_parent($laporan)
					->add_fields($this->get_ajax_field(array('type' => 'rkpd_renja')));
			}

			if (get_option('_crb_show_menu_laporan_apbd_penjabaran_settings') != true) {
				Container::make('theme_options', __('APBD Penjabaran'))
					->set_page_parent($laporan)
					->add_fields($this->get_ajax_field(array('type' => 'apbdpenjabaran')));
			}

			if (get_option('_crb_show_menu_laporan_apbd_perda_settings') != true) {
				Container::make('theme_options', __('APBD Perda'))
					->set_page_parent($laporan)
					->add_fields($this->get_ajax_field(array('type' => 'apbdperda')));
			}

			if (get_option('_crb_show_menu_laporan_rpjm_renstra_settings') != true) {
				Container::make('theme_options', __('RPJM & RENSTRA'))
					->set_page_parent($laporan);
			}

			if (get_option('_crb_show_menu_laporan_penatausahaan_settings') != true) {
				Container::make('theme_options', __('Laporan Penatausahaan'))
					->set_page_parent($laporan)
					->add_fields($this->get_ajax_field(array('type' => 'laporan_penatausahaan')));
			}

			if (get_option('_crb_show_menu_pohon_kinerja_rpd') != true) {
				Container::make('theme_options', __('Pohon Kinerja RPD'))
					->set_page_parent($laporan)
					->add_fields(array(
						Field::make('html', 'crb_pohon_kinerja_rpd')
							->set_html('
							<style>
								.postbox-container { display: none; }
								#poststuff #post-body.columns-2 { margin: 0 !important; }
							</style>
							<h5>HALAMAN TERKAIT</h5>
							<ol>
								<li><a target="_blank" href="'.$this->generatePage('Pohon Kinerja RPD', false, '[pohon_kinerja_rpd]').'">Laporan Pohon Kinerja RPD</a></li>
								<li><a target="_blank" href="https://sipemikir.magetan.go.id/pohon-kinerja-rpd-menpan-rb/">Pokin Cascading Cross Cutting Sakip Kabupaten</a></li>
								<li><a target="_blank" href="'.$this->generatePage('Penyusunan Pohon Kinerja', false, '[penyusunan_pohon_kinerja]').'">Penyusunan Pohon Kinerja</a></li>
							</ol>
							'))
				);
			}

			if (get_option('_crb_show_menu_pohon_kinerja_renja') != true) {
				Container::make('theme_options', __('Pohon Kinerja Renja'))
					->set_page_parent($laporan)
					->add_fields($this->get_ajax_field(array('type' => 'pohon_kinerja_renja')));
			}
		}

		$show_input_perencanaan_sipd_menu = get_option('_crb_show_menu_input_sipd_settings');

		if ($show_input_perencanaan_sipd_menu != true) {
			$input_perencanaan = Container::make('theme_options', __('Input Perencanaan'))
				->set_page_menu_position(4)
				->add_fields($this->generate_jadwal_perencanaan());

			if (get_option('_crb_show_menu_input_jadwal_settings') != true) {
				Container::make('theme_options', __('Jadwal & Input Perencanaan'))
					->set_page_parent($input_perencanaan)
					->add_fields($this->generate_jadwal_perencanaan());
			}

			if (get_option('_crb_show_menu_input_input_renstra_settings') != true) {
				Container::make('theme_options', __('Input RENSTRA'))
					->set_page_parent($input_perencanaan)
					->add_fields($this->generate_input_renstra());
			}

			if (get_option('_crb_show_menu_input_input_renja_settings') != true) {
				Container::make('theme_options', __('Input RENJA'))
					->set_page_parent($input_perencanaan)
					->add_fields(array(
						Field::make('separator', 'crb_show_copy_sipd_button_settings', 'Aktifkan Tombol Copy Data SIPD ke Lokal ( WP-SIPD Settings )'),
						Field::make('checkbox', 'crb_show_copy_data_renja_settings', 'Tombol Copy Data Renja SIPD')
							->set_option_value('true')
							->set_help_text('Untuk menampilkan tombol copy data RENJA SIPD ke tabel LOKAL.'),
						Field::make('checkbox', 'crb_show_copy_data_rincian_rka_settings', 'Tombol Copy Data Rincin RKA SIPD')
							->set_option_value('true')
							->set_help_text('Untuk menampilkan tombol copy data Rincian RKA SIPD ke tabel LOKAL'),
					))
					->add_fields($this->generate_input_renja());
			}
		}

		$show_standar_harga_sipd_menu = get_option('_crb_show_menu_standar_standar_harga_settings');

		if ($show_standar_harga_sipd_menu != true) {
			$satuan_harga = Container::make('theme_options', __('Standar Harga'))
				->set_page_menu_position(5)
				->add_fields($this->get_ajax_field(array('type' => 'rekap_satuan_harga')));

			if (get_option('_crb_show_menu_standar_usulan_standar_harga_settings') != true) {
				Container::make('theme_options', __('Usulan Standar Harga'))
					->set_page_parent($satuan_harga)
					->add_fields($this->get_ajax_field(array('type' => 'monev_satuan_harga')));
			}

			if (get_option('_crb_show_menu_standar_rekap_usulan_settings') != true) {
				Container::make('theme_options', __('Rekap Usulan dan Standar Harga SIPD'))
					->set_page_parent($satuan_harga)
					->add_fields($this->get_ajax_field(array('type' => 'satuan_harga_sipd')));
			}

			if (get_option('_crb_show_menu_standar_tidak_terpakai_settings') != true) {
				Container::make('theme_options', __('Tidak Terpakai di SIPD'))
					->set_page_parent($satuan_harga)
					->add_fields($this->get_ajax_field(array('type' => 'tidak_terpakai_satuan_harga')));
			}
		}

		$show_monev_fmis_sipd_menu = get_option('_crb_show_menu_monev_fmis_check_settings');

		if ($show_monev_fmis_sipd_menu != true) {
			$monev_fmis = Container::make('theme_options', __('MONEV FMIS'))
				->set_page_menu_position(5)
				->add_fields($this->get_ajax_field(array('type' => 'register_sp2d_fmis')));
		}

		$show_keuangan_pemdes_menu = get_option('_crb_show_menu_keuangan_keuangan_pemdes_settings');

		if ($show_keuangan_pemdes_menu != true) {
			$keu_pemdes = Container::make('theme_options', __('Keuangan PEMDES'))
				->set_page_menu_position(5)
				->add_fields($this->get_setting_keu_pemdes());

			$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
			if (empty($tahun_anggaran)) {
				$tahun_anggaran = date('Y');
			}
			$url_per_kecamatan = $this->generatePage('Laporan Realisasi Keuangan Desa per Kecamatan', false, '[laporan_keu_pemdes_per_kecamatan]');
			$url_bku_add = $this->generatePage('Laporan Keuangan Pemerintah Desa Bantuan Keuangan Umum (BKU) Alokasi Dana Desa (ADD) ' . $tahun_anggaran, false, '[keu_pemdes_bku_add tahun_anggaran="' . $tahun_anggaran . '"]');
			$url_bku_dd = $this->generatePage('Laporan Keuangan Pemerintah Desa Bantuan Keuangan Umum (BKU) Desa Dana Desa (DD) ' . $tahun_anggaran, false, '[keu_pemdes_bku_dd tahun_anggaran="' . $tahun_anggaran . '"]');
			$url_bhrd = $this->generatePage('Laporan Keuangan Pemerintah Desa Bagi Hasil Retribusi Daerah (BHRD) ' . $tahun_anggaran, false, '[keu_pemdes_bhrd tahun_anggaran="' . $tahun_anggaran . '"]');
			$url_bhpd = $this->generatePage('Laporan Keuangan Pemerintah Desa Bagi Hasil Pajak Daerah (BHPD) ' . $tahun_anggaran, false, '[keu_pemdes_bhpd tahun_anggaran="' . $tahun_anggaran . '"]');
			$url_bkk_pilkades = $this->generatePage('Laporan Keuangan Pemerintah Desa Bantuan Keuangan Khusus (BKK) Pilkades ' . $tahun_anggaran, false, '[keu_pemdes_bkk_pilkades tahun_anggaran="' . $tahun_anggaran . '"]');
			$url_bkk_inf = $this->generatePage('Laporan Keuangan Pemerintah Desa Bantuan Keuangan Khusus (BKK) Infrastruktur ' . $tahun_anggaran, false, '[keu_pemdes_bkk_inf tahun_anggaran="' . $tahun_anggaran . '"]');

			if (get_option('_crb_show_menu_keuangan_beranda_settings') != true) {
				Container::make('theme_options', __('Tampilan Beranda'))
					->set_page_parent($keu_pemdes)
					->add_tab(__('Logo'), array(
						Field::make('image', 'crb_keu_pemdes_menu_logo_dashboard', __('Gambar Logo'))
							->set_value_type('url')
							->set_default_value('https://via.placeholder.com/135x25'),
						Field::make('textarea', 'crb_keu_pemdes_judul_header', __('Judul'))
							->set_default_value('SIDETIK DESA<br>Sistem Deteksi Informasi Keuangan Pemerintah Desa'),
						Field::make('text', 'crb_keu_pemdes_menu_video_loading', __('Video Loading'))
							->set_default_value(WPSIPD_PLUGIN_URL . 'public/images/video-loading.mp4'),
						Field::make('text', 'crb_keu_pemdes_lama_loading', __('Lama Loading'))
							->set_default_value('10000')
							->set_attribute('type', 'number')
							->set_help_text('Lama waktu untuk menghilangkan gambar atau video intro. Satuan dalam mili detik.'),
						Field::make('complex', 'crb_keu_pemdes_background_beranda', 'Background Beranda')
							->add_fields('beranda', array(
								Field::make('image', 'gambar', 'Gambar')
									->set_value_type('url')
									->set_default_value(WPSIPD_PLUGIN_URL . 'public/images/bg_video.jpg')
							)),
					))
					->add_tab(__('Icon & Menu'), array(
						Field::make('image', 'crb_keu_pemdes_menu_logo_1', __('Gambar Menu Penyaluran Keuangan Pemerintah Desa'))
							->set_value_type('url')
							->set_default_value(WPSIPD_PLUGIN_URL . 'public/images/penyaluran-keuangan.png'),
						Field::make('text', 'crb_keu_pemdes_menu_text_1', __('Text Menu Penyaluran Keuangan Pemerintah Desa'))
							->set_default_value('Penyaluran Keuangan Pemerintah Desa'),
						Field::make('text', 'crb_keu_pemdes_menu_url_1', __('URL Menu Penyaluran Keuangan Pemerintah Desa'))
							->set_default_value(wp_login_url()),
						Field::make('image', 'crb_keu_pemdes_menu_logo_2', __('Gambar Menu Informasi Keuangan Pemerintah Desa'))
							->set_value_type('url')
							->set_default_value(WPSIPD_PLUGIN_URL . 'public/images/informasi-keuangan.png'),
						Field::make('text', 'crb_keu_pemdes_menu_text_2', __('Text Menu Informasi Keuangan Pemerintah Desa'))
							->set_default_value('Informasi Keuangan Pemerintah Desa'),
						Field::make('text', 'crb_keu_pemdes_menu_url_2', __('URL Menu Informasi Keuangan Pemerintah Desa'))
							->set_default_value($url_per_kecamatan),
						Field::make('image', 'crb_keu_pemdes_menu_logo_3', __('Gambar Menu DBHPD'))
							->set_value_type('url')
							->set_default_value(WPSIPD_PLUGIN_URL . 'public/images/dbhpd.png'),
						Field::make('text', 'crb_keu_pemdes_menu_text_3', __('Text Menu DBHPD'))
							->set_default_value('Dana Bagi Hasil Pajak Daerah'),
						Field::make('text', 'crb_keu_pemdes_menu_url_3', __('URL Menu DBHPD'))
							->set_default_value($url_bhpd),
						Field::make('image', 'crb_keu_pemdes_menu_logo_4', __('Gambar Menu DBHRD'))
							->set_value_type('url')
							->set_default_value(WPSIPD_PLUGIN_URL . 'public/images/dbhrd.png'),
						Field::make('text', 'crb_keu_pemdes_menu_text_4', __('Text Menu DBHRD'))
							->set_default_value('Dana Bagi Hasil Retribusi Daerah'),
						Field::make('text', 'crb_keu_pemdes_menu_url_4', __('URL Menu DBHRD'))
							->set_default_value($url_bhrd),
						Field::make('image', 'crb_keu_pemdes_menu_logo_5', __('Gambar Menu BKU ADD'))
							->set_value_type('url')
							->set_default_value(WPSIPD_PLUGIN_URL . 'public/images/bku-add.png'),
						Field::make('text', 'crb_keu_pemdes_menu_text_5', __('Text Menu BKU ADD'))
							->set_default_value('Bantuan Keuangan Umum Alokasi Dana Desa'),
						Field::make('text', 'crb_keu_pemdes_menu_url_5', __('URL Menu BKU ADD'))
							->set_default_value($url_bku_add),
						Field::make('image', 'crb_keu_pemdes_menu_logo_6', __('Gambar Menu BKU DD'))
							->set_value_type('url')
							->set_default_value(WPSIPD_PLUGIN_URL . 'public/images/bku-dd.png'),
						Field::make('text', 'crb_keu_pemdes_menu_text_6', __('Text Menu BKU DD'))
							->set_default_value('Bantuan Keuangan Umum Dana Desa'),
						Field::make('text', 'crb_keu_pemdes_menu_url_6', __('URL Menu BKU DD'))
							->set_default_value($url_bku_dd),
						Field::make('image', 'crb_keu_pemdes_menu_logo_7', __('Gambar Menu BKK Infrastruktur'))
							->set_value_type('url')
							->set_default_value(WPSIPD_PLUGIN_URL . 'public/images/bkk-infrastruktur.png'),
						Field::make('text', 'crb_keu_pemdes_menu_text_7', __('Text Menu BKK Infrastruktur'))
							->set_default_value('Bantuan Keuangan Khusus Infrastruktur'),
						Field::make('text', 'crb_keu_pemdes_menu_url_7', __('URL Menu BKK Infrastruktur'))
							->set_default_value($url_bkk_inf),
						Field::make('image', 'crb_keu_pemdes_menu_logo_8', __('Gambar Menu BKK Pilkades'))
							->set_value_type('url')
							->set_default_value(WPSIPD_PLUGIN_URL . 'public/images/bkk-pilkades.png'),
						Field::make('text', 'crb_keu_pemdes_menu_text_8', __('Text Menu BKK Pilkades'))
							->set_default_value('Bantuan Keuangan Khusus Pilkades'),
						Field::make('text', 'crb_keu_pemdes_menu_url_8', __('URL Menu BKK Pilkades'))
							->set_default_value($url_bkk_pilkades),
						Field::make('image', 'crb_keu_pemdes_menu_logo_9', __('Gambar Menu Total Keuangan Per Kecamatan'))
							->set_value_type('url')
							->set_default_value(WPSIPD_PLUGIN_URL . 'public/images/total-perkecamatan.png'),
						Field::make('text', 'crb_keu_pemdes_menu_text_9', __('Text Menu Total Keuangan Per Kecamatan'))
							->set_default_value('Total Keuangan Per Kecamatan'),
						Field::make('text', 'crb_keu_pemdes_menu_url_9', __('URL Menu Total Keuangan Per Kecamatan'))
							->set_default_value($url_per_kecamatan)
					));
			}

			$management_data_bkk_infrastruktur = $this->generatePage('Management Data BKK Infrastruktur', false, '[management_data_bkk_infrastruktur]');
			$management_data_bkk_pilkades = $this->generatePage('Management Data BKK Pilkades', false, '[management_data_bkk_pilkades]');
			$management_data_bhpd = $this->generatePage('Management Data BHPD', false, '[management_data_bhpd]');
			$management_data_bhrd = $this->generatePage('Management Data BHRD', false, '[management_data_bhrd]');
			$management_data_bku_dd = $this->generatePage('Management Data BKU DD', false, '[management_data_bku_dd]');
			$management_data_bku_add = $this->generatePage('Management Data BKU ADD', false, '[management_data_bku_add]');
			$input_pencairan_bkk = $this->generatePage('Halaman Input Pencairan BKK', false, '[input_pencairan_bkk]');
			$input_pencairan_bkk_pilkades = $this->generatePage('Halaman Input Pencairan BKK Pilkades', false, '[input_pencairan_bkk_pilkades]');
			$input_pencairan_bhpd = $this->generatePage('Halaman Input Pencairan bhpd', false, '[input_pencairan_bhpd]');
			$input_pencairan_bhrd = $this->generatePage('Halaman Input Pencairan bhrd', false, '[input_pencairan_bhrd]');
			$input_pencairan_bku_dd = $this->generatePage('Halaman Input Pencairan BKU DD', false, '[input_pencairan_bku_dd]');
			$input_pencairan_bku_add = $this->generatePage('Halaman Input Pencairan BKU ADD', false, '[input_pencairan_bku_add]');

			if (get_option('_crb_show_menu_keuangan_import_bkk_infra_settings') != true) {
				Container::make('theme_options', __('Import BKK Infrastruktur'))
					->set_page_parent($keu_pemdes)
					->add_fields(array(
						Field::make('html', 'crb_halaman_terkait_bkk_infrastruktur')
							->set_html('
							<style>
								.postbox-container { display: none; }
								#poststuff #post-body.columns-2 { margin: 0 !important; }
							</style>
							<h5>HALAMAN TERKAIT</h5>
							<ol>
								<li><a target="_blank" href="' . $management_data_bkk_infrastruktur . '">Management Data BKK Infrastruktur</a></li>
								<li><a target="_blank" href="' . $input_pencairan_bkk . '">Halaman Input Pencairan BKK Infrastruktur</a></li>
							</ol>
							'),
						Field::make('html', 'crb_bkk_infrastruktur_upload_html')
							->set_html('<h3>Import EXCEL data Bantuan Keuangan Khusus Infrastruktur</h3>
								<label><input type="checkbox" id="pencairan"> Pencairan Anggaran</label>
								<br>
								Pilih file excel .xlsx : <input type="file" id="file-excel" onchange="filePickedWpsipd(event);">
								<br>
								Contoh format file excel bisa <a target="_blank" href="' . WPSIPD_PLUGIN_URL . 'excel/contoh_bkk_infrastruktur.xlsx">download di sini</a>. Sheet file excel yang akan diimport harus diberi nama <b>data</b>. Untuk kolom nilai angka ditulis tanpa tanda titik.'),
						Field::make('html', 'crb_bkk_infrastruktur_satset')
							->set_html('Data JSON : <textarea id="data-excel" class="cf-select__input"></textarea>'),
						Field::make('html', 'crb_bkk_infrastruktur_save_button')
							->set_html('<a onclick="import_excel_bkk_infrastruktur(); return false" href="javascript:void(0);" class="button button-primary">Import WP</a>')
					));
			}

			if (get_option('_crb_show_menu_keuangan_import_bkk_pilkades_settings') != true) {
				Container::make('theme_options', __('Import BKK Pilkades'))
					->set_page_parent($keu_pemdes)
					->add_fields(array(
						Field::make('html', 'crb_halaman_terkait_bkk_pilkades')
							->set_html('
							<style>
								.postbox-container { display: none; }
								#poststuff #post-body.columns-2 { margin: 0 !important; }
							</style>
							<h5>HALAMAN TERKAIT</h5>
							<ol>
								<li><a target="_blank" href="' . $management_data_bkk_pilkades . '">Management Data BKK Pilkades</a></li>
								<li><a target="_blank" href="' . $input_pencairan_bkk_pilkades . '">Halaman Input Pencairan BKK Pilkades</a></li>
							</ol>
							'),
						Field::make('html', 'crb_bkk_pilkades_upload_html')
							->set_html('<h3>Import EXCEL data Bantuan Keuangan Khusus pilkades</h3>
								<label><input type="checkbox" id="pencairan"> Pencairan Anggaran</label>
								<br>
								Pilih file excel .xlsx : <input type="file" id="file-excel" onchange="filePickedWpsipd(event);">
								<br>
								Contoh format file excel bisa <a target="_blank" href="' . WPSIPD_PLUGIN_URL . 'excel/contoh_bku_dd.xlsx">download di sini</a>. Sheet file excel yang akan diimport harus diberi nama <b>data</b>. Untuk kolom nilai angka ditulis tanpa tanda titik.'),
						Field::make('html', 'crb_bkk_pilkades_satset')
							->set_html('Data JSON : <textarea id="data-excel" class="cf-select__input"></textarea>'),
						Field::make('html', 'crb_bkk_pilkades_save_button')
							->set_html('<a onclick="import_excel_bkk_pilkades(); return false" href="javascript:void(0);" class="button button-primary">Import WP</a>')
					));
			}

			if (get_option('_crb_show_menu_keuangan_import_bhpd_settings') != true) {
				Container::make('theme_options', __('Import BHPD'))
					->set_page_parent($keu_pemdes)
					->add_fields(array(
						Field::make('html', 'crb_halaman_terkait_bhpd')
							->set_html('
							<style>
								.postbox-container { display: none; }
								#poststuff #post-body.columns-2 { margin: 0 !important; }
							</style>
							<h5>HALAMAN TERKAIT</h5>
							<ol>
								<li><a target="_blank" href="' . $management_data_bhpd . '">Management Data BHPD</a></li>
								<li><a target="_blank" href="' . $input_pencairan_bhpd . '">Halaman Input Pencairan BHPD</a></li>
							</ol>
							'),
						Field::make('html', 'crb_bhpd_upload_html')
							->set_html('<h3>Import EXCEL data Bagi Hasil Pajak Daerah (BHPD)</h3>
								<label><input type="checkbox" id="pencairan"> Pencairan Anggaran</label>
								<br>
								Pilih file excel .xlsx : <input type="file" id="file-excel" onchange="filePickedWpsipd(event);">
								<br>
								Contoh format file excel bisa <a target="_blank" href="' . WPSIPD_PLUGIN_URL . 'excel/contoh_bku_dd.xlsx">download di sini</a>. Sheet file excel yang akan diimport harus diberi nama <b>data</b>. Untuk kolom nilai angka ditulis tanpa tanda titik.'),
						Field::make('html', 'crb_bhpd_satset')
							->set_html('Data JSON : <textarea id="data-excel" class="cf-select__input"></textarea>'),
						Field::make('html', 'crb_bhpd_save_button')
							->set_html('<a onclick="import_excel_bhpd(); return false" href="javascript:void(0);" class="button button-primary">Import WP</a>')
					));
			}

			if (get_option('_crb_show_menu_keuangan_import_bhrd_settings') != true) {
				Container::make('theme_options', __('Import BHRD'))
					->set_page_parent($keu_pemdes)
					->add_fields(array(
						Field::make('html', 'crb_halaman_terkait_bhrd')
							->set_html('
							<style>
								.postbox-container { display: none; }
								#poststuff #post-body.columns-2 { margin: 0 !important; }
							</style>
							<h5>HALAMAN TERKAIT</h5>
							<ol>
								<li><a target="_blank" href="' . $management_data_bhrd . '">Management Data BHRD</a></li>
								<li><a target="_blank" href="' . $input_pencairan_bhrd . '">Halaman Input Pencairan BHRD</a></li>
							</ol>
							'),
						Field::make('html', 'crb_bhrd_upload_html')
							->set_html('<h3>Import EXCEL data Bagi Hasil Retribusi Daerah (BHRD)</h3>
								<label><input type="checkbox" id="pencairan"> Pencairan Anggaran</label>
								<br>
								Pilih file excel .xlsx : <input type="file" id="file-excel" onchange="filePickedWpsipd(event);">
								<br>
								Contoh format file excel bisa <a target="_blank" href="' . WPSIPD_PLUGIN_URL . 'excel/contoh_bku_dd.xlsx">download di sini</a>. Sheet file excel yang akan diimport harus diberi nama <b>data</b>. Untuk kolom nilai angka ditulis tanpa tanda titik.'),
						Field::make('html', 'crb_bhrd_satset')
							->set_html('Data JSON : <textarea id="data-excel" class="cf-select__input"></textarea>'),
						Field::make('html', 'crb_bhrd_save_button')
							->set_html('<a onclick="import_excel_bhrd(); return false" href="javascript:void(0);" class="button button-primary">Import WP</a>')
					));
			}

			global $wpdb;
			$akun_hibah_uang = $wpdb->get_results("
				SELECT DISTINCT
					kode_akun, 
					nama_akun 
				FROM `data_akun` 
				where is_bankeu_umum=1 
					and kode_akun='5.4.02.05.01.0001'
				order by kode_akun ASC
			", ARRAY_A);
			$pilih_akun = "<option value=''>Pilih rekening</option>";
			foreach ($akun_hibah_uang as $akun) {
				$pilih_akun .= "<option value='$akun[kode_akun]'>$akun[kode_akun] $akun[nama_akun]</option>";
			}

			if (get_option('_crb_show_menu_keuangan_import_bku_dd_settings') != true) {
				Container::make('theme_options', __('Import BKU DD'))
					->set_page_parent($keu_pemdes)
					->add_fields(array(
						Field::make('html', 'crb_halaman_terkait_bku_dd')
							->set_html('
							<style>
								.postbox-container { display: none; }
								#poststuff #post-body.columns-2 { margin: 0 !important; }
							</style>
							<h5>HALAMAN TERKAIT</h5>
							<ol>
								<li><a target="_blank" href="' . $management_data_bku_dd . '">Management Data BKU DD</a></li>
								<li><a target="_blank" href="' . $input_pencairan_bku_dd . '">Halaman Input Pencairan BKU DD</a></li>
							</ol>
							'),
						Field::make('html', 'crb_bku_dd_singkron_button')
							->set_html('
							<h3>Singkronisasi dari data WP-SIPD</h3>
							<label>Tahun anggaran: <input type="number" value="' . date('Y') . '" id="tahun_anggaran"/></label>
							<br>
							<br>
							<label>Filter kelompok belanja: <input type="text" value="" id="kelompok_belanja"></label>
							<br>
							<br>
							<a onclick="singkron_bku_dd(); return false" href="javascript:void(0);" class="button button-primary">Proses</a>'),
						Field::make('html', 'crb_bku_dd_upload_html')
							->set_html('
								<h3>Import EXCEL data Bantuan Keuangan Umum (BKU) Dana Desa (DD)</h3>
								<label><input type="checkbox" id="pencairan"> Pencairan Anggaran</label>
								<br>
								Pilih file excel .xlsx : <input type="file" id="file-excel" onchange="filePickedWpsipd(event);">
								<br>
								Contoh format file excel bisa <a target="_blank" href="' . WPSIPD_PLUGIN_URL . 'excel/contoh_bku_dd.xlsx">download di sini</a>. Sheet file excel yang akan diimport harus diberi nama <b>data</b>. Untuk kolom nilai angka ditulis tanpa tanda titik.
							'),
						Field::make('html', 'crb_bku_dd_satset')
							->set_html('Data JSON : <textarea id="data-excel" class="cf-select__input"></textarea>'),
						Field::make('html', 'crb_bku_dd_save_button')
							->set_html('<a onclick="import_excel_bku_dd(); return false" href="javascript:void(0);" class="button button-primary">Import WP</a>')
					));
			}

			if (get_option('_crb_show_menu_keuangan_import_bku_add_settings') != true) {
				Container::make('theme_options', __('Import BKU ADD'))
					->set_page_parent($keu_pemdes)
					->add_fields(array(
						Field::make('html', 'crb_halaman_terkait_bku_add')
							->set_html('
							<style>
								.postbox-container { display: none; }
								#poststuff #post-body.columns-2 { margin: 0 !important; }
							</style>
							<h5>HALAMAN TERKAIT</h5>
							<ol>
								<li><a target="_blank" href="' . $management_data_bku_add . '">Management Data BKU ADD</a></li>
								<li><a target="_blank" href="' . $input_pencairan_bku_add . '">Halaman Input Pencairan BKU ADD</a></li>
							</ol>
							'),
						Field::make('html', 'crb_bku_add_singkron_button')
							->set_html('
							<h3>Singkronisasi dari data WP-SIPD</h3>
							<label>Tahun anggaran: <input type="number" value="' . date('Y') . '" id="tahun_anggaran"/></label>
							<br>
							<br>
							<label>Filter kelompok belanja: <input type="text" value="" id="kelompok_belanja"></label>
							<br>
							<br>
							<a onclick="singkron_bku_add(); return false" href="javascript:void(0);" class="button button-primary">Proses</a>'),
						Field::make('html', 'crb_bku_add_upload_html')
							->set_html('<h3>Import EXCEL data Bantuan Keuangan Umum (BKU) Alokasi Dana Desa (ADD)</h3>
								<label><input type="checkbox" id="pencairan"> Pencairan Anggaran</label>
								<br>
								Pilih file excel .xlsx : <input type="file" id="file-excel" onchange="filePickedWpsipd(event);">
								<br>
								Contoh format file excel bisa <a target="_blank" href="' . WPSIPD_PLUGIN_URL . 'excel/contoh_bku_dd.xlsx">download di sini</a>. Sheet file excel yang akan diimport harus diberi nama <b>data</b>. Untuk kolom nilai angka ditulis tanpa tanda titik.'),
						Field::make('html', 'crb_bku_add_satset')
							->set_html('Data JSON : <textarea id="data-excel" class="cf-select__input"></textarea>'),
						Field::make('html', 'crb_bku_add_save_button')
							->set_html('<a onclick="import_excel_bku_add(); return false" href="javascript:void(0);" class="button button-primary">Import WP</a>')
					));
			}
		}


		$show_verifikasi_rka_sipd_menu = get_option('_crb_show_menu_verifikasi_rka_check_settings');

		if ($show_verifikasi_rka_sipd_menu != true) {
			$url_user_verifikator = $this->generatePage('User Verifikasi RKA', false, '[user_verikasi_rka]');
			$url_user_pptk = $this->generatePage('User PPTK', false, '[user_pptk]');
			$user_verifikator = array(
				'verifikator_bappeda' => 'Verifikator Perencanaan',
				'verifikator_bppkad' => 'Verifikator Keuangan',
				'verifikator_pbj' => 'Verifikator Pengadaan Barang dan Jasa',
				'verifikator_adbang' => 'Verifikator Administrasi Pembangunan',
				'verifikator_inspektorat' => 'Verifikator Inspektorat',
				'verifikator_pupr' => 'Verifikator Pekerjaan Umum (PUPR)'
			);
			global $wpdb;
			$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran', ARRAY_A);
			$list_data_rka = "";
			foreach ($tahun as $k => $v) {
				$title = 'Jadwal Verifikasi RKA | ' . $v['tahun_anggaran'];
				$shortcode = '[jadwal_verifikasi_rka tahun_anggaran="' . $v['tahun_anggaran'] . '" sipd="1"]';
				$update = false;
				$page_url = $this->generatePage($title, $v['tahun_anggaran'], $shortcode, $update);
				$list_data_rka .= '<li><a href="' . $page_url . '" target="_blank">' . $title . '</a></li>';
			}
			Container::make('theme_options', __('Verifikasi RKA'))
				->set_page_menu_position(5)
				->add_fields(
					array(
						Field::make('multiselect', 'crb_daftar_user_verifikator', 'Daftar grup user verifikator RKA/DPA yang diaktfikan')
							->add_options($user_verifikator)
							->set_default_value(array(
								'verifikator_bappeda',
								'verifikator_bppkad',
								'verifikator_pbj',
								'verifikator_adbang',
								'verifikator_inspektorat',
								'verifikator_pupr'
							)),
						Field::make('html', 'crb_verifikasi_rka_page')
							->set_html('
					<ul>
						<li><a href="' . $url_user_verifikator . '" target="_blank">Halaman User Verifikasi RKA</a></li>
					</ul>'),
						Field::make('html', 'crb_pptk_page')
							->set_html('
					<ul>
						<li><a href="' . $url_user_pptk . '" target="_blank">Halaman User PPTK</a></li>
					</ul>'),
						Field::make('html', 'crb_jadwal_verifikasi_rka')
							->set_html('
						<ul>' . $list_data_rka . '</ul>
					')
					)
				);
		}
	}

	public function cek_lisensi_backend()
	{
		$status_lisensi = get_option('_crb_status_lisensi');
		$warna = "";
		if ($status_lisensi == 'pending') {
			$warna = "color: #979700;";
		} else if ($status_lisensi == 'active') {
			$warna = "color: green;";
		} else if ($status_lisensi == 'expired') {
			$warna = "color: red;";
		}
		$status_lisensi_ket = get_option('_crb_status_lisensi_ket');
		if (!empty($status_lisensi_ket)) {
			$status_lisensi_ket = ' Status: <b style="' . $warna . '">' . $status_lisensi_ket . '</b>';
		} else {
			$server = get_option('_crb_server_wp_sipd');
			$no_wa = get_option('_crb_no_wa');
			$pemda = get_option('_crb_daerah');
			$api_key_server = get_option('_crb_server_wp_sipd_api_key');
			if (
				!empty($server) 
				&& !empty($no_wa)
				&& !empty($pemda)
				&& !empty($api_key_server)
			) {
				$_POST['no_wa'] = $no_wa;
				$_POST['server'] = $server;
				$_POST['api_key_server'] = $api_key_server;
				$_POST['pemda'] = $pemda;
				$response = json_decode($this->generate_lisensi(true));
				if ($response->status == 'success') {
					$status_lisensi = get_option('_crb_status_lisensi');
					$warna = "";
					if ($status_lisensi == 'pending') {
						$warna = "color: #979700;";
					} else if ($status_lisensi == 'active') {
						$warna = "color: green;";
					} else if ($status_lisensi == 'expired') {
						$warna = "color: red;";
					}
					$status_lisensi_ket = get_option('_crb_status_lisensi_ket');
					if (!empty($status_lisensi_ket)) {
						$status_lisensi_ket = ' Status: <b style="' . $warna . '">' . $status_lisensi_ket . '</b>';
					}
				}
			} else {
				$warna = "color: red;";
				$status_lisensi_ket = ' Status: <b style="' . $warna . '">Proses inisiasi data awal!</b>';
			}
		}
		return $status_lisensi_ket;
	}

	public function options_basic()
	{
		global $wpdb;
		// $sinergi_link = $this->generate_sinergi_page();
		// $sirup_link = $this->generate_sirup_page();
		// $sibangda_link = $this->generate_sibangda_page();
		// $simda_link = $this->generate_simda_page();
		// $siencang_link = $this->generate_siencang_page();

		$url_sql_migrate = $this->generatePage('Monitoring SQL migrate WP-SIPD', false, '[monitoring_sql_migrate]');
		$status_lisensi_ket = $this->cek_lisensi_backend();
		$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
		if (empty($tahun_anggaran)) {
			$tahun_anggaran = date('Y');
		}

		$sumber_dana_all = array();
		$sumber_dana = $wpdb->get_results("
			SELECT
				id_dana,
				kode_dana,
				nama_dana
			from data_sumber_dana
			where set_input='Ya'
				and tahun_anggaran=" . $tahun_anggaran . "
			group by id_dana
		", ARRAY_A);
		foreach ($sumber_dana as $k => $v) {
			$sumber_dana_all[$v['id_dana']] = $v['kode_dana'] . ' ' . $v['nama_dana'] . ' [' . $v['id_dana'] . ']';
		}

		$provinsi_all = array();
		$kab_kot_all = array();
		$alamat = $wpdb->get_results("
			SELECT
				d.id_prov, 
				d.id_alamat as id_kab, 
				(SELECT nama from data_alamat where is_prov=1 and id_alamat=d.id_prov and tahun=d.tahun) as provinsi, 
				d.nama as kabupaten
			FROM `data_alamat` d 
			where d.is_kab=1 
				and tahun=" . $tahun_anggaran . "
		", ARRAY_A);
		foreach ($alamat as $k => $v) {
			$provinsi_all[$v['id_prov']] = $v['provinsi'];
			$kab_kot_all[$v['id_kab']] = $v['kabupaten'];
		}

		$nama_pemda = get_option('_crb_daerah');
		if (empty($nama_pemda) || $nama_pemda == 'false') {
			$nama_pemda = '';
		}

		$default_val_url = 'https://wpsipd.baktinegara.co.id/wp-admin/admin-ajax.php';
		$server_wpsipd = get_option('_crb_server_wp_sipd');
		if (empty($server_wpsipd) || $server_wpsipd != $default_val_url) {
			update_option('_crb_server_wp_sipd', $default_val_url);
		}

		$default_val_api_key = 'bcvbsdfr12-ret-ert-dfg-hghj6575';
		$api_key_server_wpsipd = get_option('_crb_server_wp_sipd_api_key');
		if (empty($api_key_server_wpsipd) || $api_key_server_wpsipd != $default_val_api_key) {
			update_option('_crb_server_wp_sipd_api_key', $default_val_api_key);
		}

		$options_basic = array(
			Field::make('text', 'crb_server_wp_sipd', 'Server Generate Lisensi WP-SIPD')
				->set_attribute('placeholder', $default_val_url)
				->set_default_value($default_val_url)
				->set_attribute('readOnly', 'true')
				->set_required(true),
			Field::make('text', 'crb_server_wp_sipd_api_key', 'API KEY WP-SIPD')
				->set_attribute('placeholder', 'xxxxxxx-xx-xxx-xxxx-xxxxxxxxxx')
				->set_default_value($default_val_api_key)
				->set_classes('hide')
				->set_attribute('readOnly', 'true')
				->set_required(true),
			Field::make('text', 'crb_no_wa', 'No Whatsapp')
				->set_attribute('type', 'number')
				->set_attribute('placeholder', '628xxxxxxxxx')
				->set_required(true)
				->set_help_text('Nomor whatsapp untuk menerima pesan dari server WP-SIPD. Format nomor diawali dengan 62xxxxxxxxxx tanpa perlu ada + di depan nomor.'),
			Field::make('text', 'crb_daerah', 'Nama Pemda')
				->set_default_value($nama_pemda)
				->set_required(true),
			Field::make('image', 'crb_logo_dashboard', __('Logo Pemda'))
				->set_value_type('url')
				->set_default_value('https://via.placeholder.com/233x268'),
			Field::make('text', 'crb_api_key_extension', 'Lisensi key chrome extension / API KEY')
				->set_required(true)
				->set_attribute('readOnly', 'true')
				->set_help_text('Lisensi key ini dipakai untuk <a href="https://github.com/agusnurwanto/sipd-chrome-extension" target="_blank">SIPD chrome extension</a>.<span id="ket_lisensi_wpsipd">' . $status_lisensi_ket . '</span>'),
			Field::make('html', 'crb_html_set_lisensi')
				->set_html('<a onclick="generate_lisensi(); return false;" href="#" class="button button-primary">Generate Lisensi WP-SIPD</a>'),
			Field::make('text', 'crb_awal_rpjmd', 'Tahun Awal RPJMD')
				->set_default_value('2018'),
			Field::make('text', 'crb_tahun_anggaran_sipd', 'Tahun Anggaran SIPD')
				->set_default_value('2021'),
			Field::make('text', 'crb_kepala_daerah', 'Kepala Daerah')
				->set_help_text('Data diambil dari halaman pengaturan SIPD menggunakan <a href="https://github.com/agusnurwanto/sipd-chrome-extension" target="_blank">SIPD chrome extension</a>.')
				->set_default_value(get_option('_crb_kepala_daerah')),
			Field::make('text', 'crb_wakil_daerah', 'Wakil Kepala Daerah')
				->set_default_value(get_option('_crb_wakil_daerah')),
			Field::make('radio', 'crb_kunci_sumber_dana_mapping', 'Kunci pilihan Sumber Dana di Halaman Mapping Rincian')
				->add_options(array(
					'1' => __('Ya'),
					'2' => __('Tidak')
				))
				->set_default_value('1')
				->set_help_text('Fitur ini untuk mengunci pilihan sumber dana sesuai yang sudah disetting di sipd.kemendagri.go.id saat melakukan mapping sumber dana. Pilih <b>Tidak</b> jika tidak ingin mengunci pilihan sumber dana.'),
			Field::make('radio', 'crb_cara_input_realisasi', 'Input realisasi anggaran')
				->add_options(array(
					'1' => __('Otomatis dari database SIMDA'),
					'2' => __('Manual')
				))
				->set_default_value('1')
				->set_help_text('Jika dipilih manual maka SKPD perlu melakuan input manual realisasi anggaran.'),
			Field::make('select', 'crb_default_sumber_dana', 'Sumber dana default ketika sumber dana di sub kegiatan belum disetting')
				->add_options($sumber_dana_all)
				->set_default_value(1)
				->set_help_text('Sumber dana ini akan digunakan di custom mapping sumber dana dan ketika singkron ke SIMDA'),
			Field::make('select', 'crb_default_sumber_pagu_dpa', 'Nilai pagu DPA untuk RFK')
				->add_options(array(
					'1' => 'APBD SIMDA',
					'2' => 'APBD FMIS'
				))
				->set_default_value(1)
				->set_help_text('Nilai pagu DPA pada halaman monitoring data realisasi fisik dan keuangan (RFK)'),
			Field::make('html', 'crb_generate_user_sipd_merah')
				->set_html('<a id="generate_user_sipd_merah" onclick="return false;" href="#" class="button button-primary button-large">Generate User SIPD Merah By DB Lokal</a>')
				->set_help_text('Data user active yang ada di table data_dewan akan digenerate menjadi user wordpress.'),
			Field::make('html', 'crb_sql_migrate')
				->set_html('<a target="_blank" href="' . $url_sql_migrate . '" class="button button-primary button-large">SQL Migrate WP-SIPD</a>')
				->set_help_text('Status SQL migrate WP-SIPD jika ada update struktur database.')
		);
		if (!empty($provinsi_all)) {
			$options_basic[] = Field::make('select', 'crb_id_lokasi_prov', 'ID Lokasi Provinsi')
				->add_options($provinsi_all);
			$options_basic[] = Field::make('select', 'crb_id_lokasi_kokab', 'ID Lokasi Kota/Kabupaten')
				->add_options($kab_kot_all);
		} else {
			$options_basic[] = Field::make('text', 'crb_id_lokasi_prov', 'ID Lokasi Provinsi')
				->set_default_value(0);
			$options_basic[] = Field::make('text', 'crb_id_lokasi_kokab', 'ID Lokasi Kota/Kabupaten')
				->set_default_value(0);
		}
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran order by tahun_anggaran ASC', ARRAY_A);
		$html = '';
		foreach ($tahun as $k => $v) {
			$url_monitor_update = $this->generatePage('Monitoring Update Data SIPD lokal Berdasar Waktu Terakhir Melakukan Singkronisasi Data | ' . $v['tahun_anggaran'], $v['tahun_anggaran']);
			$url_monitor_spd = $this->generatePage('Monitoring Data SPD | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitoring_data_spd tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
			$url_jadwal = $this->generatePage('Setting penjadwalan | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[setting_penjadwalan tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
			$url_monitoring_rup = $this->generatePage('Monitoring RUP | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitoring_rup tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
			$url_jadwal_verifikasi_rka = $this->generatePage('Jadwal Verifikasi RKA SIPD | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[jadwal_verifikasi_rka_sipd tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
			$html .= '
				<h3 class="header-tahun" tahun="' . $v['tahun_anggaran'] . '">Tahun Anggaran ' . $v['tahun_anggaran'] . '</h3>
				<div class="body-tahun" tahun="' . $v['tahun_anggaran'] . '">
					<ul>
						<li><a target="_blank" href="' . $url_monitor_update . '">Halaman Monitor Update Data Lokal SIPD Merah Tahun ' . $v['tahun_anggaran'] . '</a></li>
						<li><a target="_blank" href="' . $url_monitor_spd . '">Halaman Monitor Data SPD (Surat Penyediaan Dana) ' . $v['tahun_anggaran'] . '</a></li>
						<li><a target="_blank" href="' . $url_jadwal . '">Halaman Pengaturan Penjadwalan ' . $v['tahun_anggaran'] . '</a></li>
						<li><a target="_blank" href="' . $url_monitoring_rup . '">Halaman Monitoring RUP ' . $v['tahun_anggaran'] . '</a></li>
						<li><a target="_blank" href="' . $url_jadwal_verifikasi_rka . '">Halaman Pengaturan Jadwal Verifikasi RKA SIPD ' . $v['tahun_anggaran'] . '</a></li>
					</ul>
				</div>
			';
		}
		$options_basic[] = Field::make('html', 'crb_monitoring_sipd')
			->set_html($html);
		return $options_basic;
	}

	public function get_ajax_field($options = array('type' => 'rfk'))
	{
		$ret = array();
		$hide_sidebar = Field::make('html', 'crb_hide_sidebar')
			->set_html('
        		<div id="load_ajax_carbon" data-type="' . $options['type'] . '"></div>
        	');
		$ret[] = $hide_sidebar;
		return $ret;
	}

	public function load_ajax_carbon()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> ''
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if ($_POST['type'] == 'keu_pemdes') {
					return $this->load_ajax_carbon_pemdes($ret);
				}
				$sumber_pagu_dpa = get_option('_crb_default_sumber_pagu_dpa');
				$url_nilai_dpa = '&pagu_dpa=simda';
				if ($sumber_pagu_dpa == 2) {
					$url_nilai_dpa = '&pagu_dpa=fmis';
				}
				$body_all = '';
				$unit_renstra = [];
				$limit = '';
				if ($_POST['type'] == 'input_renstra') {
					$limit = ' LIMIT 1';
				}
				$tahun = $wpdb->get_results(
					'
					select 
						tahun_anggaran 
					from data_unit 
					group by tahun_anggaran 
					order by tahun_anggaran DESC ' . $limit,
					ARRAY_A
				);
				foreach ($tahun as $k => $v) {
					$unit = $wpdb->get_results("
		            	SELECT 
		            		nama_skpd, 
		            		id_skpd, 
		            		kode_skpd, 
		            		nipkepala 
		            	from data_unit 
		            	where active=1 
		            		and tahun_anggaran=" . $v['tahun_anggaran'] . ' 
		            		and is_skpd=1 
		            	order by kode_skpd ASC
		            ', ARRAY_A);
					$body_pemda = '<ul style="margin-left: 20px;">';
					foreach ($unit as $kk => $vv) {
						$subunit = $wpdb->get_results("
		            		SELECT 
		            			nama_skpd, 
		            			id_skpd, 
		            			kode_skpd, 
		            			nipkepala 
		            		from data_unit 
		            		where active=1 
		            			and tahun_anggaran=" . $v['tahun_anggaran'] . " 
		            			and is_skpd=0 
		            			and id_unit=" . $vv["id_skpd"] . " 
		            		order by kode_skpd ASC
		            	", ARRAY_A);

						if ($_POST['type'] == 'rfk') {
							$url_skpd = $this->generatePage('RFK ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rfk tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . $url_nilai_dpa . '">Halaman RFK ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						} else if ($_POST['type'] == 'monev_renja') {
							$url_skpd = $this->generatePage('MONEV ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renja tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman MONEV ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						} else if ($_POST['type'] == 'monev_renstra') {
							$url_skpd = $this->generatePage('MONEV RENSTRA ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renstra tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman MONEV RENSTRA ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						} else if ($_POST['type'] == 'sumber_dana') {
							$this->generatePage('Sumber Dana ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_daftar_sumber_dana tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
						} else if ($_POST['type'] == 'label_komponen') {
							$this->generatePage('Label Komponen ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_daftar_label_komponen tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
						} else if ($_POST['type'] == 'apbdpenjabaran') {
							$url_skpd = $this->generatePage($v['tahun_anggaran'] . ' | ' . $vv['kode_skpd'] . ' | ' . $vv['nama_skpd'] . ' | ' . ' | APBD PENJABARAN Lampiran 2', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="2" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman APBD PENJABARAN Lampiran 2 ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						} else if ($_POST['type'] == 'rekap_satuan_harga') {
							$url_skpd = $this->generatePage('Rekapitulasi Rincian Belanja ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_satuan_harga tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman Chart dan Rekapitulasi Rincian Belanja ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						} else if ($_POST['type'] == 'input_renja') {
							$url_skpd = $this->generatePage('Input RENJA ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[input_renja tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman Input RENJA ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						} else if ($_POST['type'] == 'monev_rak') {
							$url_skpd = $this->generatePage('Rencana Anggaran Kas ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rak tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman RAK ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						} else if ($_POST['type'] == 'spd') {
							$url_skpd = $this->generatePage('Halaman SPD ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[halaman_spd tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman SPD ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						} else if ($_POST['type'] == 'spp') {
							$url_skpd = $this->generatePage('Halaman SPP ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[halaman_spp tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman SPP ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						} else if ($_POST['type'] == 'spm') {
							$url_skpd = $this->generatePage('Halaman SPM ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[halaman_spm tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman SPM ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						} else if ($_POST['type'] == 'sp2d') {
							$url_skpd = $this->generatePage('Halaman SP2D ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[halaman_sp2d tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman SP2D ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						} else if ($_POST['type'] == 'monev_json_rka') {
							$url_skpd = $this->generatePage('Data JSON RKA ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_json_rka tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><input type="checkbox" value="' . $vv['id_skpd'] . '"> <a target="_blank" href="' . $url_skpd . '">Halaman JSON RKA ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ') ID = ' . $vv['id_skpd'];
						} else if ($_POST['type'] == 'input_renstra') {
							if (empty($unit_renstra[$vv['kode_skpd']])) {
								$unit_renstra[$vv['kode_skpd']] = $vv['kode_skpd'];
								$url_skpd = $this->generatePage('Input RENSTRA ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'], null, '[input_renstra id_skpd="' . $vv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman Input RENSTRA ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
							}
						} else if ($_POST['type'] == 'rkpd_renja') {
							$url_skpd = $this->generatePage('RKPD & RENJA ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rkpd_renja tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman RKPD & RENJA ' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
						}

						if (!empty($subunit)) {
							$body_pemda .= '<ul style="margin: 5px 20px;">';
						}
						foreach ($subunit as $kkk => $vvv) {
							if ($_POST['type'] == 'rfk') {
								$url_skpd = $this->generatePage('RFK ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rfk tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . $url_nilai_dpa . '">Halaman RFK ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')</li>';
							} else if ($_POST['type'] == 'monev_renja') {
								$url_skpd = $this->generatePage('MONEV ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renja tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman MONEV ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')</li>';
							} else if ($_POST['type'] == 'monev_renstra') {
								$url_skpd = $this->generatePage('MONEV RENSTRA ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renstra tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman MONEV RENSTRA ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')</li>';
							} else if ($_POST['type'] == 'sumber_dana') {
								$this->generatePage('Sumber Dana ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_daftar_sumber_dana tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
							} else if ($_POST['type'] == 'label_komponen') {
								$this->generatePage('Label Komponen ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_daftar_label_komponen tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
							} else if ($_POST['type'] == 'apbdpenjabaran') {
								$url_skpd = $this->generatePage($v['tahun_anggaran'] . ' | ' . $vvv['kode_skpd'] . ' | ' . $vvv['nama_skpd'] . ' | ' . ' | APBD PENJABARAN Lampiran 2', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="2" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman APBD PENJABARAN Lampiran 2 ' . $vv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')';
							} else if ($_POST['type'] == 'rekap_satuan_harga') {
								$url_skpd = $this->generatePage('Rekapitulasi Rincian Belanja ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_satuan_harga tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman Chart dan Rekapitulasi Rincian Belanja ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')';
							} else if ($_POST['type'] == 'input_renja') {
								$url_skpd = $this->generatePage('Input RENJA ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[input_renja tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman Input RENJA ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')';
							} else if ($_POST['type'] == 'monev_rak') {
								$url_skpd = $this->generatePage('Rencana Anggaran Kas ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rak tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman RAK ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')';
							} else if ($_POST['type'] == 'spd') {
								$url_skpd = $this->generatePage('Halaman SPD ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[halaman_spd tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman SPD ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')';
							} else if ($_POST['type'] == 'spp') {
								$url_skpd = $this->generatePage('Halaman SPP ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[halaman_spp tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman SPP ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')';
							} else if ($_POST['type'] == 'spm') {
								$url_skpd = $this->generatePage('Halaman SPM ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[halaman_spm tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman SPM ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')';
							} else if ($_POST['type'] == 'sp2d') {
								$url_skpd = $this->generatePage('Halaman SP2D ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[halaman_sp2d tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman SP2D ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')';
							} else if ($_POST['type'] == 'monev_json_rka') {
								$url_skpd = $this->generatePage('Data JSON RKA ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_json_rka tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><input type="checkbox" value="' . $vvv['id_skpd'] . '"> <a target="_blank" href="' . $url_skpd . '">Halaman JSON RKA ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ') ID = ' . $vvv['id_skpd'];
							} else if ($_POST['type'] == 'rkpd_renja') {
								$url_skpd = $this->generatePage('RKPD & RENJA ' . $vvv['nama_skpd'] . ' ' . $vvv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rkpd_renja tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vvv['id_skpd'] . '"]');
								$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">Halaman RKPD & RENJA ' . $vvv['kode_skpd'] . ' ' . $vvv['nama_skpd'] . ' ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vvv['nipkepala'] . ')</li>';
							}
						}
						if (!empty($subunit)) {
							$body_pemda .= '</ul>';
						}
						$body_pemda .= '</li>';
					}
					$body_pemda .= '</ul>';

					if ($_POST['type'] != 'input_renstra' && $_POST['type'] != 'pohon_kinerja_renja') {
						$body_all .= '
			            	<h3 class="header-tahun" tahun="' . $v['tahun_anggaran'] . '">Tahun Anggaran ' . $v['tahun_anggaran'] . '</h3>
			            	<div class="body-tahun" tahun="' . $v['tahun_anggaran'] . '">';
					}
					if ($_POST['type'] == 'rfk') {
						$url_pemda = $this->generatePage('Realisasi Fisik dan Keuangan Pemerintah Daerah | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_rfk tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_pemda . $url_nilai_dpa . '">Halaman Realisasi Fisik dan Keuangan Pemerintah Daerah Tahun ' . $v['tahun_anggaran'] . '</a>' . $body_pemda;
					} else if ($_POST['type'] == 'spd') {
						$url_pemda = $this->generatePage('Halaman SK UP | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[sk_up tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_pemda . $url_nilai_dpa . '">Halaman SK UP Tahun ' . $v['tahun_anggaran'] . '</a>' . $body_pemda;
					} else if ($_POST['type'] == 'monev_renja') {
						$url_pemda = $this->generatePage('MONEV RENJA Pemerintah Daerah | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renja tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_pemda . '">Halaman MONEV RENJA Daerah Tahun ' . $v['tahun_anggaran'] . '</a>' . $body_pemda;
					} else if ($_POST['type'] == 'monev_renstra') {
						$url_pemda = $this->generatePage('MONEV RENSTRA Pemerintah Daerah | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_renstra tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_pemda . '">Halaman MONEV RENSTRA Daerah Tahun ' . $v['tahun_anggaran'] . '</a>' . $body_pemda;
					} else if ($_POST['type'] == 'monev_rpjm') {
						$url_pemda = $this->generatePage('MONEV RPJM Pemerintah Daerah | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_monev_rpjm tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_pemda . '">Halaman MONEV RPJM Daerah Tahun ' . $v['tahun_anggaran'] . '</a>' . $body_pemda;
					} else if ($_POST['type'] == 'apbdpenjabaran') {
						$url_penjabaran1 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 1', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="1"]');
						$url_penjabaran2 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 2', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="2"]');
						$url_penjabaran3a = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 3a - Hibah Uang', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="3a"]');
						$url_penjabaran3b = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 3b - Hibang Barang / Jasa', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="3b"]');
						$url_penjabaran4a = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 4a - Bansos Uang', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="4a"]');
						$url_penjabaran4b = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 4b - Bansos Barang / Jasa', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="4b"]');
						$url_penjabaran5a = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 5a - Bankeu', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="5a"]');
						$url_penjabaran5b = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 5b - Bankeu Khusus', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="5b"]');
						$url_penjabaran6a = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 6a  - Bagi Hasil Kab', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="6a"]');
						$url_penjabaran6b = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 6b  - Bagi Hasil Kota', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="6b"]');
						$url_penjabaran6c = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran 6c  - Bagi Hasil Desa', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="6c"]');
						$url_penjabaran_triwulan = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Per Triwulan', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="per_triwulan"]');
						$url_penjabaran_kas_per_urusan = $this->generatePage($v['tahun_anggaran'] . ' | APBD PENJABARAN Anggaran Kas Per Urusan', $v['tahun_anggaran'], '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="kas_per_urusan"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran1 . '">Halaman APBD PENJABARAN Lampiran 1 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran2 . '">Halaman APBD PENJABARAN Lampiran 2 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran3a . '">Halaman APBD PENJABARAN Lampiran 3a - Hibah Uang Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran3b . '">Halaman APBD PENJABARAN Lampiran 3b - Hibang Barang / Jasa Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran4a . '">Halaman APBD PENJABARAN Lampiran 4a - Bansos Uang Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran4b . '">Halaman APBD PENJABARAN Lampiran 4b - Bansos Barang / Jasa Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran5a . '">Halaman APBD PENJABARAN Lampiran 5a - Bankeu Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran5b . '">Halaman APBD PENJABARAN Lampiran 5b - Bankeu Khusus Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran6a . '">Halaman APBD PENJABARAN Lampiran 6a - Bagi Hasil Kab Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran6b . '">Halaman APBD PENJABARAN Lampiran 6b - Bagi Hasil Kota Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran6c . '">Halaman APBD PENJABARAN Lampiran 6c - Bagi Hasil Desa Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran_triwulan . '">Halaman APBD PENJABARAN Per Triwulan Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_penjabaran_kas_per_urusan . '">Halaman APBD PENJABARAN Anggaran Kas Per Urusan Tahun ' . $v['tahun_anggaran'] . '</a>';
						$body_all .= $body_pemda;
					} else if ($_POST['type'] == 'apbdperda') {
						$url_perda1 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PERDA Lampiran 1', $v['tahun_anggaran'], '[apbdperda tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="1"]');
						$url_perda2 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PERDA Lampiran 2', $v['tahun_anggaran'], '[apbdperda tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="2"]');
						$url_perda3 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PERDA Lampiran 3', $v['tahun_anggaran'], '[apbdperda tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="3"]');
						$url_perda4 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PERDA Lampiran 4', $v['tahun_anggaran'], '[apbdperda tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="4"]');
						$url_perda5 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PERDA Lampiran 5', $v['tahun_anggaran'], '[apbdperda tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="5"]');
						$url_perda6 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PERDA Lampiran 6', $v['tahun_anggaran'], '[apbdperda tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="6"]');
						$url_perda7 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PERDA Lampiran 7', $v['tahun_anggaran'], '[apbdperda tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="7"]');
						$url_perda8 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PERDA Lampiran 8', $v['tahun_anggaran'], '[apbdperda tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="8"]');
						$url_perda9 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PERDA Lampiran 9', $v['tahun_anggaran'], '[apbdperda tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="9"]');
						$url_perda10 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PERDA Lampiran 10', $v['tahun_anggaran'], '[apbdperda tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="10"]');
						$url_perda11 = $this->generatePage($v['tahun_anggaran'] . ' | APBD PERDA Lampiran 11', $v['tahun_anggaran'], '[apbdperda tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran="11"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_perda1 . '">Halaman APBD PERDA Lampiran 1 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_perda2 . '">Halaman APBD PERDA Lampiran 2 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_perda3 . '">Halaman APBD PERDA Lampiran 3 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_perda4 . '">Halaman APBD PERDA Lampiran 4 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_perda5 . '">Halaman APBD PERDA Lampiran 5 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_perda6 . '">Halaman APBD PERDA Lampiran 6 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_perda7 . '">Halaman APBD PERDA Lampiran 7 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_perda8 . '">Halaman APBD PERDA Lampiran 8 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_perda9 . '">Halaman APBD PERDA Lampiran 9 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_perda10 . '">Halaman APBD PERDA Lampiran 10 Tahun ' . $v['tahun_anggaran'] . '</a><br>';
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_perda11 . '">Halaman APBD PERDA Lampiran 11 Tahun ' . $v['tahun_anggaran'] . '</a>';
						$body_all .= $body_pemda;
					} else if ($_POST['type'] == 'laporan_penatausahaan') {
						$url_daftar_penguji = $this->generatePage($v['tahun_anggaran'] . ' | Daftar Penguji', $v['tahun_anggaran'], '[daftar_penguji tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_daftar_penguji . '">Halaman Data Daftar Penguji ' . $v['tahun_anggaran'] . '</a><br>';
					} else if ($_POST['type'] == 'monev_satuan_harga') {
						$url_add_new_ssh = $this->generatePage('Data Usulan Standar Satuan Harga (SSH) | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[data_ssh_usulan tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
						$body_all .= '<div style="padding:.75rem 0 0 .75rem;"><a style="font-weight: bold;" target="_blank" href="' . $url_add_new_ssh . '">Halaman Data Usulan SSH ' . $v['tahun_anggaran'] . '</a></div>';
					} else if ($_POST['type'] == 'rekap_satuan_harga') {
						$url_pemda = $this->generatePage('Rekapitulasi Rincian Belanja Pemerintah Daerah ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[data_halaman_menu_ssh tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_pemda . '">Halaman Chart dan Rekapitulasi Rincian Belanja ' . $v['tahun_anggaran'] . '</a><br>' . $body_pemda;
					} else if ($_POST['type'] == 'tidak_terpakai_satuan_harga') {
						$url_pemda = $this->generatePage('Standar Harga Tidak Terpakai ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[ssh_tidak_terpakai tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_pemda . '">Standar Harga Tidak Terpakai ' . $v['tahun_anggaran'] . '</a><br>';
					} else if ($_POST['type'] == 'satuan_harga_sipd') {
						$url_pemda = $this->generatePage('Data Standar Satuan Harga SIPD | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[data_ssh_sipd tahun_anggaran="' . $v['tahun_anggaran'] . '"]', false, 'publish');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_pemda . '">Rekapitulasi Usulan dan Standar Satuan Harga SIPD | ' . $v['tahun_anggaran'] . '</a><br>';
					} else if ($_POST['type'] == 'register_sp2d_fmis') {
						$url_pemda = $this->generatePage('Register Surat Perintah Pencairan Dana (SP2D) Cair FMIS | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[register_sp2d_fmis tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
						$body_all .= '<a style="font-weight: bold;" target="_blank" href="' . $url_pemda . '">Register Surat Perintah Pencairan Dana (SP2D) Cair FMIS | ' . $v['tahun_anggaran'] . '</a><br>';
					} else if ($_POST['type'] == 'input_renja') {
						$body_all .= $body_pemda;
					} else if ($_POST['type'] == 'monev_rak') {
						$body_all .= $body_pemda;
					} else if ($_POST['type'] == 'monev_json_rka') {
						$url_skpd = $this->generatePage('Data JSON RKA | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_json_rka tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
						$body_all .= '<input type="checkbox" class="select-all"> <a onclick="run_url(this); return false;" target="_blank" href="' . $url_skpd . '">Halaman JSON RKA Tahun ' . $v['tahun_anggaran'] . '</a>';
						$body_all .= $body_pemda;
					} else if ($_POST['type'] == 'input_renstra') {
						$body_all .= $body_pemda;
					} else if ($_POST['type'] == 'rkpd_renja') {
						$body_all .= $body_pemda;
					} else if ($_POST['type'] == 'spd') {
						$body_all .= $body_pemda;
					} else if ($_POST['type'] == 'spp') {
						$body_all .= $body_pemda;
					} else if ($_POST['type'] == 'spm') {
						$body_all .= $body_pemda;
					} else if ($_POST['type'] == 'sp2d') {
						$body_all .= $body_pemda;
					}
					if ($_POST['type'] != 'input_renstra' && $_POST['type'] != 'pohon_kinerja_renja') {
						$body_all .= '</div>';
					}
				}

				if($_POST['type'] == 'pohon_kinerja_renja'){
					$data_label = $wpdb->get_results($wpdb->prepare("select distinct namalabel, tahun_anggaran from data_tag_sub_keg where idlabelgiat !=%d and namalabel is not null", 0), ARRAY_A);

					$arr_label = [];
					foreach ($data_label as $label) {
						if(empty($arr_label[$label['tahun_anggaran']])){
							$arr_label[$label['tahun_anggaran']] = [
								'tahun_anggaran' => $label['tahun_anggaran'],
								'data' => [],
							];
						}

						if(empty($arr_label[$label['tahun_anggaran']]['data'][$label['namalabel']])){
							$arr_label[$label['tahun_anggaran']]['data'][$label['namalabel']] = $label['namalabel'];
						}
					}

					foreach ($arr_label as $label) {

						$body_all .= '
			            	<h3 class="header-tahun" tahun="' . $label['tahun_anggaran'] . '">Tahun Anggaran ' . $label['tahun_anggaran'] . '</h3>
			            	<div class="body-tahun" tahun="' . $label['tahun_anggaran'] . '">';

			            foreach ($label['data'] as $namalabel) {

			            	// $url_skpd = $this->generatePage('Input RENSTRA ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'], null, '[input_renstra id_skpd="' . $vv['id_skpd'] . '"]');

			            	$url_label = $this->generatePage('POHON KINERJA RENJA ' . $namalabel . ' ' . $label['tahun_anggaran'], $label['tahun_anggaran'], '[pohon_kinerja_renja tahun_anggaran="' . $label['tahun_anggaran'] . '" namalabel="' . $namalabel . '"]');
								$body_all .= '<a target="_blank" href="'.$url_label.'">POHON KINERJA RENJA ' . $namalabel . '</a></br>';
			            }

			            $body_all .= '</div>';

					}
				}

				if (
					$_POST['type'] == 'rfk'
					|| $_POST['type'] == 'monev_renja'
					|| $_POST['type'] == 'monev_renstra'
					|| $_POST['type'] == 'monev_rpjm'
					|| $_POST['type'] == 'apbdpenjabaran'
					|| $_POST['type'] == 'apbdperda'
					|| $_POST['type'] == 'monev_satuan_harga'
					|| $_POST['type'] == 'rekap_satuan_harga'
					|| $_POST['type'] == 'tidak_terpakai_satuan_harga'
					|| $_POST['type'] == 'satuan_harga_sipd'
					|| $_POST['type'] == 'register_sp2d_fmis'
					|| $_POST['type'] == 'input_renja'
					|| $_POST['type'] == 'monev_rak'
					|| $_POST['type'] == 'monev_json_rka'
					|| $_POST['type'] == 'input_renstra'
					|| $_POST['type'] == 'laporan_penatausahaan'
					|| $_POST['type'] == 'spd'
					|| $_POST['type'] == 'spp'
					|| $_POST['type'] == 'spm'
					|| $_POST['type'] == 'sp2d'
					|| $_POST['type'] == 'rkpd_renja'
					|| $_POST['type'] == 'pohon_kinerja_renja'
				) {
					$ret['message'] = $body_all;
				}
			}
		}
		die(json_encode($ret));
	}

	public function load_ajax_carbon_pemdes($ret)
	{
		global $wpdb;
		$tahun = $wpdb->get_results('
			SELECT 
				tahun_anggaran 
			from data_unit 
			group by tahun_anggaran 
			order by tahun_anggaran DESC
		', ARRAY_A);
		$id_kab = get_option('_crb_id_lokasi_kokab');
		$body_all = '';
		foreach ($tahun as $k => $v) {
			$url_bhpd = $this->generatePage('Laporan Keuangan Pemerintah Desa Bagi Hasil Pajak Daerah (BHPD) ' . $v['tahun_anggaran'], false, '[keu_pemdes_bhpd tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
			$url_bhrd = $this->generatePage('Laporan Keuangan Pemerintah Desa Bagi Hasil Retribusi Daerah (BHRD) ' . $v['tahun_anggaran'], false, '[keu_pemdes_bhrd tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
			$url_dd = $this->generatePage('Laporan Keuangan Pemerintah Desa Bantuan Keuangan Umum (BKU) Desa Dana Desa (DD) ' . $v['tahun_anggaran'], false, '[keu_pemdes_bku_dd tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
			$url_add = $this->generatePage('Laporan Keuangan Pemerintah Desa Bantuan Keuangan Umum (BKU) Alokasi Dana Desa (ADD) ' . $v['tahun_anggaran'], false, '[keu_pemdes_bku_add tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
			$url_bkk_inf = $this->generatePage('Laporan Keuangan Pemerintah Desa Bantuan Keuangan Khusus (BKK) Infrastruktur ' . $v['tahun_anggaran'], false, '[keu_pemdes_bkk_inf tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
			$url_bkk_pilkades = $this->generatePage('Laporan Keuangan Pemerintah Desa Bantuan Keuangan Khusus (BKK) Pilkades ' . $v['tahun_anggaran'], false, '[keu_pemdes_bkk_pilkades tahun_anggaran="' . $v['tahun_anggaran'] . '"]');
			$unit = $wpdb->get_results("
            	SELECT 
            		nama_skpd, 
            		id_skpd, 
            		kode_skpd, 
            		nipkepala 
            	from data_unit 
            	where active=1 
            		and tahun_anggaran=" . $v['tahun_anggaran'] . " 
            		and is_skpd=1 
            		and nama_skpd like 'KECAMATAN %' 
            	order by kode_skpd ASC
            ", ARRAY_A);
			$key_pilkades = "_bkk_pilkades_" . $v['tahun_anggaran'];
			$cek_bkk_pilkades = get_option($key_pilkades, false);
			if (!empty($cek_bkk_pilkades)) {
				$cek_bkk_pilkades = 'checked';
			} else {
				$cek_bkk_pilkades = '';
			}
			$body_pemda = '<ul style="margin-left: 20px;">';
			$body_pemda .= '
				<li><label><input type="checkbox" data-id="' . $key_pilkades . '" ' . $cek_bkk_pilkades . ' onclick="set_setting_ajax(this);"> Tampilkan Data BKK Pilkades</label></li>
				<li><a href="' . $url_bhpd . '" target="_blank">Laporan Keuangan Pemerintah Desa Bagi Hasil Pajak Daerah (BHPD) ' . $v['tahun_anggaran'] . '</a></li>
				<li><a href="' . $url_bhrd . '" target="_blank">Laporan Keuangan Pemerintah Desa Bagi Hasil Retribusi Daerah (BHRD) ' . $v['tahun_anggaran'] . '</a></li>
				<li><a href="' . $url_dd . '" target="_blank">Laporan Keuangan Pemerintah Desa BKU Dana Desa (DD) ' . $v['tahun_anggaran'] . '</a></li>
				<li><a href="' . $url_add . '" target="_blank">Laporan Keuangan Pemerintah Desa BKU Alokasi Dana Desa (ADD) ' . $v['tahun_anggaran'] . '</a></li>
				<li><a href="' . $url_bkk_inf . '" target="_blank">Laporan Keuangan Pemerintah Desa BKK Infrastruktur ' . $v['tahun_anggaran'] . '</a></li>
				<li><a href="' . $url_bkk_pilkades . '" target="_blank">Laporan Keuangan Pemerintah Desa BKK Pilkades ' . $v['tahun_anggaran'] . '</a></li>
			';
			foreach ($unit as $kk => $vv) {
				$url_skpd = $this->generatePage($vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_keu_pemdes tahun_anggaran="' . $v['tahun_anggaran'] . '" id_skpd="' . $vv['id_skpd'] . '"]');
				$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">' . $vv['kode_skpd'] . ' ' . $vv['nama_skpd'] . ' | ' . $v['tahun_anggaran'] . '</a> (NIP: ' . $vv['nipkepala'] . ')';
				if (!empty($id_kab)) {
					$nama_kec = str_replace('kecamatan ', '', strtolower($vv['nama_skpd']));
					$id_kec = $wpdb->get_var("
	            		SELECT 
	            			id_alamat 
	            		from data_alamat 
	            		where tahun=" . $v['tahun_anggaran'] . " 
	            			and is_kec=1 
	            			and id_kab=" . $id_kab . " 
	            			and nama='" . $nama_kec . "'
	            	");
					if (!empty($id_kec)) {
						$desa = $wpdb->get_results("
		            		SELECT 
		            			id_alamat,
		            			nama 
		            		from data_alamat 
		            		where tahun=" . $v['tahun_anggaran'] . " 
		            			and is_kel=1 
		            			and id_kab=" . $id_kab . " 
		            			and id_kec=" . $id_kec . "
		            	", ARRAY_A);
						$body_pemda .= '<li>';
						if (!empty($desa)) {
							$body_pemda .= '<ul style="margin: 5px 20px;">';
						}
						foreach ($desa as $kkk => $vvv) {
							$url_skpd = $this->generatePage($vvv['nama'] . ' | ' . $v['tahun_anggaran'], $v['tahun_anggaran'], '[monitor_keu_pemdes tahun_anggaran="' . $v['tahun_anggaran'] . '" id_kec="' . $id_kec . '" id_kel="' . $vvv['id_alamat'] . '"]');
							$body_pemda .= '<li><a target="_blank" href="' . $url_skpd . '">' . $vvv['nama'] . ' | ' . $v['tahun_anggaran'] . '</a>';
						}
						if (!empty($desa)) {
							$body_pemda .= '</ul>';
						}
						$body_pemda .= '</li>';
					} else {
						$body_pemda .= '<li>Desa/Kelurahan tidak ditemukan!. ' . $wpdb->last_query . '</li>';
					}
				}
			}
			$body_pemda .= '</ul>';
			$body_all .= '
            	<h3 class="header-tahun" tahun="' . $v['tahun_anggaran'] . '">Tahun Anggaran ' . $v['tahun_anggaran'] . '</h3>
            	<div class="body-tahun" tahun="' . $v['tahun_anggaran'] . '">';
			if (empty($id_kab)) {
				$body_all .= '<h4>ID Lokasi Kota/Kabupaten Belum diisi di halaman SIPD Options!</h4>';
			}
			$body_all .= $body_pemda . '</div>';
		}
		$ret['message'] = $body_all;
		die(json_encode($ret));
	}

	public function get_setting_fmis()
	{
		global $wpdb;
		$unit = array();
		$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
		if (empty(!$tahun_anggaran)) {
			$unit = $wpdb->get_results("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd 
				from data_unit 
				where active=1 
					and tahun_anggaran=" . $tahun_anggaran . ' 
				order by id_skpd ASC
			', ARRAY_A);
		}
		$url_mapping_program = $this->generatePage('Data Mapping Data Master FMIS Program', false, '[data_mapping_master_fmis type="program"]');
		$url_mapping_kegiatan = $this->generatePage('Data Mapping Data Master FMIS Kegiatan', false, '[data_mapping_master_fmis type="kegiatan"]');
		$url_mapping_sub_kegiatan = $this->generatePage('Data Mapping Data Master FMIS Sub Kegiatan', false, '[data_mapping_master_fmis type="sub_kegiatan"]');
		$url_mapping_rekening = $this->generatePage('Data Mapping Data Master FMIS Rekening', false, '[data_mapping_master_fmis type="rekening"]');
		$mapping_unit = array();
		$mapping_unit[] = Field::make('html', 'crb_fmis_keterangan')
			->set_html('<h3>Tahun anggaran WP-SIPD: ' . $tahun_anggaran . '</h3>Informasi terkait integrasi data WP-SIPD ke FMIS bisa dicek di <a href="https://smkasiyahhomeschooling.blogspot.com/2021/12/fmis-chrome-extension-untuk-integrasi.html" target="blank">https://smkasiyahhomeschooling.blogspot.com/2021/12/fmis-chrome-extension-untuk-integrasi.html</a>.');
		$mapping_unit[] = Field::make('textarea', 'crb_custom_mapping_rekening_fmis', 'Custom Mapping Rekening Antara SIPD dan FMIS')
			->set_help_text('Data ini untuk mengakomodir perbedaan kode rekening yang ada di SIPD dan FMIS. Contoh pengisian data sebagai berikut 5.1.01.88.88.8888-5.2.2.16.1.3 data dipisah dengan pemisah "," (koma). Formatnya adalah <b>kode_rek_sipd-kode_rek_fmis</b>. <h4><a target="_blank" href="' . $url_mapping_rekening . '">Data Mapping Data Master FMIS Rekening</a></h4>');
		$mapping_unit[] = Field::make('textarea', 'crb_custom_mapping_subkeg_fmis', 'Custom Mapping Sub Kegiatan SIPD dan FMIS')
			->set_help_text('Data ini untuk mengakomodir perbedaan nama sub kegiatan yang ada di SIPD dan FMIS. Contoh pengisian data sebagai berikut [nama_subkeg]-[nama_subkeg] data dipisah dengan pemisah "," (koma). Video tutorial <a href="https://youtu.be/7J_k8NEAZLM" target="_blank">https://youtu.be/7J_k8NEAZLM</a>. <h4><a target="_blank" href="' . $url_mapping_sub_kegiatan . '">Data Mapping Data Master FMIS Sub Kegiatan</a></h4>');
		$mapping_unit[] = Field::make('textarea', 'crb_custom_mapping_keg_fmis', 'Custom Mapping Kegiatan SIPD dan FMIS')
			->set_help_text('Data ini untuk mengakomodir perbedaan nama kegiatan yang ada di SIPD dan FMIS. Contoh pengisian data sebagai berikut [nama_kegiatan]-[nama_kegiatan] data dipisah dengan pemisah "," (koma). Video tutorial <a href="https://youtu.be/7J_k8NEAZLM" target="_blank">https://youtu.be/7J_k8NEAZLM</a>. <h4><a target="_blank" href="' . $url_mapping_kegiatan . '">Data Mapping Data Master FMIS Kegiatan</a></h4>');
		$mapping_unit[] = Field::make('textarea', 'crb_custom_mapping_program_fmis', 'Custom Mapping Program SIPD dan FMIS')
			->set_help_text('Data ini untuk mengakomodir perbedaan nama program yang ada di SIPD dan FMIS. Contoh pengisian data sebagai berikut [nama_program]-[nama_program] data dipisah dengan pemisah "," (koma). Video tutorial <a href="https://youtu.be/7J_k8NEAZLM" target="_blank">https://youtu.be/7J_k8NEAZLM</a>. <h4><a target="_blank" href="' . $url_mapping_program . '">Data Mapping Data Master FMIS Program</a></h4>');
		$mapping_unit[] = Field::make('textarea', 'crb_custom_mapping_pindah_subkeg_fmis', 'Pindah Sub Kegiatan SIPD ke FMIS')
			->set_default_value('1.02.02.2.02.39-{"kode_sub_giat":"1.02.02.2.01.22", "nama_program":"PROGRAM PEMENUHAN UPAYA KESEHATAN PERORANGAN DAN UPAYA KESEHATAN MASYARAKAT", "nama_giat":"Penyediaan Fasilitas Pelayanan Kesehatan untuk UKM dan UKP Kewenangan Daerah Kabupaten/Kota", "nama_sub_giat":"1.02.02.2.01.22 Pengelolaan Pelayanan Kesehatan Dasar Melalui Pendekatan Keluarga"}')
			->set_help_text('Data ini untuk mengakomodir pindah sub kegiatan dari SIPD ke FMIS. Contoh pengisian data sebagai berikut <h4>1.02.02.2.02.39-{"kode_sub_giat":"1.02.02.2.01.22", "nama_program":"PROGRAM PEMENUHAN UPAYA KESEHATAN PERORANGAN DAN UPAYA KESEHATAN MASYARAKAT", "nama_giat":"Penyediaan Fasilitas Pelayanan Kesehatan untuk UKM dan UKP Kewenangan Daerah Kabupaten/Kota", "nama_sub_giat":"1.02.02.2.01.22 Pengelolaan Pelayanan Kesehatan Dasar Melalui Pendekatan Keluarga"}</h4><h4>(kode_sub_kegiatan_SIPD-data_JSON_sub_kegiatan_FMIS)</h4> data dipisah dengan pemisah "#" (tanda pagar).');
		$mapping_unit[] = Field::make('html', 'crb_fmis_keterangan_mapping')
			->set_html('Mapping SKPD berisi ID dari Unit dan Sub Unit FMIS. Data ID SKPD FMIS dapat dilihat pada form edit atau tambah user. ID dipisahkan dengan delimiter "." (titik). Contoh jika ID dari Unit Dindik adalah 50 dan ID dari sub Unit Dindik adalah 70, maka penulisanya adalah <b>50.70</b>.');
		$mapping_unit[] = Field::make('radio', 'crb_fmis_pagu', __('Nilai Rincian yang dikirim ke FMIS'))
			->add_options(array(
				'1' => __('Nilai Terakhir'),
				'2' => __('Sebelum Perubahan')
			))
			->set_default_value('1')
			->set_help_text('Pilihan ini untuk opsi yang dipakai saat penarikan data dijadwal pergeseran atau perubahan. Jika masih jadwal APBD Murni maka pilih <b>Nilai Terakhir</b>.');
		$mapping_unit[] = Field::make('radio', 'crb_fmis_aktivitas', __('Jenis Aktivitas FMIS '))
			->add_options(array(
				'1' => __('1 / Single Aktivitas'),
				'2' => __('Multi Aktivitas')
			))
			->set_default_value('1')
			->set_help_text('Jika sumber dana belum divalidasi kebenarannya maka sebaiknya pilih jenis aktivitas single. Referensi cara melakukan monitoring rekap sumber dana bisa dicek di <a href="https://youtu.be/JMFIXXbY_6c" target="_blank">https://youtu.be/JMFIXXbY_6c</a>. Aktivitas di FMIS menjadi dasar pencairan anggaran di proses penatausahaan, harus konsisten dari awal tahun agar proses berjalan lancar.');
		$mapping_unit[] = Field::make('radio', 'crb_backup_rincian_fmis', __('Apakah nilai rincian FMIS akan ikut di backup ke database lokal saat melakukan singkronisasi data?'))
			->add_options(array(
				'1' => __('Iya'),
				'2' => __('Tidak')
			))
			->set_default_value('2')
			->set_help_text('Jika Iya, maka data rincian FMIS akan disimpan di tabel <b>data_rincian_fmis</b>. Hal ini akan berpengaruh kepada lama tidak nya proses singkornisasi data dari wp-sipd ke FMIS. Jika dipilih Tidak, proses singkronisasi data akan lebih cepat.');
		foreach ($unit as $k => $v) {
			$mapping_unit[] = Field::make('text', 'crb_unit_fmis_' . $tahun_anggaran . '_' . $v['id_skpd'], ($k + 1) . '. Kode Sub Unit FMIS untuk ' . $v['kode_skpd'] . ' ' . $v['nama_skpd']);
		}
		return $mapping_unit;
	}

	public function get_setting_keu_pemdes()
	{
		global $wpdb;
		$url_beranda = $this->generatePage('Halaman Beranda', false, '[keu_pemdes_beranda]');
		$url_per_kecamatan = $this->generatePage('Laporan Realisasi Keuangan Desa per Kecamatan', false, '[laporan_keu_pemdes_per_kecamatan]');
		$setting = array(
			Field::make('html', 'crb_keu_pemdes_page')
				->set_html('<ul>
				<li><a href="' . $url_beranda . '" target="_blank">Halaman Beranda</a></li>
				<li><a href="' . $url_per_kecamatan . '" target="_blank">Laporan Realisasi Keuangan Desa per Kecamatan</a></li>
			</ul>')
		);
		$setting = array_merge($setting, $this->get_ajax_field(array('type' => 'keu_pemdes')));
		return $setting;
	}

	public function get_setting_sipkd()
	{
		global $wpdb;
		$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
		$url_akun = $this->generatePage('Singkronisasi Akun', false, '[sipkd_data_akun type="sipkd_akun"]');
		$url_urusan = $this->generatePage('Sinkronisasi Urusan/Bidang Urusan', false, '[sipkd_data_urusan type="sipkd_urusan"]');
		$url_program = $this->generatePage('Singkronisasi Program', false, '[sipkd_data_program type="sipkd_program"]');
		$url_giat = $this->generatePage('Singkronisasi Kegiatan dan Sub Kegiatan', false, '[sipkd_data_giat type="sipkd_giat"]');
		$url_dana = $this->generatePage('Singkronisasi Sumber Dana', false, '[sipkd_data_sumber_dana type="sipkd_sumber_dana"]');
		$url_kua = $this->generatePage('Singkronisasi KUA/PPAS', false, '[sipkd_data_kua type="sipkd_kuappa"]');
		$url_raskd = $this->generatePage('Singkronisasi RKA Pendapatan', false, '[sipkd_data_raskd type="sipkd_raskd"]');
		$url_raskr = $this->generatePage('Singkronisasi RKA Belanja', false, '[sipkd_data_raskr type="sipkd_raskr"]');
		$url_raskb = $this->generatePage('Singkronisasi RKA Pembiayaan', false, '[sipkd_data_raskb type="sipkd_raskb"]');
		$setting = array(
			Field::make('html', 'crb_singkron_sipkd')
				->set_html('<ul>
				<li><a href="' . $url_akun . '" target="_blank">Singkron Akun</a></li>
				<li><a href="' . $url_urusan . '" target="_blank">Singkron Urusan/Bidang Urusan/SKPD</a></li>
				<li><a href="' . $url_program . '" target="_blank">Singkron Program</a></li>
				<li><a href="' . $url_giat . '" target="_blank">Singkron Kegiatan/ Sub Kegiatan</a></li>
				<li><a href="' . $url_dana . '" target="_blank">Singkron Sumber Dana</a></li>
				<li><a href="' . $url_kua . '" target="_blank">Singkron KUA/PPAS</a></li>
				<li><a href="' . $url_raskd . '" target="_blank">Singkron RKA Pendapatan</a></li>
				<li><a href="' . $url_raskr . '" target="_blank">Singkron RKA Belanja</a></li>
				<li><a href="' . $url_raskb . '" target="_blank">Singkron RKA Pembiayaan</a></li>
			</ul>'),
			Field::make('text', 'crb_host_sipkd', "IP Server Database SIPKD")
				->set_help_text("Alamat server Database SIPKD"),
			Field::make('text', 'crb_port_sipkd', "Port Database SIPKD")
				->set_help_text("Port Database SIPKD"),
			Field::make('text', 'crb_user_sipkd', "User Database SIPKD")
				->set_help_text("User Database SIPKD"),
			Field::make('text', "crb_pass_sipkd", "Password Database SIPKD")
				->set_help_text("Password Database SIPKD"),
			Field::make('text', 'crb_dbname_sipkd', "Nama Database SIPKD")
				->set_help_text("Nama Database SIPKD"),
			Field::make('radio', 'crb_versi_sipkd', "Versi Aplikasi SIPKD")
				->add_options(array(
					"1" => __("Versi 6.2"),
					"2" => __("Versi 6.3")
				))
				->set_default_value("2")
				->set_help_text("Versi Aplikasi SIPKD")
		);

		return $setting;
	}

	public function get_mapping_unit()
	{
		global $wpdb;
		$unit = array();
		$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
		if (empty(!$tahun_anggaran)) {
			$unit = $wpdb->get_results("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd 
				from data_unit 
				where active=1 
					and tahun_anggaran=" . $tahun_anggaran . ' 
				order by id_skpd ASC
			', ARRAY_A);
		}
		$mapping_unit = array(
			Field::make('radio', 'crb_singkron_simda', __('Auto Singkron ke DB SIMDA'))
				->add_options(array(
					'1' => __('Ya'),
					'2' => __('Tidak')
				))
				->set_default_value('2')
				->set_help_text('Data SIMDA akan terupdate otomatis ketika melakukan singkron DB Lokal menggunakan chrome extension.'),
			Field::make('text', 'crb_url_api_simda', 'URL API SIMDA')
				->set_help_text('Scirpt PHP SIMDA API dibuat terpisah di <a href="https://github.com/agusnurwanto/SIMDA-API-PHP" target="_blank">SIMDA API PHP</a>.'),
			Field::make('text', 'crb_timeout_simda', 'MAX TIMEOUT API SIMDA')
				->set_default_value(10)
				->set_help_text('Setting maksimal timout request CURL ke API SIMDA dalam hitungan detik.'),
			Field::make('text', 'crb_apikey_simda', 'APIKEY SIMDA')
				->set_default_value($this->generateRandomString()),
			Field::make('text', 'crb_db_simda', 'Database SIMDA'),
			Field::make('radio', 'crb_singkron_simda_debug', __('Debug API integrasi SIMDA'))
				->add_options(array(
					'1' => __('Ya'),
					'2' => __('Tidak')
				))
				->set_default_value('2')
				->set_help_text('Debug API SIMDA agar notif error muncul di respon API wordpress. Untuk melakukan debug extension buka <b>halaman extension > background page > network</b>.'),
			Field::make('radio', 'crb_singkron_simda_unit', __('Integrasi Sub Unit SIMDA sesuai SIPD'))
				->add_options(array(
					'1' => __('Ya'),
					'2' => __('Tidak')
				))
				->set_default_value('2')
				->set_help_text('Jika sub unit simda (ref_unit dan ref_sub_unit) belum dibuat maka data akan diisi otomatis ketika melakukan integrasi perangkat daerah. Dan tanpa merubah yang sudah diisi.'),
			Field::make('radio', 'crb_auto_ref_kegiatan_mapping', __('Otomatis insert ke ref_kegiatan dan ref_kegiatan_mapping jika tidak ada di SIMDA'))
				->add_options(array(
					'1' => __('Ya'),
					'2' => __('Tidak')
				))
				->set_default_value('2')
				->set_help_text('Jika data di ref_program belum ada, juga akan diinput otomatis dengan kode default <b>kd_prog >= 150 dan kd_keg >= 150</b> sebagai tanda auto create by WP-SIPD.'),
			Field::make('radio', 'crb_auto_ref_rek_mapping', __('Otomatis insert ke ref_rek_1, ref_rek_2, ref_rek_3, ref_rek_4, ref_rek_5 dan ref_rek_mapping jika tidak ada di SIMDA'))
				->add_options(array(
					'1' => __('Ya'),
					'2' => __('Tidak')
				))
				->set_default_value('2')
				->set_help_text('Kode default <b>kd_rek_3 += 100, kd_rek_4 += 100 dan kd_rek_5 += 100</b> sebagai tanda auto create by WP-SIPD.'),
			Field::make('radio', 'crb_simda_pagu', __('Nilai Rincian yang dikirim ke SIMDA'))
				->add_options(array(
					'1' => __('Nilai Terakhir'),
					'2' => __('Sebelum Perubahan')
				))
				->set_default_value('1')
				->set_help_text('Pilihan ini untuk dipakai saat jadwal pergeseran atau perubahan. Jika masih jadwal APBD Murni maka pilih <b>Nilai Terakhir</b>.'),
			Field::make('textarea', 'crb_custom_mapping_sub_keg_simda', 'Custom Mapping Sub Kegiatan SIPD ke SIMDA')
				->set_help_text('Data ini untuk mengakomodir perbedaan kode sub kegiatan yang ada di SIPD dan SIMDA. Juga perbedaan mapping sub kegiatan ke Sub Unit SIMDA. Contoh pengisian data sebagai berikut 5.02.0.00.0.00.04.0000_5.02.02.2.01.05-4.04.01.02_4.04.18.08 data dipisah dengan pemisah "," (koma). Formatnya adalah <b>kodeSkpdSipd_kodeSubKeg-kodeSubUnitSimda_kodeRefKegiatan</b>.')
		);

		$cek_status_koneksi_simda = $this->simda->CurlSimda(array(
			'query' => 'select * from ref_setting',
			'no_debug' => true
		));
		$ket_simda = '<b style="color:red">Belum terkoneksi ke simda!</b>';
		if (!empty($cek_status_koneksi_simda[0]) && !empty($cek_status_koneksi_simda[0]->lastdbaplver)) {
			$ket_simda = '<b style="color: green">Terkoneksi database SIMDA versi ' . $cek_status_koneksi_simda[0]->lastdbaplver . '</b>';
		}
		$mapping_unit[] = Field::make('html', 'crb_status_simda')
			->set_html('Status koneksi SQL server SIMDA: ' . $ket_simda);

		$mapping_unit[] = Field::make('html', 'crb_mapping_unit_simda')
			->set_html('Mapping kode sub unit SIPD ke SIMDA. Format kode (kd_urusan.kd_bidang.kd_unit.kd_sub) dipisah dengan titik. Contoh untuk Dinas Pendidikan (1.1.1.1). <b>Setelah melakukan mapping kode unit secara manual maka HARUS DILAKUKAN SINGKRON ULANG PERANGKAT DAERAH agar data di ta_sub_unit terisi semua!</b>.');
		foreach ($unit as $k => $v) {
			$unit_simda = get_option('_crb_unit_' . $v['id_skpd']);
			if (empty($unit_simda)) {
				$kd = explode('.', $v['kode_skpd']);
				$default_val = $this->simda->CurlSimda(array(
					'query' => 'select * from ref_bidang_mapping where kd_urusan90=' . $kd[0] . ' and kd_bidang90=' . ((int)$kd[1])
				));
				if (!empty($default_val)){
					$default = $default_val[0]->kd_urusan . '.' . $default_val[0]->kd_bidang;
					update_option('_crb_unit_' . $v['id_skpd'], $default);
				}
			}
			$mapping_unit[] = Field::make('text', 'crb_unit_' . $v['id_skpd'], ($k + 1) . '. Kode Sub Unit SIMDA untuk ' . $v['kode_skpd'] . ' ' . $v['nama_skpd']);
		}
		return $mapping_unit;
	}

	public function get_skpd_settings()
	{
		global $wpdb;
		$unit = array();
		$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
		if (empty(!$tahun_anggaran)) {
			$unit = $wpdb->get_results("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd 
				from data_unit 
				where active=1 
					and tahun_anggaran=" . $tahun_anggaran . ' 
				order by id_skpd ASC
			', ARRAY_A);
		}
		$sub_unit = array();
		$pilih_skpd = array();
		foreach ($unit as $k => $v) {
			$sub_unit[] = Field::make('text', 'crb_skpd_alamat_' . $v['id_skpd'], ($k + 1) . '. Alamat kantor untuk ' . $v['kode_skpd'] . ' ' . $v['nama_skpd']);
			$pilih_skpd[$v['id_skpd']] = $v['kode_skpd'] . ' ' . $v['nama_skpd'];
		}
		$settings = array(
			Field::make('select', 'crb_skpd_admin_ssh', 'Pilih unit kerja penyusun Standar Harga (SSH / SBU / HSPK / ASB)')
				->add_options($pilih_skpd)
				->set_help_text('Nama unit kerja ini untuk ditampilkan di surat usulan Standar Harga.'),
			Field::make('text', 'crb_lokasi', 'Nama daerah atau lokasi tanda tangan surat')
		);
		return array_merge($settings, $sub_unit);
	}

	public function get_api_setting()
	{
		global $wpdb;
		$unit = array();
		$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
		if (empty(!$tahun_anggaran)) {
			$unit = $wpdb->get_results("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd 
				from data_unit 
				where active=1 
					and tahun_anggaran=" . $tahun_anggaran . ' 
				order by id_skpd ASC
			', ARRAY_A);
		}

		$disabled = 'onclick="get_sinkron_modul_migrasi_data(); return false;"';
		if (
			get_option('_crb_url_server_modul_migrasi_data') == admin_url(
				'admin-ajax.php'
					|| empty(get_option('_crb_url_server_modul_migrasi_data'))
			)
		) {
			$disabled = 'disabled';
		}

		$title = 'Dokumentasi API WP-SIPD';
		$shortcode = '[dokumentasi_api_wpsipd]';
		$url_api = $this->generatePage($title, false, $shortcode);
		$mapping_unit = array(
			Field::make('html', 'crb_url_tahun_anggaran_moduld_migrasi_data')
				->set_html('
					<h3>Tahun Anggaran: ' . $tahun_anggaran . '</h3>
					<h4>Halaman Terkait:</h4>
					<ol>
						<li><a href="' . $url_api . '" target="_blank">' . $title . '</a></li>
					</ol>
				'),
			Field::make('text', 'crb_url_server_modul_migrasi_data', 'URL Server Modul Migrasi Data')
				->set_default_value(admin_url('admin-ajax.php')),
			Field::make('text', 'crb_apikey_server_modul_migrasi_data', 'APIKEY Server Modul Migrasi Data')
				->set_default_value(get_option('_crb_api_key_extension')),
			Field::make('html', 'crb_html_get_sinkron_modul_migrasi_data')
				->set_html('<a href="#" class="button button-primary" ' . $disabled . '>Sinkron data dari server migrasi data</a>')
				->set_help_text($this->last_sinkron_api_setting())
		);

		return $mapping_unit;
	}

	public function get_sirup_setting()
	{
		global $wpdb;
		$unit = array();
		$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
		if (empty(!$tahun_anggaran)) {
			$unit = $wpdb->get_results("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd 
				from data_unit 
				where active=1 
					and tahun_anggaran=" . $tahun_anggaran . ' 
					and is_skpd=1 
				order by id_skpd ASC
			', ARRAY_A);
		}

		$disabled_sirup = 'onclick="get_sinkron_data_sirup(); return false;"';
		if (get_option('_crb_id_lokasi_sirup') == 0 || empty(get_option('_crb_id_lokasi_sirup'))) {
			$disabled_sirup = 'disabled';
		}

		$mapping_unit = array(
			Field::make('html', 'crb_url_tahun_anggaran_sirup')
				->set_html('<h3>Tahun Anggaran: ' . $tahun_anggaran . '</h3>'),
			Field::make('text', 'crb_id_lokasi_sirup', 'ID lokasi')
				->set_default_value(0)
				->set_help_text('Cara mendapatkan id lokasi ada <a href="https://sirup.lkpp.go.id/sirup/ro/caripaket2" target="_blank">disni</a>'),
			Field::make('text', 'crb_id_kldi_sirup', 'ID K/L/D/I')
				->set_default_value(0)
				->set_help_text('Cara mendapatkan id k/l/d/i ada <a href="https://sirup.lkpp.go.id/sirup/ro/caripaket2" target="_blank">disni</a>'),
			Field::make('html', 'crb_html_get_sinkron_data_sirup')
				->set_html('<a href="#" class="button button-primary" ' . $disabled_sirup . '>Sinkron data dari server SIRUP</a>')
				->set_help_text($this->last_sinkron_data_sirup())
		);

		foreach ($unit as $k => $v) {
			$unit_sirup = get_option('_crb_unit_sirup_' . $v['id_skpd']);
			// if(empty($unit_sirup) || $unit_sirup == '.'){
			$id = $wpdb->get_var("
					select 
						id_satuan_kerja 
					from data_skpd_sirup 
					where active=1 
						and tahun_anggaran=" . $tahun_anggaran . " 
						and satuan_kerja='" . strtoupper($v['nama_skpd']) . "'
				");
			if (!empty($id)) {
				update_option('_crb_unit_sirup_' . $v['id_skpd'], $id);
			}
			// }
			$mapping_unit[] = Field::make('text', 'crb_unit_sirup_' . $v['id_skpd'], ($k + 1) . '. ID Saker SIRUP untuk ' . $v['kode_skpd'] . ' ' . $v['nama_skpd']);
		}

		return $mapping_unit;
	}

	public function get_wpsipd_menu_setting()
	{
		$field = '';
		$field = array(
			Field::make('separator', 'crb_show_menu_dashboard_user_settings', 'Menu Dashboard User'),
			Field::make('multiselect', 'crb_daftar_tombol_user_dashboard', 'Daftar tombol di halaman dashboard user')
				->add_options(array(
					'1' => __('MONEV RFK'),
					'2' => __('MONEV SUMBER DANA'),
					'3' => __('MONEV LABEL KOMPONEN'),
					'4' => __('MONEV INDIKATOR RENJA'),
					'5' => __('MONEV INDIKATOR RENSTRA'),
					'6' => __('MONEV INDIKATOR RPJM'),
					'7' => __('MENU SSH'),
					'8' => __('INPUT RENSTRA'),
					'9' => __('INPUT RENJA'),
					'10' => __('INPUT REALISASI KEU PEMDES'),
					'11' => __('USER PPTK')
				))
				->set_default_value(array('1', '2', '3', '4', '5'))
				->set_help_text('Daftar fitur ini akan ditampilkan dalam bentuk tombol di halaman dasboard user setelah berhasil login.'),

			Field::make('separator', 'crb_show_menu_wpsipd_settings', 'Non Aktifkan Menu ( WP-SIPD Settings )'),
			Field::make('checkbox', 'crb_show_menu_wpsipd_api_settings', 'API Settings')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_wpsipd_skpd_settings', 'SKPD Settings')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_wpsipd_simda_settings', 'SIMDA Settings')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_wpsipd_fmis_settings', 'FMIS Settings')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_wpsipd_sipkd_settings', 'SIPKD Settings')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_wpsipd_sirup_settings', 'SIRUP Settings')
				->set_option_value('true'),

			Field::make('separator', 'crb_show_menu_monev_settings', 'Non Aktifkan Menu ( MONEV SIPD )'),
			Field::make('checkbox', 'crb_show_menu_monev_monev_sipd_settings', 'Monev SIPD')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_monev_rfk_settings', 'RFK')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_monev_indi_rpjm_settings', 'Indikator RPJM')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_monev_indi_renstra_settings', 'Indikator RENSTRA')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_monev_indi_renja_settings', 'Indikator RENJA')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_monev_lab_komponen_settings', 'Label Komponen')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_monev_sumber_dana_settings', 'Sumber Dana')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_monev_monev_rak_settings', 'Monev RAK')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_spd', 'Halaman SPD')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_spp', 'Halaman SPP')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_spm', 'Halaman SPM')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_sp2d', 'Halaman SP2D')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_monev_json_rka_settings', 'Data JSON RKA')
				->set_option_value('true'),

			Field::make('separator', 'crb_show_menu_laporan_settings', 'Non Aktifkan Menu ( LAPORAN SIPD )'),
			Field::make('checkbox', 'crb_show_menu_laporan_sipd_settings', 'Laporan SIPD')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_laporan_label_subkeg_settings', 'Tag/Label Sub Kegiatan')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_laporan_rkpd_settings', 'RKPD & RENJA')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_laporan_apbd_penjabaran_settings', 'APBD Penjabaran')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_laporan_apbd_perda_settings', 'APBD Perda')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_laporan_rpjm_renstra_settings', 'RPJM & RENSTRA')
				->set_option_value('true'),

			Field::make('separator', 'crb_show_menu_input_perencanaan_settings', 'Non Aktifkan Menu ( Input Perencanaan )'),
			Field::make('checkbox', 'crb_show_menu_input_sipd_settings', 'Input Perencanaan')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_input_jadwal_settings', 'Jadwal & Input Perencanaan')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_input_input_renstra_settings', 'Input RENSTRA')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_input_input_renja_settings', 'Input RENJA')
				->set_option_value('true'),

			Field::make('separator', 'crb_show_menu_keuangan_pemdes_settings', 'Non Aktifkan Menu ( Keuangan Pemdes )'),
			Field::make('checkbox', 'crb_show_menu_keuangan_keuangan_pemdes_settings', 'Keuangan Pemdes')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_keuangan_beranda_settings', 'Tampilan Beranda')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_keuangan_import_bkk_infra_settings', 'Import BKK Insfrastruktur')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_keuangan_import_bkk_pilkades_settings', 'Import BKK Pilkades')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_keuangan_import_bhpd_settings', 'Import BHPD')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_keuangan_import_bhrd_settings', 'Import BHRD')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_keuangan_import_bku_dd_settings', 'Import BKU DD')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_keuangan_import_bku_add_settings', 'Import BKU ADD')
				->set_option_value('true'),

			Field::make('separator', 'crb_show_menu_standar_harga_settings', 'Non Aktifkan Menu ( Standar Harga )'),
			Field::make('checkbox', 'crb_show_menu_standar_standar_harga_settings', 'Standar Harga')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_standar_usulan_standar_harga_settings', 'Usulan Standar Harga')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_standar_rekap_usulan_settings', 'Rekap Usulan dan Standar Harga SIPD')
				->set_option_value('true'),
			Field::make('checkbox', 'crb_show_menu_standar_tidak_terpakai_settings', 'Tidak Terpakai di SIPD')
				->set_option_value('true'),

			Field::make('separator', 'crb_show_menu_verifikasi_rka_settings', 'Non Aktifkan Menu ( Verifikasi RKA )'),
			Field::make('checkbox', 'crb_show_menu_verifikasi_rka_check_settings', 'Verifikasi RKA')
				->set_option_value('true'),

			Field::make('separator', 'crb_show_menu_monev_fmis_settings', 'Non Aktifkan Menu ( MONEV FMIS )'),
			Field::make('checkbox', 'crb_show_menu_monev_fmis_check_settings', 'MONEV FMIS')
				->set_option_value('true'),
		);
		return $field;
	}

	// hook filter untuk save field carbon field
	public function crb_edit_save($save, $value, $field)
	{
		if ($field->get_name() == '_crb_label_komponen') {
			return "";
		} else {
			return $value;
		}
	}

	public function generate_sumber_dana()
	{
		global $wpdb;
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran order by tahun_anggaran ASC', ARRAY_A);
		$options_tahun = array();
		$tahun_skr = get_option('_crb_tahun_anggaran_sipd');
		if (empty($tahun_skr)) {
			$tahun_skr = date('Y');
		}
		foreach ($tahun as $k => $v) {
			$selected = '';
			if ($tahun_skr == $v['tahun_anggaran']) {
				$selected = 'selected';
			}
			$options_tahun[] = '<option ' . $selected . ' value="' . $v['tahun_anggaran'] . '">' . $v['tahun_anggaran'] . '</option>';
		}
		$label = array(
			Field::make('html', 'crb_daftar_label_komponen')
				->set_html('
            		<style>
            			.postbox-container { display: none; }
            			#poststuff #post-body.columns-2 { margin: 0 !important; }
            		</style>
            		<div class="text_tengah" style="margin-bottom: 1em;">
	            		<h3>Daftar Sumber Dana</h3>
	            		<select style="margin-bottom: 15px; width: 200px;" id="pilih_tahun" onchange="format_sumberdana();">
	            			<option value="0">Pilih Tahun</option>
	            			' . implode('', $options_tahun) . '
	            		</select>
	            		<select style="margin-bottom: 15px; margin-left: 25px; min-width: 200px;" id="pilih_skpd" onchange="format_sumberdana();">
	            			<option value="-1">Pilih SKPD</option>
	            		</select>
	            		<br>
	            		<label><input type="radio" name="format-sd" format-id="1" onclick="format_sumberdana();"> Format Per Sumber Dana SIPD</label>
	            		<label style="margin-left: 25px;"><input type="radio" name="format-sd" format-id="3" onclick="format_sumberdana();"> Format Kombinasi Sumber Dana SIPD</label>
	            		<label style="margin-left: 25px;"><input type="radio" checked name="format-sd" format-id="2" onclick="format_sumberdana();"> Format Per Sumber Dana Mapping</label>
	            	</div>
	            	<div id="tabel_monev_sumber_dana">
	            	</div>
        		')
		);

		// walau tidak ditampilkan tapi code dibawah ini adalah untuk menggenerate page sumber dana per skpd
		$label = array_merge($label, $this->get_ajax_field(array('type' => 'sumber_dana')));
		return $label;
	}

	public function get_list_skpd()
	{
		global $wpdb;
		$tahun = $_POST['tahun_anggaran'];
		$options_skpd = array();
		$unit = $wpdb->get_results("
			SELECT 
				nama_skpd, 
				id_skpd, 
				kode_skpd, 
				nipkepala 
			from data_unit 
			where active=1 
				and tahun_anggaran=" . $tahun . ' 
				and is_skpd=1 
			order by kode_skpd ASC
		', ARRAY_A);
		foreach ($unit as $kk => $vv) {
			$options_skpd[] = $vv;
			$subunit = $wpdb->get_results("
        		SELECT 
        			nama_skpd, 
        			id_skpd, 
        			kode_skpd, 
        			nipkepala 
        		from data_unit 
        		where active=1 
        			and tahun_anggaran=" . $tahun . " 
        			and is_skpd=0 
        			and id_unit=" . $vv["id_skpd"] . " 
        		order by kode_skpd ASC
        	", ARRAY_A);
			foreach ($subunit as $kkk => $vvv) {
				$vvv['kode_skpd'] = '-- ' . $vvv['kode_skpd'];
				$options_skpd[] = $vvv;
			}
		}
		die(json_encode($options_skpd));
	}

	public function generate_sumber_dana_format()
	{
		global $wpdb;
		$format = $_POST['format'];
		$tahun = $_POST['tahun_anggaran'];
		$id_skpd = $_POST['id_skpd'];

		// sumber dana asli sipd
		if ($format == 1) {
			if (!empty($id_skpd)) {
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
			} else {
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
				$title = 'Laporan APBD Per Sumber Dana ' . $val['kodedana'] . ' ' . $val['namadana'] . ' | ' . $tahun;
				$shortcode = '[monitor_sumber_dana tahun_anggaran="' . $tahun . '" id_sumber_dana="' . $val['iddana'] . '"]';
				$update = false;
				$url_skpd = $this->generatePage($title, $tahun, $shortcode, $update);
				if (!empty($id_skpd)) {
					$url_skpd .= '&id_skpd=' . $id_skpd;
				}
				if (empty($val['kodedana'])) {
					$val['kodedana'] = '';
					$val['namadana'] = 'Belum Di Setting';
				}
				$master_sumberdana .= '
					<tr>
						<td class="text_tengah">' . $no . '</td>
						<td>' . $val['kodedana'] . '</td>
						<td><a href="' . $url_skpd . '&mapping=1" target="_blank">' . $val['namadana'] . '</a></td>
						<td class="text_kanan">' . number_format($val['pagudana'], 0, ",", ".") . '</td>
						<td class="text_tengah">' . $val['jml'] . '</td>
						<td class="text_tengah">' . $val['iddana'] . '</td>
						<td class="text_tengah">' . $tahun . '</td>
					</tr>
				';
				$total_sd += $val['pagudana'];
			}
			if (!empty($id_skpd)) {
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

				$title = 'RFK ' . $skpd[0]['nama_skpd'] . ' ' . $skpd[0]['kode_skpd'] . ' | ' . $tahun;
				$shortcode = '[monitor_rfk tahun_anggaran="' . $tahun . '" id_skpd="' . $skpd[0]['id_skpd'] . '"]';
				$update = false;
				$url_skpd = $this->generatePage($title, $tahun, $shortcode, $update);
			} else {
				$total_rka = $wpdb->get_results($wpdb->prepare('
					select 
						sum(pagu) as total_rka
					from data_sub_keg_bl 
					where tahun_anggaran=%d
						and active=1
				', $tahun), ARRAY_A);
				$title = 'Realisasi Fisik dan Keuangan Pemerintah Daerah | ' . $tahun;
				$shortcode = '[monitor_rfk tahun_anggaran="' . $tahun . '"]';
				$update = false;
				$url_skpd = $this->generatePage($title, $tahun, $shortcode, $update);
			}
			$master_sumberdana .= '
				<tr class="text_blok">
					<td class="text_tengah" colspan="3">Total Pagu Sumber Dana Tahun ' . $tahun . '</td>
					<td class="text_kanan">' . number_format($total_sd, 0, ",", ".") . '</td>
					<td class="text_tengah" colspan="2">Total RKA</td>
					<td class="text_tengah"><a target="_blank" href="' . $url_skpd . '">' . number_format($total_rka[0]['total_rka'], 0, ",", ".") . '</a></td>
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
        				' . $master_sumberdana . '
        			</tbody>
        		</table>
    		';
			die($tabel);
		}
		// sumber dana mapping
		else if ($format == 2) {
			$data_all = array();
			$total_harga = 0;
			$realisasi = 0;
			$jml_rincian = 0;
			if (!empty($id_skpd)) {
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
					$list_kode_sbl[] = "'" . $sub['kode_sbl'] . "'";
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
	    				and r.kode_sbl IN (' . implode(',', $list_kode_sbl) . ')
	    		', $tahun), ARRAY_A);
			} else {
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
				if (empty($rka['realisasi'])) {
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
				if (!empty($mapping_db)) {
					foreach ($mapping_db as $mapping) {
						if (empty($data_all[$mapping['kode_dana']])) {
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
				} else {
					if (empty($data_all['0'])) {
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
				if (empty($v['id_dana'])) {
					$title = 'Laporan APBD Per Sumber Dana   | ' . $tahun;
				} else {
					$title = 'Laporan APBD Per Sumber Dana ' . $v['kode_dana'] . ' ' . $v['nama_dana'] . ' | ' . $tahun;
				}
				$shortcode = '[monitor_sumber_dana tahun_anggaran="' . $tahun . '" id_sumber_dana="' . $v['id_dana'] . '"]';
				$update = false;
				$url_skpd = $this->generatePage($title, $tahun, $shortcode, $update) . '&mapping=2';
				if (!empty($id_skpd)) {
					$url_skpd .= '&id_skpd=' . $id_skpd;
				}
				$master_sumberdana .= '
	    			<tr>
	    				<td class="text_tengah">' . $no . '</td>
	    				<td>' . $v['kode_dana'] . '</td>
	    				<td><a href="' . $url_skpd . '" target="_blank">' . $v['nama_dana'] . '</a></td>
	    				<td class="text_kanan">' . number_format($v['pagu'], 0, ",", ".") . '</td>
	    				<td class="text_kanan">' . number_format($v['realisasi'], 0, ",", ".") . '</td>
	    				<td class="text_tengah">' . number_format($v['jml_rincian'], 0, ",", ".") . '</td>
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
        				' . $master_sumberdana . '
        				<tr class="text_blok">
		    				<td class="text_tengah" colspan="3">Total</td>
		    				<td class="text_kanan">' . number_format($total_harga, 0, ",", ".") . '</td>
		    				<td class="text_kanan">' . number_format($realisasi, 0, ",", ".") . '</td>
		    				<td class="text_tengah">' . number_format($jml_rincian, 0, ",", ".") . '</td>
		    			</tr>
        			</tbody>
        		</table>
    		';
			die($tabel);
		}
		// sumber dana kombinasi
		else if ($format == 3) {
			if (!empty($id_skpd)) {
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
			} else {
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
				if (empty($data_all[$_kode_sd])) {
					$title = 'Laporan APBD Per Sumber Dana ' . $_kombinasi_kode_sd . ' ' . $_kombinasi_nama_sd . ' | ' . $tahun;
					$shortcode = '[monitor_sumber_dana tahun_anggaran="' . $tahun . '" id_sumber_dana="' . $_kombinasi_id_sd . '"]';
					$update = false;
					$url_skpd = $this->generatePage($title, $tahun, $shortcode, $update);
					if (!empty($id_skpd)) {
						$url_skpd .= "&id_skpd=" . $id_skpd . "&mapping=3";
					} else {
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
				if (empty($data_all[$_kode_sd]['raw_pagu_dana'][$sub['kode_sbl']])) {
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
						if (empty($pagu_dana[$iddana])) {
							$pagu_dana[$iddana] = 0;
						}
						$pagu_dana[$iddana] += $dana;
						$total_sd += $dana;
					}
				}
				foreach ($pagu_dana as $iddana => $pagu) {
					$pagu_dana[$iddana] = number_format($pagu, 0, ",", ".");
				}
				$master_sumberdana .= '
					<tr>
						<td class="text_tengah">' . $no . '</td>
						<td>' . $val['kode_sd'] . '</td>
						<td><a href="' . $val['url_page'] . '" data-title="' . $val['title'] . '" target="_blank">' . $val['nama_sd'] . '</a></td>
						<td class="text_kanan">' . implode('<br>', $pagu_dana) . '</td>
						<td class="text_kanan">' . number_format($val['pagu_rka'], 0, ",", ".") . '</td>
						<td class="text_tengah">' . $val['jml_sub'] . '</td>
					</tr>
				';
				$jml_sub += $val['jml_sub'];
				$total_rka += $val['pagu_rka'];
			}
			$title = 'Realisasi Fisik dan Keuangan Pemerintah Daerah | ' . $tahun;
			$shortcode = '[monitor_rfk tahun_anggaran="' . $tahun . '"]';
			$update = false;
			$url_skpd = $this->generatePage($title, $tahun, $shortcode, $update);
			$master_sumberdana .= '
				<tr class="text_blok">
					<td class="text_tengah" colspan="3">Total</td>
					<td class="text_kanan">' . number_format($total_sd, 0, ",", ".") . '</td>
					<td class="text_kanan"><a target="_blank" href="' . $url_skpd . '">' . number_format($total_rka, 0, ",", ".") . '</a></td>
					<td class="text_tengah">' . number_format($jml_sub, 0, ",", ".") . '</td>
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
        				' . $master_sumberdana . '
        			</tbody>
        		</table>
    		';
			die($tabel);
		}
	}

	public function generate_jadwal_perencanaan()
	{
		global $wpdb;
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran', ARRAY_A);
		$list_data = '';

		$title = 'Jadwal Input Perencanaan RPJPD';
		$shortcode = '[jadwal_rpjpd]';
		$update = false;
		$page_url = $this->generatePage($title, false, $shortcode, $update);
		$list_data .= '<li><a href="' . $page_url . '" target="_blank">' . $title . '</a></li>';

		$title = 'Input Perencanaan RPJPD';
		$shortcode = '[input_rpjpd]';
		$update = false;
		$page_url = $this->generatePage($title, false, $shortcode, $update);
		$list_data .= '<li><a href="' . $page_url . '" target="_blank">' . $title . '</a></li>';

		$title = 'Jadwal Input Perencanaan RPJM';
		$shortcode = '[jadwal_rpjm]';
		$update = false;
		$page_url = $this->generatePage($title, false, $shortcode, $update);
		$list_data .= '<li><a href="' . $page_url . '" target="_blank">' . $title . '</a></li>';

		$title = 'Input Perencanaan RPJM';
		$shortcode = '[input_rpjm]';
		$update = false;
		$page_url = $this->generatePage($title, false, $shortcode, $update);
		$list_data .= '<li><a href="' . $page_url . '" target="_blank">' . $title . '</a></li>';

		$title = 'Jadwal Input Perencanaan RPD';
		$shortcode = '[jadwal_rpd]';
		$update = false;
		$page_url = $this->generatePage($title, false, $shortcode, $update);
		$list_data .= '<li><a href="' . $page_url . '" target="_blank">' . $title . '</a></li>';

		$title = 'Input Perencanaan RPD';
		$shortcode = '[input_rpd]';
		$update = false;
		$page_url = $this->generatePage($title, false, $shortcode, $update);
		$list_data .= '<li><a href="' . $page_url . '" target="_blank">' . $title . '</a></li>';

		$title = 'Jadwal Input Perencanaan RENSTRA';
		$shortcode = '[jadwal_renstra]';
		$update = false;
		$page_url = $this->generatePage($title, false, $shortcode, $update);
		$list_data .= '<li><a href="' . $page_url . '" target="_blank">' . $title . '</a></li>';

		$no = 0;
		foreach ($tahun as $k => $v) {
			$title = 'Jadwal Input Perencanaan RENJA | ' . $v['tahun_anggaran'];
			$shortcode = '[jadwal_renja tahun_anggaran="' . $v['tahun_anggaran'] . '"]';
			$update = false;
			$page_url = $this->generatePage($title, $v['tahun_anggaran'], $shortcode, $update);
			$list_data .= '<li><a href="' . $page_url . '" target="_blank">' . $title . '</a></li>';
		}
		foreach ($tahun as $k => $v) {
			$title = 'Input Batasan Pagu per-Sumber Dana | ' . $v['tahun_anggaran'];
			$shortcode = '[input_batasan_pagu_per_sumber_dana tahun_anggaran="' . $v['tahun_anggaran'] . '"]';
			$update = false;
			$page_url = $this->generatePage($title, $v['tahun_anggaran'], $shortcode, $update);
			$list_data .= '<li><a href="' . $page_url . '" target="_blank">' . $title . '</a></li>';
		}
		$label = array(
			Field::make('html', 'crb_jadwal_perencanaan')
				->set_html('
            		<ol>' . $list_data . '</ol>
            	')
		);
		return $label;
	}

	public function generate_input_renja()
	{
		global $wpdb;
		$label = $this->get_ajax_field(array('type' => 'input_renja'));
		return $label;
	}

	public function generate_input_renstra()
	{
		global $wpdb;
		$label = $this->get_ajax_field(array('type' => 'input_renstra'));
		return $label;
	}

	public function generate_tag_sipd()
	{
		global $wpdb;
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran', ARRAY_A);
		$master_tag = '';
		$no = 0;
		foreach ($tahun as $k => $v) {
			$no++;
			$nama_page = 'Mandatory Spending | ' . $v['tahun_anggaran'];
			$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
			$master_tag .= '
				<tr>
					<td class="text_tengah">' . $no . '</td>
					<td><a href="' . get_permalink($custom_post) . '" target="_blank">Semua Label di tahun ' . $v['tahun_anggaran'] . '</a></td>
					<td class="text_tengah">' . $v['tahun_anggaran'] . '</td>
				</tr>
			';
			$label_tag = $wpdb->get_results('
				select 
					idlabelgiat,
					namalabel
				from data_tag_sub_keg 
				where tahun_anggaran=' . $v['tahun_anggaran'] . '
					and active=1
					and idlabelgiat!=0
				group by idlabelgiat
				order by idlabelgiat ASC
			', ARRAY_A);
			foreach ($label_tag as $key => $val) {
				$no++;
				$title = 'Laporan APBD Per Tag/Label Sub Kegiatan ' . $val['namalabel'] . ' | ' . $v['tahun_anggaran'];
				$shortcode = '[apbdpenjabaran tahun_anggaran="' . $v['tahun_anggaran'] . '" lampiran=99 idlabelgiat="' . $val['idlabelgiat'] . '"]';
				$update = false;
				$url_tag = $this->generatePage($title, $v['tahun_anggaran'], $shortcode, $update);
				$master_tag .= '
					<tr data-idlabelgiat="' . $val['idlabelgiat'] . '">
						<td class="text_tengah">' . $no . '</td>
						<td><a href="' . $url_tag . '" target="_blank" style="padding-left: 20px;">' . $val['namalabel'] . '</a></td>
						<td class="text_tengah">' . $v['tahun_anggaran'] . '</td>
					</tr>
				';
			}
		}
		$label = array(
			Field::make('html', 'crb_daftar_tag_label_sub_kegiatan')
				->set_html('
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
            				' . $master_tag . '
            			</tbody>
            		</table>
        		')
		);
		return $label;
	}

	public function generate_label_komponen()
	{
		global $wpdb;
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran', ARRAY_A);
		$tahun_anggaran = array();
		foreach ($tahun as $k => $v) {
			$tahun_anggaran[$v['tahun_anggaran']] = $v['tahun_anggaran'];
		}
		$label = array(
			Field::make('select', 'crb_tahun_anggaran', __('Pilih Tahun Anggaran'))
				->add_options($tahun_anggaran),
			Field::make('html', 'crb_daftar_label_komponen')
				->set_html('
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
        		')
		);
		$label = array_merge($label, $this->get_ajax_field(array('type' => 'label_komponen')));
		return $label;
	}

	public function generate_siencang_page()
	{
		$nama_page = 'SIPD to SIENCANG';
		$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
		// print_r($custom_post); die();

		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post(array(
				'post_title'	=> $nama_page,
				'post_content'	=> '[tampilsiencang]',
				'post_type'		=> 'page',
				'post_status'	=> 'publish'
			));
			$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
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

	public function generate_simda_page()
	{
		$nama_page = 'SIPD to SIMDA BPKP';
		$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
		// print_r($custom_post); die();

		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post(array(
				'post_title'	=> $nama_page,
				'post_content'	=> '[tampilsimda]',
				'post_type'		=> 'page',
				'post_status'	=> 'publish'
			));
			$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
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

	public function generate_sibangda_page()
	{
		$nama_page = 'SIPD to SIBANGDA Bina Pembangunan Kemendagri';
		$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
		// print_r($custom_post); die();

		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post(array(
				'post_title'	=> $nama_page,
				'post_content'	=> '[tampilsibangda]',
				'post_type'		=> 'page',
				'post_status'	=> 'publish'
			));
			$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
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

	public function generate_sinergi_page()
	{
		$nama_page = 'SIPD to SINERGI DJPK Kementrian Keuangan';
		$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
		// print_r($custom_post); die();

		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post(array(
				'post_title'	=> $nama_page,
				'post_content'	=> '[tampilsinergi]',
				'post_type'		=> 'page',
				'post_status'	=> 'publish'
			));
			$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
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

	public function generate_sirup_page()
	{
		$nama_page = 'SIPD to SIRUP LKPP';
		$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
		// print_r($custom_post); die();

		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post(array(
				'post_title'	=> $nama_page,
				'post_content'	=> '[tampilsirup]',
				'post_type'		=> 'page',
				'post_status'	=> 'publish'
			));
			$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
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

	public function generate_input_rka_lokal()
	{
		global $wpdb;
		$label = $this->get_ajax_field(array('type' => 'input_rka_lokal'));
		return $label;
	}

	function allow_access_private_post()
	{
		if (
			!empty($_GET)
			&& !empty($_GET['key'])
		) {
			$key = base64_decode($_GET['key']);
			$key_db = md5(get_option('_crb_api_key_extension'));
			$key = explode($key_db, $key);
			$valid = 0;
			if (
				!empty($key[1])
				&& $key[0] == $key[1]
				&& is_numeric($key[1])
			) {
				$tgl1 = new DateTime();
				$date = substr($key[1], 0, strlen($key[1]) - 3);
				$tgl2 = new DateTime(date('Y-m-d', $date));
				$valid = $tgl2->diff($tgl1)->days + 1;
			}
			if ($valid == 1) {
				global $wp_query;
				// print_r($wp_query);
				// print_r($wp_query->queried_object); die('tes');
				if (!empty($wp_query->queried_object)) {
					if ($wp_query->queried_object->post_status == 'private') {
						wp_update_post(array(
							'ID'    =>  $wp_query->queried_object->ID,
							'post_status'   =>  'publish'
						));
						if (!empty($_GET['private'])) {
							die('<script>window.location =  window.location.href;</script>');
						} else {
							die('<script>window.location =  window.location.href+"&private=1";</script>');
						}
					} else if (!empty($_GET['private'])) {
						wp_update_post(array(
							'ID'    =>  $wp_query->queried_object->ID,
							'post_status'   =>  'private'
						));
					}
				} else if ($wp_query->found_posts >= 1) {
					global $wpdb;
					$sql = $wp_query->request;
					$post = $wpdb->get_results($sql, ARRAY_A);
					if (!empty($post)) {
						if (empty($post[0]['post_status'])) {
							return;
						}
						if ($post[0]['post_status'] == 'private') {
							wp_update_post(array(
								'ID'    =>  $post[0]['ID'],
								'post_status'   =>  'publish'
							));
							if (!empty($_GET['private'])) {
								die('<script>window.location =  window.location.href;</script>');
							} else {
								die('<script>window.location =  window.location.href+"&private=1";</script>');
							}
						} else if (!empty($_GET['private'])) {
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

	function get_label_komponen()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> '<tr><td colspan="4" style="text-align: center;">Data Label Komponen kosong</td></tr>',
			'data'		=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$data_label_komponen = $wpdb->get_results("select id, nama, keterangan from data_label_komponen where tahun_anggaran=" . $_POST['tahun_anggaran'], ARRAY_A);
				$body = '';
				foreach ($data_label_komponen as $k => $v) {
					$title = 'Laporan APBD Per Label Komponen "' . $v['nama'] . '" | ' . $_POST['tahun_anggaran'];
					$shortcode = '[monitor_label_komponen tahun_anggaran="' . $_POST['tahun_anggaran'] . '" id_label="' . $v['id'] . '"]';
					$update = false;
					$url_label = $this->generatePage($title, $_POST['tahun_anggaran'], $shortcode, $update);
					$body .= '
					<tr>
						<td class="text_tengah">' . ($k + 1) . '</td>
						<td><a href="' . $url_label . '" target="_blank">' . $v['nama'] . '</a></td>
						<td>' . $v['keterangan'] . '</td>
						<td class="text_kanan pagu-rincian">-</td>
						<td class="text_kanan realisasi-rincian">-</td>
						<td class="text_kanan jml-rincian">-</td>
						<td class="text_tengah"><span style="" data-id="' . $v['id'] . '" class="edit-label"><i class="dashicons dashicons-edit"></i></span> | <span style="" data-id="' . $v['id'] . '" class="hapus-label"><i class="dashicons dashicons-no-alt"></i></span></td>
					</tr>
					';
				}
				if (!empty($body)) {
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

	function get_analis_rincian_label()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil get analisa label komponen!',
			'data'		=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
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
					$v['pagu'] = number_format($v['pagu'], 0, ",", ".");
					$v['realisasi'] = number_format($v['realisasi'], 0, ",", ".");
					$v['jml_rincian'] = number_format($v['jml_rincian'], 0, ",", ".");
					$ret['data'][] = $v;
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	function simpan_data_label_komponen()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil simpan data label komponen!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$cek_exist = $wpdb->get_var(
					$wpdb->prepare('
					SELECT 
						id 
					from data_label_komponen 
					where tahun_anggaran=%d 
						and active=1 
						and nama=%s
					', $_POST['tahun_anggaran'], $_POST['nama'])
				);
				// cek jika belum ada atau update label
				if (
					!$cek_exist
					|| !empty($_POST['id_label'])
				) {
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

	function hapus_data_label_komponen()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil hapus data label komponen!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
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

	function simpan_mapping()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil simpan data mapping sumber dana dan label komponen!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$current_user = wp_get_current_user();
				if (!empty($_POST['id_mapping'])) {
					$ids = explode('-', $_POST['id_mapping']);
					$kd_sbl = $ids[0];
					$rek = explode('.', $ids[1]);
					$rek_1 = $rek[0] . '.' . $rek[1];
					$rek_2 = false;
					$rek_3 = false;
					$rek_4 = false;
					$rek_5 = false;
					$kelompok = 0;
					$keterangan = 0;
					$ids_rinci = array();
					if (isset($rek[2])) {
						$rek_2 = $rek_1 . '.' . $rek[2];
						$where = ' ';
					}
					if (isset($rek[3])) {
						$rek_3 = $rek_2 . '.' . $rek[3];
					}
					if (isset($rek[4])) {
						$rek_4 = $rek_3 . '.' . $rek[4];
					}
					if (isset($rek[5])) {
						$rek_5 = $rek_4 . '.' . $rek[5];
					}
					$where = '';
					if (isset($ids[2])) {
						$kelompok = $ids[2];
						$subs_bl_teks = $wpdb->get_var($wpdb->prepare(
							'
							select 
								subs_bl_teks 
							from data_rka 
							where tahun_anggaran=%d
								and id=%d',
							$_POST['tahun_anggaran'],
							$kelompok
						));
						$where .= ' and subs_bl_teks=\'' . $subs_bl_teks . '\'';
					}
					if (isset($ids[3])) {
						$keterangan = $ids[3];
						$ket_bl_teks = $wpdb->get_var($wpdb->prepare(
							'
							select 
								ket_bl_teks 
							from data_rka 
							where tahun_anggaran=%d
								and id=%d',
							$_POST['tahun_anggaran'],
							$keterangan
						));
						$where .= ' and ket_bl_teks=\'' . $ket_bl_teks . '\'';
					}
					if (isset($ids[4])) {
						$ids_rinci[] = array(
							'kode_akun' => $rek_5,
							'id_rinci' => $ids[4],
							'kelompok' => $kelompok,
							'keterangan' => $keterangan
						);
						$cek = $wpdb->get_var($wpdb->prepare(
							'
							select 
								id 
							from data_realisasi_rincian 
							where tahun_anggaran=%d
								and id_rinci_sub_bl=%d',
							$_POST['tahun_anggaran'],
							$ids[4]
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
					} else {
						$data_rinci = $wpdb->get_results(
							$wpdb->prepare(
								"
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
									" . $where . "
								Order by kode_akun ASC, subs_bl_teks ASC, ket_bl_teks ASC",
								$_POST['tahun_anggaran'],
								$kd_sbl,
								$ids[1] . '%'
							),
							ARRAY_A
						);
						$id_kelompok = array();
						$id_keterangan = array();
						foreach ($data_rinci as $k => $v) {
							$key_ket = $v['kode_akun'] . '-' . $v['subs_bl_teks'] . '-' . $v['ket_bl_teks'];
							if (empty($id_kelompok[$key_ket])) {
								$id_kelompok[$key_ket] = $v['id'];
							}
							if (empty($id_keterangan[$key_ket])) {
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
						$mapping_id[] = $kd_sbl . '-' . $data_rinci['kode_akun'] . '-' . $data_rinci['kelompok'] . '-' . $data_rinci['keterangan'] . '-' . $id_rinci;
						$wpdb->update(
							'data_mapping_label',
							array(
								'user' => $current_user->display_name,
								'active' => 0,
								'update_at' => current_time('mysql')
							),
							array(
								'tahun_anggaran' => $_POST['tahun_anggaran'],
								'id_rinci_sub_bl' => $id_rinci
							)
						);
						foreach ($_POST['id_label'] as $k => $id_label) {
							$cek = $wpdb->get_var($wpdb->prepare(
								'
								select 
									id 
								from data_mapping_label 
								where tahun_anggaran=%d
									and id_rinci_sub_bl=%d 
									and id_label_komponen=%d',
								$_POST['tahun_anggaran'],
								$id_rinci,
								$id_label
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
						$wpdb->update(
							'data_mapping_sumberdana',
							array(
								'user' => $current_user->display_name,
								'active' => 0,
								'update_at' => current_time('mysql')
							),
							array(
								'tahun_anggaran' => $_POST['tahun_anggaran'],
								'id_rinci_sub_bl' => $id_rinci
							)
						);
						foreach ($_POST['id_sumberdana'] as $k => $id_sumberdana) {
							if (empty($id_sumberdana)) {
								continue;
							}
							$cek = $wpdb->get_var($wpdb->prepare(
								'
								select 
									id 
								from data_mapping_sumberdana 
								where tahun_anggaran=%d
									and id_rinci_sub_bl=%d 
									and id_sumber_dana=%d',
								$_POST['tahun_anggaran'],
								$id_rinci,
								$id_sumberdana
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
				} else {
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

	function sumberdana_sipd_lokal_ke_wp_sipd()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkronisasi sumber dana dari SIPD Lokal ke WP-SIPD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$current_user = wp_get_current_user();
				if (!empty($_POST['id_skpd'])) {
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
				} else {
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
					if (!empty($dana)) {
						$iddana = $dana[0]['iddana'];
					} else {
						$iddana = get_option('_crb_default_sumber_dana');
					}
					$rka = $wpdb->get_results("
						SELECT
							id_rinci_sub_bl
						FROM data_rka
						where tahun_anggaran=" . $sub['tahun_anggaran'] . "
							and kode_sbl='" . $sub['kode_sbl'] . "'
							and active=1
					", ARRAY_A);
					foreach ($rka as $rinci) {
						$wpdb->update(
							'data_mapping_sumberdana',
							array(
								'user' => $current_user->display_name,
								'active' => 0,
								'update_at' => current_time('mysql')
							),
							array(
								'tahun_anggaran' => $sub['tahun_anggaran'],
								'id_rinci_sub_bl' => $rinci['id_rinci_sub_bl']
							)
						);
						$cek = $wpdb->get_var($wpdb->prepare(
							'
							select 
								id 
							from data_mapping_sumberdana 
							where tahun_anggaran=%d
								and id_rinci_sub_bl=%d 
								and id_sumber_dana=%d',
							$sub['tahun_anggaran'],
							$rinci['id_rinci_sub_bl'],
							$iddana
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

	function sumberdana_wp_sipd_ke_rka_simda()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkronisasi sumber dana dari WP-SIPD ke RKA SIMDA!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$current_user = wp_get_current_user();
				$debug = false;
				if (get_option('_crb_singkron_simda_debug') == 1) {
					$debug = true;
				}
				if (!empty($_POST['id_skpd'])) {
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
				} else {
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
					if ($res['status'] == 'error') {
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

	function generate_lisensi($callback = false)
	{
		$cek = true;
		if (empty($_POST['server'])) {
			$cek = false;
			$pesan = 'Server WP-SIPD wajib diisi!';
		} else if (empty($_POST['api_key_server'])) {
			$cek = false;
			$pesan = 'API KEY Server WP-SIPD wajib diisi!';
		} else if (empty($_POST['no_wa'])) {
			$cek = false;
			$pesan = 'Nomor WA wajib diisi!';
		} else if (empty($_POST['pemda'])) {
			$cek = false;
			$pesan = 'Nama Pemda wajib diisi!';
		}
		if (true == $cek) {
			$url = $_POST['server'];
			$api_key_wp_sipd = $_POST['api_key_server'];
			$no_wa = $_POST['no_wa'];
			$nama_pemda = $_POST['pemda'];
			update_option('_crb_server_wp_sipd', $url);
			update_option('_crb_server_wp_sipd_api_key', $api_key_wp_sipd);
			update_option('_crb_no_wa', $no_wa);
			update_option('_crb_daerah', $nama_pemda);
			$api_params = array(
				'action' => 'generate_lisensi_bn',
				'api_key' => $api_key_wp_sipd,
				'no_wa' => $no_wa,
				'nama_pemda' => $nama_pemda,
				'produk' => 'WP-SIPD',
				'domain' => site_url(),
			);
			$req = http_build_query($api_params);
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $req,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_CONNECTTIMEOUT => 0,
				CURLOPT_TIMEOUT => 10000
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
				$ret = array(
					'status' => 'error',
					'message' => "cURL Error #:" . $err . " (" . $url . ")"
				);
			} else {
				$ret = json_decode($response);
				if (
					!empty($ret)
					&& $ret->status == 'success'
				) {
					if ($ret->order->bn_status_wpsipd == 'active') {
						update_option('_crb_waktu_lisensi_selesai', $ret->order->bn_waktu_selesai);
					} else {
						update_option('_crb_waktu_lisensi_selesai', '');
					}
					update_option('_crb_status_lisensi', $ret->order->bn_status_wpsipd);
					update_option('_crb_status_lisensi_ket', $ret->message);
					update_option('_crb_api_key_extension', $ret->lisensi);
				} else {
					if (
						!empty($ret)
						&& !empty($ret->error)
					) {
						$response = $ret->message;
					}
					$ret = array(
						'status' => 'error',
						'message' => $response
					);
				}
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message' => $pesan
			);
		}
		if ($callback) {
			return json_encode($ret);
		} else {
			die(json_encode(array(
				'url' => $url,
				'params' => $api_params,
				'response' => $ret
			)));
		}
	}

	public function cek_lisensi_ext()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'run'		=> $_POST['run'],
			'message'	=> 'Berhasil cek lisensi aktif!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$sipd_url = get_option('_crb_server_wp_sipd');
				$sipd_url = explode('/wp-admin', $sipd_url);
				$ret['sipd_url'] = $sipd_url[0];

				$_POST['server'] = get_option('_crb_server_wp_sipd');
				$_POST['api_key_server'] = get_option('_crb_server_wp_sipd_api_key');
				$_POST['no_wa'] = get_option('_crb_no_wa');
				$_POST['pemda'] = get_option('_crb_daerah');
				$response = json_decode($this->generate_lisensi(true));

				$ret['status'] = $response->status;
				if ($response->status == 'success') {
					if(empty($response->order)){
						$ret['status'] = 'success';
						$ret['message'] = 'Lisensi gagal create order ke server';
					}else{
						$ret['api_key'] = $response->lisensi;
						$ret['status_key'] = $response->order->bn_status_wpsipd;
						$ret['pesan_key'] = $response->order->bn_status_wpsipd_message;
						if ($ret['status_key'] != 'active') {
							$ret['sipd_url'] = site_url() . '/' . $ret['status_key'] . '/';
							$ret['status'] = $ret['status_key'];
							$ret['message'] = $ret['pesan_key'];
						}
					}
				} else if($_POST['api_key'] == 'xxxxxxxx-xxxx-xxxx-xxxx'){
					$ret['status'] = 'success';
					$ret['message'] = 'Lisensi belum terkoneksi ke server';
				} else {
					$ret['sipd_url'] = site_url();
					$ret['message'] = $response->message;
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	function get_api_modul_migrasi_data()
	{
		global $wpdb;
		$cek = true;
		$unit = array();
		$api_key_server = get_option('_crb_apikey_server_modul_migrasi_data');
		$api_key_param = $_POST['api_key'];
		$tahun_anggaran = $_POST['tahun_anggaran'];
		if (empty($api_key_param)) {
			$cek = false;
			$pesan = 'API KEY server modul migrasi data wajib diisi!';
		}
		if (empty($tahun_anggaran)) {
			$cek = false;
			$pesan = 'tahun anggaran wajib diisi!';
		}
		if (true == $cek) {
			if ($api_key_server === $api_key_param) {
				if (!empty($tahun_anggaran)) {
					$unit = $wpdb->get_results($wpdb->prepare(
						'
					SELECT 
					* 
					from data_unit 
					where active=1 
						and tahun_anggaran=%d 
					order by id_skpd ASC',
						$tahun_anggaran
					), ARRAY_A);
				}

				if (!empty($unit)) {
					$ret = array(
						'status' => 'success',
						'message' => 'Data berhasil ditemukan',
						'data'	=> $unit
					);
				} else {
					$ret = array(
						'status' => 'error',
						'message' => 'Data tidak ditemukan',
						'data'	=> $unit
					);
				}
			} else {
				$ret = array(
					'status' => 'error',
					'message' => 'APIKEY tidak valid',
					'data'	=> $unit
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message' => $pesan,
				'data'	=> $unit
			);
		}

		echo json_encode($ret);

		die();
	}

	function get_sinkron_modul_migrasi_data()
	{
		global $wpdb;

		if (empty(get_option('_crb_url_server_modul_migrasi_data'))) {
			$data = array(
				'status' => 'error',
				'message' => 'URL server modul migrasi data tidak boleh kosong',
				'last_sinkron' => ''
			);
			if (get_option('_crb_url_server_modul_migrasi_data') == admin_url('admin-ajax.php')) {
				$data = array(
					'status' => 'error',
					'message' => 'URL server modul migrasi data tidak boleh sama dengan url server RFK',
					'last_sinkron' => ''
				);
			}

			$response = json_encode($data);

			die($response);
		}

		// data to send in our API request
		$api_params = array(
			'action' => 'get_api_modul_migrasi_data',
			'api_key'	=> $_POST['api_key'],
			'tahun_anggaran' => get_option('_crb_tahun_anggaran_sipd')
		);

		$response = wp_remote_post($_POST['server'], array('timeout' => 10, 'sslverify' => false, 'body' => $api_params));

		$response = wp_remote_retrieve_body($response);

		$data = json_decode($response);

		$data->last_sinkron = '';

		$data_unit = $data->data;

		if ($data->status == 'success' && !empty($data_unit)) {
			$wpdb->update('data_unit', array('active' => 0), array('tahun_anggaran' => $api_params['tahun_anggaran']));
			foreach ($data_unit as $vdata) {
				$cek = $wpdb->get_var($wpdb->prepare(
					'
					select 
						id 
					from data_unit 
					where id_skpd = %d
						and tahun_anggaran = %d',
					$vdata->id_skpd,
					$vdata->tahun_anggaran
				));
				$opsi = array(
					'id_setup_unit' => $vdata->id_setup_unit,
					'id_unit' => $vdata->id_unit,
					'is_skpd' => $vdata->is_skpd,
					'kode_skpd' => $vdata->kode_skpd,
					'kunci_skpd' => $vdata->kunci_skpd,
					'nama_skpd' => $vdata->nama_skpd,
					'posisi' => $vdata->posisi,
					'status' => $vdata->status,
					'id_skpd' => $vdata->id_skpd,
					'bidur_1' => $vdata->bidur_1,
					'bidur_2' => $vdata->bidur_2,
					'bidur_3' => $vdata->bidur_3,
					'idinduk' => $vdata->idinduk,
					'ispendapatan' => $vdata->ispendapatan,
					'isskpd' => $vdata->isskpd,
					'kode_skpd_1' => $vdata->kode_skpd_1,
					'kode_skpd_2' => $vdata->kode_skpd_2,
					'kodeunit' => $vdata->kodeunit,
					'komisi' => $vdata->komisi,
					'namabendahara' => $vdata->namabendahara,
					'namakepala' => $vdata->namakepala,
					'namaunit' => $vdata->namaunit,
					'nipbendahara' => $vdata->nipbendahara,
					'nipkepala' => $vdata->nipkepala,
					'pangkatkepala' => $vdata->pangkatkepala,
					'setupunit' => $vdata->setupunit,
					'statuskepala' => $vdata->statuskepala,
					'update_at' => $vdata->update_at,
					'tahun_anggaran' => $vdata->tahun_anggaran,
					'active' => $vdata->active
				);
				if (empty($cek)) {
					$wpdb->insert('data_unit', $opsi);
				} else {
					$wpdb->update('data_unit', $opsi, array('id' => $cek));
				}
			}

			$timezone = get_option('timezone_string');
			if (preg_match("/Asia/i", $timezone)) {
				date_default_timezone_set($timezone);
			}

			$dateTime = new DateTime();
			$time_now = $dateTime->format('d-m-Y H:i:s');
			update_option('last_sinkron_api_setting', $time_now);
			$data->last_sinkron = 'Terakhir sinkron data: ' . get_option('last_sinkron_api_setting');
		}

		$response = json_encode($data);

		die($response);
	}

	public function last_sinkron_api_setting()
	{
		return "<span id='last_sinkron'>Terakhir sinkron data: " . get_option('last_sinkron_api_setting') . "</span>";
	}

	function get_sinkron_data_sirup()
	{
		global $wpdb;

		if (!empty($_POST['id_lokasi']) || $_POST['id_lokasi'] != 0 && !empty($_POST['id_kldi']) || $_POST['id_kldi'] != 0) {
			$id_lokasi = $_POST['id_lokasi'];
			$id_kldi = $_POST['id_kldi'];
			$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');

			$url = 'https://sirup.lkpp.go.id/sirup/ro/caripaket2/search?tahunAnggaran=' . $tahun_anggaran . '&jenisPengadaan=&metodePengadaan=&minPagu=&maxPagu=&bulan=&lokasi=' . $id_lokasi . '&kldi=&pdn=&ukm=&draw=1&columns[0][data]=&columns[0][name]=&columns[0][searchable]=false&columns[0][orderable]=false&columns[0][search][value]=&columns[0][search][regex]=false&columns[1][data]=paket&columns[1][name]=&columns[1][searchable]=true&columns[1][orderable]=true&columns[1][search][value]=&columns[1][search][regex]=false&columns[2][data]=pagu&columns[2][name]=&columns[2][searchable]=true&columns[2][orderable]=true&columns[2][search][value]=&columns[2][search][regex]=false&columns[3][data]=jenisPengadaan&columns[3][name]=&columns[3][searchable]=true&columns[3][orderable]=true&columns[3][search][value]=&columns[3][search][regex]=false&columns[4][data]=isPDN&columns[4][name]=&columns[4][searchable]=true&columns[4][orderable]=true&columns[4][search][value]=&columns[4][search][regex]=false&columns[5][data]=isUMK&columns[5][name]=&columns[5][searchable]=true&columns[5][orderable]=true&columns[5][search][value]=&columns[5][search][regex]=false&columns[6][data]=metode&columns[6][name]=&columns[6][searchable]=true&columns[6][orderable]=true&columns[6][search][value]=&columns[6][search][regex]=false&columns[7][data]=pemilihan&columns[7][name]=&columns[7][searchable]=true&columns[7][orderable]=true&columns[7][search][value]=&columns[7][search][regex]=false&columns[8][data]=kldi&columns[8][name]=&columns[8][searchable]=true&columns[8][orderable]=true&columns[8][search][value]=&columns[8][search][regex]=false&columns[9][data]=satuanKerja&columns[9][name]=&columns[9][searchable]=true&columns[9][orderable]=true&columns[9][search][value]=&columns[9][search][regex]=false&columns[10][data]=lokasi&columns[10][name]=&columns[10][searchable]=true&columns[10][orderable]=true&columns[10][search][value]=&columns[10][search][regex]=false&columns[11][data]=id&columns[11][name]=&columns[11][searchable]=true&columns[11][orderable]=true&columns[11][search][value]=&columns[11][search][regex]=false&order[0][column]=5&order[0][dir]=DESC&start=0&length=1000&search[value]=&search[regex]=false&_=1663641619826';
			$url_skpd = 'https://sirup.lkpp.go.id/sirup/datatablectr/datatableruprekapkldi?idKldi=' . $id_kldi . '&tahun=' . $tahun_anggaran . '&sEcho=1&iColumns=10&sColumns=,satker,jumPenyedia,,jumSwakelola,,jumPenyediaSwakelola,,jumSwakelolaPenyedia,&iDisplayStart=0&iDisplayLength=100&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1&_=1665357280414';

			$total_insert = 0;

			$wpdb->update('data_sirup_lokal', array('active' => 0), array('tahun_anggaran' => $tahun_anggaran));
			$wpdb->update('data_skpd_sirup', array('active' => 0), array('tahun_anggaran' => $tahun_anggaran));

			do {
				$get_data_api_sirup = $this->get_data_api_sirup($url);
				$data = json_decode($get_data_api_sirup);

				$data_sirup = $data->data;
				if (!empty($data_sirup)) {
					foreach ($data_sirup as $v_sirup) {
						$cek = $wpdb->get_var($wpdb->prepare(
							'
							select 
								idSirup
							from data_sirup_lokal 
							where idSirup = %d',
							$v_sirup->id
						));

						$opsi = array(
							'idSirup' => $v_sirup->id,
							'idBulan' => $v_sirup->idBulan,
							'idJenisPengadaan' => $v_sirup->idJenisPengadaan,
							'idKldi' => $v_sirup->idKldi,
							'idMetode' => $v_sirup->idMetode,
							'id_referensi' => $v_sirup->id_referensi,
							'idlokasi' => $v_sirup->idlokasi,
							'isPDN' => $v_sirup->isPDN,
							'isUMK' => $v_sirup->isUMK,
							'jenisPengadaan' => $v_sirup->jenisPengadaan,
							'kldi' => $v_sirup->kldi,
							'metode' => $v_sirup->metode,
							'pagu' => $v_sirup->pagu,
							'paket' => $v_sirup->paket,
							'pemilihan' => $v_sirup->pemilihan,
							'satuanKerja' => $v_sirup->satuanKerja,
							'tahun_anggaran' => $tahun_anggaran,
							'active' => 1,
							'update_at' =>  current_time('mysql')
						);

						if (empty($cek)) {
							$cek_insert = $wpdb->insert('data_sirup_lokal', $opsi);
						} else {
							$cek_insert = $wpdb->update('data_sirup_lokal', $opsi, array(
								'idSirup' => $cek
							));
						}
						if ($cek_insert == 1) {
							$total_insert++;
						}
					}
				}

				$timezone = get_option('timezone_string');
				if (preg_match("/Asia/i", $timezone)) {
					date_default_timezone_set($timezone);
				}

				$dateTime = new DateTime();
				$time_now = $dateTime->format('d-m-Y H:i:s');
				update_option('last_sinkron_data_sirup', $time_now);
				$data->last_sinkron = 'Terakhir sinkron data: ' . get_option('last_sinkron_data_sirup');
				$data->status = 'success';
				$data->message = 'data berhasil disinkron';
				$data->insert_succeed = $total_insert;

				$url_pecahan = explode("&", $url);

				$int_start = 0;
				$len_start = 0;
				$int_length = 0;
				foreach ($url_pecahan as $val_url) {
					if (strpos($val_url, "start=") !== false) {
						$int_start = substr($val_url, 6);
						$len_start = strlen($int_start);
					}
					if (strpos($val_url, "length=") !== false) {
						$int_length = substr($val_url, 7);
					}
				}

				$idx = strpos($url, "start=");
				$idy = $idx + 6;

				$idplus = $int_start + $int_length;
				$url = substr_replace($url, $idplus, $idy, $len_start);
			} while (!empty($data_sirup));

			$get_data_skpd_api_sirup = $this->get_data_api_sirup($url_skpd);
			$data_skpd = json_decode($get_data_skpd_api_sirup);
			$data_skpd_sirup = $data_skpd->aaData;

			if (!empty($data_skpd_sirup)) {
				foreach ($data_skpd_sirup as $v_skpd) {
					$cek = $wpdb->get_var($wpdb->prepare(
						'
						select 
							id_satuan_kerja
						from data_skpd_sirup 
						where id_satuan_kerja = %d',
						$v_skpd[0]
					));

					$opsi = array(
						'id_satuan_kerja' => $v_skpd[0],
						'satuan_kerja' => $v_skpd[1],
						'paket_penyedia' => $v_skpd[2],
						'pagu_penyedia' => $v_skpd[3],
						'paket_swakelola' => $v_skpd[4],
						'pagu_swakelola' => $v_skpd[5],
						'paket_pd_swakelola' => $v_skpd[6],
						'pagu_pd_swakelola' => $v_skpd[7],
						'total_paket' => $v_skpd[8],
						'total_pagu' => $v_skpd[9],
						'tahun_anggaran' => $tahun_anggaran,
						'active' => 1,
						'update_at' =>  current_time('mysql')
					);

					if (empty($cek)) {
						$wpdb->insert('data_skpd_sirup', $opsi);
						$cek_insert_skpd = $wpdb->insert_id;
					} else {
						$cek_insert_skpd = $cek;
						$wpdb->update('data_skpd_sirup', $opsi, array(
							'id_satuan_kerja' => $cek
						));
					}
					$data->skpd = $cek_insert_skpd;
				}
			}
		} else {
			$data = array(
				'status' => 'error',
				'message' => 'Id lokasi SIRUP atau Id k/l/d/i tidak valid',
				'last_sinkron' => ''
			);
		}

		$response = json_encode($data);

		die($response);
	}

	public function get_data_api_sirup($url)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_NOSIGNAL => 1,
			CURLOPT_CONNECTTIMEOUT => -1,
			CURLOPT_TIMEOUT => -1
		));
		$response = curl_exec($curl);

		$err = curl_error($curl);

		curl_close($curl);

		if (!empty($err)) {
			$response = $err;
		}

		return $response;
	}

	public function last_sinkron_data_sirup()
	{
		return "<span id='last_sinkron_data_sirup'>Terakhir sinkron data: " . get_option('last_sinkron_data_sirup') . "</span>";
	}

	function get_page_by_title($page_title, $output = OBJECT, $post_type = 'page')
	{
		global $wpdb;
		if (is_array($post_type)) {
			$post_type = esc_sql($post_type);
			$post_type_in_string = "'" . implode("','", $post_type) . "'";
			$sql = $wpdb->prepare("
				SELECT ID
				FROM $wpdb->posts
				WHERE post_title = %s
					AND post_type IN ($post_type_in_string)
			", $page_title);
		} else {
			$sql = $wpdb->prepare("
				SELECT ID
				FROM $wpdb->posts
				WHERE post_title = %s
					AND post_type = %s
			", $page_title, $post_type);
		}
		$page = $wpdb->get_var($sql);
		if ($page) {
			return get_post($page, $output);
		}
		return null;
	}
}
