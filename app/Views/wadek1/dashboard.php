<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Wadek 1</h1>
            <p class="text-sm text-slate-500 mt-1">Pemantauan data magang dan konversi mata kuliah mahasiswa fakultas.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="<?= base_url('wadek1/pemantauan-magang') ?>" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Pemantauan Magang
            </a>
            <a href="<?= base_url('wadek1/konversi') ?>" class="inline-flex items-center px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Pengesahan Konversi (<?= $pendingConversionCount ?? 0 ?>)
            </a>
        </div>
    </div>

    <!-- Ringkasan Metrik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Magang Aktif</p>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-emerald-600 mt-2"><?= $totalActive ?? 0 ?></h3>
            <p class="text-xs text-slate-400 mt-1">Mahasiswa aktif magang</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pembimbing Ditugaskan</p>
                <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-cyan-700 mt-2"><?= $totalSupervisorAssigned ?? 0 ?></h3>
            <p class="text-xs text-slate-400 mt-1">Sudah ditentukan kaprodi</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Pengajuan</p>
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 mt-2"><?= $totalAll ?? 0 ?></h3>
            <p class="text-xs text-slate-400 mt-1">Semua pendaftaran fakultas</p>
        </div>

        <a href="<?= base_url('wadek1/konversi') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-purple-300 transition block">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Konversi Menunggu</p>
                <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-purple-700 mt-2"><?= $pendingConversionCount ?? 0 ?></h3>
            <p class="text-xs text-purple-600 font-semibold mt-1">Pengesahan Wadek 1 &rarr;</p>
        </a>
    </div>

    <!-- Preview Magang Aktif Terkini -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Magang Aktif Terbaru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar mahasiswa yang sedang aktif magang.</p>
            </div>
            <a href="<?= base_url('wadek1/pemantauan-magang') ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center space-x-1">
                <span>Lihat Semua</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Mahasiswa</th>
                        <th class="px-6 py-3.5">Prodi</th>
                        <th class="px-6 py-3.5">Instansi Magang</th>
                        <th class="px-6 py-3.5">Dosen Pembimbing</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (empty($recentActiveInternships)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-xs">
                                Belum ada data magang aktif.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentActiveInternships as $item): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900"><?= esc($item['student_name']) ?></div>
                                    <div class="text-xs text-slate-500">NIM: <?= esc($item['nim']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs text-slate-700"><?= esc($item['prodi_name']) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800"><?= esc($item['company_name']) ?></div>
                                    <div class="text-xs text-slate-500"><?= esc($item['position']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-medium text-slate-700"><?= esc($item['supervisor_name'] ?: 'Belum ditentukan') ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <?= view('components/status_badge', ['status' => $item['status']]) ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?= base_url('wadek1/pemantauan-magang/detail/' . $item['id']) ?>" class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
