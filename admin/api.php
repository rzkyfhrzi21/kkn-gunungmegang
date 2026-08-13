<?php
/**
 * admin/api.php
 * Endpoint AJAX (POST only) untuk CRUD data pekon yang disimpan di includes/*.php
 * dan manajemen user di db/json/user.json.
 *
 * Semua aksi menerima body JSON (POST) dan merespons JSON.
 * Aksi: list, save, save_row, delete, profile
 */
require_once __DIR__ . '/../functions/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('ADMIN_API_TEST')) {
    header('Content-Type: application/json; charset=utf-8');

    $sesi_id = (int) ($_SESSION['sesi_id'] ?? 0);
    $user    = db_find_one('user', 'id_user', (string)$sesi_id);
    if (!$user || ($user['role'] ?? '') !== 'admin') {
        echo json_encode(['ok' => false, 'error' => 'Unauthorized', 'detail' => 'Sesi tidak valid atau bukan admin.']);
        exit;
    }
}

$INCLUDES = dirname(__DIR__) . '/includes';
$UPLOADS  = dirname(__DIR__) . '/assets/uploads';

class ApiResponse extends Exception {
    public $payload;
    public function __construct($payload) {
        parent::__construct('api-response');
        $this->payload = $payload;
    }
}

function json_api_fail($msg, $detail = '') {
    throw new ApiResponse(['ok' => false, 'error' => $msg, 'detail' => $detail]);
}

function json_api_ok($data = []) {
    throw new ApiResponse(array_merge(['ok' => true], $data));
}

/** Tulis array PHP ke file includes/*.php secara atomik dengan var_export (aman escaping). */
function json_api_write_php($file, $data, $comment) {
    $export = var_export($data, true);
    $export = preg_replace('/^/m', '    ', trim($export));
    $code   = "<?php\n// $comment\n\nreturn " . $export . ";\n";
    $tmp    = $file . '.tmp';
    if (file_put_contents($tmp, $code, LOCK_EX) === false) {
        json_api_fail('Gagal menulis file data.', 'Tidak dapat menulis ke ' . basename($file));
    }
    if (!rename($tmp, $file)) {
        @unlink($tmp);
        json_api_fail('Gagal menyimpan data.', 'Rename file gagal.');
    }
    @chmod($file, 0644);
    return $data;
}

function json_api_read_module($name) {
    global $INCLUDES;
    $path = $INCLUDES . '/' . $name . '.php';
    if (!file_exists($path)) return [];
    $v = include $path;
    return is_array($v) ? $v : [];
}

function json_api_str($v, $max = 255) {
    $v = is_string($v) ? $v : '';
    $v = trim($v);
    $v = str_replace(["\0", "\r", "\n"], '', $v);
    $v = strip_tags($v);
    return mb_substr($v, 0, (int)$max);
}

function json_api_int($v) {
    return (int) ($v === '' ? 0 : $v);
}

function json_api_float($v) {
    $v = str_replace(['.', ','], ['.', '.'], (string)$v);
    return (float) $v;
}

/* ================= NORMALIZER PER MODUL ================= */

function norm_pekon($raw) {
    $k = $raw['kepala_pekon'] ?? [];
    $c = $raw['kontak'] ?? [];
    return [
        'nama'         => json_api_str($raw['nama'] ?? '', 100),
        'kecamatan'    => json_api_str($raw['kecamatan'] ?? '', 100),
        'kabupaten'    => json_api_str($raw['kabupaten'] ?? '', 100),
        'provinsi'     => json_api_str($raw['provinsi'] ?? '', 100),
        'tahun'        => json_api_str($raw['tahun'] ?? '', 10),
        'kepala_pekon' => [
            'nama'    => json_api_str($k['nama'] ?? '', 150),
            'foto'    => json_api_str($k['foto'] ?? '', 255),
            'jabatan' => json_api_str($k['jabatan'] ?? '', 150),
        ],
        'kontak' => [
            'telepon'   => json_api_str($c['telepon'] ?? '', 30),
            'maps_code' => json_api_str($c['maps_code'] ?? '', 255),
            'maps_link' => sanitize_url($c['maps_link'] ?? '', 500),
        ],
    ];
}

function norm_demografi($raw) {
    $b = $raw['batas_wilayah'] ?? [];
    return [
        'laki_laki'             => json_api_int($raw['laki_laki'] ?? 0),
        'perempuan'             => json_api_int($raw['perempuan'] ?? 0),
        'total_jiwa'            => json_api_int($raw['total_jiwa'] ?? 0),
        'jumlah_kk'             => json_api_int($raw['jumlah_kk'] ?? 0),
        'luas_wilayah_km2'      => json_api_float($raw['luas_wilayah_km2'] ?? 0),
        'luas_wilayah_ha'       => json_api_int($raw['luas_wilayah_ha'] ?? 0),
        'jarak_kecamatan_km'    => json_api_int($raw['jarak_kecamatan_km'] ?? 0),
        'waktu_kecamatan_menit' => json_api_int($raw['waktu_kecamatan_menit'] ?? 0),
        'batas_wilayah' => [
            'utara'   => json_api_str($b['utara'] ?? '', 200),
            'timur'   => json_api_str($b['timur'] ?? '', 200),
            'selatan' => json_api_str($b['selatan'] ?? '', 200),
            'barat'   => json_api_str($b['barat'] ?? '', 200),
        ],
    ];
}

function norm_potensi($raw) {
    $mp = $raw['mata_pencaharian'] ?? [];
    $list = [];
    foreach ($mp as $m) {
        $s = json_api_str($m, 150);
        if ($s !== '') $list[] = $s;
    }
    return [
        'tumpang_sari'      => json_api_int($raw['tumpang_sari'] ?? 0),
        'sawah'             => json_api_int($raw['sawah'] ?? 0),
        'jagung'            => json_api_int($raw['jagung'] ?? 0),
        'idm_status'        => json_api_str($raw['idm_status'] ?? '', 100),
        'mata_pencaharian'  => $list,
    ];
}

function norm_apbpekon($raw) {
    $p = $raw['pendapatan'] ?? [];
    $b = $raw['belanja'] ?? [];
    $f = $raw['pembiayaan'] ?? [];

    $pendapatan = [
        'alokasi_dana_pekon' => json_api_float($p['alokasi_dana_pekon'] ?? 0),
        'dana_desa'          => json_api_float($p['dana_desa'] ?? 0),
        'bagi_hasil_pajak'   => json_api_float($p['bagi_hasil_pajak'] ?? 0),
        'bantuan_provinsi'   => json_api_float($p['bantuan_provinsi'] ?? 0),
        'pendapatan_lain'    => json_api_float($p['pendapatan_lain'] ?? 0),
    ];
    $belanja = [
        'penyelenggaraan_pemerintahan' => json_api_float($b['penyelenggaraan_pemerintahan'] ?? 0),
        'pembangunan_pekon'            => json_api_float($b['pembangunan_pekon'] ?? 0),
        'pembinaan_kemasyarakatan'     => json_api_float($b['pembinaan_kemasyarakatan'] ?? 0),
        'pemberdayaan_masyarakat'      => json_api_float($b['pemberdayaan_masyarakat'] ?? 0),
        'penanggulangan_bencana'       => json_api_float($b['penanggulangan_bencana'] ?? 0),
    ];
    $pendapatan['total'] = array_sum($pendapatan);
    $belanja['total']    = array_sum($belanja);

    $penerimaan  = json_api_float($f['penerimaan'] ?? 0);
    $pengeluaran = json_api_float($f['pengeluaran'] ?? 0);
    $pembiayaan = [
        'penerimaan'       => $penerimaan,
        'pengeluaran'      => $pengeluaran,
        'pembiayaan_netto' => $penerimaan - $pengeluaran,
        'silpa'            => json_api_float($f['silpa'] ?? 0),
    ];

    return [
        'tahun'       => json_api_int($raw['tahun'] ?? 0),
        'pendapatan'  => $pendapatan,
        'belanja'     => $belanja,
        'pembiayaan'  => $pembiayaan,
    ];
}

function norm_perangkat($raw) {
    $list = [];
    foreach ($raw as $row) {
        if (!is_array($row)) continue;
        $nama    = json_api_str($row['nama'] ?? '', 150);
        $jabatan = json_api_str($row['jabatan'] ?? '', 150);
        if ($nama !== '' && $jabatan !== '') {
            $list[] = ['jabatan' => $jabatan, 'nama' => $nama];
        }
    }
    return $list;
}

/* ================= SUB-LIST DEFINISI (untuk tabel AJAX) ================= */

function json_api_sublist($module) {
    $labels = [
        'pendapatan' => [
            'alokasi_dana_pekon' => 'Alokasi Dana Pekon (ADD)',
            'dana_desa'          => 'Dana Desa (DD)',
            'bagi_hasil_pajak'   => 'Bagi Hasil Pajak & Retribusi (BHPRD)',
            'bantuan_provinsi'   => 'Bantuan Provinsi',
            'pendapatan_lain'    => 'Pendapatan Lain-lain',
        ],
        'belanja' => [
            'penyelenggaraan_pemerintahan' => 'Penyelenggaraan Pemerintahan',
            'pembangunan_pekon'            => 'Pembangunan Pekon',
            'pembinaan_kemasyarakatan'     => 'Pembinaan Kemasyarakatan',
            'pemberdayaan_masyarakat'      => 'Pemberdayaan Masyarakat',
            'penanggulangan_bencana'       => 'Penanggulangan Bencana',
        ],
        'pembiayaan' => [
            'penerimaan'       => 'Penerimaan Pembiayaan',
            'pengeluaran'      => 'Pengeluaran Pembiayaan',
            'pembiayaan_netto' => 'Pembiayaan Netto',
            'silpa'            => 'SILPA',
        ],
    ];
    return $labels[$module] ?? [];
}

/* ================= HANDLER LIST ================= */

function api_list($module, $page, $perPage, $search, $filters) {
    global $INCLUDES;

    $search = trim(strtolower($search ?? ''));

    if ($module === 'user') {
        $rows = db_read('user');
        $rawRows = $rows;
    } elseif ($module === 'perangkat') {
        $rows = json_api_read_module('perangkat');
        $tmp = [];
        foreach ($rows as $i => $r) {
            $r['idx'] = $i;
            $tmp[] = $r;
        }
        $rows = $tmp;
        $rawRows = $rows;
    } elseif ($module === 'mata_pencaharian') {
        $dataMod = json_api_read_module('potensi');
        $list = $dataMod['mata_pencaharian'] ?? [];
        $rows = [];
        foreach ($list as $i => $m) {
            $rows[] = ['index' => $i, 'nama' => $m];
        }
        $rawRows = $rows;
    } elseif (in_array($module, ['pendapatan', 'belanja', 'pembiayaan'], true)) {
        $data = json_api_read_module('apbpekon');
        $sub  = $data[$module] ?? [];
        $rows = [];
        foreach (json_api_sublist($module) as $key => $label) {
            $rows[] = ['key' => $key, 'label' => $label, 'nominal' => $sub[$key] ?? 0];
        }
        $rawRows = $rows;
    } else {
        json_api_fail('Modul tidak dikenal.', $module);
    }

    /* filter options (dari data penuh, sebelum search) */
    $filterOptions = [];
    if ($module === 'perangkat') {
        $jabatans = [];
        $jenis = [];
        foreach ($rows as $r) {
            $jabatans[$r['jabatan']] = true;
            $jenis[strpos(strtoupper($r['jabatan']), 'BHP') !== false || strpos(strtoupper($r['jabatan']), 'LPM') !== false ? 'Lembaga' : 'Perangkat'] = true;
        }
        $filterOptions = ['jabatan' => array_keys($jabatans), 'jenis' => array_keys($jenis)];
    } elseif ($module === 'user') {
        $roles = [];
        foreach ($rows as $r) $roles[$r['role']] = true;
        $filterOptions = ['role' => array_keys($roles)];
    }

    /* filter */
    if (!empty($filters) && is_array($filters)) {
        foreach ($filters as $key => $val) {
            if ($val === '' || $val === null) continue;
            $rows = array_values(array_filter($rows, function ($r) use ($key, $val) {
                if ($key === 'jenis') {
                    $j = strtoupper($r['jabatan'] ?? '');
                    $isLembaga = strpos($j, 'BHP') !== false || strpos($j, 'LPM') !== false;
                    return $val === 'Lembaga' ? $isLembaga : !$isLembaga;
                }
                return (string)($r[$key] ?? '') === (string)$val;
            }));
        }
    }

    /* search */
    if ($search !== '') {
        $rows = array_values(array_filter($rows, function ($r) use ($search) {
            $hay = strtolower(implode(' ', $r));
            return strpos($hay, $search) !== false;
        }));
    }

    $total = count($rows);
    $perPage = max(1, min(100, (int)$perPage));
    $pages = max(1, (int)ceil($total / $perPage));
    $page = max(1, min($pages, (int)$page));
    $rows = array_slice($rows, ($page - 1) * $perPage, $perPage);

    json_api_ok([
        'module' => $module,
        'rows' => array_values($rows),
        'total' => $total,
        'page' => $page,
        'pages' => $pages,
        'perPage' => $perPage,
        'filterOptions' => $filterOptions,
    ]);
}

/* ================= HANDLER SAVE ================= */

function api_save_module($module, $data) {
    global $INCLUDES;

    switch ($module) {
        case 'pekon':
            $old = json_api_read_module('pekon');
            $new = norm_pekon($data);
            /* bersihkan foto lama yang di-upload bila diganti */
            $oldFoto = $old['kepala_pekon']['foto'] ?? '';
            $newFoto = $new['kepala_pekon']['foto'];
            if ($oldFoto !== $newFoto && strpos($oldFoto, 'assets/uploads/') === 0) {
                @unlink(dirname($INCLUDES) . '/' . $oldFoto);
            }
            json_api_write_php($INCLUDES . '/pekon.php', $new, 'includes/pekon.php - Identitas & informasi umum Pekon Gunung Megang');
            break;

        case 'demografi':
            $new = norm_demografi($data);
            json_api_write_php($INCLUDES . '/demografi.php', $new, 'includes/demografi.php - Data kependudukan, wilayah, dan batas pekon');
            break;

        case 'potensi':
            $new = norm_potensi($data);
            json_api_write_php($INCLUDES . '/potensi.php', $new, 'includes/potensi.php - Potensi ekonomi dan sumber daya alam pekon');
            break;

        case 'apbpekon':
            $new = norm_apbpekon($data);
            json_api_write_php($INCLUDES . '/apbpekon.php', $new, 'includes/apbpekon.php - Anggaran Pendapatan dan Belanja Pekon (APBPekon)');
            break;

        default:
            json_api_fail('Modul tidak dikenal.', $module);
    }

    json_api_ok(['module' => $module, 'saved' => $new]);
}

/* ================= HANDLER SAVE_ROW / DELETE ================= */

function api_save_row($module, $data) {
    global $INCLUDES;

    if ($module === 'perangkat') {
        $rows = json_api_read_module('perangkat');
        $nama    = json_api_str($data['nama'] ?? '', 150);
        $jabatan = json_api_str($data['jabatan'] ?? '', 150);
        if ($nama === '' || $jabatan === '') json_api_fail('Nama dan jabatan wajib diisi.');
        $idx = isset($data['index']) && $data['index'] !== '' ? (int)$data['index'] : null;
        if ($idx !== null && isset($rows[$idx])) {
            $rows[$idx] = ['jabatan' => $jabatan, 'nama' => $nama];
        } else {
            $rows[] = ['jabatan' => $jabatan, 'nama' => $nama];
        }
        json_api_write_php($INCLUDES . '/perangkat.php', norm_perangkat($rows), 'includes/perangkat.php - Daftar perangkat pekon beserta jabatan');
        json_api_ok(['module' => $module, 'saved' => ['jabatan' => $jabatan, 'nama' => $nama]]);
    }

    if ($module === 'mata_pencaharian') {
        $dataMod = json_api_read_module('potensi');
        $list = $dataMod['mata_pencaharian'] ?? [];
        $val = json_api_str($data['nama'] ?? '', 150);
        if ($val === '') json_api_fail('Nama mata pencaharian wajib diisi.');
        $idx = isset($data['index']) && $data['index'] !== '' ? (int)$data['index'] : null;
        if ($idx !== null && isset($list[$idx])) {
            $list[$idx] = $val;
        } else {
            $list[] = $val;
        }
        $dataMod['mata_pencaharian'] = $list;
        json_api_write_php($INCLUDES . '/potensi.php', norm_potensi($dataMod), 'includes/potensi.php - Potensi ekonomi dan sumber daya alam pekon');
        json_api_ok(['module' => $module, 'saved' => ['nama' => $val]]);
    }

    if ($module === 'pendapatan' || $module === 'belanja' || $module === 'pembiayaan') {
        $key = $data['key'] ?? '';
        $labels = json_api_sublist($module);
        if (!isset($labels[$key])) json_api_fail('Pos tidak dikenal.', $key);
        $dataMod = json_api_read_module('apbpekon');
        $dataMod[$module][$key] = json_api_float($data['nominal'] ?? 0);
        json_api_write_php($INCLUDES . '/apbpekon.php', norm_apbpekon($dataMod), 'includes/apbpekon.php - Anggaran Pendapatan dan Belanja Pekon (APBPekon)');
        json_api_ok(['module' => $module, 'saved' => ['key' => $key, 'nominal' => $dataMod[$module][$key]]]);
    }

    if ($module === 'user') {
        $users = db_read('user');
        $id = isset($data['id_user']) && $data['id_user'] !== '' ? (int)$data['id_user'] : null;
        $username = json_api_str($data['username'] ?? '', 100);
        $nama     = json_api_str($data['nama_lengkap'] ?? '', 150);
        $role     = json_api_str($data['role'] ?? '', 20);
        if ($username === '' || $nama === '' || $role === '') json_api_fail('Username, nama, dan role wajib diisi.');

        foreach ($users as $u) {
            if (strtolower($u['username']) === strtolower($username) && (int)$u['id_user'] !== $id) {
                json_api_fail('Username sudah digunakan.', $username);
            }
        }

        $password = json_api_str($data['password'] ?? '', 255);

        if ($id !== null) {
            $found = false;
            foreach ($users as $i => $u) {
                if ((int)$u['id_user'] === $id) {
                    $u['username']    = $username;
                    $u['nama_lengkap'] = $nama;
                    $u['role']        = $role;
                    if ($password !== '') $u['password'] = password_hash($password, PASSWORD_DEFAULT);
                    $users[$i] = $u;
                    $found = true;
                    break;
                }
            }
            if (!$found) json_api_fail('User tidak ditemukan.');
        } else {
            if ($password === '') json_api_fail('Password wajib diisi untuk user baru.');
            $users[] = [
                'id_user'     => db_next_id('user', 'id_user'),
                'username'    => $username,
                'password'    => password_hash($password, PASSWORD_DEFAULT),
                'role'        => $role,
                'nama_lengkap'=> $nama,
            ];
        }
        db_write('user', $users);
        json_api_ok(['module' => $module]);
    }

    json_api_fail('Aksi tidak dikenal.', $module);
}

function api_delete($module, $data) {
    global $INCLUDES;

    if ($module === 'perangkat') {
        $idx = (int)($data['index'] ?? -1);
        $rows = json_api_read_module('perangkat');
        if (!isset($rows[$idx])) json_api_fail('Data tidak ditemukan.');
        array_splice($rows, $idx, 1);
        json_api_write_php($INCLUDES . '/perangkat.php', norm_perangkat($rows), 'includes/perangkat.php - Daftar perangkat pekon beserta jabatan');
        json_api_ok(['module' => $module]);
    }

    if ($module === 'mata_pencaharian') {
        $idx = (int)($data['index'] ?? -1);
        $dataMod = json_api_read_module('potensi');
        $list = $dataMod['mata_pencaharian'] ?? [];
        if (!isset($list[$idx])) json_api_fail('Data tidak ditemukan.');
        array_splice($list, $idx, 1);
        $dataMod['mata_pencaharian'] = $list;
        json_api_write_php($INCLUDES . '/potensi.php', norm_potensi($dataMod), 'includes/potensi.php - Potensi ekonomi dan sumber daya alam pekon');
        json_api_ok(['module' => $module]);
    }

    if ($module === 'user') {
        $id = (int)($data['id_user'] ?? 0);
        $sesi_id = (int) ($_SESSION['sesi_id'] ?? 0);
        if ($id === $sesi_id) json_api_fail('Tidak dapat menghapus akun sendiri.');
        $users = db_read('user');
        $found = false;
        foreach ($users as $i => $u) {
            if ((int)$u['id_user'] === $id) {
                array_splice($users, $i, 1);
                $found = true;
                break;
            }
        }
        if (!$found) json_api_fail('User tidak ditemukan.');
        db_write('user', $users);
        json_api_ok(['module' => $module]);
    }

    json_api_fail('Aksi tidak dikenal.', $module);
}

/* ================= HANDLER PROFILE ================= */

function api_profile($data) {
    $sesi_id = (int) ($_SESSION['sesi_id'] ?? 0);
    $users = db_read('user');
    $idx = null;
    foreach ($users as $i => $u) {
        if ((int)$u['id_user'] === $sesi_id) { $idx = $i; break; }
    }
    if ($idx === null) json_api_fail('Sesi tidak valid.');

    $nama = json_api_str($data['nama_lengkap'] ?? '', 150);
    if ($nama === '') json_api_fail('Nama lengkap wajib diisi.');
    $users[$idx]['nama_lengkap'] = $nama;

    $username = json_api_str($data['username'] ?? '', 100);
    if ($username !== '') {
        foreach ($users as $i => $u) {
            if (strtolower($u['username']) === strtolower($username) && (int)$u['id_user'] !== $sesi_id) {
                json_api_fail('Username sudah digunakan.', $username);
            }
        }
        $users[$idx]['username'] = $username;
    }

    $pass = json_api_str($data['password'] ?? '', 255);
    $confirm = json_api_str($data['password_confirm'] ?? '', 255);
    if ($pass !== '') {
        if ($pass !== $confirm) json_api_fail('Konfirmasi password tidak cocok.');
        $users[$idx]['password'] = md5($pass);
    }

    db_write('user', $users);
    $_SESSION['sesi_nama'] = $users[$idx]['nama_lengkap'];
    $_SESSION['sesi_username'] = $users[$idx]['username'];
    json_api_ok(['saved' => ['nama_lengkap' => $users[$idx]['nama_lengkap'], 'username' => $users[$idx]['username']]]);
}

/* ================= ROUTER ================= */

function api_run() {
    global $INCLUDES, $UPLOADS;

    // A08 - CSRF wajib untuk semua aksi POST (dikecualikan saat mode test)
    if (!defined('ADMIN_API_TEST')) {
        $csrfBody = $_POST['csrf'] ?? '';
        $csrfHdr  = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!csrf_verify($csrfBody) && !csrf_verify($csrfHdr)) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'CSRF token tidak valid.']);
            return;
        }
    }

    if (defined('ADMIN_API_TEST') && isset($GLOBALS['_API_RAW'])) {
        $raw = $GLOBALS['_API_RAW'];
    } else {
        $raw = file_get_contents('php://input');
    }
    $body = json_decode($raw, true);
    if (!is_array($body)) json_api_fail('Body JSON tidak valid.');

    $action = $body['action'] ?? '';
    $module = $body['module'] ?? '';
    $page   = $body['page'] ?? 1;
    $perPage = $body['perPage'] ?? 10;
    $search = $body['search'] ?? '';
    $filters = $body['filters'] ?? [];
    $data   = $body['data'] ?? [];

    try {
        switch ($action) {
            case 'list':
                api_list($module, $page, $perPage, $search, $filters);
                break;
            case 'save':
                api_save_module($module, $data);
                break;
            case 'save_row':
                api_save_row($module, $data);
                break;
            case 'delete':
                api_delete($module, $data);
                break;
            case 'profile':
                api_profile($data);
                break;
            default:
                json_api_fail('Aksi tidak dikenal.', $action);
        }
        json_api_fail('Aksi tidak menghasilkan respons.', $action);
    } catch (ApiResponse $e) {
        if (defined('ADMIN_API_TEST')) {
            $GLOBALS['_API_RESULT'] = $e->payload;
        } else {
            echo json_encode($e->payload);
        }
    }
}

if (!defined('ADMIN_API_TEST')) {
    api_run();
}