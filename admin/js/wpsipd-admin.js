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

jQuery(document).ready(function(){
	var loading = ''
		+'<div id="wrap-loading">'
	        +'<div class="lds-hourglass"></div>'
	        +'<div id="persen-loading"></div>'
	    +'</div>';
	if(jQuery('#wrap-loading').length == 0){
		jQuery('body').prepend(loading);
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