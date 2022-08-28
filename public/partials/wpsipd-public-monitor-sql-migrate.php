<?php
	$body = "";
	$path = WPSIPD_PLUGIN_PATH.'/sql-migrate';
	$files = array_diff(scandir($path), array('.', '..'));
	$data = array(
		0 => '
			<tr time="0" file="tabel.sql">
				<td class="text-center">-</td>
				<td class="text-center">tabel.sql</td>
				<td class="text-center">
					<button onclick="run_sql_migrate(\'tabel.sql\'); return false;" class="btn btn-primary">RUN</button>
				</td>
			</tr>
		'
	);
	foreach($files as $k => $v){
		$tgl = str_replace('migrate-', '', $v);
		$tgl = str_replace('.sql', '', $tgl);
		$time = strtotime($tgl);
		$data[$time] = '
			<tr time="'.$time.'" file="'.$v.'">
				<td class="text-center">'.$tgl.'</td>
				<td class="text-center">'.$v.'</td>
				<td class="text-center">
					<button onclick="run_sql_migrate(\''.$v.'\'); return false;" class="btn btn-primary">RUN</button>
				</td>
			</tr>
		';
	}
	krsort($data);
	$body = implode('', $data);
	$last_update = get_option('_last_update_sql_migrate');
	if(empty($last_update)){
		$last_update = 'Belum pernah dirun!';
	}

	$versi = get_option('_wp_sipd_db_version');
    if($versi !== $this->version){
    	$ket = '
    		<div class="notice notice-warning is-dismissible">
        		<p>Versi database WP-SIPD tidak sesuai! harap dimutakhirkan. Versi saat ini=<b>'.$this->version.'</b> dan versi WP-SIPD kamu=<b>'.$versi.'</p>
         	</div>
         ';
    }else{
    	$ket = '
    		<div class="notice notice-warning is-dismissible">
        		<p>Versi database WP-SIPD sudah yang terbaru! Versi=<b>'.$this->version.'</b></p>
         	</div>
         ';
    }
?>
<style type="text/css">
	.warning {
		background: #f1a4a4;
	}
	.hide {
		display: none;
	}
	.terpilih {
	    background: #d4ffd4;
	}
</style>
<div class="cetak">
	<div style="padding: 10px;">
		<input type="hidden" value="<?php echo get_option( '_crb_api_key_extension' ); ?>" id="api_key">
		<h1 class="text-center">Monitoring SQL migrate WP-SIPD</h1>
		<h3 class="text-center"><?php echo $ket; ?></h3>
		<h3 class="text-center">Update terakhir: <b id="status_update"><?php echo $last_update; ?></b></h3>
		<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
			<thead id="data_header">
				<tr>
					<th class="text-center">Tanggal</th>
					<th class="text-center">Nama File</th>
					<th class="text-center">Aksi</th>
				</tr>
			</thead>
			<tbody id="data_body">
				<?php echo $body; ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
	function run_sql_migrate(file) {
		jQuery("#wrap-loading").show();
		jQuery.ajax({
			url: ajax.url,
	      	type: "post",
	      	data: {
	      		"action": "run_sql_migrate",
	      		"api_key": jQuery('#api_key').val(),
	      		"file": file
	      	},
			dataType: "json",
	      	success: function(data){
	      		if(data.status == 'success'){
	      			alert('Sukses: '+data.message);
					jQuery("#status_update").html(data.value);
					jQuery(".notice").html('<p>Versi database WP-SIPD sudah yang terbaru! Versi=<b>'+data.version+'</b></p>');
					update_status();
				}else{
	      			alert('Error: '+data.message);
				}
				jQuery("#wrap-loading").hide();
			},
			error: function(e) {
				console.log(e);
			}
		});
	}

	function update_status(){
		var file = jQuery('#status_update').text().split(' ')[0];
		jQuery('.terpilih').removeClass('terpilih');
		jQuery('tr[file="'+file+'"]').addClass('terpilih');
	}

	update_status();
</script>