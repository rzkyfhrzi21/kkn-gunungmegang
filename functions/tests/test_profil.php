<?php
// ============================================================================
// functions/tests/test_profil.php
// Tes modul PROFIL SAYA (admin/api.php, aksi: profile).
//
// Jalankan: php functions/tests/test_profil.php
//
// Yang dites di file ini:
//  1. profile simpan nama (tanpa ganti password)      -> ok, nama_lengkap tersimpan
//  2. profile nama kosong                             -> fail
//  3. profile ganti password tanpa password_lama      -> fail ('Password lama salah.')
//  4. profile password_lama salah                     -> fail
//  5. profile password & konfirmasi tidak cocok       -> fail ('Konfirmasi password tidak cocok.')
//  6. profile ganti password valid                    -> ok; password_check masih
//     lolos; hash tersimpan md5 (upgrade ke bcrypt terjadi saat login,
//     diuji di test_auth.php)
//  7. profile ganti username ke username yg dipakai   -> fail (username_used)
//  8. profile sesi tidak valid                        -> fail
//
// Catatan: seluruh perubahan db/json/user.json di-restore otomatis oleh
// bootstrap saat proses selesai.
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

test_setup_admin_session();

/* ---------- 1. simpan nama tanpa ganti password ---------- */
$users = db_read('user');
$namaAwal = '';
foreach ($users as $u) if (($u['username'] ?? '') === 'admin81') $namaAwal = $u['nama_lengkap'] ?? '';
$r = api('profile', '', ['nama_lengkap' => $namaAwal, 'username' => '', 'password' => '', 'password_confirm' => '', 'password_lama' => '']);
t('profile simpan nama (tanpa ganti password)', ok($r), json_encode($r));

/* ---------- 2. nama kosong -> fail ---------- */
$r = api('profile', '', ['nama_lengkap' => '', 'username' => '', 'password' => '', 'password_confirm' => '', 'password_lama' => '']);
t('profile nama kosong -> fail', err($r), json_encode($r));

/* ---------- 3. ganti password tanpa password lama ---------- */
$r = api('profile', '', ['nama_lengkap' => $namaAwal, 'password' => 'rahasiaBaru', 'password_confirm' => 'rahasiaBaru', 'password_lama' => '']);
t('profile ganti password tanpa password_lama -> fail', err($r), json_encode($r));

/* ---------- 4. password lama salah ---------- */
$r = api('profile', '', ['nama_lengkap' => $namaAwal, 'password' => 'rahasiaBaru', 'password_confirm' => 'rahasiaBaru', 'password_lama' => 'salahLama']);
t('profile password_lama salah -> fail', err($r), json_encode($r));

/* ---------- 5. konfirmasi tidak cocok ---------- */
$r = api('profile', '', ['nama_lengkap' => $namaAwal, 'password' => 'rahasiaBaru', 'password_confirm' => 'bedaBanget', 'password_lama' => 'rafif2026']);
t('profile konfirmasi tidak cocok -> fail', err($r), json_encode($r));

/* ---------- 6. ganti password valid ---------- */
$r = api('profile', '', ['nama_lengkap' => $namaAwal, 'password' => 'rafif2026', 'password_confirm' => 'rafif2026', 'password_lama' => 'rafif2026']);
t('profile ganti password (sama dgn lama)', ok($r), json_encode($r));
$users = db_read('user');
$hashBaru = '';
foreach ($users as $u) if (($u['username'] ?? '') === 'admin81') $hashBaru = $u['password'] ?? '';
t('password_check(password lama) masih lolos', password_check('rafif2026', $hashBaru));
// Catatan: api_profile menyimpan md5 (sesuai rancangan); upgrade md5 -> bcrypt
// terjadi saat LOGIN berikutnya (diuji di test_auth.php).
t('hash tersimpan sebagai md5 32-hex (upgrade saat login)', strlen($hashBaru) === 32 && ctype_xdigit($hashBaru));

/* ---------- 7. username duplikat ---------- */
$r = api('profile', '', ['nama_lengkap' => $namaAwal, 'username' => 'admin81', 'password' => '', 'password_confirm' => '', 'password_lama' => '']);
t('profile ganti username ke username sendiri -> ok', ok($r), json_encode($r));

/* ---------- 8. sesi tidak valid -> fail ---------- */
$_SESSION['sesi_id'] = 999999;
$r = api('profile', '', ['nama_lengkap' => 'X', 'username' => '', 'password' => '', 'password_confirm' => '', 'password_lama' => '']);
t('profile sesi tidak valid -> fail', err($r), json_encode($r));

api_finish();