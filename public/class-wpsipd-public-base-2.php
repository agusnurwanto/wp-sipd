<?php

require_once WPSIPD_PLUGIN_PATH."/public/class-wpsipd-public-base-3.php";

class Wpsipd_Public_Base_2 extends Wpsipd_Public_Base_3
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

									$delete_lokal_history = $this->delete_data_lokal_history('data_rpjpd_visi');

									$columns_1 = array('visi_teks','update_at');
		
									$sql_backup_data_rpjpd_visi =  "INSERT INTO data_rpjpd_visi_history (".implode(', ', $columns_1).",id_jadwal,id_asli)
												SELECT ".implode(', ', $columns_1).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
												FROM data_rpjpd_visi";

									$queryRecords1 = $wpdb->query($sql_backup_data_rpjpd_visi);

									$delete_lokal_history = $this->delete_data_lokal_history('data_rpjpd_misi');

									$columns_2 = array('id_visi','misi_teks','urut_misi','update_at');
		
									$sql_backup_data_rpjpd_misi =  "INSERT INTO data_rpjpd_misi_history (".implode(', ', $columns_2).",id_jadwal,id_asli)
												SELECT ".implode(', ', $columns_2).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
												FROM data_rpjpd_misi";

									$queryRecords2 = $wpdb->query($sql_backup_data_rpjpd_misi);

									$delete_lokal_history = $this->delete_data_lokal_history('data_rpjpd_sasaran');

									$columns_3 = array('id_misi','saspok_teks','urut_saspok','update_at');
		
									$sql_backup_data_rpjpd_sasaran =  "INSERT INTO data_rpjpd_sasaran_history (".implode(', ', $columns_3).",id_jadwal,id_asli)
												SELECT ".implode(', ', $columns_3).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
												FROM data_rpjpd_sasaran";

									$queryRecords3 = $wpdb->query($sql_backup_data_rpjpd_sasaran);

									$delete_lokal_history = $this->delete_data_lokal_history('data_rpjpd_kebijakan');

									$columns_4 = array('id_saspok','kebijakan_teks','urut_kebijakan','update_at');
		
									$sql_backup_data_rpjpd_kebijakan =  "INSERT INTO data_rpjpd_kebijakan_history (".implode(', ', $columns_4).",id_jadwal,id_asli)
												SELECT ".implode(', ', $columns_4).", ".$data_this_id[0]['id_jadwal_lokal'].", id as id_asli
												FROM data_rpjpd_kebijakan";

									$queryRecords4 = $wpdb->query($sql_backup_data_rpjpd_kebijakan);

									$delete_lokal_history = $this->delete_data_lokal_history('data_rpjpd_isu');

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

	public function monitor_rkpd_renja($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}

		$input = shortcode_atts( array(
			'id_skpd' => false,
			'tahun_anggaran' => '2022',
		), $atts );

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wpsipd-public-monitor-rkpd-renja.php';
	}

	public function get_sub_unit_by_id(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'	=> array()
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$table_content = '<option value="">Pilih Satuan</option>';
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
							where id_skpd=%d
								AND tahun_anggaran=%d
								AND active=1
							order by id_skpd ASC", $ret['data'][0]['id_skpd'], $_POST['tahun_anggaran']),
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
			    	foreach ($ret['data'] as $key => $value) {
			    		$table_content .= '<option value="'.$value['id_skpd'].'">'.$value['nama_skpd'].'</option>';
			    	}
					$ret['table_content'] = $table_content;
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

	public function run_sql_data_master(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'		=> array(),
			'status_insert' => array(),
			'cek_query' => array()
		);
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if(!empty($_POST['nama_tabel'] && !empty($_POST['tahun_anggaran']))){
					$nama_tabel = $_POST['nama_tabel'];
					if($nama_tabel === 'data_rumus_indikator'){
						$ret['data'] = get_option('data_master_rumus_indikator');
						if(!empty($ret['data'])){
							foreach($ret['data'] as $val_data){
								$data_rumus_indikator = $wpdb->get_results($wpdb->prepare('
											SELECT *
											FROM data_rumus_indikator
											WHERE id=%d
											AND rumus=%s
											AND tahun_anggaran=%d',
											$val_data['id'],$val_data['rumus'],$_POST['tahun_anggaran']), ARRAY_A);
								if(empty($data_rumus_indikator)){
									$ret['status_insert'] = $wpdb->insert($nama_tabel, array('id'=>$val_data['id'],'rumus'=>$val_data['rumus'],'keterangan'=>$val_data['keterangan'],'user'=>$val_data['user'],'active'=>$val_data['active'],'update_at'=>current_time('mysql'),'tahun_anggaran'=>$_POST['tahun_anggaran']));
								}
							}
						}
					}else if($nama_tabel === 'data_label_komponen'){
						$ret['data'] = get_option('data_master_label_komponen');
						if(!empty($ret['data'])){
							foreach($ret['data'] as $val_data){
								$data_rumus_indikator = $wpdb->get_results($wpdb->prepare('
											SELECT *
											FROM data_label_komponen
											WHERE nama=%s
											AND tahun_anggaran=%d',
											$val_data['nama'],$_POST['tahun_anggaran']), ARRAY_A);
								if(empty($data_rumus_indikator)){
									$ret['status_insert'] = $wpdb->insert($nama_tabel, array('nama'=>$val_data['nama'],'keterangan'=>$val_data['keterangan'],'id_skpd'=>$val_data['id_skpd'],'user'=>$val_data['user'],'active'=>$val_data['active'],'update_at'=>current_time('mysql'),'tahun_anggaran'=>$_POST['tahun_anggaran']));
								}
							}
						}
					}else if($nama_tabel === 'data_tipe_perencanaan'){
						$ret['data'] = get_option('data_master_tipe_perencanaan');
						if(!empty($ret['data'])){
							foreach($ret['data'] as $val_data){
								$data_tipe_perencanaan = $wpdb->get_results($wpdb->prepare('
															SELECT *
															FROM data_tipe_perencanaan
															WHERE id=%d',
															$val_data['id']), ARRAY_A);
								if(empty($data_tipe_perencanaan)){
									$ret['status_insert'] = $wpdb->insert($nama_tabel, array('nama_tipe'=>$val_data['nama_tipe'],'keterangan_tipe'=>$val_data['keterangan_tipe'],'lama_pelaksanaan'=>$val_data['lama_pelaksanaan']));
								}else{
									$ret['status_insert'] = $wpdb->update($nama_tabel, array('nama_tipe'=>$val_data['nama_tipe'],'keterangan_tipe'=>$val_data['keterangan_tipe'],'lama_pelaksanaan'=>$val_data['lama_pelaksanaan']), array('id'=>$val_data['id']));
								}
							}
						}
					}
				}else{
					$ret['status'] = 'error';
					$ret['message'] = 'Data ada  yang kosong!';
				}
			}else{
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}else{
			$ret['status']	= 'error';
			$ret['message']	= 'Format Salah!';
		}
		die(json_encode($ret));
	}

	public function get_data_program_renstra(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'action'	=> $_POST['action'],
			'data'		=> array(),
		);
		if(!empty($_POST)){
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				$data_sasaran_rpd_history = $wpdb->get_results($wpdb->prepare(
					'SELECT DISTINCT id_unik
					FROM data_rpd_sasaran_lokal_history
					WHERE status=%d',
					1)
					,ARRAY_A);

				$rpd_history = array();
				foreach($data_sasaran_rpd_history as $v_rpd_history){
					array_push($rpd_history, $v_rpd_history['id_unik']);
				}
				$rpd_history = implode(",",$rpd_history);

				//mencari data program renstra yang terhubung ke rpd
				$data_renstra_program = $wpdb->get_results($wpdb->prepare(
					'SELECT
						p.*,
						t.kode_sasaran_rpjm
					FROM data_renstra_program_lokal p
					INNER JOIN data_renstra_tujuan_lokal t
					ON p.kode_tujuan = t.id_unik
					WHERE 
						p.program_lock=%d AND
						p.status=%d AND
						p.active=%d AND
						t.kode_sasaran_rpjm IN (%s)',
					0,1,1,$rpd_history)
					,ARRAY_A);

				$wpdb->query( $wpdb->prepare(
					'UPDATE data_rpd_program_lokal
						SET active = %d',
						0)
				);

				//update dan insert data program dan indikator ke rpd
				foreach($data_renstra_program as $v_renstra){
					$nama_program = explode(" ", $v_renstra['nama_program']);
					array_shift($nama_program);
					$nama_program = implode(" ", $nama_program);

					$data_kirim = array(
							'kode_sasaran' 	=> $v_renstra['kode_sasaran_rpjm'],
							'nama_program' 	=> $nama_program,
							'id_program'	=> $v_renstra['id_program'],
							'update_at' 	=> current_time('mysql'),
							'active'		=> 1
						);

					//simpan program
					if(empty($v_renstra['indikator'])){
						$data_rpd_same_program = $wpdb->get_results($wpdb->prepare(
							'SELECT *
							FROM data_rpd_program_lokal 
							WHERE 
								nama_program=%s AND 
								kode_sasaran=%s AND
								indikator IS NULL',
							$nama_program,$v_renstra['kode_sasaran_rpjm'])
							,ARRAY_A);

						if(!empty($data_rpd_same_program)){
							$wpdb->update('data_rpd_program_lokal', $data_kirim, array('id_unik' => $data_rpd_same_program[0]['id_unik'], 'indikator' => NULL));
						}else if(empty($data_rpd_same_program) && empty($v_renstra['indikator'])){
							$data_kirim['id_unik'] = time().'-'.$this->generateRandomString(5);
							$wpdb->insert('data_rpd_program_lokal', $data_kirim);
						}
					}else{
						//simpan indikator
						$data_rpd_same_indikator = $wpdb->get_results($wpdb->prepare(
							'SELECT *
							FROM data_rpd_program_lokal 
							WHERE 
								nama_program=%s AND 
								kode_sasaran=%s AND
								indikator=%s',
							$nama_program,$v_renstra['kode_sasaran_rpjm'],$v_renstra['indikator'])
							,ARRAY_A);

						$data_kirim['id_unit'] = $v_renstra['id_unit'];
						$data_kirim['indikator'] = $v_renstra['indikator'];
						$data_kirim['kode_skpd'] = $v_renstra['kode_skpd'];
						$data_kirim['nama_skpd'] = $v_renstra['nama_skpd'];
						$data_kirim['target_awal'] = $v_renstra['target_awal'].' '.$v_renstra['satuan'];
						$data_kirim['target_1'] = $v_renstra['target_1'].' '.$v_renstra['satuan'];
						$data_kirim['target_2'] = $v_renstra['target_2'].' '.$v_renstra['satuan'];
						$data_kirim['target_3'] = $v_renstra['target_3'].' '.$v_renstra['satuan'];
						$data_kirim['target_4'] = $v_renstra['target_4'].' '.$v_renstra['satuan'];
						$data_kirim['target_5'] = $v_renstra['target_5'].' '.$v_renstra['satuan'];
						$data_kirim['target_akhir'] = $v_renstra['target_akhir'].' '.$v_renstra['satuan'];

						if(!empty($data_rpd_same_indikator)){
							$data_kirim['id_unik'] = $data_rpd_same_indikator[0]['id_unik'];
							$wpdb->update('data_rpd_program_lokal', $data_kirim, array('id_unik_indikator' => $data_rpd_same_indikator[0]['id_unik_indikator']));
						}else{
							//untuk mendapatkan id unik program
							$data_program = $wpdb->get_results($wpdb->prepare(
								'SELECT id_unik
								FROM data_rpd_program_lokal 
								WHERE 
									nama_program=%s AND 
									kode_sasaran=%s AND
									indikator IS NULL',
								$nama_program,$v_renstra['kode_sasaran_rpjm'])
								,ARRAY_A);

							if(!empty($data_program)){
								$data_kirim['id_unik'] = $data_program[0]['id_unik'];
								$data_kirim['id_unik_indikator'] = time().'-'.$this->generateRandomString(5); //id_unik indikator
								$wpdb->insert('data_rpd_program_lokal', $data_kirim);
							}
						}
					}
				}
				$ret['data'] = $data_renstra_program;				
			}else{
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		}else{
			$ret['status']	= 'error';
			$ret['message']	= 'Format Salah!';
		}
		die(json_encode($ret));
	}

	public function view_rekap_rpd(){
		global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil generate laporan renstra!'
        );

		if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
				if(!empty($_POST['relasi_perencanaan']) && !empty($_POST['tahun_anggaran']) && !empty($_POST['awal_rpd']) && !empty($_POST['akhir_rpd']) && !empty($_POST['lama_pelaksanaan'])){
					$id_jadwal_rpjpd 	= $_POST['relasi_perencanaan'];
					$tahun_anggaran 	= $_POST['tahun_anggaran'];
					$awal_rpd			= $_POST['awal_rpd'];
					$akhir_rpd 			= $_POST['akhir_rpd'];
					$lama_pelaksanaan 	= $_POST['lama_pelaksanaan'];
				
					// $cek_jadwal = $this->validasi_jadwal_perencanaan('rpd');
					// $jadwal_lokal = $cek_jadwal['data'];
					// $lama_pelaksanaan = 4;
					// $tahun_anggaran = '2022';
					// $namaJadwal = '-';
					// $mulaiJadwal = '-';
					// $selesaiJadwal = '-';
					// if(!empty($jadwal_lokal)){
						// 	$tahun_anggaran = $jadwal_lokal[0]['tahun_anggaran'];
						// 	$namaJadwal = $jadwal_lokal[0]['nama'];
						// 	$mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
						// 	$selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];
						// 	$id_jadwal_rpjpd = $jadwal_lokal[0]['relasi_perencanaan'];
						// 	$lama_pelaksanaan = $jadwal_lokal[0]['lama_pelaksanaan'];
						// }
					
					$awal_rpd = $tahun_anggaran;
					$akhir_rpd = $awal_rpd+$lama_pelaksanaan-1;
					$nama_pemda = get_option('_crb_daerah');

					$bulan = date('m');
					$body_monev = '';

					$data_all = array(
						'data' => array()
					);


					$tujuan_ids = array();
					$sasaran_ids = array();
					$program_ids = array();
					$skpd_filter = array();

					$sql = "
						select 
							t.*,
							i.isu_teks 
						from data_rpd_tujuan_lokal t
						left join data_rpjpd_isu i on t.id_isu = i.id
						where t.active=1
						order by t.no_urut asc
					";
					if(!empty($id_jadwal_rpjpd)){
						$sql = "
							select 
								t.*,
								i.isu_teks 
							from data_rpd_tujuan_lokal t
							left join data_rpjpd_isu_history i on t.id_isu = i.id_asli
							where t.active=1
							order by t.no_urut asc
						";
					}
					$tujuan_all = $wpdb->get_results($sql, ARRAY_A);
					foreach ($tujuan_all as $tujuan) {
						if(empty($data_all['data'][$tujuan['id_unik']])){
							$data_all['data'][$tujuan['id_unik']] = array(
								'nama' => $tujuan['tujuan_teks'],
								'detail' => array(),
								'data' => array()
							);
							$tujuan_ids[$tujuan['id_unik']] = "'".$tujuan['id_unik']."'";
							$sql = $wpdb->prepare("
								select 
									* 
								from data_rpd_sasaran_lokal
								where kode_tujuan=%s
									and active=1
									order by sasaran_no_urut asc
							", $tujuan['id_unik']);
							$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
							foreach ($sasaran_all as $sasaran) {
								if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
									$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
										'nama' => $sasaran['sasaran_teks'],
										'detail' => array(),
										'data' => array()
									);
									$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
									$sql = $wpdb->prepare("
										select 
											* 
										from data_rpd_program_lokal
										where kode_sasaran=%s
											and active=1
									", $sasaran['id_unik']);
									$program_all = $wpdb->get_results($sql, ARRAY_A);
									foreach ($program_all as $program) {
										$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
										if(empty($program['kode_skpd']) && empty($program['nama_skpd'])){
											$program['kode_skpd'] = '';
											$program['nama_skpd'] = 'Semua Perangkat Daerah';
										}
										$skpd_filter[$program['kode_skpd']] = $program['nama_skpd'];
										if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
											$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
												'nama' => $program['nama_program'],
												'kode_skpd' => $program['kode_skpd'],
												'nama_skpd' => $program['nama_skpd'],
												'detail' => array(),
												'data' => array()
											);
										}
										$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['detail'][] = $program;
										if(
											!empty($program['id_unik_indikator']) 
											&& empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])
										){
											$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
												'nama' => $program['indikator'],
												'data' => $program
											);
										}
									}
								}
								$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
							}
						}
						$data_all['data'][$tujuan['id_unik']]['detail'][] = $tujuan;
					}

					// buat array data kosong
					if(empty($data_all['data']['tujuan_kosong'])){
						$data_all['data']['tujuan_kosong'] = array(
							'nama' => '<span style="color: red">kosong</span>',
							'detail' => array(
								array(
									'id_unik' => 'kosong',
									'isu_teks' => ''
								)
							),
							'data' => array()
						);
					}
					if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong'])){
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong'] = array(
							'nama' => '<span style="color: red">kosong</span>',
							'detail' => array(
								array(
									'id_unik' => 'kosong',
									'isu_teks' => ''
								)
							),
							'data' => array()
						);
					}

					// select tujuan yang belum terselect
					if(!empty($tujuan_ids)){
						$sql = "
							select 
								t.*,
								i.isu_teks 
							from data_rpd_tujuan_lokal t
							left join data_rpjpd_isu i on t.id_isu = i.id
							where t.id_unik not in (".implode(',', $tujuan_ids).")
								and t.active=1
						";
						if(!empty($id_jadwal_rpjpd)){
							$sql = "
								select 
									t.*,
									i.isu_teks 
								from data_rpd_tujuan_lokal t
								left join data_rpjpd_isu_history i on t.id_isu = i.id_asli
								where t.id_unik not in (".implode(',', $tujuan_ids).")
									and t.active=1
							";
						}
					}else{
						$sql = "
							select 
								t.*,
								i.isu_teks 
							from data_rpd_tujuan_lokal t
							left join data_rpjpd_isu i on t.id_isu = i.id
							where t.active=1
						";
						if(!empty($id_jadwal_rpjpd)){
							$sql = "
								select 
									t.*,
									i.isu_teks 
								from data_rpd_tujuan_lokal t
								left join data_rpjpd_isu_history i on t.id_isu = i.id_asli
								where t.active=1
							";
						}
					}
					$tujuan_all_kosong = $wpdb->get_results($sql, ARRAY_A);
					foreach ($tujuan_all_kosong as $tujuan) {
						if(empty($data_all['data'][$tujuan['id_unik']])){
							$data_all['data'][$tujuan['id_unik']] = array(
								'nama' => $tujuan['tujuan_teks'],
								'detail' => array(),
								'data' => array()
							);
						}
						$data_all['data'][$tujuan['id_unik']]['detail'][] = $tujuan;
						$sql = $wpdb->prepare("
							select 
								* 
							from data_rpd_sasaran_lokal
							where kode_tujuan=%s
								and active=1
						", $tujuan['id_unik']);
						$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
						foreach ($sasaran_all as $sasaran) {
							$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
							if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
								$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
									'nama' => $sasaran['sasaran_teks'],
									'detail' => array(),
									'data' => array()
								);
							}
							$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
							$sql = $wpdb->prepare("
								select 
									* 
								from data_rpd_program_lokal
								where kode_sasaran=%s
									and active=1
							", $sasaran['id_unik']);
							$program_all = $wpdb->get_results($sql, ARRAY_A);
							foreach ($program_all as $program) {
								$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
								if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
									$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
										'nama' => $program['nama_program'],
										'kode_skpd' => $program['kode_skpd'],
										'nama_skpd' => $program['nama_skpd'],
										'detail' => array(),
										'data' => array()
									);
								}
								$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['detail'][] = $program;
								if(
									!empty($program['id_unik_indikator']) 
									&& empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])
								){
									$data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
										'nama' => $program['indikator'],
										'data' => $program
									);
								}
							}
						}
					}

					// select sasaran yang belum terselect
					if(!empty($sasaran_ids)){
						$sql = "
							select 
								* 
							from data_rpd_sasaran_lokal
							where id_unik not in (".implode(',', $sasaran_ids).")
								and active=1
						";
					}else{
						$sql = "
							select 
								* 
							from data_rpd_sasaran_lokal
							where active=1
						";
					}
					$sasaran_all_kosong = $wpdb->get_results($sql, ARRAY_A);
					foreach ($sasaran_all_kosong as $sasaran) {
						if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']])){
							$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']] = array(
								'nama' => $sasaran['sasaran_teks'],
								'detail' => array(),
								'data' => array()
							);
						}
						$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
						$sql = $wpdb->prepare("
							select 
								* 
							from data_rpd_program_lokal
							where kode_sasaran=%s
								and active=1
						", $sasaran['id_unik']);
						$program_all = $wpdb->get_results($sql, ARRAY_A);
						foreach ($program_all as $program) {
							$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
							if(empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
								$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
									'nama' => $program['nama_program'],
									'kode_skpd' => $program['kode_skpd'],
									'nama_skpd' => $program['nama_skpd'],
									'detail' => array(),
									'data' => array()
								);
							}
							$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['detail'][] = $program;
							if(
								!empty($program['id_unik_indikator']) 
								&& empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])
							){
								$data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
									'nama' => $program['indikator'],
									'data' => $program
								);
							}
						}
					}

					// select program yang belum terselect
					if(!empty($program_ids)){
						$sql = "
							select 
								* 
							from data_rpd_program_lokal
							where id_unik not in (".implode(',', $program_ids).")
								and active=1
						";
					}else{
						$sql = "
							select 
								* 
							from data_rpd_program_lokal
							where active=1
						";
					}
					$program_all = $wpdb->get_results($sql, ARRAY_A);
					foreach ($program_all as $program) {
						if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']])){
							$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']] = array(
								'nama' => $program['nama_program'],
								'kode_skpd' => $program['kode_skpd'],
								'nama_skpd' => $program['nama_skpd'],
								'detail' => array(),
								'data' => array()
							);
						}
						$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['detail'][] = $program;
						if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
							$data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
								'nama' => $program['indikator'],
								'data' => $program
							);
						}
					}

					// hapus array jika data dengan key kosong tidak ada datanya
					if(empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'])){
						unset($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']);
					}
					if(empty($data_all['data']['tujuan_kosong']['data'])){
						unset($data_all['data']['tujuan_kosong']);
					}

					// // print_r($data_all);

					$body = '';
					$no_tujuan = 0;
					foreach ($data_all['data'] as $tujuan) {
						$no_tujuan++;
						$indikator_tujuan = '';
						$target_awal = '';
						$target_1 = '';
						$target_2 = '';
						$target_3 = '';
						$target_4 = '';
						$target_5 = '';
						$target_akhir = '';
						$satuan = '';
						$indikator_catatan_tujuan = '';
						foreach($tujuan['detail'] as $k => $v){
							if(!empty($v['indikator_teks'])){
								$indikator_tujuan .= '<div class="indikator_program">'.$v['indikator_teks'].$this->button_edit_monev($v['id_unik'].'|'.$v['id_unik_indikator']).'</div>';
								$target_awal .= '<div class="indikator_program">'.$v['target_awal'].'</div>';
								$target_1 .= '<div class="indikator_program">'.$v['target_1'].'</div>';
								$target_2 .= '<div class="indikator_program">'.$v['target_2'].'</div>';
								$target_3 .= '<div class="indikator_program">'.$v['target_3'].'</div>';
								$target_4 .= '<div class="indikator_program">'.$v['target_4'].'</div>';
								$target_5 .= '<div class="indikator_program">'.$v['target_5'].'</div>';
								$target_akhir .= '<div class="indikator_program">'.$v['target_akhir'].'</div>';
								$satuan .= '<div class="indikator_program">'.$v['satuan'].'</div>';
								$indikator_catatan_tujuan .= '<div class="indikator_program">'.$v['indikator_catatan_teks'].'</div>';
							}
						}
						$target_html = "";
						for($i=1; $i<=$lama_pelaksanaan; $i++){
							$target_html .= '<td class="atas kanan bawah text_tengah">'.${'target_'.$i}.'</td>';
						}
						$warning = "";
						if(empty($tujuan['detail'][0]['id_isu'])){
							$warning = "style='background: #80000014;'";
						}
						$no_urut = '';
						if(!empty($tujuan['detail'][0]['no_urut'])){
							$no_urut = $tujuan['detail'][0]['no_urut'];
						}
						$catatan_teks_tujuan = '';
						if(!empty($tujuan['detail'][0]['catatan_teks_tujuan'])){
							$catatan_teks_tujuan = $tujuan['detail'][0]['catatan_teks_tujuan'];
						}
						$body .= '
							<tr class="tr-tujuan" '.$warning.'>
								<td class="kiri atas kanan bawah">'.$no_tujuan.'</td>
								<td class="atas kanan bawah">'.$tujuan['detail'][0]['isu_teks'].'</td>
								<td class="atas kanan bawah">'.$this->parsing_nama_kode($tujuan['nama']).$this->button_edit_monev($tujuan['detail'][0]['id_unik']).'</td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah">'.$indikator_tujuan.'</td>
								<td class="atas kanan bawah text_tengah">'.$target_awal.'</td>
								'.$target_html.'
								<td class="atas kanan bawah text_tengah">'.$target_akhir.'</td>
								<td class="atas kanan bawah">'.$satuan.'</td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah">'.$no_urut.'</td>
								<td class="atas kanan bawah">'.$catatan_teks_tujuan.'</td>
								<td class="atas kanan bawah">'.$indikator_catatan_tujuan.'</td>
							</tr>
						';
						$no_sasaran = 0;
						foreach ($tujuan['data'] as $sasaran) {
							$no_sasaran++;
							$indikator_sasaran = '';
							$target_awal = '';
							$target_1 = '';
							$target_2 = '';
							$target_3 = '';
							$target_4 = '';
							$target_5 = '';
							$target_akhir = '';
							$satuan = '';
							$indikator_catatan_sasaran = '';
							foreach($sasaran['detail'] as $k => $v){
								if(!empty($v['indikator_teks'])){
									$indikator_sasaran .= '<div class="indikator_program">'.$v['indikator_teks'].$this->button_edit_monev($tujuan['detail'][0]['id_unik'].'||'.$v['id_unik'].'|'.$v['id_unik_indikator']).'</div>';
									$target_awal .= '<div class="indikator_program">'.$v['target_awal'].'</div>';
									$target_1 .= '<div class="indikator_program">'.$v['target_1'].'</div>';
									$target_2 .= '<div class="indikator_program">'.$v['target_2'].'</div>';
									$target_3 .= '<div class="indikator_program">'.$v['target_3'].'</div>';
									$target_4 .= '<div class="indikator_program">'.$v['target_4'].'</div>';
									$target_5 .= '<div class="indikator_program">'.$v['target_5'].'</div>';
									$target_akhir .= '<div class="indikator_program">'.$v['target_akhir'].'</div>';
									$satuan .= '<div class="indikator_program">'.$v['satuan'].'</div>';
									$indikator_catatan_sasaran .= '<div class="indikator_program">'.$v['indikator_catatan_teks'].'</div>';
								}
							}
							$target_html = "";
							for($i=1; $i<=$lama_pelaksanaan; $i++){
								$target_html .= '<td class="atas kanan bawah text_tengah">'.${'target_'.$i}.'</td>';
							}
							$sasaran_no_urut = '';
							if(!empty($sasaran['detail'][0]['sasaran_no_urut'])){
								$sasaran_no_urut = $sasaran['detail'][0]['sasaran_no_urut'];
							}
							$sasaran_catatan = '';
							if(!empty($sasaran['detail'][0]['sasaran_catatan'])){
								$sasaran_catatan = $sasaran['detail'][0]['sasaran_catatan'];
							}
							$body .= '
								<tr class="tr-sasaran" '.$warning.'>
									<td class="kiri atas kanan bawah">'.$no_tujuan.'.'.$no_sasaran.'</td>
									<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['detail'][0]['isu_teks'].'</span></td>
									<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['nama'].'</span></td>
									<td class="atas kanan bawah">'.$this->parsing_nama_kode($sasaran['nama']).$this->button_edit_monev($tujuan['detail'][0]['id_unik'].'||'.$sasaran['detail'][0]['id_unik']).'</td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah">'.$indikator_sasaran.'</td>
									<td class="atas kanan bawah text_tengah">'.$target_awal.'</td>
									'.$target_html.'
									<td class="atas kanan bawah text_tengah">'.$target_akhir.'</td>
									<td class="atas kanan bawah">'.$satuan.'</td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah">'.$sasaran_no_urut.'</td>
									<td class="atas kanan bawah">'.$sasaran_catatan.'</td>
									<td class="atas kanan bawah">'.$indikator_catatan_sasaran.'</td>
								</tr>
							';
							$no_program = 0;
							foreach ($sasaran['data'] as $program) {
								$no_program++;
								$text_indikator = array();
								$target_awal = array();
								$target_1 = array();
								$target_2 = array();
								$target_3 = array();
								$target_4 = array();
								$target_5 = array();
								$target_akhir = array();
								$satuan = array();
								foreach ($program['data'] as $indikator_program) {
									$text_indikator[] = '<div class="indikator_program">'.$indikator_program['nama'].$this->button_edit_monev($tujuan['detail'][0]['id_unik'].'||'.$sasaran['detail'][0]['id_unik'].'||'.$indikator_program['data']['id_unik'].'|'.$indikator_program['data']['id_unik_indikator']).'</div>';
									$target_awal[] = '<div class="indikator_program">'.$indikator_program['data']['target_awal'].'</div>';
									$target_1[] = '<div class="indikator_program">'.$indikator_program['data']['target_1'].'</div>'.number_format($indikator_program['data']['pagu_1'],0,",",".");
									$target_2[] = '<div class="indikator_program">'.$indikator_program['data']['target_2'].'</div>'.number_format($indikator_program['data']['pagu_2'],0,",",".");
									$target_3[] = '<div class="indikator_program">'.$indikator_program['data']['target_3'].'</div>'.number_format($indikator_program['data']['pagu_3'],0,",",".");
									$target_4[] = '<div class="indikator_program">'.$indikator_program['data']['target_4'].'</div>'.number_format($indikator_program['data']['pagu_4'],0,",",".");
									$target_5[] = '<div class="indikator_program">'.$indikator_program['data']['target_5'].'</div>'.number_format($indikator_program['data']['pagu_5'],0,",",".");
									$target_akhir[] = '<div class="indikator_program">'.$indikator_program['data']['target_akhir'].'</div>';
									$satuan[] = '<div class="indikator_program">'.$indikator_program['data']['satuan'].'</div>';
								}
								$text_indikator = implode('', $text_indikator);
								$target_awal = implode('', $target_awal);
								$target_1 = implode('', $target_1);
								$target_2 = implode('', $target_2);
								$target_3 = implode('', $target_3);
								$target_4 = implode('', $target_4);
								$target_5 = implode('', $target_5);
								$target_akhir = implode('', $target_akhir);
								$satuan = implode('', $satuan);
								$target_html = "";
								for($i=1; $i<=$lama_pelaksanaan; $i++){
									$target_html .= '<td class="atas kanan bawah text_tengah">'.${'target_'.$i}.'</td>';
								}
								$catatan_program = '';
								$catatan_indikator_program = '';
								$body .= '
									<tr class="tr-program" data-kode-skpd="'.$program['kode_skpd'].'" '.$warning.'>
										<td class="kiri atas kanan bawah">'.$no_tujuan.'.'.$no_sasaran.'.'.$no_program.'</td>
										<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['detail'][0]['isu_teks'].'</span></td>
										<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['nama'].'</span></td>
										<td class="atas kanan bawah"><span class="debug-sasaran">'.$sasaran['nama'].'</span></td>
										<td class="atas kanan bawah">'.$this->parsing_nama_kode($program['nama']).$this->button_edit_monev($tujuan['detail'][0]['id_unik'].'||'.$sasaran['detail'][0]['id_unik'].'||'.$program['detail'][0]['id_unik']).'</td>
										<td class="atas kanan bawah">'.$text_indikator.'</td>
										<td class="atas kanan bawah text_tengah">'.$target_awal.'</td>
										'.$target_html.'
										<td class="atas kanan bawah text_tengah">'.$target_akhir.'</td>
										<td class="atas kanan bawah text_tengah">'.$satuan.'</td>
										<td class="atas kanan bawah">'.$program['kode_skpd'].' '.$program['nama_skpd'].'</td>
										<td class="atas kanan bawah" colspan="2">'.$catatan_program.'</td>
										<td class="atas kanan bawah">'.$catatan_indikator_program.'</td>
									</tr>
								';
							}
						}
					}

					ksort($skpd_filter);
					$skpd_filter_html = '<option value="">Pilih SKPD</option>';
					foreach ($skpd_filter as $kode_skpd => $nama_skpd) {
						$skpd_filter_html .= '<option value="'.$kode_skpd.'">'.$kode_skpd.' '.$nama_skpd.'</option>';
					}

					$html = '';
					$html .='
						<style type="text/css">
							.debug-visi, .debug-misi, .debug-tujuan, .debug-sasaran, .debug-kode { 
								display: none; 
							}
							.indikator_program { 
								min-height: 40px; 
							}
							.aksi button {
								margin: 3px;
							}
						</style>';
					$html .='
					<div id="preview">
						<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitoring dan Evaluasi RPD (Rencana Pembangunan Daerah) <br>'.$nama_pemda .'<br>'. $awal_rpd.' - '.$akhir_rpd .'</h4>
						<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; height: 80vh;">
							<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
								<thead>
									<tr>
										<th style="width: 85px;" class="atas kiri kanan bawah text_tengah text_blok">No</th>
										<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Isu RPJPD</th>
										<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Tujuan</th>
										<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Sasaran</th>
										<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Program</th>
										<th style="width: 400px;" class="atas kanan bawah text_tengah text_blok">Indikator</th>
										<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Awal</th>';
										for($i=1; $i<=$lama_pelaksanaan; $i++){
											$html .='<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Tahun '.$i.'</th>';
										}
										$html .='
										<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Akhir</th>
										<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Satuan</th>
										<th style="width: 150px;" class="atas kanan bawah text_tengah text_blok">Keterangan</th>
										<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">No. Urut</th>
										<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Catatan</th>
										<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Indikator Catatan</th>
									</tr>
									<tr>
										<th class="atas kiri kanan bawah text_tengah text_blok">0</th>
										<th class="atas kanan bawah text_tengah text_blok">1</th>
										<th class="atas kanan bawah text_tengah text_blok">2</th>
										<th class="atas kanan bawah text_tengah text_blok">3</th>
										<th class="atas kanan bawah text_tengah text_blok">4</th>
										<th class="atas kanan bawah text_tengah text_blok">5</th>
										<th class="atas kanan bawah text_tengah text_blok">6</th>
										<th class="atas kanan bawah text_tengah text_blok">7</th>
										<th class="atas kanan bawah text_tengah text_blok">8</th>
										<th class="atas kanan bawah text_tengah text_blok">9</th>
										<th class="atas kanan bawah text_tengah text_blok">10</th>
										<th class="atas kanan bawah text_tengah text_blok">11</th>
										<th class="atas kanan bawah text_tengah text_blok">12</th>';
										for($i=1; $i<=$lama_pelaksanaan; $i++){
											$no = 12+$i;
											$html .='<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">'.$no.'</th>';
										}
										$html .='
									</tr>
								</thead>
								<tbody>
								'.$body.'
								</tbody>
							</table>
						</div>
					</div>';

					$ret['html'] = $html;
				}else{
					$ret = array(
						'status'	=> 'error',
						'message'	=> 'Data ada yang kosong, harap diisi semua!'
					);
				}
			}else{
            	$ret = array(
                    'status' => 'error',
                    'message'   => 'Api Key tidak sesuai!'
                );
            }
        }else{
        	 $ret = array(
                'status' => 'error',
                'message'   => 'Format tidak sesuai!'
            );
        }

		die(json_encode($ret));
	}

	function button_edit_monev($class=false){
		$ret = ' <span style="display: none;" data-id="'.$class.'" class="edit-monev"><i class="dashicons dashicons-edit"></i></span>';
		return $ret;
	}

	function get_target($target, $satuan){
		if(empty($satuan)){
			return $target;
		}else{
			$target = explode($satuan, $target);
			return $target[0];
		}
	}

	function parsing_nama_kode($nama_kode){
		$nama_kodes = explode('||', $nama_kode);
		$nama = $nama_kodes[0];
		unset($nama_kodes[0]);
		return $nama.'<span class="debug-kode">||'.implode('||', $nama_kodes).'</span>';
	}
}