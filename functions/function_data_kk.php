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

// ===============================
// HELPER REDIRECT SWEETALERT
// ===============================
function redirectAlert(string $action, string $result): void
{
    $page = rawurlencode('Data KK');
    header("Location: ../dashboard/admin?page={$page}&action=" . urlencode($action) . "&result=" . urlencode($result));
    exit;
}

// ===============================
// TAMBAH ANGGOTA KELUARGA
// ===============================
if (isset($_POST['btn_add_anggota'])) {

    $id_kk       = (int) ($_POST['id_kk'] ?? 0);
    $id_penduduk = (int) ($_POST['id_penduduk'] ?? 0);
    $hubungan    = trim($_POST['hubungan'] ?? '');

    if ($id_kk <= 0 || $id_penduduk <= 0 || $hubungan === '') {
        redirectAlert('add', 'invalid');
    }

    // cek penduduk sudah terdaftar di KK lain
    $existing = db_find_all('anggota_keluarga', 'id_penduduk', (string)$id_penduduk);
    if (!empty($existing)) {
        redirectAlert('add', 'duplicate');
    }

    $data   = db_read('anggota_keluarga');
    $new_id = db_next_id('anggota_keluarga', 'id_anggota');
    $data[] = [
        'id_anggota'  => $new_id,
        'id_kk'       => $id_kk,
        'id_penduduk' => $id_penduduk,
        'hubungan'    => $hubungan,
    ];
    $ok = db_write('anggota_keluarga', $data);

    redirectAlert('add', $ok ? 'success' : 'failed');
}

// ===============================
// EDIT ANGGOTA KELUARGA
// ===============================
if (isset($_POST['btn_edit_anggota'])) {

    $id_anggota = (int) ($_POST['id_anggota'] ?? 0);
    $hubungan   = trim($_POST['hubungan'] ?? '');

    if ($id_anggota <= 0 || $hubungan === '') {
        redirectAlert('edit', 'invalid');
    }

    $data = db_read('anggota_keluarga');
    $ok   = false;
    foreach ($data as &$row) {
        if ((int)$row['id_anggota'] === $id_anggota) {
            $row['hubungan'] = $hubungan;
            $ok = true;
            break;
        }
    }
    unset($row);

    if ($ok) db_write('anggota_keluarga', $data);
    redirectAlert('edit', $ok ? 'success' : 'failed');
}

// ===============================
// HAPUS ANGGOTA (ADMIN ONLY)
// ===============================
if (isset($_POST['btn_delete_anggota'])) {

    $id_anggota = (int) ($_POST['id_anggota'] ?? 0);
    if ($id_anggota <= 0) {
        redirectAlert('delete', 'invalid');
    }

    $data = array_values(array_filter(
        db_read('anggota_keluarga'),
        fn($row) => (int)$row['id_anggota'] !== $id_anggota
    ));
    $ok = db_write('anggota_keluarga', $data);

    redirectAlert('delete', $ok ? 'success' : 'failed');
}

// ===============================
// FALLBACK
// ===============================
redirectAlert('unknown', 'error');
