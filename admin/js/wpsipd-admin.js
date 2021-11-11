function load_label(tahun_anggaran){
	jQuery("#wrap-loading").show();
	jQuery.ajax({
		url: ajaxurl,
      	type: "post",
      	data: {
      		"action": "get_label_komponen",
      		"api_key": wpsipd.api_key,
      		"tahun_anggaran": tahun_anggaran
      	},
			dataType: "json",
      	success: function(data){
			jQuery("#body_label").html(data.message);
			window.data_label_komponen = data.data;
			jQuery("#wrap-loading").hide();
		},
		error: function(e) {
			console.log(e);
		}
	});
}

function format_sumberdana(){
	var tahun = jQuery('#pilih_tahun').val();
	var id_skpd = jQuery('#pilih_skpd').val();
	get_list_skpd(tahun, function(){
		jQuery("#wrap-loading").show();
		jQuery('#pilih_skpd').val(id_skpd);
		var format = jQuery('input[name="format-sd"]:checked').attr('format-id');
		jQuery.ajax({
			url: ajaxurl,
	      	type: "post",
	      	data: {
	      		"action": "generate_sumber_dana_format",
	      		"api_key": wpsipd.api_key,
	      		"id_skpd": id_skpd,
	      		"format": format,
	      		"tahun_anggaran": tahun
	      	},
	      	success: function(data){
				jQuery("#tabel_monev_sumber_dana").html(data);
				jQuery("#wrap-loading").hide();
			},
			error: function(e) {
				console.log(e);
			}
		});
	});
}

function get_list_skpd(tahun, cb){
	if(options_skpd[tahun]){
		var opsi = '<option value="0">Semua SKPD</option>';
		options_skpd[tahun].map(function(b, i){
			opsi += '<option value="'+b.id_skpd+'">'+b.kode_skpd+' '+b.nama_skpd+'</option>';
		});
		jQuery('#pilih_skpd').html(opsi);
		cb();
	}else{
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url: ajaxurl,
	      	type: "post",
	      	data: {
	      		"action": "get_list_skpd",
	      		"api_key": wpsipd.api_key,
	      		"tahun_anggaran": tahun
	      	},
	      	dataType: 'json',
	      	success: function(data){
				window.options_skpd[tahun] = data;
				jQuery("#wrap-loading").hide();
				get_list_skpd(tahun, cb);
			},
			error: function(e) {
				get_list_skpd(tahun, cb);
				console.log(e);
			}
		});
	}
}

jQuery(document).ready(function(){
	window.options_skpd = {};
	var loading = ''
		+'<div id="wrap-loading">'
	        +'<div class="lds-hourglass"></div>'
	        +'<div id="persen-loading"></div>'
	    +'</div>';
	if(jQuery('#wrap-loading').length == 0){
		jQuery('body').prepend(loading);
	}
	if(jQuery('#tabel_monev_sumber_dana').length >= 1){
		format_sumberdana();
	}
	jQuery('#generate_user_sipd_merah').on('click', function(){
		if(confirm("Apakah anda yakin akan menggenerate user SIPD!")){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajaxurl,
	          	type: "post",
	          	data: {
	          		"action": "generate_user_sipd_merah",
	          		"api_key": wpsipd.api_key,
	          		"pass": prompt('Masukan password default untuk User yang akan dibuat')
	          	},
	          	dataType: "json",
	          	success: function(data){
					jQuery('#wrap-loading').hide();
					return alert(data.message);
				},
				error: function(e) {
					console.log(e);
					return alert(data.message);
				}
			});
		}
	});
	if(jQuery("#load_ajax_carbon").length >= 1){
		jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajaxurl,
	          	type: "post",
	          	data: {
	          		"action": "load_ajax_carbon",
	          		"api_key": wpsipd.api_key,
	          		"type": jQuery("#load_ajax_carbon").attr('data-type')
	          	},
	          	dataType: "json",
	          	success: function(data){
					jQuery('#wrap-loading').hide();
					if(data.status == 'success'){
						jQuery('#load_ajax_carbon').html(data.message);
					}else{
						return alert(data.message);
					}
				},
				error: function(e) {
					console.log(e);
					return alert(data.message);
				}
			});
	}
	if(jQuery("#body_label").length >= 1){
		var tahun_anggaran = jQuery('select[name="carbon_fields_compact_input[_crb_tahun_anggaran]"]');
		load_label(tahun_anggaran.val());
		tahun_anggaran.on("change", function(){
			load_label(jQuery(this).val());
		});
		jQuery('#tambah_label_komponen').on('click', function(){
			var nama_label = jQuery('#nama_label').val();
			var keterangan_label = jQuery('#keterangan_label').val();
			if(nama_label == '' || keterangan_label == ''){
				return alert('Nama dan keterangan label harus diisi!');
			}else{
				if(confirm("Apakah anda yakin akan menyimpan data ini!")){
					jQuery('#wrap-loading').show();
					jQuery.ajax({
						url: ajaxurl,
			          	type: "post",
			          	data: {
			          		"action": "simpan_data_label_komponen",
			          		"api_key": wpsipd.api_key,
			          		"tahun_anggaran": tahun_anggaran.val(),
			          		"id_label": jQuery('#id_label').val(),
			          		"nama": nama_label,
			          		"keterangan": keterangan_label
			          	},
			          	dataType: "json",
			          	success: function(data){
							if(data.status == 'success'){
								jQuery('#id_label').val('');
								jQuery('#nama_label').val('');
								jQuery('#keterangan_label').val('');
								load_label(tahun_anggaran.val());
							}else{
								jQuery('#wrap-loading').hide();
							}
							return alert(data.message);
						},
						error: function(e) {
							console.log(e);
							return alert(data.message);
						}
					});
				}
			}
		});
		jQuery('#body_label').on('click', '.edit-label', function(){
			var id_label = jQuery(this).attr('data-id');
			data_label_komponen.map(function(b, i){
				if(b.id == id_label){
					jQuery('#id_label').val(id_label);
					jQuery('#nama_label').val(b.nama);
					jQuery('#keterangan_label').val(b.keterangan);
				}
			});
		});
		jQuery('#body_label').on('click', '.hapus-label', function(){
			var id_label = jQuery(this).attr('data-id');
			data_label_komponen.map(function(b, i){
				if(b.id == id_label){
					if(confirm("Apakah anda yakin akan menghapus label \""+b.nama+"\"!")){
						jQuery('#wrap-loading').show();
						jQuery.ajax({
							url: ajaxurl,
				          	type: "post",
				          	data: {
				          		"action": "hapus_data_label_komponen",
				          		"api_key": wpsipd.api_key,
				          		"tahun_anggaran": tahun_anggaran.val(),
				          		"id_label": id_label
				          	},
				          	dataType: "json",
				          	success: function(data){
								if(data.status == 'success'){
									load_label(tahun_anggaran.val());
								}
								return alert(data.message);
							},
							error: function(e) {
								console.log(e);
								return alert(data.message);
							}
						});
					}
				}
			});
		});
	}
});