<?php
// includes/data.php
// Single entry point — mengimpor semua modul dan merakitnya ke satu array $pekon
// Gunakan file ini bila butuh seluruh data sekaligus.
// Bila hanya butuh satu modul, require langsung file-nya (lebih efisien).

$pekon = array_merge(
    require __DIR__ . '/pekon.php',
    [
        'demografi'    => require __DIR__ . '/demografi.php',
        'potensi'      => require __DIR__ . '/potensi.php',
        'apbpekon_2026'=> require __DIR__ . '/apbpekon.php',
        'perangkat'    => require __DIR__ . '/perangkat.php',
    ]
);
