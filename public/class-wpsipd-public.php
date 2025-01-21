<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/agusnurwanto
 * @since      1.0.0
 *
 * @package    Wpsipd
 * @subpackage Wpsipd/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wpsipd
 * @subpackage Wpsipd/public
 * @author     Agus Nurwanto <agusnurwantomuslim@gmail.com>
 */

require_once WPSIPD_PLUGIN_PATH . "/public/class-wpsipd-public-base-1.php";
require_once WPSIPD_PLUGIN_PATH . "/public/trait/CustomTrait.php";

class Wpsipd_Public extends Wpsipd_Public_Base_1
{
	use CustomTrait;

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
	 * @param      string    $plugin_name       The name of the plugin.
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
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wpsipd-public.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . 'bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . 'select2', plugin_dir_url(__FILE__) . 'css/select2.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . 'datatables', plugin_dir_url(__FILE__) . 'css/datatables.min.css', array(), $this->version, 'all');

		wp_enqueue_style('dashicons');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wpsipd-public.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . 'bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.bundle.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . 'select2', plugin_dir_url(__FILE__) . 'js/select2.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . 'datatables', plugin_dir_url(__FILE__) . 'js/datatables.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . 'chart', plugin_dir_url(__FILE__) . 'js/chart.min.js', array('jquery'), $this->version, false);
		wp_localize_script($this->plugin_name, 'ajax', array(
			'url' 		=> admin_url('admin-ajax.php'),
			'api_key' 	=> get_option('_crb_api_key_extension'),
			'site_url' 	=> site_url()
		));
	}

	public function get_skpd()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'run'	=> $_POST['run'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get SKPD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$data_skpd = $wpdb->get_results($wpdb->prepare(
					"
					SELECT 
						* 
					from data_unit 
					where tahun_anggaran=%d
						and active=1",
					$_POST['tahun_anggaran']
				), ARRAY_A);
				foreach ($data_skpd as $k => $v) {
					$data_skpd[$k]['id_mapping'] = get_option('_crb_unit_fmis_' . $_POST['tahun_anggaran'] . '_' . $v['id_skpd']);
					$kode_skpd = explode('.', $v['kode_skpd']);
					$bidur_1 = $kode_skpd[0] . '.' . $kode_skpd[1];
					$bidur_2 = $kode_skpd[2] . '.' . $kode_skpd[3];
					$bidur_3 = $kode_skpd[4] . '.' . $kode_skpd[5];
					$data_skpd[$k]['bidur__1'] = $bidur_1;
					$data_skpd[$k]['bidur__2'] = $bidur_2;
					$data_skpd[$k]['bidur__3'] = $bidur_3;
					$data_skpd[$k]['bidur1'] = $wpdb->get_var($wpdb->prepare("select nama_bidang_urusan from data_prog_keg where tahun_anggaran=%d and kode_bidang_urusan=%s limit 1", $_POST['tahun_anggaran'], $bidur_1));
					$data_skpd[$k]['bidur2'] = $wpdb->get_var($wpdb->prepare("select nama_bidang_urusan from data_prog_keg where tahun_anggaran=%d and kode_bidang_urusan=%s limit 1", $_POST['tahun_anggaran'], $bidur_2));
					$data_skpd[$k]['bidur3'] = $wpdb->get_var($wpdb->prepare("select nama_bidang_urusan from data_prog_keg where tahun_anggaran=%d and kode_bidang_urusan=%s limit 1", $_POST['tahun_anggaran'], $bidur_3));
				}
				$ret['data'] = $data_skpd;
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_skpd_fmis()
	{
		global $wpdb;
		$ret = array(
			'kode_pemda'	=> $_POST['kode_pemda'],
			'nama_pemda'	=> $_POST['nama_pemda'],
			'skpd'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$data_skpd_db = $wpdb->get_results($wpdb->prepare(
					"
					SELECT 
						* 
					from data_unit 
					where tahun_anggaran=%d
						and active=1
					order by id_skpd ASC",
					$_POST['tahun_anggaran']
				), ARRAY_A);
				foreach ($data_skpd_db as $k => $v) {
					if ($v['is_skpd'] == 1) {
						$data_skpd = array(
							'id_skpd' => $v['id_skpd'],
							'kode_skpd' => $v['kode_skpd'],
							'nama_skpd' => $v['nama_skpd'],
							'unit' => array(
								array(
									'id_unit' => $v['id_skpd'],
									'kode_unit' => $v['kode_skpd'],
									'nama_unit' => $v['nama_skpd'],
									'sub_unit' => array(
										array(
											'id_sub' => $v['id_skpd'],
											'kode_sub' => $v['kode_skpd'],
											'nama_sub' => $v['nama_skpd']
										)
									)
								)
							)
						);
						foreach ($data_skpd_db as $kk => $vv) {
							if (
								$v['id_skpd'] == $vv['id_unit']
								and $vv['is_skpd'] == 0
							) {
								$data_skpd['unit'][0]['sub_unit'][] = array(
									'id_sub' => $vv['id_skpd'],
									'kode_sub' => $vv['kode_skpd'],
									'nama_sub' => $vv['nama_skpd']
								);
							}
						}
						$ret['skpd'][] = $data_skpd;
					}
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_meta_subunit_simda()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare(
					"
					SELECT 
						s.*,
						j.*
					from ta_sub_unit s
					left join ta_sub_unit_jab j on s.tahun=j.tahun
						and s.kd_urusan=j.kd_urusan
						and s.kd_bidang=j.kd_bidang
						and s.kd_unit=j.kd_unit
						and s.kd_sub=j.kd_sub
					where s.tahun=%d
						and j.kd_jab=4
					",
					$tahun_anggaran
				);
				$return['sql'] = $sql;
				$data_sub_unit = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				$mapping_skpd = $this->get_id_skpd_fmis(false, $tahun_anggaran);
				foreach ($data_sub_unit as $k => $sub_unit) {
					$kd_sub_unit = $sub_unit->kd_urusan . '.' . $sub_unit->kd_bidang . '.' . $sub_unit->kd_unit . '.' . $sub_unit->kd_sub;
					$data_sub_unit[$k]->kd_sub_unit = $kd_sub_unit;
					if (!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])) {
						$data_sub_unit[$k]->skpd = $mapping_skpd['id_mapping_simda'][$kd_sub_unit];
					}
				}
				$ret['data'] = $data_sub_unit;
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_spp()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'tipe'	=> $_POST['tipe'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$jenis = 1;
				if ($_POST['tipe'] == 'up') {
					$jenis = 1;
				} else if ($_POST['tipe'] == 'gu') {
					$jenis = 2;
				} else if ($_POST['tipe'] == 'ls') {
					$jenis = 3;
				} else if ($_POST['tipe'] == 'tu') {
					$jenis = 4;
				} else if ($_POST['tipe'] == 'nihil') {
					$jenis = 5;
				}
				$id_skpd_fmis = false;
				if (!empty($_POST['idsubunit'])) {
					$id_skpd_fmis = $_POST['idsubunit'];
				}
				$mapping_skpd = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, true);
				$kd_urusan = array();
				$kd_bidang = array();
				$kd_unit = array();
				$kd_sub = array();
				foreach ($mapping_skpd['id_skpd_sipd'] as $id) {
					$kd_unit_simda_asli = get_option('_crb_unit_' . $id);
					$kd_simda = explode('.', $kd_unit_simda_asli);
					if (!empty($kd_simda[0])) {
						if (empty($kd_urusan[$kd_simda[0]])) {
							$kd_urusan[$kd_simda[0]] = $kd_simda[0];
						}
					}
					if (!empty($kd_simda[1])) {
						if (empty($kd_bidang[$kd_simda[1]])) {
							$kd_bidang[$kd_simda[1]] = $kd_simda[1];
						}
					}
					if (!empty($kd_simda[2])) {
						if (empty($kd_unit[$kd_simda[2]])) {
							$kd_unit[$kd_simda[2]] = $kd_simda[2];
						}
					}
					if (!empty($kd_simda[3])) {
						if (empty($kd_sub[$kd_simda[3]])) {
							$kd_sub[$kd_simda[3]] = $kd_simda[3];
						}
					}
				}
				$sql = $wpdb->prepare(
					"
					SELECT 
						*
					from ta_spp
					where tahun=%d
						and jn_spp=%d
						and kd_urusan in (" . implode(',', $kd_urusan) . ")
						and kd_bidang in (" . implode(',', $kd_bidang) . ")
						and kd_unit in (" . implode(',', $kd_unit) . ")
						and kd_sub in (" . implode(',', $kd_sub) . ")
					",
					$tahun_anggaran,
					$jenis
				);
				$return['sql'] = $sql;
				$spp = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				foreach ($spp as $k => $v) {
					$kd_sub_unit = $v->kd_urusan . '.' . $v->kd_bidang . '.' . $v->kd_unit . '.' . $v->kd_sub;
					$spp[$k]->kd_sub_unit = $kd_sub_unit;
					if (!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])) {
						$spp[$k]->skpd = $mapping_skpd['id_mapping_simda'][$kd_sub_unit];
					}
				}
				$ret['data'] = $spp;
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_spm()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'tipe'	=> $_POST['tipe'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$jenis = 1;
				if ($_POST['tipe'] == 'up') {
					$jenis = 1;
				} else if ($_POST['tipe'] == 'gu') {
					$jenis = 2;
				} else if ($_POST['tipe'] == 'ls') {
					$jenis = 3;
				} else if ($_POST['tipe'] == 'tu') {
					$jenis = 4;
				} else if ($_POST['tipe'] == 'nihil') {
					$jenis = 5;
				}
				$id_skpd_fmis = false;
				if (!empty($_POST['idsubunit'])) {
					$id_skpd_fmis = $_POST['idsubunit'];
				}
				$mapping_skpd = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, true);
				$kd_urusan = array();
				$kd_bidang = array();
				$kd_unit = array();
				$kd_sub = array();
				foreach ($mapping_skpd['id_skpd_sipd'] as $id) {
					$kd_unit_simda_asli = get_option('_crb_unit_' . $id);
					$kd_simda = explode('.', $kd_unit_simda_asli);
					if (!empty($kd_simda[0])) {
						if (empty($kd_urusan[$kd_simda[0]])) {
							$kd_urusan[$kd_simda[0]] = $kd_simda[0];
						}
					}
					if (!empty($kd_simda[1])) {
						if (empty($kd_bidang[$kd_simda[1]])) {
							$kd_bidang[$kd_simda[1]] = $kd_simda[1];
						}
					}
					if (!empty($kd_simda[2])) {
						if (empty($kd_unit[$kd_simda[2]])) {
							$kd_unit[$kd_simda[2]] = $kd_simda[2];
						}
					}
					if (!empty($kd_simda[3])) {
						if (empty($kd_sub[$kd_simda[3]])) {
							$kd_sub[$kd_simda[3]] = $kd_simda[3];
						}
					}
				}
				$sql = $wpdb->prepare(
					"
					SELECT 
						*
					from ta_spm
					where tahun=%d
						and jn_spm=%d
						and kd_urusan in (" . implode(',', $kd_urusan) . ")
						and kd_bidang in (" . implode(',', $kd_bidang) . ")
						and kd_unit in (" . implode(',', $kd_unit) . ")
						and kd_sub in (" . implode(',', $kd_sub) . ")
					",
					$tahun_anggaran,
					$jenis
				);
				$return['sql'] = $sql;
				$spm = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				foreach ($spm as $k => $v) {
					$kd_sub_unit = $v->kd_urusan . '.' . $v->kd_bidang . '.' . $v->kd_unit . '.' . $v->kd_sub;
					$spm[$k]->kd_sub_unit = $kd_sub_unit;
					if (!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])) {
						$spm[$k]->skpd = $mapping_skpd['id_mapping_simda'][$kd_sub_unit];
					}
				}
				$ret['data'] = $spm;
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_sp2d()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$spm_all = json_decode(stripslashes(html_entity_decode($_POST['spm_no'])));
				if (!empty($spm_all)) {
					foreach ($spm_all as $k => $v) {
						$spm_all[$k] = "'$v'";
					}
					$sql = $wpdb->prepare(
						"
						SELECT 
							*
						from ta_sp2d
						where tahun=%d
							and no_spm in (" . implode(',', $spm_all) . ")
						",
						$tahun_anggaran
					);
					$return['sql'] = $sql;
					$sp2d = $this->simda->CurlSimda(array(
						'query' => $sql,
						'debug' => 1
					));
					$ret['data'] = $sp2d;
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'No SPM tidak boleh kosong!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_tagihan()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_skpd_fmis = false;
				if (!empty($_POST['idsubunit'])) {
					$id_skpd_fmis = $_POST['idsubunit'];
				}
				$mapping_skpd = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, true);
				$kd_urusan = array();
				$kd_bidang = array();
				$kd_unit = array();
				$kd_sub = array();
				foreach ($mapping_skpd['id_skpd_sipd'] as $id) {
					$kd_unit_simda_asli = get_option('_crb_unit_' . $id);
					$kd_simda = explode('.', $kd_unit_simda_asli);
					if (!empty($kd_simda[0])) {
						if (empty($kd_urusan[$kd_simda[0]])) {
							$kd_urusan[$kd_simda[0]] = $kd_simda[0];
						}
					}
					if (!empty($kd_simda[1])) {
						if (empty($kd_bidang[$kd_simda[1]])) {
							$kd_bidang[$kd_simda[1]] = $kd_simda[1];
						}
					}
					if (!empty($kd_simda[2])) {
						if (empty($kd_unit[$kd_simda[2]])) {
							$kd_unit[$kd_simda[2]] = $kd_simda[2];
						}
					}
					if (!empty($kd_simda[3])) {
						if (empty($kd_sub[$kd_simda[3]])) {
							$kd_sub[$kd_simda[3]] = $kd_simda[3];
						}
					}
				}
				$sql = $wpdb->prepare(
					"
					SELECT 
						s.*,
						t.*
					from ta_spp s
						left join ta_tagihan t on s.no_tagihan=t.no_tagihan
					where s.tahun=%d
						and s.no_tagihan is not null
						and s.kd_urusan in (" . implode(',', $kd_urusan) . ")
						and s.kd_bidang in (" . implode(',', $kd_bidang) . ")
						and s.kd_unit in (" . implode(',', $kd_unit) . ")
						and s.kd_sub in (" . implode(',', $kd_sub) . ")
					",
					$tahun_anggaran
				);
				$return['sql'] = $sql;
				$spp = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				foreach ($spp as $k => $v) {
					$kd_sub_unit = $v->kd_urusan . '.' . $v->kd_bidang . '.' . $v->kd_unit . '.' . $v->kd_sub;
					$spp[$k]->kd_sub_unit = $kd_sub_unit;
					if (!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])) {
						$spp[$k]->skpd = $mapping_skpd['id_mapping_simda'][$kd_sub_unit];
					}
				}
				$ret['data'] = $spp;
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_kontrak()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_skpd_fmis = false;
				if (!empty($_POST['idsubunit'])) {
					$id_skpd_fmis = $_POST['idsubunit'];
				}
				$mapping_skpd = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, true);
				$kd_urusan = array();
				$kd_bidang = array();
				$kd_unit = array();
				$kd_sub = array();
				foreach ($mapping_skpd['id_skpd_sipd'] as $id) {
					$kd_unit_simda_asli = get_option('_crb_unit_' . $id);
					$kd_simda = explode('.', $kd_unit_simda_asli);
					if (!empty($kd_simda[0])) {
						if (empty($kd_urusan[$kd_simda[0]])) {
							$kd_urusan[$kd_simda[0]] = $kd_simda[0];
						}
					}
					if (!empty($kd_simda[1])) {
						if (empty($kd_bidang[$kd_simda[1]])) {
							$kd_bidang[$kd_simda[1]] = $kd_simda[1];
						}
					}
					if (!empty($kd_simda[2])) {
						if (empty($kd_unit[$kd_simda[2]])) {
							$kd_unit[$kd_simda[2]] = $kd_simda[2];
						}
					}
					if (!empty($kd_simda[3])) {
						if (empty($kd_sub[$kd_simda[3]])) {
							$kd_sub[$kd_simda[3]] = $kd_simda[3];
						}
					}
				}
				$sql = $wpdb->prepare(
					"
					SELECT 
						s.*
					from ta_kontrak s
					where s.tahun=%d
						and s.kd_urusan in (" . implode(',', $kd_urusan) . ")
						and s.kd_bidang in (" . implode(',', $kd_bidang) . ")
						and s.kd_unit in (" . implode(',', $kd_unit) . ")
						and s.kd_sub in (" . implode(',', $kd_sub) . ")
					",
					$tahun_anggaran
				);
				$ret['sql'] = $sql;
				$program_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_program_fmis'
				));
				$keg_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_keg_fmis'
				));
				$subkeg_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_subkeg_fmis'
				));
				$rek_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_rekening_fmis'
				));
				$kontrak = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				$prog_keg = array();
				foreach ($kontrak as $k => $v) {
					$kd_sub_unit = $v->kd_urusan . '.' . $v->kd_bidang . '.' . $v->kd_unit . '.' . $v->kd_sub;
					$kontrak[$k]->kd_sub_unit = $kd_sub_unit;
					if (!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])) {
						$kontrak[$k]->skpd = $mapping_skpd['id_mapping_simda'][$kd_sub_unit];
					}
					$kd_urusan_asli = substr($v->id_prog, 0, 1);
					$kd_bidang_asli = (int) substr($v->id_prog, 1);
					$kd_keg_simda = $kd_urusan_asli . '.' . $kd_bidang_asli . '.' . $v->kd_prog . '.' . $v->kd_keg;
					if (empty($prog_keg[$kd_keg_simda])) {
						$prog_keg[$kd_keg_simda] = array();
						$sql = "
							SELECT 
								m.*
							from ref_kegiatan_mapping m
							where m.kd_urusan=$kd_urusan_asli
								and m.kd_bidang=$kd_bidang_asli
								and m.kd_prog=$v->kd_prog
								and m.kd_keg=$v->kd_keg
						";
						$mapping = $this->simda->CurlSimda(array(
							'query' => $sql,
							'debug' => 1
						));
						$kd_urusan_sipd = $mapping[0]->kd_urusan90;
						$kd_bidang_sipd = $mapping[0]->kd_bidang90;
						if ($mapping[0]->kd_program90 == 1) {
							$kd_urusan_sipd = 'X';
							$kd_bidang_sipd = 'XX';
						}
						$kode_sub = $kd_urusan_sipd . '.' . $this->simda->CekNull($kd_bidang_sipd) . '.' . $this->simda->CekNull($mapping[0]->kd_program90) . '.' . $mapping[0]->kd_kegiatan90 . '.' . $this->simda->CekNull($mapping[0]->kd_sub_kegiatan);
						$sub = $wpdb->get_results($wpdb->prepare("
							select
								*
							from data_prog_keg
							where tahun_anggaran=%d
								and kode_sub_giat=%s
						", $tahun_anggaran, $kode_sub), ARRAY_A);

						$sub[0]['nama_program'] = explode(' ', $sub[0]['nama_program']);
						unset($sub[0]['nama_program'][0]);
						$sub[0]['nama_program'] = implode(' ', $sub[0]['nama_program']);

						$sub[0]['nama_giat'] = explode(' ', $sub[0]['nama_giat']);
						unset($sub[0]['nama_giat'][0]);
						$sub[0]['nama_giat'] = implode(' ', $sub[0]['nama_giat']);

						$sub[0]['nama_sub_giat'] = explode(' ', $sub[0]['nama_sub_giat']);
						unset($sub[0]['nama_sub_giat'][0]);
						$sub[0]['nama_sub_giat'] = implode(' ', $sub[0]['nama_sub_giat']);

						$prog_keg[$kd_keg_simda]['nama_program'] = $sub[0]['nama_program'];
						$prog_keg[$kd_keg_simda]['nama_giat'] = $sub[0]['nama_giat'];
						$prog_keg[$kd_keg_simda]['nama_sub_giat'] = $sub[0]['nama_sub_giat'];
						if (!empty($program_mapping[$this->removeNewline($sub[0]['nama_program'])])) {
							$prog_keg[$kd_keg_simda]['nama_program'] = $program_mapping[$this->removeNewline($sub[0]['nama_program'])];
						}
						if (!empty($keg_mapping[$this->removeNewline($sub[0]['nama_giat'])])) {
							$prog_keg[$kd_keg_simda]['nama_giat'] = $keg_mapping[$this->removeNewline($sub[0]['nama_giat'])];
						}
						if (!empty($subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])])) {
							$prog_keg[$kd_keg_simda]['nama_sub_giat'] = $subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])];
						}
					}
					$kontrak[$k]->detail = $prog_keg[$kd_keg_simda];
				}
				$ret['data'] = $kontrak;
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_sp2b()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_skpd_fmis = false;
				if (!empty($_POST['idsubunit'])) {
					$id_skpd_fmis = $_POST['idsubunit'];
				}
				$mapping_skpd = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, true);
				$kd_urusan = array();
				$kd_bidang = array();
				$kd_unit = array();
				$kd_sub = array();
				foreach ($mapping_skpd['id_skpd_sipd'] as $id) {
					$kd_unit_simda_asli = get_option('_crb_unit_' . $id);
					$kd_simda = explode('.', $kd_unit_simda_asli);
					if (!empty($kd_simda[0])) {
						if (empty($kd_urusan[$kd_simda[0]])) {
							$kd_urusan[$kd_simda[0]] = $kd_simda[0];
						}
					}
					if (!empty($kd_simda[1])) {
						if (empty($kd_bidang[$kd_simda[1]])) {
							$kd_bidang[$kd_simda[1]] = $kd_simda[1];
						}
					}
					if (!empty($kd_simda[2])) {
						if (empty($kd_unit[$kd_simda[2]])) {
							$kd_unit[$kd_simda[2]] = $kd_simda[2];
						}
					}
					if (!empty($kd_simda[3])) {
						if (empty($kd_sub[$kd_simda[3]])) {
							$kd_sub[$kd_simda[3]] = $kd_simda[3];
						}
					}
				}
				$sql = $wpdb->prepare(
					"
					SELECT 
						s.*
					from ta_sp3b s
					where s.tahun=%d
						and s.kd_urusan in (" . implode(',', $kd_urusan) . ")
						and s.kd_bidang in (" . implode(',', $kd_bidang) . ")
						and s.kd_unit in (" . implode(',', $kd_unit) . ")
						and s.kd_sub in (" . implode(',', $kd_sub) . ")
					",
					$tahun_anggaran
				);
				$return['sql'] = $sql;
				$spp = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				foreach ($spp as $k => $v) {
					$kd_sub_unit = $v->kd_urusan . '.' . $v->kd_bidang . '.' . $v->kd_unit . '.' . $v->kd_sub;
					$spp[$k]->kd_sub_unit = $kd_sub_unit;
					if (!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])) {
						$spp[$k]->skpd = $mapping_skpd['id_mapping_simda'][$kd_sub_unit];
					}
				}
				$ret['data'] = $spp;
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_ssh()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get SSH!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (
					!empty($_POST['kelompok'])
					&& (
						$_POST['kelompok'] == 1 // SSH
						|| $_POST['kelompok'] == 2 // HSPK
						|| $_POST['kelompok'] == 3 // ASB
						|| $_POST['kelompok'] == 4 // SBU
						|| $_POST['kelompok'] == 7 // Pendapatan & Pembiayaan
						|| $_POST['kelompok'] == 8 // RKA APBD Murni SIMDA
						|| $_POST['kelompok'] == 9 // RKA
					)
				) {
					$rek_mapping = $this->get_fmis_mapping(array(
						'name' => '_crb_custom_mapping_rekening_fmis'
					));
					$type_pagu = get_option('_crb_fmis_pagu');
					if ($_POST['kelompok'] == 7) {
						$data = array();
						$data_ssh = $wpdb->get_results($wpdb->prepare(
							"
							SELECT 
								kode_akun,
								nama_akun,
								total,
								nilaimurni,
								uraian,
								keterangan
							from data_pendapatan 
							where tahun_anggaran=%d
								and active=1
								and kode_akun!=''",
							$_POST['tahun_anggaran']
						), ARRAY_A);

						// set variable ssh sesuai kebutuhan ssh di FMIS
						foreach ($data_ssh as $k => $v) {
							if (
								!empty($type_pagu)
								&& $type_pagu == 2
							) {
								$v['total'] = $v['nilaimurni'];
							}
							$_kode_akun = explode('.', $v['kode_akun']);
							$kode_akun = array();
							foreach ($_kode_akun as $vv) {
								$kode_akun[] = (int)$vv;
							}
							$kode_akun = implode('.', $kode_akun);
							if (!empty($rek_mapping[$kode_akun])) {
								$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
								$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
								$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
								$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
								$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
								$v['kode_akun'] = implode('.', $_kode_akun);
							}
							$newdata = array();
							$newdata['rek_belanja'] = array(
								array(
									'kode_akun' => $v['kode_akun'],
									'nama_akun' => $v['nama_akun']
								)
							);
							$newdata['kode_standar_harga'] = $_POST['tahun_anggaran'];
							$newdata['nama_standar_harga'] = substr($v['total'] . ' Rupiah ' . $v['uraian'], 0, 250);
							$newdata['spek'] = '';
							$newdata['satuan'] = 'Rupiah';
							$newdata['kelompok'] = 9;
							$newdata['harga'] = $v['total'];
							$newdata['kode_gol_standar_harga'] = $_POST['tahun_anggaran'];
							$newdata['kode_kel_standar_harga'] = 'Pendapatan';
							$newdata['nama_sub_kel_standar_harga'] = substr($v['keterangan'], 0, 250);
							$data[] = $newdata;
						}

						$data_ssh = $wpdb->get_results($wpdb->prepare(
							"
							SELECT 
								type,
								kode_akun,
								nama_akun,
								total,
								nilaimurni,
								uraian,
								keterangan
							from data_pembiayaan 
							where tahun_anggaran=%d
								and active=1
								and kode_akun!=''",
							$_POST['tahun_anggaran']
						), ARRAY_A);

						// set variable ssh sesuai kebutuhan ssh di FMIS
						foreach ($data_ssh as $k => $v) {
							if (
								!empty($type_pagu)
								&& $type_pagu == 2
							) {
								$v['total'] = $v['nilaimurni'];
							}
							$_kode_akun = explode('.', $v['kode_akun']);
							$kode_akun = array();
							foreach ($_kode_akun as $vv) {
								$kode_akun[] = (int)$vv;
							}
							$kode_akun = implode('.', $kode_akun);
							if (!empty($rek_mapping[$kode_akun])) {
								$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
								$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
								$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
								$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
								$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
								$v['kode_akun'] = implode('.', $_kode_akun);
							}

							$newdata = array();
							$newdata['rek_belanja'] = array(
								array(
									'kode_akun' => $v['kode_akun'],
									'nama_akun' => $v['nama_akun']
								)
							);
							$newdata['kode_standar_harga'] = $_POST['tahun_anggaran'];
							$newdata['nama_standar_harga'] = substr($v['total'] . ' Rupiah ' . $v['uraian'], 0, 250);
							$newdata['spek'] = '';
							$newdata['satuan'] = 'Rupiah';
							$newdata['kelompok'] = 9;
							$newdata['harga'] = $v['total'];
							$newdata['kode_gol_standar_harga'] = $_POST['tahun_anggaran'];
							$newdata['kode_kel_standar_harga'] = 'Pembiayaan ' . $v['type'];
							$newdata['nama_sub_kel_standar_harga'] = substr($v['keterangan'], 0, 250);
							$data[] = $newdata;
						}
					} else if ($_POST['kelompok'] == 8) {
						$sql = $wpdb->prepare("
							SELECT 
								r.kd_rek_1,
								r.kd_rek_2,
								r.kd_rek_3,
								r.kd_rek_4,
								r.kd_rek_5,
								r.kd_rek90_1,
								r.kd_rek90_2,
								r.kd_rek90_3,
								r.kd_rek90_4,
								r.kd_rek90_5,
								r.kd_rek90_6,
								rr.nm_rek90_6
							FROM ref_rek_mapping r
								inner join ref_rek90_6 rr on r.kd_rek90_1 = rr.kd_rek90_1
									and r.kd_rek90_2 = rr.kd_rek90_2
									and r.kd_rek90_3 = rr.kd_rek90_3
									and r.kd_rek90_4 = rr.kd_rek90_4
									and r.kd_rek90_5 = rr.kd_rek90_5
									and r.kd_rek90_6 = rr.kd_rek90_6
						");
						$data_rek = $this->simda->CurlSimda(array('query' => $sql));
						$new_rek = array();
						foreach ($data_rek as $rek) {
							$keyword = $rek->kd_rek_1 . $rek->kd_rek_2 . $rek->kd_rek_3 . $rek->kd_rek_4 . $rek->kd_rek_5;
							$keyword90 = $rek->kd_rek90_1 . '.' . $rek->kd_rek90_2 . '.' . $rek->kd_rek90_3 . '.' . $rek->kd_rek90_4 . '.' . $rek->kd_rek90_5 . '.' . $rek->kd_rek90_6;
							$new_rek[$keyword] = array(
								'kode_akun' => $keyword90,
								'nama_akun' => $rek->nm_rek90_6
							);
						}

						$kd_perubahan = 4;
						if (
							!empty($type_pagu)
							&& $type_pagu == 2
						) {
							$kd_perubahan = '(SELECT max(kd_perubahan) from ta_rask_arsip where tahun=' . $tahun_anggaran . ')';
						}
						$sql = $wpdb->prepare(
							"
							SELECT 
								*
							FROM ta_rask_arsip r
							WHERE r.tahun = %d
								AND r.kd_perubahan = $kd_perubahan
								AND r.kd_rek_1 = 5
							",
							$_POST['tahun_anggaran']
						);
						$data_ssh = $this->simda->CurlSimda(array('query' => $sql));
						$data = array();
						$data1 = array();
						// set rekening pada item SSH berdasarkan keyword array
						foreach ($data_ssh as $k => $v) {
							$keyword = $v->kd_rek_1 . $v->kd_rek_2 . $v->kd_rek_3 . $v->kd_rek_4 . $v->kd_rek_5;
							$key = $v->keterangan . $v->nilai_rp . $v->satuan123;
							if (empty($data1[$key])) {
								$v->rek_belanja = array($new_rek[$keyword]);
								$data1[$key] = (array) $v;
							} else {
								$data1[$key]['rek_belanja'][] = $new_rek[$keyword];
							}
						}
						// set variable ssh sesuai kebutuhan ssh di FMIS
						foreach ($data1 as $k => $v) {
							// if($k >= 10){ continue; }
							$newdata = array();
							$newdata['rek_belanja'] = $v['rek_belanja'];
							$newdata['kode_standar_harga'] = $_POST['tahun_anggaran'];
							$newdata['nama_standar_harga'] = substr($v['nilai_rp'] . ' ' . $v['satuan123'] . ' ' . $v['keterangan'], 0, 250);
							$newdata['spek'] = '';
							$newdata['satuan'] = $v['satuan123'];
							$newdata['kelompok'] = 9;
							$newdata['harga'] = $v['nilai_rp'];
							$newdata['kode_gol_standar_harga'] = $_POST['tahun_anggaran'] . ' RKA SIMDA';
							$newdata['kode_kel_standar_harga'] = $v['kd_urusan'] . '.' . $v['kd_bidang'] . '.' . $v['kd_unit'] . '.' . $v['kd_sub'] . '.' . $v['kd_prog'] . '.' . $v['id_prog'] . '.' . $v['kd_keg'];
							$newdata['nama_sub_kel_standar_harga'] = substr($v['keterangan_rinc'], 0, 250);
							$data[] = $newdata;
						}
					} else if ($_POST['kelompok'] == 9) {
						$data_ssh = $wpdb->get_results($wpdb->prepare(
							"
							SELECT 
								jenis_bl,
								nama_komponen,
								spek_komponen,
								harga_satuan,
								harga_satuan_murni,
								satuan,
								kode_akun,
								nama_akun
							from data_rka 
							where tahun_anggaran=%d
								and active=1
								and kode_akun!=''
							group by jenis_bl, nama_komponen, spek_komponen, harga_satuan, satuan, kode_akun, nama_akun",
							$_POST['tahun_anggaran'],
							$_POST['kelompok']
						), ARRAY_A);
						$data = array();
						$data1 = array();
						foreach ($data_ssh as $k => $v) {
							if (
								!empty($type_pagu)
								&& $type_pagu == 2
							) {
								$v['harga_satuan'] = $v['harga_satuan_murni'];
							}
							$_kode_akun = explode('.', $v['kode_akun']);
							$kode_akun = array();
							foreach ($_kode_akun as $vv) {
								$kode_akun[] = (int)$vv;
							}
							$kode_akun = implode('.', $kode_akun);
							if (!empty($rek_mapping[$kode_akun])) {
								$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
								$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
								$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
								$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
								$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
								$v['kode_akun'] = implode('.', $_kode_akun);
							}

							$key = $v['nama_komponen'] . $v['spek_komponen'] . $v['harga_satuan'] . $v['satuan'];
							if (empty($data1[$key])) {
								$v['rek_belanja'] = array(
									array(
										'kode_akun' => $v['kode_akun'],
										'nama_akun' => $v['nama_akun']
									)
								);
								$data1[$key] = $v;
							} else {
								$data1[$key]['rek_belanja'][] = array(
									'kode_akun' => $v['kode_akun'],
									'nama_akun' => $v['nama_akun']
								);
							}
						}
						foreach ($data1 as $k => $v) {
							// if($k >= 10){ continue; }
							$newdata = array();
							$newdata['rek_belanja'] = $v['rek_belanja'];
							$newdata['kode_standar_harga'] = $_POST['tahun_anggaran'];
							$newdata['nama_standar_harga'] = substr($v['harga_satuan'] . ' ' . $v['satuan'] . ' ' . $v['nama_komponen'], 0, 250);
							$newdata['spek'] = $v['spek_komponen'];
							$newdata['satuan'] = $v['satuan'];
							$newdata['kelompok'] = 9;
							$newdata['harga'] = $v['harga_satuan'];
							$newdata['kode_gol_standar_harga'] = $_POST['tahun_anggaran'];
							$newdata['kode_kel_standar_harga'] = $v['jenis_bl'];
							$newdata['nama_sub_kel_standar_harga'] = substr($v['nama_komponen'], 0, 250);
							$data[] = $newdata;
						}
					} else {
						$data_ssh = $wpdb->get_results($wpdb->prepare(
							"
							SELECT 
								* 
							from data_ssh 
							where tahun_anggaran=%d
								and is_deleted=0
								and kelompok=%d",
							$_POST['tahun_anggaran'],
							$_POST['kelompok']
						), ARRAY_A);
						$data = array();
						foreach ($data_ssh as $k => $v) {
							// if($k >= 10){ continue; }
							$v['rek_belanja'] = $wpdb->get_results("SELECT * from data_ssh_rek_belanja where id_standar_harga=" . $v['id_standar_harga'], ARRAY_A);
							$data[] = $v;
						}
					}
					$ret['data'] = $data;
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'ID kelompok harus diisi! 1=SSH, 4=SBU, 2=HSPK, 3=ASB';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_ssh_fmis()
	{
		global $wpdb;
		$ret = array(
			'no_perkada'	=> $_POST['no_perkada'],
			'tgl_perkada'	=> $_POST['tgl_perkada'],
			'keterangan'	=> $_POST['keterangan'],
			'golongan'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (
					!empty($_POST['kelompok'])
					&& (
						$_POST['kelompok'] == 1 // SSH
						|| $_POST['kelompok'] == 2 // HSPK
						|| $_POST['kelompok'] == 3 // ASB
						|| $_POST['kelompok'] == 4 // SBU
					)
				) {
					$data_ssh = $wpdb->get_results($wpdb->prepare(
						"
						SELECT 
							* 
						from data_ssh 
						where tahun_anggaran=%d
							and is_deleted=0
							and kelompok=%d",
						$_POST['tahun_anggaran'],
						$_POST['kelompok']
					), ARRAY_A);
					$data = array();
					foreach ($data_ssh as $k => $v) {
						if ($k >= 10) {
							continue;
						}
						$rek_ssh = explode('.', $v['kode_kel_standar_harga']);
						$rek_golongan = $rek_ssh[0] . '.' . $rek_ssh[1] . '.' . $rek_ssh[2] . '.' . $rek_ssh[3] . '.' . $rek_ssh[4];
						$rek_kelompok = $rek_golongan . '.' . $rek_ssh[5];
						$rek_sub_kelompok = $v['kode_kel_standar_harga'];
						if (empty($data[$rek_golongan])) {
							$data[$rek_golongan] = array(
								'no_golongan' => count($data) + 1,
								'uraian_golongan' => $rek_golongan,
								'kelompok' => array()
							);
						}
						if (empty($data[$rek_golongan]['kelompok'][$rek_kelompok])) {
							$data[$rek_golongan]['kelompok'][$rek_kelompok] = array(
								'no_kelompok' => count($data[$rek_golongan]['kelompok']) + 1,
								'uraian_kelompok' => $rek_kelompok,
								'sub_kelompok' => array()
							);
						}
						if (empty($data[$rek_golongan]['kelompok'][$rek_kelompok]['sub_kelompok'][$rek_sub_kelompok])) {
							$data[$rek_golongan]['kelompok'][$rek_kelompok]['sub_kelompok'][$rek_sub_kelompok] = array(
								'no_sub_kelompok' => count($data[$rek_golongan]['kelompok'][$rek_kelompok]['sub_kelompok']) + 1,
								'uraian_sub_kelompok' => $rek_sub_kelompok . ' ' . $v['nama_kel_standar_harga'],
								'item_ssh' => array()
							);
						}
						$v['rek_belanja'] = $wpdb->get_results("SELECT kode_akun, nama_akun from data_ssh_rek_belanja where id_standar_harga=" . $v['id_standar_harga'], ARRAY_A);
						$no_item = count($data[$rek_golongan]['kelompok'][$rek_kelompok]['sub_kelompok'][$rek_sub_kelompok]) + 1;
						$data[$rek_golongan]['kelompok'][$rek_kelompok]['sub_kelompok'][$rek_sub_kelompok]['item_ssh'][] = array(
							'id_ssh' => $v['id_standar_harga'],
							'no_item' => $no_item,
							'uraian_item' => $v['nama_standar_harga'],
							'spesifikasi' => $rek_sub_kelompok . ' ' . $v['spek'],
							'satuan' => $v['satuan'],
							'harga' => $v['harga'],
							'rek_belanja' => $v['rek_belanja']
						);
					}
					$ret['golongan'] = $data;
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'ID kelompok harus diisi! 1=SSH, 4=SBU, 2=HSPK, 3=ASB';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function datassh($atts)
	{
		$a = shortcode_atts(array(
			'filter' => '',
		), $atts);
		global $wpdb;

		$where = '';
		if (!empty($a['filter'])) {
			if ($a['filter'] == 'is_deleted') {
				$where = 'where is_deleted=1';
			}
		}
		$data_ssh = $wpdb->get_results("SELECT * from data_ssh " . $where, ARRAY_A);
		$tbody = '';
		$no = 1;
		foreach ($data_ssh as $k => $v) {
			// if($k >= 10){ continue; }
			$data_rek = $wpdb->get_results("SELECT * from data_ssh_rek_belanja where id_standar_harga=" . $v['id_standar_harga'], ARRAY_A);
			$rek = array();
			foreach ($data_rek as $key => $value) {
				$rek[] = $value['nama_akun'];
			}
			if (!empty($a['filter'])) {
				if ($a['filter'] == 'rek_kosong' && !empty($rek)) {
					continue;
				}
			}

			$kelompok = "";
			if ($v['kelompok'] == 1) {
				$kelompok = 'SSH';
			}
			$tbody .= '
				<tr>
					<td class="text-center">' . number_format($no, 0, ",", ".") . '</td>
					<td class="text-center">ID: ' . $v['id_standar_harga'] . '<br>Update pada:<br>' . $v['update_at'] . '</td>
					<td>' . $v['kode_standar_harga'] . '</td>
					<td>' . $v['nama_standar_harga'] . '</td>
					<td class="text-center">' . $v['satuan'] . '</td>
					<td>' . $v['spek'] . '</td>
					<td class="text-center">' . $v['is_deleted'] . '</td>
					<td class="text-center">' . $v['is_locked'] . '</td>
					<td class="text-center">' . $kelompok . '</td>
					<td class="text-right">Rp ' . number_format($v['harga'], 2, ",", ".") . '</td>
					<td>' . implode('<br>', $rek) . '</td>
				</tr>
			';
			$no++;
		}
		if (empty($tbody)) {
			$tbody = '<tr><td colspan="11" class="text-center">Data Kosong!</td></tr>';
		}
		$table = '
			<style>
				.text-center {
					text-align: center;
				}
				.text-right {
					text-align: right;
				}
			</style>
			<table>
				<thead>
					<tr>
						<th class="text-center">No</th>
						<th class="text-center">ID Standar Harga</th>
						<th class="text-center">Kode</th>
						<th class="text-center" style="width: 200px;">Nama</th>
						<th class="text-center">Satuan</th>
						<th class="text-center" style="width: 200px;">Spek</th>
						<th class="text-center">Deleted</th>
						<th class="text-center">Locked</th>
						<th class="text-center">Kelompok</th>
						<th class="text-center">Harga</th>
						<th class="text-center">Rekening Belanja</th>
					</tr>
				</thead>
				<tbody>' . $tbody . '</tbody>
			</table>
		';
		echo $table;
	}

	public function singkron_user_deskel()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data desa/kelurahan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					$data = $_POST['data'];
					$cek = $wpdb->get_var("SELECT id_lurah from data_desa_kelurahan where tahun_anggaran=" . $_POST['tahun_anggaran'] . " AND id_lurah=" . $data['id_lurah']);
					$opsi = array(
						'camat_teks' => $data['camat_teks'],
						'id_camat' => $data['id_camat'],
						'id_daerah' => $data['id_daerah'],
						'id_level' => $data['id_level'],
						'id_lurah' => $data['id_lurah'],
						'id_profil' => $data['id_profil'],
						'id_user' => $data['id_user'],
						'is_desa' => $data['is_desa'],
						'is_locked' => $data['is_locked'],
						'jenis' => $data['jenis'],
						'kab_kota' => $data['kab_kota'],
						'kode_lurah' => $data['kode_lurah'],
						'login_name' => $data['login_name'],
						'lurah_teks' => $data['lurah_teks'],
						'nama_daerah' => $data['nama_daerah'],
						'nama_user' => $data['nama_user'],
						'accasmas' => $data['accasmas'],
						'accbankeu' => $data['accbankeu'],
						'accdisposisi' => $data['accdisposisi'],
						'accgiat' => $data['accgiat'],
						'acchibah' => $data['acchibah'],
						'accinput' => $data['accinput'],
						'accjadwal' => $data['accjadwal'],
						'acckunci' => $data['acckunci'],
						'accmaster' => $data['accmaster'],
						'accspv' => $data['accspv'],
						'accunit' => $data['accunit'],
						'accusulan' => $data['accusulan'],
						'alamatteks' => $data['alamatteks'],
						'camatteks' => $data['camatteks'],
						'daerahpengusul' => $data['daerahpengusul'],
						'dapil' => $data['dapil'],
						'emailteks' => $data['emailteks'],
						'fraksi' => $data['fraksi'],
						'idcamat' => $data['idcamat'],
						'iddaerahpengusul' => $data['iddaerahpengusul'],
						'idkabkota' => $data['idkabkota'],
						'idlevel' => $data['idlevel'],
						'idlokasidesa' => $data['idlokasidesa'],
						'idlurah' => $data['idlurah'],
						'idlurahpengusul' => $data['idlurahpengusul'],
						'idprofil' => $data['idprofil'],
						'iduser' => $data['iduser'],
						'jabatan' => $data['jabatan'],
						'loginname' => $data['loginname'],
						'lokasidesateks' => $data['lokasidesateks'],
						'lurahteks' => $data['lurahteks'],
						'nama' => $data['nama'],
						'namapengusul' => $data['namapengusul'],
						'nik' => $data['nik'],
						'nip' => $data['nip'],
						'notelp' => $data['notelp'],
						'npwp' => $data['npwp'],
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);
					if (!empty($cek)) {
						$wpdb->update('data_desa_kelurahan', $opsi, array(
							'id_lurah' => $data['id_lurah'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_desa_kelurahan', $opsi);
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Data Desa/Kelurahan Salah!';
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

	public function non_active_user()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil menonactivekan user!',
			'action'	=> $_POST['action']
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['id_level'])) {
					$wpdb->update('data_dewan', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'idlevel' => $_POST['id_level'],
						'id_sub_skpd' => $_POST['id_sub_skpd']
					));
					$ret['sql'] = $wpdb->last_query;
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Data User Salah!';
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

	public function singkron_user_dewan()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data user!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					} else {
						$data = $_POST['data'];
					}
					$cek = $wpdb->get_var("SELECT iduser from data_dewan where tahun_anggaran=" . $_POST['tahun_anggaran'] . " AND iduser=" . $data['iduser']);
					if (!isset($data['active'])) {
						$data['active'] = 1;
					}
					$opsi = array(
						'accasmas' => $data['accasmas'],
						'accbankeu' => $data['accbankeu'],
						'accdisposisi' => $data['accdisposisi'],
						'accgiat' => $data['accgiat'],
						'acchibah' => $data['acchibah'],
						'accinput' => $data['accinput'],
						'accjadwal' => $data['accjadwal'],
						'acckunci' => $data['acckunci'],
						'accmaster' => $data['accmaster'],
						'accspv' => $data['accspv'],
						'accunit' => $data['accunit'],
						'accusulan' => $data['accusulan'],
						'alamatteks' => $data['alamatteks'],
						'camatteks' => $data['camatteks'],
						'daerahpengusul' => $data['daerahpengusul'],
						'dapil' => $data['dapil'],
						'emailteks' => $data['emailteks'],
						'fraksi' => $data['fraksi'],
						'idcamat' => $data['idcamat'],
						'iddaerahpengusul' => $data['iddaerahpengusul'],
						'idkabkota' => $data['idkabkota'],
						'idlevel' => $data['idlevel'],
						'idlokasidesa' => $data['idlokasidesa'],
						'idlurah' => $data['idlurah'],
						'idlurahpengusul' => $data['idlurahpengusul'],
						'idprofil' => $data['idprofil'],
						'iduser' => $data['iduser'],
						'jabatan' => $data['jabatan'],
						'loginname' => $data['loginname'],
						'lokasidesateks' => $data['lokasidesateks'],
						'lurahteks' => $data['lurahteks'],
						'nama' => $data['nama'],
						'namapengusul' => $data['namapengusul'],
						'nik' => $data['nik'],
						'nip' => $data['nip'],
						'notelp' => $data['notelp'],
						'npwp' => $data['npwp'],
						'id_sub_skpd' => $data['id_sub_skpd'],
						'active' => $data['active'],
						'is_locked' => $data['is_locked'],
						'accmonitor' => $data['accmonitor'],
						'akses_user' => $data['akses_user'],
						'akta_kumham' => $data['akta_kumham'],
						'ijin_op' => $data['ijin_op'],
						'is_profil_ok' => $data['is_profil_ok'],
						'is_vertikal' => $data['is_vertikal'],
						'map_lat_lokasi' => $data['map_lat_lokasi'],
						'map_lng_lokasi' => $data['map_lng_lokasi'],
						'no_sertifikat' => $data['no_sertifikat'],
						'path_foto' => $data['path_foto'],
						'surat_dom' => $data['surat_dom'],
						'kode_camat' => $data['kode_camat'],
						'kode_lurah' => $data['kode_lurah'],
						'kode_ddn_2' => $data['kode_ddn_2'],
						'status' => $data['status'],
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);
					if (!empty($cek)) {
						$wpdb->update('data_dewan', $opsi, array(
							'iduser' => $data['iduser'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_dewan', $opsi);
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Data User Salah!';
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

	public function singkron_skpd_mitra_bappeda()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data SKPD user mitra!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					} else {
						$data = $_POST['data'];
					}
					$wpdb->update('data_skpd_mitra_bappeda', array('active' => 0), array(
						'id_user' => $_POST['id_user'],
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($data as $k => $v) {
						if (empty($v['id_user'])) {
							continue;
						}
						$cek = $wpdb->get_var(
							"
							SELECT 
								id_user 
							from data_skpd_mitra_bappeda 
							where tahun_anggaran=" . $_POST['tahun_anggaran'] . " 
								AND id_unit=" . $v['id_unit'] . "
								AND id_user=" . $v['id_user']
						);
						$opsi = array(
							'akses_user' => $v['akses_user'],
							'id_level' => $v['id_level'],
							'id_unit' => $v['id_unit'],
							'id_user' => $v['id_user'],
							'is_locked' => $v['is_locked'],
							'kode_skpd' => $v['kode_skpd'],
							'login_name' => $v['login_name'],
							'nama_skpd' => $v['nama_skpd'],
							'nama_user' => $v['nama_user'],
							'nip' => $v['nip'],
							'active' => 1,
							// 'active' => $v['active'],
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_skpd_mitra_bappeda', $opsi, array(
								'id_unit' => $v['id_unit'],
								'id_user' => $v['id_user'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_skpd_mitra_bappeda', $opsi);
						}
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Data SKPD Mitra Salah!';
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

	public function singkron_asmas()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data ASMAS!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					} else {
						$data = $_POST['data'];
					}
					$cek = $wpdb->get_var("SELECT id_usulan from data_asmas where tahun_anggaran=" . $_POST['tahun_anggaran'] . " AND id_usulan=" . $data['id_usulan']);
					$opsi = array(
						'alamat_teks' => $data['alamat_teks'],
						'anggaran' => $data['anggaran'],
						'batal_teks' => $data['batal_teks'],
						'bidang_urusan' => $data['bidang_urusan'],
						'created_date' => $data['created_date'],
						'created_user' => $data['created_user'],
						'file_foto' => $data['file_foto'],
						'file_pengantar' => $data['file_pengantar'],
						'file_proposal' => $data['file_proposal'],
						'file_rab' => $data['file_rab'],
						'giat_teks' => $data['giat_teks'],
						'id_bidang_urusan' => $data['id_bidang_urusan'],
						'id_daerah' => $data['id_daerah'],
						'id_jenis_profil' => $data['id_jenis_profil'],
						'id_jenis_usul' => $data['id_jenis_usul'],
						'id_kab_kota' => $data['id_kab_kota'],
						'id_kecamatan' => $data['id_kecamatan'],
						'id_kelurahan' => $data['id_kelurahan'],
						'id_pengusul' => $data['id_pengusul'],
						'id_profil' => $data['id_profil'],
						'id_unit' => $data['id_unit'],
						'id_usulan' => $data['id_usulan'],
						'is_batal' => $data['is_batal'],
						'is_tolak' => $data['is_tolak'],
						'jenis_belanja' => $data['jenis_belanja'],
						'jenis_profil' => $data['jenis_profil'],
						'jenis_usul_teks' => $data['jenis_usul_teks'],
						'kelompok' => $data['kelompok'],
						'kode_skpd' => $data['kode_skpd'],
						'koefisien' => $data['koefisien'],
						'level_pengusul' => $data['level_pengusul'],
						'lokus_usulan' => $data['lokus_usulan'],
						'masalah' => $data['masalah'],
						'nama_daerah' => $data['nama_daerah'],
						'nama_skpd' => $data['nama_skpd'],
						'nama_user' => $data['nama_user'],
						'nip' => $data['nip'],
						'pengusul' => $data['pengusul'],
						'rekom_camat_anggaran' => $data['rekom_camat_anggaran'],
						'rekom_camat_koefisien' => $data['rekom_camat_koefisien'],
						'rekom_camat_rekomendasi' => $data['rekom_camat_rekomendasi'],
						'rekom_lurah_anggaran' => $data['rekom_lurah_anggaran'],
						'rekom_lurah_koefisien' => $data['rekom_lurah_koefisien'],
						'rekom_lurah_rekomendasi' => $data['rekom_lurah_rekomendasi'],
						'rekom_mitra_anggaran' => $data['rekom_mitra_anggaran'],
						'rekom_mitra_koefisien' => $data['rekom_mitra_koefisien'],
						'rekom_mitra_rekomendasi' => $data['rekom_mitra_rekomendasi'],
						'rekom_skpd_anggaran' => $data['rekom_skpd_anggaran'],
						'rekom_skpd_koefisien' => $data['rekom_skpd_koefisien'],
						'rekom_skpd_rekomendasi' => $data['rekom_skpd_rekomendasi'],
						'rekom_tapd_anggaran' => $data['rekom_tapd_anggaran'],
						'rekom_tapd_koefisien' => $data['rekom_tapd_koefisien'],
						'rekom_tapd_rekomendasi' => $data['rekom_tapd_rekomendasi'],
						'rev_skpd' => $data['rev_skpd'],
						'satuan' => $data['satuan'],
						'status_usul' => $data['status_usul'],
						'status_usul_teks' => $data['status_usul_teks'],
						'tolak_teks' => $data['tolak_teks'],
						'tujuan_usul' => $data['tujuan_usul'],
						'detail_alamatteks' => $data['detail_alamatteks'],
						'detail_anggaran' => $data['detail_anggaran'],
						'detail_bidangurusan' => $data['detail_bidangurusan'],
						'detail_camatteks' => $data['detail_camatteks'],
						'detail_filefoto' => $data['detail_filefoto'],
						'detail_filefoto2' => $data['detail_filefoto2'],
						'detail_filefoto3' => $data['detail_filefoto3'],
						'detail_filepengantar' => $data['detail_filepengantar'],
						'detail_fileproposal' => $data['detail_fileproposal'],
						'detail_filerab' => $data['detail_filerab'],
						'detail_gagasan' => $data['detail_gagasan'],
						'detail_idcamat' => $data['detail_idcamat'],
						'detail_idkabkota' => $data['detail_idkabkota'],
						'detail_idkamus' => $data['detail_idkamus'],
						'detail_idlurah' => $data['detail_idlurah'],
						'detail_idskpd' => $data['detail_idskpd'],
						'detail_jenisbelanja' => $data['detail_jenisbelanja'],
						'detail_kodeskpd' => $data['detail_kodeskpd'],
						'detail_langpeta' => $data['detail_langpeta'],
						'detail_latpeta' => $data['detail_latpeta'],
						'detail_lurahteks' => $data['detail_lurahteks'],
						'detail_masalah' => $data['detail_masalah'],
						'detail_namakabkota' => $data['detail_namakabkota'],
						'detail_namaskpd' => $data['detail_namaskpd'],
						'detail_rekomteks' => $data['detail_rekomteks'],
						'detail_satuan' => $data['detail_satuan'],
						'detail_setStatusUsul' => $data['detail_setStatusUsul'],
						'detail_subgiat' => $data['detail_subgiat'],
						'detail_tujuanusul' => $data['detail_tujuanusul'],
						'detail_usulanggaran' => $data['detail_usulanggaran'],
						'detail_usulvolume' => $data['detail_usulvolume'],
						'detail_volume' => $data['detail_volume'],
						'id_unik' => $data['id_unik'],
						'is_verif' => $data['is_verif'],
						'nama_skpd_awal' => $data['nama_skpd_awal'],
						'is_kembalikan' => $data['is_kembalikan'],
						'rekom_teks' => $data['rekom_teks'],
						'kembalikan' => $data['kembalikan'],
						'id_isu' => $data['id_isu'],
						'id_program' => $data['id_program'],
						'detail_koefisien' => $data['detail_koefisien'],
						'is_stat_lahan' => $data['is_stat_lahan'],
						'is_sertifikat_lahan' => $data['is_sertifikat_lahan'],
						'is_rincian_teknis' => $data['is_rincian_teknis'],
						'latbel_teks' => $data['latbel_teks'],
						'dashuk_teks' => $data['dashuk_teks'],
						'maksud_teks' => $data['maksud_teks'],
						'tujuan_teks' => $data['tujuan_teks'],
						'id_akun' => $data['id_akun'],
						'id_prop' => $data['id_prop'],
						'id_bl' => $data['id_bl'],
						'id_sub_bl' => $data['id_sub_bl'],
						'sub_giat_baru' => $data['sub_giat_baru'],
						'detail_id_skpd_awal' => $data['detail_id_skpd_awal'],
						'id_skpd_bl' => $data['id_skpd_bl'],
						'id_sub_skpd_bl' => $data['id_sub_skpd_bl'],
						'id_program_bl' => $data['id_program_bl'],
						'id_giat_bl' => $data['id_giat_bl'],
						'id_sub_giat_bl' => $data['id_sub_giat_bl'],
						'active' => 1,
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);
					if (!empty($cek)) {
						$wpdb->update('data_asmas', $opsi, array(
							'id_usulan' => $data['id_usulan'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_asmas', $opsi);
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Data ASMAS Salah!';
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

	public function singkron_pokir()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data POKIR!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					} else {
						$data = $_POST['data'];
					}
					$cek = $wpdb->get_var($wpdb->prepare(
						"
						SELECT 
							id_usulan 
						from data_pokir 
						where tahun_anggaran=%d AND id_usulan=%d",
						$_POST['tahun_anggaran'],
						$data['id_usulan']
					));
					$opsi = array(
						'alamat_teks' => $data['alamat_teks'],
						'anggaran' => $data['anggaran'],
						'batal_teks' => $data['batal_teks'],
						'bidang_urusan' => $data['bidang_urusan'],
						'created_date' => $data['created_date'],
						'created_user' => $data['created_user'],
						'file_foto' => $data['file_foto'],
						'file_pengantar' => $data['file_pengantar'],
						'file_proposal' => $data['file_proposal'],
						'file_rab' => $data['file_rab'],
						'fraksi_dewan' => $data['fraksi_dewan'],
						'giat_teks' => $data['giat_teks'],
						'id_bidang_urusan' => $data['id_bidang_urusan'],
						'id_jenis_usul' => $data['id_jenis_usul'],
						'id_kab_kota' => $data['id_kab_kota'],
						'id_kecamatan' => $data['id_kecamatan'],
						'id_kelurahan' => $data['id_kelurahan'],
						'id_pengusul' => $data['id_pengusul'],
						'id_reses' => $data['id_reses'],
						'id_unit' => $data['id_unit'],
						'id_usulan' => $data['id_usulan'],
						'is_batal' => $data['is_batal'],
						'is_tolak' => $data['is_tolak'],
						'jenis_belanja' => $data['jenis_belanja'],
						'jenis_usul_teks' => $data['jenis_usul_teks'],
						'kelompok' => $data['kelompok'],
						'kode_skpd' => $data['kode_skpd'],
						'koefisien' => $data['koefisien'],
						'lokus_usulan' => $data['lokus_usulan'],
						'masalah' => $data['masalah'],
						'nama_daerah' => $data['nama_daerah'],
						'nama_skpd' => $data['nama_skpd'],
						'nama_user' => $data['nama_user'],
						'pengusul' => $data['pengusul'],
						'rekom_mitra_anggaran' => $data['rekom_mitra_anggaran'],
						'rekom_mitra_koefisien' => $data['rekom_mitra_koefisien'],
						'rekom_mitra_rekomendasi' => $data['rekom_mitra_rekomendasi'],
						'rekom_setwan_anggaran' => $data['rekom_setwan_anggaran'],
						'rekom_setwan_koefisien' => $data['rekom_setwan_koefisien'],
						'rekom_setwan_rekomendasi' => $data['rekom_setwan_rekomendasi'],
						'rekom_skpd_anggaran' => $data['rekom_skpd_anggaran'],
						'rekom_skpd_koefisien' => $data['rekom_skpd_koefisien'],
						'rekom_skpd_rekomendasi' => $data['rekom_skpd_rekomendasi'],
						'rekom_tapd_anggaran' => $data['rekom_tapd_anggaran'],
						'rekom_tapd_koefisien' => $data['rekom_tapd_koefisien'],
						'rekom_tapd_rekomendasi' => $data['rekom_tapd_rekomendasi'],
						'satuan' => $data['satuan'],
						'status_usul' => $data['status_usul'],
						'status_usul_teks' => $data['status_usul_teks'],
						'tolak_teks' => $data['tolak_teks'],
						'detail_alamatteks' => $data['detail_alamatteks'],
						'detail_anggaran' => $data['detail_anggaran'],
						'detail_bidangurusan' => $data['detail_bidangurusan'],
						'detail_camatteks' => $data['detail_camatteks'],
						'detail_filefoto' => $data['detail_filefoto'],
						'detail_filefoto2' => $data['detail_filefoto2'],
						'detail_filefoto3' => $data['detail_filefoto3'],
						'detail_filepengantar' => $data['detail_filepengantar'],
						'detail_fileproposal' => $data['detail_fileproposal'],
						'detail_filerab' => $data['detail_filerab'],
						'detail_gagasan' => $data['detail_gagasan'],
						'detail_idcamat' => $data['detail_idcamat'],
						'detail_idkabkota' => $data['detail_idkabkota'],
						'detail_idkamus' => $data['detail_idkamus'],
						'detail_idlurah' => $data['detail_idlurah'],
						'detail_idskpd' => $data['detail_idskpd'],
						'detail_jenisbelanja' => $data['detail_jenisbelanja'],
						'detail_kodeskpd' => $data['detail_kodeskpd'],
						'detail_langpeta' => $data['detail_langpeta'],
						'detail_latpeta' => $data['detail_latpeta'],
						'detail_lurahteks' => $data['detail_lurahteks'],
						'detail_masalah' => $data['detail_masalah'],
						'detail_namakabkota' => $data['detail_namakabkota'],
						'detail_namaskpd' => $data['detail_namaskpd'],
						'detail_rekomteks' => $data['detail_rekomteks'],
						'detail_satuan' => $data['detail_satuan'],
						'detail_setStatusUsul' => $data['detail_setStatusUsul'],
						'detail_subgiat' => $data['detail_subgiat'],
						'detail_usulanggaran' => $data['detail_usulanggaran'],
						'detail_usulvolume' => $data['detail_usulvolume'],
						'detail_volume' => $data['detail_volume'],
						'id_unik' => $data['id_unik'],
						'is_verif' => $data['is_verif'],
						'nama_skpd_awal' => $data['nama_skpd_awal'],
						'is_kembalikan' => $data['is_kembalikan'],
						'rekom_teks' => $data['rekom_teks'],
						'kembalikan' => $data['kembalikan'],
						'id_isu' => $data['id_isu'],
						'id_program' => $data['id_program'],
						'detail_koefisien' => $data['detail_koefisien'],
						'is_stat_lahan' => $data['is_stat_lahan'],
						'is_sertifikat_lahan' => $data['is_sertifikat_lahan'],
						'is_rincian_teknis' => $data['is_rincian_teknis'],
						'latbel_teks' => $data['latbel_teks'],
						'dashuk_teks' => $data['dashuk_teks'],
						'maksud_teks' => $data['maksud_teks'],
						'tujuan_teks' => $data['tujuan_teks'],
						'id_akun' => $data['id_akun'],
						'id_prop' => $data['id_prop'],
						'id_bl' => $data['id_bl'],
						'id_sub_bl' => $data['id_sub_bl'],
						'sub_giat_baru' => $data['sub_giat_baru'],
						'detail_id_skpd_awal' => $data['detail_id_skpd_awal'],
						'id_skpd_bl' => $data['id_skpd_bl'],
						'id_sub_skpd_bl' => $data['id_sub_skpd_bl'],
						'id_program_bl' => $data['id_program_bl'],
						'id_giat_bl' => $data['id_giat_bl'],
						'id_sub_giat_bl' => $data['id_sub_giat_bl'],
						'active' => 1,
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);
					if (!empty($cek)) {
						$wpdb->update('data_pokir', $opsi, array(
							'id_usulan' => $data['id_usulan'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_pokir', $opsi);
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Data ASMAS Salah!';
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

	public function singkron_kamus_usulan_pokir()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data kamus usulan POKIR!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					} else {
						$data = $_POST['data'];
					}
					$cek = $wpdb->get_var($wpdb->prepare(
						"
						SELECT 
							id_kamus 
						from data_kamus_usulan_pokir 
						where tahun_anggaran=%d 
							AND id_kamus=%d",
						$_POST['tahun_anggaran'],
						$data['id_kamus']
					));
					$opsi = array(
						'anggaran' => $data['anggaran'],
						'bidang_urusan' => $data['bidang_urusan'],
						'giat_teks' => $data['giat_teks'],
						'id_kamus' => $data['id_kamus'],
						'id_program' => $data['id_program'],
						'id_unit' => $data['id_unit'],
						'is_locked' => $data['is_locked'],
						'idbidangurusan' => $data['idbidangurusan'],
						'idskpd' => $data['idskpd'],
						'kodeprogram' => $data['kodeprogram'],
						'namaprogram' => $data['namaprogram'],
						'jenis_profil' => $data['jenis_profil'],
						'kelompok' => $data['kelompok'],
						'kode_skpd' => $data['kode_skpd'],
						'nama_skpd' => $data['nama_skpd'],
						'outcome_teks' => $data['outcome_teks'],
						'output_teks' => $data['output_teks'],
						'pekerjaan' => $data['pekerjaan'],
						'prioritas_teks' => $data['prioritas_teks'],
						'satuan' => $data['satuan'],
						'tipe' => $data['tipe'],
						'id_jenis_usul' => $data['id_jenis_usul'],
						'active' => 1,
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);
					if (!empty($cek)) {
						$wpdb->update('data_kamus_usulan_pokir', $opsi, array(
							'id_kamus' => $data['id_kamus'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_kamus_usulan_pokir', $opsi);
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Data Salah!';
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

	public function singkron_pengaturan_sipd()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data pengaturan SIPD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					} else {
						$data = $_POST['data'];
					}
					$cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							kepala_daerah 
						from data_pengaturan_sipd 
						where tahun_anggaran=%d 
							AND id_daerah=%d
					", $_POST['tahun_anggaran'], $data['id_daerah']));
					$opsi = array(
						'id_daerah' => $data['id_daerah'],
						'kepala_daerah' => $data['kepala_daerah'],
						'wakil_kepala_daerah' => $data['wakil_kepala_daerah'],
						'awal_rpjmd' => $data['awal_rpjmd'],
						'akhir_rpjmd' => $data['akhir_rpjmd'],
						'set_kpa_sekda' => $data['set_kpa_sekda'],
						'set_kpa_sub_sekda'  => $data['set_kpa_sub_sekda'],
						'id_setup_anggaran'  => $data['id_setup_anggaran'],
						'set_rkpd'  => $data['set_rkpd'],
						'set_kua'  => $data['set_kua'],
						'ref_program'  => $data['ref_program'],
						'ref_giat'  => $data['ref_giat'],
						'ref_akun'  => $data['ref_akun'],
						'ref_skpd'  => $data['ref_skpd'],
						'ref_sumber_dana'  => $data['ref_sumber_dana'],
						'ref_lokasi'  => $data['ref_lokasi'],
						'ref_standar_harga'  => $data['ref_standar_harga'],
						'is_locked'  => $data['is_locked'],
						'jenis_set_pagu'  => $data['jenis_set_pagu'],
						'tahun_aksi'  => $data['tahun_aksi'],
						'status_kd'  => $data['status_kd'],
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);
					// sipd merah
					if (!empty($data['daerah'])) {
						$opsi['daerah'] = $data['daerah'];
						update_option('_crb_daerah', $data['daerah']);
					}
					// sipd ri
					if (!empty($data['id_prop'])) {
						update_option('_crb_id_lokasi_prov', $data['id_prop']);
					}
					// sipd ri
					if (
						!empty($data['id_kab'])
						&& empty($data['is_prop'])
					) {
						update_option('_crb_id_lokasi_kokab', $data['id_kab']);
					}

					if (!empty($data['pelaksana_rkpd'])) {
						$opsi['pelaksana_rkpd'] = $data['pelaksana_rkpd'];
					}
					if (!empty($data['pelaksana_kua'])) {
						$opsi['pelaksana_kua'] = $data['pelaksana_kua'];
					}
					if (!empty($data['pelaksana_apbd'])) {
						$opsi['pelaksana_apbd'] = $data['pelaksana_apbd'];
					}
					update_option('_crb_kepala_daerah', $data['kepala_daerah']);
					update_option('_crb_wakil_daerah', $data['wakil_kepala_daerah']);
					if (!empty($cek)) {
						$wpdb->update('data_pengaturan_sipd', $opsi, array(
							'id_daerah' => $data['id_daerah'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_pengaturan_sipd', $opsi);
					}
					// print_r($opsi); die($wpdb->last_query);
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Data Pengaturan Salah!';
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

	public function singkron_akun_belanja()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Akun Rekening Belanja!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['akun'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$akun = json_decode(stripslashes(html_entity_decode($_POST['akun'])), true);
					} else {
						$akun = $_POST['akun'];
					}

					if (!empty($_POST['page']) && $_POST['page'] == 1) {
						$wpdb->update('data_akun', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else if (empty($_POST['page'])) {
						$wpdb->update('data_akun', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					}
					foreach ($akun as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id_akun 
							from data_akun 
							where tahun_anggaran=%d 
								AND id_akun=%d
						", $_POST['tahun_anggaran'], $v['id_akun']));
						$opsi = array(
							'belanja' => $v['belanja'],
							'id_akun' => $v['id_akun'],
							'is_bagi_hasil' => $v['is_bagi_hasil'],
							'is_bankeu_khusus' => $v['is_bankeu_khusus'],
							'is_bankeu_umum' => $v['is_bankeu_umum'],
							'is_barjas' => $v['is_barjas'],
							'is_bl' => $v['is_bl'],
							'is_bos' => $v['is_bos'],
							'is_btt' => $v['is_btt'],
							'is_bunga' => $v['is_bunga'],
							'is_gaji_asn' => $v['is_gaji_asn'],
							'is_hibah_brg' => $v['is_hibah_brg'],
							'is_hibah_uang' => $v['is_hibah_uang'],
							'is_locked' => $v['is_locked'],
							'is_modal_tanah' => $v['is_modal_tanah'],
							'is_pembiayaan' => $v['is_pembiayaan'],
							'is_pendapatan' => $v['is_pendapatan'],
							'is_sosial_brg' => $v['is_sosial_brg'],
							'is_sosial_uang' => $v['is_sosial_uang'],
							'is_subsidi' => $v['is_subsidi'],
							'kode_akun' => $v['kode_akun'],
							'nama_akun' => $v['nama_akun'],
							'set_input' => $v['set_input'],
							'set_lokus' => $v['set_lokus'],
							'ket_akun' => $v['ket_akun'],
							'kode_akun_lama' => $v['kode_akun_lama'],
							'kode_akun_revisi' => $v['kode_akun_revisi'],
							'kunci_tahun' => $v['kunci_tahun'],
							'level' => $v['level'],
							'mulai_tahun' => $v['mulai_tahun'],
							'pembiayaan' => $v['pembiayaan'],
							'pendapatan' => $v['pendapatan'],
							'set_kab_kota' => $v['set_kab_kota'],
							'set_prov' => $v['set_prov'],
							'status' => $v['status'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_akun', $opsi, array(
								'id_akun' => $v['id_akun'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_akun', $opsi);
						}
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Akun Belanja Salah!';
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

	public function singkron_pendapatan()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Pendapatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
					$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					$_POST['data'] = $data;
				} else {
					$data = $_POST['data'];
				}
				$wpdb->update('data_pendapatan', array('active' => 0), array(
					'tahun_anggaran' => $_POST['tahun_anggaran'],
					'id_skpd' => $_POST['id_skpd']
				));
				foreach ($data as $k => $v) {
					$cek = $wpdb->get_var($wpdb->prepare(
						"
						SELECT id_pendapatan 
						from data_pendapatan 
						where tahun_anggaran=%d 
							AND id_pendapatan=%d 
							AND id_skpd=%d",
						$_POST['tahun_anggaran'],
						$v['id_pendapatan'],
						$_POST['id_skpd']
					));
					$opsi = array(
						'created_user' => $v['created_user'],
						'createddate' => $v['createddate'],
						'createdtime' => $v['createdtime'],
						'id_pendapatan' => $v['id_pendapatan'],
						'keterangan' => $v['keterangan'],
						'kode_akun' => $v['kode_akun'],
						'nama_akun' => $v['nama_akun'],
						'nilaimurni' => $v['nilaimurni'],
						'program_koordinator' => $v['program_koordinator'],
						'rekening' => $v['rekening'],
						'skpd_koordinator' => $v['skpd_koordinator'],
						'total' => $v['total'],
						'updated_user' => $v['updated_user'],
						'updateddate' => $v['updateddate'],
						'updatedtime' => $v['updatedtime'],
						'uraian' => $v['uraian'],
						'urusan_koordinator' => $v['urusan_koordinator'],
						'user1' => $v['user1'],
						'user2' => $v['user2'],
						'id_skpd' => $_POST['id_skpd'],
						'id_akun' => $v['id_akun'],
						'id_jadwal_murni' => $v['id_jadwal_murni'],
						'kode_akun' => $v['kode_akun'],
						'koefisien' => $v['koefisien'],
						'kua_murni' => $v['kua_murni'],
						'kua_pak' => $v['kua_pak'],
						'rkpd_murni' => $v['rkpd_murni'],
						'rkpd_pak' => $v['rkpd_pak'],
						'satuan' => $v['satuan'],
						'volume' => $v['volume'],
						'active' => 1,
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);
					if (!empty($cek)) {
						$wpdb->update('data_pendapatan', $opsi, array(
							'id_pendapatan' => $v['id_pendapatan'],
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'id_skpd' => $_POST['id_skpd']
						));
					} else {
						$wpdb->insert('data_pendapatan', $opsi);
					}
				}

				if (
					get_option('_crb_singkron_simda') == 1
					&& get_option('_crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
				) {
					$debug = false;
					if (get_option('_crb_singkron_simda_debug') == 1) {
						$debug = true;
					}
					$this->simda->singkronSimdaPendapatan(array('return' => $debug));
				}
				// print_r($ssh); die();
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

	public function singkron_pembiayaan()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Pembiayaan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
					$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				$type = array();
				foreach ($data as $k => $v) {
					if (empty($type[$v['type']])) {
						$type[$v['type']] = 1;
						$wpdb->update('data_pembiayaan', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'id_skpd' => $_POST['id_skpd'],
							'type' => $v['type']
						));
					}
					$cek = $wpdb->get_var($wpdb->prepare(
						"
						SELECT id_pembiayaan 
						from data_pembiayaan 
						where tahun_anggaran=%d
							AND id_pembiayaan=%d
							AND id_skpd=%d
							AND type=%s",
						$_POST['tahun_anggaran'],
						$v['id_pembiayaan'],
						$_POST['id_skpd'],
						$v['type']
					));
					$opsi = array(
						'created_user' => $v['created_user'],
						'createddate' => $v['createddate'],
						'createdtime' => $v['createdtime'],
						'id_pembiayaan' => $v['id_pembiayaan'],
						'keterangan' => $v['keterangan'],
						'kode_akun' => $v['kode_akun'],
						'nama_akun' => $v['nama_akun'],
						'nilaimurni' => $v['nilaimurni'],
						'program_koordinator' => $v['program_koordinator'],
						'rekening' => $v['rekening'],
						'skpd_koordinator' => $v['skpd_koordinator'],
						'total' => $v['total'],
						'updated_user' => $v['updated_user'],
						'updateddate' => $v['updateddate'],
						'updatedtime' => $v['updatedtime'],
						'uraian' => $v['uraian'],
						'urusan_koordinator' => $v['urusan_koordinator'],
						'type' => $v['type'],
						'user1' => $v['user1'],
						'user2' => $v['user2'],
						'id_skpd' => $_POST['id_skpd'],
						'id_akun' => $v['id_akun'],
						'id_jadwal_murni' => $v['id_jadwal_murni'],
						'kode_akun' => $v['kode_akun'],
						'koefisien' => $v['koefisien'],
						'kua_murni' => $v['kua_murni'],
						'kua_pak' => $v['kua_pak'],
						'rkpd_murni' => $v['rkpd_murni'],
						'rkpd_pak' => $v['rkpd_pak'],
						'satuan' => $v['satuan'],
						'volume' => $v['volume'],
						'active' => 1,
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);
					if (!empty($cek)) {
						$wpdb->update('data_pembiayaan', $opsi, array(
							'id_pembiayaan' => $v['id_pembiayaan'],
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'type' => $v['type'],
							'id_skpd' => $_POST['id_skpd']
						));
					} else {
						$wpdb->insert('data_pembiayaan', $opsi);
					}
				}

				if (
					get_option('_crb_singkron_simda') == 1
					&& get_option('_crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
				) {
					$debug = false;
					if (get_option('_crb_singkron_simda_debug') == 1) {
						$debug = true;
					}
					$this->simda->singkronSimdaPembiayaan(array('return' => $debug));
				}
				// print_r($ssh); die();
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

	public function singkron_unit()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil export Unit!',
			'request_data'	=> array(),
			'renja_link'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data_unit'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$data_unit = json_decode(stripslashes(html_entity_decode($_POST['data_unit'])), true);
					} else {
						$data_unit = $_POST['data_unit'];
					}
					// $wpdb->update('data_unit', array( 'active' => 0 ), array(
					// 	'tahun_anggaran' => $_POST['tahun_anggaran']
					// ));

					foreach ($data_unit as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id_skpd 
							from data_unit 
							where tahun_anggaran=%d 
								AND id_skpd=%d
						", $_POST['tahun_anggaran'], $v['id_skpd']));
						$opsi = array(
							'id_setup_unit' => $v['id_setup_unit'],
							'id_skpd' => $v['id_skpd'],
							'id_unit' => $v['id_unit'],
							'is_skpd' => $v['is_skpd'],
							'kode_skpd' => $v['kode_skpd'],
							'kunci_skpd' => $v['kunci_skpd'],
							'nama_skpd' => $v['nama_skpd'],
							'posisi' => $v['posisi'],
							'status' => $v['status'],
							'bidur_1' => $v['bidur_1'],
							'bidur_2' => $v['bidur_2'],
							'bidur_3' => $v['bidur_3'],
							'idinduk' => $v['idinduk'],
							'ispendapatan' => $v['ispendapatan'],
							'isskpd' => $v['isskpd'],
							'kode_skpd_1' => $v['kode_skpd_1'],
							'kode_skpd_2' => $v['kode_skpd_2'],
							'kodeunit' => $v['kodeunit'],
							'komisi' => $v['komisi'],
							'namabendahara' => $v['namabendahara'],
							'namakepala' => $v['namakepala'],
							'namaunit' => $v['namaunit'],
							'nipbendahara' => $v['nipbendahara'],
							'nipkepala' => $v['nipkepala'],
							'pangkatkepala' => $v['pangkatkepala'],
							'setupunit' => $v['setupunit'],
							'statuskepala' => $v['statuskepala'],
							'mapping' => $v['mapping'],
							'id_kecamatan' => $v['id_kecamatan'],
							'id_strategi' => $v['id_strategi'],
							'is_dpa_khusus' => $v['is_dpa_khusus'],
							'is_ppkd' => $v['is_ppkd'],
							'set_input' => $v['set_input'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (!empty($cek)) {
							$wpdb->update('data_unit', $opsi, array(
								'id_skpd' => $v['id_skpd'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
							$opsi['update'] = 1;
						} else {
							$wpdb->insert('data_unit', $opsi);
							$opsi['insert'] = 1;
						}
						$ret['request_data'][] = $opsi;

						$nama_page = $_POST['tahun_anggaran'] . ' | ' . $v['kode_skpd'] . ' | ' . $v['nama_skpd'];
						$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');

						$_post = array(
							'post_title'	=> $nama_page,
							'post_content'	=> '[tampilrkpd id_skpd="' . $v['id_skpd'] . '" tahun_anggaran="' . $_POST['tahun_anggaran'] . '"]',
							'post_type'		=> 'page',
							'post_status'	=> 'private',
							'comment_status'	=> 'closed'
						);
						if (empty($custom_post) || empty($custom_post->ID)) {
							$id = wp_insert_post($_post);
							$_post['insert'] = 1;
							$_post['ID'] = $id;
						} else {
							$_post['ID'] = $custom_post->ID;
							wp_update_post($_post);
							$_post['update'] = 1;
						}
						$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
						update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
						update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
						update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
						update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
						update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
						update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
						update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
						update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
						$ret['renja_link'][$v['kode_skpd']] = esc_url(get_permalink($custom_post));
					}

					$nama_page = 'RKPD ' . $_POST['tahun_anggaran'];
					$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');

					$_post = array(
						'post_title'	=> $nama_page,
						'post_content'	=> '[tampilrkpd tahun_anggaran="' . $_POST['tahun_anggaran'] . '"]',
						'post_type'		=> 'page',
						'post_status'	=> 'private',
						'comment_status'	=> 'closed'
					);
					if (empty($custom_post) || empty($custom_post->ID)) {
						$id = wp_insert_post($_post);
						$_post['insert'] = 1;
						$_post['ID'] = $id;
					} else {
						$_post['ID'] = $custom_post->ID;
						wp_update_post($_post);
						$_post['update'] = 1;
					}
					$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
					update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
					update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
					update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
					update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
					update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
					update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
					update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
					update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
					$ret['renja_link'][0] = esc_url(get_permalink($custom_post));

					if (
						get_option('_crb_singkron_simda') == 1
						&& get_option('_crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
					) {
						$debug = false;
						if (get_option('_crb_singkron_simda_debug') == 1) {
							$debug = true;
						}
						$this->simda->singkronSimdaUnit(array('return' => $debug, 'res' => $ret));
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Unit Salah!';
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

	public function get_mandatory_spending_link()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'message'	=> 'Berhasil get mandatory spending link!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				$nama_page = 'Mandatory Spending | ' . $_POST['tahun_anggaran'];
				$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');

				$_post = array(
					'post_title'	=> $nama_page,
					'post_content'	=> '[apbdpenjabaran tahun_anggaran="' . $_POST['tahun_anggaran'] . '" lampiran=99]',
					'post_type'		=> 'page',
					'post_status'	=> 'private',
					'comment_status'	=> 'closed'
				);
				if (empty($custom_post) || empty($custom_post->ID)) {
					$id = wp_insert_post($_post);
					$_post['insert'] = 1;
					$_post['ID'] = $id;
				} else {
					$_post['ID'] = $custom_post->ID;
					wp_update_post($_post);
					$_post['update'] = 1;
				}
				$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
				update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
				update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
				update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
				update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
				update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
				update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
				update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
				update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');

				$ret['link'] = esc_url(get_permalink($custom_post));
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

	public function singkron_data_giat()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil set program kegiatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['subgiat'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$sub_giat = json_decode(stripslashes(html_entity_decode($_POST['subgiat'])), true);
					} else {
						$sub_giat = $_POST['subgiat'];
					}
					if (!empty($_POST['page']) && $_POST['page'] == 1) {
						$wpdb->update('data_prog_keg', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
						if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
							$wpdb->update('data_master_indikator_subgiat', array('active' => 0), array(
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						}
					} else if (empty($_POST['page'])) {
						$wpdb->update('data_prog_keg', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					}
					foreach ($sub_giat as $k => $v) {
						// nama program kegiatan disertakan dengan kodenya agar sesuai dengan format sebelumnya
						if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
							$v['nama_urusan'] = $v['kode_urusan'] . ' ' . $v['nama_urusan'];
							$v['nama_bidang_urusan'] = $v['kode_bidang_urusan'] . ' ' . $v['nama_bidang_urusan'];
							$v['nama_program'] = $v['kode_program'] . ' ' . $v['nama_program'];
							$v['nama_giat'] = $v['kode_giat'] . ' ' . $v['nama_giat'];
							$v['nama_sub_giat'] = $v['kode_sub_giat'] . ' ' . $v['nama_sub_giat'];
						}
						$cek_id = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id 
							from data_prog_keg 
							where tahun_anggaran=%d 
								AND id_sub_giat=%d
						", $_POST['tahun_anggaran'], $v['id_sub_giat']));
						$opsi = array(
							'id_bidang_urusan' => $v['id_bidang_urusan'],
							'id_program' => $v['id_program'],
							'id_giat' => $v['id_giat'],
							'id_sub_giat' => $v['id_sub_giat'],
							'id_urusan' => $v['id_urusan'],
							'is_locked' => $v['is_locked'],
							'kode_bidang_urusan' => $v['kode_bidang_urusan'],
							'kode_giat' => $v['kode_giat'],
							'kode_program' => $v['kode_program'],
							'kode_sub_giat' => $v['kode_sub_giat'],
							'kode_urusan' => $v['kode_urusan'],
							'nama_bidang_urusan' => $v['nama_bidang_urusan'],
							'nama_giat' => $v['nama_giat'],
							'nama_program' => $v['nama_program'],
							'nama_sub_giat' => $v['nama_sub_giat'],
							'nama_urusan' => $v['nama_urusan'],
							'status' => $v['status'],
							'aceh' => $v['aceh'],
							'bali' => $v['bali'],
							'papua' => $v['papua'],
							'papua_barat' => $v['papua_barat'],
							'yogyakarta' => $v['yogyakarta'],
							'jakarta' => $v['jakarta'],
							'id_daerah' => $v['id_daerah'],
							'id_daerah_khusus' => $v['id_daerah_khusus'],
							'is_setda' => $v['is_setda'],
							'is_setwan' => $v['is_setwan'],
							'kunci_tahun' => $v['kunci_tahun'],
							'mulai_tahun' => $v['mulai_tahun'],
							'set_kab_kota' => $v['set_kab_kota'],
							'set_prov' => $v['set_prov'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (!empty($cek_id)) {
							$wpdb->update('data_prog_keg', $opsi, array(
								'id' => $cek_id
							));
						} else {
							$wpdb->insert('data_prog_keg', $opsi);
						}
						// master indikator sub_giat
						if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
							$cek_indikator = $wpdb->get_var($wpdb->prepare("
								SELECT
									id 
								FROM data_master_indikator_subgiat
								WHERE
									indikator=%s
									AND id_sub_keg=%d
									AND tahun_anggaran=%d
							", trim($v['indikator']), $v['id_sub_giat'], $_POST['tahun_anggaran']));
							$opsi = array(
								'id_sub_keg' => $v['id_sub_giat'],
								'indikator' => trim($v['indikator']),
								'satuan' => trim($v['satuan']),
								'active' => 1,
								'updated_at' => current_time('mysql'),
								'tahun_anggaran' => $_POST['tahun_anggaran']
							);
							if (!empty($cek_indikator)) {
								$wpdb->update('data_master_indikator_subgiat', $opsi, array(
									'id' => $cek_indikator
								));
							} else {
								$wpdb->insert('data_master_indikator_subgiat', $opsi);
							}
						}
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	public function cek_kab_kot()
	{
		$prov = get_option('_crb_id_lokasi_prov');
		$kabkot = get_option('_crb_id_lokasi_kokab');
		// 0 kosong, 1 kabupaten / kota, 2 provinsi
		$ret = array(
			'prov' => $prov,
			'kabkot' => $kabkot,
			'status' => 0
		);
		if (!empty($kabkot)) {
			$ret['status'] = 1;
		} else if (!empty($prov)) {
			$ret['status'] = 2;
		}
		return $ret;
	}

	public function singkron_data_rpjmd()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron RPJMD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['rpjmd'])) {
					//$data_rpjmd = $_POST['rpjmd'];
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$data_rpjmd = json_decode(stripslashes(html_entity_decode($_POST['rpjmd'])), true);
					} else {
						$data_rpjmd = $_POST['rpjmd'];
					}
					foreach ($data_rpjmd as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_rpjmd from data_rpjmd where tahun_anggaran=" . $_POST['tahun_anggaran'] . " AND id_rpjmd=" . $v['id_rpjmd']);
						$opsi = array(
							'id_bidang_urusan' => $v['id_bidang_urusan'],
							'id_program' => $v['id_program'],
							'id_rpjmd' => $v['id_rpjmd'],
							'indikator' => $v['indikator'],
							'kebijakan_teks' => $v['kebijakan_teks'],
							'kode_bidang_urusan' => $v['kode_bidang_urusan'],
							'kode_program' => $v['kode_program'],
							'kode_skpd' => $v['kode_skpd'],
							'misi_teks' => $v['misi_teks'],
							'nama_bidang_urusan' => $v['nama_bidang_urusan'],
							'nama_program' => $v['nama_program'],
							'nama_skpd' => $v['nama_skpd'],
							'pagu_1' => $v['pagu_1'],
							'pagu_2' => $v['pagu_2'],
							'pagu_3' => $v['pagu_3'],
							'pagu_4' => $v['pagu_4'],
							'pagu_5' => $v['pagu_5'],
							'sasaran_teks' => $v['sasaran_teks'],
							'satuan' => $v['satuan'],
							'strategi_teks' => $v['strategi_teks'],
							'target_1' => $v['target_1'],
							'target_2' => $v['target_2'],
							'target_3' => $v['target_3'],
							'target_4' => $v['target_4'],
							'target_5' => $v['target_5'],
							'tujuan_teks' => $v['tujuan_teks'],
							'visi_teks' => $v['visi_teks'],
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (!empty($cek)) {
							$wpdb->update('data_rpjmd', $opsi, array(
								'id_rpjmd' => $v['id_rpjmd'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_rpjmd', $opsi);
						}
					}
				}

				if (!empty($_POST['visi'])) {
					// $visi = $_POST['visi'];
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$visi = json_decode(stripslashes(html_entity_decode($_POST['visi'])), true);
					} else {
						$visi = $_POST['visi'];
					}
					$wpdb->update('data_rpjmd_visi', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($visi as $k => $v) {
						if (empty($v['id_visi'])) {
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_visi from data_rpjmd_visi where tahun_anggaran=" . $_POST['tahun_anggaran'] . " AND id_visi='" . $v['id_visi'] . "'");
						$opsi = array(
							'id_visi' => $v['id_visi'],
							'is_locked' => $v['is_locked'],
							'status' => $v['status'],
							'visi_teks' => $v['visi_teks'],
							'id_unik' => $v['id_unik'],
							'id_tahap' => $v['id_tahap'],
							'tahun_awal' => $v['tahun_awal'],
							'tahun_akhir' => $v['tahun_akhir'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (!empty($cek)) {
							$wpdb->update('data_rpjmd_visi', $opsi, array(
								'id_visi' => $v['id_visi'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_rpjmd_visi', $opsi);
						}
					}
				}

				if (!empty($_POST['misi'])) {
					// $misi = $_POST['misi'];
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$misi = json_decode(stripslashes(html_entity_decode($_POST['misi'])), true);
					} else {
						$misi = $_POST['misi'];
					}
					$wpdb->update('data_rpjmd_misi', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($misi as $k => $v) {
						if (empty($v['id_misi'])) {
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_misi from data_rpjmd_misi where tahun_anggaran=" . $_POST['tahun_anggaran'] . " AND id_misi='" . $v['id_misi'] . "'");
						$opsi = array(
							'id_misi' => $v['id_misi'],
							'id_misi_old' => $v['id_misi_old'],
							'id_visi' => $v['id_visi'],
							'is_locked' => $v['is_locked'],
							'misi_teks' => $v['misi_teks'],
							'status' => $v['status'],
							'urut_misi' => $v['urut_misi'],
							'visi_lock' => $v['visi_lock'],
							'visi_teks' => $v['visi_teks'],
							'id_unik' => $v['id_unik'],
							'id_tahap' => $v['id_tahap'],
							'tahun_awal' => $v['tahun_awal'],
							'tahun_akhir' => $v['tahun_akhir'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (!empty($cek)) {
							$wpdb->update('data_rpjmd_misi', $opsi, array(
								'id_misi' => $v['id_misi'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_rpjmd_misi', $opsi);
						}
					}
				}

				if (!empty($_POST['tujuan'])) {
					// $tujuan = $_POST['tujuan'];
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$tujuan = json_decode(stripslashes(html_entity_decode($_POST['tujuan'])), true);
					} else {
						$tujuan = $_POST['tujuan'];
					}
					$wpdb->update('data_rpjmd_tujuan', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($tujuan as $k => $v) {
						if (empty($v['id_tujuan'])) {
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_tujuan from data_rpjmd_tujuan where tahun_anggaran=" . $_POST['tahun_anggaran'] . " AND id_unik='" . $v['id_unik'] . "' AND id_unik_indikator='" . $v['id_unik_indikator'] . "'");
						$opsi = array(
							'id_misi' => $v['id_misi'],
							'id_misi_old' => $v['id_misi_old'],
							'id_tujuan' => $v['id_tujuan'],
							'id_unik' => $v['id_unik'],
							'id_unik_indikator' => $v['id_unik_indikator'],
							'id_visi' => $v['id_visi'],
							'indikator_teks' => $v['indikator_teks'],
							'is_locked' => $v['is_locked'],
							'is_locked_indikator' => $v['is_locked_indikator'],
							'misi_lock' => $v['misi_lock'],
							'misi_teks' => $v['misi_teks'],
							'satuan' => $v['satuan'],
							'status' => $v['status'],
							'target_1' => $v['target_1'],
							'target_2' => $v['target_2'],
							'target_3' => $v['target_3'],
							'target_4' => $v['target_4'],
							'target_5' => $v['target_5'],
							'target_akhir' => $v['target_akhir'],
							'target_awal' => $v['target_awal'],
							'tujuan_teks' => $v['tujuan_teks'],
							'urut_misi' => $v['urut_misi'],
							'urut_tujuan' => $v['urut_tujuan'],
							'visi_teks' => $v['visi_teks'],
							'id_tahap' => $v['id_tahap'],
							'tahun_awal' => $v['tahun_awal'],
							'tahun_akhir' => $v['tahun_akhir'],
							'id_tujuan_old' => $v['id_tujuan_old'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (!empty($cek)) {
							$wpdb->update('data_rpjmd_tujuan', $opsi, array(
								'id_unik' => $v['id_unik'],
								'id_unik_indikator' => $v['id_unik_indikator'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_rpjmd_tujuan', $opsi);
						}
					}
				}
				if (!empty($_POST['sasaran'])) {
					// $sasaran = $_POST['sasaran'];
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$sasaran = json_decode(stripslashes(html_entity_decode($_POST['sasaran'])), true);
					} else {
						$sasaran = $_POST['sasaran'];
					}
					$wpdb->update('data_rpjmd_sasaran', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($sasaran as $k => $v) {
						if (empty($v['id_sasaran'])) {
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_sasaran from data_rpjmd_sasaran where tahun_anggaran=" . $_POST['tahun_anggaran'] . " AND id_unik='" . $v['id_unik'] . "' AND id_unik_indikator='" . $v['id_unik_indikator'] . "'");
						$opsi = array(
							'id_misi' => $v['id_misi'],
							'id_misi_old' => $v['id_misi_old'],
							'id_sasaran' => $v['id_sasaran'],
							'id_unik' => $v['id_unik'],
							'id_unik_indikator' => $v['id_unik_indikator'],
							'id_visi' => $v['id_visi'],
							'indikator_teks' => $v['indikator_teks'],
							'is_locked' => $v['is_locked'],
							'is_locked_indikator' => $v['is_locked_indikator'],
							'kode_tujuan' => $v['kode_tujuan'],
							'misi_teks' => $v['misi_teks'],
							'sasaran_teks' => $v['sasaran_teks'],
							'satuan' => $v['satuan'],
							'status' => $v['status'],
							'target_1' => $v['target_1'],
							'target_2' => $v['target_2'],
							'target_3' => $v['target_3'],
							'target_4' => $v['target_4'],
							'target_5' => $v['target_5'],
							'target_akhir' => $v['target_akhir'],
							'target_awal' => $v['target_awal'],
							'tujuan_lock' => $v['tujuan_lock'],
							'tujuan_teks' => $v['tujuan_teks'],
							'urut_misi' => $v['urut_misi'],
							'urut_sasaran' => $v['urut_sasaran'],
							'urut_tujuan' => $v['urut_tujuan'],
							'visi_teks' => $v['visi_teks'],
							'id_tahap' => $v['id_tahap'],
							'tahun_awal' => $v['tahun_awal'],
							'tahun_akhir' => $v['tahun_akhir'],
							'id_tujuan_old' => $v['id_tujuan_old'],
							'id_sasaran_old' => $v['id_sasaran_old'],
							'id_tujuan_indikator' => $v['id_tujuan_indikator'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (!empty($cek)) {
							$wpdb->update('data_rpjmd_sasaran', $opsi, array(
								'id_unik' => $v['id_unik'],
								'id_unik_indikator' => $v['id_unik_indikator'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_rpjmd_sasaran', $opsi);
						}
					}
				}
				if (!empty($_POST['program'])) {
					// $program = $_POST['program'];
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$program = json_decode(stripslashes(html_entity_decode($_POST['program'])), true);
					} else {
						$program = $_POST['program'];
					}
					$wpdb->update('data_rpjmd_program', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($program as $k => $v) {
						if (empty($v['id_program'])) {
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_program from data_rpjmd_program where tahun_anggaran=" . $_POST['tahun_anggaran'] . " AND id_unik='" . $v['id_unik'] . "' AND id_unik_indikator='" . $v['id_unik_indikator'] . "'");
						$opsi = array(
							'id_misi' => $v['id_misi'],
							'id_misi_old' => $v['id_misi_old'],
							'id_program' => $v['id_program'],
							'id_unik' => $v['id_unik'],
							'id_unik_indikator' => $v['id_unik_indikator'],
							'id_unit' => $v['id_unit'],
							'id_visi' => $v['id_visi'],
							'indikator' => $v['indikator'],
							'is_locked' => $v['is_locked'],
							'is_locked_indikator' => $v['is_locked_indikator'],
							'kode_sasaran' => $v['kode_sasaran'],
							'kode_skpd' => $v['kode_skpd'],
							'kode_tujuan' => $v['kode_tujuan'],
							'misi_teks' => $v['misi_teks'],
							'nama_program' => $v['nama_program'],
							'nama_skpd' => $v['nama_skpd'],
							'pagu_1' => $v['pagu_1'],
							'pagu_2' => $v['pagu_2'],
							'pagu_3' => $v['pagu_3'],
							'pagu_4' => $v['pagu_4'],
							'pagu_5' => $v['pagu_5'],
							'program_lock' => $v['program_lock'],
							'sasaran_lock' => $v['sasaran_lock'],
							'sasaran_teks' => $v['sasaran_teks'],
							'satuan' => $v['satuan'],
							'status' => $v['status'],
							'target_1' => $v['target_1'],
							'target_2' => $v['target_2'],
							'target_3' => $v['target_3'],
							'target_4' => $v['target_4'],
							'target_5' => $v['target_5'],
							'target_akhir' => $v['target_akhir'],
							'target_awal' => $v['target_awal'],
							'tujuan_lock' => $v['tujuan_lock'],
							'tujuan_teks' => $v['tujuan_teks'],
							'urut_misi' => $v['urut_misi'],
							'urut_sasaran' => $v['urut_sasaran'],
							'urut_tujuan' => $v['urut_tujuan'],
							'visi_teks' => $v['visi_teks'],
							'id_rpjmd' => $v['id_rpjmd'],
							'pagu_awal' => $v['pagu_awal'],
							'pagu_akhir' => $v['pagu_akhir'],
							'id_sasaran_indikator' => $v['id_sasaran_indikator'],
							'id_tahap' => $v['id_tahap'],
							'tahun_awal' => $v['tahun_awal'],
							'tahun_akhir' => $v['tahun_akhir'],
							'id_tujuan_old' => $v['id_tujuan_old'],
							'id_sasaran_old' => $v['id_sasaran_old'],
							'id_bidang_urusan' => $v['id_bidang_urusan'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (!empty($cek)) {
							$wpdb->update('data_rpjmd_program', $opsi, array(
								'id_unik' => $v['id_unik'],
								'id_unik_indikator' => $v['id_unik_indikator'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_rpjmd_program', $opsi);
						}
					}
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

	public function singkron_data_rpd()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron RPD!'
		);
		// foreach (json_decode($_POST['tujuanfy']) as $key => $value) {
		// 	// $ret['message']=$value;
		// 	die(json_encode([
		// 		'status'	=> 'success',
		// 		'message' => 'aa'
		// 	]));
		// }
		// die(json_encode([
		// 	'status'	=> 'success',
		// 	'message' => 'bb'
		// ]));
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				if (!empty($_POST['tujuan'])) {
					$tujuan = $_POST['tujuan'];
					$wpdb->update('data_rpd_tujuan', array('active' => 0), array('active' => 1));
					foreach ($tujuan as $k => $v) {
						if (empty($v['id_tujuan'])) {
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_tujuan from data_rpd_tujuan where id_unik='" . $v['id_unik'] . "' AND id_unik_indikator='" . $v['id_unik_indikator'] . "'");
						$opsi = array(
							'head_teks' => $v['head_teks'],
							'id_misi_old' => $v['id_misi_old'],
							'id_tujuan' => $v['id_tujuan'],
							'id_unik' => $v['id_unik'],
							'id_unik_indikator' => $v['id_unik_indikator'],
							'indikator_teks' => $v['indikator_teks'],
							'is_locked' => $v['is_locked'],
							'is_locked_indikator' => $v['is_locked_indikator'],
							'isu_teks' => $v['isu_teks'],
							'kebijakan_teks' => $v['kebijakan_teks'],
							'misi_lock' => $v['misi_lock'],
							'misi_teks' => $v['misi_teks'],
							'saspok_teks' => $v['saspok_teks'],
							'satuan' => $v['satuan'],
							'status' => $v['status'],
							'target_1' => $v['target_1'],
							'target_2' => $v['target_2'],
							'target_3' => $v['target_3'],
							'target_4' => $v['target_4'],
							'target_5' => $v['target_5'],
							'target_akhir' => $v['target_akhir'],
							'target_awal' => $v['target_awal'],
							'tujuan_teks' => $v['tujuan_teks'],
							'urut_misi' => $v['urut_misi'],
							'urut_saspok' => $v['urut_saspok'],
							'urut_tujuan' => $v['urut_tujuan'],
							'visi_teks' => $v['visi_teks'],
							'active' => 1,
							'update_at' => current_time('mysql')
						);

						if (!empty($cek)) {
							$wpdb->update('data_rpd_tujuan', $opsi, array(
								'id_unik' => $v['id_unik'],
								'id_unik_indikator' => $v['id_unik_indikator']
							));
						} else {
							$wpdb->insert('data_rpd_tujuan', $opsi);
						}
					}
				}
				if (!empty($_POST['sasaran'])) {
					$sasaran = $_POST['sasaran'];
					$wpdb->update('data_rpd_sasaran', array('active' => 0), array('active' => 1));
					foreach ($sasaran as $k => $v) {
						if (empty($v['id_sasaran'])) {
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_sasaran from data_rpd_sasaran where id_unik='" . $v['id_unik'] . "' AND id_unik_indikator='" . $v['id_unik_indikator'] . "'");
						$opsi = array(
							'head_teks' => $v['head_teks'],
							'id_misi_old' => $v['id_misi_old'],
							'id_sasaran' => $v['id_sasaran'],
							'id_unik' => $v['id_unik'],
							'id_unik_indikator' => $v['id_unik_indikator'],
							'indikator_teks' => $v['indikator_teks'],
							'is_locked' => $v['is_locked'],
							'is_locked_indikator' => $v['is_locked_indikator'],
							'isu_teks' => $v['isu_teks'],
							'kebijakan_teks' => $v['kebijakan_teks'],
							'kode_tujuan' => $v['kode_tujuan'],
							'misi_lock' => $v['misi_lock'],
							'misi_teks' => $v['misi_teks'],
							'sasaran_teks' => $v['sasaran_teks'],
							'saspok_teks' => $v['saspok_teks'],
							'satuan' => $v['satuan'],
							'status' => $v['status'],
							'target_1' => $v['target_1'],
							'target_2' => $v['target_2'],
							'target_3' => $v['target_3'],
							'target_4' => $v['target_4'],
							'target_5' => $v['target_5'],
							'target_akhir' => $v['target_akhir'],
							'target_awal' => $v['target_awal'],
							'tujuan_lock' => $v['tujuan_lock'],
							'tujuan_teks' => $v['tujuan_teks'],
							'urut_misi' => $v['urut_misi'],
							'urut_sasaran' => $v['urut_sasaran'],
							'urut_saspok' => $v['urut_saspok'],
							'urut_tujuan' => $v['urut_tujuan'],
							'visi_teks' => $v['visi_teks'],
							'active' => 1,
							'update_at' => current_time('mysql')
						);

						if (!empty($cek)) {
							$wpdb->update('data_rpd_sasaran', $opsi, array(
								'id_unik' => $v['id_unik'],
								'id_unik_indikator' => $v['id_unik_indikator']
							));
						} else {
							$wpdb->insert('data_rpd_sasaran', $opsi);
						}
					}
				}
				if (!empty($_POST['program'])) {
					$program = $_POST['program'];
					$wpdb->update('data_rpd_program', array('active' => 0), array('active' => 1));
					foreach ($program as $k => $v) {
						if (empty($v['id_program'])) {
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_program from data_rpd_program where id_unik='" . $v['id_unik'] . "' AND id_unik_indikator='" . $v['id_unik_indikator'] . "'");
						$opsi = array(
							'head_teks' => $v['head_teks'],
							'id_bidur_mth' => $v['id_bidur_mth'],
							'id_misi_old' => $v['id_misi_old'],
							'id_program' => $v['id_program'],
							'id_program_mth' => $v['id_program_mth'],
							'id_unik' => $v['id_unik'],
							'id_unik_indikator' => $v['id_unik_indikator'],
							'id_unit' => $v['id_unit'],
							'indikator' => $v['indikator'],
							'is_locked' => $v['is_locked'],
							'is_locked_indikator' => $v['is_locked_indikator'],
							'isu_teks' => $v['isu_teks'],
							'kebijakan_teks' => $v['kebijakan_teks'],
							'kode_sasaran' => $v['kode_sasaran'],
							'kode_skpd' => $v['kode_skpd'],
							'kode_tujuan' => $v['kode_tujuan'],
							'misi_lock' => $v['misi_lock'],
							'misi_teks' => $v['misi_teks'],
							'nama_program' => $v['nama_program'],
							'nama_skpd' => $v['nama_skpd'],
							'pagu_1' => $v['pagu_1'],
							'pagu_2' => $v['pagu_2'],
							'pagu_3' => $v['pagu_3'],
							'pagu_4' => $v['pagu_4'],
							'pagu_5' => $v['pagu_5'],
							'program_lock' => $v['program_lock'],
							'sasaran_lock' => $v['sasaran_lock'],
							'sasaran_teks' => $v['sasaran_teks'],
							'saspok_teks' => $v['saspok_teks'],
							'satuan' => $v['satuan'],
							'status' => $v['status'],
							'target_1' => $v['target_1'],
							'target_2' => $v['target_2'],
							'target_3' => $v['target_3'],
							'target_4' => $v['target_4'],
							'target_5' => $v['target_5'],
							'target_akhir' => $v['target_akhir'],
							'target_awal' => $v['target_awal'],
							'tujuan_lock' => $v['tujuan_lock'],
							'tujuan_teks' => $v['tujuan_teks'],
							'urut_misi' => $v['urut_misi'],
							'urut_sasaran' => $v['urut_sasaran'],
							'urut_saspok' => $v['urut_saspok'],
							'urut_tujuan' => $v['urut_tujuan'],
							'visi_teks' => $v['visi_teks'],
							'active' => 1,
							'update_at' => current_time('mysql')
						);

						if (!empty($cek)) {
							$wpdb->update('data_rpd_program', $opsi, array(
								'id_unik' => $v['id_unik'],
								'id_unik_indikator' => $v['id_unik_indikator']
							));
						} else {
							$wpdb->insert('data_rpd_program', $opsi);
						}
					}
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

	public function singkron_sumber_dana()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron sumber dana!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['dana'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$sumber_dana = json_decode(stripslashes(html_entity_decode($_POST['dana'])), true);
					} else {
						$sumber_dana = $_POST['dana'];
					}
					if (!empty($_POST['page']) && $_POST['page'] == 1) {
						$wpdb->update('data_sumber_dana', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else if (empty($_POST['page'])) {
						$wpdb->update('data_sumber_dana', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					}
					foreach ($sumber_dana as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id_dana 
							from data_sumber_dana 
							where tahun_anggaran=%d
								AND id_dana=%d
						", $_POST['tahun_anggaran'], $v['id_dana']));
						$opsi = array(
							'created_at' => $v['created_at'],
							'created_user' => $v['created_user'],
							'id_daerah' => $v['id_daerah'],
							'id_dana' => $v['id_dana'],
							'id_unik' => $v['id_unik'],
							'is_locked' => $v['is_locked'],
							'kode_dana' => $v['kode_dana'],
							'nama_dana' => $v['nama_dana'],
							'sumber_dana' => $v['sumber_dana'],
							'set_input' => $v['set_input'],
							'status' => $v['status'],
							'tahun' => $v['tahun'],
							'updated_user' => $v['updated_user'],
							'active' => 1,
							'updated_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (!empty($cek)) {
							$wpdb->update('data_sumber_dana', $opsi, array(
								'id_dana' => $v['id_dana'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_sumber_dana', $opsi);
						}
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	public function get_sumber_dana()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get data sumber dana!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber_dana'])) {
					$sd_fmis = array();
					$sumber_dana = json_decode(stripslashes(html_entity_decode($_POST['sumber_dana'])));
					foreach ($sumber_dana as $v) {
						$v = (array) $v;
						$sd_fmis[$v['uraian']] = $v;
					}
					$cek_sipd_belum_ada_di_fmis = array();

					$dana = $wpdb->get_results($wpdb->prepare(
						"
						SELECT 
							*
						from data_sumber_dana
						where tahun_anggaran=%d",
						$_POST['tahun_anggaran']
					), ARRAY_A);
					foreach ($dana as $v) {
						$nama = explode('] - ', $v['nama_dana']);
						$nama = trim(str_replace(' - ', '-', $nama[1]));
						if (empty($sd_fmis[$nama])) {
							$cek_sipd_belum_ada_di_fmis[$nama] = $v;
						}
					}

					$dana_pendapatan = $wpdb->get_results($wpdb->prepare(
						"
						SELECT 
							nama_akun as nama_dana,
							kode_akun as kode_dana,
							'' as iddana,
							'pendapatan' as jenis
						from data_pendapatan
						where tahun_anggaran=%d
						group by nama_akun",
						$_POST['tahun_anggaran']
					), ARRAY_A);
					foreach ($dana_pendapatan as $v) {
						$nama = $v['nama_dana'];
						$nama = trim(str_replace(' - ', '-', $nama));
						if (empty($sd_fmis[$nama])) {
							$cek_sipd_belum_ada_di_fmis[$nama] = $v;
						}
					}

					$dana_pembiayaan = $wpdb->get_results($wpdb->prepare(
						"
						SELECT 
							nama_akun as nama_dana,
							kode_akun as kode_dana,
							'' as iddana,
							type as jenis
						from data_pembiayaan
						where tahun_anggaran=%d
						group by nama_akun",
						$_POST['tahun_anggaran']
					), ARRAY_A);
					foreach ($dana_pembiayaan as $v) {
						$nama = $v['nama_dana'];
						$nama = trim(str_replace(' - ', '-', $nama));
						if (empty($sd_fmis[$nama])) {
							$cek_sipd_belum_ada_di_fmis[$nama] = $v;
						}
					}

					$current_mapping = get_option('_crb_custom_mapping_sumberdana_fmis');
					$current_mapping = explode(',', $current_mapping);
					$mapping_rek = array();
					foreach ($current_mapping as $v) {
						$rek = explode('-', $v);
						$mapping_rek[$rek[0]] = '';
						if (!empty($rek[1])) {
							$mapping_rek[$rek[0]] = $rek[1];
						}
					}

					$mapping = array();
					foreach ($cek_sipd_belum_ada_di_fmis as $k => $v) {
						$k = '[' . $k . ']';
						if (!empty($mapping_rek[$k])) {
							$mapping[] = $k . '-' . $mapping_rek[$k];
						} else {
							$mapping[] = $k . '-' . $k;
						}
					}
					update_option('_crb_custom_mapping_sumberdana_fmis', implode(',', $mapping));
					$ret['data'] = $cek_sipd_belum_ada_di_fmis;
					$ret['total'] = count($dana) + count($dana_pendapatan) + count($dana_pembiayaan);
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format salah!';
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

	public function singkron_alamat()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron alamat!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['alamat'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$alamat = json_decode(stripslashes(html_entity_decode($_POST['alamat'])), true);
					} else {
						$alamat = $_POST['alamat'];
					}
					foreach ($alamat as $k => $v) {
						$where = '';
						$where_a = array(
							'tahun' => $_POST['tahun_anggaran'],
							'id_alamat' => $v['id_alamat']
						);
						if (!empty($v['is_prov'])) {
							$where = " AND is_prov=" . $v['is_prov'];
							$where_a['is_prov'] = $v['is_prov'];
						} else if (!empty($v['is_kab'])) {
							$where = " AND is_kab=" . $v['is_kab'];
							$where_a['is_kab'] = $v['is_kab'];
						} else if (!empty($v['is_kec'])) {
							$where = " AND is_kec=" . $v['is_kec'];
							$where_a['is_kec'] = $v['is_kec'];
						} else if (!empty($v['is_kel'])) {
							$where = " AND is_kel=" . $v['is_kel'];
							$where_a['is_kel'] = $v['is_kel'];
						}
						$cek = $wpdb->get_var(
							"
							SELECT 
								id_alamat 
							from data_alamat 
							where tahun=" . $_POST['tahun_anggaran']
								. " AND id_alamat=" . $v['id_alamat']
								. $where
						);
						$opsi = array(
							'id_alamat' => $v['id_alamat'],
							'nama' => $v['nama'],
							'id_prov' => $v['id_prov'],
							'id_kab' => $v['id_kab'],
							'id_kec' => $v['id_kec'],
							'is_prov' => $v['is_prov'],
							'is_kab' => $v['is_kab'],
							'is_kec' => $v['is_kec'],
							'is_kel' => $v['is_kel'],
							'tahun' => $_POST['tahun_anggaran'],
							'updated_at' => current_time('mysql')
						);

						if (!empty($cek)) {
							$wpdb->update('data_alamat', $opsi, $where_a);
						} else {
							$wpdb->insert('data_alamat', $opsi);
						}
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	public function replace_char($str)
	{
		$str = is_null($str) ? '' : trim($str);
		$str = html_entity_decode($str, ENT_QUOTES | ENT_XML1, 'UTF-8');
		$str = str_replace(
			array('"', "'", '\\', '&#039'),
			array('petik_dua', 'petik_satu', '', 'petik_satu'),
			$str
		);
		return $str;
	}


	public function get_alamat($input, $rincian, $no = 0)
	{
		global $wpdb;
		$profile = false;
		if (!empty($rincian['id_penerima'])) {
			$profile = $wpdb->get_row("SELECT * from data_profile_penerima_bantuan where id_profil=" . $rincian['id_penerima'] . " and tahun=" . $_POST['tahun_anggaran'], ARRAY_A);
		}
		$alamat = '';
		$keterangan_alamat = '';
		$lokus_akun_teks = $this->replace_char($rincian['lokus_akun_teks']);
		if (!empty($profile)) {
			$alamat = $profile['alamat_teks'] . ' (' . $profile['jenis_penerima'] . ')';
		} else if (!empty($lokus_akun_teks)) {
			if (
				strtoupper($rincian['jenis_bl']) == 'BAGI-HASIL'
				|| strtoupper($rincian['jenis_bl']) == 'BANKEU'
				|| strtoupper($rincian['jenis_bl']) == 'BANKEU-KHUSUS'
			) {
				$alamat = $rincian['lokus_akun_teks'];
			} else {
				$profile = $wpdb->get_row($wpdb->prepare(
					"
		            SELECT 
		                alamat_teks, 
		                jenis_penerima 
		            from data_profile_penerima_bantuan 
		            where BINARY nama_teks=%s 
		                and tahun=%d",
					$lokus_akun_teks,
					$_POST['tahun_anggaran']
				), ARRAY_A);
				if (!empty($profile)) {
					$alamat = $profile['alamat_teks'] . ' (' . $profile['jenis_penerima'] . ')';
				} else {
					if (
						strpos($lokus_akun_teks, 'petik_satu') !== false
						&& $no <= 1
					) {
						$no++;
						$rincian['lokus_akun_teks'] = str_replace('petik_satu', 'petik_satupetik_satu', $lokus_akun_teks);
						return $this->get_alamat($input, $rincian, $no);
					} else {
						$keterangan_alamat .= "<script>console.log('" . $rincian['lokus_akun_teks'] . "', \"" . preg_replace('!\s+!', ' ', str_replace(array("\n", "\r"), " ", htmlentities($wpdb->last_query))) . "\");</script>";
					}
				}
			}
		}
		return array(
			'keterangan' => $keterangan_alamat,
			'alamat' => $alamat,
			'lokus_akun_teks' => $lokus_akun_teks,
			'lokus_akun_teks_decode' => str_replace(array('petik_satu', 'petik_dua'), array("'", '"'), $lokus_akun_teks)
		);
	}

	public function singkron_penerima_bantuan()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data penerima bantuan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['profile'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$profile = json_decode(stripslashes(html_entity_decode($_POST['profile'])), true);
					} else {
						$profile = $_POST['profile'];
					}
					foreach ($profile as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_profil from data_profile_penerima_bantuan where tahun=" . $_POST['tahun_anggaran'] . " AND id_profil=" . $v['id_profil']);
						$nama_teks = $this->replace_char($v['nama_teks']);
						$opsi = array(
							'alamat_teks' => $v['alamat_teks'],
							'id_profil' => $v['id_profil'],
							'jenis_penerima' => $v['jenis_penerima'],
							'nama_teks' => $nama_teks,
							'tahun' => $_POST['tahun_anggaran'],
							'updated_at' => current_time('mysql')
						);

						if (!empty($cek)) {
							$wpdb->update('data_profile_penerima_bantuan', $opsi, array(
								'tahun' => $_POST['tahun_anggaran'],
								'id_profil' => $v['id_profil']
							));
						} else {
							$wpdb->insert('data_profile_penerima_bantuan', $opsi);
						}
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	public function singkron_user_penatausahaan()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron user penatausahaan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data_user'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$_POST['data_user'] = json_decode(stripslashes(html_entity_decode($_POST['data_user'])), true);
					}

					foreach ($_POST['data_user'] as $key => $data_user) {
						// print_r($data_user);exit;
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id 
							from data_user_penatausahaan 
							where tahun_anggaran=%d 
								AND userName=%s 
								AND idUser=%s
						", $_POST['tahun_anggaran'], $data_user['userName'], $data_user['idUser']));
						$opsi = array(
							"idSkpd" => $data_user['skpd']['idSkpd'],
							"namaSkpd" => $data_user['skpd']['namaSkpd'],
							"kodeSkpd" => $data_user['skpd']['kodeSkpd'],
							"idDaerah" => $data_user['skpd']['idDaerah'],
							"userName" => $data_user['userName'],
							"nip" => $data_user['nip'],
							"nik" => $data_user['nik'],
							"fullName" => $data_user['fullName'],
							"lahir_user" => $data_user['lahir_user'],
							"nomorHp" => $data_user['nomorHp'],
							"rank" => $data_user['rank'],
							"npwp" => $data_user['npwp'],
							"idJabatan" => $data_user['jabatan']['idJabatan'],
							"namaJabatan" => $data_user['jabatan']['namaJabatan'],
							"idRole" => $data_user['jabatan']['idRole'],
							"order" => $data_user['jabatan']['order'],
							"kpa" => $data_user['kpa'],
							"bank" => $data_user['bank'],
							"group" => $data_user['group'],
							"password" => $data_user['password'],
							"konfirmasiPassword" => $data_user['konfirmasiPassword'],
							"kodeBank" => $data_user['kodeBank'],
							"nama_rekening" => $data_user['nama_rekening'],
							"nomorRekening" => $data_user['nomorRekening'],
							"pangkatGolongan" => $data_user['pangkatGolongan'],
							"tahunPegawai" => $data_user['tahunPegawai'],
							"kodeDaerah" => $data_user['kodeDaerah'],
							"is_from_sipd" => $data_user['is_from_sipd'],
							"is_from_generate" => $data_user['is_from_generate'],
							"is_from_external" => $data_user['is_from_external'],
							"idSubUnit" => $data_user['idSubUnit'],
							"idUser" => $data_user['idUser'],
							"idPegawai" => $data_user['idPegawai'],
							"alamat" => $data_user['alamat'],
							'active' => 1,
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'updated_at' => current_time('mysql')
						);

						if (!empty($cek)) {
							$wpdb->update('data_user_penatausahaan', $opsi, array(
								'id' => $cek
							));
						} else {
							$wpdb->insert('data_user_penatausahaan', $opsi);
						}
						$ret['sql'] = $wpdb->last_query;
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	public function singkron_rekanan_sipd()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron Rekanan penatausahaan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data_rekanan'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$_POST['data_rekanan'] = json_decode(stripslashes(html_entity_decode($_POST['data_rekanan'])), true);
					}

					foreach ($_POST['data_rekanan'] as $key => $data_rekanan) {
						// print_r($data_user);exit;
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id 
							from data_rekanan_sipd 
							where tahun_anggaran=%d 
								AND nama_rekening=%s 
						", $_POST['tahun_anggaran'], $data_rekanan['nama_rekening']));
						$opsi = array(
							"id_daerah" => $data_rekanan['id_daerah'],
							"id_skpd" => $data_rekanan['id_skpd'],
							"nomor_rekening" => $data_rekanan['nomor_rekening'],
							"nama_rekening" => $data_rekanan['nama_rekening'],
							"id_bank" => $data_rekanan['id_bank'],
							"nama_bank" => $data_rekanan['nama_bank'],
							"cabang_bank" => $data_rekanan['cabang_bank'],
							"nama_tujuan" => $data_rekanan['nama_tujuan'],
							"nama_perusahaan" => $data_rekanan['nama_perusahaan'],
							"alamat_perusahaan" => $data_rekanan['alamat_perusahaan'],
							"telepon_perusahaan" => $data_rekanan['telepon_perusahaan'],
							"npwp" => $data_rekanan['npwp'],
							"nik" => $data_rekanan['nik'],
							"jenis_rekanan" => $data_rekanan['jenis_rekanan'],
							"kategori_rekanan" => $data_rekanan['kategori_rekanan'],
							"is_valid" => $data_rekanan['is_valid'],
							"is_locked" => $data_rekanan['is_locked'],
							"created_at" => $data_rekanan['created_at'],
							"created_by" => $data_rekanan['created_by'],
							'active' => 1,
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'updated_at' => current_time('mysql')
						);

						if (!empty($cek)) {
							$wpdb->update('data_rekanan_sipd', $opsi, array(
								'id' => $cek
							));
						} else {
							$wpdb->insert('data_rekanan_sipd', $opsi);
						}
						$ret['sql'] = $wpdb->last_query;
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	public function singkron_panggol_penatausahaan()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron Pangkat dan Golongan penatausahaan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					}

					foreach ($_POST['data'] as $key => $data) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								nama_pangkat 
							from data_pangkat_golongan 
							where tahun_anggaran=%d 
								AND nama_pangkat=%s 
								AND id_pangkat=%s
						", $_POST['tahun_anggaran'], $data['nama_pangkat'], $data['id']));
						$opsi = array(
							"id_pangkat" => $data['id'],
							"nama_pangkat" => $data['nama_pangkat'],
							"nama_golongan" => $data['nama_golongan'],
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'singkron_at' => current_time('mysql')
						);

						if (!empty($cek)) {
							$wpdb->update('data_pangkat_golongan', $opsi, array(
								'tahun_anggaran' => $_POST['tahun_anggaran'],
								'nama_pangkat' => $data['nama_pangkat'],
								'id_pangkat' => $data['id'],
							));
						} else {
							$wpdb->insert('data_pangkat_golongan', $opsi);
						}
						$ret['sql'] = $wpdb->last_query;
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	public function singkron_pegawai_penatausahaan()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron Pegawai penatausahaan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data_user'])) {
					if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
						$_POST['data_user'] = json_decode(stripslashes(html_entity_decode($_POST['data_user'])), true);
					}

					foreach ($_POST['data_user'] as $key => $data_user) {
						// print_r($data_user);exit;
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id
							from data_user_penatausahaan 
							where tahun_anggaran=%d 								
								AND idUser=%s
						", $_POST['tahun_anggaran'], $data_user['idUser']));
						// print_r($data_user['idUser']);exit;
						$opsi = array(
							"idSkpd" => $data_user['idSkpd'],
							"idDaerah" => $data_user['idDaerah'],
							"npwp" => $data_user['npwp'],
							"idJabatan" => $data_user['idRole'],
							"namaJabatan" => $data_user['namaJabatan'],
							"idRole" => $data_user['idRole'],
							"kpa" => $data_user['kpa'],
							"bank" => $data_user['bank'],
							"group" => $data_user['group'],
							"password" => $data_user['password'],
							"konfirmasiPassword" => $data_user['konfirmasiPassword'],
							"kodeBank" => $data_user['kodeBank'],
							"nama_rekening" => $data_user['nama_rekening'],
							"nomorRekening" => $data_user['nomorRekening'],
							"pangkatGolongan" => $data_user['pangkatGolongan'],
							"tahunPegawai" => $data_user['tahunPegawai'],
							"kodeDaerah" => $data_user['kodeDaerah'],
							"is_from_sipd" => $data_user['is_from_sipd'],
							"is_from_generate" => $data_user['is_from_generate'],
							"is_from_external" => $data_user['is_from_external'],
							"idSubUnit" => $data_user['idSubUnit'],
							"idUser" => $data_user['idUser'],
							"idPegawai" => $data_user['idPegawai'],
							"alamat" => $data_user['alamat'],
							'active' => 1,
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'updated_at' => current_time('mysql')
						);

						// if (!empty($cek)) {
						if ($cek) {
							$wpdb->update('data_user_penatausahaan', $opsi, array(
								'id' => $cek
							));
						} else {
							$wpdb->insert('data_user_penatausahaan', $opsi);
						}
						$ret['sql'] = $wpdb->last_query;
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	public function set_unit_pagu()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil set unit pagu!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$data_unit = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					} else {
						$data_unit = $_POST['data'];
					}
					$cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							kode_skpd 
						from data_unit_pagu 
						where tahun_anggaran=%d 
							AND kode_skpd=%s
					", $_POST['tahun_anggaran'], $data_unit['kode_skpd']));
					if (!isset($data_unit['id_user'])) {
						$data_unit['id_user'] = 0;
					}
					if (!isset($data_unit['totalgiat'])) {
						$data_unit['totalgiat'] = 0;
					}
					if (!isset($data_unit['realisasi'])) {
						$data_unit['realisasi'] = 0;
					}
					if (!isset($data_unit['pagu_giat'])) {
						$data_unit['pagu_giat'] = 0;
					}
					if (!isset($data_unit['nilaipagumurni'])) {
						$data_unit['nilaipagumurni'] = 0;
					}
					if (!isset($data_unit['kunciblrinci'])) {
						$data_unit['kunciblrinci'] = 0;
					}
					if (!isset($data_unit['kuncibl'])) {
						$data_unit['kuncibl'] = 0;
					}
					if (!isset($data_unit['kunci_bl_rinci'])) {
						$data_unit['kunci_bl_rinci'] = 0;
					}
					if (!isset($data_unit['kunci_bl'])) {
						$data_unit['kunci_bl'] = 0;
					}
					if (!isset($data_unit['is_komponen'])) {
						$data_unit['is_komponen'] = 0;
					}
					if (!isset($data_unit['is_deleted'])) {
						$data_unit['is_deleted'] = 0;
					}
					if (!isset($data_unit['is_anggaran'])) {
						$data_unit['is_anggaran'] = 0;
					}
					$opsi = array(
						'batasanpagu' => $data_unit['batasanpagu'],
						'id_daerah' => $data_unit['id_daerah'],
						'id_level' => $data_unit['id_level'],
						'id_skpd' => $data_unit['id_skpd'],
						'id_unit' => $data_unit['id_unit'],
						'id_user' => $data_unit['id_user'],
						'is_anggaran' => $data_unit['is_anggaran'],
						'is_deleted' => $data_unit['is_deleted'],
						'is_komponen' => $data_unit['is_komponen'],
						'is_locked' => $data_unit['is_locked'],
						'is_skpd' => $data_unit['is_skpd'],
						'kode_skpd' => $data_unit['kode_skpd'],
						'kunci_bl' => $data_unit['kunci_bl'],
						'kunci_bl_rinci' => $data_unit['kunci_bl_rinci'],
						'kuncibl' => $data_unit['kuncibl'],
						'kunciblrinci' => $data_unit['kunciblrinci'],
						'nilaipagu' => $data_unit['nilaipagu'],
						'nilaipagumurni' => $data_unit['nilaipagumurni'],
						'nilairincian' => $data_unit['nilairincian'],
						'pagu_giat' => $data_unit['pagu_giat'],
						'realisasi' => $data_unit['realisasi'],
						'rinci_giat' => $data_unit['rinci_giat'],
						'set_pagu_giat' => $data_unit['set_pagu_giat'],
						'set_pagu_skpd' => $data_unit['set_pagu_skpd'],
						'tahun' => $data_unit['tahun'],
						'total_giat' => $data_unit['total_giat'],
						'totalgiat' => $data_unit['totalgiat'],
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);

					if (!empty($cek)) {
						$wpdb->update('data_unit_pagu', $opsi, array(
							'kode_skpd' => $data_unit['kode_skpd'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_unit_pagu', $opsi);
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	public function singkron_renstra()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron RENSTRA!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					//$data = $_POST['data'];
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					} else {
						$data = $_POST['data'];
					}
					$unit = array();
					foreach ($data as $k => $v) {
						$unit[$v['id_unit']] = $v['id_unit'];
					}
					foreach ($unit as $k => $v) {
						$wpdb->update('data_renstra', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'id_unit' => $k
						));
					}
					foreach ($data as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_renstra from data_renstra where tahun_anggaran=" . $_POST['tahun_anggaran'] . " AND id_renstra=" . $v['id_renstra']);
						$opsi = array(
							'id_bidang_urusan' => $v["id_bidang_urusan"],
							'id_giat' => $v["id_giat"],
							'id_program' => $v["id_program"],
							'id_renstra' => $v["id_renstra"],
							'id_rpjmd' => $v["id_rpjmd"],
							'id_sub_giat' => $v["id_sub_giat"],
							'id_unit' => $v["id_unit"],
							'indikator' => $v["indikator"],
							'indikator_sub' => $v["indikator_sub"],
							'is_locked' => $v["is_locked"],
							'kebijakan_teks' => $v["kebijakan_teks"],
							'kode_bidang_urusan' => $v["kode_bidang_urusan"],
							'kode_giat' => $v["kode_giat"],
							'kode_program' => $v["kode_program"],
							'kode_skpd' => $v["kode_skpd"],
							'kode_sub_giat' => $v["kode_sub_giat"],
							'misi_teks' => $v["misi_teks"],
							'nama_bidang_urusan' => $v["nama_bidang_urusan"],
							'nama_giat' => $v["nama_giat"],
							'nama_program' => $v["nama_program"],
							'nama_skpd' => $v["nama_skpd"],
							'nama_sub_giat' => $v["nama_sub_giat"],
							'outcome' => $v["outcome"],
							'pagu_1' => $v["pagu_1"],
							'pagu_2' => $v["pagu_2"],
							'pagu_3' => $v["pagu_3"],
							'pagu_4' => $v["pagu_4"],
							'pagu_5' => $v["pagu_5"],
							'pagu_sub_1' => $v["pagu_sub_1"],
							'pagu_sub_2' => $v["pagu_sub_2"],
							'pagu_sub_3' => $v["pagu_sub_3"],
							'pagu_sub_4' => $v["pagu_sub_4"],
							'pagu_sub_5' => $v["pagu_sub_5"],
							'sasaran_teks' => $v["sasaran_teks"],
							'satuan' => $v["satuan"],
							'satuan_sub' => $v["satuan_sub"],
							'strategi_teks' => $v["strategi_teks"],
							'target_1' => $v["target_1"],
							'target_2' => $v["target_2"],
							'target_3' => $v["target_3"],
							'target_4' => $v["target_4"],
							'target_5' => $v["target_5"],
							'target_sub_1' => $v["target_sub_1"],
							'target_sub_2' => $v["target_sub_2"],
							'target_sub_3' => $v["target_sub_3"],
							'target_sub_4' => $v["target_sub_4"],
							'target_sub_5' => $v["target_sub_5"],
							'tujuan_teks' => $v["tujuan_teks"],
							'visi_teks' => $v["visi_teks"],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (!empty($cek)) {
							$wpdb->update('data_renstra', $opsi, array(
								'id_renstra' => $v['id_renstra'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_renstra', $opsi);
						}
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	public function update_nonactive_sub_bl()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil update data_sub_keg_bl nonactive!',
			'action'	=> $_POST['action'],
			'id_unit'	=> $_POST['id_unit']
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$all_id_sub_skpd = array();
				if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
					$subkeg_aktif = json_decode(stripslashes(html_entity_decode($_POST['subkeg_aktif'])), true);
				} else {
					$subkeg_aktif = $_POST['subkeg_aktif'];
				}
				foreach ($subkeg_aktif as $v) {
					$id = explode('.', $v['kode_sbl']);
					$all_id_sub_skpd[$id[1]] = $wpdb->prepare('%d', $id[1]);
				}
				if (!empty($all_id_sub_skpd)) {
					$all_id_sub_skpd = implode(',', $all_id_sub_skpd);
					$sub_bl = $wpdb->get_results($wpdb->prepare(
						"
						SELECT 
							kode_sbl 
						from data_sub_keg_bl 
						where tahun_anggaran=%d 
							AND id_sub_skpd IN ($all_id_sub_skpd)",
						$_POST['tahun_anggaran']
					), ARRAY_A);
					$ret['sql'] = $wpdb->last_query;
					foreach ($sub_bl as $k => $sub) {
						$cek_aktif = false;
						foreach ($subkeg_aktif as $v) {
							if ($v['kode_sbl'] == $sub['kode_sbl']) {
								$cek_aktif = true;
								break;
							}
						}
						$aktif = 0;
						if ($cek_aktif) {
							$aktif = 1;
						}
						$wpdb->update('data_sub_keg_bl', array('active' => $aktif), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $sub['kode_sbl']
						));
						$wpdb->update('data_sub_keg_indikator', array('active' => $aktif), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $sub['kode_sbl']
						));
						$wpdb->update('data_keg_indikator_hasil', array('active' => $aktif), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $sub['kode_sbl']
						));
						$wpdb->update('data_tag_sub_keg', array('active' => $aktif), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $sub['kode_sbl']
						));
						$wpdb->update('data_capaian_prog_sub_keg', array('active' => $aktif), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $sub['kode_sbl']
						));
						$wpdb->update('data_output_giat_sub_keg', array('active' => $aktif), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $sub['kode_sbl']
						));
						$wpdb->update('data_dana_sub_keg', array('active' => $aktif), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $sub['kode_sbl']
						));
						$wpdb->update('data_lokasi_sub_keg', array('active' => $aktif), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $sub['kode_sbl']
						));
						$wpdb->update('data_rka', array('active' => $aktif), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $sub['kode_sbl']
						));
					}
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

	public function update_nonactive_sub_bl_kas()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil update data_sub_keg_bl > data_anggaran_kas nonactive!',
			'action'	=> $_POST['action'],
			'id_unit'	=> $_POST['id_unit']
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$wpdb->update('data_sub_keg_bl', array('active' => 0), array(
					'tahun_anggaran' => $_POST['tahun_anggaran'],
					'id_sub_skpd' => $_POST['id_unit']
				));
				$sub_bl = $wpdb->get_results("SELECT kode_sbl, id_bidang_urusan from data_sub_keg_bl where tahun_anggaran=" . $_POST['tahun_anggaran'] . " AND id_sub_skpd='" . $_POST['id_unit'] . "'", ARRAY_A);
				foreach ($sub_bl as $k => $sub) {
					$kode_sbl = explode('.', $sub['kode_sbl']);
					$kode_sbl1 = $kode_sbl[0] . '.' . $kode_sbl[1] . '.' . $sub['id_bidang_urusan'] . '.' . $kode_sbl[2] . '.' . $kode_sbl[3] . '.' . $kode_sbl[4];
					$kode_sbl2 = $kode_sbl[1] . '.' . $kode_sbl1;
					$wpdb->update('data_anggaran_kas', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $kode_sbl1
					));
					$wpdb->update('data_anggaran_kas', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $kode_sbl2
					));
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

	public function singkron_rka()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'kode_sbl'	=> $_POST['kode_sbl'],
			'status'	=> 'success',
			'message'	=> 'Berhasil export RKA!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$kodeunit = '';
				if (!empty($_POST['data_unit'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$data_unit = json_decode(stripslashes(html_entity_decode($_POST['data_unit'])), true);
						$data_unit = $data_unit[0];
						$kodeunit = $data_unit['kode_unit'];
						$_POST['nama_skpd'] = $data_unit['nama_skpd'];
						$_POST['kode_sub_skpd'] = $data_unit['kode_skpd'];
					} else {
						$data_unit = $_POST['data_unit'];
						$kodeunit = $data_unit['kodeunit'];
						$_POST['nama_skpd'] = $data_unit['namaunit'];
						$_POST['kode_sub_skpd'] = $data_unit['kodeunit'];
					}
					//$data_unit = $_POST['data_unit'];
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Unit Salah!';
				}
				if (!isset($_POST['idsubbl'])) {
					$_POST['idsubbl'] = '';
				}
				if (!isset($_POST['idbl'])) {
					$_POST['idbl'] = '';
				}

				$_POST['idsubbl'] = (int) $_POST['idsubbl'];
				$_POST['idbl'] = (int) $_POST['idbl'];

				if (!empty($_POST['dataBl']) && $ret['status'] != 'error') {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$dataBl = json_decode(stripslashes(html_entity_decode($_POST['dataBl'])), true);
					} else {
						$dataBl = $_POST['dataBl'];
					}
					foreach ($dataBl as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								kode_sbl 
							from data_sub_keg_bl 
							where tahun_anggaran=%d 
								AND kode_sbl=%s
						", $_POST['tahun_anggaran'], $_POST['kode_sbl']));

						$kode_program = $v['kode_bidang_urusan'] . substr($v['kode_program'], 4, strlen($v['kode_program']));
						$kode_giat = $v['kode_bidang_urusan'] . substr($v['kode_giat'], 4, strlen($v['kode_giat']));
						$kode_sub_giat = $v['kode_bidang_urusan'] . substr($v['kode_sub_giat'], 4, strlen($v['kode_sub_giat']));
						// die($kode_giat);
						if (!isset($v['id_sub_bl'])) {
							$v['id_sub_bl'] = '';
						}
						if (!isset($v['id_bl'])) {
							$v['id_bl'] = '';
						}
						if (!isset($v['idsubbl'])) {
							$v['idsubbl'] = '';
						}
						if (!isset($v['idbl'])) {
							$v['idbl'] = '';
						}
						if (!isset($v['id_lokasi'])) {
							$v['id_lokasi'] = '';
						}
						if (!isset($v['nama_dana'])) {
							$v['nama_dana'] = '';
						}
						if (!isset($v['nama_lokasi'])) {
							$v['nama_lokasi'] = '';
						}
						if (!isset($v['id_dana'])) {
							$v['id_dana'] = '';
						}
						if (!isset($v['id_label_kokab'])) {
							$v['id_label_kokab'] = 0;
						}
						if (!isset($v['no_program'])) {
							$v['no_program'] = '';
						}
						if (!isset($v['target_1'])) {
							$v['target_1'] = '';
						}
						if (!isset($v['target_2'])) {
							$v['target_2'] = '';
						}
						if (!isset($v['target_3'])) {
							$v['target_3'] = '';
						}
						if (!isset($v['target_4'])) {
							$v['target_4'] = '';
						}
						if (!isset($v['target_5'])) {
							$v['target_5'] = '';
						}
						if (!isset($v['nama_bidang_urusan'])) {
							$v['nama_bidang_urusan'] = '';
						}
						if (!isset($v['no_giat'])) {
							$v['no_giat'] = '';
						}
						if (!isset($v['id_label_prov'])) {
							$v['id_label_prov'] = 0;
						}
						if (!isset($v['label_prov'])) {
							$v['label_prov'] = '';
						}
						if (!isset($v['output_sub_giat'])) {
							$v['output_sub_giat'] = '';
						}
						if (!isset($v['sasaran'])) {
							$v['sasaran'] = '';
						}
						if (!isset($v['indikator'])) {
							$v['indikator'] = '';
						}
						if (!isset($v['pagu_n_depan'])) {
							$v['pagu_n_depan'] = 0;
						}
						if (!isset($v['satuan'])) {
							$v['satuan'] = '';
						}
						if (!isset($v['id_rpjmd'])) {
							$v['id_rpjmd'] = 0;
						}
						if (!isset($v['id_giat'])) {
							$v['id_giat'] = 0;
						}
						if (!isset($v['id_label_pusat'])) {
							$v['id_label_pusat'] = '';
						}
						if (!isset($_POST['nama_skpd'])) {
							$_POST['nama_skpd'] = '';
						}
						if (!isset($v['kode_skpd'])) {
							$v['kode_skpd'] = '';
						}
						if (!isset($v['label_pusat'])) {
							$v['label_pusat'] = '';
						}
						if (!isset($v['label_kokab'])) {
							$v['label_kokab'] = '';
						}

						$opsi = array(
							'id_sub_skpd' => $v['id_sub_skpd'],
							'id_lokasi' => $v['id_lokasi'],
							'id_label_kokab' => $v['id_label_kokab'],
							'nama_dana' => $v['nama_dana'],
							'no_sub_giat' => $v['no_sub_giat'],
							'kode_giat' => $kode_giat,
							'id_program' => $v['id_program'],
							'nama_lokasi' => $v['nama_lokasi'],
							'waktu_akhir' => $v['waktu_akhir'],
							'pagu_n_lalu' => $v['pagu_n_lalu'],
							'id_urusan' => $v['id_urusan'],
							'id_unik_sub_bl' => $v['id_unik_sub_bl'],
							'id_sub_giat' => $v['id_sub_giat'],
							'label_prov' => $v['label_prov'],
							'kode_program' => $kode_program,
							'kode_sub_giat' => $kode_sub_giat,
							'no_program' => $v['no_program'],
							'kode_urusan' => $v['kode_urusan'],
							'kode_bidang_urusan' => $v['kode_bidang_urusan'],
							'nama_program' => $v['nama_program'],
							'target_4' => $v['target_4'],
							'target_5' => $v['target_5'],
							'id_bidang_urusan' => $v['id_bidang_urusan'],
							'nama_bidang_urusan' => $v['nama_bidang_urusan'],
							'target_3' => $v['target_3'],
							'no_giat' => $v['no_giat'],
							'id_label_prov' => $v['id_label_prov'],
							'waktu_awal' => $v['waktu_awal'],
							'pagu' => $v['pagu'],
							'pagumurni' => $v['pagumurni'],
							'output_sub_giat' => $v['output_sub_giat'],
							'sasaran' => $v['sasaran'],
							'indikator' => $v['indikator'],
							'id_dana' => $v['id_dana'],
							'nama_sub_giat' => $v['nama_sub_giat'],
							'pagu_n_depan' => $v['pagu_n_depan'],
							'satuan' => $v['satuan'],
							'id_rpjmd' => $v['id_rpjmd'],
							'id_giat' => $v['id_giat'],
							'id_label_pusat' => $v['id_label_pusat'],
							'nama_giat' => $v['nama_giat'],
							'kode_skpd' => $_POST['kode_skpd'],
							'nama_skpd' => $_POST['nama_skpd'],
							'kode_sub_skpd' => $_POST['kode_sub_skpd'],
							'id_skpd' => $v['id_skpd'],
							'id_sub_bl' => $v['id_sub_bl'],
							'nama_sub_skpd' => $v['nama_sub_skpd'],
							'target_1' => $v['target_1'],
							'nama_urusan' => $v['nama_urusan'],
							'target_2' => $v['target_2'],
							'label_kokab' => $v['label_kokab'],
							'label_pusat' => $v['label_pusat'],
							'pagu_keg' => $_POST['pagu'],
							'id_bl' => $v['id_bl'],
							'kode_bl' => $_POST['kode_bl'],
							'kode_sbl' => $_POST['kode_sbl'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						// print_r($opsi); die($wpdb->last_query);

						if (!empty($cek)) {
							$wpdb->update('data_sub_keg_bl', $opsi, array(
								'kode_sbl' => $_POST['kode_sbl'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_sub_keg_bl', $opsi);
						}

						$nama_page = $_POST['tahun_anggaran'] . ' | ' . $kodeunit . ' | ' . $kode_giat . ' | ' . $v['nama_giat'];
						$url_kegiatan = $this->generatePage($nama_page, $_POST['tahun_anggaran'], '[tampilrka kode_bl="' . $_POST['kode_bl'] . '" tahun_anggaran="' . $_POST['tahun_anggaran'] . '"]');
						$ret['message'] .= ' URL ' . $url_kegiatan;
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data BL Salah!';
				}

				if ($_POST['total_page'] == $_POST['no_page']) {
					if (!empty($_POST['dataOutput']) && $ret['status'] != 'error') {
						if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
							$dataOutput = json_decode(stripslashes(html_entity_decode($_POST['dataOutput'])), true);
						} else {
							$dataOutput = $_POST['dataOutput'];
						}
						$wpdb->update('data_sub_keg_indikator', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataOutput as $k => $v) {
							$cek = $wpdb->get_var($wpdb->prepare("
								SELECT 
									kode_sbl 
								from data_sub_keg_indikator 
								where tahun_anggaran=%d 
									AND kode_sbl=%s 
									AND idoutputbl=%s
							", $_POST['tahun_anggaran'], $_POST['kode_sbl'], $v['idoutputbl']));
							$opsi = array(
								'outputteks' => $v['outputteks'],
								'targetoutput' => $v['targetoutput'],
								'satuanoutput' => $v['satuanoutput'],
								'idoutputbl' => $v['idoutputbl'],
								'targetoutputteks' => $v['targetoutputteks'],
								'kode_sbl' => $_POST['kode_sbl'],
								'idsubbl' => $_POST['idsubbl'],
								'active' => 1,
								'update_at' => current_time('mysql'),
								'tahun_anggaran' => $_POST['tahun_anggaran']
							);
							if (!empty($cek)) {
								$wpdb->update('data_sub_keg_indikator', $opsi, array(
									'kode_sbl' => $_POST['kode_sbl'],
									'idoutputbl' => $v['idoutputbl'],
									'tahun_anggaran' => $_POST['tahun_anggaran']
								));
							} else {
								$wpdb->insert('data_sub_keg_indikator', $opsi);
							}
						}
					}

					if (!empty($_POST['dataHasil']) && $ret['status'] != 'error') {
						if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
							$dataHasil = json_decode(stripslashes(html_entity_decode($_POST['dataHasil'])), true);
						} else {
							$dataHasil = $_POST['dataHasil'];
						}
						$wpdb->update('data_keg_indikator_hasil', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataHasil as $k => $v) {
							$cek = $wpdb->get_var($wpdb->prepare("
								SELECT 
									kode_sbl 
								from data_keg_indikator_hasil 
								where tahun_anggaran=%d 
									AND kode_sbl=%s 
									AND hasilteks=%s
							", $_POST['tahun_anggaran'], $_POST['kode_sbl'], $v['hasilteks']));
							$opsi = array(
								'hasilteks' => $v['hasilteks'],
								'satuanhasil' => $v['satuanhasil'],
								'targethasil' => $v['targethasil'],
								'targethasilteks' => $v['targethasilteks'],
								'kode_sbl' => $_POST['kode_sbl'],
								'idsubbl' => $_POST['idsubbl'],
								'active' => 1,
								'update_at' => current_time('mysql'),
								'tahun_anggaran' => $_POST['tahun_anggaran']
							);
							if (!empty($cek)) {
								$wpdb->update('data_keg_indikator_hasil', $opsi, array(
									'kode_sbl' => $_POST['kode_sbl'],
									'hasilteks' => $v['hasilteks'],
									'tahun_anggaran' => $_POST['tahun_anggaran']
								));
							} else {
								$wpdb->insert('data_keg_indikator_hasil', $opsi);
							}
						}
					}

					if (!empty($_POST['dataTag']) && $ret['status'] != 'error') {
						if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
							$dataTag = json_decode(stripslashes(html_entity_decode($_POST['dataTag'])), true);
						} else {
							$dataTag = $_POST['dataTag'];
						}
						$wpdb->update('data_tag_sub_keg', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataTag as $k => $v) {
							$cek = $wpdb->get_var($wpdb->prepare("
								SELECT 
									kode_sbl 
								from data_tag_sub_keg 
								where tahun_anggaran=%d 
									AND kode_sbl=%s 
									AND idtagbl=%s
							", $_POST['tahun_anggaran'], $_POST['kode_sbl'], $v['idtagbl']));
							$opsi = array(
								'idlabelgiat' => $v['idlabelgiat'],
								'namalabel' => $v['namalabel'],
								'idtagbl' => $v['idtagbl'],
								'kode_sbl' => $_POST['kode_sbl'],
								'idsubbl' => $_POST['idsubbl'],
								'active' => 1,
								'update_at' => current_time('mysql'),
								'tahun_anggaran' => $_POST['tahun_anggaran']
							);
							if (!empty($cek)) {
								$wpdb->update('data_tag_sub_keg', $opsi, array(
									'kode_sbl' => $_POST['kode_sbl'],
									'idtagbl' => $v['idtagbl'],
									'tahun_anggaran' => $_POST['tahun_anggaran']
								));
							} else {
								$wpdb->insert('data_tag_sub_keg', $opsi);
							}
						}
					}

					if (!empty($_POST['dataCapaian']) && $ret['status'] != 'error') {
						if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
							$dataCapaian = json_decode(stripslashes(html_entity_decode($_POST['dataCapaian'])), true);
						} else {
							$dataCapaian = $_POST['dataCapaian'];
						}
						$wpdb->update('data_capaian_prog_sub_keg', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataCapaian as $k => $v) {
							$cek = $wpdb->get_var($wpdb->prepare("
								SELECT 
									kode_sbl 
								from data_capaian_prog_sub_keg 
								where tahun_anggaran=%d 
									AND kode_sbl=%s 
									AND capaianteks=%s
							", $_POST['tahun_anggaran'], $_POST['kode_sbl'], $v['capaianteks']));
							$opsi = array(
								'satuancapaian' => $v['satuancapaian'],
								'targetcapaianteks' => $v['targetcapaianteks'],
								'capaianteks' => $v['capaianteks'],
								'targetcapaian' => $v['targetcapaian'],
								'kode_sbl' => $_POST['kode_sbl'],
								'idsubbl' => $_POST['idsubbl'],
								'active' => 1,
								'update_at' => current_time('mysql'),
								'tahun_anggaran' => $_POST['tahun_anggaran']
							);
							if (!empty($cek)) {
								$wpdb->update('data_capaian_prog_sub_keg', $opsi, array(
									'kode_sbl' => $_POST['kode_sbl'],
									'capaianteks' => $v['capaianteks'],
									'tahun_anggaran' => $_POST['tahun_anggaran']
								));
							} else {
								$wpdb->insert('data_capaian_prog_sub_keg', $opsi);
							}
						}
					}

					if (!empty($_POST['dataOutputGiat']) && $ret['status'] != 'error') {
						if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
							$dataOutputGiat = json_decode(stripslashes(html_entity_decode($_POST['dataOutputGiat'])), true);
						} else {
							$dataOutputGiat = $_POST['dataOutputGiat'];
						}
						$wpdb->update('data_output_giat_sub_keg', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataOutputGiat as $k => $v) {
							$cek = $wpdb->get_var($wpdb->prepare("
								SELECT 
									kode_sbl 
								from data_output_giat_sub_keg 
								where tahun_anggaran=%d 
									AND kode_sbl=%s 
									AND outputteks=%s
							", $_POST['tahun_anggaran'], $_POST['kode_sbl'], $v['outputteks']));
							$opsi = array(
								'outputteks' => $v['outputteks'],
								'satuanoutput' => $v['satuanoutput'],
								'targetoutput' => $v['targetoutput'],
								'targetoutputteks' => $v['targetoutputteks'],
								'kode_sbl' => $_POST['kode_sbl'],
								'idsubbl' => $_POST['idsubbl'],
								'active' => 1,
								'update_at' => current_time('mysql'),
								'tahun_anggaran' => $_POST['tahun_anggaran']
							);
							if (!empty($cek)) {
								$wpdb->update('data_output_giat_sub_keg', $opsi, array(
									'kode_sbl' => $_POST['kode_sbl'],
									'outputteks' => $v['outputteks'],
									'tahun_anggaran' => $_POST['tahun_anggaran']
								));
							} else {
								$wpdb->insert('data_output_giat_sub_keg', $opsi);
							}
						}
					}

					if (!empty($_POST['dataDana']) && $ret['status'] != 'error') {
						if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
							$dataDana = json_decode(stripslashes(html_entity_decode($_POST['dataDana'])), true);
						} else {
							$dataDana = $_POST['dataDana'];
						}
						$wpdb->update('data_dana_sub_keg', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataDana as $k => $v) {
							$cek = $wpdb->get_var($wpdb->prepare("
								SELECT 
									kode_sbl 
								from data_dana_sub_keg 
								where tahun_anggaran=%d 
									AND kode_sbl=%s 
									AND iddanasubbl=%s
							", $_POST['tahun_anggaran'], $_POST['kode_sbl'], $v['iddanasubbl']));
							$opsi = array(
								'namadana' => $v['namadana'],
								'kodedana' => $v['kodedana'],
								'iddana' => $v['iddana'],
								'iddanasubbl' => $v['iddanasubbl'],
								'pagudana' => $v['pagudana'],
								'is_locked' => $v['is_locked'],
								'kode_sbl' => $_POST['kode_sbl'],
								'idsubbl' => $_POST['idsubbl'],
								'active' => 1,
								'update_at' => current_time('mysql'),
								'tahun_anggaran' => $_POST['tahun_anggaran']
							);
							if (!empty($cek)) {
								$wpdb->update('data_dana_sub_keg', $opsi, array(
									'kode_sbl' => $_POST['kode_sbl'],
									'iddanasubbl' => $v['iddanasubbl'],
									'tahun_anggaran' => $_POST['tahun_anggaran']
								));
							} else {
								$wpdb->insert('data_dana_sub_keg', $opsi);
							}
						}
					}
					if (!empty($_POST['dataLokout']) && $ret['status'] != 'error') {
						if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
							$dataLokout = json_decode(stripslashes(html_entity_decode($_POST['dataLokout'])), true);
						} else {
							$dataLokout = $_POST['dataLokout'];
						}
						$wpdb->update('data_lokasi_sub_keg', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataLokout as $k => $v) {
							$cek = $wpdb->get_var($wpdb->prepare("
								SELECT 
									kode_sbl 
								from data_lokasi_sub_keg 
								where tahun_anggaran=%d 
									AND kode_sbl=%s 
									AND iddetillokasi=%s
							", $_POST['tahun_anggaran'], $_POST['kode_sbl'], $v['iddetillokasi']));
							$opsi = array(
								'camatteks' => $v['camatteks'],
								'daerahteks' => $v['daerahteks'],
								'idcamat' => $v['idcamat'],
								'iddetillokasi' => $v['iddetillokasi'],
								'idkabkota' => $v['idkabkota'],
								'idlurah' => $v['idlurah'],
								'lurahteks' => $v['lurahteks'],
								'kode_sbl' => $_POST['kode_sbl'],
								'idsubbl' => $_POST['idsubbl'],
								'active' => 1,
								'update_at' => current_time('mysql'),
								'tahun_anggaran' => $_POST['tahun_anggaran']
							);
							if (!empty($cek)) {
								$wpdb->update('data_lokasi_sub_keg', $opsi, array(
									'kode_sbl' => $_POST['kode_sbl'],
									'iddetillokasi' => $v['iddetillokasi'],
									'tahun_anggaran' => $_POST['tahun_anggaran']
								));
							} else {
								$wpdb->insert('data_lokasi_sub_keg', $opsi);
							}
						}
					}
					if (!empty($_POST['realisasi']) && $ret['status'] != 'error') {
						$realisasi = $_POST['realisasi'];
						$wpdb->update('data_realisasi_akun_sipd', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($realisasi as $k => $v) {
							$cek_id = $wpdb->get_var($wpdb->prepare("
								SELECT 
									kode_sbl 
								from data_realisasi_akun_sipd 
								where tahun_anggaran=%d 
									AND kode_sbl=%s 
									AND kode_akun=%s
							", $_POST['tahun_anggaran'], $_POST['kode_sbl'], $v['kode_akun']));
							$opsi = array(
								'id_unit' => $v['id_unit'],
								'id_skpd' => $v['id_skpd'],
								'id_sub_skpd' => $v['id_sub_skpd'],
								'id_program' => $v['id_program'],
								'id_giat' => $v['id_giat'],
								'id_sub_giat' => $v['id_sub_giat'],
								'id_daerah' => $v['id_daerah'],
								'lokus_akun_teks' => $v['lokus_akun_teks'],
								'id_akun' => $v['id_akun'],
								'kode_akun' => $v['kode_akun'],
								'nama_akun' => $v['nama_akun'],
								'is_locked' => $v['is_locked'],
								'nilai' => $v['nilai'],
								'action' => $v['action'],
								'realisasi' => $v['realisasi'],
								'kode_sbl' => $_POST['kode_sbl'],
								'active' => 1,
								'update_at' => current_time('mysql'),
								'tahun_anggaran' => $_POST['tahun_anggaran']
							);
							if (!empty($cek_id)) {
								$wpdb->update('data_realisasi_akun_sipd', $opsi, array(
									'id' => $cek_id
								));
							} else {
								$wpdb->insert('data_realisasi_akun_sipd', $opsi);
							}
						}
					}
				}

				$iddana = false;
				if (!empty($_POST['dataDana'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$dataDana = json_decode(stripslashes(html_entity_decode($_POST['dataDana'])), true);
					} else {
						$dataDana = $_POST['dataDana'];
					}
					foreach ($dataDana as $k => $v) {
						if (
							empty($iddana)
							&& !empty($v['iddana'])
						) {
							$iddana = $v['iddana'];
						}
					}
				}
				if (empty($iddana)) {
					$iddana = get_option('_crb_default_sumber_dana');
				}

				if (!empty($_POST['rka']) && $ret['status'] != 'error') {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$rka = json_decode(stripslashes($_POST['rka']), true);
					} else {
						$rka = $_POST['rka'];
						if ($rka == 0) {
							$rka = array();
						}
					}
					if (
						(
							empty($rka)
							&& $_POST['no_page'] == 0
						)
						|| (
							!empty($_POST['no_page'])
							&& $_POST['no_page'] == 1
						)
					) {
						$wpdb->update('data_rka', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						), array('%d', '%s'));
					}
					foreach ($rka as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id_rinci_sub_bl 
							from data_rka 
							where tahun_anggaran=%d 
								AND id_rinci_sub_bl=%s 
								AND kode_sbl=%s
						", $_POST['tahun_anggaran'], $v['id_rinci_sub_bl'], $_POST['kode_sbl']));
						$opsi = array(
							'created_user' => $v['created_user'],
							'createddate' => $v['createddate'],
							'createdtime' => $v['createdtime'],
							'harga_satuan' => $v['harga_satuan'],
							'harga_satuan_murni' => $v['harga_satuan_murni'],
							'id_daerah' => $v['id_daerah'],
							'id_rinci_sub_bl' => $v['id_rinci_sub_bl'],
							'id_standar_nfs' => $v['id_standar_nfs'],
							'is_locked' => $v['is_locked'],
							'jenis_bl' => $v['jenis_bl'],
							'ket_bl_teks' => $v['ket_bl_teks'],
							'kode_akun' => $v['kode_akun'],
							'koefisien' => $v['koefisien'],
							'koefisien_murni' => $v['koefisien_murni'],
							'lokus_akun_teks' => $v['lokus_akun_teks'],
							'nama_akun' => $v['nama_akun'],
							'nama_komponen' => $v['nama_komponen'],
							'spek_komponen' => $v['spek_komponen'],
							'satuan' => $v['satuan'],
							'sat1' => $v['sat1'],
							'sat2' => $v['sat2'],
							'sat3' => $v['sat3'],
							'sat4' => $v['sat4'],
							'volum1' => $v['volum1'],
							'volum2' => $v['volum2'],
							'volum3' => $v['volum3'],
							'volum4' => $v['volum4'],
							'volume' => $v['volume'],
							'volume_murni' => $v['volume_murni'],
							'spek' => $v['spek'],
							'subs_bl_teks' => $v['subs_bl_teks']['subs_asli'],
							'substeks' => $v['subs_bl_teks']['substeks'],
							'id_dana' => $v['subs_bl_teks']['sumber_dana']['id_dana'],
							'nama_dana' => $v['subs_bl_teks']['sumber_dana']['nama_dana'],
							'is_paket' => $v['subs_bl_teks']['sumber_dana']['is_paket'],
							'kode_dana' => $v['subs_bl_teks']['sumber_dana']['kode_dana'],
							'subtitle_teks' => $v['subs_bl_teks']['sumber_dana']['subtitle_teks'],
							'total_harga' => $v['total_harga'],
							'rincian' => $v['rincian'],
							'rincian_murni' => $v['rincian_murni'],
							'totalpajak' => $v['totalpajak'],
							'pajak' => $v['pajak'],
							'pajak_murni' => $v['pajak_murni'],
							'updated_user' => $v['updated_user'],
							'updateddate' => $v['updateddate'],
							'updatedtime' => $v['updatedtime'],
							'user1' => $this->replace_char($v['user1']),
							'user2' => $this->replace_char($v['user2']),
							'idbl' => $_POST['idbl'],
							'idsubbl' => $_POST['idsubbl'],
							'kode_bl' => $_POST['kode_bl'],
							'kode_sbl' => $_POST['kode_sbl'],
							'idkomponen' => $v['idkomponen'],
							'idketerangan' => $v['idketerangan'],
							'idsubtitle' => $v['subs_bl_teks']['sumber_dana']['id_subtitle'],
							'ssh_locked' => $v['ssh_locked'],
							'akun_locked' => $v['akun_locked'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (
							!empty($v['id_penerima'])
							|| !empty($v['id_prop_penerima'])
							|| !empty($v['id_camat_penerima'])
							|| !empty($v['id_kokab_penerima'])
							|| !empty($v['id_lurah_penerima'])
						) {
							$opsi['id_prop_penerima'] = $v['id_prop_penerima'];
							$opsi['id_camat_penerima'] = $v['id_camat_penerima'];
							$opsi['id_kokab_penerima'] = $v['id_kokab_penerima'];
							$opsi['id_lurah_penerima'] = $v['id_lurah_penerima'];
							$opsi['id_penerima'] = $v['id_penerima'];
						}

						if (!empty($cek)) {
							$wpdb->update('data_rka', $opsi, array(
								'tahun_anggaran' => $_POST['tahun_anggaran'],
								'id_rinci_sub_bl' => $v['id_rinci_sub_bl'],
								'kode_sbl' => $_POST['kode_sbl']
							));
						} else {
							$wpdb->insert('data_rka', $opsi);
						}
						// print_r($opsi); print_r($wpdb->last_query);

						if (!empty($v['id_rinci_sub_bl'])) {
							$cek_id = $wpdb->get_var($wpdb->prepare(
								'
								select 
									id 
								from data_mapping_sumberdana 
								where tahun_anggaran=%d
									and id_rinci_sub_bl=%d 
									and active=1',
								$_POST['tahun_anggaran'],
								$v['id_rinci_sub_bl']
							));
							$opsi = array(
								'id_rinci_sub_bl' => $v['id_rinci_sub_bl'],
								'id_sumber_dana' => $iddana,
								'user' => 'Singkron SIPD Merah',
								'active' => 1,
								'update_at' => current_time('mysql'),
								'tahun_anggaran' => $_POST['tahun_anggaran']
							);
							$update = false;
							if (!empty($v['subs_bl_teks']['sumber_dana']['id_dana'])) {
								$update = true;
								$opsi['id_sumber_dana'] = $v['subs_bl_teks']['sumber_dana']['id_dana'];
							}
							if (empty($cek_id)) {
								$wpdb->insert('data_mapping_sumberdana', $opsi);
							} else if ($update) {
								$wpdb->update('data_mapping_sumberdana', $opsi, array(
									'id' => $cek_id
								));
							}
						}
					}
				} else if ($ret['status'] != 'error') {
					// untuk menghapus rka subkeg yang dihapus di perubahan
					if ($_POST['rka'] == 0) {
						$wpdb->update('data_rka', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						), array('%d', '%s'));
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'Format RKA Salah!';
					}
				}

				if (
					get_option('_crb_singkron_simda') == 1
					&& get_option('_crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
					&& $_POST['total_page'] == $_POST['no_page']
				) {
					$debug = false;
					if (get_option('_crb_singkron_simda_debug') == 1) {
						$debug = true;
					}
					$this->simda->singkronSimda(array(
						'return' => $debug
					));
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

	public function getSSH()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Akun Rekening Belanja!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['id_akun'])) {
				$data_ssh = $wpdb->get_results(
					$wpdb->prepare("
					SELECT 
						s.id_standar_harga, 
						s.nama_standar_harga, 
						s.kode_standar_harga 
					from data_ssh_rek_belanja r
						join data_ssh s ON r.id_standar_harga=s.id_standar_harga
					where r.id_akun=%d", $_POST['id_akun']),
					ARRAY_A
				);
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'Format ID Salah!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	public function rekbelanja($atts)
	{
		$a = shortcode_atts(array(
			'filter' => '',
		), $atts);
		global $wpdb;

		$data_akun = $wpdb->get_results(
			"
			SELECT 
				* 
			from data_akun 
			where belanja='Ya'
				AND is_barjas=1
				AND set_input=1",
			ARRAY_A
		);
		$tbody = '';
		$no = 1;
		foreach ($data_akun as $k => $v) {
			// if($k >= 100){ continue; }
			$data_ssh = $wpdb->get_results(
				"
				SELECT 
					s.id_standar_harga, 
					s.nama_standar_harga, 
					s.kode_standar_harga 
				from data_ssh_rek_belanja r
					join data_ssh s ON r.id_standar_harga=s.id_standar_harga
				where r.id_akun=" . $v['id_akun'],
				ARRAY_A
			);

			$ssh = array();
			foreach ($data_ssh as $key => $value) {
				$ssh[] = '(' . $value['id_standar_harga'] . ') ' . $value['kode_standar_harga'] . ' ' . $value['nama_standar_harga'];
			}
			if (!empty($a['filter'])) {
				if ($a['filter'] == 'ssh_kosong' && !empty($ssh)) {
					continue;
				}
			}

			$html_ssh = '';
			if (!empty($ssh)) {
				$html_ssh = '
					<a class="btn btn-primary" data-toggle="collapse" href="#collapseSSH' . $k . '" role="button" aria-expanded="false" aria-controls="collapseSSH' . $k . '">
				    	Lihat Item SSH Total (' . count($ssh) . ')
				  	</a>
				  	<div class="collapse" id="collapseSSH' . $k . '">
					  	<div class="card card-body">
				  			' . implode('<br>', $ssh) . '
					  	</div>
					</div>
				';
			}
			$tbody .= '
				<tr>
					<td class="text-center">' . number_format($no, 0, ",", ".") . '</td>
					<td class="text-center">ID: ' . $v['id_akun'] . '<br>Update pada:<br>' . $v['update_at'] . '</td>
					<td>' . $v['kode_akun'] . '</td>
					<td>' . $v['nama_akun'] . '</td>
					<td>' . $html_ssh . '</td>
				</tr>
			';
			$no++;
		}
		if (empty($tbody)) {
			$tbody = '<tr><td colspan="5" class="text-center">Data Kosong!</td></tr>';
		}
		$table = '
			<style>
				.text-center {
					text-align: center;
				}
				.text-right {
					text-align: right;
				}
			</style>
			<table>
				<thead>
					<tr>
						<th class="text-center" style="width: 30px;">No</th>
						<th class="text-center" style="width: 200px;">ID Akun</th>
						<th class="text-center" style="width: 100px;">Kode</th>
						<th class="text-center" style="width: 400px;">Nama</th>
						<th class="text-center">Item SSH</th>
					</tr>
				</thead>
				<tbody>' . $tbody . '</tbody>
			</table>
		';
		echo $table;
	}

	public function data_mapping_master_fmis($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/fmis/wpsipd-public-mapping-fmis.php';
	}

	public function monitoring_sql_migrate($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-sql-migrate.php';
	}

	public function monitor_json_rka($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-json-rka.php';
	}

	public function dokumentasi_api_wpsipd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/dokumentasi/wpsipd-public-dokumentasi-api.php';
	}

	public function rpjmd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/perencanaan/rpjmd.php';
	}

	public function monitoring_spd_rinci($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-spd-rinci.php';
	}

	public function monitoring_data_spd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-spd.php';
	}

	public function monitor_monev_rpjm($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monev-rpjm.php';
	}

	public function monitor_monev_renstra($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monev-renstra.php';
	}

	public function monitor_monev_renstra_pemda($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monev-renstra-pemda.php';
	}

	public function monitor_daftar_label_komponen($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-daftar-label-komponen.php';
	}

	public function monitor_daftar_sumber_dana($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-daftar-sumber-dana.php';
	}

	public function monitor_monev_renja($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-indikator-renja.php';
	}

	public function monitor_monev_renja_skpd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-indikator-renja-skpd.php';
	}

	public function monitor_rfk($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		if (!empty($atts['id_skpd'])) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-rfk.php';
		} else {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-rfk-pemda.php';
		}
	}

	public function monitor_sumber_dana($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		if (!empty($atts['id_skpd'])) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-sumberdana.php';
		} else {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-sumberdana-pemda.php';
		}
	}

	public function monitor_label_komponen($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-label-komponen.php';
	}

	public function monitor_sipd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/monev/wpsipd-public-monitor-update.php';
	}

	public function jadwal_monev_rpjmd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/monev/wpsipd-public-jadwal-monev-rpjmd.php';
	}

	public function jadwal_monev_renstra($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/monev/wpsipd-public-jadwal-monev-renstra.php';
	}

	public function jadwal_monev_renja($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/monev/wpsipd-public-jadwal-monev-renja.php';
	}

	public function tampilrka($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/wpsipd-public-rka.php';
	}

	public function tampilrkpd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/renja/wpsipd-public-rkpd.php';
	}
	public function daftar_penguji($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penatausahaan/wpsipd-public-daftar-penguji.php';
	}

	public function halaman_spd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penatausahaan/wpsipd-public-halaman-spd.php';
	}

	public function halaman_spp($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penatausahaan/wpsipd-public-halaman-spp.php';
	}

	public function halaman_sp2d($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penatausahaan/wpsipd-public-halaman-sp2d.php';
	}

	public function halaman_realisasi($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penatausahaan/wpsipd-public-halaman-realisasi.php';
	}

	public function halaman_spm($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penatausahaan/wpsipd-public-halaman-spm.php';
	}
	public function sk_up($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penatausahaan/wpsipd-public-halaman-sk-up.php';
	}

	public function apbdpenjabaran($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		$input = shortcode_atts(array(
			'idlabelgiat' => '',
			'lampiran' => '1',
			'id_skpd' => false,
			'tahun_anggaran' => '2021',
		), $atts);

		// RINGKASAN PENJABARAN APBD YANG DIKLASIFIKASI MENURUT KELOMPOK DAN JENIS PENDAPATAN, BELANJA, DAN PEMBIAYAAN
		if ($input['lampiran'] == 1) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran.php';
		}

		// RINCIAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI, PENDAPATAN, BELANJA DAN PEMBIAYAAN
		if ($input['lampiran'] == 2) {
			if (empty($input['id_skpd'])) {
				require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-2-pemda.php';
			} else {
				require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-2.php';
			}
		}

		// DAFTAR NAMA CALON PENERIMA, ALAMAT DAN BESARAN ALOKASI HIBAH BERUPA UANG & BARANG YANG DITERIMA SERTA SKPD PEMBERI HIBAH
		if ($input['lampiran'] == '3a') {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-3a.php';
		}

		// DAFTAR NAMA CALON PENERIMA, ALAMAT DAN BESARAN ALOKASI HIBAH BERUPA UANG & BARANG YANG DITERIMA SERTA SKPD PEMBERI HIBAH
		if ($input['lampiran'] == '3b') {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-3b.php';
		}

		if ($input['lampiran'] == '4a') {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-4a.php';
		}

		// DAFTAR NAMA CALON PENERIMA, ALAMAT DAN BESARAN ALOKASI BANTUAN SOSIAL BERUPA UANG YANG DITERIMA SERTA SKPD PEMBERI BANTUAN SOSIAL
		if ($input['lampiran'] == '4b') {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-4b.php';
		}

		// DAFTAR NAMA CALON PENERIMA, ALAMAT DAN BESARAN ALOKASI BANTUAN KEUANGAN BERSIFAT UMUM/KHUSUS YANG DITERIMA SERTA SKPD PEMBERI BANTUAN KEUANGAN
		if ($input['lampiran'] == '5a') {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-5a.php';
		}

		if ($input['lampiran'] == '5b') {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-5b.php';
		}

		// DAFTAR NAMA CALON PENERIMA, ALAMAT DAN BESARAN PERUBAHAN ALOKASI BELANJA BAGI HASIL PAJAK DAERAH KEPADA PEMERINTAH KABUPATEN, KOTA DAN DESA
		if ($input['lampiran'] == '6a') {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-6a.php';
		}

		if ($input['lampiran'] == '6b') {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-6b.php';
		}

		if ($input['lampiran'] == '6c') {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-6c.php';
		}

		if ($input['lampiran'] == 'per_triwulan') {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-per-triwulan.php';
		}

		if ($input['lampiran'] == 'kas_per_urusan') {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-kas-per-urusan.php';
		}

		// APBD dikelompokan berdasarkan mandatory spending atau tag label yang dipilih user ketika membuat sub kegiatan
		if ($input['lampiran'] == 99) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdpenjabaran/wpsipd-public-apbdpenjabaran-99.php';
		}
	}

	public function apbdperda($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		$input = shortcode_atts(array(
			'idlabelgiat' => '',
			'lampiran' => '1',
			'id_skpd' => false,
			'tahun_anggaran' => '2021',
		), $atts);

		// RINGKASAN APBD YANG DIKLASIFIKASI MENURUT KELOMPOK DAN JENIS PENDAPATAN, BELANJA, DAN PEMBIAYAAN
		if ($input['lampiran'] == 1) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda.php';
		}

		// RINGKASAN APBD YANG DIKLASIFIKASIKAN MENURUT URUSAN PEMERINTAHAN DAERAH DAN ORGANISASI
		if ($input['lampiran'] == 2) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-2.php';
		}

		// RINCIAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI, PROGRAM, KEGIATAN, SUB KEGIATAN, KELOMPOK, JENIS PENDAPATAN, BELANJA, DAN PEMBIAYAAN
		if ($input['lampiran'] == 3) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-3.php';
		}

		// REKAPITULASI BELANJA MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI, PROGRAM, KEGIATAN BESERTA HASIL DAN SUB KEGIATAN BESERTA SUB KELUARAN
		if ($input['lampiran'] == 4) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-4.php';
		}

		// REKAPITULASI BELANJA DAERAH UNTUK KESELARASAN DAN KETERPADUAN URUSAN PEMERINTAHAN DAERAH DAN FUNGSI DALAM KERANGKA PENGELOLAAN KEUANGAN NEGARA
		if ($input['lampiran'] == 5) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-5.php';
		}

		// REKAPITULASI BELANJA UNTUK PEMENUHAN SPM
		if ($input['lampiran'] == 6) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-6.php';
		}

		// SINKRONISASI PROGRAM PADA RPJMD/RPD DENGAN RANCANGAN APBD
		if ($input['lampiran'] == 7) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-7.php';
		}

		// SINKRONISASI PROGRAM, KEGIATAN DAN SUB KEGIATAN PADA RKPD DAN PPAS DENGAN RANCANGAN PERATURAN DAERAH TENTANG APBD
		if ($input['lampiran'] == 8) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-8.php';
		}

		// SIKRONISASI PROGRAM PRIORITAS NASIONAL DAN PRIORITAS PROVINSI DENGAN PROGRAM PRIORITAS KABUPATEN/KOTA
		if ($input['lampiran'] == 9) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-9.php';
		}

		// DAFTAR JUMLAH PEGAWAI PER GOLONGAN DAN PER JABATAN
		if ($input['lampiran'] == 10) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-10.php';
		}

		// DAFTAR PIUTANG DAERAH
		if ($input['lampiran'] == 11) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-11.php';
		}

		// APBD dikelompokan berdasarkan mandatory spending atau tag label yang dipilih user ketika membuat sub kegiatan
		if ($input['lampiran'] == 99) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penganggaran/apbdperda/wpsipd-public-apbdperda-99.php';
		}
	}

	public function setting_penjadwalan($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penjadwalan/wpsipd-public-setting-penjadwalan.php';
	}

	public function input_rpjm($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/input_perencanaan/wpsipd-public-input-rpjm.php';
	}

	public function input_renstra($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/input_perencanaan/wpsipd-public-input-renstra.php';
	}

	public function input_rpd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/input_perencanaan/wpsipd-public-input-rpd.php';
	}

	public function input_rpjpd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/input_perencanaan/wpsipd-public-input-rpjpd.php';
	}

	public function input_rka_lokal($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/input_perencanaan/wpsipd-public-input-rka.php';
	}

	public function get_unit()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['id_skpd'])) {
					$ret['data'] = $wpdb->get_results(
						$wpdb->prepare("
						SELECT 
							*
						from data_unit
						where id_skpd=%d
							AND tahun_anggaran=%d
							AND active=1
						order by id_skpd ASC", $_POST['id_skpd'], $_POST['tahun_anggaran']),
						ARRAY_A
					);
				} else if (!empty($_POST['kode_skpd'])) {
					$ret['data'] = $wpdb->get_results(
						$wpdb->prepare("
						SELECT 
							*
						from data_unit
						where kode_skpd=%s
							AND tahun_anggaran=%d
							AND active=1
						order by id_skpd ASC", $_POST['kode_skpd'], $_POST['tahun_anggaran']),
						ARRAY_A
					);
				} else {
					$ret['data'] = $wpdb->get_results(
						$wpdb->prepare("
						SELECT 
							*
						from data_unit
						where tahun_anggaran=%d
							AND active=1
						order by id_skpd ASC", $_POST['tahun_anggaran']),
						ARRAY_A
					);
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

	public function get_all_sub_unit()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['id_skpd'])) {
					$ret['data'] = $wpdb->get_results(
						$wpdb->prepare("
						SELECT 
							*
						from data_unit
						where id_unit=%d
							AND tahun_anggaran=%d
							AND active=1", $_POST['id_skpd'], $_POST['tahun_anggaran']),
						ARRAY_A
					);
					$ret['query'] = $wpdb->last_query;
					$ret['id_skpd'] = $_POST['id_skpd'];
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'ID SKPD tidak boleh kosong!';
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

	public function get_indikator()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'	=> array(
				'bl_query' => '',
				'bl' => array(),
				'output' => array(),
				'hasil' => array(),
				'ind_prog' => array(),
				'renstra' => array()
			)
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['kode_giat']) && !empty($_POST['kode_skpd'])) {
					$ret['data']['bl'] = $wpdb->get_results(
						$wpdb->prepare("
						SELECT 
							*
						from data_sub_keg_bl
						where kode_giat=%s
							AND kode_sub_skpd=%s
							AND tahun_anggaran=%d
							AND kode_sbl != ''
							AND active=1", $_POST['kode_giat'], $_POST['kode_skpd'], $_POST['tahun_anggaran']),
						ARRAY_A
					);
					$ret['data']['bl_query'] = $wpdb->last_query;
					// print_r($ret['data']['bl']); die($wpdb->last_query);
					$bl = $ret['data']['bl'];
					if (!empty($bl)) {
						$ret['data']['renstra'] = $wpdb->get_results(
							"
							SELECT 
								*
							from data_renstra
							where id_unit=" . $bl[0]['id_sub_skpd'] . "
								AND id_sub_giat=" . $bl[0]['id_sub_giat'] . "
								AND tahun_anggaran=" . $bl[0]['tahun_anggaran'] . "
								AND active=1",
							ARRAY_A
						);
						$ret['data']['output'] = $wpdb->get_results(
							"
							SELECT 
								* 
							from data_output_giat_sub_keg 
							where kode_sbl='" . $bl[0]['kode_sbl'] . "' 
								AND tahun_anggaran=" . $bl[0]['tahun_anggaran'] . "
								AND active=1",
							ARRAY_A
						);
						$ret['data']['hasil'] = $wpdb->get_results(
							"
							SELECT 
								* 
							from data_keg_indikator_hasil 
							where kode_sbl='" . $bl[0]['kode_sbl'] . "' 
								AND tahun_anggaran=" . $bl[0]['tahun_anggaran'] . "
								AND active=1",
							ARRAY_A
						);
						$ret['data']['ind_prog'] = $wpdb->get_results(
							"
							SELECT 
								* 
							from data_capaian_prog_sub_keg 
							where kode_sbl='" . $bl[0]['kode_sbl'] . "' 
								AND tahun_anggaran=" . $bl[0]['tahun_anggaran'] . "
								AND active=1",
							ARRAY_A
						);
					}
				} else {
					$ret['data'] = $wpdb->get_results('select * from data_unit');
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

	//Import Data UP SKPD
	public function singkron_up()
	{
		global $wpdb;
		$ret = array(
			"status" => "success",
			"message" => "Berhasil singkron Data SK UP",
			"action" => $_POST["action"]
		);
		//cek parameter dari client
		if (!empty($_POST)) {
			//cek API KEY
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST["data"];
				}

				foreach ($data as $k => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("select id_besaran_up from data_up_sipd where id_besaran_up=%d", $v["id_besaran_up"]));
					$opsi = array(
						"id_besaran_up" => $v["id_besaran_up"],
						"id_skpd" => $v["id_skpd"],
						"id_sub_skpd" => $v["id_sub_skpd"],
						"besaran_up" => $v["besaran_up"],
						"besaran_up_kkpd" => $v["besaran_up_kkpd"],
						"pagu" => $v["pagu"],
						"active" => 1,
						"create_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);

					if (!empty($cek)) { //Jika variable cek tidak kosong update data up
						$wpdb->update("data_up_sipd", $opsi, array("id_besaran_up" => $v["id_besaran_up"]));
					} else {
						$wpdb->insert("data_up_sipd", $opsi);
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Parameter data kosong";
		}
		die(json_encode($ret));
	}

	public function get_up_sipd()
	{
		global $wpdb;
		$ret = array(
			"status" => "success",
			"message" => "Berhasil get Data SK UP"
		);
		//cek parameter dari client
		if (!empty($_POST)) {
			//cek API KEY
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				if (!empty($tahun_anggaran)) {
					$up_results = $wpdb->get_results(
						$wpdb->prepare("
							SELECT 
								s.*
							FROM data_up_sipd s
							WHERE s.tahun_anggaran = %d
							  AND s.active = 1
						", $tahun_anggaran),
						ARRAY_A
					);

					if (!empty($up_results)) {
						$ret['data'] = $up_results;
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'Tidak ada data ditemukan!';
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Parameter tidak lengkap!';
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Parameter data kosong";
		}
		die(json_encode($ret));
	}

	//Import data SPP dari SIPD Penatausahaan
	public function singkron_spp()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil singkronisasi SPP'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				if (
					empty($_POST['page'])
					|| $_POST['page'] == 1
				) {
					$wpdb->update("data_spp_sipd", array('active' => 0), array(
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"idSubUnit" => $_POST['idSkpd'],
						"tipe" => $_POST['tipe']
					));
					$wpdb->update("data_spp_sipd_detail", array('active' => 0), array(
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"idSubSkpd" => $_POST['idSkpd'],
						"tipe" => $_POST['tipe']
					));
					$wpdb->update("data_spp_sipd_ri_detail", array('active' => 0), array(
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"idSubSkpd" => $_POST['idSkpd'],
						"tipe" => $_POST['tipe']
					));
				}
				foreach ($data as $i => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("
						select 
							idSpp 
						from data_spp_sipd 
						where idSpp=%d 
							and tahunSpp=%d
							and tahun_anggaran=%d
					", $v["idSpp"], $v["tahunSpp"], $v["tahun_anggaran"]));
					$opsi = array(
						"nomorSpp" => $v['nomorSpp'],
						"nilaiSpp" => $v['nilaiSpp'],
						"tanggalSpp" => $v['tanggalSpp'],
						"keteranganSpp" => $v['keteranganSpp'],
						"idSkpd" => $v['idSkpd'],
						"idSubUnit" => $v['idSubUnit'],
						"nilaiDisetujuiSpp" => $v['nilaiDisetujuiSpp'],
						"tanggalDisetujuiSpp" => $v['tanggalDisetujuiSpp'],
						"jenisSpp" => $v['jenisSpp'],
						"verifikasiSpp" => $v['verifikasiSpp'],
						"keteranganVerifikasi" => $v['keteranganVerifikasi'],
						"idSpd" => $v['idSpd'],
						"idPengesahanSpj" => $v['idPengesahanSpj'],
						"kunciRekening" => $v['kunciRekening'],
						"alamatPenerimaSpp" => $v['alamatPenerimaSpp'],
						"bankPenerimaSpp" => $v['bankPenerimaSpp'],
						"nomorRekeningPenerimaSpp" => $v['nomorRekeningPenerimaSpp'],
						"npwpPenerimaSpp" => $v['npwpPenerimaSpp'],
						"idUser" => $v['idUser'],
						"jenisLs" => $v['jenisLs'],
						"isUploaded" => $v['isUploaded'],
						"tahunSpp" => $v['tahunSpp'],
						"idKontrak" => $v['idKontrak'],
						"idBA" => $v['idBA'],
						"created_at" => $v['created_at'],
						"updated_at" => $v['updated_at'],
						"isSpm" => $v['isSpm'],
						"statusPerubahan" => $v['statusPerubahan'],
						"isDraft" => $v['isDraft'],
						"idSpp" => $v['idSpp'],
						"kodeDaerah" => $v['kodeDaerah'],
						"idDaerah" => $v['idDaerah'],
						"isGaji" => $v['isGaji'],
						"is_sptjm" => $v['is_sptjm'],
						"tanggal_otorisasi" => $v['tanggal_otorisasi'],
						"is_otorisasi" => $v['is_otorisasi'],
						"bulan_gaji" => $v['bulan_gaji'],
						"id_pegawai_pptk" => $v['id_pegawai_pptk'],
						"nama_pegawai_pptk" => $v['nama_pegawai_pptk'],
						"nip_pegawai_pptk" => $v['nip_pegawai_pptk'],
						"id_jadwal" => $v['id_jadwal'],
						"id_tahap" => $v['id_tahap'],
						"status_tahap" => $v['status_tahap'],
						"kode_tahap" => $v['kode_tahap'],
						"is_tpp" => $v['is_tpp'],
						"bulan_tpp" => $v['bulan_tpp'],
						"id_pengajuan_tu" => $v['id_pengajuan_tu'],
						"nomor_pengajuan_tu" => $v['nomor_pengajuan_tu'],
						"id_npd" => $v['id_npd'],
						"tipe" => $_POST['tipe'],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek)) {
						//Update data spm ditable data_spp_sipd
						$wpdb->update("data_spp_sipd", $opsi, array(
							"idSpp" => $v["idSpp"],
							"tahunSpp" => $v["tahunSpp"],
							"idSubUnit" => $_POST['idSkpd'],
							"tahun_anggaran" => $_POST["tahun_anggaran"],
							"tipe" => $_POST['tipe']
						));
					} else {
						//insert data spm ditable data_spp_sipd
						$wpdb->insert("data_spp_sipd", $opsi);
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data SPP Detail dari SIPD Penatausahaan
	public function singkron_spp_detail()
	{
		global $wpdb;
		$ret = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil singkronisasi Detail SPP'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					foreach ($data['detail'] as $i => $v) {
						$cek_id = $wpdb->get_var($wpdb->prepare("
							select 
								id 
							from data_spp_sipd_ri_detail 
							where id_skpd=%d
								and id_spp=%d
								and uraian=%s
								and jumlah=%s
								and kode_rekening=%s
								and tahun_anggaran=%d
						", $_POST['idSkpd'], $_POST['id_spp'], $v["uraian"], $v["jumlah"], $v["kode_rekening"], $_POST["tahun_anggaran"]));
						$opsi = array(
							"id_spp" => $_POST['id_spp'],
							"id_skpd" => $_POST['idSkpd'],
							"nomor_spd" => $data['dasar_pembayaran']["nomor_spd"],
							"tanggal_spd" => $data['dasar_pembayaran']["tanggal_spd"],
							"total_spd" => $data['dasar_pembayaran']["total_spd"],
							"jumlah" => $v["jumlah"],
							"kode_rekening" => $v["kode_rekening"],
							"uraian" => $v["uraian"],
							"bank_bp_bpp" => $data['header']["bank_bp_bpp"],
							"jabatan_bp_bpp" => $data['header']["jabatan_bp_bpp"],
							"jabatan_pa_kpa" => $data['header']["jabatan_pa_kpa"],
							"jenis_ls_spp" => $data['header']["jenis_ls_spp"],
							"keterangan" => $data['header']["keterangan"],
							"nama_bp_bpp" => $data['header']["nama_bp_bpp"],
							"nama_daerah" => $data['header']["nama_daerah"],
							"nama_ibukota" => $data['header']["nama_ibukota"],
							"nama_pa_kpa" => $data['header']["nama_pa_kpa"],
							"nama_pptk" => $data['header']["nama_pptk"],
							"nama_rek_bp_bpp" => $data['header']["nama_rek_bp_bpp"],
							"nama_skpd" => $data['header']["nama_skpd"],
							"nama_sub_skpd" => $data['header']["nama_sub_skpd"],
							"nilai" => $data['header']["nilai"],
							"nip_bp_bpp" => $data['header']["nip_bp_bpp"],
							"nip_pa_kpa" => $data['header']["nip_pa_kpa"],
							"nip_pptk" => $data['header']["nip_pptk"],
							"no_rek_bp_bpp" => $data['header']["no_rek_bp_bpp"],
							"nomor_transaksi" => $data['header']["nomor_transaksi"],
							"npwp_bp_bpp" => $data['header']["npwp_bp_bpp"],
							"tahun" => $data['header']["tahun"],
							"tanggal_transaksi" => $data['header']["tanggal_transaksi"],
							"tipe" => $_POST['tipe'],
							"active" => 1,
							"update_at" => current_time('mysql'),
							"tahun_anggaran" => $_POST["tahun_anggaran"]
						);
						if (!empty($cek_id)) {
							//Update data spp ditable data_spp_sipd_ri_detail
							$wpdb->update("data_spp_sipd_ri_detail", $opsi, array(
								"id" => $cek_id
							));
						} else {
							//insert data spp ditable data_spp_sipd_ri_detail
							$wpdb->insert("data_spp_sipd_ri_detail", $opsi);
						}
					}
				} else {
					$data = $_POST['data'];
					foreach ($data as $i => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							select 
								idDetailSpp 
							from data_spp_sipd_detail 
							where idDetailSpp=%d 
								and tahun=%d
								and tahun_anggaran=%d
						", $v["idDetailSpp"], $v["tahun"], $v["tahun_anggaran"]));
						$opsi = array(
							"idSpp" => $v['idSpp'],
							"idKegiatan" => $v['idKegiatan'],
							"nilaiDetailSpp" => $v['nilaiDetailSpp'],
							"nilaiDisetujuiDetailSpp" => $v['nilaiDisetujuiDetailSpp'],
							"idRekening" => $v['idRekening'],
							"idBelanja" => $v['idBelanja'],
							"nominal" => $v['nominal'],
							"idDetailSpd" => $v['idDetailSpd'],
							"created_at" => $v['created_at'],
							"updated_at" => $v['updated_at'],
							"idSpd" => $v['idSpd'],
							"idDetailSpp" => $v['idDetailSpp'],
							"id_jadwal" => $v['id_jadwal'],
							"id_tahap" => $v['id_tahap'],
							"status_tahap" => $v['status_tahap'],
							"id_daerah" => $v['id_daerah'],
							"id_skpd" => $v['id_skpd'],
							"tahun_spp" => $v['tahun_spp'],
							"kode_rekening" => $v['kode_rekening'],
							"nama_rekening" => $v['nama_rekening'],
							"id_sub_kegiatan" => $v['id_sub_kegiatan'],
							"kode_sub_kegiatan" => $v['kode_sub_kegiatan'],
							"nama_sub_kegiatan" => $v['nama_sub_kegiatan'],
							"kode_kegiatan" => $v['kode_kegiatan'],
							"nama_kegiatan" => $v['nama_kegiatan'],
							"id_sub_skpd" => $v['id_sub_skpd'],
							"nama_sub_skpd" => $v['nama_sub_skpd'],
							"distribusi" => $v['distribusi'],
							"id_pegawai_kpa" => $v['id_pegawai_kpa'],
							"id_npd" => $v['id_npd'],
							"id_detail_npd" => $v['id_detail_npd'],
							"nilaiDetailSpd" => $v['nilaiDetailSpd'],
							"sisaDetailSpd" => $v['sisaDetailSpd'],
							"isUp" => $v['isUp'],
							"tahun" => $v['tahun'],
							"isPengajuanTu" => $v['isPengajuanTu'],
							"idSubKegiatan" => $v['idSubKegiatan'],
							"kodeSubKegiatan" => $v['kodeSubKegiatan'],
							"namaSubKegiatan" => $v['namaSubKegiatan'],
							"kodeRekening" => $v['kodeRekening'],
							"namaRekening" => $v['namaRekening'],
							"kodeKegiatan" => $v['kodeKegiatan'],
							"namaKegiatan" => $v['namaKegiatan'],
							"idSkpd" => $v['idSkpd'],
							"idDaerah" => $v['idDaerah'],
							"idProgram" => $v['idProgram'],
							"idSubSkpd" => $v['idSubSkpd'],
							"idPegawaiKpa" => $v['idPegawaiKpa'],
							"kodeSubSkpd" => $v['kodeSubSkpd'],
							"namaSubSkpd" => $v['namaSubSkpd'],
							"isVerifikasiSpp" => $v['isVerifikasiSpp'],
							"id_bidang" => $v['id_bidang'],
							"kode_program" => $v['kode_program'],
							"nama_program" => $v['nama_program'],
							"kode_bidang_urusan" => $v['kode_bidang_urusan'],
							"nama_bidang_urusan" => $v['nama_bidang_urusan'],
							"tipe" => $_POST['tipe'],
							"active" => 1,
							"update_at" => current_time('mysql'),
							"tahun_anggaran" => $_POST["tahun_anggaran"]
						);
						if (!empty($cek)) {
							//Update data spp ditable data_spp_sipd_detail
							$wpdb->update("data_spp_sipd_detail", $opsi, array(
								"idDetailSpp" => $v["idDetailSpp"],
								"tahun" => $v["tahun"],
								"idSubSkpd" => $_POST['idSkpd'],
								"tahun_anggaran" => $_POST["tahun_anggaran"],
								"tipe" => $_POST['tipe']
							));
						} else {
							//insert data spp ditable data_spp_sipd_detail
							$wpdb->insert("data_spp_sipd_detail", $opsi);
						}
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data SPM Detail dari SIPD Penatausahaan
	public function singkron_spm_detail()
	{
		global $wpdb;
		$ret = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil singkronisasi Detail SPM'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				foreach ($data['detail'] as $i => $v) {
					$cek_id = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_spm_sipd_detail 
						where id_skpd=%d
							and id_spm=%d
							and uraian=%s
							and jumlah=%s
							and kode_rekening=%s
							and tahun_anggaran=%d
					", $_POST['idSkpd'], $_POST['id_spm'], $v["uraian"], $v["jumlah"], $v["kode_rekening"], $_POST["tahun_anggaran"]));
					$opsi = array(
						"id_spm" => $_POST['id_spm'],
						"id_skpd" => $_POST['idSkpd'],
						"nomor_spd" => $data['dasar_pembayaran']["nomor_spd"],
						"tanggal_spd" => $data['dasar_pembayaran']["tanggal_spd"],
						"total_spd" => $data['dasar_pembayaran']["total_spd"],
						"jumlah" => $v["jumlah"],
						"kode_rekening" => $v["kode_rekening"],
						"uraian" => $v["uraian"],
						"bank_pihak_ketiga" => $data['header']["bank_pihak_ketiga"],
						"jabatan_pa_kpa" => $data['header']["jabatan_pa_kpa"],
						"keterangan_spm" => $data['header']["keterangan_spm"],
						"nama_daerah" => $data['header']["nama_daerah"],
						"nama_ibukota" => $data['header']["nama_ibukota"],
						"nama_pa_kpa" => $data['header']["nama_pa_kpa"],
						"nama_pihak_ketiga" => $data['header']["nama_pihak_ketiga"],
						"nama_rek_pihak_ketiga" => $data['header']["nama_rek_pihak_ketiga"],
						"nama_skpd" => $data['header']["nama_skpd"],
						"nama_sub_skpd" => $data['header']["nama_sub_skpd"],
						"nilai_spm" => $data['header']["nilai_spm"],
						"nip_pa_kpa" => $data['header']["nip_pa_kpa"],
						"no_rek_pihak_ketiga" => $data['header']["no_rek_pihak_ketiga"],
						"nomor_spm" => $data['header']["nomor_spm"],
						"nomor_spp" => $data['header']["nomor_spp"],
						"npwp_pihak_ketiga" => $data['header']["npwp_pihak_ketiga"],
						"tahun" => $data['header']["tahun"],
						"tanggal_spm" => $data['header']["tanggal_spm"],
						"tanggal_spp" => $data['header']["tanggal_spp"],
						"tipe" => $_POST['tipe'],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek_id)) {
						//Update data spp ditable data_spm_sipd_detail
						$wpdb->update("data_spm_sipd_detail", $opsi, array(
							"id" => $cek_id
						));
					} else {
						//insert data spp ditable data_spm_sipd_detail
						$wpdb->insert("data_spm_sipd_detail", $opsi);
					}
				}

				$wpdb->update("data_spm_sipd_detail_potongan", array('active' => 0), array(
					"id_skpd" => $_POST['idSkpd'],
					"id_spm" => $_POST['id_spm'],
					"tahun_anggaran" => $_POST["tahun_anggaran"]
				));
				foreach ($data['pajak_potongan'] as $i => $v) {
					$cek_id = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_spm_sipd_detail_potongan 
						where id_skpd=%d
							and id_spm=%d
							and tahun_anggaran=%d
							and id_pajak_potongan=%d
					", $_POST['idSkpd'], $_POST['id_spm'], $_POST["tahun_anggaran"], $v["id_pajak_potongan"]));
					$opsi = array(
						"id_spm" => $_POST['id_spm'],
						"id_skpd" => $_POST['idSkpd'],
						"id_billing" => $v["id_billing"],
						"id_pajak_potongan" => $v["id_pajak_potongan"],
						"nama_pajak_potongan" => $v["nama_pajak_potongan"],
						"nilai_spp_pajak_potongan" => $v["nilai_spp_pajak_potongan"],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek_id)) {
						//Update data spp ditable data_spm_sipd_detail_potongan
						$wpdb->update("data_spm_sipd_detail_potongan", $opsi, array(
							"id" => $cek_id
						));
					} else {
						//insert data spp ditable data_spm_sipd_detail_potongan
						$wpdb->insert("data_spm_sipd_detail_potongan", $opsi);
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data SPM dari SIPD Penatausahaan
	public function singkron_spm()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil singkronisasi SPM'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				if (
					empty($_POST['page'])
					|| $_POST['page'] == 1
				) {
					$wpdb->update("data_spm_sipd", array('active' => 0), array(
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"id_skpd" => $_POST['idSkpd'],
						"jenisSpm" => $_POST['tipe']
					));
				}
				foreach ($data as $i => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_spm_sipd 
						where idSpm=%d 
							and tahun_anggaran=%d
						", $v["idSpm"], $_POST["tahun_anggaran"]));
					$opsi = array(
						"idSpm" => $v["idSpm"],
						"idSpp" => $v["idSpp"],
						"created_at" => $v["created_at"],
						"updated_at" => $v["updated_at"],
						"idDetailSpm" => $v["idDetailSpm"],
						"id_skpd" => $v["id_skpd"],
						"id_jadwal" => $v["id_jadwal"],
						"id_tahap" => $v["id_tahap"],
						"status_tahap" => $v["status_tahap"],
						"nomorSpp" => $v["nomorSpp"],
						"nilaiSpp" => $v["nilaiSpp"],
						"tanggalSpp" => $v["tanggalSpp"],
						"keteranganSpp" => $v["keteranganSpp"],
						"idSubUnit" => $v["idSubUnit"],
						"nilaiDisetujuiSpp" => $v["nilaiDisetujuiSpp"],
						"tanggalDisetujuiSpp" => $v["tanggalDisetujuiSpp"],
						"jenisSpp" => $v["jenisSpp"],
						"verifikasiSpp" => $v["verifikasiSpp"],
						"keteranganVerifikasi" => $v["keteranganVerifikasi"],
						"idSpd" => $v["idSpd"],
						"idPengesahanSpj" => $v["idPengesahanSpj"],
						"kunciRekening" => $v["kunciRekening"],
						"alamatPenerimaSpp" => $v["alamatPenerimaSpp"],
						"bankPenerimaSpp" => $v["bankPenerimaSpp"],
						"nomorRekeningPenerimaSpp" => $v["nomorRekeningPenerimaSpp"],
						"npwpPenerimaSpp" => $v["npwpPenerimaSpp"],
						"jenisLs" => $v["jenisLs"],
						"isUploaded" => $v["isUploaded"],
						"tahunSpp" => $v["tahunSpp"],
						"idKontrak" => $v["idKontrak"],
						"idBA" => $v["idBA"],
						"isSpm" => $v["isSpm"],
						"statusPerubahan" => $v["statusPerubahan"],
						"isDraft" => $v["isDraft"],
						"isGaji" => $v["isGaji"],
						"is_sptjm" => $v["is_sptjm"],
						"tanggal_otorisasi" => $v["tanggal_otorisasi"],
						"is_otorisasi" => $v["is_otorisasi"],
						"bulan_gaji" => $v["bulan_gaji"],
						"id_pegawai_pptk" => $v["id_pegawai_pptk"],
						"nama_pegawai_pptk" => $v["nama_pegawai_pptk"],
						"nip_pegawai_pptk" => $v["nip_pegawai_pptk"],
						"kode_tahap" => $v["kode_tahap"],
						"is_tpp" => $v["is_tpp"],
						"bulan_tpp" => $v["bulan_tpp"],
						"id_pengajuan_tu" => $v["id_pengajuan_tu"],
						"nomor_pengajuan_tu" => $v["nomor_pengajuan_tu"],
						"nomorSpm" => $v["nomorSpm"],
						"tanggalSpm" => $v["tanggalSpm"],
						"keteranganSpm" => $v["keteranganSpm"],
						"verifikasiSpm" => $v["verifikasiSpm"],
						"tanggalVerifikasiSpm" => $v["tanggalVerifikasiSpm"],
						"jenisSpm" => $_POST['tipe'],
						"nilaiSpm" => $v["nilaiSpm"],
						"keteranganVerifikasiSpm" => $v["keteranganVerifikasiSpm"],
						"isOtorisasi" => $v["isOtorisasi"],
						"tanggalOtorisasi" => $v["tanggalOtorisasi"],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek)) {
						//Update data spm ditable data_spm_sipd
						$wpdb->update("data_spm_sipd", $opsi, array("id" => $cek));
					} else {
						//insert data spm ditable data_spm_sipd
						$wpdb->insert("data_spm_sipd", $opsi);
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data SPM Detail dari SIPD Penatausahaan
	public function singkron_sp2d_detail()
	{
		global $wpdb;
		$ret = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil singkronisasi Detail SP2D'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				foreach ($data['detail_belanja'] as $i => $v) {
					$cek_id = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_sp2d_sipd_detail 
						where id_skpd=%d
							and id_sp_2_d=%d
							and tahun_anggaran=%d
							and kode_rekening=%s
					", $_POST['idSkpd'], $_POST['id_sp_2_d'], $_POST["tahun_anggaran"], $v["kode_rekening"]));
					$opsi = array(
						"id_sp_2_d" => $_POST['id_sp_2_d'],
						"id_skpd" => $_POST['idSkpd'],
						"jumlah" => $v["jumlah"],
						"kode_rekening" => $v["kode_rekening"],
						"total_anggaran" => $v["total_anggaran"],
						"uraian" => $v["uraian"],
						"bank_pihak_ketiga" => $data['header']["bank_pihak_ketiga"],
						"jabatan_bud_kbud" => $data['header']["jabatan_bud_kbud"],
						"keterangan_sp2d" => $data['header']["keterangan_sp2d"],
						"nama_bank" => $data['header']["nama_bank"],
						"nama_bud_kbud" => $data['header']["nama_bud_kbud"],
						"nama_daerah" => $data['header']["nama_daerah"],
						"nama_ibukota" => $data['header']["nama_ibukota"],
						"nama_pihak_ketiga" => $data['header']["nama_pihak_ketiga"],
						"nama_rek_pihak_ketiga" => $data['header']["nama_rek_pihak_ketiga"],
						"nama_skpd" => $data['header']["nama_skpd"],
						"nama_sub_skpd" => $data['header']["nama_sub_skpd"],
						"nilai_sp2d" => $data['header']["nilai_sp2d"],
						"nip_bud_kbud" => $data['header']["nip_bud_kbud"],
						"no_rek_pihak_ketiga" => $data['header']["no_rek_pihak_ketiga"],
						"nomor_rekening" => $data['header']["nomor_rekening"],
						"nomor_sp_2_d" => $data['header']["nomor_sp_2_d"],
						"nomor_spm" => $data['header']["nomor_spm"],
						"npwp_pihak_ketiga" => $data['header']["npwp_pihak_ketiga"],
						"tahun" => $data['header']["tahun"],
						"tanggal_sp_2_d" => $data['header']["tanggal_sp_2_d"],
						"tanggal_spm" => $data['header']["tanggal_spm"],
						"tipe" => $_POST['tipe'],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek_id)) {
						//Update data spp ditable data_sp2d_sipd_detail
						$wpdb->update("data_sp2d_sipd_detail", $opsi, array(
							"id" => $cek_id
						));
					} else {
						//insert data spp ditable data_sp2d_sipd_detail
						$wpdb->insert("data_sp2d_sipd_detail", $opsi);
					}
				}

				$wpdb->update("data_sp2d_sipd_detail_potongan", array('active' => 0), array(
					"id_skpd" => $_POST['idSkpd'],
					"id_sp_2_d" => $_POST['id_sp_2_d'],
					"tahun_anggaran" => $_POST["tahun_anggaran"]
				));
				foreach ($data['pajak_potongan'] as $i => $v) {
					$cek_id = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_sp2d_sipd_detail_potongan 
						where id_skpd=%d
							and id_sp_2_d=%d
							and tahun_anggaran=%d
							and id_pajak_potongan=%d
					", $_POST['idSkpd'], $_POST['id_sp_2_d'], $_POST["tahun_anggaran"], $v["id_pajak_potongan"]));
					$opsi = array(
						"id_sp_2_d" => $_POST['id_sp_2_d'],
						"id_skpd" => $_POST['idSkpd'],
						"id_billing" => $v["id_billing"],
						"id_pajak_potongan" => $v["id_pajak_potongan"],
						"nama_pajak_potongan" => $v["nama_pajak_potongan"],
						"nilai_sp2d_pajak_potongan" => $v["nilai_sp2d_pajak_potongan"],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek_id)) {
						//Update data spp ditable data_sp2d_sipd_detail_potongan
						$wpdb->update("data_sp2d_sipd_detail_potongan", $opsi, array(
							"id" => $cek_id
						));
					} else {
						//insert data spp ditable data_sp2d_sipd_detail_potongan
						$wpdb->insert("data_sp2d_sipd_detail_potongan", $opsi);
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data SP2D dari SIPD Penatausahaan
	public function singkron_sp2d()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil singkronisasi SP2D'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				if (
					empty($_POST['page'])
					|| $_POST['page'] == 1
				) {
					$wpdb->update("data_sp2d_sipd_ri", array('active' => 0), array(
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"id_skpd" => $_POST['idSkpd'],
						"jenis_sp_2_d" => $_POST['tipe']
					));
				}
				foreach ($data as $i => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_sp2d_sipd_ri 
						where id_sp_2_d=%d 
							and tahun_anggaran=%d
					", $v['id_sp_2_d'], $_POST['tahun_anggaran']));
					$opsi = array(
						'bulan_gaji' => $v['bulan_gaji'],
						'bulan_tpp' => $v['bulan_tpp'],
						'created_at' => $v['created_at'],
						'created_by' => $v['created_by'],
						'deleted_at' => $v['deleted_at'],
						'deleted_by' => $v['deleted_by'],
						'id_bank' => $v['id_bank'],
						'id_daerah' => $v['id_daerah'],
						'id_jadwal' => $v['id_jadwal'],
						'id_pegawai_bud_kbud' => $v['id_pegawai_bud_kbud'],
						'id_rkud' => $v['id_rkud'],
						'id_skpd' => $v['id_skpd'],
						'id_sp_2_d' => $v['id_sp_2_d'],
						'id_spm' => $v['id_spm'],
						'id_sub_skpd' => $v['id_sub_skpd'],
						'id_sumber_dana' => $v['id_sumber_dana'],
						'id_tahap' => $v['id_tahap'],
						'id_unit' => $v['id_unit'],
						'is_gaji' => $v['is_gaji'],
						'is_kunci_rekening_sp_2_d' => $v['is_kunci_rekening_sp_2_d'],
						'is_pelimpahan' => $v['is_pelimpahan'],
						'is_status_perubahan' => $v['is_status_perubahan'],
						'is_tpp' => $v['is_tpp'],
						'is_transfer_sp_2_d' => $v['is_transfer_sp_2_d'],
						'is_verifikasi_sp_2_d' => $v['is_verifikasi_sp_2_d'],
						'jenis_gaji' => $v['jenis_gaji'],
						'jenis_ls_sp_2_d' => $v['jenis_ls_sp_2_d'],
						'jenis_rkud' => $v['jenis_rkud'],
						'jenis_sp_2_d' => $v['jenis_sp_2_d'],
						'jurnal_id' => $v['jurnal_id'],
						'keterangan_sp_2_d' => $v['keterangan_sp_2_d'],
						'keterangan_transfer_sp_2_d' => $v['keterangan_transfer_sp_2_d'],
						'keterangan_verifikasi_sp_2_d' => $v['keterangan_verifikasi_sp_2_d'],
						'kode_skpd' => $v['kode_skpd'],
						'kode_sub_skpd' => $v['kode_sub_skpd'],
						'kode_tahap' => $v['kode_tahap'],
						'metode' => $v['metode'],
						'nama_bank' => $v['nama_bank'],
						'nama_bud_kbud' => $v['nama_bud_kbud'],
						'nama_rek_bp_bpp' => $v['nama_rek_bp_bpp'],
						'nama_skpd' => $v['nama_skpd'],
						'nama_sub_skpd' => $v['nama_sub_skpd'],
						'nilai_materai_sp_2_d' => $v['nilai_materai_sp_2_d'],
						'nilai_sp_2_d' => $v['nilai_sp_2_d'],
						'nip_bud_kbud' => $v['nip_bud_kbud'],
						'no_rek_bp_bpp' => $v['no_rek_bp_bpp'],
						'nomor_jurnal' => $v['nomor_jurnal'],
						'nomor_sp_2_d' => $v['nomor_sp_2_d'],
						'nomor_spm' => $v['nomor_spm'],
						'status_aklap' => $v['status_aklap'],
						'status_perubahan_at' => $v['status_perubahan_at'],
						'status_perubahan_by' => $v['status_perubahan_by'],
						'status_tahap' => $v['status_tahap'],
						'tahun' => $v['tahun'],
						'tahun_gaji' => $v['tahun_gaji'],
						'tahun_tpp' => $v['tahun_tpp'],
						'tanggal_sp_2_d' => $v['tanggal_sp_2_d'],
						'tanggal_spm' => $v['tanggal_spm'],
						'transfer_sp_2_d_at' => $v['transfer_sp_2_d_at'],
						'transfer_sp_2_d_by' => $v['transfer_sp_2_d_by'],
						'updated_at' => $v['updated_at'],
						'updated_by' => $v['updated_by'],
						'verifikasi_sp_2_d_at' => $v['verifikasi_sp_2_d_at'],
						'verifikasi_sp_2_d_by' => $v['verifikasi_sp_2_d_by'],
						'active' => 1,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);
					if (!empty($cek)) {
						$wpdb->update('data_sp2d_sipd_ri', $opsi, array(
							'id' => $cek
						));
					} else {
						$wpdb->insert('data_sp2d_sipd_ri', $opsi);
						//insert data ke table data_sp2d_sipd_ri
					}
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Tidak ada parameter yang dikirim dari Chrome Extension!';
		}
		die(json_encode($ret));
	}

	//Import data SPD SIPD Penatausahaan
	public function singkron_detail_spd()
	{

		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil Singkron Detail SPD',
			'action' => $_POST['action']
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}

				//Insert atau update data spd
				$cek = $wpdb->get_var($wpdb->prepare("select idSpd from data_spd_sipd where idSpd=%d and tahun_anggaran=%d", $_POST["idSpd"], $_POST["tahun_anggaran"]));
				$opsi = array(
					"idSpd" => $_POST["idSpd"],
					"nomorSpd" => $_POST["nomorSpd"],
					"totalSpd" => $_POST["totalSpd"],
					"keteranganSpd" => $_POST["keteranganSpd"],
					"ketentuanLainnya" => $_POST["ketentuanLainnya"],
					"id_skpd" => $_POST["id_skpd"],
					"active" => 1,
					"tahun_anggaran" => $_POST["tahun_anggaran"],
					"created_at" => current_time('mysql')
				);
				if (!empty($cek)) {
					$wpdb->update("data_spd_sipd", $opsi, array("idSpd" => $data["idSpd"]));
				} else {
					$wpdb->insert("data_spd_sipd", $opsi);
				}
				//Insert atau update data detail spd
				foreach ($data as $k => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("select idDetailSpd from data_spd_sipd_detail where idDetailSpd=%d and tahun_anggaran=%d", $v["idDetailSpd"], $_POST['tahun_anggaran']));
					$opsi = array(
						'idDetailSpd' => $v["idDetailSpd"],
						'idSpd' => $v["idSpd"],
						"id_skpd" => $v["id_skpd"],
						"id_sub_skpd" => $v["id_sub_skpd"],
						"id_program" => $v["id_program"],
						"id_giat" => $v["id_giat"],
						"id_sub_giat" => $v["id_sub_giat"],
						"kode_standar_harga" => $v["kode_standar_harga"],
						"nama_standar_harga" => $v["nama_standar_harga"],
						"koefisien" => $v["koefisien"],
						"harga_satuan" => $v["harga_satuan"],
						"pajak" => $v["pajak"],
						"id_akun" => $v["id_akun"],
						"nilai" => $v["nilai"],
						"created_at" => current_time('mysql'),
						'active' => 1,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);
					if (!empty($cek)) {
						$wpdb->update('data_spd_sipd_detail', $opsi, array("idDetailSpd" => $v["idDetailSpd"]));
					} else {
						$wpdb->insert("data_spd_sipd_detail", $opsi);
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "API KEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Tidak ada parameter yang dikirim";
		}
		die(json_encode($ret));
	}

	//Import data NPD dari SIPD Penatausahaan
	public function singkron_npd()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil singkronisasi NPD'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				if (
					empty($_POST['page'])
					|| $_POST['page'] == 1
				) {
					$wpdb->update("data_npd_sipd", array('active' => 0), array(
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"id_skpd" => $_POST['idSkpd']
					));
				}

				foreach ($data as $i => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_npd_sipd 
						where id_npd=%d 
							and tahun_anggaran=%d
						", $v["id_npd"], $_POST["tahun_anggaran"]));
					$opsi = array(
						"id_npd" => $v["id_npd"],
						"nomor_npd" => $v["nomor_npd"],
						"id_daerah" => $v["id_daerah"],
						"id_unit" => $v["id_unit"],
						"id_skpd" => $v["id_skpd"],
						"id_sub_skpd" => $v["id_sub_skpd"],
						"nilai_npd" => $v["nilai_npd"],
						"nilai_npd_disetujui" => $v["nilai_npd_disetujui"],
						"tanggal_npd" => $v["tanggal_npd"],
						"tanggal_npd_selesai" => $v["tanggal_npd_selesai"],
						"keterangan_npd" => $v["keterangan_npd"],
						"is_verifikasi_npd" => $v["is_verifikasi_npd"],
						"verifikasi_npd_at" => $v["verifikasi_npd_at"],
						"verifikasi_npd_by" => $v["verifikasi_npd_by"],
						"nomor_verifikasi" => $v["nomor_verifikasi"],
						"is_npd_panjar" => $v["is_npd_panjar"],
						"kondisi_selesai" => $v["kondisi_selesai"],
						"selesai_at" => $v["selesai_at"],
						"selesai_by" => $v["selesai_by"],
						"nomor_selesai" => $v["nomor_selesai"],
						"nilai_pengembalian" => $v["nilai_pengembalian"],
						"nilai_kurang_bayar" => $v["nilai_kurang_bayar"],
						"nomor_kurang_lebih" => $v["nomor_kurang_lebih"],
						"kurang_lebih_at" => $v["kurang_lebih_at"],
						"kurang_lebih_by" => $v["kurang_lebih_by"],
						"is_validasi_npd" => $v["is_validasi_npd"],
						"validasi_npd_at" => $v["validasi_npd_at"],
						"validasi_npd_by" => $v["validasi_npd_by"],
						"is_tbp" => $v["is_tbp"],
						"tbp_at" => $v["tbp_at"],
						"tbp_by" => $v["tbp_by"],
						"id_jadwal" => $v["id_jadwal"],
						"id_tahap" => $v["id_tahap"],
						"status_tahap" => $v["status_tahap"],
						"kode_tahap" => $v["kode_tahap"],
						"kode_skpd" => $v["kode_skpd"],
						"nama_skpd" => $v["nama_skpd"],
						"kode_sub_skpd" => $v["kode_sub_skpd"],
						"nama_sub_skpd" => $v["nama_sub_skpd"],
						"total_pertanggungjawaban" => $v["total_pertanggungjawaban"],
						"created_by" => $v["created_by"],
						"created_at" => $v["created_at"],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek)) {
						//Update data spm ditable data_spm_sipd
						$wpdb->update("data_npd_sipd", $opsi, array("id" => $cek));
					} else {
						//insert data spm ditable data_spm_sipd
						$wpdb->insert("data_npd_sipd", $opsi);
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data NPD Detail dari SIPD Penatausahaan
	public function singkron_npd_detail()
	{
		global $wpdb;
		$ret = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil singkronisasi Detail NPD'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				foreach ($data as $i => $v) {
					$wpdb->update("data_npd_sipd_detail", array('active' => 0), array(
						"id_skpd" => $v['idSkpd'],
						"id_npd" => $v['id_npd'],
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					));
					$cek_id = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_npd_sipd_detail 
						where id_skpd=%d
							and id_npd=%d
							and id_giat=%s
							and id_sub_giat=%s
							and nomor_npd=%s
							and tahun_anggaran=%d
					", $v['idSkpd'], $v['id_npd'], $v["id_giat"], $v["id_sub_giat"], $v["nomor_npd"], $_POST["tahun_anggaran"]));
					$opsi = array(
						"id_npd" => $v['id_npd'],
						"id_skpd" => $v['idSkpd'],
						"id_daerah" => $v["id_daerah"],
						"nama_daerah" => $v["nama_daerah"],
						"nomor_npd" => $v["nomor_npd"],
						"tanggal_npd" => $v["tanggal_npd"],
						"kondisi_panjar" => $v["kondisi_panjar"],
						"nama_skpd" => $v["nama_skpd"],
						"nama_sub_skpd" => $v["nama_sub_skpd"],
						"nama_pptk" => $v["nama_pptk"],
						"nip_pptk" => $v["nip_pptk"],
						"jabatan_pptk" => $v["jabatan_pptk"],
						"nama_pa_kpa" => $v["nama_pa_kpa"],
						"nip_pa_kpa" => $v["nip_pa_kpa"],
						"jabatan_pa_kpa" => $v["jabatan_pa_kpa"],
						"nomor_dpa" => $v["nomor_dpa"],
						"id_program" => $v["id_program"],
						"kode_program" => $v["kode_program"],
						"nama_program" => $v["nama_program"],
						"id_giat" => $v["id_giat"],
						"kode_giat" => $v["kode_giat"],
						"nama_giat" => $v["nama_giat"],
						"id_sub_giat" => $v["id_sub_giat"],
						"kode_sub_giat" => $v["kode_sub_giat"],
						"nama_sub_giat" => $v["nama_sub_giat"],
						"keterangan_npd" => $v["keterangan_npd"],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek_id)) {
						//Update data npd ditable data_tbp_sipd_detail
						$wpdb->update("data_npd_sipd_detail", $opsi, array(
							"id" => $cek_id
						));
					} else {
						//insert data npd ditable data_tbp_sipd_detail
						$wpdb->insert("data_npd_sipd_detail", $opsi);
					}

					foreach ($v['details'] as $i => $d) {
						$cek_id = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_npd_sipd_detail_rekening 
						where id_skpd=%d
							and id_npd=%d
							and tahun_anggaran=%d
							and uraian=%d
							and kode_rekening=%d
							and anggaran=%d
					", $v['idSkpd'], $v['id_npd'], $_POST["tahun_anggaran"], $d["kode_rekening"], $d["uraian"], $d["anggaran"]));
						$opsi = array(
							"id_npd" => $v['id_npd'],
							"id_skpd" => $v['idSkpd'],
							"kode_rekening" => $d["kode_rekening"],
							"uraian" => $d["uraian"],
							"anggaran" => $d["anggaran"],
							"sisa_anggaran" => $d["sisa_anggaran"],
							"rencana_penarikan" => $d["rencana_penarikan"],
							"active" => 1,
							"update_at" => current_time('mysql'),
							"tahun_anggaran" => $_POST["tahun_anggaran"]
						);
						if (!empty($cek_id)) {
							//Update data NPD ditable data_npd_sipd_detail_rekening
							$wpdb->update("data_npd_sipd_detail_rekening", $opsi, array(
								"id" => $cek_id
							));
						} else {
							//insert data NPD ditable data_npd_sipd_detail_rekening
							$wpdb->insert("data_npd_sipd_detail_rekening", $opsi);
						}
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data LRA AKLAP dari SIPD Penatausahaan
	public function singkron_aklap_lra()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil singkronisasi AKLAP LRA',
			'action' => $_POST['action'],
			'id_skpd' => $_POST['id_skpd']
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				if (
					empty($_POST['page'])
					|| $_POST['page'] == 1
				) {
					$wpdb->update("aklap_lra_sipd", array('active' => 0), array(
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"id_skpd" => $_POST['id_skpd']
					));
				}

				foreach ($data as $i => $v) {
					$cek_id = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from aklap_lra_sipd 
						where kode_rekening=%d 
							and id_daerah=%d
							and id_skpd=%d
							and level=%d
							and tahun_anggaran=%d
						", $v["kode_rekening"], $_POST["id_daerah"], $v["id_skpd"], $v["level"], $_POST["tahun_anggaran"]));
					$opsi = array(
						"id_daerah" => $_POST["id_daerah"],
						"id_skpd" => $v["id_skpd"],
						"kode_rekening" => $v["kode_rekening"],
						"level" => $v["level"],
						"nama_rekening" => $v["nama_rekening"],
						"nominal" => $v["nominal"],
						"presentase" => $v["presentase"],
						"previous_realisasi" => $v["previous_realisasi"],
						"realisasi" => $v["realisasi"],
						"mulai_tgl" => $_POST["mulai_tgl"],
						"sampai_tgl" => $_POST["sampai_tgl"],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek_id)) {
						//Update data spm ditable data_spm_sipd
						$wpdb->update("aklap_lra_sipd", $opsi, array("id" => $cek_id));
					} else {
						//insert data spm ditable data_spm_sipd
						$wpdb->insert("aklap_lra_sipd", $opsi);
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data Buku Jurnal Aklap SIPD Penatausahaan
	public function singkron_jurnal()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil Singkron Buku Jurnal',
			'id_skpd' => $_POST['id_skpd'],
			'action' => $_POST['action']
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				if (
					empty($_POST['page'])
					|| $_POST['page'] == 1
				) {
					$wpdb->update("data_buku_jurnal_sipd", array('active' => 0), array(
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"id_skpd" => $_POST['id_skpd']
					));
				}
				foreach ($data as $i => $j) {
					//Insert atau update data spd
					$cek = $wpdb->get_var($wpdb->prepare("select id_jurnal from data_buku_jurnal_sipd where id_jurnal=%d and id_skpd=%d and tahun_anggaran=%d", $v["id_jurnal"], $_POST['id_skpd'], $_POST["tahun_anggaran"]));
					$opsi = array(
						"id_jurnal" => $j["id_jurnal"],
						"tanggal_jurnal" => $j["tanggal_jurnal"],
						"id_skpd" => $_POST['id_skpd'],
						"nama_skpd" => $j["nama_skpd"],
						"nomor_jurnal" => $j["nomor_jurnal"],
						"dokumen_sumber" => $j["dokumen_sumber"],
						"active" => 1,
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"update_at" => current_time('mysql')
					);
					if (!empty($cek)) {
						$wpdb->update("data_buku_jurnal_sipd", $opsi, array("id_jurnal" => $data["id_jurnal"], "id_skpd" => $_POST['id_skpd']));
					} else {
						$wpdb->insert("data_buku_jurnal_sipd", $opsi);
					}
					//Insert atau update data detail spd
					foreach ($j['detail_jurnal'] as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("select id_detail from data_buku_jurnal_sipd_detail where id_detail=%d and tahun_anggaran=%d", $v["id_detail"], $_POST['tahun_anggaran']));
						$opsi = array(
							"id_jurnal" => $v["id_jurnal"],
							"id_detail" => $v["id_detail"],
							"account_id" => $v["account_id"],
							"amount" => $v["amount"],
							"kode_rekening" => $v["kode_rekening"],
							"nama_rekening" => $v["nama_rekening"],
							"position" => $v["position"],
							"update_at" => current_time('mysql'),
							'active' => 1,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_buku_jurnal_sipd_detail', $opsi, array("id_jurnal" => $v["id_jurnal"], "id_detail" => $v["id_detail"]));
						} else {
							$wpdb->insert("data_buku_jurnal_sipd_detail", $opsi);
						}
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "API KEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Tidak ada parameter yang dikirim";
		}
		die(json_encode($ret));
	}

	//Import data LPJ dari SIPD Penatausahaan
	public function singkron_lpj_bpp()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil singkronisasi LPJ BPP'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				if (
					empty($_POST['page'])
					|| $_POST['page'] == 1
				) {
					$wpdb->update("data_lpj_bpp_sipd", array('active' => 0), array(
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"id_skpd" => $_POST['idSkpd']
					));
				}

				foreach ($data as $i => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_lpj_bpp_sipd 
						where id_lpj_bpp=%d 
							and tahun_anggaran=%d
						", $v["id_lpj"], $_POST["tahun_anggaran"]));
					$opsi = array(
						"id_lpj_bpp" => $v["id_lpj_bpp"],
						"nomor_lpj_bpp" => $v["nomor_lpj_bpp"],
						"id_daerah" => $v["id_daerah"],
						"id_unit" => $v["id_unit"],
						"id_skpd" => $v["id_skpd"],
						"id_sub_skpd" => $v["id_sub_skpd"],
						"nilai_lpj_bpp" => $v["nilai_lpj_bpp"],
						"tanggal_lpj_bpp" => $v["tanggal_lpj_bpp"],
						"jenis_lpj_bpp" => $v["jenis_lpj_bpp"],
						"id_pegawai_pa_kpa" => $v["id_pegawai_pa_kpa"],
						"is_verifikasi_lpj_bpp" => $v["is_verifikasi_lpj_bpp"],
						"verifikasi_lpj_bpp_at" => $v["verifikasi_lpj_bpp_at"],
						"verifikasi_lpj_bpp_by" => $v["verifikasi_lpj_bpp_by"],
						"is_val_i_dasi_lpj_bpp" => $v["is_val_i_dasi_lpj_bpp"],
						"val_i_dasi_lpj_bpp_at" => $v["val_i_dasi_lpj_bpp_at"],
						"val_i_dasi_lpj_bpp_by" => $v["val_i_dasi_lpj_bpp_by"],
						"is_spp_gu" => $v["is_spp_gu"],
						"spp_gu_at" => $v["spp_gu_at"],
						"spp_gu_by" => $v["spp_gu_by"],
						"id_jadwal" => $v["id_jadwal"],
						"id_tahap" => $v["id_tahap"],
						"status_tahap" => $v["status_tahap"],
						"kode_tahap" => $v["kode_tahap"],
						"kode_skpd" => $v["kode_skpd"],
						"nama_skpd" => $v["nama_skpd"],
						"kode_sub_skpd" => $v["kode_sub_skpd"],
						"nama_sub_skpd" => $v["nama_sub_skpd"],
						"created_by" => $v["created_by"],
						"created_at" => $v["created_at"],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek)) {
						//Update data spm ditable data_spm_sipd
						$wpdb->update("data_lpj_bpp_sipd", $opsi, array("id" => $cek));
					} else {
						//insert data spm ditable data_spm_sipd
						$wpdb->insert("data_lpj_bpp_sipd", $opsi);
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data LPJ Detail dari SIPD Penatausahaan
	public function singkron_lpj_detail()
	{
		global $wpdb;
		$ret = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil singkronisasi Detail LPJ'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				foreach ($data as $i => $v) {
					$wpdb->update("data_lpj_sipd_detail", array('active' => 0), array(
						"id_skpd" => $v['idSkpd'],
						"id_lpj" => $v['id_lpj'],
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					));
					$cek_id = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_lpj_sipd_detail 
						where id_skpd=%d
							and id_lpj=%d							
							and nomor_lpj=%s
							and tahun_anggaran=%d
					", $v['idSkpd'], $v['id_lpj'], $v["nomor_lpj"], $_POST["tahun_anggaran"]));
					$opsi = array(
						"id_lpj" => $v['id_lpj'],
						"id_skpd" => $v['idSkpd'],
						"id_daerah" => $v["id_daerah"],
						"tipe" => $v["tipe"],
						"nama_daerah" => $v["nama_daerah"],
						"nomor_lpj" => $v["nomor_lpj"],
						"tanggal_lpj" => $v["tanggal_lpj"],
						"up_awal" => $v["up_awal"],
						"penggunaan_up" => $v["penggunaan_up"],
						"up_akhir" => $v["up_akhir"],
						"nama_skpd" => $v["nama_skpd"],
						"nama_sub_skpd" => $v["nama_sub_skpd"],
						"nama_bp_bpp" => $v["nama_bp_bpp"],
						"nip_bp_bpp" => $v["nip_bp_bpp"],
						"jabatan_bp_bpp" => $v["jabatan_bp_bpp"],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek_id)) {
						//Update data npd ditable data_tbp_sipd_detail
						$wpdb->update("data_lpj_sipd_detail", $opsi, array(
							"id" => $cek_id
						));
					} else {
						//insert data npd ditable data_tbp_sipd_detail
						$wpdb->insert("data_lpj_sipd_detail", $opsi);
					}

					foreach ($v['detail'] as $i => $d) {
						$cek_id = $wpdb->get_var($wpdb->prepare("
							select 
								id 
							from data_lpj_sipd_detail_rekening 
							where id_skpd=%d
								and id_lpj=%d
								and tahun_anggaran=%d
								and uraian=%d
								and kode_rekening=%d
								and jumlah_anggaran=%d
						", $v['idSkpd'], $v['id_lpj'], $_POST["tahun_anggaran"], $d["kode_rekening"], $d["uraian"], $d["jumlah_anggaran"]));
						$opsi = array(
							"id_lpj" => $v['id_lpj'],
							"id_skpd" => $v['idSkpd'],
							"kode_rekening" => $d["kode_rekening"],
							"uraian" => $d["uraian"],
							"jumlah_anggaran" => $d["jumlah_anggaran"],
							"belanja_periode_ini" => $d["belanja_periode_ini"],
							"akumulasi_belanja" => $d["akumulasi_belanja"],
							"sisa_anggaran" => $d["sisa_anggaran"],
							"active" => 1,
							"update_at" => current_time('mysql'),
							"tahun_anggaran" => $_POST["tahun_anggaran"]
						);
						if (!empty($cek_id)) {
							//Update data NPD ditable data_npd_sipd_detail_rekening
							$wpdb->update("data_lpj_sipd_detail_rekening", $opsi, array(
								"id" => $cek_id
							));
						} else {
							//insert data NPD ditable data_npd_sipd_detail_rekening
							$wpdb->insert("data_lpj_sipd_detail_rekening", $opsi);
						}
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data TBP dari SIPD Penatausahaan
	public function singkron_tbp()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil singkronisasi TBP'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				if (
					empty($_POST['page'])
					|| $_POST['page'] == 1
				) {
					$wpdb->update("data_tbp_sipd", array('active' => 0), array(
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"id_skpd" => $_POST['idSkpd'],
						"jenis_tbp" => $_POST['jenis']
					));
				}

				foreach ($data as $i => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_tbp_sipd 
						where id_tbp=%d 
							and tahun_anggaran=%d
						", $v["id_tbp"], $_POST["tahun_anggaran"]));
					$opsi = array(
						"id_tbp" => $v["id_tbp"],
						"id_sp2d_distribusi" => $v["id_sp2d_distribusi"],
						"id_sp2d" => $v["id_sp2d"],
						"nomor_tbp" => $v["nomor_tbp"],
						"id_daerah" => $v["id_daerah"],
						"id_unit" => $v["id_unit"],
						"id_skpd" => $v["id_skpd"],
						"id_sub_skpd" => $v["id_sub_skpd"],
						"nilai_tbp" => $v["nilai_tbp"],
						"tanggal_tbp" => $v["tanggal_tbp"],
						"keterangan_tbp" => $v["keterangan_tbp"],
						"nilai_materai_tbp" => $v["nilai_materai_tbp"],
						"nomor_kwitansi" => $v["nomor_kwitansi"],
						"id_pegawai_pa_kpa" => $v["id_pegawai_pa_kpa"],
						"jenis_tbp" => $v["jenis_tbp"],
						"jenis_ls_tbp" => $v["jenis_ls_tbp"],
						"is_kunci_rekening_tbp" => $v["is_kunci_rekening_tbp"],
						"is_panjar" => $v["is_panjar"],
						"is_lpj" => $v["is_lpj"],
						"id_lpj" => $v["id_lpj"],
						"id_npd" => $v["id_npd"],
						"is_rekanan_upload" => $v["is_rekanan_upload"],
						"status_aklap" => $v["status_aklap"],
						"nomor_jurnal" => $v["nomor_jurnal"],
						"jurnal_id" => $v["jurnal_id"],
						"metode" => $v["metode"],
						"id_jadwal" => $v["id_jadwal"],
						"id_tahap" => $v["id_tahap"],
						"status_tahap" => $v["status_tahap"],
						"kode_tahap" => $v["kode_tahap"],
						"kode_skpd" => $v["kode_skpd"],
						"nama_skpd" => $v["nama_skpd"],
						"kode_sub_skpd" => $v["kode_sub_skpd"],
						"nama_sub_skpd" => $v["nama_sub_skpd"],
						"total_pertanggungjawaban" => $v["total_pertanggungjawaban"],
						"created_by" => $v["created_by"],
						"created_at" => $v["created_at"],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek)) {
						//Update data spm ditable data_spm_sipd
						$wpdb->update("data_tbp_sipd", $opsi, array("id" => $cek));
					} else {
						//insert data spm ditable data_spm_sipd
						$wpdb->insert("data_tbp_sipd", $opsi);
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data TBP Detail dari SIPD Penatausahaan
	public function singkron_tbp_detail()
	{
		global $wpdb;
		$ret = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil singkronisasi Detail TBP'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$data = $_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				foreach ($data as $i => $v) {
					$wpdb->update("data_tbp_sipd_detail", array('active' => 0), array(
						"id_skpd" => $v['idSkpd'],
						"id_tbp" => $v['id_tbp'],
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					));
					$cek_id = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_tbp_sipd_detail 
						where id_skpd=%d
							and id_tbp=%d
							and nomor_tbp=%s
							and nilai_tbp=%s
							and tahun_anggaran=%d
					", $v['idSkpd'], $v['id_tbp'], $v["nomor_tbp"], $v["nilai_tbp"], $_POST["tahun_anggaran"]));
					$opsi = array(
						"id_tbp" => $v['id_tbp'],
						"id_skpd" => $v['idSkpd'],
						"nama_daerah" => $v["nama_daerah"],
						"nama_skpd" => $v["nama_skpd"],
						"nomor_tbp" => $v["nomor_tbp"],
						"nilai_tbp" => $v["nilai_tbp"],
						"nama_tujuan" => $v["nama_tujuan"],
						"alamat_perusahaan" => $v["alamat_perusahaan"],
						"npwp" => $v["npwp"],
						"nomor_rekening" => $v["nomor_rekening"],
						"nama_rekening" => $v["nama_rekening"],
						"nama_bank" => $v["nama_bank"],
						"keterangan_tbp" => $v["keterangan_tbp"],
						"jenis_transaksi" => $v["jenis_transaksi"],
						"nomor_npd" => $v["nomor_npd"],
						"jenis_panjar" => $v["jenis_panjar"],
						"tanggal_tbp" => $v["tanggal_tbp"],
						"nama_pa_kpa" => $v["nama_pa_kpa"],
						"nip_pa_kpa" => $v["nip_pa_kpa"],
						"jabatan_pa_kpa" => $v["jabatan_pa_kpa"],
						"nama_bp_bpp" => $v["nama_bp_bpp"],
						"nip_bp_bpp" => $v["nip_bp_bpp"],
						"jabatan_bp_bpp" => $v["jabatan_bp_bpp"],
						"pajak_potongan" => $v["pajak_potongan"],
						"jenis_tbp" => $v['jenis'],
						"jenis" => $v['jenis'],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek_id)) {
						//Update data tbp ditable data_tbp_sipd_detail
						$wpdb->update("data_tbp_sipd_detail", $opsi, array(
							"id" => $cek_id
						));
					} else {
						//insert data tbp ditable data_tbp_sipd_detail
						$wpdb->insert("data_tbp_sipd_detail", $opsi);
					}

					foreach ($v['detail'] as $i => $r) {
						$cek_id = $wpdb->get_var($wpdb->prepare("
							select 
								id 
							from data_tbp_sipd_detail_rekening 
							where id_skpd=%d
								and id_tbp=%d
								and tahun_anggaran=%d
								and uraian=%d
								and kode_rekening=%d
								and jumlah=%d
						", $v['idSkpd'], $v['id_tbp'], $_POST["tahun_anggaran"], $r["kode_rekening"], $r["uraian"], $r["jumlah"]));
						$opsi = array(
							"id_tbp" => $v['id_tbp'],
							"id_skpd" => $v['idSkpd'],
							"kode_rekening" => $r["kode_rekening"],
							"uraian" => $r["uraian"],
							"jumlah" => $r["jumlah"],
							"active" => 1,
							"update_at" => current_time('mysql'),
							"tahun_anggaran" => $_POST["tahun_anggaran"]
						);
						if (!empty($cek_id)) {
							//Update data spp ditable data_tbp_sipd_detail_rekening
							$wpdb->update("data_tbp_sipd_detail_rekening", $opsi, array(
								"id" => $cek_id
							));
						} else {
							//insert data spp ditable data_tbp_sipd_detail_rekening
							$wpdb->insert("data_tbp_sipd_detail_rekening", $opsi);
						}
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data STBP dari SIPD Penatausahaan
	public function singkron_stbp()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil singkronisasi STBP'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				if (
					empty($_POST['page'])
					|| $_POST['page'] == 1
				) {
					$wpdb->update("data_stbp_sipd", array('active' => 0), array(
						"tahun_anggaran" => $_POST["tahun_anggaran"],
						"id_skpd" => $_POST['idSkpd']
					));
				}
				foreach ($data as $i => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_stbp_sipd 
						where id_stbp=%d 
							and tahun_anggaran=%d
						", $v["id_stbp"], $_POST["tahun_anggaran"]));
					$opsi = array(
						"id_stbp" => $v["id_stbp"],
						"nomor_stbp" => $v["nomor_stbp"],
						"no_rekening" => $v["no_rekening"],
						"metode_penyetoran" => $v["metode_penyetoran"],
						"nilai_stbp" => $v["nilai_stbp"],
						"keterangan_stbp" => $v["keterangan_stbp"],
						"is_verifikasi_stbp" => $v["is_verifikasi_stbp"],
						"is_otorisasi_stbp" => $v["is_otorisasi_stbp"],
						"is_validasi_stbp" => $v["is_validasi_stbp"],
						"tanggal_stbp" => $v["tanggal_stbp"],
						"id_daerah" => $v["id_daerah"],
						"id_unit" => $v["id_unit"],
						"id_skpd" => $v["id_skpd"],
						"id_sub_skpd" => $v["id_sub_skpd"],
						"is_sts" => $v["is_sts"],
						"status" => $v["status"],
						"created_at" => $v["created_at"],
						"active" => 1,
						"update_at" => current_time('mysql'),
						"tahun_anggaran" => $_POST["tahun_anggaran"]
					);
					if (!empty($cek)) {
						//Update data spm ditable data_spm_sipd
						$wpdb->update("data_stbp_sipd", $opsi, array("id" => $cek));
					} else {
						//insert data spm ditable data_spm_sipd
						$wpdb->insert("data_stbp_sipd", $opsi);
					}
				}
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	//Import data STBP Detail dari SIPD Penatausahaan
	public function singkron_stbp_detail()
	{
		global $wpdb;
		$ret = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil singkronisasi Detail STBP'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
					$data = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
				} else {
					$data = $_POST['data'];
				}
				// print_r($data);exit;
				// foreach ($data as $i => $v) {					
				// print_r($data['data_detail'][0]['id_rekening']);exit;
				$cek_id = $wpdb->get_var($wpdb->prepare("
						select 
							id 
						from data_stbp_sipd_detail 
						where id_skpd=%d
							and id_stbp=%d
							and uraian=%s
							and nilai_stbp=%s
							and kode_rekening=%s
							and tahun_anggaran=%d
					", $_POST['idSkpd'], $_POST['id_stbp'], $data["uraian"], $data["nilai_stbp"], $data["kode_rekening"], $_POST["tahun_anggaran"]));
				$opsi = array(
					"id_stbp" => $_POST['id_stbp'],
					"id_skpd" => $_POST['idSkpd'],
					"nama_penyetor" => $data['nama_penyetor'],
					"metode_input" => $data['metode_input'],
					"nomor_stbp" => $data['nomor_stbp'],
					"tanggal_stbp" => $data['tanggal_stbp'],
					"id_bank" => $data['id_bank'],
					"nama_bank" => $data['nama_bank'],
					"no_rekening" => $data['no_rekening'],
					"nilai_stbp" => $data['nilai_stbp'],
					"keterangan_stbp" => $data['keterangan_stbp'],
					"created_by" => $data['created_by'],
					"bendahara_penerimaan_nama" => $data['bendahara_penerimaan_nama'],
					"bendahara_penerimaan_nip" => $data['bendahara_penerimaan_nip'],
					"nama_skpd" => $data['nama_skpd'],
					"id_unit" => $data['id_unit'],
					"id_skpd" => $data['id_skpd'],
					"id_sub_skpd" => $data['id_sub_skpd'],
					"nama_daerah" => $data['nama_daerah'],
					"id_rekening" => $data['data_detail'][0]['id_rekening'],
					"kode_rekening" => $data['data_detail'][0]['kode_rekening'],
					"uraian" => $data['data_detail'][0]['uraian'],
					"nilai" => $data['data_detail'][0]['nilai'],
					"active" => 1,
					"update_at" => current_time('mysql'),
					"tahun_anggaran" => $_POST["tahun_anggaran"]
				);
				if (!empty($cek_id)) {
					//Update data spp ditable data_spm_sipd_detail
					$wpdb->update("data_stbp_sipd_detail", $opsi, array(
						"id" => $cek_id
					));
				} else {
					//insert data spp ditable data_spm_sipd_detail
					$wpdb->insert("data_stbp_sipd_detail", $opsi);
				}
				// }
			} else {
				$ret["status"] = "error";
				$ret["message"] = "APIKEY tidak sesuai";
			}
		} else {
			$ret["status"] = "error";
			$ret["message"] = "Gagal, Tidak ada parameter yang dikirim dari Chrome Extension";
		}
		die(json_encode($ret));
	}

	function singkron_anggaran_kas()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil Singkron Anggaran Kas',
			'action'	=> $_POST['action']
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (
					!empty($_POST['type'])
					|| (
						!empty($_POST['kode_sbl']) && $_POST['type'] == 'belanja'
					)
				) {
					if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
						$_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					}
					$wpdb->update('data_anggaran_kas', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'type' => $_POST['type'],
						'id_unit' => $_POST['id_skpd'],
						'kode_sbl' => $_POST['kode_sbl']
					));
					if (!empty($_POST['data'])) {
						$data = $_POST['data'];
						foreach ($data as $k => $v) {
							if (empty($v['id_akun'])) {
								continue;
							}
							$cek = $wpdb->get_var($wpdb->prepare("
								SELECT 
									id 
								from data_anggaran_kas 
								where tahun_anggaran=%d 
									AND kode_sbl=%s
									AND id_unit=%d 
									AND type=%s 
									AND id_akun=%d
							", $_POST['tahun_anggaran'], $_POST['kode_sbl'], $_POST['id_skpd'], $_POST['type'], $v['id_akun']));
							$opsi = array(
								'bulan_1' => $v['bulan_1'],
								'bulan_2' => $v['bulan_2'],
								'bulan_3' => $v['bulan_3'],
								'bulan_4' => $v['bulan_4'],
								'bulan_5' => $v['bulan_5'],
								'bulan_6' => $v['bulan_6'],
								'bulan_7' => $v['bulan_7'],
								'bulan_8' => $v['bulan_8'],
								'bulan_9' => $v['bulan_9'],
								'bulan_10' => $v['bulan_10'],
								'bulan_11' => $v['bulan_11'],
								'bulan_12' => $v['bulan_12'],
								'id_akun' => $v['id_akun'],
								'id_bidang_urusan' => $v['id_bidang_urusan'],
								'id_daerah' => $v['id_daerah'],
								'id_giat' => $v['id_giat'],
								'id_program' => $v['id_program'],
								'id_skpd' => $v['id_skpd'],
								'id_sub_giat' => $v['id_sub_giat'],
								'id_sub_skpd' => $v['id_sub_skpd'],
								'id_unit' => $_POST['id_skpd'],
								'kode_akun' => $v['kode_akun'],
								'nama_akun' => $v['nama_akun'],
								'selisih' => $v['selisih'],
								'tahun' => $v['tahun'],
								'total_akb' => $v['total_akb'],
								'total_rincian' => $v['total_rincian'],
								'active' => 1,
								'kode_sbl' => $_POST['kode_sbl'],
								'kode_sbl_sub_keg' => $_POST['kode_sbl_sub_keg'],
								'type' => $_POST['type'],
								'tahun_anggaran' => $_POST['tahun_anggaran'],
								'updated_at' => current_time('mysql')
							);

							if (!empty($cek)) {
								$wpdb->update('data_anggaran_kas', $opsi, array('id' => $cek));
							} else {
								$wpdb->insert('data_anggaran_kas', $opsi);
							}
						}
					}
					if (
						get_option('_crb_singkron_simda') == 1
						&& get_option('_crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
					) {
						$debug = false;
						if (get_option('_crb_singkron_simda_debug') == 1) {
							$debug = true;
						}
						$this->simda->singkronSimdaKas(array(
							'return' => $debug
						));
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	function singkron_realisasi_dashboard()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil backup data realisasi APBD',
			'action'	=> $_POST['action']
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (
					!empty($_POST['type'])
					|| (
						//!empty($_POST['kode_sbl']) && $_POST['type'] == 'belanja'
						!empty($_POST['kode_sbl'])
					)
				) {
					if (!empty($_POST['sumber']) && $_POST['sumber'] == 'ri') {
						$_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					}
					$wpdb->update('data_realisasi_akun_sipd', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'id_unit' => $_POST['id_skpd'],
						'kode_sbl' => $_POST['kode_sbl'],
						'type' => $_POST['type']
					));
					if (!empty($_POST['data'])) {
						$data = $_POST['data'];
						foreach ($data as $k => $v) {
							if (empty($v['kode_akun'])) {
								continue;
							}
							$cek = $wpdb->get_var($wpdb->prepare("
								SELECT 
									id 
								from data_realisasi_akun_sipd 
								where tahun_anggaran=%d 									
									AND kode_sbl=%s
									AND id_unit=%d 									
									AND kode_akun=%d
									AND type=%d
							", $_POST['tahun_anggaran'], $_POST['kode_sbl'], $_POST['id_skpd'], $v['kode_akun'], $_POST['type']));
							$opsi = array(
								'id_unit' => $_POST['id_skpd'],
								'id_skpd' => $v['id_skpd'],
								'id_sub_skpd' => $v['id_sub_skpd'],
								'id_program' => $v['id_program'],
								'id_giat' => $v['id_giat'],
								'id_sub_giat' => $v['id_sub_giat'],
								'id_daerah' => $v['id_daerah'],
								'id_akun' => $v['id_akun'],
								'kode_akun' => $v['kode_akun'],
								'nama_akun' => $v['nama_akun'],
								'nilai' => $v['nilai'],
								'realisasi' => $v['realisasi'],
								'tahun' => $v['tahun'],
								'active' => 1,
								'kode_sbl' => $_POST['kode_sbl'],
								'type' => $_POST['type'],
								'tahun_anggaran' => $_POST['tahun_anggaran'],
								'updated_at' => current_time('mysql')
							);

							if (!empty($cek)) {
								$wpdb->update('data_realisasi_akun_sipd', $opsi, array('id' => $cek));
							} else {
								$wpdb->insert('data_realisasi_akun_sipd', $opsi);
							}
						}
					}
					if (
						get_option('_crb_singkron_simda') == 1
						&& get_option('_crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
					) {
						$debug = false;
						if (get_option('_crb_singkron_simda_debug') == 1) {
							$debug = true;
						}
						$this->simda->singkronSimdaKas(array(
							'return' => $debug
						));
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Salah!';
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

	function update_bl_realisasi_nonactive()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil update non active sub kegiatan Realisasi',
			'action' => $_POST['action'],
			'id_unit' => $_POST['id_skpd'],
			'type' => $_POST['type']
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$where = array(
					'tahun_anggaran' => $_POST['tahun_anggaran'],
					'type' => $_POST['type']
				);
				if (
					empty($_POST['type'])
					|| $_POST['type'] == 'belanja'
				) {
					$where['id_unit'] = $_POST['id_skpd'];
				}
				$wpdb->update('data_realisasi_akun_sipd', array('active' => 0), $where);
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

	function update_bl_rak_nonactive()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil update non active sub kegiatan Anggaran Kas',
			'action'	=> $_POST['action'],
			'id_unit'	=> $_POST['id_skpd']
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$wpdb->update('data_anggaran_kas', array('active' => 0), array(
					'tahun_anggaran' => $_POST['tahun_anggaran'],
					'type' => $_POST['type'],
					'id_unit' => $_POST['id_skpd']
				));
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

	function get_kas_fmis($no_debug = false)
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$type = $_POST['type'];
				$id_skpd_fmis = $_POST['id_skpd_fmis'];
				$get_id = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran);
				$id_skpd_sipd = $get_id['id_skpd_sipd'];
				$program_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_program_fmis'
				));
				$keg_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_keg_fmis'
				));
				$subkeg_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_subkeg_fmis'
				));
				$rek_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_rekening_fmis'
				));
				// anggaran kas belanja sipd
				if ($type == 4) {
					$kas_p = array();
					$data_sub_keg = $wpdb->get_results($wpdb->prepare(
						"
						SELECT 
							s.*,
							u.nama_skpd as nama_skpd_data_unit
						from data_sub_keg_bl s 
						inner join data_unit u on s.id_sub_skpd = u.id_skpd
							and u.tahun_anggaran = s.tahun_anggaran
							and u.active = s.active
						where s.tahun_anggaran=%d
							and u.id_skpd IN (" . implode(',', $id_skpd_sipd) . ")
							and s.active=1",
						$tahun_anggaran
					), ARRAY_A);
					foreach ($data_sub_keg as $k => $sub) {
						$newsub = array(
							'kode_sbl' => $sub['kode_sbl'],
							'nama_skpd_data_unit' => $sub['nama_skpd_data_unit'],
							'kode_sub_skpd' => $sub['kode_sub_skpd'],
							'pagu' => $sub['pagu'],
							'pagu_keg' => $sub['pagu_keg'],
							'pagu_n_depan' => $sub['pagu_n_depan'],
							'pagu_n_lalu' => $sub['pagu_n_lalu'],
							'nama_giat' => $sub['nama_giat'],
							'nama_program' => $sub['nama_program'],
							'id_bidang_urusan' => $sub['id_bidang_urusan'],
							'nama_bidang_urusan' => $sub['nama_bidang_urusan'],
							'nama_urusan' => $sub['nama_urusan'],
							'nama_sub_giat' => $sub['nama_sub_giat']
						);
						if (!empty($program_mapping[$this->removeNewline($sub['nama_program'])])) {
							$newsub['nama_program'] = $program_mapping[$this->removeNewline($sub['nama_program'])];
						}
						if (!empty($keg_mapping[$this->removeNewline($sub['nama_giat'])])) {
							$newsub['nama_giat'] = $keg_mapping[$this->removeNewline($sub['nama_giat'])];
						}
						$nama_sub_giat = explode(' ', $sub['nama_sub_giat']);
						$kode_sub = $nama_sub_giat[0];
						unset($nama_sub_giat[0]);
						$nama_sub_giat = implode(' ', $nama_sub_giat);
						if (!empty($subkeg_mapping[$this->removeNewline($nama_sub_giat)])) {
							$newsub['nama_sub_giat'] = $kode_sub . ' ' . $subkeg_mapping[$this->removeNewline($nama_sub_giat)];
						}
						$newsub['id_mapping'] = get_option('_crb_unit_fmis_' . $tahun_anggaran . '_' . $sub['id_sub_skpd']);
						$kas = array();
						$kode_sbl = explode('.', $sub['kode_sbl']);
						$kode_sbl = $kode_sbl[1] . '.' . $kode_sbl[0] . '.' . $kode_sbl[1] . '.' . $sub['id_bidang_urusan'] . '.' . $kode_sbl[2] . '.' . $kode_sbl[3] . '.' . $kode_sbl[4];
						$kas = $wpdb->get_results(
							"
							SELECT 
								bulan_1, 
								bulan_2, 
								bulan_3, 
								bulan_4, 
								bulan_5, 
								bulan_6, 
								bulan_7, 
								bulan_8, 
								bulan_9, 
								bulan_10, 
								bulan_11, 
								bulan_12, 
								kode_akun,
								nama_akun
							from data_anggaran_kas 
							where kode_sbl='" . $kode_sbl . "' 
								AND tahun_anggaran=" . $sub['tahun_anggaran'] . "
								AND active=1",
							ARRAY_A
						);
						foreach ($kas as $n => $v) {
							$_kode_akun = explode('.', $v['kode_akun']);
							$kode_akun = array();
							foreach ($_kode_akun as $vv) {
								$kode_akun[] = (int)$vv;
							}
							$kode_akun = implode('.', $kode_akun);
							if (!empty($rek_mapping[$kode_akun])) {
								$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
								$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
								$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
								$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
								$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
								$kas[$n]['kode_akun'] = implode('.', $_kode_akun);
							}
						}
						$newsub['kas'] = $kas;
						$ret['data'][] = $newsub;

						if (empty($kas_p[$sub['id_sub_skpd']])) {
							$newsub = array(
								'kode_sbl' => '',
								'nama_skpd_data_unit' => $sub['nama_skpd_data_unit'],
								'kode_sub_skpd' => $sub['kode_sub_skpd'],
								'pagu' => 0,
								'pagu_keg' => 0,
								'pagu_n_depan' => 0,
								'pagu_n_lalu' => 0,
								'nama_giat' => 'x.x.xx.xx.xx Non Kegiatan',
								'nama_program' => 'x.x.xx.xx.xx Non Program',
								'id_bidang_urusan' => 0,
								'nama_bidang_urusan' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH',
								'nama_urusan' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH',
								'nama_sub_giat' => 'x.x.xx.xx.xx Non Sub Kegiatan'
							);
							$newsub['id_mapping'] = get_option('_crb_unit_fmis_' . $tahun_anggaran . '_' . $sub['id_sub_skpd']);
							$kas = $wpdb->get_results(
								"
								SELECT 
									bulan_1, 
									bulan_2, 
									bulan_3, 
									bulan_4, 
									bulan_5, 
									bulan_6, 
									bulan_7, 
									bulan_8, 
									bulan_9, 
									bulan_10, 
									bulan_11, 
									bulan_12, 
									kode_akun,
									nama_akun
								from data_anggaran_kas 
								where type IN ('pendapatan', 'pembiayaan-pengeluaran', 'pembiayaan-penerimaan') 
									AND id_skpd = " . $sub['id_sub_skpd'] . "
									AND tahun_anggaran=" . $sub['tahun_anggaran'] . "
									AND active=1",
								ARRAY_A
							);
							foreach ($kas as $n => $v) {
								$_kode_akun = explode('.', $v['kode_akun']);
								$kode_akun = array();
								foreach ($_kode_akun as $vv) {
									$kode_akun[] = (int)$vv;
								}
								$kode_akun = implode('.', $kode_akun);
								if (!empty($rek_mapping[$kode_akun])) {
									$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
									$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
									$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
									$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
									$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
									$kas[$n]['kode_akun'] = implode('.', $_kode_akun);
								}
							}
							$newsub['kas'] = $kas;
							$ret['data'][] = $newsub;
						}
					}
					// anggaran kas belanja simda
				} else if ($type == 5) {
					$data_sub_keg = $wpdb->get_results($wpdb->prepare(
						"
						SELECT 
							s.*,
							u.nama_skpd as nama_skpd_data_unit
						from data_sub_keg_bl s 
						inner join data_unit u on s.id_sub_skpd = u.id_skpd
							and u.tahun_anggaran = s.tahun_anggaran
							and u.active = s.active
						where s.tahun_anggaran=%d
							and u.id_skpd IN (" . implode(',', $id_skpd_sipd) . ")
							and s.active=1",
						$tahun_anggaran
					), ARRAY_A);
					$kas = array();
					$kas_p = array();
					foreach ($data_sub_keg as $k => $sub) {
						$newsub = array(
							'kode_sbl' => $sub['kode_sbl'],
							'nama_skpd_data_unit' => $sub['nama_skpd_data_unit'],
							'kode_sub_skpd' => $sub['kode_sub_skpd'],
							'pagu' => $sub['pagu'],
							'pagu_keg' => $sub['pagu_keg'],
							'pagu_n_depan' => $sub['pagu_n_depan'],
							'pagu_n_lalu' => $sub['pagu_n_lalu'],
							'nama_giat' => $sub['nama_giat'],
							'nama_program' => $sub['nama_program'],
							'id_bidang_urusan' => $sub['id_bidang_urusan'],
							'nama_bidang_urusan' => $sub['nama_bidang_urusan'],
							'nama_urusan' => $sub['nama_urusan'],
							'nama_sub_giat' => $sub['nama_sub_giat']
						);
						if (!empty($program_mapping[$sub['nama_program']])) {
							$newsub['nama_program'] = $program_mapping[$sub['nama_program']];
						}
						if (!empty($keg_mapping[$sub['nama_giat']])) {
							$newsub['nama_giat'] = $keg_mapping[$sub['nama_giat']];
						}
						$nama_sub_giat = explode(' ', $sub['nama_sub_giat']);
						$kode_sub = $nama_sub_giat[0];
						unset($nama_sub_giat[0]);
						$nama_sub_giat = implode(' ', $nama_sub_giat);
						if (!empty($subkeg_mapping[$nama_sub_giat])) {
							$newsub['nama_sub_giat'] = $kode_sub . ' ' . $subkeg_mapping[$nama_sub_giat];
						}
						$newsub['id_mapping'] = get_option('_crb_unit_fmis_' . $tahun_anggaran . '_' . $sub['id_sub_skpd']);
						$kd_unit_simda = explode('.', get_option('_crb_unit_' . $sub['id_sub_skpd']));
						$_kd_urusan = $kd_unit_simda[0];
						$_kd_bidang = $kd_unit_simda[1];
						$kd_unit = $kd_unit_simda[2];
						$kd_sub_unit = $kd_unit_simda[3];
						$kd = explode('.', $sub['kode_sub_giat']);
						$kd_urusan90 = (int) $kd[0];
						$kd_bidang90 = (int) $kd[1];
						$kd_program90 = (int) $kd[2];
						$kd_kegiatan90 = ((int) $kd[3]) . '.' . $kd[4];
						$kd_sub_kegiatan = (int) $kd[5];
						$nama_keg = explode(' ', $sub['nama_sub_giat']);
						unset($nama_keg[0]);
						$nama_keg = implode(' ', $nama_keg);
						$mapping = $this->simda->cekKegiatanMapping(array(
							'kd_urusan90' => $kd_urusan90,
							'kd_bidang90' => $kd_bidang90,
							'kd_program90' => $kd_program90,
							'kd_kegiatan90' => $kd_kegiatan90,
							'kd_sub_kegiatan' => $kd_sub_kegiatan,
							'nama_program' => $sub['nama_giat'],
							'nama_kegiatan' => $nama_keg
						));

						$kd_urusan = 0;
						$kd_bidang = 0;
						$kd_prog = 0;
						$kd_keg = 0;
						if (!empty($mapping[0]) && !empty($mapping[0]->kd_urusan)) {
							$kd_urusan = $mapping[0]->kd_urusan;
							$kd_bidang = $mapping[0]->kd_bidang;
							$kd_prog = $mapping[0]->kd_prog;
							$kd_keg = $mapping[0]->kd_keg;
						}
						foreach ($this->simda->custom_mapping as $c_map_k => $c_map_v) {
							if (
								$skpd['kode_skpd'] == $c_map_v['sipd']['kode_skpd']
								&& $sub['kode_sub_giat'] == $c_map_v['sipd']['kode_sub_keg']
							) {
								$kd_unit_simda_map = explode('.', $c_map_v['simda']['kode_skpd']);
								$_kd_urusan = $kd_unit_simda_map[0];
								$_kd_bidang = $kd_unit_simda_map[1];
								$kd_unit = $kd_unit_simda_map[2];
								$kd_sub_unit = $kd_unit_simda_map[3];
								$kd_keg_simda = explode('.', $c_map_v['simda']['kode_sub_keg']);
								$kd_urusan = $kd_keg_simda[0];
								$kd_bidang = $kd_keg_simda[1];
								$kd_prog = $kd_keg_simda[2];
								$kd_keg = $kd_keg_simda[3];
							}
						}
						$id_prog = $kd_urusan . $this->simda->CekNull($kd_bidang);
						$opsi = array(
							'tahun_anggaran' => $tahun_anggaran,
							'rak_all' => true,
							'kd_urusan' => $_kd_urusan,
							'kd_bidang' => $_kd_bidang,
							'kd_unit' => $kd_unit,
							'kd_sub' => $kd_sub_unit,
							'kd_prog' => $kd_prog,
							'id_prog' => $id_prog,
							'kd_keg' => $kd_keg
						);
						$kas = $this->get_rak_simda($opsi);
						foreach ($kas as $n => $v) {
							$_kode_akun = explode('.', $v['kode_akun']);
							$kode_akun = array();
							foreach ($_kode_akun as $vv) {
								$kode_akun[] = (int)$vv;
							}
							$kode_akun = implode('.', $kode_akun);
							if (!empty($rek_mapping[$kode_akun])) {
								$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
								$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
								$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
								$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
								$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
								$kas[$n]['kode_akun'] = implode('.', $_kode_akun);
							}
						}
						$newsub['kas'] = $kas;
						$ret['data'][] = $newsub;

						if (empty($kas_p[$sub['id_sub_skpd']])) {
							$newsub = array(
								'kode_sbl' => '',
								'nama_skpd_data_unit' => $sub['nama_skpd_data_unit'],
								'kode_sub_skpd' => $sub['kode_sub_skpd'],
								'pagu' => 0,
								'pagu_keg' => 0,
								'pagu_n_depan' => 0,
								'pagu_n_lalu' => 0,
								'nama_giat' => 'x.x.xx.xx.xx Non Kegiatan',
								'nama_program' => 'x.x.xx.xx.xx Non Program',
								'id_bidang_urusan' => 0,
								'nama_bidang_urusan' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH',
								'nama_urusan' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH',
								'nama_sub_giat' => 'x.x.xx.xx.xx Non Sub Kegiatan'
							);
							$newsub['id_mapping'] = get_option('_crb_unit_fmis_' . $tahun_anggaran . '_' . $sub['id_sub_skpd']);
							$opsi = array(
								'tahun_anggaran' => $tahun_anggaran,
								'rak_all' => true,
								'kd_urusan' => $_kd_urusan,
								'kd_bidang' => $_kd_bidang,
								'kd_unit' => $kd_unit,
								'kd_sub' => $kd_sub_unit,
								'kd_prog' => 0,
								'id_prog' => 0,
								'kd_keg' => 0
							);
							$kas = $this->get_rak_simda($opsi);
							foreach ($kas as $n => $v) {
								$_kode_akun = explode('.', $v['kode_akun']);
								$kode_akun = array();
								foreach ($_kode_akun as $vv) {
									$kode_akun[] = (int)$vv;
								}
								$kode_akun = implode('.', $kode_akun);
								if (!empty($rek_mapping[$kode_akun])) {
									$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
									$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
									$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
									$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
									$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
									$kas[$n]['kode_akun'] = implode('.', $_kode_akun);
								}
							}
							$newsub['kas'] = $kas;
							$ret['data'][] = $newsub;
						}
					}
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		if ($no_debug) {
			return $ret;
		} else {
			die(json_encode($ret));
		}
	}

	function singkron_kas_fmis()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				// on progress
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		if ($no_debug) {
			return $ret;
		} else {
			die(json_encode($ret));
		}
	}

	function get_kas($no_debug = false)
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'	=> array(
				'bl' => array(),
				'kas' => array(),
				'per_bulan' => array(
					0 => 0,
					1 => 0,
					2 => 0,
					3 => 0,
					4 => 0,
					5 => 0,
					6 => 0,
					7 => 0,
					8 => 0,
					9 => 0,
					10 => 0,
					11 => 0
				),
				'total' => 0
			)
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (
					!empty($_POST['kode_giat'])
					&& !empty($_POST['kode_skpd'])
				) {
					$where_sub = '';
					if (!empty($_POST['kode_sub_giat'])) {
						$where_sub = $wpdb->prepare(' AND kode_sub_giat=%s', $_POST['kode_sub_giat']);
					}
					if (
						!empty($_POST['tipe'])
						&& (
							$_POST['tipe'] == 'pendapatan'
							|| $_POST['tipe'] == 'pembiayaan'
						)
					) {
						$ret['data']['bl'] = array(array('id_skpd' => $_POST['id_skpd']));
					} else {
						$ret['data']['bl'] = $wpdb->get_results(
							$wpdb->prepare("
							SELECT 
								*
							from data_sub_keg_bl
							where kode_giat=%s
								AND kode_sub_skpd=%s
								AND tahun_anggaran=%d
								AND kode_sbl != ''
								AND active=1
								$where_sub
						", $_POST['kode_giat'], $_POST['kode_skpd'], $_POST['tahun_anggaran']),
							ARRAY_A
						);
					}
					foreach ($ret['data']['bl'] as $k => $v) {
						if (
							!empty($_POST['tipe'])
							&& (
								$_POST['tipe'] == 'pendapatan'
								|| $_POST['tipe'] == 'pembiayaan'
							)
						) {
							$kas = $wpdb->get_results($wpdb->prepare("
								SELECT 
									* 
								from data_anggaran_kas 
								where id_sub_skpd=%d 
									AND tahun_anggaran=%d
									AND type like %s
									AND active=1
							", $v['id_skpd'], $_POST['tahun_anggaran'], $_POST['tipe'] . '%'), ARRAY_A);
						} else {
							$kode_sbl = explode('.', $v['kode_sbl']);
							if ($_POST['tahun_anggaran'] >= 2024) {
								// id_skpd.id_sub_skpd.id_skpd (format kode_sbl terbaru)
								$kode_sbl = $kode_sbl[0] . '.' . $kode_sbl[1] . '.' . $kode_sbl[0] . '.' . $v['id_bidang_urusan'] . '.' . $kode_sbl[2] . '.' . $kode_sbl[3] . '.' . $kode_sbl[4];
							} else {
								// id_unit.id_skpd.id_sub_skpd (format kode_sbl sipd biru)
								$kode_sbl = $kode_sbl[1] . '.' . $kode_sbl[0] . '.' . $kode_sbl[1] . '.' . $v['id_bidang_urusan'] . '.' . $kode_sbl[2] . '.' . $kode_sbl[3] . '.' . $kode_sbl[4];
							}
							$kas = $wpdb->get_results($wpdb->prepare("
								SELECT 
									* 
								from data_anggaran_kas 
								where kode_sbl=%s 
									AND tahun_anggaran=%d
									AND active=1
							", $kode_sbl, $v['tahun_anggaran']), ARRAY_A);
						}
						if (!empty($kas)) {
							$ret['data']['kas'][] = $kas;
							foreach ($kas as $kk => $vv) {
								$ret['data']['per_bulan'][0] += $vv['bulan_1'];
								$ret['data']['per_bulan'][1] += $vv['bulan_2'];
								$ret['data']['per_bulan'][2] += $vv['bulan_3'];
								$ret['data']['per_bulan'][3] += $vv['bulan_4'];
								$ret['data']['per_bulan'][4] += $vv['bulan_5'];
								$ret['data']['per_bulan'][5] += $vv['bulan_6'];
								$ret['data']['per_bulan'][6] += $vv['bulan_7'];
								$ret['data']['per_bulan'][7] += $vv['bulan_8'];
								$ret['data']['per_bulan'][8] += $vv['bulan_9'];
								$ret['data']['per_bulan'][9] += $vv['bulan_10'];
								$ret['data']['per_bulan'][10] += $vv['bulan_11'];
								$ret['data']['per_bulan'][11] += $vv['bulan_12'];
							}
						}
					}
					foreach ($ret['data']['per_bulan'] as $key => $value) {
						$ret['data']['total'] += $value;
					}
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		if ($no_debug) {
			return $ret;
		} else {
			die(json_encode($ret));
		}
	}

	function get_data_rka()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'post'	=> array('idrincisbl' => $_POST['idbelanjarinci']),
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['kode_sbl'])) {
					$ret['data'] = $wpdb->get_row(
						$wpdb->prepare("
						SELECT 
							*
						from data_rka
						where kode_sbl=%s
							AND id_rinci_sub_bl=%d
							AND tahun_anggaran=%d
							AND active=1", $_POST['kode_sbl'], $_POST['idbelanjarinci'], $_POST['tahun_anggaran']),
						ARRAY_A
					);
					// echo $wpdb->last_query;
					if (!empty($ret['data'])) {
						$ret['data']['nama_prop'] = $wpdb->get_row("select nama from data_alamat where id_alamat=" . $ret['data']['id_prop_penerima'] . " and is_prov=1", ARRAY_A);
						$ret['data']['nama_kab'] = $wpdb->get_row("select nama from data_alamat where id_alamat=" . $ret['data']['id_kokab_penerima'] . " and is_kab=1", ARRAY_A);
						$ret['data']['nama_kec'] = $wpdb->get_row("select nama from data_alamat where id_alamat=" . $ret['data']['id_camat_penerima'] . " and is_kec=1", ARRAY_A);
						$ret['data']['nama_kel'] = $wpdb->get_row("select nama from data_alamat where id_alamat=" . $ret['data']['id_lurah_penerima'] . " and is_kel=1", ARRAY_A);
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'kode_sbl tidak boleh kosong!';
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

	function get_up()
	{
		$this->simda->get_up_simda();
	}

	function get_link_laporan()
	{
		global $wpdb;
		$ret = $_POST;
		$ret['status'] = 'success';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['kode_bl'])) {
					$kode_bl = explode('.', $_POST['kode_bl']);
					unset($kode_bl[2]);
					// kode_skpd diselect dari data_unit karena pada tabel data_sub_keg_bl kolom kode_sub_skpd dan nama skpd salah
					$bl = $wpdb->get_results($wpdb->prepare(
						"
						select 
							u.kode_skpd,
							s.kode_bidang_urusan,
							s.kode_giat,
							s.nama_giat 
						from data_sub_keg_bl s
							left join data_unit u on s.id_sub_skpd=u.id_skpd
						where s.kode_bl=%s 
							and s.active=1 
							and s.tahun_anggaran=%d",
						implode('.', $kode_bl),
						$_POST['tahun_anggaran']
					), ARRAY_A);
					$ret['query'] = $wpdb->last_query;
					$kodeunit = $bl[0]['kode_skpd'];
					$kode_giat = $bl[0]['kode_bidang_urusan'] . substr($bl[0]['kode_giat'], 4, strlen($bl[0]['kode_giat']));
					$nama_page = $_POST['tahun_anggaran'] . ' | ' . $kodeunit . ' | ' . $kode_giat . ' | ' . $bl[0]['nama_giat'];
					$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'post');
					$ret['link'] = $this->get_link_post($custom_post);
					$ret['text_link'] = 'Print DPA Lokal';
					$ret['judul'] = $nama_page;
					$ret['bl'] = $bl;
				} else {
					$nama_page = $_POST['tahun_anggaran'] . ' | Laporan';
					$cat_name = $_POST['tahun_anggaran'] . ' APBD';
					$post_content = '';

					if (
						(
							$_POST['jenis'] == '1'
							|| $_POST['jenis'] == '2'
							|| $_POST['jenis'] == '3a'
							|| $_POST['jenis'] == '3b'
							|| $_POST['jenis'] == '4a'
							|| $_POST['jenis'] == '4b'
							|| $_POST['jenis'] == '5a'
							|| $_POST['jenis'] == '5b'
							|| $_POST['jenis'] == '6a'
							|| $_POST['jenis'] == '6b'
							|| $_POST['jenis'] == '6c'
						)
						&& $_POST['model'] == 'perkada'
						&& $_POST['cetak'] == 'apbd'
					) {
						$nama_page = $_POST['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran ' . $_POST['jenis'];
						$cat_name = $_POST['tahun_anggaran'] . ' APBD';
						$post_content = '[apbdpenjabaran tahun_anggaran="' . $_POST['tahun_anggaran'] . '" lampiran="' . $_POST['jenis'] . '"]';
						$ret['text_link'] = 'Print APBD PENJABARAN Lampiran ' . $_POST['jenis'];
						$custom_post = $this->save_update_post($nama_page, $cat_name, $post_content);
						$ret['link'] = $this->get_link_post($custom_post);
						// }else if(
						// 	$_POST['jenis'] == '2'
						// 	&& $_POST['model'] == 'perkada'
						// 	&& $_POST['cetak'] == 'apbd'
						// ){
						// 	$sql = $wpdb->prepare("
						// 	    select 
						// 	        id_skpd,
						// 	        kode_skpd,
						// 	        nama_skpd
						// 	    from data_unit
						// 	    where tahun_anggaran=%d
						// 	        and active=1
						// 	", $_POST['tahun_anggaran']);
						// 	$unit = $wpdb->get_results($sql, ARRAY_A);
						// 	$ret['link'] = array();
						// 	foreach ($unit as $k => $v) {
						// 		$nama_page = $_POST['tahun_anggaran'] .' | '.$v['kode_skpd'].' | '.$v['nama_skpd'].' | '. ' | APBD PENJABARAN Lampiran 2';
						// 		$cat_name = $_POST['tahun_anggaran'] . ' APBD';
						// 		$post_content = '[apbdpenjabaran tahun_anggaran="'.$_POST['tahun_anggaran'].'" lampiran="'.$_POST['jenis'].'" id_skpd="'.$v['id_skpd'].'"]';
						// 		$custom_post = $this->save_update_post($nama_page, $cat_name, $post_content);
						// 		$ret['link'][$v['id_skpd']] = array(
						// 			'id_skpd' => $v['id_skpd'],
						// 			'text_link' => 'Print APBD PENJABARAN Lampiran 2',
						// 			'link' => $this->get_link_post($custom_post)
						// 		);
						// 	}
					} else if (
						$_POST['jenis'] == '1'
						|| $_POST['jenis'] == '2'
						|| $_POST['jenis'] == '3'
						|| $_POST['jenis'] == '4'
						|| $_POST['jenis'] == '5'
						|| $_POST['jenis'] == '6'
						|| $_POST['jenis'] == '7'
						|| $_POST['jenis'] == '8'
						|| $_POST['jenis'] == '9'
						|| $_POST['jenis'] == '10'
						|| $_POST['jenis'] == '11'
						&& $_POST['model'] == 'perda'
						&& $_POST['cetak'] == 'apbd'
					) {
						$nama_page = $_POST['tahun_anggaran'] . ' | APBD PERDA Lampiran ' . $_POST['jenis'];
						$cat_name = $_POST['tahun_anggaran'] . ' APBD';
						$post_content = '[apbdperda tahun_anggaran="' . $_POST['tahun_anggaran'] . '" lampiran="' . $_POST['jenis'] . '"]';
						$ret['text_link'] = 'Print APBD PERDA Lampiran ' . $_POST['jenis'];
						$custom_post = $this->save_update_post($nama_page, $cat_name, $post_content);
						$ret['link'] = $this->get_link_post($custom_post);
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'Page tidak ditemukan!';
					}
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

	function save_update_post($nama_page, $cat_name, $post_content)
	{
		$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
		$_post = array(
			'post_title'	=> $nama_page,
			'post_content'	=> $post_content,
			'post_type'		=> 'page',
			'post_status'	=> 'private',
			'comment_status'	=> 'closed'
		);
		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post($_post);
			$_post['insert'] = 1;
			$_post['ID'] = $id;
		} else {
			$_post['ID'] = $custom_post->ID;
			wp_update_post($_post);
			$_post['update'] = 1;
		}
		$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
		update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
		update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
		update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
		update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
		update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
		update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
		update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
		update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
		return $custom_post;
	}

	function gen_key($key_db = false, $options = array())
	{
		$now = time() * 1000;
		if (empty($key_db)) {
			$key_db = md5(get_option('_crb_api_key_extension'));
		}
		$tambahan_url = '';
		$cek_param_get = [];
		if (!empty($options['custom_url'])) {
			$custom_url = array();
			foreach ($options['custom_url'] as $k => $v) {
				if (
					!empty($v['key'])
					&& !empty($v['value'])
				) {
					$custom_url[] = $v['key'] . '=' . $v['value'];
				} else {
					$cek_param_get[] = $k . '=' . $v;
				}
			}
			$tambahan_url = $key_db . implode('&', $custom_url);
		}
		$key = base64_encode($now . $key_db . $now . $tambahan_url);
		if (!empty($cek_param_get)) {
			$key .= '&' . implode('&', $cek_param_get);
		}
		return $key;
	}

	function penyebut($nilai)
	{
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " " . $huruf[$nilai];
		} else if ($nilai < 20) {
			$temp = $this->penyebut($nilai - 10) . " belas";
		} else if ($nilai < 100) {
			$temp = $this->penyebut($nilai / 10) . " puluh" . $this->penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . $this->penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->penyebut($nilai / 100) . " ratus" . $this->penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . $this->penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->penyebut($nilai / 1000) . " ribu" . $this->penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->penyebut($nilai / 1000000) . " juta" . $this->penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->penyebut($nilai / 1000000000) . " milyar" . $this->penyebut(fmod($nilai, 1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->penyebut($nilai / 1000000000000) . " trilyun" . $this->penyebut(fmod($nilai, 1000000000000));
		}
		return $temp;
	}

	function terbilang($nilai)
	{
		if ($nilai == 0) {
			$hasil = "nol";
		} else if ($nilai < 0) {
			$hasil = "minus " . trim($this->penyebut($nilai));
		} else {
			$hasil = trim($this->penyebut($nilai));
		}
		return $hasil . ' rupiah';
	}

	function get_bulan($bulan = false)
	{
		if (empty($bulan)) {
			$bulan = date('m');
		}
		$nama_bulan = array(
			"Januari",
			"Februari",
			"Maret",
			"April",
			"Mei",
			"Juni",
			"Juli",
			"Agustus",
			"September",
			"Oktober",
			"November",
			"Desember"
		);
		return $nama_bulan[((int) $bulan) - 1];
	}

	function singkron_pendahuluan()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil singkron data pendahuluan (SEKDA & TAPD)';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					if ($_POST['type'] == 'ri') {
						$_POST['data'] = json_decode(stripslashes(html_entity_decode($_POST['data'])), true);
					}
					$wpdb->update('data_user_tapd_sekda', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
					));
					foreach ($_POST['data']['data_sekda'] as $k => $v) {
						$sql = $wpdb->prepare("
						    select 
						        *
						    from data_user_tapd_sekda
						    where tahun_anggaran=%d
						    	and type='sekda'
						        and nip=%s
						", $_POST['tahun_anggaran'], $v['nip']);
						$cek = $wpdb->get_results($sql, ARRAY_A);
						$opsi = array(
							'created_at'	=> $v['created_at'],
							'created_user'	=> $v['created_user'],
							'id_daerah'	=> $v['id_daerah'],
							'jabatan'	=> $v['jabatan'],
							'nama'	=> $v['nama'],
							'nip'	=> $v['nip'],
							'tahun'	=> $v['tahun'],
							'updated_at'	=> $v['updated_at'],
							'type'	=>  'sekda',
							'tahun_anggaran'	=> $_POST['tahun_anggaran'],
							'singkron_at'	=>  current_time('mysql'),
							'active'	=> 1
						);
						if (!empty($cek)) {
							$wpdb->update('data_user_tapd_sekda', $opsi, array(
								'tahun_anggaran' => $_POST['tahun_anggaran'],
								'type' => 'sekda',
								'nip' => $v['nip']
							));
						} else {
							$wpdb->insert('data_user_tapd_sekda', $opsi);
						}
					}
					foreach ($_POST['data']['tim_tapd'] as $k => $v) {
						$sql = $wpdb->prepare("
						    select 
						        *
						    from data_user_tapd_sekda
						    where tahun_anggaran=%d
						        and type='tapd'
						        and nip=%s
						", $_POST['tahun_anggaran'], $v['nip']);
						$cek = $wpdb->get_results($sql, ARRAY_A);
						$opsi = array(
							'created_at'	=> $v['created_at'],
							'created_user'	=> $v['created_user'],
							'id_daerah'	=> $v['id_daerah'],
							'id_skpd'	=> $v['id_skpd'],
							'jabatan'	=> $v['jabatan'],
							'nama'	=> $v['nama'],
							'nip'	=> $v['nip'],
							'no_urut'	=> $v['no_urut'],
							'tahun'	=> $v['tahun'],
							'updated_at'	=> $v['updated_at'],
							'type'	=>  'tapd',
							'tahun_anggaran'	=> $_POST['tahun_anggaran'],
							'singkron_at'	=>  current_time('mysql'),
							'active'	=> 1
						);
						if (!empty($cek)) {
							$wpdb->update('data_user_tapd_sekda', $opsi, array(
								'tahun_anggaran' => $_POST['tahun_anggaran'],
								'type' => 'tapd',
								'nip' => $v['nip']
							));
						} else {
							$wpdb->insert('data_user_tapd_sekda', $opsi);
						}
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Data pendahuluan tidak boleh kosong!';
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


	function ubah_minus($nilai)
	{
		if ($nilai < 0) {
			$nilai = abs($nilai);
			return '(' . number_format($nilai, 0, ",", ".") . ')';
		} else {
			return number_format($nilai, 0, ",", ".");
		}
	}

	function gen_user_sipd_merah($user = array(), $update_pass = false)
	{
		global $wpdb;
		if (!empty($user)) {
			$username = $user['loginname'];
			if (!empty($user['emailteks'])) {
				$email = $user['emailteks'];
			} else {
				$email = $username . '@sipdlocal.com';
			}
			$role = get_role($user['jabatan']);
			if (empty($role)) {
				add_role($user['jabatan'], $user['jabatan'], array(
					'read' => true,
					'edit_posts' => false,
					'delete_posts' => false
				));
			}
			$insert_user = username_exists($username);
			if (!$insert_user) {
				$option = array(
					'user_login' => $username,
					'user_pass' => $user['pass'],
					'user_email' => $email,
					'first_name' => $user['nama'],
					'display_name' => $user['nama'],
					'role' => $user['jabatan']
				);
				$insert_user = wp_insert_user($option);

				if (is_wp_error($insert_user)) {
					//print_r($option);die();
					return $insert_user;
				}
			}

			if (!empty($update_pass)) {
				wp_set_password($user['pass'], $insert_user);
			}

			$meta = array(
				'_nip' => $user['nip'],
				'description' => 'User dibuat dari data SIPD Merah'
			);
			if (!empty($user['id_sub_skpd'])) {
				$skpd = $wpdb->get_var("SELECT nama_skpd from data_unit where id_skpd=" . $user['id_sub_skpd'] . " AND active=1");
				$meta['_crb_nama_skpd'] = $skpd;
				$meta['_id_sub_skpd'] = $user['id_sub_skpd'];
			}
			if (!empty($user['iduser'])) {
				$meta['id_user_sipd'] = $user['iduser'];
			}
			foreach ($meta as $key => $val) {
				update_user_meta($insert_user, $key, $val);
			}
		}
	}

	function generate_user_sipd_merah()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil Generate User Wordpress dari DB Lokal SIPD Merah';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$users_pa = $wpdb->get_results("SELECT * from data_unit where active=1", ARRAY_A);
				$update_pass = false;
				if (
					!empty($_POST['update_pass'])
					&& $_POST['update_pass'] == 'true'
				) {
					$update_pass = true;
				}
				if (!empty($users_pa)) {
					foreach ($users_pa as $k => $user) {
						$user['pass'] = $_POST['pass'];
						$user['loginname'] = $user['nipkepala'];
						$user['jabatan'] = $user['statuskepala'];
						$user['nama'] = $user['namakepala'];
						$user['id_sub_skpd'] = $user['id_skpd'];
						$user['nip'] = $user['nipkepala'];
						$this->gen_user_sipd_merah($user, $update_pass);
					}
					// 20 = kades, 21 = lembaga, 22 = individu, 16 = dewan
					$users = $wpdb->get_results("SELECT * from data_dewan where active=1 AND idlevel NOT IN (16, 20, 21, 22)", ARRAY_A);
					if (!empty($users)) {
						foreach ($users as $k => $user) {
							$user['pass'] = $_POST['pass'];
							if (
								$user['idlevel'] == 11
								|| $user['idlevel'] == 7
							) {
								$user['jabatan'] = 'mitra_bappeda';
							} else if ($user['jabatan'] == 'ADMIN PERENCANAAN') {
								$user['jabatan'] = 'tapd_pp';
							} else if ($user['jabatan'] == 'TAPD KEUANGAN') {
								$user['jabatan'] = 'tapd_keu';
							}
							$this->gen_user_sipd_merah($user, $update_pass);
						}
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'Data user kosong. Harap lakukan singkronisasi data user dulu!';
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Data user PA/KPA kosong. Harap lakukan singkronisasi data SKPD dulu!';
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

	function simpan_meta_skpd()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil simpan alamat SKPD!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_skpd = $_POST['id_skpd'];
				if (empty($id_skpd)) {
					$ret['status'] = 'error';
					$ret['message'] = 'ID SKPD tidak boleh kosong!';
				} else {
					$alamat = $_POST['alamat'];
					update_option('_crb_skpd_alamat_' . $id_skpd, $alamat);
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

	public function get_carbon_multiselect($field)
	{
		global $wpdb;
		$table = $wpdb->prefix . "options";
		return $wpdb->get_results($wpdb->prepare("
			select 
				option_value as value
			from " . $table . "
			where option_name like %s
		", '_' . $field . '%'), ARRAY_A);
	}

	public function menu_monev_skpd($options)
	{
		global $wpdb;
		$id_skpd = $options['id_skpd'];
		$nama_skpd = $options['kode_skpd'] . ' ' . $options['nama_skpd'];
		$api_key = get_option('_crb_api_key_extension');
		$alamat = get_option('_crb_skpd_alamat_' . $id_skpd);
		$ajax_url = admin_url('admin-ajax.php');
		echo '<div>';
		if (!empty($id_skpd)) {
			echo "<h5 class='text_tengah' style='margin-bottom: 10px;'>$nama_skpd</h5>";

			if (!empty($options['menu'])) {
				$daftar_tombol = $options['menu'];
			} else {
				$daftar_tombol = $this->get_carbon_multiselect('crb_daftar_tombol_user_dashboard');
			}
			$daftar_tombol_list = array();
			foreach ($daftar_tombol as $v) {
				$daftar_tombol_list[$v['value']] = $v['value'];
			}

			// jika menu input usulan ssh aktiv
			if (!empty($daftar_tombol_list[7])) {
				echo "
				<div class='container'>
					<div class='row'>
						<div class='col-md-2'>
							<label for='alamat_skpd_$id_skpd' style='display: block;'>Alamat SKPD : </label>
							<button class='btn btn-primary' onclick='simpan_alamat($id_skpd, \"$api_key\", \"$ajax_url\");'>Simpan</button>
						</div>
						<div class='col-md-9'>
							<textarea class='form-control' id='alamat_skpd_$id_skpd' placeholder='Jalan ...'>$alamat</textarea>
						</div>
					</div>
				</div>";
			}

			$tahun_skpd = get_option('_crb_tahun_anggaran_sipd');
			$tahun = $_GET['tahun'];
			$unit = $wpdb->get_results($wpdb->prepare(
				"
				SELECT 
					nama_skpd, 
					is_skpd, 
					id_skpd, 
					kode_skpd 
				from data_unit 
				where active=1 
					and tahun_anggaran=%d
					and id_skpd=%d",
				$tahun_skpd,
				$id_skpd
			), ARRAY_A);

			$sumber_pagu_dpa = get_option('_crb_default_sumber_pagu_dpa');
			$url_nilai_dpa = '&pagu_dpa=simda';
			if ($sumber_pagu_dpa == 2) {
				$url_nilai_dpa = '&pagu_dpa=fmis';
			} else if ($sumber_pagu_dpa == 3) {
				$url_nilai_dpa = '&pagu_dpa=sipd';
			}
			echo '<ul class="aksi-monev text_tengah">';
			$user_id = um_user('ID');
			$user_meta = get_userdata($user_id);
			foreach ($unit as $kk => $vv) {
				if (
					in_array("administrator", $user_meta->roles)
					|| in_array("PLH", $user_meta->roles)
					|| in_array("PLT", $user_meta->roles)
					|| in_array("PA", $user_meta->roles)
					|| in_array("KPA", $user_meta->roles)
					|| in_array("tapd_pp", $user_meta->roles)
					|| in_array("tapd_keu", $user_meta->roles)
					|| in_array("mitra_bappeda", $user_meta->roles)
					|| !empty($options['menu'])
				) {
					if (!empty($daftar_tombol_list[1])) {
						$nama_page = 'RFK ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $tahun;
						$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
						$url_rfk = $this->get_link_post($custom_post);
						echo '<li><a href="' . $this->add_param_get($url_rfk, $url_nilai_dpa) . '" target="_blank" class="btn btn-info">MONEV RFK</a></li>';
					}

					if (!empty($daftar_tombol_list[2])) {
						$nama_page_sd = 'Sumber Dana ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $tahun;
						$custom_post = $this->get_page_by_title($nama_page_sd, OBJECT, 'page');
						$url_sd = $this->get_link_post($custom_post);
						echo '<li><a href="' . $url_sd . '" target="_blank" class="btn btn-info">MONEV SUMBER DANA</a></li>';
					}

					if (!empty($daftar_tombol_list[3])) {
						$nama_page_label = 'Label Komponen ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $tahun;
						$custom_post = $this->get_page_by_title($nama_page_label, OBJECT, 'page');
						$url_label = $this->get_link_post($custom_post);
						echo '<li><a href="' . $url_label . '" target="_blank" class="btn btn-info">MONEV LABEL KOMPONEN</a></li>';
					}

					if (!empty($daftar_tombol_list[4])) {
						$nama_page_monev_renja = 'MONEV ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $tahun;
						$custom_post = $this->get_page_by_title($nama_page_monev_renja, OBJECT, 'page');
						$url_monev_renja = $this->get_link_post($custom_post);
						echo '<li><a href="' . $url_monev_renja . '" target="_blank" class="btn btn-info">MONEV INDIKATOR RENJA</a></li>';
					}

					if ($vv['is_skpd'] == 1) {
						$nama_page_monev_renstra = 'MONEV RENSTRA ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $tahun;
						$custom_post = $this->get_page_by_title($nama_page_monev_renstra, OBJECT, 'page');
						$url_monev_renstra = $this->get_link_post($custom_post);
						if (!empty($daftar_tombol_list[5])) {
							echo '<li><a href="' . $url_monev_renstra . '" target="_blank" class="btn btn-info">MONEV INDIKATOR RENSTRA</a></li>';
						}
					}

					if (!empty($daftar_tombol_list[7])) {
						$nama_page_menu_ssh = 'Rekapitulasi Rincian Belanja ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $tahun;
						$custom_post = $this->get_page_by_title($nama_page_menu_ssh, OBJECT, 'page');
						$url_menu_ssh = $this->get_link_post($custom_post);
						echo '<li><a href="' . $url_menu_ssh . '" target="_blank" class="btn btn-info">MANAJEMEN STANDAR HARGA</a></li>';
					}

					if ($vv['is_skpd'] == 1) {
						if (!empty($daftar_tombol_list[8])) {
							$nama_page = 'Input RENSTRA ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'];
							$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
							$url_menu = $this->get_link_post($custom_post);
							echo '<li><a href="' . $url_menu . '" target="_blank" class="btn btn-info">INPUT RENSTRA</a></li>';
						}
					}

					if (!empty($daftar_tombol_list[9])) {
						$nama_page = 'Input RENJA ' . $vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $tahun;
						$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
						$url_menu = $this->get_link_post($custom_post);
						echo '<li><a href="' . $url_menu . '" target="_blank" class="btn btn-info">INPUT RENJA</a></li>';
					}

					if (!empty($daftar_tombol_list[10])) {
						if (strpos(strtolower($vv['nama_skpd']), 'kecamatan ') !== false) {
							$tampil_pilkades = get_option("_bkk_pilkades_" . $tahun);

							$url_skpd = $this->generatePage($vv['nama_skpd'] . ' ' . $vv['kode_skpd'] . ' | ' . $tahun, $tahun, '[monitor_keu_pemdes tahun_anggaran="' . $tahun . '" id_skpd="' . $vv['id_skpd'] . '"]');
							echo '<li style="display: block;"><a target="_blank" href="' . $url_skpd . '" class="btn btn-info">Keuangan PEMDES</a>';

							$input_pencairan_bkk = $this->generatePage('Halaman Input Pencairan BKK', false, '[input_pencairan_bkk]');
							echo '<li><a target="_blank" href="' . $input_pencairan_bkk . '&tahun_anggaran=' . $tahun . '&id_skpd=' . $vv['id_skpd'] . '" class="btn btn-info">Pencairan BKK Infrastruktur</a></li>';

							if (!empty($tampil_pilkades)) {
								$input_pencairan_bkk_pilkades = $this->generatePage('Halaman Input Pencairan BKK Pilkades', false, '[input_pencairan_bkk_pilkades]');
								echo '<li><a target="_blank" href="' . $input_pencairan_bkk_pilkades . '&tahun_anggaran=' . $tahun . '&id_skpd=' . $vv['id_skpd'] . '" class="btn btn-info">Pencairan BKK Pilkades</a></li>';
							}

							$input_pencairan_bhpd = $this->generatePage('Halaman Input Pencairan bhpd', false, '[input_pencairan_bhpd]');
							echo '<li><a target="_blank" href="' . $input_pencairan_bhpd . '&tahun_anggaran=' . $tahun . '&id_skpd=' . $vv['id_skpd'] . '" class="btn btn-info">Pencairan BHPD</a></li>';

							$input_pencairan_bhrd = $this->generatePage('Halaman Input Pencairan bhrd', false, '[input_pencairan_bhrd]');
							echo '<li><a target="_blank" href="' . $input_pencairan_bhrd . '&tahun_anggaran=' . $tahun . '&id_skpd=' . $vv['id_skpd'] . '" class="btn btn-info">Pencairan BHRD</a></li>';

							$input_pencairan_bku_dd = $this->generatePage('Halaman Input Pencairan BKU DD', false, '[input_pencairan_bku_dd]');
							echo '<li><a target="_blank" href="' . $input_pencairan_bku_dd . '&tahun_anggaran=' . $tahun . '&id_skpd=' . $vv['id_skpd'] . '" class="btn btn-info">Pencairan BKU DD</a></li>';

							$input_pencairan_bku_add = $this->generatePage('Halaman Input Pencairan BKU ADD', false, '[input_pencairan_bku_add]');
							echo '<li><a target="_blank" href="' . $input_pencairan_bku_add . '&tahun_anggaran=' . $tahun . '&id_skpd=' . $vv['id_skpd'] . '" class="btn btn-info">Pencairan BKU ADD</a></li>';
						}
					}
					if (!empty($daftar_tombol_list[11])) {
						$url_menu = $this->generatePage('User PPTK', false, '[user_pptk]');
						echo '<li><a href="' . $url_menu . '&id_skpd=' . $vv['id_skpd'] . '" target="_blank" class="btn btn-info">User PPTK</a></li>';
					}
					if (!empty($daftar_tombol_list[12])) {
						$sppd_page = $this->generatePage(
							'Surat Perintah Tugas | ' . $tahun,
							$tahun,
							'[spt_sppd tahun_anggaran="' . $tahun . '"]'
						);
						echo '<li><a href="' . $sppd_page . '" target="_blank" class="btn btn-info">Buat SPT/SPPD</a></li>';
					}
				}
			}
			echo '</ul>';
		} else {
			echo 'SKPD tidak ditemukan!';
		}
		echo '</div>';
		return;
	}

	public function menu_monev()
	{
		global $wpdb;
		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);
		if (!empty($_GET) && !empty($_GET['tahun'])) {
			echo '<h1 class="text-center">TAHUN ANGGARAN TERPILIH<br>' . $_GET['tahun'] . '</h1>';
		}
		$tahun_skpd = get_option('_crb_tahun_anggaran_sipd');

		if (empty($tahun_skpd)) {
			echo "Tahun angaran SIPD belum disetting oleh admin!";
			return;
		}

		if (empty($user_meta->roles)) {
			echo 'User ini tidak dapat akses sama sekali :)';
		} else if (in_array("mitra_bappeda", $user_meta->roles)) {
			$this->pilih_tahun_anggaran();
			if (empty($_GET) || empty($_GET['tahun'])) {
				return;
			}
			$id_user_sipd = get_user_meta($user_id, 'id_user_sipd');
			if (!empty($id_user_sipd)) {
				$title = 'Jadwal Input Perencanaan RENJA | ' . $_GET['tahun'];
				$shortcode = '[jadwal_renja tahun_anggaran="' . $_GET['tahun'] . '"]';
				$update = false;
				$page_url = $this->generatePage($title, $_GET['tahun'], $shortcode, $update);
				echo '
					<ul class="daftar-tahun text_tengah">
						<li><a href="' . $page_url . '" target="_blank" class="btn btn-warning">' . $title . '</a></li>
					</ul>';
				$skpd_mitra = $wpdb->get_results($wpdb->prepare("
					SELECT 
						m.nama_skpd, 
						m.id_unit, 
						m.kode_skpd,
						u.is_skpd
					from data_skpd_mitra_bappeda m
						inner join data_unit u on m.id_unit=u.id_skpd
							and u.active=1
							and u.tahun_anggaran=m.tahun_anggaran
					where m.active=1 
						and m.id_user=" . $id_user_sipd[0] . " 
						and m.tahun_anggaran=%d 
						and u.is_skpd=1
					group by id_unit", $tahun_skpd), ARRAY_A);
				foreach ($skpd_mitra as $k => $v) {
					$this->menu_monev_skpd(array(
						'id_skpd' => $v['id_unit'],
						'nama_skpd' => $v['nama_skpd'],
						'kode_skpd' => $v['kode_skpd']
					));
					if ($v['is_skpd'] == 1) {
						$sub_skpd_db = $wpdb->get_results($wpdb->prepare("
							SELECT 
								nama_skpd, 
								id_skpd, 
								kode_skpd,
								is_skpd
							from data_unit 
							where id_unit=%d 
								and tahun_anggaran=%d
								and is_skpd=0
							group by id_skpd", $v['id_unit'], $_GET['tahun']), ARRAY_A);
						foreach ($sub_skpd_db as $sub_skpd) {
							$this->menu_monev_skpd(array(
								'id_skpd' => $sub_skpd['id_skpd'],
								'nama_skpd' => $sub_skpd['nama_skpd'],
								'kode_skpd' => $sub_skpd['kode_skpd']
							));
						}
					}
				}
			} else {
				echo 'User ID SIPD tidak ditemukan!';
			}
		} else if (
			in_array("PA", $user_meta->roles)
			|| in_array("KPA", $user_meta->roles)
			|| in_array("PLT", $user_meta->roles)
			|| in_array("PLH", $user_meta->roles)
		) {
			$this->pilih_tahun_anggaran();
			if (empty($_GET) || empty($_GET['tahun'])) {
				return;
			}

			$daftar_tombol = $this->get_carbon_multiselect('crb_daftar_tombol_user_dashboard');
			$daftar_tombol_list = array();
			foreach ($daftar_tombol as $v) {
				$daftar_tombol_list[$v['value']] = $v['value'];
			}

			// tampil di menu monev renja dan renstra
			if (
				!empty($daftar_tombol_list[4])
				|| !empty($daftar_tombol_list[5])
			) {
				$month = date('m');
				$year = date('Y');
				if($_GET['tahun'] + 1 == $year){
					$month = 13;
				}
				$triwulan = floor($month / 3);
				if ($month % 3 == 1 && $triwulan > 0) {
				    $notif = '<h5 style="text-align: center; padding: 10px; border: 5px; background: #f5d3d3; text-decoration: underline; border-radius: 5px;">Sekarang awal bulan triwulan baru. Waktunya mengisi <b>MONEV indikator RENJA triwulan ' . $triwulan . '</b>.<br>Jaga kesehatan & semangat!</h5>';
				    echo $notif;
				}

			}
			$nipkepala = get_user_meta($user_id, '_nip');
			$skpd_db = $wpdb->get_results($wpdb->prepare("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd,
					is_skpd
				from data_unit 
				where nipkepala=%s 
					and tahun_anggaran=%d
				group by id_skpd", $nipkepala[0], $tahun_skpd), ARRAY_A);
			foreach ($skpd_db as $skpd) {
				$this->menu_monev_skpd(array(
					'id_skpd' => $skpd['id_skpd'],
					'nama_skpd' => $skpd['nama_skpd'],
					'kode_skpd' => $skpd['kode_skpd']
				));
				if ($skpd['is_skpd'] == 1) {
					$sub_skpd_db = $wpdb->get_results($wpdb->prepare("
						SELECT 
							nama_skpd, 
							id_skpd, 
							kode_skpd,
							is_skpd
						from data_unit 
						where id_unit=%d 
							and tahun_anggaran=%d
							and is_skpd=0
						group by id_skpd", $skpd['id_skpd'], $tahun_skpd), ARRAY_A);
					foreach ($sub_skpd_db as $sub_skpd) {
						$this->menu_monev_skpd(array(
							'id_skpd' => $sub_skpd['id_skpd'],
							'nama_skpd' => $sub_skpd['nama_skpd'],
							'kode_skpd' => $sub_skpd['kode_skpd']
						));
					}
				}
			}
		} else if (
			in_array("tapd_pp", $user_meta->roles)
			|| in_array("tapd_keu", $user_meta->roles)
		) {
			$this->pilih_tahun_anggaran();
			$this->tampil_menu_rpjm();
			if (empty($_GET) || empty($_GET['tahun'])) {
				return;
			}

			$skpd_mitra = $wpdb->get_results($wpdb->prepare("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd 
				from data_unit 
				where active=1 
					and tahun_anggaran=%d
				group by id_skpd", $tahun_skpd), ARRAY_A);
			foreach ($skpd_mitra as $k => $v) {
				$this->menu_monev_skpd(array(
					'id_skpd' => $v['id_skpd'],
					'nama_skpd' => $v['nama_skpd'],
					'kode_skpd' => $v['kode_skpd']
				));
			}
		} else if (in_array("pptk", $user_meta->roles)) {
			$this->pilih_tahun_anggaran();
			if (empty($_GET) || empty($_GET['tahun'])) {
				return;
			}

			$skpd_ids = get_user_meta($user_id, 'skpd');
			$ids = array();
			foreach ($skpd_ids[0] as $tahun => $id_skpd) {
				// filter hanya yang tahunnya sesuai saja
				if ($tahun == $_GET['tahun']) {
					$ids[] = $id_skpd;
				}
			}
			$skpd_mitra = $wpdb->get_results($wpdb->prepare("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd 
				from data_unit 
				where active=1 
					and tahun_anggaran=%d
					and id_skpd IN (" . implode(',', $ids) . ")
				group by id_skpd", $tahun_skpd), ARRAY_A);
			foreach ($skpd_mitra as $k => $v) {
				$this->menu_monev_skpd(array(
					'id_skpd' => $v['id_skpd'],
					'nama_skpd' => $v['nama_skpd'],
					'kode_skpd' => $v['kode_skpd'],
					'menu' => array(
						array('value' => 1), // menu 1 adalah halaman RFK saja
						array('value' => 9),	// menu 9 input renja
						array('value' => 12) 	// menu 12 input spt/sppd
					)
				));
			}
		} else {
			$role_verifikator = $this->role_verifikator();
			$cek_verifiktor = false;
			foreach ($user_meta->roles as $role) {
				if (in_array($role, $role_verifikator)) {
					$cek_verifiktor = true;
				}
			}
			if ($cek_verifiktor) {
				$this->pilih_tahun_anggaran();
				if (empty($_GET) || empty($_GET['tahun'])) {
					return;
				}
				$skpd_mitra = $wpdb->get_results($wpdb->prepare("
					SELECT 
						nama_skpd, 
						id_skpd, 
						kode_skpd 
					from data_unit 
					where active=1 
						and tahun_anggaran=%d
					group by id_skpd", $tahun_skpd), ARRAY_A);
				foreach ($skpd_mitra as $k => $v) {
					$this->menu_monev_skpd(array(
						'id_skpd' => $v['id_skpd'],
						'nama_skpd' => $v['nama_skpd'],
						'kode_skpd' => $v['kode_skpd'],
						'menu' => array(
							array('value' => 1), //menu 1 menampilkan menu RFK
							array('value' => 9) //menu 9 menampilkan menu Input Renja
						)
					));
				}
			} else {
				echo 'User ini tidak dapat akses halaman ini :)';
			}
		}
	}

	public function tampil_menu_rpjm()
	{
		if (!empty($_GET) && !empty($_GET['tahun'])) {
			global $wpdb;
			$daftar_tombol = $this->get_carbon_multiselect('crb_daftar_tombol_user_dashboard');
			$daftar_tombol_list = array();
			foreach ($daftar_tombol as $v) {
				$daftar_tombol_list[$v['value']] = $v['value'];
			}
			if (!empty($daftar_tombol_list[6])) {
				$tahun_aktif = $_GET['tahun'];
				$custom_post = $this->get_page_by_title('MONEV RPJM Pemerintah Daerah | ' . $tahun_aktif);
				$url_pemda = $this->get_link_post($custom_post);
				echo '
				<ul class="daftar-tahun text_tengah">
					<a class="btn btn-danger" target="_blank" href="' . $url_pemda . '">MONEV RPJM</a>
				</ul>';
			}
		}
	}

	public function pilih_tahun_anggaran()
	{
		global $wpdb;
		$tahun_aktif = false;
		$class_hide = '';
		if (!empty($_GET) && !empty($_GET['tahun'])) {
			$tahun_aktif = $_GET['tahun'];
			$class_hide = 'display: none;';
		}
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran', ARRAY_A);
		echo "
		<h5 class='text_tengah' style='" . $class_hide . "'>PILIH TAHUN ANGGARAN</h5>
		<ul class='daftar-tahun text_tengah'>";
		foreach ($tahun as $k => $v) {
			$class = 'btn-primary';
			if ($tahun_aktif == $v['tahun_anggaran']) {
				$class = 'btn-success';
			}
			echo "<li><a href='?tahun=" . $v['tahun_anggaran'] . "' class='btn " . $class . "'>" . $v['tahun_anggaran'] . "</a></li>";
		}
		echo "</ul>";
	}

	public function simpan_rfk()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil simpan realisasi fisik dan keuangan!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				foreach ($_POST['data'] as $k => $v) {
					$sql = $wpdb->prepare("
					    select 
					        *
					    from data_rfk
					    where tahun_anggaran=%d
					        and bulan=%d
					        and id_skpd=%d
					        and kode_sbl=%s
					", $_POST['tahun_anggaran'], $_POST['bulan'], $v['id_skpd'], $v['kode_sbl']);
					$cek = $wpdb->get_results($sql, ARRAY_A);
					$opsi = array(
						'bulan'	=> $_POST['bulan'],
						'kode_sbl'	=> $v['kode_sbl'],
						'id_skpd'	=> $v['id_skpd'],
						'tahun_anggaran'	=> $_POST['tahun_anggaran']
					);
					if (current_user_can('administrator')) {
						$opsi['catatan_verifikator'] = $v['catatan_verifikator'];
						$opsi['user_verifikator'] = $v['user_edit'];
						$opsi['update_verifikator_at'] = current_time('mysql');
					} else {
						$opsi['permasalahan'] = $v['permasalahan'];
						$opsi['realisasi_fisik'] = $v['realisasi_fisik'];
						$opsi['user_edit'] = $v['user_edit'];
						$opsi['update_fisik_at'] = current_time('mysql');
					}
					if (!empty($cek)) {
						$wpdb->update('data_rfk', $opsi, array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'bulan' => $_POST['bulan'],
							'id_skpd' => $v['id_skpd'],
							'kode_sbl' => $v['kode_sbl']
						));
					} else {
						$opsi['created_at'] = current_time('mysql');
						$wpdb->insert('data_rfk', $opsi);
					}
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

	public function reset_catatan_verifkator_rfk()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil reset catatan verifikator sesuai bulan sebelumnya!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$sql = $wpdb->prepare("
				    select 
				        *
				    from data_rfk
				    where tahun_anggaran=%d
				        and bulan=%d
				        and id_skpd=%d
				", $_POST['tahun_anggaran'], $_POST['bulan'] - 1, $_POST['id_skpd']);
				$rfk = $wpdb->get_results($sql, ARRAY_A);
				foreach ($rfk as $k => $v) {
					$sql = $wpdb->prepare("
					    select 
					        *
					    from data_rfk
					    where tahun_anggaran=%d
					        and bulan=%d
					        and id_skpd=%d
					        and kode_sbl=%s
					", $_POST['tahun_anggaran'], $_POST['bulan'], $_POST['id_skpd'], $v['kode_sbl']);
					$cek = $wpdb->get_results($sql, ARRAY_A);
					$opsi = array(
						'bulan'	=> $_POST['bulan'],
						'kode_sbl'	=> $v['kode_sbl'],
						'catatan_verifikator'	=> $v['catatan_verifikator'],
						'user_verifikator'	=> $_POST['user'],
						'id_skpd'	=> $v['id_skpd'],
						'tahun_anggaran'	=> $_POST['tahun_anggaran'],
						'update_verifikator_at'	=>  current_time('mysql')
					);
					if (!empty($cek)) {
						$wpdb->update('data_rfk', $opsi, array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'bulan' => $_POST['bulan'],
							'id_skpd' => $v['id_skpd'],
							'kode_sbl' => $v['kode_sbl']
						));
					} else {
						$wpdb->insert('data_rfk', $opsi);
					}
				}
				if (empty($rfk)) {
					$ret['status'] = 'error';
					$ret['message'] = 'Data RFK bulan sebelumnya kosong!';
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

	public function reset_rfk()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil reset data realisasi fisik dan keuangan sesuai bulan sebelumnya!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$sql = $wpdb->prepare("
				    select 
				        *
				    from data_rfk
				    where tahun_anggaran=%d
				        and bulan=%d
				        and id_skpd=%d
				", $_POST['tahun_anggaran'], $_POST['bulan'] - 1, $_POST['id_skpd']);
				$rfk = $wpdb->get_results($sql, ARRAY_A);
				foreach ($rfk as $k => $v) {
					$sql = $wpdb->prepare("
					    select 
					        *
					    from data_rfk
					    where tahun_anggaran=%d
					        and bulan=%d
					        and id_skpd=%d
					        and kode_sbl=%s
					", $_POST['tahun_anggaran'], $_POST['bulan'], $_POST['id_skpd'], $v['kode_sbl']);
					$cek = $wpdb->get_results($sql, ARRAY_A);
					$opsi = array(
						'bulan'	=> $_POST['bulan'],
						'kode_sbl'	=> $v['kode_sbl'],
						'realisasi_fisik'	=> $v['realisasi_fisik'],
						'permasalahan'	=> $v['permasalahan'],
						'user_edit'	=> $_POST['user'],
						'id_skpd'	=> $v['id_skpd'],
						'tahun_anggaran'	=> $_POST['tahun_anggaran'],
						'created_at'	=>  current_time('mysql')
					);
					if (!empty($cek)) {
						$wpdb->update('data_rfk', $opsi, array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'bulan' => $_POST['bulan'],
							'id_skpd' => $v['id_skpd'],
							'kode_sbl' => $v['kode_sbl']
						));
					} else {
						$wpdb->insert('data_rfk', $opsi);
					}
				}
				if (empty($rfk)) {
					$ret['status'] = 'error';
					$ret['message'] = 'Data RFK bulan sebelumnya kosong!';
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

	function get_pagu_simda($options = array())
	{
		global $wpdb;
		$sumber_pagu = $options['sumber_pagu'];
		$kd_urusan = $options['kd_urusan'];
		$kd_bidang = $options['kd_bidang'];
		$kd_unit = $options['kd_unit'];
		$kd_sub = $options['kd_sub'];
		$kd_prog = $options['kd_prog'];
		$id_prog = $options['id_prog'];
		$kd_keg = $options['kd_keg'];
		if (
			$sumber_pagu == 4
			|| $sumber_pagu == 5
			|| $sumber_pagu == 6
		) {
			$sql = $wpdb->prepare(
				"
				SELECT 
					SUM(r.total) as total
				FROM ta_rask_arsip r
				WHERE r.tahun = %d
					AND r.kd_perubahan = %d
					AND r.kd_urusan = %d
					AND r.kd_bidang = %d
					AND r.kd_unit = %d
					AND r.kd_sub = %d
					AND r.kd_prog = %d
					AND r.id_prog = %d
					AND r.kd_keg = %d
				",
				$options['tahun_anggaran'],
				$sumber_pagu,
				$kd_urusan,
				$kd_bidang,
				$kd_unit,
				$kd_sub,
				$kd_prog,
				$id_prog,
				$kd_keg
			);
		}
		$pagu = $this->simda->CurlSimda(array('query' => $sql));
		if (!empty($pagu[0])) {
			return $pagu[0]->total;
		} else {
			return 0;
		}
	}

	function get_pagu_simda_last($options = array())
	{
		global $wpdb;
		$kd_urusan = $options['kd_urusan'];
		$kd_bidang = $options['kd_bidang'];
		$kd_unit = $options['kd_unit'];
		$kd_sub = $options['kd_sub'];
		$kd_prog = $options['kd_prog'];
		$id_prog = $options['id_prog'];
		$kd_keg = $options['kd_keg'];
		$sql = $wpdb->prepare(
			"
			SELECT 
				SUM(r.total) as total
			FROM ta_rask_arsip r
			WHERE r.tahun = %d
				AND r.kd_perubahan = (SELECT MAX(Kd_Perubahan) FROM Ta_Rask_Arsip where tahun=%d)
				AND r.kd_urusan = %d
				AND r.kd_bidang = %d
				AND r.kd_unit = %d
				AND r.kd_sub = %d
				AND r.kd_prog = %d
				AND r.id_prog = %d
				AND r.kd_keg = %d
			",
			$options['tahun_anggaran'],
			$options['tahun_anggaran'],
			$kd_urusan,
			$kd_bidang,
			$kd_unit,
			$kd_sub,
			$kd_prog,
			$id_prog,
			$kd_keg
		);
		$pagu = $this->simda->CurlSimda(array('query' => $sql));
		if (!empty($pagu[0])) {
			$wpdb->update('data_sub_keg_bl', array('pagu_simda' => $pagu[0]->total), array(
				'id' => $options['id_sub_keg']
			));
			return $pagu[0]->total;
		} else {
			return $options['pagu_simda'];
		}
	}

	function get_pagu_simda_rka($options = array())
	{
		global $wpdb;
		$kd_urusan = $options['kd_urusan'];
		$kd_bidang = $options['kd_bidang'];
		$kd_unit = $options['kd_unit'];
		$kd_sub = $options['kd_sub'];
		$kd_prog = $options['kd_prog'];
		$id_prog = $options['id_prog'];
		$kd_keg = $options['kd_keg'];
		$sql = $wpdb->prepare(
			"
			SELECT 
				SUM(r.total) as total
			FROM ta_belanja_rinc_sub r
			WHERE r.tahun = %d
				AND r.kd_urusan = %d
				AND r.kd_bidang = %d
				AND r.kd_unit = %d
				AND r.kd_sub = %d
				AND r.kd_prog = %d
				AND r.id_prog = %d
				AND r.kd_keg = %d
			",
			$options['tahun_anggaran'],
			$kd_urusan,
			$kd_bidang,
			$kd_unit,
			$kd_sub,
			$kd_prog,
			$id_prog,
			$kd_keg
		);
		$pagu = $this->simda->CurlSimda(array('query' => $sql));
		if (!empty($pagu[0])) {
			return $pagu[0]->total;
		} else {
			return $options['pagu_simda'];
		}
	}

	function get_rak_simda($options = array())
	{
		global $wpdb;
		$kd_urusan = $options['kd_urusan'];
		$kd_bidang = $options['kd_bidang'];
		$kd_unit = $options['kd_unit'];
		$kd_sub = $options['kd_sub'];
		$kd_prog = $options['kd_prog'];
		$id_prog = $options['id_prog'];
		$kd_keg = $options['kd_keg'];

		$sql_akun = "";
		if (!empty($options['kode_akun'])) {
			$akun = explode('.', $options['kode_akun']);
			$mapping_rek = $this->simda->cekRekMapping(array(
				'tahun_anggaran' => $options['tahun_anggaran'],
				'kode_akun' => $options['kode_akun'],
				'kd_rek_0' => $akun[0],
				'kd_rek_1' => $akun[1],
				'kd_rek_2' => $akun[2],
				'kd_rek_3' => $akun[3],
				'kd_rek_4' => $akun[4],
				'kd_rek_5' => $akun[5],
			));
			$sql_akun = $wpdb->prepare(
				"
            	AND r.kd_rek_1 = %d
            	AND r.kd_rek_2 = %d
            	AND r.kd_rek_3 = %d
            	AND r.kd_rek_4 = %d
            	AND r.kd_rek_5 = %d
            	",
				$mapping_rek[0]->kd_rek_1,
				$mapping_rek[0]->kd_rek_2,
				$mapping_rek[0]->kd_rek_3,
				$mapping_rek[0]->kd_rek_4,
				$mapping_rek[0]->kd_rek_5
			);
		}

		if (!empty($options['rak_all'])) {
			$sql = $wpdb->prepare(
				"
				SELECT 
					m.kd_rek90_1,
					m.kd_rek90_2,
					m.kd_rek90_3,
					m.kd_rek90_4,
					m.kd_rek90_5,
					m.kd_rek90_6,
					r6.nm_rek90_6 as nama_akun,
					r.jan as bulan_1,
					r.feb as bulan_2,
					r.mar as bulan_3,
					r.apr as bulan_4,
					r.mei as bulan_5,
					r.jun as bulan_6,
					r.jul as bulan_7,
					r.agt as bulan_8,
					r.sep as bulan_9,
					r.okt as bulan_10,
					r.nop as bulan_11,
					r.des as bulan_12
				FROM ta_rencana r
					LEFT JOIN ref_rek_mapping m on m.kd_rek_1=r.kd_rek_1
						and m.kd_rek_2=r.kd_rek_2
						and m.kd_rek_3=r.kd_rek_3
						and m.kd_rek_4=r.kd_rek_4
					LEFT JOIN ref_rek90_6 r6 on m.kd_rek90_1=r6.kd_rek90_1
						and m.kd_rek90_2=r6.kd_rek90_2
						and m.kd_rek90_3=r6.kd_rek90_3
						and m.kd_rek90_4=r6.kd_rek90_4
						and m.kd_rek90_5=r6.kd_rek90_5
						and m.kd_rek90_6=r6.kd_rek90_6
				WHERE r.tahun = %d 
					AND r.kd_urusan = %d
					AND r.kd_bidang = %d
					AND r.kd_unit = %d
					AND r.kd_sub = %d
					AND r.kd_prog = %d
					AND r.id_prog = %d
					AND r.kd_keg = %d
				",
				$options['tahun_anggaran'],
				$kd_urusan,
				$kd_bidang,
				$kd_unit,
				$kd_sub,
				$kd_prog,
				$id_prog,
				$kd_keg
			);
			$rak = $this->simda->CurlSimda(array('query' => $sql . $sql_akun), false);
			$ret = array();
			foreach ($rak as $key => $val) {
				$ret[$key] = array();
				foreach ($val as $k => $v) {
					if (!is_numeric($k)) {
						$ret[$key][$k] = $v;
					}
				}
				$ret[$key]['kode_akun'] = $val->kd_rek90_1 . '.' . $val->kd_rek90_2 . '.' . $this->simda->CekNull($val->kd_rek90_3) . '.' . $this->simda->CekNull($val->kd_rek90_4) . '.' . $this->simda->CekNull($val->kd_rek90_5) . '.' . $this->simda->CekNull($val->kd_rek90_6, 4);
			}
			return $ret;
		} else {
			$sql = $wpdb->prepare(
				"
				SELECT 
					sum(r.jan) as bulan_1,
					sum(r.feb) as bulan_2,
					sum(r.mar) as bulan_3,
					sum(r.apr) as bulan_4,
					sum(r.mei) as bulan_5,
					sum(r.jun) as bulan_6,
					sum(r.jul) as bulan_7,
					sum(r.agt) as bulan_8,
					sum(r.sep) as bulan_9,
					sum(r.okt) as bulan_10,
					sum(r.nop) as bulan_11,
					sum(r.des) as bulan_12
				FROM ta_rencana r
				WHERE r.tahun = %d 
					AND r.kd_urusan = %d
					AND r.kd_bidang = %d
					AND r.kd_unit = %d
					AND r.kd_sub = %d
					AND r.kd_prog = %d
					AND r.id_prog = %d
					AND r.kd_keg = %d
				",
				$options['tahun_anggaran'],
				$kd_urusan,
				$kd_bidang,
				$kd_unit,
				$kd_sub,
				$kd_prog,
				$id_prog,
				$kd_keg
			);
			$rak = $this->simda->CurlSimda(array('query' => $sql . $sql_akun), false);
		}

		$total_rak = 0;
		if (empty($rak[0])) {
			return $options['rak'];
		} else {
			for ($i = 1; $i <= $options['bulan']; $i++) {
				$total_rak += $rak[0]->{'bulan_' . $i};
			}
		}
		if (!empty($options['kode_akun'])) {
			$opsi = array(
				'rak' => $total_rak,
				'kode_akun'	=> $options['kode_akun'],
				'kode_sbl'	=> $options['kode_sbl'],
				'id_skpd'	=> $options['id_skpd'],
				'user'	=> $options['user'],
				'tahun_anggaran'	=> $options['tahun_anggaran'],
				'active'	=> 1,
				'update_at'	=>  current_time('mysql')
			);
			if (!empty($options['id_realisasi_akun'])) {
				$wpdb->update('data_realisasi_akun', $opsi, array(
					'id' => $options['id_realisasi_akun']
				));
			} else {
				$wpdb->insert('data_realisasi_akun', $opsi);
			}
		} else {
			$sql = $wpdb->prepare("
			    select 
			        id
			    from data_rfk
			    where tahun_anggaran=%d
			        and bulan=%d
			        and id_skpd=%d
			        and kode_sbl=%s
			", $options['tahun_anggaran'], $options['bulan'], $options['id_skpd'], $options['kode_sbl']);
			$cek = $wpdb->get_results($sql, ARRAY_A);
			$opsi = array(
				'bulan'	=> $options['bulan'],
				'kode_sbl'	=> $options['kode_sbl'],
				'rak' => $total_rak,
				'user_edit'	=> $options['user'],
				'id_skpd'	=> $options['id_skpd'],
				'tahun_anggaran'	=> $options['tahun_anggaran'],
				'created_at'	=>  current_time('mysql')
			);
			if (!empty($cek)) {
				$wpdb->update('data_rfk', $opsi, array(
					'tahun_anggaran' => $options['tahun_anggaran'],
					'bulan' => $options['bulan'],
					'id_skpd' => $options['id_skpd'],
					'kode_sbl' => $options['kode_sbl']
				));
			} else {
				$wpdb->insert('data_rfk', $opsi);
			}
		}
		return $total_rak;
	}

	function get_rak_sipd_rfk($options = array())
	{
		global $wpdb;
		$total_rak = 0;
		$data_kas = $wpdb->get_row($wpdb->prepare("
			SELECT
				SUM(bulan_1) as bulan_1,
				SUM(bulan_2) as bulan_2,
				SUM(bulan_3) as bulan_3,
				SUM(bulan_4) as bulan_4,
				SUM(bulan_5) as bulan_5,
				SUM(bulan_6) as bulan_6,
				SUM(bulan_7) as bulan_7,
				SUM(bulan_8) as bulan_8,
				SUM(bulan_9) as bulan_9,
				SUM(bulan_10) as bulan_10,
				SUM(bulan_11) as bulan_11,
				SUM(bulan_12) as bulan_12
			FROM data_anggaran_kas
			WHERE kode_sbl_sub_keg=%s
				AND id_sub_skpd=%d
				AND active=1
				AND type='belanja'
				AND tahun_anggaran=%d
		", $options['kode_sbl'], $options['id_skpd'], $options['tahun_anggaran']), ARRAY_A);
		// die($wpdb->last_query);
		if (empty($data_kas)) {
			return $options['rak'];
		} else {
			for ($i = 1; $i <= $options['bulan']; $i++) {
				$total_rak += $data_kas['bulan_' . $i];
			}
		}
		if (
			empty($options['cek_insert'])
			&& $total_rak == $options['rak']
		) {
			return $total_rak;
		} else {
			$sql = $wpdb->prepare("
			    select 
			        id
			    from data_rfk
			    where tahun_anggaran=%d
			        and bulan=%d
			        and id_skpd=%d
			        and kode_sbl=%s
			", $options['tahun_anggaran'], $options['bulan'], $options['id_skpd'], $options['kode_sbl']);
			$cek_id = $wpdb->get_var($sql);
			$opsi = array(
				'bulan'	=> $options['bulan'],
				'kode_sbl'	=> $options['kode_sbl'],
				'rak' => $total_rak,
				'user_edit'	=> $options['user'],
				'id_skpd'	=> $options['id_skpd'],
				'tahun_anggaran'	=> $options['tahun_anggaran'],
				'created_at'	=>  current_time('mysql')
			);
			if (!empty($cek_id)) {
				$wpdb->update('data_rfk', $opsi, array(
					'id' => $cek_id
				));
			} else {
				$wpdb->insert('data_rfk', $opsi);
			}
			return $total_rak;
		}
	}

	function get_realisasi_simda($options = array())
	{
		global $wpdb;
		$kd_urusan = $options['kd_urusan'];
		$kd_bidang = $options['kd_bidang'];
		$kd_unit = $options['kd_unit'];
		$kd_sub = $options['kd_sub'];
		$kd_prog = $options['kd_prog'];
		$id_prog = $options['id_prog'];
		$kd_keg = $options['kd_keg'];
		$hari_mulai = $options['tahun_anggaran'] . '-01-01';
		$hari_akhir = $options['tahun_anggaran'] . '-' . $this->simda->CekNull($options['bulan']) . '-01';
		$hari_akhir = date("Y-m-t", strtotime($hari_akhir));

		$sql_akun = "";
		if (!empty($options['kode_akun'])) {
			$akun = explode('.', $options['kode_akun']);
			$mapping_rek = $this->simda->cekRekMapping(array(
				'tahun_anggaran' => $options['tahun_anggaran'],
				'kode_akun' => $options['kode_akun'],
				'kd_rek_0' => $akun[0],
				'kd_rek_1' => $akun[1],
				'kd_rek_2' => $akun[2],
				'kd_rek_3' => $akun[3],
				'kd_rek_4' => $akun[4],
				'kd_rek_5' => $akun[5],
			));
			$sql_akun = $wpdb->prepare(
				"
            	AND r.kd_rek_1 = %d
            	AND r.kd_rek_2 = %d
            	AND r.kd_rek_3 = %d
            	AND r.kd_rek_4 = %d
            	AND r.kd_rek_5 = %d
            	",
				$mapping_rek[0]->kd_rek_1,
				$mapping_rek[0]->kd_rek_2,
				$mapping_rek[0]->kd_rek_3,
				$mapping_rek[0]->kd_rek_4,
				$mapping_rek[0]->kd_rek_5
			);
		}

		/* SPM dan SP2D */
		$sql = $wpdb->prepare(
			"
			SELECT  sum(r.nilai) as total
			FROM ta_spm_rinc r
				inner join ta_spm s ON r.tahun = s.tahun 
					AND r.no_spm = s.no_spm
				inner join ta_sp2d p ON s.tahun = p.tahun
					AND s.no_spm = p.no_spm 
			WHERE r.tahun = %d 
				AND p.no_sp2d is NOT NULL
				AND s.jn_spm NOT IN (1,4)
				AND r.kd_rek_1 NOT IN (6)
				AND p.tgl_sp2d BETWEEN %s AND %s
				AND r.Kd_Urusan = %d
				AND r.Kd_Bidang = %d
				AND r.Kd_Unit = %d
				AND r.Kd_Sub = %d
				AND r.kd_prog = %d
				AND r.id_prog = %d
				AND r.kd_keg = %d
			",
			$options['tahun_anggaran'],
			$hari_mulai,
			$hari_akhir,
			$kd_urusan,
			$kd_bidang,
			$kd_unit,
			$kd_sub,
			$kd_prog,
			$id_prog,
			$kd_keg
		);
		$pagu_sp2d = $this->simda->CurlSimda(array('query' => $sql . $sql_akun), false);

		if (empty($pagu_sp2d[0])) {
			return $options['realisasi_anggaran'];
		}

		/* Penyesuaian */
		$sql = $wpdb->prepare(
			"
			SELECT  sum(r.nilai) as total
			FROM ta_penyesuaian p
				inner join ta_penyesuaian_rinc r ON p.tahun = r.tahun 
					AND p.no_bukti = r.no_bukti
			WHERE r.tahun = %d 
				AND r.d_k = 'K'
				AND p.jns_p1 = 1
				AND p.jns_p2 = 3
				AND p.tgl_bukti BETWEEN %s AND %s
				AND r.Kd_Urusan = %d
				AND r.Kd_Bidang = %d
				AND r.Kd_Unit = %d
				AND r.Kd_Sub = %d
				AND r.kd_prog = %d
				AND r.id_prog = %d
				AND r.kd_keg = %d
			",
			$options['tahun_anggaran'],
			$hari_mulai,
			$hari_akhir,
			$kd_urusan,
			$kd_bidang,
			$kd_unit,
			$kd_sub,
			$kd_prog,
			$id_prog,
			$kd_keg
		);
		$pagu_penyesuaian = $this->simda->CurlSimda(array('query' => $sql . $sql_akun), false);

		/* Jurnal Koreksi */
		$sql = $wpdb->prepare(
			"
			SELECT
				sum(r.debet) as total_debet,
				sum(r.kredit) as total_kredit
			FROM ta_jurnalsemua j
				inner join ta_jurnalsemua_rinc r ON j.tahun = r.tahun 
					AND j.kd_source = r.kd_source 
					AND j.no_bukti = r.no_bukti
			WHERE r.tahun = %d 
				AND r.kd_jurnal = 5
				AND r.kd_rek_1 = 5
				AND j.tgl_bukti BETWEEN %s AND %s
				AND j.Kd_Urusan = %d
				AND j.Kd_Bidang = %d
				AND j.Kd_Unit = %d
				AND j.Kd_Sub = %d
				AND r.kd_prog = %d
				AND r.id_prog = %d
				AND r.kd_keg = %d
			",
			$options['tahun_anggaran'],
			$hari_mulai,
			$hari_akhir,
			$kd_urusan,
			$kd_bidang,
			$kd_unit,
			$kd_sub,
			$kd_prog,
			$id_prog,
			$kd_keg
		);
		$pagu_koreksi = $this->simda->CurlSimda(array('query' => $sql . $sql_akun), false);

		$realisasi = $pagu_sp2d[0]->total - $pagu_penyesuaian[0]->total + $pagu_koreksi[0]->total_debet - $pagu_koreksi[0]->total_kredit;

		/* Jurnal BLUD / FKTP */
		if (
			$kd_urusan == 1
			and $kd_bidang == 2
		) {
			$sql = $wpdb->prepare(
				"
				SELECT
					sum(r.nilai) as total
				FROM ta_jurnal_rinc r
					inner join ta_jurnal j ON j.tahun = r.tahun 
						AND j.no_bukti = r.no_bukti
				WHERE r.tahun = %d 
					AND j.kd_jurnal = 5
					AND r.kd_rek_1 = 5
					AND j.tgl_bukti BETWEEN %s AND %s
					AND r.Kd_Urusan = %d
					AND r.Kd_Bidang = %d
					AND r.Kd_Unit = %d
					AND r.Kd_Sub = %d
					AND r.kd_prog = %d
					AND r.id_prog = %d
					AND r.kd_keg = %d
				",
				$options['tahun_anggaran'],
				$hari_mulai,
				$hari_akhir,
				$kd_urusan,
				$kd_bidang,
				$kd_unit,
				$kd_sub,
				$kd_prog,
				$id_prog,
				$kd_keg
			);
			$pagu_blud_fktp = $this->simda->CurlSimda(array('query' => $sql . $sql_akun), false);

			$realisasi = $realisasi + $pagu_blud_fktp[0]->total;
		}

		/* Jurnal BLUD / FKTP + SIMDA BOS */
		if (
			$kd_urusan == 1
			and (
				$kd_bidang == 1
				|| $kd_bidang == 2
			)
		) {
			$sql = $wpdb->prepare(
				"
				SELECT
					sum(r.nilai) as total
				FROM ta_sp3b_rinc r
					inner join ta_sp3b s ON s.tahun = r.tahun 
						AND s.no_sp3b = r.no_sp3b
				WHERE r.tahun = %d 
					AND r.kd_rek_1 = 5
					AND s.tgl_sp3b BETWEEN %s AND %s
					AND r.Kd_Urusan = %d
					AND r.Kd_Bidang = %d
					AND r.Kd_Unit = %d
					AND r.Kd_Sub = %d
					AND r.kd_prog = %d
					AND r.id_prog = %d
					AND r.kd_keg = %d
				",
				$options['tahun_anggaran'],
				$hari_mulai,
				$hari_akhir,
				$kd_urusan,
				$kd_bidang,
				$kd_unit,
				$kd_sub,
				$kd_prog,
				$id_prog,
				$kd_keg
			);
			$pagu_sp3b = $this->simda->CurlSimda(array('query' => $sql . $sql_akun), false);

			$realisasi = $realisasi + $pagu_sp3b[0]->total;
		}

		if (!empty($options['kode_akun'])) {
			$opsi = array(
				'realisasi' => $realisasi,
				'kode_akun'	=> $options['kode_akun'],
				'kode_sbl'	=> $options['kode_sbl'],
				'id_skpd'	=> $options['id_skpd'],
				'user'	=> $options['user'],
				'tahun_anggaran'	=> $options['tahun_anggaran'],
				'active'	=> 1,
				'update_at'	=>  current_time('mysql')
			);
			if (!empty($options['id_realisasi_akun'])) {
				$wpdb->update('data_realisasi_akun', $opsi, array(
					'id' => $options['id_realisasi_akun']
				));
			} else {
				$wpdb->insert('data_realisasi_akun', $opsi);
			}
		} else {
			$sql = $wpdb->prepare("
			    select 
			        id
			    from data_rfk
			    where tahun_anggaran=%d
			        and bulan=%d
			        and id_skpd=%d
			        and kode_sbl=%s
			", $options['tahun_anggaran'], $options['bulan'], $options['id_skpd'], $options['kode_sbl']);
			$cek = $wpdb->get_results($sql, ARRAY_A);
			$opsi = array(
				'bulan'	=> $options['bulan'],
				'kode_sbl'	=> $options['kode_sbl'],
				'realisasi_anggaran' => $realisasi,
				'user_edit'	=> $options['user'],
				'id_skpd'	=> $options['id_skpd'],
				'tahun_anggaran'	=> $options['tahun_anggaran'],
				'created_at'	=>  current_time('mysql')
			);
			if (!empty($cek)) {
				$wpdb->update('data_rfk', $opsi, array(
					'tahun_anggaran' => $options['tahun_anggaran'],
					'bulan' => $options['bulan'],
					'id_skpd' => $options['id_skpd'],
					'kode_sbl' => $options['kode_sbl']
				));
			} else {
				$wpdb->insert('data_rfk', $opsi);
			}
		}

		/*
			Nilai realisasi adalah total dari:
			REALISASI = Nilai SPM (SP2D) - Nilai Penyesuaian (ta_penyesuaian) + Nilai Jurnal Koreksi Debet (ta_jurnalsemua) - Nilai Jurnal Koreksi Kredit (ta_jurnal_semua)

			Khusus belanja BLUD dan FKTP maka ditambahkan:
			REALISASI = REALISASI + Jurnal BLUD / FKTP (ta_jurnal)
		*/

		return $realisasi;
	}

	function pembulatan($angka)
	{
		$angka = $angka * 100;
		return round($angka) / 100;
	}

	function get_mapping()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil get data mapping!';
		$ret['data'] = array();
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['id_mapping'])) {
					foreach ($_POST['id_mapping'] as $k => $id_unik) {
						$ids = explode('-', $id_unik);
						$kd_sbl = $ids[0];
						$rek = explode('.', $ids[1]);
						$rek_1 = $rek[0] . '.' . $rek[1];
						$rek_2 = false;
						$rek_3 = false;
						$rek_4 = false;
						$rek_5 = false;
						$kelompok = false;
						$keterangan = false;
						$id_rinci = false;
						if (isset($rek[2])) {
							$rek_2 = $rek_1 . '.' . $rek[2];
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
						if (isset($ids[2])) {
							$kelompok = $ids[2];
						}
						if (isset($ids[3])) {
							$keterangan = $ids[3];
						}
						if (isset($ids[4])) {
							$id_rinci = $ids[4];
						}
						$res = array('id_unik' => $id_unik);
						$res['data_realisasi'] = $wpdb->get_var(
							$wpdb->prepare('
								SELECT realisasi
								FROM data_realisasi_rincian
								WHERE tahun_anggaran=%d
								  AND id_rinci_sub_bl=%d
								  AND active=1
							', $_POST['tahun_anggaran'], $id_rinci)
						);
						if (empty($res['data_realisasi'])) {
							$res['data_realisasi'] = 0;
						}
						$res['data_label'] = $wpdb->get_results(
							$wpdb->prepare('
								SELECT 
									l.nama,
									l.id
								FROM data_mapping_label m
								LEFT JOIN data_label_komponen l 
									   ON m.id_label_komponen=l.id
								WHERE m.tahun_anggaran=%d
								  AND m.id_rinci_sub_bl=%d
								  AND m.active=1
							', $_POST['tahun_anggaran'], $id_rinci)
						);
						$sql = $wpdb->prepare('
							SELECT 
								s.nama_dana,
								s.id_dana as id
							FROM data_mapping_sumberdana m
							LEFT JOIN data_sumber_dana s 
								   ON m.id_sumber_dana=s.id_dana
								  AND s.tahun_anggaran=m.tahun_anggaran
							WHERE m.tahun_anggaran=%d
							  AND m.id_rinci_sub_bl=%d
							  AND m.active=1
							', $_POST['tahun_anggaran'], $id_rinci);
						$res['data_sumber_dana'] = $wpdb->get_results($sql);

						if ($_POST['realisasi_bku'] === 'true') {
							$res['realisasi_bku'] = $wpdb->get_var(
								$wpdb->prepare('
									SELECT SUM(pagu)
									FROM data_buku_kas_umum_pembantu
									WHERE tahun_anggaran=%d
									  AND id_rinci_sub_bl=%d
									  AND active=1
								', $_POST['tahun_anggaran'], $id_rinci)
							);
						}
						$ret['data'][] = $res;
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Id mapping tidak boleh kosong!';
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

	function singkron_renstra_tujuan()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil singkron tujuan RENSTRA!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$wpdb->update('data_renstra_tujuan', array('active' => 0), array(
					'tahun_anggaran' => $_POST['tahun_anggaran']
				));
				if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
					$tujuan = json_decode(stripslashes(html_entity_decode($_POST['tujuan'])), true);
				} else {
					$tujuan = $_POST['tujuan'];
				}
				foreach ($tujuan as $k => $v) {
					if (empty($v['id_unik_indikator'])) {
						$v['id_unik_indikator'] = '';
					}
					if (empty($v['id_unit'])) {
						$v['id_unit'] = '';
					}
					if (empty($v['id_unik'])) {
						$v['id_unik'] = '';
					}
					$cek = $wpdb->get_var("
						SELECT 
							id_unik 
						from data_renstra_tujuan 
						where tahun_anggaran=" . $_POST['tahun_anggaran'] . " 
							AND id_unik='" . $v['id_unik'] . "' 
							AND id_unik_indikator='" . $v['id_unik_indikator'] . "' 
							AND id_unit='" . $v['id_unit'] . "'
							AND id_bidang_urusan='" . $v['id_bidang_urusan'] . "'
					");
					$opsi = array(
						'bidur_lock' => $v['bidur_lock'],
						'id_bidang_urusan' => $v['id_bidang_urusan'],
						'id_unik' => $v['id_unik'],
						'id_unik_indikator' => $v['id_unik_indikator'],
						'id_unit' => $v['id_unit'],
						'indikator_teks' => $v['indikator_teks'],
						'is_locked' => $v['is_locked'],
						'is_locked_indikator' => $v['is_locked_indikator'],
						'kode_bidang_urusan' => $v['kode_bidang_urusan'],
						'kode_sasaran_rpjm' => $v['kode_sasaran_rpjm'],
						'kode_skpd' => $v['kode_skpd'],
						'nama_bidang_urusan' => $v['nama_bidang_urusan'],
						'nama_skpd' => $v['nama_skpd'],
						'satuan' => $v['satuan'],
						'status' => $v['status'],
						'target_1' => $v['target_1'],
						'target_2' => $v['target_2'],
						'target_3' => $v['target_3'],
						'target_4' => $v['target_4'],
						'target_5' => $v['target_5'],
						'target_akhir' => $v['target_akhir'],
						'target_awal' => $v['target_awal'],
						'tujuan_teks' => $v['tujuan_teks'],
						'urut_tujuan' => $v['urut_tujuan'],
						'id_tujuan' => $v['id_tujuan'],
						'perlu_mutakhirkan' => $v['perlu_mutakhirkan'],
						'id_visi' => $v['id_visi'],
						'id_misi' => $v['id_misi'],
						'id_tahap' => $v['id_tahap'],
						'tahun_awal' => $v['tahun_awal'],
						'tahun_akhir' => $v['tahun_akhir'],
						'active' => 1,
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);

					if (!empty($cek)) {
						$wpdb->update('data_renstra_tujuan', $opsi, array(
							'id_bidang_urusan' => $v['id_bidang_urusan'],
							'id_unit' => $v['id_unit'],
							'id_unik' => $v['id_unik'],
							'id_unik_indikator' => $v['id_unik_indikator'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_renstra_tujuan', $opsi);
					}
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

	function singkron_renstra_sasaran()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil singkron sasaran RENSTRA!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$wpdb->update('data_renstra_sasaran', array('active' => 0), array(
					'tahun_anggaran' => $_POST['tahun_anggaran']
				));
				if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
					$sasaran = json_decode(stripslashes(html_entity_decode($_POST['sasaran'])), true);
				} else {
					$sasaran = $_POST['sasaran'];
				}
				foreach ($sasaran as $k => $v) {
					if (empty($v['id_unik'])) {
						$v['id_unik'] = '0';
					}
					if (empty($v['id_unik_indikator'])) {
						$v['id_unik_indikator'] = '0';
					}
					if (empty($v['id_bidang_urusan'])) {
						$v['id_bidang_urusan'] = '0';
					}
					$cek = $wpdb->get_var("
						SELECT 
							id_unik 
						from data_renstra_sasaran 
						where tahun_anggaran=" . $_POST['tahun_anggaran'] . " 
							AND id_unik='" . $v['id_unik'] . "' 
							AND id_unik_indikator='" . $v['id_unik_indikator'] . "'
							AND id_bidang_urusan='" . $v['id_bidang_urusan'] . "'
					");
					$opsi = array(
						'bidur_lock' => $v['bidur_lock'],
						'id_bidang_urusan' => $v['id_bidang_urusan'],
						'id_misi' => $v['id_misi'],
						'id_unik' => $v['id_unik'],
						'id_unik_indikator' => $v['id_unik_indikator'],
						'id_unit' => $v['id_unit'],
						'id_visi' => $v['id_visi'],
						'indikator_teks' => $v['indikator_teks'],
						'is_locked' => $v['is_locked'],
						'is_locked_indikator' => $v['is_locked_indikator'],
						'kode_bidang_urusan' => $v['kode_bidang_urusan'],
						'kode_skpd' => $v['kode_skpd'],
						'kode_tujuan' => $v['kode_tujuan'],
						'nama_bidang_urusan' => $v['nama_bidang_urusan'],
						'nama_skpd' => $v['nama_skpd'],
						'sasaran_teks' => $v['sasaran_teks'],
						'satuan' => $v['satuan'],
						'status' => $v['status'],
						'target_1' => $v['target_1'],
						'target_2' => $v['target_2'],
						'target_3' => $v['target_3'],
						'target_4' => $v['target_4'],
						'target_5' => $v['target_5'],
						'target_akhir' => $v['target_akhir'],
						'target_awal' => $v['target_awal'],
						'tujuan_lock' => $v['tujuan_lock'],
						'tujuan_teks' => $v['tujuan_teks'],
						'urut_sasaran' => $v['urut_sasaran'],
						'urut_tujuan' => $v['urut_tujuan'],
						'id_sasaran' => $v['id_sasaran'],
						'id_tujuan_indikator' => $v['id_tujuan_indikator'],
						'kode_sasaran_rpjm' => $v['kode_sasaran_rpjm'],
						'id_tahap' => $v['id_tahap'],
						'tahun_awal' => $v['tahun_awal'],
						'tahun_akhir' => $v['tahun_akhir'],
						'active' => 1,
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);

					if (!empty($cek)) {
						$wpdb->update('data_renstra_sasaran', $opsi, array(
							'id_unik' => $v['id_unik'],
							'id_unik_indikator' => $v['id_unik_indikator'],
							'id_bidang_urusan' => $v['id_bidang_urusan'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_renstra_sasaran', $opsi);
					}
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

	function singkron_renstra_program()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil singkron program RENSTRA!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (
					empty($_POST['page'])
					|| $_POST['page'] == 1
				) {
					$wpdb->update('data_renstra_program', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
				}
				if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
					$program = json_decode(stripslashes(html_entity_decode($_POST['program'])), true);
				} else {
					$program = $_POST['program'];
				}
				foreach ($program as $k => $v) {
					if (empty($v['id_unik'])) {
						$v['id_unik'] = '0';
					}
					if (empty($v['id_unik_indikator'])) {
						$v['id_unik_indikator'] = '0';
					}
					if (empty($v['id_program'])) {
						$v['id_program'] = '0';
					}
					if (empty($v['id_bidang_urusan'])) {
						$v['id_bidang_urusan'] = '0';
					}
					if (empty($v['id_visi'])) {
						$v['id_visi'] = '0';
					}
					if (empty($v['id_misi'])) {
						$v['id_misi'] = '0';
					}
					if (empty($v['urut_tujuan'])) {
						$v['urut_tujuan'] = '0';
					}
					if (empty($v['urut_sasaran'])) {
						$v['urut_sasaran'] = '0';
					}
					$cek = $wpdb->get_var("
						SELECT 
							id_unik 
						from data_renstra_program 
						where tahun_anggaran=" . $_POST['tahun_anggaran'] . " 
							AND id_unik='" . $v['id_unik'] . "' 
							AND id_unik_indikator='" . $v['id_unik_indikator'] . "'
							AND id_program='" . $v['id_program'] . "'
							AND id_bidang_urusan='" . $v['id_bidang_urusan'] . "'
							AND id_visi='" . $v['id_visi'] . "'
							AND id_misi='" . $v['id_misi'] . "'
							AND urut_tujuan='" . $v['urut_tujuan'] . "'
							AND urut_sasaran='" . $v['urut_sasaran'] . "'
					");
					$opsi = array(
						'bidur_lock' => $v['bidur_lock'],
						'id_bidang_urusan' => $v['id_bidang_urusan'],
						'id_misi' => $v['id_misi'],
						'id_program' => $v['id_program'],
						'id_unik' => $v['id_unik'],
						'id_unik_indikator' => $v['id_unik_indikator'],
						'id_unit' => $v['id_unit'],
						'id_visi' => $v['id_visi'],
						'indikator' => $v['indikator'],
						'is_locked' => $v['is_locked'],
						'is_locked_indikator' => $v['is_locked_indikator'],
						'kode_bidang_urusan' => $v['kode_bidang_urusan'],
						'kode_program' => $v['kode_program'],
						'kode_sasaran' => $v['kode_sasaran'],
						'kode_skpd' => $v['kode_skpd'],
						'kode_tujuan' => $v['kode_tujuan'],
						'nama_bidang_urusan' => $v['nama_bidang_urusan'],
						'nama_program' => $v['nama_program'],
						'nama_skpd' => $v['nama_skpd'],
						'pagu_1' => $v['pagu_1'],
						'pagu_2' => $v['pagu_2'],
						'pagu_3' => $v['pagu_3'],
						'pagu_4' => $v['pagu_4'],
						'pagu_5' => $v['pagu_5'],
						'program_lock' => $v['program_lock'],
						'sasaran_lock' => $v['sasaran_lock'],
						'sasaran_teks' => $v['sasaran_teks'],
						'satuan' => $v['satuan'],
						'status' => $v['status'],
						'target_1' => $v['target_1'],
						'target_2' => $v['target_2'],
						'target_3' => $v['target_3'],
						'target_4' => $v['target_4'],
						'target_5' => $v['target_5'],
						'target_akhir' => $v['target_akhir'],
						'target_awal' => $v['target_awal'],
						'tujuan_lock' => $v['tujuan_lock'],
						'tujuan_teks' => $v['tujuan_teks'],
						'urut_sasaran' => $v['urut_sasaran'],
						'urut_tujuan' => $v['urut_tujuan'],
						'pagu_awal' => $v['pagu_awal'],
						'pagu_akhir' => $v['pagu_akhir'],
						'id_sasaran_indikator' => $v['id_sasaran_indikator'],
						'id_renstra_program' => $v['id_renstra_program'],
						'kode_sasaran_rpjm' => $v['kode_sasaran_rpjm'],
						'id_tahap' => $v['id_tahap'],
						'tahun_awal' => $v['tahun_awal'],
						'tahun_akhir' => $v['tahun_akhir'],
						'active' => 1,
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);

					if (!empty($cek)) {
						$wpdb->update('data_renstra_program', $opsi, array(
							'id_unik' => $v['id_unik'],
							'id_unik_indikator' => $v['id_unik_indikator'],
							'id_bidang_urusan' => $v['id_bidang_urusan'],
							'id_program' => $v['id_program'],
							'id_visi' => $v['id_visi'],
							'id_misi' => $v['id_misi'],
							'urut_tujuan' => $v['urut_tujuan'],
							'urut_sasaran' => $v['urut_sasaran'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_renstra_program', $opsi);
					}
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

	function singkron_renstra_kegiatan()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil singkron kegiatan RENSTRA!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (
					empty($_POST['page'])
					|| $_POST['page'] == 1
				) {
					$wpdb->update('data_renstra_kegiatan', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
				}

				if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
					$kegiatan = json_decode(stripslashes(html_entity_decode($_POST['kegiatan'])), true);
				} else {
					$kegiatan = $_POST['kegiatan'];
				}

				foreach ($kegiatan as $k => $v) {
					if (empty($v['id_unik'])) {
						$v['id_unik'] = '0';
					}
					if (empty($v['id_unik_indikator'])) {
						$v['id_unik_indikator'] = '0';
					}
					if (empty($v['id_bidang_urusan'])) {
						$v['id_bidang_urusan'] = '0';
					}
					if (empty($v['id_visi'])) {
						$v['id_visi'] = '0';
					}
					if (empty($v['id_misi'])) {
						$v['id_misi'] = '0';
					}
					if (empty($v['urut_tujuan'])) {
						$v['urut_tujuan'] = '0';
					}
					if (empty($v['urut_sasaran'])) {
						$v['urut_sasaran'] = '0';
					}
					$cek = $wpdb->get_var("
						SELECT 
							id_unik 
						from data_renstra_kegiatan 
						where tahun_anggaran=" . $_POST['tahun_anggaran'] . " 
							AND id_unik='" . $v['id_unik'] . "' 
							AND id_unik_indikator='" . $v['id_unik_indikator'] . "'
							AND id_bidang_urusan='" . $v['id_bidang_urusan'] . "'
							AND id_visi='" . $v['id_visi'] . "'
							AND id_misi='" . $v['id_misi'] . "'
							AND urut_tujuan='" . $v['urut_tujuan'] . "'
							AND urut_sasaran='" . $v['urut_sasaran'] . "'
					");
					$opsi = array(
						'bidur_lock' => $v['bidur_lock'],
						'giat_lock' => $v['giat_lock'],
						'id_bidang_urusan' => $v['id_bidang_urusan'],
						'id_giat' => $v['id_giat'],
						'id_misi' => $v['id_misi'],
						'id_program' => $v['id_program'],
						'id_unik' => $v['id_unik'],
						'id_unik_indikator' => $v['id_unik_indikator'],
						'id_unit' => $v['id_unit'],
						'id_visi' => $v['id_visi'],
						'indikator' => $v['indikator'],
						'is_locked' => $v['is_locked'],
						'is_locked_indikator' => $v['is_locked_indikator'],
						'kode_bidang_urusan' => $v['kode_bidang_urusan'],
						'kode_giat' => $v['kode_giat'],
						'kode_program' => $v['kode_program'],
						'kode_sasaran' => $v['kode_sasaran'],
						'kode_skpd' => $v['kode_skpd'],
						'kode_tujuan' => $v['kode_tujuan'],
						'kode_unik_program' => $v['kode_unik_program'],
						'nama_bidang_urusan' => $v['nama_bidang_urusan'],
						'nama_giat' => $v['nama_giat'],
						'nama_program' => $v['nama_program'],
						'nama_skpd' => $v['nama_skpd'],
						'pagu_1' => $v['pagu_1'],
						'pagu_2' => $v['pagu_2'],
						'pagu_3' => $v['pagu_3'],
						'pagu_4' => $v['pagu_4'],
						'pagu_5' => $v['pagu_5'],
						'program_lock' => $v['program_lock'],
						'renstra_prog_lock' => $v['renstra_prog_lock'],
						'sasaran_lock' => $v['sasaran_lock'],
						'sasaran_teks' => $v['sasaran_teks'],
						'satuan' => $v['satuan'],
						'status' => $v['status'],
						'target_1' => $v['target_1'],
						'target_2' => $v['target_2'],
						'target_3' => $v['target_3'],
						'target_4' => $v['target_4'],
						'target_5' => $v['target_5'],
						'target_akhir' => $v['target_akhir'],
						'target_awal' => $v['target_awal'],
						'tujuan_lock' => $v['tujuan_lock'],
						'tujuan_teks' => $v['tujuan_teks'],
						'urut_sasaran' => $v['urut_sasaran'],
						'urut_tujuan' => $v['urut_tujuan'],
						'pagu_awal' => $v['pagu_awal'],
						'pagu_akhir' => $v['pagu_akhir'],
						'id_rpjm_indikator' => $v['id_rpjm_indikator'],
						'kode_sasaran_rpjm' => $v['kode_sasaran_rpjm'],
						'id_tahap' => $v['id_tahap'],
						'tahun_awal' => $v['tahun_awal'],
						'tahun_akhir' => $v['tahun_akhir'],
						'active' => 1,
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);

					if (!empty($cek)) {
						$wpdb->update('data_renstra_kegiatan', $opsi, array(
							'id_unik' => $v['id_unik'],
							'id_unik_indikator' => $v['id_unik_indikator'],
							'id_bidang_urusan' => $v['id_bidang_urusan'],
							'id_visi' => $v['id_visi'],
							'id_misi' => $v['id_misi'],
							'urut_tujuan' => $v['urut_tujuan'],
							'urut_sasaran' => $v['urut_sasaran'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_renstra_kegiatan', $opsi);
					}
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

	function get_realisasi_akun()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil get realisasi akun rekening!';
		$ret['data'] = array();
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$kd_unit_simda = explode('.', get_option('_crb_unit_' . $_POST['id_skpd']));
				$_kd_urusan = $kd_unit_simda[0];
				$_kd_bidang = $kd_unit_simda[1];
				$kd_unit = $kd_unit_simda[2];
				$kd_sub_unit = $kd_unit_simda[3];
				$simda = get_option('_crb_singkron_simda');

				$skpd = $wpdb->get_row($wpdb->prepare(
					"
    				SELECT 
    					nama_skpd, kode_skpd 
    				from data_unit 
    				where id_skpd=%d 
    					and tahun_anggaran=%d
    					and active=1",
					$_POST['id_skpd'],
					$_POST['tahun_anggaran']
				), ARRAY_A);
				foreach ($_POST['id_unik'] as $k => $v) {
					$ids = explode('-', $v);
					$kode_sbl = $ids[0];
					$kode_akun = $ids[1];
					$rek = explode('.', $ids[1]);
					$data_realisasi = array(
						'id_unik' => $v,
						'kode_sbl' => $kode_sbl,
						'realisasi' => 0,
						'realisasi_rp' => 'Rp 0'
					);
					$sub_db = $wpdb->get_results($wpdb->prepare("
						select 
							* 
						from data_sub_keg_bl 
						where active=1 
							and tahun_anggaran=%d 
							and kode_sbl=%s
					", $_POST['tahun_anggaran'], $kode_sbl), ARRAY_A);
					if (!empty($sub_db)) {
						$sub = $sub_db[0];
						$kd = explode('.', $sub['kode_sub_giat']);
						$kd_urusan90 = (int) $kd[0];
						$kd_bidang90 = (int) $kd[1];
						$kd_program90 = (int) $kd[2];
						$kd_kegiatan90 = ((int) $kd[3]) . '.' . $kd[4];
						$kd_sub_kegiatan = (int) $kd[5];
						$nama_keg = explode(' ', $sub['nama_sub_giat']);
						unset($nama_keg[0]);
						$nama_keg = implode(' ', $nama_keg);
						$mapping = false;
						if ($simda == 1) {
							$mapping = $this->simda->cekKegiatanMapping(array(
								'kd_urusan90' => $kd_urusan90,
								'kd_bidang90' => $kd_bidang90,
								'kd_program90' => $kd_program90,
								'kd_kegiatan90' => $kd_kegiatan90,
								'kd_sub_kegiatan' => $kd_sub_kegiatan,
								'nama_program' => $sub['nama_giat'],
								'nama_kegiatan' => $nama_keg
							));
						}

						$kd_urusan = 0;
						$kd_bidang = 0;
						$kd_prog = 0;
						$kd_keg = 0;
						if (!empty($mapping[0]) && !empty($mapping[0]->kd_urusan)) {
							$kd_urusan = $mapping[0]->kd_urusan;
							$kd_bidang = $mapping[0]->kd_bidang;
							$kd_prog = $mapping[0]->kd_prog;
							$kd_keg = $mapping[0]->kd_keg;
						}
						foreach ($this->simda->custom_mapping as $c_map_k => $c_map_v) {
							if (
								$skpd['kode_skpd'] == $c_map_v['sipd']['kode_skpd']
								&& $sub['kode_sub_giat'] == $c_map_v['sipd']['kode_sub_keg']
							) {
								$kd_unit_simda_map = explode('.', $c_map_v['simda']['kode_skpd']);
								$_kd_urusan = $kd_unit_simda_map[0];
								$_kd_bidang = $kd_unit_simda_map[1];
								$kd_unit = $kd_unit_simda_map[2];
								$kd_sub_unit = $kd_unit_simda_map[3];
								$kd_keg_simda = explode('.', $c_map_v['simda']['kode_sub_keg']);
								$kd_urusan = $kd_keg_simda[0];
								$kd_bidang = $kd_keg_simda[1];
								$kd_prog = $kd_keg_simda[2];
								$kd_keg = $kd_keg_simda[3];
							}
						}
						$id_prog = $kd_urusan . $this->simda->CekNull($kd_bidang);

						$realisasi_db = $wpdb->get_results($wpdb->prepare(
							"
							select 
								id,
								rak, 
								realisasi 
							from data_realisasi_akun 
							where active=1 
								and tahun_anggaran=%d 
								and kode_sbl=%s 
								and kode_akun=%s 
								and id_skpd=%d",
							$_POST['tahun_anggaran'],
							$kode_sbl,
							$kode_akun,
							$_POST['id_skpd']
						), ARRAY_A);
						$realisasi_db_total = 0;
						$rak_db_total = 0;
						$realisasi_db_id = 0;
						if (!empty($realisasi_db)) {
							$realisasi_db_total = $realisasi_db[0]['realisasi'];
							$rak_db_total = $realisasi_db[0]['rak'];
							$realisasi_db_id = $realisasi_db[0]['id'];
						}

						$opsi = array(
							'user' => $_POST['user'],
							'id_skpd' => $_POST['id_skpd'],
							'kode_sbl' => $kode_sbl,
							'kode_akun' => $kode_akun,
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'bulan' => date('m'),
							'realisasi_anggaran' => $realisasi_db_total,
							'id_realisasi_akun' => $realisasi_db_id,
							'kd_urusan' => $_kd_urusan,
							'kd_bidang' => $_kd_bidang,
							'kd_unit' => $kd_unit,
							'kd_sub' => $kd_sub_unit,
							'kd_prog' => $kd_prog,
							'id_prog' => $id_prog,
							'kd_keg' => $kd_keg
						);
						// $data_realisasi['opsi'] = $opsi;

						$data_realisasi['realisasi'] = 0;
						$data_realisasi['rak'] = 0;
						if ($simda == 1) {
							$data_realisasi['realisasi'] = $this->get_realisasi_simda($opsi);
							$data_realisasi['rak'] = $this->get_rak_simda($opsi);
						}
						$data_realisasi['realisasi_rp'] = 'Rp ' . number_format($data_realisasi['realisasi'], 0, ",", ".");

						$opsi['rak'] = $rak_db_total;
						$data_realisasi['rak_rp'] = 'Rp ' . number_format($data_realisasi['rak'], 0, ",", ".");
					}
					$ret['data'][] = $data_realisasi;
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

	function get_date_rfk_update($params = array())
	{
		global $wpdb;
		$tanggal = '-';
		$column = 'idinduk';

		if (isset($params['type']) && $params['type'] == 'sub_unit') {
			$column = 'id_skpd';
		}
		$last_update = $wpdb->get_results($wpdb->prepare(
			"
			select 
				min(d.created_at) as last_update
			from data_sub_keg_bl k 
			left join data_rfk d 
				on d.id_skpd=k.id_sub_skpd and 
				d.kode_sbl=k.kode_sbl and 
				d.tahun_anggaran=k.tahun_anggaran and 
				d.bulan=" . $params['bulan'] . " 
			where 
				k.tahun_anggaran=%d and 
				k.id_sub_skpd in (
					select 
						id_skpd 
					from data_unit 
					where 
						" . $column . "=" . $params['id_skpd'] . " and 
						active=1 and 
						tahun_anggaran=" . $params['tahun_anggaran'] . "
				) and 
				k.active=1
			",
			$params['tahun_anggaran']
		), ARRAY_A);

		if (!empty($last_update[0]['last_update'])) {
			$date = new DateTime($last_update[0]['last_update']);
			$tanggal = $date->format('d-m-Y');
		}

		return $tanggal;
	}

	function save_monev_renja()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil simpan data MONEV!';
		$ret['data'] = array();
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$current_user = wp_get_current_user();
				$ids = explode('-', $_POST['id_unik']);
				$tahun_anggaran = $ids[0];
				$id_skpd = $ids[1];
				$kode = $ids[2];
				$kode_sbl = $ids[3];
				$id_indikator = $ids[4];
				$kode_sbl_s = explode('.', $kode_sbl);
				$count_kode_sbl = count(explode('.', $ids[2]));
				$type_indikator = 0;

				$tahun_sekarang = date('Y');
				$batas_bulan_input = date('m');
				if ($tahun_anggaran < $tahun_sekarang) {
					$batas_bulan_input = 12;
				}
				$keterangan_bulan = array();
				$keterangan_bulan[1] = $_POST['keterangan']['keterangan_bulan_1'];
				$keterangan_bulan[2] = $_POST['keterangan']['keterangan_bulan_2'];
				$keterangan_bulan[3] = $_POST['keterangan']['keterangan_bulan_3'];
				$keterangan_bulan[4] = $_POST['keterangan']['keterangan_bulan_4'];
				$keterangan_bulan[5] = $_POST['keterangan']['keterangan_bulan_5'];
				$keterangan_bulan[6] = $_POST['keterangan']['keterangan_bulan_6'];
				$keterangan_bulan[7] = $_POST['keterangan']['keterangan_bulan_7'];
				$keterangan_bulan[8] = $_POST['keterangan']['keterangan_bulan_8'];
				$keterangan_bulan[9] = $_POST['keterangan']['keterangan_bulan_9'];
				$keterangan_bulan[10] = $_POST['keterangan']['keterangan_bulan_10'];
				$keterangan_bulan[11] = $_POST['keterangan']['keterangan_bulan_11'];
				$keterangan_bulan[12] = $_POST['keterangan']['keterangan_bulan_12'];
				$realisasi_bulan = array();
				$realisasi_bulan[1] = $_POST['data']['target_realisasi_bulan_1'];
				$realisasi_bulan[2] = $_POST['data']['target_realisasi_bulan_2'];
				$realisasi_bulan[3] = $_POST['data']['target_realisasi_bulan_3'];
				$realisasi_bulan[4] = $_POST['data']['target_realisasi_bulan_4'];
				$realisasi_bulan[5] = $_POST['data']['target_realisasi_bulan_5'];
				$realisasi_bulan[6] = $_POST['data']['target_realisasi_bulan_6'];
				$realisasi_bulan[7] = $_POST['data']['target_realisasi_bulan_7'];
				$realisasi_bulan[8] = $_POST['data']['target_realisasi_bulan_8'];
				$realisasi_bulan[9] = $_POST['data']['target_realisasi_bulan_9'];
				$realisasi_bulan[10] = $_POST['data']['target_realisasi_bulan_10'];
				$realisasi_bulan[11] = $_POST['data']['target_realisasi_bulan_11'];
				$realisasi_bulan[12] = $_POST['data']['target_realisasi_bulan_12'];
				for ($i = 1; $i <= 12; $i++) {
					if ($i > $batas_bulan_input) {
						$realisasi_bulan[$i] = 0;
					}
				}

				// sub kegiatan
				if ($count_kode_sbl == 6) {
					$type_indikator = 1;
					$table = "data_sub_keg_indikator"; //table indikator
					// kegiatan
				} else if ($count_kode_sbl == 5) {
					$table = "data_output_giat_sub_keg"; //table indikator
					$kode_sbl = $kode_sbl_s[0] . '.' . $kode_sbl_s[1] . '.' . $kode_sbl_s[2] . '.' . $kode_sbl_s[3];
					$type_indikator = 2;
					// program
				} else if ($count_kode_sbl == 3) {
					$table = "data_capaian_prog_sub_keg"; //table indikator
					$kode_sbl = $kode_sbl_s[0] . '.' . $kode_sbl_s[1] . '.' . $kode_sbl_s[2];
					$type_indikator = 3;
				}
				$cek = $wpdb->get_results($wpdb->prepare("
					select id
					from data_realisasi_renja
					where tahun_anggaran=%d
						and id_indikator=%d
						and tipe_indikator=%d
						and kode_sbl=%s
				", $tahun_anggaran, $id_indikator, $type_indikator, $kode_sbl));
				$ret['data_realisasi_renja'] = $wpdb->last_query;
				$opsi = array(
					'id_indikator' 				=> $id_indikator,
					'id_unik_indikator_renstra' => $_POST['id_indikator_renstra'],
					'tipe_indikator' 			=> $type_indikator,
					'id_rumus_indikator' 		=> $_POST['rumus_indikator'],
					'kode_sbl' 					=> $kode_sbl,
					'realisasi_bulan_1' 		=> $realisasi_bulan[1],
					'realisasi_bulan_2' 		=> $realisasi_bulan[2],
					'realisasi_bulan_3' 		=> $realisasi_bulan[3],
					'realisasi_bulan_4' 		=> $realisasi_bulan[4],
					'realisasi_bulan_5' 		=> $realisasi_bulan[5],
					'realisasi_bulan_6' 		=> $realisasi_bulan[6],
					'realisasi_bulan_7' 		=> $realisasi_bulan[7],
					'realisasi_bulan_8' 		=> $realisasi_bulan[8],
					'realisasi_bulan_9' 		=> $realisasi_bulan[9],
					'realisasi_bulan_10' 		=> $realisasi_bulan[10],
					'realisasi_bulan_11' 		=> $realisasi_bulan[11],
					'realisasi_bulan_12' 		=> $realisasi_bulan[12],
					'keterangan_bulan_1' 		=> $keterangan_bulan[1],
					'keterangan_bulan_2' 		=> $keterangan_bulan[2],
					'keterangan_bulan_3' 		=> $keterangan_bulan[3],
					'keterangan_bulan_4' 		=> $keterangan_bulan[4],
					'keterangan_bulan_5' 		=> $keterangan_bulan[5],
					'keterangan_bulan_6' 		=> $keterangan_bulan[6],
					'keterangan_bulan_7' 		=> $keterangan_bulan[7],
					'keterangan_bulan_8' 		=> $keterangan_bulan[8],
					'keterangan_bulan_9' 		=> $keterangan_bulan[9],
					'keterangan_bulan_10' 		=> $keterangan_bulan[10],
					'keterangan_bulan_11' 		=> $keterangan_bulan[11],
					'keterangan_bulan_12' 		=> $keterangan_bulan[12],
					'user' 						=> $current_user->display_name,
					'active' 					=> 1,
					'update_at' 				=> current_time('mysql'),
					'tahun_anggaran' 			=> $tahun_anggaran
				);
				if (!empty($cek)) {
					$wpdb->update('data_realisasi_renja', $opsi, array(
						'id_indikator' 		=> $id_indikator,
						'tipe_indikator' 	=> $type_indikator,
						'kode_sbl' 			=> $kode_sbl,
						'tahun_anggaran' 	=> $tahun_anggaran
					));
				} else {
					$wpdb->insert('data_realisasi_renja', $opsi);
				}

				$crb_cara_input_realisasi = get_option('_crb_cara_input_realisasi', 1);
				// cek jika cara input realisasi secara manual dan tipe indikator adalah sub kegiatan
				if (
					$crb_cara_input_realisasi == 2
					&& $type_indikator == 1
					&& !empty($_POST['rak'])
					&& !empty($_POST['realisasi'])
				) {
					// lakukan update data_rfk hanya pada bulan yang sudah dilalui
					for ($bulan = 1; $bulan <= $batas_bulan_input; $bulan++) {
						$sql = $wpdb->prepare("
						    select 
						        id
						    from data_rfk
						    where tahun_anggaran=%d
						        and bulan=%d
						        and id_skpd=%d
						        and kode_sbl=%s
						", $tahun_anggaran, $bulan, $id_skpd, $kode_sbl);
						$cek_id = $wpdb->get_var($sql);
						$realisasi_anggaran = 0;
						for ($b = 1; $b <= $bulan; $b++) {
							$realisasi_anggaran += $_POST['realisasi']['nilai_realisasi_bulan_' . $b];
						}
						$rak = 0;
						for ($b = 1; $b <= $bulan; $b++) {
							$rak += $_POST['rak']['nilai_rak_bulan_' . $b];
						}
						$opsi = array(
							'bulan'					=> $bulan,
							'kode_sbl'				=> $kode_sbl,
							'rak' 					=> $rak,
							'realisasi_anggaran' 	=> $realisasi_anggaran,
							'user_edit'				=> $current_user->display_name,
							'id_skpd'				=> $id_skpd,
							'tahun_anggaran'		=> $tahun_anggaran,
							'created_at'			=> current_time('mysql')
						);
						if (!empty($cek_id)) {
							$wpdb->update('data_rfk', $opsi, array(
								'id' => $cek_id
							));
						} else {
							$wpdb->insert('data_rfk', $opsi);
						}
					}
				}

				//simpan atau update bobot kinerja
				if (!empty($_POST['bobot_kinerja'])) {
					$sql = $wpdb->prepare("
						SELECT 
							id
						FROM " . $table . "
						WHERE tahun_anggaran = %d
						  AND kode_sbl = %s
						  AND active = 1
					", $tahun_anggaran, $ids[3]);
					$cek_id = $wpdb->get_var($sql);

					$data = array(
						'bobot_kinerja'	=>  $_POST['bobot_kinerja'],
						'update_at'		=>  current_time('mysql')
					);
					if (!empty($cek_id)) {
						$wpdb->update($table, $data, array(
							'id' => $cek_id
						));
						$ret['message'] = "Berhasil Update";
					}
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

	function get_monev()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil get data MONEV!';
		$ret['data'] = array();
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$current_user = wp_get_current_user();
				$ids = explode('-', $_POST['id_unik']);
				$tahun_anggaran = $ids[0];
				$id_skpd = $ids[1];
				$kode = $ids[2];
				$kode_sbl = $ids[3];
				$id_indikator = $ids[4];
				$kode_sbl_s = explode('.', $kode_sbl);
				$count_kode_sbl = count(explode('.', $ids[2]));
				$type_indikator = 0;
				$tahun_sekarang = date('Y');
				$batas_bulan_input = date('m');
				//jika tahun berganti, taruh bulan pada desember
				if ($tahun_anggaran < $tahun_sekarang) {
					$batas_bulan_input = 12;
				}

				// sub kegiatan
				if ($count_kode_sbl == 6) {
					$type_indikator = 1;
					$table = "data_sub_keg_indikator"; //table indikator
					// kegiatan
				} else if ($count_kode_sbl == 5) {
					$table = "data_output_giat_sub_keg"; //table indikator
					$kode_sbl = $kode_sbl_s[0] . '.' . $kode_sbl_s[1] . '.' . $kode_sbl_s[2] . '.' . $kode_sbl_s[3];
					$type_indikator = 2;
					// program
				} else if ($count_kode_sbl == 3) {
					$table = "data_capaian_prog_sub_keg"; //table indikator
					$kode_sbl = $kode_sbl_s[0] . '.' . $kode_sbl_s[1] . '.' . $kode_sbl_s[2];
					$type_indikator = 3;
				}

				$realisasi_renja = $wpdb->get_results($wpdb->prepare("
					select
						id_rumus_indikator,
						id_unik_indikator_renstra,
						realisasi_bulan_1,
						realisasi_bulan_2,
						realisasi_bulan_3,
						realisasi_bulan_4,
						realisasi_bulan_5,
						realisasi_bulan_6,
						realisasi_bulan_7,
						realisasi_bulan_8,
						realisasi_bulan_9,
						realisasi_bulan_10,
						realisasi_bulan_11,
						realisasi_bulan_12,
						keterangan_bulan_1,
						keterangan_bulan_2,
						keterangan_bulan_3,
						keterangan_bulan_4,
						keterangan_bulan_5,
						keterangan_bulan_6,
						keterangan_bulan_7,
						keterangan_bulan_8,
						keterangan_bulan_9,
						keterangan_bulan_10,
						keterangan_bulan_11,
						keterangan_bulan_12
					from data_realisasi_renja
					where tahun_anggaran=%d
						and id_indikator=%d
						and tipe_indikator=%d
						and kode_sbl=%s
				", $tahun_anggaran, $id_indikator, $type_indikator, $kode_sbl), ARRAY_A);
				$ret['id_rumus_indikator'] = 1;
				$ret['id_unik_indikator_renstra'] = '';
				if (!empty($realisasi_renja)) {
					$ret['id_rumus_indikator'] = $realisasi_renja[0]['id_rumus_indikator'];
					$ret['id_unik_indikator_renstra'] = $realisasi_renja[0]['id_unik_indikator_renstra'];
				}

				$rfk_all = $wpdb->get_results($wpdb->prepare("
					select 
						realisasi_anggaran,
						rak,
						bulan
					from data_rfk
					where tahun_anggaran=%d
						and id_skpd=%d
						and kode_sbl LIKE %s
						and bulan<=%d
					order by bulan ASC
				", $tahun_anggaran, $id_skpd, $kode_sbl . '%', $batas_bulan_input), ARRAY_A);
				$ret['rfk_sql'] = $wpdb->last_query;
				$realisasi_anggaran = array();
				$rak = array();
				foreach ($rfk_all as $k => $v) {
					if (empty($realisasi_anggaran[$v['bulan']])) {
						$realisasi_anggaran[$v['bulan']] = 0;
					}
					$realisasi_anggaran[$v['bulan']] += $v['realisasi_anggaran'];
					if (empty($rak[$v['bulan']])) {
						$rak[$v['bulan']] = 0;
					}
					$rak[$v['bulan']] += $v['rak'];
				}
				$total_rak = 0;
				$total_realisasi = 0;
				$total_selisih = 0;
				$bulan = 12;
				$tbody = '';

				$crb_cara_input_realisasi = get_option('_crb_cara_input_realisasi', 1);
				for ($i = 1; $i <= $bulan; $i++) {
					$realisasi_target_bulanan = 0;
					if (!empty($realisasi_renja)) {
						$realisasi_target_bulanan = $realisasi_renja[0]['realisasi_bulan_' . $i];
					}
					if (empty($realisasi_anggaran[$i])) {
						$realisasi_anggaran[$i] = 0;
					}
					$bulan_minus = $i - 1;
					if (empty($realisasi_anggaran[$bulan_minus])) {
						$realisasi_anggaran[$bulan_minus] = 0;
					}
					$realisasi_bulanan = $realisasi_anggaran[$i] - $realisasi_anggaran[$bulan_minus];
					if ($realisasi_bulanan < 0) {
						$realisasi_bulanan = 0;
					}
					if (empty($rak[$i]) && $i <= $batas_bulan_input) {
						$rak[$i] = $this->get_rak_sipd_rfk(array(
							'user' => $current_user->display_name,
							'id_skpd' => $id_skpd,
							'kode_sbl' => $kode_sbl,
							'tahun_anggaran' => $tahun_anggaran,
							'bulan' => $i,
							'rak' => 0
						));
					}
					$bulan_minus = $i - 1;
					if (empty($rak[$bulan_minus])) {
						$rak[$bulan_minus] = 0;
					}
					$rak_bulanan = $rak[$i] - $rak[$bulan_minus];
					if ($rak_bulanan < 0) {
						$rak_bulanan = 0;
					}
					$selisih = $rak_bulanan - $realisasi_bulanan;

					$rak_bulanan_format = number_format($rak_bulanan, 0, ",", ".");
					$realisasi_bulanan_format = number_format($realisasi_bulanan, 0, ",", ".");
					$selisih_format = number_format($selisih, 0, ",", ".");

					$editable = 'contenteditable="true"';
					$editable_realisasi = 'contenteditable="true"';

					/* 
						- jika bulan belum dilalui 
						- atau user login adalah admin 
						- atau user login adalah mitra bappeda
						- atau user login adalah tapd bappeda
					*/
					$bobot_kinerja_field = '';
					if (
						$batas_bulan_input < $i
						|| current_user_can('administrator')
						|| in_array("mitra_bappeda", $current_user->roles)
						|| in_array("tapd_pp", $current_user->roles)
					) {
						$editable = '';
						$editable_realisasi = '';
						// jika input realisasi secara manual dan type indikator adalah sub kegiatan
					} else if (
						$crb_cara_input_realisasi == 2
						&& $type_indikator == 1
					) {
						$rak_bulanan_format = $rak_bulanan;
						$realisasi_bulanan_format = $realisasi_bulanan;
					}

					// jika cara input realisasi otomatis dari SIMDA atau tipe indikator bukan sub kegiatan
					if (
						$crb_cara_input_realisasi == 1
						|| $type_indikator != 1
					) {
						$editable_realisasi = '';
					}
					$tbody .= '
						<tr>
							<td>' . $this->get_bulan($i) . '</td>
							<td class="text_kanan nilai_rak" onkeypress="onlyNumber(event);" onkeyup="setTotalRealisasi();" id="nilai_rak_bulan_' . $i . '">' . $rak_bulanan_format . '</td>
							<td class="text_kanan nilai_realisasi" ' . $editable_realisasi . ' onkeypress="onlyNumber(event);" onkeyup="setTotalRealisasi();" id="nilai_realisasi_bulan_' . $i . '">' . $realisasi_bulanan_format . '</td>
							<td class="text_kanan nilai_selisih">' . $selisih_format . '</td>
							<td class="text_tengah target_realisasi" id="target_realisasi_bulan_' . $i . '" ' . $editable . ' onkeypress="onlyNumber(event);" onkeyup="setTotalMonev(this);">' . $realisasi_target_bulanan . '</td>
							<td class="text_kiri" id="keterangan_bulan_' . $i . '" ' . $editable . '>' . $realisasi_renja[0]['keterangan_bulan_' . $i] . '</td>
						</tr>
					';
					$total_rak += $rak_bulanan;
					$total_realisasi += $realisasi_bulanan;
					$total_selisih += $selisih;
				}
				$tbody .= '
					<tr>
						<td class="text_tengah text_blok">Total</td>
						<td class="text_kanan text_blok" id="total_nilai_rak">' . number_format($total_rak, 0, ",", ".") . '</td>
						<td class="text_kanan text_blok" id="total_nilai_realisasi">' . number_format($total_realisasi, 0, ",", ".") . '</td>
						<td class="text_kanan text_blok" id="total_nilai_selisih">' . number_format($total_selisih, 0, ",", ".") . '</td>
						<td class="text_tengah text_blok" id="total_target_realisasi">0</td>
						<td class="text_tengah text_blok"></td>
					</tr>
				';

				//bobot_kinerja
				$sql = $wpdb->prepare("
					SELECT 
						bobot_kinerja
					FROM " . $table . "
					WHERE tahun_anggaran = %d
						AND kode_sbl = %s
						AND active = 1
				", $tahun_anggaran, $ids[3]);
				$bobot_kinerja = $wpdb->get_var($sql);

				$ret['table'] = $tbody;
				$ret['bobot_kinerja'] = $bobot_kinerja;
				$ret['tipe_indikator'] = $type_indikator;
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

	public function get_link_post($custom_post, $link = false)
	{
		if (
			!empty($custom_post)
			&& $custom_post->post_status == 'publish'
		) {
			return get_permalink($custom_post);
		} else {
			if (null == $custom_post) {
				if (empty($link)) {
					$link = '#';
				}
			} else {
				if (empty($link)) {
					$link = get_permalink($custom_post);
				}
				if (false == $link) {
					$link = '#';
				}
			}
			if ($link != '#') {
				$options = array();
				if (!empty($custom_post->custom_url)) {
					$options['custom_url'] = $custom_post->custom_url;
				}
				if (strpos($link, '?') === false) {
					$link .= '?key=' . $this->gen_key(false, $options);
				} else {
					$link .= '&key=' . $this->gen_key(false, $options);
				}
			}
			return $link;
		}
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

	public function decode_key($value)
	{
		$key = base64_decode($value);
		$key_db = md5(get_option('_crb_api_key_extension'));
		$key = explode($key_db, $key);
		$get = array();
		if (!empty($key[2])) {
			$all_get = explode('&', $key[2]);
			foreach ($all_get as $k => $v) {
				$current_get = explode('=', $v);
				$get[$current_get[0]] = $current_get[1];
			}
		}
		return $get;
	}

	public function get_url_page()
	{
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil get data link!';
		$ret['data'] = array();
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['type'])) {
					$type_page = $_POST['type'];
					if (!empty($_POST['tahun_anggaran'])) {
						$tahun_anggaran = $_POST['tahun_anggaran'];
					} else {
						$tahun_anggaran = 2021;
					}
					if ($type_page == 'rfk_pemda') {
						$title = 'Realisasi Fisik dan Keuangan Pemerintah Daerah | ' . $tahun_anggaran;
						$custom_post = $this->get_page_by_title($title);
						$custom_post->custom_url = array(array('key' => 'public', 'value' => 1));
						$url = $this->get_link_post($custom_post);
						$ret['url'] = $url;
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Param type tidak sesuai!';
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

	function simpan_catatan_rfk_unit()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil simpan data!'
		);
		if (!empty($_POST)) {
			if (isset($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$cek = $wpdb->get_var("SELECT id FROM data_catatan_rfk_unit WHERE id_skpd=" . $_POST['data']['id_skpd'] . " AND bulan=" . $_POST['bulan'] . " AND tahun_anggaran=" . $_POST['tahun_anggaran']);
				$data = array(
					'bulan' => $_POST['bulan'],
					'catatan_ka_adbang' => !empty($_POST['data']['catatan_ka_adbang']) ? $_POST['data']['catatan_ka_adbang'] : NULL,
					'id_skpd' => $_POST['data']['id_skpd'],
					'tahun_anggaran' => $_POST['tahun_anggaran']
				);

				if (!empty($cek)) {
					$data['updated_by'] = $_POST['user'];
					$data['updated_at'] = current_time('mysql');
					$wpdb->update('data_catatan_rfk_unit', $data, array('id_skpd' => $_POST['data']['id_skpd'], 'bulan' => $_POST['bulan'], 'tahun_anggaran' => $_POST['tahun_anggaran']));
				} else {
					$data['created_by'] = $_POST['user'];
					$data['created_at'] = current_time('mysql');
					$wpdb->insert('data_catatan_rfk_unit', $data);
				}
			} else {
				$ret = array(
					'status' => 'error',
					'message' => 'APIKEY tidak sama!'
				);
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	function save_monev_renja_triwulan()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil simpan data!'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$current_user = wp_get_current_user();
				$cek = $wpdb->get_var(
					"
					SELECT 
						id 
					FROM data_monev_renja_triwulan 
					WHERE id_skpd=" . $_POST['id_skpd'] . " 
						AND triwulan=" . $_POST['triwulan'] . " 
						AND tahun_anggaran=" . $_POST['tahun_anggaran']
				);
				$data = array(
					'triwulan' => $_POST['triwulan'],
					'id_skpd' => $_POST['id_skpd'],
					'tahun_anggaran' => $_POST['tahun_anggaran']
				);
				if (
					current_user_can('administrator')
					|| in_array("mitra_bappeda", $current_user->roles)
					|| in_array("tapd_pp", $current_user->roles)
				) {
					$data['catatan_verifikator'] = $_POST['catatan_verifikator'];
					$data['user_verifikator'] = $current_user->display_name;
					$data['update_verifikator_at'] = current_time('mysql');
					$ret['update_verifikator_at'] = $data['update_verifikator_at'];
				} else {
					$file_name = ((int) $_POST['triwulan']) . '.' . ((int) $_POST['tahun_anggaran']) . '.' . ((int) $_POST['id_skpd']) . '.MONEV_RENJA.xlsx';
					$target_folder = WPSIPD_PLUGIN_PATH . 'public/media/';
					$target_file = $target_folder . $file_name;
					if (!empty($_POST['file_remove']) && $_POST['file_remove'] == 1) {
						$ret['message'] = 'Berhasil hapus file ' . $file_name . '!';
						$file_name = '';
						unlink($target_file);
					} else {
						// max 10MB
						if ($_FILES["file"]["size"] > 1000000) {
							$ret['status'] = 'error';
							$ret['message'] = 'Max file upload sebesar 10MB!';
						}
						// cek type file
						$imageFileType = strtolower(pathinfo($target_folder . basename($_FILES["file"]["name"]), PATHINFO_EXTENSION));
						if ($imageFileType != "xlsx") {
							$ret['status'] = 'error';
							$ret['message'] = 'File yang diupload harus berextensi .xlsx!';
						}
						if ($ret['status'] == 'success') {
							move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
						}
						$data['keterangan_skpd'] = $_POST['keterangan_skpd'];
					}
					$data['user_skpd'] = $current_user->display_name;
					$data['file_monev'] = $file_name;
					$data['update_skpd_at'] = current_time('mysql');
					$ret['update_skpd_at'] = $data['update_skpd_at'];
					$ret['nama_file'] = $file_name;
				}
				if ($ret['status'] == 'success') {
					if (!empty($cek)) {
						$wpdb->update('data_monev_renja_triwulan', $data, array(
							'id_skpd' => $_POST['id_skpd'],
							'triwulan' => $_POST['triwulan'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_monev_renja_triwulan', $data);
					}
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

	function get_data_rpjm()
	{
		global $wpdb;
		if (empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_unik = $_POST['kode_sasaran_rpjm'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_unit = $_POST['id_unit'];

				$data_rpjmd = $wpdb->get_results(
					$wpdb->prepare("
						SELECT 
							* 
						FROM data_rpjmd_sasaran 
						WHERE active=1 
						  AND id_unik=%s 
						  AND tahun_anggaran=%d
					", $id_unik, $tahun_anggaran),
					ARRAY_A
				);

				$return['status'] = 0;
				$return['body_rpjm'] = '';
				if (!empty($data_rpjmd)) {

					$data_all = array(
						'data' => array()
					);
					foreach ($data_rpjmd as $k => $v) {
						if (empty($data_all['data'][$v['id_visi']])) {
							$data_all['data'][$v['id_visi']] = array(
								'nama' => $v['visi_teks'],
								'data' => array()
							);
						}
						if (empty($data_all['data'][$v['id_visi']]['data'][$v['id_misi']])) {
							$data_all['data'][$v['id_visi']]['data'][$v['id_misi']] = array(
								'nama' => $v['misi_teks'],
								'data' => array()
							);
						}
						if (empty($data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']])) {
							$data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']] = array(
								'nama' => $v['tujuan_teks'],
								'indikator' => array(),
								'data' => array(),
							);
							$indikators = $wpdb->get_results(
								$wpdb->prepare("
									SELECT 
										* 
									FROM data_rpjmd_tujuan 
									WHERE active=1 
									AND id_unik=%s 
									AND tahun_anggaran=%d
								", $v['kode_tujuan'], $tahun_anggaran),
								ARRAY_A
							);

							if (!empty($indikators)) {
								foreach ($indikators as $key => $indikator) {
									$data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']]['indikator'][] = array(
										'indikator' => !empty($indikator['indikator_teks']) ? $indikator['indikator_teks'] : "",
										'satuan' => !empty($indikator['satuan']) ? $indikator['satuan'] : "",
										'target_1' => !empty($indikator['target_1']) ? $indikator['target_1'] : "",
										'target_2' => !empty($indikator['target_2']) ? $indikator['target_2'] : "",
										'target_3' => !empty($indikator['target_3']) ? $indikator['target_3'] : "",
										'target_4' => !empty($indikator['target_4']) ? $indikator['target_4'] : "",
										'target_5' => !empty($indikator['target_5']) ? $indikator['target_5'] : "",
										'target_awal' => !empty($indikator['target_awal']) ? $indikator['target_awal'] : "",
										'target_akhir' => !empty($indikator['target_akhir']) ? $indikator['target_akhir'] : "",
									);
								}
							}
						}

						if (empty($data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']]['data'][$v['kode_sasaran']])) {
							$data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']]['data'][$v['kode_sasaran']] = array(
								'nama' => $v['sasaran_teks'],
								'indikator' => array(),
								'data' => array(),
							);

							if (!empty($v['id_unik_indikator'])) {
								$data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']]['data'][$v['kode_sasaran']]['data'][$v['id_program']]['indikator'][] = array(
									'id_unik' => $v['id_unik'],
									'id_unik_indikator' => $v['id_unik_indikator'],
									'indikator' => $v['indikator'],
									'satuan' => $v['satuan'],
									'target_1' => $v['target_1'],
									'target_2' => $v['target_2'],
									'target_3' => $v['target_3'],
									'target_4' => $v['target_4'],
									'target_5' => $v['target_5'],
									'target_awal' => $v['target_awal'],
									'target_akhir' => $v['target_akhir'],
								);
							}
						}

						$program = $wpdb->get_results(
							$wpdb->prepare("
								SELECT 
									* 
								FROM data_rpjmd_program 
								WHERE active=1 
								  AND kode_sasaran=%s 
								  AND id_unit=%d 
								  AND tahun_anggaran=%d
							", $v['id_unik'], $id_unit, $tahun_anggaran),
							ARRAY_A
						);

						if (!empty($program)) {
							foreach ($program as $kp => $vp) {
								if (empty($data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']]['data'][$v['kode_sasaran']]['data'][$vp['id_program']])) {
									$data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']]['data'][$v['kode_sasaran']]['data'][$vp['id_program']] = array(
										'nama' => $vp['nama_program'],
										'indikator' => array(),
									);
									if (!empty($vp['id_unik_indikator'])) {
										$data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']]['data'][$v['kode_sasaran']]['data'][$vp['id_program']]['indikator'][] = array(
											'id_unik' => $vp['id_unik'],
											'id_unik_indikator' => $vp['id_unik_indikator'],
											'indikator' => $vp['indikator'],
											'satuan' => $vp['satuan'],
											'target_1' => $vp['target_1'],
											'target_2' => $vp['target_2'],
											'target_3' => $vp['target_3'],
											'target_4' => $vp['target_4'],
											'target_5' => $vp['target_5'],
											'target_awal' => $vp['target_awal'],
											'target_akhir' => $vp['target_akhir'],
										);
									}
								}
							}
						}
					}

					$body = '';
					foreach ($data_all['data'] as $v => $visi) {
						$body .= '
						<tr class="misi" data-kode="">
				            <td class="kiri kanan bawah text_blok">' . $visi['nama'] . '</td>
				            <td class="text_kiri kanan bawah text_blok"></td>
				            <td class="text_kiri kanan bawah text_blok"></td>
				            <td class="kanan bawah text_blok"></td>
				            <td class="kanan bawah text_blok"></td>
				            <td class="text_kiri kanan bawah text_blok"></td>
				        </tr>';
						foreach ($visi['data'] as $m => $misi) {
							$body .= '
							<tr class="misi" data-kode="">
					            <td class="kiri kanan bawah text_blok"></td>
					            <td class="text_kiri kanan bawah text_blok">' . $misi['nama'] . '</td>
					            <td class="text_kiri kanan bawah text_blok"></td>
					            <td class="kanan bawah text_blok"></td>
					            <td class="kanan bawah text_blok"></td>
					            <td class="text_kiri kanan bawah text_blok"></td>
					        </tr>';
							foreach ($misi['data'] as $t => $tujuan) {
								$tujuan_teks = explode("||", $tujuan['nama']);
								$body .= '
								<tr class="misi" data-kode="">
						            <td class="kiri kanan bawah text_blok"></td>
						            <td class="text_kiri kanan bawah text_blok"></td>
						            <td class="text_kiri kanan bawah text_blok">' . $tujuan_teks[0] . '</td>
						            <td class="kanan bawah text_blok"></td>
						            <td class="kanan bawah text_blok"></td>
						            <td class="text_kiri kanan bawah text_blok"></td>
						        </tr>';
								foreach ($tujuan['data'] as $s => $sasaran) {
									$sasaran_teks = explode("||", $sasaran['nama']);
									$body .= '
									<tr class="misi" data-kode="">
							            <td class="kiri kanan bawah text_blok"></td>
							            <td class="text_kiri kanan bawah text_blok"></td>
							            <td class="text_kiri kanan bawah text_blok"></td>
							            <td class="kanan bawah text_blok">' . $sasaran_teks[0] . '</td>
							            <td class="kanan bawah text_blok"></td>
							            <td class="text_kiri kanan bawah text_blok"></td>
							        </tr>';
									foreach ($sasaran['data'] as $p => $program) {
										$program_teks = explode("||", $program['nama']);
										$indikator = array(
											'indikator_teks' => array()
										);
										foreach ($program['indikator'] as $i => $p_indikator) {
											$indikator['indikator_teks'][] = $p_indikator['indikator'];
										}
										$body .= '
										<tr class="misi" data-kode="">
								            <td class="kiri kanan bawah text_blok"></td>
								            <td class="text_kiri kanan bawah text_blok"></td>
								            <td class="text_kiri kanan bawah text_blok"></td>
								            <td class="kanan bawah text_blok"></td>
								            <td class="kanan bawah text_blok">' . $program_teks[0] . '</td>
								            <td class="text_kiri kanan bawah text_blok">' . implode(' <br><br> ', $indikator['indikator_teks']) . '</td>
								        </tr>';
									}
								}
							}
						}
					}
					$return['status'] = 1;
					$return['body_rpjm'] = $body;
				}
			} else {
				$return['status'] = 'error';
				$return['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$return['status'] = 'error';
			$return['message'] = 'Format Salah!';
		}
		echo json_encode($return);
		exit();
	}

	public function reset_rfk_pemda()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil reset data sesuai bulan sebelumnya!',
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$bulan = $_POST['bulan'];
				$bulan_n = $_POST['bulan'] - 1;
				$tahun_anggaran = $_POST['tahun_anggaran'];

				$units = $wpdb->get_results($wpdb->prepare(
					"
						SELECT 
							id_skpd, nama_skpd, active 
						FROM 
							data_unit 
						WHERE 
							active=1 AND 
							tahun_anggaran=%d 
						ORDER BY 
							kode_skpd ASC",
					$tahun_anggaran
				), ARRAY_A);

				foreach ($units as $key => $unit) {
					$data_n = $wpdb->get_results($wpdb->prepare(
						"
						SELECT 
							id,bulan,tahun_anggaran,id_skpd,catatan_ka_adbang
						FROM
							data_catatan_rfk_unit
						WHERE
							bulan=%d and
							id_skpd=%d and
							tahun_anggaran=%d
						
						UNION

						SELECT 
							id,bulan,tahun_anggaran,id_skpd,catatan_ka_adbang
						FROM
							data_catatan_rfk_unit
						WHERE
							bulan=%d and
							id_skpd=%d and
							tahun_anggaran=%d
							",
						$bulan_n,
						$unit['id_skpd'],
						$tahun_anggaran,

						$bulan_n,
						"-" . $unit['id_skpd'],
						$tahun_anggaran
					), ARRAY_A);
					// die($wpdb->last_query);

					if (!empty($data_n)) {
						foreach ($data_n as $k => $v) {
							$cek = $wpdb->get_results($wpdb->prepare("
								SELECT id FROM data_catatan_rfk_unit WHERE bulan=%d AND id_skpd=%d AND tahun_anggaran=%d", $bulan, $v['id_skpd'], $tahun_anggaran), ARRAY_A);
							if (!empty($cek)) {
								$data = array(
									'catatan_ka_adbang' => $v['catatan_ka_adbang'],
									'updated_by' => $_POST['user'],
									'updated_at' => current_time('mysql')
								);
								$wpdb->update('data_catatan_rfk_unit', $data, array(
									'tahun_anggaran' => $_POST['tahun_anggaran'],
									'bulan' => $bulan,
									'id_skpd' => $v['id_skpd']
								));
							} else {
								$data = array(
									'id_skpd' => $v['id_skpd'],
									'bulan' => $bulan,
									'tahun_anggaran' => $_POST['tahun_anggaran'],
									'catatan_ka_adbang' => $v['catatan_ka_adbang'],
									'created_by' => $_POST['user'],
									'created_at' => current_time('mysql')
								);
								$stat = $wpdb->insert('data_catatan_rfk_unit', $data);
							}
						}
					}
				}
			} else {
				$ret = array(
					'status' => 'error',
					'message' => 'Apikey salah!',
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message' => 'Format salah!',
			);
		}
		die(json_encode($ret));
	}

	public function get_pagu_renstra_keg($opsi)
	{
		global $wpdb;
		$ret = $wpdb->get_row($wpdb->prepare("
			SELECT
				SUM(pagu_1) as pagu_1,
				SUM(pagu_2) as pagu_2,
				SUM(pagu_3) as pagu_3,
				SUM(pagu_4) as pagu_4,
				SUM(pagu_5) as pagu_5,
				SUM(realisasi_pagu_1) as realisasi_pagu_1,
				SUM(realisasi_pagu_2) as realisasi_pagu_2,
				SUM(realisasi_pagu_3) as realisasi_pagu_3,
				SUM(realisasi_pagu_4) as realisasi_pagu_4,
				SUM(realisasi_pagu_5) as realisasi_pagu_5
			FROM data_renstra_sub_kegiatan
			WHERE tahun_anggaran=%d
				AND active=1
				AND id_unit=%d
				AND kode_kegiatan=%s
				AND id_unik_indikator IS NULL
		", $opsi['tahun_anggaran'], $opsi['id_skpd'], $opsi['id_unik']), ARRAY_A);
		return $ret;
	}

	public function get_pagu_renstra_prog($opsi)
	{
		global $wpdb;
		$kegiatan = $wpdb->get_results($wpdb->prepare("
			SELECT
				id_unik
			FROM data_renstra_kegiatan
			WHERE tahun_anggaran=%d
				AND active=1
				AND id_unit=%d
				AND kode_program=%s
				AND id_unik_indikator IS NULL
			GROUP BY id_unik
		", $opsi['tahun_anggaran'], $opsi['id_skpd'], $opsi['id_unik']), ARRAY_A);
		$ret = array(
			'pagu_1' => 0,
			'pagu_2' => 0,
			'pagu_3' => 0,
			'pagu_4' => 0,
			'pagu_5' => 0,
			'realisasi_pagu_1' => 0,
			'realisasi_pagu_2' => 0,
			'realisasi_pagu_3' => 0,
			'realisasi_pagu_4' => 0,
			'realisasi_pagu_5' => 0,
		);
		foreach ($kegiatan as $v) {
			$opsi['id_unik'] = $v['id_unik'];
			$pagu = $this->get_pagu_renstra_keg($opsi);
			$ret['pagu_1'] += $pagu['pagu_1'];
			$ret['pagu_2'] += $pagu['pagu_2'];
			$ret['pagu_3'] += $pagu['pagu_3'];
			$ret['pagu_4'] += $pagu['pagu_4'];
			$ret['pagu_5'] += $pagu['pagu_5'];
			$ret['realisasi_pagu_1'] += $pagu['realisasi_pagu_1'];
			$ret['realisasi_pagu_2'] += $pagu['realisasi_pagu_2'];
			$ret['realisasi_pagu_3'] += $pagu['realisasi_pagu_3'];
			$ret['realisasi_pagu_4'] += $pagu['realisasi_pagu_4'];
			$ret['realisasi_pagu_5'] += $pagu['realisasi_pagu_5'];
		}
		return $ret;
	}

	public function get_pagu_renstra_sasaran($opsi)
	{
		global $wpdb;
		$program = $wpdb->get_results($wpdb->prepare("
			SELECT
				id_unik
			FROM data_renstra_program
			WHERE tahun_anggaran=%d
				AND active=1
				AND id_unit=%d
				AND kode_sasaran=%s
				AND id_unik_indikator IS NULL
			GROUP BY id_unik
		", $opsi['tahun_anggaran'], $opsi['id_skpd'], $opsi['id_unik']), ARRAY_A);
		$ret = array(
			'pagu_1' => 0,
			'pagu_2' => 0,
			'pagu_3' => 0,
			'pagu_4' => 0,
			'pagu_5' => 0,
			'realisasi_pagu_1' => 0,
			'realisasi_pagu_2' => 0,
			'realisasi_pagu_3' => 0,
			'realisasi_pagu_4' => 0,
			'realisasi_pagu_5' => 0,
		);
		foreach ($program as $v) {
			$opsi['id_unik'] = $v['id_unik'];
			$pagu = $this->get_pagu_renstra_prog($opsi);
			$ret['pagu_1'] += $pagu['pagu_1'];
			$ret['pagu_2'] += $pagu['pagu_2'];
			$ret['pagu_3'] += $pagu['pagu_3'];
			$ret['pagu_4'] += $pagu['pagu_4'];
			$ret['pagu_5'] += $pagu['pagu_5'];
			$ret['realisasi_pagu_1'] += $pagu['realisasi_pagu_1'];
			$ret['realisasi_pagu_2'] += $pagu['realisasi_pagu_2'];
			$ret['realisasi_pagu_3'] += $pagu['realisasi_pagu_3'];
			$ret['realisasi_pagu_4'] += $pagu['realisasi_pagu_4'];
			$ret['realisasi_pagu_5'] += $pagu['realisasi_pagu_5'];
		}
		return $ret;
	}

	public function get_pagu_renstra_tujuan($opsi)
	{
		global $wpdb;
		$sasaran = $wpdb->get_results($wpdb->prepare("
			SELECT
				id_unik
			FROM data_renstra_sasaran
			WHERE tahun_anggaran=%d
				AND active=1
				AND id_unit=%d
				AND kode_tujuan=%s
				AND id_unik_indikator IS NULL
			GROUP BY id_unik
		", $opsi['tahun_anggaran'], $opsi['id_skpd'], $opsi['id_unik']), ARRAY_A);
		$ret = array(
			'pagu_1' => 0,
			'pagu_2' => 0,
			'pagu_3' => 0,
			'pagu_4' => 0,
			'pagu_5' => 0,
			'realisasi_pagu_1' => 0,
			'realisasi_pagu_2' => 0,
			'realisasi_pagu_3' => 0,
			'realisasi_pagu_4' => 0,
			'realisasi_pagu_5' => 0,
		);
		foreach ($sasaran as $v) {
			$opsi['id_unik'] = $v['id_unik'];
			$pagu = $this->get_pagu_renstra_sasaran($opsi);
			$ret['pagu_1'] += $pagu['pagu_1'];
			$ret['pagu_2'] += $pagu['pagu_2'];
			$ret['pagu_3'] += $pagu['pagu_3'];
			$ret['pagu_4'] += $pagu['pagu_4'];
			$ret['pagu_5'] += $pagu['pagu_5'];
			$ret['realisasi_pagu_1'] += $pagu['realisasi_pagu_1'];
			$ret['realisasi_pagu_2'] += $pagu['realisasi_pagu_2'];
			$ret['realisasi_pagu_3'] += $pagu['realisasi_pagu_3'];
			$ret['realisasi_pagu_4'] += $pagu['realisasi_pagu_4'];
			$ret['realisasi_pagu_5'] += $pagu['realisasi_pagu_5'];
		}
		return $ret;
	}

	public function get_monev_renstra()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'message' => 'Berhasil get data!',
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$indikator = '';
				$body_renstra = '';
				$sum_anggaran = 0;
				$sum_realisasi_anggaran = 0;
				$edit_realisasi_pagu = '';
				$anggaran = array(
					'pagu_1' => 0,
					'pagu_2' => 0,
					'pagu_3' => 0,
					'pagu_4' => 0,
					'pagu_5' => 0,
					'realisasi_pagu_1' => 0,
					'realisasi_pagu_2' => 0,
					'realisasi_pagu_3' => 0,
					'realisasi_pagu_4' => 0,
					'realisasi_pagu_5' => 0,
				);
				switch ($_POST['type_indikator']) {
					case '5':
						$indikator = $wpdb->get_row($wpdb->prepare(
							"
							SELECT 
								* 
							FROM data_renstra_sub_kegiatan 
							WHERE active=1 
								AND id=%d 
								AND id_unit=%d 
								AND id_jadwal=%d",
							$_POST['id'],
							$_POST['id_skpd'],
							$_POST['id_jadwal']
						), ARRAY_A);
						$edit_realisasi_pagu = 'contenteditable="true"';
						$anggaran = $wpdb->get_row($wpdb->prepare("
							SELECT
								pagu_1,
								pagu_2,
								pagu_3,
								pagu_4,
								pagu_5,
								realisasi_pagu_1,
								realisasi_pagu_2,
								realisasi_pagu_3,
								realisasi_pagu_4,
								realisasi_pagu_5
							FROM data_renstra_sub_kegiatan
							WHERE id_jadwal=%d
								AND active=1
								AND id_unik_indikator is NULL
								AND id_unit=%d
								AND id_unik=%s
						", $_POST['id_jadwal'], $_POST['id_skpd'], $indikator['id_unik']), ARRAY_A);
						break;

					case '4':
						$indikator = $wpdb->get_row($wpdb->prepare(
							"
							SELECT 
								* 
							FROM data_renstra_kegiatan 
							where active=1 
								AND id=%d 
								AND id_unit=%d 
								AND id_jadwal=%d
						",
							$_POST['id'],
							$_POST['id_skpd'],
							$_POST['id_jadwal']
						), ARRAY_A);
						$anggaran = $this->get_pagu_renstra_keg(array(
							'id_jadwal' => $_POST['id_jadwal'],
							'id_skpd' => $_POST['id_skpd'],
							'id_unik' => $indikator['id_unik']
						));
						break;

					case '3':
						$indikator = $wpdb->get_row($wpdb->prepare(
							"SELECT * FROM data_renstra_program WHERE active=1 AND id=%d AND id_unit=%d AND id_jadwal=%d",
							$_POST['id'],
							$_POST['id_skpd'],
							$_POST['id_jadwal']
						), ARRAY_A);
						$anggaran = $this->get_pagu_renstra_prog(array(
							'id_jadwal' => $_POST['id_jadwal'],
							'id_skpd' => $_POST['id_skpd'],
							'id_unik' => $indikator['id_unik']
						));
						break;

					case '2':
						$indikator = $wpdb->get_row($wpdb->prepare(
							"SELECT *, indikator_teks as indikator FROM data_renstra_sasaran WHERE active=1 AND id=%d AND id_unit=%d AND id_jadwal=%d",
							$_POST['id'],
							$_POST['id_skpd'],
							$_POST['id_jadwal']
						), ARRAY_A);
						$anggaran = $this->get_pagu_renstra_sasaran(array(
							'id_jadwal' => $_POST['id_jadwal'],
							'id_skpd' => $_POST['id_skpd'],
							'id_unik' => $indikator['id_unik']
						));
						break;

					case '1':
						$indikator = $wpdb->get_row($wpdb->prepare(
							"SELECT *, indikator_teks as indikator FROM data_renstra_tujuan WHERE active=1 AND id=%d AND id_unit=%d AND id_jadwal=%d",
							$_POST['id'],
							$_POST['id_skpd'],
							$_POST['id_jadwal']
						), ARRAY_A);
						$anggaran = $this->get_pagu_renstra_tujuan(array(
							'id_jadwal' => $_POST['id_jadwal'],
							'id_skpd' => $_POST['id_skpd'],
							'id_unik' => $indikator['id_unik']
						));
						break;

					default:
						$return['status'] = 'error';
						$return['message'] = 'REQUEST TIDAK SPESIFIK';
						break;
				}

				if (!empty($indikator)) {
					for ($i = 1; $i <= $_POST['lama_pelaksanaan']; $i++) {
						$capaian_pagu = 0;
						if (
							!empty($indikator['pagu_' . $i])
							&& !empty($indikator['realisasi_pagu_' . $i])
						) {
							$capaian_pagu = ($indikator['realisasi_pagu_' . $i] / $indikator['pagu_' . $i]) * 100;
						}
						$capaian_target = 0;
						if (
							!empty($indikator['target_' . $i])
							&& !empty($indikator['realisasi_target_' . $i])
							&& is_numeric($indikator['target_' . $i])
							&& is_numeric($indikator['realisasi_target_' . $i])
						) {
							$capaian_target = ($indikator['realisasi_target_' . $i] / $indikator['target_' . $i]) * 100;
						}
						$body_renstra .= '
							<tr>
								<td class="text-center">' . ($_POST['tahun_awal'] + $i - 1) . '</td>
								<td class="text-right pagu_' . $i . '">' . $this->_number_format($anggaran['pagu_' . $i]) . '</td>
								<td class="text-right realisasi_pagu_' . $i . '" ' . $edit_realisasi_pagu . ' onkeyup="setTotalRealisasi();" onkeypress="onlyNumber(event);">' . $this->_number_format($anggaran['realisasi_pagu_' . $i]) . '</td>
								<td class="text-center capaian_pagu_' . $i . '">' . $this->pembulatan($capaian_pagu) . '</td>
								<td class="text-center target_' . $i . '">' . $indikator['target_' . $i] . '</td>
								<td class="text-center realisasi_target_' . $i . '" contenteditable="true" onkeyup="setTotalRealisasi();" onkeypress="onlyNumber(event);">' . $indikator['realisasi_target_' . $i] . '</td>
								<td class="text-center capaian_target_' . $i . '">' . $this->pembulatan($capaian_target) . '</td>
								<td class="keterangan_' . $i . '" contenteditable="true">' . $indikator['keterangan_' . $i] . '</td>
							</tr>';
					}
					$return['body_renstra'] = $body_renstra;
					$return['indikator'] = $indikator['indikator'];
					$return['satuan'] = $indikator['satuan'];
					$return['target_awal'] = $indikator['target_awal'];
					$return['target_akhir'] = $indikator['target_akhir'];
				}
			} else {
				$return['status'] = 'error';
				$return['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$return['status'] = 'error';
			$return['message'] = 'Format Salah!';
		}
		echo json_encode($return);
		exit();
	}

	public function save_monev_renstra()
	{
		global $wpdb;
		$current_user = wp_get_current_user();
		$return = array(
			'status' => 'success',
			'message' => 'Berhasil simpan data!',
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				$data = array(
					'realisasi_target_1' => $_POST['realisasi_target'][1],
					'realisasi_target_2' => $_POST['realisasi_target'][2],
					'realisasi_target_3' => $_POST['realisasi_target'][3],
					'realisasi_target_4' => $_POST['realisasi_target'][4],
					'realisasi_target_5' => $_POST['realisasi_target'][5],
					'keterangan_1' => $_POST['keterangan'][1],
					'keterangan_2' => $_POST['keterangan'][2],
					'keterangan_3' => $_POST['keterangan'][3],
					'keterangan_4' => $_POST['keterangan'][4],
					'keterangan_5' => $_POST['keterangan'][5],
					'update_at' => current_time('mysql')
				);

				$table = 'data_renstra_tujuan';
				if ($_POST['type_indikator'] == '2') {
					$table = 'data_renstra_sasaran';
				} else if ($_POST['type_indikator'] == '3') {
					$table = 'data_renstra_program';
				} else if ($_POST['type_indikator'] == '4') {
					$table = 'data_renstra_kegiatan';
				} else if ($_POST['type_indikator'] == '5') {
					$table = 'data_renstra_sub_kegiatan';
				}
				$cek_data = $wpdb->get_row($wpdb->prepare("
					select 
						id,
						id_unik 
					from $table 
					where id=%d
				", $_POST['id_indikator']), ARRAY_A);

				if (empty($cek_data['id'])) {
					$return['status'] = 'error';
					$return['message'] = 'Indikator tidak ditemukan!';
				} else {
					$wpdb->update($table, $data, array('id' => $cek_data['id']));
					if ($_POST['type_indikator'] == '5') {
						$id_sub = $wpdb->get_var($wpdb->prepare("
							select 
								id
							from $table 
							where id_unik=%s
								AND id_unik_indikator is null
								AND tahun_anggaran=%d
						", $cek_data['id_unik'], $_POST['tahun_anggaran']));
						$wpdb->update($table, array(
							'realisasi_pagu_1' => $_POST['realisasi_anggaran'][1],
							'realisasi_pagu_2' => $_POST['realisasi_anggaran'][2],
							'realisasi_pagu_3' => $_POST['realisasi_anggaran'][3],
							'realisasi_pagu_4' => $_POST['realisasi_anggaran'][4],
							'realisasi_pagu_5' => $_POST['realisasi_anggaran'][5],
						), array('id' => $id_sub));
					}
				}
				$return['sql'] = $wpdb->last_query;
			} else {
				$return['status'] = 'error';
				$return['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$return['status'] = 'error';
			$return['message'] = 'Format Salah!';
		}
		echo json_encode($return);
		exit();
	}

	public function valid_number($no)
	{
		$no = str_replace(array(','), array('.'), $no);
		return $no;
	}

	public function get_indikator_renstra_renja($options)
	{
		$renstra = $options['renstra'];
		$renja = $options['renja'];
		$type = $options['type'];
		$ret['renstra_sasaran'] = array();
		$ret['renstra_tujuan'] = array();
		$ret['renstra_indikator'] = array();
		$ret['total_pagu_renstra'] = array();
		$ret['total_pagu_renstra_tahun_sebelumnya'] = array();
		$ret['total_target_renstra_tahun_sebelumnya'] = array();
		$ret['total_pagu_renstra_tahun_ini'] = array();
		$ret['total_target_renstra_tahun_ini'] = array();
		$ret['total_capaian_pagu_renstra_tahun_ini'] = array();
		$ret['total_capaian_target_renstra_tahun_ini'] = array();
		$ret['total_target_renstra'] = array();
		$ret['total_pagu_renstra_renja'] = array();
		$ret['total_target_renstra_text'] = array();

		$ret['total_pagu_renstra_asli'] = array();
		$ret['total_pagu_renstra_tahun_sebelumnya_asli'] = array();
		$ret['total_pagu_renstra_tahun_ini_asli'] = array();
		$ret['total_capaian_pagu_renstra_tahun_ini_asli'] = array();
		foreach ($renja['indikator'] as $k_sub => $v_sub) {
			$ret['total_target_renstra_text'][$k_sub] = '<span class="total_target_renstra" data-id="' . $k_sub . '">0</span>';
			$ret['total_pagu_renstra_renja'][$k_sub] = '<span class="monev_total_renstra" data-id="' . $k_sub . '">0</span>';
			if ($options['type'] == 'kegiatan') {
				$ret['satuan_renstra'][$k_sub] = '<span class="satuan_renstra" data-id="' . $k_sub . '">' . $v_sub['satuanoutput'] . '</span>';
			} else if ($options['type'] == 'program') {
				$ret['satuan_renstra'][$k_sub] = '<span class="satuan_renstra" data-id="' . $k_sub . '">' . $v_sub['satuancapaian'] . '</span>';
			} else if ($options['type'] == 'sub_kegiatan') {
				$ret['satuan_renstra'][$k_sub] = '<span class="satuan_renstra" data-id="' . $k_sub . '">' . $v_sub['satuanoutput'] . '</span>';
			}
		}

		$no_ind = 0;
		foreach ($renstra as $k => $v) {
			if (empty($v['sasaran_teks'])) {
				continue;
			}
			if (empty($v['id_unik_indikator'])) {
				$sasaran_teks = explode('||', $v['sasaran_teks']);
				$tujuan_teks = explode('||', $v['tujuan_teks']);
				if ($options['type'] == 'program') {
					$ret['renstra_tujuan'][0] = $tujuan_teks[0];
					$ret['renstra_sasaran'][0] = $sasaran_teks[0];
				} else {
					$ret['renstra_tujuan'][0] = '<span class="renstra_kegiatan" data-id="' . $v['id'] . '">' . $tujuan_teks[0] . '</span>';
					$ret['renstra_sasaran'][0] = '<span class="renstra_kegiatan">' . $sasaran_teks[0] . '</span>';
				}
				$ret['total_pagu_renstra'][$k] = $v['pagu_1'] + $v['pagu_2'] + $v['pagu_3'] + $v['pagu_4'] + $v['pagu_5'];
				$ret['total_pagu_renstra_asli'][$k] = $ret['total_pagu_renstra'][$k];

				// total pagu renstra tahun sebelumnya
				$ret['total_pagu_renstra_tahun_sebelumnya'][$k] = 0;
				if ($options['tahun_renstra'] - 1 > 0) {
					for ($i = 1; $i < $options['tahun_renstra'] - 1; $i++) {
						$ret['total_pagu_renstra_tahun_sebelumnya'][$k] += $v['realisasi_pagu_' . $i];
					}
				}
				$ret['total_pagu_renstra_tahun_sebelumnya_asli'][$k] = $ret['total_pagu_renstra_tahun_sebelumnya'][$k];

				// total pagu renstra tahun ini
				$ret['total_pagu_renstra_tahun_ini'][$k] = $ret['total_pagu_renstra_tahun_sebelumnya'][$k] + $renja['realisasi'];
				$ret['total_pagu_renstra_tahun_ini_asli'][$k] = $ret['total_pagu_renstra_tahun_ini'][$k];

				// total capaian pagu renstra tahun ini
				$ret['total_capaian_pagu_renstra_tahun_ini'][$k] = 0;
				if (
					!empty($ret['total_pagu_renstra_tahun_ini'][$k])
					&& !empty($ret['total_pagu_renstra'][$k])
				) {
					$ret['total_capaian_pagu_renstra_tahun_ini'][$k] = $this->pembulatan(($ret['total_pagu_renstra_tahun_ini'][$k] / $ret['total_pagu_renstra'][$k]) * 100);
				}
				$ret['total_capaian_pagu_renstra_tahun_ini_asli'][$k] = $ret['total_capaian_pagu_renstra_tahun_ini'][$k];

				// data indikator
			} else {
				// total target renstra tahun sebelumnya
				$ret['total_target_renstra_tahun_sebelumnya'][$no_ind] = 0;
				if ($options['tahun_renstra'] - 1 > 0) {
					$ret['total_target_renstra_tahun_sebelumnya'][$no_ind] += $v['realisasi_target_' . ($options['tahun_renstra'] - 1)];
				} else {
					if (empty($v['target_awal'])) {
						$v['target_awal'] = 0;
					}
					$ret['total_target_renstra_tahun_sebelumnya'][$no_ind] = $v['target_awal'];
				}

				// total target renstra tahun ini
				$ret['total_target_renstra_tahun_ini'][$no_ind] = $ret['total_target_renstra_tahun_sebelumnya'][$no_ind];

				// total target
				if (empty($v['target_akhir'])) {
					$v['target_akhir'] = 0;
				}
				$ret['total_target_renstra_text'][$no_ind] = $v['target_akhir'];

				// total capaian target renstra tahun ini
				$ret['total_capaian_target_renstra_tahun_ini'][$no_ind] = 0;
				if (
					!empty($ret['total_target_renstra_tahun_sebelumnya'][$no_ind])
					&& !empty($ret['total_target_renstra_text'][$no_ind])
				) {
					$ret['total_capaian_target_renstra_tahun_ini'][$no_ind] = $this->pembulatan(($ret['total_target_renstra_tahun_sebelumnya'][$no_ind] / $ret['total_target_renstra_text'][$no_ind]) * 100);
				}

				// satuan renstra
				if (empty($v['satuan'])) {
					$v['satuan'] = str_replace('data-id="', 'class="mod_total_renstra" data-id="', $options['default_satuan_renstra']);
				}
				$ret['satuan_renstra'][$no_ind] = $v['satuan'];

				// debug RENSTRA
				$ret['renstra_indikator'][] = '<li data-id=' . $v['id_unik_indikator'] . '><span class="indikator_renstra_text_hide">' . $v['indikator'] . '</span> <span class="target_indikator_renstra_text_hide">' . $v['target_1'] . ' | ' . $v['target_2'] . ' | ' . $v['target_3'] . ' | ' . $v['target_4'] . ' | ' . $v['target_5'] . '</span> <span class="satuan_indikator_renstra_text_hide">' . $v['satuan'] . '</span></li>';
				$no_ind++;
			}
		}

		// total target indikator renstra
		$ret['total_target_renstra_text'] = implode('<br>', $ret['total_target_renstra_text']);

		// satuan indikator renstra
		if (empty($ret['satuan_renstra'])) {
			$ret['satuan_renstra'] = str_replace('data-id="', 'class="mod_total_renstra" data-id="', $options['default_satuan_renstra']);
		} else {
			$ret['satuan_renstra'] = implode('<br>', $ret['satuan_renstra']);
		}
		$ret['total_pagu_renstra_renja'] = implode('<br>', $ret['total_pagu_renstra_renja']);

		// total pagu renstra
		foreach ($ret['total_pagu_renstra'] as $k => $pagu) {
			$ret['total_pagu_renstra'][$k] = $this->_number_format($pagu);
		}
		if (empty($ret['total_pagu_renstra'])) {
			$ret['total_pagu_renstra'][0] = 0;
		}
		$ret['total_pagu_renstra'] = implode('<br>', $ret['total_pagu_renstra']);

		// total realisasi pagu renstra sampai dengan tahun sebelumnya
		foreach ($ret['total_pagu_renstra_tahun_sebelumnya'] as $k => $pagu) {
			$ret['total_pagu_renstra_tahun_sebelumnya'][$k] = $this->_number_format($pagu);
		}
		if (empty($ret['total_pagu_renstra_tahun_sebelumnya'])) {
			$ret['total_pagu_renstra_tahun_sebelumnya'][0] = 0;
		}
		$ret['total_pagu_renstra_tahun_sebelumnya'] = implode('<br>', $ret['total_pagu_renstra_tahun_sebelumnya']);

		// total realisasi target renstra sampai dengan tahun sebelumnya
		foreach ($ret['total_target_renstra_tahun_sebelumnya'] as $k => $target) {
			$ret['total_target_renstra_tahun_sebelumnya'][$k] = $target;
		}
		if (empty($ret['total_target_renstra_tahun_sebelumnya'])) {
			$ret['total_target_renstra_tahun_sebelumnya'][0] = 0;
		}
		$ret['total_target_renstra_tahun_sebelumnya'] = implode('<br>', $ret['total_target_renstra_tahun_sebelumnya']);

		// total realisasi pagu renstra sampai dengan tahun sebelumnya
		foreach ($ret['total_pagu_renstra_tahun_ini'] as $k => $pagu) {
			$ret['total_pagu_renstra_tahun_ini'][$k] = $this->_number_format($pagu);
		}
		if (empty($ret['total_pagu_renstra_tahun_ini'])) {
			$ret['total_pagu_renstra_tahun_ini'][0] = 0;
		}
		$ret['total_pagu_renstra_tahun_ini'] = implode('<br>', $ret['total_pagu_renstra_tahun_ini']);

		// total realisasi target renstra sampai dengan tahun sebelumnya
		foreach ($ret['total_target_renstra_tahun_ini'] as $k => $target) {
			$ret['total_target_renstra_tahun_ini'][$k] = $target;
		}
		if (empty($ret['total_target_renstra_tahun_ini'])) {
			$ret['total_target_renstra_tahun_ini'][0] = 0;
		}
		$ret['total_target_renstra_tahun_ini'] = implode('<br>', $ret['total_target_renstra_tahun_ini']);

		$ret['renstra_sasaran'] = implode('<br>', $ret['renstra_sasaran']) . ' <ul class="indikator_renstra">' . implode('', $ret['renstra_indikator']) . '</ul>';
		$ret['renstra_tujuan'] = implode('<br>', $ret['renstra_tujuan']);
		$ret['total_capaian_pagu_renstra_tahun_ini'] = implode('<br>', $ret['total_capaian_pagu_renstra_tahun_ini']);
		$ret['total_capaian_target_renstra_tahun_ini'] = implode('<br>', $ret['total_capaian_target_renstra_tahun_ini']);
		return $ret;
	}

	public function get_ref_unit($options)
	{
		global $wpdb;
		$tahun_anggaran = $options['tahun_anggaran'];
		$sql = $wpdb->prepare(
			"
			SELECT 
				*
			FROM ref_sub_unit r"
		);
		$new_unit = array();
		$unit_simda = $this->simda->CurlSimda(array('query' => $sql));
		foreach ($unit_simda as $k => $v) {
			$new_unit[$v->kd_urusan . '.' . $v->kd_bidang . '.' . $v->kd_unit . '.' . $v->kd_sub] = $v;
		}
		return $new_unit;
	}

	public function get_rka_simda()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil get RKA SIMDA!',
			'data'	=> array(),
			'data_blm_singkron'	=> array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$unit_simda = $this->get_ref_unit(array('tahun_anggaran' => $tahun_anggaran));
				foreach ($_POST['id_skpd'] as $id_skpd) {
					$kd_unit_simda_asli = get_option('_crb_unit_' . $id_skpd);
					$kd_unit_simda = explode('.', $kd_unit_simda_asli);
					if (
						!empty($kd_unit_simda)
						&& !empty($kd_unit_simda[3])
					) {
						if (!empty($unit_simda[$kd_unit_simda_asli])) {
							unset($unit_simda[$kd_unit_simda_asli]);
						}
						$_kd_urusan = $kd_unit_simda[0];
						$_kd_bidang = $kd_unit_simda[1];
						$kd_unit = $kd_unit_simda[2];
						$kd_sub_unit = $kd_unit_simda[3];
						$sql = $wpdb->prepare(
							"
							SELECT 
								SUM(r.total) as total
							FROM ta_belanja_rinc_sub r
							WHERE r.tahun = %d
								AND r.kd_urusan = %d
								AND r.kd_bidang = %d
								AND r.kd_unit = %d
								AND r.kd_sub = %d
							",
							$tahun_anggaran,
							$_kd_urusan,
							$_kd_bidang,
							$kd_unit,
							$kd_sub_unit
						);
						$pagu = $this->simda->CurlSimda(array('query' => $sql));
						if (!empty($pagu[0])) {
							$ret['data'][$id_skpd] = number_format($pagu[0]->total, 0, ",", ".");
						} else {
							$ret['data'][$id_skpd] = 0;
						}
					} else {
						$ret['data'][$id_skpd] = 0;
					}
				}
				foreach ($unit_simda as $k => $v) {
					$ret['data_blm_singkron'][$k] = $v;
				}
			} else {
				$ret = array(
					'status' => 'error',
					'message' => 'Apikey salah!',
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message' => 'Format salah!',
			);
		}
		die(json_encode($ret));
	}

	function get_dpa_simda()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil get DPA SIMDA!',
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$unit_simda = $this->get_ref_unit(array('tahun_anggaran' => $tahun_anggaran));
				foreach ($_POST['id_skpd'] as $id_skpd) {
					$kd_unit_simda_asli = get_option('_crb_unit_' . $id_skpd);
					$kd_unit_simda = explode('.', $kd_unit_simda_asli);
					if (
						!empty($kd_unit_simda)
						&& !empty($kd_unit_simda[3])
					) {
						if (!empty($unit_simda[$kd_unit_simda_asli])) {
							unset($unit_simda[$kd_unit_simda_asli]);
						}
						$_kd_urusan = $kd_unit_simda[0];
						$_kd_bidang = $kd_unit_simda[1];
						$kd_unit = $kd_unit_simda[2];
						$kd_sub_unit = $kd_unit_simda[3];
						$sql = $wpdb->prepare(
							"
							SELECT 
								SUM(r.total) as total
							FROM ta_rask_arsip r
							WHERE r.tahun = %d
								AND r.kd_perubahan = (SELECT MAX(Kd_Perubahan) FROM Ta_Rask_Arsip where tahun=%d)
								AND r.kd_urusan = %d
								AND r.kd_bidang = %d
								AND r.kd_unit = %d
								AND r.kd_sub = %d
								AND r.kd_rek_1 = 5
							",
							$tahun_anggaran,
							$tahun_anggaran,
							$_kd_urusan,
							$_kd_bidang,
							$kd_unit,
							$kd_sub_unit
						);
						$pagu = $this->simda->CurlSimda(array('query' => $sql));
						if (!empty($pagu[0])) {
							$ret['data'][$id_skpd] = number_format($pagu[0]->total, 0, ",", ".");
						} else {
							$ret['data'][$id_skpd] = 0;
						}
					} else {
						$ret['data'][$id_skpd] = 0;
					}
				}
				foreach ($unit_simda as $k => $v) {
					$ret['data_blm_singkron'][$k] = $v;
				}
			} else {
				$ret = array(
					'status' => 'error',
					'message' => 'Apikey salah!',
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message' => 'Format salah!',
			);
		}
		die(json_encode($ret));
	}

	public function get_sumber_dana_mapping()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$master_sumberdana = '';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				// sumber dana asli sipd
				if ($_POST['format_sumber_dana'] == 1) {

					$total_sd = 0;
					$sumberdana = $wpdb->get_results('
						select 
							d.iddana, d.kodedana, d.namadana, sum(d.pagudana) total, count(s.kode_sbl) jml 
						from data_dana_sub_keg d
							INNER JOIN data_sub_keg_bl s ON d.kode_sbl=s.kode_sbl
								AND s.tahun_anggaran=d.tahun_anggaran
								AND s.active=d.active
						where d.tahun_anggaran=' . $_POST['tahun_anggaran'] . '
							and s.id_sub_skpd=' . $_POST['id_skpd'] . '
							and d.active=1
						group by iddana
						order by kodedana ASC
					', ARRAY_A);

					foreach ($sumberdana as $key => $val) {
						$no++;
						$title = 'Laporan APBD Per Sumber Dana ' . $val['kodedana'] . ' ' . $val['namadana'] . ' | ' . $_POST['tahun_anggaran'];
						$custom_post = $this->get_page_by_title($title, OBJECT, 'page');
						$url_skpd = $this->get_link_post($custom_post);
						if (empty($val['kodedana'])) {
							$val['kodedana'] = '';
							$val['namadana'] = 'Belum Di Setting';
						}
						$master_sumberdana .= '
							<tr>
								<td class="text_tengah atas kanan bawah kiri">' . $no . '</td>
								<td class="text_kiri atas kanan bawah">' . $val['kodedana'] . '</td>
								<td class="text_kiri atas kanan bawah"><a href="' . $url_skpd . '&id_skpd=' . $_POST['id_skpd'] . '&mapping=1" target="_blank" data-id="' . $title . '">' . $val['namadana'] . '</a></td>
								<td class="text_kanan atas kanan bawah">' . number_format($val['total'], 0, ',', '.') . '</td>
								<td class="text_tengah atas kanan bawah">' . $val['jml'] . '</td>
								<td class="text_tengah atas kanan bawah">' . $val['iddana'] . '</td>
								<td class="text_tengah atas kanan bawah">' . $_POST['tahun_anggaran'] . '</td>
							</tr>
						';
						$total_sd += $val['total'];
					}

					$total_rka = $wpdb->get_results($wpdb->prepare('
					select 
						sum(pagu) as total_rka
					from data_sub_keg_bl 
					where tahun_anggaran=%d
						and id_sub_skpd=%d
						and active=1
					', $_POST['tahun_anggaran'], $_POST['id_skpd']), ARRAY_A);

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
					", $_POST['tahun_anggaran'], $_POST['id_skpd']), ARRAY_A);

					$title = 'RFK ' . $skpd[0]['nama_skpd'] . ' ' . $skpd[0]['kode_skpd'] . ' | ' . $_POST['tahun_anggaran'];
					$shortcode = '[monitor_rfk tahun_anggaran="' . $_POST['tahun_anggaran'] . '" id_skpd="' . $skpd[0]['id_skpd'] . '"]';
					$update = false;
					$url_skpd = $this->generatePage($title, $_POST['tahun_anggaran'], $shortcode, $update);

					$master_sumberdana .= "
						<tr>
							<td colspan='3' class='atas kanan bawah kiri text_tengah text_blok'>Total</td>
							<td class='atas kanan bawah text_kanan text_blok'>" . number_format($total_sd, 0, ',', '.') . "</td>
							<td class='text_tengah kanan bawah text_blok' colspan='2'>Total RKA</td>
							<td class='text_kanan kanan bawah text_blok'><a target='_blank' href='" . $url_skpd . "&id_skpd=" . $_POST['id_skpd'] . "'>" . number_format($total_rka[0]['total_rka'], 0, ",", ".") . "</a></td>
						</tr>
					";

					$table_content =
						'<thead>
							<tr class="text_tengah">
								<th class="atas kanan bawah kiri text_tengah" style="width: 20px; vertical-align: middle;">No</th>
								<th class="atas kanan bawah text_tengah" style="width: 100px; vertical-align: middle;">Kode</th>
								<th class="atas kanan bawah text_tengah" style="vertical-align: middle;">Sumber Dana</th>
								<th class="atas kanan bawah text_tengah" style="vertical-align: middle;">Pagu Sumber Dana (Rp.)</th>
								<th class="atas kanan bawah text_tengah" style="width:50px; vertical-align: middle;">Jumlah Sub Kegiatan</th>
								<th class="atas kanan bawah text_tengah" style="width: 50px; vertical-align: middle;">ID Dana</th>
								<th class="atas kanan bawah text_tengah" style="width: 110px; vertical-align: middle;">Tahun Anggaran</th>
							</tr>
						</thead>
						<tbody id="body-sumber-dana">' . $master_sumberdana . '</tbody>
						<tfooter>

						</tfooter>
						';

					$list_dokumentasi = "
						<li>Format Per Sumber Dana SIPD menampilkan daftar sumber dana berdasarkan sumber dana yang di input melalui SIPD Merah.</li>
						<li>Pagu Sumber Dana merupakan akumulasi pagu sumber dana dari masing-masing sub kegiatan yang di kelompokan berdasarkan jenis sumber dana.</li>
						<li>Jumlah Sub Kegiatan merupakan jumlah sub kegiatan dengan sumber dana yang sama.</li>
					";

					$return = array(
						'status' => 'success',
						'table_content' => $table_content,
						'list_dokumentasi' => $list_dokumentasi,
					);
					// 	sumber dana mapping
				} elseif ($_POST['format_sumber_dana'] == 2) {

					$data = array();
					$total_harga = 0;
					$realisasi = 0;
					$jml_rincian = 0;

					$sub_keg_bl = $wpdb->get_results(
						$wpdb->prepare(
							"
						SELECT 
							kode_sbl, 
							nama_sub_giat 
						FROM data_sub_keg_bl 
						WHERE 
							active=1 AND 
							id_sub_skpd=%d AND 
							tahun_anggaran=%d",
							$_POST['id_skpd'],
							$_POST['tahun_anggaran']
						),
						ARRAY_A
					);

					foreach ($sub_keg_bl as $k1 => $s) {
						$rka = $wpdb->get_results($wpdb->prepare('
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
				    				and r.kode_sbl=%s
				    		', $_POST['tahun_anggaran'], $s['kode_sbl']), ARRAY_A);
						// die($wpdb->last_query);
						foreach ($rka as $k2 => $v2) {
							if (empty($v2['realisasi'])) {
								$v2['realisasi'] = 0;
							}
							$mapping = $wpdb->get_results($wpdb->prepare('
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
				    			', $_POST['tahun_anggaran'], $v2['id_rinci_sub_bl']), ARRAY_A);

							if (!empty($mapping)) {
								foreach ($mapping as $key_m => $v_m) {
									if (empty($data[$v_m['id_dana']])) {
										$data[$v_m['id_dana']] = array(
											'id_dana' => $v_m['id_dana'],
											'kode_dana' => $v_m['kode_dana'],
											'nama_dana' => $v_m['nama_dana'],
											'jml_rincian' => 0,
											'pagu' => 0,
											'realisasi' => 0,
											'id_rinci_sub_bl' => array()
										);
									}

									$data[$v_m['id_dana']]['id_rinci_sub_bl'][] = $v2['id_rinci_sub_bl'];
									$data[$v_m['id_dana']]['jml_rincian']++;
									$data[$v_m['id_dana']]['pagu'] += $v2['total_harga'];
									$data[$v_m['id_dana']]['realisasi'] += $v2['realisasi'];
								}
							} else {
								$key = 0;
								if (empty($data[$key])) {
									$data[$key] = array(
										'id_dana' => '',
										'kode_dana' => '-',
										'nama_dana' => 'Belum di mapping!',
										'jml_rincian' => 0,
										'pagu' => 0,
										'realisasi' => 0,
										'id_rinci_sub_bl' => array()
									);
								}
								$data[$key]['jml_rincian']++;
								$data[$key]['pagu'] += $v2['total_harga'];
								$data[$key]['realisasi'] += $v2['realisasi'];
							}
							$total_harga += $v2['total_harga'];
							$realisasi += $v2['realisasi'];
							$jml_rincian++;
						}
					}

					ksort($data);
					$no = 0;
					foreach ($data as $key => $value) {
						$no++;
						$title = 'Laporan APBD Per Sumber Dana ' . $value['kode_dana'] . ' ' . $value['nama_dana'] . ' | ' . $_POST['tahun_anggaran'];
						$shortcode = '[monitor_sumber_dana tahun_anggaran="' . $_POST['tahun_anggaran'] . '" id_sumber_dana="' . $value['id_dana'] . '"]';
						$update = false;
						$url_skpd = $this->generatePage($title, $_POST['tahun_anggaran'], $shortcode, $update);

						// $id_rinci_sub_bl=implode(",", $value['id_rinci_sub_bl']);
						$id_rinci_sub_bl = 0;
						$master_sumberdana .= '
			    			<tr>
			    				<td class="atas kanan bawah kiri text_tengah">' . $no . '</td>
			    				<td class="atas kanan bawah" data-id-rinci-sub-bl="' . $id_rinci_sub_bl . '">' . $value['kode_dana'] . '</td>
			    				<td class="atas kanan bawah"><a href="' . $url_skpd . '&id_skpd=' . $_POST['id_skpd'] . '&mapping=2" target="_blank">' . $value['nama_dana'] . '</a></td>
			    				<td class="atas kanan bawah text_kanan">' . number_format($value['pagu'], 0, ",", ".") . '</td>
			    				<td class="atas kanan bawah text_kanan">' . number_format($value['realisasi'], 0, ",", ".") . '</td>
			    				<td class="atas kanan bawah text_tengah">' . number_format($value['jml_rincian'], 0, ",", ".") . '</td>
			    			</tr>
			    		';
					}

					$master_sumberdana .= "
						<tr>
							<td colspan='3' class='atas kanan bawah kiri text_tengah text_blok'>Total</td>
							<td class='atas kanan bawah text_kanan text_blok'>" . number_format($total_harga, 0, ',', '.') . "</td>
							<td class='atas kanan bawah text_kanan text_blok'>" . number_format($realisasi, 0, ',', '.') . "</td>
							<td class='atas kanan bawah text_tengah text_blok'>" . $jml_rincian . "</td>
						</tr>
					";

					$table_content =
						'<thead>
							<tr class="text_tengah">
								<th class="atas kanan bawah kiri text_tengah" style="width: 20px; vertical-align: middle;">No</th>
								<th class="atas kanan bawah text_tengah" style="width: 100px; vertical-align: middle;">Kode</th>
								<th class="atas kanan bawah text_tengah" style="vertical-align: middle;">Sumber Dana</th>
								<th class="atas kanan bawah text_tengah" style="vertical-align: middle;">Pagu Sumber Dana Mapping (Rp.)</th>
								<th class="atas kanan bawah text_tengah" style="vertical-align: middle;">Realisasi Rincian</th>
								<th class="atas kanan bawah text_tengah" style="width: 110px; vertical-align: middle;">Jumlah Rincian</th>
							</tr>
						</thead>
						<tbody id="body-sumber-dana">' . $master_sumberdana . '</tbody>';

					$list_dokumentasi = "
						<li>Format Per Sumber Dana Mapping menampilkan daftar sumber dana berdasarkan hasil mapping rincian dari masing-masing sub kegiatan.</li>
						<li>Pagu Sumber Dana merupakan akumulasi pagu rincian item berdasarkan hasil mapping dari masing-masing sub kegiatan yang dikelompokan berdasarkan sumber dana yang sama.</li>
						<li>Realisasi Rincian merupakan akumulasi realisasi rincian yang diinput melalui halaman mapping sumber dana yang dikelompokkan berdasarkan sumber dana yang sama.</li>
						<li>Jumlah Rincian merupakan akumulasi dari rincian yang diinput melalui halaman mapping sumber yang dikelompokkan berdasarkan sumber dana yang sama.</li>
					";

					$return = array(
						'status' => 'success',
						'table_content' => $table_content,
						'list_dokumentasi' => $list_dokumentasi
					);
					// sumber dana kombinasi
				} elseif ($_POST['format_sumber_dana'] == 3) {
					$sub_keg = $wpdb->get_results($wpdb->prepare('
		    			select
		    				kode_sbl,
		    				pagu,
		    				pagu_simda
		    			from data_sub_keg_bl
		    			where tahun_anggaran=%d
		    				and active=1
		    				and id_sub_skpd=%d
		    		', $_POST['tahun_anggaran'], $_POST['id_skpd']), ARRAY_A);
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
						', $_POST['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);
						$id_dana = array();
						$pagu_dana_raw = array();
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
							$data_all[$_kode_sd] = array(
								'id_dana' => $_id_dana,
								'pagu_dana' => $_pagu_dana,
								'kode_sd' => $_kode_sd,
								'kombinasi_id_sd' => $_kombinasi_id_sd,
								'kombinasi_kode_sd' => $_kombinasi_kode_sd,
								'kombinasi_nama_sd' => $_kombinasi_nama_sd,
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
					ksort($data_all);
					foreach ($data_all as $k => $val) {
						$no++;
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

						$title = 'Laporan APBD Per Sumber Dana ' . $val['kombinasi_kode_sd'] . ' ' . $val['kombinasi_nama_sd'] . ' | ' . $_POST['tahun_anggaran'];
						$shortcode = '[monitor_sumber_dana tahun_anggaran="' . $_POST['tahun_anggaran'] . '" id_sumber_dana="' . $val['kombinasi_id_sd'] . '"]';
						$update = false;
						$url_skpd = $this->generatePage($title, $_POST['tahun_anggaran'], $shortcode, $update);
						$url_skpd .= "&id_skpd=" . $_POST['id_skpd'] . "&mapping=3";

						$master_sumberdana .= '
							<tr>
								<td class="text_tengah atas kanan bawah kiri">' . $no . '</td>
								<td class="atas kanan bawah">' . $val['kode_sd'] . '</td>
								<td class="atas kanan bawah"><a href="' . $url_skpd . '" data-title="' . $title . '" target="_blank">' . $val['nama_sd'] . '</a>' . '</td>
								<td class="text_kanan atas kanan bawah atas kanan bawah">' . implode('<br>', $pagu_dana) . '</td>
								<td class="text_kanan atas kanan bawah atas kanan bawah">' . number_format($val['pagu_rka'], 0, ",", ".") . '</td>
								<td class="text_tengah atas kanan bawah atas kanan bawah">' . $val['jml_sub'] . '</td>
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
							<td class="text_tengah atas kanan bawah kiri" colspan="3">Total</td>
							<td class="text_kanan atas kanan bawah ">' . number_format($total_sd, 0, ",", ".") . '</td>
							<td class="text_kanan atas kanan bawah "><a target="_blank" href="' . $url_skpd . '&id_skpd=' . $_POST['id_skpd'] . '">' . number_format($total_rka, 0, ",", ".") . '</a></td>
							<td class="text_tengah atas kanan bawah ">' . number_format($jml_sub, 0, ",", ".") . '</td>
						</tr>
					';
					$table_content = '
		        		<table class="wp-list-table widefat fixed striped">
		        			<thead>
		        				<tr class="text_tengah atas kanan bawah kiri">
		        					<th class="text_tengah atas kanan bawah" style="width: 20px">No</th>
		        					<th class="text_tengah atas kanan bawah" style="width: 100px">Kode</th>
		        					<th class="text_tengah atas kanan bawah">Sumber Dana</th>
		        					<th class="text_tengah atas kanan bawah" style="width: 150px">Pagu Sumber Dana (Rp.)</th>
		        					<th class="text_tengah atas kanan bawah" style="width: 150px">Pagu RKA (Rp.)</th>
		        					<th class="text_tengah atas kanan bawah" style="width: 150px">Jumlah Sub Keg</th>
		        				</tr>
		        			</thead>
		        			<tbody>
		        				' . $master_sumberdana . '
		        			</tbody>
		        		</table>
		    		';

					$list_dokumentasi = "
						<li>Format Kombinasi Sumber Dana SIPD menampilkan daftar sumber dana berdasarkan kombinasi sumber dana yang di input melalui SIPD Merah.</li>
						<li>Pagu Sumber Dana merupakan akumulasi pagu sumber dana dari masing-masing sub kegiatan yang di kelompokan berdasarkan kombinasi sumber dana yang sama.</li>
						<li>Jumlah Sub Kegiatan merupakan jumlah sub kegiatan yang di kelompokan berdasarkan kombinasi sumber dana yang sama.</li>
					";

					$return = array(
						'status' => 'success',
						'table_content' => $table_content,
						'list_dokumentasi' => $list_dokumentasi
					);
				}
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_rincian_sumber_dana_mapping()
	{

		global $wpdb;
		$data_all = array(
			'data' => array()
		);

		$bulan = (int) date('m');
		$list_sub_keg_bl = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT 
				a.kode_sbl, 
				a.nama_sub_giat,
				a.pagu,
				a.pagu_simda,
				b.realisasi_anggaran 
			FROM data_sub_keg_bl a
				LEFT JOIN data_rfk b
					ON 
						a.kode_sbl=b.kode_sbl AND 
						a.tahun_anggaran=b.tahun_anggaran AND
						a.id_sub_skpd=b.id_skpd AND
						b.bulan = " . $bulan . "
			WHERE 
				a.active=1 AND 
				a.id_sub_skpd=%d AND 
				a.tahun_anggaran=%d",
				$_POST['id_skpd'],
				$_POST['tahun_anggaran']
			),
			ARRAY_A
		);

		foreach ($list_sub_keg_bl as $key_sub => $val_sub) {
			if (empty($data_all['data'][$val_sub['kode_sbl']])) {
				$list_mapping = $wpdb->get_row(
					$wpdb->prepare(
						"
					SELECT 
						SUM(b.total_harga) total_harga,
						c.nama_dana 
					FROM data_mapping_sumberdana a 
						LEFT JOIN data_rka b 
							ON a.id_rinci_sub_bl=b.id_rinci_sub_bl
						LEFT JOIN data_sumber_dana c 
							ON a.id_sumber_dana=c.id_dana
					WHERE 
						b.kode_sbl=%s AND
						a.id_sumber_dana=%d AND
						a.tahun_anggaran=%d AND
						b.tahun_anggaran=%d AND
						a.active=1 AND 
						b.active=1 
					GROUP BY a.id_sumber_dana 
					ORDER BY a.id DESC",
						$val_sub['kode_sbl'],
						$_POST['id_sumber_dana'],
						$_POST['tahun_anggaran'],
						$_POST['tahun_anggaran']
					),
					ARRAY_A
				);

				if (!empty($list_mapping)) {
					$data_all['data'][$val_sub['kode_sbl']] = array(
						'kode_sbl' => $val_sub['kode_sbl'],
						'nama_sub_giat' => $val_sub['nama_sub_giat'],
						'pagu_rka' => $val_sub['pagu'],
						'pagu_dpa' => $val_sub['pagu_simda'],
						'realisasi_anggaran' => $val_sub['realisasi_anggaran'],
						'nama_dana' => $list_mapping['nama_dana'],
						'pagu_sumber_dana' => $list_mapping['total_harga'],
						'capaian' => !empty($val_sub['pagu_simda']) ? $this->pembulatan(($val_sub['realisasi_anggaran'] / $val_sub['pagu_simda']) * 100) : 0
					);
				}
			}
		}

		$no = 1;
		$html_sub_body = '';
		foreach ($data_all['data'] as $key => $value) {
			$html_sub_body .= "
				<tr class='sub_keg'>
					<td class='atas kanan bawah kiri'>" . $no . "</td>
					<td class='atas kanan bawah'>" . $value['nama_sub_giat'] . "</td>
					<td class='atas kanan bawah'>" . $value['nama_dana'] . "</td>
					<td class='atas kanan bawah text_kanan'>" . number_format($value['pagu_rka'], 0, ',', '.') . "</td>
					<td class='atas kanan bawah text_kanan'>" . number_format($value['pagu_sumber_dana'], 0, ',', '.') . "</td>
					<td class='atas kanan bawah text_kanan'>" . number_format($value['pagu_dpa'], 0, ',', '.') . "</td>
					<td class='atas kanan bawah text_kanan'>" . number_format($value['realisasi_anggaran'], 0, ',', '.') . "</td>
					<td class='atas kanan bawah text_tengah'>" . $value['capaian'] . "</td>
				</tr>
			";
			$no++;
		}

		$html_sub = '<h4 style="text-align: center; margin: 0; font-weight: bold;"></h4>
		    <table cellpadding="3" cellspacing="0" class="apbd-penjabaran" width="100%">
		        <thead>
		            <tr>
		                <td class="atas kanan bawah kiri text_tengah text_blok" width="20px;">No</td>
		                <td class="atas kanan bawah text_tengah text_blok">SKPD/Sub Kegiatan</td>
		                <td class="atas kanan bawah text_tengah text_blok" width="300px;">Sumber Dana</td>
		                <td class="atas kanan bawah text_tengah text_blok" style="width: 140px;">RKA SIPD (Rp.)</td>
		                <td class="atas kanan bawah text_tengah text_blok" style="width: 140px;">Pagu Sumber Dana SIPD (Rp.)</td>
		                <td class="atas kanan bawah text_tengah text_blok" style="width: 140px;">Pagu Simda (Rp.)</td>
		                <td class="atas kanan bawah text_tengah text_blok" style="width: 140px;">Ralisasi Simda (Rp.)</td>
		                <td class="atas kanan bawah text_tengah text_blok" style="width: 100px;">Capaian (%)</td>
		            </tr>
		        </thead>
		        <tbody>' . $html_sub_body . '</tbody>
		    </table>';

		$response = array(
			'rincian' => $html_sub
		);
		die(json_encode($response));
	}

	public function generatePage($nama_page, $tahun_anggaran, $content = false, $update = false)
	{
		$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
		if (empty($content)) {
			$content = '[monitor_sipd tahun_anggaran="' . $tahun_anggaran . '"]';
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
			$custom_post = $this->get_page_by_title($nama_page, OBJECT, 'page');
			update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
			update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
			update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
			update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
			update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
			update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
			update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
			update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
			update_post_meta($custom_post->ID, 'ast-global-header-display', 'disabled');
		} else if ($update || get_post_status($custom_post->ID) == 'public') {
			$_post['ID'] = $custom_post->ID;
			wp_update_post($_post);
			$_post['update'] = 1;
		}
		return $this->get_link_post($custom_post);
	}

	function add_param_get($url, $param)
	{
		$data = explode('?', $url);
		if (count($data) > 1) {
			$url .= $param;
		} else {
			$url .= '?' . $param;
		}
		return $url;
	}

	public function get_sub_keg_sipd()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get sub kegiatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_skpd = $_POST['id_skpd'];
				$idsumber = $_POST['idsumber'];
				$column = 's.*, u.nama_skpd as nama_skpd_data_unit, uu.nama_skpd as nama_skpd_data_unit_utama';
				$tipe = false;
				if (!empty($_POST['tipe'])) {
					if (
						$_POST['tipe'] == 'pt_tati'
						|| $_POST['tipe'] == 'simple'
					) {
						$tipe = $_POST['tipe'];
						$column = "
							s.id_skpd,
							uu.kode_skpd,
							uu.nama_skpd,
							s.id_sub_skpd,
							u.kode_skpd as kode_sub_skpd,
							u.nama_skpd as nama_sub_skpd,
							s.kode_bidang_urusan,
							s.nama_bidang_urusan,
							s.kode_program,
							s.nama_program,
							s.kode_giat as kode_kegiatan,
							s.nama_giat as nama_kegiatan,
							s.kode_sub_giat as kode_sub_kegiatan,
							s.nama_sub_giat as nama_sub_kegiatan,
							s.pagu as pagu_anggaran,
							s.tahun_anggaran,
							s.kode_sbl
						";
					}
				}
				$data_sub_keg = $wpdb->get_results($wpdb->prepare(
					"
					SELECT 
						$column
					from data_sub_keg_bl s 
					inner join data_unit u on s.id_sub_skpd = u.id_skpd
						and u.tahun_anggaran = s.tahun_anggaran
						and u.active = s.active
					inner join data_unit uu on s.id_skpd = uu.id_skpd
						and uu.tahun_anggaran = s.tahun_anggaran
						and uu.active = s.active
					where s.tahun_anggaran=%d
						and u.id_skpd=%d
						and s.active=1",
					$tahun_anggaran,
					$id_skpd
				), ARRAY_A);
				foreach ($data_sub_keg as $k => $v) {
					if (empty($tipe)) {
						$data_sub_keg[$k]['sub_keg_indikator'] = $wpdb->get_results("
							select 
								* 
							from data_sub_keg_indikator 
							where tahun_anggaran=" . $v['tahun_anggaran'] . "
								and kode_sbl='" . $v['kode_sbl'] . "'
								and active=1", ARRAY_A);
						$data_sub_keg[$k]['sub_keg_indikator_hasil'] = $wpdb->get_results("
							select 
								* 
							from data_keg_indikator_hasil 
							where tahun_anggaran=" . $v['tahun_anggaran'] . "
								and kode_sbl='" . $v['kode_sbl'] . "'
								and active=1", ARRAY_A);
						$data_sub_keg[$k]['tag_sub_keg'] = $wpdb->get_results("
							select 
								* 
							from data_tag_sub_keg 
							where tahun_anggaran=" . $v['tahun_anggaran'] . "
								and kode_sbl='" . $v['kode_sbl'] . "'
								and active=1", ARRAY_A);
						$data_sub_keg[$k]['capaian_prog_sub_keg'] = $wpdb->get_results("
							select 
								* 
							from data_capaian_prog_sub_keg 
							where tahun_anggaran=" . $v['tahun_anggaran'] . "
								and kode_sbl='" . $v['kode_sbl'] . "'
								and active=1", ARRAY_A);
						$data_sub_keg[$k]['output_giat'] = $wpdb->get_results("
							select 
								* 
							from data_output_giat_sub_keg 
							where tahun_anggaran=" . $v['tahun_anggaran'] . "
								and kode_sbl='" . $v['kode_sbl'] . "'
								and active=1", ARRAY_A);
						$data_sub_keg[$k]['lokasi_sub_keg'] = $wpdb->get_results("
							select 
								* 
							from data_lokasi_sub_keg 
							where tahun_anggaran=" . $v['tahun_anggaran'] . "
								and kode_sbl='" . $v['kode_sbl'] . "'
								and active=1", ARRAY_A);
					} else if ($tipe == 'pt_tati') {
						$data_sub_keg[$k]['nama_sub_kegiatan'] = $this->remove_kode($v['nama_sub_kegiatan']);
						unset($data_sub_keg[$k]['kode_sbl']);
					}
					if ($tipe != 'simple') {
						$data_sub_keg[$k]['sumber_dana'] = $wpdb->get_results($wpdb->prepare('
							SELECT 
								m.id_sumber_dana,
								s.kode_dana,
								s.nama_dana
							FROM data_mapping_sumberdana m
							INNER JOIN data_sumber_dana s on s.id_dana=m.id_sumber_dana
								and s.tahun_anggaran=m.tahun_anggaran
							INNER JOIN data_rka r on r.tahun_anggaran=m.tahun_anggaran
								and r.active=m.active
								and m.id_rinci_sub_bl=r.id_rinci_sub_bl
							WHERE m.tahun_anggaran=%d
								AND m.active=1
								and r.kode_sbl=%s
							GROUP BY m.id_sumber_dana
						', $v['tahun_anggaran'], $v['kode_sbl']), ARRAY_A);
					}
				}
				$ret['data'] = $data_sub_keg;
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_sub_keg()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'run'	=> $_POST['run'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get sub kegiatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['idsumber'])) {
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$id_skpd_fmis = $_POST['id_skpd_fmis'];
					$idsumber = $_POST['idsumber'];
					$get_id = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran);
					$id_skpd_sipd = $get_id['id_skpd_sipd'];
					$id_mapping_simda = $get_id['id_mapping_simda'];
					if (
						!empty($id_skpd_sipd)
						&& !empty($id_skpd_fmis)
					) {
						$program_mapping = $this->get_fmis_mapping(array(
							'name' => '_crb_custom_mapping_program_fmis'
						));
						$keg_mapping = $this->get_fmis_mapping(array(
							'name' => '_crb_custom_mapping_keg_fmis'
						));
						$subkeg_mapping = $this->get_fmis_mapping(array(
							'name' => '_crb_custom_mapping_subkeg_fmis'
						));
						$pindah_subkeg_mapping = $this->get_fmis_mapping(array(
							'name' => '_crb_custom_mapping_pindah_subkeg_fmis'
						));
						$data_sub_keg = array();
						$type_pagu = get_option('_crb_fmis_pagu');
						if ($idsumber == 1) {
							$data_sub_keg = $wpdb->get_results($wpdb->prepare(
								"
								SELECT 
									s.*,
									u.nama_skpd as nama_skpd_data_unit
								from data_sub_keg_bl s 
								inner join data_unit u on s.id_sub_skpd = u.id_skpd
									and u.tahun_anggaran = s.tahun_anggaran
									and u.active = s.active
								where s.tahun_anggaran=%d
									and u.id_skpd IN (" . implode(',', $id_skpd_sipd) . ")
									and s.active=1",
								$tahun_anggaran
							), ARRAY_A);
							foreach ($data_sub_keg as $k => $v) {
								$nama_sub_giat = explode(' ', $v['nama_sub_giat']);
								$kode_sub = $nama_sub_giat[0];
								unset($nama_sub_giat[0]);
								$nama_sub_giat = implode(' ', $nama_sub_giat);
								if (!empty($pindah_subkeg_mapping[$kode_sub])) {
									$v['nama_program'] = $pindah_subkeg_mapping[$kode_sub]['nama_program'];
									$v['nama_giat'] = $pindah_subkeg_mapping[$kode_sub]['nama_giat'];
									$v['nama_sub_giat'] = $pindah_subkeg_mapping[$kode_sub]['nama_sub_giat'];
									$data_sub_keg[$k] = $v;
								}
								if (
									!empty($type_pagu)
									&& $type_pagu == 2
								) {
									$data_sub_keg[$k]['pagu'] = $v['pagumurni'];
									$data_sub_keg[$k]['pagu_keg'] = $v['pagumurni'];
								}
								if (!empty($program_mapping[$this->removeNewline($v['nama_program'])])) {
									$data_sub_keg[$k]['nama_program'] = $program_mapping[$v['nama_program']];
								}
								if (!empty($keg_mapping[$this->removeNewline($v['nama_giat'])])) {
									$data_sub_keg[$k]['nama_giat'] = $keg_mapping[$this->removeNewline($v['nama_giat'])];
								}
								if (!empty($subkeg_mapping[$this->removeNewline($nama_sub_giat)])) {
									$data_sub_keg[$k]['nama_sub_giat'] = $kode_sub . ' ' . $subkeg_mapping[$this->removeNewline($nama_sub_giat)];
								}
								$data_sub_keg[$k]['id_mapping'] = get_option('_crb_unit_fmis_' . $tahun_anggaran . '_' . $v['id_sub_skpd']);
								$data_sub_keg[$k]['sub_keg_indikator'] = $wpdb->get_results("
									select 
										* 
									from data_sub_keg_indikator 
									where tahun_anggaran=" . $v['tahun_anggaran'] . "
										and kode_sbl='" . $v['kode_sbl'] . "'
										and active=1", ARRAY_A);
								$data_sub_keg[$k]['sub_keg_indikator_hasil'] = $wpdb->get_results("
									select 
										* 
									from data_keg_indikator_hasil 
									where tahun_anggaran=" . $v['tahun_anggaran'] . "
										and kode_sbl='" . $v['kode_sbl'] . "'
										and active=1", ARRAY_A);
								$data_sub_keg[$k]['tag_sub_keg'] = $wpdb->get_results("
									select 
										* 
									from data_tag_sub_keg 
									where tahun_anggaran=" . $v['tahun_anggaran'] . "
										and kode_sbl='" . $v['kode_sbl'] . "'
										and active=1", ARRAY_A);
								$data_sub_keg[$k]['capaian_prog_sub_keg'] = $wpdb->get_results("
									select 
										* 
									from data_capaian_prog_sub_keg 
									where tahun_anggaran=" . $v['tahun_anggaran'] . "
										and kode_sbl='" . $v['kode_sbl'] . "'
										and active=1", ARRAY_A);
								$data_sub_keg[$k]['output_giat'] = $wpdb->get_results("
									select 
										* 
									from data_output_giat_sub_keg 
									where tahun_anggaran=" . $v['tahun_anggaran'] . "
										and kode_sbl='" . $v['kode_sbl'] . "'
										and active=1", ARRAY_A);
								$data_sub_keg[$k]['lokasi_sub_keg'] = $wpdb->get_results("
									select 
										* 
									from data_lokasi_sub_keg 
									where tahun_anggaran=" . $v['tahun_anggaran'] . "
										and kode_sbl='" . $v['kode_sbl'] . "'
										and active=1", ARRAY_A);
							}
							// sementara dicomment dulu karena get data dari simda belum siap
							// }else if($idsumber == 2){
						} else if ($idsumber == 22) {
							$kd_unit_simda_asli = get_option('_crb_unit_' . $id_skpd_sipd);
							$kd_unit_simda = explode('.', $kd_unit_simda_asli);
							if (
								!empty($kd_unit_simda)
								&& !empty($kd_unit_simda[3])
							) {
								if (!empty($unit_simda[$kd_unit_simda_asli])) {
									unset($unit_simda[$kd_unit_simda_asli]);
								}
								$_kd_urusan = $kd_unit_simda[0];
								$_kd_bidang = $kd_unit_simda[1];
								$kd_unit = $kd_unit_simda[2];
								$kd_sub_unit = $kd_unit_simda[3];
								$kd_perubahan = '(SELECT max(kd_perubahan) from ta_rask_arsip where tahun=' . $tahun_anggaran . ')';
								if (
									!empty($type_pagu)
									&& $type_pagu == 2
								) {
									$kd_perubahan = 4;
								}
								$sql = $wpdb->prepare(
									"
									SELECT 
										r.kd_urusan, 
										r.kd_bidang, 
										r.kd_unit, 
										r.kd_sub,
										r.kd_prog,
										r.id_prog,
										r.kd_keg,
										SUM(r.total) as total
									FROM ta_rask_arsip r
									WHERE r.tahun = %d
										AND r.kd_perubahan = $kd_perubahan
										AND r.kd_urusan = %d
										AND r.kd_bidang = %d
										AND r.kd_unit = %d
										AND r.kd_rek_1 = 5
									GROUP BY r.kd_urusan, 
										r.kd_bidang, 
										r.kd_unit, 
										r.kd_sub,
										r.kd_prog,
										r.id_prog,
										r.kd_keg
									",
									$tahun_anggaran,
									$_kd_urusan,
									$_kd_bidang,
									$kd_unit
								);
								$sub_keg = $this->simda->CurlSimda(array('query' => $sql));
								foreach ($sub_keg as $k => $dpa) {
									$kd_urusan = substr($dpa->id_prog, 0, 1);
									$kd_bidang = (int) substr($dpa->id_prog, 1, 2);
									$sql = $wpdb->prepare("
										SELECT
											kd_urusan90,
											kd_bidang90,
											kd_program90,
											kd_kegiatan90,
											kd_sub_kegiatan
										FROM ref_kegiatan_mapping
										WHERE kd_urusan=%d
											AND kd_bidang=%d
											AND kd_prog=%d
											AND kd_keg=%d
									", $kd_urusan, $kd_bidang, $dpa->kd_prog, $dpa->kd_keg);
									$keg90 = $this->simda->CurlSimda(array('query' => $sql));

									// cek jika program penunjang urusan
									if ($keg90[0]->kd_program90 == 1) {
										$keg90[0]->kd_urusan90 = 'X';
										$keg90[0]->kd_bidang90 = 'XX';
									}
									$kd_program = $keg90[0]->kd_urusan90 . '.' . $this->simda->CekNull($keg90[0]->kd_bidang90) . '.' . $this->simda->CekNull($keg90[0]->kd_program90);
									$kd_keg = $kd_program . '.' . $this->simda->CekNull($keg90[0]->kd_kegiatan90);
									$kd_sub_keg = $kd_keg . '.' . $this->simda->CekNull($keg90[0]->kd_sub_kegiatan);

									$nama_program = $wpdb->get_var($wpdb->prepare("SELECT nama_program from data_prog_keg where kode_program=%s LIMIT 1", $kd_program));
									$nama_program = explode(' ', $nama_program);
									unset($nama_program[0]);
									$nama_program = implode(' ', $nama_program);

									$nama_keg = $wpdb->get_var($wpdb->prepare("SELECT nama_giat from data_prog_keg where kode_giat=%s LIMIT 1", $kd_keg));
									$nama_keg = explode(' ', $nama_keg);
									unset($nama_keg[0]);
									$nama_keg = implode(' ', $nama_keg);

									$nama_sub_keg = $wpdb->get_var($wpdb->prepare("SELECT nama_sub_giat from data_prog_keg where kode_sub_giat=%s LIMIT 1", $kd_sub_keg));
									$nama_sub_keg = explode(' ', $nama_sub_keg);
									unset($nama_sub_keg[0]);
									$nama_sub_keg = implode(' ', $nama_sub_keg);

									$newdata = array();
									$newdata['keg90_simda'] = $keg90[0];
									$newdata['keg_simda'] = $dpa;
									$newdata['kode_program'] = $kd_program;
									$newdata['kode_giat'] = $kd_keg;
									$newdata['kode_sub_giat'] = $kd_sub_keg;
									if (!empty($program_mapping[$this->removeNewline($nama_program)])) {
										$newdata['nama_program'] = $program_mapping[$this->removeNewline($nama_program)];
									} else {
										$newdata['nama_program'] = $nama_program;
									}
									if (!empty($keg_mapping[$this->removeNewline($nama_keg)])) {
										$newdata['nama_giat'] = $keg_mapping[$this->removeNewline($nama_keg)];
									} else {
										$newdata['nama_giat'] = $nama_keg;
									}
									if (!empty($subkeg_mapping[$this->removeNewline($nama_sub_keg)])) {
										$newdata['nama_sub_giat'] = $kd_sub_keg . ' ' . $subkeg_mapping[$this->removeNewline($nama_sub_keg)];
									} else {
										$newdata['nama_sub_giat'] = $kd_sub_keg . ' ' . $nama_sub_keg;
									}

									$newdata['sub_keg_indikator'] = array();
									$newdata['sub_keg_indikator_hasil'] = array();
									$newdata['tag_sub_keg'] = array();
									$newdata['capaian_prog_sub_keg'] = array();
									$newdata['output_giat'] = array();
									$newdata['lokasi_sub_keg'] = array();

									$newdata['pagu'] = $dpa->total;
									$newdata['pagu_keg'] = $dpa->total;
									$newdata['pagu_n_depan'] = 0;
									$newdata['pagu_n_lalu'] = 0;

									$kd_unit_simda = $dpa->kd_urusan . '.' . $dpa->kd_bidang . '.' . $dpa->kd_unit . '.' . $dpa->kd_sub;
									$newdata['kd_unit_simda'] = $kd_unit_simda;
									if ($id_mapping_simda[$kd_unit_simda]) {
										$newdata['id_mapping'] = $id_mapping_simda[$kd_unit_simda]['id_mapping_fmis'];
										$newdata['nama_sub_skpd'] = $id_mapping_simda[$kd_unit_simda]['nama_skpd'];
									} else {
										$newdata['nama_sub_skpd'] = 'Kode Unit SIMDA = ' . $kd_unit_simda . ', belum dimapping!';
									}
									$newdata['kode_sbl'] = $kd_keg;

									$data_sub_keg[] = $newdata;
								}
							} else {
								$ret['status'] = 'error';
								$ret['message'] = 'SKPD SIMDA PINK belum dimapping!';
							}
						}
						$ret['data'] = $data_sub_keg;
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'id_skpd_fmis=' . $_POST['id_skpd_fmis'] . ' tahun_anggaran=' . $tahun_anggaran . ' belum dimapping!';
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'idsumber harus diisi!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	function removeNewline($string)
	{
		return trim(preg_replace('/\s+/S', " ", $string));
	}

	public function get_fmis_mapping($options, $no_remove = false)
	{
		global $wpdb;
		if ($options['name'] == '_crb_custom_mapping_rekening_fmis') {
			$mapping = get_option($options['name']);
			$mapping = explode(',', $mapping);
			$ret = array();
			foreach ($mapping as $map) {
				$map = explode('-', $map);
				$ret[$map[0]] = $map[1];
			}
		} else if ($options['name'] == '_crb_custom_mapping_pindah_subkeg_fmis') {
			$mapping = get_option($options['name']);
			$mapping = explode('#', $mapping); // pindah sub keg dipisah dengan tanda pagar
			$ret = array();
			foreach ($mapping as $map) {
				$map = explode('-', $map);
				$ret[$map[0]] = '';
				$sub_giat_fmis = json_decode(stripslashes(html_entity_decode($map[1])), true);
				if (!empty($sub_giat_fmis)) {
					if (!empty($sub_giat_fmis['nama_sub_giat'])) {
						$ret[$map[0]] = $sub_giat_fmis;
					} else {
						$sub_giat = $wpdb->get_row($wpdb->prepare("
							SELECT 
								kode_sub_giat,
								nama_program,
								nama_giat,
								nama_sub_giat
							FROM data_prog_keg
							where kode_sub_giat=%s
							order by tahun_anggaran DESC
						", $sub_giat_fmis['kode_sub_giat']), ARRAY_A);
						$ret[$map[0]] = $sub_giat;
					}
				}
			}
		} else {
			$mapping = get_option($options['name']);
			$mapping = explode('],[', $mapping);
			$ret = array();
			foreach ($mapping as $map) {
				$map = explode(']-[', $map);
				if (true == $no_remove) {
					$ret[str_replace('[', '', $map[0])] = str_replace(']', '', $map[1]);
				} else {
					$ret[str_replace('[', '', $this->removeNewline($map[0]))] = str_replace(']', '', $this->removeNewline($map[1]));
				}
			}
		}
		return $ret;
	}

	public function get_sub_keg_rka()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil RKA get sub kegiatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$kode_sbl = $_POST['kode_sbl'];
				$rka = $wpdb->get_results($wpdb->prepare("
					SELECT 
						*
					FROM data_rka
					WHERE tahun_anggaran=%d
						AND active=1
						AND kode_sbl=%s
						AND kode_akun!=''
				", $tahun_anggaran, $kode_sbl), ARRAY_A);
				$id_sumber_dana_default = get_option('_crb_default_sumber_dana');
				$sumber_dana_default = $wpdb->get_results($wpdb->prepare('
					SELECT 
						id_dana as id_sumber_dana,
						kode_dana,
						nama_dana
					FROM data_sumber_dana
					WHERE tahun_anggaran=%d
						AND id_dana=%d
				', $tahun_anggaran, $id_sumber_dana_default), ARRAY_A);
				$rek_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_rekening_fmis'
				));
				$type_pagu = get_option('_crb_fmis_pagu');
				$type_aktivitas = get_option('_crb_fmis_aktivitas');
				if ($type_aktivitas != 2) {
					$sumber_dana_single = $wpdb->get_results($wpdb->prepare('
						SELECT 
							m.id_sumber_dana,
							s.kode_dana,
							s.nama_dana
						FROM data_mapping_sumberdana m
						INNER JOIN data_sumber_dana s on s.id_dana=m.id_sumber_dana
							and s.tahun_anggaran=m.tahun_anggaran
						INNER JOIN data_rka r on r.tahun_anggaran=m.tahun_anggaran
							and r.active=m.active
							and m.id_rinci_sub_bl=r.id_rinci_sub_bl
						WHERE m.tahun_anggaran=%d
							AND m.active=1
							and r.kode_sbl=%s
						GROUP BY m.id_sumber_dana
						LIMIT 1
					', $tahun_anggaran, $kode_sbl), ARRAY_A);
				}
				foreach ($rka as $k => $v) {
					if (
						!empty($type_pagu)
						&& $type_pagu == 2
					) {
						$rka[$k]['pajak'] = $v['pajak_murni'];
						$rka[$k]['rincian'] = $v['rincian_murni'];
						$rka[$k]['volume'] = $v['volume_murni'];
						$rka[$k]['koefisien'] = $v['koefisien_murni'];
						$rka[$k]['harga_satuan'] = $v['harga_satuan_murni'];
					}
					$_kode_akun = explode('.', $v['kode_akun']);
					$kode_akun = array();
					foreach ($_kode_akun as $vv) {
						$kode_akun[] = (int)$vv;
					}
					$kode_akun = implode('.', $kode_akun);
					if (!empty($rek_mapping[$kode_akun])) {
						$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
						$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
						$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
						$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
						$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
						$rka[$k]['kode_akun'] = implode('.', $_kode_akun);
					}
					if ($type_aktivitas == 2) {
						$sumber_dana = $wpdb->get_results($wpdb->prepare('
							SELECT 
								m.id_sumber_dana,
								s.kode_dana,
								s.nama_dana
							FROM data_mapping_sumberdana m
							INNER JOIN data_sumber_dana s on s.id_dana=m.id_sumber_dana
								and s.tahun_anggaran=m.tahun_anggaran
							WHERE m.tahun_anggaran=%d
								AND m.active=1
								AND m.id_rinci_sub_bl=%d
						', $tahun_anggaran, $v['id_rinci_sub_bl']), ARRAY_A);
						if (
							!empty($sumber_dana)
							&& !empty($sumber_dana[0])
							&& !empty($sumber_dana[0]['nama_dana'])
						) {
							$rka[$k]['sumber_dana'] = $sumber_dana;
						} else {
							$rka[$k]['sumber_dana'] = $sumber_dana_default;
						}
					} else {
						if (
							!empty($sumber_dana_single)
							&& !empty($sumber_dana_single[0])
							&& !empty($sumber_dana_single[0]['nama_dana'])
						) {
							$rka[$k]['sumber_dana'] = $sumber_dana_single;
						} else {
							$rka[$k]['sumber_dana'] = $sumber_dana_default;
						}
					}
					$rka[$k]['type_aktivitas'] = $type_aktivitas;
				}
				$ret['data'] = $rka;
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_arsip_label_komponen()
	{
		global $wpdb;
		$ret = array(
			'action'    => $_POST['action'],
			'status'    => 'success',
			'message'   => 'Berhasil get arsip label komponen!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (empty($_POST['tahun_anggaran'])) {
					$ret['status'] = 'error';
					$ret['message'] = 'tahun_anggaran tidak boleh kosong!';
				} else if (empty($_POST['id_label'])) {
					$ret['status'] = 'error';
					$ret['message'] = 'id_label tidak boleh kosong!';
				} else {
					$tahun_anggaran = $_POST['tahun_anggaran'];

					// get rincian terhapus di arsip
					$rka = $wpdb->get_results(
						$wpdb->prepare("
							SELECT 
								r.*,
								m.*
							FROM data_mapping_label m
							INNER JOIN data_rka r on r.id_rinci_sub_bl=m.id_rinci_sub_bl
								AND m.tahun_anggaran=r.tahun_anggaran
							  	AND r.kode_akun!=''
							WHERE m.tahun_anggaran=%d
							  	AND m.active=2
								AND m.id_label_komponen=%d
						", $_POST['id_label'], $tahun_anggaran),
						ARRAY_A
					);
					foreach ($rka as $k => $v) {
						$realisasi_rincian = $wpdb->get_var(
							$wpdb->prepare("
								SELECT 
									realisasi
								FROM data_realisasi_rincian
								WHERE tahun_anggaran=%d
								  	AND id_rinci_sub_bl=%d
								  	AND active=1
	                		", $tahun_anggaran, $v['id_rinci_sub_bl'])
						);
						$rka[$k]['realisasi_rincian'] = $realisasi_rincian == null ? 0 : $realisasi_rincian;

						if (empty($sub_kegs[$v['kode_sbl']])) {
							$sub_keg = $wpdb->get_row(
								$wpdb->prepare("
									SELECT 
										s.kode_sub_giat,
										s.nama_sub_giat,
										u.id_skpd,
										u.nama_skpd,
										u.kode_skpd 
									FROM data_sub_keg_bl s
									INNER JOIN data_unit u ON u.id_skpd=s.id_sub_skpd
										AND u.tahun_anggaran=s.tahun_anggaran
										AND u.active=s.active
									WHERE s.kode_sbl = %s
									  AND s.tahun_anggaran = %d
									  AND s.active = 1
									", $v['kode_sbl'], $tahun_anggaran),
								ARRAY_A
							);
							$sub_kegs[$v['kode_sbl']] = $sub_keg;
						} else {
							$sub_keg = $sub_kegs[$v['kode_sbl']];
						}

						// Tambahkan realisasi rincian ke dalam array
						$rka[$k]['id_skpd'] = $sub_keg['id_skpd'];
						$rka[$k]['nama_skpd'] = $sub_keg['kode_skpd'] . ' ' . $sub_keg['nama_skpd'];
						$rka[$k]['nama_sub_giat'] = $sub_keg['nama_sub_giat'];
					}
					$ret['data'] = $rka;
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_sub_keg_rka_sipd()
	{
		global $wpdb;
		$ret = array(
			'action'    => $_POST['action'],
			'status'    => 'success',
			'message'   => 'Berhasil get RKA sub kegiatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (empty($_POST['tahun_anggaran'])) {
					$ret['status'] = 'error';
					$ret['message'] = 'tahun_anggaran tidak boleh kosong!';
				} else if (empty($_POST['kode_sbl'])) {
					$ret['status'] = 'error';
					$ret['message'] = 'kode_sbl tidak boleh kosong!';
				} else {
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$kode_sbl = $_POST['kode_sbl'];
					$sub_kegs = array();

					$rka = $wpdb->get_results(
						$wpdb->prepare("
							SELECT *
							FROM data_rka
							WHERE tahun_anggaran=%d
							  AND active=1
							  AND kode_sbl=%s
							  AND kode_akun!=''
						", $tahun_anggaran, $kode_sbl),
						ARRAY_A
					);

					// Ambil sumber dana default
					$id_sumber_dana_default = get_option('_crb_default_sumber_dana');
					$sumber_dana_default = $wpdb->get_results(
						$wpdb->prepare("
							SELECT 
								id_dana as id_sumber_dana,
								kode_dana,
								nama_dana
							FROM data_sumber_dana
							WHERE tahun_anggaran=%d
							  AND id_dana=%d
	            		", $tahun_anggaran, $id_sumber_dana_default),
						ARRAY_A
					);

					foreach ($rka as $k => $v) {
						// Ambil sumber dana untuk masing-masing rincian
						$sumber_dana = $wpdb->get_row(
							$wpdb->prepare("
								SELECT 
									m.id_sumber_dana,
									s.kode_dana,
									s.nama_dana
								FROM data_mapping_sumberdana m
								INNER JOIN data_sumber_dana s 
										ON s.id_dana=m.id_sumber_dana
									   AND s.tahun_anggaran=m.tahun_anggaran
								WHERE m.tahun_anggaran=%d
								  AND m.active=1
								  AND m.id_rinci_sub_bl=%d
							", $tahun_anggaran, $v['id_rinci_sub_bl']),
							ARRAY_A
						);

						if (
							!empty($sumber_dana)
							&& !empty($sumber_dana)
							&& !empty($sumber_dana['nama_dana'])
						) {
							$rka[$k]['sumber_dana'] = $sumber_dana;
						} else {
							$rka[$k]['sumber_dana'] = $sumber_dana_default;
						}

						// mapping_label checkbox
						if (!empty($_POST['id_label'])) {
							$realisasi_rincian = $wpdb->get_var(
								$wpdb->prepare("
									SELECT realisasi
									FROM data_realisasi_rincian
									WHERE tahun_anggaran=%d
									  AND id_rinci_sub_bl=%d
									  AND active=1
	                    		", $tahun_anggaran, $v['id_rinci_sub_bl'])
							);
							$rka[$k]['realisasi_rincian'] = $realisasi_rincian == null ? 0 : $realisasi_rincian;

							$rka[$k]['is_checked'] = false;
							$rka[$k]['checked_pisah'] = false;
							$rka[$k]['labels'] = array();

							// Ambil data labels
							$labels = $wpdb->get_results(
								$wpdb->prepare('
									SELECT *
									FROM data_mapping_label
									WHERE id_rinci_sub_bl = %d
									  AND tahun_anggaran = %d
									  AND active = 1
									', $v['id_rinci_sub_bl'], $tahun_anggaran),
								ARRAY_A
							);

							if (!empty($labels)) {
								foreach ($labels as $val) {
									if ($val['id_label_komponen'] == $_POST['id_label']) {
										$rka[$k]['is_checked'] = true;
									}
									// Ambil nama label dari tabel data_label_komponen
									$label = $wpdb->get_row(
										$wpdb->prepare('
											SELECT nama
											FROM data_label_komponen
											WHERE id = %d
											  AND tahun_anggaran = %d
											  AND active = 1
											', $val['id_label_komponen'], $tahun_anggaran),
										ARRAY_A
									);

									if ($label) {
										$detail_label = array(
											'id_label' => $val['id_label_komponen'],
											'nama' => $label['nama']
										);
										if ($val['pisah'] == 0) {
											$detail_label['volume'] = $v['volume'];
											$detail_label['anggaran'] = $v['total_harga'];
											$detail_label['realisasi'] = $rka[$k]['realisasi_rincian'];
											$detail_label['pisah'] = false;
										} else {
											if ($rka[$k]['id_rinci_sub_bl'] === $val['id_rinci_sub_bl']) {
												$rka[$k]['checked_pisah'] = true;
											}
											$harga_satuan = ($v['total_harga'] > 0) ? ($v['total_harga'] / $v['volume']) : 0;
											$detail_label['volume'] = $val['volume_pisah'];
											$detail_label['anggaran'] = $harga_satuan * $val['volume_pisah'];
											$detail_label['realisasi'] = $val['realisasi_pisah'];
											$detail_label['pisah'] = true;
										}
										$rka[$k]['labels'][] = $detail_label;
									}
								}
							}
						}
					}
					$ret['data'] = $rka;
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}


	public function get_pembiayaan_sipd()
	{
		global $wpdb;

		$ret = array(
			'status'  => 'success',
			'message' => 'Berhasil Get Pembiayaan SIPD!',
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] === get_option('_crb_api_key_extension')) {
				$id_skpd    	= $_POST['id_skpd'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				if (!empty($id_skpd) && !empty($tahun_anggaran)) {
					$pembiayaan_results = $wpdb->get_row(
						$wpdb->prepare("
						SELECT 
							* 
						FROM data_pembiayaan 
						WHERE tahun_anggaran = %d 
						  AND id_skpd = %s 
						  AND active = 1
						  ", $tahun_anggaran, $id_skpd),
						ARRAY_A
					);

					if (!empty($pembiayaan_results)) {
						$ret['data'] = $pembiayaan_results;
					} else {
						$ret['status']  = 'error';
						$ret['message'] = 'Tidak ada data ditemukan!';
					}
				} else {
					$ret['status']  = 'error';
					$ret['message'] = 'Parameter tidak lengkap!';
				}
			} else {
				$ret['status']  = 'error';
				$ret['message'] = 'API Key tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	public function get_pendapatan_sipd()
	{
		global $wpdb;

		$ret = array(
			'status'  => 'success',
			'message' => 'Berhasil Get Pendapatan SIPD!',
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] === get_option('_crb_api_key_extension')) {
				$id_skpd    	= $_POST['id_skpd'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				if (!empty($id_skpd) && !empty($tahun_anggaran)) {
					$pendapatan_results = $wpdb->get_row(
						$wpdb->prepare("
						SELECT 
							* 
						FROM data_pendapatan 
						WHERE tahun_anggaran = %d 
							AND id_skpd = %s 
							AND active = 1
						",  $tahun_anggaran, $id_skpd),
						ARRAY_A
					);

					if (!empty($pendapatan_results)) {
						$ret['data'] = $pendapatan_results;
					} else {
						$ret['status']  = 'error';
						$ret['message'] = 'Tidak ada data ditemukan!';
					}
				} else {
					$ret['status']  = 'error';
					$ret['message'] = 'Parameter tidak lengkap!';
				}
			} else {
				$ret['status']  = 'error';
				$ret['message'] = 'API Key tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	public function get_spd_sipd()
	{
		global $wpdb;
		$ret = array(
			'status'   => 'success',
			'message'  => 'Berhasil Get SPD SIPD!',
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_skpd    	= $_POST['id_skpd'];
				$tahun_anggaran = $_POST['tahun_anggaran'];

				if (!empty($id_skpd) && !empty($tahun_anggaran)) {
					$spd_results = $wpdb->get_results(
						$wpdb->prepare("
							SELECT
								s.*,
								d.idDetailSpd,
								d.idSpd,
								d.id_program,
								d.id_giat,
								d.id_sub_giat,
								d.id_akun,
								d.nilai,
								a.kode_akun,
								a.nama_akun
							FROM data_spd_sipd s
							INNER JOIN data_spd_sipd_detail d ON s.idSpd = d.idSpd
								AND s.tahun_anggaran = d.tahun_anggaran
								AND s.active = d.active
							LEFT JOIN data_akun a ON a.id_akun=d.id_akun
								AND a.tahun_anggaran = d.tahun_anggaran
								AND a.active = d.active
							WHERE s.tahun_anggaran=%d
							  AND s.id_skpd = %s
							  AND s.active=1
						", $tahun_anggaran, $id_skpd),
						ARRAY_A
					);
					if (!empty($spd_results)) {
						$master_prog = $wpdb->get_results($wpdb->prepare("
							SELECT 
								id_program,
								kode_program,
								nama_program,
								id_giat,
								kode_giat,
								nama_giat,
								id_sub_giat,
								kode_sub_giat,
								nama_sub_giat
							FROM data_prog_keg
							WHERE tahun_anggaran=%d
								AND active=1
						", $tahun_anggaran), ARRAY_A);
						$program = array();
						$giat = array();
						$sub_giat = array();
						foreach ($master_prog as $k => $v) {
							$program[$v['id_program']] = $v;
							$giat[$v['id_giat']] = $v;
							$sub_giat[$v['id_sub_giat']] = $v;
						}
						foreach ($spd_results as $k => $v) {
							if (!empty($sub_giat[$v['id_sub_giat']])) {
								$spd_results[$k]['kode_sub_giat'] = $sub_giat[$v['id_sub_giat']]['kode_sub_giat'];
								$spd_results[$k]['nama_sub_giat'] = $sub_giat[$v['id_sub_giat']]['nama_sub_giat'];
								$spd_results[$k]['kode_giat'] = $sub_giat[$v['id_sub_giat']]['kode_giat'];
								$spd_results[$k]['nama_giat'] = $sub_giat[$v['id_sub_giat']]['nama_giat'];
								$spd_results[$k]['kode_program'] = $sub_giat[$v['id_sub_giat']]['kode_program'];
								$spd_results[$k]['nama_program'] = $sub_giat[$v['id_sub_giat']]['nama_program'];
							}
						}
						$ret['data'] = $spd_results;
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'Tidak ada data ditemukan!';
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Parameter tidak lengkap!';
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

	public function get_spp_sipd()
	{
		global $wpdb;
		$ret = array(
			'status'   => 'success',
			'message'  => 'Berhasil Get SPP SIPD!',
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_skpd    	= $_POST['id_skpd'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				if (!empty($id_skpd) && !empty($tahun_anggaran)) {
					$spp_results = $wpdb->get_results(
						$wpdb->prepare("
							SELECT 
								s.*
							FROM data_spp_sipd s
							WHERE s.tahun_anggaran = %d 
							  AND s.idSkpd = %d
							  AND s.active = 1
						", $tahun_anggaran, $id_skpd),
						ARRAY_A
					);
					foreach ($spp_results as $k => $v) {
						$detail = $wpdb->get_results(
							$wpdb->prepare("
								SELECT 
									*
								FROM data_spp_sipd_ri_detail
								WHERE tahun_anggaran = %d 
								  AND id_skpd = %d
								  AND id_spp = %d
								  AND active = 1
							", $tahun_anggaran, $id_skpd, $v['idSpp']),
							ARRAY_A
						);
						$spp_results[$k]['detail'] = $detail;
					}

					if (!empty($spp_results)) {
						$ret['data'] = $spp_results;
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'Tidak ada data ditemukan!';
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Parameter tidak lengkap!';
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

	public function get_sp2d_sipd()
	{
		global $wpdb;
		$ret = array(
			'status'   => 'success',
			'message'  => 'Berhasil Get SP2D SIPD!',
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_skpd    	= $_POST['id_skpd'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				if (!empty($id_skpd) && !empty($tahun_anggaran)) {
					$sp2d_results = $wpdb->get_results(
						$wpdb->prepare("
							SELECT 
								s.*
							FROM data_sp2d_sipd_ri s
							WHERE s.tahun_anggaran = %d 
							  AND s.id_skpd = %d
							  AND s.active = 1
						", $tahun_anggaran, $id_skpd),
						ARRAY_A
					);

					foreach ($sp2d_results as $k => $v) {
						$sp2d_results[$k]['detail'] = $wpdb->get_results(
							$wpdb->prepare("
							SELECT 
								*
							FROM data_sp2d_sipd_detail
							WHERE tahun_anggaran = %d 
							  	AND id_skpd = %d
							  	AND id_sp_2_d = %d
							  	AND active = 1
							", $tahun_anggaran, $id_skpd, $v['id_sp_2_d']),
							ARRAY_A
						);
						$sp2d_results[$k]['potongan'] = $wpdb->get_results(
							$wpdb->prepare("
							SELECT 
								*
							FROM data_sp2d_sipd_detail_potongan
							WHERE tahun_anggaran = %d 
							  	AND id_skpd = %d
							  	AND id_sp_2_d = %d
							  	AND active = 1
							", $tahun_anggaran, $id_skpd, $v['id_sp_2_d']),
							ARRAY_A
						);
					}

					if (!empty($sp2d_results)) {
						$ret['data'] = $sp2d_results;
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'Tidak ada data ditemukan!';
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Parameter tidak lengkap!';
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

	public function get_rak_sipd()
	{
		global $wpdb;
		$ret = array(
			'status'   => 'success',
			'message'  => 'Berhasil Get RAK SIPD!',
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_skpd = $_POST['id_skpd'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$type = $_POST['type'];
				if (!empty($id_skpd) && !empty($tahun_anggaran) && !empty($type)) {
					$rak_results = $wpdb->get_results(
						$wpdb->prepare("
						SELECT 
							*
						FROM data_anggaran_kas
						WHERE tahun_anggaran = %d 
						  AND id_sub_skpd = %s
						  AND type = %s
						  AND active = 1
					", $tahun_anggaran, $id_skpd, $type),
						ARRAY_A
					);

					if (!empty($rak_results)) {
						$ret['data'] = $rak_results;
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'Tidak ada data ditemukan!';
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Parameter tidak lengkap!';
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

	public function get_pegawai_sipd()
	{
		global $wpdb;
		$ret = array(
			'status'   => 'success',
			'message'  => 'Berhasil Get Pegawai SIPD!',
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_skpd = $_POST['id_skpd'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				if (!empty($id_skpd) && !empty($tahun_anggaran)) {
					$pegawai_results = $wpdb->get_results(
						$wpdb->prepare("
						SELECT 
							id,
							idSkpd,
							namaSkpd,
							kodeSkpd,
							idDaerah,
							userName,
							nip,
							nik,
							fullName,
							lahir_user,
							nomorHp,
							IsRank,
							npwp,
							idJabatan,
							namaJabatan,
							idRole,
							_order,
							kpa,
							bank,
							_group,
							kodeBank,
							nama_rekening,
							nomorRekening,
							pangkatGolongan,
							tahunPegawai,
							kodeDaerah,
							is_from_sipd,
							is_from_generate,
							is_from_external,
							idSubUnit,
							idUser,
							idPegawai,
							alamat,
							tahun_anggaran,
							updated_at
							FROM data_user_penatausahaan
						WHERE tahun_anggaran = %d 
						  AND idSkpd = %s
						  AND active = 1
						", $tahun_anggaran, $id_skpd),
						ARRAY_A
					);

					if (!empty($pegawai_results)) {
						$ret['data'] = $pegawai_results;
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'Tidak ada data ditemukan!';
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Parameter tidak lengkap!';
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

	public function get_spm_sipd()
	{
		global $wpdb;
		$ret = array(
			'status'   => 'success',
			'message'  => 'Berhasil Get SPM SIPD!',
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_skpd = $_POST['id_skpd'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				if (!empty($id_skpd) && !empty($tahun_anggaran)) {
					$spm_results = $wpdb->get_results(
						$wpdb->prepare("
						SELECT 
							*
						FROM data_spm_sipd
						WHERE tahun_anggaran = %d 
						  	AND id_skpd = %s
						  	AND active = 1
						", $tahun_anggaran, $id_skpd),
						ARRAY_A
					);
					foreach ($spm_results as $k => $v) {
						$spm_results[$k]['detail'] = $wpdb->get_results(
							$wpdb->prepare("
							SELECT 
								*
							FROM data_spm_sipd_detail
							WHERE tahun_anggaran = %d 
							  	AND id_skpd = %d
							  	AND id_spm = %d
							  	AND active = 1
							", $tahun_anggaran, $id_skpd, $v['idSpm']),
							ARRAY_A
						);
						$spm_results[$k]['potongan'] = $wpdb->get_results(
							$wpdb->prepare("
							SELECT 
								*
							FROM data_spm_sipd_detail_potongan
							WHERE tahun_anggaran = %d 
							  	AND id_skpd = %d
							  	AND id_spm = %d
							  	AND active = 1
							", $tahun_anggaran, $id_skpd, $v['idSpm']),
							ARRAY_A
						);
					}

					if (!empty($spm_results)) {
						$ret['data'] = $spm_results;
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'Tidak ada data ditemukan!';
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Parameter tidak lengkap!';
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

	public function get_stbp_sipd()
	{
		global $wpdb;
		$ret = array(
			'status'   => 'success',
			'message'  => 'Berhasil Get STBP SIPD!',
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_skpd = $_POST['id_skpd'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				if (!empty($id_skpd) && !empty($tahun_anggaran)) {
					$stbp_results = $wpdb->get_results(
						$wpdb->prepare("
						SELECT 
							*
						FROM data_stbp_sipd
						WHERE tahun_anggaran = %d 
						  	AND id_skpd = %s
						  	AND active = 1
						", $tahun_anggaran, $id_skpd),
						ARRAY_A
					);
					foreach ($stbp_results as $k => $v) {
						$stbp_results[$k]['detail'] = $wpdb->get_results(
							$wpdb->prepare("
							SELECT 
								*
							FROM data_stbp_sipd_detail
							WHERE tahun_anggaran = %d 
							  	AND id_skpd = %d
							  	AND id_stbp = %d
							  	AND active = 1
							", $tahun_anggaran, $id_skpd, $v['id_stbp']),
							ARRAY_A
						);
					}

					if (!empty($stbp_results)) {
						$ret['data'] = $stbp_results;
					} else {
						$ret['status'] = 'error';
						$ret['message'] = 'Tidak ada data ditemukan!';
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Parameter tidak lengkap!';
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


	function mapping_skpd_fmis()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil mapping SKPD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$data = json_decode(stripslashes(html_entity_decode($_POST['data'])));
				foreach ($data as $k => $skpd) {
					$skpd = (array) $skpd;
					foreach ($skpd['sub_unit'] as $kk => $sub_unit) {
						$sub_unit = (array) $sub_unit;
						$id_skpd_sipd = $wpdb->get_var($wpdb->prepare('
							SELECT 
								id_skpd
							FROM data_unit
							WHERE nama_skpd=%s
								AND tahun_anggaran=%d
								AND active=1
						', $sub_unit['name'], $tahun_anggaran));
						if (!empty($id_skpd_sipd)) {
							update_option('_crb_unit_fmis_' . $tahun_anggaran . '_' . $id_skpd_sipd, $skpd['id'] . '.' . $sub_unit['id']);
							$ret['data_sukses_mapping'][] = $sub_unit;
						} else {
							$ret['data_gagal_mapping'][] = $sub_unit;
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

	function mapping_rek_fmis()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil cek mapping rekening!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['rek'])) {
					$rek_sipd = $wpdb->get_results($wpdb->prepare("
						SELECT 
							*
						FROM
							data_akun
						WHERE tahun_anggaran=%d
							and (
								kode_akun like '4%'
								or kode_akun like '5%'
								or kode_akun like '6%'
							)
							and set_input=1
					", $_POST['tahun_anggaran']), ARRAY_A);
					$rek_fmis = json_decode(stripslashes(html_entity_decode($_POST['rek'])));
					$new_rek_fmis = array();
					foreach ($rek_fmis as $rek) {
						$rek = (array) $rek;
						$new_rek_fmis[$rek['kdrek']] = $rek;
					}
					$cek_sipd_belum_ada_di_fmis = array();
					foreach ($rek_sipd as $rek) {
						$_kode_akun = explode('.', $rek['kode_akun']);
						$kode_akun = array();
						foreach ($_kode_akun as $v) {
							$kode_akun[] = (int)$v;
						}
						$kode_akun = implode('.', $kode_akun);
						if (empty($new_rek_fmis[$kode_akun])) {
							$cek_sipd_belum_ada_di_fmis[$kode_akun] = $rek;
						}
					}
					$current_mapping = get_option('_crb_custom_mapping_rekening_fmis');
					$current_mapping = explode(',', $current_mapping);
					$mapping_rek = array();
					foreach ($current_mapping as $v) {
						$rek = explode('-', $v);
						$mapping_rek[$rek[0]] = '';
						if (!empty($rek[1])) {
							$mapping_rek[$rek[0]] = $rek[1];
						}
					}

					$mapping = array();
					foreach ($cek_sipd_belum_ada_di_fmis as $k => $v) {
						if (!empty($mapping_rek[$k])) {
							$mapping[] = $k . '-' . $mapping_rek[$k];
						} else {
							$mapping[] = $k . '-' . $k;
						}
					}
					update_option('_crb_custom_mapping_rekening_fmis', implode(',', $mapping));
					$ret['data_rek'] = $cek_sipd_belum_ada_di_fmis;
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'format salah!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_data_pendapatan()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get data Pendapatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_skpd_fmis = $_POST['id_skpd_fmis'];
				$get_id = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran);
				$id_skpd_sipd = $get_id['id_skpd_sipd'];
				if (
					!empty($id_skpd_sipd)
					&& !empty($id_skpd_fmis)
				) {
					$pendapatan = $wpdb->get_results($wpdb->prepare('
						SELECT 
							p.*,
							u.nama_skpd
						FROM data_pendapatan p
							LEFT JOIN data_unit u ON p.id_skpd=u.id_skpd
								AND u.active=p.active
								AND u.tahun_anggaran=p.tahun_anggaran
						WHERE p.tahun_anggaran=%d
							and p.id_skpd IN (' . implode(',', $id_skpd_sipd) . ')
							and p.active=1
					', $_POST['tahun_anggaran']), ARRAY_A);
					$ret['sql'] = $wpdb->last_query;
					$type_pagu = get_option('_crb_fmis_pagu');
					$rek_mapping = $this->get_fmis_mapping(array(
						'name' => '_crb_custom_mapping_rekening_fmis'
					));
					foreach ($pendapatan as $k => $v) {
						if (
							!empty($type_pagu)
							&& $type_pagu == 2
						) {
							$pendapatan[$k]['total'] = $v['nilaimurni'];
						}
						$_kode_akun = explode('.', $v['kode_akun']);
						$kode_akun = array();
						foreach ($_kode_akun as $vv) {
							$kode_akun[] = (int)$vv;
						}
						$kode_akun = implode('.', $kode_akun);
						if (!empty($rek_mapping[$kode_akun])) {
							$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
							$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
							$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
							$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
							$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
							$pendapatan[$k]['kode_akun'] = implode('.', $_kode_akun);
						}
						$pendapatan[$k]['id_mapping'] = get_option('_crb_unit_fmis_' . $tahun_anggaran . '_' . $v['id_skpd']);
					}
					$ret['data'] = $pendapatan;
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

	public function get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, $cek_sub_unit = false)
	{
		global $wpdb;
		$id_skpd_sipd = array();
		$kode_skpd_sipd = array();
		$id_mapping_simda = array();
		$id_skpd_sipds = $wpdb->get_results($wpdb->prepare('
			SELECT 
				id_skpd,
				is_skpd,
				kode_skpd,
				nama_skpd
			FROM data_unit
			WHERE tahun_anggaran=%d
				AND active=1
		', $tahun_anggaran), ARRAY_A);
		foreach ($id_skpd_sipds as $k => $v) {
			$kd_unit_simda_asli = get_option('_crb_unit_' . $v['id_skpd']);
			$id_mapping = get_option('_crb_unit_fmis_' . $tahun_anggaran . '_' . $v['id_skpd']);
			$id_mapping_simda[$kd_unit_simda_asli] = array(
				'id_skpd' => $v['id_skpd'],
				'id_mapping_fmis' => $id_mapping,
				'kode_skpd' => $v['kode_skpd'],
				'nama_skpd' => $v['nama_skpd']
			);
			if (!empty($id_skpd_fmis)) {
				$id_fmis = explode('.', $id_skpd_fmis);
				if (count($id_fmis) >= 2) {
					if ($id_mapping == $id_skpd_fmis) {
						$id_skpd_sipd[] = $v['id_skpd'];
						$kode_skpd_sipd[] = $v['kode_skpd'];
					}
				} else {
					if (empty($cek_sub_unit)) {
						$id_mappings = explode('.', $id_mapping);
						if ($id_mappings[0] == $id_fmis[0]) {
							$id_skpd_sipd[] = $v['id_skpd'];
							$kode_skpd_sipd[] = $v['kode_skpd'];
						}
					} else {
						$id_mappings = explode('.', $id_mapping);
						if ($id_mappings[1] == $id_fmis[0]) {
							$id_skpd_sipd[] = $v['id_skpd'];
							$kode_skpd_sipd[] = $v['kode_skpd'];
						}
					}
				}
			} else if (false === $id_skpd_fmis) {
				$id_skpd_sipd[] = $v['id_skpd'];
				$kode_skpd_sipd[] = $v['kode_skpd'];
			}
		}
		return array(
			'id_skpd_sipd' => $id_skpd_sipd,
			'id_mapping_simda' => $id_mapping_simda,
			'kode_skpd_sipd' => $kode_skpd_sipd
		);
	}

	public function get_data_pembiayaan()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get data Pembiayaan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_skpd_fmis = $_POST['id_skpd_fmis'];
				$get_id = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran);
				$id_skpd_sipd = $get_id['id_skpd_sipd'];
				if (
					!empty($id_skpd_sipd)
					&& !empty($id_skpd_fmis)
				) {
					$data_db = $wpdb->get_results($wpdb->prepare('
						SELECT 
							p.*,
							u.nama_skpd
						FROM data_pembiayaan p
							LEFT JOIN data_unit u ON p.id_skpd=u.id_skpd
								AND u.active=p.active
								AND u.tahun_anggaran=p.tahun_anggaran
						WHERE p.tahun_anggaran=%d
							and p.id_skpd IN (' . implode(',', $id_skpd_sipd) . ')
							and p.active=1
					', $_POST['tahun_anggaran']), ARRAY_A);
					$type_pagu = get_option('_crb_fmis_pagu');
					$rek_mapping = $this->get_fmis_mapping(array(
						'name' => '_crb_custom_mapping_rekening_fmis'
					));
					foreach ($data_db as $k => $v) {
						if (
							!empty($type_pagu)
							&& $type_pagu == 2
						) {
							$data_db[$k]['total'] = $v['nilaimurni'];
						}
						$_kode_akun = explode('.', $v['kode_akun']);
						$kode_akun = array();
						foreach ($_kode_akun as $vv) {
							$kode_akun[] = (int)$vv;
						}
						$kode_akun = implode('.', $kode_akun);
						if (!empty($rek_mapping[$kode_akun])) {
							$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
							$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
							$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
							$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
							$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
							$data_db[$k]['kode_akun'] = implode('.', $_kode_akun);
						}
						$data_db[$k]['id_mapping'] = get_option('_crb_unit_fmis_' . $tahun_anggaran . '_' . $v['id_skpd']);
					}
					$ret['sql'] = $wpdb->last_query;
					$ret['data'] = $data_db;
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

	function mapping_sub_kegiatan_fmis()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil cek mapping sub kegiatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['sub_kegiatan'])) {
					$sub_keg_fmis = json_decode(stripslashes(html_entity_decode($_POST['sub_kegiatan'])));
					$new_prog_fmis = array();
					$new_keg_fmis = array();
					$new_sub_keg_fmis = array();
					foreach ($sub_keg_fmis as $sub) {
						$sub = (array) $sub;
						$new_sub_keg_fmis[strtolower(trim($sub['sub_kegiatan']))] = $sub;
						$new_keg_fmis[strtolower(trim($sub['kegiatan']))] = $sub;
						$new_prog_fmis[strtolower(trim($sub['program']))] = $sub;
					}

					$where = '';
					$cek_pemda = $this->cek_kab_kot();
					// tahun 2024 sudah menggunakan sipd-ri
					if (
						$cek_pemda['status'] == 1
						&& $tahun_anggaran >= 2024
					) {
						$where .= ' AND set_kab_kota=1';
					} else if ($cek_pemda['status'] == 2) {
						$where .= ' AND set_prov=1';
					}

					$sub_keg_sipd = $wpdb->get_results($wpdb->prepare("
						SELECT 
							*
						FROM
							data_prog_keg
						WHERE tahun_anggaran=%d
							$where
					", $_POST['tahun_anggaran']), ARRAY_A);
					$cek_sipd_belum_ada_di_fmis = array();
					$cek_sipd_keg_belum_ada_di_fmis = array();
					$cek_sipd_prog_belum_ada_di_fmis = array();
					foreach ($sub_keg_sipd as $sub) {
						// cek sub kegiatan
						$_nama_sub = explode(' ', $sub['nama_sub_giat']);
						unset($_nama_sub[0]);
						$nama_sub = trim(implode(' ', $_nama_sub));
						if (empty($new_sub_keg_fmis[strtolower($nama_sub)])) {
							$cek_sipd_belum_ada_di_fmis[$nama_sub] = $sub;
						}

						// cek kegiatan
						$_nama_giat = explode(' ', $sub['nama_giat']);
						unset($_nama_giat[0]);
						$nama_giat = trim(implode(' ', $_nama_giat));
						if (empty($new_keg_fmis[strtolower($nama_giat)])) {
							$cek_sipd_keg_belum_ada_di_fmis[$nama_giat] = $sub;
						}

						// cek program
						$_nama_program = explode(' ', $sub['nama_program']);
						unset($_nama_program[0]);
						$nama_program = trim(implode(' ', $_nama_program));
						if (empty($new_prog_fmis[strtolower($nama_program)])) {
							$cek_sipd_prog_belum_ada_di_fmis[$nama_program] = $sub;
						}
					}

					// update mapping sub kegiatan
					$current_mapping = get_option('_crb_custom_mapping_subkeg_fmis');
					$current_mapping = explode(',', $current_mapping);
					$mapping_sub = array();
					foreach ($current_mapping as $v) {
						$rek = explode('-', $v);
						$mapping_sub[$rek[0]] = '';
						if (!empty($rek[1])) {
							$mapping_sub[$rek[0]] = $rek[1];
						}
					}
					$mapping = array();
					foreach ($cek_sipd_belum_ada_di_fmis as $k => $v) {
						$k = '[' . $k . ']';
						if (!empty($mapping_sub[$k])) {
							$mapping[] = $k . '-' . $mapping_sub[$k];
						} else {
							$mapping[] = $k . '-' . $k;
						}
					}
					update_option('_crb_custom_mapping_subkeg_fmis', implode(',', $mapping));

					// update mapping kegiatan
					$current_mapping = get_option('_crb_custom_mapping_keg_fmis');
					$current_mapping = explode(',', $current_mapping);
					$mapping_keg = array();
					foreach ($current_mapping as $v) {
						$rek = explode('-', $v);
						$mapping_keg[$rek[0]] = '';
						if (!empty($rek[1])) {
							$mapping_keg[$rek[0]] = $rek[1];
						}
					}
					$mapping = array();
					foreach ($cek_sipd_keg_belum_ada_di_fmis as $k => $v) {
						$k = '[' . $k . ']';
						if (!empty($mapping_keg[$k])) {
							$mapping[] = $k . '-' . $mapping_keg[$k];
						} else {
							$mapping[] = $k . '-' . $k;
						}
					}
					update_option('_crb_custom_mapping_keg_fmis', implode(',', $mapping));

					// update mapping program
					$current_mapping = get_option('_crb_custom_mapping_program_fmis');
					$current_mapping = explode(',', $current_mapping);
					$mapping_prog = array();
					foreach ($current_mapping as $v) {
						$rek = explode('-', $v);
						$mapping_prog[$rek[0]] = '';
						if (!empty($rek[1])) {
							$mapping_prog[$rek[0]] = $rek[1];
						}
					}
					$mapping = array();
					foreach ($cek_sipd_prog_belum_ada_di_fmis as $k => $v) {
						$k = '[' . $k . ']';
						if (!empty($mapping_prog[$k])) {
							$mapping[] = $k . '-' . $mapping_prog[$k];
						} else {
							$mapping[] = $k . '-' . $k;
						}
					}
					update_option('_crb_custom_mapping_program_fmis', implode(',', $mapping));

					$ret['data_prog'] = $cek_sipd_prog_belum_ada_di_fmis;
					$ret['data_keg'] = $cek_sipd_keg_belum_ada_di_fmis;
					$ret['data_sub'] = $cek_sipd_belum_ada_di_fmis;
					$ret['message'] = 'Program yang perlu dimapping ada ' . count($cek_sipd_prog_belum_ada_di_fmis) . ' program. Kegiatan yang perlu dimapping ada ' . count($cek_sipd_keg_belum_ada_di_fmis) . ' kegiatan. Sub kegiatan yang perlu dimapping ada ' . count($cek_sipd_belum_ada_di_fmis) . ' sub kegiatan. Informasi detail cek di WP-SIPD dashboard.';
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'format salah!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	function remove_kode($teks)
	{
		$t = explode(' ', $teks);
		unset($t[0]);
		return implode(' ', $t);
	}

	function singkronisasi_total_sub_keg_fmis()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil edit pagu_fmis sub kegiatan SIPD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['data'])) {
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$sub_keg_fmis = json_decode(stripslashes($_POST['data']));
					$ret['message_rinci'] = array();
					$ret['sql'] = array();
					// cek jika sub kegiatan di mapping
					$subkeg_mapping = $this->get_fmis_mapping(array(
						'name' => '_crb_custom_mapping_subkeg_fmis'
					));
					$keg_mapping = $this->get_fmis_mapping(array(
						'name' => '_crb_custom_mapping_keg_fmis'
					));
					$program_mapping = $this->get_fmis_mapping(array(
						'name' => '_crb_custom_mapping_program_fmis'
					));
					$rek_mapping = $this->get_fmis_mapping(array(
						'name' => '_crb_custom_mapping_rekening_fmis'
					));

					$pindah_subkeg_mapping = $this->get_fmis_mapping(array(
						'name' => '_crb_custom_mapping_pindah_subkeg_fmis'
					));
					$new_pindah_subkeg_mapping = array();
					foreach ($pindah_subkeg_mapping as $kode_sub => $val) {
						$key = $this->replace_text($this->remove_kode($val['nama_sub_giat']) . ' | ' . $val['nama_giat'] . ' | ' . $val['nama_program']);
						$new_pindah_subkeg_mapping[$key] = array(
							'kode_sub_asli' => $kode_sub,
							'data_fmis' => $val
						);
					}

					$total_fmis = 0;
					$cek_sub_unit_fmis = array();
					$cek_sub_keg_fmis_id_sub_asli = array();
					foreach ($sub_keg_fmis as $fmis) {
						$data_fmis = (array) $fmis;

						$new_rincian = array();
						foreach ($data_fmis['rincian'] as $rincian) {
							$new_rincian[] = (array) $rincian;
						}
						$data_fmis['rincian'] = $new_rincian;

						$id_mapping = $data_fmis['idsubunit'];
						// cek mapping di id_mapping sub unit
						$get_id_asli = $this->get_id_skpd_fmis($id_mapping, $tahun_anggaran, true);
						if (empty($get_id_asli['id_skpd_sipd'])) {
							$get_id_by_nama_sub = false;
							if (
								!empty($data_fmis['rincian'][0])
								&& !empty($data_fmis['rincian'][0]['aktivitas'])
							) {
								$nama_sub_skpd = explode(' | ', $data_fmis['rincian'][0]['aktivitas']);
								if (count($nama_sub_skpd) >= 2) {
									$get_id_by_nama_sub = $wpdb->get_row($wpdb->prepare("
										SELECT
											kode_skpd,
											nama_skpd,
											id_skpd
										FROM data_unit
										where active=1
											AND tahun_anggaran=%d
											AND nama_skpd=%s
									", $tahun_anggaran, $nama_sub_skpd[1]), ARRAY_A);
									$ret['sql'][] = $wpdb->last_query;
								}
							}
							if (empty($get_id_by_nama_sub)) {
								// cek jika id_sub_skpd asli belum ketemu
								if (empty($cek_sub_keg_fmis_id_sub_asli[$data_fmis['sub_kegiatan']])) {
									$id_mapping = $data_fmis['idunit'];
									// cek mapping di id_mapping unit
									$get_id = $this->get_id_skpd_fmis($id_mapping, $tahun_anggaran, false);
								} else {
									$get_id = array('id_skpd_sipd' => false);
								}
							} else {
								// get id skpd dari nama skpd di aktivitas
								$get_id = array('id_skpd_sipd' => array($get_id_by_nama_sub['id_skpd']));
							}
						} else {
							// get id skpd dari mapping id sub unit
							$get_id = $get_id_asli;
							if (empty($cek_sub_keg_fmis_id_sub_asli[$data_fmis['sub_kegiatan']])) {
								$cek_sub_keg_fmis_id_sub_asli[$data_fmis['sub_kegiatan']] = true;
							}
						}
						$id_skpd_sipd = $get_id['id_skpd_sipd'];

						if (!empty($id_skpd_sipd)) {

							$data_fmis['sub_kegiatan_asli'] = $data_fmis['sub_kegiatan'];
							$data_fmis['kegiatan_asli'] = $data_fmis['kegiatan'];
							$data_fmis['program_asli'] = $data_fmis['program'];
							$key_fmis = $this->replace_text($data_fmis['sub_kegiatan'] . ' | ' . $data_fmis['kegiatan'] . ' | ' . $data_fmis['program']);
							// cek apakah sub kegiatan fmis hasil dari mapping pindah kegiatan
							if (!empty($new_pindah_subkeg_mapping[$key_fmis])) {
								$bl = $wpdb->get_row($wpdb->prepare("
									SELECT
										nama_program,
										nama_giat,
										nama_sub_giat
									FROM data_prog_keg
									where kode_sub_giat=%s
										and active=1
										and tahun_anggaran=%d
									order by id_sub_giat desc
								", $new_pindah_subkeg_mapping[$key_fmis]['kode_sub_asli'], $_POST['tahun_anggaran']), ARRAY_A);
								$data_fmis['sub_kegiatan'] = $this->remove_kode($bl['nama_sub_giat']);
								$data_fmis['kegiatan'] = $this->remove_kode($bl['nama_giat']);
								$data_fmis['program'] = $this->remove_kode($bl['nama_program']);
								// cek jika sub kegiatan adalah hasil mapping
							} else {
								foreach ($subkeg_mapping as $nama_sub_sipd => $nama_sub_fmis) {
									if ($this->removeNewline($data_fmis['sub_kegiatan']) == $this->removeNewline($nama_sub_fmis)) {
										$data_fmis['sub_kegiatan'] = $nama_sub_sipd;
									}
								}
								foreach ($keg_mapping as $nama_giat_sipd => $nama_giat_fmis) {
									if ($this->removeNewline($data_fmis['kegiatan']) == $this->removeNewline($nama_giat_fmis)) {
										$data_fmis['kegiatan'] = $nama_giat_sipd;
									}
								}
								foreach ($program_mapping as $nama_program_sipd => $nama_program_fmis) {
									if ($this->removeNewline($data_fmis['program']) == $this->removeNewline($nama_program_fmis)) {
										$data_fmis['program'] = $nama_program_sipd;
									}
								}
							}

							$singkron_rincian_fmis = get_option('_crb_backup_rincian_fmis');
							if (
								$singkron_rincian_fmis == 1
								|| $data_fmis['rincian'][0]['kdrek1'] == 4
								|| $data_fmis['rincian'][0]['kdrek1'] == 6
							) {
								// perlu dicek agar data sebelumnya tidak dirubah active jadi 0
								if (empty($cek_sub_unit_fmis[$id_skpd_sipd[0]])) {
									// untuk membedakan pembiyaan pengeluaran dan penerimaa maka perlu ditambahkan param kdrek1 dan kdrek2
									$wpdb->update('data_rincian_fmis', array(
										'active' => 0
									), array(
										'nama_sub_giat' => $data_fmis['sub_kegiatan'],
										'id_sub_skpd' => $id_skpd_sipd[0],
										'kdrek1' => $data_fmis['rincian'][0]['kdrek1'],
										'kdrek2' => $data_fmis['rincian'][0]['kdrek2']
									));
								}
								foreach ($data_fmis['rincian'] as $key => $rinci) {
									foreach ($rek_mapping as $rek_mapping_sipd => $rek_mapping_fmis) {
										$_kode_akun = explode('.', $rinci['kode_rekening']);
										$_kode_akun = (int)$_kode_akun[0] . '.' . (int)$_kode_akun[1] . '.' . (int)$_kode_akun[2] . '.' . (int)$_kode_akun[3] . '.' . (int)$_kode_akun[4] . '.' . (int)$_kode_akun[5];
										if ($_kode_akun == $rek_mapping_fmis) {
											$_kode_akun = explode('.', $rek_mapping_sipd);
											$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
											$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
											$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
											$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
											$rinci['kode_rekening'] = implode('.', $_kode_akun);
											$rinci['kdrek1'] = $rek_mapping_sipd[0];
											$data_fmis['rincian'][$key] = $rinci;
										}
									}
									$get_rinci = $wpdb->get_results($wpdb->prepare("
										SELECT
											id
										FROM data_rincian_fmis
										WHERE dt_rowid = %s
											AND idaktivitas = %d
											AND tahun_anggaran = %d
									", $rinci['DT_RowId'], $rinci['idaktivitas'], $tahun_anggaran), ARRAY_A);
									$opsi = array(
										'idaktivitas' => $rinci['idaktivitas'],
										'id_mapping' => $id_mapping,
										'id_sub_skpd' => $id_skpd_sipd[0],
										'nama_sub_giat' => $data_fmis['sub_kegiatan'],
										'nama_giat' => $data_fmis['kegiatan'],
										'nama_program' => $data_fmis['program'],
										'aktivitas' => $rinci['aktivitas'],
										'dt_rowid' => $rinci['DT_RowId'],
										'dt_rowindex' => $rinci['DT_RowIndex'],
										'created_id' => $rinci['created_id'],
										'harga' => $rinci['harga'],
										'idrkpdrenjabelanja' => $rinci['idrkpdrenjabelanja'],
										'idsatuan1' => $rinci['idsatuan1'],
										'idsatuan2' => $rinci['idsatuan2'],
										'idsatuan3' => $rinci['idsatuan3'],
										'idssh_4' => $rinci['idssh_4'],
										'idsumberdana' => $rinci['idsumberdana'],
										'jml_volume' => $rinci['jml_volume'],
										'jml_volume_renja' => $rinci['jml_volume_renja'],
										'jumlah' => $rinci['jumlah'],
										'jumlah_renja' => $rinci['jumlah_renja'],
										'kdrek1' => $rinci['kdrek1'],
										'kdrek2' => $rinci['kdrek2'],
										'kdrek3' => $rinci['kdrek3'],
										'kdrek4' => $rinci['kdrek4'],
										'kdrek5' => $rinci['kdrek5'],
										'kdrek6' => $rinci['kdrek6'],
										'kdurut' => $rinci['kdurut'],
										'kode_rekening' => $rinci['kode_rekening'],
										'nmrek6' => $rinci['nmrek6'],
										'rekening_display' => $rinci['rekening_display'],
										'satuan123' => $rinci['satuan123'],
										'singkat_sat1' => $rinci['singkat_sat1'],
										'singkat_sat2' => $rinci['singkat_sat2'],
										'singkat_sat3' => $rinci['singkat_sat3'],
										'status_data' => $rinci['status_data'],
										'status_dokumen' => $rinci['status_dokumen'],
										'status_pelaksanaan' => $rinci['status_pelaksanaan'],
										'tahun' => $rinci['tahun'],
										'uraian_belanja' => $rinci['uraian_belanja'],
										'uraian_ssh' => $rinci['uraian_ssh'],
										'uraian_sumberdana' => $rinci['uraian_sumberdana'],
										'volume_1' => $rinci['volume_1'],
										'volume_2' => $rinci['volume_2'],
										'volume_3' => $rinci['volume_3'],
										'volume_renja1' => $rinci['volume_renja1'],
										'volume_renja2' => $rinci['volume_renja2'],
										'volume_renja3' => $rinci['volume_renja3'],
										'tahun_anggaran' => $tahun_anggaran,
										'active' => 1,
									);
									if (!empty($rinci['created_at'])) {
										$opsi['created_at'] = $rinci['created_at'];
									}
									if (empty($get_rinci)) {
										$wpdb->insert('data_rincian_fmis', $opsi);
									} else {
										$wpdb->update('data_rincian_fmis', $opsi, array(
											'id' => $get_rinci[0]['id']
										));
									}
								}
							}

							// jika sub kegiatan dengan id sub unit sudah ada sebelumnya maka total fmis ditambahkan
							if (empty($cek_sub_unit_fmis[$id_skpd_sipd[0]])) {
								$cek_sub_unit_fmis[$id_skpd_sipd[0]] = true;
								$total_fmis = $data_fmis['total'];
							} else {
								$total_fmis += $data_fmis['total'];
							}

							// belanja
							if (
								$data_fmis['rincian'][0]['kdrek1'] == 5
								|| $data_fmis['sub_kegiatan'] != 'Non Sub Kegiatan' // agar bisa merubah pagu rincian 0
							) {
								$sub_sipd = $wpdb->get_results($wpdb->prepare("
									SELECT
										s.id,
										s.nama_sub_giat as nama_sub_giat_bl,
										p.nama_program,
										p.nama_giat,
										p.nama_sub_giat
									FROM data_sub_keg_bl s
									LEFT JOIN data_prog_keg p ON p.kode_sub_giat=s.kode_sub_giat
										AND p.status=''
									WHERE s.tahun_anggaran = %d
										AND s.active = 1
										AND s.id_sub_skpd = %d
								", $_POST['tahun_anggaran'], $id_skpd_sipd[0]), ARRAY_A);
								$ret['sql'][] = $wpdb->last_query;
								$sub = array();
								foreach ($sub_sipd as $v) {
									// pengecekan karena sub kegiatan belum dimutakhirkan sedangkan master program kegiatan sudah dimutakhirkan
									if (empty($v['nama_sub_giat'])) {
										$nm_sub = explode(' ', $v['nama_sub_giat_bl']);
										$kode_sub = $nm_sub[0];
										$bl = $wpdb->get_results("
											SELECT
												nama_program,
												nama_giat,
												nama_sub_giat
											FROM data_prog_keg
											where kode_sub_giat='$kode_sub'
											order by id_sub_giat desc
										", ARRAY_A);
										if (!empty($bl)) {
											$new_sub = array();
											foreach ($bl as $vv) {
												$nm_sub_x = explode(' ', $vv['nama_sub_giat']);
												unset($nm_sub_x[0]);

												if (!empty($_POST['debug'])) {
													echo $this->replace_text(implode(' ', $nm_sub_x)) . ' == ' . $this->replace_text($data_fmis['sub_kegiatan']) . "\n";
													echo $this->replace_text(implode(' ', $nm_sub_x)) . ' == ' . $this->replace_text($data_fmis['sub_kegiatan_asli']) . "\n";
												}

												if (
													$this->replace_text(implode(' ', $nm_sub_x)) == $this->replace_text($data_fmis['sub_kegiatan'])
													|| $this->replace_text(implode(' ', $nm_sub_x)) == $this->replace_text($data_fmis['sub_kegiatan_asli'])
												) {
													$new_sub = $vv;
												}
											}
											if (!empty($new_sub)) {
												$v['nama_sub_giat'] = $new_sub['nama_sub_giat'];
												$v['nama_giat'] = $new_sub['nama_giat'];
												$v['nama_program'] = $new_sub['nama_program'];
											}
										}
									}
									$nm_sub = explode(' ', $v['nama_sub_giat']);
									unset($nm_sub[0]);
									$nm_giat = explode(' ', $v['nama_giat']);
									unset($nm_giat[0]);
									$nm_prog = explode(' ', $v['nama_program']);
									unset($nm_prog[0]);

									if (!empty($_POST['debug'])) {
										echo $this->replace_text(implode(' ', $nm_sub)) . ' == ' . $this->replace_text($data_fmis['sub_kegiatan']) . "\n";
										echo $this->replace_text(implode(' ', $nm_sub)) . ' == ' . $this->replace_text($data_fmis['sub_kegiatan_asli']) . "\n";
										echo $this->replace_text(implode(' ', $nm_giat)) . ' == ' . $this->replace_text($data_fmis['kegiatan']) . "\n";
										echo $this->replace_text(implode(' ', $nm_giat)) . ' == ' . $this->replace_text($data_fmis['kegiatan_asli']) . "\n";
										echo $this->replace_text(implode(' ', $nm_prog)) . ' == ' . $this->replace_text($data_fmis['program']) . "\n";
										echo $this->replace_text(implode(' ', $nm_prog)) . ' == ' . $this->replace_text($data_fmis['program_asli']) . "\n\n";
									}

									if (
										(
											$this->replace_text(implode(' ', $nm_sub)) == $this->replace_text($data_fmis['sub_kegiatan'])
											|| $this->replace_text(implode(' ', $nm_sub)) == $this->replace_text($data_fmis['sub_kegiatan_asli'])
										)
										&& (
											$this->replace_text(implode(' ', $nm_giat)) == $this->replace_text($data_fmis['kegiatan'])
											|| $this->replace_text(implode(' ', $nm_giat)) == $this->replace_text($data_fmis['kegiatan_asli'])
										)
										&& (
											$this->replace_text(implode(' ', $nm_prog)) == $this->replace_text($data_fmis['program'])
											|| $this->replace_text(implode(' ', $nm_prog)) == $this->replace_text($data_fmis['program_asli'])
										)
									) {
										$sub = $v;
									}
								}
								if (!empty($sub)) {
									$wpdb->update('data_sub_keg_bl', array(
										'pagu_fmis' => $total_fmis
									), array(
										'id' => $sub['id']
									));
								} else {
									$ret['status'] = 'error';
									$ret['message_rinci'][] = 'Sub Kegiatan SIPD dari id_sub_skpd=' . $id_skpd_sipd[0] . ' dan sub kegiatan="' . $data_fmis['sub_kegiatan'] . '" tidak ditemukan | ' . $wpdb->last_query;
								}
								// pendapatan
							} else if ($data_fmis['rincian'][0]['kdrek1'] == 4) {
								foreach ($data_fmis['rincian'] as $rinci) {
									$uraian_belanja = $this->get_uraian_belanja_fmis($rinci['uraian_belanja']);
									$uraian = $uraian_belanja['uraian'];
									$keterangan = $uraian_belanja['keterangan'];
									$sub_sipd = $wpdb->get_results($wpdb->prepare("
										SELECT
											id,
											uraian,
											keterangan
										FROM data_pendapatan
										WHERE tahun_anggaran = %d
											AND active = 1
											AND id_skpd = %d
											AND kode_akun = %s
									", $tahun_anggaran, $id_skpd_sipd[0], $rinci['kode_rekening']), ARRAY_A);
									$new_sub_sipd = array();
									$data_db = array();
									foreach ($sub_sipd as $val) {
										$uraian_db = str_replace('&', 'dan', html_entity_decode($val['uraian']));
										$keterangan_db = str_replace('&', 'dan', html_entity_decode($val['keterangan']));
										$data_db[] = array(
											'uraian' => $uraian_db,
											'keterangan' => $keterangan_db
										);
										if (
											$this->removeNewline($uraian) == $this->removeNewline($uraian_db)
											&& $this->removeNewline($keterangan) == $this->removeNewline($keterangan_db)
										) {
											$new_sub_sipd = $val;
										}
									}
									if (!empty($new_sub_sipd)) {
										$wpdb->update('data_pendapatan', array(
											'pagu_fmis' => $rinci['jumlah']
										), array(
											'id' => $new_sub_sipd['id']
										));
									} else {
										$ret['message_rinci'][] = 'Rekening Pendapatan SIPD dari kode_akun=' . $rinci['kode_rekening'] . ', id_skpd=' . $id_skpd_sipd[0] . ' dan aktivitas="' . $rinci['aktivitas'] . '" tidak ditemukan | ' . $wpdb->last_query . ' | uraian=' . $uraian . ' | keterangan=' . $keterangan . ' | ' . json_encode($data_db);
									}
								}
								// pembiayaan
							} else if ($data_fmis['rincian'][0]['kdrek1'] == 6) {
								foreach ($data_fmis['rincian'] as $rinci) {
									$uraian_belanja = $this->get_uraian_belanja_fmis($rinci['uraian_belanja']);
									$uraian = $uraian_belanja['uraian'];
									$keterangan = $uraian_belanja['keterangan'];
									$sub_sipd = $wpdb->get_results($wpdb->prepare("
										SELECT
											id,
											uraian,
											keterangan
										FROM data_pembiayaan
										WHERE tahun_anggaran = %d
											AND active = 1
											AND id_skpd = %d
											AND kode_akun = %s
									", $tahun_anggaran, $id_skpd_sipd[0], $rinci['kode_rekening']), ARRAY_A);
									$new_sub_sipd = array();
									$data_db = array();
									foreach ($sub_sipd as $val) {
										$uraian_db = str_replace('&', 'dan', html_entity_decode($val['uraian']));
										$keterangan_db = str_replace('&', 'dan', html_entity_decode($val['keterangan']));
										$data_db[] = array(
											'uraian' => $uraian_db,
											'keterangan' => $keterangan_db
										);
										if (
											$this->removeNewline($uraian) == $this->removeNewline($uraian_db)
											&& $this->removeNewline($keterangan) == $this->removeNewline($keterangan_db)
										) {
											$new_sub_sipd = $val;
										}
									}
									if (!empty($new_sub_sipd)) {
										$wpdb->update('data_pembiayaan', array(
											'pagu_fmis' => $rinci['jumlah']
										), array(
											'id' => $new_sub_sipd['id']
										));
									} else {
										$ret['message_rinci'][] = 'Rekening Pembiayaan SIPD dari kode_akun=' . $rinci['kode_rekening'] . ', id_skpd=' . $id_skpd_sipd[0] . ' dan aktivitas="' . $rinci['aktivitas'] . '" tidak ditemukan | ' . $wpdb->last_query . ' | uraian=' . $uraian . ' | keterangan=' . $keterangan . ' | ' . json_encode($data_db);
									}
								}
							} else {
								$ret['status'] = 'error';
								$ret['message'] = 'Jenis rincian tidak ditemukan! Bukan belanja, pendapatan dan pembiayaan.';
							}
						} else {
							$ret['status'] = 'error';
							$ret['message'] = 'Sub Unit SIPD dari id mapping ' . $id_mapping . ' tidak ditemukan';
						}
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'format salah!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	function replace_text($text, $debug = false)
	{
		$text = strtolower(trim(html_entity_decode($text), " \t\n\r\0\x0B\xC2\xA0"));
		$text = $this->removeNewline($text);
		if ($debug) {
			echo $text . ' | ';
		}
		return $text;
	}

	function get_uraian_belanja_fmis($uraian)
	{
		$ret = array(
			'uraian' => '',
			'keterangan' => ''
		);
		$uraian_belanja = explode('Rupiah ', $uraian);
		if (count($uraian_belanja) >= 2) {
			$uraian_belanja = explode(' | ', $uraian_belanja[1]);
			$ret['uraian'] = $uraian_belanja[0];
			if (count($uraian_belanja) >= 2) {
				$ret['keterangan'] = $uraian_belanja[1];
			}
		}
		return $ret;
	}

	public function jadwal_renja($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		$tipe_perencanaan = 'renja';

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penjadwalan/wpsipd-public-setting-penjadwalan.php';
	}

	public function jadwal_rpjm($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penjadwalan/wpsipd-public-setting-penjadwalan-rpjm.php';
	}

	public function jadwal_renstra($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penjadwalan/wpsipd-public-setting-penjadwalan-renstra.php';
	}

	public function jadwal_rpjpd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penjadwalan/wpsipd-public-setting-penjadwalan-rpjpd.php';
	}

	public function jadwal_rpd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penjadwalan/wpsipd-public-setting-penjadwalan-rpd.php';
	}

	public function get_spd($cek_return = false)
	{
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil get SPD!',
			'data'	=> array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$mapping_skpd = $this->get_id_skpd_fmis(false, $tahun_anggaran);
				$sql = $wpdb->prepare("
					SELECT 
					* 
					FROM data_spd 
					where tahun_anggaran=%d 
				", $tahun_anggaran);
				$return['sql'] = $sql;
				$data_spd = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				if (!empty($data_spd)) {
					foreach ($data_spd as $k => $spd) {
						$kd_sub_unit = $spd->kd_urusan . '.' . $spd->kd_bidang . '.' . $spd->kd_unit . '.' . $spd->kd_sub;
						$data_spd[$k]->kd_sub_unit = $kd_sub_unit;
						if (!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])) {
							$data_spd[$k]->skpd = $mapping_skpd['id_mapping_simda'][$kd_sub_unit];
						}
					}
				} else {
					$data_spd = array();
				}
				$return['data'] = $data_spd;
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		if ($cek_return) {
			return $return;
		} else {
			die(json_encode($return));
		}
	}

	public function get_spd_rinci($cek_return = false)
	{
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil get SPD rinci!',
			'data'	=> array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare(
					"
					SELECT 
						s.*,
						r.kd_rek90_1,
						r.kd_rek90_2,
						r.kd_rek90_3,
						r.kd_rek90_4,
						r.kd_rek90_5,
						r.kd_rek90_6
					FROM ta_spd_rinc s
					left join ref_rek_mapping r on r.kd_rek_1=s.kd_rek_1
						and r.kd_rek_2=s.kd_rek_2
						and r.kd_rek_3=s.kd_rek_3
						and r.kd_rek_4=s.kd_rek_4
						and r.kd_rek_5=s.kd_rek_5
					where s.tahun=%d
						and s.no_spd=%s
						and s.kd_urusan=%d
						and s.kd_bidang=%d
						and s.kd_unit=%d
						and s.kd_sub=%d
					",
					$tahun_anggaran,
					$_POST['no_spd'],
					$_POST['kd_urusan'],
					$_POST['kd_bidang'],
					$_POST['kd_unit'],
					$_POST['kd_sub']
				);
				$return['sql'] = $sql;
				$data_spd = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				$program_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_program_fmis'
				));
				$keg_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_keg_fmis'
				));
				$subkeg_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_subkeg_fmis'
				));
				$rek_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_rekening_fmis'
				));
				$prog_keg = array();
				foreach ($data_spd as $k => $spd) {
					$kd_urusan_asli = substr($spd->id_prog, 0, 1);
					$kd_bidang_asli = (int) substr($spd->id_prog, 1);
					$kd_keg_simda = $kd_urusan_asli . '.' . $kd_bidang_asli . '.' . $spd->kd_prog . '.' . $spd->kd_keg;
					if (empty($prog_keg[$kd_keg_simda])) {
						$prog_keg[$kd_keg_simda] = array();
						$sql = "
							SELECT 
								m.*
							from ref_kegiatan_mapping m
							where m.kd_urusan=$kd_urusan_asli
								and m.kd_bidang=$kd_bidang_asli
								and m.kd_prog=$spd->kd_prog
								and m.kd_keg=$spd->kd_keg
						";
						$mapping = $this->simda->CurlSimda(array(
							'query' => $sql,
							'debug' => 1
						));
						$kd_urusan_sipd = $mapping[0]->kd_urusan90;
						$kd_bidang_sipd = $mapping[0]->kd_bidang90;
						if ($mapping[0]->kd_program90 == 1) {
							$kd_urusan_sipd = 'X';
							$kd_bidang_sipd = 'XX';
						}
						$kode_sub = $kd_urusan_sipd . '.' . $this->simda->CekNull($kd_bidang_sipd) . '.' . $this->simda->CekNull($mapping[0]->kd_program90) . '.' . $mapping[0]->kd_kegiatan90 . '.' . $this->simda->CekNull($mapping[0]->kd_sub_kegiatan);
						$sub = $wpdb->get_results($wpdb->prepare("
							select
								*
							from data_prog_keg
							where tahun_anggaran=%d
								and kode_sub_giat=%s
						", $tahun_anggaran, $kode_sub), ARRAY_A);

						$sub[0]['nama_program'] = explode(' ', $sub[0]['nama_program']);
						unset($sub[0]['nama_program'][0]);
						$sub[0]['nama_program'] = implode(' ', $sub[0]['nama_program']);

						$sub[0]['nama_giat'] = explode(' ', $sub[0]['nama_giat']);
						unset($sub[0]['nama_giat'][0]);
						$sub[0]['nama_giat'] = implode(' ', $sub[0]['nama_giat']);

						$sub[0]['nama_sub_giat'] = explode(' ', $sub[0]['nama_sub_giat']);
						unset($sub[0]['nama_sub_giat'][0]);
						$sub[0]['nama_sub_giat'] = implode(' ', $sub[0]['nama_sub_giat']);

						$prog_keg[$kd_keg_simda]['nama_program'] = $sub[0]['nama_program'];
						$prog_keg[$kd_keg_simda]['nama_giat'] = $sub[0]['nama_giat'];
						$prog_keg[$kd_keg_simda]['nama_sub_giat'] = $sub[0]['nama_sub_giat'];
						if (!empty($program_mapping[$this->removeNewline($sub[0]['nama_program'])])) {
							$prog_keg[$kd_keg_simda]['nama_program'] = $program_mapping[$this->removeNewline($sub[0]['nama_program'])];
						}
						if (!empty($keg_mapping[$this->removeNewline($sub[0]['nama_giat'])])) {
							$prog_keg[$kd_keg_simda]['nama_giat'] = $keg_mapping[$this->removeNewline($sub[0]['nama_giat'])];
						}
						if (!empty($subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])])) {
							$prog_keg[$kd_keg_simda]['nama_sub_giat'] = $subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])];
						}
					}

					$kode_akun = $spd->kd_rek90_1 . '.' . $spd->kd_rek90_2 . '.' . $spd->kd_rek90_3 . '.' . $spd->kd_rek90_4 . '.' . $spd->kd_rek90_5 . '.' . $spd->kd_rek90_6;
					$prog_keg[$kd_keg_simda]['kode_akun'] = $kode_akun;
					if (!empty($rek_mapping[$kode_akun])) {
						$prog_keg[$kd_keg_simda]['kode_akun'] = $rek_mapping[$kode_akun];
						$akun = explode('.', $rek_mapping[$kode_akun]);
						$data_spd[$k]->kd_rek90_1 = $akun[0];
						$data_spd[$k]->kd_rek90_2 = $akun[1];
						$data_spd[$k]->kd_rek90_3 = $akun[2];
						$data_spd[$k]->kd_rek90_4 = $akun[3];
						$data_spd[$k]->kd_rek90_5 = $akun[4];
						$data_spd[$k]->kd_rek90_6 = $akun[5];
					}
					$data_spd[$k]->detail = $prog_keg[$kd_keg_simda];
				}
				$return['data'] = $data_spd;
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		if ($cek_return) {
			return $return;
		} else {
			die(json_encode($return));
		}
	}

	public function get_spp_rinci($cek_return = false)
	{
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil get SPP rinci!',
			'data'	=> array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare(
					"
					SELECT 
						s.*,
						r.kd_rek90_1,
						r.kd_rek90_2,
						r.kd_rek90_3,
						r.kd_rek90_4,
						r.kd_rek90_5,
						r.kd_rek90_6
					FROM ta_spp_rinc s
					left join ref_rek_mapping r on r.kd_rek_1=s.kd_rek_1
						and r.kd_rek_2=s.kd_rek_2
						and r.kd_rek_3=s.kd_rek_3
						and r.kd_rek_4=s.kd_rek_4
						and r.kd_rek_5=s.kd_rek_5
					where s.tahun=%d
						and s.no_spp=%s
						and s.kd_urusan=%d
						and s.kd_bidang=%d
						and s.kd_unit=%d
						and s.kd_sub=%d
					",
					$tahun_anggaran,
					$_POST['no_spp'],
					$_POST['kd_urusan'],
					$_POST['kd_bidang'],
					$_POST['kd_unit'],
					$_POST['kd_sub']
				);
				$return['sql'] = $sql;
				$data_spd = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				$program_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_program_fmis'
				));
				$keg_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_keg_fmis'
				));
				$subkeg_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_subkeg_fmis'
				));
				$rek_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_rekening_fmis'
				));
				$prog_keg = array();
				foreach ($data_spd as $k => $spd) {
					$kd_urusan_asli = substr($spd->id_prog, 0, 1);
					$kd_bidang_asli = (int) substr($spd->id_prog, 1);
					$kd_keg_simda = $kd_urusan_asli . '.' . $kd_bidang_asli . '.' . $spd->kd_prog . '.' . $spd->kd_keg;
					if (empty($prog_keg[$kd_keg_simda])) {
						$prog_keg[$kd_keg_simda] = array();
						$sql = "
							SELECT 
								m.*
							from ref_kegiatan_mapping m
							where m.kd_urusan=$kd_urusan_asli
								and m.kd_bidang=$kd_bidang_asli
								and m.kd_prog=$spd->kd_prog
								and m.kd_keg=$spd->kd_keg
						";
						$mapping = $this->simda->CurlSimda(array(
							'query' => $sql,
							'debug' => 1
						));
						$kd_urusan_sipd = $mapping[0]->kd_urusan90;
						$kd_bidang_sipd = $mapping[0]->kd_bidang90;
						if ($mapping[0]->kd_program90 == 1) {
							$kd_urusan_sipd = 'X';
							$kd_bidang_sipd = 'XX';
						}
						$kode_sub = $kd_urusan_sipd . '.' . $this->simda->CekNull($kd_bidang_sipd) . '.' . $this->simda->CekNull($mapping[0]->kd_program90) . '.' . $mapping[0]->kd_kegiatan90 . '.' . $this->simda->CekNull($mapping[0]->kd_sub_kegiatan);
						$sub = $wpdb->get_results($wpdb->prepare("
							select
								*
							from data_prog_keg
							where tahun_anggaran=%d
								and kode_sub_giat=%s
						", $tahun_anggaran, $kode_sub), ARRAY_A);

						$sub[0]['nama_program'] = explode(' ', $sub[0]['nama_program']);
						unset($sub[0]['nama_program'][0]);
						$sub[0]['nama_program'] = implode(' ', $sub[0]['nama_program']);

						$sub[0]['nama_giat'] = explode(' ', $sub[0]['nama_giat']);
						unset($sub[0]['nama_giat'][0]);
						$sub[0]['nama_giat'] = implode(' ', $sub[0]['nama_giat']);

						$sub[0]['nama_sub_giat'] = explode(' ', $sub[0]['nama_sub_giat']);
						unset($sub[0]['nama_sub_giat'][0]);
						$sub[0]['nama_sub_giat'] = implode(' ', $sub[0]['nama_sub_giat']);

						$prog_keg[$kd_keg_simda]['nama_program'] = $sub[0]['nama_program'];
						$prog_keg[$kd_keg_simda]['nama_giat'] = $sub[0]['nama_giat'];
						$prog_keg[$kd_keg_simda]['nama_sub_giat'] = $sub[0]['nama_sub_giat'];
						if (!empty($program_mapping[$this->removeNewline($sub[0]['nama_program'])])) {
							$prog_keg[$kd_keg_simda]['nama_program'] = $program_mapping[$this->removeNewline($sub[0]['nama_program'])];
						}
						if (!empty($keg_mapping[$this->removeNewline($sub[0]['nama_giat'])])) {
							$prog_keg[$kd_keg_simda]['nama_giat'] = $keg_mapping[$this->removeNewline($sub[0]['nama_giat'])];
						}
						if (!empty($subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])])) {
							$prog_keg[$kd_keg_simda]['nama_sub_giat'] = $subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])];
						}
					}

					$kode_akun = $spd->kd_rek90_1 . '.' . $spd->kd_rek90_2 . '.' . $spd->kd_rek90_3 . '.' . $spd->kd_rek90_4 . '.' . $spd->kd_rek90_5 . '.' . $spd->kd_rek90_6;
					$prog_keg[$kd_keg_simda]['kode_akun'] = $kode_akun;
					if (!empty($rek_mapping[$kode_akun])) {
						$prog_keg[$kd_keg_simda]['kode_akun'] = $rek_mapping[$kode_akun];
						$akun = explode('.', $rek_mapping[$kode_akun]);
						$data_spd[$k]->kd_rek90_1 = $akun[0];
						$data_spd[$k]->kd_rek90_2 = $akun[1];
						$data_spd[$k]->kd_rek90_3 = $akun[2];
						$data_spd[$k]->kd_rek90_4 = $akun[3];
						$data_spd[$k]->kd_rek90_5 = $akun[4];
						$data_spd[$k]->kd_rek90_6 = $akun[5];
					}
					$data_spd[$k]->detail = $prog_keg[$kd_keg_simda];
				}
				$return['data'] = $data_spd;
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		if ($cek_return) {
			return $return;
		} else {
			die(json_encode($return));
		}
	}

	public function get_sp2b_rinci($cek_return = false)
	{
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil get SP2B rinci!',
			'data'	=> array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare(
					"
					SELECT 
						s.*,
						r.kd_rek90_1,
						r.kd_rek90_2,
						r.kd_rek90_3,
						r.kd_rek90_4,
						r.kd_rek90_5,
						r.kd_rek90_6
					FROM ta_sp3b_rinc s
					left join ref_rek_mapping r on r.kd_rek_1=s.kd_rek_1
						and r.kd_rek_2=s.kd_rek_2
						and r.kd_rek_3=s.kd_rek_3
						and r.kd_rek_4=s.kd_rek_4
						and r.kd_rek_5=s.kd_rek_5
					where s.tahun=%d
						and s.no_sp3b=%s
						and s.kd_urusan=%d
						and s.kd_bidang=%d
						and s.kd_unit=%d
						and s.kd_sub=%d
					",
					$tahun_anggaran,
					$_POST['no_sp2b'],
					$_POST['kd_urusan'],
					$_POST['kd_bidang'],
					$_POST['kd_unit'],
					$_POST['kd_sub']
				);
				$return['sql'] = $sql;
				$data_spd = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				$program_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_program_fmis'
				));
				$keg_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_keg_fmis'
				));
				$subkeg_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_subkeg_fmis'
				));
				$rek_mapping = $this->get_fmis_mapping(array(
					'name' => '_crb_custom_mapping_rekening_fmis'
				));
				$prog_keg = array();
				foreach ($data_spd as $k => $spd) {
					$kd_urusan_asli = substr($spd->id_prog, 0, 1);
					$kd_bidang_asli = (int) substr($spd->id_prog, 1);
					$kd_keg_simda = $kd_urusan_asli . '.' . $kd_bidang_asli . '.' . $spd->kd_prog . '.' . $spd->kd_keg;
					if (empty($prog_keg[$kd_keg_simda])) {
						$prog_keg[$kd_keg_simda] = array();
						$sql = "
							SELECT 
								m.*
							from ref_kegiatan_mapping m
							where m.kd_urusan=$kd_urusan_asli
								and m.kd_bidang=$kd_bidang_asli
								and m.kd_prog=$spd->kd_prog
								and m.kd_keg=$spd->kd_keg
						";
						$mapping = $this->simda->CurlSimda(array(
							'query' => $sql,
							'debug' => 1
						));
						$kd_urusan_sipd = $mapping[0]->kd_urusan90;
						$kd_bidang_sipd = $mapping[0]->kd_bidang90;
						if ($mapping[0]->kd_program90 == 1) {
							$kd_urusan_sipd = 'X';
							$kd_bidang_sipd = 'XX';
						}
						$kode_sub = $kd_urusan_sipd . '.' . $this->simda->CekNull($kd_bidang_sipd) . '.' . $this->simda->CekNull($mapping[0]->kd_program90) . '.' . $mapping[0]->kd_kegiatan90 . '.' . $this->simda->CekNull($mapping[0]->kd_sub_kegiatan);
						$sub = $wpdb->get_results($wpdb->prepare("
							select
								*
							from data_prog_keg
							where tahun_anggaran=%d
								and kode_sub_giat=%s
						", $tahun_anggaran, $kode_sub), ARRAY_A);

						$sub[0]['nama_program'] = explode(' ', $sub[0]['nama_program']);
						unset($sub[0]['nama_program'][0]);
						$sub[0]['nama_program'] = implode(' ', $sub[0]['nama_program']);

						$sub[0]['nama_giat'] = explode(' ', $sub[0]['nama_giat']);
						unset($sub[0]['nama_giat'][0]);
						$sub[0]['nama_giat'] = implode(' ', $sub[0]['nama_giat']);

						$sub[0]['nama_sub_giat'] = explode(' ', $sub[0]['nama_sub_giat']);
						unset($sub[0]['nama_sub_giat'][0]);
						$sub[0]['nama_sub_giat'] = implode(' ', $sub[0]['nama_sub_giat']);

						$prog_keg[$kd_keg_simda]['nama_program'] = $sub[0]['nama_program'];
						$prog_keg[$kd_keg_simda]['nama_giat'] = $sub[0]['nama_giat'];
						$prog_keg[$kd_keg_simda]['nama_sub_giat'] = $sub[0]['nama_sub_giat'];
						if (!empty($program_mapping[$this->removeNewline($sub[0]['nama_program'])])) {
							$prog_keg[$kd_keg_simda]['nama_program'] = $program_mapping[$this->removeNewline($sub[0]['nama_program'])];
						}
						if (!empty($keg_mapping[$this->removeNewline($sub[0]['nama_giat'])])) {
							$prog_keg[$kd_keg_simda]['nama_giat'] = $keg_mapping[$this->removeNewline($sub[0]['nama_giat'])];
						}
						if (!empty($subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])])) {
							$prog_keg[$kd_keg_simda]['nama_sub_giat'] = $subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])];
						}
					}

					$kode_akun = $spd->kd_rek90_1 . '.' . $spd->kd_rek90_2 . '.' . $spd->kd_rek90_3 . '.' . $spd->kd_rek90_4 . '.' . $spd->kd_rek90_5 . '.' . $spd->kd_rek90_6;
					$prog_keg[$kd_keg_simda]['kode_akun'] = $kode_akun;
					if (!empty($rek_mapping[$kode_akun])) {
						$prog_keg[$kd_keg_simda]['kode_akun'] = $rek_mapping[$kode_akun];
						$akun = explode('.', $rek_mapping[$kode_akun]);
						$data_spd[$k]->kd_rek90_1 = $akun[0];
						$data_spd[$k]->kd_rek90_2 = $akun[1];
						$data_spd[$k]->kd_rek90_3 = $akun[2];
						$data_spd[$k]->kd_rek90_4 = $akun[3];
						$data_spd[$k]->kd_rek90_5 = $akun[4];
						$data_spd[$k]->kd_rek90_6 = $akun[5];
					}
					$data_spd[$k]->detail = $prog_keg[$kd_keg_simda];
				}
				$return['data'] = $data_spd;
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		if ($cek_return) {
			return $return;
		} else {
			die(json_encode($return));
		}
	}

	public function get_pegawai_simda()
	{
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil get data Pegawai SIMDA!',
			'data'	=> array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare(
					"
					SELECT 
						p.*,
						j.nm_jab
					from ta_sub_unit_jab p
					left join ref_jabatan j on p.kd_jab=j.kd_jab
					where p.tahun=%d
					",
					$tahun_anggaran
				);
				$return['sql'] = $sql;
				$data_pegawai = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				$mapping_skpd = $this->get_id_skpd_fmis(false, $tahun_anggaran);
				foreach ($data_pegawai as $k => $pegawai) {
					$kd_sub_unit = $pegawai->kd_urusan . '.' . $pegawai->kd_bidang . '.' . $pegawai->kd_unit . '.' . $pegawai->kd_sub;
					$data_pegawai[$k]->kd_sub_unit = $kd_sub_unit;
					if (!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])) {
						$data_pegawai[$k]->skpd = $mapping_skpd['id_mapping_simda'][$kd_sub_unit];
					}
				}
				$return['data'] = $data_pegawai;
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	function singkronisasi_spd_fmis()
	{
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil singkronisasi data SPD!'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$spd = json_decode(stripslashes(html_entity_decode($_POST['data'])));
				$cek = $wpdb->get_results($wpdb->prepare("
						select 
							id 
						from data_spd 
						where tahun_anggaran=%d and no_spd=%s
					", $tahun_anggaran, $spd->spd_no), ARRAY_A);
				$data_spd = array(
					'no_spd' => $spd->spd_no,
					'uraian' => $spd->uraian,
					'id_skpd_fmis' => $spd->idunit,
					'created_at' => $spd->spd_tgl + ' 00:00:00',
					'active' => 1,
					'tahun_anggaran' => $tahun_anggaran
				);
				if (empty($cek)) {
					$wpdb->insert('data_spd', $data_spd);
				} else {
					$wpdb->update('data_spd', $data_spd, array('id' => $cek['id']));
				}
				$wpdb->update(
					'data_spd_rinci',
					array('active' => 0),
					array(
						'tahun_anggaran' => $tahun_anggaran,
						'no_spd' => $spd->spd_no
					)
				);
				foreach ($spd->spd_fmis_rinci as $k => $v) {
					$cek = $wpdb->get_results($wpdb->prepare(
						"
						select 
							id 
						from data_spd_rinci 
						where tahun_anggaran=%d 
							and no_spd=%s
							and kdrek1=%d
							and kdrek2=%d
							and kdrek3=%d
							and kdrek4=%d
							and kdrek5=%d
							and kdrek6=%d
							and idrefaktivitas=%d
							and idsubunit=%d
						",
						$tahun_anggaran,
						$no_spd,
						$v->kdrek1,
						$v->kdrek2,
						$v->kdrek3,
						$v->kdrek4,
						$v->kdrek5,
						$v->kdrek6,
						$v->idrefaktivitas,
						$v->idsubunit
					), ARRAY_A);
					$data_spd_rinci = array(
						'kdurut' => $v->kdurut,
						'no_spd' => $spd->spd_no,
						'idrefaktivitas' => $v->idrefaktivitas,
						'idsubunit' => $v->idsubunit,
						'kdrek1' => $v->kdrek1,
						'kdrek2' => $v->kdrek2,
						'kdrek3' => $v->kdrek3,
						'kdrek4' => $v->kdrek4,
						'kdrek5' => $v->kdrek5,
						'kdrek6' => $v->kdrek6,
						'nilai' => $v->nilai,
						'rekening' => $v->rekening,
						'aktivitas_uraian' => $v->aktivitas_uraian,
						'subkegiatan' => $v->subkegiatan,
						'active' => 1,
						'tahun_anggaran' => $tahun_anggaran
					);
					if (empty($cek)) {
						$wpdb->insert('data_spd_rinci', $data_spd_rinci);
					} else {
						$wpdb->update('data_spd_rinci', $data_spd_rinci, array('id' => $cek['id']));
					}
				}
			} else {
				$return['status'] = 'error';
				$return['message'] = 'Api Key tidak sesuai!';
			}
		} else {
			$return['status'] = 'error';
			$return['message'] = 'Format tidak sesuai!';
		}
		die(json_encode($return));
	}

	public function get_data_penjadwalan()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data' => array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['tipe_perencanaan'])) {
					$params = $_REQUEST;
					$columns = array(
						0 => 'id_jadwal_lokal',
						1 => 'nama',
						2 => 'waktu_awal',
						3 => 'waktu_akhir',
						4 => 'status',
						5 => 'tahun_anggaran',
						6 => 'relasi_perencanaan',
						7 => 'lama_pelaksanaan',
						8 => 'jenis_jadwal',
						9 => 'id_tipe',
						10 => 'tahun_akhir_anggaran'
					);
					$where = "";
					// Check if search value exists
					if (!empty($params['search']['value'])) {
						$search_value = "%" . $params['search']['value'] . "%";
						$where .= $wpdb->prepare(
							" AND ( nama LIKE %s OR waktu_awal LIKE %s OR waktu_akhir LIKE %s AND tahun_anggaran =%d )",
							$search_value,
							$search_value,
							$search_value,
							$_POST['tahun_anggaran']
						);
					}
					$tipe_perencanaan = $_POST['tipe_perencanaan'];
					$sqlTipe = $wpdb->get_results(
						$wpdb->prepare("
							SELECT * 
							FROM `data_tipe_perencanaan` 
							WHERE nama_tipe=%s
						", $tipe_perencanaan),
						ARRAY_A
					);
					if (empty($sqlTipe)) {
						$return = array(
							'status' => 'error',
							'message' => 'Data dengan tipe sesuai tidak ditemukan!'
						);
						die(json_encode($return));
					}

					if (!empty($_POST['tahun_anggaran'])) {
						$where .= $wpdb->prepare(" AND tahun_anggaran = %d", $_POST['tahun_anggaran']);
					}

					// getting total number records without any search
					$sqlTot = "SELECT count(*) as jml FROM `data_jadwal_lokal` WHERE id_tipe =" . $sqlTipe[0]['id'];
					$sqlRec = "SELECT " . implode(', ', $columns) . " FROM `data_jadwal_lokal` WHERE id_tipe =" . $sqlTipe[0]['id'];
					if (isset($where) && $where != '') {
						$sqlTot .= $where;
						$sqlRec .= $where;
					}

					$sqlRec .= $wpdb->prepare(
						" ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] .
							" LIMIT %d, %d",
						$params['start'],
						$params['length']
					);

					$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
					$totalRecords = $queryTot[0]['jml'];
					$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

					$is_admin = false;
					$user_id = um_user('ID');
					$user_meta = get_userdata($user_id);
					if (in_array("administrator", $user_meta->roles)) {
						$is_admin = true;
					}
					$checkOpenedSchedule = 0;
					if (!empty($queryRecords)) {
						foreach ($queryRecords as $recKey => $recVal) {
							$edit = '';
							$delete = '';
							$lock = '';
							$report = '<a class="btn btn-sm btn-primary action-btn" onclick="report(\'' . $recVal['id_jadwal_lokal'] . '\'); return false;" href="#" title="Cetak Laporan"><i class="dashicons dashicons-printer"></i></a>';
							$status = array(
								0 => '<span class="badge badge-success" style="font-size:inherit;">Terbuka</span>',
								1 => '<span class="badge badge-info" style="font-size:inherit;">Dikunci</span>',
								2 => '<span class="badge badge-secondary" style="font-size:inherit;">Selesai</span>'
							);

							if ($is_admin) {
								$edit = '<a class="btn btn-sm btn-warning action-btn" onclick="edit_data_penjadwalan(\'' . $recVal['id_jadwal_lokal'] . '\'); return false;" href="#" title="Edit data penjadwalan"><i class="dashicons dashicons-edit"></i></a>';
								$delete = '<a class="btn btn-sm btn-danger action-btn" onclick="hapus_data_penjadwalan(\'' . $recVal['id_jadwal_lokal'] . '\'); return false;" href="#" title="Hapus data penjadwalan"><i class="dashicons dashicons-trash"></i></a>';
								if ($recVal['status'] == 1) {
									$edit = '';
									$delete = '';
									$lock = '<a class="btn btn-sm btn-success action-btn disabled" onclick="cannot_change_schedule(\'kunci\'); return false;" href="#" title="Kunci data penjadwalan" aria-disabled="true"><i class="dashicons dashicons-lock"></i></a>';
								} else {
									$checkOpenedSchedule++;
									$lock = '<a class="btn btn-sm btn-success action-btn" onclick="lock_data_penjadwalan(\'' . $recVal['id_jadwal_lokal'] . '\'); return false;" href="#" title="Kunci data penjadwalan"><i class="dashicons dashicons-unlock"></i></a>';
								}
								if (
									in_array($tipe_perencanaan, ['renstra', 'renja'])
									and $recVal['status'] == 0
								) {
									$delete .= '<a class="btn btn-sm btn-danger action-btn copy-data" onclick="copy_usulan(); return false;" style="display:inline;" href="#" title="Copy Data Usulan ke Penetapan">Copy Data Usulan</a>';
									if ($tipe_perencanaan == 'renja') {
										$delete .= '<a class="btn btn-sm btn-danger action-btn copy-data" onclick="copy_penetapan(); return false;" href="#" style="display:inline;" title="Copy Data Penetapan ke Usulan">Copy Data Penetapan</a>';
										$delete .= '<a class="btn btn-sm btn-danger action-btn copy-data" data-toggle="modal" data-target="#modal-copy-renja-sipd" style="display:inline;" href="#" title="Copy Data RENJA SIPD ke Lokal">Copy Data RENJA SIPD ke Lokal</a>';
									}
									if ($tipe_perencanaan == 'renja' && !empty($recVal['relasi_perencanaan'])) {
										$delete .= '<a class="btn btn-sm btn-danger action-btn copy-data" style="display:inline;" onclick="copy_data_renstra(\'' . $recVal['id_jadwal_lokal'] . '\'); return false;" href="#" title="Copy Data RENSTRA ke RENJA">Copy Data RENSTRA</a>';
									}
								} else if (in_array($tipe_perencanaan, ['monev_renstra', 'monev_renja'])) {
									$lock = '';
									$delete = '';
								}
							}
							if (in_array($tipe_perencanaan, ['verifikasi_rka', 'verifikasi_rka_sipd'])) {
								$report = '';
							}

							$relasi_perencanaan = '-';
							$relasi_perencanaan_renstra = '-';
							if (!empty($recVal['relasi_perencanaan'])) {
								$data_relasi_perencanaan = $wpdb->get_results(
									$wpdb->prepare('
										SELECT * 
										FROM data_jadwal_lokal 
										WHERE id_jadwal_lokal= %d
										', $recVal['relasi_perencanaan']),
									ARRAY_A
								);

								if (!empty($data_relasi_perencanaan)) {
									$relasi_perencanaan = $data_relasi_perencanaan[0]['nama'];
									$nama_tipe = $wpdb->get_results(
										$wpdb->prepare('
											SELECT * 
											FROM data_tipe_perencanaan 
											WHERE id = %d
											', $data_relasi_perencanaan[0]['id_tipe']),
										ARRAY_A
									);
									$relasi_perencanaan_renstra = (!empty($nama_tipe)) ? strtoupper($nama_tipe[0]['nama_tipe']) . ' | ' . $relasi_perencanaan . ' | ' . $data_relasi_perencanaan[0]['tahun_anggaran'] . ' - ' . $data_relasi_perencanaan[0]['tahun_akhir_anggaran'] : '-';
								}
							}

							$queryRecords[$recKey]['waktu_awal'] = date('d-m-Y H:i', strtotime($recVal['waktu_awal']));
							$queryRecords[$recKey]['waktu_akhir'] = date('d-m-Y H:i', strtotime($recVal['waktu_akhir']));
							$queryRecords[$recKey]['aksi'] = '<div class="action-buttons">' . $report . $lock . $edit . $delete . '</div>';
							$queryRecords[$recKey]['nama'] = ucfirst($recVal['nama']);
							$queryRecords[$recKey]['status'] = ucfirst($status[$recVal['status']]);
							$queryRecords[$recKey]['tahun_anggaran_selesai'] = $recVal['tahun_anggaran'] + $recVal['lama_pelaksanaan'] - 1;
							$queryRecords[$recKey]['relasi_perencanaan'] = $relasi_perencanaan;
							$queryRecords[$recKey]['relasi_perencanaan_renstra'] = $relasi_perencanaan_renstra;
							$queryRecords[$recKey]['tahun_akhir_anggaran'] = $recVal['tahun_akhir_anggaran'];
							$queryRecords[$recKey]['jenis_jadwal'] = strtoupper($recVal['jenis_jadwal']);
						}

						$json_data = array(
							"draw" => intval($params['draw']),
							"recordsTotal" => intval($totalRecords),
							"recordsFiltered" => intval($totalRecords),
							"data" => $queryRecords,
							"checkOpenedSchedule" => $checkOpenedSchedule
						);
						die(json_encode($json_data));
					} else {
						$json_data = array(
							"draw" => intval($params['draw']),
							"recordsTotal" => 0,
							"recordsFiltered" => 0,
							"data" => array(),
							"checkOpenedSchedule" => $checkOpenedSchedule,
							"message" => "Data tidak ditemukan!"
						);
						die(json_encode($json_data));
					}
				} else {
					$return = array(
						'status' => 'error',
						'message' => 'Tipe Perencanaan Kosong!'
					);
				}
			} else {
				$return = array(
					'status' => 'error',
					'message' => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message' => 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}


	public function submit_add_schedule()
	{
		global $wpdb;
		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);
		$return = [
			'status' => 'success',
			'data'   => []
		];

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (in_array("administrator", $user_meta->roles)) {
					$tipe_perencanaan = trim(htmlspecialchars($_POST['tipe_perencanaan'] ?? ''));
					$nama = trim(htmlspecialchars($_POST['nama'] ?? ''));
					$tahun_anggaran = trim(htmlspecialchars($_POST['tahun_anggaran'] ?? ''));
					$relasi_perencanaan = !empty($_POST['relasi_perencanaan']) ? trim(htmlspecialchars($_POST['relasi_perencanaan'])) : NULL;
					$lama_pelaksanaan = trim(htmlspecialchars($_POST['lama_pelaksanaan'] ?? ''));
					$tahun_akhir_anggaran = trim(htmlspecialchars($_POST['tahun_akhir_anggaran'] ?? ''));
					$jenis_jadwal = trim(htmlspecialchars($_POST['jenis_jadwal'] ?? ''));

					$jadwal_mulai = date('Y-m-d H:i:s', strtotime(trim(htmlspecialchars($_POST['jadwal_mulai'] ?? ''))));
					$jadwal_selesai = date('Y-m-d H:i:s', strtotime(trim(htmlspecialchars($_POST['jadwal_selesai'] ?? ''))));
					$jenis_jadwal = trim(htmlspecialchars($_POST['jenis_jadwal'] ?? 'usulan'));
					$pergeseran_renja = $_POST['pergeseran_renja'] ?? 'false';
					$id_jadwal_pergeseran_renja = $pergeseran_renja === 'true' ? $_POST['id_jadwal_pergeseran_renja'] : NULL;
					$status_pergeseran_renja = $pergeseran_renja === 'true' ? 'tampil' : 'tidak_tampil';

					if (in_array($tipe_perencanaan, ['renja', 'penganggaran', 'penganggaran_sipd'])) {
						$lama_pelaksanaan = 1;
					} else {
						$lama_pelaksanaan = trim(htmlspecialchars($_POST['lama_pelaksanaan'] ?? ''));
					}

					$sqlTipe = $wpdb->get_row(
						$wpdb->prepare("
							SELECT * 
							FROM `data_tipe_perencanaan` 
							WHERE nama_tipe = %s
						", $tipe_perencanaan),
						ARRAY_A
					);

					if (!empty($sqlTipe)) {
						switch ($tipe_perencanaan) {
							case 'monev_rpjmd':
								if (
									!empty($nama)
									&& !empty($tahun_anggaran)
									&& !empty($lama_pelaksanaan)
									&& !empty($jenis_jadwal)
									&& !empty($tahun_akhir_anggaran)
								) {
									// Tambah Jadwal Monev RPJM RENSTRA
									$data_jadwal_rpjm = [
										'nama'                 => $nama,
										'tahun_anggaran'       => $tahun_anggaran,
										'relasi_perencanaan'   => $relasi_perencanaan,
										'id_tipe'  			   => $sqlTipe['id'],
										'lama_pelaksanaan'     => $lama_pelaksanaan,
										'tahun_akhir_anggaran' => $tahun_akhir_anggaran,
										'jenis_jadwal'    	   => $jenis_jadwal,
										'status'               => 0,
									];
									$wpdb->insert('data_jadwal_lokal', $data_jadwal_rpjm);
									$id_jadwal_rpjmd_baru = $wpdb->insert_id;

									$data_jadwal_renstra = [
										'nama'                 => 'Jadwal Renstra' . ' ' . $nama,
										'tahun_anggaran'       => $tahun_anggaran,
										'relasi_perencanaan'   => $id_jadwal_rpjmd_baru,
										'id_tipe'  			   => 15,
										'lama_pelaksanaan'     => $lama_pelaksanaan,
										'tahun_akhir_anggaran' => $tahun_akhir_anggaran,
										'status'               => 0,
									];
									$wpdb->insert('data_jadwal_lokal', $data_jadwal_renstra);

									$return = [
										'status'        => 'success',
										'message'       => 'Berhasil Tambah Jadwal Monev RPJMD dan RENSTRA!',
									];
								} else {
									$return = [
										'status'    => 'error',
										'message'   => 'Harap diisi semua, tidak boleh ada yang kosong!'
									];
								}
								break;

							case 'monev_renja':
								// Tambah Jadwal Monev Renja
								if (
									!empty($nama)
									&& !empty($tahun_anggaran)
									&& !empty($relasi_perencanaan)
								) {
									$cek_jadwal_terbuka = $wpdb->get_row(
										$wpdb->prepare('
											SELECT 
												*
											FROM data_jadwal_lokal
											WHERE id_tipe = %d
												AND status = %d
												AND tahun_anggaran = %d
										', 16, 0, $tahun_anggaran)
									);
									if (!empty($cek_jadwal_terbuka)) {
										$return = [
											'status'    => 'error',
											'message'   => 'GAGAL! Masih terdapat jadwal terbuka!'
										];
										die(json_encode($return));
									}
									$data_jadwal = [
										'nama'                 => $nama,
										'tahun_anggaran'       => $tahun_anggaran,
										'relasi_perencanaan'   => $relasi_perencanaan,
										'id_tipe'  			   => $sqlTipe['id'],
										'lama_pelaksanaan'     => 1,
										'status'               => 0,
									];
									$wpdb->insert('data_jadwal_lokal', $data_jadwal);

									$return = [
										'status'   => 'success',
										'message'  => 'Berhasil Tambah Jadwal Monev RENJA!',
									];
								} else {
									$return = [
										'status'    => 'error',
										'message'   => 'Harap diisi semua, tidak boleh ada yang kosong!'
									];
								}

								break;

							default:
								if (
									!empty($nama)
									&& !empty($jadwal_mulai)
									&& !empty($jadwal_selesai)
									&& !empty($tahun_anggaran)
									&& !empty($tipe_perencanaan)
									&& !empty($lama_pelaksanaan)
								) {
									$id_tipe = $sqlTipe['id'];
									$where_renja = ($id_tipe == 5 || $id_tipe == 6) ? $wpdb->prepare(' AND tahun_anggaran = %d', $tahun_anggaran) : '';
									$sqlSameTipe = $wpdb->get_results(
										$wpdb->prepare("
											SELECT * 
											FROM `data_jadwal_lokal` 
											WHERE id_tipe = %s
											$where_renja
										", $id_tipe),
										ARRAY_A
									);

									foreach ($sqlSameTipe as $valTipe) {
										if ($valTipe['status'] != 1) {
											$return = [
												'status'    => 'error',
												'message'   => 'Masih ada penjadwalan yang terbuka!',
												'sql'       => $wpdb->last_query
											];
											die(json_encode($return));
										}
										if (($jadwal_mulai > $valTipe['waktu_awal'] && $jadwal_mulai < $valTipe['waktu_akhir']) || ($jadwal_selesai > $valTipe['waktu_awal'] && $jadwal_selesai < $valTipe['waktu_akhir'])) {
											$return = [
												'status'    => 'error',
												'message'   => 'Waktu sudah dipakai jadwal lain!'
											];
											die(json_encode($return));
										}
									}

									$data_jadwal = [
										'nama'                     => $nama,
										'waktu_awal'               => $jadwal_mulai,
										'waktu_akhir'              => $jadwal_selesai,
										'tahun_anggaran'           => $tahun_anggaran,
										'status'                   => 0,
										'id_tipe'                  => $id_tipe,
										'relasi_perencanaan'       => $relasi_perencanaan,
										'lama_pelaksanaan'         => $lama_pelaksanaan,
										'jenis_jadwal'             => $jenis_jadwal,
										'id_jadwal_pergeseran'     => $id_jadwal_pergeseran_renja,
										'status_jadwal_pergeseran' => $status_pergeseran_renja
									];

									$wpdb->insert('data_jadwal_lokal', $data_jadwal);

									$return = [
										'status'        => 'success',
										'message'       => 'Berhasil!',
										'data_jadwal'   => $data_jadwal,
									];
								} else {
									$return = [
										'status'    => 'error',
										'message'   => 'Harap diisi semua, tidak boleh ada yang kosong!'
									];
								}
								break;
						}
					} else {
						$return = [
							'status'    => 'error',
							'message'   => 'Tipe penjadwalan tidak diketahui!'
						];
					}
				} else {
					$return = [
						'status'    => 'error',
						'message'   => 'User tidak diijinkan!'
					];
				}
			} else {
				$return = [
					'status'    => 'error',
					'message'   => 'Api Key tidak sesuai!'
				];
			}
		} else {
			$return = [
				'status'    => 'error',
				'message'   => 'Format tidak sesuai!'
			];
		}
		die(json_encode($return));
	}



	public function get_data_jadwal_by_id()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_jadwal_lokal = $_POST['id_jadwal_lokal'];

				$data_penjadwalan_by_id = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_jadwal_lokal WHERE id_jadwal_lokal = %d', $id_jadwal_lokal), ARRAY_A);

				$select_option_renja_pergeseran = '<option value="">Pilih RENJA Pergeseran</option>';
				$data_renja_pergeseran = $wpdb->get_results(
					$wpdb->prepare('
						SELECT
							*
						FROM
							data_jadwal_lokal
						WHERE id_jadwal_lokal NOT IN (' . $id_jadwal_lokal . ')
						  AND status=1
						  AND id_tipe=5
						  AND tahun_anggaran=%d
					', $data_penjadwalan_by_id[0]['tahun_anggaran']),
					ARRAY_A
				);

				if (!empty($data_renja_pergeseran)) {
					foreach ($data_renja_pergeseran as $val_renja) {
						$tanggal_kunci = substr($val_renja['waktu_akhir'], 0, 10);
						$select_option_renja_pergeseran .= '<option value="' . $val_renja['id_jadwal_lokal'] . '">' . $val_renja['nama'] . ' || ' . $tanggal_kunci . '</option>';
					}
				}
				$data_penjadwalan_by_id[0]['select_option_pergeseran_renja'] = $select_option_renja_pergeseran;

				$return = array(
					'status' => 'success',
					'data' 	 => $data_penjadwalan_by_id[0]
				);
			} else {
				$return = array(
					'status'	=> 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status'	=> 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function submit_edit_schedule()
	{
		global $wpdb;
		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);
		$return = array(
			'status' => 'success',
			'data'   => array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (in_array("administrator", $user_meta->roles)) {
					if (!empty($_POST['id_jadwal_lokal'])) {
						$id_jadwal_lokal = trim(htmlspecialchars($_POST['id_jadwal_lokal']));
					} else {
						$return = [
							'status' => 'error',
							'message' => 'Id Jadwal Kosong!'
						];
						die(json_encode($return));
					}
					$data_this_id = $wpdb->get_row(
						$wpdb->prepare('
							SELECT * 
							FROM data_jadwal_lokal 
							WHERE id_jadwal_lokal = %d
						', $id_jadwal_lokal),
						ARRAY_A
					);
					if (empty($data_this_id)) {
						$return = [
							'status'  => 'error',
							'message' => 'Data tidak ditemukan!',
						];
						die(json_encode($return));
					}

					$nama					= trim(htmlspecialchars($_POST['nama'] ?? ''));
					$tahun_anggaran 		= trim(htmlspecialchars($_POST['tahun_anggaran'] ?? ''));
					$relasi_perencanaan 	= !empty($_POST['relasi_perencanaan']) ? trim(htmlspecialchars($_POST['relasi_perencanaan'])) : NULL;
					$lama_pelaksanaan 		= trim(htmlspecialchars($_POST['lama_pelaksanaan'] ?? ''));
					$jadwal_mulai			= date('Y-m-d H:i:s', strtotime(trim(htmlspecialchars($_POST['jadwal_mulai']))));
					$jadwal_selesai  		= date('Y-m-d H:i:s', strtotime(trim(htmlspecialchars($_POST['jadwal_selesai']))));
					$jenis_jadwal    		= trim(htmlspecialchars($_POST['jenis_jadwal']));
					$pergeseran_renja 		= $_POST['pergeseran_renja'];
					$tipe_perencanaan 		= trim(htmlspecialchars($_POST['tipe_perencanaan'] ?? ''));
					$tahun_akhir_anggaran 	= trim(htmlspecialchars($_POST['tahun_akhir_anggaran'] ?? ''));
					$jenis_jadwal 			= trim(htmlspecialchars($_POST['jenis_jadwal'] ?? ''));

					$status_check = array(0, NULL, 2);
					if (!in_array($data_this_id['status'], $status_check)) {
						$return = [
							'status'  => 'error',
							'message' => "User tidak diijinkan!\nData sudah dikunci!",
						];
						die(json_encode($return));
					}

					$sqlTipe = $wpdb->get_results(
						$wpdb->prepare("
							SELECT * 
							FROM `data_tipe_perencanaan` 
							WHERE nama_tipe = %s
                    	", $tipe_perencanaan),
						ARRAY_A
					);
					if (!empty($sqlTipe)) {
						switch ($tipe_perencanaan) {
							case 'monev_rpjmd':
								//Update RPJM sekaligus RENSTRA
								if (
									empty($nama)
									&& empty($tahun_anggaran)
									&& empty($lama_pelaksanaan)
									&& empty($tahun_akhir_anggaran)
									&& empty($jenis_jadwal)
								) {
									$return = [
										'status' => 'error',
										'message' => 'Harap diisi semua, tidak boleh ada yang kosong!'
									];
									die(json_encode($return));
								}

								$data_jadwal_rpjm = [
									'nama'                	=> $nama,
									'tahun_anggaran'      	=> $tahun_anggaran,
									'lama_pelaksanaan'    	=> $lama_pelaksanaan,
									'tahun_akhir_anggaran'  => $tahun_akhir_anggaran,
									'jenis_jadwal'			=> $jenis_jadwal
								];

								$data_jadwal_renstra = [
									'tahun_anggaran'      	=> $tahun_anggaran,
									'lama_pelaksanaan'    	=> $lama_pelaksanaan,
									'tahun_akhir_anggaran'  => $tahun_akhir_anggaran
								];

								$wpdb->update('data_jadwal_lokal', $data_jadwal_rpjm, [
									'id_jadwal_lokal' => $id_jadwal_lokal,
									'id_tipe' 		  => 17
								]);
								$wpdb->update('data_jadwal_lokal', $data_jadwal_renstra, [
									'relasi_perencanaan' => $id_jadwal_lokal,
									'id_tipe' 			 => 15
								]);

								$return = [
									'status'  => 'success',
									'message' => 'Berhasil Perbarui Jadwal Monev RPJM RENSTRA!',
								];
								break;

							case 'monev_renstra':
								//Update RENSTRA
								if (empty($nama)) {
									$return = [
										'status'  => 'error',
										'message' => 'Nama RENSTRA Kosong!'
									];
									die(json_encode($return));
								}

								$data_jadwal_renstra = [
									'nama' => $nama
								];

								$wpdb->update('data_jadwal_lokal', $data_jadwal_renstra, [
									'id_jadwal_lokal' => $id_jadwal_lokal
								]);

								$return = [
									'status'  => 'success',
									'message' => 'Berhasil Perbarui Jadwal Monev RENSTRA!',
								];
								break;

							case 'monev_renja':
								//Update RENJA
								if (empty($nama) || empty($relasi_perencanaan)) {
									$return = [
										'status'  => 'error',
										'message' => 'Harap diisi semua, tidak boleh ada yang kosong!'
									];
									die(json_encode($return));
								}

								$data_jadwal_renja = [
									'nama'    			 => $nama,
									'relasi_perencanaan' => $relasi_perencanaan
								];

								$wpdb->update('data_jadwal_lokal', $data_jadwal_renja, [
									'id_jadwal_lokal' => $id_jadwal_lokal
								]);

								$return = [
									'status'  => 'success',
									'message' => 'Berhasil Perbarui Jadwal Monev RENJA!',
								];
								break;

							default:
								if (
									empty($_POST['id_jadwal_lokal'])
									&& empty($_POST['nama'])
									&& empty($_POST['jadwal_mulai'])
									&& empty($_POST['jadwal_selesai'])
									&& empty($_POST['tahun_anggaran'])
									&& empty($lama_pelaksanaan)
								) {
									$return = [
										'status' => 'error',
										'message' => 'Harap diisi semua, tidak boleh ada yang kosong!'
									];
									die(json_encode($return));
								}
								if (in_array($tipe_perencanaan, ['renja', 'penganggaran', 'penganggaran_sipd'])) {
									$lama_pelaksanaan = 1;
								}

								if ($pergeseran_renja == 'true' && !empty($_POST['id_jadwal_pergeseran_renja'])) {
									$status_pergeseran_renja = 'tampil';
									$id_jadwal_pergeseran_renja = $_POST['id_jadwal_pergeseran_renja'];
								} else {
									$status_pergeseran_renja = 'tidak_tampil';
									$id_jadwal_pergeseran_renja = NULL;
								}

								$arr_jadwal = ['usulan', 'penetapan'];
								$jenis_jadwal = in_array($jenis_jadwal, $arr_jadwal) ? $jenis_jadwal : 'usulan';

								$data_jadwal = [
									'nama'                    => $nama,
									'waktu_awal'              => $jadwal_mulai,
									'waktu_akhir'             => $jadwal_selesai,
									'tahun_anggaran'          => $tahun_anggaran,
									'relasi_perencanaan'      => $relasi_perencanaan,
									'lama_pelaksanaan'        => $lama_pelaksanaan,
									'jenis_jadwal'            => $jenis_jadwal,
									'id_jadwal_pergeseran'    => $id_jadwal_pergeseran_renja,
									'status_jadwal_pergeseran' => $status_pergeseran_renja
								];

								$wpdb->update('data_jadwal_lokal', $data_jadwal, [
									'id_jadwal_lokal' => $id_jadwal_lokal
								]);

								$return = [
									'status'        => 'success',
									'message'       => 'Berhasil Perbarui Jadwal!',
								];
								break;
						}
					} else {
						$return = [
							'status'    => 'error',
							'message'   => 'Tipe penjadwalan tidak diketahui!'
						];
					}
				} else {
					$return = [
						'status' => 'error',
						'message' => "User tidak diijinkan!",
					];
				}
			} else {
				$return = [
					'status' => 'error',
					'message' => 'Api Key tidak sesuai!'
				];
			}
		} else {
			$return = [
				'status' => 'error',
				'message' => 'Format tidak sesuai!'
			];
		}
		die(json_encode($return));
	}



	/** Submit delete data jadwal */
	public function submit_delete_schedule()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['id_jadwal_lokal'])) {
					if (in_array("administrator", $user_meta->roles)) {
						$id_jadwal_lokal = trim(htmlspecialchars($_POST['id_jadwal_lokal']));

						$data_this_id = $wpdb->get_row($wpdb->prepare('
							SELECT 
								* 
							FROM data_jadwal_lokal 
							WHERE id_jadwal_lokal = %d
						', $id_jadwal_lokal), ARRAY_A);

						if (!empty($data_this_id)) {
							$status_check = array(0, NULL, 2);
							if (in_array($data_this_id['status'], $status_check)) {
								$wpdb->delete('data_jadwal_lokal', array(
									'id_jadwal_lokal' => $id_jadwal_lokal
								), array('%d'));

								$nilai_pergeseran_renja = update_option('_nilai_pergeseran_renja', 'tidak_tampil');

								$return = array(
									'status' => 'success',
									'message'	=> 'Berhasil!',
								);
							} else {
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						} else {
							$return = array(
								'status' => 'error',
								'message'	=> "Data tidak ditemukan!",
							);
						}
					} else {
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}
				} else {
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** get data default lama pelaksanaan by id */
	public function get_data_standar_lama_pelaksanaan()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tipe_perencanaan = $_POST['tipe_perencanaan'];

				$lama_pelaksanaan_by_name = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_tipe_perencanaan WHERE nama_tipe = %s', $tipe_perencanaan), ARRAY_A);

				$return = array(
					'status' 						=> 'success',
					'data' 							=> $lama_pelaksanaan_by_name[0]
				);
			} else {
				$return = array(
					'status'	=> 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status'	=> 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit lock data jadwal */
	public function submit_lock_schedule()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['id_jadwal_lokal'])) {
					if (in_array("administrator", $user_meta->roles)) {
						$id_jadwal_lokal = $_POST['id_jadwal_lokal'];

						$data_this_id = $wpdb->get_row($wpdb->prepare('
							SELECT 
								j.*,
								t.nama_tipe 
							FROM data_jadwal_lokal j
							INNER JOIN data_tipe_perencanaan t on t.id=j.id_tipe 
							WHERE j.id_jadwal_lokal = %d
						', $id_jadwal_lokal), ARRAY_A);
						if (empty($data_this_id)) {
							$return = array(
								'status' => 'error',
								'message'	=> "Jadwal dengan ID $id_jadwal_lokal tidak ditemukan!",
							);
							die(json_encode($return));
						}

						$timezone = get_option('timezone_string');
						if (preg_match("/Asia/i", $timezone)) {
							date_default_timezone_set($timezone);
						} else {
							$return = array(
								'status' => 'error',
								'message'	=> "Pengaturan timezone salah. Pilih salah satu kota di zona waktu yang sama dengan anda, antara lain:  \'Jakarta\',\'Makasar\',\'Jayapura\'",
							);
							die(json_encode($return));
						}

						$dateTime = new DateTime();
						$time_now = $dateTime->format('Y-m-d H:i:s');
						if ($time_now > $data_this_id['waktu_awal']) {
							$status_check = array(0, NULL, 2);
							if (in_array($data_this_id['status'], $status_check)) {
								$prefix = '';
								if (strpos($data_this_id['nama_tipe'], '_sipd') == false) {
									$prefix = '_lokal';
								}

								//lock data penjadwalan
								$wpdb->update('data_jadwal_lokal', array('waktu_akhir' => $time_now, 'status' => 1), array(
									'id_jadwal_lokal'	=> $id_jadwal_lokal
								));

								$delete_lokal_history = $this->delete_data_lokal_history('data_rka' . $prefix, $data_this_id['id_jadwal_lokal']);

								$columns_1 = array(
									'created_user',
									'createddate',
									'createdtime',
									'harga_satuan',
									'harga_satuan_murni',
									'id_daerah',
									'id_rinci_sub_bl',
									'id_standar_nfs',
									'is_locked',
									'jenis_bl',
									'ket_bl_teks',
									'kode_akun',
									'koefisien',
									'koefisien_murni',
									'lokus_akun_teks',
									'nama_akun',
									'nama_komponen',
									'spek_komponen',
									'satuan',
									'spek',
									'sat1',
									'sat2',
									'sat3',
									'sat4',
									'volum1',
									'volum2',
									'volum3',
									'volum4',
									'volume',
									'volume_murni',
									'subs_bl_teks',
									'subtitle_teks',
									'kode_dana',
									'is_paket',
									'nama_dana',
									'id_dana',
									'substeks',
									'total_harga',
									'rincian',
									'rincian_murni',
									'totalpajak',
									'pajak',
									'pajak_murni',
									'updated_user',
									'updateddate',
									'updatedtime',
									'user1',
									'user2',
									'active',
									'update_at',
									'tahun_anggaran',
									'idbl',
									'idsubbl',
									'kode_bl',
									'kode_sbl',
									'id_prop_penerima',
									'id_camat_penerima',
									'id_kokab_penerima',
									'id_lurah_penerima',
									'id_penerima',
									'idkomponen',
									'idketerangan',
									'idsubtitle'
								);

								$sql_backup_data_rka =  "
									INSERT INTO data_rka" . $prefix . "_history (" . implode(', ', $columns_1) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $columns_1) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_rka" . $prefix . " 
										WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords1 = $wpdb->query($sql_backup_data_rka);

								$delete_lokal_history = $this->delete_data_lokal_history('data_sub_keg_bl' . $prefix, $data_this_id['id_jadwal_lokal']);

								$columns_2 = array(
									'id_sub_skpd',
									'id_lokasi',
									'id_label_kokab',
									'nama_dana',
									'no_sub_giat',
									'kode_giat',
									'id_program',
									'nama_lokasi',
									'waktu_akhir',
									'pagu_n_lalu',
									'id_urusan',
									'id_unik_sub_bl',
									'id_sub_giat',
									'label_prov',
									'kode_program',
									'kode_sub_giat',
									'no_program',
									'kode_urusan',
									'kode_bidang_urusan',
									'nama_program',
									'target_4',
									'target_5',
									'id_bidang_urusan',
									'nama_bidang_urusan',
									'target_3',
									'no_giat',
									'id_label_prov',
									'waktu_awal',
									'pagumurni',
									'pagu',
									'pagu_simda',
									'output_sub_giat',
									'sasaran',
									'indikator',
									'id_dana',
									'nama_sub_giat',
									'pagu_n_depan',
									'satuan',
									'id_rpjmd',
									'id_giat',
									'id_label_pusat',
									'nama_giat',
									'kode_skpd',
									'nama_skpd',
									'kode_sub_skpd',
									'id_skpd',
									'id_sub_bl',
									'nama_sub_skpd',
									'target_1',
									'nama_urusan',
									'target_2',
									'label_kokab',
									'label_pusat',
									'pagu_keg',
									'pagu_fmis',
									'id_bl',
									'kode_bl',
									'kode_sbl',
									'active',
									'update_at',
									'tahun_anggaran'
								);

								if ($prefix == '_lokal') {
									$columns_2 = array_merge($columns_2, array(
										'catatan',
										'catatan_usulan',
										'pagu_usulan',
										'pagu_n_depan_usulan',
										'waktu_awal_usulan',
										'waktu_akhir_usulan',
										'sasaran_usulan',
										'kode_sbl_lama'
									));
								}

								$sql_backup_data_sub_keg_bl =  "
									INSERT INTO data_sub_keg_bl" . $prefix . "_history (" . implode(', ', $columns_2) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $columns_2) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_sub_keg_bl" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords2 = $wpdb->query($sql_backup_data_sub_keg_bl);

								$delete_lokal_history = $this->delete_data_lokal_history('data_sub_keg_indikator' . $prefix, $data_this_id['id_jadwal_lokal']);

								$oclumns_3 = array(
									'outputteks',
									'targetoutput',
									'satuanoutput',
									'idoutputbl',
									'targetoutputteks',
									'kode_sbl',
									'idsubbl',
									'active',
									'update_at',
									'tahun_anggaran'
								);

								if ($prefix == '_lokal') {
									$oclumns_3 = array_merge($oclumns_3, array(
										'outputteks_usulan',
										'targetoutput_usulan',
										'satuanoutput_usulan',
										'targetoutputteks_usulan',
										'id_indikator_sub_giat'
									));
								}

								$sql_backup_data_sub_keg_indikator =  "
									INSERT INTO data_sub_keg_indikator" . $prefix . "_history (" . implode(', ', $oclumns_3) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $oclumns_3) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_sub_keg_indikator" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords3 = $wpdb->query($sql_backup_data_sub_keg_indikator);

								$delete_lokal_history = $this->delete_data_lokal_history('data_keg_indikator_hasil' . $prefix, $data_this_id['id_jadwal_lokal']);

								$oclumns_4 = array(
									'hasilteks',
									'satuanhasil',
									'targethasil',
									'targethasilteks',
									'kode_sbl',
									'idsubbl',
									'active',
									'update_at',
									'tahun_anggaran'
								);

								if ($prefix == '_lokal') {
									$oclumns_4 = array_merge($oclumns_4, array(
										'hasilteks_usulan',
										'satuanhasil_usulan',
										'targethasil_usulan',
										'targethasilteks_usulan',
										'catatan',
										'catatan_usulan'
									));
								}

								$sql_backup_data_keg_indikator_hasil =  "
									INSERT INTO data_keg_indikator_hasil" . $prefix . "_history (" . implode(', ', $oclumns_4) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $oclumns_4) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_keg_indikator_hasil" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords4 = $wpdb->query($sql_backup_data_keg_indikator_hasil);

								$delete_lokal_history = $this->delete_data_lokal_history('data_tag_sub_keg', $data_this_id['id_jadwal_lokal']);

								$oclumns_5 = array(
									'idlabelgiat',
									'namalabel',
									'idtagbl',
									'kode_sbl',
									'idsubbl',
									'active',
									'update_at',
									'tahun_anggaran'
								);

								$sql_backup_data_tag_sub_keg =  "
									INSERT INTO data_tag_sub_keg_history (" . implode(', ', $oclumns_5) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $oclumns_5) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_tag_sub_keg 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords5 = $wpdb->query($sql_backup_data_tag_sub_keg);

								$delete_lokal_history = $this->delete_data_lokal_history('data_capaian_prog_sub_keg' . $prefix, $data_this_id['id_jadwal_lokal']);

								$oclumns_6 = array(
									'satuancapaian',
									'targetcapaianteks',
									'capaianteks',
									'targetcapaian',
									'kode_sbl',
									'idsubbl',
									'active',
									'update_at',
									'tahun_anggaran'
								);

								if ($prefix == '_lokal') {
									$oclumns_6 = array_merge($oclumns_6, array(
										'satuancapaian_usulan',
										'targetcapaianteks_usulan',
										'capaianteks_usulan',
										'targetcapaian_usulan',
										'catatan',
										'catatan_usulan'
									));
								}

								$sql_backup_data_capaian_prog_sub_keg =  "
									INSERT INTO data_capaian_prog_sub_keg" . $prefix . "_history (" . implode(', ', $oclumns_6) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $oclumns_6) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_capaian_prog_sub_keg" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords6 = $wpdb->query($sql_backup_data_capaian_prog_sub_keg);

								$delete_lokal_history = $this->delete_data_lokal_history('data_output_giat_sub_keg' . $prefix, $data_this_id['id_jadwal_lokal']);

								$oclumns_7 = array(
									'outputteks',
									'satuanoutput',
									'targetoutput',
									'targetoutputteks',
									'kode_sbl',
									'idsubbl',
									'active',
									'update_at',
									'tahun_anggaran'
								);

								if ($prefix == '_lokal') {
									$oclumns_7 = array_merge($oclumns_7, array(
										'outputteks_usulan',
										'satuanoutput_usulan',
										'targetoutput_usulan',
										'targetoutputteks_usulan',
										'catatan',
										'catatan_usulan'
									));
								}

								$sql_backup_data_output_giat_sub_keg =  "
									INSERT INTO data_output_giat_sub_keg" . $prefix . "_history (" . implode(', ', $oclumns_7) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $oclumns_7) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_output_giat_sub_keg" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords7 = $wpdb->query($sql_backup_data_output_giat_sub_keg);

								$delete_lokal_history = $this->delete_data_lokal_history('data_dana_sub_keg' . $prefix, $data_this_id['id_jadwal_lokal']);

								$oclumns_8 = array(
									'namadana',
									'kodedana',
									'iddana',
									'iddanasubbl',
									'pagudana',
									'kode_sbl',
									'idsubbl',
									'active',
									'update_at',
									'tahun_anggaran'
								);

								if ($prefix == '_lokal') {
									$oclumns_8 = array_merge($oclumns_8, array(
										'nama_dana_usulan',
										'kode_dana_usulan',
										'id_dana_usulan',
										'pagu_dana_usulan'
									));
								}

								$sql_backup_data_dana_sub_keg =  "
									INSERT INTO data_dana_sub_keg" . $prefix . "_history (" . implode(', ', $oclumns_8) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $oclumns_8) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_dana_sub_keg" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords8 = $wpdb->query($sql_backup_data_dana_sub_keg);

								$delete_lokal_history = $this->delete_data_lokal_history('data_lokasi_sub_keg' . $prefix, $data_this_id['id_jadwal_lokal']);

								$oclumns_9 = array(
									'camatteks',
									'daerahteks',
									'idcamat',
									'iddetillokasi',
									'idkabkota',
									'idlurah',
									'lurahteks',
									'kode_sbl',
									'idsubbl',
									'active',
									'update_at',
									'tahun_anggaran'
								);

								if ($prefix == '_lokal') {
									$oclumns_9 = array_merge($oclumns_9, array(
										'camatteks_usulan',
										'daerahteks_usulan',
										'idcamat_usulan',
										'iddetillokasi_usulan',
										'idkabkota_usulan',
										'idlurah_usulan',
										'lurahteks_usulan'
									));
								}

								$sql_backup_data_lokasi_sub_keg =  "
									INSERT INTO data_lokasi_sub_keg" . $prefix . "_history (" . implode(', ', $oclumns_9) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $oclumns_9) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_lokasi_sub_keg" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords9 = $wpdb->query($sql_backup_data_lokasi_sub_keg);

								$delete_lokal_history = $this->delete_data_lokal_history('data_mapping_sumberdana', $data_this_id['id_jadwal_lokal']);

								$oclumns_10 = array(
									'id_rinci_sub_bl',
									'id_sumber_dana',
									'user',
									'active',
									'update_at',
									'tahun_anggaran'
								);

								$sql_backup_data_mapping_sumberdana =  "
									INSERT INTO data_mapping_sumberdana_history (" . implode(', ', $oclumns_10) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $oclumns_10) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_mapping_sumberdana 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords10 = $wpdb->query($sql_backup_data_mapping_sumberdana);

								$delete_lokal_history = $this->delete_data_lokal_history('data_pendapatan' . $prefix, $data_this_id['id_jadwal_lokal']);

								$oclumns_11 = array(
									'created_user',
									'createddate',
									'createdtime',
									'id_pendapatan',
									'keterangan',
									'kode_akun',
									'nama_akun',
									'nilaimurni',
									'program_koordinator',
									'rekening',
									'skpd_koordinator',
									'total',
									'pagu_fmis',
									'updated_user',
									'updateddate',
									'updatedtime',
									'uraian',
									'urusan_koordinator',
									'user1',
									'user2',
									'id_skpd',
									'active',
									'update_at',
									'tahun_anggaran'
								);

								$sql_backup_data_pendapatan =  "
									INSERT INTO data_pendapatan" . $prefix . "_history (" . implode(', ', $oclumns_11) . ",id_jadwal,id_asli)
									SELECT 
										" . implode(', ', $oclumns_11) . ",
										" . $data_this_id['id_jadwal_lokal'] . ",
										id as id_asli
									FROM data_pendapatan" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords11 = $wpdb->query($sql_backup_data_pendapatan);

								$delete_lokal_history = $this->delete_data_lokal_history('data_pembiayaan' . $prefix, $data_this_id['id_jadwal_lokal']);

								$oclumns_12 = array(
									'created_user',
									'createddate',
									'createdtime',
									'id_pembiayaan',
									'keterangan',
									'kode_akun',
									'nama_akun',
									'nilaimurni',
									'program_koordinator',
									'rekening',
									'skpd_koordinator',
									'total',
									'pagu_fmis',
									'updated_user',
									'updateddate',
									'updatedtime',
									'uraian',
									'urusan_koordinator',
									'type',
									'user1',
									'user2',
									'id_skpd',
									'active',
									'update_at',
									'tahun_anggaran'
								);

								$sql_backup_data_pembiayaan =  "
									INSERT INTO data_pembiayaan" . $prefix . "_history (" . implode(', ', $oclumns_12) . ",id_jadwal,id_asli)
									SELECT 
										" . implode(', ', $oclumns_12) . ",
										" . $data_this_id['id_jadwal_lokal'] . ",
										id as id_asli
									FROM data_pembiayaan" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords12 = $wpdb->query($sql_backup_data_pembiayaan);

								$return = array(
									'status' => 'success',
									'message'	=> 'Berhasil!',
									'data_input' => $queryRecords1
								);
							} else {
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						} else {
							$return = array(
								'status' => 'error',
								'message'	=> "Penjadwalan belum dimulai!",
							);
						}
					} else {
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}
				} else {
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit lock data jadwal RPJM */
	public function submit_lock_schedule_rpjm()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['id_jadwal_lokal'])) {
					if (in_array("administrator", $user_meta->roles)) {
						$id_jadwal_lokal = trim(htmlspecialchars($_POST['id_jadwal_lokal']));

						$data_this_id 	= $wpdb->get_row($wpdb->prepare('
							SELECT 
								* 
							FROM data_jadwal_lokal 
							WHERE id_jadwal_lokal = %d
						', $id_jadwal_lokal), ARRAY_A);

						$timezone = get_option('timezone_string');
						if (preg_match("/Asia/i", $timezone)) {
							date_default_timezone_set($timezone);
						} else {
							$return = array(
								'status' => 'error',
								'message'	=> "Pengaturan timezone salah. Pilih salah satu kota di zona waktu yang sama dengan anda, antara lain:  \'Jakarta\',\'Makasar\',\'Jayapura\'",
							);
							die(json_encode($return));
						}

						$dateTime = new DateTime();
						$time_now = $dateTime->format('Y-m-d H:i:s');
						if ($time_now > $data_this_id['waktu_awal']) {
							$status_check = array(0, NULL, 2);
							if (in_array($data_this_id['status'], $status_check)) {

								//lock data penjadwalan
								$wpdb->update('data_jadwal_lokal', array('waktu_akhir' => $time_now, 'status' => 1), array(
									'id_jadwal_lokal'	=> $id_jadwal_lokal
								));

								$delete_lokal_history = $this->delete_data_lokal_history('data_rpjmd_misi_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_1 = array('id_misi', 'id_misi_old', 'id_visi', 'is_locked', 'misi_teks', 'status', 'urut_misi', 'visi_lock', 'visi_teks', 'update_at', 'active', 'tahun_anggaran');

								$sql_backup_data_rpjmd_misi_lokal =  "INSERT INTO data_rpjmd_misi_lokal_history (" . implode(', ', $columns_1) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_1) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_rpjmd_misi_lokal";

								$queryRecords1 = $wpdb->query($sql_backup_data_rpjmd_misi_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_rpjmd_program_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_2 = array('id_misi', 'id_misi_old', 'id_program', 'id_unik', 'id_unik_indikator', 'id_unit', 'id_visi', 'indikator', 'is_locked', 'is_locked_indikator', 'kode_sasaran', 'kode_skpd', 'kode_tujuan', 'misi_teks', 'nama_program', 'nama_skpd', 'pagu_1', 'pagu_2', 'pagu_3', 'pagu_4', 'pagu_5', 'program_lock', 'sasaran_lock', 'sasaran_teks', 'satuan', 'status', 'target_1', 'target_2', 'target_3', 'target_4', 'target_5', 'target_akhir', 'target_awal', 'tujuan_lock', 'tujuan_teks', 'urut_misi', 'urut_sasaran', 'urut_tujuan', 'visi_teks', 'active', 'update_at', 'tahun_anggaran');

								$sql_backup_data_rpjmd_program_lokal =  "INSERT INTO data_rpjmd_program_lokal_history (" . implode(', ', $columns_2) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_2) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_rpjmd_program_lokal";

								$queryRecords2 = $wpdb->query($sql_backup_data_rpjmd_program_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_rpjmd_sasaran_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_3 = array('id_misi', 'id_misi_old', 'id_sasaran', 'id_unik', 'id_unik_indikator', 'id_visi', 'indikator_teks', 'is_locked', 'is_locked_indikator', 'kode_tujuan', 'misi_teks', 'sasaran_teks', 'satuan', 'status', 'target_1', 'target_2', 'target_3', 'target_4', 'target_5', 'target_akhir', 'target_awal', 'tujuan_lock', 'tujuan_teks', 'urut_misi', 'urut_sasaran', 'urut_tujuan', 'visi_teks', 'active', 'update_at', 'tahun_anggaran');

								$sql_backup_data_rpjmd_sasaran_lokal =  "INSERT INTO data_rpjmd_sasaran_lokal_history (" . implode(', ', $columns_3) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_3) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_rpjmd_sasaran_lokal";

								$queryRecords3 = $wpdb->query($sql_backup_data_rpjmd_sasaran_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_rpjmd_tujuan_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_4 = array('id_misi', 'id_misi_old', 'id_tujuan', 'id_unik', 'id_unik_indikator', 'id_visi', 'indikator_teks', 'is_locked', 'is_locked_indikator', 'misi_lock', 'misi_teks', 'satuan', 'status', 'target_1', 'target_2', 'target_3', 'target_4', 'target_5', 'target_akhir', 'target_awal', 'tujuan_teks', 'urut_misi', 'urut_tujuan', 'visi_teks', 'active', 'update_at', 'tahun_anggaran');

								$sql_backup_data_rpjmd_tujuan_lokal =  "INSERT INTO data_rpjmd_tujuan_lokal_history (" . implode(', ', $columns_4) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_4) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_rpjmd_tujuan_lokal";

								$queryRecords4 = $wpdb->query($sql_backup_data_rpjmd_tujuan_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_rpjmd_visi_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_5 = array('id_visi', 'is_locked', 'status', 'visi_teks', 'update_at', 'active', 'tahun_anggaran');

								$sql_backup_data_rpjmd_visi_lokal =  "INSERT INTO data_rpjmd_visi_lokal_history (" . implode(', ', $columns_5) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_5) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_rpjmd_visi_lokal";

								$queryRecords5 = $wpdb->query($sql_backup_data_rpjmd_visi_lokal);

								$return = array(
									'status' => 'success',
									'message'	=> 'Berhasil!',
									'data_input' => $queryRecords1
								);
							} else {
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						} else {
							$return = array(
								'status' => 'error',
								'message'	=> "Penjadwalan belum dimulai!",
							);
						}
					} else {
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}
				} else {
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit lock data jadwal RENSTRA */
	public function submit_lock_schedule_renstra()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['id_jadwal_lokal'])) {
					if (in_array("administrator", $user_meta->roles)) {
						$id_jadwal_lokal = trim(htmlspecialchars($_POST['id_jadwal_lokal']));

						$data_this_id 	= $wpdb->get_row($wpdb->prepare('
							SELECT 
								* 
							FROM data_jadwal_lokal 
							WHERE id_jadwal_lokal = %d
						', $id_jadwal_lokal), ARRAY_A);

						$timezone = get_option('timezone_string');
						if (preg_match("/Asia/i", $timezone)) {
							date_default_timezone_set($timezone);
						} else {
							$return = array(
								'status' => 'error',
								'message'	=> "Pengaturan timezone salah. Pilih salah satu kota di zona waktu yang sama dengan anda, antara lain:  \'Jakarta\',\'Makasar\',\'Jayapura\'",
							);
							die(json_encode($return));
						}

						$dateTime = new DateTime();
						$time_now = $dateTime->format('Y-m-d H:i:s');
						if ($time_now > $data_this_id['waktu_awal']) {
							$status_check = array(0, NULL, 2);
							if (in_array($data_this_id['status'], $status_check)) {

								$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
								$this->check_total_pagu_penetapan([
									'lama_pelaksanaan' => $data_this_id['lama_pelaksanaan'],
									'tahun_anggaran' => $tahun_anggaran
								]);

								//lock data penjadwalan
								$wpdb->update('data_jadwal_lokal', array('waktu_akhir' => $time_now, 'status' => 1), array(
									'id_jadwal_lokal'	=> $id_jadwal_lokal
								));

								$delete_lokal_history = $this->delete_data_lokal_history('data_renstra_sub_kegiatan_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_0 = array('bidur_lock', 'giat_lock', 'id_bidang_urusan', 'id_sub_giat', 'id_giat', 'id_misi', 'id_program', 'id_unik', 'id_unik_indikator', 'id_unit', 'id_sub_unit', 'id_visi', 'id_indikator', 'indikator', 'id_indikator_usulan', 'indikator_usulan', 'is_locked', 'is_locked_indikator', 'kode_bidang_urusan', 'kode_sub_giat', 'kode_giat', 'kode_program', 'kode_sasaran', 'kode_skpd', 'kode_tujuan', 'kode_unik_program', 'nama_bidang_urusan', 'nama_sub_giat', 'nama_giat', 'nama_program', 'nama_skpd', 'nama_sub_unit', 'pagu_1', 'pagu_2', 'pagu_3', 'pagu_4', 'pagu_5', 'pagu_1_usulan', 'pagu_2_usulan', 'pagu_3_usulan', 'pagu_4_usulan', 'pagu_5_usulan', 'program_lock', 'renstra_prog_lock', 'sasaran_lock', 'sasaran_teks', 'satuan', 'status', 'target_1', 'target_2', 'target_3', 'target_4', 'target_5', 'target_akhir', 'target_awal', 'satuan_usulan', 'target_1_usulan', 'target_2_usulan', 'target_3_usulan', 'target_4_usulan', 'target_5_usulan', 'target_akhir_usulan', 'target_awal_usulan', 'catatan_usulan', 'catatan', 'tujuan_lock', 'tujuan_teks', 'urut_sasaran', 'urut_tujuan', 'active', 'update_at', 'tahun_anggaran', 'kode_kegiatan', 'id_sub_giat_lama');

								$sql_backup_data_renstra_sub_kegiatan_lokal =  "INSERT INTO data_renstra_sub_kegiatan_lokal_history (" . implode(', ', $columns_0) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_0) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_renstra_sub_kegiatan_lokal";

								$queryRecords0 = $wpdb->query($sql_backup_data_renstra_sub_kegiatan_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_renstra_kegiatan_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_1 = array('bidur_lock', 'giat_lock', 'id_bidang_urusan', 'id_giat', 'id_misi', 'id_program', 'id_unik', 'id_unik_indikator', 'id_unit', 'id_visi', 'indikator', 'indikator_usulan', 'is_locked', 'is_locked_indikator', 'kode_bidang_urusan', 'kode_giat', 'kode_program', 'kode_sasaran', 'kode_skpd', 'kode_tujuan', 'kode_unik_program', 'nama_bidang_urusan', 'nama_giat', 'nama_program', 'nama_skpd', 'pagu_1', 'pagu_2', 'pagu_3', 'pagu_4', 'pagu_5', 'pagu_1_usulan', 'pagu_2_usulan', 'pagu_3_usulan', 'pagu_4_usulan', 'pagu_5_usulan', 'program_lock', 'renstra_prog_lock', 'sasaran_lock', 'sasaran_teks', 'satuan', 'status', 'target_1', 'target_2', 'target_3', 'target_4', 'target_5', 'target_akhir', 'target_awal', 'satuan_usulan', 'target_1_usulan', 'target_2_usulan', 'target_3_usulan', 'target_4_usulan', 'target_5_usulan', 'target_akhir_usulan', 'target_awal_usulan', 'catatan_usulan', 'catatan', 'tujuan_lock', 'tujuan_teks', 'urut_sasaran', 'urut_tujuan', 'active', 'update_at', 'tahun_anggaran');

								$sql_backup_data_renstra_kegiatan_lokal =  "INSERT INTO data_renstra_kegiatan_lokal_history (" . implode(', ', $columns_1) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_1) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_renstra_kegiatan_lokal";

								$queryRecords1 = $wpdb->query($sql_backup_data_renstra_kegiatan_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_renstra_program_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_2 = array('bidur_lock', 'id_bidang_urusan', 'id_misi', 'id_program', 'id_unik', 'id_unik_indikator', 'id_unit', 'id_visi', 'indikator', 'indikator_usulan', 'is_locked', 'is_locked_indikator', 'kode_bidang_urusan', 'kode_program', 'kode_sasaran', 'kode_skpd', 'kode_tujuan', 'nama_bidang_urusan', 'nama_program', 'nama_skpd', 'pagu_1', 'pagu_2', 'pagu_3', 'pagu_4', 'pagu_5', 'pagu_1_usulan', 'pagu_2_usulan', 'pagu_3_usulan', 'pagu_4_usulan', 'pagu_5_usulan', 'program_lock', 'sasaran_lock', 'sasaran_teks', 'satuan', 'status', 'target_1', 'target_2', 'target_3', 'target_4', 'target_5', 'target_akhir', 'target_awal', 'satuan_usulan', 'target_1_usulan', 'target_2_usulan', 'target_3_usulan', 'target_4_usulan', 'target_5_usulan', 'target_akhir_usulan', 'target_awal_usulan', 'catatan_usulan', 'catatan', 'tujuan_lock', 'tujuan_teks', 'urut_sasaran', 'urut_tujuan', 'active', 'update_at', 'tahun_anggaran');

								$sql_backup_data_renstra_program_lokal =  "INSERT INTO data_renstra_program_lokal_history (" . implode(', ', $columns_2) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_2) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_renstra_program_lokal";

								$queryRecords2 = $wpdb->query($sql_backup_data_renstra_program_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_renstra_sasaran_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_3 = array('bidur_lock', 'id_bidang_urusan', 'id_misi', 'id_unit', 'id_unik_indikator', 'id_unik', 'id_visi', 'indikator_teks', 'indikator_teks_usulan', 'is_locked', 'is_locked_indikator', 'kode_bidang_urusan', 'kode_skpd', 'kode_tujuan', 'nama_bidang_urusan', 'nama_skpd', 'sasaran_teks', 'satuan', 'status', 'target_1', 'target_2', 'target_3', 'target_4', 'target_5', 'target_akhir', 'target_awal', 'satuan_usulan', 'target_1_usulan', 'target_2_usulan', 'target_3_usulan', 'target_4_usulan', 'target_5_usulan', 'target_akhir_usulan', 'target_awal_usulan', 'catatan_usulan', 'catatan', 'tujuan_lock', 'tujuan_teks', 'urut_sasaran', 'urut_tujuan', 'active', 'update_at', 'tahun_anggaran');

								$sql_backup_data_renstra_sasaran_lokal =  "INSERT INTO data_renstra_sasaran_lokal_history (" . implode(', ', $columns_3) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_3) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_renstra_sasaran_lokal";

								$queryRecords3 = $wpdb->query($sql_backup_data_renstra_sasaran_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_renstra_tujuan_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_4 = array('bidur_lock', 'id_bidang_urusan', 'id_unik', 'id_unik_indikator', 'id_unit', 'indikator_teks', 'indikator_teks_usulan', 'is_locked', 'is_locked_indikator', 'kode_bidang_urusan', 'kode_sasaran_rpjm', 'kode_skpd', 'nama_bidang_urusan', 'nama_skpd', 'satuan', 'status', 'target_1', 'target_2', 'target_3', 'target_4', 'target_5', 'target_akhir', 'target_awal', 'satuan_usulan', 'target_1_usulan', 'target_2_usulan', 'target_3_usulan', 'target_4_usulan', 'target_5_usulan', 'target_akhir_usulan', 'target_awal_usulan', 'catatan_usulan', 'catatan', 'catatan_tujuan', 'tujuan_teks', 'urut_tujuan', 'active', 'update_at', 'tahun_anggaran');

								$sql_backup_data_renstra_tujuan_lokal =  "INSERT INTO data_renstra_tujuan_lokal_history (" . implode(', ', $columns_4) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_4) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_renstra_tujuan_lokal";

								$queryRecords4 = $wpdb->query($sql_backup_data_renstra_tujuan_lokal);

								$return = array(
									'status' => 'success',
									'message'	=> 'Berhasil!',
									'data_input' => $queryRecords1
								);
							} else {
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						} else {
							$return = array(
								'status' => 'error',
								'message'	=> "Penjadwalan belum dimulai!",
							);
						}
					} else {
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}
				} else {
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit lock data jadwal RPD */
	public function submit_lock_schedule_rpd()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['id_jadwal_lokal'])) {
					if (in_array("administrator", $user_meta->roles)) {
						$id_jadwal_lokal = trim(htmlspecialchars($_POST['id_jadwal_lokal']));

						$data_this_id 	= $wpdb->get_row($wpdb->prepare('
							SELECT 
								* 
							FROM data_jadwal_lokal 
							WHERE id_jadwal_lokal = %d
						', $id_jadwal_lokal), ARRAY_A);

						$timezone = get_option('timezone_string');
						if (preg_match("/Asia/i", $timezone)) {
							date_default_timezone_set($timezone);
						} else {
							$return = array(
								'status' => 'error',
								'message'	=> "Pengaturan timezone salah. Pilih salah satu kota di zona waktu yang sama dengan anda, antara lain:  \'Jakarta\',\'Makasar\',\'Jayapura\'",
							);
							die(json_encode($return));
						}

						$dateTime = new DateTime();
						$time_now = $dateTime->format('Y-m-d H:i:s');
						if ($time_now > $data_this_id['waktu_awal']) {
							$status_check = array(0, NULL, 2);
							if (in_array($data_this_id['status'], $status_check)) {
								//lock data penjadwalan
								$wpdb->update('data_jadwal_lokal', array('waktu_akhir' => $time_now, 'status' => 1), array(
									'id_jadwal_lokal'	=> $id_jadwal_lokal
								));

								$delete_tujuan_lokal_history = $this->delete_data_lokal_history('data_rpd_tujuan_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_1 = array('head_teks', 'id_misi_old', 'id_tujuan', 'id_unik', 'id_unik_indikator', 'indikator_teks', 'is_locked', 'is_locked_indikator', 'isu_teks', 'kebijakan_teks', 'misi_lock', 'misi_teks', 'saspok_teks', 'satuan', 'status', 'target_1', 'target_2', 'target_3', 'target_4', 'target_5', 'target_akhir', 'target_awal', 'tujuan_teks', 'urut_misi', 'urut_saspok', 'urut_tujuan', 'visi_teks', 'id_isu', 'update_at', 'no_urut', 'catatan_teks_tujuan', 'indikator_catatan_teks', 'active');

								$sql_backup_data_rpd_tujuan_lokal =  "INSERT INTO data_rpd_tujuan_lokal_history (" . implode(', ', $columns_1) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_1) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_rpd_tujuan_lokal";

								$queryRecords1 = $wpdb->query($sql_backup_data_rpd_tujuan_lokal);

								$delete_sasaran_lokal_history = $this->delete_data_lokal_history('data_rpd_sasaran_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_2 = array('head_teks', 'id_misi_old', 'id_sasaran', 'id_unik', 'id_unik_indikator', 'indikator_teks', 'is_locked', 'is_locked_indikator', 'isu_teks', 'kebijakan_teks', 'kode_tujuan', 'misi_lock', 'misi_teks', 'sasaran_teks', 'saspok_teks', 'satuan', 'status', 'target_1', 'target_2', 'target_3', 'target_4', 'target_5', 'target_akhir', 'target_awal', 'tujuan_lock', 'tujuan_teks', 'urut_misi', 'urut_sasaran', 'urut_saspok', 'urut_tujuan', 'visi_teks', 'update_at', 'sasaran_no_urut', 'sasaran_catatan', 'indikator_catatan_teks', 'active');

								$sql_backup_data_rpd_sasaran_lokal =  "INSERT INTO data_rpd_sasaran_lokal_history (" . implode(', ', $columns_2) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_2) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_rpd_sasaran_lokal";

								$queryRecords2 = $wpdb->query($sql_backup_data_rpd_sasaran_lokal);

								$delete_rpd_program_lokal_history = $this->delete_data_lokal_history('data_rpd_program_lokal', $data_this_id['id_jadwal_lokal']);

								$columns_3 = array('head_teks', 'id_bidur_mth', 'id_misi_old', 'id_program', 'id_program_mth', 'id_unik', 'id_unik_indikator', 'id_unit', 'indikator', 'is_locked', 'is_locked_indikator', 'isu_teks', 'kebijakan_teks', 'kode_sasaran', 'kode_skpd', 'kode_tujuan', 'misi_lock', 'misi_teks', 'nama_program', 'nama_skpd', 'pagu_1', 'pagu_2', 'pagu_3', 'pagu_4', 'pagu_5', 'program_lock', 'sasaran_lock', 'sasaran_teks', 'saspok_teks', 'satuan', 'status', 'target_1', 'target_2', 'target_3', 'target_4', 'target_5', 'target_akhir', 'target_awal', 'tujuan_lock', 'tujuan_teks', 'urut_misi', 'urut_sasaran', 'urut_saspok', 'urut_tujuan', 'visi_teks', 'catatan', 'update_at', 'active');

								$sql_backup_data_rpd_program_lokal =  "INSERT INTO data_rpd_program_lokal_history (" . implode(', ', $columns_3) . ",id_jadwal,id_asli)
											SELECT " . implode(', ', $columns_3) . ", " . $data_this_id['id_jadwal_lokal'] . ", id as id_asli
											FROM data_rpd_program_lokal";

								$queryRecords3 = $wpdb->query($sql_backup_data_rpd_program_lokal);

								$return = array(
									'status' => 'success',
									'message'	=> 'Berhasil!',
									'data_input' => $queryRecords1
								);
							} else {
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						} else {
							$return = array(
								'status' => 'error',
								'message'	=> "Penjadwalan belum dimulai!",
							);
						}
					} else {
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}
				} else {
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit lock data jadwal verifikasi data rka */
	public function submit_lock_schedule_verif_rka()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['id_jadwal_lokal'])) {
					if (in_array("administrator", $user_meta->roles)) {
						$id_jadwal_lokal = $_POST['id_jadwal_lokal'];

						$data_this_id = $wpdb->get_row($wpdb->prepare('
							SELECT 
								j.*,
								t.nama_tipe 
							FROM data_jadwal_lokal j
							INNER JOIN data_tipe_perencanaan t on t.id=j.id_tipe 
							WHERE j.id_jadwal_lokal = %d
						', $id_jadwal_lokal), ARRAY_A);
						if (empty($data_this_id)) {
							$return = array(
								'status' => 'error',
								'message'	=> "Jadwal dengan ID $id_jadwal_lokal tidak ditemukan!",
							);
							die(json_encode($return));
						}

						$timezone = get_option('timezone_string');
						if (preg_match("/Asia/i", $timezone)) {
							date_default_timezone_set($timezone);
						} else {
							$return = array(
								'status' => 'error',
								'message'	=> "Pengaturan timezone salah. Pilih salah satu kota di zona waktu yang sama dengan anda, antara lain:  \'Jakarta\',\'Makasar\',\'Jayapura\'",
							);
							die(json_encode($return));
						}

						$dateTime = new DateTime();
						$time_now = $dateTime->format('Y-m-d H:i:s');
						if ($time_now > $data_this_id['waktu_awal']) {
							$status_check = array(0, NULL, 2);
							if (in_array($data_this_id['status'], $status_check)) {
								$prefix = '';
								if (strpos($data_this_id['nama_tipe'], '_sipd') == false) {
									$prefix = '_lokal';
								}

								//lock data penjadwalan
								$queryRecords = $wpdb->update('data_jadwal_lokal', array('waktu_akhir' => $time_now, 'status' => 1), array(
									'id_jadwal_lokal'	=> $id_jadwal_lokal
								));

								/** Copy data ke tabel history */
								$delete_lokal_history = $this->delete_data_lokal_history('data_validasi_verifikasi_rka' . $prefix, $data_this_id['id_jadwal_lokal']);

								$oclumns_1 = array(
									'id_user',
									'kode_sbl',
									'nama_bidang',
									'tahun_anggaran',
									'update_at'
								);

								$sql_backup_data_validasi_verifikasi_rka =  "
									INSERT INTO data_validasi_verifikasi_rka" . $prefix . "_history (" . implode(', ', $oclumns_1) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $oclumns_1) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_validasi_verifikasi_rka" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords1 = $wpdb->query($sql_backup_data_validasi_verifikasi_rka);

								/** -- */
								$delete_lokal_history = $this->delete_data_lokal_history('data_verifikasi_rka' . $prefix, $data_this_id['id_jadwal_lokal']);

								$oclumns_2 = array(
									'kode_sbl',
									'tahun_anggaran',
									'id_user',
									'nama_verifikator',
									'fokus_uraian',
									'catatan_verifikasi',
									'tanggapan_opd',
									'update_at_tanggapan',
									'create_at',
									'update_at',
									'active'
								);

								$sql_backup_data_verifikasi_rka =  "
									INSERT INTO data_verifikasi_rka" . $prefix . "_history (" . implode(', ', $oclumns_2) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $oclumns_2) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_verifikasi_rka" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords2 = $wpdb->query($sql_backup_data_verifikasi_rka);

								/** -- */
								$delete_lokal_history = $this->delete_data_lokal_history('data_pptk_sub_keg' . $prefix, $data_this_id['id_jadwal_lokal']);

								$oclumns_3 = array(
									'id_user',
									'kode_sbl',
									'tahun_anggaran',
									'update_at',
									'active'
								);

								$sql_backup_data_set_pptk =  "
									INSERT INTO data_pptk_sub_keg" . $prefix . "_history (" . implode(', ', $oclumns_3) . ",id_asli,id_jadwal)
									SELECT 
										" . implode(', ', $oclumns_3) . ",
										id as id_asli,
										" . $data_this_id['id_jadwal_lokal'] . "
									FROM data_pptk_sub_keg" . $prefix . " 
									WHERE tahun_anggaran='" . $data_this_id['tahun_anggaran'] . "'
								";

								$queryRecords3 = $wpdb->query($sql_backup_data_set_pptk);

								$return = array(
									'status' => 'success',
									'message'	=> 'Berhasil!'
								);
							} else {
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						} else {
							$return = array(
								'status' => 'error',
								'message'	=> "Penjadwalan belum dimulai!",
							);
						}
					} else {
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}
				} else {
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function delete_data_lokal_history($nama_tabel = 'data_rpd_tujuan_lokal', $id_jadwal = 1)
	{
		global $wpdb;
		$return = array(
			'status' => 'error',
			'message'	=> 'Format tidak sesuai!'
		);

		$nama_tabel_history = $nama_tabel . "_history";

		$delete = $wpdb->delete($nama_tabel_history, array('id_jadwal' => $id_jadwal));
		if ($delete == false) {
			$return = array(
				'status' 	=> 'error',
				'message'	=> 'Delete error, harap hubungi admin!'
			);
		}

		return $return;
	}

	public function get_kontrak_addendum()
	{
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare(
					"
					SELECT 
						s.*
					from ta_kontrak_addendum s
					where s.tahun=%d
						and s.no_kontrak=%s
					",
					$tahun_anggaran,
					$_POST['no_kontrak']
				);
				$ret['sql'] = $sql;
				$kontrak = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				$ret['data'] = $kontrak;
			} else {
				$ret = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	public function run_sql_migrate()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil menjalankan SQL migrate!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$file = basename($_POST['file']);
				$ret['value'] = $file . ' (tgl: ' . date('Y-m-d H:i:s') . ')';
				if ($file == 'tabel.sql') {
					$path = WPSIPD_PLUGIN_PATH . '/' . $file;
				} else {
					$path = WPSIPD_PLUGIN_PATH . '/sql-migrate/' . $file;
				}
				if (file_exists($path)) {
					$sql = file_get_contents($path);
					$ret['sql'] = $sql;
					if ($file == 'tabel.sql') {
						require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
						$wpdb->hide_errors();
						$rows_affected = dbDelta($sql);
						if (empty($rows_affected)) {
							$ret['status'] = 'error';
							$ret['message'] = $wpdb->last_error;
						} else {
							$ret['message'] = implode(' | ', $rows_affected);
						}
					} else {
						$wpdb->hide_errors();
						$res = $wpdb->query($sql);
						if (empty($res)) {
							$ret['status'] = 'error';
							$ret['message'] = $wpdb->last_error;
						} else {
							$ret['message'] = $res;
						}
					}
					if ($ret['status'] == 'success') {
						$ret['version'] = $this->version;
						update_option('_last_update_sql_migrate', $ret['value']);
						update_option('_wp_sipd_db_version', $this->version);
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'File ' . $file . ' tidak ditemukan!';
				}
			} else {
				$ret = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	public function singkron_rpjmd_sipd_lokal()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil mengambil data RPJMD dari data SIPD lokal!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare("
					select 
						* 
					from data_rpjmd_visi
					where tahun_anggaran=%d
						and active=1
				", $tahun_anggaran);
				$visi_all = $wpdb->get_results($sql, ARRAY_A);
				foreach ($visi_all as $visi) {
					$table = 'data_rpjmd_visi_lokal';
					$id_cek = $wpdb->get_var("
						SELECT id from $table 
						where visi_teks='{$visi['visi_teks']}'");
					$data = array(
						'id_visi' => $visi['id_visi'],
						'is_locked' => $visi['is_locked'],
						'status' => $visi['status'],
						'visi_teks' => $visi['visi_teks'],
						'update_at' => $visi['update_at']
					);
					if (!empty($id_cek)) {
						$wpdb->update($table, $data, array('id' => $id_cek));
					} else {
						$wpdb->insert($table, $data);
					}
					$sql = $wpdb->prepare("
						select 
							* 
						from data_rpjmd_misi
						where tahun_anggaran=%d
							and id_visi=%s
							and active=1
					", $tahun_anggaran, $visi['id_visi']);
					$misi_all = $wpdb->get_results($sql, ARRAY_A);
					foreach ($misi_all as $misi) {
						$table = 'data_rpjmd_misi_lokal';
						$id_cek = $wpdb->get_var("
							SELECT id from $table 
							where misi_teks='{$misi['misi_teks']}' 
								and visi_teks='{$misi['visi_teks']}'
						");
						$data = array(
							'id_misi' => $misi['id_misi'],
							'id_misi_old' => $misi['id_misi_old'],
							'id_visi' => $misi['id_visi'],
							'is_locked' => $misi['is_locked'],
							'misi_teks' => $misi['misi_teks'],
							'status' => $misi['status'],
							'urut_misi' => $misi['urut_misi'],
							'visi_lock' => $misi['visi_lock'],
							'visi_teks' => $misi['visi_teks'],
							'update_at' => $misi['update_at']
						);
						if (!empty($id_cek)) {
							$wpdb->update($table, $data, array('id' => $id_cek));
						} else {
							$wpdb->insert($table, $data);
						}
						$sql = $wpdb->prepare("
							select 
								* 
							from data_rpjmd_tujuan
							where tahun_anggaran=%d
								and id_misi=%s
								and active=1
						", $tahun_anggaran, $misi['id_misi']);
						$tujuan_all = $wpdb->get_results($sql, ARRAY_A);
						foreach ($tujuan_all as $tujuan) {
							$table = 'data_rpjmd_tujuan_lokal';
							$id_cek = $wpdb->get_var("
								SELECT id from $table 
								where tujuan_teks='{$tujuan['tujuan_teks']}' 
									and misi_teks='{$tujuan['misi_teks']}'
									and indikator_teks='{$tujuan['indikator_teks']}'
							");
							$data = array(
								'id_misi' => $tujuan['id_misi'],
								'id_misi_old' => $tujuan['id_misi_old'],
								'id_tujuan' => $tujuan['id_tujuan'],
								'id_unik' => $tujuan['id_unik'],
								'id_unik_indikator' => $tujuan['id_unik_indikator'],
								'id_visi' => $tujuan['id_visi'],
								'indikator_teks' => $tujuan['indikator_teks'],
								'is_locked' => $tujuan['is_locked'],
								'is_locked_indikator' => $tujuan['is_locked_indikator'],
								'misi_lock' => $tujuan['misi_lock'],
								'misi_teks' => $tujuan['misi_teks'],
								'satuan' => $tujuan['satuan'],
								'status' => $tujuan['status'],
								'target_1' => $tujuan['target_1'],
								'target_2' => $tujuan['target_2'],
								'target_3' => $tujuan['target_3'],
								'target_4' => $tujuan['target_4'],
								'target_5' => $tujuan['target_5'],
								'target_akhir' => $tujuan['target_akhir'],
								'target_awal' => $tujuan['target_awal'],
								'tujuan_teks' => $tujuan['tujuan_teks'],
								'urut_misi' => $tujuan['urut_misi'],
								'urut_tujuan' => $tujuan['urut_tujuan'],
								'visi_teks' => $tujuan['visi_teks'],
								'update_at' => $tujuan['update_at']
							);
							if (!empty($id_cek)) {
								$wpdb->update($table, $data, array('id' => $id_cek));
							} else {
								$wpdb->insert($table, $data);
							}
							$sql = $wpdb->prepare("
								select 
									* 
								from data_rpjmd_sasaran
								where tahun_anggaran=%d
									and kode_tujuan=%s
									and active=1
							", $tahun_anggaran, $tujuan['id_unik']);
							$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
							foreach ($sasaran_all as $sasaran) {
								$table = 'data_rpjmd_sasaran_lokal';
								$id_cek = $wpdb->get_var("
									SELECT id from $table 
									where sasaran_teks='{$sasaran['sasaran_teks']}' 
										and tujuan_teks='{$sasaran['tujuan_teks']}'
										and indikator_teks='{$sasaran['indikator_teks']}'
								");
								$data = array(
									'id_misi' => $sasaran['id_misi'],
									'id_misi_old' => $sasaran['id_misi_old'],
									'id_sasaran' => $sasaran['id_sasaran'],
									'id_unik' => $sasaran['id_unik'],
									'id_unik_indikator' => $sasaran['id_unik_indikator'],
									'id_visi' => $sasaran['id_visi'],
									'indikator_teks' => $sasaran['indikator_teks'],
									'is_locked' => $sasaran['is_locked'],
									'is_locked_indikator' => $sasaran['is_locked_indikator'],
									'kode_tujuan' => $sasaran['kode_tujuan'],
									'misi_teks' => $sasaran['misi_teks'],
									'sasaran_teks' => $sasaran['sasaran_teks'],
									'satuan' => $sasaran['satuan'],
									'status' => $sasaran['status'],
									'target_1' => $sasaran['target_1'],
									'target_2' => $sasaran['target_2'],
									'target_3' => $sasaran['target_3'],
									'target_4' => $sasaran['target_4'],
									'target_5' => $sasaran['target_5'],
									'target_akhir' => $sasaran['target_akhir'],
									'target_awal' => $sasaran['target_awal'],
									'tujuan_lock' => $sasaran['tujuan_lock'],
									'tujuan_teks' => $sasaran['tujuan_teks'],
									'urut_misi' => $sasaran['urut_misi'],
									'urut_sasaran' => $sasaran['urut_sasaran'],
									'urut_tujuan' => $sasaran['urut_tujuan'],
									'visi_teks' => $sasaran['visi_teks'],
									'update_at' => $sasaran['update_at']
								);
								if (!empty($id_cek)) {
									$wpdb->update($table, $data, array('id' => $id_cek));
								} else {
									$wpdb->insert($table, $data);
								}
								$sql = $wpdb->prepare("
									select 
										* 
									from data_rpjmd_program
									where tahun_anggaran=%d
										and kode_sasaran=%s
										and active=1
								", $tahun_anggaran, $sasaran['id_unik']);
								$program_all = $wpdb->get_results($sql, ARRAY_A);
								foreach ($program_all as $program) {
									$table = 'data_rpjmd_program_lokal';
									$id_cek = $wpdb->get_var("
										SELECT id from $table 
										where nama_program='{$program['nama_program']}' 
											and sasaran_teks='{$program['sasaran_teks']}'
											and indikator='{$program['indikator']}'
									");
									$data = array(
										'id_misi' => $program['id_misi'],
										'id_misi_old' => $program['id_misi_old'],
										'id_program' => $program['id_program'],
										'id_unik' => $program['id_unik'],
										'id_unik_indikator' => $program['id_unik_indikator'],
										'id_unit' => $program['id_unit'],
										'id_visi' => $program['id_visi'],
										'indikator' => $program['indikator'],
										'is_locked' => $program['is_locked'],
										'is_locked_indikator' => $program['is_locked_indikator'],
										'kode_sasaran' => $program['kode_sasaran'],
										'kode_skpd' => $program['kode_skpd'],
										'kode_tujuan' => $program['kode_tujuan'],
										'misi_teks' => $program['misi_teks'],
										'nama_program' => $program['nama_program'],
										'nama_skpd' => $program['nama_skpd'],
										'pagu_1' => $program['pagu_1'],
										'pagu_2' => $program['pagu_2'],
										'pagu_3' => $program['pagu_3'],
										'pagu_4' => $program['pagu_4'],
										'pagu_5' => $program['pagu_5'],
										'program_lock' => $program['program_lock'],
										'sasaran_lock' => $program['sasaran_lock'],
										'sasaran_teks' => $program['sasaran_teks'],
										'satuan' => $program['satuan'],
										'status' => $program['status'],
										'target_1' => $program['target_1'],
										'target_2' => $program['target_2'],
										'target_3' => $program['target_3'],
										'target_4' => $program['target_4'],
										'target_5' => $program['target_5'],
										'target_akhir' => $program['target_akhir'],
										'target_awal' => $program['target_awal'],
										'tujuan_lock' => $program['tujuan_lock'],
										'tujuan_teks' => $program['tujuan_teks'],
										'urut_misi' => $program['urut_misi'],
										'urut_sasaran' => $program['urut_sasaran'],
										'urut_tujuan' => $program['urut_tujuan'],
										'visi_teks' => $program['visi_teks'],
										'update_at' => $program['update_at']
									);
									if (!empty($id_cek)) {
										$wpdb->update($table, $data, array('id' => $id_cek));
									} else {
										$wpdb->insert($table, $data);
									}
								}
							}
						}
					}
				}
			} else {
				$ret = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	public function singkron_rpd_sipd_lokal()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil mengambil data RPD dari data SIPD lokal!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$sql = "
					select 
						* 
					from data_rpd_tujuan
				";
				$tujuan_all = $wpdb->get_results($sql, ARRAY_A);
				$id_unik = $this->generateRandomString();
				foreach ($tujuan_all as $tujuan) {
					$table = 'data_rpd_tujuan_lokal';
					$id_cek = $wpdb->get_var("
						SELECT id from $table 
						where tujuan_teks='{$tujuan['tujuan_teks']}' 
							and misi_teks='{$tujuan['misi_teks']}'
							and indikator_teks='{$tujuan['indikator_teks']}'
					");
					$data = array(
						'head_teks' => $tujuan['head_teks'],
						'id_misi_old' => $tujuan['id_misi_old'],
						'id_tujuan' => $tujuan['id_tujuan'],
						'id_unik' => $id_unik,
						'id_unik_indikator' => $this->generateRandomString(),
						'indikator_teks' => $tujuan['indikator_teks'],
						'is_locked' => $tujuan['is_locked'],
						'is_locked_indikator' => $tujuan['is_locked_indikator'],
						'isu_teks' => $tujuan['isu_teks'],
						'kebijakan_teks' => $tujuan['kebijakan_teks'],
						'misi_lock' => $tujuan['misi_lock'],
						'misi_teks' => $tujuan['misi_teks'],
						'saspok_teks' => $tujuan['saspok_teks'],
						'satuan' => $tujuan['satuan'],
						'status' => $tujuan['status'],
						'target_1' => $tujuan['target_1'],
						'target_2' => $tujuan['target_2'],
						'target_3' => $tujuan['target_3'],
						'target_4' => $tujuan['target_4'],
						'target_5' => $tujuan['target_5'],
						'target_akhir' => $tujuan['target_akhir'],
						'target_awal' => $tujuan['target_awal'],
						'tujuan_teks' => $tujuan['tujuan_teks'],
						'urut_misi' => $tujuan['urut_misi'],
						'urut_saspok' => $tujuan['urut_saspok'],
						'urut_tujuan' => $tujuan['urut_tujuan'],
						'visi_teks' => $tujuan['visi_teks'],
						'update_at' => $tujuan['update_at'],
						'active' => 1
					);
					if (!empty($id_cek)) {
						$wpdb->update($table, $data, array('id' => $id_cek));
					} else {
						$wpdb->insert($table, $data);
					}
					$sql = $wpdb->prepare("
						select 
							* 
						from data_rpd_sasaran
						where kode_tujuan=%s
					", $tujuan['id_unik']);
					$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
					$id_unik = $this->generateRandomString();
					foreach ($sasaran_all as $sasaran) {
						$table = 'data_rpd_sasaran_lokal';
						$id_cek = $wpdb->get_var("
							SELECT id from $table 
							where sasaran_teks='{$sasaran['sasaran_teks']}' 
								and tujuan_teks='{$sasaran['tujuan_teks']}'
								and indikator_teks='{$sasaran['indikator_teks']}'
						");
						$data = array(
							'head_teks' => $sasaran['head_teks'],
							'id_misi_old' => $sasaran['id_misi_old'],
							'id_sasaran' => $sasaran['id_sasaran'],
							'id_unik' => $id_unik,
							'id_unik_indikator' => $this->generateRandomString(),
							'indikator_teks' => $sasaran['indikator_teks'],
							'is_locked' => $sasaran['is_locked'],
							'is_locked_indikator' => $sasaran['is_locked_indikator'],
							'isu_teks' => $sasaran['isu_teks'],
							'kebijakan_teks' => $sasaran['kebijakan_teks'],
							'kode_tujuan' => $sasaran['kode_tujuan'],
							'misi_lock' => $sasaran['misi_lock'],
							'misi_teks' => $sasaran['misi_teks'],
							'sasaran_teks' => $sasaran['sasaran_teks'],
							'saspok_teks' => $sasaran['saspok_teks'],
							'satuan' => $sasaran['satuan'],
							'status' => $sasaran['status'],
							'target_1' => $sasaran['target_1'],
							'target_2' => $sasaran['target_2'],
							'target_3' => $sasaran['target_3'],
							'target_4' => $sasaran['target_4'],
							'target_5' => $sasaran['target_5'],
							'target_akhir' => $sasaran['target_akhir'],
							'target_awal' => $sasaran['target_awal'],
							'tujuan_lock' => $sasaran['tujuan_lock'],
							'tujuan_teks' => $sasaran['tujuan_teks'],
							'urut_misi' => $sasaran['urut_misi'],
							'urut_sasaran' => $sasaran['urut_sasaran'],
							'urut_saspok' => $sasaran['urut_saspok'],
							'urut_tujuan' => $sasaran['urut_tujuan'],
							'visi_teks' => $sasaran['visi_teks'],
							'update_at' => $sasaran['update_at'],
							'active' => 1
						);
						if (!empty($id_cek)) {
							$wpdb->update($table, $data, array('id' => $id_cek));
						} else {
							$wpdb->insert($table, $data);
						}
						$sql = $wpdb->prepare("
							select 
								* 
							from data_rpd_program
							where kode_sasaran=%s
						", $sasaran['id_unik']);
						$program_all = $wpdb->get_results($sql, ARRAY_A);
						$id_unik = $this->generateRandomString();
						foreach ($program_all as $program) {
							$table = 'data_rpd_program_lokal';
							$id_cek = $wpdb->get_var("
								SELECT id from $table 
								where nama_program='{$program['nama_program']}' 
									and sasaran_teks='{$program['sasaran_teks']}'
									and indikator='{$program['indikator']}'
							");
							$data = array(
								'head_teks' => $program['head_teks'],
								'id_bidur_mth' => $program['id_bidur_mth'],
								'id_misi_old' => $program['id_misi_old'],
								'id_program' => $program['id_program'],
								'id_program_mth' => $program['id_program_mth'],
								'id_unik' => $id_unik,
								'id_unik_indikator' => $this->generateRandomString(),
								'id_unit' => $program['id_unit'],
								'indikator' => $program['indikator'],
								'is_locked' => $program['is_locked'],
								'is_locked_indikator' => $program['is_locked_indikator'],
								'isu_teks' => $program['isu_teks'],
								'kebijakan_teks' => $program['kebijakan_teks'],
								'kode_sasaran' => $program['kode_sasaran'],
								'kode_skpd' => $program['kode_skpd'],
								'kode_tujuan' => $program['kode_tujuan'],
								'misi_lock' => $program['misi_lock'],
								'misi_teks' => $program['misi_teks'],
								'nama_program' => $program['nama_program'],
								'nama_skpd' => $program['nama_skpd'],
								'pagu_1' => $program['pagu_1'],
								'pagu_2' => $program['pagu_2'],
								'pagu_3' => $program['pagu_3'],
								'pagu_4' => $program['pagu_4'],
								'pagu_5' => $program['pagu_5'],
								'program_lock' => $program['program_lock'],
								'sasaran_lock' => $program['sasaran_lock'],
								'sasaran_teks' => $program['sasaran_teks'],
								'saspok_teks' => $program['saspok_teks'],
								'satuan' => $program['satuan'],
								'status' => $program['status'],
								'target_1' => $program['target_1'],
								'target_2' => $program['target_2'],
								'target_3' => $program['target_3'],
								'target_4' => $program['target_4'],
								'target_5' => $program['target_5'],
								'target_akhir' => $program['target_akhir'],
								'target_awal' => $program['target_awal'],
								'tujuan_lock' => $program['tujuan_lock'],
								'tujuan_teks' => $program['tujuan_teks'],
								'urut_misi' => $program['urut_misi'],
								'urut_sasaran' => $program['urut_sasaran'],
								'urut_saspok' => $program['urut_saspok'],
								'urut_tujuan' => $program['urut_tujuan'],
								'visi_teks' => $program['visi_teks'],
								'update_at' => $program['update_at'],
								'active' => 1
							);
							if (!empty($id_cek)) {
								$wpdb->update($table, $data, array('id' => $id_cek));
							} else {
								$wpdb->insert($table, $data);
							}
						}
					}
				}
			} else {
				$ret = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	function get_visi_rpjm()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil get visi RPJM!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$type = $_POST['type'];
				if ($type == 1) {
					$sql = $wpdb->prepare("
						select 
							* 
						from data_rpjmd_visi_lokal
						where is_locked=0
							AND status=1
							AND active=1
					");
					$ret['data'] = $wpdb->get_results($sql, ARRAY_A);
				} else {
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$sql = $wpdb->prepare("
						select 
							* 
						from data_rpjmd_visi
						where tahun_anggaran=%d
							and active=1
					", $tahun_anggaran);
					$ret['data'] = $wpdb->get_results($sql, ARRAY_A);
				}
			} else {
				$ret = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	function validasi_jadwal_perencanaan($tipe_perencanaan, $tahun_anggaran = 0)
	{
		global $wpdb;

		$data_return = array(
			'status' => 200,
			'message' => "Berhasil"
		);

		if (!empty($tipe_perencanaan)) {
			date_default_timezone_set("Asia/Bangkok");
			$dateTime = new DateTime();
			$time_now = $dateTime->format('Y-m-d H:i:s');

			$sql_tipe = $wpdb->get_results("SELECT * FROM `data_tipe_perencanaan` WHERE nama_tipe='" . $tipe_perencanaan . "'", ARRAY_A);

			$where_renja = '';
			$cek_renja_penganggaran = true;
			if ($sql_tipe[0]['id'] == 5 || $sql_tipe[0]['id'] == 6) {
				if (!empty($tahun_anggaran)) {
					$where_renja = ' AND tahun_anggaran=' . $tahun_anggaran;
				} else {
					$cek_renja_penganggaran = false;
				}
			}

			if ($cek_renja_penganggaran) {
				// get jadwal aktif dan terbuka
				$sql_jadwal_lokal = $wpdb->get_results("
					SELECT 
						* 
					FROM `data_jadwal_lokal` 
					WHERE status = 0 
						AND (
							waktu_awal < '" . $time_now . "' 
							AND waktu_akhir > '" . $time_now . "'
						) AND id_tipe='" . $sql_tipe[0]['id'] . "'" . $where_renja, ARRAY_A);

				if (!empty($sql_jadwal_lokal)) {
					$data_return = array(
						'status' 	=> 'success',
						'message'	=> "Berhasil",
						'data'		=> $sql_jadwal_lokal
					);
				} else {
					// get jadwal aktif
					$sql_jadwal_lokal = $wpdb->get_results("
						SELECT 
							* 
						FROM `data_jadwal_lokal` 
						WHERE status = 0 
							AND id_tipe='" . $sql_tipe[0]['id'] . "'" . $where_renja, ARRAY_A);
					if (empty($sql_jadwal_lokal)) {
						// get jadwal terakhir sesuai tipe
						$sql_jadwal_lokal = $wpdb->get_results("
							SELECT 
								* 
							FROM `data_jadwal_lokal` 
							WHERE id_tipe='" . $sql_tipe[0]['id'] . "'
							" . $where_renja . "
							ORDER BY id_jadwal_lokal desc
							LIMIT 1
						", ARRAY_A);
					}
					$data_return = array(
						'status' 	=> 'error',
						'message'	=> "Data terbuka tidak ditemukan.",
						'data'		=> $sql_jadwal_lokal
					);
				}
			} else {
				$data_return = array(
					'status' 	=> 'error',
					'message' 	=> "Gagal, Data Tahun Anggaran Tidak Ditemukan",
					'data'		=> ''
				);
			}
		} else {
			$data_return = array(
				'status' 	=> 'error',
				'message' 	=> "Gagal, tipe perencanaan tidak ada",
				'data'		=> ''
			);
		}

		return $data_return;
	}

	function get_last_jadwal_kunci($tipe_perencanaan, $tahun_anggaran = 0)
	{
		global $wpdb;

		$data_return = array(
			'status' => 200,
			'message' => "Berhasil"
		);

		if (!empty($tipe_perencanaan)) {
			date_default_timezone_set("Asia/Bangkok");
			$dateTime = new DateTime();
			$time_now = $dateTime->format('Y-m-d H:i:s');

			$sql_tipe = $wpdb->get_results("SELECT * FROM `data_tipe_perencanaan` WHERE nama_tipe='" . $tipe_perencanaan . "'", ARRAY_A);

			$where_renja = '';
			$cek_renja_penganggaran = true;
			if ($sql_tipe[0]['id'] == 5 || $sql_tipe[0]['id'] == 6) {
				if (!empty($tahun_anggaran)) {
					$where_renja = ' AND tahun_anggaran=' . $tahun_anggaran;
				} else {
					$cek_renja_penganggaran = false;
				}
			}

			if ($cek_renja_penganggaran) {
				// get jadwal aktif dan terbuka
				$sql_jadwal_lokal = $wpdb->get_row("
					SELECT 
						* 
					FROM `data_jadwal_lokal` 
					WHERE status = 1 
						AND id_tipe='" . $sql_tipe[0]['id'] . "'
						" . $where_renja . "
					ORDER BY id_jadwal_lokal DESC LIMIT 1", ARRAY_A);

				if (!empty($sql_jadwal_lokal)) {
					$data_return = array(
						'status' 	=> 'success',
						'message'	=> "Berhasil",
						'data'		=> $sql_jadwal_lokal
					);
				} else {
					$data_return = array(
						'status' 	=> 'error',
						'message'	=> "Data jadwal terkunci tidak ditemukan.",
						'data'		=> $sql_jadwal_lokal
					);
				}
			} else {
				$data_return = array(
					'status' 	=> 'error',
					'message' 	=> "Gagal, Data Tahun Anggaran Tidak Ditemukan",
					'data'		=> ''
				);
			}
		} else {
			$data_return = array(
				'status' 	=> 'error',
				'message' 	=> "Gagal, tipe perencanaan tidak ada",
				'data'		=> ''
			);
		}

		return $data_return;
	}

	function edit_visi_rpjm()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				$sql = $wpdb->prepare("
					SELECT * FROM data_rpjmd_visi_lokal
					WHERE id=%d
				", $_POST['id']);
				$visi = $wpdb->get_row($sql);

				echo json_encode([
					'status' => true,
					'data' => $visi,
					'message' => 'Sukses get visi by id'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	function submit_visi_rpjm()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				$data = json_decode(stripslashes($_POST['data']), true);

				if (empty($data['visi_teks'])) {
					echo json_encode([
						'status' => false,
						'message' => 'Visi tidak boleh kosong!'
					]);
					exit;
				}

				$sql = $wpdb->prepare("SELECT id FROM data_rpjmd_visi_lokal
						WHERE visi_teks=%s 
							AND is_locked=0
							AND status=1
							AND active=1", trim($data['visi_teks']));

				$id_cek = $wpdb->get_var($sql);

				if (!empty($id_cek)) {
					echo json_encode([
						'status' => false,
						'message' => 'Visi : ' . $data['visi_teks'] . ' sudah ada!'
					]);
					exit;
				}

				$wpdb->insert('data_rpjmd_visi_lokal', [
					'visi_teks' => $data['visi_teks'],
					'is_locked' => 0,
					'status' => 1,
					'active' => 1
				]);

				echo json_encode([
					'status' => true,
					'message' => 'Sukses simpan visi'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	function update_visi_rpjm()
	{

		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				$data = json_decode(stripslashes($_POST['data']), true);

				$data['type'] = 1;

				if (empty($data['visi_teks'])) {
					echo json_encode([
						'status' => false,
						'message' => 'Visi tidak boleh kosong!'
					]);
					exit;
				}

				$sql = $wpdb->prepare("SELECT id FROM data_rpjmd_visi_lokal
						WHERE visi_teks=%s
							AND id!=%d
							AND is_locked=0
							AND status=1
							AND active=1", trim($data['visi_teks']), $data['id_visi']);

				$id_cek = $wpdb->get_var($sql);

				if (!empty($id_cek)) {
					echo json_encode([
						'status' => false,
						'message' => 'Visi : ' . $data['visi_teks'] . ' sudah ada!'
					]);
					exit;
				}

				$wpdb->update('data_rpjmd_visi_lokal', [
					'visi_teks' => $data['visi_teks']
				], [
					'id' => $data['id_visi']
				]);

				echo json_encode([
					'status' => true,
					'message' => 'Sukses ubah visi'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	function delete_visi_rpjm()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_misi_lokal WHERE id_visi=%d AND is_locked=0 AND status=1 AND active=1", $_POST['id_visi']));

					if (!empty($id_cek)) {
						throw new Exception("Visi sudah digunakan oleh misi", 1);
					}

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_visi_lokal WHERE id=%d", $_POST['id_visi']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus visi'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function get_misi_rpjm()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				if ($_POST['type'] == 1) {

					$sql = $wpdb->prepare("
						SELECT * FROM data_rpjmd_misi_lokal
							WHERE id_visi=%d and active=1 ORDER BY urut_misi", $_POST['id_visi']);
					$misi = $wpdb->get_results($sql, ARRAY_A);
				} else {

					$tahun_anggaran = $_POST['tahun_anggaran'];

					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_rpjmd_misi
						WHERE tahun_anggaran=%d
								AND id_visi=%d
								AND active=1
						ORDER BY urut_misi
						", $tahun_anggaran, $_POST['id_visi']);
					$misi = $wpdb->get_results($sql, ARRAY_A);
				}

				echo json_encode([
					'status' => true,
					'data' => $misi,
					'message' => 'Sukses get detail visi dg data misi by id_visi'
				]);
				exit;
			}
			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}
		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	function submit_misi_rpjm()
	{

		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$data['type'] = 1;

					if (empty($data['id_visi'])) {
						throw new Exception('Visi wajib dipilih!');
					}

					if (empty($data['misi_teks'])) {
						throw new Exception('Misi tidak boleh kosong!');
					}

					if (empty($data['urut_misi'])) {
						throw new Exception('Urut misi tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_misi_lokal
							WHERE misi_teks=%s 
										AND id_visi=%d
										AND is_locked=0
										AND status=1
										AND active=1
								", trim($data['misi_teks']), $data['id_visi']));

					if (!empty($id_cek)) {
						throw new Exception('Misi : ' . $data['misi_teks'] . ' sudah ada!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $data['id_visi']));

					if (empty($dataVisi)) {
						throw new Exception('Visi yang dipilih tidak ditemukan!');
					}

					$wpdb->insert('data_rpjmd_misi_lokal', [
						'id_visi' => $dataVisi->id_visi,
						'visi_lock' => $dataVisi->visi_lock,
						'visi_teks' => $dataVisi->visi_teks,
						'misi_teks' => $data['misi_teks'],
						'urut_misi' => $data['urut_misi'],
						'is_locked' => 0,
						'status' => 1,
						'active' => 1
					]);

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan misi',
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function edit_misi_rpjm()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				$misi = $wpdb->get_row($wpdb->prepare("
					SELECT * FROM data_rpjmd_misi_lokal
						WHERE id=%d", $_POST['id']));

				echo json_encode([
					'status' => true,
					'misi' => $misi,
					'message' => 'Sukses get misi by id'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	function update_misi_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$data['type'] = 1;

					if (empty($data['id_visi'])) {
						throw new Exception('Visi wajib dipilih!');
					}

					if (empty($data['misi_teks'])) {
						throw new Exception('Misi tidak boleh kosong!');
					}

					if (empty($data['urut_misi'])) {
						throw new Exception('Urut misi tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_misi_lokal
							WHERE misi_teks=%s 
									AND id!=%d
									AND is_locked=0
									AND status=1
									AND active=1", trim($data['misi_teks']), $data['id_misi']));

					if (!empty($id_cek)) {
						throw new Exception('Misi : ' . $data['misi_teks'] . ' sudah ada!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $data['id_visi']));

					if (empty($dataVisi)) {
						throw new Exception('Visi yang dipilih tidak ditemukan!');
					}

					$wpdb->update('data_rpjmd_misi_lokal', [
						'id_visi' => $dataVisi->id_visi,
						'visi_lock' => $dataVisi->visi_lock,
						'visi_teks' => $dataVisi->visi_teks,
						'misi_teks' => $data['misi_teks'],
						'urut_misi' => $data['urut_misi'],
						'is_locked' => 0,
						'status' => 1,
						'active' => 1
					], [
						'id' => $data['id_misi']
					]);

					echo json_encode([
						'status' => true,
						'message' => 'Sukses ubah misi',
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function delete_misi_rpjm()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id_misi=%d AND is_locked=0 AND status=1 AND active=1", $_POST['id_misi']));

					if (!empty($id_cek)) {
						throw new Exception("Misi sudah digunakan oleh tujuan", 1);
					}

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_misi_lokal WHERE id=%d", $_POST['id_misi']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus misi'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function get_tujuan_rpjm()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				if ($_POST['type'] == 1) {

					$sql = $wpdb->prepare("
						SELECT * FROM data_rpjmd_tujuan_lokal
							WHERE id_misi=%d AND
								id_unik IS NOT NULL AND
								id_unik_indikator IS NULL AND
								status=1 AND 
								is_locked=0 AND 
								active=1 ORDER BY id", $_POST['id_misi']);
					$tujuan = $wpdb->get_results($sql, ARRAY_A);
				} else {

					$tahun_anggaran = $_POST['tahun_anggaran'];

					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_rpjmd_tujuan
						WHERE tahun_anggaran=%d
								AND id_misi=%d
								AND active=1
						ORDER BY urut_tujuan
						", $tahun_anggaran, $_POST['id_misi']);
					$tujuan = $wpdb->get_results($sql, ARRAY_A);
				}

				echo json_encode([
					'status' => true,
					'data' => $tujuan,
					'message' => 'Sukses get detail visi dg data tujuan by id_misi'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	function edit_tujuan_rpjm()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				$tujuan = $wpdb->get_row($wpdb->prepare("
					SELECT * FROM data_rpjmd_tujuan_lokal
						WHERE id=%d", $_POST['id_tujuan']));

				echo json_encode([
					'status' => true,
					'tujuan' => $tujuan,
					'message' => 'Sukses get tujuan by id'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	function submit_tujuan_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					if (empty($data['id_misi'])) {
						throw new Exception('Misi wajib dipilih!');
					}

					if (empty($data['tujuan_teks'])) {
						throw new Exception('Tujuan tidak boleh kosong!');
					}

					if (empty($data['urut_tujuan'])) {
						throw new Exception('Urut tujuan tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_tujuan_lokal
							WHERE tujuan_teks=%s
										AND id_misi=%d
										AND id_unik is not null
										AND id_unik_indikator is null
										AND is_locked=0
										AND status=1
										AND active=1
								", trim($data['tujuan_teks']), $data['id_misi']));

					if (!empty($id_cek)) {
						throw new Exception('Tujuan : ' . $data['tujuan_teks'] . ' sudah ada!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $data['id_misi']));

					if (empty($dataMisi)) {
						throw new Exception('Misi yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Visi dari misi yang dipilih tidak ditemukan!');
					}

					$status = $wpdb->insert('data_rpjmd_tujuan_lokal', [
						'id_misi' => $dataMisi->id_misi,
						'id_unik' => $this->generateRandomString(), // kode_tujuan
						'id_visi' => $dataMisi->id_visi,
						'misi_lock' => $dataMisi->misi_lock,
						'misi_teks' => $dataMisi->misi_teks,
						'status' => 1,
						'tujuan_teks' => $data['tujuan_teks'],
						'urut_misi' => $dataMisi->urut_misi,
						'urut_tujuan' => $data['urut_tujuan'],
						'visi_teks' => $dataVisi->visi_teks,
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'active' => 1
					]);

					if (!$status) {
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan tujuan',
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function update_tujuan_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					if (empty($data['id_misi'])) {
						throw new Exception('Misi wajib dipilih!');
					}

					if (empty($data['tujuan_teks'])) {
						throw new Exception('Tujuan tidak boleh kosong!');
					}

					if (empty($data['urut_tujuan'])) {
						throw new Exception('Urut tujuan tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_tujuan_lokal
							WHERE tujuan_teks=%s
										AND id!=%d
										AND id_misi=" . $data['id_misi'] . "
										AND id_unik is not null
										AND id_unik_indikator is null
										AND is_locked=0
										AND status=1
										AND active=1
								", trim($data['tujuan_teks']), $data['id_tujuan']));

					if (!empty($id_cek)) {
						throw new Exception('Tujuan : ' . $data['tujuan_teks'] . ' sudah ada!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $data['id_misi']));

					if (empty($dataMisi)) {
						throw new Exception('Misi yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Visi dari misi yang dipilih tidak ditemukan!');
					}

					$dataTujuan = [
						'id_misi' => $dataMisi->id_misi,
						'id_visi' => $dataMisi->id_visi,
						'misi_lock' => $dataMisi->misi_lock,
						'misi_teks' => $dataMisi->misi_teks,
						'tujuan_teks' => $data['tujuan_teks'],
						'urut_misi' => $dataMisi->urut_misi,
						'urut_tujuan' => $data['urut_tujuan'],
						'visi_teks' => $dataVisi->visi_teks
					];

					try {

						$wpdb->query('START TRANSACTION');

						// update tujuan
						$wpdb->update('data_rpjmd_tujuan_lokal', $dataTujuan, [
							'id' => $data['id_tujuan']
						]);

						// update tujuan di row indikator
						$wpdb->update('data_rpjmd_tujuan_lokal', $dataTujuan, [
							'id_tujuan' => $data['id_tujuan']
						]);

						$wpdb->query('COMMIT');

						echo json_encode([
							'status' => true,
							'message' => 'Sukses ubah tujuan rpjm',
						]);
						exit;
					} catch (Exception $e) {

						$wpdb->query('ROLLBACK');

						throw $e;
					}
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function delete_tujuan_rpjm()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_sasaran_lokal WHERE kode_tujuan=%s AND is_locked=0 AND status=1 AND active=1", $_POST['kode_tujuan']));

					if (!empty($id_cek)) {
						throw new Exception("Tujuan sudah digunakan oleh sasaran", 1);
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id_tujuan=%d AND id_unik IS NOT NULL AND id_unik_indikator IS NOT NULL AND is_locked=0 AND status=1 AND active=1", $_POST['id_tujuan']));

					if (!empty($id_cek)) {
						throw new Exception("Tujuan sudah digunakan oleh indikator tujuan", 1);
					}

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_tujuan_lokal WHERE id=%d", $_POST['id_tujuan']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus tujuan'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function get_indikator_tujuan_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					if ($_POST['type'] == 1) {
						$sql = $wpdb->prepare("
							SELECT * FROM data_rpjmd_tujuan_lokal 
								WHERE 
									id_tujuan=%d AND 
									id_unik IS NOT NULL AND 
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND 
									status=1 AND 
									active=1", $_POST['id_tujuan']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					} else {

						$tahun_anggaran = $_POST['tahun_anggaran'];
						$sql = $wpdb->prepare("
							SELECT 
								* 
							FROM data_rpjmd_tujuan
							WHERE tahun_anggaran=%d AND 
									id_tujuan=%d
									id_unik IS NOT NULL AND 
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND
									active=1
							ORDER BY urut_tujuan
							", $tahun_anggaran, $_POST['id_tujuan']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					}

					echo json_encode([
						'status' => true,
						'data' => $indikator,
						'message' => 'Sukses get indikator tujuan'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function submit_indikator_tujuan_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_tujuan_rpjm($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_tujuan_lokal
							WHERE indikator_teks=%s
										AND id_tujuan=%d
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								", trim($data['indikator_teks']), $data['id_tujuan']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks'] . ' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['id_tujuan']));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if (empty($dataMisi)) {
						throw new Exception('Misi yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Visi dari misi yang dipilih tidak ditemukan!');
					}

					$status = $wpdb->insert('data_rpjmd_tujuan_lokal', [
						'id_misi' => $dataMisi->id_misi,
						'id_tujuan' => $dataTujuan->id,
						'id_unik' => $dataTujuan->id_unik,
						'id_unik_indikator' => $this->generateRandomString(),
						'id_visi' => $dataMisi->id_visi,
						'indikator_teks' => $data['indikator_teks'],
						'misi_lock' => $dataMisi->misi_lock,
						'misi_teks' => $dataMisi->misi_teks,
						'satuan' => $data['satuan'],
						'status' => 1,
						'target_1' => $data['target_1'],
						'target_2' => $data['target_2'],
						'target_3' => $data['target_3'],
						'target_4' => $data['target_4'],
						'target_5' => $data['target_5'],
						'target_awal' => $data['target_awal'],
						'target_akhir' => $data['target_akhir'],
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_misi' => $dataMisi->urut_misi,
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'visi_teks' => $dataVisi->visi_teks,
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'active' => 1
					]);

					if (!$status) {
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator tujuan'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function edit_indikator_tujuan_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$indikator = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id=%d AND id_tujuan=%d AND id_unik IS NOT NULL AND id_unik_indikator IS NOT NULL AND is_locked_indikator=0 AND status=1 AND active=1", $_POST['id'], $_POST['id_tujuan']));

					echo json_encode([
						'status' => true,
						'data' => $indikator
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function update_indikator_tujuan_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_tujuan_rpjm($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_tujuan_lokal
							WHERE indikator_teks=%s
										AND id_tujuan=%d
										AND id!=%d
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								", trim($data['indikator_teks']), $data['id_tujuan'], $data['id']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks'] . ' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['id_tujuan']));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if (empty($dataMisi)) {
						throw new Exception('Misi yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Visi dari misi yang dipilih tidak ditemukan!');
					}

					$status = $wpdb->update('data_rpjmd_tujuan_lokal', [
						'indikator_teks' => $data['indikator_teks'],
						'misi_lock' => $dataMisi->misi_lock,
						'misi_teks' => $dataMisi->misi_teks,
						'satuan' => $data['satuan'],
						'target_1' => $data['target_1'],
						'target_2' => $data['target_2'],
						'target_3' => $data['target_3'],
						'target_4' => $data['target_4'],
						'target_5' => $data['target_5'],
						'target_awal' => $data['target_awal'],
						'target_akhir' => $data['target_akhir'],
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_misi' => $dataMisi->urut_misi,
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'visi_teks' => $dataVisi->visi_teks
					], [
						'id' => $data['id']
					]);

					if (!$status) {
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses ubah indikator tujuan'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function delete_indikator_tujuan_rpjm()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_tujuan_lokal WHERE id=%d AND id_unik_indikator IS NOT NULL AND active=1 AND status=1", $_POST['id']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator tujuan'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function verify_indikator_tujuan_rpjm(array $data)
	{
		if (empty($data['id_tujuan'])) {
			throw new Exception('Tujuan wajib dipilih!');
		}

		if (empty($data['indikator_teks'])) {
			throw new Exception('Indikator tujuan tidak boleh kosong!');
		}

		if (empty($data['satuan'])) {
			throw new Exception('Satuan indikator tujuan tidak boleh kosong!');
		}

		if (empty($data['target_1'])) {
			throw new Exception('Target Indikator tujuan tahun ke-1 tidak boleh kosong!');
		}

		if (empty($data['target_2'])) {
			throw new Exception('Target Indikator tujuan tahun ke-2 tidak boleh kosong!');
		}

		if (empty($data['target_3'])) {
			throw new Exception('Target Indikator tujuan tahun ke-3 tidak boleh kosong!');
		}

		if (empty($data['target_4'])) {
			throw new Exception('Target Indikator tujuan tahun ke-4 tidak boleh kosong!');
		}

		if (empty($data['target_5'])) {
			throw new Exception('Target Indikator tujuan tahun ke-5 tidak boleh kosong!');
		}

		if (empty($data['target_awal'])) {
			throw new Exception('Target awal Indikator tujuan tidak boleh kosong!');
		}

		if (empty($data['target_akhir'])) {
			throw new Exception('Target akhir Indikator tujuan tidak boleh kosong!');
		}
	}

	function get_sasaran_rpjm()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				if ($_POST['type'] == 1) {
					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_rpjmd_sasaran_lokal
						WHERE is_locked=0
							AND kode_tujuan='" . $_POST['kode_tujuan'] . "'
							AND id_unik IS NOT NULL
							AND id_unik_indikator IS NULL
							AND is_locked=0
							AND status=1
							AND active=1
					");
					$sasaran = $wpdb->get_results($sql, ARRAY_A);
				} else {
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$sql = $wpdb->prepare("
								SELECT 
									* 
								FROM data_rpjmd_sasaran
								WHERE tahun_anggaran=%d
									AND kode_tujuan=%s
									AND active=1
							", $tahun_anggaran, $_POST['kode_tujuan']);
					$sasaran = $wpdb->get_results($sql, ARRAY_A);
				}

				echo json_encode([
					'status' => true,
					'data' => $sasaran,
					'message' => 'Sukses get sasaran by tujuan'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	function submit_sasaran_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					if (empty($data['kode_tujuan'])) {
						throw new Exception('Tujuan wajib dipilih!');
					}

					if (empty($data['sasaran_teks'])) {
						throw new Exception('Sasaran tidak boleh kosong!');
					}

					if (empty($data['urut_sasaran'])) {
						throw new Exception('Urut sasaran tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT id FROM data_rpjmd_sasaran_lokal WHERE sasaran_teks=%s AND kode_tujuan=%s AND is_locked=0 AND status=1 AND active=1
					", $data['sasaran_teks'], $data['kode_tujuan']));

					if (!empty($id_cek)) {
						throw new Exception('Sasaran : ' . $data['sasaran_teks'] . ' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['kode_tujuan']));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if (empty($dataMisi)) {
						throw new Exception('Misi dari tujuan yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Visi dari tujuan yang dipilih tidak ditemukan! Mohon cek misi dari tujuan!');
					}

					$status = $wpdb->insert('data_rpjmd_sasaran_lokal', [
						'id_misi' => $dataMisi->id_misi,
						'id_unik' => $this->generateRandomString(), // kode_sasaran
						'id_visi' => $dataMisi->id_visi,
						'kode_tujuan' => $data['kode_tujuan'],
						'misi_teks' => $dataMisi->misi_teks,
						'sasaran_teks' => $data['sasaran_teks'],
						'status' => 1,
						'tujuan_lock' => $dataTujuan->tujuan_lock,
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_misi' => $dataMisi->urut_misi,
						'urut_sasaran' => $data['urut_sasaran'],
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'visi_teks' => $dataVisi->visi_teks,
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'active' => 1
					]);

					if (!$status) {
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan sasaran'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function edit_sasaran_rpjm()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				$sasaran = $wpdb->get_row($wpdb->prepare("
					SELECT *, (SELECT id FROM data_rpjmd_tujuan_lokal WHERE id_unik=data_rpjmd_sasaran_lokal.kode_tujuan AND id_unik_indikator is null and active=1 and status=1) id_tujuan FROM data_rpjmd_sasaran_lokal
						WHERE id=%d
							AND id_unik IS NOT NULL
							AND id_unik_indikator IS NULL
							AND is_locked=0
							AND active=1
							AND status=1
						", $_POST['id_sasaran']));

				echo json_encode([
					'status' => true,
					'data' => $sasaran,
					'message' => 'Sukses get sasaran by id'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	function update_sasaran_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					if (empty($data['kode_tujuan'])) {
						throw new Exception('Tujuan wajib dipilih!');
					}

					if (empty($data['sasaran_teks'])) {
						throw new Exception('Sasaran tidak boleh kosong!');
					}

					if (empty($data['urut_sasaran'])) {
						throw new Exception('Urut sasaran tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_sasaran_lokal
							WHERE sasaran_teks=%s
								AND kode_tujuan=%s
								AND id!=%s
								AND id_unik IS NOT NULL
								AND id_unik_indikator IS NULL
								AND is_locked=0
								AND status=1
								AND active=1
					", trim($data['sasaran_teks']), $data['kode_tujuan'], $data['id_sasaran']));

					if (!empty($id_cek)) {
						throw new Exception('Sasaran : ' . $data['sasaran_teks'] . ' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['kode_tujuan']));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if (empty($dataMisi)) {
						throw new Exception('Sasaran tidak terhubung ke Misi, mohon cek relasi antara tujuan dengan misi Rpjm!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Sasaran tidak terhubung ke Visi, mohon cek relasi antara tujuan dan misi Rpjm');
					}

					$dataSasaran = [
						'misi_teks' => $dataMisi->misi_teks,
						'sasaran_teks' => $data['sasaran_teks'],
						'tujuan_lock' => $dataTujuan->tujuan_lock,
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_misi' => $dataMisi->urut_misi,
						'urut_sasaran' => $data['urut_sasaran'],
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'visi_teks' => $dataVisi->visi_teks
					];

					try {
						$wpdb->query('START TRANSACTION');

						// ubah sasaran
						$wpdb->update('data_rpjmd_sasaran_lokal', $dataSasaran, [
							'id' => $data['id_sasaran']
						]);

						// ubah sasaran di row indikator
						$wpdb->update('data_rpjmd_sasaran_lokal', $dataSasaran, [
							'id_sasaran' => $data['id_sasaran']
						]);

						$wpdb->query('COMMIT');

						echo json_encode([
							'status' => true,
							'message' => 'Sukses ubah sasaran'
						]);
						exit;
					} catch (Exception $e) {

						$wpdb->query('ROLLBACK');

						throw $e;
					}
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function delete_sasaran_rpjm()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE kode_sasaran=%s AND is_locked=0 AND status=1 AND active=1", $_POST['kode_sasaran']));

					if (!empty($id_cek)) {
						throw new Exception("Sasaran sudah digunakan oleh program", 1);
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_sasaran_lokal WHERE id_sasaran=%d AND id_unik IS NOT NULL AND id_unik_indikator IS NOT NULL AND is_locked=0 AND status=1 AND active=1", $_POST['id_sasaran']));

					if (!empty($id_cek)) {
						throw new Exception("Sasaran sudah digunakan oleh indikator sasaran", 1);
					}

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_sasaran_lokal WHERE id=%d", $_POST['id_sasaran']));


					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus sasaran'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function get_indikator_sasaran_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					if ($_POST['type'] == 1) {
						$sql = $wpdb->prepare("
							SELECT * FROM data_rpjmd_sasaran_lokal 
								WHERE 
									id_sasaran=%d AND 
									id_unik IS NOT NULL AND 
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND 
									status=1 AND 
									active=1", $_POST['id_sasaran']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					} else {
						$tahun_anggaran = $_POST['tahun_anggaran'];
						$sql = $wpdb->prepare("
							SELECT 
								* 
							FROM data_rpjmd_sasaran
							WHERE tahun_anggaran=%d AND 
									id_sasaran=%d
									id_unik IS NOT NULL AND 
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND
									active=1
							ORDER BY urut_sasaran
							", $tahun_anggaran, $_POST['id_sasaran']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					}

					echo json_encode([
						'status' => true,
						'data' => $indikator,
						'message' => 'Sukses get indikator sasaran'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function submit_indikator_sasaran_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_sasaran_rpjm($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_sasaran_lokal
							WHERE indikator_teks=%s
										AND id_sasaran=%d
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								", trim($data['indikator_teks']), $data['id_sasaran']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks'] . ' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_sasaran_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['id_sasaran']));

					if (empty($dataSasaran)) {
						throw new Exception('Sasaran yang dipilih tidak ditemukan!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $dataSasaran->kode_tujuan));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan tidak terkoneksi dengan sasaran!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if (empty($dataMisi)) {
						throw new Exception('Misi terkoneksi dengan tujuan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Visi dari misi yang dipilih tidak ditemukan!');
					}

					$status = $wpdb->insert('data_rpjmd_sasaran_lokal', [
						'id_misi' => $dataMisi->id_misi,
						'id_sasaran' => $data['id_sasaran'],
						'id_unik' => $dataSasaran->id_unik, // kode_sasaran
						'id_unik_indikator' => $this->generateRandomString(),
						'id_visi' => $dataMisi->id_visi,
						'indikator_teks' => $data['indikator_teks'],
						'kode_tujuan' => $dataSasaran->kode_tujuan,
						'misi_teks' => $dataMisi->misi_teks,
						'sasaran_teks' => $dataSasaran->sasaran_teks,
						'satuan' => $data['satuan'],
						'status' => 1,
						'target_1' => $data['target_1'],
						'target_2' => $data['target_2'],
						'target_3' => $data['target_3'],
						'target_4' => $data['target_4'],
						'target_5' => $data['target_5'],
						'target_awal' => $data['target_awal'],
						'target_akhir' => $data['target_akhir'],
						'tujuan_lock' => $dataTujuan->is_locked,
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_misi' => $dataMisi->urut_misi,
						'urut_sasaran' => $dataSasaran->urut_sasaran,
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'visi_teks' => $dataVisi->visi_teks,
						'is_locked' => $dataSasaran->is_locked,
						'is_locked_indikator' => 0,
						'active' => 1
					]);

					if (!$status) {
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator sasaran'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function edit_indikator_sasaran_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$indikator = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_sasaran_lokal WHERE id=%d AND id_sasaran=%d AND id_unik IS NOT NULL AND id_unik_indikator IS NOT NULL AND is_locked_indikator=0 AND status=1 AND active=1", $_POST['id'], $_POST['id_sasaran']));

					echo json_encode([
						'status' => true,
						'data' => $indikator
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function update_indikator_sasaran_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_sasaran_rpjm($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_sasaran_lokal
							WHERE indikator_teks=%s
										AND id_sasaran=%d
										AND id!=%d
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								", $data['indikator_teks'], $data['id_sasaran'], $data['id']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks'] . ' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_sasaran_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['id_sasaran']));

					if (empty($dataSasaran)) {
						throw new Exception('Sasaran yang dipilih tidak ditemukan!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $dataSasaran->kode_tujuan));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan tidak terkoneksi dengan sasaran!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if (empty($dataMisi)) {
						throw new Exception('Misi yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Visi dari misi yang dipilih tidak ditemukan!');
					}

					$status = $wpdb->update('data_rpjmd_sasaran_lokal', [
						'indikator_teks' => $data['indikator_teks'],
						'misi_teks' => $dataMisi->misi_teks,
						'sasaran_teks' => $dataSasaran->sasaran_teks,
						'satuan' => $data['satuan'],
						'target_1' => $data['target_1'],
						'target_2' => $data['target_2'],
						'target_3' => $data['target_3'],
						'target_4' => $data['target_4'],
						'target_5' => $data['target_5'],
						'target_awal' => $data['target_awal'],
						'target_akhir' => $data['target_akhir'],
						'tujuan_lock' => $dataTujuan->is_locked,
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_misi' => $dataMisi->urut_misi,
						'urut_sasaran' => $dataSasaran->urut_sasaran,
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'visi_teks' => $dataVisi->visi_teks,
						'is_locked' => $dataSasaran->is_locked
					], [
						'id' => $data['id']
					]);

					if (!$status) {
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses ubah indikator sasaran'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function delete_indikator_sasaran_rpjm()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_sasaran_lokal WHERE id=%d AND id_unik_indikator is not null AND active=1 AND status=1", $_POST['id']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator sasaran'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function verify_indikator_sasaran_rpjm(array $data)
	{
		if (empty($data['id_sasaran'])) {
			throw new Exception('Sasaran wajib dipilih!');
		}

		if (empty($data['indikator_teks'])) {
			throw new Exception('Indikator sasaran tidak boleh kosong!');
		}

		if (empty($data['satuan'])) {
			throw new Exception('Satuan indikator sasaran tidak boleh kosong!');
		}

		if (empty($data['target_1'])) {
			throw new Exception('Target Indikator sasaran tahun ke-1 tidak boleh kosong!');
		}

		if (empty($data['target_2'])) {
			throw new Exception('Target Indikator sasaran tahun ke-2 tidak boleh kosong!');
		}

		if (empty($data['target_3'])) {
			throw new Exception('Target Indikator sasaran tahun ke-3 tidak boleh kosong!');
		}

		if (empty($data['target_4'])) {
			throw new Exception('Target Indikator sasaran tahun ke-4 tidak boleh kosong!');
		}

		if (empty($data['target_5'])) {
			throw new Exception('Target Indikator sasaran tahun ke-5 tidak boleh kosong!');
		}

		if (empty($data['target_awal'])) {
			throw new Exception('Target awal Indikator sasaran tidak boleh kosong!');
		}

		if (empty($data['target_akhir'])) {
			throw new Exception('Target akhir Indikator sasaran tidak boleh kosong!');
		}
	}

	function get_program_rpjm()
	{

		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if ($_POST['type'] == 1) {
					$sql = $wpdb->prepare("
						SELECT * FROM data_rpjmd_program_lokal
							WHERE kode_sasaran=%s AND
								id_unik is not null and
								id_unik_indikator is null and
								status=1 AND 
								is_locked=0 AND 
								active=1 ORDER BY id
						", $_POST['kode_sasaran']);
					$program = $wpdb->get_results($sql, ARRAY_A);
				} else {
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$sql = $wpdb->prepare("
								SELECT 
									* 
								FROM data_rpjmd_program
								WHERE tahun_anggaran=%d
									kode_sasaran=%s
									id_unik IS NOT NULL AND
									id_unik_indikator IS NULL AND
									status=1 AND 
									is_locked=0 AND
									active=1
							", $tahun_anggaran, $_POST['kode_sasaran']);
					$program = $wpdb->get_results($sql, ARRAY_A);
				}

				echo json_encode([
					'status' => true,
					'data' => $program,
					'message' => 'Sukses get program by sasaran'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	function submit_program_rpjm()
	{

		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_program_rpjm($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_program_lokal
							WHERE id_program=%d
								AND kode_sasaran=%s
								AND program_lock=0
								AND status=1
								AND active=1
					", $data['id_program'], $data['kode_sasaran']));

					if (!empty($id_cek)) {
						throw new Exception('Program sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("SELECT id_unik, sasaran_teks, is_locked AS sasaran_lock, urut_sasaran, kode_tujuan FROM data_rpjmd_sasaran_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $data['kode_sasaran']));

					if (empty($dataSasaran)) {
						throw new Exception('Sasaran belum dipilih!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataSasaran->kode_tujuan));

					if (empty($dataTujuan)) {
						throw new Exception('Sasaran tidak terkoneksi dengan tujuan, cek Sasaran!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if (empty($dataMisi)) {
						throw new Exception('Misi tidak terkoneksi dengan tujuan, cek Tujuan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Visi tidak terkoneksi dengan misi, cek Misi!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program
                        FROM data_prog_keg as u 
                        WHERE u.tahun_anggaran=" . get_option('_crb_tahun_anggaran_sipd') . " AND id_program=%d
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ", $data['id_program']));

					if (empty($dataProgram)) {
						throw new Exception('Program tidak ditemukan!');
					}

					try {
						$wpdb->insert('data_rpjmd_program_lokal', [
							'id_misi' => $dataMisi->id_misi,
							'id_unik' => $this->generateRandomString(), // kode_program
							'id_visi' => $dataMisi->id_visi,
							'is_locked' => 0,
							'is_locked_indikator' => 0,
							'kode_sasaran' => $data['kode_sasaran'],
							'kode_tujuan' => $dataTujuan->id_unik,
							'misi_teks' => $dataMisi->misi_teks,
							'id_program' => $dataProgram->id_program,
							'nama_program' => $dataProgram->nama_program,
							'program_lock' => 0,
							'sasaran_lock' => $dataSasaran->sasaran_lock,
							'sasaran_teks' => $dataSasaran->sasaran_teks,
							'status' => 1,
							'tujuan_lock' => $dataTujuan->tujuan_lock,
							'tujuan_teks' => $dataTujuan->tujuan_teks,
							'urut_misi' => $dataMisi->urut_misi,
							'urut_sasaran' => $dataSasaran->urut_sasaran,
							'urut_tujuan' => $dataTujuan->urut_tujuan,
							'visi_teks' => $dataVisi->visi_teks,
							'active' => 1
						]);

						echo json_encode([
							'status' => true,
							'message' => 'Sukses simpan program'
						]);
						exit;
					} catch (Exception $e) {
						throw $e;
					}
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function edit_program_rpjm()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				$data = $wpdb->get_row($wpdb->prepare("
					SELECT * FROM data_rpjmd_program_lokal
						WHERE id_unik=%s
							AND id_unik_indikator IS NULL
							AND is_locked=0
							AND active=1
							AND status=1
						", $_POST['id_unik']), ARRAY_A);

				echo json_encode([
					'status' => true,
					'data' => $data,
					'message' => 'Sukses get program by id_unik'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	function update_program_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_program_rpjm($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_program_lokal
							WHERE id_program=%d
										AND id_unik!=%s
										AND program_lock=0
										AND status=1
										AND active=1
								", $data['id_program'], $data['id_unik']));

					if (!empty($id_cek)) {
						throw new Exception('Program sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("SELECT id_unik, sasaran_teks, is_locked AS sasaran_lock, urut_sasaran, kode_tujuan FROM data_rpjmd_sasaran_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $data['kode_sasaran']));

					if (empty($dataSasaran)) {
						throw new Exception('Sasaran tidak ditemukan!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataSasaran->kode_tujuan));

					if (empty($dataTujuan)) {
						throw new Exception('Sasaran tidak terkoneksi dengan tujuan, cek Sasaran');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if (empty($dataMisi)) {
						throw new Exception('Misi tidak terkoneksi dengan tujuan, cek Tujuan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Visi tidak terkoneksi dengan misi, cek Misi!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program
                        FROM data_prog_keg as u 
                        WHERE u.tahun_anggaran=" . get_option('_crb_tahun_anggaran_sipd') . " AND id_program=%d
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ", $data['id_program']));

					if (empty($dataProgram)) {
						throw new Exception('Program tidak ditemukan!');
					}

					try {
						// update program
						$wpdb->update('data_rpjmd_program_lokal', [
							'id_misi' => $dataMisi->id_misi,
							'id_visi' => $dataMisi->id_visi,
							'kode_sasaran' => $data['kode_sasaran'],
							'kode_tujuan' => $dataTujuan->id_unik,
							'misi_teks' => $dataMisi->misi_teks,
							'id_program' => $dataProgram->id_program,
							'nama_program' => $dataProgram->nama_program,
							'sasaran_lock' => $dataSasaran->sasaran_lock,
							'sasaran_teks' => $dataSasaran->sasaran_teks,
							'tujuan_lock' => $dataTujuan->tujuan_lock,
							'tujuan_teks' => $dataTujuan->tujuan_teks,
							'urut_misi' => $dataMisi->urut_misi,
							'urut_sasaran' => $dataSasaran->urut_sasaran,
							'urut_tujuan' => $dataTujuan->urut_tujuan,
							'visi_teks' => $dataVisi->visi_teks,
						], [
							'id_unik' => $data['id_unik']
						]);

						echo json_encode([
							'status' => true,
							'message' => 'Sukses simpan program'
						]);
						exit;
					} catch (Exception $e) {
						throw $e;
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses ubah program'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function delete_program_rpjm()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE id_unik=%s AND id_unik is not null AND id_unik_indikator is not null AND is_locked=0 AND status=1 AND active=1", $_POST['kode_program']));

					if (!empty($id_cek)) {
						throw new Exception("Program sudah digunakan oleh indikator program", 1);
					}

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_program_lokal WHERE id_unik=%d", $_POST['kode_program']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus program'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function verify_program_rpjm(array $data)
	{
		if (empty($data['kode_sasaran'])) {
			throw new Exception('Sasaran wajib dipilih!');
		}

		if (empty($data['id_urusan'])) {
			throw new Exception('Urusan wajib dipilih');
		}

		if (empty($data['id_bidang'])) {
			throw new Exception('Bidang wajib dipilih!');
		}

		if (empty($data['id_program'])) {
			throw new Exception('Program wajib dipilih');
		}
	}

	function get_indikator_program_rpjm()
	{

		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					if ($_POST['type'] == 1) {
						$sql = $wpdb->prepare("
							SELECT * FROM data_rpjmd_program_lokal 
								WHERE 
									id_unik=%s AND 
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND 
									status=1 AND 
									active=1", $_POST['kode_program']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					} else {
						$tahun_anggaran = $_POST['tahun_anggaran'];
						$sql = $wpdb->prepare("
							SELECT 
								* 
							FROM data_rpjmd_program
							WHERE tahun_anggaran=%d AND 
									id_unik=%s
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND
									active=1
							ORDER BY urut_sasaran
							", $tahun_anggaran, $_POST['kode_program']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					}

					echo json_encode([
						'status' => true,
						'data' => $indikator,
						'message' => 'Sukses get indikator program'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function submit_indikator_program_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_program_rpjm($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_program_lokal
							WHERE indikator=%s
										AND id_unik=%s
										AND id_unik_indikator IS NOT NULL
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								", trim($data['indikator_teks']), $data['kode_program']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks'] . ' sudah ada!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['kode_program']));

					if (empty($dataProgram)) {
						throw new Exception('Program yang dipilih tidak ditemukan!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("SELECT id_unik, sasaran_teks, is_locked AS sasaran_lock, urut_sasaran, kode_tujuan FROM data_rpjmd_sasaran_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataProgram->kode_sasaran));

					if (empty($dataSasaran)) {
						throw new Exception('Sasaran belum dipilih!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataSasaran->kode_tujuan));

					if (empty($dataTujuan)) {
						throw new Exception('Sasaran tidak terkoneksi dengan tujuan, cek Sasaran!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if (empty($dataMisi)) {
						throw new Exception('Misi tidak terkoneksi dengan tujuan, cek Tujuan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Visi tidak terkoneksi dengan misi, cek Misi!');
					}

					$dataUnit = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_unit WHERE id_unit=%d AND tahun_anggaran=%d AND active=1 AND is_skpd=1 order by id_skpd ASC;", $data['id_unit'], get_option('_crb_tahun_anggaran_sipd')));

					if (empty($dataUnit)) {
						throw new Exception('Unit kerja tidak ditemukan!');
					}

					$wpdb->insert('data_rpjmd_program_lokal', [
						'id_misi' => $dataMisi->id_misi,
						'id_program' => $dataProgram->id_program,
						'id_unik' => $data['kode_program'], // kode_program
						'id_unik_indikator' => $this->generateRandomString(),
						'id_unit' => $dataUnit->id_unit,
						'id_visi' => $dataMisi->id_visi,
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'indikator' => $data['indikator_teks'],
						'kode_sasaran' => $dataProgram->kode_sasaran,
						'kode_skpd' => $dataUnit->kode_skpd,
						'kode_tujuan' => $dataTujuan->id_unik,
						'misi_teks' => $dataMisi->misi_teks,
						'nama_program' => $dataProgram->nama_program,
						'nama_skpd' => $dataUnit->nama_skpd,
						'pagu_1' => $data['pagu_1'],
						'pagu_2' => $data['pagu_2'],
						'pagu_3' => $data['pagu_3'],
						'pagu_4' => $data['pagu_4'],
						'pagu_5' => $data['pagu_5'],
						'program_lock' => 0,
						'sasaran_lock' => $dataSasaran->sasaran_lock,
						'sasaran_teks' => $dataSasaran->sasaran_teks,
						'satuan' => $data['satuan'],
						'status' => 1,
						'target_1' => $data['target_1'],
						'target_2' => $data['target_2'],
						'target_3' => $data['target_3'],
						'target_4' => $data['target_4'],
						'target_5' => $data['target_5'],
						'target_awal' => $data['target_awal'],
						'target_akhir' => $data['target_akhir'],
						'tujuan_lock' => $dataTujuan->tujuan_lock,
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_misi' => $dataMisi->urut_misi,
						'urut_sasaran' => $dataSasaran->urut_sasaran,
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'visi_teks' => $dataVisi->visi_teks,
						'active' => 1
					]);

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator program'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function edit_indikator_program_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$indikator = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE id=%d AND id_unik=%s AND id_unik_indikator is not null AND is_locked_indikator=0 AND status=1 AND active=1", $_POST['id'], $_POST['kode_program']));

					echo json_encode([
						'status' => true,
						'data' => $indikator
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function update_indikator_program_rpjm()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_program_rpjm($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_program_lokal
							WHERE indikator=%s
										AND id!=%d
										AND id_unik=%s
										AND id_unik_indikator is not null
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								", trim($data['indikator_teks']), $data['id'], $data['kode_program']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks'] . ' sudah ada!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['kode_program']));

					if (empty($dataProgram)) {
						throw new Exception('Program yang dipilih tidak ditemukan!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("SELECT id_unik, sasaran_teks, is_locked AS sasaran_lock, urut_sasaran, kode_tujuan FROM data_rpjmd_sasaran_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataProgram->kode_sasaran));

					if (empty($dataSasaran)) {
						throw new Exception('Sasaran belum dipilih!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataSasaran->kode_tujuan));

					if (empty($dataTujuan)) {
						throw new Exception('Sasaran tidak terkoneksi dengan tujuan, cek Sasaran!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if (empty($dataMisi)) {
						throw new Exception('Misi tidak terkoneksi dengan tujuan, cek Tujuan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if (empty($dataVisi)) {
						throw new Exception('Visi tidak terkoneksi dengan misi, cek Misi!');
					}

					$dataUnit = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_unit WHERE id_unit=%d AND tahun_anggaran=%d AND active=1 AND is_skpd=1 order by id_skpd ASC;", $data['id_unit'], get_option('_crb_tahun_anggaran_sipd')));

					if (empty($dataUnit)) {
						throw new Exception('Unit kerja tidak ditemukan!');
					}

					$wpdb->update('data_rpjmd_program_lokal', [
						'id_misi' => $dataMisi->id_misi,
						'id_visi' => $dataMisi->id_visi,
						'id_program' => $dataProgram->id_program,
						'id_unit' => $dataUnit->id_unit,
						'indikator' => $data['indikator_teks'],
						'kode_sasaran' => $dataProgram->kode_sasaran,
						'kode_skpd' => $dataUnit->kode_skpd,
						'kode_tujuan' => $dataTujuan->id_unik,
						'misi_teks' => $dataMisi->misi_teks,
						'nama_program' => $dataProgram->nama_program,
						'nama_skpd' => $dataUnit->nama_skpd,
						'pagu_1' => $data['pagu_1'],
						'pagu_2' => $data['pagu_2'],
						'pagu_3' => $data['pagu_3'],
						'pagu_4' => $data['pagu_4'],
						'pagu_5' => $data['pagu_5'],
						'sasaran_lock' => $dataSasaran->sasaran_lock,
						'sasaran_teks' => $dataSasaran->sasaran_teks,
						'satuan' => $data['satuan'],
						'target_1' => $data['target_1'],
						'target_2' => $data['target_2'],
						'target_3' => $data['target_3'],
						'target_4' => $data['target_4'],
						'target_5' => $data['target_5'],
						'target_awal' => $data['target_awal'],
						'target_akhir' => $data['target_akhir'],
						'tujuan_lock' => $dataTujuan->tujuan_lock,
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_misi' => $dataMisi->urut_misi,
						'urut_sasaran' => $dataSasaran->urut_sasaran,
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'visi_teks' => $dataVisi->visi_teks,
						'active' => 1
					], [
						'id' => $data['id']
					]);

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator program'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function delete_indikator_program_rpjm()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_program_lokal WHERE id=%d AND id_unik_indikator is not null AND active=1 AND status=1", $_POST['id']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator program',
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function verify_indikator_program_rpjm(array $data)
	{
		if (empty($data['kode_program'])) {
			throw new Exception('Program wajib dipilih!');
		}

		if (empty($data['indikator_teks'])) {
			throw new Exception('Indikator program tidak boleh kosong!');
		}

		if (empty($data['satuan'])) {
			throw new Exception('Satuan indikator program tidak boleh kosong!');
		}

		if (empty($data['target_1'])) {
			throw new Exception('Target Indikator program tahun ke-1 tidak boleh kosong!');
		}

		if (empty($data['pagu_1'])) {
			throw new Exception('Pagu indikator program tahun ke-1 tidak boleh kosong!');
		}

		if (empty($data['target_2'])) {
			throw new Exception('Target Indikator program tahun ke-2 tidak boleh kosong!');
		}

		if (empty($data['pagu_2'])) {
			throw new Exception('Pagu indikator program tahun ke-2 tidak boleh kosong!');
		}

		if (empty($data['target_3'])) {
			throw new Exception('Target Indikator program tahun ke-3 tidak boleh kosong!');
		}

		if (empty($data['pagu_3'])) {
			throw new Exception('Pagu indikator program tahun ke-3 tidak boleh kosong!');
		}

		if (empty($data['target_4'])) {
			throw new Exception('Target Indikator program tahun ke-4 tidak boleh kosong!');
		}

		if (empty($data['pagu_4'])) {
			throw new Exception('Pagu indikator program tahun ke-4 tidak boleh kosong!');
		}

		if (empty($data['target_5'])) {
			throw new Exception('Target Indikator program tahun ke-5 tidak boleh kosong!');
		}

		if (empty($data['pagu_5'])) {
			throw new Exception('Pagu indikator program tahun ke-5 tidak boleh kosong!');
		}

		if (empty($data['target_awal'])) {
			throw new Exception('Target awal Indikator program tidak boleh kosong!');
		}

		if (empty($data['target_akhir'])) {
			throw new Exception('Target akhir Indikator program tidak boleh kosong!');
		}

		if (empty($data['id_unit'])) {
			throw new Exception('Unit kerja wajib dipilih!');
		}
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

	function _number_format($number = 0, $mata_uang = '')
	{
		if (!is_numeric($number)) {
			$number = 0;
		}
		$uang = number_format($number, 0, ",", ".");
		if (!empty($mata_uang)) {
			$uang = $mata_uang . ' ' . $uang;
		}
		return $uang;
	}

	public function check_total_pagu_penetapan($opt = array())
	{

		global $wpdb;

		try {

			$data_all = array(
				'data' => array(),
				'pagu_1_program_kab' => 0,
				'pagu_2_program_kab' => 0,
				'pagu_3_program_kab' => 0,
				'pagu_4_program_kab' => 0,
				'pagu_5_program_kab' => 0,
				'pagu_1_kegiatan_kab' => 0,
				'pagu_2_kegiatan_kab' => 0,
				'pagu_3_kegiatan_kab' => 0,
				'pagu_4_kegiatan_kab' => 0,
				'pagu_5_kegiatan_kab' => 0,
				'pagu_1_subkegiatan_kab' => 0,
				'pagu_2_subkegiatan_kab' => 0,
				'pagu_3_subkegiatan_kab' => 0,
				'pagu_4_subkegiatan_kab' => 0,
				'pagu_5_subkegiatan_kab' => 0,
			);

			$sql = $wpdb->prepare("
				SELECT 
					* 
				FROM data_unit 
				WHERE tahun_anggaran=%d
					AND is_skpd=1
					AND active=1
				ORDER BY id_skpd ASC
			", $opt['tahun_anggaran']);

			$units = $wpdb->get_results($sql, ARRAY_A);

			foreach ($units as $key => $unit) {

				if (empty($data_all['data'][$unit['id_skpd']])) {
					$data_all['data'][$unit['id_skpd']] = [
						'unit_kerja' => $unit['nama_skpd'],
						'pagu_1_program_opd' => 0,
						'pagu_2_program_opd' => 0,
						'pagu_3_program_opd' => 0,
						'pagu_4_program_opd' => 0,
						'pagu_5_program_opd' => 0,
						'pagu_1_kegiatan_opd' => 0,
						'pagu_2_kegiatan_opd' => 0,
						'pagu_3_kegiatan_opd' => 0,
						'pagu_4_kegiatan_opd' => 0,
						'pagu_5_kegiatan_opd' => 0,
						'pagu_1_subkegiatan_opd' => 0,
						'pagu_2_subkegiatan_opd' => 0,
						'pagu_3_subkegiatan_opd' => 0,
						'pagu_4_subkegiatan_opd' => 0,
						'pagu_5_subkegiatan_opd' => 0,
						'data' => array()
					];
				}

				$tujuan_all = $wpdb->get_results($wpdb->prepare("
					SELECT 
						DISTINCT id_unik 
					FROM data_renstra_tujuan_lokal 
					WHERE
						id_unit=%d AND 
						active=1 $where ORDER BY urut_tujuan
				", $unit['id_skpd']), ARRAY_A);

				foreach ($tujuan_all as $keyTujuan => $tujuan_value) {

					$sasaran_all = $wpdb->get_results($wpdb->prepare("
						SELECT 
							DISTINCT id_unik 
						FROM data_renstra_sasaran_lokal 
						WHERE 
							kode_tujuan=%s AND 
							active=1 ORDER BY urut_sasaran
						", $tujuan_value['id_unik']), ARRAY_A);

					foreach ($sasaran_all as $keySasaran => $sasaran_value) {

						$program_all = $wpdb->get_results($wpdb->prepare(
							"
							SELECT 
								id_unik,
								id_unik_indikator,
								nama_program,
								COALESCE(pagu_1, 0) AS pagu_1,
								COALESCE(pagu_2, 0) AS pagu_2,
								COALESCE(pagu_3, 0) AS pagu_3,
								COALESCE(pagu_4, 0) AS pagu_4,
								COALESCE(pagu_5, 0) AS pagu_5 
							FROM data_renstra_program_lokal 
							WHERE 
								kode_sasaran=%s AND 
								kode_tujuan=%s AND
								active=1 ORDER BY id",
							$sasaran_value['id_unik'],
							$tujuan_value['id_unik']
						), ARRAY_A);

						foreach ($program_all as $keyProgram => $program_value) {

							if (empty($data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']])) {
								$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']] = [
									'nama_program' => $program_value['nama_program'],
									'pagu_akumulasi_1_program' => 0,
									'pagu_akumulasi_2_program' => 0,
									'pagu_akumulasi_3_program' => 0,
									'pagu_akumulasi_4_program' => 0,
									'pagu_akumulasi_5_program' => 0,
									'pagu_akumulasi_1_kegiatan' => 0,
									'pagu_akumulasi_2_kegiatan' => 0,
									'pagu_akumulasi_3_kegiatan' => 0,
									'pagu_akumulasi_4_kegiatan' => 0,
									'pagu_akumulasi_5_kegiatan' => 0,
									'pagu_akumulasi_1_subkegiatan' => 0,
									'pagu_akumulasi_2_subkegiatan' => 0,
									'pagu_akumulasi_3_subkegiatan' => 0,
									'pagu_akumulasi_4_subkegiatan' => 0,
									'pagu_akumulasi_5_subkegiatan' => 0,
									'data' => array(),
								];
							}

							if (!empty($program_value['id_unik_indikator'])) {
								$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_1_program'] += $program_value['pagu_1'];
								$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_2_program'] += $program_value['pagu_2'];
								$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_3_program'] += $program_value['pagu_3'];
								$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_4_program'] += $program_value['pagu_4'];
								$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_5_program'] += $program_value['pagu_5'];

								$data_all['data'][$unit['id_skpd']]['pagu_1_program_opd'] += $program_value['pagu_1'];
								$data_all['data'][$unit['id_skpd']]['pagu_2_program_opd'] += $program_value['pagu_2'];
								$data_all['data'][$unit['id_skpd']]['pagu_3_program_opd'] += $program_value['pagu_3'];
								$data_all['data'][$unit['id_skpd']]['pagu_4_program_opd'] += $program_value['pagu_4'];
								$data_all['data'][$unit['id_skpd']]['pagu_5_program_opd'] += $program_value['pagu_5'];

								$data_all['pagu_1_program_kab'] += $program_value['pagu_1'];
								$data_all['pagu_2_program_kab'] += $program_value['pagu_2'];
								$data_all['pagu_3_program_kab'] += $program_value['pagu_3'];
								$data_all['pagu_4_program_kab'] += $program_value['pagu_4'];
								$data_all['pagu_5_program_kab'] += $program_value['pagu_5'];
							}

							if (empty($program_value['id_unik_indikator'])) {
								$kegiatan_all = $wpdb->get_results($wpdb->prepare(
									"
									SELECT 
										id_unik,
										id_unik_indikator,
										nama_giat,
										COALESCE(pagu_1, 0) AS pagu_1,
										COALESCE(pagu_2, 0) AS pagu_2,
										COALESCE(pagu_3, 0) AS pagu_3,
										COALESCE(pagu_4, 0) AS pagu_4,
										COALESCE(pagu_5, 0) AS pagu_5
									FROM data_renstra_kegiatan_lokal
									WHERE 
										kode_program=%s AND
										kode_sasaran=%s AND
										kode_tujuan=%s AND
										active=1 ORDER BY id",
									$program_value['id_unik'],
									$sasaran_value['id_unik'],
									$tujuan_value['id_unik']
								), ARRAY_A);

								foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {

									if (empty($data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']])) {
										$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']] = [
											'nama_kegiatan' => $kegiatan_value['nama_giat'],
											'pagu_akumulasi_1_kegiatan' => 0,
											'pagu_akumulasi_2_kegiatan' => 0,
											'pagu_akumulasi_3_kegiatan' => 0,
											'pagu_akumulasi_4_kegiatan' => 0,
											'pagu_akumulasi_5_kegiatan' => 0,
											'pagu_akumulasi_1_subkegiatan' => 0,
											'pagu_akumulasi_2_subkegiatan' => 0,
											'pagu_akumulasi_3_subkegiatan' => 0,
											'pagu_akumulasi_4_subkegiatan' => 0,
											'pagu_akumulasi_5_subkegiatan' => 0,
										];
									}

									if (!empty($kegiatan_value['id_unik_indikator'])) {
										$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_1_kegiatan'] += $kegiatan_value['pagu_1'];
										$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_2_kegiatan'] += $kegiatan_value['pagu_2'];
										$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_3_kegiatan'] += $kegiatan_value['pagu_3'];
										$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_4_kegiatan'] += $kegiatan_value['pagu_4'];
										$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_5_kegiatan'] += $kegiatan_value['pagu_5'];

										$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1_kegiatan'] += $kegiatan_value['pagu_1'];
										$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2_kegiatan'] += $kegiatan_value['pagu_2'];
										$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3_kegiatan'] += $kegiatan_value['pagu_3'];
										$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4_kegiatan'] += $kegiatan_value['pagu_4'];
										$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5_kegiatan'] += $kegiatan_value['pagu_5'];

										$data_all['data'][$unit['id_skpd']]['pagu_1_kegiatan_opd'] += $kegiatan_value['pagu_1'];
										$data_all['data'][$unit['id_skpd']]['pagu_2_kegiatan_opd'] += $kegiatan_value['pagu_2'];
										$data_all['data'][$unit['id_skpd']]['pagu_3_kegiatan_opd'] += $kegiatan_value['pagu_3'];
										$data_all['data'][$unit['id_skpd']]['pagu_4_kegiatan_opd'] += $kegiatan_value['pagu_4'];
										$data_all['data'][$unit['id_skpd']]['pagu_5_kegiatan_opd'] += $kegiatan_value['pagu_5'];

										$data_all['pagu_1_kegiatan_kab'] += $kegiatan_value['pagu_1'];
										$data_all['pagu_2_kegiatan_kab'] += $kegiatan_value['pagu_2'];
										$data_all['pagu_3_kegiatan_kab'] += $kegiatan_value['pagu_3'];
										$data_all['pagu_4_kegiatan_kab'] += $kegiatan_value['pagu_4'];
										$data_all['pagu_5_kegiatan_kab'] += $kegiatan_value['pagu_5'];
									}

									if (empty($kegiatan_value['id_unik_indikator'])) {
										$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare(
											"
											SELECT
												nama_sub_giat, 
												COALESCE(pagu_1, 0) AS pagu_1, 
												COALESCE(pagu_2, 0) AS pagu_2, 
												COALESCE(pagu_3, 0) AS pagu_3, 
												COALESCE(pagu_4, 0) AS pagu_4, 
												COALESCE(pagu_5, 0) AS pagu_5
											FROM data_renstra_sub_kegiatan_lokal 
											WHERE 
												kode_kegiatan=%s AND 
												kode_program=%s AND 
												kode_sasaran=%s AND 
												kode_tujuan=%s AND
												id_unik_indikator IS NULL AND 
												active=1 ORDER BY id",
											$kegiatan_value['id_unik'],
											$program_value['id_unik'],
											$sasaran_value['id_unik'],
											$tujuan_value['id_unik']
										), ARRAY_A);

										foreach ($sub_kegiatan_all as $keySubKegiatan => $sub_kegiatan_value) {
											$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_1_subkegiatan'] += $sub_kegiatan_value['pagu_1'];
											$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_2_subkegiatan'] += $sub_kegiatan_value['pagu_2'];
											$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_3_subkegiatan'] += $sub_kegiatan_value['pagu_3'];
											$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_4_subkegiatan'] += $sub_kegiatan_value['pagu_4'];
											$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['pagu_akumulasi_5_subkegiatan'] += $sub_kegiatan_value['pagu_5'];

											$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1_subkegiatan'] += $sub_kegiatan_value['pagu_1'];
											$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2_subkegiatan'] += $sub_kegiatan_value['pagu_2'];
											$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3_subkegiatan'] += $sub_kegiatan_value['pagu_3'];
											$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4_subkegiatan'] += $sub_kegiatan_value['pagu_4'];
											$data_all['data'][$unit['id_skpd']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5_subkegiatan'] += $sub_kegiatan_value['pagu_5'];

											$data_all['data'][$unit['id_skpd']]['pagu_1_sub_kegiatan_opd'] += $sub_kegiatan_value['pagu_1'];
											$data_all['data'][$unit['id_skpd']]['pagu_2_sub_kegiatan_opd'] += $sub_kegiatan_value['pagu_2'];
											$data_all['data'][$unit['id_skpd']]['pagu_3_sub_kegiatan_opd'] += $sub_kegiatan_value['pagu_3'];
											$data_all['data'][$unit['id_skpd']]['pagu_4_sub_kegiatan_opd'] += $sub_kegiatan_value['pagu_4'];
											$data_all['data'][$unit['id_skpd']]['pagu_5_sub_kegiatan_opd'] += $sub_kegiatan_value['pagu_5'];

											$data_all['pagu_1_subkegiatan_kab'] += $sub_kegiatan_value['pagu_1'];
											$data_all['pagu_2_subkegiatan_kab'] += $sub_kegiatan_value['pagu_2'];
											$data_all['pagu_3_subkegiatan_kab'] += $sub_kegiatan_value['pagu_3'];
											$data_all['pagu_4_subkegiatan_kab'] += $sub_kegiatan_value['pagu_4'];
											$data_all['pagu_5_subkegiatan_kab'] += $sub_kegiatan_value['pagu_5'];
										}
									}
								}
							}
						}
					}
				}
			}

			// echo '<pre>';print_r($data_all);echo '</pre>';die();
			$error = '';

			for ($i = 0; $i < $opt['lama_pelaksanaan']; $i++) {
				if (
					($data_all['pagu_' . ($i + 1) . '_program_kab'] != $data_all['pagu_' . ($i + 1) . '_kegiatan_kab']) ||
					($data_all['pagu_' . ($i + 1) . '_program_kab'] != $data_all['pagu_' . ($i + 1) . '_subkegiatan_kab']) ||
					($data_all['pagu_' . ($i + 1) . '_kegiatan_kab'] != $data_all['pagu_' . ($i + 1) . '_subkegiatan_kab'])
				) {

					$error .= "
						<h4>Pagu akumulasi total di tahun ke " . ($i + 1) . " tidak sama antara pagu indikator program, pagu indikator kegiatan dan pagu sub kegiatan.</h4>
						<table class='table table-bordered'>
							<thead>
								<tr>
									<th width='50%'>Data Renstra</th>
									<th>Total Pagu</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class='text-left'>Program</td>
									<td class='text-right'>" . $this->_number_format($data_all['pagu_' . ($i + 1) . '_program_kab']) . "</td>
								</tr>
								<tr>
									<td class='text-left'>Kegiatan</td>
									<td class='text-right'>" . $this->_number_format($data_all['pagu_' . ($i + 1) . '_kegiatan_kab']) . "</td>
								</tr>
								<tr>
									<td class='text-left'>Sub Kegiatan</td>
									<td class='text-right'>" . $this->_number_format($data_all['pagu_' . ($i + 1) . '_subkegiatan_kab']) . "</td>
								</tr>
							</tbody>
						</table>
						";
				}
			}

			foreach ($data_all['data'] as $unit) {
				foreach ($unit['data'] as $valueProgram) {
					foreach ($valueProgram['data'] as $keyKegiatan => $valueKegiatan) {
						for ($i = 0; $i < $opt['lama_pelaksanaan']; $i++) {
							if (
								$valueKegiatan['pagu_akumulasi_' . ($i + 1) . '_kegiatan'] !=
								$valueKegiatan['pagu_akumulasi_' . ($i + 1) . '_subkegiatan']
							) {
								$error .= "
								<div>
									<h4>Pagu Akumulasi Tahun ke " . ($i + 1) . " tidak sama antara pagu indikator kegiatan dan pagu sub kegiatannya.</h4>
									<table class='table table-bordered'>
										<thead>
											<tr>
												<th width='30%'>Unit Kerja</th>
												<th width='30%'>Kegiatan</th>
												<th>Pagu Kegiatan</th>
												<th>Pagu Sub Kegiatan</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td class='text-left'>" . $unit['unit_kerja'] . "</td>
												<td class='text-left'>" . $valueKegiatan['nama_kegiatan'] . "</td>
												<td class='text-right'>" . $this->_number_format($valueKegiatan['pagu_akumulasi_' . ($i + 1) . '_kegiatan']) . "</td>
												<td class='text-right'>" . $this->_number_format($valueKegiatan['pagu_akumulasi_' . ($i + 1) . '_subkegiatan']) . "</td>
											</tr>
										</tbody>
									</table>
								</div>";
							}
						}
					}

					for ($i = 0; $i < $opt['lama_pelaksanaan']; $i++) {
						if (
							$valueProgram['pagu_akumulasi_' . ($i + 1) . '_program'] !=
							$valueProgram['pagu_akumulasi_' . ($i + 1) . '_kegiatan']
						) {
							$error .= "
								<div>
									<h4>Pagu Akumulasi Tahun ke " . ($i + 1) . " tidak sama antara pagu indikator program dan pagu indikator kegiatannya.</h4>
									<table class='table table-bordered'>
										<thead>
											<tr>
												<th width='30%'>Unit Kerja</th>
												<th width='30%'>Program</th>
												<th>Pagu Program</th>
												<th>Pagu Kegiatan</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td class='text-left'>" . $unit['unit_kerja'] . "</td>
												<td class='text-left'>" . $valueProgram['nama_program'] . "</td>
												<td class='text-right'>" . $this->_number_format($valueProgram['pagu_akumulasi_' . ($i + 1) . '_program']) . "</td>
												<td class='text-right'>" . $this->_number_format($valueProgram['pagu_akumulasi_' . ($i + 1) . '_kegiatan']) . "</td>
											</tr>
										</tbody>
									</table>
								</div>";
						}
					}
				}
			}

			if (!empty($error)) {
				throw new Exception($error);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => 'error',
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	private function wpsipd_upload_file($opsi = array(), $ret = array())
	{
		$file = $opsi['file'];
		$file_name = $opsi['file_name'];
		$file_extension = $opsi['file_extension'];
		$tahun_anggaran = $opsi['tahun_anggaran'];
		$url_asli = $opsi['url_asli'];

		$target_folder = WPSIPD_PLUGIN_PATH . 'public/media/';
		$target_file = $target_folder . $file_name;
		// max 10MB
		if ($file["size"] > 1000000) {
			$ret['status'] = 'error';
			$ret['message'] = 'Max file upload sebesar 10MB!';
		}
		// cek type file
		$imageFileType = strtolower(pathinfo($target_folder . basename($file["name"]), PATHINFO_EXTENSION));
		if ($imageFileType != $file_extension) {
			$ret['status'] = 'error';
			$ret['message'] = 'File yang diupload harus berextensi .' . $file_extension . '!';
		}
		if ($ret['status'] == 'success') {
			move_uploaded_file($file["tmp_name"], $target_file);
			$ret['path'] = $target_file;
			$cek_id = $wpdb->get_var($wpdb->prepare("
				SELECT
					id
				FROM data_file
				WHERE nama = %s
					AND tahun_anggaran = %d
			", $rinci['sp2d_no'], $tahun_anggaran), ARRAY_A);
			$opsi = array(
				'nama' => $file_name,
				'url_asli' => $url_asli,
				'path' => $target_file,
				'tipe_file' => $file_extension,
				'updated_at' => date('Y-m-d H:i:s'),
				'tahun_anggaran' => $tahun_anggaran
			);
			if (empty($cek_id)) {
				$wpdb->insert('data_sp2d_fmis', $opsi);
				$cek_id = $wpdb->insert_id;
			} else {
				$wpdb->update('data_sp2d_fmis', $opsi, array(
					'id' => $cek_id
				));
			}
			$ret['id'] = $cek_id;
		}
		return $ret;
	}

	function save_file()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'message'	=> 'Berhasil simpan file!'
		);

		$table_content = '';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$data = file_get_contents('php://input');
				$mysql_blob = base64_encode($data);
				print_r($data);
				print_r($_REQUEST);
				die('tes');
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$return = $this->wpsipd_upload_file(array(
					'file' => $_FILES["file"],
					'file_name' => $_POST["file_name"],
					'tahun_anggaran' => $tahun_anggaran,
					'file_extension' => 'pdf',
					'url_asli' => $_POST['url']
				), $return);
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	function get_data_json()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'message'	=> 'Berhasil get data JSON!'
		);

		$table_content = '';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_skpd = array();
				foreach (explode(',', $_POST['id_skpd']) as $val) {
					$id_skpd[] = $wpdb->prepare('%d', $val);
				};
				$new_id_skpd = implode(',', $id_skpd);

				$id_sumber_dana_default = get_option('_crb_default_sumber_dana');
				$sumber_dana_default = $wpdb->get_row($wpdb->prepare('
				    SELECT 
				        id_dana,
				        kode_dana,
				        nama_dana
				    FROM data_sumber_dana
				    WHERE tahun_anggaran=%d
				        AND id_dana=%d
				', $_POST['tahun_anggaran'], $id_sumber_dana_default), ARRAY_A);
				if ($_POST['tipe'] == 'json_rek_sd') {
					$sql_anggaran = $wpdb->prepare("
					    SELECT 
					        k.kode_urusan,
					        k.nama_urusan,
					        k.kode_bidang_urusan,
					        k.nama_bidang_urusan,
					        k.kode_program,
					        k.nama_program,
					        k.kode_giat,
					        k.nama_giat,
					        k.kode_skpd,
					        k.nama_skpd,
					        k.kode_sub_skpd,
					        k.nama_sub_skpd,
					        k.kode_sub_giat,
					        k.nama_sub_giat,
					        r.kode_akun,
					        r.nama_akun,
					        sum(r.rincian) as rincian,
					        coalesce(ms.id_dana, $sumber_dana_default[id_dana]) as id_dana,
					        coalesce(ms.nama_dana, '$sumber_dana_default[nama_dana]') as nama_dana
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
					        AND k.id_sub_skpd IN ($new_id_skpd)
					    GROUP BY k.kode_sub_skpd ASC, k.kode_sub_giat, r.kode_akun
					    ORDER BY k.kode_sub_skpd ASC, k.kode_sub_giat ASC
					    ", $_POST["tahun_anggaran"]);
					$return['data'] = $wpdb->get_results($sql_anggaran, ARRAY_A);
				}else if ($_POST['tipe'] == 'json_rek_rak') {
					$sql_anggaran = $wpdb->prepare("
					    SELECT 
					        (
					            SELECT 
					                nama_bidang_urusan
					            from data_prog_keg
					            where tahun_anggaran=k.tahun_anggaran
					                and active=1
					                AND id_bidang_urusan=k.id_bidang_urusan
					            LIMIT 1
					        ) as bidang_urusan,
					        k.id_sub_skpd,
					        k.id_bidang_urusan,
					        u.nama_skpd,
					        u.kode_skpd,
					        sum(k.bulan_1) as bulan_1,
					        sum(k.bulan_2) as bulan_2,
					        sum(k.bulan_3) as bulan_3,
					        sum(k.bulan_4) as bulan_4,
					        sum(k.bulan_5) as bulan_5,
					        sum(k.bulan_6) as bulan_6,
					        sum(k.bulan_7) as bulan_7,
					        sum(k.bulan_8) as bulan_8,
					        sum(k.bulan_9) as bulan_9,
					        sum(k.bulan_10) as bulan_10,
					        sum(k.bulan_11) as bulan_11,
					        sum(k.bulan_12) as bulan_12,
					        (sum(k.bulan_1) + sum(k.bulan_2) + sum(k.bulan_3)) as tw_1,
					        (sum(k.bulan_4) + sum(k.bulan_5) + sum(k.bulan_6)) as tw_2,
					        (sum(k.bulan_7) + sum(k.bulan_8) + sum(k.bulan_9)) as tw_3,
					        (sum(k.bulan_10) + sum(k.bulan_11) + sum(k.bulan_12)) as tw_4,
					        (sum(k.bulan_1) + sum(k.bulan_2) + sum(k.bulan_3) + sum(k.bulan_4) + sum(k.bulan_5) + sum(k.bulan_6) + sum(k.bulan_7) + sum(k.bulan_8)  + sum(k.bulan_9)  + sum(k.bulan_10) + sum(k.bulan_11) + sum(k.bulan_12)) as total
					    from data_anggaran_kas k
					    inner join data_unit u on k.id_sub_skpd=u.id_skpd
					        and u.tahun_anggaran=k.tahun_anggaran
					        and u.active=1
					    where k.tahun_anggaran=%d
					        and k.active=1
					        and k.type='belanja'
					        and k.id_sub_skpd IN ($input[id_skpd])
					    group by k.id_bidang_urusan, k.id_sub_skpd
					    order by k.id_bidang_urusan, k.id_sub_skpd ASC
					    ", $_POST["tahun_anggaran"]);
					$return['data'] = $wpdb->get_results($sql_anggaran, ARRAY_A);
				} else if ($_POST['tipe'] == 'json_rek_p3dn') {
					$sql_anggaran = $wpdb->prepare("
					    SELECT 
					        k.nama_skpd,
					        k.nama_program,
					        k.nama_giat,
					        k.nama_sub_giat,
					        r.kode_akun,
					        r.nama_akun,
					        r.subs_bl_teks,
					        r.ket_bl_teks,
					        r.lokus_akun_teks, 
					        r.nama_komponen, 
					        r.spek_komponen,
					        ms.nama_dana,
					        coalesce(r.rincian_murni, 0) as rincian_murni,
					        coalesce(r.rincian, 0) as rincian,
					        0 as realisasi,
					        0 as realisasi_akun,
					        '' as uraian_spm,
					        '' as sisa,
					        '' as keterangan
					    FROM data_sub_keg_bl as k 
					    INNER JOIN data_rka as r on k.kode_sbl=r.kode_sbl 
					        and r.active=k.active 
					        and r.tahun_anggaran=k.tahun_anggaran 
					    LEFT JOIN data_mapping_sumberdana as s on r.id_rinci_sub_bl=s.id_rinci_sub_bl 
					        and s.active=k.active 
					        and s.tahun_anggaran=k.tahun_anggaran 
					    LEFT JOIN data_sumber_dana as ms on ms.id_dana=s.id_sumber_dana 
					        and ms.tahun_anggaran=k.tahun_anggaran 
					    LEFT JOIN data_realisasi_akun as a on k.kode_sbl=a.kode_sbl 
					        and a.tahun_anggaran=k.tahun_anggaran 
					    LEFT JOIN data_sub_keg_bl as u on k.kode_sbl=u.kode_sbl 
					        and u.tahun_anggaran=k.tahun_anggaran
					    WHERE
					        k.tahun_anggaran=%d
					        AND k.active=1
					        AND k.id_sub_skpd IN ($new_id_skpd)
					    GROUP BY k.kode_sub_skpd ASC, k.kode_sub_giat, r.subs_bl_teks
					    ORDER BY k.kode_sub_skpd ASC, k.kode_sub_giat ASC
					", $_POST["tahun_anggaran"]);
					$data = $wpdb->get_results($sql_anggaran, ARRAY_A);
					$return['sql'] = $wpdb->last_query;

					$realisasi = $wpdb->get_results($wpdb->prepare("
						SELECT
							a.nilai,
							a.realisasi,
							a.kode_akun,
							a.kode_sbl,
							0 as realisasi_rincian,
							coalesce(sp.keteranganSpp, '') as keteranganSpp,
							coalesce(sp.nomorSpp, '') as nomorSpp,
							coalesce(sp.tanggalDisetujuiSpp, '') as tanggalDisetujuiSpp
						FROM data_realisasi_akun_sipd as a
						LEFT JOIN data_spp_sipd_detail as s on a.kode_akun=s.kode_rekening
							AND s.active=a.active
							AND s.tahun_anggaran=a.tahun_anggaran
							AND s.id_sub_skpd=a.id_sub_skpd
							AND s.id_sub_kegiatan=a.id_sub_giat
						LEFT JOIN data_spp_sipd as sp on sp.idSpp=s.idSpp
							AND sp.active=s.active
							AND sp.tahun_anggaran=s.tahun_anggaran
						WHERE a.active=1
							AND a.tahun_anggaran=%d
							AND a.id_sub_skpd IN ($new_id_skpd)
					", $_POST['tahun_anggaran']), ARRAY_A);
					$new_realisasi = array();
					foreach ($realisasi as $key => $val) {
						$new_realisasi[$val['kode_akun']] = $val;
					}

					foreach ($data as $key => $val) {
						if (
							!empty($new_realisasi[$val['kode_akun']])
							&& $new_realisasi[$val['kode_akun']]['realisasi_rincian'] < $new_realisasi[$val['kode_akun']]['realisasi']
						) {
							if ($new_realisasi[$val['kode_akun']]['realisasi_rincian'] + $val['rincian'] <= $new_realisasi[$val['kode_akun']]['realisasi']) {
								$data[$key]['realisasi'] = $val['rincian'];
								$new_realisasi[$val['kode_akun']]['realisasi_rincian'] += $data[$key]['realisasi'];
							} else {
								$data[$key]['realisasi'] = $new_realisasi[$val['kode_akun']]['realisasi'] - $new_realisasi[$val['kode_akun']]['realisasi_rincian'];
								$new_realisasi[$val['kode_akun']]['realisasi_rincian'] += $data[$key]['realisasi'];
							}
							$data[$key]['sisa'] = number_format($data[$key]['rincian'] - $data[$key]['realisasi'], 0, ",", ".");
							$data[$key]['realisasi'] = number_format($data[$key]['realisasi'], 0, ",", ".");
							$data[$key]['realisasi_akun'] = number_format($new_realisasi[$val['kode_akun']]['realisasi'], 0, ",", ".");
							$data[$key]['keteranganSpp'] = $new_realisasi[$val['kode_akun']]['keteranganSpp'];
							$data[$key]['nomorSpp'] = $new_realisasi[$val['kode_akun']]['nomorSpp'];
							$data[$key]['tanggalDisetujuiSpp'] = $new_realisasi[$val['kode_akun']]['tanggalDisetujuiSpp'];
							$data[$key]['uraian_spm'] = $data[$key]['keteranganSpp'] . ' ' . $data[$key]['nomorSpp'] . ' ' . $data[$key]['tanggalDisetujuiSpp'];
						}
						$komponen = array();
						if (!empty($val['lokus_akun_teks'])) {
							$komponen[] = $val['lokus_akun_teks'];
						}
						if (!empty($val['nama_komponen'])) {
							$komponen[] = $val['nama_komponen'];
						}
						if (!empty($val['spek_komponen'])) {
							$komponen[] = $val['spek_komponen'];
						}
						$data[$key]['komponen'] = implode(' ', $komponen);
						$data[$key]['rincian_murni'] = number_format($val['rincian_murni'], 0, ",", ".");
						$data[$key]['rincian'] = number_format($val['rincian'], 0, ",", ".");
					}
					$return['data'] = $data;
				}
			} else {
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function sipkd_get_akun_sipd()
	{
		global $wpdb;
		//verifikasi request

		//end verifikasi
		$data = $wpdb->get_results($wpdb->prepare("
		select 
		concat(id_akun,'_') mtgkey,concat(kode_akun,'.') kdper,nama_akun nmper,
		case 
		when length(kode_akun)=1 then 1
		when length(kode_akun)=3 then 2
		when length(kode_akun)=6 then 3
		when length(kode_akun)=9 then 4
		when length(kode_akun)=12 then 5
		else 6
		end mtglevel,
		if(length(kode_akun)=17,'D','H') as TYPE
		from data_akun where left(kode_akun,1)=%s and tahun_anggaran=%d 
		ORDER BY kode_akun", $_POST['jenis'], $_POST['tahun_anggaran']));
		$ret = [
			'status' => 'succes',
			'message' => 'Berhasil get data JSON',
			'data' => $data
		];
		die(json_encode($ret));
	}

	public function sipkd_singkron_akun()
	{
		global $wpdb;
		$ret = [];

		$sipkd = new Wpsipd_sqlsrv(get_option('crb_host_sipkd'), get_option('crb_port_sipkd'), get_option('crb_user_sipkd'), get_option('crb_pass_sipkd'), get_option('crb_dbname_sipkd'));
		//verifikasi request

		//end verifikasi
		if ($sipkd->status) {
			$data = $wpdb->get_results($wpdb->prepare("
			select 
			concat(id_akun,'_') mtgkey,concat(kode_akun,'.') kdper,nama_akun nmper,
			case 
			when length(kode_akun)=1 then 1
			when length(kode_akun)=3 then 2
			when length(kode_akun)=6 then 3
			when length(kode_akun)=9 then 4
			when length(kode_akun)=12 then 5
			else 6
			end mtglevel,
			if(length(kode_akun)=17,'D','H') as TYPE
			from data_akun where left(kode_akun,1)=%s and tahun_anggaran=%d 
			ORDER BY kode_akun", $_POST['jenis'], $_POST['tahun_anggaran']));

			//looping data row $data
			try {
				foreach ($data as $v) {

					//insert data ke database sipkd
				}
				$ret = [
					'status' => 'success',
					'message' => 'Berhasil singkronisasi Akun SIPD ke SIPKD'
				];
			} catch (Exception $e) {
				$ret = [
					'status' => 'error',
					'message' => $e->getMessage()
				];
			}
		} else {
			$ret = [
				'status' => 'error',
				'message' => 'Terjadi kesalahan koneksi ke database SIPKD'
			];
		}
		die(json_encode($ret));
	}

	public function sipkd_get_urus_skpd()
	{
		global $wpdb;
		//Verifikasi Request

		//end verifikasi request
		$data = $wpdb->get_results($wpdb->prepare("
			SELECT distinct 
				concat(id_urusan,'_') as unitkey,kode_urusan kdunit,nama_urusan nmunit,1 kdlevel,'H' as TYPE 
			FROM data_sub_keg_bl
			where tahun_anggaran=%d
			union ALL
			SELECT distinct 
				concat(id_bidang_urusan,'_'),kode_bidang_urusan,nama_bidang_urusan,2,'H' 
			FROM data_sub_keg_bl
			where tahun_anggaran=%d
			union all
			SELECT distinct 
				concat(id_skpd,'_'),kode_skpd,nama_skpd,3,'D' 
			FROM data_sub_keg_bl
			where tahun_anggaran=%d
			union ALL
			SELECT distinct 
				concat(id_sub_skpd,'_'),kode_sub_skpd,nama_sub_skpd,4,'D' 
			FROM data_sub_keg_bl 
			where tahun_anggaran=%d 
				and id_sub_skpd<>id_skpd 
			order by kdunit
		", $_POST['tahun_anggaran'], $_POST['tahun_anggaran'], $_POST['tahun_anggaran'], $_POST['tahun_anggaran']));

		//$data=$wpdb->get_result($qr);
		$ret = [
			'status' => 'success',
			'message' => 'Berhasil load data Urusan/Bidang Urusan/ SKPD',
			'data' => $data
		];

		die(json_encode($ret));
	}

	public function singkron_label_spm()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Label SPM!'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['label'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$label = json_decode(stripslashes(html_entity_decode($_POST['label'])), true);
					} else {
						$label = $_POST['label'];
					}
					$wpdb->update('data_label_spm', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($label as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id_label_kokab 
							from data_label_spm 
							where tahun_anggaran=%d 
								AND id_spm=%d
						", $_POST['tahun_anggaran'], $v['id_spm']));
						$opsi = array(
							'id_spm' => $v['id_spm'],
							'abjad_spm' => $v['abjad_spm'],
							'spm_teks' => $v['spm_teks'],
							'kode_layanan' => $v['kode_layanan'],
							'layanan_teks' => $v['layanan_teks'],
							'dashuk_teks' => $v['dashuk_teks'],
							'set_prov' => $v['set_prov'],
							'set_kab_kota' => $v['set_kab_kota'],
							'is_locked' => $v['is_locked'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_label_spm', $opsi, array(
								'id_spm' => $v['id_spm'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_label_spm', $opsi);
						}
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Label SPM Salah!';
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

	public function singkron_mapping_spm()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Label subgiat SPM!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['label'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$label = json_decode(stripslashes(html_entity_decode($_POST['label'])), true);
					} else {
						$label = $_POST['label'];
					}
					$wpdb->update('data_mapping_spm_subgiat', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($label as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id_spm_giat 
							from data_mapping_spm_subgiat 
							where tahun_anggaran=%d 
								AND id_spm_giat=%d
						", $_POST['tahun_anggaran'], $v['id_spm_giat']));
						$opsi = array(
							'id_spm_giat' => $v['id_spm_giat'],
							'id_spm' => $v['id_spm'],
							'id_bidang_urusan' => $v['id_bidang_urusan'],
							'id_program' => $v['id_program'],
							'id_giat' => $v['id_giat'],
							'id_sub_giat' => $v['id_sub_giat'],
							'set_prov' => $v['set_prov'],
							'set_kab_kota' => $v['set_kab_kota'],
							'is_locked' => $v['is_locked'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_mapping_spm_subgiat', $opsi, array(
								'id_spm_giat' => $v['id_spm_giat'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_mapping_spm_subgiat', $opsi);
						}
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Label SPM Sub Giat Salah!';
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

	public function singkron_label_kemiskinan()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Label subgiat Kemiskinan Ekstrim!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['label'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$label = json_decode(stripslashes(html_entity_decode($_POST['label'])), true);
					} else {
						$label = $_POST['label'];
					}
					$wpdb->update('data_label_kemiskinan', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));

					foreach ($label as $k => $v) {
						$cek = $wpdb->get_row($wpdb->prepare("
							SELECT 
								strategi_teks 
							from data_label_kemiskinan 
							where tahun_anggaran=%d 
								AND strategi_teks=%d
						", $_POST['tahun_anggaran'], $v['strategi_teks']), OBJECT);

						$opsi = array(
							'strategi_teks' => $v['strategi_teks'],
							'kelompok_teks' => $v['kelompok_teks'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_label_kemiskinan', $opsi, array(
								'strategi_teks' => $v['strategi_teks'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_label_kemiskinan', $opsi);
						}
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Label Kemiskinan Ekstrim Sub Giat Salah!';
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

	public function singkron_mapping_kemiskinan()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Label subgiat Kemiskinan Ekstrim!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['label'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$label = json_decode(stripslashes(html_entity_decode($_POST['label'])), true);
					} else {
						$label = $_POST['label'];
					}

					$wpdb->update('data_mapping_kemiskinan_subgiat', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));

					foreach ($label as $k => $vg) {
						// $cek = $wpdb->get_row($wpdb->prepare("
						// 	SELECT 
						// 		data_mapping_kemiskinan_subgiat.id_label_miskin 
						// 	from data_mapping_kemiskinan_subgiat 
						// 	JOIN data_label_kemiskinan ON data_label_kemiskinan.id = data_mapping_kemiskinan_subgiat.id_label_miskin
						// 	where data_label_kemiskinan.tahun_anggaran=%d 
						// 		AND data_label_kemiskinan.strategi_teks=%d
						// 		AND data_mapping_kemiskinan_subgiat.kode_sub_giat=%d
						// ", $_POST['tahun_anggaran'], $vg['strategi_teks'], $vg['kode_sub_giat']), OBJECT);

						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id_sub_giat 
							from data_mapping_kemiskinan_subgiat 
							where tahun_anggaran=%d 
								AND id_sub_giat=%d
						", $_POST['tahun_anggaran'], $vg['id_sub_giat']));

						$id_label_miskin = $wpdb->get_row($wpdb->prepare("
							SELECT 
								id 
							from data_label_kemiskinan 
							where tahun_anggaran=%d 
								AND strategi_teks=%d
						", $_POST['tahun_anggaran'], $vg['strategi_teks']), OBJECT);
						// print_r($id_label_miskin);die();
						$opsi = array(
							'id_label_miskin' => $id_label_miskin->id,
							'kelompok_teks' => $vg['kelompok_teks'],
							'strategi_teks' => $vg['strategi_teks'],
							'id_urusan' => $vg['id_urusan'],
							'id_bidang_urusan' => $vg['id_bidang_urusan'],
							'id_program' => $vg['id_program'],
							'id_giat' => $vg['id_giat'],
							'id_sub_giat' => $vg['id_sub_giat'],
							'kode_sub_giat' => $vg['kode_sub_giat'],
							'nama_sub_giat' => $vg['nama_sub_giat'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_mapping_kemiskinan_subgiat', $opsi, array(
								// 'id_label_miskin' => $cek->id_label_miskin,
								'id_sub_giat' => $vg['id_sub_giat'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_mapping_kemiskinan_subgiat', $opsi);
						}
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Label Kemiskinan Ekstrim Sub Giat Salah!';
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

	public function singkron_label_kokab()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Label/Prioritas Kabupaten Kota!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['label'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$label = json_decode(stripslashes(html_entity_decode($_POST['label'])), true);
						// $label = $_POST['label'];		
					} else {
						$label = $_POST['label'];
					}
					$wpdb->update('data_prioritas_kokab', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($label as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id_label_kokab 
							from data_prioritas_kokab 
							where tahun_anggaran=%d 
								AND id_label_kokab=%d
						", $_POST['tahun_anggaran'], $v['id_label_kokab']));
						$opsi = array(
							'id_prioritas' => $v['id_prioritas'],
							'id_label_kokab' => $v['id_label_kokab'],
							'teks_prioritas' => $v['teks_prioritas'],
							'id_unik' => $v['id_unik'],
							'is_locked' => $v['is_locked'],
							'nama_label' => $v['nama_label'],
							'status' => $v['status'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_prioritas_kokab', $opsi, array(
								'id_label_kokab' => $v['id_label_kokab'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_prioritas_kokab', $opsi);
						}
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Label/Prioritas Kabupaten Kota Salah!';
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

	public function singkron_label_prov()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Label/Prioritas Provinsi!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['label'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$label = json_decode(stripslashes(html_entity_decode($_POST['label'])), true);
					} else {
						$label = $_POST['label'];
					}
					$wpdb->update('data_prioritas_prov', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($label as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id_label_prov 
							from data_prioritas_prov 
							where tahun_anggaran=%d 
								AND id_label_prov=%d
						", $_POST['tahun_anggaran'], $v['id_label_prov']));
						$opsi = array(
							'id_prioritas' => $v['id_prioritas'],
							'id_label_prov' => $v['id_label_prov'],
							'teks_prioritas' => $v['teks_prioritas'],
							'id_unik' => $v['id_unik'],
							'is_locked' => $v['is_locked'],
							'nama_label' => $v['nama_label'],
							'status' => $v['status'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_prioritas_prov', $opsi, array(
								'id_label_prov' => $v['id_label_prov'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_prioritas_prov', $opsi);
						}
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Label/Prioritas Provinsi Salah!';
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

	public function singkron_label_pusat()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Label/Prioritas Pusat!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['label'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$label = json_decode(stripslashes(html_entity_decode($_POST['label'])), true);
					} else {
						$label = $_POST['label'];
					}
					$wpdb->update('data_prioritas_pusat', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($label as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id_label_pusat 
							from data_prioritas_pusat 
							where tahun_anggaran=%d 
								AND id_label_pusat=%d
						", $_POST['tahun_anggaran'], $v['id_label_pusat']));
						$opsi = array(
							'id_prioritas' => $v['id_prioritas'],
							'id_label_pusat' => $v['id_label_pusat'],
							'teks_prioritas' => $v['teks_prioritas'],
							'id_unik' => $v['id_unik'],
							'is_locked' => $v['is_locked'],
							'nama_label' => $v['nama_label'],
							'tahun_awal' => $v['tahun_awal'],
							'tahun_akhir' => $v['tahun_akhir'],
							'set_urut' => $v['set_urut'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_prioritas_pusat', $opsi, array(
								'id_label_pusat' => $v['id_label_pusat'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_prioritas_pusat', $opsi);
						}
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Label/Prioritas Pusat Salah!';
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

	public function singkron_label_giat()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Master Label Giat!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['label'])) {
					if (!empty($_POST['type']) && $_POST['type'] == 'ri') {
						$label = json_decode(stripslashes(html_entity_decode($_POST['label'])), true);
						// $label = $_POST['label'];		
					} else {
						$label = $_POST['label'];
					}
					$wpdb->update('data_label_giat', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($label as $k => $v) {
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id_label_giat 
							from data_label_giat 
							where tahun_anggaran=%d 
								AND id_label_giat=%d
						", $_POST['tahun_anggaran'], $v['id_label_giat']));
						$opsi = array(
							'id_label_giat' => $v['id_label_giat'],
							'id_unik' => $v['id_unik'],
							'is_locked' => $v['is_locked'],
							'nama_label' => $v['nama_label'],
							'status' => $v['status'],
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_label_giat', $opsi, array(
								'id_label_giat' => $v['id_label_giat'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_label_giat', $opsi);
						}
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Master Label Giat Salah!';
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

	function cekKode($kata)
	{
		$pola = '/^[0-9.]+$/';
		return preg_match($pola, $kata);
	}

	public function to_number($value = '')
	{
		return str_replace(',', '.', $value);
	}

	function get_option_complex($key, $type)
	{
		global $wpdb;
		$ret = $wpdb->get_results('select option_name, option_value from ' . $wpdb->prefix . 'options where option_name like \'' . $key . '|%\'', ARRAY_A);
		$res = array();
		$types = array();
		foreach ($ret as $v) {
			$k = explode('|', $v['option_name']);
			$column = $k[1];
			$group = $k[3];
			if ($column == '') {
				$types[$group] = $v['option_value'];
			}
		}
		foreach ($ret as $v) {
			$k = explode('|', $v['option_name']);
			$column = $k[1];
			$loop = $k[2];
			$group = $k[3];
			if ($column != '') {
				if (
					isset($types[$loop])
					&& $type == $types[$loop]
				) {
					if (empty($res[$loop])) {
						$res[$loop] = array();
					}
					$res[$loop][$column] = $v['option_value'];
				}
			}
		}
		return $res;
	}

	function current_url($url = false)
	{
		if (empty($url)) {
			$url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$query = $_GET;
		} else {
			$parts = parse_url($url, PHP_URL_QUERY);
			parse_str($parts, $query);
		}
		$current_url = explode('?', $url);
		$current_url = $current_url[0];
		if (!empty($query) && !empty($query['page_id'])) {
			$current_url .= '?page_id=' . $query['page_id'];
		}
		return $current_url;
	}

	public function get_datatable_data_spp_sipd()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil get datatable SPP SIPD!',
			'data'  => array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$user_id = um_user('ID');
				$user_meta = get_userdata($user_id);
				$params = $columns = $totalRecords = $data = array();
				$params = $_REQUEST;
				$columns = array(
					0 => 'tahunSpp',
					1 => 'nomorSpp',
					2 => 'nilaiSpp',
					3 => 'tanggalSpp',
					4 => 'keteranganSpp',
					5 => 'nilaiDisetujuiSpp',
					6 => 'tanggalDisetujuiSpp',
					7 => 'jenisSpp',
					8 => 'verifikasiSpp',
					9 => 'keteranganVerifikasi',
					10 => 'kunciRekening',
					11 => 'alamatPenerimaSpp',
					12 => 'bankPenerimaSpp',
					13 => 'nomorRekeningPenerimaSpp',
					14 => 'npwpPenerimaSpp',
					15 => 'jenisLs',
					16 => 'statusPerubahan',
					17 => 'kodeDaerah',
					18 => 'tanggal_otorisasi',
					19 => 'bulan_gaji',
					20 => 'nama_pegawai_pptk',
					21 => 'nip_pegawai_pptk',
					22 => 'status_tahap',
					23 => 'kode_tahap',
					24 => 'bulan_tpp',
					25 => 'nomor_pengajuan_tu',
					26 => 'tipe',
					27 => 'id',
					28 => 'idSpp'
				);
				$where = $sqlTot = $sqlRec = "";

				// check search value exist
				if (!empty($params['search']['value'])) {
					$search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
					$where .= " AND (nomorSpp LIKE " . $search_value;
					$where .= " OR keteranganSpp LIKE " . $search_value . ")";
				}

				if (!empty($_POST['id_skpd']) && !empty($_POST['tahun_anggaran'])) {
					$where .= $wpdb->prepare(' AND idSkpd=%s AND tahun_anggaran =%d', $_POST['id_skpd'], $_POST['tahun_anggaran']);
				}

				// getting total number records without any search
				$sql_tot = "SELECT count(id) as jml FROM `data_spp_sipd`";
				$sql = "SELECT " . implode(', ', $columns) . " FROM `data_spp_sipd`";
				$where_first = " WHERE 1=1";

				$sqlTot .= $sql_tot . $where_first;
				$sqlRec .= $sql . $where_first;
				if (isset($where) && $where != '') {
					$sqlTot .= $where;
					$sqlRec .= $where;
				}

				$limit = '';
				if ($params['length'] != -1) {
					$limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
				}
				$sqlRec .=  " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . str_replace("'", '', $wpdb->prepare('%s', $params['order'][0]['dir'])) . ",  tanggal_otorisasi DESC " . $limit;

				$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				$totalRecords = $queryTot[0]['jml'];
				$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

				foreach ($queryRecords as $recKey => $recVal) {
					$queryRecords[$recKey]['nomorSpp'] = '<a href="javascript:void(0);" onclick="modalDetailSpp(' . $recVal['idSpp'] . ')">' . $recVal['nomorSpp'] . '</a>';

					$queryRecords[$recKey]['nilaiSpp'] = number_format($recVal['nilaiSpp'], 0, ",", ".");
					$queryRecords[$recKey]['nilaiDisetujuiSpp'] = number_format($recVal['nilaiDisetujuiSpp'], 0, ",", ".");
				}

				$json_data = array(
					"draw"            => intval($params['draw']),
					"recordsTotal"    => intval($totalRecords),
					"recordsFiltered" => intval($totalRecords),
					"data"            => $queryRecords,
					"sql"             => $sqlRec
				);

				die(json_encode($json_data));
			} else {
				$return = array(
					'status' => 'error',
					'message'   => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'   => 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_datatable_data_sp2d_sipd()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil get datatable SP2D SIPD!',
			'data'  => array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_sp_2_d = $_POST['id_sp_2_d'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$user_id = um_user('ID');
				$user_meta = get_userdata($user_id);
				$params = $columns = $totalRecords = $data = array();
				$params = $_REQUEST;
				$columns = array(
					0 => 'id_sp_2_d',
					1 => 'nomor_sp_2_d',
					2 => 'tanggal_sp_2_d',
					3 => 'keterangan_sp_2_d',
					4 => 'jenis_sp_2_d',
					5 => 'keterangan_transfer_sp_2_d',
					6 => 'keterangan_verifikasi_sp_2_d',
					7 => 'kode_sub_skpd',
					8 => 'metode',
					9 => 'nama_bank',
					10 => 'nama_bud_kbud ',
					11 => 'nama_rek_bp_bpp',
					12 => 'nama_skpd',
					13 => 'nama_sub_skpd',
					14 => 'nilai_materai_sp_2_d',
					15 => 'nilai_sp_2_d',
					16 => 'nip_bud_kbud',
					17 => 'no_rek_bp_bpp',
					18 => 'nomor_jurnal',
					19 => 'nomor_spm',
					20 => 'tahun_gaji',
					21 => 'tahun_tpp',
					22 => 'tanggal_sp_2_d',
					23 => 'tanggal_spm',
					24 => 'tahun_anggaran',
					25 => 'id'
				);
				$where = $sqlTot = $sqlRec = "";

				// check search value exist
				if (!empty($params['search']['value'])) {
					$where .= " AND ( id_sp_2_d LIKE " . $wpdb->prepare('%s', "%" . $params['search']['value'] . "%") . ")";
					$where .= " OR ( nomor_sp_2_d LIKE " . $wpdb->prepare('%s', "%" . $params['search']['value'] . "%") . ")";
				}

				if (!empty($_POST['id_skpd']) && !empty($_POST['tahun_anggaran'])) {
					$where .= $wpdb->prepare(' AND id_skpd=%s AND tahun_anggaran =%d', $_POST['id_skpd'], $_POST['tahun_anggaran']);
				}

				// getting total number records without any search
				$sql_tot = "SELECT count(id) as jml FROM `data_sp2d_sipd_ri`";
				$sql = "SELECT " . implode(', ', $columns) . " FROM `data_sp2d_sipd_ri`";
				$where_first = " WHERE 1=1";

				$sqlTot .= $sql_tot . $where_first;
				$sqlRec .= $sql . $where_first;
				if (isset($where) && $where != '') {
					$sqlTot .= $where;
					$sqlRec .= $where;
				}

				$limit = '';
				if ($params['length'] != -1) {
					$limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
				}

				$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				$totalRecords = $queryTot[0]['jml'];
				$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

				$nomor_sp2d = '--';
				foreach ($queryRecords as $recKey => $recVal) {
					if ($recVal['nomor_sp_2_d'] != null) {
						$nomor_sp2d = $recVal['nomor_sp_2_d'];
					}
					$queryRecords[$recKey]['nomor_sp_2_d'] = '<a href="#" onclick="showsp2d(' . $recVal['id_sp_2_d'] . ')">' . $nomor_sp2d . '</a>';
					$queryRecords[$recKey]['nilai_sp_2_d'] = number_format($recVal['nilai_sp_2_d'], 0, ",", ".");
				}

				$json_data = array(
					"draw"            => intval($params['draw']),
					"recordsTotal"    => intval($totalRecords),
					"recordsFiltered" => intval($totalRecords),
					"data"            => $queryRecords,
					"sql"             => $sqlRec
				);

				die(json_encode($json_data));
			} else {
				$return = array(
					'status' => 'error',
					'message'   => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'   => 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_datatable_data_stbp_sipd()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil get datatable STBP SIPD!',
			'data'  => array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$user_id = um_user('ID');
				$user_meta = get_userdata($user_id);
				$params = $columns = $totalRecords = $data = array();
				$params = $_REQUEST;
				$columns = array(
					0 => 'nomor_stbp',
					1 => 'no_rekening',
					2 => 'metode_penyetoran',
					3 => 'nilai_stbp',
					4 => 'keterangan_stbp',
					5 => 'is_verifikasi_stbp',
					6 => 'is_otorisasi_stbp',
					7 => 'is_validasi_stbp',
					8 => 'tanggal_stbp',
					9 => 'is_sts',
					10 => 'status',
					11 => 'id',
					12 => 'id_stbp'
				);
				$where = $sqlTot = $sqlRec = "";

				// check search value exist
				if (!empty($params['search']['value'])) {
					$search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
					$where .= " AND (nomor_stbp LIKE " . $search_value;
					$where .= " OR keterangan_stbp LIKE " . $search_value . ")";
				}

				if (!empty($_POST['id_skpd']) && !empty($_POST['tahun_anggaran'])) {
					$where .= $wpdb->prepare(' AND id_skpd=%s AND tahun_anggaran =%d', $_POST['id_skpd'], $_POST['tahun_anggaran']);
				}

				// getting total number records without any search
				$sql_tot = "SELECT count(id) as jml FROM `data_stbp_sipd`";
				$sql = "SELECT " . implode(', ', $columns) . " FROM `data_stbp_sipd`";
				$where_first = " WHERE 1=1";

				$sqlTot .= $sql_tot . $where_first;
				$sqlRec .= $sql . $where_first;
				if (isset($where) && $where != '') {
					$sqlTot .= $where;
					$sqlRec .= $where;
				}

				$limit = '';
				if ($params['length'] != -1) {
					$limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
				}
				$sqlRec .=  " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . str_replace("'", '', $wpdb->prepare('%s', $params['order'][0]['dir'])) . ",  tanggal_stbp DESC " . $limit;

				$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				$totalRecords = $queryTot[0]['jml'];
				$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

				foreach ($queryRecords as $recKey => $recVal) {
					$queryRecords[$recKey]['nomor_stbp'] = '<a href="javascript:void(0);" onclick="modalDetailStbp(' . $recVal['id_stbp'] . ')">' . $recVal['nomor_stbp'] . '</a>';

					$queryRecords[$recKey]['nilai_stbp'] = number_format($recVal['nilai_stbp'], 0, ",", ".");;
				}

				$json_data = array(
					"draw"            => intval($params['draw']),
					"recordsTotal"    => intval($totalRecords),
					"recordsFiltered" => intval($totalRecords),
					"data"            => $queryRecords,
					"sql"             => $sqlRec
				);

				die(json_encode($json_data));
			} else {
				$return = array(
					'status' => 'error',
					'message'   => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'   => 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_data_stbp_sipd_detail()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil Get STBP SIPD Detail!',
			'data' => array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_stbp = $_POST['id_stbp'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$detail_results = $wpdb->get_results(
					$wpdb->prepare(
						'
						SELECT 
							*
						FROM data_stbp_sipd_detail
						WHERE id_stbp=%s
						  AND tahun_anggaran=%d
						  AND active=1
						',
						$id_stbp,
						$tahun_anggaran
					),
					ARRAY_A
				);
				$ret['data'] = $detail_results;
				if (empty($detail_results)) {
					$ret['status'] = 'error';
					$ret['message'] = 'Data detail STBP kosong!';
				}
			} else {
				$ret['status']  = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status']  = 'error';
			$ret['message'] = 'Format Salah!';
		}

		die(json_encode($ret));
	}

	public function get_datatable_data_tbp_sipd()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil get datatable TBP SIPD!',
			'data'  => array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$user_id = um_user('ID');
				$user_meta = get_userdata($user_id);
				$params = $columns = $totalRecords = $data = array();
				$params = $_REQUEST;
				$columns = array(
					0 => 'tahun_anggaran',
					1 => 'nomor_tbp',
					2 => 'nilai_tbp',
					3 => 'tanggal_tbp',
					4 => 'keterangan_tbp',
					5 => 'nilai_materai_tbp',
					6 => 'nomor_kwitansi',
					7 => 'jenis_tbp',
					8 => 'jenis_ls_tbp',
					9 => 'nomor_jurnal',
					10 => 'is_kunci_rekening_tbp',
					11 => 'is_panjar',
					12 => 'is_lpj',
					13 => 'is_rekanan_upload',
					14 => 'status_aklap',
					15 => 'metode',
					16 => 'total_pertanggungjawaban',
					17 => 'kode_skpd',
					18 => 'nama_skpd',
					19 => 'kode_sub_skpd',
					20 => 'nama_sub_skpd',
					21 => 'id',
					22 => 'id_tbp'
				);
				$where = $sqlTot = $sqlRec = "";

				// check search value exist
				if (!empty($params['search']['value'])) {
					$search_value = $wpdb->prepare('%s', "%" . $params['search']['value'] . "%");
					$where .= " AND (nomor_tbp LIKE " . $search_value;
					$where .= " OR keterangan_tbp LIKE " . $search_value . ")";
				}

				if (!empty($_POST['id_skpd']) && !empty($_POST['tahun_anggaran'])) {
					$where .= $wpdb->prepare(' AND id_skpd=%s AND tahun_anggaran =%d', $_POST['id_skpd'], $_POST['tahun_anggaran']);
				}

				// getting total number records without any search
				$sql_tot = "SELECT count(id) as jml FROM `data_tbp_sipd`";
				$sql = "SELECT " . implode(', ', $columns) . " FROM `data_tbp_sipd`";
				$where_first = " WHERE 1=1";

				$sqlTot .= $sql_tot . $where_first;
				$sqlRec .= $sql . $where_first;
				if (isset($where) && $where != '') {
					$sqlTot .= $where;
					$sqlRec .= $where;
				}

				$limit = '';
				if ($params['length'] != -1) {
					$limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
				}
				$sqlRec .=  " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . str_replace("'", '', $wpdb->prepare('%s', $params['order'][0]['dir'])) . ",  tanggal_tbp DESC " . $limit;

				$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				$totalRecords = $queryTot[0]['jml'];
				$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

				foreach ($queryRecords as $recKey => $recVal) {
					$queryRecords[$recKey]['nomor_tbp'] = '<a href="javascript:void(0);" onclick="modalDetailTbp(' . $recVal['id_tbp'] . ')">' . $recVal['nomor_tbp'] . '</a>';

					$queryRecords[$recKey]['nilai_tbp'] = number_format($recVal['nilai_tbp'], 0, ",", ".");
					$queryRecords[$recKey]['total_pertanggungjawaban'] = number_format($recVal['total_pertanggungjawaban'], 0, ",", ".");
				}

				$json_data = array(
					"draw"            => intval($params['draw']),
					"recordsTotal"    => intval($totalRecords),
					"recordsFiltered" => intval($totalRecords),
					"data"            => $queryRecords,
					"sql"             => $sqlRec
				);

				die(json_encode($json_data));
			} else {
				$return = array(
					'status' => 'error',
					'message'   => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'   => 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_data_tbp_sipd_detail()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil Get TBP SIPD Detail!',
			'data' => array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_tbp = $_POST['id_tbp'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$detail_results = $wpdb->get_results(
					$wpdb->prepare(
						'
						SELECT 
							*
						FROM data_tbp_sipd_detail
						WHERE id_tbp=%s
						  AND tahun_anggaran=%d
						  AND active=1
						',
						$id_tbp,
						$tahun_anggaran
					),
					ARRAY_A
				);
				$ret['data'] = $detail_results;
				if (empty($detail_results)) {
					$ret['status'] = 'error';
					$ret['message'] = 'Data detail TBP kosong!';
				}
			} else {
				$ret['status']  = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status']  = 'error';
			$ret['message'] = 'Format Salah!';
		}

		die(json_encode($ret));
	}

	public function get_data_sp2d_sipd()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil Get SPD SIPD Detail!',
			'data' => array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_sp_2_d = $_POST['id_sp_2_d'];
				$id_sp2d = $_POST['id_sp2d'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sp2d = $wpdb->get_row(
					$wpdb->prepare(
						'
                        SELECT 
                            *
                        FROM data_sp2d_sipd_ri
                        WHERE id_sp_2_d=%s
                          AND tahun_anggaran=%d
                          AND active=1
                        ',
						$id_sp_2_d,
						$tahun_anggaran
					),
					ARRAY_A
				);
				$sp2d['detail'] = $wpdb->get_results($wpdb->prepare('
                    SELECT
                        *
                    FROM data_sp2d_sipd_detail
                    WHERE id_sp_2_d = %d
                        AND active=1
                        AND tahun_anggaran=%d
                ', $id_sp_2_d, $tahun_anggaran), ARRAY_A);
				$sp2d['potongan'] = $wpdb->get_results($wpdb->prepare('
                    SELECT
                        *
                    FROM data_sp2d_sipd_detail_potongan
                    WHERE id_sp_2_d = %d
                        AND active=1
                        AND tahun_anggaran=%d
                ', $id_sp_2_d, $tahun_anggaran), ARRAY_A);
				$ret['data'] = $sp2d;
				if (empty($sp2d)) {
					$ret['status'] = 'error';
					$ret['message'] = 'Data dengan ID SPD ' . $id_sp_2_d . ' Kosong / Tidak Lengkap!';
				}
				$ret['sql'] = $wpdb->last_query;
			} else {
				$ret['status']  = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status']  = 'error';
			$ret['message'] = 'Format Salah!';
		}

		die(json_encode($ret));
	}

	public function get_data_spp_sipd_detail()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil Get SPP SIPD Detail!',
			'data' => array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$id_spp = $_POST['id_spp'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$detail_results = $wpdb->get_results(
					$wpdb->prepare(
						'
						SELECT 
							*
						FROM data_spp_sipd_ri_detail
						WHERE id_spp=%s
						  AND tahun_anggaran=%d
						  AND active=1
						',
						$id_spp,
						$tahun_anggaran
					),
					ARRAY_A
				);
				$ret['data'] = $detail_results;
				if (empty($detail_results)) {
					$ret['status'] = 'error';
					$ret['message'] = 'Data detail SPP kosong!';
				}
			} else {
				$ret['status']  = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status']  = 'error';
			$ret['message'] = 'Format Salah!';
		}

		die(json_encode($ret));
	}

	public function get_data_spd_sipd()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil Get SPD SIPD Detail!',
			'data' => array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$idSpd = $_POST['idSpd'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$spd = $wpdb->get_row(
					$wpdb->prepare(
						'
						SELECT 
							*
						FROM data_spd_sipd
						WHERE idSpd=%s
						  AND tahun_anggaran=%d
						  AND active=1
						',
						$idSpd,
						$tahun_anggaran
					),
					ARRAY_A
				);
				$spd['detail'] = $wpdb->get_results($wpdb->prepare('
					SELECT
						*
					FROM data_spd_sipd_detail
					WHERE idSpd = %d
						AND active=1
						AND tahun_anggaran=%d
				', $idSpd, $tahun_anggaran), ARRAY_A);
				$ret['data'] = $spd;
				if (empty($spd)) {
					$ret['status'] = 'error';
					$ret['message'] = 'Data dengan ID SPD ' . $idSpd . ' Kosong / Tidak Lengkap!';
				}
				$ret['sql'] = $wpdb->last_query;
			} else {
				$ret['status']  = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status']  = 'error';
			$ret['message'] = 'Format Salah!';
		}

		die(json_encode($ret));
	}

	public function get_datatable_data_spm_sipd()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil get datatable SPP SIPD!',
			'data'  => array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$user_id = um_user('ID');
				$user_meta = get_userdata($user_id);
				$params = $columns = $totalRecords = $data = array();
				$params = $_REQUEST;
				$columns = array(
					0	=> 'idSpm',
					1	=> 'idSpp',
					2	=> 'nomorSpm',
					3	=> 'idDetailSpm',
					4	=> 'nomorSpp',
					5	=> 'nilaiSpp',
					6	=> 'tanggalSpp',
					7	=> 'keteranganSpp',
					8	=> 'nilaiDisetujuiSpp',
					9	=> 'tanggalDisetujuiSpp',
					10	=> 'jenisSpp',
					11	=> 'verifikasiSpp',
					12	=> 'keteranganVerifikasi',
					13	=> 'idSpd',
					14	=> 'idPengesahanSpj',
					15	=> 'kunciRekening',
					16	=> 'alamatPenerimaSpp',
					17	=> 'bankPenerimaSpp',
					18	=> 'nomorRekeningPenerimaSpp',
					19	=> 'npwpPenerimaSpp',
					20	=> 'jenisLs',
					21	=> 'isUploaded',
					22	=> 'idKontrak',
					23	=> 'idBA',
					24	=> 'isSpm',
					25	=> 'statusPerubahan',
					26	=> 'isDraft',
					27	=> 'isGaji',
					28	=> 'is_sptjm',
					29	=> 'tanggal_otorisasi',
					30	=> 'is_otorisasi',
					31	=> 'bulan_gaji',
					32	=> 'id_pegawai_pptk',
					33	=> 'nama_pegawai_pptk',
					34	=> 'nip_pegawai_pptk',
					35	=> 'kode_tahap',
					36	=> 'is_tpp',
					37	=> 'bulan_tpp',
					38	=> 'id_pengajuan_tu',
					39	=> 'nomor_pengajuan_tu',
					40	=> 'tanggalSpm',
					41	=> 'keteranganSpm',
					42	=> 'verifikasiSpm',
					43	=> 'jenisSpm',
					44	=> 'nilaiSpm',
					45	=> 'id_jadwal',
					46	=> 'id_tahap',
					47	=> 'status_tahap',
					48	=> 'isOtorisasi',
					49	=> 'keteranganVerifikasiSpm',
					50	=> 'tanggalVerifikasiSpm',
					51	=> 'tanggalOtorisasi',
					52	=> 'tahunSpp',
					53	=> 'id'
				);
				$where = $sqlTot = $sqlRec = "";

				// check search value exist
				if (!empty($params['search']['value'])) {
					$where .= " AND ( idSpm LIKE " . $wpdb->prepare('%s', "%" . $params['search']['value'] . "%") . ")";
					$where .= " OR ( nomorSpm LIKE " . $wpdb->prepare('%s', "%" . $params['search']['value'] . "%") . ")";
					$where .= " OR ( idSpp LIKE " . $wpdb->prepare('%s', "%" . $params['search']['value'] . "%") . ")";
					$where .= " OR ( nomorSpp LIKE " . $wpdb->prepare('%s', "%" . $params['search']['value'] . "%") . ")";
				}

				if (!empty($_POST['id_skpd']) && !empty($_POST['tahun_anggaran'])) {
					$where .= $wpdb->prepare(' AND id_skpd=%s AND tahun_anggaran =%d', $_POST['id_skpd'], $_POST['tahun_anggaran']);
				}

				// getting total number records without any search
				$sql_tot = "SELECT count(id) as jml FROM `data_spm_sipd`";
				$sql = "SELECT " . implode(', ', $columns) . " FROM `data_spm_sipd`";
				$where_first = " WHERE 1=1";

				$sqlTot .= $sql_tot . $where_first;
				$sqlRec .= $sql . $where_first;
				if (isset($where) && $where != '') {
					$sqlTot .= $where;
					$sqlRec .= $where;
				}

				$limit = '';
				if ($params['length'] != -1) {
					$limit = "  LIMIT " . $wpdb->prepare('%d', $params['start']) . " ," . $wpdb->prepare('%d', $params['length']);
				}

				$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				$totalRecords = $queryTot[0]['jml'];
				$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

				$nomor_spm = '--';
				foreach ($queryRecords as $recKey => $recVal) {
					if ($recVal['nomorSpm'] != null) {
						$nomor_spm = $recVal['nomorSpm'];
					}
					$queryRecords[$recKey]['nomorSpm'] = '<a href="#" onclick="showspm(' . $recVal['idSpm'] . ')">' . $nomor_spm . '</a>';
					$queryRecords[$recKey]['nilaiSpp'] = number_format($recVal['nilaiSpp'], 0, ",", ".");
				}

				$json_data = array(
					"draw"            => intval($params['draw']),
					"recordsTotal"    => intval($totalRecords),
					"recordsFiltered" => intval($totalRecords),
					"data"            => $queryRecords,
					"sql"             => $sqlRec
				);

				die(json_encode($json_data));
			} else {
				$return = array(
					'status' => 'error',
					'message'   => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message'   => 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_data_spm_sipd()
	{
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil Get SPD SIPD Detail!',
			'data' => array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$idSpm = $_POST['idSpm'];
				$id_spm = $_POST['id_spm'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$spm = $wpdb->get_row(
					$wpdb->prepare(
						'
						SELECT 
							*
						FROM data_spm_sipd
						WHERE idSpm=%s
						  AND tahun_anggaran=%d
						  AND active=1
						',
						$idSpm,
						$tahun_anggaran
					),
					ARRAY_A
				);
				$spm['detail'] = $wpdb->get_results($wpdb->prepare('
					SELECT
						*
					FROM data_spm_sipd_detail
					WHERE id_spm = %d
						AND active=1
						AND tahun_anggaran=%d
				', $id_spm, $tahun_anggaran), ARRAY_A);
				$spm['potongan'] = $wpdb->get_results($wpdb->prepare('
                    SELECT
                        *
                    FROM data_spm_sipd_detail_potongan
                    WHERE id_spm = %d
                        AND active=1
                        AND tahun_anggaran=%d
                ', $id_spm, $tahun_anggaran), ARRAY_A);
				$ret['data'] = $spm;
				if (empty($spm)) {
					$ret['status'] = 'error';
					$ret['message'] = 'Data dengan ID SPD ' . $id_spm . ' Kosong / Tidak Lengkap!';
				}
				$ret['sql'] = $wpdb->last_query;
			} else {
				$ret['status']  = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status']  = 'error';
			$ret['message'] = 'Format Salah!';
		}

		die(json_encode($ret));
	}

	public function submit_lock_schedule_monev()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data' => array()
		);
		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (empty($_POST['id_jadwal'])) {
					$return = array(
						'status' => 'error',
						'message' => 'Id Jadwal Kosong!'
					);
				}

				if (in_array("administrator", $user_meta->roles)) {
					$id_jadwal = trim(htmlspecialchars($_POST['id_jadwal']));

					$data_this_id = $wpdb->get_row(
						$wpdb->prepare('
							SELECT * 
							FROM data_jadwal_lokal 
							WHERE id_jadwal = %d
							', $id_jadwal),
						ARRAY_A
					);

					$timezone = get_option('timezone_string');
					if (preg_match("/Asia/i", $timezone)) {
						date_default_timezone_set($timezone);
					} else {
						$return = array(
							'status' => 'error',
							'message' => "Pengaturan timezone salah. Pilih salah satu kota di zona waktu yang sama dengan anda, antara lain: 'Jakarta', 'Makasar', 'Jayapura'"
						);
						die(json_encode($return));
					}

					$dateTime = new DateTime();
					$time_now = $dateTime->format('Y-m-d H:i:s');
					if ($time_now > $data_this_id['waktu_awal']) {
						$status_check = array(0, NULL, 2);
						if (in_array($data_this_id['status'], $status_check)) {
							// kunci jadwal
							$wpdb->update(
								'data_jadwal_lokal',
								array(
									'waktu_akhir' => $time_now,
									'status' => 1
								),
								array(
									'id_jadwal_lokal' => $id_jadwal
								)
							);

							//backup subkegiatan
							$this->delete_data_lokal_history('data_renstra_sub_kegiatan', $data_this_id['id_jadwal']);
							$columns_0 = array(
								'bidur_lock',
								'giat_lock',
								'id_bidang_urusan',
								'id_sub_giat',
								'id_giat',
								'id_misi',
								'id_program',
								'id_unik',
								'id_unik_indikator',
								'id_unit',
								'id_sub_unit',
								'id_visi',
								'id_indikator',
								'indikator',
								'id_indikator_usulan',
								'indikator_usulan',
								'is_locked',
								'is_locked_indikator',
								'kode_bidang_urusan',
								'kode_sub_giat',
								'kode_giat',
								'kode_program',
								'kode_sasaran',
								'kode_skpd',
								'kode_tujuan',
								'kode_unik_program',
								'nama_bidang_urusan',
								'nama_sub_giat',
								'nama_giat',
								'nama_program',
								'nama_skpd',
								'nama_sub_unit',
								'pagu_1',
								'pagu_2',
								'pagu_3',
								'pagu_4',
								'pagu_5',
								'pagu_1_usulan',
								'pagu_2_usulan',
								'pagu_3_usulan',
								'pagu_4_usulan',
								'pagu_5_usulan',
								'program_lock',
								'renstra_prog_lock',
								'sasaran_lock',
								'sasaran_teks',
								'satuan',
								'status',
								'target_1',
								'target_2',
								'target_3',
								'target_4',
								'target_5',
								'target_akhir',
								'target_awal',
								'satuan_usulan',
								'target_1_usulan',
								'target_2_usulan',
								'target_3_usulan',
								'target_4_usulan',
								'target_5_usulan',
								'target_akhir_usulan',
								'target_awal_usulan',
								'catatan_usulan',
								'catatan',
								'tujuan_lock',
								'tujuan_teks',
								'urut_sasaran',
								'urut_tujuan',
								'active',
								'update_at',
								'tahun_anggaran',
								'kode_kegiatan',
								'id_sub_giat_lama'
							);
							$sql_backup_data_renstra_sub_kegiatan = "
								INSERT INTO data_renstra_sub_kegiatan_history 
								(" . implode(',', $columns_0) . ", id_jadwal, id_asli) 
								SELECT " . implode(', ', $columns_0) . ", " . $data_this_id['id_jadwal'] . ", id as id_asli 
								FROM data_renstra_sub_kegiatan
							";
							$wpdb->query($sql_backup_data_renstra_sub_kegiatan);

							//backup kegiatan
							$this->delete_data_lokal_history('data_renstra_kegiatan', $data_this_id['id_jadwal']);
							$columns_1 = array(
								'bidur_lock',
								'giat_lock',
								'id_bidang_urusan',
								'id_giat',
								'id_misi',
								'id_program',
								'id_unik',
								'id_unik_indikator',
								'id_unit',
								'id_visi',
								'indikator',
								'indikator_usulan',
								'is_locked',
								'is_locked_indikator',
								'kode_bidang_urusan',
								'kode_giat',
								'kode_program',
								'kode_sasaran',
								'kode_skpd',
								'kode_tujuan',
								'kode_unik_program',
								'nama_bidang_urusan',
								'nama_giat',
								'nama_program',
								'nama_skpd',
								'pagu_1',
								'pagu_2',
								'pagu_3',
								'pagu_4',
								'pagu_5',
								'pagu_1_usulan',
								'pagu_2_usulan',
								'pagu_3_usulan',
								'pagu_4_usulan',
								'pagu_5_usulan',
								'program_lock',
								'renstra_prog_lock',
								'sasaran_lock',
								'sasaran_teks',
								'satuan',
								'status',
								'target_1',
								'target_2',
								'target_3',
								'target_4',
								'target_5',
								'target_akhir',
								'target_awal',
								'satuan_usulan',
								'target_1_usulan',
								'target_2_usulan',
								'target_3_usulan',
								'target_4_usulan',
								'target_5_usulan',
								'target_akhir_usulan',
								'target_awal_usulan',
								'catatan_usulan',
								'catatan',
								'tujuan_lock',
								'tujuan_teks',
								'urut_sasaran',
								'urut_tujuan',
								'active',
								'update_at',
								'tahun_anggaran'
							);
							$sql_backup_data_renstra_kegiatan = "
								INSERT INTO data_renstra_kegiatan_history 
								(" . implode(', ', $columns_1) . ", id_jadwal, id_asli) 
								SELECT " . implode(', ', $columns_1) . ", " . $data_this_id['id_jadwal'] . ", id as id_asli 
								FROM data_renstra_kegiatan";
							$wpdb->query($sql_backup_data_renstra_kegiatan);

							//backup program
							$this->delete_data_lokal_history('data_renstra_program', $data_this_id['id_jadwal']);
							$columns_2 = array(
								'bidur_lock',
								'id_bidang_urusan',
								'id_misi',
								'id_program',
								'id_unik',
								'id_unik_indikator',
								'id_unit',
								'id_visi',
								'indikator',
								'indikator_usulan',
								'is_locked',
								'is_locked_indikator',
								'kode_bidang_urusan',
								'kode_program',
								'kode_sasaran',
								'kode_skpd',
								'kode_tujuan',
								'nama_bidang_urusan',
								'nama_program',
								'nama_skpd',
								'pagu_1',
								'pagu_2',
								'pagu_3',
								'pagu_4',
								'pagu_5',
								'pagu_1_usulan',
								'pagu_2_usulan',
								'pagu_3_usulan',
								'pagu_4_usulan',
								'pagu_5_usulan',
								'program_lock',
								'sasaran_lock',
								'sasaran_teks',
								'satuan',
								'status',
								'target_1',
								'target_2',
								'target_3',
								'target_4',
								'target_5',
								'target_akhir',
								'target_awal',
								'satuan_usulan',
								'target_1_usulan',
								'target_2_usulan',
								'target_3_usulan',
								'target_4_usulan',
								'target_5_usulan',
								'target_akhir_usulan',
								'target_awal_usulan',
								'catatan_usulan',
								'catatan',
								'tujuan_lock',
								'tujuan_teks',
								'urut_sasaran',
								'urut_tujuan',
								'active',
								'update_at',
								'tahun_anggaran'
							);
							$sql_backup_data_renstra_program = "
								INSERT INTO data_renstra_program_history (" . implode(', ', $columns_2) . ", id_jadwal, id_asli) 
								SELECT " . implode(', ', $columns_2) . ", " . $data_this_id['id_jadwal'] . ", id as id_asli 
								FROM data_renstra_program
							";
							$wpdb->query($sql_backup_data_renstra_program);

							//backup sasaran
							$this->delete_data_lokal_history('data_renstra_sasaran', $data_this_id['id_jadwal']);
							$columns_3 = array(
								'bidur_lock',
								'id_bidang_urusan',
								'id_misi',
								'id_unit',
								'id_unik_indikator',
								'id_unik',
								'id_visi',
								'indikator_teks',
								'indikator_teks_usulan',
								'is_locked',
								'is_locked_indikator',
								'kode_bidang_urusan',
								'kode_skpd',
								'kode_tujuan',
								'nama_bidang_urusan',
								'nama_skpd',
								'sasaran_teks',
								'satuan',
								'status',
								'target_1',
								'target_2',
								'target_3',
								'target_4',
								'target_5',
								'target_akhir',
								'target_awal',
								'satuan_usulan',
								'target_1_usulan',
								'target_2_usulan',
								'target_3_usulan',
								'target_4_usulan',
								'target_5_usulan',
								'target_akhir_usulan',
								'target_awal_usulan',
								'catatan_usulan',
								'catatan',
								'tujuan_lock',
								'tujuan_teks',
								'urut_sasaran',
								'urut_tujuan',
								'active',
								'update_at',
								'tahun_anggaran'
							);
							$sql_backup_data_renstra_sasaran = "
								INSERT INTO data_renstra_sasaran_history (" . implode(', ', $columns_3) . ", id_jadwal, id_asli) 
								SELECT " . implode(', ', $columns_3) . ", " . $data_this_id['id_jadwal'] . ", id as id_asli 
								FROM data_renstra_sasaran
							";
							$wpdb->query($sql_backup_data_renstra_sasaran);

							//backup tujuan
							$this->delete_data_lokal_history('data_renstra_tujuan', $data_this_id['id_jadwal']);
							$columns_4 = array(
								'bidur_lock',
								'id_bidang_urusan',
								'id_unik',
								'id_unik_indikator',
								'id_unit',
								'indikator_teks',
								'indikator_teks_usulan',
								'is_locked',
								'is_locked_indikator',
								'kode_bidang_urusan',
								'kode_sasaran_rpjm',
								'kode_skpd',
								'nama_bidang_urusan',
								'nama_skpd',
								'satuan',
								'status',
								'target_1',
								'target_2',
								'target_3',
								'target_4',
								'target_5',
								'target_akhir',
								'target_awal',
								'satuan_usulan',
								'target_1_usulan',
								'target_2_usulan',
								'target_3_usulan',
								'target_4_usulan',
								'target_5_usulan',
								'target_akhir_usulan',
								'target_awal_usulan',
								'catatan_usulan',
								'catatan',
								'catatan_tujuan',
								'tujuan_teks',
								'urut_tujuan',
								'active',
								'update_at',
								'tahun_anggaran'
							);
							$sql_backup_data_renstra_tujuan = "
								INSERT INTO data_renstra_tujuan_history (" . implode(', ', $columns_4) . ", id_jadwal, id_asli) 
								SELECT " . implode(', ', $columns_4) . ", " . $data_this_id['id_jadwal'] . ", id as id_asli 
								FROM data_renstra_tujuan
							";
							$wpdb->query($sql_backup_data_renstra_tujuan);

							$return = array(
								'status' => 'success',
								'message' => 'Berhasil!',
								'data_input' => $queryRecords1
							);
						} else {
							$return = array(
								'status' => 'error',
								'message' => "User tidak diijinkan!\nData sudah dikunci!"
							);
						}
					} else {
						$return = array(
							'status' => 'error',
							'message' => "Penjadwalan belum dimulai!"
						);
					}
				} else {
					$return = array(
						'status' => 'error',
						'message' => "User tidak diijinkan!"
					);
				}
			} else {
				$return = array(
					'status' => 'error',
					'message' => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message' => 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_data_jadwal_wpsipd()
	{
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data' => array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['tipe_perencanaan'])) {
					$params = $_REQUEST;
					$columns = array(
						0 => 'id_jadwal_lokal',
						1 => 'nama',
						2 => 'waktu_awal',
						3 => 'waktu_akhir',
						4 => 'status',
						5 => 'tahun_anggaran',
						6 => 'relasi_perencanaan',
						7 => 'lama_pelaksanaan',
						8 => 'jenis_jadwal',
						9 => 'id_tipe',
						10 => 'tahun_akhir_anggaran'
					);
					$where = "";
					$tipe_perencanaan = $_POST['tipe_perencanaan'];
					$sqlTipe = $wpdb->get_results(
						$wpdb->prepare("
							SELECT * 
							FROM `data_tipe_perencanaan` 
							WHERE nama_tipe=%s
						", $tipe_perencanaan),
						ARRAY_A
					);
					if (empty($sqlTipe)) {
						$return = array(
							'status' => 'error',
							'message' => 'Data dengan tipe sesuai tidak ditemukan!'
						);
						die(json_encode($return));
					}

					if (!empty($_POST['tahun_anggaran'])) {
						$where .= $wpdb->prepare(" AND tahun_anggaran = %d", $_POST['tahun_anggaran']);
					}

					if (!empty($_POST['id_jadwal'])) {
						$where .= $wpdb->prepare(" AND id_jadwal_lokal = %d", $_POST['id_jadwal']);
					}

					// getting total number records without any search
					$sqlTot = "SELECT count(*) as jml FROM `data_jadwal_lokal` WHERE id_tipe =" . $sqlTipe[0]['id'];
					$sqlRec = "SELECT " . implode(', ', $columns) . " FROM `data_jadwal_lokal` WHERE id_tipe =" . $sqlTipe[0]['id'];
					if (isset($where) && $where != '') {
						$sqlTot .= $where;
						$sqlRec .= $where;
					}

					$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
					$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);
					$return['data'] = $queryRecords;

					die(json_encode($return));
				} else {
					$return = array(
						'status' => 'error',
						'message' => 'Tipe Perencanaan Kosong!'
					);
				}
			} else {
				$return = array(
					'status' => 'error',
					'message' => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$return = array(
				'status' => 'error',
				'message' => 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	function get_data_rincian_belanja_rka()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (!empty($_POST['kode_sbl']) && !empty($_POST['tahun_anggaran'])) {
					$ret['data'] = $wpdb->get_results(
						$wpdb->prepare("
							SELECT
        						*
    						FROM data_rka
    						WHERE active=1
							AND tahun_anggaran=%d
							AND kode_sbl=%s
							AND kode_akun=%s", $_POST['tahun_anggaran'], $_POST['kode_sbl'], $_POST['kode_akun']),
						ARRAY_A
					);
					// echo $wpdb->last_query;
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'param tidak boleh kosong!';
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

	public function aklap_lra($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		$input = shortcode_atts(array(
			'idlabelgiat' => '',
			'id_skpd' => false,
			'tahun_anggaran' => '2021',
		), $atts);

		// LRA
		// if ($input['lampiran'] == 1) {
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/penatausahaan/wpsipd-public-halaman-aklap-lra.php';
		// }
	}

	function get_serapan_anggaran_capaian_kinerja()
	{

		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil Get Serapan Anggaran dan Capaian Kinerja!',
			'data' => array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if (empty($_POST['tahun_anggaran'])) {
					$ret['status'] = 'error';
					$ret['message'] = 'Tahun Anggaran Kosong!';
					die(json_encode($ret));
				}
				if (empty($_POST['id_skpd'])) {
					$ret['status'] = 'error';
					$ret['message'] = 'Id SKPD Kosong!';
					die(json_encode($ret));
				}

				$sql = $wpdb->prepare("
						SELECT *
						FROM data_unit
						WHERE tahun_anggaran=%d
						AND id_skpd = %d
						AND active= 1
						ORDER BY id_skpd ASC
					", $_POST['tahun_anggaran'], $_POST['id_skpd']);
				$unit = $wpdb->get_results($sql, ARRAY_A);

				$bulan = date('m');
				$subkeg = $wpdb->get_results(
					$wpdb->prepare("
							SELECT
								k.*,
								k.id as id_sub_keg
							FROM data_sub_keg_bl k
							WHERE k.tahun_anggaran=%d
							AND k.active=1
							AND k.id_sub_skpd=%d
							AND k.pagu > 0
							ORDER BY k.kode_sub_giat ASC
						", $_POST['tahun_anggaran'], $unit[0]['id_skpd']),
					ARRAY_A
				);
				$data_all = array(
					'total'          => 0,
					'total_simda'    => 0,
					'triwulan_1'     => 0,
					'triwulan_2'     => 0,
					'triwulan_3'     => 0,
					'triwulan_4'     => 0,
					'rak_triwulan_1' => 0,
					'rak_triwulan_2' => 0,
					'rak_triwulan_3' => 0,
					'rak_triwulan_4' => 0,
					'realisasi'      => 0,
					'data'           => array()
				);
				$crb_cara_input_realisasi = get_option('_crb_cara_input_realisasi', 1);
				foreach ($subkeg as $kk => $sub) {
					$nama_keg = explode(' ', $sub['nama_sub_giat']);
					unset($nama_keg[0]);
					$nama_keg = implode(' ', $nama_keg);
					if ($crb_cara_input_realisasi == 1) {
						$total_simda = $sub['pagu_simda'];
					} else {
						$total_simda = $sub['pagu'];
					}
					$total_pagu = $sub['pagu'];
					$kode = explode('.', $sub['kode_sbl']);

					$rfk_all = $wpdb->get_results($wpdb->prepare("
						SELECT
							id,
							realisasi_anggaran,
							rak,
							bulan
						FROM data_rfk
						WHERE tahun_anggaran=%d
						AND id_skpd=%d
						AND kode_sbl=%s
						AND bulan<=%d 
						ORDER BY bulan ASC, id ASC
					", $_POST['tahun_anggaran'], $unit[0]['id_skpd'], $sub['kode_sbl'], $bulan), ARRAY_A);


					$triwulan_1 = 0;
					$triwulan_2 = 0;
					$triwulan_3 = 0;
					$triwulan_4 = 0;
					$rak_triwulan_1 = 0;
					$rak_triwulan_2 = 0;
					$rak_triwulan_3 = 0;
					$rak_triwulan_4 = 0;
					$realisasi_bulan_all = array();
					foreach ($rfk_all as $k => $v) {
						// jika bulan lebih kecil dari bulan sekarang dan realisasinya masih kosong maka realisasi dibuat sama dengan bulan sebelumnya agar realisasi tidak minus
						if (
							$v['bulan'] <= $bulan
							&& empty($v['realisasi_anggaran'])
							&& !empty($realisasi_bulan_all[$v['bulan'] - 1])
						) {
							$v['realisasi_anggaran'] = $realisasi_bulan_all[$v['bulan'] - 1];
							$wpdb->update('data_rfk', array(
								'realisasi_anggaran' => $v['realisasi_anggaran']
							), array('id' => $v['id']));
						}
						$realisasi_bulan_all[$v['bulan']] = $v['realisasi_anggaran'];
						$rak_bulan_all[$v['bulan']] = $v['rak'];
						if (!empty($v['realisasi_anggaran'])) {
							if ($v['bulan'] <= 3) {
								$triwulan_1 = $v['realisasi_anggaran'];
								$rak_triwulan_1 = $v['rak'];
							} else if ($v['bulan'] <= 6) {
								$triwulan_2 = $v['realisasi_anggaran'] - $realisasi_bulan_all[3];
								$rak_triwulan_2 = $v['rak'] - $rak_bulan_all[3];
							} else if ($v['bulan'] <= 9) {
								$triwulan_3 = $v['realisasi_anggaran'] - $realisasi_bulan_all[6];
								$rak_triwulan_3 = $v['rak'] - $rak_bulan_all[6];
							} else if ($v['bulan'] <= 12) {
								$triwulan_4 = $v['realisasi_anggaran'] - $realisasi_bulan_all[9];
								$rak_triwulan_4 = $v['rak'] - $rak_bulan_all[9];
							}
						}
					}
					$realisasi = $triwulan_1 + $triwulan_2 + $triwulan_3 + $triwulan_4;

					$kode_sbl_s = explode('.', $sub['kode_sbl']);
					if (empty($data_all['data'][$sub['kode_urusan']])) {
						$data_all['data'][$sub['kode_urusan']] = array(
							'nama'         => $sub['nama_urusan'],
							'total'      => 0,
							'triwulan_1' => 0,
							'triwulan_2' => 0,
							'triwulan_3' => 0,
							'triwulan_4' => 0,
							'total_simda' => 0,
							'realisasi'  => 0,
							'data'         => array()
						);
					}
					if (empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])) {
						$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
							'nama'         => $sub['nama_bidang_urusan'],
							'total'      => 0,
							'triwulan_1' => 0,
							'triwulan_2' => 0,
							'triwulan_3' => 0,
							'triwulan_4' => 0,
							'total_simda' => 0,
							'realisasi'  => 0,
							'data'         => array()
						);
					}

					$nama = explode(' ', $sub['nama_sub_giat']);
					if ($nama[0] !== $sub['kode_sub_giat']) {
						$kode_sub_giat_asli = explode('.', $sub['kode_sub_giat']);
					} else {
						$kode_sub_giat_asli = explode('.', $nama[0]);
					}

					//program
					if (empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']])) {
						$capaian_prog = $wpdb->get_results($wpdb->prepare(" SELECT * FROM data_capaian_prog_sub_keg WHERE tahun_anggaran=%d AND active=1 AND kode_sbl=%s AND capaianteks !='' ORDER BY id ASC ", $_POST['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);

						$kode_sbl = $kode_sbl_s[0] . '.' . $kode_sbl_s[1] . '.' . $kode_sbl_s[2];
						$realisasi_renja = $wpdb->get_results($wpdb->prepare(" SELECT * FROM data_realisasi_renja WHERE tahun_anggaran=%d AND tipe_indikator=%d AND kode_sbl=%s ", $_POST['tahun_anggaran'], 3, $kode_sbl), ARRAY_A);
						$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']] = array(
							'nama'                 => $sub['nama_program'],
							'indikator'            => $capaian_prog,
							'realisasi_indikator'  => $realisasi_renja,
							'id_program'           => $sub['id_program'],
							'kode_program'         => str_replace($sub['kode_bidang_urusan'], '', $sub['kode_program']),
							'kode_sbl'             => $sub['kode_sbl'],
							'kode_urusan_bidang'   => $kode_sub_giat_asli[0] . '.' . $kode_sub_giat_asli[1] . '.' . $kode_sub_giat_asli[2],
							'total'                => 0,
							'triwulan_1'           => 0,
							'triwulan_2'           => 0,
							'triwulan_3'           => 0,
							'triwulan_4'           => 0,
							'rak_triwulan_1'       => 0,
							'rak_triwulan_2'       => 0,
							'rak_triwulan_3'       => 0,
							'rak_triwulan_4'       => 0,
							'total_simda'          => 0,
							'realisasi'            => 0,
							'data'                 => array()
						);
					}

					$data_all['total'] += $total_pagu;
					$data_all['data'][$sub['kode_urusan']]['total'] += $total_pagu;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total'] += $total_pagu;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total'] += $total_pagu;

					$data_all['realisasi'] += $realisasi;
					$data_all['data'][$sub['kode_urusan']]['realisasi'] += $realisasi;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['realisasi'] += $realisasi;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['realisasi'] += $realisasi;

					$data_all['total_simda'] += $total_simda;
					$data_all['data'][$sub['kode_urusan']]['total_simda'] += $total_simda;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_simda'] += $total_simda;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_simda'] += $total_simda;

					$data_all['triwulan_1'] += $triwulan_1;
					$data_all['data'][$sub['kode_urusan']]['triwulan_1'] += $triwulan_1;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_1'] += $triwulan_1;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_1'] += $triwulan_1;

					$data_all['triwulan_2'] += $triwulan_2;
					$data_all['data'][$sub['kode_urusan']]['triwulan_2'] += $triwulan_2;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_2'] += $triwulan_2;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_2'] += $triwulan_2;

					$data_all['triwulan_3'] += $triwulan_3;
					$data_all['data'][$sub['kode_urusan']]['triwulan_3'] += $triwulan_3;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_3'] += $triwulan_3;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_3'] += $triwulan_3;

					$data_all['triwulan_4'] += $triwulan_4;
					$data_all['data'][$sub['kode_urusan']]['triwulan_4'] += $triwulan_4;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_4'] += $triwulan_4;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_4'] += $triwulan_4;

					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['rak_triwulan_1'] += $rak_triwulan_1;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['rak_triwulan_2'] += $rak_triwulan_2;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['rak_triwulan_3'] += $rak_triwulan_3;
					$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['rak_triwulan_4'] += $rak_triwulan_4;

					$data_all['rak_triwulan_1'] += $rak_triwulan_1;
					$data_all['rak_triwulan_2'] += $rak_triwulan_2;
					$data_all['rak_triwulan_3'] += $rak_triwulan_3;
					$data_all['rak_triwulan_4'] += $rak_triwulan_4;
				}


				$persen_triwulan_1 = 0;
				$persen_triwulan_2 = 0;
				$persen_triwulan_3 = 0;
				$persen_triwulan_4 = 0;
				if (!empty($data_all['rak_triwulan_1']) && !empty($data_all['triwulan_1'])) {
					$persen_triwulan_1 = ($data_all['triwulan_1'] / $data_all['rak_triwulan_1']) * 100;
				}
				if (!empty($data_all['rak_triwulan_2']) && !empty($data_all['triwulan_2'])) {
					$persen_triwulan_2 = ($data_all['triwulan_2'] / $data_all['rak_triwulan_2']) * 100;
				}
				if (!empty($data_all['rak_triwulan_3']) && !empty($data_all['triwulan_3'])) {
					$persen_triwulan_3 = ($data_all['triwulan_3'] / $data_all['rak_triwulan_3']) * 100;
				}
				if (!empty($data_all['rak_triwulan_4']) && !empty($data_all['triwulan_4'])) {
					$persen_triwulan_4 = ($data_all['triwulan_4'] / $data_all['rak_triwulan_4']) * 100;
				}

				$data_all_js      = array();
				foreach ($data_all['data'] as $kd_urusan => $urusan) {
					foreach ($urusan['data'] as $kd_bidang => $bidang) {
						foreach ($bidang['data'] as $kd_program_asli => $program) {
							$no_program++;
							$kd_program = explode('.', $kd_program_asli);
							$kd_program = $kd_program[count($kd_program) - 1];
							$capaian = 0;
							if (!empty($program['total_simda'])) {
								$capaian = $this->pembulatan(($program['realisasi'] / $program['total_simda']) * 100);
							}
							$bobot_kinerja_indikator     = array();
							$capaian_prog_js             = array();
							$target_capaian_prog_js      = array();
							$satuan_capaian_prog_js      = array();
							$realisasi_indikator_tw1_js  = array();
							$realisasi_indikator_tw2_js  = array();
							$realisasi_indikator_tw3_js  = array();
							$realisasi_indikator_tw4_js  = array();
							$total_tw_js                 = array();
							$capaian_prog                = array();
							$realisasi_indikator_tw1     = array();
							$realisasi_indikator_tw2     = array();
							$realisasi_indikator_tw3     = array();
							$realisasi_indikator_tw4     = array();
							$total_tw                    = array();
							$capaian_realisasi_indikator = array();
							$class_rumus_target          = array();
							$keterangan                  = array();
							if (!empty($program['indikator'])) {
								$realisasi_indikator = array();
								foreach ($program['realisasi_indikator'] as $k_sub => $v_sub) {
									$realisasi_indikator[$v_sub['id_indikator']] = $v_sub;
								}
								foreach ($program['indikator'] as $k_sub => $v_sub) {
									$keterangan_db = array();
									for ($i = 1; $i <= 12; $i++) {
										if (!empty($v_sub['keterangan_bulan_' . $i])) {
											$keterangan_db[] = $v_sub['keterangan_bulan_' . $i];
										}
									}

									$keterangan[$k_sub]                  = implode(', ', $keterangan_db);
									$target_capaian_prog_js[$k_sub]      = $v_sub['targetcapaian'];
									$bobot_kinerja_indikator[$k_sub]     = $v_sub['bobot_kinerja'];
									$satuan_capaian_prog_js[$k_sub]      = $v_sub['satuancapaian'];
									$target_indikator                    = $v_sub['targetcapaian'];
									$realisasi_indikator_tw1[$k_sub]     = 0;
									$realisasi_indikator_tw2[$k_sub]     = 0;
									$realisasi_indikator_tw3[$k_sub]     = 0;
									$realisasi_indikator_tw4[$k_sub]     = 0;
									$total_tw[$k_sub]                    = 0;
									$capaian_realisasi_indikator[$k_sub] = 0;
									$realisasi_indikator_tw1_js[$k_sub]  = 0;
									$realisasi_indikator_tw2_js[$k_sub]  = 0;
									$realisasi_indikator_tw3_js[$k_sub]  = 0;
									$realisasi_indikator_tw4_js[$k_sub]  = 0;
									$total_tw_js[$k_sub]                 = 0;
									$class_rumus_target[$k_sub]          = " positif";

									if (!empty($realisasi_indikator) && !empty($realisasi_indikator[$k_sub])) {
										$rumus_indikator = $realisasi_indikator[$k_sub]['id_rumus_indikator'];
										$max = 0;
										for ($i = 1; $i <= 12; $i++) {
											$realisasi_bulan = $realisasi_indikator[$k_sub]['realisasi_bulan_' . $i];
											if ($max < $realisasi_bulan) {
												$max = $realisasi_bulan;
											}
											$total_tw[$k_sub] += $realisasi_bulan;
											if ($i <= 3) {
												if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
													if ($i == 3) {
														$realisasi_indikator_tw1[$k_sub] = $realisasi_bulan;
													}
												} else {
													$realisasi_indikator_tw1[$k_sub] += $realisasi_bulan;
												}
											} else if ($i <= 6) {
												if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
													if ($i == 6) {
														$realisasi_indikator_tw2[$k_sub] = $realisasi_bulan;
													}
												} else {
													$realisasi_indikator_tw2[$k_sub] += $realisasi_bulan;
												}
											} else if ($i <= 9) {
												if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
													if ($i == 9) {
														$realisasi_indikator_tw3[$k_sub] = $realisasi_bulan;
													}
												} else {
													$realisasi_indikator_tw3[$k_sub] += $realisasi_bulan;
												}
											} else if ($i <= 12) {
												if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
													if ($i == 12) {
														$realisasi_indikator_tw4[$k_sub] = $realisasi_bulan;
													}
												} else {
													$realisasi_indikator_tw4[$k_sub] += $realisasi_bulan;
												}
											}
										}
										if ($rumus_indikator == 1) {
											$class_rumus_target[$k_sub] = "positif";
											if (!empty($target_indikator[$k_sub])) {
												$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($total_tw[$k_sub] / $target_indikator[$k_sub]) * 100);
											}
										} else if ($rumus_indikator == 2) {
											$class_rumus_target[$k_sub] = "negatif";
											$total_tw[$k_sub] = $max;
											if (!empty($total_tw[$k_sub])) {
												$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($target_indikator[$k_sub] / $total_tw[$k_sub]) * 100);
											}
										} else if ($rumus_indikator == 3 || $rumus_indikator == 4) {
											if ($rumus_indikator == 3) {
												$class_rumus_target[$k_sub] = "persentase";
											} else if ($rumus_indikator == 4) {
												$class_rumus_target[$k_sub] = "nilai_akhir";
											}
											$total_tw[$k_sub] = $max;
											if (!empty($target_indikator[$k_sub])) {
												$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($total_tw[$k_sub] / $target_indikator[$k_sub]) * 100);
											}
										}
									}

									$capaian_prog_js[] = $v_sub['capaianteks'];
									$realisasi_indikator_tw1_js[$k_sub] = $realisasi_indikator_tw1[$k_sub];
									$realisasi_indikator_tw2_js[$k_sub] = $realisasi_indikator_tw2[$k_sub];
									$realisasi_indikator_tw3_js[$k_sub] = $realisasi_indikator_tw3[$k_sub];
									$realisasi_indikator_tw4_js[$k_sub] = $realisasi_indikator_tw4[$k_sub];
									$total_tw_js[$k_sub] = $total_tw[$k_sub];
								}
							}

							$data_all_js[] = array(
								'nama'                    => $kd_program_asli . ' ' . $program['nama'],
								'pagu'                    => number_format($program['total_simda'], 0, ",", "."),
								'realisasi'               => number_format($program['realisasi'], 0, ",", "."),
								'capaian'                 => $capaian,
								'rak_tw_1'                => $program['rak_triwulan_1'],
								'rak_tw_2'                => $program['rak_triwulan_2'],
								'rak_tw_3'                => $program['rak_triwulan_3'],
								'rak_tw_4'                => $program['rak_triwulan_4'],
								'realisasi_tw_1'          => $program['triwulan_1'],
								'realisasi_tw_2'          => $program['triwulan_2'],
								'realisasi_tw_3'          => $program['triwulan_3'],
								'realisasi_tw_4'          => $program['triwulan_4'],
								'indikator'               => $capaian_prog_js,
								'satuan'                  => $satuan_capaian_prog_js,
								'bobot_kinerja_indikator' => $bobot_kinerja_indikator,
								'target_indikator'        => $target_capaian_prog_js,
								'realisasi_indikator'     => $total_tw_js,
								'realisasi_indikator_1'   => $realisasi_indikator_tw1_js,
								'realisasi_indikator_2'   => $realisasi_indikator_tw2_js,
								'realisasi_indikator_3'   => $realisasi_indikator_tw3_js,
								'realisasi_indikator_4'   => $realisasi_indikator_tw4_js,
							);
						}
					}
				}


				$capaian_kinerja = [
					'total' => 0,
					'tw_1'  => 0,
					'tw_2'  => 0,
					'tw_3'  => 0,
					'tw_4'  => 0,
				];

				$total_bobot = 0;
				$total_capaian = 0;
				$total_realisasi_tw1 = 0;
				$total_realisasi_tw2 = 0;
				$total_realisasi_tw3 = 0;
				$total_realisasi_tw4 = 0;
				foreach ($data_all_js as $k => $v) {
					foreach ($v['bobot_kinerja_indikator'] as $kk => $vv) {
						$result_by_bobot = $v['realisasi_indikator_1'][$kk] * $vv;
						$total_realisasi_tw1 += $result_by_bobot;

						$result_by_bobot = $v['realisasi_indikator_2'][$kk] * $vv;
						$total_realisasi_tw2 += $result_by_bobot;

						$result_by_bobot = $v['realisasi_indikator_3'][$kk] * $vv;
						$total_realisasi_tw3 += $result_by_bobot;

						$result_by_bobot = $v['realisasi_indikator_4'][$kk] * $vv;
						$total_realisasi_tw4 += $result_by_bobot;

						$capaian_per_program = $v['realisasi_indikator'][$kk] * $vv;

						$total_capaian += $capaian_per_program; //capaian per program diakumulasi untuk dibagi akumulasi bobot
						$total_bobot += $vv; //akumulasi bobot untuk membagi capaian per program
					}
				}

				$capaian_kinerja['total'] = $total_bobot > 0 ? $total_capaian / $total_bobot : 0;

				if (
					!empty($total_realisasi_tw1)
					&& $total_realisasi_tw1 != 0
					&& !empty($capaian_kinerja['total'])
					&& $capaian_kinerja['total'] != 0
				) {
					$capaian_kinerja['tw_1'] = $total_realisasi_tw1 / $total_bobot;
				}
				if (
					!empty($total_realisasi_tw2)
					&& $total_realisasi_tw2 != 0
					&& !empty($capaian_kinerja['total'])
					&& $capaian_kinerja['total'] != 0
				) {
					$capaian_kinerja['tw_2'] = $total_realisasi_tw2 / $total_bobot;
				}
				if (
					!empty($total_realisasi_tw3)
					&& $total_realisasi_tw3 != 0
					&& !empty($capaian_kinerja['total'])
					&& $capaian_kinerja['total'] != 0
				) {
					$capaian_kinerja['tw_3'] = $total_realisasi_tw3 / $total_bobot;
				}
				if (
					!empty($total_realisasi_tw4)
					&& $total_realisasi_tw4 != 0
					&& !empty($capaian_kinerja['total'])
					&& $capaian_kinerja['total'] != 0
				) {
					$capaian_kinerja['tw_4'] = $total_realisasi_tw4 / $total_bobot;
				}

				//serapan
				$total_serapan = $this->pembulatan(($data_all['realisasi'] / $data_all['total']) * 100);
				$serapan_tw1 = $this->pembulatan($persen_triwulan_1);
				$serapan_tw2 = $this->pembulatan($persen_triwulan_2);
				$serapan_tw3 = $this->pembulatan($persen_triwulan_3);
				$serapan_tw4 = $this->pembulatan($persen_triwulan_4);

				$ret['data'] = array(
					'capaian_kinerja' => array(
						'total' => round($capaian_kinerja['total'], 2) . '%',
						'tw1' => round($capaian_kinerja['tw_1'], 2) . '%',
						'tw2' => round($capaian_kinerja['tw_2'], 2) . '%',
						'tw3' => round($capaian_kinerja['tw_3'], 2) . '%',
						'tw4' => round($capaian_kinerja['tw_4'], 2) . '%'
					),
					'serapan_anggaran' => array(
						'total' => $total_serapan . '%',
						'tw1' => $serapan_tw1 . '%',
						'tw2' => $serapan_tw2 . '%',
						'tw3' => $serapan_tw3 . '%',
						'tw4' => $serapan_tw4 . '%'
					),
					'anggaran' => array(
						'total' => $data_all['total'],
						'tw1' => $data_all['rak_triwulan_1'],
						'tw2' => $data_all['rak_triwulan_1'],
						'tw3' => $data_all['rak_triwulan_1'],
						'tw4' => $data_all['rak_triwulan_1']
					),
					'realisasi_anggaran' => array(
						'total' => $data_all['realisasi'],
						'tw1' => $data_all['triwulan_1'],
						'tw2' => $data_all['triwulan_2'],
						'tw3' => $data_all['triwulan_3'],
						'tw4' => $data_all['triwulan_4']
					),
					'opd' => $unit
				);
			} else {
				$ret = array(
					'status' => 'error',
					'message' => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message' => 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}
}
