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

  .footer-brand .contact-item {
    margin-top: 20px;
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
      <div class="contact-item">
        <span class="material-symbols-outlined">schedule</span>
        <span><?php $jamRowsF = $pekon['kontak']['jam'] ?? [];
        foreach ($jamRowsF as $iF => $jrF): ?><?= $iF > 0 ? '<br>' : '' ?><?= htmlspecialchars($jrF['hari'] ?? '') ?> <?= htmlspecialchars(($jrF['jam'] ?? '') !== '' ? $jrF['jam'] : ($jrF['status'] ?? '')) ?><?php endforeach; ?></span>
      </div>
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
        <span><?= htmlspecialchars($pekon['kontak']['maps_code'] ?? '') ?></span>
      </div>
      <?php $cpsF = $pekon['kontak']['contact_person'] ?? []; ?>
      <?php if (!empty($cpsF)): ?>
        <?php foreach ($cpsF as $cpF): $cpFTel = preg_replace('/\D+/', '', $cpF['telepon'] ?? ''); ?>
          <div class="contact-item">
            <span class="material-symbols-outlined">call</span>
            <span>
              <?= htmlspecialchars($cpF['nama'] ?? '') ?><br>
              <a href="https://wa.me/<?= '62' . ltrim($cpFTel, '0') ?>" target="_blank" rel="noopener"><?= trim(chunk_split($cpFTel, 4, ' ')) ?></a>
            </span>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="contact-item">
          <span class="material-symbols-outlined">call</span>
          <span><?= trim(chunk_split($pekon['kontak']['telepon'], 4, ' ')) ?></span>
        </div>
      <?php endif; ?>
      <?php $igUrlF = $pekon['kontak']['instagram'] ?? ''; ?>
      <?php if ($igUrlF !== ''): ?>
        <div class="contact-item">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="#fbbf24" style="flex-shrink:0;" aria-hidden="true">
            <path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.9.2 2.3.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1.1.4 2.3.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 1.9-.4 2.3-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1.1.4-2.3.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.9-.2-2.3-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1.1-.4-2.3-.1-1.3-.1-1.7-.1-4.9s0-3.6.1-4.9c.1-1.2.2-1.9.4-2.3.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1.1-.4 2.3-.4 1.3-.1 1.7-.1 4.9-.1zM12 0C8.7 0 8.3 0 7 .1 5.7.2 4.8.4 4 .7c-.9.3-1.6.8-2.4 1.5C.9 3 .4 3.7.1 4.6c-.3.8-.5 1.7-.6 3C-.1 8.3-.1 8.7-.1 12s0 3.7.1 5c.1 1.3.3 2.2.6 3 .3.9.8 1.6 1.5 2.4.8.8 1.5 1.2 2.4 1.5.8.3 1.7.5 3 .6 1.3.1 1.7.1 5 .1s3.7 0 5-.1c1.3-.1 2.2-.3 3-.6.9-.3 1.6-.8 2.4-1.5.8-.8 1.2-1.5 1.5-2.4.3-.8.5-1.7.6-3 .1-1.3.1-1.7.1-5s0-3.7-.1-5c-.1-1.3-.3-2.2-.6-3-.3-.9-.8-1.6-1.5-2.4C20.1.9 19.4.4 18.5.1c-.8-.3-1.7-.5-3-.6C14.2-.1 13.8-.1 12 0zm0 5.8a6.2 6.2 0 1 0 0 12.4 6.2 6.2 0 0 0 0-12.4zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm7.8-10.4a1.4 1.4 0 1 1-2.8 0 1.4 1.4 0 0 1 2.8 0z"/>
          </svg>
          <a href="<?= htmlspecialchars($igUrlF) ?>" target="_blank" rel="noopener">Instagram Resmi: <?= htmlspecialchars(preg_replace('#^https?://(www\.)?instagram\.com/([^/]+)/?$#i', '@$2', $igUrlF)) ?></a>
        </div>
      <?php endif; ?>
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