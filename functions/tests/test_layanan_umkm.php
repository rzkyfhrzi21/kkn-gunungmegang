<?php
// ============================================================================
// functions/tests/test_layanan_umkm.php
// Tes modul LAYANAN & UMKM — gabungan fungsionalitas + keamanan.
//
// Jalankan: php functions/tests/test_layanan_umkm.php
//
// BAGIAN 1 — Struktur & CRUD:
//  1.  struktur item (daftar + key lengkap)
//  2.  save module DITOLAK (teks dikode di komponen)
//  3.  save_row foto lokal diterima / URL eksternal & path aneh dibuang
//  4.  save_row maps URL valid diterima / URL aneh dibuang
//  5.  save_row wa dibersihkan jadi digit
//  6.  list (rows, total, filterOptions)
//  7.  filter kategori
//  8.  search
//  9.  save_row tambah item baru
//  10. save_row edit item
//  11. save_row nama/kategori kosong → fail
//  12. delete item valid
//  13. delete index tak ada → fail
//
// BAGIAN 2 — Keamanan & Validasi:
//  14. XSS: tag HTML di-strip dari semua field teks
//  15. Persistensi: file data bebas <script>/javascript:/onerror
//  16. Batas panjang (truncation) setiap field
//  17. Foto: whitelist assets/uploads/ — 7 kasus
//  18. Maps: hanya http(s) valid — 7 kasus
//  19. WA: digit saja, max 30 — 4 kasus
//  20. Validasi wajib & baris kosong dibuang
//  21. save module (hero) DITOLAK
//  22. norm_layanan_umkm: item invalid dibuang + sanitasi + input non-array
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

// ============================================================================
// BAGIAN 1 — STRUKTUR & CRUD
// ============================================================================

$p = require TEST_BASE . 'includes/layanan_umkm.php';

/* 1. struktur key */
$strukturOk = true;
if (!array_key_exists('daftar', $p)) $strukturOk = false;
$needKeys = ['kategori', 'badge', 'nama', 'subjudul', 'foto', 'baris', 'maps', 'wa'];
$sawItem = false;
foreach (($p['daftar'] ?? []) as $item) {
    $sawItem = true;
    foreach ($needKeys as $k) if (!array_key_exists($k, $item)) $strukturOk = false;
}
t('struktur layanan & umkm lengkap (daftar + key item)', $strukturOk && $sawItem, json_encode($p));

/* 2. save module DITOLAK */
$r = api('save', 'layanan_umkm', ['subjudul' => 'X', 'judul' => 'Y', 'deskripsi' => 'Z']);
t('save layanan & umkm (module) ditolak - teks dikode di komponen', err($r), json_encode($r));

/* 3. foto: HANYA upload lokal (assets/uploads/...) */
$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'UMKM & Produk Lokal', 'nama' => 'Tes Foto Lokal', 'foto' => 'assets/uploads/umkm.webp',
]);
$idxFoto = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Foto Lokal') $idxFoto = (int)$row['index'];
$fotoOk = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Foto Lokal' && ($row['foto'] ?? '') === 'assets/uploads/umkm.webp') $fotoOk = true;
t('save_row foto upload lokal diterima', $idxFoto >= 0 && $fotoOk, json_encode($r['rows']));
api('delete', 'layanan_umkm', ['index' => $idxFoto]);

$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'UMKM & Produk Lokal', 'nama' => 'Tes Foto Eksternal', 'foto' => 'https://example.com/x.webp',
]);
$idxFoto = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Foto Eksternal') $idxFoto = (int)$row['index'];
$fotoDitolak = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Foto Eksternal' && ($row['foto'] ?? '') === '') $fotoDitolak = true;
t('save_row foto URL eksternal ditolak', $idxFoto >= 0 && $fotoDitolak, json_encode($r['rows']));
api('delete', 'layanan_umkm', ['index' => $idxFoto]);

$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'UMKM & Produk Lokal', 'nama' => 'Tes Foto Aneh', 'foto' => 'javascript:alert(1)',
]);
$idxFoto = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Foto Aneh') $idxFoto = (int)$row['index'];
$fotoDibuang = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Foto Aneh' && ($row['foto'] ?? '') === '') $fotoDibuang = true;
t('save_row foto path aneh dibuang', $idxFoto >= 0 && $fotoDibuang, json_encode($r['rows']));
api('delete', 'layanan_umkm', ['index' => $idxFoto]);

/* 4. maps */
$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'Pariwisata', 'nama' => 'Tes Maps Valid', 'maps' => 'https://maps.google.com/?q=-5.4,104.9',
]);
$idxMaps = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Maps Valid') $idxMaps = (int)$row['index'];
$mapsOk = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Maps Valid' && ($row['maps'] ?? '') === 'https://maps.google.com/?q=-5.4,104.9') $mapsOk = true;
t('save_row maps URL valid diterima', $idxMaps >= 0 && $mapsOk, json_encode($r['rows']));
api('delete', 'layanan_umkm', ['index' => $idxMaps]);

$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'Pariwisata', 'nama' => 'Tes Maps Aneh', 'maps' => 'javascript:alert(1)',
]);
$idxMaps = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Maps Aneh') $idxMaps = (int)$row['index'];
$mapsDibuang = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Maps Aneh' && ($row['maps'] ?? '') === '') $mapsDibuang = true;
t('save_row maps URL aneh dibuang', $idxMaps >= 0 && $mapsDibuang, json_encode($r['rows']));
api('delete', 'layanan_umkm', ['index' => $idxMaps]);

$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'Pariwisata', 'nama' => 'Tes Maps Q Format', 'maps' => '?q=-5.4,104.9',
]);
$idxMaps = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Maps Q Format') $idxMaps = (int)$row['index'];
$mapsQOk = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Maps Q Format' && ($row['maps'] ?? '') === 'https://maps.google.com/?q=-5.4,104.9') $mapsQOk = true;
t('save_row maps format ?q= diubah ke https://maps.google.com/?q=', $idxMaps >= 0 && $mapsQOk, json_encode($r['rows']));
api('delete', 'layanan_umkm', ['index' => $idxMaps]);

/* 5. wa */
$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'UMKM & Produk Lokal', 'nama' => 'Tes WA', 'wa' => '(0812) 3456-7890 ext. 2',
]);
$idxWa = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes WA') $idxWa = (int)$row['index'];
$waOk = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes WA' && ($row['wa'] ?? '') === '0812345678902') $waOk = true;
t('save_row wa jadi digit saja', $idxWa >= 0 && $waOk, json_encode($r['rows']));
api('delete', 'layanan_umkm', ['index' => $idxWa]);

/* 6. list */
$r = api('list', 'layanan_umkm', [], 1, 100);
t('list layanan & umkm', ok($r) && isset($r['rows'], $r['total']) && is_array($r['rows']), json_encode($r));
$hasIndex = true;
$hasFilterOptions = false;
foreach ($r['rows'] as $row) if (!array_key_exists('index', $row)) $hasIndex = false;
if (isset($r['filterOptions']['kategori']) && is_array($r['filterOptions']['kategori'])) $hasFilterOptions = true;
t('list layanan & umkm: tiap row punya key index', ok($r) && $hasIndex, json_encode($r['rows']));
t('list layanan & umkm: filterOptions kategori ada', ok($r) && $hasFilterOptions, json_encode($r['filterOptions'] ?? []));

/* 7. filter kategori */
$r = api('list', 'layanan_umkm', [], 1, 100, '', ['kategori' => 'Pariwisata']);
$semuaPariwisata = true;
foreach ($r['rows'] as $row) if (($row['kategori'] ?? '') !== 'Pariwisata') $semuaPariwisata = false;
t('filter kategori pariwisata', ok($r) && $semuaPariwisata && $r['total'] > 0, json_encode($r['rows']));

/* 8. search */
$r = api('list', 'layanan_umkm', [], 1, 100, 'kopi');
$cocok = true;
foreach ($r['rows'] as $row) {
    $flat = function ($v) use (&$flat) {
        $parts = [];
        foreach ((array)$v as $x) {
            if (is_array($x)) $parts[] = $flat($x);
            elseif ($x !== null && $x !== '') $parts[] = (string)$x;
        }
        return implode(' ', $parts);
    };
    if (strpos(strtolower($flat($row)), 'kopi') === false) $cocok = false;
}
t('search "kopi"', ok($r) && $cocok && $r['total'] > 0, json_encode($r['rows']));

/* 9. save_row tambah */
$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'UMKM & Produk Lokal', 'badge' => 'Tes Badge', 'nama' => 'Tes Item Layanan & UMKM',
    'subjudul' => 'Tes Sub', 'maps' => 'https://maps.google.com/?q=tes', 'wa' => '081234567890',
    'baris0_ikon' => 'storefront', 'baris0_teks' => 'Baris Satu',
    'baris1_ikon' => 'location_on', 'baris1_teks' => 'Baris Dua',
]);
t('save_row layanan & umkm tambah', ok($r), json_encode($r));
$dIdx = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Item Layanan & UMKM') $dIdx = (int)$row['index'];
t('item baru tampil di list', ok($r) && $dIdx >= 0, json_encode($r['rows']));

/* 10. save_row edit */
$r = api('save_row', 'layanan_umkm', [
    'index' => $dIdx, 'kategori' => 'Pariwisata', 'badge' => 'Tes Badge 2', 'nama' => 'Tes Item Layanan & UMKM 2',
    'subjudul' => 'Tes Sub 2', 'maps' => 'https://maps.google.com/?q=edit', 'wa' => '6281234567890',
    'baris0_ikon' => 'map', 'baris0_teks' => 'Baris Baru',
]);
t('save_row layanan & umkm edit', ok($r), json_encode($r));
$dIdx = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Item Layanan & UMKM 2') $dIdx = (int)$row['index'];
t('item ter-edit (nama baru tampil)', ok($r) && $dIdx >= 0, json_encode($r['rows']));

/* 11. validasi nama/kategori kosong */
$r = api('save_row', 'layanan_umkm', ['kategori' => 'Pariwisata', 'nama' => '   ']);
t('save_row layanan & umkm nama kosong -> fail', err($r), json_encode($r));
$r = api('save_row', 'layanan_umkm', ['kategori' => '', 'nama' => 'Tes Nama']);
t('save_row layanan & umkm kategori kosong -> fail', err($r), json_encode($r));

/* 12. delete item */
$r = api('delete', 'layanan_umkm', ['index' => $dIdx]);
t('delete layanan & umkm (index valid)', ok($r), json_encode($r));
$r = api('list', 'layanan_umkm', [], 1, 100);
$masihAda = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Item Layanan & UMKM 2') $masihAda = true;
t('item terhapus dari list', ok($r) && !$masihAda, json_encode($r['rows']));

/* 13. delete index tak ada */
$r = api('delete', 'layanan_umkm', ['index' => 99999]);
t('delete layanan & umkm index tak ada -> fail', err($r), json_encode($r));

// ============================================================================
// BAGIAN 2 — KEAMANAN & VALIDASI
// ============================================================================

/* 14. XSS: tag HTML di-strip dari semua teks */
$r = api('save_row', 'layanan_umkm', [
    'kategori'    => 'UMKM <script>alert(1)</script>',
    'badge'       => '<b onclick="x()">Badge</b>',
    'nama'        => 'Item XSS Aman',
    'subjudul'    => '<img src=x onerror=alert(3)>Sub',
    'baris0_ikon' => 'storefront',
    'baris0_teks' => '<a href="javascript:alert(4)">Teks</a>',
]);
t('XSS: save_row payload jahat -> ok', ok($r), json_encode($r));
$r = api('list', 'layanan_umkm', [], 1, 100);
$xss = null;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Item XSS Aman') $xss = $row;
$clean = true;
if ($xss !== null) {
    $flat = [$xss['kategori'] ?? '', $xss['badge'] ?? '', $xss['nama'] ?? '', $xss['subjudul'] ?? ''];
    foreach (($xss['baris'] ?? []) as $b) $flat[] = ($b['teks'] ?? '');
    foreach ($flat as $v) {
        if (strpos($v, '<') !== false || strpos($v, '>') !== false || stripos($v, 'javascript:') !== false) $clean = false;
    }
}
t('XSS: semua field teks bersih (tanpa tag/js)', $xss !== null && $clean, json_encode($xss));
api('delete', 'layanan_umkm', ['index' => $xss['index'] ?? -1]);

/* 15. Persistensi: file data bebas skrip */
$raw = (string)file_get_contents(TEST_BASE . 'includes/layanan_umkm.php');
t('persistensi: file data tanpa <script>', strpos($raw, '<script') === false, '');
t('persistensi: file data tanpa javascript:', stripos($raw, 'javascript:') === false, '');
t('persistensi: file data tanpa onerror', stripos($raw, 'onerror') === false, '');

/* 16. Batas panjang */
$r = api('save_row', 'layanan_umkm', [
    'kategori'    => str_repeat('K', 150),
    'badge'       => str_repeat('B', 120),
    'nama'        => str_repeat('N', 250),
    'subjudul'    => str_repeat('S', 250),
    'maps'        => 'https://maps.example.com/?q=' . str_repeat('a', 1100),
    'wa'          => str_repeat('1', 40),
    'baris0_ikon' => str_repeat('i', 80),
    'baris0_teks' => str_repeat('t', 300),
]);
t('batas: save_row field panjang -> ok', ok($r), json_encode($r));
$r = api('list', 'layanan_umkm', [], 1, 100);
$tr = null;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === str_repeat('N', 200)) $tr = $row;
t('batas: nama max 200', $tr !== null && ($tr['nama'] ?? '') === str_repeat('N', 200), json_encode($tr['nama'] ?? ''));
t('batas: kategori max 100', $tr !== null && mb_strlen($tr['kategori'] ?? '') === 100, json_encode($tr['kategori'] ?? ''));
t('batas: badge max 100', $tr !== null && mb_strlen($tr['badge'] ?? '') === 100, json_encode($tr['badge'] ?? ''));
t('batas: subjudul max 200', $tr !== null && mb_strlen($tr['subjudul'] ?? '') === 200, json_encode($tr['subjudul'] ?? ''));
t('batas: wa max 30 digit', $tr !== null && mb_strlen($tr['wa'] ?? '') === 30, json_encode($tr['wa'] ?? ''));
t('batas: maps max 1000 (masih http valid)', $tr !== null && mb_strlen($tr['maps'] ?? '') === 1000 && strpos($tr['maps'] ?? '', 'https://') === 0, json_encode(mb_strlen($tr['maps'] ?? '')));
$b0 = $tr['baris'][0] ?? [];
t('batas: ikon baris max 50', ($b0['ikon'] ?? '') === str_repeat('i', 50), json_encode($b0['ikon'] ?? ''));
t('batas: teks baris max 255', ($b0['teks'] ?? '') === str_repeat('t', 255), json_encode(mb_strlen($b0['teks'] ?? '')));
api('delete', 'layanan_umkm', ['index' => $tr['index'] ?? -1]);

/* 17. Foto: whitelist assets/uploads/ — 7 kasus */
$fotoCases = [
    ['assets/uploads/ok.webp', 'assets/uploads/ok.webp'],
    ['javascript:alert(1)', ''],
    ['data:image/png;base64,AAAA', ''],
    ['https://evil.com/x.jpg', ''],
    ['//evil.com/x.jpg', ''],
    ['../assets/uploads/x.jpg', ''],
    ['assets/evil/x.webp', ''],
];
foreach ($fotoCases as $i => $c) {
    api('save_row', 'layanan_umkm', ['kategori' => 'UMKM & Produk Lokal', 'nama' => 'Foto Case ' . $i, 'foto' => $c[0]]);
    $r = api('list', 'layanan_umkm', [], 1, 100);
    $fotoIdx = -1; $fotoGot = null;
    foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Foto Case ' . $i) { $fotoIdx = (int)$row['index']; $fotoGot = $row['foto'] ?? null; }
    t('foto: "' . ($c[0] === '' ? '(kosong)' : $c[0]) . '" => "' . ($c[1] === '' ? '(dibuang)' : $c[1]) . '"', $fotoGot === $c[1], json_encode($fotoGot));
    if ($fotoIdx >= 0) api('delete', 'layanan_umkm', ['index' => $fotoIdx]);
}

/* 18. Maps: hanya http(s) valid — 7 kasus */
$mapsCases = [
    ['https://maps.google.com/?q=-5.4,104.9', 'https://maps.google.com/?q=-5.4,104.9'],
    ['javascript:alert(1)', ''],
    ['data:text/html,<script>x</script>', ''],
    ['ftp://evil.com/x', ''],
    ['//evil.com/x', ''],
    ['http://', ''],
    ['https://', ''],
];
foreach ($mapsCases as $i => $c) {
    api('save_row', 'layanan_umkm', ['kategori' => 'Pariwisata', 'nama' => 'Maps Case ' . $i, 'maps' => $c[0]]);
    $r = api('list', 'layanan_umkm', [], 1, 100);
    $mapsIdx = -1; $mapsGot = null;
    foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Maps Case ' . $i) { $mapsIdx = (int)$row['index']; $mapsGot = $row['maps'] ?? null; }
    t('maps: "' . ($c[0] === '' ? '(kosong)' : $c[0]) . '" => "' . ($c[1] === '' ? '(dibuang)' : $c[1]) . '"', $mapsGot === $c[1], json_encode($mapsGot));
    if ($mapsIdx >= 0) api('delete', 'layanan_umkm', ['index' => $mapsIdx]);
}

/* 19. WA: digit saja, max 30 — 4 kasus */
$waCases = [
    ['(0812) 3456-7890 ext. 2', '0812345678902'],
    ['+62 812-3456-7890', '6281234567890'],
    ['abc-xyz', ''],
    [str_repeat('9', 40), str_repeat('9', 30)],
];
foreach ($waCases as $i => $c) {
    api('save_row', 'layanan_umkm', ['kategori' => 'UMKM & Produk Lokal', 'nama' => 'WA Case ' . $i, 'wa' => $c[0]]);
    $r = api('list', 'layanan_umkm', [], 1, 100);
    $waIdx = -1; $waGot = null;
    foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'WA Case ' . $i) { $waIdx = (int)$row['index']; $waGot = $row['wa'] ?? null; }
    t('wa: "' . ($c[0] === '' ? '(kosong)' : substr($c[0], 0, 20)) . '" => "' . ($c[1] === '' ? '(kosong)' : substr($c[1], 0, 20)) . '"', $waGot === $c[1], json_encode($waGot));
    if ($waIdx >= 0) api('delete', 'layanan_umkm', ['index' => $waIdx]);
}

/* 20. Validasi wajib & baris kosong dibuang */
$r = api('save_row', 'layanan_umkm', ['kategori' => 'Pariwisata', 'nama' => '   ']);
t('validasi: nama hanya spasi -> fail', err($r), json_encode($r));
$r = api('save_row', 'layanan_umkm', ['kategori' => '', 'nama' => 'Tes Nama']);
t('validasi: kategori kosong -> fail', err($r), json_encode($r));
$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'Agrikultur', 'nama' => 'Item Baris Kosong',
    'baris0_ikon' => 'agriculture', 'baris0_teks' => '',
    'baris1_ikon' => 'location_on', 'baris1_teks' => '   ',
]);
t('validasi: save_row baris tanpa teks -> ok', ok($r), json_encode($r));
$r = api('list', 'layanan_umkm', [], 1, 100);
$bk = null;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Item Baris Kosong') $bk = $row;
t('validasi: baris tanpa teks dibuang (baris kosong)', $bk !== null && ($bk['baris'] ?? 'x') === [], json_encode($bk['baris'] ?? null));
if ($bk !== null) api('delete', 'layanan_umkm', ['index' => $bk['index']]);

/* 21. save module (hero) DITOLAK */
$r = api('save', 'layanan_umkm', ['judul' => 'Hack', 'subjudul' => 'X', 'deskripsi' => 'Y']);
t('save module layanan_umkm -> ditolak', err($r), json_encode($r));

/* 22. norm_layanan_umkm: item invalid dibuang + sanitasi + input non-array */
$norm = norm_layanan_umkm(['daftar' => [
    null,
    'string',
    123,
    ['nama' => '', 'kategori' => 'X'],
    ['nama' => 'Y', 'kategori' => ''],
    ['nama' => 'Valid', 'kategori' => 'K', 'baris' => ['teks' => 'bukan array baris'], 'foto' => 'javascript:alert(1)', 'maps' => 'javascript:x', 'wa' => 'abc'],
]]);
$validOnly = count($norm['daftar'] ?? []) === 1 && ($norm['daftar'][0]['nama'] ?? '') === 'Valid';
$vItem = $norm['daftar'][0] ?? [];
t('norm: item non-array & wajib kosong dibuang', $validOnly, json_encode($norm));
t('norm: foto/maps/wa jahat disterilkan', ($vItem['foto'] ?? 'x') === '' && ($vItem['maps'] ?? 'x') === '' && ($vItem['wa'] ?? 'x') === '', json_encode($vItem));
t('norm: baris invalid dibuang', ($vItem['baris'] ?? 'x') === [], json_encode($vItem['baris'] ?? null));
$norm2 = norm_layanan_umkm('bukan array');
t('norm: input non-array -> daftar kosong', ($norm2['daftar'] ?? 'x') === [], json_encode($norm2));

api_finish();