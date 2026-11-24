<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">Manajemen Dosen</span>
                <span class="text-xs text-slate-400">Program Studi: <?= esc($kaprodi['program_study_id'] == 1 ? 'Teknik Informatika' : 'Sistem Informasi') ?></span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight mt-1.5">Kelola Role Dosen</h1>
            <p class="text-sm text-slate-500 mt-0.5">Atur penugasan peran struktural dosen pengajar dan Wakil Dekan 1 (Wadek 1).</p>
        </div>
        <a href="<?= base_url('dashboard') ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900 self-start sm:self-auto">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <!-- Alert Edukasi / Petunjuk -->
    <div class="p-4 bg-indigo-50/60 border border-indigo-100 rounded-xl text-xs text-indigo-900 flex items-start space-x-3">
        <svg class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="space-y-1">
            <p class="font-bold">Informasi Penugasan Role Wadek 1:</p>
            <p class="text-slate-600 leading-relaxed">
                Wadek 1 adalah dosen yang memiliki penugasan struktural. Beliau <strong>tetap login menggunakan akun dosennya</strong> (bukan akun terpisah). Saat login, akun tersebut memiliki opsi untuk beralih peran (<em>Switch Role</em>) ke <strong>Wakil Dekan 1</strong> untuk pemantauan magang dan pengesahan konversi fakultas, di samping menjalankan peran tridharma sebagai <strong>Dosen Pembimbing</strong> dan <strong>Dosen Pengampu MK</strong>.
            </p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <form method="GET" action="<?= base_url('kaprodi/dosen') ?>" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
            <div class="relative flex-1 max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="q" value="<?= esc($search ?? '') ?>" placeholder="Cari nama dosen, NIDN, atau email..."
                       class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <?php if (!empty($allProdis) && count($allProdis) > 1): ?>
                <div class="sm:w-56">
                    <select name="prodi" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium">
                        <?php foreach ($allProdis as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($currentProdi == $p['id']) ? 'selected' : '' ?>>
                                Prodi: <?= esc($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="flex items-center space-x-2">
                <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Cari
                </button>
                <?php if (!empty($search)): ?>
                    <a href="<?= base_url('kaprodi/dosen' . (!empty($currentProdi) ? '?prodi=' . $currentProdi : '')) ?>" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <div class="text-xs text-slate-500 self-end sm:self-center">
            Total Dosen: <strong class="text-slate-800"><?= count($lecturers) ?></strong> orang
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Dosen & Kontak</th>
                        <th class="py-3.5 px-4">NIDN</th>
                        <th class="py-3.5 px-4">Program Studi</th>
                        <th class="py-3.5 px-4">Peran Saat Ini</th>
                        <th class="py-3.5 px-4 text-center">Atur Peran Struktural</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    <?php if (empty($lecturers)): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Tidak ada data dosen yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lecturers as $index => $lec): ?>
                            <?php $isWadek = !empty($lec['is_wadek']); ?>
                            <tr class="hover:bg-slate-50/60 transition <?= $isWadek ? 'bg-purple-50/25' : '' ?>">
                                <td class="py-4 px-4 text-center text-slate-400 font-semibold"><?= $index + 1 ?></td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                        <?= esc($lec['name']) ?>
                                        <?php if ($isWadek): ?>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                                WADEK 1 AKTIF
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5 flex items-center space-x-2">
                                        <span><?= esc($lec['email']) ?></span>
                                        <?php if (!empty($lec['phone'])): ?>
                                            <span>&bull;</span>
                                            <span><?= esc($lec['phone']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-600">
                                    <?= esc($lec['nidn'] ?: '-') ?>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        <?= esc($lec['prodi_name'] ?? 'Teknik Informatika') ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <?php if ($isWadek): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                            🏛️ Wakil Dekan 1
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            👨‍🏫 Dosen
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <form action="<?= base_url('kaprodi/dosen/update-role/' . $lec['lecturer_id']) ?>" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin mengubah peran dosen <?= esc($lec['name']) ?>?');"
                                          class="inline-flex items-center space-x-2">
                                        <?= csrf_field() ?>
                                        <select name="role" class="px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                                            <option value="dosen" <?= !$isWadek ? 'selected' : '' ?>>👨‍🏫 Dosen</option>
                                            <option value="wadek1" <?= $isWadek ? 'selected' : '' ?>>🏛️ Wadek 1</option>
                                        </select>
                                        <button type="submit" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

