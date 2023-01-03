<?php
global $wpdb;
$input = shortcode_atts( array(
	'type' => 'program'
), $atts );
if($input['type'] == 'program'){
	$program_mapping = $this->get_fmis_mapping(array(
		'name' => '_crb_custom_mapping_program_fmis',
		true
	));
	$body_program = '';
	$no = 0;
	foreach($program_mapping as $prog_sipd => $prog_fmis){
		$no++;
		$data = $wpdb->get_row("
			SELECT
				*
			FROM data_prog_keg
			WHERE nama_program LIKE '%$prog_sipd'
			ORDER BY tahun_anggaran DESC
			LIMIT 1
		");
		$bidur = '';
		$kode_program = '';
		if(!empty($data)){
			$bidur = $data->nama_bidang_urusan;
			$kode_program = $data->kode_program;
		}
		$class_tr = 'btn-danger';
		$status = 'Belum dimapping';
		if($prog_sipd != $prog_fmis){
			$class_tr = 'btn-success';
			$status = 'Sudah dimapping';
		}
		$body_program .= '
			<tr class="'.$class_tr.'">
				<td class="text-center">'.$no.'</td>
				<td>'.$bidur.'</td>
				<td class="text-center">'.$kode_program.'</td>
				<td>'.$prog_sipd.'</td>
				<td>'.$prog_fmis.'</td>
				<td class="text-center">'.$status.'</td>
			</tr>
		';
	}
}else if($input['type'] == 'kegiatan'){
	$keg_mapping = $this->get_fmis_mapping(array(
		'name' => '_crb_custom_mapping_keg_fmis',
		true
	));

	$body_kegiatan = '';
	$no = 0;
	foreach($keg_mapping as $keg_sipd => $keg_fmis){
		$no++;
		$data = $wpdb->get_row("
			SELECT
				*
			FROM data_prog_keg
			WHERE nama_giat LIKE '%$keg_sipd'
			ORDER BY tahun_anggaran DESC
			LIMIT 1
		");
		$bidur = '';
		$program = '';
		$kode_keg = '';
		if(!empty($data)){
			$bidur = $data->nama_bidang_urusan;
			$program = $data->nama_program;
			$kode_keg = $data->kode_giat;
		}
		$class_tr = 'btn-danger';
		$status = 'Belum dimapping';
		if($keg_sipd != $keg_fmis){
			$class_tr = 'btn-success';
			$status = 'Sudah dimapping';
		}
		$body_kegiatan .= '
			<tr class="'.$class_tr.'">
				<td class="text-center">'.$no.'</td>
				<td>'.$bidur.'</td>
				<td>'.$program.'</td>
				<td class="text-center">'.$kode_keg.'</td>
				<td>'.$keg_sipd.'</td>
				<td>'.$keg_fmis.'</td>
				<td class="text-center">'.$status.'</td>
			</tr>
		';
	}
}else if($input['type'] == 'sub_kegiatan'){
	$subkeg_mapping = $this->get_fmis_mapping(array(
		'name' => '_crb_custom_mapping_subkeg_fmis',
		true
	));

	$body_sub_kegiatan = '';
	$no = 0;
	foreach($subkeg_mapping as $sub_keg_sipd => $sub_keg_fmis){
		$no++;
		$data = $wpdb->get_row("
			SELECT
				*
			FROM data_prog_keg
			WHERE nama_sub_giat LIKE '%$sub_keg_sipd'
			ORDER BY tahun_anggaran DESC
			LIMIT 1
		");
		$bidur = '';
		$program = '';
		$keg = '';
		$kode_sub_keg = '';
		if(!empty($data)){
			$bidur = $data->nama_bidang_urusan;
			$program = $data->nama_program;
			$keg = $data->nama_giat;
			$kode_sub_keg = $data->kode_sub_giat;
		}
		$class_tr = 'btn-danger';
		$status = 'Belum dimapping';
		if($sub_keg_sipd != $sub_keg_fmis){
			$class_tr = 'btn-success';
			$status = 'Sudah dimapping';
		}
		$body_sub_kegiatan .= '
			<tr class="'.$class_tr.'">
				<td class="text-center">'.$no.'</td>
				<td>'.$bidur.'</td>
				<td>'.$program.'</td>
				<td>'.$keg.'</td>
				<td class="text-center">'.$kode_sub_keg.'</td>
				<td>'.$sub_keg_sipd.'</td>
				<td>'.$sub_keg_fmis.'</td>
				<td class="text-center">'.$status.'</td>
			</tr>
		';
	}
}else if($input['type'] == 'rekening'){
	$rek_mapping = $this->get_fmis_mapping(array(
		'name' => '_crb_custom_mapping_rekening_fmis',
		true
	));
}
?>
<div class="cetak">
	<div style="padding: 10px;margin:0 0 3rem 0;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<h1 class="text-center" style="margin:3rem;">Data Mapping Data Master FMIS</h1>
	<?php if($input['type'] == 'program'): ?>
		<h2 class="text-center" style="margin:3rem;">Mapping Program</h2>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">No</th>
					<th class="text-center">Bidang Urusan</th>
					<th class="text-center">Kode Program</th>
					<th class="text-center">Program SIPD</th>
					<th class="text-center">Program FMIS</th>
					<th class="text-center">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $body_program; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<?php if($input['type'] == 'kegiatan'): ?>
		<h2 class="text-center" style="margin:3rem;">Mapping Kegiatan</h2>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">No</th>
					<th class="text-center">Bidang Urusan</th>
					<th class="text-center">Program</th>
					<th class="text-center">Kode Kegiatan</th>
					<th class="text-center">Kegiatan SIPD</th>
					<th class="text-center">Kegiatan FMIS</th>
					<th class="text-center">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $body_kegiatan; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<?php if($input['type'] == 'sub_kegiatan'): ?>
		<h2 class="text-center" style="margin:3rem;">Mapping Sub Kegiatan</h2>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">No</th>
					<th class="text-center">Bidang Urusan</th>
					<th class="text-center">Program</th>
					<th class="text-center">Kegiatan</th>
					<th class="text-center">Kode Sub Kegiatan</th>
					<th class="text-center">Sub Kegiatan SIPD</th>
					<th class="text-center">Sub Kegiatan FMIS</th>
					<th class="text-center">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $body_sub_kegiatan; ?>
			</tbody>
		</table>
	<?php endif; ?>
	</div>
</div>