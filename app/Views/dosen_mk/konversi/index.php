<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Verifikasi Konversi MK Pengampu</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar permohonan konversi SKS mata kuliah yang Anda ampu dari mahasiswa magang</p>
    </div>
    <div class="flex items-center space-x-2">
        <span class="px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold">
            <?= $pendingCount ?> Menunggu Tindakan
        </span>
        <span class="px-3 py-1.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold">
            <?= $reviewedCount ?> Telah Ditinjau
        </span>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <th class="px-6 py-3.5">Mahasiswa</th>
                    <th class="px-6 py-3.5">Mata Kuliah Diampu</th>
                    <th class="px-6 py-3.5">Instansi Magang</th>
                    <th class="px-6 py-3.5">Status Respon Anda</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $req): ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?= esc($req['student_name']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">NIM: <?= esc($req['nim']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 text-xs font-mono font-bold bg-slate-200 text-slate-800 rounded">
                                        <?= esc($req['course_code']) ?>
                                    </span>
                                    <span class="font-semibold text-slate-800 text-sm"><?= esc($req['course_name']) ?></span>
                                </div>
                                <div class="text-xs text-blue-600 font-semibold mt-1"><?= esc($req['credits']) ?> SKS</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900"><?= esc($req['company_name']) ?></div>
                                <div class="text-xs text-slate-500"><?= esc($req['position']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <?= view('components/status_badge', ['status' => $req['status']]) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= base_url('dosen-mk/konversi/detail/' . $req['id']) ?>" class="inline-flex items-center px-3.5 py-1.5 <?= $req['status'] === 'PENDING' ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' ?> text-xs font-semibold rounded-md transition">
                                    <?= $req['status'] === 'PENDING' ? 'Tinjau & Verifikasi' : 'Lihat Detail' ?>
                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <div class="max-w-xs mx-auto text-center">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-700">Tidak ada pengajuan konversi</p>
                                <p class="text-xs text-slate-500 mt-1">Belum ada mahasiswa yang mengajukan konversi untuk mata kuliah yang Anda ampu.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
