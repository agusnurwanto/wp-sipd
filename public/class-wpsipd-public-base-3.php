<?php

class Wpsipd_Public_Base_3
{
	
	protected function role(){
		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);
		return $user_meta->roles;
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

	public function get_sasaran_parent_by_tipe($params = array()){

		global $wpdb;

		if($params['relasi_perencanaan']=='-'){
			return [];
		}

		$where = '';
		if(!empty($params['kode_sasaran_parent'])){
			$where .= " AND a.id_unik='".$params['kode_sasaran_parent']."'";
		}

		if($params['id_tipe_relasi'] == 2){
			$tableA = "data_rpjmd_sasaran_lokal_history";
			$tableB = "data_rpjmd_program_lokal_history";
		}else if($params['id_tipe_relasi'] == 3){
			$tableA = "data_rpd_sasaran_lokal_history";
			$tableB = "data_rpd_program_lokal_history";
		}

		return $wpdb->get_results("
				SELECT 
					a.id_unik, 
					a.sasaran_teks, 
					a.id_visi, 
					a.visi_teks, 
					a.id_misi, 
					a.misi_teks, 
					b.id_program 
				FROM ".$tableA." a 
					INNER JOIN ".$tableB." b 
						ON a.id_unik=b.kode_sasaran 
				WHERE 
					b.id_unit=".$params['id_unit']." AND 
					a.id_jadwal=".$params['relasi_perencanaan']." AND 
					a.status=1 AND 
					a.id_unik IS NOT NULL AND 
					a.id_unik_indikator IS NULL AND 
					b.id_unik IS NOT NULL AND 
					b.id_unik_indikator IS NOT NULL 
					$where;");

		throw new Exception("Relasi perencanaan tidak diketahui", 1);
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
						SELECT 
							* 
						FROM data_renstra_tujuan
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

	public function add_tujuan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					$sasaran_parent = $this->get_sasaran_parent_by_tipe($_POST);

					echo json_encode([
						'status' => true,
						'data' => $sasaran_parent
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

					$where_sasaran_rpjm = '';
					if(!empty($data['sasaran_parent'])){
						$raw_sasaran_parent = explode("|", $data['sasaran_parent']);
						$where_sasaran_rpjm = "AND kode_sasaran_rpjm='".$raw_sasaran_parent[0]."'";

						$dataBidangUrusan = $wpdb->get_row("
							SELECT DISTINCT 
								id_bidang_urusan, 
								kode_bidang_urusan, 
								nama_bidang_urusan 
							FROM data_prog_keg 
								WHERE 
									id_program=".$raw_sasaran_parent[1]);

						if(empty($dataBidangUrusan)){
							throw new Exception('Bidang urusan tidak ditemukan!');
						}

						$data['id_bidang_urusan'] = $dataBidangUrusan->id_bidang_urusan ?? null;
						$data['kode_bidang_urusan'] = $dataBidangUrusan->kode_bidang_urusan ?? null;
						$data['kode_sasaran_rpjm'] = $raw_sasaran_parent[0] ?? null;
						$data['nama_bidang_urusan'] = $dataBidangUrusan->nama_bidang_urusan ?? null;
					}

					if(empty($data['tujuan_teks'])){
						throw new Exception('Tujuan tidak boleh kosong!');
					}

					if(empty($data['urut_tujuan'])){
						throw new Exception('Urut tujuan tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_tujuan_lokal
							WHERE tujuan_teks='".trim($data['tujuan_teks'])."'
										$where_sasaran_rpjm
										AND id_unik IS NOT NULL
										AND id_unik_indikator IS NULL
										AND is_locked=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Tujuan : '.$data['tujuan_teks'].' sudah ada!');
					}

					$dataUnit = $wpdb->get_row("SELECT * FROM data_unit WHERE id_unit=".$data['id_unit']." AND tahun_anggaran=".get_option('_crb_tahun_anggaran_sipd')." AND active=1 AND is_skpd=1 order by id_skpd ASC;");

					if(empty($dataUnit)){
						throw new Exception('Unit kerja tidak ditemukan!');
					}

					$status = $wpdb->insert('data_renstra_tujuan_lokal', [
						'id_bidang_urusan' => $data['id_bidang_urusan'],
						'id_unik' => $this->generateRandomString(), // kode_tujuan
						'id_unit' => $dataUnit->id_unit,
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'kode_bidang_urusan' => $data['kode_bidang_urusan'],
						'kode_sasaran_rpjm' => $data['kode_sasaran_rpjm'],
						'kode_skpd' => $dataUnit->kode_skpd,
						'nama_bidang_urusan' => $data['nama_bidang_urusan'],
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

					$sasaran_parent = $this->get_sasaran_parent_by_tipe($_POST);

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

						$where_sasaran_rpjm = '';
						if(!empty($data['sasaran_parent'])){
							$raw_sasaran_parent = explode("|", $data['sasaran_parent']);
							$where_sasaran_rpjm = "AND kode_sasaran_rpjm='".$raw_sasaran_parent[0]."'";

							$dataBidangUrusan = $wpdb->get_row("
								SELECT DISTINCT 
									id_bidang_urusan, 
									kode_bidang_urusan, 
									nama_bidang_urusan 
								FROM data_prog_keg 
									WHERE 
										id_program=".$raw_sasaran_parent[1]);

							if(empty($dataBidangUrusan)){
								throw new Exception('Bidang urusan tidak ditemukan!');
							}

							$data['id_bidang_urusan'] = $dataBidangUrusan->id_bidang_urusan ?? null;
							$data['kode_bidang_urusan'] = $dataBidangUrusan->kode_bidang_urusan ?? null;
							$data['kode_sasaran_rpjm'] = $raw_sasaran_parent[0] ?? null;
							$data['nama_bidang_urusan'] = $dataBidangUrusan->nama_bidang_urusan ?? null;
						}

						if(empty($data['tujuan_teks'])){
							throw new Exception('Tujuan tidak boleh kosong!');
						}

						if(empty($data['urut_tujuan'])){
							throw new Exception('Urut tujuan tidak boleh kosong!');
						}

						$id_cek = $wpdb->get_var("
							SELECT id FROM data_renstra_tujuan_lokal
								WHERE tujuan_teks='".trim($data['tujuan_teks'])."'
											AND id!=".$data['id']." 
											$where_sasaran_rpjm
											AND id_unik IS NOT NULL
											AND id_unik_indikator IS NULL
											AND is_locked=0
											AND status=1
											AND active=1
									");
						
						if(!empty($id_cek)){
							throw new Exception('Tujuan : '.$data['tujuan_teks'].' sudah ada!');
						}

						$dataUnit = $wpdb->get_row("SELECT * FROM data_unit WHERE id_unit=".$data['id_unit']." AND tahun_anggaran=".get_option('_crb_tahun_anggaran_sipd')." AND active=1 AND is_skpd=1 order by id_skpd ASC;");

						if(empty($dataUnit)){
							throw new Exception('Unit kerja tidak ditemukan!');
						}

						$status = $wpdb->update('data_renstra_tujuan_lokal', [
							'id_bidang_urusan' => $data['id_bidang_urusan'],
							'id_unit' => $dataUnit->id_unit,
							'kode_bidang_urusan' => $data['kode_bidang_urusan'],
							'kode_sasaran_rpjm' => $data['kode_sasaran_rpjm'],
							'kode_skpd' => $dataUnit->kode_skpd,
							'nama_bidang_urusan' => $data['nama_bidang_urusan'],
							'nama_skpd' => $dataUnit->nama_skpd,
							'tujuan_teks' => $data['tujuan_teks'],
							'urut_tujuan' => $data['urut_tujuan'],
							'update_at' => date('Y-m-d H:i:s')
						], [
							'id_unik' => $data['id_unik'], // pake id_unik biar teks tujuan di row indikator tujuan ikut terupdate
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

					$inputs = [
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
						'status' => 1,
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'active' => 1
					];

					if(in_array('administrator', $this->role())){
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];

						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}else{
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];
					}

					$status = $wpdb->insert('data_renstra_tujuan_lokal', $inputs);

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

	public function edit_indikator_tujuan_renstra(){
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

	public function update_indikator_tujuan_renstra(){
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

					$inputs = [
						'id_bidang_urusan' => $dataTujuan->id_bidang_urusan,
						'id_unik' => $dataTujuan->id_unik, // kode_tujuan
						'id_unit' => $dataTujuan->id_unit,
						'indikator_teks' => $data['indikator_teks'],
						'kode_bidang_urusan' => $dataTujuan->kode_bidang_urusan,
						'kode_sasaran_rpjm' => $dataTujuan->kode_sasaran_rpjm,
						'kode_skpd' => $dataTujuan->kode_skpd,
						'nama_bidang_urusan' => $dataTujuan->nama_bidang_urusan,
						'nama_skpd' => $dataTujuan->nama_skpd,
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'update_at' => date('Y-m-d H:i:s')
					];

					if(in_array('administrator', $this->role())){
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];

						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}else{
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];
					}

					$status = $wpdb->update('data_renstra_tujuan_lokal', $inputs, ['id' => $data['id']]);

					if(!$status){
						throw new Exception('Terjadi kesalahan saat ubah data, harap hubungi admin!');
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

	public function delete_indikator_tujuan_renstra(){
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

	public function verify_indikator_tujuan_renstra(array $data){
		if(empty($data['id_unik'])){
			throw new Exception('Tujuan wajib dipilih!');
		}

		if(empty($data['indikator_teks'])){
			throw new Exception('Indikator tujuan tidak boleh kosong!');
		}

		if(empty($data['satuan_usulan'])){
			throw new Exception('Satuan usulan indikator tujuan tidak boleh kosong!');
		}

		if(empty($data['target_awal_usulan'])){
			throw new Exception('Target awal usulan Indikator tujuan tidak boleh kosong!');
	 	}

		for ($i=1; $i <= $data['lama_pelaksanaan'] ; $i++) { 
			if(empty($data['target_'.$i.'_usulan'])){
				throw new Exception('Target usulan Indikator tujuan tahun ke-'.$i.' tidak boleh kosong!');
			}
		}

		if(empty($data['target_akhir_usulan'])){
			throw new Exception('Target akhir usulan Indikator tujuan tidak boleh kosong!');
		}		
	}

	public function get_sasaran_renstra(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				if($_POST['type'] == 1){
					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal
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
								FROM data_renstra_sasaran
								WHERE tahun_anggaran=%d
									AND kode_tujuan=%s
									AND active=1
								ORDER BY urut_sasaran DESC
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

	public function submit_sasaran_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_sasaran_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_sasaran_lokal
							WHERE sasaran_teks ='".$data['sasaran_teks']."'
										AND kode_tujuan='".$data['kode_tujuan']."'
										AND is_locked=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Sasaran : '.$data['sasaran_teks'].' sudah ada!');
					}
					
					$dataTujuan = $wpdb->get_row("
						SELECT 
							id_unik AS kode_tujuan, 
							tujuan_teks, 
							is_locked AS tujuan_lock, 
							id_bidang_urusan, 
							id_unit, 
							kode_skpd, 
							nama_skpd, 
							kode_bidang_urusan, 
							nama_bidang_urusan, 
							urut_tujuan 
						FROM data_renstra_tujuan_lokal 
							WHERE 
								id_unik='".$data['kode_tujuan'] ."' AND 
								is_locked=0 AND status=1 AND 
								active=1 AND 
								id_unik IS NOT NULL AND 
							id_unik_indikator IS NULL");

					if(empty($dataTujuan)){
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					if($data['relasi_perencanaan']!='-'){
						$dataSasaranParent = $this->get_sasaran_parent_by_tipe([
							'id_unit' => $data['id_unit'],
							'relasi_perencanaan' => $data['relasi_perencanaan'],
							'id_tipe_relasi' => $data['id_tipe_relasi'],
							'kode_sasaran_parent' => $dataTujuan->kode_sasaran_rpjm,
						]);
						if(!empty($dataSasaranParent)){
							$data['id_visi'] = $dataSasaranParent[0]->id_visi;
							$data['id_misi'] = $dataSasaranParent[0]->id_misi;
						}
					}

					$status = $wpdb->insert('data_renstra_sasaran_lokal', [
						'id_bidang_urusan' => $dataTujuan->id_bidang_urusan,
						'id_misi' => $data['id_misi'],
						'id_unik' => $this->generateRandomString(), // kode_sasaran
						'id_unit' => $dataTujuan->id_unit,
						'id_visi' => $data['id_visi'],
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'kode_bidang_urusan' => $dataTujuan->kode_bidang_urusan,
						'kode_skpd' => $dataTujuan->kode_skpd,
						'kode_tujuan' => $dataTujuan->kode_tujuan,
						'nama_bidang_urusan' => $dataTujuan->nama_bidang_urusan,
						'nama_skpd' => $dataTujuan->nama_skpd,
						'sasaran_teks' => $data['sasaran_teks'],
						'status' => 1,
						'tujuan_lock' => $dataTujuan->tujuan_lock,
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_sasaran' => $data['urut_sasaran'],
						'urut_tujuan' => $dataTujuan->urut_tujuan,
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

	public function edit_sasaran_renstra(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				$sasaran = $wpdb->get_row("
					SELECT * FROM data_renstra_sasaran_lokal
						WHERE id=".$_POST['id_sasaran']."
							AND id_unik IS NOT NULL
							AND id_unik_indikator IS NULL
							AND is_locked=0
							AND active=1
							AND status=1
						");

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

	public function update_sasaran_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_sasaran_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_sasaran_lokal
							WHERE sasaran_teks='".$data['sasaran_teks']."'
										AND kode_tujuan='".$data['kode_tujuan']."'
										AND id_unik!='".$data['kode_sasaran']."'
										AND id_unik_indikator IS NULL
										AND is_locked=0
										AND active=1
										AND status=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Sasaran : '.$data['sasaran_teks'].' sudah ada!');
					}
					
					$dataTujuan = $wpdb->get_row("
						SELECT 
							id_unik AS kode_tujuan, 
							tujuan_teks, 
							is_locked AS tujuan_lock, 
							id_bidang_urusan, 
							id_unit, 
							kode_skpd, 
							nama_skpd, 
							kode_bidang_urusan, 
							nama_bidang_urusan, 
							urut_tujuan 
						FROM data_renstra_tujuan_lokal 
							WHERE 
								id_unik='".$data['kode_tujuan'] ."' AND 
								is_locked=0 AND 
								status=1 AND 
								active=1 AND 
								id_unik IS NOT NULL AND 
							id_unik_indikator IS NULL");

					if(empty($dataTujuan)){
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					if($data['relasi_perencanaan']!='-'){
						$dataSasaranParent = $this->get_sasaran_parent_by_tipe([
							'id_unit' => $data['id_unit'],
							'relasi_perencanaan' => $data['relasi_perencanaan'],
							'id_tipe_relasi' => $data['id_tipe_relasi'],
							'kode_sasaran_parent' => $dataTujuan->kode_sasaran_rpjm,
						]);
						if(!empty($dataSasaranParent)){
							$data['id_visi'] = $dataSasaranParent[0]->id_visi;
							$data['id_misi'] = $dataSasaranParent[0]->id_misi;
						}
					}

					$status = $wpdb->update('data_renstra_sasaran_lokal', [
						'id_bidang_urusan' => $dataTujuan->id_bidang_urusan,
						'id_misi' => $data['id_misi'],
						'id_unit' => $dataTujuan->id_unit,
						'id_visi' => $data['id_visi'],
						'kode_bidang_urusan' => $dataTujuan->kode_bidang_urusan,
						'kode_skpd' => $dataTujuan->kode_skpd,
						'kode_tujuan' => $dataTujuan->kode_tujuan,
						'nama_bidang_urusan' => $dataTujuan->nama_bidang_urusan,
						'nama_skpd' => $dataTujuan->nama_skpd,
						'sasaran_teks' => $data['sasaran_teks'],
						'tujuan_lock' => $dataTujuan->tujuan_lock,
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_sasaran' => $data['urut_sasaran'],
						'urut_tujuan' => $dataTujuan->urut_tujuan,
					], [
						'id_unik' => $data['kode_sasaran'] // pake id_unik biar teks sasaran di row indikator sasaran ikut terupdate
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

	public function delete_sasaran_renstra(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$id_cek = $wpdb->get_var("SELECT * FROM data_renstra_program_lokal WHERE kode_sasaran='" . $_POST['kode_sasaran'] . "' AND is_locked=0 AND status=1 AND active=1");

					if(!empty($id_cek)){
						throw new Exception("Sasaran sudah digunakan oleh program", 1);
					}

					$id_cek = $wpdb->get_var("SELECT * FROM data_renstra_sasaran_lokal WHERE id_unik='" . $_POST['kode_sasaran'] . "' AND id_unik_indikator IS NOT NULL AND is_locked=0 AND status=1 AND active=1");

					if(!empty($id_cek)){
						throw new Exception("Sasaran sudah digunakan oleh indikator sasaran", 1);
					}

					$wpdb->get_results("DELETE FROM data_renstra_sasaran_lokal WHERE id=".$_POST['id_sasaran']);

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

	private function verify_sasaran_renstra($data){
		if(empty($data['kode_tujuan'])){
			throw new Exception('Tujuan wajib dipilih!');
		}

		if(empty($data['sasaran_teks'])){
			throw new Exception('Sasaran tidak boleh kosong!');
		}

		if(empty($data['urut_sasaran'])){
			throw new Exception('Urut sasaran tidak boleh kosong!');
		}
	}

	public function get_indikator_sasaran_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					if($_POST['type'] == 1){
						$sql = $wpdb->prepare("
							SELECT * FROM data_renstra_sasaran_lokal 
								WHERE 
									id_unik=%d AND 
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
							FROM data_renstra_sasaran
							WHERE tahun_anggaran=%d AND 
									id_unik=%d
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND
									active=1
							ORDER BY urut_sasaran DESC
							", $tahun_anggaran, $_POST['id_unik']);
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

	public function submit_indikator_sasaran_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_sasaran_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_sasaran_lokal
							WHERE indikator_teks='".$data['indikator_teks']."'
										AND id_unik='".$data['id_unik']."'
										AND id_unik_indikator IS NOT NULL
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row("
						SELECT * FROM data_renstra_sasaran_lokal 
							WHERE 
								id_unik='".$data['id_unik']."' AND 
								id_unik_indikator IS NULL AND 
								is_locked=0 AND 
								status=1 AND 
								active=1");

					if (empty($dataSasaran)) {
						throw new Exception('Sasaran yang dipilih tidak ditemukan!');
					}

					$inputs = [
						'id_bidang_urusan' => $dataSasaran->id_bidang_urusan,
						'id_misi' => $dataSasaran->id_misi,
						'id_unik' => $dataSasaran->id_unik,
						'id_unik_indikator' => $this->generateRandomString(), 
						'id_unit' => $dataSasaran->id_unit,
						'id_visi' => $dataSasaran->id_visi,
						'indikator_teks' => $data['indikator_teks'],
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'kode_bidang_urusan' => $dataSasaran->kode_bidang_urusan,
						'kode_skpd' => $dataSasaran->kode_skpd,
						'kode_tujuan' => $dataSasaran->kode_tujuan,
						'nama_bidang_urusan' => $dataSasaran->nama_bidang_urusan,
						'nama_skpd' => $dataSasaran->nama_skpd,
						'sasaran_teks' => $dataSasaran->sasaran_teks,
						'status' => 1,
						'tujuan_lock' => $dataSasaran->tujuan_lock,
						'tujuan_teks' => $dataSasaran->tujuan_teks,
						'urut_sasaran' => $dataSasaran->urut_sasaran,
						'urut_tujuan' => $dataSasaran->urut_tujuan,
						'active' => 1
					];

					if(in_array('administrator', $this->role())){
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];

						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}else{
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];
					}

					$status = $wpdb->insert('data_renstra_sasaran_lokal', $inputs);

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

	public function edit_indikator_sasaran_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$indikator = $wpdb->get_row("
						SELECT * FROM data_renstra_sasaran_lokal 
							WHERE 
								id=".$_POST['id']." AND 
								id_unik IS NOT NULL AND 
								id_unik_indikator IS NOT NULL AND 
								is_locked_indikator=0 AND 
								status=1 AND 
								active=1");

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

	public function update_indikator_sasaran_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_sasaran_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_sasaran_lokal
							WHERE indikator_teks='".$data['indikator_teks']."'
										AND id!=".$data['id']."
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row("
						SELECT * FROM data_renstra_sasaran_lokal 
							WHERE 
								id_unik='".$data['id_unik']."' AND 
								id_unik_indikator IS NULL AND 
								is_locked=0 AND 
								status=1 AND 
								active=1");

					if (empty($dataSasaran)) {
						throw new Exception('Sasaran yang dipilih tidak ditemukan!');
					}

					$inputs = [
						'id_bidang_urusan' => $dataSasaran->id_bidang_urusan,
						'id_misi' => $dataSasaran->id_misi,
						'id_unik' => $dataSasaran->id_unik,
						'id_unit' => $dataSasaran->id_unit,
						'id_visi' => $dataSasaran->id_visi,
						'indikator_teks' => $data['indikator_teks'],
						'kode_bidang_urusan' => $dataSasaran->kode_bidang_urusan,
						'kode_skpd' => $dataSasaran->kode_skpd,
						'kode_tujuan' => $dataSasaran->kode_tujuan,
						'nama_bidang_urusan' => $dataSasaran->nama_bidang_urusan,
						'nama_skpd' => $dataSasaran->nama_skpd,
						'sasaran_teks' => $dataSasaran->sasaran_teks,
						'tujuan_lock' => $dataSasaran->tujuan_lock,
						'tujuan_teks' => $dataSasaran->tujuan_teks,
						'urut_sasaran' => $dataSasaran->urut_sasaran,
						'urut_tujuan' => $dataSasaran->urut_tujuan,
						'update_at' => date('Y-m-d H:i:s')
					];

					if(in_array('administrator', $this->role())){
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];

						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}else{
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];
					}

					$status = $wpdb->update('data_renstra_sasaran_lokal', $inputs, ['id' => $data['id']]);

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

	public function delete_indikator_sasaran_renstra(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$wpdb->get_results("
						DELETE FROM data_renstra_sasaran_lokal 
							WHERE 
								id=".$_POST['id']." AND
								id_unik_indikator IS NOT NULL AND 
								active=1 AND status=1");

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

	private function verify_indikator_sasaran_renstra(array $data){
		if(empty($data['id_unik'])){
			throw new Exception('Sasaran wajib dipilih!');
		}

		if(empty($data['indikator_teks'])){
			throw new Exception('Indikator sasaran tidak boleh kosong!');
		}

		if(empty($data['satuan_usulan'])){
			throw new Exception('Satuan indikator sasaran usulan tidak boleh kosong!');
		}

		if(empty($data['target_awal_usulan'])){
			throw new Exception('Target awal usulan Indikator sasaran tidak boleh kosong!');
		}

		for ($i=1; $i <= $data['lama_pelaksanaan'] ; $i++) { 
			if(empty($data['target_'.$i.'_usulan'])){
				throw new Exception('Target usulan Indikator sasaran tahun ke-'.$i.' tidak boleh kosong!');
			}
		}

		if(empty($data['target_akhir_usulan'])){
			throw new Exception('Target akhir usulan Indikator sasaran tidak boleh kosong!');
		}		
	}

	public function get_program_renstra(){

		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if($_POST['type'] == 1){
					$sql = $wpdb->prepare("
						SELECT * FROM data_renstra_program_lokal
							WHERE kode_sasaran='".$_POST['kode_sasaran']."' AND
								id_unik IS NOT NULL and
								id_unik_indikator IS NULL and
								status=1 AND 
								is_locked=0 AND 
								active=1 ORDER BY id
						");
					$program = $wpdb->get_results($sql, ARRAY_A);
				}else{
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$sql = $wpdb->prepare("
								SELECT 
									* 
								FROM data_renstra_program
								WHERE tahun_anggaran=%d
									kode_sasaran=%s
									id_unik IS NOT NULL AND
									id_unik_indikator IS NULL AND
									status=1 AND 
									is_locked=0 AND
									active=1
								ORDER BY id
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

	public function submit_program_renstra(){
		
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_program_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_program_lokal
							WHERE nama_program ='".trim($data['program_teks'])."'
								AND kode_sasaran='".$data['kode_sasaran']."'
								AND program_lock=0
								AND status=1
								AND active=1
					");

					if(!empty($id_cek)){
						throw new Exception('Program : '.$data['program_teks'].' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row("SELECT * FROM data_renstra_sasaran_lokal WHERE id_unik='".$data['kode_sasaran'] . "' AND is_locked=0 AND status=1 AND active=1");

					if(empty($dataSasaran)){
						throw new Exception('Sasaran belum dipilih!');
					}

					$dataProgram = $wpdb->get_row("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program
                        FROM data_prog_keg as u 
                        WHERE u.tahun_anggaran=".get_option('_crb_tahun_anggaran_sipd')." AND id_program=".$data['id_program']."
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ");

                    if(empty($dataProgram)){
						throw new Exception('Program tidak ditemukan!');
					}

					try {
							$wpdb->insert('data_renstra_program_lokal', [
								'bidur_lock' => 0,
								'id_bidang_urusan' => $dataSasaran->id_bidang_urusan, // diambil dari sasaran renstra atau dari program yang dipilih
								'id_misi' => $dataSasaran->id_misi,
								'id_program' => $dataProgram->id_program,
								'id_unik' => $this->generateRandomString(), // kode_program
								'id_unit' => $dataSasaran->id_unit,
								'id_visi' => $dataSasaran->id_visi,
								'is_locked' => 0,
								'is_locked_indikator' => 0,
								'kode_bidang_urusan' => $dataSasaran->kode_bidang_urusan, // diambil dari sasaran renstra atau dari program yang dipilih
								'kode_program' => $dataProgram->kode_program,
								'kode_sasaran' => $dataSasaran->id_unik,
								'kode_skpd' => $dataSasaran->kode_skpd,
								'kode_tujuan' => $dataSasaran->kode_tujuan,
								'nama_bidang_urusan' => $dataSasaran->nama_bidang_urusan,
								'nama_program' => $dataProgram->nama_program,
								'nama_skpd' => $dataSasaran->nama_skpd,
								'program_lock' => 0,
								'sasaran_lock' => $dataSasaran->is_locked,
								'sasaran_teks' => $dataSasaran->sasaran_teks,
								'status' => 1,
								'tujuan_lock' => $dataSasaran->tujuan_lock,
								'tujuan_teks' => $dataSasaran->tujuan_teks,
								'urut_sasaran' => $dataSasaran->urut_sasaran,
								'urut_tujuan' => $dataSasaran->urut_tujuan,
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

	public function edit_program_renstra(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

				$data = $wpdb->get_row("
					SELECT * FROM data_renstra_program_lokal
						WHERE id_unik='".$_POST['id_unik']."'
							AND id_unik_indikator IS NULL
							AND is_locked=0
							AND active=1
							AND status=1
						", ARRAY_A);

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

	public function update_program_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_program_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_program_lokal
							WHERE nama_program = '".trim($data['program_teks'])."'
										AND id_unik != '".$data['id_unik']."'
										AND program_lock=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Program : '.$data['program_teks'].' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE 
							id_unik='".$data['kode_sasaran'] . "' AND 
							id_unik_indikator IS NULL AND 
							is_locked=0 AND 
							status=1 AND 
							active=1");

					if(empty($dataSasaran)){
						throw new Exception('Sasaran tidak ditemukan!');
					}

					$dataProgram = $wpdb->get_row("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program
                        FROM data_prog_keg as u 
                        WHERE u.tahun_anggaran=".get_option('_crb_tahun_anggaran_sipd')." AND id_program=".$data['id_program']."
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ");

					if(empty($dataProgram)){
						throw new Exception('Program tidak ditemukan!');
					}

					try {
							// update program
							$wpdb->update('data_renstra_program_lokal', [
									'id_bidang_urusan' => $dataSasaran->id_bidang_urusan, // diambil dari sasaran renstra atau dari program yang dipilih
									'id_misi' => $dataSasaran->id_misi,
									'id_program' => $dataProgram->id_program,
									'id_unit' => $dataSasaran->id_unit,
									'id_visi' => $dataSasaran->id_visi,
									'kode_bidang_urusan' => $dataSasaran->kode_bidang_urusan, // diambil dari sasaran renstra atau dari program yang dipilih
									'kode_program' => $dataProgram->kode_program,
									'kode_sasaran' => $dataSasaran->id_unik,
									'kode_skpd' => $dataSasaran->kode_skpd,
									'kode_tujuan' => $dataSasaran->kode_tujuan,
									'nama_bidang_urusan' => $dataSasaran->nama_bidang_urusan,
									'nama_program' => $dataProgram->nama_program,
									'nama_skpd' => $dataSasaran->nama_skpd,
									'sasaran_lock' => $dataSasaran->is_locked,
									'sasaran_teks' => $dataSasaran->sasaran_teks,
									'tujuan_lock' => $dataSasaran->tujuan_lock,
									'tujuan_teks' => $dataSasaran->tujuan_teks,
									'urut_sasaran' => $dataSasaran->urut_sasaran,
									'urut_tujuan' => $dataSasaran->urut_tujuan,
									'update_at' => date('Y-m-d H:i:s')
								], [
									'id_unik' => $data['id_unik'] // pake id_unik biar teks program di row indikator program ikut terupdate
								]);

						echo json_encode([
						'status' => true,
						'message' => 'Sukses ubah program'
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

	public function delete_program_renstra(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					$id_cek = $wpdb->get_var("SELECT * FROM data_renstra_kegiatan_lokal WHERE kode_program='" .$_POST['kode_program']. "' AND id_unik IS NOT NULL AND id_unik_indikator IS NULL AND is_locked=0 AND status=1 AND active=1");

					if(!empty($id_cek)){
						throw new Exception("Program sudah digunakan oleh kegiatan", 1);
					}
					
					$id_cek = $wpdb->get_var("SELECT * FROM data_renstra_program_lokal WHERE id_unik='" .$_POST['kode_program']. "' AND id_unik_indikator IS NOT NULL AND is_locked=0 AND status=1 AND active=1");

					if(!empty($id_cek)){
						throw new Exception("Program sudah digunakan oleh indikator program", 1);
					}

					$wpdb->get_results("DELETE FROM data_renstra_program_lokal WHERE id_unik='".$_POST['kode_program']."'");

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

	private function verify_program_renstra(array $data){
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

	public function get_indikator_program_renstra(){
		
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					if($_POST['type'] == 1){
						$sql = $wpdb->prepare("
							SELECT * FROM data_renstra_program_lokal 
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
							FROM data_renstra_program
							WHERE tahun_anggaran=%d AND 
									id_unik=%s
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND
									active=1
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

	public function submit_indikator_program_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_program_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_program_lokal
							WHERE indikator='".$data['indikator_teks']."'
										AND id_unik='".$data['kode_program']."'
										AND id_unik_indikator IS NOT NULL
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataProgram = $wpdb->get_row("SELECT * FROM data_renstra_program_lokal WHERE id_unik='".$data['kode_program'] ."' AND is_locked=0 AND status=1 AND active=1 AND id_unik_indikator IS NULL");

					if (empty($dataProgram)) {
						throw new Exception('Program yang dipilih tidak ditemukan!');
					}

					$inputs = [
								'bidur_lock' => $dataProgram->bidur_lock,
								'id_bidang_urusan' => $dataProgram->id_bidang_urusan,
								'id_misi' => $dataProgram->id_misi,
								'id_program' => $dataProgram->id_program,
								'id_unik' => $dataProgram->id_unik,
								'id_unik_indikator' => $this->generateRandomString(),
								'id_unit' => $dataProgram->id_unit,
								'id_visi' => $dataProgram->id_visi,
								'indikator' => $data['indikator_teks'],
								'is_locked' => 0,
								'is_locked_indikator' => 0,
								'kode_bidang_urusan' => $dataProgram->kode_bidang_urusan,
								'kode_program' => $dataProgram->kode_program,
								'kode_sasaran' => $dataProgram->kode_sasaran,
								'kode_skpd' => $dataProgram->kode_skpd,
								'kode_tujuan' => $dataProgram->kode_tujuan,
								'nama_bidang_urusan' => $dataProgram->nama_bidang_urusan,
								'nama_program' => $dataProgram->nama_program,
								'nama_skpd' => $dataProgram->nama_skpd,
								'program_lock' => 0,
								'sasaran_lock' => $dataProgram->sasaran_lock,
								'sasaran_teks' => $dataProgram->sasaran_teks,
								'status' => 1,
								'tujuan_lock' => $dataProgram->tujuan_lock,
								'tujuan_teks' => $dataProgram->tujuan_teks,
								'urut_sasaran' => $dataProgram->urut_sasaran,
								'urut_tujuan' => $dataProgram->urut_tujuan,
								'active' => 1
							];

					if(in_array('administrator', $this->role())){
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];

						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}else{
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];
					}

					$status = $wpdb->insert('data_renstra_program_lokal', $inputs);

					if(!$status){
						throw new Exception("Gagal simpan data, harap hubungi admin", 1);
					}
					
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

	public function edit_indikator_program_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$indikator = $wpdb->get_row("SELECT * FROM data_renstra_program_lokal WHERE id=".$_POST['id']." AND id_unik='".$_POST['kode_program']. "' AND id_unik_indikator IS NOT NULL AND is_locked_indikator=0 AND status=1 AND active=1");

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

	public function update_indikator_program_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_program_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_program_lokal
							WHERE indikator='".$data['indikator_teks']."'
										AND id!='".$data['id']."'
										AND id_unik='".$data['kode_program']."'
										AND id_unik_indikator is not null
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataProgram = $wpdb->get_row("SELECT * FROM data_renstra_program_lokal WHERE id_unik='".$data['kode_program'] ."' AND is_locked=0 AND status=1 AND active=1 AND id_unik IS NOT NULL AND id_unik_indikator IS NULL");

					if (empty($dataProgram)) {
						throw new Exception('Program yang dipilih tidak ditemukan!');
					}

					$inputs = [
						'id_bidang_urusan' => $dataProgram->id_bidang_urusan,
						'id_misi' => $dataProgram->id_misi,
						'id_program' => $dataProgram->id_program,
						'id_unit' => $dataProgram->id_unit,
						'id_visi' => $dataProgram->id_visi,
						'indikator' => $data['indikator_teks'],
						'kode_bidang_urusan' => $dataProgram->kode_bidang_urusan,
						'kode_program' => $dataProgram->id_unik,
						'kode_sasaran' => $dataProgram->kode_sasaran,
						'kode_skpd' => $dataProgram->kode_skpd,
						'kode_tujuan' => $dataProgram->kode_tujuan,
						'nama_bidang_urusan' => $dataProgram->nama_bidang_urusan,
						'nama_program' => $dataProgram->nama_program,
						'nama_skpd' => $dataProgram->nama_skpd,
						'sasaran_lock' => $dataProgram->sasaran_lock,
						'sasaran_teks' => $dataProgram->sasaran_teks,
						'tujuan_lock' => $dataProgram->tujuan_lock,
						'tujuan_teks' => $dataProgram->tujuan_teks,
						'urut_sasaran' => $dataProgram->urut_sasaran,
						'urut_tujuan' => $dataProgram->urut_tujuan,
						'update_at' => date('Y-m-d H:i:s')
					];

					if(in_array('administrator', $this->role())){
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];

						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}else{
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];
					}

					$status = $wpdb->update('data_renstra_program_lokal', $inputs, ['id' => $data['id']]);

					if(!$status){
						throw new Exception("Gagal simpan data, harap hubungi admin", 1);
					}

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

	public function delete_indikator_program_renstra(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$wpdb->get_results("DELETE FROM data_renstra_program_lokal WHERE id=".$_POST['id'] . " AND id_unik_indikator IS NOT NULL AND active=1 AND status=1");

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

	private function verify_indikator_program_renstra(array $data){
		if(empty($data['kode_program'])){
			throw new Exception('Program wajib dipilih!');
		}

		if(empty($data['indikator_teks'])){
			throw new Exception('Indikator program tidak boleh kosong!');
		}

		if(empty($data['satuan_usulan'])){
			throw new Exception('Satuan usulan indikator program tidak boleh kosong!');
		}

		if(empty($data['target_awal_usulan'])){
			throw new Exception('Target awal usulan Indikator program tidak boleh kosong!');
		}

		for ($i=1; $i <= $data['lama_pelaksanaan'] ; $i++) { 
			if(empty($data['target_'.$i.'_usulan'])){
				throw new Exception('Target usulan Indikator program tahun ke-'.$i.' tidak boleh kosong!');
			}
		}

		if(empty($data['target_akhir_usulan'])){
			throw new Exception('Target akhir usulan Indikator program tidak boleh kosong!');
		}
	}

	public function get_kegiatan_renstra(){
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
				if($_POST['type'] == 1){
					$sql = $wpdb->prepare("
						SELECT * FROM data_renstra_kegiatan_lokal
							WHERE kode_program='".$_POST['kode_program']."' AND
								id_unik IS NOT NULL and
								id_unik_indikator IS NULL and
								status=1 AND 
								is_locked=0 AND 
								active=1 ORDER BY id
						");
					$kegiatan = $wpdb->get_results($sql, ARRAY_A);
				}else{
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$sql = $wpdb->prepare("
								SELECT 
									* 
								FROM data_renstra_kegiatan
								WHERE tahun_anggaran=%d
									kode_program=%s
									id_unik IS NOT NULL AND
									id_unik_indikator IS NULL AND
									status=1 AND 
									is_locked=0 AND
									active=1
							", $tahun_anggaran, $_POST['kode_program']);
					$kegiatan = $wpdb->get_results($sql, ARRAY_A);
				}

				echo json_encode([
					'status' => true,
					'data' => $kegiatan,
					'message' => 'Sukses get kegiatan by program'
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

	public function add_kegiatan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					$sql = $wpdb->prepare("
								SELECT 
									* 
								FROM data_prog_keg
								WHERE id_program=%d
							", $_POST['id_program']);
					$data = $wpdb->get_results($sql, ARRAY_A);

					$kegiatan = [];
					foreach ($data as $key => $value) {
						if(empty($kegiatan[$value['kode_giat']])){
							$kegiatan[$value['kode_giat']] = [
								'id' => $value['id'],
								'kegiatan_teks' => $value['nama_giat']
							];
						}
					}

					echo json_encode([
						'status' => true,
						'data' => array_values($kegiatan)
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

	public function submit_kegiatan_renstra(){
		
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_kegiatan_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_kegiatan_lokal
							WHERE id_giat=".$data['id_kegiatan']."
								AND kode_program='".$data['kode_program']."'
								AND giat_lock=0
								AND status=1
								AND active=1
					");

					if(!empty($id_cek)){
						throw new Exception('Kegiatan : '.$data['kegiatan_teks'].' sudah ada!');
					}

					$dataProgram = $wpdb->get_row("SELECT * FROM data_renstra_program_lokal WHERE id_unik='".$data['kode_program']."' AND id_unik_indikator IS NULL AND is_locked=0 AND status=1 AND active=1");

					if(empty($dataProgram)){
						throw new Exception('Program tidak ditemukan!');
					}

					$dataKegiatan = $wpdb->get_row("SELECT * FROM data_prog_keg WHERE id='".$data['id_kegiatan']."'");

					if(empty($dataKegiatan)){
						throw new Exception('Kegiatan tidak ditemukan!');
					}

					try {
						$wpdb->insert('data_renstra_kegiatan_lokal', [
								'bidur_lock' => 0,
								'giat_lock' => 0,
								'id_bidang_urusan' => $dataProgram->id_bidang_urusan,
								'id_giat' => $dataKegiatan->id,
								'id_misi' => $dataProgram->id_misi,
								'id_program' => $dataProgram->id_program,
								'id_unik' => $this->generateRandomString(), // kode_kegiatan
								'id_unit' => $dataProgram->id_unit,
								'id_visi' => $dataProgram->id_visi,
								'is_locked' => 0,
								'is_locked_indikator' => 0,
								'kode_bidang_urusan' => $dataProgram->kode_bidang_urusan,
								'kode_giat' => $dataKegiatan->kode_giat,
								'kode_program' => $dataProgram->id_unik,
								'kode_sasaran' => $dataProgram->kode_sasaran,
								'kode_skpd' => $dataProgram->kode_skpd,
								'kode_tujuan' => $dataProgram->kode_tujuan,
								'nama_bidang_urusan' => $dataProgram->nama_bidang_urusan,
								'nama_giat' => $dataKegiatan->nama_giat,
								'nama_program' => $dataProgram->nama_program,
								'nama_skpd' => $dataProgram->nama_skpd,
								'program_lock' => $dataProgram->program_lock,
								'renstra_prog_lock' => $dataProgram->program_lock,
								'sasaran_lock' => $dataProgram->sasaran_lock,
								'sasaran_teks' => $dataProgram->sasaran_teks,
								'status' => 1,
								'tujuan_lock' => $dataProgram->tujuan_lock,
								'tujuan_teks' => $dataProgram->tujuan_teks,
								'urut_sasaran' => $dataProgram->urut_sasaran,
								'urut_tujuan' => $dataProgram->urut_tujuan,
								'active' => 1
							]);

						echo json_encode([
							'status' => true,
							'message' => 'Sukses simpan kegiatan'
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

	public function edit_kegiatan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					$sql = $wpdb->prepare("
								SELECT 
									* 
								FROM data_prog_keg
								WHERE id_program=%d
							", $_POST['id_program']);
					$data = $wpdb->get_results($sql, ARRAY_A);

					$kegiatan = [];
					foreach ($data as $key => $value) {
						if(empty($kegiatan[$value['kode_giat']])){
							$kegiatan[$value['kode_giat']] = [
								'id' => $value['id'],
								'kegiatan_teks' => $value['nama_giat']
							];
						}
					}

					$dataKegiatan = $wpdb->get_row("
								SELECT 
									* 
								FROM data_renstra_kegiatan_lokal
								WHERE id=".$_POST['id_kegiatan']."
							");

					echo json_encode([
						'status' => true,
						'kegiatan' => $dataKegiatan,
						'data' => array_values($kegiatan)
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

	public function update_kegiatan_renstra(){
		
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_kegiatan_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_kegiatan_lokal
							WHERE id_giat=".$data['id_kegiatan']."
								AND kode_program='".$data['kode_program']."'
								AND id!=".$data['id']."
								AND giat_lock=0
								AND status=1
								AND active=1
					");

					if(!empty($id_cek)){
						throw new Exception('Kegiatan : '.$data['kegiatan_teks'].' sudah ada!');
					}

					$dataProgram = $wpdb->get_row("SELECT * FROM data_renstra_program_lokal WHERE id_unik='".$data['kode_program']."' AND id_unik_indikator IS NULL AND is_locked=0 AND status=1 AND active=1");

					if(empty($dataProgram)){
						throw new Exception('Program tidak ditemukan!');
					}

					$dataKegiatan = $wpdb->get_row("SELECT * FROM data_prog_keg WHERE id='".$data['id_kegiatan']."'");

					if(empty($dataKegiatan)){
						throw new Exception('Kegiatan tidak ditemukan!');
					}

					try {
						$wpdb->update('data_renstra_kegiatan_lokal', [
								'bidur_lock' => 0,
								'giat_lock' => 0,
								'id_bidang_urusan' => $dataProgram->id_bidang_urusan,
								'id_giat' => $dataKegiatan->id,
								'id_misi' => $dataProgram->id_misi,
								'id_program' => $dataProgram->id_program,
								'id_unit' => $dataProgram->id_unit,
								'id_visi' => $dataProgram->id_visi,
								'kode_bidang_urusan' => $dataProgram->kode_bidang_urusan,
								'kode_giat' => $dataKegiatan->kode_giat,
								'kode_program' => $dataProgram->id_unik,
								'kode_sasaran' => $dataProgram->kode_sasaran,
								'kode_skpd' => $dataProgram->kode_skpd,
								'kode_tujuan' => $dataProgram->kode_tujuan,
								'nama_bidang_urusan' => $dataProgram->nama_bidang_urusan,
								'nama_giat' => $dataKegiatan->nama_giat,
								'nama_program' => $dataProgram->nama_program,
								'nama_skpd' => $dataProgram->nama_skpd,
								'program_lock' => $dataProgram->program_lock,
								'renstra_prog_lock' => $dataProgram->program_lock,
								'sasaran_lock' => $dataProgram->sasaran_lock,
								'sasaran_teks' => $dataProgram->sasaran_teks,
								'tujuan_lock' => $dataProgram->tujuan_lock,
								'tujuan_teks' => $dataProgram->tujuan_teks,
								'urut_sasaran' => $dataProgram->urut_sasaran,
								'urut_tujuan' => $dataProgram->urut_tujuan
							], [
								'id_unik' => $data['id_unik'] // pake id_unik agar indikator kegiatan ikut terupdate
							]);

						echo json_encode([
							'status' => true,
							'message' => 'Sukses ubah kegiatan'
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

	public function delete_kegiatan_renstra(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					$id_cek = $wpdb->get_var("SELECT * FROM data_renstra_kegiatan_lokal WHERE id_unik='" . $_POST['id_unik']. "' AND id_unik_indikator IS NOT NULL AND is_locked=0 AND status=1 AND active=1");

					if(!empty($id_cek)){
						throw new Exception("Kegiatan sudah digunakan oleh indikator kegiatan", 1);
					}
					
					$wpdb->get_results("DELETE FROM data_renstra_kegiatan_lokal WHERE id=".$_POST['id']);

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus kegiatan'
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

	private function verify_kegiatan_renstra(array $data){
		if(empty($data['id_kegiatan'])){
			throw new Exception('Kegiatan wajib dipilih!');
		}
	}

	public function get_indikator_kegiatan_renstra(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					if($_POST['type']==1){
						$sql = $wpdb->prepare("
							SELECT * FROM data_renstra_kegiatan_lokal 
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
							FROM data_renstra_kegiatan
							WHERE tahun_anggaran=%d AND 
									id_unik=%s
									id_unik_indikator IS NOT NULL AND 
									is_locked_indikator=0 AND
									active=1
							", $tahun_anggaran, $_POST['id_unik']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					}

					echo json_encode([
						'status' => true,
						'data' => $indikator,
						'message' => 'Sukses get indikator kegiatan'
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

	public function submit_indikator_kegiatan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_kegiatan_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_kegiatan_lokal
							WHERE indikator='".$data['indikator_teks']."'
										AND id_unik='".$data['id_unik']."'
										AND id_unik_indikator IS NOT NULL
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataKegiatan = $wpdb->get_row("SELECT * FROM data_renstra_kegiatan_lokal WHERE id_unik='".$data['id_unik'] ."' AND is_locked=0 AND status=1 AND active=1 AND id_unik_indikator IS NULL");

					if (empty($dataKegiatan)) {
						throw new Exception('Kegiatan yang dipilih tidak ditemukan!');
					}

					$inputs = [
								'bidur_lock' => 0,
								'giat_lock' => 0,
								'id_bidang_urusan' => $dataKegiatan->id_bidang_urusan,
								'id_giat' => $dataKegiatan->id_giat,
								'id_misi' => $dataKegiatan->id_misi,
								'id_program' => $dataKegiatan->id_program,
								'id_unik' => $dataKegiatan->id_unik,
								'id_unik_indikator' => $this->generateRandomString(),
								'id_unit' => $dataKegiatan->id_unit,
								'id_visi' => $dataKegiatan->id_visi,
								'indikator' => $data['indikator_teks'],
								'is_locked' => $dataKegiatan->is_locked,
								'is_locked_indikator' => 0,
								'kode_bidang_urusan' => $dataKegiatan->kode_bidang_urusan,
								'kode_giat' => $dataKegiatan->kode_giat,
								'kode_program' => $dataKegiatan->kode_program,
								'kode_sasaran' => $dataKegiatan->kode_sasaran,
								'kode_skpd' => $dataKegiatan->kode_skpd,
								'kode_tujuan' => $dataKegiatan->kode_tujuan,
								'nama_bidang_urusan' => $dataKegiatan->nama_bidang_urusan,
								'nama_giat' => $dataKegiatan->nama_giat,
								'nama_program' => $dataKegiatan->nama_program,
								'nama_skpd' => $dataKegiatan->nama_skpd,
								'program_lock' => $dataKegiatan->program_lock,
								'renstra_prog_lock' => $dataKegiatan->program_lock,
								'sasaran_lock' => $dataKegiatan->sasaran_lock,
								'sasaran_teks' => $dataKegiatan->sasaran_teks,
								'status' => 1,
								'tujuan_lock' => $dataKegiatan->tujuan_lock,
								'tujuan_teks' => $dataKegiatan->tujuan_teks,
								'urut_sasaran' => $dataKegiatan->urut_sasaran,
								'urut_tujuan' => $dataKegiatan->urut_tujuan,
								'active' => 1
							];

					if(in_array('administrator', $this->role())){
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['pagu_1_usulan'] = $data['pagu_1_usulan'];
						$inputs['pagu_2_usulan'] = $data['pagu_2_usulan'];
						$inputs['pagu_3_usulan'] = $data['pagu_3_usulan'];
						$inputs['pagu_4_usulan'] = $data['pagu_4_usulan'];
						$inputs['pagu_5_usulan'] = $data['pagu_5_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];

						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['pagu_1'] = !empty($data['pagu_1']) ? $data['pagu_1'] : $data['pagu_1_usulan'];
						$inputs['pagu_2'] = !empty($data['pagu_2']) ? $data['pagu_2'] : $data['pagu_2_usulan'];
						$inputs['pagu_3'] = !empty($data['pagu_3']) ? $data['pagu_3'] : $data['pagu_3_usulan'];
						$inputs['pagu_4'] = !empty($data['pagu_4']) ? $data['pagu_4'] : $data['pagu_4_usulan'];
						$inputs['pagu_5'] = !empty($data['pagu_5']) ? $data['pagu_5'] : $data['pagu_5_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}else{
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['pagu_1_usulan'] = $data['pagu_1_usulan'];
						$inputs['pagu_2_usulan'] = $data['pagu_2_usulan'];
						$inputs['pagu_3_usulan'] = $data['pagu_3_usulan'];
						$inputs['pagu_4_usulan'] = $data['pagu_4_usulan'];
						$inputs['pagu_5_usulan'] = $data['pagu_5_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];
					}

					$status = $wpdb->insert('data_renstra_kegiatan_lokal', $inputs);

					if(!$status){
						throw new Exception("Gagal simpan data, harap hubungi admin", 1);
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator kegiatan'
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

	public function edit_indikator_kegiatan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$indikator = $wpdb->get_row("SELECT * FROM data_renstra_kegiatan_lokal WHERE id=".$_POST['id']." AND id_unik='".$_POST['kode_kegiatan']. "' AND id_unik_indikator IS NOT NULL AND is_locked_indikator=0 AND status=1 AND active=1");

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

	public function update_indikator_kegiatan_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_kegiatan_renstra($data);

					$id_cek = $wpdb->get_var("
						SELECT id FROM data_renstra_kegiatan_lokal
							WHERE indikator='".$data['indikator_teks']."'
										AND id_unik='".$data['id_unik']."'
										AND id!='".$data['id']."'
										AND id_unik_indikator IS NOT NULL
										AND is_locked_indikator=0
										AND status=1
										AND active=1
								");
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataKegiatan = $wpdb->get_row("SELECT * FROM data_renstra_kegiatan_lokal WHERE id_unik='".$data['id_unik'] ."' AND is_locked=0 AND status=1 AND active=1 AND id_unik_indikator IS NULL");

					if (empty($dataKegiatan)) {
						throw new Exception('Kegiatan yang dipilih tidak ditemukan!');
					}

					$inputs = [
								'id_bidang_urusan' => $dataKegiatan->id_bidang_urusan,
								'id_giat' => $dataKegiatan->id_giat,
								'id_misi' => $dataKegiatan->id_misi,
								'id_program' => $dataKegiatan->id_program,
								'id_unik' => $dataKegiatan->id_unik,
								'id_unit' => $dataKegiatan->id_unit,
								'id_visi' => $dataKegiatan->id_visi,
								'indikator' => $data['indikator_teks'],
								'is_locked' => $dataKegiatan->is_locked,
								'kode_bidang_urusan' => $dataKegiatan->kode_bidang_urusan,
								'kode_giat' => $dataKegiatan->kode_giat,
								'kode_program' => $dataKegiatan->id_unik,
								'kode_sasaran' => $dataKegiatan->kode_program,
								'kode_skpd' => $dataKegiatan->kode_skpd,
								'kode_tujuan' => $dataKegiatan->kode_tujuan,
								'nama_bidang_urusan' => $dataKegiatan->nama_bidang_urusan,
								'nama_giat' => $dataKegiatan->nama_giat,
								'nama_program' => $dataKegiatan->nama_program,
								'nama_skpd' => $dataKegiatan->nama_skpd,
								'program_lock' => $dataKegiatan->program_lock,
								'renstra_prog_lock' => $dataKegiatan->program_lock,
								'sasaran_lock' => $dataKegiatan->sasaran_lock,
								'sasaran_teks' => $dataKegiatan->sasaran_teks,
								'tujuan_lock' => $dataKegiatan->tujuan_lock,
								'tujuan_teks' => $dataKegiatan->tujuan_teks,
								'urut_sasaran' => $dataKegiatan->urut_sasaran,
								'urut_tujuan' => $dataKegiatan->urut_tujuan,
							];

					if(in_array('administrator', $this->role())){
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['pagu_1_usulan'] = $data['pagu_1_usulan'];
						$inputs['pagu_2_usulan'] = $data['pagu_2_usulan'];
						$inputs['pagu_3_usulan'] = $data['pagu_3_usulan'];
						$inputs['pagu_4_usulan'] = $data['pagu_4_usulan'];
						$inputs['pagu_5_usulan'] = $data['pagu_5_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];

						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['pagu_1'] = !empty($data['pagu_1']) ? $data['pagu_1'] : $data['pagu_1_usulan'];
						$inputs['pagu_2'] = !empty($data['pagu_2']) ? $data['pagu_2'] : $data['pagu_2_usulan'];
						$inputs['pagu_3'] = !empty($data['pagu_3']) ? $data['pagu_3'] : $data['pagu_3_usulan'];
						$inputs['pagu_4'] = !empty($data['pagu_4']) ? $data['pagu_4'] : $data['pagu_4_usulan'];
						$inputs['pagu_5'] = !empty($data['pagu_5']) ? $data['pagu_5'] : $data['pagu_5_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}else{
						$inputs['satuan_usulan'] = $data['satuan_usulan'];
						$inputs['target_1_usulan'] = $data['target_1_usulan'];
						$inputs['target_2_usulan'] = $data['target_2_usulan'];
						$inputs['target_3_usulan'] = $data['target_3_usulan'];
						$inputs['target_4_usulan'] = $data['target_4_usulan'];
						$inputs['target_5_usulan'] = $data['target_5_usulan'];
						$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
						$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
						$inputs['pagu_1_usulan'] = $data['pagu_1_usulan'];
						$inputs['pagu_2_usulan'] = $data['pagu_2_usulan'];
						$inputs['pagu_3_usulan'] = $data['pagu_3_usulan'];
						$inputs['pagu_4_usulan'] = $data['pagu_4_usulan'];
						$inputs['pagu_5_usulan'] = $data['pagu_5_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];
					}

					$status = $wpdb->update('data_renstra_kegiatan_lokal', $inputs, ['id' => $data['id']]);

					if(!$status){
						throw new Exception("Gagal simpan data, harap hubungi admin", 1);
					}
						
					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator kegiatan'
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

	public function delete_indikator_kegiatan_renstra(){
		global $wpdb;
		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					$wpdb->get_results("DELETE FROM data_renstra_kegiatan_lokal WHERE id=".$_POST['id']);

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator kegiatan'
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

	private function verify_indikator_kegiatan_renstra(array $data){
		if(empty($data['id_unik'])){
			throw new Exception('Kegiatan wajib dipilih!');
		}

		if(empty($data['indikator_teks'])){
			throw new Exception('Indikator kegiatan tidak boleh kosong!');
		}

		if(empty($data['satuan_usulan'])){
			throw new Exception('Satuan indikator usulan kegiatan tidak boleh kosong!');
		}

		if(empty($data['target_awal_usulan'])){
			throw new Exception('Target awal usulan Indikator kegiatan tidak boleh kosong!');
		}

		for ($i=1; $i <= $data['lama_pelaksanaan'] ; $i++) { 
			if(empty($data['target_'.$i.'_usulan'])){
				throw new Exception('Target usulan Indikator kegiatan tahun ke-'.$i.' tidak boleh kosong!');
			}

			if(empty($data['pagu_'.$i.'_usulan'])){
				throw new Exception('Pagu usulan Indikator kegiatan tahun ke-'.$i.' tidak boleh kosong!');
			}
		}

		if(empty($data['target_akhir_usulan'])){
			throw new Exception('Target akhir usulan Indikator kegiatan tidak boleh kosong!');
		}
	}

	public function get_bidang_urusan_renstra(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil get data bidang urusan!'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
                $tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');

                $join="";
                if(!empty($_POST['id_unit'])){
                	if($_POST['relasi_perencanaan'] != '-'){
                		
                		if($_POST['type']==1){
                			$join.="INNER JOIN data_unit as s on s.kode_skpd like CONCAT('%',u.kode_bidang_urusan,'%')
				                        and s.active=1
				                        and s.is_skpd=1
				                        and s.tahun_anggaran=u.tahun_anggaran";
                		}

                		if($_POST['id_tipe_relasi']==2){
                			$join.=" INNER JOIN data_rpjmd_program_lokal t on t.id_unit = s.id_skpd";
                		}elseif ($_POST['id_tipe_relasi']==3) {
                			$join.=" INNER JOIN data_rpd_program_lokal t on t.id_unit = s.id_skpd";
                		}
                	}
                }

                if(!empty($_POST['type']) && $_POST['type'] == 1){
                    $data = $wpdb->get_results("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program
                        FROM data_prog_keg as u
                        ".$join."
                        WHERE u.tahun_anggaran=$tahun_anggaran
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ");
                }else{
                    $data = $wpdb->get_results("
                        SELECT
                            s.id_skpd,
                            s.kode_skpd,
                            s.nama_skpd,
                            u.nama_urusan,
                            u.nama_program,
                            u.kode_program,
                            u.id_program,
                            u.nama_bidang_urusan
                        FROM data_prog_keg as u 
                        INNER JOIN data_unit as s on s.kode_skpd like CONCAT('%',u.kode_bidang_urusan,'%')
                            and s.active=1
                            and s.is_skpd=1
                            and s.tahun_anggaran=u.tahun_anggaran
                        ".$join."
                        WHERE u.tahun_anggaran=$tahun_anggaran
                        GROUP BY u.kode_program, s.kode_skpd
                        ORDER BY u.kode_program ASC, s.kode_skpd ASC 
                    ");
                }
                $ret['data'] = $data;
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

    public function view_laporan_tc27_renstra(){
    	global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil generate laporan tc27!'
        );
        $data_all = array(
			'data' => array()
		);
		
		if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
            	$tujuan_all = $wpdb->get_results($wpdb->prepare("
					SELECT 
						* 
					FROM data_renstra_tujuan_lokal 
					WHERE 
						id_unit=%d AND 
						active=1 ORDER BY urut_tujuan",
						$_POST['id_unit']), ARRAY_A);

				foreach ($tujuan_all as $keyTujuan => $tujuan_value) {
					if(empty($data_all['data'][$tujuan_value['id_unik']])){
						$data_all['data'][$tujuan_value['id_unik']] = [
							'id' => $tujuan_value['id'],
							'id_bidang_urusan' => $tujuan_value['id_bidang_urusan'],
							'id_unik' => $tujuan_value['id_unik'],
							'tujuan_teks' => $tujuan_value['tujuan_teks'],
							'nama_bidang_urusan' => $tujuan_value['nama_bidang_urusan'],
							'indikator' => array(),
							'data' => array(),
							'status_rpjm' => false
						];

						if(!empty($tujuan_value['kode_sasaran_rpjm']) && $relasi_perencanaan != '-'){
							$table = 'data_rpjmd_sasaran_lokal';
							switch ($id_tipe_relasi) {
								case '2':
										$table = 'data_rpjmd_sasaran_lokal';
									break;

								case '3':
										$table = 'data_rpd_sasaran_lokal';
									break;
							}

							$id = $wpdb->get_var("SELECT id FROM ".$table." WHERE id_unik='{$tujuan_value['kode_sasaran_rpjm']}'");
							if(!empty($id)){
								$data_all['data'][$tujuan_value['id_unik']]['status_rpjm']=true;
							}
						}
					}

					$tujuan_ids[$tujuan_value['id_unik']] = "'".$tujuan_value['id_unik']."'";

					if(!empty($tujuan_value['id_unik_indikator'])){
						if(empty($data_all['data'][$tujuan_value['id_unik']]['indikator'][$tujuan_value['id_unik_indikator']])){
							$data_all['data'][$tujuan_value['id_unik']]['indikator'][$tujuan_value['id_unik_indikator']] = [
								'id_unik_indikator' => $tujuan_value['id_unik_indikator'],
								'indikator_teks' => $tujuan_value['indikator_teks'],
								'satuan' => $tujuan_value['satuan'],
								'target_1' => $tujuan_value['target_1'],
								'target_2' => $tujuan_value['target_2'],
								'target_3' => $tujuan_value['target_3'],
								'target_4' => $tujuan_value['target_4'],
								'target_5' => $tujuan_value['target_5'],
								'target_awal' => $tujuan_value['target_awal'],
								'target_akhir' => $tujuan_value['target_akhir'],
							];
						}
					}

					if(empty($tujuan_value['id_unik_indikator'])){
						$sasaran_all = $wpdb->get_results($wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_sasaran_lokal 
							WHERE 
								kode_tujuan=%s AND 
								active=1 ORDER BY urut_sasaran",
								$tujuan_value['id_unik']), ARRAY_A);

						foreach ($sasaran_all as $keySasaran => $sasaran_value) {
							if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']])){
								$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']] = [
									'id' => $sasaran_value['id'],
									'id_unik' => $sasaran_value['id_unik'],
									'sasaran_teks' => $sasaran_value['sasaran_teks'],
									'indikator' => array(),
									'data' => array()
								];
							}

							$sasaran_ids[$sasaran_value['id_unik']] = "'".$sasaran_value['id_unik']."'";

							if(!empty($sasaran_value['id_unik_indikator'])){
								if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['indikator'][$sasaran_value['id_unik_indikator']])){
									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['indikator'][$sasaran_value['id_unik_indikator']] = [
										'id_unik_indikator' => $sasaran_value['id_unik_indikator'],
										'indikator_teks' => $sasaran_value['indikator_teks'],
										'satuan' => $sasaran_value['satuan'],
										'target_1' => $sasaran_value['target_1'],
										'target_2' => $sasaran_value['target_2'],
										'target_3' => $sasaran_value['target_3'],
										'target_4' => $sasaran_value['target_4'],
										'target_5' => $sasaran_value['target_5'],
										'target_awal' => $sasaran_value['target_awal'],
										'target_akhir' => $sasaran_value['target_akhir'],
									];
								}
							}

							if(empty($sasaran_value['id_unik_indikator'])){

								$program_all = $wpdb->get_results($wpdb->prepare("
										SELECT 
											* 
										FROM data_renstra_program_lokal 
										WHERE 
											kode_sasaran=%s AND 
											kode_tujuan=%s AND 
											active=1 ORDER BY id",
											$sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);

									foreach ($program_all as $keyProgram => $program_value) {
										if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']])){
											$kode = explode(" ", $program_value['nama_program']);
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']] = [
												'id' => $program_value['id'],
												'id_unik' => $program_value['id_unik'],
												'kode' => $kode[0],
												'program_teks' => $program_value['nama_program'],
												'indikator' => array(),
												'data' => array()
											];
										}

									$program_ids[$program_value['id_unik']] = "'".$program_value['id_unik']."'";

									if(!empty($program_value['id_unik_indikator'])){
										if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']])){
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']] = [
												'id_unik_indikator' => $program_value['id_unik_indikator'],
												'indikator_teks' => $program_value['indikator'],
												'satuan' => $program_value['satuan'],
												'target_1' => $program_value['target_1'],
												'pagu_1' => $program_value['pagu_1'],
												'target_2' => $program_value['target_2'],
												'pagu_2' => $program_value['pagu_2'],
												'target_3' => $program_value['target_3'],
												'pagu_3' => $program_value['pagu_3'],
												'target_4' => $program_value['target_4'],
												'pagu_4' => $program_value['pagu_4'],
												'target_5' => $program_value['target_5'],
												'pagu_5' => $program_value['pagu_5'],
												'target_awal' => $program_value['target_awal'],
												'target_akhir' => $program_value['target_akhir'],
											];
										}
									}

									if(empty($program_value['id_unik_indikator'])){
										$kegiatan_all = $wpdb->get_results($wpdb->prepare("
														SELECT 
															* 
														FROM data_renstra_kegiatan_lokal 
														WHERE 
															kode_program=%s AND 
															kode_sasaran=%s AND 
															kode_tujuan=%s AND 
															active=1 ORDER BY id",
															$program_value['id_unik'],
															$sasaran_value['id_unik'],
															$tujuan_value['id_unik'],
														), ARRAY_A);

										foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {
														
											if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']])){
												$kode = explode(" ", $kegiatan_value['nama_giat']);
												$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']] = [
													'id' => $kegiatan_value['id'],
													'id_unik' => $kegiatan_value['id_unik'],
													'kode' => $kode[0],
													'kegiatan_teks' => $kegiatan_value['nama_giat'],
													'indikator' => array()
												];
											}

											$kegiatan_ids[$kegiatan_value['id_unik']] = "'".$kegiatan_value['id_unik']."'";

											if(!empty($kegiatan_value['id_unik_indikator'])){
												if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])){
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']] = [
														'id_unik_indikator' => $kegiatan_value['id_unik_indikator'],
														'indikator_teks' => $kegiatan_value['indikator'],
														'satuan' => $kegiatan_value['satuan'],
														'target_1' => $kegiatan_value['target_1'],
														'pagu_1' => $kegiatan_value['pagu_1'],
														'target_2' => $kegiatan_value['target_2'],
														'pagu_2' => $kegiatan_value['pagu_2'],
														'target_3' => $kegiatan_value['target_3'],
														'pagu_3' => $kegiatan_value['pagu_3'],
														'target_4' => $kegiatan_value['target_4'],
														'pagu_4' => $kegiatan_value['pagu_4'],
														'target_5' => $kegiatan_value['target_5'],
														'pagu_5' => $kegiatan_value['pagu_5'],
														'target_awal' => $kegiatan_value['target_awal'],
														'target_akhir' => $kegiatan_value['target_akhir'],
													];
												}
											}
										}
									}
								}
							}
						}
					}
				}

				foreach ($data_all['data'] as $tujuan) {
					$indikator_tujuan = '';
					$target_awal = '';
					$target_1 = '';
					$target_2 = '';
					$target_3 = '';
					$target_4 = '';
					$target_5 = '';
					$target_akhir = '';
					$satuan = '';

					$bg_rpjm = (!$tujuan['status_rpjm']) ? ' status-rpjm' : '';
					foreach($tujuan['indikator'] as $key => $indikator){
						$indikator_tujuan .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
						$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
						$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
						$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
						$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
						$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
						$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
						$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
						$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
					}

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
						foreach($sasaran['indikator'] as $key => $indikator){
							$indikator_sasaran .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
							$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
							$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
							$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
							$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
							$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
							$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
							$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
							$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
						}
						
						foreach ($sasaran['data'] as $program) {
							$no_program++;
							$indikator_program = '';
							$target_awal = '';
							$target_1 = '';
							$pagu_1   = '';
							$target_2 = '';
							$pagu_2   = '';
							$target_3 = '';
							$pagu_3   = '';
							$target_4 = '';
							$pagu_4   = '';
							$target_5 = '';
							$pagu_5   = '';
							$target_akhir = '';
							$satuan = '';
							foreach($program['indikator'] as $key => $indikator){
								$indikator_program .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
								$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
								$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
								$pagu_1 .= '<div class="indikator">'.$indikator['pagu_1'].'</div>';
								$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
								$pagu_2 .= '<div class="indikator">'.$indikator['pagu_2'].'</div>';
								$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
								$pagu_3 .= '<div class="indikator">'.$indikator['pagu_3'].'</div>';
								$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
								$pagu_4 .= '<div class="indikator">'.$indikator['pagu_4'].'</div>';
								$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
								$pagu_5 .= '<div class="indikator">'.$indikator['pagu_5'].'</div>';
								$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
								$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
							}

							$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
							$pagu_arr = [$pagu_1, $pagu_2, $pagu_3, $pagu_4, $pagu_5];
							$body .= '
									<tr class="tr-program'.$bg_rpjm.'">
										<td class="kiri atas kanan bawah">'.$tujuan['tujuan_teks'].'</td>
										<td class="kiri atas kanan bawah">'.$sasaran['sasaran_teks'].'</td>
										<td class="kiri atas kanan bawah">'.$program['kode'].'</td>
										<td class="kiri atas kanan bawah">'.$program['program_teks'].'</td>
										<td class="kiri atas kanan bawah">'.$indikator_program.'</td>';
										for ($i=1; $i <= $_POST['lama_pelaksanaan']; $i++) { 
											$body.="<td class=\"kiri atas kanan bawah\">".$target_arr[$i]."</td><td class=\"atas kanan bawah\">".$pagu_arr[$i]."</td>";
										}
										$body.='<td class="kiri kiri atas kanan bawah">'.$target_akhir.'</td>
									</tr>
							';
							
							foreach ($program['data'] as $kegiatan) {
								$indikator_kegiatan = '';
								$target_awal = '';
								$target_1 = '';
								$pagu_1   = '';
								$target_2 = '';
								$pagu_2   = '';
								$target_3 = '';
								$pagu_3   = '';
								$target_4 = '';
								$pagu_4   = '';
								$target_5 = '';
								$pagu_5   = '';
								$target_akhir = '';
								$satuan = '';
								foreach($kegiatan['indikator'] as $key => $indikator){
									$indikator_kegiatan .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
									$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
									$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
									$pagu_1 .= '<div class="indikator">'.$indikator['pagu_1'].'</div>';
									$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
									$pagu_2 .= '<div class="indikator">'.$indikator['pagu_2'].'</div>';
									$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
									$pagu_3 .= '<div class="indikator">'.$indikator['pagu_3'].'</div>';
									$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
									$pagu_4 .= '<div class="indikator">'.$indikator['pagu_4'].'</div>';
									$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
									$pagu_5 .= '<div class="indikator">'.$indikator['pagu_5'].'</div>';
									$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
									$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
								}

								$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
								$pagu_arr = [$pagu_1, $pagu_2, $pagu_3, $pagu_4, $pagu_5];
								$body .= '
										<tr class="tr-kegiatan'.$bg_rpjm.'">
											<td class="kiri atas kanan bawah"></td>
											<td class="kiri atas kanan bawah"></td>
											<td class="kiri atas kanan bawah">'.$kegiatan['kode'].'</td>
											<td class="kiri atas kanan bawah">'.$kegiatan['kegiatan_teks'].'</td>
											<td class="kiri atas kanan bawah">'.$indikator_kegiatan.'</td>';
											for ($i=1; $i <= $_POST['lama_pelaksanaan']; $i++) { 
												$body.="<td class=\"kiri atas kanan bawah\">".$target_arr[$i]."</td><td class=\"atas kanan bawah\">".$pagu_arr[$i]."</td>";
											}
											$body.='
											<td class="kiri atas kanan bawah">'.$target_akhir.'</td>
										</tr>
								';
							}
						}
					}
				}

				$html='
					<table id="view-table-renstra" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
						<thead>';

					$html.='<tr>
								<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok kiri">Tujuan</th>
								<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Sasaran</th>
								<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Kode</th>
								<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Program dan Kegiatan</th>
								<th style="width: 400px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Indikator Kinerja Tujuan, Sasaran, Program(outcome) dan Kegiatan (output)</th>
								<th style="width: 400px;"class="row_head_kinerja atas kanan bawah text_tengah text_blok">Target Kinerja Program dan Kerangka Pendanaan</th>
								<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Kondisi Kinerja pada akhir periode Renstra Perangkat Daerah</th>
							</tr>';

							$html.="<tr>";
							for ($i=$_POST['awal_renstra']; $i <= $_POST['akhir_renstra']; $i++) { 
								$html.='<th style="width: 100px;" class="row_head_1_tahun atas kanan bawah text_tengah text_blok kiri">'.$i.'</th>';
							}
							$html.="</tr>";

							$html.="<tr>";
							for ($i=1; $i <= $_POST['lama_pelaksanaan']; $i++) { 
								$html.='<th style="width: 100px;" class="row_head_2 atas kanan bawah text_tengah text_blok">Target</th><th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Rp.</th>';
							}
							$html.='</tr>';

							$html.='<tr>
								<th class="atas kanan bawah text_tengah text_blok kiri">1</th>
								<th class="atas kanan bawah text_tengah text_blok">2</th>
								<th class="atas kanan bawah text_tengah text_blok">3</th>
								<th class="atas kanan bawah text_tengah text_blok">4</th>
								<th class="atas kanan bawah text_tengah text_blok">5</th>';

								$target_temp = 6;
								for ($i=1; $i <= $_POST['lama_pelaksanaan']; $i++) { 
									if($i!=1){
										$target_temp=$pagu_temp+1; 
									}
									$pagu_temp=$target_temp+1;
									$html.="<th class='atas kanan bawah text_tengah text_blok'>".$target_temp."</th><th class='atas kanan bawah text_tengah text_blok'>".$pagu_temp."</th>";
								}
								$html.="<th class='atas kanan bawah text_tengah text_blok'>".($pagu_temp+1)."</th>
							</tr>";

						$html.="</thead>
						<tbody>".$body."</tbody>
					</table>";

					$ret['html'] = $html;

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

    private function generate_tc27(){

    }
}