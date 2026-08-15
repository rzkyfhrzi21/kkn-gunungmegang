<?php
// ============================================================================
// functions/tests/test_demografi.php
// Tes modul DEMOGRAFI & WILAYAH (admin/api.php, modul: demografi).
//
// Jalankan: php functions/tests/test_demografi.php
//
// Yang dites di file ini:
//  1. save demografi round-trip (payload lengkap)      -> data tersimpan & terbaca
//  2. semua key wajib tersimpan:
//     laki_laki, perempuan, total_jiwa, jumlah_kk,
//     luas_wilayah_km2, luas_wilayah_ha, jarak_kecamatan_km,
//     waktu_kecamatan_menit, batas_wilayah{utara,timur,selatan,barat}
//     (regression: form pernah tidak mengirim batas_wilayah -> wajib tetap utuh)
//  3. angka dinormalisasi ke int / float               -> "12,5" jadi 12.5
//  4. angka negatif / bukan angka -> jadi 0            -> aman
//  5. save modul tak dikenal                           -> fail
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

$d = require TEST_BASE . 'includes/demografi.php';

/* ---------- 1. save demografi round-trip ---------- */
$r = api('save', 'demografi', $d);
t('save demografi (round-trip)', ok($r), json_encode($r));

/* ---------- 2. semua key tersimpan ---------- */
$saved = require TEST_BASE . 'includes/demografi.php';
$needKeys = [
    'laki_laki', 'perempuan', 'total_jiwa', 'jumlah_kk',
    'luas_wilayah_km2', 'luas_wilayah_ha', 'jarak_kecamatan_km',
    'waktu_kecamatan_menit', 'batas_wilayah',
];
$strukturOk = true;
foreach ($needKeys as $k) if (!array_key_exists($k, $saved)) $strukturOk = false;
$batas = $saved['batas_wilayah'] ?? [];
foreach (['utara', 'timur', 'selatan', 'barat'] as $k) if (!array_key_exists($k, $batas)) $strukturOk = false;
t('struktur demografi lengkap (termasuk batas_wilayah)', $strukturOk, json_encode($saved));

/* ---------- 3. normalisasi angka (koma desimal) ---------- */
$r = api('save', 'demografi', [
    'laki_laki' => '12', 'perempuan' => '13', 'total_jiwa' => '25', 'jumlah_kk' => '8',
    'luas_wilayah_km2' => '12,5', 'luas_wilayah_ha' => '1250',
    'jarak_kecamatan_km' => '3', 'waktu_kecamatan_menit' => '15',
    'batas_wilayah' => $batas,
]);
$saved = require TEST_BASE . 'includes/demografi.php';
t(
    'normalisasi angka: "12,5" -> 12.5 (float)',
    ok($r) && abs($saved['luas_wilayah_km2'] - 12.5) < 0.0001,
    json_encode($saved['luas_wilayah_km2'])
);
t(
    'total_jiwa tersimpan sebagai int (25)',
    ok($r) && (int)$saved['total_jiwa'] === 25,
    json_encode($saved['total_jiwa'])
);

/* ---------- 4. angka tidak valid -> 0 ---------- */
$r = api('save', 'demografi', [
    'laki_laki' => 'abc', 'perempuan' => 'xyz', 'total_jiwa' => '<b>10</b>', 'jumlah_kk' => '',
    'luas_wilayah_km2' => 'x', 'batas_wilayah' => $batas,
]);
$saved = require TEST_BASE . 'includes/demografi.php';
t(
    'angka tidak valid dinormalisasi ke 0',
    ok($r) && (int)$saved['laki_laki'] === 0 && (int)$saved['perempuan'] === 0 && abs($saved['luas_wilayah_km2']) === 0.0,
    json_encode($saved)
);

/* ---------- 5. modul tak dikenal -> fail ---------- */
$r = api('save', 'bogus_module', []);
t('save modul tak dikenal -> fail', err($r), json_encode($r));

api_finish();