<?php
$perangkat = $pekon['perangkat'];
$kepalaFoto = $pekon['kepala_pekon']['foto'] ?? '';
$kepalaFotoAbs = dirname(__DIR__, 2) . '/' . ltrim($kepalaFoto, '/');
if ($kepalaFoto === '' || !file_exists($kepalaFotoAbs)) $kepalaFoto = '';
?>
<div class="flex flex-col w-full">
<div class="w-full bg-surface pb-section-padding pt-16">
<div class="max-w-container-max mx-auto px-gutter">
<nav class="flex items-center gap-2 text-label-sm text-slate-text-muted mb-8 uppercase tracking-wider">
<a class="hover:text-primary transition-colors" href="index">Beranda</a>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
<span class="text-on-surface font-bold">Pemerintahan</span>
</nav>
<div class="max-w-3xl">
<h1 class="font-display text-display text-on-surface mb-6">Struktur Organisasi &amp; Tata Kelola Pemerintahan Pekon Gunung Megang</h1>
<p class="text-body-lg text-slate-text-muted leading-relaxed">Mengenal perangkat pekon yang bertugas melayani masyarakat Pekon Gunung Megang, berkomitmen pada transparansi dan pelayanan publik prima.</p>
</div>
</div>
</div>
<section class="w-full bg-surface-container-low py-section-padding">
<div class="max-w-container-max mx-auto px-gutter">
<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-border-neutral p-8 md:p-12">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
<div class="lg:col-span-4 relative">
<div class="aspect-[4/5] rounded-xl overflow-hidden relative border border-border-neutral bg-surface-variant">
<?php if ($kepalaFoto !== ''): ?>
<img alt="<?= htmlspecialchars($pekon['kepala_pekon']['nama']) ?> - Kepala Pekon Gunung Megang" class="w-full h-full object-cover cursor-zoom-in" src="<?= $kepalaFoto ?>" data-preview="<?= $kepalaFoto ?>"/>
<?php else: ?>
<div class="w-full h-full flex items-center justify-center bg-surface-container"><span class="material-symbols-outlined text-[80px] text-on-surface-variant">account_circle</span></div>
<?php endif; ?>
<div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
<div class="inline-block bg-primary text-on-primary text-label-sm px-3 py-1 rounded-full uppercase tracking-wider mb-2 border border-primary-container">Kepala Pekon</div>
</div>
</div>
<div class="absolute -bottom-6 -right-6 w-32 h-32 bg-primary-fixed/20 rounded-full blur-3xl -z-10"></div>
</div>
<div class="lg:col-span-8 flex flex-col justify-center">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-2"><?= htmlspecialchars($pekon['kepala_pekon']['nama']) ?></h2>
<p class="text-body-md text-primary font-bold uppercase tracking-widest mb-6"><?= htmlspecialchars($pekon['kepala_pekon']['jabatan']) ?></p>
<div class="relative">
<span class="material-symbols-outlined text-6xl text-surface-container-high absolute -top-4 -left-4 -z-10">format_quote</span>
<p class="text-body-lg text-slate-text-muted leading-relaxed italic mb-8 relative z-10 pl-4 border-l-4 border-primary">"<?= htmlspecialchars($pekon['kepala_pekon']['sambutan'] ?? '') ?>"</p>
</div>
<div class="flex gap-4">
<a class="inline-flex items-center justify-center bg-primary text-on-primary rounded-full px-6 py-3 font-label-sm uppercase tracking-wider shadow-md hover:-translate-y-0.5 transition-transform duration-300" href="kontak">
<span class="material-symbols-outlined mr-2 text-[20px]">calendar_month</span>Jadwal Audiensi
</a>
</div>
</div>
</div>
</div>
</div>
</section>
<section class="w-full bg-surface py-section-padding">
<div class="max-w-container-max mx-auto px-gutter">
<div class="text-center mb-16">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Struktur Organisasi</h2>
<p class="text-body-lg text-slate-text-muted max-w-2xl mx-auto">Susunan perangkat pelaksana kegiatan pemerintahan di tingkat desa.</p>
</div>
<div class="flex flex-col items-center gap-card-gap">
<div class="w-full max-w-sm">
<div class="bg-surface-container-lowest rounded-xl p-6 text-center border border-primary shadow-sm hover:shadow-md transition-shadow relative">
<div class="w-16 h-16 rounded-full bg-primary mx-auto mb-4 flex items-center justify-center text-on-primary">
<span class="material-symbols-outlined text-3xl">star</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-1"><?= htmlspecialchars($pekon['kepala_pekon']['nama']) ?></h3>
<p class="text-label-sm text-primary uppercase tracking-widest"><?= htmlspecialchars($pekon['kepala_pekon']['jabatan']) ?></p>
<div class="absolute -bottom-10 left-1/2 w-px h-10 bg-border-neutral -translate-x-1/2 hidden md:block"></div>
</div>
</div>
<div class="w-full max-w-md hidden md:block">
<div class="h-px w-full bg-border-neutral mx-auto relative">
<div class="absolute -top-10 left-1/2 w-px h-10 bg-border-neutral -translate-x-1/2"></div>
<div class="absolute top-0 left-1/2 w-px h-10 bg-border-neutral -translate-x-1/2"></div>
</div>
</div>
<div class="w-full max-w-sm">
<div class="bg-surface-container-lowest rounded-xl p-6 text-center border border-border-neutral shadow-sm hover:shadow-md transition-shadow relative">
<div class="w-12 h-12 rounded-full bg-secondary-container mx-auto mb-4 flex items-center justify-center text-on-secondary-container">
<span class="material-symbols-outlined text-2xl">edit_document</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-1"><?= htmlspecialchars($perangkat[0]['nama']) ?></h3>
<p class="text-label-sm text-slate-text-muted uppercase tracking-widest"><?= htmlspecialchars($perangkat[0]['jabatan']) ?></p>
<div class="absolute -bottom-10 left-1/2 w-px h-10 bg-border-neutral -translate-x-1/2 hidden md:block"></div>
</div>
</div>
<div class="w-full max-w-4xl hidden md:block">
<div class="h-px w-full bg-border-neutral mx-auto relative">
<div class="absolute -top-10 left-1/2 w-px h-10 bg-border-neutral -translate-x-1/2"></div>
<div class="absolute top-0 left-0 w-px h-10 bg-border-neutral"></div>
<div class="absolute top-0 left-1/5 w-px h-10 bg-border-neutral"></div>
<div class="absolute top-0 left-2/5 w-px h-10 bg-border-neutral"></div>
<div class="absolute top-0 left-3/5 w-px h-10 bg-border-neutral"></div>
<div class="absolute top-0 left-4/5 w-px h-10 bg-border-neutral"></div>
<div class="absolute top-0 right-0 w-px h-10 bg-border-neutral"></div>
</div>
</div>
<div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6">
<?php foreach (array_slice($perangkat, 1, 6) as $p): ?>
<div class="bg-surface-container-lowest rounded-xl p-5 text-center border border-border-neutral shadow-sm hover:-translate-y-1 transition-transform">
<h3 class="font-bold text-on-surface mb-1"><?= htmlspecialchars($p['nama']) ?></h3>
<p class="text-xs text-slate-text-muted uppercase tracking-wider"><?= htmlspecialchars($p['jabatan']) ?></p>
</div>
<?php endforeach; ?>
</div>
</div>
</div>
</section>
<section class="w-full bg-surface-container-low py-section-padding border-t border-border-neutral">
<div class="max-w-container-max mx-auto px-gutter">
<div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
<div class="lg:col-span-1">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Lembaga Desa</h2>
<p class="text-body-md text-slate-text-muted mb-8">Badan independen dan lembaga kemasyarakatan yang bermitra dengan pemerintah pekon untuk pemberdayaan masyarakat.</p>
<div class="flex flex-col gap-4">
<?php
$lembaga = [];
foreach ($perangkat as $p) {
    $j = strtoupper($p['jabatan']);
    if (strpos($j, 'BHP') !== false || strpos($j, 'LPM') !== false) {
        $lembaga[] = $p;
    }
}
$lembagaIcons = ['groups', 'diversity_3'];
foreach ($lembaga as $i => $l):
?>
<div class="bg-surface-container-lowest p-6 rounded-xl border border-border-neutral flex items-start gap-4">
<div class="w-10 h-10 rounded bg-tertiary-fixed flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-on-tertiary-fixed"><?= $lembagaIcons[$i % 2] ?></span>
</div>
<div>
<h4 class="font-bold text-on-surface"><?= htmlspecialchars($l['nama']) ?></h4>
<p class="text-sm text-slate-text-muted"><?= htmlspecialchars($l['jabatan']) ?></p>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
<div class="lg:col-span-2">
<div class="bg-surface-container-lowest rounded-xl border border-border-neutral overflow-hidden shadow-sm">
<div class="px-6 py-4 bg-surface border-b border-border-neutral flex justify-between items-center">
<h3 class="font-headline-md text-on-surface">Daftar Lengkap Personil</h3>
<span class="material-symbols-outlined text-slate-text-muted">list_alt</span>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="border-b border-border-neutral bg-surface-container-low text-label-sm text-slate-text-muted uppercase tracking-wider">
<th class="p-4 font-semibold">Nama Pejabat</th>
<th class="p-4 font-semibold">Jabatan</th>
<th class="p-4 font-semibold text-right">Status</th>
</tr>
</thead>
<tbody class="text-body-md">
<?php foreach ($perangkat as $p): ?>
<tr class="border-b border-border-neutral hover:bg-surface-container transition-colors"><td class="p-4 font-bold text-on-surface"><?= htmlspecialchars($p['nama']) ?></td><td class="p-4 text-slate-text-muted"><?= htmlspecialchars($p['jabatan']) ?></td><td class="p-4 text-right"><span class="inline-block bg-primary-fixed text-on-primary-fixed-variant text-xs px-2 py-1 rounded">Aktif</span></td></tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</section>
</div>
