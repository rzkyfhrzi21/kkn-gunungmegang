<?php
// ============================================================================
// functions/tests/test_perangkat.php
// Tes modul APARAT & LEMBAGA (admin/api.php, modul: perangkat, kepala_pekon).
//
// Jalankan: php functions/tests/test_perangkat.php
//
// Yang dites di file ini:
//  [perangkat - list]
//  1. list perangkat                      -> array rows {idx, jabatan, nama, foto}
//  2. tiap row punya key idx (dipakai edit/hapus dari frontend)
//  3. filter jabatan                      -> hanya baris dgn jabatan tsb
//  4. filter jenis (Perangkat/Lembaga)    -> BHP/LPM = Lembaga, sisanya Perangkat
//  5. search nama                         -> hanya baris yg cocok
//  [perangkat - save_row]
//  6. save_row tambah aparat              -> ok, idx = baris baru
//  7. save_row edit aparat (dengan index) -> nama/jabatan berubah
//  8. save_row nama kosong                -> fail
//  [perangkat - delete]
//  9. delete index valid                  -> ok, hilang dari list
//  10. delete index tak ada               -> fail
//  [kepala_pekon - save]
//  11. save kepala_pekon valid            -> ok & tersimpan di includes/pekon.php
//  12. save kepala_pekon kosong           -> fail
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

/* ---------- 1-2. list perangkat + key idx ---------- */
$r = api('list', 'perangkat', [], 1, 100);
t('list perangkat', ok($r) && isset($r['rows'], $r['total']) && is_array($r['rows']), json_encode($r));
$hasIdx = true;
foreach ($r['rows'] as $row) if (!array_key_exists('idx', $row)) $hasIdx = false;
t('list perangkat: tiap row punya key idx', ok($r) && $hasIdx, json_encode($r['rows']));

/* ---------- 3. filter jabatan ---------- */
if (($r['rows'][0]['jabatan'] ?? '') !== '') {
    $jab = $r['rows'][0]['jabatan'];
    $r2 = api('list', 'perangkat', [], 1, 100, '', ['jabatan' => $jab]);
    $allMatch = true;
    foreach ($r2['rows'] as $row) if (($row['jabatan'] ?? '') !== $jab) $allMatch = false;
    t('filter jabatan: semua baris sesuai', ok($r2) && $allMatch && $r2['total'] > 0, json_encode($r2));
} else {
    t('filter jabatan: semua baris sesuai (SKIP, data kosong)', true);
}

/* ---------- 4. filter jenis ---------- */
$r3 = api('list', 'perangkat', [], 1, 100, '', ['jenis' => 'Lembaga']);
$jenisOk = true;
foreach ($r3['rows'] as $row) {
    $j = strtoupper($row['jabatan'] ?? '');
    if (strpos($j, 'BHP') === false && strpos($j, 'LPM') === false) $jenisOk = false;
}
t('filter jenis=Lembaga: hanya BHP/LPM', ok($r3) && $jenisOk, json_encode($r3));

/* ---------- 5. search ---------- */
if (($r['rows'][0]['nama'] ?? '') !== '') {
    $namaCari = $r['rows'][0]['nama'];
    $r4 = api('list', 'perangkat', [], 1, 100, $namaCari);
    $allHit = true;
    foreach ($r4['rows'] as $row) if (stripos(implode(' ', $row), $namaCari) === false) $allHit = false;
    t('search nama: semua baris mengandung kata kunci', ok($r4) && $allHit && $r4['total'] > 0, json_encode($r4));
} else {
    t('search nama: semua baris mengandung kata kunci (SKIP, data kosong)', true);
}

/* ---------- 6. save_row tambah aparat ---------- */
$r = api('save_row', 'perangkat', ['nama' => 'Tes Aparat Modul', 'jabatan' => 'Staf Tes', 'foto' => '']);
t('save_row perangkat tambah', ok($r), json_encode($r));
$idx = -1;
$r = api('list', 'perangkat', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Aparat Modul') $idx = (int)$row['idx'];
t('aparat baru tampil di list (dengan idx)', ok($r) && $idx >= 0, json_encode($r['rows']));

/* ---------- 7. save_row edit aparat ---------- */
$r = api('save_row', 'perangkat', ['index' => $idx, 'nama' => 'Tes Aparat Modul 2', 'jabatan' => 'Staf Tes 2', 'foto' => '']);
t('save_row perangkat edit', ok($r), json_encode($r));
$idx = -1;
$r = api('list', 'perangkat', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Aparat Modul 2') $idx = (int)$row['idx'];
t('aparat ter-edit (nama baru tampil)', ok($r) && $idx >= 0, json_encode($r['rows']));

/* ---------- 8. save_row nama kosong -> fail ---------- */
$r = api('save_row', 'perangkat', ['nama' => '', 'jabatan' => 'Staf']);
t('save_row perangkat nama kosong -> fail', err($r), json_encode($r));

/* ---------- 9. delete aparat ---------- */
$r = api('delete', 'perangkat', ['index' => $idx]);
t('delete perangkat (index valid)', ok($r), json_encode($r));
$r = api('list', 'perangkat', [], 1, 100);
$masihAda = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Aparat Modul 2') $masihAda = true;
t('aparat terhapus dari list', ok($r) && !$masihAda, json_encode($r['rows']));

/* ---------- 10. delete index tak ada -> fail ---------- */
$r = api('delete', 'perangkat', ['index' => 99999]);
t('delete perangkat index tak ada -> fail', err($r), json_encode($r));

/* ---------- 11-12. kepala_pekon ---------- */
$cur = require TEST_BASE . 'includes/pekon.php';
$r = api('save', 'kepala_pekon', $cur['kepala_pekon'] ?? ['nama' => '', 'jabatan' => '']);
t('save kepala_pekon valid', ok($r), json_encode($r));
$r = api('save', 'kepala_pekon', ['nama' => '', 'jabatan' => '']);
t('save kepala_pekon kosong -> fail', err($r), json_encode($r));

api_finish();