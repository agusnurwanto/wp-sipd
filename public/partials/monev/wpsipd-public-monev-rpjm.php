<?php
// If this file is called directly, abort.
if (! defined('WPINC')) {
	die;
}
$input = shortcode_atts(array(
	'id_jadwal_lokal' => ''
), $atts);

if (empty($input['id_jadwal_lokal'])) {
	die('<h1 class="text-center">Id Jadwal Kosong, Hubungi ADMIN</h1>');
}

$data_jadwal = $this->get_data_jadwal_by_id_jadwal_lokal($input['id_jadwal_lokal']);
if (empty($data_jadwal)) {
	die('<h1 class="text-center">Jadwal tidak valid.</h1>');
}

if ($data_jadwal->jenis_jadwal != 'rpjmd') {
	die('<h1 class="text-center">Jenis Jadwal tidak valid.</h1>');
}

$jadwal_renstra_koneksi = $this->get_jadwal_koneksi_relasi_perencanaan($data_jadwal->id_jadwal_lokal);
$status_koneksi_renstra = false;
if (!empty($jadwal_renstra_koneksi)) {
	$status_koneksi_renstra = true;
}

$jadwal_lokal_for_copy_data = $this->validasi_jadwal_perencanaan('rpjm');
$status_copy_data = false;
if (!empty($jadwal_lokal_for_copy_data)) {
	$status_copy_data = true;
}

global $wpdb;

function button_edit_monev($class = false)
{
	$ret = ' <span style="display: none;" data-id="' . $class . '" class="edit-monev"><i class="dashicons dashicons-edit"></i></span>';
	return $ret;
}

function get_target($target, $satuan)
{
	if (empty($satuan)) {
		return $target;
	} else {
		$target = explode($satuan, $target);
		return $target[0];
	}
}

function parsing_nama_kode($nama_kode)
{
	$nama_kodes = explode('||', $nama_kode);
	$nama = $nama_kodes[0];
	unset($nama_kodes[0]);
	return $nama . '<span class="debug-kode">||' . implode('||', $nama_kodes) . '</span>';
}

$api_key = get_option('_crb_api_key_extension');

$rumus_indikator_db = $wpdb->get_results(
	$wpdb->prepare("
		SELECT * 
		FROM data_rumus_indikator 
		WHERE active=1 
		  AND tahun_anggaran=%d
	", $data_jadwal->tahun_anggaran),
	ARRAY_A
);
$rumus_indikator = '';
foreach ($rumus_indikator_db as $k => $v) {
	$rumus_indikator .= '<option value="' . $v['id'] . '">' . $v['rumus'] . '</option>';
}

$units = $this->get_all_skpd_by_tahun_anggaran($data_jadwal->tahun_anggaran);
if (empty($units)) {
	die('<h1 class="text-center">Unit tidak ditemukan.</h1>');
}

$all_skpd = [];
foreach ($units as $v) {
	$all_skpd[$v['id_skpd']] = $v;
}

if (!empty($input['id_skpd'])) {
	$unit = $units[$input['id_skpd']];
}

$judul_skpd = '';
if (!empty($input['id_skpd'])) {
	$judul_skpd = $unit['kode_skpd'] . '&nbsp;' . $unit['nama_skpd'] . '<br>';
}
$pengaturan = $wpdb->get_results($wpdb->prepare("
	select 
		* 
	from data_pengaturan_sipd 
	where tahun_anggaran=%d
", $data_jadwal->tahun_anggaran), ARRAY_A);

$awal_rpjmd = $data_jadwal->tahun_anggaran;
$akhir_rpjmd = $data_jadwal->tahun_akhir_anggaran;
if (!empty($pengaturan)) {
	$awal_rpjmd = $pengaturan[0]['awal_rpjmd'];
	$akhir_rpjmd = $pengaturan[0]['akhir_rpjmd'];
}
$urut = $data_jadwal->tahun_anggaran - $awal_rpjmd;
$nama_pemda = get_option('_crb_daerah');

$current_user = wp_get_current_user();
$bulan = date('m');
$body_monev = '';

$data_all = array(
	'data' => array()
);
$bulan = date('m');

$visi_ids = array();
$misi_ids = array();
$tujuan_ids = array();
$sasaran_ids = array();
$program_ids = array();
$skpd_filter = array();
$all_program_rpjm = array();

$sql = $wpdb->prepare("
	select 
		* 
	from data_rpjmd_visi
	where id_jadwal=%d
		and active=1
", $input['id_jadwal_lokal']);
$visi_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($visi_all as $visi) {
	if (empty($data_all['data'][$visi['id']])) {
		$data_all['data'][$visi['id']] = array(
			'nama' => $visi['visi_teks'],
			'data' => array()
		);
	}

	$visi_ids[$visi['id']] = "'" . $visi['id'] . "'";
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_misi
		where id_jadwal=%d
			and id_visi=%d
			and active=1
	", $input['id_jadwal_lokal'], $visi['id']);
	$misi_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($misi_all as $misi) {
		if (empty($data_all['data'][$visi['id']]['data'][$misi['id']])) {
			$data_all['data'][$visi['id']]['data'][$misi['id']] = array(
				'nama' => $misi['misi_teks'],
				'data' => array()
			);
		}

		$misi_ids[$misi['id']] = "'" . $misi['id'] . "'";
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjmd_tujuan
			where id_jadwal=%d
				and id_misi=%d
				and active=1
		", $input['id_jadwal_lokal'], $misi['id']);
		$tujuan_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($tujuan_all as $tujuan) {
			if (empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']])) {
				$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']] = array(
					'nama' => $tujuan['tujuan_teks'],
					'data' => array()
				);
			}

			$tujuan_ids[$tujuan['id_unik']] = "'" . $tujuan['id_unik'] . "'";
			$sql = $wpdb->prepare("
				select 
					* 
				from data_rpjmd_sasaran
				where id_jadwal=%d
					and kode_tujuan=%s
					and active=1
			", $input['id_jadwal_lokal'], $tujuan['id_unik']);
			$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
			foreach ($sasaran_all as $sasaran) {
				if (empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])) {
					$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
						'nama' => $sasaran['sasaran_teks'],
						'data' => array()
					);
				}

				$sasaran_ids[$sasaran['id_unik']] = "'" . $sasaran['id_unik'] . "'";
				$sql = $wpdb->prepare("
					select 
						* 
					from data_rpjmd_program
					where id_jadwal=%d
						and kode_sasaran=%s
						and id_unik_indikator IS NULL
						and active=1
				", $input['id_jadwal_lokal'], $sasaran['id_unik']);
				$program_all = $wpdb->get_results($sql, ARRAY_A);
				foreach ($program_all as $program) {
					$program_ids[$program['id_unik']] = "'" . $program['id_unik'] . "'";
					
					if (empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])) {
						$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
							'nama' => $program['nama_program'],
							'kode_skpd' => '-',
							'nama_skpd' => '-',
							'id_unit' 	=> '-',
							'data' 		=> array()
						);
					}

					$sql = $wpdb->prepare("
						select 
							* 
						from data_rpjmd_program
						where id_jadwal=%d
							and id_unik=%s
							and id_unik_indikator IS NOT NULL
							and active=1
					", $input['id_jadwal_lokal'], $program['id_unik']);
					$indikator_program_all = $wpdb->get_results($sql, ARRAY_A);
					foreach ($indikator_program_all as $indikator_program) {
						$parent_program =& $data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']];
						
						if ($parent_program['kode_skpd'] === '-') {
							if (!empty($indikator_program['id_unit']) && isset($all_skpd[$indikator_program['id_unit']])) {
								$parent_program['id_unit'] = $all_skpd[$indikator_program['id_unit']]['id_skpd'] ?? '-';
								$parent_program['kode_skpd'] = $all_skpd[$indikator_program['id_unit']]['kode_skpd'] ?? '00';
								$parent_program['nama_skpd'] = $all_skpd[$indikator_program['id_unit']]['nama_skpd'] ?? 'SKPD Kosong';
							}
						}

						$skpd_filter[$parent_program['kode_skpd']] = $parent_program['nama_skpd'];

						if (empty($data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$indikator_program['id_unik_indikator']])) {
							$data_all['data'][$visi['id']]['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$indikator_program['id_unik_indikator']] = array(
								'nama' => $indikator_program['indikator'],
								'data' => $indikator_program
							);
						}
					}
				}
			}
		}
	}
}

// buat array data kosong
if (empty($data_all['data']['visi_kosong'])) {
	$data_all['data']['visi_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'data' => array()
	);
}
if (empty($data_all['data']['visi_kosong']['data']['misi_kosong'])) {
	$data_all['data']['visi_kosong']['data']['misi_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'data' => array()
	);
}
if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong'])) {
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'data' => array()
	);
}
if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong'])) {
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'data' => array()
	);
}

// select misi yang belum terselect
if (!empty($misi_ids)) {
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_misi
		where id_jadwal=%d
			and active=1
			and id_misi not in (" . implode(',', $misi_ids) . ")
	", $input['id_jadwal_lokal']);
	$misi_all_kosong = $wpdb->get_results($sql, ARRAY_A);
	foreach ($misi_all_kosong as $misi) {
		if (empty($data_all['data']['visi_kosong']['data'][$misi['id']])) {
			$data_all['data']['visi_kosong']['data'][$misi['id']]['data'] = array(
				'nama' => $misi['misi_teks'],
				'data' => array()
			);
		}
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjmd_tujuan
			where id_jadwal=%d
				and id_misi=%s
				and active=1
		", $input['id_jadwal_lokal'], $misi['id']);
		$tujuan_all_kosong = $wpdb->get_results($sql, ARRAY_A);
		foreach ($tujuan_all_kosong as $tujuan) {
			$tujuan_ids[$tujuan['id_unik']] = "'" . $tujuan['id_unik'] . "'";
			if (empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']])) {
				$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']] = array(
					'nama' => $tujuan['sasaran_teks'],
					'data' => array()
				);
			}
			$sql = $wpdb->prepare("
				select 
					* 
				from data_rpjmd_sasaran
				where id_jadwal=%d
					and kode_tujuan=%s
					and active=1
			", $input['id_jadwal_lokal'], $tujuan['id_unik']);
			$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
			foreach ($sasaran_all as $sasaran) {
				$sasaran_ids[$sasaran['id_unik']] = "'" . $sasaran['id_unik'] . "'";
				if (empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])) {
					$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
						'nama' => $sasaran['sasaran_teks'],
						'data' => array()
					);
				}
				$sql = $wpdb->prepare("
					select 
						* 
					from data_rpjmd_program
					where id_jadwal=%d
						and kode_sasaran=%s
						and active=1
				", $input['id_jadwal_lokal'], $sasaran['id_unik']);
				$program_all = $wpdb->get_results($sql, ARRAY_A);
				foreach ($program_all as $program) {
					$program_ids[$program['id_unik']] = "'" . $program['id_unik'] . "'";
					if (empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])) {
						$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
							'nama' => $program['nama_program'],
							'kode_skpd' => $program['kode_skpd'],
							'nama_skpd' => $program['nama_skpd'],
							'data' => array()
						);
					}
					if (empty($data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])) {
						$data_all['data']['visi_kosong']['data'][$misi['id']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
							'nama' => $program['indikator'],
							'data' => array()
						);
					}
				}
			}
		}
	}
}

// select tujuan yang belum terselect
if (!empty($tujuan_ids)) {
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_tujuan
		where id_jadwal=%d
			and active=1
			and id_unik not in (" . implode(',', $tujuan_ids) . ")
	", $input['id_jadwal_lokal']);
	$tujuan_all_kosong = $wpdb->get_results($sql, ARRAY_A);
	foreach ($tujuan_all_kosong as $tujuan) {
		if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']])) {
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']] = array(
				'nama' => $tujuan['tujuan_teks'],
				'data' => array()
			);
		}
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjmd_sasaran
			where id_jadwal=%d
				and kode_tujuan=%s
				and active=1
		", $input['id_jadwal_lokal'], $tujuan['id_unik']);
		$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($sasaran_all as $sasaran) {
			$sasaran_ids[$sasaran['id_unik']] = "'" . $sasaran['id_unik'] . "'";
			if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])) {
				$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
					'nama' => $sasaran['sasaran_teks'],
					'data' => array()
				);
			}
			$sql = $wpdb->prepare("
				select 
					* 
				from data_rpjmd_program
				where id_jadwal=%d
					and kode_sasaran=%s
					and active=1
			", $input['id_jadwal_lokal'], $sasaran['id_unik']);
			$program_all = $wpdb->get_results($sql, ARRAY_A);
			foreach ($program_all as $program) {
				$program_ids[$program['id_unik']] = "'" . $program['id_unik'] . "'";
				if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])) {
					$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
						'nama' => $program['nama_program'],
						'kode_skpd' => $program['kode_skpd'],
						'nama_skpd' => $program['nama_skpd'],
						'data' => array()
					);
				}
				if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])) {
					$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
						'nama' => $program['indikator'],
						'data' => array()
					);
				}
			}
		}
	}
}

// select sasaran yang belum terselect
if (!empty($sasaran_ids)) {
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_sasaran
		where id_jadwal=%d
			and active=1
			and id_unik not in (" . implode(',', $sasaran_ids) . ")
	", $input['id_jadwal_lokal']);
	$sasaran_all_kosong = $wpdb->get_results($sql, ARRAY_A);
	foreach ($sasaran_all_kosong as $sasaran) {
		if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']])) {
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']] = array(
				'nama' => $sasaran['sasaran_teks'],
				'data' => array()
			);
		}
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjmd_program
			where id_jadwal=%d
				and kode_sasaran=%s
				and active=1
		", $input['id_jadwal_lokal'], $sasaran['id_unik']);
		$program_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($program_all as $program) {
			$program_ids[$program['id_unik']] = "'" . $program['id_unik'] . "'";
			if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']])) {
				$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
					'nama' => $program['nama_program'],
					'kode_skpd' => $program['kode_skpd'],
					'nama_skpd' => $program['nama_skpd'],
					'data' => array()
				);
			}
			if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])) {
				$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
					'nama' => $program['indikator'],
					'data' => array()
				);
			}
		}
	}
}

if (!empty($program_ids)) {
	// select program yang belum terselect
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_program
		where id_jadwal=%d
			and id_unik not in (" . implode(',', $program_ids) . ")
			and active=1
	", $input['id_jadwal_lokal']);
	$program_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($program_all as $program) {
		if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']])) {
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']] = array(
				'nama' => $program['nama_program'],
				'kode_skpd' => $program['kode_skpd'],
				'nama_skpd' => $program['nama_skpd'],
				'data' => array()
			);
		}
		if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])) {
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
				'nama' => $program['indikator'],
				'data' => array()
			);
		}
	}
}
// hapus array jika data dengan key kosong tidak ada datanya
if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'])) {
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']);
}
if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'])) {
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']);
}
if (empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'])) {
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']);
}
if (empty($data_all['data']['visi_kosong']['data'])) {
	unset($data_all['data']['visi_kosong']);
}


$body = '';
$no_visi = 0;
foreach ($data_all['data'] as $visi) {
	$no_visi++;
	$body .= '
		<tr class="tr-visi">
			<td class="kiri atas kanan bawah">' . $no_visi . '</td>
			<td class="atas kanan bawah">' . $visi['nama'] . '</td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
		</tr>
	';
	$no_misi = 0;
	foreach ($visi['data'] as $misi) {
		$no_misi++;
		$body .= '
			<tr class="tr-misi">
				<td class="kiri atas kanan bawah">' . $no_visi . '.' . $no_misi . '</td>
				<td class="atas kanan bawah"><span class="debug-visi">' . $visi['nama'] . '</span></td>
				<td class="atas kanan bawah">' . $misi['nama'] . '</td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah"></td>
			</tr>
		';
		$no_tujuan = 0;
		foreach ($misi['data'] as $tujuan) {
			$no_tujuan++;
			$body .= '
				<tr class="tr-tujuan">
					<td class="kiri atas kanan bawah">' . $no_visi . '.' . $no_misi . '.' . $no_tujuan . '</td>
					<td class="atas kanan bawah"><span class="debug-visi">' . $visi['nama'] . '</span></td>
					<td class="atas kanan bawah"><span class="debug-misi">' . $misi['nama'] . '</span></td>
					<td class="atas kanan bawah">' . parsing_nama_kode($tujuan['nama']) . '</td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
					<td class="atas kanan bawah"></td>
				</tr>
			';
			$no_sasaran = 0;
			foreach ($tujuan['data'] as $id_unik => $sasaran) {
				$no_sasaran++;
				$body .= '
					<tr class="tr-sasaran">
						<td class="kiri atas kanan bawah">' . $no_visi . '.' . $no_misi . '.' . $no_tujuan . '.' . $no_sasaran . '</td>
						<td class="atas kanan bawah"><span class="debug-visi">' . $visi['nama'] . '</span></td>
						<td class="atas kanan bawah"><span class="debug-misi">' . $misi['nama'] . '</span></td>
						<td class="atas kanan bawah"><span class="debug-tujuan">' . $tujuan['nama'] . '</span></td>
						<td class="atas kanan bawah">' . parsing_nama_kode($sasaran['nama']) . '</td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
						<td class="atas kanan bawah"></td>
					</tr>
				';
				$no_program = 0;
				foreach ($sasaran['data'] as $program) {
					$program_parts = explode(' ', $program['nama']);
					$kode_program = $program_parts[0];
					// for validate
					if (empty($all_program_rpjm[$kode_program])) {
						$all_program_rpjm[$kode_program] = $program;
					}
					
					$no_program++;
					$text_indikator = array();
					$target_awal = array();
					$target_1 = array();
					$target_2 = array();
					$target_3 = array();
					$target_4 = array();
					$target_5 = array();
					$target_akhir = array();
					$satuan = array();

					$all_program_rpjm[$kode_program]['indikator'] = [];
					foreach ($program['data'] as $indikator_program) {
						$all_program_rpjm[$kode_program]['indikator'][$indikator_program['nama']] = $indikator_program['data'];

						$text_indikator[] = '<div class="indikator_program">' . $indikator_program['nama'] . '</div>';
						$target_awal[] = '<div class="indikator_program">' . get_target($indikator_program['data']['target_awal'], $indikator_program['data']['satuan']) . '</div>';
						$target_1[] = '<div class="indikator_program">' . get_target($indikator_program['data']['target_1'], $indikator_program['data']['satuan']) . '</div>';
						$target_2[] = '<div class="indikator_program">' . get_target($indikator_program['data']['target_2'], $indikator_program['data']['satuan']) . '</div>';
						$target_3[] = '<div class="indikator_program">' . get_target($indikator_program['data']['target_3'], $indikator_program['data']['satuan']) . '</div>';
						$target_4[] = '<div class="indikator_program">' . get_target($indikator_program['data']['target_4'], $indikator_program['data']['satuan']) . '</div>';
						$target_5[] = '<div class="indikator_program">' . get_target($indikator_program['data']['target_5'], $indikator_program['data']['satuan']) . '</div>';
						$target_akhir[] = '<div class="indikator_program">' . get_target($indikator_program['data']['target_akhir'], $indikator_program['data']['satuan']) . '</div>';
						$satuan[] = '<div class="indikator_program">' . $indikator_program['data']['satuan'] . '</div>';
					}
					$text_indikator = implode('', $text_indikator);
					$target_awal = implode('', $target_awal);
					$target_1 = implode('', $target_1);
					$target_2 = implode('', $target_2);
					$target_3 = implode('', $target_3);
					$target_4 = implode('', $target_4);
					$target_5 = implode('', $target_5);
					$target_akhir = implode('', $target_akhir);
					$satuan = implode('', $satuan);
					$body .= '
						<tr class="tr-program" data-kode-skpd="' . $program['kode_skpd'] . '">
							<td class="kiri atas kanan bawah">' . $no_visi . '.' . $no_misi . '.' . $no_tujuan . '.' . $no_sasaran . '.' . $no_program . '</td>
							<td class="atas kanan bawah"><span class="debug-visi">' . $visi['nama'] . '</span></td>
							<td class="atas kanan bawah"><span class="debug-misi">' . $misi['nama'] . '</span></td>
							<td class="atas kanan bawah"><span class="debug-tujuan">' . $tujuan['nama'] . '</span></td>
							<td class="atas kanan bawah"><span class="debug-sasaran">' . $sasaran['nama'] . '</span></td>
							<td class="atas kanan bawah">' . parsing_nama_kode($program['nama']) . '</td>
							<td class="atas kanan bawah">' . $text_indikator . '</td>
							<td class="atas kanan bawah text_tengah">' . $target_awal . '</td>
							<td class="atas kanan bawah text_tengah">' . $target_1 . '</td>
							<td class="atas kanan bawah text_tengah">' . $target_2 . '</td>
							<td class="atas kanan bawah text_tengah">' . $target_3 . '</td>
							<td class="atas kanan bawah text_tengah">' . $target_4 . '</td>
							<td class="atas kanan bawah text_tengah">' . $target_5 . '</td>
							<td class="atas kanan bawah text_tengah">' . $target_akhir . '</td>
							<td class="atas kanan bawah text_tengah">' . $satuan . '</td>
							<td class="atas kanan bawah"><a href="javascript:void(0)" onclick="show_renstra(\'' . $program['id_unit'] . '\',\'' . $id_unik . '\', \'' . $program['kode_skpd'] . ' ' . $program['nama_skpd'] . '\')">' . $program['kode_skpd'] . ' ' . $program['nama_skpd'] . '</a></td>
						</tr>
					';
				}
			}
		}
	}
}

ksort($skpd_filter);
$skpd_filter_html = '<option value="">Pilih SKPD</option>';
foreach ($skpd_filter as $kode_skpd => $nama_skpd) {
	$skpd_filter_html .= '<option value="' . $kode_skpd . '">' . $kode_skpd . ' ' . $nama_skpd . '</option>';
}
?>
<style type="text/css">
	.debug-visi,
	.debug-misi,
	.debug-tujuan,
	.debug-sasaran,
	.debug-kode {
		display: none;
	}

	.indikator_program {
		min-height: 40px;
	}
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitoring dan Evaluasi RPJMD (Rencana Pembangunan Jangka Menengah Daerah)<br><?php echo $judul_skpd . ' Tahun ' . $data_jadwal->tahun_anggaran . ' - ' . $data_jadwal->tahun_akhir_anggaran . ' ' . $nama_pemda; ?></h4>
<div id="cetak" title="Indikator RPJMD - <?php echo $judul_skpd . ' ' . $data_jadwal->nama; ?>" style="padding: 5px; overflow: auto; height: 80vh; margin-top: 10px;">
	<table cellpadding="2" cellspacing="0" style="font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<tr>
				<th style="width: 85px;" class="atas kiri kanan bawah text_tengah text_blok">No</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Visi</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Misi</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Tujuan</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Sasaran</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Program</th>
				<th style="width: 400px;" class="atas kanan bawah text_tengah text_blok">Indikator</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Awal</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Tahun 1</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Tahun 2</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Tahun 3</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Tahun 4</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Tahun 5</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Akhir</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Satuan</th>
				<th style="width: 150px;" class="atas kanan bawah text_tengah text_blok">Keterangan</th>
			</tr>
			<tr>
				<th class='atas kiri kanan bawah text_tengah text_blok'>0</th>
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
				<th class='atas kanan bawah text_tengah text_blok'>13</th>
				<th class='atas kanan bawah text_tengah text_blok'>14</th>
				<th class='atas kanan bawah text_tengah text_blok'>15</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
	</table>
</div>

<div class="modal fade" id="modal-monev" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">'
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bgpanel-theme">
				<h4 style="margin: 0;" class="modal-title" id="">MONEV RPJM</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
			</div>
			<div class="modal-body">

			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-rpjmd" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog" style="min-width:1200px" role="document">
		<div class="modal-content">
			<div class="modal-header bgpanel-theme">
				<h5 class="modal-title" id="exampleModalLabel" style="margin: 0 auto; text-align:center; font-weight: bold"></h5>
			</div>
			<div class="modal-body" id="detail-rpjmd-body">
    		</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery('document').ready(() => {
		run_download_excel();
		let data_all = <?php echo json_encode($data_all); ?>;
		window.status_jadwal_lokal = <?php echo $status_copy_data; ?>;
		window.all_program_rpjm = <?php echo json_encode($all_program_rpjm); ?>;

		let aksi = ``;
		if (status_jadwal_lokal) {
			aksi += `
				<button class="btn btn-warning" onclick="copyDataLokal()">
					Copy Data RPJMD Lokal
				</button>
			`;
		}

		aksi += `
			<h3 style="margin-top: 20px;">PENGATURAN</h3>
			<label style="margin-left: 20px;">
				<input type="checkbox" onclick="show_debug(this);"> Debug Cascading RPJM
			</label>
			<label style="margin-left: 20px;">
				Sembunyikan Baris 
				<select id="sembunyikan-baris" onchange="sembunyikan_baris(this);" style="padding: 5px 10px; min-width: 200px;">
					<option value="">Pilih Baris</option>
					<option value="tr-misi">Misi</option>
					<option value="tr-tujuan">Tujuan</option>
					<option value="tr-sasaran">Sasaran</option>
					<option value="tr-program">Program</option>
				</select>
			</label>
			<label style="margin-left: 20px;">
				Filter SKPD 
				<select onchange="filter_skpd(this);" style="padding: 5px 10px; min-width: 200px; max-width: 400px;">
					<?php echo $skpd_filter_html; ?>
				</select>
			</label>
		`;

		jQuery('#action-sipd').append(aksi);

		jQuery('.edit-monev').on('click', function() {
			jQuery('#wrap-loading').show();
			jQuery('#mod-monev').modal('show');
			jQuery('#wrap-loading').hide();
		});
	});

	function filter_skpd(that) {
		var tr_program = jQuery('.tr-program');
		var val = jQuery(that).val();
		if (val == '') {
			tr_program.show();
		} else {
			tr_program.hide();
			jQuery('.tr-program[data-kode-skpd="' + val + '"]').show();
		}
	}

	function copyDataLokal() {
		if (confirm('Apakah anda yakin untuk mengambil data dari lokal? data lama akan diupdate!')) {
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
				type: "post",
				data: {
					action: 'copy_data_monev_rpjmd_rpd_from_data_local',
					api_key: ajax.api_key,
					type: 'rpjm',
					id_jadwal: <?php echo $input['id_jadwal_lokal']; ?>
				},
				dataType: "json",
				success: function(res) {
					jQuery('#wrap-loading').hide();
					alert(res.message);
				}
			});
		}
	}

	function sembunyikan_baris(that) {
		var val = jQuery(that).val();
		var tr_misi = jQuery('.tr-misi');
		var tr_tujuan = jQuery('.tr-tujuan');
		var tr_sasaran = jQuery('.tr-sasaran');
		var tr_program = jQuery('.tr-program');
		tr_misi.show();
		tr_tujuan.show();
		tr_sasaran.show();
		tr_program.show();
		if (val == 'tr-misi') {
			tr_misi.hide();
			tr_tujuan.hide();
			tr_sasaran.hide();
			tr_program.hide();
		} else if (val == 'tr-tujuan') {
			tr_tujuan.hide();
			tr_sasaran.hide();
			tr_program.hide();
		} else if (val == 'tr-sasaran') {
			tr_sasaran.hide();
			tr_program.hide();
		} else if (val == 'tr-program') {
			tr_program.hide();
		}
	}

	function show_debug(that) {
		if (jQuery(that).is(':checked')) {
			jQuery('.debug-visi').show();
			jQuery('.debug-misi').show();
			jQuery('.debug-tujuan').show();
			jQuery('.debug-sasaran').show();
			jQuery('.debug-kode').show();
		} else {
			jQuery('.debug-visi').hide();
			jQuery('.debug-misi').hide();
			jQuery('.debug-tujuan').hide();
			jQuery('.debug-sasaran').hide();
			jQuery('.debug-kode').hide();
		}
	}

	function showEditBtn(that) {
		if (jQuery(that).is(':checked')) {
			jQuery('.edit-monev').show();
		} else {
			jQuery('.edit-monev').hide();
		}
	}

	async function show_renstra(idUnit, kodeSasaran, namaUnit) {
		jQuery('#wrap-loading').show();
		const dataRenstra = await get_data_renstra_by_kode_sasaran_koneksi(idUnit, kodeSasaran);
		
		const modal = jQuery("#modal-rpjmd");
		const modalBody = jQuery("#detail-rpjmd-body");
		modalBody.empty();

		if (dataRenstra.status && dataRenstra.data) {
			const data = dataRenstra.data;

			const lama_pelaksanaan = <?php echo $jadwal_renstra_koneksi->lama_pelaksanaan; ?>;
			const jadwal_tahun = '<?php echo $jadwal_renstra_koneksi->tahun_anggaran; ?>';

			const tahun_akhir_relasi = <?php echo $jadwal_renstra_koneksi->tahun_akhir_anggaran; ?>;
			const nama_pemda = '<?php echo $nama_pemda; ?>';

			const processParentChildArray = (arr, textKey) => {
				if (!arr || arr.length === 0) return { text: '', indicators: []};
				const parent = arr.find(item => !item.id_unik_indikator);
				const indicators = arr.filter(item => item.id_unik_indikator);
				return {
					text: parent ? parent[textKey] : (arr[0] ? arr[0][textKey] : ''),
					indicators: indicators || []
				};
			};

			const tujuanData = processParentChildArray(data.tujuan, 'tujuan_teks');
			const sasaranData = processParentChildArray(data.sasaran, 'sasaran_teks');
			
			const groupedPrograms = {};
			if (data.program && data.program.length > 0) {
				data.program.forEach(p => {
					if (p.id_unik && !p.id_unik_indikator) {
						if (!groupedPrograms[p.id_unik]) groupedPrograms[p.id_unik] = {	
							id_unik: p.id_unik,
							nama_program: p.nama_program,
							indicators: []
						};
					}
				});
				data.program.forEach(p => {
					if (p.id_unik && p.id_unik_indikator && groupedPrograms[p.id_unik]) {
						groupedPrograms[p.id_unik].indicators.push(p);
					}
				});
			}

			// tujuan sasaran
			const generateMetaCardHtml = (title, data) => {
				if (!data.text) return '';

				let html = `
					<div class="card mb-3">
						<div class="card-header"><strong>${title} RENSTRA</strong></div>
						<div class="card-body" style="padding:0;">
							<table class="table table-bordered mb-0">
								<tbody>
									<tr>
										<td colspan="2"><strong>${data.text}</strong></td>
									</tr>`;
				
				if (data.indicators.length > 0) {
					html += `   <tr>
									<td colspan="2" style="padding: 8px;">
										<table class="table table-sm table-hover" style="font-size: 12px; margin-bottom: 0;">
											<thead class="bg-dark text-light">
												<tr>
													<th class="text-center">Indikator</th>
													<th class="text-center">Satuan</th>`;
					for (let i = 1; i <= lama_pelaksanaan; i++) { 
						html += `					<th class="text-center">Target ${i}</th>`;
					}
					html += `               	</tr>
											</thead>
										<tbody>`;
					data.indicators.forEach(ind => {
						html += `           <tr>
												<td>${ind.indikator_teks || '-'}</td>
												<td class="text-center">${ind.satuan || '-'}</td>`;
						for (let i = 1; i <= lama_pelaksanaan; i++) {
							html += `           <td class="text-right">${ind['target_' + i] || '-'}</td>`;
						}
						html += `           </tr>`;
					});
					html += `           </tbody>
										</table>
									</td>
								</tr>`;
				} else {
					html += `<tr><td colspan="2"><small class="pl-2">Tidak ada indikator.</small></td></tr>`;
				}

				html += `       </tbody>
							</table>
						</div>
					</div>`;
				return html;
			};

			// program
			const generateProgramsCardHtml = (programs) => {
				if (Object.keys(programs).length === 0) return '<p class="text-center">Tidak ada data program untuk ditampilkan.</p>';
				let html = `
					<div class="card">
						<div class="card-header"></div>
						<div class="card-body" style="padding:0;">
							<div class="container-fluid">
								<table class="table table-bordered mb-2 mt-2">
									<thead class="bg-dark text-light">
										<tr>
											<th class="text-center" style="width:5%;">No</th>
											<th class="text-center">Program RENSTRA</th>
											<th class="text-center" style="width:20%;">Keterangan</th>
										</tr>
									</thead>
									<tbody>`;

				Object.values(programs).forEach((prog, progIndex) => {

					html += `   <tr>
									<td class="text-center">${progIndex + 1}</td>
									<td><strong>${prog.nama_program}</strong></td>
									<td class="prog-ket-${prog.id_unik}">-</td>
								</tr>`;

					if (prog.indicators.length > 0) {
						html += `<tr>
									<td colspan="3" style="padding: 8px;">
										<table class="table table-sm table-hover" style="font-size: 12px; margin-bottom: 0;">
											<thead class="bg-dark text-light">
												<tr>
													<th class="text-center" style="width:5%;">No</th>
													<th class="text-center" style="width:200px;">Indikator</th>
													<th class="text-center">Satuan</th>`;
						for (let i = 1; i <= lama_pelaksanaan; i++) { 
							html += `
													<th class="text-center">Target ${i}</th>
													<th class="text-center">Pagu ${i}</th>
							`;
						}
						html += `
													<th class="text-center" style="width:20%;">Keterangan</th>
												</tr>
											</thead>
											<tbody>`;
						prog.indicators.forEach((ind, indIndex) => {
							html += `       <tr>
												<td class="text-center">${progIndex + 1}.${indIndex + 1}</td>
												<td class="text-left">${ind.indikator}</td>
												<td class="text-center">${ind.satuan || '-'}</td>`;
							for (let i = 1; i <= lama_pelaksanaan; i++) {
								html += `       <td class="text-right">${ind['target_' + i] || '-'}</td>
												<td class="text-right">${ind['pagu_' + i] || '0.00'}</td>`;
							}
							html += `
												<td class="text-left errMsg-${ind.id_unik_indikator}">-</td>
											</tr>`;
						});
						html += `           </tbody>
										</table>
									</td>
								</tr>`;
					}
				});
				html += `      </tbody>
							</table>
						</div>
					</div>
				</div>`;

				return html;
			};

			const finalHtml =
				generateMetaCardHtml('Tujuan', tujuanData) +
				generateMetaCardHtml('Sasaran', sasaranData) +
				generateProgramsCardHtml(groupedPrograms);

			modalBody.html(finalHtml);

       		await validateRPJM(groupedPrograms);

			modal.find('.modal-title').html(`Data Keterkaitan RENSTRA <br> ${namaUnit} <br>Tahun ${jadwal_tahun} - ${tahun_akhir_relasi} <br> ${nama_pemda}`);
		} else {
			modalBody.html(`<div class="alert alert-danger text-center">${dataRenstra.message || 'Gagal memuat data.'}</div>`);
		}

		modal.modal('show');
		jQuery('#wrap-loading').hide();
	} 

	async function validateRPJM(groupedPrograms) {
		window.js = groupedPrograms;
		const all_program_rpjm = window.all_program_rpjm; 
		
		if (!all_program_rpjm) {
			console.error('Variabel global all_program_rpjm tidak ditemukan.');
			return;
		}

		const lama_pelaksanaan = <?php echo $jadwal_renstra_koneksi->lama_pelaksanaan; ?>;

		for (const [progIdUnik, progData] of Object.entries(groupedPrograms)) {
			let kodeProgram = progData.nama_program.split(' ')[0];

			const programErrorField = jQuery(`.prog-ket-${progIdUnik}`);
			const rpjmProgram = all_program_rpjm[kodeProgram];

			if (!rpjmProgram) {
				programErrorField.html('Program tidak ditemukan di RPJMD.').addClass('text-danger font-weight-bold');
				continue; 
			}

			if (progData.nama_program !== rpjmProgram.nama) {
				programErrorField.html(`Nama program tidak sesuai (RPJMD: ${rpjmProgram.nama})`).addClass('text-danger');
			} else {
				programErrorField.html('Sesuai RPJMD').addClass('text-success font-weight-bold');
			}

			progData.indicators.forEach(indikator => {
				let errors = [];
				const indicatorErrorField = jQuery(`.errMsg-${indikator.id_unik_indikator}`);
				
				const rpjmIndikator = rpjmProgram.indikator?.[indikator.indikator];

				if (!rpjmIndikator) {
					indicatorErrorField.html('Indikator tidak ditemukan di RPJMD.').addClass('text-danger font-weight-bold');
					return;
				}

				if (indikator.satuan !== rpjmIndikator.satuan) {
					errors.push(`Satuan tidak sesuai (RPJMD: ${rpjmIndikator.satuan})`);
				}

				for (let i = 1; i <= lama_pelaksanaan; i++) {
					const targetKey = 'target_' + i;
					const paguKey = 'pagu_' + i;

					const currentTarget = indikator[targetKey] || '';
					const rpjmTarget = rpjmIndikator[targetKey] || '';
					if (currentTarget.toString() !== rpjmTarget.toString()) {
						errors.push(`Target ${i} tidak sesuai (RPJMD: ${rpjmTarget})`);
					}
					
					const currentPagu = parseFloat(indikator[paguKey] || 0);
					const rpjmPagu = parseFloat(rpjmIndikator[paguKey] || 0);
					if (currentPagu !== rpjmPagu) {
						errors.push(`Pagu ${i} tidak sesuai (RPJMD: ${rpjmPagu})`);
					}
				}

				if (errors.length > 0) {
					indicatorErrorField.html(errors.join(',<br>')).addClass('text-danger');
				} else {
					indicatorErrorField.html('Sesuai RPJMD').addClass('text-success');
				}
			});
		}
	}

	function get_data_renstra_by_kode_sasaran_koneksi(idUnit, kodeSasaran) {
		return jQuery.ajax({
			url: ajax.url,
			type: "post",
			dataType: 'JSON',
			data: {
				action: "get_data_renstra_by_kode_sasaran_koneksi",
				api_key: ajax.api_key,
				id_unit: idUnit,
				kode_sasaran: kodeSasaran,
				id_jadwal: <?php echo $jadwal_renstra_koneksi->id_jadwal_lokal; ?>
			}
		});
	}
</script>