<?php
// ============================================================================
// functions/tests/test_layanan_umkm_security.php
// Tes keamanan modul LAYANAN & UMKM (admin/api.php, modul: layanan_umkm).
//
// Jalankan: php functions/tests/test_layanan_umkm_security.php
//
// Fokus: memastikan input jahat TIDAK sampai tersimpan/tersaji apa adanya.
//  1. XSS: tag <script>/<style>/<img onerror>/onclick di-strip dari semua teks
//  2. Persistensi: file data (includes/layanan_umkm.php) bebas <script>/javascript:/onerror
//  3. Batas panjang: kategori<=100, badge<=100, nama<=200, subjudul<=200,
//     baris.ikon<=50, baris.teks<=255, wa<=30 digit, maps<=1000
//  4. Foto: HANYA prefix assets/uploads/ (javascript:, data:, URL eksternal,
//     path traversal, selain uploads -> dibuang)
//  5. Maps: hanya http(s) valid (javascript:, data:, ftp:, //, host kosong -> dibuang)
//  6. WA: digit saja (pemangkasan karakter non-digit, 0..9, max 30)
//  7. Validasi wajib: kategori & nama tidak boleh kosong; baris tanpa teks dibuang
//  8. save module (hero) DITOLAK
//  9. norm_layanan_umkm: item non-array/invalid dibuang
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

/* ---------- 1. XSS: tag HTML di-strip dari semua teks ---------- */
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

/* ---------- 2. Persistensi: file data bebas skrip ---------- */
$raw = (string)file_get_contents(TEST_BASE . 'includes/layanan_umkm.php');
t('persistensi: file data tanpa <script>', strpos($raw, '<script') === false, '');
t('persistensi: file data tanpa javascript:', stripos($raw, 'javascript:') === false, '');
t('persistensi: file data tanpa onerror', stripos($raw, 'onerror') === false, '');

/* ---------- 3. Batas panjang (truncation) ---------- */
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

/* ---------- 4. Foto: whitelist assets/uploads/ ---------- */
$fotoCases = [
    ['assets/uploads/ok.webp', 'assets/uploads/ok.webp'],
    ['javascript:alert(1)', ''],
    ['data:image/png;base64,AAAA', ''],
    ['https://evil.com/x.jpg', ''],
    ['//evil.com/x.jpg', ''],
    ['../assets/uploads/x.jpg', ''],
    ['assets/evil/x.webp', ''],
];
$fotoIdx = [];
foreach ($fotoCases as $i => $c) {
    api('save_row', 'layanan_umkm', ['kategori' => 'UMKM & Produk Lokal', 'nama' => 'Foto Case ' . $i, 'foto' => $c[0]]);
    $r = api('list', 'layanan_umkm', [], 1, 100);
    $fotoIdx[$i] = -1;
    $fotoGot = null;
    foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Foto Case ' . $i) { $fotoIdx[$i] = (int)$row['index']; $fotoGot = $row['foto'] ?? null; }
    t('foto: "' . ($c[0] === '' ? '(kosong)' : $c[0]) . '" => "' . ($c[1] === '' ? '(dibuang)' : $c[1]) . '"', $fotoGot === $c[1], json_encode($fotoGot));
    if ($fotoIdx[$i] >= 0) api('delete', 'layanan_umkm', ['index' => $fotoIdx[$i]]);
}

/* ---------- 5. Maps: hanya http(s) valid ---------- */
$mapsCases = [
    ['https://maps.google.com/?q=-5.4,104.9', 'https://maps.google.com/?q=-5.4,104.9'],
    ['javascript:alert(1)', ''],
    ['data:text/html,<script>x</script>', ''],
    ['ftp://evil.com/x', ''],
    ['//evil.com/x', ''],
    ['http://', ''],
    ['https://', ''],
];
$mapsIdx = [];
foreach ($mapsCases as $i => $c) {
    api('save_row', 'layanan_umkm', ['kategori' => 'Pariwisata', 'nama' => 'Maps Case ' . $i, 'maps' => $c[0]]);
    $r = api('list', 'layanan_umkm', [], 1, 100);
    $mapsIdx[$i] = -1;
    $mapsGot = null;
    foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Maps Case ' . $i) { $mapsIdx[$i] = (int)$row['index']; $mapsGot = $row['maps'] ?? null; }
    t('maps: "' . ($c[0] === '' ? '(kosong)' : $c[0]) . '" => "' . ($c[1] === '' ? '(dibuang)' : $c[1]) . '"', $mapsGot === $c[1], json_encode($mapsGot));
    if ($mapsIdx[$i] >= 0) api('delete', 'layanan_umkm', ['index' => $mapsIdx[$i]]);
}

/* ---------- 6. WA: digit saja, max 30 ---------- */
$waCases = [
    ['(0812) 3456-7890 ext. 2', '0812345678902'],
    ['+62 812-3456-7890', '6281234567890'],
    ['abc-xyz', ''],
    [str_repeat('9', 40), str_repeat('9', 30)],
];
$waIdx = [];
foreach ($waCases as $i => $c) {
    api('save_row', 'layanan_umkm', ['kategori' => 'UMKM & Produk Lokal', 'nama' => 'WA Case ' . $i, 'wa' => $c[0]]);
    $r = api('list', 'layanan_umkm', [], 1, 100);
    $waIdx[$i] = -1;
    $waGot = null;
    foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'WA Case ' . $i) { $waIdx[$i] = (int)$row['index']; $waGot = $row['wa'] ?? null; }
    t('wa: "' . ($c[0] === '' ? '(kosong)' : substr($c[0], 0, 20)) . '" => "' . ($c[1] === '' ? '(kosong)' : substr($c[1], 0, 20)) . '"', $waGot === $c[1], json_encode($waGot));
    if ($waIdx[$i] >= 0) api('delete', 'layanan_umkm', ['index' => $waIdx[$i]]);
}

/* ---------- 7. Validasi wajib & baris ---------- */
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

/* ---------- 8. save module (hero) DITOLAK ---------- */
$r = api('save', 'layanan_umkm', ['judul' => 'Hack', 'subjudul' => 'X', 'deskripsi' => 'Y']);
t('save module layanan_umkm -> ditolak', err($r), json_encode($r));

/* ---------- 9. norm_layanan_umkm: item invalid dibuang ---------- */
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