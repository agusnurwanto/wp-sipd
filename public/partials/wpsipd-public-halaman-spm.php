<?php
global $wpdb;
$api_key = get_option('_crb_api_key_extension');
$url = admin_url('admin-ajax.php');

$get_skpd = null;
$input = shortcode_atts(array(
    'id_skpd' => '',
    'tahun_anggaran' => ''
), $atts);

$unit = $wpdb->get_row(
    $wpdb->prepare('
        SELECT 
            kode_skpd,
            nama_skpd
        FROM data_unit
        WHERE id_skpd = %s
          AND tahun_anggaran = %d
          AND active = 1
    ', $input['id_skpd'], $input['tahun_anggaran']),
    ARRAY_A
);

if ($unit) {
    $get_skpd = $unit['kode_skpd'] . ' ' . $unit['nama_skpd'];
} else {
    echo 'Data SKPD tidak ditemukan';
}
?>
<style type="text/css">
    .wrap-table {
        overflow: auto;
        max-height: 100vh;
        width: 100%;
    }

    .wrap-table-detail {
        overflow: auto;
        max-height: 100vh;
        width: 100%;
    }
</style>
<div class="wrap-table">
    <h4 style="text-align: center; margin: 0; font-weight: bold;">Surat Perintah Membayar (SPM)<br><?php echo $get_skpd; ?>&nbsp;<br>Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h4><br>
    <table id="table-data-spm" cellpadding="2" cellspacing="0" style="font-family:\'Open Sans\',-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; border-collapse: collapse; width:100%; overflow-wrap: break-word;" class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">No</th>   
                <th class="text-center">idSpm</th>   
                <th class="text-center">idSpp</th>   
                <th class="text-center">nomorSpm</th>    
                <th class="text-center">idDetailSpm</th>    
                <th class="text-center">nomorSpp</th>    
                <th class="text-center">nilaiSpp</th>    
                <th class="text-center">tanggalSpp</th>  
                <th class="text-center">keteranganSpp</th>
                <th class="text-center">nilaiDisetujuiSpp</th>   
                <th class="text-center">tanggalDisetujuiSpp</th> 
                <th class="text-center">jenisSpp</th>    
                <th class="text-center">verifikasiSpp</th>   
                <th class="text-center">keteranganVerifikasi</th>    
                <th class="text-center">idSpd</th>   
                <th class="text-center">idPengesahanSpj</th> 
                <th class="text-center">kunciRekening</th>   
                <th class="text-center">alamatPenerimaSpp</th>   
                <th class="text-center">bankPenerimaSpp</th> 
                <th class="text-center">nomorRekeningPenerimaSpp</th>    
                <th class="text-center">npwpPenerimaSpp</th> 
                <th class="text-center">jenisLs</th> 
                <th class="text-center">isUploaded</th>  
                <th class="text-center">idKontrak</th>   
                <th class="text-center">idBA</th>    
                <th class="text-center">isSpm</th>   
                <th class="text-center">statusPerubahan</th> 
                <th class="text-center">isDraft</th> 
                <th class="text-center">isGaji</th>  
                <th class="text-center">is_sptjm</th>    
                <th class="text-center">tanggal_otorisasi</th>   
                <th class="text-center">is_otorisasi</th>    
                <th class="text-center">bulan_gaji</th>  
                <th class="text-center">id_pegawai_pptk</th> 
                <th class="text-center">nama_pegawai_pptk</th>   
                <th class="text-center">nip_pegawai_pptk</th>    
                <th class="text-center">kode_tahap</th>  
                <th class="text-center">is_tpp</th>  
                <th class="text-center">bulan_tpp</th>   
                <th class="text-center">id_pengajuan_tu</th> 
                <th class="text-center">nomor_pengajuan_tu</th>  
                <th class="text-center">tanggalSpm</th>  
                <th class="text-center">keteranganSpm</th>
                <th class="text-center">verifikasiSpm</th>   
                <th class="text-center">jenisSpm</th>    
                <th class="text-center">nilaiSpm</th>    
                <th class="text-center">id_jadwal</th>   
                <th class="text-center">id_tahap</th>    
                <th class="text-center">status_tahap</th> 
                <th class="text-center">isOtorisasi</th> 
                <th class="text-center">keteranganVerifikasiSpm</th> 
                <th class="text-center">tanggalVerifikasiSpm</th>
                <th class="text-center">tanggalOtorisasi</th>
                <th class="text-center">tahunSpp</th> 
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal fade" id="showspm" tabindex="-1" role="dialog" aria-labelledby="showspmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showspmLabel">Detail SPM</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="wrap-table-detail">
                    <h6>ID SPM : <span id="id_spm"></span></h6>
                    <table id="table-data-spm-detail" cellpadding="2" cellspacing="0" style="font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; border-collapse: collapse; width: 100%; overflow-wrap: break-word;" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>   
                                <th class="text-center">Nomor Spm</th>    
                                <th class="text-center">nomor_spp</th>  
                                <th class="text-center">tanggal_spd</th>    
                                <th class="text-center">total_spd</th>  
                                <th class="text-center">jumlah</th> 
                                <th class="text-center">kode_rekening</th>  
                                <th class="text-center">uraian</th> 
                                <th class="text-center">bank_pihak_ketiga</th>  
                                <th class="text-center">jabatan_pa_kpa</th> 
                                <th class="text-center">keterangan_spm</th> 
                                <th class="text-center">nama_daerah</th>    
                                <th class="text-center">nama_ibukota</th>   
                                <th class="text-center">nama_pa_kpa</th>    
                                <th class="text-center">nama_pihak_ketiga</th>  
                                <th class="text-center">nama_rek_pihak_ketiga</th>  
                                <th class="text-center">nilai_spm</th>  
                                <th class="text-center">nip_pa_kpa</th> 
                                <th class="text-center">no_rek_pihak_ketiga</th>    
                                <th class="text-center">npwp_pihak_ketiga</th>  
                                <th class="text-center">tahun</th>  
                                <th class="text-center">tanggal_spm</th>    
                                <th class="text-center">tanggal_spp</th>    
                                <th class="text-center">tipe</th>   
                                <th class="text-center">tahun_anggaran</th> 
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        get_datatable_spm();
    });

    function get_datatable_spm() {
        if (typeof tableDataSPM == 'undefined') {
            window.tableDataSPM = jQuery('#table-data-spm').on('preXhr.dt', function(e, settings, data) {
                jQuery("#wrap-loading").show();
            }).DataTable({
                "processing": true,
                "serverSide": true,
                "search": {
                    return: true
                },
                "ajax": {
                    url: '<?php echo $url; ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        'action': 'get_datatable_data_spm_sipd',
                        'api_key': '<?php echo $api_key; ?>',
                        'id_skpd': '<?php echo $input['id_skpd']; ?>',
                        'tahun_anggaran': <?php echo $input['tahun_anggaran']; ?>,
                    }
                },
                lengthMenu: [
                    [20, 50, 100, -1],
                    [20, 50, 100, "All"]
                ],
                order: [
                    [0, 'asc']
                ],
                "drawCallback": function(settings) {
                    jQuery("#wrap-loading").hide();
                },
                "columns": [{
                        "data": null,
                        "className": "text-center",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, full, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        "data": 'idSpm',
                        className: "text-center"
                    },    
                    {
                        "data": 'idSpp',
                        className: "text-center"
                    },    
                    {
                        "data": 'nomorSpm',
                        className: "text-center"
                    },     
                    {
                        "data": 'idDetailSpm',
                        className: "text-center"
                    },     
                    {
                        "data": 'nomorSpp',
                        className: "text-center"
                    },     
                    {
                        "data": 'nilaiSpp',
                        className: "text-center"
                    },     
                    {
                        "data": 'tanggalSpp',
                        className: "text-center"
                    },   
                    {
                        "data": 'keteranganSpp',
                        className: "text-center"
                    }, 
                    {
                        "data": 'nilaiDisetujuiSpp',
                        className: "text-center"
                    },    
                    {
                        "data": 'tanggalDisetujuiSpp',
                        className: "text-center"
                    },  
                    {
                        "data": 'jenisSpp',
                        className: "text-center"
                    },     
                    {
                        "data": 'verifikasiSpp',
                        className: "text-center"
                    },    
                    {
                        "data": 'keteranganVerifikasi',
                        className: "text-center"
                    },     
                    {
                        "data": 'idSpd',
                        className: "text-center"
                    },    
                    {
                        "data": 'idPengesahanSpj',
                        className: "text-center"
                    },  
                    {
                        "data": 'kunciRekening',
                        className: "text-center"
                    },    
                    {
                        "data": 'alamatPenerimaSpp',
                        className: "text-center"
                    },    
                    {
                        "data": 'bankPenerimaSpp',
                        className: "text-center"
                    },  
                    {
                        "data": 'nomorRekeningPenerimaSpp',
                        className: "text-center"
                    },     
                    {
                        "data": 'npwpPenerimaSpp',
                        className: "text-center"
                    },  
                    {
                        "data": 'jenisLs',
                        className: "text-center"
                    },  
                    {
                        "data": 'isUploaded',
                        className: "text-center"
                    },   
                    {
                        "data": 'idKontrak',
                        className: "text-center"
                    },    
                    {
                        "data": 'idBA',
                        className: "text-center"
                    },     
                    {
                        "data": 'isSpm',
                        className: "text-center"
                    },    
                    {
                        "data": 'statusPerubahan',
                        className: "text-center"
                    },  
                    {
                        "data": 'isDraft',
                        className: "text-center"
                    },  
                    {
                        "data": 'isGaji',
                        className: "text-center"
                    },   
                    {
                        "data": 'is_sptjm',
                        className: "text-center"
                    },     
                    {
                        "data": 'tanggal_otorisasi',
                        className: "text-center"
                    },    
                    {
                        "data": 'is_otorisasi',
                        className: "text-center"
                    },     
                    {
                        "data": 'bulan_gaji',
                        className: "text-center"
                    },   
                    {
                        "data": 'id_pegawai_pptk',
                        className: "text-center"
                    },  
                    {
                        "data": 'nama_pegawai_pptk',
                        className: "text-center"
                    },    
                    {
                        "data": 'nip_pegawai_pptk',
                        className: "text-center"
                    },     
                    {
                        "data": 'kode_tahap',
                        className: "text-center"
                    },   
                    {
                        "data": 'is_tpp',
                        className: "text-center"
                    },   
                    {
                        "data": 'bulan_tpp',
                        className: "text-center"
                    },    
                    {
                        "data": 'id_pengajuan_tu',
                        className: "text-center"
                    },  
                    {
                        "data": 'nomor_pengajuan_tu',
                        className: "text-center"
                    },   
                    {
                        "data": 'tanggalSpm',
                        className: "text-center"
                    },   
                    {
                        "data": 'keteranganSpm',
                        className: "text-center"
                    }, 
                    {
                        "data": 'verifikasiSpm',
                        className: "text-center"
                    },    
                    {
                        "data": 'jenisSpm',
                        className: "text-center"
                    },     
                    {
                        "data": 'nilaiSpm',
                        className: "text-center"
                    },     
                    {
                        "data": 'id_jadwal',
                        className: "text-center"
                    },    
                    {
                        "data": 'id_tahap',
                        className: "text-center"
                    },     
                    {
                        "data": 'status_tahap',
                        className: "text-center"
                    },  
                    {
                        "data": 'isOtorisasi',
                        className: "text-center"
                    },  
                    {
                        "data": 'keteranganVerifikasiSpm',
                        className: "text-center"
                    },  
                    {
                        "data": 'tanggalVerifikasiSpm',
                        className: "text-center"
                    }, 
                    {
                        "data": 'tanggalOtorisasi',
                        className: "text-center"
                    },  
                    {
                        "data": 'tahunSpp',
                        className: "text-center"
                    },
                ]
            });
        } else {
            tableDataSPM.draw();
        }
    }
    function showspm(id) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: '<?php echo $url; ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'action': 'get_data_spm_sipd',
                'api_key': '<?php echo $api_key; ?>',
                'tahun_anggaran': '<?php echo $input['tahun_anggaran'] ?>',
                'id_spm': id,
            },
            success: function(res) {
                console.log(res);
                if (res.status == 'success') {
                    jQuery('#id_spm').html(id);
                    var html = ''; 
                    var nomor_spm = "-"; 
                    var nomor_spp = "-"; 
                    var tanggal_spd = "-"; 
                    var total_spd = "-"; 
                    var jumlah = "-"; 
                    var kode_rekening = "-"; 
                    var uraian = "-"; 
                    var bank_pihak_ketiga = "-"; 
                    var jabatan_pa_kpa = "-"; 
                    var keterangan_spm = "-"; 
                    var nama_daerah = "-"; 
                    var nama_ibukota = "-"; 
                    var nama_pa_kpa = "-"; 
                    var nama_pihak_ketiga = "-"; 
                    var nama_rek_pihak_ketiga = "-"; 
                    var nama_skpd = "-"; 
                    var nama_sub_skpd = "-"; 
                    var nilai_spm = "-"; 
                    var nip_pa_kpa = "-"; 
                    var no_rek_pihak_ketiga = "-";
                    var npwp_pihak_ketiga = "-"; 
                    var tahun = "-"; 
                    var tanggal_spm = "-"; 
                    var tanggal_spp = "-"; 
                    var tipe = "-"; 
                    var tahun_anggaran = "-";
                    res.data.detail.map(function(b, i){ 
                        if(b.nomor_spm != null){ 
                           nomor_spm  = b.nomor_spm;
                        } 

                        if(b.nomor_spp != null){ 
                           nomor_spp  = b.nomor_spp;
                        } 

                        if(b.tanggal_spd != null){ 
                           tanggal_spd  = b.tanggal_spd;
                        } 

                        if(b.total_spd != null){ 
                           total_spd  = b.total_spd;
                        } 

                        if(b.jumlah != null){ 
                           jumlah  = b.jumlah;
                        } 

                        if(b.kode_rekening != null){ 
                           kode_rekening  = b.kode_rekening;
                        } 

                        if(b.uraian != null){ 
                           uraian  = b.uraian;
                        } 

                        if(b.bank_pihak_ketiga != null){ 
                           bank_pihak_ketiga  = b.bank_pihak_ketiga;
                        } 

                        if(b.jabatan_pa_kpa != null){ 
                           jabatan_pa_kpa  = b.jabatan_pa_kpa;
                        } 

                        if(b.keterangan_spm != null){ 
                           keterangan_spm  = b.keterangan_spm;
                        } 

                        if(b.nama_daerah != null){ 
                           nama_daerah  = b.nama_daerah;
                        } 

                        if(b.nama_ibukota != null){ 
                           nama_ibukota  = b.nama_ibukota;
                        } 

                        if(b.nama_pa_kpa != null){ 
                           nama_pa_kpa  = b.nama_pa_kpa;
                        } 

                        if(b.nama_pihak_ketiga != null){ 
                           nama_pihak_ketiga  = b.nama_pihak_ketiga;
                        } 

                        if(b.nama_rek_pihak_ketiga != null){ 
                           nama_rek_pihak_ketiga  = b.nama_rek_pihak_ketiga;
                        } 

                        if(b.nama_sub_skpd != null){ 
                           nama_sub_skpd  = b.nama_sub_skpd;
                        } 

                        if(b.nilai_spm != null){ 
                           nilai_spm  = b.nilai_spm;
                        } 

                        if(b.nip_pa_kpa != null){ 
                           nip_pa_kpa  = b.nip_pa_kpa;
                        } 

                        if(b.no_rek_pihak_ketig != null){ 
                           no_rek_pihak_ketig  = b.no_rek_pihak_ketiga;
                        }

                        if(b.npwp_pihak_ketiga != null){ 
                           npwp_pihak_ketiga  = b.npwp_pihak_ketiga;
                        } 

                        if(b.tahun != null){ 
                           tahun  = b.tahun;
                        } 

                        if(b.tanggal_spm != null){ 
                           tanggal_spm  = b.tanggal_spm;
                        } 

                        if(b.tanggal_spp != null){ 
                           tanggal_spp  = b.tanggal_spp;
                        } 

                        if(b.tipe != null){ 
                           tipe  = b.tipe;
                        } 
                        if(b.tahun_anggaran != null){ 
                           tahun_anggaran  = b.tahun_anggaran;
                        }
                        html += ''
                        +'<tr>' 
                            +'<td class="text-center">' + (i + 1) + '</td>' 
                            +'<td class="text-center">' + b.nomor_spm + '</td>' 
                            +'<td class="text-center">' + b.nomor_spp + '</td>' 
                            +'<td class="text-center">' + b.tanggal_spd + '</td>' 
                            +'<td class="text-center">' + b.total_spd + '</td>' 
                            +'<td class="text-center">' + b.jumlah + '</td>' 
                            +'<td class="text-center">' + b.kode_rekening + '</td>' 
                            +'<td class="text-center">' + b.uraian + '</td>' 
                            +'<td class="text-center">' + b.bank_pihak_ketiga + '</td>' 
                            +'<td class="text-center">' + b.jabatan_pa_kpa + '</td>' 
                            +'<td class="text-center">' + b.keterangan_spm + '</td>' 
                            +'<td class="text-center">' + b.nama_daerah + '</td>' 
                            +'<td class="text-center">' + b.nama_ibukota + '</td>' 
                            +'<td class="text-center">' + b.nama_pa_kpa + '</td>' 
                            +'<td class="text-center">' + b.nama_pihak_ketiga + '</td>' 
                            +'<td class="text-center">' + b.nama_rek_pihak_ketiga + '</td>' 
                            +'<td class="text-center">' + b.nilai_spm + '</td>' 
                            +'<td class="text-center">' + b.nip_pa_kpa + '</td>' 
                            +'<td class="text-center">' + b.no_rek_pihak_ketiga + '</td>'
                            +'<td class="text-center">' + b.npwp_pihak_ketiga + '</td>' 
                            +'<td class="text-center">' + b.tahun + '</td>' 
                            +'<td class="text-center">' + b.tanggal_spm + '</td>' 
                            +'<td class="text-center">' + b.tanggal_spp + '</td>' 
                            +'<td class="text-center">' + b.tipe + '</td>' 
                            +'<td class="text-center">' + b.tahun_anggaran + '</td>' 
                        +'</tr>';
                    });
                    jQuery('#table-data-spm-detail').DataTable().clear();
                    jQuery('#table-data-spm-detail tbody').html(html);
                    jQuery('#table-data-spm-detail').dataTable();
                    jQuery('#showspm').modal('show');
                } else {
                    alert(res.message);
                }
                jQuery('#wrap-loading').hide();
            }
        });
    }
</script>