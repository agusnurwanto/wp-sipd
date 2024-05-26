<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

global $wpdb;
$api_key = get_option('_crb_api_key_extension' );
$data_all = [
	'data' => []
];

// pokin level 1
$pohon_kinerja_level_1 = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_pohon_kinerja WHERE parent=%d AND level=%d AND active=%d ORDER BY id", 0, 1, 1), ARRAY_A);
if(!empty($pohon_kinerja_level_1)){
	foreach ($pohon_kinerja_level_1 as $level_1) {
		if(empty($data_all['data'][trim($level_1['label'])])){
			$data_all['data'][trim($level_1['label'])] = [
				'id' => $level_1['id'],
				'label' => $level_1['label'],
				'level' => $level_1['level'],
				'indikator' => [],
				'data' => []
			];
		}

		// indikator pokin level 1
		$indikator_pohon_kinerja_level_1 = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_pohon_kinerja WHERE parent=%d AND level=%d AND active=%d ORDER BY id", $level_1['id'], 1, 1), ARRAY_A);
		if(!empty($indikator_pohon_kinerja_level_1)){
			foreach ($indikator_pohon_kinerja_level_1 as $indikator_level_1) {
				if(!empty($indikator_level_1['label_indikator_kinerja'])){
					if(empty($data_all['data'][trim($level_1['label'])]['indikator'][(trim($indikator_level_1['label_indikator_kinerja']))])){
						$data_all['data'][trim($level_1['label'])]['indikator'][(trim($indikator_level_1['label_indikator_kinerja']))] = [
							'id' => $indikator_level_1['id'],
							'parent' => $indikator_level_1['parent'],
							'label_indikator_kinerja' => $indikator_level_1['label_indikator_kinerja'],
							'level' => $indikator_level_1['level']
						];
					}
				}
			}
		}

		// pokin level 2 
		$pohon_kinerja_level_2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_pohon_kinerja WHERE parent=%d AND level=%d AND active=%d ORDER by id", $level_1['id'], 2, 1), ARRAY_A);
		if(!empty($pohon_kinerja_level_2)){
			foreach ($pohon_kinerja_level_2 as $level_2) {
				if(empty($data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])])){
					$data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])] = [
						'id' => $level_2['id'],
						'label' => $level_2['label'],
						'level' => $level_2['level'],
						'indikator' => [],
						'data' => []
					];
				}

				// indikator pokin level 2
				$indikator_pohon_kinerja_level_2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_pohon_kinerja WHERE parent=%d AND level=%d AND active=%d ORDER BY id", $level_2['id'], 2, 1), ARRAY_A);
				if(!empty($indikator_pohon_kinerja_level_2)){
					foreach ($indikator_pohon_kinerja_level_2 as $indikator_level_2) {
						if(!empty($indikator_level_2['label_indikator_kinerja'])){
							if(empty($data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])]['indikator'][(trim($indikator_level_2['label_indikator_kinerja']))])){
								$data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])]['indikator'][(trim($indikator_level_2['label_indikator_kinerja']))] = [
									'id' => $indikator_level_2['id'],
									'parent' => $indikator_level_2['parent'],
									'label_indikator_kinerja' => $indikator_level_2['label_indikator_kinerja'],
									'level' => $indikator_level_2['level']
								];
							}
						}
					}
				}

				// pokin level 3
				$pohon_kinerja_level_3 = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_pohon_kinerja WHERE parent=%d AND level=%d AND active=%d ORDER by id", $level_2['id'], 3, 1), ARRAY_A);
				if(!empty($pohon_kinerja_level_3)){
					foreach ($pohon_kinerja_level_3 as $level_3) {
						if(empty($data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])]['data'][trim($level_3['label'])])){
							$data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])]['data'][trim($level_3['label'])] = [
								'id' => $level_3['id'],
								'label' => $level_3['label'],
								'level' => $level_3['level'],
								'indikator' => [],
								'data' => []
							];
						}

						// indikator pokin level 3
						$indikator_pohon_kinerja_level_3 = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_pohon_kinerja WHERE parent=%d AND level=%d AND active=%d ORDER BY id", $level_3['id'], 3, 1), ARRAY_A);
						if(!empty($indikator_pohon_kinerja_level_3)){
							foreach ($indikator_pohon_kinerja_level_3 as $indikator_level_3) {
								if(!empty($indikator_level_3['label_indikator_kinerja'])){
									if(empty($data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])]['data'][trim($level_3['label'])]['indikator'][(trim($indikator_level_3['label_indikator_kinerja']))])){
										$data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])]['data'][trim($level_3['label'])]['indikator'][(trim($indikator_level_3['label_indikator_kinerja']))] = [
											'id' => $indikator_level_3['id'],
											'parent' => $indikator_level_3['parent'],
											'label_indikator_kinerja' => $indikator_level_3['label_indikator_kinerja'],
											'level' => $indikator_level_3['level']
										];
									}
								}
							}
						}

						// pokin level 4
						$pohon_kinerja_level_4 = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_pohon_kinerja WHERE parent=%d AND level=%d AND active=%d ORDER by id", $level_3['id'], 4, 1), ARRAY_A);
						if(!empty($pohon_kinerja_level_4)){
							foreach ($pohon_kinerja_level_4 as $level_4) {
								if(empty($data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])]['data'][trim($level_3['label'])]['data'][trim($level_4['label'])])){
									$data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])]['data'][trim($level_3['label'])]['data'][trim($level_4['label'])] = [
										'id' => $level_4['id'],
										'label' => $level_4['label'],
										'level' => $level_4['level'],
										'indikator' => []
									];
								}

								// indikator pokin level 4
								$indikator_pohon_kinerja_level_4 = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_pohon_kinerja WHERE parent=%d AND level=%d AND active=%d ORDER BY id", $level_4['id'], 4, 1), ARRAY_A);
								if(!empty($indikator_pohon_kinerja_level_4)){
									foreach ($indikator_pohon_kinerja_level_4 as $indikator_level_4) {
										if(!empty($indikator_level_4['label_indikator_kinerja'])){
											if(empty($data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])]['data'][trim($level_3['label'])]['data'][trim($level_4['label'])]['indikator'][(trim($indikator_level_4['label_indikator_kinerja']))])){
												$data_all['data'][trim($level_1['label'])]['data'][trim($level_2['label'])]['data'][trim($level_3['label'])]['data'][trim($level_4['label'])]['indikator'][(trim($indikator_level_4['label_indikator_kinerja']))] = [
													'id' => $indikator_level_4['id'],
													'parent' => $indikator_level_4['parent'],
													'label_indikator_kinerja' => $indikator_level_4['label_indikator_kinerja'],
													'level' => $indikator_level_4['level']
												];
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

// echo '<pre>'; print_r($data_all['data']); echo '</pre>';die();

$html = '';
foreach (array_values($data_all['data']) as $key1 => $level_1) {
		$html.='<tr><td><a href="'.$this->generatePage('View Pohon Kinerja', false, '[view_pohon_kinerja]').'&id='.$level_1['id'].'" target="_blank">'.$level_1['label'].'</a></td>';
		$indikator=[];
		foreach ($level_1['indikator'] as $indikatorlevel1) {
			$indikator[]=$indikatorlevel1['label_indikator_kinerja'];
		}
		$html.='<td>'.implode("</br>", $indikator).'</td>';
		foreach (array_values($level_1['data']) as $key2 => $level_2) {
				if($key2==0){
						$html.='<td>'.$level_2['label'].'</td>';
				}else{
						$html.='<tr><td colspan="2"></td><td>'.$level_2['label'].'</td>';
				}
				$indikator=[];
				foreach ($level_2['indikator'] as $indikatorlevel2) {
						$indikator[]=$indikatorlevel2['label_indikator_kinerja'];
				}
				$html.='<td>'.implode("</br>", $indikator).'</td>';
				foreach (array_values($level_2['data']) as $key3 => $level_3) {
							if($key3==0){
									$html.='<td>'.$level_3['label'].'</td>';
							}else{
									$html.='<tr><td colspan="4"></td><td>'.$level_3['label'].'</td>';
							}
							$indikator=[];
							foreach ($level_3['indikator'] as $indikatorlevel3) {
								$indikator[]=$indikatorlevel3['label_indikator_kinerja'];
							}
						$html.='<td>'.implode("</br>", $indikator).'</td>';
						foreach (array_values($level_3['data']) as $key4 => $level_4) {
								if($key4==0){
										$html.='<td>'.$level_4['label'].'</td>';
								}else{
										$html.='<tr><td colspan="6"></td><td>'.$level_4['label'].'</td>';
								}
								$indikator=[];
								foreach ($level_4['indikator'] as $indikatorlevel4) {
									$indikator[]=$indikatorlevel4['label_indikator_kinerja'];
								}
								$html.='<td>'.implode("</br>", $indikator).'</td></tr>';
						}
				}
		}
}
?>

<style type="text/css"></style>
<h3 style="text-align: center; margin-top: 10px; font-weight: bold;">Penyusunan Pohon Kinerja</h3><br>
<div id="action" style="text-align: center; margin-top:30px; margin-bottom: 30px;">
		<a style="margin-left: 10px;" id="tambah-pohon-kinerja" onclick="return false;" href="#" class="btn btn-success">Tambah Data</a>
</div>
<div id="cetak" title="Penyusunan Pohon Kinerja" style="padding: 5px; overflow: auto; height: 100vh;">
		<table>
				<thead>
						<tr>
							<th>Level 1</th>
							<th>Indikator Kinerja</th>
							<th>Level 2</th>
							<th>Indikator Kinerja</th>
							<th>Level 3</th>
							<th>Indikator Kinerja</th>
							<th>Level 4</th>
							<th>Indikator Kinerja</th>
						</tr>
				</thead>
				<tbody>
						<?php echo $html; ?>
				</tbody>
		</table>
</div>

<div class="modal fade" id="modal-pokin" role="dialog" data-backdrop="static" aria-hidden="true">'
    <div class="modal-dialog" style="max-width: 1200px;" role="document">
        <div class="modal-content">
            <div class="modal-header bgpanel-theme">
                <h4 style="margin: 0;" class="modal-title">Data Pohon Kinerja</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span><i class="dashicons dashicons-dismiss"></i></span></button>
            </div>
            <div class="modal-body">
            	<nav>
				  	<div class="nav nav-tabs" id="nav-tab" role="tablist">
					    <a class="nav-item nav-link" id="nav-level-1-tab" data-toggle="tab" href="#nav-level-1" role="tab" aria-controls="nav-level-1" aria-selected="false">Level 1</a>
					    <a class="nav-item nav-link" id="nav-level-2-tab" data-toggle="tab" href="#nav-level-2" role="tab" aria-controls="nav-level-2" aria-selected="false">Level 2</a>
					    <a class="nav-item nav-link" id="nav-level-3-tab" data-toggle="tab" href="#nav-level-3" role="tab" aria-controls="nav-level-3" aria-selected="false">Level 3</a>
					    <a class="nav-item nav-link" id="nav-level-4-tab" data-toggle="tab" href="#nav-level-4" role="tab" aria-controls="nav-level-4" aria-selected="false">Level 4</a>
				  	</div>
				</nav>
				<div class="tab-content" id="nav-tabContent">
				  	<div class="tab-pane fade show active" id="nav-level-1" role="tabpanel" aria-labelledby="nav-level-1-tab"></div>
				  	<div class="tab-pane fade" id="nav-level-2" role="tabpanel" aria-labelledby="nav-level-2-tab"></div>
				  	<div class="tab-pane fade" id="nav-level-3" role="tabpanel" aria-labelledby="nav-level-3-tab"></div>
				  	<div class="tab-pane fade" id="nav-level-4" role="tabpanel" aria-labelledby="nav-level-4-tab"></div>
				</div>
    </div>
</div>

<!-- Modal crud -->
<div class="modal fade" id="modal-crud" data-backdrop="static"  role="dialog" aria-labelledby="modal-crud-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<script type="text/javascript">

	jQuery("#tambah-pohon-kinerja").on('click', function(){
			pokinLevel1().then(function(){
					jQuery("#pokinLevel1").DataTable();
			});
	});

	jQuery(document).on('click', '#tambah-pokin-level1', function(){
			jQuery("#modal-crud").find('.modal-title').html('Tambah Pohon Kinerja');
			jQuery("#modal-crud").find('.modal-body').html(''
						+'<form id="form-pokin">'
								+'<div class="form-group">'
										+'<textarea class="form-control" name="level_1" placeholder="Tuliskan pohon kinerja level 1"></textarea>'
								+'</div>'
						+'</form>');
			jQuery("#modal-crud").find('.modal-footer').html(''
							+'<button type="button" class="btn btn-danger" data-dismiss="modal">'
									+'Tutup'
							+'</button>'
							+'<button type="button" class="btn btn-success" id="simpan-data-pokin" '
									+'data-action="create_pokin_level1" '
									+'data-view="pokinLevel1"'
							+'>'
									+'Simpan'
							+'</button>');
			jQuery("#modal-crud").find('.modal-dialog').css('maxWidth','');
			jQuery("#modal-crud").find('.modal-dialog').css('width','');
			jQuery("#modal-crud").modal('show');
	})

	jQuery(document).on('click', '#edit-pokin-level1', function(){
			jQuery("#wrap-loading").show();
			jQuery.ajax({
					method:'POST',
					url:ajax.url,
					data:{
          		"action": "edit_pokin_level1",
          		"api_key": "<?php echo $api_key; ?>",
          		'id':jQuery(this).data('id')
					},
					dataType:'json',
					success:function(response){
							jQuery("#wrap-loading").hide();
							jQuery("#modal-crud").find('.modal-title').html('Edit pohon kinerja');
							jQuery("#modal-crud").find('.modal-body').html(``
										+`<form id="form-pokin">`
												+`<input type="hidden" name="id" value="${response.data.id}">`
												+`<div class="form-group">`
														+`<textarea class="form-control" name="level_1">${response.data.label}</textarea>`
												+`</div>`
										+`</form>`);
							jQuery("#modal-crud").find(`.modal-footer`).html(``
											+`<button type="button" class="btn btn-danger" data-dismiss="modal">`
													+`Tutup`
											+`</button>`
											+`<button type="button" class="btn btn-success" id="simpan-data-pokin" `
													+`data-action="update_pokin_level1" `
													+`data-view="pokinLevel1"`
											+`>`
													+`Update`
											+`</button>`);
							jQuery("#modal-crud").find('.modal-dialog').css('maxWidth','');
							jQuery("#modal-crud").find('.modal-dialog').css('width','');
							jQuery("#modal-crud").modal('show');
					}
			});
	})

	jQuery(document).on('click', '#hapus-pokin-level1', function(){
			if(confirm(`Data akan dihapus?`)){
					jQuery("#wrap-loading").show();
					jQuery.ajax({
							method:'POST',
							url:ajax.url,
							data:{
								'action': 'delete_pokin_level1',
					      'api_key': '<?php echo $api_key; ?>',
								'id':jQuery(this).data('id')
							},
							dataType:'json',
							success:function(response){
									jQuery("#wrap-loading").hide();
									alert(response.message);
									if(response.status){
										pokinLevel1().then(function(){
												jQuery("#pokinLevel1").DataTable();
										});
									}
							}
					})
			}
	});

	jQuery(document).on('click', '#tambah-indikator-pokin-level1', function(){
			jQuery("#modal-crud").find('.modal-title').html('Tambah Indikator');
			jQuery("#modal-crud").find('.modal-body').html(``
						+`<form id="form-pokin">`
								+`<input type="hidden" name="parent" value="${jQuery(this).data('id')}">`
								+`<input type="hidden" name="label" value="${jQuery(this).parent().parent().find('.label-level1').text()}">`
								+`<div class="form-group">`
										+`<label for="indikator-level-1">${jQuery(this).parent().parent().find('.label-level1').text()}</label>`
										+`<textarea class="form-control" name="ind_level_1" placeholder="Tuliskan indikator..."></textarea>`
								+`</div>`
						+`</form>`);
			jQuery("#modal-crud").find('.modal-footer').html(``
							+`<button type="button" class="btn btn-danger" data-dismiss="modal">`
									+`Tutup`
							+`</button>`
							+`<button type="button" class="btn btn-success" id="simpan-data-pokin" `
									+`data-action="create_indikator_pokin_level1" `
									+`data-view="pokinLevel1"`
							+`>`
									+`Simpan`
							+`</button>`);
			jQuery("#modal-crud").find('.modal-dialog').css('maxWidth','');
			jQuery("#modal-crud").find('.modal-dialog').css('width','');
			jQuery("#modal-crud").modal('show');
	})

	jQuery(document).on('click', '#edit-indikator-pokin-level1', function(){
			jQuery("#wrap-loading").show();
			jQuery.ajax({
					method:'POST',
					url:ajax.url,
					data:{
          		"action": "edit_indikator_pokin_level1",
          		"api_key": "<?php echo $api_key; ?>",
          		'id':jQuery(this).data('id')
					},
					dataType:'json',
					success:function(response){
							jQuery("#wrap-loading").hide();
							jQuery("#modal-crud").find('.modal-title').html('Edit indikator pohon kinerja');
							jQuery("#modal-crud").find('.modal-body').html(``
										+`<form id="form-pokin">`
												+`<input type="hidden" name="id" value="${response.data.id}">`
												+`<input type="hidden" name="parent" value="${response.data.parent}">`
												+`<div class="form-group">`
														+`<label for="indikator-level-1">${response.data.label}</label>`
														+`<textarea class="form-control" name="ind_level_1">${response.data.label_indikator_kinerja}</textarea>`
												+`</div>`
										+`</form>`);
							jQuery("#modal-crud").find(`.modal-footer`).html(``
											+`<button type="button" class="btn btn-danger" data-dismiss="modal">`
													+`Tutup`
											+`</button>`
											+`<button type="button" class="btn btn-success" id="simpan-data-pokin" `
													+`data-action="update_indikator_pokin_level1" `
													+`data-view="pokinLevel1"`
											+`>`
													+`Update`
											+`</button>`);
							jQuery("#modal-crud").find('.modal-dialog').css('maxWidth','');
							jQuery("#modal-crud").find('.modal-dialog').css('width','');
							jQuery("#modal-crud").modal('show');
					}
			});
	})

	jQuery(document).on('click', '#hapus-indikator-pokin-level1', function(){
			if(confirm(`Data akan dihapus?`)){
					jQuery("#wrap-loading").show();
					jQuery.ajax({
							method:'POST',
							url:ajax.url,
							data:{
								'action': 'delete_indikator_pokin_level1',
					      'api_key': '<?php echo $api_key; ?>',
								'id':jQuery(this).data('id')
							},
							dataType:'json',
							success:function(response){
									jQuery("#wrap-loading").hide();
									alert(response.message);
									if(response.status){
										pokinLevel1().then(function(){
												jQuery("#pokinLevel1").DataTable();
										});
									}
							}
					})
			}
	});

	jQuery(document).on('click', '#simpan-data-pokin', function(){
				jQuery('#wrap-loading').show();
				let modal = jQuery("#modal-crud");
				let action = jQuery(this).data('action');
				let view = jQuery(this).data('view');
				let form = getFormData(jQuery("#form-pokin"));
				
				jQuery.ajax({
						method:'POST',
						url:ajax.url,
						dataType:'json',
						data:{
							'action': action,
				      'api_key': '<?php echo $api_key; ?>',
							'data': JSON.stringify(form),
						},
						success:function(response){
							jQuery('#wrap-loading').hide();
							alert(response.message);
							if(response.status){
								runFunction(view, [form])
								modal.modal('hide');
							}
						}
				})
	});

	function pokinLevel1(){
			jQuery("#wrap-loading").show();
			return new Promise(function(resolve, reject){
					jQuery.ajax({
						url: ajax.url,
          	type: "post",
          	data: {
          		"action": "get_pokin_level1",
          		"api_key": "<?php echo $api_key; ?>"
          	},
          	dataType: "json",
          	success: function(res){
	          		jQuery('#wrap-loading').hide();
	          		let level1 = ``
		          		+`<div style="margin-top:10px">`
		          				+`<button type="button" class="btn btn-success mb-2" id="tambah-pokin-level1"><i class="dashicons dashicons-plus" style="margin-top: 2px;"></i>Tambah Data</button>`
		          		+`</div>`
		          		+`<table class="table" id="pokinLevel1">`
		          			+`<thead>`
		          				+`<tr>`
		          					+`<th class="text-center" style="width:20%">No</th>`
		          					+`<th class="text-center" style="width:60%">Label Pohon Kinerja</th>`
		          					+`<th class="text-center" style="width:20%">Aksi</th>`
		          				+`</tr>`
		          			+`</thead>`
		          			+`<tbody>`;
				          		res.data.map(function(value, index){
				          			level1 += ``
					          			+`<tr>`
						          			+`<td class="text-center">${index+1}.</td>`
						          			+`<td class="label-level1">${value.label}</td>`
						          			+`<td class="text-center">`
						          				+`<a href="javascript:void(0)" data-id="${value.id}" class="btn btn-sm btn-success" id="tambah-indikator-pokin-level1" title="Tambah Indikator"><i class="dashicons dashicons-plus"></i></a> `
						          				+`<a href="javascript:void(0)" data-id="${value.id}" class="btn btn-sm btn-warning" id="view-pokin-level2" title="Lihat pohon kinerja level 2"><i class="dashicons dashicons dashicons-menu-alt"></i></a> `
					          					+`<a href="javascript:void(0)" data-id="${value.id}" class="btn btn-sm btn-primary" id="edit-pokin-level1" title="Edit"><i class="dashicons dashicons-edit"></i></a>&nbsp;`
					          					+`<a href="javascript:void(0)" data-id="${value.id}" class="btn btn-sm btn-danger" id="hapus-pokin-level1" title="Hapus"><i class="dashicons dashicons-trash"></i></a>`
						          			+`</td>`
						          		+`</tr>`;

						          	let indikator = Object.values(value.indikator);
						          	if(indikator.length > 0){
														indikator.map(function(indikator_value, indikator_index){
																level1 += ``
														     	+`<tr>`
														      		+`<td><span style="display:none">${index+1}</span></td>`
														      		+`<td>${index+1}.${indikator_index+1} ${indikator_value.label}</td>`
														      		+`<td class="text-center">`
														      				+`<a href="javascript:void(0)" data-id="${indikator_value.id}" class="btn btn-sm btn-primary" id="edit-indikator-pokin-level1" title="Edit"><i class="dashicons dashicons-edit"></i></a> `
														      				+`<a href="javascript:void(0)" data-id="${indikator_value.id}" class="btn btn-sm btn-danger" id="hapus-indikator-pokin-level1" title="Hapus"><i class="dashicons dashicons-trash"></i></a>`
														      		+`</td>`
														      +`</tr>`;
														});
						          	}
				          		});
	          					level1+=`<tbody>`
	          			+`</table>`;

	          		jQuery("#nav-level-1").html(level1);
								jQuery('.nav-tabs a[href="#nav-level-1"]').tab('show');
								jQuery('#modal-pokin').modal('show');
								resolve();
        		}
					})
			});
	}

	function runFunction(name, arguments){
	    var fn = window[name];
	    if(typeof fn !== 'function')
	        return;

	    var run = fn.apply(window, arguments);
	    run.then(function(){
			 		jQuery("#"+name).DataTable();
			});
	}

	function getFormData($form) {
	    let unindexed_array = $form.serializeArray();
	    let indexed_array = {};

	    jQuery.map(unindexed_array, function (n, i) {
	    	indexed_array[n['name']] = n['value'];
	    });

	    return indexed_array;
	}

</script>