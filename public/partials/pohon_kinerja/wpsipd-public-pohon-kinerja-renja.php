<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$data_label = $wpdb->get_results($wpdb->prepare("
	SELECT 
		* 
	FROM data_tag_sub_keg 
	WHERE 
		namalabel=%s AND 
		tahun_anggaran=%d AND 
		active=%d", 
$atts['namalabel'], $atts['tahun_anggaran'], 1), ARRAY_A);

$data = [];
$list_label_unit = [];
if(!empty($data_label)){
	foreach ($data_label as $label) {
		if(empty($data[$label['namalabel']])){
			$data[$label['namalabel']] = [
				'namalabel' => $label['namalabel'],
				'tahun_anggaran' => $label['tahun_anggaran'],
				'pagu' => 0,
				'data' => []
			];
		}

		if(empty($list_label_unit[$label['namalabel']])){
			$list_label_unit[$label['namalabel']] = [
				'namalabel' => $label['namalabel'],
				'unit' => []
			];
		}

		$data_sub_giat = $wpdb->get_results($wpdb->prepare("
			SELECT DISTINCT 
				a.id_sub_skpd, 
				a.id_skpd, 
				a.kode_sbl, 
				a.nama_sub_giat, 
				a.pagu, 
				b.nama_skpd AS unit, 
				c.nama_skpd AS sub_unit 
			FROM data_sub_keg_bl a 
			LEFT JOIN data_unit b ON a.id_skpd=b.id_unit AND b.is_skpd=1 
			LEFT JOIN data_unit c ON c.id_skpd=a.id_sub_skpd AND c.is_skpd=0 
			WHERE 
				a.kode_sbl=%s AND 
				a.tahun_anggaran=%d AND 
				a.active=%d 
			ORDER BY 
				a.id_skpd, 
				a.id_sub_skpd", 
		$label['kode_sbl'], $label['tahun_anggaran'], 1), ARRAY_A);

		if(!empty($data_sub_giat)){
			foreach ($data_sub_giat as $sub_giat) {

					if(empty($data[$label['namalabel']]['data'][$sub_giat['id_skpd']])){
						$data[$label['namalabel']]['data'][$sub_giat['id_skpd']]=[
							'id_unit' => $sub_giat['id_skpd'],
							'unit' => $sub_giat['unit'],
							'pagu' => 0,
							'data' => []
						];
					}

					if(empty($list_label_unit[$label['namalabel']]['unit'][$sub_giat['id_skpd']])){
						$list_label_unit[$label['namalabel']]['unit'][$sub_giat['id_skpd']]=[
							'id_unit' => $sub_giat['id_skpd']
						];
					}

					if(empty($data[$label['namalabel']]['data'][$sub_giat['id_skpd']]['data'][$sub_giat['id_sub_skpd']])){
						$unit = $sub_giat['unit'];
						if($sub_giat['id_skpd']!=$sub_giat['id_sub_skpd']){
							$unit = $sub_giat['sub_unit'];
						}
						$data[$label['namalabel']]['data'][$sub_giat['id_skpd']]['data'][$sub_giat['id_sub_skpd']]=[
							'id_sub_unit' => $sub_giat['id_sub_skpd'],
							'sub_unit' => $unit,
							'pagu' => 0,
							'data' => []
						];
					}

					if(empty($data[$label['namalabel']]['data'][$sub_giat['id_skpd']]['data'][$sub_giat['id_sub_skpd']]['data'][$sub_giat['kode_sbl']])){
						$data[$label['namalabel']]['data'][$sub_giat['id_skpd']]['data'][$sub_giat['id_sub_skpd']]['data'][$sub_giat['kode_sbl']]=[
							'kode_sbl' => $sub_giat['kode_sbl'],
							'nama_sub_giat' => $sub_giat['nama_sub_giat'],
							'pagu' => $sub_giat['pagu']
						];
					}

					$data[$label['namalabel']]['data'][$sub_giat['id_skpd']]['data'][$sub_giat['id_sub_skpd']]['pagu']+=$sub_giat['pagu'];
					$data[$label['namalabel']]['data'][$sub_giat['id_skpd']]['pagu']+=$sub_giat['pagu'];
					$data[$label['namalabel']]['pagu']+=$sub_giat['pagu'];
			}
		}
	}
}

if(!empty($list_label_unit)){
	foreach ($list_label_unit as $label_unit) {
		foreach ($label_unit['unit'] as $unit) {
			ksort($data[$label_unit['namalabel']]['data'][$unit['id_unit']]['data']);
		}
	}
}

// echo '<pre>';print_r($data);echo '</pre>';die();

$styleHide = 'style=\"display:none\"';
$style0 = 'style=\"color:#252020;font-size:13px; font-weight:600; padding:20px\"';
$style1 = 'style=\"color: #0d0909; font-size:12px; font-weight:600;font-style:italic; border-radius: 5px; background: #efd655; padding:10px\"';

$data_temp = [];
foreach ($data as $keyLabel => $label) {
    $data_temp[$keyLabel][0] = (object)[
      'v' => "<div ".$style0.">".trim(preg_replace('/\s\s+/', ' ', $label['namalabel']))."</div>",
      'f' => "<div ".$style0.">".trim(preg_replace('/\s\s+/', ' ', $label['namalabel']))."</div>",
    ];

    $data_temp[$keyLabel][0]->f.="<div ".$style1."> Rp.".number_format($label['pagu'], '2', ',', '.')." </div>";

    foreach ($label['data'] as $keyUnit => $unit) {
    	$data_temp[$keyUnit."-"][0] = (object)[
          'v' => "<div ".$style0.">".trim(preg_replace('/\s\s+/', ' ', $unit['unit']))."</div>",
          'f' => "<div ".$style0.">".trim(preg_replace('/\s\s+/', ' ', $unit['unit']))."</div>",
        ];

        $data_temp[$keyUnit."-"][0]->f.="<div ".$style1."> Rp.".number_format($unit['pagu'], '2', ',', '.')." </div>";

        foreach ($unit['data'] as $keySubUnit => $subUnit) {
        	
        	if($keySubUnit==$keyUnit){
        		$keySubUnit=$keySubUnit;
        		$subUnit['sub_unit']=$subUnit['sub_unit']." (Sub Unit)";
        	}
        	
            $data_temp[$keySubUnit][0] = (object)[
              'v' => "<div ".$style0.">".trim(preg_replace('/\s\s+/', ' ', $subUnit['sub_unit']))."</div>",
              'f' => "<div ".$style0.">".trim(preg_replace('/\s\s+/', ' ', $subUnit['sub_unit']))."</div>",
            ];

            foreach ($subUnit['data'] as $keySubKegBl => $subKegBl) {
	            
	            $data_temp[$keySubKegBl][0] = (object)[
	              'v' => "<div ".$style0.">".trim(preg_replace('/\s\s+/', ' ', "<div ".$styleHide.">".$subKegBl['kode_sbl']."</div>".$subKegBl['nama_sub_giat']))."</div>",
	              'f' => "<div ".$style0.">".trim(preg_replace('/\s\s+/', ' ', "<div ".$styleHide.">".$subKegBl['kode_sbl']."</div>".$subKegBl['nama_sub_giat']))."</div>",
	            ];

	            $data_temp[$keySubKegBl][0]->f.="<div ".$style1."> Rp.".number_format($subKegBl['pagu'], '2', ',', '.')." </div>";
	            $data_temp[$keySubKegBl][1] = "<div ".$style0.">".trim(preg_replace('/\s\s+/', ' ', $subUnit['sub_unit']))."</div>";
	            $data_temp[$keySubKegBl][2] = '';
            }

            $data_temp[$keySubUnit][0]->f.="<div ".$style1."> Rp.".number_format($subUnit['pagu'], '2', ',', '.')." </div>";
            $data_temp[$keySubUnit][1] = "<div ".$style0.">".trim(preg_replace('/\s\s+/', ' ', $unit['unit']))."</div>";
            $data_temp[$keySubUnit][2] = '';
        }

        $data_temp[$keyUnit."-"][1] = "<div ".$style0.">".trim(preg_replace('/\s\s+/', ' ', $label['namalabel']))."</div>";
        $data_temp[$keyUnit."-"][2] = '';
    }

    $data_temp[$keyLabel][1] = '';
    $data_temp[$keyLabel][2] = '';
}

$data = array_values($data_temp);

// echo '<pre>';print_r($data);echo '</pre>';die();

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

<h4 style="text-align: center; margin: 0; font-weight: bold;">Pohon Kinerja Renja (Tagging) <br><?php echo $atts['tahun_anggaran']; ?></h4><br>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; height: 100vh;">
    <div id="chart_div" ></div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

    var aksi = ''
    +'<h3 style="margin-top: 30px;"></h3>';
    jQuery('#action-sipd').append(aksi);

    google.charts.load('current', {packages:["orgchart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data_temp = '<?php echo json_encode($data); ?>';
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