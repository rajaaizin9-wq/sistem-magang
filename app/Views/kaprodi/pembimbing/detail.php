<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Detail Permohonan & Penentuan Pembimbing</h1>
            <p class="text-sm text-slate-500 mt-1">Pemantauan data pendaftaran mahasiswa dan penunjukan Dosen Pembimbing</p>
        </div>
        <a href="<?= base_url('kaprodi/penentuan-pembimbing') ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <!-- Student & Internship Info Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block">Pemohon Magang</span>
                <h2 class="text-lg font-bold text-slate-900"><?= esc($internship['student_name']) ?> (NIM: <?= esc($internship['nim']) ?>)</h2>
                <p class="text-xs text-slate-500">Program Studi: <?= esc($internship['prodi_name']) ?></p>
            </div>
            <div>
                <?= view('components/status_badge', ['status' => $internship['status']]) ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Nama Instansi Tujuan</span>
                <span class="font-bold text-slate-900"><?= esc($internship['company_name']) ?></span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Posisi Magang</span>
                <span class="font-semibold text-slate-800"><?= esc($internship['position']) ?></span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Jenis Magang</span>
                <span class="inline-flex items-center px-2.5 py-0.5 mt-0.5 rounded text-xs font-semibold <?= ($internship['internship_type'] ?? 'Offline') === 'Online' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800' ?>">
                    <?= esc($internship['internship_type'] ?? 'Offline') ?>
                </span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Tahun Akademik & Semester</span>
                <span class="font-semibold text-slate-800"><?= esc($internship['academic_year']) ?> (Semester <?= esc($internship['semester']) ?>)</span>
            </div>
            <div class="sm:col-span-2">
                <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Alamat Instansi</span>
                <span class="text-slate-700"><?= esc($internship['company_address']) ?></span>
            </div>
            <div class="sm:col-span-2">
                <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Tanggal Pelaksanaan</span>
                <span class="font-semibold text-slate-800"><?= format_indo($internship['start_date'], false, true) ?> s.d. <?= format_indo($internship['end_date'], false, true) ?></span>
            </div>
        </div>
    </div>

    <!-- Uploaded Documents Preview Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-2">Dokumen Administrasi Diunggah</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ($documents as $doc): ?>
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between">
                    <div>
                        <span class="font-bold text-xs text-slate-800 uppercase tracking-wider block mb-1">
                            <?= str_replace('_', ' ', strtoupper($doc['document_type'])) ?>
                        </span>
                        <div class="mt-1">
                            <?= view('components/status_badge', ['status' => $doc['status']]) ?>
                        </div>
                    </div>
                    <a href="<?= base_url('tu/verifikasi-dokumen/file/' . $doc['id']) ?>" target="_blank" class="px-3.5 py-1.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-md transition shadow-sm">
                        Lihat Berkas
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Assigned Supervisor Status Card -->
    <?php if (!empty($assignment)): ?>
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-3">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-2">Status Penugasan Pembimbing Saat Ini</h2>
            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                <div>
                    <span class="font-bold text-slate-900 block"><?= esc($assignment['lecturer_name']) ?></span>
                    <span class="text-xs text-slate-500">NIDN: <?= esc($assignment['nidn']) ?></span>
                </div>
                <div>
                    <?= view('components/status_badge', ['status' => $assignment['status']]) ?>
                </div>
            </div>
            <?php if ($assignment['status'] === 'REJECTED'): ?>
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800">
                    <span class="font-bold">Alasan Penolakan Dosen:</span> <?= esc($assignment['rejection_reason']) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Form Penunjukan Dosen Pembimbing (Khusus Jika Sudah Disetujui TU) -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-2">Penunjukan / Perubahan Dosen Pembimbing</h2>

        <?php if ($canAssign): ?>
            <form action="<?= base_url('kaprodi/penentuan-pembimbing/assign/' . $internship['id']) ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label for="lecturer_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Pilih Dosen Pembimbing <span class="text-red-500">*</span></label>
                    <select id="lecturer_id" name="lecturer_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm">
                        <option value="">-- Pilih Dosen Pembimbing --</option>
                        <?php foreach ($lecturers as $lec): ?>
                            <option value="<?= $lec['id'] ?>" <?= (!empty($assignment) && $assignment['lecturer_id'] == $lec['id']) ? 'selected' : '' ?>>
                                <?= esc($lec['name']) ?> (NIDN: <?= esc($lec['nidn']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menunjuk Dosen Pembimbing ini untuk mahasiswa tersebut?')"
                        class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Simpan & Tetapkan Pembimbing
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800 space-y-1">
                <span class="font-bold text-sm block">⚠️ Dosen Pembimbing Belum Dapat Ditentukan</span>
                <p>Permohonan magang ini masih dalam tahap pengajuan / verifikasi dokumen oleh Staf TU (Status: <span class="font-bold"><?= view('components/status_badge', ['status' => $internship['status']]) ?></span>). Penetapan Dosen Pembimbing baru dapat dilakukan setelah Staf TU memverifikasi dan menyetujui dokumen administrasi ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
