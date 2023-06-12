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
			set_analis_komponen();
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
		if(id_skpd == '-1'){
			return;
		}
		jQuery('#pilih_skpd').val(id_skpd);
		jQuery("#wrap-loading").show();
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
		var opsi = ''
			+'<option value="-1">Pilih SKPD</option>'
			+'<option value="0">Semua SKPD</option>';
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

function set_analis_komponen(){
	if(typeof analisa_komponen == 'undefined'){
		window.analisa_komponen = [];
	}
	analisa_komponen.map(function(b, i){
		var tr = jQuery('.edit-label[data-id="'+b.id_label_komponen+'"]').closest('tr');
		tr.find('.pagu-rincian').text(b.pagu);
		tr.find('.realisasi-rincian').text(b.realisasi);
		tr.find('.jml-rincian').text(b.jml_rincian);
	});
}

function generate_lisensi(){
	jQuery('#wrap-loading').show();
	jQuery.ajax({
		url: ajaxurl,
      	type: "post",
      	data: {
      		"action": "generate_lisensi",
      		"api_key": wpsipd.api_key,
      		"server": jQuery('input[name="carbon_fields_compact_input[_crb_server_wp_sipd]"]').val(),
      		"api_key_server": jQuery('input[name="carbon_fields_compact_input[_crb_server_wp_sipd_api_key]"]').val(),
      		"no_wa": jQuery('input[name="carbon_fields_compact_input[_crb_no_wa]"]').val(),
      		"pemda": jQuery('input[name="carbon_fields_compact_input[_crb_daerah]"]').val()
      	},
      	dataType: "json",
      	success: function(data){
			jQuery('#wrap-loading').hide();
			alert(data.response.message);
			if(data.response.status == 'success'){
				jQuery('#load_ajax_carbon').html(data.response.message);
				jQuery('input[name="carbon_fields_compact_input[_crb_api_key_extension]"]').val(data.response.lisensi);
			}
		},
		error: function(e) {
			console.log(e);
			return alert(e);
		}
	});
}

function get_sinkron_modul_migrasi_data(){
	jQuery('#wrap-loading').show();
	jQuery.ajax({
		url: ajaxurl,
		type: "post",
		dataType: "json",
      	data: {
      		"action": "get_sinkron_modul_migrasi_data",
			"server": jQuery('input[name="carbon_fields_compact_input[_crb_url_server_modul_migrasi_data]"]').val(),
      		"api_key": jQuery('input[name="carbon_fields_compact_input[_crb_apikey_server_modul_migrasi_data]"]').val()
      	},
      	success: function(data){
			jQuery('#wrap-loading').hide();
			console.log(data.message)
			if(data.status == 'success'){
				alert('Data berhasil disinkron');
				jQuery('#load_ajax_carbon').html(data.message);
				jQuery("#last_sinkron").html(data.last_sinkron);
			}else{
				alert(data.message);
			}
		},
		error: function(e) {
			console.log(e);
			return alert(e);
		}
	});
}

function get_sinkron_data_sirup(){
	jQuery('#wrap-loading').show();
	jQuery.ajax({
		url: ajaxurl,
		type: "post",
		dataType: "json",
      	data: {
      		"action": "get_sinkron_data_sirup",
			"id_lokasi": jQuery('input[name="carbon_fields_compact_input[_crb_id_lokasi_sirup]"]').val(),
			"id_kldi": jQuery('input[name="carbon_fields_compact_input[_crb_id_kldi_sirup]"]').val()
      	},
      	success: function(data){
			jQuery('#wrap-loading').hide();
			console.log(data)
			if(data.status == 'success'){
				alert('Data berhasil disinkron');
				jQuery('#load_ajax_carbon').html(data.message);
				jQuery("#last_sinkron_data_sirup").html(data.last_sinkron);
			}else{
				alert(data.message);
			}
		},
		error: function(e) {
			console.log(e);
			return alert(e);
		}
	});
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
		jQuery('#analisa_komponen').on('click', function(){
			jQuery('#wrap-loading').show();
			jQuery.ajax({
				url: ajaxurl,
	          	type: "post",
	          	data: {
	          		"action": "get_analis_rincian_label",
	          		"api_key": wpsipd.api_key,
	          		"tahun_anggaran": tahun_anggaran.val()
	          	},
	          	dataType: "json",
	          	success: function(data){
					jQuery('#wrap-loading').hide();
					if(data.status == 'success'){
						window.analisa_komponen = data.data;
						set_analis_komponen();
					}else{
						return alert(data.message);
					}
				},
				error: function(e) {
					console.log(e);
					jQuery('#wrap-loading').hide();
					return alert(e);
				}
			});
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
	jQuery('#tabel_monev_sumber_dana').on('click', '#dpa_simda-to-wp_sipd', function(){
		if(confirm('Apakah anda yakin untuk singkronisasi data sumber dana dari DPA SIMDA ke WP-SIPD?')){
			alert('Masih dalam pengembangan!');
		}
	});
	jQuery('#tabel_monev_sumber_dana').on('click', '#sipd_lokal-to-wp_sipd', function(){
		if(confirm('Apakah anda yakin untuk singkronisasi data sumber dana dari SIPD Lokal ke WP-SIPD?')){
			jQuery('#wrap-loading').show();
			var tahun = jQuery('#pilih_tahun').val();
			var id_skpd = jQuery('#pilih_skpd').val();
			jQuery.ajax({
				url: ajaxurl,
	          	type: "post",
	          	data: {
	          		"action": "sumberdana_sipd_lokal_ke_wp_sipd",
	          		"api_key": wpsipd.api_key,
	          		"tahun_anggaran": tahun,
	          		"id_skpd": id_skpd
	          	},
	          	dataType: "json",
	          	success: function(data){
					jQuery('#wrap-loading').hide();
					return alert(data.message);
				},
				error: function(e) {
					console.log(e);
					jQuery('#wrap-loading').hide();
					return alert('Ada kesalahan sistem! Cek console log.');
				}
			});
		}
	});
	jQuery('#tabel_monev_sumber_dana').on('click', '#wp_sipd-to-rka_simda', function(){
		if(confirm('Apakah anda yakin untuk singkronisasi data sumber dana dan RKA dari WP-SIPD ke RKA SIMDA?')){
			jQuery('#wrap-loading').show();
			var tahun = jQuery('#pilih_tahun').val();
			var id_skpd = jQuery('#pilih_skpd').val();
			var id_all_skpd = [];
			if(id_skpd != 0){
				var nama_skpd = jQuery('#pilih_skpd option:selected').text();
				id_all_skpd.push({
					id_skpd: id_skpd,
					nama: nama_skpd
				});
			}else{
				jQuery('#pilih_skpd option').map(function(i, b){
					var id_skpd = jQuery(b).attr('value');
					if(id_skpd != 0){
						var nama_skpd = jQuery(b).text();
						id_all_skpd.push({
							id_skpd: id_skpd,
							nama: nama_skpd
						});
					}
				});
			}

			jQuery('#persen-loading').attr('persen', 0);
			jQuery('#persen-loading').html('0%');
			jQuery('#persen-loading').attr('total', id_all_skpd.length);
			var last = id_all_skpd.length-1;
			id_all_skpd.reduce(function(sequence, nextData){
                return sequence.then(function(skpd){
            		return new Promise(function(resolve_redurce, reject_redurce){
						var c_persen = +jQuery('#persen-loading').attr('persen');
						c_persen++;
						jQuery('#persen-loading').attr('persen', c_persen);
						jQuery('#persen-loading').html(((c_persen/id_all_skpd.length)*100).toFixed(2)+'%'+'<br>'+skpd.nama);
						jQuery.ajax({
							url: ajaxurl,
				          	type: "post",
				          	data: {
				          		"action": "sumberdana_wp_sipd_ke_rka_simda",
				          		"api_key": wpsipd.api_key,
				          		"tahun_anggaran": tahun,
				          		"id_skpd": skpd.id_skpd
				          	},
				          	dataType: "json",
				          	success: function(data){
				          		return resolve_redurce(nextData);
							},
							error: function(e) {
								console.log(e);
				          		return resolve_redurce(nextData);
							}
						});
            		})
                    .catch(function(e){
                        console.log(e);
                        return Promise.resolve(nextData);
                    });
                })
                .catch(function(e){
                    console.log(e);
                    return Promise.resolve(nextData);
                });
            }, Promise.resolve(id_all_skpd[last]))
            .then(function(){
				jQuery('#wrap-loading').hide();
				jQuery('#persen-loading').html('');
				jQuery('#persen-loading').attr('persen', '');
				jQuery('#persen-loading').attr('total', '');
				return alert('Berhasil singkronisasi sumberdana dan RKA dari WP-SIPD ke RKA SIMDA!');
            })
            .catch(function(e){
                console.log(e);
				return alert('Ada kesalahan sistem! Cek console log.');
            });
		}
	});
	jQuery('body').on('click', '.header-tahun', function(){
		var tahun = jQuery(this).attr('tahun');
		if(jQuery(this).hasClass('active')){
			jQuery(this).removeClass('active');
			jQuery('.body-tahun[tahun="'+tahun+'"]').removeClass('active');
		}else{
			jQuery(this).addClass('active');
			jQuery('.body-tahun[tahun="'+tahun+'"]').addClass('active');
		}
	});
});

function import_excel_bhrd(){
	var data = jQuery('#data-excel').val();
    if(!data){
        return alert('Excel Data can not empty!');
    }else{
        data = JSON.parse(data);
        jQuery('#wrap-loading').show();

        var data_all = [];
        var data_sementara = [];
        var max = 100;
        data.map(function(b, i){
            data_sementara.push(b);
            if(data_sementara.length%max == 0){
                data_all.push(data_sementara);
                data_sementara = [];
            }
        });
        if(data_sementara.length > 0){
            data_all.push(data_sementara);
        }
        var last = data_all.length - 1;
        data_all.reduce(function(sequence, nextData){
            return sequence.then(function(current_data){
                return new Promise(function(resolve_reduce, reject_reduce){
                    relayAjax({
                        url: ajaxurl,
                        type: 'post',
                        data: {
                            action: 'import_excel_bhrd',
                            data: current_data
                        },
                        success: function(res){
                            resolve_reduce(nextData);
                        },
                        error: function(e){
                            console.log('Error import excel', e);
                        }
                    });
                })
                .catch(function(e){
                    console.log(e);
                    return Promise.resolve(nextData);
                });
            })
            .catch(function(e){
                console.log(e);
                return Promise.resolve(nextData);
            });
        }, Promise.resolve(data_all[last]))
        .then(function(data_last){
            jQuery('#wrap-loading').hide();
            alert('Success import data BHRD dari excel!');
        })
        .catch(function(e){
            console.log(e);
            jQuery('#wrap-loading').hide();
            alert('Error!');
        });
    }
}

function import_excel_bhpd(){
	var data = jQuery('#data-excel').val();
    if(!data){
        return alert('Excel Data can not empty!');
    }else{
        data = JSON.parse(data);
        jQuery('#wrap-loading').show();

        var data_all = [];
        var data_sementara = [];
        var max = 100;
        data.map(function(b, i){
            data_sementara.push(b);
            if(data_sementara.length%max == 0){
                data_all.push(data_sementara);
                data_sementara = [];
            }
        });
        if(data_sementara.length > 0){
            data_all.push(data_sementara);
        }
        var last = data_all.length - 1;
        data_all.reduce(function(sequence, nextData){
            return sequence.then(function(current_data){
                return new Promise(function(resolve_reduce, reject_reduce){
                    relayAjax({
                        url: ajaxurl,
                        type: 'post',
                        data: {
                            action: 'import_excel_bhpd',
                            data: current_data
                        },
                        success: function(res){
                            resolve_reduce(nextData);
                        },
                        error: function(e){
                            console.log('Error import excel', e);
                        }
                    });
                })
                .catch(function(e){
                    console.log(e);
                    return Promise.resolve(nextData);
                });
            })
            .catch(function(e){
                console.log(e);
                return Promise.resolve(nextData);
            });
        }, Promise.resolve(data_all[last]))
        .then(function(data_last){
            jQuery('#wrap-loading').hide();
            alert('Success import data BHPD dari excel!');
        })
        .catch(function(e){
            console.log(e);
            jQuery('#wrap-loading').hide();
            alert('Error!');
        });
    }
}

function import_excel_bku_add(){
	var data = jQuery('#data-excel').val();
    if(!data){
        return alert('Excel Data can not empty!');
    }else{
        data = JSON.parse(data);
        jQuery('#wrap-loading').show();

        var data_all = [];
        var data_sementara = [];
        var max = 100;
        data.map(function(b, i){
            data_sementara.push(b);
            if(data_sementara.length%max == 0){
                data_all.push(data_sementara);
                data_sementara = [];
            }
        });
        if(data_sementara.length > 0){
            data_all.push(data_sementara);
        }
        var last = data_all.length - 1;
        data_all.reduce(function(sequence, nextData){
            return sequence.then(function(current_data){
                return new Promise(function(resolve_reduce, reject_reduce){
                    relayAjax({
                        url: ajaxurl,
                        type: 'post',
                        data: {
                            action: 'import_excel_bku_add',
                            data: current_data
                        },
                        success: function(res){
                            resolve_reduce(nextData);
                        },
                        error: function(e){
                            console.log('Error import excel', e);
                        }
                    });
                })
                .catch(function(e){
                    console.log(e);
                    return Promise.resolve(nextData);
                });
            })
            .catch(function(e){
                console.log(e);
                return Promise.resolve(nextData);
            });
        }, Promise.resolve(data_all[last]))
        .then(function(data_last){
            jQuery('#wrap-loading').hide();
            alert('Success import data BKU ADD dari excel!');
        })
        .catch(function(e){
            console.log(e);
            jQuery('#wrap-loading').hide();
            alert('Error!');
        });
    }
}

function import_excel_bku_dd(){
	var data = jQuery('#data-excel').val();
    if(!data){
        return alert('Excel Data can not empty!');
    }else{
        data = JSON.parse(data);
        jQuery('#wrap-loading').show();

        var data_all = [];
        var data_sementara = [];
        var max = 100;
        data.map(function(b, i){
            data_sementara.push(b);
            if(data_sementara.length%max == 0){
                data_all.push(data_sementara);
                data_sementara = [];
            }
        });
        if(data_sementara.length > 0){
            data_all.push(data_sementara);
        }
        var last = data_all.length - 1;
        data_all.reduce(function(sequence, nextData){
            return sequence.then(function(current_data){
                return new Promise(function(resolve_reduce, reject_reduce){
                    relayAjax({
                        url: ajaxurl,
                        type: 'post',
                        data: {
                            action: 'import_excel_bku_dd',
                            data: current_data
                        },
                        success: function(res){
                            resolve_reduce(nextData);
                        },
                        error: function(e){
                            console.log('Error import excel', e);
                        }
                    });
                })
                .catch(function(e){
                    console.log(e);
                    return Promise.resolve(nextData);
                });
            })
            .catch(function(e){
                console.log(e);
                return Promise.resolve(nextData);
            });
        }, Promise.resolve(data_all[last]))
        .then(function(data_last){
            jQuery('#wrap-loading').hide();
            alert('Success import data BKU DD dari excel!');
        })
        .catch(function(e){
            console.log(e);
            jQuery('#wrap-loading').hide();
            alert('Error!');
        });
    }
}

function singkron_bku_dd(){
    jQuery('#wrap-loading').show();
    relayAjax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'singkron_bku_dd',
            tahun_anggaran: jQuery('#tahun_anggaran').val(),
        	kelompok: jQuery('#kelompok_belanja').val()
        },
        success: function(res){
		    jQuery('#wrap-loading').hide();
		    alert('Berhasil singkron BKU DD dari WP-SIPD!');
        },
        error: function(e){
            console.log('Error singkron BKU DD dari WP-SIPD!', e);
        }
    });
}

function import_excel_bkk_infrastruktur(){
	var data = jQuery('#data-excel').val();
    if(!data){
        return alert('Excel Data can not empty!');
    }else{
        data = JSON.parse(data);
        jQuery('#wrap-loading').show();

        var data_all = [];
        var data_sementara = [];
        var max = 100;
        data.map(function(b, i){
            data_sementara.push(b);
            if(data_sementara.length%max == 0){
                data_all.push(data_sementara);
                data_sementara = [];
            }
        });
        if(data_sementara.length > 0){
            data_all.push(data_sementara);
        }
        var last = data_all.length - 1;
        data_all.reduce(function(sequence, nextData){
            return sequence.then(function(current_data){
                return new Promise(function(resolve_reduce, reject_reduce){
                    relayAjax({
                        url: ajaxurl,
                        type: 'post',
                        data: {
                            action: 'import_excel_bkk',
                            data: current_data
                        },
                        success: function(res){
                            resolve_reduce(nextData);
                        },
                        error: function(e){
                            console.log('Error import excel', e);
                        }
                    });
                })
                .catch(function(e){
                    console.log(e);
                    return Promise.resolve(nextData);
                });
            })
            .catch(function(e){
                console.log(e);
                return Promise.resolve(nextData);
            });
        }, Promise.resolve(data_all[last]))
        .then(function(data_last){
            jQuery('#wrap-loading').hide();
            alert('Success import data BKK dari excel!');
        })
        .catch(function(e){
            console.log(e);
            jQuery('#wrap-loading').hide();
            alert('Error!');
        });
    }
}

function import_excel_bkk_pilkades(){
	var data = jQuery('#data-excel').val();
    if(!data){
        return alert('Excel Data can not empty!');
    }else{
        data = JSON.parse(data);
        jQuery('#wrap-loading').show();

        var data_all = [];
        var data_sementara = [];
        var max = 100;
        data.map(function(b, i){
            data_sementara.push(b);
            if(data_sementara.length%max == 0){
                data_all.push(data_sementara);
                data_sementara = [];
            }
        });
        if(data_sementara.length > 0){
            data_all.push(data_sementara);
        }
        var last = data_all.length - 1;
        data_all.reduce(function(sequence, nextData){
            return sequence.then(function(current_data){
                return new Promise(function(resolve_reduce, reject_reduce){
                    relayAjax({
                        url: ajaxurl,
                        type: 'post',
                        data: {
                            action: 'import_excel_bkk_pilkades',
                            data: current_data
                        },
                        success: function(res){
                            resolve_reduce(nextData);
                        },
                        error: function(e){
                            console.log('Error import excel', e);
                        }
                    });
                })
                .catch(function(e){
                    console.log(e);
                    return Promise.resolve(nextData);
                });
            })
            .catch(function(e){
                console.log(e);
                return Promise.resolve(nextData);
            });
        }, Promise.resolve(data_all[last]))
        .then(function(data_last){
            jQuery('#wrap-loading').hide();
            alert('Success import data BKK Pilkades dari excel!');
        })
        .catch(function(e){
            console.log(e);
            jQuery('#wrap-loading').hide();
            alert('Error!');
        });
    }
}

function filePickedWpsipd(oEvent) {
    jQuery('#wrap-loading').show();
    // Get The File From The Input
    var oFile = oEvent.target.files[0];
    var sFilename = oFile.name;
    // Create A File Reader HTML5
    var reader = new FileReader();

    reader.onload = function(e) {
        var data = e.target.result;
        var workbook = XLSX.read(data, {
            type: 'binary'
        });

        var cek_sheet_name = false;
        workbook.SheetNames.forEach(function(sheetName) {
            // Here is your object
            console.log('sheetName', sheetName);
            if(sheetName == 'data'){
                cek_sheet_name = true;
                var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                var data = [];
                XL_row_object.map(function(b, i){
                	for(ii in b){
                		b[ii] = b[ii].replace(/(\r\n|\n|\r)/g, " ").trim();
                	}
                    data.push(b);
                });
                var json_object = JSON.stringify(data);
                jQuery('#data-excel').val(json_object);
                jQuery('#wrap-loading').hide();
            }
        });
        setTimeout(function(){
            if(false == cek_sheet_name){
                jQuery('#data-excel').val('');
                alert('Sheet dengan nama "data" tidak ditemukan!');
                jQuery('#wrap-loading').hide();
            }
        }, 2000);
    };

    reader.onerror = function(ex) {
      console.log(ex);
    };

    reader.readAsBinaryString(oFile);
}