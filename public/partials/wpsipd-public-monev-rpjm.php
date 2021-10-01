<?php
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

$api_key = get_option('_crb_api_key_extension' );

function button_edit_monev($class=false){
	$ret = ' <span style="display: none;" data-id="'.$class.'" class="edit-monev"><i class="dashicons dashicons-edit"></i></span>';
	return $ret;
}

$rumus_indikator_db = $wpdb->get_results("SELECT * from data_rumus_indikator where active=1 and tahun_anggaran=".$input['tahun_anggaran'], ARRAY_A);
$rumus_indikator = '';
foreach ($rumus_indikator_db as $k => $v){
	$rumus_indikator .= '<option value="'.$v['id'].'">'.$v['rumus'].'</option>';
}

$where_skpd = '';
if(!empty($input['id_skpd'])){
	$where_skpd = "and id_skpd =".$input['id_skpd'];
}

$sql = $wpdb->prepare("
	select 
		* 
	from data_unit 
	where tahun_anggaran=%d
		".$where_skpd."
		and active=1
	order by id_skpd ASC
", $input['tahun_anggaran']);
$unit = $wpdb->get_results($sql, ARRAY_A);

$judul_skpd = '';
if(!empty($input['id_skpd'])){
	$judul_skpd = $unit[0]['kode_skpd'].'&nbsp;'.$unit[0]['nama_skpd'].'<br>';

}
$pengaturan = $wpdb->get_results($wpdb->prepare("
	select 
		* 
	from data_pengaturan_sipd 
	where tahun_anggaran=%d
", $input['tahun_anggaran']), ARRAY_A);

$awal_rpjmd = 2018;
$akhir_rpjmd = 2023;
if(!empty($pengaturan)){
	$awal_rpjmd = $pengaturan[0]['awal_rpjmd'];
	$akhir_rpjmd = $pengaturan[0]['akhir_rpjmd'];
}
$urut = $input['tahun_anggaran']-$awal_rpjmd;
$nama_pemda = $pengaturan[0]['daerah'];

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

$sql = $wpdb->prepare("
	select 
		* 
	from data_rpjmd_visi
	where tahun_anggaran=%d
		and active=1
", $input['tahun_anggaran']);
$visi_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($visi_all as $visi) {
	if(empty($data_all['data'][$visi['id_visi']])){
		$data_all['data'][$visi['id_visi']] = array(
			'nama' => $visi['visi_teks'],
			'data' => array()
		);
	}

	$visi_ids[$visi['id_visi']] = "'".$visi['id_visi']."'";
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_misi
		where tahun_anggaran=%d
			and id_visi=%s
			and active=1
	", $input['tahun_anggaran'], $visi['id_visi']);
	$misi_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($misi_all as $misi) {
		if(empty($data_all['data'][$visi['id_visi']]['data'][$misi['id_misi']])){
			$data_all['data'][$visi['id_visi']]['data'][$misi['id_misi']] = array(
				'nama' => $misi['misi_teks'],
				'data' => array()
			);
		}

		$misi_ids[$misi['id_misi']] = "'".$misi['id_misi']."'";
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjmd_tujuan
			where tahun_anggaran=%d
				and id_misi=%s
				and active=1
		", $input['tahun_anggaran'], $misi['id_misi']);
		$tujuan_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($tujuan_all as $tujuan) {
			if(empty($data_all['data'][$visi['id_visi']]['data'][$misi['id_misi']]['data'][$tujuan['id_unik']])){
				$data_all['data'][$visi['id_visi']]['data'][$misi['id_misi']]['data'][$tujuan['id_unik']] = array(
					'nama' => $tujuan['tujuan_teks'],
					'data' => array()
				);
			}

			$tujuan_ids[$tujuan['id_unik']] = "'".$tujuan['id_unik']."'";
			$sql = $wpdb->prepare("
				select 
					* 
				from data_rpjmd_sasaran
				where tahun_anggaran=%d
					and kode_tujuan=%s
					and active=1
			", $input['tahun_anggaran'], $tujuan['id_unik']);
			$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
			foreach ($sasaran_all as $sasaran) {
				if(empty($data_all['data'][$visi['id_visi']]['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
					$data_all['data'][$visi['id_visi']]['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
						'nama' => $sasaran['sasaran_teks'],
						'data' => array()
					);
				}

				$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
				$sql = $wpdb->prepare("
					select 
						* 
					from data_rpjmd_program
					where tahun_anggaran=%d
						and kode_sasaran=%s
						and active=1
				", $input['tahun_anggaran'], $sasaran['id_unik']);
				$program_all = $wpdb->get_results($sql, ARRAY_A);
				foreach ($program_all as $program) {
					$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
					if(empty($program['kode_skpd'])){
						$program['kode_skpd'] = '00';
						$program['nama_skpd'] = 'SKPD Kosong';
					}
					$skpd_filter[$program['kode_skpd']] = $program['nama_skpd'];
					if(empty($data_all['data'][$visi['id_visi']]['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
						$data_all['data'][$visi['id_visi']]['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
							'nama' => $program['nama_program'],
							'kode_skpd' => $program['kode_skpd'],
							'nama_skpd' => $program['nama_skpd'],
							'data' => array()
						);
					}
					if(empty($data_all['data'][$visi['id_visi']]['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
						$data_all['data'][$visi['id_visi']]['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
							'nama' => $program['indikator'],
							'data' => array()
						);
					}
				}
			}
		}
	}
}

// buat array data kosong
if(empty($data_all['data']['visi_kosong'])){
	$data_all['data']['visi_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'data' => array()
	);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong'])){
	$data_all['data']['visi_kosong']['data']['misi_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'data' => array()
	);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong'])){
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'data' => array()
	);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong'])){
	$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong'] = array(
		'nama' => '<span style="color: red">kosong</span>',
		'data' => array()
	);
}

// select misi yang belum terselect
$sql = $wpdb->prepare("
	select 
		* 
	from data_rpjmd_misi
	where tahun_anggaran=%d
		and active=1
		and id_misi not in (".implode(',', $misi_ids).")
", $input['tahun_anggaran']);
$misi_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($misi_all_kosong as $misi) {
	if(empty($data_all['data']['visi_kosong']['data'][$misi['id_misi']])){
		$data_all['data']['visi_kosong']['data'][$misi['id_misi']]['data'] = array(
			'nama' => $misi['misi_teks'],
			'data' => array()
		);
	}
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_tujuan
		where tahun_anggaran=%d
			and id_misi=%s
			and active=1
	", $input['tahun_anggaran'], $misi['id_misi']);
	$tujuan_all_kosong = $wpdb->get_results($sql, ARRAY_A);
	foreach ($tujuan_all_kosong as $tujuan) {
		$tujuan_ids[$tujuan['id_unik']] = "'".$tujuan['id_unik']."'";
		if(empty($data_all['data']['visi_kosong']['data'][$misi['id_misi']]['data'][$tujuan['id_unik']])){
			$data_all['data']['visi_kosong']['data'][$misi['id_misi']]['data'][$tujuan['id_unik']] = array(
				'nama' => $tujuan['sasaran_teks'],
				'data' => array()
			);
		}
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjmd_sasaran
			where tahun_anggaran=%d
				and kode_tujuan=%s
				and active=1
		", $input['tahun_anggaran'], $tujuan['id_unik']);
		$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($sasaran_all as $sasaran) {
			$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
			if(empty($data_all['data']['visi_kosong']['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
				$data_all['data']['visi_kosong']['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
					'nama' => $sasaran['sasaran_teks'],
					'data' => array()
				);
			}
			$sql = $wpdb->prepare("
				select 
					* 
				from data_rpjmd_program
				where tahun_anggaran=%d
					and kode_sasaran=%s
					and active=1
			", $input['tahun_anggaran'], $sasaran['id_unik']);
			$program_all = $wpdb->get_results($sql, ARRAY_A);
			foreach ($program_all as $program) {
				$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
				if(empty($data_all['data']['visi_kosong']['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
					$data_all['data']['visi_kosong']['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
						'nama' => $program['nama_program'],
						'kode_skpd' => $program['kode_skpd'],
						'nama_skpd' => $program['nama_skpd'],
						'data' => array()
					);
				}
				if(empty($data_all['data']['visi_kosong']['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
					$data_all['data']['visi_kosong']['data'][$misi['id_misi']]['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
						'nama' => $program['indikator'],
						'data' => array()
					);
				}
			}
		}
	}
}

// select tujuan yang belum terselect
$sql = $wpdb->prepare("
	select 
		* 
	from data_rpjmd_tujuan
	where tahun_anggaran=%d
		and active=1
		and id_unik not in (".implode(',', $tujuan_ids).")
", $input['tahun_anggaran']);
$tujuan_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($tujuan_all_kosong as $tujuan) {
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']] = array(
			'nama' => $tujuan['tujuan_teks'],
			'data' => array()
		);
	}
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_sasaran
		where tahun_anggaran=%d
			and kode_tujuan=%s
			and active=1
	", $input['tahun_anggaran'], $tujuan['id_unik']);
	$sasaran_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($sasaran_all as $sasaran) {
		$sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
		if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
				'nama' => $sasaran['sasaran_teks'],
				'data' => array()
			);
		}
		$sql = $wpdb->prepare("
			select 
				* 
			from data_rpjmd_program
			where tahun_anggaran=%d
				and kode_sasaran=%s
				and active=1
		", $input['tahun_anggaran'], $sasaran['id_unik']);
		$program_all = $wpdb->get_results($sql, ARRAY_A);
		foreach ($program_all as $program) {
			$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
			if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
				$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
					'nama' => $program['nama_program'],
					'kode_skpd' => $program['kode_skpd'],
					'nama_skpd' => $program['nama_skpd'],
					'data' => array()
				);
			}
			if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
				$data_all['data']['visi_kosong']['data']['misi_kosong']['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
					'nama' => $program['indikator'],
					'data' => array()
				);
			}
		}
	}
}

// select sasaran yang belum terselect
$sql = $wpdb->prepare("
	select 
		* 
	from data_rpjmd_sasaran
	where tahun_anggaran=%d
		and active=1
		and id_unik not in (".implode(',', $sasaran_ids).")
", $input['tahun_anggaran']);
$sasaran_all_kosong = $wpdb->get_results($sql, ARRAY_A);
foreach ($sasaran_all_kosong as $sasaran) {
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']] = array(
			'nama' => $sasaran['sasaran_teks'],
			'data' => array()
		);
	}
	$sql = $wpdb->prepare("
		select 
			* 
		from data_rpjmd_program
		where tahun_anggaran=%d
			and kode_sasaran=%s
			and active=1
	", $input['tahun_anggaran'], $sasaran['id_unik']);
	$program_all = $wpdb->get_results($sql, ARRAY_A);
	foreach ($program_all as $program) {
		$program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
		if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
				'nama' => $program['nama_program'],
				'kode_skpd' => $program['kode_skpd'],
				'nama_skpd' => $program['nama_skpd'],
				'data' => array()
			);
		}
		if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
			$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
				'nama' => $program['indikator'],
				'data' => array()
			);
		}
	}
}

// select program yang belum terselect
$sql = $wpdb->prepare("
	select 
		* 
	from data_rpjmd_program
	where tahun_anggaran=%d
		and id_unik not in (".implode(',', $program_ids).")
		and active=1
", $input['tahun_anggaran']);
$program_all = $wpdb->get_results($sql, ARRAY_A);
foreach ($program_all as $program) {
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']] = array(
			'nama' => $program['nama_program'],
			'kode_skpd' => $program['kode_skpd'],
			'nama_skpd' => $program['nama_skpd'],
			'data' => array()
		);
	}
	if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])){
		$data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
			'nama' => $program['indikator'],
			'data' => array()
		);
	}
}

// hapus array jika data dengan key kosong tidak ada datanya
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']['data'])){
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data']['sasaran_kosong']);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']['data'])){
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']['data']['tujuan_kosong']);
}
if(empty($data_all['data']['visi_kosong']['data']['misi_kosong']['data'])){
	unset($data_all['data']['visi_kosong']['data']['misi_kosong']);
}
if(empty($data_all['data']['visi_kosong']['data'])){
	unset($data_all['data']['visi_kosong']);
}

$body = '';
$no_visi = 0;
foreach ($data_all['data'] as $visi) {
	$no_visi++;
	$body .= '
		<tr class="tr-visi">
			<td class="kiri atas kanan bawah">'.$no_visi.'</td>
			<td class="atas kanan bawah">'.$visi['nama'].'</td>
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
				<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'</td>
				<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
				<td class="atas kanan bawah">'.$misi['nama'].'</td>
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
					<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'.'.$no_tujuan.'</td>
					<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
					<td class="atas kanan bawah"><span class="debug-misi">'.$misi['nama'].'</span></td>
					<td class="atas kanan bawah">'.$tujuan['nama'].'</td>
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
			foreach ($tujuan['data'] as $sasaran) {
				$no_sasaran++;
				$body .= '
					<tr class="tr-sasaran">
						<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'.'.$no_tujuan.'.'.$no_sasaran.'</td>
						<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
						<td class="atas kanan bawah"><span class="debug-misi">'.$misi['nama'].'</span></td>
						<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['nama'].'</span></td>
						<td class="atas kanan bawah">'.$sasaran['nama'].'</td>
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
					$no_program++;
					$text_indikator = array();
					foreach ($program['data'] as $indikator_program) {
						$text_indikator[] = $indikator_program['nama'];

					}
					$text_indikator = implode('<br>', $text_indikator);
					$body .= '
						<tr class="tr-program" data-kode-skpd="'.$program['kode_skpd'].'">
							<td class="kiri atas kanan bawah">'.$no_visi.'.'.$no_misi.'.'.$no_tujuan.'.'.$no_sasaran.'.'.$no_program.'</td>
							<td class="atas kanan bawah"><span class="debug-visi">'.$visi['nama'].'</span></td>
							<td class="atas kanan bawah"><span class="debug-misi">'.$misi['nama'].'</span></td>
							<td class="atas kanan bawah"><span class="debug-tujuan">'.$tujuan['nama'].'</span></td>
							<td class="atas kanan bawah"><span class="debug-sasaran">'.$sasaran['nama'].'</span></td>
							<td class="atas kanan bawah">'.$program['nama'].'</td>
							<td class="atas kanan bawah">'.$text_indikator.'</td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah"></td>
							<td class="atas kanan bawah">'.$program['nama_skpd'].'</td>
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
	$skpd_filter_html .= '<option value="'.$kode_skpd.'">'.$kode_skpd.' '.$nama_skpd.'</option>';
}
?>
<style type="text/css">
	.debug-visi, .debug-misi, .debug-tujuan, .debug-sasaran { display: none; }
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitoring dan Evaluasi RPJMD (Rencana Pembangunan Jangka Menengah Daerah) <br><?php echo $judul_skpd.'Tahun '.$input['tahun_anggaran'].' '.$nama_pemda; ?></h4>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; height: 80vh;">
	<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
		<thead>
			<tr>
				<th style="width: 85px;" class="atas kiri kanan bawah text_tengah text_blok">No</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Visi</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Misi</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Tujuan</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Sasaran</th>
				<th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Program</th>
				<th style="width: 400px;" class="atas kanan bawah text_tengah text_blok">Indikator</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Triwulan 1</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Triwulan 2</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Triwulan 3</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Triwulan 4</th>
				<th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Total Tahun Berjalan</th>
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
<script type="text/javascript">
	run_download_excel();
	let data_all = <?php echo json_encode($data_all); ?>;

	var aksi = ''
		+'<h3 style="margin-top: 20px;">SETTING</h3>'
		+'<label><input type="checkbox" onclick="edit_monev_indikator(this);"> Edit Monev indikator</label>'
		+'<label style="margin-left: 20px;"><input type="checkbox" onclick="show_debug(this);"> Debug Cascading RPJM</label>'
		+'<label style="margin-left: 20px;">'
			+'Sembunyikan Baris '
			+'<select id="sembunyikan-baris" onchange="sembunyikan_baris(this);" style="padding: 5px 10px; min-width: 200px;">'
				+'<option value="">Pilih Baris</option>'
				+'<option value="tr-misi">Misi</option>'
				+'<option value="tr-tujuan">Tujuan</option>'
				+'<option value="tr-sasaran">Sasaran</option>'
				+'<option value="tr-program">Program</option>'
			+'</select>'
		+'</label>'
		+'<label style="margin-left: 20px;">'
			+'Filter SKPD '
			+'<select onchange="filter_skpd(this);" style="padding: 5px 10px; min-width: 200px; max-width: 400px;">'
				+'<?php echo $skpd_filter_html; ?>'
			+'</select>'
		+'</label>';
	jQuery('#action-sipd').append(aksi);
	function filter_skpd(that){
		var tr_program = jQuery('.tr-program');
		var val = jQuery(that).val();
		if(val == ''){
			tr_program.show();
		}else{
			tr_program.hide();
			jQuery('.tr-program[data-kode-skpd="'+val+'"]').show();
		}
	}
	function sembunyikan_baris(that){
		var val = jQuery(that).val();
		var tr_misi = jQuery('.tr-misi');
		var tr_tujuan = jQuery('.tr-tujuan');
		var tr_sasaran = jQuery('.tr-sasaran');
		var tr_program = jQuery('.tr-program');
		tr_misi.show();
		tr_tujuan.show();
		tr_sasaran.show();
		tr_program.show();
		if(val == 'tr-misi'){
			tr_misi.hide();
			tr_tujuan.hide();
			tr_sasaran.hide();
			tr_program.hide();
		}else if(val == 'tr-tujuan'){
			tr_tujuan.hide();
			tr_sasaran.hide();
			tr_program.hide();
		}else if(val == 'tr-sasaran'){
			tr_sasaran.hide();
			tr_program.hide();
		}else if(val == 'tr-program'){
			tr_program.hide();
		}
	}
	function show_debug(that){
		if(jQuery(that).is(':checked')){
			jQuery('.debug-visi').show();
			jQuery('.debug-misi').show();
			jQuery('.debug-tujuan').show();
			jQuery('.debug-sasaran').show();
		}else{
			jQuery('.debug-visi').hide();
			jQuery('.debug-misi').hide();
			jQuery('.debug-tujuan').hide();
			jQuery('.debug-sasaran').hide();
		}
	}
	function edit_monev_indikator(that){
		if(jQuery(that).is(':checked')){
			jQuery('.edit-monev').show();
		}else{
			jQuery('.edit-monev').hide();
		}
	}
	jQuery('.edit-monev').on('click', function(){
		jQuery('#wrap-loading').show();
		jQuery('#mod-monev').modal('show');
		jQuery('#wrap-loading').hide();
	});
</script>