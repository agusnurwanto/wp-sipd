<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$id_unit = '';
if(!empty($_GET) && !empty($_GET['id_unit'])){
    $id_unit = $_GET['id_unit'];
}

$id_jadwal_lokal = '';
if(!empty($_GET) && !empty($_GET['id_jadwal_lokal'])){
    $id_jadwal_lokal = $_GET['id_jadwal_lokal'];
}

$input = shortcode_atts( array(
	'id_skpd' => $id_unit,
    'id_jadwal_lokal' => $id_jadwal_lokal,
	'tahun_anggaran' => '2022'
), $atts );

$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
    SELECT 
        nama AS nama_jadwal,
        tahun_anggaran,
        status 
    FROM `data_jadwal_lokal` 
    WHERE id_jadwal_lokal=%d", $id_jadwal_lokal));

$_suffix='';
$where_jadwal='';
if($jadwal_lokal->status == 1){
    $_suffix='_history';
    $where_jadwal=' AND id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
}
$input['tahun_anggaran'] = $jadwal_lokal->tahun_anggaran;

if($input['id_skpd'] == 'all'){
    $data_skpd = $wpdb->get_results($wpdb->prepare("
        select 
            id_skpd 
        from data_unit
        where tahun_anggaran=%d
            and active=1
        order by kode_skpd ASC
    ", $input['tahun_anggaran']), ARRAY_A);
}else{
    $data_skpd = array(array('id_skpd' => $input['id_skpd']));
}
$nama_pemda = get_option('_crb_daerah');
$nama_excel = 'TAHUN ANGGARAN '.$input['tahun_anggaran'].' '.strtoupper($nama_pemda);

$body = '';
foreach($data_skpd as $skpd){
	$sql = "
        SELECT 
            *
        FROM data_sub_keg_bl".$_suffix."
        WHERE id_sub_skpd=%d
            AND tahun_anggaran=%d
            AND active=1
            ".$where_jadwal."
            ORDER BY kode_giat ASC, kode_sub_giat ASC";
    $subkeg = $wpdb->get_results($wpdb->prepare($sql, $skpd['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
    // die($wpdb->last_query);

    $data_all = array();
    $total_all = 0;
    foreach ($subkeg as $kk => $sub) {
    	$where_jadwal_new = '';
    	$where_jadwal_join = '';
    	if(!empty($where_jadwal)){
    		$where_jadwal_new = str_replace('AND id_jadwal', 'AND r.id_jadwal', $where_jadwal);
    		$where_jadwal_join = 'and s.id_jadwal = r.id_jadwal';
    	}
    	$rincian_all = $wpdb->get_results($wpdb->prepare("
            select 
                sum(r.rincian) as total,
                s.id_sumber_dana,
                d.nama_dana,
                d.kode_dana
            from data_rka".$_suffix." r
           	left join data_mapping_sumberdana".$_suffix." s on r.id_rinci_sub_bl = s.id_rinci_sub_bl
           		and s.active = r.active
           		and s.tahun_anggaran = r.tahun_anggaran
           		".$where_jadwal_join."
           	left join data_sumber_dana d on d.id_dana=s.id_sumber_dana
           		and d.active = s.active
           		and d.tahun_anggaran = s.tahun_anggaran
            where r.tahun_anggaran=%d
                and r.active=1
                and r.kode_sbl=%s
                ".$where_jadwal_new."
            group by d.kode_dana
            order by d.kode_dana ASC
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);
    	// die($wpdb->last_query);

        if(empty($data_all[$sub['kode_giat']])){
            $data_all[$sub['kode_giat']] = array();
        }
        foreach($rincian_all as $rincian){
            if(empty($data_all[$sub['kode_giat']][$rincian['kode_dana']])){
                $data_all[$sub['kode_giat']][$rincian['kode_dana']] = array(
                    'sumber_dana' => $rincian['kode_dana'].' '.$rincian['nama_dana'],
                    'total' => 0,
                    'data' => array()
                );
            }
            $data_all[$sub['kode_giat']][$rincian['kode_dana']]['total'] += $rincian['total'];
            $data_all[$sub['kode_giat']][$rincian['kode_dana']]['data'][] = $rincian;

        }
    }

    foreach($data_all as $kode => $data){
        foreach($data as $sd){
	    	$total_all += $sd['total'];
            $body .= '
                <tr data-kode="'.$kode.'">
                    <td>'.$sd['sumber_dana'].'</td>
                    <td>'.$data['kode_urusan'].' '.$data['nama_urusan'].'</td>
                    <td>'.$data['kode_skpd'].' '.$data['nama_skpd'].'</td>
                    <td>'.$data['kode_bidang_urusan'].' '.$data['nama_bidang_urusan'].'</td>
                    <td>'.$data['kode_sub_skpd'].' '.$data['nama_sub_skpd'].'</td>
                    <td>'.$data['kode_program'].' '.$data['nama_program'].'</td>
                    <td>'.$data['kode_giat'].' '.$data['nama_giat'].'</td>
                    <td class="text-right">'.$this->_number_format($sd['total']).'</td>
                </tr>
            ';
        }
    }
}
?>
<div id="cetak" title="<?php echo $nama_excel; ?>" style="padding: 5px; overflow: auto;">
	<h1 class="text-center"><?php echo $nama_excel; ?></h1>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th class="text-center">Sumber Dana</th>
				<th class="text-center">Urusan</th>
				<th class="text-center">OPD</th>
				<th class="text-center">Bidang Urusan</th>
				<th class="text-center">Sub Unit</th>
				<th class="text-center">Program</th>
				<th class="text-center">Kegiatan</th>
				<th class="text-center">Rincian </th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="7" class="text-center">Total</th>
				<th class="text-right"><?php echo $this->_number_format($total_all); ?></th>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        run_download_excel();
    });
</script>