<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Section -->
    <div class="pb-2 border-b border-slate-200">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pengumpulan Berkas & Penilaian Magang</h1>
        <p class="text-xs text-slate-500 mt-1">
            Lihat instruksi tugas dari Dosen Pengampu, kumpulkan tautan Google Drive berkas magang, dan pantau hasil nilai akhir konversi MK
        </p>
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

    <?php if (!$internship): ?>
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mb-1">Belum Ada Program Magang Aktif</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Anda belum memiliki periode magang yang aktif berjalan untuk melakukan pengumpulan berkas dan penilaian.</p>
        </div>
    <?php elseif (empty($coursesData)): ?>
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mb-1">Belum Ada Mata Kuliah Konversi yang Disetujui</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Pengumpulan berkas dan penilaian dilakukan untuk mata kuliah konversi yang telah disetujui. Silakan periksa status pengajuan konversi MK Anda terlebih dahulu.</p>
            <div class="mt-4">
                <a href="<?= base_url('mahasiswa/konversi') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold shadow-sm hover:bg-blue-700 transition">
                    Lihat Pengajuan Konversi &rarr;
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Quick Internship Banner -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-xl p-5 text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-200">Periode Magang Aktif:</span>
                <h2 class="text-lg font-bold text-white"><?= esc($internship['company_name']) ?></h2>
                <p class="text-xs text-blue-100 font-medium">
                    <?= esc($internship['position'] ?: 'Praktikan Magang') ?> &bull; Tahun Akademik <?= esc($internship['academic_year']) ?> (<?= esc($internship['semester']) ?>)
                </p>
            </div>
            <div class="text-right">
                <span class="text-xs text-blue-200 block">Total Mata Kuliah:</span>
                <span class="text-xl font-black text-white"><?= count($coursesData) ?> MK Konversi</span>
            </div>
        </div>

        <!-- Card Penilaian dari Pembimbing Instansi -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start space-x-3.5">
                <div class="w-10 h-10 rounded-xl <?= $instansiAssessment ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' ?> flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-sm font-bold text-slate-900">Penilaian Pembimbing Lapangan Instansi</h3>
                        <?php if ($instansiAssessment): ?>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Telah Dinilai</span>
                        <?php elseif (!empty($internship['instansi_account_requested_at'])): ?>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">Permintaan Akun Diajukan ke Admin</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-700">Menunggu Penilaian</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Instansi: <strong><?= esc($internship['company_name']) ?></strong>
                        <?php if ($instansiAssessment && !empty($instansiAssessment['evaluator_name'])): ?>
                            &bull; Penilai: <?= esc($instansiAssessment['evaluator_name']) ?> (<?= esc($instansiAssessment['evaluator_position'] ?: 'Pembimbing Lapangan') ?>)
                        <?php elseif (!empty($internship['instansi_pic_name'])): ?>
                            &bull; PIC Diajukan: <?= esc($internship['instansi_pic_name']) ?> (<?= esc($internship['instansi_pic_email']) ?>)
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-3 self-end sm:self-center">
                <?php if ($instansiAssessment): ?>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 font-semibold block uppercase">Nilai Rata-rata:</span>
                        <div class="inline-flex items-center space-x-1.5">
                            <span class="text-lg font-black text-emerald-700"><?= number_format($instansiAssessment['final_score'], 1) ?></span>
                            <span class="px-1.5 py-0.5 bg-emerald-600 text-white rounded text-[10px] font-bold">Grade <?= esc($instansiAssessment['grade']) ?></span>
                        </div>
                    </div>
                    <a href="<?= base_url('mahasiswa/penilaian/instansi/' . $internship['id']) ?>" target="_blank"
                       class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition inline-flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak / Lihat</span>
                    </a>
                <?php else: ?>
                    <?php if (empty($internship['company_user_id'])): ?>
                        <button type="button" onclick="document.getElementById('modalRequestInstansi').classList.remove('hidden')"
                                class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition inline-flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            <span><?= !empty($internship['instansi_account_requested_at']) ? 'Ubah Pengajuan Akun PIC' : 'Ajukan Akun Pembimbing Instansi' ?></span>
                        </button>
                    <?php else: ?>
                        <span class="text-xs text-slate-400 italic">Akun instansi aktif, menunggu pengisian nilai</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal Ajukan Akun Instansi -->
        <div id="modalRequestInstansi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4 border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Ajukan Akun Pembimbing Instansi</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Admin akan membuatkan akun agar pembimbing lapangan dapat mengisi nilai Anda</p>
                    </div>
                    <button type="button" onclick="document.getElementById('modalRequestInstansi').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="<?= base_url('mahasiswa/penilaian/request-instansi-account') ?>" method="POST" class="space-y-3.5">
                    <?= csrf_field() ?>
                    <input type="hidden" name="internship_id" value="<?= $internship['id'] ?>">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Instansi / Perusahaan</label>
                        <input type="text" disabled value="<?= esc($internship['company_name']) ?>" class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-200 rounded-lg text-slate-600 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pembimbing / Mentor Lapangan <span class="text-red-500">*</span></label>
                        <input type="text" name="instansi_pic_name" required 
                               value="<?= esc(old('instansi_pic_name', $internship['instansi_pic_name'] ?? '')) ?>" 
                               placeholder="Contoh: Budi Santoso, S.Kom" 
                               class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email Pembimbing Instansi <span class="text-red-500">*</span></label>
                        <input type="email" name="instansi_pic_email" required 
                               value="<?= esc(old('instansi_pic_email', $internship['instansi_pic_email'] ?? '')) ?>" 
                               placeholder="Contoh: budi.santoso@perusahaan.com" 
                               class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Email ini akan digunakan oleh Admin untuk membuat akun login pembimbing.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / Telepon Pembimbing (Opsional)</label>
                        <input type="text" name="instansi_pic_phone" 
                               value="<?= esc(old('instansi_pic_phone', $internship['instansi_pic_phone'] ?? '')) ?>" 
                               placeholder="Contoh: 081234567890" 
                               class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('modalRequestInstansi').classList.add('hidden')"
                                class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs transition">
                            Kirim Permintaan ke Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Grid of Courses Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($coursesData as $c): ?>
                <?php 
                    $crc   = $c['crc'];
                    $final = $c['final_assessment'];
                    $compCount = $c['component_count'];
                ?>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                    <div class="p-5 space-y-4">
                        <!-- Card Header -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 text-xs font-mono font-bold bg-blue-100 text-blue-800 rounded">
                                        <?= esc($crc['course_code']) ?>
                                    </span>
                                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                        <?= esc($crc['credits']) ?> SKS
                                    </span>
                                </div>
                                <h3 class="font-bold text-slate-900 text-base leading-snug"><?= esc($crc['course_name']) ?></h3>
                            </div>

                            <!-- Nilai Akhir Badge (Jika sudah diverifikasi Dosen) -->
                            <?php if ($final && !empty($final['verified_status'])): ?>
                                <div class="text-right flex-shrink-0">
                                    <span class="text-[10px] text-slate-400 block uppercase font-bold">Nilai Akhir:</span>
                                    <div class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-200 font-black">
                                        <span class="text-sm"><?= number_format($final['score'], 1) ?></span>
                                        <span class="text-xs px-1.5 py-0.2 bg-emerald-600 text-white rounded font-bold"><?= esc($final['final_grade']) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Info Dosen Pengampu -->
                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 text-xs text-slate-600 space-y-1">
                            <div class="flex items-center text-slate-700">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Dosen Pengampu: <strong><?= esc($crc['lecturer_name'] ?? 'Belum ditentukan') ?></strong></span>
                            </div>
                            <?php if (!empty($crc['lecturer_nidn'])): ?>
                                <div class="text-[11px] text-slate-400 pl-5">NIDN: <?= esc($crc['lecturer_nidn']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Status Pengumpulan Google Drive -->
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-center justify-between font-semibold text-slate-700">
                                <span>Status Pengumpulan:</span>
                                <?php 
                                    $sub = $c['submission'];
                                    $subStatus = $sub['status'] ?? 'BELUM';
                                ?>
                                <?php if ($compCount === 0): ?>
                                    <span class="text-slate-400 italic">Menunggu rubrik dosen</span>
                                <?php elseif (!$sub || $subStatus === 'BELUM'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        Belum Kumpul
                                    </span>
                                <?php elseif ($subStatus === 'SUBMITTED'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                        Menunggu Review
                                    </span>
                                <?php elseif ($subStatus === 'REVISION_REQUIRED'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600 mr-1.5"></span>
                                        Perlu Perbaikan
                                    </span>
                                <?php elseif ($subStatus === 'APPROVED'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Disetujui
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Card Action Footer -->
                    <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-500">
                            <?= $compCount ?> Komponen Penilaian
                        </span>
                        <a href="<?= base_url('mahasiswa/penilaian/detail/' . $crc['id']) ?>" 
                           class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm transition">
                            <span>Kumpulkan & Nilai</span>
                            <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

