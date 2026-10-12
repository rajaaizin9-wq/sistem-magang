<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Breadcrumbs & Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <a href="<?= base_url('dosen-mk/konversi') ?>" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Verifikasi
        </a>
        <div class="flex items-center space-x-3">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Verifikasi Konversi: <?= esc($item['course_name']) ?></h1>
            <?= view('components/status_badge', ['status' => $item['status']]) ?>
        </div>
        <p class="text-xs text-slate-500 mt-1">Diajukan oleh <?= esc($item['student_name']) ?> (NIM: <?= esc($item['nim']) ?>) &bull; <?= format_indo($item['request_date'], true, true) ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column (2/3): Verification Details & Action -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Course & CPMK Assessment Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <span class="text-xs font-mono font-bold px-2 py-0.5 bg-slate-200 text-slate-800 rounded">
                        <?= esc($item['course_code']) ?>
                    </span>
                    <h2 class="text-base font-bold text-slate-900 mt-1"><?= esc($item['course_name']) ?></h2>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full">
                    <?= esc($item['credits']) ?> SKS
                </span>
            </div>

            <div class="mt-4 space-y-3">
                <div>
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Penjelasan Hubungan Aktivitas Magang dengan MK Ini:</h3>
                    <?php if (!empty($item['student_justification'])): ?>
                        <div class="text-sm text-slate-800 leading-relaxed bg-blue-50/50 p-4 rounded-lg border border-blue-200/80">
                            <?= nl2br(esc($item['student_justification'])) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-xs text-slate-400 italic bg-slate-50 p-3 rounded border border-slate-100">Tidak ada penjelasan khusus.</p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($item['student_notes'])): ?>
                    <div class="pt-2 border-t border-slate-100">
                        <h4 class="text-xs font-semibold text-slate-500 mb-1">Catatan Tambahan Mahasiswa:</h4>
                        <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <?= nl2br(esc($item['student_notes'])) ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Form Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-2">Tindakan Verifikasi Dosen Pengampu</h2>
            <p class="text-xs text-slate-500 mb-6">Tentukan apakah aktivitas magang mahasiswa telah memenuhi CPMK mata kuliah ini</p>

            <form action="<?= base_url('dosen-mk/konversi/proses/' . $item['id']) ?>" method="POST" id="formProcess">
                <?= csrf_field() ?>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keputusan Verifikasi:</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:border-emerald-400 transition bg-slate-50/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/30">
                                <input type="radio" name="action" value="ACCEPTED" class="h-4 w-4 text-emerald-600 border-slate-300 focus:ring-emerald-500" <?= $item['status'] === 'ACCEPTED' ? 'checked' : '' ?> required>
                                <div class="ml-3">
                                    <span class="font-bold text-slate-900 text-sm block">Setujui Konversi</span>
                                    <span class="text-xs text-slate-500">Aktivitas magang relevan dengan CPMK MK</span>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:border-red-400 transition bg-slate-50/50 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30">
                                <input type="radio" name="action" value="REJECTED" class="h-4 w-4 text-red-600 border-slate-300 focus:ring-red-500" <?= $item['status'] === 'REJECTED' ? 'checked' : '' ?> required>
                                <div class="ml-3">
                                    <span class="font-bold text-slate-900 text-sm block">Tolak Konversi</span>
                                    <span class="text-xs text-slate-500">Aktivitas belum sesuai kriteria CPMK</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Catatan / Masukan Dosen Pengampu:
                        </label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Tuliskan catatan persetujuan atau alasan penolakan jika ditolak..." class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"><?= esc($item['rejection_reason'] ?? '') ?></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-3">
                        <a href="<?= base_url('dosen-mk/konversi') ?>" class="px-4 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-lg transition">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                            Simpan Keputusan
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <!-- Right Column (1/3): Student & Internship Info -->
    <div class="space-y-6">
        <!-- Student Info Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Profil Mahasiswa</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Nama Lengkap:</span>
                    <span class="font-bold text-slate-900"><?= esc($item['student_name']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">NIM:</span>
                    <span class="font-mono text-slate-800"><?= esc($item['nim']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Program Studi:</span>
                    <span class="text-slate-800"><?= esc($item['prodi_name']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Email / Kontak:</span>
                    <span class="text-slate-700 text-xs"><?= esc($item['student_email']) ?> <?= !empty($item['student_phone']) ? '&bull; ' . esc($item['student_phone']) : '' ?></span>
                </div>
            </div>
        </div>

        <!-- Internship Info Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Informasi Magang</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Instansi:</span>
                    <span class="font-bold text-slate-900"><?= esc($item['company_name']) ?></span>
                    <span class="text-xs text-slate-500 block truncate"><?= esc($item['company_address']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Posisi:</span>
                    <span class="font-semibold text-slate-800"><?= esc($item['position']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Tipe Magang:</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold <?= ($item['internship_type'] ?? 'Offline') === 'Online' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' ?>">
                        <?= esc($item['internship_type'] ?? 'Offline') ?>
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Periode:</span>
                    <span class="text-xs text-slate-700">
                        <?= format_indo($item['start_date'], false, true) ?> &ndash; <?= format_indo($item['end_date'], false, true) ?>
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Tahun Akademik:</span>
                    <span class="text-xs text-slate-700"><?= esc($item['academic_year']) ?> (<?= esc($item['semester']) ?>)</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
