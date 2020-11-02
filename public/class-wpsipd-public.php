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
class Wpsipd_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpsipd-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpsipd-public.js', array( 'jquery' ), $this->version, false );

	}

	public function singkron_ssh($value='') {
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export SSH!'
		);
		if(!empty($_POST)){
			if(!empty($_POST['api_key']) && $_POST['api_key'] == APIKEY){
				if(!empty($_POST['ssh'])){
					$ssh = $_POST['ssh'];
					foreach ($ssh as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_standar_harga from data_ssh where id_standar_harga=".$v['id_standar_harga']);
						$kelompok = explode(' ', $v['nama_kel_standar_harga']);
						$opsi = array(
						    'id_standar_harga' => $v['id_standar_harga'],
						    'kode_standar_harga' => $v['kode_standar_harga'],
						    'nama_standar_harga' => $v['nama_standar_harga'],
						    'satuan' => $v['satuan'],
						    'spek' => $v['spek'],
						    'is_deleted' => $v['is_deleted'],
						    'is_locked' => $v['is_locked'],
						    'kelompok' => $v['kelompok'],
						    'harga' => $v['harga'],
						    'kode_kel_standar_harga' => $v['kode_kel_standar_harga'],
						    'nama_kel_standar_harga' => $kelompok[1],
						    'update_at'	=> date('Y-m-d H:i:s')
						);
						if(!empty($cek)){
							$wpdb->update('data_ssh', $opsi, array(
								'id_standar_harga' => $v['id_standar_harga']
							));
						}else{
							$wpdb->insert('data_ssh', $opsi);
						}

						foreach ($v['kd_belanja'] as $key => $value) {
							$cek = $wpdb->get_var("SELECT id_standar_harga from data_ssh_rek_belanja where id_akun=".$value['id_akun'].' and id_standar_harga='.$v['id_standar_harga']);
							$opsi = array(
								"id_akun"	=> $value['id_akun'],
								"kode_akun" => $value['kode_akun'],
								"nama_akun"	=> $value['nama_akun'],
								"id_standar_harga"	=> $v['id_standar_harga']
							);
							if(!empty($cek)){
								$wpdb->update('data_ssh_rek_belanja', $opsi, array(
									'id_standar_harga' => $v['id_standar_harga'],
									'id_akun' => $value['id_akun']
								));
							}else{
								$wpdb->insert('data_ssh_rek_belanja', $opsi);
							}
						}
					}
					// print_r($ssh); die();
				}else{
					$ret['status'] = 'error';
					$ret['message'] = 'Format SSH Salah!';
				}
			}else{
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}
		die(json_encode($ret));
	}

	public function datassh($value=''){
		global $wpdb;
		$data_ssh = $wpdb->get_results("SELECT * from data_ssh", ARRAY_A);
		$tbody = '';
		foreach ($data_ssh as $k => $v) {
			if($k >= 10){ continue; }
			$data_rek = $wpdb->get_results("SELECT * from data_ssh_rek_belanja where id_standar_harga=".$v['id_standar_harga'], ARRAY_A);
			$rek = array();
			foreach ($data_rek as $key => $value) {
				$rek[] = $value['nama_akun'];
			}
			$kelompok = "";
			if($v['kelompok'] == 1){
				$kelompok = 'SSH';
			}
			$tbody .= '
				<tr>
					<td class="text-center">'.($k+1).'</td>
					<td class="text-center">ID: '.$v['id_standar_harga'].'<br>Update pada: '.$v['update_at'].'</td>
					<td>'.$v['kode_standar_harga'].'</td>
					<td>'.$v['nama_standar_harga'].'</td>
					<td class="text-center">'.$v['satuan'].'</td>
					<td>'.$v['spek'].'</td>
					<td class="text-center">'.$v['is_deleted'].'</td>
					<td class="text-center">'.$v['is_locked'].'</td>
					<td class="text-center">'.$kelompok.'</td>
					<td class="text-right">Rp '.number_format($v['harga'],2,",",".").'</td>
					<td>'.implode('<br>', $rek).'</td>
				</tr>
			';
		}
		if(empty($tbody)){
			$tbody = '<tr><td colspan="10" class="text-center">Data Kosong!</td></tr>';
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
					<tr class="text-center">
						<th>No</th>
						<th>ID Standar Harga</th>
						<th>Kode</th>
						<th>Nama</th>
						<th>Satuan</th>
						<th>Spek</th>
						<th>Deleted</th>
						<th>Locked</th>
						<th>Kelompok</th>
						<th>Harga</th>
						<th>Rekening Belanja</th>
					</tr>
				</thead>
				<tbody>'.$tbody.'</tbody>
			</table>
		';
		echo $table;
	}

}
