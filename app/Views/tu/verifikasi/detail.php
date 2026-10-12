<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Verifikasi Permohonan Magang (Staf TU)</h1>
            <p class="text-sm text-slate-500 mt-1">Periksa kelengkapan data dan dokumen sebelum menyetujui</p>
        </div>
        <a href="<?= base_url('tu/verifikasi-dokumen') ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
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
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-2">Pemeriksaan Berkas Administrasi</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ($documents as $doc): ?>
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg flex items-start justify-between">
                    <div>
                        <span class="font-bold text-xs text-slate-800 uppercase tracking-wider block mb-1">
                            <?= str_replace('_', ' ', strtoupper($doc['document_type'])) ?>
                        </span>
                        <div class="mt-1">
                            <?= view('components/status_badge', ['status' => $doc['status']]) ?>
                        </div>
                    </div>
                    <a href="<?= base_url('tu/verifikasi-dokumen/file/' . $doc['id']) ?>" target="_blank" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition shadow-sm">
                        Buka & Periksa File
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Action Form Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-2">Keputusan Verifikasi TU</h2>

        <form action="<?= base_url('tu/verifikasi-dokumen/proses/' . $internship['id']) ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Staf TU / Alasan Revisi</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Isi catatan perbaikan jika meminta revisi, atau instruksi tambahan jika disetujui..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="submit" name="action" value="revision" onclick="return confirm('Apakah Anda yakin ingin meminta revisi kepada mahasiswa ini?')"
                    class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    ⚠️ Minta Revisi Berkas
                </button>
                <button type="submit" name="action" value="approve" onclick="return confirm('Apakah Anda yakin ingin menyetujui permohonan magang ini? Status akan diteruskan ke Kaprodi.')"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    ✅ Setujui & Teruskan ke Kaprodi
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
