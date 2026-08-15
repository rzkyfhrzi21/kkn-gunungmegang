<?php
// ============================================================================
// functions/tests/run_all.php
// Menjalankan SELURUH modul test (functions/tests/test_*.php) satu per satu
// via subproses, lalu menampilkan ringkasan agregat.
//
// Jalankan: php functions/tests/run_all.php
//
// Catatan:
//  - Setiap test_*.php dijalankan dengan `php <file>` (subproses), sehingga
//    error fatal / exit code satu file tidak menghentikan file lain.
//  - Exit code script ini: 0 = semua lolos, 1 = ada yang gagal.
// ============================================================================

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit("Akses hanya via CLI.\n");
}

$dir = __DIR__;
$files = glob($dir . '/test_*.php');
sort($files);

$php = PHP_BINARY;
$totalPass = 0;
$totalFail = 0;
$failFiles = [];

echo "===== RUN ALL TESTS =====\n\n";

foreach ($files as $f) {
    $name = basename($f);
    echo "----- $name -----\n";
    $out = [];
    $code = 0;
    exec(escapeshellarg($php) . ' ' . escapeshellarg($f) . ' 2>&1', $out, $code);
    echo implode("\n", $out) . "\n";
    // Parse baris hasil dari output test
    $pass = 0; $fail = 0;
    foreach ($out as $line) {
        if (preg_match('/^\s+OK\s+/', $line)) $pass++;
        if (preg_match('/^\s+XX\s+/', $line)) $fail++;
    }
    $totalPass += $pass;
    $totalFail += $fail;
    if ($code !== 0 || $fail > 0) {
        $failFiles[] = $name;
    }
    echo "\n";
}

echo "========================================\n";
echo "TOTAL: $totalPass PASS, $totalFail FAIL\n";
if ($failFiles) {
    echo "GAGAL DI FILE: " . implode(', ', $failFiles) . "\n";
    exit(1);
}
echo "SEMUA MODUL LOLOS\n";