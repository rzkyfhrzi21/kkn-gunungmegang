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
    $page = rawurlencode('Data Penduduk');
    header("Location: ../dashboard/admin?page={$page}&action=" . urlencode($action) . "&result=" . urlencode($result));
    exit;
}

// ===============================
// TAMBAH PENDUDUK
// ===============================
if (isset($_POST['btn_add_penduduk'])) {

    $nik               = trim($_POST['nik'] ?? '');
    $nama_lengkap      = trim($_POST['nama_lengkap'] ?? '');
    $tempat_lahir      = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir     = $_POST['tanggal_lahir'] ?? null;
    $jenis_kelamin     = $_POST['jenis_kelamin'] ?? null;
    $alamat            = trim($_POST['alamat'] ?? '');
    $status_perkawinan = $_POST['status_perkawinan'] ?? null;
    $agama             = trim($_POST['agama'] ?? '');
    $pekerjaan         = trim($_POST['pekerjaan'] ?? '');
    $kewarganegaraan   = $_POST['kewarganegaraan'] ?? 'WNI';

    // validasi dasar
    if (strlen($nik) !== 16 || !ctype_digit($nik) || $nama_lengkap === '') {
        redirectAlert('add', 'invalid');
    }

    // cek NIK unik
    if (db_find_one('penduduk', 'nik', $nik) !== null) {
        redirectAlert('add', 'duplicate_nik');
    }

    $data   = db_read('penduduk');
    $new_id = db_next_id('penduduk', 'id_penduduk');
    $data[] = [
        'id_penduduk'      => $new_id,
        'nik'              => $nik,
        'nama_lengkap'     => $nama_lengkap,
        'tempat_lahir'     => $tempat_lahir,
        'tanggal_lahir'    => $tanggal_lahir,
        'jenis_kelamin'    => $jenis_kelamin,
        'alamat'           => $alamat,
        'status_perkawinan'=> $status_perkawinan,
        'agama'            => $agama,
        'pekerjaan'        => $pekerjaan,
        'kewarganegaraan'  => $kewarganegaraan,
    ];
    $ok = db_write('penduduk', $data);

    redirectAlert('add', $ok ? 'success' : 'failed');
}

// ===============================
// EDIT PENDUDUK
// ===============================
if (isset($_POST['btn_edit_penduduk'])) {

    $id_penduduk       = (int) ($_POST['id_penduduk'] ?? 0);
    $nik               = trim($_POST['nik'] ?? '');
    $nama_lengkap      = trim($_POST['nama_lengkap'] ?? '');
    $tempat_lahir      = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir     = $_POST['tanggal_lahir'] ?? null;
    $jenis_kelamin     = $_POST['jenis_kelamin'] ?? null;
    $alamat            = trim($_POST['alamat'] ?? '');
    $status_perkawinan = $_POST['status_perkawinan'] ?? null;
    $agama             = trim($_POST['agama'] ?? '');
    $pekerjaan         = trim($_POST['pekerjaan'] ?? '');
    $kewarganegaraan   = $_POST['kewarganegaraan'] ?? 'WNI';

    if ($id_penduduk <= 0 || strlen($nik) !== 16 || $nama_lengkap === '') {
        redirectAlert('edit', 'invalid');
    }

    // cek NIK unik (selain diri sendiri)
    $existing = db_find_one('penduduk', 'nik', $nik);
    if ($existing !== null && (int)$existing['id_penduduk'] !== $id_penduduk) {
        redirectAlert('edit', 'duplicate_nik');
    }

    $data = db_read('penduduk');
    $ok   = false;
    foreach ($data as &$row) {
        if ((int)$row['id_penduduk'] === $id_penduduk) {
            $row['nik']               = $nik;
            $row['nama_lengkap']      = $nama_lengkap;
            $row['tempat_lahir']      = $tempat_lahir;
            $row['tanggal_lahir']     = $tanggal_lahir;
            $row['jenis_kelamin']     = $jenis_kelamin;
            $row['alamat']            = $alamat;
            $row['status_perkawinan'] = $status_perkawinan;
            $row['agama']             = $agama;
            $row['pekerjaan']         = $pekerjaan;
            $row['kewarganegaraan']   = $kewarganegaraan;
            $ok = true;
            break;
        }
    }
    unset($row);

    if ($ok) db_write('penduduk', $data);
    redirectAlert('edit', $ok ? 'success' : 'failed');
}

// ===============================
// HAPUS PENDUDUK (ADMIN ONLY)
// ===============================
if (isset($_POST['btn_delete_penduduk'])) {

    $id_penduduk = (int) ($_POST['id_penduduk'] ?? 0);
    if ($id_penduduk <= 0) {
        redirectAlert('delete', 'invalid');
    }

    $data = array_values(array_filter(
        db_read('penduduk'),
        fn($row) => (int)$row['id_penduduk'] !== $id_penduduk
    ));
    $ok = db_write('penduduk', $data);

    redirectAlert('delete', $ok ? 'success' : 'failed');
}

// ===============================
// FALLBACK
// ===============================
redirectAlert('unknown', 'error');
