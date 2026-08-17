<?php
// components/navbar.php - Navbar bersama untuk seluruh halaman depan (dipanggil header.php)
// Opsi A: Beranda | Profil Pekon ▾ (Profil Desa, Pemerintahan, APB Pekon) | Potensi & Ekonomi | Layanan & UMKM | Kontak
$_profilPages = ['profil-desa', 'pemerintahan', 'apbpekon'];
$_profilActive = in_array($activePage, $_profilPages);
?>
<nav class="site-nav" id="siteNav">
  <a href="index" class="nav-link<?= $activePage === 'index' ? ' active' : '' ?>">Beranda</a>

  <!-- Dropdown: Profil Pekon -->
  <div class="nav-dropdown" id="navDropdownProfil">
    <button class="nav-dropdown-btn<?= $_profilActive ? ' active' : '' ?>"
      aria-haspopup="true" aria-expanded="false" id="navBtnProfil">
      Profil Pekon
      <svg class="nav-chevron" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M4 6l4 4 4-4" />
      </svg>
    </button>
    <div class="nav-dropdown-menu" role="menu" aria-label="Menu Profil Pekon">
      <a href="profil-desa" class="nav-dropdown-item<?= $activePage === 'profil-desa' ? ' active' : '' ?>" role="menuitem">
        <svg class="nd-icon" viewBox="0 0 20 20" fill="currentColor">
          <path d="M10 2a8 8 0 100 16A8 8 0 0010 2zm0 3a2 2 0 110 4 2 2 0 010-4zm0 9c-2.5 0-4.71-1.28-6-3.22C4 9.14 7.13 8 10 8c2.87 0 6 1.14 6 2.78C14.71 12.72 12.5 14 10 14z" />
        </svg>
        Profil Desa
      </a>
      <a href="pemerintahan" class="nav-dropdown-item<?= $activePage === 'pemerintahan' ? ' active' : '' ?>" role="menuitem">
        <svg class="nd-icon" viewBox="0 0 20 20" fill="currentColor">
          <path d="M10 1L2 6v1h16V6L10 1zM3 8v7h3V8H3zm5 0v7h4V8H8zm6 0v7h3V8h-3zM1 16h18v2H1v-2z" />
        </svg>
        Pemerintahan
      </a>
      <a href="apbpekon" class="nav-dropdown-item<?= ($activePage === 'apbpekon' || $activePage === 'apbpekon-2026') ? ' active' : '' ?>" role="menuitem">
        <svg class="nd-icon" viewBox="0 0 20 20" fill="currentColor">
          <path d="M4 4h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm1 3v2h5V7H5zm0 4v2h5v-2H5zm7-4v6h3V7h-3z" />
        </svg>
        APB Pekon
      </a>
    </div>
  </div>

  <a href="potensi-ekonomi" class="nav-link<?= $activePage === 'potensi-ekonomi' ? ' active' : '' ?>">Potensi &amp; Ekonomi</a>
  <a href="layanan-umkm" class="nav-link<?= $activePage === 'layanan-umkm' ? ' active' : '' ?>">Layanan &amp; UMKM</a>
  <a href="kontak" class="nav-link<?= $activePage === 'kontak' ? ' active' : '' ?>">Kontak</a>
</nav>

<script>
  (function() {
    var btn = document.getElementById('navBtnProfil');
    var dropdown = document.getElementById('navDropdownProfil');
    if (!btn || !dropdown) return;

    /* Toggle open/close saat diklik (berguna untuk touch & keyboard) */
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      var isOpen = dropdown.classList.toggle('open');
      btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    /* Tutup dropdown saat klik di luar */
    document.addEventListener('click', function() {
      dropdown.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
    });

    /* Keyboard: Escape menutup */
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        dropdown.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
        btn.focus();
      }
    });
  })();
</script>