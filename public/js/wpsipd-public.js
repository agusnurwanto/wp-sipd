jQuery(document).ready(function(){
	var loading = ''
		+'<div id="wrap-loading">'
	        +'<div class="lds-hourglass"></div>'
	        +'<div id="persen-loading"></div>'
	    +'</div>';
	if(jQuery('#wrap-loading').length == 0){
		jQuery('body').prepend(loading);
	}
});

function run_download_excel(type){
	var current_url = window.location.href;
	var body = '<a id="excel" onclick="return false;" href="#" class="button button-primary">DOWNLOAD EXCEL</a>';
	if(type == 'apbd'){
		body += ''
			+'<div style="padding-top: 20px;">'
				+'<label><input id="tampil-1" type="checkbox" checked="true" onclick="tampilData(this, 1)"> Tampil Rekening</label>'
				+'<label style="margin-left: 10px;"><input id="tampil-2" type="checkbox" checked="true" onclick="tampilData(this, 2)"> Tampil Keterangan</label>'
				+'<label style="margin-left: 10px;"><input id="tampil-3" type="checkbox" checked="true" onclick="tampilData(this, 3)"> Tampil Kelompok</label>'
			+'</div>';
	}
	var download_excel = ''
		+'<div id="action-sipd" class="hide-print">'
			+body
		+'</div>';
	jQuery('body').prepend(download_excel);

	var style = '';

	style = jQuery('.cetak').attr('style');
	if (typeof style == 'undefined'){ style = ''; };
	jQuery('.cetak').attr('style', style+" font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; padding:0; margin:0; font-size:13px;");
	
	jQuery('.bawah').map(function(i, b){
		style = jQuery(b).attr('style');
		if (typeof style == 'undefined'){ style = ''; };
		jQuery(b).attr('style', style+" border-bottom:1px solid #000;");
	});
	
	jQuery('.kiri').map(function(i, b){
		style = jQuery(b).attr('style');
		if (typeof style == 'undefined'){ style = ''; };
		jQuery(b).attr('style', style+" border-left:1px solid #000;");
	});

	jQuery('.kanan').map(function(i, b){
		style = jQuery(b).attr('style');
		if (typeof style == 'undefined'){ style = ''; };
		jQuery(b).attr('style', style+" border-right:1px solid #000;");
	});

	jQuery('.atas').map(function(i, b){
		style = jQuery(b).attr('style');
		if (typeof style == 'undefined'){ style = ''; };
		jQuery(b).attr('style', style+" border-top:1px solid #000;");
	});

	jQuery('.text_tengah').map(function(i, b){
		style = jQuery(b).attr('style');
		if (typeof style == 'undefined'){ style = ''; };
		jQuery(b).attr('style', style+" text-align: center;");
	});

	jQuery('.text_kiri').map(function(i, b){
		style = jQuery(b).attr('style');
		if (typeof style == 'undefined'){ style = ''; };
		jQuery(b).attr('style', style+" text-align: left;");
	});

	jQuery('.text_kanan').map(function(i, b){
		style = jQuery(b).attr('style');
		if (typeof style == 'undefined'){ style = ''; };
		jQuery(b).attr('style', style+" text-align: right;");
	});

	jQuery('.text_block').map(function(i, b){
		style = jQuery(b).attr('style');
		if (typeof style == 'undefined'){ style = ''; };
		jQuery(b).attr('style', style+" font-weight: bold;");
	});

	jQuery('.text_15').map(function(i, b){
		style = jQuery(b).attr('style');
		if (typeof style == 'undefined'){ style = ''; };
		jQuery(b).attr('style', style+" font-size: 15px;");
	});

	jQuery('.text_20').map(function(i, b){
		style = jQuery(b).attr('style');
		if (typeof style == 'undefined'){ style = ''; };
		jQuery(b).attr('style', style+" font-size: 20px;");
	});

	jQuery('td').map(function(i, b){
		style = jQuery(b).attr('style');
		if (typeof style == 'undefined'){ style = ''; };
		jQuery(b).attr('style', style+' mso-number-format:\\@;');
	});

	jQuery('#excel').on('click', function(){
		var name = "Laporan";
		var title = jQuery('#cetak').attr('title');
		if(title){
			name = title;
		}
		tableHtmlToExcel('cetak', name);
	});
}

function tampilData(that, type){
	jQuery('.sub_keg').map(function(i, b){
		jQuery(b).find('td').eq(1).css({'padding-left':'20px'});
	});
	jQuery('.kelompok').map(function(i, b){
		jQuery(b).find('td').eq(1).css({'padding-left':'40px'});
	});
	jQuery('.keterangan').map(function(i, b){
		jQuery(b).find('td').eq(1).css({'padding-left':'60px'});
	});
	jQuery('.rekening').map(function(i, b){
		jQuery(b).find('td').eq(1).css({'padding-left':'80px'});
	});
	jQuery('.rincian').map(function(i, b){
		jQuery(b).find('td').eq(1).css({'padding-left':'100px'});
	});
	var checked = jQuery(that).eq(0).is(':checked');
	jQuery('.rekening').show();
	jQuery('.keterangan').show();
	jQuery('.kelompok').show();

	var left = 0;
	if(!checked){
		jQuery('#tampil-1').prop('checked', true);
		jQuery('#tampil-2').prop('checked', true);
		jQuery('#tampil-3').prop('checked', true);
		if(type == '1'){
			jQuery('#tampil-1').prop('checked', false);
			jQuery('.rekening').hide();
			left = '80px';
		}else if(type == '2'){
			jQuery('#tampil-1').prop('checked', false);
			jQuery('#tampil-2').prop('checked', false);
			jQuery('.rekening').hide();
			jQuery('.keterangan').hide();
			left = '60px';
		}else if(type == '3'){
			jQuery('#tampil-1').prop('checked', false);
			jQuery('#tampil-2').prop('checked', false);
			jQuery('#tampil-3').prop('checked', false);
			jQuery('.rekening').hide();
			jQuery('.keterangan').hide();
			jQuery('.kelompok').hide();
			left = '40px';
		}
		jQuery('.rincian').map(function(i, b){
			jQuery(b).find('td').eq(1).css({'padding-left':left});
		});
	}else{
		jQuery('#tampil-1').prop('checked', false);
		jQuery('#tampil-2').prop('checked', false);
		jQuery('#tampil-3').prop('checked', false);
		if(type == '1'){
			jQuery('#tampil-1').prop('checked', true);
			jQuery('#tampil-2').prop('checked', true);
			jQuery('#tampil-3').prop('checked', true);
			left = '100px';
		}else if(type == '2'){
			jQuery('.rekening').hide();
			jQuery('#tampil-2').prop('checked', true);
			jQuery('#tampil-3').prop('checked', true);
			left = '80px';
		}else if(type == '3'){
			jQuery('.rekening').hide();
			jQuery('.keterangan').hide();
			jQuery('#tampil-3').prop('checked', true);
			left = '60px';
		}
		jQuery('.rincian').map(function(i, b){
			jQuery(b).find('td').eq(1).css({'padding-left':left});
		});
	}
}

function tableHtmlToExcel(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20').replace(/#/g, '%23');
   
    filename = filename?filename+'.xls':'excel_data.xls';
   
    downloadLink = document.createElement("a");
    
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
   
        downloadLink.download = filename;
       
        downloadLink.click();
    }
}