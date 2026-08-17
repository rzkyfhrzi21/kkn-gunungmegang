<?php
$kepalaFoto = $pekon['kepala_pekon']['foto'] ?? '';
$kepalaFotoAbs = dirname(__DIR__, 2) . '/' . ltrim($kepalaFoto, '/');
if ($kepalaFoto === '' || !file_exists($kepalaFotoAbs)) $kepalaFoto = '';
?>
<div class="flex flex-col w-full">
    <!-- HERO SECTION -->
    <section class="max-w-container-max mx-auto w-full px-gutter pt-8 pb-16">
        <div class="relative w-full h-[600px] rounded-3xl overflow-hidden flex flex-col justify-end p-8 md:p-16" style="background-image: url('assets/images/hero.jpg'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-gradient-to-t from-primary/95 via-primary/60 to-black/30"></div>
            <div class="relative z-10 max-w-3xl">
                <span class="inline-block px-4 py-1.5 mb-6 bg-secondary text-on-secondary text-label-sm font-label-sm rounded-full tracking-wider uppercase">Portal Resmi Pekon <?= $pekon['tahun'] ?></span>
                <h1 class="font-display text-display text-on-primary mb-4 text-balance">Maju, Mandiri, dan Sejahtera Bersama Warga Gunung Megang</h1>
                <p class="font-body-lg text-body-lg text-on-primary/90 mb-8 max-w-xl">Kecamatan <?= $pekon['kecamatan'] ?>, Kabupaten <?= $pekon['kabupaten'] ?>, <?= $pekon['provinsi'] ?></p>
                <div class="flex flex-wrap items-center gap-4">
                    <a class="px-8 py-4 bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm uppercase tracking-wide rounded-full hover:bg-primary-fixed-dim transition-colors shadow-md flex items-center gap-2" href="profil-desa">
                        <span>Eksplor Profil Pekon</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                    <a class="px-8 py-4 bg-surface text-on-surface font-label-sm text-label-sm uppercase tracking-wide rounded-full hover:bg-surface-variant transition-colors shadow-md border border-outline-variant flex items-center gap-2" href="apbpekon">
                        <span class="material-symbols-outlined text-[20px]">account_balance</span>
                        <span>Transparansi APB Pekon</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- QUICK STATS SECTION -->
    <section class="max-w-container-max mx-auto w-full px-gutter pb-section-padding">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 -mt-24 relative z-20">
            <div class="bg-surface rounded-2xl p-6 shadow-md border border-border-neutral hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 bg-primary-container/10 rounded-xl flex items-center justify-center mb-4 text-primary-container">
                    <span class="material-symbols-outlined text-[24px]">groups</span>
                </div>
                <p class="text-label-sm text-slate-text-muted uppercase tracking-wider mb-2">Total Penduduk</p>
                <h3 class="font-headline-lg text-headline-lg text-on-surface mb-1"><?= number_format($pekon['demografi']['total_jiwa'], 0, ',', '.') ?> <span class="text-body-lg text-slate-text-muted">Jiwa</span></h3>
                <p class="text-body-md text-slate-text-muted"><?= number_format($pekon['demografi']['laki_laki'], 0, ',', '.') ?> Laki-laki • <?= number_format($pekon['demografi']['perempuan'], 0, ',', '.') ?> Perempuan</p>
            </div>
            <div class="bg-surface rounded-2xl p-6 shadow-md border border-border-neutral hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 bg-secondary-container/10 rounded-xl flex items-center justify-center mb-4 text-secondary">
                    <span class="material-symbols-outlined text-[24px]">home_work</span>
                </div>
                <p class="text-label-sm text-slate-text-muted uppercase tracking-wider mb-2">Kepala Keluarga</p>
                <h3 class="font-headline-lg text-headline-lg text-on-surface mb-1"><?= $pekon['demografi']['jumlah_kk'] ?> <span class="text-body-lg text-slate-text-muted">KK</span></h3>
                <p class="text-body-md text-slate-text-muted">Tersebar di wilayah dusun</p>
            </div>
            <div class="bg-surface rounded-2xl p-6 shadow-md border border-border-neutral hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 bg-tertiary-container/10 rounded-xl flex items-center justify-center mb-4 text-tertiary">
                    <span class="material-symbols-outlined text-[24px]">map</span>
                </div>
                <p class="text-label-sm text-slate-text-muted uppercase tracking-wider mb-2">Luas Wilayah</p>
                <h3 class="font-headline-lg text-headline-lg text-on-surface mb-1"><?= number_format($pekon['demografi']['luas_wilayah_km2'], 2, ',', '.') ?> <span class="text-body-lg text-slate-text-muted">km²</span></h3>
                <p class="text-body-md text-slate-text-muted">Lahan produktif &amp; pemukiman</p>
            </div>
            <div class="bg-surface rounded-2xl p-6 shadow-md border border-border-neutral hover:-translate-y-1 transition-transform flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 text-primary-container">
                        <span class="material-symbols-outlined text-[24px]">verified</span>
                    </div>
                    <p class="text-label-sm text-slate-text-muted uppercase tracking-wider mb-2">Status Indeks Desa Membangun (IDM) 2026</p>
                </div>
                <div class="bg-emerald-50 px-4 py-3 rounded-lg border border-primary-fixed-dim">
                    <h3 class="font-headline-md text-headline-md text-primary-container text-center"><?= $pekon['potensi']['idm_status'] ?></h3>
                </div>
            </div>
        </div>
    </section>
    <!-- KEPALA PEKON WELCOME -->
    <section class="max-w-container-max mx-auto w-full px-gutter py-section-padding">
        <div class="bg-surface rounded-3xl p-8 md:p-12 shadow-sm border border-border-neutral">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-sm">
                        <div class="absolute inset-0 bg-primary-fixed/20 rounded-3xl transform translate-x-4 translate-y-4"></div>
                        <?php if ($kepalaFoto !== ''): ?>
                            <img alt="Foto Kepala Pekon Gunung Megang" class="relative z-10 w-full h-auto aspect-[4/5] object-cover rounded-3xl shadow-lg border border-outline-variant cursor-zoom-in" src="<?= $kepalaFoto ?>" data-preview="<?= $kepalaFoto ?>" />
                        <?php else: ?>
                            <div class="relative z-10 w-full h-auto aspect-[4/5] rounded-3xl border border-outline-variant bg-surface-container flex items-center justify-center"><span class="material-symbols-outlined text-[96px] text-on-surface-variant">account_circle</span></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="lg:col-span-7 flex flex-col gap-6">
                    <div class="inline-flex items-center gap-2 text-primary-container">
                        <span class="material-symbols-outlined">format_quote</span>
                        <span class="text-label-sm font-label-sm uppercase tracking-widest">Sambutan Kepala Pekon</span>
                    </div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Membangun Gunung Megang dengan Kolaborasi dan Transparansi</h2>
                    <div class="prose max-w-none text-body-lg text-slate-text-muted font-body-lg leading-relaxed">
                        <p><?= nl2br(htmlspecialchars($pekon['kepala_pekon']['sambutan'] ?? '')) ?></p>
                    </div>
                    <div class="mt-4 pt-6 border-t border-border-neutral">
                        <h4 class="font-headline-md text-headline-md text-on-surface"><?= htmlspecialchars($pekon['kepala_pekon']['nama']) ?></h4>
                        <p class="text-body-md text-primary-container font-medium uppercase tracking-wide mt-1"><?= htmlspecialchars($pekon['kepala_pekon']['jabatan']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- VISI & MISI -->
    <section class="bg-primary text-on-primary py-section-padding mt-8">
        <div class="max-w-container-max mx-auto w-full px-gutter">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                <div class="flex flex-col gap-6">
                    <span class="text-label-sm font-label-sm uppercase tracking-widest text-primary-fixed">Visi 2026</span>
                    <h2 class="font-display text-display leading-tight text-balance">"Mewujudkan Pekon Gunung Megang yang Maju, Mandiri, dan Sejahtera"</h2>
                    <div class="w-24 h-2 bg-secondary rounded-full mt-4"></div>
                </div>
                <div class="flex flex-col gap-8">
                    <span class="text-label-sm font-label-sm uppercase tracking-widest text-primary-fixed">Pilar Misi Utama</span>
                    <div class="flex gap-4 items-start">
                        <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center shrink-0 border border-primary-fixed/30">
                            <span class="text-headline-md font-bold text-primary-fixed">1</span>
                        </div>
                        <div>
                            <h3 class="font-headline-md text-headline-md mb-2">Pelayanan Publik Prima</h3>
                            <p class="text-body-md text-on-primary/80">Meningkatkan tata kelola pemerintahan desa yang bersih, transparan, dan berorientasi pada pelayanan masyarakat secara digital maupun tatap muka.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center shrink-0 border border-primary-fixed/30">
                            <span class="text-headline-md font-bold text-primary-fixed">2</span>
                        </div>
                        <div>
                            <h3 class="font-headline-md text-headline-md mb-2">Pemberdayaan Ekonomi</h3>
                            <p class="text-body-md text-on-primary/80">Mengoptimalkan potensi pertanian dan perkebunan, serta mendorong pertumbuhan UMKM berbasis potensi lokal untuk kemandirian ekonomi warga.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center shrink-0 border border-primary-fixed/30">
                            <span class="text-headline-md font-bold text-primary-fixed">3</span>
                        </div>
                        <div>
                            <h3 class="font-headline-md text-headline-md mb-2">Infrastruktur Berkelanjutan</h3>
                            <p class="text-body-md text-on-primary/80">Membangun dan memelihara infrastruktur dasar yang mendukung aksesibilitas pendidikan, kesehatan, dan jalur ekonomi masyarakat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>