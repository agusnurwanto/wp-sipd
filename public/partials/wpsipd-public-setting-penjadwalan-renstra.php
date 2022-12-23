<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $wpdb;

$select_rpd_rpjm = '';

$sqlTipe = $wpdb->get_results($wpdb->prepare("
	SELECT 
		* 
	FROM 
		`data_tipe_perencanaan` 
	WHERE 
		nama_tipe=%s
	OR 
		nama_tipe=%s",
		'rpd',
		'rpjm'
	), ARRAY_A);
$data_rpd_rpjm = $wpdb->get_results($wpdb->prepare('
	SELECT
		id_jadwal_lokal,
		nama,
		id_tipe
	FROM
		data_jadwal_lokal
	WHERE
		status=1
		and id_tipe=%d
	OR 
		status=1
		and id_tipe=%d',
		$sqlTipe[0]['id'],
		$sqlTipe[1]['id']
	),ARRAY_A);
				
if(!empty($data_rpd_rpjm)){
	foreach($data_rpd_rpjm as $val_rpd_rpjm){
		$tipe = [];
		foreach ($sqlTipe as $val_tipe) {
			$tipe[$val_tipe['id']] = strtoupper($val_tipe['nama_tipe']);
		}
		$select_rpd_rpjm .= '<option value="'.$val_rpd_rpjm['id_jadwal_lokal'].'">'.$tipe[$val_rpd_rpjm['id_tipe']].' | '.$val_rpd_rpjm['nama'].'</option>';
	}
}

$body = '';
?>
<style>
.bulk-action {
	padding: .45rem;
	border-color: #eaeaea;
	vertical-align: middle;
}
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<div class="cetak">
	<div style="padding: 10px;margin:0 0 3rem 0;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
	<h1 class="text-center" style="margin:3rem;">Jadwal Input Perencanaan RENSTRA Lokal</h1>
		<div style="margin-bottom: 25px;">
			<button class="btn btn-primary tambah_ssh" onclick="tambah_jadwal();">Tambah Jadwal</button>
		</div>
		<table id="data_penjadwalan_table" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center">Nama Tahapan</th>
					<th class="text-center">Status</th>
					<th class="text-center">Jadwal Mulai</th>
					<th class="text-center">Jadwal Selesai</th>
					<th class="text-center">Tahun Mulai Anggaran</th>
					<th class="text-center">Tahun Selesai Anggaran</th>
					<th class="text-center">Jadwal RPD/RPJM</th>
					<th class="text-center" style="width: 150px;">Aksi</th>
				</tr>
			</thead>
			<tbody id="data_body">
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade mt-4" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalTambahJadwalLabel">Tambah Penjadwalan</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div>
					<label for='jadwal_nama' style='display:inline-block'>Nama Tahapan</label>
					<input type='text' id='jadwal_nama' style='display:block;width:100%;' placeholder='Nama Tahapan'>
				</div>
				<div>
					<label for='tahun_mulai_anggaran' style='display:inline-block'>Tahun Mulai Anggaran</label>
					<input type="number" id='tahun_mulai_anggaran' name="tahun_mulai_anggaran" style='display:block;width:100%;' placeholder="Tahun Mulai Anggaran"/>
				</div>
				<div>
					<label for='lama_pelaksanaan' style='display:inline-block'>Lama Pelaksanaan</label>
					<input type="number" id='lama_pelaksanaan' name="lama_pelaksanaan" style='display:block;width:100%;' placeholder="Lama Pelaksanaan"/>
				</div>
				<div>
					<label for='jadwal_tanggal' style='display:inline-block'>Jadwal Pelaksanaan</label>
					<input type="text" id='jadwal_tanggal' name="datetimes" style='display:block;width:100%;'/>
				</div>
				<div>
					<label for="link_rpd_rpjm" style='display:inline-block'>Pilih Jadwal RPD atau RPJM</label>
					<select id="link_rpd_rpjm" style='display:block;width: 100%;'>
						<option value="">Pilih RPD atau RPJM</option>
						<?php echo $select_rpd_rpjm; ?>
					</select>
				</div>
			</div> 
			<div class="modal-footer">
				<button class="btn btn-primary submitBtn" onclick="submitTambahJadwalForm()">Simpan</button>
				<button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<div class="report"></div>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	jQuery(document).ready(function(){

		globalThis.tipePerencanaan = 'renstra'
		globalThis.thisAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>"
		globalThis.tahunAnggaran = "<?php echo get_option('_crb_tahun_anggaran_sipd'); ?>"

		get_data_penjadwalan();

	});

	/** get data penjadwalan */
	function get_data_penjadwalan(){
		jQuery("#wrap-loading").show();
		globalThis.penjadwalanTable = jQuery('#data_penjadwalan_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: thisAjaxUrl,
				type:"post",
				data:{
					'action' 		: "get_data_penjadwalan",
					'api_key' 		: jQuery("#api_key").val(),
					'tipe_perencanaan' : tipePerencanaan
				}
			},
			"initComplete":function( settings, json){
				jQuery("#wrap-loading").hide();
			},
			"columns": [
				{ 
					"data": "nama",
					className: "text-center"
				},
				{ 
					"data": "status",
					className: "text-center"
				},
				{ 
					"data": "waktu_awal",
					className: "text-center"
				},
				{ 
					"data": "waktu_akhir",
					className: "text-center"
				},
				{ 
					"data": "tahun_anggaran",
					className: "text-center"
				},
				{ 
					"data": "tahun_anggaran_selesai",
					className: "text-center"
				},
				{ 
					"data": "relasi_perencanaan_renstra",
					className: "text-center"
				},
				{ 
					"data": "aksi",
					className: "text-center"
				}
			]
		});
	}

	/** show modal tambah jadwal */
	function tambah_jadwal(){
		jQuery("#modalTambahJadwal .modal-title").html("Tambah Penjadwalan");
		jQuery("#modalTambahJadwal .submitBtn")
			.attr("onclick", 'submitTambahJadwalForm()')
			.attr("disabled", false)
			.text("Simpan");
		jQuery('#modalTambahJadwal').modal('show');
		jQuery.ajax({
			url: thisAjaxUrl,
			type:"post",
			data:{
				'action' 			: "get_data_standar_lama_pelaksanaan",
				'api_key' 			: jQuery("#api_key").val(),
				'tipe_perencanaan' 	: tipePerencanaan
			},
			dataType: "json",
			success:function(response){
				jQuery("#lama_pelaksanaan").val(response.data.lama_pelaksanaan);
			}
		})
	}

	/** Submit tambah jadwal */
	function submitTambahJadwalForm(){
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
		let relasi_perencanaan = jQuery("#link_rpd_rpjm").val()
		let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
		if(nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == '' || this_lama_pelaksanaan == ''){
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		}else{
			jQuery.ajax({
				url: thisAjaxUrl,
				type: 'post',
				dataType: 'json',
				data:{
					'action'			: 'submit_add_schedule',
					'api_key'			: jQuery("#api_key").val(),
					'nama'				: nama,
					'jadwal_mulai'		: jadwalMulai,
					'jadwal_selesai'	: jadwalSelesai,
					'tahun_anggaran'	: this_tahun_anggaran,
					'tipe_perencanaan'	: tipePerencanaan,
					'relasi_perencanaan': relasi_perencanaan,
					'lama_pelaksanaan'	: this_lama_pelaksanaan
				},
				beforeSend: function() {
					jQuery('.submitBtn').attr('disabled','disabled')
				},
				success: function(response){
					jQuery('#modalTambahJadwal').modal('hide')
					jQuery('#wrap-loading').hide()
					if(response.status == 'success'){
						alert('Data berhasil ditambahkan')
						penjadwalanTable.ajax.reload()
						afterSubmitForm()
					}else{
						alert(response.message)
					}
					jQuery('#jadwal_nama').val('')
					jQuery("#tahun_mulai_anggaran").val('')
					jQuery("#link_rpd_rpjm").val('')
				}
			})
		}
		jQuery('#modalTambahJadwal').modal('hide');
	}

	/** edit akun ssh usulan */
	function edit_data_penjadwalan(id_jadwal_lokal){
		jQuery('#modalTambahJadwal').modal('show');
		jQuery("#modalTambahJadwal .modal-title").html("Edit Penjadwalan");
		jQuery("#modalTambahJadwal .submitBtn")
			.attr("onclick", 'submitEditJadwalForm('+id_jadwal_lokal+')')
			.attr("disabled", false)
			.text("Simpan");
		jQuery('#wrap-loading').show();
		jQuery.ajax({
			url: thisAjaxUrl,
			type:"post",
			data:{
				'action' 			: "get_data_jadwal_by_id",
				'api_key' 			: jQuery("#api_key").val(),
				'id_jadwal_lokal' 	: id_jadwal_lokal
			},
			dataType: "json",
			success:function(response){
				jQuery('#wrap-loading').hide();
				jQuery("#jadwal_nama").val(response.data.nama);
				jQuery("#tahun_mulai_anggaran").val(response.data.tahun_anggaran);
				jQuery("#lama_pelaksanaan").val(response.data.lama_pelaksanaan);
				jQuery('#jadwal_tanggal').data('daterangepicker').setStartDate(moment(response.data.waktu_awal).format('DD-MM-YYYY HH:mm'));
				jQuery('#jadwal_tanggal').data('daterangepicker').setEndDate(moment(response.data.waktu_akhir).format('DD-MM-YYYY HH:mm'));
				jQuery("#link_rpd_rpjm").val(response.data.relasi_perencanaan).change();
			}
		})
	}

	function submitEditJadwalForm(id_jadwal_lokal){
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = jQuery("#tahun_mulai_anggaran").val()
		let relasi_perencanaan = jQuery("#link_rpd_rpjm").val()
		let this_lama_pelaksanaan = jQuery("#lama_pelaksanaan").val()
		if(nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == '' || this_lama_pelaksanaan == ''){
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		}else{
			jQuery.ajax({
				url: thisAjaxUrl,
				type: 'post',
				dataType: 'json',
				data:{
					'action'			: 'submit_edit_schedule',
					'api_key'			: jQuery("#api_key").val(),
					'nama'				: nama,
					'jadwal_mulai'		: jadwalMulai,
					'jadwal_selesai'	: jadwalSelesai,
					'id_jadwal_lokal'	: id_jadwal_lokal,
					'tahun_anggaran'	: this_tahun_anggaran,
					'tipe_perencanaan'	: tipePerencanaan,
					'relasi_perencanaan': relasi_perencanaan,
					'lama_pelaksanaan'	: this_lama_pelaksanaan
				},
				beforeSend: function() {
					jQuery('.submitBtn').attr('disabled','disabled')
				},
				success: function(response){
					jQuery('#modalTambahJadwal').modal('hide')
					jQuery('#wrap-loading').hide()
					if(response.status == 'success'){
						alert('Data berhasil diperbarui')
						penjadwalanTable.ajax.reload()
						afterSubmitForm()
					}else{
						alert(`GAGAL! \n${response.message}`)
					}
					jQuery('#jadwal_nama').val('')
					jQuery("#tahun_mulai_anggaran").val('')
					jQuery("#link_rpd_rpjm").val('')
				}
			})
		}
		jQuery('#modalTambahJadwal').modal('hide');
	}

	function hapus_data_penjadwalan(id_jadwal_lokal){
		let confirmDelete = confirm("Apakah anda yakin akan menghapus penjadwalan?");
		if(confirmDelete){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: thisAjaxUrl,
				type:'post',
				data:{
					'action' 				: 'submit_delete_schedule',
					'api_key'				: jQuery("#api_key").val(),
					'id_jadwal_lokal'		: id_jadwal_lokal
				},
				dataType: 'json',
				success:function(response){
					jQuery('#wrap-loading').hide();
					if(response.status == 'success'){
						alert('Data berhasil dihapus!.');
						penjadwalanTable.ajax.reload();	
					}else{
						alert(`GAGAL! \n${response.message}`);
					}
				}
			});
		}
	}

	function lock_data_penjadwalan(id_jadwal_lokal){
		let confirmLocked = confirm("Apakah anda yakin akan mengunci penjadwalan?");
		if(confirmLocked){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: thisAjaxUrl,
				type:'post',
				data:{
					'action' 				: 'submit_lock_schedule_renstra',
					'api_key'				: jQuery("#api_key").val(),
					'id_jadwal_lokal'		: id_jadwal_lokal
				},
				dataType: 'json',
				success:function(response){
					jQuery('#wrap-loading').hide();
					if(response.status == 'success'){
						alert('Data berhasil dikunci!.');
						penjadwalanTable.ajax.reload();
					}else{
						alert(`GAGAL! \n${response.message}`);
					}
				}
			});
		}
	}

	jQuery(function() {
		jQuery('#jadwal_tanggal').daterangepicker({
			timePicker: true,
			timePicker24Hour: true,
			startDate: moment().startOf('hour'),
			endDate: moment().startOf('hour').add(32, 'hour'),
			locale: {
				format: 'DD-MM-YYYY HH:mm'
			}
		});
	});

	function cannot_change_schedule(jenis){
		if(jenis == 'kunci'){
			alert('Tidak bisa kunci karena penjadwalan sudah dikunci');
		}else if(jenis == 'edit'){
			alert('Tidak bisa edit karena penjadwalan sudah dikunci');
		}else if(jenis == 'hapus'){
			alert('Tidak bisa hapus karena penjadwalan sudah dikunci');
		}
	}

	function afterSubmitForm(){
		jQuery("#jadwal_nama").val("")
		jQuery("#tahun_mulai_anggaran").val("")
		jQuery("#jadwal_tanggal").val("")
	}

	function copy_usulan(){
		if(confirm('Apakah anda yakin untuk melakukan ini? data penetapan akan diupdate sama dengan data usulan.')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "copy_usulan_renstra",
	          		"api_key": jQuery("#api_key").val()
	          	},
	          	dataType: "json",
	          	success: function(res){
	          		alert(res.message);
	          		jQuery('#wrap-loading').hide();
	          	}
	        });
		}
	}

	function report(awal_renstra, akhir_renstra, lama_pelaksanaan, relasi_perencanaan){

		all_skpd();

		let modal = `
			<div class="modal fade" id="modal-report" tab-index="-1" role="dialog" aria-labelledby="modal-indikator-renstra-label" aria-hidden="true">
			  <div class="modal-dialog modal-lg" role="document" style="min-width:1450px">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title">Export Data</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      
			      <div class="modal-body">
				    <div class="container-fluid">
					    <div class="row">
						    <div class="col-md-2">Unit Kerja</div>
						    <div class="col-md-6">
						    	<select class="form-control list_opd" id="list_opd"></select>
						    </div>
					    </div></br>
					     <div class="row">
					    	<div class="col-md-2">Jenis Laporan</div>
					    	<div class="col-md-6">
					      		<select class="form-control jenis" id="jenis">
					      			<option value="-">Pilih Jenis</option>
					      			<option value="rekap">Format Renstra</option>
					      			<option value="tc27">Format TC 27</option>
					      			<option value="pagu_akumulasi">Format Pagu Akumulasi</option>
				      			</select>
					    	</div>
					    </div></br>
					    <div class="row">
					    	<div class="col-md-2"></div>
					    	<div class="col-md-6">
					      		<button type="button" class="btn btn-success btn-preview" onclick="preview('${awal_renstra}', '${akhir_renstra}', '${lama_pelaksanaan}', '${relasi_perencanaan}')">Preview</button>
					      		<button type="button" class="btn btn-primary export-excel" onclick="exportExcel()" disabled>Export Excel</button>
					    	</div>
					    </div></br>
					</div>
			      </div>

			      <div class="modal-preview" style="padding:10px"></div>

			    </div>
			  </div>
			</div>`;

		jQuery("body .report").html(modal);
		jQuery("#modal-report").modal('show');
	}

	function all_skpd(){

		jQuery.ajax({
			url:ajax.url,
			type:'post',
			dataType:'json',
			data:{
				action:'get_list_skpd',
				tahun_anggaran:tahunAnggaran
			},
			success:function(response){
				let list_opd=`<option value="">Pilih Unit Kerja</option><option value="all">Semua Unit Kerja</option>`;
				response.map(function(v,i){
					list_opd+=`<option value="${v.id_skpd}">${v.nama_skpd}</option>`;
				});
				jQuery("#list_opd").html(list_opd);
				jQuery('.list_opd').select2();
				jQuery('.jenis').select2();		
			}
		})
	}

	function preview(awal_renstra, akhir_renstra, lama_pelaksanaan, relasi_perencanaan){

		let jenis=jQuery("#jenis").val();
		let id_unit=jQuery("#list_opd").val();

		if(id_unit=='' || id_unit=='undefined'){
			alert('Unit kerja belum dipilih');
			return;
		}

		if(id_unit=='all' && jenis!='pagu_akumulasi' && jenis!='-'){
			alert('Pilih minimal satu Unit Kerja!');
			return;
		}

		switch(jenis){
			case 'rekap':
				rekap(id_unit, awal_renstra, akhir_renstra, lama_pelaksanaan, relasi_perencanaan);
				break;

			case 'tc27':
				tc27(id_unit, awal_renstra, akhir_renstra, lama_pelaksanaan);
				break;

			case 'pagu_akumulasi':
				pagu_akumulasi(id_unit, awal_renstra, akhir_renstra, lama_pelaksanaan);
				break;

			case '-':
				alert('Jenis laporan belum dipilih');
				break;
		}
	}

	function rekap(id_unit, awal_renstra, akhir_renstra, lama_pelaksanaan, relasi_perencanaan){
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url:ajax.url,
			type:'post',
			dataType:'json',
			data:{
				action:'view_rekap_renstra',
				id_unit:id_unit,
				awal_renstra:awal_renstra,
				akhir_renstra:akhir_renstra,
				lama_pelaksanaan:lama_pelaksanaan,
				tahun_anggaran:tahunAnggaran,
				relasi_perencanaan:relasi_perencanaan,
				api_key:jQuery("#api_key").val(),
			},
			success:function(response){
				jQuery('#wrap-loading').hide();
				jQuery("#modal-report .modal-preview").html(response.html);
				jQuery("#modal-report .modal-preview").css('overflow-x', 'auto');
				jQuery("#modal-report .modal-preview").css('padding', '15px');
				jQuery('#modal-report .export-excel').attr("disabled", false);
				jQuery('#modal-report .export-excel').attr("title", 'Laporan Renstra');

				jQuery("#table-renstra th.row_head_1").attr('rowspan',2);
				jQuery("#table-renstra th.row_head_1_tahun").attr('colspan',2);
			}
		})
	}

	function tc27(id_unit, awal_renstra, akhir_renstra, lama_pelaksanaan){
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url:ajax.url,
			type:'post',
			dataType:'json',
			data:{
				action:'view_laporan_tc27',
				id_unit:id_unit,
				awal_renstra:awal_renstra,
				akhir_renstra:akhir_renstra,
				lama_pelaksanaan:lama_pelaksanaan,
				tahun_anggaran:tahunAnggaran,
				api_key:jQuery("#api_key").val(),
			},
			success:function(response){
				jQuery('#wrap-loading').hide();
				jQuery("#modal-report .modal-preview").html(response.html);
				jQuery("#modal-report .modal-preview").css('overflow-x', 'auto');
				jQuery("#modal-report .modal-preview").css('padding', '15px');
				jQuery('#modal-report .export-excel').attr("disabled", false);
				jQuery('#modal-report .export-excel').attr("title", 'Laporan TC 27');

				jQuery("#table-renstra th.row_head_1").attr('rowspan',3);
				jQuery("#table-renstra th.row_head_kinerja").attr('colspan',2*lama_pelaksanaan);
				jQuery("#table-renstra th.row_head_1_tahun").attr('colspan',2);
			}
		})
	}

	function pagu_akumulasi(id_unit, awal_renstra, akhir_renstra, lama_pelaksanaan){
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url:ajax.url,
			type:'post',
			dataType:'json',
			data:{
				action:'view_pagu_akumulasi_renstra',
				id_unit:id_unit,
				awal_renstra:awal_renstra,
				akhir_renstra:akhir_renstra,
				lama_pelaksanaan:lama_pelaksanaan,
				tahun_anggaran:tahunAnggaran,
				api_key:jQuery("#api_key").val(),
			},
			success:function(response){
				jQuery('#wrap-loading').hide();
				jQuery("#modal-report .modal-preview").html(response.html);
				jQuery("#modal-report .modal-preview").css('overflow-x', 'auto');
				jQuery("#modal-report .modal-preview").css('padding', '15px');
				jQuery('#modal-report .export-excel').attr("disabled", false);
				jQuery('#modal-report .export-excel').attr("title", 'Laporan TC 27');

				jQuery("#table-renstra th.row_head_1").attr('rowspan',3);
				jQuery("#table-renstra th.row_head_kinerja").attr('colspan',2*lama_pelaksanaan);
				jQuery("#table-renstra th.row_head_1_tahun").attr('colspan',2);
			}
		})
	}

	function exportExcel(){
		let name=jQuery('#modal-report .export-excel').attr("title");
		tableHtmlToExcel("preview", name);
	}

</script> 
