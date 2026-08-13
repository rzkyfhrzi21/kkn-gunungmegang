<?php
session_start();
require_once 'config.php';

// ===============================
// PROTEKSI LOGIN (admin only)
// ===============================
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') {
    header("Location: ../auth/logout");
    exit;
}

// helper redirect
function redirectAlert(string $action, string $result): void
{
    $page = rawurlencode('Aparat Desa');
    header("Location: ../dashboard/admin?page={$page}&action=" . urlencode($action) . "&result=" . urlencode($result));
    exit;
}

// ===============================
// TAMBAH
// ===============================
if (isset($_POST['btn_add_aparat'])) {

    $nama    = trim($_POST['nama'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $mulai   = trim($_POST['periode_mulai'] ?? '');
    $selesai = trim($_POST['periode_selesai'] ?? '') ?: null;

    if ($nama === '' || $jabatan === '' || $mulai === '') {
        redirectAlert('add', 'invalid');
    }

    $data      = db_read('aparat_desa');
    $new_id    = db_next_id('aparat_desa', 'id_aparat');
    $data[]    = [
        'id_aparat'       => $new_id,
        'nama'            => $nama,
        'jabatan'         => $jabatan,
        'periode_mulai'   => $mulai,
        'periode_selesai' => $selesai,
    ];
    $ok = db_write('aparat_desa', $data);

    redirectAlert('add', $ok ? 'success' : 'failed');
}

// ===============================
// EDIT
// ===============================
if (isset($_POST['btn_edit_aparat'])) {

    $id      = (int) ($_POST['id_aparat'] ?? 0);
    $nama    = trim($_POST['nama'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $mulai   = trim($_POST['periode_mulai'] ?? '');
    $selesai = trim($_POST['periode_selesai'] ?? '') ?: null;

    if ($id <= 0 || $nama === '' || $jabatan === '' || $mulai === '') {
        redirectAlert('edit', 'invalid');
    }

    $data = db_read('aparat_desa');
    $ok   = false;
    foreach ($data as &$row) {
        if ((int)$row['id_aparat'] === $id) {
            $row['nama']            = $nama;
            $row['jabatan']         = $jabatan;
            $row['periode_mulai']   = $mulai;
            $row['periode_selesai'] = $selesai;
            $ok = true;
            break;
        }
    }
    unset($row);

    if ($ok) db_write('aparat_desa', $data);
    redirectAlert('edit', $ok ? 'success' : 'failed');
}

// ===============================
// HAPUS (ADMIN ONLY)
// ===============================
if (isset($_POST['btn_delete_aparat'])) {

    $id = (int) ($_POST['id_aparat'] ?? 0);
    if ($id <= 0) {
        redirectAlert('delete', 'invalid');
    }

    $data = array_values(array_filter(
        db_read('aparat_desa'),
        fn($row) => (int)$row['id_aparat'] !== $id
    ));
    $ok = db_write('aparat_desa', $data);

    redirectAlert('delete', $ok ? 'success' : 'failed');
}

// fallback
redirectAlert('unknown', 'error');
