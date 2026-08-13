<?php
/**
 * functions/security.php
 * Keamanan aplikasi (OWASP Top 10):
 *  - A02: hashing password (password_hash) + pembaruan hash lama (md5 -> bcrypt)
 *  - A03: sanitasi seluruh input (setara proteksi SQL-injection/XSS pada data JSON)
 *  - A05: hardening konfigurasi sesi (cookie HttpOnly, SameSite, name custom)
 *  - A07: rate-limit login (5x gagal -> kunci 15 menit per IP) + regenerate session ID
 *  - A08: CSRF token (hash_equals) untuk semua aksi POST (API, upload, login)
 *  - A09: audit log login (sukses/gagal) di db/json/security.json
 *  - A10: validasi URL keluar (anti SSRF/open redirect)
 *  Dimuat otomatis melalui functions/config.php.
 */

// ============================================================
// A05 - Hardening sesi (harus dipanggil SEBELUM session_start)
// ============================================================
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_name('KKNGM_SESSION');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ]);
}

// ============================================================
// A03 - Sanitasi input (SQL-injection / XSS equivalent)
// ============================================================
function sanitize_input($value, $maxLen = 255) {
    if (!is_string($value)) {
        return $value;
    }
    $value = trim($value);
    $value = str_replace(["\0", "\r", "\n"], '', $value);
    // Buang blok script/style beserta isinya, lalu semua tag (XSS)
    $value = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $value);
    $value = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $value);
    $value = strip_tags($value);
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', (string)$value);
    return mb_substr($value, 0, (int)$maxLen);
}

function sanitize_url($url, $maxLen = 500) {
    $url = sanitize_input($url, (int)$maxLen);
    if ($url === '') {
        return '';
    }
    if (!preg_match('#^https?://#i', $url)) {
        return '';
    }
    return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
}

function get_input($source, $key, $maxLen = 255) {
    $v = $source[$key] ?? '';
    return is_array($v) ? $v : sanitize_input((string)$v, (int)$maxLen);
}

// ============================================================
// A08 - CSRF (token per sesi, verifikasi dengan hash_equals)
// ============================================================
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify($token) {
    if (!is_string($token) || $token === '') {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function csrf_check_or_die($msg = 'CSRF token tidak valid.') {
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => $msg]);
        exit;
    }
}

// ============================================================
// A07 - Rate limit & audit log login (file db/json/security.json)
// ============================================================
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCK_MINUTES', 15);

function login_client_ip() {
    if (defined('ADMIN_API_TEST')) {
        return 'cli-test';
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function login_attempts_remaining() {
    $ip   = login_client_ip();
    $data = db_read('security');
    $rec  = null;
    foreach ($data as $r) {
        if (($r['ip'] ?? '') === $ip) { $rec = $r; break; }
    }
    if (!$rec) {
        return LOGIN_MAX_ATTEMPTS;
    }
    $lockUntil = (int)($rec['lock_until'] ?? 0);
    if ($lockUntil > time()) {
        return 0;
    }
    return max(0, LOGIN_MAX_ATTEMPTS - (int)($rec['fails'] ?? 0));
}

function login_record_fail() {
    $ip    = login_client_ip();
    $data  = db_read('security');
    $idx   = null;
    foreach ($data as $i => $r) {
        if (($r['ip'] ?? '') === $ip) { $idx = $i; break; }
    }
    if ($idx === null) {
        $data[] = ['ip' => $ip, 'fails' => 1, 'lock_until' => 0, 'updated' => date('c')];
    } else {
        $fails = (int)$data[$idx]['fails'] + 1;
        $data[$idx]['fails']      = $fails;
        $data[$idx]['lock_until'] = ($fails >= LOGIN_MAX_ATTEMPTS) ? time() + LOGIN_LOCK_MINUTES * 60 : 0;
        $data[$idx]['updated']    = date('c');
    }
    db_write('security', $data);
}

function login_reset_fails() {
    $ip   = login_client_ip();
    $data = db_read('security');
    $out  = array_values(array_filter($data, function ($r) use ($ip) {
        return ($r['ip'] ?? '') !== $ip;
    }));
    db_write('security', $out);
}

function login_audit_log($username, $status) {
    $data = db_read('security_log');
    $data[] = [
        'time'     => date('c'),
        'ip'       => login_client_ip(),
        'username' => sanitize_input((string)$username, 100),
        'status'   => $status,
    ];
    $data = array_slice($data, -200);
    db_write('security_log', $data);
}

// ============================================================
// A02 - Verifikasi password (mendukung hash lama md5, lalu upgrade)
// ============================================================
function password_check($password, $storedHash) {
    if ($storedHash === '' || !is_string($storedHash)) {
        return false;
    }
    if (password_verify($password, $storedHash)) {
        return true;
    }
    // Legacy: hash md5 32 karakter hex -> verifikasi lalu tandai untuk upgrade
    if (strlen($storedHash) === 32 && ctype_xdigit($storedHash) && md5($password) === $storedHash) {
        return true;
    }
    return false;
}

function password_upgrade_hash($userId, $plainPassword) {
    $users = db_read('user');
    foreach ($users as $i => $u) {
        if ((int)($u['id_user'] ?? 0) === (int)$userId) {
            $users[$i]['password'] = password_hash($plainPassword, PASSWORD_DEFAULT);
            db_write('user', $users);
            return true;
        }
    }
    return false;
}