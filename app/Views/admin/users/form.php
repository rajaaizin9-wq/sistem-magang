<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$id  = $item['id'] ?? null;
$val = fn($k, $d = '') => esc(old($k, $item[$k] ?? $d));
$in  = 'w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';
?>
<div class="max-w-xl bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <h1 class="text-xl font-bold text-slate-900 mb-5"><?= esc($title) ?></h1>

    <form action="<?= base_url("admin/users/$type/save" . ($id ? "/$id" : '')) ?>" method="post" class="space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1"><?= $type === 'instansi' ? 'Nama Instansi / Penilai' : 'Nama Lengkap' ?></label>
            <input name="name" value="<?= $val('name') ?>" required class="<?= $in ?>">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
            <input type="email" name="email" value="<?= $val('email') ?>" required class="<?= $in ?>">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Password <?= $id ? '<span class="font-normal text-slate-400">(kosongkan jika tidak diubah)</span>' : '' ?></label>
            <input type="password" name="password" minlength="8" <?= $id ? '' : 'required' ?> class="<?= $in ?>">
        </div>

        <?php if ($type === 'kaprodi'): ?>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">NIDN</label>
                <input name="nidn" value="<?= $val('nidn') ?>" required class="<?= $in ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Program Studi</label>
                <select name="program_study_id" required class="<?= $in ?>">
                    <option value="">- Pilih -</option>
                    <?php foreach ($prodis as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= old('program_study_id', $item['program_study_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= esc($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="is_active" value="1" <?= old('is_active', $item['is_active'] ?? 1) ? 'checked' : '' ?>> Akun aktif
        </label>

        <div class="flex justify-end gap-2 pt-2">
            <a href="<?= base_url("admin/users/$type") ?>" class="px-4 py-2 text-sm font-semibold text-slate-600 border border-slate-300 rounded-lg hover:bg-slate-50">Batal</a>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">Simpan</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

