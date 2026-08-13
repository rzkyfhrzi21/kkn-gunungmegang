<footer class="site-footer">
  <div class="footer-top">
    <div class="footer-brand">
      <div class="fb-head">
        <img src="dashboard/assets/Lambang_Kabupaten_Tanggamus.png" alt="Logo Pekon Gunung Megang">
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
      <a href="apbpekon-2026">Transparansi APB Pekon</a>
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
(function(){
  var t = document.getElementById('navToggle');
  var n = document.getElementById('siteNav');
  if (t && n) t.addEventListener('click', function(){ n.classList.toggle('open'); });
})();
</script>
<script src="assets/js/app-front.js" defer></script>
<script src="assets/js/security-warning.js" defer></script>