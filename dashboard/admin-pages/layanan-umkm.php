<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$kategoriOptions = ['UMKM & Produk Lokal', 'Layanan Kesehatan', 'Pariwisata', 'Agrikultur', 'Fasilitas Publik'];
$iconOptions = ['storefront', 'location_on', 'medical_services', 'schedule', 'groups', 'map', 'agriculture', 'landscape', 'account_balance', 'work_history', 'cookie', 'shopping_bag', 'store', 'restaurant', 'park', 'school', 'mosque', 'local_florist', 'eco', 'forest', 'water_drop', 'home', 'domain', 'directions_bus'];
?>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>.material-symbols-outlined{font-size:18px;line-height:1;vertical-align:-3px}.app-ikon-preview .material-symbols-outlined{font-size:20px}</style>

<div class="page-heading">
    <h3>Layanan &amp; UMKM</h3>
    <p class="text-subtitle text-muted">Kelola layanan, UMKM, wisata, dan fasilitas publik pekon (teks judul halaman dikode di komponen front-end)</p>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="mb-0"><i class="bi bi-grid me-2 text-success"></i>Katalog Layanan &amp; UMKM</h5>
                <button type="button" class="btn btn-sm btn-primary" id="btn-add-lay">
                    <i class="bi bi-plus-lg"></i> Tambah
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="app-table-toolbar mb-3">
                <input type="text" id="search-dir" class="form-control form-control-sm" placeholder="Cari nama / kategori / lokasi...">
                <select id="filter-lay-kategori" class="form-select form-select-sm"></select>
            </div>
            <div class="app-table-wrap">
                <table class="table table-hover" id="tbl-layanan">
                    <thead>
                        <tr>
                            <th style="width:64px">Foto</th>
                            <th class="w-25">Nama</th>
                            <th>Kategori</th>
                            <th>Badge</th>
                            <th>Info Baris</th>
                            <th>Maps / WA</th>
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

<!-- Modal tambah/edit item layanan & UMKM -->
<div class="modal fade" id="modal-lay" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form-dir">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-lay-title">Tambah Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="index" value="">
                    <input type="hidden" name="foto" value="">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" name="kategori" required>
                                <?php foreach ($kategoriOptions as $kat): ?>
                                <option value="<?= $kat ?>"><?= $kat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama / Judul</label>
                            <input type="text" class="form-control" name="nama" placeholder="contoh: Kopi Robusta Petik Merah" required>
                        </div>
                    </div>
                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Badge (label di kartu)</label>
                            <input type="text" class="form-control" name="badge" placeholder="contoh: UMKM Unggulan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subjudul</label>
                            <input type="text" class="form-control" name="subjudul" placeholder="contoh: Produk Lokal Berkualitas">
                        </div>
                    </div>
                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Google Maps</label>
                            <input type="text" class="form-control" name="maps" placeholder="https://maps.app.goo.gl/... atau ?q=lat,lng">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No WhatsApp</label>
                            <input type="text" class="form-control" name="wa" placeholder="contoh: 081234567890">
                        </div>
                    </div>
                    <label class="form-label mt-3">Foto</label>
                    <input type="file" id="lay-foto" class="form-control form-control-sm" accept="image/*">
                    <div class="app-upload-hint">Maksimal 2 MB, otomatis dikompres WebP. Foto wajib diunggah dari perangkat, tidak menerima URL.</div>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <div id="lay-foto-wrap" class="d-none">
                            <img id="lay-foto-preview" class="app-foto-preview" alt="Pratinjau foto" data-preview="">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger d-none" id="lay-foto-remove">
                            <i class="bi bi-x-lg"></i> Hapus Foto
                        </button>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <label class="form-label d-flex align-items-center gap-2">
                                Baris Info 1 - Ikon
                                <span class="app-ikon-preview border rounded p-1" title="Pratinjau ikon"><span class="material-symbols-outlined" id="preview-ikon0">storefront</span></span>
                            </label>
                            <select class="form-select form-select-sm" name="baris0_ikon">
                                <?php foreach ($iconOptions as $ic): ?>
                                <option value="<?= $ic ?>"><?= $ic ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Baris Info 1 - Teks</label>
                            <input type="text" class="form-control" name="baris0_teks" placeholder="contoh: Kelompok Tani Harapan Jaya">
                        </div>
                    </div>
                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <label class="form-label d-flex align-items-center gap-2">
                                Baris Info 2 - Ikon
                                <span class="app-ikon-preview border rounded p-1" title="Pratinjau ikon"><span class="material-symbols-outlined" id="preview-ikon1">location_on</span></span>
                            </label>
                            <select class="form-select form-select-sm" name="baris1_ikon">
                                <?php foreach ($iconOptions as $ic): ?>
                                <option value="<?= $ic ?>"><?= $ic ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Baris Info 2 - Teks</label>
                            <input type="text" class="form-control" name="baris1_teks" placeholder="contoh: Dusun II, Gunung Megang">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-lay">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal konfirmasi hapus -->
<div class="modal fade" id="modal-delete-lay" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash me-1"></i>Hapus Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-delete-lay-text">Apakah Anda yakin ingin menghapus data ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete-lay">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalLay = document.getElementById('modal-lay');
    var formItem = document.getElementById('form-dir');
    var fotoInput = document.getElementById('lay-foto');
    var fotoWrap = document.getElementById('lay-foto-wrap');
    var fotoImg = document.getElementById('lay-foto-preview');
    var fotoRemove = document.getElementById('lay-foto-remove');
    var pendingDelete = null;

    function resPath(p) {
        if (!p) return '';
        if (/^(https?:)?\/\//i.test(p) || p.indexOf('data:') === 0 || p.charAt(0) === '/') return p;
        return '../' + p;
    }

    function setFotoPreview(path) {
        if (path) {
            var src = resPath(path);
            fotoImg.src = src;
            fotoImg.setAttribute('data-preview', src);
            fotoImg.style.display = '';
            fotoWrap.classList.remove('d-none');
            fotoRemove.classList.remove('d-none');
        } else {
            fotoImg.removeAttribute('src');
            fotoImg.style.display = 'none';
            fotoWrap.classList.add('d-none');
            fotoRemove.classList.add('d-none');
        }
    }

    function fotoCell(v) {
        if (!v) return '<span class="text-muted">-</span>';
        var src = resPath(v);
        return '<span class="app-thumb" data-preview="' + App.esc(src) + '" role="button" title="Klik untuk preview">' +
            '<span class="skeleton"></span>' +
            '<img src="' + App.esc(src) + '" alt="" loading="lazy" class="app-thumb-img" onload="this.previousElementSibling.remove()" onerror="this.remove()">' +
            '<span class="app-zoom"><i class="bi bi-zoom-in"></i></span></span>';
    }

    function iconCell(ikon) {
        return '<span class="material-symbols-outlined text-success">' + App.esc(ikon || '') + '</span>';
    }

    function infoCell(row) {
        var b = row.baris || [];
        if (!b.length) return '<span class="text-muted">-</span>';
        return b.map(function (x) {
            return '<div class="d-flex align-items-center gap-1 text-nowrap">' +
                iconCell(x.ikon) +
                '<span class="text-muted small">' + App.esc(x.teks || '') + '</span></div>';
        }).join('');
    }

    function kontakCell(row) {
        var bits = [];
        if (row.maps) bits.push('<span class="text-muted small">maps</span>');
        if (row.wa) bits.push('<span class="text-muted small">wa</span>');
        return bits.length ? bits.join(' &middot; ') : '<span class="text-muted">-</span>';
    }

    var table = new App.JsonTable({
        selector: '#tbl-layanan',
        module: 'layanan_umkm',
        perPage: 10,
        search: '#search-lay',
        filters: [
            { key: 'kategori', label: 'Semua Kategori', select: '#filter-lay-kategori' }
        ],
        columns: [
            { key: 'foto', label: 'Foto', format: function (row, v) { return fotoCell(v); } },
            { key: 'nama', label: 'Nama' },
            { key: 'kategori', label: 'Kategori', type: 'badge' },
            { key: 'badge', label: 'Badge', format: function (row, v) { return v ? '<span class="badge rounded-pill bg-light text-dark border">' + App.esc(v) + '</span>' : '-'; } },
            { key: 'baris', label: 'Info Baris', format: function (row, v) { return infoCell(row); } },
            { key: 'kontak', label: 'Maps / WA', format: function (row, v) { return kontakCell(row); } }
        ],
        actions: ['edit', 'delete'],
        onEdit: function (row) {
            document.getElementById('modal-lay-title').textContent = 'Edit Item';
            formItem.index.value = row.index;
            formItem.kategori.value = row.kategori || 'UMKM & Produk Lokal';
            formItem.badge.value = row.badge || '';
            formItem.nama.value = row.nama || '';
            formItem.subjudul.value = row.subjudul || '';
            formItem.maps.value = row.maps || '';
            formItem.wa.value = row.wa || '';
            formItem.foto.value = row.foto || '';
            var b0 = (row.baris && row.baris[0]) || {};
            var b1 = (row.baris && row.baris[1]) || {};
            formItem.baris0_ikon.value = b0.ikon || 'storefront';
            formItem.baris0_teks.value = b0.teks || '';
            formItem.baris1_ikon.value = b1.ikon || 'location_on';
            formItem.baris1_teks.value = b1.teks || '';
            syncIkonPreview();
            setFotoPreview(row.foto || '');
            App.showModal(modalLay);
        },
        onDelete: function (row) {
            pendingDelete = row.index;
            document.getElementById('modal-delete-lay-text').textContent =
                'Apakah Anda yakin ingin menghapus "' + row.nama + '" dari katalog?';
            App.showModal(document.getElementById('modal-delete-lay'));
        }
    });

    function syncIkonPreview() {
        document.getElementById('preview-ikon0').textContent = formItem.baris0_ikon.value || 'storefront';
        document.getElementById('preview-ikon1').textContent = formItem.baris1_ikon.value || 'location_on';
    }

    formItem.baris0_ikon.addEventListener('change', syncIkonPreview);
    formItem.baris1_ikon.addEventListener('change', syncIkonPreview);

    document.getElementById('btn-add-lay').addEventListener('click', function () {
        document.getElementById('modal-lay-title').textContent = 'Tambah Item';
        formItem.reset();
        formItem.kategori.value = 'UMKM & Produk Lokal';
        formItem.baris0_ikon.value = 'storefront';
        formItem.baris1_ikon.value = 'location_on';
        syncIkonPreview();
        setFotoPreview('');
        App.showModal(modalLay);
    });

    fotoInput.addEventListener('change', function () {
        App.uploadFile(this, function (res) {
            if (res) {
                formItem.foto.value = res.path;
                setFotoPreview(res.path);
            } else {
                fotoInput.value = '';
            }
        });
    });

    fotoRemove.addEventListener('click', function () {
        formItem.foto.value = '';
        fotoInput.value = '';
        setFotoPreview('');
    });

    formItem.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!formItem.checkValidity()) { formItem.reportValidity(); return; }
        App.postJSON('../admin/api.php', {
            action: 'save_row', module: 'layanan_umkm',
            data: {
                index: formItem.index.value,
                kategori: formItem.kategori.value,
                badge: formItem.badge.value,
                nama: formItem.nama.value,
                subjudul: formItem.subjudul.value,
                foto: formItem.foto.value,
                maps: formItem.maps.value,
                wa: formItem.wa.value,
                baris0_ikon: formItem.baris0_ikon.value,
                baris0_teks: formItem.baris0_teks.value,
                baris1_ikon: formItem.baris1_ikon.value,
                baris1_teks: formItem.baris1_teks.value
            }
        }).then(function (res) {
            if (res.ok) {
                App.hideModal(modalLay);
                App.toast('Item berhasil disimpan.', 'success', 'Berhasil');
                table.reload();
            } else {
                App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
            }
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });

    document.getElementById('btn-confirm-delete-lay').addEventListener('click', function () {
        App.postJSON('../admin/api.php', {
            action: 'delete', module: 'layanan_umkm', data: { index: pendingDelete }
        }).then(function (res) {
            if (res.ok) {
                App.hideModal(document.getElementById('modal-delete-lay'));
                App.toast('Data berhasil dihapus.', 'success', 'Berhasil');
                table.reload();
            } else {
                App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menghapus.', 'error', 'Gagal');
            }
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });
});
</script>