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

$add_rpd='';
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

$timezone = get_option('timezone_string');
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
  select 
    t.*,
    i.isu_teks 
  from data_rpd_tujuan_lokal t
  left join data_rpjpd_isu i on t.id_isu = i.id
  where t.active=1
  order by t.no_urut asc
";
if(!empty($id_jadwal_rpjpd)){
  $sql = "
    select 
      t.*,
      i.isu_teks 
    from data_rpd_tujuan_lokal t
    left join data_rpjpd_isu_history i on t.id_isu = i.id_asli
    where t.active=1
    order by t.no_urut asc
  ";
}

$tujuan_all = $wpdb->get_results($sql, ARRAY_A);
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
      select 
        * 
      from data_rpd_sasaran_lokal
      where kode_tujuan=%s
        and active=1
        order by sasaran_no_urut asc
    ", $tujuan['id_unik']);
    $sasaran_all = $wpdb->get_results($sql, ARRAY_A);
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
          select 
            * 
          from data_rpd_program_lokal
          where kode_sasaran=%s
            and active=1
            order by nama_program ASC
        ", $sasaran['id_unik']);
        $program_all = $wpdb->get_results($sql, ARRAY_A);
        foreach ($program_all as $program) {
          $program_ids[$program['id_unik']] = "'".$program['id_unik']."'";
          if(empty($program['kode_skpd']) && empty($program['nama_skpd'])){
            $program['kode_skpd'] = '';
            $program['nama_skpd'] = 'Semua Perangkat Daerah';
          }
          $skpd_filter[$program['kode_skpd']] = $program['nama_skpd'];
          if(empty($data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']])){

            //check program
            $kode_program = explode(" ", $program['nama_program']);
            $checkProgram = $wpdb->get_row($wpdb->prepare("SELECT kode_program FROM data_prog_keg WHERE kode_program=%s AND tahun_anggaran=%d AND active=%d", $kode_program[0], $tahun_anggaran, 1), ARRAY_A);

            $statusMutakhirProgram=0;
            if(empty($checkProgram['kode_program'])){
              $statusMutakhirProgram=1;
              $data_all['pemutakhiran_program']++;
            }

            $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['data'][$program['id_unik']] = array(
              'id_unik' => $program['id_unik'],
              'nama' => $program['nama_program'],
              'kode_skpd' => $program['kode_skpd'],
              'nama_skpd' => $program['nama_skpd'],
              'statusMutakhirProgram' => $statusMutakhirProgram,
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
      $data_all['data'][$tujuan['id_unik']]['data'][$sasaran['id_unik']]['detail'][] = $sasaran;
    }
  }
  $data_all['data'][$tujuan['id_unik']]['detail'][] = $tujuan;

}

$data_temp = [];
foreach ($data_all['data'] as $tujuan) {
  if(empty($data_temp[$tujuan['nama']])){
      $data_temp[$tujuan['nama']] = [
          'nama' => $tujuan['nama'],
          'indikator' => [],
          'data' => [],
          'pagu' => $tujuan['total_akumulasi_'.$n]
      ];
  }

  foreach ($tujuan['detail'] as $indikator) {
     if(!empty($indikator['id_unik']) && empty($data_temp[$tujuan['nama']]['indikator'][$indikator['id_unik']])) {
        $data_temp[$tujuan['nama']]['indikator'][$indikator['id_unik']] = $indikator['indikator_teks'];
     } 
  }

  foreach ($tujuan['data'] as $sasaran) {
    if(empty($data_temp[$tujuan['nama']]['data'][$sasaran['nama']])){
        $data_temp[$tujuan['nama']]['data'][$sasaran['nama']] = [
            'nama' => $sasaran['nama'],
            'indikator' => [],
            'data' => [],
            'pagu' => $sasaran['total_akumulasi_'.$n]
        ];
    }

    foreach ($sasaran['detail'] as $indikator) {
        if(!empty($indikator['id_unik']) && empty($data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['indikator'][$indikator['id_unik']])) {
            $data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['indikator'][$indikator['id_unik']] = $indikator['indikator_teks'];
        } 
    }

    foreach ($sasaran['data'] as $program) {
       if(empty($data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['data'][$program['nama']])){
          $data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['data'][$program['nama']] = [
              'nama' => $program['nama'],
              'indikator' => [],
              'pagu' => $program['total_akumulasi_'.$n]
          ];
       }

      foreach ($program['detail'] as $indikator) {
        if(!empty($indikator['id_unik_indikator']) && empty($data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['data'][$program['nama']]['indikator'][$indikator['id_unik_indikator']])){
            $data_temp[$tujuan['nama']]['data'][$sasaran['nama']]['data'][$program['nama']]['indikator'][$indikator['id_unik_indikator']] = $indikator['indikator'];
        }
      }
    }
  }
}

$class = 'style=\"color:red; font-style:italic\"';
$class2 = 'style=\"color:blue; font-style:italic\"';
$data = [];
foreach ($data_temp as $keyTujuan => $tujuan) {
    $data[$keyTujuan][0] = (object)[
      'v' => trim($tujuan['nama']),
      'f' => trim($tujuan['nama']),
    ];

    foreach ($tujuan['indikator'] as $keyIndikatorTujuan => $indikator) {
        $data[$keyTujuan][0]->f.="<div ".$class.">".$indikator."</div>";
    }

    $data[$keyTujuan][0]->f.="<div ".$class2."> Rp.".number_format($tujuan['pagu'], '2', ',', '.')." </div>";

    foreach ($tujuan['data'] as $keySasaran => $sasaran) {
        $data[$keySasaran][0] = (object)[
          'v' => trim($sasaran['nama']),
          'f' => trim($sasaran['nama']),
        ];

        foreach ($sasaran['indikator'] as $keyIndikatorSasaran => $indikator) {
            $data[$keySasaran][0]->f.="<div ".$class.">".$indikator."</div>";
        }

        $data[$keySasaran][0]->f.="<div ".$class2."> Rp.".number_format($sasaran['pagu'], '2', ',', '.')." </div>";

        foreach ($sasaran['data'] as $keyProgram => $program) {
            $data[$keyProgram][0] = (object)[
              'v' => trim($program['nama']),
              'f' => trim($program['nama']),
            ];

            foreach ($program['indikator'] as $keyIndikatorProgram => $indikator) {
                $data[$keyProgram][0]->f.="<div ".$class.">".$indikator."</div>";
            }

            $data[$keyProgram][0]->f.="<div ".$class2."> Rp.".number_format($program['pagu'], '2', ',', '.')." </div>";
            $data[$keyProgram][1] = trim($sasaran['nama']);
            $data[$keyProgram][2] = '';
        }

        $data[$keySasaran][1] = trim($tujuan['nama']);
        $data[$keySasaran][2] = '';
    }

    $data[$keyTujuan][1] = '';
    $data[$keyTujuan][2] = '';
}

$data = array_values($data);

?>
<h4 style="text-align: center; margin: 0; font-weight: bold;">Pohon Kinerja RPD (Rencana Pembangunan Daerah) <br><?php echo $nama_pemda; ?><br><?php echo $now->format('Y'); ?></h4><br>
<div id="cetak" title="Laporan MONEV RENJA" style="padding: 5px; overflow: auto; height: 80vh;">
    <div id="chart_div"></div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

  run_download_excel();
    var dataHitungMundur = {
      'namaJadwal' : '<?php echo ucwords($namaJadwal)  ?>',
      'mulaiJadwal' : '<?php echo $mulaiJadwal  ?>',
      'selesaiJadwal' : '<?php echo $selesaiJadwal  ?>',
      'thisTimeZone' : '<?php echo $timezone ?>'
    }

    penjadwalanHitungMundur(dataHitungMundur);

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
       
        // Create the chart.
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        // Draw the chart, setting the allowHtml option to true for the tooltips.
        chart.draw(data, {'allowHtml':true});
    }
</script>
