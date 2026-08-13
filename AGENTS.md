---
name: ai-master-rules
description: >
  File induk aturan AI untuk proyek.
  Seluruh skill telah dipecah ke file masing-masing di folder @agents/AI-SKILLS/
  agar lebih modular dan mudah di-maintain.
---

# DAFTAR ATURAN & SKILL BACA AI

Berikut adalah aturan prioritas pembacaan file yang wajib dipatuhi oleh AI saat memulai sesi atau melakukan pengerjaan.

## 1. Aturan Pembacaan Folder Skill (`@agents/AI-SKILLS/`)

AI **DILARANG KERAS** membaca secara otomatis (*auto-read*) seluruh file di folder-folder skill raksasa berikut:
- 🚫 `@agents/AI-SKILLS/ECC_SKILLS`
- 🚫 `@agents/AI-SKILLS/SUPERPOWERS`
- 🚫 `@agents/AI-SKILLS/TASTE_SKILLS`
- 🚫 `@agents/AI-SKILLS/UIUX_SKILLS`
- 🚫 `@agents/AI-SKILLS/IMPECCABLE_SKILLS`
- 🚫 `@agents/AI-SKILLS/AGENT_SKILLS`

> **⚠️ ATURAN MUTLAK:**
> AI **HANYA DIIZINKAN** membaca file skill dari folder-folder di atas **JIKA DAN HANYA JIKA** pengguna (*user*) menyebutkan atau meminta skill tersebut secara **EKSPLISIT** dalam pesan prompt.

---

### Ringkasan Fungsi Folder Skill (Referensi Pembacaan Eksplisit):

- **📁 PERSONA_SKILLS**: Kumpulan persona dan gaya komunikasi AI.
  - `CAVEMAN_SKILL.md`: Mode komunikasi sangat singkat dan padat (hemat token).
  - `PONYTAIL_SKILL.md`: Mode "Senior Dev Malas", memprioritaskan solusi paling simpel, bersih, dan menolak *over-engineering* (YAGNI).
  - `KOMENTAR_ORANG_TUA.md`: (Pengecualian) Jangan dibaca/diterapkan kecuali diminta secara eksplisit oleh pengguna.

- **📁 TOOL_SKILLS**: Panduan integrasi dan penggunaan alat (tools/MCP) pendukung (dibaca hanya file spesifik saat tool digunakan).
  - `CODEBASE_MEMORY.md`: Digunakan saat membutuhkan pemetaan struktur repository.
  - `CONTEXT7.md`: Digunakan untuk mencari dokumentasi resmi teknologi utama proyek.
  - `FIRECRAWL.md`: Digunakan untuk *web scraping* atau mencari dokumentasi dari sumber luar.
  - `RTK_SKILL.md`: Panduan inisialisasi Repository Toolkit (RTK) demi optimasi token terminal.
  - `BROWSER_USE_SKILL.md`: Panduan penggunaan library `browser-use` untuk otomasi browser.
  - `GRAPHIFY_SKILL.md`: Panduan `graphify` untuk Knowledge Graph (beserta dokumentasi teknis di `TOOL_SKILLS/graphify/`).
  - `NEEDMCP.md`: Panduan NeedMCP MCP untuk pengembangan UI berbasis theme-driven design tokens (style locking, komponen, layout, wireframe).

- **📁 WORKFLOW_SKILLS**: Aturan standar operasional, alur kerja, dan lingkungan kerja AI.
  - `AGENTS-GENERAL.md` / `WORKFLOW_MODE.md` / `AI_SETUP_MACHINE.md`: Aturan alur kerja & environment.
  - `CICD_SSH_SKILL.md`: Prosedur wajib untuk setup otomatis CI/CD menggunakan SSH.

- **📁 SUPERPOWERS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - `SUPERPOWERS_SKILLS.md`: Kumpulan *skill* modular untuk *brainstorming*, *planning*, dan *debugging*.

- **📁 AGENT_SKILLS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - Kumpulan *skill* engineering (TDD, CI/CD, Code Review, SDD, Debugging, Refactoring, dsb).

- **📁 TASTE_SKILLS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - Kumpulan *skill* (`brandkit`, `minimalist-skill`, dll) untuk merancang antarmuka (UI/UX) premium.

- **📁 ECC_SKILLS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - Kumpulan masif ratusan *skill* modular (frontend, backend, security, DevOps).

- **📁 IMPECCABLE_SKILLS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - *Skill* sistem desain (*design tokens*, tipografi, spasi) untuk konsistensi antarmuka.

- **📁 UIUX_SKILLS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - Koleksi skill UI/UX lengkap (84 gaya visual, 192 palet warna, 74 font pairing, 98 UX guide, 16 GSAP preset).

---

## 2. Aturan Pembacaan Dokumen Panduan (`docs/`)
AI **wajib** membaca panduan dari file dan subfolder di dalam `docs/` berikut:
- `docs/BRIEF.txt`
- `docs/CANVAS.md`
- `docs/PRD.md`
- `docs/DESIGN.md`
- Folder `docs/BRIEF KOMPONENT/` (Semua file di dalamnya)
- Folder `docs/BRIEF MODUL/` (Semua file di dalamnya)

**DILARANG KERAS** membaca subfolder berikut di dalam `docs/`:
1. `docs/BRIEF SAAT DEPLOY/` (Folder brief saat deploy - JANGAN DIBACA)
2. `docs/PROJECT LAIN (CONTOH)/` (Folder project lain - JANGAN DIBACA)
3. `docs/DATA KLIEN/` (Folder data klien - JANGAN DIBACA)

## 3. Aturan Pembacaan README Utama
- **DILARANG KERAS** membaca file `README.md` yang berada di root project karena itu hanya panduan untuk instalasi & deployment saja (JANGAN DIBACA). Gunakan dokumen panduan `docs/` yang diperbolehkan di atas untuk referensi teknis.

---

# INISIALISASI PROYEK BARU (AUTO-SETUP)

Jika AI mendeteksi bahwa ini adalah proyek atau *workspace* baru, AI **wajib** secara proaktif menjalankan (atau mengingatkan pengguna untuk menjalankan) langkah-langkah inisialisasi *tools* berikut:
1. **RTK (Repository Toolkit):** Jalankan perintah `./@agents/RTK/rtk.exe init` (atau sesuaikan path rtk) di terminal untuk menghasilkan folder `.rtk` (berisi konfigurasi `filters.toml`) dan folder `rules` (berisi aturan `antigravity-rtk-rules.md`).
2. **Context7:** Pastikan integrasi sudah terpasang dengan menjalankan perintah `npx ctx7 setup` untuk autentikasi dan pembuatan *rules* otomatis di dalam agen.
3. **Codebase Memory MCP:** Lakukan pemetaan struktur proyek secara otomatis. Jika server MCP sudah tersambung, AI harus segera memindai (melakukan aksi *"Index this project"*) agar grafik pengetahuan kode terbentuk di memori.
4. **Firecrawl MCP:** Ingatkan pengguna untuk memastikan variabel lingkungan `FIRECRAWL_API_KEY` sudah terpasang jika proyek membutuhkan fitur pencarian web/*scraping* lanjutan.
5. **Graphify:** Jika proyek membutuhkan pemetaan pengetahuan multimodal (dokumen, diagram, riset, & kode), jalankan perintah `graphify .` di terminal root proyek untuk menghasilkan folder `graphify-out/`.

---

# MANAJEMEN FILE/SCRIPT SEMENTARA
- Simpan semua file eksekusi, script sementara (temporary scripts), atau file uji coba/scratch di dalam folder `Zzz/` yang ada di root proyek.
- DILARANG mengotori root directory atau folder lain dengan script sekali pakai.

---

# ENVIRONMENT TERMINAL USER

- User menggunakan **CMD (Command Prompt) mode Administrator**, BUKAN PowerShell.
- AI **wajib** memberikan perintah dalam sintaks CMD, bukan PowerShell.
- Contoh perbedaan:
  - PowerShell: `New-Item`, `Remove-Item`, `Move-Item`
  - CMD: `mkdir`, `del`, `move`, `rmdir /s /q`

---

# ATURAN REFACTORING & MODIFIKASI KODE LAMA

- **DILARANG KERAS** mengubah, merombak (refactor), atau menulis ulang seluruh kode pada file yang sudah ada secara tiba-tiba hanya karena membaca aturan di `AGENTS.md` atau file skill lainnya.
- Aturan penulisan kode **hanya berlaku** untuk kode baru yang sedang ditulis atau fitur baru yang sedang ditambahkan.
- Pengecualian: AI hanya diizinkan melakukan refactor pada kode lama **JIKA DAN HANYA JIKA** pengguna secara eksplisit menginstruksikan atau meminta refactor tersebut.

**Peringatan (setelah baca standar ini):**
- Standar di atas **bukan** undangan untuk rename/refactor file yang sudah ada.
- Kalau file lama belum ikut standar (nama beda, logic di page, modal legacy, dll.) — **biarkan**. Hanya file/fitur **baru** yang wajib ikut.
- Jangan sentuh kode existing kecuali user **minta eksplisit**.
