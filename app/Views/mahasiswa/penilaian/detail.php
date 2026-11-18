<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-5xl">
    <!-- Header with Back Button -->
    <div class="flex items-center space-x-3 pb-2 border-b border-slate-200">
        <a href="<?= base_url('mahasiswa/penilaian') ?>" class="p-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-mono font-bold bg-blue-100 text-blue-800 rounded">
                    <?= esc($crc['course_code']) ?>
                </span>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight"><?= esc($crc['course_name']) ?></h1>
                <span class="text-xs font-semibold px-2 py-0.5 bg-slate-100 text-slate-700 rounded">
                    <?= esc($crc['credits']) ?> SKS
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Dosen Pengampu: <strong><?= esc($crc['lecturer_name'] ?? 'Belum ditentukan') ?></strong> <?= !empty($crc['lecturer_nidn']) ? '(NIDN: ' . esc($crc['lecturer_nidn']) . ')' : '' ?>
            </p>
        </div>
    </div>

    <!-- Flash Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 flex items-center space-x-2">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800 flex items-center space-x-2">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <!-- Banner Hasil Penilaian Akhir (Jika sudah diverifikasi Dosen) -->
    <?php if ($finalAssessment && !empty($finalAssessment['verified_status'])): ?>
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-6 text-white shadow-lg space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-emerald-500/40 pb-4">
                <div class="space-y-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white text-emerald-800 uppercase tracking-wider">
                        ✓ Nilai Akhir Telah Terbit
                    </span>
                    <h2 class="text-xl font-bold">Hasil Penilaian Konversi Mata Kuliah</h2>
                    <p class="text-xs text-emerald-100">Nilai resmi telah diverifikasi dan disahkan oleh Dosen Pengampu MK.</p>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <span class="text-xs text-emerald-200 block">Total Skor:</span>
                        <span class="text-3xl font-black text-white"><?= number_format($finalAssessment['score'], 2) ?></span>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-white text-emerald-800 flex flex-col items-center justify-center font-black shadow-md">
                        <span class="text-2xl leading-none"><?= esc($finalAssessment['final_grade']) ?></span>
                        <span class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">Grade</span>
                    </div>
                </div>
            </div>

            <?php if (!empty($finalAssessment['notes'])): ?>
                <div class="bg-emerald-800/40 p-3.5 rounded-xl border border-emerald-500/50 text-xs">
                    <span class="text-emerald-200 font-semibold block mb-0.5">Catatan Evaluasi Dosen:</span>
                    <p class="text-emerald-50 leading-relaxed"><?= nl2br(esc($finalAssessment['notes'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php 
        $subStatus = $submission['status'] ?? 'BELUM';
    ?>

    <!-- Card 1: Pengumpulan Tautan Google Drive (1 Link Drive) -->
    <div class="bg-white rounded-xl border <?= $subStatus === 'REVISION_REQUIRED' ? 'border-rose-300 ring-2 ring-rose-500/10' : ($subStatus === 'APPROVED' ? 'border-emerald-200' : 'border-slate-200') ?> shadow-sm overflow-hidden transition">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pengumpulan Berkas Magang (Google Drive)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kumpulkan 1 tautan folder Google Drive yang memuat seluruh berkas sesuai keterangan komponen di bawah</p>
            </div>
            <div>
                <?php if ($subStatus === 'APPROVED'): ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Pengumpulan Disetujui
                    </span>
                <?php elseif ($subStatus === 'REVISION_REQUIRED'): ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 inline-flex items-center animate-pulse">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Perlu Perbaikan
                    </span>
                <?php elseif ($subStatus === 'SUBMITTED'): ?>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200 inline-flex items-center">
                        <span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                        Sedang Diperiksa Dosen
                    </span>
                <?php else: ?>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                        Belum Dikumpulkan
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="p-5 space-y-4">
            <!-- Alert Catatan Perbaikan Dosen jika REVISION_REQUIRED -->
            <?php if ($subStatus === 'REVISION_REQUIRED' && !empty($submission['feedback_notes'])): ?>
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl space-y-1.5 text-xs">
                    <div class="flex items-center space-x-2 text-rose-800 font-bold">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Catatan Perbaikan dari Dosen Pengampu MK:</span>
                    </div>
                    <p class="text-rose-900 leading-relaxed bg-white p-3 rounded-lg border border-rose-200 font-medium whitespace-pre-line">
                        <?= esc($submission['feedback_notes']) ?>
                    </p>
                    <p class="text-[11px] text-rose-600 italic">Harap perbaiki berkas di folder Google Drive Anda, lalu kirim ulang tautan melalui formulir di bawah.</p>
                </div>
            <?php endif; ?>

            <!-- Info Tautan yang Sedang Aktif / Terkumpul -->
            <?php if ($submission && !empty($submission['drive_url'])): ?>
                <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="space-y-1 min-w-0">
                        <span class="text-slate-500 font-medium block">Tautan Google Drive yang Anda Kumpulkan:</span>
                        <a href="<?= esc($submission['drive_url']) ?>" target="_blank" rel="noopener noreferrer" 
                           class="font-bold text-blue-600 hover:text-blue-800 hover:underline truncate block max-w-lg">
                            <?= esc($submission['drive_url']) ?> &nearr;
                        </a>
                        <?php if (!empty($submission['student_notes'])): ?>
                            <p class="text-slate-600 italic mt-1 text-[11px]">Catatan Anda: "<?= esc($submission['student_notes']) ?>"</p>
                        <?php endif; ?>
                        <p class="text-[10px] text-slate-400">
                            Terakhir diperbarui: <?= format_indo($submission['updated_at'] ?: $submission['created_at'], true, true) ?>
                        </p>
                    </div>

                    <a href="<?= esc($submission['drive_url']) ?>" target="_blank" rel="noopener noreferrer" 
                       class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs shadow-2xs transition flex-shrink-0">
                        <span>Buka Google Drive</span>
                        <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Formulir Pengumpulan Link Drive -->
            <?php if ($subStatus !== 'APPROVED'): ?>
                <div class="pt-2 border-t border-slate-100">
                    <form action="<?= base_url('mahasiswa/penilaian/submit-drive/' . $crc['course_id']) ?>" method="POST" class="space-y-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="internship_id" value="<?= esc($crc['internship_id']) ?>">

                        <div>
                            <label for="drive_url" class="block text-xs font-semibold text-slate-800 mb-1">
                                Tautan (URL) Google Drive Berkas Magang <span class="text-red-500">*</span>:
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM19 18H6c-2.21 0-4-1.79-4-4 0-2.05 1.53-3.76 3.56-3.97l1.07-.11.5-.95C8.08 7.14 9.94 6 12 6c2.62 0 4.88 1.86 5.39 4.43l.3 1.5 1.53.11c1.56.1 2.78 1.41 2.78 2.96 0 1.65-1.35 3-3 3z"/></svg>
                                </div>
                                <input type="url" 
                                       name="drive_url" 
                                       id="drive_url" 
                                       required
                                       placeholder="https://drive.google.com/drive/folders/..." 
                                       value="<?= esc($submission['drive_url'] ?? '') ?>"
                                       class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Pastikan hak akses tautan Google Drive telah diatur ke <em>"Siapa saja yang memiliki link dapat melihat (Viewer)"</em> agar dosen pengampu dapat memeriksa berkas Anda.</p>
                        </div>

                        <div>
                            <label for="student_notes" class="block text-xs font-semibold text-slate-800 mb-1">
                                Catatan / Keterangan untuk Dosen Pengampu (Opsional):
                            </label>
                            <textarea name="student_notes" 
                                      id="student_notes" 
                                      rows="2"
                                      placeholder="Contoh: Seluruh laporan akhir, lampiran luaran project, dan logbook kegiatan telah disusun dalam subfolder..." 
                                      class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"><?= esc($submission['student_notes'] ?? '') ?></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span><?= $subStatus === 'REVISION_REQUIRED' ? 'Kirim Ulang Hasil Perbaikan' : ($submission ? 'Perbarui Tautan Google Drive' : 'Kirim Tautan Google Drive') ?></span>
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100 flex items-center text-xs text-emerald-800 space-x-2">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Pengumpulan berkas Google Drive untuk mata kuliah ini telah <strong>disetujui</strong> oleh Dosen Pengampu MK. Anda tidak perlu mengirim berkas lagi.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Card 2: Keterangan & Bobot Komponen Penilaian (Rubrik Panduan Mahasiswa) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50/50">
            <div>
                <h2 class="text-base font-bold text-slate-900">Keterangan & Bobot Komponen Penilaian</h2>
                <p class="text-xs text-slate-500 mt-0.5">Panduan tugas yang dinilai oleh Dosen Pengampu beserta bobot persentasenya</p>
            </div>
            <span class="text-xs font-bold px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg self-start sm:self-auto">
                Total Bobot: <?= number_format($totalWeight, 0) ?>%
            </span>
        </div>

        <?php if (empty($components)): ?>
            <div class="p-8 text-center text-slate-400 space-y-2">
                <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-xs font-bold text-slate-700">Dosen Pengampu Belum Menentukan Komponen Penilaian</h3>
                <p class="text-[11px] text-slate-400 max-w-sm mx-auto">Silakan hubungi dosen pengampu atau tunggu hingga rubrik penilaian ditentukan.</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-slate-100">
                <?php foreach ($components as $idx => $comp): ?>
                    <?php 
                        $compScore = $componentScores[$comp['id']] ?? null;
                    ?>
                    <div class="p-5 space-y-3 hover:bg-slate-50/40 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center space-x-2.5">
                                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                                    <?= $idx + 1 ?>
                                </span>
                                <h3 class="text-sm font-bold text-slate-900"><?= esc($comp['title']) ?></h3>
                                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                    <?= number_format($comp['weight_percentage'], 0) ?>%
                                </span>
                            </div>

                            <?php if ($compScore !== null && $finalAssessment && !empty($finalAssessment['verified_status'])): ?>
                                <span class="text-xs font-bold px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-lg self-start sm:self-auto">
                                    Skor Komponen: <strong><?= number_format($compScore, 1) ?></strong> / 100
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Keterangan apa yang perlu dikumpulkan di Drive -->
                        <div class="p-3.5 bg-slate-50 rounded-lg border border-slate-200/80 text-xs space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Keterangan yang Perlu Dikumpulkan:</span>
                            <p class="text-slate-700 leading-relaxed whitespace-pre-line">
                                <?= esc($comp['description'] ?: 'Ikuti petunjuk dan instruksi dari Dosen Pengampu MK.') ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

