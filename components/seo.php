<?php

/**
 * components/seo.php
 * SEO lengkap untuk seluruh halaman depan:
 * title unik, meta description/keywords/author/robots, canonical, favicon,
 * Open Graph, Twitter Card, theme-color, dan JSON-LD (WebSite + Organization).
 * Dipanggil di dalam <head> setiap halaman depan.
 */
require_once __DIR__ . '/../includes/data.php';

// Gunakan REQUEST_URI (URL publik di browser), BUKAN SCRIPT_NAME (path file fisik setelah rewrite).
// Setelah restructuring ke views/landing/, SCRIPT_NAME berisi 'views/landing/...' bukan URL publik asli.
$_reqPath = strtok($_SERVER['REQUEST_URI'] ?? '/', '?#'); // buang query string & fragment
$_reqPath = rtrim($_reqPath, '/');                         // buang trailing slash
$_slug    = basename($_reqPath);                           // 'layanan-umkm', 'index', ''
// $seoPage ditetapkan setelah $seoMap didefinisikan di bawah
$siteName = $pekon['nama'] ?? 'Pekon Gunung Megang';


$seoMap = [
    'index' => [
        'title'    => 'Profil Pekon Gunung Megang, Pulau Panggung, Tanggamus',
        'desc'     => 'Portal resmi Pekon Gunung Megang, Kec. Pulau Panggung, Kab. Tanggamus, Lampung. Profil desa, pemerintahan, potensi ekonomi, layanan & UMKM, dan APBPekon.',
        'keywords' => 'pekon gunung megang, gunung megang, tanggamus, lampung, desa, profil desa, pemerintahan desa, apb pekon, potensi desa',
    ],
    'profil-desa' => [
        'title'    => 'Profil Desa Gunung Megang - Geografis & Demografi',
        'desc'     => 'Profil lengkap Desa Gunung Megang: letak geografis, luas wilayah, batas desa, jumlah penduduk, dan data kependudukan Kec. Pulau Panggung, Tanggamus.',
        'keywords' => 'profil desa gunung megang, geografis, demografi, jumlah penduduk, batas wilayah, tanggamus',
    ],
    'pemerintahan' => [
        'title'    => 'Pemerintahan Pekon Gunung Megang - Struktur & Perangkat',
        'desc'     => 'Struktur organisasi dan tata kelola pemerintahan Pekon Gunung Megang: kepala pekon, perangkat desa, dan lembaga kemasyarakatan desa di Tanggamus.',
        'keywords' => 'pemerintahan desa gunung megang, kepala pekon, perangkat desa, struktur organisasi, bhp, lpm',
    ],
    'potensi-ekonomi' => [
        'title'    => 'Potensi & Ekonomi Desa Gunung Megang, Tanggamus Lampung',
        'desc'     => 'Potensi ekonomi dan sumber daya alam Pekon Gunung Megang: pertanian, komoditas unggulan, mata pencaharian masyarakat, dan indeks pembangunan desa.',
        'keywords' => 'potensi desa gunung megang, ekonomi desa, pertanian, komoditas, mata pencaharian, idm',
    ],
    'layanan-umkm' => [
        'title'    => 'Layanan & UMKM Pekon Gunung Megang - Produk Lokal',
        'desc'     => 'Layanan & UMKM Pekon Gunung Megang: produk unggulan UMKM, fasilitas kesehatan, destinasi wisata, agrikultur, dan fasilitas publik di Tanggamus.',
        'keywords' => 'layanan & umkm gunung megang, umkm desa, produk lokal, layanan kesehatan, pariwisata desa, fasilitas publik, tanggamus',
    ],
    'apbpekon' => [
        'title'    => 'Transparansi APB Pekon Gunung Megang - Anggaran Desa',
        'desc'     => 'Anggaran Pendapatan dan Belanja Pekon (APBPekon) Gunung Megang: rincian pendapatan, belanja, dan pembiayaan setiap tahun anggaran secara terbuka dan transparan.',
        'keywords' => 'apb pekon gunung megang, apbdes, anggaran desa, transparansi, dana desa, add',
    ],
    'apbpekon-2026' => [
        'title'    => 'Transparansi APB Pekon Gunung Megang - Anggaran Desa 2026',
        'desc'     => 'Anggaran Pendapatan dan Belanja Pekon (APBPekon) Gunung Megang: rincian pendapatan, belanja, dan pembiayaan secara terbuka dan transparan.',
        'keywords' => 'apb pekon gunung megang, apbdes 2026, anggaran desa, transparansi, dana desa, add',
    ],
    'kontak' => [
        'title'    => 'Kontak Kantor Pekon Gunung Megang, Pulau Panggung Tanggamus',
        'desc'     => 'Hubungi Kantor Pekon Gunung Megang, Kecamatan Pulau Panggung, Kabupaten Tanggamus: alamat, telepon, jam layanan, dan peta lokasi.',
        'keywords' => 'kontak pekon gunung megang, alamat kantor desa, telepon desa, peta lokasi, tanggamus',
    ],
];

// Deteksi halaman dari slug URL publik (REQUEST_URI), setelah $seoMap tersedia
$seoPage = (isset($seoMap[$_slug]) && $_slug !== '') ? $_slug : 'index';

$seo = $seoMap[$seoPage] ?? $seoMap['index'];

$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';

// basePath = prefix subdirektori.
// - Beranda: $_reqPath IS the basePath ('' di produksi root, '/kkn-gunungmegang' di lokal)
// - Halaman lain: satu level naik dari slug (dirname '/kkn-gunungmegang/layanan-umkm' = '/kkn-gunungmegang')
$_basePath = $seoPage === 'index'
    ? $_reqPath
    : rtrim(str_replace('\\', '/', dirname($_reqPath)), '/');
$baseUrl   = $scheme . '://' . $host . $_basePath;
$canonical = $baseUrl . ($seoPage === 'index' ? '/' : '/' . $seoPage);

$ogImage  = $baseUrl . '/assets/images/Lambang_Kabupaten_Tanggamus.png';
$telepon  = preg_replace('/[^0-9]/', '', $pekon['kontak']['telepon'] ?? '');
$telIntl  = $telepon !== '' ? '+62' . ltrim($telepon, '0') : '';

$crumbName = [
    'index'            => 'Beranda',
    'profil-desa'      => 'Profil Desa',
    'pemerintahan'     => 'Pemerintahan',
    'potensi-ekonomi'  => 'Potensi & Ekonomi',
    'layanan-umkm'     => 'Layanan & UMKM',
    'apbpekon'         => 'Transparansi APB Pekon',
    'apbpekon-2026'    => 'Transparansi APB Pekon',
    'kontak'           => 'Kontak & Pelayanan',
];

$jsonLd = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'      => 'WebSite',
            '@id'        => $canonical . '/#website',
            'name'       => $siteName,
            'url'        => $baseUrl . '/',
            'inLanguage' => 'id-ID',
            'publisher'  => ['@id' => $canonical . '/#organization'],
        ],
        [
            '@type'         => 'GovernmentOrganization',
            '@id'           => $canonical . '/#organization',
            'name'          => $siteName,
            'url'           => $baseUrl . '/',
            'description'   => $seo['desc'],
            'logo'          => ['@type' => 'ImageObject', 'url' => $ogImage],
            'image'         => $ogImage,
            'address'       => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => 'Kantor Pekon Gunung Megang, Kecamatan Pulau Panggung',
                'addressLocality' => 'Kabupaten Tanggamus',
                'addressRegion'   => 'Lampung',
                'addressCountry'  => 'ID',
            ],
        ],
    ],
];
if ($telIntl !== '') {
    $jsonLd['@graph'][1]['contactPoint'] = [
        '@type'         => 'ContactPoint',
        'telephone'     => $telIntl,
        'contactType'   => 'customer service',
        'areaServed'    => 'ID',
        'availableLanguage' => 'Indonesian',
    ];
}
$crumb = $crumbName[$seoPage] ?? 'Beranda';
$jsonLd['@graph'][] = [
    '@type'         => 'BreadcrumbList',
    '@id'           => $canonical . '#breadcrumb',
    'itemListElement' => $seoPage === 'index' ? [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => $baseUrl . '/'],
    ] : [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => $baseUrl . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => $crumb, 'item' => $canonical],
    ],
];
?>
<title><?= htmlspecialchars($seo['title']) ?></title>
<meta name="description" content="<?= htmlspecialchars($seo['desc']) ?>">
<meta name="keywords" content="<?= htmlspecialchars($seo['keywords']) ?>">
<meta name="author" content="<?= htmlspecialchars($siteName) ?>">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="theme-color" content="#004532">
<link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
<link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
<link rel="apple-touch-icon" sizes="180x180" href="assets/images/Lambang_Kabupaten_Tanggamus.png">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= htmlspecialchars($siteName) ?>">
<meta property="og:title" content="<?= htmlspecialchars($seo['title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($seo['desc']) ?>">
<meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
<meta property="og:locale" content="id_ID">
<meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
<meta property="og:image:alt" content="Lambang Kabupaten Tanggamus - <?= htmlspecialchars($siteName) ?>">
<meta property="og:image:width" content="512">
<meta property="og:image:height" content="512">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="<?= htmlspecialchars($canonical) ?>">
<meta name="twitter:title" content="<?= htmlspecialchars($seo['title']) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($seo['desc']) ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">
<script type="application/ld+json">
    <?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<style>
    html,
    body,
    button,
    input,
    textarea,
    select {
        font-family: "Inter", sans-serif;
        font-optical-sizing: auto;
    }

    .material-symbols-outlined,
    .material-symbols-rounded,
    .material-symbols-sharp,
    .material-icons,
    [class*="material-symbols"] {
        font-family: 'Material Symbols Outlined' !important;
        font-weight: normal;
        font-style: normal;
        letter-spacing: normal;
        text-transform: none;
        display: inline-block;
        white-space: nowrap;
        word-wrap: normal;
        direction: ltr;
        -webkit-font-feature-settings: 'liga';
        -webkit-font-smoothing: antialiased;
    }

    @layer base {

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: "Inter", sans-serif;
            font-optical-sizing: auto;
        }

        body {
            overscroll-behavior: none;
        }

        main> :first-child {
            margin-top: 0 !important;
        }

        main> :last-child {
            margin-bottom: 0 !important;
        }
    }

    ::-webkit-scrollbar {
        display: none;
    }
</style>