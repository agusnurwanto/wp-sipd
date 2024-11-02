<?php
if (!defined('WPINC')) {
    die;
}
$input = shortcode_atts(array(
    'tahun_anggaran' => ''
), $atts);
if (empty($input['tahun_anggaran'])) {
    die('tahun anggaran kosong!');
}
$validate_user = $this->user_authorization_wpsipd('sppd', $input['tahun_anggaran']);
if ($validate_user['status'] == 'error') {
    die('<h1>' . $validate_user['message'] . '</h1>');
}
echo "<script>console.log('Debug Objects: " . $validate_user['message'] . "' );</script>";
?>
<style type="text/css">
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<div class="pb-4 mb-5">
    <h1 class="text-center my-4">Data Surat Perintah Tugas (SPT)</h1>
    <h2 class="text-center my-4">Tahun Anggaran <?php echo $input['tahun_anggaran']; ?></h2>
    <div class="m-4">
        <button class="btn btn-primary" onclick="showModalTambahSpt();">
            <span class="dashicons dashicons-plus"></span> Tambah Data
        </button>
    </div>
</div>
<div class="wrap-table m-4">
    <table id="tableData">
        <thead>
            <tr>
                <th class="text-center">Nomor SPT</th>
                <th class="text-center">Dasar SPT</th>
                <th class="text-center">Tujuan SPT</th>
                <th class="text-center">Tanggal SPT</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<!-- modal tambah data -->
<div class="modal fade mt-4" id="modalTambahData" tabindex="-1" role="dialog" aria-labelledby="modalTambahData" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_data_spt" name="id_data_spt">

                <div class="card bg-light mb-3">
                    <div class="card-header">Informasi SPT</div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="idSkpd">Pilih SKPD</label>
                                <select name="idSkpd" class="form-control" id="idSkpd">
                                    <?php echo $validate_user['options']; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="tanggalSpt">Tanggal SPT</label>
                                <input type="date" name="tanggalSpt" class="form-control" id="tanggalSpt">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="nomorSpt">Nomor SPT</label>
                                <input type="text" name="nomorSpt" class="form-control" id="nomorSpt">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-light mb-3">
                    <div class="card-header">Dasar dan Tujuan</div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="dasarSpt">Dasar SPT</label>
                                <textarea name="dasarSpt" class="form-control" id="dasarSpt"></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="tujuanSpt">Tujuan SPT</label>
                                <textarea name="tujuanSpt" class="form-control" id="tujuanSpt"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="submitDataSpt();" class="btn btn-primary submitBtn"></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Utama -->
<div class="modal fade bd-example-modal-xl" id="modal_tambah_pegawai" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Pegawai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m-3">
                    <button class="btn btn-primary" onclick="showModalTambahSppd()">
                        <span class="dashicons dashicons-plus"></span> Tambah Pegawai
                    </button>
                </div>
                <div class="container-fluid">
                    <table id="tablePegawai" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Pegawai</th>
                                <th class="text-center">Jabatan</th>
                                <th class="text-center">Tempat Berangkat</th>
                                <th class="text-center">Tempat Tujuan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Sppd -->
<div class="modal fade" id="modal_form_sppd" tabindex="-1" role="dialog" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-sppd" id="modalFormLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambahSppd">
                    <input type="hidden" id="nomor_spt" value="">
                    <input type="hidden" id="id_data_sppd" value="">

                    <div class="card bg-light mb-3">
                        <div class="card-header">Informasi SPPD</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="tahunAnggaran">Tahun Anggaran</label>
                                <input type="number" name="tahunAnggaran" class="form-control" id="tahunAnggaran" value="<?php echo $input['tahun_anggaran']; ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label for="tanggalSppd">Tanggal SPPD</label>
                                <input type="date" name="tanggalSppd" class="form-control" id="tanggalSppd">
                            </div>
                            <div class="form-group">
                                <label for="nomorSppd">Nomor SPPD</label>
                                <input type="text" name="nomorSppd" class="form-control" id="nomorSppd">
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-header">Pilih Pegawai</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="namaPegawai">Nama Pegawai</label>
                                <input type="text" name="namaPegawai" class="form-control" id="namaPegawai">
                            </div>
                            <div class="form-group">
                                <label for="nipPegawai">NIP</label>
                                <input type="number" name="nipPegawai" class="form-control" id="nipPegawai">
                            </div>
                            <div class="form-group">
                                <label for="golPegawai">Pangkat/Gol</label>
                                <input type="text" name="golPegawai" class="form-control" id="golPegawai">
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-header">Tujuan Perjalanan Dinas</div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="tempatBerangkat">Tempat Berangkat</label>
                                    <input type="text" name="tempatBerangkat" class="form-control" id="tempatBerangkat">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="tanggalBerangkat">Tanggal Berangkat</label>
                                    <input type="date" name="tanggalBerangkat" class="form-control" id="tanggalBerangkat">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="tempatTujuan">Tempat Tujuan</label>
                                    <input type="text" name="tempatTujuan" class="form-control" id="tempatTujuan">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="tanggalSampai">Tanggal Sampai</label>
                                    <input type="date" name="tanggalSampai" class="form-control" id="tanggalSampai">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tanggalKembali">Tanggal Kembali</label>
                                <input type="date" name="tanggalKembali" class="form-control" id="tanggalKembali">
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-header">Maksud Perjalanan dan Alat Angkut yang digunakan</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="maksudSppd">Maksud Perjalanan Dinas</label>
                                <textarea name="maksudSppd" class="form-control" id="maksudSppd"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="alatAngkut">Alat Angkut</label>
                                <select name="alatAngkut" class="form-control" id="alatAngkut">
                                    <option value="">Pilih Alat Angkut</option>
                                    <option value="Kendaraan Pribadi">Kendaraan Pribadi</option>
                                    <option value="Kendaraan Dinas">Kendaraan Dinas</option>
                                    <option value="Kendaraan Umum">Kendaraan Umum</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">Keterangan Lain</label>
                                <textarea name="keterangan" class="form-control" id="keterangan"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary submitBtnSppd" onclick="submitDataSppd(); return false">Simpan</button>
            </div>
        </div>
    </div>
</div>
</div>


<script>
    window.tahun_anggaran = <?php echo json_encode($input['tahun_anggaran']); ?>;
    jQuery(document).ready(function() {
        getDataTable();
    });

    function getDataTable() {
        if (typeof tableDataSpt === 'undefined') {
            window.tableDataSpt = jQuery('#tableData').on('preXhr.dt', function(e, settings, data) {
                jQuery("#wrap-loading").show();
            }).DataTable({
                "processing": true,
                "serverSide": true,
                "search": {
                    return: true
                },
                "ajax": {
                    url: ajax.url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'action': 'get_datatable_data_spt',
                        'api_key': ajax.api_key,
                        'tahun_anggaran': tahun_anggaran
                    }
                },
                lengthMenu: [
                    [20, 50, 100, -1],
                    [20, 50, 100, "All"]
                ],
                order: [],
                "drawCallback": function(settings) {
                    jQuery("#wrap-loading").hide();
                },
                "columns": [{
                        "data": 'nomor_spt',
                        className: "text-center"
                    },
                    {
                        "data": 'dasar_spt',
                        className: "text-left"
                    },
                    {
                        "data": 'tujuan_spt',
                        className: "text-left"
                    },
                    {
                        "data": 'tgl_spt',
                        className: "text-center"
                    },
                    {
                        "data": 'aksi',
                        className: "text-center"
                    }
                ]
            });
        } else {
            tableDataSpt.draw();
        }
    }

    function hapus_spt(id) {
        let confirmDelete = confirm("Apakah anda yakin akan menghapus data ini?");
        if (confirmDelete) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: ajax.url,
                type: 'post',
                data: {
                    'action': 'hapus_data_spt_by_id',
                    'api_key': ajax.api_key,
                    'id': id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        alert("Berhasil Hapus Data!");
                        getDataTable();
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    function hapus_sppd(id) {
        let confirmDelete = confirm("Apakah anda yakin akan menghapus data ini?");
        if (confirmDelete) {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: ajax.url,
                type: 'post',
                data: {
                    'action': 'hapus_data_sppd_by_id',
                    'api_key': ajax.api_key,
                    'id': id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        get_table_pegawai_spt_sppd(jQuery("#nomor_spt").val()).then(() => {
                            alert("Berhasil Hapus Data!");
                        })
                    } else {
                        alert(`GAGAL! \n${response.message}`);
                    }
                }
            });
        }
    }

    function edit_spt(id) {
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: ajax.url,
            dataType: 'JSON',
            data: {
                'action': 'get_data_spt_by_id',
                'api_key': ajax.api_key,
                'id': id,
            },
            success: function(res) {
                jQuery('#id_data_spt').val(res.data.id);

                jQuery('#tanggalSpt').val(res.data.tgl_spt);
                jQuery('#nomorSpt').val(res.data.nomor_spt);
                jQuery('#dasarSpt').val(res.data.dasar_spt);
                jQuery('#tujuanSpt').val(res.data.tujuan_spt);

                jQuery(".submitBtn").html("Perbarui");
                jQuery(".modal-title").html("Edit Surat Perintah Tugas (SPT)");
                jQuery('#modalTambahData').modal('show');
                jQuery('#wrap-loading').hide();
            },
            error: function() {
                jQuery('#wrap-loading').hide();
            }
        });
    }

    function edit_sppd(id, isDetail) {
        let disabled = false;
        if (isDetail) {
            disabled = true;
        }

        jQuery('#wrap-loading').show();
        jQuery.ajax({
            method: 'post',
            url: ajax.url,
            dataType: 'JSON',
            data: {
                'action': 'get_data_sppd_by_id',
                'api_key': ajax.api_key,
                'id': id,
            },
            success: function(res) {
                jQuery('#id_data_sppd').val(res.data.id);

                jQuery('#tanggalSppd').val(res.data.tgl_ttd_sppd).prop('disabled', disabled)
                jQuery('#nomorSppd').val(res.data.nomor_sppd).prop('disabled', disabled)
                jQuery('#namaPegawai').val('').prop('disabled', disabled)
                jQuery('#nipPegawai').val('').prop('disabled', disabled)
                jQuery('#golPegawai').val('').prop('disabled', disabled)
                jQuery('#tempatBerangkat').val(res.data.tempat_berangkat).prop('disabled', disabled)
                jQuery('#tanggalBerangkat').val(res.data.tgl_berangkat).prop('disabled', disabled)
                jQuery('#tempatTujuan').val(res.data.tempat_tujuan).prop('disabled', disabled)
                jQuery('#tanggalSampai').val(res.data.tgl_sampai).prop('disabled', disabled)
                jQuery('#tanggalKembali').val(res.data.tgl_kembali).prop('disabled', disabled)
                jQuery('#alatAngkut').val(res.data.alat_angkut).prop('disabled', disabled)
                jQuery('#keterangan').val(res.data.keterangan).prop('disabled', disabled)
                jQuery('#maksudSppd').val(res.data.maksud_sppd).prop('disabled', disabled)

                if (isDetail) {
                    jQuery(".submitBtnSppd").hide();
                    jQuery(".title-sppd").html("Detail Surat Perintah Perjalanan Dinas(SPPD)");
                } else {
                    jQuery(".submitBtnSppd").show().html("Perbarui");
                    jQuery(".title-sppd").html("Edit Surat Perintah Perjalanan Dinas(SPPD)");
                }

                jQuery('#modal_form_sppd').modal('show');
                jQuery('#wrap-loading').hide();
            },
            error: function() {
                jQuery('#wrap-loading').hide();
            }
        });
    }

    function showModalTambahSpt() {
        jQuery('#id_data_spt').val('');

        jQuery('#tanggalSpt').val('');
        jQuery('#nomorSpt').val('');
        jQuery('#dasarSpt').val('');
        jQuery('#tujuanSpt').val('');

        jQuery(".submitBtn").html("Simpan");
        jQuery(".modal-title").html("Tambah Surat Perintah Tugas (SPT)");
        jQuery('#modalTambahData').modal('show');
    }

    function show_modal_pegawai_spt_sppd(nomor_spt) {
        get_table_pegawai_spt_sppd(nomor_spt).then(() => {
            jQuery('#nomor_spt').val(nomor_spt)

            jQuery('#modal_tambah_pegawai').modal('show');
        }).catch((error) => {
            console.error('Error loading table:', error);
        });

    }

    function showModalTambahSppd() {
        jQuery('#id_data_sppd').val('');

        jQuery('#tanggalSppd').val('')
        jQuery('#nomorSppd').val('')
        jQuery('#namaPegawai').val('')
        jQuery('#nipPegawai').val('')
        jQuery('#golPegawai').val('')
        jQuery('#tempatBerangkat').val('')
        jQuery('#tanggalBerangkat').val('')
        jQuery('#tempatTujuan').val('')
        jQuery('#tanggalSampai').val('')
        jQuery('#tanggalKembali').val('')
        jQuery('#alatAngkut').val('')
        jQuery('#keterangan').val('')
        jQuery('#maksudSppd').val('')

        jQuery(".submitBtnSppd").html("Simpan");
        jQuery("#modalFormLabel").html("Tambah Pegawai SPT/SPPD");
        jQuery('#modal_form_sppd').modal('show');
    }

    function submitDataSppd() {
        const validationRules = {
            'tanggalSppd': 'Data Tanggal SPPD tidak boleh kosong!',
            'nomorSppd': 'Data Nomor SPPD tidak boleh kosong!',
            'namaPegawai': 'Data Nama Pegawai tidak boleh kosong!',
            'nipPegawai': 'Data NIP Pegawai tidak boleh kosong!',
            'golPegawai': 'Data Golongan Pegawai tidak boleh kosong!',
            'tempatBerangkat': 'Data Tempat Berangkat tidak boleh kosong!',
            'tanggalBerangkat': 'Data Tanggal Berangkat tidak boleh kosong!',
            'tempatTujuan': 'Data Tempat Tujuan tidak boleh kosong!',
            'tanggalSampai': 'Data Tanggal Sampai tidak boleh kosong!',
            'tanggalKembali': 'Data Tanggal Kembali tidak boleh kosong!',
            'alatAngkut': 'Data Alat Angkut tidak boleh kosong!',
            'keterangan': 'Data Keterangan Lain tidak boleh kosong!',
            'maksudSppd': 'Data Maksud Perjalanan Dinas tidak boleh kosong!',
            // Tambahkan field lain jika diperlukan
        };

        const {
            error,
            data
        } = validateForm(validationRules);
        if (error) {
            return alert(error);
        }

        const id_data_sppd = jQuery('#id_data_sppd').val();
        const nomor_spt = jQuery('#nomor_spt').val();

        const tempData = new FormData();
        tempData.append('action', 'tambah_data_sppd');
        tempData.append('api_key', ajax.api_key);
        tempData.append('id_data_sppd', id_data_sppd);
        tempData.append('nomor_spt', nomor_spt);
        tempData.append('tahun_anggaran', tahun_anggaran);

        for (const [key, value] of Object.entries(data)) {
            tempData.append(key, value);
        }

        jQuery('#wrap-loading').show();

        jQuery.ajax({
            method: 'post',
            url: ajax.url,
            dataType: 'json',
            data: tempData,
            processData: false,
            contentType: false,
            cache: false,
            success: function(res) {
                alert(res.message);
                jQuery('#wrap-loading').hide();
                if (res.status === 'success') {
                    get_table_pegawai_spt_sppd(nomor_spt).then(() => {
                        jQuery('#modal_form_sppd').modal('hide');
                    })
                }
            }
        });
    }

    function submitDataSpt() {
        const validationRules = {
            'tanggalSpt': 'Data Tanggal SPT tidak boleh kosong!',
            'nomorSpt': 'Data Nomor SPT tidak boleh kosong!',
            'dasarSpt': 'Data Dasar SPT tidak boleh kosong!',
            'tujuanSpt': 'Data Tujuan SPT tidak boleh kosong!',
            // Tambahkan field lain jika diperlukan
        };

        const {
            error,
            data
        } = validateForm(validationRules);
        if (error) {
            return alert(error);
        }

        const id_data_spt = jQuery('#id_data_spt').val();

        const tempData = new FormData();
        tempData.append('action', 'tambah_data_spt');
        tempData.append('api_key', ajax.api_key);
        tempData.append('id_data_spt', id_data_spt);
        tempData.append('tahun_anggaran', tahun_anggaran);

        for (const [key, value] of Object.entries(data)) {
            tempData.append(key, value);
        }

        jQuery('#wrap-loading').show();

        jQuery.ajax({
            method: 'post',
            url: ajax.url,
            dataType: 'json',
            data: tempData,
            processData: false,
            contentType: false,
            cache: false,
            success: function(res) {
                alert(res.message);
                jQuery('#wrap-loading').hide();
                if (res.status === 'success') {
                    jQuery('#modalTambahData').modal('hide');
                    getDataTable();
                }
            }
        });
    }

    function get_table_pegawai_spt_sppd(nomor_spt) {
        return new Promise((resolve, reject) => {
            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: ajax.url,
                type: 'POST',
                data: {
                    action: 'get_table_pegawai_spt_sppd',
                    api_key: ajax.api_key,
                    nomor_spt: nomor_spt,
                },
                dataType: 'json',
                success: function(response) {
                    jQuery('#wrap-loading').hide();
                    console.log(response);
                    if (response.status === 'success') {
                        jQuery('#modal_tambah_pegawai tbody').html(response.data);
                        resolve()
                    } else {
                        alert(response.message);
                        reject(response.message)
                    }
                },
                error: function(xhr, status, error) {
                    jQuery('#wrap-loading').hide();
                    console.error(xhr.responseText);
                    alert('Terjadi kesalahan saat memuat tabel!');
                    reject(xhr.responseText);
                }
            });
        });
    }

    function cetak_spt(url) {
        window.open(url, '_blank');
    }

    function cetak_sppd(url) {
        window.open(url, '_blank');
    }
</script>