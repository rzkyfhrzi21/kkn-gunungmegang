<?php
// Proteksi role
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin'])) {
    return;
}

// Ambil data informasi desa
$info_list = db_read('informasi_desa');
usort($info_list, fn($a, $b) => strcmp($b['tanggal'], $a['tanggal']));

// Nama penulis dari session
$penulis_login = $_SESSION['sesi_nama'] ?? '-';
$tanggal_hari_ini = date('Y-m-d');
?>

<div class="page-heading">
    <div class="page-title">
        <h3>Informasi Portal Berita Desa</h3>
        <p class="text-subtitle text-muted">
            Kelola berita, pengumuman, dan agenda desa dengan cepat.
        </p>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Daftar Informasi</h4>
                <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalTambahInfo">
                    <i class="bi bi-plus-circle"></i> Tambah Informasi
                </button>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle" id="tbl-kk1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Penulis</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($info_list as $info): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($info['judul']); ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= htmlspecialchars($info['kategori']); ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($info['tanggal']); ?></td>
                                <td><?= htmlspecialchars($info['penulis']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit<?= $info['id_info']; ?>">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDelete<?= $info['id_info']; ?>">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
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
<div class="modal fade" id="modalTambahInfo" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" action="../functions/function_informasi_desa.php" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Informasi Desa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
                <div class="col-md-12">
                    <label>Judul Postingan</label>
                    <input type="text" name="judul"
                        class="form-control"
                        minlength="5" maxlength="150"
                        required>
                </div>

                <div class="col-md-6">
                    <label>Kategori</label>
                    <select name="kategori" class="form-select" required>
                        <option value="">- Pilih Kategori -</option>
                        <option value="Berita">Berita</option>
                        <option value="Pengumuman">Pengumuman</option>
                        <option value="Agenda">Agenda</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal"
                        class="form-control"
                        value="<?= $tanggal_hari_ini; ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label>Penulis</label>
                    <input type="text" name="penulis"
                        class="form-control"
                        value="<?= htmlspecialchars($penulis_login); ?>"
                        required>
                </div>

                <div class="col-md-12">
                    <label>Isi Informasi</label>
                    <textarea name="isi"
                        class="form-control"
                        rows="5"
                        minlength="10"
                        required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_add_info" class="btn btn-primary">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>

<!-- ================= MODAL EDIT & DELETE ================= -->
<?php
foreach ($info_list as $info):
?>

    <!-- EDIT -->
    <div class="modal fade" id="modalEdit<?= $info['id_info']; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="post" action="../functions/function_informasi_desa.php" class="modal-content">

                <input type="hidden" name="id_info" value="<?= $info['id_info']; ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Informasi Desa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label>Judul</label>
                        <input type="text" name="judul"
                            class="form-control"
                            minlength="5" maxlength="150"
                            value="<?= htmlspecialchars($info['judul']); ?>"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label>Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <?php foreach (['Berita', 'Pengumuman', 'Agenda'] as $kat): ?>
                                <option value="<?= $kat; ?>"
                                    <?= $info['kategori'] === $kat ? 'selected' : ''; ?>>
                                    <?= $kat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Tanggal Postingan</label>
                        <input type="date" name="tanggal"
                            class="form-control"
                            value="<?= $info['tanggal']; ?>"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label>Penulis</label>
                        <input type="text" name="penulis"
                            class="form-control"
                            value="<?= $info['penulis']; ?>"
                            required>
                    </div>

                    <div class="col-md-12">
                        <label>Isi Informasi</label>
                        <textarea name="isi"
                            class="form-control"
                            rows="5"
                            minlength="10"
                            required><?= htmlspecialchars($info['isi']); ?></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" name="btn_edit_info" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- DELETE -->
    <div class="modal fade" id="modalDelete<?= $info['id_info']; ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="../functions/function_informasi_desa.php" class="modal-content">

                <input type="hidden" name="id_info" value="<?= $info['id_info']; ?>">

                <div class="modal-header">
                    <h5 class="modal-title text-danger">Hapus Informasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Yakin ingin menghapus informasi:</p>
                    <strong><?= htmlspecialchars($info['judul']); ?></strong>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="btn_delete_info" class="btn btn-danger">
                        Ya, Hapus
                    </button>
                </div>

            </form>
        </div>
    </div>

<?php endforeach; ?>