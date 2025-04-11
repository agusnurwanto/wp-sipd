<?php
global $wpdb;

if (!defined('WPINC')) {
	die;
}

$input = shortcode_atts(array(
	'tahun_anggaran' => '2022'
), $atts);

$tahun_anggaran = $input['tahun_anggaran'];

$sumber_pagu = '1';
$api_key = get_option('_crb_api_key_extension');
$user_id = um_user('ID');
$user_meta = get_userdata($user_id);

$roles = $this->role_verifikator();

$is_admin = false;
if (in_array("administrator", $user_meta->roles)) {
	$is_admin = true;
}
$is_pptk = false;
if (in_array("pptk", $user_meta->roles) || 
	in_array("verifikator_bappeda", $user_meta->roles) || 
	in_array("verifikator_bppkad", $user_meta->roles) || 
	in_array("verifikator_pbj", $user_meta->roles) ||
	in_array("verifikator_adbang", $user_meta->roles) || 
	in_array("verifikator_inspektorat", $user_meta->roles) || 
	in_array("verifikator_pupr", $user_meta->roles)) {
	$is_pptk = true;
}

$is_verifikator = false;
foreach ($roles as $role) {
	if (in_array($role, $user_meta->roles)) {
		$is_verifikator = true;
	}
}

$tahun_asli = date('Y');
$bulan_asli = date('m');
if (!empty($_GET) && !empty($_GET['bulan'])) {
	$bulan = $_GET['bulan'];
} else if ($input['tahun_anggaran'] < $tahun_asli) {
	$bulan = 12;
} else {
	$bulan = $bulan_asli;
}

$nama_bulan = $this->get_bulan($bulan);

$current_user = wp_get_current_user();
$role_verifikator = $this->role_verifikator();
$cek_verifiktor = false;
foreach ($current_user->roles as $role) {
	if (in_array($role, $role_verifikator)) {
		$cek_verifiktor = true;
	}
}
$unit = $wpdb->get_results(
	$wpdb->prepare("
	SELECT 
		*	
	FROM data_unit 
	WHERE active=1 
	  AND tahun_anggaran=%d
	  AND is_skpd=1 
	ORDER BY kode_skpd ASC
	", $tahun_anggaran),
	ARRAY_A
);

if (!empty($unit)) {
	$tbody = '';
	$total_all = 0;
	$total_all_murni = 0;
	$total_all_efisiensi = 0;
	$total_all_selisih = 0;
	$list_skpd = array();

	foreach ($unit as $kk => $vv) {
		$subkeg = $wpdb->get_results($wpdb->prepare("
			select 
				k.*
			from data_sub_keg_bl k
			where k.tahun_anggaran=%d
				and k.active=1
				and k.id_sub_skpd=%d
			order by k.kode_sub_giat ASC
		", $input['tahun_anggaran'], $vv['id_skpd']), ARRAY_A);
		
		// echo $wpdb->last_query.'<br>';die();
		$efisiensi_belanja = $wpdb->get_results($wpdb->prepare("
			select 
				e.*
			from data_efisiensi_belanja e
			where e.tahun_anggaran=%d
				and e.active=1
				and e.id_skpd=%d
		", $input['tahun_anggaran'], $vv['id_skpd']), ARRAY_A);

		$data_all = array(
			'total' => 0,
			'total_efisiensi' => 0,
			'total_murni' => 0,
			'data' => array()
		);

		foreach ($subkeg as $kkk => $sub) {
			$data_all['total'] += $sub['pagu'];
			$data_all['total_murni'] += $sub['pagumurni'];
		}
		foreach ($efisiensi_belanja as $kkkk => $efisiensi) {
			$data_all['total_efisiensi'] += $efisiensi['pagu_efisiensi'];
		}
		$list_skpd[$kk] = array(
			'id_skpd' => $vv['id_skpd'],
			'nama_skpd' => $vv['nama_skpd']
		);
		$selisih = $data_all['total'] - $data_all['total_efisiensi'];
		$title = 'Detail Efisiensi Belanja | ' . $tahun_anggaran;
		$shortcode = '[detail_efisiensi_belanja tahun_anggaran="' . $tahun_anggaran . '"]';
		$update = false;
		$url_skpd = $this->generatePage($title, $tahun_anggaran, $shortcode, $update);
		$tbody .= "<tr>";
		$tbody .= "<td style='text-transform: uppercase;'><a target='_blank' href='" . $url_skpd . "&id_skpd=" . $vv['id_skpd'] . "'>" . $vv['kode_skpd'] . " " . $vv['nama_skpd'] . "</a></td>";
		$tbody .= "<td class='text-right'>" . number_format($data_all['total_murni'], 0, ",", ".") . "</td>";
		$tbody .= "<td class='text-right'>" . number_format($data_all['total'], 0, ",", ".") . "</td>";
		$tbody .= "<td class='text-right'>" . number_format($data_all['total_efisiensi'], 0, ",", ".") . "</td>";
		$tbody .= "<td class='text-right'>" . number_format($selisih, 0, ",", ".") . "</td>";
		$tbody .= "</tr>";

		$total_all += $data_all['total'];
		$total_all_murni += $data_all['total_murni'];
		$total_all_efisiensi += $data_all['total_efisiensi'];
		$total_all_selisih += $selisih;
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
</style>
<div class="container-md">
	<div class="cetak">
		<div style="padding: 10px;margin:0 0 3rem 0;">
			<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
			<h1 class="text-center table-title">Efisiensi Belanja</br>Tahun Anggaran <?php echo $tahun_anggaran; ?></h1>
			<div id="action" class="action-section hide-excel"></div>
			<div class="wrap-table">
				<table id="cetak" title="Rekapitulasi Efisiensi Belanja" class="table table-bordered table_dokumen_skpd">
					<thead style="background: #ffc491;">
						<tr>
							<th class="text-center" width="800px">SKPD</th>
							<th class="text-center" width="100px">PAGU SEBELUM</th>
							<th class="text-center" width="100px">PAGU</th>
							<th class="text-center" width="100px">PAGU EFISIENSI</th>
							<th class="text-center" width="100px">SELISIH</th>
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
							<th class="text-right" id="total_pagu_efisiensi"><?php echo number_format($total_all_selisih, 0, ",", ".")?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal" data-backdrop="static"  role="dialog" aria-labelledby="modal-label" aria-hidden="true">
  	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
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

<script>
	jQuery(document).ready(function() {
		jQuery('.table_dokumen_skpd').dataTable({
			 aLengthMenu: [
		        [5, 10, 25, 100, -1],
		        [5, 10, 25, 100, "All"]
		    ],
		    iDisplayLength: -1
		});
	});
</script>