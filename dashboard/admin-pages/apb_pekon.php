<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$apbData = include dirname(__DIR__, 2) . '/includes/apbpekon.php';
?>
<div class="page-heading">
    <h3>APB Pekon</h3>
    <p class="text-subtitle text-muted">Anggaran Pendapatan dan Belanja Pekon — disimpan ke <code>includes/apbpekon.php</code></p>
</div>

<section class="section">
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-wallet2 text-success"></i>
            <h6 class="mb-0">Tahun Anggaran</h6>
        </div>
        <div class="card-body">
            <form id="form-apb-tahun" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Tahun Anggaran</label>
                    <input type="number" min="2000" max="2100" class="form-control" name="tahun" value="<?= (int)$apbData['tahun'] ?>" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Tahun</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    $tables = [
        'pendapatan' => ['judul' => 'Pendapatan', 'ikon' => 'bi-trending-up text-primary'],
        'belanja'    => ['judul' => 'Belanja', 'ikon' => 'bi-bag text-danger'],
        'pembiayaan' => ['judul' => 'Pembiayaan', 'ikon' => 'bi-arrows-expand text-secondary'],
    ];
    foreach ($tables as $mod => $t):
    ?>
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi <?= $t['ikon'] ?>"></i>
            <h6 class="mb-0"><?= $t['judul'] ?></h6>
        </div>
        <div class="card-body">
            <div class="app-table-wrap">
                <table class="table table-hover" id="tbl-<?= $mod ?>">
                    <thead>
                        <tr>
                            <th>Pos</th>
                            <th class="text-right">Nominal</th>
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
    <?php endforeach; ?>
</section>

<!-- Modal edit nominal -->
<div class="modal fade" id="modal-apb" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-apb">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Nominal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="module" value="">
                    <input type="hidden" name="key" value="">
                    <label class="form-label" id="apb-label"></label>
                    <input type="number" step="0.01" min="0" class="form-control" name="nominal" required>
                    <div class="app-upload-hint">Total pendapatan, belanja, dan pembiayaan netto dihitung otomatis.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var formTahun = document.getElementById('form-apb-tahun');
    formTahun.addEventListener('submit', function (e) {
        e.preventDefault();
        App.postJSON('../admin/api.php', {
            action: 'save', module: 'apbpekon', data: { tahun: formTahun.tahun.value }
        }).then(function (res) {
            if (res.ok) App.toast('Tahun anggaran berhasil disimpan.', 'success', 'Berhasil');
            else App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });

    var modalApb = document.getElementById('modal-apb');
    var formApb = document.getElementById('form-apb');
    var apbTables = [];

    ['pendapatan', 'belanja', 'pembiayaan'].forEach(function (mod) {
        apbTables.push(new App.JsonTable({
            selector: '#tbl-' + mod,
            module: mod,
            perPage: 10,
            columns: [
                { key: 'label', label: 'Pos' },
                { key: 'nominal', label: 'Nominal', type: 'currency', align: 'text-right' }
            ],
            actions: ['edit'],
            onEdit: function (row) {
                formApb.module.value = mod;
                formApb.key.value = row.key;
                document.getElementById('apb-label').textContent = row.label;
                formApb.nominal.value = row.nominal;
                App.showModal(modalApb);
            }
        }));
    });

    formApb.addEventListener('submit', function (e) {
        e.preventDefault();
        App.postJSON('../admin/api.php', {
            action: 'save_row', module: formApb.module.value,
            data: { key: formApb.key.value, nominal: formApb.nominal.value }
        }).then(function (res) {
            if (res.ok) {
                App.hideModal(modalApb);
                App.toast('Nominal berhasil disimpan.', 'success', 'Berhasil');
                apbTables.forEach(function (t) { t.reload(); });
            } else {
                App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
            }
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });
});
</script>