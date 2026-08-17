<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$subjekLabel = [
    'infrastruktur' => 'Infrastruktur',
    'pelayanan'     => 'Pelayanan',
    'keamanan'      => 'Keamanan',
    'lainnya'       => 'Lainnya',
];
?>
<div class="page-heading">
    <h3>Kotak Aspirasi</h3>
    <p class="text-subtitle text-muted">Laporan dan aspirasi publik yang masuk dari form Kontak. Hanya bisa dilihat dan dihapus — tidak dapat diedit.</p>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="mb-0"><i class="bi bi-envelope-open me-2 text-primary"></i>Daftar Laporan Masuk</h5>
                <span class="badge bg-secondary" id="asp-total-badge">0 laporan</span>
            </div>
        </div>
        <div class="card-body">
            <div class="app-table-toolbar mb-3">
                <input type="text" id="asp-search" class="form-control form-control-sm" placeholder="Cari nama / subjek / isi pesan...">
                <select id="asp-filter-subjek" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($subjekLabel as $val => $lab): ?>
                    <option value="<?= $val ?>"><?= $lab ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="app-table-wrap">
                <table class="table table-hover" id="tbl-aspirasi">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:52px">No</th>
                            <th>Nama</th>
                            <th style="width:130px">WhatsApp</th>
                            <th style="width:130px">Kategori</th>
                            <th>Pesan (ringkasan)</th>
                            <th style="width:150px">Tanggal</th>
                            <th class="text-end" style="width:110px">Aksi</th>
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

<!-- Modal Preview Detail Aspirasi -->
<div class="modal fade" id="modal-asp-preview" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-envelope-open-text me-2"></i>Detail Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0" id="asp-detail-dl">
                    <dt class="col-sm-3">Nama</dt>       <dd class="col-sm-9" id="asp-d-nama">-</dd>
                    <dt class="col-sm-3">WhatsApp</dt>   <dd class="col-sm-9" id="asp-d-telepon">-</dd>
                    <dt class="col-sm-3">Kategori</dt>   <dd class="col-sm-9" id="asp-d-subjek">-</dd>
                    <dt class="col-sm-3">Tanggal</dt>    <dd class="col-sm-9" id="asp-d-tanggal">-</dd>
                    <dt class="col-sm-3">IP</dt>         <dd class="col-sm-9" id="asp-d-ip" class="text-muted small">-</dd>
                    <dt class="col-sm-3 mt-2">Pesan</dt>
                    <dd class="col-sm-9 mt-2">
                        <div id="asp-d-pesan" class="p-3 bg-light rounded" style="white-space:pre-wrap;word-break:break-word;max-height:300px;overflow-y:auto">-</div>
                    </dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="asp-btn-delete-from-modal">
                    <i class="bi bi-trash me-1"></i>Hapus Laporan Ini
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal konfirmasi hapus -->
<div class="modal fade" id="modal-asp-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash me-1"></i>Hapus Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Apakah Anda yakin ingin menghapus laporan ini? Data tidak bisa dikembalikan.</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="asp-btn-confirm-delete">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var SUBJEK_LABEL = <?= json_encode($subjekLabel, JSON_UNESCAPED_UNICODE) ?>;
    var modalPreviewEl = document.getElementById('modal-asp-preview');
    var modalDeleteEl  = document.getElementById('modal-asp-delete');
    var pendingDelete  = null;

    /* ---- badge warna per kategori ---- */
    var SUBJEK_COLOR = {
        infrastruktur: 'bg-warning text-dark',
        pelayanan:     'bg-primary',
        keamanan:      'bg-danger',
        lainnya:       'bg-secondary'
    };

    function subjekBadge(v) {
        var cls = SUBJEK_COLOR[v] || 'bg-secondary';
        var label = SUBJEK_LABEL[v] || v;
        return '<span class="badge ' + cls + '">' + App.esc(label) + '</span>';
    }

    function formatTanggal(iso) {
        if (!iso) return '-';
        var d = new Date(iso);
        if (isNaN(d)) return iso;
        return d.toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
    }

    function ringkas(teks, n) {
        n = n || 80;
        if (!teks) return '<span class="text-muted">-</span>';
        var s = String(teks);
        if (s.length <= n) return '<span>' + App.esc(s) + '</span>';
        return '<span title="' + App.esc(s) + '">' + App.esc(s.slice(0, n)) + '&hellip;</span>';
    }

    /* ---- JsonTable ---- */
    var table = new App.JsonTable({
        selector:  '#tbl-aspirasi',
        module:    'aspirasi',
        perPage:   10,
        search:    '#asp-search',
        filters:   [{ key: 'subjek', label: 'Semua Kategori', select: '#asp-filter-subjek' }],
        columns: [
            { no: true, label: 'No' },
            { key: 'nama',    label: 'Nama' },
            { key: 'telepon', label: 'WhatsApp', format: function (row, v) {
                var w = String(v || '').replace(/\D+/g, '');
                if (!w) return '<span class="text-muted">-</span>';
                var href = 'https://wa.me/' + (w.charAt(0) === '0' ? '62' + w.slice(1) : w);
                return '<a href="' + App.esc(href) + '" target="_blank" rel="noopener" class="text-success">' +
                    '<i class="bi bi-whatsapp me-1"></i>' + App.esc(v) + '</a>';
            }},
            { key: 'subjek',  label: 'Kategori', format: function (row, v) { return subjekBadge(v); } },
            { key: 'pesan',   label: 'Pesan', format: function (row, v) { return ringkas(v, 80); } },
            { key: 'tanggal', label: 'Tanggal', format: function (row, v) { return '<small class="text-muted">' + App.esc(formatTanggal(v)) + '</small>'; } }
        ],
        actions: ['detail', 'delete'],
        onDetail: function (row) { showPreview(row); },
        onDelete: function (row) {
            pendingDelete = row.index;
            App.showModal(modalDeleteEl);
        }
    });

    /* update badge total */
    table._origRender = table.render ? table.render.bind(table) : null;
    document.addEventListener('asp:loaded', function (e) {
        var badge = document.getElementById('asp-total-badge');
        if (badge) badge.textContent = (e.detail && e.detail.total != null ? e.detail.total : 0) + ' laporan';
    });

    /* ---- preview detail ---- */
    function showPreview(row) {
        document.getElementById('asp-d-nama').textContent    = row.nama    || '-';
        document.getElementById('asp-d-telepon').textContent = row.telepon || '-';
        document.getElementById('asp-d-ip').textContent      = row.ip      || '-';
        document.getElementById('asp-d-tanggal').textContent = formatTanggal(row.tanggal);
        document.getElementById('asp-d-pesan').textContent   = row.pesan   || '-';
        var subjekEl = document.getElementById('asp-d-subjek');
        subjekEl.innerHTML = subjekBadge(row.subjek);
        /* simpan index untuk tombol hapus di dalam modal */
        document.getElementById('asp-btn-delete-from-modal').dataset.index = row.index;
        App.showModal(modalPreviewEl);
    }

    /* hapus dari dalam modal preview */
    document.getElementById('asp-btn-delete-from-modal').addEventListener('click', function () {
        pendingDelete = parseInt(this.dataset.index, 10);
        App.hideModal(modalPreviewEl);
        /* tunggu modal preview benar-benar tertutup, lalu buka modal konfirmasi */
        modalPreviewEl.addEventListener('hidden.bs.modal', function openDel() {
            modalPreviewEl.removeEventListener('hidden.bs.modal', openDel);
            App.showModal(modalDeleteEl);
        });
    });

    /* ---- konfirmasi hapus ---- */
    document.getElementById('asp-btn-confirm-delete').addEventListener('click', function () {
        App.postJSON('../admin/api.php', {
            action: 'delete', module: 'aspirasi', data: { index: pendingDelete }
        }).then(function (res) {
            if (res.ok) {
                App.hideModal(modalDeleteEl);
                App.toast('Laporan berhasil dihapus.', 'success', 'Berhasil');
                table.reload();
            } else {
                App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menghapus.', 'error', 'Gagal');
            }
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });
    /* ---- Dropdown aksi: wrap [data-act] buttons dalam Bootstrap dropdown ---- */
    (function () {
        var tbody = document.querySelector('#tbl-aspirasi tbody');
        if (!tbody) return;
        function wrapActs() {
            tbody.querySelectorAll('td.row-actions').forEach(function (cell) {
                if (cell.dataset.ddWrapped) return;
                cell.dataset.ddWrapped = '1';
                var btns = Array.from(cell.querySelectorAll('[data-act]'));
                if (!btns.length) return;
                var dd = document.createElement('div');
                dd.className = 'dropdown d-flex justify-content-end';
                var toggle = document.createElement('button');
                toggle.type = 'button';
                toggle.className = 'btn btn-sm btn-outline-secondary py-1 px-2';
                toggle.setAttribute('data-bs-toggle', 'dropdown');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.innerHTML = '<i class="bi bi-three-dots-vertical"></i>';
                var menu = document.createElement('ul');
                menu.className = 'dropdown-menu dropdown-menu-end shadow-sm';
                btns.forEach(function (btn, i) {
                    var act = btn.dataset.act;
                    if (act === 'delete' && i > 0) {
                        var sep = document.createElement('li');
                        sep.innerHTML = '<hr class="dropdown-divider my-1">';
                        menu.appendChild(sep);
                    }
                    btn.className = 'dropdown-item d-flex align-items-center gap-2 py-2';
                    if      (act === 'detail') { btn.innerHTML = '<i class="bi bi-eye text-info"></i>Lihat Detail'; }
                    else if (act === 'delete') { btn.innerHTML = '<i class="bi bi-trash"></i>Hapus'; btn.classList.add('text-danger'); }
                    var li = document.createElement('li');
                    li.appendChild(btn);
                    menu.appendChild(li);
                });
                dd.appendChild(toggle);
                dd.appendChild(menu);
                cell.innerHTML = '';
                cell.appendChild(dd);
            });
        }
        new MutationObserver(wrapActs).observe(tbody, { childList: true });
    })();
});
</script>
