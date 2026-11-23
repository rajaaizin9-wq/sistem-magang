<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Form Pengajuan Magang Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Lengkapi data instansi dan berkas surat permohonan magang</p>
        </div>
        <a href="<?= base_url('mahasiswa/pendaftaran') ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali
        </a>
    </div>

    <!-- Error Validation Alert -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded-r-lg shadow-sm">
            <p class="font-bold mb-1">Terjadi kesalahan pengisian form:</p>
            <ul class="list-disc list-inside space-y-1">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('mahasiswa/pendaftaran/simpan') ?>" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
        <?= csrf_field() ?>

        <!-- Section 1: Profil Mahasiswa (Read-only) -->
        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Identitas Pemohon</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block">Nama Mahasiswa</span>
                    <span class="font-semibold text-slate-800"><?= esc(session()->get('userName')) ?></span>
                </div>
                <div>
                    <span class="text-slate-400 block">NIM / Program Studi</span>
                    <span class="font-semibold text-slate-800"><?= esc($student['nim'] ?? '-') ?> (<?= esc($student['prodi_code'] ?? 'IF') ?>)</span>
                </div>
            </div>
        </div>

        <!-- Section 2: Informasi Perusahaan / Instansi -->
        <div>
            <h3 class="text-sm font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4">1. Data Instansi / Perusahaan Tujuan</h3>
            
            <div class="space-y-4 text-sm">
                <div>
                    <label for="company_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Instansi / Perusahaan <span class="text-red-500">*</span></label>
                    <input type="text" id="company_name" name="company_name" value="<?= old('company_name') ?>" required placeholder="Contoh: PT. Telkom Indonesia" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm">
                </div>

                <div>
                    <label for="company_address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Instansi <span class="text-red-500">*</span></label>
                    <textarea id="company_address" name="company_address" rows="2" required placeholder="Jl. Terusan Buah Batu No. 33, Bandung" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm"><?= old('company_address') ?></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="position" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Posisi Magang <span class="text-red-500">*</span></label>
                        <input type="text" id="position" name="position" value="<?= old('position') ?>" required placeholder="Contoh: Web Developer Intern" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm">
                    </div>
                    <div>
                        <label for="internship_type" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Jenis Magang <span class="text-red-500">*</span></label>
                        <select id="internship_type" name="internship_type" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm">
                            <option value="Offline" <?= old('internship_type', 'Offline') === 'Offline' ? 'selected' : '' ?>>Offline (Tatap Muka / WFO)</option>
                            <option value="Online" <?= old('internship_type') === 'Online' ? 'selected' : '' ?>>Online (Daring / WFH)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="academic_year" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tahun Akademik <span class="text-red-500">*</span></label>
                        <select id="academic_year" name="academic_year" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm">
                            <option value="2025/2026" <?= old('academic_year') == '2025/2026' ? 'selected' : '' ?>>2025/2026</option>
                            <option value="2026/2027" <?= old('academic_year') == '2026/2027' ? 'selected' : '' ?>>2026/2027</option>
                        </select>
                    </div>
                    <div>
                        <label for="semester" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Semester <span class="text-red-500">*</span></label>
                        <select id="semester" name="semester" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm">
                            <option value="Ganjil" <?= old('semester') == 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                            <option value="Genap" <?= old('semester') == 'Genap' ? 'selected' : '' ?>>Genap</option>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="date_range" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Rentang Tanggal Magang <span class="text-red-500">*</span>
                        </label>
                        <span id="range_duration_badge" class="hidden text-xs font-semibold px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded-full border border-blue-200"></span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input type="text" id="date_range" placeholder="Pilih rentang tanggal mulai s.d. selesai magang..." readonly
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm cursor-pointer transition">
                        <button type="button" id="clear_date_range" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" title="Reset rentang tanggal">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Hidden native inputs so backend validation & saving remain 100% compatible -->
                    <input type="hidden" id="start_date" name="start_date" value="<?= esc(old('start_date', '')) ?>" required>
                    <input type="hidden" id="end_date" name="end_date" value="<?= esc(old('end_date', '')) ?>" required>

                    <p class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Klik pada kolom untuk membuka kalender dan pilih tanggal mulai hingga selesai.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 3: Upload Dokumen Wajib -->
        <div>
            <h3 class="text-sm font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4">2. Upload Dokumen Persyaratan (Max 2MB per File)</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <label for="surat_pengantar" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Surat Pengantar Kampus <span class="text-red-500">*</span></label>
                    <p class="text-xs text-slate-500 mb-2">Format PDF / JPG / PNG (Max 2MB)</p>
                    <input type="file" id="surat_pengantar" name="surat_pengantar" required accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <label for="surat_balasan" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Surat Balasan / Pendukung Instansi <span class="text-red-500">*</span></label>
                    <p class="text-xs text-slate-500 mb-2">Format PDF / JPG / PNG (Max 2MB)</p>
                    <input type="file" id="surat_balasan" name="surat_balasan" required accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-200 flex justify-end space-x-3">
            <a href="<?= base_url('mahasiswa/pendaftaran') ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                Kirim Pengajuan Magang
            </button>
        </div>
    </form>
</div>

<!-- Flatpickr Date Range Assets & Init -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar {
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e2e8f0 !important;
        font-family: inherit !important;
    }
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
        background: #2563eb !important;
        border-color: #2563eb !important;
        color: #ffffff !important;
        font-weight: 600;
    }
    .flatpickr-day.inRange {
        background: #eff6ff !important;
        border-color: #eff6ff !important;
        color: #1d4ed8 !important;
        box-shadow: -5px 0 0 #eff6ff, 5px 0 0 #eff6ff !important;
    }
    .flatpickr-day.selected.startRange + .endRange:not(:nth-child(7n+1)), 
    .flatpickr-day.startRange.startRange + .endRange:not(:nth-child(7n+1)) {
        box-shadow: -10px 0 0 #2563eb !important;
    }
    .flatpickr-day:hover:not(.selected):not(.startRange):not(.endRange) {
        background: #f1f5f9 !important;
        border-color: #f1f5f9 !important;
    }
    .flatpickr-months .flatpickr-month {
        color: #1e293b !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const badge = document.getElementById('range_duration_badge');
        const clearBtn = document.getElementById('clear_date_range');

        function updateDuration(start, end) {
            if (!start || !end) {
                badge.classList.add('hidden');
                clearBtn.classList.add('hidden');
                return;
            }
            const diffTime = Math.abs(end.getTime() - start.getTime());
            const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1; // inclusive
            const months = (diffDays / 30).toFixed(1).replace('.0', '');
            badge.textContent = `${diffDays} Hari (~${months} Bulan)`;
            badge.classList.remove('hidden');
            clearBtn.classList.remove('hidden');
        }

        const initialStart = startDateInput.value;
        const initialEnd = endDateInput.value;
        const defaultDates = (initialStart && initialEnd) ? [initialStart, initialEnd] : [];

        const fp = flatpickr("#date_range", {
            mode: "range",
            locale: "id",
            dateFormat: "d M Y",
            conjunction: "  s.d.  ",
            defaultDate: defaultDates,
            showMonths: window.innerWidth >= 640 ? 2 : 1,
            onChange: function (selectedDates) {
                if (selectedDates.length === 2) {
                    const pad = (n) => String(n).padStart(2, '0');
                    const startStr = `${selectedDates[0].getFullYear()}-${pad(selectedDates[0].getMonth() + 1)}-${pad(selectedDates[0].getDate())}`;
                    const endStr = `${selectedDates[1].getFullYear()}-${pad(selectedDates[1].getMonth() + 1)}-${pad(selectedDates[1].getDate())}`;
                    startDateInput.value = startStr;
                    endDateInput.value = endStr;
                    updateDuration(selectedDates[0], selectedDates[1]);
                } else {
                    startDateInput.value = '';
                    endDateInput.value = '';
                    updateDuration(null, null);
                }
            }
        });

        if (initialStart && initialEnd) {
            updateDuration(new Date(initialStart), new Date(initialEnd));
        }

        clearBtn.addEventListener('click', function (e) {
            e.preventDefault();
            fp.clear();
            startDateInput.value = '';
            endDateInput.value = '';
            updateDuration(null, null);
        });
    });
</script>
<?= $this->endSection() ?>
