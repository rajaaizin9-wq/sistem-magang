<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Daftar Periode Magang Anda</h1>
        <p class="text-sm text-slate-500 mt-1">Riwayat dan status permohonan magang yang pernah Anda ajukan</p>
    </div>
    <a href="<?= base_url('mahasiswa/pendaftaran/baru') ?>" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
        + Ajukan Magang Baru
    </a>
</div>

<!-- Table Card -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <th class="px-6 py-3.5">Instansi Tujuan</th>
                    <th class="px-6 py-3.5">Posisi</th>
                    <th class="px-6 py-3.5">Periode & Tahun</th>
                    <th class="px-6 py-3.5">Status Workflow</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (!empty($internships)): ?>
                    <?php foreach ($internships as $item): ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                <?= esc($item['company_name']) ?>
                                <span class="block text-xs font-normal text-slate-500 truncate max-w-xs"><?= esc($item['company_address']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium">
                                <div><?= esc($item['position']) ?></div>
                                <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[11px] font-medium <?= ($item['internship_type'] ?? 'Offline') === 'Online' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                    <?= esc($item['internship_type'] ?? 'Offline') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs">
                                <div><?= format_indo($item['start_date'], false, true) ?> - <?= format_indo($item['end_date'], false, true) ?></div>
                                <div class="text-slate-400 mt-0.5"><?= esc($item['academic_year']) ?> (<?= esc($item['semester']) ?>)</div>
                            </td>
                            <td class="px-6 py-4">
                                <?= view('components/status_badge', ['status' => $item['status']]) ?>
                                <?php if (!empty($item['supervisor_name']) && $item['assignment_status'] === 'ACCEPTED'): ?>
                                    <div class="text-xs text-emerald-700 font-medium mt-1.5 flex items-center gap-1">
                                        <span>👨‍🏫 <?= esc($item['supervisor_name']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= base_url('mahasiswa/pendaftaran/detail/' . $item['id']) ?>" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-md transition">
                                    Lihat Detail & Status
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="font-medium text-slate-600">Belum Ada Riwayat Magang</p>
                            <p class="text-xs text-slate-400 mt-1">Klik tombol "Ajukan Magang Baru" untuk memulai pendaftaran permohonan magang Anda.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
