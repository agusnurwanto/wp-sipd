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
class Wpsipd_Public
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
	}

	public function singkron_ssh($value = '')
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export SSH!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				if (!empty($_POST['ssh'])) {
					$ssh = $_POST['ssh'];
					foreach ($ssh as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_standar_harga from data_ssh where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_standar_harga=" . $v['id_standar_harga']);
						$kelompok = explode(' ', $v['nama_kel_standar_harga']);
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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

	public function singkron_asmas()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export data ASMAS!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				if (!empty($_POST['data'])) {
					$data = $_POST['data'];
					$cek = $wpdb->get_var("SELECT kepala_daerah from data_pengaturan_sipd where tahun_anggaran=".$_POST['tahun_anggaran']." AND kepala_daerah='".$data['kepala_daerah']."'");
					$opsi = array(
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
					carbon_set_theme_option( 'crb_daerah', $data['daerah'] );
					carbon_set_theme_option( 'crb_kepala_daerah', $data['kepala_daerah'] );
					carbon_set_theme_option( 'crb_wakil_daerah', $data['wakil_kepala_daerah'] );
					if (!empty($cek)) {
						$wpdb->update('data_pengaturan_sipd', $opsi, array(
							'kepala_daerah' => $v['kepala_daerah'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));
					} else {
						$wpdb->insert('data_pengaturan_sipd', $opsi);
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Data Dewan Salah!';
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				if (!empty($_POST['data'])) {
					$data = $_POST['data'];
					foreach ($data as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_pendapatan from data_pendapatan where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_pendapatan=" . $v['id_pendapatan'] ." AND id_skpd=".$_POST['id_skpd']);
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
						// if($v['id_pendapatan'] == '14384'){
						// 	print_r($opsi); die();
						// }
					}

					if(
						carbon_get_theme_option('crb_singkron_simda') == 1
						&& carbon_get_theme_option('crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
					){
						$debug = false;
						if(carbon_get_theme_option('crb_singkron_simda_debug') == 1){
							$debug = true;
						}
						$this->simda->singkronSimdaPendapatan(array('return' => $debug));
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Pendapatan Salah!';
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

	public function singkron_pembiayaan()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Pembiayaan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				if (!empty($_POST['data'])) {
					$data = $_POST['data'];
					foreach ($data as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_pembiayaan from data_pembiayaan where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_pembiayaan=" . $v['id_pembiayaan']);
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
								'tahun_anggaran' => $_POST['tahun_anggaran']
							));
						} else {
							$wpdb->insert('data_pembiayaan', $opsi);
						}
					}

					if(
						carbon_get_theme_option('crb_singkron_simda') == 1
						&& carbon_get_theme_option('crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
					){
						$debug = false;
						if(carbon_get_theme_option('crb_singkron_simda_debug') == 1){
							$debug = true;
						}
						$this->simda->singkronSimdaPembiayaan(array('return' => $debug));
					}
					// print_r($ssh); die();
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format Pembiayaan Salah!';
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
						$ret['renja_link'][$v['id_skpd']] = esc_url( get_permalink($custom_post));
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
						carbon_get_theme_option('crb_singkron_simda') == 1
						&& carbon_get_theme_option('crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
					){
						$debug = false;
						if(carbon_get_theme_option('crb_singkron_simda_debug') == 1){
							$debug = true;
						}
						$this->simda->singkronSimdaUnit(array('return' => $debug));
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {

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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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

	public function singkron_sumber_dana()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron sumber dana!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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

	public function singkron_alamat()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil singkron alamat!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			array('"', "'",'\\'), 
			array('petik_dua', 'petik_satu', ''), 
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
	                $rincian['lokus_akun_teks'] = str_replace('petik_satu', 'petik_satupetik_satu', $lokus_akun_teks);
	                return $this->get_alamat($input, $rincian, $no++);
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				$wpdb->update('data_sub_keg_bl', array( 'active' => 0 ), array(
					'tahun_anggaran' => $_POST['tahun_anggaran'],
					'id_sub_skpd' => $_POST['id_unit']
				));
				$sub_bl = $wpdb->get_results("SELECT kode_sbl from data_sub_keg_bl where tahun_anggaran=".$_POST['tahun_anggaran']." AND id_sub_skpd='" . $_POST['id_unit'] . "'", ARRAY_A);
				foreach ($sub_bl as $k => $sub) {
					$wpdb->update('data_sub_keg_indikator', array( 'active' => 0 ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_keg_indikator_hasil', array( 'active' => 0 ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_tag_sub_keg', array( 'active' => 0 ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_capaian_prog_sub_keg', array( 'active' => 0 ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_output_giat_sub_keg', array( 'active' => 0 ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_dana_sub_keg', array( 'active' => 0 ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'kode_sbl' => $sub['kode_sbl']
					));
					$wpdb->update('data_lokasi_sub_keg', array( 'active' => 0 ), array(
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			'status'	=> 'success',
			'message'	=> 'Berhasil export RKA!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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

				if (!empty($_POST['rka']) && $ret['status'] != 'error') {
					$rka = $_POST['rka'];
					if(!empty($_POST['no_page']) && $_POST['no_page']==1){
						$wpdb->delete('data_rka', array(
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'kode_sbl' => $_POST['kode_sbl']
						), array('%d', '%s'));
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
							'id_standar_harga' => $v['id_standar_harga'],
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
							'subs_bl_teks' => $v['subs_bl_teks'],
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
							'idsubtitle' => $v['idsubtitle'],
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
					}
					// print_r($ssh); die();
				} else if ($ret['status'] != 'error') {
					$ret['status'] = 'error';
					$ret['message'] = 'Format RKA Salah!';
				}

				if(
					carbon_get_theme_option('crb_singkron_simda') == 1
					&& carbon_get_theme_option('crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
				){
					$debug = false;
					if(carbon_get_theme_option('crb_singkron_simda_debug') == 1){
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

	public function monitor_rfk($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-rfk.php';
	}

	public function monitor_sipd($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-update.php';
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				if(
					!empty($_POST['data']) 
					&& !empty($_POST['type']) 
					|| (
						!empty($_POST['kode_sbl']) && $_POST['type']=='belanja'
					)
				){
					$data = $_POST['data'];
					$wpdb->update('data_anggaran_kas', array( 'active' => 0 ), array(
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'type' => $_POST['type'],
						'kode_sbl' => $_POST['kode_sbl']
					));
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
							'id_unit' => $v['id_unit'],
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
								'id_akun' => $v['id_akun']
							));
						} else {
							$wpdb->insert('data_anggaran_kas', $opsi);
						}
					}
					if(
						carbon_get_theme_option('crb_singkron_simda') == 1
						&& carbon_get_theme_option('crb_tahun_anggaran_sipd') == $_POST['tahun_anggaran']
					){
						$debug = false;
						if(carbon_get_theme_option('crb_singkron_simda_debug') == 1){
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				if(!empty($_POST['kode_bl'])){
					$kode_bl = explode('.', $_POST['kode_bl']);
					unset($kode_bl[2]);
					$bl = $wpdb->get_results($wpdb->prepare("
						select 
							* 
						from data_sub_keg_bl 
						where kode_bl=%s 
							and active=1 
							and tahun_anggaran=%d", implode('.', $kode_bl), $_POST['tahun_anggaran']
					), ARRAY_A);
					$ret['query'] = $wpdb->last_query;
					$kodeunit = $bl[0]['kode_skpd'];
					$kode_giat = $bl[0]['kode_bidang_urusan'].substr($bl[0]['kode_giat'], 4, strlen($bl[0]['kode_giat']));
					$nama_page = $_POST['tahun_anggaran'] . ' | ' . $kodeunit . ' | ' . $kode_giat . ' | ' . $bl[0]['nama_giat'];
					$custom_post = get_page_by_title($nama_page, OBJECT, 'post');
					$ret['link'] = esc_url( get_permalink($custom_post));
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
						$ret['link'] = esc_url( get_permalink($custom_post) );
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
								'link' => esc_url( get_permalink($custom_post) )
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

	function gen_key($key_db = false){
		$now = time()*1000;
		if(empty($key_db)){
			$key_db = carbon_get_theme_option( 'crb_api_key_extension' );
		}
		$key = base64_encode($now.$key_db.$now);
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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

	public function menu_monev(){
		global $wpdb;
		$user_id = um_user( 'ID' );
		$skpd = get_user_meta($user_id, '_crb_nama_skpd');
		$id_skpd = get_user_meta($user_id, '_id_sub_skpd');
		ob_start();
		echo '<div>';
		if(!empty($id_skpd)){ 
			echo "<h5>SKPD: $skpd[0]</h5>";
			$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran', ARRAY_A);
			foreach ($tahun as $k => $v) {
				$unit = $wpdb->get_results("SELECT nama_skpd, id_skpd, kode_skpd from data_unit where active=1 and tahun_anggaran=".$v['tahun_anggaran'].' and id_skpd='.$id_skpd[0], ARRAY_A);
				echo '<ul>';
	            foreach ($unit as $kk => $vv) {
					$nama_page = 'RFK '.$vv['nama_skpd'].' '.$vv['kode_skpd'].' | '.$v['tahun_anggaran'];
					$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
					$url_rfk = esc_url( get_permalink($custom_post));
					echo '<li>MONEV RFK: <a href="'.$url_rfk.'&key='.$this->gen_key().'" target="_blank">'.$nama_page.'</a></li>';
				}
				echo '</ul>';
			}
		}else{
			echo 'SKPD tidak ditemukan!';
		}
		echo '</div>';
		return ob_get_clean();
	}

	public function simpan_rfk(){
		global $wpdb;
		$ret = array();
		$ret['status'] = 'success';
		$ret['message'] = 'Berhasil simpan realisasi fisik dan keuangan!';
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
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
		$sql = $wpdb->prepare("
			SELECT  sum(a.debet) as total
			FROM ta_jurnalsemua_rinc a
				left join ta_jurnalsemua p ON a.no_bukti=p.no_bukti
					AND a.tahun = p.tahun
					AND a.kd_source = p.kd_source
			WHERE a.tahun = %d 
				AND a.kd_jurnal <> 8
				AND a.kd_rek_1 = 5
				AND p.tgl_bukti BETWEEN %s AND %s
				AND p.Kd_Urusan = %d
				AND p.Kd_Bidang = %d
				AND p.Kd_Unit = %d
				AND p.Kd_Sub = %d
				AND a.kd_prog = %d
				AND a.id_prog = %d
				AND a.kd_keg = %d
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
		$pagu = $this->simda->CurlSimda(array('query' => $sql), false);
		if(!empty($pagu[0])){
			return $pagu[0]->total;
		}else{
			return 0;
		}
	}

	function pembulatan($angka){
		$angka = $angka*100;
		return round($angka)/100;
	}
}
