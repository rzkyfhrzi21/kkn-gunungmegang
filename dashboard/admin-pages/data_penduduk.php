<?php
// Proteksi role
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin'])) {
    return;
}

// Ambil data penduduk
$penduduk_list = db_read('penduduk');
usort($penduduk_list, fn($a, $b) => strcmp($a['nama_lengkap'], $b['nama_lengkap']));
?>

<div class="page-heading">
    <div class="page-title">
        <h3>Data Penduduk</h3>
        <p class="text-subtitle text-muted">
            Kelola data identitas penduduk desa
        </p>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Daftar Penduduk</h4>
                <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalTambahPenduduk">
                    <i class="bi bi-plus-circle"></i> Tambah Penduduk
                </button>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle" id="tbl-penduduk1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NIK</th>
                            <th>Nama Lengkap</th>
                            <th>JK</th>
                            <th>TTL</th>
                            <th>Pekerjaan</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($penduduk_list as $p): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($p['nik']); ?></td>
                                <td><?= htmlspecialchars($p['nama_lengkap']); ?></td>
                                <td><?= $p['jenis_kelamin'] === 'L' ? 'L' : 'P'; ?></td>
                                <td>
                                    <?= htmlspecialchars($p['tempat_lahir']); ?>,
                                    <?= htmlspecialchars($p['tanggal_lahir']); ?>
                                </td>
                                <td><?= htmlspecialchars($p['pekerjaan']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit<?= $p['id_penduduk']; ?>">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>

                                    <?php if ($_SESSION['sesi_role'] === 'admin'): ?>
                                        <button class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDelete<?= $p['id_penduduk']; ?>">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- ================= MODAL TAMBAH ================= -->
<div class="modal fade" id="modalTambahPenduduk" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" action="../functions/function_penduduk.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Penduduk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label>NIK</label>
                    <input type="text" name="nik" class="form-control"
                        minlength="16" maxlength="17" required>
                </div>

                <div class="col-md-6">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" required class="form-control">
                </div>

                <div class="col-md-6">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" required class="form-control">
                </div>

                <div class="col-md-6">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Status Perkawinan</label>
                    <select name="status_perkawinan" class="form-select">
                        <option value="Belum Kawin">Belum Kawin</option>
                        <option value="Kawin">Kawin</option>
                        <option value="Cerai Hidup">Cerai Hidup</option>
                        <option value="Cerai Mati">Cerai Mati</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Agama</label>
                    <input type="text" name="agama" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>Kewarganegaraan</label>
                    <input type="text" name="kewarganegaraan"
                        class="form-control" value="WNI" required>
                </div>

                <div class="col-md-12">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2" required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_add_penduduk" class="btn btn-primary">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT & DELETE ================= -->
<?php
foreach ($penduduk_list as $p):
?>

    <!-- EDIT -->
    <div class="modal fade" id="modalEdit<?= $p['id_penduduk']; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="post" action="../functions/function_penduduk.php" class="modal-content">
                <input type="hidden" name="id_penduduk" value="<?= $p['id_penduduk']; ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Penduduk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>NIK</label>
                        <input type="text" minlength="16" maxlength="17" name="nik" class="form-control"
                            value="<?= htmlspecialchars($p['nik']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control"
                            value="<?= htmlspecialchars($p['nama_lengkap']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control"
                            value="<?= htmlspecialchars($p['tempat_lahir']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control"
                            value="<?= $p['tanggal_lahir']; ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="L" <?= $p['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="P" <?= $p['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Status Perkawinan</label>
                        <select name="status_perkawinan" class="form-select">
                            <option value="">- Pilih -</option>
                            <?php foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $v): ?>
                                <option value="<?= $v ?>"
                                    <?= $p['status_perkawinan'] === $v ? 'selected' : '' ?>>
                                    <?= $v ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                    <div class="col-md-6">
                        <label>Agama</label>
                        <input type="text" name="agama" class="form-control"
                            value="<?= htmlspecialchars($p['agama']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control"
                            value="<?= htmlspecialchars($p['pekerjaan']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label>Kewarganegaraan</label>
                        <input type="text" name="kewarganegaraan" class="form-control"
                            value="<?= htmlspecialchars($p['kewarganegaraan']); ?>" required>
                    </div>

                    <div class="col-md-12">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control"
                            rows="2" required><?= htmlspecialchars($p['alamat']); ?></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="btn_edit_penduduk" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE -->
    <div class="modal fade" id="modalDelete<?= $p['id_penduduk']; ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="../functions/function_penduduk.php" class="modal-content">
                <input type="hidden" name="id_penduduk" value="<?= $p['id_penduduk']; ?>">

                <div class="modal-header">
                    <h5 class="modal-title text-danger">Hapus Penduduk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Yakin ingin menghapus penduduk:</p>
                    <strong><?= htmlspecialchars($p['nama_lengkap']); ?></strong>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="btn_delete_penduduk" class="btn btn-danger">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>

<?php endforeach; ?>