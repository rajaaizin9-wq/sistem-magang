<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pengajuan Konversi Mata Kuliah Pilihan</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar permohonan konversi SKS mata kuliah pilihan berdasarkan aktivitas magang Anda</p>
    </div>
    <a href="<?= base_url('mahasiswa/konversi/baru') ?>" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajukan Konversi Baru
    </a>
</div>

<!-- Info Alert -->
<div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start space-x-3 text-sm text-blue-800">
    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div class="leading-relaxed">
        <span class="font-semibold">Ketentuan Konversi:</span> Konversi hanya berlaku untuk <strong>Mata Kuliah Pilihan</strong> pada program magang yang sedang aktif berjalan. Alur persetujuan berjenjang: <strong>Dosen Pengampu MK &rarr; Dosen Pembimbing &rarr; Wakil Dekan 1</strong>.
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <th class="px-6 py-3.5">ID / Tanggal</th>
                    <th class="px-6 py-3.5">Periode Magang Terkait</th>
                    <th class="px-6 py-3.5">Mata Kuliah Diajukan</th>
                    <th class="px-6 py-3.5">Status Pengajuan</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $req): ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">#KV-<?= esc($req['id']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5"><?= format_indo($req['created_at'], true, true) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900"><?= esc($req['company_name']) ?></div>
                                <div class="text-xs text-slate-500"><?= esc($req['position']) ?> &bull; <?= esc($req['academic_year']) ?> (<?= esc($req['semester']) ?>)</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-medium text-slate-800">
                                    <span class="font-bold text-blue-600"><?= count($req['courses']) ?> MK</span> (<?= esc($req['total_credits']) ?> SKS)
                                </div>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    <?php foreach ($req['courses'] as $c): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium <?= $c['status'] === 'ACCEPTED' ? 'bg-emerald-100 text-emerald-800' : ($c['status'] === 'REJECTED' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-700') ?>">
                                            <?= esc($c['course_code']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?= view('components/status_badge', ['status' => $req['status']]) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= base_url('mahasiswa/konversi/detail/' . $req['id']) ?>" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-md transition">
                                    Lihat Detail
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
                                <p class="text-sm font-semibold text-slate-700">Belum ada pengajuan konversi</p>
                                <p class="text-xs text-slate-500 mt-1">Anda dapat mengajukan konversi MK pilihan setelah pendaftaran magang Anda disetujui.</p>
                                <a href="<?= base_url('mahasiswa/konversi/baru') ?>" class="mt-4 inline-flex items-center px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                    Mulai Pengajuan
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
