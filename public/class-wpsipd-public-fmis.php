<?php

class Wpsipd_Public_FMIS
{
	public function register_sp2d_fmis($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/fmis/wpsipd-public-sp2d-fmis.php';
	}

	public function get_data_register_sp2d_fmis()
	{
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

				// $queryTot = $wpdb->get_results($sqlTot, ARRAY_A);
				// $totalRecords = $queryTot[0]['jml'];
				// $queryRecords = $wpdb->get_results($sqlRec, ARRAY_A);

				foreach($queryRecords as $recKey => $recVal){
					if(!empty($recVal['kelompok'])){
						$queryRecords[$recKey]['show_kelompok'] = $tipe_kelompok[$recVal['kelompok']-1];
					}else{
						$queryRecords[$recKey]['show_kelompok'] = '-';
					}
				}

				$json_data = array(
					"draw"            => intval( $params['draw'] ),   
					"recordsTotal"    => intval( 0 ),  
					"recordsFiltered" => intval(0),
					"data"            => array()
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
}