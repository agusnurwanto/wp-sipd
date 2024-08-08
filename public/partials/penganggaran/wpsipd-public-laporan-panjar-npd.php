<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
global $wpdb;

$id_npd = '';
if (!empty($_GET) && !empty($_GET['id_npd'])) {
	$id_npd = $_GET['id_npd'];
} else {
	die('<h1 class="text-center">Tahun Anggaran dan Nomor NPD tidak boleh kosong!</h1>');
}

$input = shortcode_atts( array(
    'id_npd' => $id_npd,
	'tahun_anggaran' => '2022'
), $atts );

$api_key = get_option('_crb_api_key_extension');

$data_npd = $wpdb->get_row($wpdb->prepare('
    SELECT
        *
    FROM data_nota_pencairan_dana
    WHERE id=%d
    AND active=1
    AND tahun_anggaran=%d
', $input['id_npd'], $input['tahun_anggaran']), ARRAY_A);

if(!empty($data_npd)){
    $data_rfk = $wpdb->get_row($wpdb->prepare('
        SELECT 
            s.*,
            u.nama_skpd as nama_sub_skpd_asli,
            u.kode_skpd as kode_sub_skpd_asli,
            uu.nama_skpd as nama_skpd_asli,
            uu.kode_skpd as kode_skpd_asli
        FROM data_sub_keg_bl s
        INNER JOIN data_unit u on s.id_sub_skpd=u.id_skpd
            AND u.active=s.active
            AND u.tahun_anggaran=s.tahun_anggaran
        INNER JOIN data_unit uu on uu.id_skpd=u.id_unit
            AND uu.active=s.active
            AND uu.tahun_anggaran=s.tahun_anggaran
        WHERE s.kode_sbl = %s 
            AND s.active = 1
            AND s.tahun_anggaran = %d', $data_npd['kode_sbl'], $input['tahun_anggaran']), ARRAY_A);
}else{
    die('<h1 class="text-center">Data NPD tidak ditemukan!</h1>');
}

if ($data_rfk) {
    $kode_sub_skpd = $data_rfk['kode_sub_skpd_asli'];
    $nama_sub_skpd = $data_rfk['nama_sub_skpd_asli'];
    $kode_skpd = $data_rfk['kode_skpd_asli'];
    $nama_skpd = $data_rfk['nama_skpd_asli'];
    $kode_urusan = $data_rfk['kode_urusan'];
    $nama_urusan = $data_rfk['nama_urusan'];
    $kode_program = $data_rfk['kode_program'];
    $nama_program = $data_rfk['nama_program'];
    $kode_kegiatan = $data_rfk['kode_giat'];
    $nama_kegiatan = $data_rfk['nama_giat'];
    $kode_bidang_urusan = $data_rfk['kode_bidang_urusan'];
    $nama_bidang_urusan = $data_rfk['nama_bidang_urusan'];
    $nama_sub_kegiatan = $data_rfk['nama_sub_giat'];
    $kode_sub_kegiatan = $data_rfk['kode_sub_giat'];
    $nama_sub_kegiatan = str_replace('X.XX', $kode_bidang_urusan, $nama_sub_kegiatan);
    $pagu_kegiatan = number_format($data_rfk['pagu'], 0, ",", ".");
    $id_sub_skpd = $data_rfk['id_sub_skpd'];
} else {
    die('<h1 class="text-center">Sub Kegiatan tidak ditemukan!</h1>');
}

    $data_rek_npd = $wpdb->get_results($wpdb->prepare("
        SELECT 
            rnpd.*
        FROM data_rekening_nota_pencairan_dana as rnpd
        WHERE rnpd.id_npd=%d
            AND rnpd.kode_sbl = %s
            AND rnpd.tahun_anggaran = %d
            AND rnpd.active = 1
        ORDER BY rnpd.kode_rekening ASC", $data_npd['id'], $data_npd['kode_sbl'], $data_npd['tahun_anggaran']), ARRAY_A);

    $ret['html'] = '';
    $no = 1;
    $total_pagu = 0;
    $total_anggaran = 0;
    $total_sisa = 0;
    foreach ($data_rek_npd as $v_rek) {
        $total_harga_akun = 0;
        $sisa = 0;
        $total_harga_akun_rka = $wpdb->get_var($wpdb->prepare("
                                    SELECT 
                                        SUM(total_harga)
                                    from data_rka 
                                    where kode_akun=%s
                                        AND nama_akun=%s
                                        AND kode_sbl=%s
                                        AND tahun_anggaran=%d
                                        AND active=1
                                ", $v_rek['kode_rekening'], $v_rek['nama_rekening'], $data_npd['kode_sbl'], $data_npd['tahun_anggaran']));

        if(!empty($total_harga_akun_rka)){
            $total_harga_akun = $total_harga_akun_rka;
        }
        $sisa = $total_harga_akun - $v_rek['pagu_dana'];
        $ret['html'] .= '
            <tr>
                <td class="kanan bawah kiri text-center">'. $no .'</td>
                <td class="kanan bawah">' . $v_rek['kode_rekening'] . '</td>
                <td class="kanan bawah text-left">'. $v_rek['nama_rekening'] .'</td>
                <td class="kanan bawah text-right">'. number_format($total_harga_akun,0,",",".") .'</td>
                <td class="kanan bawah text-right">'. number_format($sisa,0 ,"," , ".") .'</td>
                <td class="kanan bawah text-right">'. number_format($v_rek['pagu_dana'],0,",",".") .'</td>
            </tr>';
        $no++;
        $total_anggaran += $total_harga_akun;
        $total_sisa     += $sisa;
        $total_pagu     += $v_rek['pagu_dana'];
    }

    $user_pptk = get_userdata($data_npd['id_user_pptk']);
    $jenis_panjar = $data_npd['jenis_panjar'] == 'set_panjar' ? 'Panjar' : 'Tanpa Panjar';
    if($data_npd['jenis_panjar'] == 'set_panjar'){
        $dengan_panjar = 'checked';
        $tanpa_panjar = '';
    }else{
        $dengan_panjar = '';
        $tanpa_panjar = 'checked';
    }
    $nama_pemda = get_option('_crb_daerah');
?>
<style>
    .modal-content label:after {
        content: ' *';
        color: red;
        margin-right: 5px;
    }
    #tabel_detail_nota,
    #tabel_detail_nota td,
    #tabel_detail_nota th {
		border: 0;
        padding: 0px;
	}
    #tabel_detail_nota td:first-of-type {
        width: 10em;
    }
    #tabel_detail_nota td:nth-child(2) {
        width: 1em;
    }

    #table_data_pejabat,
    #table_data_pejabat td,
    #table_data_pejabat th {
        border: 0;
        padding: 0px;
    }
</style>
<div style="padding: 15px;">    
    <h3 class="text-center" style="margin-top: 50px;">PEMERINTAH <?php echo strtoupper($nama_pemda) ?></br> NOTA PENCAIRAN DANA</br>Nomor: <?php echo $data_npd['nomor_npd'] ?></h3>
    <table id="tabel_detail_nota">
        <tbody>
            <tr>
                <td>Jenis NPD</td>
                <td>:</td>
                <td>
                    <input type="radio" id="panjar" <?php echo $dengan_panjar ?> disabled>
                    <label for="panjar" class="mr-2">Panjar</label>
                    <input type="radio" id="tanpa_panjar" <?php echo $tanpa_panjar ?> disabled>
                    <label for="tanpa_panjar">Tanpa Panjar</label>
                </td>
            </tr>
            <tr>
                <td>PPTK</td>
                <td>:</td>
                <td><?php echo $user_pptk->display_name ?></td>
            </tr>
            <tr>
                <td>Program</td>
                <td>:</td>
                <td><?php echo $kode_program . '  ' . $nama_program ?></td>
            </tr>
            <tr>
                <td>Kegiatan</td>
                <td>:</td>
                <td><?php echo $kode_kegiatan . '  ' . $nama_kegiatan ?></td>
            </tr>
            <tr>
                <td>Sub Kegiatan</td>
                <td>:</td>
                <td><?php echo $kode_sub_kegiatan . '  ' . $nama_sub_kegiatan ?></td>
            </tr>
            <tr>
                <td>Nomor DPA</td>
                <td>:</td>
                <td><?php echo $data_npd['nomor_dpa']; ?></td>
            </tr>
            <tr>
                <td>Tahun Anggaran</td>
                <td>:</td>
                <td><?php echo $data_npd['tahun_anggaran']; ?></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- table -->
<div style="padding: 15px;margin:0 0 3rem 0;">
    <table id="table_daftar_panjar">
        <thead>
            <tr>
                <th class="atas kanan bawah kiri text-center">No</th>
                <th class="atas kanan bawah text-center">Kode Rekening</th>
                <th class="atas kanan bawah text-center">Uraian</th>
                <th class="atas kanan bawah text-center">Anggaran</th>
                <th class="atas kanan bawah text-center">Sisa Anggaran</th>
                <th class="atas kanan bawah text-center">Pencairan</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $ret['html']; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="kanan bawah text-right kiri text_blok">Jumlah</td>
                <td class="kanan bawah text_blok text-right"><?php echo number_format($total_anggaran,0,",","."); ?></td>
                <td class="kanan bawah text_blok text-right"><?php echo number_format($total_sisa,0,",","."); ?></td>
                <td class="kanan bawah text_blok text-right"><?php echo number_format($total_pagu,0,",","."); ?></td>
            </tr>
        </tfoot>
    </table>
</div>
<!-- data pejabat -->
<div style="padding: 15px;margin:0 0 3rem 0;">
    <table id="table_data_pejabat">
        <thead>
            <tr class="text-center">
                <td>
                    Disetujui</br>Penggguna Anggaran
                </td>
                <td>
                    Disiapkan oleh</br>Pejabat Pelaksana Teknis Kegiatan
                </td>
            </tr>
        </thead>
        <tbody>
            <tr style="height: 7em;">
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="text-center">
                <td style="font-weight: 700; text-decoration:underline; width: 50%;" contenteditable="true" title="Klik untuk ganti teks!">
                    Nama Pengguna Anggaran
                </td>
                <td style="font-weight: 700; text-decoration:underline; width: 50%;" contenteditable="true" title="Klik untuk ganti teks!">
                    <?php echo strtoupper($user_pptk->display_name) ?>
                </td>
            </tr>
            <tr class="text-center">
                <td contenteditable="true" title="Klik untuk ganti teks!">
                    NIP. 1234567890
                </td>
                <td contenteditable="true" title="Klik untuk ganti teks!">
                    NIP. 1234567890
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script>
    jQuery(document).ready(function(){
        var action = ''
		+'<div id="action-sipd" class="hide-print">'
			+'<button class="btn btn-info ml-2" onclick="window.print();"><i class="dashicons dashicons-printer"></i> Print</button>'
		+'</div>';
	jQuery('body').prepend(action);
    });
</script>
