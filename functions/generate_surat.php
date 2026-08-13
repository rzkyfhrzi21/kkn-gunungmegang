<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') {
    http_response_code(403);
    die('Akses ditolak');
}

// TCPDF
require_once __DIR__ . '/../dashboard/assets/vendor/tcpdf/tcpdf.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=invalid");
    exit;
}

// ================= AMBIL DATA PERMOHONAN =================
$permohonan = db_find_one('permohonan_surat', 'id_permohonan', (string)$id);
if (!$permohonan) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=notfound");
    exit;
}

$penduduk   = db_find_one('penduduk', 'id_penduduk', (string)$permohonan['id_penduduk']);
$jenis      = db_find_one('jenis_surat', 'id_jenis_surat', (string)$permohonan['id_jenis_surat']);

if (!$penduduk || !$jenis) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=notfound");
    exit;
}

// Gabungkan data (kompatibel dengan template lama yang pakai $data['nama_lengkap'] dll)
$data = array_merge($permohonan, $penduduk, $jenis);

// ================= TEMPLATE MAP =================
$templateMap = [
    'Surat Keterangan Domisili'     => 'surat_keterangan_domisili.php',
    'Surat Keterangan Kelahiran'    => 'surat_keterangan_kelahiran.php',
    'Surat Keterangan Kematian'     => 'surat_keterangan_kematian.php',
    'Surat Keterangan Usaha'        => 'surat_keterangan_usaha.php',
    'Surat Keterangan Tidak Mampu'  => 'surat_keterangan_tidak_mampu.php',
];

if (!isset($templateMap[$data['nama_surat']])) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=template_not_found");
    exit;
}

$templatePath = __DIR__ . '/../dashboard/assets/surat_templates/' . $templateMap[$data['nama_surat']];
if (!file_exists($templatePath)) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=template_missing");
    exit;
}

// ================= LOAD HTML TEMPLATE =================
ob_start();
include $templatePath;
$html = ob_get_clean();

if (trim($html) === '') {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=template_empty");
    exit;
}

// ================= SIAPKAN FOLDER GENERATED =================
$genDir = __DIR__ . '/../dashboard/assets/generated/';
if (!is_dir($genDir)) {
    @mkdir($genDir, 0777, true);
}
if (!is_dir($genDir) || !is_writable($genDir)) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=folder_not_writable");
    exit;
}

// ================= NAMA FILE =================
$kodeSurat   = strtoupper(trim($data['kode_surat'] ?? 'SURAT'));
$noSurat     = (int)($data['nomor_surat'] ?? $id);
$namaPemohon = strtoupper($data['nama_lengkap'] ?? 'PEMOHON');
$namaPemohon = preg_replace('/[^A-Z0-9]+/', '_', $namaPemohon);
$namaPemohon = trim($namaPemohon, '_') ?: 'PEMOHON';

$filename = $kodeSurat . '.' . $noSurat . '.' . $namaPemohon . '.pdf';
$savePath = $genDir . $filename;

// ================= BUAT PDF (TCPDF) =================
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetFont('times', '', 12);
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output($savePath, 'F');

if (!file_exists($savePath)) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=pdf_failed");
    exit;
}

// ================= UPDATE JSON file_pdf =================
$all = db_read('permohonan_surat');
foreach ($all as &$row) {
    if ((int)$row['id_permohonan'] === $id) {
        $row['file_pdf'] = $filename;
        break;
    }
}
unset($row);
db_write('permohonan_surat', $all);

// ================= PREVIEW INLINE =================
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . filesize($savePath));
readfile($savePath);
exit;
