<div class="flex flex-col w-full font-body-md text-on-background">
    <!-- Hero Section -->
    <section class="w-full relative px-gutter py-section-padding bg-surface-container overflow-hidden">
        <div class="absolute top-0 right-0 -mt-16 -mr-16 w-96 h-96 bg-primary-fixed/20 rounded-full mix-blend-multiply blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -mb-16 -ml-16 w-64 h-64 bg-secondary-fixed/20 rounded-full mix-blend-multiply blur-3xl opacity-50"></div>
        <div class="max-w-container-max mx-auto relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-card-gap items-center">
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-1 bg-secondary rounded-full"></span>
                    <span class="text-label-sm uppercase tracking-widest text-secondary font-bold">Profil Wilayah</span>
                </div>
                <h1 class="text-display text-on-surface">Potensi &amp; Ekonomi <span class="text-primary block">Desa Gunung Megang</span></h1>
                <p class="text-body-lg text-on-surface-variant max-w-xl"><?= htmlspecialchars($pekon['potensi']['hero_desc'] ?? '') ?></p>
                <div class="pt-4">
                    <a class="bg-primary text-on-primary px-8 py-4 rounded-full text-label-sm hover:bg-primary-container transition-all shadow-md flex items-center w-fit gap-3 group" href="#komoditas">
                        Jelajahi Potensi
                        <span class="material-symbols-outlined text-on-primary group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                </div>
            </div>
            <div class="relative w-full h-[400px] lg:h-[500px] rounded-[2rem] overflow-hidden shadow-xl bg-surface-container-high border border-outline-variant/30 group">
                <div class="w-full h-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('assets/images/potensi-alam.jpeg')"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6">
                    <div class="bg-surface/95 rounded-2xl p-4 shadow-lg border border-outline-variant/50 backdrop-blur-md">
                        <div class="flex items-center justify-between">
                            <span class="text-label-sm font-bold text-on-surface">Luas Wilayah</span>
                            <span class="text-headline-md font-bold text-primary"><?= number_format($pekon['demografi']['luas_wilayah_ha'], 0, ',', '.') ?> Ha</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Komoditas Unggulan -->
    <section class="w-full px-gutter py-section-padding bg-surface-container-lowest" id="komoditas">
        <div class="max-w-container-max mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div>
                    <h2 class="text-headline-lg text-on-surface mb-4">Komoditas Unggulan</h2>
                    <p class="text-body-md text-on-surface-variant max-w-2xl"><?= htmlspecialchars($pekon['potensi']['komoditas_desc'] ?? '') ?></p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-card-gap">
                <?php
                $komColor = ['bg-primary text-on-primary', 'bg-secondary text-on-secondary', 'bg-tertiary text-on-tertiary'];
                $komText  = ['text-primary', 'text-secondary', 'text-tertiary'];
                $komBg    = ['bg-primary-fixed/30', 'bg-secondary-fixed/30', 'bg-tertiary-fixed/30'];
                foreach ($pekon['potensi']['komoditas'] as $ki => $kom):
                    $color = $komColor[$ki % 3] ?? $komColor[0];
                    $text  = $komText[$ki % 3] ?? $komText[0];
                    $bg    = $komBg[$ki % 3] ?? $komBg[0];
                ?>
                    <div class="bg-surface rounded-3xl p-8 border border-outline-variant/40 shadow-sm hover:shadow-md transition-all group flex flex-col h-full relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 <?= $bg ?> rounded-bl-full -z-10 transition-transform group-hover:scale-110"></div>
                        <div class="w-16 h-16 rounded-2xl <?= $color ?> flex items-center justify-center mb-6 shadow-md">
                            <span class="material-symbols-outlined text-[32px]"><?= htmlspecialchars($kom['ikon'] ?? 'eco') ?></span>
                        </div>
                        <h3 class="text-headline-md text-on-surface mb-3"><?= htmlspecialchars($kom['nama'] ?? '') ?></h3>
                        <p class="text-body-md text-on-surface-variant mb-8 flex-grow"><?= htmlspecialchars($kom['deskripsi'] ?? '') ?></p>
                        <div class="flex items-baseline gap-2 pt-6 border-t border-outline-variant/30 mt-auto">
                            <span class="text-display <?= $text ?>"><?= (int)($kom['nilai'] ?? 0) ?></span>
                            <span class="text-label-sm text-on-surface-variant uppercase tracking-widest font-bold"><?= htmlspecialchars($kom['satuan'] ?? '') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- Mata Pencaharian & Indeks Desa Membangun (IDM) -->
    <section class="w-full px-gutter py-section-padding bg-surface-container">
        <div class="max-w-container-max mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-card-gap">
                <div class="lg:col-span-7 bg-surface rounded-[2rem] p-8 md:p-10 border border-outline-variant/40 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-headline-md text-on-surface">Mata Pencaharian Utama</h2>
                        <span class="material-symbols-outlined text-primary text-3xl">work</span>
                    </div>
                    <p class="text-body-md text-on-surface-variant mb-10"><?= htmlspecialchars($pekon['potensi']['mp_desc'] ?? '') ?></p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php
                        $mpIcons = ['local_florist', 'storefront', 'engineering', 'domain'];
                        $mpBadges = ['bg-primary-fixed text-on-primary-fixed', 'bg-secondary-fixed text-on-secondary-fixed', 'bg-tertiary-fixed text-on-tertiary-fixed', 'bg-surface-variant text-on-surface-variant'];
                        $mpHovers = ['hover:bg-primary-fixed/10 hover:border-primary-fixed/30', 'hover:bg-secondary-fixed/10 hover:border-secondary-fixed/30', 'hover:bg-tertiary-fixed/10 hover:border-tertiary-fixed/30', 'hover:bg-surface-variant/30 hover:border-outline-variant/30'];
                        foreach ($pekon['potensi']['mata_pencaharian'] as $i => $m):
                        ?>
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-surface-container <?= $mpHovers[$i % 4] ?> transition-colors border border-transparent">
                                <div class="w-12 h-12 rounded-full <?= $mpBadges[$i % 4] ?> flex items-center justify-center">
                                    <span class="material-symbols-outlined"><?= $mpIcons[$i % 4] ?></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-label-sm font-bold text-on-surface"><?= htmlspecialchars($m['nama'] ?? '') ?></span>
                                    <span class="text-body-md text-on-surface-variant"><?= htmlspecialchars($m['keterangan'] ?? '') ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="lg:col-span-5 bg-primary rounded-[2rem] p-8 md:p-10 shadow-lg relative overflow-hidden flex flex-col justify-between text-on-primary">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-primary-container rounded-full mix-blend-screen opacity-50 blur-2xl -translate-y-1/2 translate-x-1/4"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-primary-fixed rounded-full mix-blend-overlay opacity-30 blur-xl translate-y-1/4 -translate-x-1/4"></div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="flex justify-between items-start mb-8">
                            <div class="inline-flex items-center gap-2 bg-primary-container/80 backdrop-blur-sm border border-primary-fixed/20 px-4 py-2 rounded-full">
                                <span class="w-2 h-2 rounded-full bg-primary-fixed animate-pulse"></span>
                                <span class="text-label-sm uppercase tracking-widest text-primary-fixed font-bold">Indeks Desa Membangun</span>
                            </div>
                            <span class="material-symbols-outlined text-4xl opacity-50">trending_up</span>
                        </div>
                        <div class="mb-auto">
                            <h3 class="text-label-sm uppercase tracking-widest text-primary-fixed/80 mb-2">Status <?= $pekon['tahun'] ?></h3>
                            <div class="text-display font-bold leading-none mb-6">Desa<br /><?= htmlspecialchars($pekon['potensi']['idm_status'] ?? '') ?></div>
                            <p class="text-body-md text-on-primary/90"><?= htmlspecialchars($pekon['potensi']['idm_desc'] ?? '') ?></p>
                        </div>
                        <div class="mt-8 pt-6 border-t border-primary-container/50">
                            <div class="w-full bg-primary-container h-3 rounded-full overflow-hidden mb-3">
                                <div class="bg-primary-fixed h-full rounded-full relative" style="width: <?= (int)($pekon['potensi']['idm_progress'] ?? 0) ?>%">
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent to-white/30"></div>
                                </div>
                            </div>
                            <div class="flex justify-between text-label-sm text-on-primary/70">
                                <span>Tertinggal</span>
                                <span class="text-primary-fixed font-bold"><?= htmlspecialchars($pekon['potensi']['idm_status'] ?? '') ?></span>
                                <span>Maju</span>
                                <span>Mandiri</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Agama & Sosial -->
    <section class="w-full px-gutter py-section-padding bg-surface-container-lowest border-t border-outline-variant/20">
        <div class="max-w-container-max mx-auto">
            <div class="flex flex-col md:flex-row items-center gap-12 bg-surface rounded-[2rem] p-8 md:p-12 border border-outline-variant/30 shadow-sm">
                <div class="w-full md:w-1/3 aspect-[4/3] md:aspect-square max-w-[340px] rounded-3xl overflow-hidden shadow-md relative border border-outline-variant/40 shrink-0">
                    <img src="assets/images/berita1.jpg" alt="<?= htmlspecialchars($pekon['potensi']['sosial_judul'] ?? 'Sosialisasi Pencegahan Korupsi') ?>" class="w-full h-full object-cover cursor-zoom-in hover:scale-105 transition-transform duration-500" data-preview="assets/images/berita1.jpg" loading="lazy">
                </div>
                <div class="w-full md:w-2/3 flex flex-col justify-center">
                    <div class="inline-flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-secondary">diversity_3</span>
                        <span class="text-label-sm uppercase tracking-widest text-secondary font-bold">Kehidupan Sosial</span>
                    </div>
                    <h2 class="text-headline-lg text-on-surface mb-6"><?= htmlspecialchars($pekon['potensi']['sosial_judul'] ?? '') ?></h2>
                    <div class="prose prose-lg text-body-lg text-on-surface-variant flex flex-col gap-4">
                        <p class="leading-relaxed"><?= nl2br(htmlspecialchars($pekon['potensi']['sosial_par1'] ?? '')) ?></p>
                        <p class="leading-relaxed"><?= nl2br(htmlspecialchars($pekon['potensi']['sosial_par2'] ?? '')) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>