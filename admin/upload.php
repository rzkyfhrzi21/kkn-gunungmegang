<?php
/**
 * admin/upload.php
 * Endpoint AJAX (POST multipart) upload foto/video.
 * Foto maksimal 2 MB, video maksimal 15 MB.
 * Foto otomatis dikompres menjadi WebP (kualitas 80) jika memungkinkan.
 * Hasil: JSON {ok, path, type}
 */
require_once __DIR__ . '/../functions/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header("X-Robots-Tag: noindex, nofollow, noarchive", true);

$sesi_id = (int) ($_SESSION['sesi_id'] ?? 0);
$user    = db_find_one('user', 'id_user', (string)$sesi_id);
if (!$user || ($user['role'] ?? '') !== 'admin') {
    echo json_encode(['ok' => false, 'error' => 'Unauthorized', 'detail' => 'Sesi tidak valid atau bukan admin.']);
    exit;
}

// A08 - CSRF wajib untuk upload (dikecualikan saat mode test CLI)
if (!defined('ADMIN_API_TEST') && !csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
    echo json_encode(['ok' => false, 'error' => 'CSRF token tidak valid.']);
    exit;
}

$PHOTO_EXT = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp', 'ico', 'heic', 'heif', 'jfif'];
$VIDEO_EXT = ['mp4', 'mkv', 'mov', 'webm', 'avi', '3gp', 'm4v'];
$PHOTO_MAX = 2 * 1024 * 1024;    // 2 MB
$VIDEO_MAX = 15 * 1024 * 1024;   // 15 MB

if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'error' => 'Upload gagal', 'detail' => 'Tidak ada file yang diterima atau terjadi kesalahan upload.']);
    exit;
}

$file  = $_FILES['file'];
$ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$mime  = '';
if (function_exists('finfo_open')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
}

if (in_array($ext, $PHOTO_EXT, true)) {
    $type = 'image';
} elseif (in_array($ext, $VIDEO_EXT, true)) {
    $type = 'video';
} elseif (strpos($mime, 'image/') === 0) {
    $type = 'image';
} elseif (strpos($mime, 'video/') === 0) {
    $type = 'video';
} else {
    echo json_encode(['ok' => false, 'error' => 'Tipe file tidak diizinkan', 'detail' => 'Foto: ' . implode(', ', $PHOTO_EXT) . ' — Video: ' . implode(', ', $VIDEO_EXT)]);
    exit;
}

if ($type === 'image' && $file['size'] > $PHOTO_MAX) {
    echo json_encode(['ok' => false, 'error' => 'Ukuran foto melebihi batas', 'detail' => 'Maksimal 2 MB. Ukuran file: ' . round($file['size'] / 1024) . ' KB']);
    exit;
}
if ($type === 'video' && $file['size'] > $VIDEO_MAX) {
    echo json_encode(['ok' => false, 'error' => 'Ukuran video melebihi batas', 'detail' => 'Maksimal 15 MB. Ukuran file: ' . round($file['size'] / 1024 / 1024, 2) . ' MB']);
    exit;
}

if (strpos($mime, 'image/') === 0 && $type === 'image') {
    // verifikasi gambar benar-benar image
    $check = @getimagesize($file['tmp_name']);
    if ($check === false && !in_array($ext, ['ico', 'heic', 'heif'], true)) {
        echo json_encode(['ok' => false, 'error' => 'File bukan gambar valid', 'detail' => $file['name']]);
        exit;
    }
}

$uploadDir = dirname(__DIR__) . '/assets/uploads';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
if (!is_writable($uploadDir)) {
    echo json_encode(['ok' => false, 'error' => 'Folder upload tidak writable', 'detail' => $uploadDir]);
    exit;
}

$name = 'u_' . date('Ymd_His') . '_' . substr(uniqid(), -6) . '.' . $ext;
$dest = $uploadDir . '/' . $name;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['ok' => false, 'error' => 'Gagal menyimpan file', 'detail' => 'move_uploaded_file gagal.']);
    exit;
}

/* Kompres foto menjadi WebP (kualitas 80). Jika GD tidak bisa membaca format
   (mis. HEIC/HEIF/ICO) atau gagal, file asli tetap disimpan. */
$finalName = $name;
if ($type === 'image' && function_exists('imagewebp') && function_exists('imagecreatefromstring')) {
    $src = @imagecreatefromstring((string)file_get_contents($dest));
    if ($src !== false) {
        imagealphablending($src, true);
        imagesavealpha($src, true);
        $webpName = pathinfo($name, PATHINFO_FILENAME) . '.webp';
        if (imagewebp($src, $uploadDir . '/' . $webpName, 80)) {
            @unlink($dest);
            $finalName = $webpName;
        }
        imagedestroy($src);
    }
}

echo json_encode([
    'ok' => true,
    'path' => 'assets/uploads/' . $finalName,
    'type' => $type,
    'size' => filesize($uploadDir . '/' . $finalName),
    'name' => $file['name'],
]);