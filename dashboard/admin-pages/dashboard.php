<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$pekonData    = include dirname(__DIR__, 2) . '/includes/pekon.php';
$demoData     = include dirname(__DIR__, 2) . '/includes/demografi.php';
$potensiData  = include dirname(__DIR__, 2) . '/includes/potensi.php';
$apbData      = include dirname(__DIR__, 2) . '/includes/apbpekon.php';
$perangkat    = include dirname(__DIR__, 2) . '/includes/perangkat.php';

function dash_fmt($v, $dec = 0) {
    return number_format((float)$v, $dec, ',', '.');
}
function dash_rp($v) {
    return 'Rp ' . number_format((float)$v, 0, ',', '.');
}

$kepalaFoto = $pekonData['kepala_pekon']['foto'] ?? '';
$kepalaFotoExist = $kepalaFoto !== '' && file_exists(dirname(__DIR__, 2) . '/' . $kepalaFoto);
?>
<div class="page-heading">
    <h3>Dashboard</h3>
    <p class="text-subtitle text-muted">Ringkasan data <?= $pekonData['nama'] ?? 'Pekon' ?> <?= $pekonData['tahun'] ?? '' ?></p>
</div>

<section class="section">
    <!-- Kartu statistik -->
    <div class="row gy-3">
        <div class="col-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-primary bg-opacity-10 text-primary"><i class="bi bi-people-fill fs-4"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold"><?= dash_fmt($demoData['total_jiwa'] ?? 0) ?></h5>
                        <span class="text-muted small">Total Jiwa</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-house-heart-fill fs-4"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold"><?= dash_fmt($demoData['jumlah_kk'] ?? 0) ?></h5>
                        <span class="text-muted small">Kepala Keluarga</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success"><i class="bi bi-bounding-box-circles fs-4"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold"><?= dash_fmt($demoData['luas_wilayah_ha'] ?? 0) ?> Ha</h5>
                        <span class="text-muted small">Luas Wilayah</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-warning bg-opacity-10 text-warning"><i class="bi bi-trophy-fill fs-4"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold"><?= htmlspecialchars($potensiData['idm_status'] ?? '-') ?></h5>
                        <span class="text-muted small">Status IDM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail -->
    <div class="row mt-3 gy-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge text-primary"></i>
                    <h6 class="mb-0">Kepala Pekon</h6>
                </div>
                <div class="card-body d-flex align-items-center gap-3">
                    <?php if ($kepalaFotoExist): ?>
                    <img src="<?= htmlspecialchars($kepalaFoto) ?>" alt="Foto Kepala Pekon" class="rounded-circle" style="width:64px;height:64px;object-fit:cover" data-preview="<?= htmlspecialchars($kepalaFoto) ?>">
                    <?php else: ?>
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted" style="width:64px;height:64px"><i class="bi bi-person fs-2"></i></div>
                    <?php endif; ?>
                    <div>
                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($pekonData['kepala_pekon']['nama'] ?? '-') ?></h6>
                        <span class="text-muted small"><?= htmlspecialchars($pekonData['kepala_pekon']['jabatan'] ?? '-') ?></span>
                    </div>
                </div>
                <div class="card-footer bg-transparent small text-muted">
                    <?= htmlspecialchars($pekonData['nama'] ?? '') ?> — <?= htmlspecialchars($pekonData['kecamatan'] ?? '') ?>, <?= htmlspecialchars($pekonData['kabupaten'] ?? '') ?>, <?= htmlspecialchars($pekonData['provinsi'] ?? '') ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-wallet2 text-success"></i>
                    <h6 class="mb-0">Anggaran <?= $apbData['tahun'] ?? '' ?></h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Total Pendapatan</span>
                        <strong class="text-success"><?= dash_rp($apbData['pendapatan']['total'] ?? 0) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Total Belanja</span>
                        <strong class="text-danger"><?= dash_rp($apbData['belanja']['total'] ?? 0) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Pembiayaan Netto</span>
                        <strong><?= dash_rp($apbData['pembiayaan']['pembiayaan_netto'] ?? 0) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-tree text-success"></i>
                    <h6 class="mb-0">Potensi Lahan (Hektar)</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Tumpang Sari (Kopi, Lada, Cengkeh)</span>
                        <strong><?= dash_fmt($potensiData['tumpang_sari'] ?? 0) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Sawah</span>
                        <strong><?= dash_fmt($potensiData['sawah'] ?? 0) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Jagung</span>
                        <strong><?= dash_fmt($potensiData['jagung'] ?? 0) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-building text-secondary"></i>
                    <h6 class="mb-0">Aparat &amp; Lembaga</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Total Perangkat</span>
                        <strong><?= count($perangkat) ?> orang</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Laki-laki</span>
                        <strong><?= dash_fmt($demoData['laki_laki'] ?? 0) ?> jiwa</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Perempuan</span>
                        <strong><?= dash_fmt($demoData['perempuan'] ?? 0) ?> jiwa</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>