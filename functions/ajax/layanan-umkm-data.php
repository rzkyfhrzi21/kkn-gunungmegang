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

// Sanitasi output — pastikan hanya field yang diperlukan yang dikirim.
// Data sudah disanitasi saat save (strip_tags + whitelist foto/maps/wa),
// dan sisi frontend meng-escape saat render (esc()), jadi tidak perlu
// htmlspecialchars di sini (menghindari double-escape: "&" jadi "&amp;").
$result = [];
foreach ($daftar as $item) {
    if (!is_array($item)) continue;
    $baris = [];
    foreach (($item['baris'] ?? []) as $b) {
        if (!is_array($b)) continue;
        $baris[] = [
            'ikon' => $b['ikon'] ?? '',
            'teks' => $b['teks'] ?? '',
        ];
    }
    $result[] = [
        'kategori' => $item['kategori'] ?? '',
        'badge'    => $item['badge']    ?? '',
        'nama'     => $item['nama']     ?? '',
        'subjudul' => $item['subjudul'] ?? '',
        'foto'     => $item['foto']     ?? '',
        'maps'     => $item['maps']     ?? '',
        'wa'       => $item['wa']       ?? '',
        'baris'    => $baris,
    ];
}

echo json_encode(['ok' => true, 'daftar' => $result], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
