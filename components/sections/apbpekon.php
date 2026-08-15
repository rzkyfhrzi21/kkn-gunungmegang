<?php
$apbList = $pekon['apbpekon'] ?? [];
$apbYears = array_keys($apbList);
$apbTahun = (int)($apbYears ? max($apbYears) : 0);
$apb = $apbList[$apbTahun] ?? [];
function apb_fmt($v) { return 'Rp ' . number_format($v, 0, ',', '.'); }
function apb_pct($v, $t) { return $t > 0 ? $v / $t * 100 : 0; }
$pendapatanLabels = [
    'alokasi_dana_pekon' => 'Alokasi Dana Pekon (ADD)',
    'dana_desa'          => 'Dana Desa (DD)',
    'bagi_hasil_pajak'   => 'Bagi Hasil Pajak & Retribusi (BHPRD)',
    'bantuan_provinsi'   => 'Bantuan Provinsi',
    'pendapatan_lain'    => 'Pendapatan Lain-lain',
];
$belanjaLabels = [
    'penyelenggaraan_pemerintahan' => ['Penyelenggaraan Pemerintahan', 'Operasional, penghasilan perangkat, LPMD, BHP', 'bg-primary'],
    'pembangunan_pekon'            => ['Pembangunan Pekon', 'Infrastruktur jalan, gedung, drainase, sarana umum', 'bg-secondary'],
    'pembinaan_kemasyarakatan'     => ['Pembinaan Kemasyarakatan', 'PKK, Karang Taruna, keagamaan, keamanan', 'bg-tertiary'],
    'pemberdayaan_masyarakat'      => ['Pemberdayaan Masyarakat', 'BUMDes, pelatihan, ekonomi produktif', 'bg-surface-tint'],
    'penanggulangan_bencana'       => ['Penanggulangan Bencana', 'Kesiapsiagaan dan penanganan bencana', 'bg-error'],
];
$pembiayaanLabels = [
    'penerimaan'       => 'Penerimaan Pembiayaan',
    'pengeluaran'      => 'Pengeluaran Pembiayaan',
    'pembiayaan_netto' => 'Pembiayaan Netto',
    'silpa'            => 'SILPA',
];
?>
<div class="flex flex-col w-full">
<!-- Page Header -->
<div class="w-full bg-primary py-section-padding px-gutter relative overflow-hidden">
  <div class="max-w-container-max mx-auto relative z-10">
    <nav class="flex items-center gap-2 text-label-sm text-on-primary/70 mb-6 uppercase tracking-wider">
      <a class="hover:text-on-primary transition-colors" href="index">Beranda</a>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <span class="text-on-primary font-bold">APB Pekon <?= $apbTahun ?></span>
    </nav>
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
      <div>
        <span class="inline-block mb-4 bg-secondary text-on-secondary text-label-sm px-4 py-1.5 rounded-full uppercase tracking-wider">Transparansi Anggaran</span>
        <h1 class="font-display text-display text-on-primary mb-4">APB Pekon <?= $apbTahun ?></h1>
        <p class="font-body-lg text-on-primary/90 max-w-2xl">Anggaran Pendapatan dan Belanja Pekon Gunung Megang Tahun Anggaran <?= $apbTahun ?> — diterbitkan secara terbuka sebagai wujud komitmen tata kelola yang transparan dan akuntabel.</p>
      </div>
      <div class="flex-shrink-0 bg-primary-container/50 rounded-2xl p-6 border border-primary-fixed/20 text-center">
        <p class="text-label-sm text-primary-fixed uppercase tracking-wider mb-2">Total Pendapatan</p>
        <p class="font-display text-3xl text-on-primary"><?= apb_fmt($apb['pendapatan']['total']) ?></p>
        <p class="text-body-md text-on-primary/70 mt-1">TA <?= $apbTahun ?></p>
      </div>
    </div>
  </div>
</div>

<!-- Summary Cards -->
<section class="max-w-container-max mx-auto w-full px-gutter py-section-padding">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-section-padding">
    <!-- Pendapatan -->
    <div class="bg-surface rounded-2xl p-6 shadow-sm border border-border-neutral hover:shadow-md transition-shadow">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 bg-primary-fixed/30 rounded-xl flex items-center justify-center">
          <span class="material-symbols-outlined text-primary-container">trending_up</span>
        </div>
        <span class="text-label-sm text-primary-container uppercase tracking-wider font-bold bg-primary-fixed/20 px-3 py-1 rounded-full">Pendapatan</span>
      </div>
      <h3 class="font-display text-3xl text-primary mb-1"><?= apb_fmt($apb['pendapatan']['total']) ?></h3>
      <p class="text-body-md text-slate-text-muted">Total Pendapatan <?= $apbTahun ?></p>
      <div class="mt-4 pt-4 border-t border-border-neutral space-y-2 text-body-md">
        <?php foreach ($pendapatanLabels as $key => $label): ?>
        <div class="flex justify-between"><span class="text-slate-text-muted"><?= $label ?></span><span class="font-semibold"><?= apb_fmt($apb['pendapatan'][$key]) ?></span></div>
        <?php endforeach; ?>
      </div>
    </div>
    <!-- Belanja -->
    <div class="bg-surface rounded-2xl p-6 shadow-sm border border-border-neutral hover:shadow-md transition-shadow">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 bg-secondary-fixed/30 rounded-xl flex items-center justify-center">
          <span class="material-symbols-outlined text-secondary">account_balance_wallet</span>
        </div>
        <span class="text-label-sm text-secondary uppercase tracking-wider font-bold bg-secondary-fixed/20 px-3 py-1 rounded-full">Belanja</span>
      </div>
      <h3 class="font-display text-3xl text-secondary mb-1"><?= apb_fmt($apb['belanja']['total']) ?></h3>
      <p class="text-body-md text-slate-text-muted">Total Belanja <?= $apbTahun ?></p>
      <div class="mt-4 pt-4 border-t border-border-neutral space-y-2 text-body-md">
        <?php foreach ($belanjaLabels as $key => $info): ?>
        <div class="flex justify-between"><span class="text-slate-text-muted"><?= $info[0] ?></span><span class="font-semibold"><?= apb_fmt($apb['belanja'][$key]) ?></span></div>
        <?php endforeach; ?>
      </div>
    </div>
    <!-- Pembiayaan -->
    <div class="bg-primary rounded-2xl p-6 shadow-sm border border-primary-container hover:shadow-md transition-shadow text-on-primary">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 bg-primary-container rounded-xl flex items-center justify-center">
          <span class="material-symbols-outlined text-primary-fixed">balance</span>
        </div>
        <span class="text-label-sm text-primary-fixed uppercase tracking-wider font-bold bg-primary-container/50 px-3 py-1 rounded-full">Pembiayaan</span>
      </div>
      <h3 class="font-display text-3xl text-on-primary mb-1"><?= apb_fmt($apb['pembiayaan']['pembiayaan_netto']) ?></h3>
      <p class="text-body-md text-on-primary/80">Defisit anggaran ditutup pembiayaan netto</p>
      <div class="mt-4 pt-4 border-t border-primary-container space-y-2 text-body-md">
        <?php foreach ($pembiayaanLabels as $key => $label): ?>
        <div class="flex justify-between"><span class="text-on-primary/80"><?= $label ?></span><span class="font-semibold text-primary-fixed"><?= apb_fmt($apb['pembiayaan'][$key]) ?></span></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Detail Pendapatan Table -->
  <div class="bg-surface rounded-3xl shadow-sm border border-border-neutral overflow-hidden mb-12">
    <div class="px-6 py-5 bg-surface border-b border-border-neutral flex items-center justify-between">
      <div>
        <h2 class="font-headline-lg text-on-surface">Rincian Pendapatan</h2>
        <p class="text-body-md text-slate-text-muted mt-1">Sumber pendapatan pekon tahun anggaran <?= $apbTahun ?></p>
      </div>
      <span class="material-symbols-outlined text-slate-text-muted text-3xl">savings</span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-border-neutral bg-surface-container-low text-label-sm text-slate-text-muted uppercase tracking-wider">
            <th class="p-4 font-semibold">Sumber Pendapatan</th>
            <th class="p-4 font-semibold text-right">Nominal</th>
            <th class="p-4 font-semibold">Porsi</th>
          </tr>
        </thead>
        <tbody class="text-body-md divide-y divide-border-neutral">
          <?php foreach ($pendapatanLabels as $key => $label): $v = $apb['pendapatan'][$key]; ?>
          <tr class="hover:bg-surface-container transition-colors">
            <td class="p-4">
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-primary flex-shrink-0"></div>
                <p class="font-semibold text-on-surface"><?= $label ?></p>
              </div>
            </td>
            <td class="p-4 text-right font-bold text-on-surface"><?= apb_fmt($v) ?></td>
            <td class="p-4 w-48">
              <div class="flex items-center gap-3">
                <div class="flex-grow bg-surface-container h-2 rounded-full overflow-hidden">
                  <div class="bg-primary h-full rounded-full" style="width: <?= round(apb_pct($v, $apb['pendapatan']['total']), 1) ?>%"></div>
                </div>
                <span class="text-sm font-bold text-slate-text-muted w-10 text-right"><?= number_format(apb_pct($v, $apb['pendapatan']['total']), 1, ',', '.') ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="bg-surface-container border-t-2 border-border-neutral">
            <td class="p-4 font-bold text-on-surface uppercase tracking-wider text-sm">Total Pendapatan</td>
            <td class="p-4 text-right font-display text-xl text-primary"><?= apb_fmt($apb['pendapatan']['total']) ?></td>
            <td class="p-4"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- Detail Belanja Table -->
  <div class="bg-surface rounded-3xl shadow-sm border border-border-neutral overflow-hidden mb-12">
    <div class="px-6 py-5 bg-surface border-b border-border-neutral flex items-center justify-between">
      <div>
        <h2 class="font-headline-lg text-on-surface">Rincian Belanja</h2>
        <p class="text-body-md text-slate-text-muted mt-1">Alokasi anggaran berdasarkan bidang kegiatan</p>
      </div>
      <span class="material-symbols-outlined text-slate-text-muted text-3xl">table_chart</span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-border-neutral bg-surface-container-low text-label-sm text-slate-text-muted uppercase tracking-wider">
            <th class="p-4 font-semibold">Bidang / Kegiatan</th>
            <th class="p-4 font-semibold text-right">Anggaran</th>
            <th class="p-4 font-semibold">Porsi</th>
          </tr>
        </thead>
        <tbody class="text-body-md divide-y divide-border-neutral">
          <?php foreach ($belanjaLabels as $key => $info): $v = $apb['belanja'][$key]; ?>
          <tr class="hover:bg-surface-container transition-colors">
            <td class="p-4">
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full <?= $info[2] ?> flex-shrink-0"></div>
                <div>
                  <p class="font-semibold text-on-surface"><?= $info[0] ?></p>
                  <p class="text-slate-text-muted text-sm"><?= $info[1] ?></p>
                </div>
              </div>
            </td>
            <td class="p-4 text-right font-bold text-on-surface"><?= apb_fmt($v) ?></td>
            <td class="p-4 w-48">
              <div class="flex items-center gap-3">
                <div class="flex-grow bg-surface-container h-2 rounded-full overflow-hidden">
                  <div class="<?= $info[2] ?> h-full rounded-full" style="width: <?= round(apb_pct($v, $apb['belanja']['total']), 1) ?>%"></div>
                </div>
                <span class="text-sm font-bold text-slate-text-muted w-10 text-right"><?= number_format(apb_pct($v, $apb['belanja']['total']), 1, ',', '.') ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="bg-surface-container border-t-2 border-border-neutral">
            <td class="p-4 font-bold text-on-surface uppercase tracking-wider text-sm">Total Belanja</td>
            <td class="p-4 text-right font-display text-xl text-primary"><?= apb_fmt($apb['belanja']['total']) ?></td>
            <td class="p-4"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- Detail Pembiayaan Table -->
  <div class="bg-surface rounded-3xl shadow-sm border border-border-neutral overflow-hidden mb-12">
    <div class="px-6 py-5 bg-surface border-b border-border-neutral flex items-center justify-between">
      <div>
        <h2 class="font-headline-lg text-on-surface">Rincian Pembiayaan</h2>
        <p class="text-body-md text-slate-text-muted mt-1">Struktur pembiayaan yang menyeimbangkan anggaran pekon</p>
      </div>
      <span class="material-symbols-outlined text-slate-text-muted text-3xl">account_balance</span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-border-neutral bg-surface-container-low text-label-sm text-slate-text-muted uppercase tracking-wider">
            <th class="p-4 font-semibold">Pos Pembiayaan</th>
            <th class="p-4 font-semibold text-right">Nominal</th>
          </tr>
        </thead>
        <tbody class="text-body-md divide-y divide-border-neutral">
          <?php foreach ($pembiayaanLabels as $key => $label): ?>
          <tr class="hover:bg-surface-container transition-colors">
            <td class="p-4 font-semibold text-on-surface"><?= $label ?></td>
            <td class="p-4 text-right font-bold text-on-surface"><?= apb_fmt($apb['pembiayaan'][$key]) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="bg-surface-container border-t-2 border-border-neutral">
            <td class="p-4 font-bold text-on-surface uppercase tracking-wider text-sm">Pembiayaan Netto</td>
            <td class="p-4 text-right font-display text-xl text-primary"><?= apb_fmt($apb['pembiayaan']['pembiayaan_netto']) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>
    <div class="px-6 py-4 bg-surface border-t border-border-neutral flex items-center gap-3">
      <span class="material-symbols-outlined text-primary">verified</span>
      <span class="text-body-md text-slate-text-muted">APB Pekon <?= $apbTahun ?> telah disahkan oleh BHP (Badan Hippun Pemekonan).</span>
    </div>
  </div>

  <!-- CTA Banner -->
  <div class="bg-primary rounded-3xl p-8 md:p-12 text-on-primary flex flex-col md:flex-row items-center justify-between gap-8">
    <div>
      <h2 class="font-headline-lg text-headline-lg mb-3">Ada Pertanyaan tentang Anggaran?</h2>
      <p class="text-body-lg text-on-primary/80 max-w-lg">Kami terbuka untuk pertanyaan dan masukan warga terkait penggunaan APB Pekon. Transparansi adalah prioritas kami.</p>
    </div>
    <a class="flex-shrink-0 flex items-center gap-3 bg-primary-fixed text-on-primary-fixed px-8 py-4 rounded-full font-label-sm uppercase tracking-wider hover:bg-primary-fixed-dim transition-colors shadow-lg" href="kontak">
      <span class="material-symbols-outlined">chat</span>
      <span>Hubungi Kami</span>
    </a>
  </div>
</section>
</div>