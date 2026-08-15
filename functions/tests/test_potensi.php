<?php
// ============================================================================
// functions/tests/test_potensi.php
// Tes modul POTENSI & IDM (admin/api.php, modul: potensi, mata_pencaharian, komoditas).
//
// Jalankan: php functions/tests/test_potensi.php
//
// Yang dites di file ini:
//  [potensi - save]
//  1. save potensi round-trip (payload lengkap)   -> data tersimpan & terbaca
//  2. save potensi payload FORM (tanpa key mata_pencaharian & komoditas)
//     -> daftar mata_pencaharian & komoditas PERTAHANAN
//     (regression bug: form utama pernah MENIMPA daftar MP jadi kosong)
//  3. struktur key: hero_desc, komoditas, idm_status, idm_progress, idm_desc,
//     mp_desc, mata_pencaharian, sosial_judul, sosial_par1, sosial_par2
//  [mata_pencaharian - save_row & delete]
//  4. list mata_pencaharian                       -> array rows {index, nama, keterangan}
//  5. save_row tambah MP baru                     -> ok, index = baris baru
//  6. save_row edit MP (dengan index)             -> nama/keterangan berubah
//  7. save_row MP nama kosong                     -> fail
//  8. delete MP dengan index                      -> ok, hilang dari list
//  9. delete MP index tidak ada                   -> fail
//  [komoditas - save_row & delete]
//  10. list komoditas                             -> array rows {index, nama, nilai, satuan}
//  11. save_row tambah komoditas baru             -> ok
//  12. save_row edit komoditas                    -> nama berubah
//  13. delete komoditas dengan index              -> ok
//  14. list modul tak dikenal                     -> fail
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

$p = require TEST_BASE . 'includes/potensi.php';

/* ---------- 1. save potensi round-trip ---------- */
$r = api('save', 'potensi', $p);
t('save potensi (round-trip)', ok($r), json_encode($r));

/* ---------- 2. save potensi payload form (tanpa mata_pencaharian & komoditas) -> dipertahankan ---------- */
$formPayload = [
    'hero_desc'      => $p['hero_desc'] ?? '',
    'komoditas_desc' => $p['komoditas_desc'] ?? '',
    'mp_desc'        => $p['mp_desc'] ?? '',
    'idm_status'     => $p['idm_status'] ?? '',
    'idm_progress'   => $p['idm_progress'] ?? 0,
    'idm_desc'       => $p['idm_desc'] ?? '',
    'sosial_judul'   => $p['sosial_judul'] ?? '',
    'sosial_par1'    => $p['sosial_par1'] ?? '',
    'sosial_par2'    => $p['sosial_par2'] ?? '',
];
$r = api('save', 'potensi', $formPayload);
$afterP = require TEST_BASE . 'includes/potensi.php';
$c1 = count($p['mata_pencaharian'] ?? []);
$c2 = count($afterP['mata_pencaharian'] ?? []);
$k1 = count($p['komoditas'] ?? []);
$k2 = count($afterP['komoditas'] ?? []);
t('save potensi (form) tidak menghapus mata_pencaharian', ok($r) && $c1 === $c2 && $c1 > 0, json_encode([$r, $c1, $c2]));
t('save potensi (form) tidak menghapus komoditas', ok($r) && $k1 === $k2 && $k1 > 0, json_encode([$r, $k1, $k2]));

/* ---------- 3. struktur key ---------- */
$needKeys = ['hero_desc', 'komoditas_desc', 'komoditas', 'idm_status', 'idm_progress', 'idm_desc', 'mp_desc', 'mata_pencaharian', 'sosial_judul', 'sosial_par1', 'sosial_par2'];
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
$r = api('save_row', 'mata_pencaharian', ['nama' => 'Tes MP Modul', 'keterangan' => 'Uji']);
t('save_row mata_pencaharian tambah', ok($r), json_encode($r));
$mIdx = -1;
$r = api('list', 'mata_pencaharian', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes MP Modul') $mIdx = (int)$row['index'];
t('MP baru tampil di list', ok($r) && $mIdx >= 0, json_encode($r['rows']));

/* ---------- 6. save_row edit MP ---------- */
$r = api('save_row', 'mata_pencaharian', ['index' => $mIdx, 'nama' => 'Tes MP Modul 2', 'keterangan' => 'Uji Edit']);
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

/* ---------- 10. list komoditas ---------- */
$r = api('list', 'komoditas', [], 1, 100);
t('list komoditas', ok($r) && isset($r['rows'], $r['total']) && is_array($r['rows']), json_encode($r));
$hasIndex = true;
foreach ($r['rows'] as $row) if (!array_key_exists('index', $row)) $hasIndex = false;
t('list komoditas: tiap row punya key index', ok($r) && $hasIndex, json_encode($r['rows']));

/* ---------- 11. save_row tambah komoditas ---------- */
$r = api('save_row', 'komoditas', ['nama' => 'Tes Komoditas', 'deskripsi' => 'Uji', 'nilai' => 10, 'satuan' => 'Hektar', 'ikon' => 'eco']);
t('save_row komoditas tambah', ok($r), json_encode($r));
$kIdx = -1;
$r = api('list', 'komoditas', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Komoditas') $kIdx = (int)$row['index'];
t('komoditas baru tampil di list', ok($r) && $kIdx >= 0, json_encode($r['rows']));

/* ---------- 12. save_row edit komoditas ---------- */
$r = api('save_row', 'komoditas', ['index' => $kIdx, 'nama' => 'Tes Komoditas 2', 'deskripsi' => 'Uji Edit', 'nilai' => 20, 'satuan' => 'Ha', 'ikon' => 'grass']);
t('save_row komoditas edit', ok($r), json_encode($r));
$kIdx = -1;
$r = api('list', 'komoditas', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Komoditas 2') $kIdx = (int)$row['index'];
t('komoditas ter-edit (nama baru tampil)', ok($r) && $kIdx >= 0, json_encode($r['rows']));

/* ---------- 13. delete komoditas ---------- */
$r = api('delete', 'komoditas', ['index' => $kIdx]);
t('delete komoditas (index valid)', ok($r), json_encode($r));
$r = api('list', 'komoditas', [], 1, 100);
$masihAda = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Komoditas 2') $masihAda = true;
t('komoditas terhapus dari list', ok($r) && !$masihAda, json_encode($r['rows']));

/* ---------- 14. list modul tak dikenal -> fail ---------- */
$r = api('list', 'bogus_module', [], 1, 10);
t('list modul tak dikenal -> fail', err($r), json_encode($r));

api_finish();
