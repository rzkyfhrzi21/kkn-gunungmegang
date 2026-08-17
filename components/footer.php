<style>
  .site-footer {
    background: #06231b;
    color: #cbd5e1;
  }

  .footer-top {
    max-width: 1280px;
    margin: 0 auto;
    padding: 64px 24px 48px;
    display: grid;
    grid-template-columns: 1.6fr 1fr 1.4fr;
    gap: 48px;
  }

  .fb-head {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 20px;
  }

  .fb-head img {
    height: 54px;
    width: auto;
  }

  .fb-name {
    color: #fff;
    font-weight: 800;
    font-size: 18px;
    line-height: 1.25;
  }

  .fb-sub {
    color: #fbbf24;
    font-size: 11px;
    letter-spacing: .15em;
    text-transform: uppercase;
    font-weight: 600;
  }

  .footer-brand p {
    font-size: 14px;
    line-height: 1.75;
    color: #94a3b8;
    max-width: 340px;
  }

  .footer-col h4 {
    color: #fbbf24;
    font-size: 12.5px;
    text-transform: uppercase;
    letter-spacing: .16em;
    margin: 0 0 20px;
    font-weight: 700;
  }

  .footer-col a {
    display: block;
    color: #cbd5e1;
    font-size: 14px;
    text-decoration: none;
    margin-bottom: 12px;
    transition: all .2s;
  }

  .footer-col a:hover {
    color: #fff;
    padding-left: 6px;
  }

  .contact-item {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    font-size: 13.5px;
    line-height: 1.65;
    color: #cbd5e1;
  }

  .contact-item .material-symbols-outlined {
    color: #fbbf24;
    font-size: 20px;
    flex-shrink: 0;
  }

  .footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, .08);
  }

  .footer-bottom-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    font-size: 12.5px;
    color: #64748b;
    flex-wrap: wrap;
  }

  .footer-bottom-inner a {
    color: #94a3b8;
    text-decoration: none;
    margin-left: 20px;
    transition: color .2s;
  }

  .footer-bottom-inner a:hover {
    color: #fff;
  }

  @media (max-width:768px) {
    .footer-top {
      grid-template-columns: 1fr;
      gap: 36px;
    }
  }
</style>
<footer class="site-footer">
  <div class="footer-top">
    <div class="footer-brand">
      <div class="fb-head">
        <img src="assets/images/Lambang_Kabupaten_Tanggamus.png" alt="Logo Pekon Gunung Megang">
        <div>
          <div class="fb-name">Pekon Gunung Megang</div>
          <div class="fb-sub">Kabupaten Tanggamus</div>
        </div>
      </div>
      <p>Pusat informasi resmi dan pelayanan publik digital terpadu untuk kemajuan masyarakat Pekon Gunung Megang, Kecamatan Pulau Panggung, Kabupaten Tanggamus.</p>
    </div>
    <div class="footer-col">
      <h4>Tautan Cepat</h4>
      <a href="index">Beranda</a>
      <a href="profil-desa">Profil Desa</a>
      <a href="pemerintahan">Pemerintahan</a>
      <a href="potensi-ekonomi">Potensi &amp; Ekonomi</a>
      <a href="layanan-umkm">Layanan &amp; UMKM</a>
      <a href="apbpekon">Transparansi APB Pekon</a>
    </div>
    <div class="footer-col">
      <h4>Hubungi Kami</h4>
      <div class="contact-item">
        <span class="material-symbols-outlined">location_on</span>
        <span>Kantor Pekon Gunung Megang, Kec. Pulau Panggung, Kab. Tanggamus, Lampung 35679</span>
      </div>
      <div class="contact-item">
        <span class="material-symbols-outlined">call</span>
        <span><?= trim(chunk_split($pekon['kontak']['telepon'], 4, ' ')) ?></span>
      </div>
      <div class="contact-item">
        <span class="material-symbols-outlined">schedule</span>
        <span>Senin – Kamis 08.00–15.30 WIB<br>Jumat 08.00–11.30 WIB</span>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="footer-bottom-inner">
      <span>© 2026 Pemerintah Pekon Gunung Megang. All rights reserved.</span>
      <span>
        <a href="#">Privasi</a>
        <a href="#">Syarat &amp; Ketentuan</a>
      </span>
    </div>
  </div>
</footer>
<script>
  (function() {
    var t = document.getElementById('navToggle');
    var n = document.getElementById('siteNav');
    if (t && n) t.addEventListener('click', function() {
      n.classList.toggle('open');
    });
  })();
</script>
<script src="assets/js/app-front.js" defer></script>
<script src="assets/js/security-warning.js" defer></script>