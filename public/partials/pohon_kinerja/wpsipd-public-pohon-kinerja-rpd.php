<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

global $wpdb;

$api_key = get_option('_crb_api_key_extension' );
$cek_jadwal = $this->validasi_jadwal_perencanaan('rpd');
$jadwal_lokal = $cek_jadwal['data'];
$id_jadwal_rpjpd = "";
$lama_pelaksanaan = 4;
$tahun_anggaran = '2022';
$namaJadwal = '-';
$mulaiJadwal = '-';
$selesaiJadwal = '-';

if(!empty($jadwal_lokal)){
    $tahun_anggaran = $jadwal_lokal[0]['tahun_anggaran'];
    $namaJadwal = $jadwal_lokal[0]['nama'];
    $mulaiJadwal = $jadwal_lokal[0]['waktu_awal'];
    $selesaiJadwal = $jadwal_lokal[0]['waktu_akhir'];
    $id_jadwal_rpjpd = $jadwal_lokal[0]['relasi_perencanaan'];
    $lama_pelaksanaan = $jadwal_lokal[0]['lama_pelaksanaan'];

    $awal = new DateTime($mulaiJadwal);
    $akhir = new DateTime($selesaiJadwal);
    $now = new DateTime(date('Y-m-d H:i:s'));

    $n=1;
    for($i=$tahun_anggaran; $i<=($tahun_anggaran-1)+$lama_pelaksanaan; $i++){
      if($i==$now->format('Y')){
        break;
      }
      $n++;
    }
}

// $timezone = get_option('timezone_string');
$nama_pemda = get_option('_crb_daerah');
$current_user = wp_get_current_user();
$bulan = date('m');
$body_monev = '';

$data_all = array(
  'data' => array(),
  'pemutakhiran_program' => 0
);
$bulan = date('m');

$tujuan_ids = array();
$sasaran_ids = array();
$program_ids = array();
$skpd_filter = array();

$sql = "
  SELECT 
    t.*,
    i.isu_teks 
  FROM data_rpd_tujuan_lokal t
  LEFT JOIN data_rpjpd_isu i ON t.id_isu = i.id
  WHERE t.active=1
  ORDER BY t.no_urut asc
";
if(!empty($id_jadwal_rpjpd)){
  $sql = "
    SELECT 
      t.*,
      i.isu_teks 
    FROM data_rpd_tujuan_lokal t
    LEFT JOIN data_rpjpd_isu_history i ON t.id_isu = i.id_asli
    WHERE t.active=1
    ORDER BY t.no_urut asc
  ";
}

$tujuan_all = $wpdb->get_results($sql, ARRAY_A);
if(!empty($tujuan_all)){
  foreach ($tujuan_all as $tujuan) {
    if(empty($data_all['data'][$tujuan['id_unik']])){
      $data_all['data'][$tujuan['id_unik']] = array(
        'nama' => $tujuan['tujuan_teks'],
        'total_akumulasi_1' => 0,
        'total_akumulasi_2' => 0,
        'total_akumulasi_3' => 0,
        'total_akumulasi_4' => 0,
        'total_akumulasi_5' => 0,
        'detail' => array(),
        'data' => array()
      );
      $tujuan_ids[$tujuan['id_unik']] = "'".$tujuan['id_unik']."'";
      
      $sql = $wpdb->prepare("
        SELECT 
          * 
        FROM data_rpd_sasaran_lokal
        WHERE kode_tujuan=%s
          AND active=1
        ORDER BY sasaran_no_urut ASC
      ", $tujuan['id_unik']);
      
      $sasaran_all = $wpdb->get_results($sql, ARRAY_A);
      if(!empty($sasaran_all)){
        foreach ($sasaran_all as $sasaran) {
          if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']])){
            $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']] = array(
              'nama' => $sasaran['sasaran_teks'],
              'total_akumulasi_1' => 0,
              'total_akumulasi_2' => 0,
              'total_akumulasi_3' => 0,
              'total_akumulasi_4' => 0,
              'total_akumulasi_5' => 0,
              'detail' => array(),
              'data' => array()
            );
            $sasaran_ids[$sasaran['id_unik']] = "'".$sasaran['id_unik']."'";
            
            $sql = $wpdb->prepare("
              SELECT 
                * 
              FROM data_rpd_program_lokal
              WHERE 
                kode_sasaran=%s and 
                active=1
              ORDER BY nama_program ASC
            ", $sasaran['id_unik']);

            $program_all = $wpdb->get_results($sql, ARRAY_A);
            if(!empty($program_all)){
              foreach ($program_all as $program) {
                $program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
                if(empty($program['kode_skpd']) && empty($program['nama_skpd'])){
                  $program['kode_skpd'] = '';
                  $program['nama_skpd'] = 'Semua Perangkat Daerah';
                }
                $skpd_filter[$program['kode_skpd']] = $program['nama_skpd'];
                if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){

                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
                    'id_unik' => $program['id_unik'],
                    'nama' => $program['nama_program'],
                    'kode_skpd' => $program['kode_skpd'],
                    'nama_skpd' => $program['nama_skpd'],
                    'total_akumulasi_1' => 0,
                    'total_akumulasi_2' => 0,
                    'total_akumulasi_3' => 0,
                    'total_akumulasi_4' => 0,
                    'total_akumulasi_5' => 0,
                    'detail' => array(),
                    'data' => array()
                  );
                }
                $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['detail'][] = $program;
                if(
                  !empty($program['id_unik_indikator']) 
                  && empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']])
                ){
                  $data_all['data'][$tujuan['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
                  $data_all['data'][$tujuan['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
                  $data_all['data'][$tujuan['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
                  $data_all['data'][$tujuan['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
                  $data_all['data'][$tujuan['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_1'] += $program['pagu_1'];
                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_2'] += $program['pagu_2'];
                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_3'] += $program['pagu_3'];
                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_4'] += $program['pagu_4'];
                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['total_akumulasi_5'] += $program['pagu_5'];
                  $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']]['data'][$program['id_unik_indikator']] = array(
                    'nama' => $program['indikator'],
                    'data' => $program
                  );
                }
              }
            }
          }
          $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
        }
      }
    }
    $data_all['data'][$tujuan['id_unik']]['detail'][] = $tujuan;
  }
}

$data_temp = [];
if(isset($data_all['data']) && !empty($data_all['data'])){
    foreach ($data_all['data'] as $tujuan) {
        if(empty($data_temp[$tujuan['nama']])){
            $data_temp[$tujuan['nama']] = [
                'nama' => $tujuan['nama'],
                'indikator' => [],
                'data' => [],
                'pagu' => $tujuan['total_akumulasi_'.$n]
            ];
        }

        if(!empty($tujuan['detail'])){
            foreach ($tujuan['detail'] as $indikator) {
               if(!empty($indikator['id_unik']) && empty($data_temp[$tujuan['nama']]['indikator'][$indikator['id_unik']])) {
                  $data_temp[$tujuan['nama']]['indikator'][$indikator['id_unik']] = $indikator['indikator_teks'];
               } 
            }
        }

        if(!empty($tujuan['data'])){
            foreach ($tujuan['data'] as $sasaran) {
                if(empty($data_temp[$tujuan['nama']]['data'][$sasaran['nama']])){
                    $data_temp[$tujuan['nama']]['data'][$sasaran['nama']] = [
                        'nama' => $sasaran['nama'],
                        'indikator' => [],
                        'data' => [],
                        'pagu' => $sasaran['total_akumulasi_'.$n]
                    ];
                }

                if(!empty($sasaran['detail'])){
                    foreach ($sasaran['detail'] as $indikator) {
                        if(!empty($indikator['id_unik']) && empty($data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['indikator'][$indikator['id_unik']])) {
                            $data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['indikator'][$indikator['id_unik']] = $indikator['indikator_teks'];
                        } 
                    }
                }

                if(!empty($sasaran['data'])){
                    foreach ($sasaran['data'] as $program) {
                       if(empty($data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['data'][$program['nama']])){
                          $data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['data'][$program['nama']] = [
                              'nama' => $program['nama'],
                              'indikator' => [],
                              'pagu' => $program['total_akumulasi_'.$n]
                          ];
                       }

                       if(!empty($program['detail'])){
                          foreach ($program['detail'] as $indikator) {
                              if(!empty($indikator['id_unik_indikator']) && empty($data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['data'][$program['nama']]['indikator'][$indikator['id_unik_indikator']])){
                                  $data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['data'][$program['nama']]['indikator'][$indikator['id_unik_indikator']] = $indikator['indikator'];
                              }
                          }
                       }
                    }
                }
            }
        }
    }
}

$style0 = 'style=\"color:#252020;font-size:13px; font-weight:600; padding:20px\"';
$style1 = 'style=\"color: #0d0909; font-size:12px; font-weight:600;font-style:italic; border-radius: 5px; background: #efd655; padding:10px\"';
$style2 = 'style=\"color:#454810; font-size:12px; font-weight:600; font-style:italic; background: #4df8ef; padding:10px\"';
$style3 = 'style=\"color: #0d0909; font-size:12px; font-weight:600;font-style:italic; border-radius: 5px; background: #f84d4d; padding:10px\"';
$style4 = 'style=\"color: #0d0909; font-size:12px; font-weight:600;font-style:italic; border-radius: 5px; background: #5995e9; padding:10px\"';

$data = [];
foreach ($data_temp as $keyTujuan => $tujuan) {
    $data[$keyTujuan][0] = (object)[
      'v' => "<div ".$style0.">".trim($tujuan['nama'])."</div>",
      'f' => "<div ".$style0.">".trim($tujuan['nama'])."</div>",
    ];

    foreach ($tujuan['indikator'] as $keyIndikatorTujuan => $indikator) {
        $data[$keyTujuan][0]->f.="<div ".$style1.">".$indikator."</div>";
    }

    $data[$keyTujuan][0]->f.="<div ".$style2."> Rp.".number_format($tujuan['pagu'], '2', ',', '.')." </div>";

    foreach ($tujuan['data'] as $keySasaran => $sasaran) {
        $data[$keySasaran][0] = (object)[
          'v' => "<div ".$style0.">".trim($sasaran['nama'])."</div>",
          'f' => "<div ".$style0.">".trim($sasaran['nama'])."</div>",
        ];

        foreach ($sasaran['indikator'] as $keyIndikatorSasaran => $indikator) {
            $data[$keySasaran][0]->f.="<div ".$style3.">".$indikator."</div>";
        }

        $data[$keySasaran][0]->f.="<div ".$style2."> Rp.".number_format($sasaran['pagu'], '2', ',', '.')." </div>";

        foreach ($sasaran['data'] as $keyProgram => $program) {
            $data[$keyProgram][0] = (object)[
              'v' => "<div ".$style0.">".trim($program['nama'])."</div>",
              'f' => "<div ".$style0.">".trim($program['nama'])."</div>",
            ];

            foreach ($program['indikator'] as $keyIndikatorProgram => $indikator) {
                $data[$keyProgram][0]->f.="<div ".$style4.">".$indikator."</div>";
            }

            $data[$keyProgram][0]->f.="<div ".$style2."> Rp.".number_format($program['pagu'], '2', ',', '.')." </div>";
            $data[$keyProgram][1] = "<div ".$style0.">".trim($sasaran['nama'])."</div>";
            $data[$keyProgram][2] = '';
        }

        $data[$keySasaran][1] = "<div ".$style0.">".trim($tujuan['nama'])."</div>";
        $data[$keySasaran][2] = '';
    }

    $data[$keyTujuan][1] = '';
    $data[$keyTujuan][2] = '';
}

$data = array_values($data);

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

<h4 style="text-align: center; margin: 0; font-weight: bold;">Pohon Kinerja RPD (Rencana Pembangunan Daerah) <br><?php echo $nama_pemda; ?><br><?php echo $now->format('Y'); ?></h4><br>
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
