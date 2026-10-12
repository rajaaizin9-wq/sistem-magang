<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Dosen Mata Kuliah</h1>
    <p class="text-sm text-slate-500 mt-1">Verifikasi pengajuan konversi, penentuan komponen tugas dinamis, dan penilaian magang</p>
</div>

<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <h2 class="text-base font-bold text-slate-800 mb-3">Mata Kuliah Pilihan Pengampu</h2>
    <?php if (!empty($courses)): ?>
        <div class="space-y-2">
            <?php foreach ($courses as $course): ?>
                <div class="p-3.5 bg-slate-50 rounded-lg border border-slate-200 flex justify-between items-center text-sm">
                    <div>
                        <span class="font-bold text-slate-800"><?= esc($course['code']) ?> - <?= esc($course['name']) ?></span>
                        <span class="text-xs text-slate-500 block mt-0.5"><?= esc($course['credits']) ?> SKS (MK Pilihan)</span>
                    </div>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-semibold text-xs rounded-full">MK Pilihan</span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-sm text-slate-500">Belum ada mata kuliah pilihan yang ditugaskan kepada Anda.</p>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
