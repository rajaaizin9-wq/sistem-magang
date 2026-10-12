<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Jurnal & Logbook Magang</h1>
        <p class="text-sm text-slate-500 mt-1">Dokumentasi berkala kegiatan magang yang dapat dipantau oleh dosen pembimbing</p>
    </div>
    <?php if ($selectedInternship): ?>
        <button type="button" onclick="openLogbookModal()" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Aktivitas Baru
        </button>
    <?php endif; ?>
</div>

<?php if (empty($internships)): ?>
    <!-- Empty State: Tidak Ada Magang Aktif -->
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center shadow-sm">
        <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h3 class="text-base font-bold text-slate-800">Belum Ada Program Magang yang Sedang Berjalan (Aktif)</h3>
        <p class="text-sm text-slate-500 max-w-md mx-auto mt-1.5">
            Logbook hanya dapat diisi jika Anda memiliki program magang yang telah disetujui, pembimbing telah menerima penugasan, dan berstatus <strong>Magang Aktif</strong>.
        </p>
        <div class="mt-6">
            <a href="<?= base_url('mahasiswa/pendaftaran') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                Cek Pendaftaran Magang
            </a>
        </div>
    </div>
<?php else: ?>

    <!-- Context & Switcher Periode Magang -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Card 1: Instansi & Switcher -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm md:col-span-2">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Periode Magang Aktif</span>
                <?php if (count($internships) > 1): ?>
                    <form method="GET" action="<?= base_url('mahasiswa/logbook') ?>" class="inline-block">
                        <select name="internship_id" onchange="this.form.submit()" class="text-xs border border-slate-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <?php foreach ($internships as $opt): ?>
                                <option value="<?= $opt['id'] ?>" <?= ((int)$opt['id'] === (int)$selectedInternship['id']) ? 'selected' : '' ?>>
                                    <?= esc($opt['company_name']) ?> (<?= esc($opt['academic_year']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                <?php endif; ?>
            </div>
            <div class="text-base font-bold text-slate-900"><?= esc($selectedInternship['company_name']) ?></div>
            <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-2">
                <span><?= esc($selectedInternship['position']) ?></span>
                <span>&bull;</span>
                <span><?= format_indo($selectedInternship['start_date'], false, true) ?> - <?= format_indo($selectedInternship['end_date'], false, true) ?></span>
                <span>&bull;</span>
                <?= view('components/status_badge', ['status' => $selectedInternship['status']]) ?>
            </div>
        </div>

        <!-- Card 2: Dosen Pemantau -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Dosen Pemantau</span>
            <?php if ($supervisor): ?>
                <div class="font-bold text-slate-900 text-sm truncate" title="<?= esc($supervisor['supervisor_name']) ?>">
                    <?= esc($supervisor['supervisor_name']) ?>
                </div>
                <div class="text-xs text-slate-500 mt-0.5">NIDN: <?= esc($supervisor['nidn'] ?? '-') ?></div>
            <?php else: ?>
                <div class="text-sm font-medium text-slate-500 italic">Menunggu Penugasan Kaprodi</div>
            <?php endif; ?>
        </div>

        <!-- Card 3: Rekap Jurnal -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Catatan</span>
            <div class="text-2xl font-bold text-slate-900"><?= count($logbooks) ?></div>
            <div class="text-xs text-slate-500 mt-0.5">Jurnal aktivitas tercatat</div>
        </div>
    </div>

    <!-- Timeline Daftar Logbook -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Riwayat Jurnal Aktivitas</h2>
            <span class="text-xs text-slate-500">Terurut dari yang paling terbaru</span>
        </div>

        <?php if (empty($logbooks)): ?>
            <div class="bg-white rounded-xl border border-dashed border-slate-300 p-10 text-center">
                <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <p class="text-sm font-semibold text-slate-700">Belum ada jurnal aktivitas yang dicatat</p>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Klik tombol "Catat Aktivitas Baru" di atas untuk menambahkan catatan kegiatan magang Anda.</p>
            </div>
        <?php else: ?>
            <?php foreach ($logbooks as $log): ?>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 hover:border-slate-300 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-md text-xs font-bold">
                                Minggu ke-<?= esc($log['week_number']) ?>
                            </span>
                            <span class="text-sm font-semibold text-slate-800">
                                <?= format_indo($log['date'], false, false) ?>
                            </span>
                        </div>
                        <div class="flex items-center space-x-3 text-xs">
                            <?php if (!empty($log['verified_at'])): ?>
                                <span class="inline-flex items-center text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[11px] font-medium border border-emerald-200">
                                    <svg class="w-3.5 h-3.5 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Sudah Dilihat Dosen
                                </span>
                            <?php endif; ?>
                            
                            <form action="<?= base_url('mahasiswa/logbook/hapus/' . $log['id']) ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan aktivitas ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Hapus catatan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Uraian Aktivitas -->
                    <div class="mt-3 text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                        <?= esc($log['activity_description']) ?>
                    </div>

                    <!-- Hasil & Kendala (Jika Ada) -->
                    <?php if (!empty($log['result']) || !empty($log['obstacle'])): ?>
                        <div class="mt-3 pt-3 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                            <?php if (!empty($log['result'])): ?>
                                <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/60">
                                    <div class="font-bold text-slate-700 flex items-center mb-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Hasil yang Dicapai:
                                    </div>
                                    <p class="text-slate-600 leading-normal"><?= esc($log['result']) ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($log['obstacle'])): ?>
                                <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/60">
                                    <div class="font-bold text-slate-700 flex items-center mb-1">
                                        <svg class="w-3.5 h-3.5 text-amber-600 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Kendala / Solusi:
                                    </div>
                                    <p class="text-slate-600 leading-normal"><?= esc($log['obstacle']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Lampiran File & Catatan Pemantau -->
                    <div class="mt-3 pt-2 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <?php if (!empty($log['attachment'])): ?>
                                <a href="<?= base_url('mahasiswa/logbook/file/' . $log['id']) ?>" target="_blank" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Lihat Lampiran / Foto Bukti
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($log['lecturer_notes'])): ?>
                            <div class="w-full bg-blue-50/70 border border-blue-200 rounded-lg p-3 text-xs text-blue-900 mt-2">
                                <span class="font-bold block text-blue-800 mb-0.5">Catatan Dosen Pemantau:</span>
                                <?= esc($log['lecturer_notes']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal Form Tambah Aktivitas -->
    <div id="logbookModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden transition-all">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-slate-800 text-base">Catat Jurnal Aktivitas Magang</h3>
                <button type="button" onclick="closeLogbookModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="<?= base_url('mahasiswa/logbook/simpan') ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="internship_id" value="<?= esc($selectedInternship['id']) ?>">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Aktivitas <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="<?= date('Y-m-d') ?>" required class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Minggu ke-</label>
                        <input type="number" name="week_number" min="1" max="52" value="<?= $suggestedWeek ?>" class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Uraian Aktivitas / Kegiatan <span class="text-red-500">*</span></label>
                    <textarea name="activity_description" rows="3" required placeholder="Tuliskan kegiatan pekerjaan yang Anda laksanakan hari/minggu ini..." class="w-full text-sm border border-slate-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Hasil / Output <span class="text-slate-400 font-normal">(opsional)</span></label>
                        <textarea name="result" rows="2" placeholder="Contoh: Modul login selesai" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kendala / Solusi <span class="text-slate-400 font-normal">(opsional)</span></label>
                        <textarea name="obstacle" rows="2" placeholder="Contoh: Terkendala koneksi API" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Lampiran Foto / Bukti Dokumen <span class="text-slate-400 font-normal">(opsional, maks 3MB)</span></label>
                    <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeLogbookModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openLogbookModal() {
            document.getElementById('logbookModal').classList.remove('hidden');
        }
        function closeLogbookModal() {
            document.getElementById('logbookModal').classList.add('hidden');
        }
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogbookModal();
            }
        });
    </script>
<?php endif; ?>

<?= $this->endSection() ?>
