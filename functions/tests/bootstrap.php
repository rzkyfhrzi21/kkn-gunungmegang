<?php
// ============================================================================
// functions/tests/bootstrap.php
// Bootstrap bersama untuk seluruh modul test (pola ADMIN_API_TEST).
//
// Cara pakai: setiap file functions/tests/test_*.php memuat file ini di awal,
// lalu dijalankan via CLI:
//     php functions/tests/test_potensi.php
//     php functions/tests/run_all.php          (semua modul sekaligus)
//
// Yang disediakan:
//  - Guard CLI: hanya boleh dijalankan dari terminal (403 jika via browser),
//    sehingga test tidak bisa mengeksekusi/memodifikasi data lewat HTTP.
//  - ADMIN_API_TEST: melewati autentikasi & CSRF di admin/api.php;
//    panggilan API dilakukan lewat helper api() (tanpa HTTP nyata).
//  - Helper: t(), ok(), err(), api(), api_finish().
//  - Sesi admin81 disiapkan untuk aksi yang butuh sesi (profile, dll).
//  - Snapshot otomatis: 5 file includes/*.php + 4 db/json/*.json disalin ke
//    direktori temp saat start, dan DI-RESTORE OTOMATIS saat proses selesai
//    (termasuk saat fatal error) via register_shutdown_function.
//    => test aman dijalankan berulang-ulang tanpa merusak data asli.
// ============================================================================

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit("Akses hanya via CLI (php functions/tests/...).\n");
}

define('ADMIN_API_TEST', true);
define('TEST_BASE', dirname(__DIR__, 2) . '/');
chdir(TEST_BASE);

require_once TEST_BASE . 'functions/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$GLOBALS['_API_RESULT'] = null;
require_once TEST_BASE . 'admin/api.php';

// ------------------------------------------------------------
// Snapshot & auto-restore data
// ------------------------------------------------------------
$GLOBALS['TEST_SNAP_DIR'] = sys_get_temp_dir() . '/kkn_test_snap_' . getmypid();
$GLOBALS['TEST_SNAP_FILES'] = [
    'includes/pekon.php',
    'includes/demografi.php',
    'includes/potensi.php',
    'includes/layanan_umkm.php',
    'includes/perangkat.php',
    'includes/apbpekon.php',
    'db/json/user.json',
    'db/json/aspirasi.json',
    'db/json/security.json',
    'db/json/security_log.json',
];

function test_take_snapshot(): void
{
    $dir = $GLOBALS['TEST_SNAP_DIR'];
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }
    foreach ($GLOBALS['TEST_SNAP_FILES'] as $rel) {
        $src = TEST_BASE . $rel;
        if (file_exists($src)) {
            @copy($src, $dir . '/' . str_replace(['/', '\\'], '_', $rel));
        }
    }
}

function test_restore_snapshot(): void
{
    $dir = $GLOBALS['TEST_SNAP_DIR'] ?? '';
    if ($dir === '' || !is_dir($dir)) {
        return;
    }
    foreach ($GLOBALS['TEST_SNAP_FILES'] as $rel) {
        $snap = $dir . '/' . str_replace(['/', '\\'], '_', $rel);
        if (file_exists($snap)) {
            @copy($snap, TEST_BASE . $rel);
        }
    }
    // Bersihkan direktori temp
    foreach (glob($dir . '/*') ?: [] as $f) {
        @unlink($f);
    }
    @rmdir($dir);
}

test_take_snapshot();
register_shutdown_function('test_restore_snapshot');

// ------------------------------------------------------------
// Helper test
// ------------------------------------------------------------
$GLOBALS['TEST_PASS'] = 0;
$GLOBALS['TEST_FAIL'] = 0;
$GLOBALS['TEST_FAILS'] = [];

function t(string $label, $cond, string $extra = ''): void
{
    if ($cond) {
        $GLOBALS['TEST_PASS']++;
        echo "  OK  $label\n";
    } else {
        $GLOBALS['TEST_FAIL']++;
        $GLOBALS['TEST_FAILS'][] = $label . ($extra !== '' ? ' | ' . $extra : '');
        echo "  XX  $label" . ($extra !== '' ? ' | ' . $extra : '') . "\n";
    }
}

/** Panggil handler API admin secara langsung (tanpa HTTP). */
function api($action, $module, $data, $page = 1, $perPage = 10, $search = '', $filters = []): ?array
{
    $GLOBALS['_API_RESULT'] = null;
    $GLOBALS['_API_RAW'] = json_encode([
        'action' => $action, 'module' => $module, 'data' => $data,
        'page' => $page, 'perPage' => $perPage, 'search' => $search, 'filters' => $filters,
    ]);
    api_run();
    return $GLOBALS['_API_RESULT'];
}

function ok(?array $res): bool
{
    return is_array($res) && ($res['ok'] ?? false) === true;
}

function err(?array $res): bool
{
    return is_array($res) && ($res['ok'] ?? false) === false;
}

/** Siapkan sesi admin (admin81) untuk aksi yang butuh sesi. */
function test_setup_admin_session(): void
{
    $users = db_read('user');
    $_SESSION['sesi_id'] = 0;
    foreach ($users as $u) {
        if (($u['username'] ?? '') === 'admin81') {
            $_SESSION['sesi_id']       = (int)$u['id_user'];
            $_SESSION['sesi_role']     = 'admin';
            $_SESSION['sesi_username'] = $u['username'];
            $_SESSION['sesi_nama']     = $u['nama_lengkap'] ?? '';
            break;
        }
    }
}

/** Cetak ringkasan & exit code (1 jika ada yang gagal). */
function api_finish(): void
{
    $p = $GLOBALS['TEST_PASS'];
    $f = $GLOBALS['TEST_FAIL'];
    echo "\n===== HASIL: $p PASS, $f FAIL =====\n";
    if ($f > 0) {
        echo "GAGAL:\n" . implode("\n", $GLOBALS['TEST_FAILS']) . "\n";
        exit(1);
    }
    echo "SEMUA LOLOS\n";
}