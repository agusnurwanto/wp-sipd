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


$body_monev   	 = '';
$rowspan   	 = 1;
$is_first_row   	 = true;
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
			$capaian_anggaran_tw1 	= array();
			$capaian_anggaran_tw2 	= array();
			$capaian_anggaran_tw3 	= array();
			$capaian_anggaran_tw4 	= array();
			$total_tw 					= array();
			$capaian_realisasi_indikator = array();
			$class_rumus_target 		= array();
			$total_target_renstra_text 	= array();
			$total_target_renstra 		= array();
			$satuan_renstra 			= array();
			$total_pagu_renstra 		= array();
			$total_pagu_renstra_renja 	= array();
			$pendorong_html = array();
			$penghambat_html = array();
			if (!empty($program['indikator'])) {
				$realisasi_indikator = array();
				foreach ($program['realisasi_indikator'] as $k_sub => $v_sub) {
					$realisasi_indikator[$v_sub['id_indikator']] = $v_sub;
				}
				foreach ($program['indikator'] as $k_sub => $v_sub) {
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
					$capaian_anggaran_tw1[$k_sub] 	 = !empty($program['rak_triwulan_1']) ? $this->pembulatan(($program['triwulan_1'] / $program['rak_triwulan_1']) * 100) : 0;
					$capaian_anggaran_tw2[$k_sub] 	 = !empty($program['rak_triwulan_2']) ? $this->pembulatan(($program['triwulan_2'] / $program['rak_triwulan_2']) * 100) : 0;
					$capaian_anggaran_tw3[$k_sub] 	 = !empty($program['rak_triwulan_3']) ? $this->pembulatan(($program['triwulan_3'] / $program['rak_triwulan_3']) * 100) : 0;
					$capaian_anggaran_tw4[$k_sub] 	 = !empty($program['rak_triwulan_4']) ? $this->pembulatan(($program['triwulan_4'] / $program['rak_triwulan_4']) * 100) : 0;
					$total_tw[$k_sub] 					 = 0;
					$capaian_realisasi_indikator[$k_sub] = 0;
					$realisasi_indikator_tw1_js[$k_sub]  = 0;
					$realisasi_indikator_tw2_js[$k_sub]  = 0;
					$realisasi_indikator_tw3_js[$k_sub]  = 0;
					$realisasi_indikator_tw4_js[$k_sub]  = 0;
					$total_tw_js[$k_sub] 				 = 0;
					$class_rumus_target[$k_sub] 		 = " positif";
					$pendorong_html[$k_sub] 	= "";
					$penghambat_html[$k_sub] 	= "";

					if (!empty($realisasi_indikator) && !empty($realisasi_indikator[$k_sub])) {
						$rumus_indikator = $realisasi_indikator[$k_sub]['id_rumus_indikator'];
						$max = 0;

						$pendorong_list = [];
						$penghambat_list = [];

						for ($i = 1; $i <= 12; $i++) {
							$pendorong_text = trim($realisasi_indikator[$k_sub]['pendorong_bulan_' . $i] ?? '');
							$penghambat_text = trim($realisasi_indikator[$k_sub]['keterangan_bulan_' . $i] ?? '');

							// die(var_dump($realisasi_indikator[$k_sub]));
							if ($pendorong_text !== '') {
								$pendorong_list[] = "<li>{$pendorong_text}</li>";
							}
							if ($penghambat_text !== '') {
								$penghambat_list[] = "<li>{$penghambat_text}</li>";
							}

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
						if (!empty($pendorong_list)) {
							$pendorong_html[$k_sub] = "<ul>" . implode("", $pendorong_list) . "</ul>";
						} else {
							$pendorong_html[$k_sub] = "-";
						}

						if (!empty($penghambat_list)) {
							$penghambat_html[$k_sub] = "<ul>" . implode("", $penghambat_list) . "</ul>";
						} else {
							$penghambat_html[$k_sub] = "-";
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
										
					$capaian_anggaran_tw1[$k_sub] = '<span class="capaian_anggaran_tw1-' . $k_sub . '">' . $capaian_anggaran_tw1[$k_sub] . '</span>';
					$capaian_anggaran_tw2[$k_sub] = '<span class="capaian_anggaran_tw2-' . $k_sub . '">' . $capaian_anggaran_tw2[$k_sub] . '</span>';
					$capaian_anggaran_tw3[$k_sub] = '<span class="capaian_anggaran_tw3-' . $k_sub . '">' . $capaian_anggaran_tw3[$k_sub] . '</span>';
					$capaian_anggaran_tw4[$k_sub] = '<span class="capaian_anggaran_tw4-' . $k_sub . '">' . $capaian_anggaran_tw4[$k_sub] . '</span>';

					$penghambat_html[$k_sub] = '<span class="penghambat_html-' . $k_sub . '">' . $penghambat_html[$k_sub] . '</span>';
					$pendorong_html[$k_sub] = '<span class="pendorong_html-' . $k_sub . '">' . $pendorong_html[$k_sub] . '</span>';

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

			$capaian_anggaran_tw1 	 = implode('<br>', $capaian_anggaran_tw1);
			$capaian_anggaran_tw2 	 = implode('<br>', $capaian_anggaran_tw2);
			$capaian_anggaran_tw3 	 = implode('<br>', $capaian_anggaran_tw3);
			$capaian_anggaran_tw4 	 = implode('<br>', $capaian_anggaran_tw4);
			$total_tw 				 	 = implode('<br>', $total_tw);
			$capaian_realisasi_indikator = implode('<br>', $capaian_realisasi_indikator);

			$pendorong_html = implode('<br>', $pendorong_html);
			$penghambat_html = implode('<br>', $penghambat_html);

			if ($is_first_row) {
				$is_first_row = false;
				$first_row = '
					<tr class="tr-program program" data-kode="' . $kd_urusan . '.' . $kd_bidang . '.' . $kd_program . '" data-bidang-urusan="' . $program['kode_urusan_bidang'] . '">
						<td class="kiri kanan bawah text_blok">' . $no_program . '</td>
						<td class="kanan bawah text_blok">' . $kd_program_asli . '</td>
						<td class="kanan bawah text_blok nama">' . $program['nama'] . '</td>
						<td class="kanan bawah text_blok indikator">' . $capaian_prog . '</td>
						<td class="text_tengah kanan bawah text_blok total_renja target_indikator">' . $target_capaian_prog . '</td>
						<td class="text_tengah kanan bawah text_blok total_renja satuan_indikator">' . $satuan_capaian_prog . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_1">' . $realisasi_indikator_tw1 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_1">' . $capaian_anggaran_tw1 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_2">' . $realisasi_indikator_tw2 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_2">' . $capaian_anggaran_tw2 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_3">' . $realisasi_indikator_tw3 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_3">' . $capaian_anggaran_tw3 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_4">' . $realisasi_indikator_tw4 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_4">' . $capaian_anggaran_tw4 . '</td>
						<td class="text_tengah kanan bawah text_blok capaian_renja">' . $total_tw . '</td>
						<td class="text_tengah kanan bawah text_blok capaian_renja">' . $capaian_realisasi_indikator . '</td>
						<td class="text_kanan kanan bawah text_blok capaian_renja">' . $capaian . '</td>
						<td class="kanan bawah text_blok" data-kode-progkeg="' . $kd_urusan . '.' . $kd_bidang . '.' . $kd_program . '"></td>
						<td class="kanan bawah text_blok">' . $pendorong_html . '</td>
						<td class="kanan bawah text_blok">' . $penghambat_html . '</td>
					';
			} else {
				$body_monev .= '
					<tr class="tr-program program" data-kode="' . $kd_urusan . '.' . $kd_bidang . '.' . $kd_program . '" data-bidang-urusan="' . $program['kode_urusan_bidang'] . '">
						<td class="kiri kanan bawah text_blok">' . $no_program . '</td>
						<td class="kanan bawah text_blok">' . $kd_program_asli . '</td>
						<td class="kanan bawah text_blok nama">' . $program['nama'] . '</td>
						<td class="kanan bawah text_blok indikator">' . $capaian_prog . '</td>
						<td class="text_tengah kanan bawah text_blok total_renja target_indikator">' . $target_capaian_prog . '</td>
						<td class="text_tengah kanan bawah text_blok total_renja satuan_indikator">' . $satuan_capaian_prog . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_1">' . $realisasi_indikator_tw1 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_1">' . $capaian_anggaran_tw1 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_2">' . $realisasi_indikator_tw2 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_2">' . $capaian_anggaran_tw2 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_3">' . $realisasi_indikator_tw3 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_3">' . $capaian_anggaran_tw3 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_4">' . $realisasi_indikator_tw4 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_4">' . $capaian_anggaran_tw4 . '</td>
						<td class="text_kanan kanan bawah text_blok capaian_renja">' . $total_tw . '</td>
						<td class="text_tengah kanan bawah text_blok capaian_renja">' . $capaian_realisasi_indikator . '</td>
						<td class="text_kanan kanan bawah text_blok capaian_renja">' . $capaian . '</td>
						<td class="kanan bawah text_blok" data-kode-progkeg="' . $kd_urusan . '.' . $kd_bidang . '.' . $kd_program . '"></td>
						<td class="kanan bawah text_blok">' . $pendorong_html . '</td>
						<td class="kanan bawah text_blok">' . $penghambat_html . '</td>
					</tr>
				';
			}

			$rowspan++;
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
				$capaian_anggaran_tw1 	 = array();
				$capaian_anggaran_tw2 	 = array();
				$capaian_anggaran_tw3 	 = array();
				$capaian_anggaran_tw4 	 = array();
				$total_tw 				 	 = array();
				$capaian_realisasi_indikator = array();
				$class_rumus_target 		 = array();
				$pendorong_html = array();
				$penghambat_html = array();
				if (!empty($giat['indikator'])) {
					$realisasi_indikator = array();
					foreach ($giat['realisasi_indikator'] as $k_sub => $v_sub) {
						$realisasi_indikator[$v_sub['id_indikator']] = $v_sub;
					}
					foreach ($giat['indikator'] as $k_sub => $v_sub) {
						$target_output_giat[$k_sub] = ' <span data-id="' . $k_sub . '">' . $v_sub['targetoutput'] . '</span>';
						$satuan_output_giat[$k_sub] = '<span data-id="' . $k_sub . '">' . $v_sub['satuanoutput'] . '</span>';
						$target_indikator = $v_sub['targetoutput'];
						$realisasi_indikator_tw1[$k_sub] = 0;
						$realisasi_indikator_tw2[$k_sub] = 0;
						$realisasi_indikator_tw3[$k_sub] = 0;
						$realisasi_indikator_tw4[$k_sub] = 0;

						$capaian_anggaran_tw1[$k_sub] 	 = !empty($giat['rak_triwulan_1']) ? $this->pembulatan(($giat['triwulan_1'] / $giat['rak_triwulan_1']) * 100) : 0;
						$capaian_anggaran_tw2[$k_sub] 	 = !empty($giat['rak_triwulan_2']) ? $this->pembulatan(($giat['triwulan_2'] / $giat['rak_triwulan_2']) * 100) : 0;
						$capaian_anggaran_tw3[$k_sub] 	 = !empty($giat['rak_triwulan_3']) ? $this->pembulatan(($giat['triwulan_3'] / $giat['rak_triwulan_3']) * 100) : 0;
						$capaian_anggaran_tw4[$k_sub] 	 = !empty($giat['rak_triwulan_4']) ? $this->pembulatan(($giat['triwulan_4'] / $giat['rak_triwulan_4']) * 100) : 0;
						$total_tw[$k_sub] = 0;
						$capaian_realisasi_indikator[$k_sub] = 0;
						$class_rumus_target[$k_sub] = "positif";
						$pendorong_html[$k_sub] 	= "";
						$penghambat_html[$k_sub] 	= "";

						if (
							!empty($realisasi_indikator)
							&& !empty($realisasi_indikator[$k_sub])
						) {
							$rumus_indikator = $realisasi_indikator[$k_sub]['id_rumus_indikator'];
							$max = 0;
							$pendorong_list = [];
							$penghambat_list = [];
							for ($i = 1; $i <= 12; $i++) {
								$realisasi_bulan = $realisasi_indikator[$k_sub]['realisasi_bulan_' . $i];
								$pendorong_text = trim($realisasi_indikator[$k_sub]['pendorong_bulan_' . $i] ?? '');
								$penghambat_text = trim($realisasi_indikator[$k_sub]['keterangan_bulan_' . $i] ?? '');

								// die(var_dump($realisasi_indikator[$k_sub]));
								if ($pendorong_text !== '') {
									$pendorong_list[] = "<li>{$pendorong_text}</li>";
								}
								if ($penghambat_text !== '') {
									$penghambat_list[] = "<li>{$penghambat_text}</li>";
								}
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
							if (!empty($pendorong_list)) {
								$pendorong_html[$k_sub] = "<ul>" . implode("", $pendorong_list) . "</ul>";
							} else {
								$pendorong_html[$k_sub] = "-";
							}

							if (!empty($penghambat_list)) {
								$penghambat_html[$k_sub] = "<ul>" . implode("", $penghambat_list) . "</ul>";
							} else {
								$penghambat_html[$k_sub] = "-";
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

						$capaian_anggaran_tw1[$k_sub] = '<span class="capaian_anggaran_tw1-' . $k_sub . '">' . $capaian_anggaran_tw1[$k_sub] . '</span>';
						$capaian_anggaran_tw2[$k_sub] = '<span class="capaian_anggaran_tw2-' . $k_sub . '">' . $capaian_anggaran_tw2[$k_sub] . '</span>';
						$capaian_anggaran_tw3[$k_sub] = '<span class="capaian_anggaran_tw3-' . $k_sub . '">' . $capaian_anggaran_tw3[$k_sub] . '</span>';
						$capaian_anggaran_tw4[$k_sub] = '<span class="capaian_anggaran_tw4-' . $k_sub . '">' . $capaian_anggaran_tw4[$k_sub] . '</span>';

						$penghambat_html[$k_sub] = '<span class="penghambat_html-' . $k_sub . '">' . $penghambat_html[$k_sub] . '</span>';
						$pendorong_html[$k_sub] = '<span class="pendorong_html-' . $k_sub . '">' . $pendorong_html[$k_sub] . '</span>';

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

				$capaian_anggaran_tw1 	 = implode('<br>', $capaian_anggaran_tw1);
				$capaian_anggaran_tw2 	 = implode('<br>', $capaian_anggaran_tw2);
				$capaian_anggaran_tw3 	 = implode('<br>', $capaian_anggaran_tw3);
				$capaian_anggaran_tw4 	 = implode('<br>', $capaian_anggaran_tw4);

				$total_tw = implode('<br>', $total_tw);
				$capaian_realisasi_indikator = implode('<br>', $capaian_realisasi_indikator);

				$pendorong_html = implode('<br>', $pendorong_html);
				$penghambat_html = implode('<br>', $penghambat_html);

				$body_monev .= '
					<tr class="tr-kegiatan kegiatan" data-kode="' . $kd_urusan . '.' . $kd_bidang . '.' . $kd_program . '.' . $kd_giat . '" data-kode_giat="' . $kd_giat1 . '" data-bidang-urusan="' . $giat['kode_urusan_bidang'] . '">
						<td class="kiri kanan bawah text_blok">' . $no_program . '.' . $no_kegiatan . '</td>
						<td class="kanan bawah text_blok">' . $kd_giat1 . '</td>
						<td class="kanan bawah text_blok nama">' . $giat['nama'] . '</td>
						<td class="kanan bawah text_blok indikator">' . $output_giat . '</td>
						<td class="text_tengah kanan bawah text_blok total_renja target_indikator">' . $target_output_giat . '</td>
						<td class="text_tengah kanan bawah text_blok total_renja satuan_indikator">' . $satuan_output_giat . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_1">' . $realisasi_indikator_tw1 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_1">' . $capaian_anggaran_tw1 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_2">' . $realisasi_indikator_tw2 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_2">' . $capaian_anggaran_tw2 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_3">' . $realisasi_indikator_tw3 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_3">' . $capaian_anggaran_tw3 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_4">' . $realisasi_indikator_tw4 . '</td>
						<td class="text_tengah kanan bawah text_blok triwulan_4">' . $capaian_anggaran_tw4 . '</td>
						<td class="text_tengah kanan bawah text_blok capaian_renja">' . $total_tw . '</td>
						<td class="text_tengah kanan bawah text_blok capaian_renja">' . $capaian_realisasi_indikator . '</td>
						<td class="text_kanan kanan bawah text_blok capaian_renja">' . $capaian . '</td>
						<td class="kanan bawah text_blok" data-kode-progkeg="' . $kd_bidang . '.' . $kd_program . '.' . $kd_giat . '"></td>
						<td class="kanan bawah text_blok">' . $pendorong_html . '</td>
						<td class="kanan bawah text_blok">' . $penghambat_html . '</td>
					</tr>
					';
				$rowspan++;
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
					$capaian_anggaran_tw1 = array();
					$capaian_anggaran_tw2 = array();
					$capaian_anggaran_tw3 = array();
					$capaian_anggaran_tw4 = array();
					$total_tw = array();
					$capaian_realisasi_indikator = array();
					$class_rumus_target = array();
					$pendorong_html = array();
					$penghambat_html = array();
					if (!empty($sub_giat['indikator'])) {
						$realisasi_indikator = array();
						foreach ($sub_giat['realisasi_indikator'] as $k_sub => $v_sub) {
							$realisasi_indikator[$v_sub['id_indikator']] = $v_sub;
						}
						foreach ($sub_giat['indikator'] as $k_sub => $v_sub) {
							
							$target_output_sub_giat[] = ' <span data-id="' . $v_sub['idoutputbl'] . '">' . $v_sub['targetoutput'] . '</span>';
							$satuan_output_sub_giat[] = '<span data-id="' . $v_sub['idoutputbl'] . '">' . $v_sub['satuanoutput'] . '</span>';
							$target_indikator = $v_sub['targetoutput'];
							$realisasi_indikator_tw1[$k_sub] = 0;
							$realisasi_indikator_tw2[$k_sub] = 0;
							$realisasi_indikator_tw3[$k_sub] = 0;
							$realisasi_indikator_tw4[$k_sub] = 0;

							$capaian_anggaran_tw1[$k_sub] 	 = !empty($sub_giat['rak_triwulan_1']) ? $this->pembulatan(($sub_giat['triwulan_1'] / $sub_giat['rak_triwulan_1']) * 100) : 0;
							$capaian_anggaran_tw2[$k_sub] 	 = !empty($sub_giat['rak_triwulan_2']) ? $this->pembulatan(($sub_giat['triwulan_2'] / $sub_giat['rak_triwulan_2']) * 100) : 0;
							$capaian_anggaran_tw3[$k_sub] 	 = !empty($sub_giat['rak_triwulan_3']) ? $this->pembulatan(($sub_giat['triwulan_3'] / $sub_giat['rak_triwulan_3']) * 100) : 0;
							$capaian_anggaran_tw4[$k_sub] 	 = !empty($sub_giat['rak_triwulan_4']) ? $this->pembulatan(($sub_giat['triwulan_4'] / $sub_giat['rak_triwulan_4']) * 100) : 0;
							$total_tw[$k_sub] = 0;
							$capaian_realisasi_indikator[$k_sub] = 0;
							$class_rumus_target[$k_sub] = "positif";
							$pendorong_html[$k_sub] 	= "";
							$penghambat_html[$k_sub] 	= "";
							if (
								!empty($realisasi_indikator)
								&& !empty($realisasi_indikator[$v_sub['idoutputbl']])
							) {
								$rumus_indikator = $realisasi_indikator[$v_sub['idoutputbl']]['id_rumus_indikator'];
								$max = 0;

								$pendorong_list = [];
								$penghambat_list = [];
								for ($i = 1; $i <= 12; $i++) {
									$realisasi_bulan = $realisasi_indikator[$v_sub['idoutputbl']]['realisasi_bulan_' . $i];
									if ($max < $realisasi_bulan) {
										$max = $realisasi_bulan;
									}
									$total_tw[$k_sub] += $realisasi_bulan;

									$pendorong_text = trim($realisasi_indikator[$k_sub]['pendorong_bulan_' . $i] ?? '');
									$penghambat_text = trim($realisasi_indikator[$k_sub]['keterangan_bulan_' . $i] ?? '');

									// die(var_dump($realisasi_indikator[$k_sub]));
									if ($pendorong_text !== '') {
										$pendorong_list[] = "<li>{$pendorong_text}</li>";
									}
									if ($penghambat_text !== '') {
										$penghambat_list[] = "<li>{$penghambat_text}</li>";
									}
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

								if (!empty($pendorong_list)) {
									$pendorong_html[$k_sub] = "<ul>" . implode("", $pendorong_list) . "</ul>";
								} else {
									$pendorong_html[$k_sub] = "-";
								}

								if (!empty($penghambat_list)) {
									$penghambat_html[$k_sub] = "<ul>" . implode("", $penghambat_list) . "</ul>";
								} else {
									$penghambat_html[$k_sub] = "-";
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

							$capaian_anggaran_tw1[$k_sub] = '<span class="capaian_anggaran_tw1-' . $v_sub['idoutputbl'] . '">' . $capaian_anggaran_tw1[$k_sub] . '</span>';
							$capaian_anggaran_tw2[$k_sub] = '<span class="capaian_anggaran_tw2-' . $v_sub['idoutputbl'] . '">' . $capaian_anggaran_tw1[$k_sub] . '</span>';
							$capaian_anggaran_tw3[$k_sub] = '<span class="capaian_anggaran_tw3-' . $v_sub['idoutputbl'] . '">' . $capaian_anggaran_tw1[$k_sub] . '</span>';
							$capaian_anggaran_tw4[$k_sub] = '<span class="capaian_anggaran_tw4-' . $v_sub['idoutputbl'] . '">' . $capaian_anggaran_tw1[$k_sub] . '</span>';
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

					$capaian_anggaran_tw1 = implode('<br>', $capaian_anggaran_tw1);
					$capaian_anggaran_tw2 = implode('<br>', $capaian_anggaran_tw2);
					$capaian_anggaran_tw3 = implode('<br>', $capaian_anggaran_tw3);
					$capaian_anggaran_tw4 = implode('<br>', $capaian_anggaran_tw4);
					$total_tw = implode('<br>', $total_tw);
					$capaian_realisasi_indikator = implode('<br>', $capaian_realisasi_indikator);

					$nama_sub = $sub_giat['nama'];

					$pendorong_html = implode('<br>', $pendorong_html);
					$penghambat_html = implode('<br>', $penghambat_html);

					$body_monev .= '
						<tr class="tr-sub-kegiatan sub_kegiatan" data-kode="' . $kd_urusan . '.' . $kd_bidang . '.' . $kd_program . '.' . $kd_giat . '.' . $kd_sub_giat . '">
							<td class="kiri kanan bawah">' . $no_program . '.' . $no_kegiatan . '.' . $no_sub_kegiatan . '</td>
							<td class="kanan bawah">' . $kd_sub_giat1 . '</td>
							<td class="kanan bawah nama">' . $nama_sub . '</td>
							<td class="kanan bawah indikator">' . $output_sub_giat . '</td>
							<td class="text_tengah kanan bawah total_renja target_indikator">' . $target_output_sub_giat . '</td>
							<td class="text_tengah kanan bawah total_renja satuan_indikator">' . $satuan_output_sub_giat . '</td>
							<td class="text_tengah kanan bawah triwulan_1">' . $realisasi_indikator_tw1 . '</td>
							<td class="text_tengah kanan bawah triwulan_1">' . $capaian_anggaran_tw1 . '</td>
							<td class="text_tengah kanan bawah triwulan_2">' . $realisasi_indikator_tw2 . '</td>
							<td class="text_tengah kanan bawah triwulan_2">' . $capaian_anggaran_tw2 . '</td>
							<td class="text_tengah kanan bawah triwulan_3">' . $realisasi_indikator_tw3 . '</td>
							<td class="text_tengah kanan bawah triwulan_3">' . $capaian_anggaran_tw3 . '</td>
							<td class="text_tengah kanan bawah triwulan_4">' . $realisasi_indikator_tw4 . '</td>
							<td class="text_tengah kanan bawah triwulan_4">' . $capaian_anggaran_tw4 . '</td>
							<td class="text_tengah kanan bawah capaian_renja">' . $total_tw . '</td>
							<td class="text_tengah kanan bawah capaian_renja">' . $capaian_realisasi_indikator . '</td>
							<td class="text_kanan kanan bawah capaian_renja">' . $capaian . '</td>
							<td class="kanan bawah" data-kode-progkeg="' . $kd_bidang . '.' . $kd_program . '.' . $kd_giat . '.' . $kd_sub_giat . '"></td>
							<td class="kanan bawah text_blok">' . $pendorong_html . '</td>
							<td class="kanan bawah text_blok">' . $penghambat_html . '</td>
						</tr>
						';
					$rowspan++;
				}
			}
		}
	}
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
$monev_triwulan_all = array(
	'1' => array('update_skpd_at' => '', 'keterangan_skpd' => '', 'catatan_verifikator' => '', 'update_verifikator_at' => ''),
	'2' => array('update_skpd_at' => '', 'keterangan_skpd' => '', 'catatan_verifikator' => '', 'update_verifikator_at' => ''),
	'3' => array('update_skpd_at' => '', 'keterangan_skpd' => '', 'catatan_verifikator' => '', 'update_verifikator_at' => ''),
	'4' => array('update_skpd_at' => '', 'keterangan_skpd' => '', 'catatan_verifikator' => '', 'update_verifikator_at' => '')
);
foreach ($monev_triwulan as $k => $v) {
	$monev_triwulan_all[$v['triwulan']]['keterangan_skpd'] = $v['keterangan_skpd'];
	$monev_triwulan_all[$v['triwulan']]['catatan_verifikator'] = $v['catatan_verifikator'];
	$monev_triwulan_all[$v['triwulan']]['update_verifikator_at'] = $v['update_verifikator_at'];
	$monev_triwulan_all[$v['triwulan']]['update_skpd_at'] = $v['update_skpd_at'];
}

$catatan_column = "
		<td class='kanan bawah text_blok bg-light atas' rowspan='$rowspan'>
    <div style='display: flex; flex-direction: column; gap: 6px;'>
        <div style='background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 4px 6px;'>
            <strong>TW 1:</strong><br>{$monev_triwulan_all['1']['catatan_verifikator']}
        </div>
        <div style='background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 4px 6px;'>
            <strong>TW 2:</strong><br>{$monev_triwulan_all['2']['catatan_verifikator']}
        </div>
        <div style='background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 4px 6px;'>
            <strong>TW 3:</strong><br>{$monev_triwulan_all['3']['catatan_verifikator']}
        </div>
        <div style='background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 4px 6px;'>
            <strong>TW 4:</strong><br>{$monev_triwulan_all['4']['catatan_verifikator']}
        </div>
    </div>
</td>

";
$first_row = $first_row . $catatan_column;
$body_monev = $first_row . $body_monev;

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
$nama_page = 'RFK ' . $unit['nama_skpd'] . ' ' . $unit['kode_skpd'] . ' | ' . $input['tahun_anggaran'];
$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
$link = $this->get_link_post($custom_post);
$url_skpd = '<a href="' . $link . '&pagu_dpa=sipd" target="_blank">' . $unit['kode_skpd'] . ' ' . $unit['nama_skpd'] . '</a> ';

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
<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
<input type="hidden" value="<?php echo $unit['id_skpd']; ?>" id="id_skpd">
<h1 class="text-center">Detail Perjanjian Kinerja<br><?php echo $unit['nama_skpd']; ?><br> Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
<div id='aksi-wp-sipd'></div>
<div id="cetak" title="Laporan Perjanjian Kinerja" style="padding: 5px; overflow: auto; max-height: 80vh;">
	<table id="tabel-monev-renja" cellpadding="2" cellspacing="0" contenteditable="false">
		<thead>
			<tr>
				<th rowspan="4" style="width: 60px;" class='atas kiri kanan bawah text_tengah text_blok'>No</th>
				<th rowspan="3" style="width: 100px;" class='atas kanan bawah text_tengah text_blok'>Kode</th>
				<th rowspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Program, Kegiatan, Sub Kegiatan</th>
				<th rowspan="3" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Indikator Kinerja Tujuan, Sasaran, Program(outcome) dan Kegiatan (output), Sub Kegiatan</th>
				<th rowspan="2" colspan="2" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Target kinerja dan anggaran Renja SKPD Tahun Berjalan Tahun <?php echo $input['tahun_anggaran']; ?> yang dievaluasi</th>
				<th colspan="8" style="width: 1200px;" class='atas kanan bawah text_tengah text_blok'>Realisasi Target dan Capaian Anggaran Pada Triwulan</th>
				<th rowspan="2" colspan="3" style="width: 300px;" class='atas kanan bawah text_tengah text_blok'>Capaian Realisasi Target dan Anggaran Tahun <?php echo $input['tahun_anggaran']; ?></th>
				<th rowspan="3" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Penanggung Jawab</th>
				<th rowspan="3" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Faktor Pendukung</th>
				<th rowspan="3" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Faktor Penghambat</th>
				<th rowspan="3" style="width: 200px;" class='atas kanan bawah text_tengah text_blok'>Catatan Verifikator / Rekomendasi</th>
			</tr>
			<tr>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>I</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>II</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>III</th>
				<th colspan="2" class='atas kanan bawah text_tengah text_blok'>IV</th>
			</tr>

			<tr>
				<th class='atas kanan bawah text_tengah text_blok'>Target</th>
				<th class='atas kanan bawah text_tengah text_blok'>Satuan</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi Target</th>
				<th class='atas kanan bawah text_tengah text_blok'>Capaian Anggaran (%)</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi Target</th>
				<th class='atas kanan bawah text_tengah text_blok'>Capaian Anggaran (%)</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi Target</th>
				<th class='atas kanan bawah text_tengah text_blok'>Capaian Anggaran (%)</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi Target</th>
				<th class='atas kanan bawah text_tengah text_blok'>Capaian Anggaran (%)</th>
				<th class='atas kanan bawah text_tengah text_blok'>Realisasi Target</th>
				<th class='atas kanan bawah text_tengah text_blok'>Capaian Target (%)</th>
				<th class='atas kanan bawah text_tengah text_blok'>Capaian Anggaran (%)</th>
			</tr>
			<tr>
				<th class='atas kanan bawah text_tengah text_blok'>0</th>
				<th class='atas kanan bawah text_tengah text_blok'>1</th>
				<th class='atas kanan bawah text_tengah text_blok'>2</th>
				<th class='atas kanan bawah text_tengah text_blok'>3</th>
				<th class='atas kanan bawah text_tengah text_blok'>4</th>
				<th class='atas kanan bawah text_tengah text_blok'>5</th>
				<th class='atas kanan bawah text_tengah text_blok'>6</th>
				<th class='atas kanan bawah text_tengah text_blok'>7</th>
				<th class='atas kanan bawah text_tengah text_blok'>8</th>
				<th class='atas kanan bawah text_tengah text_blok'>9</th>
				<th class='atas kanan bawah text_tengah text_blok'>10</th>
				<th class='atas kanan bawah text_tengah text_blok'>11</th>
				<th class='atas kanan bawah text_tengah text_blok'>12</th>
				<th class='atas kanan bawah text_tengah text_blok'>13 = 5+7+9+11</th>
				<th class='atas kanan bawah text_tengah text_blok'>14 = (13/3)*100</th>
				<th class='atas kanan bawah text_tengah text_blok'>15 = </th>
				<th class='atas kanan bawah text_tengah text_blok'>16</th>
				<th class='atas kanan bawah text_tengah text_blok'>17</th>
				<th class='atas kanan bawah text_tengah text_blok'>18</th>
				<th class='atas kanan bawah text_tengah text_blok'>19</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body_monev; ?>
		</tbody>
	</table>
</div>
<style type="text/css">
	#monev-body-renstra {
		word-break: break-word;
	}
</style>