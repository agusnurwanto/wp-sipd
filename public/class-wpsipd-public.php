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
		wp_enqueue_style( $this->plugin_name.'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );

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
		wp_enqueue_script( $this->plugin_name.'bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );

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
						    'update_at'	=> current_time('mysql'),
						    'tahun_anggaran'	=> $_POST['tahun_anggaran']
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

	public function datassh($atts){
		$a = shortcode_atts( array(
			'filter' => '',
		), $atts );
		global $wpdb;

		$where = '';
		if(!empty($a['filter'])){
			if($a['filter'] == 'is_deleted'){
				$where = 'where is_deleted=1';
			}
		}
		$data_ssh = $wpdb->get_results("SELECT * from data_ssh ".$where, ARRAY_A);
		$tbody = '';
		$no = 1;
		foreach ($data_ssh as $k => $v) {
			// if($k >= 10){ continue; }
			$data_rek = $wpdb->get_results("SELECT * from data_ssh_rek_belanja where id_standar_harga=".$v['id_standar_harga'], ARRAY_A);
			$rek = array();
			foreach ($data_rek as $key => $value) {
				$rek[] = $value['nama_akun'];
			}
			if(!empty($a['filter'])){
				if($a['filter'] == 'rek_kosong' && !empty($rek)){
					continue;
				}
			}

			$kelompok = "";
			if($v['kelompok'] == 1){
				$kelompok = 'SSH';
			}
			$tbody .= '
				<tr>
					<td class="text-center">'.number_format($no,0,",",".").'</td>
					<td class="text-center">ID: '.$v['id_standar_harga'].'<br>Update pada:<br>'.$v['update_at'].'</td>
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
			$no++;
		}
		if(empty($tbody)){
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
				<tbody>'.$tbody.'</tbody>
			</table>
		';
		echo $table;
	}

	public function singkron_akun_belanja(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Akun Rekening Belanja!'
		);
		if(!empty($_POST)){
			if(!empty($_POST['api_key']) && $_POST['api_key'] == APIKEY){
				if(!empty($_POST['akun'])){
					$akun = $_POST['akun'];
					foreach ($akun as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_akun from data_akun where id_akun=".$v['id_akun']);
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
						if(!empty($cek)){
							$wpdb->update('data_akun', $opsi, array(
								'id_akun' => $v['id_akun']
							));
						}else{
							$wpdb->insert('data_akun', $opsi);
						}
					}
					// print_r($ssh); die();
				}else{
					$ret['status'] = 'error';
					$ret['message'] = 'Format Akun Belanja Salah!';
				}
			}else{
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}else{
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	public function singkron_rka(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export RKA!'
		);
		if(!empty($_POST)){
			if(!empty($_POST['api_key']) && $_POST['api_key'] == APIKEY){
				if(!empty($_POST['dataBl'])){
					$dataBl = $_POST['dataBl'];
					foreach ($dataBl as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_sub_bl from data_sub_keg_bl where id_sub_bl=".$v['id_sub_bl']);
						$opsi = array(
							'id_sub_skpd' => $v['id_sub_skpd'],
				            'id_lokasi' => $v['id_lokasi'],
				            'id_label_kokab' => $v['id_label_kokab'],
				            'nama_dana' => $v['nama_dana'],
				            'no_sub_giat' => $v['no_sub_giat'],
				            'kode_giat' => $v['kode_giat'],
				            'id_program' => $v['id_program'],
				            'nama_lokasi' => $v['nama_lokasi'],
				            'waktu_akhir' => $v['waktu_akhir'],
				            'pagu_n_lalu' => $v['pagu_n_lalu'],
				            'id_urusan' => $v['id_urusan'],
				            'id_unik_sub_bl' => $v['id_unik_sub_bl'],
				            'id_sub_giat' => $v['id_sub_giat'],
				            'label_prov' => $v['label_prov'],
				            'kode_program' => $v['kode_program'],
				            'kode_sub_giat' => $v['kode_sub_giat'],
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
				            'id_skpd' => $v['id_skpd'],
				            'id_sub_bl' => $v['id_sub_bl'],
				            'nama_sub_skpd' => $v['nama_sub_skpd'],
				            'target_1' => $v['target_1'],
				            'nama_urusan' => $v['nama_urusan'],
				            'target_2' => $v['target_2'],
				            'label_kokab' => $v['label_kokab'],
				            'label_pusat' => $v['label_pusat'],
				            'id_bl' => $v['id_bl'],
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if(!empty($cek)){
							$wpdb->update('data_sub_keg_bl', $opsi, array(
								'id_sub_bl' => $v['id_sub_bl']
							));
						}else{
							$wpdb->insert('data_sub_keg_bl', $opsi);
						}

						$nama_page = $_POST['tahun_anggaran'].' '.$v['kode_giat'].' '.$v['nama_giat'];
						$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
						// print_r($custom_post); die();
						if(empty($custom_post) || empty($custom_post->ID)){
							$id = wp_insert_post( array(
								'post_title'	=> $nama_page, 
								'post_content'	=> '[tampilrka idbl='.$v['id_bl'].']', 
								'post_type'		=> 'page',
								'post_status'	=> 'publish'
							));
							$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
						}else{
							wp_update_post(array(
							    'ID'    =>  $custom_post->ID,
							    'post_status'   =>  'publish'
						    ));
						}
						update_post_meta($custom_post->ID,'ast-breadcrumbs-content', 'disabled');
						update_post_meta($custom_post->ID,'ast-featured-img', 'disabled');
						update_post_meta($custom_post->ID,'ast-main-header-display', 'disabled');
						update_post_meta($custom_post->ID,'footer-sml-layout', 'disabled');
						update_post_meta($custom_post->ID,'site-content-layout', 'page-builder');
						update_post_meta($custom_post->ID,'site-post-title', 'disabled');
						update_post_meta($custom_post->ID,'site-sidebar-layout', 'no-sidebar');
						update_post_meta($custom_post->ID,'theme-transparent-header-meta', 'disabled');
						$ret['message'] .= ' URL '.$custom_post->guid;
					}
				}else{
					$ret['status'] = 'error';
					$ret['message'] = 'Format data BL Salah!';
				}

				if(!empty($_POST['dataOutput']) && $ret['status'] != 'error'){
					$dataOutput = $_POST['dataOutput'];
					foreach ($dataOutput as $k => $v) {
						$cek = $wpdb->get_var("SELECT idsubbl from data_sub_keg_indikator where idsubbl=".$_POST['idsubbl']);
						$opsi = array(
							'outputteks' => $v['outputteks'],
				            'targetoutput' => $v['targetoutput'],
				            'satuanoutput' => $v['satuanoutput'],
				            'idoutputbl' => $v['idoutputbl'],
				            'targetoutputteks' => $v['targetoutputteks'],
							'idsubbl' => $_POST['idsubbl'],
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if(!empty($cek)){
							$wpdb->update('data_sub_keg_indikator', $opsi, array(
								'idsubbl' => $_POST['idsubbl']
							));
						}else{
							$wpdb->insert('data_sub_keg_indikator', $opsi);
						}

					}
				}else if($ret['status'] != 'error'){
					$ret['status'] = 'error';
					$ret['message'] = 'Format data dataOutput Salah!';
				}
				
				if(!empty($_POST['rka']) && $ret['status'] != 'error'){
					$rka = $_POST['rka'];
					foreach ($rka as $k => $v) {
						$cek = $wpdb->get_var("SELECT id_rinci_sub_bl from data_rka where id_rinci_sub_bl=".$v['id_rinci_sub_bl']);
						$opsi = array(
							'created_user' => $v['created_user'],
							'createddate' => $v['createddate'],
							'createdtime' => $v['createdtime'],
							'harga_satuan' => $v['harga_satuan'],
							'id_daerah' => $v['id_daerah'],
							'id_rinci_sub_bl' => $v['id_rinci_sub_bl'],
							'id_standar_nfs' => $v['id_standar_nfs'],
							'is_locked' => $v['is_locked'],
							'jenis_bl' => $v['jenis_bl'],
							'ket_bl_teks' => $v['ket_bl_teks'],
							'kode_akun' => $v['kode_akun'],
							'koefisien' => $v['koefisien'],
							'lokus_akun_teks' => $v['lokus_akun_teks'],
							'nama_akun' => $v['nama_akun'],
							'nama_komponen' => $v['nama_komponen'],
							'spek_komponen' => $v['spek_komponen'],
							'satuan' => $v['satuan'],
							'spek' => $v['spek'],
							'subs_bl_teks' => $v['subs_bl_teks'],
							'total_harga' => $v['total_harga'],
							'totalpajak' => $v['totalpajak'],
							'updated_user' => $v['updated_user'],
							'updateddate' => $v['updateddate'],
							'updatedtime' => $v['updatedtime'],
							'user1' => $v['user1'],
							'user2' => $v['user2'],
							'idbl' => $_POST['idbl'],
							'idsubbl' => $_POST['idsubbl'],
							'update_at' => current_time('mysql'),
							'tahun_anggaran' => $_POST['tahun_anggaran']
						);
						if(!empty($cek)){
							$wpdb->update('data_rka', $opsi, array(
								'id_rinci_sub_bl' => $v['id_rinci_sub_bl']
							));
						}else{
							$wpdb->insert('data_rka', $opsi);
						}
					}
					// print_r($ssh); die();
				}else if($ret['status'] != 'error'){
					$ret['status'] = 'error';
					$ret['message'] = 'Format RKA Salah!';
				}
			}else{
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}else{
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	public function getSSH(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export Akun Rekening Belanja!'
		);
		if(!empty($_POST)){
			if(!empty($_POST['id_akun'])){
				$data_ssh = $wpdb->get_results($wpdb->prepare("
					SELECT 
						s.id_standar_harga, 
						s.nama_standar_harga, 
						s.kode_standar_harga 
					from data_ssh_rek_belanja r
						join data_ssh s ON r.id_standar_harga=s.id_standar_harga
					where r.id_akun=%d", $_POST['id_akun'])
				, ARRAY_A);
			}else{
				$ret['status'] = 'error';
				$ret['message'] = 'Format ID Salah!';
			}
		}else{
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	public function rekbelanja($atts){
		$a = shortcode_atts( array(
			'filter' => '',
		), $atts );
		global $wpdb;

		$data_akun = $wpdb->get_results("
			SELECT 
				* 
			from data_akun 
			where belanja='Ya'
				AND is_barjas=1
				AND set_input=1"
		, ARRAY_A);
		$tbody = '';
		$no = 1;
		foreach ($data_akun as $k => $v) {
			// if($k >= 100){ continue; }
			$data_ssh = $wpdb->get_results("
				SELECT 
					s.id_standar_harga, 
					s.nama_standar_harga, 
					s.kode_standar_harga 
				from data_ssh_rek_belanja r
					join data_ssh s ON r.id_standar_harga=s.id_standar_harga
				where r.id_akun=".$v['id_akun']
			, ARRAY_A);

			$ssh = array();
			foreach ($data_ssh as $key => $value) {
				$ssh[] = '('.$value['id_standar_harga'].') '.$value['kode_standar_harga'].' '.$value['nama_standar_harga'];
			}
			if(!empty($a['filter'])){
				if($a['filter'] == 'ssh_kosong' && !empty($ssh)){
					continue;
				}
			}

			$html_ssh = '';
			if(!empty($ssh)){
				$html_ssh = '
					<a class="btn btn-primary" data-toggle="collapse" href="#collapseSSH'.$k.'" role="button" aria-expanded="false" aria-controls="collapseSSH'.$k.'">
				    	Lihat Item SSH Total ('.count($ssh).')
				  	</a>
				  	<div class="collapse" id="collapseSSH'.$k.'">
					  	<div class="card card-body">
				  			'.implode('<br>', $ssh).'
					  	</div>
					</div>
				';
			}
			$tbody .= '
				<tr>
					<td class="text-center">'.number_format($no,0,",",".").'</td>
					<td class="text-center">ID: '.$v['id_akun'].'<br>Update pada:<br>'.$v['update_at'].'</td>
					<td>'.$v['kode_akun'].'</td>
					<td>'.$v['nama_akun'].'</td>
					<td>'.$html_ssh.'</td>
				</tr>
			';
			$no++;
		}
		if(empty($tbody)){
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
				<tbody>'.$tbody.'</tbody>
			</table>
		';
		echo $table;
	}

	public function tampilrka($atts){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/wpsipd-public-rka.php';
	}

}
