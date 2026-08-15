<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$pekonData = include dirname(__DIR__, 2) . '/includes/pekon.php';
$kontak    = $pekonData['kontak'];
?>
<div class="page-heading">
    <h3>Profil Pekon</h3>
    <p class="text-subtitle text-muted">Identitas umum, kontak, dan peta lokasi pekon</p>
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
                    <h6 class="fw-bold mb-3"><i class="bi bi-telephone me-1"></i>Kontak &amp; Lokasi</h6>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telepon / WhatsApp</label>
                    <input type="text" class="form-control" name="kontak_telepon" value="<?= htmlspecialchars($kontak['telepon']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" name="kontak_maps_code" rows="3" readonly required><?= htmlspecialchars($kontak['maps_code']) ?></textarea>
                    <div class="app-upload-hint">Terisi otomatis dari link Google Maps.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Link Google Maps</label>
                    <div class="input-group">
                        <input type="url" class="form-control" name="kontak_maps_link" id="kontak_maps_link" value="<?= htmlspecialchars($kontak['maps_link']) ?>">
                        <button type="button" class="btn btn-outline-primary" id="btn-resolve-maps" title="Isi alamat & peta otomatis dari link">
                            <i class="bi bi-magic"></i> Isi Otomatis
                        </button>
                    </div>
                    <div class="app-upload-hint">Tempel link dari menu "Bagikan" Google Maps (mis. <code>https://maps.app.goo.gl/...</code>), lalu klik <b>Isi Otomatis</b> — alamat &amp; peta terisi sendiri.</div>
                </div>
                <input type="hidden" name="kontak_maps_embed" id="kontak_maps_embed" value="<?= htmlspecialchars($kontak['maps_embed'] ?? '') ?>">
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
    var mapsLink = document.getElementById('kontak_maps_link');
    var mapsEmbed = document.getElementById('kontak_maps_embed');
    var mapsCode = form.querySelector('[name="kontak_maps_code"]');
    var mapsPreviewWrap = document.getElementById('maps-preview-wrap');
    var mapsPreview = document.getElementById('maps-preview');
    var btnResolve = document.getElementById('btn-resolve-maps');

    function updateMapPreview() {
        var url = mapsEmbed.value.trim();
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

    function doResolve() {
        var link = mapsLink.value.trim();
        if (!/^https?:\/\//i.test(link)) {
            App.toast('Tempel link Google Maps dulu (menu Bagikan).', 'error', 'Gagal');
            return;
        }
        btnResolve.disabled = true;
        var ic = btnResolve.querySelector('i');
        if (ic) ic.className = 'bi bi-arrow-repeat';
        App.postJSON('../admin/api.php', { action: 'resolve_maps', link: link })
            .then(function (res) {
                if (res.ok) {
                    if (res.address) mapsCode.value = res.address;
                    mapsEmbed.value = res.embed;
                    updateMapPreview();
                    App.toast('Peta & alamat terisi otomatis dari link.', 'success', 'Berhasil');
                } else {
                    App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal memproses link.', 'error', 'Gagal');
                }
            })
            .catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); })
            .then(function () {
                btnResolve.disabled = false;
                if (ic) ic.className = 'bi bi-magic';
            });
    }
    btnResolve.addEventListener('click', doResolve);
    var mapsTimer = null;
    mapsLink.addEventListener('input', function () {
        clearTimeout(mapsTimer);
        mapsTimer = setTimeout(doResolve, 900);
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.reportValidity(); return; }
        btn.disabled = true;
        var payload = {
            nama: form.nama.value, kecamatan: form.kecamatan.value,
            kabupaten: form.kabupaten.value, provinsi: form.provinsi.value,
            tahun: form.tahun.value,
            kontak: { telepon: form.kontak_telepon.value, maps_code: mapsCode.value, maps_link: mapsLink.value, maps_embed: mapsEmbed.value.trim() }
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
