<?php
global $wpdb;
$input = shortcode_atts( array(
	'type' => 'program'
), $atts );
if($input['type'] == 'program'){
	$program_mapping = $this->get_fmis_mapping(array(
		'name' => '_crb_custom_mapping_program_fmis'
	), true);
	$body_program = '';
	$no = 0;
	foreach($program_mapping as $prog_sipd => $prog_fmis){
		$no++;
		$data = $wpdb->get_row("
			SELECT
				*
			FROM data_prog_keg
			WHERE nama_program LIKE '%".$this->removeNewline($prog_sipd)."'
			ORDER BY tahun_anggaran DESC
			LIMIT 1
		");
		$class_tr = 'btn-danger';
		$bidur = '';
		$kode_program = '';
		if(!empty($data)){
			$bidur = $data->nama_bidang_urusan;
			$kode_program = $data->kode_program;
			$class_tr = 'btn-warning';
		}
		$status = 'Belum dimapping';
		if($prog_sipd != $prog_fmis){
			$class_tr = 'btn-success';
			$status = 'Sudah dimapping';
		}
		$textarea_data = $prog_sipd;
		if($this->removeNewline($prog_sipd) != $prog_sipd){
			$textarea_data = '<textarea>'.$prog_sipd.'</textarea>';
		}
		$body_program .= '
			<tr class="'.$class_tr.'">
				<td class="text-center">'.$no.'</td>
				<td>'.$bidur.'</td>
				<td class="text-center">'.$kode_program.'</td>
				<td>'.$textarea_data.'</td>
				<td>'.$prog_fmis.'</td>
				<td class="text-center">'.$status.'</td>
			</tr>
		';
	}
}else if($input['type'] == 'kegiatan'){
	$keg_mapping = $this->get_fmis_mapping(array(
		'name' => '_crb_custom_mapping_keg_fmis'
	), true);

	$body_kegiatan = '';
	$no = 0;
	foreach($keg_mapping as $keg_sipd => $keg_fmis){
		$no++;
		$data = $wpdb->get_row("
			SELECT
				*
			FROM data_prog_keg
			WHERE nama_giat LIKE '%".$this->removeNewline($keg_sipd)."'
			ORDER BY tahun_anggaran DESC
			LIMIT 1
		");
		$bidur = '';
		$program = '';
		$class_tr = 'btn-danger';
		$kode_keg = '';
		if(!empty($data)){
			$bidur = $data->nama_bidang_urusan;
			$program = $data->nama_program;
			$kode_keg = $data->kode_giat;
			$class_tr = 'btn-warning';
		}
		$status = 'Belum dimapping';
		if($keg_sipd != $keg_fmis){
			$class_tr = 'btn-success';
			$status = 'Sudah dimapping';
		}
		$textarea_data = $keg_sipd;
		if($this->removeNewline($keg_sipd) != $keg_sipd){
			$textarea_data = '<textarea>'.$keg_sipd.'</textarea>';
		}
		$body_kegiatan .= '
			<tr class="'.$class_tr.'">
				<td class="text-center">'.$no.'</td>
				<td>'.$bidur.'</td>
				<td>'.$program.'</td>
				<td class="text-center">'.$kode_keg.'</td>
				<td>'.$textarea_data.'</td>
				<td>'.$keg_fmis.'</td>
				<td class="text-center">'.$status.'</td>
			</tr>
		';
	}
}else if($input['type'] == 'sub_kegiatan'){
	$subkeg_mapping = $this->get_fmis_mapping(array(
		'name' => '_crb_custom_mapping_subkeg_fmis'
	), true);

	$body_sub_kegiatan = '';
	$no = 0;
	foreach($subkeg_mapping as $sub_keg_sipd => $sub_keg_fmis){
		$no++;
		$data = $wpdb->get_row("
			SELECT
				*
			FROM data_prog_keg
			WHERE nama_sub_giat LIKE '%".$this->removeNewline($sub_keg_sipd)."'
			ORDER BY tahun_anggaran DESC
			LIMIT 1
		");
		$bidur = '';
		$program = '';
		$keg = '';
		$kode_sub_keg = '';
		$class_tr = 'btn-danger';
		if(!empty($data)){
			$bidur = $data->nama_bidang_urusan;
			$program = $data->nama_program;
			$keg = $data->nama_giat;
			$kode_sub_keg = $data->kode_sub_giat;
			$class_tr = 'btn-warning';
		}
		$status = 'Belum dimapping';
		if($sub_keg_sipd != $sub_keg_fmis){
			$class_tr = 'btn-success';
			$status = 'Sudah dimapping';
		}
		$textarea_data = $sub_keg_sipd;
		if($this->removeNewline($sub_keg_sipd) != $sub_keg_sipd){
			$textarea_data = '<textarea>'.$sub_keg_sipd.'</textarea>';
		}
		$body_sub_kegiatan .= '
			<tr class="'.$class_tr.'">
				<td class="text-center">'.$no.'</td>
				<td>'.$bidur.'</td>
				<td>'.$program.'</td>
				<td>'.$keg.'</td>
				<td class="text-center">'.$kode_sub_keg.'</td>
				<td>'.$textarea_data.'</td>
				<td>'.$sub_keg_fmis.'</td>
				<td class="text-center">'.$status.'</td>
			</tr>
		';
	}
}else if($input['type'] == 'rekening'){
	$rek_mapping = $this->get_fmis_mapping(array(
		'name' => '_crb_custom_mapping_rekening_fmis'
	), true);
	$body_rek = '';
	$no = 0;
	foreach($rek_mapping as $rek_sipd => $rek_fmis){
		$no++;
		$_kode_akun = explode('.', $rek_sipd);
		if(!empty($_kode_akun[2])){
			$_kode_akun[2] = $this->simda->CekNull($_kode_akun[2]);
		}
		if(!empty($_kode_akun[3])){
			$_kode_akun[3] = $this->simda->CekNull($_kode_akun[3]);
		}
		if(!empty($_kode_akun[4])){
			$_kode_akun[4] = $this->simda->CekNull($_kode_akun[4]);
		}
		if(!empty($_kode_akun[5])){
			$_kode_akun[5] = $this->simda->CekNull($_kode_akun[5], 4);
		}
		$rek_sipd_format = implode('.', $_kode_akun);
		$data = $wpdb->get_row("
			SELECT
				*
			FROM data_akun
			WHERE kode_akun LIKE '$rek_sipd_format'
			ORDER BY tahun_anggaran DESC
			LIMIT 1
		");
		$class_tr = 'btn-danger';
		$bidur = '';
		$nama_rek = '';
		if(!empty($data)){
			$nama_rek = $data->nama_akun;
			$class_tr = 'btn-warning';
		}
		$status = 'Belum dimapping';
		if($rek_sipd != $rek_fmis){
			$class_tr = 'btn-success';
			$status = 'Sudah dimapping';
		}
		$body_rek .= '
			<tr class="'.$class_tr.'">
				<td class="text-center">'.$no.'</td>
				<td>'.$nama_rek.'</td>
				<td class="text-center">'.$rek_sipd.'</td>
				<td class="text-center">'.$rek_fmis.'</td>
				<td class="text-center">'.$status.'</td>
			</tr>
		';
	}
}
?>
<div class="cetak">
	<div style="padding: 10px;margin:0 0 3rem 0;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<h1 class="text-center" style="margin:3rem;">Data Mapping Data Master FMIS</h1>
		<h3>Keterangan:</h3>
		<ol>
			<li>Baris berwarna <b>kuning</b> menandakan bahwa data <b>belum dimapping</b></li>
			<li>Baris berwarna <b>hijau</b> menandakan bahwa data <b>sudah dimapping</b></li>
			<li>Baris berwarna <b>merah</b> menandakan bahwa data <b>tidak ditemukan di master data WP-SIPD tabel data_prog_keg</b></li>
		</ol>
	<?php if($input['type'] == 'program'): ?>
		<h2 class="text-center" style="margin:3rem;">Mapping Program</h2>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">No</th>
					<th class="text-center">Bidang Urusan</th>
					<th class="text-center">Kode Program</th>
					<th class="text-center" style="min-width: 350px;">Program SIPD</th>
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
					<th class="text-center" style="min-width: 350px;">Kegiatan SIPD</th>
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
					<th class="text-center" style="min-width: 350px;">Sub Kegiatan SIPD</th>
					<th class="text-center">Sub Kegiatan FMIS</th>
					<th class="text-center">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $body_sub_kegiatan; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<?php if($input['type'] == 'rekening'): ?>
		<h2 class="text-center" style="margin:3rem;">Mapping Rekening</h2>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">No</th>
					<th>Nama Rekening</th>
					<th class="text-center">Kode Rekening SIPD</th>
					<th class="text-center">Kode Rekening FMIS</th>
					<th class="text-center">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $body_rek; ?>
			</tbody>
		</table>
	<?php endif; ?>
	</div>
</div>