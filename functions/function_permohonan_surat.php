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

// helper redirect (sweetalert)
function redirectAlert(string $action, string $result): void
{
    $page = rawurlencode('Permohonan Surat');
    header("Location: ../dashboard/admin?page={$page}&action=" . urlencode($action) . "&result=" . urlencode($result));
    exit;
}

// ============================
// TAMBAH PERMOHONAN + GENERATE PDF
// ============================
if (isset($_POST['btn_add_permohonan'])) {

    $id_penduduk    = (int) ($_POST['id_penduduk'] ?? 0);
    $id_jenis_surat = (int) ($_POST['id_jenis_surat'] ?? 0);
    $tanggal        = $_POST['tanggal_pengajuan'] ?? date('Y-m-d');
    $keperluan      = trim($_POST['keperluan'] ?? '');

    if ($id_penduduk <= 0 || $id_jenis_surat <= 0 || strlen($keperluan) < 10) {
        redirectAlert('add', 'invalid');
    }

    // Hitung nomor surat per jenis surat
    $semua = db_find_all('permohonan_surat', 'id_jenis_surat', (string)$id_jenis_surat);
    $maxNo = 0;
    foreach ($semua as $s) {
        $no = (int)($s['nomor_surat'] ?? 0);
        if ($no > $maxNo) $maxNo = $no;
    }
    $nomorSurat = $maxNo + 1;

    $new_id = db_next_id('permohonan_surat', 'id_permohonan');
    $data   = db_read('permohonan_surat');
    $data[] = [
        'id_permohonan'    => $new_id,
        'id_penduduk'      => $id_penduduk,
        'id_jenis_surat'   => $id_jenis_surat,
        'nomor_surat'      => (string)$nomorSurat,
        'tanggal_pengajuan'=> $tanggal,
        'keperluan'        => $keperluan,
        'file_pdf'         => null,
    ];
    $ok = db_write('permohonan_surat', $data);

    if (!$ok) {
        redirectAlert('add', 'failed');
    }

    // Generate PDF
    header("Location: ../functions/generate_surat.php?id=" . $new_id);
    exit;
}

// ============================
// EDIT PERMOHONAN
// ============================
if (isset($_POST['btn_edit_permohonan'])) {

    $id_permohonan  = (int) ($_POST['id_permohonan'] ?? 0);
    $id_penduduk    = (int) ($_POST['id_penduduk'] ?? 0);
    $id_jenis_surat = (int) ($_POST['id_jenis_surat'] ?? 0);
    $tanggal        = $_POST['tanggal_pengajuan'] ?? '';
    $keperluan      = trim($_POST['keperluan'] ?? '');

    if ($id_permohonan <= 0 || $id_penduduk <= 0 || $id_jenis_surat <= 0 || strlen($keperluan) < 10) {
        redirectAlert('edit', 'invalid');
    }

    $data = db_read('permohonan_surat');
    $ok   = false;
    foreach ($data as &$row) {
        if ((int)$row['id_permohonan'] === $id_permohonan) {
            $row['id_penduduk']       = $id_penduduk;
            $row['id_jenis_surat']    = $id_jenis_surat;
            $row['tanggal_pengajuan'] = $tanggal;
            $row['keperluan']         = $keperluan;
            $ok = true;
            break;
        }
    }
    unset($row);

    if ($ok) db_write('permohonan_surat', $data);
    redirectAlert('edit', $ok ? 'success' : 'failed');
}

// ============================
// HAPUS PERMOHONAN
// ============================
if (isset($_POST['btn_delete_permohonan'])) {

    $id_permohonan = (int) ($_POST['id_permohonan'] ?? 0);
    if ($id_permohonan <= 0) {
        redirectAlert('delete', 'invalid');
    }

    // Hapus file fisik PDF jika ada
    $row = db_find_one('permohonan_surat', 'id_permohonan', (string)$id_permohonan);
    if ($row && !empty($row['file_pdf'])) {
        $path = __DIR__ . '/../dashboard/assets/generated/' . $row['file_pdf'];
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    $data = array_values(array_filter(
        db_read('permohonan_surat'),
        fn($r) => (int)$r['id_permohonan'] !== $id_permohonan
    ));
    $ok = db_write('permohonan_surat', $data);

    redirectAlert('delete', $ok ? 'success' : 'failed');
}

// ============================
// FALLBACK
// ============================
redirectAlert('unknown', 'error');
