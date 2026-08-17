<style>.scrollbar-hide::-webkit-scrollbar{display:none}.scrollbar-hide{scrollbar-width:none}</style>
<div class="flex flex-col w-full bg-white text-on-surface min-h-screen">
<?php
$layItems  = $pekon['layanan_umkm']['daftar'] ?? [];
$layKategoris = array_values(array_unique(array_map(function ($i) { return $i['kategori'] ?? ''; }, $layItems)));
$layHeroSub = 'Layanan & UMKM';
$layHeroJudul = 'Eksplorasi Layanan, UMKM & Fasilitas Pekon';
$layHeroDesc  = 'Temukan ragam potensi lokal, produk unggulan UMKM, serta fasilitas layanan masyarakat yang tersedia di Pekon Gunung Megang.';
$layWaLink = function ($wa) {
    $wa = preg_replace('/\D+/', '', (string)$wa);
    if ($wa !== '' && strpos($wa, '0') === 0) {
        $wa = '62' . substr($wa, 1);
    }
    return $wa !== '' ? 'https://wa.me/' . $wa : '';
};
?>
<!-- Layanan & UMKM Header Section -->
<section class="w-full pt-16 pb-8 px-gutter max-w-container-max mx-auto">
<div class="flex items-center gap-3 mb-6">
<div class="h-px w-8 bg-secondary"></div>
<span class="text-[12px] font-bold tracking-[0.2em] uppercase text-secondary"><?= htmlspecialchars($layHeroSub) ?></span>
</div>
<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 mb-12">
<div class="max-w-2xl">
<h1 class="font-display text-4xl lg:text-5xl text-on-surface leading-tight mb-4" style="font-family: 'Playfair Display', serif;"><?= htmlspecialchars($layHeroJudul) ?></h1>
<p class="text-body-lg text-slate-text-muted"><?= htmlspecialchars($layHeroDesc) ?></p>
</div>
<!-- Refined Search Bar -->
<div class="w-full lg:w-96 relative">
<input id="cari-layanan" class="w-full bg-surface border border-border-neutral text-on-surface placeholder:text-slate-text-muted/60 rounded-full py-4 pl-6 pr-12 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors font-body-md shadow-sm" placeholder="Cari layanan atau produk..." type="text"/>
<button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-primary hover:text-primary-container transition-colors" aria-label="Cari">
<span class="material-symbols-outlined text-[24px]">search</span>
</button>
</div>
</div>
<!-- Category Tabs -->
<div class="flex items-center gap-3 overflow-x-auto pb-4 scrollbar-hide border-b border-border-neutral" id="tab-layanan">
<button type="button" data-kategori="" class="whitespace-nowrap px-6 py-2.5 rounded-full bg-primary text-white text-label-sm font-bold transition-transform hover:scale-105">Semua Kategori</button>
<?php foreach ($layKategoris as $kat): ?>
<button type="button" data-kategori="<?= htmlspecialchars($kat) ?>" class="whitespace-nowrap px-6 py-2.5 rounded-full border border-border-neutral text-slate-text-muted text-label-sm hover:bg-surface-container hover:border-primary transition-all"><?= htmlspecialchars($kat) ?></button>
<?php endforeach; ?>
</div>
</section>
<!-- Layanan & UMKM Grid -->
<section class="w-full pb-section-padding px-gutter max-w-container-max mx-auto">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="grid-layanan">
<?php foreach ($layItems as $i => $item):
    $foto = $item['foto'] ?? '';
    $searchHay = strtolower(implode(' ', array_filter([$item['kategori'] ?? '', $item['badge'] ?? '', $item['nama'] ?? '', $item['subjudul'] ?? ''])));
    $layMaps = $item['maps'] ?? '';
    $layWa = $layWaLink($item['wa'] ?? '');
?>
<!-- Card <?= $i + 1 ?> -->
<div class="group flex flex-col bg-surface border border-border-neutral rounded-xl overflow-hidden hover:border-primary/50 transition-all duration-300 shadow-md" data-item="<?= htmlspecialchars($item['kategori'] ?? '') ?>" data-search="<?= htmlspecialchars($searchHay) ?>"<?= $i >= 6 ? ' data-more="1"' : '' ?>>
<div class="relative h-56 w-full overflow-hidden">
<?php if ($foto !== ''): ?>
<img alt="<?= htmlspecialchars($item['nama'] ?? '') ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 cursor-pointer" src="<?= htmlspecialchars($foto) ?>" data-preview="<?= htmlspecialchars($foto) ?>" loading="lazy"/>
<?php else: ?>
<div class="w-full h-full flex items-center justify-center" style="background-image:linear-gradient(135deg,#0b3b4a 0%,#0ea5a4 60%,#0b3b4a 100%)">
<span class="material-symbols-outlined text-[48px] text-white/70">storefront</span>
</div>
<?php endif; ?>
<div class="absolute top-4 left-4 bg-white/90 px-3 py-1 rounded border border-border-neutral text-[10px] font-bold text-primary tracking-wider uppercase"><?= htmlspecialchars($item['badge'] ?? ($item['kategori'] ?? '')) ?></div>
</div>
<div class="p-6 flex flex-col flex-grow">
<h3 class="text-2xl text-on-surface mb-1" style="font-family: 'Playfair Display', serif;"><?= htmlspecialchars($item['nama'] ?? '') ?></h3>
<p class="text-secondary text-sm font-body-md mb-4"><?= htmlspecialchars($item['subjudul'] ?? '') ?></p>
<div class="flex flex-col gap-2 mb-6 text-slate-text-muted text-sm font-body-md">
<?php foreach (($item['baris'] ?? []) as $baris): ?>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]"><?= htmlspecialchars($baris['ikon'] ?? '') ?></span>
<span><?= htmlspecialchars($baris['teks'] ?? '') ?></span>
</div>
<?php endforeach; ?>
</div>
<div class="mt-auto grid grid-cols-2 gap-3">
<?php if ($layMaps !== ''): ?>
<a href="<?= htmlspecialchars($layMaps) ?>" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 py-3 bg-primary text-white rounded hover:bg-primary-container transition-colors font-label-sm">
<span class="material-symbols-outlined text-[18px]">location_on</span>
Google Maps
</a>
<?php endif; ?>
<?php if ($layWa !== ''): ?>
<a href="<?= htmlspecialchars($layWa) ?>" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 py-3 bg-[#25D366] text-white rounded hover:opacity-90 transition-opacity font-label-sm">
<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
WhatsApp
</a>
<?php endif; ?>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
<!-- Pagination or Load More -->
<div class="mt-16 flex justify-center" id="wrap-muat-lain">
<button type="button" id="btn-muat-lain" class="flex items-center gap-2 text-primary hover:text-primary-container transition-colors font-label-sm border-b border-primary pb-1">
<span>Muat Lebih Banyak</span>
<span class="material-symbols-outlined text-[18px]">expand_more</span>
</button>
</div>
</section>
</div>
<script>
(function () {
    var grid = document.getElementById('grid-layanan');
    var search = document.getElementById('cari-layanan');
    var tabs = Array.prototype.slice.call(document.querySelectorAll('#tab-layanan button'));
    var moreBtn = document.getElementById('btn-muat-lain');
    var wrapMore = document.getElementById('wrap-muat-lain');
    var cards = Array.prototype.slice.call(grid.querySelectorAll('[data-item]'));
    var activeCat = '';
    var moreShown = false;

    function apply() {
        var q = search ? search.value.trim().toLowerCase() : '';
        var filtering = (q !== '' || activeCat !== '');
        cards.forEach(function (card) {
            var okCat = !activeCat || card.getAttribute('data-item') === activeCat;
            var okQ = !q || (card.getAttribute('data-search') || '').indexOf(q) !== -1;
            var visible = okCat && okQ;
            if (visible && !filtering && !moreShown && card.getAttribute('data-more') === '1') visible = false;
            card.style.display = visible ? '' : 'none';
        });
        if (wrapMore) wrapMore.style.display = (filtering || moreShown || cards.length <= 6) ? 'none' : '';
    }

    function setActive(btn) {
        tabs.forEach(function (b) {
            b.className = 'whitespace-nowrap px-6 py-2.5 rounded-full border border-border-neutral text-slate-text-muted text-label-sm hover:bg-surface-container hover:border-primary transition-all';
        });
        btn.className = 'whitespace-nowrap px-6 py-2.5 rounded-full bg-primary text-white text-label-sm font-bold transition-transform hover:scale-105';
    }

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            setActive(btn);
            activeCat = btn.getAttribute('data-kategori') || '';
            apply();
        });
    });
    if (search) search.addEventListener('input', apply);
    if (moreBtn) moreBtn.addEventListener('click', function () {
        moreShown = true;
        apply();
    });
    apply();
})();
</script>