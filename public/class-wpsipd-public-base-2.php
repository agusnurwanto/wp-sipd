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
}