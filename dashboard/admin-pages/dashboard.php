<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$pekonData    = include dirname(__DIR__, 2) . '/includes/pekon.php';
$demoData     = include dirname(__DIR__, 2) . '/includes/demografi.php';
$potensiData  = include dirname(__DIR__, 2) . '/includes/potensi.php';
$apbAll       = include dirname(__DIR__, 2) . '/includes/apbpekon.php';
$apbYears     = array_keys($apbAll);
$apbTahun     = (int)($apbYears ? max($apbYears) : 0);
$apbData      = $apbAll[$apbTahun] ?? [];
$perangkat    = include dirname(__DIR__, 2) . '/includes/perangkat.php';

function dash_fmt($v, $dec = 0)
{
    return number_format((float)$v, $dec, ',', '.');
}
function dash_rp($v)
{
    return 'Rp ' . number_format((float)$v, 0, ',', '.');
}

$kepalaPerangkat = null;
foreach ($perangkat as $p) {
    if (strtolower(trim((string)($p['jabatan'] ?? ''))) === 'kepala pekon') {
        $kepalaPerangkat = $p;
        break;
    }
}
$kepalaNama     = $kepalaPerangkat['nama'] ?? ($pekonData['kepala_pekon']['nama'] ?? '-');
$kepalaJabatan  = $kepalaPerangkat['jabatan'] ?? ($pekonData['kepala_pekon']['jabatan'] ?? '-');
$kepalaFoto     = $kepalaPerangkat['foto'] ?? ($pekonData['kepala_pekon']['foto'] ?? '');
$kepalaFotoShow = '';
if ($kepalaFoto !== '' && file_exists(dirname(__DIR__, 2) . '/' . $kepalaFoto)) {
    $kepalaFotoShow = '../' . $kepalaFoto;
}

/* Data chart (ApexCharts) */
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
$lainKeys   = ['bagi_hasil_pajak', 'bantuan_provinsi', 'pendapatan_lain'];
$lainLabels = ['Bagi Hasil Pajak', 'Bantuan Provinsi', 'Pendapatan Lain'];
$lainValues = [];
foreach ($lainKeys as $i => $k) {
    $lainValues[] = round((float)($apbPendapatan[$k] ?? 0));
}
$chartApb = [
    'labels'        => array_slice($apbLabels['pendapatan'], 0, 2),
    'penerimaan'    => array_slice($penerimaanSeries, 0, 2),
    'belanja'       => array_slice($belanjaSeries, 0, 2),
    'belanjaLabels' => $belanjaLabels,
    'belanjaValues' => $belanjaValues,
];
$chartLahan = ['labels' => [], 'values' => []];
foreach (($potensiData['komoditas'] ?? []) as $kom) {
    $chartLahan['labels'][] = $kom['nama'] ?? '';
    $chartLahan['values'][] = round((float)($kom['nilai'] ?? 0), 2);
}
$chartPenduduk = [
    'labels' => ['Laki-laki', 'Perempuan'],
    'values' => [(int)($demoData['laki_laki'] ?? 0), (int)($demoData['perempuan'] ?? 0)],
];
$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<div class="page-heading">
    <h3>Dashboard</h3>
    <p class="text-subtitle text-muted">Ringkasan data <?= $pekonData['nama'] ?? 'Pekon' ?> <?= $pekonData['tahun'] ?? '' ?></p>
</div>

<section class="section">
    <!-- Kepala Pekon + Statistik -->
    <div class="row gy-3">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge text-primary"></i>
                    <h6 class="mb-0">Kepala Pekon</h6>
                </div>
                <div class="card-body d-flex align-items-center gap-3">
                    <?php if ($kepalaFotoShow !== ''): ?>
                        <img src="<?= htmlspecialchars($kepalaFotoShow) ?>" alt="Foto Kepala Pekon" class="rounded-circle" style="width:64px;height:64px;object-fit:cover" data-preview="<?= htmlspecialchars($kepalaFotoShow) ?>">
                    <?php else: ?>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted" style="width:64px;height:64px"><i class="bi bi-person fs-2"></i></div>
                    <?php endif; ?>
                    <div>
                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($kepalaNama) ?></h6>
                        <span class="text-muted small"><?= htmlspecialchars($kepalaJabatan) ?></span>
                    </div>
                </div>
                <div class="card-footer bg-transparent small text-muted">
                    <?= htmlspecialchars($pekonData['nama'] ?? '') ?> — <?= htmlspecialchars($pekonData['kecamatan'] ?? '') ?>, <?= htmlspecialchars($pekonData['kabupaten'] ?? '') ?>, <?= htmlspecialchars($pekonData['provinsi'] ?? '') ?>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 text-white" style="width:52px;height:52px;background:linear-gradient(135deg,#0ea5a4,#2563eb)"><i class="bi bi-people-fill fs-5"></i></div>
                            <div class="min-w-0">
                                <div class="fw-bold fs-3 lh-1" style="color:#0d9488"><?= dash_fmt($demoData['total_jiwa'] ?? 0) ?></div>
                                <div class="text-muted small mt-1">Total Jiwa</div>
                                <div class="small mt-1" style="color:#94a3b8">L <?= dash_fmt($demoData['laki_laki'] ?? 0) ?> · P <?= dash_fmt($demoData['perempuan'] ?? 0) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 text-white" style="width:52px;height:52px;background:linear-gradient(135deg,#6366f1,#a855f7)"><i class="bi bi-house-heart-fill fs-5"></i></div>
                            <div class="min-w-0">
                                <div class="fw-bold fs-3 lh-1" style="color:#7c3aed"><?= dash_fmt($demoData['jumlah_kk'] ?? 0) ?></div>
                                <div class="text-muted small mt-1">Kepala Keluarga</div>
                                <div class="small mt-1" style="color:#94a3b8">≈ <?= dash_fmt(($demoData['total_jiwa'] ?? 0) / max(1, (int)($demoData['jumlah_kk'] ?? 1)), 1) ?> jiwa/KK</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 text-white" style="width:52px;height:52px;background:linear-gradient(135deg,#10b981,#0891b2)"><i class="bi bi-bounding-box-circles fs-5"></i></div>
                            <div class="min-w-0">
                                <div class="fw-bold fs-3 lh-1" style="color:#059669"><?= dash_fmt($demoData['luas_wilayah_ha'] ?? 0) ?> Ha</div>
                                <div class="text-muted small mt-1">Luas Wilayah</div>
                                <div class="small mt-1" style="color:#94a3b8"><?= dash_fmt($demoData['luas_wilayah_km2'] ?? 0, 2) ?> km²</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik ApexCharts -->
    <div class="row mt-3 gy-3">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart text-primary"></i>
                    <h6 class="mb-0">Komposisi Belanja APB <?= htmlspecialchars($apbTahun) ?></h6>
                </div>
                <div class="card-body">
                    <div id="dash-chart-belanja" style="height:400px"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart text-success"></i>
                    <h6 class="mb-0">Pendapatan vs Belanja per Pos — APB <?= htmlspecialchars($apbTahun) ?></h6>
                </div>
                <div class="card-body">
                    <div id="dash-chart-apb" style="height:400px"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-line text-warning"></i>
                    <h6 class="mb-0">Potensi Lahan (Hektar)</h6>
                </div>
                <div class="card-body">
                    <div id="dash-chart-lahan" style="height:300px"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-people text-info"></i>
                    <h6 class="mb-0">Komposisi Penduduk</h6>
                </div>
                <div class="card-body">
                    <div id="dash-chart-penduduk" style="height:300px"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/extensions/apexcharts/apexcharts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.ApexCharts) return;

        var PALETTE = ['#0ea5a4', '#3b82f6', '#f59e0b', '#ef4444', '#10b981'];

        function rp(v) {
            return 'Rp ' + Math.round(v).toLocaleString('id-ID');
        }

        function rpCompact(v) {
            v = Number(v) || 0;
            if (v >= 1e9) return 'Rp ' + (v / 1e9).toFixed(1).replace('.', ',') + ' M';
            if (v >= 1e6) return 'Rp ' + (v / 1e6).toFixed(1).replace('.', ',') + ' jt';
            if (v >= 1e3) return 'Rp ' + (v / 1e3).toFixed(0) + ' rb';
            return 'Rp ' + Math.round(v);
        }

        function noData(el) {
            el.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data</div>';
        }

        var chartApb = <?= json_encode($chartApb, $jsonFlags) ?>;
        var chartLahan = <?= json_encode($chartLahan, $jsonFlags) ?>;
        var chartPenduduk = <?= json_encode($chartPenduduk, $jsonFlags) ?>;

        /* Donut — Komposisi Belanja */
        var elBelanja = document.getElementById('dash-chart-belanja');
        var hasBelanja = (chartApb.belanjaValues || []).some(function(v) {
            return v > 0;
        });
        if (elBelanja && !hasBelanja) {
            noData(elBelanja);
        } else if (elBelanja) {
            new ApexCharts(elBelanja, {
                chart: {
                    type: 'donut'
                },
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
                                    formatter: function(w) {
                                        return rpCompact(w.globals.seriesTotals.reduce(function(a, b) {
                                            return a + b;
                                        }, 0));
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    formatter: function(v) {
                        return Number(v).toFixed(1) + '%';
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '12px'
                },
                tooltip: {
                    y: {
                        formatter: function(v) {
                            return rp(v);
                        }
                    }
                }
            }).render();
        }

        /* Column — Pendapatan vs Belanja per pos */
        var elApb = document.getElementById('dash-chart-apb');
        if (elApb) {
            new ApexCharts(elApb, {
                chart: {
                    type: 'bar',
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                        name: 'Pendapatan',
                        data: chartApb.penerimaan
                    },
                    {
                        name: 'Belanja',
                        data: chartApb.belanja
                    }
                ],
                colors: ['#10b981', '#ef4444'],
                plotOptions: {
                    bar: {
                        columnWidth: '40%',
                        borderRadius: 3
                    }
                },
                xaxis: {
                    categories: chartApb.labels
                },
                legend: {
                    position: 'top'
                },
                dataLabels: {
                    enabled: false
                },
                yaxis: {
                    labels: {
                        formatter: function(v) {
                            return rpCompact(v);
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(v) {
                            return rp(v);
                        }
                    }
                }
            }).render();
        }

        /* Column — Potensi Lahan */
        var elLahan = document.getElementById('dash-chart-lahan');
        var hasLahan = (chartLahan.values || []).some(function(v) {
            return v > 0;
        });
        if (elLahan && !hasLahan) {
            noData(elLahan);
        } else if (elLahan) {
            new ApexCharts(elLahan, {
                chart: {
                    type: 'bar',
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Luas (Ha)',
                    data: chartLahan.values
                }],
                colors: ['#0ea5a4'],
                plotOptions: {
                    bar: {
                        columnWidth: '45%',
                        borderRadius: 3
                    }
                },
                xaxis: {
                    categories: chartLahan.labels
                },
                dataLabels: {
                    enabled: false
                },
                yaxis: {
                    labels: {
                        formatter: function(v) {
                            return v.toLocaleString('id-ID');
                        }
                    }
                }
            }).render();
        }

        /* Donut — Komposisi Penduduk */
        var elPenduduk = document.getElementById('dash-chart-penduduk');
        var hasPenduduk = (chartPenduduk.values || []).some(function(v) {
            return v > 0;
        });
        if (elPenduduk && !hasPenduduk) {
            noData(elPenduduk);
        } else if (elPenduduk) {
            new ApexCharts(elPenduduk, {
                chart: {
                    type: 'donut'
                },
                series: chartPenduduk.values,
                labels: chartPenduduk.labels,
                colors: ['#3b82f6', '#ec4899'],
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Jiwa',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce(function(a, b) {
                                            return a + b;
                                        }, 0).toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function(v) {
                            return v.toLocaleString('id-ID') + ' jiwa';
                        }
                    }
                }
            }).render();
        }
    });
</script>