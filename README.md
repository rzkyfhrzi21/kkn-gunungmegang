<div align="center">

<img src="docs/Lambang_Kabupaten_Tanggamus.png" alt="Lambang Pekon Gunung Megang" width="96"/>

# Profil Pekon Gunung Megang

**Website profil desa digital berbasis PHP untuk Pekon Gunung Megang, Kabupaten Tanggamus, Lampung.**  
Dikembangkan sebagai tugas akhir mata kuliah *Pemrograman Lanjut* — IIB Darmajaya.

[![PHP](https://img.shields.io/badge/PHP-8%2B-8892BF?logo=php&logoColor=white)](https://www.php.net/)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-CDN-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-Academic-green)](#)

</div>

---

## Tentang Proyek

Website ini menjadi jembatan antara pekon (desa) dan warganya melalui platform digital yang transparan dan mudah diakses. Seluruh informasi pekon — mulai dari profil, keuangan anggaran, potensi ekonomi, hingga katalog layanan dan UMKM — tersaji dalam satu tempat yang bisa diakses siapa saja dari perangkat apapun.

Sistem dibangun **tanpa database SQL**. Seluruh data disimpan dalam file JSON dan PHP flat-file, sehingga mudah dihosting di shared hosting biasa tanpa konfigurasi tambahan.

---

## Fitur Unggulan

### 🌐 Halaman Publik (Frontend)

| Halaman | Deskripsi |
|---------|-----------|
| **Beranda** | Sambutan, info singkat desa, dan navigasi cepat ke seluruh konten |
| **Profil Desa** | Identitas pekon, visi-misi, kepala pekon, peta lokasi (Google Maps embed) |
| **Demografi & Wilayah** | Data kependudukan, batas wilayah, dan statistik demografis |
| **Potensi Ekonomi** | Komoditas unggulan, mata pencaharian warga, dan IDM (Indeks Desa Membangun) |
| **Pemerintahan** | Struktur aparat desa, jabatan, dan lembaga kemasyarakatan |
| **Layanan & UMKM** | Katalog layanan publik dan produk UMKM lokal dengan pencarian & filter kategori |
| **APB Pekon** | Transparansi anggaran desa multi-tahun (pendapatan, belanja, pembiayaan) |
| **Kontak & Aspirasi** | Informasi kontak resmi dan formulir aspirasi publik dengan rate limiting |

### 🎨 Tampilan & UX Frontend

- **Desain responsif** — mobile-first dengan Tailwind CSS
- **Material Symbols** & Google Fonts (Plus Jakarta Sans, Playfair Display)
- **AJAX Pagination** di halaman Layanan & UMKM — pilih tampil 9 / 24 / 50 item per halaman, tanpa reload
- **Skeleton loading** saat data AJAX dimuat
- **Lightbox preview foto** — klik gambar untuk preview modal besar, tutup dengan tombol × atau tombol `Esc`
- **Dark Mode** di panel admin (toggle sidebar)
- **SEO-ready** — meta description, Open Graph, sitemap XML, robots.txt

### 🔐 Panel Admin (`/dashboard`)

Sistem manajemen konten berbasis web untuk semua data pekon.

#### Manajemen Data
- ✏️ **CRUD lengkap** untuk semua modul: Profil, Demografi, Potensi, Layanan & UMKM, APB Pekon, Aparat Desa
- 📸 **Upload foto otomatis WebP** — foto dikompresi ke format WebP kualitas 80 saat upload, hemat storage
- 🗺️ **Integrasi Google Maps** — tempel link "Bagikan" dari Google Maps, koordinat & embed URL terisi otomatis
- 📅 **APB Pekon multi-tahun** — kelola anggaran beberapa tahun sekaligus, halaman publik otomatis menampilkan tahun terbaru
- 🔎 **Select2 icon picker** — pemilihan ikon Material Symbols dengan preview visual langsung di dropdown

#### Tabel & Filter
- Pagination server-side dengan pencarian real-time
- Filter per kategori (misalnya filter UMKM berdasarkan jenis usaha)
- Nomor urut baris otomatis
- Notifikasi toast (sukses/peringatan/error) dengan auto-hide

#### Keamanan (OWASP Top 10)
- 🔒 Login dengan proteksi brute-force — akun terkunci otomatis setelah 5 percobaan gagal
- 🛡️ CSRF token di semua form POST
- 🧹 Sanitasi XSS — semua input di-strip dari tag HTML berbahaya sebelum disimpan
- 🚫 Upload foto dibatasi hanya ke direktori `assets/uploads/` (whitelist path)
- 📵 Rate limiting aspirasi — maks 5 laporan/jam per IP
- 🔑 Password di-hash bcrypt (upgrade otomatis dari MD5 legacy saat login)
- 🚷 `.htaccess` memblokir akses langsung ke `includes/`, `functions/`, `db/`, `Zzz/`
- 🔐 Header keamanan: CSP, X-Frame-Options, X-Content-Type-Options, Permissions-Policy, HSTS

### 🗂️ Struktur Proyek

```
kkn-gunungmegang/
│
├── views/                      ← Seluruh file tampilan (terorganisir)
│   ├── landing/                ← Halaman publik
│   │   ├── index.php           ← Beranda
│   │   ├── layanan-umkm.php
│   │   ├── profil-desa.php
│   │   ├── pemerintahan.php
│   │   ├── potensi-ekonomi.php
│   │   ├── apbpekon.php
│   │   └── kontak.php
│   └── dashboard/
│       └── admin.php           ← Controller utama panel admin
│
├── dashboard/                  ← Aset & halaman modul admin
│   ├── admin-pages/            ← Modul per fitur (CRUD UI)
│   └── assets/                 ← CSS, JS, font admin (Mazer template)
│
├── components/                 ← Komponen bersama (header, footer, section)
│   └── sections/               ← Section konten tiap halaman
│
├── functions/                  ← Logika backend
│   ├── ajax/
│   │   └── layanan-umkm-data.php  ← Endpoint AJAX publik (read-only)
│   ├── tests/                  ← Unit & security tests (CLI only)
│   ├── config.php              ← Konstanta & JSON database layer
│   └── security.php            ← CSRF, rate-limit, sanitasi
│
├── admin/                      ← API endpoint & upload handler
│   ├── api.php                 ← Dispatcher CRUD semua modul
│   └── upload.php              ← Handler upload + konversi WebP
│
├── auth/                       ← Autentikasi
│   ├── login.php
│   └── logout.php
│
├── includes/                   ← Data flat-file PHP (sumber kebenaran data)
├── db/json/                    ← JSON database (user, aspirasi, security log)
├── assets/                     ← Aset publik (gambar, JS utils)
│
├── .htaccess                   ← Routing URL + header keamanan
├── sitemap.xml
└── robots.txt
```

### 🧪 Unit Testing

Tersedia suite test otomatis untuk seluruh modul backend, dijalankan via CLI:

```bash
# Jalankan semua test sekaligus
php functions/tests/run_all.php

# Atau per modul
php functions/tests/test_layanan_umkm.php
php functions/tests/test_auth.php
php functions/tests/test_pekon.php
```

Test mencakup: validasi input, sanitasi XSS, batas panjang field, keamanan foto/maps/WA, CSRF, rate-limit, dan CRUD lengkap setiap modul. Data asli otomatis di-snapshot sebelum test dan di-restore setelahnya — aman dijalankan berulang kali.

---

## Teknologi

| Kategori | Teknologi |
|----------|-----------|
| **Backend** | PHP 8+ (no MySQL — JSON flat-file database) |
| **Frontend** | Tailwind CSS (CDN), Material Symbols, Google Fonts |
| **Admin UI** | [Mazer Admin Template](https://github.com/zuramai/mazer) (Bootstrap 5) |
| **Komponen JS** | Select2, SweetAlert2, Font Awesome |
| **Server** | Apache + mod_rewrite (Laragon / cPanel shared hosting) |
| **Storage** | File JSON + PHP include (tanpa MySQL) |

---

## Instalasi & Menjalankan Lokal

### Prasyarat

- **PHP 8.0+** dengan ekstensi: `GD` (untuk konversi WebP), `json`, `session`
- **Apache** dengan `mod_rewrite` aktif
- Direkomendasikan: **[Laragon](https://laragon.org/)** (Windows) atau XAMPP/MAMP

### Langkah Instalasi

**1. Clone repositori**

```bash
git clone https://github.com/<username>/kkn-gunungmegang.git
```

Atau ekstrak ZIP ke folder `www/` (Laragon) / `htdocs/` (XAMPP).

**2. Pastikan `mod_rewrite` aktif**

Di Laragon, `mod_rewrite` sudah aktif secara default.  
Di XAMPP, uncomment baris berikut di `httpd.conf`:
```
LoadModule rewrite_module modules/mod_rewrite.so
```

**3. Akses via browser**

```
http://localhost/kkn-gunungmegang
```

Panel admin:
```
http://localhost/kkn-gunungmegang/dashboard/admin
```

> **Akun default admin:** username `admin81` — lihat `db/json/user.json` untuk detail.

**4. Pastikan folder writable (untuk upload foto)**

```bash
chmod 755 assets/uploads/
```

Di Windows/Laragon biasanya tidak diperlukan konfigurasi tambahan.

### Konfigurasi

Edit `functions/config.php` untuk mengubah nama website, pengembang, dan info kontak:

```php
define("NAMA_WEB",     "Profil Pekon Gunung Megang");
define("NAMA_LENGKAP", "Nama Pengembang");
define("NAMA_KAMPUS",  "Nama Institusi");
define("NO_WA",        "08xxxxxxxxxx");
define("IG",           "username_ig");
```

### Deploy ke Shared Hosting

1. Upload seluruh isi folder ke `public_html/` via FTP
2. Pastikan **PHP 8+** tersedia di hosting (cek di cPanel → PHP Selector)
3. Verifikasi `.htaccess` tidak terblokir (mode `AllowOverride All`)
4. Upload folder `db/json/` — pastikan tidak di-overwrite saat update (sudah dikecualikan di GitHub Actions workflow)

> ⚠️ File `db/json/*.json` berisi data produksi. Backup sebelum deploy ulang.

---

<div align="center">

Dikembangkan dengan ❤️ oleh **Rafif Rhamdo Buay Bulan**  
IIB Darmajaya · Pemrograman Lanjut · 2026  
[@ubirayap](https://www.instagram.com/ubirayap)

</div>
