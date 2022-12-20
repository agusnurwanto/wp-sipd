<?php

class Wpsipd_Public_Base_3
{
	
	protected function role(){
		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);
		return $user_meta->roles;
	}

	public function get_tujuan_parent_by_tipe($params = array(), $tujuan_exist = array()){

		global $wpdb;

		if($params['relasi_perencanaan']=='-'){
			return [];
		}

		switch ($params['id_tipe_relasi']) {
			case '2':
				$tableA = "data_rpjmd_sasaran_lokal_history";
				$tableB = "data_rpjmd_program_lokal_history";
				$tableC = "data_rpjmd_tujuan_lokal_history";
				break;

			case '3':
				$tableA = "data_rpd_sasaran_lokal_history";
				$tableB = "data_rpd_program_lokal_history";
				$tableC = "data_rpd_tujuan_lokal_history";
				break;
			
			default:
				throw new Exception("Tipe relasi perencanaan tidak diketahui, harap menghubungi admin", 1);
				break;
		}

		if(!empty($tujuan_exist)){
			$sql = "
				SELECT DISTINCT
					a.id_unik as id_unik_sasaran, 
					a.sasaran_teks,
					c.id_unik, 
					c.tujuan_teks
				FROM ".$tableA." a 
					INNER JOIN ".$tableC." c
						ON a.kode_tujuan=c.id_unik
				WHERE  
					a.id_unik='".$tujuan_exist->kode_sasaran_rpjm."' AND 
					a.id_jadwal=".$params['relasi_perencanaan']." AND 
		            c.id_jadwal=".$params['relasi_perencanaan']." AND 
					a.active=1 AND
		            c.active=1 AND
					a.id_unik IS NOT NULL AND 
		            c.id_unik IS NOT NULL";
		}else{
			$sql = "
				SELECT DISTINCT
					c.id_unik, 
					c.tujuan_teks
				FROM ".$tableA." a 
					INNER JOIN ".$tableC." c
						ON a.kode_tujuan=c.id_unik
				WHERE  
					a.id_jadwal=".$params['relasi_perencanaan']." AND 
		            c.id_jadwal=".$params['relasi_perencanaan']." AND 
					a.active=1 AND
		            c.active=1 AND
					a.id_unik IS NOT NULL AND 
		            c.id_unik IS NOT NULL";
		}
		// die($sql);
		$sql_data =  $wpdb->get_results($sql);
		return $sql_data;
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

		if(!empty($params['kode_tujuan_rpjm'])){
			$where .= " AND a.kode_tujuan='".$params['kode_tujuan_rpjm']."'";
		}

		switch ($params['id_tipe_relasi']) {
			case '2':
				$tableA = "data_rpjmd_sasaran_lokal_history";
				$tableB = "data_rpjmd_program_lokal_history";
				break;

			case '3':
				$tableA = "data_rpd_sasaran_lokal_history";
				$tableB = "data_rpd_program_lokal_history";
				break;
			
			default:
				throw new Exception("Tipe relasi perencanaan tidak diketahui, harap menghubungi admin", 1);
				break;
		}

		return $wpdb->get_results("
				SELECT DISTINCT
					a.id_unik, 
					a.sasaran_teks
				FROM ".$tableA." a  
				WHERE 
					a.id_jadwal=".$params['relasi_perencanaan']." AND 
					a.active=1 AND
					a.id_unik IS NOT NULL
					$where;");
	}

	public function get_sasaran_parent(){

		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {

					$sasaran = $this->get_sasaran_parent_by_tipe($_POST);

					echo json_encode([
						'status' => true,
						'data' => $sasaran
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
				$sql = $wpdb->prepare("
					SELECT 
						* 
					FROM data_unit
					WHERE id_skpd=%d
						AND active=1 
						AND tahun_anggaran=%d 
					ORDER BY id", $_POST['id_skpd'], get_option('_crb_tahun_anggaran_sipd'));
				$skpd = $wpdb->get_results($sql, ARRAY_A);
				if($_POST['type'] == 1){
					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_tujuan_lokal
						WHERE id_unik IS NOT NULL AND
							id_unit=%d AND
							id_unik_indikator IS NULL AND
							active=1 
						ORDER BY id", $_POST['id_skpd']);
					$tujuan = $wpdb->get_results($sql, ARRAY_A);

					foreach($tujuan as $k => $tuj){
						$sasaran = $wpdb->get_results($wpdb->prepare("
							SELECT 
								id_unik
							from data_renstra_sasaran_lokal 
							where id_unik_indikator IS NULL
								AND active=1
								AND kode_tujuan=%s
						", $tuj['id_unik']), ARRAY_A);
						$kd_all_prog = array();
						foreach($sasaran as $sas){
							$program = $wpdb->get_results($wpdb->prepare("
								SELECT 
									id_unik
								from data_renstra_program_lokal 
								where id_unik_indikator IS NULL
									AND active=1
									AND kode_sasaran=%s
							", $sas['id_unik']), ARRAY_A);
							foreach($program as $prog){
								$kd_all_prog[] = "'".$prog['id_unik']."'";
							}
						}
						$kd_all_prog = implode(',', $kd_all_prog);
						$pagu = $wpdb->get_row($wpdb->prepare("
							SELECT 
								sum(pagu_1) as pagu_akumulasi_1,
								sum(pagu_2) as pagu_akumulasi_2,
								sum(pagu_3) as pagu_akumulasi_3,
								sum(pagu_4) as pagu_akumulasi_4,
								sum(pagu_5) as pagu_akumulasi_5,
								sum(pagu_1_usulan) as pagu_akumulasi_1_usulan,
								sum(pagu_2_usulan) as pagu_akumulasi_2_usulan,
								sum(pagu_3_usulan) as pagu_akumulasi_3_usulan,
								sum(pagu_4_usulan) as pagu_akumulasi_4_usulan,
								sum(pagu_5_usulan) as pagu_akumulasi_5_usulan
							from data_renstra_kegiatan_lokal 
							where id_unik_indikator IS NOT NULL
								AND active=1
								AND kode_program IN ($kd_all_prog)
						"));
						$tujuan[$k]['pagu_akumulasi_1'] = $pagu->pagu_akumulasi_1;
						$tujuan[$k]['pagu_akumulasi_2'] = $pagu->pagu_akumulasi_2;
						$tujuan[$k]['pagu_akumulasi_3'] = $pagu->pagu_akumulasi_3;
						$tujuan[$k]['pagu_akumulasi_4'] = $pagu->pagu_akumulasi_4;
						$tujuan[$k]['pagu_akumulasi_5'] = $pagu->pagu_akumulasi_5;
						$tujuan[$k]['pagu_akumulasi_1_usulan'] = $pagu->pagu_akumulasi_1_usulan;
						$tujuan[$k]['pagu_akumulasi_2_usulan'] = $pagu->pagu_akumulasi_2_usulan;
						$tujuan[$k]['pagu_akumulasi_3_usulan'] = $pagu->pagu_akumulasi_3_usulan;
						$tujuan[$k]['pagu_akumulasi_4_usulan'] = $pagu->pagu_akumulasi_4_usulan;
						$tujuan[$k]['pagu_akumulasi_5_usulan'] = $pagu->pagu_akumulasi_5_usulan;
					}
				}else{

					$tahun_anggaran = $input['tahun_anggaran'];

					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_tujuan
						WHERE tahun_anggaran=%d
							AND active=1
						ORDER BY urut_tujuan
						", $tahun_anggaran);
					$tujuan = $wpdb->get_results($sql, ARRAY_A);
				}

				echo json_encode([
					'status' => true,
					'data' => $tujuan,
					'skpd' => $skpd,
					'message' => 'Sukses get tujuan renstra'
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
					$skpd = $this->get_skpd_db();
					$skpd_db = $skpd['skpd'];
					$bidur_db = $skpd['bidur'];
					
					if(empty($skpd_db)){
						throw new Exception('SKPD untuk user ini tidak ditemukan!');
					}

					if(empty($bidur_db)){
						throw new Exception('Bidang urusan untuk SKPD ini tidak ditemukan!');
					}

					$tujuan = $this->get_tujuan_parent_by_tipe($_POST);

					echo json_encode([
						'status' => true,
						'data' => $tujuan,
						'skpd' => $skpd_db,
						'bidur' => $bidur_db,
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
						$data['kode_sasaran_rpjm'] = $raw_sasaran_parent[0] ?? null;
					}

					$bidur_all = json_decode(stripslashes($data['bidur-all']), true);
					if(empty($bidur_all)){
						throw new Exception('Bidang urusan tidak boleh kosong!');
					}

					$data['id_bidang_urusan'] = $bidur_all['id_bidang_urusan'];
					$data['kode_bidang_urusan'] = $bidur_all['kode_bidang_urusan'];
					$data['nama_bidang_urusan'] = $bidur_all['nama_bidang_urusan'];

					if(empty($data['tujuan_teks'])){
						throw new Exception('Tujuan tidak boleh kosong!');
					}

					if(empty($data['urut_tujuan'])){
						throw new Exception('Urut tujuan tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id 
						FROM data_renstra_tujuan_lokal
						WHERE tujuan_teks=%s
							$where_sasaran_rpjm
							AND id_unik IS NOT NULL
							AND id_unik_indikator IS NULL
							AND active=1
						", trim($data['tujuan_teks'])));
					
					if(!empty($id_cek)){
						throw new Exception('Tujuan : '.$data['tujuan_teks'].' sudah ada!');
					}

					$dataUnit = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_unit 
						WHERE id_unit=%d 
							AND tahun_anggaran=%d 
							AND active=1 
							AND is_skpd=1 
							order by id_skpd ASC
					", $data['id_unit'], get_option('_crb_tahun_anggaran_sipd')));

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
						'catatan_usulan' => $data['catatan_usulan'],
						'catatan' => $data['catatan'],
						'active' => 1
					]);

					if($status === false){
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

	function get_skpd_db(){
		global $wpdb;
		$user_id = um_user( 'ID' );
		$user_meta = get_userdata($user_id);
		$skpd_db = false;
		$bidur_db = false;
		if(in_array("administrator", $user_meta->roles)){
			$skpd_db = $wpdb->get_results($wpdb->prepare("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd,
					bidur_1,
					bidur_2,
					bidur_3,
					is_skpd
				from data_unit 
				where tahun_anggaran=%d
					and is_skpd=1
					and id_skpd=%d
				group by id_skpd", $_POST['tahun_anggaran'], $_POST['id_unit']), ARRAY_A);
		}else if(
			in_array("PLT", $user_meta->roles) 
			|| in_array("PA", $user_meta->roles) 
			|| in_array("KPA", $user_meta->roles)
		){
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
					and is_skpd=1
					and id_skpd=%d
				group by id_skpd", $nipkepala[0], $_POST['tahun_anggaran'], $_POST['id_unit']), ARRAY_A);
		}

		$bidur_penunjang = 'X.XX';
		$bidur_all = array();
		foreach($skpd_db as $i => $data){
			$bidur = explode('.', $data['kode_skpd']);
			$bidur_1 = $bidur[0].'.'.$bidur[1];
			$bidur_2 = $bidur[2].'.'.$bidur[3];
			$bidur_3 = $bidur[4].'.'.$bidur[5];
			if($bidur_1!='0.00'){
				$bidur_all[$bidur_1] = "'".$bidur_1."'";
			}
			if($bidur_2!='0.00'){
				$bidur_all[$bidur_2] = "'".$bidur_2."'";
			}
			if($bidur_3!='0.00'){
				$bidur_all[$bidur_3] = "'".$bidur_3."'";
			}
			$bidur_all[$bidur_penunjang] = "'".$bidur_penunjang."'";
			$skpd_db[$i]['bidur_1'] = $bidur_1;
			$skpd_db[$i]['bidur_2'] = $bidur_2;
			$skpd_db[$i]['bidur_3'] = $bidur_3;
			$skpd_db[$i]['bidur_4'] = $bidur_penunjang;
		}

		$id_bidur_all = implode(',', $bidur_all);
		$sql = $wpdb->prepare("
			SELECT DISTINCT
				id_bidang_urusan,
				id_urusan,
				kode_bidang_urusan,
				kode_urusan,
				nama_bidang_urusan,
				nama_urusan
			from data_prog_keg 
			where kode_bidang_urusan IN ($id_bidur_all)
				and tahun_anggaran=%d
		", $_POST['tahun_anggaran']);
		// die($sql);
		$bidur_db = $wpdb->get_results($sql, ARRAY_A);
		return array(
			'skpd' => $skpd_db,
			'bidur' => $bidur_db
		);
	}

	public function edit_tujuan_renstra(){
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					$skpd = $this->get_skpd_db();
					$skpd_db = $skpd['skpd'];
					$bidur_db = $skpd['bidur'];
					
					if(empty($skpd_db)){
						throw new Exception('SKPD untuk user ini tidak ditemukan!');
					}

					if(empty($bidur_db)){
						throw new Exception('Bidang urusan untuk SKPD ini tidak ditemukan!');
					}

					$tujuan = $wpdb->get_row("SELECT a.* FROM data_renstra_tujuan_lokal a WHERE a.id=".$_POST['id_tujuan']);
					$tujuan_parent_selected = $this->get_tujuan_parent_by_tipe($_POST, $tujuan);
					$tujuan_parent = $this->get_tujuan_parent_by_tipe($_POST);

					echo json_encode([
						'status' => true,
						'tujuan' => $tujuan,
						'tujuan_parent_selected' => $tujuan_parent_selected,
						'tujuan_parent' => $tujuan_parent,
						'skpd' => $skpd_db,
						'bidur' => $bidur_db,
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

	function wpsipd_query($query){
		$query = str_replace( "= 'NULL'", "IS NULL", $query );
		$query = str_replace( "= 'NOT NULL'", "IS NOT NULL", $query );
		return $query; 
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
						$data['kode_sasaran_rpjm'] = $raw_sasaran_parent[0] ?? null;
					}

					if(empty($data['tujuan_teks'])){
						throw new Exception('Tujuan tidak boleh kosong!');
					}

					if(empty($data['urut_tujuan'])){
						throw new Exception('Urut tujuan tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_renstra_tujuan_lokal
						WHERE tujuan_teks=%s
							AND id!=%d 
							$where_sasaran_rpjm
							AND id_unik IS NOT NULL
							AND id_unik_indikator IS NULL
							AND active=1
						", trim($data['tujuan_teks']), $data['id']));
					
					if(!empty($id_cek)){
						throw new Exception('Tujuan : '.$data['tujuan_teks'].' sudah ada!');
					}

					$dataUnit = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_unit 
						WHERE id_unit=%d 
							AND tahun_anggaran=%d 
							AND active=1 
							AND is_skpd=1 
						order by id_skpd ASC
					", $data['id_unit'], get_option('_crb_tahun_anggaran_sipd')));

					if(empty($dataUnit)){
						throw new Exception('Unit kerja tidak ditemukan!');
					}

					try {

						$wpdb->query('START TRANSACTION');
						
						add_filter( 'query', array($this, 'wpsipd_query') );

						// update tujuan
						$wpdb->update('data_renstra_tujuan_lokal', [
							'id_unit' => $dataUnit->id_unit,
							'kode_sasaran_rpjm' => $data['kode_sasaran_rpjm'],
							'kode_skpd' => $dataUnit->kode_skpd,
							'nama_skpd' => $dataUnit->nama_skpd,
							'tujuan_teks' => $data['tujuan_teks'],
							'urut_tujuan' => $data['urut_tujuan'],
							'catatan_usulan' => $data['catatan_usulan'],
							'catatan' => $data['catatan'],
							'update_at' => date('Y-m-d H:i:s')
						], [
							'id_unik' => $data['id_unik'],
							'id_unik_indikator' => 'NULL',
							'status' => 1,
							'active' => 1,
						]);
						
						// update indikator tujuan
						$wpdb->update('data_renstra_tujuan_lokal', [
							'id_unit' => $dataUnit->id_unit,
							'kode_sasaran_rpjm' => $data['kode_sasaran_rpjm'],
							'kode_skpd' => $dataUnit->kode_skpd,
							'nama_skpd' => $dataUnit->nama_skpd,
							'tujuan_teks' => $data['tujuan_teks'],
							'urut_tujuan' => $data['urut_tujuan'],
							'update_at' => date('Y-m-d H:i:s')
						], [
							'id_unik' => $data['id_unik'],
							'status' => 1,
							'id_unik_indikator' => 'NOT NULL',
							'active' => 1,
						]);

    					remove_filter( 'query', array($this, 'wpsipd_query') );

						// update data tujuan di table sasaran dan indikator
						$wpdb->update('data_renstra_sasaran_lokal', [
							'tujuan_lock' => $data['tujuan_lock'],
							'tujuan_teks' => $data['tujuan_teks'],
							'urut_tujuan' => $data['urut_tujuan'],
						], [
							'kode_tujuan' => $data['id_unik']
						]);

						// update data tujuan di table program dan indikator
						$wpdb->update('data_renstra_program_lokal', [
							'tujuan_lock' => $data['tujuan_lock'],
							'tujuan_teks' => $data['tujuan_teks'],
							'urut_tujuan' => $data['urut_tujuan']
						], [
							'kode_tujuan' => $data['id_unik']
						]);

						// update data tujuan di table kegiatan dan indikator
						$wpdb->update('data_renstra_kegiatan_lokal', [
							'tujuan_lock' => $data['tujuan_lock'],
							'tujuan_teks' => $data['tujuan_teks'],
							'urut_tujuan' => $data['urut_tujuan']
						], [
							'kode_tujuan' => $data['id_unik']
						]);
						
						$wpdb->query('COMMIT');

						echo json_encode([
							'status' => true,
							'message' => 'Sukses ubah tujuan renstra',
						]);exit;

					} catch (Exception $e) {

						$wpdb->query('ROLLBACK');
						
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

					
					$id_cek = $wpdb->get_var("SELECT * FROM data_renstra_sasaran_lokal WHERE kode_tujuan='" . $_POST['id_unik'] . "' AND active=1");

					if(!empty($id_cek)){
						throw new Exception("Tujuan sudah digunakan oleh sasaran", 1);
					}

					$id_cek = $wpdb->get_var("SELECT * FROM data_renstra_tujuan_lokal WHERE id_unik='" . $_POST['id_unik']. "' AND id_unik_indikator IS NOT NULL AND active=1");

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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_tujuan_lokal
						WHERE indikator_teks_usulan=%s
							AND id_unik=%s
							AND active=1
					", $data['indikator_teks_usulan'], $data['id_unik']));
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks_usulan'].' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_tujuan_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
					", $data['id_unik']));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$inputs = [
						'id_bidang_urusan' => $dataTujuan->id_bidang_urusan,
						'id_unik' => $dataTujuan->id_unik, // kode_tujuan
						'id_unik_indikator' => $this->generateRandomString(),
						'id_unit' => $dataTujuan->id_unit,
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
					$inputs['indikator_teks_usulan'] = $data['indikator_teks_usulan'];
					$inputs['satuan_usulan'] = $data['satuan_usulan'];
					$inputs['target_1_usulan'] = $data['target_1_usulan'];
					$inputs['target_2_usulan'] = $data['target_2_usulan'];
					$inputs['target_3_usulan'] = $data['target_3_usulan'];
					$inputs['target_4_usulan'] = $data['target_4_usulan'];
					$inputs['target_5_usulan'] = $data['target_5_usulan'];
					$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
					$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
					$inputs['catatan_usulan'] = $data['catatan_usulan'];

					if(in_array('administrator', $this->role())){
						$inputs['indikator_teks'] = !empty($data['indikator_teks']) ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}

					$status = $wpdb->insert('data_renstra_tujuan_lokal', $inputs);

					if($status === false){
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
					
					$indikator = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_tujuan_lokal 
						WHERE id=%d 
							AND id_unik IS NOT NULL 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
					", $_POST['id']));

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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_tujuan_lokal
						WHERE indikator_teks_usulan=%s
							AND id_unik=%s
							AND id!=%d
							AND active=1
					", $data['indikator_teks_usulan'], $data['id_unik'], $data['id']));
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks_usulan'].' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_tujuan_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
					", $data['id_unik']));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					$inputs = [
						'id_bidang_urusan' => $dataTujuan->id_bidang_urusan,
						'id_unik' => $dataTujuan->id_unik, // kode_tujuan
						'id_unit' => $dataTujuan->id_unit,
						'kode_bidang_urusan' => $dataTujuan->kode_bidang_urusan,
						'kode_sasaran_rpjm' => $dataTujuan->kode_sasaran_rpjm,
						'kode_skpd' => $dataTujuan->kode_skpd,
						'nama_bidang_urusan' => $dataTujuan->nama_bidang_urusan,
						'nama_skpd' => $dataTujuan->nama_skpd,
						'tujuan_teks' => $dataTujuan->tujuan_teks,
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'update_at' => date('Y-m-d H:i:s')
					];
					$inputs['indikator_teks_usulan'] = $data['indikator_teks_usulan'];
					$inputs['satuan_usulan'] = $data['satuan_usulan'];
					$inputs['target_1_usulan'] = $data['target_1_usulan'];
					$inputs['target_2_usulan'] = $data['target_2_usulan'];
					$inputs['target_3_usulan'] = $data['target_3_usulan'];
					$inputs['target_4_usulan'] = $data['target_4_usulan'];
					$inputs['target_5_usulan'] = $data['target_5_usulan'];
					$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
					$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
					$inputs['catatan_usulan'] = $data['catatan_usulan'];

					if(in_array('administrator', $this->role())){
						$inputs['indikator_teks'] = !empty($data['indikator_teks']) ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}

					$status = $wpdb->update('data_renstra_tujuan_lokal', $inputs, ['id' => $data['id']]);

					if($status === false){
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
					
					$wpdb->get_results($wpdb->prepare("
						DELETE 
						FROM data_renstra_tujuan_lokal 
						WHERE id=%d 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
					", $_POST['id']));

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

		if(empty($data['indikator_teks_usulan'])){
			throw new Exception('Indikator usulan tujuan tidak boleh kosong!');
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
						WHERE kode_tujuan=%s
							AND id_unik IS NOT NULL
							AND id_unik_indikator IS NULL
							AND active=1
					", $_POST['kode_tujuan']);
					$sasaran = $wpdb->get_results($sql, ARRAY_A);

					foreach($sasaran as $k => $sas){
						$kd_all_prog = array();
						$program = $wpdb->get_results($wpdb->prepare("
							SELECT 
								id_unik
							from data_renstra_program_lokal 
							where id_unik_indikator IS NULL
								AND active=1
								AND kode_sasaran=%s
						", $sas['id_unik']), ARRAY_A);
						foreach($program as $prog){
							$kd_all_prog[] = "'".$prog['id_unik']."'";
						}
						$kd_all_prog = implode(',', $kd_all_prog);
						$pagu = $wpdb->get_row($wpdb->prepare("
							SELECT 
								sum(pagu_1) as pagu_akumulasi_1,
								sum(pagu_2) as pagu_akumulasi_2,
								sum(pagu_3) as pagu_akumulasi_3,
								sum(pagu_4) as pagu_akumulasi_4,
								sum(pagu_5) as pagu_akumulasi_5,
								sum(pagu_1_usulan) as pagu_akumulasi_1_usulan,
								sum(pagu_2_usulan) as pagu_akumulasi_2_usulan,
								sum(pagu_3_usulan) as pagu_akumulasi_3_usulan,
								sum(pagu_4_usulan) as pagu_akumulasi_4_usulan,
								sum(pagu_5_usulan) as pagu_akumulasi_5_usulan
							from data_renstra_kegiatan_lokal 
							where id_unik_indikator IS NOT NULL
								AND active=1
								AND kode_program IN ($kd_all_prog)
						"));
						$sasaran[$k]['pagu_akumulasi_1'] = $pagu->pagu_akumulasi_1;
						$sasaran[$k]['pagu_akumulasi_2'] = $pagu->pagu_akumulasi_2;
						$sasaran[$k]['pagu_akumulasi_3'] = $pagu->pagu_akumulasi_3;
						$sasaran[$k]['pagu_akumulasi_4'] = $pagu->pagu_akumulasi_4;
						$sasaran[$k]['pagu_akumulasi_5'] = $pagu->pagu_akumulasi_5;
						$sasaran[$k]['pagu_akumulasi_1_usulan'] = $pagu->pagu_akumulasi_1_usulan;
						$sasaran[$k]['pagu_akumulasi_2_usulan'] = $pagu->pagu_akumulasi_2_usulan;
						$sasaran[$k]['pagu_akumulasi_3_usulan'] = $pagu->pagu_akumulasi_3_usulan;
						$sasaran[$k]['pagu_akumulasi_4_usulan'] = $pagu->pagu_akumulasi_4_usulan;
						$sasaran[$k]['pagu_akumulasi_5_usulan'] = $pagu->pagu_akumulasi_5_usulan;
					}
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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sasaran_lokal
						WHERE sasaran_teks=%s
							AND kode_tujuan=%s
							AND active=1
						", $data['sasaran_teks'], $data['kode_tujuan']));
					
					if(!empty($id_cek)){
						throw new Exception('Sasaran : '.$data['sasaran_teks'].' sudah ada!');
					}
					
					$dataTujuan = $wpdb->get_row($wpdb->prepare("
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
							id_unik=%s AND 
							active=1 AND 
							id_unik IS NOT NULL AND 
							id_unik_indikator IS NULL
					", $data['kode_tujuan']));

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
						'catatan_usulan' => $data['catatan_usulan'],
						'catatan' => $data['catatan'],
						'urut_tujuan' => $dataTujuan->urut_tujuan,
						'active' => 1
					]);

					if($status === false){
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

				$sasaran = $wpdb->get_row($wpdb->prepare("
					SELECT 
						* 
					FROM data_renstra_sasaran_lokal
					WHERE id=%d
						AND id_unik IS NOT NULL
						AND id_unik_indikator IS NULL
						AND active=1
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

	public function update_sasaran_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_sasaran_renstra($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sasaran_lokal
						WHERE sasaran_teks=%s
							AND kode_tujuan=%s
							AND id_unik!=%s
							AND id_unik_indikator IS NULL
							AND active=1
						", $data['sasaran_teks'], $data['kode_tujuan'], $data['kode_sasaran']));
					
					if(!empty($id_cek)){
						throw new Exception('Sasaran : '.$data['sasaran_teks'].' sudah ada!');
					}
					
					$dataTujuan = $wpdb->get_row($wpdb->prepare("
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
							id_unik=%s AND 
							active=1 AND 
							id_unik IS NOT NULL AND 
						id_unik_indikator IS NULL
					", $data['kode_tujuan']));

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

					// update data sasaran
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
						'catatan_usulan' => $data['catatan_usulan'],
						'catatan' => $data['catatan'],
						'urut_tujuan' => $dataTujuan->urut_tujuan,
					], [
						'id_unik' => $data['kode_sasaran'], // pake id_unik biar teks sasaran di row indikator sasaran ikut terupdate
						'id_unik_indikator' => 'NULL'
					]);

					// update data indikator sasaran
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
						'id_unik' => $data['kode_sasaran'], // pake id_unik biar teks sasaran di row indikator sasaran ikut terupdate
						'id_unik_indikator' => 'NOT NULL'
					]);

					if($status === false){
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
					
					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE kode_sasaran=%s 
							AND active=1
					", $_POST['kode_sasaran']));

					if(!empty($id_cek)){
						throw new Exception("Sasaran sudah digunakan oleh program", 1);
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
					", $_POST['kode_sasaran']));

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
							SELECT 
								* 
							FROM data_renstra_sasaran_lokal 
							WHERE 
								id_unik=%s AND 
								id_unik_indikator IS NOT NULL AND 
								active=1", $_POST['id_unik']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					}else{
						$tahun_anggaran = $_POST['tahun_anggaran'];
						$sql = $wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_sasaran
							WHERE tahun_anggaran=%d AND 
								id_unik=%s AND
								id_unik_indikator IS NOT NULL AND 
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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sasaran_lokal
						WHERE indikator_teks_usulan=%s
							AND id_unik=%s
							AND id_unik_indikator IS NOT NULL
							AND active=1
					", $data['indikator_teks_usulan'], $data['id_unik']));
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks_usulan'].' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE 
							id_unik=%s AND 
							id_unik_indikator IS NULL AND 
							active=1
					", $data['id_unik']));

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

					$inputs['indikator_teks_usulan'] = $data['indikator_teks_usulan'];
					$inputs['satuan_usulan'] = $data['satuan_usulan'];
					$inputs['target_1_usulan'] = $data['target_1_usulan'];
					$inputs['target_2_usulan'] = $data['target_2_usulan'];
					$inputs['target_3_usulan'] = $data['target_3_usulan'];
					$inputs['target_4_usulan'] = $data['target_4_usulan'];
					$inputs['target_5_usulan'] = $data['target_5_usulan'];
					$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
					$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
					$inputs['catatan_usulan'] = $data['catatan_usulan'];

					if(in_array('administrator', $this->role())){
						$inputs['indikator_teks'] = !empty($data['indikator_teks']) ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}

					$status = $wpdb->insert('data_renstra_sasaran_lokal', $inputs);

					if($status === false){
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
					
					$indikator = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE 
							id=%d AND 
							id_unik IS NOT NULL AND 
							id_unik_indikator IS NOT NULL AND 
							active=1
					", $_POST['id']));

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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sasaran_lokal
						WHERE indikator_teks_usulan=%s
							AND id!=%d
							AND active=1
					", $data['indikator_teks_usulan'], $data['id']));
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks_usulan'].' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE 
							id_unik=%s AND 
							id_unik_indikator IS NULL AND 
							active=1
					", $data['id_unik']));

					if (empty($dataSasaran)) {
						throw new Exception('Sasaran yang dipilih tidak ditemukan!');
					}

					$inputs = [
						'id_bidang_urusan' => $dataSasaran->id_bidang_urusan,
						'id_misi' => $dataSasaran->id_misi,
						'id_unik' => $dataSasaran->id_unik,
						'id_unit' => $dataSasaran->id_unit,
						'id_visi' => $dataSasaran->id_visi,
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
					$inputs['indikator_teks_usulan'] = $data['indikator_teks_usulan'];
					$inputs['satuan_usulan'] = $data['satuan_usulan'];
					$inputs['target_1_usulan'] = $data['target_1_usulan'];
					$inputs['target_2_usulan'] = $data['target_2_usulan'];
					$inputs['target_3_usulan'] = $data['target_3_usulan'];
					$inputs['target_4_usulan'] = $data['target_4_usulan'];
					$inputs['target_5_usulan'] = $data['target_5_usulan'];
					$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
					$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
					$inputs['catatan_usulan'] = $data['catatan_usulan'];

					if(in_array('administrator', $this->role())){
						$inputs['indikator_teks'] = !empty($data['indikator_teks']) ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}

					$status = $wpdb->update('data_renstra_sasaran_lokal', $inputs, ['id' => $data['id']]);

					if($status === false){
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
					
					$wpdb->get_results($wpdb->prepare("
						DELETE 
						FROM data_renstra_sasaran_lokal 
						WHERE 
							id=%d AND
							id_unik_indikator IS NOT NULL AND 
							active=1
					", $_POST['id']));

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

		if(empty($data['indikator_teks_usulan'])){
			throw new Exception('Indikator usulan sasaran tidak boleh kosong!');
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
						SELECT 
							* 
						FROM data_renstra_program_lokal
						WHERE kode_sasaran=%s AND
							id_unik IS NOT NULL and
							id_unik_indikator IS NULL and
							active=1 ORDER BY id
					", $_POST['kode_sasaran']);
					$program = $wpdb->get_results($sql, ARRAY_A);

					foreach($program as $k => $prog){
						$pagu = $wpdb->get_row($wpdb->prepare("
							SELECT 
								sum(pagu_1) as pagu_akumulasi_1,
								sum(pagu_2) as pagu_akumulasi_2,
								sum(pagu_3) as pagu_akumulasi_3,
								sum(pagu_4) as pagu_akumulasi_4,
								sum(pagu_5) as pagu_akumulasi_5,
								sum(pagu_1_usulan) as pagu_akumulasi_1_usulan,
								sum(pagu_2_usulan) as pagu_akumulasi_2_usulan,
								sum(pagu_3_usulan) as pagu_akumulasi_3_usulan,
								sum(pagu_4_usulan) as pagu_akumulasi_4_usulan,
								sum(pagu_5_usulan) as pagu_akumulasi_5_usulan
							from data_renstra_kegiatan_lokal 
							where id_unik_indikator IS NOT NULL
								AND active=1
								AND kode_program=%s
						", $prog['id_unik']));
						$program[$k]['pagu_akumulasi_1'] = $pagu->pagu_akumulasi_1;
						$program[$k]['pagu_akumulasi_2'] = $pagu->pagu_akumulasi_2;
						$program[$k]['pagu_akumulasi_3'] = $pagu->pagu_akumulasi_3;
						$program[$k]['pagu_akumulasi_4'] = $pagu->pagu_akumulasi_4;
						$program[$k]['pagu_akumulasi_5'] = $pagu->pagu_akumulasi_5;
						$program[$k]['pagu_akumulasi_1_usulan'] = $pagu->pagu_akumulasi_1_usulan;
						$program[$k]['pagu_akumulasi_2_usulan'] = $pagu->pagu_akumulasi_2_usulan;
						$program[$k]['pagu_akumulasi_3_usulan'] = $pagu->pagu_akumulasi_3_usulan;
						$program[$k]['pagu_akumulasi_4_usulan'] = $pagu->pagu_akumulasi_4_usulan;
						$program[$k]['pagu_akumulasi_5_usulan'] = $pagu->pagu_akumulasi_5_usulan;
					}
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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_program_lokal
						WHERE nama_program=%s
							AND kode_sasaran=%s
							AND active=1
					", trim($data['program_teks']), $data['kode_sasaran']));

					if(!empty($id_cek)){
						throw new Exception('Program : '.$data['program_teks'].' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE id_unik=%s
							AND active=1
					", $data['kode_sasaran']));

					if(empty($dataSasaran)){
						throw new Exception('Sasaran belum dipilih!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program
                        FROM data_prog_keg as u 
                        WHERE u.tahun_anggaran=%d
                        	AND id_program=%d
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ", get_option('_crb_tahun_anggaran_sipd'), $data['id_program']));

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
								'catatan_usulan' => $data['catatan_usulan'],
								'catatan' => $data['catatan'],
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

				$data = $wpdb->get_row($wpdb->prepare("
					SELECT * FROM data_renstra_program_lokal
					WHERE id_unik=%s
						AND id_unik_indikator IS NULL
						AND active=1
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

	public function update_program_renstra(){
		global $wpdb;

		try{
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( '_crb_api_key_extension' )) {
					
					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_program_renstra($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_program_lokal
						WHERE nama_program=%s
							AND id_unik!=%s
							AND active=1
						", trim($data['program_teks']), $data['id_unik']));
					
					if(!empty($id_cek)){
						throw new Exception('Program : '.$data['program_teks'].' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE 
							id_unik=%s AND 
							id_unik_indikator IS NULL AND 
							active=1
					", $data['kode_sasaran']));

					if(empty($dataSasaran)){
						throw new Exception('Sasaran tidak ditemukan!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program
                        FROM data_prog_keg as u 
                        WHERE u.tahun_anggaran=%d 
                        	AND u.id_program=%d
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ", get_option('_crb_tahun_anggaran_sipd'), $data['id_program']));

					if(empty($dataProgram)){
						throw new Exception('Program tidak ditemukan!');
					}

					try {
						// update program
						$wpdb->update('data_renstra_program_lokal', [
							'id_bidang_urusan' => $dataSasaran->id_bidang_urusan,
							'id_misi' => $dataSasaran->id_misi,
							'id_program' => $dataProgram->id_program,
							'id_unit' => $dataSasaran->id_unit,
							'id_visi' => $dataSasaran->id_visi,
							'kode_bidang_urusan' => $dataSasaran->kode_bidang_urusan,
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
							'catatan_usulan' => $data['catatan_usulan'],
							'catatan' => $data['catatan'],
							'update_at' => date('Y-m-d H:i:s')
						], [
							'id_unik' => $data['id_unik'], // pake id_unik biar teks program di row indikator program ikut terupdate
							'id_unik_indikator' => 'NULL'
						]);

						// update indikator program
						$wpdb->update('data_renstra_program_lokal', [
							'id_bidang_urusan' => $dataSasaran->id_bidang_urusan,
							'id_misi' => $dataSasaran->id_misi,
							'id_program' => $dataProgram->id_program,
							'id_unit' => $dataSasaran->id_unit,
							'id_visi' => $dataSasaran->id_visi,
							'kode_bidang_urusan' => $dataSasaran->kode_bidang_urusan,
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
							'id_unik' => $data['id_unik'],
							'id_unik_indikator' => 'NOT NULL'
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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE kode_program=%s 
							AND id_unik IS NOT NULL 
							AND id_unik_indikator IS NULL 
							AND active=1
					", $_POST['kode_program']));

					if(!empty($id_cek)){
						throw new Exception("Program sudah digunakan oleh kegiatan", 1);
					}
					
					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
					", $_POST['kode_program']));

					if(!empty($id_cek)){
						throw new Exception("Program sudah digunakan oleh indikator program", 1);
					}

					$wpdb->get_results($wpdb->prepare("
						DELETE 
						FROM data_renstra_program_lokal 
						WHERE id_unik=%s
					", $_POST['kode_program']));

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

		// tidak digunakan karena data bidang urusan diambil dari tujuan
		return;

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
							SELECT 
								* 
							FROM data_renstra_program_lokal 
							WHERE 
								id_unik=%s AND 
								id_unik_indikator IS NOT NULL AND 
								active=1
						", $_POST['kode_program']);
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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_program_lokal
						WHERE indikator_usulan=%s
							AND id_unik=%s
							AND id_unik_indikator IS NOT NULL
							AND active=1
					", $data['indikator_teks_usulan'], $data['kode_program']));
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks_usulan'].' sudah ada!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
					", $data['kode_program']));

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
					$inputs['indikator_usulan'] = $data['indikator_teks_usulan'];
					$inputs['satuan_usulan'] = $data['satuan_usulan'];
					$inputs['target_1_usulan'] = $data['target_1_usulan'];
					$inputs['target_2_usulan'] = $data['target_2_usulan'];
					$inputs['target_3_usulan'] = $data['target_3_usulan'];
					$inputs['target_4_usulan'] = $data['target_4_usulan'];
					$inputs['target_5_usulan'] = $data['target_5_usulan'];
					$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
					$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
					$inputs['catatan_usulan'] = $data['catatan_usulan'];

					if(in_array('administrator', $this->role())){
						$inputs['indikator'] = !empty($data['indikator_teks']) ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}

					$status = $wpdb->insert('data_renstra_program_lokal', $inputs);

					if($status === false){
						$ket = '';
						if(in_array('administrator', $this->role())){
							$ket = " | query: ".$wpdb->last_query;
						}
						$ket .= " | error: ".$wpdb->last_error;
						throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
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
					
					$indikator = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE id=%d 
							AND id_unik=%s 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
					", $_POST['id'], $_POST['kode_program']));

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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_program_lokal
						WHERE indikator_usulan=%s
							AND id!=%d
							AND id_unik=%s
							AND id_unik_indikator is not null
							AND active=1
					", $data['indikator_teks_usulan'], $data['id'], $data['kode_program']));
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks'].' sudah ada!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik IS NOT NULL 
							AND id_unik_indikator IS NULL
					", $data['kode_program']));

					if (empty($dataProgram)) {
						throw new Exception('Program yang dipilih tidak ditemukan!');
					}

					$inputs = [
						'id_bidang_urusan' => $dataProgram->id_bidang_urusan,
						'id_misi' => $dataProgram->id_misi,
						'id_program' => $dataProgram->id_program,
						'id_unit' => $dataProgram->id_unit,
						'id_visi' => $dataProgram->id_visi,
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
					$inputs['indikator_usulan'] = $data['indikator_teks_usulan'];
					$inputs['satuan_usulan'] = $data['satuan_usulan'];
					$inputs['target_1_usulan'] = $data['target_1_usulan'];
					$inputs['target_2_usulan'] = $data['target_2_usulan'];
					$inputs['target_3_usulan'] = $data['target_3_usulan'];
					$inputs['target_4_usulan'] = $data['target_4_usulan'];
					$inputs['target_5_usulan'] = $data['target_5_usulan'];
					$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
					$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
					$inputs['catatan_usulan'] = $data['catatan_usulan'];

					if(in_array('administrator', $this->role())){
						$inputs['indikator'] = !empty($data['indikator_teks']) ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
						$inputs['satuan'] = !empty($data['satuan']) ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = !empty($data['catatan']) ? $data['catatan'] : $data['catatan_usulan'];
					}

					$status = $wpdb->update('data_renstra_program_lokal', $inputs, ['id' => $data['id']]);

					if($status === false){
						$ket = '';
						if(in_array('administrator', $this->role())){
							$ket = " | query: ".$wpdb->last_query;
						}
						$ket .= " | error: ".$wpdb->last_error;
						throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
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
					
					$wpdb->get_results($wpdb->prepare("
						DELETE 
						FROM data_renstra_program_lokal 
						WHERE id=%d 
							AND id_unik_indikator IS NOT NULL 
							AND active=1 
					", $_POST['id']));

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

		if(empty($data['indikator_teks_usulan'])){
			throw new Exception('Indikator usulan program tidak boleh kosong!');
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
						SELECT 
							k.*
						FROM data_renstra_kegiatan_lokal k
						WHERE k.kode_program=%s AND
							k.id_unik IS NOT NULL and
							k.id_unik_indikator IS NULL and
							k.active=1 ORDER BY id
					", $_POST['kode_program']);
					$kegiatan = $wpdb->get_results($sql, ARRAY_A);
					foreach($kegiatan as $k => $keg){
						$pagu_keg = $wpdb->get_row($wpdb->prepare("
							SELECT 
								sum(pagu_1) as pagu_akumulasi_1,
								sum(pagu_2) as pagu_akumulasi_2,
								sum(pagu_3) as pagu_akumulasi_3,
								sum(pagu_4) as pagu_akumulasi_4,
								sum(pagu_5) as pagu_akumulasi_5,
								sum(pagu_1_usulan) as pagu_akumulasi_1_usulan,
								sum(pagu_2_usulan) as pagu_akumulasi_2_usulan,
								sum(pagu_3_usulan) as pagu_akumulasi_3_usulan,
								sum(pagu_4_usulan) as pagu_akumulasi_4_usulan,
								sum(pagu_5_usulan) as pagu_akumulasi_5_usulan
							from data_renstra_kegiatan_lokal 
							where id_unik_indikator IS NOT NULL
								AND active=1
								AND id_unik=%s
						", $keg['id_unik']));
						$kegiatan[$k]['pagu_akumulasi_1'] = $pagu_keg->pagu_akumulasi_1;
						$kegiatan[$k]['pagu_akumulasi_2'] = $pagu_keg->pagu_akumulasi_2;
						$kegiatan[$k]['pagu_akumulasi_3'] = $pagu_keg->pagu_akumulasi_3;
						$kegiatan[$k]['pagu_akumulasi_4'] = $pagu_keg->pagu_akumulasi_4;
						$kegiatan[$k]['pagu_akumulasi_5'] = $pagu_keg->pagu_akumulasi_5;
						$kegiatan[$k]['pagu_akumulasi_1_usulan'] = $pagu->pagu_akumulasi_1_usulan;
						$kegiatan[$k]['pagu_akumulasi_2_usulan'] = $pagu->pagu_akumulasi_2_usulan;
						$kegiatan[$k]['pagu_akumulasi_3_usulan'] = $pagu->pagu_akumulasi_3_usulan;
						$kegiatan[$k]['pagu_akumulasi_4_usulan'] = $pagu->pagu_akumulasi_4_usulan;
						$kegiatan[$k]['pagu_akumulasi_5_usulan'] = $pagu->pagu_akumulasi_5_usulan;
					}
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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_kegiatan_lokal
						WHERE id_giat=%d
							AND kode_program=%s
							AND active=1
					", $data['id_kegiatan'], $data['kode_program']));

					if(!empty($id_cek)){
						throw new Exception('Kegiatan : '.$data['kegiatan_teks'].' sudah ada! id='.$id_cek);
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NULL 
							AND active=1
					", $data['kode_program']));

					if(empty($dataProgram)){
						throw new Exception('Program tidak ditemukan!');
					}

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_prog_keg 
						WHERE id=%d
					", $data['id_kegiatan']));

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
							'catatan_usulan' => $data['catatan_usulan'],
							'catatan' => $data['catatan'],
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

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal
						WHERE id=%d
					", $_POST['id_kegiatan']));

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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_kegiatan_lokal
						WHERE id_giat=%d
							AND kode_program=%s
							AND id!=%d
							AND active=1
					", $data['id_kegiatan'], $data['kode_program'], $data['id']));

					if(!empty($id_cek)){
						throw new Exception('Kegiatan : '.$data['kegiatan_teks'].' sudah ada!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NULL 
							AND active=1
					", $data['kode_program']));

					if(empty($dataProgram)){
						throw new Exception('Program tidak ditemukan!');
					}

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_prog_keg 
						WHERE id=%d
					", $data['id_kegiatan']));

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
							'catatan_usulan' => $data['catatan_usulan'],
							'catatan' => $data['catatan'],
							'urut_tujuan' => $dataProgram->urut_tujuan
						], [
							'id_unik' => $data['id_unik'], // pake id_unik agar indikator kegiatan ikut terupdate
							'id_unik_indikator' => 'NULL'
						]);

						// edit kegiatan indikator
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
							'id_unik' => $data['id_unik'],
							'id_unik_indikator' => 'NOT NULL'
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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
					", $_POST['id_unik']));

					if(!empty($id_cek)){
						throw new Exception("Kegiatan sudah digunakan oleh indikator kegiatan", 1);
					}
					
					$wpdb->get_results($wpdb->prepare("
						DELETE 
						FROM data_renstra_kegiatan_lokal 
						WHERE id=%d
					", $_POST['id']));

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
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE 
							id_unik=%s AND 
							id_unik_indikator IS NOT NULL AND 
							active=1
						", $_POST['id_unik']);
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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_kegiatan_lokal
						WHERE indikator_usulan=%s
							AND id_unik=%s
							AND id_unik_indikator IS NOT NULL
							AND active=1
					", $data['indikator_teks_usulan'], $data['id_unik']));
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks_usulan'].' sudah ada!');
					}

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
					", $data['id_unik']));

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
					$inputs['indikator_usulan'] = $data['indikator_teks_usulan'];
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

					if(in_array('administrator', $this->role())){
						$inputs['indikator'] = !empty($data['indikator_teks']) ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
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
					}

					$status = $wpdb->insert('data_renstra_kegiatan_lokal', $inputs);

					if($status === false){
						$ket = '';
						if(in_array('administrator', $this->role())){
							$ket = " | query: ".$wpdb->last_query;
						}
						$ket .= " | error: ".$wpdb->last_error;
						throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
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
					
					$indikator = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE id=%d 
							AND id_unik=%s 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
					", $_POST['id'], $_POST['kode_kegiatan']));

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

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_kegiatan_lokal
						WHERE indikator_usulan=%s
							AND id_unik=%s
							AND id!=%d
							AND id_unik_indikator IS NOT NULL
							AND active=1
					", $data['indikator_teks_usulan'], $data['id_unik'], $data['id']));
					
					if(!empty($id_cek)){
						throw new Exception('Indikator : '.$data['indikator_teks_usulan'].' sudah ada!');
					}

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
					", $data['id_unik']));

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
						'is_locked' => $dataKegiatan->is_locked,
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
						'tujuan_lock' => $dataKegiatan->tujuan_lock,
						'tujuan_teks' => $dataKegiatan->tujuan_teks,
						'urut_sasaran' => $dataKegiatan->urut_sasaran,
						'urut_tujuan' => $dataKegiatan->urut_tujuan,
					];
					$inputs['indikator_usulan'] = $data['indikator_teks_usulan'];
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

					if(in_array('administrator', $this->role())){
						$inputs['indikator'] = !empty($data['indikator_teks']) ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
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
					}

					$status = $wpdb->update('data_renstra_kegiatan_lokal', $inputs, ['id' => $data['id']]);

					if($status === false){
						$ket = '';
						if(in_array('administrator', $this->role())){
							$ket = " | query: ".$wpdb->last_query;
						}
						$ket .= " | error: ".$wpdb->last_error;
						throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
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

					$wpdb->get_results($wpdb->prepare("
						DELETE 
						FROM data_renstra_kegiatan_lokal 
						WHERE id=%d
					", $_POST['id']));

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

		if(empty($data['indikator_teks_usulan'])){
			throw new Exception('Indikator usulan kegiatan tidak boleh kosong!');
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

                $join = "";
                $where = "";
                if(!empty($_POST['id_unit'])){
            		if($_POST['type']==1){
            			$join.="
            				LEFT JOIN data_unit as s on s.kode_skpd like CONCAT('%',u.kode_bidang_urusan,'%')
		                        and s.active=1
		                        and s.is_skpd=1
		                        and s.tahun_anggaran=u.tahun_anggaran
		                ";
		                $where .= $wpdb->prepare(" and (s.id_skpd=%d or u.kode_bidang_urusan='X.XX')", $_POST['id_unit']);
            		}
                		
                	if($_POST['relasi_perencanaan'] != '-'){
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
                        	$where
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
                // $ret['sql'] = $wpdb->last_query;
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

    public function copy_usulan_renstra(){
        global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil copy data usulan ke penetapan! Segarkan/refresh halaman ini untuk melihat perubahannya.'
        );
        if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {
            	if(in_array('administrator', $this->role())){
	                $tujuan_all = $wpdb->get_results($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_tujuan_lokal 
						WHERE 
							id_unit=%d AND 
							active=1 ORDER BY urut_tujuan
					", $_POST['id_unit']), ARRAY_A);
            		if(empty($_POST['id_unit'])){
            			$sql = "
							SELECT 
								* 
							FROM data_renstra_tujuan_lokal 
							WHERE active=1 
							ORDER BY urut_tujuan
						";
            		}else{
            			$sql = $wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_tujuan_lokal 
							WHERE 
								id_unit=%d AND 
								active=1 ORDER BY urut_tujuan
						", $_POST['id_unit']);
            		}
	                $tujuan_all = $wpdb->get_results($sql, ARRAY_A);

					foreach ($tujuan_all as $keyTujuan => $tujuan_value) {
						$newData = array(
							'indikator_teks' => $tujuan_value['indikator_teks_usulan'],
							'satuan' => $tujuan_value['satuan_usulan'],
							'target_awal' => $tujuan_value['target_awal_usulan'],
							'target_1' => $tujuan_value['target_1_usulan'],
							'target_2' => $tujuan_value['target_2_usulan'],
							'target_3' => $tujuan_value['target_3_usulan'],
							'target_4' => $tujuan_value['target_4_usulan'],
							'target_5' => $tujuan_value['target_5_usulan'],
							'target_akhir' => $tujuan_value['target_akhir_usulan'],
							'catatan' => $tujuan_value['catatan_usulan']
						);
						$wpdb->update('data_renstra_tujuan_lokal', $newData, array(
							'id' => $tujuan_value['id']
						));
						if(empty($tujuan_value['id_unik_indikator'])){
							$sasaran_all = $wpdb->get_results($wpdb->prepare("
								SELECT 
									* 
								FROM data_renstra_sasaran_lokal 
								WHERE 
									kode_tujuan=%s AND 
									active=1 ORDER BY urut_sasaran
							", $tujuan_value['id_unik']), ARRAY_A);
							foreach ($sasaran_all as $keySasaran => $sasaran_value) {
								$newData = array(
									'indikator_teks' => $sasaran_value['indikator_teks_usulan'],
									'satuan' => $sasaran_value['satuan_usulan'],
									'target_awal' => $sasaran_value['target_awal_usulan'],
									'target_1' => $sasaran_value['target_1_usulan'],
									'target_2' => $sasaran_value['target_2_usulan'],
									'target_3' => $sasaran_value['target_3_usulan'],
									'target_4' => $sasaran_value['target_4_usulan'],
									'target_5' => $sasaran_value['target_5_usulan'],
									'target_akhir' => $sasaran_value['target_akhir_usulan'],
									'catatan' => $sasaran_value['catatan_usulan']
								);
								$wpdb->update('data_renstra_sasaran_lokal', $newData, array(
									'id' => $sasaran_value['id']
								));
								if(empty($sasaran_value['id_unik_indikator'])){
									$program_all = $wpdb->get_results($wpdb->prepare("
										SELECT 
											* 
										FROM data_renstra_program_lokal 
										WHERE 
											kode_sasaran=%s AND 
											kode_tujuan=%s AND 
											active=1 ORDER BY id
									", $sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);
									foreach ($program_all as $keyProgram => $program_value) {
										$newData = array(
											'indikator' => $program_value['indikator_usulan'],
											'satuan' => $program_value['satuan_usulan'],
											'target_awal' => $program_value['target_awal_usulan'],
											'target_1' => $program_value['target_1_usulan'],
											'target_2' => $program_value['target_2_usulan'],
											'target_3' => $program_value['target_3_usulan'],
											'target_4' => $program_value['target_4_usulan'],
											'target_5' => $program_value['target_5_usulan'],
											'target_akhir' => $program_value['target_akhir_usulan'],
											'catatan' => $program_value['catatan_usulan']
										);
										$wpdb->update('data_renstra_program_lokal', $newData, array(
											'id' => $program_value['id']
										));
										if(empty($program_value['id_unik_indikator'])){
											$kegiatan_all = $wpdb->get_results($wpdb->prepare("
												SELECT 
													* 
												FROM data_renstra_kegiatan_lokal 
												WHERE 
													kode_program=%s AND 
													kode_sasaran=%s AND 
													kode_tujuan=%s AND 
													active=1 ORDER BY id
											", $program_value['id_unik'], $sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);
											foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {
												$newData = array(
													'indikator' => $kegiatan_value['indikator_usulan'],
													'satuan' => $kegiatan_value['satuan_usulan'],
													'target_awal' => $kegiatan_value['target_awal_usulan'],
													'target_1' => $kegiatan_value['target_1_usulan'],
													'target_2' => $kegiatan_value['target_2_usulan'],
													'target_3' => $kegiatan_value['target_3_usulan'],
													'target_4' => $kegiatan_value['target_4_usulan'],
													'target_5' => $kegiatan_value['target_5_usulan'],
													'pagu_1' => $kegiatan_value['pagu_1_usulan'],
													'pagu_2' => $kegiatan_value['pagu_2_usulan'],
													'pagu_3' => $kegiatan_value['pagu_3_usulan'],
													'pagu_4' => $kegiatan_value['pagu_4_usulan'],
													'pagu_5' => $kegiatan_value['pagu_5_usulan'],
													'target_akhir' => $kegiatan_value['target_akhir_usulan'],
													'catatan' => $kegiatan_value['catatan_usulan']
												);
												$wpdb->update('data_renstra_kegiatan_lokal', $newData, array(
													'id' => $kegiatan_value['id']
												));
											}
										}
									}
								}
							}
						}
					}
				}else{
					$ret = array(
	                    'status' => 'error',
	                    'message'   => 'Anda tidak punya kewenangan untuk melakukan ini!'
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

            	$nama_pemda = get_option('_crb_daerah');

            	$where_skpd = '';
				if(!empty($_POST['id_unit'])){
					$where_skpd = "and id_skpd=".$_POST['id_unit'];
				}

				$tahun_anggaran=$_POST['tahun_anggaran'] ?? get_option('_crb_tahun_anggaran_sipd');

				$sql = $wpdb->prepare("
					SELECT 
						* 
					FROM data_unit 
					WHERE tahun_anggaran=%d
						".$where_skpd."
						AND active=1
					ORDER BY id_skpd ASC
				", $tahun_anggaran);

				$unit = $wpdb->get_results($sql, ARRAY_A);

				if(empty($unit)){
					die('<h1>Data SKPD dengan id_skpd='.$_POST['id_unit'].' dan tahun_anggaran='.$tahun_anggaran.' tidak ditemukan!</h1>');
				}

				$judul_skpd = '';
				if(!empty($_POST['id_unit'])){
					$judul_skpd = $unit[0]['kode_skpd'].'&nbsp;'.$unit[0]['nama_skpd'].'<br>';
				}

            	$tujuan_all = $wpdb->get_results($wpdb->prepare("
					SELECT 
						* 
					FROM data_renstra_tujuan_lokal 
					WHERE 
						id_unit=%d AND 
						active=1 ORDER BY urut_tujuan
				", $_POST['id_unit']), ARRAY_A);

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
								active=1 ORDER BY urut_sasaran
						", $tujuan_value['id_unik']), ARRAY_A);

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
										active=1 ORDER BY id
								", $sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);

									foreach ($program_all as $keyProgram => $program_value) {
										if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']])){
											$kode = explode(" ", $program_value['nama_program']);
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']] = [
												'id' => $program_value['id'],
												'id_unik' => $program_value['id_unik'],
												'kode' => $kode[0],
												'tujuan_teks' => $program_value['tujuan_teks'],
												'sasaran_teks' => $program_value['sasaran_teks'],
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
												active=1 ORDER BY id
										", $program_value['id_unik'], $sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);

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
							
							if($tujuan_teks!=$program['tujuan_teks']){
								$tujuan_teks=$program['tujuan_teks'];
							}else{
								$tujuan_teks = '';
							}
							
							if($sasaran_teks!=$program['sasaran_teks']){
								$sasaran_teks=$program['sasaran_teks'];
							}else{
								$sasaran_teks = '';
							}

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
									<tr>
										<td class="kiri atas kanan bawah">'.$tujuan_teks.'</td>
										<td class="kiri atas kanan bawah">'.$sasaran_teks.'</td>
										<td class="kiri atas kanan bawah">'.$program['kode'].'</td>
										<td class="kiri atas kanan bawah">'.$program['program_teks'].'</td>
										<td class="kiri atas kanan bawah">'.$indikator_program.'</td>';
										for ($i=1; $i <= $_POST['lama_pelaksanaan']; $i++) { 
											$body.="<td class=\"kiri atas kanan bawah text_tengah\">".$target_arr[$i-1]."</td><td class=\"atas kanan bawah text_kanan\">".$this->_number_format($pagu_arr[$i-1])."</td>";
										}
										$body.='<td class="kiri kiri atas kanan bawah"></td>
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
										<tr class="tr-kegiatan">
											<td class="kiri atas kanan bawah"></td>
											<td class="kiri atas kanan bawah"></td>
											<td class="kiri atas kanan bawah">'.$kegiatan['kode'].'</td>
											<td class="kiri atas kanan bawah">'.$kegiatan['kegiatan_teks'].'</td>
											<td class="kiri atas kanan bawah">'.$indikator_kegiatan.'</td>';
											for ($i=1; $i <= $_POST['lama_pelaksanaan']; $i++) { 
												$body.="<td class=\"kiri atas kanan bawah text_tengah\">".$target_arr[$i-1]."</td><td class=\"atas kanan bawah text_kanan\">".$this->_number_format($pagu_arr[$i-1])."</td>";
											}
											$body.='
											<td class="kiri atas kanan bawah"></td>
										</tr>
								';
							}
						}
					}
				}

				$html='
					<div id="preview">
						<h4 style="text-align: center; margin: 0; font-weight: bold;">RENCANA STRATEGIS (RENSTRA) 
						<br>'.$judul_skpd.'Tahun '.$_POST['awal_renstra'].' - '.$_POST['akhir_renstra'].' '.$nama_pemda.'</h4>
						<table id="table-renstra" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
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
						</table>
					</div>";

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

    public function view_rekap_renstra(){
    	global $wpdb;
        $ret = array(
            'status'    => 'success',
            'message'   => 'Berhasil generate laporan renstra!'
        );
        $data_all = array(
			'data' => array()
		);

		if (!empty($_POST)) {
            if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option( WPSIPD_API_KEY )) {

            	$tahun_anggaran = $_POST['tahun_anggaran'];
				$awal_renstra = $_POST['awal_renstra'];
				$akhir_renstra = $_POST['akhir_renstra'];
				$lama_pelaksanaan = $_POST['lama_pelaksanaan'];

				if(!empty($_POST['relasi_perencanaan'])){
					$relasi = $wpdb->get_row("
									SELECT 
										id_tipe 
									FROM `data_jadwal_lokal`
									WHERE id_jadwal_lokal=".$_POST['relasi_perencanaan']);
					$id_tipe_relasi = $relasi->id_tipe;
				}


				$nama_tipe_relasi = 'RPJMD / RPD';
				switch ($id_tipe_relasi) {
					case '2':
							$nama_tipe_relasi = 'RPJMD';
						break;

					case '3':
							$nama_tipe_relasi = 'RPD';
						break;
				}

				$where_skpd = '';
				if(!empty($_POST['id_unit'])){
					$where_skpd = "and id_skpd=".$_POST['id_unit'];
				}

				$sql = $wpdb->prepare("
					SELECT 
						* 
					FROM data_unit 
					WHERE tahun_anggaran=%d
						".$where_skpd."
						AND active=1
					ORDER BY id_skpd ASC
				", $tahun_anggaran);

				$unit = $wpdb->get_results($sql, ARRAY_A);

				if(empty($unit)){
					die('<h1>Data SKPD dengan id_skpd='.$_POST['id_unit'].' dan tahun_anggaran='.$tahun_anggaran.' tidak ditemukan!</h1>');
				}

				$judul_skpd = '';
				if(!empty($_POST['id_unit'])){
					$judul_skpd = $unit[0]['kode_skpd'].'&nbsp;'.$unit[0]['nama_skpd'].'<br>';
				}
				$nama_pemda = get_option('_crb_daerah');

				$body = '';
				$data_all = array(
					'data' => array()
				);

				$tujuan_all = $wpdb->get_results($wpdb->prepare("
					SELECT 
						* 
					FROM data_renstra_tujuan_lokal 
					WHERE 
						id_unit=%d AND 
						active=1 ORDER BY urut_tujuan
				", $_POST['id_unit']), ARRAY_A);

				// echo '<pre>';print_r($wpdb->last_query);echo '</pre>';die();

				foreach ($tujuan_all as $keyTujuan => $tujuan_value) {
					if(empty($data_all['data'][$tujuan_value['id_unik']])){
						$data_all['data'][$tujuan_value['id_unik']] = [
							'id' => $tujuan_value['id'],
							'id_bidang_urusan' => $tujuan_value['id_bidang_urusan'],
							'id_unik' => $tujuan_value['id_unik'],
							'tujuan_teks' => $tujuan_value['tujuan_teks'],
							'nama_bidang_urusan' => $tujuan_value['nama_bidang_urusan'],
							'urut_tujuan' => $tujuan_value['urut_tujuan'],
							'catatan' => $tujuan_value['catatan'],
							'catatan_usulan' => $tujuan_value['catatan_usulan'],
							'sasaran_rpjm' => '',
							'pagu_akumulasi_1' => 0,
							'pagu_akumulasi_2' => 0,
							'pagu_akumulasi_3' => 0,
							'pagu_akumulasi_4' => 0,
							'pagu_akumulasi_5' => 0,
							'pagu_akumulasi_1_usulan' => 0,
							'pagu_akumulasi_2_usulan' => 0,
							'pagu_akumulasi_3_usulan' => 0,
							'pagu_akumulasi_4_usulan' => 0,
							'pagu_akumulasi_5_usulan' => 0,
							'indikator' => array(),
							'data' => array(),
							'status_rpjm' => false
						];

						if(!empty($tujuan_value['kode_sasaran_rpjm']) && $relasi_perencanaan != '-'){
							$table = 'data_rpjmd_sasaran_lokal';
							switch ($id_tipe_relasi) {
								case '2':
										$table = 'data_rpjmd_sasaran_lokal_history';
									break;

								case '3':
										$table = 'data_rpd_sasaran_lokal_history';
									break;
							}

							$sasaran_rpjm = $wpdb->get_var("
								SELECT DISTINCT
									sasaran_teks
								FROM ".$table." 
								WHERE id_unik='{$tujuan_value['kode_sasaran_rpjm']}'
									AND active=1
							");
							if(!empty($sasaran_rpjm)){
								$data_all['data'][$tujuan_value['id_unik']]['status_rpjm'] = true;
								$data_all['data'][$tujuan_value['id_unik']]['sasaran_rpjm'] = $sasaran_rpjm;
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
								'catatan_indikator' => $tujuan_value['catatan'],
								'indikator_teks_usulan' => $tujuan_value['indikator_teks_usulan'],
								'satuan_usulan' => $tujuan_value['satuan_usulan'],
								'target_1_usulan' => $tujuan_value['target_1_usulan'],
								'target_2_usulan' => $tujuan_value['target_2_usulan'],
								'target_3_usulan' => $tujuan_value['target_3_usulan'],
								'target_4_usulan' => $tujuan_value['target_4_usulan'],
								'target_5_usulan' => $tujuan_value['target_5_usulan'],
								'target_awal_usulan' => $tujuan_value['target_awal_usulan'],
								'target_akhir_usulan' => $tujuan_value['target_akhir_usulan'],
								'catatan_indikator_usulan' => $tujuan_value['catatan_usulan']
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
								active=1 ORDER BY urut_sasaran
						", $tujuan_value['id_unik']), ARRAY_A);

						foreach ($sasaran_all as $keySasaran => $sasaran_value) {
							if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']])){
								$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']] = [
									'id' => $sasaran_value['id'],
									'id_unik' => $sasaran_value['id_unik'],
									'sasaran_teks' => $sasaran_value['sasaran_teks'],
									'urut_sasaran' => $sasaran_value['urut_sasaran'],
									'catatan' => $sasaran_value['catatan'],
									'catatan_usulan' => $sasaran_value['catatan_usulan'],
									'pagu_akumulasi_1' => 0,
									'pagu_akumulasi_2' => 0,
									'pagu_akumulasi_3' => 0,
									'pagu_akumulasi_4' => 0,
									'pagu_akumulasi_5' => 0,
									'pagu_akumulasi_1_usulan' => 0,
									'pagu_akumulasi_2_usulan' => 0,
									'pagu_akumulasi_3_usulan' => 0,
									'pagu_akumulasi_4_usulan' => 0,
									'pagu_akumulasi_5_usulan' => 0,
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
										'catatan_indikator' => $sasaran_value['catatan'],
										'indikator_teks_usulan' => $sasaran_value['indikator_teks_usulan'],
										'satuan_usulan' => $sasaran_value['satuan_usulan'],
										'target_1_usulan' => $sasaran_value['target_1_usulan'],
										'target_2_usulan' => $sasaran_value['target_2_usulan'],
										'target_3_usulan' => $sasaran_value['target_3_usulan'],
										'target_4_usulan' => $sasaran_value['target_4_usulan'],
										'target_5_usulan' => $sasaran_value['target_5_usulan'],
										'target_awal_usulan' => $sasaran_value['target_awal_usulan'],
										'target_akhir_usulan' => $sasaran_value['target_akhir_usulan'],
										'catatan_indikator_usulan' => $sasaran_value['catatan_usulan']
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
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']] = [
												'id' => $program_value['id'],
												'id_unik' => $program_value['id_unik'],
												'program_teks' => $program_value['nama_program'],
												'catatan' => $program_value['catatan'],
												'catatan_usulan' => $program_value['catatan_usulan'],
												'pagu_akumulasi_1' => 0,
												'pagu_akumulasi_2' => 0,
												'pagu_akumulasi_3' => 0,
												'pagu_akumulasi_4' => 0,
												'pagu_akumulasi_5' => 0,
												'pagu_akumulasi_1_usulan' => 0,
												'pagu_akumulasi_2_usulan' => 0,
												'pagu_akumulasi_3_usulan' => 0,
												'pagu_akumulasi_4_usulan' => 0,
												'pagu_akumulasi_5_usulan' => 0,
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
												'catatan_indikator' => $program_value['catatan'],
												'indikator_teks_usulan' => $program_value['indikator_usulan'],
												'satuan_usulan' => $program_value['satuan_usulan'],
												'target_1_usulan' => $program_value['target_1_usulan'],
												'pagu_1_usulan' => $program_value['pagu_1_usulan'],
												'target_2_usulan' => $program_value['target_2_usulan'],
												'pagu_2_usulan' => $program_value['pagu_2_usulan'],
												'target_3_usulan' => $program_value['target_3_usulan'],
												'pagu_3_usulan' => $program_value['pagu_3_usulan'],
												'target_4_usulan' => $program_value['target_4_usulan'],
												'pagu_4_usulan' => $program_value['pagu_4_usulan'],
												'target_5_usulan' => $program_value['target_5_usulan'],
												'pagu_5_usulan' => $program_value['pagu_5_usulan'],
												'target_awal_usulan' => $program_value['target_awal_usulan'],
												'target_akhir_usulan' => $program_value['target_akhir_usulan'],
												'catatan_indikator_usulan' => $program_value['catatan_usulan']
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
												$tujuan_value['id_unik']
											), ARRAY_A);

										foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {
														
											if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']])){

												$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']] = [
													'id' => $kegiatan_value['id'],
													'id_unik' => $kegiatan_value['id_unik'],
													'kegiatan_teks' => $kegiatan_value['nama_giat'],
													'catatan' => $kegiatan_value['catatan'],
													'catatan_usulan' => $kegiatan_value['catatan_usulan'],
													'pagu_akumulasi_1' => 0,
													'pagu_akumulasi_2' => 0,
													'pagu_akumulasi_3' => 0,
													'pagu_akumulasi_4' => 0,
													'pagu_akumulasi_5' => 0,
													'pagu_akumulasi_1_usulan' => 0,
													'pagu_akumulasi_2_usulan' => 0,
													'pagu_akumulasi_3_usulan' => 0,
													'pagu_akumulasi_4_usulan' => 0,
													'pagu_akumulasi_5_usulan' => 0,
													'indikator' => array()
												];
											}
											$kegiatan_ids[$kegiatan_value['id_unik']] = "'".$kegiatan_value['id_unik']."'";

											if(!empty($kegiatan_value['id_unik_indikator'])){
												if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])){

													$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_1'] += $kegiatan_value['pagu_1'];
													$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_2'] += $kegiatan_value['pagu_2'];
													$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_3'] += $kegiatan_value['pagu_3'];
													$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_4'] += $kegiatan_value['pagu_4'];
													$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_5'] += $kegiatan_value['pagu_5'];
													$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_1_usulan'] += $kegiatan_value['pagu_1_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_2_usulan'] += $kegiatan_value['pagu_2_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_3_usulan'] += $kegiatan_value['pagu_3_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_4_usulan'] += $kegiatan_value['pagu_4_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_5_usulan'] += $kegiatan_value['pagu_5_usulan'];

													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_1'] += $kegiatan_value['pagu_1'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_2'] += $kegiatan_value['pagu_2'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_3'] += $kegiatan_value['pagu_3'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_4'] += $kegiatan_value['pagu_4'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_5'] += $kegiatan_value['pagu_5'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_1_usulan'] += $kegiatan_value['pagu_1_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_2_usulan'] += $kegiatan_value['pagu_2_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_3_usulan'] += $kegiatan_value['pagu_3_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_4_usulan'] += $kegiatan_value['pagu_4_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_5_usulan'] += $kegiatan_value['pagu_5_usulan'];
													
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_1'] += $kegiatan_value['pagu_1'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_2'] += $kegiatan_value['pagu_2'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_3'] += $kegiatan_value['pagu_3'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_4'] += $kegiatan_value['pagu_4'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_5'] += $kegiatan_value['pagu_5'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_1_usulan'] += $kegiatan_value['pagu_1_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_2_usulan'] += $kegiatan_value['pagu_2_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_3_usulan'] += $kegiatan_value['pagu_3_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_4_usulan'] += $kegiatan_value['pagu_4_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_5_usulan'] += $kegiatan_value['pagu_5_usulan'];

													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1'] += $kegiatan_value['pagu_1'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2'] += $kegiatan_value['pagu_2'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3'] += $kegiatan_value['pagu_3'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4'] += $kegiatan_value['pagu_4'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5'] += $kegiatan_value['pagu_5'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1_usulan'] += $kegiatan_value['pagu_1_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2_usulan'] += $kegiatan_value['pagu_2_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3_usulan'] += $kegiatan_value['pagu_3_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4_usulan'] += $kegiatan_value['pagu_4_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5_usulan'] += $kegiatan_value['pagu_5_usulan'];

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
														'catatan_indikator' => $kegiatan_value['catatan'],
														'indikator_teks_usulan' => $kegiatan_value['indikator_usulan'],
														'satuan_usulan' => $kegiatan_value['satuan_usulan'],
														'target_1_usulan' => $kegiatan_value['target_1_usulan'],
														'pagu_1_usulan' => $kegiatan_value['pagu_1_usulan'],
														'target_2_usulan' => $kegiatan_value['target_2_usulan'],
														'pagu_2_usulan' => $kegiatan_value['pagu_2_usulan'],
														'target_3_usulan' => $kegiatan_value['target_3_usulan'],
														'pagu_3_usulan' => $kegiatan_value['pagu_3_usulan'],
														'target_4_usulan' => $kegiatan_value['target_4_usulan'],
														'pagu_4_usulan' => $kegiatan_value['pagu_4_usulan'],
														'target_5_usulan' => $kegiatan_value['target_5_usulan'],
														'pagu_5_usulan' => $kegiatan_value['pagu_5_usulan'],
														'target_awal_usulan' => $kegiatan_value['target_awal_usulan'],
														'target_akhir_usulan' => $kegiatan_value['target_akhir_usulan'],
														'catatan_indikator_usulan' => $kegiatan_value['catatan_usulan']
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
					$catatan_indikator = '';
					$indikator_tujuan_usulan = '';
					$target_awal_usulan = '';
					$target_1_usulan = '';
					$target_2_usulan = '';
					$target_3_usulan = '';
					$target_4_usulan = '';
					$target_5_usulan = '';
					$target_akhir_usulan = '';
					$satuan_usulan = '';
					$catatan_indikator_usulan = '';

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
						$catatan_indikator .= '<div class="indikator">'.$indikator['catatan_indikator'].'</div>';
						$indikator_tujuan_usulan .= '<div class="indikator">'.$indikator['indikator_teks_usulan'].'</div>';
						$target_awal_usulan .= '<div class="indikator">'.$indikator['target_awal_usulan'].'</div>';
						$target_1_usulan .= '<div class="indikator">'.$indikator['target_1_usulan'].'</div>';
						$target_2_usulan .= '<div class="indikator">'.$indikator['target_2_usulan'].'</div>';
						$target_3_usulan .= '<div class="indikator">'.$indikator['target_3_usulan'].'</div>';
						$target_4_usulan .= '<div class="indikator">'.$indikator['target_4_usulan'].'</div>';
						$target_5_usulan .= '<div class="indikator">'.$indikator['target_5_usulan'].'</div>';
						$target_akhir_usulan .= '<div class="indikator">'.$indikator['target_akhir_usulan'].'</div>';
						$satuan_usulan .= '<div class="indikator">'.$indikator['satuan_usulan'].'</div>';
						$catatan_indikator_usulan .= '<div class="indikator">'.$indikator['catatan_indikator_usulan'].'</div>';
					}

					$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
					$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
					$sasaran_rpjm = '';
					if(!empty($tujuan['sasaran_rpjm'])){
						$sasaran_rpjm = $tujuan['sasaran_rpjm'];
					}
					$body .= '
							<tr class="tr-tujuan">
								<td class="kiri atas kanan bawah'.$bg_rpjm.'">'.$no_tujuan.'</td>
								<td class="atas kanan bawah'.$bg_rpjm.'">'.$sasaran_rpjm.'</td>
								<td class="atas kanan bawah">'.$tujuan['nama_bidang_urusan'].'</td>
								<td class="atas kanan bawah">'.$tujuan['tujuan_teks'].'</td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah">'.$indikator_tujuan.'</td>
								<td class="atas kanan bawah text_tengah">'.$target_awal.'</td>';
								for ($i=0; $i < $lama_pelaksanaan; $i++) { 
									$body.="<td class=\"atas kanan bawah text_tengah\">".$target_arr[$i]."</td><td class=\"atas kanan bawah text_kanan\"><b>(".$this->_number_format($tujuan['pagu_akumulasi_'.($i+1)]).")</b></td>";
								}
								$body.='<td class="atas kanan bawah text_tengah">'.$target_akhir.'</td>
								<td class="atas kanan bawah">'.$satuan.'</td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah text_tengah">'.$tujuan['urut_tujuan'].'</td>
								<td class="atas kanan bawah">'.$tujuan['catatan'].'</td>
								<td class="atas kanan bawah">'.$catatan_indikator.'</td>
								<td class="atas kanan bawah td-usulan">'.$indikator_tujuan_usulan.'</td>
								<td class="atas kanan bawah text_tengah td-usulan">'.$target_awal_usulan.'</td>';
								for ($i=0; $i < $lama_pelaksanaan; $i++) { 
									$body.="<td class=\"atas kanan bawah text_tengah td-usulan\">".$target_arr_usulan[$i]."</td><td class=\"atas kanan bawah text_kanan td-usulan\"><b>(".$this->_number_format($tujuan['pagu_akumulasi_'.($i+1).'_usulan']).")</b></td>";
								}
								$body.='<td class="atas kanan bawah text_tengah td-usulan">'.$target_akhir_usulan.'</td>
								<td class="atas kanan bawah text_tengah td-usulan">'.$satuan_usulan.'</td>
								<td class="atas kanan bawah td-usulan">'.$tujuan['catatan_usulan'].'</td>
								<td class="atas kanan bawah td-usulan">'.$catatan_indikator_usulan.'</td>
							</tr>
					';

					$no_sasaran=0;
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
						$catatan_indikator = '';
						$indikator_sasaran_usulan = '';
						$target_awal_usulan = '';
						$target_1_usulan = '';
						$target_2_usulan = '';
						$target_3_usulan = '';
						$target_4_usulan = '';
						$target_5_usulan = '';
						$target_akhir_usulan = '';
						$satuan_usulan = '';
						$catatan_indikator_usulan = '';
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
							$catatan_indikator .= '<div class="indikator">'.$indikator['catatan_indikator'].'</div>';
							$indikator_sasaran_usulan .= '<div class="indikator">'.$indikator['indikator_teks_usulan'].'</div>';
							$target_awal_usulan .= '<div class="indikator">'.$indikator['target_awal_usulan'].'</div>';
							$target_1_usulan .= '<div class="indikator">'.$indikator['target_1_usulan'].'</div>';
							$target_2_usulan .= '<div class="indikator">'.$indikator['target_2_usulan'].'</div>';
							$target_3_usulan .= '<div class="indikator">'.$indikator['target_3_usulan'].'</div>';
							$target_4_usulan .= '<div class="indikator">'.$indikator['target_4_usulan'].'</div>';
							$target_5_usulan .= '<div class="indikator">'.$indikator['target_5_usulan'].'</div>';
							$target_akhir_usulan .= '<div class="indikator">'.$indikator['target_akhir_usulan'].'</div>';
							$satuan_usulan .= '<div class="indikator">'.$indikator['satuan_usulan'].'</div>';
							$catatan_indikator_usulan .= '<div class="indikator">'.$indikator['catatan_indikator_usulan'].'</div>';
						}

						$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
						$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
						$body .= '
								<tr class="tr-sasaran">
									<td class="kiri atas kanan bawah'.$bg_rpjm.'">'.$no_tujuan.".".$no_sasaran.'</td>
									<td class="atas kanan bawah'.$bg_rpjm.'"></td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah">'.$sasaran['sasaran_teks'].'</td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah">'.$indikator_sasaran.'</td>
									<td class="atas kanan bawah text_tengah">'.$target_awal.'</td>';
									for ($i=0; $i < $lama_pelaksanaan; $i++) { 
										$body.="<td class=\"atas kanan bawah text_tengah\">".$target_arr[$i]."</td><td class=\"atas kanan bawah text_kanan\"><b>(".$this->_number_format($sasaran['pagu_akumulasi_'.($i+1)]).")</b></td>";
									}
									$body.='<td class="atas kanan bawah text_tengah">'.$target_akhir.'</td>
									<td class="atas kanan bawah">'.$satuan.'</td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah text_tengah">'.$sasaran['urut_sasaran'].'</td>
									<td class="atas kanan bawah">'.$sasaran['catatan'].'</td>
									<td class="atas kanan bawah">'.$catatan_indikator.'</td>
									<td class="atas kanan bawah td-usulan">'.$indikator_sasaran_usulan.'</td>
									<td class="atas kanan bawah text_tengah td-usulan">'.$target_awal_usulan.'</td>';
									for ($i=0; $i < $lama_pelaksanaan; $i++) { 
										$body.="<td class=\"atas kanan bawah text_tengah td-usulan\">".$target_arr_usulan[$i]."</td><td class=\"atas kanan bawah text_kanan td-usulan\"><b>(".$this->_number_format($sasaran['pagu_akumulasi_'.($i+1).'_usulan']).")</b></td>";
									}
									$body.='<td class="atas kanan bawah text_tengah td-usulan">'.$target_akhir_usulan.'</td>
									<td class="atas kanan bawah td-usulan">'.$satuan_usulan.'</td>
									<td class="atas kanan bawah td-usulan">'.$sasaran['catatan_usulan'].'</td>
									<td class="atas kanan bawah td-usulan">'.$catatan_indikator_usulan.'</td>
								</tr>
						';
						
						$no_program=0;
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
							$catatan_indikator = '';
							$indikator_program_usulan = '';
							$target_awal_usulan = '';
							$target_1_usulan = '';
							$pagu_1_usulan   = '';
							$target_2_usulan = '';
							$pagu_2_usulan   = '';
							$target_3_usulan = '';
							$pagu_3_usulan   = '';
							$target_4_usulan = '';
							$pagu_4_usulan   = '';
							$target_5_usulan = '';
							$pagu_5_usulan   = '';
							$target_akhir_usulan = '';
							$satuan_usulan = '';
							$catatan_indikator_usulan = '';
							foreach($program['indikator'] as $key => $indikator){
								$indikator_program .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
								$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
								$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
								$pagu_1 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_1']).'</div>';
								$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
								$pagu_2 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_2']).'</div>';
								$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
								$pagu_3 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_3']).'</div>';
								$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
								$pagu_4 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_4']).'</div>';
								$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
								$pagu_5 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_5']).'</div>';
								$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
								$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
								$catatan_indikator .= '<div class="indikator">'.$indikator['catatan_indikator'].'</div>';
								$indikator_program_usulan .= '<div class="indikator">'.$indikator['indikator_teks_usulan'].'</div>';
								$target_awal_usulan .= '<div class="indikator">'.$indikator['target_awal_usulan'].'</div>';
								$target_1_usulan .= '<div class="indikator">'.$indikator['target_1_usulan'].'</div>';
								$pagu_1_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_1_usulan']).'</div>';
								$target_2_usulan .= '<div class="indikator">'.$indikator['target_2_usulan'].'</div>';
								$pagu_2_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_2_usulan']).'</div>';
								$target_3_usulan .= '<div class="indikator">'.$indikator['target_3_usulan'].'</div>';
								$pagu_3_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_3_usulan']).'</div>';
								$target_4_usulan .= '<div class="indikator">'.$indikator['target_4_usulan'].'</div>';
								$pagu_4_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_4_usulan']).'</div>';
								$target_5_usulan .= '<div class="indikator">'.$indikator['target_5_usulan'].'</div>';
								$pagu_5_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_5_usulan']).'</div>';
								$target_akhir_usulan .= '<div class="indikator">'.$indikator['target_akhir_usulan'].'</div>';
								$satuan_usulan .= '<div class="indikator">'.$indikator['satuan_usulan'].'</div>';
								$catatan_indikator_usulan .= '<div class="indikator">'.$indikator['catatan_indikator_usulan'].'</div>';
							}

							$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
							$pagu_arr = [$pagu_1, $pagu_2, $pagu_3, $pagu_4, $pagu_5];
							$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
							$pagu_arr_usulan = [$pagu_1_usulan, $pagu_2_usulan, $pagu_3_usulan, $pagu_4_usulan, $pagu_5_usulan];
							$body .= '
									<tr class="tr-program">
										<td class="kiri atas kanan bawah'.$bg_rpjm.'">'.$no_tujuan.".".$no_sasaran.".".$no_program.'</td>
										<td class="atas kanan bawah'.$bg_rpjm.'"></td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah">'.$program['program_teks'].'</td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah"><br>'.$indikator_program.'</td>
										<td class="atas kanan bawah text_tengah"><br>'.$target_awal.'</td>';
										for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
											$body.="<td class=\"atas kanan bawah text_tengah\"><br>".$target_arr[$i-1]."</td><td class=\"atas kanan bawah text_kanan\"><b>(".$this->_number_format($program['pagu_akumulasi_'.($i+1)]).")</b><br>".$pagu_arr[$i-1]."</td>";
										}
										$body.='<td class="atas kanan bawah text_tengah"><br>'.$target_akhir.'</td>
										<td class="atas kanan bawah"><br>'.$satuan.'</td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah">'.$program['catatan'].'</td>
										<td class="atas kanan bawah"><br>'.$catatan_indikator.'</td>
										<td class="atas kanan bawah td-usulan"><br>'.$indikator_program_usulan.'</td>
										<td class="atas kanan bawah text_tengah td-usulan"><br>'.$target_awal_usulan.'</td>';
										for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
											$body.="<td class=\"atas kanan bawah text_tengah td-usulan\"><br>".$target_arr_usulan[$i-1]."</td><td class=\"atas kanan bawah text_kanan td-usulan\"><b>(".$this->_number_format($program['pagu_akumulasi_'.($i+1).'_usulan']).")</b><br>".$pagu_arr_usulan[$i-1]."</td>";
										}
										$body.='<td class="atas kanan bawah text_tengah td-usulan"><br>'.$target_akhir_usulan.'</td>
										<td class="atas kanan bawah td-usulan"><br>'.$satuan_usulan.'</td>
										<td class="atas kanan bawah td-usulan">'.$program['catatan_usulan'].'</td>
										<td class="atas kanan bawah td-usulan"><br>'.$catatan_indikator_usulan.'</td>
									</tr>
							';
							
							$no_kegiatan=0;
							foreach ($program['data'] as $kegiatan) {
								$no_kegiatan++;
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
								$catatan_indikator = '';
								$indikator_kegiatan_usulan = '';
								$target_awal_usulan = '';
								$target_1_usulan = '';
								$pagu_1_usulan   = '';
								$target_2_usulan = '';
								$pagu_2_usulan   = '';
								$target_3_usulan = '';
								$pagu_3_usulan   = '';
								$target_4_usulan = '';
								$pagu_4_usulan   = '';
								$target_5_usulan = '';
								$pagu_5_usulan   = '';
								$target_akhir_usulan = '';
								$satuan_usulan = '';
								$catatan_indikator_usulan = '';
								foreach($kegiatan['indikator'] as $key => $indikator){
									$indikator_kegiatan .= '<div class="indikator">'.$indikator['indikator_teks'].'</div>';
									$target_awal .= '<div class="indikator">'.$indikator['target_awal'].'</div>';
									$target_1 .= '<div class="indikator">'.$indikator['target_1'].'</div>';
									$pagu_1 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_1']).'</div>';
									$target_2 .= '<div class="indikator">'.$indikator['target_2'].'</div>';
									$pagu_2 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_2']).'</div>';
									$target_3 .= '<div class="indikator">'.$indikator['target_3'].'</div>';
									$pagu_3 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_3']).'</div>';
									$target_4 .= '<div class="indikator">'.$indikator['target_4'].'</div>';
									$pagu_4 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_4']).'</div>';
									$target_5 .= '<div class="indikator">'.$indikator['target_5'].'</div>';
									$pagu_5 .= '<div class="indikator">'.$this->_number_format($indikator['pagu_5']).'</div>';
									$target_akhir .= '<div class="indikator">'.$indikator['target_akhir'].'</div>';
									$satuan .= '<div class="indikator">'.$indikator['satuan'].'</div>';
									$catatan_indikator .= '<div class="indikator">'.$indikator['catatan_indikator'].'</div>';
									$indikator_kegiatan_usulan .= '<div class="indikator">'.$indikator['indikator_teks_usulan'].'</div>';
									$target_awal_usulan .= '<div class="indikator">'.$indikator['target_awal_usulan'].'</div>';
									$target_1_usulan .= '<div class="indikator">'.$indikator['target_1_usulan'].'</div>';
									$pagu_1_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_1_usulan']).'</div>';
									$target_2_usulan .= '<div class="indikator">'.$indikator['target_2_usulan'].'</div>';
									$pagu_2_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_2_usulan']).'</div>';
									$target_3_usulan .= '<div class="indikator">'.$indikator['target_3_usulan'].'</div>';
									$pagu_3_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_3_usulan']).'</div>';
									$target_4_usulan .= '<div class="indikator">'.$indikator['target_4_usulan'].'</div>';
									$pagu_4_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_4_usulan']).'</div>';
									$target_5_usulan .= '<div class="indikator">'.$indikator['target_5_usulan'].'</div>';
									$pagu_5_usulan .= '<div class="indikator">'.$this->_number_format($indikator['pagu_5_usulan']).'</div>';
									$target_akhir_usulan .= '<div class="indikator">'.$indikator['target_akhir_usulan'].'</div>';
									$satuan_usulan .= '<div class="indikator">'.$indikator['satuan_usulan'].'</div>';
									$catatan_indikator_usulan .= '<div class="indikator">'.$indikator['catatan_indikator_usulan'].'</div>';
								}

								$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
								$pagu_arr = [$pagu_1, $pagu_2, $pagu_3, $pagu_4, $pagu_5];
								$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
								$pagu_arr_usulan = [$pagu_1_usulan, $pagu_2_usulan, $pagu_3_usulan, $pagu_4_usulan, $pagu_5_usulan];
								$body .= '
										<tr class="tr-kegiatan">
											<td class="kiri atas kanan bawah'.$bg_rpjm.'">'.$no_tujuan.".".$no_sasaran.".".$no_program.".".$no_kegiatan.'</td>
											<td class="atas kanan bawah'.$bg_rpjm.'"></td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah">'.$kegiatan['kegiatan_teks'].'</td>
											<td class="atas kanan bawah"><br>'.$indikator_kegiatan.'</td>
											<td class="atas kanan bawah text_tengah"><br>'.$target_awal.'</td>';
											for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
												$body.="<td class=\"atas kanan bawah text_tengah\"><br>".$target_arr[$i-1]."</td><td class=\"atas kanan bawah text_kanan\"><b>(".$this->_number_format($kegiatan['pagu_akumulasi_'.($i+1)]).")</b><br>".$pagu_arr[$i-1]."</td>";
											}
											$body.='
											<td class="atas kanan bawah text_tengah"><br>'.$target_akhir.'</td>
											<td class="atas kanan bawah"><br>'.$satuan.'</td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah">'.$kegiatan['catatan'].'</td>
											<td class="atas kanan bawah"><br>'.$catatan_indikator.'</td>
											<td class="atas kanan bawah td-usulan"><br>'.$indikator_kegiatan_usulan.'</td>
											<td class="atas kanan bawah text_tengah td-usulan"><br>'.$target_awal_usulan.'</td>';
											for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
												$body.="<td class=\"atas kanan bawah text_tengah td-usulan\"><br>".$target_arr_usulan[$i-1]."</td><td class=\"atas kanan bawah text_kanan td-usulan\"><b>(".$this->_number_format($kegiatan['pagu_akumulasi_'.($i+1).'_usulan']).")</b><br>".$pagu_arr_usulan[$i-1]."</td>";
											}
											$body.='
											<td class="atas kanan bawah text_tengah td-usulan"><br>'.$target_akhir_usulan.'</td>
											<td class="atas kanan bawah td-usulan"><br>'.$satuan_usulan.'</td>
											<td class="atas kanan bawah td-usulan"><br>'.$kegiatan['catatan_usulan'].'</td>
											<td class="atas kanan bawah td-usulan">'.$catatan_indikator_usulan.'</td>
										</tr>
								';
							}
						}
					}
				}

				$html='<div id="cetak" style="padding: 5px; overflow: auto; height: 80vh;">
						<table id="table-renstra" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
							<thead><tr>
									<th style="width: 85px;" class="row_head_1 atas kiri kanan bawah text_tengah text_blok">No</th>
									<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Sasaran '.$nama_tipe_relasi.'</th>
									<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Bidang Urusan</th>
									<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Tujuan</th>
									<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Sasaran</th>
									<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Program</th>
									<th style="width: 200px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Kegiatan</th>
									<th style="width: 400px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Indikator</th>
									<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Target Awal</th>';
									for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
									$html.='<th style="width: 200px;" class="row_head_1_tahun atas kanan bawah text_tengah text_blok">Tahun '.$i.'</th>';
									}
								$html.='
									<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Target Akhir</th>
									<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Satuan</th>
									<th style="width: 150px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Keterangan</th>
									<th style="width: 50px;" class="row_head_1 atas kanan bawah text_tengah text_blok">No Urut</th>
									<th style="width: 150px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Catatan</th>
									<th style="width: 150px;" class="row_head_1 atas kanan bawah text_tengah text_blok">Catatan Indikator</th>
									<th style="width: 400px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Indikator Usulan</th>
									<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Target Awal Usulan</th>';
									for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
									$html.='<th style="width: 200px;" class="row_head_1_tahun atas kanan bawah text_tengah text_blok td-usulan">Tahun '.$i.' Usulan</th>';
									}
								$html.='
									<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Target Akhir Usulan</th>
									<th style="width: 100px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Satuan Usulan</th>
									<th style="width: 150px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Catatan Usulan</th>
									<th style="width: 150px;" class="row_head_1 atas kanan bawah text_tengah text_blok td-usulan">Catatan Indikator Usulan</th>
								</tr>
								<tr>';
								for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
									$html.='
										<th style="width: 100px;" class="row_head_2 atas kanan bawah text_tengah text_blok">Target</th>
										<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Pagu</th>';
									$html.='
										<th style="width: 100px;" class="row_head_2 atas kanan bawah text_tengah text_blok td-usulan">Target Usulan</th>
										<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok td-usulan">Pagu Usulan</th>';
								}
								$html.='</tr>';
								
								$html.="<tr>
									<th class='atas kiri kanan bawah text_tengah text_blok'>0</th>
									<th class='atas kanan bawah text_tengah text_blok'>1</th>
									<th class='atas kanan bawah text_tengah text_blok'>2</th>
									<th class='atas kanan bawah text_tengah text_blok'>3</th>
									<th class='atas kanan bawah text_tengah text_blok'>4</th>
									<th class='atas kanan bawah text_tengah text_blok'>5</th>
									<th class='atas kanan bawah text_tengah text_blok'>6</th>
									<th class='atas kanan bawah text_tengah text_blok'>7</th>
									<th class='atas kanan bawah text_tengah text_blok'>8</th>";
								 
									$target_temp = 9;
									for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
										if($i!=1){
											$target_temp=$pagu_temp+1; 
										}
										$pagu_temp=$target_temp+1;
								
									$html.="<th class='atas kanan bawah text_tengah text_blok'>".$target_temp."</th>
									<th class='atas kanan bawah text_tengah text_blok'>".$pagu_temp."</th>";
									}
								
									$html.="<th class='atas kanan bawah text_tengah text_blok'>".($pagu_temp+1)."</th>
									<th class='atas kanan bawah text_tengah text_blok'>".($pagu_temp+2)."</th>
									<th class='atas kanan bawah text_tengah text_blok'>".($pagu_temp+3)."</th>
									<th class='atas kanan bawah text_tengah text_blok'>".($pagu_temp+4)."</th>
									<th class='atas kanan bawah text_tengah text_blok'>".($pagu_temp+5)."</th>
									<th class='atas kanan bawah text_tengah text_blok'>".($pagu_temp+6)."</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>".($pagu_temp+7)."</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>".($pagu_temp+8)."</th>";
								 
									$target_temp += 9;
									for ($i=1; $i <= $lama_pelaksanaan; $i++) { 
										if($i!=1){
											$target_temp=$pagu_temp+1; 
										}
										$pagu_temp=$target_temp+1;
								
									$html.="<th class='atas kanan bawah text_tengah text_blok td-usulan'>".$target_temp."</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>".$pagu_temp."</th>";
									}
								
									$html.="<th class='atas kanan bawah text_tengah text_blok td-usulan'>".($pagu_temp+1)."</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>".($pagu_temp+2)."</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>".($pagu_temp+3)."</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>".($pagu_temp+4)."</th>
								</tr>
							</thead>
							<tbody>".$body."</tbody>
						</table>
					</div>";
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
}