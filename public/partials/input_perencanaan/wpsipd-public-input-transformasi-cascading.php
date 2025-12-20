<?php
// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}
global $wpdb;

if (!$_GET['id_jadwal'] || !$_GET['id_skpd']) {
    die('<h1 class="text-center">Parameter id_jadwal atau id_skpd tidak ditemukan!</h1>');
}

$jadwal_renstra_lokal = $wpdb->get_row(
    $wpdb->prepare('
		SELECT *
		FROM data_jadwal_lokal
		WHERE id_jadwal_lokal = %d
		 AND status !=2
	', $_GET['id_jadwal']),
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
date_default_timezone_set('Asia/Jakarta');
$timezone = get_option('timezone_string');
$is_jadwal_expired = $this->check_jadwal_is_expired($jadwal_renstra_lokal)
?>
<style>
    /* Warna Mild untuk Level Cascading */
    .bg-level-1 {
        background-color: #EAF6EA !important;
    }

    .bg-level-2 {
        background-color: #F8EFE4 !important;
    }

    .bg-level-3 {
        background-color: #EEF6F9 !important;
    }

    .bg-level-4 {
        background-color: #FBFBEA !important;
    }

    .bg-level-5 {
        background-color: #FFFFFF !important;
    }

    .table-renstra td {
        vertical-align: top;
        font-size: 0.9rem;
    }

    .hr-mild {
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        margin: 5px 0;
    }

    .table-renstra tr th,
    .table-renstra tr td {
        border: 1px solid black;
    }

    .table-scroll {
        max-height: 500px;
        overflow-y: auto;
        overflow-x: auto;
        position: relative;
    }

    .table-scroll table {
        margin-bottom: 0;
    }

    .table-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        text-align: center;
        vertical-align: middle;
        background: #D3D3D3;
    }
</style>
<div style="padding : 10px">
    <h1 class="text-center" style="margin-top : 80px">TRANSFORMASI CASCADING <br><?php echo $nama_jadwal; ?><br> <?php echo $data_unit['nama_skpd']; ?></h1>

    <div id="container-unmapped-renstra" class="card shadow-sm mt-4" style="display:none;">
        <div class="card-header">
            <h5 class="mb-0 text-center">Data Renstra Belum Masuk Transformasi Cascading</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="table-renstra mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 25%;">Pohon Kinerja</th>
                            <th style="width: 10%;">Tipe Cascading</th>
                            <th style="width: 10%;">Kode</th>
                            <th>Nomenklatur Renstra</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-unmapped-renstra">
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light text-danger small font-italic">
                * Data di atas adalah master Renstra yang belum dipilih/dipetakan ke dalam tabel Transformasi Cascading. Mohon segera lengkapi.
            </div>
        </div>
    </div>

    <?php if ($is_jadwal_expired == false) : ?>
        <div class="text-center m-2">
            <button class="btn btn-primary" onclick="handleAdd()"><span class="dashicons dashicons-plus"></span> Tambah Data</button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 text-center">Tabel Transformasi Cascading</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="table-renstra mb-0">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Pohon Kinerja</th>
                            <th style="width: 10%;">Tipe</th>
                            <th style="width: 25%;">Uraian Cascading</th>
                            <th style="width: 15%;">Indikator</th>
                            <th style="width: 15%;">Satuan</th>
                            <th style="width: 20%;">Nomenklatur (Renstra)</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-cascading-full">
                        <tr>
                            <td colspan=6" class="text-center p-5"><i class="fa fa-spinner fa-spin"></i> Memuat Data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <section class="mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="fa fa-info-circle mr-1 text-primary"></i>
                    Panduan Transformasi Cascading Renstra
                </h6>
            </div>

            <div class="card-body small text-dark">

                <ul class="pl-3 mb-3">
                    <li class="mb-2">
                        <strong>Sumber Data Renstra</strong><br>
                        Data Transformasi Cascading disusun berdasarkan data Renstra yang telah diinput sebelumnya
                        melalui menu <em>Input Perencanaan Renstra</em>.
                    </li>

                    <li class="mb-2">
                        <strong>Level Input Transformasi Cascading</strong><br>
                        Data Transformasi Cascading diinput mulai dari
                        <span class="text-muted">
                            Level 3 (Program) sampai Level 5 (Sub Kegiatan)
                        </span>.
                        Level Tujuan dan Sasaran digunakan sebagai dasar navigasi dan tidak memiliki input langsung.
                    </li>

                    <li class="mb-2">
                        <strong>Perubahan Data Renstra</strong><br>
                        Apabila data Renstra yang sudah digunakan dalam Transformasi Cascading dihapus
                        melalui menu <em>Input Perencanaan Renstra</em>, maka data terkait di Transformasi Cascading
                        akan tetap ditampilkan dengan <span class="badge badge-danger">Dihapus</span>
                        sebagai penanda ketidaksesuaian data dan perlu segera ditindaklanjuti.
                    </li>

                    <li class="mb-2">
                        <strong>Pohon Kinerja (Read Only)</strong><br>
                        Data Pohon Kinerja yang ditampilkan merupakan hasil keterkaitan langsung dengan data Renstra
                        dan <strong>tidak dapat diubah</strong> melalui fitur ini.
                        Perubahan hanya dapat dilakukan melalui menu <em>Input Perencanaan Renstra</em>.
                    </li>

                    <li class="mb-2">
                        <strong>Data Renstra Belum Masuk Transformasi Cascading</strong><br>
                        Tabel ini menampilkan data Renstra yang belum dipetakan ke dalam Transformasi Cascading.
                        <span class="text-danger font-weight-bold">
                            Mohon segera dilengkapi.
                        </span>
                        Tabel akan otomatis hilang apabila seluruh data Renstra telah masuk ke Transformasi Cascading.
                    </li>
                </ul>

                <div class="alert alert-info mb-0 py-2 small">
                    <i class="fa fa-info-circle mr-1"></i>
                    Pastikan seluruh data Renstra telah disusun dengan benar sebelum melakukan Transformasi Cascading.
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modal-monev" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bgpanel-theme">
                    <h4 class="modal-title">Data Transformasi Cascading</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="info-navigasi" class="alert alert-light border mb-3">
                    <table class="table-renstra mb-0 table-sm" id="table-navigation">
                        <thead class="bg-dark text-light">
                            <tr>
                                <th class="text-center" style="width: 120px;">Tipe</th>
                                <th class="text-center" style="width: 350px;">Pohon Kinerja</th>
                                <th class="text-center">Uraian Cascading</th>
                                <th class="text-center" style="width: 350px;">RENSTRA</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
                        <button id="btn-add-new" class="btn btn-success btn-sm shadow-sm m-2" onclick="openFormModal()">
                            <i class="dashicons dashicons-plus"></i> Tambah Data Baru
                        </button>
                        <div id="content-table-wrapper">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-form-indikator" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h6 class="modal-title" id="title-form-indikator">Kelola Indikator</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-indikator">
                        <input type="hidden" id="input_ind_id" name="id">

                        <input type="hidden" id="input_ind_parent_id" name="id_uraian_cascading">

                        <div class="form-group">
                            <label class="font-weight-bold">Narasi Indikator <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="input_ind_narasi" name="indikator" rows="3" placeholder="Contoh: Jumlah dokumen..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">
                                Satuan Indikator <span class="text-danger">*</span>
                            </label>

                            <div id="wrap-satuan-list">
                                <div class="input-group mb-2 satuan-item">
                                    <input type="text"
                                        class="form-control input-satuan"
                                        placeholder="Contoh: Dokumen / Unit / Persen">
                                    <div class="input-group-append">
                                        <button class="btn btn-danger btn-remove-satuan" type="button">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button"
                                class="btn btn-sm btn-outline-primary mt-2"
                                onclick="addSatuanField()">
                                <i class="fa fa-plus"></i> Tambah Satuan
                            </button>

                            <small class="form-text text-muted">
                                Satu indikator dapat memiliki lebih dari satu satuan.
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-info btn-sm" onclick="submitIndikator()">
                        <i class="fa fa-save"></i> Simpan Indikator
                    </button>
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

                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Pohon Kinerja Terpilih</label>
                            <select class="form-control" id="pokin_list" name="pokin_list[]" multiple disabled>
                            </select>
                            <small class="form-text text-muted">Data Pohon Kinerja terkait dengan Data Renstra, Hanya dapat diubah di Input Renstra.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Referensi Renstra <span id="label-ref-level"></span> <span class="text-danger">*</span></label>
                            <select class="form-control" id="input_id_unik" name="id_unik[]" multiple required>
                            </select>
                            <small class="form-text text-muted">Cari dan pilih referensi (Bisa lebih dari satu).</small>
                        </div>

                        <hr>

                        <div class="form-group bg-light p-2 rounded border-left border-primary pelaksana-section" style="display: none;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="input_is_pelaksana" name="is_pelaksana" value="1">
                                <label class="custom-control-label font-weight-bold text-dark" for="input_is_pelaksana">Pelaksana / bukan ketua tim kerja.</label>
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
        window.idJadwal = '<?php echo $_GET['id_jadwal']; ?>';
        window.idUnit = '<?php echo $data_unit['id_unit']; ?>';
        jQuery('#pokin_list').select2({
            dropdownParent: jQuery('#modal-form-cascading'),
            multiple: true,
            placeholder: 'Pohon Kinerja',
            width: '100%',
            disabled: true
        });

        loadTabelFull();
        loadTabelUnmapped();

        jQuery('#input_id_unik').on('change', function() {
            const selectedIds = jQuery(this).val(); // array

            // reset pokin
            jQuery('#pokin_list').empty().trigger('change');

            if (!selectedIds || selectedIds.length === 0) {
                return;
            }

            jQuery('#wrap-loading').show();
            jQuery.ajax({
                url: ajax.url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'get_pokin_by_id_uniks',
                    api_key: ajax.api_key,
                    id_uniks: selectedIds.join(',')
                },
                success: function(res) {
                    if (!res.status) return;

                    const pokinSelect = jQuery('#pokin_list');

                    res.data.forEach(p => {
                        const option = new Option(
                            `[Lv. ${p.level}] ${p.label}`,
                            p.id_unik,
                            true,
                            true
                        );
                        pokinSelect.append(option);
                    });

                    pokinSelect.trigger('change');
                    jQuery('#wrap-loading').hide();
                }
            });
        });

        var dataHitungMundur = {
            'namaJadwal': '<?php echo ucwords($jadwal_renstra_lokal['nama'])  ?>',
            'mulaiJadwal': '<?php echo $jadwal_renstra_lokal['waktu_awal']  ?>',
            'selesaiJadwal': '<?php echo $jadwal_renstra_lokal['waktu_akhir']  ?>',
            'thisTimeZone': '<?php echo $timezone ?>'
        }

        penjadwalanHitungMundur(dataHitungMundur);

    });

    jQuery(document).on('click', '.btn-remove-satuan', function() {
        // minimal 1 satuan harus ada
        if (jQuery('.satuan-item').length > 1) {
            jQuery(this).closest('.satuan-item').remove();
        } else {
            alert('Minimal harus ada satu satuan.');
        }
    });


    // --- STATE MANAGEMENT ---
    const state = {
        currentLevel: 1,
        parentIds: {
            1: {
                parent_id: null,
                parent_cascading: null,
                tipe: 'Tujuan',
                pokin: null,
                uraian: null,
                renstra: null
            },
            2: {
                parent_id: null,
                parent_cascading: null,
                tipe: 'Sasaran',
                pokin: null,
                uraian: null,
                renstra: null
            },
            3: {
                parent_id: null,
                parent_cascading: null,
                tipe: 'Program',
                pokin: null,
                uraian: null,
                renstra: null
            },
            4: {
                parent_id: null,
                parent_cascading: null,
                tipe: 'Kegiatan',
                pokin: null,
                uraian: null,
                renstra: null
            }
        }
    };

    function addSatuanField(value = '') {
        let html = `
        <div class="input-group mb-2 satuan-item">
            <input type="text" 
                   class="form-control input-satuan" 
                   value="${value}"
                   placeholder="Contoh: Dokumen / Unit / Persen">
            <div class="input-group-append">
                <button class="btn btn-danger btn-remove-satuan" type="button">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    `;
        jQuery('#wrap-satuan-list').append(html);
    }


    function loadTabelUnmapped() {
        const container = jQuery('#container-unmapped-renstra');
        const tbody = jQuery('#tbody-unmapped-renstra');

        jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: {
                action: "handle_get_unmapped_renstra",
                api_key: ajax.api_key,
                id_jadwal: window.idJadwal,
                id_unit: window.idUnit
            },
            success: function(res) {
                if (res.status) {
                    if (res.has_data) {
                        // Jika ada data yang belum dimapping, TAMPILKAN
                        tbody.html(res.html);
                        container.slideDown();
                    } else {
                        // Jika semua sudah bersih (mapped), SEMBUNYIKAN
                        container.slideUp();
                        tbody.empty();
                    }
                }
            }
        });
    }

    function loadTabelFull() {
        const tbody = jQuery('#tbody-cascading-full');

        // Loading State
        if (tbody.children().length === 0 || tbody.find('.fa-spin').length > 0) {
            tbody.html('<tr><td colspan=6" class="text-center p-5"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br>Sedang memuat data...</td></tr>');
        }

        jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: {
                action: "handle_get_view_tabel_cascading",
                api_key: ajax.api_key,
                id_jadwal: window.idJadwal,
                id_skpd: window.idUnit
            },
            success: function(res) {
                if (res.status) {
                    tbody.html(res.html).hide().fadeIn(300);
                } else {
                    tbody.html('<tr><td colspan=6" class="text-center text-danger">Gagal memuat data: ' + res.message + '</td></tr>');
                }
            },
            error: function() {
                tbody.html('<tr><td colspan=6" class="text-center text-danger">Terjadi kesalahan koneksi server.</td></tr>');
            }
        });
    }

    // ============================================================
    //  NAVIGATION & VIEW CONTROLLER
    // ============================================================

    function handleAdd() {
        jQuery('#modal-monev').modal('show');
        switchLevel(1); // Reset ke level 1
    }

    function switchLevel(level) {
        resetChildLevels(level);

        state.currentLevel = level;

        jQuery('.nav-link').removeClass('active');
        jQuery(`#nav-lvl${level}-tab`).addClass('active');

        updateHeaderUI();
        loadTableData(level);
    }

    function updateHeaderUI() {
        renderNavigationTable();

        // Tombol tambah data
        if (state.currentLevel >= 3) {
            jQuery('#btn-add-new').show();
        } else {
            jQuery('#btn-add-new').hide();
        }
    }

    function handleDrillDown(currentLvl, payload) {
        const lvlState = state.parentIds[currentLvl];

        resetChildLevels(currentLvl);

        lvlState.parent_id = payload.id ?? null;
        lvlState.parent_cascading = payload.idsString ?? null;

        lvlState.pokin = Array.isArray(payload.pokin) ? payload.pokin : [];
        lvlState.uraian = payload.uraian ?
            decodeURIComponent(payload.uraian) :
            '-';

        lvlState.renstra = payload.renstra ?? '-';

        const nextLvl = currentLvl + 1;

        jQuery(`#nav-lvl${nextLvl}-tab`).removeClass('disabled');
        switchLevel(nextLvl);
    }

    function resetChildLevels(fromLevel) {
        Object.keys(state.parentIds).forEach(lvl => {
            lvl = parseInt(lvl);
            if (lvl > fromLevel) {
                state.parentIds[lvl] = {
                    ...state.parentIds[lvl],
                    parent_id: null,
                    parent_cascading: null,
                    pokin: null,
                    uraian: null,
                    renstra: null
                };

                // disable tab child
                jQuery(`#nav-lvl${lvl}-tab`).addClass('disabled');
            }
        });
    }

    function renderNavigationTable() {
        let rows = '';

        Object.keys(state.parentIds).forEach(lvl => {
            const data = state.parentIds[lvl];

            if (!data || !data.parent_id) return;

            rows += `
            <tr>
                <td class="font-weight-bold text-nowrap text-dark">
                    ${data.tipe}
                </td>
                <td class="text-dark">
                    ${renderPokinList(data.pokin)}
                </td>
            `;
            if (lvl > 2) {
                rows += `
                    <td class="text-dark">
                        ${data.uraian ?? '-'}
                    </td>
                    <td class="text-dark">
                        ${
                            data.renstra && data.renstra.length > 0
                                ? renderCascading(data.renstra)
                                : '-'
                        }
                    </td>
                </tr>`;
            } else {
                rows += `
                    <td class="text-dark" colspan="2">
                        ${data.uraian ?? '-'}
                    </td>
                </tr>`;
            }

        });

        jQuery('#table-navigation tbody').html(
            rows || `
            <tr>
                <td colspan="4" class="text-center text-muted font-italic">
                    Belum ada navigasi yang dipilih
                </td>
            </tr>
        `
        );
    }

    // ============================================================
    //  DATA LOADING & RENDERING (READ)
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

    function renderPokinList(pokinList) {
        let html = `<ul class="list-unstyled mt-0 mb-0">`;

        if (!Array.isArray(pokinList) || pokinList.length === 0) {
            html += `<li><i>-</i></li>`;
            html += `</ul>`;
            return html;
        }

        // ===== CASE 1: LEVEL 1 & 2 (array of object)
        if (pokinList.length > 0 && !Array.isArray(pokinList[0])) {
            pokinList.forEach(p => {
                html += `<li class="mb-1">[Lv. ${p.level}] ${p.label}</li>`;
            });

            html += `</ul>`;
            return html;
        }

        // ===== CASE 2: LEVEL 3+ (array of array)
        pokinList.forEach((group, groupIndex) => {

            if (groupIndex > 0) {
                html += `
                <li class="list-unstyled">
                    <hr class="my-1">
                </li>
            `;
            }

            if (Array.isArray(group)) {
                group.forEach(p => {
                    html += `<li class="mb-1">[Lv. ${p.level}] ${p.label}</li>`;
                });
            }
        });

        html += `</ul>`;
        return html;
    }


    const levelCascading = {
        1: 'Tujuan',
        2: 'Sasaran',
        3: 'Program',
        4: 'Kegiatan',
        5: 'Sub Kegiatan'
    }

    function renderTable(level, data) {
        let html = `
        <div class="table-scroll">
            <table class="table table-hover table-bordered mb-0">
        `;

        // ================= HEADER =================
        if (level <= 2) {
            html += `
            <thead class="thead-dark">
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="25%" class="text-center">Pohon Kinerja</th>
                    <th class="text-center">${levelCascading[level]}</th>
                    <th width="15%" class="text-center">Aksi</th>
                </tr>
            </thead>`;
        } else {
            html += `
            <thead class="thead-dark">
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="25%" class="text-center">Pohon Kinerja</th>
                    <th class="text-center">Uraian Cascading</th>
                    <th width="25%" class="text-center">${levelCascading[level]}</th>
                    <th width="15%" class="text-center">Aksi</th>
                </tr>
            </thead>`;
        }

        html += `<tbody>`;

        // ================= DATA KOSONG =================
        if (!data || data.length === 0) {
            html += `
            <tr>
                <td colspan=6" class="text-center text-muted p-4">
                    Data kosong.
                </td>
            </tr>`;
        } else {

            data.forEach((item, idx) => {

                // =====================================================
                // LEVEL 1 & 2 (filter MODE)
                // =====================================================
                if (level <= 2) {
                    let label = level == 1 ? item.tujuan_teks : item.sasaran_teks;
                    let labelDrillDown = level == 1 ? "Sasaran" : "Program";
                    let id = item.id_unik;

                    html += `
                    <tr>
                        <td class="text-center">${idx + 1}</td>
                        <td class="p-1">${renderPokinList(item.pokin_list)}</td>
                        <td class="font-weight-bold">${label}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info shadow-sm"
                                onclick='handleDrillDown(${level}, {
                                id: "${id}",
                                pokin: ${JSON.stringify(item.pokin_list)},
                                uraian: "${encodeURIComponent(label)}",
                                renstra: "-"
                            })'>
                                Lihat ${labelDrillDown}
                                <i class="dashicons dashicons-arrow-right-alt2"></i>
                            </button>
                        </td>
                    </tr>`;
                }

                // =====================================================
                // LEVEL 3, 4, 5 (CRUD MODE)
                // =====================================================
                else {

                    let uraian = item.uraian_cascading;
                    let isPelaksana = item.is_pelaksana == 1 ?
                        '<span class="badge badge-warning ml-2 p-1">Pelaksana</span>' :
                        '';

                    // ---------- renstra ----------
                    let refIds = [];
                    let listRef = `<ul class="list-unstyled mt-0 mb-0">`;

                    if (item.referensi && item.referensi.length > 0) {
                        item.referensi.forEach(r => {
                            refIds.push(r.id_unik);
                            listRef += `<li class="mb-1">${r.nama_ref}</li>`;
                        });
                    } else {
                        listRef += `<li><i class="text-danger">Tidak ada ${levelCascading[level]}</i></li>`;
                    }
                    listRef += `</ul>`;

                    let listPokin = renderPokinList(item.pohon_kinerja);

                    let idsString = refIds.join(',');

                    // ---------- AKSI ----------
                    let btns = `<div class="btn-group btn-group-sm">`;
                    btns += `
                    <button class="btn btn-warning" title="Edit"
                        onclick="openFormModal(${item.id})">
                        <i class="dashicons dashicons-edit"></i>
                    </button>`;
                    btns += `
                    <button class="btn btn-danger" title="Hapus"
                        onclick="deleteData(${item.id})">
                        <i class="dashicons dashicons-trash"></i>
                    </button>`;
                    btns += `
                    <button class="btn btn-info" title="Tambah Indikator"
                        onclick="openFormAddIndikator(${item.id})">
                        <i class="dashicons dashicons-plus"></i>
                    </button>`;

                    if (level < 5) {
                        btns += `
                        <button class="btn btn-primary" title="Lihat Level Berikutnya"
                            onclick='handleDrillDown(${level}, {
                                id: "${item.id}",
                                idsString: "${idsString}",
                                pokin: ${JSON.stringify(item.pohon_kinerja)},
                                uraian: "${encodeURIComponent(uraian)}",
                                renstra: ${JSON.stringify(item.referensi)}
                            })'>
                            <i class="dashicons dashicons-arrow-right-alt2"></i>
                        </button>`;
                    }
                    btns += `</div>`;

                    // ---------- BARIS PARENT ----------
                    html += `
                    <tr class="table-secondary">
                        <td class="text-center">${idx + 1}</td>
                        <td class="p-1">${listPokin}</td>
                        <td>
                            <div class="font-weight-bold text-dark">${uraian}${isPelaksana}</div>
                        </td>
                        <td class="p-1">${listRef}</td>
                        <td class="text-center">${btns}</td>
                    </tr>`;

                    // ---------- BARIS INDIKATOR ----------
                    let idxInd = 1;
                    if (item.indikator && item.indikator.length > 0) {
                        item.indikator.forEach(ind => {

                            let safeInd = encodeURIComponent(ind.indikator);

                            // Render list satuan
                            let satuanHtml = '';
                            if (ind.satuan_list && ind.satuan_list.length > 0) {
                                satuanHtml += '<ul class="m-0 pl-3">';
                                ind.satuan_list.forEach(s => {
                                    satuanHtml += `<li>${s.satuan}</li>`;
                                });
                                satuanHtml += '</ul>';
                            } else {
                                satuanHtml = '<span class="text-muted font-italic">Belum ada satuan</span>';
                            }

                            let satuanJson = encodeURIComponent(JSON.stringify(ind.satuan_list ?? []));

                            html += `
                            <tr class="table-light table-sm text-muted">
                                <td class="align-middle">
                                    <span class="font-weight-semibold pl-2 m-2">${idx + 1}.${idxInd ++}</span>
                                </td>
                                <td class="align-middle" colspan="2">
                                    <span class="font-weight-semibold">${ind.indikator}</span>
                                </td>

                                <td class="align-middle">
                                    ${satuanHtml}
                                </td>

                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-warning"
                                            title="Edit Indikator"
                                            onclick="openFormAddIndikator(${item.id}, ${ind.id})">
                                            <i class="dashicons dashicons-edit"></i>
                                        </button>
                                        <button class="btn btn-danger"
                                            title="Hapus Indikator"
                                            onclick="deleteIndikator(${ind.id})">
                                            <i class="dashicons dashicons-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
                        });
                    } else {
                        html += `
                        <tr class="table-light">
                            <td colspan="3" class="text-muted font-italic">
                                <i class="fa fa-info-circle mr-1"></i>
                                Belum ada indikator
                            </td>
                        </tr>`;
                    }

                }
            });
        }

        html += `
            </tbody>
            </table>
        </div>`;

        jQuery('#content-table-wrapper').html(html);
    }

    function renderCascading(cascadingList) {
        let html = `<ul class="list-unstyled text-muted mt-0 mb-0">`;

        if (Array.isArray(cascadingList) && cascadingList.length > 0) {
            cascadingList.forEach(r => {
                html += `<li class="mb-1">${r.nama_ref}</li>`;
            });
        } else {
            html += `<li><i class="text-muted">-</i></li>`;
        }

        html += `</ul>`;
        return html;
    }



    // ============================================================
    //  FORM HANDLING (MODAL KEDUA - INPUT/EDIT)
    // ============================================================

    async function openFormModal(editId = null) {
        try {
            //  Reset Form & UI
            jQuery('#form-cascading')[0].reset();
            jQuery('#indikator-wrapper').empty();
            jQuery('#input_id_unik').val(null).trigger('change');

            const labelMap = {
                3: 'Program',
                4: 'Kegiatan',
                5: 'Sub Kegiatan'
            };
            jQuery('#label-ref-level').text(`(${labelMap[state.currentLevel]})`);

            //  Parent cascading
            let parentCascading = state.parentIds[state.currentLevel - 1]?.parent_id ?? null;

            if (state.currentLevel === 4 || state.currentLevel === 5) {
                parentCascading = state.parentIds[state.currentLevel - 1]?.parent_cascading ?? null;
            }

            jQuery('.pelaksana-section').toggle(state.currentLevel === 5);

            jQuery("#wrap-loading").show();

            await initSelect2Modal(state.currentLevel, parentCascading);

            if (editId) {
                // ===== EDIT MODE =====
                jQuery('#form-modal-title').text('Edit Data Cascading');
                jQuery('#input_action_type').val('edit');
                jQuery('#input_id').val(editId);

                const res = await jQuery.ajax({
                    url: ajax.url,
                    type: "post",
                    dataType: 'JSON',
                    data: {
                        action: "handle_get_detail_transformasi_cascading",
                        api_key: ajax.api_key,
                        id: editId
                    }
                });

                if (!res.status) throw new Error(res.message);

                const d = res.data;
                jQuery('#input_uraian').val(d.main.uraian_cascading);

                if (state.currentLevel === 5) {
                    jQuery('#input_is_pelaksana').prop('checked', d.main.is_pelaksana == 1);
                }

                jQuery('#input_id_unik').val(d.id_unik).trigger('change');
            } else {
                // ===== CREATE MODE =====
                jQuery('#form-modal-title').text('Tambah Data Baru');
                jQuery('#input_action_type').val('create');
            }

            jQuery('#modal-form-cascading').modal('show');
        } catch (err) {
            console.error(err);
            alert(err.message || 'Terjadi kesalahan.');
        } finally {
            jQuery("#wrap-loading").hide();
        }
    }

    function openFormAddIndikator(parentId, indId = null) {

        // Reset form
        jQuery('#form-indikator')[0].reset();
        jQuery('#wrap-satuan-list').html('');
        jQuery('#input_ind_parent_id').val(parentId);
        jQuery('#input_ind_id').val('');

        if (!indId) {
            // ==========================
            // MODE TAMBAH
            // ==========================
            jQuery('#title-form-indikator').text('Tambah Indikator Baru');
            addSatuanField();
            jQuery('#modal-form-indikator').modal('show');
            return;
        }

        // ==========================
        // MODE EDIT
        // ==========================
        jQuery('#title-form-indikator').text('Edit Indikator');
        jQuery("#wrap-loading").show();

        jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: "json",
            data: {
                action: 'handle_get_indikator_detail',
                api_key: ajax.api_key,
                id: indId
            },
            success(res) {
                jQuery("#wrap-loading").hide();

                if (!res.status) {
                    alert(res.message);
                    return;
                }

                // Set data indikator
                jQuery('#input_ind_id').val(res.data.id);
                jQuery('#input_ind_narasi').val(res.data.indikator);

                // Render satuan
                if (res.data.satuan_list.length) {
                    res.data.satuan_list.forEach(s => {
                        addSatuanField(s.satuan, s.id);
                    });
                } else {
                    addSatuanField();
                }

                jQuery('#modal-form-indikator').modal('show');
            },
            error() {
                jQuery("#wrap-loading").hide();
                alert('Gagal mengambil data indikator');
            }
        });
    }

    function submitIndikator() {

        let narasi = jQuery('#input_ind_narasi').val().trim();
        if (!narasi) {
            alert('Narasi indikator wajib diisi!');
            return;
        }

        let satuanArr = [];
        jQuery('.input-satuan').each(function() {
            let val = jQuery(this).val().trim();
            if (val) satuanArr.push(val);
        });

        if (satuanArr.length === 0) {
            alert('Minimal satu satuan indikator harus diisi!');
            return;
        }

        jQuery("#wrap-loading").show();

        jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: {
                action: 'handle_save_indikator',
                api_key: ajax.api_key,
                id: jQuery('#input_ind_id').val(),
                id_uraian_cascading: jQuery('#input_ind_parent_id').val(),
                indikator: narasi,
                satuan: satuanArr,
                id_jadwal: idJadwal
            },
            beforeSend: function() {
                jQuery('#modal-form-indikator button').prop('disabled', true);
            },
            success: function(res) {
                jQuery('#modal-form-indikator button').prop('disabled', false);
                jQuery("#wrap-loading").hide();

                if (res.status) {
                    jQuery('#modal-form-indikator').modal('hide');
                    alert(res.message);
                    loadTableData(state.currentLevel);
                    loadTabelFull();
                    loadTabelUnmapped();
                } else {
                    alert("Error: " + res.message);
                }
            },
            error: function() {
                jQuery("#wrap-loading").hide();
                jQuery('#modal-form-indikator button').prop('disabled', false);
                alert("Gagal koneksi server.");
            }
        });
    }


    function deleteIndikator(id) {
        if (!confirm("Yakin hapus indikator ini?")) return;

        jQuery("#wrap-loading").show();
        jQuery.ajax({
            url: ajax.url,
            type: "post",
            dataType: 'JSON',
            data: {
                action: 'handle_delete_indikator',
                api_key: ajax.api_key,
                id_jadwal: idJadwal,
                id: id
            },
            success: function(res) {
                if (res.status) {
                    alert(res.message);
                    loadTableData(state.currentLevel); // Refresh tabel
                    loadTabelFull();
                    loadTabelUnmapped();
                } else {
                    alert(res.message);
                }
                jQuery("#wrap-loading").hide();
            }
        });
    }

    async function initSelect2Modal(level, parentId) {
        const $select = jQuery('#input_id_unik');

        let ajaxSourceFunc;
        if (level === 3) ajaxSourceFunc = get_program_renstra_lokal_by_parent;
        else if (level === 4) ajaxSourceFunc = get_kegiatan_renstra_lokal_by_parent;
        else if (level === 5) ajaxSourceFunc = get_subkegiatan_renstra_lokal_by_parent;
        else return;

        if ($select.data('select2')) {
            $select.select2('destroy');
        }

        $select.empty();

        const res = await ajaxSourceFunc(parentId);

        if (res.status && Array.isArray(res.data)) {
            res.data.forEach(item => {
                const text =
                    item.nama_program ||
                    item.nama_giat ||
                    item.nama_sub_giat ||
                    '-';

                $select.append(
                    `<option value="${item.id_unik}">${text}</option>`
                );
            });
        }

        $select.select2({
            dropdownParent: jQuery('#modal-form-cascading'),
            placeholder: "Cari...",
            allowClear: true,
            multiple: true,
            width: '100%'
        });
    }


    function submitForm() {
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
        };

        if (!formObj.uraian_cascading) {
            alert("Uraian wajib diisi!");
            return;
        }
        if (!formObj.id_unik || formObj.id_unik.length === 0) {
            alert("Pilih minimal satu Renstra!");
            return;
        }

        // Kirim
        jQuery("#wrap-loading").show();
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
                    loadTabelFull();
                    loadTabelUnmapped();
                } else {
                    alert("Gagal: " + res.message);
                }
                jQuery("#wrap-loading").hide();
            },
            error: function() {
                jQuery("#wrap-loading").hide();
                jQuery('#modal-form-cascading button').prop('disabled', false);
                alert("Terjadi kesalahan jaringan.");
            }
        });
    }

    function deleteData(id) {
        if (confirm("Yakin hapus data ini?")) {
            jQuery("#wrap-loading").show();
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
                        alert(res.message);
                        loadTableData(state.currentLevel);
                        loadTabelFull();
                        loadTabelUnmapped();
                    } else {
                        alert(res.message);
                    }
                    jQuery("#wrap-loading").hide();
                }
            });
        }
    }

    // ============================================================
    // AJAX WRAPPERS
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