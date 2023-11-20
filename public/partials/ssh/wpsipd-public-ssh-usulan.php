<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$input = shortcode_atts( array(
	'tahun_anggaran' => '2022'
), $atts );

global $wpdb;

$title = 'Cetak Usulan Standar Harga';
$shortcode = '[cetak_usulan_standar_harga]';
$url_cetak_usulan = $this->generatePage($title, false, $shortcode, false);

$all_skpd = array();
$list_skpd_options = '<option value="">Pilih Perangkat Daerah</option>';
$nama_skpd = "";
$is_admin = false;
if(
	in_array("PA", $user_meta->roles)
	|| in_array("KPA", $user_meta->roles)
	|| in_array("PLT", $user_meta->roles)
){
	$nipkepala = get_user_meta($user_id, '_nip');
	$skpd_db = $wpdb->get_results($wpdb->prepare("
		SELECT 
			nama_skpd, 
			id_skpd, 
			kode_skpd,
			is_skpd
		from data_unit 
		where nipkepala=%s 
			and tahun_anggaran=%d
		group by id_skpd", $nipkepala[0], $input['tahun_anggaran']), ARRAY_A);
	foreach ($skpd_db as $skpd) {
		$nama_skpd = '<br>'.$skpd['kode_skpd'].' '.$skpd['nama_skpd'];
		$all_skpd[] = $skpd;
		$list_skpd_options .= '<option value="'.$skpd['id_skpd'].'">'.$skpd['kode_skpd'].' '.$skpd['nama_skpd'].'</option>';
		if($skpd['is_skpd'] == 1){
			$sub_skpd_db = $wpdb->get_results($wpdb->prepare("
				SELECT 
					nama_skpd, 
					id_skpd, 
					kode_skpd,
					is_skpd
				from data_unit 
				where id_unit=%d 
					and tahun_anggaran=%d
					and is_skpd=0
				group by id_skpd", $skpd['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
			foreach ($sub_skpd_db as $sub_skpd) {
				$all_skpd[] = $sub_skpd;
				$list_skpd_options .= '<option value="'.$sub_skpd['id_skpd'].'">-- '.$sub_skpd['kode_skpd'].' '.$sub_skpd['nama_skpd'].'</option>';
			}
		}
	}
	$input['id_skpd'] = $skpd_db[0]['id_skpd'];
}else if(
	in_array("administrator", $user_meta->roles)
	|| in_array("tapd_keu", $user_meta->roles)
){
	$is_admin = true;
	$skpd_mitra = $wpdb->get_results($wpdb->prepare("
		SELECT 
			nama_skpd, 
			id_skpd, 
			kode_skpd,
			is_skpd 
		from data_unit 
		where active=1 
			and tahun_anggaran=%d
		group by id_skpd
		order by id_unit ASC, kode_skpd ASC", $input['tahun_anggaran']), ARRAY_A);
	foreach ($skpd_mitra as $k => $v) {
		$all_skpd[] = $v;
		if($v['is_skpd'] == 1){
			$list_skpd_options .= '<option value="'.$v['id_skpd'].'">'.$v['kode_skpd'].' '.$v['nama_skpd'].'</option>';
		}else{
			$list_skpd_options .= '<option value="'.$v['id_skpd'].'">-- '.$v['kode_skpd'].' '.$v['nama_skpd'].'</option>';
		}
	}
}else{
	echo "<h1>Anda tidak mempunyai akses untuk melihat halaman ini!</h1>";
}

$nama_skpd .= "<br>".get_option('_crb_daerah');
echo $this->menu_ssh($input);
?>

<style type="text/css">
	.warning {
		background: #f1a4a4;
	}
	.hide {
		display: none;
	}
	.akun-ssh-desc table, .akun-ssh-desc tr, .akun-ssh-desc td{
		border: none;
	}
	.akun-ssh-desc .first-desc{
		width: 11rem;
	}
	.akun-ssh-desc .sec-desc{
		width: 1rem;
	}
	ul.td-aksi {
	    margin: 0;
	    width: 45px;
	    padding: 0;
	}
	ul.td-aksi li {
	    list-style: none;
	    display: inline-block;
	}
	.kol-keterangan{
		max-width: 300px;
	}
	td.kol-keterangan{
		padding: 0!important;
	}
	td.in-kol-keterangan{
		max-width: 300px;
	}
	.medium-bold{
		font-weight: 600;
		color: #50575e;
	}
	.medium-bold-2{
		font-weight: 600;
		color: #212529;
	}
	ul.keterangan{
		margin-bottom: 0!important;
	}
	td.spek-satuan{
		padding: 0!important;
	}

	td.show_status{
		padding: 0!important;
	}
	td.show-komponen{
		padding: 0!important;
	}
	.toolbar {
		float: left;
	}
	.bulk-action {
		padding: .45rem;
		border-color: #eaeaea;
		vertical-align: middle;
	}
	.verify-ssh{
		margin-right: .5rem;
	}
	ul.td-aksi a {
		text-decoration: none !important;
	}
	.required {
		font-size: 12px;color: red;
	}
	button[disabled], input[disabled], textarea[disabled] {
	    background: #dfdfdf;
	}
	#toolbar_ssh_usulan {
		margin: 0;
	}
	#toolbar_ssh_usulan li{
		list-style: none;
		display: inline-block;
		margin-left: 10px;
		margin-top: 5px;
	}
</style>
<div class="cetak">
	<div style="padding: 10px;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
		<h1 class="text-center">Usulan Standar Harga<?php echo $nama_skpd; ?><br>Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h1>
		<div>
			<h2 class="text-center">Daftar Nota Dinas</h2>
		<?php if(in_array("administrator", $user_meta->roles)): ?>
			<div style="margin-bottom: 25px;">
				<button class="btn btn-primary" onclick="tambah_nota_dinas(<?php echo $input['tahun_anggaran']; ?>);"><i class="dashicons dashicons-plus"></i> Tambah Nota Dinas</button>
			</div>
		<?php endif; ?>
			<table id="surat_nota_dinas_usulan_ssh_table" class="table table-bordered">
				<thead>
					<tr>
						<th class="text-center">Waktu Update</th>
						<th class="text-center">Nomor Surat</th>
						<th class="text-center">Jumlah Usulan</th>
						<th class="text-center">Catatan</th>
						<th class="text-center" style="width: 200px;">Aksi</th>
					</tr>
				</thead>
				<tbody id="data_body_surat_nota_dinas" class="data_body_ssh_surat_nota_dinas">
				</tbody>
			</table>
		</div>
		<div>
			<h2 class="text-center">Daftar Surat Usulan</h2>
			<table id="surat_usulan_ssh_table" class="table table-bordered">
				<thead>
					<tr>
						<th class="text-center">Waktu Update</th>
						<th class="text-center">Dibuat Oleh</th>
						<th class="text-center">Nomor Surat</th>
						<th class="text-center">File</th>
						<th class="text-center">Jumlah</th>
						<th class="text-center">Dasar Pengusulan</th>
						<th class="text-center">Catatan</th>
						<th class="text-center">Catatan Verifikator</th>
						<th class="text-center" style="width: 65px;">Aksi</th>
					</tr>
				</thead>
				<tbody id="data_body_surat" class="data_body_ssh_surat">
				</tbody>
			</table>
		</div>
		<div>
			<h2 class="text-center">Daftar Usulan</h2>
			<div style="margin-bottom: 25px;">
			<?php if(false == $is_admin): ?>
				<button class="btn btn-primary tambah_ssh" disabled onclick="tambah_new_ssh(<?php echo $input['tahun_anggaran']; ?>);"><i class="dashicons dashicons-plus"></i> Tambah Item SSH</button>
				<button class="btn btn-primary tambah_new_ssh" disabled onclick="get_data_by_name_komponen_ssh('harga',<?php echo $input['tahun_anggaran']; ?>)"><i class="dashicons dashicons-plus"></i> Tambah Harga SSH</button>
				<button class="btn btn-primary tambah_new_ssh" disabled onclick="get_data_by_name_komponen_ssh('akun',<?php echo $input['tahun_anggaran']; ?>)"><i class="dashicons dashicons-plus"></i> Tambah Akun SSH</button>
				<button class="btn btn-warning" onclick="buat_surat_usulan(<?php echo $input['tahun_anggaran']; ?>)"><i class="dashicons dashicons-welcome-add-page"></i> Buat Surat Usulan</button>
			<?php endif; ?>
				<button class="btn btn-success" onclick="cetak_usulan()"><i class="dashicons dashicons-edit"></i> Cetak/Print Laporan</button>
			</div>
			<table id="usulan_ssh_table" class="table table-bordered" style="font-size:90%">
				<thead id="data_header">
					<tr>
						<th class="text-center"><input type="checkbox" id="checkall"></th>
						<th class="text-center">Kode Komponen</th>
						<th class="text-center">Uraian Komponen</th>
						<th class="text-center">Spesifikasi Satuan</th>
						<th class="text-center">Harga Satuan</th>
						<th class="text-center">Keterangan</th>
						<th class="text-center">Lampiran</th>
						<th class="text-center">Verifikator 1</th>
						<th class="text-center">Verifikator 2</th>
						<th class="text-center">Status</th>
						<th class="text-right" style="width: 100px">Aksi</th>
					</tr>
				</thead>
				<tbody id="data_body" class="data_body_ssh">
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal fade" id="tambahNotaDinasModal" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Form Nota Dinas</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row form-group">
					<label class="col-md-2" for="nomor_surat_nota_dinas">Nomor Surat</label>
					<div class="col-md-10">
						<input type="text" id="nomor_surat_nota_dinas" class="form-control" placeholder="kode_surat/no_urut/kode_opd/tahun" value="kode_surat/no_urut/kode_opd/<?php echo $input['tahun_anggaran']; ?>">
						<input type="hidden" id="ids_nota_dinas">
						<input type="hidden" name="ubah_id" value="">
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-2" for="catatan_surat_nota_dinas">Catatan</label>
					<div class="col-md-10">
						<textarea type="text" id="catatan_surat_nota_dinas" class="form-control"></textarea>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-12">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Uraian Kelompok</th>
									<th>Nama Komponen</th>
									<th>Spesifikasi</th>
									<th>Harga</th>
									<th>Rekening</th>
									<th>Nomor Surat SKPD</th>
									<th>Jenis Usulan</th>
								</tr>
							</thead>
							<tbody id="tbody_data_usulan_nota_dinas"></tbody>
						</table>
					</div>
				</div>
			</div> 
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="submitNotaDinas(<?php echo $input['tahun_anggaran']; ?>)">Simpan</button>
				<button type="button" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="tambahSuratUsulan" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Form Surat Usulan</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row form-group">
					<label class="col-md-2" for="surat_skpd">Perangkat Daerah</label>
					<div class="col-md-10">
						<select id="surat_skpd" class="form-control"><?php echo $list_skpd_options; ?></select>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-2" for="nomor_surat">Nomor Surat</label>
					<div class="col-md-10">
						<input type="text" id="nomor_surat" class="form-control" placeholder="kode_surat/no_urut/kode_opd/tahun" value="kode_surat/no_urut/kode_opd/<?php echo $input['tahun_anggaran']; ?>">
						<input type="hidden" id="ids_surat_usulan">
						<input type="hidden" name="ubah_id" value="">
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-2" for="catatan_surat">Catatan</label>
					<div class="col-md-10">
						<textarea type="text" id="catatan_surat" class="form-control"></textarea>
					</div>
				</div>
				<div class="row form-group" style="display: none;">
					<label class="col-md-2" for="catatan_verifikator">Catatan Verifikator</label>
					<div class="col-md-10">
						<textarea type="text" id="catatan_verifikator" class="form-control"></textarea>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-2" for="Acuan penyusunan SSH">Acuan Penyusunan SSH</label>
					<div class="col-md-10">
						<input class="type-sumber-ssh" id="jenis_survey" name="jenis[]" value="1" type="checkbox" >&nbsp;<label for="jenis_survey">Survey harga pasar yang telah kami lakukan secara mandiri.</label></br>
						<input class="type-sumber-ssh" id="jenis_juknis" name="jenis[]" value="2" type="checkbox">&nbsp;<label for="jenis_juknis">Petunjuk Teknis yang kami terima dari kementrian/provinsi.</label></br>
						<small style='color:red'>* Pilih salah satu atau keduanya. </small>
					</div>
				</div>
				<div class="row form-group" style="display: none;">
					<label for='u_surat_usulan_ssh' class="col-md-2">Soft File Surat Usulan SSH</label>
					<div class="col-md-10">
						<input type='file' id='u_surat_usulan_ssh' accept="image/png, image/jpeg, image/jpg, application/pdf" style='display:block;width:100%;' onchange="checkFileType(this)">
						<small>Tipe file adalah .jpg .jpeg .png .pdf dengan maksimal ukuran 2MB.</small>
						<div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_surat_usulan_ssh"></a></div>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-12">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Uraian Kelompok</th>
									<th>Nama Komponen</th>
									<th>Spesifikasi</th>
									<th>Harga</th>
									<th>Rekening</th>
									<th>Jenis Usulan</th>
								</tr>
							</thead>
							<tbody id="tbody_data_usulan"></tbody>
						</table>
					</div>
				</div>
			</div> 
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" id="submitSuratUsulan" onclick="submitSuratUsulan(<?php echo $input['tahun_anggaran']; ?>)">Simpan</button>
				<button type="button" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="tambahUsulanSsh" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="tambahUsulanSshLabel">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div> 
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>

<!-- Modal tambah harga usulan -->
<div class="modal fade" id="tambahUsulanHargaByKompSSH" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="tambahUsulanHargaByKompSSHLabel">Tambah Harga usulan SSH</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:hidden;">
				<div class="row form-group">
					<label for='tambah_harga_id_sub_unit' class="col-md-12">Sub Unit <span class="required">*</span></label>
					<div class="col-md-12">
						<select id='tambah_harga_id_sub_unit' name="id_sub_skpd" class="form-control"><?php echo $list_skpd_options; ?></select>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_harga_komp_kategori" class="col-md-12">Kategori</label>
					<div class="col-md-12">
						<input type="text" id="tambah_harga_komp_kategori" class="form-control" placeholder="Kategori" disabled>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_harga_komp_nama_komponent" class="col-md-12">Nama Komponen</label>
					<div class="col-md-12">
						<select id="tambah_harga_komp_nama_komponent" class="js-example-basic-single" class="form-control" placeholder="Nama Komponen"></select>
						<input type="text" id="tambah_harga_show_komp_nama" class="hide form-control" placeholder="Nama Komponen" disabled>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_harga_komp_spesifikasi" class="col-md-12">Spesifikasi</label>
					<div class="col-md-12">
						<input type="text" id="tambah_harga_komp_spesifikasi" class="form-control" placeholder="Spesifikasi" disabled>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_harga_komp_satuan" class="col-md-12">Satuan</label>
					<div class="col-md-12">
						<input type="text" id="tambah_harga_komp_satuan" class="form-control" placeholder="Satuan" disabled>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-12">
					<label for="tambah_harga_komp_harga_satuan" class="col-md-12">Harga Satuan</label>
						<input type="text" id="tambah_harga_komp_harga_satuan" class="form-control" placeholder="Harga Satuan">
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_harga_komp_akun" class="col-md-12">Rekening Akun</label>
					<div class="col-md-12">
						<textarea type="text" id="tambah_harga_komp_akun" class="form-control" placeholder="Rekening Akun" disabled></textarea>
					</div>
				</div>
				<div class="row form-group">
					<label for='tambah_harga_komp_jenis_produk' class="col-md-12">Jenis Produk</label>
					<div class="col-md-12">
						<input type='radio' id='tambah_harga_komp_jenis_produk_dalam_negeri' name='tambah_harga_komp_jenis_produk' value='1' disabled>
						<label class='mr-4' for='tambah_harga_komp_jenis_produk_dalam_negeri'>Produk Dalam Negeri</label>
						<input type='radio' id='tambah_harga_komp_jenis_produk_luar_negeri' name='tambah_harga_komp_jenis_produk' value='0' disabled>
						<label for='tambah_harga_komp_jenis_produk_luar_negeri'>Produk Luar Negeri</label>
					</div>
				</div>
				<div class="row form-group">
					<label for='tambah_harga_komp_tkdn' class="col-md-12">Tingkat Komponen Dalam Negeri (TKDN)</label>
					<div class="col-md-12">
						<input type='number' min="0" max="100" id='tambah_harga_komp_tkdn' style='width:22%;' placeholder='Presentase TKDN 0-100' disabled>
						<label style='font-size: 1.2rem;margin-left: 0.5rem;'>%</label>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-12">Lampiran Usulan SSH 1 <span class="required">*</span></label>
					<div class="col-md-12">
						<input type='file' id='u_lapiran_usulan_harga_ssh_1' accept="image/png, image/jpeg, image/jpg, application/pdf" style='display:block;width:100%;' onchange="checkFileType(this)">
						<small>Tipe file adalah .jpg .jpeg .png .pdf dengan maksimal ukuran 1MB.</small>
						<div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_lapiran_usulan_harga_ssh_1"></a></div>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-12">Lampiran Usulan SSH 2 <span class="required">*</span></label>
					<div class="col-md-12">
						<input type='file' id='u_lapiran_usulan_harga_ssh_2' accept="image/png, image/jpeg, image/jpg, application/pdf" style='display:block;width:100%;' onchange="checkFileType(this)">
						<small>Tipe file adalah .jpg .jpeg .png .pdf dengan maksimal ukuran 1MB.</small>
						<div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_lapiran_usulan_harga_ssh_2"></a></div>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-12">Lampiran Usulan SSH 3</label>
					<div class="col-md-12">
						<input type='file' id='u_lapiran_usulan_harga_ssh_3' accept="image/png, image/jpeg, image/jpg, application/pdf" style='display:block;width:100%;' onchange="checkFileType(this)">
						<small>Tipe file adalah .jpg .jpeg .png .pdf dengan maksimal ukuran 1MB.</small>
						<div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_lapiran_usulan_harga_ssh_3"></a></div>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_harga_komp_keterangan_lampiran" class="col-md-12">Keterangan</label>
					<div class="col-md-12">
						<input type="text" id="tambah_harga_komp_keterangan_lampiran" class="form-control" placeholder="Keterangan">
					</div>
				</div>
			</div> 
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="submitUsulanTambahHargaSshForm(<?php echo $input['tahun_anggaran']; ?>)">Simpan</button>
				<button type="button" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal tambah akun usulan -->
<div class="modal fade" id="tambahUsulanAkunByKompSSH" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="tambahUsulanAkunByKompSSHLabel">Tambah Rekening Akun usulan SSH</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:hidden;">
				<div class="row form-group">
					<label for='tambah_akun_id_sub_unit' class="col-md-12">Sub Unit <span class="required">*</span></label>
					<div class="col-md-12">
						<select id='tambah_akun_id_sub_unit' name="id_sub_skpd" class="form-control"><?php echo $list_skpd_options; ?></select>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_akun_komp_kategori" class="col-md-12">Kategori</label>
					<div class="col-md-12">
						<input type="text" id="tambah_akun_komp_kategori" style="display:block;width:100%;" placeholder="Kategori" disabled>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_akun_komp_nama_komponent" class="col-md-12">Nama Komponen</label>
					<div class="col-md-12">
						<select id="tambah_akun_komp_nama_komponent" class="form-control" placeholder="Nama Komponen"></select>
						<input type="text" id="tambah_akun_show_komp_nama" class="hide form-control" placeholder="Nama Komponen" disabled>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_akun_komp_spesifikasi" class="col-md-12">Spesifikasi</label>
					<div class="col-md-12">
						<input type="text" id="tambah_akun_komp_spesifikasi" class="form-control" placeholder="Spesifikasi" disabled>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_akun_komp_satuan" class="col-md-12">Satuan</label>
					<div class="col-md-12">
						<input type="text" id="tambah_akun_komp_satuan" class="form-control" placeholder="Satuan" disabled>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_akun_komp_harga_satuan" class="col-md-12">Harga Satuan</label>
					<div class="col-md-12">
						<input type="text" id="tambah_akun_komp_harga_satuan" class="form-control" placeholder="Harga Satuan" disabled>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_akun_komp_akun" class="col-md-12">Rekening Akun SIPD</label>
					<div class="col-md-12">
						<textarea type="text" id="tambah_akun_komp_akun" class="form-control" placeholder="Rekening Akun" disabled></textarea>
					</div>
				</div>
				<div class="row form-group">
					<label for="tambah_new_akun_komp" id_standar_harga="" class="col-md-12">Rekening Akun Usulan</label>
					<div class="col-md-12">
						<select id="tambah_new_akun_komp" multiple class="form-control"></select>
					</div>
				</div>
				<div class="row form-group">
					<label for='tambah_akun_komp_jenis_produk' class="col-md-12">Jenis Produk</label>
					<div class="col-md-12">
						<input type='radio' id='tambah_akun_komp_jenis_produk_dalam_negeri' name='tambah_akun_komp_jenis_produk' value='1' disabled>
						<label class='mr-4' for='tambah_akun_komp_jenis_produk_dalam_negeri'>Produk Dalam Negeri</label>
						<input type='radio' id='tambah_akun_komp_jenis_produk_luar_negeri' name='tambah_akun_komp_jenis_produk' value='0' disabled>
						<label for='tambah_akun_komp_jenis_produk_luar_negeri'>Produk Luar Negeri</label>
					</div>
				</div>
				<div class="row form-group">
					<label for='tambah_akun_komp_tkdn' class="col-md-12">Tingkat Komponen Dalam Negeri (TKDN)</label>
					<div class="col-md-12">
						<input type='number' id='tambah_akun_komp_tkdn' style='width:22%;' placeholder='Presentase TKDN' disabled>
						<label style='font-size: 1.2rem;margin-left: 0.5rem;'>%</label>
					</div>
				</div>
				<div class="row form-group" id="tambah_akun_lampiran">
					<label for="tambah_akun_komp_keterangan_lampiran" class="col-md-12">Keterangan</label>
					<div class="col-md-12">
						<input type="text" id="tambah_akun_komp_keterangan_lampiran" style="display:block;width:100%;" placeholder="Keterangan" disabled>
					</div>
				</div>
			</div> 
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="submitUsulanTambahAkunSshForm(<?php echo $input['tahun_anggaran']; ?>)">Simpan</button>
				<button type="button" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal tambah usulan ssh -->
<div class="modal fade" id="tambahUsulanSshModal" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Tambah usulan SSH</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:hidden;">
				<form id="form-usulan-ssh" onsubmit="return false;">
					<div class="row form-group">
						<label for='id_u_sub_skpd' class="col-md-12">Sub Unit <span class="required">*</span></label>
						<div class="col-md-12">
							<select id='id_sub_skpd' name="id_sub_skpd" class="form-control"><?php echo $list_skpd_options; ?></select>
						</div>
					</div>
					<div class="row form-group">
						<label for='u_kategori' class="col-md-12">Kategori <span class="required">*</span></label>
						<div class="col-md-12">
							<select id='u_kategori' class="form-control"></select>
						</div>
					</div>
					<div class="row form-group">
						<label for='u_nama_komponen' class="col-md-12">Nama Komponen <span class="required">*</span></label>
						<div class="col-md-12">
							<input type='text' id='u_nama_komponen' class="form-control" placeholder='Nama Komponen'>
						</div>
					</div>
					<div class="row form-group">
						<label for='u_spesifikasi' class="col-md-12">Spesifikasi <span class="required">*</span></label>
						<div class="col-md-12">
							<input type='text' id='u_spesifikasi' class="form-control" placeholder='Spesifikasi'>
						</div>
					</div>
					<div class="row form-group">
						<label for='u_satuan' class="col-md-12">Satuan <span class="required">*</span></label>
						<div class="col-md-12">
							<select id='u_satuan' class="form-control"></select>
						</div>
					</div>
					<div class="row form-group">
						<label for='u_harga_satuan' class="col-md-12">Harga Satuan <span class="required">*</span></label>
						<div class="col-md-12">
							<input type='number' id='u_harga_satuan' class="form-control" placeholder='Harga Satuan'>
						</div>
					</div>
					<div class="row form-group">
						<label for='u_akun' class="col-md-12">Rekening Akun <span class="required">*</span></label>
						<div class="col-md-12">
							<select id='u_akun' multiple class="form-control"></select>
						</div>
					</div>
					<div class="row form-group">
						<label for='u_jenis_produk' class="col-md-12">Jenis Produk <span class="required">*</span></label>
						<div class="col-md-12">
							<input type='radio' id='u_jenis_produk_dalam_negeri' name='u_jenis_produk' value='1'>
							<label class='mr-4' for='u_jenis_produk_dalam_negeri'>Produk Dalam Negeri</label>
							<input type='radio' id='u_jenis_produk_luar_negeri' name='u_jenis_produk' value='0'>
							<label for='u_jenis_produk_luar_negeri'>Produk Luar Negeri</label>
						</div>
					</div>
					<div class="row form-group">
						<label for='u_tkdn' class="col-md-12">Tingkat Komponen Dalam Negeri (TKDN) <span class="required">*</span></label>
						<div class="col-md-12">
							<input type='number' id='u_tkdn' style='width:22%;' placeholder='Presentase TKDN'>
							<label style='font-size: 1.2rem;margin-left: 0.5rem;'>%</label>
						</div>
					</div>
					<div class="row form-group">
						<label class="col-md-12">Lampiran Usulan SSH 1 <span class="required">*</span></label>
						<div class="col-md-12">
							<input type='file' id='u_lapiran_usulan_ssh_1' accept="image/png, image/jpeg, image/jpg, application/pdf" style='display:block;width:100%;' onchange="checkFileType(this)">
							<small>Tipe file adalah .jpg .jpeg .png .pdf dengan maksimal ukuran 1MB.</small>
							<div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_lapiran_usulan_ssh_1"></a></div>
						</div>
					</div>
					<div class="row form-group">
						<label class="col-md-12">Lampiran Usulan SSH 2 <span class="required">*</span></label>
						<div class="col-md-12">
							<input type='file' id='u_lapiran_usulan_ssh_2' accept="image/png, image/jpeg, image/jpg, application/pdf" style='display:block;width:100%;' onchange="checkFileType(this)">
							<small>Tipe file adalah .jpg .jpeg .png .pdf dengan maksimal ukuran 1MB.</small>
							<div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_lapiran_usulan_ssh_2"></a></div>
						</div>
					</div>
					<div class="row form-group">
						<label class="col-md-12">Lampiran Usulan SSH 3</label>
						<div class="col-md-12">
							<input type='file' id='u_lapiran_usulan_ssh_3' accept="image/png, image/jpeg, image/jpg, application/pdf" style='display:block;width:100%;' onchange="checkFileType(this)">
							<small>Tipe file adalah .jpg .jpeg .png .pdf dengan maksimal ukuran 1MB.</small>
							<div style="padding-top: 10px; padding-bottom: 10px;"><a id="file_lapiran_usulan_ssh_3"></a></div>
						</div>
					</div>
					<div class="row form-group">
						<label for='u_keterangan_lampiran' class="col-md-12">Keterangan</label>
						<div class="col-md-12">
							<input type="text" id='u_keterangan_lampiran' class="form-control" placeholder="Keterangan">
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
                <button type="button" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function(){
		jQuery('#wrap-loading').show();
		window.tahun = <?php echo $input['tahun_anggaran']; ?>;
		window.all_skpd = <?php echo json_encode($all_skpd); ?>;
		get_data_ssh_surat_nota_dinas(tahun)
		.then(function(){
			get_data_ssh_surat(tahun)
			.then(function(){
				get_data_ssh(tahun)
				.then(function(){
		            get_data_satuan_ssh(tahun);
		            get_data_nama_ssh(tahun);
					jQuery("#usulan_ssh_table_wrapper div:first").addClass("h-100 align-items-center");
					let html_filter = ""
					+"<ul id='toolbar_ssh_usulan'>"
						+"<li>"
							+"<select name='filter_action' class='ml-3 bulk-action' id='multi_select_action'>"
								+"<option value='0'>Tindakan Massal</option>"
								+"<option value='approve'>Setuju</option>"
								+"<option value='notapprove'>Tolak</option>"
								+"<option value='delete'>Hapus</option>"
							+"</select>"
							+"<button style='margin-left: 10px;' type='submit' class='ml-1 btn btn-secondary' onclick='action_check_data_usulan_ssh()'>Terapkan</button>"
						+"</li>"
						+"<li>"
							+"<select name='filter_status' class='ml-3 bulk-action' id='search_filter_action' onchange='action_filter_data_usulan_ssh()'>"
								+"<option value=''>Pilih Status</option>"
								+"<option value='diterima'>Diterima</option>"
								+"<option value='ditolak'>Ditolak</option>"
								+"<option value='diterima_admin'>Diterima Admin</option>"
								+"<option value='ditolak_admin'>Ditolak Admin</option>"
								+"<option value='diterima_tapdkeu'>Diterima TAPD Keuangan</option>"
								+"<option value='ditolak_tapdkeu'>Ditolak TAPD Keuangan</option>"
								+"<option value='menunggu'>Menunggu</option>"
								+"<option value='sudah_upload_sipd'>Sudah upload SIPD</option>"
								+"<option value='belum_upload_sipd'>Belum upload SIPD</option>"
							+"</select>"
						+"</li>"
						+"<li>"
							+"<select name='filter_opd' class='ml-3 bulk-action' id='search_filter_action_opd' style='margin-left: 10px;' onchange='action_filter_data_usulan_ssh()'>"
							+"</select>"
						+"</li>"
						+"<li>"
							+"<select name='filter_surat' class='ml-3 bulk-action' id='search_filter_surat' style='margin-left: 10px; width:300px;' onchange='action_filter_data_usulan_ssh()'>"
								+"<option value=''>Pilih Surat</option>"
							+"</select>"
						+"</li>"
						+"<li>"
							+"<select name='filter_nota_dinas' class='ml-3 bulk-action' id='search_nota_dinas_filter_surat' style='margin-left: 10px; width:300px;' onchange='action_filter_data_usulan_ssh()'>"
								+"<option value=''>Pilih Nota Dinas</option>"
							+"</select>"
						+"</li>"
					+"</ul>";
					
					jQuery(".h-100").after(html_filter);
					jQuery("#multi_select_action").select2();
					jQuery("#search_filter_action").select2();
					jQuery('#search_filter_surat').html(html_surat_usulan);
					jQuery("#search_filter_surat").select2();
					jQuery('#search_nota_dinas_filter_surat').html(html_surat_usulan_nota_dinas);
					jQuery("#search_nota_dinas_filter_surat").select2();
					jQuery("#search_filter_action_opd").html('<?php echo $list_skpd_options; ?>');
				    jQuery("#search_filter_action_opd").select2();
				});
			});
		});
		let get_data = 1;
		jQuery('#tambahSuratUsulan').on('hidden.bs.modal', function () {
			jQuery("#tambahSuratUsulan input[name='ubah_id']").val("");
			jQuery("#tambahSuratUsulan #surat_skpd").val("");
			jQuery("#tambahSuratUsulan #nomor_surat").val(jQuery("#tambahSuratUsulan #nomor_surat").attr('value'));
			jQuery("#tambahSuratUsulan #catatan_surat").val("");
			jQuery("#tambahSuratUsulan #catatan_verifikator").val("").closest('.row').hide();
			jQuery("#tambahSuratUsulan #u_surat_usulan_ssh").val("").closest('.row').hide();
        	<?php 
        		if(
					in_array("administrator", $user_meta->roles)
					|| in_array("tapd_keu", $user_meta->roles)
				){
        			echo "jQuery('#tambahSuratUsulan #catatan_verifikator').prop('disabled', false);";
        			echo "jQuery('#tambahSuratUsulan #catatan_surat').prop('disabled', true);";
        		}else{
        			echo "jQuery('#tambahSuratUsulan #catatan_verifikator').prop('disabled', true);";
        			echo "jQuery('#tambahSuratUsulan #catatan_surat').prop('disabled', false);";
        		}
        	?>
		});
		jQuery('#tambahUsulanSsh').on('hidden.bs.modal', function () {
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-lg");
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-xl");
			jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-sm");
			jQuery("#tambahUsulanSsh .modal-body").html("");
			jQuery("#tambahUsulanSsh .modal-footer").removeClass("");
		});
		jQuery('#tambahUsulanSshModal').on('hidden.bs.modal', function () {
			jQuery("#u_kategori").val("").trigger('change');
			jQuery("#u_nama_komponen").val("");
			jQuery("#u_spesifikasi").val("");
			jQuery("#u_satuan").val("").trigger('change');
			jQuery("#u_harga_satuan").val("");
			jQuery("#u_akun").val("").trigger('change');
			jQuery("#u_keterangan_lampiran").val("");
			jQuery("#tambahUsulanSshModal .modal-title").html("");
			jQuery("#tambahUsulanSshModal .submitBtn").text("");
			jQuery("#tambahUsulanSshModal .submitBtn").attr("onclick", '');
		})
		jQuery('#tambahUsulanHargaByKompSSH').on('hidden.bs.modal', function () {
			jQuery("#tambah_harga_id_sub_unit").val("");
			jQuery("#tambah_harga_komp_kategori").val("");
			jQuery("#tambah_harga_komp_nama_komponent").val("").trigger('change');
			jQuery("#tambah_harga_komp_nama_komponent").next(".select2-container").removeClass("hide");
			jQuery("#tambah_harga_show_komp_nama").addClass("hide");
			jQuery("#tambah_harga_komp_spesifikasi").val("");
			jQuery("#tambah_harga_komp_satuan").val("");
			jQuery("#tambah_harga_komp_harga_satuan").val("");
			jQuery("#tambah_harga_komp_akun").html("");
			jQuery("#tambah_harga_komp_keterangan_lampiran").val("");
			jQuery("#tambahUsulanHargaByKompSSH .modal-title").html("");
			jQuery("#tambahUsulanHargaByKompSSH .submitBtn").text("");
			jQuery("#tambahUsulanHargaByKompSSH .submitBtn").attr("onclick", '');
			jQuery("#tambah_harga_komp_nama_komponent").attr("disabled", false);
		})
		jQuery('#tambahUsulanAkunByKompSSH').on('hidden.bs.modal', function () {
			jQuery("#tambah_akun_id_sub_unit").val("");
			jQuery("#tambah_akun_komp_kategori").val("");
			jQuery("#tambah_akun_komp_nama_komponent").val("").trigger('change');
			jQuery("#tambah_akun_komp_nama_komponent").next(".select2-container").removeClass("hide");
			jQuery("#tambah_akun_show_komp_nama").addClass("hide");
			jQuery("#tambah_akun_komp_spesifikasi").val("");
			jQuery("#tambah_akun_komp_satuan").val("");
			jQuery("#tambah_akun_komp_harga_satuan").val("");
			jQuery("#tambah_akun_komp_akun").html("");
			jQuery("#tambah_new_akun_komp").val("").trigger('change');
			jQuery("#tambah_new_akun_komp").attr('id_standar_harga', '');;
			jQuery("#tambah_akun_komp_keterangan_lampiran").val("");
			jQuery("#tambah_akun_lampiran").show();
			jQuery("#tambahUsulanAkunByKompSSH .modal-title").html("");
			jQuery("#tambahUsulanAkunByKompSSH .submitBtn").text("");
			jQuery("#tambahUsulanAkunByKompSSH .submitBtn").attr("onclick", '');
			
		})
		jQuery('#checkall').click(function(){
			if(jQuery(this).is(':checked')){
				jQuery('.delete_check').prop('checked', true);
			}else{
				jQuery('.delete_check').prop('checked', false);
			}
		})
	});

	function get_data_ssh(tahun){
		return new Promise(function(resolve, reject){
			globalThis.usulanSSHTable = jQuery('#usulan_ssh_table')
			.on('preXhr.dt', function( e, settings, data ) {
				jQuery("#wrap-loading").show();
				// console.log('preXhr.dt');
				data.filter = jQuery("#search_filter_action").val();
				data.filter_opd = jQuery("#search_filter_action_opd").val();
				data.filter_surat = jQuery("#search_filter_surat").val();
				data.filter_nota_dinas = jQuery("#search_nota_dinas_filter_surat").val();
			} )
			.DataTable({
				"processing": true,
        		"serverSide": true,
		        "ajax": {
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:"post",
					data:{
						'action': "get_data_usulan_ssh",
						'api_key': jQuery("#api_key").val(),
						'tahun_anggaran': tahun
					}
				},
  				order: [[5, 'desc']], // order by waktu input descending
				"columns": [
					{ 
						"data": "deleteCheckbox",
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
		            { 
		            	"data": "show_kode_komponen",
		            	className: "text-left show-komponen",
						"targets": "no-sort",
						"orderable": false
		            },
		            {
		            	"data": "nama_standar_harga",
						"targets": "no-sort",
						"orderable": false
					},
		            { "data": "spek_satuan",
		            	className: "text-left spek-satuan",
						"targets": "no-sort",
						"orderable": false
					},
		            { 
		            	"data": "harga",
		            	className: "text-right",
						"targets": "no-sort",
						"orderable": false,
		            	render: function(data, type) {
			                var number = jQuery.fn.dataTable.render.number( '.', ',', 2, ''). display(data);
			                return number;
			            }
		            },
					{ 
		            	"data": {
		            		_: "show_keterangan",
							sort: "update_at"
		            	},
		            	className: "text-left kol-keterangan",
						"targets": "no-sort",
						"orderable": false
		            },
		            { 
		            	"data": 'lampiran',
		            	className: "text-left lampiran",
						"targets": "no-sort",
						"orderable": false
		            },
		            { 
		            	"data": "verify_admin",
		            	className: "text-left verify_admin",
						"targets": "no-sort",
						"orderable": false
		            },
		            { 
		            	"data": "verify_tapdkeu",
		            	className: "text-left verify_tapdkeu",
						"targets": "no-sort",
						"orderable": false
		            },
					{ 
		            	"data": "show_status",
		            	className: "text-left show_status",
						"targets": "no-sort",
						"orderable": false
		            },
					{ 
		            	"data": "aksi",
		            	className: "text-center",
						"targets": "no-sort",
						"orderable": false
		            }
		        ],
		        'createdRow': function(row, data, dataIndex) {
			    	var dataCell = jQuery(row).find('>td:eq(5)');
			    	var dateOrder = dataCell.find('td:eq(0)').text().split(': ')[1];
			    	dataCell.attr('data-order', dateOrder);
			  	},
				"drawCallback": function(settings) {
					var api = this.api();
					// console.log('drawCallback');
					jQuery("#wrap-loading").hide();
					resolve();
				}
			});
		});
	}

	function get_data_ssh_surat(tahun){
		return new Promise(function(resolve, reject){
			window.suratUsulanSSHTable = jQuery('#surat_usulan_ssh_table')
			.on('preXhr.dt', function ( e, settings, data ) {
				jQuery("#wrap-loading").show();
			})
			.DataTable({
				"processing": true,
        		"serverSide": true,
		        "ajax": {
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:"post",
					data:{
						'action' : "get_data_usulan_ssh_surat",
						'api_key' : jQuery("#api_key").val(),
						'tahun_anggaran' : tahun
					}
				},
  				order: [[0, 'desc']],
				"columns": [
					{
						"data": 'update_at',
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"data": 'nama_skpd',
						"targets": 'no-sort',
						"orderable": false
		            },
					{
						"data": 'nomor_surat',
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"data": 'nama_file',
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"data": 'jml_usulan',
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"data": 'acuan_ssh',
						"targets": 'no-sort',
						"orderable": false
		            },
					{
						"data": 'catatan',
						"targets": 'no-sort',
						"orderable": false
		            },
					{
						"data": 'catatan_verifikator',
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"data": 'aksi',
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            }
		        ],
		        "drawCallback": function(settings){
		        	var api = this.api();
		        	// console.log('api', api.rows().data());
		        	window.html_surat_usulan = ""
		        		+"<option value=''>Pilih Surat</option>";
		        	api.rows().data().map(function(b, i){
		        		html_surat_usulan += "<option value='"+b.nomor_surat+"'>"+b.nomor_surat+"</option>";
		        	});
		        	jQuery('#search_filter_surat').html(html_surat_usulan);
					jQuery("#wrap-loading").hide();
		        	resolve();
		        }
			});
		});
	}

	function get_data_ssh_surat_nota_dinas(tahun){
		return new Promise(function(resolve, reject){
			window.suratNotaDinasUsulanSSHTable = jQuery('#surat_nota_dinas_usulan_ssh_table')
			.on('preXhr.dt', function ( e, settings, data ) {
				jQuery("#wrap-loading").show();
			})
			.DataTable({
				"processing": true,
        		"serverSide": true,
		        "ajax": {
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:"post",
					data:{
						'action' : "get_data_usulan_ssh_surat_nota_dinas",
						'api_key' : jQuery("#api_key").val(),
						'tahun_anggaran' : tahun
					}
				},
  				order: [[0, 'desc']],
				"columns": [
					{
						"data": 'update_at',
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"data": 'nomor_surat',
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"data": 'jml_usulan',
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"data": 'catatan',
						"targets": 'no-sort',
						"orderable": false
		            },
					{
						"data": 'aksi',
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            }
		        ],
		        "drawCallback": function(settings){
					jQuery("#wrap-loading").hide();
		        	var api = this.api();
		        	// console.log('api', api.rows().data());
		        	window.html_surat_usulan_nota_dinas = ""
		        		+"<option value=''>Pilih Nota Dinas</option>";
		        	api.rows().data().map(function(b, i){
		        		html_surat_usulan_nota_dinas += "<option value='"+b.nomor_surat+"'>"+b.nomor_surat+"</option>";
		        	});
		        	jQuery('#search_nota_dinas_filter_surat').html(html_surat_usulan_nota_dinas);
		        	resolve();
		        }
			});
		});
	}

	function get_data_satuan_ssh(tahun){
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:"post",
			data:{
				'action' : "get_data_satuan_ssh",
				'api_key' : jQuery("#api_key").val(),
				'tahun_anggaran' : tahun,
			},
			dataType: "json",
			success:function(response){
				globalThis.dataSatuanSsh = response;
				enable_button();
			}
		})
	}

	function get_data_kategori_ssh(tahun){
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:"post",
			data:{
				'action' : "get_data_kategori_ssh",
				'api_key' : jQuery("#api_key").val(),
				'tahun_anggaran' : tahun,
			},
			dataType: "json",
			success:function(response){
				globalThis.dataCategorySsh = response;
				enable_button();
			}
		})
	}

	function get_data_akun_ssh(tahun){
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:"post",
			data:{
				'action' : "get_data_akun_ssh",
				'api_key' : jQuery("#api_key").val(),
				'tahun_anggaran' : tahun,
			},
			dataType: "json",
			success:function(response){
				globalThis.dataAkunSsh = response;
				enable_button();
			}
		})
	}
	
	function enable_button(){
		if(
			typeof dataSatuanSsh != 'undefined'
		){
			var ajax_nama_komponen = {
			  	ajax: {
				    url: "<?php echo admin_url('admin-ajax.php'); ?>",
				    type: 'post',
				    dataType: 'json',
				    data: function (params) {
				      	var query = {
				        	search: params.term,
					        page: params.page || 0,
					        action: 'get_komponen_and_id_kel_ssh',
					        api_key : jQuery("#api_key").val(),
							tahun_anggaran : tahun
				      	}
				      	return query;
				    },
				    processResults: function (data, params) {
				    	console.log('data', data);
				      	return {
					        results: data.results,
					        pagination: {
					          	more: data.pagination.more
					        }
				      	};
				    }
			  	},
			    placeholder: 'Cari komponen',
			    minimumInputLength: 3,
			    width: '100%'
			};
			var ajax_kategori = {
			  	ajax: {
				    url: "<?php echo admin_url('admin-ajax.php'); ?>",
				    type: 'post',
				    dataType: 'json',
				    data: function (params) {
				      	var query = {
				        	search: params.term,
					        page: params.page || 0,
					        action: 'get_data_kategori_ssh',
					        api_key : jQuery("#api_key").val(),
							tahun_anggaran : tahun
				      	}
				      	return query;
				    },
				    processResults: function (data, params) {
				    	console.log('data', data);
				      	return {
					        results: data.results,
					        pagination: {
					          	more: data.pagination.more
					        }
				      	};
				    }
			  	},
			    placeholder: 'Cari kategori',
			    minimumInputLength: 3,
			    width: '100%'
			};
			var ajax_akun = {
			  	ajax: {
				    url: "<?php echo admin_url('admin-ajax.php'); ?>",
				    type: 'post',
				    dataType: 'json',
				    data: function (params) {
				      	var query = {
				        	search: params.term,
					        page: params.page || 0,
					        action: 'get_data_akun_ssh',
					        api_key : jQuery("#api_key").val(),
					        id : jQuery("#tambah_new_akun_komp").attr('id_standar_harga'),
							tahun_anggaran : tahun
				      	}
				      	return query;
				    },
				    processResults: function (data, params) {
				    	console.log('data', data);
				      	return {
					        results: data.results,
					        pagination: {
					          	more: data.pagination.more
					        }
				      	};
				    }
			  	},
			    placeholder: 'Cari komponen',
			    minimumInputLength: 6,
			    width: '100%'
			};

			var ajax_akun2 = { ...ajax_akun };
			ajax_akun2.dropdownParent = jQuery('#tambahUsulanSshModal .modal-content');
			jQuery("#u_akun").select2(ajax_akun2);

			ajax_akun.dropdownParent = jQuery('#tambahUsulanAkunByKompSSH .modal-content');
			jQuery("#tambah_new_akun_komp").select2(ajax_akun);

			var ajax_nama_komponen2 = { ...ajax_nama_komponen };
			ajax_nama_komponen2.dropdownParent = jQuery('#tambahUsulanHargaByKompSSH .modal-content');
			jQuery('#tambah_harga_komp_nama_komponent').select2(ajax_nama_komponen2);

			ajax_nama_komponen.dropdownParent = jQuery('#tambahUsulanAkunByKompSSH .modal-content');
			jQuery('#tambah_akun_komp_nama_komponent').select2(ajax_nama_komponen);

			ajax_kategori.dropdownParent = jQuery('#tambahUsulanSshModal .modal-content');
			jQuery('#u_kategori').select2(ajax_kategori);
			
			jQuery("#u_satuan").html(dataSatuanSsh.table_content);
			jQuery('#u_satuan').select2({
				width: '100%', 
				dropdownParent: jQuery('#tambahUsulanSshModal .modal-content')
			});

			jQuery('.tambah_ssh').attr('disabled', false);
			jQuery('.tambah_new_ssh').attr('disabled', false);
			jQuery('#wrap-loading').hide();
		}
	}

	function get_data_nama_ssh(tahun){
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:"post",
			data:{
				'action' : "get_data_nama_ssh",
				'api_key' : jQuery("#api_key").val(),
				'tahun_anggaran' : tahun,
			},
			dataType: "json",
			success:function(response){
				globalThis.dataNamaSsh = response;
			}
		})
	}

<?php if(false == $is_admin): ?>
	function tambah_new_ssh(tahun){
		jQuery('#id_sub_skpd').prop('disabled', false);
		jQuery('#u_kategori').prop('disabled', false);
		jQuery('#u_nama_komponen').prop('disabled', false);
		jQuery('#u_spesifikasi').prop('disabled', false);
		jQuery('#u_satuan').prop('disabled', false);
		jQuery('#u_harga_satuan').prop('disabled', false);
		jQuery('input[name="u_jenis_produk"]').prop('disabled', false);
		jQuery('#u_tkdn').prop('disabled', false);
		jQuery('#u_akun').prop('disabled', false);

		jQuery("#u_tkdn").val(null);
		jQuery("#u_lapiran_usulan_ssh_1").val(null);
		jQuery("#u_lapiran_usulan_ssh_2").val(null);
		jQuery("#u_lapiran_usulan_ssh_3").val(null);
		jQuery("#file_lapiran_usulan_ssh_1").html('');
		jQuery("#file_lapiran_usulan_ssh_2").html('');
		jQuery("#file_lapiran_usulan_ssh_3").html('');

		jQuery("#tambahUsulanSshModal .modal-title").html("Tambah usulan SSH");
		jQuery("#tambahUsulanSshModal .modal-footer").find('.submitBtn').remove();
		jQuery("#tambahUsulanSshModal .modal-footer").prepend('<button class=\'btn btn-primary submitBtn\' onclick=\'return false;\'>Simpan</button>');
		jQuery("#tambahUsulanSshModal .submitBtn")
			.attr("onclick", 'submitUsulanSshForm('+tahun+')')
			.attr("disabled", false)
			.text("Simpan");
		jQuery('#tambahUsulanSshModal').modal('show');
		jQuery("#id_sub_skpd").select2({width:'100%', dropdownParent: jQuery('#tambahUsulanSshModal .modal-content')});
		jQuery("#id_sub_skpd").val('').trigger('change');
	}

	/** Menampilkan data SSH sesuai komponen */
	function get_data_by_name_komponen_ssh(jenis, tahun){
		if(jenis === 'harga'){
			jQuery("#tambah_harga_komp_nama_komponent").on("change", function(){
				var id_standar_harga = jQuery(this).val();
				if(id_standar_harga != null){
					get_data_usulan_ssh_by_komponen('harga',id_standar_harga)
				}
			});
			jQuery('#tambah_harga_id_sub_unit').prop('disabled', false);
			jQuery('#tambah_harga_komp_kategori').prop('disabled', true);
			jQuery('#tambah_harga_komp_nama_komponent').prop('disabled', false);
			jQuery('#tambah_harga_komp_spesifikasi').prop('disabled', true);
			jQuery('#tambah_harga_komp_satuan').prop('disabled', true);
			jQuery('input[name="tambah_harga_komp_jenis_produk"]').prop('disabled', true);
			jQuery('#tambah_harga_komp_tkdn').prop('disabled', true);
			jQuery('#tambah_harga_komp_akun').prop('disabled', true);
			jQuery('#tambah_harga_komp_harga_satuan').prop('disabled', false);
			jQuery('#file_lapiran_usulan_harga_ssh_1').html('');
			jQuery('#file_lapiran_usulan_harga_ssh_2').html('');
			jQuery('#file_lapiran_usulan_harga_ssh_3').html('');
			jQuery('#u_lapiran_usulan_harga_ssh_1').show();
			jQuery('#u_lapiran_usulan_harga_ssh_1').parent().find('small').show();
			jQuery('#u_lapiran_usulan_harga_ssh_2').show();
			jQuery('#u_lapiran_usulan_harga_ssh_2').parent().find('small').show();
			jQuery('#u_lapiran_usulan_harga_ssh_3').show();
			jQuery('#u_lapiran_usulan_harga_ssh_3').parent().find('small').show();
			jQuery('#tambah_harga_komp_keterangan_lampiran').prop('disabled', false);

			jQuery("#tambahUsulanHargaByKompSSH .modal-footer").find('.submitBtn').remove();
			jQuery("#tambahUsulanHargaByKompSSH .modal-title").html('Tambah Harga usulan SSH');
			jQuery("#tambahUsulanHargaByKompSSH .modal-footer").prepend('<button class=\'btn btn-primary submitBtn\' onclick=\'return false;\'>Simpan</button>');
			jQuery("#tambahUsulanHargaByKompSSH .submitBtn")
				.attr("onclick", 'submitUsulanTambahHargaSshForm('+tahun+')')
				.attr("disabled", false)
				.text("Simpan");
			jQuery('#tambahUsulanHargaByKompSSH').modal('show');
		}else if(jenis === 'akun'){
			jQuery("#tambah_akun_komp_nama_komponent").on("change", function(){
				var id_standar_harga = jQuery(this).val();
				if(id_standar_harga != null){
					get_data_usulan_ssh_by_komponen('akun',id_standar_harga)
				}
			});
			jQuery('#tambah_akun_id_sub_unit').prop('disabled', false);
			jQuery('#tambah_akun_komp_kategori').prop('disabled', true);
			jQuery('#tambah_akun_komp_nama_komponent').prop('disabled', false);
			jQuery('#tambah_akun_komp_spesifikasi').prop('disabled', true);
			jQuery('#tambah_akun_komp_satuan').prop('disabled', true);
			jQuery('#tambah_akun_komp_harga_satuan').prop('disabled', true);
			jQuery('input[name="tambah_akun_komp_jenis_produk"]').prop('disabled', true);
			jQuery('#tambah_akun_komp_tkdn').prop('disabled', true);
			jQuery('#tambah_akun_komp_akun').prop('disabled', true);
			jQuery('#tambah_new_akun_komp').prop('disabled', false);
			jQuery('#tambah_akun_komp_keterangan_lampiran').prop('disabled', false);

			jQuery("#tambahUsulanAkunByKompSSH .modal-footer").find('.submitBtn').remove();
			jQuery("#tambahUsulanAkunByKompSSH .modal-title").html('Tambah Rekening Akun usulan SSH');
			jQuery("#tambahUsulanAkunByKompSSH .modal-footer").prepend('<button class=\'btn btn-primary submitBtn\' onclick=\'return false;\'>Simpan</button>');
			jQuery("#tambahUsulanAkunByKompSSH .submitBtn")
				.attr("onclick", 'submitUsulanTambahAkunSshForm('+tahun+')')
				.attr("disabled", false)
				.text("Simpan");
			jQuery('#tambahUsulanAkunByKompSSH').modal('show');
		}
	}
<?php else: ?>
	function tambah_nota_dinas(tahun){
		jQuery("#tambahNotaDinasModal .modal-title").html("Tambah Nota Dinas");
		jQuery('#tbody_data_usulan_nota_dinas').closest('.row.form-group').hide();
		jQuery('#nomor_surat_nota_dinas').val(jQuery('#nomor_surat_nota_dinas').attr('placeholder'));
		jQuery('#catatan_surat_nota_dinas').val('');
		jQuery('.submitBtn').prop("disabled", false);
		jQuery("#tambahNotaDinasModal input[name='ubah_id']").val('');
		jQuery("#tambahNotaDinasModal #ids_nota_dinas").val('');
		jQuery('#tambahNotaDinasModal').modal('show');
	}

	function submitNotaDinas(tahun){
		var nomor_surat = jQuery('#nomor_surat_nota_dinas').val();
		if(nomor_surat == ''){
			return alert('Nomor surat Nota Dinas tidak boleh kosong!');
		}
		var catatan = jQuery('#catatan_surat_nota_dinas').val();
		if(catatan == ''){
			return alert('Catatan Nota Dinas tidak boleh kosong!');
		}
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:'post',
			data: {
				'action' : "submit_nota_dinas",
				'api_key' : jQuery("#api_key").val(),
				'tahun_anggaran' : tahun,
				'nomor_surat': nomor_surat,
				'catatan': catatan,
				'ubah': jQuery("#tambahNotaDinasModal input[name='ubah_id']").val(),
				'ids': jQuery("#tambahNotaDinasModal #ids_nota_dinas").val()
			},
			dataType: 'json',
			beforeSend: function () {
				jQuery('.submitBtn').attr("disabled","disabled");
			},
			success:function(response){
				jQuery('.submitBtn').prop("disabled", false);
				alert(response.message);
				if(response.status == 'success'){
					jQuery('#tambahNotaDinasModal').modal('hide');
					suratNotaDinasUsulanSSHTable.ajax.reload();
				}
				jQuery("#wrap-loading").hide();
			}
		});
	}
<?php endif; ?>

	function get_komponen_and_id_kel_ssh(tahun){
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:"post",
			data:{
				'action' : "get_komponen_and_id_kel_ssh",
				'api_key' : jQuery("#api_key").val(),
				'tahun_anggaran' : tahun,
			},
			dataType: "json",
			success:function(response){
				globalThis.dataKomponenAndId = response;
				enable_button();
			}
		})
	}

	/** Ambil data detail ssh sesuai komponen */
	function get_data_usulan_ssh_by_komponen(jenis, id_standar_harga){
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:'post',
			data:{
				'action' : 'get_data_usulan_ssh_by_komponen',
				'api_key' : jQuery("#api_key").val(),
				'id_standar_harga' : id_standar_harga,
			},
			dataType: 'json',
			success:function(response){
				if(jenis === 'harga'){
					jQuery("#tambah_harga_id_sub_unit").val(response.data_ssh_usulan_by_id.id_sub_skpd);
					jQuery("#tambah_harga_komp_kategori").val(response.data_ssh_usulan_by_id.kode_kel_standar_harga+" "+response.data_ssh_usulan_by_id.nama_kel_standar_harga);
					jQuery("#tambah_harga_komp_spesifikasi").val(response.data_ssh_usulan_by_id.spek);
					jQuery("#tambah_harga_komp_satuan").val(response.data_ssh_usulan_by_id.satuan);
					jQuery("#tambah_harga_komp_akun").html(response.table_content);
					jQuery(`#tambah_harga_komp_jenis_produk_${response.data_ssh_usulan_by_id.jenis_produk}`).prop('checked',true);
					jQuery("#tambah_harga_komp_tkdn").val(response.data_ssh_usulan_by_id.tkdn);
				}else if(jenis === 'akun'){
					jQuery("#tambah_akun_id_sub_unit").val(response.data_ssh_usulan_by_id.id_sub_skpd);
					jQuery("#tambah_akun_komp_kategori").val(response.data_ssh_usulan_by_id.kode_kel_standar_harga+" "+response.data_ssh_usulan_by_id.nama_kel_standar_harga);
					jQuery("#tambah_akun_komp_spesifikasi").val(response.data_ssh_usulan_by_id.spek);
					jQuery("#tambah_akun_komp_satuan").val(response.data_ssh_usulan_by_id.satuan);
					jQuery("#tambah_akun_komp_harga_satuan").val(response.data_ssh_usulan_by_id.harga);
					jQuery("#tambah_akun_komp_keterangan_lampiran").val(response.data_ssh_usulan_by_id.keterangan_lampiran);
					jQuery("#tambah_akun_komp_akun").html(response.table_content);
					jQuery(`#tambah_akun_komp_jenis_produk_${response.data_ssh_usulan_by_id.jenis_produk}`).prop('checked',true);
					jQuery("#tambah_akun_komp_tkdn").val(response.data_ssh_usulan_by_id.tkdn);
				}
				if(response.status != 'success'){
					alert('Some problem occurred, please try again.');
				}
			}
		});
	}

	function submitUsulanSshForm(tahun){
		var kategori = jQuery('#u_kategori').val();
		var nama_komponen = jQuery('#u_nama_komponen').val();
		var spesifikasi = jQuery('#u_spesifikasi').val();
		var satuan = jQuery('#u_satuan').val();
		var harga_satuan = jQuery('#u_harga_satuan').val();
		var akun = jQuery('#u_akun').val();
		var keterangan_lampiran = jQuery('#u_keterangan_lampiran').val();
		var jenis_produk = jQuery('input[name=u_jenis_produk]:checked').val();
		var tkdn = jQuery('#u_tkdn').val();
		var lapiran_usulan_ssh_1 = jQuery('#u_lapiran_usulan_ssh_1')[0].files[0];
		var lapiran_usulan_ssh_2 = jQuery('#u_lapiran_usulan_ssh_2')[0].files[0];
		var lapiran_usulan_ssh_3 = jQuery('#u_lapiran_usulan_ssh_3')[0].files[0];
		var id_sub_skpd = jQuery("#id_sub_skpd").val();

		if(kategori == '' || kategori.trim('') == ''){
			alert('Kategori kelompok tidak boleh kosong!');
		}else if(nama_komponen == '' || nama_komponen.trim('') == ''){
			alert('Nama Komponen tidak boleh kosong!');
		}else if(spesifikasi == '' || spesifikasi.trim('') == ''){
			alert('Spesifikasi tidak boleh kosong!');
		}else if(satuan == '' || satuan.trim('') == ''){
			alert('Satuan tidak boleh kosong!');
		}else if(harga_satuan == '' || harga_satuan.trim('') == ''){
			alert('Harga satuan tidak boleh kosong!');
		}else if(jenis_produk == '' || jenis_produk.trim('') == ''){
			alert('Jenis produk harus dipilih!');
		}else if(tkdn == '' || tkdn.trim('') == ''){
			alert('TKDN tidak boleh kosong!');
		}else if(akun.length == 0){
			alert('Akun rekening tidak boleh kosong!');
		}else if(typeof lapiran_usulan_ssh_1 == 'undefined'){
			alert('Lampiran 1 tidak boleh kosong!');
		}else if(typeof lapiran_usulan_ssh_2 == 'undefined'){
			alert('Lampiran 2 tidak boleh kosong!');
		}else if(typeof id_sub_skpd == 'undefined'){
			alert('Sub unit tidak boleh kosong!');
		}else{
			jQuery("#wrap-loading").show();
			let tempData = new FormData();
			tempData.append('action', 'submit_usulan_ssh');
			tempData.append('api_key', jQuery("#api_key").val());
			tempData.append('kategori', kategori);
			tempData.append('nama_komponen', nama_komponen);
			tempData.append('spesifikasi', spesifikasi);
			tempData.append('satuan', satuan);
			tempData.append('harga_satuan', harga_satuan);
			tempData.append('jenis_produk', jenis_produk);
			tempData.append('tkdn', tkdn);
			tempData.append('akun', akun);
			tempData.append('tahun_anggaran', tahun);
			tempData.append('keterangan_lampiran', keterangan_lampiran);
			tempData.append('lapiran_usulan_ssh_1', lapiran_usulan_ssh_1);
			tempData.append('lapiran_usulan_ssh_2', lapiran_usulan_ssh_2);
			tempData.append('id_sub_skpd', id_sub_skpd);

			if(typeof lapiran_usulan_ssh_3 !== 'undefined'){
				tempData.append('lapiran_usulan_ssh_3', lapiran_usulan_ssh_3);
			}

			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data: tempData,
				dataType: 'json',
			    processData: false,
			    contentType: false,
			    cache: false,
				beforeSend: function () {
					jQuery('.submitBtn').attr("disabled","disabled");
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
						jQuery('#tambahUsulanSshModal').modal('hide');
						usulanSSHTable.ajax.reload();
					}else{
						alert(response.message);
					}
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery("#wrap-loading").hide();
				}
			});
		}
	}

	function field_add_akun_to_ssh(){
		jQuery(".add_akun_to_ssh").toggle();
	}

	function save_add_akun_to_ssh(tahun){
		var id_standar_harga = jQuery('#u_data_add_ssh_akun_id').val();
		var data_rek_akun = jQuery('#u_data_akun').val();
		jQuery("#wrap-loading").show();
		if(id_standar_harga.trim() == '' || data_rek_akun == ''){
			jQuery("#wrap-loading").hide();
			alert('Harap diisi semua, tidak ada yang kosong.');
			return false;
		}else{
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action' : 'submit_add_new_akun_ssh_usulan',
					'api_key' : jQuery("#api_key").val(),
					'id_standar_harga' : id_standar_harga,
					'data_akun' : data_rek_akun,
					'tahun_anggaran' : tahun,
				},
				dataType: 'json',
				beforeSend: function () {
					jQuery('.submitBtn').attr("disabled","disabled");
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
						jQuery('#tambahUsulanSsh').modal('hide');
						usulanSSHTable.ajax.reload();
					}else{
						alert(response.message);
					}
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery("#wrap-loading").hide();
				}
			});
		}
	}
<?php if(true == $is_admin): ?>
	//verify akun ssh usulan
	function verify_ssh_usulan(id){
		jQuery('#tambahUsulanSsh').modal('show');
		jQuery("#tambahUsulanSshLabel").html("Verifikasi SSH");
		jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-lg modal-xl");
		jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-sm");
		jQuery("#tambahUsulanSsh .modal-body").html(""
			+"<div class='akun-ssh-verify'><table>"
				+"<tr>"
					+"<td><input class='verify-ssh' id='verify-ssh-yes' name='verify_ssh' value='1' type='radio' checked><label for='verify-ssh-yes'>Terima</label></td>"
					+"<td><input class='verify-ssh' id='verify-ssh-no' name='verify_ssh' value='0' type='radio'><label for='verify-ssh-no'>Tolak</label></td>"
				+"</tr>"
				+"<tr class='add-desc-verify-ssh' style='display:none;'>"
					+"<td colspan='2'><label for='alasan_verify_ssh' style='display:inline-block;'>Alasan</label><textarea id='alasan_verify_ssh'></textarea></td>"
				+"</tr>"
				+"<tr class='add-nota-dinas-verify-ssh' style='display:none;'>"
					+"<td colspan='2'>"
						+"<label for='pilih-nota-dinas' style='display:inline-block;'>Nota Dinas</label>"
						+"<select id='pilih-nota-dinas' class='form-control'>"
							+html_surat_usulan_nota_dinas
						+"</select>"
					+"</td>"
				+"</tr>"
			+"</div>");
		jQuery("#tambahUsulanSsh .modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'btn_submit_verify_ssh\' onclick=\'submit_verify_ssh("+id+")\'>Simpan</button>");
		jQuery("#verify-ssh-no").on("click", function() {
			var check = jQuery(this).is(':checked');
			if(check){
				jQuery(".add-desc-verify-ssh").show();
				jQuery(".catatan-verify-ssh").show();
				jQuery(".add-nota-dinas-verify-ssh").hide();
			}
		});
		jQuery("#verify-ssh-yes").on("click", function() {
			var check = jQuery(this).is(':checked');
			if(check){
				jQuery(".add-desc-verify-ssh").hide();
				jQuery(".catatan-verify-ssh").hide();
			<?php 
				if(in_array("administrator", $user_meta->roles)){
					echo 'jQuery(".add-nota-dinas-verify-ssh").show();';
				}
			?>
			}
		});

		getDataUsulanSshByIdStandarHarga(id);
	}

	/** submit verifikasi usulan ssh */
	function submit_verify_ssh(id_standar_ssh){
		var verify_ssh = jQuery("input[name=\'verify_ssh\']:checked").val();
		var reason_verify_ssh = jQuery("#alasan_verify_ssh").val();
		var nota_dinas = jQuery('#pilih-nota-dinas').val();
		if(verify_ssh == 0 && reason_verify_ssh.trim() == ''){
			alert('Alasan ditolak tidak boleh kosong.');
			return false;
	<?php if(in_array("administrator", $user_meta->roles)): ?>
		}else if(verify_ssh==1 && nota_dinas==''){
			alert('Nota Dinas tidak boleh kosong.');
			return false;
	<?php endif; ?>
		}else{
			jQuery("#wrap-loading").show();
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action': 'submit_verify_ssh',
					'api_key': jQuery("#api_key").val(),
					'verify_ssh': verify_ssh,
					'reason_verify_ssh': reason_verify_ssh,
					'id_ssh_verify_ssh': id_standar_ssh,
					'nota_dinas': nota_dinas
				},
				dataType: 'json',
				beforeSend: function () {
					jQuery('.btn_submit_verify_ssh').attr("disabled","disabled");
				},
				success:function(response){
					jQuery("#wrap-loading").hide();
					if(response.status == 'success'){
						alert(response.message);
						jQuery('#tambahUsulanSsh').modal('hide')
						suratNotaDinasUsulanSSHTable.ajax.reload(function(){
							usulanSSHTable.ajax.reload();
						});
					}else{
						alert("GAGAL! "+response.message);
					}
					jQuery('.submitBtn').removeAttr("disabled");
				}
			});
		}
	}
<?php endif; ?>

	/** submit tambah usulan harga ssh */
	function submitUsulanTambahHargaSshForm(tahun){
		var id_standar_harga = jQuery('#tambah_harga_komp_nama_komponent').val();
		var harga_satuan = jQuery('#tambah_harga_komp_harga_satuan').val();
		var keterangan_lampiran = jQuery('#tambah_harga_komp_keterangan_lampiran').val();
		var lapiran_usulan_ssh_1 = jQuery('#u_lapiran_usulan_harga_ssh_1')[0].files[0];
		var lapiran_usulan_ssh_2 = jQuery('#u_lapiran_usulan_harga_ssh_2')[0].files[0];
		var lapiran_usulan_ssh_3 = jQuery('#u_lapiran_usulan_harga_ssh_3')[0].files[0];
		var id_sub_skpd = jQuery("#tambah_harga_id_sub_unit").val();
		if(harga_satuan.trim() == ''){
			alert('Harga satuan tidak boleh kosong!');
			return false;
		}else if(keterangan_lampiran.trim() == ''){
			alert('Keterangan lampiran tidak boleh kosong!');
			return false;
		}else if(id_standar_harga.trim() == ''){
			alert('id_standar_harga tidak boleh kosong!');
			return false;
		}else if(typeof lapiran_usulan_ssh_1 == 'undefined'){
			alert('Lampiran usulan SSH 1 tidak boleh kosong!');
			return false;
		}else if(typeof lapiran_usulan_ssh_2 == 'undefined'){
			alert('Lampiran usulan SSH 2 tidak boleh kosong!');
			return false;
		}else if(id_sub_skpd == ''){
			alert('Sub unit tidak boleh kosong!');
			return false;
		}else{
			jQuery("#wrap-loading").show();
			let tempData = new FormData();
			tempData.append('action', 'submit_tambah_harga_ssh');
			tempData.append('api_key', jQuery("#api_key").val());
			tempData.append('tahun_anggaran', tahun);
			tempData.append('id_standar_harga', id_standar_harga);
			tempData.append('harga_satuan', harga_satuan);
			tempData.append('keterangan_lampiran', keterangan_lampiran);
			tempData.append('lapiran_usulan_ssh_1', lapiran_usulan_ssh_1);
			tempData.append('lapiran_usulan_ssh_2', lapiran_usulan_ssh_2);
			tempData.append('id_sub_skpd', id_sub_skpd);

			if(typeof lapiran_usulan_ssh_3 !== 'undefined'){
				tempData.append('lapiran_usulan_ssh_3', lapiran_usulan_ssh_3);
			}
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data: tempData,
				processData: false,
				contentType: false,
				cache: false,
				dataType: 'json',
				beforeSend: function () {
					jQuery('.submitBtn').attr("disabled","disabled");
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
						jQuery('#tambahUsulanHargaByKompSSH').modal('hide')
						usulanSSHTable.ajax.reload();
					}else{
						alert(response.message);
					}
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery("#wrap-loading").hide();
				}
			});
		}
	}

	/** Submit tombol usulan akun rekening */
	function submitUsulanTambahAkunSshForm(tahun){
		var id_standar_harga = jQuery('#tambah_akun_komp_nama_komponent').val();
		var id_sub_skpd = jQuery('#tambah_akun_id_sub_unit').val();
		var new_akun = jQuery('#tambah_new_akun_komp').val();
		var keterangan_lampiran = jQuery('#tambah_akun_komp_keterangan_lampiran').val();
		if(new_akun == ''){
			alert('Rekening akun tidak boleh kosong!');
			return false;
		}else if(id_standar_harga.trim() == ''){
			alert('id_standar_harga tidak boleh kosong!');
			return false;
		}else if(id_sub_skpd == ''){
			alert('id_standar_harga tidak boleh kosong!');
			return false;
		}else{
			jQuery("#wrap-loading").show();
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action' : 'submit_tambah_akun_ssh',
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
					'id_standar_harga' : id_standar_harga,
					'new_akun' : new_akun,
					'id_sub_skpd' : id_sub_skpd,
					'keterangan_lampiran' : keterangan_lampiran
				},
				dataType: 'json',
				beforeSend: function () {
					jQuery('.submitBtn').attr("disabled","disabled");
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
						jQuery('#tambahUsulanAkunByKompSSH').modal('hide')
						usulanSSHTable.ajax.reload();
					}else{
						alert(response.message);
					}
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery("#wrap-loading").hide();
				}
			});
		}
	}

	/** edit akun ssh usulan */
	function edit_ssh_usulan(status_jenis_usulan,id,mod='edit'){
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:"post",
			data:{
				'action': "get_data_ssh_usulan_by_id",
				'api_key': jQuery("#api_key").val(),
				'id': id,
				'tahun_anggaran': tahun
			},
			dataType: "json",
			success:function(response){
				jQuery('#wrap-loading').hide();
				if(status_jenis_usulan === 'tambah_akun'){
					jQuery("#tambah_akun_id_sub_unit").val(response.data.id_sub_skpd);
					jQuery("#tambah_akun_komp_kategori").val(response.data.kode_kel_standar_harga+" "+response.data.nama_kel_standar_harga);
					if (jQuery('#tambah_akun_komp_nama_komponent').find("option[value='usulan-" + response.data.id + "']").length == 0) {
					    var newOption = new Option(response.data.nama_standar_harga, 'usulan-'+response.data.id, true, true);
					    jQuery('#tambah_akun_komp_nama_komponent').append(newOption).trigger('change');
					}
					jQuery("#tambah_akun_komp_nama_komponent").val('usulan-'+response.data.id).trigger('change');
					jQuery("#tambah_akun_komp_spesifikasi").val(response.data.spek);
					jQuery("#tambah_akun_komp_satuan").val(response.data.satuan);
					jQuery("#tambah_akun_komp_harga_satuan").val(response.data.harga);
					jQuery('input[name="tambah_harga_komp_jenis_produk"][value="'+response.data.jenis_produk+'"]').prop('checked', true);
					jQuery("#tambah_akun_komp_tkdn").val(response.data.tkdn);
					jQuery("#tambah_akun_komp_keterangan_lampiran").val(response.data.keterangan_lampiran);
					jQuery("#tambah_akun_komp_akun").html(response.table_content_akun);
					jQuery("#tambah_new_akun_komp").attr('id_standar_harga', response.data.id);
					response.data_akun_usulan.map(function(b, i){
						var myText = b.id_akun+" "+b.nama_akun;
						var option = new Option(myText,b.id_akun, true, true);
						jQuery("#tambah_new_akun_komp").append(option).trigger('change');
						jQuery("#tambah_new_akun_komp").trigger({
							type: 'select2:select',
							params: {
								data: b.id_akun
							}
						});
					});

					jQuery("#tambahUsulanAkunByKompSSH .modal-footer").find('.submitBtn').remove();
					jQuery('#tambah_akun_id_sub_unit').prop('disabled', true);
					jQuery('#tambah_akun_komp_kategori').prop('disabled', true);
					jQuery('#tambah_akun_komp_nama_komponent').prop('disabled', true);
					jQuery('#tambah_akun_komp_spesifikasi').prop('disabled', true);
					jQuery('#tambah_akun_komp_satuan').prop('disabled', true);
					jQuery('#tambah_akun_komp_harga_satuan').prop('disabled', true);
					jQuery('input[name="tambah_akun_komp_jenis_produk"][value="'+response.data.jenis_produk+'"]').prop('disabled', true);
					jQuery('#tambah_akun_komp_tkdn').prop('disabled', true);
					jQuery('#tambah_akun_komp_akun').prop('disabled', true);
					if(mod==='edit'){
						jQuery('#tambah_new_akun_komp').prop('disabled', false);
						jQuery('#tambah_akun_komp_keterangan_lampiran').prop('disabled', false);

						jQuery("#tambahUsulanAkunByKompSSH .modal-footer").prepend('<button class=\'btn btn-primary submitBtn\' onclick=\'return false;\'>Simpan</button>');
						jQuery("#tambahUsulanAkunByKompSSH .submitBtn")
							.attr('onclick', 'submitEditTambahAkunUsulanSshForm('+id+', '+tahun+')')
							.attr('disabled', false)
							.text('Simpan');
						jQuery("#tambahUsulanAkunByKompSSH .modal-title").html('Edit Tambah Akun Usulan SSH');
					}else if(mod==='detil'){
						jQuery('#tambah_new_akun_komp').prop('disabled', true);
						jQuery('#tambah_akun_komp_keterangan_lampiran').prop('disabled', true);
						
						jQuery("#tambahUsulanAkunByKompSSH .modal-title").html('Detail Tambah Akun Usulan SSH');
					}
					jQuery('#tambahUsulanAkunByKompSSH').modal('show');
				}else if(status_jenis_usulan === 'tambah_harga'){
					jQuery("#tambah_harga_id_sub_unit").val(response.data.id_sub_skpd).trigger('change');
					jQuery("#tambah_harga_komp_kategori").val(response.data.kode_kel_standar_harga+" "+response.data.nama_kel_standar_harga);
					if (jQuery('#tambah_harga_komp_nama_komponent').find("option[value='usulan-" + response.data.id + "']").length == 0) {
					    var newOption = new Option(response.data.nama_standar_harga, 'usulan-'+response.data.id, true, true);
					    jQuery('#tambah_harga_komp_nama_komponent').append(newOption).trigger('change');
					}
					jQuery("#tambah_harga_komp_nama_komponent").val('usulan-'+response.data.id).trigger('change');
					jQuery("#tambah_harga_komp_spesifikasi").val(response.data.spek);
					jQuery("#tambah_harga_komp_satuan").val(response.data.satuan);
					jQuery("#tambah_harga_komp_harga_satuan").val(response.data.harga);
					jQuery(`#tambah_harga_komp_jenis_produk_${response.data.jenis_produk}`).prop('checked',true);
					jQuery("#tambah_harga_komp_tkdn").val(response.data.tkdn);
					jQuery("#tambah_harga_komp_keterangan_lampiran").val(response.data.keterangan_lampiran);
					jQuery("#tambah_harga_komp_akun").html(response.table_content_akun);

					jQuery("#u_lapiran_usulan_harga_ssh_1").val(null);
					jQuery("#u_lapiran_usulan_harga_ssh_2").val(null);
					jQuery("#u_lapiran_usulan_harga_ssh_3").val(null);

					jQuery("#file_lapiran_usulan_harga_ssh_1").html(response.data.lampiran_1);
					jQuery("#file_lapiran_usulan_harga_ssh_1").attr('target', '_blank');
					jQuery("#file_lapiran_usulan_harga_ssh_1").attr('href', '<?php echo esc_url(plugin_dir_url(__DIR__).'media/ssh/') ?>' + response.data.lampiran_1);

					jQuery("#file_lapiran_usulan_harga_ssh_2").html(response.data.lampiran_2);
					jQuery("#file_lapiran_usulan_harga_ssh_2").attr('target', '_blank');
					jQuery("#file_lapiran_usulan_harga_ssh_2").attr('href', '<?php echo esc_url(plugin_dir_url(__DIR__).'media/ssh/') ?>' + response.data.lampiran_2);

					jQuery("#file_lapiran_usulan_harga_ssh_3").html(response.data.lampiran_3);
					jQuery("#file_lapiran_usulan_harga_ssh_3").attr('target', '_blank');
					jQuery("#file_lapiran_usulan_harga_ssh_3").attr('href', '<?php echo esc_url(plugin_dir_url(__DIR__).'media/ssh/') ?>' + response.data.lampiran_3);

					jQuery("#tambahUsulanHargaByKompSSH .modal-footer").find('.submitBtn').remove();
					jQuery('#tambah_harga_id_sub_unit').prop('disabled', true);
					jQuery('#tambah_harga_komp_kategori').prop('disabled', true);
					jQuery('#tambah_harga_komp_nama_komponent').prop('disabled', true);
					jQuery('#tambah_harga_komp_spesifikasi').prop('disabled', true);
					jQuery('#tambah_harga_komp_satuan').prop('disabled', true);
					jQuery(`#tambah_harga_komp_jenis_produk_${response.data.jenis_produk}`).prop('disabled', true);
					jQuery('#tambah_harga_komp_tkdn').prop('disabled', true);
					jQuery('#tambah_harga_komp_akun').prop('disabled', true);
					if(mod==='edit'){
						jQuery('#tambah_harga_komp_harga_satuan').prop('disabled', false);
						jQuery('#u_lapiran_usulan_harga_ssh_1').show();
						jQuery('#u_lapiran_usulan_harga_ssh_1').parent().find('small').show();
						jQuery('#u_lapiran_usulan_harga_ssh_2').show();
						jQuery('#u_lapiran_usulan_harga_ssh_2').parent().find('small').show();
						jQuery('#u_lapiran_usulan_harga_ssh_3').show();
						jQuery('#u_lapiran_usulan_harga_ssh_3').parent().find('small').show();
						jQuery('#tambah_harga_komp_keterangan_lampiran').prop('disabled', false);

						jQuery("#tambahUsulanHargaByKompSSH .modal-footer").prepend('<button class=\'btn btn-primary submitBtn\' onclick=\'return false;\'>Simpan</button>');
						jQuery("#tambahUsulanHargaByKompSSH .submitBtn")
							.attr('onclick', 'submitEditTambahHargaUsulanSshForm('+id+', '+tahun+')')
							.attr('disabled', false)
							.text('Simpan');
						jQuery("#tambahUsulanHargaByKompSSH .modal-title").html('Edit Tambah Harga Usulan SSH');
					}else if(mod==='detil'){
						jQuery('#tambah_harga_komp_harga_satuan').prop('disabled', true);
						jQuery('#u_lapiran_usulan_harga_ssh_1').hide();
						jQuery('#u_lapiran_usulan_harga_ssh_1').parent().find('small').hide();
						jQuery('#u_lapiran_usulan_harga_ssh_2').hide();
						jQuery('#u_lapiran_usulan_harga_ssh_2').parent().find('small').hide();
						jQuery('#u_lapiran_usulan_harga_ssh_3').hide();
						jQuery('#u_lapiran_usulan_harga_ssh_3').parent().find('small').hide();
						jQuery('#tambah_harga_komp_keterangan_lampiran').prop('disabled', true);
						
						jQuery("#tambahUsulanHargaByKompSSH .modal-title").html('Detail Tambah Harga Usulan SSH');
					}
					jQuery('#tambahUsulanHargaByKompSSH').modal('show');
				}else if(status_jenis_usulan == 'tambah_baru'){
					var myText = response.data_kel_standar_harga_by_id.tipe_kelompok+" "+response.data_kel_standar_harga_by_id.kode_kategori+" "+response.data_kel_standar_harga_by_id.uraian_kategori;
					var option = new Option(myText,response.data_kel_standar_harga_by_id.id_kategori, true, true);	
					jQuery("#u_kategori").append(option).trigger('change');
					jQuery("#u_kategori").trigger({
						type: 'select2:select',
						params: {
							data: response.data_kel_standar_harga_by_id.id_kategori
						}
					});
					jQuery('#u_satuan').val(response.data.satuan).trigger('change');
					jQuery("#u_nama_komponen").val(response.data.nama_standar_harga);
					jQuery("#u_spesifikasi").val(response.data.spek);
					jQuery("#u_harga_satuan").val(response.data.harga);
					jQuery(`input[name=u_jenis_produk][value=${response.data.jenis_produk}]`).prop('checked',true);
					jQuery("#u_tkdn").val(response.data.tkdn);
					jQuery("#u_keterangan_lampiran").val(response.data.keterangan_lampiran);
					
					response.data_akun_usulan.map(function(b, i){
						var myText = b.id_akun+" "+b.nama_akun;
						var option = new Option(myText,b.id_akun, true, true);
						jQuery("#u_akun").append(option).trigger('change');
						jQuery("#u_akun").trigger({
							type: 'select2:select',
							params: {
								data: b.id_akun
							}
						});
					});
					jQuery("#u_lapiran_usulan_ssh_1").val(null);
					jQuery("#u_lapiran_usulan_ssh_2").val(null);
					jQuery("#u_lapiran_usulan_ssh_3").val(null);

					jQuery("#file_lapiran_usulan_ssh_1").html(response.data.lampiran_1);
					jQuery("#file_lapiran_usulan_ssh_1").attr('target', '_blank');
					jQuery("#file_lapiran_usulan_ssh_1").attr('href', '<?php echo esc_url(plugin_dir_url(__DIR__).'media/ssh/') ?>' + response.data.lampiran_1);

					jQuery("#file_lapiran_usulan_ssh_2").html(response.data.lampiran_2);
					jQuery("#file_lapiran_usulan_ssh_2").attr('target', '_blank');
					jQuery("#file_lapiran_usulan_ssh_2").attr('href', '<?php echo esc_url(plugin_dir_url(__DIR__).'media/ssh/') ?>' + response.data.lampiran_2);

					jQuery("#file_lapiran_usulan_ssh_3").html(response.data.lampiran_3);
					jQuery("#file_lapiran_usulan_ssh_3").attr('target', '_blank');
					jQuery("#file_lapiran_usulan_ssh_3").attr('href', '<?php echo esc_url(plugin_dir_url(__DIR__).'media/ssh/') ?>' + response.data.lampiran_3);

					jQuery("#id_sub_skpd").val(response.data.id_sub_skpd).trigger('change');
					jQuery("#tambahUsulanSshModal .modal-footer").find('.submitBtn').remove();
					jQuery('#id_sub_skpd').prop('disabled', true);
					if(mod==='edit'){
						jQuery('#u_kategori').prop('disabled', false);
						jQuery('#u_nama_komponen').prop('disabled', false);
						jQuery('#u_spesifikasi').prop('disabled', false);
						jQuery('#u_satuan').prop('disabled', false);
						jQuery('#u_harga_satuan').prop('disabled', false);
						jQuery('input[name="u_jenis_produk"]').prop('disabled', false);
						jQuery('#u_tkdn').prop('disabled', false);
						jQuery('#u_akun').prop('disabled', false);
						jQuery('#u_lapiran_usulan_ssh_1').show();
						jQuery('#u_lapiran_usulan_ssh_1').parent().find('small').show();
						jQuery('#u_lapiran_usulan_ssh_2').show();
						jQuery('#u_lapiran_usulan_ssh_2').parent().find('small').show();
						jQuery('#u_lapiran_usulan_ssh_3').show();
						jQuery('#u_lapiran_usulan_ssh_3').parent().find('small').show();
						jQuery('#u_keterangan_lampiran').prop('disabled', false);

						jQuery("#tambahUsulanSshModal .modal-footer").prepend('<button class=\'btn btn-primary submitBtn\' onclick=\'return false;\'>Simpan</button>');
						jQuery("#tambahUsulanSshModal .submitBtn")
							.attr('onclick', 'submitEditUsulanSshForm('+id+', '+tahun+')')
							.attr('disabled', false)
							.text('Simpan');
						jQuery("#tambahUsulanSshModal .modal-title").html('Edit Tambah Usulan SSH');
					}else if(mod==='detil'){
						jQuery('#u_kategori').prop('disabled', true);
						jQuery('#u_nama_komponen').prop('disabled', true);
						jQuery('#u_spesifikasi').prop('disabled', true);
						jQuery('#u_satuan').prop('disabled', true);
						jQuery('#u_harga_satuan').prop('disabled', true);
						jQuery('input[name="u_jenis_produk"]').prop('disabled', true);
						jQuery('#u_tkdn').prop('disabled', true);
						jQuery('#u_akun').prop('disabled', true);
						jQuery('#u_lapiran_usulan_ssh_1').hide();
						jQuery('#u_lapiran_usulan_ssh_1').parent().find('small').hide();
						jQuery('#u_lapiran_usulan_ssh_2').hide();
						jQuery('#u_lapiran_usulan_ssh_2').parent().find('small').hide();
						jQuery('#u_lapiran_usulan_ssh_3').hide();
						jQuery('#u_lapiran_usulan_ssh_3').parent().find('small').hide();
						jQuery('#u_keterangan_lampiran').prop('disabled', true);
						
						jQuery("#tambahUsulanSshModal .modal-title").html('Detail Tambah Usulan SSH');
					}
					jQuery('#tambahUsulanSshModal').modal('show');
				}
			}
		})
	}

	function cannot_change_ssh_usulan(option,jenis){
		if(jenis == 'usulan'){
			alert('Tidak bisa '+option+' usulan SSH karena sudah ditahap verifikasi');
		}else if(jenis == 'upload'){
			alert('Tidak bisa '+option+' usulan SSH karena sudah ditahap upload SIPD');
		}
	}

	function submitEditUsulanSshForm(id,tahun){
		var kategori = jQuery('#u_kategori').val();
		var nama_komponen = jQuery('#u_nama_komponen').val();
		var spesifikasi = jQuery('#u_spesifikasi').val();
		var satuan = jQuery('#u_satuan').val();
		var harga_satuan = jQuery('#u_harga_satuan').val();
		var keterangan_lampiran = jQuery('#u_keterangan_lampiran').val();
		var akun = jQuery('#u_akun').val();
		var jenis_produk = jQuery('input[name=u_jenis_produk]:checked').val();
		var tkdn = jQuery('#u_tkdn').val();
		var lapiran_usulan_ssh_1 = jQuery('#u_lapiran_usulan_ssh_1')[0].files[0];
		var lapiran_usulan_ssh_2 = jQuery('#u_lapiran_usulan_ssh_2')[0].files[0];
		var lapiran_usulan_ssh_3 = jQuery('#u_lapiran_usulan_ssh_3')[0].files[0];
		var lapiran_usulan_ssh_1_old = jQuery("#file_lapiran_usulan_ssh_1").text();
		var lapiran_usulan_ssh_2_old = jQuery("#file_lapiran_usulan_ssh_2").text();
		var lapiran_usulan_ssh_3_old = jQuery("#file_lapiran_usulan_ssh_3").text();
		var id_sub_skpd	= jQuery("#id_sub_skpd").val();
		jQuery("#wrap-loading").show();
		if(
			kategori.trim() == ''
			|| nama_komponen.trim() == ''
			|| spesifikasi.trim() == ''
			|| satuan.trim() == ''
			|| harga_satuan.trim() == ''
			|| jenis_produk.trim() == ''
			|| tkdn.trim() == ''
		){
			jQuery("#wrap-loading").hide();
			alert('Harap diisi semua, tidak ada yang kosong.');
			return false;
		}else{
			let tempData = new FormData();
			tempData.append('action', 'submit_edit_usulan_ssh');
			tempData.append('api_key', jQuery("#api_key").val());
			tempData.append('id', id);
			tempData.append('kategori', kategori);
			tempData.append('nama_komponen', nama_komponen);
			tempData.append('spesifikasi', spesifikasi);
			tempData.append('satuan', satuan);
			tempData.append('harga_satuan', harga_satuan);
			tempData.append('jenis_produk', jenis_produk);
			tempData.append('tkdn', tkdn);
			tempData.append('akun', akun);
			tempData.append('tahun_anggaran', tahun);
			tempData.append('keterangan_lampiran', keterangan_lampiran);
			tempData.append('lapiran_usulan_ssh_1_old', lapiran_usulan_ssh_1_old);
			tempData.append('lapiran_usulan_ssh_2_old', lapiran_usulan_ssh_2_old);
			tempData.append('lapiran_usulan_ssh_3_old', lapiran_usulan_ssh_3_old);
			tempData.append('id_sub_skpd',id_sub_skpd);

			if(typeof lapiran_usulan_ssh_1 !== 'undefined'){
				tempData.append('lapiran_usulan_ssh_1', lapiran_usulan_ssh_1);
			}

			if(typeof lapiran_usulan_ssh_2 !== 'undefined'){
				tempData.append('lapiran_usulan_ssh_2', lapiran_usulan_ssh_2);
			}

			if(typeof lapiran_usulan_ssh_3 !== 'undefined'){
				tempData.append('lapiran_usulan_ssh_3', lapiran_usulan_ssh_3);
			}

			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data: tempData,
				processData: false,
				contentType: false,
				cache: false,
				dataType: 'json',
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
						jQuery('#tambahUsulanSshModal').modal('hide')
						usulanSSHTable.ajax.reload();
					}else{
						alert(`GAGAL! ${response.message}`);
					}
					jQuery("#wrap-loading").hide();
				}
			});
		}
	}

	/** submit edit tambah usulan harga ssh */
	function submitEditTambahHargaUsulanSshForm(id,tahun){
		var harga_satuan = jQuery('#tambah_harga_komp_harga_satuan').val();
		var keterangan_lampiran = jQuery('#tambah_harga_komp_keterangan_lampiran').val();
		var lapiran_usulan_ssh_1 = jQuery('#u_lapiran_usulan_harga_ssh_1')[0].files[0];
		var lapiran_usulan_ssh_2 = jQuery('#u_lapiran_usulan_harga_ssh_2')[0].files[0];
		var lapiran_usulan_ssh_3 = jQuery('#u_lapiran_usulan_harga_ssh_3')[0].files[0];
		var id_sub_skpd = jQuery('#u_id_sub_skpd').val();

		if(!id){
			alert('ID tidak tidak boleh kosong!');
			return false;
		}else if(harga_satuan.trim() == ''){
			alert('Harga tidak tidak boleh kosong!');
			return false;
		}else if(keterangan_lampiran.trim() == ''){
			alert('Keterangan tidak tidak boleh kosong!');
			return false;
		}else if(id_sub_skpd == ''){
			alert('Sub unit tidak boleh kosong!');
			return false;
		}else{
			let tempData = new FormData();
			tempData.append('action', 'submit_edit_tambah_harga_ssh');
			tempData.append('api_key', jQuery("#api_key").val());
			tempData.append('tahun_anggaran', tahun);
			tempData.append('id', id);
			tempData.append('harga_satuan', harga_satuan);
			tempData.append('keterangan_lampiran', keterangan_lampiran);
			tempData.append('id_sub_skpd', id_sub_skpd);
			if(typeof lapiran_usulan_ssh_1 !== 'undefined'){
				tempData.append('lapiran_usulan_ssh_1', lapiran_usulan_ssh_1);
			}
			if(typeof lapiran_usulan_ssh_2 !== 'undefined'){
				tempData.append('lapiran_usulan_ssh_2', lapiran_usulan_ssh_2);
			}
			if(typeof lapiran_usulan_ssh_3 !== 'undefined'){
				tempData.append('lapiran_usulan_ssh_3', lapiran_usulan_ssh_3);
			}
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data: tempData,
				processData: false,
				contentType: false,
				cache: false,
				dataType: 'json',
				beforeSend: function () {
					jQuery('.submitBtn').attr("disabled","disabled");
					jQuery('#wrap-loading').show();
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
						jQuery('#tambahUsulanHargaByKompSSH').modal('hide');
						usulanSSHTable.ajax.reload();
					}else{
						alert(response.message);
					}
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery("#wrap-loading").hide();
				}
			});
		}
	}

	/** submit edit tambah akun ssh */
	function submitEditTambahAkunUsulanSshForm(id, tahun){
		var new_akun = jQuery('#tambah_new_akun_komp').val();
		var keterangan = jQuery('#tambah_akun_komp_keterangan_lampiran').val();
		jQuery("#wrap-loading").show();
		if(new_akun == ''){
			jQuery("#wrap-loading").hide();
			alert('Harap diisi semua, tidak ada yang kosong.');
			return false;
		}else{
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action' : 'submit_edit_tambah_akun_ssh',
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
					'id' : id,
					'new_akun' : new_akun,
					'keterangan' : keterangan
				},
				dataType: 'json',
				beforeSend: function () {
					jQuery('.submitBtn').attr("disabled","disabled");
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
						jQuery('#tambahUsulanAkunByKompSSH').modal('hide')
						usulanSSHTable.ajax.reload();
					}else{
						alert(response.message);
					}
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery("#wrap-loading").hide();
				}
			});
		}
	}

	function delete_ssh_usulan(id){
		let confirmDelete = confirm("Apakah anda yakin akan menghapus usulan SSH?");
		if(confirmDelete){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action': 'submit_delete_usulan_ssh',
					'api_key': jQuery("#api_key").val(),
					'id': id,
					'tahun_anggaran': tahun
				},
				dataType: 'json',
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil dihapus!.');
						usulanSSHTable.ajax.reload();
					}else{
						alert(`GAGAL! ${response.message}`);
					}
					jQuery('#wrap-loading').hide();
				}
			});
		}
	}

	function delete_akun_ssh_usulan(id, id_rek){
		let confirmDelete = confirm("Apakah anda yakin akan menghapus rekening akun usulan SSH?");
		if(confirmDelete){
			jQuery("#wrap-loading").show();
			if(id_standar_harga == '' || id == ''){
				jQuery("#wrap-loading").hide();
				alert("GAGAL, coba ulangi lagi!")
			}else{
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:'post',
					data:{
						'action': 'submit_delete_akun_usulan_ssh',
						'api_key': jQuery("#api_key").val(),
						'id': id_standar_harga,
						'tahun_anggaran': tahun,
						'id_rek_akun_usulan_ssh': id_rek
					},
					dataType: 'json',
					success:function(response){
						jQuery("#wrap-loading").hide();
						if(response.status == 'success'){
							alert('Data berhasil dihapus!.');
							jQuery(`#rek_akun_${id}`).remove()
							usulanSSHTable.ajax.reload();
						}else{
							alert(`GAGAL! ${response.message}`);
						}
					}
				});
			}
		}
	}

	// Checkbox checked
    function checkcheckbox(){

        var length = jQuery('.delete_check').length;

        var totalchecked = 0;
        jQuery('.delete_check').each(function(){
            if(jQuery(this).is(':checked')){
                totalchecked+=1;
            }
        }); 

        if(totalchecked == length){
            jQuery("#checkall").prop('checked', true);
        }else{
            jQuery('#checkall').prop('checked', false);
        }
    }

	// Multi action
	function action_check_data_usulan_ssh(){
		var data_action = jQuery('#multi_select_action').val();

		var check_id_arr = [];

		jQuery("input[type='checkbox'].delete_check:checked").each(function () {
			check_id_arr.push(jQuery(this).val());
		});
		jQuery("#wrap-loading").show();

		if(check_id_arr.length > 0){

			if(data_action == 'delete'){
				var confirmdelete = confirm("Apakah kamu yakin hapus data?");
				if (confirmdelete == true) {
					jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type:"post",
						data:{
							'action' : "submit_delete_check_usulan_ssh",
							'api_key' : jQuery("#api_key").val(),
							'tahun_anggaran' : tahun,
							'check_id_arr'	: check_id_arr
						},
						dataType: "json",
						success:function(response){
							jQuery("#wrap-loading").hide();
							if(response.status == 'success'){
								alert('Data berhasil dihapus.');
								usulanSSHTable.ajax.reload();
							}else{
								alert(`GAGAL! ${response.message}`);
							}
						}
					})
				}else{
					jQuery("#wrap-loading").hide();
				}
			}else if(data_action == 'approve'){
				var confirmapprove = confirm("Apakah kamu yakin menyetujui data?");
				if (confirmapprove == true) {
					jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type:"post",
						data:{
							'action' : "submit_approve_check_usulan_ssh",
							'api_key' : jQuery("#api_key").val(),
							'tahun_anggaran' : tahun,
							'check_id_arr'	: check_id_arr,
							'alasan': '',
							'status': data_action
						},
						dataType: "json",
						success:function(response){
							jQuery("#wrap-loading").hide();
							if(response.status == 'success'){
								alert('Data berhasil disetujui.');
								usulanSSHTable.ajax.reload();
							}else{
								alert(`GAGAL! ${response.message}`);
							}
						}
					})
				}else{
					jQuery("#wrap-loading").hide();
				}
			}else if(data_action == 'notapprove'){
				var reason = prompt("Alasan:", "");
				if(reason != null || reason != ""){
					var confirmnotapprove = confirm("Apakah kamu yakin menolak data?");
					if (confirmnotapprove == true) {
						jQuery.ajax({
							url: "<?php echo admin_url('admin-ajax.php'); ?>",
							type:"post",
							data:{
								'action' : "submit_approve_check_usulan_ssh",
								'api_key' : jQuery("#api_key").val(),
								'tahun_anggaran' : tahun,
								'check_id_arr'	: check_id_arr,
								'alasan': reason,
								'status': data_action
							},
							dataType: "json",
							success:function(response){
								jQuery("#wrap-loading").hide();
								if(response.status == 'success'){
									alert('Data berhasil ditolak.');
									usulanSSHTable.ajax.reload();
								}else{
									alert(`GAGAL! ${response.message}`);
								}
							}
						})
					}
				}
				jQuery("#wrap-loading").hide();
			}else{
				jQuery("#wrap-loading").hide();
				alert('Tindakan massal tidak dipilih!');
			}

		}else if(check_id_arr.length == 0){
			jQuery("#wrap-loading").hide();
			alert('Tidak ada data dipilih!');
		}
	}

	function action_filter_data_usulan_ssh(){
		usulanSSHTable.draw();
	}

	function readMore(btn){
		let post = btn.parentElement;
		post.querySelector(".dots").classList.toggle("hide");
		post.querySelector(".more").classList.toggle("hide");
		btn.textContent == "more" ? btn.textContent = "less" : btn.textContent = "more";
	}

	function checkFileType(that){
		let allowExt = ['jpg', 'jpeg', 'png', 'pdf'];
		let file = jQuery(that).val();
		let ext = file.split('.').pop();

		if(!allowExt.includes(ext)){
			alert('Lampiran wajib ber-type png, jpeg, jpg, atau pdf');
			jQuery(that).val(null);
			return false;
		}

		if(that.files[0].size > 1048576){ // default 1MB
			alert('Ukuran File Lampiran terlalu besar!');
			jQuery(that).val(null);
			return false;
		}
		
		return true;
	}

	function getDataUsulanSshByIdStandarHarga(id){
		return new Promise(function(resolve, reject){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
			    type: "post",
			    data: {
			        "action": "get_data_usulan_ssh_by_id_standar_harga",
			        "api_key": jQuery("#api_key").val(),
			        "id": id
			    },
			    dataType: "json",
			    success: function(res){
			    	if(res.status){
			    		let user='';
			    		let catatan='';
			    		if(res.role==='administrator'){
			    			user = 'Verifikator 2';
			    			if(res.data.keterangan_status_tapdkeu != null){
			    				catatan += '<ol>';
			    				res.data.keterangan_status_tapdkeu.split(' | ').map(function(b, i){
			    					catatan += '<li>'+b+'</li>';
			    				});
			    				catatan += '</ol>';
			    			}
			    		}else if(res.role==='tapd_keu'){
			    			user = 'Verifikator 1';
			    			if(res.data.keterangan_status_admin != null){
			    				catatan += '<ol>';
			    				res.data.keterangan_status_admin.split(' | ').map(function(b, i){
			    					catatan += '<li>'+b+'</li>';
			    				});
			    				catatan += '</ol>';
			    			}
			    		}
			    		let html=""
			    			+"<tr class='catatan-verify-ssh' style='display:none'>"
			    				+"<td colspan='2'>"
			    					+"<label for='catatan_verify_ssh' style='display:inline-block;'>Alasan "+user+"</label><br><span class='medium-bold-2' id='catatan_verify_ssh'>"+catatan+"</span>"
			    				+"</td>"
			    			+"</tr>";
			    		jQuery(".add-desc-verify-ssh").after(html);
			    		jQuery("#pilih-nota-dinas").val(res.data.no_nota_dinas);
			    	<?php
			    		if(in_array("administrator", $user_meta->roles)){
			    			echo 'jQuery(".add-nota-dinas-verify-ssh").show();';
			    		}
			    	?>
			    	}
					jQuery('#wrap-loading').hide();
			    	resolve();
			    }
			});
		})
	}

	function buat_surat_usulan(tahun) {
		jQuery("#tambahSuratUsulan #u_surat_usulan_ssh").val("").closest('.row').hide();
		jQuery("#tambahSuratUsulan #catatan_verifikator").val("").closest('.row').hide();
        var ids = [];
        var status = false;
        jQuery('.delete_check').each(function(){
        	if(
        		jQuery(this).is(':checked')
	            && jQuery(this).attr('no-surat') !== ''
	        ){
        		alert('Salah satu usulan standar harga sudah memiliki nomor surat, mohon dicermati ulang!');
        		return false;
        	}else{
        		status = true;
        		if(
	            	jQuery(this).is(':checked')
	            	&& jQuery(this).attr('no-surat') == ''
	            ){
	            	var tr = jQuery(this).closest('tr');
	            	var data = {
	            		id: jQuery(this).val(),
	            		kelompok: tr.find('>td').eq(1).html(),
	            		komponen: tr.find('>td').eq(2).html(),
	            		spesifikasi: tr.find('>td').eq(3).html(),
	            		harga: tr.find('>td').eq(4).html(),
	            		rekening: '',
	            		jenis: tr.find('>td').eq(9).html()
	            	}
	                ids.push(data);
	            }
        	}
        });

        if(status){
	        if(ids.length == 0){
	        	alert('Harap pilih dulu usulan standar harga! Pastikan pilih data usulan yang belum pernah dibuat surat usulan.');
	        }else{
	        	jQuery('#wrap-loading').show();
	        	var data = '';
	        	var data_ids = [];
	        	ids.map(function(b, i){
	        		data_ids.push(b.id);
	        		data += ''
	        			+'<tr>'
	        				+'<td>'+b.kelompok+'</td>'
	        				+'<td>'+b.komponen+'</td>'
	        				+'<td>'+b.spesifikasi+'</td>'
	        				+'<td>'+b.harga+'</td>'
	        				+'<td>'+b.rekening+'</td>'
	        				+'<td>'+b.jenis+'</td>'
	        			+'</tr>'
	        	});
	        	jQuery('.type-sumber-ssh').prop('checked', false);
	        	jQuery('#surat_skpd').val('');
	        	jQuery('#catatan_surat').val('');
	        	jQuery('#catatan_verifikator').val('');
	        	jQuery('#nomor_surat').val(jQuery('#nomor_surat').attr('placeholder'));
	        	jQuery('#ids_surat_usulan').val(data_ids);
	        	jQuery('#tbody_data_usulan').html(data);
	        	jQuery('#tambahSuratUsulan').modal('show');
				jQuery('#jenis_survey').prop('checked', true);
        	<?php 
        		if(
					in_array("administrator", $user_meta->roles)
					|| in_array("tapd_keu", $user_meta->roles)
				){
        			echo "jQuery('#tambahSuratUsulan #catatan_verifikator').prop('disabled', false);";
        			echo "jQuery('#tambahSuratUsulan #catatan_surat').prop('disabled', true);";
        		}else{
        			echo "jQuery('#tambahSuratUsulan #catatan_verifikator').prop('disabled', true);";
        			echo "jQuery('#tambahSuratUsulan #catatan_surat').prop('disabled', false);";
        		}
        	?>
	        	jQuery('#wrap-loading').hide();
	        }
        }else{
			alert('Usulan standar harga tidak ditemukan! Pastikan ada data usulan yang akan dibuat surat usulan.');
		}

	}

	function submitSuratUsulan(){
		var nomor_surat = jQuery('#nomor_surat').val();
		if(nomor_surat == ''){
			return alert('Nomor surat tidak boleh kosong!');
		}
		var ids = jQuery('#ids_surat_usulan').val();
		if(ids == ''){
			return alert('Usulan SSH tidak boleh kosong!');
		}
		var idskpd = jQuery('#surat_skpd').val();
		if(idskpd == ''){
			return alert('Perangkat daerah tidak boleh kosong!');
		}
		var catatan = jQuery('#catatan_surat').val();
		var catatan_verifikator = jQuery('#catatan_verifikator').val();
		var acuanSsh = jQuery('#tambahSuratUsulan input:checkbox:checked').map(function() {
		    return this.value;
		}).get();
		var ubah = jQuery("#tambahSuratUsulan input[name='ubah_id']").val();
		var lapiran_surat = jQuery('#u_surat_usulan_ssh')[0].files[0];
		let tempData = new FormData();
		jQuery('#wrap-loading').show();
		tempData.append('action', 'simpan_surat_usulan_ssh');
		tempData.append('api_key', jQuery("#api_key").val());
		tempData.append('tahun_anggaran', tahun);
		tempData.append('nomor_surat', nomor_surat);
		tempData.append('catatan', catatan);
		tempData.append('idskpd', idskpd);
		tempData.append('ids', ids);
		tempData.append('acuanSsh', acuanSsh);

		if(typeof lapiran_surat != 'undefined'){
			tempData.append('lapiran_surat', lapiran_surat);
		}
		if(ubah != ''){
			tempData.append('ubah', ubah);
			tempData.append('catatan_verifikator', catatan_verifikator);
		}

		jQuery.ajax({
			url: ajax.url,
		    type: "post",
			data: tempData,
			dataType: 'json',
		    processData: false,
		    contentType: false,
		    cache: false,
		    success: function(res){
		    	alert(res.message);
				jQuery("#wrap-loading").hide();
		    	if(res.status == 'success'){
					suratUsulanSSHTable.ajax.reload();
    				jQuery('#tambahSuratUsulan').modal('hide');
				}
		    }
		});
	}

	function edit_surat_usulan(that){
		var tr = jQuery(that).parent().parent();
		var id = jQuery(that).data('id');
		var idskpd = jQuery(that).data('idskpd');
		var nomor_surat = jQuery(that).data('nomorsurat');
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
		    type: "post",
		    data: {
		        "action": "get_data_usulan_ssh_surat_by_id",
		        "api_key": jQuery("#api_key").val(),
		        "tahun_anggaran": tahun,
		        "nomor_surat": nomor_surat,
		        "id": id
		  	},
		    dataType: "json",
		    success: function(res){
				jQuery("#wrap-loading").hide();
		    	if(res.status == 'success'){
					jQuery("#tambahSuratUsulan input[name='ubah_id']").val(res.surat.id);
					jQuery("#tambahSuratUsulan #surat_skpd").val(res.surat.idskpd);
					jQuery("#tambahSuratUsulan #nomor_surat").val(res.surat.nomor_surat);
					jQuery("#tambahSuratUsulan #catatan_surat").val(res.surat.catatan);
					jQuery("#tambahSuratUsulan #catatan_verifikator").val(res.surat.catatan_verifikator);
					jQuery("#tambahSuratUsulan #catatan_verifikator").val("").closest('.row').show();
					jQuery("#tambahSuratUsulan #u_surat_usulan_ssh").val("").closest('.row').show();
					var file = '';
					if(res.surat.nama_file){
						file = "<a href='<?php echo esc_url(plugin_dir_url(__DIR__).'media/ssh/') ?>"+res.surat.nama_file+"' target='_blank'>"+res.surat.nama_file+"</a>";
					}
					jQuery("#tambahSuratUsulan #file_surat_usulan_ssh").html(file);

					if(res.surat.jenis_survey == 1){
						jQuery("#jenis_survey").prop('checked', true);
					}else{
						jQuery("#jenis_survey").prop('checked', false);
					}

					if(res.surat.jenis_juknis == 2){
						jQuery("#jenis_juknis").prop('checked', true);
					}else{
						jQuery("#jenis_juknis").prop('checked', false);
					}
					
					var ids = [];
					res.data.map(function(b, i){
		            	var data = {
		            		id: b.id,
		            		kelompok: b.nama_kel_standar_harga,
		            		komponen: b.nama_standar_harga,
		            		spesifikasi: b.spek,
		            		harga: b.harga,
		            		rekening: '<ul style="margin-bottom: 0; margin-left: 20px;">',
		            		jenis: b.status_jenis_usulan
		            	}
		            	b.rekening.map(function(bb, ii){
		            		data.rekening += '<li>'+bb.nama_akun+'</li>';
		            	})
		            	data.rekening += '</ul>';
		                ids.push(data);
			        });

		        	var data = '';
		        	var data_ids = [];
			        if(ids.length == 0){
			        	alert('Usulan standar harga dengan nomor surat '+nomor_surat+' tidak ditemukan');
			        }else{
			        	ids.map(function(b, i){
			        		data_ids.push(b.id);
			        		data += ''
			        			+'<tr>'
			        				+'<td>'+b.kelompok+'</td>'
			        				+'<td>'+b.komponen+'</td>'
			        				+'<td>'+b.spesifikasi+'</td>'
			        				+'<td>'+b.harga+'</td>'
			        				+'<td>'+b.rekening+'</td>'
			        				+'<td>'+b.jenis+'</td>'
			        			+'</tr>'
			        	});
			        }
		        	jQuery('#ids_surat_usulan').val(data_ids);
		        	jQuery('#tbody_data_usulan').html(data);
	        	<?php 
	        		if(
						in_array("administrator", $user_meta->roles)
						|| in_array("tapd_keu", $user_meta->roles)
					){
	        			echo "jQuery('#tambahSuratUsulan #catatan_verifikator').prop('disabled', false);";
	        			echo "jQuery('#tambahSuratUsulan #catatan_surat').prop('disabled', true);";
	        		}else{
	        			echo "jQuery('#tambahSuratUsulan #catatan_verifikator').prop('disabled', true);";
	        			echo "jQuery('#tambahSuratUsulan #catatan_surat').prop('disabled', false);";
	        		}
	        	?>
		        	jQuery('#tambahSuratUsulan').modal('show');
		        	jQuery('#wrap-loading').hide();
				}else{
		    		alert(res.message);
				}
		    }
		});
	}

	function edit_nota_dinas(that){
		var tr = jQuery(that).parent().parent();
		var id = jQuery(that).data('id');
		var nomor_surat = jQuery(that).data('nomorsurat');
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
		    type: "post",
		    data: {
		        "action": "get_data_nota_dinas_by_id",
		        "api_key": jQuery("#api_key").val(),
		        "tahun_anggaran": tahun,
		        "nomor_surat": nomor_surat,
		        "id": id
		  	},
		    dataType: "json",
		    success: function(res){
				jQuery("#wrap-loading").hide();
		    	if(res.status == 'success'){
					jQuery("#tambahNotaDinasModal input[name='ubah_id']").val(res.nota_dinas.id);
					jQuery("#tambahNotaDinasModal #nomor_surat").val(res.nota_dinas.nomor_surat);
					jQuery("#tambahNotaDinasModal #catatan_surat_nota_dinas").val(res.nota_dinas.catatan);
					jQuery("#tambahNotaDinasModal #tbody_data_usulan_nota_dinas").val("").closest('.row').show();
					
					var ids = [];
					res.data.map(function(b, i){
		            	var data = {
		            		id: b.id,
		            		kelompok: b.nama_kel_standar_harga,
		            		komponen: b.nama_standar_harga,
		            		spesifikasi: b.spek,
		            		harga: b.harga,
		            		rekening: '',
		            		nomor_surat: b.nomor_surat,
		            		jenis: b.status_jenis_usulan
		            	}
		                ids.push(data);
			        });

		        	var data = '';
		        	var data_ids = [];
		        	ids.map(function(b, i){
		        		data_ids.push(b.id);
		        		data += ''
		        			+'<tr>'
		        				+'<td>'+b.kelompok+'</td>'
		        				+'<td>'+b.komponen+'</td>'
		        				+'<td>'+b.spesifikasi+'</td>'
		        				+'<td>'+b.harga+'</td>'
		        				+'<td>'+b.rekening+'</td>'
		        				+'<td>'+b.nomor_surat+'</td>'
		        				+'<td>'+b.jenis+'</td>'
		        			+'</tr>';
		        	});
		        	jQuery('.submitBtn').prop("disabled", false);
		        	jQuery('#ids_nota_dinas').val(data_ids);
		        	jQuery('#tbody_data_usulan_nota_dinas').html(data);
		        	jQuery('#tambahNotaDinasModal').modal('show');
			        jQuery('#wrap-loading').hide();
				}else{
		    		alert(res.message);
				}
		    }
		});
	}

	function generateSuratUsulanSsh(that){
		let url = jQuery(that).attr('url');
		jQuery('#tambahUsulanSsh').modal('show');
		jQuery("#tambahUsulanSshLabel").html("Pilih Acuan Penyusunan SSH");
		jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-lg modal-xl");
		jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-md");
		jQuery("#tambahUsulanSsh .modal-body").html(
			"<div class=\'akun-ssh-verify\'>"+
				"<table>"+
					"<tr>"+
						"<td><input class=\'type-sumber-ssh\' id=\'jenis_survey\' name=\'jenis[]\' value=\'1\' type=\'checkbox\' ><label for=\'verify-ssh-yes\'>Survey harga pasar yang telah kami lakukan secara mandiri.</label></td>"+
					"</tr>"+
					"<tr>"+
						"<td><input class=\'type-sumber-ssh\' id=\'jenis_juknis\' name=\'jenis[]\' value=\'2\' type=\'checkbox\'><label for=\'verify-ssh-no\'>Petunjuk Teknis yang kami terima dari kementrian/provinsi.</label></td>"+
					"</tr>"+
					"<tr>"+
						"<td colspan='2'><small style='color:red'>* Pilih salah satu atau keduanya. </small></td>"+
					"</tr>"+
				"</table>"+
			"</div>");
		jQuery("#tambahUsulanSsh .modal-footer").html("<a target='_blank' style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\' btn btn-primary\' url='"+url+"' onclick='openGenerate(this)'>Cetak</a>");
	}

	function openGenerate(that){
		let url = jQuery(that).attr('url');
		let checkedValues = jQuery('#tambahUsulanSsh input:checkbox:checked').map(function() {
		    return this.value;
		}).get().join(',');

		if(checkedValues.length > 0){
			url = url+'&type='+checkedValues;
		}
		window.open(url, '_blank');
	}

	function hapus_nota_dinas(that){
		var id = jQuery(that).attr('data-id');
		var nomor_surat = jQuery(that).attr('data-nomorsurat');
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
		    type: "post",
		    data: {
		        "action": "get_data_nota_dinas_by_id",
		        "api_key": jQuery("#api_key").val(),
		        "nomor_surat": nomor_surat,
		        "tahun_anggaran": tahun,
		        "id": id
		  	},
		    dataType: "json",
		    success: function(res){
				jQuery('#wrap-loading').hide();
				if(res.status != 'error'){
					var cek_ids = [];
					res.data.map(function(b, i){
						if(b.status == 'approved'){
							cek_ids.push(b);
						}
					});
					if(cek_ids.length >= 1){
						alert('Tidak bisa hapus Nota Dinas karena sudah ada usulan Standar Harga yang disetujui!');
					}else{
						if(confirm("Apakah anda yakin untuk menghapus Nota Dinas ini? Data tidak bisa dikembalikan.")){
							jQuery('#wrap-loading').show();
							jQuery.ajax({
								url: ajax.url,
							    type: "post",
							    data: {
							        "action": "hapus_nota_dinas",
							        "api_key": jQuery("#api_key").val(),
							        "nomor_surat": nomor_surat,
							        "tahun_anggaran": tahun,
							        "id": id
							  	},
							    dataType: "json",
							    success: function(res){
									jQuery('#wrap-loading').hide();
									alert(res.message);
									if(res.status != 'error'){
										suratNotaDinasUsulanSSHTable.ajax.reload();
									}
								}
							});
						}
					}
				}else{
		    		alert(res.message);
				}
		    }
		});
	}

	function hapus_surat_usulan(that){
		var id = jQuery(that).attr('data-id');
		var nomor_surat = jQuery(that).attr('data-nomorsurat');
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: ajax.url,
		    type: "post",
		    data: {
		        "action": "get_data_usulan_ssh_surat_by_id",
		        "api_key": jQuery("#api_key").val(),
		        "nomor_surat": nomor_surat,
		        "tahun_anggaran": tahun,
		        "id": id
		  	},
		    dataType: "json",
		    success: function(res){
				jQuery('#wrap-loading').hide();
				if(res.status != 'error'){
					var cek_ids = [];
					res.data.map(function(b, i){
						if(b.status == 'approved'){
							cek_ids.push(b);
						}
					});
					if(cek_ids.length >= 1){
						alert('Tidak bisa hapus surat usulan karena sudah ada usulan Standar Harga yang disetujui!');
					}else{
						if(confirm("Apakah anda yakin untuk menghapus surat usulan ini? Data tidak bisa dikembalikan.")){
							jQuery('#wrap-loading').show();
							jQuery.ajax({
								url: ajax.url,
							    type: "post",
							    data: {
							        "action": "hapus_surat_usulan_ssh",
							        "api_key": jQuery("#api_key").val(),
							        "nomor_surat": nomor_surat,
							        "tahun_anggaran": tahun,
							        "id": id
							  	},
							    dataType: "json",
							    success: function(res){
									jQuery('#wrap-loading').hide();
									alert(res.message);
									if(res.status != 'error'){
										suratUsulanSSHTable.ajax.reload();
									}
								}
							});
						}
					}
				}else{
		    		alert(res.message);
				}
		    }
		});
	}
	
	function filter_surat_usulan(nomor_surat){
		jQuery('#search_filter_surat').val(nomor_surat).trigger('change');
		jQuery('html, body').animate({
	        scrollTop: jQuery("#usulan_ssh_table").offset().top
	    }, 1000);
		return false;
	}
	
	function filter_nota_dinas(nomor_surat){
		jQuery('#search_nota_dinas_filter_surat').val(nomor_surat).trigger('change');
		jQuery('html, body').animate({
	        scrollTop: jQuery("#usulan_ssh_table").offset().top
	    }, 1000);
		return false;
	}

	function cetak_usulan(){
		var status = jQuery('#search_filter_action').val();
		if(status == ''){
			return alert('Filter status usulan harus dipilih!');
		}
		var id_skpd = jQuery('#search_filter_action_opd').val();
		var no_surat = jQuery('#search_filter_surat').val();
		var nota_dinas = jQuery('#search_nota_dinas_filter_surat').val();
		var tipe_laporan = prompt('Pilih tipe laporan: 1=Laporan excel upload SIPD, 2=Laporan WP-SIPD', 1);
		var url = '<?php echo $url_cetak_usulan; ?>'+'&tahun='+tahun+'&status='+status+'&id_skpd='+id_skpd+'&no_surat='+no_surat+'&nota_dinas='+nota_dinas+'&tipe_laporan='+tipe_laporan;
		window.open(url, '_blank').focus();
	}

	function submit_ssh_usulan(id,tahun){
	    if(confirm('Apakah anda yakin mengirim data ini ?')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action': 'submit_ssh_usulan_by_id',
					'api_key': jQuery("#api_key").val(),
					'id': id,
					'tahun_anggaran': tahun
				},
				dataType: 'json',
	            success: function(res){
	                jQuery('#wrap-loading').hide();
	                alert(res.message);
	                if(res.status == 'success'){
	                    usulanSSHTable.ajax.reload();
	                }
	            }
			});
		}
	}
</script> 

