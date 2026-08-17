<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;
?>
<div class="page-heading">
    <h3>Manajemen User</h3>
    <p class="text-subtitle text-muted">Akun pengguna panel admin — disimpan ke <code>db/json/user.json</code></p>
</div>

<section class="section">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-people text-primary"></i>
                <h6 class="mb-0">Daftar User</h6>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="btn-add-user">
                <i class="bi bi-plus-lg"></i> Tambah User
            </button>
        </div>
        <div class="card-body">
            <div class="app-table-wrap">
                <div class="app-table-toolbar mb-3">
                    <input type="text" id="search-user" class="form-control form-control-sm" placeholder="Cari username / nama...">
                    <select id="filter-role" class="form-select form-select-sm"></select>
                </div>
                <table class="table table-hover" id="tbl-user">
                    <thead>
                        <tr>
                            <th class="w-25">#</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
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

<!-- Modal tambah/edit -->
<div class="modal fade" id="modal-user" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-user">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-user-title">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_user" value="">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" required>
                    <label class="form-label mt-3">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_lengkap" required>
                    <label class="form-label mt-3">Role</label>
                    <select class="form-select" name="role" required>
                        <option value="admin">admin</option>
                    </select>
                    <label class="form-label mt-3" id="lbl-password">Password</label>
                    <input type="password" class="form-control" name="password" autocomplete="new-password">
                    <div class="app-upload-hint" id="hint-password">Isi untuk mengganti password (kosongkan jika tidak diubah).</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal konfirmasi hapus -->
<div class="modal fade" id="modal-delete-user" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash me-1"></i>Hapus User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-delete-user-text">Apakah Anda yakin ingin menghapus user ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete-user">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalUser = document.getElementById('modal-user');
        var formUser = document.getElementById('form-user');
        var pendingDelete = null;

        var table = new App.JsonTable({
            selector: '#tbl-user',
            module: 'user',
            perPage: 10,
            search: '#search-user',
            filters: [{
                key: 'role',
                label: 'Semua Role',
                select: '#filter-role'
            }],
            columns: [{
                    key: 'id_user',
                    label: '#'
                },
                {
                    key: 'username',
                    label: 'Username'
                },
                {
                    key: 'nama_lengkap',
                    label: 'Nama Lengkap'
                },
                {
                    key: 'role',
                    label: 'Role',
                    type: 'badge'
                }
            ],
            actions: ['edit', 'delete'],
            onEdit: function(row) {
                document.getElementById('modal-user-title').textContent = 'Edit User';
                formUser.id_user.value = row.id_user;
                formUser.username.value = row.username;
                formUser.nama_lengkap.value = row.nama_lengkap;
                formUser.role.value = row.role || 'admin';
                formUser.password.value = '';
                formUser.password.required = false;
                document.getElementById('lbl-password').textContent = 'Password Baru';
                document.getElementById('hint-password').textContent = 'Kosongkan jika tidak ingin mengganti password.';
                App.showModal(modalUser);
            },
            onDelete: function(row) {
                pendingDelete = row.id_user;
                document.getElementById('modal-delete-user-text').textContent =
                    'Apakah Anda yakin ingin menghapus user "' + row.username + '"?';
                App.showModal(document.getElementById('modal-delete-user'));
            }
        });

        document.getElementById('btn-add-user').addEventListener('click', function() {
            document.getElementById('modal-user-title').textContent = 'Tambah User';
            formUser.reset();
            formUser.password.required = true;
            document.getElementById('lbl-password').textContent = 'Password';
            document.getElementById('hint-password').textContent = 'Password wajib diisi untuk user baru.';
            App.showModal(modalUser);
        });

        formUser.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!formUser.checkValidity()) {
                formUser.reportValidity();
                return;
            }
            App.postJSON('../admin/api.php', {
                action: 'save_row',
                module: 'user',
                data: {
                    id_user: formUser.id_user.value,
                    username: formUser.username.value,
                    nama_lengkap: formUser.nama_lengkap.value,
                    role: formUser.role.value,
                    password: formUser.password.value
                }
            }).then(function(res) {
                if (res.ok) {
                    App.hideModal(modalUser);
                    App.toast('User berhasil disimpan.', 'success', 'Berhasil');
                    table.reload();
                } else {
                    App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
                }
            }).catch(function() {
                App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
            });
        });

        document.getElementById('btn-confirm-delete-user').addEventListener('click', function() {
            App.postJSON('../admin/api.php', {
                action: 'delete',
                module: 'user',
                data: {
                    id_user: pendingDelete
                }
            }).then(function(res) {
                if (res.ok) {
                    App.hideModal(document.getElementById('modal-delete-user'));
                    App.toast('User berhasil dihapus.', 'success', 'Berhasil');
                    table.reload();
                } else {
                    App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menghapus.', 'error', 'Gagal');
                }
            }).catch(function() {
                App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
            });
        });
    });
</script>