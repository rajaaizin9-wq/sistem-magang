<?= $this->extend('layouts/instansi') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto py-12 text-center space-y-4">
    <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
    </div>
    <div class="space-y-1">
        <h1 class="text-xl font-bold text-slate-900">Selamat Datang, <?= esc($user['name']) ?></h1>
        <p class="text-xs text-slate-500 max-w-md mx-auto">
            Akun instansi Anda saat ini belum ditautkan ke data mahasiswa magang yang aktif.
        </p>
    </div>
    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 text-left max-w-md mx-auto">
        <strong>Pemberitahuan:</strong> Silakan hubungi koordinator magang atau admin fakultas untuk menautkan akun ini dengan mahasiswa yang melaksanakan magang di tempat Anda.
    </div>
</div>
<?= $this->endSection() ?>

