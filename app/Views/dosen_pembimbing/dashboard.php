<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Dosen Pembimbing</h1>
    <p class="text-sm text-slate-500 mt-1">Pemantauan & verifikasi logbook mingguan mahasiswa bimbingan</p>
</div>

<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <h2 class="text-base font-bold text-slate-800 mb-2">Pemantauan Logbook Mahasiswa</h2>
    <p class="text-sm text-slate-500">Anda belum memiliki mahasiswa bimbingan magang yang aktif untuk dipantau logbook-nya saat ini.</p>
</div>
<?= $this->endSection() ?>
