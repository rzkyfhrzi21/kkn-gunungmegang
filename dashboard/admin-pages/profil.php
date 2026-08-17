<?php
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') return;

$me = db_find_one('user', 'id_user', (string)($_SESSION['sesi_id'] ?? 0));
?>
<div class="page-heading">
    <h3>Profil Saya</h3>
    <p class="text-subtitle text-muted">Ubah data pribadi dan akun login Anda</p>
</div>

<section class="section">
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-person-circle text-primary"></i>
            <h6 class="mb-0">Informasi Akun</h6>
        </div>
        <div class="card-body">
            <form id="form-profil" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_lengkap"
                        value="<?= htmlspecialchars($me['nama_lengkap'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username"
                        value="<?= htmlspecialchars($me['username'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password Lama</label>
                    <input type="password" class="form-control" name="password_lama" autocomplete="current-password">
                    <div class="app-upload-hint">Wajib diisi jika ingin mengganti password.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password Baru</label>
                    <input type="password" class="form-control" name="password" autocomplete="new-password">
                    <div class="app-upload-hint">Kosongkan jika tidak ingin mengganti password.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" name="password_confirm" autocomplete="new-password">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($me['role'] ?? '') ?>" disabled>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="btn-save-profil">
                        <i class="bi bi-check-lg"></i> Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('form-profil');
        var btn = document.getElementById('btn-save-profil');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            if (form.password.value && !form.password_lama.value) {
                App.toast('Password lama wajib diisi untuk mengganti password.', 'error', 'Gagal');
                return;
            }
            if (form.password.value !== form.password_confirm.value) {
                App.toast('Konfirmasi password tidak cocok.', 'error', 'Gagal');
                return;
            }
            btn.disabled = true;
            App.postJSON('../admin/api.php', {
                action: 'profile',
                data: {
                    nama_lengkap: form.nama_lengkap.value,
                    username: form.username.value,
                    password_lama: form.password_lama.value,
                    password: form.password.value,
                    password_confirm: form.password_confirm.value
                }
            }).then(function(res) {
                btn.disabled = false;
                if (res.ok) {
                    form.password_lama.value = '';
                    form.password.value = '';
                    form.password_confirm.value = '';
                    App.toast('Profil berhasil disimpan.', 'success', 'Berhasil');
                } else {
                    App.toast((res.detail ? res.error + ': ' + res.detail : res.error) || 'Gagal menyimpan.', 'error', 'Gagal');
                }
            }).catch(function() {
                btn.disabled = false;
                App.toast('Terjadi kesalahan jaringan.', 'error', 'Gagal');
            });
        });
    });
</script>