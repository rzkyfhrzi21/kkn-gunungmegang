<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$demoData = include dirname(__DIR__, 2) . '/includes/demografi.php';
$batas    = $demoData['batas_wilayah'];
?>
<div class="page-heading">
    <h3>Demografi &amp; Wilayah</h3>
    <p class="text-subtitle text-muted">Kependudukan, luas wilayah, dan batas pekon</p>
</div>

<section class="section">
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-people-fill text-primary"></i>
            <h6 class="mb-0">Data Demografi</h6>
        </div>
        <div class="card-body">
            <form id="form-demografi" class="row g-3">
                <div class="col-12 border-bottom pb-2">
                    <h6 class="fw-bold mb-0"><i class="bi bi-person-hearts me-1"></i>Kependudukan</h6>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Laki-laki (jiwa)</label>
                    <input type="number" min="0" class="form-control" name="laki_laki" value="<?= (int)$demoData['laki_laki'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Perempuan (jiwa)</label>
                    <input type="number" min="0" class="form-control" name="perempuan" value="<?= (int)$demoData['perempuan'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Total Jiwa</label>
                    <input type="number" min="0" class="form-control" name="total_jiwa" value="<?= (int)$demoData['total_jiwa'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jumlah KK</label>
                    <input type="number" min="0" class="form-control" name="jumlah_kk" value="<?= (int)$demoData['jumlah_kk'] ?>" required>
                </div>

                <div class="col-12 border-bottom pb-2 pt-2">
                    <h6 class="fw-bold mb-0"><i class="bi bi-bounding-box-circles me-1"></i>Wilayah</h6>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Luas Wilayah (km²)</label>
                    <input type="number" min="0" step="0.01" class="form-control" name="luas_wilayah_km2" value="<?= htmlspecialchars(number_format((float)$demoData['luas_wilayah_km2'], 2, '.', '')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Luas Wilayah (Ha)</label>
                    <input type="number" min="0" class="form-control" name="luas_wilayah_ha" value="<?= (int)$demoData['luas_wilayah_ha'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jarak ke Kecamatan (km)</label>
                    <input type="number" min="0" class="form-control" name="jarak_kecamatan_km" value="<?= (int)$demoData['jarak_kecamatan_km'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Waktu ke Kecamatan (menit)</label>
                    <input type="number" min="0" class="form-control" name="waktu_kecamatan_menit" value="<?= (int)$demoData['waktu_kecamatan_menit'] ?>" required>
                </div>

                <div class="col-12 border-bottom pb-2 pt-2">
                    <h6 class="fw-bold mb-0"><i class="bi bi-signpost-split me-1"></i>Batas Wilayah</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Utara</label>
                    <input type="text" class="form-control" name="batas_utara" value="<?= htmlspecialchars($batas['utara']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Timur</label>
                    <input type="text" class="form-control" name="batas_timur" value="<?= htmlspecialchars($batas['timur']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Selatan</label>
                    <input type="text" class="form-control" name="batas_selatan" value="<?= htmlspecialchars($batas['selatan']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Barat</label>
                    <input type="text" class="form-control" name="batas_barat" value="<?= htmlspecialchars($batas['barat']) ?>" required>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="btn-save-demografi">
                        <i class="bi bi-check-lg"></i> Simpan Demografi
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form-demografi');
    var btn = document.getElementById('btn-save-demografi');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.reportValidity(); return; }
        btn.disabled = true;
        var payload = {
            laki_laki: form.laki_laki.value, perempuan: form.perempuan.value,
            total_jiwa: form.total_jiwa.value, jumlah_kk: form.jumlah_kk.value,
            luas_wilayah_km2: form.luas_wilayah_km2.value, luas_wilayah_ha: form.luas_wilayah_ha.value,
            jarak_kecamatan_km: form.jarak_kecamatan_km.value, waktu_kecamatan_menit: form.waktu_kecamatan_menit.value,
            batas_wilayah: {
                utara: form.batas_utara.value, timur: form.batas_timur.value,
                selatan: form.batas_selatan.value, barat: form.batas_barat.value
            }
        };
        App.postJSON('../admin/api.php', { action: 'save', module: 'demografi', data: payload })
            .then(function (res) {
                btn.disabled = false;
                if (res.ok) App.toast('Data demografi berhasil disimpan.', 'success', 'Berhasil');
                else App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
            })
            .catch(function () { btn.disabled = false; App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });
});
</script>