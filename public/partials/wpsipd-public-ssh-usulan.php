<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$input = shortcode_atts( array(
	'tahun_anggaran' => '2022'
), $atts );

global $wpdb;

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
				// 
			}
		}
	}
}else if(
	in_array("administrator", $user_meta->roles)
	|| in_array("tapd_keu", $user_meta->roles)
){
	$skpd_mitra = $wpdb->get_results($wpdb->prepare("
		SELECT 
			nama_skpd, 
			id_skpd, 
			kode_skpd 
		from data_unit 
		where active=1 
			and tahun_anggaran=%d
		group by id_skpd", $input['tahun_anggaran']), ARRAY_A);
	foreach ($skpd_mitra as $k => $v) {
		// 
	}
}

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
	    width: 100px;
	}
	ul.td-aksi li {
	    list-style: none;
	    margin: 2px;
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
</style>
<div class="cetak">
	<div style="padding: 10px;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<input type="hidden" value="<?php echo $input['tahun_anggaran']; ?>" id="tahun_anggaran">
		<h1 class="text-center">Data usulan satuan standar harga (SSH)<br>tahun anggaran <?php echo $input['tahun_anggaran']; ?></h1>
		<h2 class="text-center">Surat Usulan Standar Harga</h2>
		<table id="surat_usulan_ssh_table" class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">Nomor Surat</th>
					<th class="text-center">File</th>
					<th class="text-center">Waktu Dibuat</th>
					<th class="text-center">Jumlah Usulan</th>
					<th class="text-center">Catatan</th>
				</tr>
			</thead>
			<tbody id="data_body_surat" class="data_body_ssh_surat">
			</tbody>
		</table>
		<div style="margin-bottom: 25px;">
			<button class="btn btn-primary tambah_ssh" disabled onclick="tambah_new_ssh(<?php echo $input['tahun_anggaran']; ?>);">Tambah Item SSH</button>
			<button class="btn btn-primary tambah_new_ssh" disabled onclick="get_data_by_name_komponen_ssh('harga',<?php echo $input['tahun_anggaran']; ?>)">Tambah Harga SSH</button>
			<button class="btn btn-primary tambah_new_ssh" disabled onclick="get_data_by_name_komponen_ssh('akun',<?php echo $input['tahun_anggaran']; ?>)">Tambah Akun SSH</button>
			<button class="btn btn-warning" onclick="buat_surat_usulan(<?php echo $input['tahun_anggaran']; ?>)">Buat Surat Usulan</button>
		</div>
		<table id="usulan_ssh_table" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center"><input type="checkbox" id="checkall"></th>
					<th class="text-center">Kode Komponen</th>
					<th class="text-center">Uraian Komponen</th>
					<th class="text-center">Spesifikasi Satuan</th>
					<th class="text-center">Harga Satuan</th>
					<th class="text-center">Keterangan</th>
					<th class="text-center">Verifikator 1</th>
					<th class="text-center">Verifikator 2</th>
					<th class="text-center">Status</th>
					<th class="text-right">Aksi</th>
				</tr>
			</thead>
			<tbody id="data_body" class="data_body_ssh">
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="tambahSuratUsulan" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Form Surat Usulan</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<label class="col-md-2" for="nomor_surat" style="display:inline-block">Kategori</label>
					<div class="col-md-10">
						<input type="text" id="nomor_surat" class="form-control" placeholder="kode_surat/no_urut/kode_opd/tahun" value="kode_surat/no_urut/kode_opd/<?php echo $input['tahun_anggaran']; ?>">
					</div>
				</div>
			</div> 
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="submitSuratUsulan(<?php echo $input['tahun_anggaran']; ?>)">Simpan</button>
				<button type="button" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="tambahUsulanSsh" role="dialog" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
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
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="tambahUsulanHargaByKompSSHLabel">Tambah Harga usulan SSH</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div>
					<label for="tambah_harga_komp_kategori" style="display:inline-block">Kategori</label>
					<input type="text" id="tambah_harga_komp_kategori" style="display:block;width:100%;" placeholder="Kategori" disabled>
				</div>
				<div>
					<label for="tambah_harga_komp_nama_komponent" style="display:inline-block">Nama Komponen</label>
					<select id="tambah_harga_komp_nama_komponent" class="js-example-basic-single" style="display:block;width:100%;" placeholder="Nama Komponen"></select>
					<input type="text" id="tambah_harga_show_komp_nama" class="hide" style="width:100%;" placeholder="Nama Komponen" disabled>
				</div>
				<div>
					<label for="tambah_harga_komp_spesifikasi" style="display:inline-block">Spesifikasi</label>
					<input type="text" id="tambah_harga_komp_spesifikasi" style="display:block;width:100%;" placeholder="Spesifikasi" disabled>
				</div>
				<div>
					<label for="tambah_harga_komp_satuan" style="display:inline-block">Satuan</label>
					<input type="text" id="tambah_harga_komp_satuan" style="display:block;width:100%;" placeholder="Satuan" disabled>
				</div>
				<div>
					<label for="tambah_harga_komp_harga_satuan" style="display:inline-block">Harga Satuan</label>
					<input type="text" id="tambah_harga_komp_harga_satuan" style="display:block;width:100%;" placeholder="Harga Satuan">
				</div>
				<div>
					<label for='tambah_harga_komp_jenis_produk' style='display:inline-block'>Jenis Produk</label>
					<div>
						<input type='radio' id='tambah_harga_komp_jenis_produk_dalam_negeri' name='tambah_harga_komp_jenis_produk' value='1' disabled>
						<label class='mr-4' for='tambah_harga_komp_jenis_produk_dalam_negeri'>Produk Dalam Negeri</label>
						<input type='radio' id='tambah_harga_komp_jenis_produk_luar_negeri' name='tambah_harga_komp_jenis_produk' value='0' disabled>
						<label for='tambah_harga_komp_jenis_produk_luar_negeri'>Produk Luar Negeri</label>
					</div>
				</div>
				<div>
					<label for='tambah_harga_komp_tkdn' style='display: block;'>Tingkat Komponen Dalam Negeri (TKDN)</label>
					<input type='number' min="0" max="100" id='tambah_harga_komp_tkdn' style='width:22%;' placeholder='Presentase TKDN 0-100' disabled>
					<label style='font-size: 1.2rem;margin-left: 0.5rem;'>%</label>
				</div>
				<div>
					<label for="tambah_harga_komp_akun" style="display:inline-block">Rekening Akun</label>
					<textarea type="text" id="tambah_harga_komp_akun" style="display:block;width:100%;" placeholder="Rekening Akun" disabled></textarea>
				</div>
				<div>
					<label for="tambah_harga_komp_keterangan_lampiran" style="display:inline-block">Keterangan</label>
					<input type="text" id="tambah_harga_komp_keterangan_lampiran" style="display:block;width:100%;" placeholder="Link Google Drive Keterangan">
					<small>*Masukkan link Google Drive berisikan lampiran minimal 3 harga toko beserta gambar.</small>
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
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="tambahUsulanAkunByKompSSHLabel">Tambah Rekening Akun usulan SSH</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div>
					<label for="tambah_akun_komp_kategori" style="display:inline-block">Kategori</label>
					<input type="text" id="tambah_akun_komp_kategori" style="display:block;width:100%;" placeholder="Kategori" disabled>
				</div>
				<div><label for="tambah_akun_komp_nama_komponent" style="display:inline-block">Nama Komponen</label>
					<select id="tambah_akun_komp_nama_komponent" class="js-example-basic-single" style="display:block;width:100%;" placeholder="Nama Komponen"></select>
					<input type="text" id="tambah_akun_show_komp_nama" class="hide" style="width:100%;" placeholder="Nama Komponen" disabled>
				</div>
				<div><label for="tambah_akun_komp_spesifikasi" style="display:inline-block">Spesifikasi</label>
					<input type="text" id="tambah_akun_komp_spesifikasi" style="display:block;width:100%;" placeholder="Spesifikasi" disabled>
				</div>
				<div><label for="tambah_akun_komp_satuan" style="display:inline-block">Satuan</label>
					<input type="text" id="tambah_akun_komp_satuan" style="display:block;width:100%;" placeholder="Satuan" disabled>
				</div>
				<div><label for="tambah_akun_komp_harga_satuan" style="display:inline-block">Harga Satuan</label>
					<input type="text" id="tambah_akun_komp_harga_satuan" style="display:block;width:100%;" placeholder="Harga Satuan" disabled>
				</div>
				<div>
					<label for='tambah_akun_komp_jenis_produk' style='display:inline-block'>Jenis Produk</label>
					<div>
						<input type='radio' id='tambah_akun_komp_jenis_produk_dalam_negeri' name='tambah_akun_komp_jenis_produk' value='1' disabled>
						<label class='mr-4' for='tambah_akun_komp_jenis_produk_dalam_negeri'>Produk Dalam Negeri</label>
						<input type='radio' id='tambah_akun_komp_jenis_produk_luar_negeri' name='tambah_akun_komp_jenis_produk' value='0' disabled>
						<label for='tambah_akun_komp_jenis_produk_luar_negeri'>Produk Luar Negeri</label>
					</div>
				</div>
				<div>
					<label for='tambah_akun_komp_tkdn' style='display: block;'>Tingkat Komponen Dalam Negeri (TKDN)</label>
					<input type='number' id='tambah_akun_komp_tkdn' style='width:22%;' placeholder='Presentase TKDN' disabled>
					<label style='font-size: 1.2rem;margin-left: 0.5rem;'>%</label>
				</div>
				<div><label for="tambah_akun_komp_akun" style="display:inline-block">Rekening Akun SIPD</label>
					<textarea type="text" id="tambah_akun_komp_akun" style="display:block;width:100%;" placeholder="Rekening Akun" disabled></textarea>
				</div>
				<div><label for="tambah_new_akun_komp" id_standar_harga="" style="display:inline-block">Rekening Akun Usulan</label>
					<select id="tambah_new_akun_komp" name="states[]" multiple="multiple" style="display:block;width:100%;"></select>
				</div>
				<div id="tambah_akun_lampiran"><label for="tambah_akun_komp_keterangan_lampiran" style="display:inline-block">Keterangan</label>
					<input type="text" id="tambah_akun_komp_keterangan_lampiran" style="display:block;width:100%;" placeholder="Link Google Drive Keterangan" disabled>
					<small>*Masukkan link Google Drive berisikan lampiran minimal 3 harga toko beserta gambar.</small>
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
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Tambah usulan SSH</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="form-usulan-ssh">
				<div class="modal-body">
					<div class="row">
						<label for='u_kategori' class="col-md-12">Kategori</label>
						<div class="col-md-12">
							<select id='u_kategori' class="form-control"></select>
						</div>
					</div>
					<div class="row">
						<label for='u_nama_komponen' class="col-md-12">Nama Komponen</label>
						<div class="col-md-12">
							<input type='text' id='u_nama_komponen' class="form-control" placeholder='Nama Komponen'>
						</div>
					</div>
					<div class="row">
						<label for='u_spesifikasi' class="col-md-12">Spesifikasi</label>
						<div class="col-md-12">
							<input type='text' id='u_spesifikasi' class="form-control" placeholder='Spesifikasi'>
						</div>
					</div>
					<div class="row">
						<label for='u_satuan' class="col-md-12">Satuan</label>
						<div class="col-md-12">
							<select id='u_satuan' class="form-control"></select>
						</div>
					</div>
					<div class="row">
						<label for='u_harga_satuan' class="col-md-12">Harga Satuan</label>
						<div class="col-md-12">
							<input type='number' id='u_harga_satuan' class="form-control" placeholder='Harga Satuan'>
						</div>
					</div>
					<div class="row">
						<label for='u_jenis_produk' class="col-md-12">Jenis Produk</label>
						<div class="col-md-12">
							<input type='radio' id='u_jenis_produk_dalam_negeri' name='u_jenis_produk' value='1'>
							<label class='mr-4' for='u_jenis_produk_dalam_negeri'>Produk Dalam Negeri</label>
							<input type='radio' id='u_jenis_produk_luar_negeri' name='u_jenis_produk' value='0'>
							<label for='u_jenis_produk_luar_negeri'>Produk Luar Negeri</label>
						</div>
					</div>
					<div class="row">
						<label for='u_tkdn' class="col-md-12">Tingkat Komponen Dalam Negeri (TKDN)</label>
						<div class="col-md-12">
							<input type='number' id='u_tkdn' style='width:22%;' placeholder='Presentase TKDN'>
							<label style='font-size: 1.2rem;margin-left: 0.5rem;'>%</label>
						</div>
					</div>
					<div class="row">
						<label for='u_akun' class="col-md-12">Rekening Akun</label>
						<div class="col-md-12">
							<select id='u_akun' name='states[]' multiple='multiple'></select>
						</div>
					</div>
					<div class="row">
						<label for='u_lapiran_usulan_ssh' class="col-md-12">Lampiran Usulan SSH</label>
						<div class="col-md-12">
							<input type='file' id='u_lapiran_usulan_ssh_1' accept="image/png, image/jpeg, image/jpg, application/pdf" style='display:block;width:100%;' onchange="checkFileType(this)"><a id="file_lapiran_usulan_ssh_1"></a></br>
							<input type='file' id='u_lapiran_usulan_ssh_2' accept="image/png, image/jpeg, image/jpg, application/pdf" style='display:block;width:100%;' onchange="checkFileType(this)"><a id="file_lapiran_usulan_ssh_2"></a></br>
							<input type='file' id='u_lapiran_usulan_ssh_3' accept="image/png, image/jpeg, image/jpg, application/pdf" style='display:block;width:100%;' onchange="checkFileType(this)"><a id="file_lapiran_usulan_ssh_3"></a><br>
							<small style="color:red">*Lampiran wajib ber-type png, jpeg, jpg, atau pdf.</small><br>
							<small style="color:red">*Ukuran lampiran maksimal 2MB.</small>
						</div>
					</div>
					<div class="row">
						<label for='u_keterangan_lampiran' class="col-md-12">Catatan</label>
						<div class="col-md-12">
							<textarea id='u_keterangan_lampiran' class="form-control" placeholder='Catatan'></textarea>
						</div>
					</div>
				</div> 
				<div class="modal-footer">
					<button class='btn btn-primary submitBtn' onclick='submitUsulanSshForm(<?php echo $input['tahun_anggaran']; ?>); return false;'>Simpan</button>
	                <button type="button" class="components-button btn btn-default" data-dismiss="modal">Tutup</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function(){
		globalThis.tahun = <?php echo $input['tahun_anggaran']; ?>;
		get_data_ssh_surat(tahun);
		get_data_ssh(tahun)
		.then(function(){
			jQuery('#wrap-loading').show();
            get_data_satuan_ssh(tahun);
            get_data_nama_ssh(tahun);
            get_list_unit({
				tahun_anggaran:tahun
			});
			jQuery("#usulan_ssh_table_wrapper div:first").addClass("h-100 align-items-center");
			let html_filter = "<div class='row'><div class='col-sm-12 col-md-10'><select name='filter_action' class='ml-3 bulk-action' id='multi_select_action'>"+
				"<option value='0'>Tindakan Massal</option>"+
				"<option value='approve'>Setuju</option>"+
				"<option value='notapprove'>Tolak</option>"+
				"<option value='delete'>Hapus</option></select>"+
			"<button type='submit' class='ml-1 btn btn-secondary' onclick='action_check_data_usulan_ssh()'>Terapkan</button>&nbsp;"+
			"<select name='filter_status' class='ml-3 bulk-action' id='search_filter_action' onchange='action_filter_data_usulan_ssh()'>"+
				"<option value=''>Pilih Filter</option>"+
				"<option value='diterima'>Diterima</option>"+
				"<option value='ditolak'>Ditolak</option>"+
				"<option value='diterima_admin'>Diterima Admin</option>"+
				"<option value='ditolak_admin'>Ditolak Admin</option>"+
				"<option value='diterima_tapdkeu'>Diterima TAPD Keuangan</option>"+
				"<option value='ditolak_tapdkeu'>Ditolak TAPD Keuangan</option>"+
				"<option value='menunggu'>Menunggu</option>"+
				"<option value='sudah_upload_sipd'>Sudah upload SIPD</option>"+
				"<option value='belum_upload_sipd'>Belum upload SIPD</option>"+
			"</select>&nbsp;"
			+"<select name='filter_opd' class='ml-3 bulk-action' id='search_filter_action_opd' style='width:50%' onchange='action_filter_data_usulan_ssh()'></select>";
			
			// jQuery("#usulan_ssh_table_length").append(html_filter);
			
			jQuery(".h-100").after(html_filter);
			jQuery("#multi_select_action").select2();
			jQuery("#search_filter_action").select2();
		});
		let get_data = 1;
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
		jQuery("#wrap-loading").show();
		return new Promise(function(resolve, reject){
			globalThis.usulanSSHTable = jQuery('#usulan_ssh_table')
			.on('preXhr.dt', function ( e, settings, data ) {
				if(jQuery("#search_filter_action").val()){
					data.filter = jQuery("#search_filter_action").val();
				}

				if(jQuery("#search_filter_action_opd").val()){
					data.filter_opd = jQuery("#search_filter_action_opd").val();
				}
			} )
			.DataTable({
				"processing": true,
        		"serverSide": true,
		        "ajax": {
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:"post",
					data:{
						'action' : "get_data_usulan_ssh",
						'api_key' : jQuery("#api_key").val(),
						'tahun_anggaran' : tahun
					}
				},
  				order: [0],
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
		            { "data": "nama_standar_harga" },
		            { "data": "spek_satuan",
		            	className: "text-left spek-satuan",
						"targets": "no-sort",
						"orderable": false
					},
		            { 
		            	"data": "harga",
		            	className: "text-right",
		            	render: function(data, type) {
			                var number = jQuery.fn.dataTable.render.number( '.', ',', 2, ''). display(data);
			                return number;
			            }
		            },
					{ 
		            	"data": "show_keterangan",
		            	className: "text-left kol-keterangan",
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
		            	"data": "varify_tapdkeu",
		            	className: "text-left varify_tapdkeu",
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
				"initComplete":function( settings, json){
					jQuery("#wrap-loading").hide();
					resolve();
				}
			});
		});
	}

	function get_data_ssh_surat(tahun){
		return new Promise(function(resolve, reject){
			globalThis.usulanSSHTable = jQuery('#surat_usulan_ssh_table').DataTable({
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
  				order: [0],
				"columns": [
					{
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-center"
		            },
					{
						"targets": 'no-sort',
						"orderable": false,
		            	className: "text-left"
		            }
		        ],
				"initComplete":function( settings, json){
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
			
			jQuery("#u_satuan").html(dataSatuanSsh.table_content);
			jQuery('#u_satuan').select2({width: '100%'});
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
					        id_standar_harga : jQuery("#tambah_new_akun_komp").attr('id_standar_harga'),
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
			jQuery('#tambah_harga_komp_nama_komponent').select2(ajax_nama_komponen);
			jQuery('#tambah_akun_komp_nama_komponent').select2(ajax_nama_komponen);
			jQuery('#u_kategori').select2(ajax_kategori);
			jQuery("#u_akun").select2(ajax_akun);
			jQuery("#tambah_new_akun_komp").select2(ajax_akun);

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

	function tambah_new_ssh(tahun){
		jQuery("#u_tkdn").val(null);
		jQuery("#u_lapiran_usulan_ssh_1").val(null);
		jQuery("#u_lapiran_usulan_ssh_2").val(null);
		jQuery("#u_lapiran_usulan_ssh_3").val(null);
		jQuery("#file_lapiran_usulan_ssh_1").html('');
		jQuery("#file_lapiran_usulan_ssh_2").html('');
		jQuery("#file_lapiran_usulan_ssh_3").html('');
		jQuery("#tambahUsulanSshModal .modal-title").html("Tambah usulan SSH");
		jQuery("#tambahUsulanSshModal .submitBtn")
			.attr("onclick", 'submitUsulanSshForm('+tahun+')')
			.attr("disabled", false)
			.text("Simpan");
		jQuery('#tambahUsulanSshModal').modal('show');
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
			jQuery('#tambahUsulanHargaByKompSSH').modal('show');
			jQuery("#tambahUsulanHargaByKompSSH .modal-title").html('Tambah Harga usulan SSH');
			jQuery("#tambahUsulanHargaByKompSSH .submitBtn")
				.attr("onclick", 'submitUsulanTambahHargaSshForm('+tahun+')')
				.attr("disabled", false)
				.text("Simpan");
		}else if(jenis === 'akun'){
			jQuery("#tambah_akun_komp_nama_komponent").on("change", function(){
				var id_standar_harga = jQuery(this).val();
				if(id_standar_harga != null){
					get_data_usulan_ssh_by_komponen('akun',id_standar_harga)
				}
			});
			jQuery('#tambahUsulanAkunByKompSSH').modal('show');
			jQuery("#tambahUsulanAkunByKompSSH .modal-title").html('Tambah Rekening Akun usulan SSH');
			jQuery("#tambahUsulanAkunByKompSSH .submitBtn")
				.attr("onclick", 'submitUsulanTambahAkunSshForm('+tahun+')')
				.attr("disabled", false)
				.text("Simpan");
		}
	}

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
					jQuery("#tambah_harga_komp_kategori").val(response.data_ssh_usulan_by_id.kode_kel_standar_harga+" "+response.data_ssh_usulan_by_id.nama_kel_standar_harga);
					jQuery("#tambah_harga_komp_spesifikasi").val(response.data_ssh_usulan_by_id.spek);
					jQuery("#tambah_harga_komp_satuan").val(response.data_ssh_usulan_by_id.satuan);
					jQuery("#tambah_harga_komp_akun").html(response.table_content);
					jQuery(`#tambah_harga_komp_jenis_produk_${response.data_ssh_usulan_by_id.jenis_produk}`).prop('checked',true);
					jQuery("#tambah_harga_komp_tkdn").val(response.data_ssh_usulan_by_id.tkdn);

				}else if(jenis === 'akun'){
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
		jQuery("#wrap-loading").show();

		if(kategori == '' || nama_komponen == '' || spesifikasi == '' || satuan == '' || harga_satuan == '' || jenis_produk == '' || tkdn == '' || akun == '' || typeof lapiran_usulan_ssh_1 == 'undefined' || typeof lapiran_usulan_ssh_2 == 'undefined' ){
			jQuery("#wrap-loading").hide();
			alert('Harap diisi semua, tidak ada yang kosong, lampiran 1 dan 2 wajib terisi..');
			return false;
		}else if(kategori.trim() == '' || nama_komponen.trim() == '' || spesifikasi.trim() == '' || satuan.trim() == '' || harga_satuan.trim() == '' || jenis_produk.trim() == '' || tkdn.trim() == '' || akun == '' ){
			jQuery("#wrap-loading").hide();
			alert('Harap diisi semua, tidak ada yang kosong...');
			return false;
		}else{
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

			if(typeof lapiran_usulan_ssh_3 !== 'undefined'){
				tempData.append('lapiran_usulan_ssh_3', lapiran_usulan_ssh_3);
			}

			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:tempData,
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
					}else{
						alert(response.message);
					}
					jQuery('#tambahUsulanSshModal').modal('hide');
					jQuery("#wrap-loading").hide();
					usulanSSHTable.ajax.reload();	
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
					jQuery('.modal-body').css('opacity', '.5');
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
					}else{
						alert(response.message);
					}
					jQuery('#tambahUsulanSsh').modal('hide')
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery('.modal-body').css('opacity', '');
					jQuery("#wrap-loading").hide();
					usulanSSHTable.ajax.reload();
				}
			});
		}
	}

	//edit akun ssh usulan
	function verify_ssh_usulan(id_standar_ssh){
		jQuery('#tambahUsulanSsh').modal('show');
		jQuery("#tambahUsulanSshLabel").html("Verifikasi SSH");
		jQuery("#tambahUsulanSsh .modal-dialog").removeClass("modal-lg modal-xl");
		jQuery("#tambahUsulanSsh .modal-dialog").addClass("modal-sm");
		jQuery("#tambahUsulanSsh .modal-body").html("<div class=\'akun-ssh-verify\'><table>"+
					"<tr><td><input class=\'verify-ssh\' id=\'verify-ssh-yes\' name=\'verify_ssh\' value=\'1\' type=\'radio\' checked><label for=\'verify-ssh-yes\'>Terima</label></td>"+
					"<td><input class=\'verify-ssh\' id=\'verify-ssh-no\' name=\'verify_ssh\' value=\'0\' type=\'radio\'><label for=\'verify-ssh-no\'>Tolak</label></td></tr>"+
					"<tr class=\'add-desc-verify-ssh\' style=\'display:none;\'><td colspan=\'2\'><label for=\'alasan_verify_ssh\' style=\'display:inline-block;\'>Alasan</label><textarea id=\'alasan_verify_ssh\'></textarea></td></tr></div>");
		jQuery("#tambahUsulanSsh .modal-footer").html("<button style=\'margin: 0 0 2rem 0.5rem;border-radius:0.2rem;\' class=\'btn_submit_verify_ssh\' onclick=\'submit_verify_ssh("+id_standar_ssh+")\'>Simpan</button>");
		jQuery("#verify-ssh-no").on("click", function() {
			jQuery(".add-desc-verify-ssh").show();
			jQuery(".catatan-verify-ssh").show();
		})
		jQuery("#verify-ssh-yes").on("click", function() {
			jQuery(".add-desc-verify-ssh").hide();
			jQuery(".catatan-verify-ssh").hide();
		})

		getDataUsulanSshByIdStandarHarga(id_standar_ssh);
	}

	/** submit verifikasi usulan ssh */
	function submit_verify_ssh(id_standar_ssh){
		var verify_ssh = jQuery("input[name=\'verify_ssh\']:checked").val();
		var reason_verify_ssh = jQuery("#alasan_verify_ssh").val();
		jQuery("#wrap-loading").show();
		if(verify_ssh == 0 && reason_verify_ssh.trim() == ''){
				jQuery("#wrap-loading").hide();
				alert('Harap diisi semua, tidak ada yang kosong.');
				return false;
			}else{
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					type:'post',
					data:{
						'action' : 'submit_verify_ssh',
						'api_key' : jQuery("#api_key").val(),
						'verify_ssh' : verify_ssh,
						'reason_verify_ssh' : reason_verify_ssh,
						'id_ssh_verify_ssh' : id_standar_ssh,
					},
					dataType: 'json',
					beforeSend: function () {
						jQuery('.btn_submit_verify_ssh').attr("disabled","disabled");
						jQuery('.modal-body').css('opacity', '.5');
					},
					success:function(response){
						if(response.status == 'success'){
							alert('Data berhasil diverifikasi.');
						}else{
							alert("GAGAL! "+response.message);
						}
						jQuery('#tambahUsulanSsh').modal('hide')
						jQuery('.submitBtn').removeAttr("disabled");
						jQuery('.modal-body').css('opacity', '');
						jQuery("#wrap-loading").hide();
						usulanSSHTable.ajax.reload();
					}
				});
			}
	}

	/** submit tambah usulan harga ssh */
	function submitUsulanTambahHargaSshForm(tahun){
		var id_standar_harga = jQuery('#tambah_harga_komp_nama_komponent').val();
		var harga_satuan = jQuery('#tambah_harga_komp_harga_satuan').val();
		var keterangan_lampiran = jQuery('#tambah_harga_komp_keterangan_lampiran').val();
		jQuery("#wrap-loading").show();
		if(harga_satuan.trim() == '' || keterangan_lampiran.trim() == '' || id_standar_harga.trim() == ''){
			jQuery("#wrap-loading").hide();
			alert('Harap diisi semua, tidak ada yang kosong.');
			return false;
		}else{
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action' : 'submit_tambah_harga_ssh',
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
					'id_standar_harga' : id_standar_harga,
					'harga_satuan' : harga_satuan,
					'keterangan_lampiran' : keterangan_lampiran,
				},
				dataType: 'json',
				beforeSend: function () {
					jQuery('.submitBtn').attr("disabled","disabled");
					jQuery('.modal-body').css('opacity', '.5');
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
					}else{
						alert(response.message);
					}
					jQuery('#tambahUsulanHargaByKompSSH').modal('hide')
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery('.modal-body').css('opacity', '');
					jQuery("#wrap-loading").hide();
					usulanSSHTable.ajax.reload();
				}
			});
		}
	}

	/** Submit tombol usulan akun rekening */
	function submitUsulanTambahAkunSshForm(tahun){
		var id_standar_harga = jQuery('#tambah_akun_komp_nama_komponent').val();
		var new_akun = jQuery('#tambah_new_akun_komp').val();
		jQuery("#wrap-loading").show();
		if(new_akun == '' || id_standar_harga.trim() == ''){
			jQuery("#wrap-loading").hide();
			alert('Harap diisi semua, tidak ada yang kosong.');
			return false;
		}else{
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action' : 'submit_tambah_akun_ssh',
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
					'id_standar_harga' : id_standar_harga,
					'new_akun' : new_akun
				},
				dataType: 'json',
				beforeSend: function () {
					jQuery('.submitBtn').attr("disabled","disabled");
					jQuery('.modal-body').css('opacity', '.5');
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
					}else{
						alert(response.message);
					}
					jQuery('#tambahUsulanAkunByKompSSH').modal('hide')
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery('.modal-body').css('opacity', '');
					jQuery("#wrap-loading").hide();
					usulanSSHTable.ajax.reload();
				}
			});
		}
	}

	/** edit akun ssh usulan */
	function edit_ssh_usulan(status_jenis_usulan,id_standar_harga){
		jQuery('#wrap-loading').show();

		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			type:"post",
			data:{
				'action'			: "get_data_ssh_usulan_by_id",
				'api_key'			: jQuery("#api_key").val(),
				'id_standar_harga'	: id_standar_harga,
				'tahun_anggaran'	: tahun
			},
			dataType: "json",
			success:function(response){
				jQuery('#wrap-loading').hide();
				if(status_jenis_usulan === 'tambah_akun'){
					jQuery('#tambahUsulanAkunByKompSSH').modal('show');
					jQuery("#tambahUsulanAkunByKompSSH .modal-title").html('Edit Tambah Akun Usulan SSH');
					jQuery("#tambah_akun_komp_kategori").val(response.data.kode_kel_standar_harga+" "+response.data.nama_kel_standar_harga);
					jQuery("#tambah_akun_komp_nama_komponent").next(".select2-container").addClass("hide");
					jQuery("#tambah_akun_show_komp_nama").removeClass("hide");
					jQuery("#tambah_akun_show_komp_nama").val(response.data.nama_standar_harga);
					jQuery("#tambah_akun_komp_spesifikasi").val(response.data.spek);
					jQuery("#tambah_akun_komp_satuan").val(response.data.satuan);
					jQuery("#tambah_akun_komp_harga_satuan").val(response.data.harga);
					jQuery(`#tambah_akun_komp_jenis_produk_${response.data.jenis_produk}`).prop('checked',true);
					jQuery("#tambah_akun_komp_tkdn").val(response.data.tkdn);
					jQuery("#tambah_akun_komp_keterangan_lampiran").val(response.data.keterangan_lampiran);
					jQuery("#tambah_akun_komp_akun").html(response.table_content_akun);
					jQuery("#tambah_new_akun_komp").attr('id_standar_harga', id_standar_harga);
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
					jQuery("#tambah_akun_lampiran").hide();
					jQuery("#tambahUsulanAkunByKompSSH .submitBtn")
						.attr('onclick', 'submitEditTambahAkunUsulanSshForm('+id_standar_harga+', '+tahun+')')
						.attr("disabled", false)
						.text("Simpan");
				}else if(status_jenis_usulan === 'tambah_harga'){
					jQuery('#tambahUsulanHargaByKompSSH').modal('show');
					jQuery("#tambahUsulanHargaByKompSSH .modal-title").html('Edit Tambah Harga Usulan SSH');
					jQuery("#tambah_harga_komp_kategori").val(response.data.kode_kel_standar_harga+" "+response.data.nama_kel_standar_harga);
					jQuery("#tambah_harga_komp_nama_komponent").next(".select2-container").addClass("hide");
					jQuery("#tambah_harga_show_komp_nama").removeClass("hide");
					jQuery("#tambah_harga_show_komp_nama").val(response.data.nama_standar_harga);
					jQuery("#tambah_harga_komp_spesifikasi").val(response.data.spek);
					jQuery("#tambah_harga_komp_satuan").val(response.data.satuan);
					jQuery("#tambah_harga_komp_harga_satuan").val(response.data.harga);
					jQuery(`#tambah_harga_komp_jenis_produk_${response.data.jenis_produk}`).prop('checked',true);
					jQuery("#tambah_harga_komp_tkdn").val(response.data.tkdn);
					jQuery("#tambah_harga_komp_keterangan_lampiran").val(response.data.keterangan_lampiran);
					jQuery("#tambah_harga_komp_akun").html(response.table_content_akun);
					jQuery("#tambahUsulanHargaByKompSSH .submitBtn")
						.attr('onclick', 'submitEditTambahHargaUsulanSshForm('+id_standar_harga+', '+tahun+')')
						.attr("disabled", false)
						.text("Simpan");
				}else if(status_jenis_usulan == 'tambah_baru'){
					jQuery('#tambahUsulanSshModal').modal('show');
					jQuery("#tambahUsulanSshModal .modal-title").html('Edit Tambah Usulan SSH');
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
					jQuery("#file_lapiran_usulan_ssh_1").attr('href', '#');
					jQuery("#file_lapiran_usulan_ssh_2").html(response.data.lampiran_2);
					jQuery("#file_lapiran_usulan_ssh_2").attr('href', '#');
					jQuery("#file_lapiran_usulan_ssh_3").html(response.data.lampiran_3);
					jQuery("#file_lapiran_usulan_ssh_3").attr('href', '#');
					jQuery("#tambahUsulanSshModal .submitBtn")
						.attr('onclick', 'submitEditUsulanSshForm('+id_standar_harga+', '+tahun+')')
						.attr('disabled', false)
						.text('Simpan');
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

	function submitEditUsulanSshForm(id_standar_harga,tahun){
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
			tempData.append('id_standar_harga', id_standar_harga);
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
					}else{
						alert(`GAGAL! ${response.message}`);
					}
					jQuery("#wrap-loading").hide();
					jQuery('#tambahUsulanSshModal').modal('hide')
					usulanSSHTable.ajax.reload();	
				}
			});
		}
	}

	/** submit edit tambah usulan harga ssh */
	function submitEditTambahHargaUsulanSshForm(id_standar_harga,tahun){
		var harga_satuan = jQuery('#tambah_harga_komp_harga_satuan').val();
		var keterangan_lampiran = jQuery('#tambah_harga_komp_keterangan_lampiran').val();
		jQuery("#wrap-loading").show();
		if(harga_satuan.trim() == '' || keterangan_lampiran.trim() == '' || id_standar_harga == ''){
			jQuery("#wrap-loading").hide();
			alert('Harap diisi semua, tidak ada yang kosong.');
			return false;
		}else{
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action' : 'submit_edit_tambah_harga_ssh',
					'api_key' : jQuery("#api_key").val(),
					'tahun_anggaran' : tahun,
					'id_standar_harga' : id_standar_harga,
					'harga_satuan' : harga_satuan,
					'keterangan_lampiran' : keterangan_lampiran,
				},
				dataType: 'json',
				beforeSend: function () {
					jQuery('.submitBtn').attr("disabled","disabled");
					jQuery('.modal-body').css('opacity', '.5');
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
					}else{
						alert(response.message);
					}
					jQuery('#tambahUsulanHargaByKompSSH').modal('hide')
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery('.modal-body').css('opacity', '');
					jQuery("#wrap-loading").hide();
					usulanSSHTable.ajax.reload();
				}
			});
		}
	}

	/** submit edit tambah akun ssh */
	function submitEditTambahAkunUsulanSshForm(id_standar_harga,tahun){
		var new_akun = jQuery('#tambah_new_akun_komp').val();
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
					'id_standar_harga' : id_standar_harga,
					'new_akun' : new_akun
				},
				dataType: 'json',
				beforeSend: function () {
					jQuery('.submitBtn').attr("disabled","disabled");
					jQuery('.modal-body').css('opacity', '.5');
				},
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil disimpan.');
					}else{
						alert(response.message);
					}
					jQuery('#tambahUsulanAkunByKompSSH').modal('hide')
					jQuery('.submitBtn').removeAttr("disabled");
					jQuery('.modal-body').css('opacity', '');
					jQuery("#wrap-loading").hide();
					usulanSSHTable.ajax.reload();
				}
			});
		}
	}

	function delete_ssh_usulan(id_standar_harga){
		let confirmDelete = confirm("Apakah anda yakin akan menghapus usulan SSH?");
		if(confirmDelete){
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type:'post',
				data:{
					'action' 				: 'submit_delete_usulan_ssh',
					'api_key'				: jQuery("#api_key").val(),
					'id_standar_harga'		: id_standar_harga,
					'tahun_anggaran'		: tahun
				},
				dataType: 'json',
				success:function(response){
					if(response.status == 'success'){
						alert('Data berhasil dihapus!.');
					}else{
						alert(`GAGAL! ${response.message}`);
					}
					usulanSSHTable.ajax.reload();	
				}
			});
		}
	}

	function delete_akun_ssh_usulan(id_standar_harga,id){
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
						'action' 				: 'submit_delete_akun_usulan_ssh',
						'api_key'				: jQuery("#api_key").val(),
						'id_standar_harga'		: id_standar_harga,
						'tahun_anggaran'		: tahun,
						'id_rek_akun_usulan_ssh': id
					},
					dataType: 'json',
					success:function(response){
						jQuery("#wrap-loading").hide();
						if(response.status == 'success'){
							alert('Data berhasil dihapus!.');
							jQuery(`#rek_akun_${id}`).remove()
						}else{
							alert(`GAGAL! ${response.message}`);
						}
						usulanSSHTable.ajax.reload();	
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

		jQuery("input:checkbox[class=delete_check]:checked").each(function () {
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
							}else{
								alert(`GAGAL! ${response.message}`);
							}
							usulanSSHTable.ajax.reload();
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
							}else{
								alert(`GAGAL! ${response.message}`);
							}
							usulanSSHTable.ajax.reload();
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
								}else{
									alert(`GAGAL! ${response.message}`);
								}
								usulanSSHTable.ajax.reload();
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

	function getDataUsulanSshByIdStandarHarga(id_standar_harga){
		return new Promise(function(resolve, reject){
			jQuery.ajax({
				url: ajax.url,
			    type: "post",
			    data: {
			          "action": "get_data_usulan_ssh_by_id_standar_harga",
			          "api_key": jQuery("#api_key").val(),
			          "id_standar_harga": id_standar_harga,
			    },
			    dataType: "json",
			    success: function(res){
			    	if(res.status){

			    		let user='';
			    		let catatan='';
			    		if(res.role==='administrator'){
			    			user = 'Tapd Keuangan';
			    			catatan = res.data.keterangan_status_tapdkeu!=null ? res.data.keterangan_status_tapdkeu : '';
			    		}else if(res.role==='tapd_keu'){
			    			user = 'Administrator';
			    			catatan = res.data.keterangan_status_admin!=null ? res.data.keterangan_status_admin : '';
			    		}
			    		let html="<tr class=\'catatan-verify-ssh\' style='display:none'><td colspan=\'2\'><label for=\'catatan_verify_ssh\' style=\'display:inline-block;\'>Alasan "+user+"</label><br><span class=\'medium-bold-2\' id=\'catatan_verify_ssh\'>"+catatan+"</span></td></tr>"
			    		jQuery(".add-desc-verify-ssh").after(html);
			    	}
			    	resolve();
			    }
			});
		})
	}

	function buat_surat_usulan(tahun) {
        var ids = [];
        jQuery('.delete_check').each(function(){
            if(
            	jQuery(this).is(':checked')
            	&& jQuery(this).attr('no-surat') == ''
            ){
                ids.push(jQuery(this).val());
            }
        });

        if(ids.length == 0){
        	alert('Harap pilih dulu usulan standar harga! Pastikan pilih data usulan yang belum pernah dibuat surat usulan.');
        }else{
        	jQuery('#tambahSuratUsulan').modal('show');
        }
	}

	function get_list_unit(params){
		return new Promise(function(resolve, reject){
			jQuery.ajax({
				url: ajax.url,
			    type: "post",
			    data: {
			       		"action": "get_unit",
			       		"api_key": jQuery("#api_key").val(),
			       		"tahun_anggaran": params.tahun_anggaran
			       	},
			       	dataType: "json",
			       	success: function(res){
			          	let opt = ''
			          		+'<option value="">Pilih Filter Unit</option>'
			          		res.data.map(function(value, index) {
			          			opt+='<option value="'+value.id_skpd+'">'+value.nama_skpd+'</option>'
			          		});
			          	jQuery("#search_filter_action_opd").html(opt);
			          	jQuery("#search_filter_action_opd").select2();
			          	resolve();
			        }
			});
		})
	}
</script> 