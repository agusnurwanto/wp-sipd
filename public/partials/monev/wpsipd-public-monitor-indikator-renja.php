<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

global $wpdb;
$input = shortcode_atts(array(
	'id_skpd' => '',
	'tahun_anggaran' => ''
), $atts);
// die($input['tahun_anggaran']);

if (empty($input['id_skpd'])) {
	die('<h1>SKPD tidak ditemukan!</h1>');
}

$data_jadwal_renja = $wpdb->get_row(
	$wpdb->prepare("
        SELECT *
        FROM data_jadwal_lokal
        WHERE status = %d
          AND id_tipe = %d
		  AND tahun_anggaran = %d
    ", 0, 16, $input['tahun_anggaran']),
	ARRAY_A
);
$data_jadwal_renstra = $wpdb->get_row(
	$wpdb->prepare("
        SELECT *
        FROM data_jadwal_lokal
        WHERE status = %d
          AND id_tipe = %d
		  AND id_jadwal_lokal = %d
    ", 0, 15, $data_jadwal_renja['relasi_perencanaan']),
	ARRAY_A
);

if (empty($data_jadwal_renja) || empty($data_jadwal_renstra)) {
	die('<h1>Jadwal Tidak Tersedia!</h1>');
}

$awal_renstra = $data_jadwal_renstra['tahun_anggaran']; // 2024
$akhir_renstra = $data_jadwal_renstra['tahun_akhir_anggaran']; // 2026
$lama_pelaksanaan = $data_jadwal_renstra['lama_pelaksanaan']; // 3
$body_tahun = '';
for ($i = 0; $i < $lama_pelaksanaan; $i++) {
	$tahun = $awal_renstra + $i;
	$body_tahun .= '<th class="text-center">' . $tahun . '</th>';
}

$tahun_renstra = ($input['tahun_anggaran'] - $awal_renstra) + 1;

if ($tahun_renstra > 5) {
	die('<h1>Tahun awal RPJMD sudah lebih dari 5 tahun. Sesuaikan di halaman admin!</h1>');
} else if ($tahun_renstra < 1) {
	die('<h1>Tahun awal RPJMD sudah kurang dari 1 tahun. Sesuaikan di halaman admin!</h1>');
}

$tahun_sekarang = date('Y');
$batas_bulan_input = date('m');
if ($input['tahun_anggaran'] < $tahun_sekarang) {
	$batas_bulan_input = 12;
}
$api_key = get_option('_crb_api_key_extension');
function button_edit_monev($class = false, $bobot_kinerja = false)
{
	$ret = ' <span style="display: none;" data-id="' . $class . '" class="edit-monev" title="Edit Monev"><i class="dashicons dashicons-edit"></i></span>';
	return $ret;
}
function valid_number($no)
{
	$no = str_replace(array(','), array('.'), $no);
	return $no;
}

$rumus_indikator_db = $wpdb->get_results(
	$wpdb->prepare("
		SELECT *
		FROM data_rumus_indikator
		WHERE tahun_anggaran = %d
			AND active = 1
	", $input['tahun_anggaran']),
	ARRAY_A
);
$rumus_indikator_html = '';
$keterangan_indikator_html = '';
foreach ($rumus_indikator_db as $k => $v) {
	$rumus_indikator_html .= '<option value="' . $v['id'] . '">' . $v['rumus'] . '</option>';
	$keterangan_indikator_html .= '<li data-id="' . $v['id'] . '" style="display: none;">' . $v['keterangan'] . '</li>';
}

$sql = $wpdb->prepare("
	SELECT *
	FROM data_unit
	WHERE tahun_anggaran=%d
	  AND id_skpd = %d
	  AND active= 1
	ORDER BY id_skpd ASC
	", $input['tahun_anggaran'], $input['id_skpd']);
$unit = $wpdb->get_results($sql, ARRAY_A);

$nama_pemda = get_option('_crb_daerah');
$current_user = wp_get_current_user();

$bulan = date('m');
$current_year = date('Y');

//jika berganti tahun, bulan akan diset di bulan desember
if ($input['tahun_anggaran'] < $current_year) {
    $bulan = 12;
}
$subkeg = $wpdb->get_results(
	$wpdb->prepare("
		SELECT
			k.*,
			k.id as id_sub_keg
		FROM data_sub_keg_bl k
		WHERE k.tahun_anggaran=%d
		  AND k.active=1
		  AND k.id_sub_skpd=%d
		  AND k.pagu > 0
	ORDER BY k.kode_sub_giat ASC
	", $input['tahun_anggaran'], $unit[0]['id_skpd']),
	ARRAY_A
);
$data_all = array(
	'total' 		 => 0,
	'total_simda' 	 => 0,
	'triwulan_1' 	 => 0,
	'triwulan_2' 	 => 0,
	'triwulan_3' 	 => 0,
	'triwulan_4' 	 => 0,
	'rak_triwulan_1' => 0,
	'rak_triwulan_2' => 0,
	'rak_triwulan_3' => 0,
	'rak_triwulan_4' => 0,
	'realisasi' 	 => 0,
	'data' 			 => array()
);
$crb_cara_input_realisasi = get_option('_crb_cara_input_realisasi', 1);
foreach ($subkeg as $kk => $sub) {
	$kd = explode('.', $sub['kode_sub_giat']);
	$kd_urusan90 = (int) $kd[0];
	$kd_bidang90 = (int) $kd[1];
	$kd_program90 = (int) $kd[2];
	$kd_kegiatan90 = ((int) $kd[3]) . '.' . $kd[4];
	$kd_sub_kegiatan = (int) $kd[5];
	$nama_keg = explode(' ', $sub['nama_sub_giat']);
	unset($nama_keg[0]);
	$nama_keg = implode(' ', $nama_keg);
	if ($crb_cara_input_realisasi == 1) {
		$total_simda = $sub['pagu_simda'];
	} else {
		$total_simda = $sub['pagu'];
	}
	$total_pagu = $sub['pagu'];
	$kode = explode('.', $sub['kode_sbl']);

	$rfk_all = $wpdb->get_results($wpdb->prepare("
		SELECT
			id,
			realisasi_anggaran,
			rak,
			bulan
		FROM data_rfk
		WHERE tahun_anggaran=%d
		  AND id_skpd=%d
		  AND kode_sbl=%s
		  AND bulan<=%d 
		ORDER BY bulan ASC, id ASC
	", $input['tahun_anggaran'], $unit[0]['id_skpd'], $sub['kode_sbl'], $bulan), ARRAY_A);
	$rak = array();
	foreach ($rfk_all as $k => $v) {
		if (empty($rak[$v['bulan']])) {
			$v['key'] = $k;
			$rak[$v['bulan']] = $v;
		} else {
			// hapus jika ada bulan yang double
			$wpdb->delete('data_rfk', array('id' => $v['id']));
		}
	}

	$cek_input = false;
	for ($i = 1; $i <= $bulan; $i++) {
		$opsi = array(
			'user' 			 => $current_user->display_name,
			'id_skpd' 		 => $unit[0]['id_skpd'],
			'kode_sbl' 		 => $sub['kode_sbl'],
			'tahun_anggaran' => $input['tahun_anggaran'],
			'bulan' 		 => $i,
			'cek_insert' 	 => false,
			'rak' 		 	 => 0
		);
		if (!isset($rak[$i])) {
			$cek_input = true;
			$opsi['cek_insert'] = true;
		} else {
			$opsi['rak'] = $rak[$i]['rak'];
		}

		// fungsi untuk mengupdate RAK sesuai RAK SIPD atau menginsert data baru jika data_rfk bulan ini belum ada
		$cek_rak_sipd = $this->get_rak_sipd_rfk($opsi);

		// setting nilai RAK terbaru SIPD ke variable rfk all
		if (isset($rak[$i])) {
			$rfk_all[$rak[$i]['key']]['rak'] = $cek_rak_sipd;
		}
	}

	// jika ada data rak yang baru diinput maka diselect ulang
	if ($cek_input == true) {
		$rfk_all = $wpdb->get_results($wpdb->prepare("
			SELECT 
				id, 
				realisasi_anggaran, 
				rak, 
				bulan 
			FROM data_rfk 
			WHERE tahun_anggaran=%d 
			  AND id_skpd=%d 
			  AND kode_sbl=%s 
			  AND bulan<=%d ORDER BY bulan ASC
		", $input['tahun_anggaran'], $unit[0]['id_skpd'], $sub['kode_sbl'], $bulan), ARRAY_A);
	}

	$triwulan_1 = 0;
	$triwulan_2 = 0;
	$triwulan_3 = 0;
	$triwulan_4 = 0;
	$rak_triwulan_1 = 0;
	$rak_triwulan_2 = 0;
	$rak_triwulan_3 = 0;
	$rak_triwulan_4 = 0;
	$realisasi_bulan_all = array();
	$rak_bulan_all = array();
	foreach ($rfk_all as $k => $v) {
		// jika bulan lebih kecil dari bulan sekarang dan realisasinya masih kosong maka realisasi dibuat sama dengan bulan sebelumnya agar realisasi tidak minus
		if ($input['tahun_anggaran'] == $v)
		if (
			$v['bulan'] <= $bulan
			&& empty($v['realisasi_anggaran'])
			&& !empty($realisasi_bulan_all[$v['bulan'] - 1])
		) {
			$v['realisasi_anggaran'] = $realisasi_bulan_all[$v['bulan'] - 1];
			$wpdb->update(
				'data_rfk',
				array('realisasi_anggaran' => $v['realisasi_anggaran']),
				array('id' => $v['id'])
			);
		}
		$realisasi_bulan_all[$v['bulan']] = $v['realisasi_anggaran'];
		$rak_bulan_all[$v['bulan']] = $v['rak'];
		if (empty($v['realisasi_anggaran'])) {
			$v['realisasi_anggaran'] = 0;
		}
		if (empty($v['rak'])) {
			$v['rak'] = 0;
		}
		if ($v['bulan'] <= 3) {
			$triwulan_1 = $v['realisasi_anggaran'];
			$rak_triwulan_1 = $v['rak'];
		} else if ($v['bulan'] <= 6) {
			$triwulan_2 = $v['realisasi_anggaran'] - $realisasi_bulan_all[3];
			$rak_triwulan_2 = $v['rak'] - $rak_bulan_all[3];
		} else if ($v['bulan'] <= 9) {
			$triwulan_3 = $v['realisasi_anggaran'] - $realisasi_bulan_all[6];
			$rak_triwulan_3 = $v['rak'] - $rak_bulan_all[6];
		} else if ($v['bulan'] <= 12) {
			$triwulan_4 = $v['realisasi_anggaran'] - $realisasi_bulan_all[9];
			$rak_triwulan_4 = $v['rak'] - $rak_bulan_all[9];
		}
	}
	$realisasi = $triwulan_1 + $triwulan_2 + $triwulan_3 + $triwulan_4;

	$kode_sbl_s = explode('.', $sub['kode_sbl']);
	if (empty($data_all['data'][$sub['kode_urusan']])) {
		$data_all['data'][$sub['kode_urusan']] = array(
			'nama'		 => $sub['nama_urusan'],
			'total' 	 => 0,
			'triwulan_1' => 0,
			'triwulan_2' => 0,
			'triwulan_3' => 0,
			'triwulan_4' => 0,
			'total_simda' => 0,
			'realisasi'  => 0,
			'data'		 => array()
		);
	}
	if (empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']])) {
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']] = array(
			'nama'		 => $sub['nama_bidang_urusan'],
			'total' 	 => 0,
			'triwulan_1' => 0,
			'triwulan_2' => 0,
			'triwulan_3' => 0,
			'triwulan_4' => 0,
			'total_simda' => 0,
			'realisasi'  => 0,
			'data'		 => array()
		);
	}

	$nama = explode(' ', $sub['nama_sub_giat']);
	if ($nama[0] !== $sub['kode_sub_giat']) {
		$kode_sub_giat_asli = explode('.', $sub['kode_sub_giat']);
	} else {
		$kode_sub_giat_asli = explode('.', $nama[0]);
	}

	//program
	if (empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']])) {
		$capaian_prog = $wpdb->get_results(
			$wpdb->prepare("
				SELECT * 
				FROM data_capaian_prog_sub_keg 
				WHERE tahun_anggaran=%d 
				  AND active=1 
				  AND kode_sbl=%s 
				  AND capaianteks !='' 
				ORDER BY id ASC 
			", $input['tahun_anggaran'], $sub['kode_sbl']),
			ARRAY_A
		);

		$kode_sbl = $kode_sbl_s[0] . '.' . $kode_sbl_s[1] . '.' . $kode_sbl_s[2];
		$realisasi_renja = $wpdb->get_results(
			$wpdb->prepare("
				SELECT * 
				FROM data_realisasi_renja 
				WHERE tahun_anggaran=%d 
				  AND tipe_indikator=%d 
				  AND kode_sbl=%s 
			", $input['tahun_anggaran'], 3, $kode_sbl),
			ARRAY_A
		);
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']] = array(
			'nama'				   => $sub['nama_program'],
			'indikator' 		   => $capaian_prog,
			'realisasi_indikator'  => $realisasi_renja,
			'id_program' 		   => $sub['id_program'],
			'kode_program' 		   => str_replace($sub['kode_bidang_urusan'], '', $sub['kode_program']),
			'kode_sbl' 			   => $sub['kode_sbl'],
			'kode_urusan_bidang'   => $kode_sub_giat_asli[0] . '.' . $kode_sub_giat_asli[1] . '.' . $kode_sub_giat_asli[2],
			'total' 			   => 0,
			'triwulan_1' 		   => 0,
			'triwulan_2' 		   => 0,
			'triwulan_3' 		   => 0,
			'triwulan_4' 		   => 0,
			'rak_triwulan_1' 	   => 0,
			'rak_triwulan_2' 	   => 0,
			'rak_triwulan_3' 	   => 0,
			'rak_triwulan_4' 	   => 0,
			'total_simda' 		   => 0,
			'realisasi' 		   => 0,
			'data'				   => array()
		);
	}
	//kegiatan
	if (empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']])) {
		$output_giat = $wpdb->get_results(
			$wpdb->prepare("
				SELECT * 
				FROM data_output_giat_sub_keg 
				WHERE tahun_anggaran=%d 
				  AND kode_sbl=%s 
				  AND active=1 
				ORDER BY id ASC 
			", $input['tahun_anggaran'], $sub['kode_sbl']),
			ARRAY_A
		);

		$kode_sbl = $kode_sbl_s[0] . '.' . $kode_sbl_s[1] . '.' . $kode_sbl_s[2] . '.' . $kode_sbl_s[3];
		$realisasi_renja = $wpdb->get_results(
			$wpdb->prepare("
				SELECT * 
				FROM data_realisasi_renja 
				WHERE tahun_anggaran=%d 
				  AND tipe_indikator=%d 
				  AND kode_sbl=%s 
			", $input['tahun_anggaran'], 2, $kode_sbl),
			ARRAY_A
		);
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']] = array(
			'nama'					=> $sub['nama_giat'],
			'indikator' 			=> $output_giat,
			'id_giat' 				=> $sub['id_giat'],
			'realisasi_indikator' 	=> $realisasi_renja,
			'kode_sbl' 				=> $sub['kode_sbl'],
			'kode_giat' 			=> str_replace($sub['kode_bidang_urusan'], '', $sub['kode_giat']),
			'kode_urusan_bidang' 	=> $kode_sub_giat_asli[0] . '.' . $kode_sub_giat_asli[1] . '.' . $kode_sub_giat_asli[2] . '.' . $kode_sub_giat_asli[3] . '.' . $kode_sub_giat_asli[4],
			'total' 				=> 0,
			'triwulan_1' 			=> 0,
			'triwulan_2' 			=> 0,
			'triwulan_3' 			=> 0,
			'triwulan_4' 			=> 0,
			'total_simda' 			=> 0,
			'realisasi' 			=> 0,
			'data'					=> array()
		);
	}
	//subkegiatan
	if (empty($data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']])) {
		$output_sub_giat = $wpdb->get_results(
			$wpdb->prepare("
				SELECT * 
				FROM data_sub_keg_indikator 
				WHERE tahun_anggaran=%d 
				  AND active=1 
				  AND kode_sbl=%s 
				ORDER BY id DESC 
			", $input['tahun_anggaran'], $sub['kode_sbl']),
			ARRAY_A
		);

		$realisasi_renja = $wpdb->get_results(
			$wpdb->prepare("
				SELECT * 
				FROM data_realisasi_renja 
				WHERE tahun_anggaran=%d 
				  AND tipe_indikator=%d 
				  AND kode_sbl=%s 
			", $input['tahun_anggaran'], 1, $sub['kode_sbl']),
			ARRAY_A
		);
		$nama = explode(' ', $sub['nama_sub_giat']);
		unset($nama[0]);
		$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']] = array(
			'nama'					 => implode(' ', $nama),
			'indikator' 			 => $output_sub_giat,
			'kode_sub_giat' 		 => str_replace($sub['kode_bidang_urusan'], '', $sub['kode_sub_giat']),
			'id_sub_giat' 			 => $sub['id_sub_giat'],
			'realisasi_indikator' 	 => $realisasi_renja,
			'total' 				 => 0,
			'triwulan_1' 			 => 0,
			'triwulan_2' 			 => 0,
			'triwulan_3' 			 => 0,
			'triwulan_4' 			 => 0,
			'total_simda' 			 => 0,
			'realisasi' 			 => 0,
			'data'					 => $sub
		);
	}
	$data_all['total'] += $total_pagu;
	$data_all['data'][$sub['kode_urusan']]['total'] += $total_pagu;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total'] += $total_pagu;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total'] += $total_pagu;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total'] += $total_pagu;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total'] += $total_pagu;

	$data_all['realisasi'] += $realisasi;
	$data_all['data'][$sub['kode_urusan']]['realisasi'] += $realisasi;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['realisasi'] += $realisasi;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['realisasi'] += $realisasi;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['realisasi'] += $realisasi;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['realisasi'] += $realisasi;

	$data_all['total_simda'] += $total_simda;
	$data_all['data'][$sub['kode_urusan']]['total_simda'] += $total_simda;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['total_simda'] += $total_simda;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['total_simda'] += $total_simda;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['total_simda'] += $total_simda;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['total_simda'] += $total_simda;

	$data_all['triwulan_1'] += $triwulan_1;
	$data_all['data'][$sub['kode_urusan']]['triwulan_1'] += $triwulan_1;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_1'] += $triwulan_1;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_1'] += $triwulan_1;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['triwulan_1'] += $triwulan_1;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['triwulan_1'] += $triwulan_1;

	$data_all['triwulan_2'] += $triwulan_2;
	$data_all['data'][$sub['kode_urusan']]['triwulan_2'] += $triwulan_2;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_2'] += $triwulan_2;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_2'] += $triwulan_2;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['triwulan_2'] += $triwulan_2;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['triwulan_2'] += $triwulan_2;

	$data_all['triwulan_3'] += $triwulan_3;
	$data_all['data'][$sub['kode_urusan']]['triwulan_3'] += $triwulan_3;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_3'] += $triwulan_3;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_3'] += $triwulan_3;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['triwulan_3'] += $triwulan_3;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['triwulan_3'] += $triwulan_3;

	$data_all['triwulan_4'] += $triwulan_4;
	$data_all['data'][$sub['kode_urusan']]['triwulan_4'] += $triwulan_4;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['triwulan_4'] += $triwulan_4;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['triwulan_4'] += $triwulan_4;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['triwulan_4'] += $triwulan_4;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['triwulan_4'] += $triwulan_4;

	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['rak_triwulan_1'] += $rak_triwulan_1;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['rak_triwulan_2'] += $rak_triwulan_2;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['rak_triwulan_3'] += $rak_triwulan_3;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['rak_triwulan_4'] += $rak_triwulan_4;

	$data_all['rak_triwulan_1'] += $rak_triwulan_1;
	$data_all['rak_triwulan_2'] += $rak_triwulan_2;
	$data_all['rak_triwulan_3'] += $rak_triwulan_3;
	$data_all['rak_triwulan_4'] += $rak_triwulan_4;
}

$persen_triwulan_1 = 0;
$persen_triwulan_2 = 0;
$persen_triwulan_3 = 0;
$persen_triwulan_4 = 0;
if (!empty($data_all['rak_triwulan_1']) && !empty($data_all['triwulan_1'])) {
	$persen_triwulan_1 = ($data_all['triwulan_1'] / $data_all['rak_triwulan_1']) * 100;
}
if (!empty($data_all['rak_triwulan_2']) && !empty($data_all['triwulan_2'])) {
	$persen_triwulan_2 = ($data_all['triwulan_2'] / $data_all['rak_triwulan_2']) * 100;
}
if (!empty($data_all['rak_triwulan_3']) && !empty($data_all['triwulan_3'])) {
	$persen_triwulan_3 = ($data_all['triwulan_3'] / $data_all['rak_triwulan_3']) * 100;
}
if (!empty($data_all['rak_triwulan_4']) && !empty($data_all['triwulan_4'])) {
	$persen_triwulan_4 = ($data_all['triwulan_4'] / $data_all['rak_triwulan_4']) * 100;
}

$renstra_program = $wpdb->get_results(
	$wpdb->prepare("
		SELECT * 
		FROM data_renstra_program 
		WHERE id_jadwal=%d 
		  AND tahun_anggaran=%d 
		  AND active=1 
		  AND id_unit=%d
	", $data_jadwal_renstra['id_jadwal_lokal'], $input['tahun_anggaran'], $unit[0]['id_unit']),
	ARRAY_A
);
$renstra_program_id = array();
$renstra_program_kode = array();
foreach ($renstra_program as $prog) {
	if (empty($renstra_program_id[$prog['id_program']])) {
		$renstra_program_id[$prog['id_program']] = array();
	}
	$renstra_program_id[$prog['id_program']][] = $prog;
	$kode = str_replace($prog['kode_bidang_urusan'], '', $prog['kode_program']);
	$renstra_program_kode[$kode] = $renstra_program_id[$prog['id_program']];
}

$renstra_keg = $wpdb->get_results(
	$wpdb->prepare("
		SELECT * 
		FROM data_renstra_kegiatan 
		WHERE id_jadwal=%d 
		  AND tahun_anggaran=%d 
		  AND active=1 
		  AND id_unit=%d
	", $data_jadwal_renstra['id_jadwal_lokal'], $input['tahun_anggaran'], $unit[0]['id_unit']),
	ARRAY_A
);
$renstra_keg_id = array();
$renstra_keg_kode = array();
foreach ($renstra_keg as $giat) {
	if (empty($renstra_keg_id[$giat['id_giat']])) {
		$renstra_keg_id[$giat['id_giat']] = array();
	}
	$renstra_keg_id[$giat['id_giat']][] = $giat;
	$kode = str_replace($giat['kode_bidang_urusan'], '', $giat['kode_giat']);
	$renstra_keg_kode[$kode] = $renstra_keg_id[$giat['id_giat']];
}

$renstra_sub_keg = $wpdb->get_results(
	$wpdb->prepare("
		SELECT * 
		FROM data_renstra_sub_kegiatan 
		WHERE id_jadwal=%d 
		  AND tahun_anggaran=%d 
		  AND active=1 
		  AND id_unit=%d 
	", $data_jadwal_renstra['id_jadwal_lokal'], $input['tahun_anggaran'], $unit[0]['id_unit']),
	ARRAY_A
);
$renstra_sub_keg_id = array();
$renstra_sub_keg_kode = array();
foreach ($renstra_sub_keg as $sub_giat) {
	if (empty($renstra_sub_keg_id[$sub_giat['id_sub_giat']])) {
		$renstra_sub_keg_id[$sub_giat['id_sub_giat']] = array();
	}
	$renstra_sub_keg_id[$sub_giat['id_sub_giat']][] = $sub_giat;
	$kode = str_replace($sub_giat['kode_bidang_urusan'], '', $sub_giat['kode_sub_giat']);
	$renstra_sub_keg_kode[$kode] = $renstra_sub_keg_id[$sub_giat['id_sub_giat']];
}

$body_monev   	 = '';
$no_urusan 	  	 = 0;
$no_bidang 	  	 = 0;
$no_program   	 = 0;
$no_kegiatan  	 = 0;
$no_sub_kegiatan = 0;
$data_all_js 	 = array();

$total_pagu_renstra_asli = 0;
$total_pagu_renstra_tahun_ini_asli = 0;
$total_pagu_renstra_tahun_sebelumnya_asli = 0;
$total_capaian_pagu_renstra_tahun_ini_asli = 0;

foreach ($data_all['data'] as $kd_urusan => $urusan) {
	$no_urusan++;
	foreach ($urusan['data'] as $kd_bidang => $bidang) {
		$no_bidang++;
		foreach ($bidang['data'] as $kd_program_asli => $program) {
			$no_program++;
			$kd_program = explode('.', $kd_program_asli);
			$kd_program = $kd_program[count($kd_program) - 1];
			$capaian = 0;
			if (!empty($program['total_simda'])) {
				$capaian = $this->pembulatan(($program['realisasi'] / $program['total_simda']) * 100);
			}
			$bobot_kinerja_indikator 	= array();
			$capaian_prog_js 			= array();
			$target_capaian_prog_js 	= array();
			$satuan_capaian_prog_js 	= array();
			$realisasi_indikator_tw1_js = array();
			$realisasi_indikator_tw2_js = array();
			$realisasi_indikator_tw3_js = array();
			$realisasi_indikator_tw4_js = array();
			$total_tw_js 				= array();
			$capaian_prog 				= array();
			$target_capaian_prog 		= array();
			$satuan_capaian_prog 		= array();
			$realisasi_indikator_tw1 	= array();
			$realisasi_indikator_tw2 	= array();
			$realisasi_indikator_tw3 	= array();
			$realisasi_indikator_tw4 	= array();
			$total_tw 					= array();
			$capaian_realisasi_indikator = array();
			$class_rumus_target 		= array();
			$total_target_renstra_text 	= array();
			$total_target_renstra 		= array();
			$satuan_renstra 			= array();
			$total_pagu_renstra 		= array();
			$total_pagu_renstra_renja 	= array();
			$keterangan 				= array();
			if (!empty($program['indikator'])) {
				$realisasi_indikator = array();
				foreach ($program['realisasi_indikator'] as $k_sub => $v_sub) {
					$realisasi_indikator[$v_sub['id_indikator']] = $v_sub;
				}
				foreach ($program['indikator'] as $k_sub => $v_sub) {
					$keterangan_db = array();
					for ($i = 1; $i <= 12; $i++) {
						if (!empty($v_sub['keterangan_bulan_' . $i])) {
							$keterangan_db[] = $v_sub['keterangan_bulan_' . $i];
						}
					}

					$keterangan[$k_sub] 				 = implode(', ', $keterangan_db);
					$target_capaian_prog_js[$k_sub] 	 = $v_sub['targetcapaian'];
					$bobot_kinerja_indikator[$k_sub] 	 = $v_sub['bobot_kinerja'];
					$satuan_capaian_prog_js[$k_sub] 	 = $v_sub['satuancapaian'];
					$target_capaian_prog[$k_sub] 		 = '<span data-id="' . $k_sub . '" bobot="">' . $v_sub['targetcapaian'] . '</span>';
					$satuan_capaian_prog[$k_sub] 		 = '<span data-id="' . $k_sub . '">' . $v_sub['satuancapaian'] . '</span>';
					$target_indikator 					 = $v_sub['targetcapaian'];
					$realisasi_indikator_tw1[$k_sub] 	 = 0;
					$realisasi_indikator_tw2[$k_sub] 	 = 0;
					$realisasi_indikator_tw3[$k_sub] 	 = 0;
					$realisasi_indikator_tw4[$k_sub] 	 = 0;
					$total_tw[$k_sub] 					 = 0;
					$capaian_realisasi_indikator[$k_sub] = 0;
					$realisasi_indikator_tw1_js[$k_sub]  = 0;
					$realisasi_indikator_tw2_js[$k_sub]  = 0;
					$realisasi_indikator_tw3_js[$k_sub]  = 0;
					$realisasi_indikator_tw4_js[$k_sub]  = 0;
					$total_tw_js[$k_sub] 				 = 0;
					$class_rumus_target[$k_sub] 		 = " positif";

					if (!empty($realisasi_indikator) && !empty($realisasi_indikator[$k_sub])) {
						$rumus_indikator = $realisasi_indikator[$k_sub]['id_rumus_indikator'];
						$max = 0;
						for ($i = 1; $i <= 12; $i++) {
							$realisasi_bulan = $realisasi_indikator[$k_sub]['realisasi_bulan_' . $i];
							if ($max < $realisasi_bulan) {
								$max = $realisasi_bulan;
							}
							$total_tw[$k_sub] += $realisasi_bulan;
							if ($i <= 3) {
								if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
									if ($i == 3) {
										$realisasi_indikator_tw1[$k_sub] = $realisasi_bulan;
									}
								} else {
									$realisasi_indikator_tw1[$k_sub] += $realisasi_bulan;
								}
							} else if ($i <= 6) {
								if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
									if ($i == 6) {
										$realisasi_indikator_tw2[$k_sub] = $realisasi_bulan;
									}
								} else {
									$realisasi_indikator_tw2[$k_sub] += $realisasi_bulan;
								}
							} else if ($i <= 9) {
								if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
									if ($i == 9) {
										$realisasi_indikator_tw3[$k_sub] = $realisasi_bulan;
									}
								} else {
									$realisasi_indikator_tw3[$k_sub] += $realisasi_bulan;
								}
							} else if ($i <= 12) {
								if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
									if ($i == 12) {
										$realisasi_indikator_tw4[$k_sub] = $realisasi_bulan;
									}
								} else {
									$realisasi_indikator_tw4[$k_sub] += $realisasi_bulan;
								}
							}
						}
						if ($rumus_indikator == 1) {
							$class_rumus_target[$k_sub] = "positif";
							if (!empty($target_indikator)) {
								$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($total_tw[$k_sub] / $target_indikator) * 100);
							}
						} else if ($rumus_indikator == 2) {
							$class_rumus_target[$k_sub] = "negatif";
							$total_tw[$k_sub] = $max;
							if (!empty($total_tw[$k_sub])) {
								$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($target_indikator / $total_tw[$k_sub]) * 100);
							}
						} else if ($rumus_indikator == 3 || $rumus_indikator == 4) {
							if ($rumus_indikator == 3) {
								$class_rumus_target[$k_sub] = "persentase";
							} else if ($rumus_indikator == 4) {
								$class_rumus_target[$k_sub] = "nilai_akhir";
							}
							$total_tw[$k_sub] = $max;
							if (!empty($target_indikator)) {
								$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($total_tw[$k_sub] / $target_indikator) * 100);
							}
						}
					}
					$capaian_prog_js[] = $v_sub['capaianteks'];
					$realisasi_indikator_tw1_js[$k_sub] = $realisasi_indikator_tw1[$k_sub];
					$realisasi_indikator_tw2_js[$k_sub] = $realisasi_indikator_tw2[$k_sub];
					$realisasi_indikator_tw3_js[$k_sub] = $realisasi_indikator_tw3[$k_sub];
					$realisasi_indikator_tw4_js[$k_sub] = $realisasi_indikator_tw4[$k_sub];
					$total_tw_js[$k_sub] = $total_tw[$k_sub];

					$realisasi_indikator_tw1[$k_sub] = '<span class="realisasi_indikator_tw1-' . $k_sub . '">' . $realisasi_indikator_tw1[$k_sub] . '</span>';
					$realisasi_indikator_tw2[$k_sub] = '<span class="realisasi_indikator_tw2-' . $k_sub . '">' . $realisasi_indikator_tw2[$k_sub] . '</span>';
					$realisasi_indikator_tw3[$k_sub] = '<span class="realisasi_indikator_tw3-' . $k_sub . '">' . $realisasi_indikator_tw3[$k_sub] . '</span>';
					$realisasi_indikator_tw4[$k_sub] = '<span class="realisasi_indikator_tw4-' . $k_sub . '">' . $realisasi_indikator_tw4[$k_sub] . '</span>';
					$total_tw[$k_sub] = '<span class="total_tw-' . $k_sub . ' rumus_indikator ' . $class_rumus_target[$k_sub] . '">' . $total_tw[$k_sub] . '</span>';
					$capaian_realisasi_indikator[$k_sub] = '<span class="capaian_realisasi_indikator-' . $k_sub . ' rumus_indikator ' . $class_rumus_target[$k_sub] . '">' . $this->pembulatan($capaian_realisasi_indikator[$k_sub]) . '</span>';
					$capaian_prog[] = '<span data-id="' . $k_sub . '" class="rumus_indikator ' . $class_rumus_target[$k_sub] . '">' . $v_sub['capaianteks'] . button_edit_monev($input['tahun_anggaran'] . '-' . $input['id_skpd'] . '-' . $kd_program_asli . '-' . $program['kode_sbl'] . '-' . $k_sub, $v_sub['bobot_kinerja']) . '</span>';
				}
			}

			$data_all_js[] = array(
				'nama' 					  => $kd_program_asli . ' ' . $program['nama'],
				'pagu' 					  => number_format($program['total_simda'], 0, ",", "."),
				'realisasi' 			  => number_format($program['realisasi'], 0, ",", "."),
				'capaian' 				  => $capaian,
				'rak_tw_1' 				  => $program['rak_triwulan_1'],
				'rak_tw_2' 				  => $program['rak_triwulan_2'],
				'rak_tw_3' 				  => $program['rak_triwulan_3'],
				'rak_tw_4' 				  => $program['rak_triwulan_4'],
				'realisasi_tw_1' 		  => $program['triwulan_1'],
				'realisasi_tw_2' 		  => $program['triwulan_2'],
				'realisasi_tw_3' 		  => $program['triwulan_3'],
				'realisasi_tw_4' 		  => $program['triwulan_4'],
				'indikator' 			  => $capaian_prog_js,
				'satuan' 				  => $satuan_capaian_prog_js,
				'bobot_kinerja_indikator' => $bobot_kinerja_indikator,
				'target_indikator' 		  => $target_capaian_prog_js,
				'realisasi_indikator' 	  => $total_tw_js,
				'realisasi_indikator_1'   => $realisasi_indikator_tw1_js,
				'realisasi_indikator_2'   => $realisasi_indikator_tw2_js,
				'realisasi_indikator_3'   => $realisasi_indikator_tw3_js,
				'realisasi_indikator_4'   => $realisasi_indikator_tw4_js,
			);

			$capaian_prog 			 	 = implode('<br>', $capaian_prog);
			$target_capaian_prog 	 	 = implode('<br>', $target_capaian_prog);
			$satuan_capaian_prog 	 	 = implode('<br>', $satuan_capaian_prog);
			$realisasi_indikator_tw1 	 = implode('<br>', $realisasi_indikator_tw1);
			$realisasi_indikator_tw2 	 = implode('<br>', $realisasi_indikator_tw2);
			$realisasi_indikator_tw3 	 = implode('<br>', $realisasi_indikator_tw3);
			$realisasi_indikator_tw4 	 = implode('<br>', $realisasi_indikator_tw4);
			$total_tw 				 	 = implode('<br>', $total_tw);
			$capaian_realisasi_indikator = implode('<br>', $capaian_realisasi_indikator);
			$keterangan 				 = implode('<br>', $keterangan);

			$renstra = array();
			if (!empty($renstra_program_id[$program['id_program']])) {
				$renstra = $renstra_program_id[$program['id_program']];
			} else if (!empty($renstra_program_kode[$program['kode_program']])) {
				$renstra = $renstra_program_kode[$program['kode_program']];
			}
			$data_renstra = $this->get_indikator_renstra_renja(array(
				'renstra' 				 => $renstra,
				'type' 					 => 'program',
				'renja' 				 => $program,
				'default_satuan_renstra' => $satuan_capaian_prog,
				'tahun_renstra' 		 => $tahun_renstra
			));
			$body_monev .= '
			<tr class="tr-program program" data-kode="' . $kd_urusan . '.' . $kd_bidang . '.' . $kd_program . '" data-bidang-urusan="' . $program['kode_urusan_bidang'] . '">
				<td class="kiri kanan bawah text_blok">' . $no_program . '</td>
				<td class="kanan bawah text_blok">' . $data_renstra['renstra_tujuan'] . '</td>
				<td class="kanan bawah text_blok">' . $data_renstra['renstra_sasaran'] . '</td>
				<td class="kanan bawah text_blok">' . $kd_program_asli . '</td>
				<td class="kanan bawah text_blok nama">' . $program['nama'] . '</td>
				<td class="kanan bawah text_blok indikator">' . $capaian_prog . '</td>
				<td class="text_tengah kanan bawah text_blok total_renstra">' . $data_renstra['total_target_renstra_text'] . '</td>
				<td class="text_tengah kanan bawah text_blok total_renstra">' . $data_renstra['satuan_renstra'] . '</td>
				<td class="text_kanan kanan bawah text_blok total_renstra">' . $data_renstra['total_pagu_renstra'] . '</td>
				<td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu">' . $data_renstra['total_target_renstra_tahun_sebelumnya'] . '</td>
				<td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu">' . $data_renstra['satuan_renstra'] . '</td>
				<td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_lalu">' . $data_renstra['total_pagu_renstra_tahun_sebelumnya'] . '</td>
				<td class="text_tengah kanan bawah text_blok total_renja target_indikator">' . $target_capaian_prog . '</td>
				<td class="text_tengah kanan bawah text_blok total_renja satuan_indikator">' . $satuan_capaian_prog . '</td>
				<td class="text_kanan kanan bawah text_blok total_renja pagu_renja" data-pagu="' . $program['total_simda'] . '">' . number_format($program['total_simda'], 0, ",", ".") . '</td>
				<td class="text_tengah kanan bawah text_blok triwulan_1">' . $realisasi_indikator_tw1 . '</td>
				<td class="text_tengah kanan bawah text_blok triwulan_1">' . $satuan_capaian_prog . '</td>
				<td class="text_kanan kanan bawah text_blok triwulan_1"><span class="nilai_realisasi_tw1">' . number_format($program['triwulan_1'], 0, ",", ".") . '</span></td>
				<td class="text_tengah kanan bawah text_blok triwulan_2">' . $realisasi_indikator_tw2 . '</td>
				<td class="text_tengah kanan bawah text_blok triwulan_2">' . $satuan_capaian_prog . '</td>
				<td class="text_kanan kanan bawah text_blok triwulan_2"><span class="nilai_realisasi_tw2">' . number_format($program['triwulan_2'], 0, ",", ".") . '</span></td>
				<td class="text_tengah kanan bawah text_blok triwulan_3">' . $realisasi_indikator_tw3 . '</td>
				<td class="text_tengah kanan bawah text_blok triwulan_3">' . $satuan_capaian_prog . '</td>
				<td class="text_kanan kanan bawah text_blok triwulan_3"><span class="nilai_realisasi_tw3">' . number_format($program['triwulan_3'], 0, ",", ".") . '</span></td>
				<td class="text_tengah kanan bawah text_blok triwulan_4">' . $realisasi_indikator_tw4 . '</td>
				<td class="text_tengah kanan bawah text_blok triwulan_4">' . $satuan_capaian_prog . '</td>
				<td class="text_kanan kanan bawah text_blok triwulan_4"><span class="nilai_realisasi_tw4">' . number_format($program['triwulan_4'], 0, ",", ".") . '</span></td>
				<td class="text_tengah kanan bawah text_blok realisasi_renja">' . $total_tw . '</td>
				<td class="text_tengah kanan bawah text_blok realisasi_renja">' . $satuan_capaian_prog . '</td>
				<td class="text_kanan kanan bawah text_blok realisasi_renja pagu_renja_realisasi" data-pagu="' . $program['realisasi'] . '"><span class="nilai_realisasi_renja">' . number_format($program['realisasi'], 0, ",", ".") . '</span></td>
				<td class="text_tengah kanan bawah text_blok capaian_renja">' . $capaian_realisasi_indikator . '</td>
				<td class="text_kanan kanan bawah text_blok capaian_renja">' . $capaian . '</td>
				<td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan">' . $data_renstra['total_target_renstra_tahun_ini'] . '</td>
				<td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan">' . $data_renstra['satuan_renstra'] . '</td>
				<td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_berjalan">' . $data_renstra['total_pagu_renstra_tahun_ini'] . '</td>
				<td class="text_tengah kanan bawah text_blok capaian_renstra_tahun_berjalan">' . $data_renstra['total_capaian_target_renstra_tahun_ini'] . '</td>
				<td class="text_kanan kanan bawah text_blok capaian_renstra_tahun_berjalan">' . $data_renstra['total_capaian_pagu_renstra_tahun_ini'] . '</td>
				<td class="kanan bawah text_blok">' . $unit[0]['nama_skpd'] . '</td>
				<td class="kanan bawah text_blok">' . $keterangan . '</td>
			</tr>
			';
			foreach ($program['data'] as $kd_giat1 => $giat) {
				$no_kegiatan++;
				$kd_giat = explode('.', $kd_giat1);
				$kd_giat = $kd_giat[count($kd_giat) - 2] . '.' . $kd_giat[count($kd_giat) - 1];
				$capaian = 0;
				if (!empty($giat['total_simda'])) {
					$capaian = $this->pembulatan(($giat['realisasi'] / $giat['total_simda']) * 100);
				}
				$output_giat 			 	 = array();
				$target_output_giat 	 	 = array();
				$satuan_output_giat 	 	 = array();
				$realisasi_indikator_tw1 	 = array();
				$realisasi_indikator_tw2 	 = array();
				$realisasi_indikator_tw3 	 = array();
				$realisasi_indikator_tw4 	 = array();
				$total_tw 				 	 = array();
				$capaian_realisasi_indikator = array();
				$class_rumus_target 		 = array();
				$keterangan 				 = array();
				if (!empty($giat['indikator'])) {
					$realisasi_indikator = array();
					foreach ($giat['realisasi_indikator'] as $k_sub => $v_sub) {
						$realisasi_indikator[$v_sub['id_indikator']] = $v_sub;
					}
					foreach ($giat['indikator'] as $k_sub => $v_sub) {
						$keterangan_db = array();
						for ($i = 1; $i <= 12; $i++) {
							if (!empty($v_sub['keterangan_bulan_' . $i])) {
								$keterangan_db[] = $v_sub['keterangan_bulan_' . $i];
							}
						}
						$keterangan[$k_sub] = implode(', ', $keterangan_db);
						$target_output_giat[$k_sub] = ' <span data-id="' . $k_sub . '">' . $v_sub['targetoutput'] . '</span>';
						$satuan_output_giat[$k_sub] = '<span data-id="' . $k_sub . '">' . $v_sub['satuanoutput'] . '</span>';
						$target_indikator = $v_sub['targetoutput'];
						$realisasi_indikator_tw1[$k_sub] = 0;
						$realisasi_indikator_tw2[$k_sub] = 0;
						$realisasi_indikator_tw3[$k_sub] = 0;
						$realisasi_indikator_tw4[$k_sub] = 0;
						$total_tw[$k_sub] = 0;
						$capaian_realisasi_indikator[$k_sub] = 0;
						$class_rumus_target[$k_sub] = "positif";

						if (
							!empty($realisasi_indikator)
							&& !empty($realisasi_indikator[$k_sub])
						) {
							$rumus_indikator = $realisasi_indikator[$k_sub]['id_rumus_indikator'];
							$max = 0;
							for ($i = 1; $i <= 12; $i++) {
								$realisasi_bulan = $realisasi_indikator[$k_sub]['realisasi_bulan_' . $i];
								if ($max < $realisasi_bulan) {
									$max = $realisasi_bulan;
								}
								$total_tw[$k_sub] += $realisasi_bulan;
								if ($i <= 3) {
									if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
										if ($i == 3) {
											$realisasi_indikator_tw1[$k_sub] = $realisasi_bulan;
										}
									} else {
										$realisasi_indikator_tw1[$k_sub] += $realisasi_bulan;
									}
								} else if ($i <= 6) {
									if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
										if ($i == 6) {
											$realisasi_indikator_tw2[$k_sub] = $realisasi_bulan;
										}
									} else {
										$realisasi_indikator_tw2[$k_sub] += $realisasi_bulan;
									}
								} else if ($i <= 9) {
									if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
										if ($i == 9) {
											$realisasi_indikator_tw3[$k_sub] = $realisasi_bulan;
										}
									} else {
										$realisasi_indikator_tw3[$k_sub] += $realisasi_bulan;
									}
								} else if ($i <= 12) {
									if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
										if ($i == 12) {
											$realisasi_indikator_tw4[$k_sub] = $realisasi_bulan;
										}
									} else {
										$realisasi_indikator_tw4[$k_sub] += $realisasi_bulan;
									}
								}
							}
							if ($rumus_indikator == 1) {
								$class_rumus_target[$k_sub] = "positif";
								if (!empty($target_indikator)) {
									$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($total_tw[$k_sub] / $target_indikator) * 100);
								}
							} else if ($rumus_indikator == 2) {
								$class_rumus_target[$k_sub] = "negatif";
								$total_tw[$k_sub] = $max;
								if (!empty($total_tw[$k_sub])) {
									$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($target_indikator / $total_tw[$k_sub]) * 100);
								}
							} else if ($rumus_indikator == 3 || $rumus_indikator == 4) {
								if ($rumus_indikator == 3) {
									$class_rumus_target[$k_sub] = "persentase";
								} else if ($rumus_indikator == 4) {
									$class_rumus_target[$k_sub] = "nilai_akhir";
								}
								$total_tw[$k_sub] = $max;
								if (!empty($target_indikator)) {
									$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($total_tw[$k_sub] / $target_indikator) * 100);
								}
							}
						}

						$realisasi_indikator_tw1[$k_sub] = '<span class="realisasi_indikator_tw1-' . $k_sub . '">' . $realisasi_indikator_tw1[$k_sub] . '</span>';
						$realisasi_indikator_tw2[$k_sub] = '<span class="realisasi_indikator_tw2-' . $k_sub . '">' . $realisasi_indikator_tw2[$k_sub] . '</span>';
						$realisasi_indikator_tw3[$k_sub] = '<span class="realisasi_indikator_tw3-' . $k_sub . '">' . $realisasi_indikator_tw3[$k_sub] . '</span>';
						$realisasi_indikator_tw4[$k_sub] = '<span class="realisasi_indikator_tw4-' . $k_sub . '">' . $realisasi_indikator_tw4[$k_sub] . '</span>';
						$total_tw[$k_sub] = '<span class="total_tw-' . $k_sub . ' rumus_indikator ' . $class_rumus_target[$k_sub] . '">' . $total_tw[$k_sub] . '</span>';
						$capaian_realisasi_indikator[$k_sub] = '<span class="capaian_realisasi_indikator-' . $k_sub . ' rumus_indikator ' . $class_rumus_target[$k_sub] . '">' . $this->pembulatan($capaian_realisasi_indikator[$k_sub]) . '</span>';
						$output_giat[] = '<span data-id="' . $k_sub . '" class="rumus_indikator ' . $class_rumus_target[$k_sub] . '">' . $v_sub['outputteks'] . button_edit_monev($input['tahun_anggaran'] . '-' . $input['id_skpd'] . '-' . $kd_giat1 . '-' . $giat['kode_sbl'] . '-' . $k_sub) . '</span>';
					}
				}
				$output_giat = implode('<br>', $output_giat);
				$target_output_giat = implode('<br>', $target_output_giat);
				$satuan_output_giat = implode('<br>', $satuan_output_giat);
				$realisasi_indikator_tw1 = implode('<br>', $realisasi_indikator_tw1);
				$realisasi_indikator_tw2 = implode('<br>', $realisasi_indikator_tw2);
				$realisasi_indikator_tw3 = implode('<br>', $realisasi_indikator_tw3);
				$realisasi_indikator_tw4 = implode('<br>', $realisasi_indikator_tw4);
				$total_tw = implode('<br>', $total_tw);
				$capaian_realisasi_indikator = implode('<br>', $capaian_realisasi_indikator);
				$keterangan = implode('<br>', $keterangan);

				$renstra = array();
				if (!empty($renstra_keg_id[$giat['id_giat']])) {
					$renstra = $renstra_keg_id[$giat['id_giat']];
				} else if (!empty($renstra_keg_kode[$giat['kode_giat']])) {
					$renstra = $renstra_keg_kode[$giat['kode_giat']];
				}
				$data_renstra = $this->get_indikator_renstra_renja(array(
					'renstra' 				 => $renstra,
					'type' 					 => 'kegiatan',
					'renja' 				 => $giat,
					'default_satuan_renstra' => $satuan_output_giat,
					'tahun_renstra' 		 => $tahun_renstra
				));
				$body_monev .= '
					<tr class="tr-kegiatan kegiatan" data-kode="' . $kd_urusan . '.' . $kd_bidang . '.' . $kd_program . '.' . $kd_giat . '" data-kode_giat="' . $kd_giat1 . '" data-bidang-urusan="' . $giat['kode_urusan_bidang'] . '">
						<td class="kiri kanan bawah text_blok">' . $no_program . '.' . $no_kegiatan . '</td>
						<td class="kanan bawah text_blok">' . $data_renstra['renstra_tujuan'] . '</td>
						<td class="kanan bawah text_blok">' . $data_renstra['renstra_sasaran'] . '</td>
						<td class="kanan bawah text_blok">' . $kd_giat1 . '</td>
						<td class="kanan bawah text_blok nama">' . $giat['nama'] . '</td>
						<td class="kanan bawah text_blok indikator">' . $output_giat . '</td>
						<td class="text_tengah kanan bawah text_blok total_renstra">' . $data_renstra['total_target_renstra_text'] . '</td>
						<td class="text_tengah kanan bawah text_blok total_renstra">' . $data_renstra['satuan_renstra'] . '</td>
						<td class="text_kanan kanan bawah text_blok total_renstra">' . $data_renstra['total_pagu_renstra'] . '</td>
						<td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu">' . $data_renstra['total_target_renstra_tahun_sebelumnya'] . '</td>
						<td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_lalu">' . $data_renstra['satuan_renstra'] . '</td>
						<td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_lalu">' . $data_renstra['total_pagu_renstra_tahun_sebelumnya'] . '</td>
						<td class="text_tengah kanan bawah text_blok total_renja target_indikator">' . $target_output_giat . '</td>
						<td class="text_tengah kanan bawah text_blok total_renja satuan_indikator">' . $satuan_output_giat . '</td>
						<td class="text_kanan kanan bawah text_blok total_renja pagu_renja" data-pagu="' . $giat['total_simda'] . '">' . number_format($giat['total_simda'], 0, ",", ".") . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_1">' . $realisasi_indikator_tw1 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_1">' . $satuan_output_giat . '</td>
						<td class="text_kanan kanan bawah text_blok triwulan_1"><span class="nilai_realisasi_tw1">' . number_format($giat['triwulan_1'], 0, ",", ".") . '</span></td>
						<td class="text_tengah kanan bawah text_blok triwulan_2">' . $realisasi_indikator_tw2 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_2">' . $satuan_output_giat . '</td>
						<td class="text_kanan kanan bawah text_blok triwulan_2"><span class="nilai_realisasi_tw2">' . number_format($giat['triwulan_2'], 0, ",", ".") . '</span></td>
						<td class="text_tengah kanan bawah text_blok triwulan_3">' . $realisasi_indikator_tw3 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_3">' . $satuan_output_giat . '</td>
						<td class="text_kanan kanan bawah text_blok triwulan_3"><span class="nilai_realisasi_tw3">' . number_format($giat['triwulan_3'], 0, ",", ".") . '</span></td>
						<td class="text_tengah kanan bawah text_blok triwulan_4">' . $realisasi_indikator_tw4 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_4">' . $satuan_output_giat . '</td>
						<td class="text_kanan kanan bawah text_blok triwulan_4"><span class="nilai_realisasi_tw4">' . number_format($giat['triwulan_4'], 0, ",", ".") . '</span></td>
						<td class="text_tengah kanan bawah text_blok realisasi_renja">' . $total_tw . '</td>
						<td class="text_tengah kanan bawah text_blok realisasi_renja">' . $satuan_output_giat . '</td>
						<td class="text_kanan kanan bawah text_blok realisasi_renja pagu_renja_realisasi" data-pagu="' . $giat['realisasi'] . '"><span class="nilai_realisasi_renja">' . number_format($giat['realisasi'], 0, ",", ".") . '</span></td>
						<td class="text_tengah kanan bawah text_blok capaian_renja">' . $capaian_realisasi_indikator . '</td>
						<td class="text_kanan kanan bawah text_blok capaian_renja">' . $capaian . '</td>
						<td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan">' . $data_renstra['total_target_renstra_tahun_ini'] . '</td>
						<td class="text_tengah kanan bawah text_blok realisasi_renstra_tahun_berjalan">' . $data_renstra['satuan_renstra'] . '</td>
						<td class="text_kanan kanan bawah text_blok realisasi_renstra_tahun_berjalan">' . $data_renstra['total_pagu_renstra_tahun_ini'] . '</td>
						<td class="text_tengah kanan bawah capaian_renstra_tahun_berjalan">' . $data_renstra['total_capaian_target_renstra_tahun_ini'] . '</td>
						<td class="text_kanan kanan bawah capaian_renstra_tahun_berjalan">' . $data_renstra['total_capaian_pagu_renstra_tahun_ini'] . '</td>
						<td class="kanan bawah text_blok">' . $unit[0]['nama_skpd'] . '</td>
						<td class="kanan bawah">' . $keterangan . '</td>
					</tr>
					';
				foreach ($giat['data'] as $kd_sub_giat1 => $sub_giat) {
					$no_sub_kegiatan++;
					$kd_sub_giat = explode('.', $kd_sub_giat1);
					$kd_sub_giat = $kd_sub_giat[count($kd_sub_giat) - 1];
					$capaian = 0;
					if (!empty($sub_giat['total_simda'])) {
						$capaian = $this->pembulatan(($sub_giat['realisasi'] / $sub_giat['total_simda']) * 100);
					}
					$output_sub_giat = array();
					$target_output_sub_giat = array();
					$satuan_output_sub_giat = array();
					$realisasi_indikator_tw1 = array();
					$realisasi_indikator_tw2 = array();
					$realisasi_indikator_tw3 = array();
					$realisasi_indikator_tw4 = array();
					$total_tw = array();
					$capaian_realisasi_indikator = array();
					$class_rumus_target = array();
					$keterangan = array();
					if (!empty($sub_giat['indikator'])) {
						$realisasi_indikator = array();
						foreach ($sub_giat['realisasi_indikator'] as $k_sub => $v_sub) {
							$realisasi_indikator[$v_sub['id_indikator']] = $v_sub;
						}
						foreach ($sub_giat['indikator'] as $k_sub => $v_sub) {
							$keterangan_db = array();
							for ($i = 1; $i <= 12; $i++) {
								if (!empty($v_sub['keterangan_bulan_' . $i])) {
									$keterangan_db[] = $v_sub['keterangan_bulan_' . $i];
								}
							}
							$keterangan[$k_sub] = implode(', ', $keterangan_db);
							$target_output_sub_giat[] = ' <span data-id="' . $v_sub['idoutputbl'] . '">' . $v_sub['targetoutput'] . '</span>';
							$satuan_output_sub_giat[] = '<span data-id="' . $v_sub['idoutputbl'] . '">' . $v_sub['satuanoutput'] . '</span>';
							$target_indikator = $v_sub['targetoutput'];
							$realisasi_indikator_tw1[$k_sub] = 0;
							$realisasi_indikator_tw2[$k_sub] = 0;
							$realisasi_indikator_tw3[$k_sub] = 0;
							$realisasi_indikator_tw4[$k_sub] = 0;
							$total_tw[$k_sub] = 0;
							$capaian_realisasi_indikator[$k_sub] = 0;
							$class_rumus_target[$k_sub] = "positif";
							if (
								!empty($realisasi_indikator)
								&& !empty($realisasi_indikator[$v_sub['idoutputbl']])
							) {
								$rumus_indikator = $realisasi_indikator[$v_sub['idoutputbl']]['id_rumus_indikator'];
								$max = 0;
								for ($i = 1; $i <= 12; $i++) {
									$realisasi_bulan = $realisasi_indikator[$v_sub['idoutputbl']]['realisasi_bulan_' . $i];
									if ($max < $realisasi_bulan) {
										$max = $realisasi_bulan;
									}
									$total_tw[$k_sub] += $realisasi_bulan;
									if ($i <= 3) {
										if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
											if ($i == 3) {
												$realisasi_indikator_tw1[$k_sub] = $realisasi_bulan;
											}
										} else {
											$realisasi_indikator_tw1[$k_sub] += $realisasi_bulan;
										}
									} else if ($i <= 6) {
										if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
											if ($i == 6) {
												$realisasi_indikator_tw2[$k_sub] = $realisasi_bulan;
											}
										} else {
											$realisasi_indikator_tw2[$k_sub] += $realisasi_bulan;
										}
									} else if ($i <= 9) {
										if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
											if ($i == 9) {
												$realisasi_indikator_tw3[$k_sub] = $realisasi_bulan;
											}
										} else {
											$realisasi_indikator_tw3[$k_sub] += $realisasi_bulan;
										}
									} else if ($i <= 12) {
										if ($rumus_indikator == 3 || $rumus_indikator == 2 || $rumus_indikator == 4) {
											if ($i == 12) {
												$realisasi_indikator_tw4[$k_sub] = $realisasi_bulan;
											}
										} else {
											$realisasi_indikator_tw4[$k_sub] += $realisasi_bulan;
										}
									}
								}
								if ($rumus_indikator == 1) {
									$class_rumus_target[$k_sub] = "positif";
									if (
										!empty($target_indikator)
										&& !empty($total_tw[$k_sub])
									) {
										$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($total_tw[$k_sub] / $target_indikator) * 100);
									}
								} else if ($rumus_indikator == 2) {
									$class_rumus_target[$k_sub] = "negatif";
									$total_tw[$k_sub] = $max;
									if (
										!empty($target_indikator)
										&& !empty($total_tw[$k_sub])
									) {
										$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($target_indikator / $total_tw[$k_sub]) * 100);
									}
								} else if ($rumus_indikator == 3 || $rumus_indikator == 4) {
									if ($rumus_indikator == 3) {
										$class_rumus_target[$k_sub] = "persentase";
									} else if ($rumus_indikator == 4) {
										$class_rumus_target[$k_sub] = "nilai_akhir";
									}
									$total_tw[$k_sub] = $max;
									if (
										!empty($target_indikator)
										&& !empty($total_tw[$k_sub])
									) {
										$capaian_realisasi_indikator[$k_sub] = $this->pembulatan(($total_tw[$k_sub] / $target_indikator) * 100);
									}
								}
							}
							$output_sub_giat[] = '<span data-id="' . $v_sub['idoutputbl'] . '" class="rumus_indikator ' . $class_rumus_target[$k_sub] . '">' . $v_sub['outputteks'] . button_edit_monev($input['tahun_anggaran'] . '-' . $input['id_skpd'] . '-' . $kd_sub_giat1 . '-' . $sub_giat['data']['kode_sbl'] . '-' . $v_sub['idoutputbl']) . '</span>';
							$realisasi_indikator_tw1[$k_sub] = '<span class="realisasi_indikator_tw1-' . $v_sub['idoutputbl'] . '">' . $realisasi_indikator_tw1[$k_sub] . '</span>';
							$realisasi_indikator_tw2[$k_sub] = '<span class="realisasi_indikator_tw2-' . $v_sub['idoutputbl'] . '">' . $realisasi_indikator_tw2[$k_sub] . '</span>';
							$realisasi_indikator_tw3[$k_sub] = '<span class="realisasi_indikator_tw3-' . $v_sub['idoutputbl'] . '">' . $realisasi_indikator_tw3[$k_sub] . '</span>';
							$realisasi_indikator_tw4[$k_sub] = '<span class="realisasi_indikator_tw4-' . $v_sub['idoutputbl'] . '">' . $realisasi_indikator_tw4[$k_sub] . '</span>';
							$total_tw[$k_sub] = '<span class="total_tw-' . $v_sub['idoutputbl'] . ' rumus_indikator ' . $class_rumus_target[$k_sub] . '">' . $total_tw[$k_sub] . '</span>';
							$capaian_realisasi_indikator[$k_sub] = '<span class="capaian_realisasi_indikator-' . $v_sub['idoutputbl'] . ' rumus_indikator ' . $class_rumus_target[$k_sub] . '">' . $capaian_realisasi_indikator[$k_sub] . '</span>';
						}
					}
					$output_sub_giat = implode('<br>', $output_sub_giat);
					$target_output_sub_giat = implode('<br>', $target_output_sub_giat);
					$satuan_output_sub_giat = implode('<br>', $satuan_output_sub_giat);
					$realisasi_indikator_tw1 = implode('<br>', $realisasi_indikator_tw1);
					$realisasi_indikator_tw2 = implode('<br>', $realisasi_indikator_tw2);
					$realisasi_indikator_tw3 = implode('<br>', $realisasi_indikator_tw3);
					$realisasi_indikator_tw4 = implode('<br>', $realisasi_indikator_tw4);
					$total_tw = implode('<br>', $total_tw);
					$capaian_realisasi_indikator = implode('<br>', $capaian_realisasi_indikator);
					$keterangan = implode('<br>', $keterangan);
					$url_rka_sipd = $this->generatePage('Data RKA SIPD | ' . $sub_giat['data']['kode_sbl'] . ' | ' . $sub_giat['data']['tahun_anggaran'], $sub_giat['data']['tahun_anggaran'], '[input_rka_sipd id_skpd="' . $sub_giat['data']['id_sub_skpd'] . '" kode_sbl="' . $sub_giat['data']['kode_sbl'] . '" tahun_anggaran="' . $sub_giat['data']['tahun_anggaran'] . '"]');
					$nama_sub = '<a href="' . $url_rka_sipd . '" target="_blank">' . $sub_giat['nama'] . '</a>';

					$renstra = array();
					if (!empty($renstra_sub_keg_id[$sub_giat['id_sub_giat']])) {
						$renstra = $renstra_sub_keg_id[$sub_giat['id_sub_giat']];
					} else if (!empty($renstra_sub_keg_kode[$sub_giat['kode_sub_giat']])) {
						$renstra = $renstra_sub_keg_kode[$sub_giat['kode_sub_giat']];
					}
					$data_renstra = $this->get_indikator_renstra_renja(array(
						'renstra' => $renstra,
						'type' => 'sub_kegiatan',
						'renja' => $sub_giat,
						'default_satuan_renstra' => $satuan_output_sub_giat,
						'tahun_renstra' => $tahun_renstra
					));
					$body_monev .= '
							<tr class="tr-sub-kegiatan sub_kegiatan" data-kode="' . $kd_urusan . '.' . $kd_bidang . '.' . $kd_program . '.' . $kd_giat . '.' . $kd_sub_giat . '">
								<td class="kiri kanan bawah">' . $no_program . '.' . $no_kegiatan . '.' . $no_sub_kegiatan . '</td>
								<td class="kanan bawah">' . $data_renstra['renstra_tujuan'] . '</td>
								<td class="kanan bawah">' . $data_renstra['renstra_sasaran'] . '</td>
								<td class="kanan bawah">' . $kd_sub_giat1 . '</td>
								<td class="kanan bawah nama">' . $nama_sub . '</td>
								<td class="kanan bawah indikator">' . $output_sub_giat . '</td>
								<td class="text_tengah kanan bawah total_renstra">' . $data_renstra['total_target_renstra_text'] . '</td>
								<td class="text_tengah kanan bawah total_renstra">' . $data_renstra['satuan_renstra'] . '</td>
								<td class="text_kanan kanan bawah total_renstra">' . $data_renstra['total_pagu_renstra'] . '</td>
								<td class="text_tengah kanan bawah realisasi_renstra_tahun_lalu">' . $data_renstra['total_target_renstra_tahun_sebelumnya'] . '</td>
								<td class="text_tengah kanan bawah realisasi_renstra_tahun_lalu">' . $data_renstra['satuan_renstra'] . '</td>
								<td class="text_kanan kanan bawah realisasi_renstra_tahun_lalu">' . $data_renstra['total_pagu_renstra_tahun_sebelumnya'] . '</td>
								<td class="text_tengah kanan bawah total_renja target_indikator">' . $target_output_sub_giat . '</td>
								<td class="text_tengah kanan bawah total_renja satuan_indikator">' . $satuan_output_sub_giat . '</td>
								<td class="text_kanan kanan bawah total_renja pagu_renja" data-pagu="' . $sub_giat['total_simda'] . '">' . number_format($sub_giat['total_simda'], 0, ",", ".") . '</td>
								<td class="text_tengah kanan bawah triwulan_1">' . $realisasi_indikator_tw1 . '</td>
								<td class="text_tengah kanan bawah triwulan_1">' . $satuan_output_sub_giat . '</td>
								<td class="text_kanan kanan bawah triwulan_1"><span class="nilai_realisasi_tw1">' . number_format($sub_giat['triwulan_1'], 0, ",", ".") . '</span></td>
								<td class="text_tengah kanan bawah triwulan_2">' . $realisasi_indikator_tw2 . '</td>
								<td class="text_tengah kanan bawah triwulan_2">' . $satuan_output_sub_giat . '</td>
								<td class="text_kanan kanan bawah triwulan_2"><span class="nilai_realisasi_tw2">' . number_format($sub_giat['triwulan_2'], 0, ",", ".") . '</span></td>
								<td class="text_tengah kanan bawah triwulan_3">' . $realisasi_indikator_tw3 . '</td>
								<td class="text_tengah kanan bawah triwulan_3">' . $satuan_output_sub_giat . '</td>
								<td class="text_kanan kanan bawah triwulan_3"><span class="nilai_realisasi_tw3">' . number_format($sub_giat['triwulan_3'], 0, ",", ".") . '</span></td>
								<td class="text_tengah kanan bawah triwulan_4">' . $realisasi_indikator_tw4 . '</td>
								<td class="text_tengah kanan bawah triwulan_4">' . $satuan_output_sub_giat . '</td>
								<td class="text_kanan kanan bawah triwulan_4"><span class="nilai_realisasi_tw4">' . number_format($sub_giat['triwulan_4'], 0, ",", ".") . '</span></td>
								<td class="text_tengah kanan bawah realisasi_renja">' . $total_tw . '</td>
								<td class="text_tengah kanan bawah realisasi_renja">' . $satuan_output_sub_giat . '</td>
								<td class="text_kanan kanan bawah realisasi_renja pagu_renja_realisasi" data-pagu="' . $sub_giat['realisasi'] . '"><span class="nilai_realisasi_renja">' . number_format($sub_giat['realisasi'], 0, ",", ".") . '</span></td>
								<td class="text_tengah kanan bawah capaian_renja">' . $capaian_realisasi_indikator . '</td>
								<td class="text_kanan kanan bawah capaian_renja">' . $capaian . '</td>
								<td class="text_tengah kanan bawah realisasi_renstra_tahun_berjalan">' . $data_renstra['total_target_renstra_tahun_ini'] . '</td>
								<td class="text_tengah kanan bawah realisasi_renstra_tahun_berjalan">' . $data_renstra['satuan_renstra'] . '</td>
								<td class="text_kanan kanan bawah realisasi_renstra_tahun_berjalan">' . $data_renstra['total_pagu_renstra_tahun_ini'] . '</td>
								<td class="text_tengah kanan bawah capaian_renstra_tahun_berjalan">' . $data_renstra['total_capaian_target_renstra_tahun_ini'] . '</td>
								<td class="text_kanan kanan bawah capaian_renstra_tahun_berjalan">' . $data_renstra['total_capaian_pagu_renstra_tahun_ini'] . '</td>
								<td class="kanan bawah">' . $unit[0]['nama_skpd'] . '</td>
								<td class="kanan bawah">' . $keterangan . '</td>
							</tr>
							';
					$total_pagu_renstra_asli += array_sum($data_renstra['total_pagu_renstra_asli']);
					$total_pagu_renstra_tahun_ini_asli += array_sum($data_renstra['total_pagu_renstra_tahun_ini_asli']);
					$total_pagu_renstra_tahun_sebelumnya_asli += array_sum($data_renstra['total_pagu_renstra_tahun_sebelumnya_asli']);
					$total_capaian_pagu_renstra_tahun_ini_asli += array_sum($data_renstra['total_capaian_pagu_renstra_tahun_ini_asli']);
				}
			}
		}
	}
}


$capaian_kinerja = [
	'total' => 0,
	'tw_1'  => 0,
	'tw_2'  => 0,
	'tw_3'  => 0,
	'tw_4'  => 0,
];

$total_bobot = 0;
$total_capaian = 0;
$total_realisasi_tw1 = 0;
$total_realisasi_tw2 = 0;
$total_realisasi_tw3 = 0;
$total_realisasi_tw4 = 0;
foreach ($data_all_js as $k => $v) {
	foreach ($v['bobot_kinerja_indikator'] as $kk => $vv) {
		$result_by_bobot = $v['realisasi_indikator_1'][$kk] * $vv;
		$total_realisasi_tw1 += $result_by_bobot;

		$result_by_bobot = $v['realisasi_indikator_2'][$kk] * $vv;
		$total_realisasi_tw2 += $result_by_bobot;

		$result_by_bobot = $v['realisasi_indikator_3'][$kk] * $vv;
		$total_realisasi_tw3 += $result_by_bobot;

		$result_by_bobot = $v['realisasi_indikator_4'][$kk] * $vv;
		$total_realisasi_tw4 += $result_by_bobot;

		$capaian_per_program = $v['realisasi_indikator'][$kk] * $vv;

		$total_capaian += $capaian_per_program; //capaian per program diakumulasi untuk dibagi akumulasi bobot
		$total_bobot += $vv; //akumulasi bobot untuk membagi capaian per program
	}
}

$capaian_kinerja['total'] = $total_bobot > 0 ? $total_capaian / $total_bobot : 0;

if (
	!empty($total_realisasi_tw1)
	&& $total_realisasi_tw1 != 0
	&& !empty($capaian_kinerja['total'])
	&& $capaian_kinerja['total'] != 0
) {
	$capaian_kinerja['tw_1'] = $total_realisasi_tw1 / $total_bobot;
}
if (
	!empty($total_realisasi_tw2)
	&& $total_realisasi_tw2 != 0
	&& !empty($capaian_kinerja['total'])
	&& $capaian_kinerja['total'] != 0
) {
	$capaian_kinerja['tw_2'] = $total_realisasi_tw2 / $total_bobot;
}
if (
	!empty($total_realisasi_tw3)
	&& $total_realisasi_tw3 != 0
	&& !empty($capaian_kinerja['total'])
	&& $capaian_kinerja['total'] != 0
) {
	$capaian_kinerja['tw_3'] = $total_realisasi_tw3 / $total_bobot;
}
if (
	!empty($total_realisasi_tw4)
	&& $total_realisasi_tw4 != 0
	&& !empty($capaian_kinerja['total'])
	&& $capaian_kinerja['total'] != 0
) {
	$capaian_kinerja['tw_4'] = $total_realisasi_tw4 / $total_bobot;
}

// die(print_r($data_all_js));
$nama_page = 'RFK ' . $unit[0]['nama_skpd'] . ' ' . $unit[0]['kode_skpd'] . ' | ' . $input['tahun_anggaran'];
$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
$link = $this->get_link_post($custom_post);
$url_skpd = '<a href="' . $link . '&pagu_dpa=sipd" target="_blank">' . $unit[0]['kode_skpd'] . ' ' . $unit[0]['nama_skpd'] . '</a> ';

if (
	!empty($total_pagu_renstra_tahun_ini_asli)
	&& !empty($total_pagu_renstra_asli)
) {
	$total_capaian_pagu_renstra_tahun_ini_asli = $this->pembulatan(($total_pagu_renstra_tahun_ini_asli / $total_pagu_renstra_asli) * 100);
}
?>
<style type="text/css">
	table th,
	#mod-monev th {
		vertical-align: middle;
	}

	.no_border tr,
	.no_border td,
	.no_border th,
	.no_border table {
		border: none !important;
	}

	body {
		overflow: auto;
	}

	td[contenteditable="true"] {
		background: #ff00002e;
		max-width: 300px;
	}

	td.target_realisasi[contenteditable="true"] {
		max-width: 150px;
	}

	th#bobotKinerja[contenteditable="true"] {
		background: #ff00002e;
		max-width: 600px;
	}

	.negatif {
		color: #ff0000;
	}

	.persentase {
		color: #9d00ff;
	}

	.nilai_akhir {
		color: #28bb00;
	}

	.renstra_kegiatan,
	.indikator_renstra {
		display: none;
	}

	#mod-monev table {
		margin: 0;
	}

	.edit-monev-file {
		padding: 3px 2px 3px 2px;
		margin: 0;
	}

	#data-file-monev th {
		vertical-align: top;
	}

	.edit-monev-file-danger {
		color: red;
	}

	.edit-monev-file-grey {
		color: grey;
	}

	.edit-monev-file-danger:hover {
		background: red;
		color: #fff;
	}

	.edit-monev-file-grey:hover {
		background: grey;
		color: #fff;
	}

	.display-indikator-renstra {
		display: none;
	}

	#tabel-monev-renja {
		font-family: \'Open Sans\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', sans-serif;
		border-collapse: collapse;
		font-size: 70%;
		border: 0;
		table-layout: fixed;
	}

	#tabel-monev-renja thead {
		position: sticky;
		top: -6px;
		background: #ffc491;
	}

	#tabel-monev-renja tfoot {
		position: sticky;
		bottom: -6px;
		background: #ffc491;
	}


	.hover-shadow-lg {
		transition: box-shadow 0.3s ease-in-out;
	}

	.hover-shadow-lg:hover {
		box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
	}

	.transition-shadow {
		transition: transform 0.3s ease;
	}

	.transition-shadow:hover {
		transform: translateY(-3px);
	}
</style>
<input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
<input type="hidden" value="<?php echo $unit[0]['id_skpd']; ?>" id="id_skpd">
<h1 class="text-center">Monitoring dan Evaluasi Rencana Kerja <br><?php echo $data_jadwal_renja['nama'] . ' Tahun ' . $data_jadwal_renja['tahun_anggaran'] . '<br>' . $url_skpd . '<br>Tahun ' . $input['tahun_anggaran'] . ' ' . $nama_pemda; ?></h1>
<div class="content flex-row-fluid" style="max-width: 1500px; margin:auto; padding: 10px;">
	<div class="row gy-5 g-xl-8 mb-5">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div class="card-title">
						<h4 style="margin: 0;"><i class="dashicons dashicons-chart-bar" style="font-size: x-large; padding-top: 2px;"></i> Dashboard Anggaran dan Realisasi</h4>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div id="chart" style="padding: 30px; height: 500px;"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 shadow-sm p-3 mb-5 bg-white hover-shadow-lg transition-shadow rounded">
							<div class="row">
								<div class="col-md-12">
									<h2 class="font-weight-bolder text-white p-5 bg-warning rounded m-0 text-center">Anggaran</h2>
								</div>
							</div>
							<div class="d-flex align-items-center mb-9 bg-light-warning rounded" style="margin-top: 3rem;">
								<!--begin::Title-->
								<div class="col-md-12 no_border">
									<table class="table ">
										<tr>
											<td style="width:20px;">
												<h2 class="font-weight-bolder text-warning py-1 m-0">Total</h2>
											</td>
											<td style="width:2px;">
												<h2 class="font-weight-bolder text-warning py-1 m-0">:</h2>
											</td>
											<td class="text-end text-right">
												<h2 class="font-weight-bolder text-warning py-1 m-0"><?php echo number_format($data_all['total'], 0, ",", "."); ?></h2>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-warning py-1 m-0">TW 1</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-warning py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-warning py-1 m-0"><?php echo number_format($data_all['rak_triwulan_1'], 0, ",", "."); ?></h4>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-warning py-1 m-0">TW 2</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-warning py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-warning py-1 m-0"><?php echo number_format($data_all['rak_triwulan_2'], 0, ",", "."); ?></h4>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-warning py-1 m-0">TW 3</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-warning py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-warning py-1 m-0"><?php echo number_format($data_all['rak_triwulan_3'], 0, ",", "."); ?></h4>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-warning py-1 m-0">TW 4</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-warning py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-warning py-1 m-0"><?php echo number_format($data_all['rak_triwulan_4'], 0, ",", "."); ?></h4>
											</td>
										</tr>
									</table>
								</div>
								<!--end::Title-->
							</div>
						</div>

						<div class="col-md-4 shadow-sm p-3 mb-5 bg-white hover-shadow-lg transition-shadow rounded">
							<div class="row">
								<div class="col-md-12">
									<h2 class="font-weight-bolder text-white p-5 bg-primary rounded m-0 text-center">Realisasi</h2>
								</div>
							</div>
							<div class="d-flex align-items-center mb-9 bg-light-primary rounded" style="margin-top: 3rem;">
								<!--begin::Title-->
								<div class="col-md-12 no_border">
									<table class="table">
										<tr>
											<td style="width:20px;">
												<h2 class="font-weight-bolder text-primary py-1 m-0">Total</h2>
											</td>
											<td style="width:2px;">
												<h2 class="font-weight-bolder text-primary py-1 m-0">:</h2>
											</td>
											<td class="text-end text-right">
												<h2 class="font-weight-bolder text-primary py-1 m-0"><?php echo number_format($data_all['realisasi'], 0, ",", "."); ?></h2>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-primary py-1 m-0">TW 1</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-primary py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-primary py-1 m-0"><?php echo number_format($data_all['triwulan_1'], 0, ",", "."); ?></h4>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-primary py-1 m-0">TW 2</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-primary py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-primary py-1 m-0"><?php echo number_format($data_all['triwulan_2'], 0, ",", "."); ?></h4>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-primary py-1 m-0">TW 3</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-primary py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-primary py-1 m-0"><?php echo number_format($data_all['triwulan_3'], 0, ",", "."); ?></h4>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-primary py-1 m-0">TW 4</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-primary py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-primary py-1 m-0"><?php echo number_format($data_all['triwulan_4'], 0, ",", "."); ?></h4>
											</td>
										</tr>
									</table>
								</div>
								<!--end::Title-->
							</div>
						</div>

						<div class="col-md-4 shadow-sm p-3 mb-5 bg-white hover-shadow-lg transition-shadow rounded">
							<div class="row">
								<div class="col-md-12">
									<h2 class="font-weight-bolder text-white p-5 bg-success rounded m-0 text-center">Penyerapan Anggaran</h2>
								</div>
							</div>
							<div class="d-flex align-items-center mb-9 bg-light-success rounded p-5">
								<!--begin::Title-->
								<div class="col-md-12 no_border">
									<table class="table">
										<tr>
											<td style="width:20px;">
												<h2 class="font-weight-bolder text-success py-1 m-0">Total</h2>
											</td>
											<td style="width:2px;">
												<h2 class="font-weight-bolder text-success py-1 m-0">:</h2>
											</td>
											<td class="text-end text-right">
												<h2 class="font-weight-bolder text-success py-1 m-0"><?php echo $this->pembulatan(($data_all['total'] != 0 ? ($data_all['realisasi'] / $data_all['total']) * 100 : 0) ?? 0); ?>%</h2>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-success py-1 m-0">TW 1</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-success py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-success py-1 m-0"><?php echo $this->pembulatan($persen_triwulan_1); ?>%</h4>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-success py-1 m-0">TW 2</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-success py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-success py-1 m-0"><?php echo $this->pembulatan($persen_triwulan_2); ?>%</h4>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-success py-1 m-0">TW 3</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-success py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-success py-1 m-0"><?php echo $this->pembulatan($persen_triwulan_3); ?>%</h4>
											</td>
										</tr>
										<tr>
											<td>
												<h4 class="font-weight-bolder text-success py-1 m-0">TW 4</h4>
											</td>
											<td>
												<h4 class="font-weight-bolder text-success py-1 m-0">:</h4>
											</td>
											<td class="text-end text-right">
												<h4 class="font-weight-bolder text-success py-1 m-0"><?php echo $this->pembulatan($persen_triwulan_4); ?>%</h4>
											</td>
										</tr>
									</table>
								</div>
								<!--end::Title-->
							</div>
						</div>
					</div>

					<div class="row mb-5">
						<div class="col-md-6">
							<div class="card card-primary shadow-sm bg-white hover-shadow-lg transition-shadow rounded">
								<div class="card-header bg-danger text-white p-5">
									<div class="col-12">
										<div class="row">
											<div class="col-2">
												<i class="fas fa-money-bill-wave-alt fa-3x lh-lg"></i>
											</div>
											<div class="col">
												<h2 class="m-0 p-0 col-md-12 lh-lg text-white">Capaian Kinerja Program</h2>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="row mb-2">
										<div class="col-12 text-center" style="font-size:1.3em">
											<h2 class="font-weight-bolder text-danger py-1 m-3">Total: <?php echo round($capaian_kinerja['total'], 2); ?>%</h2>
										</div>
									</div>
									<div class="row mb-4">
										<div class="col-3 text-center" style="font-size:1.3em; border-right:1px solid #666;">
											<p>TW I</p>
											<p><?php echo round($capaian_kinerja['tw_1'], 2); ?>%</p>
										</div>
										<div class="col-3 text-center" style="font-size:1.3em; border-right:1px solid #666;">
											<p>TW II</p>
											<p><?php echo round($capaian_kinerja['tw_2'], 2); ?>%</p>
										</div>
										<div class="col-3 text-center" style="font-size:1.3em; border-right:1px solid #666;">
											<p>TW III</p>
											<p><?php echo round($capaian_kinerja['tw_3'], 2); ?>%</p>
										</div>
										<div class="col-3 text-center" style="font-size:1.3em;">
											<p>TW IV</p>
											<p><?php echo round($capaian_kinerja['tw_4'], 2); ?>%</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="card card-primary shadow-sm bg-white hover-shadow-lg transition-shadow rounded">
								<div class="card-header bg-primary text-white p-5">
									<div class="col-12">
										<div class="row">
											<div class="col-2">
												<i class="fas fa-money-bill-wave-alt fa-3x lh-lg"></i>
											</div>
											<div class="col">
												<h2 class="m-0 p-0 col-md-12 lh-lg text-white">Nomenklatur Rencana Kerja</h2>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="row mb-2">
										<div class="col-12 text-center" style="font-size:1.3em">
											<h2 class="font-weight-bolder text-primary py-1 m-3"><?php echo $no_urusan; ?> Urusan | <?php echo $no_bidang; ?> Bidang</h2>
										</div>
									</div>
									<div class="row mb-4">
										<div class="col-4 text-center" style="font-size:1.3em; border-right:1px solid #666;">
											<p>Program</p>
											<p><?php echo $no_program; ?></p>
										</div>
										<div class="col-4 text-center" style="font-size:1.3em; border-right:1px solid #666;">
											<p>Kegiatan</p>
											<p><?php echo $no_kegiatan; ?></p>
										</div>
										<div class="col-4 text-center" style="font-size:1.3em;">
											<p>Sub Kegiatan</p>
											<p><?php echo $no_sub_kegiatan; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row" id="chart-program"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id='aksi-wp-sipd'></div>
<h2 class="text-center">Tabel Monitoring dan Evaluasi RENJA</h2>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; max-height: 80vh;">
	<table id="tabel-monev-renja" cellpadding="2" cellspacing="0" contenteditable="false">
		<thead>
			<tr>
				<th rowspan="5" style="width: 60px;" class='atas kiri kanan bawah text_tengah text_blok'>No</th>
				<th rowspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Tujuan</th>
				<th rowspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Sasaran</th>
				<th rowspan="2" style="width: 100px;" class='atas kanan bawah text_tengah text_blok'>Kode</th>
				<th rowspan="2" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Program, Kegiatan, Sub Kegiatan</th>
				<th rowspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Indikator Kinerja Tujuan, Sasaran, Program(outcome) dan Kegiatan (output), Sub Kegiatan</th>
				<th rowspan="2" colspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Target Renstra SKPD pada Tahun <?php echo $awal_renstra; ?> s/d <?php echo $akhir_renstra; ?> (periode Renstra SKPD)</th>
				<th rowspan="2" colspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Realisasi Capaian Kinerja Renstra SKPD sampai dengan Renja SKPD Tahun Lalu</th>
				<th rowspan="2" colspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Target kinerja dan anggaran Renja SKPD Tahun Berjalan Tahun <?php echo $input['tahun_anggaran']; ?> yang dievaluasi</th>
				<th colspan="12" style="width: 1200px;" class='atas kanan bawah text_tengah text_blok'>Realisasi Kinerja Pada Triwulan</th>
				<th rowspan="2" colspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Realisasi Capaian Kinerja dan Anggaran Renja SKPD yang dievaluasi</th>
				<th rowspan="2" colspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Tingkat Capaian Kinerja dan Realisasi Anggaran Renja yang dievaluasi (%)</th>
				<th rowspan="2" colspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Realisasi Kinerja dan Anggaran Renstra SKPD s/d Tahun <?php echo $input['tahun_anggaran']; ?> (Akhir Tahun Pelaksanaan Renja SKPD)</th>
				<th rowspan="2" colspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Tingkat Capaian Kinerja dan Realisasi Anggaran Renstra SKPD s/d tahun <?php echo $input['tahun_anggaran']; ?> (%)</th>
				<th rowspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Unit OPD Penanggung Jawab</th>
				<th rowspan="2" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Keterangan</th>
			</tr>
			<tr>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>I</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>II</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>III</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>IV</th>
			</tr>
			<tr>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>0</th>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>1</th>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>2</th>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>3</th>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>4</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>5</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>6</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>7</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>8</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>9</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>10</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>11</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>12 = 8+9+10+11</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>13 = 12/7x100</th>
				<th colspan="3" class='atas kanan bawah text_tengah text_blok'>14 = 6 + 12</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>15 = 14/5 x100</th>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>16</th>
				<th rowspan="3" class='atas kanan bawah text_tengah text_blok'>17</th>
			</tr>
			<tr>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>K</th>
				<th rowspan="2" class='atas kanan bawah text_tengah text_blok'>Rp</th>
			</tr>
			<tr>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Volume</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body_monev; ?>
		</tbody>
		<tfoot>
			<tr>
				<th class='atas kanan bawah text_kanan text_blok' colspan="9"><?php echo $this->_number_format($total_pagu_renstra_asli); ?></th>
				<th class='atas kanan bawah text_kanan text_blok' colspan="3"><?php echo $this->_number_format($total_pagu_renstra_tahun_sebelumnya_asli); ?></th>
				<th class='atas kanan bawah text_kanan text_blok' colspan="3"><?php echo number_format($data_all['total'], 0, ",", "."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok' colspan="3"><?php echo number_format($data_all['triwulan_1'], 0, ",", "."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok' colspan="3"><?php echo number_format($data_all['triwulan_2'], 0, ",", "."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok' colspan="3"><?php echo number_format($data_all['triwulan_3'], 0, ",", "."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok' colspan="3"><?php echo number_format($data_all['triwulan_4'], 0, ",", "."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok' colspan="3"><?php echo number_format($data_all['realisasi'], 0, ",", "."); ?></th>
				<th class='atas kanan bawah text_kanan text_blok' colspan="2"><?php echo $this->pembulatan(($data_all['realisasi'] / $data_all['total']) * 100); ?></th>
				<th class='atas kanan bawah text_kanan text_blok' colspan="3"><?php echo $this->_number_format($total_pagu_renstra_tahun_ini_asli); ?></th>
				<th class='atas kanan bawah text_kanan text_blok' colspan="2"><?php echo $total_capaian_pagu_renstra_tahun_ini_asli; ?></th>
				<th class='atas kanan bawah text_kanan text_blok' colspan="2"></th>
			</tr>
		</tfoot>
	</table>
</div>
<?php
function generate_aksi_triwulan($type)
{
	$upload = '<span class="edit-monev-file edit-monev-file-grey upload_monev_triwulan" title="Upload Monev"><i class="dashicons dashicons-cloud-upload"></i></span>';
	$simpan = '<span class="edit-monev-file simpan_monev_triwulan" title="Simpan Monev"><i class="dashicons dashicons-saved"></i></span>';
	$hapus = '<span style="margin-left: 10px;" class="edit-monev-file edit-monev-file-danger hapus_monev_triwulan" title="Hapus Monev"><i class="dashicons dashicons-no-alt"></i></span>';
	$ret = '';
	if ($type == 'skpd') {
		$ret = $simpan . $hapus;
	} else if ($type == 'verifikator') {
		$ret = $simpan;
	}
	return $ret;
}

$edit_bobot_kinerja = 'contenteditable="true"';
$keterangan_skpd_triwulan = 'contenteditable="true"';
$keterangan_verifikator_triwulan = '';
$aksi_user = 'skpd';
$upload_monev = '<input type="file" class="upload_monev" style="font-size:12px; width: 100%; overflow: hidden;">';
$edit_monev = '<button type="button" class="btn btn-success" id="set-monev">Simpan</button>';
if (
	current_user_can('administrator')
	|| in_array("mitra_bappeda", $current_user->roles)
	|| in_array("tapd_pp", $current_user->roles)
) {
	$edit_bobot_kinerja = '';
	$edit_monev = '';
	$upload_monev = '';
	$keterangan_skpd_triwulan = '';
	$keterangan_verifikator_triwulan = 'contenteditable="true"';
	$aksi_user = 'verifikator';
}
$monev_triwulan = $wpdb->get_results(
	"
	SELECT 
		triwulan,
		file_monev,
		update_skpd_at, 
		keterangan_skpd, 
		catatan_verifikator, 
		update_verifikator_at 
	FROM data_monev_renja_triwulan 
	WHERE id_skpd=" . $input['id_skpd'] . " 
		AND tahun_anggaran=" . $input['tahun_anggaran'],
	ARRAY_A
);
$monev_triwulan_all = array(
	'1' => array('file_monev' => $upload_monev, 'update_skpd_at' => '', 'keterangan_skpd' => '', 'catatan_verifikator' => '', 'update_verifikator_at' => ''),
	'2' => array('file_monev' => $upload_monev, 'update_skpd_at' => '', 'keterangan_skpd' => '', 'catatan_verifikator' => '', 'update_verifikator_at' => ''),
	'3' => array('file_monev' => $upload_monev, 'update_skpd_at' => '', 'keterangan_skpd' => '', 'catatan_verifikator' => '', 'update_verifikator_at' => ''),
	'4' => array('file_monev' => $upload_monev, 'update_skpd_at' => '', 'keterangan_skpd' => '', 'catatan_verifikator' => '', 'update_verifikator_at' => '')
);
foreach ($monev_triwulan as $k => $v) {
	$monev_triwulan_all[$v['triwulan']]['file_monev'] .= '<div style="padding-top: 10px;"><a class="file_monev" href="' . WPSIPD_PLUGIN_URL . 'public/media/' . $v['file_monev'] . '" target="_blank">' . $v['file_monev'] . '</a></div>';
	$monev_triwulan_all[$v['triwulan']]['keterangan_skpd'] = $v['keterangan_skpd'];
	$monev_triwulan_all[$v['triwulan']]['catatan_verifikator'] = $v['catatan_verifikator'];
	$monev_triwulan_all[$v['triwulan']]['update_verifikator_at'] = $v['update_verifikator_at'];
	$monev_triwulan_all[$v['triwulan']]['update_skpd_at'] = $v['update_skpd_at'];
}
?>
<style type="text/css">
	#monev-body-renstra {
		word-break: break-word;
	}
</style>
<div class="hide-print" style="margin: auto; max-width: 1200px;">
	<h4 style="text-align: center; margin: 30px 0 10px; font-weight: bold;">Data File MONEV Indikator RENJA Tahun <?php echo $input['tahun_anggaran']; ?></h4>
	<table class="table table-bordered" id="data-file-monev">
		<thead>
			<tr>
				<th class="text_tengah" style="width: 90px;">Triwulan</th>
				<th class="text_tengah" style="width: 200px;">Lampiran Excel</th>
				<th class="text_tengah" style="width: 150px;">Tanggal Update File</th>
				<th class="text_tengah" style="width: 250px;">Keterangan SKPD</th>
				<th class="text_tengah">Catatan Verifikator / Rekomendasi</th>
				<th class="text_tengah" style="width: 150px;">Tanggal Update Catatan</th>
				<th class="text_tengah" style="width: 100px;">Aksi</th>
			</tr>
		</thead>
		<tbody>
			<tr data-tw="1">
				<td class="text_tengah">I</td>
				<td class="lampiran_excel"><?php echo $monev_triwulan_all['1']['file_monev']; ?></td>
				<td class="tgl_update_file text_tengah"><?php echo $monev_triwulan_all['1']['update_skpd_at']; ?></td>
				<td class="keterangan_skpd" <?php echo $keterangan_skpd_triwulan; ?>><?php echo $monev_triwulan_all['1']['keterangan_skpd']; ?></td>
				<td class="catatan_verifikator" <?php echo $keterangan_verifikator_triwulan; ?>><?php echo $monev_triwulan_all['1']['catatan_verifikator']; ?></td>
				<td class="tgl_update_catatan_renja text_tengah"><?php echo $monev_triwulan_all['1']['update_verifikator_at']; ?></td>
				<td class="text_tengah">
					<?php echo generate_aksi_triwulan($aksi_user); ?>
				</td>
			</tr>
			<tr data-tw="2">
				<td class="text_tengah">II</td>
				<td class="lampiran_excel"><?php echo $monev_triwulan_all['2']['file_monev']; ?></td>
				<td class="tgl_update_file text_tengah"><?php echo $monev_triwulan_all['2']['update_skpd_at']; ?></td>
				<td class="keterangan_skpd" <?php echo $keterangan_skpd_triwulan; ?>><?php echo $monev_triwulan_all['2']['keterangan_skpd']; ?></td>
				<td class="catatan_verifikator" <?php echo $keterangan_verifikator_triwulan; ?>><?php echo $monev_triwulan_all['2']['catatan_verifikator']; ?></td>
				<td class="tgl_update_catatan_renja text_tengah"><?php echo $monev_triwulan_all['2']['update_verifikator_at']; ?></td>
				<td class="text_tengah">
					<?php echo generate_aksi_triwulan($aksi_user); ?>
				</td>
			</tr>
			<tr data-tw="3">
				<td class="text_tengah">III</td>
				<td class="lampiran_excel"><?php echo $monev_triwulan_all['3']['file_monev']; ?></td>
				<td class="tgl_update_file text_tengah"><?php echo $monev_triwulan_all['3']['update_skpd_at']; ?></td>
				<td class="keterangan_skpd" <?php echo $keterangan_skpd_triwulan; ?>><?php echo $monev_triwulan_all['3']['keterangan_skpd']; ?></td>
				<td class="catatan_verifikator" <?php echo $keterangan_verifikator_triwulan; ?>><?php echo $monev_triwulan_all['3']['catatan_verifikator']; ?></td>
				<td class="tgl_update_catatan_renja text_tengah"><?php echo $monev_triwulan_all['3']['update_verifikator_at']; ?></td>
				<td class="text_tengah">
					<?php echo generate_aksi_triwulan($aksi_user); ?>
				</td>
			</tr>
			<tr data-tw="4">
				<td class="text_tengah">IV</td>
				<td class="lampiran_excel"><?php echo $monev_triwulan_all['4']['file_monev']; ?></td>
				<td class="tgl_update_file text_tengah"><?php echo $monev_triwulan_all['4']['update_skpd_at']; ?></td>
				<td class="keterangan_skpd" <?php echo $keterangan_skpd_triwulan; ?>><?php echo $monev_triwulan_all['4']['keterangan_skpd']; ?></td>
				<td class="catatan_verifikator" <?php echo $keterangan_verifikator_triwulan; ?>><?php echo $monev_triwulan_all['4']['catatan_verifikator']; ?></td>
				<td class="tgl_update_catatan_renja text_tengah"><?php echo $monev_triwulan_all['4']['update_verifikator_at']; ?></td>
				<td class="text_tengah">
					<?php echo generate_aksi_triwulan($aksi_user); ?>
				</td>
			</tr>
		</tbody>
	</table>

	<h4 style="margin: 30px 0 10px; font-weight: bold;">Dokumentasi:</h4>
	<ul>
		<li>Background warna hijau adalah baris program.</li>
		<li>Background warna biru muda adalah baris kegiatan</li>
		<li>Background warna putih adalah baris sub kegiatan</li>
		<li>Nama SKPD berisi url menuju halaman RFK</li>
		<li>Nama sub kegiatan berisi url menuju halaman RKA/DPA</li>
		<li>Nilai realisasi anggaran per triwulan diambil dari data SP2D SIMDA yang diload saat membuka halaman RFK per bulan</li>
		<li>Untuk mengisi MONEV RENJA per bulan lakukan checked pada <b>Settings > Edit Monev indikator</b></li>
		<li>Untuk melihat indikator renstra pada laporan MONEV lakukan checked pada <b>Settings > Tampilkan indikator RENSTRA</b></li>
		<li>Indikator berwarna <b>hitam</b> adalah indikator dengan rumus <b>Tren Positif</b></li>
		<li>Indikator berwarna <b>merah</b> adalah indikator dengan rumus <b>Tren Negatif</b></li>
		<li>Indikator berwarna <b>ungu</b> adalah indikator dengan rumus <b>Prosentase</b></li>
		<li>Indikator berwarna <b>hijau</b> adalah indikator dengan rumus <b>Nilai Akhir</b></li>
		<li>Lampiran excel, tanggal update file dan keterangan SKPD pada tabel Data File MONEV Indikator RENJA diisi oleh user SKPD (PA/KPA)</li>
		<li>Catatan verifikator dan tanggal update catatan pada tabel Data File MONEV Indikator RENJA diisi oleh user Admin (BAPPEDA)</li>
		<li>Ukuran file maksimal adalah 10MB dan berextensi .xlsx (excel)</li>
		<li>Tekan tombol (X) untuk menghapus file .xlsx (excel)</li>
		<li>Tekan tombol checklist berwarna biru untuk menyimpan data File MONEV</li>
		<li>Pagu program, kegiatan dan sub kegiatan RENJA diambil dari nilai RKA terakhir di sipd.kemendagri.go.id</li>
		<li>Jika target indikator RENSTRA bertipe string/karakter maka total target adalah tahun ke 5. Tidak diakumulasikan seperti ketika tipenya interger/angka.</li>
	</ul>
</div>
<div class="modal fade" id="mod-monev" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
	<div class="modal-dialog" style="min-width: 80%;" role="document">
		<div class="modal-content">
			<div class="modal-header bgpanel-theme">
				<h4 style="margin: 0;" class="modal-title" id="">Edit MONEV Indikator Per Bulan</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<table class="table table-bordered">
							<tbody>
								<tr>
									<th style="width: 200px;">Program / Kegiatan / Sub Kegiatan</th>
									<td id="monev-nama"></td>
								</tr>
								<tr>
									<td colspan="2">
										<table class="display-indikator-renstra">
											<thead>
												<tr>
													<th class="text_tengah" colspan="2" rowspan="2">Indikator RENSTRA</th>
													<th class="text_tengah" style="width: 100px;" colspan="<?php echo $lama_pelaksanaan; ?>">Target</th>
													<th class="text_tengah" style="width: 100px;" rowspan="2">Satuan</th>
													<th class="text_tengah" style="width: 140px;" rowspan="2">Total Pagu (Rp)<br>Tahun <?php echo $awal_renstra . '-' . $akhir_renstra; ?></th>
												</tr>
												<tr>
													<?php echo $body_tahun; ?>
												</tr>
											</thead>
											<tbody id="monev-body-renstra">
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<table>
											<thead>
												<tr>
													<th class="text_tengah">Indikator Program(outcome) dan Kegiatan (output), Sub Kegiatan RENJA</th>
													<th class="text_tengah" style="width: 120px;">Target</th>
													<th class="text_tengah" style="width: 120px;">Satuan</th>
												</tr>
											</thead>
											<tbody id="monev-indikator">
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<table>
											<thead>
												<tr>
													<th class="text_tengah" style="width: 140px;">Total Pagu (Rp.)</th>
													<th class="text_tengah">Pilih Rumus Indikator</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="text_kanan" id="monev-pagu">-</td>
													<td>
														<select style="width: 100%;" id="tipe_indikator">
															<?php echo $rumus_indikator_html; ?>
														</select>
														<ul id="helptext_tipe_indikator" style="margin: 10px 0 0 30px;">
															<?php echo $keterangan_indikator_html; ?>
														</ul>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<table>
											<thead>
												<tr>
													<th class="text_tengah" style="width: 150px;">Bulan</th>
													<th class="text_tengah" style="width: 150px;">RAK (Rp.)</th>
													<th class="text_tengah" style="width: 150px;">Realisasi (Rp.)</th>
													<th class="text_tengah" style="width: 150px;">Selisih (Rp.)</th>
													<th class="text_tengah" style="width: 150px;">Realisasi Target</th>
													<th class="text_tengah" style="width: 300px;">Faktor Pendorong</th>
													<th class="text_tengah" style="width: 300px;">Faktor Penghambat</th>
												</tr>
												<tr>
													<th class="text_tengah">1</th>
													<th class="text_tengah">2</th>
													<th class="text_tengah">3</th>
													<th class="text_tengah">4 = 2 - 3</th>
													<th class="text_tengah">5</th>
													<th class="text_tengah">6</th>
													<th class="text_tengah">7</th>
												</tr>
											</thead>
											<tbody id="monev-body"></tbody>
											<tfoot>
												<tr>
													<th class="text_kiri text_blok" colspan="2">Target Indikator</th>
													<th class="text_kanan text_blok" id="target_indikator_monev_rumus">0</th>
													<th class="text_kiri text_blok" colspan="2">Capaian target dihitung sesuai rumus indikator. Satuan (%)</th>
													<th class="text_tengah text_blok" id="capaian_target_realisasi" colspan="2">0</th>
												</tr>
												<tr>
													<th class="text_kiri text_blok" colspan="3">Bobot Kinerja<br><small class="text-muted">( Default 1 )</small></th>
													<th class="text_tengah text_blok" colspan="4" id="bobotKinerja" <?php echo $edit_bobot_kinerja; ?> onkeypress="onlyNumber(event);"></th>
												</tr>
											</tfoot>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<?php echo $edit_monev; ?>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	function edit_monev_indikator(that) {
		if (jQuery(that).is(':checked')) {
			jQuery('.edit-monev').show();
		} else {
			jQuery('.edit-monev').hide();
		}
	}

	function tampil_indikator_renstra(that) {
		if (jQuery(that).is(':checked')) {
			jQuery('.renstra_kegiatan').show();
			jQuery('.indikator_renstra').show();
		} else {
			jQuery('.renstra_kegiatan').hide();
			jQuery('.indikator_renstra').hide();
		}
	}

	function setRumus(id) {
		jQuery('#tipe_indikator').val(id);
		jQuery('#helptext_tipe_indikator li').hide();
		jQuery('#helptext_tipe_indikator li[data-id="' + id + '"]').show();
		setTotalMonev(false);
	}

	function jenis_indikator(id_unik) {
		let idunik = id_unik;
		let data = idunik.split("-");
		let kode = data[2];
		let jenis_indikator = kode.split(".");
		return jenis_indikator.length;
	}

	function setTotalMonev(that) {
		var total_indikator = +jQuery('#target_indikator_monev').text();
		var tipe_indikator = jQuery('#tipe_indikator').val();
		if (tipe_indikator == 3 && that) {
			var id = jQuery(that).attr('id');
			var bulan = +id.replace('target_realisasi_bulan_', '');
			if (bulan > 1) {
				var val_bulan_sebelumnya = +jQuery('#target_realisasi_bulan_' + (bulan - 1)).text();
				var val = jQuery(that).text();
				if (val < val_bulan_sebelumnya && val > 0) {
					jQuery(that).text(val_bulan_sebelumnya);
					alert('Untuk rumus indikator persentasi, nilai target tidak boleh lebih kecil dari bulan sebelumnya!');
				}
			}
		}
		var total = 0;
		var target_batas_bulan_input = 0;
		var bulan = 0;
		jQuery('#monev-body .target_realisasi').map(function() {
			bulan++;
			var target_bulanan = +jQuery(this).text();
			total += target_bulanan;
			if (batas_bulan_input == bulan) {
				target_batas_bulan_input = target_bulanan;
			}
		});
		var total_realisasi_indikator = 0;
		if (tipe_indikator == 1) {
			if (total_indikator > 0) {
				total_realisasi_indikator = Math.round((total / total_indikator) * 10000) / 100;
			}
		} else if (tipe_indikator == 2) {
			total = target_batas_bulan_input;
			if (total > 0) {
				total_realisasi_indikator = Math.round((total_indikator / total) * 10000) / 100;
			}
		} else if (tipe_indikator == 3) {
			total = target_batas_bulan_input;
			if (total_indikator > 0) {
				total_realisasi_indikator = Math.round((total / total_indikator) * 10000) / 100;
			}
		} else if (tipe_indikator == 4) {
			total = target_batas_bulan_input;
			if (total_indikator > 0) {
				total_realisasi_indikator = Math.round((total / total_indikator) * 10000) / 100;
			}
		}
		jQuery('#total_target_realisasi').text(total);
		jQuery('#capaian_target_realisasi').text(total_realisasi_indikator);
	}

	function setTotalRealisasi() {
		var total_rak = 0;
		var total_realisasi = 0;
		var total_selisih = 0;
		jQuery('#monev-body .target_realisasi').map(function() {
			var tr = jQuery(this).closest('tr');
			var nilai_rak = +tr.find('.nilai_rak').text().replace(/\./g, '');
			var nilai_realisasi = +tr.find('.nilai_realisasi').text().replace(/\./g, '');
			var nilai_selisih = nilai_rak - nilai_realisasi;
			tr.find('.nilai_selisih').text(formatRupiah(nilai_selisih));
			total_rak += nilai_rak;
			total_realisasi += nilai_realisasi;
			total_selisih += nilai_selisih;
		});
		jQuery('#total_nilai_rak').text(formatRupiah(total_rak));
		jQuery('#total_nilai_realisasi').text(formatRupiah(total_realisasi));
		jQuery('#total_nilai_selisih').text(formatRupiah(total_selisih));
	}

	function drawColColors() {
		var data_cart = [
			['Triwulan', 'Anggaran', 'Realisasi'],
			['Triwulan 1', <?php echo $data_all['rak_triwulan_1']; ?>, <?php echo $data_all['triwulan_1']; ?>],
			['Triwulan 2', <?php echo $data_all['rak_triwulan_2']; ?>, <?php echo $data_all['triwulan_2']; ?>],
			['Triwulan 3', <?php echo $data_all['rak_triwulan_3']; ?>, <?php echo $data_all['triwulan_3']; ?>],
			['Triwulan 4', <?php echo $data_all['rak_triwulan_4']; ?>, <?php echo $data_all['triwulan_4']; ?>],
		];

		var data = new google.visualization.arrayToDataTable(data_cart);

		var options = {
			title: 'ANGGARAN DAN REALISASI',
			colors: ['#ffc107', '#007bff'],
			hAxis: {
				title: 'TRIWULAN',
				minValue: 0
			},
			vAxis: {
				title: 'NILAI'
			}
		};

		var chart = new google.visualization.ColumnChart(document.getElementById('chart'));
		chart.draw(data, options);

		var no = 0;
		data_all_js.map(function(program, i) {
			no++;
			var id_cart = 'chart-program-' + no;
			var html = '<div id="' + id_cart + '" style="margin-buttom: 20px; min-height: 400px;" class="col-md-6"></div>';
			var id_cart_indikator = [];
			program.indikator.map(function(indikator, ii) {
				id_cart_indikator[ii] = id_cart + '-indikator-' + (ii + 1);
				html += '<div id="' + id_cart_indikator[ii] + '" style="margin-buttom: 20px; min-height: 400px;" class="col-md-6"></div>';
			});
			jQuery('#chart-program').append('<div class="col-md-12"><h3 class="text-center" style="margin-top: 30px;">' + program.nama + '</h3></div>' + html);
			var data_cart = [
				['Triwulan', 'Anggaran', 'Realisasi'],
				['Triwulan 1', +program.rak_tw_1, +program.realisasi_tw_1],
				['Triwulan 2', +program.rak_tw_2, +program.realisasi_tw_2],
				['Triwulan 3', +program.rak_tw_3, +program.realisasi_tw_3],
				['Triwulan 4', +program.rak_tw_4, +program.realisasi_tw_4],
			];

			var data = new google.visualization.arrayToDataTable(data_cart);

			var options = {
				title: 'Pagu Program: ' + program.pagu + ', Realisasi: ' + program.realisasi + ', Capaian: ' + program.capaian + '%',
				colors: ['#9575cd', '#33ac71'],
				hAxis: {
					title: 'Anggaran Kas dan Realisasi Anggaran Per Triwulan',
					minValue: 0
				},
				vAxis: {
					title: 'Rp'
				}
			};
			var chart = new google.visualization.ColumnChart(document.getElementById(id_cart));
			chart.draw(data, options);

			program.indikator.map(function(indikator, ii) {

				var data_cart = [
					['Triwulan', 'Realisasi Target Program'],
					['Triwulan 1', +program.realisasi_indikator_1[ii]],
					['Triwulan 2', +program.realisasi_indikator_2[ii]],
					['Triwulan 3', +program.realisasi_indikator_3[ii]],
					['Triwulan 4', +program.realisasi_indikator_4[ii]],
				];
				var data = new google.visualization.arrayToDataTable(data_cart);
				var options = {
					title: 'Indikator: ' + indikator + ', ' + 'Target: ' + program.target_indikator[ii] + ' ' + program.satuan[ii] + ', Realisasi: ' + program.realisasi_indikator[ii] + ' ' + program.satuan[ii],
					hAxis: {
						title: 'Realisasi Target Per Triwulan',
						minValue: 0
					},
					vAxis: {
						title: program.satuan[ii]
					}
				};
				var chart = new google.visualization.ColumnChart(document.getElementById(id_cart_indikator[ii]));
				chart.draw(data, options);
			});
		});
	}

	jQuery(document).on('ready', function() {
		run_download_excel('', '#aksi-wp-sipd');
		let lama_pelaksanaan = <?php echo $lama_pelaksanaan; ?>

		var aksi = '' +
			'<h3 style="margin-top: 20px;">PENGATURAN</h3>' +
			'<label><input type="checkbox" onclick="edit_monev_indikator(this);"> Edit Monev indikator</label>' +
			'<label style="margin-left: 20px;"><input type="checkbox" onclick="tampil_indikator_renstra(this);"> Tampilkan indikator RENSTRA</label>';
		jQuery('#action-sipd').append(aksi);

		google.charts.load('current', {
			packages: ['corechart', 'bar']
		});
		google.charts.setOnLoadCallback(drawColColors);

		window.batas_bulan_input = <?php echo $batas_bulan_input; ?>;
		window.data_all_js = <?php echo json_encode($data_all_js); ?>

		jQuery('#tipe_indikator').on('click', function() {
			setRumus(jQuery(this).val());
		});
		jQuery('.edit-monev').on('click', function() {
			jQuery('#wrap-loading').show();
			var id_unik = jQuery(this).attr('data-id');

			var tr = jQuery(this).closest('tr');
			var nama = tr.find('td.nama').prev().text() + ' ' + tr.find('td.nama').text();
			var id_indikator = id_unik.split('-').pop();
			var indikator_text = tr.find('td.indikator span[data-id="' + id_indikator + '"]').text();
			if (indikator_text == '') {
				indikator_text = tr.find('td.indikator').text();
			}
			var target_indikator_text = tr.find('td.target_indikator span[data-id="' + id_indikator + '"]').text();
			if (target_indikator_text == '') {
				target_indikator_text = tr.find('td.target_indikator').text();
			}
			var satuan_indikator_text = tr.find('td.satuan_indikator span[data-id="' + id_indikator + '"]').text();
			if (satuan_indikator_text == '') {
				satuan_indikator_text = tr.find('td.satuan_indikator').text();
			}
			var pagu_renja = tr.find('td.pagu_renja').attr('data-pagu');
			var pagu_renja_text = tr.find('td.pagu_renja').text();
			var indikator = '' +
				'<tr>' +
				'<td>' + indikator_text + '</td>' +
				'<td class="text_tengah" id="target_indikator_monev">' + target_indikator_text + '</td>' +
				'<td class="text_tengah">' + satuan_indikator_text + '</td>' +
				'</tr>';
			jQuery('#target_indikator_monev_rumus').text(target_indikator_text + ' ' + satuan_indikator_text);
			jQuery.ajax({
				url: ajax.url,
				type: "post",
				data: {
					"action": "get_monev",
					"api_key": "<?php echo $api_key; ?>",
					"tahun_anggaran": <?php echo $input['tahun_anggaran']; ?>,
					"id_unik": id_unik
				},
				dataType: "json",
				success: function(res) {
					jQuery('#monev-nama').text(nama);
					jQuery('#monev-indikator').html(indikator);
					jQuery('#monev-pagu').attr('data-pagu', pagu_renja).text(pagu_renja_text);
					jQuery('#monev-body').html(res.table);
					jQuery('#mod-monev').attr('data-id_unik', id_unik);
					jQuery('#bobotKinerja').text(res.bobot_kinerja);

					setRumus(res.id_rumus_indikator);
					jQuery('#monev-body-renstra').html('');
					if (res.tipe_indikator == 2 || res.tipe_indikator == 3) {
						jQuery(".display-indikator-renstra").show();
						var renstra_html = '';
						tr.find('.indikator_renstra li').map(function(i, b) {
							var id_indikator_renstra = jQuery(b).attr('data-id');
							var indikator_renstra_text = jQuery(b).find('.indikator_renstra_text_hide').text();
							var indikator_renstra_target = jQuery(b).find('.target_indikator_renstra_text_hide').text().split(' | ');
							var indikator_renstra_satuan = jQuery(b).find('.satuan_indikator_renstra_text_hide').text();
							var indikator_renstra_pagu = jQuery(b).find('.pagu_indikator_renstra_text_hide').text();
							var total_indikator_renstra_pagu = jQuery(b).find('.total_pagu_indikator_renstra_text_hide').text();
							var checked = '';
							if (res.id_unik_indikator_renstra == id_indikator_renstra) {
								checked = 'checked';
							}
							renstra_html += '<tr>' +
								'<td class="text_tengah"><input type="radio" ' + checked + ' value="' + id_indikator_renstra + '" name="pilih_indikator_renstra"></td>' +
								'<td>' + indikator_renstra_text + '</td>';
							// Menambahkan kolom target secara dinamis berdasarkan lama pelaksanaan
							for (let i = 0; i < lama_pelaksanaan; i++) {
								renstra_html += '<td class="text_tengah target_renstra_' + (i + 1) + '">' + indikator_renstra_target[i] + '</td>';
							}
							renstra_html += '<td class="text_tengah mod_satuan_renstra">' + indikator_renstra_satuan + '</td>' +
								'<td class="text_kanan mod_total_renstra">' + total_indikator_renstra_pagu + '</td>' +
								'</tr>';

						});
						if (renstra_html == '') {
							renstra_html = '' +
								'<tr>' +
								'<td colspan="9" class="text_tengah">Data kosong!</td>' +
								'</tr>'
						}
						jQuery('#monev-body-renstra').html(renstra_html);
					} else {
						jQuery(".display-indikator-renstra").hide();
					}
					jQuery('#mod-monev').modal('show');
					jQuery('#wrap-loading').hide();
				}
			});
		});
		jQuery('#set-monev').on('click', function() {
			var nilai_rak = {};
			var nilai_realisasi = {};
			var target_realisasi = {};
			var keterangan = {};
			var pendorong = {};
			var total_tw1 = 0;
			var total_tw2 = 0;
			var total_tw3 = 0;
			var total_tw4 = 0;
			var total_tw = jQuery('#total_target_realisasi').text();
			var total_tw1_realisasi = 0;
			var total_tw2_realisasi = 0;
			var total_tw3_realisasi = 0;
			var total_tw4_realisasi = 0;
			var total_tw_realisasi = jQuery('#total_nilai_realisasi').text();
			var capaian_realisasi_indikator = jQuery('#capaian_target_realisasi').text();
			var tipe_indikator = jQuery('#tipe_indikator').val();
	    var faktor_penghambat = false;
	    var faktor_pendorong = false;
			for (var i = 1; i <= 12; i++) {
				var id_rak = 'nilai_rak_bulan_' + i;
				var id_realisasi = 'nilai_realisasi_bulan_' + i;
				var id = 'target_realisasi_bulan_' + i;
				var id_ket = 'keterangan_bulan_' + i; //faktor penghambat
				var id_pendorong = 'pendorong_bulan_' + i; //faktor pendorong
				nilai_rak[id_rak] = +jQuery('#' + id_rak).text().trim();
				nilai_realisasi[id_realisasi] = +jQuery('#' + id_realisasi).text().trim().replace(/\./g, '');
				target_realisasi[id] = +jQuery('#' + id).text().trim();
				keterangan[id_ket] = jQuery('#' + id_ket).text().trim();
				pendorong[id_pendorong] = jQuery('#' + id_pendorong).text().trim();
				
				if (keterangan[id_ket] !== '' && keterangan[id_ket] !== null) {
            faktor_penghambat = true;
        }

        if (pendorong[id_pendorong] !== '' && pendorong[id_pendorong] !== null) {
            faktor_pendorong = true;
        }
        
				if (i <= 3) {
					total_tw1_realisasi += nilai_realisasi[id_realisasi];
					if (tipe_indikator == 3 || tipe_indikator == 2 || tipe_indikator == 4) {
						if (i == 3) {
							total_tw1 = target_realisasi[id];
						}
					} else {
						total_tw1 += target_realisasi[id];
					}
				} else if (i <= 6) {
					total_tw2_realisasi += nilai_realisasi[id_realisasi];
					if (tipe_indikator == 3 || tipe_indikator == 2 || tipe_indikator == 4) {
						if (i == 6) {
							total_tw2 = target_realisasi[id];
						}
					} else {
						total_tw2 += target_realisasi[id];
					}
				} else if (i <= 9) {
					total_tw3_realisasi += nilai_realisasi[id_realisasi];
					if (tipe_indikator == 3 || tipe_indikator == 2 || tipe_indikator == 4) {
						if (i == 9) {
							total_tw3 = target_realisasi[id];
						}
					} else {
						total_tw3 += target_realisasi[id];
					}
				} else if (i <= 12) {
					total_tw4_realisasi += nilai_realisasi[id_realisasi];
					if (tipe_indikator == 3 || tipe_indikator == 2 || tipe_indikator == 4) {
						if (i == 12) {
							total_tw4 = target_realisasi[id];
						}
					} else {
						total_tw4 += target_realisasi[id];
					}
				}
			}

	    if (!faktor_pendorong && !faktor_penghambat) {
        alert('Faktor Pendorong dan Faktor Penghambat wajib diisi salah satu');
        return false;
	    }

	    if (!faktor_pendorong) {
        alert('Faktor Pendorong wajib diisi salah satu');
        return false;
	    } 

	    if (!faktor_penghambat) {
        alert('Faktor Penghambat wajib diisi salah satu');
        return false;
	    }	    	    

			var status = false;
			var id_unik = jQuery('#mod-monev').attr('data-id_unik');
			var jenisIndikator = jenis_indikator(id_unik);

			if (jenisIndikator == 6) {
				status = true;
			} else if (jenisIndikator == 5 || jenisIndikator == 3) {
				var cek_indikator_renstra = jQuery('input[name="pilih_indikator_renstra"]');
				var id_indikator_renstra = '';
				if (cek_indikator_renstra.length >= 1) {
					id_indikator_renstra = jQuery('input[name="pilih_indikator_renstra"]:checked').val();
					if (!id_indikator_renstra) {
						alert('Indiktor renstra belum dipilih, klik ok untuk melanjutkan!');
					}
				}
				status = true;
			}

			if (status) {
				if (confirm('Apakah anda yakin untuk menyimpan data ini!')) {
					jQuery('#wrap-loading').show();
					var id_unik = jQuery('#mod-monev').attr('data-id_unik');
					jQuery.ajax({
						url: ajax.url,
						type: "post",
						data: {
							"action": "save_monev_renja",
							"api_key": "<?php echo $api_key; ?>",
							"tahun_anggaran": <?php echo $input['tahun_anggaran']; ?>,
							"id_unik": id_unik,
							"data": target_realisasi,
							"keterangan": keterangan,
							"pendorong": pendorong,
							"rak": nilai_rak,
							"realisasi": nilai_realisasi,
							"bobot_kinerja": jQuery('#bobotKinerja').text(),
							"rumus_indikator": jQuery('#tipe_indikator').val(),
							"id_indikator_renstra": id_indikator_renstra
						},
						dataType: "json",
						success: function(res) {
							var tr = jQuery('.edit-monev[data-id="' + id_unik + '"]').closest('tr');
							var ids = id_unik.split('-');
							var id_indikator = ids[4];
							jQuery(tr).find('.realisasi_indikator_tw1-' + id_indikator).text(total_tw1);
							jQuery(tr).find('.realisasi_indikator_tw2-' + id_indikator).text(total_tw2);
							jQuery(tr).find('.realisasi_indikator_tw3-' + id_indikator).text(total_tw3);
							jQuery(tr).find('.realisasi_indikator_tw4-' + id_indikator).text(total_tw4);
							jQuery(tr).find('.realisasi_indikator_tw4-' + id_indikator).text(total_tw4);
							jQuery(tr).find('.total_tw-' + id_indikator).text(total_tw);
							<?php if ($crb_cara_input_realisasi == 2) { ?>
								jQuery(tr).find('.nilai_realisasi_tw1').text(formatRupiah(total_tw1_realisasi));
								jQuery(tr).find('.nilai_realisasi_tw2').text(formatRupiah(total_tw2_realisasi));
								jQuery(tr).find('.nilai_realisasi_tw3').text(formatRupiah(total_tw3_realisasi));
								jQuery(tr).find('.nilai_realisasi_tw4').text(formatRupiah(total_tw4_realisasi));
								jQuery(tr).find('.nilai_realisasi_renja').text(total_tw_realisasi);
							<?php } ?>
							jQuery(tr).find('.capaian_realisasi_indikator-' + id_indikator).text(capaian_realisasi_indikator);
							jQuery(tr).find('.rumus_indikator').removeClass('positif negatif persentase');
							var rumus_indikator = 'positif';
							if (tipe_indikator == 2) {
								rumus_indikator = 'negatif';
							} else if (tipe_indikator == 3) {
								rumus_indikator = 'persentase';
							} else if (tipe_indikator == 4) {
								rumus_indikator = 'nilai_akhir';
							}
							jQuery(tr).find('.rumus_indikator').addClass(rumus_indikator);
							if (id_indikator_renstra) {
								var tr_modal = jQuery('input[value="' + id_indikator_renstra + '"]').closest('tr');
								var target_1 = +tr_modal.find('td.target_renstra_1').text().replace(/,/g, '.').trim();
								var target_2 = +tr_modal.find('td.target_renstra_2').text().replace(/,/g, '.').trim();
								var target_3 = +tr_modal.find('td.target_renstra_3').text().replace(/,/g, '.').trim();
								var target_4 = +tr_modal.find('td.target_renstra_4').text().replace(/,/g, '.').trim();
								var target_5_asli = tr_modal.find('td.target_renstra_5').text().replace(/,/g, '.').trim();
								var target_5 = +target_5_asli;
								var total_target_renstra = target_1 + target_2 + target_3 + target_4 + target_5;

								// cek jika type target bukan interger
								if (isNaN(target_5) || isNaN(total_target_renstra)) {
									target_5 = target_5_asli;
									total_target_renstra = target_5;
								}
								if (tipe_indikator == 2 || tipe_indikator == 3 || tipe_indikator == 4) {
									total_target_renstra = target_5;
								}
								jQuery(tr).find('.total_target_renstra[data-id="' + id_indikator + '"]').text(total_target_renstra);
								var satuan_indikator_renstra = tr_modal.find('td.mod_satuan_renstra').text().trim();
								jQuery(tr).find('.satuan_renstra[data-id="' + id_indikator + '"]').text(satuan_indikator_renstra);
								var total_indikator_renstra = tr_modal.find('td.mod_total_renstra').text().trim();
								jQuery(tr).find('.monev_total_renstra[data-id="' + id_indikator + '"]').text(total_indikator_renstra);
							}
							jQuery('#mod-monev').modal('hide');
							jQuery('#wrap-loading').hide();
						}
					});
				}
			}
		});
		jQuery('.simpan_monev_triwulan').on('click', function() {
			if (confirm('Apakah anda yakin untuk menyimpan data ini?')) {
				jQuery('#wrap-loading').show();
				var tr = jQuery(this).closest('tr');
				var triwulan = tr.attr('data-tw');
				var keterangan_skpd = tr.find('.keterangan_skpd').text();
				var catatan_verifikator = tr.find('.catatan_verifikator').text();
				var file_data = tr.find('.upload_monev');
				if (file_data.length >= 1) {
					file_data = file_data.prop('files')[0];
				} else {
					file_data = '';
				}
				var form_data = new FormData();
				form_data.append('file', file_data);
				form_data.append('api_key', "<?php echo $api_key; ?>");
				form_data.append('tahun_anggaran', <?php echo $input['tahun_anggaran']; ?>);
				form_data.append('id_skpd', <?php echo $input['id_skpd']; ?>);
				form_data.append('triwulan', triwulan);
				form_data.append('keterangan_skpd', keterangan_skpd);
				form_data.append('catatan_verifikator', catatan_verifikator);
				jQuery.ajax({
					url: ajax.url + '?action=save_monev_renja_triwulan',
					type: "post",
					data: form_data,
					dataType: "json",
					cache: false,
					contentType: false,
					processData: false,
					success: function(res) {
						if (res.status == 'success') {
							if (res.update_skpd_at) {
								var file_html = '' +
									'<div style="padding-top: 10px;">' +
									'<a class="file_monev" href="<?php echo esc_url(plugin_dir_url(__DIR__) . 'media/'); ?>' + res.nama_file + '" target="_blank">' + res.nama_file + '</a>' +
									'</div>';
								tr.find('.file_monev').closest('div').remove();
								tr.find('.lampiran_excel').append(file_html);
								tr.find('.tgl_update_file').text(res.update_skpd_at);
							} else {
								tr.find('.tgl_update_catatan_renja').text(res.update_verifikator_at);
							}
						}
						jQuery('#wrap-loading').hide();
						alert(res.message);
					}
				});
			}
		});
		jQuery('.hapus_monev_triwulan').on('click', function() {
			var tr = jQuery(this).closest('tr');
			var file = tr.find('.file_monev');
			if (file.length <= 0) {
				return alert('File belum diupload!');
			} else {
				var file_name = file.text();
				if (confirm('Apakah anda yakin untuk menghapus file upload ' + file_name + '?')) {
					jQuery('#wrap-loading').show();
					var triwulan = tr.attr('data-tw');
					var form_data = new FormData();
					form_data.append('file_remove', 1);
					form_data.append('api_key', "<?php echo $api_key; ?>");
					form_data.append('tahun_anggaran', <?php echo $input['tahun_anggaran']; ?>);
					form_data.append('id_skpd', <?php echo $input['id_skpd']; ?>);
					form_data.append('triwulan', triwulan);
					jQuery.ajax({
						url: ajax.url + '?action=save_monev_renja_triwulan',
						type: "post",
						data: form_data,
						dataType: "json",
						cache: false,
						contentType: false,
						processData: false,
						success: function(res) {
							file.closest('div').remove();
							tr.find('.tgl_update_file').text(res.update_skpd_at);
							jQuery('#wrap-loading').hide();
							alert(res.message);
						}
					});
				}
			}
		});
	});
</script>