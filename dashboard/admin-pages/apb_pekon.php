<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$allApb = include dirname(__DIR__, 2) . '/includes/apbpekon.php';
$tahun  = (int)($_GET['tahun'] ?? 0);
$isDetail = isset($allApb[$tahun]);

$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
if ($isDetail):
    $apbData = $allApb[$tahun];
    $pendapatanKeys = ['alokasi_dana_pekon', 'dana_desa', 'bagi_hasil_pajak', 'bantuan_provinsi', 'pendapatan_lain'];
    $belanjaKeys    = ['penyelenggaraan_pemerintahan', 'pembangunan_pekon', 'pembinaan_kemasyarakatan', 'pemberdayaan_masyarakat', 'penanggulangan_bencana'];
    $apbLabels      = [
        'pendapatan' => ['Alokasi Dana Pekon', 'Dana Desa', 'Bagi Hasil Pajak', 'Bantuan Provinsi', 'Pendapatan Lain'],
        'belanja'    => ['Penyelenggaraan Pem.', 'Pembangunan Pekon', 'Pembinaan Masyarakat', 'Pemberdayaan Masyarakat', 'Penanggulangan Bencana'],
    ];
    $apbPendapatan = $apbData['pendapatan'] ?? [];
    $apbBelanja    = $apbData['belanja'] ?? [];
    $belanjaLabels = [];
    $belanjaValues = [];
    foreach ($belanjaKeys as $i => $k) {
        $belanjaLabels[] = $apbLabels['belanja'][$i];
        $belanjaValues[] = round((float)($apbBelanja[$k] ?? 0));
    }
    $penerimaanSeries = [];
    $belanjaSeries    = [];
    foreach ($pendapatanKeys as $i => $k) {
        $penerimaanSeries[] = round((float)($apbPendapatan[$k] ?? 0));
        $belanjaSeries[]    = round((float)($apbBelanja[$belanjaKeys[$i]] ?? 0));
    }
    $chartApb = [
        'labels'        => $apbLabels['pendapatan'],
        'penerimaan'    => $penerimaanSeries,
        'belanja'       => $belanjaSeries,
        'belanjaLabels' => $belanjaLabels,
        'belanjaValues' => $belanjaValues,
    ];
endif;
?>
<div class="page-heading">
    <h3>APB Pekon<?= $isDetail ? ' Tahun ' . $tahun : '' ?></h3>
    <p class="text-subtitle text-muted"><?= $isDetail ? 'Rincian pendapatan, belanja, dan pembiayaan tahun anggaran ' . $tahun : 'Kelola anggaran pendapatan dan belanja pekon per tahun anggaran' ?></p>
</div>

<?php if (!$isDetail):
    $listYears = array_keys($allApb);
    $listYearsAsc = array_reverse($listYears);
    $chartKompilasi = ['tahun' => [], 'pendapatan' => [], 'belanja' => [], 'pembiayaan' => [], 'surplus' => []];
    foreach ($listYearsAsc as $yr) {
        $pen = round((float)($allApb[$yr]['pendapatan']['total'] ?? 0));
        $bel = round((float)($allApb[$yr]['belanja']['total'] ?? 0));
        $chartKompilasi['tahun'][]      = (int)$yr;
        $chartKompilasi['pendapatan'][] = $pen;
        $chartKompilasi['belanja'][]    = $bel;
        $chartKompilasi['pembiayaan'][] = round((float)($allApb[$yr]['pembiayaan']['pembiayaan_netto'] ?? 0));
        $chartKompilasi['surplus'][]    = $pen - $bel;
    }
?>
<section class="section">
    <div class="row gy-3 mb-3">
        <div class="col-lg-7">
            <div class="card h-100 mb-0">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart text-primary"></i>
                    <h6 class="mb-0">Perbandingan APB per Tahun Anggaran</h6>
                </div>
                <div class="card-body py-2"><div id="apb-chart-kompilasi"></div></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100 mb-0">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-activity text-warning"></i>
                        <h6 class="mb-0">Surplus / Defisit</h6>
                    </div>
                    <span class="badge bg-light text-muted small py-1 px-2 border" style="font-size:11px">
                        <span class="text-success fw-bold">● Hijau</span> Surplus &nbsp;|&nbsp; <span class="text-danger fw-bold">● Merah</span> Defisit
                    </span>
                </div>
                <div class="card-body py-2"><div id="apb-chart-surplus"></div></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-wallet2 text-success"></i>
                <h6 class="mb-0">Daftar Tahun Anggaran</h6>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="btn-add-tahun"><i class="bi bi-plus-lg"></i> Tambah Tahun</button>
        </div>
        <div class="card-body">
            <div class="app-table-wrap">
                <table class="table table-hover" id="tbl-tahun">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:60px">No</th>
                            <th>Tahun Anggaran</th>
                            <th class="text-right">Pendapatan</th>
                            <th class="text-right">Belanja</th>
                            <th class="text-right">Pembiayaan</th>
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

<!-- Modal tambah tahun anggaran -->
<div class="modal fade" id="modal-tahun" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-tahun">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tahun Anggaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Tahun Anggaran</label>
                    <input type="number" min="2000" max="2100" class="form-control" name="tahun" required>
                    <div class="app-upload-hint">Tahun anggaran baru dibuat dengan nominal kosong, lalu isi rinciannya pada halaman detail.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal konfirmasi hapus tahun -->
<div class="modal fade" id="modal-delete-tahun" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-delete-tahun-text">Apakah Anda yakin ingin menghapus tahun anggaran ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete-tahun">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalTahun = document.getElementById('modal-tahun');
    var formTahun = document.getElementById('form-tahun');
    var modalDelete = document.getElementById('modal-delete-tahun');
    var pendingDelete = null;

    document.getElementById('btn-add-tahun').addEventListener('click', function () {
        formTahun.reset();
        App.showModal(modalTahun);
    });

    formTahun.addEventListener('submit', function (e) {
        e.preventDefault();
        App.postJSON('../admin/api.php', {
            action: 'save', module: 'apb_tahun', data: { tahun: formTahun.tahun.value }
        }).then(function (res) {
            if (res.ok) {
                App.hideModal(modalTahun);
                App.toast('Tahun anggaran ' + formTahun.tahun.value + ' berhasil dibuat.', 'success', 'Berhasil');
                window.location.href = '?page=APB Pekon&tahun=' + res.saved.tahun;
            } else {
                App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
            }
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });

    var tableTahun = new App.JsonTable({
        selector: '#tbl-tahun',
        module: 'apb_tahun',
        perPage: 10,
        columns: [
            { key: 'no', label: 'No', align: 'text-center' },
            { key: 'tahun', label: 'Tahun Anggaran' },
            { key: 'pendapatan', label: 'Pendapatan', type: 'currency', align: 'text-right' },
            { key: 'belanja', label: 'Belanja', type: 'currency', align: 'text-right' },
            { key: 'pembiayaan', label: 'Pembiayaan', type: 'currency', align: 'text-right' }
        ],
        actions: ['edit', 'delete'],
        onEdit: function (row) {
            window.location.href = '?page=APB Pekon&tahun=' + row.tahun;
        },
        onDelete: function (row) {
            if (tableTahun.total <= 1) {
                App.toast('Minimal harus ada 1 tahun anggaran karena wajib ditampilkan di website.', 'warning', 'Tidak Dapat Dihapus');
                return;
            }
            pendingDelete = row.tahun;
            document.getElementById('modal-delete-tahun-text').textContent =
                'Apakah Anda yakin ingin menghapus tahun anggaran ' + row.tahun + ' beserta seluruh rinciannya?';
            App.showModal(modalDelete);
        }
    });

    document.getElementById('btn-confirm-delete-tahun').addEventListener('click', function () {
        if (pendingDelete === null) return;
        var tahun = pendingDelete;
        App.postJSON('../admin/api.php', {
            action: 'delete', module: 'apb_tahun', data: { tahun: tahun }
        }).then(function (res) {
            if (res.ok) {
                App.hideModal(modalDelete);
                App.toast('Tahun anggaran ' + tahun + ' berhasil dihapus.', 'success', 'Berhasil');
                tableTahun.reload();
            } else {
                App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menghapus.', 'error', 'Gagal');
            }
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });
});
</script>

<script src="assets/extensions/apexcharts/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.ApexCharts) return;

    function rpCompact(v) {
        v = Number(v) || 0;
        var prefix = v < 0 ? 'Rp -' : 'Rp ';
        var abs = Math.abs(v);
        if (abs >= 1e9) return prefix + (abs / 1e9).toFixed(1).replace('.', ',') + ' M';
        if (abs >= 1e6) return prefix + (abs / 1e6).toFixed(1).replace('.', ',') + ' jt';
        if (abs >= 1e3) return prefix + (abs / 1e3).toFixed(0) + ' rb';
        return prefix + Math.round(abs);
    }
    function rp(v) {
        v = Number(v) || 0;
        var prefix = v < 0 ? '-Rp ' : 'Rp ';
        return prefix + Math.abs(Math.round(v)).toLocaleString('id-ID');
    }
    function noData(el) {
        el.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data</div>';
    }

    var chartKompilasi = <?= json_encode($chartKompilasi, $jsonFlags) ?>;
    var elKompilasi = document.getElementById('apb-chart-kompilasi');
    var numYears = (chartKompilasi.tahun || []).length;
    var hasData = numYears > 0 &&
        chartKompilasi.pendapatan.some(function (v) { return v > 0; });
    if (elKompilasi && !hasData) { noData(elKompilasi); }
    else if (elKompilasi) {
        new ApexCharts(elKompilasi, {
            chart: {
                type: 'bar',
                height: 260,
                toolbar: { show: false },
                stacked: false,
                parentHeightOffset: 0
            },
            series: [
                { name: 'Pendapatan', data: chartKompilasi.pendapatan },
                { name: 'Belanja', data: chartKompilasi.belanja },
                { name: 'Pembiayaan', data: chartKompilasi.pembiayaan }
            ],
            colors: ['#10b981', '#ef4444', '#3b82f6'],
            plotOptions: {
                bar: {
                    columnWidth: numYears === 1 ? '38%' : (numYears <= 2 ? '48%' : '58%'),
                    borderRadius: 4
                }
            },
            xaxis: { categories: chartKompilasi.tahun },
            legend: { position: 'top', horizontalAlign: 'right', offsetTop: -8 },
            dataLabels: { enabled: false },
            yaxis: { labels: { formatter: function (v) { return rpCompact(v); } } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 3, padding: { top: 0, bottom: 0 } },
            tooltip: { y: { formatter: function (v) { return rp(v); } } }
        }).render();
    }

    var elSurplus = document.getElementById('apb-chart-surplus');
    if (elSurplus) {
        var surplusData = chartKompilasi.surplus || [];
        var hasSurplus  = surplusData.some(function (v) { return v !== 0; });
        if (!hasSurplus) { noData(elSurplus); }
        else {
            new ApexCharts(elSurplus, {
                chart: {
                    type: 'bar',
                    height: 260,
                    toolbar: { show: false },
                    parentHeightOffset: 0
                },
                series: [{ name: 'Surplus / Defisit', data: surplusData }],
                colors: ['#10b981'],
                plotOptions: {
                    bar: {
                        columnWidth: numYears === 1 ? '22%' : (numYears <= 2 ? '36%' : '48%'),
                        borderRadius: 5,
                        colors: {
                            ranges: [
                                { from: -1e15, to: -0.01, color: '#ef4444' },
                                { from: 0,     to:  1e15, color: '#10b981' }
                            ]
                        }
                    }
                },
                xaxis: {
                    categories: chartKompilasi.tahun,
                    axisBorder: { show: true, color: '#cbd5e1' },
                    axisTicks: { show: true }
                },
                legend: { show: false },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) { return rpCompact(val); },
                    style: {
                        fontSize: '11px',
                        fontWeight: 600,
                        colors: ['#334155']
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (v) { return rpCompact(v); }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 3,
                    padding: { top: 0, bottom: 0 }
                },
                tooltip: {
                    y: {
                        formatter: function (v) {
                            return (v > 0 ? '+Rp ' : (v < 0 ? '-Rp ' : 'Rp ')) + Math.abs(Math.round(v)).toLocaleString('id-ID');
                        }
                    }
                },
                annotations: {
                    yaxis: [{
                        y: 0,
                        borderColor: '#64748b',
                        strokeDashArray: 3,
                        borderWidth: 1.5,
                        label: { text: '', borderWidth: 0 }
                    }]
                }
            }).render();
        }
    }
});
</script>

<?php else: ?>

<section class="section">
    <a href="?page=APB Pekon" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Tahun</a>

    <div class="row gy-3">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart text-primary"></i>
                    <h6 class="mb-0">Komposisi Belanja APB <?= $tahun ?></h6>
                </div>
                <div class="card-body"><div id="apb-chart-belanja" style="height:320px"></div></div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart text-success"></i>
                    <h6 class="mb-0">Pendapatan vs Belanja per Pos — APB <?= $tahun ?></h6>
                </div>
                <div class="card-body"><div id="apb-chart-apb" style="height:320px"></div></div>
            </div>
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
            <h6 class="mb-0"><?= $t['judul'] ?> — <?= $tahun ?></h6>
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

<script src="assets/extensions/apexcharts/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var TAHUN = <?= (int)$tahun ?>;

    if (window.ApexCharts) {
        var PALETTE = ['#0ea5a4', '#3b82f6', '#f59e0b', '#ef4444', '#10b981'];
        function rpCompact(v) {
            v = Number(v) || 0;
            var prefix = v < 0 ? 'Rp -' : 'Rp ';
            var abs = Math.abs(v);
            if (abs >= 1e9) return prefix + (abs / 1e9).toFixed(1).replace('.', ',') + ' M';
            if (abs >= 1e6) return prefix + (abs / 1e6).toFixed(1).replace('.', ',') + ' jt';
            if (abs >= 1e3) return prefix + (abs / 1e3).toFixed(0) + ' rb';
            return prefix + Math.round(abs);
        }
        function rp(v) {
            v = Number(v) || 0;
            var prefix = v < 0 ? '-Rp ' : 'Rp ';
            return prefix + Math.abs(Math.round(v)).toLocaleString('id-ID');
        }
        function noData(el) {
            el.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data</div>';
        }

        var chartApb = <?= json_encode($chartApb, $jsonFlags) ?>;

        var elBelanja = document.getElementById('apb-chart-belanja');
        var hasBelanja = (chartApb.belanjaValues || []).some(function (v) { return v > 0; });
        if (elBelanja && !hasBelanja) { noData(elBelanja); }
        else if (elBelanja) {
            new ApexCharts(elBelanja, {
                chart: { type: 'donut' },
                series: chartApb.belanjaValues,
                labels: chartApb.belanjaLabels,
                colors: PALETTE,
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Belanja',
                                    formatter: function (w) {
                                        return rpCompact(w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0));
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: { formatter: function (v) { return Number(v).toFixed(1) + '%'; } },
                legend: { position: 'bottom' },
                tooltip: { y: { formatter: function (v) { return rp(v); } } }
            }).render();
        }

        var elApb = document.getElementById('apb-chart-apb');
        if (elApb) {
            new ApexCharts(elApb, {
                chart: { type: 'bar', toolbar: { show: false } },
                series: [
                    { name: 'Pendapatan', data: chartApb.penerimaan },
                    { name: 'Belanja', data: chartApb.belanja }
                ],
                colors: ['#10b981', '#ef4444'],
                plotOptions: { bar: { columnWidth: '55%', borderRadius: 3 } },
                xaxis: { categories: chartApb.labels },
                legend: { position: 'top' },
                dataLabels: { enabled: false },
                yaxis: { labels: { formatter: function (v) { return rpCompact(v); } } },
                tooltip: { y: { formatter: function (v) { return rp(v); } } }
            }).render();
        }
    }

    var modalApb = document.getElementById('modal-apb');
    var formApb = document.getElementById('form-apb');
    var apbTables = [];

    ['pendapatan', 'belanja', 'pembiayaan'].forEach(function (mod) {
        apbTables.push(new App.JsonTable({
            selector: '#tbl-' + mod,
            module: mod,
            perPage: 10,
            filters: [{ key: 'tahun', fixed: TAHUN }],
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
            data: { key: formApb.key.value, nominal: formApb.nominal.value, tahun: TAHUN }
        }).then(function (res) {
            if (res.ok) {
                App.hideModal(modalApb);
                App.toast('Nominal berhasil disimpan.', 'success', 'Berhasil');
                apbTables.forEach(function (t) { t.reload(); });
                window.location.reload();
            } else {
                App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
            }
        }).catch(function () { App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal'); });
    });
});
</script>
<?php endif; ?>