<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-0.5">
            <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-[11px] font-bold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Lembar Penilaian Instansi / Perusahaan Mitra</span>
            </div>
            <h1 class="text-lg font-bold text-slate-900 tracking-tight">Formulir Penilaian Praktikan Magang</h1>
            <p class="text-xs text-slate-500">
                Instansi: <strong class="text-slate-800"><?= esc($user['name']) ?></strong> &bull; Akun: <?= esc($user['email']) ?>
            </p>
        </div>

        <?php if ($assessment): ?>
            <div class="flex items-center space-x-2">
                <a href="<?= base_url('instansi/penilaian/cetak/' . $activeInternship['id']) ?>" target="_blank"
                   class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Nilai</span>
                </a>
                <div class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-bold shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>Rata-rata: <?= number_format($assessment['final_score'], 1) ?> (Grade <?= esc($assessment['grade']) ?>)</span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Student Switcher (Jika ada lebih dari 1 mahasiswa magang di instansi ini) -->
    <?php if (count($students) > 1): ?>
        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih Mahasiswa:</span>
            <div class="flex flex-wrap items-center gap-2">
                <?php foreach ($students as $st): ?>
                    <?php 
                        $isActive = (int)$st['id'] === (int)$activeInternship['id'];
                        $isGraded = !empty($st['assessment_id']);
                    ?>
                    <a href="<?= base_url('instansi/penilaian?mahasiswa=' . $st['id']) ?>"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center space-x-1.5 <?= $isActive ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' ?>">
                        <span><?= esc($st['student_name']) ?> (<?= esc($st['nim']) ?>)</span>
                        <?php if ($isGraded): ?>
                            <span class="w-4 h-4 rounded-full <?= $isActive ? 'bg-white text-blue-700' : 'bg-emerald-600 text-white' ?> text-[10px] font-black flex items-center justify-center">✓</span>
                        <?php else: ?>
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

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
                <span class="font-bold text-slate-800 text-sm"><?= esc($activeInternship['student_name']) ?></span>
                <span class="text-slate-500 block font-mono">NIM: <?= esc($activeInternship['nim']) ?></span>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Program Studi:</span>
                <span class="font-semibold text-slate-800"><?= esc($activeInternship['prodi_name']) ?></span>
                <span class="text-slate-500 block"><?= esc($activeInternship['student_email']) ?></span>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Instansi & Posisi:</span>
                <span class="font-semibold text-slate-800"><?= esc($activeInternship['company_name']) ?></span>
                <span class="text-slate-500 block"><?= esc($activeInternship['position'] ?: 'Praktikan Magang') ?></span>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Periode Magang:</span>
                <span class="font-semibold text-slate-800"><?= format_indo($activeInternship['start_date'], false) ?> &ndash; <?= format_indo($activeInternship['end_date'], false) ?></span>
                <span class="text-slate-400 block font-mono">TA <?= esc($activeInternship['academic_year']) ?> (<?= esc($activeInternship['semester']) ?>)</span>
            </div>
        </div>
    </div>

    <!-- Form Penilaian -->
    <form action="<?= base_url('instansi/penilaian/save/' . $activeInternship['id']) ?>" method="POST" id="instansiAssessmentForm" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Table Kriteria Penilaian -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white">A. Kriteria Penilaian Praktikan Magang</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Silakan isi nilai angka (1 - 100) pada masing-masing poin kriteria penilaian berikut</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-800 text-slate-300 rounded-md border border-slate-700">Skala 1 - 100</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase tracking-wider font-semibold">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Kriteria Penilaian & Panduan Rubrik</th>
                            <th class="py-3 px-4 w-48 text-center">Nilai (Angka)<br><span class="text-[10px] text-slate-400 lowercase font-normal">(1 - 100)</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <!-- Kriteria 1 -->
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-4 text-center font-bold text-slate-700 align-top">1.</td>
                            <td class="py-4 px-4 space-y-1.5">
                                <h3 class="font-bold text-slate-900 text-sm">Aktifitas di Lapangan</h3>
                                <ul class="list-disc list-inside text-[11px] text-slate-600 space-y-0.5 pl-1">
                                    <li>1.1 Kehadiran Mahasiswa di Instansi/Perusahaan</li>
                                    <li>1.2 Tepat Waktu dalam Kehadiran (Disiplin)</li>
                                </ul>
                            </td>
                            <td class="py-4 px-4 align-middle text-center">
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       required
                                       name="score_field_activity" 
                                       id="score1"
                                       value="<?= esc(old('score_field_activity', $assessment['score_field_activity'] ?? '')) ?>"
                                       placeholder="1 - 100" 
                                       class="score-input w-32 px-3 py-2 text-center text-sm font-bold border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                            </td>
                        </tr>

                        <!-- Kriteria 2 -->
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-4 text-center font-bold text-slate-700 align-top">2.</td>
                            <td class="py-4 px-4 space-y-1.5">
                                <h3 class="font-bold text-slate-900 text-sm">Kerajinan</h3>
                                <ul class="list-disc list-inside text-[11px] text-slate-600 space-y-0.5 pl-1">
                                    <li>2.1 Keterampilan yang Dimiliki Mahasiswa</li>
                                    <li>2.2 Penggunaan Keterampilan terhadap Kepentingan Instansi/Perusahaan</li>
                                </ul>
                            </td>
                            <td class="py-4 px-4 align-middle text-center">
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       required
                                       name="score_craftsmanship" 
                                       id="score2"
                                       value="<?= esc(old('score_craftsmanship', $assessment['score_craftsmanship'] ?? '')) ?>"
                                       placeholder="1 - 100" 
                                       class="score-input w-32 px-3 py-2 text-center text-sm font-bold border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                            </td>
                        </tr>

                        <!-- Kriteria 3 -->
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-4 text-center font-bold text-slate-700 align-top">3.</td>
                            <td class="py-4 px-4 space-y-1.5">
                                <h3 class="font-bold text-slate-900 text-sm">Inisiatif</h3>
                                <ul class="list-disc list-inside text-[11px] text-slate-600 space-y-0.5 pl-1">
                                    <li>3.1 Kecepatan dan ketepatan menguasai materi/problem yang disajikan di instansi/perusahaan</li>
                                    <li>3.2 Tanggap terhadap permasalahan yang timbul dalam lingkungan KP Mahasiswa</li>
                                    <li>3.3 Penggunaan materi yang dikuasai untuk menyelesaikan pekerjaan</li>
                                </ul>
                            </td>
                            <td class="py-4 px-4 align-middle text-center">
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       required
                                       name="score_initiative" 
                                       id="score3"
                                       value="<?= esc(old('score_initiative', $assessment['score_initiative'] ?? '')) ?>"
                                       placeholder="1 - 100" 
                                       class="score-input w-32 px-3 py-2 text-center text-sm font-bold border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                            </td>
                        </tr>

                        <!-- Kriteria 4 -->
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-4 text-center font-bold text-slate-700 align-top">4.</td>
                            <td class="py-4 px-4 space-y-1.5">
                                <h3 class="font-bold text-slate-900 text-sm">Kemampuan Mengemukakan Ide</h3>
                                <ul class="list-disc list-inside text-[11px] text-slate-600 space-y-0.5 pl-1">
                                    <li>4.1 Beradaptasi terhadap lingkungan instansi/perusahaan</li>
                                    <li>4.2 Kesopanan dalam berbicara terhadap pegawai/karyawan dan pimpinan</li>
                                    <li>4.3 Tidak pasif</li>
                                </ul>
                            </td>
                            <td class="py-4 px-4 align-middle text-center">
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       required
                                       name="score_communication" 
                                       id="score4"
                                       value="<?= esc(old('score_communication', $assessment['score_communication'] ?? '')) ?>"
                                       placeholder="1 - 100" 
                                       class="score-input w-32 px-3 py-2 text-center text-sm font-bold border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                            </td>
                        </tr>

                        <!-- Kriteria 5 -->
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-4 text-center font-bold text-slate-700 align-top">5.</td>
                            <td class="py-4 px-4 space-y-1.5">
                                <h3 class="font-bold text-slate-900 text-sm">Kemampuan Menganalisa Persoalan</h3>
                                <ul class="list-disc list-inside text-[11px] text-slate-600 space-y-0.5 pl-1">
                                    <li>5.1 Kemampuan Mengidentifikasi dan Memecahkan Masalah</li>
                                    <li>5.2 Kemampuan kerja sama</li>
                                </ul>
                            </td>
                            <td class="py-4 px-4 align-middle text-center">
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       required
                                       name="score_problem_solving" 
                                       id="score5"
                                       value="<?= esc(old('score_problem_solving', $assessment['score_problem_solving'] ?? '')) ?>"
                                       placeholder="1 - 100" 
                                       class="score-input w-32 px-3 py-2 text-center text-sm font-bold border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <!-- Jumlah Nilai -->
                        <tr class="bg-slate-50 font-bold border-t-2 border-slate-200">
                            <td colspan="2" class="py-3 px-4 text-right uppercase tracking-wider text-slate-700">Jumlah Nilai:</td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-base font-black text-slate-900" id="displaySum">
                                    <?= $assessment ? number_format($assessment['total_score'], 2) : '0.00' ?>
                                </span>
                            </td>
                        </tr>
                        <!-- Total = Jumlah Nilai / 5 -->
                        <tr class="bg-blue-50/80 font-bold border-t border-blue-100">
                            <td colspan="2" class="py-3 px-4 text-right uppercase tracking-wider text-blue-900">
                                Total = Jumlah Nilai / 5 (Nilai Rata-rata):
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <span class="text-lg font-black text-blue-700" id="displayAverage">
                                        <?= $assessment ? number_format($assessment['final_score'], 2) : '0.00' ?>
                                    </span>
                                    <span class="px-2 py-0.5 text-xs font-black bg-blue-600 text-white rounded-md shadow-xs" id="displayGrade">
                                        Grade <?= $assessment['grade'] ?? 'E' ?>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Bagian B: Uraian Lain & Identitas Pembimbing -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4">
            <div>
                <label for="feedback_notes" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">
                    B. Uraian Lain dari Pembimbing di Instansi / Perusahaan:
                </label>
                <p class="text-[11px] text-slate-500 mb-2">Tuliskan ulasan, catatan kinerja, saran pengembangan, atau apresiasi untuk mahasiswa:</p>
                <textarea name="feedback_notes" id="feedback_notes" rows="4" 
                          placeholder="Contoh: Mahasiswa menunjukkan kedisiplinan dan keterampilan yang sangat baik selama menjalankan tugas di divisi..."
                          class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white outline-none leading-relaxed transition"><?= esc(old('feedback_notes', $assessment['feedback_notes'] ?? '')) ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pembimbing / Mentor Lapangan:</label>
                    <input type="text" name="evaluator_name" 
                           value="<?= esc(old('evaluator_name', $assessment['evaluator_name'] ?? $user['name'])) ?>" 
                           placeholder="Nama lengkap penilai..." 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jabatan di Instansi / Perusahaan:</label>
                    <input type="text" name="evaluator_position" 
                           value="<?= esc(old('evaluator_position', $assessment['evaluator_position'] ?? 'Pembimbing Lapangan')) ?>" 
                           placeholder="Contoh: Senior Engineer / Team Lead" 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Penilaian:</label>
                    <input type="date" name="assessment_date" 
                           value="<?= esc(old('assessment_date', $assessment['assessment_date'] ?? date('Y-m-d'))) ?>" 
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-2">
            <button type="submit" 
                    onclick="return confirm('Apakah Anda yakin ingin menyimpan penilaian ini? Nilai resmi akan diterbitkan ke akun mahasiswa.')"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition inline-flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Penilaian Instansi</span>
            </button>
        </div>
    </form>
</div>

<!-- Script Live Calculation -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = [
            document.getElementById('score1'),
            document.getElementById('score2'),
            document.getElementById('score3'),
            document.getElementById('score4'),
            document.getElementById('score5')
        ];
        const displaySum = document.getElementById('displaySum');
        const displayAverage = document.getElementById('displayAverage');
        const displayGrade = document.getElementById('displayGrade');

        function calculateLiveTotal() {
            let sum = 0;
            inputs.forEach(inp => {
                const val = parseFloat(inp.value) || 0;
                sum += val;
            });

            const avg = sum / 5.0;

            displaySum.textContent = sum.toFixed(2);
            displayAverage.textContent = avg.toFixed(2);
            displayGrade.textContent = 'Grade ' + getGrade(avg);
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

        inputs.forEach(inp => {
            if (inp) {
                inp.addEventListener('input', calculateLiveTotal);
            }
        });
    });
</script>
<?= $this->endSection() ?>
