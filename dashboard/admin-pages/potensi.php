<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$potensiData = include dirname(__DIR__, 2) . '/includes/potensi.php';
$idmOptions = ['Sangat Tertinggal', 'Tertinggal', 'Berkembang', 'Maju', 'Mandiri'];
$iconOptions = ['eco', 'grass', 'agriculture', 'storefront', 'local_florist', 'engineering', 'domain', 'landscape', 'forest', 'water_drop'];
?>
<div class="page-heading">
    <h3>Potensi &amp; IDM</h3>
    <p class="text-subtitle text-muted">Potensi lahan, status IDM, dan mata pencaharian</p>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="potensiTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-teks" data-bs-toggle="tab" data-bs-target="#pane-teks" type="button" role="tab" aria-controls="pane-teks" aria-selected="true">
                        <i class="bi bi-pencil-square me-1"></i> Teks &amp; IDM
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-komoditas" data-bs-toggle="tab" data-bs-target="#pane-komoditas" type="button" role="tab" aria-controls="pane-komoditas" aria-selected="false">
                        <i class="bi bi-box-seam me-1"></i> Komoditas Unggulan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-mp" data-bs-toggle="tab" data-bs-target="#pane-mp" type="button" role="tab" aria-controls="pane-mp" aria-selected="false">
                        <i class="bi bi-briefcase me-1"></i> Mata Pencaharian
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="potensiTabContent">

                <!-- ============ TAB: TEKS & IDM ============ -->
                <div class="tab-pane fade show active" id="pane-teks" role="tabpanel" aria-labelledby="tab-teks">
                    <form id="form-potensi" class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Deskripsi Hero (Potensi &amp; Ekonomi Lokal)</label>
                            <textarea class="form-control" name="hero_desc" rows="5" maxlength="600"><?= htmlspecialchars($potensiData['hero_desc'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi Section Komoditas Unggulan</label>
                            <textarea class="form-control" name="komoditas_desc" rows="5" maxlength="600"><?= htmlspecialchars($potensiData['komoditas_desc'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi Mata Pencaharian Utama</label>
                            <textarea class="form-control" name="mp_desc" rows="5" maxlength="600"><?= htmlspecialchars($potensiData['mp_desc'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status IDM</label>
                            <select class="form-select" name="idm_status" required>
                                <?php foreach ($idmOptions as $opt): ?>
                                    <option value="<?= $opt ?>" <?= ($potensiData['idm_status'] ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Progress IDM (%)</label>
                            <input type="number" min="0" max="100" class="form-control" name="idm_progress" value="<?= (int)($potensiData['idm_progress'] ?? 0) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Deskripsi IDM</label>
                            <textarea class="form-control" name="idm_desc" rows="5" maxlength="600"><?= htmlspecialchars($potensiData['idm_desc'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Judul Kehidupan Sosial</label>
                            <input type="text" class="form-control" name="sosial_judul" maxlength="200" value="<?= htmlspecialchars($potensiData['sosial_judul'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Paragraf Sosial 1</label>
                            <textarea class="form-control" name="sosial_par1" rows="5" maxlength="1000"><?= htmlspecialchars($potensiData['sosial_par1'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Paragraf Sosial 2</label>
                            <textarea class="form-control" name="sosial_par2" rows="5" maxlength="1000"><?= htmlspecialchars($potensiData['sosial_par2'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" id="btn-save-potensi">
                                <i class="bi bi-check-lg"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ============ TAB: KOMODITAS UNGGULAN ============ -->
                <div class="tab-pane fade" id="pane-komoditas" role="tabpanel" aria-labelledby="tab-komoditas">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam text-success"></i>
                            <h6 class="mb-0">Komoditas Unggulan</h6>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" id="btn-add-komoditas">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </div>
                    <div class="app-table-wrap">
                        <div class="app-table-toolbar mb-3">
                            <input type="text" id="search-komoditas" class="form-control form-control-sm" placeholder="Cari komoditas...">
                        </div>
                        <table class="table table-hover" id="tbl-komoditas">
                            <thead>
                                <tr>
                                    <th class="w-25">Nama Komoditas</th>
                                    <th>Luasan</th>
                                    <th class="text-end" style="width:120px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div class="app-pagination">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary app-pagination-prev" disabled>
                                    <i class="bi bi-chevron-left"></i> Prev
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary app-pagination-next">
                                    Next <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                            <div class="app-pagination-info"></div>
                        </div>
                    </div>
                </div>

                <!-- ============ TAB: MATA PENCAHARIAN ============ -->
                <div class="tab-pane fade" id="pane-mp" role="tabpanel" aria-labelledby="tab-mp">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-briefcase text-primary"></i>
                            <h6 class="mb-0">Mata Pencaharian</h6>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" id="btn-add-mp">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </div>
                    <div class="app-table-wrap">
                        <div class="app-table-toolbar mb-3">
                            <input type="text" id="search-mp" class="form-control form-control-sm" placeholder="Cari mata pencaharian...">
                        </div>
                        <table class="table table-hover" id="tbl-mp">
                            <thead>
                                <tr>
                                    <th class="w-25">Nama Mata Pencaharian</th>
                                    <th>Keterangan</th>
                                    <th class="text-end" style="width:120px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div class="app-pagination">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary app-pagination-prev" disabled>
                                    <i class="bi bi-chevron-left"></i> Prev
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary app-pagination-next">
                                    Next <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                            <div class="app-pagination-info"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Modal komoditas -->
<div class="modal fade" id="modal-komoditas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-komoditas">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-komoditas-title">Tambah Komoditas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="index" value="">
                    <label class="form-label">Nama Komoditas</label>
                    <input type="text" class="form-control" name="nama" placeholder="contoh: Kopi, Lada &amp; Cengkeh" required>
                    <label class="form-label mt-3">Deskripsi</label>
                    <textarea class="form-control" name="deskripsi" rows="5" maxlength="600"></textarea>
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <label class="form-label">Luasan (nilai)</label>
                            <input type="number" min="0" class="form-control" name="nilai">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Satuan</label>
                            <input type="text" class="form-control" name="satuan" placeholder="Hektar">
                        </div>
                    </div>
                    <label class="form-label mt-3">Ikon</label>
                    <select class="form-select" name="ikon">
                        <?php foreach ($iconOptions as $ic): ?>
                            <option value="<?= $ic ?>"><?= $ic ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-komoditas">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal tambah/edit mata pencaharian -->
<div class="modal fade" id="modal-mp" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-mp">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-mp-title">Tambah Mata Pencaharian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="index" value="">
                    <label class="form-label">Nama Mata Pencaharian</label>
                    <input type="text" class="form-control" name="nama" placeholder="contoh: Petani / Pekebun" required>
                    <label class="form-label mt-3">Keterangan</label>
                    <input type="text" class="form-control" name="keterangan" placeholder="contoh: Mayoritas">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-mp">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal konfirmasi hapus -->
<div class="modal fade" id="modal-delete-pot" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash me-1"></i>Hapus Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-delete-pot-text">Apakah Anda yakin ingin menghapus data ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete-pot">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var formPot = document.getElementById('form-potensi');
        var btnPot = document.getElementById('btn-save-potensi');

        formPot.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!formPot.checkValidity()) {
                formPot.reportValidity();
                return;
            }
            btnPot.disabled = true;
            var payload = {
                hero_desc: formPot.hero_desc.value,
                komoditas_desc: formPot.komoditas_desc.value,
                mp_desc: formPot.mp_desc.value,
                idm_status: formPot.idm_status.value,
                idm_progress: formPot.idm_progress.value,
                idm_desc: formPot.idm_desc.value,
                sosial_judul: formPot.sosial_judul.value,
                sosial_par1: formPot.sosial_par1.value,
                sosial_par2: formPot.sosial_par2.value
            };
            App.postJSON('../admin/api.php', {
                    action: 'save',
                    module: 'potensi',
                    data: payload
                })
                .then(function(res) {
                    btnPot.disabled = false;
                    if (res.ok) App.toast('Data potensi berhasil disimpan.', 'success', 'Berhasil');
                    else App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
                })
                .catch(function() {
                    btnPot.disabled = false;
                    App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
                });
        });

        /* ---------- Tabel komoditas ---------- */
        var modalKom = document.getElementById('modal-komoditas');
        var formKom = document.getElementById('form-komoditas');
        var pendingDelete = null;
        var pendingModule = null;

        var komTable = new App.JsonTable({
            selector: '#tbl-komoditas',
            module: 'komoditas',
            perPage: 10,
            search: '#search-komoditas',
            columns: [{
                    key: 'nama',
                    label: 'Nama Komoditas'
                },
                {
                    key: 'nilai',
                    label: 'Luasan',
                    format: function(row, v) {
                        return v + ' ' + (row.satuan || '');
                    }
                }
            ],
            actions: ['edit', 'delete'],
            onEdit: function(row, btn) {
                document.getElementById('modal-komoditas-title').textContent = 'Edit Komoditas';
                formKom.index.value = row.index;
                formKom.nama.value = row.nama || '';
                formKom.deskripsi.value = row.deskripsi || '';
                formKom.nilai.value = row.nilai || 0;
                formKom.satuan.value = row.satuan || '';
                formKom.ikon.value = row.ikon || 'eco';
                App.showModal(modalKom);
            },
            onDelete: function(row, btn) {
                pendingDelete = row.index;
                pendingModule = 'komoditas';
                document.getElementById('modal-delete-pot-text').textContent =
                    'Apakah Anda yakin ingin menghapus "' + row.nama + '" dari daftar komoditas?';
                App.showModal(document.getElementById('modal-delete-pot'));
            }
        });

        document.getElementById('btn-add-komoditas').addEventListener('click', function() {
            document.getElementById('modal-komoditas-title').textContent = 'Tambah Komoditas';
            formKom.reset();
            formKom.ikon.value = 'eco';
            App.showModal(modalKom);
        });

        formKom.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!formKom.checkValidity()) {
                formKom.reportValidity();
                return;
            }
            App.postJSON('../admin/api.php', {
                action: 'save_row',
                module: 'komoditas',
                data: {
                    index: formKom.index.value,
                    nama: formKom.nama.value,
                    deskripsi: formKom.deskripsi.value,
                    nilai: formKom.nilai.value,
                    satuan: formKom.satuan.value,
                    ikon: formKom.ikon.value
                }
            }).then(function(res) {
                if (res.ok) {
                    App.hideModal(modalKom);
                    App.toast('Komoditas berhasil disimpan.', 'success', 'Berhasil');
                    komTable.reload();
                } else {
                    App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
                }
            }).catch(function() {
                App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
            });
        });

        /* ---------- Tabel mata pencaharian ---------- */
        var modalMp = document.getElementById('modal-mp');
        var formMp = document.getElementById('form-mp');

        var table = new App.JsonTable({
            selector: '#tbl-mp',
            module: 'mata_pencaharian',
            perPage: 10,
            search: '#search-mp',
            columns: [{
                    key: 'nama',
                    label: 'Nama Mata Pencaharian'
                },
                {
                    key: 'keterangan',
                    label: 'Keterangan'
                }
            ],
            actions: ['edit', 'delete'],
            onEdit: function(row, btn) {
                document.getElementById('modal-mp-title').textContent = 'Edit Mata Pencaharian';
                formMp.index.value = row.index;
                formMp.nama.value = row.nama;
                formMp.keterangan.value = row.keterangan || '';
                App.showModal(modalMp);
            },
            onDelete: function(row, btn) {
                pendingDelete = row.index;
                pendingModule = 'mata_pencaharian';
                document.getElementById('modal-delete-pot-text').textContent =
                    'Apakah Anda yakin ingin menghapus "' + row.nama + '" dari daftar mata pencaharian?';
                App.showModal(document.getElementById('modal-delete-pot'));
            }
        });

        document.getElementById('btn-add-mp').addEventListener('click', function() {
            document.getElementById('modal-mp-title').textContent = 'Tambah Mata Pencaharian';
            formMp.reset();
            App.showModal(modalMp);
        });

        formMp.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!formMp.checkValidity()) {
                formMp.reportValidity();
                return;
            }
            App.postJSON('../admin/api.php', {
                action: 'save_row',
                module: 'mata_pencaharian',
                data: {
                    nama: formMp.nama.value,
                    keterangan: formMp.keterangan.value,
                    index: formMp.index.value
                }
            }).then(function(res) {
                if (res.ok) {
                    App.hideModal(modalMp);
                    App.toast('Mata pencaharian berhasil disimpan.', 'success', 'Berhasil');
                    table.reload();
                } else {
                    App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
                }
            }).catch(function() {
                App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
            });
        });

        document.getElementById('btn-confirm-delete-pot').addEventListener('click', function() {
            App.postJSON('../admin/api.php', {
                action: 'delete',
                module: pendingModule,
                data: {
                    index: pendingDelete
                }
            }).then(function(res) {
                if (res.ok) {
                    App.hideModal(document.getElementById('modal-delete-pot'));
                    App.toast('Data berhasil dihapus.', 'success', 'Berhasil');
                    if (pendingModule === 'komoditas') komTable.reload();
                    else table.reload();
                } else {
                    App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menghapus.', 'error', 'Gagal');
                }
            }).catch(function() {
                App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
            });
        });
    });
</script>