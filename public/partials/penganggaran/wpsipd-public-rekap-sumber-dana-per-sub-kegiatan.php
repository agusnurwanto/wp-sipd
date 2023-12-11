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

$sumber_sd = 1;
if(!empty($_GET) && !empty($_GET['sumber_dana'])){
    $sumber_sd = $_GET['sumber_dana'];
}

$input = shortcode_atts( array(
	'id_skpd' => $id_unit,
    'id_jadwal_lokal' => $id_jadwal_lokal,
	'tahun_anggaran' => '2022'
), $atts );

$jadwal_lokal = $wpdb->get_row($wpdb->prepare("
    SELECT 
        j.nama AS nama_jadwal,
        j.tahun_anggaran,
        j.status,
        t.nama_tipe 
    FROM `data_jadwal_lokal` j
    INNER JOIN `data_tipe_perencanaan` t on t.id=j.id_tipe 
    WHERE j.id_jadwal_lokal=%d", $id_jadwal_lokal));

$_suffix='';
$where_jadwal='';
if($jadwal_lokal->status == 1){
    $_suffix='_history';
    $where_jadwal=' AND id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
}
$input['tahun_anggaran'] = $jadwal_lokal->tahun_anggaran;

$_suffix_sipd='';
if(strpos($jadwal_lokal->nama_tipe, '_sipd') == false){
    $_suffix_sipd = '_lokal';
}

$nama_skpd = '';
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
    $nama_skpd = $wpdb->get_var($wpdb->prepare("
        SELECT 
            CONCAT(kode_skpd, ' ',nama_skpd)
        FROM data_unit
        WHERE tahun_anggaran=%d
            and active=1
            and id_skpd=%d
    ", $input['tahun_anggaran'], $input['id_skpd']));
    if(!empty($nama_skpd)){
        $nama_skpd = '<br>'.$nama_skpd;
    }else{
        die('<h1 class="text-center">SKPD tidak ditemukan!</h1>');
    }
}
$nama_pemda = get_option('_crb_daerah');
$nama_excel = 'REKAPITULASI SUMBER DANA PER SUB KEGIATAN<br>TAHUN ANGGARAN '.$input['tahun_anggaran'].'<br>'.strtoupper($nama_pemda).$nama_skpd.'<br>'.$jadwal_lokal->nama_jadwal;

$body = '';
$total_all = 0;
foreach($data_skpd as $skpd){
	$sql = "
        SELECT 
            *
        FROM data_sub_keg_bl".$_suffix_sipd."".$_suffix."
        WHERE id_sub_skpd=%d
            AND tahun_anggaran=%d
            AND active=1
            ".$where_jadwal."
            ORDER BY kode_giat ASC, kode_sub_giat ASC";
    $subkeg = $wpdb->get_results($wpdb->prepare($sql, $skpd['id_skpd'], $input['tahun_anggaran']), ARRAY_A);
    // die($wpdb->last_query);

    foreach ($subkeg as $kk => $sub) {
    	$where_jadwal_new = '';
    	$where_jadwal_join = '';
        if($sumber_sd == 1){
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
                from data_rka".$_suffix_sipd."".$_suffix." r
               	left join data_mapping_sumberdana".$_suffix_sipd."".$_suffix." s on r.id_rinci_sub_bl = s.id_rinci_sub_bl
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
        }else{
            if(!empty($where_jadwal)){
                $where_jadwal_new = str_replace('AND id_jadwal', 'AND s_lokal.id_jadwal', $where_jadwal);
            }
            $rincian_all = $wpdb->get_results("
                SELECT 
                    s_dana.id_dana as id_sumber_dana, 
                    s_dana.kode_dana,
                    s_dana.nama_dana, 
                    SUM(s_lokal.pagudana) as total
                FROM data_dana_sub_keg".$_suffix_sipd."".$_suffix." as s_lokal
                LEFT JOIN data_sumber_dana as s_dana ON (s_lokal.iddana=s_dana.id_dana) 
                    AND s_dana.active = s_lokal.active
                    AND s_dana.tahun_anggaran = s_lokal.tahun_anggaran
                WHERE s_lokal.active = 1 
                    AND s_lokal.tahun_anggaran = ".$wpdb->prepare('%d', $input['tahun_anggaran'])." 
                    AND s_lokal.kode_sbl = ".$wpdb->prepare('%s', $sub['kode_sbl'])."
                    ".$where_jadwal_new."
                GROUP BY s_dana.id_dana 
                ORDER BY s_dana.kode_dana asc;
            ", ARRAY_A);
        }
    	// die($wpdb->last_query);
        $pagu_sumber_dana = 0;
        foreach($rincian_all as $rincian){
            if(!empty($rincian['kode_dana'])){
                $pagu_sumber_dana += $rincian['total'];
            }
        }

        foreach($rincian_all as $rincian){
            $title = '';
            $warning = '';
            if($pagu_sumber_dana != $sub['pagu']){
                $warning = 'style="background-color: #f9d9d9"';
                $title = 'title="Total pagu Sumber Dana tidak sama dengan pagu Sub Kegiatan. '.$this->_number_format($pagu_sumber_dana).' != '.$this->_number_format($sub['pagu']).'" class="tidak-sama"';
            }
	    	$body .= '
	    		<tr data-kodesbl="'.$sub['kode_sbl'].'" '.$warning.' '.$title.'>
	    			<td>'.$sub['kode_urusan'].' '.$sub['nama_urusan'].'</td>
	    			<td>'.$sub['kode_skpd'].' '.$sub['nama_skpd'].'</td>
	    			<td>'.$sub['kode_bidang_urusan'].' '.$sub['nama_bidang_urusan'].'</td>
	    			<td>'.$sub['kode_sub_skpd'].' '.$sub['nama_sub_skpd'].'</td>
	    			<td>'.$sub['kode_program'].' '.$sub['nama_program'].'</td>
	    			<td>'.$sub['kode_giat'].' '.$sub['nama_giat'].'</td>
	    			<td>'.$sub['nama_sub_giat'].'</td>
	    			<td class="text-right">'.$this->_number_format($rincian['total']).'</td>
	    			<td>'.$rincian['kode_dana'].' '.$rincian['nama_dana'].'</td>
	    		</tr>
	    	';
	    	$total_all += $rincian['total'];
        }
    }
}
?>
<div id="cetak" title="<?php echo $nama_excel; ?>" style="padding: 5px; overflow: auto;">
	<h1 class="text-center"><?php echo $nama_excel; ?></h1>
	<table class="table table-bordered" id="tabel-data">
		<thead>
			<tr>
				<th class="text-center">Urusan</th>
				<th class="text-center">OPD</th>
				<th class="text-center">Bidang Urusan</th>
				<th class="text-center">Sub Unit</th>
				<th class="text-center">Program</th>
				<th class="text-center">Kegiatan</th>
				<th class="text-center">Sub Kegiatan</th>
				<th class="text-center">Rincian </th>
				<th class="text-center">Sumber Dana</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="7" class="text-center">Total</th>
				<th class="text-right"><?php echo $this->_number_format($total_all); ?></th>
                <th></th>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        run_download_excel();
        var action = ''
            +'<h3 style="margin-top: 20px;">SETTING</h3><label><input type="checkbox" onclick="show_pagu_tidak_sama(this);"/> Tampilkan hanya pagu yang tidak sama antara total sub kegiatan dan total sumber dana</label>'
        jQuery('#action-sipd').append(action);
    });
    function show_pagu_tidak_sama(that){
        if(jQuery(that).is(':checked') == true){
            jQuery('#tabel-data > tbody > tr').hide();
            jQuery('#tabel-data > tbody > tr.tidak-sama').show();
        }else{
            jQuery('#tabel-data > tbody > tr').show();
        }
    }
</script>