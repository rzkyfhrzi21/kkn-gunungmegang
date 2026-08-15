<?php
// ============================================================================
// functions/tests/test_potensi.php
// Tes modul POTENSI & IDM (admin/api.php, modul: potensi, mata_pencaharian).
//
// Jalankan: php functions/tests/test_potensi.php
//
// Yang dites di file ini:
//  [potensi - save]
//  1. save potensi round-trip (payload lengkap)   -> data tersimpan & terbaca
//  2. save potensi payload FORM (tanpa key mata_pencaharian)
//     -> daftar mata_pencaharian PERTAHANAN
//     (regression bug: form utama pernah MENIMPA daftar MP jadi kosong)
//  3. struktur key: tumpang_sari, sawah, jagung, idm_status, mata_pencaharian
//  [mata_pencaharian - save_row & delete]
//  4. list mata_pencaharian                       -> array rows {index, nama}
//  5. save_row tambah MP baru                     -> ok, index = baris baru
//  6. save_row edit MP (dengan index)             -> nama berubah
//  7. save_row MP nama kosong                     -> fail
//  8. delete MP dengan index                      -> ok, hilang dari list
//  9. delete MP index tidak ada                   -> fail
//  10. list modul tak dikenal                     -> fail
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

$p = require TEST_BASE . 'includes/potensi.php';

/* ---------- 1. save potensi round-trip ---------- */
$r = api('save', 'potensi', $p);
t('save potensi (round-trip)', ok($r), json_encode($r));

/* ---------- 2. save potensi payload form (tanpa mata_pencaharian) -> MP dipertahankan ---------- */
$formPayload = [
    'tumpang_sari' => $p['tumpang_sari'] ?? 0,
    'sawah'        => $p['sawah'] ?? 0,
    'jagung'       => $p['jagung'] ?? 0,
    'idm_status'   => $p['idm_status'] ?? '',
];
$r = api('save', 'potensi', $formPayload);
$afterP = require TEST_BASE . 'includes/potensi.php';
$c1 = count($p['mata_pencaharian'] ?? []);
$c2 = count($afterP['mata_pencaharian'] ?? []);
t('save potensi (form) tidak menghapus mata_pencaharian', ok($r) && $c1 === $c2 && $c1 > 0, json_encode([$r, $c1, $c2]));

/* ---------- 3. struktur key ---------- */
$needKeys = ['tumpang_sari', 'sawah', 'jagung', 'idm_status', 'mata_pencaharian'];
$strukturOk = true;
foreach ($needKeys as $k) if (!array_key_exists($k, $afterP)) $strukturOk = false;
t('struktur potensi lengkap', $strukturOk, json_encode($afterP));

/* ---------- 4. list mata_pencaharian ---------- */
$r = api('list', 'mata_pencaharian', [], 1, 100);
t('list mata_pencaharian', ok($r) && isset($r['rows'], $r['total']) && is_array($r['rows']), json_encode($r));
$hasIndex = true;
foreach ($r['rows'] as $row) if (!array_key_exists('index', $row)) $hasIndex = false;
t('list mata_pencaharian: tiap row punya key index', ok($r) && $hasIndex, json_encode($r['rows']));

/* ---------- 5. save_row tambah MP ---------- */
$r = api('save_row', 'mata_pencaharian', ['nama' => 'Tes MP Modul']);
t('save_row mata_pencaharian tambah', ok($r), json_encode($r));
$mIdx = -1;
$r = api('list', 'mata_pencaharian', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes MP Modul') $mIdx = (int)$row['index'];
t('MP baru tampil di list', ok($r) && $mIdx >= 0, json_encode($r['rows']));

/* ---------- 6. save_row edit MP ---------- */
$r = api('save_row', 'mata_pencaharian', ['index' => $mIdx, 'nama' => 'Tes MP Modul 2']);
t('save_row mata_pencaharian edit', ok($r), json_encode($r));
$mIdx = -1;
$r = api('list', 'mata_pencaharian', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes MP Modul 2') $mIdx = (int)$row['index'];
t('MP ter-edit (nama baru tampil)', ok($r) && $mIdx >= 0, json_encode($r['rows']));

/* ---------- 7. save_row nama kosong -> fail ---------- */
$r = api('save_row', 'mata_pencaharian', ['nama' => '   ']);
t('save_row mata_pencaharian nama kosong -> fail', err($r), json_encode($r));

/* ---------- 8. delete MP ---------- */
$r = api('delete', 'mata_pencaharian', ['index' => $mIdx]);
t('delete mata_pencaharian (index valid)', ok($r), json_encode($r));
$r = api('list', 'mata_pencaharian', [], 1, 100);
$masihAda = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes MP Modul 2') $masihAda = true;
t('MP terhapus dari list', ok($r) && !$masihAda, json_encode($r['rows']));

/* ---------- 9. delete index tak ada -> fail ---------- */
$r = api('delete', 'mata_pencaharian', ['index' => 99999]);
t('delete mata_pencaharian index tak ada -> fail', err($r), json_encode($r));

/* ---------- 10. list modul tak dikenal -> fail ---------- */
$r = api('list', 'bogus_module', [], 1, 10);
t('list modul tak dikenal -> fail', err($r), json_encode($r));

api_finish();