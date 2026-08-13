<?php
// Proteksi role
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin'])) {
    return;
}

// Ambil data jenis surat
$jenis_list = db_read('jenis_surat');
usort($jenis_list, fn($a, $b) => strcmp($a['nama_surat'], $b['nama_surat']));
?>

<div class="page-heading">
    <h3>Jenis Surat</h3>
    <p class="text-subtitle text-muted">Kelola daftar jenis surat desa</p>
</div>

<section class="section">
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Daftar Jenis Surat</h4>
            <!-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Jenis Surat
            </button> -->
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover table-striped align-middle" id="tbl-kk1">
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th>Kode Surat</th>
                        <th>Nama Surat</th>
                        <th>Keterangan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($jenis_list as $d): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($d['kode_surat']); ?></span></td>
                            <td><?= htmlspecialchars($d['nama_surat']); ?></td>
                            <td><?= htmlspecialchars($d['keterangan']); ?></td>
                            <td>
                                <!-- <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit<?= $d['id_jenis_surat']; ?>">
                                    <i class="bi bi-pencil-fill"></i>
                                </button> -->

                                <?php if ($_SESSION['sesi_role'] === 'admin'): ?>
                                    <button class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDelete<?= $d['id_jenis_surat']; ?>">
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

<!-- ================= MODAL TAMBAH ================= -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" action="../functions/function_jenis_surat.php" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Jenis Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
                <div class="col-md-4">
                    <label>Kode Surat</label>
                    <input type="text" name="kode_surat" class="form-control"
                        minlength="2" maxlength="20" required>
                </div>

                <div class="col-md-8">
                    <label>Nama Surat</label>
                    <input type="text" name="nama_surat" class="form-control"
                        minlength="5" maxlength="150" required>
                </div>

                <div class="col-md-12">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control"
                        rows="3" minlength="5" maxlength="255"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_add" class="btn btn-primary">Simpan</button>
            </div>

        </form>
    </div>
</div>

<!-- ================= MODAL EDIT & DELETE ================= -->
<?php
foreach ($jenis_list as $d):
?>

    <!-- EDIT -->
    <div class="modal fade" id="modalEdit<?= $d['id_jenis_surat']; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="post" action="../functions/function_jenis_surat.php" class="modal-content">

                <input type="hidden" name="id_jenis_surat" value="<?= $d['id_jenis_surat']; ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Jenis Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-4">
                        <label>Kode Surat</label>
                        <input type="text" name="kode_surat" class="form-control"
                            value="<?= htmlspecialchars($d['kode_surat']); ?>"
                            minlength="2" maxlength="20" required>
                    </div>

                    <div class="col-md-8">
                        <label>Nama Surat</label>
                        <input type="text" name="nama_surat" class="form-control"
                            value="<?= htmlspecialchars($d['nama_surat']); ?>"
                            minlength="5" maxlength="150" required>
                    </div>

                    <div class="col-md-12">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control"
                            rows="3"><?= htmlspecialchars($d['keterangan']); ?></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="btn_edit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- DELETE -->
    <?php if ($_SESSION['sesi_role'] === 'admin'): ?>
        <div class="modal fade" id="modalDelete<?= $d['id_jenis_surat']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" action="../functions/function_jenis_surat.php" class="modal-content">

                    <input type="hidden" name="id_jenis_surat" value="<?= $d['id_jenis_surat']; ?>">

                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Hapus Jenis Surat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Yakin ingin menghapus jenis surat:</p>
                        <strong><?= htmlspecialchars($d['nama_surat']); ?></strong>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="btn_delete" class="btn btn-danger">
                            Ya, Hapus
                        </button>
                    </div>

                </form>
            </div>
        </div>
    <?php endif; ?>

<?php endforeach; ?>