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
	          		"api_key": jQuery('input[name="carbon_fields_compact_input[_crb_api_key_extension]"]').val(),
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
});