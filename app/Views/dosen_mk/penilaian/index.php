<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Penilaian & Pengumpulan Magang</h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola komponen pengumpulan, periksa berkas Google Drive mahasiswa, dan berikan penilaian akhir mata kuliah magang
            </p>
        </div>

        <?php if ($totalPendingReviews > 0): ?>
            <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold shadow-sm">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                <span><?= $totalPendingReviews ?> Pengumpulan Menunggu Pemeriksaan</span>
            </div>
        <?php endif; ?>
    </div>

    <?php if (empty($coursesData)): ?>
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mb-1">Belum Ada Mata Kuliah yang Diampu</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Anda belum terdaftar sebagai Dosen Pengampu mata kuliah magang / konversi manapun.</p>
        </div>
    <?php else: ?>
        <div class="space-y-8">
            <?php foreach ($coursesData as $cData): ?>
                <?php 
                    $course   = $cData['course'];
                    $students = $cData['students'];
                    $comps    = $cData['components'];
                    $weight   = $cData['total_weight'];
                ?>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition">
                    <!-- Course Header Banner -->
                    <div class="p-5 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="px-2.5 py-0.5 text-xs font-mono font-bold bg-blue-600 text-white rounded">
                                    <?= esc($course['code']) ?>
                                </span>
                                <h2 class="text-lg font-bold tracking-tight text-white"><?= esc($course['name']) ?></h2>
                                <span class="text-xs font-medium text-slate-300 bg-slate-700/80 px-2 py-0.5 rounded">
                                    <?= esc($course['credits']) ?> SKS
                                </span>
                            </div>
                            <p class="text-xs text-slate-400">
                                Total <?= count($students) ?> Mahasiswa Peserta Magang &bull; <?= count($comps) ?> Komponen Pengumpulan (Total Bobot: <?= number_format($weight, 0) ?>%)
                            </p>
                        </div>

                        <div class="flex items-center space-x-3">
                            <a href="<?= base_url('dosen-mk/penilaian/komponen/' . $course['id']) ?>" 
                               class="inline-flex items-center px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-sm transition">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Kelola Komponen Pengumpulan
                            </a>
                        </div>
                    </div>

                    <!-- Komponen Ringkasan Bar -->
                    <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-600">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-slate-700">Komponen Penilaian:</span>
                            <?php if (empty($comps)): ?>
                                <span class="text-amber-600 italic">Belum ada komponen pengumpulan ditentukan. Klik tombol "Kelola Komponen" untuk menentukan tugas.</span>
                            <?php else: ?>
                                <div class="flex flex-wrap gap-1.5">
                                    <?php foreach ($comps as $cmp): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-white border border-slate-200 text-[11px] font-medium text-slate-800 shadow-2xs">
                                            <?= esc($cmp['title']) ?> <strong class="ml-1 text-blue-600">(<?= number_format($cmp['weight_percentage'], 0) ?>%)</strong>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center space-x-4">
                            <?php if ($cData['pending_reviews'] > 0): ?>
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[11px]">
                                    <?= $cData['pending_reviews'] ?> Menunggu Review
                                </span>
                            <?php endif; ?>
                            <span class="text-slate-500">
                                Dinilai: <strong class="text-slate-800"><?= $cData['graded_count'] ?></strong> / <?= count($students) ?> Mahasiswa
                            </span>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-semibold">
                                    <th class="py-3 px-4 w-12 text-center">No</th>
                                    <th class="py-3 px-4">Mahasiswa</th>
                                    <th class="py-3 px-4">Instansi Magang</th>
                                    <th class="py-3 px-4 text-center">Status Pengumpulan</th>
                                    <th class="py-3 px-4 text-center">Nilai Akhir</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($students)): ?>
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                            Belum ada mahasiswa yang mengambil mata kuliah ini pada periode magang berjalan.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($students as $idx => $st): ?>
                                        <?php 
                                            $final  = $st['final_assessment'];
                                            $sub    = $st['submission'];
                                            $status = $sub['status'] ?? 'BELUM';
                                        ?>
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="py-3.5 px-4 text-center font-medium text-slate-400"><?= $idx + 1 ?></td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-bold text-slate-900 text-sm"><?= esc($st['student_name']) ?></div>
                                                <div class="text-slate-500 text-[11px] font-mono">NIM: <?= esc($st['nim']) ?></div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-semibold text-slate-800"><?= esc($st['company_name']) ?></div>
                                                <div class="text-slate-400 text-[11px]"><?= esc($st['position'] ?: 'Praktikan Magang') ?> &bull; TA <?= esc($st['academic_year']) ?></div>
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                <?php if (empty($comps)): ?>
                                                    <span class="text-slate-400 italic text-[11px]">Belum ada rubrik</span>
                                                <?php elseif (!$sub || $status === 'BELUM'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                        Belum Kumpul
                                                    </span>
                                                <?php elseif ($status === 'SUBMITTED'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                                        Menunggu Review
                                                    </span>
                                                <?php elseif ($status === 'REVISION_REQUIRED'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600 mr-1.5"></span>
                                                        Perlu Perbaikan
                                                    </span>
                                                <?php elseif ($status === 'APPROVED'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <svg class="w-3 h-3 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        Disetujui
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                <?php if ($final && !empty($final['verified_status'])): ?>
                                                    <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800">
                                                        <span class="text-sm font-black"><?= number_format($final['score'], 1) ?></span>
                                                        <span class="text-xs font-bold px-1.5 py-0.2 bg-emerald-600 text-white rounded">
                                                            <?= esc($final['final_grade']) ?>
                                                        </span>
                                                    </div>
                                                <?php elseif ($final && !empty($final['score'])): ?>
                                                    <div class="inline-flex items-center space-x-1 px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[11px]">
                                                        <span>Draft: <strong><?= number_format($final['score'], 1) ?></strong></span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-slate-400 italic text-[11px]">Belum dinilai</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3.5 px-4 text-right">
                                                <a href="<?= base_url('dosen-mk/penilaian/mahasiswa/' . $course['id'] . '/' . $st['internship_id']) ?>" 
                                                   class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-blue-600 text-white font-semibold text-xs shadow-sm transition">
                                                    <span>Periksa & Nilai</span>
                                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

