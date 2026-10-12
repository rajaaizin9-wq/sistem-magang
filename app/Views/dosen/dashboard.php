<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Welcome Header -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="flex items-center space-x-2">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Portal Dosen</span>
            <span class="text-xs text-slate-400">NIDN: <?= esc($lecturer['nidn'] ?? '-') ?></span>
        </div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight mt-1.5">Selamat Datang, <?= esc($userName) ?></h1>
        <p class="text-sm text-slate-500 mt-0.5">Kelola penugasan bimbingan magang mahasiswa dan mata kuliah pilihan yang Anda ampu</p>
    </div>
</div>

<!-- Summary Metric Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Card 1: Penugasan Menunggu Respon -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tawaran Bimbingan</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                ⚠️
            </div>
        </div>
        <div class="mt-3">
            <div class="text-2xl font-bold text-slate-900"><?= $pendingAssignmentsCount ?? 0 ?></div>
            <p class="text-xs text-slate-500 mt-1">Perlu respon persetujuan</p>
        </div>
    </div>

    <!-- Card 2: Mahasiswa Bimbingan Aktif -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mahasiswa Bimbingan</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                🎓
            </div>
        </div>
        <div class="mt-3">
            <div class="text-2xl font-bold text-slate-900"><?= $activeStudentsCount ?? 0 ?></div>
            <p class="text-xs text-slate-500 mt-1">Bimbingan magang aktif</p>
        </div>
    </div>

    <!-- Card 3: MK Pilihan Diampu -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">MK Pilihan Diampu</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                📚
            </div>
        </div>
        <div class="mt-3">
            <div class="text-2xl font-bold text-slate-900"><?= count($courses ?? []) ?></div>
            <p class="text-xs text-slate-500 mt-1">Mata kuliah semester ini</p>
        </div>
    </div>

    <!-- Card 4: Pengajuan Konversi MK -->
    <a href="<?= base_url('dosen-mk/konversi') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-purple-300 transition block">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Konversi MK Masuk</span>
            <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
                📑
            </div>
        </div>
        <div class="mt-3">
            <div class="text-2xl font-bold text-slate-900"><?= $pendingCourseConversionCount ?? 0 ?></div>
            <p class="text-xs text-slate-500 mt-1">Menunggu review CPMK &rarr;</p>
        </div>
    </a>
</div>

<!-- Contextual Roles Sections Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Section 1: Konteks Dosen Pembimbing -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Peran: Dosen Pembimbing</h2>
            </div>
            <div class="space-x-2 text-xs">
                <a href="<?= base_url('dosen-pembimbing/pemantauan-logbook') ?>" class="font-semibold text-blue-600 hover:text-blue-800">
                    Logbook &rarr;
                </a>
                <span class="text-slate-300">|</span>
                <a href="<?= base_url('dosen-pembimbing/konversi') ?>" class="font-semibold text-blue-600 hover:text-blue-800">
                    Konversi (<?= $pendingSupervisorConversionCount ?? 0 ?>) &rarr;
                </a>
            </div>
        </div>

        <p class="text-xs text-slate-500">Daftar penugasan bimbingan magang terbaru yang ditunjuk oleh Kaprodi:</p>

        <?php if (!empty($assignments)): ?>
            <div class="space-y-3">
                <?php foreach (array_slice($assignments, 0, 3) as $item): ?>
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-900 block"><?= esc($item['student_name']) ?></span>
                            <span class="text-slate-500"><?= esc($item['company_name']) ?> &bull; <?= esc($item['position']) ?></span>
                        </div>
                        <div>
                            <?= view('components/status_badge', ['status' => $item['assignment_status']]) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-lg border border-dashed border-slate-200 text-xs">
                Belum ada penugasan pembimbingan magang dari Kaprodi saat ini.
            </div>
        <?php endif; ?>
    </div>

    <!-- Section 2: Konteks Dosen Mata Kuliah -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Peran: Dosen Pengampu MK</h2>
            </div>
            <a href="<?= base_url('dosen-mk/konversi') ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                Verifikasi Konversi MK &rarr;
            </a>
        </div>

        <p class="text-xs text-slate-500">Mata kuliah pilihan yang Anda ampu dan terbuka untuk konversi CPMK magang:</p>

        <?php if (!empty($courses)): ?>
            <div class="space-y-3">
                <?php foreach ($courses as $course): ?>
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-900 block"><?= esc($course['code']) ?> - <?= esc($course['name']) ?></span>
                            <span class="text-slate-500"><?= esc($course['credits']) ?> SKS &bull; Program Studi S-1</span>
                        </div>
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-semibold text-[11px] rounded-md">
                            MK Pilihan
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-lg border border-dashed border-slate-200 text-xs">
                Belum ada mata kuliah pilihan yang terdaftar di bawah pengampuan Anda.
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
