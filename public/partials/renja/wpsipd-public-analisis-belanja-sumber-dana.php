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
    $where_jadwal=' AND dana.id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
    $where_jadwal.=' AND sub_keg.id_jadwal='.$wpdb->prepare("%d", $id_jadwal_lokal);
}

if($input['id_skpd'] == 'all'){
    $where_skpd = '';
    $nama_skpd = '';
}else{
    $where_skpd = ' AND sub_keg.id_sub_skpd = '.$input['id_skpd'].' ';
    $nama_skpd_tunggal = $wpdb->get_row($wpdb->prepare("
    SELECT 
        nama_skpd
    FROM `data_unit` 
    WHERE id_skpd=%d
        AND tahun_anggaran=%d", $input['id_skpd'], $input['tahun_anggaran']));
    $nama_skpd = '<br>'.$nama_skpd_tunggal->nama_skpd;
}

$nama_pemda = get_option('_crb_daerah');
$nama_excel = 'Analisis Belanja per-Sumber Dana '.$input['tahun_anggaran'].' '.strtoupper($nama_pemda);
echo '<div id="cetak" title="'.$nama_excel.'" style="padding: 5px;">';

$sql = "
    SELECT 
        dana.id, 
        dana.namadana, 
        dana.kodedana, 
        SUM(dana.pagudana) as total_pagu, 
        sub_keg.kode_sbl, 
        COUNT(DISTINCT sub_keg.id_sub_skpd) as skpd 
    FROM data_dana_sub_keg_lokal".$_suffix." AS dana 
    INNER JOIN data_sub_keg_bl_lokal".$_suffix." AS sub_keg 
    ON dana.kode_sbl = sub_keg.kode_sbl 
    WHERE dana.tahun_anggaran=%d 
        AND dana.active=1
        AND sub_keg.tahun_anggaran=%d 
        AND sub_keg.active=1
        ".$where_jadwal."
        ".$where_skpd."
        GROUP by dana.kodedana 
        ORDER BY dana.kodedana ASC";
$analisis_sumber_dana = $wpdb->get_results($wpdb->prepare($sql,$input['tahun_anggaran'],$input['tahun_anggaran']), ARRAY_A);

$data_all = array(
    'total' => 0,
    'data'  => array()
);
if(!empty($analisis_sumber_dana)){
    foreach($analisis_sumber_dana as $k => $ap){
        if(empty($data_all['data'])){
            $data_all['data'] = $analisis_sumber_dana;
        }
        $data_all['total'] += $ap['total_pagu'];
    }
}

$body = '';
$urut = 1;
foreach ($data_all['data'] as $k => $all_ap) {
    $skpd   = '<a style="text-decoration: none;" onclick="show_analisis(\''.$all_ap['kodedana'].'\'); return false;" href="#" title="Menampilkan Analisis Sumber Dana">'.$all_ap['skpd'].'</a>';
    $body .='
    <tr>
        <td class="kiri kanan bawah text_tengah">'.$urut.'</td>
        <td class="kiri kanan bawah">'.$all_ap['kodedana'].'</td>
        <td class="kiri kanan bawah text_kiri">'.$all_ap['namadana'].'</td>
        <td class="kiri kanan bawah text_tengah">'.$skpd.'</td>
        <td class="kiri kanan bawah text_kanan">'.number_format($all_ap['total_pagu'],0,",",".").'</td>
    </tr>';
    $urut++;
}

$nama_laporan = 'ANALISIS BELANJA PAGU per-SUMBER DANA'.$nama_skpd.'<br>TAHUN ANGGARAN '.$input['tahun_anggaran'].' '.strtoupper($nama_pemda);
echo '
<button type="button" style="background-color:#FFD670; text-align: center; margin: 10px auto 20px; display: block;" class="btn">Laporan Jadwal '.$jadwal_lokal->nama_jadwal.'</button>
<h4 style="text-align: center; margin: 10px auto; min-width: 450px; max-width: 570px; font-weight: bold;">'.$nama_laporan.'</h4>
<div id="wrap-table">
<table cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width: 100%; table-layout: fixed; overflow-wrap: break-word; border: 0;">
    <thead>
        <tr>    
            <th class="atas kiri kanan bawah text_tengah" style=" width:45px;">No</th>
            <th class="atas kiri kanan bawah text_tengah" style=" width:300px;">Kode</th>
            <th class="atas kiri kanan bawah text_tengah">Sumber Dana</th>
            <th class="atas kiri kanan bawah text_tengah" style="width:100px;">SKPD</th>
            <th class="atas kiri kanan bawah text_tengah" style=" width:200px;">Pagu</th>
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
				<h5 class="modal-title" id="modalmodalAnalisisLabel">Laporan Skpd Sumber Dana</h5>
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

    /** modal menampilkan analisis sumber dana */
	function show_analisis(kode_dana){
		jQuery('#modalAnalisis').modal('show');
		jQuery("#modalAnalisis .modal-title").html("Daftar SKPD Sumber Dana "+kode_dana);
        jQuery("#wrap-loading").show();
		jQuery.ajax({
			url:ajax.url,
			type:'post',
			dataType:'json',
			data:{
				action:"show_skpd_sumber_dana_analisis",
				kode_dana:kode_dana,
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
					// jQuery('#modalAnalisis .export-excel').attr("disabled", false);
					// jQuery('#modalAnalisis .export-excel').attr("title", title);

					window.table_skpd_sumber_dana = jQuery("#table-skpd-sumber-dana").DataTable( {
				        dom: 'Blfrtip',
				        lengthMenu: [
				            [10, 25, 50, -1],
				            [10, 25, 50, 'All'],
				        ]
				    } );
				    // jQuery('#modal-report .action-footer .dt-buttons').remove();
					// jQuery('#modal-report .action-footer').html(
				    // 	"<button type=\"button\" class=\"btn btn-success btn-preview\" onclick=\"preview('"+id_jadwal_lokal+"')\">Preview</button>");
					// jQuery('#modal-report .action-footer').append(table_renja.buttons().container());
				    // jQuery('#modal-report .action-footer .dt-buttons').css('margin-left', '5px');
				    // jQuery('#modal-report .action-footer .buttons-excel').addClass('btn btn-primary');
				    // jQuery('#modal-report .action-footer .buttons-excel span').html('Export Excel');
				}
				jQuery("#wrap-loading").hide();
			}
		})
    }
</script>