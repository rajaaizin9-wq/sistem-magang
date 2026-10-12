<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Verifikasi Berkas Permohonan Magang (Staf TU)</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar pengajuan magang mahasiswa yang membutuhkan tindakan verifikasi administrasi</p>
    </div>
    
    <!-- Filter -->
    <div class="flex items-center space-x-2">
        <label for="filterStatus" class="text-xs font-semibold text-slate-500 uppercase">Filter Status:</label>
        <select id="filterStatus" onchange="location = this.value;" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
            <option value="<?= base_url('tu/verifikasi-dokumen') ?>" <?= empty($statusFilter) ? 'selected' : '' ?>>Semua Menunggu TU</option>
            <option value="<?= base_url('tu/verifikasi-dokumen?status=SUBMITTED') ?>" <?= $statusFilter == 'SUBMITTED' ? 'selected' : '' ?>>SUBMITTED (Baru)</option>
            <option value="<?= base_url('tu/verifikasi-dokumen?status=REVISION_REQUIRED') ?>" <?= $statusFilter == 'REVISION_REQUIRED' ? 'selected' : '' ?>>REVISION_REQUIRED</option>
            <option value="<?= base_url('tu/verifikasi-dokumen?status=ADMIN_APPROVED') ?>" <?= $statusFilter == 'ADMIN_APPROVED' ? 'selected' : '' ?>>ADMIN_APPROVED (Disetujui)</option>
        </select>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <th class="px-6 py-3.5">Mahasiswa Pemohon</th>
                    <th class="px-6 py-3.5">Instansi Tujuan</th>
                    <th class="px-6 py-3.5">Periode & Tahun</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-right">Tindakan TU</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (!empty($internships)): ?>
                    <?php foreach ($internships as $item): ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?= esc($item['student_name']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">NIM: <?= esc($item['nim']) ?> (<?= esc($item['prodi_code']) ?>)</div>
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
                            <td class="px-6 py-4 text-slate-600 text-xs">
                                <div><?= format_indo($item['start_date'], false, true) ?> - <?= format_indo($item['end_date'], false, true) ?></div>
                                <div class="text-slate-400 mt-0.5"><?= esc($item['academic_year']) ?> (<?= esc($item['semester']) ?>)</div>
                            </td>
                            <td class="px-6 py-4">
                                <?= view('components/status_badge', ['status' => $item['status']]) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= base_url('tu/verifikasi-dokumen/detail/' . $item['id']) ?>" class="inline-flex items-center px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                    Verifikasi Berkas
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-medium text-slate-600">Tidak Ada Permohonan Menunggu Verifikasi</p>
                            <p class="text-xs text-slate-400 mt-1">Seluruh berkas pendaftaran magang mahasiswa telah diproses.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
