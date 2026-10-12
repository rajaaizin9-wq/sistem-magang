<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Breadcrumbs & Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="<?= base_url('mahasiswa/konversi') ?>" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Konversi
        </a>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Formulir Pengajuan Konversi MK Pilihan</h1>
        <p class="text-sm text-slate-500 mt-0.5">Pilih periode magang dan mata kuliah pilihan yang relevan dengan aktivitas magang Anda</p>
    </div>
</div>

<form action="<?= base_url('mahasiswa/konversi/simpan') ?>" method="POST" id="formKonversi">
    <?= csrf_field() ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form Column (Left 2/3) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Step 1: Pilih Periode Magang -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Pilih Periode Magang</h2>
                        <p class="text-xs text-slate-500">Pilih magang yang sedang berjalan untuk diajukan konversi</p>
                    </div>
                </div>

                <div>
                    <label for="internship_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Program Magang Aktif
                    </label>
                    <select name="internship_id" id="internship_id" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                        <?php if (count($internships) > 1): ?>
                            <option value="">-- Pilih Program Magang --</option>
                        <?php endif; ?>
                        <?php foreach ($internships as $idx => $item): ?>
                            <option value="<?= $item['id'] ?>" 
                                    data-company="<?= esc($item['company_name']) ?>" 
                                    data-position="<?= esc($item['position']) ?>" 
                                    data-type="<?= esc($item['internship_type'] ?? 'Offline') ?>" 
                                    data-period="<?= format_indo($item['start_date'], false, true) ?> &ndash; <?= format_indo($item['end_date'], false, true) ?>" 
                                    data-year="<?= esc($item['academic_year']) ?> (<?= esc($item['semester']) ?>)"
                                    data-address="<?= esc($item['company_address']) ?>"
                                    <?= ($idx === 0) ? 'selected' : '' ?>>
                                <?= esc($item['company_name']) ?> &mdash; <?= esc($item['position']) ?> (TA <?= esc($item['academic_year']) ?> <?= esc($item['semester']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Preview Info Box -->
                    <div id="internshipPreviewBox" class="mt-3.5 p-3.5 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-600 space-y-1">
                        <div class="flex items-center justify-between">
                            <span id="previewCompany" class="font-bold text-slate-900 text-sm">-</span>
                            <span id="previewType" class="px-2 py-0.5 rounded font-medium bg-emerald-100 text-emerald-800 text-[11px]">-</span>
                        </div>
                        <p id="previewPosition" class="text-slate-700 font-medium">-</p>
                        <p id="previewMeta" class="text-slate-500">-</p>
                    </div>
                </div>
            </div>

            <!-- Step 2: Pilih Mata Kuliah Pilihan -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <span class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Pilih Mata Kuliah Pilihan</h2>
                            <p class="text-xs text-slate-500">Mata kuliah pilihan yang tersedia pada Program Studi <?= esc($student['prodi_name'] ?? 'Anda') ?></p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($electiveCourses)): ?>
                    <div class="space-y-3">
                        <?php foreach ($electiveCourses as $course): ?>
                            <div class="course-card border border-slate-200 rounded-xl transition bg-slate-50/50 p-4 space-y-3" id="course_card_<?= $course['id'] ?>">
                                <label class="flex items-start cursor-pointer">
                                    <input type="checkbox" name="courses[]" value="<?= $course['id'] ?>" data-credits="<?= $course['credits'] ?>" data-name="<?= esc($course['name']) ?>" data-code="<?= esc($course['code']) ?>" class="course-checkbox mt-1 h-4 w-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-0.5 text-xs font-mono font-bold bg-slate-200 text-slate-800 rounded">
                                                    <?= esc($course['code']) ?>
                                                </span>
                                                <span class="font-semibold text-slate-900 text-sm"><?= esc($course['name']) ?></span>
                                            </div>
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-800">
                                                <?= esc($course['credits']) ?> SKS
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-1.5 flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            Dosen Pengampu: <strong class="ml-1 text-slate-700"><?= esc($course['lecturer_name'] ?? 'Belum ditentukan') ?></strong>
                                        </p>
                                    </div>
                                </label>

                                <!-- Wajib: Penjelasan hubungan aktivitas magang dengan MK ini -->
                                <div class="course-justification-box hidden pl-7 pt-2.5 border-t border-slate-200/80">
                                    <label for="justification_<?= $course['id'] ?>" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Hubungan Aktivitas Magang dengan Mata Kuliah Ini <span class="text-red-500">*</span>:
                                    </label>
                                    <textarea name="justifications[<?= $course['id'] ?>]" 
                                              id="justification_<?= $course['id'] ?>" 
                                              rows="2" 
                                              disabled
                                              placeholder="Jelaskan aktivitas atau proyek magang yang relevan dengan capaian mata kuliah <?= esc($course['name']) ?>..." 
                                              class="course-justification-input w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white" required></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-6 text-center text-slate-500 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                        <p class="text-sm font-semibold">Tidak ada mata kuliah pilihan yang tersedia saat ini.</p>
                        <p class="text-xs text-slate-400 mt-1">Silakan hubungi admin program studi untuk informasi lebih lanjut.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Step 3: Catatan Tambahan (Opsional) -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Catatan Tambahan</h2>
                        <p class="text-xs text-slate-500">Informasi atau catatan umum lainnya untuk tim verifikator (Opsional)</p>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Catatan Umum (Opsional)
                    </label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Tuliskan catatan tambahan jika ada..." class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></textarea>
                </div>
            </div>

        </div>

        <!-- Summary & Action Sidebar (Right 1/3) -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm sticky top-20">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100">Ringkasan Konversi</h3>

                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Jumlah MK Dipilih:</span>
                        <span id="summaryCount" class="font-bold text-slate-900">0 MK</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Total SKS Konversi:</span>
                        <span id="summaryCredits" class="text-base font-bold text-blue-600">0 SKS</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Mata Kuliah Terpilih:</span>
                    <ul id="selectedCoursesList" class="space-y-1.5 text-xs text-slate-600">
                        <li class="text-slate-400 italic">Belum ada mata kuliah yang dipilih.</li>
                    </ul>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <button type="submit" id="btnSubmit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Kirim Pengajuan Konversi
                    </button>
                    <p class="text-[11px] text-slate-400 text-center mt-2.5">
                        Pengajuan akan diverifikasi bertahap oleh Dosen MK, Dosen Pembimbing, dan Wadek 1.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const internshipSelect = document.getElementById('internship_id');
    const previewBox       = document.getElementById('internshipPreviewBox');
    const previewCompany   = document.getElementById('previewCompany');
    const previewType      = document.getElementById('previewType');
    const previewPosition  = document.getElementById('previewPosition');
    const previewMeta      = document.getElementById('previewMeta');

    const checkboxes       = document.querySelectorAll('.course-checkbox');
    const summaryCount     = document.getElementById('summaryCount');
    const summaryCredits   = document.getElementById('summaryCredits');
    const selectedList     = document.getElementById('selectedCoursesList');
    const btnSubmit        = document.getElementById('btnSubmit');

    function updateInternshipPreview() {
        if (!internshipSelect) return;
        const selectedOpt = internshipSelect.options[internshipSelect.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            if (previewBox) previewBox.classList.add('hidden');
            return;
        }

        if (previewBox) {
            previewBox.classList.remove('hidden');
            previewCompany.textContent  = selectedOpt.dataset.company || '-';
            previewType.textContent     = selectedOpt.dataset.type || 'Offline';
            previewPosition.textContent = 'Posisi: ' + (selectedOpt.dataset.position || '-');
            previewMeta.innerHTML       = (selectedOpt.dataset.period || '-') + ' &bull; TA ' + (selectedOpt.dataset.year || '-');
        }
    }

    function updateSummary() {
        let count = 0;
        let totalCredits = 0;
        let selectedHtml = '';
        let allJustificationsFilled = true;

        checkboxes.forEach(cb => {
            const card = document.getElementById('course_card_' + cb.value);
            const justBox = card ? card.querySelector('.course-justification-box') : null;
            const justInput = card ? card.querySelector('.course-justification-input') : null;

            if (cb.checked) {
                count++;
                const credits = parseInt(cb.dataset.credits) || 0;
                totalCredits += credits;
                selectedHtml += `<li class="flex items-center justify-between py-1 border-b border-slate-100 last:border-0">
                    <span class="font-medium text-slate-800 truncate mr-2">${cb.dataset.code} - ${cb.dataset.name}</span>
                    <span class="font-bold text-blue-600 flex-shrink-0">${credits} SKS</span>
                </li>`;

                if (justBox) justBox.classList.remove('hidden');
                if (justInput) {
                    justInput.disabled = false;
                    if (!justInput.value.trim()) {
                        allJustificationsFilled = false;
                    }
                }
                if (card) {
                    card.classList.add('border-blue-500', 'bg-blue-50/20');
                    card.classList.remove('bg-slate-50/50');
                }
            } else {
                if (justBox) justBox.classList.add('hidden');
                if (justInput) {
                    justInput.disabled = true;
                }
                if (card) {
                    card.classList.remove('border-blue-500', 'bg-blue-50/20');
                    card.classList.add('bg-slate-50/50');
                }
            }
        });

        summaryCount.textContent = count + ' MK';
        summaryCredits.textContent = totalCredits + ' SKS';
        selectedList.innerHTML = selectedHtml || '<li class="text-slate-400 italic">Belum ada mata kuliah yang dipilih.</li>';
        
        const hasInternship = internshipSelect && internshipSelect.value !== '';
        btnSubmit.disabled = (count === 0 || !hasInternship || !allJustificationsFilled);
    }

    if (internshipSelect) {
        internshipSelect.addEventListener('change', function () {
            updateInternshipPreview();
            updateSummary();
        });
        updateInternshipPreview();
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSummary);
    });

    document.querySelectorAll('.course-justification-input').forEach(input => {
        input.addEventListener('input', updateSummary);
    });

    updateSummary();
});
</script>
<?= $this->endSection() ?>
