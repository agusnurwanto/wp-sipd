<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
$input = shortcode_atts( array(
	'id_skpd' => '',
	'tahun_anggaran' => '2022'
), $atts );

if(empty($input['id_skpd'])){
    if(!empty($_GET['id_skpd'])){
        $id_skpd = array();
        foreach(explode(',', $_GET['id_skpd']) as $val){
            $id_skpd[] = $wpdb->prepare('%d', $val);
        };
        $input['id_skpd'] = implode(',', $id_skpd);
    }
}

if(empty($input['id_skpd'])){
    $sql_unit = $wpdb->prepare("
        SELECT 
            id_skpd
        FROM data_unit 
        WHERE 
            tahun_anggaran=%d
            AND active=1
        order by kode_skpd ASC
        ", $input['tahun_anggaran']);
    $unit_db = $wpdb->get_results($sql_unit, ARRAY_A);
    $id_skpd = array();
    foreach($unit_db as $u){
        $id_skpd[] = $u['id_skpd'];
    }
    $input['id_skpd'] = implode(',', $id_skpd);
}

$api_key = get_option('_crb_api_key_extension');
$id_sumber_dana_default = get_option('_crb_default_sumber_dana' );
$sumber_dana_default = $wpdb->get_row($wpdb->prepare('
    SELECT 
        id_dana,
        kode_dana,
        nama_dana
    FROM data_sumber_dana
    WHERE tahun_anggaran=%d
        AND id_dana=%d
', $input['tahun_anggaran'], $id_sumber_dana_default), ARRAY_A);

$body_json = '';
$nama_pemda = get_option('_crb_daerah');
echo "<h1 class='text-center'>Data JSON RKA<br>$nama_pemda";

if(count(explode(',', $input['id_skpd'])) == 1){
    $sql_unit = $wpdb->prepare("
    	SELECT 
    		*
    	FROM data_unit 
    	WHERE 
            tahun_anggaran=%d
    		AND id_skpd IN ($input[id_skpd])
    		AND active=1
    	order by id_skpd ASC
        ", $input['tahun_anggaran']);
    $unit = $wpdb->get_row($sql_unit, ARRAY_A);
    if(!empty($unit['nama_skpd'])){
        echo '<br>'.$unit['kode_skpd'].' '.$unit['nama_skpd'];
    }
}
echo "</h1>";

$sql_anggaran = $wpdb->prepare("
    SELECT 
        k.kode_urusan,
        k.nama_urusan,
        k.kode_bidang_urusan,
        k.nama_bidang_urusan,
        k.kode_program,
        k.nama_program,
        k.kode_giat,
        k.nama_giat,
        k.kode_skpd,
        k.nama_skpd,
        k.kode_sub_skpd,
        k.nama_sub_skpd,
        k.kode_sub_giat,
        k.nama_sub_giat,
        r.subs_bl_teks,
        sum(r.rincian) as rincian,
        coalesce(ms.id_dana, $sumber_dana_default[id_dana]) as id_dana,
        coalesce(ms.nama_dana, '$sumber_dana_default[nama_dana]') as nama_dana
    FROM data_sub_keg_bl as k 
    INNER JOIN data_rka as r on k.kode_sbl=r.kode_sbl 
        and r.active=k.active 
        and r.tahun_anggaran=k.tahun_anggaran 
    LEFT JOIN data_mapping_sumberdana as s on r.id_rinci_sub_bl=s.id_rinci_sub_bl 
        and s.active=k.active 
        and s.tahun_anggaran=k.tahun_anggaran 
    LEFT JOIN data_sumber_dana as ms on ms.id_dana=s.id_sumber_dana 
        and ms.tahun_anggaran=k.tahun_anggaran 
    WHERE
        k.tahun_anggaran=%d
        AND k.active=1
        AND k.id_sub_skpd IN ($input[id_skpd])
    GROUP BY k.kode_sub_skpd ASC, k.kode_sub_giat, r.subs_bl_teks
    ORDER BY k.kode_sub_skpd ASC, k.kode_sub_giat ASC
    ",$input["tahun_anggaran"]);

echo '
    <h2>SQL untuk select total rincian per kelompok belanja (#)</h2>
    <pre>'.$sql_anggaran.'</pre>';

$sql_anggaran = $wpdb->prepare("
    SELECT 
        k.kode_urusan,
        k.nama_urusan,
        k.kode_bidang_urusan,
        k.nama_bidang_urusan,
        k.kode_program,
        k.nama_program,
        k.kode_giat,
        k.nama_giat,
        k.kode_skpd,
        k.nama_skpd,
        k.kode_sub_skpd,
        k.nama_sub_skpd,
        k.kode_sub_giat,
        k.nama_sub_giat,
        r.kode_akun,
        r.nama_akun,
        sum(r.rincian) as rincian,
        coalesce(ms.id_dana, $sumber_dana_default[id_dana]) as id_dana,
        coalesce(ms.nama_dana, '$sumber_dana_default[nama_dana]') as nama_dana
    FROM data_sub_keg_bl as k 
    INNER JOIN data_rka as r on k.kode_sbl=r.kode_sbl 
        and r.active=k.active 
        and r.tahun_anggaran=k.tahun_anggaran 
    LEFT JOIN data_mapping_sumberdana as s on r.id_rinci_sub_bl=s.id_rinci_sub_bl 
        and s.active=k.active 
        and s.tahun_anggaran=k.tahun_anggaran 
    LEFT JOIN data_sumber_dana as ms on ms.id_dana=s.id_sumber_dana 
        and ms.tahun_anggaran=k.tahun_anggaran 
    WHERE
        k.tahun_anggaran=%d
        AND k.active=1
        AND k.id_sub_skpd IN ($input[id_skpd])
    GROUP BY k.kode_sub_skpd ASC, k.kode_sub_giat, r.kode_akun
    ORDER BY k.kode_sub_skpd ASC, k.kode_sub_giat ASC
    ",$input["tahun_anggaran"]);

echo '
<h2>SQL untuk select total rincian per kode akun dan sumber dana untuk keperluan SPD FMIS</h2>
<button onclick="get_data('.$input['tahun_anggaran'].', \''.$input['id_skpd'].'\', \'json_rek_sd\');" class="btn btn-success" style="margin: 0 10px 10px;">Get Data</button>
<button class="btn btn-primary" style="margin: 0 0 10px; display: none;" id="json_rek_sd_excel" onclick="tableHtmlToExcel(\'json_rek_sd\', \'Data P3DN\');">Download Excel</button>
<div id="json_rek_sd" style="overflow: auto; max-height: 100vh;"></div>
<pre>'.$sql_anggaran.'</pre>';

$sql_anggaran = $wpdb->prepare("
    SELECT
        k.id,
        k.id_sub_skpd,
        k.id_lokasi,
        k.id_label_kokab,
        k.nama_dana,
        k.no_sub_giat,
        k.kode_giat,
        k.id_program,
        k.nama_lokasi,
        k.waktu_akhir,
        k.pagu_n_lalu,
        k.id_urusan,
        k.id_unik_sub_bl,
        k.id_sub_giat,
        k.label_prov,
        k.kode_program,
        k.kode_sub_giat,
        k.no_program,
        k.kode_urusan,
        k.kode_bidang_urusan,
        k.nama_program,
        k.target_4,
        k.target_5,
        k.id_bidang_urusan,
        k.nama_bidang_urusan,
        k.target_3,
        k.no_giat,
        k.id_label_prov,
        k.waktu_awal,
        k.pagumurni,
        k.pagu,
        k.pagu_simda,
        k.output_sub_giat,
        k.sasaran,
        k.indikator,
        k.id_dana,
        k.nama_sub_giat,
        k.pagu_n_depan,
        k.satuan,
        k.id_rpjmd,
        k.id_giat,
        k.id_label_pusat,
        k.nama_giat,
        k.kode_skpd,
        k.nama_skpd,
        k.kode_sub_skpd,
        k.id_skpd,
        k.id_sub_bl,
        k.nama_sub_skpd,
        k.target_1,
        k.nama_urusan,
        k.target_2,
        k.label_kokab,
        k.label_pusat,
        k.pagu_keg,
        k.pagu_fmis,
        k.id_bl,
        k.kode_bl,
        k.kode_sbl,
        k.active,
        k.update_at,
        k.tahun_anggaran,
        r.id as id_rka,
        r.created_user,
        r.createddate,
        r.createdtime,
        r.harga_satuan,
        r.harga_satuan_murni,
        r.id_daerah,
        r.id_rinci_sub_bl,
        r.id_standar_nfs,
        r.is_locked,
        r.jenis_bl,
        r.ket_bl_teks,
        r.substeks,
        r.id_dana,
        r.nama_dana,
        r.is_paket,
        r.kode_dana,
        r.subtitle_teks,
        r.kode_akun,
        r.koefisien,
        r.koefisien_murni,
        r.lokus_akun_teks,
        r.nama_akun,
        r.nama_komponen,
        r.spek_komponen,
        r.satuan,
        r.spek,
        r.sat1,
        r.sat2,
        r.sat3,
        r.sat4,
        r.volum1,
        r.volum2,
        r.volum3,
        r.volum4,
        r.volume,
        r.volume_murni,
        r.subs_bl_teks,
        r.total_harga,
        r.rincian,
        r.rincian_murni,
        r.totalpajak,
        r.pajak,
        r.pajak_murni,
        r.updated_user,
        r.updateddate,
        r.updatedtime,
        r.user1,
        r.user2,
        r.active,
        r.update_at,
        r.tahun_anggaran,
        r.idbl,
        r.idsubbl,
        r.kode_bl,
        r.kode_sbl,
        r.id_prop_penerima,
        r.id_camat_penerima,
        r.id_kokab_penerima,
        r.id_lurah_penerima,
        r.id_penerima,
        r.idkomponen,
        r.idketerangan,
        r.idsubtitle,
        coalesce(ms.id_dana, $sumber_dana_default[id_dana]) as id_dana,
        coalesce(ms.nama_dana, '$sumber_dana_default[nama_dana]') as nama_dana
    FROM data_sub_keg_bl as k
    INNER JOIN data_rka as r on k.kode_sbl=r.kode_sbl
        and r.active=k.active
        and r.tahun_anggaran=k.tahun_anggaran
    LEFT JOIN data_mapping_sumberdana as s on r.id_rinci_sub_bl=s.id_rinci_sub_bl
        and s.active=k.active
        and s.tahun_anggaran=k.tahun_anggaran
    LEFT JOIN data_sumber_dana as ms on ms.id_dana=s.id_sumber_dana
        and ms.tahun_anggaran=k.tahun_anggaran
    WHERE
        k.tahun_anggaran=%d
        AND k.active=1
        AND k.id_sub_skpd IN ($input[id_skpd])
    ",$input["tahun_anggaran"]);

echo '
    <h2>SQL untuk select semua rincian</h2>
    <pre>'.$sql_anggaran.'</pre>';

$sql_anggaran = $wpdb->prepare("
    SELECT 
        k.nama_skpd AS OPD,
        k.nama_program AS PROGRAM,
        k.nama_giat AS KEGIATAN,
        k.nama_sub_giat AS \"SUB KEGIATAN\",
        r.nama_akun AS BELANJA,
        r.subs_bl_teks AS \"[#]\",
        r.ket_bl_teks AS \"[-]\",
        concat(r.nama_komponen, ' ', r.spek_komponen) AS \"SPESIFIKASI BELANJA\",
        ms.nama_dana AS \"SUMBER DANA\",
        r.rincian_murni AS \"ANGGARAN SEBELUM PERUBAHAN\",
        r.rincian AS \"ANGGARAN SETELAH PERUBAHAN\",
        '' AS \"REALISASI (Rp)\",
        '' AS \"Uraian SPM\",
        '' AS \"SISA\",
        '' AS \"KETERANGAN\"
    FROM data_sub_keg_bl AS k 
    INNER JOIN data_rka AS r on k.kode_sbl=r.kode_sbl 
        AND r.active=k.active 
        AND r.tahun_anggaran=k.tahun_anggaran 
    LEFT JOIN data_mapping_sumberdana AS s on r.id_rinci_sub_bl=s.id_rinci_sub_bl 
        AND s.active=k.active 
        AND s.tahun_anggaran=k.tahun_anggaran 
    LEFT JOIN data_sumber_dana AS ms on ms.id_dana=s.id_sumber_dana 
        AND ms.tahun_anggaran=k.tahun_anggaran 
    LEFT JOIN data_realisasi_akun AS a on k.kode_sbl=a.kode_sbl 
        AND a.tahun_anggaran=k.tahun_anggaran 
    LEFT JOIN data_sub_keg_bl AS u on k.kode_sbl=u.kode_sbl 
        AND u.tahun_anggaran=k.tahun_anggaran
    WHERE
        k.tahun_anggaran=%d
        AND k.active=1
        AND k.id_sub_skpd IN ($input[id_skpd])
    GROUP BY k.kode_sub_skpd ASC, k.kode_sub_giat, r.subs_bl_teks
    ORDER BY k.kode_sub_skpd ASC, k.kode_sub_giat ASC
    ",$input["tahun_anggaran"]);

echo '
<h2>SQL untuk select kontrol realisasi dan P3DN</h2>
<button onclick="get_data('.$input['tahun_anggaran'].', \''.$input['id_skpd'].'\', \'json_rek_p3dn\');" class="btn btn-success" style="margin: 0 10px 10px;">Get Data</button>
<button class="btn btn-primary" style="margin: 0 0 10px; display: none;" id="json_rek_p3dn_excel" onclick="tableHtmlToExcel(\'json_rek_p3dn\', \'Data P3DN\');">Download Excel</button>
<div id="json_rek_p3dn" style="overflow: auto; max-height: 100vh;"></div>
<pre>'.$sql_anggaran.'</pre>';

$sql_anggaran_kas = $wpdb->prepare("
    SELECT 
        (
            SELECT 
                nama_bidang_urusan
            from data_prog_keg
            where tahun_anggaran=k.tahun_anggaran
                and active=1
                AND id_bidang_urusan=k.id_bidang_urusan
            LIMIT 1
        ) as bidang_urusan,
        k.id_sub_skpd,
        k.id_bidang_urusan,
        u.nama_skpd,
        u.kode_skpd,
        sum(k.bulan_1) as bulan_1,
        sum(k.bulan_2) as bulan_2,
        sum(k.bulan_3) as bulan_3,
        sum(k.bulan_4) as bulan_4,
        sum(k.bulan_5) as bulan_5,
        sum(k.bulan_6) as bulan_6,
        sum(k.bulan_7) as bulan_7,
        sum(k.bulan_8) as bulan_8,
        sum(k.bulan_9) as bulan_9,
        sum(k.bulan_10) as bulan_10,
        sum(k.bulan_11) as bulan_11,
        sum(k.bulan_12) as bulan_12,
        (sum(k.bulan_1) + sum(k.bulan_2) + sum(k.bulan_3)) as tw_1,
        (sum(k.bulan_4) + sum(k.bulan_5) + sum(k.bulan_6)) as tw_2,
        (sum(k.bulan_7) + sum(k.bulan_8) + sum(k.bulan_9)) as tw_3,
        (sum(k.bulan_10) + sum(k.bulan_11) + sum(k.bulan_12)) as tw_4,
        (sum(k.bulan_1) + sum(k.bulan_2) + sum(k.bulan_3) + sum(k.bulan_4) + sum(k.bulan_5) + sum(k.bulan_6) + sum(k.bulan_7) + sum(k.bulan_8)  + sum(k.bulan_9)  + sum(k.bulan_10) + sum(k.bulan_11) + sum(k.bulan_12)) as total
    from data_anggaran_kas k
    inner join data_unit u on k.id_sub_skpd=u.id_skpd
        and u.tahun_anggaran=k.tahun_anggaran
        and u.active=1
    where k.tahun_anggaran=%d
        and k.active=1
        and k.type='belanja'
        and k.id_sub_skpd IN ($input[id_skpd])
    group by k.id_bidang_urusan, k.id_sub_skpd
    order by k.id_bidang_urusan, k.id_sub_skpd ASC
    ",$input["tahun_anggaran"]);

echo '
<h2>SQL untuk select anggaran kas (RAK)</h2>
<button onclick="get_data('.$input['tahun_anggaran'].', \''.$input['id_skpd'].'\', \'json_rek_rak\');" class="btn btn-success" style="margin: 0 10px 10px;">Get Data</button>
<button class="btn btn-primary" style="margin: 0 0 10px; display: none;" id="json_rek_rak_excel" onclick="tableHtmlToExcel(\'json_rek_rak\', \'Data P3DN\');">Download Excel</button>
<div id="json_rek_rak" style="overflow: auto; max-height: 100vh;"></div>
<pre>'.$sql_anggaran_kas.'</pre>';
?>
<script type="text/javascript">
    function get_data(tahun_anggaran, id_unit, tipe){
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: ajax.url,
            type: "post",
            data: {
                "action": "get_data_json",
                "api_key": "<?php echo $api_key; ?>",
                "tahun_anggaran": tahun_anggaran,
                "id_skpd": id_unit,
                "tipe": tipe
            },
            dataType: "json",
            success: function(ret){
                var html_data = '';
                var html = '';
                if(tipe == 'json_rek_sd'){
                    ret.data.map(function(b, i){
                        html_data += ''
                            +'<tr>'
                                +'<td>'+b.kode_urusan+'</td>'
                                +'<td>'+b.nama_urusan+'</td>'
                                +'<td>'+b.kode_bidang_urusan+'</td>'
                                +'<td>'+b.nama_bidang_urusan+'</td>'
                                +'<td>'+b.kode_program+'</td>'
                                +'<td>'+b.nama_program+'</td>'
                                +'<td>'+b.kode_giat+'</td>'
                                +'<td>'+b.nama_giat+'</td>'
                                +'<td>'+b.kode_skpd+'</td>'
                                +'<td>'+b.nama_skpd+'</td>'
                                +'<td>'+b.kode_sub_skpd+'</td>'
                                +'<td>'+b.nama_sub_skpd+'</td>'
                                +'<td>'+b.kode_sub_giat+'</td>'
                                +'<td>'+b.nama_sub_giat+'</td>'
                                +'<td>'+b.kode_akun+'</td>'
                                +'<td>'+b.nama_akun+'</td>'
                                +'<td>'+b.rincian+'</td>'
                                +'<td>'+b.id_dana+'</td>'
                                +'<td>'+b.nama_dana+'</td>'
                            +'</tr>';
                    });
                    var html = ''
                        +'<table class="table table-bordered">'
                            +'<thead>'
                                +'<tr>'
                                    +'<th>kode_urusan</th>'
                                    +'<th>nama_urusan</th>'
                                    +'<th>kode_bidang_urusan</th>'
                                    +'<th>nama_bidang_urusan</th>'
                                    +'<th>kode_program</th>'
                                    +'<th>nama_program</th>'
                                    +'<th>kode_giat</th>'
                                    +'<th>nama_giat</th>'
                                    +'<th>kode_skpd</th>'
                                    +'<th>nama_skpd</th>'
                                    +'<th>kode_sub_skpd</th>'
                                    +'<th>nama_sub_skpd</th>'
                                    +'<th>kode_sub_giat</th>'
                                    +'<th>nama_sub_giat</th>'
                                    +'<th>kode_akun</th>'
                                    +'<th>nama_akun</th>'
                                    +'<th>rincian</th>'
                                    +'<th>id_dana</th>'
                                    +'<th>nama_dana</th>'
                                +'</tr>'
                            +'</thead>'
                            +'<tbody>'+html_data+'</tbody>'
                        +'</table>';
                    jQuery('#json_rek_sd').html(html);
                    jQuery('#json_rek_sd_excel').show();
                }else if(tipe == 'json_rek_p3dn'){
                    ret.data.map(function(b, i){
                        html_data += ''
                            +'<tr>'
                                +'<td>'+b.nama_skpd+'</td>'
                                +'<td>'+b.nama_program+'</td>'
                                +'<td>'+b.nama_giat+'</td>'
                                +'<td>'+b.nama_sub_giat+'</td>'
                                +'<td>'+b.nama_akun+'</td>'
                                +'<td>'+b.subs_bl_teks+'</td>'
                                +'<td>'+b.ket_bl_teks+'</td>'
                                +'<td>'+b.komponen+'</td>'
                                +'<td>'+b.nama_dana+'</td>'
                                +'<td class="text-right">'+b.rincian_murni+'</td>'
                                +'<td class="text-right">'+b.rincian+'</td>'
                                +'<td class="text-right">'+b.realisasi+'</td>'
                                +'<td>'+b.uraian_spm+'</td>'
                                +'<td class="text-right">'+b.sisa+'</td>'
                                +'<td>'+b.keterangan+'</td>'
                            +'</tr>';
                    });
                    var html = ''
                        +'<table class="table table-bordered">'
                            +'<thead>'
                                +'<tr>'
                                    +'<th>OPD</th>'
                                    +'<th>PROGRAM</th>'
                                    +'<th>KEGIATAN</th>'
                                    +'<th>SUB KEGIATAN</th>'
                                    +'<th>BELANJA</th>'
                                    +'<th>[#]</th>'
                                    +'<th>[-]</th>'
                                    +'<th>SPESIFIKASI BELANJA</th>'
                                    +'<th>SUMBER DANA</th>'
                                    +'<th>ANGGARAN SEBELUM PERUBAHAN</th>'
                                    +'<th>ANGGARAN SETELAH PERUBAHAN</th>'
                                    +'<th>REALISASI (Rp)</th>'
                                    +'<th>Uraian SPM</th>'
                                    +'<th>SISA</th>'
                                    +'<th>KETERANGAN</th>'
                                +'</tr>'
                            +'</thead>'
                            +'<tbody>'+html_data+'</tbody>'
                        +'</table>';
                    jQuery('#json_rek_p3dn').html(html);
                    jQuery('#json_rek_p3dn_excel').show();
                }else if(tipe == 'json_rek_rak'){
                    ret.data.map(function(b, i){
                        html_data += ''
                            +'<tr>'
                                +'<td>'+b.bidang_urusan+'</td>'
                                +'<td>'+b.kode_skpd+' '+b.nama_skpd+'</td>'
                                +'<td class="text-right">'+b.bulan_1+'</td>'
                                +'<td class="text-right">'+b.bulan_2+'</td>'
                                +'<td class="text-right">'+b.bulan_3+'</td>'
                                +'<td class="text-right">'+b.bulan_4+'</td>'
                                +'<td class="text-right">'+b.bulan_5+'</td>'
                                +'<td class="text-right">'+b.bulan_6+'</td>'
                                +'<td class="text-right">'+b.bulan_7+'</td>'
                                +'<td class="text-right">'+b.bulan_8+'</td>'
                                +'<td class="text-right">'+b.bulan_9+'</td>'
                                +'<td class="text-right">'+b.bulan_10+'</td>'
                                +'<td class="text-right">'+b.bulan_11+'</td>'
                                +'<td class="text-right">'+b.bulan_12+'</td>'
                                +'<td class="text-right">'+b.tw_1+'</td>'
                                +'<td class="text-right">'+b.tw_2+'</td>'
                                +'<td class="text-right">'+b.tw_3+'</td>'
                                +'<td class="text-right">'+b.tw_4+'</td>'
                                +'<td class="text-right">'+b.total+'</td>'
                            +'</tr>';
                    });
                    var html = ''
                        +'<table class="table table-bordered">'
                            +'<thead>'
                                +'<tr>'
                                    +'<th>Bidang Urusan</th>'
                                    +'<th>OPD</th>'
                                    +'<th>Bulan 1</th>'
                                    +'<th>Bulan 2</th>'
                                    +'<th>Bulan 3</th>'
                                    +'<th>Bulan 4</th>'
                                    +'<th>Bulan 5</th>'
                                    +'<th>Bulan 6</th>'
                                    +'<th>Bulan 7</th>'
                                    +'<th>Bulan 8</th>'
                                    +'<th>Bulan 9</th>'
                                    +'<th>Bulan 10</th>'
                                    +'<th>Bulan 11</th>'
                                    +'<th>Bulan 12</th>'
                                    +'<th>Triwulan 1</th>'
                                    +'<th>Triwulan 2</th>'
                                    +'<th>Triwulan 3</th>'
                                    +'<th>Triwulan 4</th>'
                                    +'<th>Total</th>'
                                +'</tr>'
                            +'</thead>'
                            +'<tbody>'+html_data+'</tbody>'
                        +'</table>';
                    jQuery('#json_rek_rak').html(html);
                    jQuery('#json_rek_rak_excel').show();
                }
                alert(ret.message);
                jQuery('#wrap-loading').hide();
            }
        });
    }
</script>