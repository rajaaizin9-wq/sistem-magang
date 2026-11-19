<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white p-5 sm:p-6 rounded-xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Komponen Penilaian oleh Instansi</h1>
            <p class="text-xs text-slate-500 mt-1">
                Atur butir-butir kriteria penilaian praktikan magang yang akan diisi oleh pembimbing lapangan instansi mitra.
            </p>
        </div>
        <div class="flex items-center space-x-2.5">
            <form action="<?= base_url('admin/kriteria-instansi/reset-default') ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh kriteria penilaian instansi kembali ke 5 kriteria standar (default)? Data kriteria kustom akan diganti.');">
                <?= csrf_field() ?>
                <button type="submit" class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 shadow-2xs transition">
                    Reset ke Default
                </button>
            </form>
            <button type="button" onclick="openModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-xs transition inline-flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Kriteria</span>
            </button>
        </div>
    </div>

    <!-- Flash Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 flex items-center space-x-2">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800 flex items-center space-x-2">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <!-- Table of Criteria -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-4 w-16 text-center">Urutan</th>
                        <th class="py-3 px-5">Nama Kriteria Penilaian</th>
                        <th class="py-3 px-5">Panduan / Rincian Rubrik</th>
                        <th class="py-3 px-4 text-center w-28">Status</th>
                        <th class="py-3 px-5 text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($components)): ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 italic">
                                Belum ada kriteria penilaian. Klik "Tambah Kriteria" atau "Reset ke Default".
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($components as $c): ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-4 px-4 text-center font-bold text-slate-700">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 font-mono text-xs">
                                        <?= (int)$c['order_number'] ?>
                                    </span>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-900 text-sm"><?= esc($c['name']) ?></div>
                                </td>
                                <td class="py-4 px-5 text-slate-600 leading-relaxed max-w-md">
                                    <?php if (!empty($c['description'])): ?>
                                        <div class="text-[11px] whitespace-pre-line"><?= esc($c['description']) ?></div>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic text-[11px]">- Tidak ada keterangan rincian -</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <?php if ($c['is_active']): ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                            Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <button type="button" 
                                            onclick='openEditModal(<?= json_encode($c, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                            class="text-blue-600 hover:text-blue-800 font-semibold text-xs hover:underline mr-3">
                                        Edit
                                    </button>
                                    <form action="<?= base_url('admin/kriteria-instansi/delete/' . $c['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Hapus kriteria penilaian ini?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-200 text-slate-500 text-[11px] flex items-center justify-between">
            <span>Total Kriteria Aktif: <strong><?= count(array_filter($components, fn($c) => $c['is_active'] == 1)) ?></strong> kriteria.</span>
            <span>Nilai akhir instansi dihitung dari: <strong>Rata-rata = Jumlah Nilai / Jumlah Kriteria Aktif</strong></span>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Kriteria -->
<div id="modalKriteria" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-2xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4 border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 id="modalTitle" class="text-base font-bold text-slate-900">Tambah Kriteria Penilaian Instansi</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Kriteria ini akan muncul di formulir penilaian pembimbing instansi.</p>
            </div>
            <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="formKriteria" action="<?= base_url('admin/kriteria-instansi/save') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-1">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Urutan <span class="text-red-500">*</span></label>
                    <input type="number" name="order_number" id="inputOrder" min="1" max="99" required value="1"
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kriteria Penilaian <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="inputName" required placeholder="Contoh: Kedisiplinan & Sikap Kerja"
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Panduan / Rincian Indikator Rubrik</label>
                <textarea name="description" id="inputDesc" rows="4" 
                          placeholder="Contoh:&#10;1.1 Kehadiran tepat waktu&#10;1.2 Mematuhi SOP dan regulasi perusahaan"
                          class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none leading-relaxed"></textarea>
                <span class="text-[10px] text-slate-400 mt-1 block">Tuliskan panduan atau poin rincian rubrik per baris untuk memandu penilai.</span>
            </div>

            <div class="pt-1">
                <label class="inline-flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_active" id="inputActive" value="1" checked
                           class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4">
                    <span class="text-xs font-medium text-slate-700">Aktifkan kriteria ini pada formulir penilaian</span>
                </label>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs transition">
                    Simpan Kriteria
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('modalKriteria');
    const form = document.getElementById('formKriteria');
    const modalTitle = document.getElementById('modalTitle');
    const inputOrder = document.getElementById('inputOrder');
    const inputName = document.getElementById('inputName');
    const inputDesc = document.getElementById('inputDesc');
    const inputActive = document.getElementById('inputActive');

    function openModal() {
        form.action = '<?= base_url('admin/kriteria-instansi/save') ?>';
        modalTitle.textContent = 'Tambah Kriteria Penilaian Instansi';
        inputOrder.value = '<?= count($components) + 1 ?>';
        inputName.value = '';
        inputDesc.value = '';
        inputActive.checked = true;
        modal.classList.remove('hidden');
    }

    function openEditModal(data) {
        form.action = '<?= base_url('admin/kriteria-instansi/save') ?>/' + data.id;
        modalTitle.textContent = 'Edit Kriteria Penilaian Instansi';
        inputOrder.value = data.order_number;
        inputName.value = data.name;
        inputDesc.value = data.description || '';
        inputActive.checked = parseInt(data.is_active) === 1;
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
<?= $this->endSection() ?>

