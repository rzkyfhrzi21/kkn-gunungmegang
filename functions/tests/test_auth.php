<?php
// ============================================================================
// functions/tests/test_auth.php
// Tes unit murni fungsi KEAMANAN LOGIN (functions/security.php).
//
// Jalankan: php functions/tests/test_auth.php
//
// Catatan: alur login lengkap (redirect, session, CSRF di form) bersifat
// exit-based sehingga di-cover oleh tes UI Chrome (crud-ui-test.js).
// File ini menguji logika inti yang bisa dipanggil langsung:
//
//  [password_check - A02]
//  1. hash bcrypt (password_hash)   -> password benar lolos, salah ditolak
//  2. hash legacy md5 32-hex        -> password benar lolos (legacy)
//  3. hash kosong / bukan string    -> ditolak
//  [password_upgrade_hash - A02]
//  4. upgrade md5 -> bcrypt saat login sukses (hash berubah, tetap lolos cek)
//  [csrf - A08]
//  5. csrf_token() menghasilkan 64 karakter hex & stabil per sesi
//  6. csrf_verify benar / salah / kosong
//  [rate limit login - A07]
//  7. login_attempts_remaining awal = LOGIN_MAX_ATTEMPTS (5)
//  8. 5x login_record_fail -> 0 (terkunci, lock_until di masa depan)
//  9. login_reset_fails -> kembali 5
//  10. status terlihat di db/json/security.json (ip cli-test)
//  [sanitasi - A03]
//  11. sanitize_input buang <script>...</script> & tag lain
//  12. get_input batas maxLen
//  13. sanitize_url hanya http(s) valid
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

/* ---------- 1. password_check: bcrypt ---------- */
$bcrypt = password_hash('rahasia123', PASSWORD_DEFAULT);
t('password_check bcrypt: benar -> lolos', password_check('rahasia123', $bcrypt));
t('password_check bcrypt: salah -> ditolak', !password_check('salah', $bcrypt));

/* ---------- 2. password_check: md5 legacy ---------- */
$md5 = md5('legacy456');
t('password_check md5 legacy: benar -> lolos', password_check('legacy456', $md5));
t('password_check md5 legacy: salah -> ditolak', !password_check('salah', $md5));

/* ---------- 3. password_check: kosong / non-string ---------- */
t('password_check hash kosong -> ditolak', !password_check('x', ''));
t('password_check hash non-string -> ditolak', !password_check('x', 123));

/* ---------- 4. upgrade md5 -> bcrypt ---------- */
$users = db_read('user');
$adminId = 0;
foreach ($users as $u) if (($u['username'] ?? '') === 'admin81') $adminId = (int)$u['id_user'];
$upgraded = password_upgrade_hash($adminId, 'rafif2026');
$users = db_read('user');
$hashBaru = '';
foreach ($users as $u) if ((int)$u['id_user'] === $adminId) $hashBaru = $u['password'] ?? '';
t(
    'password_upgrade_hash: md5 -> bcrypt (bukan 32-hex lagi)',
    $upgraded && strlen($hashBaru) !== 32 && password_check('rafif2026', $hashBaru)
);

/* ---------- 5-6. CSRF ---------- */
$t1 = csrf_token();
$t2 = csrf_token();
t('csrf_token 64 hex & stabil per sesi', strlen($t1) === 64 && ctype_xdigit($t1) && $t1 === $t2);
t('csrf_verify token benar -> lolos', csrf_verify($t1));
t('csrf_verify token salah -> ditolak', !csrf_verify(str_repeat('0', 64)));
t('csrf_verify kosong -> ditolak', !csrf_verify(''));

/* ---------- 7-9. rate limit login ---------- */
t('login_attempts_remaining awal = ' . LOGIN_MAX_ATTEMPTS, login_attempts_remaining() === LOGIN_MAX_ATTEMPTS);
login_record_fail();
login_record_fail();
t('setelah 2x gagal: sisa 3', login_attempts_remaining() === LOGIN_MAX_ATTEMPTS - 2);
login_record_fail();
login_record_fail();
login_record_fail();
t('setelah 5x gagal: 0 (terkunci)', login_attempts_remaining() === 0);
$sec = db_read('security');
$rec = null;
foreach ($sec as $r) if (($r['ip'] ?? '') === 'cli-test') $rec = $r;
t('security.json mencatat fails=5 & lock_until masa depan', $rec !== null && (int)($rec['fails'] ?? 0) === 5 && (int)($rec['lock_until'] ?? 0) > time());
login_reset_fails();
t('login_reset_fails -> kembali 5', login_attempts_remaining() === LOGIN_MAX_ATTEMPTS);

/* ---------- 11-13. sanitasi ---------- */
$evil = '<script>alert(1)</script>Halo <b>Dunia</b>';
$clean = sanitize_input($evil, 255);
t('sanitize_input buang script & tag', strpos($clean, '<script>') === false && strpos($clean, '<b>') === false && strpos($clean, 'Halo') !== false);
$short = get_input(['pesan' => str_repeat('x', 500)], 'pesan', 100);
t('get_input memotong maxLen (100)', strlen($short) === 100);
t('sanitize_url http valid', sanitize_url('https://example.com/a') === 'https://example.com/a');
t('sanitize_url non-http -> kosong', sanitize_url('javascript:alert(1)') === '' && sanitize_url('ftp://x') === '');

api_finish();