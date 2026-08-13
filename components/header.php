<?php
require_once __DIR__ . '/../includes/data.php';
$activePage = basename($_SERVER['SCRIPT_NAME'], '.php');
?>
<style>
/* ===== HEADER & FOOTER ===== */
.site-header{position:fixed;top:0;left:0;right:0;z-index:1000;background:#ffffff;box-shadow:0 2px 14px rgba(6,35,27,.10);}
.header-inner{max-width:1280px;margin:0 auto;padding:0 24px;height:78px;display:flex;align-items:center;justify-content:space-between;gap:20px;}
.brand{display:flex;align-items:center;gap:12px;text-decoration:none;flex-shrink:0;}
.brand-logo{height:50px;width:auto;object-fit:contain;}
.brand-text{display:flex;flex-direction:column;line-height:1.2;}
.brand-name{font-weight:800;color:#06231b;font-size:17px;letter-spacing:.01em;white-space:nowrap;}
.brand-sub{font-size:10px;color:#64748b;letter-spacing:.2em;text-transform:uppercase;font-weight:700;margin-top:2px;}
.site-nav{display:flex;align-items:center;gap:2px;flex-wrap:nowrap;}
.nav-link{padding:9px 14px;border-radius:10px;font-size:13px;font-weight:700;color:#475569;text-decoration:none;text-transform:uppercase;letter-spacing:.05em;transition:all .2s;white-space:nowrap;}
.nav-link:hover{color:#0a3d2f;background:#ecfdf5;}
.nav-link.active{color:#ffffff;background:#0a3d2f;}
.header-actions{display:flex;align-items:center;gap:12px;flex-shrink:0;}
.btn-contact{background:#0a3d2f;color:#fff;padding:11px 22px;border-radius:999px;font-size:12.5px;font-weight:700;text-decoration:none;transition:all .2s;letter-spacing:.03em;white-space:nowrap;}
.btn-contact:hover{background:#065f46;transform:translateY(-1px);box-shadow:0 6px 16px rgba(10,61,47,.25);}
.nav-toggle{display:none;flex-direction:column;gap:5px;background:none;border:none;cursor:pointer;padding:8px;}
.nav-toggle span{width:22px;height:2.5px;background:#0a3d2f;border-radius:2px;transition:.3s;}
.site-footer{background:#06231b;color:#cbd5e1;}
.footer-top{max-width:1280px;margin:0 auto;padding:64px 24px 48px;display:grid;grid-template-columns:1.6fr 1fr 1.4fr;gap:48px;}
.fb-head{display:flex;align-items:center;gap:14px;margin-bottom:20px;}
.fb-head img{height:54px;width:auto;}
.fb-name{color:#fff;font-weight:800;font-size:18px;line-height:1.25;}
.fb-sub{color:#fbbf24;font-size:11px;letter-spacing:.15em;text-transform:uppercase;font-weight:600;}
.footer-brand p{font-size:14px;line-height:1.75;color:#94a3b8;max-width:340px;}
.footer-col h4{color:#fbbf24;font-size:12.5px;text-transform:uppercase;letter-spacing:.16em;margin:0 0 20px;font-weight:700;}
.footer-col a{display:block;color:#cbd5e1;font-size:14px;text-decoration:none;margin-bottom:12px;transition:all .2s;}
.footer-col a:hover{color:#fff;padding-left:6px;}
.contact-item{display:flex;gap:12px;margin-bottom:16px;font-size:13.5px;line-height:1.65;color:#cbd5e1;}
.contact-item .material-symbols-outlined{color:#fbbf24;font-size:20px;flex-shrink:0;}
.footer-bottom{border-top:1px solid rgba(255,255,255,.08);}
.footer-bottom-inner{max-width:1280px;margin:0 auto;padding:20px 24px;display:flex;justify-content:space-between;align-items:center;gap:12px;font-size:12.5px;color:#64748b;flex-wrap:wrap;}
.footer-bottom-inner a{color:#94a3b8;text-decoration:none;margin-left:20px;transition:color .2s;}
.footer-bottom-inner a:hover{color:#fff;}
@media (max-width:1024px){
  .site-nav{display:none;position:absolute;top:78px;left:0;right:0;background:#fff;flex-direction:column;padding:16px 24px;box-shadow:0 12px 24px rgba(0,0,0,.08);}
  .site-nav.open{display:flex;}
  .nav-toggle{display:flex;}
}
@media (max-width:768px){
  .footer-top{grid-template-columns:1fr;gap:36px;}
  .btn-contact{display:none;}
  .brand-name{font-size:14px;}
}
/* ---------- Preview Media (front) ---------- */
.front-preview-overlay{
  position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;
  background:rgba(6,18,26,.88);backdrop-filter:blur(4px);
  opacity:0;visibility:hidden;transition:opacity .25s ease,visibility .25s ease;padding:24px;
}
.front-preview-overlay.open{opacity:1;visibility:visible;}
.front-preview-box{
  position:relative;max-width:min(92vw,1000px);max-height:88vh;width:100%;
  background:#0b3b4a;border-radius:16px;padding:20px;box-shadow:0 24px 64px rgba(0,0,0,.5);
}
.front-preview-close{
  position:absolute;top:-14px;right:-14px;width:40px;height:40px;border-radius:50%;
  background:#fff;color:#0b3b4a;border:0;cursor:pointer;display:flex;align-items:center;justify-content:center;
  box-shadow:0 4px 12px rgba(0,0,0,.35);z-index:2;
}
.front-preview-close:hover{background:#fecaca;color:#b91c1c;}
.front-preview-content{display:flex;align-items:center;justify-content:center;min-height:200px;max-height:calc(88vh - 60px);}
.front-preview-media{max-width:100%;max-height:calc(88vh - 60px);object-fit:contain;border-radius:8px;display:block;}
.front-preview-loading{width:44px;height:44px;border:4px solid rgba(255,255,255,.25);border-top-color:#0ea5a4;border-radius:50%;animation:frontSpin 1s linear infinite;}
@keyframes frontSpin{to{transform:rotate(360deg);}}
.front-preview-error{color:#fecaca;font-size:1rem;}
</style>
<header class="site-header">
  <div class="header-inner">
    <a href="index" class="brand">
      <img src="dashboard/assets/Lambang_Kabupaten_Tanggamus.png" alt="Logo Pekon Gunung Megang" class="brand-logo">
      <div class="brand-text">
        <span class="brand-name">Pekon Gunung Megang</span>
        <span class="brand-sub">Kabupaten Tanggamus</span>
      </div>
    </a>
    <nav class="site-nav" id="siteNav">
      <a href="index" class="nav-link<?= $activePage === 'index' ? ' active' : '' ?>">Beranda</a>
      <a href="profil-desa" class="nav-link<?= $activePage === 'profil-desa' ? ' active' : '' ?>">Profil Desa</a>
      <a href="pemerintahan" class="nav-link<?= $activePage === 'pemerintahan' ? ' active' : '' ?>">Pemerintahan</a>
      <a href="potensi-ekonomi" class="nav-link<?= $activePage === 'potensi-ekonomi' ? ' active' : '' ?>">Potensi &amp; Ekonomi</a>
      <a href="apbpekon-2026" class="nav-link<?= $activePage === 'apbpekon-2026' ? ' active' : '' ?>">APB PEKON 2026</a>
      <a href="kontak" class="nav-link<?= $activePage === 'kontak' ? ' active' : '' ?>">Kontak</a>
    </nav>
    <div class="header-actions">
      <a href="kontak" class="btn-contact">Hubungi Desa</a>
      <button class="nav-toggle" id="navToggle" aria-label="Menu"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>