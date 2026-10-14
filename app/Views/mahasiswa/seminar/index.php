<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="mb-5 bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Seminar Magang Mata Kuliah</h1>
        <p class="text-xs text-slate-500 mt-0.5">Jadwal dan status pelaksanaan seminar konversi mata kuliah pilihan pada magang aktif</p>
    </div>
    <?php if (!empty($activeInternships) && count($activeInternships) > 1): ?>
        <form method="GET" action="<?= base_url('mahasiswa/seminar') ?>">
            <select name="internship_id" onchange="this.form.submit()" class="text-xs border border-slate-300 rounded-lg px-2.5 py-1.5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                <?php foreach ($activeInternships as $item): ?>
                    <option value="<?= $item['id'] ?>" <?= ((int)$item['id'] === (int)$selectedInternship['id']) ? 'selected' : '' ?>>
                        <?= esc($item['company_name']) ?> (<?= esc($item['academic_year']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    <?php endif; ?>
</div>

<?php if (empty($selectedInternship)): ?>
    <!-- Empty State: Tidak Ada Magang Aktif -->
    <div class="bg-white rounded-xl border border-slate-200 p-10 text-center shadow-sm">
        <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <h3 class="text-sm font-bold text-slate-800">Tidak Ada Program Magang yang Sedang Berjalan (Aktif)</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
            Seminar hanya dapat diakses saat Anda memiliki program magang yang berstatus aktif berjalan.
        </p>
    </div>
<?php else: ?>

    <!-- Context Strip Minimalis -->
    <div class="mb-4 bg-slate-100/70 border border-slate-200 rounded-lg px-4 py-2.5 flex flex-wrap items-center justify-between text-xs text-slate-700 gap-2">
        <div class="flex items-center space-x-2">
            <span class="font-bold text-slate-900"><?= esc($selectedInternship['company_name']) ?></span>
            <span class="text-slate-400">&bull;</span>
            <span><?= esc($selectedInternship['position']) ?></span>
            <span class="text-slate-400">&bull;</span>
            <span class="text-slate-500"><?= format_indo($selectedInternship['start_date'], false, true) ?> - <?= format_indo($selectedInternship['end_date'], false, true) ?></span>
        </div>
        <div class="text-[11px] text-slate-500">
            TA <?= esc($selectedInternship['academic_year']) ?> (<?= esc($selectedInternship['semester']) ?>)
        </div>
    </div>

    <!-- Data Table Minimalis -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-600 uppercase tracking-wider">
                        <th class="px-5 py-3 w-12 text-center">No</th>
                        <th class="px-5 py-3">Mata Kuliah Pilihan</th>
                        <th class="px-5 py-3">Dosen Pengampu</th>
                        <th class="px-5 py-3">Status Seminar</th>
                        <th class="px-5 py-3">Jadwal Pelaksanaan</th>
                        <th class="px-5 py-3">Ruangan / Link</th>
                        <th class="px-5 py-3 text-center">Nilai</th>
                        <th class="px-5 py-3">Catatan Dosen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (!empty($seminars)): ?>
                        <?php $no = 1; foreach ($seminars as $row): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3.5 text-center text-slate-400 font-medium">
                                    <?= $no++ ?>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-slate-900"><?= esc($row['course_name']) ?></div>
                                    <div class="text-[11px] text-slate-500 mt-0.5"><?= esc($row['course_code']) ?> &bull; <?= esc($row['course_credits']) ?> SKS</div>
                                </td>
                                <td class="px-5 py-3.5 font-medium text-slate-800">
                                    <?= esc($row['lecturer_name'] ?? '-') ?>
                                </td>
                                <td class="px-5 py-3.5">
                                    <?php if (empty($row['seminar_status'])): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Menunggu Keputusan
                                        </span>
                                    <?php else: ?>
                                        <?= view('components/status_badge', ['status' => $row['seminar_status']]) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5">
                                    <?php if ($row['seminar_status'] === 'SCHEDULED' || $row['seminar_status'] === 'COMPLETED'): ?>
                                        <span class="font-semibold text-slate-800"><?= format_indo($row['schedule_date'], true, true) ?></span>
                                    <?php elseif ($row['seminar_status'] === 'NOT_REQUIRED'): ?>
                                        <span class="text-slate-400 italic">Bebas seminar</span>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5">
                                    <?php if (!empty($row['location'])): ?>
                                        <?php if (filter_var($row['location'], FILTER_VALIDATE_URL)): ?>
                                            <a href="<?= esc($row['location']) ?>" target="_blank" class="text-blue-600 hover:underline font-medium inline-flex items-center">
                                                Link Ruang
                                                <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        <?php else: ?>
                                            <span class="font-medium text-slate-700"><?= esc($row['location']) ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <?php if (!empty($row['seminar_score'])): ?>
                                        <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            <?= esc($row['seminar_score']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 max-w-xs truncate" title="<?= esc($row['seminar_notes'] ?? '') ?>">
                                    <?= esc($row['seminar_notes'] ?? '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                Belum ada mata kuliah konversi yang disetujui pada periode magang ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
