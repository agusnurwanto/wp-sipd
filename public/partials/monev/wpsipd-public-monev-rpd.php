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

if ($data_jadwal->jenis_jadwal != 'rpd') {
    die('<h1 class="text-center">Jenis Jadwal tidak valid.</h1>');
}

$jadwal_lokal_for_copy_data = $this->validasi_jadwal_perencanaan('rpd');
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

$timezone = get_option('timezone_string');

$akhir_rpd = $data_jadwal->tahun_anggaran + $data_jadwal->lama_pelaksanaan - 1;
$nama_pemda = get_option('_crb_daerah');

$current_user = wp_get_current_user();
$bulan = date('m');
$body_monev = '';

$data_all = array(
    'data' => array()
);
$bulan = date('m');

$tujuan_ids = array();
$sasaran_ids = array();
$program_ids = array();
$skpd_filter = array();

$sql = $wpdb->prepare("
	SELECT *
	FROM data_rpd_tujuan
	WHERE active = 1
      AND id_jadwal = %d
", $input['id_jadwal_lokal']);
$tujuan_all = $wpdb->get_results($sql, ARRAY_A);

foreach ($tujuan_all as $tujuan) {
    if (empty($data_all['data'][$tujuan['id_unik']])) {
        $data_all['data'][$tujuan['id_unik']] = array(
            'nama' => $tujuan['tujuan_teks'],
            'total_akumulasi_1' => 0,
            'total_akumulasi_2' => 0,
            'total_akumulasi_3' => 0,
            'total_akumulasi_4' => 0,
            'total_akumulasi_5' => 0,
            'detail' => array(),
            'data' => array()
        );
        $tujuan_ids[$tujuan['id_unik']] = "'" . $tujuan['id_unik'] . "'";
        $sql = $wpdb->prepare("
			SELECT * 
			FROM data_rpd_sasaran
			WHERE kode_tujuan = %s
              AND id_jadwal = %d
			  AND active = 1
		", $tujuan['id_unik'], $input['id_jadwal_lokal']);
        $sasaran_all = $wpdb->get_results($sql, ARRAY_A);
        foreach ($sasaran_all as $sasaran) {
            if (empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])) {
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
                    'nama' => $sasaran['sasaran_teks'],
                    'total_akumulasi_1' => 0,
                    'total_akumulasi_2' => 0,
                    'total_akumulasi_3' => 0,
                    'total_akumulasi_4' => 0,
                    'total_akumulasi_5' => 0,
                    'detail' => array(),
                    'data' => array()
                );
                $sasaran_ids[$sasaran['id_unik']] = "'" . $sasaran['id_unik'] . "'";
                $sql = $wpdb->prepare("
					SELECT * 
					FROM data_rpd_program
					WHERE kode_sasaran = %s
                      AND id_jadwal = %d
					  AND active = 1
					ORDER BY nama_program ASC
				", $sasaran['id_unik'], $input['id_jadwal_lokal']);
                $program_all = $wpdb->get_results($sql, ARRAY_A);
                foreach ($program_all as $program) {
                    $program_ids[$program['id_unik']] = "'" . $program['id_unik'] . "'";
                    if (empty($program['kode_skpd']) && empty($program['nama_skpd'])) {
                        $program['kode_skpd'] = '';
                        $program['nama_skpd'] = 'Semua Perangkat Daerah';
                    }
                    $skpd_filter[$program['kode_skpd']] = $program['nama_skpd'];
                    if (empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])) {

                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
                            'id_unik' => $program['id_unik'],
                            'nama' => $program['nama_program'],
                            'kode_skpd' => $program['kode_skpd'],
                            'nama_skpd' => $program['nama_skpd'],
                            'total_akumulasi_1' => 0,
                            'total_akumulasi_2' => 0,
                            'total_akumulasi_3' => 0,
                            'total_akumulasi_4' => 0,
                            'total_akumulasi_5' => 0,
                            'detail' => array(),
                            'data' => array()
                        );
                    }
                    $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['detail'][] = $program;
                    if (
                        !empty($program['id_unik_indikator'])
                        && empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])
                    ) {
                        $data_all['data'][$tujuan['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
                        $data_all['data'][$tujuan['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
                        $data_all['data'][$tujuan['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
                        $data_all['data'][$tujuan['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
                        $data_all['data'][$tujuan['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
                        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
                            'nama' => $program['indikator'],
                            'data' => $program
                        );
                    }
                }
            }
            $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
        }
    }
    $data_all['data'][$tujuan['id_unik']]['detail'][] = $tujuan;
}

// buat array data kosong
if (empty($data_all['data']['tujuan_kosong'])) {
    $data_all['data']['tujuan_kosong'] = array(
        'nama' => '<span style="color: red">kosong</span>',
        'total_akumulasi_1' => 0,
        'total_akumulasi_2' => 0,
        'total_akumulasi_3' => 0,
        'total_akumulasi_4' => 0,
        'total_akumulasi_5' => 0,
        'detail' => array(
            array(
                'id_unik' => 'kosong',
                'isu_teks' => ''
            )
        ),
        'data' => array()
    );
}
if (empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong'])) {
    $data_all['data']['tujuan_kosong']['data']['sasaran_kosong'] = array(
        'nama' => '<span style="color: red">kosong</span>',
        'total_akumulasi_1' => 0,
        'total_akumulasi_2' => 0,
        'total_akumulasi_3' => 0,
        'total_akumulasi_4' => 0,
        'total_akumulasi_5' => 0,
        'detail' => array(
            array(
                'id_unik' => 'kosong',
                'isu_teks' => ''
            )
        ),
        'data' => array()
    );
}

// SELECT tujuan yang belum terSELECT
if (!empty($tujuan_ids)) {
    $sql = $wpdb->prepare("
		SELECT 
			t.*
		FROM data_rpd_tujuan t
		WHERE t.id_unik not in (" . implode(',', $tujuan_ids) . ")
		  AND t.active = 1
          AND t.id_jadwal = %d
	", $input['id_jadwal_lokal']);
} else {
    $sql = $wpdb->prepare("
		SELECT 
			t.*
		FROM data_rpd_tujuan t
		WHERE t.active = 1
          AND t.id_jadwal = %d
	", $input['id_jadwal_lokal']);
}
$tujuan_all_kosong = $wpdb->get_results($sql, ARRAY_A);

foreach ($tujuan_all_kosong as $tujuan) {
    if (empty($data_all['data'][$tujuan['id_unik']])) {
        $data_all['data'][$tujuan['id_unik']] = array(
            'nama' => $tujuan['tujuan_teks'],
            'total_akumulasi_1' => 0,
            'total_akumulasi_2' => 0,
            'total_akumulasi_3' => 0,
            'total_akumulasi_4' => 0,
            'total_akumulasi_5' => 0,
            'detail' => array(),
            'data' => array()
        );
    }
    $data_all['data'][$tujuan['id_unik']]['detail'][] = $tujuan;
    
    $sql = $wpdb->prepare("
		SELECT * 
		FROM data_rpd_sasaran
		WHERE kode_tujuan = %s
		  AND active = 1
          AND id_jadwal = %d
	", $tujuan['id_unik'], $input['id_jadwal_lokal']);
    $sasaran_all = $wpdb->get_results($sql, ARRAY_A);

    foreach ($sasaran_all as $sasaran) {
        $sasaran_ids[$sasaran['id_unik']] = "'" . $sasaran['id_unik'] . "'";
        if (empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])) {
            $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
                'nama' => $sasaran['sasaran_teks'],
                'total_akumulasi_1' => 0,
                'total_akumulasi_2' => 0,
                'total_akumulasi_3' => 0,
                'total_akumulasi_4' => 0,
                'total_akumulasi_5' => 0,
                'detail' => array(),
                'data' => array()
            );
        }
        $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;

        $sql = $wpdb->prepare("
			SELECT * 
			FROM data_rpd_program
			WHERE kode_sasaran = %s
			  AND active = 1
              AND id_jadwal = %d
		", $sasaran['id_unik'], $input['id_jadwal_lokal']);
        $program_all = $wpdb->get_results($sql, ARRAY_A);

        foreach ($program_all as $program) {
            $program_ids[$program['id_unik']] = "'" . $program['id_unik'] . "'";
            if (empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])) {
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
                    'nama' => $program['nama_program'],
                    'kode_skpd' => $program['kode_skpd'],
                    'nama_skpd' => $program['nama_skpd'],
                    'total_akumulasi_1' => 0,
                    'total_akumulasi_2' => 0,
                    'total_akumulasi_3' => 0,
                    'total_akumulasi_4' => 0,
                    'total_akumulasi_5' => 0,
                    'detail' => array(),
                    'data' => array()
                );
            }
            $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['detail'][] = $program;

            if (
                !empty($program['id_unik_indikator'])
                && empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])
            ) {
                $data_all['data'][$tujuan['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
                $data_all['data'][$tujuan['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
                $data_all['data'][$tujuan['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
                $data_all['data'][$tujuan['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
                $data_all['data'][$tujuan['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
                    'nama' => $program['indikator'],
                    'data' => $program
                );
            }
        }
    }
}

// SELECT sasaran yang belum terSELECT
if (!empty($sasaran_ids)) {
    $sql = $wpdb->prepare("
		SELECT * 
		FROM data_rpd_sasaran
		WHERE id_unik not in (" . implode(',', $sasaran_ids) . ")
		  AND active = 1
          AND id_jadwal = %d
	", $input['id_jadwal_lokal']);
} else {
    $sql = $wpdb->prepare("
		SELECT * 
		FROM data_rpd_sasaran
		WHERE active = 1
          AND id_jadwal = %d   
	", $input['id_jadwal_lokal']);
}
$sasaran_all_kosong = $wpdb->get_results($sql, ARRAY_A);

foreach ($sasaran_all_kosong as $sasaran) {
    if (empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']])) {
        $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']] = array(
            'nama' => $sasaran['sasaran_teks'],
            'total_akumulasi_1' => 0,
            'total_akumulasi_2' => 0,
            'total_akumulasi_3' => 0,
            'total_akumulasi_4' => 0,
            'total_akumulasi_5' => 0,
            'detail' => array(),
            'data' => array()
        );
    }
    $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['detail'][] = $sasaran;

    $sql = $wpdb->prepare("
		SELECT * 
		FROM data_rpd_program
		WHERE kode_sasaran = %s
		  AND active = 1
          AND id_jadwal = %d
	", $sasaran['id_unik'], $input['id_jadwal_lokal']);
    $program_all = $wpdb->get_results($sql, ARRAY_A);

    foreach ($program_all as $program) {
        $program_ids[$program['id_unik']] = "'" . $program['id_unik'] . "'";
        if (empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']])) {
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
                'nama' => $program['nama_program'],
                'kode_skpd' => $program['kode_skpd'],
                'nama_skpd' => $program['nama_skpd'],
                'total_akumulasi_1' => 0,
                'total_akumulasi_2' => 0,
                'total_akumulasi_3' => 0,
                'total_akumulasi_4' => 0,
                'total_akumulasi_5' => 0,
                'detail' => array(),
                'data' => array()
            );
        }
        $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['detail'][] = $program;

        if (
            !empty($program['id_unik_indikator'])
            && empty($data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])
        ) {
            $data_all['data']['tujuan_kosong']['total_akumulasi_1'] += $program['pagu_1'];
            $data_all['data']['tujuan_kosong']['total_akumulasi_2'] += $program['pagu_2'];
            $data_all['data']['tujuan_kosong']['total_akumulasi_3'] += $program['pagu_3'];
            $data_all['data']['tujuan_kosong']['total_akumulasi_4'] += $program['pagu_4'];
            $data_all['data']['tujuan_kosong']['total_akumulasi_5'] += $program['pagu_5'];
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
            $data_all['data']['tujuan_kosong']['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
                'nama' => $program['indikator'],
                'data' => $program
            );
        }
    }
}

// SELECT program yang belum terSELECT
if (!empty($program_ids)) {
    $sql = $wpdb->prepare("
		SELECT * 
		FROM data_rpd_program
		WHERE id_unik not in (" . implode(',', $program_ids) . ")
          AND active = 1
          AND id_jadwal = %d
	", $input['id_jadwal_lokal']);
} else {
    $sql = $wpdb->prepare("
		SELECT * 
		FROM data_rpd_program
		WHERE active = 1
          AND id_jadwal = %d
	", $input['id_jadwal_lokal']);
}
$program_all = $wpdb->get_results($sql, ARRAY_A);

foreach ($program_all as $program) {
    if (empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']])) {
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']] = array(
            'nama' => $program['nama_program'],
            'kode_skpd' => $program['kode_skpd'],
            'nama_skpd' => $program['nama_skpd'],
            'total_akumulasi_1' => 0,
            'total_akumulasi_2' => 0,
            'total_akumulasi_3' => 0,
            'total_akumulasi_4' => 0,
            'total_akumulasi_5' => 0,
            'detail' => array(),
            'data' => array()
        );
    }
    $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['detail'][] = $program;

    if (empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])) {
        $data_all['data']['tujuan_kosong']['total_akumulasi_1'] += $program['pagu_1'];
        $data_all['data']['tujuan_kosong']['total_akumulasi_2'] += $program['pagu_2'];
        $data_all['data']['tujuan_kosong']['total_akumulasi_3'] += $program['pagu_3'];
        $data_all['data']['tujuan_kosong']['total_akumulasi_4'] += $program['pagu_4'];
        $data_all['data']['tujuan_kosong']['total_akumulasi_5'] += $program['pagu_5'];
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['total_akumulasi_1'] += $program['pagu_1'];
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['total_akumulasi_2'] += $program['pagu_2'];
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['total_akumulasi_3'] += $program['pagu_3'];
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['total_akumulasi_4'] += $program['pagu_4'];
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['total_akumulasi_5'] += $program['pagu_5'];
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
        $data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
            'nama' => $program['indikator'],
            'data' => $program
        );
    }
}

// hapus array jika data dengan key kosong tidak ada datanya
if (empty($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']['data'])) {
    unset($data_all['data']['tujuan_kosong']['data']['sasaran_kosong']);
}
if (empty($data_all['data']['tujuan_kosong']['data'])) {
    unset($data_all['data']['tujuan_kosong']);
}

// print_r($data_all);

$body = '';
$no_tujuan = 0;
foreach ($data_all['data'] as $tujuan) {
    $no_tujuan++;
    $indikator_tujuan = '';
    $target_awal = '';
    $target_1 = '';
    $target_2 = '';
    $target_3 = '';
    $target_4 = '';
    $target_5 = '';
    $target_akhir = '';
    $satuan = '';
    foreach ($tujuan['detail'] as $k => $v) {
        if (!empty($v['indikator_teks'])) {
            $indikator_tujuan .= '<div class="indikator_program">' . $v['indikator_teks'] . button_edit_monev($v['id_unik'] . '|' . $v['id_unik_indikator']) . '</div>';
            $target_awal .= '<div class="indikator_program">' . $v['target_awal'] . '</div>';
            $target_1 .= '<div class="indikator_program">' . $v['target_1'] . '</div>';
            $target_2 .= '<div class="indikator_program">' . $v['target_2'] . '</div>';
            $target_3 .= '<div class="indikator_program">' . $v['target_3'] . '</div>';
            $target_4 .= '<div class="indikator_program">' . $v['target_4'] . '</div>';
            $target_5 .= '<div class="indikator_program">' . $v['target_5'] . '</div>';
            $target_akhir .= '<div class="indikator_program">' . $v['target_akhir'] . '</div>';
            $satuan .= '<div class="indikator_program">' . $v['satuan'] . '</div>';
        }
    }
    $target_html = "";
    for ($i = 1; $i <= $data_jadwal->lama_pelaksanaan; $i++) {
        $target_html .= '<td class="atas kanan bawah text_tengah">' . ${'target_' . $i} . '</td>';
        $target_html .= '<td class="atas kanan bawah text_tengah"><b>(' . $this->_number_format($tujuan['total_akumulasi_' . $i]) . ')</b></td>';
    }
    $warning = "";
    // if (empty($tujuan['detail'][0]['id_isu'])) {
    //     $warning = "style='background: #80000014;'";
    // }
    // $no_urut = '';
    // if (!empty($tujuan['detail'][0]['no_urut'])) {
    //     $no_urut = $tujuan['detail'][0]['no_urut'];
    // }
    $body .= '
		<tr class="tr-tujuan" ' . $warning . '>
			<td class="kiri atas kanan bawah">' . $no_tujuan . '</td>
			<td class="atas kanan bawah">' . $tujuan['detail'][0]['isu_teks'] . '</td>
			<td class="atas kanan bawah">' . parsing_nama_kode($tujuan['nama']) . button_edit_monev($tujuan['detail'][0]['id_unik']) . '</td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah">' . $indikator_tujuan . '</td>
			<td class="atas kanan bawah text_tengah">' . $target_awal . '</td>
			' . $target_html . '
			<td class="atas kanan bawah text_tengah">' . $target_akhir . '</td>
			<td class="atas kanan bawah">' . $satuan . '</td>
			<td class="atas kanan bawah"></td>
			<td class="atas kanan bawah"></td>
		</tr>
	';
    $no_sasaran = 0;
    foreach ($tujuan['data'] as $sasaran) {
        $no_sasaran++;
        $indikator_sasaran = '';
        $target_awal = '';
        $target_1 = '';
        $target_2 = '';
        $target_3 = '';
        $target_4 = '';
        $target_5 = '';
        $target_akhir = '';
        $satuan = '';
        foreach ($sasaran['detail'] as $k => $v) {
            if (!empty($v['indikator_teks'])) {
                $indikator_sasaran .= '<div class="indikator_program">' . $v['indikator_teks'] . button_edit_monev($tujuan['detail'][0]['id_unik'] . '||' . $v['id_unik'] . '|' . $v['id_unik_indikator']) . '</div>';
                $target_awal .= '<div class="indikator_program">' . $v['target_awal'] . '</div>';
                $target_1 .= '<div class="indikator_program">' . $v['target_1'] . '</div>';
                $target_2 .= '<div class="indikator_program">' . $v['target_2'] . '</div>';
                $target_3 .= '<div class="indikator_program">' . $v['target_3'] . '</div>';
                $target_4 .= '<div class="indikator_program">' . $v['target_4'] . '</div>';
                $target_5 .= '<div class="indikator_program">' . $v['target_5'] . '</div>';
                $target_akhir .= '<div class="indikator_program">' . $v['target_akhir'] . '</div>';
                $satuan .= '<div class="indikator_program">' . $v['satuan'] . '</div>';
            }
        }
        $target_html = "";
        for ($i = 1; $i <= $data_jadwal->lama_pelaksanaan; $i++) {
            $target_html .= '<td class="atas kanan bawah text_tengah">' . ${'target_' . $i} . '</td>';
            $target_html .= '<td class="atas kanan bawah text_tengah"><b>(' . $this->_number_format($sasaran['total_akumulasi_' . $i]) . ')</b></td>';
        }
        $sasaran_no_urut = '';
        if (!empty($sasaran['detail'][0]['sasaran_no_urut'])) {
            $sasaran_no_urut = $sasaran['detail'][0]['sasaran_no_urut'];
        }
        $body .= '
			<tr class="tr-sasaran" ' . $warning . '>
				<td class="kiri atas kanan bawah">' . $no_tujuan . '.' . $no_sasaran . '</td>
				<td class="atas kanan bawah"><span class="debug-tujuan">' . $tujuan['detail'][0]['isu_teks'] . '</span></td>
				<td class="atas kanan bawah"><span class="debug-tujuan">' . $tujuan['nama'] . '</span></td>
				<td class="atas kanan bawah">' . parsing_nama_kode($sasaran['nama']) . button_edit_monev($tujuan['detail'][0]['id_unik'] . '||' . $sasaran['detail'][0]['id_unik']) . '</td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah">' . $indikator_sasaran . '</td>
				<td class="atas kanan bawah text_tengah">' . $target_awal . '</td>
				' . $target_html . '
				<td class="atas kanan bawah text_tengah">' . $target_akhir . '</td>
				<td class="atas kanan bawah">' . $satuan . '</td>
				<td class="atas kanan bawah"></td>
				<td class="atas kanan bawah">' . $sasaran_no_urut . '</td>
			</tr>
		';
        $no_program = 0;
        foreach ($sasaran['data'] as $program) {
            $no_program++;
            $text_indikator = array();
            $target_awal = array();
            $target_1 = array();
            $target_2 = array();
            $target_3 = array();
            $target_4 = array();
            $target_5 = array();
            $pagu_1 = array();
            $pagu_2 = array();
            $pagu_3 = array();
            $pagu_4 = array();
            $pagu_5 = array();
            $target_akhir = array();
            $satuan = array();
            $nama_skpd = array();
            foreach ($program['data'] as $indikator_program) {
                $text_indikator[] = '<div class="indikator_program">' . $indikator_program['nama'] . button_edit_monev($tujuan['detail'][0]['id_unik'] . '||' . $sasaran['detail'][0]['id_unik'] . '||' . $indikator_program['data']['id_unik'] . '|' . $indikator_program['data']['id_unik_indikator']) . '</div>';
                $target_awal[] = '<div class="indikator_program">' . $indikator_program['data']['target_awal'] . '</div>';
                $target_1[] = '<div class="indikator_program">' . $indikator_program['data']['target_1'] . '</div>';
                $target_2[] = '<div class="indikator_program">' . $indikator_program['data']['target_2'] . '</div>';
                $target_3[] = '<div class="indikator_program">' . $indikator_program['data']['target_3'] . '</div>';
                $target_4[] = '<div class="indikator_program">' . $indikator_program['data']['target_4'] . '</div>';
                $target_5[] = '<div class="indikator_program">' . $indikator_program['data']['target_5'] . '</div>';
                $pagu_1[] = '<div class="indikator_program">' . $this->_number_format($indikator_program['data']['pagu_1']) . '</div>';
                $pagu_2[] = '<div class="indikator_program">' . $this->_number_format($indikator_program['data']['pagu_2']) . '</div>';
                $pagu_3[] = '<div class="indikator_program">' . $this->_number_format($indikator_program['data']['pagu_3']) . '</div>';
                $pagu_4[] = '<div class="indikator_program">' . $this->_number_format($indikator_program['data']['pagu_4']) . '</div>';
                $pagu_5[] = '<div class="indikator_program">' . $this->_number_format($indikator_program['data']['pagu_5']) . '</div>';
                $target_akhir[] = '<div class="indikator_program">' . $indikator_program['data']['target_akhir'] . '</div>';
                $satuan[] = '<div class="indikator_program">' . $indikator_program['data']['satuan'] . '</div>';
                $nama_skpd[] = '<div class="indikator_program">' . $indikator_program['data']['kode_skpd'] . ' ' . $indikator_program['data']['nama_skpd'] . '</div>';
            }
            $text_indikator = implode('', $text_indikator);
            $target_awal = implode('', $target_awal);
            $target_1 = implode('', $target_1);
            $target_2 = implode('', $target_2);
            $target_3 = implode('', $target_3);
            $target_4 = implode('', $target_4);
            $target_5 = implode('', $target_5);
            $pagu_1 = implode('', $pagu_1);
            $pagu_2 = implode('', $pagu_2);
            $pagu_3 = implode('', $pagu_3);
            $pagu_4 = implode('', $pagu_4);
            $pagu_5 = implode('', $pagu_5);
            $target_akhir = implode('', $target_akhir);
            $satuan = implode('', $satuan);
            $nama_skpd = implode('', $nama_skpd);
            $target_html = "";
            for ($i = 1; $i <= $data_jadwal->lama_pelaksanaan; $i++) {
                $target_html .= '<td class="atas kanan bawah text_tengah">' . ${'target_' . $i} . '</td>';
                $target_html .= '<td class="atas kanan bawah text_tengah">' . ${'pagu_' . $i} . '<div class="indikator_program"><b>(' . $this->_number_format($program['total_akumulasi_' . $i]) . ')</b></div></td>';
            }

            $body .= '
				<tr class="tr-program" data-kode-skpd="' . $program['kode_skpd'] . '" ' . $warning . '>
					<td class="kiri atas kanan bawah">' . $no_tujuan . '.' . $no_sasaran . '.' . $no_program . '</td>
					<td class="atas kanan bawah"><span class="debug-tujuan">' . $tujuan['detail'][0]['isu_teks'] . '</span></td>
					<td class="atas kanan bawah"><span class="debug-tujuan">' . $tujuan['nama'] . '</span></td>
					<td class="atas kanan bawah"><span class="debug-sasaran">' . $sasaran['nama'] . '</span></td>
					<td class="atas kanan bawah">' . parsing_nama_kode($program['nama']) . button_edit_monev($tujuan['detail'][0]['id_unik'] . '||' . $sasaran['detail'][0]['id_unik'] . '||' . $program['detail'][0]['id_unik']) . '</td>
					<td class="atas kanan bawah">' . $text_indikator . '</td>
					<td class="atas kanan bawah text_tengah">' . $target_awal . '</td>
					' . $target_html . '
					<td class="atas kanan bawah text_tengah">' . $target_akhir . '</td>
					<td class="atas kanan bawah text_tengah">' . $satuan . '</td>
					<td class="atas kanan bawah">' . $nama_skpd . '</td>
					<td class="atas kanan bawah"></td>
				</tr>
			';
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

    .aksi button {
        margin: 3px;
    }

    .tr-tujuan {
        background: #0000ff1f;
    }

    .tr-sasaran {
        background: #ffff0059;
    }

    .tr-program {
        background: #baffba;
    }
</style>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Monitoring dan Evaluasi RPD (Rencana Pembangunan Daerah) <br><?php echo $nama_pemda; ?><br><?php echo $data_jadwal->tahun_anggaran . ' - ' . $akhir_rpd; ?></h4>
<div id="cetak" title="Indikator RPD - <?php echo $data_jadwal->nama; ?>" style="padding: 5px; overflow: auto; height: 80vh;">
    <table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; font-size: 70%; border: 0; table-layout: fixed;" contenteditable="false">
        <thead>
            <tr>
                <th style="width: 85px;" class="atas kiri kanan bawah text_tengah text_blok">No</th>
                <th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Isu RPJPD</th>
                <th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Tujuan</th>
                <th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Sasaran</th>
                <th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Program</th>
                <th style="width: 400px;" class="atas kanan bawah text_tengah text_blok">Indikator</th>
                <th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Awal</th>
                <?php for ($i = 1; $i <= $data_jadwal->lama_pelaksanaan; $i++) { ?>
                    <th style="width: 300px;" class="atas kanan bawah text_tengah text_blok" colspan="2">Tahun <?php echo $i; ?></th>
                <?php }; ?>
                <th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Target Akhir</th>
                <th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">Satuan</th>
                <th style="width: 150px;" class="atas kanan bawah text_tengah text_blok">Keterangan</th>
                <th style="width: 100px;" class="atas kanan bawah text_tengah text_blok">No. Urut</th>
            </tr>
            <tr>
                <th rowspan="2" class='atas kiri kanan bawah text_tengah text_blok'>1</th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'>2</th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'>3</th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'>4</th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'>5</th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'>6</th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'>7</th>
                <?php for ($i = 1; $i <= $data_jadwal->lama_pelaksanaan; $i++) { ?>
                    <th class="atas kanan bawah text_tengah text_blok" colspan="2"><?php echo 7 + $i; ?></th>
                <?php }; ?>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'><?php echo $i + 7; ?></th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'><?php echo $i + 8; ?></th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'><?php echo $i + 9; ?></th>
                <th rowspan="2" class='atas kanan bawah text_tengah text_blok'><?php echo $i + 10; ?></th>
            </tr>
            <tr>
                <?php for ($i = 1; $i <= $data_jadwal->lama_pelaksanaan; $i++) { ?>
                    <th class="atas kanan bawah text_tengah text_blok">Target</th>
                    <th style="width: 200px;" class="atas kanan bawah text_tengah text_blok">Pagu</th>
                <?php }; ?>
            </tr>
        </thead>
        <tbody>
            <?php echo $body; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modal-raw" tabindex="-2" role="dialog" aria-labelledby="modal-crud-RPD-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(() => {
        run_download_excel();

        let data_all = <?php echo json_encode($data_all); ?>;
        let status_jadwal_lokal = <?php echo $status_copy_data; ?>;

        let aksi = ``;
        if (status_jadwal_lokal) {
            aksi += `
				<button class="btn btn-warning" onclick="copyDataLokal()">
					Copy Data RPD Lokal
				</button>
			`;
        }

        aksi += `
			<h3 style="margin-top: 20px;">PENGATURAN</h3>
			<label style="margin-left: 20px;">
				<input type="checkbox" onclick="show_debug(this);"> Debug Cascading RPD
			</label>
			<label style="margin-left: 20px;">
				Sembunyikan Baris 
				<SELECT id="sembunyikan-baris" onchange="sembunyikan_baris(this);" style="padding: 5px 10px; min-width: 200px;">
					<option value="">Pilih Baris</option>
					<option value="tr-sasaran">Sasaran</option>
					<option value="tr-program">Program</option>
				</SELECT>
			</label>
			<label style="margin-left: 20px;">
				Filter SKPD 
				<SELECT onchange="filter_skpd(this);" style="padding: 5px 10px; min-width: 200px; max-width: 400px;">
					<?php echo $skpd_filter_html; ?>
				</SELECT>
			</label>
		`;
        jQuery('#action-sipd').append(aksi);

        jQuery('.edit-monev').on('click', function() {
            jQuery('#wrap-loading').show();
            jQuery('#mod-monev').modal('show');
            jQuery('#wrap-loading').hide();
        });
    });

    function copyDataLokal() {
        if (confirm('Apakah anda yakin untuk mengambil data dari lokal? data lama akan diupdate!')) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: ajax.url,
                type: "post",
                data: {
                    action: 'copy_data_monev_rpjmd_rpd_from_data_local',
                    api_key: ajax.api_key,
                    type: 'rpd',
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

    function sembunyikan_baris(that) {
        var val = jQuery(that).val();
        var tr_sasaran = jQuery('.tr-sasaran');
        var tr_program = jQuery('.tr-program');
        tr_sasaran.show();
        tr_program.show();
        if (val == 'tr-sasaran') {
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
</script>