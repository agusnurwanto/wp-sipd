<?php
// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}
global $wpdb;

$input = shortcode_atts(array(
    'id_jadwal' => '',
), $atts);

if (!$input['id_jadwal'] || !$_GET['id_skpd']) {
    die('<h1 class="text-center">Parameter id_jadwal atau id_skpd tidak ditemukan!</h1>');
}

$jadwal_renstra_lokal = $wpdb->get_row(
    $wpdb->prepare('
		SELECT *
		FROM data_jadwal_lokal
		WHERE id_jadwal_lokal = %d
		 AND status !=2
	', $input['id_jadwal']),
    ARRAY_A
);

if (!$jadwal_renstra_lokal) {
    die('<h1 class="text-center">Jadwal Renstra Lokal tidak ditemukan!</h1>');
}
$nama_jadwal = $jadwal_renstra_lokal['nama'] . ' ' . '(' . $jadwal_renstra_lokal['tahun_anggaran'] . ' - ' . ($jadwal_renstra_lokal['tahun_anggaran'] + $jadwal_renstra_lokal['lama_pelaksanaan'] - 1) . ')';

$data_unit = $wpdb->get_row(
    $wpdb->prepare('
        SELECT *
        FROM data_unit
        WHERE id_skpd = %d
         AND tahun_anggaran = %d
         AND active = 1
    ', $_GET['id_skpd'], $jadwal_renstra_lokal['tahun_anggaran']),
    ARRAY_A
);

if (!$data_unit) {
    die('<h1 class="text-center">Data Unit tidak ditemukan!</h1>');
}
?>
<style>
    .table-renstra {
        border: 0;
        table-layout: fixed;
    }

    .table-renstra thead {
        position: sticky;
        text-align: center;
        vertical-align: middle;
        background: #D3D3D3;
    }

    .table-renstra tr th,
    .table-renstra tr td {
        border: 1px solid black;
    }
</style>
<div style="padding : 5px">
    <h1 class="text-center">TRANSFORMASI CASCADING <br><?php echo $nama_jadwal; ?><br> <?php echo $data_unit['nama_skpd']; ?></h1>

    <h3 class="text-center">Data Renstra yang belum masuk Transformasi Cascading</h3>
    <table class="table-renstra">
        <thead>
            <tr>
                <th>No</th>
                <th>Pohon Kinerja</th>
                <th>Tipe Cascading</th>
                <th>Kode</th>
                <th>Nomenklatur</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="text-center m-2">
        <button class="btn btn-primary" onclick="handleAdd()"><span class="dashicons dashicons-plus"></span> Tambah Data</button>
    </div>

    <h3 class="text-center">Tabel Transformasi Cascading</h3>
    <table class="table-renstra">
        <thead>
            <tr>
                <th>Pohon Kinerja</th>
                <th>Tipe Cascading</th>
                <th>Uraian Cascading</th>
                <th>Indikator</th>
                <th>Satuan</th>
                <th>Nomenklatur</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="modal fade" id="modal-monev" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bgpanel-theme">
                    <h4 class="modal-title">Data Transformasi Cascading</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="info-navigasi" class="alert alert-light border mb-3" style="display:none; background-color: #f8f9fa;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold" style="font-size: 10px; letter-spacing: 1px;">Posisi Saat Ini:</small>
                            <div id="breadcrumb-text" class="text-primary font-weight-bold" style="font-size: 1.1em;"></div>
                        </div>
                        <button id="btn-add-new" class="btn btn-success btn-sm shadow-sm" onclick="openFormModal()">
                            <i class="fa fa-plus-circle"></i> Tambah Data Baru
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-lvl1-tab" onclick="switchLevel(1)" href="#">Tujuan</a>
                            <a class="nav-item nav-link disabled" id="nav-lvl2-tab" onclick="switchLevel(2)" href="#">Sasaran</a>
                            <a class="nav-item nav-link disabled" id="nav-lvl3-tab" onclick="switchLevel(3)" href="#">Program</a>
                            <a class="nav-item nav-link disabled" id="nav-lvl4-tab" onclick="switchLevel(4)" href="#">Kegiatan</a>
                            <a class="nav-item nav-link disabled" id="nav-lvl5-tab" onclick="switchLevel(5)" href="#">Sub Kegiatan</a>
                        </div>
                    </nav>

                    <div class="tab-content pt-3">
                        <div id="content-table-wrapper"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-form-cascading" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="form-modal-title">Form Input</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-cascading">
                        <input type="hidden" id="input_id" name="id">
                        <input type="hidden" id="input_action_type" name="action_type" value="create">

                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Uraian Cascading <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="input_uraian" name="uraian_cascading" rows="3" placeholder="Contoh: Terlaksananya kegiatan..." required style="min-height: 100px;"></textarea>
                        </div>

                        <div class="form-group bg-light p-2 rounded border-left border-primary">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="input_is_pelaksana" name="is_pelaksana" value="1">
                                <label class="custom-control-label font-weight-bold text-dark" for="input_is_pelaksana">Pelaksana / bukan ketua tim kerja.</label>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Referensi Renstra <span id="label-ref-level"></span> <span class="text-danger">*</span></label>
                            <select class="form-control select2-modal" id="input_id_unik" name="id_unik[]" multiple="multiple" style="width: 100%;" required>
                            </select>
                            <small class="form-text text-muted">Cari dan pilih referensi (Bisa lebih dari satu).</small>
                        </div>

                        <div class="form-group mt-4">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <label class="font-weight-bold text-dark mb-0">Indikator & Satuan</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addIndikatorRow()">
                                    <i class="fa fa-plus"></i> Tambah Indikator
                                </button>
                            </div>

                            <div class="table-responsive border rounded">
                                <table class="table table-sm table-borderless mb-0 bg-white">
                                    <thead class="bg-light border-bottom">
                                        <tr>
                                            <th class="pl-3">Narasi Indikator</th>
                                            <th style="width: 120px;">Satuan</th>
                                            <th style="width: 40px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="indikator-wrapper">
                                    </tbody>
                                </table>
                            </div>
                            <div id="empty-indikator-msg" class="text-center text-muted p-3 bg-light" style="display:none; font-style:italic;">
                                Belum ada indikator ditambahkan.
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitForm()">
                        <i class="fa fa-save"></i> Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(() => {
        window.idJadwal = '<?php echo $input['id_jadwal']; ?>';
        window.idUnit = '<?php echo $data_unit['id_unit']; ?>';
    });

    // --- STATE MANAGEMENT ---
    const state = {
        currentLevel: 1,
        breadcrumbs: {}, 
        parentIds: {
            1: { parent_id: null, parent_cascading: null },
            2: { parent_id: null, parent_cascading: null },
            3: { parent_id: null, parent_cascading: null },
            4: { parent_id: null, parent_cascading: null }
        }
    };

    // ============================================================
    // 1. NAVIGATION & VIEW CONTROLLER
    // ============================================================

    function handleAdd() {
        jQuery('#modal-monev').modal('show');
        switchLevel(1); // Reset ke level 1
    }

    function switchLevel(level) {
        state.currentLevel = level;

        // A. Update UI Tab (Active/Disabled state)
        jQuery('.nav-link').removeClass('active');
        jQuery(`#nav-lvl${level}-tab`).addClass('active').removeClass('disabled');

        // B. Update Header (Breadcrumb & Tombol Tambah)
        updateHeaderUI();

        // C. Load Data Tabel
        loadTableData(level);
    }

    function updateHeaderUI() {
        // Render Breadcrumbs
        let texts = [];
        for (let i = 1; i < state.currentLevel; i++) {
            if (state.breadcrumbs[i]) texts.push(state.breadcrumbs[i]);
        }

        let html = texts.length > 0 ?
            texts.join(' <i class="fa fa-chevron-right mx-1 text-muted"></i> ') :
            '<span class="text-muted font-italic">Silakan Pilih Level 1</span>';

        jQuery('#breadcrumb-text').html(html);
        jQuery('#info-navigasi').show();

        // Logika Tombol "Tambah Data Baru"
        // Hanya muncul di Level 3 (Program), 4 (Kegiatan), 5 (Sub-Kegiatan)
        if (state.currentLevel >= 3) {
            jQuery('#btn-add-new').show();
        } else {
            jQuery('#btn-add-new').hide();
        }
    }

    function handleDrillDown(currentLvl, id, labelEnc, idsString = null) {
        if (!state.parentIds[currentLvl]) state.parentIds[currentLvl] = {};

        state.breadcrumbs[currentLvl] = labelEnc;
        
        state.parentIds[currentLvl]['parent_id'] = id;         
        state.parentIds[currentLvl]['parent_cascading'] = idsString; 
        console.log(state.parentIds);

        // 2. Pindah ke Level Berikutnya
        let nextLvl = currentLvl + 1;

        // Enable tab tujuan
        jQuery(`#nav-lvl${nextLvl}-tab`).removeClass('disabled');
        switchLevel(nextLvl);
    }

    // ============================================================
    // 2. DATA LOADING & RENDERING (READ)
    // ============================================================

    function loadTableData(level) {
        const container = jQuery('#content-table-wrapper');
        container.html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-3x text-muted"></i><br><span class="text-muted mt-2">Memuat Data...</span></div>');

        // Tentukan Parent ID (Level 1 tidak butuh parentId)
        let parentId = (level === 1) ? null : state.parentIds[level - 1]['parent_id'];

        let ajaxCall;

        // KASUS A: LEVEL 1 & 2 (DATA MASTER RENSTRA - HANYA FILTER)
        if (level === 1) {
            ajaxCall = get_tujuan_renstra_lokal_by_id_jadwal(window.idJadwal, window.idUnit);
        } else if (level === 2) {
            ajaxCall = get_sasaran_renstra_lokal_by_parent(parentId);
        }
        // KASUS B: LEVEL 3, 4, 5 (DATA TRANSFORMASI CASCADING - CRUD)
        else {
            // parentId = state.parentIds[level - 1]['parent_cascading'];
            ajaxCall = get_transformasi_data(level, parentId);
        }

        // --- HANDLE RESPONSE ---
        ajaxCall.done((res) => {
            if (res.status) {
                renderTable(level, res.data);
            } else {
                let msg = level <= 2 ? "Data Renstra tidak ditemukan." : "Belum ada data Cascading. Silakan tambah baru.";
                container.html(`<div class="alert alert-light border text-center m-4 text-muted font-italic">${msg}</div>`);
            }
        }).fail(() => {
            container.html(`<div class="alert alert-danger text-center m-4">Gagal mengambil data dari server.</div>`);
        });
    }

    function renderTable(level, data) {
        let html = `<div class="table-responsive"><table class="table table-hover table-bordered table-striped custom-table mb-0">`;

        // HEADER TABLE (BEDA ANTARA FILTER DAN CRUD)
        if (level <= 2) {
            html += `
            <thead class="bg-light">
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th>Nomenklatur Renstra ${level===1 ? 'Tujuan' : 'Sasaran'}</th>
                    <th width="15%" class="text-center">Aksi</th>
                </tr>
            </thead>`;
        } else {
            html += `
            <thead class="thead-dark">
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th>Uraian Cascading</th>
                    <th width="30%">Referensi Renstra</th>
                    <th width="15%" class="text-center">Aksi</th>
                </tr>
            </thead>`;
        }

        html += `<tbody>`;

        if (data.length === 0) {
            html += `<tr><td colspan="4" class="text-center text-muted p-4">Data kosong.</td></tr>`;
        } else {
            data.forEach((item, idx) => {
                let tr = '';

                // --- RENDER BARIS LEVEL 1 & 2 (VIEW MODE) ---
                if (level <= 2) {
                    let label = level == 1 ? item.tujuan_teks : item.sasaran_teks;
                    let labelDrillDown = level == 1 ? "Sasaran" : "Program";
                    let id = item.id_unik;

                    tr = `
                    <tr>
                        <td class="text-center">${idx+1}</td>
                        <td>${label}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info shadow-sm" onclick="handleDrillDown(${level}, '${id}', '${label}')">
                                Lihat ${labelDrillDown} <i class="fa fa-arrow-right"></i>
                            </button>
                        </td>
                    </tr>`;
                }
                // --- RENDER BARIS LEVEL 3, 4, 5 (CRUD MODE) ---
                else {
                    let uraian = item.uraian_cascading;
                    let isPelaksana = item.is_pelaksana == 1 ? '<span class="badge badge-warning ml-2">Pelaksana</span>' : '';

                    // Format List Referensi
                    let refIds = [];
                    let listRef = '<ul class="pl-3 mb-0 small text-muted">';
                    if (item.referensi && item.referensi.length > 0) {
                        item.referensi.forEach(r => {
                            refIds.push(r.id_unik);
                            listRef += `<li>${r.nama_ref}</li>`;
                        });
                    } else {
                        listRef += '<li><i class="text-danger">Tidak ada referensi</i></li>';
                    }
                    listRef += '</ul>';

                    let idsString = refIds.join(',');

                    // Tombol Aksi
                    let btns = `<div class="btn-group btn-group-sm">`;
                    btns += `<button class="btn btn-warning" title="Edit" onclick="openFormModal(${item.id})"><i class="fa fa-edit"></i></button>`;
                    btns += `<button class="btn btn-danger" title="Hapus" onclick="deleteData(${item.id})"><i class="fa fa-trash"></i></button>`;

                    // Tombol Lihat Anak (Hanya di Level 3 & 4)
                    if (level < 5) {
                        btns += `<button class="btn btn-primary" title="Lihat Level Berikutnya" onclick="handleDrillDown(${level}, '${item.id}', '${uraian}', '${idsString}')"><i class="fa fa-arrow-right"></i></button>`;
                    }
                    btns += `</div>`;

                    tr = `<tr>
                        <td class="text-center">${idx+1}</td>
                        <td><span class="font-weight-bold text-dark">${uraian}</span>${isPelaksana}</td>
                        <td>${listRef}</td>
                        <td class="text-center">${btns}</td>
                    </tr>`;
                }
                html += tr;
            });
        }
        html += `</tbody></table></div>`;
        jQuery('#content-table-wrapper').html(html);
    }

    // ============================================================
    // 3. FORM HANDLING (MODAL KEDUA - INPUT/EDIT)
    // ============================================================

    function openFormModal(editId = null) {
        // 1. Reset Form & UI
        jQuery('#form-cascading')[0].reset();
        jQuery('#indikator-wrapper').empty();
        jQuery('#input_id_unik').val(null).trigger('change');

        // Update Label UI
        let labelMap = {
            3: 'Program',
            4: 'Kegiatan',
            5: 'Sub-Kegiatan'
        };
        jQuery('#label-ref-level').text(`(${labelMap[state.currentLevel]})`);

        // 2. Init Select2 (Mengambil Data Master Renstra sebagai Pilihan)
        // Parent ID diambil dari state level sebelumnya
        let parentCascading = state.parentIds[state.currentLevel - 1]['parent_id'];
        if (state.currentLevel == 4 || state.currentLevel == 5) {
            parentCascading = state.parentIds[state.currentLevel - 1]['parent_cascading'];
        }
        initSelect2Modal(state.currentLevel, parentCascading);

        if (editId) {
            // --- MODE EDIT ---
            jQuery('#form-modal-title').text('Edit Data Cascading');
            jQuery('#input_action_type').val('edit');
            jQuery('#input_id').val(editId);

            // Fetch Detail Data
            jQuery.ajax({
                url: ajax.url,
                type: "post",
                dataType: 'JSON',
                data: {
                    action: "handle_get_detail_transformasi_cascading",
                    api_key: ajax.api_key,
                    id: editId
                },
                success: function(res) {
                    if (res.status) {
                        let d = res.data;
                        jQuery('#input_uraian').val(d.main.uraian_cascading);
                        jQuery('#input_is_pelaksana').prop('checked', d.main.is_pelaksana == 1);

                        // Set Select2 (Array ID Unik)
                        jQuery('#input_id_unik').val(d.id_unik).trigger('change');

                        // Render Indikator
                        if (d.indikator && d.indikator.length > 0) {
                            d.indikator.forEach(ind => addIndikatorRow(ind.indikator, ind.satuan));
                        } else {
                            addIndikatorRow();
                        }

                        // Tampilkan Modal Kedua (Modal Form)
                        jQuery('#modal-form-cascading').modal('show');
                    } else {
                        alert(res.message);
                    }
                }
            });

        } else {
            // --- MODE CREATE ---
            jQuery('#form-modal-title').text('Tambah Data Baru');
            jQuery('#input_action_type').val('create');
            addIndikatorRow(); // Baris indikator default
            jQuery('#modal-form-cascading').modal('show');
        }
    }

    function initSelect2Modal(level, parentId) {
        let $select = jQuery('#input_id_unik');

        // Logic Source Data Select2:
        // Jika input di Level 3 (Program Cascading), maka ambil sumber dari Program Renstra (Anak dari Sasaran Renstra)
        let ajaxSourceFunc;
        if (level === 3) ajaxSourceFunc = get_program_renstra_lokal_by_parent(parentId);
        else if (level === 4) ajaxSourceFunc = get_kegiatan_renstra_lokal_by_parent(parentId);
        else if (level === 5) ajaxSourceFunc = get_subkegiatan_renstra_lokal_by_parent(parentId);

        // Hancurkan instance lama untuk refresh
        if ($select.hasClass("select2-hidden-accessible")) {
            $select.select2('destroy');
        }

        ajaxSourceFunc.done(function(res) {
            $select.empty();
            if (res.status && res.data.length > 0) {
                let opts = '';
                res.data.forEach(item => {
                    let txt = item.nama_program || item.nama_giat || item.nama_sub_giat; // Sesuaikan nama kolom
                    let id = item.id_unik;
                    opts += `<option value="${id}">${txt}</option>`;
                });
                $select.append(opts);
            }

            // Init Select2 dengan dropdownParent agar tidak tertutup modal
            $select.select2({
                dropdownParent: jQuery('#modal-form-cascading'),
                theme: 'bootstrap4',
                placeholder: "Cari & Pilih Referensi...",
                allowClear: true,
                width: '100%'
            });
        });
    }

    function submitForm() {
        // 1. Koleksi Data Indikator Custom
        let indicators = [];
        jQuery('#indikator-wrapper tr').each(function() {
            let ind = jQuery(this).find('textarea').val();
            let sat = jQuery(this).find('input[type="text"]').val();
            if (ind && sat) indicators.push({
                indikator: ind,
                satuan: sat
            });
        });

        // 2. Persiapan Object Data
        let formObj = {
            action: 'handle_save_transformasi_cascading',
            api_key: ajax.api_key,
            action_type: jQuery('#input_action_type').val(),
            id: jQuery('#input_id').val(),
            id_jadwal: window.idJadwal,
            id_skpd: window.idUnit,
            parent_id: state.parentIds[state.currentLevel - 1]['parent_id'],
            level: state.currentLevel,
            uraian_cascading: jQuery('#input_uraian').val(),
            is_pelaksana: jQuery('#input_is_pelaksana').is(':checked') ? 1 : 0,
            id_unik: jQuery('#input_id_unik').val(), // Array dari Select2
            indikator_data: indicators
        };

        // 3. Validasi
        if (!formObj.uraian_cascading) {
            alert("Uraian wajib diisi!");
            return;
        }
        if (!formObj.id_unik || formObj.id_unik.length === 0) {
            alert("Pilih minimal satu Renstra!");
            return;
        }
        if (indicators.length === 0) {
            alert("Isi minimal satu indikator!");
            return;
        }

        // 4. Kirim
        jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: formObj,
            beforeSend: function() {
                jQuery('#modal-form-cascading button').prop('disabled', true);
            },
            success: function(res) {
                jQuery('#modal-form-cascading button').prop('disabled', false);
                if (res.status) {
                    alert(res.message);
                    jQuery('#modal-form-cascading').modal('hide');
                    loadTableData(state.currentLevel); // Refresh Tabel Utama
                } else {
                    alert("Gagal: " + res.message);
                }
            },
            error: function() {
                jQuery('#modal-form-cascading button').prop('disabled', false);
                alert("Terjadi kesalahan jaringan.");
            }
        });
    }

    function deleteData(id) {
        if (confirm("Yakin hapus data ini? Data anak & indikator terkait akan ikut terhapus.")) {
            jQuery.ajax({
                url: ajax.url,
                type: "post",
                dataType: 'JSON',
                data: {
                    action: 'handle_delete_transformasi_cascading',
                    api_key: ajax.api_key,
                    id: id
                },
                success: function(res) {
                    if (res.status) {
                        loadTableData(state.currentLevel);
                    } else {
                        alert(res.message);
                    }
                }
            });
        }
    }

    // --- HELPER UI INDIKATOR ---
    function addIndikatorRow(valInd = '', valSat = '') {
        let rowId = Date.now() + Math.floor(Math.random() * 1000);
        let html = `
        <tr id="row-ind-${rowId}">
            <td class="pl-3"><textarea class="form-control form-control-sm" rows="2" placeholder="Uraian..." required>${valInd}</textarea></td>
            <td><input type="text" class="form-control form-control-sm" placeholder="Satuan" value="${valSat}" required></td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="jQuery('#row-ind-${rowId}').remove(); checkIndikatorEmpty();"><i class="fa fa-times"></i></button>
            </td>
        </tr>`;
        jQuery('#indikator-wrapper').append(html);
        checkIndikatorEmpty();
    }

    function checkIndikatorEmpty() {
        if (jQuery('#indikator-wrapper tr').length === 0) jQuery('#empty-indikator-msg').show();
        else jQuery('#empty-indikator-msg').hide();
    }

    // ============================================================
    // 4. AJAX WRAPPERS
    // ============================================================

    function get_tujuan_renstra_lokal_by_id_jadwal(idJadwal, idUnit) {
        return jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: {
                action: "handle_add_tranformasi_cascading_by_level",
                api_key: ajax.api_key,
                id_unit: idUnit,
                id_jadwal: idJadwal,
                level: 1
            }
        });
    }

    function get_sasaran_renstra_lokal_by_parent(kodeTujuan) {
        return jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: {
                action: "handle_add_tranformasi_cascading_by_level",
                api_key: ajax.api_key,
                id_unik: kodeTujuan,
                level: 2
            }
        });
    }

    function get_program_renstra_lokal_by_parent(kodeSasaran) {
        return jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: {
                action: "handle_add_tranformasi_cascading_by_level",
                api_key: ajax.api_key,
                id_unik: kodeSasaran,
                level: 3
            }
        });
    }

    function get_kegiatan_renstra_lokal_by_parent(kodeProgram) {
        return jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: {
                action: "handle_add_tranformasi_cascading_by_level",
                api_key: ajax.api_key,
                id_unik: kodeProgram,
                level: 4
            }
        });
    }

    function get_subkegiatan_renstra_lokal_by_parent(kodeKegiatan) {
        return jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: {
                action: "handle_add_tranformasi_cascading_by_level",
                api_key: ajax.api_key,
                id_unik: kodeKegiatan,
                level: 5
            }
        });
    }

    function get_transformasi_data(level, parentId) {
        return jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: {
                action: "handle_get_transformasi_list",
                api_key: ajax.api_key,
                level: level,
                parent_id: parentId
            }
        });
    }
</script>