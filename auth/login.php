<?php
require_once '../functions/config.php';

session_start();
if (!empty($_SESSION['sesi_role'])) {
    if (strtolower($_SESSION['sesi_role']) === 'admin') {
        header('Location: ../dashboard/admin?page=Dashboard');
        exit;
    } else {
        header('Location: ../auth/logout');
        exit;
    }
}

$usernameLogin = isset($_GET['username']) ? $_GET['username'] : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="../dashboard/assets/Lambang_Kabupaten_Tanggamus.ico" type="image/x-icon">

    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Login panel administrasi <?php echo NAMA_WEB; ?>">
    <title>Login - <?php echo NAMA_WEB ?></title>

    <link rel="stylesheet" href="../dashboard/assets/compiled/css/app.css">
    <link rel="stylesheet" href="../dashboard/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="../dashboard/assets/compiled/css/auth.css">
    <link rel="stylesheet" href="../dashboard/assets/extensions/sweetalert2/sweetalert2.min.css">
    <style>
        :root {
            --primary: #0ea5a4;
            /* warna tombol utama */
            --primary2: #0b3b4a;
            /* navy teal */
            --bg1: #f6fbfb;
            --bg2: #edf6f6;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
        }

        body {
            background: radial-gradient(900px 600px at 12% 18%, rgba(14, 165, 164, .14), transparent 60%),
                radial-gradient(800px 520px at 88% 22%, rgba(11, 59, 74, .10), transparent 55%),
                linear-gradient(135deg, var(--bg1), var(--bg2));
            min-height: 100vh;
            margin: 0;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 26px 14px;
        }

        /* Wrapper agar footer tetap rapi */
        .auth-shell {
            width: 100%;
            max-width: 560px;
            /* FINAL: lebih kecil */
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        /* Header kecil di atas card */
        .brand-top {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            user-select: none;
        }

        .brand-top img {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 10px 22px rgba(15, 23, 42, .10);
            border: 1px solid rgba(226, 232, 240, .9);
            background: #fff;
            padding: 6px;
        }

        .brand-top .brand-text {
            text-align: left;
            line-height: 1.1;
        }

        .brand-top .brand-text .title {
            font-weight: 900;
            color: var(--primary2);
            letter-spacing: .2px;
            font-size: 15px;
        }

        .brand-top .brand-text .sub {
            color: var(--muted);
            font-size: 12.5px;
            font-weight: 600;
        }

        /* Card login */
        .card {
            max-width: 420px;
            /* FINAL: card diperkecil */
            width: 100%;
            margin: 0 auto;
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 14px 40px rgba(15, 23, 42, .10);
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(6px);
        }

        .card-header {
            border-bottom: 1px solid rgba(226, 232, 240, .85);
            background: linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(255, 255, 255, .93));
            padding: 18px 18px 12px;
            /* FINAL: lebih rapat */
        }

        .card-body {
            padding: 14px 18px 18px;
            /* FINAL: lebih rapat */
        }

        .auth-title {
            color: var(--primary2) !important;
            font-weight: 900;
            letter-spacing: .2px;
            margin-bottom: 4px;
            font-size: 24px;
            /* FINAL: judul sedikit kecil */
        }

        .auth-subtitle {
            color: var(--muted) !important;
            margin-bottom: 0;
            font-size: 14px;
        }

        .form-label,
        label {
            font-size: 14px;
            color: var(--text);
            font-weight: 700;
        }

        .form-control {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: .75rem .9rem;
            /* FINAL: input lebih kecil */
            transition: .15s ease;
        }

        .form-control:focus {
            border-color: rgba(14, 165, 164, .60);
            box-shadow: 0 0 0 .25rem rgba(14, 165, 164, .18);
        }

        /* Tombol utama (kamu pakai class btn-danger) */
        .btn-danger,
        .btn-primary {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
            border-radius: 12px;
            font-weight: 800;
            padding: .75rem 1rem;
            /* FINAL: tombol lebih kecil */
            font-size: 15px;
            box-shadow: 0 12px 22px rgba(14, 165, 164, .25);
            transition: .15s ease;
        }

        .btn-danger:hover,
        .btn-primary:hover {
            background-color: #0b8c8b !important;
            border-color: #0b8c8b !important;
            transform: translateY(-1px);
        }

        .text-danger {
            color: var(--primary2) !important;
        }

        /* Footer/copyright */
        .copyright {
            text-align: center;
            font-size: 12.5px;
            color: rgba(100, 116, 139, .95);
            line-height: 1.45;
            padding: 6px 10px 0;
        }

        .copyright a {
            color: var(--primary2);
            font-weight: 800;
            text-decoration: none;
        }

        .copyright a:hover {
            text-decoration: underline;
        }

        /* Responsive: mobile tetap nyaman */
        @media (max-width: 480px) {
            body {
                padding: 22px 12px;
            }

            .auth-shell {
                max-width: 420px;
            }

            .card {
                max-width: 100%;
            }

            .card-header,
            .card-body {
                padding-left: 16px;
                padding-right: 16px;
            }

            .brand-top {
                flex-direction: column;
                gap: 10px;
            }

            .brand-top .brand-text {
                text-align: center;
            }
        }
    </style>


</head>

<body>
    <script src="../dashboard/assets/static/js/initTheme.js"></script>

    <div class="auth-shell">

        <!-- Brand header -->
        <div class="brand-top">
            <img src="../dashboard/assets/Lambang_Kabupaten_Tanggamus.png" alt="Logo">
            <div class="brand-text">
                <div class="title"><?php echo NAMA_WEB; ?></div>
                <div class="sub">Sistem Informasi Pekon Gunung Megang</div>
            </div>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="auth-title text-danger">Log In Akun</h2>
                <p class="auth-subtitle">Silakan masuk untuk melanjutkan</p>
            </div>

            <div class="card-body">
                <form class="form" data-parsley-validate action="../functions/function_auth.php" method="post" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-group position-relative has-icon-left mb-3 has-icon-left">
                        <label for="username" class="form-label">Username</label>
                        <div class="position-relative">
                            <input type="text" name="username" class="form-control form-control-xl"
                                placeholder="Masukkan username" value="<?= htmlspecialchars($usernameLogin); ?>"
                                id="username" data-parsley-required="true" minlength="5">
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group position-relative has-icon-left mb-3 has-icon-left">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="password" name="password" class="form-control form-control-xl"
                                placeholder="••••••" id="password" data-parsley-required="true" minlength="5">
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                    </div>

                    <button name="btn_login" type="submit" class="btn btn-danger btn-block btn-lg shadow-lg mt-2">
                        Log In
                    </button>
                    <a href="../index" class="btn btn-block mt-2" style="background:#fff;border:1px solid var(--border);color:var(--primary2);border-radius:12px;font-weight:800;padding:.75rem 1rem;font-size:15px;">
                        Ke Beranda
                    </a>
                </form>
            </div>
        </div>

        <!-- Copyright -->
        <div class="copyright">
            © <?php echo date('Y'); ?> <b><?php echo NAMA_WEB; ?></b> •
            Dibuat oleh <?php echo NAMA_LENGKAP; ?> (<?php echo NAMA_KAMPUS; ?>) —
            <a href="<?php echo URL_IG; ?>" target="_blank" rel="noopener">@<?php echo IG; ?></a>
        </div>

    </div>

    <script src="../dashboard/assets/extensions/jquery/jquery.min.js"></script>
    <script src="../dashboard/assets/extensions/parsleyjs/parsley.min.js"></script>
    <script src="../dashboard/assets/static/js/pages/parsley.js"></script>
    <script src="../dashboard/assets/extensions/sweetalert2/sweetalert2.min.js"></script>

    <!-- script sweetalert kamu biarin seperti semula -->
    <script src="sweetalert2_auth.js"></script>
<script src="../assets/js/security-warning.js" defer></script>
</body>


</html>