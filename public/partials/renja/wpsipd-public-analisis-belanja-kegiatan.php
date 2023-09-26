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

$_suffix_sipd='';
if(strpos($jadwal_lokal->nama_tipe, '_sipd') == false){
    $_suffix_sipd = '_lokal';
}

if($input['id_skpd'] == 'all'){
    $where_skpd = '';
    $nama_skpd = '';
}else{
    $where_skpd = ' AND id_sub_skpd = '.$input['id_skpd'].' ';
    $nama_skpd_tunggal = $wpdb->get_row($wpdb->prepare("
    SELECT 
        nama_skpd
    FROM `data_unit` 
    WHERE id_skpd=%d
        AND tahun_anggaran=%d", $input['id_skpd'], $input['tahun_anggaran']));
    $nama_skpd = '<br>'.$nama_skpd_tunggal->nama_skpd;
}

$nama_pemda = get_option('_crb_daerah');
$nama_excel = 'Analisis Belanja per-KEGIATAN '.$input['tahun_anggaran'].' '.strtoupper($nama_pemda);
echo '<div id="cetak" title="'.$nama_excel.'" style="padding: 5px;">';

$sql = "
    SELECT
        kode_giat,
        nama_giat,
        sum(pagu) as total_pagu,
        COUNT(DISTINCT id_sub_skpd) as skpd  
    FROM data_sub_keg_bl".$_suffix_sipd."".$_suffix."
    WHERE tahun_anggaran=%d
    AND active=1
    ".$where_jadwal." 
    ".$where_skpd."
    GROUP by kode_giat
    ORDER BY kode_giat ASC";
$analisis_kegiatan = $wpdb->get_results($wpdb->prepare($sql,$input['tahun_anggaran']), ARRAY_A);

$data_all = array(
    'total' => 0,
    'data'  => array()
);
if(!empty($analisis_kegiatan)){
    foreach($analisis_kegiatan as $k => $ap){
        if(empty($data_all['data'])){
            $data_all['data'] = $analisis_kegiatan;
        }
        $data_all['total'] += $ap['total_pagu'];
    }
}

$body = '';
$urut = 1;
foreach ($data_all['data'] as $k => $all_ap) {
    $skpd   = '<a style="text-decoration: none;" onclick="show_analisis(\''.$all_ap['kode_giat'].'\'); return false;" href="#" title="Menampilkan Analisis SKPD per-Kegiatan">'.$all_ap['skpd'].'</a>';
    $body .='
    <tr>
        <td class="kiri kanan bawah text_tengah">'.$urut.'</td>
        <td class="kiri kanan bawah text_tengah">'.$all_ap['kode_giat'].'</td>
        <td class="kiri kanan bawah text_kiri">'.$all_ap['nama_giat'].'</td>
        <td class="kiri kanan bawah text_tengah">'.$skpd.'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($all_ap['total_pagu'],0,",",".").'</td>
    </tr>';
    $urut++;
}

$nama_laporan = 'ANALISIS BELANJA PAGU per-KEGIATAN'.$nama_skpd.'<br>TAHUN ANGGARAN '.$input['tahun_anggaran'].' '.strtoupper($nama_pemda);
echo '
<button type="button" style="background-color:#FFD670; text-align: center; margin: 10px auto 20px; display: block;" class="btn">Laporan Jadwal '.$jadwal_lokal->nama_jadwal.'</button>
<h4 style="text-align: center; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">'.$nama_laporan.'</h4>
<div id="wrap-table">
<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width: 100%; table-layout: fixed; overflow-wrap: break-word; font-size: 100%; border: 0;">
    <thead>
        <tr>    
            <th class="atas kiri kanan bawah text_tengah" style=" width:35px;">No</th>
            <th class="atas kiri kanan bawah text_tengah" style=" width:300px;">Kode</th>
            <th class="atas kiri kanan bawah text_tengah">Kegiatan</th>
            <th class="atas kiri kanan bawah text_tengah" style="width:100px;">SKPD</th>
            <th class="atas kiri kanan bawah text_tengah" style=" width:400px;">Pagu</th>
        </tr>
    </thead>
    <tbody>
        '.$body.'
        <tr>
            <td class="kiri kanan bawah text_kanan text_blok" colspan="4">Jumlah</td>
            <td class="kiri kanan bawah text_kanan text_blok">'.number_format($data_all['total'],0,",",".").'</td>
        </tr>
    </tbody>
</table>
</div>';
echo '</div>
<div class="modal fade mt-4" id="modalAnalisis" tabindex="-1" role="dialog" aria-labelledby="modalmodalAnalisisLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document" style="min-width:1400px">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalmodalAnalisisLabel">Laporan Skpd Kegiatan</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div> 
			<div class="modal-footer">
				<button type="submit" class="components-button btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>';
?>
<style type="text/css">
    #wrap-table {
        overflow: auto;
        height: 100vh;
    }
    @media  print {
        #wrap-table {
            overflow: none;
            height: auto;
        }
    }
</style>
<script type="text/javascript">
    jQuery(document).ready(function(){
        run_download_excel();
    });

    /** modal menampilkan analisis program */
	function show_analisis(kode_giat){
		jQuery('#modalAnalisis').modal('show');
		jQuery("#modalAnalisis .modal-title").html("Daftar SKPD Kegiatan "+kode_giat);
        jQuery("#wrap-loading").show();
		jQuery.ajax({
			url:ajax.url,
			type:'post',
			dataType:'json',
			data:{
				action:"show_skpd_kegiatan_analisis",
				kode_giat:kode_giat,
                id_jadwal_lokal:<?php echo $input['id_jadwal_lokal']; ?>,
                id_sub_skpd:'<?php echo $input['id_skpd']; ?>',
				tahun_anggaran:<?php echo $input['tahun_anggaran']; ?>,
				api_key:jQuery("#api_key").val(),
			},
			success:function(response){
				if(response.status=='error'){
					alert(response.message);
				}else{
					jQuery("#modalAnalisis .modal-body").html(response.html);
					jQuery("#modalAnalisis .modal-body").css('overflow-x', 'auto');
					jQuery("#modalAnalisis .modal-body").css('margin-right','15px');
					jQuery("#modalAnalisis .modal-body").css('padding', '15px');
                    jQuery("#modalAnalisis .modal-title").html(response.title);

					window.table_skpd_giat = jQuery("#table-skpd-giat").DataTable( {
				        dom: 'Blfrtip',
				        lengthMenu: [
				            [10, 25, 50, -1],
				            [10, 25, 50, 'All'],
				        ]
				    } );
                }
				jQuery("#wrap-loading").hide();
			}
		})
    }
</script>