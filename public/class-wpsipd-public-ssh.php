<?php
require_once WPSIPD_PLUGIN_PATH."/public/class-wpsipd-public-fmis.php";

class Wpsipd_Public_Ssh extends Wpsipd_Public_FMIS
{
	public function data_ssh_sipd($atts){
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/ssh/wpsipd-public-data-ssh.php';
	}
	
	public function monitor_satuan_harga($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		if(!empty($atts['id_skpd'])){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/ssh/wpsipd-public-monitor-satuan-harga.php';
		}
	}

	public function laporan_per_item_ssh($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/ssh/wpsipd-public-laporan-per-item-ssh.php';
	}

	function ssh_tidak_terpakai($atts){

		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/ssh/wpsipd-public-ssh-tidak-terpakai.php';
	}

	function surat_usulan_ssh($atts){

		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/ssh/wpsipd-public-surat-usulan-ssh.php';
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
				$tidak_terpakai = '';
				$tidak_terpakai_where = '';
				if(
					!empty($_POST['tidak_terpakai']) 
					&& $_POST['tidak_terpakai']==1
				){
					$id_ssh_all = $wpdb->get_results($wpdb->prepare("
						SELECT 
							r.id_standar_harga
						FROM data_ssh r
						INNER JOIN (
							SELECT DISTINCT
								r.tahun_anggaran,
								r.nama_komponen,
								r.harga_satuan,
								r.satuan
							FROM data_rka r
							WHERE r.tahun_anggaran=%d
								AND r.active=1
								AND r.jenis_bl NOT IN (
									'btl-gaji', 
									'blud', 
									'hibah', 
									'hibah-brg', 
									'bansos', 
									'bansos-brg', 
									'bankeu', 
									'bos',
									'bos-pusat',
									'hutang',
									'sewa-tanah',
									'subsidi',
									'tanah'
								)
						) AS s ON s.nama_komponen=r.nama_standar_harga
							AND s.harga_satuan=r.harga
							AND s.satuan=r.satuan
							AND r.tahun_anggaran=s.tahun_anggaran
					", $params['tahun_anggaran']), ARRAY_A);
					if(!empty($id_ssh_all)){
						$new_id_ssh_all = array();
						foreach($id_ssh_all as $id){
							$new_id_ssh_all[] = $id['id_standar_harga'];
						}
						$id_ssh_all = implode(',', $new_id_ssh_all);
						$tidak_terpakai_where = " AND s.id_standar_harga NOT IN ($id_ssh_all)";
					}
				}
				$columns = array( 
					0 => 's.id_standar_harga',
					1 => 's.kode_standar_harga', 
					2 => 's.nama_standar_harga',
					3 => 's.spek',
					4 => 's.satuan',
					5 => 's.harga',
					6 => 's.kelompok'
				);
				$where = $sqlTot = $sqlRec = "";

				// check search value exist
				if( !empty($params['search']['value']) ) {
					$where .=" AND ( s.kode_standar_harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");    
					$where .=" OR s.nama_standar_harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR s.spek LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR s.id_standar_harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%");
					$where .=" OR s.harga LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%")." )";
				}

				// getting total number records without any search
				$sql_tot = "SELECT count(s.id) as jml FROM `data_ssh` s".$tidak_terpakai;
				$sql = "SELECT ".implode(', ', $columns)." FROM `data_ssh` s".$tidak_terpakai;
				$where_first = " WHERE s.id_standar_harga IS NOT NULL AND s.tahun_anggaran=".$wpdb->prepare('%d', $params['tahun_anggaran']).$tidak_terpakai_where;
				$sqlTot .= $sql_tot.$where_first;
				$sqlRec .= $sql.$where_first;
				if(isset($where) && $where != '') {
					$sqlTot .= $where;
					$sqlRec .= $where;
				}

				$limit = '';
				if($params['length'] != -1){
					$limit = "  LIMIT ".$wpdb->prepare('%d', $params['start'])." ,".$wpdb->prepare('%d', $params['length']);
				}
			 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir'].$limit;

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
				if(
					!empty($_POST['id_standar_harga']) 
					&& !empty($_POST['harga_satuan']) 
					&& !empty($_POST['keterangan_lampiran'])
					&& !empty($_FILES['lapiran_usulan_ssh_1'])
					&& !empty($_FILES['lapiran_usulan_ssh_2'])
				){
					$id_standar_harga = trim(htmlspecialchars($_POST['id_standar_harga']));
					$tahun_anggaran = trim(htmlspecialchars($_POST['tahun_anggaran']));
					$harga_satuan = trim(htmlspecialchars($_POST['harga_satuan']));
					$keterangan_lampiran = trim(htmlspecialchars($_POST['keterangan_lampiran']));
					
					// get data dari tabel data_ssh
					$data_old_ssh = $wpdb->get_results($wpdb->prepare("
						SELECT 
							* 
						FROM data_ssh 
						WHERE id_standar_harga = %d
					",$id_standar_harga), ARRAY_A);
					$data_old_ssh_akun = $wpdb->get_results($wpdb->prepare("
						SELECT 
							* 
						FROM data_ssh_rek_belanja 
						WHERE id_standar_harga = %d
					",$id_standar_harga), ARRAY_A);
					$kode_standar_harga_sipd = $data_old_ssh[0]['kode_standar_harga'];

					// get data dari tabel data_ssh_usulan
					if(empty($data_old_ssh)){
						$data_old_ssh = $wpdb->get_results($wpdb->prepare("
							SELECT 
								* 
							FROM data_ssh_usulan 
							WHERE id_standar_harga = %d
						",$id_standar_harga), ARRAY_A);
						$data_old_ssh_akun = $wpdb->get_results($wpdb->prepare("
							SELECT 
								* 
							FROM data_ssh_rek_belanja_usulan 
							WHERE id_standar_harga = %d
						",$id_standar_harga), ARRAY_A);
						$kode_standar_harga_sipd = NULL;
					}

					if(!empty($data_old_ssh_akun)){
						foreach($data_old_ssh_akun as $v_a_akun){
							$data_akun[$v_a_akun['id_akun']] = $wpdb->get_results($wpdb->prepare("
								SELECT 
									id_akun,
									kode_akun,
									nama_akun 
								FROM data_akun 
								WHERE id_akun = %d
							",$v_a_akun['id_akun']), ARRAY_A);
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
	
					$last_kode_standar_harga = $wpdb->get_results($wpdb->prepare("
						SELECT 
							id_standar_harga,
							kode_standar_harga 
						FROM `data_ssh_usulan` 
						WHERE kode_standar_harga=(
								SELECT 
									MAX(kode_standar_harga) 
								FROM `data_ssh_usulan` 
								WHERE kode_kel_standar_harga = %s
							)
					",$data_old_ssh[0]['kode_kel_standar_harga']), ARRAY_A);
					$last_kode_standar_harga = (empty($last_kode_standar_harga[0]['kode_standar_harga'])) ? "0" : explode(".",$last_kode_standar_harga[0]['kode_standar_harga']);
					$last_kode_standar_harga = (int) end($last_kode_standar_harga);
					$last_kode_standar_harga = $last_kode_standar_harga+1;
					$last_kode_standar_harga = sprintf("%05d",$last_kode_standar_harga);
					$last_kode_standar_harga = $data_old_ssh[0]['kode_kel_standar_harga'].'.'.$last_kode_standar_harga;
	
					$id_standar_harga = $wpdb->get_results($wpdb->prepare("
						SELECT 
							id_standar_harga 
						FROM `data_ssh_usulan` 
						WHERE id_standar_harga=(
								SELECT 
									MAX(id_standar_harga) 
								FROM `data_ssh_usulan`
							) 
							AND tahun_anggaran=%d
					",$tahun_anggaran), ARRAY_A);
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
					$upload_1 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_1'], ['jpg', 'jpeg', 'png', 'pdf']);

					if($upload_1['status']){
						$opsi_ssh['lampiran_1'] = $upload_1['filename'];
					}

					$upload_2 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_2'], ['jpg', 'jpeg', 'png', 'pdf']);
					if($upload_2['status']){
						$opsi_ssh['lampiran_2'] = $upload_2['filename'];
					}

					if(!empty($_FILES['lapiran_usulan_ssh_3'])){
						$upload_3 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_3'], ['jpg', 'jpeg', 'png', 'pdf']);
						if($upload_3['status']){
							$opsi_ssh['lampiran_3'] = $upload_3['filename'];
						}
					}
	
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
					if(
						in_array("administrator", $user_meta->roles) ||
						in_array("tapd_keu", $user_meta->roles)
					){
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

						$data_ssh = $wpdb->get_results($wpdb->prepare("
							SELECT 
								status_upload_sipd, 
								status, 
								status_by_admin, 
								status_by_tapdkeu, 
								keterangan_status_admin, 
								keterangan_status_tapdkeu 
							FROM data_ssh_usulan 
							WHERE id_standar_harga = %d
						",$id_ssh_verify_ssh), ARRAY_A);
		
						if($data_ssh[0]['status_upload_sipd'] != 1){
							
							// $date_now = date("Y-m-d H:i:s");
							// $status_usulan_ssh = ($verify_ssh) ? 'approved' : 'rejected';
							// $keterangan_status = (!empty($reason_verify_ssh)) ? $reason_verify_ssh : NULL;
			
							//// update status data usulan ssh
							// $opsi_ssh = array(
								// 'status' => $status_usulan_ssh,
								// 'keterangan_status' => $keterangan_status,
								// 'update_at' => $date_now,
								// 'verified_by' => um_user( 'ID' ),
							// );

							$opsi_ssh = array();
							if(in_array("administrator", $user_meta->roles)){
								$opsi_ssh['update_at_admin']=date("Y-m-d H:i:s");
								$opsi_ssh['verified_by_admin']=um_user( 'ID' );
								if($verify_ssh){
									if(trim($data_ssh[0]['status_by_tapdkeu'])==='approved'){
										$opsi_ssh['status']='approved';
									}
									$opsi_ssh['status_by_admin']='approved';
								}else{
									$opsi_ssh['status']='rejected';
									$opsi_ssh['status_by_admin']='rejected';
									$opsi_ssh['keterangan_status_admin']=(!empty($reason_verify_ssh)) ? $reason_verify_ssh . " | " . $data_ssh[0]['keterangan_status_admin'] : $data_ssh[0]['keterangan_status_admin'];
								}
							}

							if(in_array("tapd_keu", $user_meta->roles)){
								$opsi_ssh['update_at_tapdkeu']=date("Y-m-d H:i:s");
								$opsi_ssh['verified_by_tapdkeu']=um_user( 'ID' );
								if($verify_ssh){
									if(trim($data_ssh[0]['status_by_admin'])==='approved'){
										$opsi_ssh['status']='approved';
									}
									$opsi_ssh['status_by_tapdkeu']='approved';
								}else{
									$opsi_ssh['status']='rejected';
									$opsi_ssh['status_by_tapdkeu']='rejected';
									$opsi_ssh['keterangan_status_tapdkeu']=(!empty($reason_verify_ssh)) ? $reason_verify_ssh . " | " . $data_ssh[0]['keterangan_status_tapdkeu'] : $data_ssh[0]['keterangan_status_tapdkeu'];
								}
							}
			
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
					$id = $_POST['id'];
					$tahun_anggaran = $_POST['tahun_anggaran'];
					
					$data_id_ssh_usulan = $wpdb->get_results($wpdb->prepare('
						SELECT 
							* 
						FROM data_ssh_usulan 
						WHERE id = %d
					',$id), ARRAY_A);

					$data_akun_ssh_usulan = $wpdb->get_results($wpdb->prepare('
						SELECT 
							id,
							id_akun,
							nama_akun 
						FROM data_ssh_rek_belanja_usulan 
						WHERE id_standar_harga = %d
					',$id_standar_harga), ARRAY_A);

					$data_kel_standar_harga_by_id = $wpdb->get_results($wpdb->prepare('
						SELECT 
							* 
						FROM data_kelompok_satuan_harga 
						WHERE kode_kategori 
							LIKE %s 
							AND tahun_anggaran = %s
					',$data_id_ssh_usulan[0]['kode_kel_standar_harga'].'%',$tahun_anggaran), ARRAY_A);
				    
					$table_content_akun = '';
					if(!empty($data_id_ssh_usulan[0]['kode_standar_harga_sipd'])){
						$data_id_ssh_existing = $wpdb->get_results($wpdb->prepare('
							SELECT 
								id_standar_harga 
							FROM data_ssh 
							WHERE kode_standar_harga = %s
						', $data_id_ssh_usulan[0]['kode_standar_harga_sipd']), ARRAY_A);
						$data_akun_ssh_existing_sipd = $wpdb->get_results($wpdb->prepare('
							SELECT 
								id,
								id_akun,
								nama_akun 
							FROM data_ssh_rek_belanja 
							WHERE id_standar_harga = %d
						',$data_id_ssh_existing[0]['id_standar_harga']), ARRAY_A);
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

	public function get_data_usulan_ssh_by_id_standar_harga(){
		global $wpdb;
		
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				$user_id = um_user( 'ID' );
				$user_meta = get_userdata($user_id);

				$data_ssh = $wpdb->get_row($wpdb->prepare("SELECT keterangan_status_admin, keterangan_status_tapdkeu FROM data_ssh_usulan WHERE id_standar_harga=%d", $_POST['id_standar_harga']));

				$return = array(
					'status' => 'success',
					'role' => $user_meta->roles[0],
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

	public function get_data_usulan_ssh_surat(){
		global $wpdb;
		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);

		$return = array(
			'status' => 'success',
			'message' => 'Berhasil get data surat usulan!',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$params = $columns = $totalRecords = array();
				$params = $_REQUEST;
				$columns = array( 
					0 => 'update_at',
					1 => 'idskpd', 
					2 => 'nomor_surat',
					3 => 'nama_file',
					4 => 'catatan',
					5 => 'catatan_verifikator',
					6 => 'active',
					7 => 'created_user',
					8 => 'tahun_anggaran',
					9 => 'id',
					10 => 'jenis_survey',
					11 => 'jenis_juknis',
				);
				$where = $sqlTot = $sqlRec = "";

				$is_admin = true;
				/** Jika admin tampilkan semua data */
				if(
					!in_array("administrator",$user_meta->roles) &&
					!in_array("tapd_keu", $user_meta->roles)
				){
					$is_admin = false;
					$this_user_meta = get_user_meta($user_id);
					/** cari data user berdasarkan nama skpd */
					if(
						$this_user_meta['_id_sub_skpd'][0] != ''
					){
						$where .=" AND idskpd = '".$this_user_meta['_id_sub_skpd'][0]."' ";
					}else{
						$where .=" AND idskpd = '-' ";
					}
				}

				$tahun_anggaran = $wpdb->prepare('%d', $params['tahun_anggaran']);
				// getting total number records without any search
				$sql_tot = "SELECT count(*) as jml FROM `data_surat_usulan_ssh`";
				$sql = "SELECT ".implode(', ', $columns)." FROM `data_surat_usulan_ssh`";
				$where_first = " WHERE active=1 AND tahun_anggaran=".$tahun_anggaran;
				$sqlTot .= $sql_tot.$where_first;
				$sqlRec .= $sql.$where_first;
				if(isset($where) && $where != '') {
					$sqlTot .= $where;
					$sqlRec .= $where;
				}

			 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$wpdb->prepare('%d', $params['start'])." ,".$wpdb->prepare('%d', $params['length'])." ";

				$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				$totalRecords = $queryTot[0]['jml'];
				$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);
				foreach($queryRecords as $k => $val){
					$nama_skpd = $wpdb->get_var($wpdb->prepare("
						SELECT
							nama_skpd
						FROM data_unit
						WHERE id_skpd=%d
					", $val['idskpd']));
					$queryRecords[$k]['nama_skpd'] = $nama_skpd;
					$title = 'Surat Usulan Standar Harga Nomor '.$val['nomor_surat'].' Tahun '.$tahun_anggaran;
					$url_surat = $this->generatePage($title, $tahun_anggaran, '[surat_usulan_ssh id_surat="'.$val['id'].'"]');
					$queryRecords[$k]['aksi'] = '
						<a class="btn btn-sm btn-warning" target="_blank" href="'.$url_surat."&idskpd=".$val['idskpd'].'" title="Cetak Surat Usulan"><i class="dashicons dashicons-printer"></i></a>
						<a class="btn btn-sm btn-primary" href="#" onclick="return simpan_surat_usulan(\''.$val['id'].'\');" title="Simpan Surat Usulan"><i class="dashicons dashicons-saved"></i></a>
						<a class="btn btn-sm btn-warning" href="#" onclick="return edit_surat_usulan(this);" title="Edit Surat Usulan" data-id="'.$val['id'].'" data-nomorsurat="'.$val['nomor_surat'].'" data-idskpd="'.$val['idskpd'].'"><i class="dashicons dashicons-edit"></i></a>';
					$queryRecords[$k]['jml_usulan'] = 0;
					$queryRecords[$k]['ids_usulan'] = array();
					if(!empty($val['nama_file'])){
						$queryRecords[$k]['nama_file'] = '<a href="'.WPSIPD_PLUGIN_URL.'/public/media/ssh/'.$val['nama_file'].'" target="_blank">'.$val['nama_file']."</a>";
					}else{
						$queryRecords[$k]['nama_file'] = '';
					}
					if(!$is_admin){
						$queryRecords[$k]['nama_file'] .= '<br><input type="file" id="surat_file">';
						$queryRecords[$k]['catatan'] = '<textarea id="catatan_surat_edit" class="form-control">'.$val['catatan'].'</textarea>';
					}else{
						$queryRecords[$k]['catatan_verifikator'] = '<textarea id="catatan_verifikator_surat_edit" class="form-control">'.$val['catatan_verifikator'].'</textarea>';
					}

					if(!empty($val['jenis_survey']) && $val['jenis_survey'] == 1){
						$queryRecords[$k]['acuan_ssh'] = '<span class="jenis_survey" data-id="'.$val['jenis_survey'].'">-Survey harga pasar</span>';
					}

					if(!empty($val['jenis_juknis']) && $val['jenis_juknis'] == 2){
						$queryRecords[$k]['acuan_ssh'] .= '</br><span class="jenis_juknis" data-id="'.$val['jenis_juknis'].'">-Petunjuk Teknis</span>';
					}
				}

				$json_data = array(
					"draw" => intval( $params['draw'] ),   
					"recordsTotal" => intval( $totalRecords ),  
					"recordsFiltered" => intval($totalRecords),
					"data" => $queryRecords,
					"sql" => $sqlRec
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
		}else if(
			in_array("administrator", $user_meta->roles) 
			|| in_array("PLT", $user_meta->roles) 
			|| in_array("PA", $user_meta->roles) 
			|| in_array("KPA", $user_meta->roles)
			|| in_array("tapd_keu", $user_meta->roles)
		){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-ssh-usulan.php';
		}else{
			echo 'User ini tidak dapat akses ke halaman ini :)';
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
					4 => 'harga',
					5 => 'created_at',
					6 => 'satuan',
					7 => 'keterangan_status',
					8 => 'status_upload_sipd',
					9 => 'created_user',
					10 => 'kode_standar_harga_sipd',
					11 => 'update_at',
					12 => 'keterangan_lampiran',
					13 => 'status_jenis_usulan',
					14 => 'jenis_produk',
					15 => 'tkdn',
					16 => 'status_by_admin',
					17 => 'status_by_tapdkeu',
					18 => 'keterangan_status_admin',
					19 => 'keterangan_status_tapdkeu',
					20 => 'status',
					21 => 'no_surat_usulan',
					22 => 'kode_kel_standar_harga',
					23 => 'nama_kel_standar_harga',
					24 => 'lampiran_1',
					25 => 'lampiran_2',
					26 => 'lampiran_3',
					27 => 'id'
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
					}else if($_POST['filter'] == 'diterima_admin'){
						$where .=" AND status_by_admin = 'approved' ";
					}else if($_POST['filter'] == 'ditolak_admin'){
						$where .=" AND status_by_admin = 'rejected' ";
					}else if($_POST['filter'] == 'diterima_tapdkeu'){
						$where .=" AND status_by_tapdkeu = 'approved' ";
					}else if($_POST['filter'] == 'ditolak_tapdkeu'){
						$where .=" AND status_by_tapdkeu = 'rejected' ";
					}
				}
				
				if(!empty($_POST['filter_opd'])){
					$where .=" AND id_sub_skpd = " . $_POST['filter_opd'];
				}

				/** Jika admin tampilkan semua data */
				if(
					!in_array("administrator",$user_meta->roles) &&
					!in_array("tapd_keu", $user_meta->roles)
				){
					$this_user_meta = get_user_meta($user_id);
					/** cari data user berdasarkan nama skpd */
					if(
						$this_user_meta['_crb_nama_skpd'][0] != ''
					){
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

				$order = 'DESC';
				if(!empty($params['order'][0]['dir'])){
					$order = $params['order'][0]['dir'];
				}

			 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]." ".$order." LIMIT ".$wpdb->prepare('%d', $params['start'])." ,".$wpdb->prepare('%d', $params['length'])." ";

				$queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				$totalRecords = $queryTot[0]['jml'];
				$queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

				foreach($queryRecords as $recKey => $recVal){
					$iconX		= '<i class="dashicons dashicons-trash"></i>';
					$iconEdit 	= '<i class="dashicons dashicons-edit"></i>';
					$detilUsulanSSH = '<a class="btn btn-sm btn-primary" href="#" onclick="return edit_ssh_usulan(\''.$recVal['status_jenis_usulan'].'\',\''.$recVal['id'].'\', \'detil\');" title="Detil komponen usulan SSH" style="text-decoration:none"><i class="dashicons dashicons-search" style="text-decoration:none"></i></a>&nbsp;';
					if(
						$recVal['status'] == 'waiting' || 
						$recVal['status'] == 'rejected'  
						// $recVal['status_upload_sipd'] != 1 
						// || in_array("administrator", $user_meta->roles) && $recVal['status_upload_sipd'] != 1
					){
						
						$can_edit = false;
						$can_delete = false;
						if(
							in_array("administrator", $user_meta->roles) ||
							in_array("tapd_keu", $user_meta->roles)
						){
							$can_edit = true;
							$can_delete = true;
						}elseif (in_array("pa", $user_meta->roles)) {
							if(
								$recVal['status_by_admin']=='' &&
								$recVal['status_by_tapdkeu']==''
							){
								$can_edit = true;
								$can_delete = true;
							}
						}
						
						if($can_edit){
							$editUsulanSSH = '<a class="btn btn-sm btn-warning" onclick="edit_ssh_usulan(\''.$recVal['status_jenis_usulan'].'\',\''.$recVal['id'].'\'); return false;" href="#" title="Edit komponen usulan SSH" style="text-decoration:none">'.$iconEdit.'</a>&nbsp;';
						}else{
							$jenis = ($recVal['status_upload_sipd'] == 1) ? 'upload' : 'usulan';
							$editUsulanSSH = '<a style="display:none" class="btn btn-sm btn-warning" onclick="cannot_change_ssh_usulan(\'ubah\',\''.$jenis.'\'); return false;" href="#" title="Edit komponen usulan SSH" style="text-decoration:none">'.$iconEdit.'</a>&nbsp;';
						}

						if($can_delete){
							if(empty($recVal['no_surat_usulan'])){
								$deleteUsulanSSH = '<a class="btn btn-sm btn-danger" onclick="delete_ssh_usulan(\''.$recVal['id'].'\'); return false;" href="#" title="Delete komponen usulan SSH" style="text-decoration:none">'.$iconX.'</a>&nbsp;';
							}else{
								$deleteUsulanSSH = '<a style="display:none" class="btn btn-sm btn-danger" onclick="cannot_change_ssh_usulan(\'hapus\',\''.$jenis.'\'); return false;" href="#" title="Delete komponen usulan SSH" style="text-decoration:none">'.$iconX.'</a>&nbsp;';
							}
						}else{
							$deleteUsulanSSH = '<a style="display:none" class="btn btn-sm btn-danger" onclick="cannot_change_ssh_usulan(\'hapus\',\''.$jenis.'\'); return false;" href="#" title="Delete komponen usulan SSH" style="text-decoration:none">'.$iconX.'</a>&nbsp;';
						}

					}else{
						$jenis = ($recVal['status_upload_sipd'] == 1) ? 'upload' : 'usulan';
						$editUsulanSSH = '<a style="display:none" class="btn btn-sm btn-warning" href="#" onclick="return cannot_change_ssh_usulan(\'ubah\',\''.$jenis.'\');" title="Edit komponen usulan SSH" style="text-decoration:none">'.$iconEdit.'</a>&nbsp;';
						$deleteUsulanSSH = '<a style="display:none" class="btn btn-sm btn-danger" href="#" onclick="return cannot_change_ssh_usulan(\'hapus\',\''.$jenis.'\');" title="Delete komponen usulan SSH" style="text-decoration:none">'.$iconX.'</a>&nbsp;';
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
							$keterangan_status .= '<span class="hide more">'.substr($recVal['keterangan_status'],20).'</span>&nbsp;<span class="more-bold medium-bold" onclick="readMore(this); return false;">more</span></td></tr>';
						}else{
							$keterangan_status = '<tr><td>Komentar: '.$recVal['keterangan_status'].'</td></tr>';
						}
					}

					$created_at = '<tr><td>Dibuat: '.date( 'Y-m-d H:i:s', strtotime($recVal['created_at'])).'</td></tr>';
					if(empty($recVal['update_at'])){
						$created_at .= '<tr><td>Update: -</td></tr>';
					}else{
						$created_at .= '<tr><td>Update: '.date( 'Y-m-d H:i:s', strtotime($recVal['update_at'])).'</td></tr>';
					}

					$keterangan_lampiran = "";
					if(!empty($recVal['keterangan_lampiran'])){
						if(strlen($recVal['keterangan_lampiran']) > 25){
							$keterangan_lampiran = '<tr><td class="in-kol-keterangan">Catatan: '.substr($recVal['keterangan_lampiran'],0,20).'<span class="dots">...</span>';
							$keterangan_lampiran .= '<span class="hide more">'.substr($recVal['keterangan_lampiran'],20).'</span>&nbsp;<span class="more-bold medium-bold" onclick="readMore(this); return false;">more</span></td></tr>';
						}else{
							$keterangan_lampiran = '<tr><td>Catatan: '.$recVal['keterangan_lampiran'].'</td></tr>';
						}
					}

					if($recVal['status_upload_sipd'] == 0 || $recVal['status_upload_sipd'] == NULL){
						$queryRecords[$recKey]['status_upload_sipd'] = 'Belum';
					}else if($recVal['status_upload_sipd'] == 1){
						$queryRecords[$recKey]['status_upload_sipd'] = 'Sudah';
					}

					$status_verif = '';
					if($recVal['status'] == 'approved'){
						$status_verif = '<span class="btn btn-sm btn-success">Disetujui</span>';
					}else if($recVal['status'] == 'rejected'){
						$status_verif = '<span class="btn btn-sm btn-danger">Ditolak</span>';
					}else if($recVal['status'] == 'waiting'){
						$status_verif = '<span class="btn btn-sm btn-warning">Menunggu</span>';
					}

					$status_verif_admin = '<table style="margin: 0;border-color:white;"><tbody>';
					$riwayat_admin='-';
					if(!empty($recVal['keterangan_status_admin'])){
						$data_riwayat_admin = explode("|", $recVal['keterangan_status_admin']);
						$riwayat_admin='<ul style="margin:15px">';
						foreach ($data_riwayat_admin as $key => $value) {
							$riwayat_admin.='<li>'.$value.'</li>';
						}
						$riwayat_admin.='</ul>';
					}
					
					if($recVal['status_by_admin'] == 'approved'){
						$status_verif_admin .= '<tr>
													<td style="border-color:white;">Usulan : <span class="medium-bold-2">Disetujui</span></td>
												</tr>
												<tr>
													<td style="border-color:white;">Riwayat Alasan : <span class="medium-bold-2">' .$riwayat_admin. '</span></td>
												</tr>
												';
					}else if($recVal['status_by_admin'] == 'rejected'){
						$status_verif_admin .= '
										<tr>
											<td style="border-color:white;">Usulan : <span class="medium-bold-2">Ditolak</span></td>
										</tr>
										<tr>
											<td style="border-color:white;">Riwayat Alasan : <span class="medium-bold-2">' .$riwayat_admin. '</span></td>
										</tr>';
					}
					$status_verif_admin .= '</tbody></table>';

					$status_verif_tapdkeu = '<table style="margin: 0;border-color:white;"><tbody>';
					$riwayat_tapdkeu='-';
					if(!empty($recVal['keterangan_status_tapdkeu'])){
						$data_riwayat_tapdkeu = explode("|", $recVal['keterangan_status_tapdkeu']);
						$riwayat_tapdkeu='<ul style="margin:15px">';
						foreach ($data_riwayat_tapdkeu as $key => $value) {
							$riwayat_tapdkeu.='<li>'.$value.'</li>';
						}
						$riwayat_tapdkeu.='</ul>';
					}
					
					if($recVal['status_by_tapdkeu'] == 'approved'){
						$status_verif_tapdkeu .= '<tr>
													<td style="border-color:white;">Usulan : <span class="medium-bold-2">Disetujui</span></td>
												</tr>
												<tr>
													<td style="border-color:white;">Riwayat Alasan : <span class="medium-bold-2">' . $riwayat_tapdkeu . '</span></td>
												</tr>
												';
					}else if($recVal['status_by_tapdkeu'] == 'rejected'){
						$status_verif_tapdkeu .= 
										'
										<tr>
											<td style="border-color:white;">Usulan : <span class="medium-bold-2">Ditolak</span></td>
										</tr>
										<tr>
											<td style="border-color:white;">Riwayat Alasan : <span class="medium-bold-2">' . $riwayat_tapdkeu . '</span></td>
										</tr>';
					}
					$status_verif_tapdkeu .= '</tbody></table>';

					if(
						in_array("administrator", $user_meta->roles) || 
						in_array("tapd_keu", $user_meta->roles)
					){
						$iconFilter = '<i class="dashicons dashicons-yes"></i>';
						$verify = '<a class="btn btn-sm btn-success" onclick="verify_ssh_usulan(\''.$recVal['id_standar_harga'].'\'); return false;" href="#" title="Verifikasi Item Usulan SSH" style="text-decoration:none">'.$iconFilter.'</a>&nbsp';
					}else{
						$verify = '';
					}
					$deleteCheck = '<input type="checkbox" class="delete_check" id="delcheck_'.$recVal['id_standar_harga'].'" onclick="checkcheckbox(); return true;" value="'.$recVal['id_standar_harga'].'" no-surat="'.$recVal['no_surat_usulan'].'">';

					$kode_komponen = '
						<table style="margin: 0;">
							<tr>
								<td>No Surat: '.$recVal['no_surat_usulan'].'</td>
							</tr>
							<tr>
								<td>'.$recVal['kode_kel_standar_harga'].' '.$recVal['nama_kel_standar_harga'].'</td>
							</tr>';
					if(!empty($recVal['kode_standar_harga_sipd'])){
						$kode_komponen .= '<tr><td>Data SIPD: '.$recVal['kode_standar_harga_sipd'].'</td></tr>';
					}
					$kode_komponen .= '</table>';

					$arr_jenis_produk = [0 => 'Luar Negeri', 1 => 'Dalam Negeri'];
					$jenis_produk = ($recVal['jenis_produk'] == 0 || $recVal['jenis_produk'] == 1) ? $arr_jenis_produk[$recVal['jenis_produk']] : '-';
					$spek_satuan = '<table style="margin: 0;"><tr><td>Spesifikasi: '.ucwords($recVal['spek']).'</tr></td>';
					$spek_satuan .= '<tr><td>Satuan: '.ucwords($recVal['satuan']).'</td></tr><tr><td>Jenis Produk: '.$jenis_produk.'</td></tr><tr><td>TKDN: '.$recVal['tkdn'].' %</td></tr></table>';

					$show_status = '<table style="margin: 0;">';
					$show_status .= '<tr>
										<td>
											Usulan: <span class="medium-bold-2">'.$status_verif.'</span>
										</td>
									</tr>';
					$show_status .= '<tr><td>Upload SIPD: <span class="medium-bold-2">'.ucwords($queryRecords[$recKey]['status_upload_sipd']).'</span></td></tr>';
					$show_status .= '<tr><td>Jenis: <span class="medium-bold-2">'.ucwords(str_replace("_"," ",$recVal['status_jenis_usulan'])).'</span></td></tr></table>';

					if($recVal['status_upload_sipd'] == 1){
						$tombol_aksi = '<a class="btn btn-sm btn-success" onclick="alert(\'Usulan SSH sudah diupload ke SIPD\'); return false;" href="#" title="Usulan SSH sudah diupload ke SIPD"><span class="dashicons dashicons-lock"></span></a>';
					}else{
						$tombol_aksi = $verify.$detilUsulanSSH.$editUsulanSSH.$deleteUsulanSSH;
					}

					$lampiran = '';
					if(!empty($recVal['lampiran_1'])){
						$lampiran.='Lampiran 1 : <a href="'.esc_url(plugin_dir_url(__DIR__).'public/media/ssh/'.$recVal['lampiran_1']).'" target="_blank">'.$recVal['lampiran_1'].'</a>';	
					}

					if(!empty($recVal['lampiran_2'])){
						$lampiran.='<br>Lampiran 2 : <a href="'.esc_url(plugin_dir_url(__DIR__).'public/media/ssh/'.$recVal['lampiran_2']).'" target="_blank">'.$recVal['lampiran_2'].'</a>';	
					}

					if(!empty($recVal['lampiran_3'])){
						$lampiran.='<br>Lampiran 3 : <a href="'.esc_url(plugin_dir_url(__DIR__).'public/media/ssh/'.$recVal['lampiran_3']).'" target="_blank">'.$recVal['lampiran_3'].'</a>';	
					}

					$queryRecords[$recKey]['aksi'] = $tombol_aksi;	
					$queryRecords[$recKey]['deleteCheckbox'] = $deleteCheck;
					$queryRecords[$recKey]['show_kode_komponen'] = $kode_komponen;
					$queryRecords[$recKey]['spek_satuan'] = $spek_satuan;
					$queryRecords[$recKey]['verify_admin'] = $status_verif_admin;
					$queryRecords[$recKey]['varify_tapdkeu'] = $status_verif_tapdkeu;
					$queryRecords[$recKey]['show_status'] = $show_status;
					$queryRecords[$recKey]['show_keterangan'] = '<table style="margin: 0;">'.$created_at.$created_user.$keterangan_status.$keterangan_lampiran.'</table>';
					$queryRecords[$recKey]['show_keterangan'] = $queryRecords[$recKey]['show_keterangan'] == '' ? '-' : $queryRecords[$recKey]['show_keterangan'];
					$queryRecords[$recKey]['lampiran'] = $lampiran;
				}

				$json_data = array(
					"draw" => intval( $params['draw'] ),   
					"recordsTotal" => intval( $totalRecords ),  
					"recordsFiltered" => intval($totalRecords),
					"data" => $queryRecords,
					"sql" => $sqlRec
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
								kode_kategori LIKE %s
								OR uraian_kategori LIKE %s
							)
						', $_POST['search'], $_POST['search'], $_POST['search']);
					}

					$data_kategori = $wpdb->get_results($wpdb->prepare("
						SELECT 
							* 
						FROM data_kelompok_satuan_harga 
						WHERE active=1
							AND tahun_anggaran = %d
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
						$data_id_ssh_usulan = $wpdb->get_results($wpdb->prepare('
							SELECT 
								kode_standar_harga_sipd 
							FROM data_ssh_usulan 
							WHERE id_standar_harga = %d
						', $id_standar_harga), ARRAY_A);
						$data_akun_ssh_usulan = $wpdb->get_results($wpdb->prepare('
							SELECT 
								id,
								id_akun,
								nama_akun 
							FROM data_ssh_rek_belanja_usulan 
							WHERE id_standar_harga = %d
						',$id_standar_harga), ARRAY_A);
						if(!empty($data_id_ssh_usulan[0]['kode_standar_harga_sipd'])){
							$data_id_ssh_existing = $wpdb->get_results($wpdb->prepare('
								SELECT 
									id_standar_harga 
								FROM data_ssh 
								WHERE kode_standar_harga = %s
							', $data_id_ssh_usulan[0]['kode_standar_harga_sipd']), ARRAY_A);
							$data_akun_ssh_existing_sipd = $wpdb->get_results($wpdb->prepare('
								SELECT 
									id,
									id_akun,
									nama_akun 
								FROM data_ssh_rek_belanja 
								WHERE id_standar_harga = %d
							',$data_id_ssh_existing[0]['id_standar_harga']), ARRAY_A);
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
						LIMIT %d, 20
					", 1, $tahun_anggaran, $_POST['page']), ARRAY_A);
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

		try{
			$table_content = '';
			if(!empty($_POST)){
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					if(
						!empty($_POST['kategori']) 
						&& !empty($_POST['nama_komponen']) 
						&& !empty($_POST['spesifikasi']) 
						&& !empty($_POST['satuan']) 
						&& !empty($_POST['harga_satuan']) 
						&& !empty($_POST['akun']) 
						&& !empty($_FILES['lapiran_usulan_ssh_1']) 
						&& !empty($_FILES['lapiran_usulan_ssh_2']) 
						&& !empty($_POST['id_sub_skpd'])
					){
						$kategori =trim(htmlspecialchars($_POST['kategori']));
						$nama_standar_harga = trim(htmlspecialchars($_POST['nama_komponen']));
						$spek = trim(htmlspecialchars($_POST['spesifikasi']));
						$satuan = trim(htmlspecialchars($_POST['satuan']));
						$harga = trim(htmlspecialchars($_POST['harga_satuan']));
						$akun = $_POST['akun'];
						$tahun_anggaran = trim(htmlspecialchars($_POST['tahun_anggaran']));
						$keterangan_lampiran = trim(htmlspecialchars($_POST['keterangan_lampiran']));
						$jenis_produk = !empty($_POST['keterangan_lampiran']) ? trim(htmlspecialchars($_POST['jenis_produk'])) : null;
						$jenis_produk = ($jenis_produk == 0 || $jenis_produk == 1) ? $jenis_produk : NULL;
						$tkdn = trim(htmlspecialchars($_POST['tkdn']));
						$tkdn = ($tkdn >= 0) ? $tkdn : NULL;
						$id_sub_skpd = $_POST['id_sub_skpd'];
						
						$data_kategori = $wpdb->get_results($wpdb->prepare("
							SELECT 
								* 
							FROM data_kelompok_satuan_harga 
							WHERE id_kategori = %d
						",$kategori), ARRAY_A);

						$data_akun = array();
						foreach(explode(",", $akun) as $v_akun){
							$data_akun[$v_akun] = $wpdb->get_results($wpdb->prepare("
								SELECT 
									id_akun,
									kode_akun,
									nama_akun 
								FROM data_akun 
								WHERE id_akun = %d
							",$v_akun), ARRAY_A);
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

						$last_kode_standar_harga = $wpdb->get_var($wpdb->prepare("
							SELECT 
								MAX(kode_standar_harga) as kode_standar_harga
							FROM `data_ssh_usulan` 
							WHERE kode_kel_standar_harga = %s
						", $data_kategori[0]['kode_kategori']));
						$last_kode_standar_harga = (empty($last_kode_standar_harga)) ? array("0") : explode(".",$last_kode_standar_harga);
						$last_kode_standar_harga = ((int) end($last_kode_standar_harga)) + 1;
						$last_kode_standar_harga = sprintf("%05d",$last_kode_standar_harga); // menambahkan angka nol di depan
						$last_kode_standar_harga = $data_kategori[0]['kode_kategori'].'.'.$last_kode_standar_harga;

						$id_standar_harga = $wpdb->get_var($wpdb->prepare("
							SELECT 
								MAX(id_standar_harga) as id_standar_harga
							FROM `data_ssh_usulan`
							WHERE tahun_anggaran=%d
						", $tahun_anggaran));
						$id_standar_harga = !empty($id_standar_harga) ? $id_standar_harga + 1 : 1;

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
							'tkdn' => $tkdn,
							'id_sub_skpd' => $id_sub_skpd
						);

						$upload_1 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_1'], ['jpg', 'jpeg', 'png', 'pdf']);

						if($upload_1['status']){
							$opsi_ssh['lampiran_1'] = $upload_1['filename'];
						}

						
						$upload_2 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_2'], ['jpg', 'jpeg', 'png', 'pdf']);
						if($upload_2['status']){
							$opsi_ssh['lampiran_2'] = $upload_2['filename'];
						}

						if(!empty($_FILES['lapiran_usulan_ssh_3'])){
							$upload_3 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_3'], ['jpg', 'jpeg', 'png', 'pdf']);
							if($upload_3['status']){
								$opsi_ssh['lampiran_3'] = $upload_3['filename'];
							}
						}
						
						$wpdb->insert('data_ssh_usulan',$opsi_ssh);
						
						$opsi_akun=[];
						foreach(explode(",", $akun) as $v_akun){
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
						throw new Exception('Harap diisi semua,tidak boleh ada yang kosong! Lampiran 1 dan 2 wajib terisi!');
					}
				}else{
					throw new Exception('Api Key tidak sesuai!');
				}
			}else{
				throw new Exception('Format tidak sesuai!');
			}

			echo json_encode($return);exit;

		}catch(Exception $e){
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;
		}
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
				if(empty($_POST['kategori'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'Kategori tidak boleh kosong!'
					);
				}else if(empty($_POST['spesifikasi'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'Spesifikasi tidak boleh kosong!'
					);
				}else if(empty($_POST['satuan'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'Satuan tidak boleh kosong!'
					);
				}else if(empty($_POST['harga_satuan'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'Harga tidak boleh kosong!'
					);
				}else if(empty($_POST['tahun_anggaran'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'Tahun tidak boleh kosong!'
					);
				}else if(empty($_POST['id'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'ID tidak boleh kosong!'
					);
				}else if(empty($_POST['id_sub_skpd'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'ID Sub SKPD tidak boleh kosong!'
					);
				}else{
					$kategori = trim(htmlspecialchars($_POST['kategori']));
					$nama_standar_harga = trim(htmlspecialchars($_POST['nama_komponen']));
					$spek = trim(htmlspecialchars($_POST['spesifikasi']));
					$satuan = trim(htmlspecialchars($_POST['satuan']));
					$harga = trim(htmlspecialchars($_POST['harga_satuan']));
					$tahun_anggaran = trim(htmlspecialchars($_POST['tahun_anggaran']));
					$keterangan_lampiran = !empty($_POST['keterangan_lampiran']) ? trim(htmlspecialchars($_POST['keterangan_lampiran'])) : null;
					$akun = $_POST['akun'];
					$id	= trim(htmlspecialchars($_POST['id']));
					$jenis_produk = trim(htmlspecialchars($_POST['jenis_produk']));
					$jenis_produk = ($jenis_produk == 0 || $jenis_produk == 1) ? $jenis_produk : NULL;
					$tkdn = trim(htmlspecialchars($_POST['tkdn']));
					$tkdn = ($tkdn >= 0) ? $tkdn : NULL;
					$id_sub_skpd = $_POST['id_sub_skpd'];
					
					$data_kategori = $wpdb->get_results($wpdb->prepare("
						SELECT 
							kode_kategori,
							uraian_kategori 
						FROM data_kelompok_satuan_harga 
						WHERE id_kategori = %d
					",$kategori), ARRAY_A);
					$data_this_id_ssh = $wpdb->get_results($wpdb->prepare('
						SELECT 
							id_standar_harga,
							kode_kel_standar_harga,
							kode_standar_harga,
							status,
							status_upload_sipd,
							status_by_admin,
							status_by_tapdkeu 
						FROM data_ssh_usulan 
						WHERE id = %d
					',$id), ARRAY_A);

					$status_edit=false;
					if(
						in_array("administrator", $user_meta->roles) ||
						in_array("tapd_keu", $user_meta->roles)
					){
						$status_edit=true;
					}elseif (in_array("pa", $user_meta->roles)) {
						if($data_this_id_ssh[0]['status'] == 'waiting'){
							if(
								$data_this_id_ssh[0]['status_by_admin']=='' && 
								$data_this_id_ssh[0]['status_by_tapdkeu']==''
							){
								$status_edit=true;
							}else{
								$return = array(
									'status' => 'error',
									'message'	=> "Data sudah dalam tahap verifikasi",
								);
							}
						}elseif ($data_this_id_ssh[0]['status'] == 'rejected') {
							$status_edit=true;
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> "User tidak diijinkan!",
						);
					}

					if($status_edit){
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
								$data_this_id_ssh[0]['id_standar_harga']
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
								$data_this_id_ssh[0]['id_standar_harga']
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
								$last_kode_standar_harga = $wpdb->get_results($wpdb->prepare("
									SELECT 
										id_standar_harga,
										kode_standar_harga 
									FROM `data_ssh_usulan` 
									WHERE kode_standar_harga=(
											SELECT 
												MAX(kode_standar_harga) 
											FROM `data_ssh_usulan` 
											WHERE kode_kel_standar_harga = %s
										)
								",$data_kategori[0]['kode_kategori']), ARRAY_A);
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
								'tkdn'	=> $tkdn,
								'status'	=> 'waiting',
								'id_sub_skpd'	=> $id_sub_skpd,
							);

							if(!empty($_FILES['lapiran_usulan_ssh_1'])){
								$upload_1 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_1'], ['jpg', 'jpeg', 'png', 'pdf']);
								if($upload_1['status']){
									$opsi_edit_ssh['lampiran_1'] = $upload_1['filename'];
									if(
										!empty($_POST['lapiran_usulan_ssh_1_old']) && 
										is_file(WPSIPD_PLUGIN_PATH.'public/media/ssh/'.$_POST['lapiran_usulan_ssh_1_old']))
									{
										unlink(WPSIPD_PLUGIN_PATH.'public/media/ssh/'.$_POST['lapiran_usulan_ssh_1_old']);
									}
								}
							}

							if(!empty($_FILES['lapiran_usulan_ssh_2'])){
								$upload_2 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_2'], ['jpg', 'jpeg', 'png', 'pdf']);
								if($upload_2['status']){
									$opsi_edit_ssh['lampiran_2'] = $upload_2['filename'];
									if(
										!empty($_POST['lapiran_usulan_ssh_2_old']) && 
										is_file(WPSIPD_PLUGIN_PATH.'public/media/ssh/'.$_POST['lapiran_usulan_ssh_2_old']))
									{
										unlink(WPSIPD_PLUGIN_PATH.'public/media/ssh/'.$_POST['lapiran_usulan_ssh_2_old']);
									}
								}
							}

							if(!empty($_FILES['lapiran_usulan_ssh_3'])){
								$upload_3 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_3'], ['jpg', 'jpeg', 'png', 'pdf']);
								if($upload_3['status']){
									$opsi_edit_ssh['lampiran_3'] = $upload_3['filename'];
									if(
										!empty($_POST['lapiran_usulan_ssh_3_old']) && 
										is_file(WPSIPD_PLUGIN_PATH.'public/media/ssh/'.$_POST['lapiran_usulan_ssh_3_old']))
									{
										unlink(WPSIPD_PLUGIN_PATH.'public/media/ssh/'.$_POST['lapiran_usulan_ssh_3_old']);
									}
								}
							}
	
							$wpdb->update('data_ssh_usulan', $opsi_edit_ssh, array(
								'id' => $id,
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
								$data_this_id_ssh[0]['id_standar_harga'],
								$tahun_anggaran
							), ARRAY_A);
							$cek_akun = array();
							foreach($akun_exist as $v_akun){
								$cek_akun[$v_akun['id_akun']] = $v_akun;
							}
	
							// get data detail akun
							$data_akun = array();
							foreach(explode(",", $akun) as $v_akun){
								$data_akun[$v_akun] = $wpdb->get_results($wpdb->prepare("
									SELECT 
										id_akun,
										kode_akun,
										nama_akun 
									FROM data_akun 
									WHERE id_akun = %d
								",$v_akun), ARRAY_A);
							}
	
							// input dan update akun
							foreach(explode(",", $akun) as $id_akun){
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
				if(empty($_POST['id'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'ID tidak boleh kosong!'
					);
				}else if(empty($_POST['tahun_anggaran'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'Tahun anggaran tidak boleh kosong!'
					);
				}else if(empty($_POST['harga_satuan'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'Harga tidak boleh kosong!'
					);
				}else if(empty($_POST['keterangan_lampiran'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'Keterangan tidak boleh kosong!'
					);
				}else{
					$tahun_anggaran = trim(htmlspecialchars($_POST['tahun_anggaran']));
					$keterangan_lampiran = trim(htmlspecialchars($_POST['keterangan_lampiran']));
					$id = trim(htmlspecialchars($_POST['id']));
					$harga_satuan = trim(htmlspecialchars($_POST['harga_satuan']));
					
					$data_this_id_ssh = $wpdb->get_results($wpdb->prepare('
						SELECT 
							id_standar_harga,
							kode_kel_standar_harga,
							kode_standar_harga,
							nama_standar_harga,
							satuan,
							spek,
							harga,
							status,
							status_upload_sipd 
						FROM data_ssh_usulan 
						WHERE id = %d
					',$id), ARRAY_A);

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
								$data_this_id_ssh[0]['id_standar_harga']
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
								'keterangan_lampiran' => $keterangan_lampiran
							);

							if(!empty($_FILES['lapiran_usulan_ssh_1'])){
								$upload_1 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_1'], ['jpg', 'jpeg', 'png', 'pdf']);
								if($upload_1['status']){
									$opsi_edit_ssh['lampiran_1'] = $upload_1['filename'];
								}
							}

							if(!empty($_FILES['lapiran_usulan_ssh_2'])){
								$upload_2 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_2'], ['jpg', 'jpeg', 'png', 'pdf']);
								if($upload_2['status']){
									$opsi_edit_ssh['lampiran_2'] = $upload_2['filename'];
								}
							}

							if(!empty($_FILES['lapiran_usulan_ssh_3'])){
								$upload_3 = CustomTrait::uploadFile($_POST['api_key'], $path = WPSIPD_PLUGIN_PATH.'public/media/ssh/', $_FILES['lapiran_usulan_ssh_3'], ['jpg', 'jpeg', 'png', 'pdf']);
								if($upload_3['status']){
									$opsi_edit_ssh['lampiran_3'] = $upload_3['filename'];
								}
							}
	
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
				$id	= $_POST['id'];
				$tahun_anggaran	= $_POST['tahun_anggaran'];

				$data_this_id_ssh = $wpdb->get_row($wpdb->prepare('
					SELECT 
						id_standar_harga,
						kode_kel_standar_harga,
						kode_standar_harga,
						status,
						status_upload_sipd 
					FROM data_ssh_usulan 
					WHERE id = %d
				',$id), ARRAY_A);

				if($data_this_id_ssh['status'] == 'waiting' || in_array("administrator", $user_meta->roles)){
					if($data_this_id_ssh['status_upload_sipd'] != 1){
						$wpdb->delete('data_ssh_usulan', array(
							'tahun_anggaran' => $tahun_anggaran,
							'id' => $id
						), array('%d', '%d'));
					
						$wpdb->delete('data_ssh_rek_belanja_usulan', array(
							'tahun_anggaran' => $tahun_anggaran,
							'id_standar_harga' => $data_this_id_ssh['id_standar_harga']
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

				$data_this_id_ssh = $wpdb->get_results($wpdb->prepare('
					SELECT 
						id_standar_harga,
						kode_kel_standar_harga,
						kode_standar_harga,
						status 
					FROM data_ssh_usulan 
					WHERE id_standar_harga = %d
				',$id_standar_harga), ARRAY_A);

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
				if(!empty($_POST['type']) && $_POST['type'] == 'ri'){
					$kategori = json_decode(stripslashes(html_entity_decode($_POST['kategori'])), true);						
				}else{
					$kategori = $_POST['kategori'];
				}

				if(empty($_POST['type'])){
					$wpdb->update('data_kelompok_satuan_harga', array('active' => 0), array(
						'tahun_anggaran'	=> $_POST['tahun_anggaran'],
						'tipe_kelompok' => $_POST['tipe_ssh']
					));	
				}
				foreach ($kategori as $k => $v) {
					if(!empty($_POST['type']) && $_POST['type'] == 'ri'){
						$cek = $wpdb->get_var($wpdb->prepare("
							SELECT 
								id 
							from data_kelompok_satuan_harga 
							where tahun_anggaran=%d 
								AND id_kategori=%s",
							$_POST['tahun_anggaran'],
							$v['id_kategori']
						));
					}else{
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
					}
					if(isset($v['active'])){
						$v['active'] = 1;
					}
					$opsi = array(
						'id_kategori' => $v['id_kategori'],
						'kode_kategori' => $v['kode_kategori'],
						'uraian_kategori' => $v['uraian_kategori'],
						'tipe_kelompok' => $v['kelompok'],
						'active' => $v['active'],
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
			'message' => 'Berhasil singkronisasi data satuan!',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['type']) && $_POST['type'] == 'ri'){
					$satuan = json_decode(stripslashes(html_entity_decode($_POST['satuan'])), true);						
				}else{
					$satuan = $_POST['satuan'];
				}
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
						'id_satuan' => $v['id_satuan'],
						'nama_satuan' => $v['satuan'],
						'tahun_anggaran' => $_POST['tahun_anggaran']
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

	public function simpan_surat_usulan_ssh(){
		global $wpdb;
		$return = array(
			'action' => $_POST['action'],
			'status' => 'success',
			'message' => 'Berhasil simpan surat usulan SSH!'
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(empty($_POST['nomor_surat'])){
					$return['status'] = 'error';
					$return['message'] = 'Nomor surat tidak boleh kosong!';
				}else if(empty($_POST['idskpd'])){
					$return['status'] = 'error';
					$return['message'] = 'SKPD tidak boleh kosong!';
				}else if(empty($_POST['tahun_anggaran'])){
					$return['status'] = 'error';
					$return['message'] = 'Tahun anggaran tidak boleh kosong!';
				}else if(empty($_POST['ids'])){
					$return['status'] = 'error';
					$return['message'] = 'Usulan SSH tidak boleh kosong!';
				}else if(empty($_POST['acuanSsh'])){
					$return['status'] = 'error';
					$return['message'] = 'Acuan penyusunan SSH wajib dipilih!';
				}

				if($return['status'] == 'success'){
					$nomor_surat = $_POST['nomor_surat'];
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$catatan = $_POST['catatan'];
					$idskpd = $_POST['idskpd'];
					if(!empty($_POST['ubah'])){
						$catatan_verifikator = $_POST['catatan_verifikator'];
					}else{
						$ids = explode(',', $_POST['ids']);
					}
					$cek_surat_id = $wpdb->get_var($wpdb->prepare("
						SELECT
							id
						FROM data_surat_usulan_ssh
						WHERE nomor_surat=%s
							AND tahun_anggaran=%d
							AND active=1
					", $nomor_surat, $tahun_anggaran));

					// jika surat id ditemukan dan bukan proses update
					if(!empty($cek_surat_id) && empty($_POST['ubah'])){
						$return['status'] = 'error';
						$return['message'] = 'Nomor surat sudah digunakan!';
						
					// jika surat id tidak ditemukan dan proses update
					}else if(empty($cek_surat_id) && !empty($_POST['ubah'])){
						$return['status'] = 'error';
						$return['message'] = 'Nomor surat tidak ditemukan!';
					}
				}
				if($return['status'] == 'success'){
					$user_id = um_user( 'ID' );
					$user_info = get_userdata($user_id);

					$data = array(
						'nomor_surat' => $nomor_surat,
						'idskpd' => $idskpd,
						'catatan' => $catatan,
						'active' => 1,
						'update_at' => date("Y-m-d H:i:s"),
						'tahun_anggaran' => $tahun_anggaran,
						'jenis_survey' => 0,
						'jenis_juknis' => 0,
					);

					foreach ($_POST['acuanSsh'] as $key => $value) {
						if($value==1){
							$data['jenis_survey']=$value;
						}elseif ($value==2) {
							$data['jenis_juknis']=$value;
						}
					}

					if(!empty($_POST['ubah'])){
						$data['catatan_verifikator'] = $catatan_verifikator;
						if(!empty($_FILES['file'])){
							$upload = CustomTrait::uploadFile(
								$_POST['api_key'], 
								WPSIPD_PLUGIN_PATH.'public/media/ssh/', 
								$_FILES['file'], 
								array('jpg', 'jpeg', 'png', 'pdf'), 
								2097152, 
								$nomor_surat
							);
							if($upload['status']){
								$data['nama_file'] = $upload['filename'];
							}
						}
					}else{
						$data['created_user'] = $user_info->display_name;
					}
					if(empty($cek_surat_id)){
						$wpdb->insert('data_surat_usulan_ssh', $data);
						foreach($ids as $id){
							$wpdb->update('data_ssh_usulan', array(
								'no_surat_usulan' => $nomor_surat
							), array(
								'id_standar_harga' => $id
							));
						}
					}else{
						$wpdb->update('data_surat_usulan_ssh', $data, array(
							'id' => $cek_surat_id
						));
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

	public function get_data_summary_ssh_sipd(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'message' => 'Berhasil get data summary rekapitulasi SSH!'
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				if(empty($_POST['tahun_anggaran'])){
					$return = array(
						'status' => 'error',
						'message'	=> 'Tahun anggaran tidak boleh kosong!'
					);
				}

				$data = $wpdb->get_row($wpdb->prepare("
					SELECT 
						(SELECT COUNT(id) FROM `data_ssh_usulan` WHERE tahun_anggaran=%d AND status='rejected') AS rejected,
    					(SELECT COUNT(id) FROM `data_ssh_usulan` WHERE tahun_anggaran=%d AND status='approved') AS approved,
    					(SELECT COUNT(id) FROM `data_ssh_usulan` WHERE tahun_anggaran=%d AND status='waiting') AS waiting;", 
    					$_POST['tahun_anggaran'],
    					$_POST['tahun_anggaran'],
    					$_POST['tahun_anggaran']));

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
}