<?php 

trait CustomTrait {

	public static function uploadFile(
		string $api_key = '', 
		string $path = '', 
		array $file = array(), 
		array $ext = array(),
		int $maxSize = 1048576, // default 1MB
		string $nama_file = ''
	)
	{
		try{
			if (!empty($api_key) && $api_key == get_option( '_crb_api_key_extension' )) {
				if(!empty($file)){

					if(empty($ext)){
						throw new Exception('Extensi file belum ditentukan '.json_encode($file));
					}

					if(empty($path)){
						throw new Exception('Lokasi folder belum ditentukan '.json_encode($file));
					}

					$imageFileType = strtolower(pathinfo($path.basename($file["name"]),PATHINFO_EXTENSION));
					if(!in_array($imageFileType, $ext)){
						throw new Exception('Lampiran wajib ber-type ' . implode(", ", $ext).' '.json_encode($file));
					}

					if($file['size'] > $maxSize){
						throw new Exception('Ukuran file melebihi ukuran maksimal '.json_encode($file));
					}

					if(!empty($nama_file)){
						$file['name'] = $nama_file.'.'.$imageFileType;
					}else{
						$nama_file = date('Y-m-d-H-i-s');
						$file['name'] = $nama_file.'-'.$file['name'];
					}
					$target = $path .  $file['name'];
					$moved = move_uploaded_file($file['tmp_name'], $target);
					if( $moved ) {
						return [
							'status' => true,
							'filename' => $file['name']
						];
					} else {
						throw new Exception("Oops, gagal upload file ".$file['name'].", hubungi admin. Not uploaded because of error #".$file["error"].' '.json_encode($file));
					}
				}
				throw new Exception('Oops, file belum dipilih');
			}
			throw new Exception('Api key tidak ditemukan');
		}catch(Exception $e){
			return array(
				'status' => false,
				'message' => $e->getMessage()
			);
		}
	}

	public function tanggalan(string $tanggal){
		
		$tanggal = explode("-", $tanggal);

		$bulan = $this->get_bulan($tanggal[1]);

		return $tanggal[2] . " " . $bulan . " " . $tanggal[0];		
	}

	public function get_bulan($bulan) {
		if(empty($bulan)){
			$bulan = date('m');
		}
		$nama_bulan = array(
			"Januari", 
			"Februari", 
			"Maret", 
			"April", 
			"Mei", 
			"Juni", 
			"Juli", 
			"Agustus", 
			"September", 
			"Oktober", 
			"November", 
			"Desember"
		);
		return $nama_bulan[((int) $bulan)-1];
	}
}