<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

global $wpdb;

$data_all = [
	'data' => []
];

// pokin level 1
$pohon_kinerja_level_1 = $wpdb->get_results($wpdb->prepare("SELECT * FROM data_pohon_kinerja WHERE id=%d AND parent=%d AND level=%d AND active=%d ORDER BY id", $_GET['id'], 0, 1, 1), ARRAY_A);
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

$style0 = 'style=\"color:#252020;font-size:13px; font-weight:600; padding:20px\"';
$style1 = 'style=\"color: #0d0909; font-size:12px; font-weight:600;font-style:italic; border-radius: 5px; background: #efd655; padding:10px\"';
$style2 = 'style=\"color:#454810; font-size:12px; font-weight:600; font-style:italic; background: #4df8ef; padding:10px\"';
$style3 = 'style=\"color: #0d0909; font-size:12px; font-weight:600;font-style:italic; border-radius: 5px; background: #f84d4d; padding:10px\"';
$style4 = 'style=\"color: #0d0909; font-size:12px; font-weight:600;font-style:italic; border-radius: 5px; background: #5995e9; padding:10px\"';

$data_temp= [];
if(!empty($data_all['data'])){

		foreach ($data_all['data'] as $keylevel1 => $level_1) {
				$data_temp[$keylevel1][0] = (object)[
			      'v' => "<div ".$style0.">".trim($level_1['label'])."</div>",
			      'f' => "<div ".$style0.">".trim($level_1['label'])."</div>",
		    ];

		    if(!empty($level_1['indikator'])){

				    foreach ($level_1['indikator'] as $keyindikatorlevel1 => $indikator) {
				        $data_temp[$keylevel1][0]->f.="<div ".$style1.">".$indikator['label_indikator_kinerja']."</div>";
				    }
		    }

		    if(!empty($level_1['data'])){

				    foreach ($level_1['data'] as $keylevel2 => $level_2) {
				        $data_temp[$keylevel2][0] = (object)[
					          'v' => "<div ".$style0.">".trim($level_2['label'])."</div>",
					          'f' => "<div ".$style0.">".trim($level_2['label'])."</div>",
				        ];

				        if(!empty($level_2['indikator'])){

						        foreach ($level_2['indikator'] as $keyindikatorlevel2 => $indikator) {
						            $data_temp[$keylevel2][0]->f.="<div ".$style3.">".$indikator['label_indikator_kinerja']."</div>";
						        }
				        }

				        if(!empty($level_2['data'])){

						        foreach ($level_2['data'] as $keylevel3 => $level_3) {
						            $data_temp[$keylevel3][0] = (object)[
						              'v' => "<div ".$style0.">".trim($level_3['label'])."</div>",
						              'f' => "<div ".$style0.">".trim($level_3['label'])."</div>",
						            ];

						            if(!empty($level_3['indikator'])){

								            foreach ($level_3['indikator'] as $keyindikatorlevel3 => $indikator) {
								                $data_temp[$keylevel3][0]->f.="<div ".$style4.">".$indikator['label_indikator_kinerja']."</div>";
								            }
						            }

						            $data_temp[$keylevel3][1] = "<div ".$style0.">".trim($level_2['label'])."</div>";
						            $data_temp[$keylevel3][2] = '';

						            if(!empty($level_3['data'])){

						            		foreach ($level_3['data'] as $keylevel4 => $level_4) {
								            		$data_temp[$keylevel4][0] = (object)[
										              'v' => "<div ".$style0.">".trim($level_4['label'])."</div>",
										              'f' => "<div ".$style0.">".trim($level_4['label'])."</div>",
										            ];

										            if(!empty($level_4['indikator'])){
										            	
										            		foreach ($level_4['indikator'] as $keyindikatorlevel4 => $indikator) {
												                $data_temp[$keylevel4][0]->f.="<div ".$style4.">".$indikator['label_indikator_kinerja']."</div>";
												            }
										            }

										            $data_temp[$keylevel4][1] = "<div ".$style0.">".trim($level_3['label'])."</div>";
										            $data_temp[$keylevel4][2] = '';
										        }
						            }
						        }
				        }

						    $data_temp[$keylevel2][1] = "<div ".$style0.">".trim($level_1['label'])."</div>";
						    $data_temp[$keylevel2][2] = '';
					  }
			  }

			  $data_temp[$keylevel1][1] = '';
			  $data_temp[$keylevel1][2] = '';
		}
}

// echo '<pre>'; print_r($data_temp); echo '</pre>';die();

?>

<style type="text/css">
  .google-visualization-orgchart-node{
    background: #53cb82;
  }
  #chart_div .google-visualization-orgchart-connrow-medium{
    height: 34px;
  }
  #chart_div .google-visualization-orgchart-linebottom {
    border-bottom: 4px solid #f84d4d;
  }

  #chart_div .google-visualization-orgchart-lineleft {
    border-left: 4px solid #f84d4d;
  }

  #chart_div .google-visualization-orgchart-lineright {
    border-right: 4px solid #f84d4d;
  }

  #chart_div .google-visualization-orgchart-linetop {
    border-top: 4px solid #f84d4d;
  }
</style>

<h4 style="text-align: center; margin: 0; font-weight: bold;">Pohon Kinerja</h4><br>
<div id="cetak" title="Laporan Pohon Kinerja" style="padding: 5px; overflow: auto; height: 100vh;">
    <div id="chart_div" ></div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

    google.charts.load('current', {packages:["orgchart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data_temp = '<?php echo json_encode(array_values($data_temp)); ?>';
      data_all = JSON.parse(data_temp);
      console.log(data_all);

      var data = new google.visualization.DataTable();
        data.addColumn('string', 'Level1');
        data.addColumn('string', 'Level2');
        data.addColumn('string', 'ToolTip');
        data.addRows(data_all);
        data.setRowProperty(2, 'selectedStyle', 'background-color:#00FF00');
       
        // Create the chart.
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        // Draw the chart, setting the allowHtml option to true for the tooltips.
        chart.draw(data, {
          'allowHtml':true,
        });
    }
</script>