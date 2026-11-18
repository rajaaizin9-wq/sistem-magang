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
        <p class="text-sm text-slate-500 mt-0.5">Unggah LIRS semester aktif untuk membaca mata kuliah yang sedang Anda tempuh</p>
    </div>
</div>

<form action="<?= base_url('mahasiswa/konversi/simpan') ?>" method="POST" enctype="multipart/form-data" id="formKonversi">
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
                        <p class="text-xs text-slate-500">Pilih program magang aktif yang relevan untuk dikonversi</p>
                    </div>
                </div>

                <div>
                    <label for="internship_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Program Magang Aktif <span class="text-red-500">*</span>
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

            <!-- Step 2: Upload Berkas LIRS -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Unggah Lembar Isian Rencana Studi (LIRS)</h2>
                        <p class="text-xs text-slate-500">Unggah berkas PDF LIRS resmi dari SIPA UMRAH untuk membaca mata kuliah semester ini</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="border-2 border-dashed border-slate-300 hover:border-blue-400 rounded-xl p-5 text-center transition bg-slate-50/50">
                        <input type="file" name="lirs_file" id="lirs_file" accept=".pdf" class="hidden" required>
                        <label for="lirs_file" class="cursor-pointer block">
                            <div class="w-12 h-12 mx-auto rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <span class="text-sm font-bold text-blue-600 hover:underline block">Klik untuk memilih berkas LIRS (PDF)</span>
                            <span class="text-xs text-slate-400 block mt-1" id="lirsFileNameDisplay">Format dokumen: PDF asli SIPA UMRAH (Maksimal 5 MB)</span>
                        </label>
                    </div>

                    <!-- Status Parsing & Loading Alert -->
                    <div id="lirsLoading" class="hidden p-4 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-800 flex items-center space-x-3 animate-pulse">
                        <svg class="w-5 h-5 animate-spin text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Sedang membaca dan menganalisis berkas PDF LIRS Anda... Mohon tunggu sebentar.</span>
                    </div>

                    <div id="lirsSuccessAlert" class="hidden p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="space-y-1.5 flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <p class="font-bold text-emerald-900" id="lirsSuccessTitle">LIRS Berhasil Divalidasi</p>
                                <button type="button" id="btnPreviewSelectedLirs" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md bg-emerald-100 hover:bg-emerald-200 text-emerald-900 border border-emerald-300 transition w-fit">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Pratinjau PDF LIRS
                                </button>
                            </div>
                            <p id="lirsSuccessDetail" class="text-emerald-700 leading-relaxed">-</p>
                        </div>
                    </div>

                    <div id="lirsErrorAlert" class="hidden p-4 rounded-xl bg-red-50 border border-red-200 text-xs text-red-800 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="space-y-1">
                            <p class="font-bold text-red-900">Gagal Membaca LIRS</p>
                            <p id="lirsErrorMessage" class="text-red-700 leading-relaxed">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Pilih Mata Kuliah Konversi & Tentukan Dosen Pengampu -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <span class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Pilih Mata Kuliah & Konfirmasi Dosen Pengampu</h2>
                            <p class="text-xs text-slate-500">Centang mata kuliah pilihan yang ingin dikonversi dan konfirmasi dosen pengampunya</p>
                        </div>
                    </div>
                </div>

                <!-- Container Hasil Parsing LIRS -->
                <div id="coursesContainer" class="space-y-4">
                    <div class="p-8 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-xs font-medium">Unggah berkas LIRS (PDF) di atas untuk menampilkan mata kuliah semester ini.</p>
                    </div>
                </div>
            </div>

            <!-- Step 4: Catatan Tambahan (Opsional) -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center">4</span>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Catatan Tambahan (Opsional)</h2>
                        <p class="text-xs text-slate-500">Pesan atau keterangan tambahan untuk Dosen Pengampu dan verifikator</p>
                    </div>
                </div>

                <div>
                    <textarea name="notes" id="notes" rows="3" placeholder="Tuliskan catatan tambahan jika ada..." class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></textarea>
                </div>
            </div>

        </div>

        <!-- Summary & Action Sidebar (Right 1/3) -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm sticky top-20">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100">Ringkasan Konversi</h3>

                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between items-center text-slate-600 text-xs">
                        <span>Status Dokumen LIRS:</span>
                        <span id="lirsStatusBadge" class="font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">Belum Ada</span>
                    </div>
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
                    <ul id="selectedCoursesList" class="space-y-2 text-xs text-slate-600">
                        <li class="text-slate-400 italic">Belum ada mata kuliah yang dipilih.</li>
                    </ul>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <button type="submit" id="btnSubmit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center justify-center" disabled>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Kirim Pengajuan Konversi
                    </button>
                    <p class="text-[11px] text-slate-400 text-center mt-2.5 leading-relaxed">
                        Pengajuan akan diverifikasi bertahap oleh Dosen Pengampu MK, Dosen Pembimbing, dan Wadek 1.
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

    const lirsInput        = document.getElementById('lirs_file');
    const lirsFileNameDisplay = document.getElementById('lirsFileNameDisplay');
    const lirsLoading      = document.getElementById('lirsLoading');
    const lirsSuccessAlert = document.getElementById('lirsSuccessAlert');
    const lirsSuccessTitle = document.getElementById('lirsSuccessTitle');
    const lirsSuccessDetail= document.getElementById('lirsSuccessDetail');
    const lirsErrorAlert   = document.getElementById('lirsErrorAlert');
    const lirsErrorMessage = document.getElementById('lirsErrorMessage');
    const lirsStatusBadge  = document.getElementById('lirsStatusBadge');

    const coursesContainer = document.getElementById('coursesContainer');
    const summaryCount     = document.getElementById('summaryCount');
    const summaryCredits   = document.getElementById('summaryCredits');
    const selectedList     = document.getElementById('selectedCoursesList');
    const btnSubmit        = document.getElementById('btnSubmit');

    const lecturersList    = <?= json_encode($lecturers ?? []) ?>;
    let isLirsValid        = false;

    // Update info magang
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

    if (internshipSelect) {
        internshipSelect.addEventListener('change', function () {
            updateInternshipPreview();
            updateSummary();
        });
        updateInternshipPreview();
    }

    // Handler Upload LIRS via AJAX
    lirsInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        lirsFileNameDisplay.textContent = 'Berkas dipilih: ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        lirsSuccessAlert.classList.add('hidden');
        lirsErrorAlert.classList.add('hidden');
        lirsLoading.classList.remove('hidden');
        isLirsValid = false;
        lirsStatusBadge.className = 'font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200';
        lirsStatusBadge.textContent = 'Memverifikasi...';

        const formData = new FormData();
        formData.append('lirs_file', file);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= base_url('mahasiswa/konversi/parse-lirs') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            lirsLoading.classList.add('hidden');
            if (data.success) {
                isLirsValid = true;
                lirsStatusBadge.className = 'font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200';
                lirsStatusBadge.textContent = 'Terverifikasi';

                lirsSuccessAlert.classList.remove('hidden');
                lirsSuccessTitle.textContent = 'LIRS Terverifikasi (NIM: ' + (data.header.nim || '-') + ')';
                lirsSuccessDetail.innerHTML = `Tahun Akademik: <strong>${data.header.tahun_akademik || '-'}</strong> (Semester ${data.header.semester || '-'}). Ditemukan <strong>${data.courses.length} mata kuliah</strong>.`;

                renderCourses(data.courses, data.lecturers || lecturersList);
            } else {
                isLirsValid = false;
                lirsStatusBadge.className = 'font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded border border-red-200';
                lirsStatusBadge.textContent = 'Tidak Valid';
                lirsErrorAlert.classList.remove('hidden');
                lirsErrorMessage.textContent = data.message || 'Gagal memproses dokumen LIRS.';
                coursesContainer.innerHTML = `<div class="p-6 text-center text-red-600 bg-red-50 rounded-xl border border-red-200 text-xs">
                    ${data.message || 'Berkas LIRS tidak dapat dibaca.'} Silakan coba unggah berkas PDF LIRS yang valid.
                </div>`;
                updateSummary();
            }
        })
        .catch(err => {
            lirsLoading.classList.add('hidden');
            lirsErrorAlert.classList.remove('hidden');
            lirsErrorMessage.textContent = 'Terjadi kesalahan jaringan saat menganalisis LIRS.';
            updateSummary();
        });
    });

    // Pratinjau berkas LIRS yang dipilih mahasiswa langsung di Modal
    const btnPreviewSelectedLirs = document.getElementById('btnPreviewSelectedLirs');
    if (btnPreviewSelectedLirs) {
        btnPreviewSelectedLirs.addEventListener('click', function () {
            const file = lirsInput.files[0];
            if (file) {
                const blobUrl = URL.createObjectURL(file);
                openPdfModal(blobUrl, 'Pratinjau Berkas LIRS SIPA UMRAH', file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)');
            }
        });
    }

    // Render kartu-kartu mata kuliah hasil LIRS
    function renderCourses(courses, lecturers) {
        if (!courses || courses.length === 0) {
            coursesContainer.innerHTML = `<div class="p-6 text-center text-slate-500 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                Tidak ada mata kuliah yang terdeteksi dari berkas LIRS Anda.
            </div>`;
            updateSummary();
            return;
        }

        let html = '';
        courses.forEach((c, idx) => {
            const cleanCode = c.code.replace(/[^a-zA-Z0-9]/g, '');

            // Bangun opsi dropdown Dosen Pengampu
            let optionsHtml = '<option value="">-- Pilih Dosen Pengampu --</option>';
            lecturers.forEach(l => {
                const isSelected = (c.suggested_lecturer_id && c.suggested_lecturer_id == l.id) ? 'selected' : '';
                optionsHtml += `<option value="${l.id}" ${isSelected}>${l.name} (NIDN: ${l.nidn || '-'})</option>`;
            });

            html += `
            <div class="course-card border border-slate-200 rounded-xl transition bg-slate-50/50 p-4 space-y-3" id="course_card_${cleanCode}">
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" name="selected_courses[]" value="${cleanCode}" 
                           data-code="${c.code}" data-name="${c.name}" data-credits="${c.sks}" 
                           class="course-checkbox mt-1 h-4 w-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 text-xs font-mono font-bold bg-slate-200 text-slate-800 rounded">
                                    ${c.code}
                                </span>
                                <span class="font-semibold text-slate-900 text-sm">${c.name}</span>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-800">
                                ${c.sks} SKS
                            </span>
                        </div>
                        <input type="hidden" name="course_names[${cleanCode}]" value="${c.name}">
                        <input type="hidden" name="course_credits[${cleanCode}]" value="${c.sks}">
                    </div>
                </label>

                <!-- Pemilihan Dosen Pengampu Resmi (SIMAGANG) -->
                <div class="pt-2 border-t border-slate-200/60">
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">
                        Konfirmasi Dosen Pengampu MK (SIMAGANG) <span class="text-red-500">*</span>:
                    </label>
                    <select name="course_lecturers[${cleanCode}]" id="lecturer_${cleanCode}" 
                            class="course-lecturer-select w-full px-3 py-2 text-xs border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" disabled required>
                        ${optionsHtml}
                    </select>
                </div>

                <!-- Input Hubungan & Upload Berkas Proposal (Aktif Saat Dicentang) -->
                <div class="course-justification-box hidden pl-0 sm:pl-7 pt-2 border-t border-slate-200/80 space-y-3">
                    <div>
                        <label for="justification_${cleanCode}" class="block text-xs font-semibold text-slate-700 mb-1">
                            Hubungan Aktivitas Magang dengan MK Ini <span class="text-red-500">*</span>:
                        </label>
                        <textarea name="justifications[${cleanCode}]" 
                                  id="justification_${cleanCode}" 
                                  rows="2" 
                                  disabled
                                  placeholder="Jelaskan aktivitas atau proyek magang yang relevan dengan capaian mata kuliah ${c.name}..." 
                                  class="course-justification-input w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white" required></textarea>
                    </div>

                    <!-- Upload Berkas Proposal MK -->
                    <div class="p-3 bg-slate-100/70 rounded-lg border border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                            <label for="proposal_${cleanCode}" class="block text-xs font-semibold text-slate-800">
                                Berkas Proposal Konversi MK <span class="text-red-500">*</span>
                            </label>
                            <a href="<?= base_url('konversi/template-default') ?>" 
                               class="inline-flex items-center text-[11px] font-semibold text-blue-700 hover:text-blue-900 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Unduh Format Proposal (DOCX)
                            </a>
                        </div>
                        <input type="file" 
                               name="proposals_${cleanCode}" 
                               id="proposal_${cleanCode}" 
                               accept=".pdf,.doc,.docx" 
                               disabled
                               class="course-proposal-input w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" required>
                        <p class="text-[10px] text-slate-500 mt-1">Format berkas: PDF, DOC, atau DOCX (Maksimal 5 MB)</p>
                    </div>
                </div>
            </div>`;
        });

        coursesContainer.innerHTML = html;
        bindCourseEvents();
        updateSummary();
    }

    // Pasang listener interaktivitas dinamis
    function bindCourseEvents() {
        document.querySelectorAll('.course-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSummary);
        });

        document.querySelectorAll('.course-lecturer-select').forEach(sel => {
            sel.addEventListener('change', updateSummary);
        });

        document.querySelectorAll('.course-justification-input').forEach(inp => {
            inp.addEventListener('input', updateSummary);
        });

        document.querySelectorAll('.course-proposal-input').forEach(inp => {
            inp.addEventListener('change', updateSummary);
        });
    }

    // Hitung ringkasan SKS dan status tombol submit
    function updateSummary() {
        let count = 0;
        let totalCredits = 0;
        let selectedHtml = '';
        let allJustificationsFilled = true;
        let allLecturersSelected   = true;
        let allProposalsUploaded   = true;

        const checkboxes = document.querySelectorAll('.course-checkbox');

        checkboxes.forEach(cb => {
            const card       = document.getElementById('course_card_' + cb.value);
            const justBox    = card ? card.querySelector('.course-justification-box') : null;
            const justInput  = card ? card.querySelector('.course-justification-input') : null;
            const lecSelect  = card ? card.querySelector('.course-lecturer-select') : null;
            const propInput  = card ? card.querySelector('.course-proposal-input') : null;

            if (cb.checked) {
                count++;
                const credits = parseInt(cb.dataset.credits) || 0;
                totalCredits += credits;
                
                const lecName = (lecSelect && lecSelect.selectedIndex > 0) ? lecSelect.options[lecSelect.selectedIndex].text : 'Belum memilih dosen';

                selectedHtml += `<li class="py-1.5 border-b border-slate-100 last:border-0">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-slate-800 truncate mr-2">${cb.dataset.code} - ${cb.dataset.name}</span>
                        <span class="font-bold text-blue-600 flex-shrink-0">${credits} SKS</span>
                    </div>
                    <div class="text-[10px] text-slate-400 mt-0.5 truncate">&bull; Dosen: ${lecName}</div>
                </li>`;

                if (justBox) justBox.classList.remove('hidden');
                if (lecSelect) {
                    lecSelect.disabled = false;
                    if (!lecSelect.value) {
                        allLecturersSelected = false;
                    }
                }
                if (justInput) {
                    justInput.disabled = false;
                    if (!justInput.value.trim()) {
                        allJustificationsFilled = false;
                    }
                }
                if (propInput) {
                    propInput.disabled = false;
                    if (!propInput.files || propInput.files.length === 0) {
                        allProposalsUploaded = false;
                    }
                }
                if (card) {
                    card.classList.add('border-blue-500', 'bg-blue-50/20');
                    card.classList.remove('bg-slate-50/50');
                }
            } else {
                if (justBox) justBox.classList.add('hidden');
                if (lecSelect) {
                    lecSelect.disabled = true;
                }
                if (justInput) {
                    justInput.disabled = true;
                }
                if (propInput) {
                    propInput.disabled = true;
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
        btnSubmit.disabled = (!isLirsValid || count === 0 || !hasInternship || !allJustificationsFilled || !allLecturersSelected || !allProposalsUploaded);
    }
});
</script>
<?= $this->endSection() ?>
