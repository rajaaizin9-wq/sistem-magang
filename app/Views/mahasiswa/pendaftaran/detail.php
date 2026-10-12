<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Detail Pengajuan Magang</h1>
            <p class="text-sm text-slate-500 mt-1">Status dan kelengkapan administrasi permohonan magang</p>
        </div>
        <a href="<?= base_url('mahasiswa/pendaftaran') ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <!-- Status Banner -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Status Pendaftaran</span>
            <div class="flex items-center space-x-3">
                <?= view('components/status_badge', ['status' => $internship['status']]) ?>
            </div>
        </div>

        <?php if ($internship['status'] === 'REVISION_REQUIRED'): ?>
            <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800 font-medium">
                ⚠️ <span class="font-bold">Perlu Revisi Berkas:</span> Harap perhatikan catatan dari Staf TU di bawah.
            </div>
        <?php endif; ?>
    </div>

    <!-- Detail Info Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-2">Informasi Instansi Magang</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Nama Instansi / Perusahaan</span>
                <span class="font-bold text-slate-900 text-base"><?= esc($internship['company_name']) ?></span>
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

    <!-- Informasi Dosen Pembimbing (Tampil jika sudah diterima oleh Dosen) -->
    <?php if (!empty($assignment) && $assignment['status'] === 'ACCEPTED'): ?>
        <div class="bg-white p-6 rounded-xl border border-emerald-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-emerald-100 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Dosen Pembimbing Magang Anda</h2>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Aktif Membimbing
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm bg-emerald-50/50 p-4 rounded-lg border border-emerald-100">
                <div>
                    <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Nama Dosen Pembimbing</span>
                    <span class="font-bold text-slate-900 text-base"><?= esc($assignment['lecturer_name']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">NIDN</span>
                    <span class="font-semibold text-slate-800"><?= esc($assignment['nidn']) ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">Email Kontak</span>
                    <span class="text-slate-700 font-medium"><?= esc($assignment['lecturer_email'] ?? '-') ?></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-medium uppercase tracking-wider">No. Telepon / WhatsApp</span>
                    <span class="text-slate-700 font-medium"><?= esc($assignment['phone'] ?? '-') ?></span>
                </div>
            </div>
        </div>
    <?php elseif (!empty($assignment) && $assignment['status'] === 'PENDING'): ?>
        <div class="bg-white p-5 rounded-xl border border-amber-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider block">Penentuan Dosen Pembimbing</span>
                <p class="text-xs text-slate-600 mt-1">Kaprodi telah menunjuk Dosen Pembimbing. Saat ini sedang menunggu konfirmasi kesediaan dari Dosen terkait.</p>
            </div>
            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded-md border border-amber-200">
                Menunggu Respon Dosen
            </span>
        </div>
    <?php endif; ?>

    <!-- Uploaded Documents Card -->
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

    <!-- Catatan Verifikasi TU (Ditampilkan HANYA SATU KALI) -->
    <?php 
    $tuNote = '';
    $verifiedAt = '';
    foreach ($documents as $doc) {
        if (!empty($doc['notes'])) {
            $tuNote = $doc['notes'];
            $verifiedAt = $doc['verified_at'];
            break; // Cukup ambil 1 kali
        }
    }
    ?>

    <?php if (!empty($tuNote)): ?>
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-2">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-2">Catatan Hasil Verifikasi TU</h2>
            <div class="p-4 bg-amber-50/80 border border-amber-200 rounded-lg text-xs text-slate-700 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-amber-900 text-sm">Catatan Staf TU:</span>
                    <?php if (!empty($verifiedAt)): ?>
                        <span class="text-slate-400 text-[11px]"><?= date('d M Y H:i', strtotime($verifiedAt)) ?> WIB</span>
                    <?php endif; ?>
                </div>
                <p class="text-slate-800 text-xs leading-relaxed pt-1"><?= esc($tuNote) ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
