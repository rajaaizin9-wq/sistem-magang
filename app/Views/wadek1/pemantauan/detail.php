<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header & Navigasi -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 text-sm text-slate-500">
            <a href="<?= base_url('wadek1/pemantauan-magang') ?>" class="hover:text-blue-600 font-medium">Pemantauan Magang</a>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 font-semibold truncate"><?= esc($internship['student_name']) ?> (<?= esc($internship['nim']) ?>)</span>
        </div>
        <a href="<?= base_url('wadek1/pemantauan-magang') ?>" class="inline-flex items-center px-3.5 py-1.5 rounded-lg text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm transition">
            <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <!-- Informasi Utama -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Data Mahasiswa & Magang -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Mahasiswa -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Data Mahasiswa</h3>
                    <div>
                        <?= view('components/status_badge', ['status' => $internship['status']]) ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 text-sm">
                    <div>
                        <span class="block text-xs text-slate-400">Nama Mahasiswa</span>
                        <span class="font-semibold text-slate-800"><?= esc($internship['student_name']) ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">NIM</span>
                        <span class="font-semibold text-slate-800"><?= esc($internship['nim']) ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">Program Studi</span>
                        <span class="text-slate-700"><?= esc($internship['prodi_name']) ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">Email</span>
                        <span class="text-slate-700"><?= esc($internship['student_email'] ?? '-') ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">Tahun Akademik / Semester</span>
                        <span class="text-slate-700"><?= esc($internship['academic_year']) ?> (Semester <?= esc($internship['semester']) ?>)</span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">Tanggal Pengajuan</span>
                        <span class="text-slate-700"><?= date('d M Y - H:i', strtotime($internship['created_at'])) ?> WIB</span>
                    </div>
                </div>
            </div>

            <!-- Tempat Magang -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Tempat & Waktu Magang</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 text-sm">
                    <div class="sm:col-span-2">
                        <span class="block text-xs text-slate-400">Instansi / Perusahaan</span>
                        <span class="font-semibold text-slate-900"><?= esc($internship['company_name']) ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">Posisi</span>
                        <span class="font-medium text-slate-800"><?= esc($internship['position']) ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">Jenis Magang</span>
                        <div class="mt-1">
                            <?php if (($internship['internship_type'] ?? 'OFFLINE') === 'ONLINE'): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-sky-50 text-sky-700 border border-sky-200">
                                    Online (Daring)
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Offline (Onsite)
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="block text-xs text-slate-400">Alamat Instansi</span>
                        <span class="text-slate-700"><?= esc($internship['company_address'] ?: '-') ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">Tanggal Mulai</span>
                        <span class="font-medium text-slate-800"><?= format_indo($internship['start_date']) ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">Tanggal Selesai</span>
                        <span class="font-medium text-slate-800"><?= format_indo($internship['end_date']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Berkas -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Dokumen Berkas</h3>
                </div>

                <div class="mt-4 space-y-3">
                    <?php if (empty($documents)): ?>
                        <p class="text-sm text-slate-400">Tidak ada berkas.</p>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-slate-800"><?= esc(str_replace('_', ' ', $doc['document_type'])) ?></div>
                                    <div class="text-xs text-slate-500"><?= esc($doc['original_filename'] ?? 'Dokumen') ?></div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <?= view('components/status_badge', ['status' => $doc['status']]) ?>
                                    <a href="<?= base_url('tu/verifikasi-dokumen/file/' . $doc['id']) ?>" target="_blank" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="Lihat Berkas">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Dosen Pembimbing -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Dosen Pembimbing</h3>
                </div>

                <div class="mt-4">
                    <?php if ($assignment): ?>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="block text-xs text-slate-400">Nama Dosen</span>
                                <span class="font-semibold text-slate-900"><?= esc($assignment['lecturer_name']) ?></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">NIDN</span>
                                <span class="text-slate-800"><?= esc($assignment['nidn']) ?></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Email</span>
                                <span class="text-slate-700"><?= esc($assignment['lecturer_email'] ?? '-') ?></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">No. HP</span>
                                <span class="text-slate-700"><?= esc($assignment['phone'] ?? '-') ?></span>
                            </div>
                            <div class="pt-2 border-t border-slate-100">
                                <span class="block text-xs text-slate-400 mb-1">Status Penugasan:</span>
                                <?php if ($assignment['status'] === 'ACCEPTED'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Diterima Dosen
                                    </span>
                                <?php elseif ($assignment['status'] === 'REJECTED'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                        Ditolak Dosen
                                    </span>
                                    <?php if (!empty($assignment['rejection_reason'])): ?>
                                        <p class="text-xs text-red-600 mt-1 italic">
                                            Alasan: "<?= esc($assignment['rejection_reason']) ?>"
                                        </p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu Konfirmasi
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-xs text-slate-400 italic">Belum ditentukan oleh Kaprodi.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
