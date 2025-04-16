<?php
global $wpdb;

if (!defined('WPINC')) {
	die;
}

$input = shortcode_atts(array(
	'tahun_anggaran' => '2022'
), $atts);

$tahun_anggaran = $input['tahun_anggaran'];
$api_key = get_option('_crb_api_key_extension');

$unit = $wpdb->get_results(
	$wpdb->prepare("
	SELECT 
		*	
	FROM data_unit 
	WHERE active=1 
	  AND tahun_anggaran=%d
	ORDER BY kode_skpd ASC
	", $tahun_anggaran),
	ARRAY_A
);

if (!empty($unit)) {
	$all_unit = array();
	foreach ($unit as $kk => $vv) {
		$all_unit[$vv['id_skpd']] = $vv;
	}
}

$sql_anggaran = $wpdb->prepare("
    SELECT
        s.*,
        sum(r.total_harga) as total_rincian,
        sum(r.rincian_murni) as total_murni,
        r.nama_akun,
        r.kode_akun
    FROM data_rka r
    left join data_sub_keg_bl s on s.kode_sbl=r.kode_sbl
        AND s.tahun_anggaran=r.tahun_anggaran
        AND s.active=r.active
    WHERE r.tahun_anggaran=%d
        AND r.active=1
    GROUP by r.kode_akun, s.kode_sbl, s.id_sub_skpd
    ",$input["tahun_anggaran"]);
$data_anggaran = $wpdb->get_results($sql_anggaran, ARRAY_A);
$tbody2 = '';
$pagu_efisiensi = 0;
$total_all_pagu_sebelum = 0;
$total_all_pagu = 0;
$total_all_rek_pagu_sebelum = 0;
$total_all_rek_pagu = 0;
$total_all_rek_efisiensi = 0;
$total_all_rek_selisih = 0;
$total_all_rek_realisasi = 0;
$no = 1;
$no2 = 1;
if(!empty($data_anggaran)){
    $sub_keg_all = array();
    $rek_all = array();

    $data_all_unit = array();
    foreach ($data_anggaran as $v_anggaran) {
        $kode_akun = $v_anggaran['kode_akun'];
        $kode_sbl = $v_anggaran['kode_sbl'];

        $kode_sbl_kas = explode('.', $kode_sbl);
        $kode_sbl_kas = $kode_sbl_kas[0].'.'.$kode_sbl_kas[0].'.'.$kode_sbl_kas[1].'.'.$v_anggaran['id_bidang_urusan'].'.'.$kode_sbl_kas[2].'.'.$kode_sbl_kas[3].'.'.$kode_sbl_kas[4];
        $v_anggaran['kode_sbl'] = $kode_sbl_kas;
        
        //get per akun belanja
        if(empty($sub_keg_all[$kode_sbl])){
            $sub_keg_all[$kode_sbl] = array(
                'sub_keg' => array(),
                'data' => array()
            );
            $sub_keg_all[$kode_sbl]['sub_keg'] = $v_anggaran;
        }

        $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']] = array(
        	'pagu_murni' => 0,
        	'realisasi' => 0, 
        	'pagu_efisiensi' => 0, 
        	'keterangan' => ''
        );

        $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['pagu_murni'] = $v_anggaran['total_murni'];

        $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['realisasi'] = $wpdb->get_var($wpdb->prepare("
            SELECT
                realisasi
            FROM data_realisasi_akun_sipd
            WHERE active=1
                AND tahun_anggaran=%d
                AND kode_akun=%s
                AND (
                    kode_sbl=%s
                    or kode_sbl=%s
                )
        ", $input["tahun_anggaran"], $kode_akun, $kode_sbl, $v_anggaran['kode_sbl']));

        $efisiensi = $wpdb->get_row($wpdb->prepare("
            SELECT
                pagu_efisiensi,
                keterangan
            FROM data_efisiensi_belanja
            WHERE active=1
                AND tahun_anggaran=%d
                AND (
                    kode_sbl=%s
                    or kode_sbl=%s
                ) AND kode_akun=%s
        ", $input["tahun_anggaran"], $kode_sbl, $v_anggaran['kode_sbl'], $v_anggaran['kode_akun']), ARRAY_A);
        if(empty($efisiensi)){
            $efisiensi = array(
                'pagu_efisiensi' => 0,
                'keterangan' => ''
            );
            $wpdb->insert('data_efisiensi_belanja', array(
            	'pagu_efisiensi' => $v_anggaran['total_rincian'],
            	'kode_sbl' => $v_anggaran['kode_sbl'],
            	'tahun_anggaran' => $input['tahun_anggaran'],
            	'id_skpd' => $v_anggaran['id_sub_skpd'],
            	'kode_akun' => $v_anggaran['kode_akun'],
            	'active' => 1
            ));
        }
        $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['pagu_efisiensi'] = $efisiensi['pagu_efisiensi'];
        $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['keterangan'] = $efisiensi['keterangan'];

        $nama_akun = explode(' ', $v_anggaran['nama_akun']);
        unset($nama_akun[0]);
        $nama_akun = implode(' ', $nama_akun);
        $selisih = $v_anggaran['total_rincian'] - $sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['pagu_efisiensi'];

        if (empty($rek_all[$kode_akun])) {
		    $rek_all[$kode_akun] = [
		        'nama_akun' => $nama_akun,
		        'pagu_murni' => 0,
		        'total_rincian' => 0,
		        'pagu_efisiensi' => 0,
		        'realisasi' => 0,
		    ];
		}

        $rek_all[$kode_akun]['pagu_murni'] += floatval($v_anggaran['total_murni']);
		$rek_all[$kode_akun]['total_rincian'] += floatval($v_anggaran['total_rincian']);
		$rek_all[$kode_akun]['pagu_efisiensi'] += floatval($efisiensi['pagu_efisiensi']);
		$rek_all[$kode_akun]['realisasi'] += floatval($sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['realisasi']);

		if(empty($data_all_unit[$v_anggaran['id_sub_skpd']])){
	        $data_all_unit[$v_anggaran['id_sub_skpd']] = array(
				'total' => 0,
				'total_efisiensi' => 0,
				'total_murni' => 0,
				'realisasi' => 0,
				'data' => $all_unit[$v_anggaran['id_sub_skpd']]
			);
		}
		$data_all_unit[$v_anggaran['id_sub_skpd']]['total_murni'] += floatval($v_anggaran['total_murni']);
		$data_all_unit[$v_anggaran['id_sub_skpd']]['total'] += floatval($v_anggaran['total_rincian']);
		$data_all_unit[$v_anggaran['id_sub_skpd']]['total_efisiensi'] += floatval($efisiensi['pagu_efisiensi']);
		$data_all_unit[$v_anggaran['id_sub_skpd']]['realisasi'] += floatval($sub_keg_all[$kode_sbl]['data'][$v_anggaran['kode_akun']]['realisasi']);
    }

	$tbody = '';
	$total_all = 0;
	$total_all_murni = 0;
	$total_all_efisiensi = 0;
	$total_all_selisih = 0;
	$total_all_realisasi = 0;
    foreach ($data_all_unit as $id_sub_skpd => $data) {
		$selisih = $data['total'] - $data['total_efisiensi'];
		$title = 'Detail Efisiensi Belanja | ' . $tahun_anggaran;
		$shortcode = '[detail_efisiensi_belanja tahun_anggaran="' . $tahun_anggaran . '"]';
		$update = false;
		$url_skpd = $this->generatePage($title, $tahun_anggaran, $shortcode, $update);
		$tbody .= "<tr>";
		$tbody .= "<td style='text-transform: uppercase;'><a target='_blank' href='" . $url_skpd . "&id_skpd=" . $data['data']['id_skpd'] . "'>" . $data['data']['kode_skpd'] . " " . $data['data']['nama_skpd'] . "</a></td>";
		$tbody .= "<td class='text-right'>" . number_format($data['total_murni'], 0, '.', ',') . "</td>";
		$tbody .= "<td class='text-right'>" . number_format($data['total'], 0, '.', ',') . "</td>";
		$tbody .= "<td class='text-right'>" . number_format($data['total_efisiensi'], 0, '.', ',') . "</td>";
		$tbody .= "<td class='text-right'>" . number_format($selisih, 0, '.', ',') . "</td>";
		$tbody .= "<td class='text-right'>" . number_format($data['realisasi'], 0, '.', ',') . "</td>";
		$tbody .= "</tr>";

		$total_all += $data['total'];
		$total_all_murni += $data['total_murni'];
		$total_all_efisiensi += $data['total_efisiensi'];
		$total_all_selisih += $selisih;
		$total_all_realisasi += $data['realisasi'];
    }

    foreach ($rek_all as $kode_akun => $data) {
        $selisih_rek = $data['total_rincian'] - $data['pagu_efisiensi'];
        $title = 'Detail Efisiensi Belanja Pemerintah Daerah | ' . $tahun_anggaran;
		$shortcode = '[detail_efisiensi_belanja_pemda tahun_anggaran="' . $tahun_anggaran . '"]';

		$update = false;
		$url_pemda = $this->generatePage($title, $tahun_anggaran, $shortcode, $update);
        $tbody2 .= '
            <tr>
                <td class="text_tengah">'.$no2++.'</td>
                <td class="text_tengah">'.$kode_akun.'</td>
                <td style="text-transform: uppercase;"><a target="_blank" href="' . $url_pemda . '?&kode_akun=' . $kode_akun . '">' . $data['nama_akun'] . '</a></td>
                <td class="text_kanan">'.number_format($data['pagu_murni'], 0, '.', ',').'</td>
                <td class="text_kanan">'.number_format($data['total_rincian'], 0, '.', ',').'</td>
                <td class="text_kanan">'.number_format($data['pagu_efisiensi'], 0, '.', ',').'</td>
                <td class="text_kanan">'.number_format($selisih_rek, 0, '.', ',').'</td>
                <td class="text_kanan">'.number_format($data['realisasi'], 0, '.', ',').'</td>
            </tr>';

        $total_all_rek_pagu += $data['total_rincian'];
        $total_all_rek_pagu_sebelum += $data['pagu_murni'];
        $total_all_rek_efisiensi += $data['pagu_efisiensi'];
        $total_all_rek_realisasi += $data['realisasi'];
        $total_all_rek_selisih += $selisih_rek;
    }
}

?>
<style type="text/css">
	.wrap-table {
		overflow: auto;
		max-height: 100vh;
		width: 100%;
	}

	.btn-action-group {
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.btn-action-group .btn {
		margin: 0 5px;
	}
	.table_dokumen_skpd thead{
		position: sticky;
        top: -6px;
	}.table_dokumen_skpd thead th{
		vertical-align: middle;
	}
	.table_dokumen_skpd tfoot{
        position: sticky;
        bottom: 0;
    }

	.table_dokumen_skpd tfoot th{
		vertical-align: middle;
	}
	.table_rekening_belanja thead{
		position: sticky;
        top: -6px;
	}.table_rekening_belanja thead th{
		vertical-align: middle;
	}
	.table_rekening_belanja tfoot{
        position: sticky;
        bottom: 0;
    }

	.table_rekening_belanja tfoot th{
		vertical-align: middle;
	}
</style>
<div class="container-md">
	<div class="cetak">
		<div style="padding: 10px;margin:0 0 3rem 0;">
			<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
			<h1 class="text-center table-title">Efisiensi Belanja SKPD</br>Tahun Anggaran <?php echo $tahun_anggaran; ?></h1>
			<div id="action" class="action-section hide-excel"></div>
			<div class="wrap-table">
				<table id="cetak" title="Rekapitulasi Efisiensi Belanja SKPD" class="table table-bordered table_dokumen_skpd">
					<thead style="background: #ffc491;">
						<tr>
							<th class="text-center" width="800px">SKPD</th>
							<th class="text-center" width="100px">PAGU SEBELUM</th>
							<th class="text-center" width="100px">PAGU</th>
							<th class="text-center" width="100px">PAGU EFISIENSI</th>
							<th class="text-center" width="100px">SELISIH</th>
							<th class="text-center" width="100px">REALISASI</th>
						</tr>
					</thead>
					<tbody>
						<?php echo $tbody; ?>
					</tbody>
					<tfoot style="background: #ffc491;">
						<tr>
							<th class="text-center">Jumlah</th>
							<th class="text-right" id="total_pagu_murni"><?php echo number_format($total_all_murni, 0, ",", ".")?></th>
							<th class="text-right" id="total_pagu"><?php echo number_format($total_all, 0, ",", ".")?></th>
							<th class="text-right" id="total_pagu_efisiensi"><?php echo number_format($total_all_efisiensi, 0, ",", ".")?></th>
							<th class="text-right" id="total_pagu_selisih"><?php echo number_format($total_all_selisih, 0, ",", ".")?></th>
							<th class="text-right" id="total_pagu_realisasi"><?php echo number_format($total_all_realisasi, 0, ",", ".")?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="container-md">
	<div class="cetak">
		<div style="padding: 10px;margin:0 0 3rem 0;">
			<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
			<h1 class="text-center table-title">Rekening Belanja</br>Tahun Anggaran <?php echo $tahun_anggaran; ?></h1>
			<div id="action" class="action-section hide-excel"></div>
			<div class="wrap-table">
				<table id="cetak" title="Rekening Belanja" class="table table-bordered table_rekening_belanja">
					<thead style="background: #ffc491;">
						<tr>
			                <th style="width: 35px;" class="text-center">No</th>
			                <th style="width: 110px;" class="text-center">Kode Rekening</th>
			                <th style="width: 200px;" class="text-center">Nama Rekening</th>
			                <th style="width: 200px;" class="text-center">Total Sebelum</th>
			                <th style="width: 200px;" class="text-center">Total</th>
			                <th style="width: 200px;" class="text-center">Efisiensi</th>
			                <th style="width: 200px;" class="text-center">Selisih</th>
			                <th style="width: 200px;" class="text-center">Realisasi</th>
						</tr>
					</thead>
					<tbody>
						<?php echo $tbody2; ?>
					</tbody>
					<tfoot style="background: #ffc491;">
						<tr>
			                <th colspan="3" class="text-center">Total</th>
			                <th class="text-right"><?php echo number_format($total_all_rek_pagu_sebelum, 0, '.', ','); ?></th>
			                <th class="text-right"><?php echo number_format($total_all_rek_pagu, 0, '.', ','); ?></th>
			                <th class="text-right"><?php echo number_format($total_all_rek_efisiensi, 0, '.', ','); ?></th>
			                <th class="text-right"><?php echo number_format($total_all_rek_selisih, 0, '.', ','); ?></th>
			                <th class="text-right"><?php echo number_format($total_all_rek_realisasi, 0, '.', ','); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function() {
		jQuery('.table_dokumen_skpd').dataTable({
			 aLengthMenu: [
		        [5, 10, 25, 100, -1],
		        [5, 10, 25, 100, "All"]
		    ],
		    iDisplayLength: -1
		});
		jQuery('.table_rekening_belanja').dataTable({
			 aLengthMenu: [
		        [5, 10, 25, 100, -1],
		        [5, 10, 25, 100, "All"]
		    ],
		    iDisplayLength: -1
		});
	});
</script>