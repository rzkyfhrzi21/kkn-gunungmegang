<div class="flex flex-col w-full font-body-md text-on-surface">
<section class="relative w-full overflow-hidden bg-primary pb-16 lg:pb-32 -mt-20 pt-32 lg:pt-48">
<div class="absolute inset-0 w-full h-full">
<div class="w-full h-full bg-cover bg-center absolute inset-0 opacity-40 mix-blend-overlay" style="background-image: linear-gradient(135deg, #0b3b4a 0%, #0ea5a4 60%, #0b3b4a 100%)"></div>
<div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/80 to-transparent"></div>
</div>
<div class="relative max-w-container-max mx-auto px-gutter z-10 text-on-primary">
<div class="max-w-3xl">
<span class="inline-block py-1.5 px-4 rounded-full bg-surface-tint text-on-primary text-label-sm uppercase tracking-widest mb-6 shadow-sm border border-outline-variant/30">Profil Pekon</span>
<h1 class="font-display text-4xl lg:text-6xl mb-6">Mengenal Lebih Dekat Gunung Megang.</h1>
<p class="font-body-lg text-on-primary/90 leading-relaxed mb-8">Pekon Gunung Megang merupakan wilayah agraris yang subur di Kecamatan Pulau Panggung, Kabupaten Tanggamus. Dikenal dengan kekayaan alamnya yang melimpah, khususnya komoditas kopi dan pertanian, pekon ini terus berkembang menjadi desa mandiri yang sejahtera berlandaskan nilai religius dan kearifan lokal.</p>
<div class="flex flex-wrap gap-4">
<div class="flex items-center gap-3 bg-primary-container/50 px-5 py-3 rounded-xl border border-outline-variant/20">
<span class="material-symbols-outlined text-inverse-primary" style="font-variation-settings: 'FILL' 1;">location_on</span>
<span class="font-headline-md text-base">Kec. <?= $pekon['kecamatan'] ?></span>
</div>
<div class="flex items-center gap-3 bg-primary-container/50 px-5 py-3 rounded-xl border border-outline-variant/20">
<span class="material-symbols-outlined text-inverse-primary" style="font-variation-settings: 'FILL' 1;">terrain</span>
<span class="font-headline-md text-base">Kab. <?= $pekon['kabupaten'] ?></span>
</div>
</div>
</div>
</div>
</section>
<section class="w-full py-section-padding bg-surface-container-low -mt-8 relative z-20">
<div class="max-w-container-max mx-auto px-gutter">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-card-gap mb-section-padding">
<div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 hover:shadow-md transition-shadow group">
<div class="w-12 h-12 rounded-full bg-primary-container text-on-primary flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">groups</span>
</div>
<h3 class="text-label-sm uppercase text-slate-text-muted mb-2 tracking-wider">Total Penduduk</h3>
<p class="font-display text-4xl text-primary mb-2"><?= number_format($pekon['demografi']['total_jiwa'], 0, ',', '.') ?></p>
<div class="flex flex-col gap-1 text-sm text-on-surface-variant font-medium">
<span class="flex justify-between items-center bg-surface-container px-3 py-1.5 rounded-md"><span>Laki-laki</span> <span><?= number_format($pekon['demografi']['laki_laki'], 0, ',', '.') ?> Jiwa</span></span>
<span class="flex justify-between items-center bg-surface-container px-3 py-1.5 rounded-md"><span>Perempuan</span> <span><?= number_format($pekon['demografi']['perempuan'], 0, ',', '.') ?> Jiwa</span></span>
</div>
</div>
<div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 hover:shadow-md transition-shadow group">
<div class="w-12 h-12 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">family_home</span>
</div>
<h3 class="text-label-sm uppercase text-slate-text-muted mb-2 tracking-wider">Kepala Keluarga</h3>
<p class="font-display text-4xl text-secondary mb-2"><?= $pekon['demografi']['jumlah_kk'] ?></p>
<p class="text-sm text-on-surface-variant">Keluarga yang tersebar di wilayah administrasi pekon.</p>
</div>
<div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 hover:shadow-md transition-shadow group">
<div class="w-12 h-12 rounded-full bg-tertiary-container text-on-tertiary-container flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">landscape</span>
</div>
<h3 class="text-label-sm uppercase text-slate-text-muted mb-2 tracking-wider">Luas Wilayah</h3>
<p class="font-display text-4xl text-tertiary mb-2"><?= number_format($pekon['demografi']['luas_wilayah_km2'], 2, ',', '.') ?> <span class="text-xl">km²</span></p>
<p class="text-sm text-on-surface-variant">Didominasi oleh lahan perkebunan kopi dan pertanian produktif.</p>
</div>
<div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 hover:shadow-md transition-shadow group">
<div class="w-12 h-12 rounded-full bg-surface-tint text-on-primary flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">route</span>
</div>
<h3 class="text-label-sm uppercase text-slate-text-muted mb-2 tracking-wider">Orbitasi Jarak</h3>
<p class="font-display text-4xl text-primary mb-2"><?= $pekon['demografi']['jarak_kecamatan_km'] ?> <span class="text-xl">km</span></p>
<p class="text-sm text-on-surface-variant">Jarak ke Kantor Kecamatan (Est. <?= $pekon['demografi']['waktu_kecamatan_menit'] ?> menit perjalanan).</p>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-card-gap">
<div class="lg:col-span-5 flex flex-col gap-card-gap">
<div class="bg-surface p-8 rounded-3xl shadow-sm border border-outline-variant/30 h-full">
<div class="flex items-center gap-4 mb-8">
<div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
<span class="material-symbols-outlined text-primary">explore</span>
</div>
<h2 class="font-headline-lg text-primary">Batas Wilayah</h2>
</div>
<div class="relative pl-6 border-l-2 border-outline-variant/30 space-y-8">
<div class="relative">
<div class="absolute -left-[29px] top-1 w-4 h-4 rounded-full border-4 border-surface bg-primary"></div>
<h4 class="font-headline-md text-base text-on-surface mb-1">Utara</h4>
<p class="text-on-surface-variant"><?= htmlspecialchars($pekon['demografi']['batas_wilayah']['utara']) ?></p>
</div>
<div class="relative">
<div class="absolute -left-[29px] top-1 w-4 h-4 rounded-full border-4 border-surface bg-secondary"></div>
<h4 class="font-headline-md text-base text-on-surface mb-1">Timur</h4>
<p class="text-on-surface-variant"><?= htmlspecialchars($pekon['demografi']['batas_wilayah']['timur']) ?></p>
</div>
<div class="relative">
<div class="absolute -left-[29px] top-1 w-4 h-4 rounded-full border-4 border-surface bg-tertiary"></div>
<h4 class="font-headline-md text-base text-on-surface mb-1">Selatan</h4>
<p class="text-on-surface-variant"><?= htmlspecialchars($pekon['demografi']['batas_wilayah']['selatan']) ?></p>
</div>
<div class="relative">
<div class="absolute -left-[29px] top-1 w-4 h-4 rounded-full border-4 border-surface bg-surface-tint"></div>
<h4 class="font-headline-md text-base text-on-surface mb-1">Barat</h4>
<p class="text-on-surface-variant"><?= htmlspecialchars($pekon['demografi']['batas_wilayah']['barat']) ?></p>
</div>
</div>
</div>
</div>
<div class="lg:col-span-7 bg-primary rounded-3xl p-8 lg:p-12 shadow-md relative overflow-hidden text-on-primary">
<div class="absolute -right-24 -top-24 w-96 h-96 bg-primary-container rounded-full blur-[100px] opacity-60"></div>
<div class="relative z-10">
<span class="text-label-sm uppercase tracking-widest text-inverse-primary mb-4 block">Visi 2026</span>
<h2 class="font-display text-2xl md:text-3xl lg:text-4xl leading-tight mb-12">"Terwujudnya Pekon Gunung Megang yang maju, mandiri, sejahtera, religius, dan berkelanjutan melalui pembangunan dan pelayanan yang partisipatif, transparan, serta berbasis potensi lokal."</h2>
<span class="text-label-sm uppercase tracking-widest text-inverse-primary mb-6 block border-b border-primary-container pb-4">Misi Strategis</span>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div class="bg-surface/10 p-5 rounded-2xl border border-surface/20 flex gap-4 items-start hover:bg-surface/20 transition-colors">
<span class="font-display text-2xl text-inverse-primary/60 mt-[-4px]">01</span>
<p class="text-sm font-medium leading-relaxed">Meningkatkan kualitas pelayanan publik berbasis digital yang cepat dan efisien.</p>
</div>
<div class="bg-surface/10 p-5 rounded-2xl border border-surface/20 flex gap-4 items-start hover:bg-surface/20 transition-colors">
<span class="font-display text-2xl text-inverse-primary/60 mt-[-4px]">02</span>
<p class="text-sm font-medium leading-relaxed">Membangun dan memelihara infrastruktur pekon yang merata.</p>
</div>
<div class="bg-surface/10 p-5 rounded-2xl border border-surface/20 flex gap-4 items-start hover:bg-surface/20 transition-colors">
<span class="font-display text-2xl text-inverse-primary/60 mt-[-4px]">03</span>
<p class="text-sm font-medium leading-relaxed">Mengembangkan potensi ekonomi lokal melalui BUMDes dan UMKM.</p>
</div>
<div class="bg-surface/10 p-5 rounded-2xl border border-surface/20 flex gap-4 items-start hover:bg-surface/20 transition-colors">
<span class="font-display text-2xl text-inverse-primary/60 mt-[-4px]">04</span>
<p class="text-sm font-medium leading-relaxed">Meningkatkan kualitas SDM melalui pendidikan dan pelatihan terapan.</p>
</div>
<div class="bg-surface/10 p-5 rounded-2xl border border-surface/20 flex gap-4 items-start hover:bg-surface/20 transition-colors">
<span class="font-display text-2xl text-inverse-primary/60 mt-[-4px]">05</span>
<p class="text-sm font-medium leading-relaxed">Mendorong tata kelola pemerintahan yang transparan dan akuntabel.</p>
</div>
<div class="bg-surface/10 p-5 rounded-2xl border border-surface/20 flex gap-4 items-start hover:bg-surface/20 transition-colors">
<span class="font-display text-2xl text-inverse-primary/60 mt-[-4px]">06</span>
<p class="text-sm font-medium leading-relaxed">Melestarikan nilai-nilai religius dan budaya lokal.</p>
</div>
<div class="md:col-span-2 bg-surface/10 p-5 rounded-2xl border border-surface/20 flex gap-4 items-start hover:bg-surface/20 transition-colors">
<span class="font-display text-2xl text-inverse-primary/60 mt-[-4px]">07</span>
<p class="text-sm font-medium leading-relaxed">Menjaga kelestarian lingkungan hidup bagi keberlanjutan pertanian.</p>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
</div>
