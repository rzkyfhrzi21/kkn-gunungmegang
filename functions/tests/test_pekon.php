<?php
// ============================================================================
// functions/tests/test_pekon.php
// Tes modul PROFIL PEKON (admin/api.php, modul: pekon, kepala_pekon, resolve_maps).
//
// Jalankan: php functions/tests/test_pekon.php
//
// Yang dites di file ini:
//  [pekon - save]
//  1. save pekon round-trip (payload lengkap)          -> data tersimpan & terbaca
//  2. save pekon payload FORM (tanpa key kepala_pekon) -> kepala_pekon PERTAHANAN
//     (regression bug: form identitas pernah MENIMPA kepala pekon jadi kosong
//      dan @unlink menghapus file foto kepala-pekon.jpg)
//  3. sanitasi: strip tag HTML / XSS pada field teks
//  [kepala_pekon - save]
//  4. save kepala_pekon lengkap (nama+jabatan)         -> ok
//  5. save kepala_pekon kosong                         -> fail
//  6. save kepala_pekon foto URL (bukan upload)        -> fail
//  7. save kepala_pekon foto file tidak ada            -> fail
//  [resolve_maps]
//  8. link kosong                                      -> fail
//  9. link bukan http(s)                               -> fail
//  10. (opsional) link Google Maps asli -> ok          -> SKIP default (butuh internet)
//  [pekon - struktur output]
//  11. norm output memuat semua key: nama, kecamatan,
//      kabupaten, provinsi, tahun, kepala_pekon{nama,foto,jabatan},
//      kontak{telepon,maps_code,maps_link,maps_embed}
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

test_setup_admin_session();

$cur = require TEST_BASE . 'includes/pekon.php';

/* ---------- 1. save pekon round-trip ---------- */
$r = api('save', 'pekon', $cur);
t('save pekon (round-trip payload lengkap)', ok($r), json_encode($r));

/* ---------- 2. save pekon payload form (tanpa kepala_pekon) -> kepala dipertahankan ---------- */
$formPayload = [
    'nama' => $cur['nama'], 'kecamatan' => $cur['kecamatan'],
    'kabupaten' => $cur['kabupaten'], 'provinsi' => $cur['provinsi'],
    'tahun' => $cur['tahun'], 'kontak' => $cur['kontak'],
];
$r = api('save', 'pekon', $formPayload);
$afterForm = require TEST_BASE . 'includes/pekon.php';
$k1 = $cur['kepala_pekon'] ?? [];
$k2 = $afterForm['kepala_pekon'] ?? [];
t(
    'save pekon (form) tidak menghapus kepala_pekon',
    ok($r) && ($k1['nama'] ?? '') === ($k2['nama'] ?? '') && ($k1['foto'] ?? '') === ($k2['foto'] ?? ''),
    json_encode([$r, $k2])
);

/* ---------- 3. sanitasi XSS pada save pekon ---------- */
$evil = ['nama' => '<script>alert(1)</script>Pekon XSS', 'kecamatan' => $cur['kecamatan'], 'kontak' => $cur['kontak']];
$r = api('save', 'pekon', $evil);
$saved = require TEST_BASE . 'includes/pekon.php';
t('save pekon: tag HTML dibuang dari field nama', ok($r) && strpos($saved['nama'] ?? '', '<script>') === false && strpos($saved['nama'] ?? '', 'Pekon XSS') !== false, json_encode($saved['nama'] ?? ''));

/* ---------- 4. save kepala_pekon lengkap ---------- */
$r = api('save', 'kepala_pekon', $cur['kepala_pekon'] ?? ['nama' => '', 'jabatan' => '']);
t('save kepala_pekon (round-trip)', ok($r), json_encode($r));

/* ---------- 5. save kepala_pekon kosong -> fail ---------- */
$r = api('save', 'kepala_pekon', ['nama' => '', 'jabatan' => '']);
t('save kepala_pekon kosong -> fail', err($r), json_encode($r));

/* ---------- 6. save kepala_pekon foto URL -> fail ---------- */
$r = api('save', 'kepala_pekon', ['nama' => 'Orang', 'jabatan' => 'Kepala', 'foto' => 'https://example.com/x.jpg']);
t('save kepala_pekon foto URL -> fail', err($r), json_encode($r));

/* ---------- 7. save kepala_pekon foto file tak ada -> fail ---------- */
$r = api('save', 'kepala_pekon', ['nama' => 'Orang', 'jabatan' => 'Kepala', 'foto' => 'assets/uploads/tidak-ada.jpg']);
t('save kepala_pekon foto file tak ada -> fail', err($r), json_encode($r));

/* ---------- 8-9. resolve_maps validasi awal (tanpa HTTP) ---------- */
$r = api('resolve_maps', '', ['link' => '']);
t('resolve_maps link kosong -> fail', err($r), json_encode($r));
$r = api('resolve_maps', '', ['link' => 'bukan-url']);
t('resolve_maps link bukan http(s) -> fail', err($r), json_encode($r));

/* ---------- 11. struktur output norm_pekon ---------- */
$saved = require TEST_BASE . 'includes/pekon.php';
$needKeys = ['nama', 'kecamatan', 'kabupaten', 'provinsi', 'tahun', 'kepala_pekon', 'kontak'];
$kepalaKeys = ['nama', 'foto', 'jabatan'];
$kontakKeys = ['telepon', 'maps_code', 'maps_link', 'maps_embed'];
$strukturOk = true;
foreach ($needKeys as $k) if (!array_key_exists($k, $saved)) $strukturOk = false;
foreach ($kepalaKeys as $k) if (!array_key_exists($k, $saved['kepala_pekon'] ?? [])) $strukturOk = false;
foreach ($kontakKeys as $k) if (!array_key_exists($k, $saved['kontak'] ?? [])) $strukturOk = false;
t('struktur output pekon lengkap', $strukturOk, json_encode($saved));

api_finish();