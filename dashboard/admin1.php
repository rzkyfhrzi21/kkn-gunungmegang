<?php
session_start();

require_once '../functions/config.php';

// Ambil data sesi dari JSON
$sesi_id = (int) ($_SESSION['sesi_id'] ?? 0);
$user    = db_find_one('user', 'id_user', (string)$sesi_id);

$sesi_nama     = $user['nama_lengkap'] ?? '';
$sesi_username = $user['username'] ?? '';
$sesi_role     = $user['role'] ?? '';
$sesi_img      = '';

// Pastikan pengguna sudah login dan memiliki role admin
if (!$user || $sesi_role !== 'admin') {
    header('Location: ../auth/login');
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : '';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="robots content=" noindex, nofollow">

    <title><?= ucfirst($page); ?> - Panel Admin <?php echo NAMA_WEB ?></title>

    <link rel="shortcut icon" href="assets/Lambang_Kabupaten_Tanggamus.ico" type="image/x-icon">

    <?php include 'admin-pages/css.php'; ?>

</head>

<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="app">

        <!-- ================= SIDEBAR ================= -->
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo text-center">
                            <img src="assets/Lambang_Kabupaten_Tanggamus.png" width="48" alt="Profil Pekon Gunung Megang">
                        </div>

                        <!-- Dark mode toggle (biarkan) -->
                        <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input me-0" type="checkbox" id="toggle-dark">
                            </div>
                        </div>

                        <div class="sidebar-toggler x">
                            <a href="#" class="sidebar-hide d-xl-none d-block">
                                <i class="bi bi-x bi-middle"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu Utama</li>

                        <!-- DASHBOARD -->
                        <li class="sidebar-item">
                            <a href="?page=Dashboard" class="sidebar-link">
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <!-- USER -->
                        <li class="sidebar-item">
                            <a href="?page=Data User" class="sidebar-link">
                                <i class="bi bi-people-fill"></i>
                                <span>Manajemen User</span>
                            </a>
                        </li>

                        <!-- PENDUDUk -->
                        <li class="sidebar-item has-sub">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-person-lines-fill"></i>
                                <span>Manajemen Penduduk</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="?page=Data Penduduk">Data Penduduk</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="?page=Data Kepala KK">Data Kepala KK</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="?page=Data KK">Data KK</a>
                                </li>
                            </ul>
                        </li>

                        <!-- SURAT -->
                        <li class="sidebar-item has-sub">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-envelope-paper-fill"></i>
                                <span>Administrasi Surat</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="?page=Permohonan Surat">Permohonan Surat</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="?page=Jenis Surat">Jenis Surat</a>
                                </li>
                            </ul>
                        </li>

                        <!-- INFORMASI DESA -->
                        <li class="sidebar-item">
                            <a href="?page=Informasi Desa" class="sidebar-link">
                                <i class="bi bi-newspaper"></i>
                                <span>Portal Berita Desa</span>
                            </a>
                        </li>

                        <!-- APARAT DESA -->
                        <li class="sidebar-item">
                            <a href="?page=Aparat Desa" class="sidebar-link">
                                <i class="bi bi-building"></i>
                                <span>Aparat Desa</span>
                            </a>
                        </li>

                        <!-- PROFIL -->
                        <li class="sidebar-item">
                            <a href="?page=Profil" class="sidebar-link">
                                <i class="bi bi-person-circle"></i>
                                <span>Profil Saya</span>
                            </a>
                        </li>

                        <!-- LOGOUT -->
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

            <!-- ================= MAIN ================= -->
            <div id="main">
                <header class="mb-3">
                    <a href="#" class="burger-btn d-xl-none d-block">
                        <i class="bi bi-justify fs-3"></i>
                    </a>
                </header>

                <?php
                switch ($page) {

                    // =====================
                    // DASHBOARD
                    // =====================
                    case 'Dashboard':
                        include 'admin-pages/dashboard.php';
                        break;

                    // =====================
                    // USER
                    // =====================
                    case 'Data User':
                        include 'admin-pages/data_user.php';
                        break;

                    // =====================
                    // DATA PENDUDUK & KARTU KELUARGA
                    // =====================
                    case 'Data Penduduk':
                        include 'admin-pages/data_penduduk.php';
                        break;

                    case 'Data Kepala KK':
                        include 'admin-pages/data_kepala_kk.php';
                        break;

                    case 'Data KK':
                        include 'admin-pages/data_kk.php';
                        break;

                    // =====================
                    // SURAT
                    // =====================
                    case 'Permohonan Surat':
                        include 'admin-pages/permohonan_surat.php';
                        break;

                    case 'Jenis Surat':
                        include 'admin-pages/jenis_surat.php';
                        break;

                    // =====================
                    // INFORMASI DESA
                    // =====================
                    case 'Informasi Desa':
                        include 'admin-pages/informasi_desa.php';
                        break;

                    // =====================
                    // APARAT DESA
                    // =====================
                    case 'Aparat Desa':
                        include 'admin-pages/aparat_desa.php';
                        break;

                    // =====================
                    // PROFIL
                    // =====================
                    case 'Profil':
                        include 'admin-pages/profil.php';
                        break;
                }
                ?>
                <footer>
                    <div class="container">
                        <div class="footer clearfix mb-0 text-muted">
                            <div class="float-start">
                                <p>
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script>
                                    &copy; <?php echo NAMA_WEB; ?>
                                </p>
                            </div>
                            <div class="float-end">
                                <p>
                                    Dikembangkan oleh <?php echo NAMA_LENGKAP; ?> –
                                    <?php echo NAMA_KAMPUS; ?> •
                                    <a href="<?php echo URL_IG; ?>" target="_blank" rel="noopener">@<?php echo IG; ?></a>
                                </p>
                            </div>
                        </div>
                    </div>
                </footer>


            </div>
        </div>
        <!-- JS -->
        <?php include 'admin-pages/js.php'; ?>
        <!-- JS -->

</body>

</html>