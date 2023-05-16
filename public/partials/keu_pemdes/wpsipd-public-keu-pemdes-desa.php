<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$input = shortcode_atts( array(
    'tahun_anggaran' => '2022',
    'id_skpd' => '',
    'id_kec' => '',
    'id_kel' => ''
), $atts );

if(empty($input['id_kel']) && empty($input['id_skpd'])){
    die('<h1 class="text-center">id_skpd, id_kec dan id_kel tidak boleh kosong!</h1>');
}else if(!empty($input['id_skpd'])){
    $id_kab = get_option('_crb_id_lokasi_kokab');
    $unit = $wpdb->get_row($wpdb->prepare("
        SELECT 
            nama_skpd, 
            id_skpd, 
            kode_skpd, 
            nipkepala 
        from data_unit 
        where active=1 
            and tahun_anggaran=%d 
            and is_skpd=1 
            and id_skpd = %d 
        order by kode_skpd ASC
    ", $input['tahun_anggaran'], $input['id_skpd']), ARRAY_A);
    if(empty($unit)){
        die('<h1 class="text-center">Data id_skpd = '.$input['id_skpd'].' tidak ditemukan!</h1>');
    }
    $nama_kec = str_replace('KECAMATAN ', '', $unit['nama_skpd']);
    $id_kec = $wpdb->get_var("
        SELECT 
            id_alamat 
        from data_alamat 
        where tahun=".$input['tahun_anggaran']." 
            and is_kec=1 
            and id_kab=".$id_kab." 
            and nama='".$nama_kec."'
    ");
    if(empty($id_kec)){
        die('<h1 class="text-center">Data kecamatan dari id_skpd = '.$input['id_skpd'].' tidak ditemukan!</h1>');
    }
    $desa = $wpdb->get_results("
        SELECT 
            id_alamat,
            nama 
        from data_alamat 
        where tahun=".$input['tahun_anggaran']." 
            and is_kel=1  
            and id_kec=".$id_kec."
    ", ARRAY_A);
    if (empty($desa)) {
        die('<h1 class="text-center">Desa dengan id_kec ='.$input['id_kec'].' tidak ditemukan!</h1>');
    }else{
        echo '<ul>';
        foreach($desa as $val){
            $url_skpd = $this->generatePage($val['nama'].' | '.$input['tahun_anggaran'], $input['tahun_anggaran'], '[monitor_keu_pemdes tahun_anggaran="'.$input['tahun_anggaran'].'" id_kec="'.$id_kec.'" id_kel="'.$val['id_alamat'].'"]');
            echo '<li><a target="_blank" href="'.$url_skpd.'">'.$val['nama'].' | '.$input['tahun_anggaran'].'</a>';
        }  
        echo '</ul>';
    }
}else{
    $body = $wpdb->get_row($wpdb->prepare('
        SELECT 
            desa,
            kecamatan, 
            sum(total) as total 
        from data_bkk_desa 
        WHERE tahun_anggaran=%d
            and active=1
            and id_desa=%d
        group by desa
    ', $input['tahun_anggaran'], $input['id_kel'],$input['id_kec']), ARRAY_A);
    if (empty($body)) {
        die('<h1 class="text-center">Desa dengan id ='.$input['id_kel'].' tidak ditemukan!</h1>');
    }


    $total_all = 0;
    $belum_all = 0;
    $realisasi_all = 0;
    $persen_all = 0;


    // print_r($body);die($wpdb->last_query);
    ?>


    <h1 class="text-center">Laporan<br>Desa <!-- <?php echo $body['desa'] ?> --> Kecamatan <!-- <?php echo $body['kecamatan'] ?> --><br>Tahun <?php echo $input['tahun_anggaran']; ?></h1>
    <div class="cetak">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="atas kanan bawah kiri text_tengah text_blok" style="width: 30px;">No</th>
                    <th class="atas kanan bawah text_tengah text_blok" >Uraian</th>
                    <th class="atas kanan bawah text_tengah text_blok">Anggaran</th>
                    <th class="atas kanan bawah text_tengah text_blok">Realisasi</th>
                    <th class="atas kanan bawah text_tengah text_blok">Belum Realisasi</th>
                    <th class="atas kanan bawah text_tengah text_blok">% Realisasi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>BHPD</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                    <td class="text-center">0%</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>BHRD</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                    <td class="text-center">0%</td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td>DD</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                    <td class="text-center">0%</td>
                </tr>
                <tr>
                    <td class="text-center">4</td>
                    <td>ADD</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                    <td class="text-center">0%</td>
                </tr>
                <tr>
                    <td class="text-center">5</td>
                    <td>BKK Infrastruktur</td>
                    <td class="text-right"><?php echo number_format($body['total'],0,",","."); ?></td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                    <td class="text-center">0%</td>
                </tr>
                <tr>
                    <td class="text-center">6</td>
                    <td>BKK PILKADES</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                    <td class="text-center">0%</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">JUMLAH</th>
                    <th class="text-right"><?php echo number_format($body['total'],0,",","."); ?></th>
                    <th class="text-right"><?php echo number_format($realisasi_all,0,",","."); ?></th>
                    <th class="text-right"><?php echo number_format($belum_all,0,",","."); ?></th>
                    <th class="text-center"><?php echo $persen_all; ?>%</th>
                </tr>
            </tfoot>
        </table>
    </div>
<?php }; ?>