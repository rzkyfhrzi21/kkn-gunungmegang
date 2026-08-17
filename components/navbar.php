<?php // components/navbar.php - Navbar bersama untuk seluruh halaman depan (dipanggil header.php) ?>
<nav class="site-nav" id="siteNav">
  <a href="index" class="nav-link<?= $activePage === 'index' ? ' active' : '' ?>">Beranda</a>
  <a href="profil-desa" class="nav-link<?= $activePage === 'profil-desa' ? ' active' : '' ?>">Profil Desa</a>
  <a href="pemerintahan" class="nav-link<?= $activePage === 'pemerintahan' ? ' active' : '' ?>">Pemerintahan</a>
  <a href="potensi-ekonomi" class="nav-link<?= $activePage === 'potensi-ekonomi' ? ' active' : '' ?>">Potensi &amp; Ekonomi</a>
  <a href="layanan-umkm" class="nav-link<?= $activePage === 'layanan-umkm' ? ' active' : '' ?>">Layanan &amp; UMKM</a>
  <a href="apbpekon" class="nav-link<?= $activePage === 'apbpekon' || $activePage === 'apbpekon-2026' ? ' active' : '' ?>">APB Pekon</a>
  <a href="kontak" class="nav-link<?= $activePage === 'kontak' ? ' active' : '' ?>">Kontak</a>
</nav>