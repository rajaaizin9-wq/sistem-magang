<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="mb-6 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Verifikasi Konversi MK Pengampu</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar permohonan konversi SKS mata kuliah yang Anda ampu dari mahasiswa magang</p>
    </div>
    <div class="flex items-center space-x-2">
        <span class="px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold">
            <?= $pendingCount ?> Menunggu Tindakan
        </span>
        <span class="px-3 py-1.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold">
            <?= $reviewedCount ?> Telah Ditinjau
        </span>
    </div>
</div>

<!-- Section 1: Pengaturan Template Format Proposal per Mata Kuliah -->
<div class="mb-6 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h2 class="text-base font-bold text-slate-900">Format Proposal Mata Kuliah Pilihan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Atur format proposal konversi untuk mata kuliah yang Anda ampu (opsional; secara default sistem menggunakan template standar).</p>
        </div>
        <a href="<?= base_url('konversi/template-default') ?>" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Unduh Template Default Sistem (.DOCX)
        </a>
    </div>

    <?php if (!empty($courses)): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        <th class="px-6 py-3">Mata Kuliah Diampu</th>
                        <th class="px-6 py-3">Format Proposal Aktif</th>
                        <th class="px-6 py-3 text-right">Aksi Template</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($courses as $c): ?>
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5">
                                <span class="font-mono font-bold text-xs px-2 py-0.5 bg-slate-100 text-slate-700 rounded mr-1.5"><?= esc($c['code']) ?></span>
                                <span class="font-semibold text-slate-900 text-sm"><?= esc($c['name']) ?></span>
                                <span class="text-xs text-slate-500 ml-1">(<?= esc($c['credits']) ?> SKS)</span>
                            </td>
                            <td class="px-6 py-3.5 text-xs">
                                <?php if (!empty($c['proposal_template'])): ?>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                            Format Khusus Dosen
                                        </span>
                                        <a href="<?= base_url('konversi/template-mk/' . $c['id']) ?>" target="_blank" class="text-blue-600 hover:underline font-medium">
                                            Lihat File
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        Template Standar Sistem
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <!-- Form Upload Template Khusus -->
                                    <form action="<?= base_url('dosen-mk/konversi/template-upload/' . $c['id']) ?>" method="POST" enctype="multipart/form-data" class="inline-flex items-center gap-1.5">
                                        <?= csrf_field() ?>
                                        <label class="cursor-pointer inline-flex items-center px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-md border border-slate-200 transition">
                                            <span><?= !empty($c['proposal_template']) ? 'Ganti Format' : 'Upload Format Khusus' ?></span>
                                            <input type="file" name="template_file" accept=".doc,.docx,.pdf" class="hidden" onchange="if(confirm('Unggah format template proposal untuk <?= esc($c['name']) ?>?')) this.form.submit();">
                                        </label>
                                    </form>

                                    <?php if (!empty($c['proposal_template'])): ?>
                                        <form action="<?= base_url('dosen-mk/konversi/template-hapus/' . $c['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Kembalikan ke format template default sistem?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-2.5 py-1 text-xs text-red-600 hover:bg-red-50 rounded-md border border-red-200 font-semibold transition">
                                                Reset ke Default
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="p-5 text-xs text-slate-400 italic">Tidak ada mata kuliah pilihan yang terdaftar di bawah pengampuan Anda.</p>
    <?php endif; ?>
</div>

<!-- Section 2: Daftar Permohonan Konversi SKS Masuk -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-200">
        <h2 class="text-base font-bold text-slate-900">Daftar Pengajuan Konversi dari Mahasiswa</h2>
        <p class="text-xs text-slate-500 mt-0.5">Tinjau proposal dan kesesuaian capaian pembelajaran magang mahasiswa dengan mata kuliah yang Anda ampu.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    <th class="px-6 py-3.5">Mahasiswa</th>
                    <th class="px-6 py-3.5">Mata Kuliah Diampu</th>
                    <th class="px-6 py-3.5">Instansi Magang</th>
                    <th class="px-6 py-3.5">Status Respon Anda</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $req): ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?= esc($req['student_name']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">NIM: <?= esc($req['nim']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 text-xs font-mono font-bold bg-slate-200 text-slate-800 rounded">
                                        <?= esc($req['course_code']) ?>
                                    </span>
                                    <span class="font-semibold text-slate-800 text-sm"><?= esc($req['course_name']) ?></span>
                                </div>
                                <div class="text-xs text-blue-600 font-semibold mt-1"><?= esc($req['credits']) ?> SKS</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900"><?= esc($req['company_name']) ?></div>
                                <div class="text-xs text-slate-500"><?= esc($req['position']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <?= view('components/status_badge', ['status' => $req['status']]) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= base_url('dosen-mk/konversi/detail/' . $req['id']) ?>" class="inline-flex items-center px-3.5 py-1.5 <?= $req['status'] === 'PENDING' ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' ?> text-xs font-semibold rounded-md transition">
                                    <?= $req['status'] === 'PENDING' ? 'Tinjau & Verifikasi' : 'Lihat Detail' ?>
                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <div class="max-w-xs mx-auto text-center">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-700">Tidak ada pengajuan konversi</p>
                                <p class="text-xs text-slate-500 mt-1">Belum ada mahasiswa yang mengajukan konversi untuk mata kuliah yang Anda ampu.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
