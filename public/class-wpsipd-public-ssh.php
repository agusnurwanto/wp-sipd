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
								'verified_by' => um_user( 'ID' ),
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
}