<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$kategoriOptions = ['UMKM & Produk Lokal', 'Layanan Kesehatan', 'Pariwisata', 'Agrikultur', 'Fasilitas Publik'];

/* Mapping: kode Material Symbol => nama deskriptif Indonesia */
$iconOptions = [
    'storefront'       => 'Toko / Usaha',
    'location_on'      => 'Lokasi / Maps',
    'medical_services' => 'Layanan Medis',
    'schedule'         => 'Jam Operasional',
    'groups'           => 'Kelompok / Komunitas',
    'map'              => 'Peta Wilayah',
    'agriculture'      => 'Pertanian',
    'landscape'        => 'Alam / Pemandangan',
    'account_balance'  => 'Lembaga / Kantor',
    'work_history'     => 'Riwayat Kerja',
    'cookie'           => 'Produk Makanan',
    'shopping_bag'     => 'Belanja / Produk',
    'store'            => 'Warung / Kios',
    'restaurant'       => 'Restoran / Kuliner',
    'park'             => 'Taman / Wisata Alam',
    'school'           => 'Sekolah / Pendidikan',
    'mosque'           => 'Masjid / Ibadah',
    'local_florist'    => 'Tanaman / Bunga',
    'eco'              => 'Ramah Lingkungan',
    'forest'           => 'Hutan / Kebun',
    'water_drop'       => 'Air / Sumber Daya Air',
    'home'             => 'Rumah / Hunian',
    'domain'           => 'Gedung / Bangunan',
    'directions_bus'   => 'Transportasi',
];
?>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10/public/assets/styles/choices.min.css">
<style>
    .material-symbols-outlined {
        font-size: 18px;
        line-height: 1;
        vertical-align: -3px
    }

    .app-ikon-preview .material-symbols-outlined {
        font-size: 20px
    }

    /* Choices.js — limit dropdown 10 item (scrollable) */
    .choices__list--dropdown .choices__list {
        max-height: 280px;
        overflow-y: auto;
    }

    /* Choices item row: ikon + nama + kode */
    .choices__item--choice .app-ch-row,
    .choices__item .app-ch-row {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: .875rem;
    }

    .choices__item--choice .app-ch-row .material-symbols-outlined {
        font-size: 18px;
        color: var(--bs-success, #198754);
        min-width: 20px;
    }

    .choices__item .app-ch-row .material-symbols-outlined {
        font-size: 16px;
        color: var(--bs-success, #198754);
        min-width: 18px;
    }

    .choices__item--choice.is-highlighted .app-ch-row .material-symbols-outlined {
        color: #fff;
    }

    .app-ch-code {
        font-size: .7rem;
        color: #94a3b8;
        font-family: monospace;
        display: block;
        line-height: 1;
    }

    .choices__item--choice.is-highlighted .app-ch-code {
        color: rgba(255, 255, 255, .6);
    }

    /* match form-select-sm height */
    .choices.form-select-sm .choices__inner {
        min-height: calc(1.5em + .5rem + 2px);
        padding: .25rem .5rem;
        font-size: .875rem;
    }
</style>

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
                            <th class="text-center" style="width:52px">No</th>
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
                    <input type="hidden" name="foto" id="lay-foto-val" value="">
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
                    <!-- Foto Upload -->
                    <label class="form-label mt-3">Foto</label>
                    <input type="file" id="lay-foto" class="form-control form-control-sm" accept="image/*">
                    <div class="app-upload-hint">Maksimal 2 MB, otomatis dikompres WebP. Foto wajib diunggah dari perangkat, tidak menerima URL.</div>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <div id="lay-foto-wrap" style="display:none">
                            <img id="lay-foto-preview" class="app-foto-preview" alt="Pratinjau foto" style="cursor:pointer">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="lay-foto-remove" style="display:none">
                            <i class="bi bi-x-lg"></i> Hapus Foto
                        </button>
                    </div>
                    <!-- Baris info 1 -->
                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <label class="form-label d-flex align-items-center gap-2">
                                Baris Info 1 - Ikon
                                <span class="app-ikon-preview border rounded p-1" title="Pratinjau ikon"><span class="material-symbols-outlined" id="preview-ikon0">storefront</span></span>
                            </label>
                            <select class="form-select form-select-sm" name="baris0_ikon" id="sel-ikon0">
                                <?php foreach ($iconOptions as $code => $label): ?>
                                    <option value="<?= $code ?>"><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="d-flex align-items-center gap-1 mt-1">
                                <small class="text-muted"><i class="bi bi-info-circle"></i> Kode ikon dari</small>
                                <a href="https://fonts.google.com/icons" target="_blank" rel="noopener" class="small">Material Symbols <i class="bi bi-box-arrow-up-right" style="font-size:10px"></i></a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Baris Info 1 - Teks</label>
                            <input type="text" class="form-control" name="baris0_teks" placeholder="contoh: Kelompok Tani Harapan Jaya">
                        </div>
                    </div>
                    <!-- Baris info 2 -->
                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <label class="form-label d-flex align-items-center gap-2">
                                Baris Info 2 - Ikon
                                <span class="app-ikon-preview border rounded p-1" title="Pratinjau ikon"><span class="material-symbols-outlined" id="preview-ikon1">location_on</span></span>
                            </label>
                            <select class="form-select form-select-sm" name="baris1_ikon" id="sel-ikon1">
                                <?php foreach ($iconOptions as $code => $label): ?>
                                    <option value="<?= $code ?>"><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="d-flex align-items-center gap-1 mt-1">
                                <small class="text-muted"><i class="bi bi-info-circle"></i> Kode ikon dari</small>
                                <a href="https://fonts.google.com/icons" target="_blank" rel="noopener" class="small">Material Symbols <i class="bi bi-box-arrow-up-right" style="font-size:10px"></i></a>
                            </div>
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

<!-- Mapping ikon -> nama (dipakai oleh JS Choices template) -->
<script>
    var LAY_IKON_MAP = <?= json_encode(array_map(fn($label) => $label, $iconOptions), JSON_UNESCAPED_UNICODE) ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/choices.js@10/public/assets/scripts/choices.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalLayEl = document.getElementById('modal-lay');
        var formItem = document.getElementById('form-dir');
        var fotoInput = document.getElementById('lay-foto');
        var fotoVal = document.getElementById('lay-foto-val'); // hidden input "foto"
        var fotoWrap = document.getElementById('lay-foto-wrap');
        var fotoImg = document.getElementById('lay-foto-preview');
        var fotoRemove = document.getElementById('lay-foto-remove');
        var pendingDelete = null;

        /* ---- path resolver: dari dashboard/ ke root ---- */
        function resPath(p) {
            if (!p) return '';
            if (/^(https?:)?\/\//i.test(p) || p.indexOf('data:') === 0 || p.charAt(0) === '/') return p;
            return '../' + p;
        }

        /* ---- tampilkan / sembunyikan preview foto di modal ---- */
        function setFotoPreview(path) {
            if (path) {
                var src = resPath(path);
                fotoImg.src = src;
                fotoImg.setAttribute('data-preview', src);
                fotoWrap.style.display = '';
                fotoRemove.style.display = '';
            } else {
                fotoImg.removeAttribute('src');
                fotoImg.removeAttribute('data-preview');
                fotoWrap.style.display = 'none';
                fotoRemove.style.display = 'none';
            }
            /* sync hidden input */
            fotoVal.value = path || '';
        }

        /* ---- render cell tabel ---- */
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
            return b.map(function(x) {
                return '<div class="d-flex align-items-center gap-1 text-nowrap">' +
                    iconCell(x.ikon) +
                    '<span class="text-muted small">' + App.esc(x.teks || '') + '</span></div>';
            }).join('');
        }

        function waLink(wa) {
            var w = String(wa || '').replace(/\D+/g, '');
            if (!w) return '';
            if (w.charAt(0) === '0') w = '62' + w.slice(1);
            return 'https://wa.me/' + w;
        }

        function kontakCell(row) {
            var bits = [];
            if (row.maps) {
                bits.push('<a class="btn btn-sm btn-icon row-act text-primary" href="' + App.esc(row.maps) + '" target="_blank" rel="noopener" title="Buka Google Maps">' +
                    '<span class="material-symbols-outlined">location_on</span></a>');
            }
            if (row.wa) {
                var href = waLink(row.wa);
                if (href) {
                    bits.push('<a class="btn btn-sm btn-icon row-act" style="color:#25D366" href="' + App.esc(href) + '" target="_blank" rel="noopener" title="Chat WhatsApp">' +
                        '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg></a>');
                }
            }
            return bits.length ? '<div class="d-inline-flex align-items-center gap-1">' + bits.join('') + '</div>' : '<span class="text-muted">-</span>';
        }

        /* ---- JsonTable ---- */
        var table = new App.JsonTable({
            selector: '#tbl-layanan',
            module: 'layanan_umkm',
            perPage: 10,
            search: '#search-dir',
            filters: [{
                key: 'kategori',
                label: 'Semua Kategori',
                select: '#filter-lay-kategori'
            }],
            columns: [{
                    no: true,
                    label: 'No'
                },
                {
                    key: 'foto',
                    label: 'Foto',
                    format: function(row, v) {
                        return fotoCell(v);
                    }
                },
                {
                    key: 'nama',
                    label: 'Nama'
                },
                {
                    key: 'kategori',
                    label: 'Kategori',
                    type: 'badge'
                },
                {
                    key: 'badge',
                    label: 'Badge',
                    format: function(row, v) {
                        return v ? '<span class="badge rounded-pill bg-light text-dark border">' + App.esc(v) + '</span>' : '-';
                    }
                },
                {
                    key: 'baris',
                    label: 'Info Baris',
                    format: function(row, v) {
                        return infoCell(row);
                    }
                },
                {
                    key: 'kontak',
                    label: 'Maps / WA',
                    format: function(row, v) {
                        return kontakCell(row);
                    }
                }
            ],
            actions: ['edit', 'delete'],
            onEdit: function(row) {
                document.getElementById('modal-lay-title').textContent = 'Edit Item';
                formItem.index.value = row.index;
                formItem.kategori.value = row.kategori || 'UMKM & Produk Lokal';
                formItem.badge.value = row.badge || '';
                formItem.nama.value = row.nama || '';
                formItem.subjudul.value = row.subjudul || '';
                formItem.maps.value = row.maps || '';
                formItem.wa.value = row.wa || '';
                var b0 = (row.baris && row.baris[0]) || {};
                var b1 = (row.baris && row.baris[1]) || {};
                setIkonChoice('sel-ikon0', b0.ikon || 'storefront');
                formItem.baris0_teks.value = b0.teks || '';
                setIkonChoice('sel-ikon1', b1.ikon || 'location_on');
                formItem.baris1_teks.value = b1.teks || '';
                setFotoPreview(row.foto || '');
                App.showModal(modalLayEl);
            },
            onDelete: function(row) {
                pendingDelete = row.index;
                document.getElementById('modal-delete-lay-text').textContent =
                    'Apakah Anda yakin ingin menghapus "' + row.nama + '" dari katalog?';
                App.showModal(document.getElementById('modal-delete-lay'));
            }
        });

        /* ======================================================
         * CHOICES.JS IKON — vanilla JS, search built-in, 10 item scrollable
         * ====================================================== */
        var choicesMap = {}; // { 'sel-ikon0': choicesInstance, 'sel-ikon1': choicesInstance }
        var IKON_IDS = [
            ['sel-ikon0', 'preview-ikon0'],
            ['sel-ikon1', 'preview-ikon1']
        ];

        /** Buat template HTML 1 baris ikon untuk Choices */
        function makeChoiceInnerHTML(code, label, isSelection) {
            var codeSpan = isSelection ? '' : '<span class="app-ch-code">' + code + '</span>';
            return '<span class="app-ch-row">' +
                '<span class="material-symbols-outlined">' + code + '</span>' +
                '<span><span>' + label + '</span>' + codeSpan + '</span>' +
                '</span>';
        }

        function buildChoicesOptions() {
            return Object.keys(LAY_IKON_MAP).map(function(code) {
                return {
                    value: code,
                    label: LAY_IKON_MAP[code]
                };
            });
        }

        function initIkonChoices() {
            IKON_IDS.forEach(function(pair) {
                var selId = pair[0];
                var prevId = pair[1];
                var selEl = document.getElementById(selId);
                if (!selEl || choicesMap[selId]) return;

                var ch = new Choices(selEl, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'Cari ikon...',
                    searchFields: ['label', 'value'],
                    itemSelectText: '',
                    shouldSort: false,
                    allowHTML: true,
                    callbackOnCreateTemplates: function(tmpl) {
                        return {
                            /* Item terpilih (di dalam kotak) */
                            item: function(cfg, data) {
                                var label = LAY_IKON_MAP[data.value] || data.label;
                                return tmpl('<div class="' + cfg.classNames.item + ' ' +
                                    (data.placeholder ? cfg.classNames.placeholder : '') + '" data-item data-id="' +
                                    data.id + '" data-value="' + data.value + '">' +
                                    makeChoiceInnerHTML(data.value, label, true) + '</div>');
                            },
                            /* Tiap pilihan di dropdown */
                            choice: function(cfg, data) {
                                var label = LAY_IKON_MAP[data.value] || data.label;
                                return tmpl('<div class="' + cfg.classNames.item + ' ' +
                                    cfg.classNames.itemChoice + ' ' +
                                    (data.disabled ? cfg.classNames.itemDisabled : cfg.classNames.itemSelectable) + '" ' +
                                    'data-choice data-id="' + data.id + '" data-value="' + data.value + '" ' +
                                    (data.disabled ? 'aria-disabled="true"' : 'data-choice-selectable') + '>' +
                                    makeChoiceInnerHTML(data.value, label, false) + '</div>');
                            }
                        };
                    }
                });

                /* update preview ikon ketika pilihan berubah */
                selEl.addEventListener('change', function() {
                    var el = document.getElementById(prevId);
                    if (el) el.textContent = this.value || 'storefront';
                });

                choicesMap[selId] = ch;
            });
        }

        /* Init Choices saat modal pertama kali shown */
        modalLayEl.addEventListener('shown.bs.modal', function() {
            initIkonChoices();
        });

        /** Set nilai pilihan Choices + preview icon */
        function setIkonChoice(selId, val) {
            var selEl = document.getElementById(selId);
            if (!selEl) return;
            /* Update nilai native <select> */
            selEl.value = val || '';
            /* Update Choices instance jika sudah ada */
            if (choicesMap[selId]) {
                choicesMap[selId].setChoiceByValue(val || '');
            }
            /* Update preview icon di label */
            var previewId = selId === 'sel-ikon0' ? 'preview-ikon0' : 'preview-ikon1';
            var el = document.getElementById(previewId);
            if (el) el.textContent = val || 'storefront';
        }

        /* ---- Tombol Tambah ---- */
        document.getElementById('btn-add-lay').addEventListener('click', function() {
            document.getElementById('modal-lay-title').textContent = 'Tambah Item';
            formItem.reset();
            fotoVal.value = '';
            formItem.kategori.value = 'UMKM & Produk Lokal';
            setFotoPreview('');
            App.showModal(modalLayEl);
            /* Set default ikon setelah Choices siap (tunggu shown.bs.modal selesai) */
            setTimeout(function() {
                setIkonChoice('sel-ikon0', 'storefront');
                setIkonChoice('sel-ikon1', 'location_on');
            }, 80);
        });

        /* ---- Upload foto ---- */
        fotoInput.addEventListener('change', function() {
            App.uploadFile(this, function(res) {
                if (res && res.ok !== false) {
                    setFotoPreview(res.path);
                } else {
                    fotoInput.value = '';
                    setFotoPreview('');
                }
            });
        });

        /* ---- Hapus foto ---- */
        fotoRemove.addEventListener('click', function() {
            setFotoPreview('');
            fotoInput.value = '';
        });

        /* ---- Submit form ---- */
        formItem.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!formItem.checkValidity()) {
                formItem.reportValidity();
                return;
            }
            var btn = document.getElementById('btn-save-lay');
            btn.disabled = true;
            App.postJSON('../admin/api.php', {
                action: 'save_row',
                module: 'layanan_umkm',
                data: {
                    index: formItem.index.value,
                    kategori: formItem.kategori.value,
                    badge: formItem.badge.value,
                    nama: formItem.nama.value,
                    subjudul: formItem.subjudul.value,
                    foto: fotoVal.value,
                    maps: formItem.maps.value,
                    wa: formItem.wa.value,
                    baris0_ikon: formItem.baris0_ikon.value,
                    baris0_teks: formItem.baris0_teks.value,
                    baris1_ikon: formItem.baris1_ikon.value,
                    baris1_teks: formItem.baris1_teks.value
                }
            }).then(function(res) {
                btn.disabled = false;
                if (res.ok) {
                    App.hideModal(modalLayEl);
                    App.toast('Item berhasil disimpan.', 'success', 'Berhasil');
                    table.reload();
                } else {
                    App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
                }
            }).catch(function() {
                btn.disabled = false;
                App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
            });
        });

        /* ---- Konfirmasi hapus ---- */
        document.getElementById('btn-confirm-delete-lay').addEventListener('click', function() {
            App.postJSON('../admin/api.php', {
                action: 'delete',
                module: 'layanan_umkm',
                data: {
                    index: pendingDelete
                }
            }).then(function(res) {
                if (res.ok) {
                    App.hideModal(document.getElementById('modal-delete-lay'));
                    App.toast('Data berhasil dihapus.', 'success', 'Berhasil');
                    table.reload();
                } else {
                    App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menghapus.', 'error', 'Gagal');
                }
            }).catch(function() {
                App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
            });
        });

        /* ---- Dropdown aksi: wrap [data-act] buttons dalam Bootstrap dropdown ---- */
        (function() {
            var tbody = document.querySelector('#tbl-layanan tbody');
            if (!tbody) return;

            function wrapActs() {
                tbody.querySelectorAll('td.row-actions').forEach(function(cell) {
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
                    btns.forEach(function(btn, i) {
                        var act = btn.dataset.act;
                        if (act === 'delete' && i > 0) {
                            var sep = document.createElement('li');
                            sep.innerHTML = '<hr class="dropdown-divider my-1">';
                            menu.appendChild(sep);
                        }
                        btn.className = 'dropdown-item d-flex align-items-center gap-2 py-2';
                        if (act === 'edit') {
                            btn.innerHTML = '<i class="bi bi-pencil-square text-primary"></i>Ubah';
                        } else if (act === 'delete') {
                            btn.innerHTML = '<i class="bi bi-trash"></i>Hapus';
                            btn.classList.add('text-danger');
                        } else if (act === 'detail') {
                            btn.innerHTML = '<i class="bi bi-eye text-info"></i>Lihat';
                        }
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
            new MutationObserver(wrapActs).observe(tbody, {
                childList: true
            });
        })();
    });
</script>