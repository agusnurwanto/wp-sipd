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
$unit = $wpdb->get_row($sql, ARRAY_A);

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
	", $input['tahun_anggaran'], $unit['id_skpd']),
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
	", $input['tahun_anggaran'], $unit['id_skpd'], $sub['kode_sbl'], $bulan), ARRAY_A);
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
			'id_skpd' 		 => $unit['id_skpd'],
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
		", $input['tahun_anggaran'], $unit['id_skpd'], $sub['kode_sbl'], $bulan), ARRAY_A);
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
			'rak_triwulan_1' 			 => 0,
			'rak_triwulan_2' 			 => 0,
			'rak_triwulan_3' 			 => 0,
			'rak_triwulan_4' 			 => 0,
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
			'rak_triwulan_1' 			 => 0,
			'rak_triwulan_2' 			 => 0,
			'rak_triwulan_3' 			 => 0,
			'rak_triwulan_4' 			 => 0,
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
			'rak_triwulan_1' 			 => 0,
			'rak_triwulan_2' 			 => 0,
			'rak_triwulan_3' 			 => 0,
			'rak_triwulan_4' 			 => 0,
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
			'rak_triwulan_1' 			 => 0,
			'rak_triwulan_2' 			 => 0,
			'rak_triwulan_3' 			 => 0,
			'rak_triwulan_4' 			 => 0,
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
			'rak_triwulan_1' 			 => 0,
			'rak_triwulan_2' 			 => 0,
			'rak_triwulan_3' 			 => 0,
			'rak_triwulan_4' 			 => 0,
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
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['rak_triwulan_1'] += $rak_triwulan_1;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['rak_triwulan_1'] += $rak_triwulan_1;

	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['rak_triwulan_2'] += $rak_triwulan_2;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['rak_triwulan_2'] += $rak_triwulan_2;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['rak_triwulan_2'] += $rak_triwulan_2;

	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['rak_triwulan_3'] += $rak_triwulan_3;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['rak_triwulan_3'] += $rak_triwulan_3;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['rak_triwulan_3'] += $rak_triwulan_3;

	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['rak_triwulan_4'] += $rak_triwulan_4;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['rak_triwulan_4'] += $rak_triwulan_4;
	$data_all['data'][$sub['kode_urusan']]['data'][$sub['kode_bidang_urusan']]['data'][$sub['kode_program']]['data'][$sub['kode_giat']]['data'][$sub['kode_sub_giat']]['rak_triwulan_4'] += $rak_triwulan_4;

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

$monev_triwulan = $wpdb->get_results(
	$wpdb->prepare("
		SELECT 
			triwulan,
			file_monev,
			update_skpd_at, 
			keterangan_skpd, 
			catatan_verifikator, 
			update_verifikator_at 
		FROM data_monev_renja_triwulan 
		WHERE id_skpd = %d
		  AND tahun_anggaran = %d
	", $input['id_skpd'], $input['tahun_anggaran']),
	ARRAY_A
);
$data_all['catatan_rekomendasi'] = $monev_triwulan;
die(json_encode($data_all));
?>