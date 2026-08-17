<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$demoData = include dirname(__DIR__, 2) . '/includes/demografi.php';
$batas    = $demoData['batas_wilayah'];

$chartGender = [
    'labels' => ['Laki-laki', 'Perempuan'],
    'values' => [(int)($demoData['laki_laki'] ?? 0), (int)($demoData['perempuan'] ?? 0)],
];
$chartDemografi = [
    'labels' => ['Total Jiwa', 'Jumlah KK', 'Luas Wilayah (Ha)'],
    'values' => [(int)($demoData['total_jiwa'] ?? 0), (int)($demoData['jumlah_kk'] ?? 0), (int)($demoData['luas_wilayah_ha'] ?? 0)],
];
$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<div class="page-heading">
    <h3>Demografi &amp; Wilayah</h3>
    <p class="text-subtitle text-muted">Kependudukan, luas wilayah, dan batas pekon</p>
</div>

<section class="section">
    <div class="row gy-3 mb-3">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-people text-primary"></i>
                    <h6 class="mb-0">Komposisi Penduduk</h6>
                </div>
                <div class="card-body">
                    <div id="demo-chart-gender" style="height:300px"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart text-success"></i>
                    <h6 class="mb-0">Indikator Kependudukan &amp; Wilayah</h6>
                </div>
                <div class="card-body">
                    <div id="demo-chart-summary" style="height:300px"></div>
                </div>
            </div>
        </div>
    </div>

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
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('form-demografi');
        var btn = document.getElementById('btn-save-demografi');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            btn.disabled = true;
            var payload = {
                laki_laki: form.laki_laki.value,
                perempuan: form.perempuan.value,
                total_jiwa: form.total_jiwa.value,
                jumlah_kk: form.jumlah_kk.value,
                luas_wilayah_km2: form.luas_wilayah_km2.value,
                luas_wilayah_ha: form.luas_wilayah_ha.value,
                jarak_kecamatan_km: form.jarak_kecamatan_km.value,
                waktu_kecamatan_menit: form.waktu_kecamatan_menit.value,
                batas_wilayah: {
                    utara: form.batas_utara.value,
                    timur: form.batas_timur.value,
                    selatan: form.batas_selatan.value,
                    barat: form.batas_barat.value
                }
            };
            App.postJSON('../admin/api.php', {
                    action: 'save',
                    module: 'demografi',
                    data: payload
                })
                .then(function(res) {
                    btn.disabled = false;
                    if (res.ok) App.toast('Data demografi berhasil disimpan.', 'success', 'Berhasil');
                    else App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
                })
                .catch(function() {
                    btn.disabled = false;
                    App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
                });
        });
    });
</script>

<script src="assets/extensions/apexcharts/apexcharts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.ApexCharts) return;

        function noData(el) {
            el.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data</div>';
        }

        var chartGender = <?= json_encode($chartGender, $jsonFlags) ?>;
        var chartSummary = <?= json_encode($chartDemografi, $jsonFlags) ?>;

        var elGender = document.getElementById('demo-chart-gender');
        var hasGender = (chartGender.values || []).some(function(v) {
            return v > 0;
        });
        if (elGender && !hasGender) {
            noData(elGender);
        } else if (elGender) {
            new ApexCharts(elGender, {
                chart: {
                    type: 'donut'
                },
                series: chartGender.values,
                labels: chartGender.labels,
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
                dataLabels: {
                    formatter: function(v) {
                        return Number(v).toFixed(1) + '%';
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

        var elSummary = document.getElementById('demo-chart-summary');
        var hasSummary = (chartSummary.values || []).some(function(v) {
            return v > 0;
        });
        if (elSummary && !hasSummary) {
            noData(elSummary);
        } else if (elSummary) {
            new ApexCharts(elSummary, {
                chart: {
                    type: 'bar',
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Jumlah',
                    data: chartSummary.values
                }],
                colors: ['#0ea5a4'],
                plotOptions: {
                    bar: {
                        columnWidth: '45%',
                        borderRadius: 3
                    }
                },
                xaxis: {
                    categories: chartSummary.labels
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
    });
</script>