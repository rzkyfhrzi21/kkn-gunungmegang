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
    header("X-Robots-Tag: noindex, nofollow, noarchive", true);

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

/** Ambil URL embed dari nilai: terima URL mentah atau potongan iframe (<iframe src="...">). */
function json_api_embed_url($v) {
    $v = is_string($v) ? trim($v) : '';
    if (preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $v, $m)) {
        $v = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
    }
    return sanitize_url($v, 1000);
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
            'nama'     => json_api_str($k['nama'] ?? '', 150),
            'foto'     => json_api_str($k['foto'] ?? '', 255),
            'jabatan'  => json_api_str($k['jabatan'] ?? '', 150),
            'sambutan' => json_api_str($k['sambutan'] ?? '', 2000),
        ],
        'kontak' => [
            'telepon'   => json_api_str($c['telepon'] ?? '', 30),
            'maps_code' => json_api_str($c['maps_code'] ?? '', 255),
            'maps_link' => sanitize_url($c['maps_link'] ?? '', 500),
            'maps_embed' => json_api_embed_url($c['maps_embed'] ?? ''),
            'wa_desc'   => json_api_str($c['wa_desc'] ?? '', 300),
            'jam_desc'  => json_api_str($c['jam_desc'] ?? '', 300),
            'jam'       => array_values(array_map(function ($r) {
                return [
                    'hari'   => json_api_str($r['hari'] ?? '', 80),
                    'jam'    => json_api_str($r['jam'] ?? '', 80),
                    'status' => json_api_str($r['status'] ?? '', 80),
                ];
            }, is_array($c['jam'] ?? null) ? $c['jam'] : [])),
            'akses'         => json_api_str($c['akses'] ?? '', 300),
            'aspirasi_desc' => json_api_str($c['aspirasi_desc'] ?? '', 600),
            'map_subtitle'  => json_api_str($c['map_subtitle'] ?? '', 300),
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
    $mpList = [];
    foreach ($mp as $m) {
        $item = is_array($m) ? $m : ['nama' => $m];
        $nama = json_api_str($item['nama'] ?? '', 150);
        if ($nama === '') continue;
        $mpList[] = [
            'nama'        => $nama,
            'keterangan'  => json_api_str($item['keterangan'] ?? '', 150),
        ];
    }
    $kom = $raw['komoditas'] ?? [];
    $komList = [];
    foreach ($kom as $i => $k) {
        $item = is_array($k) ? $k : ['nama' => (string)$k];
        $nama = json_api_str($item['nama'] ?? '', 150);
        if ($nama === '') continue;
        $komList[] = [
            'nama'       => $nama,
            'deskripsi'  => json_api_str($item['deskripsi'] ?? '', 600),
            'nilai'      => json_api_int($item['nilai'] ?? 0),
            'satuan'     => json_api_str($item['satuan'] ?? '', 50),
            'ikon'       => json_api_str($item['ikon'] ?? '', 50),
        ];
    }
    return [
        'hero_desc'         => json_api_str($raw['hero_desc'] ?? '', 600),
        'komoditas_desc'    => json_api_str($raw['komoditas_desc'] ?? '', 600),
        'komoditas'         => $komList,
        'idm_status'        => json_api_str($raw['idm_status'] ?? '', 100),
        'idm_progress'      => max(0, min(100, json_api_int($raw['idm_progress'] ?? 0))),
        'idm_desc'          => json_api_str($raw['idm_desc'] ?? '', 600),
        'mp_desc'           => json_api_str($raw['mp_desc'] ?? '', 600),
        'mata_pencaharian'  => $mpList,
        'sosial_judul'      => json_api_str($raw['sosial_judul'] ?? '', 200),
        'sosial_par1'       => json_api_str($raw['sosial_par1'] ?? '', 1000),
        'sosial_par2'       => json_api_str($raw['sosial_par2'] ?? '', 1000),
    ];
}

/** Foto item layanan & UMKM HANYA dari upload lokal (assets/uploads/...); URL eksternal/data: ditolak. */
function json_api_layanan_umkm_foto($v) {
    $foto = json_api_str($v, 500);
    if ($foto === '') return '';
    if (strpos($foto, 'assets/uploads/') === 0) return $foto;
    return '';
}

/** Link Google Maps: hanya URL http(s) yang diterima. */
function json_api_layanan_umkm_maps($v) {
    return sanitize_url($v, 1000);
}

/** Nomor WhatsApp: simpan digit saja (tanpa +/spasi/tanda). */
function json_api_layanan_umkm_wa($v) {
    $wa = preg_replace('/\D+/', '', json_api_str($v, 30));
    return $wa;
}

function norm_layanan_umkm($raw) {
    $list = [];
    foreach ((array)($raw['daftar'] ?? []) as $item) {
        if (!is_array($item)) continue;
        $nama     = json_api_str($item['nama'] ?? '', 200);
        $kategori = json_api_str($item['kategori'] ?? '', 100);
        if ($nama === '' || $kategori === '') continue;
        $baris = [];
        foreach ((array)($item['baris'] ?? []) as $b) {
            if (!is_array($b)) continue;
            $teks = json_api_str($b['teks'] ?? '', 255);
            if ($teks === '') continue;
            $baris[] = [
                'ikon' => json_api_str($b['ikon'] ?? '', 50),
                'teks' => $teks,
            ];
        }
        $list[] = [
            'kategori' => $kategori,
            'badge'    => json_api_str($item['badge'] ?? '', 100),
            'nama'     => $nama,
            'subjudul' => json_api_str($item['subjudul'] ?? '', 200),
            'foto'     => json_api_layanan_umkm_foto($item['foto'] ?? ''),
            'baris'    => $baris,
            'maps'     => json_api_layanan_umkm_maps($item['maps'] ?? ''),
            'wa'       => json_api_layanan_umkm_wa($item['wa'] ?? ''),
        ];
    }
    return ['daftar' => $list];
}

function norm_apb_tahun($raw) {
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
        'pendapatan'  => $pendapatan,
        'belanja'     => $belanja,
        'pembiayaan'  => $pembiayaan,
    ];
}

/** Baca seluruh tahun APB; migrasi otomatis format lama (tahun tunggal). */
function apb_read_all() {
    $all = json_api_read_module('apbpekon');
    if (isset($all['tahun']) && isset($all['pendapatan'])) {
        $y = (int)$all['tahun'];
        unset($all['tahun']);
        $all = [$y => $all];
    }
    $out = [];
    foreach ((array)$all as $y => $d) {
        $y = is_numeric($y) ? (int)$y : 0;
        if ($y > 0 && is_array($d)) $out[$y] = $d;
    }
    return $out;
}

function apb_write_all($all) {
    global $INCLUDES;
    ksort($all);
    return json_api_write_php($INCLUDES . '/apbpekon.php', $all, 'includes/apbpekon.php - APB Pekon per tahun anggaran (kunci = tahun)');
}

function norm_perangkat($raw) {
    $list = [];
    foreach ($raw as $row) {
        if (!is_array($row)) continue;
        $nama    = json_api_str($row['nama'] ?? '', 150);
        $jabatan = json_api_str($row['jabatan'] ?? '', 150);
        $foto    = json_api_str($row['foto'] ?? '', 500);
        if ($nama !== '' && $jabatan !== '') {
            $list[] = ['jabatan' => $jabatan, 'nama' => $nama, 'foto' => $foto];
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

    if ($module === 'perangkat') {
        $rows = json_api_read_module('perangkat');
        $tmp = [];
        foreach ($rows as $i => $r) {
            $r['idx'] = $i;
            $foto = $r['foto'] ?? '';
            if ($foto !== '' && !file_exists(dirname(__DIR__) . '/' . $foto)) {
                $r['foto'] = '';
            }
            $tmp[] = $r;
        }
        $rows = $tmp;
        $rawRows = $rows;
    } elseif ($module === 'mata_pencaharian') {
        $dataMod = json_api_read_module('potensi');
        $list = $dataMod['mata_pencaharian'] ?? [];
        $rows = [];
        foreach ($list as $i => $m) {
            $rows[] = ['index' => $i, 'nama' => $m['nama'] ?? '', 'keterangan' => $m['keterangan'] ?? ''];
        }
        $rawRows = $rows;
    } elseif ($module === 'komoditas') {
        $dataMod = json_api_read_module('potensi');
        $list = $dataMod['komoditas'] ?? [];
        $rows = [];
        foreach ($list as $i => $k) {
            $rows[] = ['index' => $i] + $k;
        }
        $rawRows = $rows;
    } elseif ($module === 'layanan_umkm') {
        $dataMod = json_api_read_module('layanan_umkm');
        $list = $dataMod['daftar'] ?? [];
        $rows = [];
        foreach ($list as $i => $d) {
            $rows[] = ['index' => $i] + $d;
        }
        $rawRows = $rows;
    } elseif (in_array($module, ['pendapatan', 'belanja', 'pembiayaan'], true)) {
        $all = apb_read_all();
        $tahun = (int)($filters['tahun'] ?? 0);
        if (!isset($all[$tahun])) $tahun = (int)max(array_keys($all), [0]);
        unset($filters['tahun']);
        $sub  = $all[$tahun][$module] ?? [];
        $rows = [];
        foreach (json_api_sublist($module) as $key => $label) {
            $rows[] = ['key' => $key, 'label' => $label, 'nominal' => $sub[$key] ?? 0];
        }
        $rawRows = $rows;
    } elseif ($module === 'apb_tahun') {
        $all = apb_read_all();
        krsort($all);
        $rows = [];
        $no = 1;
        foreach ($all as $t => $d) {
            $rows[] = [
                'no'         => $no++,
                'tahun'      => (int)$t,
                'pendapatan' => (float)($d['pendapatan']['total'] ?? 0),
                'belanja'    => (float)($d['belanja']['total'] ?? 0),
                'pembiayaan' => (float)($d['pembiayaan']['pembiayaan_netto'] ?? 0),
            ];
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
    } elseif ($module === 'layanan_umkm') {
        $filterOptions = ['kategori' => array_values(array_unique(array_map(function ($r) {
            return $r['kategori'] ?? '';
        }, $rows)))];
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
        $flatten = function ($v) use (&$flatten) {
            $parts = [];
            foreach ((array)$v as $x) {
                if (is_array($x)) {
                    $parts[] = $flatten($x);
                } elseif ($x !== null && $x !== '') {
                    $parts[] = (string)$x;
                }
            }
            return implode(' ', $parts);
        };
        $rows = array_values(array_filter($rows, function ($r) use ($search, $flatten) {
            $hay = strtolower($flatten($r));
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

/* ================= RESOLVE LINK GOOGLE MAPS ================= */

function maps_http_get($url, $timeout = 8) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_CONNECTTIMEOUT => $timeout,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_USERAGENT      => 'PekonGunungMegang-Admin/1.0 (https://gunungmegang.id)',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_HTTPHEADER     => ['Accept: text/html,application/json;q=0.9,*/*;q=0.8'],
    ]);
    $body = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return [
        'body' => is_string($body) ? $body : '',
        'url'  => (string)($info['url'] ?? ''),
        'code' => (int)($info['http_code'] ?? 0),
    ];
}

/** Ekstrak [lat, lng] dari URL final Google Maps (pola @lat,lng, q=, ll=, daddr=). */
function maps_extract_coords($url) {
    $url = preg_replace('/#.*$/', '', $url);
    if (preg_match('/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $url, $m)) {
        return [(float)$m[1], (float)$m[2]];
    }
    if (preg_match('/[?&](?:q|ll)=([^&]+)/i', $url, $m)) {
        $q = rawurldecode($m[1]);
        if (preg_match('/(?:@|^)(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $q, $m2)) {
            return [(float)$m2[1], (float)$m2[2]];
        }
    }
    if (preg_match('/[?&]daddr=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/i', $url, $m)) {
        return [(float)$m[1], (float)$m[2]];
    }
    return null;
}

/** Reverse geocode lat,lng via OSM Nominatim -> teks alamat ringkas (id). */
function maps_reverse_geocode($lat, $lng) {
    $res = maps_http_get(
        'https://nominatim.openstreetmap.org/reverse?format=jsonv2&zoom=18&addressdetails=1&accept-language=id&lat=' . $lat . '&lon=' . $lng,
        8
    );
    if ($res['code'] !== 200 || $res['body'] === '') return '';
    $j = json_decode($res['body'], true);
    if (!is_array($j) || empty($j['address'])) return '';
    $parts = [];
    foreach (['road', 'hamlet', 'village', 'suburb', 'city_district', 'town', 'city', 'county', 'state', 'country'] as $k) {
        if (!empty($j['address'][$k])) $parts[] = $j['address'][$k];
    }
    return implode(', ', array_unique($parts));
}

function api_resolve_maps($data) {
    $link = json_api_str($data['link'] ?? '', 1000);
    if ($link === '' || !preg_match('#^https?://#i', $link)) {
        json_api_fail('Link Google Maps tidak valid.', $link);
    }
    $res = maps_http_get($link, 10);
    if ($res['code'] === 0 || $res['code'] >= 400) {
        json_api_fail('Tidak dapat membuka link Google Maps.', 'HTTP ' . $res['code']);
    }
    $finalUrl = $res['url'] !== '' ? $res['url'] : $link;
    $coords = maps_extract_coords($finalUrl);
    if (!$coords) {
        json_api_fail('Koordinat tidak ditemukan di link tersebut.', 'Pastikan link dari menu "Bagikan" di Google Maps (maps.app.goo.gl/... atau google.com/maps/place/...).');
    }
    list($lat, $lng) = $coords;
    $embed   = 'https://www.google.com/maps?q=' . $lat . ',' . $lng . '&z=16&output=embed';
    $address = maps_reverse_geocode($lat, $lng);
    json_api_ok(['lat' => $lat, 'lng' => $lng, 'embed' => $embed, 'address' => $address]);
}

/* ================= HANDLER SAVE ================= */

function api_save_module($module, $data) {
    global $INCLUDES;

    switch ($module) {
        case 'pekon':
            $old = json_api_read_module('pekon');
            // Form identitas pekon tidak mengirim kepala_pekon -> pertahankan data lama (anti-hapus)
            if (!isset($data['kepala_pekon'])) {
                $data['kepala_pekon'] = $old['kepala_pekon'] ?? ['nama' => '', 'foto' => '', 'jabatan' => ''];
            }
            $new = norm_pekon($data);
            /* bersihkan foto lama yang di-upload bila diganti */
            $oldFoto = $old['kepala_pekon']['foto'] ?? '';
            $newFoto = $new['kepala_pekon']['foto'];
            if ($oldFoto !== $newFoto && strpos($oldFoto, 'assets/uploads/') === 0) {
                @unlink(dirname($INCLUDES) . '/' . $oldFoto);
            }
            json_api_write_php($INCLUDES . '/pekon.php', $new, 'includes/pekon.php - Identitas & informasi umum Pekon Gunung Megang');
            break;

        case 'kepala_pekon':
            $old = json_api_read_module('pekon');
            $k = $data['kepala_pekon'] ?? $data;
            $nama    = json_api_str($k['nama'] ?? '', 150);
            $jabatan = json_api_str($k['jabatan'] ?? '', 150);
            $foto    = json_api_str($k['foto'] ?? '', 500);
            $sambutan = json_api_str($k['sambutan'] ?? ($old['kepala_pekon']['sambutan'] ?? ''), 2000);
            if ($nama === '' || $jabatan === '') json_api_fail('Nama dan jabatan kepala pekon wajib diisi.');
            if ($foto !== '') {
                if (strpos($foto, 'assets/uploads/') !== 0) json_api_fail('Foto harus file upload, bukan URL.', $foto);
                if (!file_exists(dirname($INCLUDES) . '/' . $foto)) json_api_fail('File foto tidak ditemukan di penyimpanan.', $foto);
            }
            $oldFoto = $old['kepala_pekon']['foto'] ?? '';
            if ($oldFoto !== $foto && strpos($oldFoto, 'assets/uploads/') === 0) {
                @unlink(dirname($INCLUDES) . '/' . $oldFoto);
            }
            $old['kepala_pekon'] = ['nama' => $nama, 'foto' => $foto, 'jabatan' => $jabatan, 'sambutan' => $sambutan];
            json_api_write_php($INCLUDES . '/pekon.php', $old, 'includes/pekon.php - Identitas & informasi umum Pekon Gunung Megang');
            $new = $old['kepala_pekon'];
            break;

        case 'demografi':
            $new = norm_demografi($data);
            json_api_write_php($INCLUDES . '/demografi.php', $new, 'includes/demografi.php - Data kependudukan, wilayah, dan batas pekon');
            break;

        case 'potensi':
            $old = json_api_read_module('potensi');
            // Form utama tidak mengirim mata_pencaharian/komoditas (dikelola lewat tabel) -> pertahankan data lama
            if (!isset($data['mata_pencaharian'])) {
                $data['mata_pencaharian'] = $old['mata_pencaharian'] ?? [];
            }
            if (!isset($data['komoditas'])) {
                $data['komoditas'] = $old['komoditas'] ?? [];
            }
            $new = norm_potensi($data);
            json_api_write_php($INCLUDES . '/potensi.php', $new, 'includes/potensi.php - Potensi ekonomi dan sumber daya alam pekon');
            break;

        case 'apb_tahun':
            $all = apb_read_all();
            $tahun = json_api_int($data['tahun'] ?? 0);
            if ($tahun < 2000 || $tahun > 2100) json_api_fail('Tahun anggaran tidak valid.', (string)$tahun);
            if (isset($all[$tahun])) json_api_fail('Tahun anggaran sudah ada.', (string)$tahun);
            $all[$tahun] = norm_apb_tahun([]);
            apb_write_all($all);
            $new = ['tahun' => $tahun];
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
        $foto    = json_api_str($data['foto'] ?? '', 500);
        if ($nama === '' || $jabatan === '') json_api_fail('Nama dan jabatan wajib diisi.');
        if ($foto !== '') {
            if (strpos($foto, 'assets/uploads/') !== 0) json_api_fail('Foto harus file upload, bukan URL.', $foto);
            if (!file_exists(dirname(__DIR__) . '/' . $foto)) json_api_fail('File foto tidak ditemukan di penyimpanan.', $foto);
        }
        $idx = isset($data['index']) && $data['index'] !== '' ? (int)$data['index'] : null;
        if ($idx !== null && isset($rows[$idx])) {
            $rows[$idx] = ['jabatan' => $jabatan, 'nama' => $nama, 'foto' => $foto];
        } else {
            $rows[] = ['jabatan' => $jabatan, 'nama' => $nama, 'foto' => $foto];
        }
        json_api_write_php($INCLUDES . '/perangkat.php', norm_perangkat($rows), 'includes/perangkat.php - Daftar perangkat pekon beserta jabatan');
        json_api_ok(['module' => $module, 'saved' => ['jabatan' => $jabatan, 'nama' => $nama, 'foto' => $foto]]);
    }

    if ($module === 'mata_pencaharian') {
        $dataMod = json_api_read_module('potensi');
        $list = $dataMod['mata_pencaharian'] ?? [];
        $val = json_api_str($data['nama'] ?? '', 150);
        if ($val === '') json_api_fail('Nama mata pencaharian wajib diisi.');
        $row = ['nama' => $val, 'keterangan' => json_api_str($data['keterangan'] ?? '', 150)];
        $idx = isset($data['index']) && $data['index'] !== '' ? (int)$data['index'] : null;
        if ($idx !== null && isset($list[$idx])) {
            $list[$idx] = $row;
        } else {
            $list[] = $row;
        }
        $dataMod['mata_pencaharian'] = $list;
        json_api_write_php($INCLUDES . '/potensi.php', norm_potensi($dataMod), 'includes/potensi.php - Potensi ekonomi dan sumber daya alam pekon');
        json_api_ok(['module' => $module, 'saved' => $row]);
    }

    if ($module === 'komoditas') {
        $dataMod = json_api_read_module('potensi');
        $list = $dataMod['komoditas'] ?? [];
        $nama = json_api_str($data['nama'] ?? '', 150);
        if ($nama === '') json_api_fail('Nama komoditas wajib diisi.');
        $row = [
            'nama'      => $nama,
            'deskripsi' => json_api_str($data['deskripsi'] ?? '', 600),
            'nilai'     => json_api_int($data['nilai'] ?? 0),
            'satuan'    => json_api_str($data['satuan'] ?? '', 50),
            'ikon'      => json_api_str($data['ikon'] ?? '', 50),
        ];
        $idx = isset($data['index']) && $data['index'] !== '' ? (int)$data['index'] : null;
        if ($idx !== null && isset($list[$idx])) {
            $list[$idx] = $row;
        } else {
            $list[] = $row;
        }
        $dataMod['komoditas'] = $list;
        json_api_write_php($INCLUDES . '/potensi.php', norm_potensi($dataMod), 'includes/potensi.php - Potensi ekonomi dan sumber daya alam pekon');
        json_api_ok(['module' => $module, 'saved' => $row]);
    }

    if ($module === 'layanan_umkm') {
        $dataMod = json_api_read_module('layanan_umkm');
        $list = $dataMod['daftar'] ?? [];
        $kategori = json_api_str($data['kategori'] ?? '', 100);
        $nama = json_api_str($data['nama'] ?? '', 200);
        if ($kategori === '' || $nama === '') json_api_fail('Kategori dan nama wajib diisi.');
        $baris = [];
        foreach ([0, 1] as $b) {
            $teks = json_api_str($data['baris' . $b . '_teks'] ?? '', 255);
            if ($teks === '') continue;
            $baris[] = [
                'ikon' => json_api_str($data['baris' . $b . '_ikon'] ?? '', 50),
                'teks' => $teks,
            ];
        }
        $row = [
            'kategori' => $kategori,
            'badge'    => json_api_str($data['badge'] ?? '', 100),
            'nama'     => $nama,
            'subjudul' => json_api_str($data['subjudul'] ?? '', 200),
            'foto'     => json_api_layanan_umkm_foto($data['foto'] ?? ''),
            'baris'    => $baris,
            'maps'     => json_api_layanan_umkm_maps($data['maps'] ?? ''),
            'wa'       => json_api_layanan_umkm_wa($data['wa'] ?? ''),
        ];
        $idx = isset($data['index']) && $data['index'] !== '' ? (int)$data['index'] : null;
        if ($idx !== null && isset($list[$idx])) {
            $list[$idx] = $row;
        } else {
            $list[] = $row;
        }
        $dataMod['daftar'] = $list;
        json_api_write_php($INCLUDES . '/layanan_umkm.php', norm_layanan_umkm($dataMod), 'includes/layanan_umkm.php - Layanan & UMKM pekon');
        json_api_ok(['module' => $module, 'saved' => $row]);
    }

    if ($module === 'pendapatan' || $module === 'belanja' || $module === 'pembiayaan') {
        $key = $data['key'] ?? '';
        $labels = json_api_sublist($module);
        if (!isset($labels[$key])) json_api_fail('Pos tidak dikenal.', $key);
        $all = apb_read_all();
        $tahun = (int)($data['tahun'] ?? 0);
        if (!isset($all[$tahun])) json_api_fail('Tahun anggaran tidak ditemukan.', (string)$tahun);
        $all[$tahun][$module][$key] = json_api_float($data['nominal'] ?? 0);
        $all[$tahun] = norm_apb_tahun($all[$tahun]);
        apb_write_all($all);
        json_api_ok(['module' => $module, 'saved' => ['key' => $key, 'nominal' => $all[$tahun][$module][$key]]]);
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

    if ($module === 'komoditas') {
        $idx = (int)($data['index'] ?? -1);
        $dataMod = json_api_read_module('potensi');
        $list = $dataMod['komoditas'] ?? [];
        if (!isset($list[$idx])) json_api_fail('Data tidak ditemukan.');
        array_splice($list, $idx, 1);
        $dataMod['komoditas'] = $list;
        json_api_write_php($INCLUDES . '/potensi.php', norm_potensi($dataMod), 'includes/potensi.php - Potensi ekonomi dan sumber daya alam pekon');
        json_api_ok(['module' => $module]);
    }

    if ($module === 'layanan_umkm') {
        $idx = (int)($data['index'] ?? -1);
        $dataMod = json_api_read_module('layanan_umkm');
        $list = $dataMod['daftar'] ?? [];
        if (!isset($list[$idx])) json_api_fail('Data tidak ditemukan.');
        array_splice($list, $idx, 1);
        $dataMod['daftar'] = $list;
        json_api_write_php($INCLUDES . '/layanan_umkm.php', norm_layanan_umkm($dataMod), 'includes/layanan_umkm.php - Layanan & UMKM pekon');
        json_api_ok(['module' => $module]);
    }

    if ($module === 'apb_tahun') {
        $all = apb_read_all();
        $tahun = (int)($data['tahun'] ?? 0);
        if (!isset($all[$tahun])) json_api_fail('Tahun anggaran tidak ditemukan.');
        if (count($all) <= 1) json_api_fail('Tidak dapat menghapus tahun anggaran terakhir.');
        unset($all[$tahun]);
        apb_write_all($all);
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
        $passLama = json_api_str($data['password_lama'] ?? '', 255);
        if ($passLama === '' || !password_check($passLama, $users[$idx]['password'] ?? '')) {
            json_api_fail('Password lama salah.', 'Masukkan password lama yang benar.');
        }
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
            case 'resolve_maps':
                api_resolve_maps($body);
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