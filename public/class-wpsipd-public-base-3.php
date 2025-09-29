<?php
require_once WPSIPD_PLUGIN_PATH . "/public/class-wpsipd-public-ssh.php";

class Wpsipd_Public_Base_3 extends Wpsipd_Public_Ssh
{

	public function rekap_total_prog_keg_renstra($atts)
	{
		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}
		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-total-prog-keg.php';
	}

	protected function role()
	{
		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);
		return $user_meta->roles;
	}

	public function get_tujuan_parent_by_tipe($params = array(), $tujuan_exist = array(), $is_locked_jadwal = true, $with_program = false)
	{

		global $wpdb;

		if ($params['relasi_perencanaan'] == '-') {
			return [];
		}

		switch ($params['id_tipe_relasi']) {
			case '2':
				if ($is_locked_jadwal) {
					$table_sasaran = "data_rpjmd_sasaran_lokal_history";
					$table_program = "data_rpjmd_program_lokal_history";
					$table_tujuan = "data_rpjmd_tujuan_lokal_history";
				} else {
					$table_sasaran = "data_rpjmd_sasaran_lokal";
					$table_program = "data_rpjmd_program_lokal";
					$table_tujuan = "data_rpjmd_tujuan_lokal";
				}
				break;

			case '3':
				if ($is_locked_jadwal) {
					$table_sasaran = "data_rpd_sasaran_lokal_history";
					$table_program = "data_rpd_program_lokal_history";
					$table_tujuan = "data_rpd_tujuan_lokal_history";
				} else {
					$table_sasaran = "data_rpd_sasaran_lokal";
					$table_program = "data_rpd_program_lokal";
					$table_tujuan = "data_rpd_tujuan_lokal";
				}
				break;

			default:
				throw new Exception("Tipe relasi perencanaan tidak diketahui, harap menghubungi admin", 1);
				break;
		}

		$where_clause = '';
		if ($is_locked_jadwal) {
			$where_clause = $wpdb->prepare("WHERE a.id_jadwal = %d AND c.id_jadwal = %d", $params['relasi_perencanaan'], $params['relasi_perencanaan']);
		}

		if ($with_program) {
			if (!empty($tujuan_exist)) {
				$sql = $wpdb->prepare("
					SELECT DISTINCT
						a.id_unik as id_unik_sasaran, 
						a.sasaran_teks,
						c.id_unik, 
						c.tujuan_teks
					FROM {$table_sasaran} a 
					INNER JOIN {$table_tujuan} c
							ON a.kode_tujuan = c.id_unik
					WHERE a.id_unik = %s
					  AND a.active = 1 
					  AND c.active = 1 
					  {$where_clause}
				", $tujuan_exist['kode_sasaran_rpjm']);
			} else {
				$sql = $wpdb->prepare("
					SELECT DISTINCT
						c.id_unik, 
						c.tujuan_teks
					FROM {$table_sasaran} a 
					INNER JOIN {$table_program} b
							ON a.id_unik = b.kode_sasaran
					INNER JOIN {$table_tujuan} c
							ON a.kode_tujuan = c.id_unik
					WHERE a.active = 1 
					  AND b.id_unit = %d 
					  AND b.id_unik_indikator IS NOT NULL 
					  AND b.active = 1 
					  AND c.active = 1 
					  {$where_clause}
				", $params['id_unit']);
			}
		} else {
			if (!empty($tujuan_exist)) {
				$sql = $wpdb->prepare("
					SELECT DISTINCT
						a.id_unik as id_unik_sasaran, 
						a.sasaran_teks,
						c.id_unik, 
						c.tujuan_teks
					FROM {$table_sasaran} a 
					INNER JOIN {$table_tujuan} c
							ON a.kode_tujuan = c.id_unik
					WHERE a.id_unik = %s
					  AND a.active = 1 
					  AND c.active = 1 
					  {$where_clause}
				", $tujuan_exist['kode_sasaran_rpjm']);
			} else {
				$sql = "
					SELECT DISTINCT
						c.id_unik, 
						c.tujuan_teks
					FROM {$table_sasaran} a
					INNER JOIN {$table_tujuan} c
							ON a.kode_tujuan = c.id_unik
					WHERE a.active = 1
					  AND c.active = 1 
					  {$where_clause}
				";
			}
		}

		$data =  $wpdb->get_results($sql);
		return $data;
	}

	public function get_sasaran_lokal_by_id_jadwal()
	{
		try {
            $this->newValidate($_POST, [
                'api_key' 	=> 'required|string',
                'id_jadwal' => 'required|numeric',
            ]);

            if ($_POST['api_key'] !== get_option(WPSIPD_API_KEY)) {
                throw new Exception("API key tidak valid atau tidak ditemukan!", 401);
            }

			$data_jadwal = $this->get_data_jadwal_by_id_jadwal_lokal($_POST['id_jadwal']);
			if (empty($data_jadwal)) {
				throw new Exception("Data Jadwal tidak ditemukan!", 401);
			}

			global $wpdb;

			$tahun_anggaran = $data_jadwal->tahun_anggaran;
			$is_locked = $data_jadwal->status == 1;

			if ($data_jadwal->id_tipe == 2) {
				$table = 'data_rpjmd_sasaran_lokal';
				$nama_jadwal = 'RPJMD';
			} elseif ($data_jadwal->id_tipe == 3) {
				$table = 'data_rpd_sasaran_lokal';
				$nama_jadwal = 'RPD';
			} else {
                throw new Exception("Tipe jadwal tidak diketahui!", 401);
			}

			$prefix_history = '';
			if ($is_locked) {
				$prefix_history = '_history';
				$nama_jadwal = $nama_jadwal . ' History';
			}

			$data = $wpdb->get_results(
				$wpdb->prepare("
					SELECT *
					FROM {$table}{$prefix_history}
					WHERE tahun_anggaran = %d
					  AND active = 1
					  AND id_unik_indikator IS NULL
				", $tahun_anggaran)
			);
        
            echo json_encode([
                'status'  => true,
                'message' => "Berhasil Get Sasaran {$nama_jadwal} Lokal.",
				'data'    => $data
            ]);
        } catch (Exception $e) {
            $code = is_int($e->getCode()) && $e->getCode() !== 0 ? $e->getCode() : 500;
            http_response_code($code);
            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
        wp_die();
	}

	public function get_sasaran_parent_by_tipe($params = array(), $is_locked_jadwal = true)
	{
		global $wpdb;

		if ($params['relasi_perencanaan'] == '-') {
			return [];
		}

		$where_clause = '';
		if (!empty($params['kode_sasaran_parent'])) {
			$where_clause .= $wpdb->prepare(" AND id_unik=%s", $params['kode_sasaran_parent']);
		}

		if (!empty($params['kode_tujuan_rpjm'])) {
			$where_clause .= $wpdb->prepare(" AND kode_tujuan=%s", $params['kode_tujuan_rpjm']);
		}

		if ($is_locked_jadwal) {
			$where_clause .= $wpdb->prepare(" AND id_jadwal=%d", $params['relasi_perencanaan']);
		}

		switch ($params['id_tipe_relasi']) {
			case '2':
				if ($is_locked_jadwal) {
					$table_sasaran = "data_rpjmd_sasaran_lokal_history";
				} else {
					$table_sasaran = "data_rpjmd_sasaran_lokal";
				}
				break;

			case '3':
				if ($is_locked_jadwal) {
					$table_sasaran = "data_rpd_sasaran_lokal_history";
				} else {
					$table_sasaran = "data_rpd_sasaran_lokal";
				}
				break;

			default:
				throw new Exception("Tipe relasi perencanaan tidak diketahui, harap menghubungi admin", 1);
				break;
		}

		return $wpdb->get_results("
			SELECT DISTINCT
				id_unik, 
				sasaran_teks
			FROM {$table_sasaran}
			WHERE active = 1 
			  {$where_clause}
		");
	}

	public function get_sasaran_parent()
	{

		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					if ($_POST['id_tipe_relasi'] == '2') {
						$jadwal = $this->validasi_jadwal_perencanaan('rpjm');
					} elseif ($_POST['id_tipe_relasi'] == '3') {
						$jadwal = $this->validasi_jadwal_perencanaan('rpd');
					}

					$is_active = (!empty($jadwal['data'][0]) && $jadwal['data'][0]['status'] == 0);
					$is_locked_jadwal = true;
					if ($is_active) {
						$is_locked_jadwal = false;
					}

					$sasaran = $this->get_sasaran_parent_by_tipe($_POST, $is_locked_jadwal);

					echo json_encode([
						'status' => true,
						'data' => $sasaran
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function get_tujuan_renstra()
	{

		global $wpdb;

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$sql = $wpdb->prepare("
					SELECT 
						* 
					FROM data_unit
					WHERE id_skpd=%d
						AND active=1 
						AND tahun_anggaran=%d 
					ORDER BY id", $_POST['id_skpd'], get_option('_crb_tahun_anggaran_sipd'));
				$skpd = $wpdb->get_results($sql, ARRAY_A);
				if ($_POST['type'] == 1) {
					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_tujuan_lokal
						WHERE id_unik IS NOT NULL AND
							id_unit=%d AND
							tahun_anggaran=%d AND
							id_unik_indikator IS NULL AND
							active=1 
						ORDER BY urut_tujuan", $_POST['id_skpd'], $_POST['tahun_anggaran']);
					$tujuan = $wpdb->get_results($sql, ARRAY_A);

					foreach ($tujuan as $k => $tuj) {
						$sasaran = $wpdb->get_results($wpdb->prepare("
							SELECT 
								id_unik
							from data_renstra_sasaran_lokal 
							where id_unik_indikator IS NULL
								AND active=1
								AND kode_tujuan=%s
								AND tahun_anggaran=%d
						", $tuj['id_unik'], $_POST['tahun_anggaran']), ARRAY_A);

						$kd_all_keg = array();
						foreach ($sasaran as $sas) {

							$program = $wpdb->get_results($wpdb->prepare("
								SELECT 
									id_unik
								from data_renstra_program_lokal 
								where id_unik_indikator IS NULL
									AND active=1
									AND kode_sasaran=%s
									AND tahun_anggaran=%d
							", $sas['id_unik'], $_POST['tahun_anggaran']), ARRAY_A);
							foreach ($program as $prog) {
								$kegiatan = $wpdb->get_results($wpdb->prepare("
									SELECT 
										id_unik
									from data_renstra_kegiatan_lokal 
									where id_unik_indikator IS NULL
										AND active=1
										AND kode_program=%s
										AND tahun_anggaran=%d
								", $prog['id_unik'], $_POST['tahun_anggaran']), ARRAY_A);

								foreach ($kegiatan as $keg) {
									$kd_all_keg[] = "'" . $keg['id_unik'] . "'";
								}
							}
						}
						$tujuan[$k]['pagu_akumulasi_1'] = 0;
						$tujuan[$k]['pagu_akumulasi_2'] = 0;
						$tujuan[$k]['pagu_akumulasi_3'] = 0;
						$tujuan[$k]['pagu_akumulasi_4'] = 0;
						$tujuan[$k]['pagu_akumulasi_5'] = 0;
						$tujuan[$k]['pagu_akumulasi_1_usulan'] = 0;
						$tujuan[$k]['pagu_akumulasi_2_usulan'] = 0;
						$tujuan[$k]['pagu_akumulasi_3_usulan'] = 0;
						$tujuan[$k]['pagu_akumulasi_4_usulan'] = 0;
						$tujuan[$k]['pagu_akumulasi_5_usulan'] = 0;

						if (!empty($kd_all_keg)) {
							$kd_all_keg = implode(',', $kd_all_keg);
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
								from data_renstra_sub_kegiatan_lokal 
								where id_unik_indikator IS NULL
									AND active=1
									AND kode_kegiatan IN ($kd_all_keg)
									AND kode_tujuan=%s
							", $tuj['id_unik']));
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

						$tujuan[$k]['pokin'] = $wpdb->get_results($wpdb->prepare("
							SELECT
								*
							FROM data_pokin_renstra
							WHERE id_unik=%s
								AND id_skpd=%d
								AND active=1
								AND tipe=1
								AND tahun_anggaran=%d
						", $tuj['id_unik'], $_POST['id_skpd'], $_POST['tahun_anggaran']), ARRAY_A);
						
						$tujuan[$k]['satker'] = $wpdb->get_results($wpdb->prepare("
							SELECT
								*
							FROM data_pelaksana_renstra
							WHERE id_unik=%s
								AND id_skpd=%d
								AND active=1
								AND tipe=1
								AND tahun_anggaran=%d
						", $tuj['id_unik'], $_POST['id_skpd'], $_POST['tahun_anggaran']), ARRAY_A);

						if (!empty($tuj['kode_bidang_urusan_multiple'])) {
							$bidur = json_decode($tuj['kode_bidang_urusan_multiple']);
							
							$bidur_text = [];
							foreach ($bidur as $b) {
								$bidur_query = $wpdb->get_var($wpdb->prepare("
									SELECT nama_bidang_urusan
									FROM data_prog_keg
									WHERE kode_bidang_urusan=%s
									  AND active=1
									  AND tahun_anggaran=%d
								", $b, $_POST['tahun_anggaran']));

								$bidur_text[] = $bidur_query;
							}

							$tujuan[$k]['bidur'] = $bidur_text;
						}
					}
				} else {
					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_tujuan
						WHERE tahun_anggaran=%d
							AND active=1
						ORDER BY urut_tujuan
						", $_POST['tahun_anggaran']);
					$tujuan = $wpdb->get_results($sql, ARRAY_A);
				}

				echo json_encode([
					'status' => true,
					'data' => $tujuan,
					'skpd' => $skpd,
					'message' => 'Sukses get tujuan renstra'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	public function add_tujuan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					$skpd = $this->get_skpd_db();
					$skpd_db = $skpd['skpd'];
					$bidur_db = $skpd['bidur'];

					if (empty($skpd_db)) {
						throw new Exception('SKPD untuk user ini tidak ditemukan!');
					}

					if (empty($bidur_db)) {
						throw new Exception('Bidang urusan untuk SKPD ini tidak ditemukan!');
					}

					if ($_POST['id_tipe_relasi'] == '2') {
						$jadwal = $this->validasi_jadwal_perencanaan('rpjm');
					} elseif ($_POST['id_tipe_relasi'] == '3') {
						$jadwal = $this->validasi_jadwal_perencanaan('rpd');
					}

					$is_active = (!empty($jadwal['data'][0]) && $jadwal['data'][0]['status'] == 0);
					$is_locked_jadwal = true;
					if ($is_active) {
						$is_locked_jadwal = false;
					}

					$tujuan = $this->get_tujuan_parent_by_tipe($_POST, [], $is_locked_jadwal);

					echo json_encode([
						'status' => true,
						'data' => $tujuan,
						'skpd' => $skpd_db,
						'bidur' => $bidur_db,
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function submit_tujuan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					$data = json_decode(stripslashes($_POST['data']), true);

					$sasaran_multiple = '';
					if (!empty($data['sasaran-rpjm'])) {
						if (is_array($data['sasaran-rpjm'])) {
							$sasaran_multiple = json_encode($data['sasaran-rpjm']);
						} else {
							$sasaran_multiple = json_encode(array($data['sasaran-rpjm']));
						}
					}

					if (empty($data['bidang-urusan'])) {
						throw new Exception('Bidang urusan tidak boleh kosong!');
					}

					if (is_array($data['bidang-urusan'])) {
						$bidur_multiple = json_encode($data['bidang-urusan']);
					} else {
						$bidur_multiple = json_encode(array($data['bidang-urusan']));
					}

					if(!empty($data['id_jadwal_wp_sakip'])){
						if (empty($data['pokin-level'])) {
							throw new Exception('Pohon Kinerja tidak boleh kosong!');
						}
						if (empty($data['satker-pelaksana'])) {
							throw new Exception('Satuan Kerja Pelaksana tidak boleh kosong!');
						}
					}

					if (empty($data['tujuan_teks'])) {
						throw new Exception('Tujuan tidak boleh kosong!');
					}

					if (empty($data['urut_tujuan'])) {
						throw new Exception('Urut tujuan tidak boleh kosong!');
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id 
						FROM data_renstra_tujuan_lokal
						WHERE tujuan_teks=%s
							AND id_bidang_urusan=%s
							AND id_unit=%d
							AND id_unik IS NOT NULL
							AND id_unik_indikator IS NULL
							AND active=1
							AND tahun_anggaran=%d
						", trim($data['tujuan_teks']), $data['id_bidang_urusan'], $data['id_unit'], $_POST['tahun_anggaran']));
					// die($wpdb->last_query);

					if (!empty($id_cek)) {
						throw new Exception('Tujuan : ' . $data['tujuan_teks'] . ' sudah ada!');
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
					", $data['id_unit'], $_POST['tahun_anggaran']));

					if (empty($dataUnit)) {
						throw new Exception('Unit kerja tidak ditemukan!');
					}

					$data_tujuan = array(
						'id_bidang_urusan' => $data['id_bidang_urusan'],
						'id_unik' => $this->generateRandomString(), // kode_tujuan
						'id_unit' => $dataUnit->id_unit,
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'kode_bidang_urusan' => $data['kode_bidang_urusan'],
						'kode_bidang_urusan_multiple' => $bidur_multiple,
						'kode_sasaran_multiple' => $sasaran_multiple,
						'kode_skpd' => $dataUnit->kode_skpd,
						'nama_bidang_urusan' => $data['nama_bidang_urusan'],
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'nama_skpd' => $dataUnit->nama_skpd,
						'status' => 1,
						'tujuan_teks' => $data['tujuan_teks'],
						'urut_tujuan' => $data['urut_tujuan'],
						'catatan_usulan' => $data['catatan_usulan'],
						'catatan' => $data['catatan'],
						'active' => 1
					);

					$status = $wpdb->insert('data_renstra_tujuan_lokal', $data_tujuan);

					if ($status === false) {
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					$wpdb->update("data_pokin_renstra", array("active" => 0), array(
						"id_unik" => $data_tujuan['id_unik'],
						"tipe" => 1,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));

					$wpdb->update("data_pelaksana_renstra", array("active" => 0), array(
						"id_unik" => $data_tujuan['id_unik'],
						"tipe" => 1,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));

					// cek jika jadwal sakip aktif
					if(!empty($data['id_jadwal_wp_sakip'])){
						$_POST['id_skpd'] = $dataUnit->id_unit;
						$_POST['id_jadwal_wp_sakip'] = $data['id_jadwal_wp_sakip'];
						$pokin_all = $this->get_data_pohon_kinerja(true);
						$new_pokin = array();
						foreach($pokin_all['data'] as $val){
							$new_pokin[$val->id] = $val;
						}
						if(!is_array($data['pokin-level'])){
							$data['pokin-level'] = array($data['pokin-level']);
						}
						foreach($data['pokin-level'] as $id_pokin){
							if(!empty($new_pokin[$id_pokin])){
								$indikator = array();
								foreach($new_pokin[$id_pokin]->indikator as $ind){
									$indikator[] = $ind->label;
								}
								$data_pokin = array(
									"id_pokin" => $id_pokin,
									"level" => $new_pokin[$id_pokin]->level,
									"label" => $new_pokin[$id_pokin]->label,
									"indikator" => implode(', ', $indikator),
									"tipe" => 1,
									"id_unik" => $data_tujuan['id_unik'],
									"id_skpd" => $dataUnit->id_unit,
									"tahun_anggaran" => $_POST['tahun_anggaran'],
									"active" => 1
								);

								$cek_id = $wpdb->get_var($wpdb->prepare("
									SELECT
										id
									FROM data_pokin_renstra
									WHERE id_pokin=%d
										AND id_unik=%s
										AND tipe=1
										AND tahun_anggaran=%d
								", $id_pokin, $data_tujuan['id_unik'], $_POST['tahun_anggaran']));

								if(!empty($cek_id)){
									$wpdb->update("data_pokin_renstra", $data_pokin, array(
										"id" => $cek_id
									));
								}else{
									$wpdb->insert("data_pokin_renstra", $data_pokin);
								}
							}
						}

						$satker_all = $this->get_data_satker(true);
						$new_satker = array();
						foreach($satker_all['data'] as $val){
							$new_satker[$val->id] = $val;
						}
						if(!is_array($data['satker-pelaksana'])){
							$data['satker-pelaksana'] = array($data['satker-pelaksana']);
						}
						foreach($data['satker-pelaksana'] as $id_satker){
							if(!empty($new_satker[$id_satker])){
								$data_satker = array(
									"id_satker" => $id_satker,
									"nama_satker" => $new_satker[$id_satker]->nama,
									"tipe" => 1,
									"id_skpd" => $dataUnit->id_unit,
									"id_unik" => $data_tujuan['id_unik'],
									"tahun_anggaran" => $_POST['tahun_anggaran'],
									"active" => 1
								);

								$cek_id = $wpdb->get_var($wpdb->prepare("
									SELECT
										id
									FROM data_pelaksana_renstra
									WHERE id_satker=%s
										AND tipe=1
										AND id_unik=%s
										AND tahun_anggaran=%d
								", $id_pokin, $data_tujuan['id_unik'], $_POST['tahun_anggaran']));

								if(!empty($cek_id)){
									$wpdb->update("data_pelaksana_renstra", $data_satker, array(
										"id" => $cek_id
									));
								}else{
									$wpdb->insert("data_pelaksana_renstra", $data_satker);
								}
							}
						}
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan tujuan',
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function get_skpd_db($id_skpd = false)
	{
		global $wpdb;
		$user_id = um_user('ID');
		$user_meta = get_userdata($user_id);
		$skpd_db = array();
		$bidur_db = false;
		$tahun_anggaran = 0;
		if (!empty($id_skpd)) {
			$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
			$skpd_db = $wpdb->get_results($wpdb->prepare("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd,
					is_skpd
				from data_unit 
				where tahun_anggaran=%d
					and id_skpd=%d
				group by id_skpd
			", $tahun_anggaran, $id_skpd), ARRAY_A);
		} else {
			if (
				in_array("administrator", $user_meta->roles)
				|| in_array("mitra_bappeda", $user_meta->roles)
				|| in_array("PLT", $user_meta->roles)
				|| in_array("PLH", $user_meta->roles)
				|| in_array("PA", $user_meta->roles)
				|| in_array("KPA", $user_meta->roles)
			) {
				$skpd_db = $wpdb->get_results($wpdb->prepare("
					SELECT 
						nama_skpd, 
						id_skpd, 
						kode_skpd,
						is_skpd
					from data_unit 
					where tahun_anggaran=%d
						and id_skpd=%d
					group by id_skpd
				", $_POST['tahun_anggaran'], $_POST['id_unit']), ARRAY_A);
				$tahun_anggaran = $_POST['tahun_anggaran'];
			}
		}

		$bidur_penunjang = 'X.XX';
		$bidur_all = array();
		foreach ($skpd_db as $i => $data) {
			$bidur = explode('.', $data['kode_skpd']);
			$bidur_1 = $bidur[0] . '.' . $bidur[1];
			$bidur_2 = $bidur[2] . '.' . $bidur[3];
			$bidur_3 = $bidur[4] . '.' . $bidur[5];
			if ($bidur_1 != '0.00') {
				$bidur_all[$bidur_1] = "'" . $bidur_1 . "'";
			}
			if ($bidur_2 != '0.00') {
				$bidur_all[$bidur_2] = "'" . $bidur_2 . "'";
			}
			if ($bidur_3 != '0.00') {
				$bidur_all[$bidur_3] = "'" . $bidur_3 . "'";
			}
			$bidur_all[$bidur_penunjang] = "'" . $bidur_penunjang . "'";
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
		", $tahun_anggaran);
		// die($sql);
		$bidur_db = $wpdb->get_results($sql, ARRAY_A);
		return array(
			'skpd' => $skpd_db,
			'bidur' => $bidur_db
		);
	}

	public function edit_tujuan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					$skpd = $this->get_skpd_db();
					$skpd_db = $skpd['skpd'];
					$bidur_db = $skpd['bidur'];

					if (empty($skpd_db)) {
						throw new Exception('SKPD untuk user ini tidak ditemukan!');
					}

					if (empty($bidur_db)) {
						throw new Exception('Bidang urusan untuk SKPD ini tidak ditemukan!');
					}

					$tujuan = $wpdb->get_row(
						$wpdb->prepare("
							SELECT * 
							FROM data_renstra_tujuan_lokal 
							WHERE id = %d
						", $_POST['id_tujuan']), 
						ARRAY_A
					);

					if ($_POST['id_tipe_relasi'] == '2') {
						$jadwal = $this->validasi_jadwal_perencanaan('rpjm');
					} elseif ($_POST['id_tipe_relasi'] == '3') {
						$jadwal = $this->validasi_jadwal_perencanaan('rpd');
					}

					$is_active = (!empty($jadwal['data'][0]) && $jadwal['data'][0]['status'] == 0);
					$is_locked_jadwal = true;
					if ($is_active) {
						$is_locked_jadwal = false;
					}

					$tujuan_parent_selected = $this->get_tujuan_parent_by_tipe($_POST, $tujuan, $is_locked_jadwal);
					$tujuan_parent = $this->get_tujuan_parent_by_tipe($_POST, [], $is_locked_jadwal);

					$pokin = $wpdb->get_results($wpdb->prepare("
						SELECT
							*
						FROM data_pokin_renstra
						WHERE id_unik=%s
							AND tipe=1
							AND active=1
							AND tahun_anggaran=%d
					", $tujuan['id_unik'], $tujuan['tahun_anggaran']), ARRAY_A);

					$satker = $wpdb->get_results($wpdb->prepare("
						SELECT
							*
						FROM data_pelaksana_renstra
						WHERE id_unik=%s
							AND tipe=1
							AND active=1
							AND tahun_anggaran=%d
					", $tujuan['id_unik'], $tujuan['tahun_anggaran']), ARRAY_A);

					echo json_encode([
						'status' => true,
						'tujuan' => $tujuan,
						'tujuan_parent_selected' => $tujuan_parent_selected,
						'tujuan_parent' => $tujuan_parent,
						'skpd' => $skpd_db,
						'bidur' => $bidur_db,
						'pokin' => $pokin,
						'satker' => $satker,
						'message' => 'Sukses get tujuan by id'
					]);
					exit;
				} else {
					throw new Exception("'Api key tidak sesuai'", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	function wpsipd_query($query)
	{
		$query = str_replace("= 'NULL'", "IS NULL", $query);
		$query = str_replace("= 'NOT NULL'", "IS NOT NULL", $query);
		return $query;
	}

	public function update_tujuan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					if(!empty($data['id_jadwal_wp_sakip'])){
						if(empty($data['pokin-level'])){
							throw new Exception('Pohon Kinerja tidak boleh kosong!');
						}
						if(empty($data['satker-pelaksana'])){
							throw new Exception('Satuan Kerja Pelaksana tidak boleh kosong!');
						}
					}

					if (empty($data['tujuan_teks'])) {
						throw new Exception('Tujuan tidak boleh kosong!');
					}

					if (empty($data['urut_tujuan'])) {
						throw new Exception('Urut tujuan tidak boleh kosong!');
					}

					$sasaran_multiple = '';
					if (!empty($data['sasaran-rpjm'])) {
						if (is_array($data['sasaran-rpjm'])) {
							$sasaran_multiple = json_encode($data['sasaran-rpjm']);
						} else {
							$sasaran_multiple = json_encode(array($data['sasaran-rpjm']));
						}
					}

					if (empty($data['bidang-urusan'])) {
						throw new Exception('Bidang urusan tidak boleh kosong!');
					}

					if (is_array($data['bidang-urusan'])) {
						$bidur_multiple = json_encode($data['bidang-urusan']);
					} else {
						$bidur_multiple = json_encode(array($data['bidang-urusan']));
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT id FROM data_renstra_tujuan_lokal
						WHERE tujuan_teks=%s
							AND id!=%d 
							AND id_unit=%d
							AND id_unik IS NOT NULL
							AND id_unik_indikator IS NULL
							AND active=1
							AND tahun_anggaran=%d
						", trim($data['tujuan_teks']), $data['id'], $data['id_unit'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Tujuan : ' . $data['tujuan_teks'] . ' sudah ada!');
					}

					$tahun_anggaran_wpsipd = $_POST['tahun_anggaran'];
					$dataUnit = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_unit 
						WHERE id_unit=%d 
							AND tahun_anggaran=%d 
							AND active=1 
							AND is_skpd=1 
						order by id_skpd ASC
					", $data['id_unit'], $tahun_anggaran_wpsipd));

					if (empty($dataUnit)) {
						throw new Exception('Unit kerja tidak ditemukan!');
					}

					try {

						$wpdb->query('START TRANSACTION');

						add_filter('query', array($this, 'wpsipd_query'));

						// update tujuan
						$wpdb->update('data_renstra_tujuan_lokal', [
							'id_unit' => $dataUnit->id_unit,
							'kode_sasaran_multiple' => $sasaran_multiple,
							'kode_bidang_urusan_multiple' => $bidur_multiple,
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
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						// update indikator tujuan
						$wpdb->update('data_renstra_tujuan_lokal', [
							'id_unit' => $dataUnit->id_unit,
							'kode_sasaran_multiple' => $sasaran_multiple,
							'kode_bidang_urusan_multiple' => $bidur_multiple,
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
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						remove_filter('query', array($this, 'wpsipd_query'));

						// update data tujuan di table sasaran dan indikator
						$wpdb->update('data_renstra_sasaran_lokal', [
							'tujuan_teks' => $data['tujuan_teks'],
							'urut_tujuan' => $data['urut_tujuan'],
						], [
							'kode_tujuan' => $data['id_unik'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						// update data tujuan di table program dan indikator
						$wpdb->update('data_renstra_program_lokal', [
							'tujuan_teks' => $data['tujuan_teks'],
							'urut_tujuan' => $data['urut_tujuan']
						], [
							'kode_tujuan' => $data['id_unik'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						// update data tujuan di table kegiatan dan indikator
						$wpdb->update('data_renstra_kegiatan_lokal', [
							'tujuan_teks' => $data['tujuan_teks'],
							'urut_tujuan' => $data['urut_tujuan']
						], [
							'kode_tujuan' => $data['id_unik'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						// update data tujuan di table sub kegiatan dan indikator
						$wpdb->update('data_renstra_sub_kegiatan_lokal', [
							'tujuan_teks' => $data['tujuan_teks'],
							'urut_tujuan' => $data['urut_tujuan']
						], [
							'kode_tujuan' => $data['id_unik'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						$wpdb->update("data_pokin_renstra", array("active" => 0), array(
							"id_unik" => $data['id_unik'],
							"tipe" => 1,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						$wpdb->update("data_pelaksana_renstra", array("active" => 0), array(
							"id_unik" => $data['id_unik'],
							"tipe" => 1,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						// cek jika jadwal sakip aktif
						if(!empty($data['id_jadwal_wp_sakip'])){
							$_POST['id_skpd'] = $dataUnit->id_unit;
							$_POST['id_jadwal_wp_sakip'] = $data['id_jadwal_wp_sakip'];
							$pokin_all = $this->get_data_pohon_kinerja(true);
							$new_pokin = array();
							foreach($pokin_all['data'] as $val){
								$new_pokin[$val->id] = $val;
							}
							if(!is_array($data['pokin-level'])){
								$data['pokin-level'] = array($data['pokin-level']);
							}
							foreach($data['pokin-level'] as $id_pokin){
								if(!empty($new_pokin[$id_pokin])){
									$indikator = array();
									foreach($new_pokin[$id_pokin]->indikator as $ind){
										$indikator[] = $ind->label;
									}
									$data_pokin = array(
										"id_pokin" => $id_pokin,
										"level" => $new_pokin[$id_pokin]->level,
										"label" => $new_pokin[$id_pokin]->label,
										"indikator" => implode(', ', $indikator),
										"tipe" => 1,
										"id_unik" => $data['id_unik'],
										"id_skpd" => $dataUnit->id_unit,
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pokin_renstra
										WHERE id_pokin=%d
											AND id_unik=%s
											AND tahun_anggaran=%d
											AND tipe=1
									", $id_pokin, $data['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pokin_renstra", $data_pokin, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pokin_renstra", $data_pokin);
									}
								}
							}

							$satker_all = $this->get_data_satker(true);
							$new_satker = array();
							foreach($satker_all['data'] as $val){
								$new_satker[$val->id] = $val;
							}
							if(!is_array($data['satker-pelaksana'])){
								$data['satker-pelaksana'] = array($data['satker-pelaksana']);
							}
							foreach($data['satker-pelaksana'] as $id_satker){
								if(!empty($new_satker[$id_satker])){
									$data_satker = array(
										"id_satker" => $id_satker,
										"nama_satker" => $new_satker[$id_satker]->nama,
										"tipe" => 1,
										"id_skpd" => $dataUnit->id_unit,
										"id_unik" => $data['id_unik'],
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pelaksana_renstra
										WHERE id_satker=%s
											AND tipe=1
											AND id_unik=%s
											AND tahun_anggaran=%d
									", $id_pokin, $data['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pelaksana_renstra", $data_satker, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pelaksana_renstra", $data_satker);
									}
								}
							}
						}

						$wpdb->query('COMMIT');

						echo json_encode([
							'status' => true,
							'message' => 'Sukses ubah tujuan renstra',
						]);
						exit;
					} catch (Exception $e) {

						$wpdb->query('ROLLBACK');

						throw new Exception('Terjadi kesalahan saat ubah data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses ubah tujuan',
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function delete_tujuan_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE kode_tujuan=%s 
							AND active=1
							AND tahun_anggaran=%d
					", $_POST['id_unik'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception("Tujuan sudah digunakan oleh sasaran", 1);
					}

					$cek_indikator_tujuan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_tujuan_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
							AND tahun_anggaran=%d
					", $_POST['id_unik'], $_POST['tahun_anggaran']), ARRAY_A);

					if (!empty($cek_indikator_tujuan)) {
						throw new Exception("Tujuan sudah digunakan oleh indikator tujuan", 1);
					}

					$wpdb->update('data_renstra_tujuan_lokal', array('active' => 0), array(
						'id_unik' => $_POST['id_unik'],
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					$wpdb->update('data_pokin_renstra', array('active' => 0), array(
						'id_unik' => $_POST['id_unik'],
						'tipe' => 1,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					$wpdb->update('data_pelaksana_renstra', array('active' => 0), array(
						'id_unik' => $_POST['id_unik'],
						'tipe' => 1,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus tujuan'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function get_indikator_tujuan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					if ($_POST['type'] == 1) {
						$sql = $wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_tujuan_lokal 
							WHERE id_unik=%s AND 
								id_unik_indikator IS NOT NULL AND 
								active=1 AND
								tahun_anggaran=%d
							ORDER BY id", $_POST['id_unik'], $_POST['tahun_anggaran']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					} else {

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
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function submit_indikator_tujuan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_tujuan_renstra($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_tujuan_lokal
						WHERE indikator_teks_usulan=%s
							AND id_unik=%s
							AND active=1
							AND tahun_anggaran=%d
					", $data['indikator_teks_usulan'], $data['id_unik'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks_usulan'] . ' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_tujuan_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['id_unik'], $_POST['tahun_anggaran']));

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
						'tahun_anggaran' => $_POST['tahun_anggaran'],
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

					if (in_array('administrator', $this->role())) {
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

					if ($status === false) {
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator tujuan'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function edit_indikator_tujuan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

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
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function update_indikator_tujuan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

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
							AND tahun_anggaran=%d
					", $data['indikator_teks_usulan'], $data['id_unik'], $data['id'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks_usulan'] . ' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_tujuan_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['id_unik'], $_POST['tahun_anggaran']));

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
						'tahun_anggaran' => $_POST['tahun_anggaran'],
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

					if (in_array('administrator', $this->role())) {
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

					if ($status === false) {
						throw new Exception('Terjadi kesalahan saat ubah data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses ubah indikator tujuan'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function delete_indikator_tujuan_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->update('data_renstra_tujuan_lokal', array('active' => 0), array(
						'id' => $_POST['id']
					));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator tujuan'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function verify_indikator_tujuan_renstra(array $data)
	{
		if (empty($data['id_unik'])) {
			throw new Exception('Tujuan wajib dipilih!');
		}

		if (empty($data['indikator_teks_usulan'])) {
			throw new Exception('Indikator usulan tujuan tidak boleh kosong!');
		}

		if (empty($data['satuan_usulan'])) {
			throw new Exception('Satuan usulan indikator tujuan tidak boleh kosong!');
		}

		if ($data['target_awal_usulan'] < 0 || $data['target_awal_usulan'] == '') {
			throw new Exception('Target awal usulan Indikator tujuan tidak boleh kosong!');
		}

		for ($i = 1; $i <= $data['lama_pelaksanaan']; $i++) {
			if ($data['target_' . $i . '_usulan'] < 0 || $data['target_' . $i . '_usulan'] == '') {
				throw new Exception('Target usulan Indikator tujuan tahun ke-' . $i . ' tidak boleh kosong!');
			}
		}

		if ($data['target_akhir_usulan'] < 0 || $data['target_akhir_usulan'] == '') {
			throw new Exception('Target akhir usulan Indikator tujuan tidak boleh kosong!');
		}
	}

	public function get_sasaran_renstra()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				if ($_POST['type'] == 1) {
					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal
						WHERE kode_tujuan=%s
							AND id_unik IS NOT NULL
							AND id_unik_indikator IS NULL
							AND active=1
							AND tahun_anggaran=%d
						ORDER BY urut_sasaran
					", $_POST['kode_tujuan'], $_POST['tahun_anggaran']);
					$sasaran = $wpdb->get_results($sql, ARRAY_A);

					foreach ($sasaran as $k => $sas) {
						$program = $wpdb->get_results($wpdb->prepare("
							SELECT 
								id_unik
							from data_renstra_program_lokal 
							where id_unik_indikator IS NULL
								AND active=1
								AND kode_sasaran=%s
								AND tahun_anggaran=%d
						", $sas['id_unik'], $_POST['tahun_anggaran']), ARRAY_A);

						$kd_all_keg = array();
						foreach ($program as $prog) {
							$kegiatan = $wpdb->get_results($wpdb->prepare("
								SELECT 
									id_unik
								from data_renstra_kegiatan_lokal 
								where id_unik_indikator IS NULL
									AND active=1
									AND kode_program=%s
									AND tahun_anggaran=%d
							", $prog['id_unik'], $_POST['tahun_anggaran']), ARRAY_A);
							foreach ($kegiatan as $keg) {
								$kd_all_keg[] = "'" . $keg['id_unik'] . "'";
							}
						}
						$sasaran[$k]['pagu_akumulasi_1'] = 0;
						$sasaran[$k]['pagu_akumulasi_2'] = 0;
						$sasaran[$k]['pagu_akumulasi_3'] = 0;
						$sasaran[$k]['pagu_akumulasi_4'] = 0;
						$sasaran[$k]['pagu_akumulasi_5'] = 0;
						$sasaran[$k]['pagu_akumulasi_1_usulan'] = 0;
						$sasaran[$k]['pagu_akumulasi_2_usulan'] = 0;
						$sasaran[$k]['pagu_akumulasi_3_usulan'] = 0;
						$sasaran[$k]['pagu_akumulasi_4_usulan'] = 0;
						$sasaran[$k]['pagu_akumulasi_5_usulan'] = 0;
						if (!empty($kd_all_keg)) {
							$kd_all_keg = implode(',', $kd_all_keg);
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
								from data_renstra_sub_kegiatan_lokal 
								where id_unik_indikator IS NULL
									AND active=1
									AND kode_kegiatan IN ($kd_all_keg)
									AND tahun_anggaran=%d
							", $_POST['tahun_anggaran']));
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

						$sasaran[$k]['pokin'] = $wpdb->get_results($wpdb->prepare("
							SELECT
								*
							FROM data_pokin_renstra
							WHERE id_unik=%s
								AND id_skpd=%d
								AND active=1
								AND tipe=2
								AND tahun_anggaran=%d
						", $sas['id_unik'], $sas['id_unit'], $_POST['tahun_anggaran']), ARRAY_A);
						
						$sasaran[$k]['satker'] = $wpdb->get_results($wpdb->prepare("
							SELECT
								*
							FROM data_pelaksana_renstra
							WHERE id_unik=%s
								AND id_skpd=%d
								AND active=1
								AND tipe=2
								AND tahun_anggaran=%d
						", $sas['id_unik'], $sas['id_unit'], $_POST['tahun_anggaran']), ARRAY_A);
					}
				} else {
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
					'sql' => $wpdb->last_query,
					'message' => 'Sukses get sasaran by tujuan'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	public function submit_sasaran_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_sasaran_renstra($data);

					if(!empty($data['id_jadwal_wp_sakip'])){
						if (empty($data['pokin-level'])) {
							throw new Exception('Pohon Kinerja tidak boleh kosong!');
						}
						if (empty($data['satker-pelaksana'])) {
							throw new Exception('Satuan Kerja Pelaksana tidak boleh kosong!');
						}
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sasaran_lokal
						WHERE sasaran_teks=%s
							AND kode_tujuan=%s
							AND active=1
							AND tahun_anggaran=%d
						", $data['sasaran_teks'], $data['kode_tujuan'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Sasaran : ' . $data['sasaran_teks'] . ' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							*
						FROM data_renstra_tujuan_lokal 
						WHERE 
							id_unik=%s AND 
							active=1 AND 
							id_unik IS NOT NULL AND 
							id_unik_indikator IS NULL AND
							tahun_anggaran=%d
					", $data['kode_tujuan'], $_POST['tahun_anggaran']));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					if ($data['relasi_perencanaan'] != '-') {
						$dataSasaranParent = $this->get_sasaran_parent_by_tipe([
							'id_unit' => $data['id_unit'],
							'relasi_perencanaan' => $data['relasi_perencanaan'],
							'id_tipe_relasi' => $data['id_tipe_relasi'],
							'kode_sasaran_parent' => $dataTujuan->kode_sasaran_rpjm,
						]);
						if (!empty($dataSasaranParent)) {
							$data['id_visi'] = $dataSasaranParent[0]->id_visi;
							$data['id_misi'] = $dataSasaranParent[0]->id_misi;
						}
					}

					$data_sasaran = array(
						'id_bidang_urusan' => $dataTujuan->id_bidang_urusan,
						'id_misi' => $data['id_misi'],
						'id_unik' => $this->generateRandomString(), // kode_sasaran
						'id_unit' => $dataTujuan->id_unit,
						'id_visi' => $data['id_visi'],
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'kode_bidang_urusan' => $dataTujuan->kode_bidang_urusan,
						'kode_skpd' => $dataTujuan->kode_skpd,
						'kode_tujuan' => $dataTujuan->id_unik,
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
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'active' => 1
					);

					$status = $wpdb->insert('data_renstra_sasaran_lokal', $data_sasaran);

					if ($status === false) {
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					$wpdb->update("data_pokin_renstra", array("active" => 0), array(
						"id_unik" => $data_sasaran['id_unik'],
						"tipe" => 2,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));

					$wpdb->update("data_pelaksana_renstra", array("active" => 0), array(
						"id_unik" => $data_sasaran['id_unik'],
						"tipe" => 2,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));

					// cek jika jadwal sakip aktif
					if(!empty($data['id_jadwal_wp_sakip'])){
						$_POST['id_skpd'] = $data['id_unit'];
						$_POST['id_jadwal_wp_sakip'] = $data['id_jadwal_wp_sakip'];
						$pokin_all = $this->get_data_pohon_kinerja(true);
						$new_pokin = array();
						foreach($pokin_all['data'] as $val){
							$new_pokin[$val->id] = $val;
						}
						if(!is_array($data['pokin-level'])){
							$data['pokin-level'] = array($data['pokin-level']);
						}
						foreach($data['pokin-level'] as $id_pokin){
							if(!empty($new_pokin[$id_pokin])){
								$indikator = array();
								foreach($new_pokin[$id_pokin]->indikator as $ind){
									$indikator[] = $ind->label;
								}
								$data_pokin = array(
									"id_pokin" => $id_pokin,
									"level" => $new_pokin[$id_pokin]->level,
									"label" => $new_pokin[$id_pokin]->label,
									"indikator" => implode(', ', $indikator),
									"tipe" => 2,
									"id_unik" => $data_sasaran['id_unik'],
									"id_skpd" => $dataTujuan->id_unit,
									"tahun_anggaran" => $_POST['tahun_anggaran'],
									"active" => 1
								);

								$cek_id = $wpdb->get_var($wpdb->prepare("
									SELECT
										id
									FROM data_pokin_renstra
									WHERE id_pokin=%d
										AND id_unik=%s
										AND tipe=2
										AND tahun_anggaran=%d
								", $id_pokin, $data_sasaran['id_unik'], $_POST['tahun_anggaran']));

								if(!empty($cek_id)){
									$wpdb->update("data_pokin_renstra", $data_pokin, array(
										"id" => $cek_id
									));
								}else{
									$wpdb->insert("data_pokin_renstra", $data_pokin);
								}
							}
						}

						$satker_all = $this->get_data_satker(true);
						$new_satker = array();
						foreach($satker_all['data'] as $val){
							$new_satker[$val->id] = $val;
						}
						if(!is_array($data['satker-pelaksana'])){
							$data['satker-pelaksana'] = array($data['satker-pelaksana']);
						}
						foreach($data['satker-pelaksana'] as $id_satker){
							if(!empty($new_satker[$id_satker])){
								$data_satker = array(
									"id_satker" => $id_satker,
									"nama_satker" => $new_satker[$id_satker]->nama,
									"tipe" => 2,
									"id_skpd" => $dataTujuan->id_unit,
									"id_unik" => $data_sasaran['id_unik'],
									"tahun_anggaran" => $_POST['tahun_anggaran'],
									"active" => 1
								);

								$cek_id = $wpdb->get_var($wpdb->prepare("
									SELECT
										id
									FROM data_pelaksana_renstra
									WHERE id_satker=%s
										AND tipe=2
										AND id_unik=%s
										AND tahun_anggaran=%d
								", $id_pokin, $data_sasaran['id_unik'], $_POST['tahun_anggaran']));

								if(!empty($cek_id)){
									$wpdb->update("data_pelaksana_renstra", $data_satker, array(
										"id" => $cek_id
									));
								}else{
									$wpdb->insert("data_pelaksana_renstra", $data_satker);
								}
							}
						}
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan sasaran'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function edit_sasaran_renstra()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				$sasaran = $wpdb->get_row($wpdb->prepare("
					SELECT 
						* 
					FROM data_renstra_sasaran_lokal
					WHERE id=%d
						AND id_unik IS NOT NULL
						AND id_unik_indikator IS NULL
						AND active=1
				", $_POST['id_sasaran']), ARRAY_A);

				$pokin = $wpdb->get_results($wpdb->prepare("
						SELECT
							*
						FROM data_pokin_renstra
						WHERE id_unik=%s
							AND tipe=2
							AND active=1
							AND tahun_anggaran=%d
					", $sasaran['id_unik'], $sasaran['tahun_anggaran']), ARRAY_A);

				$satker = $wpdb->get_results($wpdb->prepare("
					SELECT
						*
					FROM data_pelaksana_renstra
					WHERE id_unik=%s
						AND tipe=2
						AND active=1
						AND tahun_anggaran=%d
				", $sasaran['id_unik'], $sasaran['tahun_anggaran']), ARRAY_A);

				echo json_encode([
					'status' => true,
					'data' => $sasaran,
					'pokin' => $pokin,
					'satker' => $satker,
					'message' => 'Sukses get sasaran by id'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	public function update_sasaran_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_sasaran_renstra($data);

					if(!empty($data['id_jadwal_wp_sakip'])){
						if (empty($data['pokin-level'])) {
							throw new Exception('Pohon Kinerja tidak boleh kosong!');
						}
						if (empty($data['satker-pelaksana'])) {
							throw new Exception('Satuan Kerja Pelaksana tidak boleh kosong!');
						}
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sasaran_lokal
						WHERE sasaran_teks=%s
							AND kode_tujuan=%s
							AND id_unik!=%s
							AND id_unik_indikator IS NULL
							AND active=1
							AND tahun_anggaran=%d
						", $data['sasaran_teks'], $data['kode_tujuan'], $data['kode_sasaran'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Sasaran : ' . $data['sasaran_teks'] . ' sudah ada!');
					}

					$dataTujuan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							*
						FROM data_renstra_tujuan_lokal 
						WHERE 
							id_unik=%s AND 
							active=1 AND 
							id_unik IS NOT NULL AND 
							id_unik_indikator IS NULL AND
							tahun_anggaran=%d
					", $data['kode_tujuan'], $_POST['tahun_anggaran']));

					if (empty($dataTujuan)) {
						throw new Exception('Tujuan yang dipilih tidak ditemukan!');
					}

					if ($data['relasi_perencanaan'] != '-') {
						$dataSasaranParent = $this->get_sasaran_parent_by_tipe([
							'id_unit' => $data['id_unit'],
							'relasi_perencanaan' => $data['relasi_perencanaan'],
							'id_tipe_relasi' => $data['id_tipe_relasi'],
							'kode_sasaran_parent' => $dataTujuan->kode_sasaran_rpjm,
						]);
						if (!empty($dataSasaranParent)) {
							$data['id_visi'] = $dataSasaranParent[0]->id_visi;
							$data['id_misi'] = $dataSasaranParent[0]->id_misi;
						}
					}

					try {

						$wpdb->query('START TRANSACTION');
						add_filter('query', array($this, 'wpsipd_query'));

						// update data sasaran
						$status = $wpdb->update('data_renstra_sasaran_lokal', [
							'id_bidang_urusan' => $dataTujuan->id_bidang_urusan,
							'id_misi' => $data['id_misi'],
							'id_unit' => $dataTujuan->id_unit,
							'id_visi' => $data['id_visi'],
							'kode_bidang_urusan' => $dataTujuan->kode_bidang_urusan,
							'kode_skpd' => $dataTujuan->kode_skpd,
							'kode_tujuan' => $dataTujuan->id_unik,
							'nama_bidang_urusan' => $dataTujuan->nama_bidang_urusan,
							'nama_skpd' => $dataTujuan->nama_skpd,
							'sasaran_teks' => $data['sasaran_teks'],
							'tujuan_lock' => $dataTujuan->tujuan_lock,
							'tujuan_teks' => $dataTujuan->tujuan_teks,
							'urut_sasaran' => $data['urut_sasaran'],
							'catatan_usulan' => $data['catatan_usulan'],
							'catatan' => $data['catatan'],
							'urut_tujuan' => $dataTujuan->urut_tujuan,
							'update_at' => current_time('mysql')
						], [
							'id_unik' => $data['kode_sasaran'], // pake id_unik biar teks sasaran di row indikator sasaran ikut terupdate
							'id_unik_indikator' => 'NULL',
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						// update data indikator sasaran
						$status = $wpdb->update('data_renstra_sasaran_lokal', [
							'id_bidang_urusan' => $dataTujuan->id_bidang_urusan,
							'id_misi' => $data['id_misi'],
							'id_unit' => $dataTujuan->id_unit,
							'id_visi' => $data['id_visi'],
							'kode_bidang_urusan' => $dataTujuan->kode_bidang_urusan,
							'kode_skpd' => $dataTujuan->kode_skpd,
							'kode_tujuan' => $dataTujuan->id_unik,
							'nama_bidang_urusan' => $dataTujuan->nama_bidang_urusan,
							'nama_skpd' => $dataTujuan->nama_skpd,
							'sasaran_teks' => $data['sasaran_teks'],
							'tujuan_lock' => $dataTujuan->tujuan_lock,
							'tujuan_teks' => $dataTujuan->tujuan_teks,
							'urut_sasaran' => $data['urut_sasaran'],
							'urut_tujuan' => $dataTujuan->urut_tujuan,
							'update_at' => current_time('mysql')
						], [
							'id_unik' => $data['kode_sasaran'], // pake id_unik biar teks sasaran di row indikator sasaran ikut terupdate
							'id_unik_indikator' => 'NOT NULL',
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						// update data sasaran di table program dan indikator
						$wpdb->update('data_renstra_program_lokal', [
							'sasaran_teks' => $data['sasaran_teks'],
							'urut_sasaran' => $data['urut_sasaran']
						], [
							'kode_sasaran' => $data['kode_sasaran'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						// update data sasaran di table kegiatan dan indikator
						$wpdb->update('data_renstra_kegiatan_lokal', [
							'sasaran_teks' => $data['sasaran_teks'],
							'urut_sasaran' => $data['urut_sasaran']
						], [
							'kode_sasaran' => $data['kode_sasaran'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						// update data sasaran di table sub kegiatan dan indikator
						$wpdb->update('data_renstra_sub_kegiatan_lokal', [
							'sasaran_teks' => $data['sasaran_teks'],
							'urut_sasaran' => $data['urut_sasaran']
						], [
							'kode_sasaran' => $data['kode_sasaran'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						$wpdb->update("data_pokin_renstra", array("active" => 0), array(
							"id_unik" => $data['kode_sasaran'],
							"tipe" => 2,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						$wpdb->update("data_pelaksana_renstra", array("active" => 0), array(
							"id_unik" => $data['kode_sasaran'],
							"tipe" => 2,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						// cek jika jadwal sakip aktif
						if(!empty($data['id_jadwal_wp_sakip'])){
							$_POST['id_skpd'] = $dataTujuan->id_unit;
							$_POST['id_jadwal_wp_sakip'] = $data['id_jadwal_wp_sakip'];
							$pokin_all = $this->get_data_pohon_kinerja(true);
							$new_pokin = array();
							foreach($pokin_all['data'] as $val){
								$new_pokin[$val->id] = $val;
							}
							if(!is_array($data['pokin-level'])){
								$data['pokin-level'] = array($data['pokin-level']);
							}
							foreach($data['pokin-level'] as $id_pokin){
								if(!empty($new_pokin[$id_pokin])){
									$indikator = array();
									foreach($new_pokin[$id_pokin]->indikator as $ind){
										$indikator[] = $ind->label;
									}
									$data_pokin = array(
										"id_pokin" => $id_pokin,
										"level" => $new_pokin[$id_pokin]->level,
										"label" => $new_pokin[$id_pokin]->label,
										"indikator" => implode(', ', $indikator),
										"tipe" => 2,
										"id_unik" => $data['kode_sasaran'],
										"id_skpd" => $dataTujuan->id_unit,
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pokin_renstra
										WHERE id_pokin=%d
											AND id_unik=%s
											AND tipe=2
											AND tahun_anggaran=%d
									", $id_pokin, $data['kode_sasaran'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pokin_renstra", $data_pokin, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pokin_renstra", $data_pokin);
									}
								}
							}

							$satker_all = $this->get_data_satker(true);
							$new_satker = array();
							foreach($satker_all['data'] as $val){
								$new_satker[$val->id] = $val;
							}
							if(!is_array($data['satker-pelaksana'])){
								$data['satker-pelaksana'] = array($data['satker-pelaksana']);
							}
							foreach($data['satker-pelaksana'] as $id_satker){
								if(!empty($new_satker[$id_satker])){
									$data_satker = array(
										"id_satker" => $id_satker,
										"nama_satker" => $new_satker[$id_satker]->nama,
										"tipe" => 2,
										"id_skpd" => $dataTujuan->id_unit,
										"id_unik" => $data['kode_sasaran'],
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pelaksana_renstra
										WHERE id_satker=%s
											AND tipe=2
											AND id_unik=%s
											AND tahun_anggaran=%d
									", $id_pokin, $data['kode_sasaran'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pelaksana_renstra", $data_satker, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pelaksana_renstra", $data_satker);
									}
								}
							}
						}

						remove_filter('query', array($this, 'wpsipd_query'));

						$wpdb->query('COMMIT');

						die(json_encode([
							'status' => true,
							'message' => 'Sukses ubah sasaran RENSTRA',
						]));
					} catch (Exception $e) {

						$wpdb->query('ROLLBACK');

						throw new Exception('Terjadi kesalahan saat ubah data, harap hubungi admin!');
					}
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function delete_sasaran_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE kode_sasaran=%s 
							AND active=1
							AND tahun_anggaran=%d
					", $_POST['kode_sasaran'], $_POST['kode_sasaran']));

					if (!empty($id_cek)) {
						throw new Exception("Sasaran sudah digunakan oleh program", 1);
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
							AND tahun_anggaran=%d
					", $_POST['kode_sasaran'], $_POST['kode_sasaran']));

					if (!empty($id_cek)) {
						throw new Exception("Sasaran sudah digunakan oleh indikator sasaran", 1);
					}

					$wpdb->update('data_renstra_sasaran_lokal', array('active' => 0), array(
						'id_unik' => $_POST['kode_sasaran'],
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					$wpdb->update('data_pokin_renstra', array('active' => 0), array(
						'id_unik' => $_POST['kode_sasaran'],
						'tipe' => 2,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					$wpdb->update('data_pelaksana_renstra', array('active' => 0), array(
						'id_unik' => $_POST['kode_sasaran'],
						'tipe' => 2,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus sasaran'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	private function verify_sasaran_renstra($data)
	{
		if (empty($data['kode_tujuan'])) {
			throw new Exception('Tujuan wajib dipilih!');
		}

		if (empty($data['sasaran_teks'])) {
			throw new Exception('Sasaran tidak boleh kosong!');
		}

		if (empty($data['urut_sasaran'])) {
			throw new Exception('Urut sasaran tidak boleh kosong!');
		}
	}

	public function get_indikator_sasaran_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					if ($_POST['type'] == 1) {
						$sql = $wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_sasaran_lokal 
							WHERE 
								id_unik=%s AND 
								id_unik_indikator IS NOT NULL AND 
								active=1 AND
								tahun_anggaran=%d
							ORDER BY id", $_POST['id_unik'], $_POST['tahun_anggaran']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					} else {
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
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function submit_indikator_sasaran_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

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
							AND tahun_anggaran=%d
					", $data['indikator_teks_usulan'], $data['id_unik'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks_usulan'] . ' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE 
							id_unik=%s AND 
							id_unik_indikator IS NULL AND 
							active=1 AND
							tahun_anggaran=%d
					", $data['id_unik'], $_POST['tahun_anggaran']));

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
						'tahun_anggaran' => $_POST['tahun_anggaran'],
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

					if (in_array('administrator', $this->role())) {
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

					if ($status === false) {
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator sasaran'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function edit_indikator_sasaran_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

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
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function update_indikator_sasaran_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_sasaran_renstra($data);

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sasaran_lokal
						WHERE indikator_teks_usulan=%s
							AND id_unik=%s
							AND id!=%d
							AND active=1
							AND tahun_anggaran=%d
					", $data['indikator_teks_usulan'], $data['id_unik'], $data['id'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks_usulan'] . ' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE 
							id_unik=%s AND 
							id_unik_indikator IS NULL AND 
							active=1 AND
							tahun_anggaran=%d
					", $data['id_unik'], $_POST['tahun_anggaran']));

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

					if (in_array('administrator', $this->role())) {
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

					if ($status === false) {
						throw new Exception('Terjadi kesalahan saat simpan data, harap hubungi admin!');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses ubah indikator sasaran'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function delete_indikator_sasaran_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->update('data_renstra_sasaran_lokal', array('active' => 0), array(
						'id' => $_POST['id']
					));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator sasaran'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	private function verify_indikator_sasaran_renstra(array $data)
	{
		if (empty($data['id_unik'])) {
			throw new Exception('Sasaran wajib dipilih!');
		}

		if (empty($data['indikator_teks_usulan'])) {
			throw new Exception('Indikator usulan sasaran tidak boleh kosong!');
		}

		if (empty($data['satuan_usulan'])) {
			throw new Exception('Satuan indikator sasaran usulan tidak boleh kosong!');
		}

		if ($data['target_awal_usulan'] < 0 || $data['target_awal_usulan'] == '') {
			throw new Exception('Target awal usulan Indikator sasaran tidak boleh kosong!');
		}

		for ($i = 1; $i <= $data['lama_pelaksanaan']; $i++) {
			if ($data['target_' . $i . '_usulan'] < 0 || $data['target_' . $i . '_usulan'] == '') {
				throw new Exception('Target usulan Indikator sasaran tahun ke-' . $i . ' tidak boleh kosong!');
			}
		}

		if ($data['target_akhir_usulan'] < 0 || $data['target_akhir_usulan'] == '') {
			throw new Exception('Target akhir usulan Indikator sasaran tidak boleh kosong!');
		}
	}

	public function get_program_renstra()
	{

		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if ($_POST['type'] == 1) {
					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal
						WHERE kode_sasaran=%s AND
							id_unik IS NOT NULL and
							id_unik_indikator IS NULL and
							active=1 AND
							tahun_anggaran=%d
						ORDER BY nama_program
					", $_POST['kode_sasaran'], $_POST['tahun_anggaran']);
					$program = $wpdb->get_results($sql, ARRAY_A);

					foreach ($program as $k => $prog) {
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
							from data_renstra_program_lokal 
							where id_unik_indikator IS NOT NULL
								AND active=1
								AND id_unik=%s
						", $prog['id_unik']));
						$program[$k]['pagu_akumulasi_1_program'] = $pagu->pagu_akumulasi_1;
						$program[$k]['pagu_akumulasi_2_program'] = $pagu->pagu_akumulasi_2;
						$program[$k]['pagu_akumulasi_3_program'] = $pagu->pagu_akumulasi_3;
						$program[$k]['pagu_akumulasi_4_program'] = $pagu->pagu_akumulasi_4;
						$program[$k]['pagu_akumulasi_5_program'] = $pagu->pagu_akumulasi_5;
						$program[$k]['pagu_akumulasi_1_usulan_program'] = $pagu->pagu_akumulasi_1_usulan;
						$program[$k]['pagu_akumulasi_2_usulan_program'] = $pagu->pagu_akumulasi_2_usulan;
						$program[$k]['pagu_akumulasi_3_usulan_program'] = $pagu->pagu_akumulasi_3_usulan;
						$program[$k]['pagu_akumulasi_4_usulan_program'] = $pagu->pagu_akumulasi_4_usulan;
						$program[$k]['pagu_akumulasi_5_usulan_program'] = $pagu->pagu_akumulasi_5_usulan;

						$kegiatan = $wpdb->get_results(
							$wpdb->prepare("
							SELECT 
								id_unik 
							FROM 
								data_renstra_kegiatan_lokal 
							WHERE 
								kode_program=%s AND
								id_unik_indikator IS NULL AND 
								active=1
								", $prog['id_unik']),
							ARRAY_A
						);

						$program[$k]['pagu_akumulasi_1'] = 0;
						$program[$k]['pagu_akumulasi_2'] = 0;
						$program[$k]['pagu_akumulasi_3'] = 0;
						$program[$k]['pagu_akumulasi_4'] = 0;
						$program[$k]['pagu_akumulasi_5'] = 0;
						$program[$k]['pagu_akumulasi_1_usulan'] = 0;
						$program[$k]['pagu_akumulasi_2_usulan'] = 0;
						$program[$k]['pagu_akumulasi_3_usulan'] = 0;
						$program[$k]['pagu_akumulasi_4_usulan'] = 0;
						$program[$k]['pagu_akumulasi_5_usulan'] = 0;

						$kd_all_keg = [];
						foreach ($kegiatan as $key => $keg) {
							$kd_all_keg[] = "'" . $keg['id_unik'] . "'";
						}

						if (!empty($kd_all_keg)) {
							$kd_keg = implode(",", $kd_all_keg);
							$pagu = $wpdb->get_row($wpdb->prepare("
								SELECT 
									coalesce(sum(pagu_1), 0) as pagu_akumulasi_1,
									coalesce(sum(pagu_2), 0) as pagu_akumulasi_2,
									coalesce(sum(pagu_3), 0) as pagu_akumulasi_3,
									coalesce(sum(pagu_4), 0) as pagu_akumulasi_4,
									coalesce(sum(pagu_5), 0) as pagu_akumulasi_5,
									coalesce(sum(pagu_1_usulan), 0) as pagu_akumulasi_1_usulan,
									coalesce(sum(pagu_2_usulan), 0) as pagu_akumulasi_2_usulan,
									coalesce(sum(pagu_3_usulan), 0) as pagu_akumulasi_3_usulan,
									coalesce(sum(pagu_4_usulan), 0) as pagu_akumulasi_4_usulan,
									coalesce(sum(pagu_5_usulan), 0) as pagu_akumulasi_5_usulan
								from data_renstra_sub_kegiatan_lokal 
								where id_unik_indikator IS NULL
									AND kode_kegiatan in (" . $kd_keg . ")
									AND active=1
							"));

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
						$program[$k]['pokin'] = $wpdb->get_results($wpdb->prepare("
							SELECT
								*
							FROM data_pokin_renstra
							WHERE id_unik=%s
								AND id_skpd=%d
								AND active=1
								AND tipe=3
								AND tahun_anggaran=%d
						", $prog['id_unik'], $prog['id_unit'], $_POST['tahun_anggaran']), ARRAY_A);
						
						$program[$k]['satker'] = $wpdb->get_results($wpdb->prepare("
							SELECT
								*
							FROM data_pelaksana_renstra
							WHERE id_unik=%s
								AND id_skpd=%d
								AND active=1
								AND tipe=3
								AND tahun_anggaran=%d
						", $prog['id_unik'], $prog['id_unit'], $_POST['tahun_anggaran']), ARRAY_A);
					}
				} else {
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
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	public function submit_program_renstra()
	{

		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_program_renstra($data);
					if(!empty($data['id_jadwal_wp_sakip'])){
						if (empty($data['pokin-level'])) {
							throw new Exception('Pohon Kinerja tidak boleh kosong!');
						}
						if (empty($data['satker-pelaksana'])) {
							throw new Exception('Satuan Kerja Pelaksana tidak boleh kosong!');
						}
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_program_lokal
						WHERE id_program=%d
							AND kode_sasaran=%s
							AND active=1
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['id_program'], $data['kode_sasaran'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						$program = $wpdb->get_row($wpdb->prepare("
							SELECT 
								nama_program 
							FROM data_prog_keg 
							WHERE id_program=%d 
								AND tahun_anggaran=%d
						", $data['id_program'], $_POST['tahun_anggaran']));
						if (empty($program)) {
							throw new Exception('Program tidak ditemukan!');
						}
						throw new Exception('Program : ' . $program->nama_program . ' sudah ada!');
					}

					$dataSasaran = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sasaran_lokal 
						WHERE id_unik=%s
							AND active=1
							AND tahun_anggaran=%d
					", $data['kode_sasaran'], $_POST['tahun_anggaran']));

					if (empty($dataSasaran)) {
						throw new Exception('Sasaran belum dipilih!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program,
                            u.kode_program
                        FROM data_prog_keg as u 
                        WHERE u.tahun_anggaran=%d
                        	AND id_program=%d
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ", $_POST['tahun_anggaran'], $data['id_program']));

					if (empty($dataProgram)) {
						throw new Exception('Program tidak ditemukan!');
					}

					try {
						$data_program = array(
							'bidur_lock' => 0,
							'id_bidang_urusan' => $dataSasaran->id_bidang_urusan,
							'id_misi' => $dataSasaran->id_misi,
							'id_program' => $dataProgram->id_program,
							'id_unik' => $this->generateRandomString(),
							'id_unit' => $dataSasaran->id_unit,
							'id_visi' => $dataSasaran->id_visi,
							'is_locked' => 0,
							'is_locked_indikator' => 0,
							'kode_bidang_urusan' => $dataSasaran->kode_bidang_urusan,
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
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'active' => 1
						);
						$wpdb->insert('data_renstra_program_lokal', $data_program);

						$wpdb->update("data_pokin_renstra", array("active" => 0), array(
							"id_unik" => $data_program['id_unik'],
							"tipe" => 3,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						$wpdb->update("data_pelaksana_renstra", array("active" => 0), array(
							"id_unik" => $data_program['id_unik'],
							"tipe" => 3,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						// cek jika jadwal sakip aktif
						if(!empty($data['id_jadwal_wp_sakip'])){
							$_POST['id_skpd'] = $dataSasaran->id_unit;
							$_POST['id_jadwal_wp_sakip'] = $data['id_jadwal_wp_sakip'];
							$pokin_all = $this->get_data_pohon_kinerja(true);
							$new_pokin = array();
							foreach($pokin_all['data'] as $val){
								$new_pokin[$val->id] = $val;
							}
							if(!is_array($data['pokin-level'])){
								$data['pokin-level'] = array($data['pokin-level']);
							}
							foreach($data['pokin-level'] as $id_pokin){
								if(!empty($new_pokin[$id_pokin])){
									$indikator = array();
									foreach($new_pokin[$id_pokin]->indikator as $ind){
										$indikator[] = $ind->label;
									}
									$data_pokin = array(
										"id_pokin" => $id_pokin,
										"level" => $new_pokin[$id_pokin]->level,
										"label" => $new_pokin[$id_pokin]->label,
										"indikator" => implode(', ', $indikator),
										"tipe" => 3,
										"id_unik" => $data_program['id_unik'],
										"id_skpd" => $dataSasaran->id_unit,
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pokin_renstra
										WHERE id_pokin=%d
											AND id_unik=%s
											AND tipe=3
											AND tahun_anggaran=%d
									", $id_pokin, $data_program['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pokin_renstra", $data_pokin, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pokin_renstra", $data_pokin);
									}
								}
							}

							$satker_all = $this->get_data_satker(true);
							$new_satker = array();
							foreach($satker_all['data'] as $val){
								$new_satker[$val->id] = $val;
							}
							if(!is_array($data['satker-pelaksana'])){
								$data['satker-pelaksana'] = array($data['satker-pelaksana']);
							}
							foreach($data['satker-pelaksana'] as $id_satker){
								if(!empty($new_satker[$id_satker])){
									$data_satker = array(
										"id_satker" => $id_satker,
										"nama_satker" => $new_satker[$id_satker]->nama,
										"tipe" => 3,
										"id_skpd" => $dataSasaran->id_unit,
										"id_unik" => $data_program['id_unik'],
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pelaksana_renstra
										WHERE id_satker=%s
											AND tipe=3
											AND id_unik=%s
											AND tahun_anggaran=%d
									", $id_pokin, $data_program['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pelaksana_renstra", $data_satker, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pelaksana_renstra", $data_satker);
									}
								}
							}
						}
						echo json_encode([
							'status' => true,
							'message' => 'Sukses simpan program'
						]);
						exit;
					} catch (Exception $e) {
						throw $e;
					}
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function edit_program_renstra()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

				$data = $wpdb->get_row($wpdb->prepare("
					SELECT * FROM data_renstra_program_lokal
					WHERE id_unik=%s
						AND id_unik_indikator IS NULL
						AND active=1
						AND tahun_anggaran=%d
					", $_POST['id_unik'], $_POST['tahun_anggaran']), ARRAY_A);

				$pokin = $wpdb->get_results($wpdb->prepare("
						SELECT
							*
						FROM data_pokin_renstra
						WHERE id_unik=%s
							AND tipe=3
							AND active=1
							AND tahun_anggaran=%d
					", $data['id_unik'], $data['tahun_anggaran']), ARRAY_A);

				$satker = $wpdb->get_results($wpdb->prepare("
					SELECT
						*
					FROM data_pelaksana_renstra
					WHERE id_unik=%s
						AND tipe=3
						AND active=1
						AND tahun_anggaran=%d
				", $data['id_unik'], $data['tahun_anggaran']), ARRAY_A);

				echo json_encode([
					'status' => true,
					'data' => $data,
					'pokin' => $pokin,
					'satker' => $satker,
					'message' => 'Sukses get program by id_unik'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	public function update_program_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_program_renstra($data);
					if(!empty($data['id_jadwal_wp_sakip'])){
						if (empty($data['pokin-level'])) {
							throw new Exception('Pohon Kinerja tidak boleh kosong!');
						}
						if (empty($data['satker-pelaksana'])) {
							throw new Exception('Satuan Kerja Pelaksana tidak boleh kosong!');
						}
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_program_lokal
						WHERE id_program=%d
							AND id_unik!=%s
							AND kode_sasaran=%s
							AND active=1
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
						", $data['id_program'], $data['id_unik'], $data['kode_sasaran'], $_POST['tahun_anggaran']));

					$tahun_anggaran_wpsipd = $_POST['tahun_anggaran'];

					if (!empty($id_cek)) {
						$program = $wpdb->get_row($wpdb->prepare("
							SELECT 
								nama_program 
							FROM data_prog_keg 
							WHERE id_program=%d 
								AND tahun_anggaran=%d
						", $data['id_program'], $tahun_anggaran_wpsipd));
						if (empty($program)) {
							throw new Exception('Program tidak ditemukan!');
						}
						throw new Exception('Program : ' . $program->nama_program . ' sudah ada!');
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

					if (empty($dataSasaran)) {
						throw new Exception('Sasaran tidak ditemukan!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program,
                            u.kode_program
                        FROM data_prog_keg as u 
                        WHERE u.tahun_anggaran=%d 
                        	AND u.id_program=%d
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC 
                    ", $tahun_anggaran_wpsipd, $data['id_program']));

					if (empty($dataProgram)) {
						throw new Exception('Program tidak ditemukan!');
					}

					try {

						add_filter('query', array($this, 'wpsipd_query'));

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
							'id_unik_indikator' => 'NULL',
							'tahun_anggaran' => $_POST['tahun_anggaran']
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
							'id_unik_indikator' => 'NOT NULL',
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						// update kegiatan
						$wpdb->update('data_renstra_kegiatan_lokal', [
							'id_bidang_urusan' => $dataSasaran->id_bidang_urusan,
							'id_misi' => $dataSasaran->id_misi,
							'id_program' => $dataProgram->id_program,
							'id_unit' => $dataSasaran->id_unit,
							'id_visi' => $dataSasaran->id_visi,
							'kode_bidang_urusan' => $dataSasaran->kode_bidang_urusan,
							'kode_program' => $data['id_unik'],
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
							'kode_program' => $data['id_unik'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						// update sub kegiatan
						$wpdb->update('data_renstra_sub_kegiatan_lokal', [
							'id_bidang_urusan' => $dataSasaran->id_bidang_urusan,
							'id_misi' => $dataSasaran->id_misi,
							'id_program' => $dataProgram->id_program,
							'id_unit' => $dataSasaran->id_unit,
							'id_visi' => $dataSasaran->id_visi,
							'kode_bidang_urusan' => $dataSasaran->kode_bidang_urusan,
							'kode_program' => $data['id_unik'],
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
							'kode_program' => $data['id_unik'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						$wpdb->update("data_pokin_renstra", array("active" => 0), array(
							"id_unik" => $data['id_unik'],
							"tipe" => 3,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						$wpdb->update("data_pelaksana_renstra", array("active" => 0), array(
							"id_unik" => $data['id_unik'],
							"tipe" => 3,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						// cek jika jadwal sakip aktif
						if(!empty($data['id_jadwal_wp_sakip'])){
							$_POST['id_skpd'] = $dataSasaran->id_unit;
							$_POST['id_jadwal_wp_sakip'] = $data['id_jadwal_wp_sakip'];
							$pokin_all = $this->get_data_pohon_kinerja(true);
							$new_pokin = array();
							foreach($pokin_all['data'] as $val){
								$new_pokin[$val->id] = $val;
							}
							if(!is_array($data['pokin-level'])){
								$data['pokin-level'] = array($data['pokin-level']);
							}
							foreach($data['pokin-level'] as $id_pokin){
								if(!empty($new_pokin[$id_pokin])){
									$indikator = array();
									foreach($new_pokin[$id_pokin]->indikator as $ind){
										$indikator[] = $ind->label;
									}
									$data_pokin = array(
										"id_pokin" => $id_pokin,
										"level" => $new_pokin[$id_pokin]->level,
										"label" => $new_pokin[$id_pokin]->label,
										"indikator" => implode(', ', $indikator),
										"tipe" => 3,
										"id_unik" => $data['id_unik'],
										"id_skpd" => $dataSasaran->id_unit,
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pokin_renstra
										WHERE id_pokin=%d
											AND id_unik=%s
											AND tipe=3
											AND tahun_anggaran=%d
									", $id_pokin, $data['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pokin_renstra", $data_pokin, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pokin_renstra", $data_pokin);
									}
								}
							}

							$satker_all = $this->get_data_satker(true);
							$new_satker = array();
							foreach($satker_all['data'] as $val){
								$new_satker[$val->id] = $val;
							}
							if(!is_array($data['satker-pelaksana'])){
								$data['satker-pelaksana'] = array($data['satker-pelaksana']);
							}
							foreach($data['satker-pelaksana'] as $id_satker){
								if(!empty($new_satker[$id_satker])){
									$data_satker = array(
										"id_satker" => $id_satker,
										"nama_satker" => $new_satker[$id_satker]->nama,
										"tipe" => 3,
										"id_skpd" => $dataSasaran->id_unit,
										"id_unik" => $data['id_unik'],
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pelaksana_renstra
										WHERE id_satker=%s
											AND tipe=3
											AND id_unik=%s
											AND tahun_anggaran=%d
									", $id_pokin, $data['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pelaksana_renstra", $data_satker, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pelaksana_renstra", $data_satker);
									}
								}
							}
						}

						remove_filter('query', array($this, 'wpsipd_query'));

						echo json_encode([
							'status' => true,
							'message' => 'Sukses ubah program'
						]);
						exit;
					} catch (Exception $e) {
						throw $e;
					}
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function delete_program_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE kode_program=%s 
							AND id_unik IS NOT NULL 
							AND id_unik_indikator IS NULL 
							AND active=1
							AND tahun_anggaran=%d
					", $_POST['kode_program'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception("Program sudah digunakan oleh kegiatan", 1);
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
							AND tahun_anggaran=%d
					", $_POST['kode_program'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception("Program sudah digunakan oleh indikator program", 1);
					}

					$wpdb->update('data_renstra_program_lokal', array('active' => 0), array(
						'id_unik' => $_POST['kode_program'],
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					$wpdb->update('data_pokin_renstra', array('active' => 0), array(
						'id_unik' => $_POST['kode_program'],
						'tipe' => 3,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					$wpdb->update('data_pelaksana_renstra', array('active' => 0), array(
						'id_unik' => $_POST['kode_program'],
						'tipe' => 3,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus program'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	private function verify_program_renstra(array $data)
	{
		if (empty($data['kode_sasaran'])) {
			throw new Exception('Sasaran wajib dipilih!');
		}

		// tidak digunakan karena data bidang urusan diambil dari tujuan
		return;

		if (empty($data['id_urusan'])) {
			throw new Exception('Urusan wajib dipilih');
		}

		if (empty($data['id_bidang'])) {
			throw new Exception('Bidang wajib dipilih!');
		}

		if (empty($data['id_program'])) {
			throw new Exception('Program wajib dipilih');
		}
	}

	function get_pagu_indikator_program($id_unik)
	{
		global $wpdb;
		$program = array();
		$program['pagu_akumulasi_1_program'] = '';
		$program['pagu_akumulasi_2_program'] = '';
		$program['pagu_akumulasi_3_program'] = '';
		$program['pagu_akumulasi_4_program'] = '';
		$program['pagu_akumulasi_5_program'] = '';
		$program['pagu_akumulasi_1_usulan_program'] = '';
		$program['pagu_akumulasi_2_usulan_program'] = '';
		$program['pagu_akumulasi_3_usulan_program'] = '';
		$program['pagu_akumulasi_4_usulan_program'] = '';
		$program['pagu_akumulasi_5_usulan_program'] = '';
		$program['pagu_akumulasi_1'] = '';
		$program['pagu_akumulasi_2'] = '';
		$program['pagu_akumulasi_3'] = '';
		$program['pagu_akumulasi_4'] = '';
		$program['pagu_akumulasi_5'] = '';
		$program['pagu_akumulasi_1_usulan'] = '';
		$program['pagu_akumulasi_2_usulan'] = '';
		$program['pagu_akumulasi_3_usulan'] = '';
		$program['pagu_akumulasi_4_usulan'] = '';
		$program['pagu_akumulasi_5_usulan'] = '';
		$program['catatan'] = '';
		$program['catatan_usulan'] = '';
		if (!empty($id_unik)) {
			$sql = $wpdb->prepare("
				SELECT 
					* 
				FROM data_renstra_program_lokal
				WHERE id_unik=%s and
					id_unik_indikator IS NULL and
					active=1 ORDER BY id
			", $id_unik);
			$program = $wpdb->get_results($sql, ARRAY_A);

			if (!empty($program)) {
				foreach ($program as $k => $prog) {
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
						from data_renstra_program_lokal 
						where id_unik_indikator IS NOT NULL
							AND active=1
							AND id_unik=%s
					", $prog['id_unik']));
					$program[$k]['pagu_akumulasi_1_program'] = $pagu->pagu_akumulasi_1;
					$program[$k]['pagu_akumulasi_2_program'] = $pagu->pagu_akumulasi_2;
					$program[$k]['pagu_akumulasi_3_program'] = $pagu->pagu_akumulasi_3;
					$program[$k]['pagu_akumulasi_4_program'] = $pagu->pagu_akumulasi_4;
					$program[$k]['pagu_akumulasi_5_program'] = $pagu->pagu_akumulasi_5;
					$program[$k]['pagu_akumulasi_1_usulan_program'] = $pagu->pagu_akumulasi_1_usulan;
					$program[$k]['pagu_akumulasi_2_usulan_program'] = $pagu->pagu_akumulasi_2_usulan;
					$program[$k]['pagu_akumulasi_3_usulan_program'] = $pagu->pagu_akumulasi_3_usulan;
					$program[$k]['pagu_akumulasi_4_usulan_program'] = $pagu->pagu_akumulasi_4_usulan;
					$program[$k]['pagu_akumulasi_5_usulan_program'] = $pagu->pagu_akumulasi_5_usulan;

					$kegiatan = $wpdb->get_results(
						$wpdb->prepare("
							SELECT 
								id_unik 
							FROM 
								data_renstra_kegiatan_lokal 
							WHERE 
								kode_program=%s AND
								id_unik_indikator IS NULL AND 
								active=1
								", $prog['id_unik']),
						ARRAY_A
					);

					$program[$k]['pagu_akumulasi_1'] = 0;
					$program[$k]['pagu_akumulasi_2'] = 0;
					$program[$k]['pagu_akumulasi_3'] = 0;
					$program[$k]['pagu_akumulasi_4'] = 0;
					$program[$k]['pagu_akumulasi_5'] = 0;
					$program[$k]['pagu_akumulasi_1_usulan'] = 0;
					$program[$k]['pagu_akumulasi_2_usulan'] = 0;
					$program[$k]['pagu_akumulasi_3_usulan'] = 0;
					$program[$k]['pagu_akumulasi_4_usulan'] = 0;
					$program[$k]['pagu_akumulasi_5_usulan'] = 0;

					$kd_all_keg = [];
					foreach ($kegiatan as $key => $keg) {
						$kd_all_keg[] = "'" . $keg['id_unik'] . "'";
					}

					if (!empty($kd_all_keg)) {
						$kd_keg = implode(",", $kd_all_keg);
						$pagu = $wpdb->get_row($wpdb->prepare("
							SELECT 
								coalesce(sum(pagu_1), 0) as pagu_akumulasi_1,
								coalesce(sum(pagu_2), 0) as pagu_akumulasi_2,
								coalesce(sum(pagu_3), 0) as pagu_akumulasi_3,
								coalesce(sum(pagu_4), 0) as pagu_akumulasi_4,
								coalesce(sum(pagu_5), 0) as pagu_akumulasi_5,
								coalesce(sum(pagu_1_usulan), 0) as pagu_akumulasi_1_usulan,
								coalesce(sum(pagu_2_usulan), 0) as pagu_akumulasi_2_usulan,
								coalesce(sum(pagu_3_usulan), 0) as pagu_akumulasi_3_usulan,
								coalesce(sum(pagu_4_usulan), 0) as pagu_akumulasi_4_usulan,
								coalesce(sum(pagu_5_usulan), 0) as pagu_akumulasi_5_usulan
							from data_renstra_sub_kegiatan_lokal 
							where id_unik_indikator IS NULL
								AND kode_kegiatan in (" . $kd_keg . ")
								AND active=1
						"));

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
				}
				$program = $program[0];
			}
		}
		return $program;
	}

	public function get_indikator_program_renstra()
	{

		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					$program = array();
					if ($_POST['type'] == 1) {
						$sql = $wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_program_lokal 
							WHERE 
								id_unik=%s AND 
								id_unik_indikator IS NOT NULL AND 
								active=1 AND
								tahun_anggaran=%d
							ORDER BY id
						", $_POST['kode_program'], $_POST['tahun_anggaran']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
						$program = $this->get_pagu_indikator_program($_POST['kode_program']);
					} else {
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
						'program' => $program,
						'message' => 'Sukses get indikator program'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function submit_indikator_program_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

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
							AND tahun_anggaran=%d
					", $data['indikator_teks_usulan'], $data['kode_program'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks_usulan'] . ' sudah ada!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['kode_program'], $_POST['tahun_anggaran']));

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
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'active' => 1
					];
					$inputs['indikator_usulan'] = $data['indikator_teks_usulan'];
					$inputs['satuan_usulan'] = $data['satuan_usulan'];
					$inputs['target_1_usulan'] = $data['target_1_usulan'];
					$inputs['target_2_usulan'] = $data['target_2_usulan'];
					$inputs['target_3_usulan'] = $data['target_3_usulan'];
					$inputs['target_4_usulan'] = $data['target_4_usulan'];
					$inputs['target_5_usulan'] = $data['target_5_usulan'];
					$inputs['pagu_1_usulan'] = $data['pagu_1_usulan'];
					$inputs['pagu_2_usulan'] = $data['pagu_2_usulan'];
					$inputs['pagu_3_usulan'] = $data['pagu_3_usulan'];
					$inputs['pagu_4_usulan'] = $data['pagu_4_usulan'];
					$inputs['pagu_5_usulan'] = $data['pagu_5_usulan'];
					$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
					$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
					$inputs['catatan_usulan'] = $data['catatan_usulan'];

					if (in_array('administrator', $this->role())) {
						$inputs['indikator'] = !empty($data['indikator_teks']) || $data['indikator_teks'] == 0 ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
						$inputs['satuan'] = !empty($data['satuan']) || $data['satuan'] == 0 ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) || $data['target_1'] == 0 ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) || $data['target_2'] == 0 ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) || $data['target_3'] == 0 ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) || $data['target_4'] == 0 ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) || $data['target_5'] == 0 ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['pagu_1'] = !empty($data['pagu_1']) || $data['pagu_1'] == 0 ? $data['pagu_1'] : $data['pagu_1_usulan'];
						$inputs['pagu_2'] = !empty($data['pagu_2']) || $data['pagu_2'] == 0 ? $data['pagu_2'] : $data['pagu_2_usulan'];
						$inputs['pagu_3'] = !empty($data['pagu_3']) || $data['pagu_3'] == 0 ? $data['pagu_3'] : $data['pagu_3_usulan'];
						$inputs['pagu_4'] = !empty($data['pagu_4']) || $data['pagu_4'] == 0 ? $data['pagu_4'] : $data['pagu_4_usulan'];
						$inputs['pagu_5'] = !empty($data['pagu_5']) || $data['pagu_5'] == 0 ? $data['pagu_5'] : $data['pagu_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) || $data['target_awal'] == 0 ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) || $data['target_akhir'] == 0 ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = $data['catatan'];
					}

					$status = $wpdb->insert('data_renstra_program_lokal', $inputs);

					if ($status === false) {
						$ket = '';
						if (in_array('administrator', $this->role())) {
							$ket = " | query: " . $wpdb->last_query;
						}
						$ket .= " | error: " . $wpdb->last_error;
						throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator program'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function edit_indikator_program_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

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
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function update_indikator_program_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

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
							AND tahun_anggaran=%d
					", $data['indikator_teks_usulan'], $data['id'], $data['kode_program'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks'] . ' sudah ada!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik IS NOT NULL 
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['kode_program'], $_POST['tahun_anggaran']));

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
						'kode_program' => $dataProgram->kode_program,
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
					$inputs['pagu_1_usulan'] = $data['pagu_1_usulan'];
					$inputs['pagu_2_usulan'] = $data['pagu_2_usulan'];
					$inputs['pagu_3_usulan'] = $data['pagu_3_usulan'];
					$inputs['pagu_4_usulan'] = $data['pagu_4_usulan'];
					$inputs['pagu_5_usulan'] = $data['pagu_5_usulan'];
					$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
					$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
					$inputs['catatan_usulan'] = $data['catatan_usulan'];

					if (in_array('administrator', $this->role())) {
						$inputs['indikator'] = !empty($data['indikator_teks']) || $data['indikator_teks'] == 0 ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
						$inputs['satuan'] = !empty($data['satuan']) || $data['satuan'] == 0 ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) || $data['target_1'] == 0 ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) || $data['target_2'] == 0 ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) || $data['target_3'] == 0 ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) || $data['target_4'] == 0 ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) || $data['target_5'] == 0 ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['pagu_1'] = !empty($data['pagu_1']) || $data['pagu_1'] == 0 ? $data['pagu_1'] : $data['pagu_1_usulan'];
						$inputs['pagu_2'] = !empty($data['pagu_2']) || $data['pagu_2'] == 0 ? $data['pagu_2'] : $data['pagu_2_usulan'];
						$inputs['pagu_3'] = !empty($data['pagu_3']) || $data['pagu_3'] == 0 ? $data['pagu_3'] : $data['pagu_3_usulan'];
						$inputs['pagu_4'] = !empty($data['pagu_4']) || $data['pagu_4'] == 0 ? $data['pagu_4'] : $data['pagu_4_usulan'];
						$inputs['pagu_5'] = !empty($data['pagu_5']) || $data['pagu_5'] == 0 ? $data['pagu_5'] : $data['pagu_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) || $data['target_awal'] == 0 ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) || $data['target_akhir'] == 0 ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = $data['catatan'];
					}

					$status = $wpdb->update('data_renstra_program_lokal', $inputs, ['id' => $data['id']]);

					if ($status === false) {
						$ket = '';
						if (in_array('administrator', $this->role())) {
							$ket = " | query: " . $wpdb->last_query;
						}
						$ket .= " | error: " . $wpdb->last_error;
						throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator program'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function delete_indikator_program_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->update('data_renstra_program_lokal', array('active' => 0), array(
						'id' => $_POST['id']
					));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator program',
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	private function verify_indikator_program_renstra(array $data)
	{
		if (empty($data['kode_program'])) {
			throw new Exception('Program wajib dipilih!');
		}

		if (empty($data['indikator_teks_usulan'])) {
			throw new Exception('Indikator usulan program tidak boleh kosong!');
		}

		if (empty($data['satuan_usulan'])) {
			throw new Exception('Satuan usulan indikator program tidak boleh kosong!');
		}

		if ($data['target_awal_usulan'] < 0 || $data['target_awal_usulan'] == '') {
			throw new Exception('Target awal usulan Indikator program tidak boleh kosong atau tidak kurang dari 0!');
		}

		for ($i = 1; $i <= $data['lama_pelaksanaan']; $i++) {
			if ($data['target_' . $i . '_usulan'] < 0 || $data['target_' . $i . '_usulan'] == '') {
				throw new Exception('Target usulan Indikator program tahun ke-' . $i . ' tidak boleh kosong atau tidak kurang dari 0!');
			}

			if ($data['pagu_' . $i . '_usulan'] < 0 || $data['pagu_' . $i . '_usulan'] == '') {
				throw new Exception('Pagu usulan Indikator program tahun ke-' . $i . ' tidak boleh kosong atau tidak kurang dari 0!');
			}
		}

		if ($data['target_akhir_usulan'] < 0 || $data['target_akhir_usulan'] == '') {
			throw new Exception('Target akhir usulan Indikator program tidak boleh kosong atau tidak kurang dari 0!');
		}
	}

	public function get_kegiatan_renstra()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if ($_POST['type'] == 1) {
					$sql = $wpdb->prepare("
						SELECT 
							k.*
						FROM data_renstra_kegiatan_lokal k
						WHERE k.kode_program=%s AND
							k.id_unik IS NOT NULL and
							k.id_unik_indikator IS NULL and
							k.active=1 and
							k.tahun_anggaran=%d
						ORDER BY nama_giat
					", $_POST['kode_program'], $_POST['tahun_anggaran']);
					$kegiatan = $wpdb->get_results($sql, ARRAY_A);
					foreach ($kegiatan as $k => $keg) {

						$pagu = $wpdb->get_row($wpdb->prepare("
							SELECT 
								COALESCE(sum(pagu_1), 0) as pagu_akumulasi_1,
								COALESCE(sum(pagu_2), 0) as pagu_akumulasi_2,
								COALESCE(sum(pagu_3), 0) as pagu_akumulasi_3,
								COALESCE(sum(pagu_4), 0) as pagu_akumulasi_4,
								COALESCE(sum(pagu_5), 0) as pagu_akumulasi_5,
								COALESCE(sum(pagu_1_usulan), 0) as pagu_akumulasi_1_usulan,
								COALESCE(sum(pagu_2_usulan), 0) as pagu_akumulasi_2_usulan,
								COALESCE(sum(pagu_3_usulan), 0) as pagu_akumulasi_3_usulan,
								COALESCE(sum(pagu_4_usulan), 0) as pagu_akumulasi_4_usulan,
								COALESCE(sum(pagu_5_usulan), 0) as pagu_akumulasi_5_usulan
							from data_renstra_kegiatan_lokal 
							where id_unik_indikator IS NOT NULL
								AND active=1
								AND id_unik=%s
						", $keg['id_unik']));
						$kegiatan[$k]['pagu_akumulasi_1'] = $pagu->pagu_akumulasi_1;
						$kegiatan[$k]['pagu_akumulasi_2'] = $pagu->pagu_akumulasi_2;
						$kegiatan[$k]['pagu_akumulasi_3'] = $pagu->pagu_akumulasi_3;
						$kegiatan[$k]['pagu_akumulasi_4'] = $pagu->pagu_akumulasi_4;
						$kegiatan[$k]['pagu_akumulasi_5'] = $pagu->pagu_akumulasi_5;
						$kegiatan[$k]['pagu_akumulasi_1_usulan'] = $pagu->pagu_akumulasi_1_usulan;
						$kegiatan[$k]['pagu_akumulasi_2_usulan'] = $pagu->pagu_akumulasi_2_usulan;
						$kegiatan[$k]['pagu_akumulasi_3_usulan'] = $pagu->pagu_akumulasi_3_usulan;
						$kegiatan[$k]['pagu_akumulasi_4_usulan'] = $pagu->pagu_akumulasi_4_usulan;
						$kegiatan[$k]['pagu_akumulasi_5_usulan'] = $pagu->pagu_akumulasi_5_usulan;

						$pagu = $wpdb->get_row($wpdb->prepare("
							SELECT 
								COALESCE(sum(pagu_1), 0) as pagu_akumulasi_1,
								COALESCE(sum(pagu_2), 0) as pagu_akumulasi_2,
								COALESCE(sum(pagu_3), 0) as pagu_akumulasi_3,
								COALESCE(sum(pagu_4), 0) as pagu_akumulasi_4,
								COALESCE(sum(pagu_5), 0) as pagu_akumulasi_5,
								COALESCE(sum(pagu_1_usulan), 0) as pagu_akumulasi_1_usulan,
								COALESCE(sum(pagu_2_usulan), 0) as pagu_akumulasi_2_usulan,
								COALESCE(sum(pagu_3_usulan), 0) as pagu_akumulasi_3_usulan,
								COALESCE(sum(pagu_4_usulan), 0) as pagu_akumulasi_4_usulan,
								COALESCE(sum(pagu_5_usulan), 0) as pagu_akumulasi_5_usulan
							from data_renstra_sub_kegiatan_lokal 
							where id_unik_indikator IS NULL
								AND kode_kegiatan=%s
								AND active=1
						", $keg['id_unik']));
						$kegiatan[$k]['pagu_akumulasi_1_subkegiatan'] = $pagu->pagu_akumulasi_1;
						$kegiatan[$k]['pagu_akumulasi_2_subkegiatan'] = $pagu->pagu_akumulasi_2;
						$kegiatan[$k]['pagu_akumulasi_3_subkegiatan'] = $pagu->pagu_akumulasi_3;
						$kegiatan[$k]['pagu_akumulasi_4_subkegiatan'] = $pagu->pagu_akumulasi_4;
						$kegiatan[$k]['pagu_akumulasi_5_subkegiatan'] = $pagu->pagu_akumulasi_5;
						$kegiatan[$k]['pagu_akumulasi_1_usulan_subkegiatan'] = $pagu->pagu_akumulasi_1_usulan;
						$kegiatan[$k]['pagu_akumulasi_2_usulan_subkegiatan'] = $pagu->pagu_akumulasi_2_usulan;
						$kegiatan[$k]['pagu_akumulasi_3_usulan_subkegiatan'] = $pagu->pagu_akumulasi_3_usulan;
						$kegiatan[$k]['pagu_akumulasi_4_usulan_subkegiatan'] = $pagu->pagu_akumulasi_4_usulan;
						$kegiatan[$k]['pagu_akumulasi_5_usulan_subkegiatan'] = $pagu->pagu_akumulasi_5_usulan;

						$kegiatan[$k]['pokin'] = $wpdb->get_results($wpdb->prepare("
							SELECT
								*
							FROM data_pokin_renstra
							WHERE id_unik=%s
								AND id_skpd=%d
								AND active=1
								AND tipe=4
								AND tahun_anggaran=%d
						", $keg['id_unik'], $keg['id_unit'], $_POST['tahun_anggaran']), ARRAY_A);
						
						$kegiatan[$k]['satker'] = $wpdb->get_results($wpdb->prepare("
							SELECT
								*
							FROM data_pelaksana_renstra
							WHERE id_unik=%s
								AND id_skpd=%d
								AND active=1
								AND tipe=4
								AND tahun_anggaran=%d
						", $keg['id_unik'], $keg['id_unit'], $_POST['tahun_anggaran']), ARRAY_A);
					}
				} else {
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
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	public function add_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$kode_program = $wpdb->get_var($wpdb->prepare("
						SELECT 
							kode_program 
						FROM data_prog_keg
						WHERE id_program=%d 
					", $_POST['id_program']));

					$where = '';
					$cek_pemda = $this->cek_kab_kot();
					// tahun 2024 sudah menggunakan sipd-ri
					if (
						$cek_pemda['status'] == 1
						&& $tahun_anggaran >= 2024
					) {
						if(!empty($cek_pemda['kabkot']) && !empty($cek_pemda['prov'])){
							$where .= ' AND u.set_kab_kota=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['kabkot'].' OR u.id_daerah_khusus='.$cek_pemda['prov'].')';
						}else if(!empty($cek_pemda['kabkot'])){
							$where .= ' AND u.set_kab_kota=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['kabkot'].')';
						}else if(!empty($cek_pemda['prov'])){
							$where .= ' AND u.set_kab_kota=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['prov'].')';
						}else{
							$where .= ' AND u.set_kab_kota=1 AND u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL';
						}
					} else if (
						$cek_pemda['status'] == 2
						&& $tahun_anggaran >= 2024
					) {
						if(!empty($cek_pemda['kabkot']) && !empty($cek_pemda['prov'])){
							$where .= ' AND u.set_prov=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['kabkot'].' OR u.id_daerah_khusus='.$cek_pemda['prov'].')';
						}else if(!empty($cek_pemda['kabkot'])){
							$where .= ' AND u.set_prov=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['kabkot'].')';
						}else if(!empty($cek_pemda['prov'])){
							$where .= ' AND u.set_prov=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['prov'].')';
						}else{
							$where .= ' AND u.set_prov=1 AND u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL';
						}
					}

					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_prog_keg u
						WHERE u.kode_program=%s
							AND u.tahun_anggaran=%d
							AND u.active=%d
							$where
                        ORDER BY u.id_daerah_khusus DESC 
					", $kode_program, $tahun_anggaran, 1);
					$data = $wpdb->get_results($sql, ARRAY_A);

					$kegiatan = [];
					foreach ($data as $key => $value) {
						if (empty($kegiatan[$value['kode_giat']])) {
							$kegiatan[$value['kode_giat']] = [
								'id' => $value['id_giat'],
								'kegiatan_teks' => $value['nama_giat']
							];
						}
					}

					echo json_encode([
						'status' => true,
						'sql' => $wpdb->last_query,
						'data' => array_values($kegiatan)
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function submit_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_kegiatan_renstra($data);
					if(!empty($data['id_jadwal_wp_sakip'])){
						if (empty($data['pokin-level'])) {
							throw new Exception('Pohon Kinerja tidak boleh kosong!');
						}
						if (empty($data['satker-pelaksana'])) {
							throw new Exception('Satuan Kerja Pelaksana tidak boleh kosong!');
						}
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_kegiatan_lokal
						WHERE id_giat=%d
							AND kode_program=%s
							AND active=1
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['id_kegiatan'], $data['kode_program'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Kegiatan : ' . $data['kegiatan_teks'] . ' sudah ada! id=' . $id_cek);
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NULL 
							AND active=1
							AND tahun_anggaran=%d
					", $data['kode_program'], $_POST['tahun_anggaran']));

					if (empty($dataProgram)) {
						throw new Exception('Program tidak ditemukan!');
					}

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_prog_keg 
						WHERE 
							id_giat=%d AND
							tahun_anggaran=%d
					", $data['id_kegiatan'], $_POST['tahun_anggaran']));

					if (empty($dataKegiatan)) {
						throw new Exception('Kegiatan tidak ditemukan!');
					}

					try {
						$data_kegiatan = array(
							'bidur_lock' => 0,
							'giat_lock' => 0,
							'id_bidang_urusan' => $dataProgram->id_bidang_urusan,
							'id_giat' => $dataKegiatan->id_giat,
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
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'active' => 1
						);
						$wpdb->insert('data_renstra_kegiatan_lokal', $data_kegiatan);

						$wpdb->update("data_pokin_renstra", array("active" => 0), array(
							"id_unik" => $data_kegiatan['id_unik'],
							"tipe" => 4,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						$wpdb->update("data_pelaksana_renstra", array("active" => 0), array(
							"id_unik" => $data_kegiatan['id_unik'],
							"tipe" => 4,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						// cek jika jadwal sakip aktif
						if(!empty($data['id_jadwal_wp_sakip'])){
							$_POST['id_skpd'] = $dataProgram->id_unit;
							$_POST['id_jadwal_wp_sakip'] = $data['id_jadwal_wp_sakip'];
							$pokin_all = $this->get_data_pohon_kinerja(true);
							$new_pokin = array();
							foreach($pokin_all['data'] as $val){
								$new_pokin[$val->id] = $val;
							}
							if(!is_array($data['pokin-level'])){
								$data['pokin-level'] = array($data['pokin-level']);
							}
							foreach($data['pokin-level'] as $id_pokin){
								if(!empty($new_pokin[$id_pokin])){
									$indikator = array();
									foreach($new_pokin[$id_pokin]->indikator as $ind){
										$indikator[] = $ind->label;
									}
									$data_pokin = array(
										"id_pokin" => $id_pokin,
										"level" => $new_pokin[$id_pokin]->level,
										"label" => $new_pokin[$id_pokin]->label,
										"indikator" => implode(', ', $indikator),
										"tipe" => 4,
										"id_unik" => $data_kegiatan['id_unik'],
										"id_skpd" => $dataProgram->id_unit,
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pokin_renstra
										WHERE id_pokin=%d
											AND id_unik=%s
											AND tipe=4
											AND tahun_anggaran=%d
									", $id_pokin, $data_kegiatan['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pokin_renstra", $data_pokin, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pokin_renstra", $data_pokin);
									}
								}
							}

							$satker_all = $this->get_data_satker(true);
							$new_satker = array();
							foreach($satker_all['data'] as $val){
								$new_satker[$val->id] = $val;
							}
							if(!is_array($data['satker-pelaksana'])){
								$data['satker-pelaksana'] = array($data['satker-pelaksana']);
							}
							foreach($data['satker-pelaksana'] as $id_satker){
								if(!empty($new_satker[$id_satker])){
									$data_satker = array(
										"id_satker" => $id_satker,
										"nama_satker" => $new_satker[$id_satker]->nama,
										"tipe" => 4,
										"id_skpd" => $dataProgram->id_unit,
										"id_unik" => $data_kegiatan['id_unik'],
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pelaksana_renstra
										WHERE id_satker=%s
											AND tipe=4
											AND id_unik=%s
											AND tahun_anggaran=%d
									", $id_pokin, $data_kegiatan['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pelaksana_renstra", $data_satker, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pelaksana_renstra", $data_satker);
									}
								}
							}
						}

						echo json_encode([
							'status' => true,
							'message' => 'Sukses simpan kegiatan'
						]);
						exit;
					} catch (Exception $e) {
						throw $e;
					}
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function edit_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_prog_keg
						WHERE 
							id_program=%d AND
							tahun_anggaran=%d
					", $_POST['id_program'], $_POST['tahun_anggaran']);
					$data = $wpdb->get_results($sql, ARRAY_A);

					$kegiatan = [];
					foreach ($data as $key => $value) {
						if (empty($kegiatan[$value['kode_giat']])) {
							$kegiatan[$value['kode_giat']] = [
								'id' => $value['id_giat'],
								'kegiatan_teks' => $value['nama_giat']
							];
						}
					}

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal
						WHERE id=%d
							AND tahun_anggaran=%d
					", $_POST['id_kegiatan'], $_POST['tahun_anggaran']), ARRAY_A);

					$pokin = $wpdb->get_results($wpdb->prepare("
						SELECT
							*
						FROM data_pokin_renstra
						WHERE id_unik=%s
							AND tipe=4
							AND active=1
							AND tahun_anggaran=%d
					", $dataKegiatan['id_unik'], $dataKegiatan['tahun_anggaran']), ARRAY_A);

					$satker = $wpdb->get_results($wpdb->prepare("
						SELECT
							*
						FROM data_pelaksana_renstra
						WHERE id_unik=%s
							AND tipe=4
							AND active=1
							AND tahun_anggaran=%d
					", $dataKegiatan['id_unik'], $dataKegiatan['tahun_anggaran']), ARRAY_A);

					echo json_encode([
						'status' => true,
						'kegiatan' => $dataKegiatan,
						'pokin' => $pokin,
						'satker' => $satker,
						'data' => array_values($kegiatan)
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function update_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_kegiatan_renstra($data);
					if(!empty($data['id_jadwal_wp_sakip'])){
						if (empty($data['pokin-level'])) {
							throw new Exception('Pohon Kinerja tidak boleh kosong!');
						}
						if (empty($data['satker-pelaksana'])) {
							throw new Exception('Satuan Kerja Pelaksana tidak boleh kosong!');
						}
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_kegiatan_lokal
						WHERE id_giat=%d
							AND kode_program=%s
							AND id!=%d
							AND active=1
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['id_kegiatan'], $data['kode_program'], $data['id'], $_POST['tahun_anggaran']));

					$tahun_anggaran_wpsipd = $_POST['tahun_anggaran'];
					if (!empty($id_cek)) {
						$kegiatan = $wpdb->get_row($wpdb->prepare("
							SELECT 
								nama_giat 
							FROM data_prog_keg 
							WHERE id_giat=%d 
								AND tahun_anggaran=%d
						", $data['id_kegiatan'], $tahun_anggaran_wpsipd));
						if (empty($kegiatan)) {
							throw new Exception('Program tidak ditemukan!');
						}
						throw new Exception('Kegiatan : ' . $kegiatan->nama_giat . ' sudah ada!');
					}

					$dataProgram = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_program_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NULL 
							AND active=1
							AND tahun_anggaran=%d
					", $data['kode_program'], $_POST['tahun_anggaran']));

					if (empty($dataProgram)) {
						throw new Exception('Program tidak ditemukan!');
					}

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_prog_keg 
						WHERE 
							id_giat=%d AND
							tahun_anggaran=%d
					", $data['id_kegiatan'], $tahun_anggaran_wpsipd));

					if (empty($dataKegiatan)) {
						throw new Exception('Kegiatan tidak ditemukan!');
					}

					try {

						add_filter('query', array($this, 'wpsipd_query'));

						$wpdb->update('data_renstra_kegiatan_lokal', [
							'bidur_lock' => 0,
							'giat_lock' => 0,
							'id_bidang_urusan' => $dataProgram->id_bidang_urusan,
							'id_giat' => $dataKegiatan->id_giat,
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
							'urut_tujuan' => $dataProgram->urut_tujuan,
							'update_at' => current_time('mysql')
						], [
							'id_unik' => $data['id_unik'], // pake id_unik agar indikator kegiatan ikut terupdate
							'id_unik_indikator' => 'NULL',
							'tahun_anggaran' => $_POST['tahun_anggaran']
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
							'urut_tujuan' => $dataProgram->urut_tujuan,
							'update_at' => current_time('mysql')
						], [
							'id_unik' => $data['id_unik'],
							'id_unik_indikator' => 'NOT NULL',
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						// edit sub kegiatan
						$wpdb->update('data_renstra_sub_kegiatan_lokal', [
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
							'urut_tujuan' => $dataProgram->urut_tujuan,
							'update_at' => current_time('mysql')
						], [
							'kode_kegiatan' => $data['id_unik'],
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						$wpdb->update("data_pokin_renstra", array("active" => 0), array(
							"id_unik" => $data['id_unik'],
							"tipe" => 4,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						$wpdb->update("data_pelaksana_renstra", array("active" => 0), array(
							"id_unik" => $data['id_unik'],
							"tipe" => 4,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						// cek jika jadwal sakip aktif
						if(!empty($data['id_jadwal_wp_sakip'])){
							$_POST['id_skpd'] = $dataProgram->id_unit;
							$_POST['id_jadwal_wp_sakip'] = $data['id_jadwal_wp_sakip'];
							$pokin_all = $this->get_data_pohon_kinerja(true);
							$new_pokin = array();
							foreach($pokin_all['data'] as $val){
								$new_pokin[$val->id] = $val;
							}
							if(!is_array($data['pokin-level'])){
								$data['pokin-level'] = array($data['pokin-level']);
							}
							foreach($data['pokin-level'] as $id_pokin){
								if(!empty($new_pokin[$id_pokin])){
									$indikator = array();
									foreach($new_pokin[$id_pokin]->indikator as $ind){
										$indikator[] = $ind->label;
									}
									$data_pokin = array(
										"id_pokin" => $id_pokin,
										"level" => $new_pokin[$id_pokin]->level,
										"label" => $new_pokin[$id_pokin]->label,
										"indikator" => implode(', ', $indikator),
										"tipe" => 4,
										"id_unik" => $data['id_unik'],
										"id_skpd" => $dataProgram->id_unit,
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pokin_renstra
										WHERE id_pokin=%d
											AND id_unik=%s
											AND tipe=4
											AND tahun_anggaran=%d
									", $id_pokin, $data['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pokin_renstra", $data_pokin, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pokin_renstra", $data_pokin);
									}
								}
							}

							$satker_all = $this->get_data_satker(true);
							$new_satker = array();
							foreach($satker_all['data'] as $val){
								$new_satker[$val->id] = $val;
							}
							if(!is_array($data['satker-pelaksana'])){
								$data['satker-pelaksana'] = array($data['satker-pelaksana']);
							}
							foreach($data['satker-pelaksana'] as $id_satker){
								if(!empty($new_satker[$id_satker])){
									$data_satker = array(
										"id_satker" => $id_satker,
										"nama_satker" => $new_satker[$id_satker]->nama,
										"tipe" => 4,
										"id_skpd" => $dataProgram->id_unit,
										"id_unik" => $data['id_unik'],
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pelaksana_renstra
										WHERE id_satker=%s
											AND tipe=4
											AND id_unik=%s
											AND tahun_anggaran=%d
									", $id_pokin, $data['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pelaksana_renstra", $data_satker, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pelaksana_renstra", $data_satker);
									}
								}
							}
						}

						remove_filter('query', array($this, 'wpsipd_query'));

						echo json_encode([
							'status' => true,
							'message' => 'Sukses ubah kegiatan'
						]);
						exit;
					} catch (Exception $e) {
						throw $e;
					}
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function delete_kegiatan_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE kode_kegiatan=%s 
							AND id_unik IS NOT NULL 
							AND id_unik_indikator IS NULL 
							AND active=1
							AND tahun_anggaran=%d
					", $_POST['id_unik'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception("Kegiatan sudah digunakan oleh sub kegiatan", 1);
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
					", $_POST['id_unik']));

					if (!empty($id_cek)) {
						throw new Exception("Kegiatan sudah digunakan oleh indikator kegiatan", 1);
					}

					$wpdb->update('data_renstra_kegiatan_lokal', array('active' => 0), array(
						'id_unik' => $_POST['id_unik'],
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					$wpdb->update('data_pokin_renstra', array('active' => 0), array(
						'id_unik' => $_POST['id_unik'],
						'tipe' => 4,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					$wpdb->update('data_pelaksana_renstra', array('active' => 0), array(
						'id_unik' => $_POST['id_unik'],
						'tipe' => 4,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus kegiatan'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	private function verify_kegiatan_renstra(array $data)
	{
		if (empty($data['id_kegiatan'])) {
			throw new Exception('Kegiatan wajib dipilih!');
		}
	}

	function get_pagu_indikator_kegiatan($id_unik)
	{
		global $wpdb;
		$kegiatan = array();
		$kegiatan['pagu_akumulasi_1_kegiatan'] = '';
		$kegiatan['pagu_akumulasi_2_kegiatan'] = '';
		$kegiatan['pagu_akumulasi_3_kegiatan'] = '';
		$kegiatan['pagu_akumulasi_4_kegiatan'] = '';
		$kegiatan['pagu_akumulasi_5_kegiatan'] = '';
		$kegiatan['pagu_akumulasi_1_usulan_kegiatan'] = '';
		$kegiatan['pagu_akumulasi_2_usulan_kegiatan'] = '';
		$kegiatan['pagu_akumulasi_3_usulan_kegiatan'] = '';
		$kegiatan['pagu_akumulasi_4_usulan_kegiatan'] = '';
		$kegiatan['pagu_akumulasi_5_usulan_kegiatan'] = '';
		$kegiatan['pagu_akumulasi_1'] = '';
		$kegiatan['pagu_akumulasi_2'] = '';
		$kegiatan['pagu_akumulasi_3'] = '';
		$kegiatan['pagu_akumulasi_4'] = '';
		$kegiatan['pagu_akumulasi_5'] = '';
		$kegiatan['pagu_akumulasi_1_usulan'] = '';
		$kegiatan['pagu_akumulasi_2_usulan'] = '';
		$kegiatan['pagu_akumulasi_3_usulan'] = '';
		$kegiatan['pagu_akumulasi_4_usulan'] = '';
		$kegiatan['pagu_akumulasi_5_usulan'] = '';
		$kegiatan['catatan'] = '';
		$kegiatan['catatan_usulan'] = '';
		if (!empty($id_unik)) {
			$sql = $wpdb->prepare("
				SELECT 
					* 
				FROM data_renstra_kegiatan_lokal
				WHERE id_unik=%s and
					id_unik_indikator IS NULL and
					active=1 ORDER BY id
			", $id_unik);
			$kegiatan = $wpdb->get_results($sql, ARRAY_A);

			if (!empty($kegiatan)) {
				foreach ($kegiatan as $k => $keg) {
					$pagu = $wpdb->get_row($wpdb->prepare("
						SELECT 
							coalesce(sum(pagu_1), 0) as pagu_akumulasi_1,
							coalesce(sum(pagu_2), 0) as pagu_akumulasi_2,
							coalesce(sum(pagu_3), 0) as pagu_akumulasi_3,
							coalesce(sum(pagu_4), 0) as pagu_akumulasi_4,
							coalesce(sum(pagu_5), 0) as pagu_akumulasi_5,
							coalesce(sum(pagu_1_usulan), 0) as pagu_akumulasi_1_usulan,
							coalesce(sum(pagu_2_usulan), 0) as pagu_akumulasi_2_usulan,
							coalesce(sum(pagu_3_usulan), 0) as pagu_akumulasi_3_usulan,
							coalesce(sum(pagu_4_usulan), 0) as pagu_akumulasi_4_usulan,
							coalesce(sum(pagu_5_usulan), 0) as pagu_akumulasi_5_usulan
						from data_renstra_kegiatan_lokal 
						where id_unik=%s 
							AND id_unik_indikator IS NOT NULL
							AND active=1
					", $keg['id_unik']));
					$kegiatan[$k]['pagu_akumulasi_1_kegiatan'] = $pagu->pagu_akumulasi_1;
					$kegiatan[$k]['pagu_akumulasi_2_kegiatan'] = $pagu->pagu_akumulasi_2;
					$kegiatan[$k]['pagu_akumulasi_3_kegiatan'] = $pagu->pagu_akumulasi_3;
					$kegiatan[$k]['pagu_akumulasi_4_kegiatan'] = $pagu->pagu_akumulasi_4;
					$kegiatan[$k]['pagu_akumulasi_5_kegiatan'] = $pagu->pagu_akumulasi_5;
					$kegiatan[$k]['pagu_akumulasi_1_usulan_kegiatan'] = $pagu->pagu_akumulasi_1_usulan;
					$kegiatan[$k]['pagu_akumulasi_2_usulan_kegiatan'] = $pagu->pagu_akumulasi_2_usulan;
					$kegiatan[$k]['pagu_akumulasi_3_usulan_kegiatan'] = $pagu->pagu_akumulasi_3_usulan;
					$kegiatan[$k]['pagu_akumulasi_4_usulan_kegiatan'] = $pagu->pagu_akumulasi_4_usulan;
					$kegiatan[$k]['pagu_akumulasi_5_usulan_kegiatan'] = $pagu->pagu_akumulasi_5_usulan;

					$pagu = $wpdb->get_row($wpdb->prepare("
						SELECT 
							coalesce(sum(pagu_1), 0) as pagu_akumulasi_1,
							coalesce(sum(pagu_2), 0) as pagu_akumulasi_2,
							coalesce(sum(pagu_3), 0) as pagu_akumulasi_3,
							coalesce(sum(pagu_4), 0) as pagu_akumulasi_4,
							coalesce(sum(pagu_5), 0) as pagu_akumulasi_5,
							coalesce(sum(pagu_1_usulan), 0) as pagu_akumulasi_1_usulan,
							coalesce(sum(pagu_2_usulan), 0) as pagu_akumulasi_2_usulan,
							coalesce(sum(pagu_3_usulan), 0) as pagu_akumulasi_3_usulan,
							coalesce(sum(pagu_4_usulan), 0) as pagu_akumulasi_4_usulan,
							coalesce(sum(pagu_5_usulan), 0) as pagu_akumulasi_5_usulan
						from data_renstra_sub_kegiatan_lokal 
						where id_unik_indikator IS NULL
							AND active=1
							AND kode_kegiatan=%s
					", $keg['id_unik']));
					$kegiatan[$k]['pagu_akumulasi_1'] = $pagu->pagu_akumulasi_1;
					$kegiatan[$k]['pagu_akumulasi_2'] = $pagu->pagu_akumulasi_2;
					$kegiatan[$k]['pagu_akumulasi_3'] = $pagu->pagu_akumulasi_3;
					$kegiatan[$k]['pagu_akumulasi_4'] = $pagu->pagu_akumulasi_4;
					$kegiatan[$k]['pagu_akumulasi_5'] = $pagu->pagu_akumulasi_5;
					$kegiatan[$k]['pagu_akumulasi_1_usulan'] = $pagu->pagu_akumulasi_1_usulan;
					$kegiatan[$k]['pagu_akumulasi_2_usulan'] = $pagu->pagu_akumulasi_2_usulan;
					$kegiatan[$k]['pagu_akumulasi_3_usulan'] = $pagu->pagu_akumulasi_3_usulan;
					$kegiatan[$k]['pagu_akumulasi_4_usulan'] = $pagu->pagu_akumulasi_4_usulan;
					$kegiatan[$k]['pagu_akumulasi_5_usulan'] = $pagu->pagu_akumulasi_5_usulan;
				}
				$kegiatan = $kegiatan[0];
			}
		}
		return $kegiatan;
	}

	public function get_indikator_kegiatan_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					if ($_POST['type'] == 1) {
						$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE 
							id_unik=%s AND 
							id_unik_indikator IS NOT NULL AND 
							active=1 AND
							tahun_anggaran=%d
						ORDER BY id
						", $_POST['id_unik'], $_POST['tahun_anggaran']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
						$kegiatan = $this->get_pagu_indikator_kegiatan($_POST['id_unik']);
					} else {
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
						'kegiatan' => $kegiatan,
						'message' => 'Sukses get indikator kegiatan'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function submit_indikator_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

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
							AND tahun_anggaran=%d
					", $data['indikator_teks_usulan'], $data['id_unik'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks_usulan'] . ' sudah ada!');
					}

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['id_unik'], $_POST['tahun_anggaran']));

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
						'tahun_anggaran' => $_POST['tahun_anggaran'],
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

					if (in_array('administrator', $this->role())) {
						$inputs['indikator'] = !empty($data['indikator_teks']) ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
						$inputs['satuan'] = !empty($data['satuan']) || $data['satuan'] == 0 ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) || $data['target_1'] == 0 ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) || $data['target_2'] == 0 ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) || $data['target_3'] == 0 ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) || $data['target_4'] == 0 ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) || $data['target_5'] == 0 ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) || $data['target_awal'] == 0 ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) || $data['target_akhir'] == 0 ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['pagu_1'] = !empty($data['pagu_1']) || $data['pagu_1'] == 0 ? $data['pagu_1'] : $data['pagu_1_usulan'];
						$inputs['pagu_2'] = !empty($data['pagu_2']) || $data['pagu_2'] == 0 ? $data['pagu_2'] : $data['pagu_2_usulan'];
						$inputs['pagu_3'] = !empty($data['pagu_3']) || $data['pagu_3'] == 0 ? $data['pagu_3'] : $data['pagu_3_usulan'];
						$inputs['pagu_4'] = !empty($data['pagu_4']) || $data['pagu_4'] == 0 ? $data['pagu_4'] : $data['pagu_4_usulan'];
						$inputs['pagu_5'] = !empty($data['pagu_5']) || $data['pagu_5'] == 0 ? $data['pagu_5'] : $data['pagu_5_usulan'];
						$inputs['catatan'] = $data['catatan'];
					}

					$status = $wpdb->insert('data_renstra_kegiatan_lokal', $inputs);

					if ($status === false) {
						$ket = '';
						if (in_array('administrator', $this->role())) {
							$ket = " | query: " . $wpdb->last_query;
						}
						$ket .= " | error: " . $wpdb->last_error;
						throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator kegiatan'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function edit_indikator_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

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
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function update_indikator_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

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
							AND tahun_anggaran=%d
					", $data['indikator_teks_usulan'], $data['id_unik'], $data['id'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $data['indikator_teks_usulan'] . ' sudah ada!');
					}

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['id_unik'], $_POST['tahun_anggaran']));

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

					if (in_array('administrator', $this->role())) {
						$inputs['indikator'] = !empty($data['indikator_teks']) || $data['indikator_teks'] == 0 ? $data['indikator_teks'] : $data['indikator_teks_usulan'];
						$inputs['satuan'] = !empty($data['satuan']) || $data['satuan'] == 0 ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) || $data['target_1'] == 0 ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) || $data['target_2'] == 0 ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) || $data['target_3'] == 0 ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) || $data['target_4'] == 0 ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) || $data['target_5'] == 0 ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) || $data['target_awal'] == 0 ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) || $data['target_akhir'] == 0 ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['pagu_1'] = !empty($data['pagu_1']) || $data['pagu_1'] == 0 ? $data['pagu_1'] : $data['pagu_1_usulan'];
						$inputs['pagu_2'] = !empty($data['pagu_2']) || $data['pagu_2'] == 0 ? $data['pagu_2'] : $data['pagu_2_usulan'];
						$inputs['pagu_3'] = !empty($data['pagu_3']) || $data['pagu_3'] == 0 ? $data['pagu_3'] : $data['pagu_3_usulan'];
						$inputs['pagu_4'] = !empty($data['pagu_4']) || $data['pagu_4'] == 0 ? $data['pagu_4'] : $data['pagu_4_usulan'];
						$inputs['pagu_5'] = !empty($data['pagu_5']) || $data['pagu_5'] == 0 ? $data['pagu_5'] : $data['pagu_5_usulan'];
						$inputs['catatan'] = $data['catatan'];
					}

					$status = $wpdb->update('data_renstra_kegiatan_lokal', $inputs, ['id' => $data['id']]);

					if ($status === false) {
						$ket = '';
						if (in_array('administrator', $this->role())) {
							$ket = " | query: " . $wpdb->last_query;
						}
						$ket .= " | error: " . $wpdb->last_error;
						throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator kegiatan'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function delete_indikator_kegiatan_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->update('data_renstra_kegiatan_lokal', array('active' => 0), array(
						'id' => $_POST['id']
					));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator kegiatan'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	private function verify_indikator_kegiatan_renstra(array $data)
	{
		if (empty($data['id_unik'])) {
			throw new Exception('Kegiatan wajib dipilih!');
		}

		if (empty($data['indikator_teks_usulan'])) {
			throw new Exception('Indikator usulan kegiatan tidak boleh kosong!');
		}

		if (empty($data['satuan_usulan'])) {
			throw new Exception('Satuan indikator usulan kegiatan tidak boleh kosong!');
		}

		if ($data['target_awal_usulan'] == '' || $data['target_awal_usulan'] < 0) {
			throw new Exception('Target awal usulan Indikator kegiatan tidak boleh kosong atau kurang dari 0!');
		}

		for ($i = 1; $i <= $data['lama_pelaksanaan']; $i++) {
			if ($data['target_' . $i . '_usulan'] < 0 || $data['target_' . $i . '_usulan'] == '') {
				throw new Exception('Target usulan Indikator kegiatan tahun ke-' . $i . ' tidak boleh kosong atau kurang dari 0!');
			}

			if ($data['pagu_' . $i . '_usulan'] < 0 || $data['pagu_' . $i . '_usulan'] == '') {
				throw new Exception('Pagu usulan Indikator kegiatan tahun ke-' . $i . ' tidak boleh kosong atau kurang dari 0!');
			}
		}

		if ($data['target_akhir_usulan'] < 0 || $data['target_akhir_usulan'] == '') {
			throw new Exception('Target akhir usulan Indikator kegiatan tidak boleh kosong atau kurang dari 0!');
		}
	}

	public function get_bidang_urusan_renstra()
	{
		global $wpdb;
		$ret = array(
			'status'    => 'success',
			'message'   => 'Berhasil get data bidang urusan!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
				$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');

				$join = "";
				$where = "";

				$cek_pemda = $this->cek_kab_kot();
				// tahun 2024 sudah menggunakan sipd-ri
				if (
					$cek_pemda['status'] == 1
					&& $tahun_anggaran >= 2024
				) {
					if(!empty($cek_pemda['kabkot']) && !empty($cek_pemda['prov'])){
						$where .= ' AND u.set_kab_kota=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['kabkot'].' OR u.id_daerah_khusus='.$cek_pemda['prov'].')';
					}else if(!empty($cek_pemda['kabkot'])){
						$where .= ' AND u.set_kab_kota=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['kabkot'].')';
					}else if(!empty($cek_pemda['prov'])){
						$where .= ' AND u.set_kab_kota=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['prov'].')';
					}else{
						$where .= ' AND u.set_kab_kota=1 AND u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL';
					}
				} else if (
					$cek_pemda['status'] == 2
					&& $tahun_anggaran >= 2024
				) {
					if(!empty($cek_pemda['kabkot']) && !empty($cek_pemda['prov'])){
						$where .= ' AND u.set_prov=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['kabkot'].' OR u.id_daerah_khusus='.$cek_pemda['prov'].')';
					}else if(!empty($cek_pemda['kabkot'])){
						$where .= ' AND u.set_prov=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['kabkot'].')';
					}else if(!empty($cek_pemda['prov'])){
						$where .= ' AND u.set_prov=1 AND (u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL OR u.id_daerah_khusus='.$cek_pemda['prov'].')';
					}else{
						$where .= ' AND u.set_prov=1 AND u.id_daerah_khusus=0 OR u.id_daerah_khusus IS NULL';
					}
				}

				if (!empty($_POST['id_unit'])) {
					if ($_POST['type'] == 1) {
						$join .= "
            				LEFT JOIN data_unit as s on s.kode_skpd like CONCAT('%',u.kode_bidang_urusan,'%')
		                        and s.active=1
		                        and s.is_skpd=1
		                        and s.tahun_anggaran=u.tahun_anggaran
		                ";
						$where .= $wpdb->prepare(" and (s.id_skpd=%d or u.kode_bidang_urusan='X.XX')", $_POST['id_unit']);
					}

					// program RENSTRA tidak harus ada di RPJMD - agus 08/09/2025
					/* 
					if ($_POST['relasi_perencanaan'] != '-') {
						if ($_POST['id_tipe_relasi'] == 2) {
							$join .= " INNER JOIN data_rpjmd_program_lokal t on t.id_unit = s.id_skpd";
						} elseif ($_POST['id_tipe_relasi'] == 3) {
							$join .= " INNER JOIN data_rpd_program_lokal t on t.id_unit = s.id_skpd";
						}
					}
					*/
				}

				if (!empty($_POST['type']) && $_POST['type'] == 1) {
					$data = $wpdb->get_results("
                        SELECT
                            u.nama_urusan,
                            u.nama_bidang_urusan,
                            u.nama_program,
                            u.id_program
                        FROM data_prog_keg as u
                        " . $join . "
                        WHERE u.tahun_anggaran=$tahun_anggaran and 
                        	u.active=1
                        	$where
                        GROUP BY u.kode_program
                        ORDER BY u.kode_program ASC, u.id_daerah_khusus DESC 
                    ");
				} else {
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
                        " . $join . "
                        WHERE u.tahun_anggaran=$tahun_anggaran
                        	u.active=1
                        	$where
                        GROUP BY u.kode_program, s.kode_skpd
                        ORDER BY u.kode_program ASC, s.kode_skpd ASC, u.id_daerah_khusus DESC
                    ");
				}
				$ret['data'] = $data;
				if (in_array('administrator', $this->role())) {
					$ret['sql'] = $wpdb->last_query;
				}
			} else {
				$ret = array(
					'status' => 'error',
					'message'   => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message'   => 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	public function copy_usulan_renstra()
	{
		global $wpdb;
		$ret = array(
			'status'    => 'success',
			'message'   => 'Berhasil copy data usulan ke penetapan! Segarkan/refresh halaman ini untuk melihat perubahannya.'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
				if (in_array('administrator', $this->role())) {
					if (empty($_POST['id_unit'])) {
						$sql = "
							SELECT 
								* 
							FROM data_renstra_tujuan_lokal 
							WHERE active=1 
							ORDER BY urut_tujuan
						";
					} else {
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
						if (empty($tujuan_value['id_unik_indikator'])) {
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
								if (empty($sasaran_value['id_unik_indikator'])) {
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
											'pagu_1' => $program_value['pagu_1_usulan'],
											'pagu_2' => $program_value['pagu_2_usulan'],
											'pagu_3' => $program_value['pagu_3_usulan'],
											'pagu_4' => $program_value['pagu_4_usulan'],
											'pagu_5' => $program_value['pagu_5_usulan'],
											'target_akhir' => $program_value['target_akhir_usulan'],
											'catatan' => $program_value['catatan_usulan']
										);
										$wpdb->update('data_renstra_program_lokal', $newData, array(
											'id' => $program_value['id']
										));
										if (empty($program_value['id_unik_indikator'])) {
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

												if (empty($kegiatan_value['id_unik_indikator'])) {
													$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare("
														SELECT 
															*
														FROM data_renstra_sub_kegiatan_lokal
														WHERE
															kode_kegiatan=%s AND
															kode_program=%s AND
															kode_sasaran=%s AND
															kode_tujuan=%s AND
															active=1 ORDER BY id
													", $kegiatan_value['id_unik'], $program_value['id_unik'], $sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);
													foreach ($sub_kegiatan_all as $keySubKegiatan => $sub_kegiatan_value) {
														$newData = array(
															'indikator' => $sub_kegiatan_value['indikator_usulan'],
															'satuan' => $sub_kegiatan_value['satuan_usulan'],
															'target_awal' => $sub_kegiatan_value['target_awal_usulan'],
															'target_1' => $sub_kegiatan_value['target_1_usulan'],
															'target_2' => $sub_kegiatan_value['target_2_usulan'],
															'target_3' => $sub_kegiatan_value['target_3_usulan'],
															'target_4' => $sub_kegiatan_value['target_4_usulan'],
															'target_5' => $sub_kegiatan_value['target_5_usulan'],
															'pagu_1' => $sub_kegiatan_value['pagu_1_usulan'],
															'pagu_2' => $sub_kegiatan_value['pagu_2_usulan'],
															'pagu_3' => $sub_kegiatan_value['pagu_3_usulan'],
															'pagu_4' => $sub_kegiatan_value['pagu_4_usulan'],
															'pagu_5' => $sub_kegiatan_value['pagu_5_usulan'],
															'target_akhir' => $sub_kegiatan_value['target_akhir_usulan'],
															'catatan' => $sub_kegiatan_value['catatan_usulan']
														);
														$wpdb->update('data_renstra_sub_kegiatan_lokal', $newData, array(
															'id' => $sub_kegiatan_value['id']
														));
													}
												}
											}
										}
									}
								}
							}
						}
					}
				} else {
					$ret = array(
						'status' => 'error',
						'message'   => 'Anda tidak punya kewenangan untuk melakukan ini!'
					);
				}
			} else {
				$ret = array(
					'status' => 'error',
					'message'   => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message'   => 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	public function view_laporan_tc27_renstra()
	{
		global $wpdb;
		$ret = array(
			'status'    => 'success',
			'message'   => 'Berhasil generate laporan tc27!'
		);
		$data_all = array(
			'data' => array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {

				if ($_POST['option'] == '-') {
					echo json_encode(array(
						'status'    => 'error',
						'message'   => 'Laporan TC 27 wajib memilih jenis pagu!'
					));
					die();
				}

				$nama_pemda = get_option('_crb_daerah');
				$tahun_anggaran = $_POST['tahun_anggaran'] ?? get_option('_crb_tahun_anggaran_sipd');

				$where_skpd = '';
				if (!empty($_POST['id_unit'])) {
					$where_skpd = "and id_skpd=" . $wpdb->prepare("%d", $_POST['id_unit']);
				}

				$unit = $wpdb->get_results($wpdb->prepare("
					SELECT 
						* 
					FROM data_unit 
					WHERE tahun_anggaran=%d
						" . $where_skpd . "
						AND active=1
					ORDER BY id_skpd ASC
				", $tahun_anggaran), ARRAY_A);

				if (empty($unit)) {
					die('<h1>Data SKPD dengan id_skpd=' . $_POST['id_unit'] . ' dan tahun_anggaran=' . $tahun_anggaran . ' tidak ditemukan!</h1>');
				}

				$judul_skpd = '';
				if (!empty($_POST['id_unit'])) {
					$judul_skpd = $unit[0]['kode_skpd'] . '&nbsp;' . $unit[0]['nama_skpd'] . '<br>';
				}

				$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
					SELECT 
						tahun_anggaran AS awal_renstra,
						(tahun_anggaran+lama_pelaksanaan-1) AS akhir_renstra,
						lama_pelaksanaan,
						status 
					FROM `data_jadwal_lokal` 
						WHERE id_jadwal_lokal=%d", $_POST['id_jadwal_lokal']));

				$_suffix = '';
				$where = '';
				if ($jadwal_lokal->status == 1) {
					$_suffix = '_history';
					$where = 'AND id_jadwal=' . $wpdb->prepare("%d", $_POST['id_jadwal_lokal']);
				}

				$tujuan_all = $wpdb->get_results($wpdb->prepare("
					SELECT 
						* 
					FROM data_renstra_tujuan_lokal" . $_suffix . " 
					WHERE 
						id_unit=%d AND 
						active=1 $where 
					ORDER BY urut_tujuan
				", $_POST['id_unit']), ARRAY_A);

				foreach ($tujuan_all as $keyTujuan => $tujuan_value) {
					if (empty($data_all['data'][$tujuan_value['id_unik']])) {
						$data_all['data'][$tujuan_value['id_unik']] = [
							'id' => $tujuan_value['id'],
							'id_bidang_urusan' => $tujuan_value['id_bidang_urusan'],
							'id_unik' => $tujuan_value['id_unik'],
							'tujuan_teks' => $tujuan_value['tujuan_teks'],
							'nama_bidang_urusan' => $tujuan_value['nama_bidang_urusan'],
							// 'indikator' => array(),
							'data' => array(),
							'status_rpjm' => false
						];
					}

					/*
					if(!empty($tujuan_value['id_unik_indikator'])){
						if(empty($data_all['data'][$tujuan_value['id_unik']]['indikator'][$tujuan_value['id_unik_indikator']])){
							$data_all['data'][$tujuan_value['id_unik']]['indikator'][$tujuan_value['id_unik_indikator']] = [
								'id_unik_indikator' => $tujuan_value['id_unik_indikator'],
								'indikator_teks' => $tujuan_value['indikator_teks'],
								'indikator_teks_usulan' => $tujuan_value['indikator_teks_usulan'],
								'satuan' => $tujuan_value['satuan'],
								'target_1' => $tujuan_value['target_1'],
								'target_2' => $tujuan_value['target_2'],
								'target_3' => $tujuan_value['target_3'],
								'target_4' => $tujuan_value['target_4'],
								'target_5' => $tujuan_value['target_5'],
								'target_awal' => $tujuan_value['target_awal'],
								'target_akhir' => $tujuan_value['target_akhir'],
								'satuan_usulan' => $tujuan_value['satuan_usulan'],
								'target_1_usulan' => $tujuan_value['target_1_usulan'],
								'target_2_usulan' => $tujuan_value['target_2_usulan'],
								'target_3_usulan' => $tujuan_value['target_3_usulan'],
								'target_4_usulan' => $tujuan_value['target_4_usulan'],
								'target_5_usulan' => $tujuan_value['target_5_usulan'],
								'target_awal_usulan' => $tujuan_value['target_awal_usulan'],
								'target_akhir_usulan' => $tujuan_value['target_akhir_usulan'],
							];
						}
					}
					*/

					if (empty($tujuan_value['id_unik_indikator'])) {
						$sasaran_all = $wpdb->get_results($wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_sasaran_lokal" . $_suffix . " 
							WHERE 
								kode_tujuan=%s AND 
								active=1 $where
							ORDER BY urut_sasaran
						", $tujuan_value['id_unik']), ARRAY_A);

						foreach ($sasaran_all as $keySasaran => $sasaran_value) {
							if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']])) {
								$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']] = [
									'id' => $sasaran_value['id'],
									'id_unik' => $sasaran_value['id_unik'],
									'sasaran_teks' => $sasaran_value['sasaran_teks'],
									// 'indikator' => array(),
									'data' => array()
								];
							}

							/*
							if(!empty($sasaran_value['id_unik_indikator'])){
								if(empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['indikator'][$sasaran_value['id_unik_indikator']])){
									$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['indikator'][$sasaran_value['id_unik_indikator']] = [
										'id_unik_indikator' => $sasaran_value['id_unik_indikator'],
										'indikator_teks' => $sasaran_value['indikator_teks'],
										'indikator_teks_usulan' => $sasaran_value['indikator_teks_usulan'],
										'satuan' => $sasaran_value['satuan'],
										'target_1' => $sasaran_value['target_1'],
										'target_2' => $sasaran_value['target_2'],
										'target_3' => $sasaran_value['target_3'],
										'target_4' => $sasaran_value['target_4'],
										'target_5' => $sasaran_value['target_5'],
										'target_awal' => $sasaran_value['target_awal'],
										'target_akhir' => $sasaran_value['target_akhir'],
										'satuan_usulan' => $sasaran_value['satuan_usulan'],
										'target_1_usulan' => $sasaran_value['target_1_usulan'],
										'target_2_usulan' => $sasaran_value['target_2_usulan'],
										'target_3_usulan' => $sasaran_value['target_3_usulan'],
										'target_4_usulan' => $sasaran_value['target_4_usulan'],
										'target_5_usulan' => $sasaran_value['target_5_usulan'],
										'target_awal_usulan' => $sasaran_value['target_awal_usulan'],
										'target_akhir_usulan' => $sasaran_value['target_akhir_usulan'],
									];
								}
							}
							*/

							if (empty($sasaran_value['id_unik_indikator'])) {

								$program_all = $wpdb->get_results($wpdb->prepare("
									SELECT 
										* 
									FROM data_renstra_program_lokal" . $_suffix . " 
									WHERE 
										kode_sasaran=%s AND 
										kode_tujuan=%s AND 
										active=1 $where
									ORDER BY nama_program
								", $sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);

								foreach ($program_all as $keyProgram => $program_value) {
									if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']])) {
										$kode = explode(" ", $program_value['nama_program']);
										$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']] = [
											'id' => $program_value['id'],
											'id_unik' => $program_value['id_unik'],
											'kode' => $kode[0],
											'tujuan_teks' => $tujuan_value['tujuan_teks'],
											'sasaran_teks' => $sasaran_value['sasaran_teks'],
											'program_teks' => $program_value['nama_program'],
											'pagu_1' => 0,
											'pagu_2' => 0,
											'pagu_3' => 0,
											'pagu_4' => 0,
											'pagu_5' => 0,
											'pagu_1_usulan' => 0,
											'pagu_2_usulan' => 0,
											'pagu_3_usulan' => 0,
											'pagu_4_usulan' => 0,
											'pagu_5_usulan' => 0,
											'indikator' => array(),
											'data' => array(),
										];
									}

									if (!empty($program_value['id_unik_indikator'])) {
										if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']])) {
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']] = [
												'id_unik_indikator' => $program_value['id_unik_indikator'],
												'indikator_teks' => $program_value['indikator'],
												'indikator_teks_usulan' => $program_value['indikator_usulan'],
												'satuan' => $program_value['satuan'],
												'target_1' => $program_value['target_1'],
												'target_2' => $program_value['target_2'],
												'target_3' => $program_value['target_3'],
												'target_4' => $program_value['target_4'],
												'target_5' => $program_value['target_5'],
												'target_awal' => $program_value['target_awal'],
												'target_akhir' => $program_value['target_akhir'],
												'satuan_usulan' => $program_value['satuan_usulan'],
												'target_1_usulan' => $program_value['target_1_usulan'],
												'target_2_usulan' => $program_value['target_2_usulan'],
												'target_3_usulan' => $program_value['target_3_usulan'],
												'target_4_usulan' => $program_value['target_4_usulan'],
												'target_5_usulan' => $program_value['target_5_usulan'],
												'target_awal_usulan' => $program_value['target_awal_usulan'],
												'target_akhir_usulan' => $program_value['target_akhir_usulan']
											];
										}
									}

									if (empty($program_value['id_unik_indikator'])) {
										$kegiatan_all = $wpdb->get_results($wpdb->prepare("
											SELECT 
												* 
											FROM data_renstra_kegiatan_lokal" . $_suffix . " 
											WHERE 
												kode_program=%s AND 
												kode_sasaran=%s AND 
												kode_tujuan=%s AND 
												active=1 
												$where
											ORDER BY nama_giat
										", $program_value['id_unik'], $sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);

										foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {

											if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']])) {
												$kode = explode(" ", $kegiatan_value['nama_giat']);
												$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']] = [
													'id' => $kegiatan_value['id'],
													'id_unik' => $kegiatan_value['id_unik'],
													'kode' => $kode[0],
													'kegiatan_teks' => $kegiatan_value['nama_giat'],
													'pagu_1' => 0,
													'pagu_2' => 0,
													'pagu_3' => 0,
													'pagu_4' => 0,
													'pagu_5' => 0,
													'pagu_1_usulan' => 0,
													'pagu_2_usulan' => 0,
													'pagu_3_usulan' => 0,
													'pagu_4_usulan' => 0,
													'pagu_5_usulan' => 0,
													'indikator' => array(),
													'data' => array(),
												];
											}

											if (!empty($kegiatan_value['id_unik_indikator'])) {
												if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])) {
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']] = [
														'id_unik_indikator' => $kegiatan_value['id_unik_indikator'],
														'indikator_teks' => $kegiatan_value['indikator'],
														'indikator_teks_usulan' => $kegiatan_value['indikator_usulan'],
														'satuan' => $kegiatan_value['satuan'],
														'target_1' => $kegiatan_value['target_1'],
														'target_2' => $kegiatan_value['target_2'],
														'target_3' => $kegiatan_value['target_3'],
														'target_4' => $kegiatan_value['target_4'],
														'target_5' => $kegiatan_value['target_5'],
														'target_awal' => $kegiatan_value['target_awal'],
														'target_akhir' => $kegiatan_value['target_akhir'],
														'satuan_usulan' => $kegiatan_value['satuan_usulan'],
														'target_1_usulan' => $kegiatan_value['target_1_usulan'],
														'target_2_usulan' => $kegiatan_value['target_2_usulan'],
														'target_3_usulan' => $kegiatan_value['target_3_usulan'],
														'target_4_usulan' => $kegiatan_value['target_4_usulan'],
														'target_5_usulan' => $kegiatan_value['target_5_usulan'],
														'target_awal_usulan' => $kegiatan_value['target_awal_usulan'],
														'target_akhir_usulan' => $kegiatan_value['target_akhir_usulan']
													];
												}
											}

											if (empty($kegiatan_value['id_unik_indikator'])) {

												$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare("
													SELECT 
														* 
													FROM data_renstra_sub_kegiatan_lokal" . $_suffix . " 
													WHERE 
														kode_kegiatan=%s AND 
														kode_program=%s AND 
														kode_sasaran=%s AND 
														kode_tujuan=%s AND 
														active=1 
														$where
													ORDER BY nama_sub_giat
												", $kegiatan_value['id_unik'], $program_value['id_unik'], $sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);

												foreach ($sub_kegiatan_all as $keySubKegiatan => $sub_kegiatan_value) {

													if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']])) {

														$kode = explode(" ", $sub_kegiatan_value['nama_sub_giat']);

														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']] = [
															'id' => $sub_kegiatan_value['id'],
															'id_unik' => $sub_kegiatan_value['id_unik'],
															'kode' => $kode[0],
															'sub_kegiatan_teks' => $sub_kegiatan_value['nama_sub_giat'],
															'pagu_1' => $sub_kegiatan_value['pagu_1'],
															'pagu_2' => $sub_kegiatan_value['pagu_2'],
															'pagu_3' => $sub_kegiatan_value['pagu_3'],
															'pagu_4' => $sub_kegiatan_value['pagu_4'],
															'pagu_5' => $sub_kegiatan_value['pagu_5'],
															'pagu_1_usulan' => $sub_kegiatan_value['pagu_1_usulan'],
															'pagu_2_usulan' => $sub_kegiatan_value['pagu_2_usulan'],
															'pagu_3_usulan' => $sub_kegiatan_value['pagu_3_usulan'],
															'pagu_4_usulan' => $sub_kegiatan_value['pagu_4_usulan'],
															'pagu_5_usulan' => $sub_kegiatan_value['pagu_5_usulan'],
															'indikator' => array(),
														];

														//kegiatan
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_1'] += $sub_kegiatan_value['pagu_1'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_2'] += $sub_kegiatan_value['pagu_2'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_3'] += $sub_kegiatan_value['pagu_3'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_4'] += $sub_kegiatan_value['pagu_4'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_5'] += $sub_kegiatan_value['pagu_5'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

														//program
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_1'] += $sub_kegiatan_value['pagu_1'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_2'] += $sub_kegiatan_value['pagu_2'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_3'] += $sub_kegiatan_value['pagu_3'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_4'] += $sub_kegiatan_value['pagu_4'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_5'] += $sub_kegiatan_value['pagu_5'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];
													}

													if (!empty($sub_kegiatan_value['id_unik_indikator'])) {
														if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']])) {
															$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']] = [
																'id_unik_indikator' => $sub_kegiatan_value['id_unik_indikator'],
																'indikator_teks' => $sub_kegiatan_value['indikator'],
																'indikator_teks_usulan' => $sub_kegiatan_value['indikator_usulan'],
																'satuan' => $sub_kegiatan_value['satuan'],
																'target_1' => $sub_kegiatan_value['target_1'],
																'target_2' => $sub_kegiatan_value['target_2'],
																'target_3' => $sub_kegiatan_value['target_3'],
																'target_4' => $sub_kegiatan_value['target_4'],
																'target_5' => $sub_kegiatan_value['target_5'],
																'target_awal' => $sub_kegiatan_value['target_awal'],
																'target_akhir' => $sub_kegiatan_value['target_akhir'],
																'satuan_usulan' => $sub_kegiatan_value['satuan_usulan'],
																'target_1_usulan' => $sub_kegiatan_value['target_1_usulan'],
																'target_2_usulan' => $sub_kegiatan_value['target_2_usulan'],
																'target_3_usulan' => $sub_kegiatan_value['target_3_usulan'],
																'target_4_usulan' => $sub_kegiatan_value['target_4_usulan'],
																'target_5_usulan' => $sub_kegiatan_value['target_5_usulan'],
																'target_awal_usulan' => $sub_kegiatan_value['target_awal_usulan'],
																'target_akhir_usulan' => $sub_kegiatan_value['target_akhir_usulan'],
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
					}
				}

				foreach ($data_all['data'] as $tujuan) {
					/*
					$indikator_tujuan = '';
					$target_awal = '';
					$target_1 = '';
					$target_2 = '';
					$target_3 = '';
					$target_4 = '';
					$target_5 = '';
					$target_akhir = '';

					foreach($tujuan['indikator'] as $key => $indikator){
						$indikator_tujuan .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['indikator_teks'] : $indikator['indikator_teks_usulan']).'</div>';
						$target_awal .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_awal'] . " " . $indikator['satuan'] : $indikator['target_awal_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
						$target_1 .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_1'] . " " . $indikator['satuan'] : $indikator['target_1_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
						$target_2 .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_2'] . " " . $indikator['satuan'] : $indikator['target_2_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
						$target_3 .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_3'] . " " . $indikator['satuan'] : $indikator['target_3_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
						$target_4 .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_4'] . " " . $indikator['satuan'] : $indikator['target_4_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
						$target_5 .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_5'] . " " . $indikator['satuan'] : $indikator['target_5_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
						$target_akhir .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_akhir'] . " " . $indikator['satuan'] : $indikator['target_akhir_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
					}
					*/

					foreach ($tujuan['data'] as $sasaran) {
						/*
						$no_sasaran++;
						$indikator_sasaran = '';
						$target_awal = '';
						$target_1 = '';
						$target_2 = '';
						$target_3 = '';
						$target_4 = '';
						$target_5 = '';
						$target_akhir = '';

						foreach($sasaran['indikator'] as $key => $indikator){
							$indikator_sasaran .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['indikator_teks'] : $indikator['indikator_teks_usulan']).'</div>';
							$target_awal .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_awal'] . " " . $indikator['satuan'] : $indikator['target_awal_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
							$target_1 .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_1'] . " " . $indikator['satuan'] : $indikator['target_1_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
							$target_2 .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_2'] . " " . $indikator['satuan'] : $indikator['target_2_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
							$target_3 .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_3'] . " " . $indikator['satuan'] : $indikator['target_3_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
							$target_4 .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_4'] . " " . $indikator['satuan'] : $indikator['target_4_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
							$target_5 .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_5'] . " " . $indikator['satuan'] : $indikator['target_5_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
							$target_akhir .= '<div class="indikator">
								'.(!empty($_POST['option']) ? $indikator['target_akhir'] . " " . $indikator['satuan'] : $indikator['target_akhir_usulan'] . " " . $indikator['satuan_usulan']).'</div>';
						}
						*/

						foreach ($sasaran['data'] as $program) {
							$no_program++;
							$indikator_program = '';
							$target_awal = '';
							$target_1 = '';
							$target_2 = '';
							$target_3 = '';
							$target_4 = '';
							$target_5 = '';
							$target_akhir = '';

							if ($tujuan_teks != $program['tujuan_teks']) {
								$tujuan_teks = $program['tujuan_teks'];
							} else {
								$tujuan_teks = '';
							}

							if ($sasaran_teks != $program['sasaran_teks']) {
								$sasaran_teks = $program['sasaran_teks'];
							} else {
								$sasaran_teks = '';
							}

							foreach ($program['indikator'] as $key => $indikator) {
								$indikator_program .= '<div class="indikator">
									' . (!empty($_POST['option']) ? $indikator['indikator_teks'] : $indikator['indikator_teks_usulan']) . '</div>';
								$target_awal .= '<div class="indikator">
									' . (!empty($_POST['option']) ? $indikator['target_awal'] . " " . $indikator['satuan'] : $indikator['target_awal_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
								$target_1 .= '<div class="indikator">
									' . (!empty($_POST['option']) ? $indikator['target_1'] . " " . $indikator['satuan'] : $indikator['target_1_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
								$target_2 .= '<div class="indikator">
									' . (!empty($_POST['option']) ? $indikator['target_2'] . " " . $indikator['satuan'] : $indikator['target_2_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
								$target_3 .= '<div class="indikator">
									' . (!empty($_POST['option']) ? $indikator['target_3'] . " " . $indikator['satuan'] : $indikator['target_3_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
								$target_4 .= '<div class="indikator">
									' . (!empty($_POST['option']) ? $indikator['target_4'] . " " . $indikator['satuan'] : $indikator['target_4_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
								$target_5 .= '<div class="indikator">
									' . (!empty($_POST['option']) ? $indikator['target_5'] . " " . $indikator['satuan'] : $indikator['target_5_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
								$target_akhir .= '<div class="indikator">
									' . (!empty($_POST['option']) ? $indikator['target_akhir'] . " " . $indikator['satuan'] : $indikator['target_akhir_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
							}

							$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
							$body .= '
									<tr class="tr-program">
										<td class="kiri atas kanan bawah">' . $tujuan_teks . '</td>
										<td class="kiri atas kanan bawah">' . $sasaran_teks . '</td>
										<td class="kiri atas kanan bawah">' . $program['kode'] . '</td>
										<td class="kiri atas kanan bawah">' . $program['program_teks'] . '</td>
										<td class="kiri atas kanan bawah">' . $indikator_program . '</td>';
							for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
								$body .= "<td class=\"kiri atas kanan bawah text_tengah\">" . $target_arr[($i - 1)] . "</td><td class=\"atas kanan bawah text_kanan\">" . $this->_number_format((!empty($_POST['option']) ? $program['pagu_' . $i] : $program['pagu_' . $i . '_usulan'])) . "</td>";
							}
							$body .= '<td class="kiri kiri atas kanan bawah"></td>
									</tr>
							';

							foreach ($program['data'] as $kegiatan) {
								$indikator_kegiatan = '';
								$target_awal = '';
								$target_1 = '';
								$target_2 = '';
								$target_3 = '';
								$target_4 = '';
								$target_5 = '';
								$target_akhir = '';

								foreach ($kegiatan['indikator'] as $key => $indikator) {
									$indikator_kegiatan .= '<div class="indikator">
										' . (!empty($_POST['option']) ? $indikator['indikator_teks'] : $indikator['indikator_teks_usulan']) . '</div>';
									$target_awal .= '<div class="indikator">
										' . (!empty($_POST['option']) ? $indikator['target_awal'] . " " . $indikator['satuan'] : $indikator['target_awal_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
									$target_1 .= '<div class="indikator">
										' . (!empty($_POST['option']) ? $indikator['target_1'] . " " . $indikator['satuan'] : $indikator['target_1_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
									$target_2 .= '<div class="indikator">
										' . (!empty($_POST['option']) ? $indikator['target_2'] . " " . $indikator['satuan'] : $indikator['target_2_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
									$target_3 .= '<div class="indikator">
										' . (!empty($_POST['option']) ? $indikator['target_3'] . " " . $indikator['satuan'] : $indikator['target_3_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
									$target_4 .= '<div class="indikator">
										' . (!empty($_POST['option']) ? $indikator['target_4'] . " " . $indikator['satuan'] : $indikator['target_4_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
									$target_5 .= '<div class="indikator">
										' . (!empty($_POST['option']) ? $indikator['target_5'] . " " . $indikator['satuan'] : $indikator['target_5_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
									$target_akhir .= '<div class="indikator">
										' . (!empty($_POST['option']) ? $indikator['target_akhir'] . " " . $indikator['satuan'] : $indikator['target_akhir_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
								}

								$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
								$body .= '
										<tr class="tr-kegiatan">
											<td class="kiri atas kanan bawah"></td>
											<td class="kiri atas kanan bawah"></td>
											<td class="kiri atas kanan bawah">' . $kegiatan['kode'] . '</td>
											<td class="kiri atas kanan bawah">' . $kegiatan['kegiatan_teks'] . '</td>
											<td class="kiri atas kanan bawah">' . $indikator_kegiatan . '</td>';
								for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
									$body .= "<td class=\"kiri atas kanan bawah text_tengah\">" . $target_arr[($i - 1)] . "</td><td class=\"atas kanan bawah text_kanan\">" . $this->_number_format((!empty($_POST['option']) ? $kegiatan['pagu_' . $i] : $kegiatan['pagu_' . $i . '_usulan'])) . "</td>";
								}
								$body .= '
											<td class="kiri atas kanan bawah"></td>
										</tr>
								';

								foreach ($kegiatan['data'] as $sub_kegiatan) {
									$indikator_sub_kegiatan = '';
									$target_awal = '';
									$target_1 = '';
									$target_2 = '';
									$target_3 = '';
									$target_4 = '';
									$target_5 = '';
									$target_akhir = '';

									foreach ($sub_kegiatan['indikator'] as $key => $indikator) {
										$indikator_sub_kegiatan .= '<div class="indikator">
											' . (!empty($_POST['option']) ? $indikator['indikator_teks'] : $indikator['indikator_teks_usulan']) . '</div>';
										$target_awal .= '<div class="indikator">
											' . (!empty($_POST['option']) ? $indikator['target_awal'] . " " . $indikator['satuan'] : $indikator['target_awal_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
										$target_1 .= '<div class="indikator">
											' . (!empty($_POST['option']) ? $indikator['target_1'] . " " . $indikator['satuan'] : $indikator['target_1_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
										$target_2 .= '<div class="indikator">
											' . (!empty($_POST['option']) ? $indikator['target_2'] . " " . $indikator['satuan'] : $indikator['target_2_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
										$target_3 .= '<div class="indikator">
											' . (!empty($_POST['option']) ? $indikator['target_3'] . " " . $indikator['satuan'] : $indikator['target_3_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
										$target_4 .= '<div class="indikator">
											' . (!empty($_POST['option']) ? $indikator['target_4'] . " " . $indikator['satuan'] : $indikator['target_4_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
										$target_5 .= '<div class="indikator">
											' . (!empty($_POST['option']) ? $indikator['target_5'] . " " . $indikator['satuan'] : $indikator['target_5_usulan'] . " " . $indikator['satuan_usulan']) . '</div>';
										$target_akhir .= '<div class="indikator">
											' . (!empty($_POST['option']) ? $indikator['target_akhir'] . " " . $indikator['satuan'] : $indikator['target_akhir_usulan'] . " " . $indikator['satuan']) . '</div>';
									}

									$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
									$body .= '
											<tr class="tr-sub-kegiatan">
												<td class="kiri atas kanan bawah"></td>
												<td class="kiri atas kanan bawah"></td>
												<td class="kiri atas kanan bawah">' . $sub_kegiatan['kode'] . '</td>
												<td class="kiri atas kanan bawah">' . $sub_kegiatan['sub_kegiatan_teks'] . '</td>
												<td class="kiri atas kanan bawah">' . $indikator_sub_kegiatan . '</td>';
									for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
										$body .= "<td class=\"kiri atas kanan bawah text_tengah\">" . $target_arr[($i - 1)] . "</td>
															<td class=\"atas kanan bawah text_kanan\">" . $this->_number_format((!empty($_POST['option']) ? $sub_kegiatan['pagu_' . $i] : $sub_kegiatan['pagu_' . $i . '_usulan'])) . "</td>";
									}
									$body .= '
												<td class="kiri atas kanan bawah"></td>
											</tr>
									';
								}
							}
						}
					}
				}

				$btnLaporan = '';
				if ($_POST['from'] != 'jadwal') {
					$btnLaporan = '<div class="dropdown" style="margin:30px; text-align:center">
							<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								TC 27 RENSTRA ' . (!empty($_POST['option']) ? ' PENETAPAN' : ' USULAN') . '
							</button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(678px, 36px, 0px);">
								<a class="dropdown-item" href="javascript:laporan(\'tc27\', 1)">TC 27 RENSTRA PENETAPAN</a>
								<a class="dropdown-item" href="javascript:laporan(\'tc27\', 0)">TC 27 RENSTRA USULAN</a>
							</div>
						</div>';
				}

				$html = '
					<style>
						.tr-program {
						    background: #baffba;
						}
						.tr-kegiatan {
						    background: #13d0d03d;
						}
					</style>
					<div id="preview">
						' . $btnLaporan . '
						<h4 style="text-align: center; margin: 0; font-weight: bold;">RENCANA STRATEGIS (RENSTRA)
						<br>' . $judul_skpd . 'Tahun ' . $jadwal_lokal->awal_renstra . ' - ' . $jadwal_lokal->akhir_renstra . ' ' . $nama_pemda . '</h4>
						<table id="table-renstra" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
							<thead>';

				$html .= '<tr>
									<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok kiri" rowspan="3">Tujuan</th>
									<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok" rowspan="3">Sasaran</th>
									<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok" rowspan="3">Kode</th>
									<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok" rowspan="3">Program, Kegiatan, dan Sub Kegiatan</th>
									<th style="width: 400px;" class="atas kanan bawah text_tengah text_blok" rowspan="3">Indikator Kinerja Tujuan, Sasaran, <br>Program(outcome), Kegiatan (output), dan Sub Kegiatan</th>
									<th style="width: 400px;"class="row_head_kinerja atas kanan bawah text_tengah text_blok" colspan="' . (2 * $jadwal_lokal->lama_pelaksanaan) . '">Target Kinerja Program dan Kerangka Pendanaan</th>
									<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok" rowspan="3">Kondisi Kinerja pada akhir periode Renstra Perangkat Daerah</th>
								</tr>';

				$html .= "<tr>";
				for ($i = $jadwal_lokal->awal_renstra; $i <= $jadwal_lokal->akhir_renstra; $i++) {
					$html .= '<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok kiri" colspan="2">' . $i . '</th>';
				}
				$html .= "</tr>";

				$html .= "<tr>";
				for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
					$html .= '<th style="width: 100px;" class="row_head_2 atas kanan bawah text_tengah text_blok">Target</th><th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Rp.</th>';
				}
				$html .= '</tr>';

				$html .= '<tr>
									<th class="atas kanan bawah text_tengah text_blok kiri">1</th>
									<th class="atas kanan bawah text_tengah text_blok">2</th>
									<th class="atas kanan bawah text_tengah text_blok">3</th>
									<th class="atas kanan bawah text_tengah text_blok">4</th>
									<th class="atas kanan bawah text_tengah text_blok">5</th>';

				$target_temp = 6;
				for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
					if ($i != 1) {
						$target_temp = $pagu_temp + 1;
					}
					$pagu_temp = $target_temp + 1;
					$html .= "<th class='atas kanan bawah text_tengah text_blok'>" . $target_temp . "</th><th class='atas kanan bawah text_tengah text_blok'>" . $pagu_temp . "</th>";
				}
				$html .= "<th class='atas kanan bawah text_tengah text_blok'>" . ($pagu_temp + 1) . "</th>
								</tr>";

				$html .= "</thead>
							<tbody>" . $body . "</tbody>
						</table>
					</div>";

				$ret['html'] = $html;
			} else {
				$ret = array(
					'status' => 'error',
					'message'   => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message'   => 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	public function view_rekap_renstra()
	{
		global $wpdb;
		$ret = array(
			'status'    => 'success',
			'message'   => 'Berhasil generate laporan renstra!'
		);
		$data_all = array(
			'data' => array(),
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
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {

				$tahun_anggaran = $_POST['tahun_anggaran'];

				$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
					SELECT 
						d.tahun_anggaran AS awal_renstra,
						(d.tahun_anggaran+d.lama_pelaksanaan-1) AS akhir_renstra,
						d.lama_pelaksanaan,
						d.status,
						d.relasi_perencanaan,
						(SELECT status FROM `data_jadwal_lokal` WHERE id_jadwal_lokal=d.relasi_perencanaan) AS status_rpjm, 
						(SELECT id_tipe FROM `data_jadwal_lokal` WHERE id_jadwal_lokal=d.relasi_perencanaan) AS id_tipe_relasi 
					FROM `data_jadwal_lokal` d
						WHERE id_jadwal_lokal=%d", $_POST['id_jadwal_lokal']));

				$_suffix = '';
				$where = '';
				if ($jadwal_lokal->status == 1) {
					$_suffix = '_history';
					$where = 'AND id_jadwal=' . $wpdb->prepare("%d", $_POST['id_jadwal_lokal']);
				}

				$_suffix_rpjmd = '';
				if ($jadwal_lokal->status_rpjm == 1) {
					$_suffix_rpjmd = '_history';
					$where_rpjm = 'AND id_jadwal=' . $jadwal_lokal->relasi_perencanaan;
				}


				$nama_tipe_relasi = 'RPJMD / RPD';
				switch ($jadwal_lokal->id_tipe_relasi) {
					case '2':
						$nama_tipe_relasi = 'RPJMD';
						break;

					case '3':
						$nama_tipe_relasi = 'RPD';
						break;
				}

				$where_skpd = '';
				if (!empty($_POST['id_unit'])) {
					$where_skpd = "and id_skpd=" . $wpdb->prepare("%d", $_POST['id_unit']);
				}

				$sql = $wpdb->prepare("
					SELECT 
						* 
					FROM data_unit 
					WHERE tahun_anggaran=%d
						" . $where_skpd . "
						AND active=1
					ORDER BY id_skpd ASC
				", $tahun_anggaran);

				$unit = $wpdb->get_results($sql, ARRAY_A);

				if (empty($unit)) {
					die('<h1>Data SKPD dengan id_skpd=' . $_POST['id_unit'] . ' dan tahun_anggaran=' . $tahun_anggaran . ' tidak ditemukan!</h1>');
				}

				$judul_skpd = '';
				if (!empty($_POST['id_unit'])) {
					$judul_skpd = $unit[0]['kode_skpd'] . '&nbsp;' . $unit[0]['nama_skpd'] . '<br>';
				}
				$nama_pemda = get_option('_crb_daerah');

				$body = '';
				$data_all = array(
					'data' => array()
				);

				$tujuan_all = $wpdb->get_results($wpdb->prepare("
					SELECT 
						* 
					FROM data_renstra_tujuan_lokal" . $_suffix . " 
					WHERE 
						id_unit=%d AND 
						active=1 $where 
					ORDER BY urut_tujuan
				", $_POST['id_unit']), ARRAY_A);

				foreach ($tujuan_all as $keyTujuan => $tujuan_value) {
					if (empty($data_all['data'][$tujuan_value['id_unik']])) {
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

						if (!empty($tujuan_value['kode_sasaran_rpjm']) && $jadwal_lokal->relasi_perencanaan != '-') {
							$table = 'data_rpjmd_sasaran_lokal';
							switch ($jadwal_lokal->id_tipe_relasi) {
								case '2':
									$table = 'data_rpjmd_sasaran_lokal' . $_suffix_rpjmd;
									break;

								case '3':
									$table = 'data_rpd_sasaran_lokal' . $_suffix_rpjmd;
									break;
							}

							$sasaran_rpjm = $wpdb->get_var("
								SELECT DISTINCT
									sasaran_teks
								FROM " . $table . " 
								WHERE id_unik='{$tujuan_value['kode_sasaran_rpjm']}'
									AND active=1 $where_rpjm
							");
							if (!empty($sasaran_rpjm)) {
								$data_all['data'][$tujuan_value['id_unik']]['status_rpjm'] = true;
								$data_all['data'][$tujuan_value['id_unik']]['sasaran_rpjm'] = $sasaran_rpjm;
							}
						}
					}

					$tujuan_ids[$tujuan_value['id_unik']] = "'" . $tujuan_value['id_unik'] . "'";

					if (!empty($tujuan_value['id_unik_indikator'])) {
						if (empty($data_all['data'][$tujuan_value['id_unik']]['indikator'][$tujuan_value['id_unik_indikator']])) {
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

					if (empty($tujuan_value['id_unik_indikator'])) {
						$sasaran_all = $wpdb->get_results($wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_sasaran_lokal" . $_suffix . " 
							WHERE 
								kode_tujuan=%s AND 
								active=1 $where 
							ORDER BY urut_sasaran
						", $tujuan_value['id_unik']), ARRAY_A);

						foreach ($sasaran_all as $keySasaran => $sasaran_value) {
							if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']])) {
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

							$sasaran_ids[$sasaran_value['id_unik']] = "'" . $sasaran_value['id_unik'] . "'";

							if (!empty($sasaran_value['id_unik_indikator'])) {
								if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['indikator'][$sasaran_value['id_unik_indikator']])) {
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

							if (empty($sasaran_value['id_unik_indikator'])) {

								$program_all = $wpdb->get_results($wpdb->prepare(
									"
										SELECT 
											* 
										FROM data_renstra_program_lokal" . $_suffix . " 
										WHERE 
											kode_sasaran=%s AND 
											kode_tujuan=%s AND 
											active=1 $where 
										ORDER BY nama_program",
									$sasaran_value['id_unik'],
									$tujuan_value['id_unik']
								), ARRAY_A);

								foreach ($program_all as $keyProgram => $program_value) {
									if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']])) {
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
											'pagu_akumulasi_1_usulan' => 0,
											'pagu_akumulasi_2_usulan' => 0,
											'pagu_akumulasi_3_usulan' => 0,
											'pagu_akumulasi_4_usulan' => 0,
											'pagu_akumulasi_5_usulan' => 0,
											'pagu_akumulasi_indikator_1' => 0,
											'pagu_akumulasi_indikator_2' => 0,
											'pagu_akumulasi_indikator_3' => 0,
											'pagu_akumulasi_indikator_4' => 0,
											'pagu_akumulasi_indikator_5' => 0,
											'pagu_akumulasi_indikator_1_usulan' => 0,
											'pagu_akumulasi_indikator_2_usulan' => 0,
											'pagu_akumulasi_indikator_3_usulan' => 0,
											'pagu_akumulasi_indikator_4_usulan' => 0,
											'pagu_akumulasi_indikator_5_usulan' => 0,
											'indikator' => array(),
											'data' => array()
										];
									}

									$program_ids[$program_value['id_unik']] = "'" . $program_value['id_unik'] . "'";

									if (!empty($program_value['id_unik_indikator'])) {
										if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['indikator'][$program_value['id_unik_indikator']])) {

											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_1'] += $program_value['pagu_1'];
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_2'] += $program_value['pagu_2'];
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_3'] += $program_value['pagu_3'];
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_4'] += $program_value['pagu_4'];
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_5'] += $program_value['pagu_5'];
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_1_usulan'] += $program_value['pagu_1_usulan'];
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_2_usulan'] += $program_value['pagu_2_usulan'];
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_3_usulan'] += $program_value['pagu_3_usulan'];
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_4_usulan'] += $program_value['pagu_4_usulan'];
											$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_indikator_5_usulan'] += $program_value['pagu_5_usulan'];

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

									if (empty($program_value['id_unik_indikator'])) {
										$kegiatan_all = $wpdb->get_results($wpdb->prepare(
											"
											SELECT 
												* 
											FROM data_renstra_kegiatan_lokal" . $_suffix . " 
											WHERE 
												kode_program=%s AND 
												kode_sasaran=%s AND 
												kode_tujuan=%s AND 
												active=1 $where 
											ORDER BY nama_giat",
											$program_value['id_unik'],
											$sasaran_value['id_unik'],
											$tujuan_value['id_unik']
										), ARRAY_A);

										foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {

											if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']])) {

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
													'pagu_akumulasi_indikator_1' => 0,
													'pagu_akumulasi_indikator_2' => 0,
													'pagu_akumulasi_indikator_3' => 0,
													'pagu_akumulasi_indikator_4' => 0,
													'pagu_akumulasi_indikator_5' => 0,
													'pagu_akumulasi_indikator_1_usulan' => 0,
													'pagu_akumulasi_indikator_2_usulan' => 0,
													'pagu_akumulasi_indikator_3_usulan' => 0,
													'pagu_akumulasi_indikator_4_usulan' => 0,
													'pagu_akumulasi_indikator_5_usulan' => 0,
													'indikator' => array(),
													'data' => array()
												];
											}
											$kegiatan_ids[$kegiatan_value['id_unik']] = "'" . $kegiatan_value['id_unik'] . "'";

											if (!empty($kegiatan_value['id_unik_indikator'])) {
												if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['indikator'][$kegiatan_value['id_unik_indikator']])) {

													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_1'] += $kegiatan_value['pagu_1'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_2'] += $kegiatan_value['pagu_2'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_3'] += $kegiatan_value['pagu_3'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_4'] += $kegiatan_value['pagu_4'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_5'] += $kegiatan_value['pagu_5'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_1_usulan'] += $kegiatan_value['pagu_1_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_2_usulan'] += $kegiatan_value['pagu_2_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_3_usulan'] += $kegiatan_value['pagu_3_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_4_usulan'] += $kegiatan_value['pagu_4_usulan'];
													$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_indikator_5_usulan'] += $kegiatan_value['pagu_5_usulan'];

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

											if (empty($kegiatan_value['id_unik_indikator'])) {
												$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare(
													"
													SELECT 
														* 
													FROM data_renstra_sub_kegiatan_lokal 
													WHERE 
														kode_kegiatan=%s AND 
														kode_program=%s AND 
														kode_sasaran=%s AND 
														kode_tujuan=%s AND 
														active=1 
													ORDER BY nama_sub_giat",
													$kegiatan_value['id_unik'],
													$program_value['id_unik'],
													$sasaran_value['id_unik'],
													$tujuan_value['id_unik']
												), ARRAY_A);

												foreach ($sub_kegiatan_all as $keySubKegiatan => $sub_kegiatan_value) {

													if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']])) {

														$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
														$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
														$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
														$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
														$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
														$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

														$data_all['pagu_akumulasi_1'] += $sub_kegiatan_value['pagu_1'];
														$data_all['pagu_akumulasi_2'] += $sub_kegiatan_value['pagu_2'];
														$data_all['pagu_akumulasi_3'] += $sub_kegiatan_value['pagu_3'];
														$data_all['pagu_akumulasi_4'] += $sub_kegiatan_value['pagu_4'];
														$data_all['pagu_akumulasi_5'] += $sub_kegiatan_value['pagu_5'];
														$data_all['pagu_akumulasi_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
														$data_all['pagu_akumulasi_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
														$data_all['pagu_akumulasi_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
														$data_all['pagu_akumulasi_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
														$data_all['pagu_akumulasi_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

														$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']] = [
															'id' => $sub_kegiatan_value['id'],
															'id_unik' => $sub_kegiatan_value['id_unik'],
															'sub_kegiatan_teks' => $sub_kegiatan_value['nama_sub_giat'],
															'catatan' => $sub_kegiatan_value['catatan'],
															'catatan_usulan' => $sub_kegiatan_value['catatan_usulan'],
															'pagu_1' => $sub_kegiatan_value['pagu_1'],
															'pagu_2' => $sub_kegiatan_value['pagu_2'],
															'pagu_3' => $sub_kegiatan_value['pagu_3'],
															'pagu_4' => $sub_kegiatan_value['pagu_4'],
															'pagu_5' => $sub_kegiatan_value['pagu_5'],
															'pagu_1_usulan' => $sub_kegiatan_value['pagu_1_usulan'],
															'pagu_2_usulan' => $sub_kegiatan_value['pagu_2_usulan'],
															'pagu_3_usulan' => $sub_kegiatan_value['pagu_3_usulan'],
															'pagu_4_usulan' => $sub_kegiatan_value['pagu_4_usulan'],
															'pagu_5_usulan' => $sub_kegiatan_value['pagu_5_usulan'],
															'id_sub_unit' => $sub_kegiatan_value['id_sub_unit'],
															'nama_sub_unit' => $sub_kegiatan_value['nama_sub_unit'],
															'indikator' => array(),
														];
													}
													$sub_kegiatan_ids[$sub_kegiatan_value['id_unik']] = "'" . $sub_kegiatan_value['id_unik'] . "'";

													if (!empty($sub_kegiatan_value['id_unik_indikator'])) {
														if (empty($data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']])) {

															$data_all['data'][$tujuan_value['id_unik']]['data'][$sasaran_value['id_unik']]['data'][$program_value['id_unik']]['data'][$kegiatan_value['id_unik']]['data'][$sub_kegiatan_value['id_unik']]['indikator'][$sub_kegiatan_value['id_unik_indikator']] = [

																'id_unik_indikator' => $sub_kegiatan_value['id_unik_indikator'],
																'indikator_teks' => $sub_kegiatan_value['indikator'],
																'satuan' => $sub_kegiatan_value['satuan'],
																'target_1' => $sub_kegiatan_value['target_1'],
																'target_2' => $sub_kegiatan_value['target_2'],
																'target_3' => $sub_kegiatan_value['target_3'],
																'target_4' => $sub_kegiatan_value['target_4'],
																'target_5' => $sub_kegiatan_value['target_5'],
																'target_awal' => $sub_kegiatan_value['target_awal'],
																'target_akhir' => $sub_kegiatan_value['target_akhir'],
																'catatan_indikator' => $sub_kegiatan_value['catatan'],
																'indikator_teks_usulan' => $sub_kegiatan_value['indikator_usulan'],
																'satuan_usulan' => $sub_kegiatan_value['satuan_usulan'],
																'target_1_usulan' => $sub_kegiatan_value['target_1_usulan'],
																'target_2_usulan' => $sub_kegiatan_value['target_2_usulan'],
																'target_3_usulan' => $sub_kegiatan_value['target_3_usulan'],
																'target_4_usulan' => $sub_kegiatan_value['target_4_usulan'],
																'target_5_usulan' => $sub_kegiatan_value['target_5_usulan'],
																'target_awal_usulan' => $sub_kegiatan_value['target_awal_usulan'],
																'target_akhir_usulan' => $sub_kegiatan_value['target_akhir_usulan'],
																'catatan_indikator_usulan' => $sub_kegiatan_value['catatan_usulan']
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
					foreach ($tujuan['indikator'] as $key => $indikator) {
						$indikator_tujuan .= '<div class="indikator">' . $indikator['indikator_teks'] . '</div>';
						$target_awal .= '<div class="indikator">' . $indikator['target_awal'] . '</div>';
						$target_1 .= '<div class="indikator">' . $indikator['target_1'] . '</div>';
						$target_2 .= '<div class="indikator">' . $indikator['target_2'] . '</div>';
						$target_3 .= '<div class="indikator">' . $indikator['target_3'] . '</div>';
						$target_4 .= '<div class="indikator">' . $indikator['target_4'] . '</div>';
						$target_5 .= '<div class="indikator">' . $indikator['target_5'] . '</div>';
						$target_akhir .= '<div class="indikator">' . $indikator['target_akhir'] . '</div>';
						$satuan .= '<div class="indikator">' . $indikator['satuan'] . '</div>';
						$catatan_indikator .= '<div class="indikator">' . $indikator['catatan_indikator'] . '</div>';
						$indikator_tujuan_usulan .= '<div class="indikator">' . $indikator['indikator_teks_usulan'] . '</div>';
						$target_awal_usulan .= '<div class="indikator">' . $indikator['target_awal_usulan'] . '</div>';
						$target_1_usulan .= '<div class="indikator">' . $indikator['target_1_usulan'] . '</div>';
						$target_2_usulan .= '<div class="indikator">' . $indikator['target_2_usulan'] . '</div>';
						$target_3_usulan .= '<div class="indikator">' . $indikator['target_3_usulan'] . '</div>';
						$target_4_usulan .= '<div class="indikator">' . $indikator['target_4_usulan'] . '</div>';
						$target_5_usulan .= '<div class="indikator">' . $indikator['target_5_usulan'] . '</div>';
						$target_akhir_usulan .= '<div class="indikator">' . $indikator['target_akhir_usulan'] . '</div>';
						$satuan_usulan .= '<div class="indikator">' . $indikator['satuan_usulan'] . '</div>';
						$catatan_indikator_usulan .= '<div class="indikator">' . $indikator['catatan_indikator_usulan'] . '</div>';
					}

					$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
					$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
					$sasaran_rpjm = '';
					if (!empty($tujuan['sasaran_rpjm'])) {
						$sasaran_rpjm = $tujuan['sasaran_rpjm'];
					}
					$body .= '
							<tr class="tr-tujuan">
								<td class="kiri atas kanan bawah' . $bg_rpjm . '">' . $no_tujuan . '</td>
								<td class="atas kanan bawah' . $bg_rpjm . '">' . $sasaran_rpjm . '</td>
								<td class="atas kanan bawah">' . $tujuan['nama_bidang_urusan'] . '</td>
								<td class="atas kanan bawah">' . $tujuan['tujuan_teks'] . '</td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah">' . $indikator_tujuan . '</td>
								<td class="atas kanan bawah text_tengah">' . $target_awal . '</td>';
					for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
						$body .= "<td class=\"atas kanan bawah text_tengah\">" . $target_arr[$i] . "</td><td class=\"atas kanan bawah text_kanan\"><b>(" . $this->_number_format($tujuan['pagu_akumulasi_' . ($i + 1)]) . ")</b></td>";
					}
					$body .= '<td class="atas kanan bawah text_tengah">' . $target_akhir . '</td>
								<td class="atas kanan bawah">' . $satuan . '</td>
								<td class="atas kanan bawah"></td>
								<td class="atas kanan bawah text_tengah">' . $tujuan['urut_tujuan'] . '</td>
								<td class="atas kanan bawah">' . $tujuan['catatan'] . '</td>
								<td class="atas kanan bawah">' . $catatan_indikator . '</td>
								<td class="atas kanan bawah td-usulan">' . $indikator_tujuan_usulan . '</td>
								<td class="atas kanan bawah text_tengah td-usulan">' . $target_awal_usulan . '</td>';
					for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
						$body .= "<td class=\"atas kanan bawah text_tengah td-usulan\">" . $target_arr_usulan[$i] . "</td><td class=\"atas kanan bawah text_kanan td-usulan\"><b>(" . $this->_number_format($tujuan['pagu_akumulasi_' . ($i + 1) . '_usulan']) . ")</b></td>";
					}
					$body .= '<td class="atas kanan bawah text_tengah td-usulan">' . $target_akhir_usulan . '</td>
								<td class="atas kanan bawah text_tengah td-usulan">' . $satuan_usulan . '</td>
								<td class="atas kanan bawah td-usulan">' . $tujuan['catatan_usulan'] . '</td>
								<td class="atas kanan bawah td-usulan">' . $catatan_indikator_usulan . '</td>
								<td class="atas kanan bawah"></td>
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
						foreach ($sasaran['indikator'] as $key => $indikator) {
							$indikator_sasaran .= '<div class="indikator">' . $indikator['indikator_teks'] . '</div>';
							$target_awal .= '<div class="indikator">' . $indikator['target_awal'] . '</div>';
							$target_1 .= '<div class="indikator">' . $indikator['target_1'] . '</div>';
							$target_2 .= '<div class="indikator">' . $indikator['target_2'] . '</div>';
							$target_3 .= '<div class="indikator">' . $indikator['target_3'] . '</div>';
							$target_4 .= '<div class="indikator">' . $indikator['target_4'] . '</div>';
							$target_5 .= '<div class="indikator">' . $indikator['target_5'] . '</div>';
							$target_akhir .= '<div class="indikator">' . $indikator['target_akhir'] . '</div>';
							$satuan .= '<div class="indikator">' . $indikator['satuan'] . '</div>';
							$catatan_indikator .= '<div class="indikator">' . $indikator['catatan_indikator'] . '</div>';
							$indikator_sasaran_usulan .= '<div class="indikator">' . $indikator['indikator_teks_usulan'] . '</div>';
							$target_awal_usulan .= '<div class="indikator">' . $indikator['target_awal_usulan'] . '</div>';
							$target_1_usulan .= '<div class="indikator">' . $indikator['target_1_usulan'] . '</div>';
							$target_2_usulan .= '<div class="indikator">' . $indikator['target_2_usulan'] . '</div>';
							$target_3_usulan .= '<div class="indikator">' . $indikator['target_3_usulan'] . '</div>';
							$target_4_usulan .= '<div class="indikator">' . $indikator['target_4_usulan'] . '</div>';
							$target_5_usulan .= '<div class="indikator">' . $indikator['target_5_usulan'] . '</div>';
							$target_akhir_usulan .= '<div class="indikator">' . $indikator['target_akhir_usulan'] . '</div>';
							$satuan_usulan .= '<div class="indikator">' . $indikator['satuan_usulan'] . '</div>';
							$catatan_indikator_usulan .= '<div class="indikator">' . $indikator['catatan_indikator_usulan'] . '</div>';
						}

						$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
						$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
						$body .= '
								<tr class="tr-sasaran">
									<td class="kiri atas kanan bawah' . $bg_rpjm . '">' . $no_tujuan . "." . $no_sasaran . '</td>
									<td class="atas kanan bawah' . $bg_rpjm . '"></td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah">' . $sasaran['sasaran_teks'] . '</td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah">' . $indikator_sasaran . '</td>
									<td class="atas kanan bawah text_tengah">' . $target_awal . '</td>';
						for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
							$body .= "<td class=\"atas kanan bawah text_tengah\">" . $target_arr[$i] . "</td><td class=\"atas kanan bawah text_kanan\"><b>(" . $this->_number_format($sasaran['pagu_akumulasi_' . ($i + 1)]) . ")</b></td>";
						}
						$body .= '<td class="atas kanan bawah text_tengah">' . $target_akhir . '</td>
									<td class="atas kanan bawah">' . $satuan . '</td>
									<td class="atas kanan bawah"></td>
									<td class="atas kanan bawah text_tengah">' . $sasaran['urut_sasaran'] . '</td>
									<td class="atas kanan bawah">' . $sasaran['catatan'] . '</td>
									<td class="atas kanan bawah">' . $catatan_indikator . '</td>
									<td class="atas kanan bawah td-usulan">' . $indikator_sasaran_usulan . '</td>
									<td class="atas kanan bawah text_tengah td-usulan">' . $target_awal_usulan . '</td>';
						for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
							$body .= "<td class=\"atas kanan bawah text_tengah td-usulan\">" . $target_arr_usulan[$i] . "</td><td class=\"atas kanan bawah text_kanan td-usulan\"><b>(" . $this->_number_format($sasaran['pagu_akumulasi_' . ($i + 1) . '_usulan']) . ")</b></td>";
						}
						$body .= '<td class="atas kanan bawah text_tengah td-usulan">' . $target_akhir_usulan . '</td>
									<td class="atas kanan bawah td-usulan">' . $satuan_usulan . '</td>
									<td class="atas kanan bawah td-usulan">' . $sasaran['catatan_usulan'] . '</td>
									<td class="atas kanan bawah td-usulan">' . $catatan_indikator_usulan . '</td>
									<td class="atas kanan bawah"></td>
								</tr>
						';

						$no_program = 0;
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
							foreach ($program['indikator'] as $key => $indikator) {
								$indikator_program .= '<div class="indikator">' . $indikator['indikator_teks'] . '</div>';
								$target_awal .= '<div class="indikator">' . $indikator['target_awal'] . '</div>';
								$target_1 .= '<div class="indikator">' . $indikator['target_1'] . '</div>';
								$pagu_1 .= '<div class="indikator">' . $this->_number_format($indikator['pagu_1']) . '</div>';
								$target_2 .= '<div class="indikator">' . $indikator['target_2'] . '</div>';
								$pagu_2 .= '<div class="indikator">' . $this->_number_format($indikator['pagu_2']) . '</div>';
								$target_3 .= '<div class="indikator">' . $indikator['target_3'] . '</div>';
								$pagu_3 .= '<div class="indikator">' . $this->_number_format($indikator['pagu_3']) . '</div>';
								$target_4 .= '<div class="indikator">' . $indikator['target_4'] . '</div>';
								$pagu_4 .= '<div class="indikator">' . $this->_number_format($indikator['pagu_4']) . '</div>';
								$target_5 .= '<div class="indikator">' . $indikator['target_5'] . '</div>';
								$pagu_5 .= '<div class="indikator">' . $this->_number_format($indikator['pagu_5']) . '</div>';
								$target_akhir .= '<div class="indikator">' . $indikator['target_akhir'] . '</div>';
								$satuan .= '<div class="indikator">' . $indikator['satuan'] . '</div>';
								$catatan_indikator .= '<div class="indikator">' . $indikator['catatan_indikator'] . '</div>';
								$indikator_program_usulan .= '<div class="indikator">' . $indikator['indikator_teks_usulan'] . '</div>';
								$target_awal_usulan .= '<div class="indikator">' . $indikator['target_awal_usulan'] . '</div>';
								$target_1_usulan .= '<div class="indikator">' . $indikator['target_1_usulan'] . '</div>';
								$pagu_1_usulan .= '<div class="indikator">' . $this->_number_format($indikator['pagu_1_usulan']) . '</div>';
								$target_2_usulan .= '<div class="indikator">' . $indikator['target_2_usulan'] . '</div>';
								$pagu_2_usulan .= '<div class="indikator">' . $this->_number_format($indikator['pagu_2_usulan']) . '</div>';
								$target_3_usulan .= '<div class="indikator">' . $indikator['target_3_usulan'] . '</div>';
								$pagu_3_usulan .= '<div class="indikator">' . $this->_number_format($indikator['pagu_3_usulan']) . '</div>';
								$target_4_usulan .= '<div class="indikator">' . $indikator['target_4_usulan'] . '</div>';
								$pagu_4_usulan .= '<div class="indikator">' . $this->_number_format($indikator['pagu_4_usulan']) . '</div>';
								$target_5_usulan .= '<div class="indikator">' . $indikator['target_5_usulan'] . '</div>';
								$pagu_5_usulan .= '<div class="indikator">' . $this->_number_format($indikator['pagu_5_usulan']) . '</div>';
								$target_akhir_usulan .= '<div class="indikator">' . $indikator['target_akhir_usulan'] . '</div>';
								$satuan_usulan .= '<div class="indikator">' . $indikator['satuan_usulan'] . '</div>';
								$catatan_indikator_usulan .= '<div class="indikator">' . $indikator['catatan_indikator_usulan'] . '</div>';
							}

							$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
							$pagu_arr = [$pagu_1, $pagu_2, $pagu_3, $pagu_4, $pagu_5];
							$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
							$pagu_arr_usulan = [$pagu_1_usulan, $pagu_2_usulan, $pagu_3_usulan, $pagu_4_usulan, $pagu_5_usulan];
							$body .= '
									<tr class="tr-program">
										<td class="kiri atas kanan bawah' . $bg_rpjm . '">' . $no_tujuan . "." . $no_sasaran . "." . $no_program . '</td>
										<td class="atas kanan bawah' . $bg_rpjm . '"></td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah">' . $program['program_teks'] . '</td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah"><br>' . $indikator_program . '</td>
										<td class="atas kanan bawah text_tengah"><br>' . $target_awal . '</td>';
							for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
								$class_warning = '';
								if ($program['pagu_akumulasi_' . ($i + 1)] != $program['pagu_akumulasi_indikator_' . ($i + 1)]) {
									$class_warning = 'peringatan';
								}
								$body .= "
											<td class=\"atas kanan bawah text_tengah\"><br>" . $target_arr[$i] . "</td>
											<td class=\"atas kanan bawah text_kanan $class_warning\"><b>(" . $this->_number_format($program['pagu_akumulasi_' . ($i + 1)]) . ")</b><br>" . $pagu_arr[$i] . "</td>";
							}
							$body .= '<td class="atas kanan bawah text_tengah"><br>' . $target_akhir . '</td>
										<td class="atas kanan bawah"><br>' . $satuan . '</td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah"></td>
										<td class="atas kanan bawah">' . $program['catatan'] . '</td>
										<td class="atas kanan bawah"><br>' . $catatan_indikator . '</td>
										<td class="atas kanan bawah td-usulan"><br>' . $indikator_program_usulan . '</td>
										<td class="atas kanan bawah text_tengah td-usulan"><br>' . $target_awal_usulan . '</td>';
							for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
								$class_warning = '';
								if ($program['pagu_akumulasi_' . ($i + 1) . '_usulan'] != $program['pagu_akumulasi_indikator_' . ($i + 1) . '_usulan']) {
									$class_warning = 'peringatan';
								}
								$body .= "
											<td class=\"atas kanan bawah text_tengah td-usulan\"><br>" . $target_arr_usulan[$i] . "</td>
											<td class=\"atas kanan bawah text_kanan td-usulan $class_warning\"><b>(" . $this->_number_format($program['pagu_akumulasi_' . ($i + 1) . '_usulan']) . ")</b><br>" . $pagu_arr_usulan[$i] . "</td>";
							}
							$body .= '<td class="atas kanan bawah text_tengah td-usulan"><br>' . $target_akhir_usulan . '</td>
										<td class="atas kanan bawah td-usulan"><br>' . $satuan_usulan . '</td>
										<td class="atas kanan bawah td-usulan">' . $program['catatan_usulan'] . '</td>
										<td class="atas kanan bawah td-usulan"><br>' . $catatan_indikator_usulan . '</td>
										<td class="atas kanan bawah"></td>
									</tr>
							';

							$no_kegiatan = 0;
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
								foreach ($kegiatan['indikator'] as $key => $indikator) {
									$indikator_kegiatan .= '<div class="indikator">' . $indikator['indikator_teks'] . '</div>';
									$target_awal .= '<div class="indikator">' . $indikator['target_awal'] . '</div>';
									$target_1 .= '<div class="indikator">' . $indikator['target_1'] . '</div>';
									$pagu_1 .= '<div class="indikator">' . $this->_number_format($indikator['pagu_1']) . '</div>';
									$target_2 .= '<div class="indikator">' . $indikator['target_2'] . '</div>';
									$pagu_2 .= '<div class="indikator">' . $this->_number_format($indikator['pagu_2']) . '</div>';
									$target_3 .= '<div class="indikator">' . $indikator['target_3'] . '</div>';
									$pagu_3 .= '<div class="indikator">' . $this->_number_format($indikator['pagu_3']) . '</div>';
									$target_4 .= '<div class="indikator">' . $indikator['target_4'] . '</div>';
									$pagu_4 .= '<div class="indikator">' . $this->_number_format($indikator['pagu_4']) . '</div>';
									$target_5 .= '<div class="indikator">' . $indikator['target_5'] . '</div>';
									$pagu_5 .= '<div class="indikator">' . $this->_number_format($indikator['pagu_5']) . '</div>';
									$target_akhir .= '<div class="indikator">' . $indikator['target_akhir'] . '</div>';
									$satuan .= '<div class="indikator">' . $indikator['satuan'] . '</div>';
									$catatan_indikator .= '<div class="indikator">' . $indikator['catatan_indikator'] . '</div>';
									$indikator_kegiatan_usulan .= '<div class="indikator">' . $indikator['indikator_teks_usulan'] . '</div>';
									$target_awal_usulan .= '<div class="indikator">' . $indikator['target_awal_usulan'] . '</div>';
									$target_1_usulan .= '<div class="indikator">' . $indikator['target_1_usulan'] . '</div>';
									$pagu_1_usulan .= '<div class="indikator">' . $this->_number_format($indikator['pagu_1_usulan']) . '</div>';
									$target_2_usulan .= '<div class="indikator">' . $indikator['target_2_usulan'] . '</div>';
									$pagu_2_usulan .= '<div class="indikator">' . $this->_number_format($indikator['pagu_2_usulan']) . '</div>';
									$target_3_usulan .= '<div class="indikator">' . $indikator['target_3_usulan'] . '</div>';
									$pagu_3_usulan .= '<div class="indikator">' . $this->_number_format($indikator['pagu_3_usulan']) . '</div>';
									$target_4_usulan .= '<div class="indikator">' . $indikator['target_4_usulan'] . '</div>';
									$pagu_4_usulan .= '<div class="indikator">' . $this->_number_format($indikator['pagu_4_usulan']) . '</div>';
									$target_5_usulan .= '<div class="indikator">' . $indikator['target_5_usulan'] . '</div>';
									$pagu_5_usulan .= '<div class="indikator">' . $this->_number_format($indikator['pagu_5_usulan']) . '</div>';
									$target_akhir_usulan .= '<div class="indikator">' . $indikator['target_akhir_usulan'] . '</div>';
									$satuan_usulan .= '<div class="indikator">' . $indikator['satuan_usulan'] . '</div>';
									$catatan_indikator_usulan .= '<div class="indikator">' . $indikator['catatan_indikator_usulan'] . '</div>';
								}

								$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
								$pagu_arr = [$pagu_1, $pagu_2, $pagu_3, $pagu_4, $pagu_5];
								$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
								$pagu_arr_usulan = [$pagu_1_usulan, $pagu_2_usulan, $pagu_3_usulan, $pagu_4_usulan, $pagu_5_usulan];
								$body .= '
										<tr class="tr-kegiatan">
											<td class="kiri atas kanan bawah' . $bg_rpjm . '">' . $no_tujuan . "." . $no_sasaran . "." . $no_program . "." . $no_kegiatan . '</td>
											<td class="atas kanan bawah' . $bg_rpjm . '"></td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah">' . $kegiatan['kegiatan_teks'] . '</td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah"><br>' . $indikator_kegiatan . '</td>
											<td class="atas kanan bawah text_tengah"><br>' . $target_awal . '</td>';
								for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
									$class_warning = '';
									if ($kegiatan['pagu_akumulasi_' . ($i + 1)] != $kegiatan['pagu_akumulasi_indikator_' . ($i + 1)]) {
										$class_warning = 'peringatan';
									}
									$body .= "<td class=\"atas kanan bawah text_tengah\"><br>" . $target_arr[$i] . "</td><td class=\"atas kanan bawah text_kanan $class_warning\"><b>(" . $this->_number_format($kegiatan['pagu_akumulasi_' . ($i + 1)]) . ")</b><br>" . $pagu_arr[$i] . "</td>";
								}
								$body .= '
											<td class="atas kanan bawah text_tengah"><br>' . $target_akhir . '</td>
											<td class="atas kanan bawah"><br>' . $satuan . '</td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah"></td>
											<td class="atas kanan bawah">' . $kegiatan['catatan'] . '</td>
											<td class="atas kanan bawah"><br>' . $catatan_indikator . '</td>
											<td class="atas kanan bawah td-usulan"><br>' . $indikator_kegiatan_usulan . '</td>
											<td class="atas kanan bawah text_tengah td-usulan"><br>' . $target_awal_usulan . '</td>';
								for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
									$class_warning = '';
									if ($kegiatan['pagu_akumulasi_' . ($i + 1) . '_usulan'] != $kegiatan['pagu_akumulasi_indikator_' . ($i + 1) . '_usulan']) {
										$class_warning = 'peringatan';
									}
									$body .= "<td class=\"atas kanan bawah text_tengah td-usulan\"><br>" . $target_arr_usulan[$i - 1] . "</td><td class=\"atas kanan bawah text_kanan td-usulan\"><b>(" . $this->_number_format($kegiatan['pagu_akumulasi_' . ($i + 1) . '_usulan']) . ")</b><br>" . $pagu_arr_usulan[$i] . "</td>";
								}
								$body .= '
											<td class="atas kanan bawah text_tengah td-usulan"><br>' . $target_akhir_usulan . '</td>
											<td class="atas kanan bawah td-usulan"><br>' . $satuan_usulan . '</td>
											<td class="atas kanan bawah td-usulan"><br>' . $kegiatan['catatan_usulan'] . '</td>
											<td class="atas kanan bawah td-usulan">' . $catatan_indikator_usulan . '</td>
											<td class="atas kanan bawah"></td>
										</tr>
								';

								$no_sub_kegiatan = 0;
								foreach ($kegiatan['data'] as $key => $sub_kegiatan) {

									$no_sub_kegiatan++;
									$indikator_sub_kegiatan = '';
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
									$indikator_sub_kegiatan_usulan = '';
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

									foreach ($sub_kegiatan['indikator'] as $key => $indikator) {
										$indikator_sub_kegiatan .= '<div class="indikator">' . $indikator['indikator_teks'] . '</div>';
										$target_awal .= '<div class="indikator">' . $indikator['target_awal'] . '</div>';
										$target_1 .= '<div class="indikator">' . $indikator['target_1'] . '</div>';
										$target_2 .= '<div class="indikator">' . $indikator['target_2'] . '</div>';
										$target_3 .= '<div class="indikator">' . $indikator['target_3'] . '</div>';
										$target_4 .= '<div class="indikator">' . $indikator['target_4'] . '</div>';
										$target_5 .= '<div class="indikator">' . $indikator['target_5'] . '</div>';
										$target_akhir .= '<div class="indikator">' . $indikator['target_akhir'] . '</div>';
										$satuan .= '<div class="indikator">' . $indikator['satuan'] . '</div>';
										$catatan_indikator .= '<div class="indikator">' . $indikator['catatan_indikator'] . '</div>';
										$indikator_sub_kegiatan_usulan .= '<div class="indikator">' . $indikator['indikator_teks_usulan'] . '</div>';
										$target_awal_usulan .= '<div class="indikator">' . $indikator['target_awal_usulan'] . '</div>';
										$target_1_usulan .= '<div class="indikator">' . $indikator['target_1_usulan'] . '</div>';
										$target_2_usulan .= '<div class="indikator">' . $indikator['target_2_usulan'] . '</div>';
										$target_3_usulan .= '<div class="indikator">' . $indikator['target_3_usulan'] . '</div>';
										$target_4_usulan .= '<div class="indikator">' . $indikator['target_4_usulan'] . '</div>';
										$target_5_usulan .= '<div class="indikator">' . $indikator['target_5_usulan'] . '</div>';
										$target_akhir_usulan .= '<div class="indikator">' . $indikator['target_akhir_usulan'] . '</div>';
										$satuan_usulan .= '<div class="indikator">' . $indikator['satuan_usulan'] . '</div>';
										$catatan_indikator_usulan .= '<div class="indikator">' . $indikator['catatan_indikator_usulan'] . '</div>';
									}

									$target_arr = [$target_1, $target_2, $target_3, $target_4, $target_5];
									$target_arr_usulan = [$target_1_usulan, $target_2_usulan, $target_3_usulan, $target_4_usulan, $target_5_usulan];
									$body .= '
											<tr class="tr-sub-kegiatan">
												<td class="kiri atas kanan bawah' . $bg_rpjm . '">' . $no_tujuan . '.' . $no_sasaran . '.' . $no_program . '.' . $no_kegiatan . '.' . $no_sub_kegiatan . '</td>
												<td class="atas kanan bawah' . $bg_rpjm . '"></td>
												<td class="atas kanan bawah"></td>
												<td class="atas kanan bawah"></td>
												<td class="atas kanan bawah"></td>
												<td class="atas kanan bawah"></td>
												<td class="atas kanan bawah"></td>
												<td class="atas kanan bawah">' . $sub_kegiatan['sub_kegiatan_teks'] . '</td>
												<td class="atas kanan bawah"><br>' . $indikator_sub_kegiatan . '</td>
												<td class="atas kanan bawah text_tengah"><br>' . $target_awal . '</td>';
									for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
										$body .= "<td class=\"atas kanan bawah text_tengah\"><br>" . $target_arr[$i] . "</td><td class=\"atas kanan bawah text_kanan\">" . $this->_number_format($sub_kegiatan['pagu_' . ($i + 1)]) . "</td>";
									}
									$body .= '
												<td class="atas kanan bawah text_tengah"><br>' . $target_akhir . '</td>
												<td class="atas kanan bawah"><br>' . $satuan . '</td>
												<td class="atas kanan bawah"></td>
												<td class="atas kanan bawah"></td>
												<td class="atas kanan bawah">' . $sub_kegiatan['catatan'] . '</td>
												<td class="atas kanan bawah"><br>' . $catatan_indikator . '</td>
												<td class="atas kanan bawah td-usulan"><br>' . $indikator_sub_kegiatan_usulan . '</td>
												<td class="atas kanan bawah text_tengah td-usulan"><br>' . $target_awal_usulan . '</td>';
									for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
										$body .= "<td class=\"atas kanan bawah text_tengah td-usulan\"><br>" . $target_arr_usulan[$i] . "</td><td class=\"atas kanan bawah text_kanan td-usulan\">" . $this->_number_format($sub_kegiatan['pagu_' . ($i + 1) . '_usulan']) . "</td>";
									}
									$body .= '
												<td class="atas kanan bawah text_tengah td-usulan"><br>' . $target_akhir_usulan . '</td>
												<td class="atas kanan bawah td-usulan"><br>' . $satuan_usulan . '</td>
												<td class="atas kanan bawah td-usulan"><br>' . $kegiatan['catatan_usulan'] . '</td>
												<td class="atas kanan bawah td-usulan">' . $catatan_indikator_usulan . '</td>
												<td class="atas kanan bawah">' . $sub_kegiatan['nama_sub_unit'] . '</td>
											</tr>
									';
								}
							}
						}
					}
				}

				$body .= '<tr class="tr-total-pagu-opd">
					<td colspan="10" class="kiri atas kanan bawah"><b>TOTAL PAGU PER TAHUN ANGGARAN</b></td>';
				for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
					$body .= "<td colspan='2' class=\"atas kanan bawah text_kanan\"><b>" . $this->_number_format($data_all['pagu_akumulasi_' . ($i + 1)]) . "</b></td>";
				}
				$body .= '
					<td colspan="8" class="atas kanan bawah"></td>';
				for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
					$body .= "<td colspan='2' class=\"atas kanan bawah text_kanan\"><b>" . $this->_number_format($data_all['pagu_akumulasi_' . ($i + 1) . '_usulan']) . "</b></td>";
				}
				$body .= '
					<td colspan="6" class="atas kanan bawah"></td>
				</tr>';

				$table = '<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 80%; border: 0; table-layout: fixed;margin:30px 0px 30px 0px" contenteditable="false">
						<thead>
							<tr style="background:#ddf0a6">
								<th class="kiri atas kanan bawah text_tengah lebar1">Pagu Akumulasi Sub Kegiatan Per Tahun Anggaran</th>';
				for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
					$table .= "<th class=\"kiri atas kanan bawah text_tengah lebar2\">Tahun " . ($i + 1) . "</th>";
				}
				$table .= '</tr>
						</thead>
						<tbody>
							<tr style="background:#a2e9d1">
								<td class="kiri kanan bawah text_tengah"><b>Pagu Penetapan</b></td>';
				for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
					$table .= "<td class=\"atas kanan bawah text_kanan\">" . $this->_number_format($data_all['pagu_akumulasi_' . ($i + 1)]) . "</td>";
				}
				$table .= '</tr>
							<tr style="background:#b0ffb0">
								<td class="kiri kanan bawah text_tengah"><b>Pagu Usulan</b></td>';
				for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
					$table .= "<td class=\" kanan bawah text_kanan\">" . $this->_number_format($data_all['pagu_akumulasi_' . ($i + 1) . '_usulan']) . "</td>";
				}
				$table .= '
							</tr>
							<tr>
								<td class="kiri kanan bawah text_tengah"><b>Selisih</b></td>';
				for ($i = 0; $i < $jadwal_lokal->lama_pelaksanaan; $i++) {
					$selisih = ($data_all['pagu_akumulasi_' . ($i + 1)]) - ($data_all['pagu_akumulasi_' . ($i + 1) . '_usulan']);
					$table .= "<td class=\"atas kanan bawah text_kanan\">" . $this->_number_format($selisih) . "</td>";
				}
				$table .= '
							</tr>
						</tbody>
					</table>';

				$html = '
					<style type="text/css">
						.indikator_program { min-height: 40px; }
						.indikator_kegiatan { min-height: 40px; }
						.tr-tujuan {
						    background: #0000ff1f;
						}
						.tr-sasaran {
						    background: #ffff0059;
						}
						.tr-program {
						    background: #baffba;
						}
						.tr-kegiatan {
						    background: #13d0d03d;
						}
						.tr-total-pagu-opd{
							background: #83efef;
						}
						.peringatan {
							background: #f5c9c9;
						}
					</style>
					<div id="preview" class="card bg-light m-3 p-3 shadow-md" style="overflow-x : auto; overflow-y : auto; height : 90vh">
						<h4 style="text-align: center; margin: 0; font-weight: bold;">RENCANA STRATEGIS (RENSTRA)
						<br>' . $judul_skpd . 'Tahun ' . $jadwal_lokal->awal_renstra . ' - ' . $jadwal_lokal->akhir_renstra . ' ' . $nama_pemda . '</h4>
						' . $table . '
						<table id="table-renstra" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
							<thead><tr>
									<th style="width: 85px;" rowspan="2" class="atas kiri kanan bawah text_tengah text_blok">No</th>
									<th style="width: 200px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Sasaran ' . $nama_tipe_relasi . '</th>
									<th style="width: 200px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Bidang Urusan</th>
									<th style="width: 200px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Tujuan</th>
									<th style="width: 200px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Sasaran</th>
									<th style="width: 200px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Program</th>
									<th style="width: 200px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Kegiatan</th>
									<th style="width: 200px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Sub Kegiatan</th>
									<th style="width: 400px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Indikator</th>
									<th style="width: 100px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Target Awal</th>';
				for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
					$html .= '<th style="width: 200px;" colspan="2" class="atas kanan bawah text_tengah text_blok">Tahun ' . $i . '</th>';
				}
				$html .= '
									<th style="width: 100px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Target Akhir</th>
									<th style="width: 100px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Satuan</th>
									<th style="width: 150px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Keterangan</th>
									<th style="width: 50px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">No Urut</th>
									<th style="width: 150px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Catatan</th>
									<th style="width: 150px;" rowspan="2" class="atas kanan bawah text_tengah text_blok">Catatan Indikator</th>
									<th style="width: 400px;" rowspan="2" class="atas kanan bawah text_tengah text_blok td-usulan">Indikator Usulan</th>
									<th style="width: 100px;" rowspan="2" class="atas kanan bawah text_tengah text_blok td-usulan">Target Awal Usulan</th>';
				for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
					$html .= '<th style="width: 200px;" colspan="2" class="atas kanan bawah text_tengah text_blok td-usulan">Tahun ' . $i . ' Usulan</th>';
				}
				$html .= '
									<th style="width: 100px;" rowspan="2" class="atas kanan bawah text_tengah text_blok td-usulan">Target Akhir Usulan</th>
									<th style="width: 100px;" rowspan="2" class="atas kanan bawah text_tengah text_blok td-usulan">Satuan Usulan</th>
									<th style="width: 150px;" rowspan="2" class="atas kanan bawah text_tengah text_blok td-usulan">Catatan Usulan</th>
									<th style="width: 150px;" rowspan="2" class="atas kanan bawah text_tengah text_blok td-usulan">Catatan Indikator Usulan</th>
									<th style="width: 150px;" rowspan="2" class="atas kanan bawah text_tengah text_blok td-usulan">Sub Unit Pelaksana</th>
								</tr>
								<tr>';
				for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
					$html .= '
										<th style="width: 100px;" class="row_head_2 atas kanan bawah text_tengah text_blok">Target</th>
										<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Pagu</th>';
				}
				for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
					$html .= '
										<th style="width: 100px;" class="row_head_2 atas kanan bawah text_tengah text_blok td-usulan">Target Usulan</th>
										<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok td-usulan">Pagu Usulan</th>';
				}
				$html .= '</tr>';

				$html .= "<tr>
									<th class='atas kiri kanan bawah text_tengah text_blok'>0</th>
									<th class='atas kanan bawah text_tengah text_blok'>1</th>
									<th class='atas kanan bawah text_tengah text_blok'>2</th>
									<th class='atas kanan bawah text_tengah text_blok'>3</th>
									<th class='atas kanan bawah text_tengah text_blok'>4</th>
									<th class='atas kanan bawah text_tengah text_blok'>5</th>
									<th class='atas kanan bawah text_tengah text_blok'>6</th>
									<th class='atas kanan bawah text_tengah text_blok'>7</th>
									<th class='atas kanan bawah text_tengah text_blok'>8</th>
									<th class='atas kanan bawah text_tengah text_blok'>9</th>";

				$target_temp = 10;
				for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
					if ($i != 1) {
						$target_temp = $pagu_temp + 1;
					}
					$pagu_temp = $target_temp + 1;

					$html .= "<th class='atas kanan bawah text_tengah text_blok'>" . $target_temp . "</th>
									<th class='atas kanan bawah text_tengah text_blok'>" . $pagu_temp . "</th>";
				}

				$html .= "<th class='atas kanan bawah text_tengah text_blok'>" . ($pagu_temp + 1) . "</th>
									<th class='atas kanan bawah text_tengah text_blok'>" . ($pagu_temp + 2) . "</th>
									<th class='atas kanan bawah text_tengah text_blok'>" . ($pagu_temp + 3) . "</th>
									<th class='atas kanan bawah text_tengah text_blok'>" . ($pagu_temp + 4) . "</th>
									<th class='atas kanan bawah text_tengah text_blok'>" . ($pagu_temp + 5) . "</th>
									<th class='atas kanan bawah text_tengah text_blok'>" . ($pagu_temp + 6) . "</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>" . ($pagu_temp + 7) . "</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>" . ($pagu_temp + 8) . "</th>";

				$target_temp += 9;
				for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
					if ($i != 1) {
						$target_temp = $pagu_temp + 1;
					}
					$pagu_temp = $target_temp + 1;

					$html .= "<th class='atas kanan bawah text_tengah text_blok td-usulan'>" . $target_temp . "</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>" . $pagu_temp . "</th>";
				}

				$html .= "<th class='atas kanan bawah text_tengah text_blok td-usulan'>" . ($pagu_temp + 1) . "</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>" . ($pagu_temp + 2) . "</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>" . ($pagu_temp + 3) . "</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>" . ($pagu_temp + 4) . "</th>
									<th class='atas kanan bawah text_tengah text_blok td-usulan'>" . ($pagu_temp + 5) . "</th>
								</tr>
							</thead>
							<tbody>" . $body . "</tbody>
						</table>
					</div>";
				$ret['html'] = $html;
			} else {
				$ret = array(
					'status' => 'error',
					'message'   => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message'   => 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	public function view_pagu_akumulasi_renstra()
	{
		global $wpdb;
		$ret = array(
			'status'    => 'success',
			'message'   => 'Berhasil generate laporan pagu akumulasi!'
		);
		$data_all = array(
			'data' => array()
		);

		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {

				$nama_pemda = get_option('_crb_daerah');
				$id_unit = $_POST['id_unit'];
				$tahun_anggaran = $_POST['tahun_anggaran'];
				$id_jadwal_lokal = $_POST['id_jadwal_lokal'];

				$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
					SELECT 
						nama AS nama_jadwal,
						tahun_anggaran AS awal_renstra,
						(tahun_anggaran+lama_pelaksanaan-1) AS akhir_renstra,
						lama_pelaksanaan,
						status 
					FROM `data_jadwal_lokal` 
						WHERE id_jadwal_lokal=%d", $id_jadwal_lokal));

				$_suffix = '';
				$where = '';
				if ($jadwal_lokal->status == 1) {
					$_suffix = '_history';
					$where = 'AND id_jadwal=' . $wpdb->prepare("%d", $id_jadwal_lokal);
				}

				$where_skpd = '';
				if (!empty($id_unit)) {
					if ($id_unit != 'all') {
						$where_skpd = "and id_skpd=" . $wpdb->prepare("%d", $id_unit);
					}
				}

				$sql = $wpdb->prepare("
					SELECT 
						* 
					FROM data_unit 
					WHERE tahun_anggaran=%d
						" . $where_skpd . "
						AND is_skpd=1
						AND active=1
					ORDER BY id_skpd ASC
				", $tahun_anggaran);

				$units = $wpdb->get_results($sql, ARRAY_A);

				$data_all = array(
					'data' => array(),
					'pagu_1_total' => 0,
					'pagu_2_total' => 0,
					'pagu_3_total' => 0,
					'pagu_4_total' => 0,
					'pagu_5_total' => 0,
					'pagu_1_usulan_total' => 0,
					'pagu_2_usulan_total' => 0,
					'pagu_3_usulan_total' => 0,
					'pagu_4_usulan_total' => 0,
					'pagu_5_usulan_total' => 0
				);
				foreach ($units as $unit) {

					if (empty($data_all['data'][$unit['id_skpd']])) {
						$data_all['data'][$unit['id_skpd']] = [
							'kode_skpd' => $unit['kode_skpd'],
							'nama_skpd' => $unit['nama_skpd'],
							'pagu_1' => 0,
							'pagu_2' => 0,
							'pagu_3' => 0,
							'pagu_4' => 0,
							'pagu_5' => 0,
							'pagu_1_usulan' => 0,
							'pagu_2_usulan' => 0,
							'pagu_3_usulan' => 0,
							'pagu_4_usulan' => 0,
							'pagu_5_usulan' => 0
						];
					}

					$tujuan_all = $wpdb->get_results($wpdb->prepare("
						SELECT 
							DISTINCT id_unik 
						FROM data_renstra_tujuan_lokal" . $_suffix . " 
						WHERE 
							id_unit=%d AND 
							active=1 $where ORDER BY urut_tujuan
					", $unit['id_skpd']), ARRAY_A);

					foreach ($tujuan_all as $keyTujuan => $tujuan_value) {

						$sasaran_all = $wpdb->get_results($wpdb->prepare("
								SELECT 
									DISTINCT id_unik 
								FROM data_renstra_sasaran_lokal" . $_suffix . " 
								WHERE 
									kode_tujuan=%s AND 
									active=1 $where ORDER BY urut_sasaran
							", $tujuan_value['id_unik']), ARRAY_A);

						foreach ($sasaran_all as $keySasaran => $sasaran_value) {

							$program_all = $wpdb->get_results($wpdb->prepare(
								"
								SELECT 
									DISTINCT id_unik 
								FROM data_renstra_program_lokal" . $_suffix . " 
								WHERE 
									kode_sasaran=%s AND 
									kode_tujuan=%s AND 
									active=1 $where ORDER BY id",
								$sasaran_value['id_unik'],
								$tujuan_value['id_unik']
							), ARRAY_A);

							foreach ($program_all as $keyProgram => $program_value) {

								$kegiatan_all = $wpdb->get_results($wpdb->prepare(
									"
									SELECT 
										DISTINCT id_unik
									FROM data_renstra_kegiatan_lokal" . $_suffix . "
									WHERE 
										kode_program=%s AND
										kode_sasaran=%s AND
										kode_tujuan=%s $where ORDER BY id",
									$program_value['id_unik'],
									$sasaran_value['id_unik'],
									$tujuan_value['id_unik']
								), ARRAY_A);

								foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {

									$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare(
										"
										SELECT 
											COALESCE(SUM(pagu_1), 0) AS pagu_1, 
											COALESCE(SUM(pagu_2), 0) AS pagu_2, 
											COALESCE(SUM(pagu_3), 0) AS pagu_3, 
											COALESCE(SUM(pagu_4), 0) AS pagu_4, 
											COALESCE(SUM(pagu_5), 0) AS pagu_5, 
											COALESCE(SUM(pagu_1_usulan), 0) AS pagu_1_usulan, 
											COALESCE(SUM(pagu_2_usulan), 0) AS pagu_2_usulan, 
											COALESCE(SUM(pagu_3_usulan), 0) AS pagu_3_usulan, 
											COALESCE(SUM(pagu_4_usulan), 0) AS pagu_4_usulan, 
											COALESCE(SUM(pagu_5_usulan), 0) AS pagu_5_usulan 
										FROM data_renstra_sub_kegiatan_lokal" . $_suffix . " 
										WHERE 
											kode_kegiatan=%s AND 
											kode_program=%s AND 
											kode_sasaran=%s AND 
											kode_tujuan=%s AND 
											active=1 $where ORDER BY id",
										$kegiatan_value['id_unik'],
										$program_value['id_unik'],
										$sasaran_value['id_unik'],
										$tujuan_value['id_unik']
									), ARRAY_A);

									foreach ($sub_kegiatan_all as $keySubKegiatan => $sub_kegiatan_value) {

										$data_all['data'][$unit['id_skpd']]['pagu_1'] += $sub_kegiatan_value['pagu_1'];
										$data_all['data'][$unit['id_skpd']]['pagu_2'] += $sub_kegiatan_value['pagu_2'];
										$data_all['data'][$unit['id_skpd']]['pagu_3'] += $sub_kegiatan_value['pagu_3'];
										$data_all['data'][$unit['id_skpd']]['pagu_4'] += $sub_kegiatan_value['pagu_4'];
										$data_all['data'][$unit['id_skpd']]['pagu_5'] += $sub_kegiatan_value['pagu_5'];
										$data_all['data'][$unit['id_skpd']]['pagu_1_usulan'] += $sub_kegiatan_value['pagu_1_usulan'];
										$data_all['data'][$unit['id_skpd']]['pagu_2_usulan'] += $sub_kegiatan_value['pagu_2_usulan'];
										$data_all['data'][$unit['id_skpd']]['pagu_3_usulan'] += $sub_kegiatan_value['pagu_3_usulan'];
										$data_all['data'][$unit['id_skpd']]['pagu_4_usulan'] += $sub_kegiatan_value['pagu_4_usulan'];
										$data_all['data'][$unit['id_skpd']]['pagu_5_usulan'] += $sub_kegiatan_value['pagu_5_usulan'];

										$data_all['pagu_1_total'] += $sub_kegiatan_value['pagu_1'];
										$data_all['pagu_2_total'] += $sub_kegiatan_value['pagu_2'];
										$data_all['pagu_3_total'] += $sub_kegiatan_value['pagu_3'];
										$data_all['pagu_4_total'] += $sub_kegiatan_value['pagu_4'];
										$data_all['pagu_5_total'] += $sub_kegiatan_value['pagu_5'];
										$data_all['pagu_1_usulan_total'] += $sub_kegiatan_value['pagu_1_usulan'];
										$data_all['pagu_2_usulan_total'] += $sub_kegiatan_value['pagu_2_usulan'];
										$data_all['pagu_3_usulan_total'] += $sub_kegiatan_value['pagu_3_usulan'];
										$data_all['pagu_4_usulan_total'] += $sub_kegiatan_value['pagu_4_usulan'];
										$data_all['pagu_5_usulan_total'] += $sub_kegiatan_value['pagu_5_usulan'];
									}
								}
							}
						}
					}
				}

				$body = '';
				$no = 1;
				foreach ($data_all['data'] as $key => $unit) {
					$body .= '<tr>
						<td class="kiri atas kanan bawah text_tengah">' . $no . '.</td>
						<td class="atas kanan bawah">' . $unit['nama_skpd'] . '</td>';
					for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
						$body .= '<td class="atas kanan bawah">' . $this->_number_format($unit['pagu_' . $i]) . '</td>';
						$body .= '<td class="atas kanan bawah">' . $this->_number_format($unit['pagu_' . $i . '_usulan']) . '</td>';
					}
					$body .= '</tr>';
					$no++;
				}
				$body .= '<tr>
						<td class="kiri atas kanan bawah text_tengah" colspan="2"><b>TOTAL PAGU</b></td>';
				for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
					$body .= '<td class="atas kanan bawah"><b>' . $this->_number_format($data_all['pagu_' . $i . '_total']) . '</b></td>';
					$body .= '<td class="atas kanan bawah"><b>' . $this->_number_format($data_all['pagu_' . $i . '_usulan_total']) . '</b></td>';
				}
				$body .= '</tr>';

				$html = '<div id="preview" class="card bg-light m-3 p-3 shadow-md" style="overflow-x : auto; overflow-y : auto; height : 90vh">
						<h4 style="text-align: center; margin: 0; font-weight: bold;">PAGU AKUMULASI RENSTRA Per Unit Kerja<br>Tahun ' . $jadwal_lokal->awal_renstra . ' - ' . $jadwal_lokal->akhir_renstra . ' ' . $nama_pemda . '<br>' . $jadwal_lokal->nama_jadwal . '
						</h4>
						<br>
						<button class="btn btn-warning" onclick="cek_pemutakhiran();">Cek Pemutakhiran</button>
						<div id="tabel-pemutakhiran-belanja"></div>
						<br>
						<table id="table-renstra" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 80%; border: 0; table-layout: fixed;" contenteditable="false">
							<thead><tr>
									<th style="width: 85px;" class="atas kiri kanan bawah text_tengah text_blok" rowspan="2">No</th>
									<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok" rowspan="2">Unit Kerja</th>';
				for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
					$html .= '<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok" colspan="2">Tahun ' . $i . '</th>';
				}
				$html .= '
								</tr>';
				$html .= '<tr>';
				for ($i = 1; $i <= $jadwal_lokal->lama_pelaksanaan; $i++) {
					$html .= '<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Pagu</th><th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Pagu Usulan</th>';
				}
				$html .= '</tr>';
				$html .= '</thead>
							<tbody>' . $body . '</tbody>
						</table>
					</div>';
				$ret['html'] = $html;
			} else {
				$ret = array(
					'status' => 'error',
					'message'   => 'Api Key tidak sesuai!'
				);
			}
		} else {
			$ret = array(
				'status' => 'error',
				'message'   => 'Format tidak sesuai!'
			);
		}
		die(json_encode($ret));
	}

	public function get_sub_kegiatan_renstra()
	{
		global $wpdb;
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				if ($_POST['type'] == 1) {
					$sql = $wpdb->prepare("
						SELECT 
							k.*
						FROM data_renstra_sub_kegiatan_lokal k
						WHERE k.kode_kegiatan=%s AND
							k.id_unik IS NOT NULL and
							k.id_unik_indikator IS NULL and
							k.active=1 and
							k.tahun_anggaran=%d
						ORDER BY nama_sub_giat
					", $_POST['kode_kegiatan'], $_POST['tahun_anggaran']);
					$sub_kegiatan = $wpdb->get_results($sql, ARRAY_A);
					foreach ($sub_kegiatan as $k => $sub_keg) {
						$sub_kegiatan[$k]['pokin'] = $wpdb->get_results($wpdb->prepare("
							SELECT
								*
							FROM data_pokin_renstra
							WHERE id_unik=%s
								AND id_skpd=%d
								AND active=1
								AND tipe=5
								AND tahun_anggaran=%d
						", $sub_keg['id_unik'], $sub_keg['id_unit'], $_POST['tahun_anggaran']), ARRAY_A);
						
						$sub_kegiatan[$k]['satker'] = $wpdb->get_results($wpdb->prepare("
							SELECT
								*
							FROM data_pelaksana_renstra
							WHERE id_unik=%s
								AND id_skpd=%d
								AND active=1
								AND tipe=5
								AND tahun_anggaran=%d
						", $sub_keg['id_unik'], $sub_keg['id_unit'], $_POST['tahun_anggaran']), ARRAY_A);
					}
				}

				echo json_encode([
					'status' => true,
					'data' => $sub_kegiatan,
					'message' => 'Sukses get sub kegiatan by kegiatan'
				]);
				exit;
			}

			echo json_encode([
				'status' => false,
				'message' => 'Api key tidak sesuai'
			]);
			exit;
		}

		echo json_encode([
			'status' => false,
			'message' => 'Format tidak sesuai'
		]);
		exit;
	}

	public function submit_sub_kegiatan_renstra()
	{

		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_sub_kegiatan_renstra($data);
					if(!empty($data['id_jadwal_wp_sakip'])){
						if (empty($data['pokin-level'])) {
							throw new Exception('Pohon Kinerja tidak boleh kosong!');
						}
						if (empty($data['satker-pelaksana'])) {
							throw new Exception('Satuan Kerja Pelaksana tidak boleh kosong!');
						}
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sub_kegiatan_lokal
						WHERE id_sub_giat=%d
							AND kode_giat=%s
							AND kode_kegiatan=%s
							AND active=1
							AND id_sub_unit=%d 
							AND id_unik_indikator IS NULL
					", $data['id_sub_kegiatan'], $data['kode_giat'], $data['kode_kegiatan'], $data['id_sub_unit']));

					if (!empty($id_cek)) {
						throw new Exception('Sub Kegiatan : ' . $data['sub_kegiatan_teks'] . ' sudah ada! id=' . $id_cek);
					}

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NULL 
							AND active=1
					", $data['kode_kegiatan']));

					if (empty($dataKegiatan)) {
						throw new Exception('Kegiatan tidak ditemukan!');
					}

					$dataSubKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_prog_keg 
						WHERE id_sub_giat=%d
							AND tahun_anggaran=%d
					", $data['id_sub_kegiatan'], $_POST['tahun_anggaran']));

					if (empty($dataSubKegiatan)) {
						throw new Exception('Sub Kegiatan tidak ditemukan!');
					}

					$dataSubUnit = $wpdb->get_row($wpdb->prepare("SELECT kode_skpd, nama_skpd FROM data_unit WHERE id_skpd=%d AND tahun_anggaran=%d", $data['id_sub_unit'], $_POST['tahun_anggaran']));

					if (empty($dataSubUnit)) {
						throw new Exception('Sub Unit tidak ditemukan di tahun anggaran ' . $_POST['tahun_anggaran'] . '!');
					}

					try {

						$inputs = array(
							'bidur_lock' => $dataKegiatan->bidur_lock,
							'giat_lock' => $dataKegiatan->giat_lock,
							'id_bidang_urusan' => $dataKegiatan->id_bidang_urusan,
							'id_sub_giat' => $dataSubKegiatan->id_sub_giat,
							'id_giat' => $dataKegiatan->id_giat,
							'id_misi' => $dataKegiatan->id_misi,
							'id_program' => $dataKegiatan->id_program,
							'id_unik' => $this->generateRandomString(), // kode_sub_kegiatan
							'id_unit' => $dataKegiatan->id_unit,
							'id_sub_unit' => $data['id_sub_unit'],
							'id_visi' => $dataKegiatan->id_visi,
							'is_locked' => 0,
							'is_locked_indikator' => 0,
							'kode_bidang_urusan' => $dataKegiatan->kode_bidang_urusan,
							'kode_sub_giat' => $dataSubKegiatan->kode_sub_giat,
							'kode_giat' => $dataKegiatan->kode_giat,
							'kode_kegiatan' => $dataKegiatan->id_unik,
							'kode_program' => $dataKegiatan->kode_program,
							'kode_sasaran' => $dataKegiatan->kode_sasaran,
							'kode_skpd' => $dataKegiatan->kode_skpd,
							'kode_tujuan' => $dataKegiatan->kode_tujuan,
							'nama_bidang_urusan' => $dataKegiatan->nama_bidang_urusan,
							'nama_sub_giat' => $dataSubKegiatan->nama_sub_giat,
							'nama_giat' => $dataKegiatan->nama_giat,
							'nama_program' => $dataKegiatan->nama_program,
							'nama_skpd' => $dataKegiatan->nama_skpd,
							'nama_sub_unit' => $dataSubUnit->kode_skpd . " " . $dataSubUnit->nama_skpd,
							'program_lock' => $dataKegiatan->program_lock,
							'renstra_prog_lock' => $dataKegiatan->program_lock,
							'sasaran_lock' => $dataKegiatan->sasaran_lock,
							'sasaran_teks' => $dataKegiatan->sasaran_teks,
							'status' => 1,
							'tujuan_lock' => $dataKegiatan->tujuan_lock,
							'tujuan_teks' => $dataKegiatan->tujuan_teks,
							'urut_sasaran' => $dataKegiatan->urut_sasaran,
							'urut_tujuan' => $dataKegiatan->urut_tujuan,
							'tahun_anggaran' => $_POST['tahun_anggaran'],
							'active' => 1
						);

						$inputs['pagu_1_usulan'] = $data['pagu_1_usulan'];
						$inputs['pagu_2_usulan'] = $data['pagu_2_usulan'];
						$inputs['pagu_3_usulan'] = $data['pagu_3_usulan'];
						$inputs['pagu_4_usulan'] = $data['pagu_4_usulan'];
						$inputs['pagu_5_usulan'] = $data['pagu_5_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];

						if (in_array('administrator', $this->role())) {
							$inputs['pagu_1'] = !empty($data['pagu_1']) || $data['pagu_1'] == 0 ? $data['pagu_1'] : $data['pagu_1_usulan'];
							$inputs['pagu_2'] = !empty($data['pagu_2']) || $data['pagu_2'] == 0 ? $data['pagu_2'] : $data['pagu_2_usulan'];
							$inputs['pagu_3'] = !empty($data['pagu_3']) || $data['pagu_3'] == 0 ? $data['pagu_3'] : $data['pagu_3_usulan'];
							$inputs['pagu_4'] = !empty($data['pagu_4']) || $data['pagu_4'] == 0 ? $data['pagu_4'] : $data['pagu_4_usulan'];
							$inputs['pagu_5'] = !empty($data['pagu_5']) || $data['pagu_5'] == 0 ? $data['pagu_5'] : $data['pagu_5_usulan'];
							$inputs['catatan'] = $data['catatan'];
						}

						$status = $wpdb->insert('data_renstra_sub_kegiatan_lokal', $inputs);

						if ($status === false) {
							$ket = '';
							if (in_array('administrator', $this->role())) {
								$ket = " | query: " . $wpdb->last_query;
							}
							$ket .= " | error: " . $wpdb->last_error;
							throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
						}

						$wpdb->update("data_pokin_renstra", array("active" => 0), array(
							"id_unik" => $inputs['id_unik'],
							"tipe" => 5,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						$wpdb->update("data_pelaksana_renstra", array("active" => 0), array(
							"id_unik" => $inputs['id_unik'],
							"tipe" => 5,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						// cek jika jadwal sakip aktif
						if(!empty($data['id_jadwal_wp_sakip'])){
							$_POST['id_skpd'] = $dataKegiatan->id_unit;
							$_POST['id_jadwal_wp_sakip'] = $data['id_jadwal_wp_sakip'];
							$pokin_all = $this->get_data_pohon_kinerja(true);
							$new_pokin = array();
							foreach($pokin_all['data'] as $val){
								$new_pokin[$val->id] = $val;
							}
							if(!is_array($data['pokin-level'])){
								$data['pokin-level'] = array($data['pokin-level']);
							}
							foreach($data['pokin-level'] as $id_pokin){
								if(!empty($new_pokin[$id_pokin])){
									$indikator = array();
									foreach($new_pokin[$id_pokin]->indikator as $ind){
										$indikator[] = $ind->label;
									}
									$data_pokin = array(
										"id_pokin" => $id_pokin,
										"level" => $new_pokin[$id_pokin]->level,
										"label" => $new_pokin[$id_pokin]->label,
										"indikator" => implode(', ', $indikator),
										"tipe" => 5,
										"id_unik" => $inputs['id_unik'],
										"id_skpd" => $dataKegiatan->id_unit,
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pokin_renstra
										WHERE id_pokin=%d
											AND id_unik=%s
											AND tipe=5
											AND tahun_anggaran=%d
									", $id_pokin, $inputs['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pokin_renstra", $data_pokin, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pokin_renstra", $data_pokin);
									}
								}
							}

							$satker_all = $this->get_data_satker(true);
							$new_satker = array();
							foreach($satker_all['data'] as $val){
								$new_satker[$val->id] = $val;
							}
							if(!is_array($data['satker-pelaksana'])){
								$data['satker-pelaksana'] = array($data['satker-pelaksana']);
							}
							foreach($data['satker-pelaksana'] as $id_satker){
								if(!empty($new_satker[$id_satker])){
									$data_satker = array(
										"id_satker" => $id_satker,
										"nama_satker" => $new_satker[$id_satker]->nama,
										"tipe" => 5,
										"id_skpd" => $dataKegiatan->id_unit,
										"id_unik" => $inputs['id_unik'],
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pelaksana_renstra
										WHERE id_satker=%s
											AND tipe=5
											AND id_unik=%s
											AND tahun_anggaran=%d
									", $id_pokin, $inputs['id_unik'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pelaksana_renstra", $data_satker, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pelaksana_renstra", $data_satker);
									}
								}
							}
						}

						echo json_encode([
							'status' => true,
							'message' => 'Sukses simpan sub kegiatan'
						]);
						exit;
					} catch (Exception $e) {
						throw $e;
					}
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function edit_sub_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$dataSubKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							d.*,
							COALESCE(d.pagu_1_usulan, 0) AS pagu_1_usulan_temp, 
							COALESCE(d.pagu_1, 0) AS pagu_1_temp, 
							COALESCE(d.pagu_2_usulan, 0) AS pagu_2_usulan_temp, 
							COALESCE(d.pagu_2, 0) AS pagu_2_temp, 
							COALESCE(d.pagu_3_usulan, 0) AS pagu_3_usulan_temp, 
							COALESCE(d.pagu_3, 0) AS pagu_3_temp, 
							COALESCE(d.pagu_4_usulan, 0) AS pagu_4_usulan_temp, 
							COALESCE(d.pagu_4, 0) AS pagu_4_temp, 
							COALESCE(d.pagu_5_usulan, 0) AS pagu_5_usulan_temp, 
							COALESCE(d.pagu_5, 0) AS pagu_5_temp 
						FROM data_renstra_sub_kegiatan_lokal d
						WHERE id=%d
						AND tahun_anggaran=%d
					", $_POST['id_sub_kegiatan'], $_POST['tahun_anggaran']), ARRAY_A);

					$pokin = $wpdb->get_results($wpdb->prepare("
						SELECT
							*
						FROM data_pokin_renstra
						WHERE id_unik=%s
							AND tipe=5
							AND active=1
							AND tahun_anggaran=%d
					", $dataSubKegiatan['id_unik'], $dataSubKegiatan['tahun_anggaran']), ARRAY_A);

					$satker = $wpdb->get_results($wpdb->prepare("
						SELECT
							*
						FROM data_pelaksana_renstra
						WHERE id_unik=%s
							AND tipe=5
							AND active=1
							AND tahun_anggaran=%d
					", $dataSubKegiatan['id_unik'], $dataSubKegiatan['tahun_anggaran']), ARRAY_A);

					echo json_encode([
						'status' => true,
						'sub_kegiatan' => $dataSubKegiatan,
						'pokin' => $pokin,
						'satker' => $satker
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function update_sub_kegiatan_renstra()
	{

		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_sub_kegiatan_renstra($data);
					if(!empty($data['id_jadwal_wp_sakip'])){
						if (empty($data['pokin-level'])) {
							throw new Exception('Pohon Kinerja tidak boleh kosong!');
						}
						if (empty($data['satker-pelaksana'])) {
							throw new Exception('Satuan Kerja Pelaksana tidak boleh kosong!');
						}
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sub_kegiatan_lokal
						WHERE id!=%d
							AND id_sub_giat=%d
							AND id_giat=%d
							AND kode_giat=%s
							AND kode_kegiatan=%s
							AND active=1
							AND id_sub_unit=%d 
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['id'], $data['id_sub_giat'], $data['id_giat'], $data['kode_giat'], $data['kode_kegiatan'], $data['id_sub_unit'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Sub Kegiatan : ' . $data['sub_kegiatan_teks'] . ' sudah ada! id=' . $id_cek);
					}

					$dataKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_kegiatan_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NULL 
							AND active=1
							AND tahun_anggaran=%d
					", $data['kode_kegiatan'], $_POST['tahun_anggaran']));

					if (empty($dataKegiatan)) {
						throw new Exception('Kegiatan tidak ditemukan!');
					}

					$tahun_anggaran_wpsipd = $_POST['tahun_anggaran'];
					$dataSubKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_prog_keg 
						WHERE id_sub_giat=%d
							AND tahun_anggaran=%d
					", $data['id_sub_kegiatan'], $tahun_anggaran_wpsipd));

					if (empty($dataSubKegiatan)) {
						throw new Exception('Sub Kegiatan tidak ditemukan!');
					}

					$dataSubUnit = $wpdb->get_row($wpdb->prepare("
						SELECT 
							kode_skpd, 
							nama_skpd 
						FROM data_unit 
						WHERE id_skpd=%d 
							AND tahun_anggaran=%d
					", $data['id_sub_unit'], $tahun_anggaran_wpsipd));

					if (empty($dataSubUnit)) {
						throw new Exception('Sub Unit tidak ditemukan di tahun anggaran ' . $_POST['tahun_anggaran'] . '!');
					}

					try {

						add_filter('query', array($this, 'wpsipd_query'));

						$inputs['bidur_lock'] = $dataKegiatan->bidur_lock;
						$inputs['giat_lock'] = $dataKegiatan->giat_lock;
						$inputs['id_bidang_urusan'] = $dataKegiatan->id_bidang_urusan;
						$inputs['id_sub_giat'] = $dataSubKegiatan->id_sub_giat;
						$inputs['id_giat'] = $dataKegiatan->id_giat;
						$inputs['id_misi'] = $dataKegiatan->id_misi;
						$inputs['id_program'] = $dataKegiatan->id_program;
						$inputs['id_unit'] = $dataKegiatan->id_unit;
						$inputs['id_sub_unit'] = $data['id_sub_unit'];
						$inputs['id_visi'] = $dataKegiatan->id_visi;
						$inputs['kode_bidang_urusan'] = $dataKegiatan->kode_bidang_urusan;
						$inputs['kode_sub_giat'] = $dataSubKegiatan->kode_sub_giat;
						$inputs['kode_giat'] = $dataKegiatan->kode_giat;
						$inputs['kode_kegiatan'] = $dataKegiatan->id_unik;
						$inputs['kode_program'] = $dataKegiatan->kode_program;
						$inputs['kode_sasaran'] = $dataKegiatan->kode_sasaran;
						$inputs['kode_skpd'] = $dataKegiatan->kode_skpd;
						$inputs['kode_tujuan'] = $dataKegiatan->kode_tujuan;
						$inputs['nama_bidang_urusan'] = $dataKegiatan->nama_bidang_urusan;
						$inputs['nama_sub_giat'] = $dataSubKegiatan->nama_sub_giat;
						$inputs['nama_giat'] = $dataKegiatan->nama_giat;
						$inputs['nama_program'] = $dataKegiatan->nama_program;
						$inputs['nama_skpd'] = $dataKegiatan->nama_skpd;
						$inputs['nama_sub_unit'] = $dataSubUnit->kode_skpd . " " . $dataSubUnit->nama_skpd;
						$inputs['program_lock'] = $dataKegiatan->program_lock;
						$inputs['renstra_prog_lock'] = $dataKegiatan->program_lock;
						$inputs['sasaran_lock'] = $dataKegiatan->sasaran_lock;
						$inputs['sasaran_teks'] = $dataKegiatan->sasaran_teks;
						$inputs['tujuan_lock'] = $dataKegiatan->tujuan_lock;
						$inputs['tujuan_teks'] = $dataKegiatan->tujuan_teks;
						$inputs['urut_sasaran'] = $dataKegiatan->urut_sasaran;
						$inputs['urut_tujuan'] = $dataKegiatan->urut_tujuan;
						$inputs['update_at'] = current_time('mysql');

						$inputs['pagu_1_usulan'] = $data['pagu_1_usulan'];
						$inputs['pagu_2_usulan'] = $data['pagu_2_usulan'];
						$inputs['pagu_3_usulan'] = $data['pagu_3_usulan'];
						$inputs['pagu_4_usulan'] = $data['pagu_4_usulan'];
						$inputs['pagu_5_usulan'] = $data['pagu_5_usulan'];
						$inputs['catatan_usulan'] = $data['catatan_usulan'];

						if (in_array('administrator', $this->role())) {
							$inputs['pagu_1'] = !empty($data['pagu_1']) || $data['pagu_1'] == 0 ? $data['pagu_1'] : $data['pagu_1_usulan'];
							$inputs['pagu_2'] = !empty($data['pagu_2']) || $data['pagu_2'] == 0 ? $data['pagu_2'] : $data['pagu_2_usulan'];
							$inputs['pagu_3'] = !empty($data['pagu_3']) || $data['pagu_3'] == 0 ? $data['pagu_3'] : $data['pagu_3_usulan'];
							$inputs['pagu_4'] = !empty($data['pagu_4']) || $data['pagu_4'] == 0 ? $data['pagu_4'] : $data['pagu_4_usulan'];
							$inputs['pagu_5'] = !empty($data['pagu_5']) || $data['pagu_5'] == 0 ? $data['pagu_5'] : $data['pagu_5_usulan'];
							$inputs['catatan'] = $data['catatan'];
						}

						$status = $wpdb->update('data_renstra_sub_kegiatan_lokal', $inputs, [
							'id_unik' => $data['kode_sub_kegiatan'],
							'id_unik_indikator' => 'NULL',
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						if ($status === false) {
							$ket = '';
							if (in_array('administrator', $this->role())) {
								$ket = " | query: " . $wpdb->last_query;
							}
							$ket .= " | error: " . $wpdb->last_error;
							throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
						}

						$inputs_indikator['bidur_lock'] = $dataKegiatan->bidur_lock;
						$inputs_indikator['giat_lock'] = $dataKegiatan->giat_lock;
						$inputs_indikator['id_bidang_urusan'] = $dataKegiatan->id_bidang_urusan;
						$inputs_indikator['id_sub_giat'] = $dataSubKegiatan->id_sub_giat;
						$inputs_indikator['id_giat'] = $dataKegiatan->id_giat;
						$inputs_indikator['id_misi'] = $dataKegiatan->id_misi;
						$inputs_indikator['id_program'] = $dataKegiatan->id_program;
						$inputs_indikator['id_unit'] = $dataKegiatan->id_unit;
						$inputs_indikator['id_sub_unit'] = $data['id_sub_unit'];
						$inputs_indikator['id_visi'] = $dataKegiatan->id_visi;
						$inputs_indikator['kode_bidang_urusan'] = $dataKegiatan->kode_bidang_urusan;
						$inputs_indikator['kode_sub_giat'] = $dataSubKegiatan->kode_sub_giat;
						$inputs_indikator['kode_giat'] = $dataKegiatan->kode_giat;
						$inputs_indikator['kode_kegiatan'] = $dataKegiatan->id_unik;
						$inputs_indikator['kode_program'] = $dataKegiatan->kode_program;
						$inputs_indikator['kode_sasaran'] = $dataKegiatan->kode_sasaran;
						$inputs_indikator['kode_skpd'] = $dataKegiatan->kode_skpd;
						$inputs_indikator['kode_tujuan'] = $dataKegiatan->kode_tujuan;
						$inputs_indikator['nama_bidang_urusan'] = $dataKegiatan->nama_bidang_urusan;
						$inputs_indikator['nama_sub_giat'] = $dataSubKegiatan->nama_sub_giat;
						$inputs_indikator['nama_giat'] = $dataKegiatan->nama_giat;
						$inputs_indikator['nama_program'] = $dataKegiatan->nama_program;
						$inputs_indikator['nama_skpd'] = $dataKegiatan->nama_skpd;
						$inputs_indikator['nama_sub_unit'] = $dataSubUnit->kode_skpd . " " . $dataSubUnit->nama_skpd;
						$inputs_indikator['program_lock'] = $dataKegiatan->program_lock;
						$inputs_indikator['renstra_prog_lock'] = $dataKegiatan->program_lock;
						$inputs_indikator['sasaran_lock'] = $dataKegiatan->sasaran_lock;
						$inputs_indikator['sasaran_teks'] = $dataKegiatan->sasaran_teks;
						$inputs_indikator['tujuan_lock'] = $dataKegiatan->tujuan_lock;
						$inputs_indikator['tujuan_teks'] = $dataKegiatan->tujuan_teks;
						$inputs_indikator['urut_sasaran'] = $dataKegiatan->urut_sasaran;
						$inputs_indikator['urut_tujuan'] = $dataKegiatan->urut_tujuan;
						$inputs_indikator['update_at'] = current_time('mysql');

						$status = $wpdb->update('data_renstra_sub_kegiatan_lokal', $inputs_indikator, [
							'id_unik' => $data['kode_sub_kegiatan'],
							'id_unik_indikator' => 'NOT NULL',
							'tahun_anggaran' => $_POST['tahun_anggaran']
						]);

						if ($status === false) {
							$ket = '';
							if (in_array('administrator', $this->role())) {
								$ket = " | query: " . $wpdb->last_query;
							}
							$ket .= " | error: " . $wpdb->last_error;
							throw new Exception("Gagal simpan data indikator, harap hubungi admin. $ket", 1);
						}

						$wpdb->update("data_pokin_renstra", array("active" => 0), array(
							"id_unik" => $data['kode_sub_kegiatan'],
							"tipe" => 5,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						$wpdb->update("data_pelaksana_renstra", array("active" => 0), array(
							"id_unik" => $data['kode_sub_kegiatan'],
							"tipe" => 5,
							'tahun_anggaran' => $_POST['tahun_anggaran']
						));

						// cek jika jadwal sakip aktif
						if(!empty($data['id_jadwal_wp_sakip'])){
							$_POST['id_skpd'] = $dataKegiatan->id_unit;
							$_POST['id_jadwal_wp_sakip'] = $data['id_jadwal_wp_sakip'];
							$pokin_all = $this->get_data_pohon_kinerja(true);
							$new_pokin = array();
							foreach($pokin_all['data'] as $val){
								$new_pokin[$val->id] = $val;
							}
							if(!is_array($data['pokin-level'])){
								$data['pokin-level'] = array($data['pokin-level']);
							}
							foreach($data['pokin-level'] as $id_pokin){
								if(!empty($new_pokin[$id_pokin])){
									$indikator = array();
									foreach($new_pokin[$id_pokin]->indikator as $ind){
										$indikator[] = $ind->label;
									}
									$data_pokin = array(
										"id_pokin" => $id_pokin,
										"level" => $new_pokin[$id_pokin]->level,
										"label" => $new_pokin[$id_pokin]->label,
										"indikator" => implode(', ', $indikator),
										"tipe" => 5,
										"id_unik" => $data['kode_sub_kegiatan'],
										"id_skpd" => $dataKegiatan->id_unit,
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pokin_renstra
										WHERE id_pokin=%d
											AND id_unik=%s
											AND tipe=5
											AND tahun_anggaran=%d
									", $id_pokin, $data['kode_sub_kegiatan'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pokin_renstra", $data_pokin, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pokin_renstra", $data_pokin);
									}
								}
							}

							$satker_all = $this->get_data_satker(true);
							$new_satker = array();
							foreach($satker_all['data'] as $val){
								$new_satker[$val->id] = $val;
							}
							if(!is_array($data['satker-pelaksana'])){
								$data['satker-pelaksana'] = array($data['satker-pelaksana']);
							}
							foreach($data['satker-pelaksana'] as $id_satker){
								if(!empty($new_satker[$id_satker])){
									$data_satker = array(
										"id_satker" => $id_satker,
										"nama_satker" => $new_satker[$id_satker]->nama,
										"tipe" => 5,
										"id_skpd" => $dataKegiatan->id_unit,
										"id_unik" => $data['kode_sub_kegiatan'],
										"tahun_anggaran" => $_POST['tahun_anggaran'],
										"active" => 1
									);

									$cek_id = $wpdb->get_var($wpdb->prepare("
										SELECT
											id
										FROM data_pelaksana_renstra
										WHERE id_satker=%s
											AND tipe=5
											AND id_unik=%s
											AND tahun_anggaran=%d
									", $id_pokin, $data['kode_sub_kegiatan'], $_POST['tahun_anggaran']));

									if(!empty($cek_id)){
										$wpdb->update("data_pelaksana_renstra", $data_satker, array(
											"id" => $cek_id
										));
									}else{
										$wpdb->insert("data_pelaksana_renstra", $data_satker);
									}
								}
							}
						}

						remove_filter('query', array($this, 'wpsipd_query'));
						echo json_encode([
							'status' => true,
							'message' => 'Sukses simpan sub kegiatan'
						]);
						exit;
					} catch (Exception $e) {
						throw $e;
					}
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function delete_sub_kegiatan_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sub_kegiatan_lokal 
						WHERE id_unik=%s 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
							AND tahun_anggaran=%d
					", $_POST['id_unik'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception("Sub kegiatan sudah digunakan oleh indikator sub kegiatan", 1);
					}

					$wpdb->update('data_renstra_sub_kegiatan_lokal', array('active' => 0), array(
						'id_unik' => $_POST['id_unik'],
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					$wpdb->update('data_pokin_renstra', array('active' => 0), array(
						'id_unik' => $_POST['id_unik'],
						'tipe' => 5,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));
					$wpdb->update('data_pelaksana_renstra', array('active' => 0), array(
						'id_unik' => $_POST['id_unik'],
						'tipe' => 5,
						'tahun_anggaran' => $_POST['tahun_anggaran']
					));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus sub kegiatan'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	private function verify_sub_kegiatan_renstra(array $data)
	{
		if (empty($data['id_sub_kegiatan'])) {
			throw new Exception('Sub Kegiatan wajib dipilih!');
		}

		if (empty($data['id_sub_unit'])) {
			throw new Exception('Sub Unit wajib dipilih!');
		}

		for ($i = 1; $i <= $data['lama_pelaksanaan']; $i++) {
			if ($data['pagu_' . $i . '_usulan'] < 0 || $data['pagu_' . $i . '_usulan'] == '') {
				throw new Exception('Pagu usulan sub kegiatan tahun ke-' . $i . ' tidak boleh kosong atau tidak kurang 0!');
			}
		}
	}

	public function get_indikator_sub_kegiatan_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					if ($_POST['type'] == 1) {
						$sql = $wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sub_kegiatan_lokal 
						WHERE 
							id_unik=%s AND 
							id_unik_indikator IS NOT NULL AND 
							active=1 AND
							tahun_anggaran=%d
						ORDER BY id
						", $_POST['id_unik'], $_POST['tahun_anggaran']);
						$indikator = $wpdb->get_results($sql, ARRAY_A);
					}

					echo json_encode([
						'status' => true,
						'data' => $indikator,
						'message' => 'Sukses get indikator sub kegiatan'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function submit_indikator_sub_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_sub_kegiatan_renstra($data);

					if (!empty($data['id_indikator_usulan'])) {
						$usulan = $wpdb->get_row($wpdb->prepare("
							SELECT 
								id, 
								indikator 
							FROM data_master_indikator_subgiat 
							WHERE id=%d
						", $data['id_indikator_usulan']));
						if (empty($usulan)) {
							throw new Exception('Indikator usulan tidak ditemukan!');
						}
					}

					if (!empty($data['id_indikator'])) {
						$penetapan = $wpdb->get_row($wpdb->prepare("
							SELECT 
								id, 
								indikator 
							FROM data_master_indikator_subgiat 
							WHERE id=%d
						", $data['id_indikator']));
						if (empty($usulan)) {
							throw new Exception('Indikator penetapan tidak ditemukan!');
						}
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sub_kegiatan_lokal
						WHERE id_indikator_usulan=%d
							AND id_unik=%s
							AND id_unik_indikator IS NOT NULL
							AND active=1
							AND tahun_anggaran=%d
					", $data['id_indikator_usulan'], $data['id_unik'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $usulan->indikator . ' sudah ada!');
					}

					$dataSubKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sub_kegiatan_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['id_unik'], $_POST['tahun_anggaran']));

					if (empty($dataSubKegiatan)) {
						throw new Exception('Sub Kegiatan yang dipilih tidak ditemukan!');
					}

					$inputs = [
						'bidur_lock' => $dataSubKegiatan->bidur_lock,
						'giat_lock' => $dataSubKegiatan->giat_lock,
						'id_bidang_urusan' => $dataSubKegiatan->id_bidang_urusan,
						'id_sub_giat' => $dataSubKegiatan->id_sub_giat,
						'id_giat' => $dataSubKegiatan->id_giat,
						'id_misi' => $dataSubKegiatan->id_misi,
						'id_program' => $dataSubKegiatan->id_program,
						'id_unik' => $dataSubKegiatan->id_unik,
						'id_unik_indikator' => $this->generateRandomString(),
						'id_unit' => $dataSubKegiatan->id_unit,
						'id_sub_unit' => $dataSubKegiatan->id_sub_unit,
						'id_visi' => $dataSubKegiatan->id_visi,
						'is_locked' => $dataSubKegiatan->is_locked,
						'is_locked_indikator' => 0,
						'kode_bidang_urusan' => $dataSubKegiatan->kode_bidang_urusan,
						'kode_sub_giat' => $dataSubKegiatan->kode_sub_giat,
						'kode_giat' => $dataSubKegiatan->kode_giat,
						'kode_kegiatan' => $dataSubKegiatan->kode_kegiatan,
						'kode_program' => $dataSubKegiatan->kode_program,
						'kode_sasaran' => $dataSubKegiatan->kode_sasaran,
						'kode_skpd' => $dataSubKegiatan->kode_skpd,
						'kode_tujuan' => $dataSubKegiatan->kode_tujuan,
						'nama_bidang_urusan' => $dataSubKegiatan->nama_bidang_urusan,
						'nama_sub_giat' => $dataSubKegiatan->nama_sub_giat,
						'nama_giat' => $dataSubKegiatan->nama_giat,
						'nama_program' => $dataSubKegiatan->nama_program,
						'nama_skpd' => $dataSubKegiatan->nama_skpd,
						'nama_sub_unit' => $dataSubKegiatan->nama_sub_unit,
						'program_lock' => $dataSubKegiatan->program_lock,
						'renstra_prog_lock' => $dataSubKegiatan->program_lock,
						'sasaran_lock' => $dataSubKegiatan->sasaran_lock,
						'sasaran_teks' => $dataSubKegiatan->sasaran_teks,
						'status' => 1,
						'tujuan_lock' => $dataSubKegiatan->tujuan_lock,
						'tujuan_teks' => $dataSubKegiatan->tujuan_teks,
						'urut_sasaran' => $dataSubKegiatan->urut_sasaran,
						'urut_tujuan' => $dataSubKegiatan->urut_tujuan,
						'tahun_anggaran' => $_POST['tahun_anggaran'],
						'active' => 1
					];
					$inputs['indikator_usulan'] = $usulan->indikator;
					$inputs['id_indikator_usulan'] = $usulan->id;
					$inputs['satuan_usulan'] = $data['satuan_usulan'];
					$inputs['target_1_usulan'] = $data['target_1_usulan'];
					$inputs['target_2_usulan'] = $data['target_2_usulan'];
					$inputs['target_3_usulan'] = $data['target_3_usulan'];
					$inputs['target_4_usulan'] = $data['target_4_usulan'];
					$inputs['target_5_usulan'] = $data['target_5_usulan'];
					$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
					$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
					$inputs['catatan_usulan'] = $data['catatan_usulan'];

					if (in_array('administrator', $this->role())) {
						$inputs['indikator'] = !empty($penetapan->indikator) ? $penetapan->indikator : $usulan->indikator;
						$inputs['id_indikator'] = !empty($penetapan->id) ? $penetapan->id : $usulan->id;
						$inputs['satuan'] = !empty($data['satuan']) || $data['satuan'] == 0 ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) || $data['target_1'] == 0 ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) || $data['target_2'] == 0 ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) || $data['target_3'] == 0 ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) || $data['target_4'] == 0 ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) || $data['target_5'] == 0 ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) || $data['target_awal'] == 0 ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) || $data['target_akhir'] == 0 ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = $data['catatan'];
					}

					$status = $wpdb->insert('data_renstra_sub_kegiatan_lokal', $inputs);

					if ($status === false) {
						$ket = '';
						if (in_array('administrator', $this->role())) {
							$ket = " | query: " . $wpdb->last_query;
						}
						$ket .= " | error: " . $wpdb->last_error;
						throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator sub kegiatan'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function edit_indikator_sub_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$indikator = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sub_kegiatan_lokal 
						WHERE id=%d 
							AND id_unik=%s 
							AND id_unik_indikator IS NOT NULL 
							AND active=1
					", $_POST['id'], $_POST['kode_sub_kegiatan']));

					echo json_encode([
						'status' => true,
						'data' => $indikator
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function update_indikator_sub_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = json_decode(stripslashes($_POST['data']), true);

					$this->verify_indikator_sub_kegiatan_renstra($data);

					if (!empty($data['id_indikator_usulan'])) {
						$usulan = $wpdb->get_row($wpdb->prepare("
							SELECT 
								id, indikator 
							FROM data_master_indikator_subgiat 
							WHERE id=%d
						", $data['id_indikator_usulan']));
						if (empty($usulan)) {
							throw new Exception('Indikator usulan tidak ditemukan!');
						}
					}

					if (!empty($data['id_indikator'])) {
						$penetapan = $wpdb->get_row($wpdb->prepare("
							SELECT 
								id, indikator 
							FROM data_master_indikator_subgiat 
							WHERE id=%d
						", $data['id_indikator']));
						if (empty($usulan)) {
							throw new Exception('Indikator penetapan tidak ditemukan!');
						}
					}

					$id_cek = $wpdb->get_var($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sub_kegiatan_lokal
						WHERE id_indikator_usulan=%d
							AND id_unik=%s
							AND id!=%d
							AND id_unik_indikator IS NOT NULL
							AND active=1
							AND tahun_anggaran=%d
					", $data['id_indikator_usulan'], $data['id_unik'], $data['id'], $_POST['tahun_anggaran']));

					if (!empty($id_cek)) {
						throw new Exception('Indikator : ' . $usulan->indikator . ' sudah ada!');
					}

					$dataSubKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sub_kegiatan_lokal 
						WHERE id_unik=%s 
							AND active=1 
							AND id_unik_indikator IS NULL
							AND tahun_anggaran=%d
					", $data['id_unik'], $_POST['tahun_anggaran']));

					if (empty($dataSubKegiatan)) {
						throw new Exception('Sub Kegiatan yang dipilih tidak ditemukan!');
					}

					$inputs = [
						'id_bidang_urusan' => $dataSubKegiatan->id_bidang_urusan,
						'id_sub_giat' => $dataSubKegiatan->id_sub_giat,
						'id_giat' => $dataSubKegiatan->id_giat,
						'id_misi' => $dataSubKegiatan->id_misi,
						'id_program' => $dataSubKegiatan->id_program,
						'id_unik' => $dataSubKegiatan->id_unik,
						'id_unit' => $dataSubKegiatan->id_unit,
						'id_sub_unit' => $dataSubKegiatan->id_sub_unit,
						'id_visi' => $dataSubKegiatan->id_visi,
						'is_locked' => $dataSubKegiatan->is_locked,
						'kode_bidang_urusan' => $dataSubKegiatan->kode_bidang_urusan,
						'kode_sub_giat' => $dataSubKegiatan->kode_sub_giat,
						'kode_kegiatan' => $dataSubKegiatan->kode_kegiatan,
						'kode_program' => $dataSubKegiatan->kode_program,
						'kode_sasaran' => $dataSubKegiatan->kode_sasaran,
						'kode_skpd' => $dataSubKegiatan->kode_skpd,
						'kode_tujuan' => $dataSubKegiatan->kode_tujuan,
						'nama_bidang_urusan' => $dataSubKegiatan->nama_bidang_urusan,
						'kode_sub_giat' => $dataSubKegiatan->kode_sub_giat,
						'nama_sub_giat' => $dataSubKegiatan->nama_sub_giat,
						'kode_giat' => $dataSubKegiatan->kode_giat,
						'nama_giat' => $dataSubKegiatan->nama_giat,
						'nama_program' => $dataSubKegiatan->nama_program,
						'nama_skpd' => $dataSubKegiatan->nama_skpd,
						'nama_sub_unit' => $dataSubKegiatan->nama_sub_unit,
						'program_lock' => $dataSubKegiatan->program_lock,
						'renstra_prog_lock' => $dataSubKegiatan->program_lock,
						'sasaran_lock' => $dataSubKegiatan->sasaran_lock,
						'sasaran_teks' => $dataSubKegiatan->sasaran_teks,
						'tujuan_lock' => $dataSubKegiatan->tujuan_lock,
						'tujuan_teks' => $dataSubKegiatan->tujuan_teks,
						'urut_sasaran' => $dataSubKegiatan->urut_sasaran,
						'urut_tujuan' => $dataSubKegiatan->urut_tujuan,
					];
					$inputs['indikator_usulan'] = $usulan->indikator;
					$inputs['id_indikator_usulan'] = $usulan->id;
					$inputs['satuan_usulan'] = $data['satuan_usulan'];
					$inputs['target_1_usulan'] = $data['target_1_usulan'];
					$inputs['target_2_usulan'] = $data['target_2_usulan'];
					$inputs['target_3_usulan'] = $data['target_3_usulan'];
					$inputs['target_4_usulan'] = $data['target_4_usulan'];
					$inputs['target_5_usulan'] = $data['target_5_usulan'];
					$inputs['target_awal_usulan'] = $data['target_awal_usulan'];
					$inputs['target_akhir_usulan'] = $data['target_akhir_usulan'];
					$inputs['catatan_usulan'] = $data['catatan_usulan'];

					if (in_array('administrator', $this->role())) {
						$inputs['indikator'] = !empty($penetapan->indikator) || $penetapan->indikator == 0 ? $penetapan->indikator : $usulan->indikator;
						$inputs['id_indikator'] = !empty($penetapan->id) ? $penetapan->id : $usulan->id;
						$inputs['satuan'] = !empty($data['satuan']) || $data['satuan'] == 0 ? $data['satuan'] : $data['satuan_usulan'];
						$inputs['target_1'] = !empty($data['target_1']) || $data['target_1'] == 0 ? $data['target_1'] : $data['target_1_usulan'];
						$inputs['target_2'] = !empty($data['target_2']) || $data['target_2'] == 0 ? $data['target_2'] : $data['target_2_usulan'];
						$inputs['target_3'] = !empty($data['target_3']) || $data['target_3'] == 0 ? $data['target_3'] : $data['target_3_usulan'];
						$inputs['target_4'] = !empty($data['target_4']) || $data['target_4'] == 0 ? $data['target_4'] : $data['target_4_usulan'];
						$inputs['target_5'] = !empty($data['target_5']) || $data['target_5'] == 0 ? $data['target_5'] : $data['target_5_usulan'];
						$inputs['target_awal'] = !empty($data['target_awal']) || $data['target_awal'] == 0 ? $data['target_awal'] : $data['target_awal_usulan'];
						$inputs['target_akhir'] = !empty($data['target_akhir']) || $data['target_akhir'] == 0 ? $data['target_akhir'] : $data['target_akhir_usulan'];
						$inputs['catatan'] = $data['catatan'];
					}

					$status = $wpdb->update('data_renstra_sub_kegiatan_lokal', $inputs, ['id' => $data['id']]);

					if ($status === false) {
						$ket = '';
						if (in_array('administrator', $this->role())) {
							$ket = " | query: " . $wpdb->last_query;
						}
						$ket .= " | error: " . $wpdb->last_error;
						throw new Exception("Gagal simpan data, harap hubungi admin. $ket", 1);
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses simpan indikator sub kegiatan'
					]);
					exit;
				} else {
					throw new Exception('Api key tidak sesuai');
				}
			} else {
				throw new Exception('Format tidak sesuai');
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function delete_indikator_sub_kegiatan_renstra()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->update('data_renstra_sub_kegiatan_lokal', array('active' => 0), array(
						'id' => $_POST['id']
					));

					echo json_encode([
						'status' => true,
						'message' => 'Sukses hapus indikator sub kegiatan'
					]);
					exit;
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	private function verify_indikator_sub_kegiatan_renstra(array $data)
	{
		if (empty($data['id_unik'])) {
			throw new Exception('Sub Kegiatan wajib dipilih!');
		}

		if (empty($data['id_indikator_usulan'])) {
			throw new Exception('Indikator usulan sub kegiatan tidak boleh kosong!');
		}

		if (empty($data['satuan_usulan'])) {
			throw new Exception('Satuan indikator usulan sub kegiatan tidak boleh kosong!');
		}

		if (
			$data['target_awal_usulan'] < 0 || $data['target_awal_usulan'] == ''
		) {
			throw new Exception('Target awal usulan Indikator sub kegiatan tidak boleh kosong atau kurang dari 0!');
		}

		for ($i = 1; $i <= $data['lama_pelaksanaan']; $i++) {
			if (
				$data['target_' . $i . '_usulan'] < 0 || $data['target_' . $i . '_usulan'] == ''
			) {
				throw new Exception('Target usulan Indikator sub kegiatan tahun ke-' . $i . ' tidak boleh kosong atau kurang dari 0!');
			}
		}

		if ($data['target_akhir_usulan'] < 0 || $data['target_akhir_usulan'] == '') {
			throw new Exception('Target akhir usulan Indikator sub kegiatan tidak boleh kosong atau kurang dari 0!');
		}
	}

	protected function debug(array $args)
	{
		echo '<pre>';
		if (!empty($args['sql'])) {
			print_r($args['sql']);
		}
		if (!empty($args['data'])) {
			print_r($args['data']);
		}
		echo '<pre>';
		die();
	}

	public function singkronisasi_kegiatan_renstra()
	{
		return;
		global $wpdb;
		$ret = array(
			'status'    => 'success',
			'message'   => 'Berhasil ubah id_giat ke table kegiatan dan sub_kegiatan! Segarkan/refresh halaman ini untuk melihat perubahannya.'
		);

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option(WPSIPD_API_KEY)) {
					if (in_array('administrator', $this->role())) {

						$tujuan_all = $wpdb->get_results($wpdb->prepare("
							SELECT 
								* 
							FROM data_renstra_tujuan_lokal 
							WHERE active=1 AND 
								id_unik_indikator IS NULL 
							ORDER BY urut_tujuan"), ARRAY_A);

						foreach ($tujuan_all as $keyTujuan => $tujuan_value) {
							$sasaran_all = $wpdb->get_results($wpdb->prepare("
								SELECT 
									* 
								FROM data_renstra_sasaran_lokal 
								WHERE 
									kode_tujuan=%s AND 
									active=1  AND 
									id_unik_indikator IS NULL ORDER BY urut_sasaran
							", $tujuan_value['id_unik']), ARRAY_A);
							foreach ($sasaran_all as $keySasaran => $sasaran_value) {
								$program_all = $wpdb->get_results($wpdb->prepare("
									SELECT 
										* 
									FROM data_renstra_program_lokal 
									WHERE 
										kode_sasaran=%s AND 
										kode_tujuan=%s AND 
										active=1  AND 
										id_unik_indikator IS NULL ORDER BY id
								", $sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);
								foreach ($program_all as $keyProgram => $program_value) {
									$kegiatan_all = $wpdb->get_results($wpdb->prepare("
										SELECT 
											* 
										FROM data_renstra_kegiatan_lokal 
										WHERE 
											kode_program=%s AND 
											kode_sasaran=%s AND 
											kode_tujuan=%s AND 
											active=1 AND
											id_unik_indikator IS NULL ORDER BY id
									", $program_value['id_unik'], $sasaran_value['id_unik'], $tujuan_value['id_unik']), ARRAY_A);
									foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {
										$master = $wpdb->get_row($wpdb->prepare("
												SELECT 
													DISTINCT id_giat 
												FROM data_prog_keg
												WHERE id=%d
													AND id_giat != 0
											", $kegiatan_value['id_giat']));

										if (empty($master)) {
											throw new Exception('Kegiatan tidak ditemukan!' . $kegiatan_value['id_giat']);
										}

										$newData = array(
											'id_giat' => $master->id_giat
										);

										$wpdb->update('data_renstra_kegiatan_lokal', $newData, array(
											'id_unik' => $kegiatan_value['id_unik']
										));

										$wpdb->update('data_renstra_sub_kegiatan_lokal', $newData, array(
											'kode_kegiatan' => $kegiatan_value['id_unik']
										));
									}
								}
							}
						}

						echo json_encode($ret);
						exit;
					} else {
						throw new Exception("Anda tidak punya kewenangan untuk melakukan ini!", 1);
					}
				} else {
					throw new Exception("Api Key tidak sesuai!", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai!", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit;
		}
	}

	public function get_pagu_program()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = [
						'penetapan' => [
							'pagu_1' => 0,
							'pagu_2' => 0,
							'pagu_3' => 0,
							'pagu_4' => 0,
							'pagu_5' => 0,
						],
						'usulan' => [
							'pagu_1' => 0,
							'pagu_2' => 0,
							'pagu_3' => 0,
							'pagu_4' => 0,
							'pagu_5' => 0,
						]
					];

					$kegiatan_all = $wpdb->get_results($wpdb->prepare("
						SELECT 
							id_unik
						FROM data_renstra_kegiatan_lokal
						WHERE
							kode_program=%s AND
							id_unik_indikator IS NULL ORDER BY nama_program
					", $_POST['kode_program']), ARRAY_A);

					foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {
						$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare("
							SELECT 
								COALESCE(pagu_1, 0) AS pagu_1, 
								COALESCE(pagu_2, 0) AS pagu_2, 
								COALESCE(pagu_3, 0) AS pagu_3, 
								COALESCE(pagu_4, 0) AS pagu_4, 
								COALESCE(pagu_5, 0) AS pagu_5,
								COALESCE(pagu_1_usulan, 0) AS pagu_1_usulan, 
								COALESCE(pagu_2_usulan, 0) AS pagu_2_usulan, 
								COALESCE(pagu_3_usulan, 0) AS pagu_3_usulan, 
								COALESCE(pagu_4_usulan, 0) AS pagu_4_usulan, 
								COALESCE(pagu_5_usulan, 0) AS pagu_5_usulan 
							FROM data_renstra_sub_kegiatan_lokal 
							WHERE 
								kode_kegiatan=%s AND 
								id_unik_indikator IS NULL ORDER BY nama_sub_giat
						", $kegiatan_value['id_unik']), ARRAY_A);

						foreach ($sub_kegiatan_all as $sub_kegiatan) {
							$data['penetapan']['pagu_1'] += $sub_kegiatan['pagu_1'];
							$data['penetapan']['pagu_2'] += $sub_kegiatan['pagu_2'];
							$data['penetapan']['pagu_3'] += $sub_kegiatan['pagu_3'];
							$data['penetapan']['pagu_4'] += $sub_kegiatan['pagu_4'];
							$data['penetapan']['pagu_5'] += $sub_kegiatan['pagu_5'];
							$data['usulan']['pagu_1'] += $sub_kegiatan['pagu_1_usulan'];
							$data['usulan']['pagu_2'] += $sub_kegiatan['pagu_2_usulan'];
							$data['usulan']['pagu_3'] += $sub_kegiatan['pagu_3_usulan'];
							$data['usulan']['pagu_4'] += $sub_kegiatan['pagu_4_usulan'];
							$data['usulan']['pagu_5'] += $sub_kegiatan['pagu_5_usulan'];
						}
					}

					$data_indikator_program = $wpdb->get_row($wpdb->prepare("
						SELECT
							COALESCE(SUM(pagu_1), 0) AS pagu_1,
							COALESCE(SUM(pagu_2), 0) AS pagu_2,
							COALESCE(SUM(pagu_3), 0) AS pagu_3,
							COALESCE(SUM(pagu_4), 0) AS pagu_4,
							COALESCE(SUM(pagu_5), 0) AS pagu_5,
							COALESCE(SUM(pagu_1_usulan), 0) AS pagu_1_usulan,
							COALESCE(SUM(pagu_2_usulan), 0) AS pagu_2_usulan,
							COALESCE(SUM(pagu_3_usulan), 0) AS pagu_3_usulan,
							COALESCE(SUM(pagu_4_usulan), 0) AS pagu_4_usulan,
							COALESCE(SUM(pagu_5_usulan), 0) AS pagu_5_usulan
						FROM data_renstra_program_lokal
						WHERE
							id_unik=%s AND
							id_unik_indikator IS NOT NULL
					", $_POST['kode_program']));

					$data['penetapan']['pagu_1'] = $data['penetapan']['pagu_1'] - $data_indikator_program->pagu_1;
					$data['penetapan']['pagu_2'] = $data['penetapan']['pagu_2'] - $data_indikator_program->pagu_2;
					$data['penetapan']['pagu_3'] = $data['penetapan']['pagu_3'] - $data_indikator_program->pagu_3;
					$data['penetapan']['pagu_4'] = $data['penetapan']['pagu_4'] - $data_indikator_program->pagu_4;
					$data['penetapan']['pagu_5'] = $data['penetapan']['pagu_5'] - $data_indikator_program->pagu_5;
					$data['usulan']['pagu_1'] = $data['usulan']['pagu_1'] - $data_indikator_program->pagu_1_usulan;
					$data['usulan']['pagu_2'] = $data['usulan']['pagu_2'] - $data_indikator_program->pagu_2_usulan;
					$data['usulan']['pagu_3'] = $data['usulan']['pagu_3'] - $data_indikator_program->pagu_3_usulan;
					$data['usulan']['pagu_4'] = $data['usulan']['pagu_4'] - $data_indikator_program->pagu_4_usulan;
					$data['usulan']['pagu_5'] = $data['usulan']['pagu_5'] - $data_indikator_program->pagu_5_usulan;

					echo json_encode([
						'status' => true,
						'data' => $data
					]);
					exit();
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function get_pagu_kegiatan()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = [
						'penetapan' => [
							'pagu_1' => 0,
							'pagu_2' => 0,
							'pagu_3' => 0,
							'pagu_4' => 0,
							'pagu_5' => 0,
						],
						'usulan' => [
							'pagu_1' => 0,
							'pagu_2' => 0,
							'pagu_3' => 0,
							'pagu_4' => 0,
							'pagu_5' => 0,
						]
					];

					$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare("
						SELECT 
							COALESCE(pagu_1, 0) AS pagu_1, 
							COALESCE(pagu_2, 0) AS pagu_2, 
							COALESCE(pagu_3, 0) AS pagu_3, 
							COALESCE(pagu_4, 0) AS pagu_4, 
							COALESCE(pagu_5, 0) AS pagu_5,
							COALESCE(pagu_1_usulan, 0) AS pagu_1_usulan, 
							COALESCE(pagu_2_usulan, 0) AS pagu_2_usulan, 
							COALESCE(pagu_3_usulan, 0) AS pagu_3_usulan, 
							COALESCE(pagu_4_usulan, 0) AS pagu_4_usulan, 
							COALESCE(pagu_5_usulan, 0) AS pagu_5_usulan 
						FROM data_renstra_sub_kegiatan_lokal 
						WHERE 
							kode_kegiatan=%s AND 
							id_unik_indikator IS NULL ORDER BY nama_sub_giat
					", $_POST['kode_kegiatan']), ARRAY_A);

					foreach ($sub_kegiatan_all as $sub_kegiatan) {
						$data['penetapan']['pagu_1'] += $sub_kegiatan['pagu_1'];
						$data['penetapan']['pagu_2'] += $sub_kegiatan['pagu_2'];
						$data['penetapan']['pagu_3'] += $sub_kegiatan['pagu_3'];
						$data['penetapan']['pagu_4'] += $sub_kegiatan['pagu_4'];
						$data['penetapan']['pagu_5'] += $sub_kegiatan['pagu_5'];
						$data['usulan']['pagu_1'] += $sub_kegiatan['pagu_1_usulan'];
						$data['usulan']['pagu_2'] += $sub_kegiatan['pagu_2_usulan'];
						$data['usulan']['pagu_3'] += $sub_kegiatan['pagu_3_usulan'];
						$data['usulan']['pagu_4'] += $sub_kegiatan['pagu_4_usulan'];
						$data['usulan']['pagu_5'] += $sub_kegiatan['pagu_5_usulan'];
					}

					$data_indikator_kegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT
							COALESCE(SUM(pagu_1), 0) AS pagu_1,
							COALESCE(SUM(pagu_2), 0) AS pagu_2,
							COALESCE(SUM(pagu_3), 0) AS pagu_3,
							COALESCE(SUM(pagu_4), 0) AS pagu_4,
							COALESCE(SUM(pagu_5), 0) AS pagu_5,
							COALESCE(SUM(pagu_1_usulan), 0) AS pagu_1_usulan,
							COALESCE(SUM(pagu_2_usulan), 0) AS pagu_2_usulan,
							COALESCE(SUM(pagu_3_usulan), 0) AS pagu_3_usulan,
							COALESCE(SUM(pagu_4_usulan), 0) AS pagu_4_usulan,
							COALESCE(SUM(pagu_5_usulan), 0) AS pagu_5_usulan
						FROM data_renstra_kegiatan_lokal
						WHERE
							id_unik=%s AND
							id_unik_indikator IS NOT NULL
					", $_POST['kode_kegiatan']));

					$data['penetapan']['pagu_1'] = $data['penetapan']['pagu_1'] - $data_indikator_kegiatan->pagu_1;
					$data['penetapan']['pagu_2'] = $data['penetapan']['pagu_2'] - $data_indikator_kegiatan->pagu_2;
					$data['penetapan']['pagu_3'] = $data['penetapan']['pagu_3'] - $data_indikator_kegiatan->pagu_3;
					$data['penetapan']['pagu_4'] = $data['penetapan']['pagu_4'] - $data_indikator_kegiatan->pagu_4;
					$data['penetapan']['pagu_5'] = $data['penetapan']['pagu_5'] - $data_indikator_kegiatan->pagu_5;
					$data['usulan']['pagu_1'] = $data['usulan']['pagu_1'] - $data_indikator_kegiatan->pagu_1_usulan;
					$data['usulan']['pagu_2'] = $data['usulan']['pagu_2'] - $data_indikator_kegiatan->pagu_2_usulan;
					$data['usulan']['pagu_3'] = $data['usulan']['pagu_3'] - $data_indikator_kegiatan->pagu_3_usulan;
					$data['usulan']['pagu_4'] = $data['usulan']['pagu_4'] - $data_indikator_kegiatan->pagu_4_usulan;
					$data['usulan']['pagu_5'] = $data['usulan']['pagu_5'] - $data_indikator_kegiatan->pagu_5_usulan;

					echo json_encode([
						'status' => true,
						'data' => $data
					]);
					exit();
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function get_objek_belanja()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					$items = array();
					$item[""] = "Pilih Objek";
					$item["BTL-GAJI"] = "Belanja Gaji dan Tunjangan ASN";
					$item["BARJAS-MODAL"] = "Belanja Barang Jasa dan Modal";
					$item["BUNGA"] = "Belanja Bunga";
					$item["SUBSIDI"] = "Belanja Subsidi";
					$item["HIBAH-BRG"] = "Belanja Hibah (Barang/Jasa";
					$item["HIBAH"] = "Belanja Hibah (Uang";
					$item["BANSOS-BRG"] = "Belanja Bantuan Sosial (Barang/Jasa";
					$item["BANSOS"] = "Belanja Bantuan Sosial (Uang";
					$item["BAGI-HASIL"] = "Belanja Bagi Hasil";
					$item["BANKEU"] = "Belanja Bantuan Keuangan Umum";
					$item["BANKEU-KHUSUS"] = "Belanja Bantuan Keuangan Khusus";
					$item["BTT"] = "Belanja Tidak Terduga (BTT";
					$item["BOS"] = "Dana BOS (BOS Pusat";
					$item["BLUD"] = "Belanja Operasional (BLUD";
					$item["TANAH"] = "Pembebasan Tanah/ Lahan";

					echo json_encode([
						'status' => true,
						'items' => $item
					]);
					exit();
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function list_perangkat_daerah()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$all_skpd = array();
					$list_skpd_options = '<option value="">Pilih Unit Kerja</option><option value="all">Semua Unit Kerja</option>';
					$nama_skpd = "";
					if (
						in_array("PA", $this->role())
						|| in_array("KPA", $this->role())
						|| in_array("PLT", $this->role())
						|| in_array("PLH", $this->role())
					) {
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
							group by id_skpd", $nipkepala[0], $_POST['tahun_anggaran']), ARRAY_A);
						foreach ($skpd_db as $skpd) {
							$nama_skpd = '<br>' . $skpd['kode_skpd'] . ' ' . $skpd['nama_skpd'];
							$all_skpd[] = $skpd;
							$list_skpd_options .= '<option value="' . $skpd['id_skpd'] . '">' . $skpd['kode_skpd'] . ' ' . $skpd['nama_skpd'] . '</option>';
							if ($skpd['is_skpd'] == 1) {
								$sub_skpd_db = $wpdb->get_results($wpdb->prepare("
									SELECT 
										nama_skpd, 
										id_skpd, 
										kode_skpd,
										is_skpd
									from data_unit 
									where id_unit=%d 
										and tahun_anggaran=%d
										and is_skpd=0
									group by id_skpd", $skpd['id_skpd'], $_POST['tahun_anggaran']), ARRAY_A);
								foreach ($sub_skpd_db as $sub_skpd) {
									$all_skpd[] = $sub_skpd;
									$list_skpd_options .= '<option value="' . $sub_skpd['id_skpd'] . '">-- ' . $sub_skpd['kode_skpd'] . ' ' . $sub_skpd['nama_skpd'] . '</option>';
								}
							}
						}
					} else if (
						in_array("administrator", $this->role())
						|| in_array("tapd_keu", $this->role())
						|| in_array("mitra_bappeda", $this->role())
					) {
						$skpd_mitra = $wpdb->get_results($wpdb->prepare("
							SELECT 
								nama_skpd, 
								id_skpd, 
								kode_skpd,
								is_skpd 
							from data_unit 
							where active=1 
								and tahun_anggaran=%d
							group by id_skpd
							order by id_unit ASC, kode_skpd ASC", $_POST['tahun_anggaran']), ARRAY_A);
						foreach ($skpd_mitra as $k => $v) {
							$all_skpd[] = $v;
							if ($v['is_skpd'] == 1) {
								$list_skpd_options .= '<option value="' . $v['id_skpd'] . '">' . $v['kode_skpd'] . ' ' . $v['nama_skpd'] . '</option>';
							} else {
								$list_skpd_options .= '<option value="' . $v['id_skpd'] . '">-- ' . $v['kode_skpd'] . ' ' . $v['nama_skpd'] . '</option>';
							}
						}
					}

					echo json_encode([
						'status' => true,
						'list_skpd_options' => $list_skpd_options
					]);
					exit();
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function view_pagu_total_renja()
	{
		global $wpdb;
		try {
			$nama_pemda = get_option('_crb_daerah');
			$id_unit = $_POST['id_unit'];
			$tahun_anggaran = $_POST['tahun_anggaran'];
			$id_jadwal_lokal = $_POST['id_jadwal_lokal'];

			$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
				SELECT 
					j.nama AS nama_jadwal,
					j.tahun_anggaran,
					j.status,
					t.nama_tipe 
				FROM `data_jadwal_lokal` j
				INNER JOIN `data_tipe_perencanaan` t on t.id=j.id_tipe 
					WHERE j.id_jadwal_lokal=%d", $id_jadwal_lokal));

			$_suffix = '';
			$where = '';
			if ($jadwal_lokal->status == 1) {
				$_suffix = '_history';
				$where = ' AND id_jadwal=' . $wpdb->prepare("%d", $id_jadwal_lokal);
			}

			$_suffix_sipd = '';
			if (strpos($jadwal_lokal->nama_tipe, '_sipd') == false) {
				$_suffix_sipd = '_lokal';
			}

			$where_skpd = '';
			if (!empty($id_unit)) {
				if ($id_unit != 'all') {
					$where_skpd = "AND id_skpd=" . $wpdb->prepare("%d", $id_unit);
				}
			}

			$sql = $wpdb->prepare("
				SELECT 
					id_skpd,
					kode_skpd,
					nama_skpd 
				FROM data_unit 
				WHERE tahun_anggaran=%d
					" . $where_skpd . "
					AND active=1
				ORDER BY kode_skpd ASC
			", $tahun_anggaran);

			$units = $wpdb->get_results($sql, ARRAY_A);

			$data_all = array(
				'data' => array(),
				'pagu_usulan_kab' => 0,
				'pagu_penetapan_kab' => 0,
				'pagu_sipd' => 0
			);

			$data_pendapatan = array(
				'data' => array(),
				'total_pendapatan' => 0,
				'total_pendapatan_sipd' => 0
			);

			$data_pembiayaan_penerimaan = array(
				'data' => array(),
				'total_penerimaan' => 0,
				'total_penerimaan_sipd' => 0,
			);

			$data_pembiayaan_pengeluaran = array(
				'data' => array(),
				'total_pengeluaran' => 0,
				'total_pengeluaran_sipd' => 0,
			);

			foreach ($units as $key => $unit) {

				$pagu_usulan = $_suffix_sipd == '_lokal' ? 'COALESCE(SUM(s.pagu_usulan), 0) as pagu_usulan,' : '';
				/** Mendapatkan data belanja */
				$sql = $wpdb->prepare("
					SELECT 
					  " . $pagu_usulan . "
					  COALESCE(SUM(s.pagu), 0) as pagu_penetapan
					FROM data_sub_keg_bl" . $_suffix_sipd . "" . $_suffix . " s
					WHERE s.tahun_anggaran=%d
					  AND s.id_sub_skpd=%d
					  " . $where . "
					  AND s.active=1", $tahun_anggaran, $unit['id_skpd']);

				$pagu = $wpdb->get_row($sql, ARRAY_A);

				if (empty($data_all['data'][$unit['kode_skpd']])) {
					$pagu_sipd = $wpdb->get_row($wpdb->prepare("
				        select 
				            sum(pagu) as pagu
				        from data_sub_keg_bl
				        where tahun_anggaran=%d
				            and active=1
				            and id_sub_skpd=%d
				        order by id ASC
				    ", $tahun_anggaran, $unit['id_skpd']), ARRAY_A);

					$data_pagu_usulan = !empty($pagu['pagu_usulan']) ?: 0;

					$data_all['data'][$unit['kode_skpd']] = [
						'id_skpd' => $unit['id_skpd'],
						'kode_skpd' => $unit['kode_skpd'],
						'nama_skpd' => $unit['nama_skpd'],
						'pagu_usulan' => $data_pagu_usulan,
						'pagu_penetapan' => $pagu['pagu_penetapan'],
						'pagu_sipd' => $pagu_sipd['pagu']
					];

					$data_all['pagu_usulan_kab'] += $data_pagu_usulan;
					$data_all['pagu_penetapan_kab'] += $pagu['pagu_penetapan'];
					$data_all['pagu_sipd'] += $pagu_sipd['pagu'];
				}

				/** Mendapatkan data pendapatan */
				$sql_pendapatan = $wpdb->prepare("
					SELECT
						COALESCE(SUM(total), 0) as total_pendapatan
					FROM data_pendapatan" . $_suffix_sipd . "" . $_suffix . "
					WHERE tahun_anggaran=%d
						AND id_skpd=%d
						" . $where . "
						AND active=1", $tahun_anggaran, $unit['id_skpd']);
				$total_pendapatan = $wpdb->get_row($sql_pendapatan, ARRAY_A);

				if (empty($data_pendapatan['data'][$unit['kode_skpd']])) {
					$total_pendapatan_sipd = $wpdb->get_row($wpdb->prepare("
				        select 
				            sum(total) as total
				        from data_pendapatan
				        where tahun_anggaran=%d
				            and active=1
				            and id_skpd=%d
				        order by id ASC
				    ", $tahun_anggaran, $unit['id_skpd']), ARRAY_A);

					$data_pendapatan['data'][$unit['kode_skpd']] = [
						'id_skpd' => $unit['id_skpd'],
						'kode_skpd' => $unit['kode_skpd'],
						'nama_skpd' => $unit['nama_skpd'],
						'total_pendapatan' => $total_pendapatan['total_pendapatan'],
						'total_pendapatan_sipd' => $total_pendapatan_sipd['total']
					];

					$data_pendapatan['total_pendapatan'] += $total_pendapatan['total_pendapatan'];
					$data_pendapatan['total_pendapatan_sipd'] += $total_pendapatan_sipd['total'];
				}
				/** Mendapatkan data pembiayaan penerimaan*/
				$sql_penerimaan = $wpdb->prepare("
					SELECT
						COALESCE(SUM(total), 0) as total_penerimaan
					FROM data_pembiayaan" . $_suffix_sipd . "" . $_suffix . "
					WHERE tahun_anggaran=%d
						AND id_skpd=%d
						AND active=1
						" . $where . "
						AND type='penerimaan'", $tahun_anggaran, $unit['id_skpd']);
				$total_penerimaan = $wpdb->get_row($sql_penerimaan, ARRAY_A);

				if (empty($data_pembiayaan_penerimaan['data'][$unit['kode_skpd']])) {
					$total_penerimaan_sipd = $wpdb->get_row($wpdb->prepare("
				        select 
				            sum(total) as total
				        from data_pembiayaan
				        where tahun_anggaran=%d
				            and active=1
				            and id_skpd=%d
							and type='penerimaan'
				        order by id ASC
				    ", $tahun_anggaran, $unit['id_skpd']), ARRAY_A);

					$data_pembiayaan_penerimaan['data'][$unit['kode_skpd']] = [
						'id_skpd' => $unit['id_skpd'],
						'kode_skpd' => $unit['kode_skpd'],
						'nama_skpd' => $unit['nama_skpd'],
						'total_penerimaan' => $total_penerimaan['total_penerimaan'],
						'total_penerimaan_sipd' => $total_penerimaan_sipd['total']
					];

					$data_pembiayaan_penerimaan['total_penerimaan'] += $total_penerimaan['total_penerimaan'];
					$data_pembiayaan_penerimaan['total_penerimaan_sipd'] += $total_penerimaan_sipd['total'];
				}
				/** Mendapatkan data pembiayaan pengeluaran*/
				$sql_pengeluaran = $wpdb->prepare("
					SELECT
						COALESCE(SUM(total), 0) as total_pengeluaran
					FROM data_pembiayaan" . $_suffix_sipd . "" . $_suffix . "
					WHERE tahun_anggaran=%d
						AND id_skpd=%d
						AND active=1
						" . $where . "
						AND type='pengeluaran'", $tahun_anggaran, $unit['id_skpd']);
				$total_pengeluaran = $wpdb->get_row($sql_pengeluaran, ARRAY_A);

				if (empty($data_pembiayaan_pengeluaran['data'][$unit['kode_skpd']])) {
					$total_pengeluaran_sipd = $wpdb->get_row($wpdb->prepare("
				        select 
				            sum(total) as total
				        from data_pembiayaan
				        where tahun_anggaran=%d
				            and active=1
				            and id_skpd=%d
							and type='pengeluaran'
				        order by id ASC
				    ", $tahun_anggaran, $unit['id_skpd']), ARRAY_A);

					$data_pembiayaan_pengeluaran['data'][$unit['kode_skpd']] = [
						'id_skpd' => $unit['id_skpd'],
						'kode_skpd' => $unit['kode_skpd'],
						'nama_skpd' => $unit['nama_skpd'],
						'total_pengeluaran' => $total_pengeluaran['total_pengeluaran'],
						'total_pengeluaran_sipd' => $total_pengeluaran_sipd['total']
					];

					$data_pembiayaan_pengeluaran['total_pengeluaran'] += $total_pengeluaran['total_pengeluaran'];
					$data_pembiayaan_pengeluaran['total_pengeluaran_sipd'] += $total_pengeluaran_sipd['total'];
				}
			}

			/** Tabel belanja */
			$body = '';
			$no = 1;
			foreach ($data_all['data'] as $key => $unit) {
				$warning = '';
				if ($unit['pagu_penetapan'] != $unit['pagu_sipd']) {
					$warning = 'background: #f9d9d9;';
				}
				$body .= '
					<tr data-idskpd="' . $unit['id_skpd'] . '">
						<td class="kiri atas kanan bawah text_tengah">' . $no . '</td>
						<td class="atas kanan bawah">' . $unit['kode_skpd'] . ' ' . $unit['nama_skpd'] . '</td>
						<td class="atas kanan bawah text_kanan">' . $this->_number_format($unit['pagu_usulan']) . '</td>
						<td class="atas kanan bawah text_kanan">' . $this->_number_format($unit['pagu_penetapan']) . '</td>
						<td style="' . $warning . '" class="atas kanan bawah text_kanan">' . $this->_number_format($unit['pagu_sipd']) . '</td>
					</tr>';
				$no++;
			}

			$warning = '';
			if ($data_all['pagu_penetapan_kab'] != $data_all['pagu_sipd']) {
				$warning = 'background: #f9d9d9;';
			}
			$footer = '<tr>
						<td class="kiri atas kanan bawah text_tengah" colspan="2"><b>TOTAL PAGU BELANJA</b></td>
						<td class="atas kanan bawah text_kanan"><b>' . $this->_number_format($data_all['pagu_usulan_kab']) . '</b></td>
						<td class="atas kanan bawah text_kanan"><b>' . $this->_number_format($data_all['pagu_penetapan_kab']) . '</b></td>
						<td style="' . $warning . '" class="atas kanan bawah text_kanan"><b>' . $this->_number_format($data_all['pagu_sipd']) . '</b></td>
					</tr>';

			$html = '<div id="preview" class="card bg-light m-3 p-3 shadow-md" style="overflow-x : auto; overflow-y : auto; height : 90vh">
					<h4 style="text-align: center; margin: 0; font-weight: bold;">PAGU TOTAL BELANJA RENJA Per Unit Kerja 
					<br>Tahun ' . $jadwal_lokal->tahun_anggaran . ' ' . $nama_pemda . '
					<br>' . $jadwal_lokal->nama_jadwal . '
					</h4>
					<br>
					<button class="btn btn-warning" onclick="cek_pemutakhiran();">Cek Pemutakhiran</button>
					<div id="tabel-pemutakhiran-belanja"></div>
					<br>
					<table id="table-renja" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 90%; border: 0; table-layout: fixed;" contenteditable="false">
						<thead>
							<tr>
								<th style="width: 19px;" class="kiri atas kanan bawah text_tengah text_blok">No</th>
								<th class="atas kanan bawah text_tengah text_blok">Nama SKPD</th>
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Usulan</th>
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu Penetapan</th>
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Pagu SIPD</th>
							</tr>
						</thead>
						<tbody>' . $body . '</tbody>
						<tfoot>' . $footer . '</tfoot>
					</table>';

			/** Tabel pendapatan */
			$body_pend = '';
			$no_pendapatan = 1;
			foreach ($data_pendapatan['data'] as $key => $unit) {
				$warning = '';
				if ($unit['total_pendapatan'] != $unit['total_pendapatan_sipd']) {
					$warning = 'background: #f9d9d9;';
				}
				$body_pend .= '
					<tr data-idskpd-pendapatan="' . $unit['id_skpd'] . '">
						<td class="kiri atas kanan bawah text_tengah">' . $no_pendapatan . '</td>
						<td class="atas kanan bawah">' . $unit['kode_skpd'] . ' ' . $unit['nama_skpd'] . '</td>
						<td class="atas kanan bawah text_kanan">' . $this->_number_format($unit['total_pendapatan']) . '</td>
						<td style="' . $warning . '" class="atas kanan bawah text_kanan">' . $this->_number_format($unit['total_pendapatan_sipd']) . '</td>
					</tr>';
				$no_pendapatan++;
			}

			$warning = '';
			if ($data_pendapatan['total_pendapatan'] != $data_pendapatan['total_pendapatan_sipd']) {
				$warning = 'background: #f9d9d9;';
			}
			$footer_pend = '<tr>
					<td class="kiri atas kanan bawah text_tengah" colspan="2"><b>TOTAL PAGU PENDAPATAN</b></td>
					<td class="atas kanan bawah text_kanan"><b>' . $this->_number_format($data_pendapatan['total_pendapatan']) . '</b></td>
					<td style="' . $warning . '" class="atas kanan bawah text_kanan"><b>' . $this->_number_format($data_pendapatan['total_pendapatan_sipd']) . '</b></td>
				</tr>';

			$html .= '<br>
					<br>
					<h4 style="text-align: center; margin: 0; font-weight: bold;">PAGU TOTAL PENDAPATAN Per Unit Kerja 
					<br>Tahun ' . $jadwal_lokal->tahun_anggaran . ' ' . $nama_pemda . '
					<br>' . $jadwal_lokal->nama_jadwal . '
					</h4><br>
					<table id="table-renja-pendapatan" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 90%; border: 0; table-layout: fixed;" contenteditable="false">
						<thead>
							<tr>
								<th style="width: 19px;" class="kiri atas kanan bawah text_tengah text_blok">No</th>
								<th class="atas kanan bawah text_tengah text_blok">Nama SKPD</th>
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Total Pendapatan</th>
								<th style="width: 145px;" class="atas kanan bawah text_tengah text_blok">Total Pendapatan SIPD</th>
							</tr>
						</thead>
						<tbody>' . $body_pend . '</tbody>
						<tfoot>' . $footer_pend . '</tfoot>
					</table>';

			/** Tabel pembiayaan penerimaan */
			$body_penerimaan = '';
			$no_penerimaan = 1;
			foreach ($data_pembiayaan_penerimaan['data'] as $key => $unit) {
				$warning = '';
				if ($unit['total_penerimaan'] != $unit['total_penerimaan_sipd']) {
					$warning = 'background: #f9d9d9;';
				}
				$body_penerimaan .= '
					<tr data-idskpd-pembiayaan-penerimaan="' . $unit['id_skpd'] . '">
						<td class="kiri atas kanan bawah text_tengah">' . $no_penerimaan . '</td>
						<td class="atas kanan bawah">' . $unit['kode_skpd'] . ' ' . $unit['nama_skpd'] . '</td>
						<td class="atas kanan bawah text_kanan">' . $this->_number_format($unit['total_penerimaan']) . '</td>
						<td style="' . $warning . '" class="atas kanan bawah text_kanan">' . $this->_number_format($unit['total_penerimaan_sipd']) . '</td>
					</tr>';
				$no_penerimaan++;
			}

			$warning = '';
			if ($data_pembiayaan_penerimaan['total_penerimaan'] != $data_pembiayaan_penerimaan['total_penerimaan_sipd']) {
				$warning = 'background: #f9d9d9;';
			}
			$footer_penerimaan = '<tr>
					<td class="kiri atas kanan bawah text_tengah" colspan="2"><b>TOTAL PAGU PEMBIAYAAN PENERIMAAN</b></td>
					<td class="atas kanan bawah text_kanan"><b>' . $this->_number_format($data_pembiayaan_penerimaan['total_penerimaan']) . '</b></td>
					<td style="' . $warning . '" class="atas kanan bawah text_kanan"><b>' . $this->_number_format($data_pembiayaan_penerimaan['total_penerimaan_sipd']) . '</b></td>
				</tr>';

			$html .= '<br>
					<br>
					<h4 style="text-align: center; margin: 0; font-weight: bold;">PAGU TOTAL PEMBIAYAAN PENERIMAAN Per Unit Kerja 
					<br>Tahun ' . $jadwal_lokal->tahun_anggaran . ' ' . $nama_pemda . '
					<br>' . $jadwal_lokal->nama_jadwal . '
					</h4><br>
					<table id="table-renja-penerimaan" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 90%; border: 0; table-layout: fixed;" contenteditable="false">
						<thead>
							<tr>
								<th style="width: 19px;" class="kiri atas kanan bawah text_tengah text_blok">No</th>
								<th class="atas kanan bawah text_tengah text_blok">Nama SKPD</th>
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Total Penerimaan</th>
								<th style="width: 145px;" class="atas kanan bawah text_tengah text_blok">Total Penerimaan SIPD</th>
							</tr>
						</thead>
						<tbody>' . $body_penerimaan . '</tbody>
						<tfoot>' . $footer_penerimaan . '</tfoot>
					</table>';

			/** Tabel pembiayaan pengeluaran */
			$body_pengeluaran = '';
			$no_pengeluaran = 1;
			foreach ($data_pembiayaan_pengeluaran['data'] as $key => $unit) {
				$warning = '';
				if ($unit['total_pengeluaran'] != $unit['total_pengeluaran_sipd']) {
					$warning = 'background: #f9d9d9;';
				}
				$body_pengeluaran .= '
					<tr data-idskpd-pembiayaan-pengeluaran="' . $unit['id_skpd'] . '">
						<td class="kiri atas kanan bawah text_tengah">' . $no_pengeluaran . '</td>
						<td class="atas kanan bawah">' . $unit['kode_skpd'] . ' ' . $unit['nama_skpd'] . '</td>
						<td class="atas kanan bawah text_kanan">' . $this->_number_format($unit['total_pengeluaran']) . '</td>
						<td style="' . $warning . '" class="atas kanan bawah text_kanan">' . $this->_number_format($unit['total_pengeluaran_sipd']) . '</td>
					</tr>';
				$no_pengeluaran++;
			}

			$warning = '';
			if ($data_pembiayaan_pengeluaran['total_pengeluaran'] != $data_pembiayaan_pengeluaran['total_pengeluaran_sipd']) {
				$warning = 'background: #f9d9d9;';
			}
			$footer_pengeluaran = '<tr>
					<td class="kiri atas kanan bawah text_tengah" colspan="2"><b>TOTAL PAGU PEMBIAYAAN PENGELUARAN</b></td>
					<td class="atas kanan bawah text_kanan"><b>' . $this->_number_format($data_pembiayaan_pengeluaran['total_pengeluaran']) . '</b></td>
					<td style="' . $warning . '" class="atas kanan bawah text_kanan"><b>' . $this->_number_format($data_pembiayaan_pengeluaran['total_pengeluaran_sipd']) . '</b></td>
				</tr>';

			$html .= '<br>
					<br>
					<h4 style="text-align: center; margin: 0; font-weight: bold;">PAGU TOTAL PEMBIAYAAN PENGELUARAN Per Unit Kerja 
					<br>Tahun ' . $jadwal_lokal->tahun_anggaran . ' ' . $nama_pemda . '
					<br>' . $jadwal_lokal->nama_jadwal . '
					</h4><br>
					<table id="table-renja-pengeluaran" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 90%; border: 0; table-layout: fixed;" contenteditable="false">
						<thead>
							<tr>
								<th style="width: 19px;" class="kiri atas kanan bawah text_tengah text_blok">No</th>
								<th class="atas kanan bawah text_tengah text_blok">Nama SKPD</th>
								<th style="width: 140px;" class="atas kanan bawah text_tengah text_blok">Total Pengeluaran</th>
								<th style="width: 145px;" class="atas kanan bawah text_tengah text_blok">Total Pengeluaran SIPD</th>
							</tr>
						</thead>
						<tbody>' . $body_pengeluaran . '</tbody>
						<tfoot>' . $footer_pengeluaran . '</tfoot>
					</table>
					</div>';

			echo json_encode([
				'status' => true,
				'html' => $html
			]);
			exit();
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function cek_pemutakhiran_total()
	{
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil get data cek pemutakhiran!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
				$sub_keg = $wpdb->get_results($wpdb->prepare("
					SELECT
						count(s.id) as jml,
						id_sub_skpd
					FROM data_sub_keg_bl_lokal s
					LEFT JOIN data_prog_keg k on s.id_sub_giat=k.id_sub_giat
						AND s.tahun_anggaran=k.tahun_anggaran
					WHERE s.active=1
						AND s.tahun_anggaran=%d
						AND k.active != 1
					GROUP BY id_sub_skpd
				", $_POST['tahun_anggaran']), ARRAY_A);
				$ret['sub_keg'] = $sub_keg;
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

	public function cek_pemutakhiran_total_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$id_unit = $_POST['id_unit'];
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$id_jadwal_lokal = $_POST['id_jadwal_lokal'];

					$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
						SELECT 
							nama AS nama_jadwal,
							tahun_anggaran AS awal_renstra,
							(tahun_anggaran+lama_pelaksanaan-1) AS akhir_renstra,
							lama_pelaksanaan,
							status 
						FROM `data_jadwal_lokal` 
							WHERE id_jadwal_lokal=%d", $id_jadwal_lokal));

					$_suffix = '';
					$where = '';
					if ($jadwal_lokal->status == 1) {
						$_suffix = '_history';
						$where = 'AND id_jadwal=' . $wpdb->prepare("%d", $id_jadwal_lokal);
					}

					$where_skpd = '';
					if (!empty($id_unit)) {
						if ($id_unit != 'all') {
							$where_skpd = "and id_skpd=" . $wpdb->prepare("%d", $id_unit);
						}
					}

					$sql = $wpdb->prepare("
						SELECT 
							id_skpd, kode_skpd, nama_skpd
						FROM data_unit 
						WHERE tahun_anggaran=%d
							" . $where_skpd . "
							AND is_skpd=1
							AND active=1
						ORDER BY id_skpd ASC
					", $tahun_anggaran);

					$units = $wpdb->get_results($sql, ARRAY_A);

					$data_all = array(
						'data' => array(),
						'pemutakhiran_program' => 0,
						'pemutakhiran_kegiatan' => 0,
						'pemutakhiran_sub_kegiatan' => 0
					);

					foreach ($units as $unit) {

						if (empty($data_all['data'][$unit['id_skpd']])) {
							$data_all['data'][$unit['id_skpd']] = [
								'kode_skpd' => $unit['kode_skpd'],
								'nama_skpd' => $unit['nama_skpd'],
								'pemutakhiran_program' => 0,
								'pemutakhiran_kegiatan' => 0,
								'pemutakhiran_sub_kegiatan' => 0
							];
						}

						$tujuan_all = $wpdb->get_results($wpdb->prepare("
							SELECT 
								DISTINCT id_unik 
							FROM data_renstra_tujuan_lokal" . $_suffix . " 
							WHERE 
								id_unit=%d AND 
								active=1 $where ORDER BY urut_tujuan
						", $unit['id_skpd']), ARRAY_A);

						foreach ($tujuan_all as $keyTujuan => $tujuan_value) {

							$sasaran_all = $wpdb->get_results($wpdb->prepare("
									SELECT 
										DISTINCT id_unik 
									FROM data_renstra_sasaran_lokal" . $_suffix . " 
									WHERE 
										kode_tujuan=%s AND 
										active=1 $where ORDER BY urut_sasaran
								", $tujuan_value['id_unik']), ARRAY_A);

							foreach ($sasaran_all as $keySasaran => $sasaran_value) {

								$program_all = $wpdb->get_results($wpdb->prepare(
									"
									SELECT 
										id_unik, kode_program 
									FROM data_renstra_program_lokal" . $_suffix . " 
									WHERE 
										kode_sasaran=%s AND 
										kode_tujuan=%s AND 
										active=1 AND
										id_unik_indikator IS NULL $where ORDER BY id",
									$sasaran_value['id_unik'],
									$tujuan_value['id_unik']
								), ARRAY_A);

								foreach ($program_all as $keyProgram => $program_value) {

									$programExist = $this->program_exist([
										'kode_program' => $program_value['kode_program'],
										'tahun_anggaran' => $tahun_anggaran
									]);

									if ($programExist['status'] && empty($programExist['count'])) {
										$data_all['data'][$unit['id_skpd']]['pemutakhiran_program']++;
										$data_all['pemutakhiran_program']++;
									}

									$kegiatan_all = $wpdb->get_results($wpdb->prepare(
										"
										SELECT 
											id_unik, kode_giat
										FROM data_renstra_kegiatan_lokal" . $_suffix . "
										WHERE 
											kode_program=%s AND
											kode_sasaran=%s AND
											kode_tujuan=%s AND 
											active=1 AND
											id_unik_indikator IS NULL $where ORDER BY id",
										$program_value['id_unik'],
										$sasaran_value['id_unik'],
										$tujuan_value['id_unik']
									), ARRAY_A);

									foreach ($kegiatan_all as $keyKegiatan => $kegiatan_value) {

										$kegiatanExist = $this->kegiatan_exist([
											'kode_giat' => $kegiatan_value['kode_giat'],
											'tahun_anggaran' => $tahun_anggaran
										]);

										if ($kegiatanExist['status'] && empty($kegiatanExist['count'])) {
											$data_all['data'][$unit['id_skpd']]['pemutakhiran_kegiatan']++;
											$data_all['pemutakhiran_kegiatan']++;
										}

										$sub_kegiatan_all = $wpdb->get_results($wpdb->prepare(
											"
											SELECT 
												kode_sub_giat
											FROM data_renstra_sub_kegiatan_lokal" . $_suffix . " 
											WHERE 
												kode_kegiatan=%s AND 
												kode_program=%s AND 
												kode_sasaran=%s AND 
												kode_tujuan=%s AND 
												active=1 AND
												id_unik_indikator IS NULL $where ORDER BY id",
											$kegiatan_value['id_unik'],
											$program_value['id_unik'],
											$sasaran_value['id_unik'],
											$tujuan_value['id_unik']
										), ARRAY_A);

										foreach ($sub_kegiatan_all as $keySubKegiatan => $sub_kegiatan_value) {

											$subkegiatanExist = $this->subgiat_exist([
												'kode_sub_giat' => $sub_kegiatan_value['kode_sub_giat'],
												'tahun_anggaran' => $tahun_anggaran
											]);

											if ($subkegiatanExist['status'] && empty($subkegiatanExist['count'])) {
												$data_all['data'][$unit['id_skpd']]['pemutakhiran_sub_kegiatan']++;
												$data_all['pemutakhiran_sub_kegiatan']++;
											}
										}
									}
								}
							}
						}
					}

					echo json_encode([
						'status' => true,
						'data' => $data_all
					]);
					exit();
				} else {
					throw new Exception("APIKEY tidak sesuai!", 1);
				}
			} else {
				throw new Exception("Format Salah!", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function get_rekening_akun()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$where = '';
					if ($_POST['kode_akun'] == 'BTL-GAJI') {
						$where = 'AND is_gaji_asn = 1';
					} else if ($_POST['kode_akun'] == 'BARJAS-MODAL') {
						$where = 'AND is_barjas = 1';
					} else if ($_POST['kode_akun'] == 'BUNGA') {
						$where = 'AND is_bunga = 1';
					} else if ($_POST['kode_akun'] == 'SUBSIDI') {
						$where = 'AND is_subsidi = 1';
					} else if ($_POST['kode_akun'] == 'HIBAH-BRG') {
						$where = 'AND is_hibah_brg = 1';
					} else if ($_POST['kode_akun'] == 'HIBAH') {
						$where = 'AND is_hibah_uang = 1';
					} else if ($_POST['kode_akun'] == 'BANSOS-BRG') {
						$where = 'AND is_sosial_brg = 1';
					} else if ($_POST['kode_akun'] == 'BANSOS') {
						$where = 'AND is_sosial_uang = 1';
					} else if ($_POST['kode_akun'] == 'BAGI-HASIL') {
						$where = 'AND is_bagi_hasil = 1';
					} else if ($_POST['kode_akun'] == 'BANKEU') {
						$where = 'AND is_bankeu_umum = 1';
					} else if ($_POST['kode_akun'] == 'BANKEU-KHUSUS') {
						$where = 'AND is_bankeu_khusus = 1';
					} else if ($_POST['kode_akun'] == 'BTT') {
						$where = 'AND is_btt = 1';
					} else if ($_POST['kode_akun'] == 'BOS') {
						$where = 'AND is_bos = 1';
					} else if ($_POST['kode_akun'] == 'BLUD') {
						$where = 'AND is_bl = 1';
					} else if ($_POST['kode_akun'] == 'BLUD') {
						$where = 'AND is_modal_tanah = 1';
					}

					$data = $wpdb->get_results($wpdb->prepare("
						SELECT 
							id_akun,
							kode_akun,
							nama_akun
						FROM data_akun 
						WHERE tahun_anggaran=%d 
							AND set_input=1
							AND active=1
							" . $where . "
					", $_POST['tahun_anggaran']), ARRAY_A);

					echo json_encode([
						'status' => true,
						'items' => $data
					]);
					exit();
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function get_jenis_standar_harga()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_jenis_standar_harga ORDER BY id"), ARRAY_A);

					echo json_encode([
						'status' => true,
						'items' => $data
					]);
					exit();
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function mutakhirkan_sub_kegiatan_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->query('START TRANSACTION');

					$subKegiatanRenstraLama = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sub_kegiatan_lokal
						WHERE id=%d 
							AND active=1 
						order by id ASC
					", $_POST['id']));

					if (empty($subKegiatanRenstraLama)) {
						throw new Exception('Sub kegiatan existing tidak ditemukan!');
					}

					$indikatorSubKegiatanRenstraLama = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sub_kegiatan_lokal
						WHERE id_unik=%s 
							AND id_unik_indikator IS NOT NULL
							AND active=1 
						order by id ASC
					", $subKegiatanRenstraLama->id_unik));

					$subKegiatanBaru = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_prog_keg 
						WHERE id_sub_giat=%d 
							AND tahun_anggaran=%d 
							AND active=1 
						order by id ASC
					", $_POST['id_sub_kegiatan'], $_POST['tahun_anggaran']));

					if (empty($subKegiatanBaru)) {
						throw new Exception('Sub kegiatan pemutakhiran tidak ditemukan di data master tahun anggaran ' . $_POST['tahun_anggaran'] . '!');
					}

					$indikatorSubKegiatanBaru = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_master_indikator_subgiat 
						WHERE id=%d 
							AND tahun_anggaran=%d 
							AND active=1 
						order by id ASC
					", $_POST['id_indikator_sub_kegiatan'], $_POST['tahun_anggaran']));

					if (empty($indikatorSubKegiatanBaru)) {
						throw new Exception('Indikator sub kegiatan pemutakhiran tidak ditemukan di data master tahun anggaran ' . $_POST['tahun_anggaran'] . '!');
					}

					// insert sub keg baru
					$result1 = $wpdb->insert('data_renstra_sub_kegiatan_lokal', [
						'bidur_lock' => $subKegiatanRenstraLama->bidur_lock,
						'giat_lock' => $subKegiatanRenstraLama->giat_lock,
						'id_bidang_urusan' => $subKegiatanBaru->id_bidang_urusan,
						'id_sub_giat' => $subKegiatanBaru->id_sub_giat,
						'id_giat' => $subKegiatanBaru->id_giat,
						'id_misi' => $subKegiatanRenstraLama->id_misi,
						'id_program' => $subKegiatanBaru->id_program,
						'id_unik' => $this->generateRandomString(), // kode_sub_kegiatan
						'id_unit' => $subKegiatanRenstraLama->id_unit,
						'tahun_anggaran' => $subKegiatanRenstraLama->tahun_anggaran,
						'id_sub_unit' => $subKegiatanRenstraLama->id_sub_unit,
						'id_visi' => $subKegiatanRenstraLama->id_visi,
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'kode_bidang_urusan' => $subKegiatanBaru->kode_bidang_urusan,
						'kode_sub_giat' => $subKegiatanBaru->kode_sub_giat,
						'kode_giat' => $subKegiatanBaru->kode_giat,
						'kode_kegiatan' => $subKegiatanRenstraLama->kode_kegiatan,
						'kode_program' => $subKegiatanRenstraLama->kode_program,
						'kode_sasaran' => $subKegiatanRenstraLama->kode_sasaran,
						'kode_skpd' => $subKegiatanRenstraLama->kode_skpd,
						'kode_tujuan' => $subKegiatanRenstraLama->kode_tujuan,
						'nama_bidang_urusan' => $subKegiatanBaru->nama_bidang_urusan,
						'nama_sub_giat' => $subKegiatanBaru->nama_sub_giat,
						'nama_giat' => $subKegiatanBaru->nama_giat,
						'nama_program' => $subKegiatanBaru->nama_program,
						'nama_skpd' => $subKegiatanRenstraLama->nama_skpd,
						'pagu_1' => $subKegiatanRenstraLama->pagu_1,
						'pagu_2' => $subKegiatanRenstraLama->pagu_2,
						'pagu_3' => $subKegiatanRenstraLama->pagu_3,
						'pagu_4' => $subKegiatanRenstraLama->pagu_4,
						'pagu_5' => $subKegiatanRenstraLama->pagu_5,
						'pagu_1_usulan' => $subKegiatanRenstraLama->pagu_1_usulan,
						'pagu_2_usulan' => $subKegiatanRenstraLama->pagu_2_usulan,
						'pagu_3_usulan' => $subKegiatanRenstraLama->pagu_3_usulan,
						'pagu_4_usulan' => $subKegiatanRenstraLama->pagu_4_usulan,
						'pagu_5_usulan' => $subKegiatanRenstraLama->pagu_5_usulan,
						'nama_sub_unit' => $subKegiatanRenstraLama->nama_sub_unit,
						'program_lock' => $subKegiatanRenstraLama->program_lock,
						'renstra_prog_lock' => $subKegiatanRenstraLama->renstra_prog_lock,
						'sasaran_lock' => $subKegiatanRenstraLama->sasaran_lock,
						'sasaran_teks' => $subKegiatanRenstraLama->sasaran_teks,
						'status' => 1,
						'tujuan_lock' => $subKegiatanRenstraLama->tujuan_lock,
						'tujuan_teks' => $subKegiatanRenstraLama->tujuan_teks,
						'urut_sasaran' => $subKegiatanRenstraLama->urut_sasaran,
						'urut_tujuan' => $subKegiatanRenstraLama->urut_tujuan,
						'active' => 1,
						'id_sub_giat_lama' => $_POST['id_sub_kegiatan_lama']
					]);

					$subKegiatanRenstraBaru = $wpdb->get_row($wpdb->prepare("
						SELECT 
							* 
						FROM data_renstra_sub_kegiatan_lokal
						WHERE id_sub_giat=%d 
							AND active=1
							AND id_unik_indikator IS NULL 
						order by id ASC
					", $_POST['id_sub_kegiatan']));

					if (empty($subKegiatanRenstraBaru)) {
						throw new Exception('Sub kegiatan pemutakhiran tidak ditemukan!');
					}

					$result2 = 1;
					// $result2 dikasih 1 karena tidak semua sub keg lama ada indikator
					if (!empty($indikatorSubKegiatanRenstraLama)) {
						// insert indikator sub keg baru
						$result2 = $wpdb->insert('data_renstra_sub_kegiatan_lokal', [
							'bidur_lock' => $subKegiatanRenstraBaru->bidur_lock,
							'giat_lock' => $subKegiatanRenstraBaru->giat_lock,
							'id_bidang_urusan' => $subKegiatanRenstraBaru->id_bidang_urusan,
							'id_sub_giat' => $subKegiatanRenstraBaru->id_sub_giat,
							'id_giat' => $subKegiatanRenstraBaru->id_giat,
							'id_misi' => $subKegiatanRenstraBaru->id_misi,
							'id_program' => $subKegiatanRenstraBaru->id_program,
							'id_unik' => $subKegiatanRenstraBaru->id_unik,
							'id_unik_indikator' => $this->generateRandomString(),
							'id_unit' => $subKegiatanRenstraBaru->id_unit,
							'tahun_anggaran' => $subKegiatanRenstraBaru->tahun_anggaran,
							'id_sub_unit' => $subKegiatanRenstraBaru->id_sub_unit,
							'id_visi' => $subKegiatanRenstraBaru->id_visi,
							'is_locked' => $subKegiatanRenstraBaru->is_locked,
							'is_locked_indikator' => 0,
							'kode_bidang_urusan' => $subKegiatanRenstraBaru->kode_bidang_urusan,
							'kode_sub_giat' => $subKegiatanRenstraBaru->kode_sub_giat,
							'kode_giat' => $subKegiatanRenstraBaru->kode_giat,
							'kode_kegiatan' => $subKegiatanRenstraBaru->kode_kegiatan,
							'kode_program' => $subKegiatanRenstraBaru->kode_program,
							'kode_sasaran' => $subKegiatanRenstraBaru->kode_sasaran,
							'kode_skpd' => $subKegiatanRenstraBaru->kode_skpd,
							'kode_tujuan' => $subKegiatanRenstraBaru->kode_tujuan,
							'nama_bidang_urusan' => $subKegiatanRenstraBaru->nama_bidang_urusan,
							'nama_sub_giat' => $subKegiatanRenstraBaru->nama_sub_giat,
							'nama_giat' => $subKegiatanRenstraBaru->nama_giat,
							'nama_program' => $subKegiatanRenstraBaru->nama_program,
							'nama_skpd' => $subKegiatanRenstraBaru->nama_skpd,
							'nama_sub_unit' => $subKegiatanRenstraBaru->nama_sub_unit,
							'program_lock' => $subKegiatanRenstraBaru->program_lock,
							'renstra_prog_lock' => $subKegiatanRenstraBaru->renstra_prog_lock,
							'sasaran_lock' => $subKegiatanRenstraBaru->sasaran_lock,
							'sasaran_teks' => $subKegiatanRenstraBaru->sasaran_teks,
							'id_indikator' => $indikatorSubKegiatanBaru->id,
							'indikator' => $indikatorSubKegiatanBaru->indikator,
							'satuan' => $indikatorSubKegiatanBaru->satuan,
							'id_indikator_usulan' => $indikatorSubKegiatanBaru->id,
							'indikator_usulan' => $indikatorSubKegiatanBaru->indikator,
							'satuan_usulan' => $indikatorSubKegiatanBaru->satuan,
							'target_1' => $indikatorSubKegiatanRenstraLama->target_1,
							'target_2' => $indikatorSubKegiatanRenstraLama->target_2,
							'target_3' => $indikatorSubKegiatanRenstraLama->target_3,
							'target_4' => $indikatorSubKegiatanRenstraLama->target_4,
							'target_5' => $indikatorSubKegiatanRenstraLama->target_5,
							'target_1_usulan' => $indikatorSubKegiatanRenstraLama->target_1_usulan,
							'target_2_usulan' => $indikatorSubKegiatanRenstraLama->target_2_usulan,
							'target_3_usulan' => $indikatorSubKegiatanRenstraLama->target_3_usulan,
							'target_4_usulan' => $indikatorSubKegiatanRenstraLama->target_4_usulan,
							'target_5_usulan' => $indikatorSubKegiatanRenstraLama->target_5_usulan,
							'target_awal' => $indikatorSubKegiatanRenstraLama->target_awal,
							'target_akhir' => $indikatorSubKegiatanRenstraLama->target_akhir,
							'target_awal_usulan' => $indikatorSubKegiatanRenstraLama->target_awal_usulan,
							'target_akhir_usulan' => $indikatorSubKegiatanRenstraLama->target_akhir_usulan,
							'catatan' => $indikatorSubKegiatanRenstraLama->catatan,
							'catatan_usulan' => $indikatorSubKegiatanRenstraLama->catatan_usulan,
							'status' => 1,
							'tujuan_lock' => $subKegiatanRenstraBaru->tujuan_lock,
							'tujuan_teks' => $subKegiatanRenstraBaru->tujuan_teks,
							'urut_sasaran' => $subKegiatanRenstraBaru->urut_sasaran,
							'urut_tujuan' => $subKegiatanRenstraBaru->urut_tujuan,
							'active' => 1,
							'id_sub_giat_lama' => $_POST['id_sub_kegiatan_lama']
						]);
					}
					$result2_sql = $wpdb->last_query;

					// non aktifkan sub kegiatan lama dan indikatornya
					$result3 = $wpdb->query($wpdb->prepare("
						UPDATE data_renstra_sub_kegiatan_lokal 
						SET 
							active=0,
							status=0,
							update_at=%s
						WHERE id_unik=%s AND 
							active=%d
					", date('Y-m-d H:i:s'), $subKegiatanRenstraLama->id_unik, 1));

					if ($result1 && $result2 && $result3) {
						$wpdb->query('COMMIT');
						echo json_encode([
							'status' => true,
							'message' => 'Sukses mutakhirkan sub kegiatan!'
						]);
						exit();
					} else {
						$last_query = $wpdb->last_query;
						$wpdb->query('ROLLBACK');
						echo json_encode([
							'status' => false,
							'message' => 'Gagal mutakhirkan sub kegiatan. Hubungi admin!',
							'sql' => $last_query,
							'result1' => $result1,
							'result2' => $result2,
							'result2_sql' => $result2_sql,
							'result3' => $result3,
							'error' => $wpdb->last_error
						]);
						exit();
					}
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function mutakhirkan_program_renstra()
	{
		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->query('START TRANSACTION');

					$programRenstraLama = $wpdb->get_row($wpdb->prepare("
											SELECT 
												* 
											FROM data_renstra_program_lokal
											WHERE id=%d 
												AND active=1 
											order by id ASC
										", $_POST['id']));

					if (empty($programRenstraLama)) {
						throw new Exception('Program lama tidak ditemukan!');
					}

					$programBaru = $wpdb->get_row($wpdb->prepare("
											SELECT 
												* 
											FROM data_prog_keg 
											WHERE id_program=%d 
												AND tahun_anggaran=%d 
												AND active=1 
											order by id ASC
										", $_POST['id_program'], $_POST['tahun_anggaran']));

					if (empty($programBaru)) {
						throw new Exception('Program baru tidak ditemukan di data master tahun anggaran ' . $_POST['tahun_anggaran'] . '!');
					}

					// insert program baru
					$result1 = $wpdb->insert('data_renstra_program_lokal', [
						'bidur_lock' => 0,
						'id_bidang_urusan' => $programRenstraLama->id_bidang_urusan,
						'id_misi' => $programRenstraLama->id_misi,
						'tahun_anggaran' => $programRenstraLama->tahun_anggaran,
						'id_program' => $programBaru->id_program,
						'id_unik' => $this->generateRandomString(),
						'id_unit' => $programRenstraLama->id_unit,
						'id_visi' => $programRenstraLama->id_visi,
						'is_locked' => 0,
						'is_locked_indikator' => 0,
						'kode_bidang_urusan' => $programRenstraLama->kode_bidang_urusan,
						'kode_program' => $programBaru->kode_program,
						'kode_sasaran' => $programRenstraLama->kode_sasaran,
						'kode_skpd' => $programRenstraLama->kode_skpd,
						'kode_tujuan' => $programRenstraLama->kode_tujuan,
						'nama_bidang_urusan' => $programRenstraLama->nama_bidang_urusan,
						'nama_program' => $programBaru->nama_program,
						'nama_skpd' => $programRenstraLama->nama_skpd,
						'program_lock' => 0,
						'sasaran_lock' => $programRenstraLama->is_locked,
						'sasaran_teks' => $programRenstraLama->sasaran_teks,
						'status' => 1,
						'tujuan_lock' => $programRenstraLama->tujuan_lock,
						'tujuan_teks' => $programRenstraLama->tujuan_teks,
						'urut_sasaran' => $programRenstraLama->urut_sasaran,
						'urut_tujuan' => $programRenstraLama->urut_tujuan,
						'catatan_usulan' => $programRenstraLama->catatan_usulan,
						'catatan' => $programRenstraLama->catatan,
						'active' => 1,
						'id_program_lama' => $_POST['id_program_lama']
					]);

					$programRenstraBaru = $wpdb->get_row($wpdb->prepare("
											SELECT 
												* 
											FROM data_renstra_program_lokal
											WHERE id_program=%d 
												AND active=1 
											order by id ASC
										", $_POST['id_program']));

					if (empty($programRenstraBaru)) {
						throw new Exception('Program baru tidak ditemukan!');
					}

					$indikatorprogramRenstraLama = $wpdb->get_results($wpdb->prepare("
											SELECT 
												* 
											FROM data_renstra_program_lokal
											WHERE id_unik=%s 
												AND id_unik_indikator IS NOT NULL
												AND active=1 
											order by id ASC
										", $_POST['id_unik']));

					if (!empty($indikatorprogramRenstraLama)) {
						// insert indikator program baru
						$arrStatus = [];
						foreach ($indikatorprogramRenstraLama as $key => $indikatorProgram) {
							$arrStatus[] = $wpdb->insert('data_renstra_program_lokal', [
								'bidur_lock' => $programRenstraBaru->bidur_lock,
								'id_bidang_urusan' => $programRenstraBaru->id_bidang_urusan,
								'id_misi' => $programRenstraBaru->id_misi,
								'id_program' => $programRenstraBaru->id_program,
								'id_unik' => $programRenstraBaru->id_unik,
								'id_unik_indikator' => $this->generateRandomString(),
								'id_unit' => $programRenstraBaru->id_unit,
								'tahun_anggaran' => $programRenstraBaru->tahun_anggaran,
								'id_visi' => $programRenstraBaru->id_visi,
								'is_locked' => 0,
								'is_locked_indikator' => 0,
								'kode_bidang_urusan' => $programRenstraBaru->kode_bidang_urusan,
								'kode_program' => $programRenstraBaru->kode_program,
								'kode_sasaran' => $programRenstraBaru->kode_sasaran,
								'kode_skpd' => $programRenstraBaru->kode_skpd,
								'kode_tujuan' => $programRenstraBaru->kode_tujuan,
								'nama_bidang_urusan' => $programRenstraBaru->nama_bidang_urusan,
								'nama_program' => $programRenstraBaru->nama_program,
								'nama_skpd' => $programRenstraBaru->nama_skpd,
								'tahun_anggaran' => $programRenstraBaru->tahun_anggaran,

								'indikator' => $indikatorProgram->indikator,
								'satuan' => $indikatorProgram->satuan,
								'pagu_1' => $indikatorProgram->pagu_1,
								'pagu_2' => $indikatorProgram->pagu_2,
								'pagu_3' => $indikatorProgram->pagu_3,
								'pagu_4' => $indikatorProgram->pagu_4,
								'pagu_5' => $indikatorProgram->pagu_5,
								'target_1' => $indikatorProgram->target_1,
								'target_2' => $indikatorProgram->target_2,
								'target_3' => $indikatorProgram->target_3,
								'target_4' => $indikatorProgram->target_4,
								'target_5' => $indikatorProgram->target_5,
								'target_akhir' => $indikatorProgram->target_akhir,
								'target_awal' => $indikatorProgram->target_awal,

								'indikator_usulan' => $indikatorProgram->indikator_usulan,
								'satuan_usulan' => $indikatorProgram->satuan_usulan,
								'pagu_1_usulan' => $indikatorProgram->pagu_1_usulan,
								'pagu_2_usulan' => $indikatorProgram->pagu_2_usulan,
								'pagu_3_usulan' => $indikatorProgram->pagu_3_usulan,
								'pagu_4_usulan' => $indikatorProgram->pagu_4_usulan,
								'pagu_5_usulan' => $indikatorProgram->pagu_5_usulan,
								'target_1_usulan' => $indikatorProgram->target_1_usulan,
								'target_2_usulan' => $indikatorProgram->target_2_usulan,
								'target_3_usulan' => $indikatorProgram->target_3_usulan,
								'target_4_usulan' => $indikatorProgram->target_4_usulan,
								'target_5_usulan' => $indikatorProgram->target_5_usulan,
								'target_akhir_usulan' => $indikatorProgram->target_akhir_usulan,
								'target_awal_usulan' => $indikatorProgram->target_awal_usulan,

								'program_lock' => 0,
								'sasaran_lock' => $programRenstraBaru->sasaran_lock,
								'sasaran_teks' => $programRenstraBaru->sasaran_teks,
								'status' => 1,
								'tujuan_lock' => $programRenstraBaru->tujuan_lock,
								'tujuan_teks' => $programRenstraBaru->tujuan_teks,
								'urut_sasaran' => $programRenstraBaru->urut_sasaran,
								'urut_tujuan' => $programRenstraBaru->urut_tujuan,
								'active' => 1,
								'catatan_usulan' => $indikatorProgram->catatan_usulan,
								'catatan' => $indikatorProgram->catatan,
								'id_program_lama' => $_POST['id_program_lama']
							]);
						}
					}

					// cek kegiatan
					$checkKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_kegiatan_lokal 
						WHERE 
							kode_program=%s AND 
							active=%d", $programRenstraLama->id_unik, 1));

					if (!empty($checkKegiatan)) {

						// update kegiatan sesuai program yang dipilih
						$result2 = $wpdb->query($wpdb->prepare("
							UPDATE data_renstra_kegiatan_lokal 
								SET 
									kode_program='" . $programRenstraBaru->id_unik . "',
									id_program=" . $programRenstraBaru->id_program . ",
									nama_program='" . $programRenstraBaru->nama_program . "',
									update_at='" . date('Y-m-d H:i:s') . "'
							WHERE 
								kode_program=%s AND 
								active=%d", $programRenstraLama->id_unik, 1));
					}

					// cek sub kegiatan
					$checkSubKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sub_kegiatan_lokal 
						WHERE 
							kode_program=%s AND 
							active=%d", $programRenstraLama->id_unik, 1));

					if (!empty($checkSubKegiatan)) {

						// update sub kegiatan sesuai program yang dipilih
						$result3 = $wpdb->query($wpdb->prepare("
							UPDATE data_renstra_sub_kegiatan_lokal 
								SET 
									kode_program='" . $programRenstraBaru->id_unik . "',
									id_program=" . $programRenstraBaru->id_program . ",
									nama_program='" . $programRenstraBaru->nama_program . "',
									update_at='" . date('Y-m-d H:i:s') . "'
							WHERE 
								kode_program=%s AND 
								active=%d", $programRenstraLama->id_unik, 1));
					}

					// non aktifkan program lama dan indikatornya
					$result4 = $wpdb->query($wpdb->prepare("
						UPDATE data_renstra_program_lokal 
							SET 
								active=0,
								status=0,
								update_at='" . date('Y-m-d H:i:s') . "'
						WHERE 
							id_unik=%s AND 
							active=%d", $programRenstraLama->id_unik, 1));

					if (!empty($checkKegiatan) && !empty($checkSubKegiatan)) {
						if ($result1 && $result2 && $result3 && $result4) {
							if (!empty($indikatorprogramRenstraLama)) {
								$res = array_unique($arrStatus);
								if (count($res) === 1 && $res[0]) {
									$wpdb->query('COMMIT');
								} else {
									$wpdb->query('ROLLBACK');
									throw new Exception("Oops terjadi kesalahan mengambil indikator program lama!", 1);
								}
							} else {
								$wpdb->query('COMMIT');
							}
						} else {
							$wpdb->query('ROLLBACK');
							throw new Exception("Oops te
								rjadi kesalahan pemutakhiran program baru, cek kegiatan dan sub kegiatan!", 1);
						}
					}

					if (!empty($checkKegiatan) && empty($checkSubKegiatan)) {
						if ($result1 && $result2 && $result4) {
							if (!empty($indikatorprogramRenstraLama)) {
								$res = array_unique($arrStatus);
								if (count($res) === 1 && $res[0]) {
									$wpdb->query('COMMIT');
								} else {
									$wpdb->query('ROLLBACK');
									throw new Exception("Oops terjadi kesalahan mengambil indikator program lama!", 1);
								}
							} else {
								$wpdb->query('COMMIT');
							}
						} else {
							$wpdb->query('ROLLBACK');
							throw new Exception("Oops terjadi kesalahan pemutakhiran program baru, cek kegiatan", 1);
						}
					}

					if (empty($checkKegiatan) && empty($checkSubKegiatan)) {
						if ($result1 && $result4) {
							if (!empty($indikatorprogramRenstraLama)) {
								$res = array_unique($arrStatus);
								if (count($res) === 1 && $res[0]) {
									$wpdb->query('COMMIT');
								} else {
									$wpdb->query('ROLLBACK');
									throw new Exception("Oops terjadi kesalahan mengambil indikator program lama!", 1);
								}
							} else {
								$wpdb->query('COMMIT');
							}
						} else {
							$wpdb->query('ROLLBACK');
							throw new Exception("Oops terjadi kesalahan pemutakhiran program baru", 1);
						}
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses mutakhirkan program!'
					]);
					exit();
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function listKegiatanByProgram()
	{
		global $wpdb;

		try {

			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					$tahun_anggaran = get_option('_crb_tahun_anggaran_sipd');
					$kode_program = $wpdb->get_var($wpdb->prepare("
						SELECT 
							kode_program 
						FROM data_prog_keg
						WHERE id_program=%d", $_POST['id_program']));

					$where = '';
					$cek_pemda = $this->cek_kab_kot();
					// tahun 2024 sudah menggunakan sipd-ri

					if (
						$cek_pemda['status'] == 1
						&& $tahun_anggaran >= 2024
					) {
						$where .= ' AND set_kab_kota=1';
					} else if ($cek_pemda['status'] == 2) {
						$where .= ' AND set_prov=1';
					}

					$sql = $wpdb->prepare("
							SELECT 
								* 
							FROM data_prog_keg
							WHERE kode_program=%s
								AND tahun_anggaran=%d
								AND active=%d
								$where
						", $kode_program, $tahun_anggaran, 1);
					$data = $wpdb->get_results($sql, ARRAY_A);

					$kegiatan = [];
					foreach ($data as $key => $value) {
						if (empty($kegiatan[$value['kode_giat']])) {
							$kegiatan[$value['kode_giat']] = [
								'id' => $value['id_giat'],
								'kegiatan_teks' => $value['nama_giat']
							];
						}
					}

					echo json_encode([
						'status' => true,
						'data' => array_values($kegiatan)
					]);
					exit();
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function mutakhirkan_kegiatan_renstra()
	{

		global $wpdb;

		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->query('START TRANSACTION');

					$kegiatanRenstraLama = $wpdb->get_row($wpdb->prepare("
											SELECT 
												* 
											FROM data_renstra_kegiatan_lokal
											WHERE id=%d 
												AND active=1 
											order by id ASC
										", $_POST['id']));

					if (empty($kegiatanRenstraLama)) {
						throw new Exception('Kegiatan lama tidak ditemukan!');
					}

					$kegiatanBaru = $wpdb->get_row($wpdb->prepare("
											SELECT 
												* 
											FROM data_prog_keg 
											WHERE id_giat=%d 
												AND tahun_anggaran=%d 
												AND active=1 
											order by id ASC
										", $_POST['id_giat'], $_POST['tahun_anggaran']));

					if (empty($kegiatanBaru)) {
						throw new Exception('Kegiatan baru tidak ditemukan di data master tahun anggaran ' . $_POST['tahun_anggaran'] . '!');
					}

					$result1 = $wpdb->insert('data_renstra_kegiatan_lokal', [
						'bidur_lock' => $kegiatanRenstraLama->bidur_lock,
						'giat_lock' => $kegiatanRenstraLama->giat_lock,
						'id_bidang_urusan' => $kegiatanBaru->id_bidang_urusan,
						'id_giat' => $kegiatanBaru->id_giat,
						'id_misi' => $kegiatanRenstraLama->id_misi,
						'id_program' => $kegiatanBaru->id_program,
						'id_unik' => $this->generateRandomString(), // kode_kegiatan
						'id_unit' => $kegiatanRenstraLama->id_unit,
						'tahun_anggaran' => $kegiatanRenstraLama->tahun_anggaran,
						'id_visi' => $kegiatanRenstraLama->id_visi,
						'is_locked' => $kegiatanRenstraLama->is_locked,
						'is_locked_indikator' => $kegiatanRenstraLama->is_locked_indikator,
						'kode_bidang_urusan' => $kegiatanBaru->kode_bidang_urusan,
						'kode_giat' => $kegiatanBaru->kode_giat,
						'kode_program' => $kegiatanRenstraLama->kode_program,
						'kode_sasaran' => $kegiatanRenstraLama->kode_sasaran,
						'kode_skpd' => $kegiatanRenstraLama->kode_skpd,
						'kode_tujuan' => $kegiatanRenstraLama->kode_tujuan,
						'nama_bidang_urusan' => $kegiatanBaru->nama_bidang_urusan,
						'nama_giat' => $kegiatanBaru->nama_giat,
						'nama_program' => $kegiatanBaru->nama_program,
						'nama_skpd' => $kegiatanRenstraLama->nama_skpd,
						'program_lock' => $kegiatanRenstraLama->program_lock,
						'renstra_prog_lock' => $kegiatanRenstraLama->program_lock,
						'sasaran_lock' => $kegiatanRenstraLama->sasaran_lock,
						'sasaran_teks' => $kegiatanRenstraLama->sasaran_teks,
						'status' => $kegiatanRenstraLama->status,
						'tujuan_lock' => $kegiatanRenstraLama->tujuan_lock,
						'tujuan_teks' => $kegiatanRenstraLama->tujuan_teks,
						'urut_sasaran' => $kegiatanRenstraLama->urut_sasaran,
						'urut_tujuan' => $kegiatanRenstraLama->urut_tujuan,
						'catatan_usulan' => $kegiatanRenstraLama->catatan_usulan,
						'catatan' => $kegiatanRenstraLama->catatan,
						'active' => $kegiatanRenstraLama->active,
						'id_giat_lama' => $_POST['id_giat_lama']
					]);

					$kegiatanRenstraBaru = $wpdb->get_row($wpdb->prepare("
											SELECT 
												* 
											FROM data_renstra_kegiatan_lokal
											WHERE id_giat=%d 
												AND active=1 
											order by id ASC
										", $_POST['id_giat']));

					if (empty($kegiatanRenstraBaru)) {
						throw new Exception('Kegiatan baru tidak ditemukan!');
					}

					$indikatorKegiatanRenstraLama = $wpdb->get_results($wpdb->prepare("
											SELECT 
												* 
											FROM data_renstra_kegiatan_lokal
											WHERE id_unik=%s 
												AND id_unik_indikator IS NOT NULL
												AND active=1 
											order by id ASC
										", $_POST['id_unik']));

					if (!empty($indikatorKegiatanRenstraLama)) {

						// insert indikator kegiatan baru
						$arrStatus = [];
						foreach ($indikatorKegiatanRenstraLama as $key => $indikatorKegiatan) {
							$arrStatus[] = $wpdb->insert('data_renstra_kegiatan_lokal', [
								'bidur_lock' => $indikatorKegiatan->bidur_lock,
								'giat_lock' => $indikatorKegiatan->giat_lock,
								'id_bidang_urusan' => $kegiatanRenstraBaru->id_bidang_urusan,
								'id_giat' => $kegiatanRenstraBaru->id_giat,
								'id_misi' => $kegiatanRenstraLama->id_misi,
								'id_program' => $kegiatanRenstraBaru->id_program,
								'id_unik' => $kegiatanRenstraBaru->id_unik,
								'id_unik_indikator' => $this->generateRandomString(),
								'id_unit' => $kegiatanRenstraLama->id_unit,
								'tahun_anggaran' => $kegiatanRenstraLama->tahun_anggaran,
								'id_visi' => $kegiatanRenstraLama->id_visi,
								'is_locked' => $kegiatanRenstraLama->is_locked,
								'is_locked_indikator' => $kegiatanRenstraLama->is_locked_indikator,
								'kode_bidang_urusan' => $kegiatanRenstraBaru->kode_bidang_urusan,
								'kode_giat' => $kegiatanRenstraBaru->kode_giat,
								'kode_program' => $kegiatanRenstraLama->kode_program,
								'kode_sasaran' => $kegiatanRenstraLama->kode_sasaran,
								'kode_skpd' => $kegiatanRenstraLama->kode_skpd,
								'kode_tujuan' => $kegiatanRenstraLama->kode_tujuan,
								'nama_bidang_urusan' => $kegiatanRenstraBaru->nama_bidang_urusan,
								'nama_giat' => $kegiatanRenstraBaru->nama_giat,
								'nama_program' => $kegiatanRenstraBaru->nama_program,
								'nama_skpd' => $kegiatanRenstraLama->nama_skpd,

								'indikator' => $indikatorKegiatan->indikator,
								'satuan' => $indikatorKegiatan->satuan,
								'pagu_1' => $indikatorKegiatan->pagu_1,
								'pagu_2' => $indikatorKegiatan->pagu_2,
								'pagu_3' => $indikatorKegiatan->pagu_3,
								'pagu_4' => $indikatorKegiatan->pagu_4,
								'pagu_5' => $indikatorKegiatan->pagu_5,
								'target_1' => $indikatorKegiatan->target_1,
								'target_2' => $indikatorKegiatan->target_2,
								'target_3' => $indikatorKegiatan->target_3,
								'target_4' => $indikatorKegiatan->target_4,
								'target_5' => $indikatorKegiatan->target_5,
								'target_akhir' => $indikatorKegiatan->target_akhir,
								'target_awal' => $indikatorKegiatan->target_awal,

								'indikator_usulan' => $indikatorKegiatan->indikator_usulan,
								'satuan_usulan' => $indikatorKegiatan->satuan_usulan,
								'pagu_1_usulan' => $indikatorKegiatan->pagu_1_usulan,
								'pagu_2_usulan' => $indikatorKegiatan->pagu_2_usulan,
								'pagu_3_usulan' => $indikatorKegiatan->pagu_3_usulan,
								'pagu_4_usulan' => $indikatorKegiatan->pagu_4_usulan,
								'pagu_5_usulan' => $indikatorKegiatan->pagu_5_usulan,
								'target_1_usulan' => $indikatorKegiatan->target_1_usulan,
								'target_2_usulan' => $indikatorKegiatan->target_2_usulan,
								'target_3_usulan' => $indikatorKegiatan->target_3_usulan,
								'target_4_usulan' => $indikatorKegiatan->target_4_usulan,
								'target_5_usulan' => $indikatorKegiatan->target_5_usulan,
								'target_akhir_usulan' => $indikatorKegiatan->target_akhir_usulan,
								'target_awal_usulan' => $indikatorKegiatan->target_awal_usulan,

								'program_lock' => $kegiatanRenstraLama->program_lock,
								'renstra_prog_lock' => $kegiatanRenstraLama->program_lock,
								'sasaran_lock' => $kegiatanRenstraLama->sasaran_lock,
								'sasaran_teks' => $kegiatanRenstraLama->sasaran_teks,
								'status' => $kegiatanRenstraLama->status,
								'tujuan_lock' => $kegiatanRenstraLama->tujuan_lock,
								'tujuan_teks' => $kegiatanRenstraLama->tujuan_teks,
								'urut_sasaran' => $kegiatanRenstraLama->urut_sasaran,
								'urut_tujuan' => $kegiatanRenstraLama->urut_tujuan,
								'active' => $kegiatanRenstraLama->active,
								'id_giat_lama' => $_POST['id_giat_lama']
							]);
						}
					}

					// cek sub kegiatan
					$checkSubKegiatan = $wpdb->get_row($wpdb->prepare("
						SELECT 
							id 
						FROM data_renstra_sub_kegiatan_lokal 
						WHERE 
							kode_kegiatan=%s AND 
							active=%d", $kegiatanRenstraLama->id_unik, 1));

					if (!empty($checkSubKegiatan)) {

						// update sub kegiatan sesuai kegiatan yang dipilih
						$result2 = $wpdb->query($wpdb->prepare("
							UPDATE data_renstra_sub_kegiatan_lokal 
								SET 
									kode_kegiatan='" . $kegiatanRenstraBaru->id_unik . "',
									id_giat=" . $kegiatanRenstraBaru->id_giat . ",
									nama_giat='" . $kegiatanRenstraBaru->nama_giat . "',
									update_at='" . date('Y-m-d H:i:s') . "'
							WHERE 
								kode_kegiatan=%s AND 
								active=%d", $kegiatanRenstraLama->id_unik, 1));
					}

					// non aktifkan kegiatan lama dan indikatornya
					$result3 = $wpdb->query($wpdb->prepare("
						UPDATE data_renstra_kegiatan_lokal 
							SET 
								active=0,
								status=0,
								update_at='" . date('Y-m-d H:i:s') . "'
						WHERE 
							id_unik=%s AND 
							active=%d", $kegiatanRenstraLama->id_unik, 1));

					if (!empty($checkSubKegiatan)) {
						if ($result1 && $result2 && $result3) {
							if (!empty($indikatorKegiatanRenstraLama)) {
								$res = array_unique($arrStatus);
								if (count($res) === 1 && $res[0]) {
									$wpdb->query('COMMIT');
								} else {
									$wpdb->query('ROLLBACK');
									throw new Exception("Oops terjadi kesalahan mengambil indikator kegiatan lama!", 1);
								}
							} else {
								$wpdb->query('COMMIT');
							}
						} else {
							$wpdb->query('ROLLBACK');
							throw new Exception("Oops terjadi kesalahan pemutakhiran kegiatan baru, cek sub kegiatan!", 1);
						}
					} else {
						if ($result1 && $result3) {
							if (!empty($indikatorKegiatanRenstraLama)) {
								$res = array_unique($arrStatus);
								if (count($res) === 1 && $res[0]) {
									$wpdb->query('COMMIT');
								} else {
									$wpdb->query('ROLLBACK');
									throw new Exception("Oops terjadi kesalahan mengambil indikator kegiatan lama!", 1);
								}
							} else {
								$wpdb->query('COMMIT');
							}
						} else {
							$wpdb->query('ROLLBACK');
							throw new Exception("Oops terjadi kesalahan pemutakhiran kegiatan baru, cek sub kegiatan!", 1);
						}
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses mutakhirkan kegiatan!'
					]);
					exit();
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function mutakhirkan_lintas_sub_kegiatan_renstra()
	{
		global $wpdb;

		try {

			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$wpdb->query('START TRANSACTION');

					$data = json_decode(stripslashes($_POST['data']), true);

					if (empty($data['id_program']) || !isset($data['id_program'])) {
						throw new Exception('Program wajib dipilih!');
					}

					if (empty($data['kegiatan']) || !isset($data['kegiatan'])) {
						throw new Exception('Kegiatan wajib dipilih');
					}

					if (empty($data['sub_kegiatan_2']) || !isset($data['sub_kegiatan_2'])) {
						throw new Exception('Sub Kegiatan wajib dipilih');
					}

					$subKegiatanRenstraExisting = $wpdb->get_row(
						$wpdb->prepare(
							"
						SELECT 
							id,
							id_unik,
							id_visi, 
							id_misi, 
							kode_tujuan, 
							kode_sub_giat,
							tujuan_lock, 
							tujuan_teks, 
							urut_tujuan, 
							kode_sasaran, 
							sasaran_lock, 
							sasaran_teks, 
							urut_sasaran,
							COALESCE(pagu_1, 0) AS pagu_1, 
							COALESCE(pagu_2, 0) AS pagu_2, 
							COALESCE(pagu_3, 0) AS pagu_3, 
							COALESCE(pagu_4, 0) AS pagu_4, 
							COALESCE(pagu_5, 0) AS pagu_5,
							COALESCE(pagu_1_usulan, 0) AS pagu_1_usulan, 
							COALESCE(pagu_2_usulan, 0) AS pagu_2_usulan, 
							COALESCE(pagu_3_usulan, 0) AS pagu_3_usulan, 
							COALESCE(pagu_4_usulan, 0) AS pagu_4_usulan, 
							COALESCE(pagu_5_usulan, 0) AS pagu_5_usulan 
						FROM 
							data_renstra_sub_kegiatan_lokal 
						WHERE 
							id=%d AND 
							active=1",
							$data['id']
						),
						ARRAY_A
					);

					if (empty($subKegiatanRenstraExisting)) {
						throw new Exception('Sub kegiatan existing tidak ditemukan!');
					}

					for ($i = 1; $i <= $data['lama_pelaksanaan']; $i++) {
						if (is_null($data['pagu_' . $i . '_usulan']) || ($data['pagu_' . $i . '_usulan'] == "")) {
							throw new Exception("Pagu usulan tahun ke-" . $i . " pemutakhiran wajib diisi!", 1);
						}

						if (intval($data['pagu_' . $i . '_usulan']) < 0) {
							throw new Exception("Pagu usulan tahun ke-" . $i . " pemutakhiran tidak boleh negatif!", 1);
						}

						// jika non aktivkan sub giat lama pagu mutakhir wajib == pagu lama
						if ($data['disable_subgiat']) {
							if (intval($data['pagu_' . $i . '_usulan']) != intval($subKegiatanRenstraExisting['pagu_' . $i . '_usulan'])) {
								throw new Exception("Pagu usulan tahun ke-" . $i . " pemutakhiran wajib sama dengan pagu usulan tahun ke-" . $i . " existing!", 1);
							}

							if (in_array('administrator', $this->role())) {
								if (intval($data['pagu_' . $i]) != intval($subKegiatanRenstraExisting['pagu_' . $i])) {
									throw new Exception("Pagu tahun ke-" . $i . " pemutakhiran wajib sama dengan pagu tahun ke-" . $i . " existing!", 1);
								}
							}
						} else {
							if (intval($data['pagu_' . $i . '_usulan']) > intval($subKegiatanRenstraExisting['pagu_' . $i . '_usulan'])) {
								throw new Exception("Pagu usulan tahun ke-" . $i . " pemutakhiran tidak boleh lebih besar dari pagu usulan tahun ke-" . $i . " existing!", 1);
							}

							if (in_array('administrator', $this->role())) {
								if (intval($data['pagu_' . $i]) > intval($subKegiatanRenstraExisting['pagu_' . $i])) {
									throw new Exception("Pagu tahun ke-" . $i . " pemutakhiran tidak boleh lebih besar dari pagu tahun ke-" . $i . " existing!", 1);
								}
							}
						}
					}

					$program = $wpdb->get_row($wpdb->prepare("SELECT id_bidang_urusan, id_program, id_urusan, kode_bidang_urusan, kode_program, kode_urusan, nama_bidang_urusan, nama_program, nama_urusan FROM data_prog_keg WHERE id_program=%d AND tahun_anggaran=%d AND active=%d", $data['id_program'], $data['tahun_anggaran'], 1), ARRAY_A);

					if (empty($program)) {
						throw new Exception('Program pemutakhiran yang dipilih di tahun ' . $data['tahun_anggaran'] . ' tidak ditemukan, harap hubungi Admin!');
					}

					$kegiatan = $wpdb->get_row($wpdb->prepare("SELECT id_bidang_urusan, id_urusan, id_program, id_giat, kode_bidang_urusan, kode_program, kode_giat, kode_urusan, nama_bidang_urusan, nama_giat, nama_program, nama_urusan FROM data_prog_keg WHERE id_giat=%d AND tahun_anggaran=%d AND active=%d", $data['kegiatan'], $data['tahun_anggaran'], 1), ARRAY_A);

					if (empty($kegiatan)) {
						throw new Exception('Kegiatan pemutakhiran yang dipilih di tahun ' . $data['tahun_anggaran'] . ' tidak ditemukan, harap hubungi Admin!');
					}

					$subKegiatan = $wpdb->get_row($wpdb->prepare("SELECT id_bidang_urusan, id_sub_giat, id_urusan, id_giat, id_program, kode_program, nama_program, kode_giat, kode_bidang_urusan, kode_sub_giat, kode_urusan, nama_bidang_urusan, nama_giat, nama_sub_giat, nama_urusan FROM data_prog_keg WHERE id_sub_giat=%d AND tahun_anggaran=%d AND active=%d", $data['sub_kegiatan_2'], $data['tahun_anggaran'], 1), ARRAY_A);

					if (empty($subKegiatan)) {
						throw new Exception('Sub Kegiatan pemutakhiran yang dipilih di tahun ' . $data['tahun_anggaran'] . ' tidak ditemukan, harap hubungi Admin!');
					}

					$programPemutakhiranRenstra = $wpdb->get_row($wpdb->prepare("SELECT id FROM data_renstra_program_lokal WHERE kode_bidang_urusan=%s AND kode_program=%s AND id_unit=%d AND active=1", $program['kode_bidang_urusan'], $program['kode_program'], $data['id_unit']), ARRAY_A);

					if (empty($programPemutakhiranRenstra)) {
						throw new Exception("Program pemutakhiran yang dipilih tidak ditemukan di program renstra lokal, lakukan pengecekan sesuai nomenklatur dan kode program, jika merah lakukan pemutakhiran program terlebih dahulu!", 1);
					}

					$kegiatanPemutakhiranRenstra = $wpdb->get_row($wpdb->prepare("SELECT id FROM data_renstra_kegiatan_lokal WHERE kode_bidang_urusan=%s AND kode_giat=%s AND id_unit=%d AND active=1", $kegiatan['kode_bidang_urusan'], $kegiatan['kode_giat'], $data['id_unit']), ARRAY_A);

					if (empty($kegiatanPemutakhiranRenstra)) {
						throw new Exception("Kegiatan pemutakhiran yang dipilih tidak ditemukan di kegiatan renstra lokal, lakukan pengecekan sesuai nomenklatur dan kode kegiatan, jika merah lakukan pemutakhiran kegiatan terlebih dahulu!", 1);
					}

					$subKegiatanPemutakhiranRenstra = $wpdb->get_row($wpdb->prepare(
						"SELECT 
							id, 
							COALESCE(pagu_1, 0) AS pagu_1, 
							COALESCE(pagu_2, 0) AS pagu_2, 
							COALESCE(pagu_3, 0) AS pagu_3, 
							COALESCE(pagu_4, 0) AS pagu_4, 
							COALESCE(pagu_5, 0) AS pagu_5,
							COALESCE(pagu_1_usulan, 0) AS pagu_1_usulan, 
							COALESCE(pagu_2_usulan, 0) AS pagu_2_usulan, 
							COALESCE(pagu_3_usulan, 0) AS pagu_3_usulan, 
							COALESCE(pagu_4_usulan, 0) AS pagu_4_usulan, 
							COALESCE(pagu_5_usulan, 0) AS pagu_5_usulan,
							id_sub_giat_lama
						FROM 
							data_renstra_sub_kegiatan_lokal 
						WHERE 
							kode_bidang_urusan=%s AND 
							kode_sub_giat=%s AND 
							id_unit=%d AND 
							active=1",
						$subKegiatan['kode_bidang_urusan'],
						$subKegiatan['kode_sub_giat'],
						$data['id_unit']
					), ARRAY_A);

					$inputs = [];
					if (empty($subKegiatanPemutakhiranRenstra)) {
						throw new Exception("Sub kegiatan pemutakhiran yang dipilih tidak ditemukan di sub kegiatan renstra lokal, lakukan pengecekan sesuai nomenklatur jika merah lakukan pemutakhiran sub giat yang ditarget di tab Default terlebih dahulu!", 1);
					}

					// update kolom id sub giat lama di sub giat pemutakhiran 
					if (!empty($subKegiatanPemutakhiranRenstra['id_sub_giat_lama'])) {
						$sub_giat_lama = explode(",", $subKegiatanPemutakhiranRenstra['id_sub_giat_lama']);
						if (!in_array($value, $sub_giat_lama)) {
							$sub_giat_lama[] = $data['sub_kegiatan_1'];
						}
						$list_sub_giat_lama = implode(",", $sub_giat_lama);
						$inputs['id_sub_giat_lama'] = $list_sub_giat_lama;
					} else {
						$inputs['id_sub_giat_lama'] = $data['sub_kegiatan_1'];
					}

					// update pagu sub giat pemutakhiran
					if (isset($data['pagu_1_usulan'])) {
						$inputs['pagu_1_usulan'] = intval($data['pagu_1_usulan']) + intval($subKegiatanPemutakhiranRenstra['pagu_1_usulan']);
					}
					if (isset($data['pagu_2_usulan'])) {
						$inputs['pagu_2_usulan'] = intval($data['pagu_2_usulan']) + intval($subKegiatanPemutakhiranRenstra['pagu_2_usulan']);
					}
					if (isset($data['pagu_3_usulan'])) {
						$inputs['pagu_3_usulan'] = intval($data['pagu_3_usulan']) + intval($subKegiatanPemutakhiranRenstra['pagu_3_usulan']);
					}
					if (isset($data['pagu_4_usulan'])) {
						$inputs['pagu_4_usulan'] = intval($data['pagu_4_usulan']) + intval($subKegiatanPemutakhiranRenstra['pagu_4_usulan']);
					}
					if (isset($data['pagu_5_usulan'])) {
						$inputs['pagu_5_usulan'] = intval($data['pagu_5_usulan']) + intval($subKegiatanPemutakhiranRenstra['pagu_5_usulan']);
					}

					if (in_array('administrator', $this->role())) {
						if (isset($data['pagu_1'])) {
							$inputs['pagu_1'] = !empty($data['pagu_1']) || $data['pagu_1'] == 0 ? $data['pagu_1'] : $data['pagu_1_usulan'];
							$inputs['pagu_1'] = intval($inputs['pagu_1']) + intval($subKegiatanPemutakhiranRenstra['pagu_1']);
						}
						if (isset($data['pagu_2'])) {
							$inputs['pagu_2'] = !empty($data['pagu_2']) || $data['pagu_2'] == 0 ? $data['pagu_2'] : $data['pagu_2_usulan'];
							$inputs['pagu_2'] = intval($inputs['pagu_2']) + intval($subKegiatanPemutakhiranRenstra['pagu_2']);
						}
						if (isset($data['pagu_3'])) {
							$inputs['pagu_3'] = !empty($data['pagu_3']) || $data['pagu_3'] == 0 ? $data['pagu_3'] : $data['pagu_3_usulan'];
							$inputs['pagu_3'] = intval($inputs['pagu_3']) + intval($subKegiatanPemutakhiranRenstra['pagu_3']);
						}
						if (isset($data['pagu_4'])) {
							$inputs['pagu_4'] = !empty($data['pagu_4']) || $data['pagu_4'] == 0 ? $data['pagu_4'] : $data['pagu_4_usulan'];
							$inputs['pagu_4'] = intval($inputs['pagu_4']) + intval($subKegiatanPemutakhiranRenstra['pagu_4']);
						}
						if (isset($data['pagu_5'])) {
							$inputs['pagu_5'] = !empty($data['pagu_5']) || $data['pagu_5'] == 0 ? $data['pagu_5'] : $data['pagu_5_usulan'];
							$inputs['pagu_5'] = intval($inputs['pagu_5']) + intval($subKegiatanPemutakhiranRenstra['pagu_5']);
						}
					}

					// action update pagu sub giat pemutakhiran
					$result1 = $wpdb->update('data_renstra_sub_kegiatan_lokal', $inputs, array(
						'id' => $subKegiatanPemutakhiranRenstra['id']
					));

					$inputs = [];
					if ($data['disable_subgiat']) {

						// non aktivkan sub giat dan indikator existing
						$result2 = $wpdb->update('data_renstra_sub_kegiatan_lokal', [
							'active' => 0,
							'status' => 0
						], array(
							'id_unik' => $subKegiatanRenstraExisting['id_unik']
						));
					} else {

						// update pagu sub giat existing, dengan mengurangi sebesar pagu sub giat pemutakhiran 
						if (isset($data['pagu_1_usulan'])) {
							$inputs['pagu_1_usulan'] = intval($subKegiatanRenstraExisting['pagu_1_usulan']) - intval($data['pagu_1_usulan']);
						}
						if (isset($data['pagu_2_usulan'])) {
							$inputs['pagu_2_usulan'] = intval($subKegiatanRenstraExisting['pagu_2_usulan']) - intval($data['pagu_2_usulan']);
						}
						if (isset($data['pagu_3_usulan'])) {
							$inputs['pagu_3_usulan'] = intval($subKegiatanRenstraExisting['pagu_3_usulan']) - intval($data['pagu_3_usulan']);
						}
						if (isset($data['pagu_4_usulan'])) {
							$inputs['pagu_4_usulan'] = intval($subKegiatanRenstraExisting['pagu_4_usulan']) - intval($data['pagu_4_usulan']);
						}
						if (isset($data['pagu_5_usulan'])) {
							$inputs['pagu_5_usulan'] = intval($subKegiatanRenstraExisting['pagu_5_usulan']) - intval($data['pagu_5_usulan']);
						}

						if (in_array('administrator', $this->role())) {
							if (isset($data['pagu_1'])) {
								$inputs['pagu_1'] = !empty($data['pagu_1']) || $data['pagu_1'] == 0 ? $data['pagu_1'] : $data['pagu_1_usulan'];
								$inputs['pagu_1'] = intval($subKegiatanRenstraExisting['pagu_1']) - intval($inputs['pagu_1']);
							}
							if (isset($data['pagu_2'])) {
								$inputs['pagu_2'] = !empty($data['pagu_2']) || $data['pagu_2'] == 0 ? $data['pagu_2'] : $data['pagu_2_usulan'];
								$inputs['pagu_2'] = intval($subKegiatanRenstraExisting['pagu_2']) - intval($inputs['pagu_2']);
							}
							if (isset($data['pagu_3'])) {
								$inputs['pagu_3'] = !empty($data['pagu_3']) || $data['pagu_3'] == 0 ? $data['pagu_3'] : $data['pagu_3_usulan'];
								$inputs['pagu_3'] = intval($subKegiatanRenstraExisting['pagu_3']) - intval($inputs['pagu_3']);
							}
							if (isset($data['pagu_4'])) {
								$inputs['pagu_4'] = !empty($data['pagu_4']) || $data['pagu_4'] == 0 ? $data['pagu_4'] : $data['pagu_4_usulan'];
								$inputs['pagu_4'] = intval($subKegiatanRenstraExisting['pagu_4']) - intval($inputs['pagu_4']);
							}
							if (isset($data['pagu_5'])) {
								$inputs['pagu_5'] = !empty($data['pagu_5']) || $data['pagu_5'] == 0 ? $data['pagu_5'] : $data['pagu_5_usulan'];
								$inputs['pagu_5'] = intval($subKegiatanRenstraExisting['pagu_5']) - intval($inputs['pagu_5']);
							}
						}

						$result2 = $wpdb->update('data_renstra_sub_kegiatan_lokal', $inputs, array(
							'id' => $subKegiatanRenstraExisting['id']
						));
					}

					if ($result1 && $result2) {
						$wpdb->query('COMMIT');
					} else {
						$wpdb->query('ROLLBACK');
						throw new Exception("Oops terjadi kesalahan saat melakukan pemutakhiran, code:123", 1);
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses mutakhirkan sub kegiatan!'
					]);
					exit();
				} else {
					throw new Exception("Api key tidak sesuai", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function program_exist(array $data = [])
	{
		global $wpdb;

		try {

			if (empty($data)) {
				throw new Exception("Parameter kosong!", 1);
			}

			$program = $wpdb->get_row($wpdb->prepare("SELECT id_program FROM data_prog_keg WHERE kode_program=%s AND active=%d AND tahun_anggaran=%d", $data['kode_program'], 1, $data['tahun_anggaran']), ARRAY_A);

			if (empty($program)) {
				return [
					'status' => true,
					'count' => 0
				];
			} else {
				return [
					'status' => true,
					'count' => 1
				];
			}
		} catch (Exception $e) {
			return [
				'status' => false,
				'message' => $e->getMessage()
			];
		}
	}

	public function kegiatan_exist(array $data = [])
	{
		global $wpdb;

		try {

			if (empty($data)) {
				throw new Exception("Parameter kosong!", 1);
			}

			$kegiatan = $wpdb->get_row($wpdb->prepare("SELECT id_giat FROM data_prog_keg WHERE kode_giat=%s AND active=%d AND tahun_anggaran=%d", $data['kode_giat'], 1, $data['tahun_anggaran']), ARRAY_A);

			if (empty($kegiatan)) {
				return [
					'status' => true,
					'count' => 0
				];
			} else {
				return [
					'status' => true,
					'count' => 1
				];
			}
		} catch (Exception $e) {
			return [
				'status' => false,
				'message' => $e->getMessage()
			];
		}
	}

	public function subgiat_exist(array $data = [])
	{
		global $wpdb;

		try {

			if (empty($data)) {
				throw new Exception("Parameter kosong!", 1);
			}

			$subgiat = $wpdb->get_row($wpdb->prepare("SELECT id_sub_giat FROM data_prog_keg WHERE kode_sub_giat=%s AND active=%d AND tahun_anggaran=%d", $data['kode_sub_giat'], 1, $data['tahun_anggaran']), ARRAY_A);

			if (empty($subgiat)) {
				return [
					'status' => true,
					'count' => 0
				];
			} else {
				return [
					'status' => true,
					'count' => 1
				];
			}

			return [
				'status' => true,
				'count' => 0
			];
		} catch (Exception $e) {
			return [
				'status' => false,
				'message' => $e->getMessage()
			];
		}
	}

	public function subgiat_renstra_local_exist()
	{
		global $wpdb;

		try {

			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					$data = $wpdb->get_row($wpdb->prepare(
						"SELECT 
							id, 
							COALESCE(pagu_1, 0) AS pagu_1, 
							COALESCE(pagu_2, 0) AS pagu_2, 
							COALESCE(pagu_3, 0) AS pagu_3, 
							COALESCE(pagu_4, 0) AS pagu_4, 
							COALESCE(pagu_5, 0) AS pagu_5,
							COALESCE(pagu_1_usulan, 0) AS pagu_1_usulan, 
							COALESCE(pagu_2_usulan, 0) AS pagu_2_usulan, 
							COALESCE(pagu_3_usulan, 0) AS pagu_3_usulan, 
							COALESCE(pagu_4_usulan, 0) AS pagu_4_usulan, 
							COALESCE(pagu_5_usulan, 0) AS pagu_5_usulan
						FROM 
							data_renstra_sub_kegiatan_lokal 
						WHERE 
							kode_bidang_urusan=%s AND 
							kode_sub_giat=%s AND 
							id_unit=%d AND 
							active=1",
						$_POST['kode_bidang_urusan'],
						$_POST['kode_sub_giat'],
						$_POST['id_unit']
					), ARRAY_A);

					if (!empty($data)) {
						echo json_encode([
							'status' => true,
							'count' => 1,
							'data' => $data
						]);
						exit();
					} else {
						echo json_encode([
							'status' => true,
							'count' => 0,
							'data' => $data
						]);
						exit();
					}
				} else {
					throw new Exception("API tidak ditemukan!", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai!", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function mutakhirkan_program_rpjm()
	{
		global $wpdb;

		try {

			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$checkProgramRpjm = $wpdb->get_var(
						$wpdb->prepare("
							SELECT id 
							FROM data_rpjmd_program_lokal 
							WHERE id_program=%d 
							  AND active=1 
							  AND status=1 
							  AND id_unik_indikator IS NULL 
							  AND tahun_anggaran = %d
							ORDER BY id ASC
						", $_POST['id_program'], $_POST['tahun_anggaran'])
					);

					if (!empty($checkProgramRpjm)) {
						throw new Exception('Program Pemutakhiran sudah pernah ditambahkan!');
					}

					$programRpjmLama = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE id=%d", $_POST['id']), ARRAY_A);

					$program_baru = $wpdb->get_row($wpdb->prepare("SELECT DISTINCT id_program, kode_program, nama_program, kode_bidang_urusan, nama_bidang_urusan, nama_urusan, tahun_anggaran, kode_urusan FROM data_prog_keg WHERE id_program=%d AND active=%d AND tahun_anggaran=%d", $_POST['id_program'], 1, $_POST['tahun_anggaran']), ARRAY_A);

					if (empty($program_baru)) {
						throw new Exception('Program Pemutakhiran di tahun ' . $_POST['tahun_anggaran'] . ' tidak ditemukan, perlu melakukan updating master program tahun ' . $_POST['tahun_anggaran'] . '!');
					}

					// tahun anggaran n-1 ?
					$program_lama = $wpdb->get_row($wpdb->prepare("SELECT DISTINCT id_program, kode_program, nama_program, kode_bidang_urusan, nama_bidang_urusan, nama_urusan, tahun_anggaran, kode_urusan FROM data_prog_keg WHERE id_program=%d AND active=%d", $programRpjmLama['id_program'], 1), ARRAY_A);

					if (
						$program_baru['kode_bidang_urusan'] != $program_lama['kode_bidang_urusan'] ||
						$program_baru['kode_urusan'] != $program_lama['kode_urusan']
					) {
						throw new Exception("Urusan atau Bidang Urusan tidak linear antara Program Existing dan Program Pemutakhiran", 1);
					}

					$id_unik_program = $this->generateRandomString();
					$result1 = $wpdb->insert('data_rpjmd_program_lokal', [
						'id_misi' => $programRpjmLama['id_misi'],
						'id_misi_old' => $programRpjmLama['id_misi_old'],
						'id_program' => $program_baru['id_program'],
						'id_unik' => $id_unik_program,
						'id_unit' => $programRpjmLama['id_unit'],
						'id_visi' => $programRpjmLama['id_visi'],
						'is_locked' => $programRpjmLama['is_locked'],
						'is_locked_indikator' => $programRpjmLama['is_locked_indikator'],
						'kode_sasaran' => $programRpjmLama['kode_sasaran'],
						'kode_skpd' => $programRpjmLama['kode_skpd'],
						'kode_tujuan' => $programRpjmLama['kode_tujuan'],
						'misi_teks' => $programRpjmLama['misi_teks'],
						'nama_program' => $program_baru['nama_program'],
						'nama_skpd' => $programRpjmLama['nama_skpd'],
						'program_lock' => $programRpjmLama['program_lock'],
						'sasaran_lock' => $programRpjmLama['sasaran_lock'],
						'sasaran_teks' => $programRpjmLama['sasaran_teks'],
						'satuan' => $programRpjmLama['satuan'],
						'status' => 1,
						'tujuan_lock' => $programRpjmLama['tujuan_lock'],
						'tujuan_teks' => $programRpjmLama['tujuan_teks'],
						'urut_misi' => $programRpjmLama['urut_misi'],
						'urut_sasaran' => $programRpjmLama['urut_sasaran'],
						'urut_tujuan' => $programRpjmLama['urut_tujuan'],
						'visi_teks' => $programRpjmLama['visi_teks'],
						'active' => 1,
						'update_at' => "'" . date('Y-m-d H:i:s') . "'",
						'id_program_lama' => $programRpjmLama['id_program']
					]);

					$indikatorProgramRpjm = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE id_unik=%s AND id_unik_indikator IS NOT NULL AND active=1 AND status=1 ORDER BY id ASC", $_POST['id_unik']), ARRAY_A);

					if (!empty($indikatorProgramRpjm)) {

						$programRpjmBaru = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpjmd_program_lokal WHERE id_program=%d AND active=1 AND status=1 AND id_unik_indikator IS NULL ORDER BY id ASC", $_POST['id_program']));

						if (empty($programRpjmBaru)) {
							throw new Exception('Program Pemutakhiran tidak ditemukan!');
						}

						$arrStatus = [];
						foreach ($indikatorProgramRpjm as $key => $indikatorProgram) {

							$arrStatus[] = $wpdb->insert('data_rpjmd_program_lokal', [
								'id_misi' => $programRpjmBaru->id_misi,
								'id_program' => $programRpjmBaru->id_program,
								'id_unik' => $id_unik_program,
								'id_unik_indikator' => $this->generateRandomString(),
								'id_unit' => $indikatorProgram['id_unit'],
								'id_visi' => $programRpjmBaru->id_visi,
								'is_locked' => 0,
								'is_locked_indikator' => 0,
								'indikator' => $indikatorProgram['indikator'],
								'kode_sasaran' => $programRpjmBaru->kode_sasaran,
								'kode_skpd' => $indikatorProgram['kode_skpd'],
								'kode_tujuan' => $programRpjmBaru->id_unik,
								'misi_teks' => $programRpjmBaru->misi_teks,
								'nama_program' => $programRpjmBaru->nama_program,
								'nama_skpd' => $indikatorProgram['nama_skpd'],
								'pagu_1' => $indikatorProgram['pagu_1'],
								'pagu_2' => $indikatorProgram['pagu_2'],
								'pagu_3' => $indikatorProgram['pagu_3'],
								'pagu_4' => $indikatorProgram['pagu_4'],
								'pagu_5' => $indikatorProgram['pagu_5'],
								'program_lock' => 0,
								'sasaran_lock' => $programRpjmBaru->sasaran_lock,
								'sasaran_teks' => $programRpjmBaru->sasaran_teks,
								'satuan' => $indikatorProgram['satuan'],
								'target_1' => $indikatorProgram['target_1'],
								'target_2' => $indikatorProgram['target_2'],
								'target_3' => $indikatorProgram['target_3'],
								'target_4' => $indikatorProgram['target_4'],
								'target_5' => $indikatorProgram['target_5'],
								'target_awal' => $indikatorProgram['target_awal'],
								'target_akhir' => $indikatorProgram['target_akhir'],
								'tujuan_lock' => $programRpjmBaru->tujuan_lock,
								'tujuan_teks' => $programRpjmBaru->tujuan_teks,
								'urut_misi' => $programRpjmBaru->urut_misi,
								'urut_sasaran' => $programRpjmBaru->urut_sasaran,
								'urut_tujuan' => $programRpjmBaru->urut_tujuan,
								'visi_teks' => $programRpjmBaru->visi_teks,
								'status' => 1,
								'active' => 1,
								'id_program_lama' => $programRpjmBaru->id_program_lama
							]);
						}
					}

					$result2 = $wpdb->query($wpdb->prepare("UPDATE data_rpjmd_program_lokal SET active=0, status=0, update_at='" . date('Y-m-d H:i:s') . "' WHERE id_unik=%s AND active=%d", $programRpjmLama['id_unik'], 1));

					if ($result1 && $result2) {
						if (!empty($indikatorProgramRpjm)) {
							$res = array_unique($arrStatus);
							if (count($res) === 1 && $res[0]) {
								$wpdb->query('COMMIT');
							} else {
								$wpdb->query('ROLLBACK');
								throw new Exception("Oops terjadi kesalahan mengambil indikator program lama!", 1);
							}
						} else {
							$wpdb->query('COMMIT');
						}
					} else {
						$wpdb->query('ROLLBACK');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses mutakhirkan program!'
					]);
					exit();
				} else {
					throw new Exception("API tidak ditemukan!", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai!", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function mutakhirkan_program_rpd()
	{
		global $wpdb;

		try {

			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$checkProgramRpd = $wpdb->get_var($wpdb->prepare("SELECT id FROM data_rpd_program_lokal WHERE id_program=%d AND active=1 AND id_unik_indikator IS NULL ORDER BY id ASC", $_POST['id_program']));

					if (!empty($checkProgramRpd)) {
						throw new Exception('Program Pemutakhiran sudah pernah ditambahkan!');
					}

					$programRpdLama = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpd_program_lokal WHERE id=%d", $_POST['id']), ARRAY_A);

					$program_baru = $wpdb->get_row($wpdb->prepare("SELECT DISTINCT id_program, kode_program, nama_program, kode_bidang_urusan, nama_bidang_urusan, nama_urusan, tahun_anggaran, kode_urusan FROM data_prog_keg WHERE id_program=%d AND active=%d AND tahun_anggaran=%d", $_POST['id_program'], 1, $_POST['tahun_anggaran']), ARRAY_A);

					if (empty($program_baru)) {
						throw new Exception('Program Pemutakhiran di tahun ' . $_POST['tahun_anggaran'] . ' tidak ditemukan, perlu melakukan updating master program tahun ' . $_POST['tahun_anggaran'] . '!');
					}

					// tahun anggaran n-1 ?
					$program_lama = $wpdb->get_row($wpdb->prepare("SELECT DISTINCT id_program, kode_program, nama_program, kode_bidang_urusan, nama_bidang_urusan, nama_urusan, tahun_anggaran, kode_urusan FROM data_prog_keg WHERE id_program=%d AND active=%d", $programRpdLama['id_program'], 1), ARRAY_A);

					if (
						$program_baru['kode_bidang_urusan'] != $program_lama['kode_bidang_urusan'] ||
						$program_baru['kode_urusan'] != $program_lama['kode_urusan']
					) {
						throw new Exception("Urusan atau Bidang Urusan tidak linear antara Program Existing dan Program Pemutakhiran", 1);
					}

					$id_unik_program = $this->generateRandomString();
					$result1 = $wpdb->insert('data_rpd_program_lokal', [
						'id_unik' => $id_unik_program,
						'kode_sasaran' => $programRpdLama['kode_sasaran'],
						'nama_program' => $program_baru['nama_program'],
						'catatan' => $programRpdLama['catatan'],
						'id_program' => $program_baru['id_program'],
						'active' => 1,
						'update_at' => "'" . date('Y-m-d H:i:s') . "'",
						'id_program_lama' => $programRpdLama['id_program']
					]);

					$indikatorProgramRpd = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_rpd_program_lokal WHERE id_unik=%s AND id_unik_indikator IS NOT NULL AND active=1 ORDER BY id ASC", $_POST['id_unik']), ARRAY_A);

					if (!empty($indikatorProgramRpd)) {

						$programRpdBaru = $wpdb->get_row($wpdb->prepare("SELECT * FROM data_rpd_program_lokal WHERE id_program=%d AND active=1 AND id_unik_indikator IS NULL ORDER BY id ASC", $_POST['id_program']));

						if (empty($programRpdBaru)) {
							throw new Exception('Program Pemutakhiran tidak ditemukan!');
						}

						$arrStatus = [];
						foreach ($indikatorProgramRpd as $key => $indikatorProgram) {

							$arrStatus[] = $wpdb->insert('data_rpd_program_lokal', [
								'kode_sasaran' => $programRpdBaru->kode_sasaran,
								'nama_program' => $programRpdBaru->nama_program,
								'id_program' => $programRpdBaru->id_program,
								'id_unik' => $id_unik_program,
								'id_unik_indikator' => $this->generateRandomString(),
								'id_unit' => $indikatorProgram['id_unit'],
								'kode_skpd' => $indikatorProgram['kode_skpd'],
								'nama_skpd' => $indikatorProgram['nama_skpd'],
								'indikator' => $indikatorProgram['indikator'],
								'target_awal' => $indikatorProgram['target_awal'],
								'target_1' => $indikatorProgram['target_1'],
								'pagu_1' => $indikatorProgram['pagu_1'],
								'target_2' => $indikatorProgram['target_2'],
								'pagu_2' => $indikatorProgram['pagu_2'],
								'target_3' => $indikatorProgram['target_3'],
								'pagu_3' => $indikatorProgram['pagu_3'],
								'target_4' => $indikatorProgram['target_4'],
								'pagu_4' => $indikatorProgram['pagu_4'],
								'target_5' => $indikatorProgram['target_5'],
								'pagu_5' => $indikatorProgram['pagu_5'],
								'target_akhir' => $indikatorProgram['target_akhir'],
								'catatan' => $indikatorProgram['catatan'],
								'satuan' => $indikatorProgram['satuan'],
								'update_at' => date('Y-m-d H:i:s'),
								'active' => 1,
								'id_program_lama' => $programRpdBaru->id_program_lama
							]);
						}
					}

					$result2 = $wpdb->query($wpdb->prepare("UPDATE data_rpd_program_lokal SET active=0, update_at='" . date('Y-m-d H:i:s') . "' WHERE id_unik=%s AND active=%d", $programRpdLama['id_unik'], 1));

					if ($result1 && $result2) {
						if (!empty($indikatorProgramRpd)) {
							$res = array_unique($arrStatus);
							if (count($res) === 1 && $res[0]) {
								$wpdb->query('COMMIT');
							} else {
								$wpdb->query('ROLLBACK');
								throw new Exception("Oops terjadi kesalahan mengambil indikator program lama!", 1);
							}
						} else {
							$wpdb->query('COMMIT');
						}
					} else {
						$wpdb->query('ROLLBACK');
					}

					echo json_encode([
						'status' => true,
						'message' => 'Sukses mutakhirkan program!'
					]);
					exit();
				} else {
					throw new Exception("API tidak ditemukan!", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai!", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function list_jadwal_rpjmd()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

					$data = $wpdb->get_results($wpdb->prepare("SELECT id_jadwal_lokal, nama, tahun_anggaran, status FROM data_jadwal_lokal WHERE id_tipe=%d ORDER BY status", $_POST['tipe']), ARRAY_A);

					echo json_encode([
						'status' => true,
						'data' => $data
					]);
					exit();
				} else {
					throw new Exception("API tidak ditemukan!", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai!", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function pohon_kinerja_rpd()
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/pohon_kinerja/wpsipd-public-pohon-kinerja-rpd.php';
	}

	public function pohon_kinerja_renja($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/pohon_kinerja/wpsipd-public-pohon-kinerja-renja.php';
	}

	public function copy_data_renstra_lokal()
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					if (empty($_POST['id_jadwal'])) {
						$ret['status'] = 'error';
						$ret['message'] = 'ID Jadwal tidak boleh kosong!';
					}
					$filter_renstra = array('id_jadwal' => $_POST['id_jadwal']);
					$where_skpd = '';

					if (!empty($_POST['id_unit'])) {
						$filter_renstra['id_unit'] = $_POST['id_unit'];
						$where_skpd = $wpdb->prepare("AND id_unit=%s", $_POST['id_unit']);
					}

					// Nonaktifkan tujuan yang ada
					$wpdb->update('data_renstra_tujuan', array('active' => 0), $filter_renstra);

					// Ambil data tujuan lokal

					$data_jadwal_renstra = $wpdb->get_var(
						$wpdb->prepare('
							SELECT 
								tahun_anggaran 
							FROM data_jadwal_lokal 
							WHERE id_jadwal_lokal=%d
						', $_POST['id_jadwal'])
					);

					$tujuan_lokal = $wpdb->get_results($wpdb->prepare("
						SELECT * 
						FROM data_renstra_tujuan_lokal 
						WHERE active=%d 
							AND tahun_anggaran=%d
							$where_skpd
					", 1, $data_jadwal_renstra), ARRAY_A);
					if (!empty($tujuan_lokal)) {
						foreach ($tujuan_lokal as $tujuan_value) {
							$data = $tujuan_value;
							$data['update_at'] = date('Y-m-d H:i:s');
							$data['tahun_anggaran'] = $data_jadwal_renstra;
							$data['id_jadwal'] = $_POST['id_jadwal'];

							// Hapus data yang tidak diperlukan
							foreach ($data as $k => $v) {
								if (
									strpos($k, '_usulan') !== false ||
									strpos($k, 'catatan') !== false ||
									strpos($k, '_lama') !== false ||
									$k == 'id'
								) {
									unset($data[$k]);
								}
							}

							// Cek apakah tujuan sudah ada
							if (empty($tujuan_value['id_unik_indikator'])) {
								$cek_tujuan_renstra = $wpdb->get_var($wpdb->prepare("
									SELECT id 
									FROM data_renstra_tujuan 
									WHERE id_unik=%s 
									  AND id_unik_indikator IS NULL
									  $where_skpd
								", $tujuan_value['id_unik']));
							} else {
								$cek_tujuan_renstra = $wpdb->get_var($wpdb->prepare("
									SELECT id 
									FROM data_renstra_tujuan 
									WHERE id_unik=%s 
									  AND id_unik_indikator=%s
									  $where_skpd
								", $tujuan_value['id_unik'], $tujuan_value['id_unik_indikator']));
							}

							if (empty($cek_tujuan_renstra)) {
								$wpdb->insert("data_renstra_tujuan", $data);
							} else {
								$wpdb->update("data_renstra_tujuan", $data, array('id' => $cek_tujuan_renstra));
							}

							// Proses data sasaran
							$wpdb->update('data_renstra_sasaran', array('active' => 0), array(
								'kode_tujuan' => $tujuan_value['id_unik'],
								'tahun_anggaran' => $data_jadwal_renstra
							));

							$sasaran_lokal = $wpdb->get_results($wpdb->prepare("
								SELECT * 
								FROM data_renstra_sasaran_lokal 
								WHERE kode_tujuan=%s 
								 	AND active=1
									AND tahun_anggaran=%d
								 	$where_skpd
							", $tujuan_value['id_unik'], $data['tahun_anggaran']), ARRAY_A);

							if (!empty($sasaran_lokal)) {
								foreach ($sasaran_lokal as $sasaran_value) {
									$data = $sasaran_value;
									$data['update_at'] = date('Y-m-d H:i:s');
									$data['tahun_anggaran'] = $data_jadwal_renstra;
									$data['id_jadwal'] = $_POST['id_jadwal'];

									foreach ($data as $k => $v) {
										if (
											strpos($k, '_usulan') !== false ||
											strpos($k, 'catatan') !== false ||
											strpos($k, '_lama') !== false ||
											$k == 'id'
										) {
											unset($data[$k]);
										}
									}

									if (empty($sasaran_value['id_unik_indikator'])) {
										$cek_sasaran_renstra = $wpdb->get_var($wpdb->prepare("
											SELECT id 
											FROM data_renstra_sasaran 
											WHERE id_unik=%s 
											  AND id_unik_indikator IS NULL
											  $where_skpd
										", $sasaran_value['id_unik']));
									} else {
										$cek_sasaran_renstra = $wpdb->get_var($wpdb->prepare("
											SELECT id 
											FROM data_renstra_sasaran 
											WHERE id_unik=%s 
											  AND id_unik_indikator=%s
											  $where_skpd
										", $sasaran_value['id_unik'], $sasaran_value['id_unik_indikator']));
									}

									if (empty($cek_sasaran_renstra)) {
										$wpdb->insert('data_renstra_sasaran', $data);
									} else {
										$wpdb->update("data_renstra_sasaran", $data, array('id' => $cek_sasaran_renstra));
									}

									// Proses data program
									$wpdb->update('data_renstra_program', array('active' => 0), array(
										'kode_sasaran' => $sasaran_value['id_unik'],
										'tahun_anggaran' => $data_jadwal_renstra
									));

									$program_lokal = $wpdb->get_results($wpdb->prepare("
										SELECT * 
										FROM data_renstra_program_lokal 
										WHERE kode_sasaran=%s 
											AND active=1
											AND tahun_anggaran=%d
											$where_skpd
									", $sasaran_value['id_unik'], $data['tahun_anggaran']), ARRAY_A);

									if (!empty($program_lokal)) {
										foreach ($program_lokal as $program_value) {
											$data = $program_value;
											$data['update_at'] = date('Y-m-d H:i:s');
											$data['tahun_anggaran'] = $data_jadwal_renstra;
											$data['id_jadwal'] = $_POST['id_jadwal'];

											foreach ($data as $k => $v) {
												if (
													strpos($k, '_usulan') !== false ||
													strpos($k, 'catatan') !== false ||
													strpos($k, '_lama') !== false ||
													$k == 'id'
												) {
													unset($data[$k]);
												}
											}

											if (empty($program_value['id_unik_indikator'])) {
												$cek_program = $wpdb->get_var($wpdb->prepare("
													SELECT id 
													FROM data_renstra_program 
													WHERE id_unik=%s 
													  AND id_unik_indikator IS NULL
													  $where_skpd
												", $program_value['id_unik']));
											} else {
												$cek_program = $wpdb->get_var($wpdb->prepare("
													SELECT id 
													FROM data_renstra_program 
													WHERE id_unik=%s 
													  AND id_unik_indikator=%s
													  $where_skpd
												", $program_value['id_unik'], $program_value['id_unik_indikator']));
											}

											if (empty($cek_program)) {
												$wpdb->insert('data_renstra_program', $data);
											} else {
												$wpdb->update("data_renstra_program", $data, array('id' => $cek_program));
											}

											// Proses data kegiatan
											$wpdb->update('data_renstra_kegiatan', array('active' => 0), array(
												'kode_unik_program' => $program_value['id_unik'],
												'tahun_anggaran' => $data_jadwal_renstra
											));

											$kegiatan_lokal = $wpdb->get_results($wpdb->prepare("
												SELECT * 
												FROM data_renstra_kegiatan_lokal 
												WHERE kode_program=%s 
													AND active=1
													AND tahun_anggaran=%d
													$where_skpd
											", $program_value['id_unik'], $data['tahun_anggaran']), ARRAY_A);

											if (!empty($kegiatan_lokal)) {
												foreach ($kegiatan_lokal as $kegiatan_value) {
													$data = $kegiatan_value;

													if (empty($data['kode_unik_program'])) {
														$data['kode_unik_program'] = $data['kode_program'];
													}

													$data['update_at'] = date('Y-m-d H:i:s');
													$data['tahun_anggaran'] = $data_jadwal_renstra;
													$data['id_jadwal'] = $_POST['id_jadwal'];

													foreach ($data as $k => $v) {
														if (
															strpos($k, '_usulan') !== false ||
															strpos($k, 'catatan') !== false ||
															strpos($k, '_lama') !== false ||
															$k == 'id'
														) {
															unset($data[$k]);
														}
													}

													if (empty($kegiatan_value['id_unik_indikator'])) {
														$cek_kegiatan = $wpdb->get_var($wpdb->prepare("
															SELECT id 
															FROM data_renstra_kegiatan 
															WHERE id_unik=%s 
															  AND id_unik_indikator IS NULL
															  $where_skpd
														", $kegiatan_value['id_unik']));
													} else {
														$cek_kegiatan = $wpdb->get_var($wpdb->prepare("
															SELECT id 
															FROM data_renstra_kegiatan 
															WHERE id_unik=%s 
															  AND id_unik_indikator=%s
															  $where_skpd
														", $kegiatan_value['id_unik'], $kegiatan_value['id_unik_indikator']));
													}

													if (empty($cek_kegiatan)) {
														$wpdb->insert('data_renstra_kegiatan', $data);
													} else {
														$wpdb->update("data_renstra_kegiatan", $data, array('id' => $cek_kegiatan));
													}

													// Proses data sub-kegiatan
													$wpdb->update('data_renstra_sub_kegiatan', array('active' => 0), array(
														'kode_kegiatan' => $kegiatan_value['id_unik'],
														'tahun_anggaran' => $data_jadwal_renstra
													));

													$sub_kegiatan_lokal = $wpdb->get_results($wpdb->prepare("
														SELECT * 
														FROM data_renstra_sub_kegiatan_lokal 
														WHERE kode_kegiatan=%s 
															AND active=1
															AND tahun_anggaran=%d
															$where_skpd
													", $kegiatan_value['id_unik'], $data['tahun_anggaran']), ARRAY_A);

													if (!empty($sub_kegiatan_lokal)) {
														foreach ($sub_kegiatan_lokal as $sub_kegiatan_value) {
															$data = $sub_kegiatan_value;
															$data['update_at'] = date('Y-m-d H:i:s');
															$data['tahun_anggaran'] = $data_jadwal_renstra;
															$data['id_jadwal'] = $_POST['id_jadwal'];

															foreach ($data as $k => $v) {
																if (
																	strpos($k, '_usulan') !== false ||
																	strpos($k, 'catatan') !== false ||
																	strpos($k, '_lama') !== false ||
																	$k == 'id'
																) {
																	unset($data[$k]);
																}
															}

															if (empty($sub_kegiatan_value['id_unik_indikator'])) {
																$cek_sub_kegiatan = $wpdb->get_var($wpdb->prepare("
																	SELECT id 
																	FROM data_renstra_sub_kegiatan 
																	WHERE id_unik=%s 
																	  AND id_unik_indikator IS NULL
																	  $where_skpd
																", $sub_kegiatan_value['id_unik']));
															} else {
																$cek_sub_kegiatan = $wpdb->get_var($wpdb->prepare("
																	SELECT id 
																	FROM data_renstra_sub_kegiatan 
																	WHERE id_unik=%s 
																	  AND id_unik_indikator=%s
																	  $where_skpd
																", $sub_kegiatan_value['id_unik'], $sub_kegiatan_value['id_unik_indikator']));
															}

															if (empty($cek_sub_kegiatan)) {
																$wpdb->insert('data_renstra_sub_kegiatan', $data);
															} else {
																$wpdb->update("data_renstra_sub_kegiatan", $data, array('id' => $cek_sub_kegiatan));
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}

					// Return success message
					$return = [
						'status' => 'Error',
						'message' => 'Sukses copy data Tujuan dan Sasaran dari Renstra Lokal!'
					];
					die(json_encode($return));
				} else {
					$return = [
						'status' => 'Error',
						'message' => 'API KEY tidak sesuai !'
					];
					die(json_encode($return));
				}
			} else {
				$return = [
					'status' => 'Error',
					'message' => 'Format Tidak Sesuai !'
				];
				die(json_encode($return));
			}
		} catch (Exception $e) {
			$return = [
				'status' => 'Error',
				'message' => $e->getMessage()
			];
			die(json_encode($return));
		}
	}

	public function konteks_resiko_manrisk($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-list-konteks-resiko-manrisk.php';
	}

	public function skor_resiko_manrisk($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-detail-skor-resiko-manrisk.php';
	}

	public function kode_resiko_manrisk($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-detail-kode-resiko-manrisk.php';
	}

	public function rpjmd_renstra_manrisk($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-list-rpjmd-renstra-manrisk.php';
	}

	public function tujuan_sasaran_manrisk($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-list-tujuan-sasaran-manrisk.php';
	}

	public function program_kegiatan_manrisk($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-list-program-kegiatan-manrisk.php';
	}

	public function detail_konteks_resiko_manrisk($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-detail-konteks-resiko-manrisk.php';
	}

	public function detail_rpjmd_renstra_manrisk($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-detail-rpjmd-renstra-manrisk.php';
	}

	public function detail_tujuan_sasaran_manrisk($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-detail-tujuan-sasaran-manrisk.php';
	}

	public function detail_program_kegiatan_manrisk($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-detail-program-kegiatan-manrisk.php';
	}

	public function manrisk_list($atts)
	{

		if (!empty($_GET) && !empty($_GET['post'])) {
			return '';
		}

		require_once WPSIPD_PLUGIN_PATH . 'public/partials/renstra/wpsipd-public-list-manrisk.php';
	}

	public function get_table_tujuan_sasaran()
	{
	    global $wpdb;
	    $ret = array(
	        'status' => 'success',
	        'message' => 'Berhasil get data!',
	        'data' => array()
	    );

	    if (!empty($_POST)) {
	        if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

	            if (empty($_POST['tahun_anggaran'])) {
	                $ret['status'] = 'error';
	                $ret['message'] = 'Tahun Anggaran kosong!';
	                 die(json_encode($ret));
	            }
	            $tahun_anggaran = intval($_POST['tahun_anggaran']);

	            if (empty($_POST['id_skpd'])) {
	                $ret['status'] = 'error';
	                $ret['message'] = 'ID SKPD kosong!';
	                die(json_encode($ret));
	            }
	            $id_skpd = intval($_POST['id_skpd']);

	            if (empty($_POST['id_jadwal'])) {
	                $ret['status'] = 'error';
	                $ret['message'] = 'ID Jadwal kosong!';
	                die(json_encode($ret));
	            }
	            $id_jadwal = intval($_POST['id_jadwal']);

	            $this->get_data_renstra_manrisk($tahun_anggaran, $id_skpd, $id_jadwal, $_POST['tipe_renstra']);

	            $user_id = um_user('ID');
	            $user_meta = get_userdata($user_id);
	            $nama_pemda = get_option('_crb_daerah');
	            if (empty($nama_pemda) || $nama_pemda == 'false') {
	                $nama_pemda = '';
	            }
	            $get_data = $wpdb->get_results($wpdb->prepare("
	                SELECT 
	                    * 
	                FROM data_tujuan_sasaran_manrisk_sebelum
	                WHERE tahun_anggaran = %d
	                    AND id_skpd = %d
	                    AND active = 1
	            ", $tahun_anggaran, $id_skpd),
	            ARRAY_A);

	            $html = '';
	            if (!empty($get_data)) {
	                $grouped_data = array();
	                $data_sesudah = false;
	                
	                $tujuan_sasaran_groups = array();
	                
	                foreach ($get_data as $row) {
	                    $kode_bidang = '';
	                    $nama_bidang = '';
	                    $nama_tujuan_sasaran = '';

	                    if (isset($row['tipe']) && $row['tipe'] == 0) {
	                        $get_data_tujuan = $wpdb->get_row($wpdb->prepare("
	                            SELECT 
	                                id_unik, 
	                                tujuan_teks, 
	                                kode_bidang_urusan, 
	                                nama_bidang_urusan
	                            FROM data_renstra_tujuan
	                            WHERE id_unik = %d
	                            	AND id_jadwal = %d
	                            	AND id_unit = %d
	                            	AND active = 1
	                        ", $row['id_tujuan_sasaran'], $id_jadwal, $id_skpd),
	                        ARRAY_A);

	                        if (!empty($get_data_tujuan)) {
	                            $nama_tujuan_sasaran = $get_data_tujuan['tujuan_teks'];
	                            $kode_bidang = $get_data_tujuan['kode_bidang_urusan'];
	                            $nama_bidang = preg_replace('/^\d+\.\d+\s*/', '', $get_data_tujuan['nama_bidang_urusan']);

	                            $get_data_indikator = $wpdb->get_row($wpdb->prepare("
	                                SELECT 
	                                    id,
	                                    indikator_teks
	                                FROM data_renstra_tujuan
	                                WHERE id_unik = %s
	                                	AND id_unik_indikator = %s
	                                	AND active = 1
	                              ", $get_data_tujuan['id_unik'], $row['id_indikator']), ARRAY_A);
	                              $row['indikator'] = !empty($get_data_indikator) ? $get_data_indikator['indikator_teks'] : '';
	                              $row['label_tipe'] = 'Tujuan OPD:';
	                        }

	                    } elseif (isset($row['tipe']) && $row['tipe'] == 1) {
	                        $get_data_sasaran = $wpdb->get_row($wpdb->prepare("
	                            SELECT 
	                                id_unik, 
	                                sasaran_teks, 
	                                kode_bidang_urusan, 
	                                nama_bidang_urusan
	                            FROM data_renstra_sasaran
	                            WHERE id_unik = %d
	                                AND id_jadwal = %d
	                                AND id_unit = %d
	                                AND active = 1
	                        ", $row['id_tujuan_sasaran'], $id_jadwal, $id_skpd),
	                        ARRAY_A);
	                          
	                        if (!empty($get_data_sasaran)) {
	                            $nama_tujuan_sasaran = $get_data_sasaran['sasaran_teks'];
	                            $kode_bidang = $get_data_sasaran['kode_bidang_urusan'];
	                            $nama_bidang = preg_replace('/^\d+\.\d+\s*/', '', $get_data_sasaran['nama_bidang_urusan']);

	                            $get_data_indikator = $wpdb->get_row($wpdb->prepare("
	                                SELECT 
	                                    id,
	                                    indikator_teks
	                                FROM data_renstra_sasaran
	                                WHERE id_unik = %s
	                                    AND id_unik_indikator = %s
	                                    AND active = 1
	                            ", $get_data_sasaran['id_unik'], $row['id_indikator']), ARRAY_A);

	                            $row['indikator'] = !empty($get_data_indikator) ? $get_data_indikator['indikator_teks'] : '';
	                            $row['label_tipe'] = 'Sasaran OPD:';
	                          }
	                      	}

	                      	$row['nama_tujuan_sasaran'] = $nama_tujuan_sasaran;

	                      	if (!empty($kode_bidang)) {
	                          	$grouped_data[$kode_bidang]['nama_bidang'] = $nama_bidang;
	                          
	                         	$tujuan_sasaran_key = $row['id_tujuan_sasaran'] . '_' . $row['tipe'];
	                          
	                          	if (!isset($tujuan_sasaran_groups[$kode_bidang][$tujuan_sasaran_key])) {
	                              	$tujuan_sasaran_groups[$kode_bidang][$tujuan_sasaran_key] = array(
	                                  	'nama_tujuan_sasaran' => $nama_tujuan_sasaran,
	                                  	'label_tipe' => $row['label_tipe'],
	                                  	'tipe' => $row['tipe'],
	                                  	'id_tujuan_sasaran' => $row['id_tujuan_sasaran'],
	                                  	'indikator' => array()
	                              	);
	                          	}
	                          
	                          	$indikator_key = $row['id_indikator'];
	                          	if (!isset($tujuan_sasaran_groups[$kode_bidang][$tujuan_sasaran_key]['indikator'][$indikator_key])) {
	                              	$tujuan_sasaran_groups[$kode_bidang][$tujuan_sasaran_key]['indikator'][$indikator_key] = array(
	                                  	'indikator_text' => $row['indikator'],
	                                  	'data' => array()
	                              	);
	                          	}
	                          
	                         	$tujuan_sasaran_groups[$kode_bidang][$tujuan_sasaran_key]['indikator'][$indikator_key]['data'][] = $row;
	                          
	                          	$grouped_data[$kode_bidang]['tujuan_sasaran_groups'] = $tujuan_sasaran_groups[$kode_bidang];
	                      	}
	                  	}
	                  
	                  	// shorting numeric
	                  	uksort($grouped_data, function($a, $b) {
	                      	$a_clean = str_replace(',', '.', (string)$a);
	                      	$b_clean = str_replace(',', '.', (string)$b);

	                      	$a_is_num = (bool) preg_match('/^[0-9]+(\.[0-9]+)*$/', $a_clean);
	                      	$b_is_num = (bool) preg_match('/^[0-9]+(\.[0-9]+)*$/', $b_clean);

	                      	if ($a_is_num && $b_is_num) {
	                          	$a_parts = explode('.', $a_clean);
	                          	$b_parts = explode('.', $b_clean);
	                          	$len = max(count($a_parts), count($b_parts));
	                          	for ($i = 0; $i < $len; $i++) {
	                              	$ap = isset($a_parts[$i]) ? intval($a_parts[$i]) : 0;
	                              	$bp = isset($b_parts[$i]) ? intval($b_parts[$i]) : 0;
	                              	if ($ap === $bp) {
	                                  	continue;
	                             }
	                             return ($ap < $bp) ? -1 : 1;
	                        }
	                        return 0;
	                    }

	                    if ($a_is_num && !$b_is_num) return -1;
	                    if (!$a_is_num && $b_is_num) return 1;

	                    return strcasecmp($a_clean, $b_clean);
	                });

	                foreach ($grouped_data as $group) {
	                    $html .= '
	                        <tr style="background:#f0f0f0; font-weight:bold;">
	                            <td colspan="30">' . $group['nama_bidang'] . '</td>
	                        </tr>
	                    ';

	                    $no = 1;
	                    
	                    uasort($group['tujuan_sasaran_groups'], function($a, $b) {
	                        return $a['tipe'] <=> $b['tipe'];
	                    });
	                      
	                    foreach ($group['tujuan_sasaran_groups'] as $tujuan_sasaran_group) {
	                        $tampil_data_tujuan_sasaran = true;
	                        $tampil_data_tujuan_sasaran_sesudah = true;
	                        
	                        $total_rows = 0;
	                        foreach ($tujuan_sasaran_group['indikator'] as $indikator_data) {
	                            $total_rows += count($indikator_data['data']);
	                        }
	                          
	                        foreach ($tujuan_sasaran_group['indikator'] as $indikator_id => $indikator_data) {
	                            $tampil_indikator = true;
	                            $tampil_indikator_sesudah = true;
	                            $indikator_count = count($indikator_data['data']);
	                              
	                            foreach ($indikator_data['data'] as $index => $data_sebelum) {
	                                $controllable = '';
	                                $pemilik_resiko = '';
	                                $sumber_sebab = '';
	                                $pihak_terkena = '';
	                                $nilai_resiko = '';

	                                if ($data_sebelum['controllable'] == 0) {
	                                    $controllable = 'Controllable';
	                                  } elseif ($data_sebelum['controllable'] == 1) {
	                                    $controllable = 'Uncontrollable';
	                                  }

	                                if ($data_sebelum['pemilik_resiko'] == 'kepala_daerah') {
	                                    $pemilik_resiko = 'Kepala Daerah';
	                                } elseif ($data_sebelum['pemilik_resiko'] == 'kepala_opd') {
	                                    $pemilik_resiko = 'Kepala OPD';
	                                } elseif ($data_sebelum['pemilik_resiko'] == 'kepala_bidang') {
	                                    $pemilik_resiko = 'Kepala Bidang';
	                                }

	                                if ($data_sebelum['sumber_sebab'] == 'internal') {
	                                    $sumber_sebab = 'Internal';
	                                } elseif ($data_sebelum['sumber_sebab'] == 'eksternal') {
	                                    $sumber_sebab = 'Eksternal';
	                                } elseif ($data_sebelum['sumber_sebab'] == 'internal_eksternal') {
	                                    $sumber_sebab = 'Internal Eksternal';
	                                }

	                                if ($data_sebelum['pihak_terkena'] == 'pemda') {
	                                    $pihak_terkena = 'Pemerintah ' . $nama_pemda;
	                                } elseif ($data_sebelum['pihak_terkena'] == 'perangkat_daerah') {
	                                    $pihak_terkena = 'Perangkat Daerah';
	                                } elseif ($data_sebelum['pihak_terkena'] == 'kepala_opd') {
	                                    $pihak_terkena = 'Kepala OPD';
	                                } elseif ($data_sebelum['pihak_terkena'] == 'pegawai_opd') {
	                                    $pihak_terkena = 'Pegawai OPD';
	                                } elseif ($data_sebelum['pihak_terkena'] == 'masyarakat') {
	                                    $pihak_terkena = 'Masyarakat';
	                                }

	                                $nilai_resiko = $data_sebelum['skala_dampak']*$data_sebelum['skala_kemungkinan'];
	                                $id_tujuan = $data_sebelum['id_tujuan_sasaran'];
	                                $tipe_target = intval($data_sebelum['tipe']);
	                                $id_indikator = $data_sebelum['id_indikator'];

	                                $get_data_sesudah = $wpdb->get_results($wpdb->prepare("
	                                    SELECT 
	                                          * 
	                                    FROM data_tujuan_sasaran_manrisk_sesudah
	                                    WHERE id_sebelum = %d
	                                      	AND active = 1
	                                ", $data_sebelum['id']), ARRAY_A);

	                                $id_sesudah = 0;
	                                $tipe_sesudah = 0;
	                                $id_tujuan_sesudah = 0;
	                                $id_indikator_sesudah = 0;

	                                $html .= '<tr>';
	                                
	                                if ($tampil_data_tujuan_sasaran) {
	                                    $html .= '<td class="text-left" rowspan="' . $total_rows . '">' . $no++ . '</td>';
	                                    $html .= '<td class="text-left" rowspan="' . $total_rows . '"><b>' . $tujuan_sasaran_group['label_tipe'] . '</b><br> ' . $tujuan_sasaran_group['nama_tujuan_sasaran'] . '</td>';
	                                    $tampil_data_tujuan_sasaran = false;
	                                }
	                                  
	                                if ($tampil_indikator) {
	                                    $html .= '<td class="text-left" rowspan="' . $indikator_count . '">' . $indikator_data['indikator_text'] . '</td>';
	                                    $tampil_indikator = false;
	                                }
	                                  
	                                $html .= '
	                                    <td class="text-left">' . $data_sebelum['uraian_resiko'] . '</td>
	                                    <td class="text-left">' . $data_sebelum['kode_resiko'] . '</td>
	                                    <td class="text-left">' . $pemilik_resiko . '</td>
	                                    <td class="text-left">' . $data_sebelum['uraian_sebab'] . '</td>
	                                    <td class="text-left">' . $sumber_sebab . '</td>
	                                    <td class="text-left">' . $controllable . '</td>
	                                    <td class="text-left">' . $data_sebelum['uraian_dampak'] . '</td>
	                                    <td class="text-left">' . $pihak_terkena . '</td>
	                                    <td class="text-left">' . $data_sebelum['skala_dampak'] . '</td>
	                                    <td class="text-left">' . $data_sebelum['skala_kemungkinan'] . '</td>
	                                    <td class="text-left">' . $nilai_resiko . '</td>
	                                    <td class="text-left">' . $data_sebelum['rencana_tindak_pengendalian'] . '</td>
	                                ';
	                                
	                                if ($tampil_data_tujuan_sasaran_sesudah) {
	                                    $html .= '<td class="text-left" rowspan="' . $total_rows . '"><b>' . $tujuan_sasaran_group['label_tipe'] . '</b><br>' . $tujuan_sasaran_group['nama_tujuan_sasaran'] . '</td>';
	                                    $tampil_data_tujuan_sasaran_sesudah = false;
	                                }
	                                  
	                                if ($tampil_indikator_sesudah) {
	                                    $html .= '<td class="text-left" rowspan="' . $indikator_count . '">' . $indikator_data['indikator_text'] . '</td>';
	                                    $tampil_indikator_sesudah = false;
	                                }
	                                  
	                                if (!empty($get_data_sesudah)) {
	                                    foreach ($get_data_sesudah as $data_sesudah) {
	                                        $id_sesudah = $data_sesudah['id'];
	                                        $tipe_sesudah = intval($data_sesudah['tipe']);
	                                        $id_tujuan_sesudah = $data_sesudah['id_tujuan_sasaran'];
	                                        $id_indikator_sesudah = $data_sesudah['id_indikator'];
	                                        
	                                        $controllable_sesudah = '';
	                                        $pemilik_resiko_sesudah = '';
	                                        $sumber_sebab_sesudah = '';
	                                        $pihak_terkena_sesudah = '';
	                                        $nilai_resiko_sesudah = '';

	                                        if ($data_sesudah['controllable'] == 0) {
	                                            $controllable_sesudah = 'Controllable';
	                                        } elseif ($data_sesudah['controllable'] == 1) {
	                                            $controllable_sesudah = 'Uncontrollable';
	                                        }

	                                        if ($data_sesudah['pemilik_resiko'] == 'kepala_daerah') {
	                                            $pemilik_resiko_sesudah = 'Kepala Daerah';
	                                        } elseif ($data_sesudah['pemilik_resiko'] == 'kepala_opd') {
	                                            $pemilik_resiko_sesudah = 'Kepala OPD';
	                                        } elseif ($data_sesudah['pemilik_resiko'] == 'kepala_bidang') {
	                                            $pemilik_resiko_sesudah = 'Kepala Bidang';
	                                        }

	                                        if ($data_sesudah['sumber_sebab'] == 'internal') {
	                                            $sumber_sebab_sesudah = 'Internal';
	                                        } elseif ($data_sesudah['sumber_sebab'] == 'eksternal') {
	                                            $sumber_sebab_sesudah = 'Eksternal';
	                                        } elseif ($data_sesudah['sumber_sebab'] == 'internal_eksternal') {
	                                            $sumber_sebab_sesudah = 'Internal Eksternal';
	                                        }

	                                        if ($data_sesudah['pihak_terkena'] == 'pemda') {
	                                            $pihak_terkena_sesudah = 'Pemerintah ' . $nama_pemda;
	                                        } elseif ($data_sesudah['pihak_terkena'] == 'perangkat_daerah') {
	                                            $pihak_terkena_sesudah = 'Perangkat Daerah';
	                                        } elseif ($data_sesudah['pihak_terkena'] == 'kepala_opd') {
	                                            $pihak_terkena_sesudah = 'Kepala OPD';
	                                        } elseif ($data_sesudah['pihak_terkena'] == 'pegawai_opd') {
	                                            $pihak_terkena_sesudah = 'Pegawai OPD';
	                                        } elseif ($data_sesudah['pihak_terkena'] == 'masyarakat') {
	                                            $pihak_terkena_sesudah = 'Masyarakat';
	                                        }
	          
	                                        $nilai_resiko_sesudah = $data_sesudah['skala_dampak']*$data_sesudah['skala_kemungkinan'];
	                                          
	                                        $html .= '
	                                            <td class="text-left">' . $data_sesudah['uraian_resiko'] . '</td>
	                                            <td class="text-left">' . $data_sesudah['kode_resiko'] . '</td>
	                                            <td class="text-left">' . $pemilik_resiko_sesudah . '</td>
	                                            <td class="text-left">' . $data_sesudah['uraian_sebab'] . '</td>
	                                            <td class="text-left">' . $sumber_sebab_sesudah . '</td>
	                                            <td class="text-left">' . $controllable_sesudah . '</td>
	                                            <td class="text-left">' . $data_sesudah['uraian_dampak'] . '</td>
	                                            <td class="text-left">' . $pihak_terkena_sesudah . '</td>
	                                            <td class="text-left">' . $data_sesudah['skala_dampak'] . '</td>
	                                            <td class="text-left">' . $data_sesudah['skala_kemungkinan'] . '</td>
	                                            <td class="text-left">' . $nilai_resiko_sesudah . '</td>
	                                            <td class="text-left">' . $data_sesudah['rencana_tindak_pengendalian'] . '</td>
	                                        ';
	                                          
	                                        $data_sesudah = true;
	                                    }
	                                } else {
	                                    $html .= '
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left">0</td>
	                                        <td class="text-left">0</td>
	                                        <td class="text-left">0</td>
	                                        <td class="text-left"></td>
	                                    ';
	                                }

	                                $namaJadwal = '-';
	                                $mulaiJadwal = '';
	                                $selesaiJadwal = '-';

	                                $jadwal_lokal = $wpdb->get_row($wpdb->prepare('
	                                  	SELECT 
	                                    	*
	                                  	FROM data_jadwal_lokal
	                                  	WHERE tahun_anggaran = %d
	                                    	AND id_tipe = %d
	                                ', $tahun_anggaran, 20),
	                                ARRAY_A);
	                                
	                                if (empty($jadwal_lokal['id_jadwal_sakip'])) {
	                                    $id_jadwal_wp_sakip = 0;
	                                } else {
	                                    $id_jadwal_wp_sakip = $jadwal_lokal['id_jadwal_sakip'];
	                                }
	                                $tahun_anggaran = $jadwal_lokal['tahun_anggaran'];

	                                $add_renstra = '';
	                                if (!empty($jadwal_lokal)) {
	                                    $namaJadwal = $jadwal_lokal['nama'];
	                                    $jenisJadwal = $jadwal_lokal['jenis_jadwal'];

	                                    if ($jenisJadwal == 'penetapan' && in_array("administrator", $user_meta->roles)) {
	                                        $mulaiJadwal = $jadwal_lokal['waktu_awal'];
	                                        $selesaiJadwal = $jadwal_lokal['waktu_akhir'];
	                                        $awal = new DateTime($mulaiJadwal);
	                                        $akhir = new DateTime($selesaiJadwal);
	                                        $now = new DateTime(date('Y-m-d H:i:s'));

	                                        if ($now >= $awal && $now <= $akhir) {
	                                            $html .= '
	                                                <td class="text-center">
	                                                    <button class="btn btn-success" onclick="verif_tujuan_sasaran_manrisk(' . $id_sesudah . ', ' . $data_sebelum['id'] . ', \'' . $id_tujuan . '\', \'' . $id_indikator . '\', ' . $tipe_sesudah . '); return false;" title="Verifikasi Data">
	                                                        <span class="dashicons dashicons-yes"></span>
	                                                    </button>
	                                                </td>
	                                            ';
	                                        } else {
	                                            $html .= '<td class="text-center"></td>';
	                                        }
	                                    } else if ($jenisJadwal == 'usulan' && !in_array("administrator", $user_meta->roles)) {
	                                        $mulaiJadwal = $jadwal_lokal['waktu_awal'];
	                                        $selesaiJadwal = $jadwal_lokal['waktu_akhir'];
	                                        $awal = new DateTime($mulaiJadwal);
	                                        $akhir = new DateTime($selesaiJadwal);
	                                        $now = new DateTime(date('Y-m-d H:i:s'));

	                                        if ($now >= $awal && $now <= $akhir) {
	                                            $html .= '
	                                                <td class="text-center">
	                                                    <button class="btn btn-success" onclick="tambah_tujuan_sasaran_manrisk( \'' . $id_tujuan . '\', \'' . $id_indikator . '\', \'' . $tujuan_sasaran_group['nama_tujuan_sasaran'] . '\', \'' . $indikator_data['indikator_text'] . '\', ' . $tipe_target . '); return false;" title="Tambah Data Manrisk">
	                                                        <span class="dashicons dashicons-plus"></span>
	                                                    </button>
	                                                    <button class="btn btn-primary" onclick="edit_tujuan_sasaran_manrisk(' . $data_sebelum['id'] . ', \'' . $id_tujuan . '\', \'' . $id_indikator . '\', ' . $tipe_target . '); return false;" title="Edit Data Sebelum">
	                                                        <span class="dashicons dashicons-edit"></span>
	                                                    </button>';
	                                                    
	                                            if ($indikator_count > 1) {
	                                                $html .= '
	                                                    <button class="btn btn-danger" onclick="hapus_tujuan_sasaran_manrisk(' . $data_sebelum['id'] . '); return false;" title="Hapus Data Sebelum">
	                                                        <span class="dashicons dashicons-trash"></span>
	                                                    </button>';
	                                            }
	                                            
	                                            $html .= '</td>';
	                                        } else {
	                                              $html .= '<td class="text-center"></td>';
	                                        }
	                                    } else {
	                                        $html .= '<td class="text-center"></td>';
	                                    }
	                                } else {
	                                    $html .= '<td class="text-center"></td>';
	                                }

	                                $html .= '</tr>';
	                            }
	                        }
	                    }
	                }
	                $ret['data_sesudah'] = $data_sesudah;
	            } else {
	                $html = '<tr><td class="text-center" colspan="30">Data masih kosong!</td></tr>';
	                $ret['data_sesudah'] = false;
	            }
	              $ret['data'] = $html;
	        } else {
	            $ret = array(
	                'status' => 'error',
	                'message'   => 'Api Key tidak sesuai!'
	            );
	        }
	    } else {
	        $ret = array(
	            'status' => 'error',
	            'message'   => 'Format tidak sesuai!'
	        );
	    }
	    die(json_encode($ret));
	}

	public function get_data_renstra_manrisk($tahun_anggaran, $id_skpd, $id_jadwal, $tipe_renstra)
	{
	    global $wpdb;
	    
	    if ($tipe_renstra == 'tujuan_sasaran') {
	        $data_tujuan = $wpdb->get_results($wpdb->prepare("
	            SELECT 
	                id, 
	                id_unik, 
	                tujuan_teks,
	                active
	            FROM data_renstra_tujuan 
	            WHERE id_unik_indikator IS NULL 
	                AND id_jadwal = %d 
	                AND id_unit = %d 
	        ", $id_jadwal, $id_skpd), ARRAY_A);

	        $data_sasaran = $wpdb->get_results($wpdb->prepare("
	            SELECT 
	                id, 
	                id_unik, 
	                sasaran_teks,
	                active
	            FROM data_renstra_sasaran 
	            WHERE id_unik_indikator IS NULL 
	                AND id_jadwal = %d 
	                AND id_unit = %d 
	        ", $id_jadwal, $id_skpd), ARRAY_A);

	        foreach ($data_tujuan as $tujuan) {
	            $this->get_data_manrisk($tujuan, 0, $tahun_anggaran, $id_skpd, $id_jadwal, 'data_renstra_tujuan', $tipe_renstra);
	        }

	        foreach ($data_sasaran as $sasaran) {
	            $this->get_data_manrisk($sasaran, 1, $tahun_anggaran, $id_skpd, $id_jadwal, 'data_renstra_sasaran', $tipe_renstra);
	        }
	    } else if ($tipe_renstra == 'program_kegiatan') {
	        $data_program = $wpdb->get_results($wpdb->prepare("
	            SELECT 
	                kode_program,
	                nama_program,
	                kode_sbl,
	                active
	            FROM data_sub_keg_bl 
	            WHERE id_sub_skpd=%d
	              AND active=1 
	              AND tahun_anggaran=%d 
	            GROUP BY kode_program, id_sub_skpd  
	            ORDER BY kode_program
	        ", $id_skpd, $tahun_anggaran), ARRAY_A);

	        foreach ($data_program as $program) {
	            $this->get_data_manrisk($program, 0, $tahun_anggaran, $id_skpd, $id_jadwal, 'data_sub_keg_bl', $tipe_renstra);
	        }

	        $data_kegiatan = $wpdb->get_results($wpdb->prepare("
	            SELECT 
	                kode_giat,
	                nama_giat,
	                kode_sbl,
	                active
	            FROM data_sub_keg_bl 
	            WHERE id_sub_skpd=%d
	              AND active=1 
	              AND tahun_anggaran=%d 
	            ORDER BY kode_giat
	        ", $id_skpd, $tahun_anggaran), ARRAY_A);

	        foreach ($data_kegiatan as $kegiatan) {
	            $this->get_data_manrisk($kegiatan, 1, $tahun_anggaran, $id_skpd, $id_jadwal, 'data_sub_keg_bl', $tipe_renstra);
	        }

	        $data_sub_kegiatan = $wpdb->get_results($wpdb->prepare("
	            SELECT 
	                kode_sub_giat,
	                nama_sub_giat,
	                kode_sbl,
	                active
	            FROM data_sub_keg_bl 
	            WHERE id_sub_skpd=%d
	              AND active=1 
	              AND tahun_anggaran=%d 
	            ORDER BY kode_sbl
	        ", $id_skpd, $tahun_anggaran), ARRAY_A);

	        foreach ($data_sub_kegiatan as $sub_kegiatan) {
	            $this->get_data_manrisk($sub_kegiatan, 2, $tahun_anggaran, $id_skpd, $id_jadwal, 'data_sub_keg_bl', $tipe_renstra);
	        }
	    }
	}

  	public function get_data_manrisk($data, $tipe, $tahun_anggaran, $id_skpd, $id_jadwal, $table_name, $tipe_renstra)
	{
	    global $wpdb;
	    
	    if ($tipe_renstra == 'tujuan_sasaran') {
	        $indikator_data = $wpdb->get_results($wpdb->prepare("
	            SELECT 
	            	id_unik_indikator 
	            FROM {$table_name}
	            WHERE id_unik = %s 
	              AND id_unik_indikator IS NOT NULL 
	              AND active = 1
	        ", $data['id_unik']), ARRAY_A);

	        $indikator_list = !empty($indikator_data) ? array_column($indikator_data, 'id_unik_indikator') : array(0);
	        
	        foreach ($indikator_list as $id_indikator) {
	            $existing_data_sebelum = $wpdb->get_row($wpdb->prepare("
	                SELECT 
	                	id, 
	                	active
	                FROM data_tujuan_sasaran_manrisk_sebelum 
	                WHERE id_tujuan_sasaran = %s 
	                  AND id_indikator = %s 
	                  AND tipe = %d 
	                  AND tahun_anggaran = %d 
	                  AND id_skpd = %d
	            ", $data['id_unik'], $id_indikator, $tipe, $tahun_anggaran, $id_skpd), ARRAY_A);

	            if ($data['active'] == 1) {
	                if (empty($existing_data_sebelum)) {
	                    $wpdb->insert(
	                        'data_tujuan_sasaran_manrisk_sebelum',
	                        array(
	                            'id_tujuan_sasaran' => $data['id_unik'],
	                            'id_indikator'      => $id_indikator,
	                            'tipe'              => $tipe,
	                            'controllable'      => 2,
	                            'tahun_anggaran'    => $tahun_anggaran,
	                            'id_skpd'           => $id_skpd,
	                            'active'            => 1,
	                            'created_at'        => current_time('mysql')
	                        )
	                    );
	                    $id_sebelum = $wpdb->insert_id;
	                } else {
	                    $id_sebelum = $existing_data_sebelum['id'];
	                }

	                $this->get_data_sesudah($id_sebelum, $data, $id_indikator, $tipe, $tahun_anggaran, $id_skpd, $table_name, $tipe_renstra);

	            } else {
	                if (!empty($existing_data_sebelum)) {
	                    $wpdb->update(
	                        'data_tujuan_sasaran_manrisk_sebelum',
	                        array('active' => 0),
	                        array('id' => $existing_data_sebelum['id']),
	                        array('%d'),
	                        array('%d')
	                    );

	                    $wpdb->update(
	                        'data_tujuan_sasaran_manrisk_sesudah',
	                        array('active' => 0),
	                        array('id_sebelum' => $existing_data_sebelum['id']),
	                        array('%d'),
	                        array('%d')
	                    );
	                }
	            }
	        }
	    } else if ($tipe_renstra == 'program_kegiatan') {
	        if ($tipe == 0) {
	            $master_data_ind = $wpdb->get_results($wpdb->prepare("
	                SELECT  
	                    SUBSTRING_INDEX(kode_sbl, '.', 3) AS kode_sbl,
	                    satuancapaian,
	                    targetcapaianteks,
	                    capaianteks,
	                    targetcapaian 
	                FROM data_capaian_prog_sub_keg 
	                WHERE tahun_anggaran = %d  
	                  AND active = 1  
	                  AND capaianteks != ''  
	                  AND kode_sbl LIKE %s 
	                ORDER BY id ASC
	            ", $tahun_anggaran, $data['kode_sbl'].'%'), ARRAY_A);

	        } else if ($tipe == 1) {       
	            $master_data_ind = $wpdb->get_results($wpdb->prepare("
	                SELECT  
	                    SUBSTRING_INDEX(kode_sbl, '.', 4) AS kode_sbl,
	                    outputteks as capaianteks,
	                    satuanoutput as satuancapaian,
	                    targetoutput as targetcapaian,
	                    targetoutputteks as targetcapaianteks 
	                FROM data_output_giat_sub_keg 
	                WHERE tahun_anggaran = %d  
	                  AND active = 1  
	                  AND outputteks != ''  
	                  AND kode_sbl LIKE %s 
	                ORDER BY id ASC
	            ", $tahun_anggaran, $data['kode_sbl'].'%'), ARRAY_A);

	        } else if ($tipe == 2) {     
	            $master_data_ind = $wpdb->get_results($wpdb->prepare("
	                SELECT  
	                    SUBSTRING_INDEX(kode_sbl, '.', 5) AS kode_sbl,
	                    outputteks as capaianteks,
	                    targetoutput as targetcapaian,
	                    satuanoutput as satuancapaian,
	                    targetoutputteks as targetcapaianteks
	                FROM data_sub_keg_indikator 
	                WHERE tahun_anggaran = %d  
	                  AND active = 1  
	                  AND outputteks != ''  
	                  AND kode_sbl LIKE %s 
	                ORDER BY id ASC
	            ", $tahun_anggaran, $data['kode_sbl'].'%'), ARRAY_A);
	        }

	        if ($tipe == 0) {
	            $id_program_kegiatan = $data['kode_program'];
	        } else if ($tipe == 1) {
	            $id_program_kegiatan = $data['kode_giat'];
	        } else if ($tipe == 2) {
	            $id_program_kegiatan = $data['kode_sub_giat'];
	        }

	        $existing_manrisk_data = $wpdb->get_results($wpdb->prepare("
	            SELECT 
	                id_indikator,
	                satuan_capaian,
	                target_capaian_teks,
	                capaian_teks,
	                target_capaian
	            FROM data_program_kegiatan_manrisk_sebelum
	            WHERE id_program_kegiatan = %s
	              AND tipe = %d
	              AND tahun_anggaran = %d
	              AND id_skpd = %d
	              AND active = 1
	            ORDER BY id_indikator ASC
	        ", $id_program_kegiatan, $tipe, $tahun_anggaran, $id_skpd), ARRAY_A);

	        // Cek data master dengan manrisk
	        // Cek jumlah apakah sama
	        $master_count = count($master_data_ind);
	        $manrisk_count = count($existing_manrisk_data);
	        
	        $cek_data = false;
	        
	        if ($master_count != $manrisk_count) {
	            // Jika jumlah tidak sama
	            $cek_data = true;
	        } else {
	            // Jika jumlah sama, cek per indikator apakah teks nya sama
	            $grouped_master = array();
	            foreach ($master_data_ind as $index => $master_item) {
	                $key = $index + 1;
	                if ($master_count > 1) {
	                    $kode_indikator = $master_item['kode_sbl'] . '.' . $key;
	                } else {
	                    $kode_indikator = $master_item['kode_sbl'];
	                }
	                $grouped_master[$kode_indikator] = $master_item;
	            }
	            
	            // Bandingkan data master dengan manrisk
	            foreach ($existing_manrisk_data as $manrisk_item) {
	                $kode_indikator = $manrisk_item['id_indikator'];
	                
	                if (!isset($grouped_master[$kode_indikator])) {
	                    $cek_data = true;
	                    break;
	                }
	                
	                $master_item = $grouped_master[$kode_indikator];
	                
	                // Cek apakah data berbeda
	                if ($master_item['satuancapaian'] != $manrisk_item['satuan_capaian'] ||
	                    $master_item['targetcapaianteks'] != $manrisk_item['target_capaian_teks'] ||
	                    $master_item['capaianteks'] != $manrisk_item['capaian_teks'] ||
	                    $master_item['targetcapaian'] != $manrisk_item['target_capaian']) {
	                    $cek_data = true;
	                    break;
	                }
	            }
	        }

	        // Jika perlu update atau data belum ada, hapus data lama dan insert yang baru
	        if ($cek_data || empty($existing_manrisk_data)) {
	            if (!empty($existing_manrisk_data)) {
	                $wpdb->query($wpdb->prepare("
	                    UPDATE 
	                    	data_program_kegiatan_manrisk_sebelum 
	                    SET active = 0 
	                    WHERE id_program_kegiatan = %s 
	                      AND tipe = %d 
	                      AND tahun_anggaran = %d 
	                      AND id_skpd = %d
	                ", $id_program_kegiatan, $tipe, $tahun_anggaran, $id_skpd));
	                
	                $wpdb->query($wpdb->prepare("
	                    UPDATE 
	                    	data_program_kegiatan_manrisk_sesudah s
	                    INNER JOIN data_program_kegiatan_manrisk_sebelum b ON s.id_sebelum = b.id
	                    SET s.active = 0 
	                    WHERE b.id_program_kegiatan = %s 
	                      AND b.tipe = %d 
	                      AND b.tahun_anggaran = %d 
	                      AND b.id_skpd = %d
	                ", $id_program_kegiatan, $tipe, $tahun_anggaran, $id_skpd));
	            }
	        }

	        if (!empty($master_data_ind) && $data['active'] == 1) {
	            $index = 0;
	            $total_indikator = count($master_data_ind);
	            
	            $grouped_data = array();
	            foreach ($master_data_ind as $item) {
	                $key = $item['capaianteks'];
	                if (!isset($grouped_data[$key])) {
	                    $grouped_data[$key] = $item;
	                }
	            }
	            
	            $indikator_data = array_values($grouped_data);
	            $total_indikator = count($indikator_data);
	            
	            foreach ($indikator_data as $indikator) {
	                $index++;
	                $id_indikator = $indikator['kode_sbl'];
	                if ($total_indikator > 1) {
	                    $id_indikator .= '.' . $index;
	                }

	                $existing_data_sebelum = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                    	id, 
	                    	active 
	                    FROM data_program_kegiatan_manrisk_sebelum  
	                    WHERE id_program_kegiatan = %s  
	                      AND id_indikator = %s  
	                      AND tipe = %d  
	                      AND tahun_anggaran = %d  
	                      AND id_skpd = %d
	                ", $id_program_kegiatan, $id_indikator, $tipe, $tahun_anggaran, $id_skpd), ARRAY_A);

	                if (empty($existing_data_sebelum)) {
	                    $wpdb->insert(
	                        'data_program_kegiatan_manrisk_sebelum',
	                        array(
	                            'id_program_kegiatan' => $id_program_kegiatan,
	                            'id_indikator'        => $id_indikator,
	                            'satuan_capaian'      => $indikator['satuancapaian'],
	                            'target_capaian_teks' => $indikator['targetcapaianteks'],
	                            'capaian_teks'        => $indikator['capaianteks'],
	                            'target_capaian'      => $indikator['targetcapaian'],
	                            'tipe'                => $tipe,
	                            'controllable'        => 2,
	                            'tahun_anggaran'      => $tahun_anggaran,
	                            'id_skpd'             => $id_skpd,
	                            'active'              => 1,
	                            'created_at'          => current_time('mysql')
	                        )
	                    );
	                    $id_sebelum = $wpdb->insert_id;
	                } else {
	                    $wpdb->update(
	                        'data_program_kegiatan_manrisk_sebelum',
	                        array(
	                            'satuan_capaian'      => $indikator['satuancapaian'],
	                            'target_capaian_teks' => $indikator['targetcapaianteks'],
	                            'capaian_teks'        => $indikator['capaianteks'],
	                            'target_capaian'      => $indikator['targetcapaian'],
	                            'active'              => 1
	                        ),
	                        array('id' => $existing_data_sebelum['id']),
	                        array('%s', '%s', '%s', '%s', '%d'),
	                        array('%d')
	                    );
	                    $id_sebelum = $existing_data_sebelum['id'];
	                }
	                
	                $this->get_data_sesudah($id_sebelum, $data, $id_indikator, $tipe, $tahun_anggaran, $id_skpd, $table_name, $tipe_renstra, $indikator['satuancapaian'], $indikator['targetcapaianteks'], $indikator['capaianteks'], $indikator['targetcapaian']);
	            }
	        } else if ($data['active'] == 0) {
	            $wpdb->query($wpdb->prepare("
	                UPDATE 
	                	data_program_kegiatan_manrisk_sebelum 
	                SET active = 0 
	                WHERE id_program_kegiatan = %s 
	                  AND tipe = %d 
	                  AND tahun_anggaran = %d 
	                  AND id_skpd = %d
	            ", $id_program_kegiatan, $tipe, $tahun_anggaran, $id_skpd));
	            
	            $wpdb->query($wpdb->prepare("
	                UPDATE 
	                	data_program_kegiatan_manrisk_sesudah s
	                INNER JOIN data_program_kegiatan_manrisk_sebelum b ON s.id_sebelum = b.id
	                SET s.active = 0 
	                WHERE b.id_program_kegiatan = %s 
	                  AND b.tipe = %d 
	                  AND b.tahun_anggaran = %d 
	                  AND b.id_skpd = %d
	            ", $id_program_kegiatan, $tipe, $tahun_anggaran, $id_skpd));
	        }
	    }
	}

	public function get_data_sesudah($id_sebelum, $data, $id_indikator, $tipe, $tahun_anggaran, $id_skpd, $table_name, $tipe_renstra, $satuan_capaian = '', $target_capaian_teks = '', $capaian_teks = '', $target_capaian = '')
	{
	    global $wpdb;
	    
	    if ($tipe_renstra == 'tujuan_sasaran') {
	        if ($tipe == 0) { 
	            $text_field = 'tujuan_teks';
	            $data_text = $data['tujuan_teks'];
	        } else { 
	            $text_field = 'sasaran_teks';
	            $data_text = $data['sasaran_teks'];
	        }
	        
	        $indikator_text = '';
	        if ($id_indikator > 0) {
	            $indikator_text = $wpdb->get_var($wpdb->prepare("
	                SELECT 
	                    indikator_teks 
	                FROM {$table_name}
	                WHERE id = %d 
	                    AND active = 1
	            ", $id_indikator));
	        }

	        $existing_data_sesudah = $wpdb->get_row($wpdb->prepare("
	            SELECT 
	                id, 
	                active 
	            FROM data_tujuan_sasaran_manrisk_sesudah 
	            WHERE id_sebelum = %d 
	                AND id_tujuan_sasaran = %s 
	                AND id_indikator = %s 
	                AND tipe = %d 
	                AND tahun_anggaran = %d 
	                AND id_skpd = %d
	        ", $id_sebelum, $data['id_unik'], $id_indikator, $tipe, $tahun_anggaran, $id_skpd), ARRAY_A);

	        if (empty($existing_data_sesudah)) {
	            $wpdb->insert(
	                'data_tujuan_sasaran_manrisk_sesudah',
	                array(
	                    'id_sebelum' => $id_sebelum,
	                    'id_tujuan_sasaran' => $data['id_unik'],
	                    'id_indikator' => $id_indikator,
	                    'tipe' => $tipe,
	                    'controllable' => 2,
	                    'tahun_anggaran' => $tahun_anggaran,
	                    'id_skpd' => $id_skpd,
	                    'active' => 1,
	                    'created_at' => current_time('mysql')
	                )
	            );
	        } else {
	            $update_data = array(
	                'id_tujuan_sasaran' => $data['id_unik'],
	                'id_indikator' => $id_indikator,
	                'active' => 1
	            );
	            
	            $wpdb->update(
	                'data_tujuan_sasaran_manrisk_sesudah',
	                $update_data,
	                array('id' => $existing_data_sesudah['id']),
	                array('%s', '%s', '%d'), 
	                array('%d') 
	            );
	        }
	    } else if ($tipe_renstra == 'program_kegiatan') {
	        if ($tipe == 0) {
	            $id_program_kegiatan = $data['kode_program'];
	            $text_field = 'nama_program';
	            $data_text = $data['nama_program'];
	        } else if ($tipe == 1) {
	            $id_program_kegiatan = $data['kode_giat'];
	            $text_field = 'nama_giat';
	            $data_text = $data['nama_giat'];
	        } else if ($tipe == 2) {
	            $id_program_kegiatan = $data['kode_sub_giat'];
	            $text_field = 'nama_sub_giat';
	            $data_text = $data['nama_sub_giat'];
	        }

	        $kode_indikator = $id_indikator;

	        $existing_data_sesudah = $wpdb->get_row($wpdb->prepare("
	            SELECT 
	            	id, 
	            	active  
	            FROM data_program_kegiatan_manrisk_sesudah  
	            WHERE id_sebelum = %d  
	              AND id_program_kegiatan = %s  
	              AND id_indikator = %s  
	              AND tipe = %d  
	              AND tahun_anggaran = %d  
	              AND id_skpd = %d
	        ", $id_sebelum, $id_program_kegiatan, $kode_indikator, $tipe, $tahun_anggaran, $id_skpd), ARRAY_A);

	        if (empty($existing_data_sesudah)) {
	            $wpdb->insert(
	                'data_program_kegiatan_manrisk_sesudah',
	                array(
	                    'id_sebelum'         => $id_sebelum,
	                    'id_program_kegiatan'=> $id_program_kegiatan,
	                    'id_indikator'       => $kode_indikator, 
	                    'satuan_capaian'     => $satuan_capaian,
	                    'target_capaian_teks'=> $target_capaian_teks,
	                    'capaian_teks'       => $capaian_teks,
	                    'target_capaian'     => $target_capaian,
	                    'tipe'               => $tipe,
	                    'controllable'       => 2,
	                    'tahun_anggaran'     => $tahun_anggaran,
	                    'id_skpd'            => $id_skpd,
	                    'active'             => 1,
	                    'created_at'         => current_time('mysql')
	                )
	            );
	        } else {
	            $update_data = array(
	                'id_program_kegiatan' => $id_program_kegiatan,
	                'id_indikator'        => $kode_indikator,
	                'satuan_capaian'      => $satuan_capaian,
	                'target_capaian_teks' => $target_capaian_teks,
	                'capaian_teks'        => $capaian_teks,
	                'target_capaian'      => $target_capaian,
	                'active'              => 1
	            );

	            $wpdb->update(
	                'data_program_kegiatan_manrisk_sesudah',
	                $update_data,
	                array('id' => $existing_data_sesudah['id']),
	                array('%s', '%s', '%s', '%s', '%s', '%s', '%d'),
	                array('%d')
	            );
	        }
	    }
	}

  	public function submit_tujuan_sasaran()
  	{
      	global $wpdb;
      	$ret = array(
          	'status' => 'success',
          	'message' => 'Berhasil simpan data!'
      	);

      	if (!empty($_POST)) {
          	if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

              	if (!empty($_POST['tahun_anggaran'])) {
          			$tahun_anggaran = $_POST['tahun_anggaran'];
        		} else {
          			$ret['status'] = 'error';
          			$ret['message'] = 'Tahun Anggaran kosong!';
        		}
			    if (!empty($_POST['id_skpd'])) {
			      $id_skpd = $_POST['id_skpd'];
			    } else {
			      $ret['status'] = 'error';
			      $ret['message'] = 'ID SKPD kosong!';
			    }
              	$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
              	$skala_dampak = isset($_POST['skala_dampak']) ? intval($_POST['skala_dampak']) : 0;
              	$skala_kemungkinan = isset($_POST['skala_kemungkinan']) ? intval($_POST['skala_kemungkinan']) : 0;
              	$controllable_status = isset($_POST['controllable_status']) ? intval($_POST['controllable_status']) : 2;

              	$data = array(
                  	'id_tujuan_sasaran'       		=> $_POST['id_tujuan_sasaran'],
                  	'id_indikator'          		=> $_POST['id_indikator'],
                  	'tipe'              			=> $_POST['tipe'],
                  	'uraian_resiko'         		=> $_POST['uraian_resiko'],
                  	'kode_resiko'         			=> $_POST['kode_resiko'],
                  	'pemilik_resiko'        		=> $_POST['pemilik_resiko'],
                  	'uraian_sebab'          		=> $_POST['uraian_sebab'],
                  	'sumber_sebab'          		=> $_POST['sumber_sebab'],
                  	'controllable'          		=> $controllable_status, 
                  	'uraian_dampak'         		=> $_POST['uraian_dampak'],
                  	'pihak_terkena'         		=> $_POST['pihak_terkena'],
                  	'skala_dampak'          		=> $skala_dampak,
                  	'skala_kemungkinan'      		=> $skala_kemungkinan,
                  	'rencana_tindak_pengendalian'	=> $_POST['rencana_tindak_pengendalian'],
                  	'id_skpd'           			=> $id_skpd,
                  	'tahun_anggaran'        		=> $tahun_anggaran,
                  	'active'            			=> 1
              );

	            if ($id <= 0) {
	          		$wpdb->insert('data_tujuan_sasaran_manrisk_sebelum',
	                    $data
	                );
	        	} else {
	          		$wpdb->update('data_tujuan_sasaran_manrisk_sebelum',
	                    $data, 
	                    array('id' => $id)
	                );
	        	}

	        } else {
	            $ret['status']  = 'error';
	            $ret['message'] = 'API key tidak ditemukan!';
	        }
	    } else {
	          $ret['status']  = 'error';
	          $ret['message'] = 'Format salah!';
	    }

	    wp_send_json($ret);
	}

  	public function edit_tujuan_sasaran_manrisk(){
      	global $wpdb;
      	$ret = array(
          	'status' => 'success',
          	'message' => 'Berhasil ambil data!',
          	'data' => array()
      	);

      	if (!empty($_POST)) {
          	if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

              	if (!empty($_POST['tahun_anggaran'])) {
                  	$tahun_anggaran = intval($_POST['tahun_anggaran']);
              	} else {
                  	$ret['status'] = 'error';
                  	$ret['message'] = 'Tahun Anggaran kosong!';
                  	wp_send_json($ret);
              	}

              	if (!empty($_POST['id_skpd'])) {
                  	$id_skpd = intval($_POST['id_skpd']);
              	} else {
                  	$ret['status'] = 'error';
                  	$ret['message'] = 'ID SKPD kosong!';
                  	wp_send_json($ret);
              	}

              	if (!empty($_POST['id'])) {
                  	$id = intval($_POST['id']);
              	} else {
                  	$ret['status'] = 'error';
                  	$ret['message'] = 'ID kosong!';
                  	wp_send_json($ret);
              	}

              	if (isset($_POST['tipe']) && is_numeric($_POST['tipe'])) {
            		$tipe = intval($_POST['tipe']);
			    } else {
			        $ret['status'] = 'error';
			        $ret['message'] = 'Tipe kosong atau bukan angka!';
			        wp_send_json($ret);
			    }

              $get_data = $wpdb->get_row($wpdb->prepare("
                    SELECT * 
                    FROM data_tujuan_sasaran_manrisk_sebelum
                    WHERE id = %d
                      AND id_tujuan_sasaran = %s
                      AND id_indikator = %s
                      AND id_skpd = %d
                      AND tahun_anggaran = %d
                      AND tipe = %d
                      AND active = 1
                    LIMIT 1
                ", $id, $_POST['id_tujuan_sasaran'], $_POST['id_indikator'], $id_skpd, $tahun_anggaran, $tipe),ARRAY_A);

              	$nama_tujuan_sasaran = '';
              	$indikator_teks = '';

              	if ($tipe == 0) {
                  	$get_tujuan = $wpdb->get_row($wpdb->prepare("
                      	SELECT 
                        	tujuan_teks 
                      	FROM data_renstra_tujuan
                      	WHERE id_unik = %s
                        	AND id_unit = %d
                        	AND id_jadwal = %d
                        	AND active = 1
                      	LIMIT 1
                  	", $get_data['id_tujuan_sasaran'], $id_skpd, $_POST['id_jadwal']), ARRAY_A);

                  if ($get_tujuan) {
                      $nama_tujuan_sasaran = $get_tujuan['tujuan_teks'];
                  }

                  	$get_indikator = $wpdb->get_row($wpdb->prepare("
                      	SELECT 
                        	indikator_teks 
                      FROM data_renstra_tujuan
                      WHERE id_unik = %s
                        	AND id_unik_indikator = %s
                        	AND active = 1
                  ", $get_data['id_tujuan_sasaran'], $get_data['id_indikator']), ARRAY_A);

                  if ($get_indikator) {
                      $indikator_teks = $get_indikator['indikator_teks'];
                  }
              } elseif ($tipe == 1) {
                  	$get_sasaran = $wpdb->get_row($wpdb->prepare("
                      	SELECT 
                        	sasaran_teks 
                      	FROM data_renstra_sasaran
                      	WHERE id_unik = %s
                        	AND id_unit = %d
                        	AND id_jadwal = %d
                        	AND active = 1
                      	LIMIT 1
                  	", $get_data['id_tujuan_sasaran'], $id_skpd, $_POST['id_jadwal']), ARRAY_A);

                  	if ($get_sasaran) {
                      	$nama_tujuan_sasaran = $get_sasaran['sasaran_teks'];
                  	}

                  	$get_indikator = $wpdb->get_row($wpdb->prepare("
                      	SELECT 
                        	indikator_teks 
                      	FROM data_renstra_sasaran
                      	WHERE id_unik = %s
                        	AND id_unik_indikator = %s
                        	AND active = 1
                  	", $get_data['id_tujuan_sasaran'], $get_data['id_indikator']), ARRAY_A);

                  	if ($get_indikator) {
                      	$indikator_teks = $get_indikator['indikator_teks'];
                  	}
              	}

              	$get_data['nama_tujuan_sasaran'] = $nama_tujuan_sasaran;
              	$get_data['indikator_teks'] = $indikator_teks;

              	$ret['data'] = $get_data;

              	if (empty($get_data)) {
                  	$ret['status'] = 'error';
                  	$ret['message'] = 'Data tidak ditemukan!';
              	}

          	} else {
              	$ret['status']  = 'error';
              	$ret['message'] = 'API key tidak ditemukan!';
          	}
      	} else {
          	$ret['status']  = 'error';
          	$ret['message'] = 'Format salah!';
      	}

      	wp_send_json($ret);
  	}

  	public function verif_tujuan_sasaran_manrisk()
  	{
    	global $wpdb;
      	$ret = array(
          	'status' => 'success',
          	'message' => 'Berhasil ambil data!',
          	'data' => array()
      	);

      	if (!empty($_POST)) {
          	if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

            	if (!empty($_POST['tahun_anggaran'])) {
                	$tahun_anggaran = intval($_POST['tahun_anggaran']);
              	} else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'Tahun Anggaran kosong!';
	                wp_send_json($ret);
              	}

              	if (!empty($_POST['id_skpd'])) {
                	$id_skpd = intval($_POST['id_skpd']);
              	} else {
                 	$ret['status'] = 'error';
                 	$ret['message'] = 'ID SKPD kosong!';
                 	wp_send_json($ret);
             	}

             	if (isset($_POST['tipe']) && is_numeric($_POST['tipe'])) {
               	 	$tipe = intval($_POST['tipe']);
             	} else {
                 	$ret['status'] = 'error';
                 	$ret['message'] = 'Tipe kosong atau bukan angka!';
                 	wp_send_json($ret);
             	}

				$get_data = null;
				$id_tujuan_sasaran = '';
				$id_indikator = '';

				if (!empty($_POST['id'])) {
                  	$get_data = $wpdb->get_row($wpdb->prepare("
                        SELECT 
                          * 
                        FROM data_tujuan_sasaran_manrisk_sesudah
                        WHERE id = %d
                          AND id_sebelum = %d
                          AND id_tujuan_sasaran = %s
                          AND id_indikator = %s
                          AND id_skpd = %d
                          AND tahun_anggaran = %d
                          AND tipe = %d
                          AND active = 1
                        LIMIT 1
                    ", $_POST['id'], $_POST['id_sebelum'], $_POST['id_tujuan_sasaran'], $_POST['id_indikator'], $id_skpd, $tahun_anggaran, $tipe),ARRAY_A);

                  	if ($get_data) {
                     	$id_tujuan_sasaran = $get_data['id_tujuan_sasaran'];
                     	$id_indikator = $get_data['id_indikator'];
                 	}
              	} else {
                 	if (!empty($_POST['id_tujuan_sasaran'])) {
                     	$id_tujuan_sasaran = $_POST['id_tujuan_sasaran'];
                 	} else {
                     	$ret['status'] = 'error';
                     	$ret['message'] = 'ID Tujuan Sasaran kosong!';
                     	wp_send_json($ret);
                 	}

                 	if (!empty($_POST['id_indikator'])) {
                     	$id_indikator = $_POST['id_indikator'];
                 	} else {
                   	 	$ret['status'] = 'error';
                     	$ret['message'] = 'ID Indikator kosong!';
                     	wp_send_json($ret);
                 	}
             	}

             	$nama_tujuan_sasaran = '';
             	$indikator_teks = '';

             	if ($tipe == 0) {
                 	$get_tujuan = $wpdb->get_row($wpdb->prepare("
                      SELECT 
                        tujuan_teks 
                      FROM data_renstra_tujuan
                      WHERE id_unik = %s
                        AND id_unit = %d
                        AND id_jadwal = %d
                        AND active = 1
                      LIMIT 1
                  ", $id_tujuan_sasaran, $id_skpd, $_POST['id_jadwal']), ARRAY_A);

                  	if ($get_tujuan) {
                      	$nama_tujuan_sasaran = $get_tujuan['tujuan_teks'];
                  	}

                  	$get_indikator = $wpdb->get_row($wpdb->prepare("
                      	SELECT 
                        	indikator_teks 
                      	FROM data_renstra_tujuan
                      	WHERE id_unik = %s
                        	AND id_unik_indikator = %s
                        	AND active = 1
                  	", $id_tujuan_sasaran, $id_indikator), ARRAY_A);

                  	if ($get_indikator) {
                      	$indikator_teks = $get_indikator['indikator_teks'];
                  	}
              	} elseif ($tipe == 1) {
                  	$get_sasaran = $wpdb->get_row($wpdb->prepare("
                      	SELECT 
                        	sasaran_teks 
                      	FROM data_renstra_sasaran
                      	WHERE id_unik = %s
                        	AND id_unit = %d
                        	AND id_jadwal = %d
                        	AND active = 1
                      	LIMIT 1
                  	", $id_tujuan_sasaran, $id_skpd, $_POST['id_jadwal']), ARRAY_A);

                  	if ($get_sasaran) {
                      	$nama_tujuan_sasaran = $get_sasaran['sasaran_teks'];
                  	}

                  	$get_indikator = $wpdb->get_row($wpdb->prepare("
                      	SELECT 
                        	indikator_teks 
                      	FROM data_renstra_sasaran
                      	WHERE id_unik = %s
                        	AND id_unik_indikator = %s
                        	AND active = 1
                  	", $id_tujuan_sasaran, $id_indikator), ARRAY_A);

                  	if ($get_indikator) {
                      	$indikator_teks = $get_indikator['indikator_teks'];
                  	}
              	}

              	$get_data_sebelum = null;
              	if (!empty($_POST['id_sebelum'])) {
                  	$get_data_sebelum = $wpdb->get_row(
                      	$wpdb->prepare("
                          	SELECT 
                            	* 
                          	FROM data_tujuan_sasaran_manrisk_sebelum
                          	WHERE id = %d
                            	AND id_skpd = %d
                            	AND tahun_anggaran = %d
                            	AND active = 1
                          	LIMIT 1
                      	", $_POST['id_sebelum'], $id_skpd, $tahun_anggaran),
                      	ARRAY_A
                  	);
              	}
              
              	$ret['data_sebelum'] = $get_data_sebelum;

              	if (is_null($get_data)) {
                  	$get_data = array(
                      	'id_tujuan_sasaran' => $id_tujuan_sasaran,
                      	'id_indikator' => $id_indikator
                  	);
              	}
              
              	$get_data['nama_tujuan_sasaran'] = $nama_tujuan_sasaran;
              	$get_data['indikator_teks'] = $indikator_teks;

              	$ret['data'] = $get_data;

              	if (!empty($_POST['id']) && empty($get_data)) {
                  	$ret['status'] = 'error';
                  	$ret['message'] = 'Data tidak ditemukan!';
              	}

          	} else {
              	$ret['status']  = 'error';
              	$ret['message'] = 'API key tidak ditemukan!';
          	}
      	} else {
          	$ret['status']  = 'error';
          	$ret['message'] = 'Format salah!';
      	}

      	wp_send_json($ret);
  	}

  	public function submit_verif_tujuan_sasaran()
  	{
	    global $wpdb;
	    $ret = array(
          	'status' => 'success',
          	'message' => 'Berhasil simpan data!'
	    );

	    if (!empty($_POST)) {
	        if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

		        if (!empty($_POST['tahun_anggaran'])) {
		          $tahun_anggaran = $_POST['tahun_anggaran'];
		        } else {
		          $ret['status'] = 'error';
		          $ret['message'] = 'Tahun Anggaran kosong!';
		        }
		        if (!empty($_POST['id_skpd'])) {
		          $id_skpd = $_POST['id_skpd'];
		        } else {
		          $ret['status'] = 'error';
		          $ret['message'] = 'ID SKPD kosong!';
		        }
	            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	            $tipe = isset($_POST['tipe']) ? intval($_POST['tipe']) : 0;
              	$skala_dampak = isset($_POST['skala_dampak']) ? intval($_POST['skala_dampak']) : 0;
              	$skala_kemungkinan = isset($_POST['skala_kemungkinan']) ? intval($_POST['skala_kemungkinan']) : 0;
              	$controllable_status = isset($_POST['controllable_status']) ? intval($_POST['controllable_status']) : 2;

	            $data = array(
	                'id_tujuan_sasaran'       		=> $_POST['id_tujuan_sasaran'],
	                'id_indikator'          		=> $_POST['id_indikator'],
	                'tipe'              			=> $_POST['tipe'],
	                'id_sebelum'          			=> $_POST['id_sebelum'], 
	                'tipe'             		 		=> $tipe,
	                'uraian_resiko'         		=> $_POST['uraian_resiko'],
	                'kode_resiko'         			=> $_POST['kode_resiko'],
	                'pemilik_resiko'        		=> $_POST['pemilik_resiko'],
	                'uraian_sebab'          		=> $_POST['uraian_sebab'],
	                'sumber_sebab'          		=> $_POST['sumber_sebab'],
	                'controllable'          		=> $controllable_status, 
	                'uraian_dampak'         		=> $_POST['uraian_dampak'],
	                'pihak_terkena'         		=> $_POST['pihak_terkena'],
	                'skala_dampak'          		=> $skala_dampak,
	                'skala_kemungkinan'       		=> $skala_kemungkinan,
	                'rencana_tindak_pengendalian' 	=> $_POST['rencana_tindak_pengendalian'],
	                'id_skpd'           			=> $id_skpd,
	                'tahun_anggaran'        		=> $tahun_anggaran,
	                'active'            			=> 1
	            );

	            if ($id <= 0) {
		          $wpdb->insert('data_tujuan_sasaran_manrisk_sesudah',
                      $data
                  );
		        } else {
		          $wpdb->update('data_tujuan_sasaran_manrisk_sesudah',
                      $data, 
                      array('id' => $id)
                  );
		        }

	        } else {
              	$ret['status']  = 'error';
              	$ret['message'] = 'API key tidak ditemukan!';
	        }
	    } else {
	        $ret['status']  = 'error';
	        $ret['message'] = 'Format salah!';
	    }

	    wp_send_json($ret);
  	}

	public function hapus_tujuan_sasaran_manrisk()
	{
	    global $wpdb;
	    $ret = array(
	        'status' => 'success',
	        'message' => 'Berhasil hapus data!'
	    );

	    if (!empty($_POST)) {
	        if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
	            if (empty($_POST['id'])) {
	                $ret['status'] = 'error';
	                $ret['message'] = 'ID kosong!';
	            } else {
	                $wpdb->update(
	                    'data_tujuan_sasaran_manrisk_sebelum',
	                    array('active' => 0),
	                    array('id' => intval($_POST['id'])),
	                    array('%d'),
	                    array('%d')
	                );

	                $wpdb->update(
	                    'data_tujuan_sasaran_manrisk_sesudah',
	                    array('active' => 0),
	                    array('id_sebelum' => intval($_POST['id'])),
	                    array('%d'),
	                    array('%d')
	                );
	            }
	        } else {
	            $ret['status'] = 'error';
	            $ret['message'] = 'API key tidak ditemukan!';
	        }
	    } else {
	        $ret['status'] = 'error';
	        $ret['message'] = 'Format salah!';
	    }

	    die(json_encode($ret));
	}

  	public function get_table_program_kegiatan()
	{
	    global $wpdb;
	    $ret = array(
	        'status' => 'success',
	        'message' => 'Berhasil get data!',
	        'data' => array()
	    );

	    if (!empty($_POST)) {
	        if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

	            if (empty($_POST['tahun_anggaran'])) {
	                $ret['status'] = 'error';
	                $ret['message'] = 'Tahun Anggaran kosong!';
	                die(json_encode($ret));
	            }
	            $tahun_anggaran = intval($_POST['tahun_anggaran']);

	            if (empty($_POST['id_skpd'])) {
	                $ret['status'] = 'error';
	                $ret['message'] = 'ID SKPD kosong!';
	                die(json_encode($ret));
	            }
	            $id_skpd = intval($_POST['id_skpd']);

	            if (empty($_POST['id_jadwal'])) {
	                $ret['status'] = 'error';
	                $ret['message'] = 'ID Jadwal kosong!';
	                die(json_encode($ret));
	            }
	            $id_jadwal = intval($_POST['id_jadwal']);

	            $this->get_data_renstra_manrisk($tahun_anggaran, $id_skpd, $id_jadwal, $_POST['tipe_renstra']);

	            $user_id = um_user('ID');
	            $user_meta = get_userdata($user_id);
	            $nama_pemda = get_option('_crb_daerah');
	            if (empty($nama_pemda) || $nama_pemda == 'false') {
	                $nama_pemda = '';
	            }
	            $get_data = $wpdb->get_results($wpdb->prepare("
	                    SELECT 
	                        * 
	                    FROM data_program_kegiatan_manrisk_sebelum
	                    WHERE tahun_anggaran = %d
	                        AND id_skpd = %d
	                        AND active = 1

	                ", $tahun_anggaran, $id_skpd),
	            ARRAY_A);

	            $html = '';
	            if (!empty($get_data)) {
	                $grouped_data = array();
	                $data_sesudah = false;
	                
	                $program_kegiatan_groups = array();
	                
	                foreach ($get_data as $row) {
	                    $kode_bidang = '';
	                    $nama_bidang = '';
	                    $nama_program_kegiatan = '';
	                    $data = $row;

	                    if (isset($row['tipe']) && $row['tipe'] == 0) {
	                        $program_kegiatan_key = $data['id_program_kegiatan'] . '_' . $data['tipe'];

	                        $get_data_program = $wpdb->get_row($wpdb->prepare("
	                            SELECT 
	                                kode_program,
	                                nama_program,
	                                kode_sbl,
	                                kode_bidang_urusan,
	                                nama_bidang_urusan
	                            FROM data_sub_keg_bl 
	                            WHERE kode_program = %s
	                                AND id_sub_skpd = %d
	                                AND tahun_anggaran = %d
	                                AND active = 1
	                            LIMIT 1
	                        ", $row['id_program_kegiatan'], $id_skpd, $tahun_anggaran),
	                        ARRAY_A);

	                        if (!empty($get_data_program)) {
	                            $nama_program_kegiatan = $get_data_program['nama_program'];
	                            $kode_bidang = $get_data_program['kode_bidang_urusan'];
	                            $nama_bidang = $get_data_program['nama_bidang_urusan'];

	                            $data['indikator'] = $row['capaian_teks'];
	                            $data['label_tipe'] = 'Program OPD : ';
	                            $data['nama_program_kegiatan'] = $nama_program_kegiatan;

	                            if (!empty($kode_bidang)) {
	                                $grouped_data[$kode_bidang]['nama_bidang'] = $nama_bidang;
	                                
	                                if (!isset($program_kegiatan_groups[$kode_bidang][$program_kegiatan_key])) {
	                                    $program_kegiatan_groups[$kode_bidang][$program_kegiatan_key] = array(
	                                        'nama_program_kegiatan' => $nama_program_kegiatan,
	                                        'label_tipe' => $data['label_tipe'],
	                                        'tipe' => $data['tipe'],
	                                        'id_program_kegiatan' => $data['id_program_kegiatan'],
	                                        'indikator' => array()
	                                    );
	                                }

	                                $indikator_key = $data['id_indikator'] . '_' . $row['capaian_teks'];
	                                if (!isset($program_kegiatan_groups[$kode_bidang][$program_kegiatan_key]['indikator'][$indikator_key])) {
	                                    $program_kegiatan_groups[$kode_bidang][$program_kegiatan_key]['indikator'][$indikator_key] = array(
	                                        'indikator_text' => $row['capaian_teks'],
	                                        'data' => array()
	                                    );
	                                }
	                                
	                                $program_kegiatan_groups[$kode_bidang][$program_kegiatan_key]['indikator'][$indikator_key]['data'][] = $data;
	                                
	                                $grouped_data[$kode_bidang]['program_kegiatan_groups'] = $program_kegiatan_groups[$kode_bidang];
	                            }
	                        }

	                    } elseif (isset($row['tipe']) && $row['tipe'] == 1) {
	                        $get_data_kegiatan = $wpdb->get_row($wpdb->prepare("
	                            SELECT 
	                                kode_giat,
	                                nama_giat,
	                                kode_sbl,
	                                kode_bidang_urusan,
	                                nama_bidang_urusan
	                            FROM data_sub_keg_bl 
	                            WHERE kode_giat = %s
	                                AND id_sub_skpd = %d
	                                AND tahun_anggaran = %d
	                                AND active = 1
	                            LIMIT 1
	                        ", $row['id_program_kegiatan'], $id_skpd, $tahun_anggaran),
	                        ARRAY_A);
	                        
	                        if (!empty($get_data_kegiatan)) {
	                            $nama_program_kegiatan = $get_data_kegiatan['nama_giat'];
	                            $kode_bidang = $get_data_kegiatan['kode_bidang_urusan'];
	                            $nama_bidang = $get_data_kegiatan['nama_bidang_urusan'];

	                            $data['indikator'] = $row['capaian_teks'];
	                            $data['label_tipe'] = 'Kegiatan OPD : ';
	                            $data['nama_program_kegiatan'] = $nama_program_kegiatan;

	                            if (!empty($kode_bidang)) {
	                                $grouped_data[$kode_bidang]['nama_bidang'] = $nama_bidang;
	                                
	                                $program_kegiatan_key = $data['id_program_kegiatan'] . '_' . $data['tipe'];
	                                
	                                if (!isset($program_kegiatan_groups[$kode_bidang][$program_kegiatan_key])) {
	                                    $program_kegiatan_groups[$kode_bidang][$program_kegiatan_key] = array(
	                                        'nama_program_kegiatan' => $nama_program_kegiatan,
	                                        'label_tipe' => $data['label_tipe'],
	                                        'tipe' => $data['tipe'],
	                                        'id_program_kegiatan' => $data['id_program_kegiatan'],
	                                        'indikator' => array()
	                                    );
	                                }
	                                
	                                $indikator_key = $data['id_indikator'] . '_' . $row['capaian_teks'];
	                                if (!isset($program_kegiatan_groups[$kode_bidang][$program_kegiatan_key]['indikator'][$indikator_key])) {
	                                    $program_kegiatan_groups[$kode_bidang][$program_kegiatan_key]['indikator'][$indikator_key] = array(
	                                        'indikator_text' => $row['capaian_teks'],
	                                        'data' => array()
	                                    );
	                                }
	                                
	                                $program_kegiatan_groups[$kode_bidang][$program_kegiatan_key]['indikator'][$indikator_key]['data'][] = $data;
	                                
	                                $grouped_data[$kode_bidang]['program_kegiatan_groups'] = $program_kegiatan_groups[$kode_bidang];
	                            }
	                        }
	                    } elseif (isset($row['tipe']) && $row['tipe'] == 2) {
	                        $get_data_sub_kegiatan = $wpdb->get_row($wpdb->prepare("
	                            SELECT 
	                                kode_sbl,
	                                nama_sub_giat,
	                                kode_bidang_urusan,
	                                nama_bidang_urusan
	                            FROM data_sub_keg_bl 
	                            WHERE kode_sub_giat = %s
	                                AND id_sub_skpd = %d
	                                AND tahun_anggaran = %d
	                                AND active = 1
	                            LIMIT 1
	                        ", $row['id_program_kegiatan'], $id_skpd, $tahun_anggaran),
	                        ARRAY_A);
	                        
	                        if (!empty($get_data_sub_kegiatan)) {
	                            $nama_program_kegiatan = $get_data_sub_kegiatan['nama_sub_giat'];
	                            $kode_bidang = $get_data_sub_kegiatan['kode_bidang_urusan'];
	                            $nama_bidang = $get_data_sub_kegiatan['nama_bidang_urusan'];

	                            $data['indikator'] = $row['capaian_teks'];
	                            $data['label_tipe'] = 'Sub Kegiatan OPD : ';
	                            $data['nama_program_kegiatan'] = $nama_program_kegiatan;

	                            if (!empty($kode_bidang)) {
	                                $grouped_data[$kode_bidang]['nama_bidang'] = $nama_bidang;
	                                
	                                $program_kegiatan_key = $data['id_program_kegiatan'] . '_' . $data['tipe'];
	                                
	                                if (!isset($program_kegiatan_groups[$kode_bidang][$program_kegiatan_key])) {
	                                    $program_kegiatan_groups[$kode_bidang][$program_kegiatan_key] = array(
	                                        'nama_program_kegiatan' => $nama_program_kegiatan,
	                                        'label_tipe' => $data['label_tipe'],
	                                        'tipe' => $data['tipe'],
	                                        'id_program_kegiatan' => $data['id_program_kegiatan'],
	                                        'indikator' => array()
	                                    );
	                                }
	                                
	                                $indikator_key = $data['id_indikator'] . '_' . $row['capaian_teks'];
	                                if (!isset($program_kegiatan_groups[$kode_bidang][$program_kegiatan_key]['indikator'][$indikator_key])) {
	                                    $program_kegiatan_groups[$kode_bidang][$program_kegiatan_key]['indikator'][$indikator_key] = array(
	                                        'indikator_text' => $row['capaian_teks'],
	                                        'data' => array()
	                                    );
	                                }
	                                
	                                $program_kegiatan_groups[$kode_bidang][$program_kegiatan_key]['indikator'][$indikator_key]['data'][] = $data;
	                                
	                                $grouped_data[$kode_bidang]['program_kegiatan_groups'] = $program_kegiatan_groups[$kode_bidang];
	                            }
	                        }
	                    }
	                }
	                
	                uksort($grouped_data, function($a, $b) {
	                    $a_clean = str_replace(',', '.', (string)$a);
	                    $b_clean = str_replace(',', '.', (string)$b);

	                    $a_is_num = (bool) preg_match('/^[0-9]+(\.[0-9]+)*$/', $a_clean);
	                    $b_is_num = (bool) preg_match('/^[0-9]+(\.[0-9]+)*$/', $b_clean);

	                    if ($a_is_num && $b_is_num) {
	                        $a_parts = explode('.', $a_clean);
	                        $b_parts = explode('.', $b_clean);
	                        $len = max(count($a_parts), count($b_parts));
	                        for ($i = 0; $i < $len; $i++) {
	                            $ap = isset($a_parts[$i]) ? intval($a_parts[$i]) : 0;
	                            $bp = isset($b_parts[$i]) ? intval($b_parts[$i]) : 0;
	                            if ($ap === $bp) {
	                                continue;
	                            }
	                            return ($ap < $bp) ? -1 : 1;
	                        }
	                        return 0;
	                    }

	                    if ($a_is_num && !$b_is_num) return -1;
	                    if (!$a_is_num && $b_is_num) return 1;

	                    return strcasecmp($a_clean, $b_clean);
	                });

	                foreach ($grouped_data as $group) {
	                    $html .= '
	                        <tr style="background:#f0f0f0; font-weight:bold;">
	                            <td colspan="30">' . $group['nama_bidang'] . '</td>
	                        </tr>
	                    ';

	                    $no = 1;
	                    
	                    uasort($group['program_kegiatan_groups'], function($a, $b) {
	                        return $a['tipe'] <=> $b['tipe'];
	                    });
	                    
	                    foreach ($group['program_kegiatan_groups'] as $program_kegiatan_group) {
	                        $tampil_data_program_kegiatan = true;
	                        $tampil_data_program_kegiatan_sesudah = true;
	                        
	                        $total_rows = 0;
	                        foreach ($program_kegiatan_group['indikator'] as $indikator_data) {
	                            $total_rows += count($indikator_data['data']);
	                        }
	                        
	                        foreach ($program_kegiatan_group['indikator'] as $indikator_id => $indikator_data) {
	                            $indikator_count = count($indikator_data['data']);
	                            
	                            foreach ($indikator_data['data'] as $index => $data_sebelum) {
	                                $controllable = '';
	                                $pemilik_resiko = '';
	                                $sumber_sebab = '';
	                                $pihak_terkena = '';
	                                $nilai_resiko = '';

	                                if ($data_sebelum['controllable'] == 0) {
	                                    $controllable = 'Controllable';
	                                } elseif ($data_sebelum['controllable'] == 1) {
	                                    $controllable = 'Uncontrollable';
	                                }

	                                $pemilik_resiko = $data_sebelum['pemilik_resiko'];
	                                $sumber_sebab = $data_sebelum['sumber_sebab'];
	                                $pihak_terkena = $data_sebelum['pihak_terkena'];
	                                $nilai_resiko = $data_sebelum['skala_dampak']*$data_sebelum['skala_kemungkinan'];
	                                $id_program = $data_sebelum['id_program_kegiatan'];
	                                $tipe_target = intval($data_sebelum['tipe']);
	                                $id_indikator = $data_sebelum['id_indikator'];

	                                $get_data_sesudah = $wpdb->get_results($wpdb->prepare("
	                                    SELECT 
	                                        * 
	                                    FROM data_program_kegiatan_manrisk_sesudah
	                                    WHERE id_sebelum = %d
	                                        AND active = 1
	                                ", $data_sebelum['id']), ARRAY_A);

	                                $id_sesudah = 0;
	                                $tipe_sesudah = 0;
	                                $id_program_sesudah = 0;
	                                $id_indikator_sesudah = 0;

	                                $html .= '<tr>';
	                                
	                                if ($tampil_data_program_kegiatan) {
	                                    $html .= '<td class="text-left" rowspan="' . $total_rows . '">' . $no++ . '</td>';
	                                    $html .= '<td class="text-left" rowspan="' . $total_rows . '"><b>' . $program_kegiatan_group['label_tipe'] . '</b>' . $program_kegiatan_group['nama_program_kegiatan'] . '</td>';
	                                    $tampil_data_program_kegiatan = false;
	                                }
	                                
	                                $html .= '<td class="text-left">' . $indikator_data['indikator_text'] . '</td>';
	                                
	                                $html .= '
	                                    <td class="text-left">' . $data_sebelum['uraian_resiko'] . '</td>
	                                    <td class="text-left">' . $data_sebelum['kode_resiko'] . '</td>
	                                    <td class="text-left">' . $pemilik_resiko . '</td>
	                                    <td class="text-left">' . $data_sebelum['uraian_sebab'] . '</td>
	                                    <td class="text-left">' . $sumber_sebab . '</td>
	                                    <td class="text-left">' . $controllable . '</td>
	                                    <td class="text-left">' . $data_sebelum['uraian_dampak'] . '</td>
	                                    <td class="text-left">' . $pihak_terkena . '</td>
	                                    <td class="text-left">' . $data_sebelum['skala_dampak'] . '</td>
	                                    <td class="text-left">' . $data_sebelum['skala_kemungkinan'] . '</td>
	                                    <td class="text-left">' . $nilai_resiko . '</td>
	                                    <td class="text-left">' . $data_sebelum['rencana_tindak_pengendalian'] . '</td>
	                                ';
	                                
	                                if ($tampil_data_program_kegiatan_sesudah) {
	                                    $html .= '<td class="text-left" rowspan="' . $total_rows . '"><b>' . $program_kegiatan_group['label_tipe'] . '</b>' . $program_kegiatan_group['nama_program_kegiatan'] . '</td>';
	                                    $tampil_data_program_kegiatan_sesudah = false;
	                                }
	                                
	                                $html .= '<td class="text-left">' . $indikator_data['indikator_text'] . '</td>';
	                                
	                                if (!empty($get_data_sesudah)) {
	                                    foreach ($get_data_sesudah as $data_sesudah) {
	                                        $id_sesudah = $data_sesudah['id'];
	                                        $tipe_sesudah = intval($data_sesudah['tipe']);
	                                        $id_program_sesudah = $data_sesudah['id_program_kegiatan'];
	                                        $id_indikator_sesudah = $data_sesudah['id_indikator'];
	                                        
	                                        $controllable_sesudah = '';
	                                        $pemilik_resiko_sesudah = '';
	                                        $sumber_sebab_sesudah = '';
	                                        $pihak_terkena_sesudah = '';
	                                        $nilai_resiko_sesudah = '';

	                                        if ($data_sesudah['controllable'] == 0) {
	                                            $controllable_sesudah = 'Controllable';
	                                        } elseif ($data_sesudah['controllable'] == 1) {
	                                            $controllable_sesudah = 'Uncontrollable';
	                                        }

	                                        $pemilik_resiko_sesudah = $data_sesudah['pemilik_resiko'];
	                                        $sumber_sebab_sesudah = $data_sesudah['sumber_sebab'];
	                                        $pihak_terkena_sesudah = $data_sesudah['pihak_terkena'];
	        
	                                        $nilai_resiko_sesudah = $data_sesudah['skala_dampak']*$data_sesudah['skala_kemungkinan'];
	                                        
	                                        $html .= '
	                                            <td class="text-left">' . $data_sesudah['uraian_resiko'] . '</td>
	                                            <td class="text-left">' . $data_sesudah['kode_resiko'] . '</td>
	                                            <td class="text-left">' . $pemilik_resiko_sesudah . '</td>
	                                            <td class="text-left">' . $data_sesudah['uraian_sebab'] . '</td>
	                                            <td class="text-left">' . $sumber_sebab_sesudah . '</td>
	                                            <td class="text-left">' . $controllable_sesudah . '</td>
	                                            <td class="text-left">' . $data_sesudah['uraian_dampak'] . '</td>
	                                            <td class="text-left">' . $pihak_terkena_sesudah . '</td>
	                                            <td class="text-left">' . $data_sesudah['skala_dampak'] . '</td>
	                                            <td class="text-left">' . $data_sesudah['skala_kemungkinan'] . '</td>
	                                            <td class="text-left">' . $nilai_resiko_sesudah . '</td>
	                                            <td class="text-left">' . $data_sesudah['rencana_tindak_pengendalian'] . '</td>
	                                        ';
	                                        
	                                        $data_sesudah = true;
	                                    }
	                                } else {
	                                    $html .= '
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left"></td>
	                                        <td class="text-left">0</td>
	                                        <td class="text-left">0</td>
	                                        <td class="text-left">0</td>
	                                        <td class="text-left"></td>
	                                    ';
	                                }

	                                $namaJadwal = '-';
	                                $mulaiJadwal = '';
	                                $selesaiJadwal = '-';

	                                $jadwal_lokal = $wpdb->get_row(
	                                  $wpdb->prepare('
	                                    SELECT 
	                                      *
	                                    FROM data_jadwal_lokal
	                                    WHERE tahun_anggaran = %d
	                                        AND id_tipe = %d
	                                  ', $tahun_anggaran, 20),
	                                  ARRAY_A
	                                );
	                                if (empty($jadwal_lokal['id_jadwal_sakip'])) {
	                                    $id_jadwal_wp_sakip = 0;
	                                } else {
	                                    $id_jadwal_wp_sakip = $jadwal_lokal['id_jadwal_sakip'];
	                                }
	                                $tahun_anggaran = $jadwal_lokal['tahun_anggaran'];

	                                $add_renstra = '';
	                                if (!empty($jadwal_lokal)) {
	                                  $namaJadwal = $jadwal_lokal['nama'];
	                                  $jenisJadwal = $jadwal_lokal['jenis_jadwal'];

	                                    if ($jenisJadwal == 'penetapan' && in_array("administrator", $user_meta->roles)) {
	                                        $mulaiJadwal = $jadwal_lokal['waktu_awal'];
	                                        $selesaiJadwal = $jadwal_lokal['waktu_akhir'];
	                                        $awal = new DateTime($mulaiJadwal);
	                                        $akhir = new DateTime($selesaiJadwal);
	                                        $now = new DateTime(date('Y-m-d H:i:s'));

	                                        if ($now >= $awal && $now <= $akhir) {
	                                          $html .= '
	                                              <td class="text-center">
	                                                  <button class="btn btn-success" onclick="verif_program_kegiatan_manrisk(' . $id_sesudah . ', ' . $data_sebelum['id'] . ', \'' . $id_program . '\', \'' . $id_indikator . '\', ' . $tipe_sesudah . '); return false;" title="Verifikasi Data">
	                                                      <span class="dashicons dashicons-yes"></span>
	                                                  </button>
	                                              </td>
	                                          ';
	                                        } else {
	                                            $html .= '<td class="text-center"></td>';
	                                        }
	                                    } else if ($jenisJadwal == 'usulan' && !in_array("administrator", $user_meta->roles)) {
	                                        $mulaiJadwal = $jadwal_lokal['waktu_awal'];
	                                        $selesaiJadwal = $jadwal_lokal['waktu_akhir'];
	                                        $awal = new DateTime($mulaiJadwal);
	                                        $akhir = new DateTime($selesaiJadwal);
	                                        $now = new DateTime(date('Y-m-d H:i:s'));

	                                        if ($now >= $awal && $now <= $akhir) {
	                                          $html .= '
	                                              <td class="text-center">
	                                                  <button class="btn btn-success" onclick="tambah_program_kegiatan_manrisk( \'' . $id_program . '\', \'' . $id_indikator . '\', \'' . $program_kegiatan_group['nama_program_kegiatan'] . '\', \'' . $indikator_data['indikator_text'] . '\', ' . $tipe_target . '); return false;" title="Tambah Data Manrisk">
	                                                      <span class="dashicons dashicons-plus"></span>
	                                                  </button>
	                                                <button class="btn btn-primary" onclick="edit_program_kegiatan_manrisk(' . $data_sebelum['id'] . ', \'' . $id_program . '\', \'' . $id_indikator . '\', ' . $tipe_target . '); return false;" title="Edit Data Sebelum">
	                                                      <span class="dashicons dashicons-edit"></span>
	                                                </button>';
	                                                  
	                                            if ($indikator_count > 1) {
	                                                $html .= '
	                                                  <button class="btn btn-danger" onclick="hapus_program_kegiatan_manrisk(' . $data_sebelum['id'] . '); return false;" title="Hapus Data Sebelum">
	                                                      <span class="dashicons dashicons-trash"></span>
	                                                  </button>';
	                                            }
	                                          
	                                          $html .= '</td>';
	                                        } else {
	                                            $html .= '<td class="text-center"></td>';
	                                        }
	                                    } else {
	                                        $html .= '<td class="text-center"></td>';
	                                    }
	                                } else {
	                                    $html .= '<td class="text-center"></td>';
	                                }

	                                $html .= '</tr>';
	                            }
	                        }
	                    }
	                }
	                $ret['data_sesudah'] = $data_sesudah;
	            } else {
	                $html = '<tr><td class="text-center" colspan="30">Data masih kosong!</td></tr>';
	                $ret['data_sesudah'] = false;
	            }
	            $ret['data'] = $html;
	        } else {
	            $ret = array(
	                'status' => 'error',
	                'message'   => 'Api Key tidak sesuai!'
	            );
	        }
	    } else {
	        $ret = array(
	            'status' => 'error',
	            'message'   => 'Format tidak sesuai!'
	        );
	    }
	    die(json_encode($ret));
	}

    public function submit_program_kegiatan()
	{
	    global $wpdb;
	    $ret = array(
	        'status' => 'success',
	        'message' => 'Berhasil simpan data!'
	    );

	    if (!empty($_POST)) {
	        if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

	            if (!empty($_POST['tahun_anggaran'])) {
	                $tahun_anggaran = $_POST['tahun_anggaran'];
	            } else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'Tahun Anggaran kosong!';
	            }
	            if (!empty($_POST['id_skpd'])) {
	                $id_skpd = $_POST['id_skpd'];
	            } else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'ID SKPD kosong!';
	            }
	            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	            $skala_dampak = isset($_POST['skala_dampak']) ? intval($_POST['skala_dampak']) : 0;
	            $skala_kemungkinan = isset($_POST['skala_kemungkinan']) ? intval($_POST['skala_kemungkinan']) : 0;
	            $controllable_status = isset($_POST['controllable_status']) ? intval($_POST['controllable_status']) : 2;

	            $data = array(
	                'id_program_kegiatan'           => $_POST['id_program_kegiatan'],
	                'id_indikator'                  => $_POST['id_indikator'],
	                'tipe'                          => $_POST['tipe'],
	                'uraian_resiko'                 => $_POST['uraian_resiko'],
	                'kode_resiko'                   => $_POST['kode_resiko'],
	                'pemilik_resiko'                => $_POST['pemilik_resiko'],
	                'uraian_sebab'                  => $_POST['uraian_sebab'],
	                'sumber_sebab'                  => $_POST['sumber_sebab'],
	                'controllable'                  => $controllable_status, 
	                'uraian_dampak'                 => $_POST['uraian_dampak'],
	                'pihak_terkena'                 => $_POST['pihak_terkena'],
	                'skala_dampak'                  => $skala_dampak,
	                'skala_kemungkinan'             => $skala_kemungkinan,
	                'rencana_tindak_pengendalian'   => $_POST['rencana_tindak_pengendalian'],
	                'id_skpd'                       => $id_skpd,
	                'tahun_anggaran'                => $tahun_anggaran,
	                'active'                        => 1
	            );

	            if ($id <= 0) {
	                $wpdb->insert('data_program_kegiatan_manrisk_sebelum',
	                    $data
	                );
	            } else {
	                $wpdb->update('data_program_kegiatan_manrisk_sebelum',
	                    $data, 
	                    array('id' => $id)
	                );
	            }

	        } else {
	            $ret['status']  = 'error';
	            $ret['message'] = 'API key tidak ditemukan!';
	        }
	    } else {
	        $ret['status']  = 'error';
	        $ret['message'] = 'Format salah!';
	    }

	    wp_send_json($ret);
	}

	public function edit_program_kegiatan_manrisk()
	{
	    global $wpdb;
	    $ret = array(
	        'status' => 'success',
	        'message' => 'Berhasil ambil data!',
	        'data' => array()
	    );

	    if (!empty($_POST)) {
	        if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

	            if (!empty($_POST['tahun_anggaran'])) {
	                $tahun_anggaran = intval($_POST['tahun_anggaran']);
	            } else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'Tahun Anggaran kosong!';
	                wp_send_json($ret);
	            }

	            if (!empty($_POST['id_skpd'])) {
	                $id_skpd = intval($_POST['id_skpd']);
	            } else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'ID SKPD kosong!';
	                wp_send_json($ret);
	            }

	            if (!empty($_POST['id'])) {
	                $id = intval($_POST['id']);
	            } else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'ID kosong!';
	                wp_send_json($ret);
	            }

	            if (isset($_POST['tipe']) && is_numeric($_POST['tipe'])) {
	                $tipe = intval($_POST['tipe']);
	            } else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'Tipe kosong atau bukan angka!';
	                wp_send_json($ret);
	            }

	            $get_data = $wpdb->get_row(
	                $wpdb->prepare("
	                    SELECT 
	                      * 
	                    FROM data_program_kegiatan_manrisk_sebelum
	                    WHERE id = %d
	                      AND id_program_kegiatan = %s
	                      AND id_indikator = %s
	                      AND id_skpd = %d
	                      AND tahun_anggaran = %d
	                      AND tipe = %d
	                      AND active = 1
	                    LIMIT 1
	                ", $id, $_POST['id_program_kegiatan'], $_POST['id_indikator'], $id_skpd, $tahun_anggaran, $tipe),
	                ARRAY_A
	            );

	            $nama_program_kegiatan = '';
	            $indikator_teks = '';

	            if ($tipe == 0) {
	                $get_data_program = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        nama_program
	                    FROM data_sub_keg_bl 
	                    WHERE kode_program = %s
	                        AND id_sub_skpd = %d
	                        AND tahun_anggaran = %d
	                        AND active = 1
	                    LIMIT 1
	                ", $get_data['id_program_kegiatan'], $id_skpd, $tahun_anggaran),
	                ARRAY_A);

	                if (!empty($get_data_program)) {
	                    $nama_program_kegiatan = $get_data_program['nama_program'];
	                }

	                $get_data_indikator = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        capaianteks
	                    FROM data_capaian_prog_sub_keg
	                    WHERE kode_sbl LIKE %s
	                        AND active = 1
	                        AND tahun_anggaran = %d
	                        AND capaianteks != ''
	                    ORDER BY id ASC
	                    LIMIT 1
	                ", $get_data['id_indikator'] . '%', $tahun_anggaran), ARRAY_A);

	                if (!empty($get_data_indikator)) {
	                    $indikator_teks = $get_data_indikator['capaianteks'];
	                }

	            } elseif ($tipe == 1) {
	                $get_data_kegiatan = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        nama_giat
	                    FROM data_sub_keg_bl 
	                    WHERE kode_giat = %s
	                        AND id_sub_skpd = %d
	                        AND tahun_anggaran = %d
	                        AND active = 1
	                    LIMIT 1
	                ", $get_data['id_program_kegiatan'], $id_skpd, $tahun_anggaran),
	                ARRAY_A);

	                if (!empty($get_data_kegiatan)) {
	                    $nama_program_kegiatan = $get_data_kegiatan['nama_giat'];
	                }

	                $get_data_indikator = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        outputteks
	                    FROM data_output_giat_sub_keg
	                    WHERE kode_sbl LIKE %s
	                        AND active = 1
	                        AND tahun_anggaran = %d
	                        AND outputteks != ''
	                    ORDER BY id ASC
	                    LIMIT 1
	                ", $get_data['id_indikator'] . '%', $tahun_anggaran), ARRAY_A);

	                if (!empty($get_data_indikator)) {
	                    $indikator_teks = $get_data_indikator['outputteks'];
	                }

	            } elseif ($tipe == 2) {
	                $get_data_sub_kegiatan = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        nama_sub_giat
	                    FROM data_sub_keg_bl 
	                    WHERE kode_sub_giat = %s
	                        AND id_sub_skpd = %d
	                        AND tahun_anggaran = %d
	                        AND active = 1
	                    LIMIT 1
	                ", $get_data['id_program_kegiatan'], $id_skpd, $tahun_anggaran),
	                ARRAY_A);

	                if (!empty($get_data_sub_kegiatan)) {
	                    $nama_program_kegiatan = $get_data_sub_kegiatan['nama_sub_giat'];
	                }

	                $get_data_indikator = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        outputteks
	                    FROM data_sub_keg_indikator
	                    WHERE kode_sbl LIKE %s
	                        AND active = 1
	                        AND tahun_anggaran = %d
	                        AND outputteks != ''
	                    ORDER BY id ASC
	                    LIMIT 1
	                ", $get_data['id_indikator'] . '%', $tahun_anggaran), ARRAY_A);

	                if (!empty($get_data_indikator)) {
	                    $indikator_teks = $get_data_indikator['outputteks'];
	                }
	            }

	            $get_data['nama_program_kegiatan'] = $nama_program_kegiatan;
	            $get_data['indikator'] = $indikator_teks;

	            $ret['data'] = $get_data;

	            if (empty($get_data)) {
	                $ret['status'] = 'error';
	                $ret['message'] = 'Data tidak ditemukan!';
	            }

	        } else {
	            $ret['status']  = 'error';
	            $ret['message'] = 'API key tidak ditemukan!';
	        }
	    } else {
	        $ret['status']  = 'error';
	        $ret['message'] = 'Format salah!';
	    }

	    wp_send_json($ret);
	}

	public function verif_program_kegiatan_manrisk()
	{
	    global $wpdb;
	    $ret = array(
	        'status' => 'success',
	        'message' => 'Berhasil ambil data!',
	        'data' => array()
	    );

	    if (!empty($_POST)) {
	        if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

	            if (!empty($_POST['tahun_anggaran'])) {
	                $tahun_anggaran = intval($_POST['tahun_anggaran']);
	            } else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'Tahun Anggaran kosong!';
	                wp_send_json($ret);
	            }

	            if (!empty($_POST['id_skpd'])) {
	                $id_skpd = intval($_POST['id_skpd']);
	            } else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'ID SKPD kosong!';
	                wp_send_json($ret);
	            }

	            if (isset($_POST['tipe']) && is_numeric($_POST['tipe'])) {
	                $tipe = intval($_POST['tipe']);
	            } else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'Tipe kosong atau bukan angka!';
	                wp_send_json($ret);
	            }

	            $get_data = null;
	            $id_program_kegiatan = '';
	            $id_indikator = '';

	            if (!empty($_POST['id'])) {
	                $get_data = $wpdb->get_row($wpdb->prepare("
	                        SELECT 
	                          * 
	                        FROM data_program_kegiatan_manrisk_sesudah
	                        WHERE id = %d
	                          AND id_sebelum = %d
	                          AND id_program_kegiatan = %s
	                          AND id_indikator = %s
	                          AND id_skpd = %d
	                          AND tahun_anggaran = %d
	                          AND tipe = %d
	                          AND active = 1
	                        LIMIT 1
	                    ", $_POST['id'], $_POST['id_sebelum'], $_POST['id_program_kegiatan'], $_POST['id_indikator'], $id_skpd, $tahun_anggaran, $tipe),
	                ARRAY_A);

	                if ($get_data) {
	                    $id_program_kegiatan = $get_data['id_program_kegiatan'];
	                    $id_indikator = $get_data['id_indikator'];
	                }
	            } else {
	                if (!empty($_POST['id_program_kegiatan'])) {
	                    $id_program_kegiatan = $_POST['id_program_kegiatan'];
	                } else {
	                    $ret['status'] = 'error';
	                    $ret['message'] = 'ID Program Kegiatan kosong!';
	                    wp_send_json($ret);
	                }

	                if (!empty($_POST['id_indikator'])) {
	                    $id_indikator = $_POST['id_indikator'];
	                } else {
	                    $ret['status'] = 'error';
	                    $ret['message'] = 'ID Indikator kosong!';
	                    wp_send_json($ret);
	                }
	            }

	            $nama_program_kegiatan = '';
	            $indikator_teks = '';

	            if ($tipe == 0) {
	                $get_data_program = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        nama_program
	                    FROM data_sub_keg_bl 
	                    WHERE kode_program = %s
	                        AND id_sub_skpd = %d
	                        AND tahun_anggaran = %d
	                        AND active = 1
	                    LIMIT 1
	                ", $id_program_kegiatan, $id_skpd, $tahun_anggaran),
	                ARRAY_A);

	                if (!empty($get_data_program)) {
	                    $nama_program_kegiatan = $get_data_program['nama_program'];
	                }

	                $get_data_indikator = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        capaianteks
	                    FROM data_capaian_prog_sub_keg
	                    WHERE kode_sbl LIKE %s
	                        AND active = 1
	                        AND tahun_anggaran = %d
	                        AND capaianteks != ''
	                    ORDER BY id ASC
	                    LIMIT 1
	                ", $id_indikator . '%', $tahun_anggaran), ARRAY_A);

	                if (!empty($get_data_indikator)) {
	                    $indikator_teks = $get_data_indikator['capaianteks'];
	                }

	            } elseif ($tipe == 1) {
	                $get_data_kegiatan = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        nama_giat
	                    FROM data_sub_keg_bl 
	                    WHERE kode_giat = %s
	                        AND id_sub_skpd = %d
	                        AND tahun_anggaran = %d
	                        AND active = 1
	                    LIMIT 1
	                ", $id_program_kegiatan, $id_skpd, $tahun_anggaran),
	                ARRAY_A);

	                if (!empty($get_data_kegiatan)) {
	                    $nama_program_kegiatan = $get_data_kegiatan['nama_giat'];
	                }

	                $get_data_indikator = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        outputteks
	                    FROM data_output_giat_sub_keg
	                    WHERE kode_sbl LIKE %s
	                        AND active = 1
	                        AND tahun_anggaran = %d
	                        AND outputteks != ''
	                    ORDER BY id ASC
	                    LIMIT 1
	                ", $id_indikator . '%', $tahun_anggaran), ARRAY_A);

	                if (!empty($get_data_indikator)) {
	                    $indikator_teks = $get_data_indikator['outputteks'];
	                }

	            } elseif ($tipe == 2) {
	                $get_data_sub_kegiatan = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        nama_sub_giat
	                    FROM data_sub_keg_bl 
	                    WHERE kode_sub_giat = %s
	                        AND id_sub_skpd = %d
	                        AND tahun_anggaran = %d
	                        AND active = 1
	                    LIMIT 1
	                ", $id_program_kegiatan, $id_skpd, $tahun_anggaran),
	                ARRAY_A);

	                if (!empty($get_data_sub_kegiatan)) {
	                    $nama_program_kegiatan = $get_data_sub_kegiatan['nama_sub_giat'];
	                }

	                $get_data_indikator = $wpdb->get_row($wpdb->prepare("
	                    SELECT 
	                        outputteks
	                    FROM data_sub_keg_indikator
	                    WHERE kode_sbl LIKE %s
	                        AND active = 1
	                        AND tahun_anggaran = %d
	                        AND outputteks != ''
	                    ORDER BY id ASC
	                    LIMIT 1
	                ", $id_indikator . '%', $tahun_anggaran), ARRAY_A);

	                if (!empty($get_data_indikator)) {
	                    $indikator_teks = $get_data_indikator['outputteks'];
	                }
	            }

	            $get_data_sebelum = null;
	            if (!empty($_POST['id_sebelum'])) {
	                $get_data_sebelum = $wpdb->get_row(
	                    $wpdb->prepare("
	                        SELECT 
	                          * 
	                        FROM data_program_kegiatan_manrisk_sebelum
	                        WHERE id = %d
	                          AND id_skpd = %d
	                          AND tahun_anggaran = %d
	                          AND active = 1
	                        LIMIT 1
	                    ", $_POST['id_sebelum'], $id_skpd, $tahun_anggaran),
	                    ARRAY_A
	                );
	            }
	            
	            $ret['data_sebelum'] = $get_data_sebelum;

	            if (is_null($get_data)) {
	                $get_data = array(
	                    'id_program_kegiatan' => $id_program_kegiatan,
	                    'id_indikator' => $id_indikator
	                );
	            }
	            
	            $get_data['nama_program_kegiatan'] = $nama_program_kegiatan;
	            $get_data['indikator'] = $indikator_teks;

	            $ret['data'] = $get_data;

	            if (!empty($_POST['id']) && empty($get_data)) {
	                $ret['status'] = 'error';
	                $ret['message'] = 'Data tidak ditemukan!';
	            }

	        } else {
	            $ret['status']  = 'error';
	            $ret['message'] = 'API key tidak ditemukan!';
	        }
	    } else {
	        $ret['status']  = 'error';
	        $ret['message'] = 'Format salah!';
	    }

	    wp_send_json($ret);
	}

	public function submit_verif_program_kegiatan()
	{
	    global $wpdb;
	    $ret = array(
	        'status' => 'success',
	        'message' => 'Berhasil simpan data!'
	    );

	    if (!empty($_POST)) {
	        if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {

	            if (!empty($_POST['tahun_anggaran'])) {
	                $tahun_anggaran = $_POST['tahun_anggaran'];
	            } else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'Tahun Anggaran kosong!';
	            }
	            if (!empty($_POST['id_skpd'])) {
	                $id_skpd = $_POST['id_skpd'];
	            } else {
	                $ret['status'] = 'error';
	                $ret['message'] = 'ID SKPD kosong!';
	            }
	            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	            $tipe = isset($_POST['tipe']) ? intval($_POST['tipe']) : 0;
	            $skala_dampak = isset($_POST['skala_dampak']) ? intval($_POST['skala_dampak']) : 0;
	            $skala_kemungkinan = isset($_POST['skala_kemungkinan']) ? intval($_POST['skala_kemungkinan']) : 0;
	            $controllable_status = isset($_POST['controllable_status']) ? intval($_POST['controllable_status']) : 2;

	            $data = array(
	                'id_program_kegiatan'           => $_POST['id_program_kegiatan'],
	                'id_indikator'                  => $_POST['id_indikator'],
	                'tipe'                          => $_POST['tipe'],
	                'id_sebelum'                    => $_POST['id_sebelum'], 
	                'tipe'                          => $tipe,
	                'uraian_resiko'                 => $_POST['uraian_resiko'],
	                'kode_resiko'                   => $_POST['kode_resiko'],
	                'pemilik_resiko'                => $_POST['pemilik_resiko'],
	                'uraian_sebab'                  => $_POST['uraian_sebab'],
	                'sumber_sebab'                  => $_POST['sumber_sebab'],
	                'controllable'                  => $controllable_status, 
	                'uraian_dampak'                 => $_POST['uraian_dampak'],
	                'pihak_terkena'                 => $_POST['pihak_terkena'],
	                'skala_dampak'                  => $skala_dampak,
	                'skala_kemungkinan'             => $skala_kemungkinan,
	                'rencana_tindak_pengendalian'   => $_POST['rencana_tindak_pengendalian'],
	                'id_skpd'                       => $id_skpd,
	                'tahun_anggaran'                => $tahun_anggaran,
	                'active'                        => 1
	            );

	            if ($id <= 0) {
	                $wpdb->insert('data_program_kegiatan_manrisk_sesudah',
	                    $data
	                );
	            } else {
	                $wpdb->update('data_program_kegiatan_manrisk_sesudah',
	                    $data, 
	                    array('id' => $id)
	                );
	            }

	        } else {
	            $ret['status']  = 'error';
	            $ret['message'] = 'API key tidak ditemukan!';
	        }
	    } else {
	        $ret['status']  = 'error';
	        $ret['message'] = 'Format salah!';
	    }

	    wp_send_json($ret);
	}

	function hapus_program_kegiatan_manrisk()
	{
	    global $wpdb;
	    $ret = array(
	        'status' => 'success',
	        'message' => 'Berhasil hapus data!'
	    );

	    if (!empty($_POST)) {
	        if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
	            if (empty($_POST['id'])) {
	                $ret['status'] = 'error';
	                $ret['message'] = 'ID kosong!';
	            }
	            $wpdb->update(
	                'data_program_kegiatan_manrisk_sebelum',
	                array('active' => 0),
	                array('id' => $_POST['id']),
	                array('%d')
	            );
	            $wpdb->update(
	                'data_program_kegiatan_manrisk_sesudah',
	                array('active' => 0),
	                array('id_sebelum' => $_POST['id']),
	                array('%d')
	            );
	        } else {
	            $ret['status'] = 'error';
	            $ret['message'] = 'API key tidak ditemukan!';
	        }
	    } else {
	        $ret['status'] = 'error';
	        $ret['message'] = 'Format salah!';
	    }

	    die(json_encode($ret));
	}

	public function get_data_iku($return_text)
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					if (!empty($_POST['id_skpd'])) {
						$id_skpd = $_POST['id_skpd'];
					} else {
						throw new Exception("Id Skpd tidak boleh  kosong!", 1);
					}
					$id_jadwal = '';
					if (empty($_POST['id_jadwal'])) {
						throw new Exception("Id Jadwal Kosong!", 1);
					} else {
						$id_jadwal = $_POST['id_jadwal'];
					}

					$api_params = array(
						'action'		=> 'get_data_iku_all',
						'api_key'		=> get_option('_crb_api_key_wp_eval_sakip'),
						'id_jadwal'		=> $id_jadwal,
						'id_skpd'		=> $id_skpd,
						'tipe_iku'		=> 'opd'
					);
					$response_asli = wp_remote_post(
						get_option('_crb_url_wp_eval_sakip'),
						array(
							'timeout' 	=> 1000,
							'sslverify' => false,
							'body' 		=> $api_params
						)
					);

					$response = wp_remote_retrieve_body($response_asli);
					$response = json_decode($response);
					$return = array(
						'status' => 'success',
						'data' => $response->data,
						// 'params' => $api_params,
						// 'response_asli' => $response_asli
					);
					if (!empty($return_text)) {
						return $return;
					} else {
						die(json_encode($return));
					}
				} else {
					throw new Exception("API tidak ditemukan!", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai!", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function get_data_pohon_kinerja($return_text)
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					if (!empty($_POST['id_skpd'])) {
						$id_skpd = $_POST['id_skpd'];
					} else {
						throw new Exception("Id Skpd tidak boleh  kosong!", 1);
					}
					$id_jadwal_wp_sakip = '';
					if (empty($_POST['id_jadwal_wp_sakip'])) {
						throw new Exception("Id Jadwal WpSakip Kosong!", 1);
					} else {
						$id_jadwal_wp_sakip = $_POST['id_jadwal_wp_sakip'];
					}

					$api_params = array(
						'action'		=> 'get_data_pokin_all',
						'api_key'		=> get_option('_crb_api_key_wp_eval_sakip'),
						'id_jadwal'		=> $id_jadwal_wp_sakip,
						'id_skpd'		=> $id_skpd,
						'tipe_pokin'	=> 'opd'
					);

					$response_asli = wp_remote_post(
						get_option('_crb_url_wp_eval_sakip'),
						array(
							'timeout' 	=> 1000,
							'sslverify' => false,
							'body' 		=> $api_params
						)
					);

					$response = wp_remote_retrieve_body($response_asli);
					$response = json_decode($response);

					$return = array(
						'status' => 'success',
						'data' => $response->data,
						// 'params' => $api_params,
						// 'response_asli' => $response_asli
					);
					if (!empty($return_text)) {
						return $return;
					} else {
						die(json_encode($return));
					}
				} else {
					throw new Exception("API tidak ditemukan!", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai!", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function get_tabel_pokin_cascading($return_text)
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					$pokin_all = $this->get_data_pohon_kinerja(true);

					$pokin_all_new = array();
					$cek_pokin_all = array();
					if(!empty($pokin_all['data'])){
						foreach($pokin_all['data'] as $val){
							$pokin_all_new[$val->id] = $val;
							$cek_pokin_all[$val->id] = $val;
						}
					}
					$pokin_existing = $wpdb->get_results(
						$wpdb->prepare("
							SELECT 
								*
							FROM data_pokin_renstra 
							WHERE id_skpd = %d 
							  	AND active = 1 
							  	AND tahun_anggaran = %d
							ORDER by level ASC
						", $_POST['id_skpd'], $_POST['tahun_anggaran']),
						ARRAY_A
					);
					$unset_pokin_esisting = array();
					$pokin_tujuan = $wpdb->get_results($wpdb->prepare("
						SELECT
							id_unik, 
							tujuan_teks
						FROM data_renstra_tujuan_lokal
						WHERE id_unit=%d
							AND active=1
							AND tahun_anggaran=%d
							AND id_unik_indikator IS NULL
					", $_POST['id_skpd'], $_POST['tahun_anggaran']), ARRAY_A);
					$unset_pokin_tujuan = array();
					foreach($pokin_tujuan as $val){
						$unset_pokin_tujuan[$val['id_unik']] = $val;
					}

					$pokin_sasaran = $wpdb->get_results($wpdb->prepare("
						SELECT
							id_unik, 
							sasaran_teks
						FROM data_renstra_sasaran_lokal
						WHERE id_unit=%d
							AND active=1
							AND tahun_anggaran=%d
							AND id_unik_indikator IS NULL
					", $_POST['id_skpd'], $_POST['tahun_anggaran']), ARRAY_A);
					$unset_pokin_sasaran = array();
					foreach($pokin_sasaran as $val){
						$unset_pokin_sasaran[$val['id_unik']] = $val;
					}

					$pokin_program = $wpdb->get_results($wpdb->prepare("
						SELECT
							id_unik, 
							nama_program
						FROM data_renstra_program_lokal
						WHERE id_unit=%d
							AND active=1
							AND tahun_anggaran=%d
							AND id_unik_indikator IS NULL
					", $_POST['id_skpd'], $_POST['tahun_anggaran']), ARRAY_A);
					$unset_pokin_program = array();
					foreach($pokin_program as $val){
						$unset_pokin_program[$val['id_unik']] = $val;
					}

					$pokin_giat = $wpdb->get_results($wpdb->prepare("
						SELECT
							id_unik, 
							nama_giat
						FROM data_renstra_kegiatan_lokal
						WHERE id_unit=%d
							AND active=1
							AND tahun_anggaran=%d
							AND id_unik_indikator IS NULL
					", $_POST['id_skpd'], $_POST['tahun_anggaran']), ARRAY_A);
					$unset_pokin_giat = array();
					foreach($pokin_giat as $val){
						$unset_pokin_giat[$val['id_unik']] = $val;
					}

					$pokin_sub_giat = $wpdb->get_results($wpdb->prepare("
						SELECT
							id_unik, 
							nama_sub_giat
						FROM data_renstra_sub_kegiatan_lokal
						WHERE id_unit=%d
							AND active=1
							AND tahun_anggaran=%d
							AND id_unik_indikator IS NULL
					", $_POST['id_skpd'], $_POST['tahun_anggaran']), ARRAY_A);
					$unset_pokin_sub_giat = array();
					foreach($pokin_sub_giat as $val){
						$unset_pokin_sub_giat[$val['id_unik']] = $val;
					}
					
					foreach ($pokin_existing as $val) {
						if(!empty($pokin_all_new[$val['id_pokin']])){
							unset($pokin_all_new[$val['id_pokin']]);
						}
						if(empty($cek_pokin_all[$val['id_pokin']])){
							$unset_pokin_esisting[$val['id_pokin']] = $val;
							$unset_pokin_esisting[$val['id_pokin']]['cascading'] = 'Cascading tidak ditemukan! id='.$val['id'];
							if($val['tipe'] == 1){
								if(!empty($unset_pokin_tujuan[$val['id_unik']])){
									$unset_pokin_esisting[$val['id_pokin']]['cascading'] = '(Tujuan) '.$unset_pokin_tujuan[$val['id_unik']]['tujuan_teks'];
								}
							}else if($val['tipe'] == 2){
								if(!empty($unset_pokin_sasaran[$val['id_unik']])){
									$unset_pokin_esisting[$val['id_pokin']]['cascading'] = '(Sasaran) '.$unset_pokin_sasaran[$val['id_unik']]['sasaran_teks'];
								}
							}else if($val['tipe'] == 3){
								if(!empty($unset_pokin_program[$val['id_unik']])){
									$unset_pokin_esisting[$val['id_pokin']]['cascading'] = '(Program) '.$unset_pokin_program[$val['id_unik']]['nama_program'];
								}
							}else if($val['tipe'] == 4){
								if(!empty($unset_pokin_giat[$val['id_unik']])){
									$unset_pokin_esisting[$val['id_pokin']]['cascading'] = '(Kegiatan) '.$unset_pokin_giat[$val['id_unik']]['nama_giat'];
								}
							}else if($val['tipe'] == 5){
								if(!empty($unset_pokin_sub_giat[$val['id_unik']])){
									$unset_pokin_esisting[$val['id_pokin']]['cascading'] = '(Sub Kegiatan) '.$unset_pokin_sub_giat[$val['id_unik']]['nama_sub_giat'];
								}
							}
						}
					}

					$return = array(
						'status' => 'success',
						'pokin_all' => array_values($pokin_all_new),
						'unset_pokin_esisting' => array_values($unset_pokin_esisting)
					);
					if (!empty($return_text)) {
						return $return;
					} else {
						die(json_encode($return));
					}
				} else {
					throw new Exception("API tidak ditemukan!", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai!", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}

	public function get_data_satker($return_text)
	{
		global $wpdb;
		try {
			if (!empty($_POST)) {
				if (!empty($_POST['api_key']) && $_POST['api_key'] == get_option('_crb_api_key_extension')) {
					if (!empty($_POST['id_skpd'])) {
						$id_skpd = $_POST['id_skpd'];
					} else {
						throw new Exception("Id Skpd tidak boleh  kosong!", 1);
					}

					$api_params = array(
						'action'		=> 'get_jabatan_cascading',
						'api_key'		=> get_option('_crb_api_key_wp_eval_sakip'),
						'id_skpd'		=> $id_skpd,
						'q'		=> ''
					);

					$response_asli = wp_remote_post(
						get_option('_crb_url_wp_eval_sakip'),
						array(
							'timeout' 	=> 1000,
							'sslverify' => false,
							'body' 		=> $api_params
						)
					);

					$response = wp_remote_retrieve_body($response_asli);
					$response = json_decode($response);

					$return = array(
						'status' => 'success',
						'data' => $response->data,
						// 'params' => $api_params,
						// 'response_asli' => $response_asli
					);
					if (!empty($return_text)) {
						return $return;
					} else {
						die(json_encode($return));
					}
				} else {
					throw new Exception("API tidak ditemukan!", 1);
				}
			} else {
				throw new Exception("Format tidak sesuai!", 1);
			}
		} catch (Exception $e) {
			echo json_encode([
				'status' => false,
				'message' => $e->getMessage()
			]);
			exit();
		}
	}
}
