<?php

define("NAMA_WEB", "Profil Pekon Gunung Megang");
define("NAMA_LENGKAP", "Rafif Rhamdo Buay Bulan");
define("IG", "ubirayap");
define("NO_WA", "085162642703");
define("MATKUL", "Pemrograman Lanjut");
define("URL_IG", "https://www.instagram.com/ubirayap");
define("URL_WA", "https://api.whatsapp.com/send/?phone=6285173200421");
define("NAMA_KAMPUS", "IIB Darmajaya");
define("MAPS_KAMPUS", "https://maps.app.goo.gl/MDRbHF1mJTq81Ec67");

date_default_timezone_set('Asia/Jakarta');
$pukul = date('H:i A');

// ============================================================
// JSON DATABASE LAYER
// Menggantikan koneksi MySQL sepenuhnya.
// Semua data disimpan di db/json/<table>.json
// ============================================================

define("DB_PATH", dirname(__DIR__) . "/db/json/");

/**
 * Baca semua data dari tabel JSON.
 * @param string $table Nama tabel (tanpa .json)
 * @return array
 */
/**
 * Validasi nama tabel JSON (anti path traversal / injection nama file).
 * Hanya karakter [a-z0-9_] yang diizinkan.
 * @param string $table Nama tabel
 * @return string Nama tabel aman, atau '' jika tidak valid
 */
function db_table_name(string $table): string
{
    $table = trim($table);
    return preg_match('/^[a-z0-9_]+$/i', $table) ? strtolower($table) : '';
}

/**
 * Baca seluruh data tabel JSON.
 * @param string $table Nama tabel
 * @return array
 */
function db_read(string $table): array
{
    $table = db_table_name($table);
    if ($table === '') {
        return [];
    }
    $file = DB_PATH . $table . ".json";
    if (!file_exists($file)) {
        return [];
    }
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/**
 * Tulis seluruh data ke tabel JSON.
 * @param string $table Nama tabel
 * @param array  $data  Array data
 * @return bool
 */
function db_write(string $table, array $data): bool
{
    $table = db_table_name($table);
    if ($table === '') {
        return false;
    }
    $file = DB_PATH . $table . ".json";
    $json = json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

/**
 * Dapatkan nilai ID berikutnya berdasarkan field.
 * @param string $table    Nama tabel
 * @param string $id_field Nama field primary key
 * @return int
 */
function db_next_id(string $table, string $id_field): int
{
    $data = db_read($table);
    if (empty($data)) return 1;
    $max = max(array_column($data, $id_field));
    return (int)$max + 1;
}

/**
 * Cari satu baris berdasarkan field = value.
 * @param string $table
 * @param string $field
 * @param mixed  $value
 * @return array|null
 */
function db_find_one(string $table, string $field, $value): ?array
{
    foreach (db_read($table) as $row) {
        if (isset($row[$field]) && (string)$row[$field] === (string)$value) {
            return $row;
        }
    }
    return null;
}

/**
 * Cari banyak baris berdasarkan field = value.
 * @param string $table
 * @param string $field
 * @param mixed  $value
 * @return array
 */
function db_find_all(string $table, string $field, $value): array
{
    return array_values(array_filter(
        db_read($table),
        fn($row) => isset($row[$field]) && (string)$row[$field] === (string)$value
    ));
}

// ============================================================
// KEAMANAN (OWASP Top 10): hardening sesi, CSRF, sanitasi input,
// rate-limit login, dan helper keamanan lainnya.
// ============================================================
require_once __DIR__ . '/security.php';

if (!function_exists('formatTanggalIndonesia')) {
    function formatTanggalIndonesia(string $tanggalInggris): string
    {
        $namaHari = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];

        $namaBulan = [
            'January'   => 'Januari',
            'February'  => 'Februari',
            'March'     => 'Maret',
            'April'     => 'April',
            'May'       => 'Mei',
            'June'      => 'Juni',
            'July'      => 'Juli',
            'August'    => 'Agustus',
            'September' => 'September',
            'October'   => 'Oktober',
            'November'  => 'November',
            'December'  => 'Desember'
        ];

        $date           = new DateTime($tanggalInggris);
        $hariInggris    = $date->format('l');
        $bulanInggris   = $date->format('F');
        $hariIndonesia  = $namaHari[$hariInggris];
        $bulanIndonesia = $namaBulan[$bulanInggris];

        return $hariIndonesia . ', ' . $date->format('d') . ' ' . $bulanIndonesia . ' ' . $date->format('Y');
    }
}
