<?php
require_once __DIR__ . '/../includes/data.php';
// Gunakan REQUEST_URI (URL publik) agar konsisten setelah restructuring ke views/landing/
$_hReqPath  = strtok($_SERVER['REQUEST_URI'] ?? '/', '?#');
$activePage = basename(rtrim($_hReqPath, '/')) ?: 'index';
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
@media (max-width:1024px){
  .site-nav{
    display:none;
    position:fixed;inset:0;z-index:998;
    background:#fff;
    flex-direction:column;
    padding:calc(78px + 16px) 28px 48px;
    overflow-y:auto;
    gap:2px;
  }
  .site-nav.open{display:flex;animation:navOpenFade .22s ease;}
  @keyframes navOpenFade{from{opacity:0;transform:translateY(-6px);}to{opacity:1;transform:translateY(0);}}
  .nav-toggle{display:flex;position:relative;z-index:1001;}
  .site-nav .nav-link{
    font-size:15px;padding:13px 16px;border-radius:12px;width:100%;
    border-bottom:1px solid #f1f5f9;
  }
  .site-nav .nav-link:last-child{border-bottom:none;}
}
@media (max-width:768px){
  .btn-contact{display:none;}
  .brand-name{font-size:14px;}
}
/* ===== NAV DROPDOWN ===== */
.nav-dropdown{position:relative;}
.nav-dropdown-btn{
  display:inline-flex;align-items:center;gap:5px;
  padding:9px 14px;border-radius:10px;font-size:13px;font-weight:700;
  color:#475569;background:none;border:none;cursor:pointer;
  text-transform:uppercase;letter-spacing:.05em;transition:all .2s;white-space:nowrap;
}
.nav-dropdown-btn:hover,.nav-dropdown-btn:focus{color:#0a3d2f;background:#ecfdf5;outline:none;}
.nav-dropdown-btn.active{color:#ffffff;background:#0a3d2f;}
.nav-dropdown-btn.active .nav-chevron{color:#fff;}
.nav-chevron{width:14px;height:14px;flex-shrink:0;transition:transform .2s;color:#94a3b8;}
.nav-dropdown-menu{
  position:absolute;top:calc(100% + 6px);left:50%;
  transform:translateX(-50%) translateY(-6px);
  background:#fff;border-radius:14px;
  box-shadow:0 8px 32px rgba(6,35,27,.15),0 2px 8px rgba(0,0,0,.06);
  padding:6px;min-width:210px;
  opacity:0;visibility:hidden;
  transition:opacity .18s ease,visibility .18s ease,transform .18s ease;
  border:1px solid rgba(10,61,47,.07);
}
.nav-dropdown:hover .nav-dropdown-menu,
.nav-dropdown.open .nav-dropdown-menu{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0);}
.nav-dropdown:hover .nav-chevron,
.nav-dropdown.open .nav-chevron{transform:rotate(180deg);}
.nav-dropdown-item{
  display:flex;align-items:center;gap:10px;
  padding:10px 14px;border-radius:9px;font-size:13px;font-weight:600;
  color:#334155;text-decoration:none;letter-spacing:.02em;transition:all .15s;
}
.nav-dropdown-item:hover{background:#ecfdf5;color:#0a3d2f;}
.nav-dropdown-item.active{background:#0a3d2f;color:#fff;}
.nav-dropdown-item .nd-icon{width:16px;height:16px;flex-shrink:0;opacity:.7;}
@media (max-width:1024px){
  .nav-dropdown{width:100%;}
  .nav-dropdown-btn{
    width:100%;justify-content:space-between;
    padding:13px 16px;font-size:15px;border-radius:12px;
    border-bottom:1px solid #f1f5f9;
  }
  .nav-dropdown.open .nav-dropdown-btn{border-bottom-color:transparent;}
  .nav-dropdown-menu{
    position:static;transform:none !important;opacity:1;visibility:visible;
    box-shadow:none;border:none;border-radius:0;padding:0;min-width:0;
    display:none;padding-left:20px;margin-bottom:4px;
    background:#f8fafb;border-radius:12px;
  }
  .nav-dropdown.open .nav-dropdown-menu{display:flex;flex-direction:column;gap:2px;padding:6px 8px;}
  .nav-dropdown-item{padding:10px 14px;font-size:14px;border-radius:10px;}
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
      <img src="assets/images/Lambang_Kabupaten_Tanggamus.png" alt="Logo Pekon Gunung Megang" class="brand-logo">
      <div class="brand-text">
        <span class="brand-name">Pekon Gunung Megang</span>
        <span class="brand-sub">Kabupaten Tanggamus</span>
      </div>
    </a>
    <?php include __DIR__ . '/navbar.php'; ?>
    <div class="header-actions">
      <a href="kontak" class="btn-contact">Hubungi Desa</a>
      <button class="nav-toggle" id="navToggle" aria-label="Menu"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>