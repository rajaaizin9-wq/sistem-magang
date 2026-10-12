<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pemantauan Magang Mahasiswa</h1>
                <p class="text-sm text-slate-500 mt-0.5">Data pendaftaran dan pelaksanaan magang mahasiswa fakultas.</p>
            </div>
        </div>
    </div>

    <!-- Filter Tab & Prodi -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50/50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Tabs -->
            <div class="flex items-center space-x-2">
                <a href="<?= base_url('wadek1/pemantauan-magang?tab=active' . (!empty($selectedProdi) ? '&prodi=' . $selectedProdi : '')) ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition <?= $currentTab === 'active' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' ?>">
                    Magang Aktif (<?= $totalActiveFaculty ?>)
                </a>
                <a href="<?= base_url('wadek1/pemantauan-magang?tab=all' . (!empty($selectedProdi) ? '&prodi=' . $selectedProdi : '')) ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition <?= $currentTab === 'all' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' ?>">
                    Semua Status (<?= $totalAllRegistrations ?>)
                </a>
            </div>

            <!-- Filter Prodi -->
            <form method="GET" action="<?= base_url('wadek1/pemantauan-magang') ?>" class="flex items-center space-x-2">
                <input type="hidden" name="tab" value="<?= esc($currentTab) ?>">
                <label for="prodi" class="text-xs font-semibold text-slate-500 whitespace-nowrap">Program Studi:</label>
                <select name="prodi" id="prodi" onchange="this.form.submit()" class="bg-white border border-slate-300 text-slate-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                    <option value="">Semua Program Studi</option>
                    <?php foreach ($prodis as $prodi): ?>
                        <option value="<?= $prodi['id'] ?>" <?= $selectedProdi == $prodi['id'] ? 'selected' : '' ?>>
                            <?= esc($prodi['name']) ?> (<?= esc($prodi['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <!-- Tabel Pemantauan -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Mahasiswa</th>
                        <th class="px-6 py-4">Prodi</th>
                        <th class="px-6 py-4">Instansi & Posisi</th>
                        <th class="px-6 py-4">Periode</th>
                        <th class="px-6 py-4">Dosen Pembimbing</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (empty($internships)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <span class="text-sm">Tidak ada data pendaftaran magang.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($internships as $item): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900"><?= esc($item['student_name']) ?></div>
                                    <div class="text-xs text-slate-500">NIM: <?= esc($item['nim']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                        <?= esc($item['prodi_code']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800"><?= esc($item['company_name']) ?></div>
                                    <div class="text-xs text-slate-500"><?= esc($item['position']) ?></div>
                                    <div class="mt-1">
                                        <?php if (($item['internship_type'] ?? 'OFFLINE') === 'ONLINE'): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-sky-50 text-sky-700 border border-sky-200">
                                                Online
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Offline
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    <div><?= format_indo($item['start_date'], false, true) ?></div>
                                    <div class="text-slate-400">s/d <?= format_indo($item['end_date'], false, true) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if (!empty($item['supervisor_name'])): ?>
                                        <div class="font-medium text-slate-800 text-xs"><?= esc($item['supervisor_name']) ?></div>
                                        <?php if (($item['assignment_status'] ?? '') === 'ACCEPTED'): ?>
                                            <span class="text-[10px] text-emerald-600 font-medium block mt-0.5">Diterima</span>
                                        <?php elseif (($item['assignment_status'] ?? '') === 'REJECTED'): ?>
                                            <span class="text-[10px] text-red-600 font-medium block mt-0.5">Ditolak</span>
                                        <?php else: ?>
                                            <span class="text-[10px] text-amber-600 font-medium block mt-0.5">Menunggu konfirmasi</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 italic">Belum ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= view('components/status_badge', ['status' => $item['status']]) ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?= base_url('wadek1/pemantauan-magang/detail/' . $item['id']) ?>" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition shadow-sm">
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
