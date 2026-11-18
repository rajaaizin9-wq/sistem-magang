<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-5xl">
    <!-- Back Button & Header -->
    <div class="flex items-center space-x-3">
        <a href="<?= base_url('dosen-mk/penilaian') ?>" class="p-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 text-xs font-mono font-bold bg-blue-100 text-blue-800 rounded">
                    <?= esc($course['code']) ?>
                </span>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Komponen Pengumpulan & Rubrik Penilaian</h1>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Mata Kuliah: <strong><?= esc($course['name']) ?></strong> (<?= esc($course['credits']) ?> SKS)</p>
        </div>
    </div>

    <!-- Alert / Messages -->
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

    <!-- Bobot Status Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-3">
        <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-slate-700">Total Akumulasi Bobot Penilaian:</span>
            <div class="flex items-center space-x-2">
                <span class="text-sm font-black <?= $totalWeight == 100.0 ? 'text-emerald-600' : ($totalWeight > 100.0 ? 'text-red-600' : 'text-blue-600') ?>">
                    <?= number_format($totalWeight, 1) ?>%
                </span>
                <span class="text-slate-400">/ 100%</span>
            </div>
        </div>

        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden flex">
            <div class="h-full <?= $totalWeight == 100.0 ? 'bg-emerald-500' : ($totalWeight > 100.0 ? 'bg-red-500' : 'bg-blue-600') ?> transition-all duration-500" 
                 style="width: <?= min(100, $totalWeight) ?>%"></div>
        </div>

        <div class="flex items-center justify-between text-[11px] text-slate-500">
            <span>
                <?php if ($totalWeight == 100.0): ?>
                    <span class="text-emerald-700 font-semibold flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Bobot ideal telah genap 100%. Komponen siap digunakan untuk penilaian.
                    </span>
                <?php elseif ($totalWeight < 100.0): ?>
                    <span class="text-amber-700 font-medium">
                        Sisa bobot yang belum teralokasi: <strong><?= number_format(100.0 - $totalWeight, 1) ?>%</strong>.
                    </span>
                <?php else: ?>
                    <span class="text-red-600 font-semibold">Total bobot melebihi 100%! Harap sesuaikan persentase bobot masing-masing komponen.</span>
                <?php endif; ?>
            </span>
            <span>Total Komponen: <?= count($components) ?></span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Form Tambah Komponen (1/3) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm sticky top-20 space-y-4">
                <div class="pb-3 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900">Tambah Komponen Baru</h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">Tentukan tugas / berkas yang wajib disubmit mahasiswa via Google Drive</p>
                </div>

                <form action="<?= base_url('dosen-mk/penilaian/komponen/' . $course['id'] . '/store') ?>" method="POST" class="space-y-4">
                    <?= csrf_field() ?>

                    <div>
                        <label for="title" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Komponen <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" required 
                               placeholder="Contoh: Laporan Akhir Magang"
                               value="<?= old('title') ?>"
                               class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label for="weight_percentage" class="block text-xs font-semibold text-slate-700 mb-1">
                            Bobot Nilai (%) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="1" max="100" name="weight_percentage" id="weight_percentage" required 
                                   placeholder="Contoh: 40"
                                   value="<?= old('weight_percentage') ?>"
                                   class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition pr-8">
                            <span class="absolute right-3 top-2 text-xs text-slate-400 font-bold">%</span>
                        </div>
                        <?php if ($totalWeight < 100.0): ?>
                            <span class="text-[10px] text-slate-400 mt-1 block">Sisa bobot: <?= number_format(100.0 - $totalWeight, 1) ?>%</span>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                            Keterangan & Petunjuk Pengumpulan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="4" required
                                  placeholder="Jelaskan secara detail apa yang harus ada di dalam folder Google Drive mahasiswa (misal: dokumen laporan bertanda tangan pembimbing lapangan, sertifikat, lampiran dokumentasi foto/video)..."
                                  class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition leading-relaxed"><?= old('description') ?></textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Keterangan ini akan langsung dibaca mahasiswa sebagai instruksi pengumpulan.</p>
                    </div>

                    <button type="submit" 
                            class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Simpan Komponen
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Daftar Komponen Aktif (2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Daftar Komponen Pengumpulan MK</h2>
                    <span class="text-xs text-slate-500 font-medium"><?= count($components) ?> Komponen Aktif</span>
                </div>

                <?php if (empty($components)): ?>
                    <div class="p-8 text-center text-slate-400 space-y-2">
                        <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-xs font-semibold text-slate-700">Belum ada komponen yang ditambahkan</p>
                        <p class="text-[11px] text-slate-400 max-w-sm mx-auto">Silakan tambahkan komponen menggunakan formulir di sebelah kiri agar mahasiswa dapat mengumpulkan berkas magang.</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($components as $idx => $comp): ?>
                            <div class="p-5 space-y-3 hover:bg-slate-50/50 transition" id="comp_view_<?= $comp['id'] ?>">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 text-[11px] font-bold flex items-center justify-center">
                                                <?= $idx + 1 ?>
                                            </span>
                                            <h3 class="text-sm font-bold text-slate-900"><?= esc($comp['title']) ?></h3>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-black shadow-2xs">
                                            Bobot: <?= number_format($comp['weight_percentage'], 0) ?>%
                                        </span>
                                        <button type="button" onclick="toggleEdit(<?= $comp['id'] ?>)" 
                                                class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Komponen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="<?= base_url('dosen-mk/penilaian/komponen/delete/' . $comp['id']) ?>" 
                                              method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus komponen ini? Seluruh data pengumpulan mahasiswa terkait komponen ini akan ikut terhapus.')" 
                                              class="inline m-0">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Komponen">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-lg text-xs text-slate-700 space-y-1">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Keterangan / Instruksi Pengumpulan Mahasiswa:</span>
                                    <p class="leading-relaxed whitespace-pre-line"><?= esc($comp['description'] ?: 'Tidak ada instruksi khusus.') ?></p>
                                </div>
                            </div>

                            <!-- Form Edit Inline (Hidden by default) -->
                            <div class="p-5 bg-blue-50/40 border border-blue-200 rounded-lg hidden m-3 space-y-3" id="comp_edit_<?= $comp['id'] ?>">
                                <div class="flex items-center justify-between pb-2 border-b border-blue-100">
                                    <h4 class="text-xs font-bold text-blue-900">Edit Komponen: <?= esc($comp['title']) ?></h4>
                                    <button type="button" onclick="toggleEdit(<?= $comp['id'] ?>)" class="text-slate-400 hover:text-slate-600 text-xs font-semibold">Batal</button>
                                </div>

                                <form action="<?= base_url('dosen-mk/penilaian/komponen/update/' . $comp['id']) ?>" method="POST" class="space-y-3">
                                    <?= csrf_field() ?>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div class="sm:col-span-2">
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Komponen</label>
                                            <input type="text" name="title" value="<?= esc($comp['title']) ?>" required
                                                   class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Bobot (%)</label>
                                            <input type="number" step="0.01" min="1" max="100" name="weight_percentage" value="<?= esc($comp['weight_percentage']) ?>" required
                                                   class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Keterangan / Instruksi</label>
                                        <textarea name="description" rows="3" required
                                                  class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none leading-relaxed"><?= esc($comp['description']) ?></textarea>
                                    </div>

                                    <div class="flex justify-end space-x-2">
                                        <button type="button" onclick="toggleEdit(<?= $comp['id'] ?>)" 
                                                class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-medium rounded-lg hover:bg-slate-50 transition">
                                            Batal
                                        </button>
                                        <button type="submit" 
                                                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                            Perbarui Komponen
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleEdit(id) {
        const viewEl = document.getElementById('comp_view_' + id);
        const editEl = document.getElementById('comp_edit_' + id);
        if (viewEl && editEl) {
            editEl.classList.toggle('hidden');
        }
    }
</script>
<?= $this->endSection() ?>

