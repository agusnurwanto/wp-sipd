<?php

class Wpsipd_Public_Base_2
{
	public function monitoring_rup($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitoring-rup.php';
	}

	public function get_data_monitoring_rup(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['tahun_anggaran']) && !empty($_POST['id_lokasi'])){
					$params = $columns = $totalRecords = array();
					$params = $_REQUEST;
					$columns = array(
						0 => 'id_skpd',
						1 => 'nama_skpd'
					);

					$where_sirup = $where_sipd = $where_data_unit = $sqlRec ="";

					// check search value exist
					if( !empty($params['search']['value']) ) {
						$where_data_unit .=" AND ( nama_skpd LIKE ".$wpdb->prepare('%s', "%".$params['search']['value']."%").")";  
					}

					$sqlTotDataUnit = "SELECT 
											count(*) as jml 
										FROM 
											`data_unit`
										where 
											tahun_anggaran=".$_POST['tahun_anggaran']."
											and is_skpd=1  
											and active=1".$where_data_unit;
					$totalRecords = $wpdb->get_results($sqlTotDataUnit, ARRAY_A);
					$totalRecords = $totalRecords[0]['jml'];

					$sqlRec = 'SELECT 
									* 
								from 
									data_unit 
								where 
									tahun_anggaran='.$_POST['tahun_anggaran'].'
									and is_skpd=1  
									and active=1'.$where_data_unit;
					$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";

					$skpd = $wpdb->get_results($sqlRec, ARRAY_A);

					foreach ($skpd as $k => $opd) {
						$id_skpd_sirup = get_option('_crb_unit_sirup_'.$opd['id_skpd']);

						$where_sipd = '
							tahun_anggaran='.$_POST['tahun_anggaran'].' 
								and active=1 
								and id_skpd='.$opd['id_skpd'].'
						';
						$data_total_pagu_sipd = $wpdb->get_row('
							select 
								sum(pagu) as total_sipd
							from 
								data_sub_keg_bl 
							where 
								'.$where_sipd
						, ARRAY_A);
						$skpd[$k]['total_pagu_sipd'] = (!empty($data_total_pagu_sipd['total_sipd'])) ? number_format($data_total_pagu_sipd['total_sipd'],0,",",".") : '-' ;

						$where_non_pengadaan = 'a.kode_akun LIKE "5.1.01.03%"
												and a.active=1
												and b.id_skpd='.$opd['id_skpd'].' 
												and b.tahun_anggaran='.$_POST['tahun_anggaran'].' 
												and b.active=1 
												';
						$data_total_pagu_non_pengadaan = $wpdb->get_row('
							select 
								sum(a.total_harga) as total_non_pengadaan
							from 
								data_rka as a 
							join 
								data_sub_keg_bl as b 
								on a.kode_sbl = b.kode_sbl
							where 
								'.$where_non_pengadaan
						, ARRAY_A);
						$skpd[$k]['total_pagu_non_pengadaan'] = (!empty($data_total_pagu_non_pengadaan['total_non_pengadaan'])) ? number_format($data_total_pagu_non_pengadaan['total_non_pengadaan'],0,",",".") : '-' ;

						$where_sirup = '
							tahun_anggaran='.$_POST['tahun_anggaran'].'
							and active=1
							and idlokasi='.$_POST['id_lokasi'].'
							and satuanKerja="'.$opd['nama_skpd'].'"
						';
						$data_total_pagu_sirup = $wpdb->get_row('
							select 
								sum(pagu) as total_sirup
							from 
								data_sirup_lokal
							where 
								'.$where_sirup
						, ARRAY_A);
						$skpd[$k]['total_pagu_sirup'] = (!empty($data_total_pagu_sirup['total_sirup'])) ? number_format($data_total_pagu_sirup['total_sirup'],0,",",".") : '-';

						$total_paket_penyedia_sirup = $wpdb->get_results($wpdb->prepare('
							SELECT
								COUNT(DISTINCT paket) as total_paket
							FROM 
								data_sirup_lokal
							WHERE
								active=1
								and idlokasi=%d
								and satuanKerja=%s
								and tahun_anggaran=%d',
								$_POST['id_lokasi'],
								$opd['nama_skpd'],
								$_POST['tahun_anggaran'])
								,ARRAY_A);
						$skpd[$k]['cek_query'] = $wpdb->last_query;
						$skpd[$k]['total_paket_penyedia_sirup'] = (!empty($total_paket_penyedia_sirup[0]['total_paket'])) ? $total_paket_penyedia_sirup[0]['total_paket'] : '-';

						$total_pagu_rekap_penyedia_sirup = $wpdb->get_results($wpdb->prepare('
							SELECT
								pagu_penyedia
							FROM
								data_skpd_sirup
							WHERE
								active=1
								and id_satuan_kerja=%d
								and tahun_anggaran=%d',
								$id_skpd_sirup,
								$_POST['tahun_anggaran'] )
							, ARRAY_A);

						$skpd[$k]['total_pagu_rekap_penyedia_sirup'] = (!empty($total_pagu_rekap_penyedia_sirup[0]['pagu_penyedia'])) ? number_format($total_pagu_rekap_penyedia_sirup[0]['pagu_penyedia'] * 1000000,0,",",".") : '-';

						$total_paket_rekap_penyedia_sirup = $wpdb->get_results($wpdb->prepare('
							SELECT
								paket_penyedia
							FROM
								data_skpd_sirup
							WHERE
								active=1
								and id_satuan_kerja=%d
								and tahun_anggaran=%d',
								$id_skpd_sirup,
								$_POST['tahun_anggaran']
						),ARRAY_A);
						$skpd[$k]['total_paket_rekap_penyedia_sirup'] = (!empty($total_paket_rekap_penyedia_sirup[0]['paket_penyedia'])) ? $total_paket_rekap_penyedia_sirup[0]['paket_penyedia'] : '-';

						$data_total_pagu_rekap_sirup = $wpdb->get_results($wpdb->prepare('
							SELECT 
								total_pagu
							FROM 
								data_skpd_sirup
							WHERE 
								active=1
								and id_satuan_kerja=%d
								and tahun_anggaran=%d',
								$id_skpd_sirup,
								$_POST['tahun_anggaran'] )
						, ARRAY_A);

						$skpd[$k]['total_pagu_rekap_sirup'] = (!empty($data_total_pagu_rekap_sirup[0]['total_pagu'])) ? number_format($data_total_pagu_rekap_sirup[0]['total_pagu'] * 1000000,0,",",".") : '-';

						$skpd[$k]['selisih_pagu'] = $data_total_pagu_sipd['total_sipd'] - $data_total_pagu_non_pengadaan['total_non_pengadaan'] - $data_total_pagu_sirup['total_sirup'];
						$skpd[$k]['selisih_pagu'] = number_format($skpd[$k]['selisih_pagu'],0,",",".");
						$skpd[$k]['keterangan'] = '-';
					}


					$json_data = array(
						"draw"            => intval( $params['draw'] ),  
						"recordsTotal"    => intval( $totalRecords ), 
						"recordsFiltered" => intval( $totalRecords ),
						"data"            => $skpd
					);

					die(json_encode($json_data));
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'Harap diisi semua,API setting tidak boleh ada yang kosong!'
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

		/** Submit lock data jadwal RPJPD */
	public function submit_lock_schedule_rpjpd(){
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
						if(!empty($data_this_id[0])){
							if($time_now > $data_this_id[0]['waktu_awal']){
								$status_check = array(0,NULL,2);
								if(in_array($data_this_id[0]['status'],$status_check)){
									//lock data penjadwalan
									$wpdb->update('data_jadwal_lokal', array('waktu_akhir' => $time_now,'status' => 1), array(
										'id_jadwal_lokal'	=> $id_jadwal_lokal
									));

									$columns_1 = array('visi_teks','update_at');
		
									$sql_backup_data_rpjpd_visi =  "INSERT INTO data_rpjpd_visi_history (".implode(', ', $columns_1).",id_jadwal,id_asli)
												SELECT ".implode(', ', $columns_1).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
												FROM data_rpjpd_visi";

									$queryRecords1 = $wpdb->query($sql_backup_data_rpjpd_visi);

									$columns_2 = array('id_visi','misi_teks','urut_misi','update_at');
		
									$sql_backup_data_rpjpd_misi =  "INSERT INTO data_rpjpd_misi_history (".implode(', ', $columns_2).",id_jadwal,id_asli)
												SELECT ".implode(', ', $columns_2).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
												FROM data_rpjpd_misi";

									$queryRecords2 = $wpdb->query($sql_backup_data_rpjpd_misi);

									$columns_3 = array('id_misi','saspok_teks','urut_saspok','update_at');
		
									$sql_backup_data_rpjpd_sasaran =  "INSERT INTO data_rpjpd_sasaran_history (".implode(', ', $columns_3).",id_jadwal,id_asli)
												SELECT ".implode(', ', $columns_3).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
												FROM data_rpjpd_sasaran";

									$queryRecords3 = $wpdb->query($sql_backup_data_rpjpd_sasaran);

									$columns_4 = array('id_saspok','kebijakan_teks','urut_kebijakan','update_at');
		
									$sql_backup_data_rpjpd_kebijakan =  "INSERT INTO data_rpjpd_kebijakan_history (".implode(', ', $columns_4).",id_jadwal,id_asli)
												SELECT ".implode(', ', $columns_4).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
												FROM data_rpjpd_kebijakan";

									$queryRecords4 = $wpdb->query($sql_backup_data_rpjpd_kebijakan);

									$columns_5 = array('id_kebijakan','isu_teks','urut_isu','update_at',);
		
									$sql_backup_data_rpjpd_isu =  "INSERT INTO data_rpjpd_isu_history (".implode(', ', $columns_5).",id_jadwal,id_asli)
												SELECT ".implode(', ', $columns_5).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
												FROM data_rpjpd_isu";

									$queryRecords5 = $wpdb->query($sql_backup_data_rpjpd_isu);

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
								'message'	=> "Penjadwalan tidak ditemukan!",
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

	public function input_renja($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-input-renja.php';
	}

	public function monitor_rak($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		if(!empty($atts['id_skpd'])){
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-rak.php';
		}
	}

	public function get_data_jadwal_lokal(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					$data = $wpdb->get_results("SELECT * FROM data_jadwal_lokal WHERE status=1 ORDER BY id_jadwal_lokal");

					echo json_encode([
						'status' => true,
						'data' => $data
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

	public function get_sasaran_rpjm_history(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					$data = $wpdb->get_results("SELECT a.id_unik, a.sasaran_teks, b.id_program FROM data_rpjmd_sasaran_lokal_history a INNER JOIN data_rpjmd_program_lokal_history b ON a.id_unik=b.kode_sasaran WHERE b.id_unit=".$_POST['id_unit']." AND a.id_jadwal=".$_POST['id_jadwal']." AND a.status=1;");


					echo json_encode([
						'status' => true,
						'data' => $data
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

	public function get_tujuan_renstra(){

		global $wpdb;

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				if($_POST['type'] == 1){
					$sql = $wpdb->prepare("
						SELECT * FROM data_renstra_tujuan_lokal
							WHERE id_unik IS NOT NULL AND
								id_unik_indikator IS NULL AND
								status=1 AND 
								is_locked=0 AND 
								active=1 ORDER BY id");
					$tujuan = $wpdb->get_results($sql, ARRAY_A);

				}else{

					$tahun_anggaran = $input['tahun_anggaran'];

					$sql = $wpdb->prepare("
						select 
							* 
						from data_renstra_tujuan
						where tahun_anggaran=%d
								and id_misi=%d
								and active=1
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

	public function add_tujuan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					$jadwal_renstra = $wpdb->get_row("SELECT a.*, (SELECT id_tipe FROM data_jadwal_lokal WHERE id_jadwal_lokal=a.relasi_perencanaan) id_tipe_relasi FROM data_jadwal_lokal a WHERE DATE_ADD(NOW(), INTERVAL 1 HOUR) > a.waktu_awal AND DATE_ADD(NOW(), INTERVAL 1 HOUR) < a.waktu_akhir AND a.id_tipe=4 AND a.status=0");

					if(empty($jadwal_renstra->relasi_perencanaan)){
						throw new Exception("Relasi perencanaan jadwal renstra belum diatur", 1);
					}

					switch ($jadwal_renstra->id_tipe_relasi) {
						case '2': // rpjm
							$data = $wpdb->get_results("SELECT a.id_unik, a.sasaran_teks, b.id_program FROM data_rpjmd_sasaran_lokal_history a INNER JOIN data_rpjmd_program_lokal_history b ON a.id_unik=b.kode_sasaran WHERE b.id_unit=".$_POST['id_unit']." AND a.id_jadwal=".$jadwal_renstra->relasi_perencanaan." AND a.status=1;");
							break;
						
						case '3': // rpd
							$data = $wpdb->get_results("SELECT a.id_unik, a.sasaran_teks, b.id_program FROM data_rpd_sasaran_lokal_history a INNER JOIN data_rpd_program_lokal_history b ON a.id_unik=b.kode_sasaran WHERE b.id_unit=".$_POST['id_unit']." AND a.id_jadwal=".$jadwal_renstra->relasi_perencanaan." AND a.status=1;");
							break;

						default:

							throw new Exception("Relasi perencanaan tidak diketahui", 1);
							
							break;
					}

					echo json_encode([
						'status' => true,
						'data' => $data
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

	public function submit_tujuan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					$data = json_decode(stripslashes($_POST['data']), true);

					if(empty($data['sasaran_parent'])){
						throw new Exception('Sasaran RPJM/RPD wajib dipilih!');
					}

					if(empty($data['tujuan_teks'])){
						throw new Exception('Tujuan tidak boleh kosong!');
					}

					if(empty($data['urut_tujuan'])){
						throw new Exception('Urut tujuan tidak boleh kosong!');
					}

					$raw_sasaran_parent = explode("|", $data['sasaran_parent']);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_tujuan_lokal
							WHERE tujuan_teks='".trim($data['tujuan_teks'])."'
										AND kode_sasaran_rpjm='".$raw_sasaran_parent[0]."'
										AND id_unik is not null
										AND id_unik_indikator is null
										AND is_locked=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Tujuan : '.$data['tujuan_teks'].' sudah ada!');
					}

					$dataBidangUrusan = $wpdb->get_row("SELECT DISTINCT id_bidang_urusan, kode_bidang_urusan, nama_bidang_urusan FROM data_prog_keg WHERE id_program=".$raw_sasaran_parent[1]);

					if(empty($dataBidangUrusan)){
						throw new Exception('Bidang urusan tidak ditemukan!');
					}

					$dataUnit = $wpdb->get_row("SELECT * FROM data_unit WHERE id_unit=".$data['id_unit']." AND tahun_anggaran=".get_option('_crb_tahun_anggaran_sipd')." AND active=1 AND is_skpd=1 order by id_skpd ASC;");

					if(empty($dataUnit)){
						throw new Exception('Unit kerja tidak ditemukan!');
					}

					$status = $wpdb->insert('data_renstra_tujuan_lokal', [
						'id_bidang_urusan' => $dataBidangUrusan->id_bidang_urusan,
						'id_unik' => $this->generateRandomString(), // kode_tujuan
						'id_unit' => $dataUnit->id_unit,
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'kode_bidang_urusan' => $dataBidangUrusan->kode_bidang_urusan,
						'kode_sasaran_rpjm' => $raw_sasaran_parent[0],
						'kode_skpd' => $dataUnit->kode_skpd,
						'nama_bidang_urusan' => $dataBidangUrusan->nama_bidang_urusan,
						'nama_skpd' => $dataUnit->nama_skpd,
						'status' => 1,
						'tujuan_teks' => $data['tujuan_teks'],
						'urut_tujuan' => $data['urut_tujuan'],
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

	public function edit_tujuan_renstra(){
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$jadwal_renstra = $wpdb->get_row("SELECT a.*, (SELECT id_tipe FROM data_jadwal_lokal WHERE id_jadwal_lokal=a.relasi_perencanaan) id_tipe_relasi FROM data_jadwal_lokal a WHERE DATE_ADD(NOW(), INTERVAL 1 HOUR) > a.waktu_awal AND DATE_ADD(NOW(), INTERVAL 1 HOUR) < a.waktu_akhir AND a.id_tipe=4 AND a.status=0");

					if(empty($jadwal_renstra->relasi_perencanaan)){
						throw new Exception("Relasi perencanaan jadwal renstra belum diatur", 1);
					}

					switch ($jadwal_renstra->id_tipe_relasi) {
						case '2': // rpjm
							$sasaran_parent = $wpdb->get_results("SELECT a.id_unik, a.sasaran_teks, b.id_program FROM data_rpjmd_sasaran_lokal_history a INNER JOIN data_rpjmd_program_lokal_history b ON a.id_unik=b.kode_sasaran WHERE b.id_unit=".$_POST['id_unit']." AND a.id_jadwal=".$jadwal_renstra->relasi_perencanaan." AND a.status=1;");
						break;
							
						case '3': // rpd
							$sasaran_parent = $wpdb->get_results("SELECT a.id_unik, a.sasaran_teks, b.id_program FROM data_rpd_sasaran_lokal_history a INNER JOIN data_rpd_program_lokal_history b ON a.id_unik=b.kode_sasaran WHERE b.id_unit=".$_POST['id_unit']." AND a.id_jadwal=".$jadwal_renstra->relasi_perencanaan." AND a.status=1;");
							break;

						default:
							throw new Exception("Relasi perencanaan tidak diketahui", 1);
								
						break;
					}

					$tujuan = $wpdb->get_row("SELECT a.* FROM data_renstra_tujuan_lokal a WHERE a.id=".$_POST['id_tujuan']);

					echo json_encode([
						'status' => true,
						'tujuan' => $tujuan,
						'sasaran_parent' => $sasaran_parent,
						'message' => 'Sukses get tujuan by id'
					]);exit;

				}else{
					throw new Exception("'Api key tidak sesuai'", 1);
					
				}
			}else{
				throw new Exception("Format tidak sesuai", 1);
				
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);exit;		
		}
	}

	public function update_tujuan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
						$data = json_decode(stripslashes($_POST['data']), true);

						if(empty($data['sasaran_parent'])){
							throw new Exception('Sasaran RPJM/RPD wajib dipilih!');
						}

						if(empty($data['tujuan_teks'])){
							throw new Exception('Tujuan tidak boleh kosong!');
						}

						if(empty($data['urut_tujuan'])){
							throw new Exception('Urut tujuan tidak boleh kosong!');
						}

						$raw_sasaran_parent = explode("|", $data['sasaran_parent']);

						$id_cek = $wpdb->get_var("
							SELECT id FROM data_renstra_tujuan_lokal
								WHERE tujuan_teks='".trim($data['tujuan_teks'])."'
											AND kode_sasaran_rpjm!='".$raw_sasaran_parent[0]."'
											AND id_unik is not null
											AND id_unik_indikator is null
											AND is_locked=0
											AND status=1
											AND active=1
									");
						
						if(!empty($id_cek)){
							throw new Exception('Tujuan : '.$data['tujuan_teks'].' sudah ada!');
						}

						$dataBidangUrusan = $wpdb->get_row("SELECT DISTINCT id_bidang_urusan, kode_bidang_urusan, nama_bidang_urusan FROM data_prog_keg WHERE id_program=".$raw_sasaran_parent[1]);

						if(empty($dataBidangUrusan)){
							throw new Exception('Bidang urusan tidak ditemukan!');
						}

						$dataUnit = $wpdb->get_row("SELECT * FROM data_unit WHERE id_unit=".$data['id_unit']." AND tahun_anggaran=".get_option('_crb_tahun_anggaran_sipd')." AND active=1 AND is_skpd=1 order by id_skpd ASC;");

						if(empty($dataUnit)){
							throw new Exception('Unit kerja tidak ditemukan!');
						}

						$status = $wpdb->update('data_renstra_tujuan_lokal', [
							'id_bidang_urusan' => $dataBidangUrusan->id_bidang_urusan,
							'id_unit' => $dataUnit->id_unit,
							'kode_bidang_urusan' => $dataBidangUrusan->kode_bidang_urusan,
							'kode_sasaran_rpjm' => $raw_sasaran_parent[0],
							'kode_skpd' => $dataUnit->kode_skpd,
							'nama_bidang_urusan' => $dataBidangUrusan->nama_bidang_urusan,
							'nama_skpd' => $dataUnit->nama_skpd,
							'tujuan_teks' => $data['tujuan_teks'],
							'urut_tujuan' => $data['urut_tujuan'],
							'update_at' => date('Y-m-d H:i:s')
						], [
							'id_unik' => $data['id_unik'],
							'status' => 1,
							'active' => 1,
						]);

						if(!$status){
							throw new Exception('Terjadi kesalahan saat ubah data, harap hubungi admin!');
						}

						echo json_encode([
							'status' => true,
							'message' => 'Sukses ubah tujuan',
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

	public function delete_tujuan_renstra(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					
					$id_cek = $wpdb->get_var("SELECT * FROM data_renstra_sasaran_lokal WHERE kode_tujuan='" . $_POST['id_unik'] . "' AND is_locked=0 AND status=1 AND active=1");

					if(!empty($id_cek)){
						throw new Exception("Tujuan sudah digunakan oleh sasaran", 1);
					}

					$id_cek = $wpdb->get_var("SELECT * FROM data_renstra_tujuan_lokal WHERE id_unik='" . $_POST['id_unik']. "' AND id_unik_indikator IS NOT NULL AND is_locked=0 AND status=1 AND active=1");

					if(!empty($id_cek)){
						throw new Exception("Tujuan sudah digunakan oleh indikator tujuan", 1);
					}
					
					$wpdb->get_results("DELETE FROM data_renstra_tujuan_lokal WHERE id=".$_POST['id_tujuan']);

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

	public function get_indikator_tujuan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					if($_POST['type']==1){
						$sql = $wpdb->prepare("
							SELECT * FROM data_renstra_tujuan_lokal 
								WHERE 
									id_unik=%s AND 
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND 
									status=1 AND 
									active=1", $_POST['id_unik']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					}else{

						$tahun_anggaran = $_POST['tahun_anggaran'];
						$sql = $wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_tujuan
							WHERE tahun_anggaran=%d AND 
									id_unik=%s
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND
									active=1
							ORDER BY urut_tujuan
							", $tahun_anggaran, $_POST['id_unik']);
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

	public function submit_indikator_tujuan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_tujuan_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_rpjmd_tujuan_lokal
							WHERE indikator_teks='".$data['indikator_teks']."'
										AND id_unik='".$data['id_unik']."'
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row("SELECT * FROM data_renstra_tujuan_lokal WHERE id_unik='" . $data['id_unik'] . "' AND is_locked=0 AND status=1 AND active=1 AND id_unik_indikator IS NULL");

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$status = $wpdb->insert('data_renstra_tujuan_lokal', [
						'id_bidang_urusan' => $dataTujuan->id_bidang_urusan,
						'id_unik' => $dataTujuan->id_unik, // kode_tujuan
						'id_unik_indikator' => $this->generateRandomString(),
						'id_unit' => $dataTujuan->id_unit,
						'indikator_teks' => $data['indikator_teks'],
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'kode_bidang_urusan' => $dataTujuan->kode_bidang_urusan,
						'kode_sasaran_rpjm' => $dataTujuan->kode_sasaran_rpjm,
						'kode_skpd' => $dataTujuan->kode_skpd,
						'nama_bidang_urusan' => $dataTujuan->nama_bidang_urusan,
						'nama_skpd' => $dataTujuan->nama_skpd,
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
						'urut_tujuan' => $dataTujuan->urut_tujuan,
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

	function verify_indikator_tujuan_renstra(array $data){
		if(empty($data['id_unik'])){
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

	function edit_indikator_tujuan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$indikator = $wpdb->get_row("SELECT * FROM data_renstra_tujuan_lokal WHERE id=".$_POST['id']." AND id_unik IS NOT NULL AND id_unik_indikator IS NOT NULL AND is_locked_indikator=0 AND status=1 AND active=1");

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

	function update_indikator_tujuan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_tujuan_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_rpjmd_tujuan_lokal
							WHERE indikator_teks='".$data['indikator_teks']."'
										AND id_unik='".$data['id_unik']."'
										AND id!=".$data['id']."
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row("SELECT * FROM data_renstra_tujuan_lokal WHERE id_unik='" . $data['id_unik'] . "' AND is_locked=0 AND status=1 AND active=1 AND id_unik_indikator IS NULL");

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$status = $wpdb->update('data_renstra_tujuan_lokal', [
						'id_bidang_urusan' => $dataTujuan->id_bidang_urusan,
						'id_unit' => $dataTujuan->id_unit,
						'indikator_teks' => $data['indikator_teks'],
						'kode_bidang_urusan' => $dataTujuan->kode_bidang_urusan,
						'kode_sasaran_rpjm' => $dataTujuan->kode_sasaran_rpjm,
						'kode_skpd' => $dataTujuan->kode_skpd,
						'nama_bidang_urusan' => $dataTujuan->nama_bidang_urusan,
						'nama_skpd' => $dataTujuan->nama_skpd,
						'satuan' => $data['satuan'],
						'target_1' => $data['target_1'],
						'target_2' => $data['target_2'],
						'target_3' => $data['target_3'],
						'target_4' => $data['target_4'],
						'target_5' => $data['target_5'],
						'target_awal' => $data['target_awal'],
						'target_akhir' => $data['target_akhir'],
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'update_at' => date('Y-m-d H:i:s')
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

	function delete_indikator_tujuan_renstra(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$wpdb->get_results("DELETE FROM data_renstra_tujuan_lokal WHERE id=" . $_POST['id'] . " AND id_unik_indikator IS NOT NULL AND active=1 AND status=1");

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


}