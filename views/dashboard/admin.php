<?php
require_once __DIR__ . '/../../functions/config.php';

// Halaman privat: larang pengindeksan oleh mesin pencari
header("X-Robots-Tag: noindex, nofollow, noarchive, nosnippet", true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil data sesi dari JSON
$sesi_id = (int) ($_SESSION['sesi_id'] ?? 0);
$user    = db_find_one('user', 'id_user', (string)$sesi_id);

$sesi_nama     = $user['nama_lengkap'] ?? '';
$sesi_username = $user['username'] ?? '';
$sesi_role     = $user['role'] ?? '';

// Pastikan pengguna sudah login dan memiliki role admin
if (!$user || $sesi_role !== 'admin') {
    header('Location: ../auth/login');
    exit();
}

$page = get_input($_GET, 'page', 50);
if ($page === '') $page = 'Dashboard';

$menuPages = [
    'Dashboard'    => 'Dashboard',
    'Profil Pekon' => 'Profil Pekon',
    'Demografi'    => 'Demografi',
    'Potensi'      => 'Potensi',
    'Layanan UMKM' => 'Layanan & UMKM',
    'APB Pekon'    => 'APB Pekon',
    'Aparat Desa'  => 'Aparat Desa',
    'Aspirasi'     => 'Kotak Aspirasi',
    'Profil'       => 'Profil Saya',
];

$pageTitle = isset($menuPages[$page]) ? $menuPages[$page] : 'Dashboard';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="description" content="Panel Administrasi <?php echo NAMA_WEB; ?>">
    <meta name="csrf-token" content="<?= csrf_token() ?>">

    <title><?= $pageTitle; ?> - Panel Admin <?php echo NAMA_WEB ?></title>

    <link rel="shortcut icon" href="assets/Lambang_Kabupaten_Tanggamus.ico" type="image/x-icon">

    <?php include __DIR__ . '/../../dashboard/admin-pages/css.php'; ?>
</head>

<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <img src="assets/Lambang_Kabupaten_Tanggamus.png" alt="<?= NAMA_WEB; ?>" style="width:48px;height:auto;">
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <div class="theme-toggle d-flex gap-2 align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                    role="img" class="iconify iconify--system-uicons" width="20" height="20"
                                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                            opacity=".3"></path>
                                        <g transform="translate(-210 -1)">
                                            <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                            <circle cx="220.5" cy="11.5" r="4"></circle>
                                            <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
                                        </g>
                                    </g>
                                </svg>
                                <div class="form-check form-switch fs-6">
                                    <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                                    <label class="form-check-label"></label>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                    role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet"
                                    viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                                    </path>
                                </svg>
                            </div>
                            <button type="button" class="sidebar-hide-btn d-xl-none" aria-label="Tutup menu sidebar">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu Utama</li>

                        <li class="sidebar-item <?= $page === 'Dashboard' ? 'active' : '' ?>">
                            <a href="?page=Dashboard" class="sidebar-link">
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Profil &amp; Wilayah</li>

                        <li class="sidebar-item <?= $page === 'Profil Pekon' ? 'active' : '' ?>">
                            <a href="?page=Profil Pekon" class="sidebar-link">
                                <i class="bi bi-globe2"></i>
                                <span>Profil Pekon</span>
                            </a>
                        </li>

                        <li class="sidebar-item <?= $page === 'Demografi' ? 'active' : '' ?>">
                            <a href="?page=Demografi" class="sidebar-link">
                                <i class="bi bi-people-fill"></i>
                                <span>Demografi &amp; Wilayah</span>
                            </a>
                        </li>

                        <li class="sidebar-item <?= $page === 'Potensi' ? 'active' : '' ?>">
                            <a href="?page=Potensi" class="sidebar-link">
                                <i class="bi bi-tree-fill"></i>
                                <span>Potensi &amp; IDM</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Layanan &amp; Keuangan</li>

                        <li class="sidebar-item <?= $page === 'Layanan UMKM' ? 'active' : '' ?>">
                            <a href="?page=Layanan UMKM" class="sidebar-link">
                                <i class="bi bi-compass"></i>
                                <span>Layanan &amp; UMKM</span>
                            </a>
                        </li>

                        <li class="sidebar-item <?= $page === 'APB Pekon' ? 'active' : '' ?>">
                            <a href="?page=APB Pekon" class="sidebar-link">
                                <i class="bi bi-wallet2"></i>
                                <span>APB Pekon</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Pemerintahan</li>

                        <li class="sidebar-item <?= $page === 'Aparat Desa' ? 'active' : '' ?>">
                            <a href="?page=Aparat Desa" class="sidebar-link">
                                <i class="bi bi-building"></i>
                                <span>Aparat &amp; Lembaga</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Interaksi Publik</li>

                        <li class="sidebar-item <?= $page === 'Aspirasi' ? 'active' : '' ?>">
                            <a href="?page=Aspirasi" class="sidebar-link">
                                <i class="bi bi-envelope-open"></i>
                                <span>Kotak Aspirasi</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Akun</li>

                        <li class="sidebar-item <?= $page === 'Profil' ? 'active' : '' ?>">
                            <a href="?page=Profil" class="sidebar-link">
                                <i class="bi bi-person-circle"></i>
                                <span>Profil Saya</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link fw-bold"
                                data-bs-toggle="modal" data-bs-target="#modal-logout">
                                <i class="bi bi-box-arrow-right fs-5"></i>
                                <span>Logout</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <!-- Backdrop mobile sidebar -->
        <div id="sidebar-backdrop" aria-hidden="true"></div>
        <div id="main">
            <header class="d-flex align-items-center gap-3 mb-3">
                <button type="button" id="btn-hamburger" aria-label="Tampilkan/sembunyikan menu">
                    <i class="bi bi-list"></i>
                </button>
                <a class="btn btn-sm btn-outline-secondary ms-auto" href="../index" target="_blank" title="Buka halaman depan">
                    <i class="bi bi-box-arrow-up-right"></i> <span class="d-none d-sm-inline">Lihat Situs</span>
                </a>
            </header>

            <?php
            switch ($page) {
                case 'Profil Pekon':
                    include __DIR__ . '/../../dashboard/admin-pages/profil_pekon.php';
                    break;
                case 'Demografi':
                    include __DIR__ . '/../../dashboard/admin-pages/demografi.php';
                    break;
                case 'Potensi':
                    include __DIR__ . '/../../dashboard/admin-pages/potensi.php';
                    break;
                case 'Layanan UMKM':
                    include __DIR__ . '/../../dashboard/admin-pages/layanan-umkm.php';
                    break;
                case 'APB Pekon':
                    include __DIR__ . '/../../dashboard/admin-pages/apb_pekon.php';
                    break;
                case 'Aparat Desa':
                    include __DIR__ . '/../../dashboard/admin-pages/aparat_desa.php';
                    break;
                case 'Aspirasi':
                    include __DIR__ . '/../../dashboard/admin-pages/aspirasi.php';
                    break;
                case 'Profil':
                    include __DIR__ . '/../../dashboard/admin-pages/profil.php';
                    break;
                case 'Dashboard':
                default:
                    include __DIR__ . '/../../dashboard/admin-pages/dashboard.php';
                    break;
            }
            ?>

            <footer class="app-footer">
                <p>
                    <script>
                        document.write(new Date().getFullYear())
                    </script>
                    &copy; <?php echo NAMA_WEB; ?>
                </p>
                <p class="text-end">
                    Dikembangkan oleh <?php echo NAMA_LENGKAP; ?> –
                    <?php echo NAMA_KAMPUS; ?> •
                    <a href="<?php echo URL_IG; ?>" target="_blank" rel="noopener">@<?php echo IG; ?></a>
                </p>
            </footer>
        </div>
    </div>

    <!-- ================= MODAL LOGOUT ================= -->
    <div class="modal fade" id="modal-logout" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin keluar dari sistem?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="../auth/logout" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <?php include __DIR__ . '/../../dashboard/admin-pages/js.php'; ?>
    <!-- JS -->

    <script src="../assets/js/security-warning.js" defer></script>
</body>

</html>