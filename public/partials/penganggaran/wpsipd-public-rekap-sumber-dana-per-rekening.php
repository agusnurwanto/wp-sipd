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

    // nomor urut tahun anggaran RENJA sesuai jadwal tahun awal di RENSTRA
    $urut = 0;
    $nama_skpd = "";
    $nama_sub_skpd = "";
    $total_all = 0;
    foreach ($subkeg as $kk => $sub) {
    	$where_jadwal_new = '';
    	if(!empty($where_jadwal)){
    		$where_jadwal_new = 'r.'.$where_jadwal;
    	}
    	$rincian_all = $wpdb->get_results($wpdb->prepare("
            select 
                sum(r.rincian) as total,
                r.kode_akun,
                r.nama_akun,
                s.id_sumber_dana,
                d.nama_dana,
                d.kode_dana,
            from data_rka".$_suffix." r
           	left join data_mapping_sumberdana".$_suffix." s on r.id_rinci_sub_bl = s.id_rinci_sub_bl
           		and s.active = r.active
           		and s.tahun_anggaran = r.tahun_anggaran
           	left join data_sumber_dana d on d.id_dana=s.id_sumber_dana
           		and d.active = s.active
           		and d.tahun_anggaran = s.tahun_anggaran
            where r.tahun_anggaran=%d
                and r.active=1
                and r.kode_sbl=%s
                ".$where_jadwal_new."
            group by d.kode_dana ASC, r.kode_akun ASC
            order by id ASC
        ", $input['tahun_anggaran'], $sub['kode_sbl']), ARRAY_A);
        foreach($rincian_all as $rincian){
	    	$body .= '
	    		<tr>
	    			<td>'.$rincian['kode_dana'].' '.$rincian['nama_dana'].'</td>
	    			<td>'.$sub['kode_urusan'].' '.$sub['nama_urusan'].'</td>
	    			<td>'.$sub['kode_skpd'].' '.$sub['nama_skpd'].'</td>
	    			<td>'.$sub['kode_bidang_urusan'].' '.$sub['nama_bidang_urusan'].'</td>
	    			<td>'.$sub['kode_sub_skpd'].' '.$sub['nama_sub_skpd'].'</td>
	    			<td>'.$sub['kode_program'].' '.$sub['nama_program'].'</td>
	    			<td>'.$sub['kode_giat'].' '.$sub['nama_giat'].'</td>
	    			<td>'.$sub['kode_sub_giat'].' '.$sub['nama_sub_giat'].'</td>
	    			<td>'.$rincian['kode_akun'].' '.$rincian['nama_akun'].'</td>
	    			<td class="text-right">'.$this->_number_format($rincian['total']).'</td>
	    		</tr>
	    	';
	    	$total_all += $rincian['total'];
        }
    }
}
?>
<div id="cetak" title="<?php echo $nama_excel; ?>" style="padding: 5px;">
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
				<th class="text-center">Sub Kegiatan</th>
				<th class="text-center">Rekening</th>
				<th class="text-center">Rincian </th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="9">Total</td>
				<td class="text-right"><?php echo $this->_number_format($total_all); ?></td>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        run_download_excel();
    });
</script>