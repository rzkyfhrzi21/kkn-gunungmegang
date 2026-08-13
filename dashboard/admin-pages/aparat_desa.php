<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;
?>
<div class="page-heading">
    <h3>Aparat &amp; Lembaga</h3>
    <p class="text-subtitle text-muted">Daftar perangkat pekon, BHP, dan LPM — disimpan ke <code>includes/perangkat.php</code></p>
</div>

<section class="section">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-building text-secondary"></i>
                <h6 class="mb-0">Daftar Aparat &amp; Lembaga</h6>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="btn-add-aparat">
                <i class="bi bi-plus-lg"></i> Tambah
            </button>
        </div>
        <div class="card-body">
            <div class="app-table-wrap">
                <div class="app-table-toolbar mb-3">
                    <input type="text" id="search-aparat" class="form-control form-control-sm" placeholder="Cari nama / jabatan...">
                    <select id="filter-jabatan" class="form-select form-select-sm"></select>
                    <select id="filter-jenis" class="form-select form-select-sm"></select>
                </div>
                <table class="table table-hover" id="tbl-aparat">
                    <thead>
                        <tr>
                            <th class="w-25">#</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th class="text-end" style="width:140px">Aksi</th>
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

<!-- Modal tambah/edit -->
<div class="modal fade" id="modal-aparat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-aparat">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-aparat-title">Tambah Aparat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="index" value="">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" placeholder="contoh: HARSON, BBA" required>
                    <label class="form-label mt-3">Jabatan</label>
                    <input type="text" class="form-control" name="jabatan" placeholder="contoh: Kepala Pekon" list="daftar-jabatan" required>
                    <datalist id="daftar-jabatan">
                        <option value="Kepala Pekon">
                        <option value="Juru Tulis (Sekretaris Pekon)">
                        <option value="Kasih Pemerintahan">
                        <option value="Kasih Pelayanan">
                        <option value="Kasih Kesejahteraan">
                        <option value="Kaur Tata Usaha &amp; Umum">
                        <option value="Kaur Keuangan">
                        <option value="Kaur Perencanaan">
                        <option value="Ketua BHP">
                        <option value="Ketua LPM">
                    </datalist>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal detail -->
<div class="modal fade" id="modal-detail-aparat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-badge me-1"></i>Detail Aparat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless mb-0">
                    <tbody id="detail-aparat-body"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal konfirmasi hapus -->
<div class="modal fade" id="modal-delete-aparat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash me-1"></i>Hapus Aparat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-delete-aparat-text">Apakah Anda yakin ingin menghapus data ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete-aparat">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalAparat = document.getElementById('modal-aparat');
    var formAparat = document.getElementById('form-aparat');
    var pendingDelete = null;

    var table = new App.JsonTable({
        selector: '#tbl-aparat',
        module: 'perangkat',
        perPage: 10,
        search: '#search-aparat',
        filters: [
            { key: 'jabatan', label: 'Semua Jabatan', select: '#filter-jabatan' },
            { key: 'jenis', label: 'Semua Jenis', select: '#filter-jenis' }
        ],
        columns: [
            { key: 'no', label: '#', format: function (row, v) { return row.idx + 1; } },
            { key: 'nama', label: 'Nama' },
            { key: 'jabatan', label: 'Jabatan', type: 'badge' }
        ],
        actions: ['detail', 'edit', 'delete'],
        onDetail: function (row) {
            var body = document.getElementById('detail-aparat-body');
            body.innerHTML =
                '<tr><th style="width:120px" class="text-muted">Nama</th><td class="fw-bold">' + App.esc(row.nama) + '</td></tr>' +
                '<tr><th class="text-muted">Jabatan</th><td>' + App.esc(row.jabatan) + '</td></tr>';
            App.showModal(document.getElementById('modal-detail-aparat'));
        },
        onEdit: function (row) {
            document.getElementById('modal-aparat-title').textContent = 'Edit Aparat';
            formAparat.index.value = row.idx;
            formAparat.nama.value = row.nama;
            formAparat.jabatan.value = row.jabatan;
            App.showModal(modalAparat);
        },
        onDelete: function (row) {
            pendingDelete = row.idx;
            document.getElementById('modal-delete-aparat-text').textContent =
                'Apakah Anda yakin ingin menghapus "' + row.nama + '" dari daftar aparat?';
            App.showModal(document.getElementById('modal-delete-aparat'));
        }
    });

    document.getElementById('btn-add-aparat').addEventListener('click', function () {
        document.getElementById('modal-aparat-title').textContent = 'Tambah Aparat';
        formAparat.reset();
        App.showModal(modalAparat);
    });

    formAparat.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!formAparat.checkValidity()) { formAparat.reportValidity(); return; }
        App.postJSON('../admin/api.php', {
            action: 'save_row', module: 'perangkat',
            data: { nama: formAparat.nama.value, jabatan: formAparat.jabatan.value, index: formAparat.index.value }
        }).then(function (res) {
            if (res.ok) {
                App.hideModal(modalAparat);
                App.toast('Aparat berhasil disimpan.', 'success', 'Berhasil');
                table.reload();
            } else {
                App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
            }
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });

    document.getElementById('btn-confirm-delete-aparat').addEventListener('click', function () {
        App.postJSON('../admin/api.php', {
            action: 'delete', module: 'perangkat', data: { index: pendingDelete }
        }).then(function (res) {
            if (res.ok) {
                App.hideModal(document.getElementById('modal-delete-aparat'));
                App.toast('Aparat berhasil dihapus.', 'success', 'Berhasil');
                table.reload();
            } else {
                App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menghapus.', 'error', 'Gagal');
            }
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });
});
</script>