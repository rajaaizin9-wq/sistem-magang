<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Kaprodi</h1>
    <p class="text-sm text-slate-500 mt-1">Penentuan dosen pembimbing dan pemantauan konversi MK magang</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
    <a href="<?= base_url('kaprodi/penentuan-pembimbing') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-blue-300 transition block">
        <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Menunggu Penentuan Pembimbing</div>
        <div class="text-3xl font-bold text-slate-900 mt-2"><?= esc($pendingAssignments) ?></div>
        <p class="text-xs text-slate-500 mt-1">Pengajuan magang disetujui TU &rarr;</p>
    </a>
    <a href="<?= base_url('kaprodi/konversi') ?>" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-emerald-300 transition block">
        <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Pemantauan Konversi MK</div>
        <div class="text-3xl font-bold text-slate-900 mt-2"><?= $conversionCount ?? 0 ?></div>
        <p class="text-xs text-slate-500 mt-1">Mahasiswa prodi mengajukan konversi &rarr;</p>
    </a>
</div>
<?= $this->endSection() ?>
