<?php
session_start();
require_once 'config.php';

// Proteksi role admin
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') {
    header("Location: ../auth/logout");
    exit;
}

// helper redirect
function redirectAlert(string $action, string $result): void
{
    $page = rawurlencode('Jenis Surat');
    header("Location: ../dashboard/admin?page={$page}&action=" . urlencode($action) . "&result=" . urlencode($result));
    exit;
}

// ================= TAMBAH =================
if (isset($_POST['btn_add'])) {

    $kode = trim($_POST['kode_surat'] ?? '');
    $nama = trim($_POST['nama_surat'] ?? '');
    $ket  = trim($_POST['keterangan'] ?? '');

    if ($kode === '' || $nama === '') {
        redirectAlert('add', 'invalid');
    }

    // kode unik
    if (db_find_one('jenis_surat', 'kode_surat', $kode) !== null) {
        redirectAlert('add', 'duplicate');
    }

    $data   = db_read('jenis_surat');
    $new_id = db_next_id('jenis_surat', 'id_jenis_surat');
    $data[] = [
        'id_jenis_surat' => $new_id,
        'nama_surat'     => $nama,
        'kode_surat'     => $kode,
        'keterangan'     => $ket,
    ];
    $ok = db_write('jenis_surat', $data);

    redirectAlert('add', $ok ? 'success' : 'failed');
}

// ================= EDIT =================
if (isset($_POST['btn_edit'])) {

    $id   = (int) ($_POST['id_jenis_surat'] ?? 0);
    $kode = trim($_POST['kode_surat'] ?? '');
    $nama = trim($_POST['nama_surat'] ?? '');
    $ket  = trim($_POST['keterangan'] ?? '');

    if ($id <= 0 || $kode === '' || $nama === '') {
        redirectAlert('edit', 'invalid');
    }

    // kode unik kecuali dirinya
    $existing = db_find_one('jenis_surat', 'kode_surat', $kode);
    if ($existing !== null && (int)$existing['id_jenis_surat'] !== $id) {
        redirectAlert('edit', 'duplicate');
    }

    $data = db_read('jenis_surat');
    $ok   = false;
    foreach ($data as &$row) {
        if ((int)$row['id_jenis_surat'] === $id) {
            $row['kode_surat'] = $kode;
            $row['nama_surat'] = $nama;
            $row['keterangan'] = $ket;
            $ok = true;
            break;
        }
    }
    unset($row);

    if ($ok) db_write('jenis_surat', $data);
    redirectAlert('edit', $ok ? 'success' : 'failed');
}

// ================= DELETE (ADMIN) =================
if (isset($_POST['btn_delete'])) {

    $id = (int) ($_POST['id_jenis_surat'] ?? 0);
    if ($id <= 0) {
        redirectAlert('delete', 'invalid');
    }

    // cek apakah masih digunakan di permohonan_surat
    $used = db_find_all('permohonan_surat', 'id_jenis_surat', (string)$id);
    if (!empty($used)) {
        redirectAlert('delete', 'used');
    }

    $data = array_values(array_filter(
        db_read('jenis_surat'),
        fn($row) => (int)$row['id_jenis_surat'] !== $id
    ));
    $ok = db_write('jenis_surat', $data);

    redirectAlert('delete', $ok ? 'success' : 'failed');
}

// fallback
redirectAlert('unknown', 'error');
