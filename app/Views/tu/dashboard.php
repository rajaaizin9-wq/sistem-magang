<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Staf TU / Administrasi</h1>
        <p class="text-sm text-slate-500 mt-1">Pengelolaan berkas dan surat pengantar magang mahasiswa</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Menunggu Verifikasi Dokumen</div>
        <div class="text-3xl font-bold text-slate-900 mt-2"><?= esc($pendingApprovals) ?></div>
        <p class="text-xs text-slate-500 mt-1">Berkas permohonan magang perlu ditinjau</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Magang Aktif Disetujui</div>
        <div class="text-3xl font-bold text-slate-900 mt-2"><?= esc($totalActive) ?></div>
        <p class="text-xs text-slate-500 mt-1">Mahasiswa aktif melaksanakan magang</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Surat Pengantar Diterbitkan</div>
        <div class="text-3xl font-bold text-slate-900 mt-2">0</div>
        <p class="text-xs text-slate-500 mt-1">Dokumen fisik administrasi</p>
    </div>
</div>
<?= $this->endSection() ?>
