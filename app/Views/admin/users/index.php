<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Kelola Akun <?= esc($label) ?></h1>
        <p class="text-sm text-slate-500 mt-1">Tambah, ubah, dan hapus akun <?= esc(strtolower($label)) ?>.</p>
    </div>
    <a href="<?= base_url("admin/users/$type/form") ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">+ Tambah</a>
</div>

<?php if ($type === 'instansi' && !empty($pendingRequests)): ?>
    <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 sm:p-5 shadow-sm">
        <div class="flex items-center justify-between pb-3 border-b border-amber-200/70 mb-3">
            <div class="flex items-center gap-2.5">
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                </span>
                <h2 class="text-sm font-bold text-amber-900">Permintaan Pembuatan Akun Instansi dari Mahasiswa</h2>
                <span class="px-2 py-0.5 text-xs font-semibold bg-amber-200 text-amber-800 rounded-full"><?= count($pendingRequests) ?></span>
            </div>
            <p class="text-xs text-amber-700 hidden sm:block">Klik tombol untuk membuat akun dan menautkan otomatis.</p>
        </div>
        <div class="divide-y divide-amber-200/50">
            <?php foreach ($pendingRequests as $req): ?>
                <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-0.5 text-xs">
                        <div class="font-bold text-slate-800 text-sm"><?= esc($req['company_name']) ?></div>
                        <div class="text-slate-600">
                            PIC: <span class="font-semibold text-slate-700"><?= esc($req['instansi_pic_name'] ?: '-') ?></span> 
                            &bull; Email: <span class="font-mono text-slate-700"><?= esc($req['instansi_pic_email'] ?: '-') ?></span>
                            <?php if (!empty($req['instansi_pic_phone'])): ?>
                                &bull; Telp: <span class="text-slate-700"><?= esc($req['instansi_pic_phone']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="text-slate-500 text-[11px]">
                            Diajukan oleh: <span class="font-medium text-slate-700"><?= esc($req['student_name']) ?></span> (NIM: <?= esc($req['nim']) ?>) 
                            &bull; <?= format_indo($req['instansi_account_requested_at'], true) ?>
                        </div>
                    </div>
                    <div>
                        <a href="<?= base_url("admin/users/instansi/form?from_internship={$req['id']}") ?>" 
                           class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg text-xs transition shadow-sm">
                            + Buat Akun Instansi
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <th class="px-6 py-3.5">Nama</th>
                    <th class="px-6 py-3.5">Email</th>
                    <?php if ($type === 'kaprodi'): ?><th class="px-6 py-3.5">NIDN / Prodi</th><?php endif; ?>
                    <?php if ($type === 'instansi'): ?><th class="px-6 py-3.5">Mahasiswa Terhubung</th><?php endif; ?>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php foreach ($items as $u): ?>
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-6 py-4 font-semibold text-slate-900"><?= esc($u['name']) ?></td>
                        <td class="px-6 py-4 text-slate-600"><?= esc($u['email']) ?></td>
                        <?php if ($type === 'kaprodi'): ?>
                            <td class="px-6 py-4 text-slate-600"><?= esc($u['nidn'] ?? '-') ?><div class="text-xs text-slate-400"><?= esc($u['prodi_name'] ?? '-') ?></div></td>
                        <?php endif; ?>
                        <?php if ($type === 'instansi'): ?>
                            <td class="px-6 py-4 text-slate-600">
                                <?php if (!empty($u['student_names'])): ?>
                                    <div class="font-semibold text-slate-800 text-xs"><?= esc($u['student_names']) ?></div>
                                    <div class="text-[11px] text-blue-600 font-medium"><?= (int)($u['student_count'] ?? 0) ?> Mahasiswa</div>
                                <?php else: ?>
                                    <span class="text-slate-400 text-xs italic">Belum ditautkan</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold <?= $u['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>"><?= $u['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="<?= base_url("admin/users/$type/form/{$u['id']}") ?>" class="text-blue-600 hover:underline text-xs font-semibold">Edit</a>
                            <form action="<?= base_url("admin/users/$type/delete/{$u['id']}") ?>" method="post" class="inline ml-3" onsubmit="return confirm('Hapus akun ini?')">
                                <?= csrf_field() ?>
                                <button class="text-red-600 hover:underline text-xs font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada data.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

