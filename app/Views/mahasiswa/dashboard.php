<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header Banner -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Mahasiswa</h1>
        <p class="text-sm text-slate-500 mt-1">
            Selamat datang, <span class="font-semibold text-slate-800"><?= esc($userName) ?></span>
            <?php if (!empty($student['nim'])): ?>
                &bull; <span class="text-slate-600">NIM: <?= esc($student['nim']) ?></span>
            <?php endif; ?>
            <?php if (!empty($student['prodi_name'])): ?>
                &bull; <span class="text-slate-600"><?= esc($student['prodi_name']) ?></span>
            <?php endif; ?>
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= base_url('mahasiswa/pendaftaran/baru') ?>" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Ajukan Magang Baru
        </a>
    </div>
</div>

<!-- Overview Metric Cards (Informasi Penting & Relevan) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Magang</div>
            <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold text-slate-800 mt-2"><?= count($internships) ?></div>
        <p class="text-xs text-slate-500 mt-1">Total periode magang terdaftar</p>
    </div>

    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Magang Aktif</div>
            <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold text-slate-800 mt-2"><?= $activeInternshipsCount ?></div>
        <p class="text-xs text-slate-500 mt-1">Sedang dalam masa pelaksanaan</p>
    </div>

    <a href="<?= base_url('mahasiswa/logbook') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-blue-300 transition block group">
        <div class="flex items-center justify-between">
            <div class="text-xs font-semibold text-slate-400 group-hover:text-blue-600 uppercase tracking-wider transition">Logbook Terisi</div>
            <span class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold text-slate-800 mt-2"><?= $logbookCount ?></div>
        <p class="text-xs text-blue-600 font-medium mt-1">Buka entri logbook &rarr;</p>
    </a>

    <a href="<?= base_url('mahasiswa/konversi') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-blue-300 transition block group">
        <div class="flex items-center justify-between">
            <div class="text-xs font-semibold text-slate-400 group-hover:text-blue-600 uppercase tracking-wider transition">Konversi MK</div>
            <span class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold text-slate-800 mt-2"><?= $conversionCount ?></div>
        <p class="text-xs text-blue-600 font-medium mt-1">Kelola permohonan konversi &rarr;</p>
    </a>
</div>

<!-- Daftar Riwayat Periode Magang -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h2 class="text-base font-bold text-slate-900">Riwayat & Status Periode Magang</h2>
            <p class="text-xs text-slate-500 mt-0.5">Setiap periode magang memiliki dosen pembimbing dan alur verifikasi masing-masing.</p>
        </div>
        <a href="<?= base_url('mahasiswa/pendaftaran') ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
            Lihat Semua Periode &rarr;
        </a>
    </div>

    <?php if (!empty($internships)): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Instansi & Posisi</th>
                        <th class="px-6 py-3.5">Periode Pelaksanaan</th>
                        <th class="px-6 py-3.5">Dosen Pembimbing</th>
                        <th class="px-6 py-3.5">Status Magang</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($internships as $item): ?>
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900"><?= esc($item['company_name']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                                    <span><?= esc($item['position']) ?></span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium <?= ($item['internship_type'] ?? 'Offline') === 'Online' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                        <?= esc($item['internship_type'] ?? 'Offline') ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <div class="font-medium text-slate-800">
                                    <?= format_indo($item['start_date'], false, true) ?> - <?= format_indo($item['end_date'], false, true) ?>
                                </div>
                                <div class="text-slate-400 mt-0.5">
                                    TA <?= esc($item['academic_year'] ?? '-') ?> (<?= esc($item['semester'] ?? '-') ?>)
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <?php if (!empty($item['supervisor_name']) && $item['assignment_status'] === 'ACCEPTED'): ?>
                                    <div class="inline-flex items-center gap-1.5 text-emerald-800 font-semibold bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <?= esc($item['supervisor_name']) ?>
                                    </div>
                                <?php elseif ($item['assignment_status'] === 'PENDING'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu Konfirmasi
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-400 italic">Belum ditentukan</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= view('components/status_badge', ['status' => $item['status']]) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <?php if ($item['status'] === 'INTERNSHIP_ACTIVE'): ?>
                                        <a href="<?= base_url('mahasiswa/logbook?internship_id=' . $item['id']) ?>" class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-md border border-blue-200 transition">
                                            Isi Logbook
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('mahasiswa/pendaftaran/detail/' . $item['id']) ?>" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-md transition">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="p-12 text-center">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Belum Ada Pengajuan Magang</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Anda belum pernah mengajukan magang. Ajukan periode magang pertama Anda untuk memulai verifikasi dokumen dan plotting pembimbing.</p>
            <div class="mt-4">
                <a href="<?= base_url('mahasiswa/pendaftaran/baru') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    + Ajukan Magang Sekarang
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
