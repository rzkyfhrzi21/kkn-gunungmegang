<?php
// ============================================================================
// functions/tests/test_aspirasi.php
// Tes modul ASPIRASI PUBLIK (functions/function_aspirasi.php, fungsi inti
// aspirasi_process()).
//
// Jalankan: php functions/tests/test_aspirasi.php
//
// Yang dites di file ini:
//  1. input valid                  -> ok + pesan sukses + tersimpan di db/json/aspirasi.json
//  2. nama kosong                  -> fail 'Nama lengkap wajib diisi.'
//  3. telepon kosong               -> fail 'Nomor WhatsApp wajib diisi.'
//  4. telepon bukan digit          -> fail 'Nomor WhatsApp tidak valid.'
//  5. telepon disanitasi           -> '(0812) 3456-7890' -> '081234567890'
//  6. subjek tidak dikenal         -> fail 'Kategori laporan tidak valid.'
//  7. pesan kosong                 -> fail 'Pesan / laporan wajib diisi.'
//  8. rate limit: 5 laporan/jam/IP -> laporan ke-6 ditolak
//  9. rate limit: IP berbeda       -> tetap diterima
//  10. strip XSS pada nama/pesan   -> tidak tersimpan tag HTML
//
// Catatan:
//  - Bagian HTTP (metode GET -> 405, json body, dsb.) di-cover oleh tes UI
//    Chrome (crud-ui-test.js) karena handler web exit-based.
//  - Seluruh perubahan db/json/aspirasi.json di-restore otomatis oleh bootstrap.
// ============================================================================

require_once __DIR__ . '/bootstrap.php';
require_once TEST_BASE . 'functions/function_aspirasi.php';

/* ---------- 1. input valid ---------- */
$r = aspirasi_process(['nama' => 'Penguji Modul', 'telepon' => '081234567890', 'subjek' => 'infrastruktur', 'pesan' => 'Tes aspirasi dari unit test.'], '10.0.0.1');
t('aspirasi valid -> ok + message', $r['ok'] === true && isset($r['message']), json_encode($r));
$rows = db_read('aspirasi');
$mine = array_values(array_filter($rows, fn($x) => ($x['nama'] ?? '') === 'Penguji Modul'));
t(
    'tersimpan di aspirasi.json (nama/telepon/subjek/pesan/ip)',
    count($mine) === 1 && $mine[0]['subjek'] === 'infrastruktur' && $mine[0]['ip'] === '10.0.0.1',
    json_encode($mine)
);

/* ---------- 2-7. validasi ---------- */
$r = aspirasi_process(['nama' => '', 'telepon' => '0812', 'subjek' => 'lainnya', 'pesan' => 'x'], '10.0.0.2');
t('nama kosong -> fail', $r['ok'] === false && strpos($r['error'] ?? '', 'Nama lengkap') !== false, json_encode($r));

$r = aspirasi_process(['nama' => 'A', 'telepon' => '', 'subjek' => 'lainnya', 'pesan' => 'x'], '10.0.0.2');
t('telepon kosong -> fail', $r['ok'] === false && strpos($r['error'] ?? '', 'Nomor WhatsApp') !== false, json_encode($r));

$r = aspirasi_process(['nama' => 'A', 'telepon' => 'abc!!!', 'subjek' => 'lainnya', 'pesan' => 'x'], '10.0.0.2');
t('telepon bukan digit -> fail', $r['ok'] === false && strpos($r['error'] ?? '', 'tidak valid') !== false, json_encode($r));

$r = aspirasi_process(['nama' => 'A', 'telepon' => '(0812) 3456-7890', 'subjek' => 'lainnya', 'pesan' => 'x'], '10.0.0.2');
$rows = db_read('aspirasi');
$last = end($rows);
t('telepon disanitasi: "(0812) 3456-7890" -> "081234567890"', $r['ok'] === true && ($last['telepon'] ?? '') === '081234567890', json_encode($last));

$r = aspirasi_process(['nama' => 'A', 'telepon' => '0812', 'subjek' => 'hacking', 'pesan' => 'x'], '10.0.0.2');
t('subjek tidak dikenal -> fail', $r['ok'] === false && strpos($r['error'] ?? '', 'Kategori') !== false, json_encode($r));

$r = aspirasi_process(['nama' => 'A', 'telepon' => '0812', 'subjek' => 'lainnya', 'pesan' => '  '], '10.0.0.2');
t('pesan kosong -> fail', $r['ok'] === false && strpos($r['error'] ?? '', 'Pesan') !== false, json_encode($r));

/* ---------- 10. strip XSS ---------- */
$r = aspirasi_process(['nama' => '<script>alert(1)</script>Nama XSS', 'telepon' => '0812', 'subjek' => 'lainnya', 'pesan' => 'pesan <b>tebal</b>'], '10.0.0.2');
$rows = db_read('aspirasi');
$last = array_values(array_filter($rows, fn($x) => ($x['nama'] ?? '') === 'Nama XSS'))[0] ?? [];
t(
    'tag HTML dibuang dari nama & pesan',
    $r['ok'] === true && strpos($last['nama'] ?? '', '<script>') === false && strpos($last['pesan'] ?? '', '<b>') === false,
    json_encode($last)
);

/* ---------- 8-9. rate limit ---------- */
// Seed 5 laporan dari IP yang sama dalam 1 jam terakhir
$seed = [];
for ($i = 1; $i <= 5; $i++) {
    $seed[] = ['id' => $i, 'nama' => 'Spam' . $i, 'telepon' => '0812', 'subjek' => 'lainnya', 'pesan' => 'spam', 'ip' => '10.0.0.99', 'tanggal' => date('c')];
}
db_write('aspirasi', $seed);
$r = aspirasi_process(['nama' => 'B', 'telepon' => '0812', 'subjek' => 'lainnya', 'pesan' => 'x'], '10.0.0.99');
t('rate limit: laporan ke-6 dari IP sama -> ditolak', $r['ok'] === false && strpos($r['error'] ?? '', 'Terlalu banyak') !== false, json_encode($r));
$r = aspirasi_process(['nama' => 'C', 'telepon' => '0812', 'subjek' => 'lainnya', 'pesan' => 'x'], '10.0.0.100');
t('rate limit: IP berbeda -> tetap diterima', $r['ok'] === true, json_encode($r));

api_finish();