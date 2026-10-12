<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Penentuan Dosen Pembimbing & Pemantauan Magang (Kaprodi)</h1>
        <p class="text-sm text-slate-500 mt-1">Kaprodi dapat memantau seluruh pengajuan magang mahasiswa serta menentukan Dosen Pembimbing</p>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="mb-6 flex border-b border-slate-200 text-sm font-semibold">
    <a href="<?= base_url('kaprodi/penentuan-pembimbing?tab=pending') ?>" class="py-3 px-5 border-b-2 transition <?= $tab === 'pending' ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">
        ⏳ Menunggu Penentuan Pembimbing
    </a>
    <a href="<?= base_url('kaprodi/penentuan-pembimbing?tab=all') ?>" class="py-3 px-5 border-b-2 transition <?= $tab === 'all' ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">
        📋 Semua Pengajuan Magang (Pemantauan Prodi)
    </a>
</div>

<!-- Table Card -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <th class="px-6 py-3.5">Mahasiswa Pemohon</th>
                    <th class="px-6 py-3.5">Instansi Tujuan</th>
                    <th class="px-6 py-3.5">Status Pendaftaran</th>
                    <th class="px-6 py-3.5">Dosen Pembimbing</th>
                    <th class="px-6 py-3.5 text-right">Aksi Kaprodi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (!empty($internships)): ?>
                    <?php foreach ($internships as $item): ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?= esc($item['student_name']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">NIM: <?= esc($item['nim']) ?></div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                <?= esc($item['company_name']) ?>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-xs font-normal text-slate-500"><?= esc($item['position']) ?></span>
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold <?= ($item['internship_type'] ?? 'Offline') === 'Online' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                        <?= esc($item['internship_type'] ?? 'Offline') ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?= view('components/status_badge', ['status' => $item['status']]) ?>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <?php if (!empty($item['supervisor_name'])): ?>
                                    <span class="font-semibold text-slate-800 block"><?= esc($item['supervisor_name']) ?></span>
                                    <?php if ($item['assignment_status'] === 'PENDING'): ?>
                                        <span class="text-amber-600 font-medium">(Menunggu Respon Dosen)</span>
                                    <?php elseif ($item['assignment_status'] === 'ACCEPTED'): ?>
                                        <span class="text-emerald-600 font-medium">(Menerima Pembimbingan)</span>
                                    <?php elseif ($item['assignment_status'] === 'REJECTED'): ?>
                                        <span class="text-red-600 font-medium block">(Menolak: <?= esc($item['rejection_reason']) ?>)</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-slate-400 italic">Belum Ditentukan</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= base_url('kaprodi/penentuan-pembimbing/detail/' . $item['id']) ?>" class="inline-flex items-center px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                    Detail & Tentukan Pembimbing
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <p class="font-medium text-slate-600">Tidak Ada Data Pengajuan Magang</p>
                            <p class="text-xs text-slate-400 mt-1">Belum ada data pendaftaran magang di tab ini.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
