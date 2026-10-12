<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pemantauan Logbook & Penugasan Pembimbingan</h1>
        <p class="text-sm text-slate-500 mt-1">Konfirmasi penugasan pembimbing dari Kaprodi dan pantau logbook mahasiswa</p>
    </div>
</div>

<!-- Penugasan Table -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
    <div class="p-5 border-b border-slate-200 bg-slate-50">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Mahasiswa Bimbingan</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <th class="px-6 py-3.5">Mahasiswa</th>
                    <th class="px-6 py-3.5">Instansi & Posisi</th>
                    <th class="px-6 py-3.5">Periode</th>
                    <th class="px-6 py-3.5">Status Penugasan</th>
                    <th class="px-6 py-3.5 text-right">Aksi Konfirmasi / Pemantauan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (!empty($assignments)): ?>
                    <?php foreach ($assignments as $item): ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?= esc($item['student_name']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">NIM: <?= esc($item['nim']) ?></div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                <?= esc($item['company_name']) ?>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-xs font-normal text-slate-500"><?= esc($item['position']) ?></span>
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold <?= ($item['internship_type'] ?? 'Offline') === 'Online' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                        <?= esc($item['internship_type'] ?? 'Offline') ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs">
                                <?= format_indo($item['start_date'], false, true) ?> - <?= format_indo($item['end_date'], false, true) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= view('components/status_badge', ['status' => $item['assignment_status']]) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <?php if ($item['assignment_status'] === 'PENDING'): ?>
                                    <form action="<?= base_url('dosen-pembimbing/respon-penugasan/' . $item['assignment_id']) ?>" method="POST" class="inline-flex items-center space-x-2">
                                        <?= csrf_field() ?>
                                        <button type="submit" name="action" value="accept" onclick="return confirm('Apakah Anda bersedia menjadi Dosen Pembimbing mahasiswa ini?')"
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                            Terima Penugasan
                                        </button>
                                        <button type="button" onclick="showRejectModal(<?= $item['assignment_id'] ?>)"
                                            class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold rounded-md transition">
                                            Tolak
                                        </button>
                                    </form>
                                <?php elseif ($item['assignment_status'] === 'ACCEPTED'): ?>
                                    <a href="#" class="inline-flex items-center px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                        Pantau Logbook Mahasiswa
                                    </a>
                                <?php elseif ($item['assignment_status'] === 'REJECTED'): ?>
                                    <div class="text-xs text-red-600 font-medium">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200">
                                            Penugasan Ditolak
                                        </span>
                                        <?php if (!empty($item['rejection_reason'])): ?>
                                            <span class="block text-[11px] text-slate-400 mt-0.5 max-w-xs ml-auto truncate" title="<?= esc($item['rejection_reason']) ?>">
                                                Alasan: <?= esc($item['rejection_reason']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <p class="font-medium text-slate-600">Belum Ada Mahasiswa Bimbingan</p>
                            <p class="text-xs text-slate-400 mt-1">Penugasan dari Kaprodi akan muncul di halaman ini.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tolak Penugasan (Vanilla JS) -->
<div id="rejectModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl shadow-lg border border-slate-200 max-w-md w-full p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-900 border-b border-slate-200 pb-2">Alasan Penolakan Penugasan</h3>
        
        <form id="rejectForm" method="POST" action="" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="reject">
            
            <div>
                <label for="rejection_reason" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Jelaskan Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea id="rejection_reason" name="rejection_reason" rows="3" required placeholder="Contoh: Kuota pembimbingan semester ini sudah penuh..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition">
                    Kirim Penolakan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showRejectModal(assignmentId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = '<?= base_url('dosen-pembimbing/respon-penugasan/') ?>/' + assignmentId;
        modal.classList.remove('hidden');
    }

    function hideRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
<?= $this->endSection() ?>
