<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$input = shortcode_atts( array(
	'tahun_anggaran' => '2022'
), $atts );

if(isset($tipe_perencanaan)){
	if($tipe_perencanaan == 'renja'){
		$judul = 'Renja';
	}else{
		$tipe_perencanaan = 'penganggaran';
		$judul = 'Penganggaran';
	}
}else{
	$tipe_perencanaan = 'penganggaran';
	$judul = 'Penganggaran';
}

global $wpdb;
$tahun = $wpdb->get_results('select tahun_anggaran from data_unit group by tahun_anggaran order by tahun_anggaran ASC', ARRAY_A);
$select_tahun = "<option value=''>Pilih berdasarkan tahun anggaran</option>";
foreach($tahun as $tahun_value){
	$select = $tahun_value['tahun_anggaran'] == $input['tahun_anggaran'] ? 'selected' : '';
	$nama_page_menu_ssh = 'Setting penjadwalan | '.$tahun_value['tahun_anggaran'];
	$custom_post = get_page_by_title($nama_page_menu_ssh, OBJECT, 'page');
	$url_data_ssh = $this->get_link_post($custom_post);
	$select_tahun .= "<option value='".$url_data_ssh."' ".$select.">Setting penjadwalan | ".$tahun_value['tahun_anggaran']."</option>";
}

$select_renstra = '';

$sqlTipe = $wpdb->get_results($wpdb->prepare("
				SELECT 
					* 
				FROM 
					`data_tipe_perencanaan` 
				WHERE 
					nama_tipe=%s",
					'renstra'
				), ARRAY_A);
$data_renstra = $wpdb->get_results($wpdb->prepare('
				SELECT
					id_jadwal_lokal,
					nama
				FROM
					data_jadwal_lokal
				WHERE
					status=1
					and id_tipe=%d',
					$sqlTipe[0]['id']
				),ARRAY_A);
				
if(!empty($data_renstra)){
	foreach($data_renstra as $val_renstra){
		$select_renstra .= '<option value="'.$val_renstra['id_jadwal_lokal'].'">'.$val_renstra['nama'].'</option>';
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
<div class="cetak">
	<div style="padding: 10px;margin:0 0 3rem 0;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<h1 class="text-center" style="margin:3rem;">Halaman Setting Penjadwalan <?php echo $judul; ?>  <?php echo $input['tahun_anggaran']; ?></h1>
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
					<th class="text-center">Tahun Anggaran</th>
					<th class="text-center">Jadwal RENSTRA</th>
					<th class="text-center">Jenis Jadwal</th>
					<th class="text-center" style="width: 250px;">Aksi</th>
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
					<label for='jadwal_tanggal' style='display:inline-block'>Jadwal Pelaksanaan</label>
					<input type="text" id='jadwal_tanggal' name="datetimes" style='display:block;width:100%;'/>
				</div>
				<div>
					<label for="jenis_jadwal" style='display:inline-block'>Pilih Jenis Jadwal</label>
					<select id="jenis_jadwal" style='display:block;width: 100%;'>
						<option value="usulan" selected>Usulan</option>
						<option value="penetapan">Penetapan</option>
					</select>
				</div>
				<div>
					<label for="link_renstra" style='display:inline-block'>Pilih Jadwal RENSTRA</label>
					<select id="link_renstra" style='display:block;width: 100%;'>
						<option value="">Pilih RENSTRA</option>
						<?php echo $select_renstra; ?>
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
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
<script>
	jQuery(document).ready(function(){
		globalThis.tahun_anggaran = <?php echo $input['tahun_anggaran']; ?>;
		globalThis.tipe_perencanaan = '<?php echo $tipe_perencanaan; ?>';
		globalThis.thisAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>"

		get_data_penjadwalan();

		// let html_filter = "<select class='ml-3 bulk-action' id='selectYears'><?php echo $select_tahun ?></select>"
		// jQuery("#data_penjadwalan_table_length").append(html_filter);

		jQuery('#selectYears').on('change', function(e){
			let selectedVal = jQuery(this).find('option:selected').val();
			if(selectedVal != ''){
				window.location = selectedVal;
			}
		});

		jQuery('#modalTambahJadwal').on('hidden.bs.modal', function () {
			jQuery("#jadwal_nama").val("");
			jQuery("#link_renstra").val("");
		})
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
					'tipe_perencanaan' : tipe_perencanaan,
					'tahun_anggaran': tahun_anggaran
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
					"data": "relasi_perencanaan",
					className: "text-center"
				},
				{ 
					"data": "jenis_jadwal",
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
	}

	/** Submit tambah jadwal */
	function submitTambahJadwalForm(){
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = tahun_anggaran
		let jenis_jadwal = jQuery("#jenis_jadwal").val()
		let relasi_perencanaan = jQuery("#link_renstra").val()
		if(nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == '' || jenis_jadwal == ''){
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		}else{
			jQuery.ajax({
				url:thisAjaxUrl,
				type: 'post',
				dataType: 'json',
				data:{
					'action'			: 'submit_add_schedule',
					'api_key'			: jQuery("#api_key").val(),
					'nama'				: nama,
					'jadwal_mulai'		: jadwalMulai,
					'jadwal_selesai'	: jadwalSelesai,
					'tahun_anggaran'	: this_tahun_anggaran,
					'tipe_perencanaan'	: tipe_perencanaan,
					'relasi_perencanaan': relasi_perencanaan,
					'jenis_jadwal'		: jenis_jadwal
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
					}else{
						alert(response.message)
					}
					jQuery('#jadwal_nama').val('')
					jQuery("#link_renstra").val('')
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
		jQuery("#wrap-loading").show()
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
				jQuery("#wrap-loading").hide()
				jQuery("#jadwal_nama").val(response.data.nama);
				jQuery('#jadwal_tanggal').data('daterangepicker').setStartDate(moment(response.data.waktu_awal).format('DD-MM-YYYY HH:mm'));
				jQuery('#jadwal_tanggal').data('daterangepicker').setEndDate(moment(response.data.waktu_akhir).format('DD-MM-YYYY HH:mm'));
				jQuery("#link_renstra").val(response.data.relasi_perencanaan).change();
				jQuery("#jenis_jadwal").val(response.data.jenis_jadwal).change();
			}
		})
	}

	function submitEditJadwalForm(id_jadwal_lokal){
		jQuery("#wrap-loading").show()
		let nama = jQuery('#jadwal_nama').val()
		let jadwalMulai = jQuery("#jadwal_tanggal").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss')
		let jadwalSelesai = jQuery("#jadwal_tanggal").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss')
		let this_tahun_anggaran = tahun_anggaran
		let relasi_perencanaan = jQuery("#link_renstra").val()
		let jenis_jadwal = jQuery("#jenis_jadwal").val()
		if(nama.trim() == '' || jadwalMulai == '' || jadwalSelesai == '' || this_tahun_anggaran == '' || jenis_jadwal == ''){
			jQuery("#wrap-loading").hide()
			alert("Ada yang kosong, Harap diisi semua")
			return false
		}else{
			jQuery.ajax({
				url:thisAjaxUrl,
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
					'tipe_perencanaan'	: tipe_perencanaan,
					'relasi_perencanaan': relasi_perencanaan,
					'jenis_jadwal'		: jenis_jadwal
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
					}else{
						alert(`GAGAL! \n${response.message}`)
					}
					jQuery('#jadwal_nama').val('')
					jQuery("#link_renstra").val('')
					jQuery("#jenis_jadwal").val('')
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
					'action' 				: 'submit_lock_schedule',
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

	function copy_usulan(){
		if(confirm('Apakah anda yakin untuk melakukan ini? data penetapan akan diupdate sama dengan data usulan.')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "copy_usulan_renja",
	          		"api_key": jQuery("#api_key").val(),
					"tahun_anggaran": tahun_anggaran
	          	},
	          	dataType: "json",
	          	success: function(res){
	          		alert(res.message);
	          		jQuery('#wrap-loading').hide();
	          	}
	        });
		}
	}

	function copy_data_renstra(id_jadwal){
		if(confirm('Apakah anda yakin untuk melakukan ini? data RENJA akan diisi sesuai data RENSTRA tahun berjalan!.')){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajax.url,
	          	type: "post",
	          	data: {
	          		"action": "copy_data_renstra_ke_renja",
	          		"api_key": jQuery("#api_key").val(),
					'id_jadwal': id_jadwal
	          	},
	          	dataType: "json",
	          	success: function(res){
	          		alert(res.message);
	          		jQuery('#wrap-loading').hide();
	          	}
	        });
		}
	}

	function report(id_jadwal_lokal){
		let modal = `
			<div class="modal fade" id="modal-report" tab-index="-1" role="dialog" aria-labelledby="modal-indikator-renja-label" aria-hidden="true">
			  <div class="modal-dialog modal-lg" role="document" style="min-width:1450px">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title">Report</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      
			      <div class="modal-body">
				    <div class="container-fluid">
					    <div class="row">
						    <div class="col-md-2">Unit Kerja</div>
						    <div class="col-md-6">
						    	<select class="form-control list_perangkat_daerah" id="list_perangkat_daerah"></select>
						    </div>
					    </div></br>
					    <div class="row">
					    	<div class="col-md-2">Jenis Laporan</div>
					    	<div class="col-md-6">
					      		<select class="form-control jenis" id="jenis">
					      			<option value="-">Pilih Jenis</option>
					      			<option value="pagu_total">Format Pagu Total Per Unit Kerja</option>
				      			</select>
					    	</div>
					    </div></br>
					    <div class="row">
					    	<div class="col-md-2"></div>
					    	<div class="col-md-6 action-footer">
					      		<button type="button" class="btn btn-success btn-preview" onclick="preview('${id_jadwal_lokal}')">Preview</button>
					    	</div>
					    </div></br>
					</div>
			      </div>
			      <div class="modal-preview" style="padding:10px"></div>
			    </div>
			  </div>
			</div>`;

		jQuery("body .report").html(modal);
		list_perangkat_daerah()
		.then(function(){
			jQuery("#modal-report").modal('show');
			jQuery('.jenis').select2({width: '100%'});
		});
	}

	function preview(id_jadwal_lokal){

		let jenis=jQuery("#jenis").val();
		let id_unit=jQuery("#list_perangkat_daerah").val();

		if(id_unit=='' || id_unit=='undefined'){
			alert('Unit kerja belum dipilih');
			return;
		}

		switch(jenis){
			case 'pagu_total':
				generate(id_unit, id_jadwal_lokal, 'view_pagu_total_renja', 'Laporan Pagu Akumulasi Per Unit Kerja');
				break;

			case '-':
				alert('Jenis laporan belum dipilih');
				break;
		}
	}

	function generate(id_unit, id_jadwal_lokal, action, title){
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url:ajax.url,
			type:'post',
			dataType:'json',
			data:{
				action:action,
				id_unit:id_unit,
				id_jadwal_lokal:id_jadwal_lokal,
				tahun_anggaran:tahun_anggaran,
				api_key:jQuery("#api_key").val(),
			},
			success:function(response){
				if(response.status=='error'){
					alert(response.message);
				}else{
					jQuery("#modal-report .modal-preview").html(response.html);
					jQuery("#modal-report .modal-preview").css('overflow-x', 'auto');
					jQuery("#modal-report .modal-preview").css('margin-right','20px');
					jQuery("#modal-report .modal-preview").css('padding', '15px');
					jQuery('#modal-report .export-excel').attr("disabled", false);
					jQuery('#modal-report .export-excel').attr("title", title);
					var table = jQuery("#table-renja").DataTable( {
				        dom: 'Blfrtip',
				        lengthMenu: [
				            [10, 25, 50, -1],
				            [10, 25, 50, 'All'],
				        ],
				        buttons: [
				            'excel'
				        ]
				    } );
				    jQuery('#modal-report .action-footer .dt-buttons').remove();
				    jQuery('#modal-report .action-footer').append(table.buttons().container());
				    jQuery('#modal-report .action-footer .dt-buttons').css('margin-left', '5px');
				    jQuery('#modal-report .action-footer .buttons-excel').addClass('btn btn-primary');
				    jQuery('#modal-report .action-footer .buttons-excel span').html('Export Excel');
				}
				jQuery("#wrap-loading").hide();
			}
		})
	}

	function list_perangkat_daerah(){
		jQuery('#wrap-loading').show();
		return new Promise(function(resolve, reject){
			if(typeof list_perangkat_daerah_global == 'undefined'){
				jQuery.ajax({
					url:ajax.url,
					type:'post',
					dataType:'json',
					data:{
						action:'list_perangkat_daerah',
						tahun_anggaran:tahun_anggaran,
						api_key:jQuery("#api_key").val(),
					},
					success:function(response){
						jQuery('#wrap-loading').hide();
						if(response.status){
							list_perangkat_daerah_global = response.list_skpd_options;
							jQuery("#list_perangkat_daerah").html(list_perangkat_daerah_global);
							jQuery('#list_perangkat_daerah').select2({width: '100%'});
							return resolve();
						}

						alert('Oops ada kesalahan load data Unit kerja');
						return resolve();
					}
				})
			}else{
				jQuery("#list_perangkat_daerah").html(list_perangkat_daerah_global);
				jQuery('#list_perangkat_daerah').select2({width: '100%'});
				return resolve();
			}
		})
	}

</script> 
