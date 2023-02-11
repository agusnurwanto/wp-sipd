<?php 

trait CustomTrait {

	public static function uploadFile(array $data, array $file, array $ext, string $path = '', int $maxSize = 2097152) // default 2MB
	{
		if (!empty($data['api_key']) && $data['api_key'] == get_option( '_crb_api_key_extension' )) {
			
			$fileExt = explode(".", $file['name']);
			if(!in_array(strtolower(end($fileExt)), $ext)){
				throw new Exception('Lampiran wajib ber-type ' . implode(",", $ext));
			}

			if($file['size'] >= $maxSize){
				throw new Exception('Ukuran file melebihi ukuran minimal');
			}

			$target = $path .  $file['name'];
			if(move_uploaded_file($file['tmp_name'], $target)){
				return [
					'status' => true,
					'filename' => $file['name']
				];
			}

			throw new Exception('Oops, gagal upload file, hubungi admin');

		}else{
			throw new Exception('api key tidak ditemukan');
		}
	}
}