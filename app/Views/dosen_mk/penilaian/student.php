<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div class="flex items-center space-x-3">
            <a href="<?= base_url('dosen-mk/penilaian') ?>" class="p-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-0.5 text-xs font-mono font-bold bg-blue-100 text-blue-800 rounded">
                        <?= esc($course['code']) ?>
                    </span>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pemeriksaan Pengumpulan & Penilaian</h1>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Mata Kuliah: <strong><?= esc($course['name']) ?></strong> &bull; Mahasiswa: <strong><?= esc($internship['student_name']) ?></strong> (NIM: <?= esc($internship['nim']) ?>)
                </p>
            </div>
        </div>

        <?php if ($finalAssessment && !empty($finalAssessment['verified_status'])): ?>
            <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-bold shadow-xs">
                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>Berkas Disetujui & Nilai Dikunci (Grade: <?= esc($finalAssessment['final_grade']) ?>)</span>
            </div>
        <?php endif; ?>
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

    <!-- Student & Internship Quick Info Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block mb-0.5">Nama Mahasiswa:</span>
                <span class="font-bold text-slate-800 text-sm"><?= esc($internship['student_name']) ?></span>
                <span class="text-slate-500 block font-mono">NIM: <?= esc($internship['nim']) ?></span>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Program Studi:</span>
                <span class="font-semibold text-slate-800"><?= esc($internship['prodi_name']) ?></span>
                <span class="text-slate-500 block"><?= esc($internship['student_email']) ?></span>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Instansi Magang:</span>
                <span class="font-semibold text-slate-800"><?= esc($internship['company_name']) ?></span>
                <span class="text-slate-500 block"><?= esc($internship['position'] ?: 'Praktikan Magang') ?></span>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Tahun Akademik:</span>
                <span class="font-semibold text-slate-800">TA <?= esc($internship['academic_year']) ?> (<?= esc($internship['semester']) ?>)</span>
                <span class="text-blue-600 font-bold block"><?= esc($course['credits']) ?> SKS Konversi</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column (2/3): Pemeriksaan Berkas Pengumpulan (Google Drive) & Rubrik Komponen -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card 1: Berkas Pengumpulan Google Drive Mahasiswa -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Berkas Pengumpulan Mahasiswa</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tautan folder Google Drive berkas magang yang dikumpulkan mahasiswa</p>
                    </div>
                    <div>
                        <?php 
                            $status = $submission['status'] ?? 'BELUM';
                        ?>
                        <?php if ($status === 'APPROVED'): ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Disetujui
                            </span>
                        <?php elseif ($status === 'REVISION_REQUIRED'): ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 inline-flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Perlu Perbaikan
                            </span>
                        <?php elseif ($status === 'SUBMITTED'): ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 inline-flex items-center">
                                <span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                Menunggu Review
                            </span>
                        <?php else: ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                Belum Mengumpulkan
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    <?php if ($submission && !empty($submission['drive_url'])): ?>
                        <!-- Tautan Drive Mahasiswa -->
                        <div class="p-4 bg-blue-50/50 border border-blue-100 rounded-xl space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM19 18H6c-2.21 0-4-1.79-4-4 0-2.05 1.53-3.76 3.56-3.97l1.07-.11.5-.95C8.08 7.14 9.94 6 12 6c2.62 0 4.88 1.86 5.39 4.43l.3 1.5 1.53.11c1.56.1 2.78 1.41 2.78 2.96 0 1.65-1.35 3-3 3z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold text-slate-800 block">Link Google Drive Pengumpulan:</span>
                                        <a href="<?= esc($submission['drive_url']) ?>" target="_blank" rel="noopener noreferrer" 
                                           class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline truncate block max-w-md">
                                            <?= esc($submission['drive_url']) ?>
                                        </a>
                                    </div>
                                </div>

                                <a href="<?= esc($submission['drive_url']) ?>" target="_blank" rel="noopener noreferrer" 
                                   class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition flex-shrink-0">
                                    <span>Buka Google Drive</span>
                                    <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>

                            <?php if (!empty($submission['student_notes'])): ?>
                                <div class="pt-2.5 border-t border-blue-100/80 text-xs">
                                    <span class="text-slate-500 font-semibold block mb-0.5">Catatan dari Mahasiswa:</span>
                                    <p class="text-slate-800 bg-white p-2.5 rounded-lg border border-blue-100 leading-relaxed">
                                        <?= nl2br(esc($submission['student_notes'])) ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($submission['feedback_notes'])): ?>
                                <div class="pt-2.5 border-t border-blue-100/80 text-xs">
                                    <span class="text-slate-600 font-semibold block mb-0.5">Catatan Review Terakhir:</span>
                                    <p class="text-slate-800 bg-white p-2.5 rounded-lg border border-slate-200 leading-relaxed">
                                        <?= nl2br(esc($submission['feedback_notes'])) ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <div class="text-[11px] text-slate-400">
                                Diunggah / diperbarui: <?= format_indo($submission['updated_at'] ?: $submission['created_at'], true, true) ?>
                            </div>
                        </div>

                        <!-- Pemeriksaan & Opsi Minta Perbaikan -->
                        <?php if ($status !== 'APPROVED'): ?>
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                                <div class="flex items-start space-x-2 text-xs text-slate-600">
                                    <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="leading-relaxed">
                                        <strong>Pemeriksaan & Penilaian Terpadu:</strong> Periksa berkas Google Drive di atas, lalu masukkan nilai komponen pada form di sebelah kanan dan klik <strong>Setujui Berkas & Kunci Nilai</strong>.
                                    </p>
                                </div>

                                <!-- Form Minta Perbaikan (Jika berkas bermasalah / tautan rusak) -->
                                <details class="group pt-2 border-t border-slate-200">
                                    <summary class="flex items-center justify-between text-xs font-semibold text-rose-700 hover:text-rose-800 cursor-pointer select-none">
                                        <span class="inline-flex items-center space-x-1.5">
                                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span>Berkas tidak lengkap atau tautan tidak dapat dibuka? Minta Perbaikan</span>
                                        </span>
                                        <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </summary>

                                    <form action="<?= base_url('dosen-mk/penilaian/review-submission/' . $submission['id']) ?>" method="POST" class="mt-3 space-y-3">
                                        <?= csrf_field() ?>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                                                Catatan / Alasan Perbaikan untuk Mahasiswa (Wajib):
                                            </label>
                                            <textarea name="feedback_notes" rows="2" required
                                                      placeholder="Contoh: Izin akses Google Drive belum diset ke publik / berkas laporan belum diunggah..."
                                                      class="w-full px-3 py-2 text-xs border border-rose-300 rounded-lg focus:ring-2 focus:ring-rose-500 bg-white outline-none"></textarea>
                                        </div>

                                        <button type="submit" name="action" value="REVISE" 
                                                class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition inline-flex items-center space-x-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span>Kirim Permintaan Perbaikan</span>
                                        </button>
                                    </form>
                                </details>
                            </div>
                        <?php else: ?>
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center space-x-2 text-xs text-emerald-800">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Berkas pengumpulan mahasiswa telah disetujui bersamaan dengan penilaian akhir.</span>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="p-8 text-center text-slate-400 space-y-2 border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                            <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs font-semibold text-slate-700">Mahasiswa belum mengumpulkan tautan Google Drive untuk mata kuliah ini</p>
                            <p class="text-[11px] text-slate-400">Setelah mahasiswa mengunggah tautan berkas di menu pengumpulan mereka, tautan akan muncul di sini untuk diperiksa.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card 2: Rubrik & Keterangan Komponen Penilaian -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Keterangan & Bobot Komponen Penilaian</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Panduan rubrik komponen tugas dan bobot persentase nilai mata kuliah ini</p>
                    </div>
                    <a href="<?= base_url('dosen-mk/penilaian/komponen/' . $course['id']) ?>" 
                       class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                        <span>Kelola Komponen</span>
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>
                </div>

                <?php if (empty($components)): ?>
                    <div class="p-8 text-center text-slate-400 space-y-2">
                        <p class="text-xs font-semibold text-slate-700">Belum ada komponen penilaian yang diatur</p>
                        <a href="<?= base_url('dosen-mk/penilaian/komponen/' . $course['id']) ?>" 
                           class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-semibold">
                            Atur Komponen Sekarang
                        </a>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($components as $idx => $comp): ?>
                            <div class="p-4 space-y-2 hover:bg-slate-50/50 transition">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">
                                            <?= $idx + 1 ?>
                                        </span>
                                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm"><?= esc($comp['title']) ?></h3>
                                    </div>
                                    <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                        Bobot: <?= number_format($comp['weight_percentage'], 0) ?>%
                                    </span>
                                </div>
                                <?php if (!empty($comp['description'])): ?>
                                    <p class="text-xs text-slate-600 pl-7 leading-relaxed whitespace-pre-line"><?= esc($comp['description']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="p-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                        <span class="font-semibold">Total Bobot Komponen:</span>
                        <span class="font-bold <?= $totalWeight == 100 ? 'text-emerald-600' : 'text-amber-600' ?>">
                            <?= number_format($totalWeight, 0) ?>% <?= $totalWeight == 100 ? '(Sesuai)' : '(Belum 100%)' ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Right Column (1/3): Form Penilaian Akhir Terintegrasi -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm sticky top-20 overflow-hidden">
                <div class="p-4 bg-slate-900 text-white">
                    <h2 class="text-sm font-bold text-white">Form Penilaian Mata Kuliah</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Kalkulasi nilai akhir berdasarkan bobot tiap komponen</p>
                </div>

                <form action="<?= base_url('dosen-mk/penilaian/save-grade/' . $course['id'] . '/' . $internship['id']) ?>" 
                      method="POST" 
                      id="gradeForm"
                      class="p-5 space-y-5">
                    <?= csrf_field() ?>

                    <!-- Komponen Score Inputs -->
                    <div class="space-y-3">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Nilai Komponen (0 - 100):</span>

                        <?php foreach ($components as $comp): ?>
                            <?php 
                                $existingScore = $componentScores[$comp['id']] ?? '';
                            ?>
                            <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/60 space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-800"><?= esc($comp['title']) ?></span>
                                    <span class="text-[11px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded">
                                        <?= number_format($comp['weight_percentage'], 0) ?>%
                                    </span>
                                </div>
                                <div class="relative">
                                    <input type="number" 
                                           step="0.01" 
                                           min="0" 
                                           max="100" 
                                           name="scores[<?= $comp['id'] ?>]" 
                                           data-weight="<?= $comp['weight_percentage'] ?>"
                                           value="<?= esc($existingScore) ?>"
                                           placeholder="0 - 100"
                                           class="score-input w-full px-3 py-1.5 text-sm font-bold text-slate-900 border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Live Calculation Result Card -->
                    <div class="p-4 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 space-y-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-blue-900 block">Kalkulasi Nilai Akhir:</span>
                        
                        <div class="flex items-baseline justify-between">
                            <div>
                                <span class="text-3xl font-black text-blue-700" id="liveScoreDisplay">
                                    <?= $finalAssessment ? number_format($finalAssessment['score'], 2) : '0.00' ?>
                                </span>
                                <span class="text-xs text-slate-500 font-medium">/ 100</span>
                            </div>

                            <div class="text-right">
                                <span class="text-xs text-slate-500 block">Huruf Mutu:</span>
                                <span class="text-2xl font-black px-2.5 py-0.5 bg-blue-600 text-white rounded-lg inline-block shadow-sm" id="liveGradeDisplay">
                                    <?= $finalAssessment['final_grade'] ?? 'E' ?>
                                </span>
                            </div>
                        </div>

                        <p class="text-[10px] text-slate-500 italic mt-1 leading-tight">
                            Nilai akhir dihitung secara otomatis berdasarkan skor dan persentase bobot masing-masing komponen.
                        </p>
                    </div>

                    <!-- Catatan Evaluasi Dosen -->
                    <div>
                        <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1">
                            Catatan Evaluasi Dosen (Opsional):
                        </label>
                        <textarea name="notes" id="notes" rows="2" 
                                  placeholder="Catatan apresiasi atau saran evaluasi untuk mahasiswa..."
                                  class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"><?= esc($finalAssessment['notes'] ?? '') ?></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <button type="submit" 
                                name="is_verified" 
                                value="1"
                                onclick="return confirm('Apakah Anda yakin ingin menyetujui berkas dan mengunci nilai akhir ini? Berkas pengumpulan akan disetujui dan nilai resmi akan diterbitkan ke akun mahasiswa.')"
                                class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Setujui Berkas & Kunci Nilai</span>
                        </button>

                        <button type="submit" 
                                name="is_verified" 
                                value="0"
                                class="w-full py-2 px-4 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg shadow-sm transition flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span>Simpan Sebagai Draft</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- Script Live Calculation -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const scoreInputs = document.querySelectorAll('.score-input');
        const liveScore = document.getElementById('liveScoreDisplay');
        const liveGrade = document.getElementById('liveGradeDisplay');

        function calculateLiveTotal() {
            let total = 0;
            scoreInputs.forEach(inp => {
                const val = parseFloat(inp.value) || 0;
                const weight = parseFloat(inp.dataset.weight) || 0;
                total += (val * (weight / 100));
            });

            liveScore.textContent = total.toFixed(2);
            liveGrade.textContent = getGrade(total);
        }

        function getGrade(score) {
            if (score >= 85.0) return 'A';
            if (score >= 80.0) return 'A-';
            if (score >= 75.0) return 'B+';
            if (score >= 70.0) return 'B';
            if (score >= 65.0) return 'B-';
            if (score >= 60.0) return 'C+';
            if (score >= 55.0) return 'C';
            if (score >= 40.0) return 'D';
            return 'E';
        }

        scoreInputs.forEach(inp => {
            inp.addEventListener('input', calculateLiveTotal);
        });
    });
</script>
<?= $this->endSection() ?>

