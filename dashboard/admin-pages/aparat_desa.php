<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;
?>
<div class="page-heading">
    <h3>Aparat &amp; Lembaga</h3>
    <p class="text-subtitle text-muted">Kepala pekon, perangkat, BHP, dan LPM pekon</p>
</div>

<?php
$pekonData = include dirname(__DIR__, 2) . '/includes/pekon.php';
$kepala    = $pekonData['kepala_pekon'] ?? [];
$kpFoto    = $kepala['foto'] ?? '';
$kpFotoExist = $kpFoto !== '' && file_exists(dirname(__DIR__, 2) . '/' . $kpFoto);
$kpFotoShow  = $kpFotoExist && !preg_match('#^(https?:)?//#i', $kpFoto) && strpos($kpFoto, 'data:') !== 0 && $kpFoto[0] !== '/' ? '../' . $kpFoto : $kpFoto;
?>

<section class="section">
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-person-badge text-primary"></i>
            <h6 class="mb-0">Kepala Pekon</h6>
        </div>
        <div class="card-body">
            <form id="form-kepala" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nama Kepala Pekon</label>
                    <input type="text" class="form-control" name="kp_nama" value="<?= htmlspecialchars($kepala['nama'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jabatan</label>
                    <input type="text" class="form-control" name="kp_jabatan" value="<?= htmlspecialchars($kepala['jabatan'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Foto Kepala Pekon</label>
                    <input type="file" id="kp-foto-input" class="form-control"
                        accept=".png,.jpg,.jpeg,.gif,.webp,.bmp,.ico,.heic,.heif,.jfif,image/*">
                    <div class="app-upload-hint">Maksimal 2 MB (PNG, JPG, GIF, WEBP, ICO, HEIC/HEIF, dll).</div>
                    <input type="hidden" name="kp_foto" value="<?= htmlspecialchars($kpFoto) ?>">
                </div>
                <div class="col-md-4 d-flex align-items-center">
                    <?php if ($kpFotoExist): ?>
                        <img src="<?= htmlspecialchars($kpFotoShow) ?>" alt="Foto Kepala Pekon" class="app-foto-preview" data-preview="<?= htmlspecialchars($kpFotoShow) ?>" id="kp-foto-preview">
                    <?php else: ?>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted app-foto-preview" id="kp-foto-preview">
                            <i class="bi bi-person fs-3"></i>
                        </div>
                    <?php endif; ?>
                    <span class="ms-2 text-muted small">Klik foto untuk preview</span>
                </div>
                <div class="col-12">
                    <label class="form-label">Sambutan Kepala Pekon</label>
                    <textarea class="form-control" name="kp_sambutan" rows="4" maxlength="2000"><?= htmlspecialchars($kepala['sambutan'] ?? '') ?></textarea>
                    <div class="app-upload-hint">Teks sambutan/welcome yang tampil di halaman Pemerintahan.</div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="btn-save-kepala">
                        <i class="bi bi-check-lg"></i> Simpan Kepala Pekon
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

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
                            <th style="width:64px">Foto</th>
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
                    <input type="hidden" name="foto" value="">
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
                    <label class="form-label mt-3">Foto</label>
                    <input type="file" id="aparat-foto" class="form-control form-control-sm" accept="image/*">
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <div id="aparat-foto-wrap" class="d-none">
                            <img id="aparat-foto-preview" class="app-foto-preview" alt="Pratinjau foto" data-preview="">
                            <span id="aparat-foto-avatar" class="app-thumb app-foto-preview d-inline-flex align-items-center justify-content-center fw-bold text-white" style="display:none;font-size:26px"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger d-none" id="aparat-foto-remove">
                            <i class="bi bi-x-lg"></i> Hapus Foto
                        </button>
                    </div>
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

<!-- Modal konfirmasi hapus (dinonaktifkan: daftar aparat tidak boleh kosong) -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalAparat = document.getElementById('modal-aparat');
        var formAparat = document.getElementById('form-aparat');
        var fotoInput = document.getElementById('aparat-foto');
        var fotoWrap = document.getElementById('aparat-foto-wrap');
        var fotoImg = document.getElementById('aparat-foto-preview');
        var fotoAvatar = document.getElementById('aparat-foto-avatar');
        var fotoRemove = document.getElementById('aparat-foto-remove');

        function resPath(p) {
            if (!p) return '';
            if (/^(https?:)?\/\//i.test(p) || p.indexOf('data:') === 0 || p.charAt(0) === '/') return p;
            return '../' + p;
        }

        var AVATAR_COLORS = ['#0ea5a4', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#ef4444', '#14b8a6'];

        function initials(n) {
            var parts = String(n || '').trim().split(/\s+/).filter(Boolean);
            if (!parts.length) return '?';
            var ini = parts[0].charAt(0).toUpperCase();
            if (parts.length > 1) ini += parts[parts.length - 1].charAt(0).toUpperCase();
            return ini;
        }

        function avatarColors(n) {
            var h = 0;
            for (var i = 0; i < n.length; i++) h = (h * 31 + n.charCodeAt(i)) >>> 0;
            return [AVATAR_COLORS[h % AVATAR_COLORS.length], AVATAR_COLORS[(h + 3) % AVATAR_COLORS.length]];
        }

        function avatarHtml(nama, cls, fs) {
            var n = String(nama || '');
            var cs = avatarColors(n);
            return '<span class="app-thumb d-inline-flex align-items-center justify-content-center fw-bold text-white' + (cls ? ' ' + cls : '') +
                '" style="background:linear-gradient(135deg,' + cs[0] + ',' + cs[1] + ');font-size:' + (fs || 15) + 'px" title="' + App.esc(n) + '">' +
                App.esc(initials(n)) + '</span>';
        }

        function fotoCell(v, nama) {
            if (!v) return avatarHtml(nama);
            var src = resPath(v);
            return '<span class="app-thumb" data-preview="' + App.esc(src) + '" role="button" title="Klik untuk preview">' +
                '<span class="skeleton"></span>' +
                '<img src="' + App.esc(src) + '" alt="" loading="lazy" class="app-thumb-img" data-name="' + App.esc(nama) + '" onload="this.previousElementSibling.remove()">' +
                '<span class="app-zoom"><i class="bi bi-zoom-in"></i></span></span>';
        }

        /* fallback: jika file foto tidak ditemukan (404/rusak) -> avatar inisial */
        document.addEventListener('error', function(e) {
            var t = e.target;
            if (!t || t.tagName !== 'IMG' || !t.hasAttribute('data-name')) return;
            var nama = t.getAttribute('data-name');
            var big = t.classList.contains('app-foto-preview');
            var wrap = t.closest ? t.closest('.app-thumb') : null;
            if (wrap && wrap.parentNode) {
                wrap.outerHTML = avatarHtml(nama, big ? 'app-foto-preview' : '', big ? 26 : 15);
            } else {
                if (t.id === 'aparat-foto-preview') fotoImg = document.getElementById('aparat-foto-preview');
                t.outerHTML = avatarHtml(nama, big ? 'app-foto-preview' : '', big ? 26 : 15);
            }
        }, true);

        function setFotoPreview(path, nama) {
            fotoAvatar.title = nama || '';
            fotoAvatar.textContent = initials(nama);
            var cs = avatarColors(nama || '');
            fotoAvatar.style.background = 'linear-gradient(135deg,' + cs[0] + ',' + cs[1] + ')';
            if (path) {
                var src = resPath(path);
                fotoImg.src = src;
                fotoImg.style.display = '';
                fotoImg.setAttribute('data-preview', src);
                fotoImg.setAttribute('data-name', nama || '');
                fotoAvatar.style.display = 'none';
                fotoWrap.classList.remove('d-none');
                fotoRemove.classList.remove('d-none');
            } else {
                fotoImg.removeAttribute('src');
                fotoImg.style.display = 'none';
                fotoAvatar.style.display = 'flex';
                fotoWrap.classList.remove('d-none');
                fotoRemove.classList.add('d-none');
            }
        }

        var table = new App.JsonTable({
            selector: '#tbl-aparat',
            module: 'perangkat',
            perPage: 10,
            search: '#search-aparat',
            rowClick: true,
            filters: [{
                    key: 'jabatan',
                    label: 'Semua Jabatan',
                    select: '#filter-jabatan'
                },
                {
                    key: 'jenis',
                    label: 'Semua Jenis',
                    select: '#filter-jenis'
                }
            ],
            columns: [{
                    key: 'no',
                    label: '#',
                    format: function(row, v) {
                        return row.idx + 1;
                    }
                },
                {
                    key: 'foto',
                    label: 'Foto',
                    format: function(row, v) {
                        return fotoCell(v, row.nama);
                    }
                },
                {
                    key: 'nama',
                    label: 'Nama'
                },
                {
                    key: 'jabatan',
                    label: 'Jabatan',
                    type: 'badge'
                }
            ],
            actions: ['detail', 'edit'],
            onDetail: function(row) {
                var body = document.getElementById('detail-aparat-body');
                body.innerHTML =
                    '<tr><th style="width:120px" class="text-muted">Foto</th><td>' + (row.foto ? '<img src="' + App.esc(resPath(row.foto)) + '" class="app-foto-preview" data-preview="' + App.esc(resPath(row.foto)) + '" data-name="' + App.esc(row.nama) + '" alt="Foto">' : avatarHtml(row.nama, 'app-foto-preview', 26)) + '</td></tr>' +
                    '<tr><th style="width:120px" class="text-muted">Nama</th><td class="fw-bold">' + App.esc(row.nama) + '</td></tr>' +
                    '<tr><th class="text-muted">Jabatan</th><td>' + App.esc(row.jabatan) + '</td></tr>';
                App.showModal(document.getElementById('modal-detail-aparat'));
            },
            onEdit: function(row) {
                document.getElementById('modal-aparat-title').textContent = 'Edit Aparat';
                formAparat.index.value = row.idx;
                formAparat.nama.value = row.nama;
                formAparat.jabatan.value = row.jabatan;
                formAparat.foto.value = row.foto || '';
                setFotoPreview(row.foto || '', row.nama);
                App.showModal(modalAparat);
            }
        });

        document.getElementById('btn-add-aparat').addEventListener('click', function() {
            document.getElementById('modal-aparat-title').textContent = 'Tambah Aparat';
            formAparat.reset();
            setFotoPreview('', '');
            App.showModal(modalAparat);
        });

        fotoInput.addEventListener('change', function() {
            App.uploadFile(this, function(res) {
                if (res) {
                    formAparat.foto.value = res.path;
                    setFotoPreview(res.path);
                } else {
                    fotoInput.value = '';
                }
            });
        });

        fotoRemove.addEventListener('click', function() {
            formAparat.foto.value = '';
            fotoInput.value = '';
            setFotoPreview('');
        });

        formAparat.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!formAparat.checkValidity()) {
                formAparat.reportValidity();
                return;
            }
            App.postJSON('../admin/api.php', {
                action: 'save_row',
                module: 'perangkat',
                data: {
                    nama: formAparat.nama.value,
                    jabatan: formAparat.jabatan.value,
                    foto: formAparat.foto.value,
                    index: formAparat.index.value
                }
            }).then(function(res) {
                if (res.ok) {
                    App.hideModal(modalAparat);
                    App.toast('Aparat berhasil disimpan.', 'success', 'Berhasil');
                    table.reload();
                } else {
                    App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
                }
            }).catch(function() {
                App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
            });
        });
    });

    /* ---------- Kepala Pekon ---------- */
    document.addEventListener('DOMContentLoaded', function() {
        var formK = document.getElementById('form-kepala');
        var btnK = document.getElementById('btn-save-kepala');
        var hFotoK = formK.querySelector('[name="kp_foto"]');
        var imgK = document.getElementById('kp-foto-preview');
        var inputK = document.getElementById('kp-foto-input');

        inputK.addEventListener('change', function() {
            App.uploadFile(this, function(res) {
                if (res) {
                    hFotoK.value = res.path;
                    if (imgK) {
                        if (imgK.tagName === 'DIV') {
                            var img = document.createElement('img');
                            img.className = 'app-foto-preview';
                            img.id = 'kp-foto-preview';
                            img.setAttribute('data-preview', res.path);
                            img.src = '../' + res.path;
                            imgK.replaceWith(img);
                            imgK = img;
                        } else {
                            imgK.src = '../' + res.path;
                            imgK.setAttribute('data-preview', '../' + res.path);
                        }
                    }
                } else {
                    inputK.value = '';
                }
            });
        });

        formK.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!formK.checkValidity()) {
                formK.reportValidity();
                return;
            }
            btnK.disabled = true;
            App.postJSON('../admin/api.php', {
                action: 'save',
                module: 'kepala_pekon',
                data: {
                    nama: formK.kp_nama.value,
                    jabatan: formK.kp_jabatan.value,
                    foto: hFotoK.value,
                    sambutan: formK.kp_sambutan.value
                }
            }).then(function(res) {
                btnK.disabled = false;
                if (res.ok) App.toast('Kepala pekon berhasil disimpan.', 'success', 'Berhasil');
                else App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
            }).catch(function() {
                btnK.disabled = false;
                App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
            });
        });
    });
</script>