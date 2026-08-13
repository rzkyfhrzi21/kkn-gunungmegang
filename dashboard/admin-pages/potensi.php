<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$potensiData = include dirname(__DIR__, 2) . '/includes/potensi.php';
$idmOptions = ['Sangat Tertinggal', 'Tertinggal', 'Berkembang', 'Maju', 'Mandiri'];
?>
<div class="page-heading">
    <h3>Potensi &amp; IDM</h3>
    <p class="text-subtitle text-muted">Potensi lahan, status IDM, dan mata pencaharian — disimpan ke <code>includes/potensi.php</code></p>
</div>

<section class="section">
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-tree text-success"></i>
            <h6 class="mb-0">Potensi Lahan &amp; Status IDM</h6>
        </div>
        <div class="card-body">
            <form id="form-potensi" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tumpang Sari (Ha)</label>
                    <input type="number" min="0" class="form-control" name="tumpang_sari" value="<?= (int)$potensiData['tumpang_sari'] ?>" required>
                    <div class="app-upload-hint">Kopi, Lada, Cengkeh</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sawah (Ha)</label>
                    <input type="number" min="0" class="form-control" name="sawah" value="<?= (int)$potensiData['sawah'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jagung (Ha)</label>
                    <input type="number" min="0" class="form-control" name="jagung" value="<?= (int)$potensiData['jagung'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status IDM</label>
                    <select class="form-select" name="idm_status" required>
                        <?php foreach ($idmOptions as $opt): ?>
                        <option value="<?= $opt ?>" <?= $potensiData['idm_status'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="btn-save-potensi">
                        <i class="bi bi-check-lg"></i> Simpan Potensi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-briefcase text-primary"></i>
                <h6 class="mb-0">Mata Pencaharian</h6>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="btn-add-mp">
                <i class="bi bi-plus-lg"></i> Tambah
            </button>
        </div>
        <div class="card-body">
            <div class="app-table-wrap">
                <div class="app-table-toolbar mb-3">
                    <input type="text" id="search-mp" class="form-control form-control-sm" placeholder="Cari mata pencaharian...">
                </div>
                <table class="table table-hover" id="tbl-mp">
                    <thead>
                        <tr>
                            <th class="w-25">#</th>
                            <th>Nama Mata Pencaharian</th>
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
</section>

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
<div class="modal fade" id="modal-delete-mp" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash me-1"></i>Hapus Mata Pencaharian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-delete-mp-text">Apakah Anda yakin ingin menghapus data ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete-mp">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var formPot = document.getElementById('form-potensi');
    var btnPot = document.getElementById('btn-save-potensi');

    formPot.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!formPot.checkValidity()) { formPot.reportValidity(); return; }
        btnPot.disabled = true;
        var payload = {
            tumpang_sari: formPot.tumpang_sari.value, sawah: formPot.sawah.value,
            jagung: formPot.jagung.value, idm_status: formPot.idm_status.value
        };
        App.postJSON('../admin/api.php', { action: 'save', module: 'potensi', data: payload })
            .then(function (res) {
                btnPot.disabled = false;
                if (res.ok) App.toast('Data potensi berhasil disimpan.', 'success', 'Berhasil');
                else App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
            })
            .catch(function () { btnPot.disabled = false; App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });

    /* ---------- Tabel mata pencaharian ---------- */
    var modalMp = document.getElementById('modal-mp');
    var formMp = document.getElementById('form-mp');
    var pendingDelete = null;

    var table = new App.JsonTable({
        selector: '#tbl-mp',
        module: 'mata_pencaharian',
        perPage: 10,
        search: '#search-mp',
        columns: [
            { key: 'index', label: '#', format: function (row, v) { return v + 1; } },
            { key: 'nama', label: 'Nama Mata Pencaharian' }
        ],
        actions: ['edit', 'delete'],
        onEdit: function (row, btn) {
            document.getElementById('modal-mp-title').textContent = 'Edit Mata Pencaharian';
            formMp.index.value = row.key;
            formMp.nama.value = row.nama;
            App.showModal(modalMp);
        },
        onDelete: function (row, btn) {
            pendingDelete = row.key;
            document.getElementById('modal-delete-mp-text').textContent =
                'Apakah Anda yakin ingin menghapus "' + row.nama + '" dari daftar mata pencaharian?';
            App.showModal(document.getElementById('modal-delete-mp'));
        }
    });

    document.getElementById('btn-add-mp').addEventListener('click', function () {
        document.getElementById('modal-mp-title').textContent = 'Tambah Mata Pencaharian';
        formMp.reset();
        App.showModal(modalMp);
    });

    formMp.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!formMp.checkValidity()) { formMp.reportValidity(); return; }
        App.postJSON('../admin/api.php', {
            action: 'save_row', module: 'mata_pencaharian',
            data: { nama: formMp.nama.value, index: formMp.index.value }
        }).then(function (res) {
            if (res.ok) {
                App.hideModal(modalMp);
                App.toast('Mata pencaharian berhasil disimpan.', 'success', 'Berhasil');
                table.reload();
            } else {
                App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
            }
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });

    document.getElementById('btn-confirm-delete-mp').addEventListener('click', function () {
        App.postJSON('../admin/api.php', {
            action: 'delete', module: 'mata_pencaharian', data: { index: pendingDelete }
        }).then(function (res) {
            if (res.ok) {
                App.hideModal(document.getElementById('modal-delete-mp'));
                App.toast('Mata pencaharian berhasil dihapus.', 'success', 'Berhasil');
                table.reload();
            } else {
                App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menghapus.', 'error', 'Gagal');
            }
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });
});
</script>