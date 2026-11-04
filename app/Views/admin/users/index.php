<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Kelola Akun <?= esc($label) ?></h1>
        <p class="text-sm text-slate-500 mt-1">Tambah, ubah, dan hapus akun <?= esc(strtolower($label)) ?>.</p>
    </div>
    <a href="<?= base_url("admin/users/$type/form") ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">+ Tambah</a>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <th class="px-6 py-3.5">Nama</th>
                    <th class="px-6 py-3.5">Email</th>
                    <?php if ($type === 'kaprodi'): ?><th class="px-6 py-3.5">NIDN / Prodi</th><?php endif; ?>
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

