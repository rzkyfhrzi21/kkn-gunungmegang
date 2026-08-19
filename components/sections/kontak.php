<div class="flex flex-col w-full">
    <!-- Hero Section -->
    <section class="w-full bg-primary py-section-padding px-gutter relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg height="100%" preserveaspectratio="none" viewbox="0 0 1440 320" width="100%" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,160L48,165.3C96,171,192,181,288,170.7C384,160,480,128,576,128C672,128,768,160,864,165.3C960,171,1056,149,1152,144C1248,139,1344,149,1392,154.7L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" fill="currentColor"></path>
            </svg>
        </div>
        <div class="max-w-container-max mx-auto relative z-10 text-center">
            <h1 class="font-display text-display text-on-primary mb-6">Kontak &amp; Pelayanan Pekon Gunung Megang</h1>
            <p class="font-body-lg text-on-primary/90 max-w-2xl mx-auto">Komitmen Pekon Gunung Megang dalam memberikan pelayanan publik yang cepat, transparan, dan mudah diakses oleh seluruh warga.</p>
        </div>
    </section>
    <!-- Main Content Area -->
    <section class="w-full py-section-padding px-gutter -mt-12">
        <div class="max-w-container-max mx-auto">
            <!-- Direct Contact Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                <!-- WhatsApp -->
                <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow border border-border-neutral flex flex-col items-start gap-4">
                    <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center text-on-primary">
                        <span class="material-symbols-outlined text-[28px]">chat</span>
                    </div>
                    <div>
                        <h3 class="font-headline-md text-on-background mb-2">WhatsApp Resmi</h3>
                        <p class="font-body-md text-slate-text-muted mb-6"><?= htmlspecialchars($pekon['kontak']['wa_desc'] ?? '') ?></p>
                        <?php $cps = $pekon['kontak']['contact_person'] ?? []; ?>
                        <?php if (!empty($cps)): ?>
                            <div class="w-full space-y-3">
                                <?php foreach ($cps as $cp): $cpTel = preg_replace('/\D+/', '', $cp['telepon'] ?? ''); ?>
                                    <a class="w-full inline-flex items-center justify-center gap-2 bg-primary text-on-primary px-6 py-3 rounded-full font-label-sm uppercase tracking-wider hover:bg-primary-container transition-colors" href="https://wa.me/<?= '62' . ltrim($cpTel, '0') ?>" target="_blank" rel="noopener">
                                        <span>Hubungi <?= htmlspecialchars($cp['nama'] ?? '') ?></span>
                                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <a class="inline-flex items-center gap-2 bg-primary text-on-primary px-6 py-3 rounded-full font-label-sm uppercase tracking-wider hover:bg-primary-container transition-colors" href="https://wa.me/<?= '62' . ltrim($pekon['kontak']['telepon'], '0') ?>" target="_blank" rel="noopener">
                                <span>Hubungi Kami</span>
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </a>
                        <?php endif; ?>
                        <p class="mt-4 font-label-sm text-on-surface-variant font-mono"><?= trim(chunk_split($pekon['kontak']['telepon'], 4, ' ')) ?></p>
                        <?php $igUrl = $pekon['kontak']['instagram'] ?? ''; ?>
                        <?php if ($igUrl !== ''): ?>
                            <div class="w-full mt-6 pt-5 border-t border-border-neutral">
                                <a class="inline-flex items-center gap-2 text-primary hover:text-primary-container transition-colors font-label-sm" href="<?= htmlspecialchars($igUrl) ?>" target="_blank" rel="noopener">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.9.2 2.3.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1.1.4 2.3.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 1.9-.4 2.3-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1.1.4-2.3.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.9-.2-2.3-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1.1-.4-2.3-.1-1.3-.1-1.7-.1-4.9s0-3.6.1-4.9c.1-1.2.2-1.9.4-2.3.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1.1-.4 2.3-.4 1.3-.1 1.7-.1 4.9-.1zM12 0C8.7 0 8.3 0 7 .1 5.7.2 4.8.4 4 .7c-.9.3-1.6.8-2.4 1.5C.9 3 .4 3.7.1 4.6c-.3.8-.5 1.7-.6 3C-.1 8.3-.1 8.7-.1 12s0 3.7.1 5c.1 1.3.3 2.2.6 3 .3.9.8 1.6 1.5 2.4.8.8 1.5 1.2 2.4 1.5.8.3 1.7.5 3 .6 1.3.1 1.7.1 5 .1s3.7 0 5-.1c1.3-.1 2.2-.3 3-.6.9-.3 1.6-.8 2.4-1.5.8-.8 1.2-1.5 1.5-2.4.3-.8.5-1.7.6-3 .1-1.3.1-1.7.1-5s0-3.7-.1-5c-.1-1.3-.3-2.2-.6-3-.3-.9-.8-1.6-1.5-2.4C20.1.9 19.4.4 18.5.1c-.8-.3-1.7-.5-3-.6C14.2-.1 13.8-.1 12 0zm0 5.8a6.2 6.2 0 1 0 0 12.4 6.2 6.2 0 0 0 0-12.4zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm7.8-10.4a1.4 1.4 0 1 1-2.8 0 1.4 1.4 0 0 1 2.8 0z"/>
                                    </svg>
                                    <span><?= htmlspecialchars(preg_replace('#^https?://(www\.)?instagram\.com/([^/]+)/?$#i', '@$2', $igUrl)) ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Jam Operasional -->
                <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow border border-border-neutral flex flex-col items-start gap-4">
                    <div class="w-14 h-14 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-container">
                        <span class="material-symbols-outlined text-[28px]">schedule</span>
                    </div>
                    <div>
                        <h3 class="font-headline-md text-on-background mb-2">Jam Operasional</h3>
                        <p class="font-body-md text-slate-text-muted mb-4"><?= htmlspecialchars($pekon['kontak']['jam_desc'] ?? '') ?></p>
                        <ul class="space-y-3 font-body-md text-on-surface">
                            <?php $jamRows = $pekon['kontak']['jam'] ?? [];
                            $jamN = count($jamRows); ?>
                            <?php foreach ($jamRows as $i => $jr): ?>
                                <li class="flex justify-between <?= $i < $jamN - 1 ? 'border-b border-border-neutral pb-2' : '' ?> <?= !empty($jr['status']) ? 'text-error' : '' ?>"><span class="font-semibold"><?= htmlspecialchars($jr['hari'] ?? '') ?></span><span><?= htmlspecialchars(($jr['jam'] ?? '') !== '' ? $jr['jam'] : ($jr['status'] ?? '')) ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <!-- Alamat -->
                <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow border border-border-neutral flex flex-col items-start gap-4">
                    <div class="w-14 h-14 rounded-full bg-tertiary-container flex items-center justify-center text-on-tertiary-container">
                        <span class="material-symbols-outlined text-[28px]">location_on</span>
                    </div>
                    <div>
                        <h3 class="font-headline-md text-on-background mb-2">Alamat Kantor</h3>
                        <p class="font-body-md text-slate-text-muted mb-4 leading-relaxed"><?= htmlspecialchars($pekon['kontak']['maps_code']) ?></p>
                        <div class="bg-surface-container-low p-4 rounded-xl border border-border-neutral flex items-start gap-3 mt-auto">
                            <span class="material-symbols-outlined text-primary mt-0.5">info</span>
                            <p class="font-body-md text-sm text-on-surface-variant"><strong>Aksesibilitas:</strong><br />±<?= $pekon['demografi']['jarak_kecamatan_km'] ?> km / <?= $pekon['demografi']['waktu_kecamatan_menit'] ?> menit dari pusat kecamatan. <?= htmlspecialchars($pekon['kontak']['akses'] ?? '') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Form & Map Split -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-16">
                <!-- Form -->
                <div class="lg:col-span-5 bg-surface-container-lowest rounded-3xl p-8 shadow-md border border-border-neutral">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="material-symbols-outlined text-[32px] text-primary" style="font-variation-settings: 'FILL' 1;">assignment</span>
                        <h2 class="font-headline-lg text-on-background">Pengaduan &amp; Aspirasi</h2>
                    </div>
                    <p class="font-body-md text-slate-text-muted mb-8"><?= htmlspecialchars($pekon['kontak']['aspirasi_desc'] ?? '') ?></p>
                    <form class="space-y-6" id="aspirasiForm">
                        <div class="space-y-2">
                            <label class="font-label-sm text-on-surface-variant block uppercase tracking-wider" for="nama">Nama Lengkap</label>
                            <input class="w-full bg-surface border border-border-neutral rounded-lg px-4 py-3 font-body-md text-on-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" id="nama" name="nama" placeholder="Masukkan nama Anda" type="text" />
                        </div>
                        <div class="space-y-2">
                            <label class="font-label-sm text-on-surface-variant block uppercase tracking-wider" for="telepon">Nomor WhatsApp</label>
                            <input class="w-full bg-surface border border-border-neutral rounded-lg px-4 py-3 font-body-md text-on-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" id="telepon" name="telepon" placeholder="08xxxxxxxxxx" type="tel" />
                        </div>
                        <div class="space-y-2">
                            <label class="font-label-sm text-on-surface-variant block uppercase tracking-wider" for="subjek">Kategori</label>
                            <select class="w-full bg-surface border border-border-neutral rounded-lg px-4 py-3 font-body-md text-on-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all appearance-none" id="subjek" name="subjek">
                                <option disabled="" selected="" value="">Pilih kategori laporan...</option>
                                <option value="infrastruktur">Infrastruktur &amp; Pembangunan</option>
                                <option value="pelayanan">Pelayanan Administrasi</option>
                                <option value="keamanan">Keamanan &amp; Ketertiban</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="font-label-sm text-on-surface-variant block uppercase tracking-wider" for="pesan">Pesan / Laporan</label>
                            <textarea class="w-full bg-surface border border-border-neutral rounded-lg px-4 py-3 font-body-md text-on-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none" id="pesan" name="pesan" placeholder="Tuliskan detail laporan Anda di sini..." rows="4"></textarea>
                        </div>
                        <button class="w-full bg-primary text-on-primary py-4 rounded-xl font-label-sm uppercase tracking-wider hover:bg-primary-container transition-all flex items-center justify-center gap-2 shadow-sm hover:shadow-md" type="submit">
                            <span>Kirim Laporan</span>
                            <span class="material-symbols-outlined text-[20px]">send</span>
                        </button>
                        <div id="aspirasiMsg" class="hidden font-body-md text-sm rounded-lg px-4 py-3" role="alert"></div>
                    </form>
                </div>
                <!-- Map -->
                <div class="lg:col-span-7 bg-surface-container-lowest rounded-3xl p-6 shadow-md border border-border-neutral flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="font-headline-md text-on-background">Peta Lokasi</h2>
                        <a class="inline-flex items-center gap-2 bg-surface text-on-surface border border-border-neutral px-4 py-2 rounded-full font-label-sm hover:bg-surface-container transition-colors" href="<?= htmlspecialchars($pekon['kontak']['maps_link']) ?>" target="_blank">
                            <span class="material-symbols-outlined text-[18px]">map</span>
                            <span>Buka di Google Maps</span>
                        </a>
                    </div>
                    <div class="w-full flex-grow min-h-[400px] rounded-2xl overflow-hidden border border-border-neutral relative group">
                        <?php if (!empty($pekon['kontak']['maps_embed'])): ?>
                            <iframe class="w-full h-full absolute inset-0 border-0" src="<?= htmlspecialchars($pekon['kontak']['maps_embed']) ?>" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" title="Peta Lokasi Kantor Pekon Gunung Megang"></iframe>
                        <?php else: ?>
                            <div class="w-full h-full bg-cover bg-center absolute inset-0 transition-transform duration-700 group-hover:scale-105" style="background-image: linear-gradient(135deg, #0b3b4a 0%, #0ea5a4 60%, #0b3b4a 100%)"></div>
                        <?php endif; ?>
                        <div class="absolute bottom-6 left-6 right-6 bg-surface-container-lowest/95 p-4 rounded-xl shadow-lg border border-border-neutral flex items-center gap-4 z-10">
                            <div class="w-12 h-12 rounded-full bg-emerald-surface flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">assured_workload</span>
                            </div>
                            <div>
                                <h4 class="font-label-sm text-on-background mb-1">Kantor <?= htmlspecialchars($pekon['nama'] ?? '') ?></h4>
                                <p class="font-body-md text-sm text-slate-text-muted"><?= htmlspecialchars($pekon['kontak']['map_subtitle'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('aspirasiForm');
        if (form) {
            const msg = document.getElementById('aspirasiMsg');
            const showMsg = (text, ok) => {
                msg.textContent = text;
                msg.className = 'font-body-md text-sm rounded-lg px-4 py-3 ' + (ok ? 'bg-emerald-surface text-primary' : 'bg-error-container text-on-error-container');
            };
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                const originalHTML = btn.innerHTML;
                const nama = form.nama.value.trim();
                const telepon = form.telepon.value.trim();
                const subjek = form.subjek.value;
                const pesan = form.pesan.value.trim();
                if (!nama || !telepon || !subjek || !pesan) {
                    showMsg('Mohon lengkapi semua kolom sebelum mengirim.', false);
                    return;
                }
                btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">sync</span><span>Mengirim...</span>';
                btn.classList.add('opacity-80', 'cursor-not-allowed');
                try {
                    const res = await fetch('functions/function_aspirasi.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            nama,
                            telepon,
                            subjek,
                            pesan
                        })
                    });
                    const j = await res.json().catch(() => null);
                    if (j && j.ok) {
                        showMsg(j.message || 'Laporan berhasil dikirim. Terima kasih!', true);
                        btn.innerHTML = '<span class="material-symbols-outlined text-[20px]">check_circle</span><span>Terkirim!</span>';
                        btn.classList.replace('bg-primary', 'bg-secondary-container');
                        btn.classList.replace('text-on-primary', 'text-on-secondary-container');
                        form.reset();
                        setTimeout(() => {
                            btn.innerHTML = originalHTML;
                            btn.classList.replace('bg-secondary-container', 'bg-primary');
                            btn.classList.replace('text-on-secondary-container', 'text-on-primary');
                            btn.classList.remove('opacity-80', 'cursor-not-allowed');
                            setTimeout(() => {
                                msg.classList.add('hidden');
                            }, 5000);
                        }, 2500);
                    } else {
                        showMsg((j && j.error) || 'Gagal mengirim laporan. Silakan coba lagi.', false);
                        btn.innerHTML = originalHTML;
                        btn.classList.remove('opacity-80', 'cursor-not-allowed');
                    }
                } catch (err) {
                    showMsg('Gagal mengirim laporan. Periksa koneksi internet Anda.', false);
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('opacity-80', 'cursor-not-allowed');
                }
            });
        }
    });
</script>