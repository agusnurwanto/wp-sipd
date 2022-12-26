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

require_once WPSIPD_PLUGIN_PATH."/public/class-wpsipd-public-base-1.php";

class Wpsipd_Public extends Wpsipd_Public_Base_1
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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version, $simda)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->simda = $simda;
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

		wp_enqueue_style( 'dashicons' );
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
		wp_localize_script( $this->plugin_name, 'ajax', array(
		    'url' => admin_url( 'admin-ajax.php' )
		));
	}

	public function singkron_ssh($value = '')
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export SSH!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['ssh'])) {
					$ssh = $_POST['ssh'];
					foreach ($ssh as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_standar_harga from data_ssh where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_standar_harga=" . $v['id_standar_harga']);
						$kelompok = explode(' ', $v['nama_kel_standar_harga']);
						$nilai = explode(' ', $v['nilai_tkdn']);
						$opsi = array(
							'id_standar_harga' => $v['id_standar_harga'],
							'kode_standar_harga' => $v['kode_standar_harga'],
							'nama_standar_harga' => $v['nama_standar_harga'],
							'satuan' => $v['satuan'],
							'spek' => $v['spek'],
							'ket_teks' => $v['ket_teks'],
							'created_at' => $v['created_at'],
							'created_user' => $v['created_user'],
							'updated_user' => $v['updated_user'],
							'is_deleted' => $v['is_deleted'],
							'is_locked' => $v['is_locked'],
							'kelompok' => $v['kelompok'],
							'harga' => $v['harga'],
							'harga_2' => $v['harga_2'],
							'harga_3' => $v['harga_3'],
							'kode_kel_standar_harga' => $v['kode_kel_standar_harga'],
							'nama_kel_standar_harga' => $kelompok[1],
							'tkdn' => $nilai[0],
							'jenis_produk' => $v['is_pdn'],
							'update_at'	=> current_time('mysql'),
							'tahun_anggaran'	=> $_POST['tahun_anggaran']
						);
						if (!empty($cek)) {
							$wpdb->update('data_ssh', $opsi, array(
								'tahun_anggaran'	=> $_POST['tahun_anggaran'],
								'id_standar_harga' => $v['id_standar_harga']
							));
						} else {
							$wpdb->insert('data_ssh', $opsi);
						}

						foreach ($v['kd_belanja'] as $key => $value) {
							$cek = $wpdb->get_var("
								SELECT 
									id_standar_harga 
								from data_ssh_rek_belanja 
								where tahun_anggaran=".$_POST['tahun_anggaran']." 
									and id_akun=" . $value['id_akun'] . ' 
									and id_standar_harga=' . $v['id_standar_harga']
							);
							$opsi = array(
								"id_akun"	=> $value['id_akun'],
								"kode_akun" => $value['kode_akun'],
								"nama_akun"	=> $value['nama_akun'],
								"id_standar_harga"	=> $v['id_standar_harga'],
								"tahun_anggaran"	=> $_POST['tahun_anggaran']
							);
							if (!empty($cek)) {
								$wpdb->update('data_ssh_rek_belanja', $opsi, array(
									'id_standar_harga' => $v['id_standar_harga'],
									'id_akun' => $value['id_akun'],
									"tahun_anggaran"	=> $_POST['tahun_anggaran']
								));
							} else {
								$wpdb->insert('data_ssh_rek_belanja', $opsi);
							}
						}
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format SSH Salah!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function get_skpd(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'run'	=> $_POST['run'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get SKPD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$data_skpd = $wpdb->get_results($wpdb->prepare("
					SELECT 
						* 
					from data_unit 
					where tahun_anggaran=%d
						and active=1", 
				$_POST['tahun_anggaran']), ARRAY_A);
				foreach ($data_skpd as $k => $v) {
					$data_skpd[$k]['id_mapping'] = get_option('_crb_unit_fmis_'.$_POST['tahun_anggaran'].'_'.$v['id_skpd']);
					$kode_skpd = explode('.', $v['kode_skpd']);
					$bidur_1 = $kode_skpd[0].'.'.$kode_skpd[1];
					$bidur_2 = $kode_skpd[2].'.'.$kode_skpd[3];
					$bidur_3 = $kode_skpd[4].'.'.$kode_skpd[5];
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

	public function get_skpd_fmis(){
		global $wpdb;
		$ret = array(
			'kode_pemda'	=> $_POST['kode_pemda'],
			'nama_pemda'	=> $_POST['nama_pemda'],
			'skpd'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$data_skpd_db = $wpdb->get_results($wpdb->prepare("
					SELECT 
						* 
					from data_unit 
					where tahun_anggaran=%d
						and active=1
					order by id_skpd ASC", 
				$_POST['tahun_anggaran']), ARRAY_A);
				foreach ($data_skpd_db as $k => $v) {
					if($v['is_skpd'] == 1)	{
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
							if(
								$v['id_skpd'] == $vv['id_unit']
								and $vv['is_skpd'] == 0
							){
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

	public function get_meta_subunit_simda(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare("
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
				foreach($data_sub_unit as $k => $sub_unit){
					$kd_sub_unit = $sub_unit->kd_urusan.'.'.$sub_unit->kd_bidang.'.'.$sub_unit->kd_unit.'.'.$sub_unit->kd_sub;
					$data_sub_unit[$k]->kd_sub_unit = $kd_sub_unit;
					if(!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])){
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

	public function get_spp(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'tipe'	=> $_POST['tipe'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$jenis = 1;
				if($_POST['tipe'] == 'up'){
					$jenis = 1;
				}else if($_POST['tipe'] == 'gu'){
					$jenis = 2;
				}else if($_POST['tipe'] == 'ls'){
					$jenis = 3;
				}else if($_POST['tipe'] == 'tu'){
					$jenis = 4;
				}else if($_POST['tipe'] == 'nihil'){
					$jenis = 5;
				}
				$id_skpd_fmis = false;
				if(!empty($_POST['idsubunit'])){
					$id_skpd_fmis = $_POST['idsubunit'];
				}
				$mapping_skpd = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, true);
				$kd_urusan = array();
				$kd_bidang = array();
				$kd_unit = array();
				$kd_sub = array();
				foreach($mapping_skpd['id_skpd_sipd'] as $id){
					$kd_unit_simda_asli = get_option('_crb_unit_'.$id);
					$kd_simda = explode('.', $kd_unit_simda_asli);
					if(!empty($kd_simda[0])){
						if(empty($kd_urusan[$kd_simda[0]])){
							$kd_urusan[$kd_simda[0]] = $kd_simda[0];
						}
					}
					if(!empty($kd_simda[1])){
						if(empty($kd_bidang[$kd_simda[1]])){
							$kd_bidang[$kd_simda[1]] = $kd_simda[1];
						}
					}
					if(!empty($kd_simda[2])){
						if(empty($kd_unit[$kd_simda[2]])){
							$kd_unit[$kd_simda[2]] = $kd_simda[2];
						}
					}
					if(!empty($kd_simda[3])){
						if(empty($kd_sub[$kd_simda[3]])){
							$kd_sub[$kd_simda[3]] = $kd_simda[3];
						}
					}
				}
				$sql = $wpdb->prepare("
					SELECT 
						*
					from ta_spp
					where tahun=%d
						and jn_spp=%d
						and kd_urusan in (".implode(',', $kd_urusan).")
						and kd_bidang in (".implode(',', $kd_bidang).")
						and kd_unit in (".implode(',', $kd_unit).")
						and kd_sub in (".implode(',', $kd_sub).")
					", 
					$tahun_anggaran, 
					$jenis
				);
				$return['sql'] = $sql;
				$spp = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				foreach($spp as $k => $v){
					$kd_sub_unit = $v->kd_urusan.'.'.$v->kd_bidang.'.'.$v->kd_unit.'.'.$v->kd_sub;
					$spp[$k]->kd_sub_unit = $kd_sub_unit;
					if(!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])){
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

	public function get_spm(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'tipe'	=> $_POST['tipe'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$jenis = 1;
				if($_POST['tipe'] == 'up'){
					$jenis = 1;
				}else if($_POST['tipe'] == 'gu'){
					$jenis = 2;
				}else if($_POST['tipe'] == 'ls'){
					$jenis = 3;
				}else if($_POST['tipe'] == 'tu'){
					$jenis = 4;
				}else if($_POST['tipe'] == 'nihil'){
					$jenis = 5;
				}
				$id_skpd_fmis = false;
				if(!empty($_POST['idsubunit'])){
					$id_skpd_fmis = $_POST['idsubunit'];
				}
				$mapping_skpd = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, true);
				$kd_urusan = array();
				$kd_bidang = array();
				$kd_unit = array();
				$kd_sub = array();
				foreach($mapping_skpd['id_skpd_sipd'] as $id){
					$kd_unit_simda_asli = get_option('_crb_unit_'.$id);
					$kd_simda = explode('.', $kd_unit_simda_asli);
					if(!empty($kd_simda[0])){
						if(empty($kd_urusan[$kd_simda[0]])){
							$kd_urusan[$kd_simda[0]] = $kd_simda[0];
						}
					}
					if(!empty($kd_simda[1])){
						if(empty($kd_bidang[$kd_simda[1]])){
							$kd_bidang[$kd_simda[1]] = $kd_simda[1];
						}
					}
					if(!empty($kd_simda[2])){
						if(empty($kd_unit[$kd_simda[2]])){
							$kd_unit[$kd_simda[2]] = $kd_simda[2];
						}
					}
					if(!empty($kd_simda[3])){
						if(empty($kd_sub[$kd_simda[3]])){
							$kd_sub[$kd_simda[3]] = $kd_simda[3];
						}
					}
				}
				$sql = $wpdb->prepare("
					SELECT 
						*
					from ta_spm
					where tahun=%d
						and jn_spm=%d
						and kd_urusan in (".implode(',', $kd_urusan).")
						and kd_bidang in (".implode(',', $kd_bidang).")
						and kd_unit in (".implode(',', $kd_unit).")
						and kd_sub in (".implode(',', $kd_sub).")
					", 
					$tahun_anggaran, 
					$jenis
				);
				$return['sql'] = $sql;
				$spm = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				foreach($spm as $k => $v){
					$kd_sub_unit = $v->kd_urusan.'.'.$v->kd_bidang.'.'.$v->kd_unit.'.'.$v->kd_sub;
					$spm[$k]->kd_sub_unit = $kd_sub_unit;
					if(!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])){
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

	public function get_sp2d(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$spm_all = json_decode(stripslashes(html_entity_decode($_POST['spm_no'])));
				if(!empty($spm_all)){
					foreach($spm_all as $k => $v){
						$spm_all[$k] = "'$v'";
					}
					$sql = $wpdb->prepare("
						SELECT 
							*
						from ta_sp2d
						where tahun=%d
							and no_spm in (".implode(',', $spm_all).")
						", 
						$tahun_anggaran
					);
					$return['sql'] = $sql;
					$sp2d = $this->simda->CurlSimda(array(
						'query' => $sql,
						'debug' => 1
					));
					$ret['data'] = $sp2d;
				}else{
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

	public function get_tagihan(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_skpd_fmis = false;
				if(!empty($_POST['idsubunit'])){
					$id_skpd_fmis = $_POST['idsubunit'];
				}
				$mapping_skpd = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, true);
				$kd_urusan = array();
				$kd_bidang = array();
				$kd_unit = array();
				$kd_sub = array();
				foreach($mapping_skpd['id_skpd_sipd'] as $id){
					$kd_unit_simda_asli = get_option('_crb_unit_'.$id);
					$kd_simda = explode('.', $kd_unit_simda_asli);
					if(!empty($kd_simda[0])){
						if(empty($kd_urusan[$kd_simda[0]])){
							$kd_urusan[$kd_simda[0]] = $kd_simda[0];
						}
					}
					if(!empty($kd_simda[1])){
						if(empty($kd_bidang[$kd_simda[1]])){
							$kd_bidang[$kd_simda[1]] = $kd_simda[1];
						}
					}
					if(!empty($kd_simda[2])){
						if(empty($kd_unit[$kd_simda[2]])){
							$kd_unit[$kd_simda[2]] = $kd_simda[2];
						}
					}
					if(!empty($kd_simda[3])){
						if(empty($kd_sub[$kd_simda[3]])){
							$kd_sub[$kd_simda[3]] = $kd_simda[3];
						}
					}
				}
				$sql = $wpdb->prepare("
					SELECT 
						s.*,
						t.*
					from ta_spp s
						left join ta_tagihan t on s.no_tagihan=t.no_tagihan
					where s.tahun=%d
						and s.no_tagihan is not null
						and s.kd_urusan in (".implode(',', $kd_urusan).")
						and s.kd_bidang in (".implode(',', $kd_bidang).")
						and s.kd_unit in (".implode(',', $kd_unit).")
						and s.kd_sub in (".implode(',', $kd_sub).")
					", 
					$tahun_anggaran
				);
				$return['sql'] = $sql;
				$spp = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				foreach($spp as $k => $v){
					$kd_sub_unit = $v->kd_urusan.'.'.$v->kd_bidang.'.'.$v->kd_unit.'.'.$v->kd_sub;
					$spp[$k]->kd_sub_unit = $kd_sub_unit;
					if(!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])){
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

	public function get_kontrak(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_skpd_fmis = false;
				if(!empty($_POST['idsubunit'])){
					$id_skpd_fmis = $_POST['idsubunit'];
				}
				$mapping_skpd = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, true);
				$kd_urusan = array();
				$kd_bidang = array();
				$kd_unit = array();
				$kd_sub = array();
				foreach($mapping_skpd['id_skpd_sipd'] as $id){
					$kd_unit_simda_asli = get_option('_crb_unit_'.$id);
					$kd_simda = explode('.', $kd_unit_simda_asli);
					if(!empty($kd_simda[0])){
						if(empty($kd_urusan[$kd_simda[0]])){
							$kd_urusan[$kd_simda[0]] = $kd_simda[0];
						}
					}
					if(!empty($kd_simda[1])){
						if(empty($kd_bidang[$kd_simda[1]])){
							$kd_bidang[$kd_simda[1]] = $kd_simda[1];
						}
					}
					if(!empty($kd_simda[2])){
						if(empty($kd_unit[$kd_simda[2]])){
							$kd_unit[$kd_simda[2]] = $kd_simda[2];
						}
					}
					if(!empty($kd_simda[3])){
						if(empty($kd_sub[$kd_simda[3]])){
							$kd_sub[$kd_simda[3]] = $kd_simda[3];
						}
					}
				}
				$sql = $wpdb->prepare("
					SELECT 
						s.*
					from ta_kontrak s
					where s.tahun=%d
						and s.kd_urusan in (".implode(',', $kd_urusan).")
						and s.kd_bidang in (".implode(',', $kd_bidang).")
						and s.kd_unit in (".implode(',', $kd_unit).")
						and s.kd_sub in (".implode(',', $kd_sub).")
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
				foreach($kontrak as $k => $v){
					$kd_sub_unit = $v->kd_urusan.'.'.$v->kd_bidang.'.'.$v->kd_unit.'.'.$v->kd_sub;
					$kontrak[$k]->kd_sub_unit = $kd_sub_unit;
					if(!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])){
						$kontrak[$k]->skpd = $mapping_skpd['id_mapping_simda'][$kd_sub_unit];
					}
					$kd_urusan_asli = substr($v->id_prog, 0, 1);
					$kd_bidang_asli = (int) substr($v->id_prog, 1);
					$kd_keg_simda = $kd_urusan_asli.'.'.$kd_bidang_asli.'.'.$v->kd_prog.'.'.$v->kd_keg;
					if(empty($prog_keg[$kd_keg_simda])){
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
						if($mapping[0]->kd_program90 == 1){
							$kd_urusan_sipd = 'X';
							$kd_bidang_sipd = 'XX';
						}
						$kode_sub = $kd_urusan_sipd.'.'.$this->simda->CekNull($kd_bidang_sipd).'.'.$this->simda->CekNull($mapping[0]->kd_program90).'.'.$mapping[0]->kd_kegiatan90.'.'.$this->simda->CekNull($mapping[0]->kd_sub_kegiatan);
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
						if(!empty($program_mapping[$this->removeNewline($sub[0]['nama_program'])])){
							$prog_keg[$kd_keg_simda]['nama_program'] = $program_mapping[$this->removeNewline($sub[0]['nama_program'])];
						}
						if(!empty($keg_mapping[$this->removeNewline($sub[0]['nama_giat'])])){
							$prog_keg[$kd_keg_simda]['nama_giat'] = $keg_mapping[$this->removeNewline($sub[0]['nama_giat'])];
						}
						if(!empty($subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])])){
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

	public function get_sp2b(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_skpd_fmis = false;
				if(!empty($_POST['idsubunit'])){
					$id_skpd_fmis = $_POST['idsubunit'];
				}
				$mapping_skpd = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, true);
				$kd_urusan = array();
				$kd_bidang = array();
				$kd_unit = array();
				$kd_sub = array();
				foreach($mapping_skpd['id_skpd_sipd'] as $id){
					$kd_unit_simda_asli = get_option('_crb_unit_'.$id);
					$kd_simda = explode('.', $kd_unit_simda_asli);
					if(!empty($kd_simda[0])){
						if(empty($kd_urusan[$kd_simda[0]])){
							$kd_urusan[$kd_simda[0]] = $kd_simda[0];
						}
					}
					if(!empty($kd_simda[1])){
						if(empty($kd_bidang[$kd_simda[1]])){
							$kd_bidang[$kd_simda[1]] = $kd_simda[1];
						}
					}
					if(!empty($kd_simda[2])){
						if(empty($kd_unit[$kd_simda[2]])){
							$kd_unit[$kd_simda[2]] = $kd_simda[2];
						}
					}
					if(!empty($kd_simda[3])){
						if(empty($kd_sub[$kd_simda[3]])){
							$kd_sub[$kd_simda[3]] = $kd_simda[3];
						}
					}
				}
				$sql = $wpdb->prepare("
					SELECT 
						s.*
					from ta_sp3b s
					where s.tahun=%d
						and s.kd_urusan in (".implode(',', $kd_urusan).")
						and s.kd_bidang in (".implode(',', $kd_bidang).")
						and s.kd_unit in (".implode(',', $kd_unit).")
						and s.kd_sub in (".implode(',', $kd_sub).")
					", 
					$tahun_anggaran
				);
				$return['sql'] = $sql;
				$spp = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				foreach($spp as $k => $v){
					$kd_sub_unit = $v->kd_urusan.'.'.$v->kd_bidang.'.'.$v->kd_unit.'.'.$v->kd_sub;
					$spp[$k]->kd_sub_unit = $kd_sub_unit;
					if(!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])){
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

	public function get_ssh(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get SSH!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(
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
				){
					$rek_mapping = $this->get_fmis_mapping(array(
						'name' => '_crb_custom_mapping_rekening_fmis'
					));
					$type_pagu = get_option('_crb_fmis_pagu');
					if($_POST['kelompok'] == 7){
						$data = array();
						$data_ssh = $wpdb->get_results($wpdb->prepare("
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
						$_POST['tahun_anggaran']), ARRAY_A);

						// set variable ssh sesuai kebutuhan ssh di FMIS
						foreach ($data_ssh as $k => $v) {
							if(
								!empty($type_pagu)
								&& $type_pagu == 2
							){
								$v['total'] = $v['nilaimurni'];
							}
							$_kode_akun = explode('.', $v['kode_akun']);
							$kode_akun = array();
							foreach ($_kode_akun as $vv) {
								$kode_akun[] = (int)$vv;
							}
							$kode_akun = implode('.', $kode_akun);
							if(!empty($rek_mapping[$kode_akun])){
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
							$newdata['nama_standar_harga'] = substr($v['total'].' Rupiah '.$v['uraian'], 0, 250);
							$newdata['spek'] = '';
							$newdata['satuan'] = 'Rupiah';
							$newdata['kelompok'] = 9;
							$newdata['harga'] = $v['total'];
							$newdata['kode_gol_standar_harga'] = $_POST['tahun_anggaran'];
							$newdata['kode_kel_standar_harga'] = 'Pendapatan';
							$newdata['nama_sub_kel_standar_harga'] = substr($v['keterangan'], 0, 250);
							$data[] = $newdata;
						}

						$data_ssh = $wpdb->get_results($wpdb->prepare("
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
						$_POST['tahun_anggaran']), ARRAY_A);

						// set variable ssh sesuai kebutuhan ssh di FMIS
						foreach ($data_ssh as $k => $v) {
							if(
								!empty($type_pagu)
								&& $type_pagu == 2
							){
								$v['total'] = $v['nilaimurni'];
							}
							$_kode_akun = explode('.', $v['kode_akun']);
							$kode_akun = array();
							foreach ($_kode_akun as $vv) {
								$kode_akun[] = (int)$vv;
							}
							$kode_akun = implode('.', $kode_akun);
							if(!empty($rek_mapping[$kode_akun])){
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
							$newdata['nama_standar_harga'] = substr($v['total'].' Rupiah '.$v['uraian'], 0, 250);
							$newdata['spek'] = '';
							$newdata['satuan'] = 'Rupiah';
							$newdata['kelompok'] = 9;
							$newdata['harga'] = $v['total'];
							$newdata['kode_gol_standar_harga'] = $_POST['tahun_anggaran'];
							$newdata['kode_kel_standar_harga'] = 'Pembiayaan '.$v['type'];
							$newdata['nama_sub_kel_standar_harga'] = substr($v['keterangan'], 0, 250);
							$data[] = $newdata;
						}
					}else if($_POST['kelompok'] == 8){
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
						foreach($data_rek as $rek){
							$keyword = $rek->kd_rek_1.$rek->kd_rek_2.$rek->kd_rek_3.$rek->kd_rek_4.$rek->kd_rek_5;
							$keyword90 = $rek->kd_rek90_1.'.'.$rek->kd_rek90_2.'.'.$rek->kd_rek90_3.'.'.$rek->kd_rek90_4.'.'.$rek->kd_rek90_5.'.'.$rek->kd_rek90_6;
							$new_rek[$keyword] = array(
								'kode_akun' => $keyword90,
								'nama_akun' => $rek->nm_rek90_6
							);
						}

						$kd_perubahan = 4;
						if(
							!empty($type_pagu)
							&& $type_pagu == 2
						){
							$kd_perubahan = '(SELECT max(kd_perubahan) from ta_rask_arsip where tahun='.$tahun_anggaran.')';
						}
						$sql = $wpdb->prepare("
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
							$keyword = $v->kd_rek_1.$v->kd_rek_2.$v->kd_rek_3.$v->kd_rek_4.$v->kd_rek_5;
							$key = $v->keterangan.$v->nilai_rp.$v->satuan123;
							if(empty($data1[$key])){
								$v->rek_belanja = array($new_rek[$keyword]);
								$data1[$key] = (array) $v;
							}else{
								$data1[$key]['rek_belanja'][] = $new_rek[$keyword];
							}
						}
						// set variable ssh sesuai kebutuhan ssh di FMIS
						foreach ($data1 as $k => $v) {
							// if($k >= 10){ continue; }
							$newdata = array();
							$newdata['rek_belanja'] = $v['rek_belanja'];
							$newdata['kode_standar_harga'] = $_POST['tahun_anggaran'];
							$newdata['nama_standar_harga'] = substr($v['nilai_rp'].' '.$v['satuan123'].' '.$v['keterangan'], 0, 250);
							$newdata['spek'] = '';
							$newdata['satuan'] = $v['satuan123'];
							$newdata['kelompok'] = 9;
							$newdata['harga'] = $v['nilai_rp'];
							$newdata['kode_gol_standar_harga'] = $_POST['tahun_anggaran'].' RKA SIMDA';
							$newdata['kode_kel_standar_harga'] = $v['kd_urusan'].'.'.$v['kd_bidang'].'.'.$v['kd_unit'].'.'.$v['kd_sub'].'.'.$v['kd_prog'].'.'.$v['id_prog'].'.'.$v['kd_keg'];
							$newdata['nama_sub_kel_standar_harga'] = substr($v['keterangan_rinc'], 0, 250);
							$data[] = $newdata;
						}
					}else if($_POST['kelompok'] == 9){
						$data_ssh = $wpdb->get_results($wpdb->prepare("
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
						$_POST['tahun_anggaran'], $_POST['kelompok']), ARRAY_A);
						$data = array(); 
						$data1 = array();
						foreach ($data_ssh as $k => $v) {
							if(
								!empty($type_pagu)
								&& $type_pagu == 2
							){
								$v['harga_satuan'] = $v['harga_satuan_murni'];
							}
							$_kode_akun = explode('.', $v['kode_akun']);
							$kode_akun = array();
							foreach ($_kode_akun as $vv) {
								$kode_akun[] = (int)$vv;
							}
							$kode_akun = implode('.', $kode_akun);
							if(!empty($rek_mapping[$kode_akun])){
								$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
								$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
								$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
								$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
								$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
								$v['kode_akun'] = implode('.', $_kode_akun);
							}

							$key = $v['nama_komponen'].$v['spek_komponen'].$v['harga_satuan'].$v['satuan'];
							if(empty($data1[$key])){
								$v['rek_belanja'] = array(
									array(
										'kode_akun' => $v['kode_akun'],
										'nama_akun' => $v['nama_akun']
									)
								);
								$data1[$key] = $v;
							}else{
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
							$newdata['nama_standar_harga'] = substr($v['harga_satuan'].' '.$v['satuan'].' '.$v['nama_komponen'], 0, 250);
							$newdata['spek'] = $v['spek_komponen'];
							$newdata['satuan'] = $v['satuan'];
							$newdata['kelompok'] = 9;
							$newdata['harga'] = $v['harga_satuan'];
							$newdata['kode_gol_standar_harga'] = $_POST['tahun_anggaran'];
							$newdata['kode_kel_standar_harga'] = $v['jenis_bl'];
							$newdata['nama_sub_kel_standar_harga'] = substr($v['nama_komponen'], 0, 250);
							$data[] = $newdata;
						}
					}else{
						$data_ssh = $wpdb->get_results($wpdb->prepare("
							SELECT 
								* 
							from data_ssh 
							where tahun_anggaran=%d
								and is_deleted=0
								and kelompok=%d", 
						$_POST['tahun_anggaran'], $_POST['kelompok']), ARRAY_A);
						$data = array(); 
						foreach ($data_ssh as $k => $v) {
							// if($k >= 10){ continue; }
							$v['rek_belanja'] = $wpdb->get_results("SELECT * from data_ssh_rek_belanja where id_standar_harga=" . $v['id_standar_harga'], ARRAY_A);
							$data[] = $v;
						}
					}
					$ret['data'] = $data;
				}else{
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

	public function get_ssh_fmis(){
		global $wpdb;
		$ret = array(
			'no_perkada'	=> $_POST['no_perkada'],
			'tgl_perkada'	=> $_POST['tgl_perkada'],
			'keterangan'	=> $_POST['keterangan'],
			'golongan'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(
					!empty($_POST['kelompok'])
					&& (
						$_POST['kelompok'] == 1 // SSH
						|| $_POST['kelompok'] == 2 // HSPK
						|| $_POST['kelompok'] == 3 // ASB
						|| $_POST['kelompok'] == 4 // SBU
					)
				){
					$data_ssh = $wpdb->get_results($wpdb->prepare("
						SELECT 
							* 
						from data_ssh 
						where tahun_anggaran=%d
							and is_deleted=0
							and kelompok=%d", 
					$_POST['tahun_anggaran'], $_POST['kelompok']), ARRAY_A);
					$data = array();
					foreach ($data_ssh as $k => $v) {
						if($k >= 10){ continue; }
						$rek_ssh = explode('.', $v['kode_kel_standar_harga']);
						$rek_golongan = $rek_ssh[0].'.'.$rek_ssh[1].'.'.$rek_ssh[2].'.'.$rek_ssh[3].'.'.$rek_ssh[4];
						$rek_kelompok = $rek_golongan.'.'.$rek_ssh[5];
						$rek_sub_kelompok = $v['kode_kel_standar_harga'];
						if(empty($data[$rek_golongan])){
							$data[$rek_golongan] = array(
								'no_golongan'=> count($data)+1,
								'uraian_golongan'=> $rek_golongan,
								'kelompok'=> array()
							);
						}
						if(empty($data[$rek_golongan]['kelompok'][$rek_kelompok])){
							$data[$rek_golongan]['kelompok'][$rek_kelompok] = array(
								'no_kelompok'=> count($data[$rek_golongan]['kelompok'])+1,
								'uraian_kelompok'=> $rek_kelompok,
								'sub_kelompok'=> array()
							);
						}
						if(empty($data[$rek_golongan]['kelompok'][$rek_kelompok]['sub_kelompok'][$rek_sub_kelompok])){
							$data[$rek_golongan]['kelompok'][$rek_kelompok]['sub_kelompok'][$rek_sub_kelompok] = array(
								'no_sub_kelompok'=> count($data[$rek_golongan]['kelompok'][$rek_kelompok]['sub_kelompok'])+1,
								'uraian_sub_kelompok'=> $rek_sub_kelompok.' '.$v['nama_kel_standar_harga'],
								'item_ssh'=> array()
							);
						}
						$v['rek_belanja'] = $wpdb->get_results("SELECT kode_akun, nama_akun from data_ssh_rek_belanja where id_standar_harga=" . $v['id_standar_harga'], ARRAY_A);
						$no_item = count($data[$rek_golongan]['kelompok'][$rek_kelompok]['sub_kelompok'][$rek_sub_kelompok])+1;
						$data[$rek_golongan]['kelompok'][$rek_kelompok]['sub_kelompok'][$rek_sub_kelompok]['item_ssh'][] = array(
							'id_ssh' => $v['id_standar_harga'],
							'no_item' => $no_item,
							'uraian_item' => $v['nama_standar_harga'],
							'spesifikasi' => $rek_sub_kelompok.' '.$v['spek'],
							'satuan' => $v['satuan'],
							'harga' => $v['harga'],
							'rek_belanja' => $v['rek_belanja']
						);
					}
					$ret['golongan'] = $data;
				}else{
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['data'])) {
					$data = $_POST['data'];
					$cek = $wpdb->get_var("SELECT id_lurah from data_desa_kelurahan where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_lurah=" . $data['id_lurah']);
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

	public function non_active_user(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil menonactivekan user!',
			'action'	=> $_POST['action']
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['id_level'])) {
					$wpdb->update('data_dewan', array( 'active' => 0 ), array(
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

	public function singkron_user_dewan(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data user!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['data'])) {
					$data = $_POST['data'];
					$cek = $wpdb->get_var("SELECT iduser from data_dewan where tahun_anggaran=".$_POST['tahun_anggaran']." AND iduser=" . $data['iduser']);
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
						'active' => 1,
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

	public function singkron_skpd_mitra_bappeda(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data SKPD user mitra!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['data'])) {
					$wpdb->update('data_skpd_mitra_bappeda', array( 'active' => 0 ), array(
						'id_user' => $_POST['id_user'],
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($_POST['data'] as $k => $v) {
						$cek = $wpdb->get_var("
							SELECT 
								id_user 
							from data_skpd_mitra_bappeda 
							where tahun_anggaran=".$_POST['tahun_anggaran']." 
								AND id_unit=" . $v['id_unit']."
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['data'])) {
					$data = $_POST['data'];
					$cek = $wpdb->get_var("SELECT id_usulan from data_asmas where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_usulan=" . $data['id_usulan']);
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['data'])) {
					$data = $_POST['data'];
					$cek = $wpdb->get_var("SELECT id_usulan from data_pokir where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_usulan=" . $data['id_usulan']);
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

	public function singkron_pengaturan_sipd()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data pengaturan SIPD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['data'])) {
					$data = $_POST['data'];
					$cek = $wpdb->get_var("SELECT kepala_daerah from data_pengaturan_sipd where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_daerah='".$data['id_daerah']."'");
					$opsi = array(
						'id_daerah' => $data['id_daerah'],
						'daerah' => $data['daerah'],
						'kepala_daerah' => $data['kepala_daerah'],
						'wakil_kepala_daerah' => $data['wakil_kepala_daerah'],
						'awal_rpjmd' => $data['awal_rpjmd'],
						'akhir_rpjmd' => $data['akhir_rpjmd'],
						'pelaksana_rkpd' => $data['pelaksana_rkpd'],
						'pelaksana_kua' => $data['pelaksana_kua'],
						'pelaksana_apbd' => $data['pelaksana_apbd'],
						'set_kpa_sekda' => $data['set_kpa_sekda'],
						'update_at' => current_time('mysql'),
						'tahun_anggaran' => $_POST['tahun_anggaran']
					);
					update_option( '_crb_daerah', $data['daerah'] );
					update_option( '_crb_kepala_daerah', $data['kepala_daerah'] );
					update_option( '_crb_wakil_daerah', $data['wakil_kepala_daerah'] );
					if (!empty($cek)) {
						$wpdb->update('data_pengaturan_sipd', $opsi, array(
							'id_daerah' => $v['id_daerah'],
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['akun'])) {
					$akun = $_POST['akun'];
					foreach ($akun as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_akun from data_akun where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_akun=" . $v['id_akun']);
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
							'status' => $v['status'],
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$data = $_POST['data'];
				$wpdb->update('data_pendapatan', array('active' => 0), array(
					'tahun_anggaran' => $_POST['tahun_anggaran'],
					'id_skpd' => $_POST['id_skpd']
				));
				foreach ($data as $k => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("
						SELECT id_pendapatan 
						from data_pendapatan 
						where tahun_anggaran=%d 
							AND id_pendapatan=%d 
							AND id_skpd=%d", 
						$_POST['tahun_anggaran'], $v['id_pendapatan'], $_POST['id_skpd']
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

				if(
					get_option('_crb_singkron_simda') == 1
					&& get_option('_crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
				){
					$debug = false;
					if(get_option('_crb_singkron_simda_debug') == 1){
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$data = $_POST['data'];
				$type = array();
				foreach ($data as $k => $v) {
					if(empty($type[$v['type']])){
						$type[$v['type']] = 1;
						$wpdb->update('data_pembiayaan', array('active' => 0), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'id_skpd' => $_POST['id_skpd'],
							'type' => $v['type']
						));
					}
					$cek = $wpdb->get_var($wpdb->prepare("
						SELECT id_pembiayaan 
						from data_pembiayaan 
						where tahun_anggaran=%d
							AND id_pembiayaan=%d
							AND id_skpd=%d
							AND type=%s",
						$_POST['tahun_anggaran'], $v['id_pembiayaan'], $_POST['id_skpd'], $v['type']
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

				if(
					get_option('_crb_singkron_simda') == 1
					&& get_option('_crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
				){
					$debug = false;
					if(get_option('_crb_singkron_simda_debug') == 1){
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['data_unit'])) {
					$data_unit = $_POST['data_unit'];
					// $wpdb->update('data_unit', array( 'active' => 0 ), array(
					// 	'tahun_anggaran' => $_POST['tahun_anggaran']
					// ));

					$cat_name = $_POST['tahun_anggaran'] . ' RKPD';
					$taxonomy = 'category';
					$cat  = get_term_by('name', $cat_name, $taxonomy);
					if ($cat == false) {
						$cat = wp_insert_term($cat_name, $taxonomy);
						$cat_id = $cat['term_id'];
					} else {
						$cat_id = $cat->term_id;
					}
					foreach ($data_unit as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_skpd from data_unit where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_skpd=" . $v['id_skpd']);
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
						$custom_post = get_page_by_title($nama_page, OBJECT, 'page');

						$_post = array(
							'post_title'	=> $nama_page,
							'post_content'	=> '[tampilrkpd id_skpd="'.$v['id_skpd'].'" tahun_anggaran="'.$_POST['tahun_anggaran'].'"]',
							'post_type'		=> 'page',
							'post_status'	=> 'private',
							'comment_status'	=> 'closed'
						);
						if (empty($custom_post) || empty($custom_post->ID)) {
							$id = wp_insert_post($_post);
							$_post['insert'] = 1;
							$_post['ID'] = $id;
						}else{
							$_post['ID'] = $custom_post->ID;
							wp_update_post( $_post );
							$_post['update'] = 1;
						}
						$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
						update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
						update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
						update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
						update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
						update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
						update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
						update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
						update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');

						// https://stackoverflow.com/questions/3010124/wordpress-insert-category-tags-automatically-if-they-dont-exist
						$append = true;
						wp_set_post_terms($custom_post->ID, array($cat_id), $taxonomy, $append);
						$ret['renja_link'][$v['kode_skpd']] = esc_url( get_permalink($custom_post));
					}

					$nama_page = 'RKPD '.$_POST['tahun_anggaran'];
					$custom_post = get_page_by_title($nama_page, OBJECT, 'page');

					$_post = array(
						'post_title'	=> $nama_page,
						'post_content'	=> '[tampilrkpd tahun_anggaran="'.$_POST['tahun_anggaran'].'"]',
						'post_type'		=> 'page',
						'post_status'	=> 'private',
						'comment_status'	=> 'closed'
					);
					if (empty($custom_post) || empty($custom_post->ID)) {
						$id = wp_insert_post($_post);
						$_post['insert'] = 1;
						$_post['ID'] = $id;
					}else{
						$_post['ID'] = $custom_post->ID;
						wp_update_post( $_post );
						$_post['update'] = 1;
					}
					$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
					update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
					update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
					update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
					update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
					update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
					update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
					update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
					update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');

					$append = true;
					wp_set_post_terms($custom_post->ID, array($cat_id), $taxonomy, $append);
					$ret['renja_link'][0] = esc_url( get_permalink($custom_post));

					if(
						get_option('_crb_singkron_simda') == 1
						&& get_option('_crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
					){
						$debug = false;
						if(get_option('_crb_singkron_simda_debug') == 1){
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

	public function get_mandatory_spending_link(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'message'	=> 'Berhasil get mandatory spending link!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				$nama_page = 'Mandatory Spending | '.$_POST['tahun_anggaran'];
				$custom_post = get_page_by_title($nama_page, OBJECT, 'page');

				$_post = array(
					'post_title'	=> $nama_page,
					'post_content'	=> '[apbdpenjabaran tahun_anggaran="'.$_POST['tahun_anggaran'].'" lampiran=99]',
					'post_type'		=> 'page',
					'post_status'	=> 'private',
					'comment_status'	=> 'closed'
				);
				if (empty($custom_post) || empty($custom_post->ID)) {
					$id = wp_insert_post($_post);
					$_post['insert'] = 1;
					$_post['ID'] = $id;
				}else{
					$_post['ID'] = $custom_post->ID;
					wp_update_post( $_post );
					$_post['update'] = 1;
				}
				$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
				update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
				update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
				update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
				update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
				update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
				update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
				update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
				update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');

				$ret['link'] = esc_url( get_permalink($custom_post));
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['subgiat'])) {
					$sub_giat = $_POST['subgiat'];
					foreach ($sub_giat as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_sub_giat from data_prog_keg where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_sub_giat=" . $v['id_sub_giat']);
						$opsi = array(
							'id_bidang_urusan' => $v['id_bidang_urusan'],
							'id_program' => $v['id_program'],
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
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if (!empty($cek)) {
							$wpdb->update('data_prog_keg', $opsi, array(
								'id_sub_giat' => $v['id_sub_giat'],
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_prog_keg', $opsi);
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

	public function singkron_data_rpjmd()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron RPJMD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['rpjmd'])) {
					$data_rpjmd = $_POST['rpjmd'];
					foreach ($data_rpjmd as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_rpjmd from data_rpjmd where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_rpjmd=" . $v['id_rpjmd']);
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
					$visi = $_POST['visi'];
					$wpdb->update('data_rpjmd_visi', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($visi as $k => $v) {
						if(empty($v['id_visi'])){
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_visi from data_rpjmd_visi where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_visi='" . $v['id_visi']."'");
						$opsi = array(
							'id_visi' => $v['id_visi'],
							'is_locked' => $v['is_locked'],
							'status' => $v['status'],
							'visi_teks' => $v['visi_teks'],
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
					$misi = $_POST['misi'];
					$wpdb->update('data_rpjmd_misi', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($misi as $k => $v) {
						if(empty($v['id_misi'])){
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_misi from data_rpjmd_misi where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_misi='" . $v['id_misi']."'");
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
					$tujuan = $_POST['tujuan'];
					$wpdb->update('data_rpjmd_tujuan', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($tujuan as $k => $v) {
						if(empty($v['id_tujuan'])){
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_tujuan from data_rpjmd_tujuan where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_unik='" . $v['id_unik']."' AND id_unik_indikator='" . $v['id_unik_indikator']."'");
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
					$sasaran = $_POST['sasaran'];
					$wpdb->update('data_rpjmd_sasaran', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($sasaran as $k => $v) {
						if(empty($v['id_sasaran'])){
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_sasaran from data_rpjmd_sasaran where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_unik='" . $v['id_unik']."' AND id_unik_indikator='" . $v['id_unik_indikator']."'");
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
					$program = $_POST['program'];
					$wpdb->update('data_rpjmd_program', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					foreach ($program as $k => $v) {
						if(empty($v['id_program'])){
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_program from data_rpjmd_program where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_unik='" . $v['id_unik']."' AND id_unik_indikator='" . $v['id_unik_indikator']."'");
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
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				if (!empty($_POST['tujuan'])) {
					$tujuan = $_POST['tujuan'];
					$wpdb->update('data_rpd_tujuan', array('active' => 0), array('active' => 1));
					foreach ($tujuan as $k => $v) {
						if(empty($v['id_tujuan'])){
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_tujuan from data_rpd_tujuan where id_unik='" . $v['id_unik']."' AND id_unik_indikator='" . $v['id_unik_indikator']."'");
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
						if(empty($v['id_sasaran'])){
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_sasaran from data_rpd_sasaran where id_unik='" . $v['id_unik']."' AND id_unik_indikator='" . $v['id_unik_indikator']."'");
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
						if(empty($v['id_program'])){
							continue;
						}
						$cek = $wpdb->get_var("SELECT id_program from data_rpd_program where id_unik='" . $v['id_unik']."' AND id_unik_indikator='" . $v['id_unik_indikator']."'");
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['dana'])) {
					$sumber_dana = $_POST['dana'];
					foreach ($sumber_dana as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_dana from data_sumber_dana where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_dana=" . $v['id_dana']);
						$opsi = array(
							'created_at' => $v['created_at'],
							'created_user' => $v['created_user'],
							'id_daerah' => $v['id_daerah'],
							'id_dana' => $v['id_dana'],
							'id_unik' => $v['id_unik'],
							'is_locked' => $v['is_locked'],
							'kode_dana' => $v['kode_dana'],
							'nama_dana' => $v['nama_dana'],
							'set_input' => $v['set_input'],
							'status' => $v['status'],
							'tahun' => $v['tahun'],
							'updated_user' => $v['updated_user'],
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['sumber_dana'])){
					$sd_fmis = array();
					$sumber_dana = json_decode(stripslashes(html_entity_decode($_POST['sumber_dana'])));
					foreach($sumber_dana as $v){
						$v = (array) $v;
						$sd_fmis[$v['uraian']] = $v;
					}
					$cek_sipd_belum_ada_di_fmis = array();

					$dana = $wpdb->get_results($wpdb->prepare("
						SELECT 
							*
						from data_sumber_dana
						where tahun_anggaran=%d",
					$_POST['tahun_anggaran']), ARRAY_A);
					foreach($dana as $v){
						$nama = explode('] - ', $v['nama_dana']);
						$nama = trim(str_replace(' - ', '-', $nama[1]));
						if(empty($sd_fmis[$nama])){
							$cek_sipd_belum_ada_di_fmis[$nama] = $v;
						}
					}

					$dana_pendapatan = $wpdb->get_results($wpdb->prepare("
						SELECT 
							nama_akun as nama_dana,
							kode_akun as kode_dana,
							'' as iddana,
							'pendapatan' as jenis
						from data_pendapatan
						where tahun_anggaran=%d
						group by nama_akun",
					$_POST['tahun_anggaran']), ARRAY_A);
					foreach($dana_pendapatan as $v){
						$nama = $v['nama_dana'];
						$nama = trim(str_replace(' - ', '-', $nama));
						if(empty($sd_fmis[$nama])){
							$cek_sipd_belum_ada_di_fmis[$nama] = $v;
						}
					}

					$dana_pembiayaan = $wpdb->get_results($wpdb->prepare("
						SELECT 
							nama_akun as nama_dana,
							kode_akun as kode_dana,
							'' as iddana,
							type as jenis
						from data_pembiayaan
						where tahun_anggaran=%d
						group by nama_akun",
					$_POST['tahun_anggaran']), ARRAY_A);
					foreach($dana_pembiayaan as $v){
						$nama = $v['nama_dana'];
						$nama = trim(str_replace(' - ', '-', $nama));
						if(empty($sd_fmis[$nama])){
							$cek_sipd_belum_ada_di_fmis[$nama] = $v;
						}
					}
					
					$current_mapping = get_option('_crb_custom_mapping_sumberdana_fmis');
					$current_mapping = explode(',', $current_mapping);
					$mapping_rek = array();
					foreach($current_mapping as $v){
						$rek = explode('-', $v);
						$mapping_rek[$rek[0]] = '';
						if(!empty($rek[1])){
							$mapping_rek[$rek[0]] = $rek[1];
						}
					}

					$mapping = array();
					foreach($cek_sipd_belum_ada_di_fmis as $k => $v){
						$k = '['.$k.']';
						if(!empty($mapping_rek[$k])){
							$mapping[] = $k.'-'.$mapping_rek[$k];
						}else{
							$mapping[] = $k.'-'.$k;
						}
					}
					update_option( '_crb_custom_mapping_sumberdana_fmis', implode(',', $mapping) );
					$ret['data'] = $cek_sipd_belum_ada_di_fmis;
					$ret['total'] = count($dana)+count($dana_pendapatan)+count($dana_pembiayaan);
				}else{
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['alamat'])) {
					$alamat = $_POST['alamat'];
					foreach ($alamat as $k => $v) {
						$where = '';
						$where_a = array(
							'tahun' => $_POST['tahun_anggaran'],
							'id_alamat' => $v['id_alamat']
						);
						if(!empty($v['is_prov'])){
							$where = " AND is_prov=" . $v['is_prov'];
							$where_a['is_prov'] = $v['is_prov'];
						}else if(!empty($v['is_kab'])){
							$where = " AND is_kab=" . $v['is_kab'];
							$where_a['is_kab'] = $v['is_kab'];
						}else if(!empty($v['is_kec'])){
							$where = " AND is_kec=" . $v['is_kec'];
							$where_a['is_kec'] = $v['is_kec'];
						}else if(!empty($v['is_kel'])){
							$where = " AND is_kel=" . $v['is_kel'];
							$where_a['is_kel'] = $v['is_kel'];
						}
						$cek = $wpdb->get_var("
							SELECT 
								id_alamat 
							from data_alamat 
							where tahun=".$_POST['tahun_anggaran']
								." AND id_alamat=" . $v['id_alamat']
								.$where
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

	public function replace_char($str){
		// $str = preg_replace("/(\r?\n){2,}/", " ", trim($str));
		$str = trim($str);
    	$str = html_entity_decode($str, ENT_QUOTES | ENT_XML1, 'UTF-8');
		$str = str_replace(
			array('"', "'",'\\', '&#039'), 
			array('petik_dua', 'petik_satu', '', 'petik_satu'), 
			$str
		);
		return $str;
	}

	public function get_alamat($input, $rincian, $no=0){
	    global $wpdb;
	    $profile = false;
	    if(!empty($rincian['id_penerima'])){
	        $profile = $wpdb->get_row("SELECT * from data_profile_penerima_bantuan where id_profil=".$rincian['id_penerima']." and tahun=".$input['tahun_anggaran'], ARRAY_A);
	    }
	    $alamat = '';
	    $lokus_akun_teks = $this->replace_char($rincian['lokus_akun_teks']);
	    if(!empty($profile)){
	        $alamat = $profile['alamat_teks'].' ('.$profile['jenis_penerima'].')';
	    }else if(!empty($lokus_akun_teks)){
	        $profile = $wpdb->get_row($wpdb->prepare("
	            SELECT 
	                alamat_teks, 
	                jenis_penerima 
	            from data_profile_penerima_bantuan 
	            where BINARY nama_teks=%s 
	                and tahun=%d", $lokus_akun_teks, $input['tahun_anggaran']
	        ), ARRAY_A);
	        if(!empty($profile)){
	            $alamat = $profile['alamat_teks'].' ('.$profile['jenis_penerima'].')';
	        }else{
	            if(
	                strpos($lokus_akun_teks, 'petik_satu') !== false 
	                && $no <= 1
	            ){
	            	$no++;
	                $rincian['lokus_akun_teks'] = str_replace('petik_satu', 'petik_satupetik_satu', $lokus_akun_teks);
	                return $this->get_alamat($input, $rincian, $no);
	            }else{
	                echo "<script>console.log('".$rincian['lokus_akun_teks']."', \"".preg_replace('!\s+!', ' ', str_replace(array("\n", "\r"), " ", htmlentities($wpdb->last_query)))."\");</script>";
	            }
	        }
	    }
	    return array(
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['profile'])) {
					$profile = $_POST['profile'];
					foreach ($profile as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_profil from data_profile_penerima_bantuan where tahun=".$_POST['tahun_anggaran']." AND id_profil=" . $v['id_profil']);
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['data_user'])) {
					foreach ($_POST['data_user'] as $key => $data_user) {
						$cek = $wpdb->get_var("
							SELECT 
								userName 
							from data_user_penatausahaan 
							where tahun=".$_POST['tahun_anggaran']." 
								AND userName='" . $data_user['userName']."' 
								AND idUser='".$data_user['idUser']."'"
						);
						$opsi = array(
							"idSkpd" => $data_user['skpd']['idSkpd'],
							"namaSkpd" => $data_user['skpd']['namaSkpd'],
							"kodeSkpd" => $data_user['skpd']['kodeSkpd'],
							"idDaerah" => $data_user['skpd']['idDaerah'],
							"userName" => $data_user['userName'],
							"nip" => $data_user['nip'],
							"fullName" => $data_user['fullName'],
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
							'tahun' => $_POST['tahun_anggaran'],
							'updated_at' => current_time('mysql')
						);

						if (!empty($cek)) {
							$wpdb->update('data_user_penatausahaan', $opsi, array(
								'tahun' => $_POST['tahun_anggaran'],
								'userName' => $data_user['userName'],
								'idUser' => $data_user['idUser'],
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['data'])) {
					$data_unit = $_POST['data'];
					$cek = $wpdb->get_var($wpdb->prepare("SELECT kode_skpd from data_unit_pagu where tahun_anggaran=%d AND kode_skpd=%s", $_POST['tahun_anggaran'], $data_unit['kode_skpd']));
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if (!empty($_POST['data'])) {
					$data = $_POST['data'];
					$unit = array();
					foreach ($data as $k => $v) {
						$unit[$v['id_unit']] = $v['id_unit'];
					}
					foreach ($unit as $k => $v) {
						$wpdb->update('data_renstra', array( 'active' => 0 ), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'id_unit' => $k
						));
					}
					foreach ($data as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_renstra from data_renstra where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_renstra=" . $v['id_renstra']);
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$sub_bl = $wpdb->get_results($wpdb->prepare("
					SELECT 
						kode_sbl 
					from data_sub_keg_bl 
					where tahun_anggaran=%d 
						AND id_sub_skpd=%s"
					, $_POST['tahun_anggaran'], $_POST['id_unit']
				), ARRAY_A);
				foreach ($sub_bl as $k => $sub) {
					$cek_aktif = false;
					foreach ($_POST['subkeg_aktif'] as $v) {
						if($v['kode_sbl'] == $sub['kode_sbl']){
							$cek_aktif = true;
							break;
						}
					}
					$aktif = 0;
					if($cek_aktif){
						$aktif = 1;
					}
					$wpdb->update('data_sub_keg_bl', array( 'active' => $aktif ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'id_sub_skpd' => $_POST['id_unit'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_sub_keg_indikator', array( 'active' => $aktif ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_keg_indikator_hasil', array( 'active' => $aktif ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_tag_sub_keg', array( 'active' => $aktif ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_capaian_prog_sub_keg', array( 'active' => $aktif ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_output_giat_sub_keg', array( 'active' => $aktif ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_dana_sub_keg', array( 'active' => $aktif ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_lokasi_sub_keg', array( 'active' => $aktif ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
                    $wpdb->update('data_rka', array( 'active' => $aktif ), array(
                        'tahun_anggaran' => $_POST['tahun_anggaran'],
                        'kode_sbl' => $sub['kode_sbl']
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$wpdb->update('data_sub_keg_bl', array( 'active' => 0 ), array(
					'tahun_anggaran' => $_POST['tahun_anggaran'],
					'id_sub_skpd' => $_POST['id_unit']
				));
				$sub_bl = $wpdb->get_results("SELECT kode_sbl, id_bidang_urusan from data_sub_keg_bl where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_sub_skpd='" . $_POST['id_unit'] . "'", ARRAY_A);
				foreach ($sub_bl as $k => $sub) {
					$kode_sbl = explode('.', $sub['kode_sbl']);
					$kode_sbl1 = $kode_sbl[0].'.'.$kode_sbl[1].'.'.$sub['id_bidang_urusan'].'.'.$kode_sbl[2].'.'.$kode_sbl[3].'.'.$kode_sbl[4];
					$kode_sbl2 = $kode_sbl[1].'.'.$kode_sbl1;
					$wpdb->update('data_anggaran_kas', array( 'active' => 0 ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $kode_sbl1
					));
					$wpdb->update('data_anggaran_kas', array( 'active' => 0 ), array(
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$parent_cat_name = 'Semua Perangkat Daerah Tahun Anggaran ' . $_POST['tahun_anggaran'];
				$taxonomy = 'category';
				$parent_cat  = get_term_by('name', $parent_cat_name, $taxonomy);
				if ($parent_cat == false) {
					$parent_cat = wp_insert_term($parent_cat_name, $taxonomy);
					$parent_cat_id = $parent_cat['term_id'];
				} else {
					$parent_cat_id = $parent_cat->term_id;
				}

				$kodeunit = '';
				if (!empty($_POST['data_unit'])) {
					$data_unit = $_POST['data_unit'];
					$kodeunit = $data_unit['kodeunit'];
					$_POST['nama_skpd'] = $data_unit['namaunit'];
					$_POST['kode_sub_skpd'] = $data_unit['kodeunit'];
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data Unit Salah!';
				}
				if(!isset($_POST['idsubbl'])){
					$_POST['idsubbl'] = '';
				}
				if(!isset($_POST['idbl'])){
					$_POST['idbl'] = '';
				}
				
				$_POST['idsubbl'] = (int) $_POST['idsubbl'];
				$_POST['idbl'] = (int) $_POST['idbl'];

				if (!empty($_POST['dataBl']) && $ret['status'] != 'error') {
					$dataBl = $_POST['dataBl'];
					foreach ($dataBl as $k => $v) {
						$cek = $wpdb->get_var("SELECT kode_sbl from data_sub_keg_bl where tahun_anggaran=".$_POST['tahun_anggaran']." AND kode_sbl='" . $_POST['kode_sbl'] . "'");

						$kode_program = $v['kode_bidang_urusan'].substr($v['kode_program'], 4, strlen($v['kode_program']));
						$kode_giat = $v['kode_bidang_urusan'].substr($v['kode_giat'], 4, strlen($v['kode_giat']));
						$kode_sub_giat = $v['kode_bidang_urusan'].substr($v['kode_sub_giat'], 4, strlen($v['kode_sub_giat']));
						// die($kode_giat);
						if(!isset($v['id_sub_bl'])){
							$v['id_sub_bl'] = '';
						}
						if(!isset($v['id_bl'])){
							$v['id_bl'] = '';
						}
						if(!isset($v['idsubbl'])){
							$v['idsubbl'] = '';
						}
						if(!isset($v['idbl'])){
							$v['idbl'] = '';
						}
						if(!isset($v['id_lokasi'])){
							$v['id_lokasi'] = '';
						}
						if(!isset($v['nama_dana'])){
							$v['nama_dana'] = '';
						}
						if(!isset($v['nama_lokasi'])){
							$v['nama_lokasi'] = '';
						}
						if(!isset($v['id_dana'])){
							$v['id_dana'] = '';
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
						$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
						// print_r($custom_post); die();

						$cat_name = $_POST['kode_sub_skpd'] . ' ' . $v['nama_sub_skpd'];
						$taxonomy = 'category';
						$cat  = get_term_by('name', $cat_name, $taxonomy);
						// print_r($cat); die($cat_name);
						if ($cat == false) {
							$cat = wp_insert_term($cat_name, $taxonomy);
							$cat_id = $cat['term_id'];
						} else {
							$cat_id = $cat->term_id;
						}
						wp_update_term($cat_id, $taxonomy, array(
							'parent' => $parent_cat_id
						));

						$_post = array(
							'post_title'	=> $nama_page,
							'post_content'	=> '[tampilrka kode_bl="'.$_POST['kode_bl'].'" tahun_anggaran="'.$_POST['tahun_anggaran'].'"]',
							'post_type'		=> 'post',
							'post_status'	=> 'private',
							'comment_status'	=> 'closed'
						);
						if (empty($custom_post) || empty($custom_post->ID)) {
							$id = wp_insert_post($_post);
							$_post['insert'] = 1;
							$_post['ID'] = $id;
						}else{
							$_post['ID'] = $custom_post->ID;
							wp_update_post( $_post );
							$_post['update'] = 1;
						}
						$ret['post'] = $_post;
						$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
						update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
						update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
						update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
						update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
						update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
						update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
						update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
						update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');

						// https://stackoverflow.com/questions/3010124/wordpress-insert-category-tags-automatically-if-they-dont-exist
						$append = true;
						wp_set_post_terms($custom_post->ID, array($cat_id), $taxonomy, $append);
						$category_link = get_category_link($cat_id);

						$ret['message'] .= ' URL ' . $custom_post->guid . '?key=' . $this->gen_key($_POST['api_key']);
						$ret['category'] = $category_link;
					}
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format data BL Salah!';
				}

				if($_POST['total_page'] == $_POST['no_page']){
					if (!empty($_POST['dataOutput']) && $ret['status'] != 'error') {
						$dataOutput = $_POST['dataOutput'];
						$wpdb->update('data_sub_keg_indikator', array( 'active' => 0 ), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataOutput as $k => $v) {
							$cek = $wpdb->get_var("SELECT kode_sbl from data_sub_keg_indikator where tahun_anggaran=".$_POST['tahun_anggaran']." AND kode_sbl='" . $_POST['kode_sbl'] . "' AND idoutputbl='" . $v['idoutputbl'] . "'");
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
						$dataHasil = $_POST['dataHasil'];
						$wpdb->update('data_keg_indikator_hasil', array( 'active' => 0 ), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataHasil as $k => $v) {
							$cek = $wpdb->get_var("SELECT kode_sbl from data_keg_indikator_hasil where tahun_anggaran=".$_POST['tahun_anggaran']." AND kode_sbl='" . $_POST['kode_sbl'] . "' AND hasilteks='" . $v['hasilteks'] . "'");
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
						$dataTag = $_POST['dataTag'];
						$wpdb->update('data_tag_sub_keg', array( 'active' => 0 ), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataTag as $k => $v) {
							$cek = $wpdb->get_var("SELECT kode_sbl from data_tag_sub_keg where tahun_anggaran=".$_POST['tahun_anggaran']." AND kode_sbl='" . $_POST['kode_sbl'] . "' AND idtagbl='" . $v['idtagbl'] . "'");
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
						$dataCapaian = $_POST['dataCapaian'];
						$wpdb->update('data_capaian_prog_sub_keg', array( 'active' => 0 ), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataCapaian as $k => $v) {
							$cek = $wpdb->get_var("SELECT kode_sbl from data_capaian_prog_sub_keg where tahun_anggaran=".$_POST['tahun_anggaran']." AND kode_sbl='" . $_POST['kode_sbl'] . "' AND capaianteks='" . $v['capaianteks'] . "'");
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
						$dataOutputGiat = $_POST['dataOutputGiat'];
						$wpdb->update('data_output_giat_sub_keg', array( 'active' => 0 ), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataOutputGiat as $k => $v) {
							$cek = $wpdb->get_var("SELECT kode_sbl from data_output_giat_sub_keg where tahun_anggaran=".$_POST['tahun_anggaran']." AND kode_sbl='" . $_POST['kode_sbl'] . "' AND outputteks='" . $v['outputteks'] . "'");
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
						$dataDana = $_POST['dataDana'];
						$wpdb->update('data_dana_sub_keg', array( 'active' => 0 ), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataDana as $k => $v) {
							$cek = $wpdb->get_var("SELECT kode_sbl from data_dana_sub_keg where tahun_anggaran=".$_POST['tahun_anggaran']." AND kode_sbl='" . $_POST['kode_sbl'] . "' AND iddanasubbl='" . $v['iddanasubbl'] . "'");
							$opsi = array(
								'namadana' => $v['namadana'],
								'kodedana' => $v['kodedana'],
								'iddana' => $v['iddana'],
								'iddanasubbl' => $v['iddanasubbl'],
								'pagudana' => $v['pagudana'],
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
						$dataLokout = $_POST['dataLokout'];
						$wpdb->update('data_lokasi_sub_keg', array( 'active' => 0 ), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($dataLokout as $k => $v) {
							$cek = $wpdb->get_var("SELECT kode_sbl from data_lokasi_sub_keg where tahun_anggaran=".$_POST['tahun_anggaran']." AND kode_sbl='" . $_POST['kode_sbl'] . "' AND iddetillokasi='" . $v['iddetillokasi'] . "'");
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
						$wpdb->update('data_realisasi_akun_sipd', array( 'active' => 0 ), array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						));
						foreach ($realisasi as $k => $v) {
							$cek_id = $wpdb->get_var("SELECT kode_sbl from data_realisasi_akun_sipd where tahun_anggaran=".$_POST['tahun_anggaran']." AND kode_sbl='" . $_POST['kode_sbl'] . "' AND kode_akun='" . $v['kode_akun'] . "'");
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
					$dataDana = $_POST['dataDana'];
					foreach ($dataDana as $k => $v) {
						if(
							empty($iddana)
							&& !empty($v['iddana'])
						){
							$iddana = $v['iddana'];
						}
					}
				}
				if(empty($iddana)){
					$iddana = get_option('_crb_default_sumber_dana' );
				}

				if (!empty($_POST['rka']) && $ret['status'] != 'error') {
					$rka = $_POST['rka'];
					if(!empty($_POST['no_page']) && $_POST['no_page']==1){
						$wpdb->delete('data_rka', array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						), array('%d', '%s'));
					}
                   	if($rka == 0){
                       $rka = array();
                   	}
					foreach ($rka as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_rinci_sub_bl from data_rka where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_rinci_sub_bl='" . $v['id_rinci_sub_bl'] . "' AND kode_sbl='".$_POST['kode_sbl']."'");
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
							'active' => 1,
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);

						if(
							!empty($v['id_penerima']) 
							|| !empty($v['id_prop_penerima'])
							|| !empty($v['id_camat_penerima'])
							|| !empty($v['id_kokab_penerima'])
							|| !empty($v['id_lurah_penerima'])
						){
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

						if(!empty($v['id_rinci_sub_bl'])){
							$cek_id = $wpdb->get_var($wpdb->prepare('
								select 
									id 
								from data_mapping_sumberdana 
								where tahun_anggaran=%d
									and id_rinci_sub_bl=%d 
									and active=1', 
								$_POST['tahun_anggaran'], $v['id_rinci_sub_bl']
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
							if(!empty($v['subs_bl_teks']['sumber_dana']['id_dana'])){
								$update = true;
								$opsi['id_sumber_dana'] = $v['subs_bl_teks']['sumber_dana']['id_dana'];
							}
							if (empty($cek_id)) {
								$wpdb->insert('data_mapping_sumberdana', $opsi);
							}else if($update) {
								$wpdb->update('data_mapping_sumberdana', $opsi, array(
									'id' => $cek_id
								));
							}
						}
					}
				} else if ($ret['status'] != 'error') {
					// untuk menghapus rka subkeg yang dihapus di perubahan
					if($_POST['rka'] == 0){
						$wpdb->delete('data_rka', array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						), array('%d', '%s'));
					}else{
						$ret['status'] = 'error';
						$ret['message'] = 'Format RKA Salah!';
					}
				}

				if(
					get_option('_crb_singkron_simda') == 1
					&& get_option('_crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
					&& $_POST['total_page'] == $_POST['no_page']
				){
					$debug = false;
					if(get_option('_crb_singkron_simda_debug') == 1){
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

	public function monitoring_sql_migrate($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-sql-migrate.php';
	}

	public function monitor_json_rka($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-json-rka.php';
	}

	public function monitoring_spd_rinci($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-spd-rinci.php';
	}

	public function monitoring_data_spd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-spd.php';
	}

	public function monitor_monev_rpjm($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monev-rpjm.php';
	}

	public function monitor_monev_renstra($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monev-renstra.php';
	}

	public function monitor_daftar_label_komponen($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-daftar-label-komponen.php';
	}

	public function monitor_daftar_sumber_dana($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-daftar-sumber-dana.php';
	}

	public function monitor_monev_renja($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-indikator-renja.php';
	}

	public function monitor_rfk($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		if(!empty($atts['id_skpd'])){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-rfk.php';
		}else{
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-rfk-pemda.php';
		}
	}

	public function monitor_sumber_dana($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		if(!empty($atts['id_skpd'])){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-sumberdana.php';
		}else{
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-sumberdana-pemda.php';
		}
	}

	public function monitor_label_komponen($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-label-komponen.php';
	}

	public function monitor_sipd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-update.php';
	}
	
	public function monitor_satuan_harga($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		if(!empty($atts['id_skpd'])){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-satuan-harga.php';
		}
	}

	public function tampilrka($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-rka.php';
	}

	public function tampilrkpd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-rkpd.php';
	}

	public function apbdpenjabaran($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}

		$input = shortcode_atts( array(
			'idlabelgiat' => '',
			'lampiran' => '1',
			'id_skpd' => false,
			'tahun_anggaran' => '2021',
		), $atts );

		// RINGKASAN PENJABARAN APBD YANG DIKLASIFIKASI MENURUT KELOMPOK DAN JENIS PENDAPATAN, BELANJA, DAN PEMBIAYAAN
		if($input['lampiran'] == 1){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-apbdpenjabaran.php';
		}

		// RINCIAN APBD MENURUT URUSAN PEMERINTAHAN DAERAH, ORGANISASI, PENDAPATAN, BELANJA DAN PEMBIAYAAN
		if($input['lampiran'] == 2){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-apbdpenjabaran-2.php';
		}

		// DAFTAR NAMA CALON PENERIMA, ALAMAT DAN BESARAN ALOKASI HIBAH BERUPA UANG & BARANG YANG DITERIMA SERTA SKPD PEMBERI HIBAH
		if($input['lampiran'] == 3){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-apbdpenjabaran-3.php';
		}

		// DAFTAR NAMA CALON PENERIMA, ALAMAT DAN BESARAN ALOKASI BANTUAN SOSIAL BERUPA UANG YANG DITERIMA SERTA SKPD PEMBERI BANTUAN SOSIAL
		if($input['lampiran'] == 4){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-apbdpenjabaran-4.php';
		}

		// DAFTAR NAMA CALON PENERIMA, ALAMAT DAN BESARAN ALOKASI BANTUAN KEUANGAN BERSIFAT UMUM/KHUSUS YANG DITERIMA SERTA SKPD PEMBERI BANTUAN KEUANGAN
		if($input['lampiran'] == 5){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-apbdpenjabaran-5.php';
		}

		// DAFTAR NAMA CALON PENERIMA, ALAMAT DAN BESARAN PERUBAHAN ALOKASI BELANJA BAGI HASIL PAJAK DAERAH KEPADA PEMERINTAH KABUPATEN, KOTA DAN DESA
		if($input['lampiran'] == 6){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-apbdpenjabaran-6.php';
		}

		// APBD dikelompokan berdasarkan mandatory spending atau tag label yang dipilih user ketika membuat sub kegiatan
		if($input['lampiran'] == 99){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-apbdpenjabaran-99.php';
		}
	}

	public function laporan_per_item_ssh($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-laporan-per-item-ssh.php';
	}

	public function setting_penjadwalan($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-setting-penjadwalan.php';
	}

	public function input_rpjm($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-input-rpjm.php';
	}

	public function input_renstra($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-input-renstra.php';
	}

	public function input_rpd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-input-rpd.php';
	}

	public function input_rpjpd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-input-rpjpd.php';
	}

	public function get_cat_url()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'URL ...'
		);
		if (!empty($_POST)) {
			$cat_name = $_POST['category'];
			$taxonomy = 'category';
			$cat  = get_term_by('name', $cat_name, $taxonomy);
			if (!empty($cat)) {
				$category_link = get_category_link($cat->term_id);
				$ret['message'] = 'URL Category ' . $category_link;
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'Category tidak ditemukan!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	public function get_unit(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['id_skpd'])){
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
				}else if(!empty($_POST['kode_skpd'])){
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
				}else{
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

	public function get_all_sub_unit(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['id_skpd'])){
					$ret['data'] = $wpdb->get_results(
						$wpdb->prepare("
						SELECT 
							*
						from data_unit
						where id_skpd=%d
							AND tahun_anggaran=%d
							AND active=1", $_POST['id_skpd'], $_POST['tahun_anggaran']),
						ARRAY_A
					);
					if(!empty($ret['data']) && $ret['data'][0]['isskpd'] == 0){
						$ret['data'] = $wpdb->get_results(
							$wpdb->prepare("
							SELECT 
								*
							from data_unit
							where idinduk=%d
								AND tahun_anggaran=%d
								AND active=1
							order by id_skpd ASC", $ret['data'][0]['idinduk'], $_POST['tahun_anggaran']),
							ARRAY_A
						);
					}else if(!empty($ret['data'])){
						$ret['data'] = $wpdb->get_results(
							$wpdb->prepare("
							SELECT 
								*
							from data_unit
							where idinduk=%d
								AND tahun_anggaran=%d
								AND active=1
							order by id_skpd ASC", $ret['data'][0]['id_skpd'], $_POST['tahun_anggaran']),
							ARRAY_A
						);
					}
					$ret['query'] = $wpdb->last_query;
					$ret['id_skpd'] = $_POST['id_skpd'];
				}else{
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

	public function get_indikator(){
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['kode_giat']) && !empty($_POST['kode_skpd'])){
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
					if(!empty($bl)){
						$ret['data']['renstra'] = $wpdb->get_results("
							SELECT 
								*
							from data_renstra
							where id_unit=".$bl[0]['id_sub_skpd']."
								AND id_sub_giat=".$bl[0]['id_sub_giat']."
								AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
								AND active=1",
							ARRAY_A
						);
						$ret['data']['output'] = $wpdb->get_results("
							SELECT 
								* 
							from data_output_giat_sub_keg 
							where kode_sbl='".$bl[0]['kode_sbl']."' 
								AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
								AND active=1"
							, ARRAY_A
						);
						$ret['data']['hasil'] = $wpdb->get_results("
							SELECT 
								* 
							from data_keg_indikator_hasil 
							where kode_sbl='".$bl[0]['kode_sbl']."' 
								AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
								AND active=1"
							, ARRAY_A
						);
						$ret['data']['ind_prog'] = $wpdb->get_results("
							SELECT 
								* 
							from data_capaian_prog_sub_keg 
							where kode_sbl='".$bl[0]['kode_sbl']."' 
								AND tahun_anggaran=".$bl[0]['tahun_anggaran']."
								AND active=1"
							, ARRAY_A
						);
					}
				}else{
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

	function singkron_anggaran_kas(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil Singkron Anggaran Kas',
			'action'	=> $_POST['action']
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(
					!empty($_POST['type']) 
					|| (
						!empty($_POST['kode_sbl']) && $_POST['type']=='belanja'
					)
				){
					$wpdb->update('data_anggaran_kas', array( 'active' => 0 ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'type' => $_POST['type'],
						'id_unit' => $_POST['id_skpd'],
						'kode_sbl' => $_POST['kode_sbl']
					));
					if(!empty($_POST['data'])){
						$data = $_POST['data'];
						foreach ($data as $k => $v) {
							if(empty($v['id_akun'])){
								continue;
							}
							$cek = $wpdb->get_var("
								SELECT 
									id_akun 
								from data_anggaran_kas 
								where tahun_anggaran=".$_POST['tahun_anggaran']." 
									AND kode_sbl='" . $_POST['kode_sbl']."' 
									AND id_unit='" . $_POST['id_skpd']."' 
									AND type='" . $_POST['type']."' 
									AND id_akun=".$v['id_akun']);
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
								'type' => $_POST['type'],
								'tahun_anggaran' => $_POST['tahun_anggaran'],
								'updated_at' => current_time('mysql')
							);

							if (!empty($cek)) {
								$wpdb->update('data_anggaran_kas', $opsi, array(
									'tahun_anggaran' => $_POST['tahun_anggaran'],
									'kode_sbl' => $_POST['kode_sbl'],
									'type' => $_POST['type'],
									'id_unit' => $_POST['id_skpd'],
									'id_akun' => $v['id_akun']
								));
							} else {
								$wpdb->insert('data_anggaran_kas', $opsi);
							}
						}
					}
					if(
						get_option('_crb_singkron_simda') == 1
						&& get_option('_crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
					){
						$debug = false;
						if(get_option('_crb_singkron_simda_debug') == 1){
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

	function get_kas_fmis($no_debug=false){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
				if($type == 4){
					$kas_p = array();
					$data_sub_keg = $wpdb->get_results($wpdb->prepare("
						SELECT 
							s.*,
							u.nama_skpd as nama_skpd_data_unit
						from data_sub_keg_bl s 
						inner join data_unit u on s.id_sub_skpd = u.id_skpd
							and u.tahun_anggaran = s.tahun_anggaran
							and u.active = s.active
						where s.tahun_anggaran=%d
							and u.id_skpd IN (".implode(',', $id_skpd_sipd).")
							and s.active=1", 
					$tahun_anggaran), ARRAY_A);
					foreach($data_sub_keg as $k => $sub){
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
						if(!empty($program_mapping[$this->removeNewline($sub['nama_program'])])){
							$newsub['nama_program'] = $program_mapping[$this->removeNewline($sub['nama_program'])];
						}
						if(!empty($keg_mapping[$this->removeNewline($sub['nama_giat'])])){
							$newsub['nama_giat'] = $keg_mapping[$this->removeNewline($sub['nama_giat'])];
						}
						$nama_sub_giat = explode(' ', $sub['nama_sub_giat']);
						$kode_sub = $nama_sub_giat[0];
						unset($nama_sub_giat[0]);
						$nama_sub_giat = implode(' ', $nama_sub_giat);
						if(!empty($subkeg_mapping[$this->removeNewline($nama_sub_giat)])){
							$newsub['nama_sub_giat'] = $kode_sub.' '.$subkeg_mapping[$this->removeNewline($nama_sub_giat)];
						}
						$newsub['id_mapping'] = get_option('_crb_unit_fmis_'.$tahun_anggaran.'_'.$sub['id_sub_skpd']);
						$kas = array();
						$kode_sbl = explode('.', $sub['kode_sbl']);
						$kode_sbl = $kode_sbl[1].'.'.$kode_sbl[0].'.'.$kode_sbl[1].'.'.$sub['id_bidang_urusan'].'.'.$kode_sbl[2].'.'.$kode_sbl[3].'.'.$kode_sbl[4];
						$kas = $wpdb->get_results("
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
							where kode_sbl='".$kode_sbl."' 
								AND tahun_anggaran=".$sub['tahun_anggaran']."
								AND active=1"
							, ARRAY_A
						);
						foreach($kas as $n => $v){
							$_kode_akun = explode('.', $v['kode_akun']);
							$kode_akun = array();
							foreach ($_kode_akun as $vv) {
								$kode_akun[] = (int)$vv;
							}
							$kode_akun = implode('.', $kode_akun);
							if(!empty($rek_mapping[$kode_akun])){
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

						if(empty($kas_p[$sub['id_sub_skpd']])){
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
							$newsub['id_mapping'] = get_option('_crb_unit_fmis_'.$tahun_anggaran.'_'.$sub['id_sub_skpd']);
							$kas = $wpdb->get_results("
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
									AND id_skpd = ".$sub['id_sub_skpd']."
									AND tahun_anggaran=".$sub['tahun_anggaran']."
									AND active=1"
								, ARRAY_A
							);
							foreach($kas as $n => $v){
								$_kode_akun = explode('.', $v['kode_akun']);
								$kode_akun = array();
								foreach ($_kode_akun as $vv) {
									$kode_akun[] = (int)$vv;
								}
								$kode_akun = implode('.', $kode_akun);
								if(!empty($rek_mapping[$kode_akun])){
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
				}else if($type == 5){
					$data_sub_keg = $wpdb->get_results($wpdb->prepare("
						SELECT 
							s.*,
							u.nama_skpd as nama_skpd_data_unit
						from data_sub_keg_bl s 
						inner join data_unit u on s.id_sub_skpd = u.id_skpd
							and u.tahun_anggaran = s.tahun_anggaran
							and u.active = s.active
						where s.tahun_anggaran=%d
							and u.id_skpd IN (".implode(',', $id_skpd_sipd).")
							and s.active=1", 
					$tahun_anggaran), ARRAY_A);
					$kas = array();
					$kas_p = array();
					foreach($data_sub_keg as $k => $sub){
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
						if(!empty($program_mapping[$sub['nama_program']])){
							$newsub['nama_program'] = $program_mapping[$sub['nama_program']];
						}
						if(!empty($keg_mapping[$sub['nama_giat']])){
							$newsub['nama_giat'] = $keg_mapping[$sub['nama_giat']];
						}
						$nama_sub_giat = explode(' ', $sub['nama_sub_giat']);
						$kode_sub = $nama_sub_giat[0];
						unset($nama_sub_giat[0]);
						$nama_sub_giat = implode(' ', $nama_sub_giat);
						if(!empty($subkeg_mapping[$nama_sub_giat])){
							$newsub['nama_sub_giat'] = $kode_sub.' '.$subkeg_mapping[$nama_sub_giat];
						}
						$newsub['id_mapping'] = get_option('_crb_unit_fmis_'.$tahun_anggaran.'_'.$sub['id_sub_skpd']);
						$kd_unit_simda = explode('.', get_option('_crb_unit_'.$sub['id_sub_skpd']));
						$_kd_urusan = $kd_unit_simda[0];
						$_kd_bidang = $kd_unit_simda[1];
						$kd_unit = $kd_unit_simda[2];
						$kd_sub_unit = $kd_unit_simda[3];
						$kd = explode('.', $sub['kode_sub_giat']);
						$kd_urusan90 = (int) $kd[0];
						$kd_bidang90 = (int) $kd[1];
						$kd_program90 = (int) $kd[2];
						$kd_kegiatan90 = ((int) $kd[3]).'.'.$kd[4];
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
						if(!empty($mapping[0]) && !empty($mapping[0]->kd_urusan)){
							$kd_urusan = $mapping[0]->kd_urusan;
							$kd_bidang = $mapping[0]->kd_bidang;
							$kd_prog = $mapping[0]->kd_prog;
							$kd_keg = $mapping[0]->kd_keg;
						}
					    foreach ($this->simda->custom_mapping as $c_map_k => $c_map_v) {
					        if(
					            $skpd['kode_skpd'] == $c_map_v['sipd']['kode_skpd']
					            && $sub['kode_sub_giat'] == $c_map_v['sipd']['kode_sub_keg']
					        ){
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
				        $id_prog = $kd_urusan.$this->simda->CekNull($kd_bidang);
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
						foreach($kas as $n => $v){
							$_kode_akun = explode('.', $v['kode_akun']);
							$kode_akun = array();
							foreach ($_kode_akun as $vv) {
								$kode_akun[] = (int)$vv;
							}
							$kode_akun = implode('.', $kode_akun);
							if(!empty($rek_mapping[$kode_akun])){
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

						if(empty($kas_p[$sub['id_sub_skpd']])){
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
							$newsub['id_mapping'] = get_option('_crb_unit_fmis_'.$tahun_anggaran.'_'.$sub['id_sub_skpd']);
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
							foreach($kas as $n => $v){
								$_kode_akun = explode('.', $v['kode_akun']);
								$kode_akun = array();
								foreach ($_kode_akun as $vv) {
									$kode_akun[] = (int)$vv;
								}
								$kode_akun = implode('.', $kode_akun);
								if(!empty($rek_mapping[$kode_akun])){
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
		if($no_debug){
			return $ret;
		}else{
			die(json_encode($ret));
		}
	}

	function get_kas($no_debug=false){
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(
					!empty($_POST['kode_giat']) 
					&& !empty($_POST['kode_skpd'])
				){
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
					foreach ($ret['data']['bl'] as $k => $v) {
						$kode_sbl = explode('.', $v['kode_sbl']);
						// id_unit.id_skpd.id_sub_skpd (format kode_sbl terbaru)
						$kode_sbl = $kode_sbl[1].'.'.$kode_sbl[0].'.'.$kode_sbl[1].'.'.$v['id_bidang_urusan'].'.'.$kode_sbl[2].'.'.$kode_sbl[3].'.'.$kode_sbl[4];
						$kas = $wpdb->get_results("
							SELECT 
								* 
							from data_anggaran_kas 
							where kode_sbl='".$kode_sbl."' 
								AND tahun_anggaran=".$v['tahun_anggaran']."
								AND active=1"
							, ARRAY_A
						);
						if(!empty($kas)){
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
		if($no_debug){
			return $ret;
		}else{
			die(json_encode($ret));
		}
	}

	function get_data_rka(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'post'	=> array('idrincisbl' => $_POST['idbelanjarinci']),
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['kode_sbl'])){
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
					if(!empty($ret['data'])){
						$ret['data']['nama_prop'] = $wpdb->get_row("select nama from data_alamat where id_alamat=".$ret['data']['id_prop_penerima']." and is_prov=1", ARRAY_A);
						$ret['data']['nama_kab'] = $wpdb->get_row("select nama from data_alamat where id_alamat=".$ret['data']['id_kokab_penerima']." and is_kab=1", ARRAY_A);
						$ret['data']['nama_kec'] = $wpdb->get_row("select nama from data_alamat where id_alamat=".$ret['data']['id_camat_penerima']." and is_kec=1", ARRAY_A);
						$ret['data']['nama_kel'] = $wpdb->get_row("select nama from data_alamat where id_alamat=".$ret['data']['id_lurah_penerima']." and is_kel=1", ARRAY_A);
					}
				}else{
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

	function get_up(){
		$this->simda->get_up_simda();
	}

	function get_link_laporan(){
		global $wpdb;
		$ret = $_POST;
		$ret['status'] = 'success';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['kode_bl'])){
					$kode_bl = explode('.', $_POST['kode_bl']);
					unset($kode_bl[2]);
					// kode_skpd diselect dari data_unit karena pada tabel data_sub_keg_bl kolom kode_sub_skpd dan nama skpd salah
					$bl = $wpdb->get_results($wpdb->prepare("
						select 
							u.kode_skpd,
							s.kode_bidang_urusan,
							s.kode_giat,
							s.nama_giat 
						from data_sub_keg_bl s
							left join data_unit u on s.id_sub_skpd=u.id_skpd
						where s.kode_bl=%s 
							and s.active=1 
							and s.tahun_anggaran=%d", implode('.', $kode_bl), $_POST['tahun_anggaran']
					), ARRAY_A);
					$ret['query'] = $wpdb->last_query;
					$kodeunit = $bl[0]['kode_skpd'];
					$kode_giat = $bl[0]['kode_bidang_urusan'].substr($bl[0]['kode_giat'], 4, strlen($bl[0]['kode_giat']));
					$nama_page = $_POST['tahun_anggaran'] . ' | ' . $kodeunit . ' | ' . $kode_giat . ' | ' . $bl[0]['nama_giat'];
					$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
					$ret['link'] = $this->get_link_post($custom_post);
					$ret['text_link'] = 'Print DPA Lokal';
					$ret['judul'] = $nama_page;
					$ret['bl'] = $bl;
				}else{
					$nama_page = $_POST['tahun_anggaran'] . ' | Laporan';
					$cat_name = $_POST['tahun_anggaran'] . ' APBD';
					$post_content = '';
					
					if(
						(
							$_POST['jenis'] == '1'
							|| $_POST['jenis'] == '3'
							|| $_POST['jenis'] == '4'
							|| $_POST['jenis'] == '5'
							|| $_POST['jenis'] == '6'
						)
						&& $_POST['model'] == 'perkada'
						&& $_POST['cetak'] == 'apbd'
					){
						$nama_page = $_POST['tahun_anggaran'] . ' | APBD PENJABARAN Lampiran '.$_POST['jenis'];
						$cat_name = $_POST['tahun_anggaran'] . ' APBD';
						$post_content = '[apbdpenjabaran tahun_anggaran="'.$_POST['tahun_anggaran'].'" lampiran="'.$_POST['jenis'].'"]';
						$ret['text_link'] = 'Print APBD PENJABARAN Lampiran '.$_POST['jenis'];
						$custom_post = $this->save_update_post($nama_page, $cat_name, $post_content);
						$ret['link'] = $this->get_link_post($custom_post);
					}else if(
						$_POST['jenis'] == '2'
						&& $_POST['model'] == 'perkada'
						&& $_POST['cetak'] == 'apbd'
					){
						$sql = $wpdb->prepare("
						    select 
						        id_skpd,
						        kode_skpd,
						        nama_skpd
						    from data_unit
						    where tahun_anggaran=%d
						        and active=1
						", $_POST['tahun_anggaran']);
						$unit = $wpdb->get_results($sql, ARRAY_A);
						$ret['link'] = array();
						foreach ($unit as $k => $v) {
							$nama_page = $_POST['tahun_anggaran'] .' | '.$v['kode_skpd'].' | '.$v['nama_skpd'].' | '. ' | APBD PENJABARAN Lampiran 2';
							$cat_name = $_POST['tahun_anggaran'] . ' APBD';
							$post_content = '[apbdpenjabaran tahun_anggaran="'.$_POST['tahun_anggaran'].'" lampiran="'.$_POST['jenis'].'" id_skpd="'.$v['id_skpd'].'"]';
							$custom_post = $this->save_update_post($nama_page, $cat_name, $post_content);
							$ret['link'][$v['id_skpd']] = array(
								'id_skpd' => $v['id_skpd'],
								'text_link' => 'Print APBD PENJABARAN Lampiran 2',
								'link' => $this->get_link_post($custom_post)
							);
						}
					}else{
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

	function save_update_post($nama_page, $cat_name, $post_content){
		$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
		$taxonomy = 'category';
		$cat  = get_term_by('name', $cat_name, $taxonomy);
		if ($cat == false) {
			$cat = wp_insert_term($cat_name, $taxonomy);
			$cat_id = $cat['term_id'];
		} else {
			$cat_id = $cat->term_id;
		}

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
		}else{
			$_post['ID'] = $custom_post->ID;
			wp_update_post( $_post );
			$_post['update'] = 1;
		}
		$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
		update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
		update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
		update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
		update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
		update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
		update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
		update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
		update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');

		// https://stackoverflow.com/questions/3010124/wordpress-insert-category-tags-automatically-if-they-dont-exist
		$append = true;
		wp_set_post_terms($custom_post->ID, array($cat_id), $taxonomy, $append);
		return $custom_post;
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

	function penyebut($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = $this->penyebut($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . $this->penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . $this->penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->penyebut($nilai/1000000000) . " milyar" . $this->penyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->penyebut($nilai/1000000000000) . " trilyun" . $this->penyebut(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
 
	function terbilang($nilai) {
		if($nilai==0) {
			$hasil = "nol";
		}else if($nilai<0) {
			$hasil = "minus ". trim($this->penyebut($nilai));
		} else {
			$hasil = trim($this->penyebut($nilai));
		}     		
		return $hasil.' rupiah';
	}

	function get_bulan($bulan) {
		if(empty($bulan)){
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
		return $nama_bulan[((int) $bulan)-1];
	}

	function singkron_pendahuluan(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil singkron data pendahuluan (SEKDA & TAPD)';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['data'])){
					$wpdb->update('data_user_tapd_sekda', array( 'active' => 0 ), array(
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
						        and active=1
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
						        and active=1
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
				}else{
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


	function ubah_minus($nilai){
	    if($nilai < 0){
	        $nilai = abs($nilai);
	        return '('.number_format($nilai,0,",",".").')';
	    }else{
	        return number_format($nilai,0,",",".");
	    }
	}

	function gen_user_sipd_merah($user = array()){
		global $wpdb;
		if(!empty($user)){
			$username = $user['loginname'];
			$email = $user['emailteks'];
			if(empty($email)){
				$email = $username.'@sipdlocal.com';
			}
			$role = get_role($user['jabatan']);
			if(empty($role)){
				add_role( $user['jabatan'], $user['jabatan'], array( 
					'read' => true,
					'edit_posts' => false,
					'delete_posts' => false
				) );
			}
			$insert_user = username_exists($username);
			if(!$insert_user){
				$option = array(
					'user_login' => $username,
					'user_pass' => $user['pass'],
					'user_email' => $email,
					'first_name' => $user['nama'],
					'display_name' => $user['nama'],
					'role' => $user['jabatan']
				);
				$insert_user = wp_insert_user($option);
			}

			$skpd = $wpdb->get_var("SELECT nama_skpd from data_unit where id_skpd=".$user['id_sub_skpd']." AND active=1");
			$meta = array(
			    '_crb_nama_skpd' => $skpd,
			    '_id_sub_skpd' => $user['id_sub_skpd'],
			    '_nip' => $user['nip'],
			    'id_user_sipd' => $user['iduser'],
			    'description' => 'User dibuat dari data SIPD Merah'
			);
		    foreach( $meta as $key => $val ) {
		      	update_user_meta( $insert_user, $key, $val ); 
		    }
		}
	}

	function generate_user_sipd_merah(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil Generate User Wordpress dari DB Lokal SIPD Merah';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$users_pa = $wpdb->get_results("SELECT * from data_unit where active=1", ARRAY_A);
				if(!empty($users_pa)){
					foreach ($users_pa as $k => $user) {
						$user['pass'] = $_POST['pass'];
						$user['loginname'] = $user['nipkepala'];
						$user['jabatan'] = $user['statuskepala'];
						$user['nama'] = $user['namakepala'];
						$user['id_sub_skpd'] = $user['id_skpd'];
						$user['nip'] = $user['nipkepala'];
						$this->gen_user_sipd_merah($user);
					}

					$users = $wpdb->get_results("SELECT * from data_dewan where active=1", ARRAY_A);
					if(!empty($users)){
						foreach ($users as $k => $user) {
							$user['pass'] = $_POST['pass'];
							if(
								$user['idlevel'] == 11
								|| $user['idlevel'] == 7
							){
								$user['jabatan'] = 'mitra_bappeda';
							}
							$this->gen_user_sipd_merah($user);
						}
					}else{
						$ret['status'] = 'error';
						$ret['message'] = 'Data user kosong. Harap lakukan singkronisasi data user dulu!';
					}
				}else{
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

	public function get_carbon_multiselect($field){
		global $wpdb;
		$table = $wpdb->prefix."options";
		return $wpdb->get_results($wpdb->prepare("
			select 
				option_value as value
			from ".$table."
			where option_name like %s
		", '_'.$field.'%'), ARRAY_A);
	}

	public function menu_monev_skpd($options){
		global $wpdb;
		$id_skpd = $options['id_skpd'];
		$nama_skpd = $options['nama_skpd'];
		echo '<div>';
		if(!empty($id_skpd)){ 
			echo "<h5 class='text_tengah' style='margin-bottom: 10px;'>$nama_skpd</h5>";
			$daftar_tombol = $this->get_carbon_multiselect('crb_daftar_tombol_user_dashboard');
			$daftar_tombol_list = array();
			foreach ($daftar_tombol as $v) {
				$daftar_tombol_list[$v['value']] = $v['value'];
			}

			$tahun = $_GET['tahun'];
			$unit = $wpdb->get_results($wpdb->prepare("
				SELECT 
					nama_skpd, 
					is_skpd, 
					id_skpd, 
					kode_skpd 
				from data_unit 
				where active=1 
					and tahun_anggaran=%d
					and id_skpd=%d",
				$tahun, $id_skpd), ARRAY_A);
			echo '<ul class="aksi-monev text_tengah">';
            foreach ($unit as $kk => $vv) {
				$user_id = um_user( 'ID' );
				$user_meta = get_userdata($user_id);
				if(
					in_array("administrator", $user_meta->roles) 
					|| in_array("PLT", $user_meta->roles) 
					|| in_array("PA", $user_meta->roles) 
					|| in_array("KPA", $user_meta->roles)
				){
					$nama_page = 'RFK '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$tahun;
					$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
					$url_rfk = $this->get_link_post($custom_post);

					if(!empty($daftar_tombol_list[1])){
						echo '<li><a href="'.$url_rfk.'" target="_blank" class="btn btn-info">MONEV RFK</a></li>';
					}

					$nama_page_sd = 'Sumber Dana '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$tahun;
					$custom_post = get_page_by_title($nama_page_sd, OBJECT, 'page');
					$url_sd = $this->get_link_post($custom_post);
					if(!empty($daftar_tombol_list[2])){
						echo '<li><a href="'.$url_sd.'" target="_blank" class="btn btn-info">MONEV SUMBER DANA</a></li>';
					}

					$nama_page_label = 'Label Komponen '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$tahun;
					$custom_post = get_page_by_title($nama_page_label, OBJECT, 'page');
					$url_label = $this->get_link_post($custom_post);
					if(!empty($daftar_tombol_list[3])){
						echo '<li><a href="'.$url_label.'" target="_blank" class="btn btn-info">MONEV LABEL KOMPONEN</a></li>';
					}

					$nama_page_monev_renja = 'MONEV '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$tahun;
					$custom_post = get_page_by_title($nama_page_monev_renja, OBJECT, 'page');
					$url_monev_renja = $this->get_link_post($custom_post);
					if(!empty($daftar_tombol_list[4])){
						echo '<li><a href="'.$url_monev_renja.'" target="_blank" class="btn btn-info">MONEV INDIKATOR RENJA</a></li>';
					}

					if($vv['is_skpd'] == 1){
						$nama_page_monev_renstra = 'MONEV RENSTRA '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$tahun;
						$custom_post = get_page_by_title($nama_page_monev_renstra, OBJECT, 'page');
						$url_monev_renstra = $this->get_link_post($custom_post);
						if(!empty($daftar_tombol_list[5])){
							echo '<li><a href="'.$url_monev_renstra.'" target="_blank" class="btn btn-info">MONEV INDIKATOR RENSTRA</a></li>';
						}
					}

					$nama_page_menu_ssh = 'Rekapitulasi Rincian Belanja '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$tahun;
					$custom_post = get_page_by_title($nama_page_menu_ssh, OBJECT, 'page');
					$url_menu_ssh = $this->get_link_post($custom_post);
					if(!empty($daftar_tombol_list[7])){
						echo '<li><a href="'.$url_menu_ssh.'" target="_blank" class="btn btn-info">MENU SSH</a></li>';
					}

					if($vv['is_skpd'] == 1){
						$nama_page = 'Input RENSTRA '.$vv['nama_skpd'].' '.$vv['kode_skpd'];
						$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
						$url_menu = $this->get_link_post($custom_post);
						if(!empty($daftar_tombol_list[8])){
							echo '<li><a href="'.$url_menu.'" target="_blank" class="btn btn-info">INPUT RENSTRA</a></li>';
						}
					}

					$nama_page = 'Input RENJA '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$tahun;
					$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
					$url_menu = $this->get_link_post($custom_post);
					if(!empty($daftar_tombol_list[9])){
						echo '<li><a href="'.$url_menu.'" target="_blank" class="btn btn-info">INPUT RENJA</a></li>';
					}
				}
			}
			echo '</ul>';

		}else{
			echo 'SKPD tidak ditemukan!';
		}
		echo '</div>';
		return;
	}

	public function menu_monev(){
		global $wpdb;
		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);
		if(empty($user_meta->roles)){
			echo 'User ini tidak dapat akses sama sekali :)';
		}else if(in_array("mitra_bappeda", $user_meta->roles)){
			$this->pilih_tahun_anggaran();
			if(empty($_GET) || empty($_GET['tahun'])){ return; }

			$id_user_sipd = get_user_meta($user_id, 'id_user_sipd');
			if(!empty($id_user_sipd)){
				$skpd_mitra = $wpdb->get_results($wpdb->prepare("
					SELECT 
						nama_skpd, 
						id_unit, 
						kode_skpd 
					from data_skpd_mitra_bappeda 
					where active=1 
						and id_user=".$id_user_sipd[0]." 
						and tahun_anggaran=%d 
					group by id_unit", $_GET['tahun']), ARRAY_A);
				foreach ($skpd_mitra as $k => $v) {
					$this->menu_monev_skpd(array(
						'id_skpd' => $v['id_unit'],
						'nama_skpd' => $v['nama_skpd']
					));
				}
			}else{
				echo 'User ID SIPD tidak ditemukan!';
			}
		}else if(
			in_array("PA", $user_meta->roles)
			|| in_array("KPA", $user_meta->roles)
			|| in_array("PLT", $user_meta->roles)
		){
			$this->pilih_tahun_anggaran();
			if(empty($_GET) || empty($_GET['tahun'])){ return; }

			$month = date('m');
			$triwulan = floor($month/3);
			$notif = '<h5 style="text-align: center; padding: 10px; border: 5px; background: #f5d3d3; text-decoration: underline; border-radius: 5px;">Sekarang awal bulan triwulan baru. Waktunya mengisi <b>MONEV indikator RENJA triwulan '.$triwulan.'</b>.<br>Jaga kesehatan & semangat!</h5>';
			if($month%3 == 1){
				if($triwulan == 0){
					$triwulan = 4;
				}
				echo $notif;
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
				group by id_skpd", $nipkepala[0], $_GET['tahun']), ARRAY_A);
			foreach ($skpd_db as $skpd) {
				$this->menu_monev_skpd(array(
					'id_skpd' => $skpd['id_skpd'],
					'nama_skpd' => $skpd['nama_skpd']
				));
				if($skpd['is_skpd'] == 1){
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
						group by id_skpd", $skpd['id_skpd'], $_GET['tahun']), ARRAY_A);
					foreach ($sub_skpd_db as $sub_skpd) {
						$this->menu_monev_skpd(array(
							'id_skpd' => $sub_skpd['id_skpd'],
							'nama_skpd' => $sub_skpd['nama_skpd']
						));
					}
				}
			}
		}else if(in_array("tapd_pp", $user_meta->roles)){
			$this->pilih_tahun_anggaran();
			$this->tampil_menu_rpjm();
			if(empty($_GET) || empty($_GET['tahun'])){ return; }

			$skpd_mitra = $wpdb->get_results($wpdb->prepare("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd 
				from data_unit 
				where active=1 
					and tahun_anggaran=%d
				group by id_skpd", $_GET['tahun']), ARRAY_A);
			foreach ($skpd_mitra as $k => $v) {
				$this->menu_monev_skpd(array(
					'id_skpd' => $v['id_skpd'],
					'nama_skpd' => $v['nama_skpd']
				));
			}
		}else{
			echo 'User ini tidak dapat akses halaman ini :)';
		}
	}

	public function tampil_menu_rpjm(){
		if(!empty($_GET) && !empty($_GET['tahun'])){
			global $wpdb;
			$daftar_tombol = $this->get_carbon_multiselect('crb_daftar_tombol_user_dashboard');
			$daftar_tombol_list = array();
			foreach ($daftar_tombol as $v) {
				$daftar_tombol_list[$v['value']] = $v['value'];
			}
			if(!empty($daftar_tombol_list[6])){
				$tahun_aktif = $_GET['tahun'];
				$custom_post = get_page_by_title('MONEV RPJM Pemerintah Daerah | '.$tahun_aktif);
				$url_pemda = $this->get_link_post($custom_post);
				echo '
				<ul class="daftar-tahun text_tengah">
					<a class="btn btn-danger" target="_blank" href="'.$url_pemda.'">MONEV RPJM</a>
				</ul>';
			}
		}

	}

	public function pilih_tahun_anggaran(){
		global $wpdb;
		$tahun_aktif = false;
		$class_hide = '';
		if(!empty($_GET) && !empty($_GET['tahun'])){
			$tahun_aktif = $_GET['tahun'];
			$class_hide = 'display: none;';
		}
		$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran', ARRAY_A);
		echo "
		<h5 class='text_tengah' style='".$class_hide."'>PILIH TAHUN ANGGARAN</h5>
		<ul class='daftar-tahun text_tengah'>";
		foreach ($tahun as $k => $v) {
			$class = 'btn-primary';
			if($tahun_aktif == $v['tahun_anggaran']){
				$class = 'btn-success';
			}
			echo "<li><a href='?tahun=".$v['tahun_anggaran']."' class='btn ".$class."'>".$v['tahun_anggaran']."</a></li>";
		}
		echo "</ul>";
	}

	public function simpan_rfk(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil simpan realisasi fisik dan keuangan!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
					if(current_user_can('administrator')){
						$opsi['catatan_verifikator'] = $v['catatan_verifikator'];
						$opsi['user_verifikator'] = $v['user_edit'];
						$opsi['update_verifikator_at'] = current_time('mysql');
					}else{
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

	public function reset_catatan_verifkator_rfk(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil reset catatan verifikator sesuai bulan sebelumnya!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$sql = $wpdb->prepare("
				    select 
				        *
				    from data_rfk
				    where tahun_anggaran=%d
				        and bulan=%d
				        and id_skpd=%d
				", $_POST['tahun_anggaran'], $_POST['bulan']-1, $_POST['id_skpd']);
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
				if(empty($rfk)){
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

	public function reset_rfk(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil reset data realisasi fisik dan keuangan sesuai bulan sebelumnya!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$sql = $wpdb->prepare("
				    select 
				        *
				    from data_rfk
				    where tahun_anggaran=%d
				        and bulan=%d
				        and id_skpd=%d
				", $_POST['tahun_anggaran'], $_POST['bulan']-1, $_POST['id_skpd']);
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
				if(empty($rfk)){
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

	function get_pagu_simda($options = array()){
		global $wpdb;
		$sumber_pagu = $options['sumber_pagu'];
		$kd_urusan = $options['kd_urusan'];
		$kd_bidang = $options['kd_bidang'];
		$kd_unit = $options['kd_unit'];
		$kd_sub = $options['kd_sub'];
		$kd_prog = $options['kd_prog'];
		$id_prog = $options['id_prog'];
		$kd_keg = $options['kd_keg'];
		if(
			$sumber_pagu == 4
			|| $sumber_pagu == 5
			|| $sumber_pagu == 6
		){
			$sql = $wpdb->prepare("
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
		if(!empty($pagu[0])){
			return $pagu[0]->total;
		}else{
			return 0;
		}
	}

	function get_pagu_simda_last($options = array()){
		global $wpdb;
		$kd_urusan = $options['kd_urusan'];
		$kd_bidang = $options['kd_bidang'];
		$kd_unit = $options['kd_unit'];
		$kd_sub = $options['kd_sub'];
		$kd_prog = $options['kd_prog'];
		$id_prog = $options['id_prog'];
		$kd_keg = $options['kd_keg'];
		$sql = $wpdb->prepare("
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
		if(!empty($pagu[0])){
			$wpdb->update('data_sub_keg_bl', array('pagu_simda' => $pagu[0]->total), array(
				'id' => $options['id_sub_keg']
			));
			return $pagu[0]->total;
		}else{
			return $options['pagu_simda'];
		}
	}

	function get_rak_simda($options = array()){
		global $wpdb;
		$kd_urusan = $options['kd_urusan'];
		$kd_bidang = $options['kd_bidang'];
		$kd_unit = $options['kd_unit'];
		$kd_sub = $options['kd_sub'];
		$kd_prog = $options['kd_prog'];
		$id_prog = $options['id_prog'];
		$kd_keg = $options['kd_keg'];

		$sql_akun = "";
		if(!empty($options['kode_akun'])){
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
            $sql_akun = $wpdb->prepare("
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

		if(!empty($options['rak_all'])){
			$sql = $wpdb->prepare("
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
			$rak = $this->simda->CurlSimda(array('query' => $sql.$sql_akun), false);
			$ret = array();
			foreach($rak as $key => $val){
				$ret[$key] = array();
				foreach($val as $k => $v){
					if(!is_numeric($k)){
						$ret[$key][$k] = $v;
					}
				}
				$ret[$key]['kode_akun'] = $val->kd_rek90_1.'.'.$val->kd_rek90_2.'.'.$this->simda->CekNull($val->kd_rek90_3).'.'.$this->simda->CekNull($val->kd_rek90_4).'.'.$this->simda->CekNull($val->kd_rek90_5).'.'.$this->simda->CekNull($val->kd_rek90_6, 4);
			}
			return $ret;
		}else{
			$sql = $wpdb->prepare("
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
			$rak = $this->simda->CurlSimda(array('query' => $sql.$sql_akun), false);
		}

		$total_rak = 0;
		if(empty($rak[0])){
			return $options['rak'];
		}else{
			for($i=1; $i<=$options['bulan']; $i++){
				$total_rak += $rak[0]->{'bulan_'.$i};
			}
		}
		if(!empty($options['kode_akun'])){
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
			if(!empty($options['id_realisasi_akun'])){
				$wpdb->update('data_realisasi_akun', $opsi, array(
					'id' => $options['id_realisasi_akun']
				));
			}else{
				$wpdb->insert('data_realisasi_akun', $opsi);
			}
		}else{
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
			if(!empty($cek)){
				$wpdb->update('data_rfk', $opsi, array(
					'tahun_anggaran' => $options['tahun_anggaran'],
					'bulan' => $options['bulan'],
					'id_skpd' => $options['id_skpd'],
					'kode_sbl' => $options['kode_sbl']
				));
			}else{
				$wpdb->insert('data_rfk', $opsi);
			}
		}
		return $total_rak;
	}

	function get_realisasi_simda($options = array()){
		global $wpdb;
		$kd_urusan = $options['kd_urusan'];
		$kd_bidang = $options['kd_bidang'];
		$kd_unit = $options['kd_unit'];
		$kd_sub = $options['kd_sub'];
		$kd_prog = $options['kd_prog'];
		$id_prog = $options['id_prog'];
		$kd_keg = $options['kd_keg'];
		$hari_mulai = $options['tahun_anggaran'].'-01-01';
		$hari_akhir = $options['tahun_anggaran'].'-'.$this->simda->CekNull($options['bulan']).'-01';
		$hari_akhir = date("Y-m-t", strtotime($hari_akhir));

		$sql_akun = "";
		if(!empty($options['kode_akun'])){
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
            $sql_akun = $wpdb->prepare("
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
		$sql = $wpdb->prepare("
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
		$pagu_sp2d = $this->simda->CurlSimda(array('query' => $sql.$sql_akun), false);

		if(empty($pagu_sp2d[0])){
			return $options['realisasi_anggaran'];
		}

		/* Penyesuaian */
		$sql = $wpdb->prepare("
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
		$pagu_penyesuaian = $this->simda->CurlSimda(array('query' => $sql.$sql_akun), false);

		/* Jurnal Koreksi */
		$sql = $wpdb->prepare("
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
		$pagu_koreksi = $this->simda->CurlSimda(array('query' => $sql.$sql_akun), false);

		$realisasi = $pagu_sp2d[0]->total - $pagu_penyesuaian[0]->total + $pagu_koreksi[0]->total_debet - $pagu_koreksi[0]->total_kredit;

		/* Jurnal BLUD / FKTP */
		if(
			$kd_urusan == 1
			AND $kd_bidang == 2
		){
			$sql = $wpdb->prepare("
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
			$pagu_blud_fktp = $this->simda->CurlSimda(array('query' => $sql.$sql_akun), false);

			$realisasi = $realisasi + $pagu_blud_fktp[0]->total;
		}

		/* Jurnal BLUD / FKTP + SIMDA BOS */
		if(
			$kd_urusan == 1
			AND (
				$kd_bidang == 1
				|| $kd_bidang == 2
			)
		){
			$sql = $wpdb->prepare("
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
			$pagu_sp3b = $this->simda->CurlSimda(array('query' => $sql.$sql_akun), false);

			$realisasi = $realisasi + $pagu_sp3b[0]->total;
		}

		if(!empty($options['kode_akun'])){
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
			if(!empty($options['id_realisasi_akun'])){
				$wpdb->update('data_realisasi_akun', $opsi, array(
					'id' => $options['id_realisasi_akun']
				));
			}else{
				$wpdb->insert('data_realisasi_akun', $opsi);
			}
		}else{
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
			if(!empty($cek)){
				$wpdb->update('data_rfk', $opsi, array(
					'tahun_anggaran' => $options['tahun_anggaran'],
					'bulan' => $options['bulan'],
					'id_skpd' => $options['id_skpd'],
					'kode_sbl' => $options['kode_sbl']
				));
			}else{
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

	function pembulatan($angka){
		$angka = $angka*100;
		return round($angka)/100;
	}

	function get_mapping(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil get data mapping!';
		$ret['data'] = array();
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['id_mapping'])){
					foreach ($_POST['id_mapping'] as $k => $id_unik) {
						$ids = explode('-', $id_unik);
						$kd_sbl = $ids[0];
						$rek = explode('.', $ids[1]);
						$rek_1 = $rek[0].'.'.$rek[1];
						$rek_2 = false;
						$rek_3 = false;
						$rek_4 = false;
						$rek_5 = false;
						$kelompok = false;
						$keterangan = false;
						$id_rinci = false;
						if(isset($rek[2])){
							$rek_2 = $rek_1.'.'.$rek[2];
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
						if(isset($ids[2])){
							$kelompok = $ids[2];
						}
						if(isset($ids[3])){
							$keterangan = $ids[3];
						}
						if(isset($ids[4])){
							$id_rinci = $ids[4];
						}
						$res = array('id_unik' => $id_unik);
						$res['data_realisasi'] = $wpdb->get_var(
							$wpdb->prepare('
								select 
									realisasi
								from data_realisasi_rincian
								where tahun_anggaran=%d
									and id_rinci_sub_bl=%d
									and active=1', 
							$_POST['tahun_anggaran'], $id_rinci )
						);
						if(empty($res['data_realisasi'])){
							$res['data_realisasi'] = 0;
						}
						$res['data_label'] = $wpdb->get_results(
							$wpdb->prepare('
								select 
									l.nama,
									l.id
								from data_mapping_label m
									left join data_label_komponen l on m.id_label_komponen=l.id
								where m.tahun_anggaran=%d
									and m.id_rinci_sub_bl=%d
									and m.active=1', 
							$_POST['tahun_anggaran'], $id_rinci )
						);
						$sql = $wpdb->prepare('
								select 
									s.nama_dana,
									s.id_dana as id
								from data_mapping_sumberdana m
								left join data_sumber_dana s on m.id_sumber_dana=s.id_dana
									and s.tahun_anggaran=m.tahun_anggaran
								where m.tahun_anggaran=%d
									and m.id_rinci_sub_bl=%d
									and m.active=1', 
							$_POST['tahun_anggaran'], $id_rinci );
						// $res['data_sumber_dana_query'] = $sql;
						$res['data_sumber_dana'] = $wpdb->get_results($sql);
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

	function singkron_renstra_tujuan(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil singkron tujuan RENSTRA!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$wpdb->update('data_renstra_tujuan', array('active' => 0), array(
					'tahun_anggaran' => $_POST['tahun_anggaran']
				));
				foreach ($_POST['tujuan'] as $k => $v) {
					if(empty($v['id_unik_indikator'])){
						$v['id_unik_indikator'] = '';
					}
					if(empty($v['id_unit'])){
						$v['id_unit'] = '';
					}
					if(empty($v['id_unik'])){
						$v['id_unik'] = '';
					}
					$cek = $wpdb->get_var("
						SELECT 
							id_unik 
						from data_renstra_tujuan 
						where tahun_anggaran=".$_POST['tahun_anggaran']." 
							AND id_unik='" . $v['id_unik']."' 
							AND id_unik_indikator='" . $v['id_unik_indikator']."' 
							AND id_unit='" . $v['id_unit']."'
							AND id_bidang_urusan='" . $v['id_bidang_urusan']."'
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

	function singkron_renstra_sasaran(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil singkron sasaran RENSTRA!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$wpdb->update('data_renstra_sasaran', array('active' => 0), array(
					'tahun_anggaran' => $_POST['tahun_anggaran']
				));
				foreach ($_POST['sasaran'] as $k => $v) {
					if(empty($v['id_unik'])){
						$v['id_unik'] = '0';
					}
					if(empty($v['id_unik_indikator'])){
						$v['id_unik_indikator'] = '0';
					}
					if(empty($v['id_bidang_urusan'])){
						$v['id_bidang_urusan'] = '0';
					}
					$cek = $wpdb->get_var("
						SELECT 
							id_unik 
						from data_renstra_sasaran 
						where tahun_anggaran=".$_POST['tahun_anggaran']." 
							AND id_unik='" . $v['id_unik']."' 
							AND id_unik_indikator='" . $v['id_unik_indikator']."'
							AND id_bidang_urusan='" . $v['id_bidang_urusan']."'
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

	function singkron_renstra_program(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil singkron program RENSTRA!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(
					empty($_POST['page']) 
					|| $_POST['page'] == 1
				){
					$wpdb->update('data_renstra_program', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
				}
				foreach ($_POST['program'] as $k => $v) {
					if(empty($v['id_unik'])){
						$v['id_unik'] = '0';
					}
					if(empty($v['id_unik_indikator'])){
						$v['id_unik_indikator'] = '0';
					}
					if(empty($v['id_program'])){
						$v['id_program'] = '0';
					}
					if(empty($v['id_bidang_urusan'])){
						$v['id_bidang_urusan'] = '0';
					}
					if(empty($v['id_visi'])){
						$v['id_visi'] = '0';
					}
					if(empty($v['id_misi'])){
						$v['id_misi'] = '0';
					}
					if(empty($v['urut_tujuan'])){
						$v['urut_tujuan'] = '0';
					}
					if(empty($v['urut_sasaran'])){
						$v['urut_sasaran'] = '0';
					}
					$cek = $wpdb->get_var("
						SELECT 
							id_unik 
						from data_renstra_program 
						where tahun_anggaran=".$_POST['tahun_anggaran']." 
							AND id_unik='" . $v['id_unik']."' 
							AND id_unik_indikator='" . $v['id_unik_indikator']."'
							AND id_program='" . $v['id_program']."'
							AND id_bidang_urusan='" . $v['id_bidang_urusan']."'
							AND id_visi='" . $v['id_visi']."'
							AND id_misi='" . $v['id_misi']."'
							AND urut_tujuan='" . $v['urut_tujuan']."'
							AND urut_sasaran='" . $v['urut_sasaran']."'
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

	function singkron_renstra_kegiatan(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil singkron kegiatan RENSTRA!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(
					empty($_POST['page']) 
					|| $_POST['page'] == 1
				){
					$wpdb->update('data_renstra_kegiatan', array('active' => 0), array(
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
				}
				foreach ($_POST['kegiatan'] as $k => $v) {
					if(empty($v['id_unik'])){
						$v['id_unik'] = '0';
					}
					if(empty($v['id_unik_indikator'])){
						$v['id_unik_indikator'] = '0';
					}
					if(empty($v['id_bidang_urusan'])){
						$v['id_bidang_urusan'] = '0';
					}
					if(empty($v['id_visi'])){
						$v['id_visi'] = '0';
					}
					if(empty($v['id_misi'])){
						$v['id_misi'] = '0';
					}
					if(empty($v['urut_tujuan'])){
						$v['urut_tujuan'] = '0';
					}
					if(empty($v['urut_sasaran'])){
						$v['urut_sasaran'] = '0';
					}
					$cek = $wpdb->get_var("
						SELECT 
							id_unik 
						from data_renstra_kegiatan 
						where tahun_anggaran=".$_POST['tahun_anggaran']." 
							AND id_unik='" . $v['id_unik']."' 
							AND id_unik_indikator='" . $v['id_unik_indikator']."'
							AND id_bidang_urusan='" . $v['id_bidang_urusan']."'
							AND id_visi='" . $v['id_visi']."'
							AND id_misi='" . $v['id_misi']."'
							AND urut_tujuan='" . $v['urut_tujuan']."'
							AND urut_sasaran='" . $v['urut_sasaran']."'
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

	function get_realisasi_akun(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil get realisasi akun rekening!';
		$ret['data'] = array();
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$kd_unit_simda = explode('.', get_option('_crb_unit_'.$_POST['id_skpd']));
				$_kd_urusan = $kd_unit_simda[0];
				$_kd_bidang = $kd_unit_simda[1];
				$kd_unit = $kd_unit_simda[2];
				$kd_sub_unit = $kd_unit_simda[3];

    			$skpd = $wpdb->get_row($wpdb->prepare("
    				SELECT 
    					nama_skpd, kode_skpd 
    				from data_unit 
    				where id_skpd=%d 
    					and tahun_anggaran=%d
    					and active=1",
    				$_POST['id_skpd'], $_POST['tahun_anggaran']), ARRAY_A);
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
					$sub_db = $wpdb->get_results($wpdb->prepare("select * from data_sub_keg_bl where active=1 and tahun_anggaran=%d and kode_sbl=%s", $_POST['tahun_anggaran'], $kode_sbl), ARRAY_A);
					if(!empty($sub_db)){
						$sub = $sub_db[0];
						$kd = explode('.', $sub['kode_sub_giat']);
						$kd_urusan90 = (int) $kd[0];
						$kd_bidang90 = (int) $kd[1];
						$kd_program90 = (int) $kd[2];
						$kd_kegiatan90 = ((int) $kd[3]).'.'.$kd[4];
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
						if(!empty($mapping[0]) && !empty($mapping[0]->kd_urusan)){
							$kd_urusan = $mapping[0]->kd_urusan;
							$kd_bidang = $mapping[0]->kd_bidang;
							$kd_prog = $mapping[0]->kd_prog;
							$kd_keg = $mapping[0]->kd_keg;
						}
					    foreach ($this->simda->custom_mapping as $c_map_k => $c_map_v) {
					        if(
					            $skpd['kode_skpd'] == $c_map_v['sipd']['kode_skpd']
					            && $sub['kode_sub_giat'] == $c_map_v['sipd']['kode_sub_keg']
					        ){
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
				        $id_prog = $kd_urusan.$this->simda->CekNull($kd_bidang);

						$realisasi_db = $wpdb->get_results($wpdb->prepare("
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
						if(!empty($realisasi_db)){
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
						$data_realisasi['realisasi'] = $this->get_realisasi_simda($opsi);
						$data_realisasi['realisasi_rp'] = 'Rp '.number_format($data_realisasi['realisasi'], 0, ",", ".");

						$opsi['rak'] = $rak_db_total;
						$data_realisasi['rak'] = $this->get_rak_simda($opsi);
						$data_realisasi['rak_rp'] = 'Rp '.number_format($data_realisasi['rak'], 0, ",", ".");
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

	function get_date_rfk_update($params = array()){
		global $wpdb;
		$tanggal = '-';
		$column='idinduk';
		
		if(isset($params['type']) && $params['type']=='sub_unit'){
			$column='id_skpd';
		}
		$last_update = $wpdb->get_results($wpdb->prepare("
							select 
								min(d.created_at) as last_update
							from data_sub_keg_bl k 
							left join data_rfk d 
								on d.id_skpd=k.id_sub_skpd and 
								d.kode_sbl=k.kode_sbl and 
								d.tahun_anggaran=k.tahun_anggaran and 
								d.bulan=".$params['bulan']." 
							where 
								k.tahun_anggaran=%d and 
								k.id_sub_skpd in (
									select 
										id_skpd 
									from data_unit 
									where 
										".$column."=".$params['id_skpd']." and 
										active=1 and 
										tahun_anggaran=".$params['tahun_anggaran']."
								) and 
								k.active=1
							", 
								$params['tahun_anggaran']
					), ARRAY_A);

		if(!empty($last_update[0]['last_update'])){
			$date = new DateTime($last_update[0]['last_update']);
			$tanggal = $date->format('d-m-Y');
		}

		return $tanggal;
	}

	function save_monev_renja(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil simpan data MONEV!';
		$ret['data'] = array();
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
				if($tahun_anggaran < $tahun_sekarang){
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
				for($i=1; $i<=12; $i++){
					if($i > $batas_bulan_input){
						$realisasi_bulan[$i] = 0;
					}
				}

				// sub kegiatan
				if($count_kode_sbl == 6){
					$type_indikator = 1;
				// kegiatan
				}else if($count_kode_sbl == 5){
					$kode_sbl = $kode_sbl_s[0].'.'.$kode_sbl_s[1].'.'.$kode_sbl_s[2].'.'.$kode_sbl_s[3];
					$type_indikator = 2;
				// program
				}else if($count_kode_sbl == 3){
					$kode_sbl = $kode_sbl_s[0].'.'.$kode_sbl_s[1].'.'.$kode_sbl_s[2];
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
					'id_indikator' => $id_indikator,
					'id_unik_indikator_renstra' => $_POST['id_indikator_renstra'],
					'tipe_indikator' => $type_indikator,
					'id_rumus_indikator' => $_POST['rumus_indikator'],
					'kode_sbl' => $kode_sbl,
					'realisasi_bulan_1' => $realisasi_bulan[1],
					'realisasi_bulan_2' => $realisasi_bulan[2],
					'realisasi_bulan_3' => $realisasi_bulan[3],
					'realisasi_bulan_4' => $realisasi_bulan[4],
					'realisasi_bulan_5' => $realisasi_bulan[5],
					'realisasi_bulan_6' => $realisasi_bulan[6],
					'realisasi_bulan_7' => $realisasi_bulan[7],
					'realisasi_bulan_8' => $realisasi_bulan[8],
					'realisasi_bulan_9' => $realisasi_bulan[9],
					'realisasi_bulan_10' =>$realisasi_bulan[10],
					'realisasi_bulan_11' =>$realisasi_bulan[11],
					'realisasi_bulan_12' =>$realisasi_bulan[12],
					'keterangan_bulan_1' => $keterangan_bulan[1],
					'keterangan_bulan_2' => $keterangan_bulan[2],
					'keterangan_bulan_3' => $keterangan_bulan[3],
					'keterangan_bulan_4' => $keterangan_bulan[4],
					'keterangan_bulan_5' => $keterangan_bulan[5],
					'keterangan_bulan_6' => $keterangan_bulan[6],
					'keterangan_bulan_7' => $keterangan_bulan[7],
					'keterangan_bulan_8' => $keterangan_bulan[8],
					'keterangan_bulan_9' => $keterangan_bulan[9],
					'keterangan_bulan_10' =>$keterangan_bulan[10],
					'keterangan_bulan_11' =>$keterangan_bulan[11],
					'keterangan_bulan_12' =>$keterangan_bulan[12],
					'user' => $current_user->display_name,
					'active' => 1,
					'update_at' => current_time('mysql'),
					'tahun_anggaran' => $tahun_anggaran
				);
				if (!empty($cek)) {
					$wpdb->update('data_realisasi_renja', $opsi, array(
						'id_indikator' => $id_indikator,
						'tipe_indikator' => $type_indikator,
						'kode_sbl' => $kode_sbl,
						'tahun_anggaran' => $tahun_anggaran
					));
				} else {
					$wpdb->insert('data_realisasi_renja', $opsi);
				}

				$crb_cara_input_realisasi = get_option('_crb_cara_input_realisasi', 1);
				// cek jika cara input realisasi secara manual dan tipe indikator adalah sub kegiatan
				if(
					$crb_cara_input_realisasi == 2
					&& $type_indikator == 1
					&& !empty($_POST['rak'])
					&& !empty($_POST['realisasi'])
				){
					// lakukan update data_rfk hanya pada bulan yang sudah dilalui
					for($bulan=1; $bulan<=$batas_bulan_input; $bulan++){
						$sql = $wpdb->prepare("
						    select 
						        id
						    from data_rfk
						    where tahun_anggaran=%d
						        and bulan=%d
						        and id_skpd=%d
						        and kode_sbl=%s
						", $tahun_anggaran, $bulan, $id_skpd, $kode_sbl);
						$cek = $wpdb->get_results($sql, ARRAY_A);
						$realisasi_anggaran = 0;
						for($b=1; $b<=$bulan; $b++){
							$realisasi_anggaran += $_POST['realisasi']['nilai_realisasi_bulan_'.$b];
						}
						$rak = 0;
						for($b=1; $b<=$bulan; $b++){
							$rak += $_POST['rak']['nilai_rak_bulan_'.$b];
						}
						$opsi = array(
							'bulan'	=> $bulan,
							'kode_sbl'	=> $kode_sbl,
							'rak' => $rak,
							'realisasi_anggaran' => $realisasi_anggaran,
							'user_edit'	=> $current_user->display_name,
							'id_skpd'	=> $id_skpd,
							'tahun_anggaran'	=> $tahun_anggaran,
							'created_at'	=>  current_time('mysql')
						);
						if(!empty($cek)){
							$wpdb->update('data_rfk', $opsi, array(
								'tahun_anggaran' => $tahun_anggaran,
								'bulan' => $bulan,
								'id_skpd' => $id_skpd,
								'kode_sbl' => $kode_sbl
							));
						}else{
							$wpdb->insert('data_rfk', $opsi);
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

	function get_monev(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil get data MONEV!';
		$ret['data'] = array();
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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

				// sub kegiatan
				if($count_kode_sbl == 6){
					$type_indikator = 1;
				// kegiatan
				}else if($count_kode_sbl == 5){
					$kode_sbl = $kode_sbl_s[0].'.'.$kode_sbl_s[1].'.'.$kode_sbl_s[2].'.'.$kode_sbl_s[3];
					$type_indikator = 2;
				// program
				}else if($count_kode_sbl == 3){
					$kode_sbl = $kode_sbl_s[0].'.'.$kode_sbl_s[1].'.'.$kode_sbl_s[2];
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
				if(!empty($realisasi_renja)){
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
					order by id DESC
				", $tahun_anggaran, $id_skpd, $kode_sbl.'%'), ARRAY_A);
				$ret['rfk_sql'] = $wpdb->last_query;
				$realisasi_anggaran = array();
				$rak = array();
				foreach ($rfk_all as $k => $v) {
					if(empty($realisasi_anggaran[$v['bulan']])){
						$realisasi_anggaran[$v['bulan']] = 0;
					}
					$realisasi_anggaran[$v['bulan']] += $v['realisasi_anggaran'];
					if(empty($rak[$v['bulan']])){
						$rak[$v['bulan']] = 0;
					}
					$rak[$v['bulan']] += $v['rak'];
				}

				$tahun_sekarang = date('Y');
				$batas_bulan_input = date('m');
				if($tahun_anggaran < $tahun_sekarang){
					$batas_bulan_input = 12;
				}
				$total_rak = 0;
				$total_realisasi = 0;
				$total_selisih = 0;
				$bulan = 12;
				$tbody = '';

				$crb_cara_input_realisasi = get_option('_crb_cara_input_realisasi', 1);
				for($i=1; $i<=$bulan; $i++){
					$realisasi_target_bulanan = 0;
					if(!empty($realisasi_renja)){
						$realisasi_target_bulanan = $realisasi_renja[0]['realisasi_bulan_'.$i];
					}
					if(empty($realisasi_anggaran[$i])){
						$realisasi_anggaran[$i] = 0;
					}
					$bulan_minus = $i-1;
					if(empty($realisasi_anggaran[$bulan_minus])){
						$realisasi_anggaran[$bulan_minus] = 0;
					}
					$realisasi_bulanan = $realisasi_anggaran[$i] - $realisasi_anggaran[$bulan_minus];
					if($realisasi_bulanan < 0){
						$realisasi_bulanan = 0;
					}
					if(empty($rak[$i])){
						$rak[$i] = 0;
					}
					$bulan_minus = $i-1;
					if(empty($rak[$bulan_minus])){
						$rak[$bulan_minus] = 0;
					}
					$rak_bulanan = $rak[$i] - $rak[$bulan_minus];
					if($rak_bulanan < 0){
						$rak_bulanan = 0;
					}
					$selisih = $rak_bulanan-$realisasi_bulanan;

					$rak_bulanan_format = number_format($rak_bulanan,0,",",".");
					$realisasi_bulanan_format = number_format($realisasi_bulanan,0,",",".");
					$selisih_format = number_format($selisih,0,",",".");

					$editable = 'contenteditable="true"';
					$editable_realisasi = 'contenteditable="true"';

					/* 
						- jika bulan belum dilalui 
						- atau user login adalah admin 
						- atau user login adalah mitra bappeda
						- atau user login adalah tapd bappeda
					*/
					if(
						$batas_bulan_input < $i
						|| current_user_can('administrator')
						|| in_array("mitra_bappeda", $current_user->roles)
						|| in_array("tapd_pp", $current_user->roles)
					){
						$editable = '';
						$editable_realisasi = '';
					// jika input realisasi secara manual dan type indikator adalah sub kegiatan
					}else if(
						$crb_cara_input_realisasi == 2 
						&& $type_indikator == 1
					){
						$rak_bulanan_format = $rak_bulanan;
						$realisasi_bulanan_format = $realisasi_bulanan;
					}

					// jika cara input realisasi otomatis dari SIMDA atau tipe indikator bukan sub kegiatan
					if(
						$crb_cara_input_realisasi == 1
						|| $type_indikator != 1
					){
						$editable_realisasi = '';
					}
					$tbody .= '
						<tr>
							<td>'.$this->get_bulan($i).'</td>
							<td class="text_kanan nilai_rak" '.$editable_realisasi.' onkeypress="onlyNumber(event);" onkeyup="setTotalRealisasi();" id="nilai_rak_bulan_'.$i.'">'.$rak_bulanan_format.'</td>
							<td class="text_kanan nilai_realisasi" '.$editable_realisasi.' onkeypress="onlyNumber(event);" onkeyup="setTotalRealisasi();" id="nilai_realisasi_bulan_'.$i.'">'.$realisasi_bulanan_format.'</td>
							<td class="text_kanan nilai_selisih">'.$selisih_format.'</td>
							<td class="text_tengah target_realisasi" id="target_realisasi_bulan_'.$i.'" '.$editable.' onkeypress="onlyNumber(event);" onkeyup="setTotalMonev(this);">'.$realisasi_target_bulanan.'</td>
							<td class="text_kiri" id="keterangan_bulan_'.$i.'" '.$editable.'>'.$realisasi_renja[0]['keterangan_bulan_'.$i].'</td>
						</tr>
					';
					$total_rak += $rak_bulanan;
					$total_realisasi += $realisasi_bulanan;
					$total_selisih += $selisih;
				}
				$tbody .= '
					<tr>
						<td class="text_tengah text_blok">Total</td>
						<td class="text_kanan text_blok" id="total_nilai_rak">'.number_format($total_rak,0,",",".").'</td>
						<td class="text_kanan text_blok" id="total_nilai_realisasi">'.number_format($total_realisasi,0,",",".").'</td>
						<td class="text_kanan text_blok" id="total_nilai_selisih">'.number_format($total_selisih,0,",",".").'</td>
						<td class="text_tengah text_blok" id="total_target_realisasi">0</td>
						<td class="text_tengah text_blok"></td>
					</tr>
				';
				$ret['table'] = $tbody;
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

	public function decode_key($value){
		$key = base64_decode($value);
		$key_db = md5(get_option( '_crb_api_key_extension' ));
		$key = explode($key_db, $key);
		$get = array();
		if(!empty($key[2])){
			$all_get = explode('&', $key[2]);
			foreach ($all_get as $k => $v) {
				$current_get = explode('=', $v);
				$get[$current_get[0]] = $current_get[1];
			}
		}
		return $get;
	}

	public function get_url_page(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil get data link!';
		$ret['data'] = array();
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['type'])){
					$type_page = $_POST['type'];
					if(!empty($_POST['tahun_anggaran'])){
						$tahun_anggaran = $_POST['tahun_anggaran'];
					}else{
						$tahun_anggaran = 2021;
					}
					if($type_page == 'rfk_pemda'){
						$title = 'Realisasi Fisik dan Keuangan Pemerintah Daerah | '.$tahun_anggaran;
						$custom_post = get_page_by_title($title);
						$custom_post->custom_url = array(array('key' => 'public', 'value' => 1));
						$url = $this->get_link_post($custom_post);
						$ret['url'] = $url;
					}
				}else{
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

	function simpan_catatan_rfk_unit(){
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil simpan data!'
		);
		if (!empty($_POST)) {
			if(isset($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )){
				$cek = $wpdb->get_var("SELECT id FROM data_catatan_rfk_unit WHERE id_skpd=".$_POST['data']['id_skpd']." AND bulan=".$_POST['bulan']." AND tahun_anggaran=".$_POST['tahun_anggaran']);
				$data = array(
					'bulan' => $_POST['bulan'],
					'catatan_ka_adbang' => !empty($_POST['data']['catatan_ka_adbang']) ? $_POST['data']['catatan_ka_adbang'] : NULL,
					'id_skpd' => $_POST['data']['id_skpd'],
					'tahun_anggaran' => $_POST['tahun_anggaran']
				);
				
				if(!empty($cek)){
					$data['updated_by'] = $_POST['user'];
					$data['updated_at'] = current_time('mysql');
					$wpdb->update('data_catatan_rfk_unit', $data, array('id_skpd'=>$_POST['data']['id_skpd'], 'bulan'=>$_POST['bulan'], 'tahun_anggaran'=>$_POST['tahun_anggaran']));
				}else{
					$data['created_by'] = $_POST['user'];
					$data['created_at'] = current_time('mysql');
					$wpdb->insert('data_catatan_rfk_unit', $data);
				}
			}else{
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

	function save_monev_renja_triwulan(){
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil simpan data!'
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$current_user = wp_get_current_user();
				$cek = $wpdb->get_var("
					SELECT 
						id 
					FROM data_monev_renja_triwulan 
					WHERE id_skpd=".$_POST['id_skpd']." 
						AND triwulan=".$_POST['triwulan']." 
						AND tahun_anggaran=".$_POST['tahun_anggaran']
				);
				$data = array(
					'triwulan' => $_POST['triwulan'],
					'id_skpd' => $_POST['id_skpd'],
					'tahun_anggaran' => $_POST['tahun_anggaran']
				);
				if(
					current_user_can('administrator')
					|| in_array("mitra_bappeda", $current_user->roles)
                                        || in_array("tapd_pp", $current_user->roles)
				){
					$data['catatan_verifikator'] = $_POST['catatan_verifikator'];
					$data['user_verifikator'] = $current_user->display_name;
					$data['update_verifikator_at'] = current_time('mysql');
					$ret['update_verifikator_at'] = $data['update_verifikator_at'];
				}else{
					$file_name = ((int) $_POST['triwulan']).'.'.((int) $_POST['tahun_anggaran']).'.'.((int) $_POST['id_skpd']).'.MONEV_RENJA.xlsx';
					$target_folder = plugin_dir_path(dirname(__FILE__)).'public/media/';
					$target_file = $target_folder.$file_name;
					if(!empty($_POST['file_remove']) && $_POST['file_remove']==1){
						$ret['message'] = 'Berhasil hapus file '.$file_name.'!';
						$file_name = '';
						unlink($target_file);
					}else{
						// max 10MB
						if ($_FILES["file"]["size"] > 1000000) {
							$ret['status'] = 'error';
							$ret['message'] = 'Max file upload sebesar 10MB!';
						}
						// cek type file
						$imageFileType = strtolower(pathinfo($target_folder.basename($_FILES["file"]["name"]),PATHINFO_EXTENSION));
						if($imageFileType != "xlsx") {
							$ret['status'] = 'error';
							$ret['message'] = 'File yang diupload harus berextensi .xlsx!';
						}
						if($ret['status'] == 'success'){
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
				if($ret['status'] == 'success'){
					if(!empty($cek)){				
						$wpdb->update('data_monev_renja_triwulan', $data, array(
							'id_skpd'=>$_POST['id_skpd'], 
							'triwulan'=>$_POST['triwulan'], 
							'tahun_anggaran'=>$_POST['tahun_anggaran']
						));
					}else{
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

	function get_data_rpjm(){
		global $wpdb;


		if(empty($_POST)){		
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				$id_unik = $_POST['kode_sasaran_rpjm'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_unit = $_POST['id_unit'];

				$data_rpjmd = $wpdb->get_results($wpdb->prepare("
									SELECT 
										* 
									FROM data_rpjmd_sasaran 
									where 
										active=1 and 
										id_unik=%s and 
										tahun_anggaran=%d", 
									$id_unik,
									$tahun_anggaran
								), ARRAY_A);

				$return['status'] = 0;
				$return['body_rpjm'] = '';
				if(!empty($data_rpjmd)){
					
					$data_all = array(
						'data' => array()
					);
					foreach ($data_rpjmd as $k => $v) {
						
						if(empty($data_all['data'][$v['id_visi']])){
							$data_all['data'][$v['id_visi']] = array(
								'nama' => $v['visi_teks'],
								'data' => array()
							);
						}

						if(empty($data_all['data'][$v['id_visi']]['data'][$v['id_misi']])){
							$data_all['data'][$v['id_visi']]['data'][$v['id_misi']] = array(
								'nama' => $v['misi_teks'],
								'data' => array()
							);
						}

						if(empty($data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']])){
							$data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']] = array(
								'nama' => $v['tujuan_teks'],
								'indikator' => array(),
								'data' => array(),
							);

							$indikators = $wpdb->get_results($wpdb->prepare("
								select 
									* 
								from data_rpjmd_tujuan 
								where 
									active=1 and 
									id_unik=%s and
									tahun_anggaran=%d
								",
								$v['kode_tujuan'],
								$tahun_anggaran
							), ARRAY_A);

							if(!empty($indikators)){
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

						if(empty($data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']]['data'][$v['kode_sasaran']])){
							$data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']]['data'][$v['kode_sasaran']] = array(
								'nama' => $v['sasaran_teks'],
								'indikator' => array(),
								'data' => array(),
							);

							if(!empty($v['id_unik_indikator'])){
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

						$program = $wpdb->get_results($wpdb->prepare("
							select 
								* 
							from data_rpjmd_program 
							where 
								active=1 and
								kode_sasaran=%s and
								id_unit=%d and
								tahun_anggaran=%d",

								$v['id_unik'],
								$id_unit,
								$tahun_anggaran
							), ARRAY_A);
						// die($wpdb->last_query);
				
						if(!empty($program)){
							foreach ($program as $kp => $vp) {
								
								if(empty($data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']]['data'][$v['kode_sasaran']]['data'][$vp['id_program']]))
								{
									$data_all['data'][$v['id_visi']]['data'][$v['id_misi']]['data'][$v['kode_tujuan']]['data'][$v['kode_sasaran']]['data'][$vp['id_program']] = array(
										'nama' => $vp['nama_program'],
										'indikator' => array(),
									);

									if(!empty($vp['id_unik_indikator'])){
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

					// echo '<pre>';print_r($data_all);echo '</pre>';die();
					$body = '';
					foreach ($data_all['data'] as $v => $visi) {
						$body .= '
						<tr class="misi" data-kode="">
				            <td class="kiri kanan bawah text_blok">'.$visi['nama'].'</td>
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
					            <td class="text_kiri kanan bawah text_blok">'.$misi['nama'].'</td>
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
						            <td class="text_kiri kanan bawah text_blok">'.$tujuan_teks[0].'</td>
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
							            <td class="kanan bawah text_blok">'.$sasaran_teks[0].'</td>
							            <td class="kanan bawah text_blok"></td>
							            <td class="text_kiri kanan bawah text_blok"></td>
							        </tr>';

							        foreach ($sasaran['data'] as $p => $program) {
							        	$program_teks = explode("||", $program['nama']);
							        	$indikator = array(
											'indikator_teks' => array()
										);
										foreach ($program['indikator'] as $i => $p_indikator) {
											$indikator['indikator_teks'][]=$p_indikator['indikator'];
										}
							        	
							        	$body .= '
										<tr class="misi" data-kode="">
								            <td class="kiri kanan bawah text_blok"></td>
								            <td class="text_kiri kanan bawah text_blok"></td>
								            <td class="text_kiri kanan bawah text_blok"></td>
								            <td class="kanan bawah text_blok"></td>
								            <td class="kanan bawah text_blok">'.$program_teks[0].'</td>
								            <td class="text_kiri kanan bawah text_blok">'.implode(' <br><br> ', $indikator['indikator_teks']).'</td>
								        </tr>';						        
							        }
						        }
					        }
				        }
					}
					$return['status'] = 1;
					$return['body_rpjm'] = $body;
				}
			}else{
				$return['status'] = 'error';
				$return['message'] = 'APIKEY tidak sesuai!';
			}
		}else{
			$return['status'] = 'error';
			$return['message'] = 'Format Salah!';
		}
		echo json_encode($return);exit();
	}

	public function reset_rfk_pemda(){
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil reset data sesuai bulan sebelumnya!',
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$bulan = $_POST['bulan'];
				$bulan_n = $_POST['bulan']-1;
				$tahun_anggaran = $_POST['tahun_anggaran'];

				$units = $wpdb->get_results($wpdb->prepare("
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
					$data_n = $wpdb->get_results($wpdb->prepare("
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
							"-".$unit['id_skpd'],
							$tahun_anggaran
					), ARRAY_A);
					// die($wpdb->last_query);

					if(!empty($data_n)){
						foreach ($data_n as $k => $v) {
							$cek = $wpdb->get_results($wpdb->prepare("
								SELECT id FROM data_catatan_rfk_unit WHERE bulan=%d AND id_skpd=%d AND tahun_anggaran=%d",$bulan,$v['id_skpd'],$tahun_anggaran),ARRAY_A);
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
			}else{
				$ret = array(
					'status' => 'error',
					'message' => 'Apikey salah!',
				);		
			}
		}else{
			$ret = array(
				'status' => 'error',
				'message' => 'Format salah!',
			);			
		}
		die(json_encode($ret));
	}

	public function get_monev_renstra()
	{
		global $wpdb;
		if(!empty($_POST)){
			if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )){

				$bulan = 12;
				$indikator = '';
				$body_renstra = '';
				$rumus_indikator = 1;
				$hitung_indikator = 1;
				$target_indikator = 0;
				$capaian_indikator = 0;
				$sum_realisasi_anggaran = 0;
				$body_realisasi_renstra = '';
				switch ($_POST['type_indikator']) {
					case '4':
						$indikator = $wpdb->get_row($wpdb->prepare("select * from data_renstra_kegiatan where active=1 and id=%d and id_unit=%d and tahun_anggaran=%d",
						$_POST['id'], $_POST['id_skpd'], $_POST['tahun_anggaran']), ARRAY_A);
						break;

					case '3':
						$indikator = $wpdb->get_row($wpdb->prepare("select * from data_renstra_program where active=1 and id=%d and id_unit=%d and tahun_anggaran=%d",
						$_POST['id'], $_POST['id_skpd'], $_POST['tahun_anggaran']), ARRAY_A);
						break;
					
					default:
						$return['status'] = 'error';
						$return['message'] = 'REQUEST TIDAK SPESIFIK';
						break;
				}

				if(!empty($indikator)){
					$body_renstra .='
							<tr>
								<td colspan="2">'.$indikator['indikator'].'</td>
								<td>'.$indikator['target_1'].'</td>
								<td>'.$indikator['target_2'].'</td>
								<td>'.$indikator['target_3'].'</td>
								<td>'.$indikator['target_4'].'</td>
								<td>'.$indikator['target_5'].'</td>
								<td>'.$indikator['satuan'].'</td>
								<td colspan="2">Rp. '.number_format($indikator['pagu_'.$_POST['urut']], 2, ',', '.').'</td>
							</tr>';
					$target_indikator = $indikator['target_'.$_POST['urut']];					
					
					$realisasi_renstra = $wpdb->get_row($wpdb->prepare("select * from data_realisasi_renstra where id_indikator=%d and type_indikator=%d and tahun_anggaran=%d", $_POST['id'], $_POST['type_indikator'], $_POST['tahun_anggaran']), ARRAY_A);

					$hitung_indikator = !is_null($realisasi_renstra['hitung_indikator']) ? $realisasi_renstra['hitung_indikator'] : $hitung_indikator;
					$rumus_indikator = !is_null($realisasi_renstra['id_rumus_indikator']) ? $realisasi_renstra['id_rumus_indikator'] : $rumus_indikator;

					for($i=1; $i <= $bulan; $i++){
						$edited='false';
						if($i <= $_POST['bulan']){
							$edited='true';
						}
						$realisasi_anggaran_bulanan = !empty($realisasi_renstra['realisasi_anggaran_bulan_'.$i]) ? $realisasi_renstra['realisasi_anggaran_bulan_'.$i] : 0;
						$realisasi_target_bulanan = !empty($realisasi_renstra['realisasi_bulan_'.$i]) ? $realisasi_renstra['realisasi_bulan_'.$i] : 0;
						$capaian_target_bulanan = !empty($realisasi_renstra['capaian_bulan_'.$i]) ? $realisasi_renstra['capaian_bulan_'.$i] : 0;
						$body_realisasi_renstra .='
							<tr>
								<td>'.$this->get_bulan($i).'</td>
								<td class="realisasi_anggaran" contenteditable="'.$edited.'" id="realisasi_anggaran_bulan_'.$i.'" style="text-align:right" onkeypress=onlyNumber(event) onkeyup=setTotalMonev()>'.number_format($realisasi_anggaran_bulanan,2,'.',',').'</td>
								<td class="realisasi_target_bulanan" contenteditable="'.$edited.'" id="realisasi_target_bulan_'.$i.'" style="text-align:center" onkeyup=setCapaianMonev(this)>'.$realisasi_target_bulanan.'</td>
								<td class="capaian_target_bulanan" id="capaian_target_bulan_'.$i.'" style="text-align:center">'.$capaian_target_bulanan.'</td>
								<td contenteditable="'.$edited.'" id="keterangan_bulan_'.$i.'" style="text-align:left">'.$realisasi_renstra['keterangan_bulan_'.$i].'</td>
							</tr>';
						$sum_realisasi_anggaran += $realisasi_anggaran_bulanan;
					}

					$return['hitung_indikator'] = $hitung_indikator;
					$return['rumus_indikator'] = $rumus_indikator;
					$return['target_indikator'] = $target_indikator;
					$return['sum_realisasi_anggaran'] = number_format($sum_realisasi_anggaran,2,'.',',');
					$return['body_renstra'] = $body_renstra;
					$return['body_realisasi_renstra'] = $body_realisasi_renstra;
				}
			}else{
				$return['status'] = 'error';
				$return['message'] = 'APIKEY tidak sesuai!';
			}
		}else{
			$return['status'] = 'error';
			$return['message'] = 'Format Salah!';
		}
		echo json_encode($return);exit();
	}

	public function save_monev_renstra()
	{
		global $wpdb;
		$current_user = wp_get_current_user();
		$return = array(
			'status' => 'success',
			'message' => 'Berhasil simpan data!',
		);

		if(!empty($_POST)){
			if(!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )){
				
				$data = array(
					'id_indikator' => $_POST['id_indikator'],
					'id_rumus_indikator' => $_POST['id_rumus_indikator'],
					'type_indikator' => $_POST['type_indikator'],
					'type_indikator' => $_POST['type_indikator'],
					'hitung_indikator' => $_POST['hitung_indikator'],
					'realisasi_bulan_1' => $_POST['realisasi_target']['realisasi_target_bulan_1'],
					'realisasi_bulan_2' => $_POST['realisasi_target']['realisasi_target_bulan_2'],
					'realisasi_bulan_3' => $_POST['realisasi_target']['realisasi_target_bulan_3'],
					'realisasi_bulan_4' => $_POST['realisasi_target']['realisasi_target_bulan_4'],
					'realisasi_bulan_5' => $_POST['realisasi_target']['realisasi_target_bulan_5'],
					'realisasi_bulan_6' => $_POST['realisasi_target']['realisasi_target_bulan_6'],
					'realisasi_bulan_7' => $_POST['realisasi_target']['realisasi_target_bulan_7'],
					'realisasi_bulan_8' => $_POST['realisasi_target']['realisasi_target_bulan_8'],
					'realisasi_bulan_9' => $_POST['realisasi_target']['realisasi_target_bulan_9'],
					'realisasi_bulan_10' => $_POST['realisasi_target']['realisasi_target_bulan_10'],
					'realisasi_bulan_11' => $_POST['realisasi_target']['realisasi_target_bulan_11'],
					'realisasi_bulan_12' => $_POST['realisasi_target']['realisasi_target_bulan_12'],
					'capaian_bulan_1' => $_POST['capaian_indikator']['capaian_bulan_1'],
					'capaian_bulan_2' => $_POST['capaian_indikator']['capaian_bulan_2'],
					'capaian_bulan_3' => $_POST['capaian_indikator']['capaian_bulan_3'],
					'capaian_bulan_4' => $_POST['capaian_indikator']['capaian_bulan_4'],
					'capaian_bulan_5' => $_POST['capaian_indikator']['capaian_bulan_5'],
					'capaian_bulan_6' => $_POST['capaian_indikator']['capaian_bulan_6'],
					'capaian_bulan_7' => $_POST['capaian_indikator']['capaian_bulan_7'],
					'capaian_bulan_8' => $_POST['capaian_indikator']['capaian_bulan_8'],
					'capaian_bulan_9' => $_POST['capaian_indikator']['capaian_bulan_9'],
					'capaian_bulan_10' => $_POST['capaian_indikator']['capaian_bulan_10'],
					'capaian_bulan_11' => $_POST['capaian_indikator']['capaian_bulan_11'],
					'capaian_bulan_12' => $_POST['capaian_indikator']['capaian_bulan_12'],
					'realisasi_anggaran_bulan_1' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_1'],
					'realisasi_anggaran_bulan_2' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_2'],
					'realisasi_anggaran_bulan_3' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_3'],
					'realisasi_anggaran_bulan_4' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_4'],
					'realisasi_anggaran_bulan_5' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_5'],
					'realisasi_anggaran_bulan_6' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_6'],
					'realisasi_anggaran_bulan_7' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_7'],
					'realisasi_anggaran_bulan_8' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_8'],
					'realisasi_anggaran_bulan_9' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_9'],
					'realisasi_anggaran_bulan_10' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_10'],
					'realisasi_anggaran_bulan_11' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_11'],
					'realisasi_anggaran_bulan_12' => $_POST['realisasi_anggaran']['realisasi_anggaran_bulan_12'],
					'keterangan_bulan_1' => $_POST['keterangan']['keterangan_bulan_1'],
					'keterangan_bulan_2' => $_POST['keterangan']['keterangan_bulan_2'],
					'keterangan_bulan_3' => $_POST['keterangan']['keterangan_bulan_3'],
					'keterangan_bulan_4' => $_POST['keterangan']['keterangan_bulan_4'],
					'keterangan_bulan_5' => $_POST['keterangan']['keterangan_bulan_5'],
					'keterangan_bulan_6' => $_POST['keterangan']['keterangan_bulan_6'],
					'keterangan_bulan_7' => $_POST['keterangan']['keterangan_bulan_7'],
					'keterangan_bulan_8' => $_POST['keterangan']['keterangan_bulan_8'],
					'keterangan_bulan_9' => $_POST['keterangan']['keterangan_bulan_9'],
					'keterangan_bulan_10' => $_POST['keterangan']['keterangan_bulan_10'],
					'keterangan_bulan_11' => $_POST['keterangan']['keterangan_bulan_11'],
					'keterangan_bulan_12' => $_POST['keterangan']['keterangan_bulan_12'],
					'tahun_anggaran' => $_POST['tahun_anggaran']
				);

				$cek_data = $wpdb->get_row($wpdb->prepare("select id from data_realisasi_renstra where id_indikator=%d and type_indikator=%d and tahun_anggaran=%d", $_POST['id_indikator'], $_POST['type_indikator'], $_POST['tahun_anggaran']), ARRAY_A);

				if(empty($cek_data['id'])){
					$data['created_by'] = $current_user->display_name;
					$data['created_at'] = current_time('mysql');
					$wpdb->insert('data_realisasi_renstra', $data);
				}else{
					$data['updated_by'] = $current_user->display_name;
					$data['updated_at'] = current_time('mysql');
					$wpdb->update('data_realisasi_renstra', $data, array('id_indikator' => $_POST['id_indikator'], 'type_indikator' => $_POST['type_indikator'], 'tahun_anggaran' => $_POST['tahun_anggaran']));
				}
				
			}else{
				$return['status'] = 'error';
				$return['message'] = 'APIKEY tidak sesuai!';
			}
		}else{
			$return['status'] = 'error';
			$return['message'] = 'Format Salah!';
		}
		echo json_encode($return);exit();
	}

	public function valid_number($no){
		$no = str_replace(array(','), array('.'), $no);
		return $no;
	}

	public function get_indikator_renstra_renja($options){
		$renstra = $options['renstra'];
		$renja = $options['renja'];
		$type = $options['type'];
		$ret['renstra_sasaran'] = array();
		$ret['renstra_tujuan'] = array();
		$ret['renstra_indikator'] = array();
		$ret['total_pagu_renstra'] = array();
		$ret['total_target_renstra'] = array();
		$ret['total_pagu_renstra_renja'] = array();
		$ret['total_target_renstra_text'] = array();
		foreach ($renja['indikator'] as $k_sub => $v_sub) {
			$ret['total_target_renstra_text'][$k_sub] = '<span class="total_target_renstra" data-id="'.$k_sub.'">0</span>';
			$ret['total_pagu_renstra_renja'][$k_sub] = '<span class="monev_total_renstra" data-id="'.$k_sub.'">0</span>';
			if($options['type'] == 'kegiatan'){
				$ret['satuan_renstra'][$k_sub] = '<span class="satuan_renstra" data-id="'.$k_sub.'">'.$v_sub['satuanoutput'].'</span>';
			}else if($options['type'] == 'program'){
				$ret['satuan_renstra'][$k_sub] = '<span class="satuan_renstra" data-id="'.$k_sub.'">'.$v_sub['satuancapaian'].'</span>';
			}
		}

		foreach ($renstra as $k => $v) {
			$sasaran_teks = explode('||', $v['sasaran_teks']);
			$tujuan_teks = explode('||', $v['tujuan_teks']);
			if($options['type'] == 'kegiatan'){
				$ret['renstra_sasaran'][0] = '<span class="renstra_kegiatan">'.$sasaran_teks[0].'</span>';
				$ret['renstra_tujuan'][0] = '<span class="renstra_kegiatan">'.$tujuan_teks[0].'</span>';
			}else if($options['type'] == 'program'){
				$ret['renstra_sasaran'][0] = $sasaran_teks[0];
				$ret['renstra_tujuan'][0] = $tujuan_teks[0];
			}
			$target_indikator_renstra_1 = explode(' ', $v['target_1']);
			$target_indikator_renstra_2 = explode(' ', $v['target_2']);
			$target_indikator_renstra_3 = explode(' ', $v['target_3']);
			$target_indikator_renstra_4 = explode(' ', $v['target_4']);
			$target_indikator_renstra_5 = explode(' ', $v['target_5']);
			$pagu_1 = explode(' ', $v['pagu_1']);
			$pagu_2 = explode(' ', $v['pagu_2']);
			$pagu_3 = explode(' ', $v['pagu_3']);
			$pagu_4 = explode(' ', $v['pagu_4']);
			$pagu_5 = explode(' ', $v['pagu_5']);
			$ret['total_pagu_renstra'][$k] = $pagu_1[0]+$pagu_2[0]+$pagu_3[0]+$pagu_4[0]+$pagu_5[0];

			// debug RENSTRA
			$ret['renstra_indikator'][] = '<li data-id='.$v['id_unik_indikator'].'><span class="indikator_renstra_text_hide">'.$v['indikator'].'</span> <span class="target_indikator_renstra_text_hide">'.$target_indikator_renstra_1[0].' | '.$target_indikator_renstra_2[0].' | '.$target_indikator_renstra_3[0].' | '.$target_indikator_renstra_4[0].' | '.$target_indikator_renstra_5[0].'</span> <span class="satuan_indikator_renstra_text_hide">'.$v['satuan'].'</span> (Rp <span class="pagu_indikator_renstra_text_hide">'.number_format($v['pagu_'.$options['tahun_renstra']],0,",",".").'</span> / Rp <span class="total_pagu_indikator_renstra_text_hide">'.number_format($ret['total_pagu_renstra'][$k],0,",",".").'</span>)</li>';

			foreach ($renja['realisasi_indikator'] as $k_sub => $v_sub) {
				$ret['total_target_renstra'][$k_sub] = 0;
				if(
					!empty($v_sub['id_unik_indikator_renstra']) 
					&& $v_sub['id_unik_indikator_renstra'] == $v['id_unik_indikator']
				){
					$rumus_indikator = $v_sub['id_rumus_indikator'];
					if(empty($v['satuan'])){
						if($options['type'] == 'kegiatan'){
							$v['satuan'] = $v_sub['satuanoutput'];
						}else if($options['type'] == 'program'){
							$v['satuan'] = $v_sub['satuancapaian'];
						}
					}
					$cek_string = false;
					$target_renstra_1 = $this->valid_number($target_indikator_renstra_1[0]);
					if(!is_numeric($target_renstra_1)){
						$cek_string = true;
					}else{
						$ret['total_target_renstra'][$k_sub] += $target_renstra_1;
					}
					$target_renstra_2 = $this->valid_number($target_indikator_renstra_2[0]);
					if(!is_numeric($target_renstra_2)){
						$cek_string = true;
					}else{
						$ret['total_target_renstra'][$k_sub] += $target_renstra_2;
					}
					$target_renstra_3 = $this->valid_number($target_indikator_renstra_3[0]);
					if(!is_numeric($target_renstra_3)){
						$cek_string = true;
					}else{
						$ret['total_target_renstra'][$k_sub] += $target_renstra_3;
					}
					$target_renstra_4 = $this->valid_number($target_indikator_renstra_4[0]);
					if(!is_numeric($target_renstra_4)){
						$cek_string = true;
					}else{
						$ret['total_target_renstra'][$k_sub] += $target_renstra_4;
					}
					$target_renstra_5 = $this->valid_number($target_indikator_renstra_5[0]);
					if(!is_numeric($target_renstra_5)){
						$cek_string = true;
					}else{
						$ret['total_target_renstra'][$k_sub] += $target_renstra_5;
					}

					if($rumus_indikator == 1){
						if($cek_string){
							$ret['total_target_renstra'][$k_sub] = $target_indikator_renstra_5[0];
						}else{
							$ret['total_target_renstra'][$k_sub] = number_format($ret['total_target_renstra'][$k_sub],2,",",".");
						}
					}else if($rumus_indikator == 2){
						if($cek_string){
							$ret['total_target_renstra'][$k_sub] = $target_indikator_renstra_5[0];
						}else{
							$ret['total_target_renstra'][$k_sub] = number_format($this->valid_number($target_indikator_renstra_5[0]),2,",",".");
						}
					}else if($rumus_indikator == 3){
						if($cek_string){
							$ret['total_target_renstra'][$k_sub] = $target_indikator_renstra_5[0];
						}else{
							$ret['total_target_renstra'][$k_sub] = number_format($this->valid_number($target_indikator_renstra_5[0]),2,",",".");
						}
					}
					$pagu_1 = explode(' ', $v['pagu_1']);
					$pagu_2 = explode(' ', $v['pagu_2']);
					$pagu_3 = explode(' ', $v['pagu_3']);
					$pagu_4 = explode(' ', $v['pagu_4']);
					$pagu_5 = explode(' ', $v['pagu_5']);
					$ret['total_pagu_renstra_renja'][$k_sub] = $pagu_1[0]+$pagu_2[0]+$pagu_3[0]+$pagu_4[0]+$pagu_5[0];
					$ret['total_target_renstra_text'][$k_sub] = '<span class="total_target_renstra" data-id="'.$k_sub.'">'.$ret['total_target_renstra'][$k_sub].'</span>';
					$ret['satuan_renstra'][$k_sub] = '<span class="satuan_renstra" data-id="'.$k_sub.'">'.$v['satuan'].'</span>';
					$ret['total_pagu_renstra_renja'][$k_sub] = '<span class="monev_total_renstra" data-id="'.$k_sub.'">'.number_format($ret['total_pagu_renstra_renja'][$k_sub],0,",",".").'</span>';
				}
			}
		}
		$ret['total_target_renstra_text'] = implode('<br>', $ret['total_target_renstra_text']);
		if(empty($ret['satuan_renstra'])){
			$ret['satuan_renstra'] = str_replace('data-id="', 'class="mod_total_renstra" data-id="', $options['default_satuan_renstra']);
		}else{
			$ret['satuan_renstra'] = implode('<br>', $ret['satuan_renstra']);
		}
		$ret['total_pagu_renstra_renja'] = implode('<br>', $ret['total_pagu_renstra_renja']);
		$ret['renstra_sasaran'] = implode('<br>', $ret['renstra_sasaran']).' <ul class="indikator_renstra">'.implode('', $ret['renstra_indikator']).'</ul>';
		$ret['renstra_tujuan'] = implode('<br>', $ret['renstra_tujuan']);
		return $ret;
	}

	public function get_ref_unit($options){
		global $wpdb;
		$tahun_anggaran = $options['tahun_anggaran'];
		$sql = $wpdb->prepare("
			SELECT 
				*
			FROM ref_sub_unit r"
		);
		$new_unit = array();
		$unit_simda = $this->simda->CurlSimda(array('query' => $sql));
		foreach ($unit_simda as $k => $v) {
			$new_unit[$v->kd_urusan.'.'.$v->kd_bidang.'.'.$v->kd_unit.'.'.$v->kd_sub] = $v;
		}
		return $new_unit;
	}

	public function get_rka_simda(){
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil get RKA SIMDA!',
			'data'	=> array(),
			'data_blm_singkron'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$unit_simda = $this->get_ref_unit(array('tahun_anggaran' => $tahun_anggaran));
				foreach ($_POST['id_skpd'] as $id_skpd) {
					$kd_unit_simda_asli = get_option('_crb_unit_'.$id_skpd);
					$kd_unit_simda = explode('.', $kd_unit_simda_asli);
					if(
						!empty($kd_unit_simda) 
						&& !empty($kd_unit_simda[3])
					){
						if(!empty($unit_simda[$kd_unit_simda_asli])){
							unset($unit_simda[$kd_unit_simda_asli]);
						}
						$_kd_urusan = $kd_unit_simda[0];
						$_kd_bidang = $kd_unit_simda[1];
						$kd_unit = $kd_unit_simda[2];
						$kd_sub_unit = $kd_unit_simda[3];
						$sql = $wpdb->prepare("
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
						if(!empty($pagu[0])){
							$ret['data'][$id_skpd] = number_format($pagu[0]->total, 0, ",", ".");
						}else{
							$ret['data'][$id_skpd] = 0;
						}
					}else{
						$ret['data'][$id_skpd] = 0;
					}
				}
				foreach ($unit_simda as $k => $v) {
					$ret['data_blm_singkron'][$k] = $v;
				}
			}else{
				$ret = array(
					'status' => 'error',
					'message' => 'Apikey salah!',
				);		
			}
		}else{
			$ret = array(
				'status' => 'error',
				'message' => 'Format salah!',
			);			
		}
		die(json_encode($ret));
	}

	function get_dpa_simda(){
		global $wpdb;
		$ret = array(
			'status' => 'success',
			'message' => 'Berhasil get DPA SIMDA!',
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$unit_simda = $this->get_ref_unit(array('tahun_anggaran' => $tahun_anggaran));
				foreach ($_POST['id_skpd'] as $id_skpd) {
					$kd_unit_simda_asli = get_option('_crb_unit_'.$id_skpd);
					$kd_unit_simda = explode('.', $kd_unit_simda_asli);
					if(
						!empty($kd_unit_simda) 
						&& !empty($kd_unit_simda[3])
					){
						if(!empty($unit_simda[$kd_unit_simda_asli])){
							unset($unit_simda[$kd_unit_simda_asli]);
						}
						$_kd_urusan = $kd_unit_simda[0];
						$_kd_bidang = $kd_unit_simda[1];
						$kd_unit = $kd_unit_simda[2];
						$kd_sub_unit = $kd_unit_simda[3];
						$sql = $wpdb->prepare("
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
						if(!empty($pagu[0])){
							$ret['data'][$id_skpd] = number_format($pagu[0]->total, 0, ",", ".");
						}else{
							$ret['data'][$id_skpd] = 0;
						}
					}else{
						$ret['data'][$id_skpd] = 0;
					}
				}
				foreach ($unit_simda as $k => $v) {
					$ret['data_blm_singkron'][$k] = $v;
				}
			}else{
				$ret = array(
					'status' => 'error',
					'message' => 'Apikey salah!',
				);		
			}
		}else{
			$ret = array(
				'status' => 'error',
				'message' => 'Format salah!',
			);			
		}
		die(json_encode($ret));
	}

	public function get_sumber_dana_mapping(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$master_sumberdana = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				// sumber dana asli sipd
				if($_POST['format_sumber_dana'] == 1){

					$total_sd = 0;
					$sumberdana = $wpdb->get_results('
						select 
							d.iddana, d.kodedana, d.namadana, sum(d.pagudana) total, count(s.kode_sbl) jml 
						from data_dana_sub_keg d
							INNER JOIN data_sub_keg_bl s ON d.kode_sbl=s.kode_sbl
								AND s.tahun_anggaran=d.tahun_anggaran
								AND s.active=d.active
						where d.tahun_anggaran='.$_POST['tahun_anggaran'].'
							and s.id_sub_skpd='.$_POST['id_skpd'].'
							and d.active=1
						group by iddana
						order by kodedana ASC
					', ARRAY_A);

					foreach ($sumberdana as $key => $val) {
						$no++;
						$title = 'Laporan APBD Per Sumber Dana '.$val['kodedana'].' '.$val['namadana'].' | '.$_POST['tahun_anggaran'];
						$custom_post = get_page_by_title($title, OBJECT, 'page');
						$url_skpd = $this->get_link_post($custom_post);
						if(empty($val['kodedana'])){
							$val['kodedana'] = '';
							$val['namadana'] = 'Belum Di Setting';
						}
						$master_sumberdana .= '
							<tr>
								<td class="text_tengah atas kanan bawah kiri">'.$no.'</td>
								<td class="text_kiri atas kanan bawah">'.$val['kodedana'].'</td>
								<td class="text_kiri atas kanan bawah"><a href="'.$url_skpd.'&id_skpd='.$_POST['id_skpd'].'&mapping=1" target="_blank" data-id="'.$title.'">'.$val['namadana'].'</a></td>
								<td class="text_kanan atas kanan bawah">'.number_format($val['total'], 0,',','.').'</td>
								<td class="text_tengah atas kanan bawah">'.$val['jml'].'</td>
								<td class="text_tengah atas kanan bawah">'.$val['iddana'].'</td>
								<td class="text_tengah atas kanan bawah">'.$_POST['tahun_anggaran'].'</td>
							</tr>
						';
						$total_sd+=$val['total'];
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

					$title = 'RFK '.$skpd[0]['nama_skpd'].' '.$skpd[0]['kode_skpd'].' | '.$_POST['tahun_anggaran'];
					$shortcode = '[monitor_rfk tahun_anggaran="'.$_POST['tahun_anggaran'].'" id_skpd="'.$skpd[0]['id_skpd'].'"]';
					$update = false;
					$url_skpd = $this->generatePage($title, $_POST['tahun_anggaran'], $shortcodee, $update);

					$master_sumberdana .= "
						<tr>
							<td colspan='3' class='atas kanan bawah kiri text_tengah text_blok'>Total</td>
							<td class='atas kanan bawah text_kanan text_blok'>".number_format($total_sd, 0,',','.')."</td>
							<td class='text_tengah kanan bawah text_blok' colspan='2'>Total RKA</td>
							<td class='text_kanan kanan bawah text_blok'><a target='_blank' href='".$url_skpd."&id_skpd=".$_POST['id_skpd']."'>".number_format($total_rka[0]['total_rka'],0,",",".")."</a></td>
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
						<tbody id="body-sumber-dana">'.$master_sumberdana.'</tbody>
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
				}elseif ($_POST['format_sumber_dana'] == 2) {  
					
					$data = array();
					$total_harga = 0;
					$realisasi = 0;
					$jml_rincian = 0;

					$sub_keg_bl = $wpdb->get_results($wpdb->prepare("
						SELECT 
							kode_sbl, 
							nama_sub_giat 
						FROM data_sub_keg_bl 
						WHERE 
							active=1 AND 
							id_sub_skpd=%d AND 
							tahun_anggaran=%d", 
							$_POST['id_skpd'], $_POST['tahun_anggaran']), 
						ARRAY_A);
					
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
				    		if(empty($v2['realisasi'])){
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

				    		if(!empty($mapping)){
				    			foreach ($mapping as $key_m => $v_m) {
					    			if(empty($data[$v_m['id_dana']])){
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

						   			$data[$v_m['id_dana']]['id_rinci_sub_bl'][]=$v2['id_rinci_sub_bl'];
						   			$data[$v_m['id_dana']]['jml_rincian']++;
						   			$data[$v_m['id_dana']]['pagu'] += $v2['total_harga'];
						   			$data[$v_m['id_dana']]['realisasi'] += $v2['realisasi'];
				    			}
				    		}else{
				    			$key=0;
					    		if(empty($data[$key])){
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
			    		$title = 'Laporan APBD Per Sumber Dana '.$value['kode_dana'].' '.$value['nama_dana'].' | '.$_POST['tahun_anggaran'];
						$shortcode = '[monitor_sumber_dana tahun_anggaran="'.$_POST['tahun_anggaran'].'" id_sumber_dana="'.$value['id_dana'].'"]';
						$update = false;
						$url_skpd = $this->generatePage($title, $_POST['tahun_anggaran'], $shortcode, $update);

						// $id_rinci_sub_bl=implode(",", $value['id_rinci_sub_bl']);
						$id_rinci_sub_bl=0;
			    		$master_sumberdana .= '
			    			<tr>
			    				<td class="atas kanan bawah kiri text_tengah">'.$no.'</td>
			    				<td class="atas kanan bawah" data-id-rinci-sub-bl="'.$id_rinci_sub_bl.'">'.$value['kode_dana'].'</td>
			    				<td class="atas kanan bawah"><a href="'.$url_skpd.'&id_skpd='.$_POST['id_skpd'].'&mapping=2" target="_blank">'.$value['nama_dana'].'</a></td>
			    				<td class="atas kanan bawah text_kanan">'.number_format($value['pagu'],0,",",".").'</td>
			    				<td class="atas kanan bawah text_kanan">'.number_format($value['realisasi'],0,",",".").'</td>
			    				<td class="atas kanan bawah text_tengah">'.number_format($value['jml_rincian'],0,",",".").'</td>
			    			</tr>
			    		';
			    	}
					
					$master_sumberdana .="
						<tr>
							<td colspan='3' class='atas kanan bawah kiri text_tengah text_blok'>Total</td>
							<td class='atas kanan bawah text_kanan text_blok'>".number_format($total_harga, 0,',','.')."</td>
							<td class='atas kanan bawah text_kanan text_blok'>".number_format($realisasi, 0,',','.')."</td>
							<td class='atas kanan bawah text_tengah text_blok'>".$jml_rincian."</td>
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
						<tbody id="body-sumber-dana">'.$master_sumberdana.'</tbody>';

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
				}elseif ($_POST['format_sumber_dana'] == 3) {
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
						if(empty($data_all[$_kode_sd])){
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
				    ksort($data_all);
					foreach ($data_all as $k => $val) {
				    	$no++;
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

				    	$title = 'Laporan APBD Per Sumber Dana '.$val['kombinasi_kode_sd'].' '.$val['kombinasi_nama_sd'].' | '.$_POST['tahun_anggaran'];
						$shortcode = '[monitor_sumber_dana tahun_anggaran="'.$_POST['tahun_anggaran'].'" id_sumber_dana="'.$val['kombinasi_id_sd'].'"]';
						$update = false;
						$url_skpd = $this->generatePage($title, $_POST['tahun_anggaran'], $shortcode, $update);
						$url_skpd .= "&id_skpd=".$_POST['id_skpd']."&mapping=3";
					
				    	$master_sumberdana .= '
							<tr>
								<td class="text_tengah atas kanan bawah kiri">'.$no.'</td>
								<td class="atas kanan bawah">'.$val['kode_sd'].'</td>
								<td class="atas kanan bawah"><a href="'.$url_skpd.'" data-title="'.$title.'" target="_blank">'.$val['nama_sd'].'</a>'.'</td>
								<td class="text_kanan atas kanan bawah atas kanan bawah">'.implode('<br>', $pagu_dana).'</td>
								<td class="text_kanan atas kanan bawah atas kanan bawah">'.number_format($val['pagu_rka'],0,",",".").'</td>
								<td class="text_tengah atas kanan bawah atas kanan bawah">'.$val['jml_sub'].'</td>
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
							<td class="text_tengah atas kanan bawah kiri" colspan="3">Total</td>
							<td class="text_kanan atas kanan bawah ">'.number_format($total_sd,0,",",".").'</td>
							<td class="text_kanan atas kanan bawah "><a target="_blank" href="'.$url_skpd.'&id_skpd='.$_POST['id_skpd'].'">'.number_format($total_rka,0,",",".").'</a></td>
							<td class="text_tengah atas kanan bawah ">'.number_format($jml_sub,0,",",".").'</td>
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
		        				'.$master_sumberdana.'
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
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_rincian_sumber_dana_mapping(){
		
		global $wpdb;
		$data_all = array(
			'data' => array()
		);

		$bulan = (int) date('m');
		$list_sub_keg_bl = $wpdb->get_results($wpdb->prepare("
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
						b.bulan = ".$bulan."
			WHERE 
				a.active=1 AND 
				a.id_sub_skpd=%d AND 
				a.tahun_anggaran=%d", 
				$_POST['id_skpd'], $_POST['tahun_anggaran']), 
		ARRAY_A);

		foreach ($list_sub_keg_bl as $key_sub => $val_sub) {
			if(empty($data_all['data'][$val_sub['kode_sbl']])){
				$list_mapping = $wpdb->get_row($wpdb->prepare("
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
				ARRAY_A);

				if(!empty($list_mapping)){
					$data_all['data'][$val_sub['kode_sbl']] = array(
						'kode_sbl' => $val_sub['kode_sbl'],
						'nama_sub_giat' => $val_sub['nama_sub_giat'],
						'pagu_rka' => $val_sub['pagu'],
						'pagu_dpa' => $val_sub['pagu_simda'],
						'realisasi_anggaran' => $val_sub['realisasi_anggaran'],
						'nama_dana' => $list_mapping['nama_dana'],
						'pagu_sumber_dana' => $list_mapping['total_harga'],
						'capaian' => !empty($val_sub['pagu_simda']) ? $this->pembulatan(($val_sub['realisasi_anggaran']/$val_sub['pagu_simda']) * 100) : 0
					);
				}
			}
		}

		$no=1;
		$html_sub_body = '';
		foreach ($data_all['data'] as $key => $value) {
			$html_sub_body .= "
				<tr class='sub_keg'>
					<td class='atas kanan bawah kiri'>".$no."</td>
					<td class='atas kanan bawah'>".$value['nama_sub_giat']."</td>
					<td class='atas kanan bawah'>".$value['nama_dana']."</td>
					<td class='atas kanan bawah text_kanan'>".number_format($value['pagu_rka'],0,',','.')."</td>
					<td class='atas kanan bawah text_kanan'>".number_format($value['pagu_sumber_dana'],0,',','.')."</td>
					<td class='atas kanan bawah text_kanan'>".number_format($value['pagu_dpa'],0,',','.')."</td>
					<td class='atas kanan bawah text_kanan'>".number_format($value['realisasi_anggaran'],0,',','.')."</td>
					<td class='atas kanan bawah text_tengah'>".$value['capaian']."</td>
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
		        <tbody>'.$html_sub_body.'</tbody>
		    </table>';

		$response = array(
			'rincian' => $html_sub 
		);
		die(json_encode($response));
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

	public function get_sub_keg(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'run'	=> $_POST['run'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get sub kegiatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['idsumber'])){
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$id_skpd_fmis = $_POST['id_skpd_fmis'];
					$idsumber = $_POST['idsumber'];
					$get_id = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran);
					$id_skpd_sipd = $get_id['id_skpd_sipd'];
					$id_mapping_simda = $get_id['id_mapping_simda'];
					if(
						!empty($id_skpd_sipd) 
						&& !empty($id_skpd_fmis)
					){
						$program_mapping = $this->get_fmis_mapping(array(
							'name' => '_crb_custom_mapping_program_fmis'
						));
						$keg_mapping = $this->get_fmis_mapping(array(
							'name' => '_crb_custom_mapping_keg_fmis'
						));
						$subkeg_mapping = $this->get_fmis_mapping(array(
							'name' => '_crb_custom_mapping_subkeg_fmis'
						));
						$data_sub_keg = array();
						$type_pagu = get_option('_crb_fmis_pagu');
						if($idsumber == 1){
							$data_sub_keg = $wpdb->get_results($wpdb->prepare("
								SELECT 
									s.*,
									u.nama_skpd as nama_skpd_data_unit
								from data_sub_keg_bl s 
								inner join data_unit u on s.id_sub_skpd = u.id_skpd
									and u.tahun_anggaran = s.tahun_anggaran
									and u.active = s.active
								where s.tahun_anggaran=%d
									and u.id_skpd IN (".implode(',', $id_skpd_sipd).")
									and s.active=1", 
							$tahun_anggaran), ARRAY_A);
							foreach ($data_sub_keg as $k => $v) {
								if(
									!empty($type_pagu)
									&& $type_pagu == 2
								){
									$data_sub_keg[$k]['pagu'] = $v['pagumurni'];
									$data_sub_keg[$k]['pagu_keg'] = $v['pagumurni'];
								}
								if(!empty($program_mapping[$this->removeNewline($v['nama_program'])])){
									$data_sub_keg[$k]['nama_program'] = $program_mapping[$v['nama_program']];
								}
								if(!empty($keg_mapping[$this->removeNewline($v['nama_giat'])])){
									$data_sub_keg[$k]['nama_giat'] = $keg_mapping[$this->removeNewline($v['nama_giat'])];
								}
								$nama_sub_giat = explode(' ', $v['nama_sub_giat']);
								$kode_sub = $nama_sub_giat[0];
								unset($nama_sub_giat[0]);
								$nama_sub_giat = implode(' ', $nama_sub_giat);
								if(!empty($subkeg_mapping[$this->removeNewline($nama_sub_giat)])){
									$data_sub_keg[$k]['nama_sub_giat'] = $kode_sub.' '.$subkeg_mapping[$this->removeNewline($nama_sub_giat)];
								}
								$data_sub_keg[$k]['id_mapping'] = get_option('_crb_unit_fmis_'.$tahun_anggaran.'_'.$v['id_sub_skpd']);
								$data_sub_keg[$k]['sub_keg_indikator'] = $wpdb->get_results("
									select 
										* 
									from data_sub_keg_indikator 
									where tahun_anggaran=".$v['tahun_anggaran']."
										and kode_sbl='".$v['kode_sbl']."'
										and active=1", ARRAY_A);
								$data_sub_keg[$k]['sub_keg_indikator_hasil'] = $wpdb->get_results("
									select 
										* 
									from data_keg_indikator_hasil 
									where tahun_anggaran=".$v['tahun_anggaran']."
										and kode_sbl='".$v['kode_sbl']."'
										and active=1", ARRAY_A);
								$data_sub_keg[$k]['tag_sub_keg'] = $wpdb->get_results("
									select 
										* 
									from data_tag_sub_keg 
									where tahun_anggaran=".$v['tahun_anggaran']."
										and kode_sbl='".$v['kode_sbl']."'
										and active=1", ARRAY_A);
								$data_sub_keg[$k]['capaian_prog_sub_keg'] = $wpdb->get_results("
									select 
										* 
									from data_capaian_prog_sub_keg 
									where tahun_anggaran=".$v['tahun_anggaran']."
										and kode_sbl='".$v['kode_sbl']."'
										and active=1", ARRAY_A);
								$data_sub_keg[$k]['output_giat'] = $wpdb->get_results("
									select 
										* 
									from data_output_giat_sub_keg 
									where tahun_anggaran=".$v['tahun_anggaran']."
										and kode_sbl='".$v['kode_sbl']."'
										and active=1", ARRAY_A);
								$data_sub_keg[$k]['lokasi_sub_keg'] = $wpdb->get_results("
									select 
										* 
									from data_lokasi_sub_keg 
									where tahun_anggaran=".$v['tahun_anggaran']."
										and kode_sbl='".$v['kode_sbl']."'
										and active=1", ARRAY_A);
							}
						// sementara dicomment dulu karena get data dari simda belum siap
						// }else if($idsumber == 2){
						}else if($idsumber == 22){
							$kd_unit_simda_asli = get_option('_crb_unit_'.$id_skpd_sipd);
							$kd_unit_simda = explode('.', $kd_unit_simda_asli);
							if(
								!empty($kd_unit_simda) 
								&& !empty($kd_unit_simda[3])
							){
								if(!empty($unit_simda[$kd_unit_simda_asli])){
									unset($unit_simda[$kd_unit_simda_asli]);
								}
								$_kd_urusan = $kd_unit_simda[0];
								$_kd_bidang = $kd_unit_simda[1];
								$kd_unit = $kd_unit_simda[2];
								$kd_sub_unit = $kd_unit_simda[3];
								$kd_perubahan = '(SELECT max(kd_perubahan) from ta_rask_arsip where tahun='.$tahun_anggaran.')';
								if(
									!empty($type_pagu)
									&& $type_pagu == 2
								){
									$kd_perubahan = 4;
								}
								$sql = $wpdb->prepare("
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
								foreach($sub_keg as $k => $dpa){
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
									if($keg90[0]->kd_program90 == 1){
										$keg90[0]->kd_urusan90 = 'X';
										$keg90[0]->kd_bidang90 = 'XX';
									}
									$kd_program = $keg90[0]->kd_urusan90.'.'.$this->simda->CekNull($keg90[0]->kd_bidang90).'.'.$this->simda->CekNull($keg90[0]->kd_program90);
									$kd_keg = $kd_program.'.'.$this->simda->CekNull($keg90[0]->kd_kegiatan90);
									$kd_sub_keg = $kd_keg.'.'.$this->simda->CekNull($keg90[0]->kd_sub_kegiatan);

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
									if(!empty($program_mapping[$this->removeNewline($nama_program)])){
										$newdata['nama_program'] = $program_mapping[$this->removeNewline($nama_program)];
									}else{
										$newdata['nama_program'] = $nama_program;
									}
									if(!empty($keg_mapping[$this->removeNewline($nama_keg)])){
										$newdata['nama_giat'] = $keg_mapping[$this->removeNewline($nama_keg)];
									}else{
										$newdata['nama_giat'] = $nama_keg;
									}
									if(!empty($subkeg_mapping[$this->removeNewline($nama_sub_keg)])){
										$newdata['nama_sub_giat'] = $kd_sub_keg.' '.$subkeg_mapping[$this->removeNewline($nama_sub_keg)];
									}else{
										$newdata['nama_sub_giat'] = $kd_sub_keg.' '.$nama_sub_keg;
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

									$kd_unit_simda = $dpa->kd_urusan.'.'.$dpa->kd_bidang.'.'.$dpa->kd_unit.'.'.$dpa->kd_sub;
									$newdata['kd_unit_simda'] = $kd_unit_simda;
									if($id_mapping_simda[$kd_unit_simda]){
										$newdata['id_mapping'] = $id_mapping_simda[$kd_unit_simda]['id_mapping_fmis'];
										$newdata['nama_sub_skpd'] = $id_mapping_simda[$kd_unit_simda]['nama_skpd'];
									}else{
										$newdata['nama_sub_skpd'] = 'Kode Unit SIMDA = '.$kd_unit_simda.', belum dimapping!';
									}
									$newdata['kode_sbl'] = $kd_keg;

									$data_sub_keg[] = $newdata;
								}
							}else{
								$ret['status'] = 'error';
								$ret['message'] = 'SKPD SIMDA PINK belum dimapping!';
							}
						}
						$ret['data'] = $data_sub_keg;
					}else{
						$ret['status'] = 'error';
						$ret['message'] = 'id_skpd_fmis='.$_POST['id_skpd_fmis'].' tahun_anggaran='.$tahun_anggaran.' belum dimapping!';
					}
				}else{
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

	function removeNewline($string){
		return preg_replace('/\s+/S', " ", $string);
	}

	public function get_fmis_mapping($options){
		if($options['name'] == '_crb_custom_mapping_rekening_fmis'){
			$mapping = get_option($options['name']);
			$mapping = explode(',', $mapping);
			$ret = array();
			foreach($mapping as $map){
				$map = explode('-', $map);
				$ret[$map[0]] = $map[1];
			}
		}else{
			$mapping = get_option($options['name']);
			$mapping = explode('],[', $mapping);
			$ret = array();
			foreach($mapping as $map){
				$map = explode(']-[', $map);
				$ret[str_replace('[', '', $this->removeNewline($map[0]))] = str_replace(']', '', $this->removeNewline($map[1]));
			}
		}
		return $ret;
	}

	public function get_sub_keg_rka(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil RKA get sub kegiatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
				$id_sumber_dana_default = get_option('_crb_default_sumber_dana' );
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
				if($type_aktivitas != 2){
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
					if(
						!empty($type_pagu)
						&& $type_pagu == 2
					){
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
					if(!empty($rek_mapping[$kode_akun])){
						$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
						$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
						$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
						$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
						$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
						$rka[$k]['kode_akun'] = implode('.', $_kode_akun);
					}
					if($type_aktivitas == 2){
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
						if(
							!empty($sumber_dana) 
							&& !empty($sumber_dana[0])
							&& !empty($sumber_dana[0]['nama_dana'])
						){
							$rka[$k]['sumber_dana'] = $sumber_dana;
						}else{
							$rka[$k]['sumber_dana'] = $sumber_dana_default;
						}
					}else{
						if(
							!empty($sumber_dana_single) 
							&& !empty($sumber_dana_single[0])
							&& !empty($sumber_dana_single[0]['nama_dana'])
						){
							$rka[$k]['sumber_dana'] = $sumber_dana_single;
						}else{
							$rka[$k]['sumber_dana'] = $sumber_dana_default;
						}
					}
				}
				$ret['data'] = $rka;
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	function mapping_skpd_fmis(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil mapping SKPD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
						if(!empty($id_skpd_sipd)){
							update_option('_crb_unit_fmis_'.$tahun_anggaran.'_'.$id_skpd_sipd, $skpd['id'].'.'.$sub_unit['id']);
							$ret['data_sukses_mapping'][] = $sub_unit;
						}else{
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

	function mapping_rek_fmis(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil cek mapping rekening!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['rek'])){
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
					foreach($rek_fmis as $rek){
						$rek = (array) $rek;
						$new_rek_fmis[$rek['kdrek']] = $rek;
					}
					$cek_sipd_belum_ada_di_fmis = array();
					foreach($rek_sipd as $rek){
						$_kode_akun = explode('.', $rek['kode_akun']);
						$kode_akun = array();
						foreach ($_kode_akun as $v) {
							$kode_akun[] = (int)$v;
						}
						$kode_akun = implode('.', $kode_akun);
						if(empty($new_rek_fmis[$kode_akun])){
							$cek_sipd_belum_ada_di_fmis[$kode_akun] = $rek;
						}
					}
					$current_mapping = get_option('_crb_custom_mapping_rekening_fmis');
					$current_mapping = explode(',', $current_mapping);
					$mapping_rek = array();
					foreach($current_mapping as $v){
						$rek = explode('-', $v);
						$mapping_rek[$rek[0]] = '';
						if(!empty($rek[1])){
							$mapping_rek[$rek[0]] = $rek[1];
						}
					}

					$mapping = array();
					foreach($cek_sipd_belum_ada_di_fmis as $k => $v){
						if(!empty($mapping_rek[$k])){
							$mapping[] = $k.'-'.$mapping_rek[$k];
						}else{
							$mapping[] = $k.'-'.$k;
						}
					}
					update_option( '_crb_custom_mapping_rekening_fmis', implode(',', $mapping) );
					$ret['data_rek'] = $cek_sipd_belum_ada_di_fmis;
				}else{
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

	public function get_data_pendapatan(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get data Pendapatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_skpd_fmis = $_POST['id_skpd_fmis'];
				$get_id = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran);
				$id_skpd_sipd = $get_id['id_skpd_sipd'];
				if(
					!empty($id_skpd_sipd) 
					&& !empty($id_skpd_fmis)
				){
					$pendapatan = $wpdb->get_results($wpdb->prepare('
						SELECT 
							p.*,
							u.nama_skpd
						FROM data_pendapatan p
							LEFT JOIN data_unit u ON p.id_skpd=u.id_skpd
								AND u.active=p.active
								AND u.tahun_anggaran=p.tahun_anggaran
						WHERE p.tahun_anggaran=%d
							and p.id_skpd IN ('.implode(',', $id_skpd_sipd).')
							and p.active=1
					', $_POST['tahun_anggaran']), ARRAY_A);
					$ret['sql'] = $wpdb->last_query;
					$type_pagu = get_option('_crb_fmis_pagu');
					$rek_mapping = $this->get_fmis_mapping(array(
						'name' => '_crb_custom_mapping_rekening_fmis'
					));
					foreach($pendapatan as $k => $v){
						if(
							!empty($type_pagu)
							&& $type_pagu == 2
						){
							$pendapatan[$k]['total'] = $v['nilaimurni'];
						}
						$_kode_akun = explode('.', $v['kode_akun']);
						$kode_akun = array();
						foreach ($_kode_akun as $vv) {
							$kode_akun[] = (int)$vv;
						}
						$kode_akun = implode('.', $kode_akun);
						if(!empty($rek_mapping[$kode_akun])){
							$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
							$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
							$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
							$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
							$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
							$pendapatan[$k]['kode_akun'] = implode('.', $_kode_akun);
						}
						$pendapatan[$k]['id_mapping'] = get_option('_crb_unit_fmis_'.$tahun_anggaran.'_'.$v['id_skpd']);
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

	public function get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran, $cek_sub_unit=false){
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
			$kd_unit_simda_asli = get_option('_crb_unit_'.$v['id_skpd']);
			$id_mapping = get_option('_crb_unit_fmis_'.$tahun_anggaran.'_'.$v['id_skpd']);
			$id_mapping_simda[$kd_unit_simda_asli] = array(
				'id_skpd' => $v['id_skpd'],
				'id_mapping_fmis' => $id_mapping,
				'kode_skpd' => $v['kode_skpd'],
				'nama_skpd' => $v['nama_skpd']
			);
			if(!empty($id_skpd_fmis)){
				$id_fmis = explode('.', $id_skpd_fmis);
				if(count($id_fmis) >= 2){
					if($id_mapping == $id_skpd_fmis){
						$id_skpd_sipd[] = $v['id_skpd'];
						$kode_skpd_sipd[] = $v['kode_skpd'];
					}
				}else{
					if(empty($cek_sub_unit)){
						$id_mappings = explode('.', $id_mapping);
						if($id_mappings[0] == $id_fmis[0]){
							$id_skpd_sipd[] = $v['id_skpd'];
							$kode_skpd_sipd[] = $v['kode_skpd'];
						}
					}else{
						$id_mappings = explode('.', $id_mapping);
						if($id_mappings[1] == $id_fmis[0]){
							$id_skpd_sipd[] = $v['id_skpd'];
							$kode_skpd_sipd[] = $v['kode_skpd'];
						}
					}
				}
			}else if(false === $id_skpd_fmis){
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

	public function get_data_pembiayaan(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil get data Pembiayaan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_skpd_fmis = $_POST['id_skpd_fmis'];
				$get_id = $this->get_id_skpd_fmis($id_skpd_fmis, $tahun_anggaran);
				$id_skpd_sipd = $get_id['id_skpd_sipd'];
				if(
					!empty($id_skpd_sipd) 
					&& !empty($id_skpd_fmis)
				){
					$data_db = $wpdb->get_results($wpdb->prepare('
						SELECT 
							p.*,
							u.nama_skpd
						FROM data_pembiayaan p
							LEFT JOIN data_unit u ON p.id_skpd=u.id_skpd
								AND u.active=p.active
								AND u.tahun_anggaran=p.tahun_anggaran
						WHERE p.tahun_anggaran=%d
							and p.id_skpd IN ('.implode(',', $id_skpd_sipd).')
							and p.active=1
					', $_POST['tahun_anggaran']), ARRAY_A);
					$type_pagu = get_option('_crb_fmis_pagu');
					$rek_mapping = $this->get_fmis_mapping(array(
						'name' => '_crb_custom_mapping_rekening_fmis'
					));
					foreach($data_db as $k => $v){
						if(
							!empty($type_pagu)
							&& $type_pagu == 2
						){
							$data_db[$k]['total'] = $v['nilaimurni'];
						}
						$_kode_akun = explode('.', $v['kode_akun']);
						$kode_akun = array();
						foreach ($_kode_akun as $vv) {
							$kode_akun[] = (int)$vv;
						}
						$kode_akun = implode('.', $kode_akun);
						if(!empty($rek_mapping[$kode_akun])){
							$_kode_akun = explode('.', $rek_mapping[$kode_akun]);
							$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
							$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
							$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
							$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
							$data_db[$k]['kode_akun'] = implode('.', $_kode_akun);
						}
						$data_db[$k]['id_mapping'] = get_option('_crb_unit_fmis_'.$tahun_anggaran.'_'.$v['id_skpd']);
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

	function mapping_sub_kegiatan_fmis(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil cek mapping sub kegiatan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['sub_kegiatan'])){
					$sub_keg_fmis = json_decode(stripslashes(html_entity_decode($_POST['sub_kegiatan'])));
					$new_prog_fmis = array();
					$new_keg_fmis = array();
					$new_sub_keg_fmis = array();
					foreach($sub_keg_fmis as $sub){
						$sub = (array) $sub;
						$new_sub_keg_fmis[strtolower(trim($sub['sub_kegiatan']))] = $sub;
						$new_keg_fmis[strtolower(trim($sub['kegiatan']))] = $sub;
						$new_prog_fmis[strtolower(trim($sub['program']))] = $sub;
					}
					$sub_keg_sipd = $wpdb->get_results($wpdb->prepare("
						SELECT 
							*
						FROM
							data_prog_keg
						WHERE tahun_anggaran=%d
					", $_POST['tahun_anggaran']), ARRAY_A);
					$cek_sipd_belum_ada_di_fmis = array();
					$cek_sipd_keg_belum_ada_di_fmis = array();
					$cek_sipd_prog_belum_ada_di_fmis = array();
					foreach($sub_keg_sipd as $sub){
						// cek sub kegiatan
						$_nama_sub = explode(' ', $sub['nama_sub_giat']);
						unset($_nama_sub[0]);
						$nama_sub = trim(implode(' ', $_nama_sub));
						if(empty($new_sub_keg_fmis[strtolower($nama_sub)])){
							$cek_sipd_belum_ada_di_fmis[$nama_sub] = $sub;
						}

						// cek kegiatan
						$_nama_giat = explode(' ', $sub['nama_giat']);
						unset($_nama_giat[0]);
						$nama_giat = trim(implode(' ', $_nama_giat));
						if(empty($new_keg_fmis[strtolower($nama_giat)])){
							$cek_sipd_keg_belum_ada_di_fmis[$nama_giat] = $sub;
						}

						// cek program
						$_nama_program = explode(' ', $sub['nama_program']);
						unset($_nama_program[0]);
						$nama_program = trim(implode(' ', $_nama_program));
						if(empty($new_prog_fmis[strtolower($nama_program)])){
							$cek_sipd_prog_belum_ada_di_fmis[$nama_program] = $sub;
						}
					}

					// update mapping sub kegiatan
					$current_mapping = get_option('_crb_custom_mapping_subkeg_fmis');
					$current_mapping = explode(',', $current_mapping);
					$mapping_sub = array();
					foreach($current_mapping as $v){
						$rek = explode('-', $v);
						$mapping_sub[$rek[0]] = '';
						if(!empty($rek[1])){
							$mapping_sub[$rek[0]] = $rek[1];
						}
					}
					$mapping = array();
					foreach($cek_sipd_belum_ada_di_fmis as $k => $v){
						$k = '['.$k.']';
						if(!empty($mapping_sub[$k])){
							$mapping[] = $k.'-'.$mapping_sub[$k];
						}else{
							$mapping[] = $k.'-'.$k;
						}
					}
					update_option( '_crb_custom_mapping_subkeg_fmis', implode(',', $mapping) );

					// update mapping kegiatan
					$current_mapping = get_option('_crb_custom_mapping_keg_fmis');
					$current_mapping = explode(',', $current_mapping);
					$mapping_keg = array();
					foreach($current_mapping as $v){
						$rek = explode('-', $v);
						$mapping_keg[$rek[0]] = '';
						if(!empty($rek[1])){
							$mapping_keg[$rek[0]] = $rek[1];
						}
					}
					$mapping = array();
					foreach($cek_sipd_keg_belum_ada_di_fmis as $k => $v){
						$k = '['.$k.']';
						if(!empty($mapping_keg[$k])){
							$mapping[] = $k.'-'.$mapping_keg[$k];
						}else{
							$mapping[] = $k.'-'.$k;
						}
					}
					update_option( '_crb_custom_mapping_keg_fmis', implode(',', $mapping) );

					// update mapping program
					$current_mapping = get_option('_crb_custom_mapping_program_fmis');
					$current_mapping = explode(',', $current_mapping);
					$mapping_prog = array();
					foreach($current_mapping as $v){
						$rek = explode('-', $v);
						$mapping_prog[$rek[0]] = '';
						if(!empty($rek[1])){
							$mapping_prog[$rek[0]] = $rek[1];
						}
					}
					$mapping = array();
					foreach($cek_sipd_prog_belum_ada_di_fmis as $k => $v){
						$k = '['.$k.']';
						if(!empty($mapping_prog[$k])){
							$mapping[] = $k.'-'.$mapping_prog[$k];
						}else{
							$mapping[] = $k.'-'.$k;
						}
					}
					update_option( '_crb_custom_mapping_program_fmis', implode(',', $mapping) );

					$ret['data_prog'] = $cek_sipd_prog_belum_ada_di_fmis;
					$ret['data_keg'] = $cek_sipd_keg_belum_ada_di_fmis;
					$ret['data_sub'] = $cek_sipd_belum_ada_di_fmis;
					$ret['message'] = 'Program yang perlu dimapping ada '.count($cek_sipd_prog_belum_ada_di_fmis).' program. Kegiatan yang perlu dimapping ada '.count($cek_sipd_keg_belum_ada_di_fmis).' kegiatan. Sub kegiatan yang perlu dimapping ada '.count($cek_sipd_belum_ada_di_fmis).' sub kegiatan. Informasi detail cek di WP-SIPD dashboard.';
				}else{
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

	function singkroniasi_total_sub_keg_fmis(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'status'	=> 'success',
			'message'	=> 'Berhasil edit pagu_fmis sub kegiatan SIPD!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['data'])){
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$sub_keg_fmis = json_decode(stripslashes($_POST['data']));
					$ret['message_rinci'] = array();
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
					foreach($sub_keg_fmis as $fmis){
						$data_fmis = (array) $fmis;
						$new_rincian = array();
						foreach($data_fmis['rincian'] as $rincian){
							$new_rincian[] = (array) $rincian;
						}
						$data_fmis['rincian'] = $new_rincian;
						$data_fmis['sub_kegiatan_asli'] = $data_fmis['sub_kegiatan'];
						foreach($subkeg_mapping as $nama_sub_sipd => $nama_sub_fmis){
							if($this->removeNewline($data_fmis['sub_kegiatan']) == $this->removeNewline($nama_sub_fmis)){
								$data_fmis['sub_kegiatan'] = $nama_sub_sipd;
							}
						}
						$data_fmis['kegiatan_asli'] = $data_fmis['kegiatan'];
						foreach($keg_mapping as $nama_giat_sipd => $nama_giat_fmis){
							if($this->removeNewline($data_fmis['kegiatan']) == $this->removeNewline($nama_giat_fmis)){
								$data_fmis['kegiatan'] = $nama_giat_sipd;
							}
						}
						$data_fmis['program_asli'] = $data_fmis['program'];
						foreach($program_mapping as $nama_program_sipd => $nama_program_fmis){
							if($this->removeNewline($data_fmis['program']) == $this->removeNewline($nama_program_fmis)){
								$data_fmis['program'] = $nama_program_sipd;
							}
						}
						$id_mapping = $data_fmis['idsubunit'];
						$get_id = $this->get_id_skpd_fmis($id_mapping, $tahun_anggaran, true);
						$id_skpd_sipd = $get_id['id_skpd_sipd'];
						if(!empty($id_skpd_sipd)){
							$cek_aktivitas = array();
							$singkron_rincian_fmis = get_option( '_crb_backup_rincian_fmis' );
							if($singkron_rincian_fmis == 1){
								foreach($data_fmis['rincian'] as $key => $rinci){
									foreach($rek_mapping as $rek_mapping_sipd => $rek_mapping_fmis){
										$_kode_akun = explode('.', $rinci['kode_rekening']);
										$_kode_akun = (int)$_kode_akun[0].'.'.(int)$_kode_akun[1].'.'.(int)$_kode_akun[2].'.'.(int)$_kode_akun[3].'.'.(int)$_kode_akun[4].'.'.(int)$_kode_akun[5];
										if($_kode_akun == $rek_mapping_fmis){
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
									if(empty($cek_aktivitas[$rinci['idaktivitas']])){
										$wpdb->update('data_rincian_fmis', array(
											'active' => 0
										), array(
											'idaktivitas' => $rinci['idaktivitas']
										));
										$cek_aktivitas[$rinci['idaktivitas']] = true;
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
									if(!empty($rinci['created_at'])){
										$opsi['created_at'] = $rinci['created_at'];
									}
									if(empty($get_rinci)){
										$wpdb->insert('data_rincian_fmis', $opsi);
									}else{
										$wpdb->update('data_rincian_fmis', $opsi, array(
											'id' => $get_rinci[0]['id']
										));
									}
								}
							}

							// belanja
							if($data_fmis['rincian'][0]['kdrek1'] == 5){
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
								$ret['sql'] = $wpdb->last_query;
								$sub = array();
								foreach($sub_sipd as $v){
									// pengecekan karena sub kegiatan belum dimutakhirkan sedangkan master program kegiatan sudah dimutakhirkan
									if(empty($v['nama_sub_giat'])){
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
										if(!empty($bl)){
											$new_sub = array();
											foreach($bl as $vv){
												$nm_sub_x = explode(' ', $vv['nama_sub_giat']);
												unset($nm_sub_x[0]);
												if(
													$this->replace_text(implode(' ', $nm_sub_x)) == $this->replace_text($data_fmis['sub_kegiatan'])
													|| $this->replace_text(implode(' ', $nm_sub_x)) == $this->replace_text($data_fmis['sub_kegiatan_asli'])
												){
													$new_sub = $vv;
												}
											}
											if(!empty($new_sub)){
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
									if(
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
									){
										$sub = $v;
									}
								}
								if(!empty($sub)){
									$wpdb->update('data_sub_keg_bl', array(
										'pagu_fmis' => $data_fmis['total']
									), array(
										'id' => $sub['id']
									));
								}else{
									$ret['status'] = 'error';
									$ret['message'] = 'Sub Kegiatan SIPD dari id_sub_skpd='.$id_skpd_sipd[0].' dan sub kegiatan="'.$data_fmis['sub_kegiatan'].'" tidak ditemukan';
								}
							// pendapatan
							}else if($data_fmis['rincian'][0]['kdrek1'] == 4){
								foreach($data_fmis['rincian'] as $rinci){
									$uraian_belanja = $this->get_uraian_belanja_fmis($rinci['uraian_belanja']);
									$uraian = $uraian_belanja['uraian'];
									$keterangan = $uraian_belanja['keterangan'];
									$sub_sipd = $wpdb->get_results($wpdb->prepare("
										SELECT
											id
										FROM data_pendapatan
										WHERE tahun_anggaran = %d
											AND active = 1
											AND id_skpd = %d
											AND kode_akun = %s
											AND uraian like %s
											AND keterangan like %s
									", $tahun_anggaran, $id_skpd_sipd[0], $rinci['kode_rekening'], $uraian.'%', $keterangan.'%'), ARRAY_A);
									if(!empty($sub_sipd)){
										$wpdb->update('data_pendapatan', array(
											'pagu_fmis' => $rinci['jumlah']
										), array(
											'id' => $sub_sipd[0]['id']
										));
									}else{
										$ret['message_rinci'][] = 'Rekening Pendapatan SIPD dari kode_akun='.$rinci['kode_rekening'].', id_skpd='.$id_skpd_sipd[0].' dan aktivitas="'.$rinci['aktivitas'].'" tidak ditemukan | '.$wpdb->last_query;
									}
								}
							// pembiayaan
							}else if($data_fmis['rincian'][0]['kdrek1'] == 6){
								foreach($data_fmis['rincian'] as $rinci){
									$uraian_belanja = $this->get_uraian_belanja_fmis($rinci['uraian_belanja']);
									$uraian = $uraian_belanja['uraian'];
									$keterangan = $uraian_belanja['keterangan'];
									$sub_sipd = $wpdb->get_results($wpdb->prepare("
										SELECT
											id
										FROM data_pembiayaan
										WHERE tahun_anggaran = %d
											AND active = 1
											AND id_skpd = %d
											AND kode_akun = %s
											AND uraian like %s
											AND keterangan like %s
									", $tahun_anggaran, $id_skpd_sipd[0], $rinci['kode_rekening'], $uraian.'%', $keterangan.'%'), ARRAY_A);
									if(!empty($sub_sipd)){
										$wpdb->update('data_pembiayaan', array(
											'pagu_fmis' => $rinci['jumlah']
										), array(
											'id' => $sub_sipd[0]['id']
										));
									}else{
										$ret['message_rinci'][] = 'Rekening Pembiayaan SIPD dari kode_akun='.$rinci['kode_rekening'].', id_skpd='.$id_skpd_sipd[0].' dan aktivitas="'.$rinci['aktivitas'].'" tidak ditemukan | '.$wpdb->last_query;
									}
								}
							}else{
								$ret['status'] = 'error';
								$ret['message'] = 'Jenis rincian tidak ditemukan! Bukan belanja, pendapatan dan pembiayaan.';
							}
						}else{
							$ret['status'] = 'error';
							$ret['message'] = 'Sub Unit SIPD dari id mapping '.$id_mapping.' tidak ditemukan';
						}
					}
				}else{
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

	function replace_text($text, $debug=false){
		$text = strtolower(trim(html_entity_decode($text), " \t\n\r\0\x0B\xC2\xA0"));
		$text = $this->removeNewline($text);
		if($debug){
			echo $text.' | ';
		}
		return $text;
	}

	function get_uraian_belanja_fmis($uraian){
		$ret = array(
			'uraian' => '',
			'keterangan' => ''
		);
		$uraian_belanja = explode('Rupiah ', $uraian);
		if(count($uraian_belanja) >= 2){
			$uraian_belanja = explode(' | ', $uraian_belanja[1]);
			$ret['uraian'] = explode("\n", $uraian_belanja[0]);
			$ret['uraian'] = explode("\t", $ret['uraian'][0]);
			$ret['uraian'] = explode("  ", $ret['uraian'][0]);
			$ret['uraian'] = $ret['uraian'][0];
			if(count($uraian_belanja) >= 2){
				$ret['keterangan'] = explode("\n", $uraian_belanja[1]);
				$ret['keterangan'] = explode("\t", $ret['keterangan'][0]);
				$ret['keterangan'] = explode("  ", $ret['keterangan'][0]);
				$ret['keterangan'] = $ret['keterangan'][0];
			}
		}
		return $ret;
	}

	public function data_ssh_usulan($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);
		if(empty($user_meta->roles)){
			echo 'User ini tidak dapat akses sama sekali :)';
		}else if(in_array("administrator", $user_meta->roles) || in_array("PLT", $user_meta->roles) || in_array("PA", $user_meta->roles) || in_array("KPA", $user_meta->roles)){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-ssh-usulan.php';
		}
	}

	public function get_data_usulan_ssh(){
		global $wpdb;
		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$params = $columns = $totalRecords = array();
				$params = $_REQUEST;
				$columns = array( 
					0 =>'id_standar_harga',
					1 =>'kode_standar_harga', 
					2 => 'nama_standar_harga',
					3 => 'spek',
					4 => 'satuan',
					5 => 'harga',
					6 => 'status',
					7 => 'keterangan_status',
					8 => 'status_upload_sipd',
					9 => 'created_user',
					10=> 'kode_standar_harga_sipd',
					11=> 'created_at',
					12=> 'keterangan_lampiran',
					13=> 'status_jenis_usulan',
					14=> 'jenis_produk',
					15=> 'tkdn'
				);
				$where = $sqlTot = $sqlRec = "";

				// check search value exist
				if( !empty($params['search']['value']) ) {
					$where .=" AND ( kode_standar_harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");    
					$where .=" OR nama_standar_harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR spek LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR id_standar_harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%")." )";
				}

				/** check filter */
				if(!empty($_POST['filter'])){
					if($_POST['filter'] == 'diterima'){
						$where .=" AND status = 'approved' ";
					}else if($_POST['filter'] == 'ditolak'){
						$where .=" AND status = 'rejected' ";
					}else if($_POST['filter'] == 'menunggu'){
						$where .=" AND status = 'waiting' ";
					}else if($_POST['filter'] == 'sudah_upload_sipd'){
						$where .=" AND status_upload_sipd = '1' ";
					}else if($_POST['filter'] == 'belum_upload_sipd'){
						$where .=" AND status_upload_sipd != '1' OR status_upload_sipd IS NULL ";
					}
				}

				/** Jika admin tampilkan semua data */
				if(!in_array("administrator",$user_meta->roles)){
					$this_user_meta = get_user_meta($user_id);
					/** cari data user berdasarkan nama skpd */
					if($this_user_meta['_crb_nama_skpd'][0] != ''){
						$user_meta = get_users(array(
							'meta_key' => '_crb_nama_skpd',
							'meta_value' => $this_user_meta['_crb_nama_skpd'][0]
						));
						
						$id_user_skpd = array();
						foreach ($user_meta as $metaVal) {
							array_push($id_user_skpd,$metaVal->data->ID);
						}
						$get_by_skpd = $id_user_skpd;
					}else{
						$get_by_skpd = array($user_id);
					}
					
					/** menambahkan filter data usulan ssh berdasarkan skpd terkait */
					if(count($get_by_skpd) >= 1){
						foreach($get_by_skpd as $skpd_key => $skpd_val){
							if($skpd_key == 0){
								$where .=" AND created_user = ".$skpd_val." ";
							}else if($skpd_key > 1){
								$where .=" OR created_user = ".$skpd_val." ";
							}
						}
					}
				}

				// getting total number records without any search
				$sql_tot = "SELECT count(*) as jml FROM `data_ssh_usulan`";
				$sql = "SELECT ".implode(', ', $columns)." FROM `data_ssh_usulan`";
				$where_first = " WHERE id_standar_harga IS NOT NULL AND tahun_anggaran=".$wpdb->prepare('%d', $params['tahun_anggaran']);
				$sqlTot .= $sql_tot.$where_first;
				$sqlRec .= $sql.$where_first;
				if(isset($where) && $where != '') {
					$sqlTot .= $where;
					$sqlRec .= $where;
				}

			 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";

				$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				$totalRecords = $queryTot[0]['jml'];
				$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

				foreach($queryRecords as $recKey => $recVal){
					$iconPlus 	= '<i class="dashicons dashicons-plus"></i>';
					$akun 		= '<li><a class="btn btn-primary" href="#" onclick="return edit_akun_ssh_usulan(\''.$recVal['id_standar_harga'].'\');" title="Rekening penyusun usulan SSH">'.$iconPlus.'</a></li>';

					$iconX		= '<i class="dashicons dashicons-trash"></i>';

					$iconEdit 	= '<i class="dashicons dashicons-edit"></i>';
					if($recVal['status'] == 'waiting' && $recVal['status_upload_sipd'] != 1 || in_array("administrator", $user_meta->roles) && $recVal['status_upload_sipd'] != 1){
						$editUsulanSSH = '<li><a class="btn btn-warning" href="#" onclick="return edit_ssh_usulan(\''.$recVal['status_jenis_usulan'].'\',\''.$recVal['id_standar_harga'].'\');" title="Edit komponen usulan SSH">'.$iconEdit.'</a></li>';
						$deleteUsulanSSH = '<li><a class="btn btn-danger" href="#" onclick="return delete_ssh_usulan(\''.$recVal['id_standar_harga'].'\');" title="Delete komponen usulan SSH">'.$iconX.'</a></li>';
					}else{
						$jenis = ($recVal['status_upload_sipd'] == 1) ? 'upload' : 'usulan';
						$editUsulanSSH = '<li><a class="btn btn-warning" href="#" onclick="return cannot_change_ssh_usulan(\'ubah\',\''.$jenis.'\');" title="Edit komponen usulan SSH">'.$iconEdit.'</a></li>';
						$deleteUsulanSSH = '<li><a class="btn btn-danger" href="#" onclick="return cannot_change_ssh_usulan(\'hapus\',\''.$jenis.'\');" title="Delete komponen usulan SSH">'.$iconX.'</a></li>';
					}

					$created_user = "";
					if(!empty($recVal['created_user'])){
						$created_user_data = get_userdata($recVal['created_user']);
						$created_user_meta = get_user_meta($recVal['created_user']);
						$pengusul = ($created_user_meta['_crb_nama_skpd'][0] == '') ? $created_user_data->display_name : $created_user_meta['_crb_nama_skpd'][0];

						$created_user = '<tr><td>Pengusul: '.$pengusul.'</td></tr>';
					}
					$keterangan_status = "";
					if(!empty($recVal['keterangan_status'])){
						if(strlen($recVal['keterangan_status']) > 25){
							$keterangan_status = '<tr><td class="in-kol-keterangan">Komentar: '.substr($recVal['keterangan_status'],0,20).'<span class="dots">...</span>';
							$keterangan_status .= '<span class="hide more">'.substr($recVal['keterangan_status'],20).'</span>&nbsp;<span class="more-bold medium-bold" onclick="readMore(this)">more</span></td></tr>';
						}else{
							$keterangan_status = '<tr><td>Komentar: '.$recVal['keterangan_status'].'</td></tr>';
						}
					}

					$created_at = "";
					if(!empty($recVal['created_at'])){
						$created_at = '<tr><td>Tanggal: '.date( 'd-m-Y', strtotime($recVal['created_at'])).'</td></tr>';
					}

					$keterangan_lampiran = "";
					if(!empty($recVal['keterangan_lampiran'])){
						if(strlen($recVal['keterangan_lampiran']) > 25){
							$keterangan_lampiran = '<tr><td class="in-kol-keterangan">Lampiran: '.substr($recVal['keterangan_lampiran'],0,20).'<span class="dots">...</span>';
							$keterangan_lampiran .= '<span class="hide more">'.substr($recVal['keterangan_lampiran'],20).'</span>&nbsp;<span class="more-bold medium-bold" onclick="readMore(this)">more</span></td></tr>';
						}else{
							$keterangan_lampiran = '<tr><td>Lampiran: '.$recVal['keterangan_lampiran'].'</td></tr>';
						}
					}

					if($recVal['status_upload_sipd'] == 0 || $recVal['status_upload_sipd'] == NULL){
						$queryRecords[$recKey]['status_upload_sipd'] = 'Belum';
					}else if($recVal['status_upload_sipd'] == 1){
						$queryRecords[$recKey]['status_upload_sipd'] = 'Sudah';
					}

					$status_verif = '';
					if($recVal['status'] == 'approved'){
						$status_verif = 'Disetujui';
					}else if($recVal['status'] == 'rejected'){
						$status_verif = 'Ditolak';
					}else if($recVal['status'] == 'waiting'){
						$status_verif = 'Menunggu';
					}

					if(in_array("administrator", $user_meta->roles)){
						$iconFilter = '<i class="dashicons dashicons-yes"></i>';
						$verify = '<li><a class="btn btn-success" href="#" onclick="return verify_ssh_usulan(\''.$recVal['id_standar_harga'].'\');" title="Verifikasi Item Usulan SSH">'.$iconFilter.'</a></li>';
					}else{
						$verify = '';
					}
					$deleteCheck = '<input type="checkbox" class="delete_check" id="delcheck_'.$recVal['id_standar_harga'].'" onclick="checkcheckbox();" value="'.$recVal['id_standar_harga'].'">';

					$kode_komponen = '<table style="margin: 0;"><tr><td>Usulan: '.$recVal['kode_standar_harga'].'</td></tr>';
					if(!empty($recVal['kode_standar_harga_sipd'])){
						$kode_komponen .= '<tr><td>Data SIPD: '.$recVal['kode_standar_harga_sipd'].'</td></tr>';
					}
					$kode_komponen .= '</table>';

					$arr_jenis_produk = [0 => 'Luar Negeri', 1 => 'Dalam Negeri'];
					$jenis_produk = ($recVal['jenis_produk'] == 0 || $recVal['jenis_produk'] == 1) ? $arr_jenis_produk[$recVal['jenis_produk']] : '-';
					$spek_satuan = '<table style="margin: 0;"><tr><td>Spesifikasi: '.ucwords($recVal['spek']).'</tr></td>';
					$spek_satuan .= '<tr><td>Satuan: '.ucwords($recVal['satuan']).'</td></tr><tr><td>Jenis Produk: '.$jenis_produk.'</td></tr><tr><td>TKDN: '.$recVal['tkdn'].' %</td></tr></table>';

					$show_status = '<table style="margin: 0;">';
					$show_status .= '<tr><td>Usulan: <span class="medium-bold-2">'.$status_verif.'</span></td></tr>';
					$show_status .= '<tr><td>Upload SIPD: <span class="medium-bold-2">'.ucwords($queryRecords[$recKey]['status_upload_sipd']).'</span></td></tr>';
					$show_status .= '<tr><td>Jenis: <span class="medium-bold-2">'.ucwords(str_replace("_"," ",$recVal['status_jenis_usulan'])).'</span></td></tr></table>';

					if($recVal['status_upload_sipd'] == 1){
						$tombol_aksi = '<li><a class="btn btn-success" href="#" onclick="alert(\'Usulan SSH sudah diupload ke SIPD\')" title="Usulan SSH sudah diupload ke SIPD"><span class="dashicons dashicons-lock"></span></a></li>';
					}else{
						$tombol_aksi = $verify.$editUsulanSSH.$deleteUsulanSSH;
					}

					$queryRecords[$recKey]['aksi'] = '<ul class="td-aksi">'.$tombol_aksi.'</ul>';	
					$queryRecords[$recKey]['deleteCheckbox'] = $deleteCheck;
					$queryRecords[$recKey]['show_kode_komponen'] = $kode_komponen;
					$queryRecords[$recKey]['spek_satuan'] = $spek_satuan;
					$queryRecords[$recKey]['show_status'] = $show_status;
					$queryRecords[$recKey]['show_keterangan'] = '<table style="margin: 0;">'.$created_user.$keterangan_status.$created_at.$keterangan_lampiran.'</table>';
					$queryRecords[$recKey]['show_keterangan'] = $queryRecords[$recKey]['show_keterangan'] == '' ? '-' : $queryRecords[$recKey]['show_keterangan'];
				}

				$json_data = array(
					"draw"            => intval( $params['draw'] ),   
					"recordsTotal"    => intval( $totalRecords ),  
					"recordsFiltered" => intval($totalRecords),
					"data"            => $queryRecords
				);

				die(json_encode($json_data));
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_data_kategori_ssh($cek_return=false){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'results'	=> array(),
			'pagination'=> array(
			    "more" => false
			)
		);

		$table_content = '<option value="">Pilih Kategori</option>';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$where = '';
					if(!empty($_POST['search'])){
						$_POST['search'] = '%'.$_POST['search'].'%';
						$where = $wpdb->prepare('
							AND (
								tipe_kelompok LIKE %s
								OR kode_kategori LIKE %s
								OR uraian_kategori LIKE %s
							)
						', $_POST['search'], $_POST['search'], $_POST['search']);
					}

					$data_kategori = $wpdb->get_results($wpdb->prepare("
						SELECT 
							* 
						FROM data_kelompok_satuan_harga 
						WHERE tahun_anggaran = %d
							$where
						LIMIT %d, 20",
						$tahun_anggaran,
						$_POST['page']
					), ARRAY_A);
					$return['sql'] = $wpdb->last_query;

					foreach ($data_kategori as $key => $value) {
			    		$return['results'][] = array(
			    			'id' => $value['id_kategori'],
			    			'text' => $value['tipe_kelompok'].' '.$value['kode_kategori'].' '.$value['uraian_kategori']
			    		);
			    	}

					if(count($return['results']) > 0){
						$return['pagination']['more'] = true;
					}
			}else{
				$return['status'] = 'error';
				$return['message'] ='Api Key tidak sesuai!';
			}
		}else{
			$return['status'] = 'error';
			$return['message'] ='Format tidak sesuai!';
		}
		if($cek_return){
			return $return;
		}else{
			die(json_encode($return));
		}
	}

	public function get_data_satuan_ssh(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$table_content = '<option value="">Pilih Satuan</option>';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$data_satuan = $wpdb->get_results($wpdb->prepare('SELECT id_satuan,nama_satuan FROM data_satuan WHERE tahun_anggaran = %s',$tahun_anggaran), ARRAY_A);
			    	$no = 0;
			    	foreach ($data_satuan as $key => $value) {
			    		$no++;
			    		$table_content .= '<option value="'.$value['nama_satuan'].'">'.$value['nama_satuan'].'</option>';
			    	}

					$return = array(
						'status' => 'success',
						'table_content' => $table_content
					);
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_data_akun_ssh($cek_return=false){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'results'	=> array(),
			'pagination'=> array(
			    "more" => false
			)
		);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					$tahun_anggaran = $_POST['tahun_anggaran'];

					$filter = array();
					if(!empty($_POST['id_standar_harga'])){
						$id_standar_harga = $_POST['id_standar_harga'];
						$data_id_ssh_usulan = $wpdb->get_results($wpdb->prepare('SELECT kode_standar_harga_sipd FROM data_ssh_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);
						$data_akun_ssh_usulan = $wpdb->get_results($wpdb->prepare('SELECT id,id_akun,nama_akun FROM data_ssh_rek_belanja_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);
						if(!empty($data_id_ssh_usulan[0]['kode_standar_harga_sipd'])){
							$data_id_ssh_existing = $wpdb->get_results($wpdb->prepare('SELECT id_standar_harga FROM data_ssh WHERE kode_standar_harga = %s', $data_id_ssh_usulan[0]['kode_standar_harga_sipd']), ARRAY_A);
							$data_akun_ssh_existing_sipd = $wpdb->get_results($wpdb->prepare('SELECT id,id_akun,nama_akun FROM data_ssh_rek_belanja WHERE id_standar_harga = %d',$data_id_ssh_existing[0]['id_standar_harga']), ARRAY_A);
						}
						$filter_all = array_merge($data_akun_ssh_usulan, $data_akun_ssh_existing_sipd);
						foreach($filter_all as $akun){
							$filter[] = $akun['id_akun'];
						}
					}

					$where = '';
					if(!empty($filter)){
						$where .= ' AND id_akun NOT IN ('.implode(',', $filter).')';
					}
					if(!empty($_POST['search'])){
						$_POST['search'] = '%'.$_POST['search'].'%';
						$where .= $wpdb->prepare('
							AND (
								kode_akun LIKE %s
								OR nama_akun LIKE %s
							)
						', $_POST['search'], $_POST['search']);
					}

					$data_akun = $wpdb->get_results($wpdb->prepare("
						SELECT 
							id_akun,
							kode_akun,
							nama_akun 
						FROM data_akun 
						WHERE set_input = %d
							AND tahun_anggaran = %d
							AND kode_akun LIKE '5.%'
							$where
						LIMIT %d, 20",
						1,
						$tahun_anggaran,
						$_POST['page']
					), ARRAY_A);
					$return['sql'] = $wpdb->last_query;

					foreach ($data_akun as $key => $value) {
			    		$return['results'][] = array(
			    			'id' => $value['id_akun'],
			    			'text' => $value['kode_akun'].' '.$value['nama_akun']
			    		);
			    	}

					if(count($return['results']) > 0){
						$return['pagination']['more'] = true;
					}
			}else{
				$return['status'] = 'error';
				$return['message'] ='Api Key tidak sesuai!';
			}
		}else{
			$return['status'] = 'error';
			$return['message'] ='Format tidak sesuai!';
		}
		if($cek_return){
			return $return;
		}else{
			die(json_encode($return));
		}
	}

	/** Submit data usulan SSH */
	public function submit_usulan_ssh(){
		global $wpdb;
		$user_id = um_user( 'ID' );
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['kategori']) && !empty($_POST['nama_komponen']) && !empty($_POST['spesifikasi']) && !empty($_POST['satuan']) && !empty($_POST['harga_satuan']) && !empty($_POST['akun']) && !empty($_POST['keterangan_lampiran'])){
					$kategori =trim(htmlspecialchars($_POST['kategori']));
					$nama_standar_harga = trim(htmlspecialchars($_POST['nama_komponen']));
					$spek = trim(htmlspecialchars($_POST['spesifikasi']));
					$satuan = trim(htmlspecialchars($_POST['satuan']));
					$harga = trim(htmlspecialchars($_POST['harga_satuan']));
					$akun = $_POST['akun'];
					$tahun_anggaran = trim(htmlspecialchars($_POST['tahun_anggaran']));
					$keterangan_lampiran = trim(htmlspecialchars($_POST['keterangan_lampiran']));
					$jenis_produk = trim(htmlspecialchars($_POST['jenis_produk']));
					$jenis_produk = ($jenis_produk == 0 || $jenis_produk == 1) ? $jenis_produk : NULL;
					$tkdn = trim(htmlspecialchars($_POST['tkdn']));
					$tkdn = ($tkdn >= 0) ? $tkdn : NULL;
					
					$data_kategori = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_kelompok_satuan_harga WHERE id_kategori = %d",$kategori), ARRAY_A);

					$data_akun = array();
					foreach($akun as $v_akun){
						$data_akun[$v_akun] = $wpdb->get_results($wpdb->prepare("SELECT id_akun,kode_akun,nama_akun FROM data_akun WHERE id_akun = %d",$v_akun), ARRAY_A);
					}

					$date_now = date("Y-m-d H:i:s");

					//avoid double data ssh
					$data_avoid = $wpdb->get_results($wpdb->prepare("
						SELECT 
							id 
						FROM data_ssh
						WHERE 
							nama_standar_harga = %s AND
							satuan = %s AND
							spek = %s AND
							harga = %s AND
							kode_kel_standar_harga = %s",
						$nama_standar_harga,
						$satuan,
						$spek,
						$harga,
						$data_kategori[0]['kode_kategori']
					), ARRAY_A);

					//avoid double data ssh usulan
					$data_avoid_usulan = $wpdb->get_results($wpdb->prepare("
						SELECT 
							id 
						FROM data_ssh_usulan 
						WHERE 
							nama_standar_harga = %s AND
							satuan = %s AND
							spek = %s AND
							harga = %s AND
							kode_kel_standar_harga = %s",
						$nama_standar_harga,
						$satuan,
						$spek,
						$harga,
						$data_kategori[0]['kode_kategori']
					), ARRAY_A);

					if(!empty($data_avoid) || !empty($data_avoid_usulan)){
						$data_avoid = !empty($data_avoid) ? $data_avoid : $data_avoid_usulan;
						$return = array(
							'status' => 'error',
							'message'	=> 'Standar Harga Sudah Ada!',
							'opsi_ssh' => $data_avoid,
						);

						die(json_encode($return));
					}

					$last_kode_standar_harga = $wpdb->get_results($wpdb->prepare("SELECT id_standar_harga,kode_standar_harga FROM `data_ssh_usulan` WHERE kode_standar_harga=(SELECT MAX(kode_standar_harga) FROM `data_ssh_usulan` WHERE kode_kel_standar_harga = %s)",$data_kategori[0]['kode_kategori']), ARRAY_A);
					$last_kode_standar_harga = (empty($last_kode_standar_harga[0]['kode_standar_harga'])) ? "0" : explode(".",$last_kode_standar_harga[0]['kode_standar_harga']);
					$last_kode_standar_harga = (int) end($last_kode_standar_harga);
					$last_kode_standar_harga = $last_kode_standar_harga+1;
					$last_kode_standar_harga = sprintf("%05d",$last_kode_standar_harga);
					$last_kode_standar_harga = $data_kategori[0]['kode_kategori'].'.'.$last_kode_standar_harga;

					$id_standar_harga = $wpdb->get_results("SELECT id_standar_harga FROM `data_ssh_usulan` WHERE id_standar_harga=(SELECT MAX(id_standar_harga) FROM `data_ssh_usulan`) AND tahun_anggaran=2022", ARRAY_A);
					$id_standar_harga = !empty($id_standar_harga) ? $id_standar_harga[0]['id_standar_harga'] + 1 : 1;

					//insert data usulan ssh
					$opsi_ssh = array(
						'id_standar_harga' => $id_standar_harga,
						'kode_standar_harga' => $last_kode_standar_harga,
						'nama_standar_harga' => $nama_standar_harga,
						'satuan' => $satuan,
						'spek' => $spek,
						'created_at' => $date_now,
						'created_user' => $user_id,
						'kelompok' => 1,
						'harga' => $harga,
						'kode_kel_standar_harga' => $data_kategori[0]['kode_kategori'],
						'nama_kel_standar_harga' => $data_kategori[0]['uraian_kategori'],
						'tahun_anggaran' => $tahun_anggaran,
						'status' => 'waiting',
						'keterangan_lampiran' => $keterangan_lampiran,
						'status_jenis_usulan' => 'tambah_baru',
						'jenis_produk' => $jenis_produk,
						'tkdn' => $tkdn
					);

					$wpdb->insert('data_ssh_usulan',$opsi_ssh);

					foreach($akun as $v_akun){
						$opsi_akun[$v_akun] = array(
							'id_akun' => $data_akun[$v_akun][0]['id_akun'],
							'kode_akun' => $data_akun[$v_akun][0]['kode_akun'],
							'nama_akun' => $data_akun[$v_akun][0]['kode_akun'].' '.$data_akun[$v_akun][0]['nama_akun'],
							'id_standar_harga' => $id_standar_harga,
							'tahun_anggaran' => $tahun_anggaran,
						);
		
						$wpdb->insert('data_ssh_rek_belanja_usulan',$opsi_akun[$v_akun]);
					}

					$return = array(
						'status' => 'success',
						'message'	=> 'Berhasil!',
						'opsi_ssh' => $opsi_ssh,
					);
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_data_nama_ssh(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					$tahun_anggaran = $_POST['tahun_anggaran'];
					
					$data_nama_ssh = $wpdb->get_results($wpdb->prepare('SELECT id_standar_harga, kode_standar_harga, nama_standar_harga FROM data_ssh_usulan WHERE tahun_anggaran = %d',$tahun_anggaran), ARRAY_A);

				    ksort($data_nama_ssh);
			    	$no = 0;
			    	foreach ($data_nama_ssh as $key => $value) {
			    		$no++;
			    		$table_content .= '
							<option value="'.$value['id_standar_harga'].'">'.$value['nama_standar_harga'].'</option>
			    		';
			    	}

					$return = array(
						'status' => 'success',
						'table_content' => $table_content
					);
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_data_usulan_ssh_by_komponen(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					$id_standar_harga = $_POST['id_standar_harga'];
					
					$data_ssh_by_id = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_ssh WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);

					$data_ssh_akun_by_id = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_ssh_rek_belanja WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);
					
					if(empty($data_ssh_by_id)){
						$data_ssh_by_id = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_ssh_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);
						$data_ssh_akun_by_id = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_ssh_rek_belanja_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);
					}

				    ksort($data_ssh_by_id);
			    	$no = 0;

					$table_content = '';
					foreach($data_ssh_akun_by_id as $data_akun){
						$table_content .= $data_akun['nama_akun']."&#13;&#10;";
					}

					$return = array(
						'status' => 'success',
						'data_ssh_usulan_by_id' => $data_ssh_by_id[0],
						'table_content' => $table_content,
					);
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function data_ssh_sipd($atts){
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-data-ssh.php';
	}

	public function jadwal_renja($atts){
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}

		$tipe_perencanaan = 'renja';

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-setting-penjadwalan.php';
	}

	public function jadwal_rpjm($atts){
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-setting-penjadwalan-rpjm.php';
	}

	public function jadwal_renstra($atts){
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-setting-penjadwalan-renstra.php';
	}
	
	public function jadwal_rpjpd($atts){
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-setting-penjadwalan-rpjpd.php';
	}

	public function jadwal_rpd($atts){
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-setting-penjadwalan-rpd.php';
	}

	public function get_data_ssh_sipd(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$params = $columns = $totalRecords = $data = array();
				$params = $_REQUEST;
				$columns = array( 
					0 => 'id_standar_harga',
					1 => 'kode_standar_harga', 
					2 => 'nama_standar_harga',
					3 => 'spek',
					4 => 'satuan',
					5 => 'harga',
					6 => 'kelompok'
				);
				$where = $sqlTot = $sqlRec = "";

				// check search value exist
				if( !empty($params['search']['value']) ) {
					$where .=" AND ( kode_standar_harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");    
					$where .=" OR nama_standar_harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR spek LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR id_standar_harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%")." )";
				}

				// getting total number records without any search
				$sql_tot = "SELECT count(*) as jml FROM `data_ssh`";
				$sql = "SELECT ".implode(', ', $columns)." FROM `data_ssh`";
				$where_first = " WHERE id_standar_harga IS NOT NULL AND tahun_anggaran=".$wpdb->prepare('%d', $params['tahun_anggaran']);
				$sqlTot .= $sql_tot.$where_first;
				$sqlRec .= $sql.$where_first;
				if(isset($where) && $where != '') {
					$sqlTot .= $where;
					$sqlRec .= $where;
				}

			 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";

				$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				$totalRecords = $queryTot[0]['jml'];
				$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

				foreach($queryRecords as $recKey => $recVal){
					$iconPlus = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>';
					$queryRecords[$recKey]['aksi'] = '<a href="#" onclick="return data_akun_ssh_sipd(\''.$recVal['id_standar_harga'].'\');" title="Melihat Data Akun">'.$iconPlus.'</a>';
					$tipe_kelompok = ['SSH','HSPK','ASB','SBU'];
					if(!empty($recVal['kelompok'])){
						$queryRecords[$recKey]['show_kelompok'] = $tipe_kelompok[$recVal['kelompok']-1];
					}else{
						$queryRecords[$recKey]['show_kelompok'] = '-';
					}
				}

				$json_data = array(
					"draw"            => intval( $params['draw'] ),   
					"recordsTotal"    => intval( $totalRecords ),  
					"recordsFiltered" => intval($totalRecords),
					"data"            => $queryRecords
				);

				die(json_encode($json_data));
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_komponen_and_id_kel_ssh($cek_return=false){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'results'	=> array(),
			'pagination'=> array(
			    "more" => false
			)
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$where = '';
					if(!empty($_POST['search'])){
						$_POST['search'] = '%'.$_POST['search'].'%';
						$where = $wpdb->prepare('
							AND (
								kode_standar_harga LIKE %s
								OR nama_standar_harga LIKE %s
							)
						', $_POST['search'], $_POST['search']);
					}

					$data_nama_ssh = $wpdb->get_results($wpdb->prepare("
						SELECT 
							id_standar_harga,
							kode_standar_harga, 
							nama_standar_harga 
						FROM data_ssh 
						WHERE tahun_anggaran = %d
							$where
						LIMIT %d, 20",
						$tahun_anggaran,
						$_POST['page']
					), ARRAY_A);
			    	$return['sql'] = $wpdb->last_query;

					$data_nama_ssh_usulan = $wpdb->get_results($wpdb->prepare("
						SELECT 
							id_standar_harga,
							kode_standar_harga, 
							nama_standar_harga 
						FROM data_ssh_usulan 
						WHERE tahun_anggaran = %d
							$where
						LIMIT %d, 20",
						$tahun_anggaran,
						$_POST['page']
					), ARRAY_A);
			    	$return['sql_usulan'] = $wpdb->last_query;

			    	foreach ($data_nama_ssh_usulan as $key => $value) {
			    		$return['results'][] = array(
			    			'id' => $value['id_standar_harga'],
			    			'text' => 'Usulan '.$value['kode_standar_harga'].' '.$value['nama_standar_harga']
			    		);
			    	}
			    	foreach ($data_nama_ssh as $key => $value) {
			    		$return['results'][] = array(
			    			'id' => $value['id_standar_harga'],
			    			'text' => $value['kode_standar_harga'].' '.$value['nama_standar_harga']
			    		);
			    	}

					if(count($return['results']) > 0){
						$return['pagination']['more'] = true;
					}
			}else{
				$return['status'] = 'error';
				$return['message'] ='Api Key tidak sesuai!';
			}
		}else{
			$return['status'] = 'error';
			$return['message'] ='Format tidak sesuai!';
		}
		if($cek_return){
			return $return;
		}else{
			die(json_encode($return));
		}
	}

	/** Submit data tambah harga usulan SSH */
	public function submit_tambah_harga_ssh(){
		global $wpdb;
		$user_id = um_user( 'ID' );
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['id_standar_harga']) && !empty($_POST['harga_satuan']) && !empty($_POST['keterangan_lampiran'])){
					$id_standar_harga = trim(htmlspecialchars($_POST['id_standar_harga']));
					$tahun_anggaran = trim(htmlspecialchars($_POST['tahun_anggaran']));
					$harga_satuan = trim(htmlspecialchars($_POST['harga_satuan']));
					$keterangan_lampiran = trim(htmlspecialchars($_POST['keterangan_lampiran']));
					
					$data_old_ssh = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_ssh WHERE id_standar_harga = %d",$id_standar_harga), ARRAY_A);
					$data_old_ssh_akun = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_ssh_rek_belanja WHERE id_standar_harga = %d",$id_standar_harga), ARRAY_A);
					$kode_standar_harga_sipd = $data_old_ssh[0]['kode_standar_harga'];

					if(empty($data_old_ssh)){
						$data_old_ssh = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_ssh_usulan WHERE id_standar_harga = %d",$id_standar_harga), ARRAY_A);
						$data_old_ssh_akun = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_ssh_rek_belanja_usulan WHERE id_standar_harga = %d",$id_standar_harga), ARRAY_A);
						$kode_standar_harga_sipd = NULL;
					}

					if(!empty($data_old_ssh_akun)){
						foreach($data_old_ssh_akun as $v_a_akun){
							$data_akun[$v_a_akun['id_akun']] = $wpdb->get_results($wpdb->prepare("SELECT id_akun,kode_akun,nama_akun FROM data_akun WHERE id_akun = %d",$v_a_akun['id_akun']), ARRAY_A);
						}
					}
	
					$date_now = date("Y-m-d H:i:s");
	
					// //avoid double ssh
					$data_avoid = $wpdb->get_results($wpdb->prepare("
									SELECT 
										id 
									FROM data_ssh
									WHERE 
										nama_standar_harga = %s AND
										satuan = %s AND
										spek = %s AND
										harga = %s AND
										kode_kel_standar_harga = %s",
									$data_old_ssh[0]['nama_standar_harga'],
									$data_old_ssh[0]['satuan'],
									$data_old_ssh[0]['spek'],
									$harga_satuan,
									$data_old_ssh[0]['kode_kel_standar_harga']
								), ARRAY_A);

					$data_avoid_usulan = $wpdb->get_results($wpdb->prepare("
									SELECT 
										id 
									FROM data_ssh_usulan 
									WHERE 
										nama_standar_harga = %s AND
										satuan = %s AND
										spek = %s AND
										harga = %s AND
										kode_kel_standar_harga = %s",
									$data_old_ssh[0]['nama_standar_harga'],
									$data_old_ssh[0]['satuan'],
									$data_old_ssh[0]['spek'],
									$harga_satuan,
									$data_old_ssh[0]['kode_kel_standar_harga']
								), ARRAY_A);

					if(!empty($data_avoid) || !empty($data_avoid_usulan)){
						$data_avoid = !empty($data_avoid) ? $data_avoid : $data_avoid_usulan;
						$return = array(
							'status' => 'error',
							'message'	=> 'Standar Harga Sudah Ada!',
							'opsi_ssh' => $data_avoid,
						);
	
						die(json_encode($return));
					}
	
					$last_kode_standar_harga = $wpdb->get_results($wpdb->prepare("SELECT id_standar_harga,kode_standar_harga FROM `data_ssh_usulan` WHERE kode_standar_harga=(SELECT MAX(kode_standar_harga) FROM `data_ssh_usulan` WHERE kode_kel_standar_harga = %s)",$data_old_ssh[0]['kode_kel_standar_harga']), ARRAY_A);
					$last_kode_standar_harga = (empty($last_kode_standar_harga[0]['kode_standar_harga'])) ? "0" : explode(".",$last_kode_standar_harga[0]['kode_standar_harga']);
					$last_kode_standar_harga = (int) end($last_kode_standar_harga);
					$last_kode_standar_harga = $last_kode_standar_harga+1;
					$last_kode_standar_harga = sprintf("%05d",$last_kode_standar_harga);
					$last_kode_standar_harga = $data_old_ssh[0]['kode_kel_standar_harga'].'.'.$last_kode_standar_harga;
	
					$id_standar_harga = $wpdb->get_results($wpdb->prepare("SELECT id_standar_harga FROM `data_ssh_usulan` WHERE id_standar_harga=(SELECT MAX(id_standar_harga) FROM `data_ssh_usulan`) AND tahun_anggaran=%d",$tahun_anggaran), ARRAY_A);
					$id_standar_harga = !empty($id_standar_harga) ? $id_standar_harga[0]['id_standar_harga'] + 1 : 1;
	
					//insert data usulan ssh
					$opsi_ssh = array(
						'id_standar_harga' => $id_standar_harga,
						'kode_standar_harga' => $last_kode_standar_harga,
						'nama_standar_harga' => $data_old_ssh[0]['nama_standar_harga'],
						'satuan' => $data_old_ssh[0]['satuan'],
						'spek' => $data_old_ssh[0]['spek'],
						'created_at' => $date_now,
						'created_user' => $user_id,
						'kelompok' => 1,
						'harga' => $harga_satuan,
						'kode_kel_standar_harga' => $data_old_ssh[0]['kode_kel_standar_harga'],
						'nama_kel_standar_harga' => $data_old_ssh[0]['nama_kel_standar_harga'],
						'tahun_anggaran' => $tahun_anggaran,
						'status' => 'waiting',
						'keterangan_lampiran' => $keterangan_lampiran,
						'kode_standar_harga_sipd' => $kode_standar_harga_sipd,
						'status_jenis_usulan' => 'tambah_harga',
						'jenis_produk' => $data_old_ssh[0]['jenis_produk'],
						'tkdn'	=> $data_old_ssh[0]['tkdn']
					);
	
					$wpdb->insert('data_ssh_usulan',$opsi_ssh);
	
					$return = array(
						'status' => 'success',
						'message'	=> 'Berhasil!',
						'opsi_ssh' => $opsi_ssh,
					);
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua, tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Tombol tambah usulan rekening */
	public function submit_tambah_akun_ssh(){
		global $wpdb;
		$user_id = um_user( 'ID' );
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['id_standar_harga']) && !empty($_POST['new_akun'])){
					$id_standar_harga = trim(htmlspecialchars($_POST['id_standar_harga']));
					$tahun_anggaran = trim(htmlspecialchars($_POST['tahun_anggaran']));
					$new_akun = $_POST['new_akun'];
					
					$data_old_ssh = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_ssh WHERE id_standar_harga = %d",$id_standar_harga), ARRAY_A);
					$kode_standar_harga_sipd = $data_old_ssh[0]['kode_standar_harga'];

					if(empty($data_old_ssh)){
						$data_old_ssh = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_ssh_usulan WHERE id_standar_harga = %d",$id_standar_harga), ARRAY_A);
						$kode_standar_harga_sipd = NULL;
					}
					
					foreach($new_akun as $v_new_akun){
						$data_akun[$v_new_akun] = $wpdb->get_results($wpdb->prepare("SELECT id_akun,kode_akun,nama_akun FROM data_akun WHERE id_akun = %d",$v_new_akun), ARRAY_A);
					}
	
					$date_now = date("Y-m-d H:i:s");

					//insert data ke usulan ssh jika komponen berasal dari data existing SIPD
					if(!empty($kode_standar_harga_sipd)){
						$last_kode_standar_harga = $wpdb->get_results($wpdb->prepare("SELECT id_standar_harga,kode_standar_harga FROM `data_ssh_usulan` WHERE kode_standar_harga=(SELECT MAX(kode_standar_harga) FROM `data_ssh_usulan` WHERE kode_kel_standar_harga = %s)",$data_old_ssh[0]['kode_kel_standar_harga']), ARRAY_A);
						$last_kode_standar_harga = (empty($last_kode_standar_harga[0]['kode_standar_harga'])) ? "0" : explode(".",$last_kode_standar_harga[0]['kode_standar_harga']);
						$last_kode_standar_harga = (int) end($last_kode_standar_harga);
						$last_kode_standar_harga = $last_kode_standar_harga+1;
						$last_kode_standar_harga = sprintf("%05d",$last_kode_standar_harga);
						$last_kode_standar_harga = $data_old_ssh[0]['kode_kel_standar_harga'].'.'.$last_kode_standar_harga;
		
						$id_standar_harga = $wpdb->get_results($wpdb->prepare("SELECT id_standar_harga FROM `data_ssh_usulan` WHERE id_standar_harga=(SELECT MAX(id_standar_harga) FROM `data_ssh_usulan`) AND tahun_anggaran=%d",$tahun_anggaran), ARRAY_A);
						$id_standar_harga = !empty($id_standar_harga) ? $id_standar_harga[0]['id_standar_harga'] + 1 : 1;

						$opsi_ssh = array(
							'id_standar_harga' => $id_standar_harga,
							'kode_standar_harga' => $last_kode_standar_harga,
							'nama_standar_harga' => $data_old_ssh[0]['nama_standar_harga'],
							'satuan' => $data_old_ssh[0]['satuan'],
							'spek' => $data_old_ssh[0]['spek'],
							'created_at' => $date_now,
							'created_user' => $user_id,
							'kelompok' => 1,
							'harga' => $data_old_ssh[0]['harga'],
							'kode_kel_standar_harga' => $data_old_ssh[0]['kode_kel_standar_harga'],
							'nama_kel_standar_harga' => $data_old_ssh[0]['nama_kel_standar_harga'],
							'tahun_anggaran' => $tahun_anggaran,
							'status' => 'waiting',
							'keterangan_lampiran' => NULL,
							'kode_standar_harga_sipd' => $kode_standar_harga_sipd,
							'status_jenis_usulan' => 'tambah_akun',
							'jenis_produk'	=> $data_old_ssh[0]['jenis_produk'],
							'tkdn'	=> $data_old_ssh[0]['tkdn']
						);
		
						$wpdb->insert('data_ssh_usulan',$opsi_ssh);
					}

					/** Insert usulan rek akun */
					foreach($data_akun as $k_akun => $v_akun){
						$opsi_akun[$k_akun] = array(
							'id_akun' => $v_akun[0]['id_akun'],
							'kode_akun' => $v_akun[0]['kode_akun'],
							'nama_akun' => $v_akun[0]['kode_akun'].' '.$v_akun[0]['nama_akun'],
							'id_standar_harga' => $id_standar_harga,
							'tahun_anggaran' => $tahun_anggaran,
						);
		
						$wpdb->insert('data_ssh_rek_belanja_usulan',$opsi_akun[$k_akun]);
					}
	
					$return = array(
						'status' => 'success',
						'message'	=> 'Berhasil!',
						'data_akun' => $data_akun,
					);
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua, tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit verifikasi usulan SSH */
	public function submit_verify_ssh(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if($_POST['verify_ssh'] != '' && !empty($_POST['id_ssh_verify_ssh'])){
					if(in_array("administrator", $user_meta->roles)){
						if(($_POST['verify_ssh'] == 0) && empty($_POST['reason_verify_ssh'])){
							$return = array(
								'status' => 'error',
								'message'	=> 'Harap diisi semua, tidak boleh ada yang kosong!'
							);

							die(json_encode($return));
						}

						$verify_ssh = trim(htmlspecialchars($_POST['verify_ssh']));
						$reason_verify_ssh = trim(htmlspecialchars($_POST['reason_verify_ssh']));
						$id_ssh_verify_ssh = trim(htmlspecialchars($_POST['id_ssh_verify_ssh']));

						$data_ssh = $wpdb->get_results($wpdb->prepare("SELECT status_upload_sipd FROM data_ssh_usulan WHERE id_standar_harga = %d",$id_ssh_verify_ssh), ARRAY_A);
		
						if($data_ssh[0]['status_upload_sipd'] != 1){
							$date_now = date("Y-m-d H:i:s");
			
							$status_usulan_ssh = ($verify_ssh) ? 'approved' : 'rejected';
			
							$keterangan_status = (!empty($reason_verify_ssh)) ? $reason_verify_ssh : NULL;
			
							//update status data usulan ssh
							$opsi_ssh = array(
								'status' => $status_usulan_ssh,
								'keterangan_status' => $keterangan_status,
								'update_at' => $date_now,
							);
			
							$where_ssh = array(
								'id_standar_harga' => $id_ssh_verify_ssh
							);
			
							$wpdb->update('data_ssh_usulan',$opsi_ssh,$where_ssh);
			
							$return = array(
								'status' => 'success',
								'message'	=> 'Berhasil!',
							);
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "User tidak diijinkan!\nData sudah diunggah di SIPD"
							);	
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> 'User tidak diijinkan!'
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua, tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function data_halaman_menu_ssh($atts){
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);
		if(empty($user_meta->roles)){
			echo 'User ini tidak dapat akses sama sekali :)';
		}else if(in_array("administrator", $user_meta->roles) || in_array("PLT", $user_meta->roles)){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-data-halaman-ssh.php';
		}
	}

	public function get_data_ssh_sipd_by_id(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					$id_standar_harga = $_POST['id_standar_harga'];
					
					$data_id_ssh = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_ssh WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);

					$data_akun_ssh = $wpdb->get_results($wpdb->prepare('SELECT id_akun,nama_akun FROM data_ssh_rek_belanja WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);


				    ksort($data_id_ssh);

					$return = array(
						'status' => 'success',
						'data' => $data_id_ssh[0],
						'data_akun' => $data_akun_ssh,
					);
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_data_ssh_usulan_by_id(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					$id_standar_harga = $_POST['id_standar_harga'];
					$tahun_anggaran = $_POST['tahun_anggaran'];
					
					$data_id_ssh_usulan = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_ssh_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);

					$data_akun_ssh_usulan = $wpdb->get_results($wpdb->prepare('SELECT id,id_akun,nama_akun FROM data_ssh_rek_belanja_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);

					$data_kel_standar_harga_by_id = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_kelompok_satuan_harga WHERE kode_kategori LIKE %s AND tahun_anggaran = %s',$data_id_ssh_usulan[0]['kode_kel_standar_harga'].'%',$tahun_anggaran), ARRAY_A);
				    
					$table_content_akun = '';
					if(!empty($data_id_ssh_usulan[0]['kode_standar_harga_sipd'])){
						$data_id_ssh_existing = $wpdb->get_results($wpdb->prepare('SELECT id_standar_harga FROM data_ssh WHERE kode_standar_harga = %s', $data_id_ssh_usulan[0]['kode_standar_harga_sipd']), ARRAY_A);
						$data_akun_ssh_existing_sipd = $wpdb->get_results($wpdb->prepare('SELECT id,id_akun,nama_akun FROM data_ssh_rek_belanja WHERE id_standar_harga = %d',$data_id_ssh_existing[0]['id_standar_harga']), ARRAY_A);
						foreach($data_akun_ssh_existing_sipd as $data_akun){
							$table_content_akun .= $data_akun['nama_akun']."&#13;&#10;";
						}
					}
					$return = array(
						'status' 						=> 'success',
						'data' 							=> $data_id_ssh_usulan[0],
						'data_akun_usulan'				=> $data_akun_ssh_usulan,
						'data_kel_standar_harga_by_id'	=> $data_kel_standar_harga_by_id[0],
						'table_content_akun'			=> $table_content_akun
					);
			}else{
				$return = array(
					'status'	=> 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status'	=> 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function submit_add_new_akun_ssh_usulan(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$akun = $_POST['data_akun'];
				$id_standar_harga = $_POST['id_standar_harga'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$data_this_id_ssh = $wpdb->get_results($wpdb->prepare('SELECT id_standar_harga,kode_kel_standar_harga,kode_standar_harga,status FROM data_ssh_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);
				
				if($data_this_id_ssh[0]['status'] == 'waiting' || in_array("administrator", $user_meta->roles)){
					foreach($akun as $v_akun){
						$data_akun[$v_akun] = $wpdb->get_results($wpdb->prepare("SELECT id_akun,kode_akun,nama_akun FROM data_akun WHERE id_akun = %d",$v_akun), ARRAY_A);
					}
	
					$date_now = date("Y-m-d H:i:s");
					//insert data new akun usulan ssh
	
					foreach($akun as $v_akun){
						$opsi_akun[$v_akun] = array(
							'id_akun' => $data_akun[$v_akun][0]['id_akun'],
							'kode_akun' => $data_akun[$v_akun][0]['kode_akun'],
							'nama_akun' => $data_akun[$v_akun][0]['kode_akun'].' '.$data_akun[$v_akun][0]['nama_akun'],
							'id_standar_harga' => $id_standar_harga,
							'tahun_anggaran' => $tahun_anggaran,
						);
		
						$wpdb->insert('data_ssh_rek_belanja_usulan',$opsi_akun[$v_akun]);
					}
	
					$return = array(
						'status' => 'success',
						'message'	=> 'Berhasil!',
					);
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> "User tidak diijinkan!\nData sudah dalam tahap verifikasi",
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_data_ssh_analisis(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$params = $columns = $totalRecords = $data = array();
				$params = $_REQUEST;
				$columns = array( 
					0 =>'nama_komponen',
					1 =>'spek_komponen', 
					2 => 'harga_satuan',
					3 => 'satuan',
					4 => 'SUM(volume) as volume',
					5 => 'SUM(total_harga) as total',
					6 => 'kode_bl'
				);
				$where = $sqlTot = $sqlRec = "";

				// check search value exist
				if( !empty($params['search']['value']) ) {
					$where .=" AND ( nama_komponen LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");    
					$where .=" OR spek_komponen LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR harga_satuan LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR satuan LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%").")";
				}

				$current_user = wp_get_current_user();
				$data_ssh = $wpdb->get_results("SELECT id_unit FROM `data_unit` where nipkepala=".$wpdb->prepare('%d', $current_user->user_login),ARRAY_A);
				
				// mengambil data per skpd
				$sqlKodeBl = '';
				if(!empty($data_ssh[0]['id_unit']) && $current_user->roles != 'administrator'){
					$data_bl = array();
					$sql = "SELECT kode_bl FROM data_sub_keg_bl WHERE id_skpd = ".$wpdb->prepare('%s', $data_ssh[0]['id_unit'])." GROUP BY id_skpd,kode_bl";
					$run = $wpdb->get_results($sql,ARRAY_A);
					foreach ($run as $value) {
						array_push($data_bl,strval($value['kode_bl']));
					}
					$sqlKodeBl = " and kode_bl in ('".implode("','", $data_bl)."')";
				}

				// getting total number records without any search
				$sql_tot = "SELECT count(*) as jml FROM `data_rka`";
				$sql = "SELECT ".implode(', ', $columns)." FROM `data_rka`";
				$where_first = " WHERE active=1 and tahun_anggaran=".$wpdb->prepare('%d', $params['tahun_anggaran']).$sqlKodeBl;
				$sqlTot .= $sql_tot.$where_first;
				$sqlRec .= $sql.$where_first;
				if(isset($where) && $where != '') {
					$sqlTot .= $where;
					$sqlRec .= $where;
				}

			 	$sqlRec .=  " GROUP by nama_komponen, spek_komponen, harga_satuan ORDER BY total DESC, ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";
				$sqlTot .=  " GROUP by nama_komponen, spek_komponen, harga_satuan";

				$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				$totalRecords = count($queryTot);
				$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

				$title = 'Laporan Data per Item SSH';
				$shortcode = '[laporan_per_item_ssh]';
				$update = false;
				$url_skpd = $this->generatePage($title, $params['tahun_anggaran'], $shortcode, $update);

				foreach($queryRecords as $key => $val){
					$queryRecords[$key]['link'] = '<a href="'.$url_skpd.'&nama_komponen='.$val['nama_komponen'].'&spek_komponen='.$val['spek_komponen'].'&harga_satuan='.$val['harga_satuan'].'&satuan='.$val['satuan'].'" target="_blank" style="text-decoration: none;">'.$val['nama_komponen'].'</a>';
				}

				$json_data = array(
					"draw"            => intval( $params['draw'] ),   
					"recordsTotal"    => intval( $totalRecords ),  
					"recordsFiltered" => intval($totalRecords),
					"data"            => $queryRecords
				);

				die(json_encode($json_data));
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_data_chart_ssh(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$current_user = wp_get_current_user();
				$data_ssh = $wpdb->get_results("SELECT id_unit FROM `data_unit` where nipkepala=".$wpdb->prepare('%d', $current_user->user_login),ARRAY_A);
				
				// mengambil data per skpd
				$sqlKodeBl = '';
				if(!empty($data_ssh[0]['id_unit']) && $current_user->roles != 'administrator'){
					$data_bl = array();
					$sql = "SELECT kode_bl FROM data_sub_keg_bl WHERE id_skpd = ".$wpdb->prepare('%s', $data_ssh[0]['id_unit'])." GROUP BY id_skpd,kode_bl";
					$run = $wpdb->get_results($sql,ARRAY_A);
					foreach ($run as $value) {
						array_push($data_bl,strval($value['kode_bl']));
					}
					$sqlKodeBl = " and kode_bl in ('".implode("','", $data_bl)."')";
				}

				$data_ssh = $wpdb->get_results("SELECT nama_komponen, spek_komponen, harga_satuan, satuan, volume, sum(total_harga) as total FROM `data_rka` where active=1 and tahun_anggaran=".$wpdb->prepare('%d', $tahun_anggaran).$sqlKodeBl." GROUP by nama_komponen, spek_komponen, harga_satuan order by total desc limit 20",ARRAY_A);

				$return = array(
					'status' => 'success',
					'data' => $data_ssh
				);
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_spd($cek_return = false){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil get SPD!',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$mapping_skpd = $this->get_id_skpd_fmis(false, $tahun_anggaran);
				$sql = $wpdb->prepare("SELECT * FROM ta_spd where tahun=%d order by tgl_spd ASC", $tahun_anggaran);
				$return['sql'] = $sql;
				$data_spd = $this->simda->CurlSimda(array(
					'query' => $sql,
					'debug' => 1
				));
				foreach($data_spd as $k => $spd){
					$kd_sub_unit = $spd->kd_urusan.'.'.$spd->kd_bidang.'.'.$spd->kd_unit.'.'.$spd->kd_sub;
					$data_spd[$k]->kd_sub_unit = $kd_sub_unit;
					if(!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])){
						$data_spd[$k]->skpd = $mapping_skpd['id_mapping_simda'][$kd_sub_unit];
					}
				}
				$return['data'] = $data_spd;
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		if($cek_return){
			return $return;
		}else{
			die(json_encode($return));
		}
	}

	public function get_spd_rinci($cek_return = false){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil get SPD rinci!',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare("
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
				foreach($data_spd as $k => $spd){
					$kd_urusan_asli = substr($spd->id_prog, 0, 1);
					$kd_bidang_asli = (int) substr($spd->id_prog, 1);
					$kd_keg_simda = $kd_urusan_asli.'.'.$kd_bidang_asli.'.'.$spd->kd_prog.'.'.$spd->kd_keg;
					if(empty($prog_keg[$kd_keg_simda])){
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
						if($mapping[0]->kd_program90 == 1){
							$kd_urusan_sipd = 'X';
							$kd_bidang_sipd = 'XX';
						}
						$kode_sub = $kd_urusan_sipd.'.'.$this->simda->CekNull($kd_bidang_sipd).'.'.$this->simda->CekNull($mapping[0]->kd_program90).'.'.$mapping[0]->kd_kegiatan90.'.'.$this->simda->CekNull($mapping[0]->kd_sub_kegiatan);
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
						if(!empty($program_mapping[$this->removeNewline($sub[0]['nama_program'])])){
							$prog_keg[$kd_keg_simda]['nama_program'] = $program_mapping[$this->removeNewline($sub[0]['nama_program'])];
						}
						if(!empty($keg_mapping[$this->removeNewline($sub[0]['nama_giat'])])){
							$prog_keg[$kd_keg_simda]['nama_giat'] = $keg_mapping[$this->removeNewline($sub[0]['nama_giat'])];
						}
						if(!empty($subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])])){
							$prog_keg[$kd_keg_simda]['nama_sub_giat'] = $subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])];
						}
					}

					$kode_akun = $spd->kd_rek90_1.'.'.$spd->kd_rek90_2.'.'.$spd->kd_rek90_3.'.'.$spd->kd_rek90_4.'.'.$spd->kd_rek90_5.'.'.$spd->kd_rek90_6;
					$prog_keg[$kd_keg_simda]['kode_akun'] = $kode_akun;
					if(!empty($rek_mapping[$kode_akun])){
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
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		if($cek_return){
			return $return;
		}else{
			die(json_encode($return));
		}
	}

	public function get_spp_rinci($cek_return = false){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil get SPP rinci!',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare("
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
				foreach($data_spd as $k => $spd){
					$kd_urusan_asli = substr($spd->id_prog, 0, 1);
					$kd_bidang_asli = (int) substr($spd->id_prog, 1);
					$kd_keg_simda = $kd_urusan_asli.'.'.$kd_bidang_asli.'.'.$spd->kd_prog.'.'.$spd->kd_keg;
					if(empty($prog_keg[$kd_keg_simda])){
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
						if($mapping[0]->kd_program90 == 1){
							$kd_urusan_sipd = 'X';
							$kd_bidang_sipd = 'XX';
						}
						$kode_sub = $kd_urusan_sipd.'.'.$this->simda->CekNull($kd_bidang_sipd).'.'.$this->simda->CekNull($mapping[0]->kd_program90).'.'.$mapping[0]->kd_kegiatan90.'.'.$this->simda->CekNull($mapping[0]->kd_sub_kegiatan);
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
						if(!empty($program_mapping[$this->removeNewline($sub[0]['nama_program'])])){
							$prog_keg[$kd_keg_simda]['nama_program'] = $program_mapping[$this->removeNewline($sub[0]['nama_program'])];
						}
						if(!empty($keg_mapping[$this->removeNewline($sub[0]['nama_giat'])])){
							$prog_keg[$kd_keg_simda]['nama_giat'] = $keg_mapping[$this->removeNewline($sub[0]['nama_giat'])];
						}
						if(!empty($subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])])){
							$prog_keg[$kd_keg_simda]['nama_sub_giat'] = $subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])];
						}
					}

					$kode_akun = $spd->kd_rek90_1.'.'.$spd->kd_rek90_2.'.'.$spd->kd_rek90_3.'.'.$spd->kd_rek90_4.'.'.$spd->kd_rek90_5.'.'.$spd->kd_rek90_6;
					$prog_keg[$kd_keg_simda]['kode_akun'] = $kode_akun;
					if(!empty($rek_mapping[$kode_akun])){
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
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		if($cek_return){
			return $return;
		}else{
			die(json_encode($return));
		}
	}

	public function get_sp2b_rinci($cek_return = false){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil get SP2B rinci!',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare("
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
				foreach($data_spd as $k => $spd){
					$kd_urusan_asli = substr($spd->id_prog, 0, 1);
					$kd_bidang_asli = (int) substr($spd->id_prog, 1);
					$kd_keg_simda = $kd_urusan_asli.'.'.$kd_bidang_asli.'.'.$spd->kd_prog.'.'.$spd->kd_keg;
					if(empty($prog_keg[$kd_keg_simda])){
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
						if($mapping[0]->kd_program90 == 1){
							$kd_urusan_sipd = 'X';
							$kd_bidang_sipd = 'XX';
						}
						$kode_sub = $kd_urusan_sipd.'.'.$this->simda->CekNull($kd_bidang_sipd).'.'.$this->simda->CekNull($mapping[0]->kd_program90).'.'.$mapping[0]->kd_kegiatan90.'.'.$this->simda->CekNull($mapping[0]->kd_sub_kegiatan);
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
						if(!empty($program_mapping[$this->removeNewline($sub[0]['nama_program'])])){
							$prog_keg[$kd_keg_simda]['nama_program'] = $program_mapping[$this->removeNewline($sub[0]['nama_program'])];
						}
						if(!empty($keg_mapping[$this->removeNewline($sub[0]['nama_giat'])])){
							$prog_keg[$kd_keg_simda]['nama_giat'] = $keg_mapping[$this->removeNewline($sub[0]['nama_giat'])];
						}
						if(!empty($subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])])){
							$prog_keg[$kd_keg_simda]['nama_sub_giat'] = $subkeg_mapping[$this->removeNewline($sub[0]['nama_sub_giat'])];
						}
					}

					$kode_akun = $spd->kd_rek90_1.'.'.$spd->kd_rek90_2.'.'.$spd->kd_rek90_3.'.'.$spd->kd_rek90_4.'.'.$spd->kd_rek90_5.'.'.$spd->kd_rek90_6;
					$prog_keg[$kd_keg_simda]['kode_akun'] = $kode_akun;
					if(!empty($rek_mapping[$kode_akun])){
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
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		if($cek_return){
			return $return;
		}else{
			die(json_encode($return));
		}
	}

	public function get_pegawai_simda(){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil get data Pegawai SIMDA!',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare("
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
				foreach($data_pegawai as $k => $pegawai){
					$kd_sub_unit = $pegawai->kd_urusan.'.'.$pegawai->kd_bidang.'.'.$pegawai->kd_unit.'.'.$pegawai->kd_sub;
					$data_pegawai[$k]->kd_sub_unit = $kd_sub_unit;
					if(!empty($mapping_skpd['id_mapping_simda'][$kd_sub_unit])){
						$data_pegawai[$k]->skpd = $mapping_skpd['id_mapping_simda'][$kd_sub_unit];
					}
				}
				$return['data'] = $data_pegawai;
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function cek_api_key(){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'api_key' => '',
			'message' => 'Berhasil cek lisensi!'
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$res = $this->simda->cek_lisensi(array(
					'api_key' => $_POST['api_key']
				));
				$return = array_merge($return, $res);
			}else{
				$return['status'] = 'error';
				$return['message']	= 'Api Key tidak sesuai!';
			}
		}else{
			$return['status'] = 'error';
			$return['message']	= 'Format tidak sesuai!';
		}
		die(json_encode($return));
	}

	public function get_data_ssh_analisis_skpd(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$params = $columns = $totalRecords = $data = array();
				$params = $_REQUEST;
				$columns = array( 
					0 =>'nama_komponen',
					1 =>'spek_komponen', 
					2 => 'harga_satuan',
					3 => 'satuan',
					4 => 'SUM(volume) as volume',
					5 => 'SUM(total_harga) as total'
				);
				$where = $sqlTot = $sqlRec = "";

				// check search value exist
				if( !empty($params['search']['value']) ) {
					$where .=" AND ( nama_komponen LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");    
					$where .=" OR spek_komponen LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR harga_satuan LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR satuan LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%").")";
				}

				// mengambil data per skpd
				$sqlKodeBl = '';
				if($_POST['id_skpd'] != 0){
					$data_bl = array();
					$sql = "SELECT kode_bl FROM data_sub_keg_bl WHERE id_skpd = ".$wpdb->prepare('%s', $_POST['id_skpd'])." GROUP BY id_skpd,kode_bl";
					$run = $wpdb->get_results($sql,ARRAY_A);
					foreach ($run as $value) {
						array_push($data_bl,strval($value['kode_bl']));
					}
					$sqlKodeBl = " and kode_bl in ('".implode("','", $data_bl)."')";
				}

				// getting total number records without any search
				$sql_tot = "SELECT count(*) as jml FROM `data_rka`";
				$sql = "SELECT ".implode(', ', $columns)." FROM `data_rka`";
				$where_first = " WHERE active=1 and tahun_anggaran=".$wpdb->prepare('%d', $params['tahun_anggaran']).$sqlKodeBl;
				$sqlTot .= $sql_tot.$where_first;
				$sqlRec .= $sql.$where_first;
				if(isset($where) && $where != '') {
					$sqlTot .= $where;
					$sqlRec .= $where;
				}

			 	$sqlRec .=  " GROUP by nama_komponen, spek_komponen, harga_satuan ORDER BY total DESC, ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";
				$sqlTot .=  " GROUP by nama_komponen, spek_komponen, harga_satuan";

				$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				$totalRecords = count($queryTot);
				$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

				$title = 'Laporan Data per Item SSH';
				$shortcode = '[laporan_per_item_ssh]';
				$update = false;
				$url_skpd = $this->generatePage($title, $params['tahun_anggaran'], $shortcode, $update);

				foreach($queryRecords as $key => $val){
					$queryRecords[$key]['nama_komponen'] = '<a href="'.$url_skpd.'&nama_komponen='.$val['nama_komponen'].'&spek_komponen='.$val['spek_komponen'].'&harga_satuan='.$val['harga_satuan'].'&satuan='.$val['satuan'].'&id_skpd='.$_POST['id_skpd'].'" target="_blank" style="text-decoration: none;">'.$val['nama_komponen'].'</a>';
				}

				$json_data = array(
					"draw"            => intval( $params['draw'] ),   
					"recordsTotal"    => intval( $totalRecords ),  
					"recordsFiltered" => intval($totalRecords),
					"data"            => $queryRecords
				);

				die(json_encode($json_data));
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	function singkroniasi_spd_fmis(){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil singkronisasi data SPD!'
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
				 	'created_at' => $spd->spd_tgl+' 00:00:00',
				 	'active' => 1,
				 	'tahun_anggaran' => $tahun_anggaran
				);
				if(empty($cek)){
					$wpdb->insert('data_spd', $data_spd);
				}else{
					$wpdb->update('data_spd', $data_spd, array( 'id' => $cek['id'] ) );
				}
				$wpdb->update('data_spd_rinci', array('active' => 0),
					array(
						'tahun_anggaran' => $tahun_anggaran,
						'no_spd' => $spd->spd_no
					)
				);
				foreach($spd->spd_fmis_rinci as $k => $v){
					$cek = $wpdb->get_results($wpdb->prepare("
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
					if(empty($cek)){
						$wpdb->insert('data_spd_rinci', $data_spd_rinci);
					}else{
						$wpdb->update('data_spd_rinci', $data_spd_rinci, array( 'id' => $cek['id'] ) );
					}
				}
			}else{
				$return['status'] = 'error';
				$return['message'] = 'Api Key tidak sesuai!';
			}
		}else{
			$return['status'] = 'error';
			$return['message'] = 'Format tidak sesuai!';
		}
		die(json_encode($return));
	}

	public function get_data_chart_ssh_skpd(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				// mengambil data per skpd
				$sqlKodeBl = '';
				if($_POST['id_skpd'] != 0){
					$data_bl = array();
					$sql = "SELECT kode_bl FROM data_sub_keg_bl WHERE id_skpd = ".$wpdb->prepare('%s', $_POST['id_skpd'])." GROUP BY id_skpd,kode_bl";
					$run = $wpdb->get_results($sql,ARRAY_A);
					foreach ($run as $value) {
						array_push($data_bl,strval($value['kode_bl']));
					}
					$sqlKodeBl = " and kode_bl in ('".implode("','", $data_bl)."')";
				}

				$data_ssh = $wpdb->get_results("SELECT nama_komponen, spek_komponen, harga_satuan, satuan, volume, sum(total_harga) as total FROM `data_rka` where active=1 and tahun_anggaran=".$wpdb->prepare('%d', $_POST['tahun_anggaran']).$sqlKodeBl." GROUP by nama_komponen, spek_komponen, harga_satuan order by total desc limit 20",ARRAY_A);

				$return = array(
					'status' => 'success',
					'data' => $data_ssh
				);
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit edit usulan SSH */
	public function submit_edit_usulan_ssh(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['kategori']) && !empty($_POST['spesifikasi']) && !empty($_POST['satuan']) && !empty($_POST['harga_satuan']) && !empty($_POST['tahun_anggaran']) && !empty($_POST['keterangan_lampiran']) && !empty($_POST['id_standar_harga'])){
					$kategori 				= trim(htmlspecialchars($_POST['kategori']));
					$nama_standar_harga 	= trim(htmlspecialchars($_POST['nama_komponen']));
					$spek 					= trim(htmlspecialchars($_POST['spesifikasi']));
					$satuan 				= trim(htmlspecialchars($_POST['satuan']));
					$harga					= trim(htmlspecialchars($_POST['harga_satuan']));
					$tahun_anggaran 		= trim(htmlspecialchars($_POST['tahun_anggaran']));
					$keterangan_lampiran 	= trim(htmlspecialchars($_POST['keterangan_lampiran']));
					$akun 					= $_POST['akun'];
					$id_standar_harga		= trim(htmlspecialchars($_POST['id_standar_harga']));
					$jenis_produk 			= trim(htmlspecialchars($_POST['jenis_produk']));
					$jenis_produk 			= ($jenis_produk == 0 || $jenis_produk == 1) ? $jenis_produk : NULL;
					$tkdn 					= trim(htmlspecialchars($_POST['tkdn']));
					$tkdn					= ($tkdn >= 0) ? $tkdn : NULL;
					
					$data_kategori = $wpdb->get_results($wpdb->prepare("SELECT kode_kategori,uraian_kategori FROM data_kelompok_satuan_harga WHERE id_kategori = %d",$kategori), ARRAY_A);
					$data_this_id_ssh = $wpdb->get_results($wpdb->prepare('SELECT id_standar_harga,kode_kel_standar_harga,kode_standar_harga,status,status_upload_sipd FROM data_ssh_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);

					if($data_this_id_ssh[0]['status'] == 'waiting' || in_array("administrator", $user_meta->roles)){
						if($data_this_id_ssh[0]['status_upload_sipd'] != 1){
							$date_now = date("Y-m-d H:i:s");
	
							//avoid double data ssh
							$data_avoid = $wpdb->get_results($wpdb->prepare("
								SELECT 
									id 
								FROM data_ssh
								WHERE 
									nama_standar_harga = %s AND
									satuan = %s AND
									spek = %s AND
									harga = %s AND
									kode_kel_standar_harga = %s AND
									NOT id_standar_harga = %d",
								$nama_standar_harga,
								$satuan,
								$spek,
								$harga,
								$data_kategori[0]['kode_kategori'],
								$id_standar_harga
							), ARRAY_A);
	
							//avoid double data ssh usulan
							$data_avoid_usulan = $wpdb->get_results($wpdb->prepare("
								SELECT 
									id 
								FROM data_ssh_usulan 
								WHERE 
									nama_standar_harga = %s AND
									satuan = %s AND
									spek = %s AND
									harga = %s AND
									kode_kel_standar_harga = %s AND
									NOT id_standar_harga = %d",
								$nama_standar_harga,
								$satuan,
								$spek,
								$harga,
								$data_kategori[0]['kode_kategori'],
								$id_standar_harga
							), ARRAY_A);
	
							if(!empty($data_avoid) || !empty($data_avoid_usulan)){
								$data_avoid = !empty($data_avoid) ? $data_avoid : $data_avoid_usulan;
								$return = array(
									'status' => 'error',
									'message'	=> 'Standar Harga Sudah Ada!',
									'opsi_ssh' => $data_avoid,
								);
	
								die(json_encode($return));
							}
		
							if($data_this_id_ssh[0]['kode_kel_standar_harga'] != $data_kategori[0]['kode_kategori']){
								$last_kode_standar_harga = $wpdb->get_results($wpdb->prepare("SELECT id_standar_harga,kode_standar_harga FROM `data_ssh_usulan` WHERE kode_standar_harga=(SELECT MAX(kode_standar_harga) FROM `data_ssh_usulan` WHERE kode_kel_standar_harga = %s)",$data_kategori[0]['kode_kategori']), ARRAY_A);
								$last_kode_standar_harga = (empty($last_kode_standar_harga[0]['kode_standar_harga'])) ? "0" : explode(".",$last_kode_standar_harga[0]['kode_standar_harga']);
								$last_kode_standar_harga = (int) end($last_kode_standar_harga);
								$last_kode_standar_harga = $last_kode_standar_harga+1;
								$last_kode_standar_harga = sprintf("%05d",$last_kode_standar_harga);
								$last_kode_standar_harga = $data_kategori[0]['kode_kategori'].'.'.$last_kode_standar_harga;
							}else{
								$last_kode_standar_harga = $data_this_id_ssh[0]['kode_standar_harga'];
							}
	
							//insert edit data usulan ssh
							$opsi_edit_ssh = array(
								'kode_standar_harga' => $last_kode_standar_harga,
								'nama_standar_harga' => $nama_standar_harga,
								'satuan' => $satuan,
								'spek' => $spek,
								'created_at' => $date_now,
								'kelompok' => 1,
								'harga' => $harga,
								'kode_kel_standar_harga' => $data_kategori[0]['kode_kategori'],
								'nama_kel_standar_harga' => $data_kategori[0]['uraian_kategori'],
								'tahun_anggaran' => $tahun_anggaran,
								'keterangan_lampiran' => $keterangan_lampiran,
								'jenis_produk'	=> $jenis_produk,
								'tkdn'	=> $tkdn
							);
	
							$wpdb->update('data_ssh_usulan', $opsi_edit_ssh, array(
								'id_standar_harga' => $id_standar_harga,
								'tahun_anggaran' => $tahun_anggaran
							));
	
							// get all data usulan akun existing
							$akun_exist = $wpdb->get_results($wpdb->prepare("
								SELECT
									id, 
									id_akun 
								FROM data_ssh_rek_belanja_usulan 
								WHERE id_standar_harga = %s
									AND tahun_anggaran = %s",
								$id_standar_harga,
								$tahun_anggaran
							), ARRAY_A);
							$cek_akun = array();
							foreach($akun_exist as $v_akun){
								$cek_akun[$v_akun['id_akun']] = $v_akun;
							}
	
							// get data detail akun
							$data_akun = array();
							foreach($akun as $v_akun){
								$data_akun[$v_akun] = $wpdb->get_results($wpdb->prepare("SELECT id_akun,kode_akun,nama_akun FROM data_akun WHERE id_akun = %d",$v_akun), ARRAY_A);
							}
	
							// input dan update akun
							foreach($akun as $id_akun){
								$opsi_akun = array(
									'id_akun' => $data_akun[$id_akun][0]['id_akun'],
									'kode_akun' => $data_akun[$id_akun][0]['kode_akun'],
									'nama_akun' => $data_akun[$id_akun][0]['kode_akun'].' '.$data_akun[$id_akun][0]['nama_akun'],
									'id_standar_harga' => $id_standar_harga,
									'tahun_anggaran' => $tahun_anggaran,
								);
								if(empty($cek_akun[$id_akun])){
									$wpdb->insert('data_ssh_rek_belanja_usulan', $opsi_akun);
								}else{
									$wpdb->update('data_ssh_rek_belanja_usulan', $opsi_akun, array(
										'id' => $cek_akun[$id_akun]['id']
									));
									unset($cek_akun[$id_akun]);
								}
							}
	
							// hapus akun yang tidak dipakai
							foreach($cek_akun as $v_akun){
								$wpdb->delete('data_ssh_rek_belanja_usulan', array(
									'id' => $v_akun['id']
								), array('%d'));
							}
	
							$return = array(
								'status' => 'success',
								'message'	=> 'Berhasil!',
								'opsi_edit_ssh' => $opsi_edit_ssh,
							);
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "User tidak diijinkan!\nData sudah diunggah di SIPD",
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!\nData sudah dalam tahap verifikasi",
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit edit tambah harga usulan SSH */
	public function submit_edit_tambah_harga_ssh(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['id_standar_harga']) && !empty($_POST['tahun_anggaran']) && !empty($_POST['harga_satuan']) && !empty($_POST['keterangan_lampiran'])){
					$tahun_anggaran 		= trim(htmlspecialchars($_POST['tahun_anggaran']));
					$keterangan_lampiran 	= trim(htmlspecialchars($_POST['keterangan_lampiran']));
					$id_standar_harga		= trim(htmlspecialchars($_POST['id_standar_harga']));
					$harga_satuan			= trim(htmlspecialchars($_POST['harga_satuan']));
					
					$data_this_id_ssh = $wpdb->get_results($wpdb->prepare('SELECT id_standar_harga,kode_kel_standar_harga,kode_standar_harga,nama_standar_harga,satuan,spek,harga,status,status_upload_sipd FROM data_ssh_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);

					if($data_this_id_ssh[0]['status'] == 'waiting' || in_array("administrator", $user_meta->roles)){
						if($data_this_id_ssh[0]['status_upload_sipd'] != 1){
							//avoid double data ssh
							$data_avoid = $wpdb->get_results($wpdb->prepare("
								SELECT 
									id 
								FROM data_ssh
								WHERE 
									nama_standar_harga = %s AND
									satuan = %s AND
									spek = %s AND
									harga = %s AND
									kode_kel_standar_harga = %s",
								$data_this_id_ssh[0]['nama_standar_harga'],
								$data_this_id_ssh[0]['satuan'],
								$data_this_id_ssh[0]['spek'],
								$harga_satuan,
								$data_this_id_ssh[0]['kode_kel_standar_harga']
							), ARRAY_A);
	
							//avoid double data ssh usulan
							$data_avoid_usulan = $wpdb->get_results($wpdb->prepare("
								SELECT 
									id 
								FROM data_ssh_usulan 
								WHERE 
									nama_standar_harga = %s AND
									satuan = %s AND
									spek = %s AND
									harga = %s AND
									kode_kel_standar_harga = %s AND
									NOT id_standar_harga = %d",
								$data_this_id_ssh[0]['nama_standar_harga'],
								$data_this_id_ssh[0]['satuan'],
								$data_this_id_ssh[0]['spek'],
								$harga_satuan,
								$data_this_id_ssh[0]['kode_kel_standar_harga'],
								$id_standar_harga
							), ARRAY_A);
	
							if(!empty($data_avoid) || !empty($data_avoid_usulan)){
								$data_avoid = !empty($data_avoid) ? $data_avoid : $data_avoid_usulan;
								$return = array(
									'status' => 'error',
									'message'	=> 'Standar Harga Sudah Ada!',
									'data_avoid' => $data_avoid,
								);
	
								die(json_encode($return));
							}
		
	
							//insert edit data tambah harga usulan ssh
							$opsi_edit_ssh = array(
								'harga' => $harga_satuan,
								'keterangan_lampiran' => $keterangan_lampiran,
							);
	
							$wpdb->update('data_ssh_usulan', $opsi_edit_ssh, array(
								'id_standar_harga' => $id_standar_harga,
								'tahun_anggaran' => $tahun_anggaran
							));
	
							$return = array(
								'status' => 'success',
								'message'	=> 'Berhasil!',
								'opsi_edit_ssh' => $opsi_edit_ssh,
							);
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "User tidak diijinkan!\nData sudah diunggah di SIPD",
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!\nData sudah dalam tahap verifikasi",
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit edit tambah akun usulan SSH */
	public function submit_edit_tambah_akun_ssh(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['tahun_anggaran']) && !empty($_POST['id_standar_harga'])  && !empty($_POST['new_akun'])){
					$tahun_anggaran 	= trim(htmlspecialchars($_POST['tahun_anggaran']));
					$new_akun 			= $_POST['new_akun'];
					$id_standar_harga	= trim(htmlspecialchars($_POST['id_standar_harga']));

					$data_this_id_ssh = $wpdb->get_results($wpdb->prepare('SELECT id_standar_harga,kode_kel_standar_harga,kode_standar_harga,status,status_upload_sipd FROM data_ssh_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);
	
					if($data_this_id_ssh[0]['status'] == 'waiting' || in_array("administrator", $user_meta->roles)){
						if($data_this_id_ssh[0]['status_upload_sipd'] != 1){
							// get all data usulan akun existing
							$akun_exist = $wpdb->get_results($wpdb->prepare("
								SELECT
									id, 
									id_akun 
								FROM data_ssh_rek_belanja_usulan 
								WHERE id_standar_harga = %s
									AND tahun_anggaran = %s",
								$id_standar_harga,
								$tahun_anggaran
							), ARRAY_A);
					
							/** get data exist akun by id */
							$cek_akun = array();
							foreach($akun_exist as $v_akun){
								$cek_akun[$v_akun['id_akun']] = $v_akun;
							}

							// get data detail input new akun
							$data_akun = array();
							foreach($new_akun as $v_akun){
								$data_akun[$v_akun] = $wpdb->get_results($wpdb->prepare("SELECT id_akun,kode_akun,nama_akun FROM data_akun WHERE id_akun = %d",$v_akun), ARRAY_A);
							}

							// input dan update akun
							foreach($new_akun as $id_akun){
								$opsi_akun = array(
									'id_akun' => $data_akun[$id_akun][0]['id_akun'],
									'kode_akun' => $data_akun[$id_akun][0]['kode_akun'],
									'nama_akun' => $data_akun[$id_akun][0]['kode_akun'].' '.$data_akun[$id_akun][0]['nama_akun'],
									'id_standar_harga' => $id_standar_harga,
									'tahun_anggaran' => $tahun_anggaran,
								);
								if(empty($cek_akun[$id_akun])){
									$wpdb->insert('data_ssh_rek_belanja_usulan', $opsi_akun);
								}else{
									$wpdb->update('data_ssh_rek_belanja_usulan', $opsi_akun, array(
										'id' => $cek_akun[$id_akun]['id']
									));
									unset($cek_akun[$id_akun]);
								}
							}

							// hapus akun yang tidak dipakai
							foreach($cek_akun as $v_akun){
								$wpdb->delete('data_ssh_rek_belanja_usulan', array(
									'id' => $v_akun['id']
								), array('%d'));
							}

							$return = array(
								'status' => 'success',
								'message'	=> 'Berhasil!',
								'data' => $data_akun,
							);
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "User tidak diijinkan!\nData sudah diunggah di SIPD",
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!\nData sudah dalam tahap verifikasi",
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function submit_delete_usulan_ssh(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$id_standar_harga		= $_POST['id_standar_harga'];
				$tahun_anggaran			= $_POST['tahun_anggaran'];

				$data_this_id_ssh = $wpdb->get_results($wpdb->prepare('SELECT id_standar_harga,kode_kel_standar_harga,kode_standar_harga,status,status_upload_sipd FROM data_ssh_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);

				if($data_this_id_ssh[0]['status'] == 'waiting' || in_array("administrator", $user_meta->roles)){
					if($data_this_id_ssh[0]['status_upload_sipd'] != 1){
						$wpdb->delete('data_ssh_usulan', array(
							'tahun_anggaran' => $tahun_anggaran,
							'id_standar_harga' => $id_standar_harga
						), array('%d', '%d'));
					
						$wpdb->delete('data_ssh_rek_belanja_usulan', array(
							'tahun_anggaran' => $tahun_anggaran,
							'id_standar_harga' => $id_standar_harga
						), array('%d', '%d'));
	
						$return = array(
							'status' => 'success',
							'message'	=> 'Berhasil!',
						);
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!\nData sudah diunggah di SIPD",
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> "User tidak diijinkan!\nData sudah dalam tahap verifikasi",
					);
				}

			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function submit_delete_akun_usulan_ssh(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);
		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$id_standar_harga		= $_POST['id_standar_harga'];
				$tahun_anggaran			= $_POST['tahun_anggaran'];
				$id_rek_akun_usulan_ssh	= $_POST['id_rek_akun_usulan_ssh'];

				$data_this_id_ssh = $wpdb->get_results($wpdb->prepare('SELECT id_standar_harga,kode_kel_standar_harga,kode_standar_harga,status FROM data_ssh_usulan WHERE id_standar_harga = %d',$id_standar_harga), ARRAY_A);

				if($data_this_id_ssh[0]['status'] == 'waiting' || in_array("administrator", $user_meta->roles)){
					$wpdb->delete('data_ssh_rek_belanja_usulan', array(
						'id'				=> $id_rek_akun_usulan_ssh,
						'tahun_anggaran' 	=> $tahun_anggaran,
						'id_standar_harga' 	=> $id_standar_harga
					), array('%d', '%d'));

					$return = array(
						'status' => 'success',
						'message'	=> 'Berhasil!',
					);
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> "User tidak diijinkan!\nData sudah dalam tahap verifikasi",
					);
				}

			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit delete check multi select */
	public function submit_delete_check_usulan_ssh(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				// $id_standar_harga		= $_POST['id_standar_harga'];
				$tahun_anggaran			= $_POST['tahun_anggaran'];
				$check_id_arr = $_POST['check_id_arr'];

				foreach($check_id_arr as $deleteid){
					$data_this_id_ssh = $wpdb->get_results($wpdb->prepare('SELECT id_standar_harga,kode_kel_standar_harga,kode_standar_harga,status,status_upload_sipd FROM data_ssh_usulan WHERE id_standar_harga = %d',$deleteid), ARRAY_A);
					if($data_this_id_ssh[0]['status'] == 'waiting' || in_array("administrator", $user_meta->roles)){
						if($data_this_id_ssh[0]['status_upload_sipd'] != 1){
							$wpdb->delete('data_ssh_usulan', array(
								'id_standar_harga'	=> $deleteid,
								'tahun_anggaran' 	=> $tahun_anggaran
							), array('%d', '%d'));
	
							$wpdb->delete('data_ssh_rek_belanja_usulan', array(
								'tahun_anggaran' => $tahun_anggaran,
								'id_standar_harga' => $deleteid
							), array('%d', '%d'));
						
							$return = array(
								'status' => 'success',
								'message'	=> 'Berhasil!',
							);
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "User tidak diijinkan!\nAda data yang sudah diunggah di SIPD",
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!\nData sudah dalam tahap verifikasi",
						);
					}
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit approve check multi select */
	public function submit_approve_check_usulan_ssh(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		$table_content = '';
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran	= $_POST['tahun_anggaran'];
				$check_id_arr	= $_POST['check_id_arr'];
				$status			= $_POST['status'];
				$alasan			= $_POST['alasan'];

				if(!empty($_POST['check_id_arr']) && !empty($_POST['status'])){
					if($status =='notapprove' && $_POST['alasan'] == ""){
						$return = array(
							'status' => 'error',
							'message'	=> 'Alasan tidak boleh kosong!',
						);
					}else{
						foreach($check_id_arr as $checked_id){
							$data_this_id_ssh = $wpdb->get_results($wpdb->prepare('SELECT id_standar_harga,kode_kel_standar_harga,kode_standar_harga,status,status_upload_sipd FROM data_ssh_usulan WHERE id_standar_harga = %d',$checked_id), ARRAY_A);
							if($data_this_id_ssh[0]['status'] == 'waiting' || in_array("administrator", $user_meta->roles)){
								if($data_this_id_ssh[0]['status_upload_sipd'] != 1){
									$date_now = date("Y-m-d H:i:s");
					
									$status_usulan_ssh = ($status == 'approve') ? 'approved' : 'rejected';
					
									$keterangan_status = (!empty($alasan)) ? $alasan : NULL;
					
									//update status data usulan ssh
									$opsi_ssh = array(
										'status' => $status_usulan_ssh,
										'keterangan_status' => $keterangan_status,
										'update_at' => $date_now,
									);
					
									$where_ssh = array(
										'id_standar_harga' => $checked_id
									);
					
									$wpdb->update('data_ssh_usulan',$opsi_ssh,$where_ssh);
									
									$return = array(
										'status' => 'success',
										'message'	=> 'Berhasil!',
									);
								}else{
									$return = array(
										'status' => 'error',
										'message'	=> "User tidak diijinkan!\nAda data yang sudah diunggah di SIPD",
									);
								}
							}else{
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dalam tahap verifikasi",
								);
							}
						}
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Tidak ada data yang dipilih!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function singkron_kategori_ssh(){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil singkronisasi data kategori satuan harga!',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$kategori = $_POST['kategori'];
				$wpdb->update('data_kelompok_satuan_harga', array('active' => 0), array(
					'tahun_anggaran'	=> $_POST['tahun_anggaran'],
					'tipe_kelompok' => $_POST['tipe_ssh']
				));
				foreach ($kategori as $k => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						from data_kelompok_satuan_harga 
						where tahun_anggaran=%d 
							AND id_kategori=%s
							AND tipe_kelompok=%s",
						$_POST['tahun_anggaran'],
						$v['id_kategori'],
						$v['kelompok']
					));
					$opsi = array(
						'id_kategori' => $v['id_kategori'],
						'kode_kategori' => $v['kode_kategori'],
						'uraian_kategori' => $v['uraian_kategori'],
						'tipe_kelompok' => $v['kelompok'],
						'active' => 1,
						'tahun_anggaran'	=> $_POST['tahun_anggaran']
					);
					if (!empty($cek)) {
						$wpdb->update('data_kelompok_satuan_harga', $opsi, array(
							'tahun_anggaran'	=> $_POST['tahun_anggaran'],
							'id_kategori' => $v['id_kategori']
						));
					} else {
						$wpdb->insert('data_kelompok_satuan_harga', $opsi);
					}
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function singkron_satuan(){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil singkronisasi data kategori dan satuan!',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$satuan = $_POST['satuan'];
				foreach ($satuan as $k => $v) {
					$cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						from data_satuan 
						where tahun_anggaran=%d 
							AND nama_satuan=%s",
						$_POST['tahun_anggaran'],
						$v['satuan']
					));
					$opsi = array(
						'nama_satuan' => $v['satuan'],
						'tahun_anggaran'	=> $_POST['tahun_anggaran']
					);
					if (!empty($cek)) {
						$wpdb->update('data_satuan', $opsi, array(
							'tahun_anggaran'	=> $_POST['tahun_anggaran'],
							'nama_satuan' => $v['satuan']
						));
					} else {
						$wpdb->insert('data_satuan', $opsi);
					}
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function get_usulan_ssh_sipd(){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil get usulan SSH!',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$data = $wpdb->get_results($wpdb->prepare("
					SELECT 
						s.*
					from data_ssh_usulan as s
					where s.tahun_anggaran=%d
						and s.status='approved'
						and (
							s.status_upload_sipd is null
							OR s.status_upload_sipd=0
						)
					",
					$_POST['tahun_anggaran']
				), ARRAY_A);
				foreach($data as $k => $v){
					$data[$k]['akun'] = $wpdb->get_results($wpdb->prepare("
						SELECT 
							r.*
						from data_ssh_rek_belanja_usulan r 
						where r.id_standar_harga=%d
							and r.tahun_anggaran=%d
						",
						$v['id_standar_harga'],
						$_POST['tahun_anggaran']
					), ARRAY_A);
				}
				$return['data'] = $data;
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function update_usulan_ssh_sipd(){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil update status usulan SSH!'
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				foreach($_POST['data_id'] as $id_standar_harga){
					$wpdb->update('data_ssh_usulan', array(
						'status_upload_sipd' => 1
					), array(
						'id_standar_harga' => $id_standar_harga,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Ambil data penjadwalan */
	public function get_data_penjadwalan(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['tipe_perencanaan'])){
					$params = $columns = $totalRecords = $data = array();
					$params = $_REQUEST;
					$columns = array(
						0 => 'id_jadwal_lokal',
						1 => 'nama',
						2 => 'waktu_awal',
						3 => 'waktu_akhir',
						4 => 'status',
						5 => 'tahun_anggaran',
						6 => 'relasi_perencanaan',
						7 => 'lama_pelaksanaan'
					);
					$where = $sqlTot = $sqlRec = "";

					// check search value exist
					if( !empty($params['search']['value']) ) {
						$where .=" AND ( nama LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");   
						$where .=" OR waktu_awal LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
						$where .=" OR waktu_akhir LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					}

					/** Search id tipe */
					$tipe_perencanaan = $_POST['tipe_perencanaan'];
					$sqlTipe = $wpdb->get_results($wpdb->prepare("
						SELECT 
							* 
						FROM `data_tipe_perencanaan` 
						WHERE nama_tipe=%s
					", $tipe_perencanaan), ARRAY_A);

					if(empty($sqlTipe)){
						$return = array(
							'status' => 'error',
							'message'	=> 'Data tidak ditemukan!'
						);
						die(json_encode($return));
					}

					if(!empty($_POST['tahun_anggaran'])){
						$where .=$wpdb->prepare(" AND tahun_anggaran = %d", $_POST['tahun_anggaran']);
					}

					// getting total number records without any search
					$sqlTot = "SELECT count(*) as jml FROM `data_jadwal_lokal` WHERE id_tipe =".$sqlTipe[0]['id'];
					$sqlRec = "SELECT ".implode(', ', $columns)." FROM `data_jadwal_lokal` WHERE id_tipe =".$sqlTipe[0]['id'];
					if(isset($where) && $where != '') {
						$sqlTot .= $where;
						$sqlRec .= $where;
					}

					$sqlRec .=  $wpdb->prepare(" ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT %d ,%d ", $params['start'], $params['length']);

					$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
					$totalRecords = $queryTot[0]['jml'];
					$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

					if(!empty($queryRecords)){
						foreach($queryRecords as $recKey => $recVal){
							if($recVal['status'] == 1){
								$report = '<a class="btn btn-primary mr-2" href="#" onclick="return report(\''.$recVal['id_jadwal_lokal'].'\');" title="Cetak Laporan"><i class="dashicons dashicons-printer"></i></a>';
								$lock	= '<a class="btn btn-success disabled" href="#" onclick="return cannot_change_schedule(\'kunci\');" title="Kunci data penjadwalan" aria-disabled="true"><i class="dashicons dashicons-lock"></i></a>';
								$edit	= '';
								$delete	= '';
							}else{
								$report	= '';
								$lock	= '<a class="btn btn-success mr-2" href="#" onclick="return lock_data_penjadwalan(\''.$recVal['id_jadwal_lokal'].'\');" title="Kunci data penjadwalan"><i class="dashicons dashicons-unlock"></i></a>';
								$edit	= '<a class="btn btn-warning mr-2" href="#" onclick="return edit_data_penjadwalan(\''.$recVal['id_jadwal_lokal'].'\');" title="Edit data penjadwalan"><i class="dashicons dashicons-edit"></i></a>';
								$delete	= '<a class="btn btn-danger" href="#" onclick="return hapus_data_penjadwalan(\''.$recVal['id_jadwal_lokal'].'\');" title="Hapus data penjadwalan"><i class="dashicons dashicons-trash"></i></a>';
								$delete	.= '<a class="btn btn-danger" href="#" onclick="copy_usulan(); return false;" title="Copy Data Usulan ke Penetapan">Copy Data Usulan</a>';
							}
	
							$status = array(
								0 => 'terbuka',
								1 => 'dikunci',
								2 => 'selesai'
							);
	
							$relasi_perencanaan = '-';
							$relasi_perencanaan_renstra = '-';
							if(!empty($recVal['relasi_perencanaan'])){
								$data_relasi_perencanaan = $wpdb->get_results($wpdb->prepare(
									'SELECT 
										* 
									FROM 
										data_jadwal_lokal
									WHERE 
										id_jadwal_lokal=%d',
									$recVal['relasi_perencanaan']
								),ARRAY_A);
	
								if(!empty($data_relasi_perencanaan)){
									$relasi_perencanaan = $data_relasi_perencanaan[0]['nama'];
	
									$nama_tipe = $wpdb->get_results($wpdb->prepare('
										SELECT
											*
										FROM
											data_tipe_perencanaan
										WHERE
											id=%d',
											$data_relasi_perencanaan[0]['id_tipe']
									), ARRAY_A);
									$relasi_perencanaan_renstra = (!empty($nama_tipe)) ? strtoupper($nama_tipe[0]['nama_tipe']) .' | '.$relasi_perencanaan : '-';
								}
							}
	
							$tahun_anggaran_selesai = $recVal['tahun_anggaran'] + $recVal['lama_pelaksanaan'] - 1;
						
							$queryRecords[$recKey]['waktu_awal']	= date('d-m-Y H:i', strtotime($recVal['waktu_awal']));
							$queryRecords[$recKey]['waktu_akhir']	= date('d-m-Y H:i', strtotime($recVal['waktu_akhir']));
							$queryRecords[$recKey]['aksi'] = $report.$lock.$edit.$delete;
							$queryRecords[$recKey]['nama'] = ucfirst($recVal['nama']);
							$queryRecords[$recKey]['status'] = $status[$recVal['status']];
							$queryRecords[$recKey]['tahun_anggaran_selesai'] = $tahun_anggaran_selesai;
							$queryRecords[$recKey]['relasi_perencanaan'] = $relasi_perencanaan;
							$queryRecords[$recKey]['relasi_perencanaan_renstra'] = $relasi_perencanaan_renstra;
						}
	
						$json_data = array(
							"draw"            => intval( $params['draw'] ),  
							"recordsTotal"    => intval( $totalRecords ), 
							"recordsFiltered" => intval( $totalRecords ),
							"data"            => $queryRecords
						);
	
						die(json_encode($json_data));
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> 'Data tidak ditemukan!'
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit data penjadwalan */
	public function submit_add_schedule(){
		global $wpdb;
		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(in_array("administrator", $user_meta->roles)){
					$lama_pelaksanaan = ($_POST['tipe_perencanaan'] == 'renja') ? 1 : trim(htmlspecialchars($_POST['lama_pelaksanaan']));
					if(!empty($_POST['nama']) && !empty($_POST['jadwal_mulai']) && !empty($_POST['jadwal_selesai']) && !empty($_POST['tahun_anggaran']) && !empty($_POST['tipe_perencanaan']) && !empty($lama_pelaksanaan)){
						$nama				= trim(htmlspecialchars($_POST['nama']));
						$jadwal_mulai		= trim(htmlspecialchars($_POST['jadwal_mulai']));
						$jadwal_mulai		= date('Y-m-d H:i:s', strtotime($jadwal_mulai));
						$jadwal_selesai		= trim(htmlspecialchars($_POST['jadwal_selesai']));
						$jadwal_selesai		= date('Y-m-d H:i:s', strtotime($jadwal_selesai));
						$tahun_anggaran		= trim(htmlspecialchars($_POST['tahun_anggaran']));
						$tipe_perencanaan	= trim(htmlspecialchars($_POST['tipe_perencanaan']));
						$relasi_perencanaan = (!empty($_POST['relasi_perencanaan'])) ? trim(htmlspecialchars($_POST['relasi_perencanaan'])) : NULL;

						$id_tipe = 0;
						$sqlTipe = $wpdb->get_results("SELECT * FROM `data_tipe_perencanaan` WHERE nama_tipe='".$tipe_perencanaan."'", ARRAY_A);
						if(!empty($sqlTipe)){
							$id_tipe = $sqlTipe[0]['id'];

							$sqlSameTipe = $wpdb->get_results("SELECT * FROM `data_jadwal_lokal` WHERE id_tipe='".$id_tipe."'", ARRAY_A);
							foreach($sqlSameTipe as $valTipe){
								if($valTipe['status'] != 1){
									$return = array(
										'status' => 'error',
										'message'	=> 'Masih ada penjadwalan yang terbuka!'
									);
									die(json_encode($return));
								}
								if($jadwal_mulai > $valTipe['waktu_awal'] && $jadwal_mulai < $valTipe['waktu_akhir'] || $jadwal_selesai > $valTipe['waktu_awal'] && $jadwal_selesai < $valTipe['waktu_akhir']){
									$return = array(
										'status' => 'error',
										'message'	=> 'Waktu sudah dipakai jadwal lain!'
									);
									die(json_encode($return));
								}
							}

							//insert data penjadwalan
							$data_jadwal = array(
								'nama' 				=> $nama,
								'waktu_awal'		=> $jadwal_mulai,
								'waktu_akhir'		=> $jadwal_selesai,
								'tahun_anggaran'	=> $tahun_anggaran,
								'status'			=> 0,
								'tahun_anggaran'	=> $tahun_anggaran,
								'id_tipe'			=> $id_tipe,
								'relasi_perencanaan'=> $relasi_perencanaan,
								'lama_pelaksanaan'	=> $lama_pelaksanaan
							);
	
							$wpdb->insert('data_jadwal_lokal',$data_jadwal);
							
							$return = array(
								'status'		=> 'success',
								'message'		=> 'Berhasil!',
								'data_jadwal'	=> $data_jadwal,
							);
						}else{
							$return = array(
									'status' => 'error',
									'message'	=> 'Tipe penjadwalan tidak diketahui!'
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> "User tidak diijinkan!",
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** get data jadwal by id */
	public function get_data_jadwal_by_id(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$id_jadwal_lokal = $_POST['id_jadwal_lokal'];
				
				$data_penjadwalan_by_id = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_jadwal_lokal WHERE id_jadwal_lokal = %d',$id_jadwal_lokal), ARRAY_A);

				$return = array(
					'status' 						=> 'success',
					'data' 							=> $data_penjadwalan_by_id[0]
				);
			}else{
				$return = array(
					'status'	=> 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status'	=> 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit data penjadwalan */
	public function submit_edit_schedule(){
		global $wpdb;
		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(in_array("administrator", $user_meta->roles)){
					$lama_pelaksanaan = ($_POST['tipe_perencanaan'] == 'renja') ? 1 : trim(htmlspecialchars($_POST['lama_pelaksanaan']));
					if(!empty($_POST['id_jadwal_lokal']) && !empty($_POST['nama']) && !empty($_POST['jadwal_mulai']) && !empty($_POST['jadwal_selesai']) && !empty($_POST['tahun_anggaran']) && !empty($lama_pelaksanaan)){
						$id_jadwal_lokal= trim(htmlspecialchars($_POST['id_jadwal_lokal']));
						$nama			= trim(htmlspecialchars($_POST['nama']));
						$jadwal_mulai	= trim(htmlspecialchars($_POST['jadwal_mulai']));
						$jadwal_mulai	= date('Y-m-d H:i:s', strtotime($jadwal_mulai));
						$jadwal_selesai	= trim(htmlspecialchars($_POST['jadwal_selesai']));
						$jadwal_selesai	= date('Y-m-d H:i:s', strtotime($jadwal_selesai));
						$tahun_anggaran	= trim(htmlspecialchars($_POST['tahun_anggaran']));
						$relasi_perencanaan = (!empty($_POST['relasi_perencanaan'])) ? trim(htmlspecialchars($_POST['relasi_perencanaan'])) : NULL;

						$data_this_id = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_jadwal_lokal WHERE id_jadwal_lokal = %d',$id_jadwal_lokal), ARRAY_A);

						if(!empty($data_this_id)){
							$status_check = array(0,NULL,2);
							if(in_array($data_this_id[0]['status'],$status_check)){
								//update data penjadwalan
								$data_jadwal = array(
									'nama' 					=> $nama,
									'waktu_awal'			=> $jadwal_mulai,
									'waktu_akhir'			=> $jadwal_selesai,
									'tahun_anggaran'		=> $tahun_anggaran,
									'relasi_perencanaan' 	=> $relasi_perencanaan,
									'lama_pelaksanaan'	=> $lama_pelaksanaan
								);
	
								$wpdb->update('data_jadwal_lokal', $data_jadwal, array(
									'id_jadwal_lokal'	=> $id_jadwal_lokal
								));
								
								$return = array(
									'status'		=> 'success',
									'message'		=> 'Berhasil!',
									'data_jadwal'	=> $data_jadwal,
								);
							}else{
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "Data tidak ditemukan!",
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> "User tidak diijinkan!",
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit delete data jadwal */
	public function submit_delete_schedule(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['id_jadwal_lokal'])){
					if(in_array("administrator", $user_meta->roles)){
						$id_jadwal_lokal= trim(htmlspecialchars($_POST['id_jadwal_lokal']));

						$data_this_id = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_jadwal_lokal WHERE id_jadwal_lokal = %d',$id_jadwal_lokal), ARRAY_A);

						if(!empty($data_this_id)){
							$status_check = array(0,NULL,2);
							if(in_array($data_this_id[0]['status'],$status_check)){
								$wpdb->delete('data_jadwal_lokal', array(
									'id_jadwal_lokal' => $id_jadwal_lokal
								), array('%d'));
	
								$return = array(
									'status' => 'success',
									'message'	=> 'Berhasil!',
								);
							}else{
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "Data tidak ditemukan!",
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** get data default lama pelaksanaan by id */
	public function get_data_standar_lama_pelaksanaan(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tipe_perencanaan = $_POST['tipe_perencanaan'];
				
				$lama_pelaksanaan_by_name = $wpdb->get_results($wpdb->prepare('SELECT * FROM data_tipe_perencanaan WHERE nama_tipe = %s',$tipe_perencanaan), ARRAY_A);

				$return = array(
					'status' 						=> 'success',
					'data' 							=> $lama_pelaksanaan_by_name[0]
				);
			}else{
				$return = array(
					'status'	=> 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status'	=> 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit lock data jadwal */
	public function submit_lock_schedule(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['id_jadwal_lokal'])){
					if(in_array("administrator", $user_meta->roles)){
						$id_jadwal_lokal= trim(htmlspecialchars($_POST['id_jadwal_lokal']));

						$data_this_id 	= $wpdb->get_results($wpdb->prepare('SELECT * FROM data_jadwal_lokal WHERE id_jadwal_lokal = %d',$id_jadwal_lokal), ARRAY_A);

						$timezone = get_option('timezone_string');
						if(preg_match("/Asia/i", $timezone)){
							date_default_timezone_set($timezone);
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "Pengaturan timezone salah. Pilih salah satu kota di zona waktu yang sama dengan anda, antara lain:  \'Jakarta\',\'Makasar\',\'Jayapura\'",
							);
							die(json_encode($return));
						}

						$dateTime = new DateTime();
						$time_now = $dateTime->format('Y-m-d H:i:s');
						if($time_now > $data_this_id[0]['waktu_awal']){
							$status_check = array(0,NULL,2);
							if(in_array($data_this_id[0]['status'],$status_check)){
								//lock data penjadwalan
								$wpdb->update('data_jadwal_lokal', array('waktu_akhir' => $time_now,'status' => 1), array(
									'id_jadwal_lokal'	=> $id_jadwal_lokal
								));

								$delete_lokal_history = $this->delete_data_lokal_history('data_rka', $data_this_id[0]['id_jadwal_lokal']);

								$columns_1 = array('created_user','createddate','createdtime','harga_satuan','harga_satuan_murni','id_daerah','id_rinci_sub_bl','id_standar_nfs','is_locked','jenis_bl','ket_bl_teks','kode_akun','koefisien','koefisien_murni','lokus_akun_teks','nama_akun','nama_komponen','spek_komponen','satuan','spek','sat1','sat2','sat3','sat4','volum1','volum2','volum3','volum4','volume','volume_murni','subs_bl_teks','subtitle_teks','kode_dana','is_paket','nama_dana','id_dana','substeks','total_harga','rincian','rincian_murni','totalpajak','pajak','pajak_murni','updated_user','updateddate','updatedtime','user1','user2','active','update_at','tahun_anggaran','idbl','idsubbl','kode_bl','kode_sbl','id_prop_penerima','id_camat_penerima','id_kokab_penerima','id_lurah_penerima','id_penerima','idkomponen','idketerangan','idsubtitle');
	
								$sql_backup_data_rka =  "INSERT INTO data_rka_history (".implode(', ', $columns_1).",id_asli,id_jadwal)
											SELECT ".implode(', ', $columns_1).",id as id_asli,".$data_this_id[0]['id_jadwal_lokal']."
											FROM data_rka WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords1 = $wpdb->query($sql_backup_data_rka);

								$delete_lokal_history = $this->delete_data_lokal_history('data_sub_keg_bl', $data_this_id[0]['id_jadwal_lokal']);

								$columns_2 = array('id_sub_skpd','id_lokasi','id_label_kokab','nama_dana','no_sub_giat','kode_giat','id_program','nama_lokasi','waktu_akhir','pagu_n_lalu','id_urusan','id_unik_sub_bl','id_sub_giat','label_prov','kode_program','kode_sub_giat','no_program','kode_urusan','kode_bidang_urusan','nama_program','target_4','target_5','id_bidang_urusan','nama_bidang_urusan','target_3','no_giat','id_label_prov','waktu_awal','pagumurni','pagu','pagu_simda','output_sub_giat','sasaran','indikator','id_dana','nama_sub_giat','pagu_n_depan','satuan','id_rpjmd','id_giat','id_label_pusat','nama_giat','kode_skpd','nama_skpd','kode_sub_skpd','id_skpd','id_sub_bl','nama_sub_skpd','target_1','nama_urusan','target_2','label_kokab','label_pusat','pagu_keg','pagu_fmis','id_bl','kode_bl','kode_sbl','active','update_at','tahun_anggaran');

								$sql_backup_data_sub_keg_bl =  "INSERT INTO data_sub_keg_bl_history (".implode(', ', $columns_2).",id_asli,id_jadwal)
											SELECT ".implode(', ', $columns_2).",id as id_asli,".$data_this_id[0]['id_jadwal_lokal']."
											FROM data_sub_keg_bl WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords2 = $wpdb->query($sql_backup_data_sub_keg_bl);

								$delete_lokal_history = $this->delete_data_lokal_history('data_sub_keg_indikator', $data_this_id[0]['id_jadwal_lokal']);

								$oclumns_3 = array('outputteks','targetoutput','satuanoutput','idoutputbl','targetoutputteks','kode_sbl','idsubbl','active','update_at','tahun_anggaran');

								$sql_backup_data_sub_keg_indikator =  "INSERT INTO data_sub_keg_indikator_history (".implode(', ', $oclumns_3).",id_asli,id_jadwal)
											SELECT ".implode(', ', $oclumns_3).",id as id_asli,".$data_this_id[0]['id_jadwal_lokal']."
											FROM data_sub_keg_indikator WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords3 = $wpdb->query($sql_backup_data_sub_keg_indikator);

								$delete_lokal_history = $this->delete_data_lokal_history('data_keg_indikator_hasil', $data_this_id[0]['id_jadwal_lokal']);

								$oclumns_4 = array('hasilteks','satuanhasil','targethasil','targethasilteks','kode_sbl','idsubbl','active','update_at','tahun_anggaran');

								$sql_backup_data_keg_indikator_hasil =  "INSERT INTO data_keg_indikator_hasil_history (".implode(', ', $oclumns_4).",id_asli,id_jadwal)
											SELECT ".implode(', ', $oclumns_4).",id as id_asli,".$data_this_id[0]['id_jadwal_lokal']."
											FROM data_keg_indikator_hasil WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords4 = $wpdb->query($sql_backup_data_keg_indikator_hasil);

								$delete_lokal_history = $this->delete_data_lokal_history('data_tag_sub_keg', $data_this_id[0]['id_jadwal_lokal']);

								$oclumns_5 = array('idlabelgiat','namalabel','idtagbl','kode_sbl','idsubbl','active','update_at','tahun_anggaran');

								$sql_backup_data_tag_sub_keg =  "INSERT INTO data_tag_sub_keg_history (".implode(', ', $oclumns_5).",id_asli,id_jadwal)
											SELECT ".implode(', ', $oclumns_5).",id as id_asli,".$data_this_id[0]['id_jadwal_lokal']."
											FROM data_tag_sub_keg WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords5 = $wpdb->query($sql_backup_data_tag_sub_keg);

								$delete_lokal_history = $this->delete_data_lokal_history('data_capaian_prog_sub_keg', $data_this_id[0]['id_jadwal_lokal']);

								$oclumns_6 = array('satuancapaian','targetcapaianteks','capaianteks','targetcapaian','kode_sbl','idsubbl','active','update_at','tahun_anggaran');

								$sql_backup_data_capaian_prog_sub_keg =  "INSERT INTO data_capaian_prog_sub_keg_history (".implode(', ', $oclumns_6).",id_asli,id_jadwal)
											SELECT ".implode(', ', $oclumns_6).",id as id_asli,".$data_this_id[0]['id_jadwal_lokal']."
											FROM data_capaian_prog_sub_keg WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords6 = $wpdb->query($sql_backup_data_capaian_prog_sub_keg);

								$delete_lokal_history = $this->delete_data_lokal_history('data_output_giat_sub_keg', $data_this_id[0]['id_jadwal_lokal']);

								$oclumns_7 = array('outputteks','satuanoutput','targetoutput','targetoutputteks','kode_sbl','idsubbl','active','update_at','tahun_anggaran');

								$sql_backup_data_output_giat_sub_keg =  "INSERT INTO data_output_giat_sub_keg_history (".implode(', ', $oclumns_7).",id_asli,id_jadwal)
											SELECT ".implode(', ', $oclumns_7).",id as id_asli,".$data_this_id[0]['id_jadwal_lokal']."
											FROM data_output_giat_sub_keg WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords7 = $wpdb->query($sql_backup_data_output_giat_sub_keg);

								$delete_lokal_history = $this->delete_data_lokal_history('data_dana_sub_keg', $data_this_id[0]['id_jadwal_lokal']);

								$oclumns_8 = array('namadana','kodedana','iddana','iddanasubbl','pagudana','kode_sbl','idsubbl','active','update_at','tahun_anggaran');

								$sql_backup_data_dana_sub_keg =  "INSERT INTO data_dana_sub_keg_history (".implode(', ', $oclumns_8).",id_asli,id_jadwal)
											SELECT ".implode(', ', $oclumns_8).",id as id_asli,".$data_this_id[0]['id_jadwal_lokal']."
											FROM data_dana_sub_keg WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords8 = $wpdb->query($sql_backup_data_dana_sub_keg);

								$delete_lokal_history = $this->delete_data_lokal_history('data_lokasi_sub_keg', $data_this_id[0]['id_jadwal_lokal']);

								$oclumns_9 = array('camatteks','daerahteks','idcamat','iddetillokasi','idkabkota','idlurah','lurahteks','kode_sbl','idsubbl','active','update_at','tahun_anggaran');

								$sql_backup_data_lokasi_sub_keg =  "INSERT INTO data_lokasi_sub_keg_history (".implode(', ', $oclumns_9).",id_asli,id_jadwal)
											SELECT ".implode(', ', $oclumns_9).",id as id_asli,".$data_this_id[0]['id_jadwal_lokal']."
											FROM data_lokasi_sub_keg WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords9 = $wpdb->query($sql_backup_data_lokasi_sub_keg);

								$delete_lokal_history = $this->delete_data_lokal_history('data_mapping_sumberdana', $data_this_id[0]['id_jadwal_lokal']);

								$oclumns_10 = array('id_rinci_sub_bl','id_sumber_dana','user','active','update_at','tahun_anggaran');

								$sql_backup_data_mapping_sumberdana =  "INSERT INTO data_mapping_sumberdana_history (".implode(', ', $oclumns_10).",id_asli,id_jadwal)
											SELECT ".implode(', ', $oclumns_10).",id as id_asli,".$data_this_id[0]['id_jadwal_lokal']."
											FROM data_mapping_sumberdana WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords10 = $wpdb->query($sql_backup_data_mapping_sumberdana);

								$delete_lokal_history = $this->delete_data_lokal_history('data_pendapatan', $data_this_id[0]['id_jadwal_lokal']);

								$oclumns_11 = array('created_user','createddate','createdtime','id_pendapatan','keterangan','kode_akun','nama_akun','nilaimurni','program_koordinator','rekening','skpd_koordinator','total','pagu_fmis','updated_user','updateddate','updatedtime','uraian','urusan_koordinator','user1','user2','id_skpd','active','update_at','tahun_anggaran');

								$sql_backup_data_pendapatan =  "INSERT INTO data_pendapatan_history (".implode(', ', $oclumns_11).",id_jadwal,id_asli)
											SELECT ".implode(', ', $oclumns_11).",".$data_this_id[0]['id_jadwal_lokal'].",id as id_asli
											FROM data_pendapatan WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords11 = $wpdb->query($sql_backup_data_pendapatan);

								$delete_lokal_history = $this->delete_data_lokal_history('data_pembiayaan', $data_this_id[0]['id_jadwal_lokal']);

								$oclumns_12 = array('created_user','createddate','createdtime','id_pembiayaan','keterangan','kode_akun','nama_akun','nilaimurni','program_koordinator','rekening','skpd_koordinator','total','pagu_fmis','updated_user','updateddate','updatedtime','uraian','urusan_koordinator','type','user1','user2','id_skpd','active','update_at','tahun_anggaran');

								$sql_backup_data_pembiayaan =  "INSERT INTO data_pembiayaan_history (".implode(', ', $oclumns_12).",id_jadwal,id_asli)
											SELECT ".implode(', ', $oclumns_12).",".$data_this_id[0]['id_jadwal_lokal'].",id as id_asli
											FROM data_pembiayaan WHERE tahun_anggaran='".$data_this_id[0]['tahun_anggaran']."'";

								$queryRecords12 = $wpdb->query($sql_backup_data_pembiayaan);

								$return = array(
									'status' => 'success',
									'message'	=> 'Berhasil!',
									'data_input' => $queryRecords1
								);
							}else{
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "Penjadwalan belum dimulai!",
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit lock data jadwal RPJM */
	public function submit_lock_schedule_rpjm(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['id_jadwal_lokal'])){
					if(in_array("administrator", $user_meta->roles)){
						$id_jadwal_lokal= trim(htmlspecialchars($_POST['id_jadwal_lokal']));

						$data_this_id 	= $wpdb->get_results($wpdb->prepare('SELECT * FROM data_jadwal_lokal WHERE id_jadwal_lokal = %d',$id_jadwal_lokal), ARRAY_A);
						
						$timezone = get_option('timezone_string');
						if(preg_match("/Asia/i", $timezone)){
							date_default_timezone_set($timezone);
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "Pengaturan timezone salah. Pilih salah satu kota di zona waktu yang sama dengan anda, antara lain:  \'Jakarta\',\'Makasar\',\'Jayapura\'",
							);
							die(json_encode($return));
						}

						$dateTime = new DateTime();
						$time_now = $dateTime->format('Y-m-d H:i:s');
						if($time_now > $data_this_id[0]['waktu_awal']){
							$status_check = array(0,NULL,2);
							if(in_array($data_this_id[0]['status'],$status_check)){

								//lock data penjadwalan
								$wpdb->update('data_jadwal_lokal', array('waktu_akhir' => $time_now,'status' => 1), array(
									'id_jadwal_lokal'	=> $id_jadwal_lokal
								));

								$delete_lokal_history = $this->delete_data_lokal_history('data_rpjmd_misi_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_1 = array('id_misi','id_misi_old','id_visi','is_locked','misi_teks','status','urut_misi','visi_lock','visi_teks','update_at','active','tahun_anggaran');
	
								$sql_backup_data_rpjmd_misi_lokal =  "INSERT INTO data_rpjmd_misi_lokal_history (".implode(', ', $columns_1).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_1).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_rpjmd_misi_lokal";

								$queryRecords1 = $wpdb->query($sql_backup_data_rpjmd_misi_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_rpjmd_program_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_2 = array('id_misi','id_misi_old','id_program','id_unik','id_unik_indikator','id_unit','id_visi','indikator','is_locked','is_locked_indikator','kode_sasaran','kode_skpd','kode_tujuan','misi_teks','nama_program','nama_skpd','pagu_1','pagu_2','pagu_3','pagu_4','pagu_5','program_lock','sasaran_lock','sasaran_teks','satuan','status','target_1','target_2','target_3','target_4','target_5','target_akhir','target_awal','tujuan_lock','tujuan_teks','urut_misi','urut_sasaran','urut_tujuan','visi_teks','active','update_at','tahun_anggaran');
	
								$sql_backup_data_rpjmd_program_lokal =  "INSERT INTO data_rpjmd_program_lokal_history (".implode(', ', $columns_2).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_2).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_rpjmd_program_lokal";

								$queryRecords2 = $wpdb->query($sql_backup_data_rpjmd_program_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_rpjmd_sasaran_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_3 = array('id_misi','id_misi_old','id_sasaran','id_unik','id_unik_indikator','id_visi','indikator_teks','is_locked','is_locked_indikator','kode_tujuan','misi_teks','sasaran_teks','satuan','status','target_1','target_2','target_3','target_4','target_5','target_akhir','target_awal','tujuan_lock','tujuan_teks','urut_misi','urut_sasaran','urut_tujuan','visi_teks','active','update_at','tahun_anggaran');
	
								$sql_backup_data_rpjmd_sasaran_lokal =  "INSERT INTO data_rpjmd_sasaran_lokal_history (".implode(', ', $columns_3).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_3).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_rpjmd_sasaran_lokal";

								$queryRecords3 = $wpdb->query($sql_backup_data_rpjmd_sasaran_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_rpjmd_tujuan_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_4 = array('id_misi','id_misi_old','id_tujuan','id_unik','id_unik_indikator','id_visi','indikator_teks','is_locked','is_locked_indikator','misi_lock','misi_teks','satuan','status','target_1','target_2','target_3','target_4','target_5','target_akhir','target_awal','tujuan_teks','urut_misi','urut_tujuan','visi_teks','active','update_at','tahun_anggaran');
	
								$sql_backup_data_rpjmd_tujuan_lokal =  "INSERT INTO data_rpjmd_tujuan_lokal_history (".implode(', ', $columns_4).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_4).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_rpjmd_tujuan_lokal";

								$queryRecords4 = $wpdb->query($sql_backup_data_rpjmd_tujuan_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_rpjmd_visi_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_5 = array('id_visi','is_locked','status','visi_teks','update_at','active','tahun_anggaran');
	
								$sql_backup_data_rpjmd_visi_lokal =  "INSERT INTO data_rpjmd_visi_lokal_history (".implode(', ', $columns_5).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_5).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_rpjmd_visi_lokal";

								$queryRecords5 = $wpdb->query($sql_backup_data_rpjmd_visi_lokal);

								$return = array(
									'status' => 'success',
									'message'	=> 'Berhasil!',
									'data_input' => $queryRecords1
								);
							}else{
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "Penjadwalan belum dimulai!",
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit lock data jadwal RENSTRA */
	public function submit_lock_schedule_renstra(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['id_jadwal_lokal'])){
					if(in_array("administrator", $user_meta->roles)){
						$id_jadwal_lokal= trim(htmlspecialchars($_POST['id_jadwal_lokal']));

						$data_this_id 	= $wpdb->get_results($wpdb->prepare('SELECT * FROM data_jadwal_lokal WHERE id_jadwal_lokal = %d',$id_jadwal_lokal), ARRAY_A);

						$timezone = get_option('timezone_string');
						if(preg_match("/Asia/i", $timezone)){
							date_default_timezone_set($timezone);
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "Pengaturan timezone salah. Pilih salah satu kota di zona waktu yang sama dengan anda, antara lain:  \'Jakarta\',\'Makasar\',\'Jayapura\'",
							);
							die(json_encode($return));
						}

						$dateTime = new DateTime();
						$time_now = $dateTime->format('Y-m-d H:i:s');
						if($time_now > $data_this_id[0]['waktu_awal']){
							$status_check = array(0,NULL,2);
							if(in_array($data_this_id[0]['status'],$status_check)){
								//lock data penjadwalan
								$wpdb->update('data_jadwal_lokal', array('waktu_akhir' => $time_now,'status' => 1), array(
									'id_jadwal_lokal'	=> $id_jadwal_lokal
								));

								$delete_lokal_history = $this->delete_data_lokal_history('data_renstra_kegiatan_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_1 = array('bidur_lock','giat_lock','id_bidang_urusan','id_giat','id_misi','id_program','id_unik','id_unik_indikator','id_unit','id_visi','indikator','indikator_usulan','is_locked','is_locked_indikator','kode_bidang_urusan','kode_giat','kode_program','kode_sasaran','kode_skpd','kode_tujuan','kode_unik_program','nama_bidang_urusan','nama_giat','nama_program','nama_skpd','pagu_1','pagu_2','pagu_3','pagu_4','pagu_5','program_lock','renstra_prog_lock','sasaran_lock','sasaran_teks','satuan','status','target_1','target_2','target_3','target_4','target_5','target_akhir','target_awal','satuan_usulan','target_1_usulan','target_2_usulan','target_3_usulan','target_4_usulan','target_5_usulan','target_akhir_usulan','target_awal_usulan','catatan_usulan','catatan','tujuan_lock','tujuan_teks','urut_sasaran','urut_tujuan','active','update_at','tahun_anggaran');
	
								$sql_backup_data_renstra_kegiatan_lokal =  "INSERT INTO data_renstra_kegiatan_lokal_history (".implode(', ', $columns_1).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_1).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_renstra_kegiatan_lokal";

								$queryRecords1 = $wpdb->query($sql_backup_data_renstra_kegiatan_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_renstra_program_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_2 = array('bidur_lock','id_bidang_urusan','id_misi','id_program','id_unik','id_unik_indikator','id_unit','id_visi','indikator','indikator_usulan','is_locked','is_locked_indikator','kode_bidang_urusan','kode_program','kode_sasaran','kode_skpd','kode_tujuan','nama_bidang_urusan','nama_program','nama_skpd','pagu_1','pagu_2','pagu_3','pagu_4','pagu_5','pagu_1_usulan','pagu_2_usulan','pagu_3_usulan','pagu_4_usulan','pagu_5_usulan','program_lock','sasaran_lock','sasaran_teks','satuan','status','target_1','target_2','target_3','target_4','target_5','target_akhir','target_awal','satuan_usulan','target_1_usulan','target_2_usulan','target_3_usulan','target_4_usulan','target_5_usulan','target_akhir_usulan','target_awal_usulan','catatan_usulan','catatan','tujuan_lock','tujuan_teks','urut_sasaran','urut_tujuan','active','update_at','tahun_anggaran');
	
								$sql_backup_data_renstra_program_lokal =  "INSERT INTO data_renstra_program_lokal_history (".implode(', ', $columns_2).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_2).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_renstra_program_lokal";

								$queryRecords2 = $wpdb->query($sql_backup_data_renstra_program_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_renstra_sasaran_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_3 = array('bidur_lock','id_bidang_urusan','id_misi','id_unit','id_unik_indikator','id_unik','id_visi','indikator_teks','indikator_teks_usulan','is_locked','is_locked_indikator','kode_bidang_urusan','kode_skpd','kode_tujuan','nama_bidang_urusan','nama_skpd','sasaran_teks','satuan','status','target_1','target_2','target_3','target_4','target_5','target_akhir','target_awal','satuan_usulan','target_1_usulan','target_2_usulan','target_3_usulan','target_4_usulan','target_5_usulan','target_akhir_usulan','target_awal_usulan','catatan_usulan','catatan','tujuan_lock','tujuan_teks','urut_sasaran','urut_tujuan','active','update_at','tahun_anggaran');
	
								$sql_backup_data_renstra_sasaran_lokal =  "INSERT INTO data_renstra_sasaran_lokal_history (".implode(', ', $columns_3).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_3).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_renstra_sasaran_lokal";

								$queryRecords3 = $wpdb->query($sql_backup_data_renstra_sasaran_lokal);

								$delete_lokal_history = $this->delete_data_lokal_history('data_renstra_tujuan_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_4 = array('bidur_lock','id_bidang_urusan','id_unik','id_unik_indikator','id_unit','indikator_teks','indikator_teks_usulan','is_locked','is_locked_indikator','kode_bidang_urusan','kode_sasaran_rpjm','kode_skpd','nama_bidang_urusan','nama_skpd','satuan','status','target_1','target_2','target_3','target_4','target_5','target_akhir','target_awal','satuan_usulan','target_1_usulan','target_2_usulan','target_3_usulan','target_4_usulan','target_5_usulan','target_akhir_usulan','target_awal_usulan','catatan_usulan','catatan','tujuan_teks','urut_tujuan','active','update_at','tahun_anggaran');
	
								$sql_backup_data_renstra_tujuan_lokal =  "INSERT INTO data_renstra_tujuan_lokal_history (".implode(', ', $columns_4).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_4).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_renstra_tujuan_lokal";

								$queryRecords4 = $wpdb->query($sql_backup_data_renstra_tujuan_lokal);

								$return = array(
									'status' => 'success',
									'message'	=> 'Berhasil!',
									'data_input' => $queryRecords1
								);
							}else{
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "Penjadwalan belum dimulai!",
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	/** Submit lock data jadwal RPD */
	public function submit_lock_schedule_rpd(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['id_jadwal_lokal'])){
					if(in_array("administrator", $user_meta->roles)){
						$id_jadwal_lokal= trim(htmlspecialchars($_POST['id_jadwal_lokal']));

						$data_this_id 	= $wpdb->get_results($wpdb->prepare('SELECT * FROM data_jadwal_lokal WHERE id_jadwal_lokal = %d',$id_jadwal_lokal), ARRAY_A);

						$timezone = get_option('timezone_string');
						if(preg_match("/Asia/i", $timezone)){
							date_default_timezone_set($timezone);
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "Pengaturan timezone salah. Pilih salah satu kota di zona waktu yang sama dengan anda, antara lain:  \'Jakarta\',\'Makasar\',\'Jayapura\'",
							);
							die(json_encode($return));
						}

						$dateTime = new DateTime();
						$time_now = $dateTime->format('Y-m-d H:i:s');
						if($time_now > $data_this_id[0]['waktu_awal']){
							$status_check = array(0,NULL,2);
							if(in_array($data_this_id[0]['status'],$status_check)){
								//lock data penjadwalan
								$wpdb->update('data_jadwal_lokal', array('waktu_akhir' => $time_now,'status' => 1), array(
									'id_jadwal_lokal'	=> $id_jadwal_lokal
								));

								$delete_tujuan_lokal_history = $this->delete_data_lokal_history('data_rpd_tujuan_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_1 = array('head_teks','id_misi_old','id_tujuan','id_unik','id_unik_indikator','indikator_teks','is_locked','is_locked_indikator','isu_teks','kebijakan_teks','misi_lock','misi_teks','saspok_teks','satuan','status','target_1','target_2','target_3','target_4','target_5','target_akhir','target_awal','tujuan_teks','urut_misi','urut_saspok','urut_tujuan','visi_teks','id_isu','update_at','no_urut','catatan_teks_tujuan','indikator_catatan_teks','active');
	
								$sql_backup_data_rpd_tujuan_lokal =  "INSERT INTO data_rpd_tujuan_lokal_history (".implode(', ', $columns_1).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_1).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_rpd_tujuan_lokal";

								$queryRecords1 = $wpdb->query($sql_backup_data_rpd_tujuan_lokal);

								$delete_sasaran_lokal_history = $this->delete_data_lokal_history('data_rpd_sasaran_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_2 = array('head_teks','id_misi_old','id_sasaran','id_unik','id_unik_indikator','indikator_teks','is_locked','is_locked_indikator','isu_teks','kebijakan_teks','kode_tujuan','misi_lock','misi_teks','sasaran_teks','saspok_teks','satuan','status','target_1','target_2','target_3','target_4','target_5','target_akhir','target_awal','tujuan_lock','tujuan_teks','urut_misi','urut_sasaran','urut_saspok','urut_tujuan','visi_teks','update_at','sasaran_no_urut','sasaran_catatan','indikator_catatan_teks','id_visi','id_misi','active');
	
								$sql_backup_data_rpd_sasaran_lokal =  "INSERT INTO data_rpd_sasaran_lokal_history (".implode(', ', $columns_2).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_2).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_rpd_sasaran_lokal";

								$queryRecords2 = $wpdb->query($sql_backup_data_rpd_sasaran_lokal);

								$delete_rpd_program_lokal_history = $this->delete_data_lokal_history('data_rpd_program_lokal', $data_this_id[0]['id_jadwal_lokal']);

								$columns_3 = array('head_teks','id_bidur_mth','id_misi_old','id_program','id_program_mth','id_unik','id_unik_indikator','id_unit','indikator','is_locked','is_locked_indikator','isu_teks','kebijakan_teks','kode_sasaran','kode_skpd','kode_tujuan','misi_lock','misi_teks','nama_program','nama_skpd','pagu_1','pagu_2','pagu_3','pagu_4','pagu_5','program_lock','sasaran_lock','sasaran_teks','saspok_teks','satuan','status','target_1','target_2','target_3','target_4','target_5','target_akhir','target_awal','tujuan_lock','tujuan_teks','urut_misi','urut_sasaran','urut_saspok','urut_tujuan','visi_teks','update_at','id_visi','id_misi','active');
	
								$sql_backup_data_rpd_program_lokal =  "INSERT INTO data_rpd_program_lokal_history (".implode(', ', $columns_3).",id_jadwal,id_asli)
											SELECT ".implode(', ', $columns_3).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
											FROM data_rpd_program_lokal";

								$queryRecords3 = $wpdb->query($sql_backup_data_rpd_program_lokal);

								$return = array(
									'status' => 'success',
									'message'	=> 'Berhasil!',
									'data_input' => $queryRecords1
								);
							}else{
								$return = array(
									'status' => 'error',
									'message'	=> "User tidak diijinkan!\nData sudah dikunci!",
								);
							}
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> "Penjadwalan belum dimulai!",
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,tidak boleh ada yang kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	public function delete_data_lokal_history($nama_tabel = 'data_rpd_tujuan_lokal', $id_jadwal = 1){
		global $wpdb;
		$return = array(
			'status' => 'error',
			'message'	=> 'Format tidak sesuai!'
		);

		$nama_tabel_history = $nama_tabel."_history";

		$delete = $wpdb->delete($nama_tabel_history, array('id_jadwal' => $id_jadwal));
		if($delete == false){
			$return = array(
				'status' 	=> 'error',
				'message'	=> 'Delete error, harap hubungi admin!'
			);
		}

		return $return;
	}

	public function get_kontrak_addendum(){
		global $wpdb;
		$ret = array(
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$sql = $wpdb->prepare("
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
			}else{
				$ret = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$ret = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	public function run_sql_migrate(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil menjalankan SQL migrate!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$file = basename($_POST['file']);
				$ret['value'] = $file.' (tgl: '.date('Y-m-d H:i:s').')';
				if($file == 'tabel.sql'){
					$path = WPSIPD_PLUGIN_PATH.'/'.$file;
				}else{
					$path = WPSIPD_PLUGIN_PATH.'/sql-migrate/'.$file;
				}
				if(file_exists($path)){
					$sql = file_get_contents($path);
					$ret['sql'] = $sql;
					if($file == 'tabel.sql'){
						require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
						$wpdb->hide_errors();
						$rows_affected = dbDelta($sql);
						if(empty($rows_affected)){
							$ret['status'] = 'error';
							$ret['message'] = $wpdb->last_error;
						}else{
							$ret['message'] = implode(' | ', $rows_affected);
						}
					}else{
						$wpdb->hide_errors();
						$res = $wpdb->query($sql);
						if(empty($res)){
							$ret['status'] = 'error';
							$ret['message'] = $wpdb->last_error;
						}else{
							$ret['message'] = $res;
						}
					}
					if($ret['status'] == 'success'){
						$ret['version'] = $this->version;
						update_option('_last_update_sql_migrate', $ret['value']);
						update_option('_wp_sipd_db_version', $this->version);
					}
				}else{
					$ret['status'] = 'error';
					$ret['message'] = 'File '.$file.' tidak ditemukan!';
				}
			}else{
				$ret = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$ret = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	public function singkron_rpjmd_sipd_lokal(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil mengambil data RPJMD dari data SIPD lokal!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
					if(!empty($id_cek)){
						$wpdb->update($table, $data, array('id' => $id_cek));
					}else{
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
						if(!empty($id_cek)){
							$wpdb->update($table, $data, array('id' => $id_cek));
						}else{
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
							if(!empty($id_cek)){
								$wpdb->update($table, $data, array('id' => $id_cek));
							}else{
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
									'id_misi_old'=> $sasaran['id_misi_old'],
									'id_sasaran'=> $sasaran['id_sasaran'],
									'id_unik'=> $sasaran['id_unik'],
									'id_unik_indikator'=> $sasaran['id_unik_indikator'],
									'id_visi'=> $sasaran['id_visi'],
									'indikator_teks'=> $sasaran['indikator_teks'],
									'is_locked'=> $sasaran['is_locked'],
									'is_locked_indikator'=> $sasaran['is_locked_indikator'],
									'kode_tujuan'=> $sasaran['kode_tujuan'],
									'misi_teks'=> $sasaran['misi_teks'],
									'sasaran_teks'=> $sasaran['sasaran_teks'],
									'satuan'=> $sasaran['satuan'],
									'status'=> $sasaran['status'],
									'target_1'=> $sasaran['target_1'],
									'target_2'=> $sasaran['target_2'],
									'target_3'=> $sasaran['target_3'],
									'target_4'=> $sasaran['target_4'],
									'target_5'=> $sasaran['target_5'],
									'target_akhir'=> $sasaran['target_akhir'],
									'target_awal'=> $sasaran['target_awal'],
									'tujuan_lock'=> $sasaran['tujuan_lock'],
									'tujuan_teks'=> $sasaran['tujuan_teks'],
									'urut_misi'=> $sasaran['urut_misi'],
									'urut_sasaran'=> $sasaran['urut_sasaran'],
									'urut_tujuan'=> $sasaran['urut_tujuan'],
									'visi_teks'=> $sasaran['visi_teks'],
									'update_at'=> $sasaran['update_at']
								);
								if(!empty($id_cek)){
									$wpdb->update($table, $data, array('id' => $id_cek));
								}else{
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
									if(!empty($id_cek)){
										$wpdb->update($table, $data, array('id' => $id_cek));
									}else{
										$wpdb->insert($table, $data);
									}
								}
							}
						}
					}
				}
			}else{
				$ret = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$ret = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	public function singkron_rpd_sipd_lokal(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil mengambil data RPD dari data SIPD lokal!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
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
					if(!empty($id_cek)){
						$wpdb->update($table, $data, array('id' => $id_cek));
					}else{
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
						if(!empty($id_cek)){
							$wpdb->update($table, $data, array('id' => $id_cek));
						}else{
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
							if(!empty($id_cek)){
								$wpdb->update($table, $data, array('id' => $id_cek));
							}else{
								$wpdb->insert($table, $data);
							}
						}
					}
				}
			}else{
				$ret = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$ret = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	function get_visi_rpjm(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil get visi RPJM!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$type = $_POST['type'];
				if($type == 1){
					$sql = $wpdb->prepare("
						select 
							* 
						from data_rpjmd_visi_lokal
						where is_locked=0
							AND status=1
							AND active=1
					");
					$ret['data'] = $wpdb->get_results($sql, ARRAY_A);
				}else{
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
			}else{
				$ret = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$ret = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	function validasi_jadwal_perencanaan($tipe_perencanaan){
		global $wpdb;

		$data_return = array(
			'status' => 200,
			'message' => "Berhasil"
		);

		if(!empty($tipe_perencanaan)){
			date_default_timezone_set("Asia/Bangkok");
			$dateTime = new DateTime();
			$time_now = $dateTime->format('Y-m-d H:i:s');

			$sql_tipe = $wpdb->get_results("SELECT * FROM `data_tipe_perencanaan` WHERE nama_tipe='".$tipe_perencanaan."'", ARRAY_A);

			// get jadwal aktif dan terbuka
			$sql_jadwal_lokal = $wpdb->get_results("
				SELECT 
					* 
				FROM `data_jadwal_lokal` 
				WHERE status = 0 
					AND (
						waktu_awal < '".$time_now."' 
						AND waktu_akhir > '".$time_now."'
					) AND id_tipe='".$sql_tipe[0]['id']."'
			", ARRAY_A);

			if(!empty($sql_jadwal_lokal)){
				$data_return = array(
					'status' 	=> 'success',
					'message'	=> "Berhasil",
					'data'		=> $sql_jadwal_lokal
				);
			}else{
				// get jadwal aktif
				$sql_jadwal_lokal = $wpdb->get_results("
					SELECT 
						* 
					FROM `data_jadwal_lokal` 
					WHERE status = 0 
						AND id_tipe='".$sql_tipe[0]['id']."'
				", ARRAY_A);
				if(empty($sql_jadwal_lokal)){
					// get jadwal terakhir sesuai tipe
					$sql_jadwal_lokal = $wpdb->get_results("
						SELECT 
							* 
						FROM `data_jadwal_lokal` 
						WHERE id_tipe='".$sql_tipe[0]['id']."'
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
			
		}else{
			$data_return = array(
				'status' 	=> 'error',
				'message' 	=> "Gagal, tipe perencanaan tidak ada",
				'data'		=> ''
			);
		}

		return $data_return;
	}

	function edit_visi_rpjm(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				$sql=$wpdb->prepare("
					SELECT * FROM data_rpjmd_visi_lokal
					WHERE id=%d
				", $_POST['id']);
				$visi = $wpdb->get_row($sql);

				echo json_encode([
					'status' => true,
					'data' => $visi,
					'message' => 'Sukses get visi by id'
				]);exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);exit;
	}

	function submit_visi_rpjm(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				$data = json_decode(stripslashes($_POST['data']), true);
				
				if(empty($data['visi_teks'])){
					echo json_encode([
						'status' => false,
						'message' => 'Visi tidak boleh kosong!'
					]);exit;
				}

				$sql = $wpdb->prepare("SELECT id FROM data_rpjmd_visi_lokal
						WHERE visi_teks=%s 
							AND is_locked=0
							AND status=1
							AND active=1", trim($data['visi_teks']));

				$id_cek = $wpdb->get_var($sql);

				if(!empty($id_cek)){
					echo json_encode([
						'status' => false,
						'message' => 'Visi : '.$data['visi_teks'].' sudah ada!'
					]);exit;
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
				]);exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);exit;
	}

	function update_visi_rpjm(){

		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				$data = json_decode(stripslashes($_POST['data']), true);

				$data['type'] = 1;
				
				if(empty($data['visi_teks'])){
					echo json_encode([
						'status' => false,
						'message' => 'Visi tidak boleh kosong!'
					]);exit;
				}

				$sql = $wpdb->prepare("SELECT id FROM data_rpjmd_visi_lokal
						WHERE visi_teks=%s
							AND id!=%d
							AND is_locked=0
							AND status=1
							AND active=1", trim($data['visi_teks']), $data['id_visi']);

				$id_cek = $wpdb->get_var($sql);
				
				if(!empty($id_cek)){
					echo json_encode([
						'status' => false,
						'message' => 'Visi : '.$data['visi_teks'].' sudah ada!'
					]);exit;
				}

				$wpdb->update('data_rpjmd_visi_lokal', [
					'visi_teks' => $data['visi_teks']
				], [
					'id' => $data['id_visi']
				]);

				echo json_encode([
					'status' => true,
					'message' => 'Sukses ubah visi'
				]);exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);exit;
	}

	function delete_visi_rpjm(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_misi_lokal WHERE id_visi=%d AND is_locked=0 AND status=1 AND active=1", $_POST['id_visi']));

					if(!empty($id_cek)){
						throw new Exception("Visi sudah digunakan oleh misi", 1);
					}

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_visi_lokal WHERE id=%d", $_POST['id_visi']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus visi'
					]);exit;

				}else{
					throw new Exception("Api key tidak sesuai", 1);
				}
			}else{
				throw new Exception("Format tidak sesuai", 1);
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function get_misi_rpjm(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				if($_POST['type'] == 1){

					$sql = $wpdb->prepare("
						SELECT * FROM data_rpjmd_misi_lokal
							WHERE id_visi=%d and active=1 ORDER BY urut_misi", $_POST['id_visi']);
					$misi = $wpdb->get_results($sql, ARRAY_A);

				}else{

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
				]);exit;
			}
			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);exit;
		}
		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);exit;
	}

	function submit_misi_rpjm(){

		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$data['type'] = 1;

					if(empty($data['id_visi'])){
						throw new Exception('Visi wajib dipilih!');
					}

					if(empty($data['misi_teks'])){
						throw new Exception('Misi tidak boleh kosong!');
					}

					if(empty($data['urut_misi'])){
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
					
					if(!empty($id_cek)){
						throw new Exception('Misi : '.$data['misi_teks'].' sudah ada!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $data['id_visi']));
					
					if(empty($dataVisi)){
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
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}	
	}

	function edit_misi_rpjm(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				$misi = $wpdb->get_row($wpdb->prepare("
					SELECT * FROM data_rpjmd_misi_lokal
						WHERE id=%d", $_POST['id']));

				echo json_encode([
					'status' => true,
					'misi' => $misi,
					'message' => 'Sukses get misi by id'
				]);exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);exit;
	}

	function update_misi_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$data['type'] = 1;

					if(empty($data['id_visi'])){
						throw new Exception('Visi wajib dipilih!');
					}

					if(empty($data['misi_teks'])){
						throw new Exception('Misi tidak boleh kosong!');
					}

					if(empty($data['urut_misi'])){
						throw new Exception('Urut misi tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_misi_lokal
							WHERE misi_teks=%s 
									AND id!=%d
									AND is_locked=0
									AND status=1
									AND active=1", trim($data['misi_teks']), $data['id_misi']));
					
					if(!empty($id_cek)){
						throw new Exception('Misi : '.$data['misi_teks'].' sudah ada!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $data['id_visi']));

					if(empty($dataVisi)){
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
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function delete_misi_rpjm(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id_misi=%d AND is_locked=0 AND status=1 AND active=1", $_POST['id_misi']));

					if(!empty($id_cek)){
						throw new Exception("Misi sudah digunakan oleh tujuan", 1);
					}

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_misi_lokal WHERE id=%d", $_POST['id_misi']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus misi'
					]);exit;

				}else{
					throw new Exception("Api key tidak sesuai", 1);
				}
			}else{
				throw new Exception("Format tidak sesuai", 1);
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function get_tujuan_rpjm(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				if($_POST['type'] == 1){

					$sql = $wpdb->prepare("
						SELECT * FROM data_rpjmd_tujuan_lokal
							WHERE id_misi=%d AND
								id_unik IS NOT NULL AND
								id_unik_indikator IS NULL AND
								status=1 AND 
								is_locked=0 AND 
								active=1 ORDER BY id", $_POST['id_misi']);
					$tujuan = $wpdb->get_results($sql, ARRAY_A);

				}else{

					$tahun_anggaran = $input['tahun_anggaran'];

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
				]);exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);exit;		
	}

	function edit_tujuan_rpjm(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				$tujuan = $wpdb->get_row($wpdb->prepare("
					SELECT * FROM data_rpjmd_tujuan_lokal
						WHERE id=%d", $_POST['id_tujuan']));

				echo json_encode([
					'status' => true,
					'tujuan' => $tujuan,
					'message' => 'Sukses get tujuan by id'
				]);exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);exit;
	}

	function submit_tujuan_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					if(empty($data['id_misi'])){
						throw new Exception('Misi wajib dipilih!');
					}

					if(empty($data['tujuan_teks'])){
						throw new Exception('Tujuan tidak boleh kosong!');
					}

					if(empty($data['urut_tujuan'])){
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
					
					if(!empty($id_cek)){
						throw new Exception('Tujuan : '.$data['tujuan_teks'].' sudah ada!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $data['id_misi']));

					if(empty($dataMisi)){
						throw new Exception('Misi yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
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

					if(!$status){
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan tujuan',
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function update_tujuan_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					if(empty($data['id_misi'])){
						throw new Exception('Misi wajib dipilih!');
					}

					if(empty($data['tujuan_teks'])){
						throw new Exception('Tujuan tidak boleh kosong!');
					}

					if(empty($data['urut_tujuan'])){
						throw new Exception('Urut tujuan tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_rpjmd_tujuan_lokal
							WHERE tujuan_teks=%s
										AND id!=%d
										AND id_misi=".$data['id_misi']."
										AND id_unik is not null
										AND id_unik_indikator is null
										AND is_locked=0
										AND status=1
										AND active=1
								", trim($data['tujuan_teks']), $data['id_tujuan']));
					
					if(!empty($id_cek)){
						throw new Exception('Tujuan : '.$data['tujuan_teks'].' sudah ada!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $data['id_misi']));

					if(empty($dataMisi)){
						throw new Exception('Misi yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
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
						]);exit;

					} catch (Exception $e) {

						$wpdb->query('ROLLBACK');
						
						throw $e; 
					}

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function delete_tujuan_rpjm(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_sasaran_lokal WHERE kode_tujuan=%s AND is_locked=0 AND status=1 AND active=1", $_POST['kode_tujuan']));

					if(!empty($id_cek)){
						throw new Exception("Tujuan sudah digunakan oleh sasaran", 1);
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id_tujuan=%d AND id_unik IS NOT NULL AND id_unik_indikator IS NOT NULL AND is_locked=0 AND status=1 AND active=1", $_POST['id_tujuan']));

					if(!empty($id_cek)){
						throw new Exception("Tujuan sudah digunakan oleh indikator tujuan", 1);
					}

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_tujuan_lokal WHERE id=%d", $_POST['id_tujuan']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus tujuan'
					]);exit;

				}else{
					throw new Exception("Api key tidak sesuai", 1);
				}
			}else{
				throw new Exception("Format tidak sesuai", 1);
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function get_indikator_tujuan_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					if($_POST['type']==1){
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
					}else{

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
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function submit_indikator_tujuan_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
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
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['id_tujuan']));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if(empty($dataMisi)){
						throw new Exception('Misi yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
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

					if(!$status){
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator tujuan'
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function edit_indikator_tujuan_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$indikator = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id=%d AND id_tujuan=%d AND id_unik IS NOT NULL AND id_unik_indikator IS NOT NULL AND is_locked_indikator=0 AND status=1 AND active=1", $_POST['id'], $_POST['id_tujuan']));

					echo json_encode([
						'status' => true,
						'data' => $indikator
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function update_indikator_tujuan_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
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
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_tujuan_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['id_tujuan']));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if(empty($dataMisi)){
						throw new Exception('Misi yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
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

					if(!$status){
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses ubah indikator tujuan'
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function delete_indikator_tujuan_rpjm(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_tujuan_lokal WHERE id=%d AND id_unik_indikator IS NOT NULL AND active=1 AND status=1", $_POST['id']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator tujuan'
					]);exit;

				}else{
					throw new Exception("Api key tidak sesuai", 1);
				}
			}else{
				throw new Exception("Format tidak sesuai", 1);
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function verify_indikator_tujuan_rpjm(array $data){
		if(empty($data['id_tujuan'])){
			throw new Exception('Tujuan wajib dipilih!');
		}

		if(empty($data['indikator_teks'])){
			throw new Exception('Indikator tujuan tidak boleh kosong!');
		}

		if(empty($data['satuan'])){
			throw new Exception('Satuan indikator tujuan tidak boleh kosong!');
		}

		if(empty($data['target_1'])){
			throw new Exception('Target Indikator tujuan tahun ke-1 tidak boleh kosong!');
		}

		if(empty($data['target_2'])){
			throw new Exception('Target Indikator tujuan tahun ke-2 tidak boleh kosong!');
		}

		if(empty($data['target_3'])){
			throw new Exception('Target Indikator tujuan tahun ke-3 tidak boleh kosong!');
		}

		if(empty($data['target_4'])){
			throw new Exception('Target Indikator tujuan tahun ke-4 tidak boleh kosong!');
		}

		if(empty($data['target_5'])){
			throw new Exception('Target Indikator tujuan tahun ke-5 tidak boleh kosong!');
		}

		if(empty($data['target_awal'])){
			throw new Exception('Target awal Indikator tujuan tidak boleh kosong!');
		}

		if(empty($data['target_akhir'])){
			throw new Exception('Target akhir Indikator tujuan tidak boleh kosong!');
		}		
	}

	function get_sasaran_rpjm(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				if($_POST['type'] == 1){
					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_rpjmd_sasaran_lokal
						WHERE is_locked=0
							AND kode_tujuan='".$_POST['kode_tujuan']."'
							AND id_unik IS NOT NULL
							AND id_unik_indikator IS NULL
							AND is_locked=0
							AND status=1
							AND active=1
					");
					$sasaran = $wpdb->get_results($sql, ARRAY_A);
				}else{
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
				]);exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);exit;		
	}

	function submit_sasaran_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					if(empty($data['kode_tujuan'])){
						throw new Exception('Tujuan wajib dipilih!');
					}

					if(empty($data['sasaran_teks'])){
						throw new Exception('Sasaran tidak boleh kosong!');
					}

					if(empty($data['urut_sasaran'])){
						throw new Exception('Urut sasaran tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT id FROM data_rpjmd_sasaran_lokal WHERE sasaran_teks=%s AND kode_tujuan=%s AND is_locked=0 AND status=1 AND active=1
					", $data['sasaran_teks'], $data['kode_tujuan']));
					
					if(!empty($id_cek)){
						throw new Exception('Sasaran : '.$data['sasaran_teks'].' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['kode_tujuan']));

					if(empty($dataTujuan)){
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if(empty($dataMisi)){
						throw new Exception('Misi dari tujuan yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
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

					if(!$status){
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan sasaran'
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function edit_sasaran_rpjm(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

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
				]);exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);exit;
	}

	function update_sasaran_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					if(empty($data['kode_tujuan'])){
						throw new Exception('Tujuan wajib dipilih!');
					}

					if(empty($data['sasaran_teks'])){
						throw new Exception('Sasaran tidak boleh kosong!');
					}

					if(empty($data['urut_sasaran'])){
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
					
					if(!empty($id_cek)){
						throw new Exception('Sasaran : '.$data['sasaran_teks'].' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['kode_tujuan']));

					if(empty($dataTujuan)){
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if(empty($dataMisi)){
						throw new Exception('Sasaran tidak terhubung ke Misi, mohon cek relasi antara tujuan dengan misi Rpjm!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
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
						]);exit;

					} catch (Exception $e) {

						$wpdb->query('ROLLBACK');

						throw $e;	
					}
				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function delete_sasaran_rpjm(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE kode_sasaran=%s AND is_locked=0 AND status=1 AND active=1", $_POST['kode_sasaran']));

					if(!empty($id_cek)){
						throw new Exception("Sasaran sudah digunakan oleh program", 1);
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_sasaran_lokal WHERE id_sasaran=%d AND id_unik IS NOT NULL AND id_unik_indikator IS NOT NULL AND is_locked=0 AND status=1 AND active=1", $_POST['id_sasaran']));

					if(!empty($id_cek)){
						throw new Exception("Sasaran sudah digunakan oleh indikator sasaran", 1);
					}

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_sasaran_lokal WHERE id=%d", $_POST['id_sasaran']));


					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus sasaran'
					]);exit;

				}else{
					throw new Exception("Api key tidak sesuai", 1);
				}
			}else{
				throw new Exception("Format tidak sesuai", 1);
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function get_indikator_sasaran_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					if($_POST['type'] == 1){
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
					}else{
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
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function submit_indikator_sasaran_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
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
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
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

					if(empty($dataMisi)){
						throw new Exception('Misi terkoneksi dengan tujuan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
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

					if(!$status){
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator sasaran'
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function edit_indikator_sasaran_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$indikator = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_sasaran_lokal WHERE id=%d AND id_sasaran=%d AND id_unik IS NOT NULL AND id_unik_indikator IS NOT NULL AND is_locked_indikator=0 AND status=1 AND active=1", $_POST['id'], $_POST['id_sasaran']));

					echo json_encode([
						'status' => true,
						'data' => $indikator
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function update_indikator_sasaran_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
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
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
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

					if(empty($dataMisi)){
						throw new Exception('Misi yang dipilih tidak ditemukan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
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

					if(!$status){
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses ubah indikator sasaran'
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function delete_indikator_sasaran_rpjm(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_sasaran_lokal WHERE id=%d AND id_unik_indikator is not null AND active=1 AND status=1", $_POST['id']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator sasaran'
					]);exit;

				}else{
					throw new Exception("Api key tidak sesuai", 1);
				}
			}else{
				throw new Exception("Format tidak sesuai", 1);
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function verify_indikator_sasaran_rpjm(array $data){
		if(empty($data['id_sasaran'])){
			throw new Exception('Sasaran wajib dipilih!');
		}

		if(empty($data['indikator_teks'])){
			throw new Exception('Indikator sasaran tidak boleh kosong!');
		}

		if(empty($data['satuan'])){
			throw new Exception('Satuan indikator sasaran tidak boleh kosong!');
		}

		if(empty($data['target_1'])){
			throw new Exception('Target Indikator sasaran tahun ke-1 tidak boleh kosong!');
		}

		if(empty($data['target_2'])){
			throw new Exception('Target Indikator sasaran tahun ke-2 tidak boleh kosong!');
		}

		if(empty($data['target_3'])){
			throw new Exception('Target Indikator sasaran tahun ke-3 tidak boleh kosong!');
		}

		if(empty($data['target_4'])){
			throw new Exception('Target Indikator sasaran tahun ke-4 tidak boleh kosong!');
		}

		if(empty($data['target_5'])){
			throw new Exception('Target Indikator sasaran tahun ke-5 tidak boleh kosong!');
		}

		if(empty($data['target_awal'])){
			throw new Exception('Target awal Indikator sasaran tidak boleh kosong!');
		}

		if(empty($data['target_akhir'])){
			throw new Exception('Target akhir Indikator sasaran tidak boleh kosong!');
		}		
	}

	function get_program_rpjm(){

		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if($_POST['type'] == 1){
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
				}else{
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
				]);exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);exit;
	}

	function submit_program_rpjm(){
		
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
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
					
					if(!empty($id_cek)){
						throw new Exception('Program sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("SELECT id_unik, sasaran_teks, is_locked AS sasaran_lock, urut_sasaran, kode_tujuan FROM data_rpjmd_sasaran_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $data['kode_sasaran']));

					if(empty($dataSasaran)){
						throw new Exception('Sasaran belum dipilih!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataSasaran->kode_tujuan));

					if(empty($dataTujuan)){
						throw new Exception('Sasaran tidak terkoneksi dengan tujuan, cek Sasaran!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if(empty($dataMisi)){
						throw new Exception('Misi tidak terkoneksi dengan tujuan, cek Tujuan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
						throw new Exception('Visi tidak terkoneksi dengan misi, cek Misi!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program
                        FROM data_prog_keg as u 
                        WHERE u.tahun_anggaran=".get_option('_crb_tahun_anggaran_sipd')." AND id_program=%d
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ", $data['id_program']));

					if(empty($dataProgram)){
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
						]);exit;

					} catch (Exception $e) {
						throw $e;										
					}

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function edit_program_rpjm(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

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
				]);exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);exit;
	}

	function update_program_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
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
					
					if(!empty($id_cek)){
						throw new Exception('Program sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("SELECT id_unik, sasaran_teks, is_locked AS sasaran_lock, urut_sasaran, kode_tujuan FROM data_rpjmd_sasaran_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $data['kode_sasaran']));

					if(empty($dataSasaran)){
						throw new Exception('Sasaran tidak ditemukan!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataSasaran->kode_tujuan));

					if(empty($dataTujuan)){
						throw new Exception('Sasaran tidak terkoneksi dengan tujuan, cek Sasaran');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if(empty($dataMisi)){
						throw new Exception('Misi tidak terkoneksi dengan tujuan, cek Tujuan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
						throw new Exception('Visi tidak terkoneksi dengan misi, cek Misi!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program
                        FROM data_prog_keg as u 
                        WHERE u.tahun_anggaran=".get_option('_crb_tahun_anggaran_sipd')." AND id_program=%d
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ", $data['id_program']));

					if(empty($dataProgram)){
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
						]);exit;

					} catch (Exception $e) {
						throw $e;										
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses ubah program'
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function delete_program_rpjm(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$id_cek = $wpdb->get_var($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE id_unik=%s AND id_unik is not null AND id_unik_indikator is not null AND is_locked=0 AND status=1 AND active=1", $_POST['kode_program']));

					if(!empty($id_cek)){
						throw new Exception("Program sudah digunakan oleh indikator program", 1);
					}

					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_program_lokal WHERE id_unik=%d", $_POST['kode_program']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus program'
					]);exit;

				}else{
					throw new Exception("Api key tidak sesuai", 1);
				}
			}else{
				throw new Exception("Format tidak sesuai", 1);
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function verify_program_rpjm(array $data){
		if(empty($data['kode_sasaran'])){
			throw new Exception('Sasaran wajib dipilih!');
		}

		if(empty($data['id_urusan'])){
			throw new Exception('Urusan wajib dipilih');
		}

		if(empty($data['id_bidang'])){
			throw new Exception('Bidang wajib dipilih!');
		}

		if(empty($data['id_program'])){
			throw new Exception('Program wajib dipilih');
		}

	}

	function get_indikator_program_rpjm(){
		
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					if($_POST['type'] == 1){
						$sql = $wpdb->prepare("
							SELECT * FROM data_rpjmd_program_lokal 
								WHERE 
									id_unik=%s AND 
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND 
									status=1 AND 
									active=1", $_POST['kode_program']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					}else{
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
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function submit_indikator_program_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
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
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['kode_program']));

					if (empty($dataProgram)) {
						throw new Exception('Program yang dipilih tidak ditemukan!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("SELECT id_unik, sasaran_teks, is_locked AS sasaran_lock, urut_sasaran, kode_tujuan FROM data_rpjmd_sasaran_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataProgram->kode_sasaran));

					if(empty($dataSasaran)){
						throw new Exception('Sasaran belum dipilih!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataSasaran->kode_tujuan));

					if(empty($dataTujuan)){
						throw new Exception('Sasaran tidak terkoneksi dengan tujuan, cek Sasaran!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if(empty($dataMisi)){
						throw new Exception('Misi tidak terkoneksi dengan tujuan, cek Tujuan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
						throw new Exception('Visi tidak terkoneksi dengan misi, cek Misi!');
					}

					$dataUnit = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_unit WHERE id_unit=%d AND tahun_anggaran=%d AND active=1 AND is_skpd=1 order by id_skpd ASC;", $data['id_unit'], get_option('_crb_tahun_anggaran_sipd')));

					if(empty($dataUnit)){
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
						]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function edit_indikator_program_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$indikator = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE id=%d AND id_unik=%s AND id_unik_indikator is not null AND is_locked_indikator=0 AND status=1 AND active=1", $_POST['id'], $_POST['kode_program']));

					echo json_encode([
						'status' => true,
						'data' => $indikator
					]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function update_indikator_program_rpjm(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
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
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL", $data['kode_program']));

					if (empty($dataProgram)) {
						throw new Exception('Program yang dipilih tidak ditemukan!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("SELECT id_unik, sasaran_teks, is_locked AS sasaran_lock, urut_sasaran, kode_tujuan FROM data_rpjmd_sasaran_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataProgram->kode_sasaran));

					if(empty($dataSasaran)){
						throw new Exception('Sasaran belum dipilih!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("SELECT id_unik, tujuan_teks, is_locked AS tujuan_lock, id_misi, urut_tujuan FROM data_rpjmd_tujuan_lokal WHERE id_unik=%s AND is_locked=0 AND status=1 AND active=1", $dataSasaran->kode_tujuan));

					if(empty($dataTujuan)){
						throw new Exception('Sasaran tidak terkoneksi dengan tujuan, cek Sasaran!');
					}

					$dataMisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_misi, misi_teks, is_locked AS misi_lock, id_visi, urut_misi FROM data_rpjmd_misi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataTujuan->id_misi));

					if(empty($dataMisi)){
						throw new Exception('Misi tidak terkoneksi dengan tujuan, cek Tujuan!');
					}

					$dataVisi = $wpdb->get_row($wpdb->prepare("SELECT id AS id_visi, visi_teks, is_locked AS visi_lock FROM data_rpjmd_visi_lokal WHERE id=%d AND is_locked=0 AND status=1 AND active=1", $dataMisi->id_visi));

					if(empty($dataVisi)){
						throw new Exception('Visi tidak terkoneksi dengan misi, cek Misi!');
					}

					$dataUnit = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_unit WHERE id_unit=%d AND tahun_anggaran=%d AND active=1 AND is_skpd=1 order by id_skpd ASC;", $data['id_unit'], get_option('_crb_tahun_anggaran_sipd')));

					if(empty($dataUnit)){
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
						]);exit;

				}else{
					throw new Exception('Api key tidak sesuai');
				}
			}else{
				throw new Exception('Format tidak sesuai');
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function delete_indikator_program_rpjm(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$wpdb->get_results($wpdb->prepare("DELETE FROM data_rpjmd_program_lokal WHERE id=%d AND id_unik_indikator is not null AND active=1 AND status=1", $_POST['id']));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator program',
					]);exit;

				}else{
					throw new Exception("Api key tidak sesuai", 1);
				}
			}else{
				throw new Exception("Format tidak sesuai", 1);
			}
		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
	}

	function verify_indikator_program_rpjm(array $data){
		if(empty($data['kode_program'])){
			throw new Exception('Program wajib dipilih!');
		}

		if(empty($data['indikator_teks'])){
			throw new Exception('Indikator program tidak boleh kosong!');
		}

		if(empty($data['satuan'])){
			throw new Exception('Satuan indikator program tidak boleh kosong!');
		}

		if(empty($data['target_1'])){
			throw new Exception('Target Indikator program tahun ke-1 tidak boleh kosong!');
		}

		if(empty($data['pagu_1'])){
			throw new Exception('Pagu indikator program tahun ke-1 tidak boleh kosong!');
		}

		if(empty($data['target_2'])){
			throw new Exception('Target Indikator program tahun ke-2 tidak boleh kosong!');
		}

		if(empty($data['pagu_2'])){
			throw new Exception('Pagu indikator program tahun ke-2 tidak boleh kosong!');
		}

		if(empty($data['target_3'])){
			throw new Exception('Target Indikator program tahun ke-3 tidak boleh kosong!');
		}

		if(empty($data['pagu_3'])){
			throw new Exception('Pagu indikator program tahun ke-3 tidak boleh kosong!');
		}

		if(empty($data['target_4'])){
			throw new Exception('Target Indikator program tahun ke-4 tidak boleh kosong!');
		}

		if(empty($data['pagu_4'])){
			throw new Exception('Pagu indikator program tahun ke-4 tidak boleh kosong!');
		}

		if(empty($data['target_5'])){
			throw new Exception('Target Indikator program tahun ke-5 tidak boleh kosong!');
		}

		if(empty($data['pagu_5'])){
			throw new Exception('Pagu indikator program tahun ke-5 tidak boleh kosong!');
		}

		if(empty($data['target_awal'])){
			throw new Exception('Target awal Indikator program tidak boleh kosong!');
		}

		if(empty($data['target_akhir'])){
			throw new Exception('Target akhir Indikator program tidak boleh kosong!');
		}

		if(empty($data['id_unit'])){
			throw new Exception('Unit kerja wajib dipilih!');
		}
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

	function _number_format($number = 0, $mata_uang = ''){
		if(!is_numeric($number)){
			$number = 0;
		}
		$uang = number_format($number, 0, ",", ".");
		if(!empty($mata_uang)){
			$uang = $mata_uang.' '.$uang;
		}
		return $uang;
	}
}
