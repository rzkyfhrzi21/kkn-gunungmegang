<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['btn_login'])) {

    // A08 - CSRF
    if (!csrf_verify(get_input($_POST, 'csrf_token', 128))) {
        login_audit_log('', 'csrf-blocked');
        header('Location: ../auth/login?action=login&status=csrf');
        exit;
    }

    // A07 - Rate limit per IP
    if (login_attempts_remaining() <= 0) {
        login_audit_log('', 'locked');
        header('Location: ../auth/login?action=login&status=locked');
        exit;
    }

    $username = get_input($_POST, 'username', 100);
    $password = (string)($_POST['password'] ?? '');

    // Cari user di JSON (case-insensitive username)
    $users = db_read('user');
    $found = null;
    foreach ($users as $u) {
        if (strtolower($u['username'] ?? '') === strtolower($username)) {
            $found = $u;
            break;
        }
    }

    $ok = false;
    if ($found !== null && password_check($password, $found['password'] ?? '')) {
        $ok = true;
        // A02 - Upgrade hash lama (md5) ke password_hash
        if (strlen($found['password']) === 32 && ctype_xdigit($found['password'])) {
            password_upgrade_hash($found['id_user'], $password);
        }
    }

    if ($ok) {
        login_reset_fails();
        login_audit_log($username, 'success');

        // A05 - Regenerate session ID setelah login (anti session fixation)
        session_regenerate_id(true);

        $_SESSION['sesi_id']       = $found['id_user'];
        $_SESSION['sesi_role']     = strtolower($found['role']);
        $_SESSION['sesi_username'] = $found['username'];
        $_SESSION['sesi_nama']     = $found['nama_lengkap'];
        csrf_token();

        // Hanya admin yang diizinkan
        if ($_SESSION['sesi_role'] === 'admin') {
            header('Location: ../dashboard/admin?page=Dashboard');
        } else {
            // Role tidak dikenal → logout
            header('Location: ../auth/logout');
        }
        exit;
    }

    login_record_fail();
    login_audit_log($username, 'failed');
    header('Location: ../auth/login?action=login&status=error');
    exit;
}