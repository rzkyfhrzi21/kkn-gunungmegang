<?php
session_start();
require_once 'config.php';

// Pastikan user login sebagai admin
if (!isset($_SESSION['sesi_id'], $_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') {
    header('Location: ../auth/logout');
    exit;
}

$id_login = (int) $_SESSION['sesi_id'];

/*
|--------------------------------------------------------------------------
| UPDATE DATA PRIBADI (nama lengkap)
|--------------------------------------------------------------------------
*/
if (isset($_POST['btn_editdatapribadi'])) {

    $id_user      = (int) ($_POST['id_user'] ?? 0);
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');

    if ($id_user <= 0 || $nama_lengkap === '') {
        header('Location: ../dashboard/admin?page=Profil&status=invalid');
        exit;
    }

    $users = db_read('user');
    foreach ($users as &$u) {
        if ((int)$u['id_user'] === $id_user) {
            $u['nama_lengkap'] = $nama_lengkap;
            break;
        }
    }
    unset($u);
    db_write('user', $users);

    $_SESSION['sesi_nama'] = $nama_lengkap;

    header('Location: ../dashboard/admin?page=Profil&status=success');
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE DATA AKUN (username, password)
|--------------------------------------------------------------------------
*/
if (isset($_POST['btn_editdataakun'])) {

    $id_user       = (int) ($_POST['id_user'] ?? 0);
    $username_baru = trim($_POST['username'] ?? '');
    $username_lama = trim($_POST['username_lama'] ?? '');
    $password      = $_POST['password'] ?? '';
    $konfirmasi    = $_POST['konfirmasi_password'] ?? '';

    if ($id_user <= 0 || $username_baru === '') {
        header('Location: ../dashboard/admin?page=Profil&status=invalid');
        exit;
    }

    // Cek username jika berubah
    if ($username_baru !== $username_lama) {
        $existing = db_find_one('user', 'username', $username_baru);
        if ($existing !== null && (int)$existing['id_user'] !== $id_user) {
            header('Location: ../dashboard/admin?page=Profil&status=username_used');
            exit;
        }
    }

    // Validasi password
    if (!empty($password) && $password !== $konfirmasi) {
        header('Location: ../dashboard/admin?page=Profil&status=password_mismatch');
        exit;
    }

    $users = db_read('user');
    foreach ($users as &$u) {
        if ((int)$u['id_user'] === $id_user) {
            $u['username'] = $username_baru;
            if (!empty($password)) {
                $u['password'] = md5($password);
            }
            break;
        }
    }
    unset($u);
    db_write('user', $users);

    $_SESSION['sesi_username'] = $username_baru;

    header('Location: ../dashboard/admin?page=Profil&status=success');
    exit;
}

/*
|--------------------------------------------------------------------------
| DEFAULT (akses langsung)
|--------------------------------------------------------------------------
*/
header('Location: ../auth/logout');
exit;
