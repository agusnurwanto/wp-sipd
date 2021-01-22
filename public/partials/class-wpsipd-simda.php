<?php
class Wpsipd_Simda
{

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
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	function singkronSimdaPembiayaan($opsi=array()){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export SIMDA!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				if(!empty($_POST['data']) && !empty($_POST['tahun_anggaran'])){
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$pembiayaan_all = array();
					$pembiayaan = $wpdb->get_results($wpdb->prepare("
						SELECT 
							*
						from data_pembiayaan
						where tahun_anggaran=%d
							AND id_skpd=%d
							AND active=1", $tahun_anggaran, $_POST['id_skpd'])
					, ARRAY_A);
					foreach ($pembiayaan as $k => $v) {
						$pembiayaan_all[$v['kode_akun']][] = $v;
					}
					foreach ($pembiayaan_all as $kode_akun => $v) {
						$kd_unit_simda = explode('.', carbon_get_theme_option('crb_unit_'.$_POST['id_skpd']));
						if(empty($kd_unit_simda) || empty($kd_unit_simda[3])){
							continue;
						}
						$_kd_urusan = $kd_unit_simda[0];
						$_kd_bidang = $kd_unit_simda[1];
						$kd_unit = $kd_unit_simda[2];
						$kd_sub_unit = $kd_unit_simda[3];
						
						$akun = explode('.', $kode_akun);
	                    $mapping_rek = $this->CurlSimda(array(
							'query' => "
								SELECT 
									* 
								from ref_rek_mapping
								where kd_rek90_1=".((int)$akun[0])
		                            .' and kd_rek90_2='.((int)$akun[1])
		                            .' and kd_rek90_3='.((int)$akun[2])
		                            .' and kd_rek90_4='.((int)$akun[3])
		                            .' and kd_rek90_5='.((int)$akun[4])
		                            .' and kd_rek90_6='.((int)$akun[5])
		                ));

						$kd = explode('.', $kode_sub_giat[0]['kode_sub_giat']);
						$kd_urusan90 = (int) $kd[0];
						$kd_bidang90 = (int) $kd[1];
						$kd_program90 = (int) $kd[2];
						$kd_kegiatan90 = ((int) $kd[3]).'.'.$kd[4];
						$kd_sub_kegiatan = (int) $kd[5];

		                if(!empty($mapping_rek)){
		                	$options = array(
		                        'query' => "
		                        DELETE from ta_pembiayaan_rinc
		                        where 
		                            tahun=".$tahun_anggaran
		                            .' and kd_urusan='.$_kd_urusan
		                            .' and kd_bidang='.$_kd_bidang
		                            .' and kd_unit='.$kd_unit
		                            .' and kd_sub='.$kd_sub_unit
		                            .' and kd_prog=0'
		                            .' and id_prog=0'
		                            .' and kd_keg=0'
		                            .' and kd_rek_1='.$mapping_rek[0]->kd_rek_1
		                            .' and kd_rek_2='.$mapping_rek[0]->kd_rek_2
		                            .' and kd_rek_3='.$mapping_rek[0]->kd_rek_3
		                            .' and kd_rek_4='.$mapping_rek[0]->kd_rek_4
		                            .' and kd_rek_5='.$mapping_rek[0]->kd_rek_5
		                    );
		                    // print_r($options); die();

		                    $this->CurlSimda($options);
		                	$options = array(
		                        'query' => "
		                        DELETE from ta_pembiayaan
		                        where 
		                            tahun=".$tahun_anggaran
		                            .' and kd_urusan='.$_kd_urusan
		                            .' and kd_bidang='.$_kd_bidang
		                            .' and kd_unit='.$kd_unit
		                            .' and kd_sub='.$kd_sub_unit
		                            .' and kd_prog=0'
		                            .' and id_prog=0'
		                            .' and kd_keg=0'
		                            .' and kd_rek_1='.$mapping_rek[0]->kd_rek_1
		                            .' and kd_rek_2='.$mapping_rek[0]->kd_rek_2
		                            .' and kd_rek_3='.$mapping_rek[0]->kd_rek_3
		                            .' and kd_rek_4='.$mapping_rek[0]->kd_rek_4
		                            .' and kd_rek_5='.$mapping_rek[0]->kd_rek_5
		                    );
		                    // print_r($options); die();
		                    $this->CurlSimda($options);

	                        $options = array(
	                            'query' => "
	                            INSERT INTO ta_pembiayaan (
	                                tahun,
	                                kd_urusan,
	                                kd_bidang,
	                                kd_unit,
	                                kd_sub,
	                                kd_prog,
	                                id_prog,
	                                kd_keg,
	                                kd_rek_1,
		                            kd_rek_2,
		                            kd_rek_3,
		                            kd_rek_4,
		                            kd_rek_5,
	                                kd_sumber
	                            )
	                            VALUES (
	                                ".$tahun_anggaran.",
	                                ".$_kd_urusan.",
	                                ".$_kd_bidang.",
	                                ".$kd_unit.",
	                                ".$kd_sub_unit.",
	                                0,
	                                0,
	                                0,
	                                ".$mapping_rek[0]->kd_rek_1.",
		                            ".$mapping_rek[0]->kd_rek_2.",
		                            ".$mapping_rek[0]->kd_rek_3.",
		                            ".$mapping_rek[0]->kd_rek_4.",
		                            ".$mapping_rek[0]->kd_rek_5.",
	                                null
	                            )"
	                        );
							// print_r($v); die($kode_akun);
							$this->CurlSimda($options);
							$no_rinc = 0;
							foreach ($v as $kk => $vv) {
								$no_rinc++;
								$options = array(
		                            'query' => "
		                            INSERT INTO ta_pembiayaan_rinc (
		                                tahun,
		                                kd_urusan,
		                                kd_bidang,
		                                kd_unit,
		                                kd_sub,
		                                kd_prog,
		                                id_prog,
		                                kd_keg,
		                                kd_rek_1,
			                            kd_rek_2,
			                            kd_rek_3,
			                            kd_rek_4,
			                            kd_rek_5,
		                                no_id,
		                                sat_1,
		                                nilai_1,
		                                sat_2,
		                                nilai_2,
		                                sat_3,
		                                nilai_3,
		                                satuan123,
		                                jml_satuan,
		                                nilai_rp,
		                                total,
		                                keterangan
		                            )
		                            VALUES (
		                                ".$tahun_anggaran.",
		                                ".$_kd_urusan.",
		                                ".$_kd_bidang.",
		                                ".$kd_unit.",
		                                ".$kd_sub_unit.",
		                                0,
		                                0,
		                                0,
		                                ".$mapping_rek[0]->kd_rek_1.",
			                            ".$mapping_rek[0]->kd_rek_2.",
			                            ".$mapping_rek[0]->kd_rek_3.",
			                            ".$mapping_rek[0]->kd_rek_4.",
			                            ".$mapping_rek[0]->kd_rek_5.",
		                                ".$no_rinc.",
		                                null,
		                                1,
		                                null,
		                                0,
		                                null,
		                                0,
		                                'Tahun',
		                                1,
		                                ".$vv['total'].",
		                                ".$vv['total'].",
		                                '".$vv['uraian']."'
		                            )"
		                        );
								// print_r($options); die($kode_akun);
								$this->CurlSimda($options);
							}
		                }
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format singkron simda salah!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		if(!empty($opsi['return'])){
			die(json_encode($ret));
		}
	}

	function singkronSimdaPendapatan($opsi=array()){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export SIMDA!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				if(!empty($_POST['data']) && !empty($_POST['tahun_anggaran'])){
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$pendapatan_all = array();
					foreach ($_POST['data'] as $k => $v) {
						$pendapatan = $wpdb->get_results($wpdb->prepare("
							SELECT 
								*
							from data_pendapatan
							where tahun_anggaran=%d
								AND id_pendapatan=%d
								AND active=1", $tahun_anggaran, $v['id_pendapatan'])
						, ARRAY_A);
						
						$pendapatan_all[$pendapatan[0]['kode_akun']] = $pendapatan;
					}
					$no_pendapatan = 0;
					foreach ($pendapatan_all as $kode_akun => $v) {
						$no_pendapatan++;
						$kd_unit_simda = explode('.', carbon_get_theme_option('crb_unit_'.$_POST['id_skpd']));
						if(empty($kd_unit_simda) || empty($kd_unit_simda[3])){
							continue;
						}
						$_kd_urusan = $kd_unit_simda[0];
						$_kd_bidang = $kd_unit_simda[1];
						$kd_unit = $kd_unit_simda[2];
						$kd_sub_unit = $kd_unit_simda[3];
						
						$akun = explode('.', $kode_akun);
	                    $mapping_rek = $this->CurlSimda(array(
							'query' => "
								SELECT 
									* 
								from ref_rek_mapping
								where kd_rek90_1=".((int)$akun[0])
		                            .' and kd_rek90_2='.((int)$akun[1])
		                            .' and kd_rek90_3='.((int)$akun[2])
		                            .' and kd_rek90_4='.((int)$akun[3])
		                            .' and kd_rek90_5='.((int)$akun[4])
		                            .' and kd_rek90_6='.((int)$akun[5])
		                ));

						$kd = explode('.', $kode_sub_giat[0]['kode_sub_giat']);
						$kd_urusan90 = (int) $kd[0];
						$kd_bidang90 = (int) $kd[1];
						$kd_program90 = (int) $kd[2];
						$kd_kegiatan90 = ((int) $kd[3]).'.'.$kd[4];
						$kd_sub_kegiatan = (int) $kd[5];

		                if(!empty($mapping_rek)){
		                	$options = array(
		                        'query' => "
		                        DELETE from ta_pendapatan_rinc
		                        where 
		                            tahun=".$tahun_anggaran
		                            .' and kd_urusan='.$_kd_urusan
		                            .' and kd_bidang='.$_kd_bidang
		                            .' and kd_unit='.$kd_unit
		                            .' and kd_sub='.$kd_sub_unit
		                            .' and kd_prog=0'
		                            .' and id_prog=0'
		                            .' and kd_keg=0'
		                            .' and kd_rek_1='.$mapping_rek[0]->kd_rek_1
		                            .' and kd_rek_2='.$mapping_rek[0]->kd_rek_2
		                            .' and kd_rek_3='.$mapping_rek[0]->kd_rek_3
		                            .' and kd_rek_4='.$mapping_rek[0]->kd_rek_4
		                            .' and kd_rek_5='.$mapping_rek[0]->kd_rek_5
		                    );
		                    // print_r($options); die();

		                    $this->CurlSimda($options);
		                	$options = array(
		                        'query' => "
		                        DELETE from ta_pendapatan
		                        where 
		                            tahun=".$tahun_anggaran
		                            .' and kd_urusan='.$_kd_urusan
		                            .' and kd_bidang='.$_kd_bidang
		                            .' and kd_unit='.$kd_unit
		                            .' and kd_sub='.$kd_sub_unit
		                            .' and kd_prog=0'
		                            .' and id_prog=0'
		                            .' and kd_keg=0'
		                            .' and kd_rek_1='.$mapping_rek[0]->kd_rek_1
		                            .' and kd_rek_2='.$mapping_rek[0]->kd_rek_2
		                            .' and kd_rek_3='.$mapping_rek[0]->kd_rek_3
		                            .' and kd_rek_4='.$mapping_rek[0]->kd_rek_4
		                            .' and kd_rek_5='.$mapping_rek[0]->kd_rek_5
		                    );
		                    // print_r($options); die();
		                    $this->CurlSimda($options);

	                        $options = array(
	                            'query' => "
	                            INSERT INTO ta_pendapatan (
	                                tahun,
	                                kd_urusan,
	                                kd_bidang,
	                                kd_unit,
	                                kd_sub,
	                                kd_prog,
	                                id_prog,
	                                kd_keg,
	                                kd_rek_1,
		                            kd_rek_2,
		                            kd_rek_3,
		                            kd_rek_4,
		                            kd_rek_5,
		                            kd_pendapatan,
	                                kd_sumber
	                            )
	                            VALUES (
	                                ".$tahun_anggaran.",
	                                ".$_kd_urusan.",
	                                ".$_kd_bidang.",
	                                ".$kd_unit.",
	                                ".$kd_sub_unit.",
	                                0,
	                                0,
	                                0,
	                                ".$mapping_rek[0]->kd_rek_1.",
		                            ".$mapping_rek[0]->kd_rek_2.",
		                            ".$mapping_rek[0]->kd_rek_3.",
		                            ".$mapping_rek[0]->kd_rek_4.",
		                            ".$mapping_rek[0]->kd_rek_5.",
		                            ".$mapping_rek[0]->kd_rek_2.",
	                                null
	                            )"
	                        );
							// print_r($options); die($kode_akun);
							$this->CurlSimda($options);
							$no_rinc = 0;
							foreach ($v as $kk => $vv) {
								$no_rinc++;
								$options = array(
		                            'query' => "
		                            INSERT INTO ta_pendapatan_rinc (
		                                tahun,
		                                kd_urusan,
		                                kd_bidang,
		                                kd_unit,
		                                kd_sub,
		                                kd_prog,
		                                id_prog,
		                                kd_keg,
		                                kd_rek_1,
			                            kd_rek_2,
			                            kd_rek_3,
			                            kd_rek_4,
			                            kd_rek_5,
		                                no_id,
		                                sat_1,
		                                nilai_1,
		                                sat_2,
		                                nilai_2,
		                                sat_3,
		                                nilai_3,
		                                satuan123,
		                                jml_satuan,
		                                nilai_rp,
		                                total,
		                                keterangan
		                            )
		                            VALUES (
		                                ".$tahun_anggaran.",
		                                ".$_kd_urusan.",
		                                ".$_kd_bidang.",
		                                ".$kd_unit.",
		                                ".$kd_sub_unit.",
		                                0,
		                                0,
		                                0,
		                                ".$mapping_rek[0]->kd_rek_1.",
			                            ".$mapping_rek[0]->kd_rek_2.",
			                            ".$mapping_rek[0]->kd_rek_3.",
			                            ".$mapping_rek[0]->kd_rek_4.",
			                            ".$mapping_rek[0]->kd_rek_5.",
		                                ".$no_rinc.",
		                                null,
		                                1,
		                                null,
		                                0,
		                                null,
		                                0,
		                                'Tahun',
		                                1,
		                                ".$vv['total'].",
		                                ".$vv['total'].",
		                                '".$vv['uraian']."'
		                            )"
		                        );
								// print_r($options); die($kode_akun);
								$this->CurlSimda($options);
							}
		                }
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format singkron simda salah!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		if(!empty($opsi['return'])){
			die(json_encode($ret));
		}
	}

	function singkronSimdaUnit($opsi=array()){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export SIMDA!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				if(!empty($_POST['data_unit']) && !empty($_POST['tahun_anggaran'])){
					foreach ($_POST['data_unit'] as $k => $v) {
						$kd_unit_simda = explode('.', carbon_get_theme_option('crb_unit_'.$v['id_skpd']));
						if(empty($kd_unit_simda) || empty($kd_unit_simda[3])){
							continue;
						}
						$tahun_anggaran = $_POST['tahun_anggaran'];
						$unit = $wpdb->get_results($wpdb->prepare("
							SELECT 
								* 
							from data_unit 
							where tahun_anggaran=%d
								AND id_skpd=%d
								AND active=1", $tahun_anggaran, $v['id_skpd'])
						, ARRAY_A);
						$_kd_urusan = $kd_unit_simda[0];
						$_kd_bidang = $kd_unit_simda[1];
						$kd_unit = $kd_unit_simda[2];
						$kd_sub_unit = $kd_unit_simda[3];

						$cek_ta_sub_unit = $this->CurlSimda(array(
							'query' => "
								SELECT 
									* 
								from ta_sub_unit 
								where tahun=".$tahun_anggaran
		                            .' and kd_urusan='.$_kd_urusan
		                            .' and kd_bidang='.$_kd_bidang
		                            .' and kd_unit='.$kd_unit
		                            .' and kd_sub='.$kd_sub_unit
						));
						if(!empty($cek_ta_sub_unit)){
							$options = array(
	                            'query' => "
	                            UPDATE ta_sub_unit set
	                                nm_pimpinan = '".$unit[0]['namakepala']."',
	                                nip_pimpinan = '".$unit[0]['nipkepala']."'
	                            where 
		                            tahun=".$tahun_anggaran
		                            .' and kd_urusan='.$_kd_urusan
		                            .' and kd_bidang='.$_kd_bidang
		                            .' and kd_unit='.$kd_unit
		                            .' and kd_sub='.$kd_sub_unit
	                        );
							$this->CurlSimda($options);
							/*
							*/
	                    }else{
	                        $options = array(
	                            'query' => "
	                            INSERT INTO ta_sub_unit (
	                                tahun,
	                                kd_urusan,
	                                kd_bidang,
	                                kd_unit,
	                                kd_sub,
	                                nm_pimpinan,
	                                nip_pimpinan
	                            )
	                            VALUES (
	                                ".$tahun_anggaran.",
	                                ".$_kd_urusan.",
	                                ".$_kd_bidang.",
	                                ".$kd_unit.",
	                                ".$kd_sub_unit.",
	                                '".$unit[0]['namakepala']."',
	                                '".$unit[0]['nipkepala']."'
	                            )"
	                        );
							// print_r($options); die($v['id_skpd']);
							$this->CurlSimda($options);
						}
						// print_r($options); die($v['id_skpd']);
						// $this->CurlSimda($options);
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format singkron simda salah!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		if(!empty($opsi['return'])){
			die(json_encode($ret));
		}
	}

	function singkronSimdaKas($opsi=array()){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export SIMDA!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				if(!empty($_POST['data']) && !empty($_POST['tahun_anggaran'])){
					$tahun_anggaran = $_POST['tahun_anggaran'];
					$type = $_POST['type'];
					if($type == 'belanja'){
						$kode_sbl = explode('.', $_POST['kode_sbl']);
						unset($kode_sbl[2]);
						$kode_sub_giat = $wpdb->get_results($wpdb->prepare("
							SELECT 
								k.kode_sub_giat 
							from data_sub_keg_bl k
							where k.tahun_anggaran=%d
								AND k.kode_sbl=%s
								AND k.active=1", $tahun_anggaran, implode('.', $kode_sbl))
						, ARRAY_A);
						// print_r($kode_sub_giat); die($wpdb->last_query);
					}

					foreach ($_POST['data'] as $k => $v) {
						if(!empty($v['id_skpd'])){
							$kd_unit_simda = explode('.', carbon_get_theme_option('crb_unit_'.$v['id_skpd']));
						}else{
							$kd_unit_simda = explode('.', carbon_get_theme_option('crb_unit_'.$v['id_unit']));
						}
						if(empty($kd_unit_simda) || empty($kd_unit_simda[3])){
							continue;
						}
						$_kd_urusan = $kd_unit_simda[0];
						$_kd_bidang = $kd_unit_simda[1];
						$kd_unit = $kd_unit_simda[2];
						$kd_sub_unit = $kd_unit_simda[3];

						$rak = $wpdb->get_results($wpdb->prepare("
							SELECT 
								k.*
							from data_anggaran_kas k
							where k.tahun_anggaran=%d
								AND k.kode_sbl=%s
								AND k.id_akun=%d
								AND k.active=1", $tahun_anggaran, $_POST['kode_sbl'], $v['id_akun'])
						, ARRAY_A);
						
						$akun = explode('.', $rak[0]['kode_akun']);
	                    $mapping_rek = $this->CurlSimda(array(
							'query' => "
								SELECT 
									* 
								from ref_rek_mapping
								where kd_rek90_1=".((int)$akun[0])
		                            .' and kd_rek90_2='.((int)$akun[1])
		                            .' and kd_rek90_3='.((int)$akun[2])
		                            .' and kd_rek90_4='.((int)$akun[3])
		                            .' and kd_rek90_5='.((int)$akun[4])
		                            .' and kd_rek90_6='.((int)$akun[5])
		                ));

						if($type == 'belanja'){
							$kd = explode('.', $kode_sub_giat[0]['kode_sub_giat']);
							$kd_urusan90 = (int) $kd[0];
							$kd_bidang90 = (int) $kd[1];
							$kd_program90 = (int) $kd[2];
							$kd_kegiatan90 = ((int) $kd[3]).'.'.$kd[4];
							$kd_sub_kegiatan = (int) $kd[5];
			                $mapping = $this->CurlSimda(array(
								'query' => "
									SELECT 
										* 
									from ref_kegiatan_mapping
									where kd_urusan90=".$kd_urusan90
			                            .' and kd_bidang90='.$kd_bidang90
			                            .' and kd_program90='.$kd_program90
			                            .' and kd_kegiatan90='.$kd_kegiatan90
			                            .' and kd_sub_kegiatan='.$kd_sub_kegiatan
							));
			            }else{
			            	$mapping = true;
			            }
		                
		                if(!empty($mapping) && !empty($mapping_rek)){
		                	if($type == 'belanja'){
								$kd_urusan = $mapping[0]->kd_urusan;
								$kd_bidang = $mapping[0]->kd_bidang;
								$kd_prog = $mapping[0]->kd_prog;
								$kd_keg = $mapping[0]->kd_keg;
								$id_prog = $kd_urusan.$this->CekNull($kd_bidang);
							}else{
								$kd_urusan = 0;
								$kd_bidang = 0;
								$kd_prog = 0;
								$kd_keg = 0;
								$id_prog = 0;
							}
							$cek_ta_rencana = $this->CurlSimda(array(
								'query' => "
									SELECT 
										* 
									from ta_rencana 
									where tahun=".$tahun_anggaran
			                            .' and kd_urusan='.$_kd_urusan
			                            .' and kd_bidang='.$_kd_bidang
			                            .' and kd_unit='.$kd_unit
			                            .' and kd_sub='.$kd_sub_unit
			                            .' and kd_prog='.$kd_prog
			                            .' and id_prog='.$id_prog
			                            .' and kd_keg='.$kd_keg
			                            .' and kd_rek_1='.$mapping_rek[0]->kd_rek_1
			                            .' and kd_rek_2='.$mapping_rek[0]->kd_rek_2
			                            .' and kd_rek_3='.$mapping_rek[0]->kd_rek_3
			                            .' and kd_rek_4='.$mapping_rek[0]->kd_rek_4
			                            .' and kd_rek_5='.$mapping_rek[0]->kd_rek_5
							));
							if(!empty($cek_ta_rencana)){
								$options = array(
		                            'query' => "
		                            UPDATE ta_rencana set
		                                jan = ".$rak[0]['bulan_1'].",
		                                feb = ".$rak[0]['bulan_2'].",
		                                mar = ".$rak[0]['bulan_3'].",
		                                apr = ".$rak[0]['bulan_4'].",
		                                mei = ".$rak[0]['bulan_5'].",
		                                jun = ".$rak[0]['bulan_6'].",
		                                jul = ".$rak[0]['bulan_7'].",
		                                agt = ".$rak[0]['bulan_8'].",
		                                sep = ".$rak[0]['bulan_9'].",
		                                okt = ".$rak[0]['bulan_10'].",
		                                nop = ".$rak[0]['bulan_11'].",
		                                des = ".$rak[0]['bulan_12']."
		                            where 
			                            tahun=".$tahun_anggaran
			                            .' and kd_urusan='.$_kd_urusan
			                            .' and kd_bidang='.$_kd_bidang
			                            .' and kd_unit='.$kd_unit
			                            .' and kd_sub='.$kd_sub_unit
			                            .' and kd_prog='.$kd_prog
			                            .' and id_prog='.$id_prog
			                            .' and kd_keg='.$kd_keg
			                            .' and kd_rek_1='.$mapping_rek[0]->kd_rek_1
			                            .' and kd_rek_2='.$mapping_rek[0]->kd_rek_2
			                            .' and kd_rek_3='.$mapping_rek[0]->kd_rek_3
			                            .' and kd_rek_4='.$mapping_rek[0]->kd_rek_4
			                            .' and kd_rek_5='.$mapping_rek[0]->kd_rek_5
		                        );
		                    }else{
		                        $options = array(
		                            'query' => "
		                            INSERT INTO ta_rencana (
		                                tahun,
		                                kd_urusan,
		                                kd_bidang,
		                                kd_unit,
		                                kd_sub,
		                                kd_prog,
		                                id_prog,
		                                kd_keg,
		                                kd_rek_1,
			                            kd_rek_2,
			                            kd_rek_3,
			                            kd_rek_4,
			                            kd_rek_5,
			                            jan,
		                                feb,
		                                mar,
		                                apr,
		                                mei,
		                                jun,
		                                jul,
		                                agt,
		                                sep,
		                                okt,
		                                nop,
		                                des
		                            )
		                            VALUES (
		                                ".$tahun_anggaran.",
		                                ".$_kd_urusan.",
		                                ".$_kd_bidang.",
		                                ".$kd_unit.",
		                                ".$kd_sub_unit.",
		                                ".$kd_prog.",
		                                ".$id_prog.",
		                                ".$kd_keg.",
		                                ".$mapping_rek[0]->kd_rek_1.",
			                            ".$mapping_rek[0]->kd_rek_2.",
			                            ".$mapping_rek[0]->kd_rek_3.",
			                            ".$mapping_rek[0]->kd_rek_4.",
			                            ".$mapping_rek[0]->kd_rek_5.",
		                                ".$rak[0]['bulan_1'].",
		                                ".$rak[0]['bulan_2'].",
		                                ".$rak[0]['bulan_3'].",
		                                ".$rak[0]['bulan_4'].",
		                                ".$rak[0]['bulan_5'].",
		                                ".$rak[0]['bulan_6'].",
		                                ".$rak[0]['bulan_7'].",
		                                ".$rak[0]['bulan_8'].",
		                                ".$rak[0]['bulan_9'].",
		                                ".$rak[0]['bulan_10'].",
		                                ".$rak[0]['bulan_11'].",
		                                ".$rak[0]['bulan_12']."
		                            )"
		                        );
							}
							// print_r($options); die($v['id_akun']);
							$this->CurlSimda($options);
		                }else{
		                	$ret['status'] = 'error';
							$ret['message'] = 'ref_kegiatan_mapping atau ref_rek_mapping tidak ditemukan!';
		                }
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format singkron simda salah!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		if(!empty($opsi['return'])){
			die(json_encode($ret));
		}
	}

	function singkronSimda($opsi=array()){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil export SIMDA!'
		);
		if (!empty($_POST)) {
			if (!empty($_POST['api_key']) && $_POST['api_key'] == carbon_get_theme_option( 'crb_api_key_extension' )) {
				$kodeunit = '';
				if(!empty($_POST['kode_sbl']) && !empty($_POST['tahun_anggaran'])){
					$sbl = $wpdb->get_results($wpdb->prepare("
						SELECT 
							* 
						from data_sub_keg_bl 
						where kode_sbl=%s
							AND tahun_anggaran=%d
							AND active=1", $_POST['kode_sbl'], $_POST['tahun_anggaran'])
					, ARRAY_A);
					if(!empty($sbl)){
						foreach ($sbl as $k => $v) {
							$sql = "
								SELECT 
									* 
								from data_lokasi_sub_keg 
								where kode_sbl='".$v['kode_sbl']."'
									AND tahun_anggaran=".$v['tahun_anggaran']."
									AND active=1";
							$lokasi_sub_keg = $wpdb->get_results($sql, ARRAY_A);
							$lokasi_sub = array();
							foreach ($lokasi_sub_keg as $key => $lok) {
								if(!empty($lok['idkabkota'])){
									$lokasi_sub[] = $lok['daerahteks'];
								}
								if(!empty($lok['idcamat'])){
									$lokasi_sub[] = $lok['camatteks'];
								}
								if(!empty($lok['idlurah'])){
									$lokasi_sub[] = $lok['lurahteks'];
								}
							}

							$sql = "
								SELECT 
									* 
								from data_dana_sub_keg 
								where kode_sbl='".$v['kode_sbl']."'
									AND tahun_anggaran=".$v['tahun_anggaran']."
									AND active=1";
							$sd_sub_keg = $wpdb->get_results($sql, ARRAY_A);
							$id_sd = array();
							foreach ($sd_sub_keg as $key => $sd) {
								if(!empty($sd['iddana'])){
									$new_sd = explode(' - ', $sd['namadana']);
									$cek_sd = $this->CurlSimda(array('query' => "select * from ref_sumber_dana where nm_sumber='".trim($new_sd[1])."'"));
									if(empty($cek_sd)){
										$cek_sd = $this->CurlSimda(array('query' => "select * from ref_sumber_dana where kd_sumber='".$sd['iddana']."'"));
										if(!empty($cek_sd)){
											$options = array('query' => "
												UPDATE ref_sumber_dana 
												set nm_sumber='".trim($new_sd[1])."'
												where kd_sumber=".$sd['iddana']
											);
										}else{
											$options = array('query' => "
												INSERT INTO ref_sumber_dana (
					                                kd_sumber,
					                                nm_sumber
					                            )
					                            VALUES (
													".$sd['iddana'].",
													'".trim($new_sd[1])."'
												)"
											);
										}
										$this->CurlSimda($options);
									}
									$id_sd[] = $sd['iddana'];
								}
							}
							if(empty($id_sd)){
								$sumber_dana = 'null';
							}else{
								$sumber_dana = $id_sd[0];
							}

							$bulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
							$waktu_pelaksanaan = $bulan[$v['waktu_awal']-1].' s.d. '.$bulan[$v['waktu_akhir']-1];

							$kd_unit_simda = explode('.', carbon_get_theme_option('crb_unit_'.$v['id_sub_skpd']));
							$tahun_anggaran = $v['tahun_anggaran'];
							if(!empty($kd_unit_simda) && !empty($kd_unit_simda[3])){
								$kd = explode('.', $v['kode_sub_giat']);
								$kd_urusan90 = (int) $kd[0];
								$kd_bidang90 = (int) $kd[1];
								$kd_program90 = (int) $kd[2];
								$kd_kegiatan90 = ((int) $kd[3]).'.'.$kd[4];
								$kd_sub_kegiatan = (int) $kd[5];
								$mapping = $this->CurlSimda(array(
									'query' => "
										SELECT 
											* 
										from ref_kegiatan_mapping
										where kd_urusan90=".$kd_urusan90
				                            .' and kd_bidang90='.$kd_bidang90
				                            .' and kd_program90='.$kd_program90
				                            .' and kd_kegiatan90='.$kd_kegiatan90
				                            .' and kd_sub_kegiatan='.$kd_sub_kegiatan
								));
								if(!empty($mapping)){
									$_kd_urusan = $kd_unit_simda[0];
									$_kd_bidang = $kd_unit_simda[1];
									$kd_unit = $kd_unit_simda[2];
									$kd_sub_unit = $kd_unit_simda[3];
									
									$kd_urusan = $mapping[0]->kd_urusan;
									$kd_bidang = $mapping[0]->kd_bidang;
									$kd_prog = $mapping[0]->kd_prog;
									$kd_keg = $mapping[0]->kd_keg;

				                    $id_prog = $kd_urusan.$this->CekNull($kd_bidang);
				                    $nama_prog = $v['nama_giat'];

				                    $nama_keg = explode(' ', $v['nama_sub_giat']);
				                    unset($nama_keg[0]);
				                    $nama_keg = implode(' ', $nama_keg);

									$program_simda = $this->CurlSimda(array(
										'query' => "
											SELECT 
												* 
											from ta_program
											where tahun=".$tahun_anggaran
					                            .' and kd_urusan='.$_kd_urusan
					                            .' and kd_bidang='.$_kd_bidang
					                            .' and kd_unit='.$kd_unit
					                            .' and kd_sub='.$kd_sub_unit
					                            .' and kd_prog='.$kd_prog
					                            .' and id_prog='.$id_prog
									));
									if(empty($program_simda)){
				                        $options = array(
											'query' => "
												INSERT INTO ta_program (
				                                    tahun,
				                                    kd_urusan,
				                                    kd_bidang,
				                                    kd_unit,
				                                    kd_sub,
				                                    kd_prog,
				                                    id_prog,
				                                    ket_program,
				                                    kd_urusan1,
				                                    kd_bidang1
				                                )
				                                VALUES (
				                                    ".$tahun_anggaran.",
				                                    ".$_kd_urusan.",
				                                    ".$_kd_bidang.",
				                                    ".$kd_unit.",
				                                    ".$kd_sub_unit.",
				                                    ".$kd_prog.",
				                                    ".$id_prog.",
				                                    '".$nama_prog."',
				                                    ".$kd_urusan.",
				                                    ".$kd_bidang."
				                                )"
										);
										// print_r($options); die();
										$this->CurlSimda($options);
									}
									$options = array(
				                        'query' => "
				                        select
				                            tahun 
				                        from 
				                            ta_kegiatan 
				                        where 
				                            tahun=".$tahun_anggaran."
				                            and kd_urusan=".$_kd_urusan."
				                            and kd_bidang=".$_kd_bidang."
				                            and kd_unit=".$kd_unit."
				                            and kd_sub=".$kd_sub_unit."
				                            and kd_prog=".$kd_prog."
				                            and id_prog=".$id_prog."
				                            and kd_keg=".$kd_keg
				                    );
				                    $cek_kegiatan = $this->CurlSimda($options);
				                    if(!empty($cek_kegiatan)){
				                        $options = array(
				                            'query' => "
				                            UPDATE ta_kegiatan set
				                                ket_kegiatan = '".$nama_keg."',
				                                lokasi = '".implode(', ', $lokasi_sub)."',
				                                status_kegiatan = 1,
				                                pagu_anggaran = ".$v['pagu'].",
				                                kd_sumber = ".$sumber_dana.",
				                                waktu_pelaksanaan = '".$waktu_pelaksanaan."',
				                                kelompok_sasaran = '".$v['sasaran']."'
				                            where 
					                            tahun=".$tahun_anggaran."
					                            and kd_urusan=".$_kd_urusan."
					                            and kd_bidang=".$_kd_bidang."
					                            and kd_unit=".$kd_unit."
					                            and kd_sub=".$kd_sub_unit."
					                            and kd_prog=".$kd_prog."
					                            and id_prog=".$id_prog."
					                            and kd_keg=".$kd_keg
				                        );
				                    }else{
				                        $options = array(
				                            'query' => "
				                            INSERT INTO ta_kegiatan (
				                                tahun,
				                                kd_urusan,
				                                kd_bidang,
				                                kd_unit,
				                                kd_sub,
				                                kd_prog,
				                                id_prog,
				                                kd_keg,
				                                ket_kegiatan,
				                                lokasi,
				                                kelompok_sasaran,
				                                status_kegiatan,
				                                pagu_anggaran,
				                                waktu_pelaksanaan,
				                                kd_sumber
				                            )
				                            VALUES (
				                                ".$tahun_anggaran.",
				                                ".$_kd_urusan.",
				                                ".$_kd_bidang.",
				                                ".$kd_unit.",
				                                ".$kd_sub_unit.",
				                                ".$kd_prog.",
				                                ".$id_prog.",
				                                ".$kd_keg.",
				                                '".$nama_keg."',
				                                '".implode(', ', $lokasi_sub)."',
				                                '".$v['sasaran']."',
				                                1,
				                                ".$v['pagu'].",
				                                '".$waktu_pelaksanaan."',
				                                ".$sumber_dana."
				                            )"
				                        );
				                    }
									// print_r($options); die($_POST['kode_sbl']);
									$this->CurlSimda($options);

									$options = array(
				                        'query' => "
				                        DELETE from ta_indikator
				                        where 
				                            tahun=".$tahun_anggaran."
				                            and kd_urusan=".$_kd_urusan."
				                            and kd_bidang=".$_kd_bidang."
				                            and kd_unit=".$kd_unit."
				                            and kd_sub=".$kd_sub_unit."
				                            and kd_prog=".$kd_prog."
				                            and id_prog=".$id_prog."
				                            and kd_keg=".$kd_keg
				                    );
				                    // print_r($options); die();
				                    $this->CurlSimda($options);

									$options = array(
				                        'query' => "
				                        DELETE from ta_belanja_rinc_sub
				                        where 
				                            tahun=".$tahun_anggaran."
				                            and kd_urusan=".$_kd_urusan."
				                            and kd_bidang=".$_kd_bidang."
				                            and kd_unit=".$kd_unit."
				                            and kd_sub=".$kd_sub_unit."
				                            and kd_prog=".$kd_prog."
				                            and id_prog=".$id_prog."
				                            and kd_keg=".$kd_keg
				                    );
				                    // print_r($options); die();
				                    $this->CurlSimda($options);

									$options = array(
				                        'query' => "
				                        DELETE from ta_belanja_rinc
				                        where 
				                            tahun=".$tahun_anggaran."
				                            and kd_urusan=".$_kd_urusan."
				                            and kd_bidang=".$_kd_bidang."
				                            and kd_unit=".$kd_unit."
				                            and kd_sub=".$kd_sub_unit."
				                            and kd_prog=".$kd_prog."
				                            and id_prog=".$id_prog."
				                            and kd_keg=".$kd_keg
				                    );
				                    // print_r($options); die();
				                    $this->CurlSimda($options);

									$options = array(
				                        'query' => "
				                        DELETE from ta_belanja
				                        where 
				                            tahun=".$tahun_anggaran."
				                            and kd_urusan=".$_kd_urusan."
				                            and kd_bidang=".$_kd_bidang."
				                            and kd_unit=".$kd_unit."
				                            and kd_sub=".$kd_sub_unit."
				                            and kd_prog=".$kd_prog."
				                            and id_prog=".$id_prog."
				                            and kd_keg=".$kd_keg
				                    );
				                    // print_r($options); die();
				                    $this->CurlSimda($options);

				                    $sql = "
										SELECT 
											* 
										from data_sub_keg_indikator 
										where kode_sbl='".$v['kode_sbl']."'
											AND tahun_anggaran=".$v['tahun_anggaran']."
											AND active=1";
									$ind_keg = $wpdb->get_results($sql, ARRAY_A);
									$no = 0;
									foreach ($ind_keg as $kk => $ind) {
										$no++;
										$options = array(
				                            'query' => "
				                            INSERT INTO ta_indikator (
				                                tahun,
				                                kd_urusan,
				                                kd_bidang,
				                                kd_unit,
				                                kd_sub,
				                                kd_prog,
				                                id_prog,
				                                kd_keg,
				                                kd_indikator,
				                                no_id,
				                                tolak_ukur,
				                                target_angka,
				                                target_uraian
				                            )
				                            VALUES (
								            	".$tahun_anggaran.",
				                                ".$_kd_urusan.",
				                                ".$_kd_bidang.",
				                                ".$kd_unit.",
				                                ".$kd_sub_unit.",
				                                ".$kd_prog.",
				                                ".$id_prog.",
				                                ".$kd_keg.",
				                                3,
				                                ".$no.",
				                                '".substr($ind['outputteks'], 0, 255)."',
				                                ".(!empty($ind['targetoutput'])? $ind['targetoutput']:0).",
				                                '".$ind['satuanoutput']."'
				                            )"
				                        );
				                        // print_r($options); die();
				                        $this->CurlSimda($options);
									}

				                    $sql = "
										SELECT 
											* 
										from data_keg_indikator_hasil 
										where kode_sbl='".$v['kode_sbl']."'
											AND tahun_anggaran=".$v['tahun_anggaran']."
											AND active=1";
									$ind_keg = $wpdb->get_results($sql, ARRAY_A);
									$no = 0;
									foreach ($ind_keg as $kk => $ind) {
										$no++;
										$options = array(
				                            'query' => "
				                            INSERT INTO ta_indikator (
				                                tahun,
				                                kd_urusan,
				                                kd_bidang,
				                                kd_unit,
				                                kd_sub,
				                                kd_prog,
				                                id_prog,
				                                kd_keg,
				                                kd_indikator,
				                                no_id,
				                                tolak_ukur,
				                                target_angka,
				                                target_uraian
				                            )
				                            VALUES (
								            	".$tahun_anggaran.",
				                                ".$_kd_urusan.",
				                                ".$_kd_bidang.",
				                                ".$kd_unit.",
				                                ".$kd_sub_unit.",
				                                ".$kd_prog.",
				                                ".$id_prog.",
				                                ".$kd_keg.",
				                                4,
				                                ".$no.",
				                                '".substr($ind['hasilteks'], 0, 255)."',
				                                ".(!empty($ind['targethasil'])? $ind['targethasil']:0).",
				                                '".$ind['satuanhasil']."'
				                            )"
				                        );
				                        // print_r($options); die();
				                        $this->CurlSimda($options);
									}

				                    $sql = "
										SELECT 
											* 
										from data_rka 
										where kode_sbl='".$v['kode_sbl']."'
											AND tahun_anggaran=".$v['tahun_anggaran']."
											AND active=1";
									$rka = $wpdb->get_results($sql, ARRAY_A);
									$akun_all = array();
									$rinc_all = array();
									foreach ($rka as $kk => $rk) {
										if(empty($akun_all[$rk['kode_akun']])){
											$akun_all[$rk['kode_akun']] = array();	
										}
										if(empty($akun_all[$rk['kode_akun']][$rk['subs_bl_teks'].' | '.$rk['ket_bl_teks']])){
											$akun_all[$rk['kode_akun']][$rk['subs_bl_teks'].' | '.$rk['ket_bl_teks']] = array();	
										}
										$akun_all[$rk['kode_akun']][$rk['subs_bl_teks'].' | '.$rk['ket_bl_teks']][] = $rk;
									}

									foreach ($akun_all as $kk => $rk) {
										$akun = explode('.', $kk);

					                    $mapping_rek = $this->CurlSimda(array(
											'query' => "
												SELECT 
													* 
												from ref_rek_mapping
												where kd_rek90_1=".((int)$akun[0])
						                            .' and kd_rek90_2='.((int)$akun[1])
						                            .' and kd_rek90_3='.((int)$akun[2])
						                            .' and kd_rek90_4='.((int)$akun[3])
						                            .' and kd_rek90_5='.((int)$akun[4])
						                            .' and kd_rek90_6='.((int)$akun[5])
										));
										if(!empty($mapping_rek)){
								            $options = array(
								                'query' => "
										            INSERT INTO ta_belanja (
										                tahun,
										                kd_urusan,
										                kd_bidang,
										                kd_unit,
										                kd_sub,
										                kd_prog,
										                id_prog,
										                kd_keg,
										                kd_rek_1,
										                kd_rek_2,
										                kd_rek_3,
										                kd_rek_4,
										                kd_rek_5,
										                kd_sumber
										            ) VALUES (
										            	".$tahun_anggaran.",
						                                ".$_kd_urusan.",
						                                ".$_kd_bidang.",
						                                ".$kd_unit.",
						                                ".$kd_sub_unit.",
						                                ".$kd_prog.",
						                                ".$id_prog.",
						                                ".$kd_keg.",
										                ".$mapping_rek[0]->kd_rek_1.",
										                ".$mapping_rek[0]->kd_rek_2.",
										                ".$mapping_rek[0]->kd_rek_3.",
										                ".$mapping_rek[0]->kd_rek_4.",
										                ".$mapping_rek[0]->kd_rek_5.",
										                ".$sumber_dana."
										            )"
								            );
						                    // print_r($options); die();
						                    $this->CurlSimda($options);
											
					                		$no_rinc = 0;
											foreach ($rk as $kkk => $rkk) {
												$no_rinc++;
												$options = array(
									                'query' => "
											            INSERT INTO ta_belanja_rinc (
											                tahun,
											                kd_urusan,
											                kd_bidang,
											                kd_unit,
											                kd_sub,
											                kd_prog,
											                id_prog,
											                kd_keg,
											                kd_rek_1,
											                kd_rek_2,
											                kd_rek_3,
											                kd_rek_4,
											                kd_rek_5,
											                no_rinc,
											                keterangan,
											                kd_sumber
											            ) VALUES (
											            	".$tahun_anggaran.",
							                                ".$_kd_urusan.",
							                                ".$_kd_bidang.",
							                                ".$kd_unit.",
							                                ".$kd_sub_unit.",
							                                ".$kd_prog.",
							                                ".$id_prog.",
							                                ".$kd_keg.",
											                ".$mapping_rek[0]->kd_rek_1.",
											                ".$mapping_rek[0]->kd_rek_2.",
											                ".$mapping_rek[0]->kd_rek_3.",
											                ".$mapping_rek[0]->kd_rek_4.",
											                ".$mapping_rek[0]->kd_rek_5.",
											                ".$no_rinc.",
											                '".$kkk."',
											                ".$sumber_dana."
											            )"
									            );
							                    // print_r($options); die();
							                    $this->CurlSimda($options);

						                		$no_rinc_sub = 0;
												foreach ($rkk as $kkkk => $rkkk) {
													$no_rinc_sub++;
													$nilai1 = 0;
													$nilai1_t = 1;
													if(!empty($rkkk['volum1'])){
														$nilai1 = $rkkk['volum1'];
														$nilai1_t = $rkkk['volum1'];
													}else{
														$jml_satuan_db = explode(' ', $rkkk['koefisien']);
														if(!empty($jml_satuan_db) && $jml_satuan_db[0] >= 1){
															$nilai1 = $jml_satuan_db[0];
														}
													}
													$sat1 = $rkkk['satuan'];
													if(!empty($rkkk['sat1'])){
														$sat1 = $rkkk['sat1'];
													}
													$nilai2 = 0;
													$nilai2_t = 1;
													if(!empty($rkkk['volum2'])){
														$nilai2 = $rkkk['volum2'];
														$nilai2_t = $rkkk['volum2'];
													}
													$nilai3 = 0;
													$nilai3_t = 1;
													if(!empty($rkkk['volum3'])){
														$nilai3 = $rkkk['volum3'];
														$nilai3_t = $rkkk['volum3'];
													}
													$nilai4_t = 1;
													if(!empty($rkkk['volum4'])){
														$nilai4_t = $rkkk['volum4'];
													}
													$jml_satuan = $nilai1_t*$nilai2_t*$nilai3_t*$nilai4_t;
													$options = array(
										                'query' => "
												            INSERT INTO ta_belanja_rinc_sub (
												                tahun,
												                kd_urusan,
												                kd_bidang,
												                kd_unit,
												                kd_sub,
												                kd_prog,
												                id_prog,
												                kd_keg,
												                kd_rek_1,
												                kd_rek_2,
												                kd_rek_3,
												                kd_rek_4,
												                kd_rek_5,
												                no_rinc,
												                no_id,
												                sat_1,
										                        nilai_1,
										                        sat_2,
										                        nilai_2,
										                        sat_3,
										                        nilai_3,
										                        satuan123,
										                        jml_satuan,
										                        nilai_rp,
										                        total,
										                        keterangan
												            ) VALUES (
												            	".$tahun_anggaran.",
								                                ".$_kd_urusan.",
								                                ".$_kd_bidang.",
								                                ".$kd_unit.",
								                                ".$kd_sub_unit.",
								                                ".$kd_prog.",
								                                ".$id_prog.",
								                                ".$kd_keg.",
												                ".$mapping_rek[0]->kd_rek_1.",
												                ".$mapping_rek[0]->kd_rek_2.",
												                ".$mapping_rek[0]->kd_rek_3.",
												                ".$mapping_rek[0]->kd_rek_4.",
												                ".$mapping_rek[0]->kd_rek_5.",
												                ".$no_rinc.",
												                ".$no_rinc_sub.",
												                '".substr($sat1, 0, 10)."',
												                ".$nilai1.",
												                '".substr($rkkk['sat2'], 0, 10)."',
												                ".$nilai2.",
												                '".substr($rkkk['sat3'], 0, 10)."',
												                ".$nilai3.",
												                '".substr($rkkk['satuan'], 0, 50)."',
												                ".$jml_satuan.",
												                ".$rkkk['harga_satuan'].",
												                ".$rkkk['total_harga'].",
												                '".substr(implode(' | ', $komponen), 0, 255)."'
												            )"
										            );
								                    // print_r($options); die();
								                    $this->CurlSimda($options);
												}
											}

										}else{
											$ret['status'] = 'error';
											$ret['simda_status'] = 'error';
											$ret['simda_msg'] = 'Kode akun '.$rk['kode_akun'].' tidak ditemukan di ref_rek_mapping SIMDA';
										}
					                }
								}else{
									$ret['status'] = 'error';
									$ret['simda_status'] = 'error';
									$ret['simda_msg'] = 'Kode kegiatan '.$v['kode_sub_giat'].' tidak ditemukan di ref_kegiatan_mapping SIMDA';
								}
							}else{
								$ret['status'] = 'error';
								$ret['simda_status'] = 'error';
								$ret['simda_msg'] = 'Kode Unit belum dimapping di wp-sipd untuk OPD '.$v['nama_skpd'];
							}
						}
					}else{
						$ret['status'] = 'error';
						$ret['message'] = 'kode_sbl '.$_POST['kode_sbl'].' di tahun anggaran '.$_POST['tahun_anggaran'].' tidak ditemukan!';
					}
				} else {
					$ret['status'] = 'error';
					$ret['message'] = 'Format singkron simda salah!';
				}
			} else {
				$ret['status'] = 'error';
				$ret['message'] = 'APIKEY tidak sesuai!';
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		if(!empty($opsi['return'])){
			die(json_encode($ret));
		}
	}

	function CurlSimda($options, $debug=false){
        $query = $options['query'];
        $curl = curl_init();
        $req = array(
            'api_key' => carbon_get_theme_option( 'crb_apikey_simda' ),
            'query' => $query,
            'db' => carbon_get_theme_option('crb_db_simda')
        );
        set_time_limit(0);
        $req = http_build_query($req);
        $url = carbon_get_theme_option( 'crb_url_api_simda' );
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $req,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 10000
        ));

        $response = curl_exec($curl);
        // die($response);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err; die();
        } else {
        	if($debug){
            	print_r($response); die();
        	}
            $ret = json_decode($response);
            if(!empty($ret->error)){
                echo "<pre>".print_r($ret, 1)."</pre>"; die();
            }else{
                return $ret->msg;
            }
        }
    }

    function CekNull($number, $length=2){
        $l = strlen($number);
        $ret = '';
        for($i=0; $i<$length; $i++){
            if($i+1 > $l){
                $ret .= '0';
            }
        }
        $ret .= $number;
        return $ret;
    }
}