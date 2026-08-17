<?php
// components/sections/layanan-umkm.php
// Halaman Layanan & UMKM — AJAX pagination
if (!isset($pekon)) return;

$layKategoris = [];
$_raw = @include dirname(__DIR__, 2) . '/includes/layanan_umkm.php';
if (is_array($_raw)) {
  $layKategoris = array_values(array_unique(array_filter(
    array_map(fn($i) => $i['kategori'] ?? '', $_raw['daftar'] ?? [])
  )));
}

$layHeroSub   = 'Layanan & UMKM';
$layHeroJudul = 'Eksplorasi Layanan, UMKM & Fasilitas Pekon';
$layHeroDesc  = 'Temukan ragam potensi lokal, produk unggulan UMKM, serta fasilitas layanan masyarakat yang tersedia di Pekon Gunung Megang.';
?>
<style>
  .scrollbar-hide::-webkit-scrollbar {
    display: none
  }

  .scrollbar-hide {
    scrollbar-width: none
  }

  /* ---- lightbox overlay ---- */
  .lay-photo-wrap {
    position: relative;
    overflow: hidden;
    cursor: pointer;
  }

  .lay-photo-wrap .lay-zoom-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0);
    transition: background .25s ease;
    pointer-events: none;
  }

  .lay-photo-wrap:hover .lay-zoom-overlay {
    background: rgba(0, 0, 0, .35);
  }

  .lay-photo-wrap .lay-zoom-overlay span {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .15);
    backdrop-filter: blur(4px);
    border: 1.5px solid rgba(255, 255, 255, .5);
    color: #fff;
    font-size: 24px;
    opacity: 0;
    transform: scale(.8);
    transition: opacity .25s ease, transform .25s ease;
  }

  .lay-photo-wrap:hover .lay-zoom-overlay span {
    opacity: 1;
    transform: scale(1);
  }

  /* ---- lightbox modal ---- */
  #lay-lightbox {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9990;
    background: rgba(0, 0, 0, .82);
    backdrop-filter: blur(6px);
    align-items: center;
    justify-content: center;
    animation: layLbIn .2s ease;
  }

  #lay-lightbox.active {
    display: flex;
  }

  @keyframes layLbIn {
    from {
      opacity: 0
    }

    to {
      opacity: 1
    }
  }

  #lay-lightbox-img {
    max-width: min(94vw, 1100px);
    max-height: 88vh;
    object-fit: contain;
    border-radius: 12px;
    box-shadow: 0 24px 80px rgba(0, 0, 0, .6);
    display: block;
    animation: layLbImgIn .25s ease;
  }

  @keyframes layLbImgIn {
    from {
      transform: scale(.92);
      opacity: 0
    }

    to {
      transform: scale(1);
      opacity: 1
    }
  }

  #lay-lightbox-close {
    position: fixed;
    top: 20px;
    right: 20px;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .15);
    backdrop-filter: blur(8px);
    border: 1.5px solid rgba(255, 255, 255, .3);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    transition: background .2s ease, transform .2s ease;
    z-index: 9991;
  }

  #lay-lightbox-close:hover {
    background: rgba(255, 255, 255, .3);
    transform: rotate(90deg);
  }

  #lay-lightbox-spinner {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
  }

  #lay-lightbox-spinner::after {
    content: '';
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, .2);
    border-top-color: #fff;
    animation: layLbSpin .7s linear infinite;
  }

  @keyframes layLbSpin {
    to {
      transform: rotate(360deg)
    }
  }

  /* ---- skeleton card ---- */
  .lay-card-skeleton {
    border-radius: .75rem;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    background: #fff;
    animation: laySkPulse 1.4s ease-in-out infinite;
  }

  @keyframes laySkPulse {

    0%,
    100% {
      opacity: 1
    }

    50% {
      opacity: .5
    }
  }

  .lay-sk-img {
    height: 224px;
    background: #e2e8f0;
  }

  .lay-sk-body {
    padding: 1.5rem;
  }

  .lay-sk-line {
    height: .9rem;
    border-radius: 4px;
    background: #e2e8f0;
    margin-bottom: .6rem;
  }

  /* ---- pagination bar ---- */
  #lay-pagbar {
    margin-top: 3.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .lay-pag-info {
    font-size: 1rem;
    color: #64748b;
    font-weight: 500;
  }

  .lay-pag-btns {
    display: flex;
    align-items: center;
    gap: .5rem;
  }

  .lay-pag-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 36px;
    min-width: 36px;
    padding: 0 .65rem;
    border-radius: .5rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #334155;
    font-size: .85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all .18s ease;
    user-select: none;
  }

  .lay-pag-btn:hover:not(:disabled) {
    background: #f1f5f9;
    border-color: #94a3b8;
  }

  .lay-pag-btn.active {
    background: var(--color-primary, #0a3d2f);
    color: #fff;
    border-color: var(--color-primary, #0a3d2f);
  }

  .lay-pag-btn:disabled {
    opacity: .4;
    cursor: not-allowed;
  }

  .lay-perpage-wrap {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: 1rem;
    color: #475569;
    font-weight: 500;
  }

  .lay-perpage-select {
    border: 1px solid #e2e8f0;
    border-radius: .4rem;
    padding: .3rem .6rem;
    font-size: 1rem;
    background: #fff;
    color: #334155;
    cursor: pointer;
    font-weight: 500;
  }

  /* state error/empty */
  #lay-state {
    padding: 4rem 0;
    text-align: center;
    color: #94a3b8;
    font-size: .9rem;
    display: none;
  }
</style>

<div class="flex flex-col w-full bg-white text-on-surface min-h-screen">
  <!-- Header -->
  <section class="w-full pt-16 pb-8 px-gutter max-w-container-max mx-auto">
    <div class="flex items-center gap-3 mb-6">
      <div class="h-px w-8 bg-secondary"></div>
      <span class="text-[12px] font-bold tracking-[0.2em] uppercase text-secondary"><?= htmlspecialchars($layHeroSub) ?></span>
    </div>
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 mb-12">
      <div class="max-w-2xl">
        <h1 class="font-display text-4xl lg:text-5xl text-on-surface leading-tight mb-4" style="font-family:'Playfair Display',serif;"><?= htmlspecialchars($layHeroJudul) ?></h1>
        <p class="text-body-lg text-slate-text-muted"><?= htmlspecialchars($layHeroDesc) ?></p>
      </div>
      <!-- Search -->
      <div class="w-full lg:w-96 relative">
        <input id="cari-layanan" class="w-full bg-surface border border-border-neutral text-on-surface placeholder:text-slate-text-muted/60 rounded-full py-4 pl-6 pr-12 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors font-body-md shadow-sm" placeholder="Cari layanan atau produk..." type="text" />
        <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-primary hover:text-primary-container transition-colors" aria-label="Cari">
          <span class="material-symbols-outlined text-[24px]">search</span>
        </button>
      </div>
    </div>
    <!-- Category Tabs -->
    <div class="flex items-center gap-3 overflow-x-auto pb-4 scrollbar-hide border-b border-border-neutral" id="tab-layanan">
      <button type="button" data-kategori="" class="whitespace-nowrap px-6 py-2.5 rounded-full bg-primary text-white text-label-sm font-bold transition-transform hover:scale-105">Semua Kategori</button>
      <?php foreach ($layKategoris as $kat): ?>
        <button type="button" data-kategori="<?= htmlspecialchars($kat) ?>" class="whitespace-nowrap px-6 py-2.5 rounded-full border border-border-neutral text-slate-text-muted text-label-sm hover:bg-surface-container hover:border-primary transition-all"><?= htmlspecialchars($kat) ?></button>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Grid + Pagination -->
  <section class="w-full pb-section-padding px-gutter max-w-container-max mx-auto">
    <!-- Skeleton saat loading -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="lay-skeletons">
      <?php for ($s = 0; $s < 6; $s++): ?>
        <div class="lay-card-skeleton">
          <div class="lay-sk-img"></div>
          <div class="lay-sk-body">
            <div class="lay-sk-line" style="width:40%"></div>
            <div class="lay-sk-line" style="width:75%"></div>
            <div class="lay-sk-line" style="width:55%"></div>
          </div>
        </div>
      <?php endfor; ?>
    </div>

    <!-- State kosong / error -->
    <div id="lay-state">
      <span class="material-symbols-outlined text-[40px] block mb-2">inbox</span>
      <span id="lay-state-text">Tidak ada data ditemukan.</span>
    </div>

    <!-- Grid konten -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="grid-layanan" style="display:none"></div>

    <!-- Pagination bar -->
    <div id="lay-pagbar" style="display:none">
      <!-- Kiri: info + per-page -->
      <div class="flex flex-wrap items-center gap-4">
        <div class="lay-pag-info" id="lay-pag-info">Menampilkan 1–9 dari 0 item</div>
        <div class="lay-perpage-wrap">
          <label for="lay-perpage">Tampil:</label>
          <select id="lay-perpage" class="lay-perpage-select">
            <option value="9">9</option>
            <option value="24">24</option>
            <option value="50">50</option>
          </select>
          <span>per halaman</span>
        </div>
      </div>
      <!-- Kanan: prev / nomor / next -->
      <div class="lay-pag-btns" id="lay-pag-btns"></div>
    </div>
  </section>
</div>

<!-- Lightbox -->
<div id="lay-lightbox" role="dialog" aria-modal="true" aria-label="Preview foto">
  <div id="lay-lightbox-spinner"></div>
  <img id="lay-lightbox-img" alt="Preview foto" style="display:none">
  <button id="lay-lightbox-close" aria-label="Tutup">
    <span class="material-symbols-outlined" style="font-size:22px;line-height:1">close</span>
  </button>
</div>

<script>
  (function() {
    /* =====================================================
     * CONFIG
     * ===================================================== */
    var API_URL = 'functions/ajax/layanan-umkm-data.php';
    var WA_SVG = '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>';

    /* =====================================================
     * STATE
     * ===================================================== */
    var allItems = []; /* semua item dari API */
    var filtered = []; /* setelah filter kategori + search */
    var activeCat = '';
    var curPage = 1;
    var perPage = 9;

    /* =====================================================
     * DOM REFS
     * ===================================================== */
    var grid = document.getElementById('grid-layanan');
    var skeletons = document.getElementById('lay-skeletons');
    var stateEl = document.getElementById('lay-state');
    var stateTxt = document.getElementById('lay-state-text');
    var pagBar = document.getElementById('lay-pagbar');
    var pagInfo = document.getElementById('lay-pag-info');
    var pagBtns = document.getElementById('lay-pag-btns');
    var searchEl = document.getElementById('cari-layanan');
    var perPageEl = document.getElementById('lay-perpage');
    var tabs = Array.prototype.slice.call(document.querySelectorAll('#tab-layanan button'));

    /* =====================================================
     * HELPERS
     * ===================================================== */
    function esc(s) {
      return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function waHref(wa) {
      var w = String(wa || '').replace(/\D+/g, '');
      if (!w) return '';
      if (w.charAt(0) === '0') w = '62' + w.slice(1);
      return 'https://wa.me/' + w;
    }

    function haystack(item) {
      return [item.kategori, item.badge, item.nama, item.subjudul,
        (item.baris || []).map(function(b) {
          return b.teks;
        }).join(' ')
      ].join(' ').toLowerCase();
    }

    /* =====================================================
     * RENDER CARD
     * ===================================================== */
    function renderCard(item) {
      var foto = item.foto || '';
      var maps = item.maps || '';
      var wa = waHref(item.wa);
      var baris = (item.baris || []).map(function(b) {
        return '<div class="flex items-center gap-2">' +
          '<span class="material-symbols-outlined text-[18px]">' + esc(b.ikon) + '</span>' +
          '<span>' + esc(b.teks) + '</span></div>';
      }).join('');
      var fotoHtml = foto ?
        '<div class="lay-photo-wrap w-full h-full" data-preview-front="' + esc(foto) + '" role="button">' +
        '<img alt="' + esc(item.nama) + '" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="' + esc(foto) + '" loading="lazy">' +
        '<div class="lay-zoom-overlay"><span class="material-symbols-outlined">zoom_in</span></div></div>' :
        '<div class="w-full h-full flex items-center justify-center" style="background-image:linear-gradient(135deg,#0b3b4a 0%,#0ea5a4 60%,#0b3b4a 100%)">' +
        '<span class="material-symbols-outlined text-[48px] text-white/70">storefront</span></div>';
      var btns = '';
      if (maps) btns += '<a href="' + esc(maps) + '" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 py-3 bg-primary text-white rounded hover:bg-primary-container transition-colors font-label-sm"><span class="material-symbols-outlined text-[18px]">location_on</span>Google Maps</a>';
      if (wa) btns += '<a href="' + esc(wa) + '" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 py-3 bg-[#25D366] text-white rounded hover:opacity-90 transition-opacity font-label-sm">' + WA_SVG + 'WhatsApp</a>';
      return '<div class="group flex flex-col bg-surface border border-border-neutral rounded-xl overflow-hidden hover:border-primary/50 transition-all duration-300 shadow-md">' +
        '<div class="relative h-56 w-full overflow-hidden">' + fotoHtml +
        '<div class="absolute top-4 left-4 bg-white/90 px-3 py-1 rounded border border-border-neutral text-[10px] font-bold text-primary tracking-wider uppercase">' + esc(item.badge || item.kategori) + '</div>' +
        '</div><div class="p-6 flex flex-col flex-grow">' +
        '<h3 class="text-2xl text-on-surface mb-1" style="font-family:\'Playfair Display\',serif;">' + esc(item.nama) + '</h3>' +
        '<p class="text-secondary text-sm font-body-md mb-4">' + esc(item.subjudul) + '</p>' +
        '<div class="flex flex-col gap-2 mb-6 text-slate-text-muted text-sm font-body-md">' + baris + '</div>' +
        '<div class="mt-auto grid grid-cols-2 gap-3">' + btns + '</div>' +
        '</div></div>';
    }

    /* =====================================================
     * FILTER + SLICE
     * ===================================================== */
    function applyFilter() {
      var q = searchEl ? searchEl.value.trim().toLowerCase() : '';
      filtered = allItems.filter(function(item) {
        var okCat = !activeCat || item.kategori === activeCat;
        var okQ = !q || haystack(item).indexOf(q) !== -1;
        return okCat && okQ;
      });
      curPage = 1;
      render();
    }

    /* =====================================================
     * RENDER GRID + PAGINATION
     * ===================================================== */
    function render() {
      var total = filtered.length;
      var pages = Math.max(1, Math.ceil(total / perPage));
      curPage = Math.min(curPage, pages);
      var start = (curPage - 1) * perPage;
      var end = Math.min(start + perPage, total);
      var slice = filtered.slice(start, end);

      /* Grid */
      if (total === 0) {
        grid.style.display = 'none';
        pagBar.style.display = 'none';
        stateEl.style.display = '';
        stateTxt.textContent = 'Tidak ada item yang cocok.';
      } else {
        stateEl.style.display = 'none';
        grid.style.display = '';
        pagBar.style.display = '';
        grid.innerHTML = slice.map(renderCard).join('');
      }

      /* Info teks */
      pagInfo.textContent = total > 0 ?
        'Halaman ' + curPage + ' dari ' + pages + ' \u2014 ' + total + ' item' :
        '';

      /* Tombol numerik + prev/next */
      renderPagBtns(pages);
    }

    function renderPagBtns(pages) {
      var html = '';

      /* Prev */
      html += '<button class="lay-pag-btn" id="lay-pag-prev"' + (curPage <= 1 ? ' disabled' : '') + ' aria-label="Sebelumnya">' +
        '<span class="material-symbols-outlined" style="font-size:18px;line-height:1">chevron_left</span></button>';

      /* Nomor halaman — tampilkan max 5 tombol dengan elipsis */
      var pageNums = buildPageNums(pages, curPage);
      pageNums.forEach(function(p) {
        if (p === '…') {
          html += '<span class="lay-pag-btn" style="cursor:default;border:none;background:none">…</span>';
        } else {
          html += '<button class="lay-pag-btn' + (p === curPage ? ' active' : '') + '" data-page="' + p + '">' + p + '</button>';
        }
      });

      /* Next */
      html += '<button class="lay-pag-btn" id="lay-pag-next"' + (curPage >= pages ? ' disabled' : '') + ' aria-label="Berikutnya">' +
        '<span class="material-symbols-outlined" style="font-size:18px;line-height:1">chevron_right</span></button>';

      pagBtns.innerHTML = html;

      /* Bind events */
      var prev = document.getElementById('lay-pag-prev');
      var next = document.getElementById('lay-pag-next');
      if (prev) prev.addEventListener('click', function() {
        if (curPage > 1) {
          curPage--;
          render();
          scrollToGrid();
        }
      });
      if (next) next.addEventListener('click', function() {
        if (curPage < pages) {
          curPage++;
          render();
          scrollToGrid();
        }
      });
      Array.prototype.forEach.call(pagBtns.querySelectorAll('[data-page]'), function(btn) {
        btn.addEventListener('click', function() {
          curPage = parseInt(this.getAttribute('data-page'), 10);
          render();
          scrollToGrid();
        });
      });
    }

    function buildPageNums(total, cur) {
      if (total <= 7) {
        var arr = [];
        for (var i = 1; i <= total; i++) arr.push(i);
        return arr;
      }
      /* elipsis style: 1 ... cur-1 cur cur+1 ... last */
      var nums = [1];
      if (cur > 3) nums.push('…');
      for (var p = Math.max(2, cur - 1); p <= Math.min(total - 1, cur + 1); p++) nums.push(p);
      if (cur < total - 2) nums.push('…');
      nums.push(total);
      return nums;
    }

    function scrollToGrid() {
      var el = document.getElementById('lay-skeletons') || grid;
      if (el) el.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }

    /* =====================================================
     * AJAX LOAD
     * ===================================================== */
    function loadData() {
      fetch(API_URL)
        .then(function(r) {
          if (!r.ok) throw new Error('HTTP ' + r.status);
          return r.json();
        })
        .then(function(res) {
          skeletons.style.display = 'none';
          if (!res.ok || !Array.isArray(res.daftar)) {
            showError('Gagal memuat data. Silakan muat ulang halaman.');
            return;
          }
          allItems = res.daftar;
          if (allItems.length === 0) {
            stateEl.style.display = '';
            stateTxt.textContent = 'Belum ada data layanan & UMKM.';
            return;
          }
          applyFilter();
        })
        .catch(function() {
          skeletons.style.display = 'none';
          showError('Terjadi kesalahan jaringan. Silakan muat ulang halaman.');
        });
    }

    function showError(msg) {
      stateEl.style.display = '';
      stateTxt.textContent = msg;
      stateTxt.previousElementSibling && (stateTxt.previousElementSibling.textContent = 'wifi_off');
    }

    /* =====================================================
     * EVENT LISTENERS
     * ===================================================== */
    /* Tab kategori */
    tabs.forEach(function(btn) {
      btn.addEventListener('click', function() {
        tabs.forEach(function(b) {
          b.className = 'whitespace-nowrap px-6 py-2.5 rounded-full border border-border-neutral text-slate-text-muted text-label-sm hover:bg-surface-container hover:border-primary transition-all';
        });
        btn.className = 'whitespace-nowrap px-6 py-2.5 rounded-full bg-primary text-white text-label-sm font-bold transition-transform hover:scale-105';
        activeCat = btn.getAttribute('data-kategori') || '';
        applyFilter();
      });
    });

    /* Search */
    if (searchEl) {
      var searchTimer;
      searchEl.addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(applyFilter, 220);
      });
    }

    /* Per-page selector */
    if (perPageEl) {
      perPageEl.addEventListener('change', function() {
        perPage = parseInt(this.value, 10) || 9;
        curPage = 1;
        render();
      });
    }

    /* Lightbox — klik foto di grid */
    grid.addEventListener('click', function(e) {
      var wrap = e.target.closest('[data-preview-front]');
      if (!wrap) return;
      var src = wrap.getAttribute('data-preview-front');
      if (src) openLightbox(src);
    });

    /* =====================================================
     * LIGHTBOX
     * ===================================================== */
    var lightbox = document.getElementById('lay-lightbox');
    var lbImg = document.getElementById('lay-lightbox-img');
    var lbSpinner = document.getElementById('lay-lightbox-spinner');
    var lbClose = document.getElementById('lay-lightbox-close');

    function openLightbox(src) {
      lbImg.style.display = 'none';
      lbSpinner.style.display = 'flex';
      lightbox.classList.add('active');
      document.body.style.overflow = 'hidden';
      var tmp = new Image();
      tmp.onload = function() {
        lbImg.src = src;
        lbImg.style.display = 'block';
        lbSpinner.style.display = 'none';
      };
      tmp.onerror = function() {
        lbSpinner.style.display = 'none';
      };
      tmp.src = src;
    }

    function closeLightbox() {
      lightbox.classList.remove('active');
      document.body.style.overflow = '';
      lbImg.src = '';
      lbImg.style.display = 'none';
    }
    lbClose.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function(e) {
      if (e.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeLightbox();
    });

    /* =====================================================
     * INIT
     * ===================================================== */
    loadData();
  })();
</script>