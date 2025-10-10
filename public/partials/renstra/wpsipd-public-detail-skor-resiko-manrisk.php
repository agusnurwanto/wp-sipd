<?php 
global $wpdb;

if (!defined('WPINC')) {
    die;
}

$input = shortcode_atts(array(
    'tahun_anggaran' => '2023'
), $atts);

$sql_unit = $wpdb->prepare("
    SELECT 
        *
    FROM data_unit 
    WHERE 
        tahun_anggaran=%d
        AND active=1
    order by kode_skpd ASC
    ", $input['tahun_anggaran']);
$unit = $wpdb->get_results($sql_unit, ARRAY_A);
$unit = (!empty($unit)) ? $unit : array();

$data = $wpdb->get_results(
    $wpdb->prepare("
        SELECT id, id_skpd
        FROM data_opd_kecurangan_mcp
        WHERE tahun_anggaran=%d
          AND active=1
    ", $input['tahun_anggaran']), ARRAY_A);

$opd_master = $wpdb->get_results(
    $wpdb->prepare("
        SELECT id_skpd, nama_skpd
        FROM data_unit
        WHERE tahun_anggaran=%d
          AND active=1
    ", $input['tahun_anggaran']), ARRAY_A);
$data_sasaran = $wpdb->get_results(
    $wpdb->prepare("
        SELECT 
            * 
        FROM data_sasaran_tahapan_mcp 
        WHERE tahun_anggaran=%d 
            AND active=1
        ORDER BY id ASC
    ", $input['tahun_anggaran']),
    ARRAY_A
);
?>
<style type="text/css">
    .table-contoh th {
        background-color: #C7D4E5; 
        text-align: center;
        padding: 8px;
        border: 1px solid #9c9c9cff;
        font-weight: bold;
    }

    .table_sasaran th {
        background-color: #a8f5b4ff; 
        text-align: center;
        padding: 8px;
        border: 1px solid #989898ff;
        font-weight: bold;
    }
    
    .td-sangat-tinggi { 
        background-color: #ff6e4e;
    }
    .td-tinggi {
         background-color: #ffb499; 
     }
    .td-sedang { 
        background-color: #ffeec9; 
    }
    .td-rendah { 
        background-color: #cfffdd;  
    }
    .td-sangat-rendah { 
        background-color: #e8ffef;  
    }

    /* Biar chip lebih kecil dan rapih */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff; /* biru bootstrap */
        border: none;
        color: #0c0c0cff;
        font-size: 15px;
        padding-left: 0px !important;
        margin-top: 3px !important;
        margin-right: 3px;
        border-radius: 3px;
    }

    /* Teks di dalam chip */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        padding-left: 2px;
    }

    /* Tombol silang di chip */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #050505ff;
        margin-right: 4px !important;
        font-weight: bold;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ffdddd;
    }


</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<div class="container-md">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <input type="hidden" value="<?php echo get_option('_crb_api_key_extension'); ?>" id="api_key">
        <h1 class="text-center" style="margin:3rem;">
            PETUNJUK SKOR RISIKO<br>Tahun Anggaran <?php echo $input['tahun_anggaran']; ?>
        </h1>
        <div class="row">
            <div class="col-md-4">
                <table class="table-contoh" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word;">
                    <thead>
                        <tr>
                            <th colspan="5" class="text-center">Perkalian Antara Kemungkinan dan Dampak, apabila Risiko Terjadi</th>
                        </tr>
                        <tr>
                            <th class="text-center">Skor</th>
                            <th class="text-center">Paparan Risiko (Wajib)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center td-sangat-tinggi">23 s.d. 25</td>
                            <td class="text-center">Sangat Tinggi</td>
                        </tr>
                        <tr>
                            <td class="text-center td-tinggi">18 s.d. 22</td>
                            <td class="text-center">Tinggi</td>                            
                        </tr>
                        <tr>
                            <td class="text-center td-sedang">9 s.d. 17</td>
                            <td class="text-center">Sedang</td>
                        </tr>
                        <tr>
                            <td class="text-center td-rendah">4 s.d. 8</td>
                            <td class="text-center">Rendah</td>                            
                        </tr>
                        <tr>
                            <td class="text-center td-sangat-rendah">1 s.d. 3</td>
                            <td class="text-center">Sangat Rendah</td>                            
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-4">
                <table class="table-contoh" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word;">
                    <thead>
                        <tr>
                            <th colspan="5" class="text-center">Kriteria RISIKO (KEMUNGKINAN/KETERJADIAN)<br>
                            <span style="font-weight: normal; font-size: 12px;">
                                Seberapa sering Dampak dari Risiko akan terjadi terhadap Tujuan
                            </span>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-center">Skor</th>
                            <th class="text-center">Paparan Risiko (Wajib)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center td-sangat-tinggi">5</td>
                            <td class="text-center">Hampir Pasti Terjadi</td>
                        </tr>
                        <tr>
                            <td class="text-center td-tinggi">4</td>
                            <td class="text-center">Sangat Sering Terjadi</td>                            
                        </tr>
                        <tr>
                            <td class="text-center td-sedang">3</td>
                            <td class="text-center">Sering Terjadi</td>
                        </tr>
                        <tr>
                            <td class="text-center td-rendah">2</td>
                            <td class="text-center">Kadang Terjadi</td>                            
                        </tr>
                        <tr>
                            <td class="text-center td-sangat-rendah">1</td>
                            <td class="text-center">Jarang Terjadi</td>                            
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-4">
                <table class="table-contoh" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word;">
                    <thead>
                        <tr>
                            <th colspan="5" class="text-center">Kriteria RISIKO (DAMPAK)</th>
                            
                        </tr>
                        <tr>
                            <th class="text-center">Skor</th>
                            <th class="text-center">Paparan Risiko (Wajib)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center td-sangat-tinggi">5</td>
                            <td class="text-center">Katastoprik</td>
                        </tr>
                        <tr>
                            <td class="text-center td-tinggi">4</td>
                            <td class="text-center">Besar</td>                            
                        </tr>
                        <tr>
                            <td class="text-center td-sedang">3</td>
                            <td class="text-center">Sedang</td>
                        </tr>
                        <tr>
                            <td class="text-center td-rendah">2</td>
                            <td class="text-center">Kecil</td>                            
                        </tr>
                        <tr>
                            <td class="text-center td-sangat-rendah">1</td>
                            <td class="text-center">Tidak Signifikan</td>                            
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="container-md">
    <div style="padding: 10px;margin:0 15px 3rem 15px;">
        <div class="row">
            <table class="table-keterangan" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word;">
                <thead>
                    <tr>
                        <th class="text-center align-middle" rowspan="2">Level Dampak</th>
                        <th class="text-center" colspan="5">Area Dampak</th>
                    </tr>                
                    <tr>    
                        <th class="text-center">Kerugian Negara</th>
                        <th class="text-center">Penurunan Reputasi</th>
                        <th class="text-center">Penurunan Kinerja</th>
                        <th class="text-center">Ganguan Terhadap Layanan Organisasi</th>
                        <th class="text-center">Tuntutan Hukum  </th>
                    </tr>
                </thead>                
                <tbody>
                    <tr>
                        <td class="text-center">Tidak Signifikan (1)</td> 
                        <td class="text-center">Jumlah Kerugian negara ≤ Rp 10 juta</td> 
                        <td class="text-center">Keluhan <i>Stakeholder</i> secara langsung lisan / tertulis ke organisasi jumlahnya ≤ 3 dalam satu periode</td> 
                        <td class="text-center">Pencapaian target kinerja ≥ 100%</td> 
                        <td class="text-center">Pelayanan tertunda ≤ 1 hari</td> 
                        <td class="text-center">Jumlah tuntutan hukum ≤ 5 kali dalam satu periode</td> 
                    </tr>  
                    <tr>
                        <td class="text-center">Minor (2)</td> 
                        <td class="text-center">Jumlah Kerugian negara lebih dari Rp 10 juta s.d Rp 50 juta</td> 
                        <td class="text-center">Keluhan <i>Stakeholder</i> secara langsung lisan / tertulis ke organisasi jumlahnya lebih dari 3 dalam satu periode</td> 
                        <td class="text-center">Pencapaian target kinerja di atas 80% s.d 100%</td> 
                        <td class="text-center">Pelayanan tertunda di atas 1 s.d 5 hari</td> 
                        <td class="text-center">Jumlah tuntutan hukum di atas 5 s.d 15 kali dalam satu periode</td> 
                    </tr> 
                    <tr>
                        <td class="text-center">Moderat (3)</td> 
                        <td class="text-center">Jumlah Kerugian negara lebih dari Rp 50 juta s.d Rp 100 juta</td> 
                        <td class="text-center">Pemberitaan negatif di media massa lokal</td> 
                        <td class="text-center">Pencapaian target kinerja di atas 50% s.d 80%</td> 
                        <td class="text-center">Pelayanan tertunda di atas 5 s.d 15 hari</td> 
                        <td class="text-center">Jumlah tuntutan hukum di atas 15 s.d 30 kali dalam satu periode</td> 
                    </tr> 
                    <tr>
                        <td class="text-center">Signifikan (4)</td> 
                        <td class="text-center">Jumlah Kerugian negara lebih dari Rp 100 juta s.d Rp 500 juta</td> 
                        <td class="text-center">Pemberitaan negatif di media massa nasional</td> 
                        <td class="text-center">Pencapaian target kinerja di atas 25% s.d 50%</td> 
                        <td class="text-center">Pelayanan tertunda di atas 15 s.d 30 hari</td> 
                        <td class="text-center">Jumlah tuntutan hukum di atas 30 s.d 50 kali dalam satu periode</td> 
                    </tr> 
                    <tr>
                        <td class="text-center">Sangat Signifikan (5)</td> 
                        <td class="text-center">Jumlah Kerugian negara lebih dari Rp 500 juta</td> 
                        <td class="text-center">Pemberitaan negatif di media massa internasional</td> 
                        <td class="text-center">Pencapaian target kinerja ≤ 25%</td> 
                        <td class="text-center">Pelayanan tertunda lebih dari 30 hari</td> 
                        <td class="text-center">Jumlah tuntutan hukum lebih dari 50 kali dalam satu periode</td> 
                    </tr> 
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container-md">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <div class="row">
            <div class="col-md-7">
                <table class="table-keterangan" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word;  background-color: #d8f3dc">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="2">Controllable/Uncontrollable</th>
                        </tr>
                    </thead>                
                    <tbody>
                        <tr>
                            <td class="text-center">C</td> 
                            <td>Controlable : Risiko yang dapat dikendalikan, dapat diidentifikasi, dinilai, dan dikelola, dapat diatasi melalui tindakan dan keputusan proaktif
                                <br>Contohnya termasuk risiko nilai tukar mata uang, arus kas yang buruk, tuntutan hukum, dan masalah keterampilan							
                            </td> 
                        </tr>  
                        <tr>
                            <td>UC</td>
                            <td>Uncontrolable: Risiko yang tidak dapat dikendalikan, Merupakan kebalikan dari risiko yang dapat dikendalikan
                                <br>Contohnya termasuk bencana alam, kerusuhan politik, perang, dan lingkungan hidup							
                            </td>
                        </tr>                 
                    </tbody>
                </table>
            </div>

            <div class="col-md-5">
                <table cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word; background-color: #e3f2fd">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">Pemilik Resiko</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">Kepala Daerah</td>
                            <td class="text-center">untuk risiko Pemda (Risiko StrategisPemda)</td>                            
                        </tr>
                        <tr>
                            <td class="text-center">Kepala OPD</td>
                            <td class="text-center">untuk risiko yang menghambat tujuan OPD/ pada Program (Risiko Strategis OPD)</td>                            
                        </tr>
                        <tr>
                            <td class="text-center">Kepala Bidang</td>
                            <td class="text-center">untuk risiko pada Program/Kegiatan (Risiko Operasional)</td>                            
                        </tr>
                        <tr>
                            <td class="text-center">PA/PK</td>
                            <td class="text-center"></td>                            
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="container-md">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <div class="row">
            <div class="col-md-6">
                <table class="table-keterangan" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word; background-color: #e3f2fd">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="2">Sumber</th>
                        </tr>
                    </thead>                
                    <tbody>
                        <tr>
                            <td class="text-center">Internal</td> 
                            <td>Sumber risiko berasal dari internal</td> 
                        </tr> 
                        <tr>
                            <td class="text-center">Eksternal</td> 
                            <td>Sumber risiko berasal dari eksternal</td> 
                        </tr>  
                        <tr>
                            <td class="text-center">Internal & Eksternal</td> 
                            <td>Sumber risiko berasal dari internal dan eksternal</td> 
                        </tr>              
                    </tbody>
                </table>
            </div>

            <div class="col-md-3">
                <table cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word; background-color: #e3f2fd">
                    <thead>
                        <tr>
                            <th colspan="1" class="text-center">Pihak Yang Terkena</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">Pemerintah Kabupaten Magetan</td>                                                    
                        </tr>
                        <tr>
                            <td class="text-center">Perangkat Daerah</td>                                                    
                        </tr>
                        <tr>
                            <td class="text-center">Kepala OPD</td>                                                    
                        </tr>
                        <tr>
                            <td class="text-center">Pagawai OPD</td>                                                    
                        </tr>
                        <tr>
                            <td class="text-center">Masyarakat</td>                                                    
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-3">
                <table class="table-keterangan" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word; background-color: #e3f2fd">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="2">Jenis Risiko Kecurangan</th>
                        </tr>
                    </thead>                
                    <tbody>
                        <tr>
                            <td class="text-center">Konflik kepentingan</td> 
                        </tr> 
                        <tr>
                            <td class="text-center">Pemberian suap</td> 
                        </tr>
                        <tr>
                            <td class="text-center">Penggelapan</td> 
                        </tr>
                        <tr>
                            <td class="text-center">Pemalsuan Data</td> 
                        </tr>
                        <tr>
                            <td class="text-center"> Pemerasan/ Pungutan Liar</td> 
                        </tr>  
                        <tr>
                            <td class="text-center">Penyalahgunaan wewenang</td> 
                        </tr>   
                        <tr>
                            <td class="text-center">Fraud/ Korupsi</td> 
                        </tr>           
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="container-md">
    <div style="padding: 10px;margin:0 0 3rem 0;">
        <div class="row">
            <div class="col-md-7">
                <table class="table-keterangan" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word;  background-color: #fefae0;">
                    <thead>
                        <tr>
                            <th class="text-center">Level Kemungkinan</th>
                            <th class="text-center">Kriteria Kemungkinan</th>
                        </tr>
                    </thead>                
                    <tbody>
                        <tr>
                            <td class="text-center">Hampir Tidak Terjadi (1)</td> 
                            <td> Kemungkinan terjadinya sangat jarang (kurang dari 2 kali dalam 5 tahun) 
                                <br> Persentase kemungkinan terjadinya kurang dari 5% dalam 1 periode
                            </td> 
                        </tr>  
                        <tr>
                            <td class="text-center">Jarang Terjadi (2)</td> 
                            <td> Kemungkinan terjadinya jarang (2 kali s.d 10 kali dalam 5 tahun)
                                <br>  Persentase kemungkinan terjadinya 5% s.d 10% dalam 1 periode
                            </td> 
                        </tr>
                        <tr>
                            <td class="text-center">Kadang Terjadi (3)</td> 
                            <td> Kemungkinan terjadinya cukup sering (di atas 10 kali s.d 18 kali dalam 5 tahun) 
                                <br> Persentase kemungkinan terjadinya di atas 10% s.d 20% dalam 1 periode
                            </td> 
                        </tr>  
                        <tr>
                            <td class="text-center">Sering Terjadi (4)</td> 
                            <td> Kemungkinan terjadinya sering (di atas 18 kali s.d 26 kali dalam 5 tahun)
                                <br> Persentase kemungkinan terjadinya di atas 20% s.d 50% dalam 1 periode
                            </td> 
                        </tr> 
                        <tr>
                            <td class="text-center">Hampir Pasti Terjadi (5)</td> 
                            <td> Kemungkinan terjadinya sangat sering (di atas 26 kali dalam 5 tahun) 
                                <br> Persentase kemungkinan terjadinya lebih dari 50% dalam 1 periode
                            </td> 
                        </tr>                   
                    </tbody>
                </table>
            </div>

            <div class="col-md-5">
                <div style="text-align:left; margin-bottom:5px;">
                    <button type="button" class="btn btn-primary" onclick="tambah_opd()">
                        + Tambah OPD
                    </button>
                </div>
                <table class="daftar_opd" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word;">
                    <thead>
                        <tr style="background-color: #ffd7ba">
                            <th colspan="5" class="text-center">OPD yang menyusun Daftar Risiko Kecurangan 2025 untuk MCP (3.2)</th>                            
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div>
    <div style="padding: 10px;margin:0 15px 3rem 15px;">
        <div class="row">
            <div style="text-align:left; margin-bottom:5px;">
                <button type="button" class="btn btn-success" onclick="tambah_sasaran()">
                    <span class="dashicons dashicons-insert"></span>
                        Tambah Sasaran MCP     
                </button>
            </div>
            <table class="table_sasaran" cellpadding="2" cellspacing="0" style="width:100%; overflow-wrap: break-word;">
                <thead>    
                    <tr>    
                        <th class="text-center" colspan="4">Tabel Data Master Sasaran Dan Tahapan MCP</th>     
                    </tr>              
                    <tr>    
                        <th style="width:70px;" class="text-center">No</th>
                        <th class="text-center">Sasaran</th>
                        <th class="text-center">Tahapan</th>
                        <th style="width:15%;" class="text-center">Aksi</th>
                    </tr>
                </thead>                
                <tbody>
                    <?php
                    if (!empty($data_sasaran)) {
                        $no = 1;
                        foreach ($data_sasaran as $row) {
                            echo '
                            <tr>
                                <td class="text-center">' . $no++ . '</td>
                                <td id="sasaran_'.$row['id'].'">' . esc_html($row['sasaran']) . '</td>
                                <td id="tahapan_'.$row['id'].'">' . esc_html($row['tahapan']) . '</td>
                                <td class="text-center">
                                    <button class="btn btn-warning" onclick="edit_sasaran(' . $row['id'] . ')";>
                                        <span class="dashicons dashicons-edit"></span>
                                    </button>
                                    <button class="btn btn-danger" onclick="hapus_sasaran('. $row['id'] . ')";>
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </td>
                            </tr>';
                        }
                    } else {
                        echo '
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data sasaran</td>
                        </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal tambah OPD Penyusun -->
<div class="modal fade" id="modalTambahOPD" tabindex="-1">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih OPD Penyusun Daftar Risiko Kecurangan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body">
            <div class="mb-3">
            <label>Nama OPD</label>
            <select id="opd_select" class="form-control" multiple style="width:100%;">
                <!-- opsi diisi lewat AJAX -->
            </select>
        </div>
    </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="simpan_opd()">Simpan</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal tambah Sasaran tahapan -->
<div class="modal fade" id="modalTambahSasaran" tabindex="-1">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modalSasaranTitle">Tambah Sasaran dan Tahapan Kecurangan MCP</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
            <div class="modal-body">
                <input type="hidden" id="id" value="">

            <div class="mb-3">
            <label class="form-label">Sasaran</label>
            <input type="text" id="sasaran" class="form-control"/>
            </div>

            <div class="mb-3">
            <label class="form-label">Tahapan</label>
            <input type="text" id="tahapan" class="form-control"/>
            </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="simpan_sasaran()">Simpan</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('#opd_select').select2({
            minimumResultsForSearch: 0, // selalu tampil search
            dropdownParent: $('#modalTambahOPD'),
            placeholder: "Pilih OPD"
        });

        let tbody = jQuery('.daftar_opd tbody');
        tbody.empty();
        var opdDataAwal   = <?php echo json_encode($data); ?>;
        var opdMasterAwal = <?php echo json_encode($opd_master); ?>;
        opdDataAwal.forEach((item, index) => {
            const opd = opdMasterAwal.find(u => u.id_skpd == item.id_skpd);
            const nama_opd = opd ? opd.nama_skpd : 'Unknown';

            tbody.append(`<tr>
                <td class="text-center">${index + 1}</td>
                <td>${nama_opd}</td>
                <td>
                    <button class="btn btn-danger" onclick="hapus_daftar_opd(${item.id_skpd})";>
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </td>
            </tr>`);
        });
    });

    function tambah_opd() {
        jQuery('#opd_select').val(null).trigger('change');

        // Hapus semua option (kecuali placeholder)
        jQuery('#opd_select').html('<option value="">Pilih OPD</option>');
        load_opd();

        // tampilkan modal
        jQuery('#modalTambahOPD').modal('show');
    }

    function load_opd() {
        jQuery("#wrap-loading").show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'load_opd_manrisk',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>
            },
            success: function(response) {
                jQuery("#wrap-loading").hide();
                if(response.status == 'success') {
                    jQuery('#opd_select').html(response.data);
                } else {
                    console.log('Data OPD Kosong!');
                }
            },
            error: function(xhr, status, error) {
                jQuery("#wrap-loading").show();
                console.error('AJAX error:', error)
            }
        });
    }

    function simpan_opd() {
        jQuery("#wrap-loading").show();
        let opd = jQuery('#opd_select').val();
        var opdDataAwal = <?php echo json_encode($data); ?>;
        var opdMasterAwal = <?php echo json_encode($opd_master); ?>;

        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'simpan_opd_manrisk',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                opd: opd
            },
            success: function(response) {
                jQuery("#wrap-loading").hide();
                if(response.status == 'success') {
                   let tbody = jQuery('.daftar_opd tbody');
                    tbody.empty(); // kosongkan dulu
                    const data = response.data;
                    const master = response.master;
                    data.forEach((item, index) => {
                        // Cari nama OPD dari master
                        const opd = master.find(u => u.id_skpd == item.id_skpd);
                        const nama_opd = opd ? opd.nama_skpd : 'Unknown';

                        tbody.append(`<tr id="row-opd-${item.id_skpd}">
                            <td class="text-center">${index + 1}</td>
                            <td>${nama_opd}</td>
                            <td>
                                <button class="btn btn-danger" onclick="hapus_daftar_opd(${item.id_skpd})";>
                                    <span class="dashicons dashicons-trash"></span>
                                </button>
                            </td>
                        </tr>`);
                    });

                    jQuery('#modalTambahOPD').modal('hide'); 
                } else {
                    alert('Gagal menyimpan OPD!');
                }
            },
            error: function(xhr, status, error) {
                jQuery("#wrap-loading").show();
                console.error('AJAX error:', error);
            }
        });
    }

    function hapus_daftar_opd(id_skpd) {
        if(confirm('Apakah anda yakin untuk menghapus data ini?')){
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'hapus_daftar_opd_manrisk',
                    api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                    id_skpd: id_skpd,
                    tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                },
                success: function(response) {
                    console.log(response);
                    jQuery('#wrap-loading').hide();
                    if (response.status === 'success') {
                        alert(response.message);
                        jQuery(`#row-opd-${id_skpd}`).remove();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    jQuery('#wrap-loading').hide();
                    alert('Terjadi kesalahan saat mengirim data!');
                }
            });
        }
    }

    function tambah_sasaran() {
        jQuery('#nama_sasaran').val('');
        jQuery('#keterangan_sasaran').val('');
         jQuery('.modalSasaranTitle').text('Tambah Sasaran dan Tahapan Kecurangan MCP');
        jQuery('#modalTambahSasaran').modal('show');
    }

    function simpan_sasaran() {
        let id =  jQuery('#id').val();
        let sasaran = jQuery('#sasaran').val();
        let tahapan = jQuery('#tahapan').val();

        if (sasaran === '') {
            alert('Sasaran belum diisi!');
            jQuery('#sasaran').focus();
            return false;
        }

        if (tahapan === '') {
            alert('Tahapan belum diisi!');
            jQuery('#tahapan').focus();
            return false;
        }
        jQuery("#wrap-loading").show();
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                action:'simpan_sasaran_mcp',
                api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>,
                id: id,
                sasaran: sasaran,
                tahapan: tahapan,
            },
            success: function(response) {
                console.log(response);
                jQuery('#wrap-loading').hide();
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload();
                    
                    jQuery('#sasaran').val('sasaran');
                    jQuery('#tahapan').val('tahapan');
                    jQuery('#modalTambahSasaran').modal('hide');
                } else {
                    alert(response.message || 'Terjadi kesalahan saat menyimpan data.');
                }
            },
            error: function() {
                jQuery("#wrap-loading").hide();
                alert('Gagal menghubungi server. Coba lagi.');
            }
        });
    }

    function hapus_sasaran(id) {
        if(confirm('Apakah anda yakin menghapus data ini?')) {
            jQuery("#wrap-loading").show();
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'hapus_sasaran_mcp',
                    api_key: '<?php echo get_option('_crb_api_key_extension'); ?>',
                    id: id,
                    tahun_anggaran: <?php echo $input['tahun_anggaran']; ?>
                },
                success: function(response) {
                    console.log(response);
                    jQuery('#wrap-loading').hide();
                    if (response.status === 'success') {
                        alert(response.message);
                        jQuery(`#row_${id}`).remove();
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    jQuery('#wrap-loading').hide();
                    alert('Terjadi kesalahan saat mengirim data!');
                }
            });
        }
    }

    function edit_sasaran(id){
        let sasaran = jQuery('#sasaran_' + id).text().trim();
        let tahapan = jQuery('#tahapan_' + id).text().trim();
        jQuery('#id').val(id);
        jQuery('#sasaran').val(sasaran);
        jQuery('#tahapan').val(tahapan);
        jQuery('.modalSasaranTitle').text('Edit Sasaran dan Tahapan Kecurangan MCP');
        jQuery('#modalTambahSasaran').modal('show');
    }
</script>