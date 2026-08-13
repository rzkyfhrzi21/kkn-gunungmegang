<?php
session_start();
require_once 'config.php';

/*
|--------------------------------------------------------------------------
| VALIDASI ROLE ADMIN
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') {
    header('Location: ../auth/logout');
    exit;
}

$id_admin = (int) ($_SESSION['sesi_id'] ?? 0);

/*
|--------------------------------------------------------------------------
| TAMBAH USER
|--------------------------------------------------------------------------
*/
if (isset($_POST['btn_add_user'])) {

    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $password     = $_POST['password'] ?? '';
    $role         = $_POST['role'] ?? '';

    if ($nama_lengkap === '' || $username === '' || $password === '' || $role !== 'admin') {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=invalid');
        exit;
    }

    // Cek username unik
    $existing = db_find_one('user', 'username', $username);
    if ($existing !== null) {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=username_used');
        exit;
    }

    $new_id = db_next_id('user', 'id_user');
    $users  = db_read('user');
    $users[] = [
        'id_user'      => $new_id,
        'username'     => $username,
        'password'     => md5($password),
        'role'         => 'admin',
        'nama_lengkap' => $nama_lengkap,
    ];
    db_write('user', $users);

    header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=success_add');
    exit;
}

/*
|--------------------------------------------------------------------------
| EDIT USER
|--------------------------------------------------------------------------
*/
if (isset($_POST['btn_edit_user'])) {

    $id_user      = (int) ($_POST['id_user'] ?? 0);
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $password     = $_POST['password'] ?? '';
    $role         = $_POST['role'] ?? 'admin';

    if ($id_user <= 0 || $nama_lengkap === '' || $username === '') {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=invalid');
        exit;
    }

    // Cek username unik (selain diri sendiri)
    $existing = db_find_one('user', 'username', $username);
    if ($existing !== null && (int)$existing['id_user'] !== $id_user) {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=username_used');
        exit;
    }

    $users = db_read('user');
    foreach ($users as &$u) {
        if ((int)$u['id_user'] === $id_user) {
            $u['nama_lengkap'] = $nama_lengkap;
            $u['username']     = $username;
            $u['role']         = 'admin';
            if (!empty($password)) {
                $u['password'] = md5($password);
            }
            break;
        }
    }
    unset($u);
    db_write('user', $users);

    header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=success_edit');
    exit;
}

/*
|--------------------------------------------------------------------------
| HAPUS USER
|--------------------------------------------------------------------------
*/
if (isset($_POST['btn_delete_user'])) {

    $id_user = (int) ($_POST['id_user'] ?? 0);

    if ($id_user <= 0) {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=invalid');
        exit;
    }

    // Admin tidak boleh hapus dirinya sendiri
    if ($id_user === $id_admin) {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=forbidden');
        exit;
    }

    $users = array_values(array_filter(
        db_read('user'),
        fn($u) => (int)$u['id_user'] !== $id_user
    ));
    db_write('user', $users);

    header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=success_delete');
    exit;
}

/*
|--------------------------------------------------------------------------
| DEFAULT
|--------------------------------------------------------------------------
*/
header('Location: ../dashboard/admin?page=' . urlencode('Data User'));
exit;
