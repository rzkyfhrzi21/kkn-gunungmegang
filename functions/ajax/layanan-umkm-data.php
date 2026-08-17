<?php
// functions/ajax/layanan-umkm-data.php
// Endpoint publik ringan untuk data Layanan & UMKM.
// Hanya membaca file includes/ (read-only, tanpa auth, tanpa tulis data).
// Tidak ada data sensitif di sini — hanya katalog publik pekon.

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60'); // cache 60 detik di browser

$INCLUDES = dirname(__DIR__, 2) . '/includes';

$data = @include $INCLUDES . '/layanan_umkm.php';
if (!is_array($data)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Data tidak tersedia.']);
    exit;
}

$daftar = $data['daftar'] ?? [];

// Sanitasi output — pastikan hanya field yang diperlukan yang dikirim
$result = [];
foreach ($daftar as $item) {
    if (!is_array($item)) continue;
    $baris = [];
    foreach (($item['baris'] ?? []) as $b) {
        if (!is_array($b)) continue;
        $baris[] = [
            'ikon' => htmlspecialchars($b['ikon'] ?? '', ENT_QUOTES, 'UTF-8'),
            'teks' => htmlspecialchars($b['teks'] ?? '', ENT_QUOTES, 'UTF-8'),
        ];
    }
    $result[] = [
        'kategori' => htmlspecialchars($item['kategori'] ?? '', ENT_QUOTES, 'UTF-8'),
        'badge'    => htmlspecialchars($item['badge']    ?? '', ENT_QUOTES, 'UTF-8'),
        'nama'     => htmlspecialchars($item['nama']     ?? '', ENT_QUOTES, 'UTF-8'),
        'subjudul' => htmlspecialchars($item['subjudul'] ?? '', ENT_QUOTES, 'UTF-8'),
        'foto'     => htmlspecialchars($item['foto']     ?? '', ENT_QUOTES, 'UTF-8'),
        'maps'     => htmlspecialchars($item['maps']     ?? '', ENT_QUOTES, 'UTF-8'),
        'wa'       => htmlspecialchars($item['wa']       ?? '', ENT_QUOTES, 'UTF-8'),
        'baris'    => $baris,
    ];
}

echo json_encode(['ok' => true, 'daftar' => $result], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
