<?php
// functions/function_aspirasi.php
// Endpoint publik: simpan pengaduan & aspirasi dari form Kontak.
// Data disimpan ke db/json/aspirasi.json
//
// Struktur:
//  - aspirasi_process($data, $ip) : inti logika (validasi + rate limit + simpan),
//    dipisah agar bisa diuji langsung oleh functions/tests/test_aspirasi.php.
//  - Bagian HTTP (di bawah) hanya berjalan saat diakses lewat web
//    (PHP_SAPI !== 'cli'); saat di-require dari CLI, file ini murni library.

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Proses satu laporan aspirasi.
 *
 * @param array  $data Input mentah dari form: nama, telepon, subjek, pesan.
 * @param string $ip   IP pengirim (untuk rate limit 5 laporan/jam).
 * @return array ['ok' => true, 'message' => ...] atau ['ok' => false, 'error' => ...]
 */
function aspirasi_process(array $data, string $ip = 'unknown'): array
{
    $nama    = get_input($data, 'nama', 150);
    $telepon = get_input($data, 'telepon', 20);
    $subjek  = get_input($data, 'subjek', 50);
    $pesan   = get_input($data, 'pesan', 2000);

    if ($nama === '') return ['ok' => false, 'error' => 'Nama lengkap wajib diisi.'];
    if ($telepon === '') return ['ok' => false, 'error' => 'Nomor WhatsApp wajib diisi.'];
    $telepon = preg_replace('/[^0-9+]/', '', $telepon);
    if ($telepon === '') return ['ok' => false, 'error' => 'Nomor WhatsApp tidak valid.'];
    if (!in_array($subjek, ['infrastruktur', 'pelayanan', 'keamanan', 'lainnya'], true)) {
        return ['ok' => false, 'error' => 'Kategori laporan tidak valid.'];
    }
    if ($pesan === '') return ['ok' => false, 'error' => 'Pesan / laporan wajib diisi.'];

    // Rate limit: maksimal 5 laporan per jam per IP
    $rows = db_read('aspirasi');
    $cutoff = time() - 3600;
    $recent = 0;
    foreach ($rows as $r) {
        if (($r['ip'] ?? '') === $ip && strtotime((string)($r['tanggal'] ?? '')) >= $cutoff) {
            $recent++;
        }
    }
    if ($recent >= 5) {
        return ['ok' => false, 'error' => 'Terlalu banyak laporan. Silakan coba lagi beberapa saat.'];
    }

    $rows[] = [
        'id'       => count($rows) + 1,
        'nama'     => $nama,
        'telepon'  => $telepon,
        'subjek'   => $subjek,
        'pesan'    => $pesan,
        'ip'       => $ip,
        'tanggal'  => date('c'),
    ];

    db_write('aspirasi', $rows);

    return ['ok' => true, 'message' => 'Laporan berhasil dikirim. Terima kasih, kami akan segera menindaklanjuti.'];
}

/* ================= Bagian HTTP (web only) ================= */

function aspirasi_fail(string $msg): void
{
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

if (PHP_SAPI !== 'cli') {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Metode tidak diizinkan.']);
        exit;
    }

    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        aspirasi_fail('Data tidak valid.');
    }

    $result = aspirasi_process($data, $_SERVER['REMOTE_ADDR'] ?? 'unknown');
    if (!$result['ok']) {
        aspirasi_fail($result['error']);
    }

    echo json_encode(['ok' => true, 'message' => $result['message']], JSON_UNESCAPED_UNICODE);
    exit;
}