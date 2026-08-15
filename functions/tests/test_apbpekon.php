<?php
// ============================================================================
// functions/tests/test_apbpekon.php
// Tes modul APB PEKON (admin/api.php, modul: apb_tahun, pendapatan, belanja, pembiayaan).
//
// Jalankan: php functions/tests/test_apbpekon.php
//
// Yang dites di file ini:
//  [apb_tahun - list & save & delete]
//  1. list apb_tahun (urutan terbaru dulu / krsort)  -> rows {tahun, pendapatan, belanja}
//  2. save apb_tahun tambah tahun baru                -> ok
//  3. save apb_tahun duplikat                         -> fail
//  4. delete apb_tahun tahun baru                     -> ok
//  5. delete apb_tahun yg sudah hilang                -> fail
//  6. delete TAHUN TERAKHIR (satu-satunya)            -> ditolak (guard)
//  [pendapatan/belanja/pembiayaan - list & save_row]
//  7. list pendapatan filter tahun valid              -> rows {key, label, nominal}
//  8. list pendapatan filter tahun tak ada            -> fallback ke tahun terbaru
//  9. save_row pendapatan ubah nominal                -> ok, nilai tersimpan
//  10. save_row pendapatan pos (key) tak dikenal      -> fail
//  11. save_row belanja ubah & restore                -> ok
//  12. save_row pembiayaan ubah & restore             -> ok
//  13. save_row tanpa tahun                            -> fail (tahun wajib)
//  14. list modul tak dikenal                          -> fail
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

/* ---------- 1. list apb_tahun ---------- */
$r = api('list', 'apb_tahun', [], 1, 10);
t('list apb_tahun', ok($r) && count($r['rows']) >= 1, json_encode($r));
$firstYear = (int)$r['rows'][0]['tahun'];
$sortedOk = true;
$prev = PHP_INT_MAX;
foreach ($r['rows'] as $row) {
    if ((int)$row['tahun'] > $prev) $sortedOk = false;
    $prev = (int)$row['tahun'];
}
t('apb_tahun urut terbaru dulu (krsort)', ok($r) && $sortedOk, json_encode(array_column($r['rows'], 'tahun')));
$jumlahSebelum = count($r['rows']);

/* ---------- 2. save apb_tahun tambah ---------- */
$r = api('save', 'apb_tahun', ['tahun' => 2099]);
t('apb_tahun tambah 2099', ok($r), json_encode($r));

/* ---------- 3. duplikat -> fail ---------- */
$r = api('save', 'apb_tahun', ['tahun' => 2099]);
t('apb_tahun duplikat -> fail', err($r), json_encode($r));

/* ---------- 4. delete 2099 ---------- */
$r = api('delete', 'apb_tahun', ['tahun' => 2099]);
t('apb_tahun hapus 2099', ok($r), json_encode($r));

/* ---------- 5. delete yg sudah hilang -> fail ---------- */
$r = api('delete', 'apb_tahun', ['tahun' => 2099]);
t('apb_tahun hapus yg sudah hilang -> fail', err($r), json_encode($r));

/* ---------- 6. guard: hapus tahun terakhir ---------- */
$lastYear = null;
foreach ($r = api('list', 'apb_tahun', [], 1, 10)['rows'] as $row) {
    if ($lastYear === null || (int)$row['tahun'] > $lastYear) $lastYear = (int)$row['tahun'];
}
if (count(api('list', 'apb_tahun', [], 1, 10)['rows']) > 1) {
    // Tahun terbaru boleh dihapus bila masih ada tahun lain -> bukan guard
    $r = api('delete', 'apb_tahun', ['tahun' => $lastYear]);
    t('apb_tahun hapus tahun terbaru (masih ada tahun lain)', ok($r), json_encode($r));
    // Sekarang coba hapus tahun terakhir (yg tersisa) -> harus ditolak
    $r = api('list', 'apb_tahun', [], 1, 10);
    if (count($r['rows']) === 1) {
        $only = (int)$r['rows'][0]['tahun'];
        $r = api('delete', 'apb_tahun', ['tahun' => $only]);
        t(
            'apb_tahun hapus TAHUN TERAKHIR -> ditolak',
            err($r) && strpos($r['error'] ?? '', 'Tidak dapat menghapus') !== false,
            json_encode($r)
        );
    } else {
        t('apb_tahun hapus TAHUN TERAKHIR -> ditolak (SKIP, multi-tahun)', true);
    }
} else {
    // Sudah 1 tahun sejak awal -> langsung uji guard
    $r = api('delete', 'apb_tahun', ['tahun' => $lastYear]);
    t(
        'apb_tahun hapus TAHUN TERAKHIR -> ditolak',
        err($r) && strpos($r['error'] ?? '', 'Tidak dapat menghapus') !== false,
        json_encode($r)
    );
}

/* ---------- 7-8. list pendapatan (filter tahun) ---------- */
$r = api('list', 'apb_tahun', [], 1, 10);
$firstYear = (int)$r['rows'][0]['tahun']; // re-kaptur: guard di atas bisa menghapus tahun terbaru
$r = api('list', 'pendapatan', [], 1, 10, '', ['tahun' => $firstYear]);
t('list pendapatan (filter tahun valid)', ok($r) && count($r['rows']) >= 1, json_encode($r));
$hasKeyLabel = true;
foreach ($r['rows'] as $row) if (!isset($row['key'], $row['label'], $row['nominal'])) $hasKeyLabel = false;
t('rows pendapatan punya key/label/nominal', ok($r) && $hasKeyLabel, json_encode($r['rows']));

$r = api('list', 'pendapatan', [], 1, 10, '', ['tahun' => 9999]);
t('list pendapatan (tahun tak ada -> fallback tahun terbaru)', ok($r) && count($r['rows']) >= 1, json_encode($r));

/* ---------- 9-10. save_row pendapatan ---------- */
$r = api('list', 'pendapatan', [], 1, 10, '', ['tahun' => $firstYear]);
$key = $r['rows'][0]['key'];
$oldNominal = (float)$r['rows'][0]['nominal'];
$r = api('save_row', 'pendapatan', ['tahun' => $firstYear, 'key' => $key, 'nominal' => 555000]);
t('save_row pendapatan ubah nominal', ok($r) && (float)$r['saved']['nominal'] === 555000.0, json_encode($r));
$r = api('save_row', 'pendapatan', ['tahun' => $firstYear, 'key' => $key, 'nominal' => $oldNominal]);
t('save_row pendapatan restore nominal', ok($r), json_encode($r));
$r = api('save_row', 'pendapatan', ['tahun' => $firstYear, 'key' => 'pos_tidak_ada', 'nominal' => 1]);
t('save_row pos tak dikenal -> fail', err($r), json_encode($r));

/* ---------- 11. belanja ubah & restore ---------- */
$r = api('save_row', 'belanja', ['tahun' => $firstYear, 'key' => 'pembangunan_pekon', 'nominal' => 555000]);
t('save_row belanja ubah', ok($r), json_encode($r));
$r = api('save_row', 'belanja', ['tahun' => $firstYear, 'key' => 'pembangunan_pekon', 'nominal' => 0]);
t('save_row belanja restore', ok($r), json_encode($r));

/* ---------- 12. pembiayaan ubah & restore ---------- */
$r = api('save_row', 'pembiayaan', ['tahun' => $firstYear, 'key' => 'penerimaan', 'nominal' => 555000]);
t('save_row pembiayaan ubah', ok($r), json_encode($r));
$r = api('save_row', 'pembiayaan', ['tahun' => $firstYear, 'key' => 'penerimaan', 'nominal' => 0]);
t('save_row pembiayaan restore', ok($r), json_encode($r));

/* ---------- 13. save_row tanpa tahun -> fail ---------- */
$r = api('save_row', 'pendapatan', ['key' => $key, 'nominal' => 1]);
t('save_row tanpa tahun -> fail', err($r), json_encode($r));

/* ---------- 14. list modul tak dikenal ---------- */
$r = api('list', 'bogus_module', [], 1, 10);
t('list modul tak dikenal -> fail', err($r), json_encode($r));

api_finish();