<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Breadcrumbs & Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <a href="<?= base_url('mahasiswa/konversi') ?>" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Konversi
        </a>
        <div class="flex items-center space-x-3">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pengajuan Konversi #KV-<?= esc($request['id']) ?></h1>
            <?= view('components/status_badge', ['status' => $request['status']]) ?>
        </div>
        <p class="text-xs text-slate-500 mt-1">Diajukan pada: <?= format_indo($request['created_at'], true) ?></p>
    </div>
</div>

<!-- Workflow Stepper -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Progres Persetujuan Berjenjang</h2>
    <?php
    $steps = [
        ['key' => 'SUBMITTED',         'label' => 'Diajukan',             'sub' => 'Mahasiswa'],
        ['key' => 'LECTURER_REVIEW',   'label' => 'Dosen MK',             'sub' => 'Cek CPMK Mata Kuliah'],
        ['key' => 'SUPERVISOR_REVIEW', 'label' => 'Dosen Pembimbing',    'sub' => 'Verifikasi Pembimbing'],
        ['key' => 'VICE_DEAN_REVIEW',  'label' => 'Wakil Dekan 1',        'sub' => 'Pengesahan Fakultas'],
        ['key' => 'APPROVED',          'label' => 'Selesai & Disahkan',   'sub' => 'Konversi Berhasil'],
    ];

    $statusOrder = [
        'DRAFT'             => 0,
        'SUBMITTED'         => 1,
        'LECTURER_REVIEW'   => 1,
        'SUPERVISOR_REVIEW' => 2,
        'VICE_DEAN_REVIEW'  => 3,
        'APPROVED'          => 4,
        'REJECTED'          => -1,
    ];

    $currentStage = $statusOrder[$request['status']] ?? 1;
    $isRejected   = ($request['status'] === 'REJECTED');
    ?>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <?php foreach ($steps as $idx => $st): ?>
            <?php
            $isComplete = ($currentStage > $idx) || ($request['status'] === 'APPROVED');
            $isCurrent  = ($currentStage === $idx) && !$isRejected;
            ?>
            <div class="flex items-center space-x-3 p-3 rounded-lg border <?= $isCurrent ? 'border-blue-500 bg-blue-50/50' : ($isComplete ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-100 bg-slate-50/50') ?>">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs <?= $isComplete ? 'bg-emerald-600 text-white' : ($isCurrent ? 'bg-blue-600 text-white animate-pulse' : 'bg-slate-200 text-slate-500') ?>">
                    <?php if ($isComplete): ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <?php else: ?>
                        <?= $idx + 1 ?>
                    <?php endif; ?>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold <?= $isCurrent ? 'text-blue-900' : ($isComplete ? 'text-emerald-900' : 'text-slate-600') ?> truncate">
                        <?= esc($st['label']) ?>
                    </div>
                    <div class="text-[10px] text-slate-400 truncate"><?= esc($st['sub']) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($isRejected): ?>
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700 flex items-center space-x-2">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>Pengajuan konversi ini berstatus <strong>DITOLAK</strong>. Silakan periksa catatan penolakan pada riwayat di bawah.</span>
        </div>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column (2/3): Courses & Details -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Courses List Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Mata Kuliah Pilihan yang Diajukan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Total <?= count($courses) ?> mata kuliah (<?= esc($totalCredits) ?> SKS)</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full">
                    <?= esc($approvedCredits) ?> / <?= esc($totalCredits) ?> SKS Disetujui
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                <?php foreach ($courses as $c): ?>
                    <div class="p-5 hover:bg-slate-50/50 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 text-xs font-mono font-bold bg-slate-200 text-slate-800 rounded">
                                        <?= esc($c['course_code']) ?>
                                    </span>
                                    <h3 class="font-bold text-slate-900 text-sm"><?= esc($c['course_name']) ?></h3>
                                    <span class="text-xs font-semibold px-2 py-0.5 bg-blue-100 text-blue-800 rounded">
                                        <?= esc($c['credits']) ?> SKS
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Dosen Pengampu: <strong class="ml-1 text-slate-700"><?= esc($c['lecturer_name'] ?? 'Belum ditentukan') ?></strong>
                                    <?php if (!empty($c['lecturer_nidn'])): ?>
                                        <span class="text-slate-400 ml-1">(NIDN: <?= esc($c['lecturer_nidn']) ?>)</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <?= view('components/status_badge', ['status' => $c['status']]) ?>
                            </div>
                        </div>

                        <?php if (!empty($c['student_justification'])): ?>
                            <div class="mt-2.5 p-3 bg-slate-50 border border-slate-200/80 rounded-lg text-xs text-slate-700">
                                <span class="font-semibold text-slate-900 block mb-0.5">Penjelasan Mahasiswa:</span>
                                <p class="leading-relaxed"><?= nl2br(esc($c['student_justification'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($c['rejection_reason'])): ?>
                            <div class="mt-2.5 p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700">
                                <strong>Catatan Dosen MK:</strong> <?= esc($c['rejection_reason']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Catatan / Justifikasi Mahasiswa -->
        <?php if (!empty($request['notes'])): ?>
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Catatan / Justifikasi Mahasiswa</h3>
                <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <?= nl2br(esc($request['notes'])) ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Approval History Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Riwayat & Catatan Verifikator</h3>

            <?php if (!empty($approvals)): ?>
                <div class="space-y-4">
                    <?php foreach ($approvals as $appr): ?>
                        <div class="flex items-start space-x-3 p-3.5 rounded-lg border border-slate-100 bg-slate-50/50">
                            <div class="w-8 h-8 rounded-full bg-slate-800 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">
                                <?= strtoupper(substr($appr['role'], 0, 2)) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-900"><?= esc($appr['approver_name']) ?></span>
                                    <span class="text-[11px] text-slate-400"><?= format_indo($appr['created_at'], true, true) ?></span>
                                </div>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 bg-slate-200 text-slate-700 rounded">
                                        <?= str_replace('_', ' ', $appr['role']) ?>
                                    </span>
                                    <span class="text-[11px] font-bold <?= $appr['status'] === 'APPROVED' ? 'text-emerald-700' : 'text-red-700' ?>">
                                        <?= $appr['status'] === 'APPROVED' ? '✓ Disetujui' : '✗ Ditolak' ?>
                                    </span>
                                </div>
                                <?php if (!empty($appr['notes'])): ?>
                                    <p class="text-xs text-slate-600 mt-2 bg-white p-2.5 rounded border border-slate-200">
                                        <?= nl2br(esc($appr['notes'])) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-xs text-slate-400 italic">Belum ada verifikasi yang tercatat.</p>
            <?php endif; ?>
        </div>

    </div>

    <!-- Right Column (1/3): Internship & Student Info -->
    <div class="space-y-6">
        <!-- Internship Info Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Informasi Magang Terkait</h3>
            
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Instansi:</span>
                    <span class="font-bold text-slate-900"><?= esc($request['company_name']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Posisi:</span>
                    <span class="font-medium text-slate-800"><?= esc($request['position']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Tipe Magang:</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold <?= ($request['internship_type'] ?? 'Offline') === 'Online' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' ?>">
                        <?= esc($request['internship_type'] ?? 'Offline') ?>
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Periode:</span>
                    <span class="text-xs text-slate-700">
                        <?= format_indo($request['start_date'], false, true) ?> &ndash; <?= format_indo($request['end_date'], false, true) ?>
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Tahun Akademik:</span>
                    <span class="text-xs text-slate-700"><?= esc($request['academic_year']) ?> (<?= esc($request['semester']) ?>)</span>
                </div>
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-xs text-slate-400 block">Dosen Pembimbing Magang:</span>
                    <span class="font-semibold text-slate-900 text-xs">
                        <?= esc($request['supervisor_name'] ?? 'Belum ditentukan') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
