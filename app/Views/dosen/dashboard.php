<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Welcome Header & Role Switcher Banner -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center space-x-2">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Portal Dosen</span>
            <span class="text-xs text-slate-400">NIDN: <?= esc($lecturer['nidn'] ?? '-') ?></span>
        </div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight mt-1.5">Selamat Datang, <?= esc($userName) ?></h1>
        <p class="text-sm text-slate-500 mt-0.5">
            <?php if ($activeRole === 'dosen_pembimbing'): ?>
                Mode Aktif: <strong class="text-blue-700">Dosen Pembimbing</strong> &bull; Memantau bimbingan magang, logbook mingguan, dan rekomendasi konversi.
            <?php else: ?>
                Mode Aktif: <strong class="text-blue-700">Dosen Pengampu MK</strong> &bull; Memverifikasi kesesuaian CPMK konversi mata kuliah pilihan dan seminar magang.
            <?php endif; ?>
        </p>
    </div>

    <!-- Mode Switcher Tabs -->
    <div class="flex items-center bg-slate-100 p-1.5 rounded-xl border border-slate-200 self-start md:self-auto">
        <a href="<?= base_url('switch-role/dosen_pembimbing') ?>"
           class="flex items-center space-x-2 px-4 py-2 rounded-lg text-xs font-bold transition <?= $activeRole === 'dosen_pembimbing' ? 'bg-white text-blue-700 shadow-sm border border-slate-200/80 ring-1 ring-blue-500/20' : 'text-slate-600 hover:text-slate-900' ?>">
            <span>👨‍🏫 Dosen Pembimbing</span>
            <?php if ($pendingAssignmentsCount > 0): ?>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-500 text-white font-extrabold"><?= $pendingAssignmentsCount ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= base_url('switch-role/dosen_mk') ?>"
           class="flex items-center space-x-2 px-4 py-2 rounded-lg text-xs font-bold transition <?= $activeRole === 'dosen_mk' ? 'bg-white text-blue-700 shadow-sm border border-slate-200/80 ring-1 ring-blue-500/20' : 'text-slate-600 hover:text-slate-900' ?>">
            <span>📚 Dosen Pengampu MK</span>
            <?php if ($pendingCourseConversionCount > 0): ?>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-purple-600 text-white font-extrabold"><?= $pendingCourseConversionCount ?></span>
            <?php endif; ?>
        </a>
    </div>
</div>

<?php if ($activeRole === 'dosen_pembimbing'): ?>
    <!-- ========================================== -->
    <!-- TAMPILAN MODE: DOSEN PEMBIMBING           -->
    <!-- ========================================== -->

    <!-- Summary Metric Cards Pembimbing -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Tawaran Bimbingan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tawaran Bimbingan</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                    ⚠️
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900"><?= $pendingAssignmentsCount ?></div>
                <p class="text-xs text-slate-500 mt-1">Perlu respon persetujuan</p>
            </div>
        </div>

        <!-- Mahasiswa Bimbingan Aktif -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bimbingan Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    🎓
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900"><?= $activeStudentsCount ?></div>
                <p class="text-xs text-slate-500 mt-1">Sedang pelaksanaan magang</p>
            </div>
        </div>

        <!-- Verifikasi Konversi -->
        <a href="<?= base_url('dosen-pembimbing/konversi') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-blue-300 transition block group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 group-hover:text-blue-600 uppercase tracking-wider transition">Verifikasi Konversi</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                    📝
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900"><?= $pendingSupervisorConversionCount ?></div>
                <p class="text-xs text-blue-600 font-medium mt-1">Menunggu persetujuan pembimbing &rarr;</p>
            </div>
        </a>

        <!-- Shortcut Pemantauan Logbook -->
        <a href="<?= base_url('dosen-pembimbing/pemantauan-logbook') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-emerald-300 transition block group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 group-hover:text-emerald-600 uppercase tracking-wider transition">Jurnal & Logbook</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    📋
                </div>
            </div>
            <div class="mt-3">
                <div class="text-base font-bold text-slate-800">Pantau Aktivitas</div>
                <p class="text-xs text-emerald-600 font-medium mt-1">Buka pemantauan logbook &rarr;</p>
            </div>
        </a>
    </div>

    <!-- Table: Daftar Mahasiswa Bimbingan -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-slate-900">Daftar Penugasan Mahasiswa Bimbingan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Penugasan pembimbing magang yang ditentukan oleh Ketua Program Studi.</p>
            </div>
            <a href="<?= base_url('dosen-pembimbing/pemantauan-logbook') ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                Kelola Semua di Pemantauan Logbook &rarr;
            </a>
        </div>

        <?php if (!empty($assignments)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <th class="px-6 py-3.5">Mahasiswa</th>
                            <th class="px-6 py-3.5">Instansi & Posisi</th>
                            <th class="px-6 py-3.5">Periode Magang</th>
                            <th class="px-6 py-3.5">Status Penugasan</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($assignments as $item): ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900"><?= esc($item['student_name']) ?></div>
                                    <div class="text-xs text-slate-500 mt-0.5">NIM: <?= esc($item['nim']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800"><?= esc($item['company_name']) ?></div>
                                    <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                                        <span><?= esc($item['position']) ?></span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium <?= ($item['internship_type'] ?? 'Offline') === 'Online' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                            <?= esc($item['internship_type'] ?? 'Offline') ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    <?= format_indo($item['start_date'], false, true) ?> - <?= format_indo($item['end_date'], false, true) ?>
                                    <div class="text-slate-400 mt-0.5"><?= view('components/status_badge', ['status' => $item['internship_status']]) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <?= view('components/status_badge', ['status' => $item['assignment_status']]) ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <?php if ($item['assignment_status'] === 'PENDING'): ?>
                                            <a href="<?= base_url('dosen-pembimbing/pemantauan-logbook') ?>" class="inline-flex items-center px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold rounded-md transition">
                                                Respon Tawaran
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('dosen-pembimbing/pemantauan-logbook/detail/' . $item['assignment_id']) ?>" class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold rounded-md transition">
                                                Pantau Logbook
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="p-10 text-center text-slate-400 bg-slate-50/50">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2">
                    👨‍🏫
                </div>
                <h3 class="text-sm font-bold text-slate-700">Belum Ada Penugasan Pembimbing</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Saat ini belum ada mahasiswa magang yang ditugaskan kepada Anda oleh Kaprodi.</p>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>
    <!-- ========================================== -->
    <!-- TAMPILAN MODE: DOSEN PENGAMPU MK          -->
    <!-- ========================================== -->

    <!-- Summary Metric Cards Pengampu MK -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- MK Pilihan Diampu -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">MK Pilihan Diampu</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                    📚
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900"><?= count($courses) ?></div>
                <p class="text-xs text-slate-500 mt-1">Mata kuliah semester ini</p>
            </div>
        </div>

        <!-- Konversi CPMK Masuk -->
        <a href="<?= base_url('dosen-mk/konversi') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-purple-300 transition block group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 group-hover:text-purple-600 uppercase tracking-wider transition">Konversi Masuk</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
                    📑
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900"><?= $pendingCourseConversionCount ?></div>
                <p class="text-xs text-purple-600 font-medium mt-1">Menunggu review CPMK &rarr;</p>
            </div>
        </a>

        <!-- Calon Seminar Magang -->
        <a href="<?= base_url('dosen-mk/seminar') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-indigo-300 transition block group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 group-hover:text-indigo-600 uppercase tracking-wider transition">Seminar Magang</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    🎓
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900"><?= $candidateSeminarsCount ?></div>
                <p class="text-xs text-indigo-600 font-medium mt-1">Mahasiswa terdaftar &rarr;</p>
            </div>
        </a>

        <!-- Aksi Cepat Verifikasi -->
        <a href="<?= base_url('dosen-mk/konversi') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-blue-300 transition block group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 group-hover:text-blue-600 uppercase tracking-wider transition">Evaluasi CPMK</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                    ✓
                </div>
            </div>
            <div class="mt-3">
                <div class="text-base font-bold text-slate-800">Verifikasi MK</div>
                <p class="text-xs text-blue-600 font-medium mt-1">Buka daftar pengajuan konversi &rarr;</p>
            </div>
        </a>
    </div>

    <!-- Table: Mata Kuliah Pilihan yang Diampu -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-slate-900">Mata Kuliah Pilihan yang Anda Ampu</h2>
                <p class="text-xs text-slate-500 mt-0.5">Mata kuliah yang dapat dipilih mahasiswa untuk dikonversikan dengan kegiatan magang.</p>
            </div>
            <a href="<?= base_url('dosen-mk/konversi') ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                Lihat Pengajuan Konversi &rarr;
            </a>
        </div>

        <?php if (!empty($courses)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <th class="px-6 py-3.5">Kode MK</th>
                            <th class="px-6 py-3.5">Nama Mata Kuliah</th>
                            <th class="px-6 py-3.5">Bobot SKS</th>
                            <th class="px-6 py-3.5">Jenis</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($courses as $course): ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-mono font-semibold text-slate-700">
                                    <?= esc($course['code']) ?>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    <?= esc($course['name']) ?>
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-600">
                                    <?= esc($course['credits']) ?> SKS
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        MK Pilihan
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="<?= base_url('dosen-mk/konversi') ?>" class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold rounded-md transition">
                                            Verifikasi Konversi
                                        </a>
                                        <a href="<?= base_url('dosen-mk/seminar') ?>" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-md transition">
                                            Seminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="p-10 text-center text-slate-400 bg-slate-50/50">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2">
                    📚
                </div>
                <h3 class="text-sm font-bold text-slate-700">Belum Ada Mata Kuliah Diampu</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tidak ada mata kuliah pilihan yang terdaftar di bawah pengampuan Anda saat ini.</p>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
