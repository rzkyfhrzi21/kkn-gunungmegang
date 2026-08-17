<?php
// ============================================================================
// functions/tests/test_layanan_umkm.php
// Tes modul LAYANAN & UMKM (admin/api.php, modul: layanan_umkm).
//
// Jalankan: php functions/tests/test_layanan_umkm.php
//
// Yang dites di file ini:
//  [layanan & umkm - struktur & save]
//  1. struktur item: kategori, badge, nama, subjudul, foto, baris, maps, wa
//  2. save module layanan & umkm DITOLAK (teks hero dikode di komponen, bukan admin)
//  3. save_row foto HANYA upload lokal: assets/uploads/ diterima, URL eksternal & path aneh dibuang
//  4. save_row maps URL valid diterima, URL aneh dibuang
//  5. save_row wa dibersihkan menjadi digit saja
//  [layanan & umkm - list]
//  6. list layanan & umkm                               -> array rows {index, ...}
//  7. filter kategori                              -> hanya kategori yang diminta
//  8. search                                       -> hanya row yang cocok
//  [layanan & umkm - save_row & delete]
//  9. save_row tambah item baru                    -> ok, index = baris baru
//  10. save_row edit item (dengan index)            -> nama berubah
//  11. save_row kategori/nama kosong               -> fail
//  12. delete item dengan index                    -> ok, hilang dari list
//  13. delete index tidak ada                      -> fail
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

$p = require TEST_BASE . 'includes/layanan_umkm.php';

/* ---------- 1. struktur key ---------- */
$strukturOk = true;
if (!array_key_exists('daftar', $p)) $strukturOk = false;
$needKeys = ['kategori', 'badge', 'nama', 'subjudul', 'foto', 'baris', 'maps', 'wa'];
$sawItem = false;
foreach (($p['daftar'] ?? []) as $item) {
    $sawItem = true;
    foreach ($needKeys as $k) if (!array_key_exists($k, $item)) $strukturOk = false;
}
t('struktur layanan & umkm lengkap (daftar + key item)', $strukturOk && $sawItem, json_encode($p));

/* ---------- 2. save module DITOLAK ---------- */
$r = api('save', 'layanan_umkm', ['subjudul' => 'X', 'judul' => 'Y', 'deskripsi' => 'Z']);
t('save layanan & umkm (module) ditolak - teks dikode di komponen', err($r), json_encode($r));

/* ---------- 3. foto: HANYA upload lokal (assets/uploads/...), URL eksternal & path aneh dibuang ---------- */
$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'UMKM & Produk Lokal', 'nama' => 'Tes Foto Lokal', 'foto' => 'assets/uploads/umkm.webp',
]);
$idxFoto = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Foto Lokal') $idxFoto = (int)$row['index'];
$r = api('list', 'layanan_umkm', [], 1, 100);
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
$r = api('list', 'layanan_umkm', [], 1, 100);
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
$r = api('list', 'layanan_umkm', [], 1, 100);
$fotoDibuang = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Foto Aneh' && ($row['foto'] ?? '') === '') $fotoDibuang = true;
t('save_row foto path aneh dibuang', $idxFoto >= 0 && $fotoDibuang, json_encode($r['rows']));
api('delete', 'layanan_umkm', ['index' => $idxFoto]);

/* ---------- 4. maps: URL valid diterima, aneh dibuang ---------- */
$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'Pariwisata', 'nama' => 'Tes Maps Valid', 'maps' => 'https://maps.google.com/?q=-5.4,104.9',
]);
$idxMaps = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Maps Valid') $idxMaps = (int)$row['index'];
$r = api('list', 'layanan_umkm', [], 1, 100);
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
$r = api('list', 'layanan_umkm', [], 1, 100);
$mapsDibuang = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Maps Aneh' && ($row['maps'] ?? '') === '') $mapsDibuang = true;
t('save_row maps URL aneh dibuang', $idxMaps >= 0 && $mapsDibuang, json_encode($r['rows']));
api('delete', 'layanan_umkm', ['index' => $idxMaps]);

/* ---------- 5. wa: dibersihkan jadi digit saja ---------- */
$r = api('save_row', 'layanan_umkm', [
    'kategori' => 'UMKM & Produk Lokal', 'nama' => 'Tes WA', 'wa' => '(0812) 3456-7890 ext. 2',
]);
$idxWa = -1;
$r = api('list', 'layanan_umkm', [], 1, 100);
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes WA') $idxWa = (int)$row['index'];
$r = api('list', 'layanan_umkm', [], 1, 100);
$waOk = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes WA' && ($row['wa'] ?? '') === '0812345678902') $waOk = true;
t('save_row wa jadi digit saja', $idxWa >= 0 && $waOk, json_encode($r['rows']));
api('delete', 'layanan_umkm', ['index' => $idxWa]);

/* ---------- 6. list layanan & umkm ---------- */
$r = api('list', 'layanan_umkm', [], 1, 100);
t('list layanan & umkm', ok($r) && isset($r['rows'], $r['total']) && is_array($r['rows']), json_encode($r));
$hasIndex = true;
$hasFilterOptions = false;
foreach ($r['rows'] as $row) if (!array_key_exists('index', $row)) $hasIndex = false;
if (isset($r['filterOptions']['kategori']) && is_array($r['filterOptions']['kategori'])) $hasFilterOptions = true;
t('list layanan & umkm: tiap row punya key index', ok($r) && $hasIndex, json_encode($r['rows']));
t('list layanan & umkm: filterOptions kategori ada', ok($r) && $hasFilterOptions, json_encode($r['filterOptions'] ?? []));

/* ---------- 7. filter kategori ---------- */
$r = api('list', 'layanan_umkm', [], 1, 100, '', ['kategori' => 'Pariwisata']);
$semuaPariwisata = true;
foreach ($r['rows'] as $row) if (($row['kategori'] ?? '') !== 'Pariwisata') $semuaPariwisata = false;
t('filter kategori pariwisata', ok($r) && $semuaPariwisata && $r['total'] > 0, json_encode($r['rows']));

/* ---------- 8. search ---------- */
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

/* ---------- 9. save_row tambah item ---------- */
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

/* ---------- 10. save_row edit item ---------- */
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

/* ---------- 11. save_row kategori/nama kosong -> fail ---------- */
$r = api('save_row', 'layanan_umkm', ['kategori' => 'Pariwisata', 'nama' => '   ']);
t('save_row layanan & umkm nama kosong -> fail', err($r), json_encode($r));
$r = api('save_row', 'layanan_umkm', ['kategori' => '', 'nama' => 'Tes Nama']);
t('save_row layanan & umkm kategori kosong -> fail', err($r), json_encode($r));

/* ---------- 12. delete item ---------- */
$r = api('delete', 'layanan_umkm', ['index' => $dIdx]);
t('delete layanan & umkm (index valid)', ok($r), json_encode($r));
$r = api('list', 'layanan_umkm', [], 1, 100);
$masihAda = false;
foreach ($r['rows'] as $row) if (($row['nama'] ?? '') === 'Tes Item Layanan & UMKM 2') $masihAda = true;
t('item terhapus dari list', ok($r) && !$masihAda, json_encode($r['rows']));

/* ---------- 13. delete index tak ada -> fail ---------- */
$r = api('delete', 'layanan_umkm', ['index' => 99999]);
t('delete layanan & umkm index tak ada -> fail', err($r), json_encode($r));

api_finish();