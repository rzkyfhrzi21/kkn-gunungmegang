<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$pekonData = include dirname(__DIR__, 2) . '/includes/pekon.php';
$kepala    = $pekonData['kepala_pekon'];
$kontak    = $pekonData['kontak'];
$foto      = $kepala['foto'] ?? '';
$fotoExist = $foto !== '' && file_exists(dirname(__DIR__, 2) . '/' . $foto);
?>
<div class="page-heading">
    <h3>Profil Pekon</h3>
    <p class="text-subtitle text-muted">Identitas umum, kepala pekon, dan kontak — disimpan ke <code>includes/pekon.php</code></p>
</div>

<section class="section">
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-globe2 text-primary"></i>
            <h6 class="mb-0">Data Profil Pekon</h6>
        </div>
        <div class="card-body">
            <form id="form-pekon" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Pekon</label>
                    <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($pekonData['nama']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tahun Anggaran</label>
                    <input type="text" class="form-control" name="tahun" value="<?= htmlspecialchars($pekonData['tahun']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" class="form-control" name="kecamatan" value="<?= htmlspecialchars($pekonData['kecamatan']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kabupaten</label>
                    <input type="text" class="form-control" name="kabupaten" value="<?= htmlspecialchars($pekonData['kabupaten']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Provinsi</label>
                    <input type="text" class="form-control" name="provinsi" value="<?= htmlspecialchars($pekonData['provinsi']) ?>" required>
                </div>

                <div class="col-12 border-top pt-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-badge me-1"></i>Kepala Pekon</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Kepala Pekon</label>
                    <input type="text" class="form-control" name="kp_nama" value="<?= htmlspecialchars($kepala['nama']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan</label>
                    <input type="text" class="form-control" name="kp_jabatan" value="<?= htmlspecialchars($kepala['jabatan']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto Kepala Pekon</label>
                    <input type="file" id="foto-kepala" class="form-control"
                        accept=".png,.jpg,.jpeg,.gif,.webp,.bmp,.ico,.heic,.heif,.jfif,image/*">
                    <div class="app-upload-hint">Maksimal 2 MB (PNG, JPG, GIF, WEBP, ICO, HEIC/HEIF, dll).</div>
                    <input type="hidden" name="kp_foto" value="<?= htmlspecialchars($foto) ?>">
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <?php if ($fotoExist): ?>
                    <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Kepala Pekon" class="app-foto-preview"
                        data-preview="<?= htmlspecialchars($foto) ?>" id="foto-preview">
                    <?php else: ?>
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted app-foto-preview" id="foto-preview">
                        <i class="bi bi-person fs-3"></i>
                    </div>
                    <?php endif; ?>
                    <span class="ms-2 text-muted small">Klik foto untuk preview</span>
                </div>

                <div class="col-12 border-top pt-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-telephone me-1"></i>Kontak &amp; Lokasi</h6>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telepon / WhatsApp</label>
                    <input type="text" class="form-control" name="kontak_telepon" value="<?= htmlspecialchars($kontak['telepon']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kode Lokasi (Plus Code / Alamat)</label>
                    <input type="text" class="form-control" name="kontak_maps_code" value="<?= htmlspecialchars($kontak['maps_code']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Link Google Maps</label>
                    <input type="url" class="form-control" name="kontak_maps_link" value="<?= htmlspecialchars($kontak['maps_link']) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Embed Peta (Kode iframe Google Maps)</label>
                    <textarea class="form-control" name="kontak_maps_embed" id="kontak_maps_embed" rows="2" placeholder="Tempel kode iframe (mis. &lt;iframe src=&quot;https://www.google.com/maps/embed?...&quot;...) atau langsung URL embednya"><?= htmlspecialchars($kontak['maps_embed'] ?? '') ?></textarea>
                    <div class="app-upload-hint">Buka Google Maps &rarr; Bagikan &rarr; Sematkan peta &rarr; salin kode iframe, lalu tempel di sini. Format URL atau kode iframe keduanya diterima.</div>
                </div>
                <div class="col-12" id="maps-preview-wrap" style="display:none;">
                    <label class="form-label">Pratinjau Peta</label>
                    <div class="border rounded overflow-hidden">
                        <iframe id="maps-preview" src="" style="width:100%;height:320px;border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="btn-save-pekon">
                        <i class="bi bi-check-lg"></i> Simpan Profil Pekon
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form-pekon');
    var btn = document.getElementById('btn-save-pekon');
    var hFoto = form.querySelector('[name="kp_foto"]');
    var imgPrev = document.getElementById('foto-preview');
    var inputFoto = document.getElementById('foto-kepala');
    var mapsEmbed = document.getElementById('kontak_maps_embed');
    var mapsPreviewWrap = document.getElementById('maps-preview-wrap');
    var mapsPreview = document.getElementById('maps-preview');

    function getEmbedUrl(val) {
        var m = /src\s*=\s*["']([^"']+)["']/i.exec(val || '');
        return m ? m[1] : (val || '').trim();
    }
    function updateMapPreview() {
        var url = getEmbedUrl(mapsEmbed.value);
        if (/^https?:\/\//i.test(url)) {
            mapsPreview.src = url;
            mapsPreviewWrap.style.display = '';
        } else {
            mapsPreview.removeAttribute('src');
            mapsPreviewWrap.style.display = 'none';
        }
    }
    mapsEmbed.addEventListener('input', updateMapPreview);
    updateMapPreview();

    inputFoto.addEventListener('change', function () {
        App.uploadFile(this, function (res) {
            if (res) {
                hFoto.value = res.path;
                if (imgPrev) {
                    if (imgPrev.tagName === 'DIV') {
                        var img = document.createElement('img');
                        img.className = 'app-foto-preview';
                        img.id = 'foto-preview';
                        img.setAttribute('data-preview', res.path);
                        img.src = res.path;
                        imgPrev.replaceWith(img);
                        imgPrev = img;
                    } else {
                        imgPrev.src = res.path;
                        imgPrev.setAttribute('data-preview', res.path);
                    }
                }
            } else {
                inputFoto.value = '';
            }
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.reportValidity(); return; }
        btn.disabled = true;
        var payload = {
            nama: form.nama.value, kecamatan: form.kecamatan.value,
            kabupaten: form.kabupaten.value, provinsi: form.provinsi.value,
            tahun: form.tahun.value,
            kepala_pekon: { nama: form.kp_nama.value, foto: hFoto.value, jabatan: form.kp_jabatan.value },
            kontak: { telepon: form.kontak_telepon.value, maps_code: form.kontak_maps_code.value, maps_link: form.kontak_maps_link.value, maps_embed: getEmbedUrl(mapsEmbed.value) }
        };
        App.postJSON('../admin/api.php', { action: 'save', module: 'pekon', data: payload })
            .then(function (res) {
                btn.disabled = false;
                if (res.ok) App.toast('Profil pekon berhasil disimpan.', 'success', 'Berhasil');
                else App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
            })
            .catch(function () { btn.disabled = false; App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });
});
</script>