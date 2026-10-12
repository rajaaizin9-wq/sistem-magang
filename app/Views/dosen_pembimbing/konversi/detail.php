<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Breadcrumbs & Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <a href="<?= base_url('dosen-pembimbing/konversi') ?>" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Konversi
        </a>
        <div class="flex items-center space-x-3">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Verifikasi Konversi #KV-<?= esc($request['id']) ?></h1>
            <?= view('components/status_badge', ['status' => $request['status']]) ?>
        </div>
        <p class="text-xs text-slate-500 mt-1">Mahasiswa: <strong><?= esc($request['student_name']) ?></strong> (NIM: <?= esc($request['nim']) ?>)</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column (2/3): Courses & Action Form -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Action Form Card (If Status is SUPERVISOR_REVIEW) -->
        <?php if ($request['status'] === 'SUPERVISOR_REVIEW'): ?>
            <div class="bg-white rounded-xl border border-blue-200 p-6 shadow-sm ring-2 ring-blue-500/10">
                <div class="flex items-center space-x-2 pb-3 mb-4 border-b border-slate-100">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center">!</span>
                    <h2 class="text-base font-bold text-slate-900">Form Verifikasi Dosen Pembimbing</h2>
                </div>
                <p class="text-xs text-slate-500 mb-5">
                    Mata kuliah pilihan berikut telah diverifikasi oleh Dosen Pengampu masing-masing. Berikan persetujuan atau penolakan sebagai Dosen Pembimbing untuk diteruskan ke Wakil Dekan 1.
                </p>

                <form action="<?= base_url('dosen-pembimbing/konversi/proses/' . $request['id']) ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keputusan Pembimbing:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:border-emerald-400 transition bg-slate-50/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/30">
                                    <input type="radio" name="action" value="APPROVED" class="h-4 w-4 text-emerald-600 border-slate-300 focus:ring-emerald-500" checked required>
                                    <div class="ml-3">
                                        <span class="font-bold text-slate-900 text-sm block">Setujui Konversi</span>
                                        <span class="text-xs text-slate-500">Teruskan ke Wadek 1 untuk pengesahan</span>
                                    </div>
                                </label>

                                <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:border-red-400 transition bg-slate-50/50 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30">
                                    <input type="radio" name="action" value="REJECTED" class="h-4 w-4 text-red-600 border-slate-300 focus:ring-red-500" required>
                                    <div class="ml-3">
                                        <span class="font-bold text-slate-900 text-sm block">Tolak Konversi</span>
                                        <span class="text-xs text-slate-500">Batalkan permohonan konversi ini</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Catatan / Rekomendasi Pembimbing (Opsional):
                            </label>
                            <textarea name="notes" id="notes" rows="3" placeholder="Tuliskan catatan rekomendasi konversi..." class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></textarea>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-3">
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                Simpan Verifikasi Pembimbing
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Courses List Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Mata Kuliah yang Diajukan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Total <?= count($courses) ?> mata kuliah (<?= esc($totalCredits) ?> SKS)</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full">
                    <?= esc($acceptedCredits) ?> / <?= esc($totalCredits) ?> SKS Disetujui Dosen MK
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

        <!-- Catatan Mahasiswa -->
        <?php if (!empty($request['notes'])): ?>
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Catatan / Justifikasi Mahasiswa</h3>
                <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <?= nl2br(esc($request['notes'])) ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Approval History -->
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

    <!-- Right Column (1/3): Student & Internship Info -->
    <div class="space-y-6">
        <!-- Student Info Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Profil Mahasiswa</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Nama Mahasiswa:</span>
                    <span class="font-bold text-slate-900"><?= esc($request['student_name']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">NIM:</span>
                    <span class="font-mono text-slate-800"><?= esc($request['nim']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Program Studi:</span>
                    <span class="text-slate-800"><?= esc($request['prodi_name']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Kontak:</span>
                    <span class="text-slate-700 text-xs"><?= esc($request['student_email']) ?> <?= !empty($request['student_phone']) ? '&bull; ' . esc($request['student_phone']) : '' ?></span>
                </div>
            </div>
        </div>

        <!-- Internship Info Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Informasi Magang</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Instansi:</span>
                    <span class="font-bold text-slate-900"><?= esc($request['company_name']) ?></span>
                    <span class="text-xs text-slate-500 block truncate"><?= esc($request['company_address']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Posisi:</span>
                    <span class="font-semibold text-slate-800"><?= esc($request['position']) ?></span>
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
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
