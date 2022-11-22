<?php

class Wpsipd_Public_Base_3
{
	
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

	public function verify_indikator_tujuan_renstra(array $data){
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

					$status = $wpdb->insert('data_renstra_sasaran_lokal', [
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
						'satuan' => $data['satuan'],
						'status' => 1,
						'target_1' => $data['target_1'],
						'target_2' => $data['target_2'],
						'target_3' => $data['target_3'],
						'target_4' => $data['target_4'],
						'target_5' => $data['target_5'],
						'target_awal' => $data['target_awal'],
						'target_akhir' => $data['target_akhir'],
						'tujuan_lock' => $dataSasaran->tujuan_lock,
						'tujuan_teks' => $dataSasaran->tujuan_teks,
						'urut_sasaran' => $dataSasaran->urut_sasaran,
						'urut_tujuan' => $dataSasaran->urut_tujuan,
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

					$status = $wpdb->update('data_renstra_sasaran_lokal', [
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
						'satuan' => $data['satuan'],
						'target_1' => $data['target_1'],
						'target_2' => $data['target_2'],
						'target_3' => $data['target_3'],
						'target_4' => $data['target_4'],
						'target_5' => $data['target_5'],
						'target_awal' => $data['target_awal'],
						'target_akhir' => $data['target_akhir'],
						'tujuan_lock' => $dataSasaran->tujuan_lock,
						'tujuan_teks' => $dataSasaran->tujuan_teks,
						'urut_sasaran' => $dataSasaran->urut_sasaran,
						'urut_tujuan' => $dataSasaran->urut_tujuan
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

					$wpdb->insert('data_renstra_program_lokal', [
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
								'kode_sasaran' => $dataProgram->id_unik,
								'kode_skpd' => $dataProgram->kode_skpd,
								'kode_tujuan' => $dataProgram->kode_tujuan,
								'nama_bidang_urusan' => $dataProgram->nama_bidang_urusan,
								'nama_program' => $dataProgram->nama_program,
								'nama_skpd' => $dataProgram->nama_skpd,
								'program_lock' => 0,
								'sasaran_lock' => $dataProgram->sasaran_lock,
								'sasaran_teks' => $dataProgram->sasaran_teks,
								'satuan' => $data['satuan'],
								'status' => 1,
								'target_1' => $data['target_1'],
								'target_2' => $data['target_2'],
								'target_3' => $data['target_3'],
								'target_4' => $data['target_4'],
								'target_5' => $data['target_5'],
								'target_awal' => $data['target_awal'],
								'target_akhir' => $data['target_akhir'],
								'tujuan_lock' => $dataProgram->tujuan_lock,
								'tujuan_teks' => $dataProgram->tujuan_teks,
								'urut_sasaran' => $dataProgram->urut_sasaran,
								'urut_tujuan' => $dataProgram->urut_tujuan,
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

					$wpdb->update('data_renstra_program_lokal', [
						'id_bidang_urusan' => $dataSasaran->id_bidang_urusan,
						'id_misi' => $dataSasaran->id_misi,
						'id_program' => $dataProgram->id_program,
						'id_unit' => $dataSasaran->id_unit,
						'id_visi' => $dataSasaran->id_visi,
						'indikator' => $data['indikator_teks'],
						'kode_bidang_urusan' => $dataSasaran->kode_bidang_urusan,
						'kode_program' => $dataProgram->kode_program,
						'kode_sasaran' => $dataSasaran->id_unik,
						'kode_skpd' => $dataSasaran->kode_skpd,
						'kode_tujuan' => $dataSasaran->kode_tujuan,
						'nama_bidang_urusan' => $dataSasaran->nama_bidang_urusan,
						'nama_program' => $dataProgram->nama_program,
						'nama_skpd' => $dataSasaran->nama_skpd,
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
						'tujuan_lock' => $dataSasaran->tujuan_lock,
						'tujuan_teks' => $dataSasaran->tujuan_teks,
						'urut_sasaran' => $dataSasaran->urut_sasaran,
						'urut_tujuan' => $dataSasaran->urut_tujuan,
						'update_at' => date('Y-m-d H:i:s')
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

		if(empty($data['satuan'])){
			throw new Exception('Satuan indikator program tidak boleh kosong!');
		}

		if(empty($data['target_1'])){
			throw new Exception('Target Indikator program tahun ke-1 tidak boleh kosong!');
		}

		if(empty($data['target_2'])){
			throw new Exception('Target Indikator program tahun ke-2 tidak boleh kosong!');
		}

		if(empty($data['target_3'])){
			throw new Exception('Target Indikator program tahun ke-3 tidak boleh kosong!');
		}

		if(empty($data['target_4'])){
			throw new Exception('Target Indikator program tahun ke-4 tidak boleh kosong!');
		}

		if(empty($data['target_5'])){
			throw new Exception('Target Indikator program tahun ke-5 tidak boleh kosong!');
		}

		if(empty($data['target_awal'])){
			throw new Exception('Target awal Indikator program tidak boleh kosong!');
		}

		if(empty($data['target_akhir'])){
			throw new Exception('Target akhir Indikator program tidak boleh kosong!');
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
								'kode_sasaran' => $dataProgram->kode_program,
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
								'kode_sasaran' => $dataProgram->kode_program,
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

					$wpdb->insert('data_renstra_kegiatan_lokal', [
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
								'kode_program' => $dataKegiatan->id_unik,
								'kode_sasaran' => $dataKegiatan->kode_program,
								'kode_skpd' => $dataKegiatan->kode_skpd,
								'kode_tujuan' => $dataKegiatan->kode_tujuan,
								'nama_bidang_urusan' => $dataKegiatan->nama_bidang_urusan,
								'nama_giat' => $dataKegiatan->nama_giat,
								'nama_program' => $dataKegiatan->nama_program,
								'nama_skpd' => $dataKegiatan->nama_skpd,
								'pagu_1' => $data['pagu_1'],
								'pagu_2' => $data['pagu_2'],
								'pagu_3' => $data['pagu_3'],
								'pagu_4' => $data['pagu_4'],
								'pagu_5' => $data['pagu_5'],
								'program_lock' => $dataKegiatan->program_lock,
								'renstra_prog_lock' => $dataKegiatan->program_lock,
								'sasaran_lock' => $dataKegiatan->sasaran_lock,
								'sasaran_teks' => $dataKegiatan->sasaran_teks,
								'satuan' => $data['satuan'],
								'status' => 1,
								'target_1' => $data['target_1'],
								'target_2' => $data['target_2'],
								'target_3' => $data['target_3'],
								'target_4' => $data['target_4'],
								'target_5' => $data['target_5'],
								'target_awal' => $data['target_awal'],
								'target_akhir' => $data['target_akhir'],
								'tujuan_lock' => $dataKegiatan->tujuan_lock,
								'tujuan_teks' => $dataKegiatan->tujuan_teks,
								'urut_sasaran' => $dataKegiatan->urut_sasaran,
								'urut_tujuan' => $dataKegiatan->urut_tujuan,
								'active' => 1
							]);

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

					$wpdb->update('data_renstra_kegiatan_lokal', [
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
								'pagu_1' => $data['pagu_1'],
								'pagu_2' => $data['pagu_2'],
								'pagu_3' => $data['pagu_3'],
								'pagu_4' => $data['pagu_4'],
								'pagu_5' => $data['pagu_5'],
								'program_lock' => $dataKegiatan->program_lock,
								'renstra_prog_lock' => $dataKegiatan->program_lock,
								'sasaran_lock' => $dataKegiatan->sasaran_lock,
								'sasaran_teks' => $dataKegiatan->sasaran_teks,
								'satuan' => $data['satuan'],
								'target_1' => $data['target_1'],
								'target_2' => $data['target_2'],
								'target_3' => $data['target_3'],
								'target_4' => $data['target_4'],
								'target_5' => $data['target_5'],
								'target_awal' => $data['target_awal'],
								'target_akhir' => $data['target_akhir'],
								'tujuan_lock' => $dataKegiatan->tujuan_lock,
								'tujuan_teks' => $dataKegiatan->tujuan_teks,
								'urut_sasaran' => $dataKegiatan->urut_sasaran,
								'urut_tujuan' => $dataKegiatan->urut_tujuan,
							], [
								'id' => $data['id']
							]);

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

	private function verify_indikator_kegiatan_renstra(array $data){
		if(empty($data['id_unik'])){
			throw new Exception('Kegiatan wajib dipilih!');
		}

		if(empty($data['indikator_teks'])){
			throw new Exception('Indikator kegiatan tidak boleh kosong!');
		}

		if(empty($data['satuan'])){
			throw new Exception('Satuan indikator kegiatan tidak boleh kosong!');
		}

		if(empty($data['target_1'])){
			throw new Exception('Target Indikator kegiatan tahun ke-1 tidak boleh kosong!');
		}

		if(empty($data['pagu_1'])){
			throw new Exception('Pagu indikator kegiatan tahun ke-1 tidak boleh kosong!');
		}

		if(empty($data['target_2'])){
			throw new Exception('Target Indikator kegiatan tahun ke-2 tidak boleh kosong!');
		}

		if(empty($data['pagu_2'])){
			throw new Exception('Pagu indikator kegiatan tahun ke-2 tidak boleh kosong!');
		}

		if(empty($data['target_3'])){
			throw new Exception('Target Indikator kegiatan tahun ke-3 tidak boleh kosong!');
		}

		if(empty($data['pagu_3'])){
			throw new Exception('Pagu indikator kegiatan tahun ke-3 tidak boleh kosong!');
		}

		if(empty($data['target_4'])){
			throw new Exception('Target Indikator kegiatan tahun ke-4 tidak boleh kosong!');
		}

		if(empty($data['pagu_4'])){
			throw new Exception('Pagu indikator kegiatan tahun ke-4 tidak boleh kosong!');
		}

		if(empty($data['target_5'])){
			throw new Exception('Target Indikator kegiatan tahun ke-5 tidak boleh kosong!');
		}

		if(empty($data['pagu_5'])){
			throw new Exception('Pagu indikator kegiatan tahun ke-5 tidak boleh kosong!');
		}

		if(empty($data['target_awal'])){
			throw new Exception('Target awal Indikator kegiatan tidak boleh kosong!');
		}

		if(empty($data['target_akhir'])){
			throw new Exception('Target akhir Indikator kegiatan tidak boleh kosong!');
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
}