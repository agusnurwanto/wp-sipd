<?php
	global $wpdb;
	$body = "";
	$path = WPSIPD_PLUGIN_PATH.'/sql-migrate';
	$files = array_diff(scandir($path), array('.', '..'));
	$data = array(
		0 => '
			<tr time="0" file="tabel.sql">
				<td class="text-center">-</td>
				<td class="text-center">tabel.sql</td>
				<td class="text-center">
					<button onclick="run_sql_migrate(\'tabel.sql\'); return false;" class="btn btn-primary">RUN</button>
				</td>
			</tr>
		'
	);
	foreach($files as $k => $v){
		$tgl = str_replace('migrate-', '', $v);
		$tgl = str_replace('.sql', '', $tgl);
		$time = strtotime($tgl);
		$data[$time] = '
			<tr time="'.$time.'" file="'.$v.'">
				<td class="text-center">'.$tgl.'</td>
				<td class="text-center">'.$v.'</td>
				<td class="text-center">
					<button onclick="run_sql_migrate(\''.$v.'\'); return false;" class="btn btn-primary">RUN</button>
				</td>
			</tr>
		';
	}
	krsort($data);
	$body = implode('', $data);
	$last_update = get_option('_last_update_sql_migrate');
	if(empty($last_update)){
		$last_update = 'Belum pernah dirun!';
	}

	$versi = get_option('_wp_sipd_db_version');
    if($versi !== $this->version){
    	$ket = '
    		<div class="notice notice-warning is-dismissible">
        		<p>Versi database WP-SIPD tidak sesuai! harap dimutakhirkan. Versi saat ini=<b>'.$this->version.'</b> dan versi WP-SIPD kamu=<b>'.$versi.'</p>
         	</div>
         ';
    }else{
    	$ket = '
    		<div class="notice notice-warning is-dismissible">
        		<p>Versi database WP-SIPD sudah yang terbaru! Versi=<b>'.$this->version.'</b></p>
         	</div>
         ';
    }

	$table_default_value = array();
	$body_default_value = '';
	$data_statis_rumus_indikator = array(
		array('id' => 1,'rumus' => 'Indikator Tren Positif','keterangan' => 'Indikator Tren Positif adalah jenis indikator yang semakin tinggi realisasi maka dianggap semakin baik. Rumus capaian target = Realisasi/Target * 100. Rumus total target triwulan = akumulasi dari semua triwulan.','user' => NULL,'active' => 1),
		array('id' => 2,'rumus' => 'Indikator Tren Negatif','keterangan' => 'Indikator Tren Negatif adalah jenis indikator yang semakin rendah realisasi maka dianggap semakin baik. Contoh : Angka Stunting. Rumus capaian target : Target/Realisasi * 100. Total triwulan = realisasi triwulan terakhir.','user' => NULL,'active' => 1),
		array('id' => 3,'rumus' => 'Indikator Jenis Persentase','keterangan' => 'Indikator Jenis Persentase adalah jenis indikator berupa persentase. Pengisian realisasi target per bulan diisi sama atau lebih besar dari bulan sebelumnya. Rumus capaian target = Realisasi/Target * 100. Rumus total target triwulan = realisasi triwulan terakhir.','user' => NULL,'active' => 1)
	);

	update_option('data_master_rumus_indikator', $data_statis_rumus_indikator);

	$data_statis_label_komponen = array(
		array('id' => 1,'nama' => 'Penanganan Covid 19','keterangan' => 'Laporan Monev Penanganan Covid 19', 'id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 4,'nama' => 'Alokasi Dana Pendidikan','keterangan' => 'Alokasi anggaran pendidikan sebesar 20% dari APBD sesuai amanat UUD 1945 pasal 31 ayat (4) dan UU No. 20 tahun 2003 tentang Sistem Pendidikan Nasional pasal 49 ayat (1). http://www.djpk.kemenkeu.go.id/?ufaq=apakah-yang-disebut-dengan-mandatory-spending','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 6,'nama' => 'Alokasi Dana Kesehatan','keterangan' => 'Besar anggaran kesehatan pemerintah daerah provinsi, kabupaten/kota dialokasikan minimal 10% (sepuluh persen) dari anggaran pendapatan dan belanja daerah di luar gaji (UU No. 36 Tahun 2009 Tentang Kesehatan). http://www.djpk.kemenkeu.go.id/?ufaq=apakah-yang-disebut-dengan-mandatory-spending','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 7,'nama' => 'Alokasi Dana Ifrastruktur ','keterangan' => 'Dana Transfer Umum (DTU) diarahkan penggunaannya, yaitu paling sedikit 25% (dua puluh lima persen) untuk belanja infrastruktur daerah http://www.djpk.kemenkeu.go.id/?ufaq=apakah-yang-disebut-dengan-mandatory-spending','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 8,'nama' => 'Alokasi Dana Desa','keterangan' => 'Alokasi dana Desa (ADD) paling sedikit 10% dari dana perimbangan yang diterima Kabupaten/Kota dalam Anggaran Pendapatan dan Belanja Daerah setelah dikurangi Dana Alokasi Khusus (UU No. 6 Tahun 2014 Tentang Desa) http://www.djpk.kemenkeu.go.id/?ufaq=apakah-yang-disebut-dengan-mandatory-spending','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 9,'nama' => 'Belanja SPM Pendidikan Anak Usia Dini','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pendidikan Anak Usia Dini','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 10,'nama' => 'Belanja SPM Pendidikan Dasar','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pendidikan Dasar','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 11,'nama' => 'Belanja SPM Pendidikan Kesetaraan','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pendidikan Kesetaraan','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 12,'nama' => 'Belanja SPM Pelayanan Kesehatan Ibu Hamil','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Ibu Hamil','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 13,'nama' => 'Belanja SPM Pelayanan Kesehatan Ibu Bersalin','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Ibu Bersalin','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 14,'nama' => 'Belanja SPM Pelayanan Kesehatan Bayi Baru Lahir','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Bayi Baru Lahir','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 15,'nama' => 'Belanja SPM Pelayanan Kesehatan Balita','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Balita','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 16,'nama' => 'Belanja SPM Pelayanan Kesehatan Pada Usia Pendidikan Dasar','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Pada Usia Pendidikan Dasar','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 17,'nama' => 'Belanja SPM Pelayanan Kesehatan Pada Usia Produktif','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Pada Usia Produktif','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 18,'nama' => 'Belanja SPM Pelayanan Kesehatan Pada Usia Lanjut','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Pada Usia Lanjut','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 19,'nama' => 'Belanja SPM Pelayanan Kesehatan Penderita Hipertensi','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Penderita Hipertensi','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 20,'nama' => 'Belanja SPM Pelayanan Kesehatan Penderita Diabetes Melitus','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Penderita Diabetes Melitus','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 21,'nama' => 'Belanja SPM Pelayanan Kesehatan Orang Dengan Gangguan Jiwa Berat','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Orang Dengan Gangguan Jiwa Berat','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 22,'nama' => 'Belanja SPM Pelayanan Kesehatan Orang Terduga Tuberkulosis','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Orang Terduga Tuberkulosis','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 23,'nama' => 'Belanja SPM Pelayanan Kesehatan Orang Dengan Risiko Terinfeksi Virus Yang Melemahkan Daya Tahan Tubuh Manusia (Human Immunodeficiency Virus)','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pelayanan Kesehatan Orang Dengan Risiko Terinfeksi Virus Yang Melemahkan Daya Tahan Tubuh Manusia (Human Immunodeficiency Virus)','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 24,'nama' => 'Belanja SPM Pekerjaan Umum dan Penataan Ruang Pemenuhan Kebutuhan Pokok Air Minum Sehari-hari','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pekerjaan Umum dan Penataan Ruang Pemenuhan Kebutuhan Pokok Air Minum Sehari-hari','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 25,'nama' => 'Belanja SPM Pekerjaan Umum dan Penataan Ruang Penyediaan Pelayanan Pengolahan Air Limbah Domestik','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Pekerjaan Umum dan Penataan Ruang Penyediaan Pelayanan Pengolahan Air Limbah Domestik','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 26,'nama' => 'Belanja SPM Perumahan Rakyat dan Pemukiman Penyediaan Dan Rehabilitasi Rumah Yang Layak Huni Bagi Korban Bencana Daerah Kabupaten/kota','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Perumahan Rakyat dan Pemukiman Penyediaan Dan Rehabilitasi Rumah Yang Layak Huni Bagi Korban Bencana Daerah Kabupaten/kota','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 27,'nama' => 'Belanja SPM Perumahan Rakyat dan Pemukiman Fasilitasi Penyediaan Rumah Yang Layak Huni Bagi Masyarakat Yang Terkena Relokasi Program Pemerintah Daerah Kabupaten/kota','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Perumahan Rakyat dan Pemukiman Fasilitasi Penyediaan Rumah Yang Layak Huni Bagi Masyarakat Yang Terkena Relokasi Program Pemerintah Daerah Kabupaten/kota','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 28,'nama' => 'Belanja SPM Trantibulinmas Pelayanan Ketenteraman Dan Ketertiban Umum','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Trantibulinmas Pelayanan Ketenteraman Dan Ketertiban Umum','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 29,'nama' => 'Belanja SPM Trantibulinmas Pelayanan Informasi Rawan Bencana','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Trantibulinmas Pelayanan Informasi Rawan Bencana','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 30,'nama' => 'Belanja SPM Trantibulinmas Pelayanan Pencegahan Dan Kesiapsiagaan Terhadap Bencana','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Trantibulinmas Pelayanan Pencegahan Dan Kesiapsiagaan Terhadap Bencana','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 31,'nama' => 'Belanja SPM Trantibulinmas Pelayanan Penyelamatan Dan Evakuasi Korban Bencana','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Trantibulinmas Pelayanan Penyelamatan Dan Evakuasi Korban Bencana','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 32,'nama' => 'Belanja SPM Trantibulinmas Pelayanan Penyelamatan Dan Evakuasi Korban Kebakaran','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Trantibulinmas Pelayanan Penyelamatan Dan Evakuasi Korban Kebakaran','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 33,'nama' => 'Belanja SPM Sosial Rehabilitasi Sosial Dasar Penyandang Disabilitas Terlantar Di Luar Panti','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Sosial Rehabilitasi Sosial Dasar Penyandang Disabilitas Terlantar Di Luar Panti','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 34,'nama' => 'Belanja SPM Sosial Rehabilitasi Sosial Dasar Anak Terlantar Di Luar Panti','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Sosial Rehabilitasi Sosial Dasar Anak Terlantar Di Luar Panti','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 35,'nama' => 'Sosial Rehabilitasi Sosial Dasar Lanjut Usia Terlantar Di Luar Panti','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Sosial Rehabilitasi Sosial Dasar Lanjut Usia Terlantar Di Luar Panti','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 36,'nama' => 'Belanja SPM Sosial Rehabilitasi Sosial Dasar Tuna Sosial Khususnya Gelandangan Dan Pengemis Di Luar Panti','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Sosial Rehabilitasi Sosial Dasar Tuna Sosial Khususnya Gelandangan Dan Pengemis Di Luar Panti','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1),
		array('id' => 37,'nama' => 'Belanja SPM Sosial Perlindungan Dan Jaminan Sosial Pada Saat Dan Setelah Tanggap Darurat Bencana Bagi Korban Bencana Daerah Kabupaten/kota','keterangan' => 'Indeks Penilaian Keuangan Daerah dimensi 2 http://ipkd-bpp.kemendagri.go.id/ . Standar Pelayanan Minimal Sosial Perlindungan Dan Jaminan Sosial Pada Saat Dan Setelah Tanggap Darurat Bencana Bagi Korban Bencana Daerah Kabupaten/kota','id_skpd' => 0,'user' => 'Admin SIPD','active' => 1)
	);

	update_option('data_master_label_komponen', $data_statis_label_komponen);

	$data_statis_tipe_perencanaan = array(
		array('id' => 1,'nama_tipe' => 'rpjpd','keterangan_tipe' => '','lama_pelaksanaan' => 25),
		array('id' => 2,'nama_tipe' => 'rpjm','keterangan_tipe' => '','lama_pelaksanaan' => 5),
		array('id' => 3,'nama_tipe' => 'rpd','keterangan_tipe' => '','lama_pelaksanaan' => 5),
		array('id' => 4,'nama_tipe' => 'renstra','keterangan_tipe' => '','lama_pelaksanaan' => 5),
		array('id' => 5,'nama_tipe' => 'renja','keterangan_tipe' => '','lama_pelaksanaan' => 1),
		array('id' => 6,'nama_tipe' => 'penganggaran_sipd','keterangan_tipe' => '','lama_pelaksanaan' => 1),
		array('id' => 7,'nama_tipe' => 'rpjpd_sipd','keterangan_tipe' => '','lama_pelaksanaan' => 25),
		array('id' => 8,'nama_tipe' => 'rpjm_sipd','keterangan_tipe' => '','lama_pelaksanaan' => 5),
		array('id' => 9,'nama_tipe' => 'rpd_sipd','keterangan_tipe' => '','lama_pelaksanaan' => 5),
		array('id' => 10,'nama_tipe' => 'renstra_sipd','keterangan_tipe' => '','lama_pelaksanaan' => 5),
		array('id' => 11,'nama_tipe' => 'renja_sipd','keterangan_tipe' => '','lama_pelaksanaan' => 1),
		array('id' => 12,'nama_tipe' => 'penganggaran','keterangan_tipe' => '','lama_pelaksanaan' => 1)
	);

	update_option('data_master_tipe_perencanaan', $data_statis_tipe_perencanaan);

	$status_tipe_perencanaan = array();
	$status_tipe_perencanaan['jumlah'] = array();
	$jumlah_data = 0;
	//cek data master tabel tipe perencanaan
	foreach($data_statis_tipe_perencanaan as $val_data){
		$data_tipe_perencanaan = $wpdb->get_results($wpdb->prepare('
								SELECT *
								FROM data_tipe_perencanaan
								WHERE id=%d
								AND nama_tipe=%s',
								$val_data['id'],$val_data['nama_tipe']), ARRAY_A);

		if(empty($data_tipe_perencanaan)){
			array_push($status_tipe_perencanaan,$val_data['nama_tipe']);
		}else{
			if(in_array($val_data['nama_tipe'],$status_tipe_perencanaan)){
				$status_tipe_perencanaan = array_diff($status_tipe_perencanaan,array($val_data['nama_tipe']));
			}
		}
		$jumlah_data = $jumlah_data+1;
		$status_tipe_perencanaan['jumlah'] = $jumlah_data;
	}

	$status_disabled = '';
	$status_color_tipe_perencanaan = '';
	if(count($status_tipe_perencanaan) == 1 && !empty($status_tipe_perencanaan['jumlah'])){
		$data_status_tipe_perencanaan = 'sudah ada';
		$status_disabled = 'disabled';
	}else if(count($status_tipe_perencanaan) < $status_tipe_perencanaan['jumlah']){
		$data_status_tipe_perencanaan = 'sebagian ada';
		$status_color_tipe_perencanaan = "soft-warning";
	}else{
		$data_status_tipe_perencanaan = 'belum ada';
		$status_color_tipe_perencanaan = "soft-danger";
	}

	$body_default_value .= '
		<tr>
			<td class="text-center">-</td>
			<td class="text-center">data_tipe_perencanaan</td>
			<td class="text-center data_tipe_perencanaan_2022 '.$status_color_tipe_perencanaan.'">'.$data_status_tipe_perencanaan.'</td>
			<td class="text-center">
				<button onclick="run_sql_data_master(\'data_tipe_perencanaan\',2022); return false;" class="btn btn-primary btn_data_tipe_perencanaan_2022" '.$status_disabled.'>RUN</button>
			</td>
		</tr>
	';

	$data_tahun = $wpdb->get_results(
		'SELECT tahun_anggaran
		FROM data_unit
		GROUP BY tahun_anggaran
		ORDER BY tahun_anggaran ASC'
	,ARRAY_A);

	$data_tahun = (empty($data_tahun)) ? array(array('tahun_anggaran'=>date("Y"))) : $data_tahun;
	$tahun_terakhir = end($data_tahun);
	$tahun_terakhir = array('tahun_anggaran'=>$tahun_terakhir['tahun_anggaran']+1);
	array_push($data_tahun,$tahun_terakhir);
	$data_tahun = array_reverse($data_tahun);
	foreach ($data_tahun as $val_tahun) {
		//cek data master tabel rumus indikator
		$jumlah_data = 0;
		$status_rumus_indikator = array();
		$status_rumus_indikator['rumus_indikator'][$val_tahun['tahun_anggaran']] = array();
		$status_rumus_indikator['rumus_indikator'][$val_tahun['tahun_anggaran']]['jumlah'] = 0;
		foreach($data_statis_rumus_indikator as $key_indikator => $val_indikator){
			$data_rumus_indikator = $wpdb->get_results($wpdb->prepare('
										SELECT *
										FROM data_rumus_indikator
										WHERE id=%d
										AND rumus=%s
										AND tahun_anggaran=%d',
										$val_indikator['id'],$val_indikator['rumus'],$val_tahun['tahun_anggaran']), ARRAY_A);
			if(empty($data_rumus_indikator)){
				array_push($status_rumus_indikator['rumus_indikator'][$val_tahun['tahun_anggaran']],$val_indikator['id']);
			}else{
				if(in_array($val_indikator['id'],$status_rumus_indikator['rumus_indikator'][$val_tahun['tahun_anggaran']])){
					$status_rumus_indikator['rumus_indikator'][$val_tahun['tahun_anggaran']] = array_diff($status_rumus_indikator['rumus_indikator'][$val_tahun['tahun_anggaran']],array($val_indikator['id_asli']));
				}
			}
			$jumlah_data = $jumlah_data+1;
			$status_rumus_indikator['rumus_indikator'][$val_tahun['tahun_anggaran']]['jumlah'] = $jumlah_data;
		}
		$status_disabled = '';
		$status_color_indikator = '';
		$jumlah_after_count = $status_rumus_indikator['rumus_indikator'][$val_tahun['tahun_anggaran']]['jumlah'] + 1;
		if(count($status_rumus_indikator['rumus_indikator'][$val_tahun['tahun_anggaran']]) == 1 && !empty($status_rumus_indikator['rumus_indikator'][$val_tahun['tahun_anggaran']]['jumlah'])){
			$data_status_rumus_indikator = 'sudah ada';
			$status_disabled = 'disabled';
		}else if(count($status_rumus_indikator['rumus_indikator'][$val_tahun['tahun_anggaran']]) < $jumlah_after_count){
			$data_status_rumus_indikator = 'sebagian ada';
			$status_color_indikator = "soft-warning";
		}else{
			$data_status_rumus_indikator = 'belum ada';
			$status_color_indikator = "soft-danger";
		}

		$body_default_value .= '
			<tr>
				<td class="text-center">'.$val_tahun['tahun_anggaran'].'</td>
				<td class="text-center">data_rumus_indikator</td>
				<td class="text-center data_rumus_indikator_'.$val_tahun['tahun_anggaran'].' '.$status_color_indikator.'">'.$data_status_rumus_indikator.'</td>
				<td class="text-center">
					<button onclick="run_sql_data_master(\'data_rumus_indikator\','.$val_tahun['tahun_anggaran'].'); return false;" class="btn btn-primary btn_data_rumus_indikator_'.$val_tahun['tahun_anggaran'].'" '.$status_disabled.'>RUN</button>
				</td>
			</tr>
		';

		//cek data master tabel label komponen
		$jumlah_data = 0;
		$status_label_komponen = array();
		$status_label_komponen['label_komponen'][$val_tahun['tahun_anggaran']] = array();
		$status_label_komponen['label_komponen'][$val_tahun['tahun_anggaran']]['jumlah'] = 0;
		foreach($data_statis_label_komponen as $key_komponen => $val_komponen){
				$data_label_komponen = $wpdb->get_results($wpdb->prepare('
											SELECT id
											FROM data_label_komponen
											WHERE nama=%s
											AND tahun_anggaran=%d',
											$val_komponen['nama'],$val_tahun['tahun_anggaran']), ARRAY_A);
				if(empty($data_label_komponen)){
					array_push($status_label_komponen['label_komponen'][$val_tahun['tahun_anggaran']],$val_komponen['id']);
				}else{
					if(in_array($val_komponen['id'],$status_label_komponen['label_komponen'][$val_tahun['tahun_anggaran']])){
						$status_label_komponen['label_komponen'][$val_tahun['tahun_anggaran']] = array_diff($status_label_komponen['label_komponen'][$val_tahun['tahun_anggaran']],array($val_komponen['id']));
					}
				}
				$jumlah_data = $jumlah_data+1;
				$status_label_komponen['label_komponen'][$val_tahun['tahun_anggaran']]['jumlah'] = $jumlah_data;
		}
		$status_disabled = '';
		$status_color_komponen = '';
		$jumlah_after_count = $status_label_komponen['label_komponen'][$val_tahun['tahun_anggaran']]['jumlah'] + 1;
		if(count($status_label_komponen['label_komponen'][$val_tahun['tahun_anggaran']]) == 1 && !empty($status_label_komponen['label_komponen'][$val_tahun['tahun_anggaran']]['jumlah'])){
			$data_status_label_komponen = 'sudah ada';
			$status_disabled = 'disabled';
		}else if(count($status_label_komponen['label_komponen'][$val_tahun['tahun_anggaran']]) < $jumlah_after_count){
			$data_status_label_komponen = 'sebagian ada';
			$status_color_komponen = "soft-warning";
		}else{
			$data_status_label_komponen = 'belum ada';
			$status_color_komponen = "soft-danger";
		}
		$body_default_value .= '
			<tr>
				<td class="text-center">'.$val_tahun['tahun_anggaran'].'</td>
				<td class="text-center">data_label_komponen</td>
				<td class="text-center data_label_komponen_'.$val_tahun['tahun_anggaran'].' '.$status_color_komponen.'">'.$data_status_label_komponen.'</td>
				<td class="text-center">
					<button onclick="run_sql_data_master(\'data_label_komponen\','.$val_tahun['tahun_anggaran'].'); return false;" class="btn btn-primary btn_data_label_komponen_'.$val_tahun['tahun_anggaran'].'" '.$status_disabled.'>RUN</button>
				</td>
			</tr>
		';
	}


?>
<style type="text/css">
	.warning {
		background: #f1a4a4;
	}
	.hide {
		display: none;
	}
	.terpilih {
	    background: #d4ffd4;
	}
	.soft-warning {
		background: #F8F988;
	}
	.soft-danger {
		background: #FF9E9E;
	}
</style>
<div class="cetak">
	<div style="padding: 10px;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<h1 class="text-center">Monitoring SQL migrate WP-SIPD</h1>
		<h3 class="text-center"><?php echo $ket; ?></h3>
		<h3 class="text-center">Update terakhir: <b id="status_update"><?php echo $last_update; ?></b></h3>
		<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center">Tanggal</th>
					<th class="text-center">Nama File</th>
					<th class="text-center">Aksi</th>
				</tr>
			</thead>
			<tbody id="data_body">
				<?php echo $body; ?>
			</tbody>
		</table>
		<h5>Catatan:</h5>
		<ul>
			<li>File yang wajib di RUN adalah file <b>tabel.sql</b></li>
			<li>Untuk file yang lain bisa di RUN jika diperlukan. Jika data sudah benar dan di RUN makan query akan error. Kecuali untuk file <b>tabel.sql</b></li>
			<li>Jika ada error waktu menjalankan query selain <b>tabel.sql</b>, maka diabaikan saja</li>
		</ul>
		<h3 style="margin-top:64px;" class="text-center">Monitoring SQL Data Master</h3>
		<table>
			<thead>
				<tr>
					<th class="text-center">Tahun</th>
					<th class="text-center">Nama Tabel</th>
					<th class="text-center">Status</th>
					<th class="text-center">Aksi</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $body_default_value; ?>
			</tbody>
		</table>
		<h5>Catatan:</h5>
		<ul>
			<li>Pastikan setiap tabel memiliki status <b>sudah ada</b></li>
			<li>Tekan tombol run untuk migrasi data ke tabel lokal</li>
			<li>Status <b>sudah ada</b> berarti data sudah ada di tabel lokal</li>
			<li>Status <b>belum ada</b> dengan label warna merah berarti data belum ada di tabel lokal</li>
			<li>Status <b>sebagian ada</b> dengan label warna kuning berarti sebagian ada sudah ada di tabel lokal</li>
		</ul>
	</div>
</div>
<script type="text/javascript">
	function run_sql_migrate(file) {
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url: ajax.url,
	      	type: "post",
	      	data: {
	      		"action": "run_sql_migrate",
	      		"api_key": jQuery('#api_key').val(),
	      		"file": file
	      	},
			dataType: "json",
	      	success: function(data){
	      		if(data.status == 'success'){
	      			alert('Sukses: '+data.message);
					jQuery("#status_update").html(data.value);
					jQuery(".notice").html('<p>Versi database WP-SIPD sudah yang terbaru! Versi=<b>'+data.version+'</b></p>');
					update_status();
				}else{
	      			alert('Error: '+data.message);
				}
				jQuery("#wrap-loading").hide();
			},
			error: function(e) {
				console.log(e);
			}
		});
	}

	function update_status(){
		var file = jQuery('#status_update').text().split(' ')[0];
		jQuery('.terpilih').removeClass('terpilih');
		jQuery('tr[file="'+file+'"]').addClass('terpilih');
	}

	update_status();

	function run_sql_data_master(nama_tabel,tahun_anggaran) {
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url: ajax.url,
			type: "post",
			data: {
				"action"		: "run_sql_data_master",
				"api_key"		: jQuery("#api_key").val(),
				"nama_tabel"	: nama_tabel,
				"tahun_anggaran": tahun_anggaran
			},
			dataType: "json",
			success: function(data){
				if(data.status == 'success'){
					jQuery(`.${nama_tabel}_${tahun_anggaran}`).removeClass('soft-danger');
					jQuery(`.${nama_tabel}_${tahun_anggaran}`).removeClass('soft-warning');
					jQuery(`.${nama_tabel}_${tahun_anggaran}`).html('sudah ada');
					jQuery(`.btn_${nama_tabel}_${tahun_anggaran}`).prop("disabled","disabled");
	      			alert('Data berhasil ditambahkan')
				}else{
	      			alert('Error: '+data.message);
				}
				jQuery("#wrap-loading").hide();
			},
			error:function(e) {
				console.log(e);
			}
		});
	}
</script>