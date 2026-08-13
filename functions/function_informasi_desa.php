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
    $page = rawurlencode('Informasi Desa');
    header("Location: ../dashboard/admin?page={$page}&action=" . urlencode($action) . "&result=" . urlencode($result));
    exit;
}

// ===============================
// TAMBAH INFORMASI
// ===============================
if (isset($_POST['btn_add_info'])) {

    $judul    = trim($_POST['judul'] ?? '');
    $isi      = trim($_POST['isi'] ?? '');
    $kategori = $_POST['kategori'] ?? '';
    $tanggal  = $_POST['tanggal'] ?? '';
    $penulis  = trim($_POST['penulis'] ?? '');

    if (
        strlen($judul) < 5 ||
        strlen($isi) < 10 ||
        !in_array($kategori, ['Berita', 'Pengumuman', 'Agenda']) ||
        empty($tanggal) ||
        empty($penulis)
    ) {
        redirectAlert('add', 'invalid');
    }

    $data   = db_read('informasi_desa');
    $new_id = db_next_id('informasi_desa', 'id_info');
    $data[] = [
        'id_info'  => $new_id,
        'judul'    => $judul,
        'isi'      => $isi,
        'kategori' => $kategori,
        'tanggal'  => $tanggal,
        'penulis'  => $penulis,
    ];
    $ok = db_write('informasi_desa', $data);

    redirectAlert('add', $ok ? 'success' : 'failed');
}

// ===============================
// EDIT INFORMASI
// ===============================
if (isset($_POST['btn_edit_info'])) {

    $id_info  = (int) ($_POST['id_info'] ?? 0);
    $judul    = trim($_POST['judul'] ?? '');
    $isi      = trim($_POST['isi'] ?? '');
    $kategori = $_POST['kategori'] ?? '';
    $tanggal  = $_POST['tanggal'] ?? '';
    $penulis  = trim($_POST['penulis'] ?? '');

    if (
        $id_info <= 0 ||
        strlen($judul) < 5 ||
        strlen($isi) < 10 ||
        !in_array($kategori, ['Berita', 'Pengumuman', 'Agenda']) ||
        empty($tanggal) ||
        empty($penulis)
    ) {
        redirectAlert('edit', 'invalid');
    }

    $data = db_read('informasi_desa');
    $ok   = false;
    foreach ($data as &$row) {
        if ((int)$row['id_info'] === $id_info) {
            $row['judul']    = $judul;
            $row['isi']      = $isi;
            $row['kategori'] = $kategori;
            $row['tanggal']  = $tanggal;
            $row['penulis']  = $penulis;
            $ok = true;
            break;
        }
    }
    unset($row);

    if ($ok) db_write('informasi_desa', $data);
    redirectAlert('edit', $ok ? 'success' : 'failed');
}

// ===============================
// HAPUS INFORMASI
// ===============================
if (isset($_POST['btn_delete_info'])) {

    $id_info = (int) ($_POST['id_info'] ?? 0);
    if ($id_info <= 0) {
        redirectAlert('delete', 'invalid');
    }

    $data = array_values(array_filter(
        db_read('informasi_desa'),
        fn($row) => (int)$row['id_info'] !== $id_info
    ));
    $ok = db_write('informasi_desa', $data);

    redirectAlert('delete', $ok ? 'success' : 'failed');
}

// ===============================
// FALLBACK
// ===============================
redirectAlert('unknown', 'error');
