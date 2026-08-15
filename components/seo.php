<?php
/**
 * components/seo.php
 * SEO lengkap untuk seluruh halaman depan:
 * title unik, meta description/keywords/author/robots, canonical, favicon,
 * Open Graph, Twitter Card, theme-color, dan JSON-LD (WebSite + Organization).
 * Dipanggil di dalam <head> setiap halaman depan.
 */
require_once __DIR__ . '/../includes/data.php';

$seoPage = basename($_SERVER['SCRIPT_NAME'] ?? 'index', '.php');
$siteName = $pekon['nama'] ?? 'Pekon Gunung Megang';

$seoMap = [
    'index' => [
        'title'    => 'Beranda | Profil Pekon Gunung Megang',
        'desc'     => 'Portal resmi Pekon Gunung Megang, Kecamatan Pulau Panggung, Kabupaten Tanggamus, Lampung. Informasi profil desa, pemerintahan, potensi ekonomi, dan transparansi APBPekon.',
        'keywords' => 'pekon gunung megang, gunung megang, tanggamus, lampung, desa, profil desa, pemerintahan desa, apb pekon, potensi desa',
    ],
    'profil-desa' => [
        'title'    => 'Profil Desa | Pekon Gunung Megang',
        'desc'     => 'Profil lengkap Pekon Gunung Megang: letak geografis, luas wilayah, batas desa, jumlah penduduk, dan data kependudukan Kecamatan Pulau Panggung, Kabupaten Tanggamus.',
        'keywords' => 'profil desa gunung megang, geografis, demografi, jumlah penduduk, batas wilayah, tanggamus',
    ],
    'pemerintahan' => [
        'title'    => 'Pemerintahan | Pekon Gunung Megang',
        'desc'     => 'Struktur organisasi dan tata kelola pemerintahan Pekon Gunung Megang: kepala pekon, perangkat desa, dan lembaga kemasyarakatan desa.',
        'keywords' => 'pemerintahan desa gunung megang, kepala pekon, perangkat desa, struktur organisasi, bhp, lpm',
    ],
    'potensi-ekonomi' => [
        'title'    => 'Potensi & Ekonomi | Pekon Gunung Megang',
        'desc'     => 'Potensi ekonomi dan sumber daya alam Pekon Gunung Megang: pertanian, komoditas unggulan, mata pencaharian masyarakat, dan indeks pembangunan desa.',
        'keywords' => 'potensi desa gunung megang, ekonomi desa, pertanian, komoditas, mata pencaharian, idm',
    ],
    'apbpekon' => [
        'title'    => 'Transparansi APB Pekon | Pekon Gunung Megang',
        'desc'     => 'Anggaran Pendapatan dan Belanja Pekon (APBPekon) Gunung Megang: rincian pendapatan, belanja, dan pembiayaan setiap tahun anggaran secara terbuka dan transparan.',
        'keywords' => 'apb pekon gunung megang, apbdes, anggaran desa, transparansi, dana desa, add',
    ],
    'apbpekon-2026' => [
        'title'    => 'Transparansi APB Pekon | Pekon Gunung Megang',
        'desc'     => 'Anggaran Pendapatan dan Belanja Pekon (APBPekon) Gunung Megang: rincian pendapatan, belanja, dan pembiayaan secara terbuka dan transparan.',
        'keywords' => 'apb pekon gunung megang, apbdes 2026, anggaran desa, transparansi, dana desa, add',
    ],
    'kontak' => [
        'title'    => 'Kontak | Pekon Gunung Megang',
        'desc'     => 'Hubungi Kantor Pekon Gunung Megang, Kecamatan Pulau Panggung, Kabupaten Tanggamus: alamat, telepon, jam layanan, dan peta lokasi.',
        'keywords' => 'kontak pekon gunung megang, alamat kantor desa, telepon desa, peta lokasi, tanggamus',
    ],
];

$seo = $seoMap[$seoPage] ?? $seoMap['index'];

$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$baseUrl  = $scheme . '://' . $host . $basePath;
$canonical = $baseUrl . ($seoPage === 'index' ? '/' : '/' . $seoPage);

$ogImage  = $baseUrl . '/dashboard/assets/Lambang_Kabupaten_Tanggamus.png';
$telepon  = preg_replace('/[^0-9]/', '', $pekon['kontak']['telepon'] ?? '');
$telIntl  = $telepon !== '' ? '+62' . ltrim($telepon, '0') : '';

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
?>
<title><?= htmlspecialchars($seo['title']) ?></title>
<meta name="description" content="<?= htmlspecialchars($seo['desc']) ?>">
<meta name="keywords" content="<?= htmlspecialchars($seo['keywords']) ?>">
<meta name="author" content="<?= htmlspecialchars($siteName) ?>">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="theme-color" content="#004532">
<link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
<link rel="icon" href="dashboard/assets/Lambang_Kabupaten_Tanggamus.ico" type="image/x-icon">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= htmlspecialchars($siteName) ?>">
<meta property="og:title" content="<?= htmlspecialchars($seo['title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($seo['desc']) ?>">
<meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
<meta property="og:locale" content="id_ID">
<meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
<meta property="og:image:width" content="512">
<meta property="og:image:height" content="512">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($seo['title']) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($seo['desc']) ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">
<script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>