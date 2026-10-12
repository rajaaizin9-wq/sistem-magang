<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb & Back -->
<div class="mb-4 flex items-center space-x-2 text-xs text-slate-500">
    <a href="<?= base_url('dosen-pembimbing/pemantauan-logbook') ?>" class="hover:text-blue-600 transition flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Penugasan
    </a>
    <span>/</span>
    <span class="text-slate-700 font-medium">Pemantauan Jurnal Magang</span>
</div>

<!-- Header Card: Info Mahasiswa & Magang -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Magang Aktif</span>
                <span class="text-xs text-slate-400">NIM: <?= esc($assignment['nim']) ?></span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight mt-1.5"><?= esc($assignment['student_name']) ?></h1>
            <p class="text-xs text-slate-500 mt-0.5"><?= esc($assignment['prodi_name']) ?> &bull; <?= esc($assignment['student_phone'] ?? '-') ?></p>
        </div>
        <div class="text-left md:text-right">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Aktivitas</span>
            <div class="text-2xl font-bold text-slate-900"><?= count($logbooks) ?> <span class="text-xs font-normal text-slate-500">Jurnal</span></div>
        </div>
    </div>

    <!-- Rincian Instansi -->
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
        <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/60">
            <span class="text-slate-400 uppercase tracking-wider text-[10px] font-bold block mb-0.5">Instansi & Posisi</span>
            <div class="font-bold text-slate-800"><?= esc($assignment['company_name']) ?></div>
            <div class="text-slate-500 mt-0.5"><?= esc($assignment['position']) ?> (<?= esc($assignment['internship_type'] ?? 'Offline') ?>)</div>
        </div>
        <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/60">
            <span class="text-slate-400 uppercase tracking-wider text-[10px] font-bold block mb-0.5">Periode Magang</span>
            <div class="font-bold text-slate-800"><?= format_indo($assignment['start_date'], false, true) ?> - <?= format_indo($assignment['end_date'], false, true) ?></div>
            <div class="text-slate-500 mt-0.5">TA <?= esc($assignment['academic_year']) ?> (<?= esc($assignment['semester']) ?>)</div>
        </div>
        <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/60">
            <span class="text-slate-400 uppercase tracking-wider text-[10px] font-bold block mb-0.5">Lokasi Perusahaan</span>
            <div class="font-medium text-slate-700 truncate" title="<?= esc($assignment['company_address']) ?>">
                <?= esc($assignment['company_address']) ?>
            </div>
        </div>
    </div>
</div>

<!-- Timeline Logbook Section -->
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Jurnal Aktivitas Mahasiswa</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pantau pelaksanaan kegiatan lapangan dan berikan catatan bila diperlukan</p>
        </div>
    </div>

    <?php if (empty($logbooks)): ?>
        <div class="bg-white rounded-xl border border-dashed border-slate-300 p-10 text-center shadow-sm">
            <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <p class="text-sm font-semibold text-slate-700">Mahasiswa Belum Mengisi Catatan Aktivitas</p>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Riwayat logbook akan tampil secara otomatis di sini saat mahasiswa menginput kegiatannya.</p>
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
                    <div>
                        <button type="button" onclick="openNoteModal(<?= $log['id'] ?>, '<?= esc(addslashes($log['lecturer_notes'] ?? '')) ?>')"
                            class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-md transition">
                            <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <?= empty($log['lecturer_notes']) ? 'Beri Catatan Pemantau' : 'Ubah Catatan' ?>
                        </button>
                    </div>
                </div>

                <!-- Deskripsi Kegiatan -->
                <div class="mt-3 text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                    <?= esc($log['activity_description']) ?>
                </div>

                <!-- Hasil & Kendala -->
                <?php if (!empty($log['result']) || !empty($log['obstacle'])): ?>
                    <div class="mt-3 pt-3 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <?php if (!empty($log['result'])): ?>
                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/60">
                                <div class="font-bold text-slate-700 flex items-center mb-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Hasil Capaian:
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

                <!-- Lampiran & Catatan Dosen -->
                <div class="mt-3 pt-2">
                    <?php if (!empty($log['attachment'])): ?>
                        <div class="mb-2">
                            <a href="<?= base_url('dosen-pembimbing/pemantauan-logbook/file/' . $log['id']) ?>" target="_blank" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                Buka Lampiran / Bukti Kegiatan
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($log['lecturer_notes'])): ?>
                        <div class="bg-blue-50/70 border border-blue-200 rounded-lg p-3 text-xs text-blue-900">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-blue-800">Catatan Pemantauan Anda:</span>
                                <?php if (!empty($log['verified_at'])): ?>
                                    <span class="text-[10px] text-blue-500"><?= format_indo($log['verified_at'], true, true) ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="leading-relaxed"><?= esc($log['lecturer_notes']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Beri Catatan Pemantauan (Vanilla JS) -->
<div id="noteModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="font-bold text-slate-800 text-sm">Catatan Pemantau Aktivitas</h3>
            <button type="button" onclick="closeNoteModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="noteForm" action="" method="POST" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="lecturer_notes" class="block text-xs font-semibold text-slate-700 mb-1">
                    Ulasan / Masukan Pemantau
                </label>
                <textarea id="lecturer_notes" name="lecturer_notes" rows="4" required placeholder="Tuliskan masukan singkat atau apresiasi untuk kegiatan mahasiswa..."
                    class="w-full text-sm border border-slate-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                <p class="text-[11px] text-slate-500 mt-1">Catatan ini dapat dibaca langsung oleh mahasiswa pada akun mereka.</p>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeNoteModal()" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Simpan Catatan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openNoteModal(logbookId, currentNote) {
        const form = document.getElementById('noteForm');
        form.action = '<?= base_url('dosen-pembimbing/pemantauan-logbook/catatan/') ?>/' + logbookId;
        document.getElementById('lecturer_notes').value = currentNote || '';
        document.getElementById('noteModal').classList.remove('hidden');
    }

    function closeNoteModal() {
        document.getElementById('noteModal').classList.add('hidden');
    }

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNoteModal();
        }
    });
</script>

<?= $this->endSection() ?>
