<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Seminar Magang (Mata Kuliah Pengampu)</h1>
        <p class="text-sm text-slate-500 mt-1">Tentukan apakah mahasiswa perlu mengikuti seminar untuk mata kuliah yang Anda ampu serta jadwalkan & nilai pelaksanaannya</p>
    </div>
</div>

<!-- Metric Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Belum Ditentukan</span>
        <div class="text-2xl font-bold text-slate-800 mt-1"><?= $pendingCount ?></div>
        <p class="text-[11px] text-slate-500 mt-0.5">Perlu keputusan dosen</p>
    </div>
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-blue-500 uppercase tracking-wider block">Seminar Terjadwal</span>
        <div class="text-2xl font-bold text-blue-600 mt-1"><?= $scheduledCount ?></div>
        <p class="text-[11px] text-slate-500 mt-0.5">Menunggu pelaksanaan</p>
    </div>
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-emerald-500 uppercase tracking-wider block">Selesai Dinilai</span>
        <div class="text-2xl font-bold text-emerald-600 mt-1"><?= $completedCount ?></div>
        <p class="text-[11px] text-slate-500 mt-0.5">Nilai telah diinput</p>
    </div>
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Bebas Seminar</span>
        <div class="text-2xl font-bold text-slate-600 mt-1"><?= $notRequiredCount ?></div>
        <p class="text-[11px] text-slate-500 mt-0.5">Tidak perlu seminar</p>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Mahasiswa Konversi MK</h2>
        <span class="text-xs text-slate-500">Hanya menampilkan mata kuliah konversi yang telah disetujui</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <th class="px-6 py-3.5">Mahasiswa</th>
                    <th class="px-6 py-3.5">Mata Kuliah Diajukan</th>
                    <th class="px-6 py-3.5">Instansi Magang</th>
                    <th class="px-6 py-3.5">Status Seminar</th>
                    <th class="px-6 py-3.5">Jadwal / Nilai</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (!empty($candidates)): ?>
                    <?php foreach ($candidates as $row): ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?= esc($row['student_name']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">NIM: <?= esc($row['nim']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800"><?= esc($row['course_name']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5"><?= esc($row['course_code']) ?> &bull; <?= esc($row['course_credits']) ?> SKS</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-medium text-slate-800"><?= esc($row['company_name']) ?></div>
                                <div class="text-[11px] text-slate-500"><?= esc($row['position']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if (empty($row['seminar_status'])): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        Belum Ditentukan
                                    </span>
                                <?php else: ?>
                                    <?= view('components/status_badge', ['status' => $row['seminar_status']]) ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <?php if ($row['seminar_status'] === 'SCHEDULED'): ?>
                                    <div class="font-bold text-blue-700"><?= format_indo($row['schedule_date'], true, true) ?></div>
                                    <div class="text-slate-500 mt-0.5"><?= esc($row['location']) ?></div>
                                <?php elseif ($row['seminar_status'] === 'COMPLETED'): ?>
                                    <div class="flex items-center space-x-1.5">
                                        <span class="font-bold text-emerald-700 text-sm">Nilai: <?= esc($row['seminar_score']) ?></span>
                                    </div>
                                    <?php if (!empty($row['seminar_notes'])): ?>
                                        <div class="text-[11px] text-slate-400 truncate max-w-xs" title="<?= esc($row['seminar_notes']) ?>">
                                            Catatan: <?= esc($row['seminar_notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php elseif ($row['seminar_status'] === 'NOT_REQUIRED'): ?>
                                    <span class="text-slate-400 italic">Bebas seminar</span>
                                <?php else: ?>
                                    <span class="text-slate-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center space-x-1.5">
                                    <?php if (empty($row['seminar_status'])): ?>
                                        <!-- Tombol Tentukan: Bebas Seminar atau Jadwalkan -->
                                        <button type="button" onclick="confirmNotRequired(<?= $row['internship_id'] ?>, <?= $row['course_id'] ?>, '<?= esc(addslashes($row['course_name'])) ?>')"
                                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-md transition">
                                            Bebas Seminar
                                        </button>
                                        <button type="button" onclick="openScheduleModal(<?= $row['internship_id'] ?>, <?= $row['course_id'] ?>, '<?= esc(addslashes($row['student_name'])) ?>', '<?= esc(addslashes($row['course_name'])) ?>')"
                                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                            Jadwalkan
                                        </button>
                                    <?php elseif ($row['seminar_status'] === 'SCHEDULED'): ?>
                                        <!-- Tombol Input Nilai atau Ubah Jadwal -->
                                        <button type="button" onclick="openScoreModal(<?= $row['seminar_id'] ?>, '<?= esc(addslashes($row['student_name'])) ?>', '<?= esc(addslashes($row['course_name'])) ?>')"
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                            Input Nilai
                                        </button>
                                        <button type="button" onclick="openScheduleModal(<?= $row['internship_id'] ?>, <?= $row['course_id'] ?>, '<?= esc(addslashes($row['student_name'])) ?>', '<?= esc(addslashes($row['course_name'])) ?>', '<?= esc($row['schedule_date']) ?>', '<?= esc(addslashes($row['location'])) ?>', '<?= esc(addslashes($row['seminar_notes'] ?? '')) ?>')"
                                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-md transition">
                                            Ubah
                                        </button>
                                    <?php elseif ($row['seminar_status'] === 'COMPLETED'): ?>
                                        <!-- Ubah Nilai -->
                                        <button type="button" onclick="openScoreModal(<?= $row['seminar_id'] ?>, '<?= esc(addslashes($row['student_name'])) ?>', '<?= esc(addslashes($row['course_name'])) ?>', '<?= esc($row['seminar_score']) ?>', '<?= esc(addslashes($row['seminar_notes'] ?? '')) ?>')"
                                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-md transition">
                                            Ubah Nilai
                                        </button>
                                    <?php elseif ($row['seminar_status'] === 'NOT_REQUIRED'): ?>
                                        <!-- Ubah Keputusan Jadi Butuh Seminar -->
                                        <button type="button" onclick="openScheduleModal(<?= $row['internship_id'] ?>, <?= $row['course_id'] ?>, '<?= esc(addslashes($row['student_name'])) ?>', '<?= esc(addslashes($row['course_name'])) ?>')"
                                            class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-md transition">
                                            Jadwalkan Seminar
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <p class="font-medium text-slate-600">Belum Ada Mahasiswa Konversi</p>
                            <p class="text-xs text-slate-400 mt-1">Daftar mahasiswa akan muncul setelah pengajuan konversi ke mata kuliah Anda telah disetujui.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Jadwalkan Seminar (Vanilla JS) -->
<div id="scheduleModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="font-bold text-slate-800 text-sm">Jadwalkan Seminar Magang</h3>
            <button type="button" onclick="closeScheduleModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="<?= base_url('dosen-mk/seminar/keputusan') ?>" method="POST" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="internship_id" id="sch_internship_id">
            <input type="hidden" name="course_id" id="sch_course_id">
            <input type="hidden" name="decision" value="SCHEDULED">

            <div>
                <span class="text-xs text-slate-400 font-semibold block uppercase">Mahasiswa & Mata Kuliah</span>
                <div class="text-sm font-bold text-slate-800 mt-0.5" id="sch_student_info">-</div>
                <div class="text-xs text-blue-600 font-medium" id="sch_course_info">-</div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal & Waktu Seminar <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="schedule_date" id="sch_date" required class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Ruangan / Tautan Online <span class="text-red-500">*</span></label>
                <input type="text" name="location" id="sch_location" required placeholder="Contoh: Lab Komputer 2 atau Google Meet: https://..." class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Arahan Mahasiswa <span class="text-slate-400 font-normal">(opsional)</span></label>
                <textarea name="notes" id="sch_notes" rows="2" placeholder="Contoh: Siapkan presentasi slide maksimal 10 menit..." class="w-full text-xs border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeScheduleModal()" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Simpan Jadwal Seminar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Input Nilai Seminar (Vanilla JS) -->
<div id="scoreModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="font-bold text-slate-800 text-sm">Penilaian Seminar Magang</h3>
            <button type="button" onclick="closeScoreModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="scoreForm" action="" method="POST" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <div>
                <span class="text-xs text-slate-400 font-semibold block uppercase">Mahasiswa & Mata Kuliah</span>
                <div class="text-sm font-bold text-slate-800 mt-0.5" id="score_student_info">-</div>
                <div class="text-xs text-blue-600 font-medium" id="score_course_info">-</div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Skor Nilai Seminar (0 - 100) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" max="100" name="score" id="score_value" required placeholder="Contoh: 85.5" class="w-full text-base font-bold text-slate-900 border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Evaluasi / Masukan Dosen <span class="text-slate-400 font-normal">(opsional)</span></label>
                <textarea name="notes" id="score_notes" rows="3" placeholder="Tuliskan catatan evaluasi presentasi seminar..." class="w-full text-xs border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeScoreModal()" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden Form untuk Bebas Seminar -->
<form id="notRequiredForm" action="<?= base_url('dosen-mk/seminar/keputusan') ?>" method="POST" class="hidden">
    <?= csrf_field() ?>
    <input type="hidden" name="internship_id" id="nr_internship_id">
    <input type="hidden" name="course_id" id="nr_course_id">
    <input type="hidden" name="decision" value="NOT_REQUIRED">
</form>

<script>
    function openScheduleModal(internshipId, courseId, studentName, courseName, scheduleDate, location, notes) {
        document.getElementById('sch_internship_id').value = internshipId;
        document.getElementById('sch_course_id').value = courseId;
        document.getElementById('sch_student_info').textContent = studentName;
        document.getElementById('sch_course_info').textContent = courseName;
        document.getElementById('sch_date').value = scheduleDate ? scheduleDate.replace(' ', 'T').substring(0, 16) : '';
        document.getElementById('sch_location').value = location || '';
        document.getElementById('sch_notes').value = notes || '';
        document.getElementById('scheduleModal').classList.remove('hidden');
    }

    function closeScheduleModal() {
        document.getElementById('scheduleModal').classList.add('hidden');
    }

    function openScoreModal(seminarId, studentName, courseName, score, notes) {
        const form = document.getElementById('scoreForm');
        form.action = '<?= base_url('dosen-mk/seminar/nilai/') ?>/' + seminarId;
        document.getElementById('score_student_info').textContent = studentName;
        document.getElementById('score_course_info').textContent = courseName;
        document.getElementById('score_value').value = score || '';
        document.getElementById('score_notes').value = notes || '';
        document.getElementById('scoreModal').classList.remove('hidden');
    }

    function closeScoreModal() {
        document.getElementById('scoreModal').classList.add('hidden');
    }

    function confirmNotRequired(internshipId, courseId, courseName) {
        if (confirm('Apakah Anda yakin mata kuliah "' + courseName + '" TIDAK MEMERLUKAN seminar magang?')) {
            document.getElementById('nr_internship_id').value = internshipId;
            document.getElementById('nr_course_id').value = courseId;
            document.getElementById('notRequiredForm').submit();
        }
    }

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeScheduleModal();
            closeScoreModal();
        }
    });
</script>

<?= $this->endSection() ?>
