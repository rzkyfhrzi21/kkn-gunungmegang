<?php
// ================================
// PROTEKSI ROLE
// ================================
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin'])) {
    return;
}

$role = $_SESSION['sesi_role'];

// ================================
// AMBIL LIST KK
// ================================
$kk_list = db_read('kartu_keluarga');
usort($kk_list, fn($a, $b) => strcmp($a['nomor_kk'], $b['nomor_kk']));

// KK aktif dari URL
$id_kk_aktif = $_GET['id_kk'] ?? '';

// ================================
// AMBIL SEMUA DATA
// ================================
$semua_anggota = db_read('anggota_keluarga');
$semua_penduduk = db_read('penduduk');
usort($semua_penduduk, fn($a, $b) => strcmp($a['nama_lengkap'], $b['nama_lengkap']));

$penduduk_map = [];
foreach ($semua_penduduk as $p) {
    $penduduk_map[$p['id_penduduk']] = $p;
}

// ================================
// AMBIL ANGGOTA KK JIKA KK DIPILIH
// ================================
$anggota = [];
if (!empty($id_kk_aktif)) {
    foreach ($semua_anggota as $ak) {
        if ($ak['id_kk'] == $id_kk_aktif) {
            $p = $penduduk_map[$ak['id_penduduk']] ?? null;
            if ($p) {
                $anggota[] = [
                    'id_anggota' => $ak['id_anggota'],
                    'hubungan' => $ak['hubungan'],
                    'id_penduduk' => $p['id_penduduk'],
                    'nik' => $p['nik'],
                    'nama_lengkap' => $p['nama_lengkap']
                ];
            }
        }
    }
    usort($anggota, fn($a, $b) => strcmp($a['nama_lengkap'], $b['nama_lengkap']));
}

// ================================
// FILTER PENDUDUK (BELUM MASUK KK)
// ================================
$anggota_penduduk_ids = array_column($semua_anggota, 'id_penduduk');
$penduduk_list = [];
foreach ($semua_penduduk as $p) {
    if (!in_array($p['id_penduduk'], $anggota_penduduk_ids)) {
        $penduduk_list[] = $p;
    }
}
?>

<div class="page-heading">
    <div class="page-title">
        <h3>Data Anggota Keluarga</h3>
        <p class="text-subtitle text-muted">
            Kelola anggota keluarga berdasarkan Kartu Keluarga
        </p>
    </div>

    <!-- ================= FILTER KK ================= -->
    <section class="section">
        <form method="get" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="Data KK">

            <div class="col-md-6">
                <label class="fw-bold">Pilih Nomor KK</label>
                <select name="id_kk" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Pilih Kartu Keluarga --</option>
                    <?php foreach ($kk_list as $kk): ?>
                        <option value="<?= $kk['id_kk']; ?>"
                            <?= $kk['id_kk'] == $id_kk_aktif ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($kk['nomor_kk']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($role !== 'kades' && !empty($id_kk_aktif)): ?>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTambahAnggota">
                        <i class="bi bi-plus-circle"></i> Tambah Anggota
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </section>

    <!-- ================= TABLE ================= -->
    <?php if (!empty($id_kk_aktif)): ?>
        <section class="section mt-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Daftar Anggota Keluarga</h4>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover align-middle" id="tbl-penduduk1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NIK</th>
                                <th>Nama Lengkap</th>
                                <th>Hubungan Keluarga</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($anggota) === 0): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Belum ada anggota keluarga
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php $no = 1;
                            foreach ($anggota as $a): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($a['nik']); ?></td>
                                    <td><?= htmlspecialchars($a['nama_lengkap']); ?></td>
                                    <td><?= htmlspecialchars($a['hubungan']); ?></td>
                                    <td>
                                        <?php if ($role !== 'kades'): ?>
                                            <button class="btn btn-sm btn-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEdit<?= $a['id_anggota']; ?>">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>

                                            <?php if ($role === 'admin'): ?>
                                                <button class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalDelete<?= $a['id_anggota']; ?>">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<!-- ================= MODAL TAMBAH ================= -->
<?php if ($role !== 'kades' && !empty($id_kk_aktif)): ?>
    <div class="modal fade" id="modalTambahAnggota" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="post" action="../functions/function_data_kk.php" class="modal-content">
                <input type="hidden" name="id_kk" value="<?= $id_kk_aktif; ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Anggota Keluarga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>Penduduk</label>
                        <select name="id_penduduk" class="form-select" required>
                            <option value="">-- Pilih Penduduk --</option>
                            <?php foreach ($penduduk_list as $p): ?>
                                <option value="<?= $p['id_penduduk']; ?>">
                                    <?= htmlspecialchars($p['nama_lengkap']); ?> (<?= $p['nik']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Hubungan Keluarga</label>
                        <select name="hubungan" class="form-select" required>
                            <option value="Istri">Istri</option>
                            <option value="Suami">Suami</option>
                            <option value="Anak">Anak</option>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="btn_add_anggota" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- ================= MODAL EDIT & DELETE ================= -->
<?php foreach ($anggota as $a): ?>
    <?php if ($role !== 'kades'): ?>
        <!-- EDIT -->
        <div class="modal fade" id="modalEdit<?= $a['id_anggota']; ?>" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form method="post" action="../functions/function_data_kk.php" class="modal-content">
                    <input type="hidden" name="id_anggota" value="<?= $a['id_anggota']; ?>">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Hubungan Keluarga</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label>Nama Penduduk</label>
                            <input type="text" class="form-control"
                                value="<?= htmlspecialchars($a['nama_lengkap']); ?>" readonly>
                        </div>

                        <div class="col-md-6">
                            <label>Hubungan Keluarga</label>
                            <select name="hubungan" class="form-select" required>
                                <?php
                                $opsi = ['Istri', 'Suami', 'Anak', 'Orang Tua', 'Lainnya'];
                                foreach ($opsi as $o):
                                ?>
                                    <option value="<?= $o; ?>"
                                        <?= $a['hubungan'] === $o ? 'selected' : ''; ?>>
                                        <?= $o; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="btn_edit_anggota" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DELETE -->
        <?php if ($role === 'admin'): ?>
            <div class="modal fade" id="modalDelete<?= $a['id_anggota']; ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="post" action="../functions/function_data_kk.php" class="modal-content">
                        <input type="hidden" name="id_anggota" value="<?= $a['id_anggota']; ?>">

                        <div class="modal-header">
                            <h5 class="modal-title text-danger">Hapus Anggota</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <p>Yakin ingin menghapus anggota:</p>
                            <strong><?= htmlspecialchars($a['nama_lengkap']); ?></strong>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" name="btn_delete_anggota" class="btn btn-danger">
                                Ya, Hapus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>