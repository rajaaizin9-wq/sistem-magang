<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header Banner -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Mahasiswa</h1>
        <p class="text-sm text-slate-500 mt-1">Selamat datang kembali, <span class="font-semibold text-slate-800"><?= esc($userName) ?></span></p>
    </div>
    <a href="<?= base_url('mahasiswa/pendaftaran/baru') ?>" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
        + Daftar Magang Baru
    </a>
</div>

<!-- Informasi Dosen Pembimbing Aktif (Jika Dosen Sudah Menerima Penugasan) -->
<?php if (!empty($activeAssignment)): ?>
    <div class="mb-6 bg-white p-6 rounded-xl border border-emerald-200 shadow-sm">
        <div class="flex items-center justify-between border-b border-emerald-100 pb-3 mb-4">
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Dosen Pembimbing Magang Anda</h2>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                Aktif Membimbing
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs bg-emerald-50/50 p-4 rounded-lg border border-emerald-100">
            <div>
                <span class="text-slate-400 block font-medium uppercase tracking-wider">Nama Dosen</span>
                <span class="font-bold text-slate-900 text-sm mt-0.5 block"><?= esc($activeAssignment['lecturer_name']) ?></span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium uppercase tracking-wider">NIDN</span>
                <span class="font-semibold text-slate-800 text-sm mt-0.5 block"><?= esc($activeAssignment['nidn']) ?></span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium uppercase tracking-wider">Email Kontak</span>
                <span class="text-slate-700 font-medium text-sm mt-0.5 block"><?= esc($activeAssignment['lecturer_email'] ?? '-') ?></span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium uppercase tracking-wider">No. Telepon / WA</span>
                <span class="text-slate-700 font-medium text-sm mt-0.5 block"><?= esc($activeAssignment['phone'] ?? '-') ?></span>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Riwayat Magang</div>
        <div class="text-2xl font-bold text-slate-800 mt-2"><?= count($internships) ?> Periode</div>
        <p class="text-xs text-slate-500 mt-1">Mendukung multi-periode magang</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Logbook Mingguan</div>
        <div class="text-2xl font-bold text-slate-800 mt-2">0 Entri</div>
        <p class="text-xs text-slate-500 mt-1">Pengisian logbook berkala</p>
    </div>
    <a href="<?= base_url('mahasiswa/konversi') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-blue-300 transition block">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Konversi MK Pilihan</div>
        <div class="text-2xl font-bold text-slate-800 mt-2"><?= $conversionCount ?? 0 ?> Pengajuan</div>
        <p class="text-xs text-blue-600 font-semibold mt-1">Kelola permohonan konversi &rarr;</p>
    </a>
</div>
<?= $this->endSection() ?>
