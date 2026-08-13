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
// TAMBAH KARTU KELUARGA
// ===============================
if (isset($_POST['btn_add_kk'])) {

    $nomor_kk           = trim($_POST['nomor_kk'] ?? '');
    $id_kepala_keluarga = (int) ($_POST['id_kepala_keluarga'] ?? 0);
    $alamat             = trim($_POST['alamat'] ?? '');

    if (strlen($nomor_kk) !== 16 || !ctype_digit($nomor_kk) || $id_kepala_keluarga <= 0 || strlen($alamat) < 10) {
        redirectAlert('add', 'invalid');
    }

    // cek nomor KK unik
    if (db_find_one('kartu_keluarga', 'nomor_kk', $nomor_kk) !== null) {
        redirectAlert('add', 'duplicate');
    }

    $data   = db_read('kartu_keluarga');
    $new_id = db_next_id('kartu_keluarga', 'id_kk');
    $data[] = [
        'id_kk'             => $new_id,
        'nomor_kk'          => $nomor_kk,
        'id_kepala_keluarga'=> $id_kepala_keluarga,
        'alamat'            => $alamat,
    ];
    $ok = db_write('kartu_keluarga', $data);

    redirectAlert('add', $ok ? 'success' : 'failed');
}

// ===============================
// EDIT KARTU KELUARGA
// ===============================
if (isset($_POST['btn_edit_kk'])) {

    $id_kk              = (int) ($_POST['id_kk'] ?? 0);
    $nomor_kk           = trim($_POST['nomor_kk'] ?? '');
    $id_kepala_keluarga = (int) ($_POST['id_kepala_keluarga'] ?? 0);
    $alamat             = trim($_POST['alamat'] ?? '');

    if ($id_kk <= 0 || strlen($nomor_kk) !== 16 || !ctype_digit($nomor_kk) || $id_kepala_keluarga <= 0 || strlen($alamat) < 10) {
        redirectAlert('edit', 'invalid');
    }

    // cek nomor KK unik kecuali dirinya
    $existing = db_find_one('kartu_keluarga', 'nomor_kk', $nomor_kk);
    if ($existing !== null && (int)$existing['id_kk'] !== $id_kk) {
        redirectAlert('edit', 'duplicate');
    }

    $data = db_read('kartu_keluarga');
    $ok   = false;
    foreach ($data as &$row) {
        if ((int)$row['id_kk'] === $id_kk) {
            $row['nomor_kk']           = $nomor_kk;
            $row['id_kepala_keluarga'] = $id_kepala_keluarga;
            $row['alamat']             = $alamat;
            $ok = true;
            break;
        }
    }
    unset($row);

    if ($ok) db_write('kartu_keluarga', $data);
    redirectAlert('edit', $ok ? 'success' : 'failed');
}

// ===============================
// HAPUS KARTU KELUARGA (ADMIN ONLY)
// ===============================
if (isset($_POST['btn_delete_kk'])) {

    $id_kk = (int) ($_POST['id_kk'] ?? 0);
    if ($id_kk <= 0) {
        redirectAlert('delete', 'invalid');
    }

    // cek apakah KK masih punya anggota
    $anggota = db_find_all('anggota_keluarga', 'id_kk', (string)$id_kk);
    if (!empty($anggota)) {
        redirectAlert('delete', 'used');
    }

    $data = array_values(array_filter(
        db_read('kartu_keluarga'),
        fn($row) => (int)$row['id_kk'] !== $id_kk
    ));
    $ok = db_write('kartu_keluarga', $data);

    redirectAlert('delete', $ok ? 'success' : 'failed');
}

// ===============================
// FALLBACK
// ===============================
redirectAlert('unknown', 'error');
