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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" id="start_date" name="start_date" value="<?= old('start_date') ?>" required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" id="end_date" name="end_date" value="<?= old('end_date') ?>" required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm">
                    </div>
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
<?= $this->endSection() ?>
