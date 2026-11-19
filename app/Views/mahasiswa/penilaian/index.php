<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-5">
    <!-- Header & Periode Switcher -->
    <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pengumpulan Berkas & Penilaian</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau nilai dari pembimbing instansi dan kumpulkan tugas konversi MK ke Dosen Pengampu.</p>
        </div>

        <?php if (!empty($internships)): ?>
            <!-- Dropdown Pilihan Periode Magang -->
            <div class="flex items-center space-x-2 self-start md:self-auto">
                <label for="periodeSelect" class="text-xs font-semibold text-slate-500 whitespace-nowrap">Periode Magang:</label>
                <div class="relative">
                    <select id="periodeSelect" 
                            onchange="window.location.href='<?= base_url('mahasiswa/penilaian?periode=') ?>' + this.value"
                            class="appearance-none pl-3 pr-8 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-bold text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-xs transition">
                        <?php foreach ($internships as $item): ?>
                            <option value="<?= $item['id'] ?>" <?= (int)$item['id'] === (int)$internship['id'] ? 'selected' : '' ?>>
                                <?= esc($item['company_name']) ?> &bull; TA <?= esc($item['academic_year']) ?> (<?= esc($item['semester']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center shadow-xs">
            <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mb-1">Belum Ada Program Magang</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Anda belum memiliki periode magang yang aktif berjalan untuk melakukan pengumpulan berkas dan penilaian.</p>
        </div>
    <?php else: ?>
        <!-- Card Terpadu: Ringkasan Periode & Penilaian Pembimbing Lapangan -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <!-- Sisi Kiri: Identitas Magang -->
            <div class="space-y-1.5">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-base font-bold text-slate-900"><?= esc($internship['company_name']) ?></h2>
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        TA <?= esc($internship['academic_year']) ?> (<?= esc($internship['semester']) ?>)
                    </span>
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <?= str_replace('_', ' ', $internship['status']) ?>
                    </span>
                </div>
                <div class="text-xs text-slate-500 flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span>Posisi: <strong class="text-slate-700 font-semibold"><?= esc($internship['position'] ?: 'Praktikan Magang') ?></strong></span>
                    <span>Periode: <strong class="text-slate-700 font-semibold"><?= format_indo($internship['start_date'], false) ?> &ndash; <?= format_indo($internship['end_date'], false) ?></strong></span>
                    <span>Total MK: <strong class="text-slate-700 font-semibold"><?= count($coursesData) ?> MK Konversi</strong></span>
                </div>
            </div>

            <!-- Sisi Kanan: Status Nilai Pembimbing Lapangan (Instansi) -->
            <div class="bg-slate-50 border border-slate-200/90 rounded-xl p-3.5 flex items-center justify-between sm:justify-start gap-4 self-stretch lg:self-auto text-xs">
                <?php if ($instansiAssessment): ?>
                    <!-- Sudah Dinilai -->
                    <div class="space-y-0.5 pr-2">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Nilai Lapangan (Instansi)</span>
                        <div class="flex items-center space-x-1.5">
                            <span class="text-xl font-black text-emerald-700 leading-none">
                                <?= number_format($instansiAssessment['final_score'], 2) ?>
                            </span>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-100 px-1.5 py-0.5 rounded">Telah Dinilai</span>
                        </div>
                    </div>
                    <a href="<?= base_url('mahasiswa/penilaian/instansi/' . $internship['id']) ?>" target="_blank"
                       class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition inline-flex items-center space-x-1 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Lembar Nilai</span>
                    </a>
                <?php elseif (!empty($internship['company_user_id'])): ?>
                    <!-- Akun Aktif, Menunggu Nilai -->
                    <div class="space-y-0.5">
                        <span class="text-[10px] text-blue-700 font-bold uppercase tracking-wider block">Akun Pembimbing Aktif</span>
                        <p class="text-[11px] text-slate-500">Menunggu pembimbing lapangan mengisi nilai praktikan.</p>
                    </div>
                <?php else: ?>
                    <!-- Belum Ada Akun / Sedang Diajukan -->
                    <div class="space-y-0.5 pr-2">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Pembimbing Lapangan</span>
                        <?php if (!empty($internship['instansi_account_requested_at'])): ?>
                            <div class="text-[11px] font-semibold text-amber-800 flex items-center space-x-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <span>Diajukan: <?= esc($internship['instansi_pic_name']) ?></span>
                            </div>
                            <span class="text-[10px] text-slate-400 block"><?= esc($internship['instansi_pic_email']) ?></span>
                        <?php else: ?>
                            <span class="text-[11px] text-slate-400 italic block">Akun pembimbing belum dibuat</span>
                        <?php endif; ?>
                    </div>
                    <button type="button" onclick="document.getElementById('modalRequestInstansi').classList.remove('hidden')"
                            class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition whitespace-nowrap">
                        <?= !empty($internship['instansi_account_requested_at']) ? 'Ubah PIC' : '+ Ajukan Akun PIC' ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tabel Mata Kuliah Konversi -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Mata Kuliah Konversi</h2>
                    <p class="text-[11px] text-slate-500">Kumpulkan tautan tugas Google Drive dan pantau nilai akhir dari masing-masing Dosen Pengampu.</p>
                </div>
                <span class="text-xs font-bold text-slate-500"><?= count($coursesData) ?> Mata Kuliah</span>
            </div>

            <?php if (empty($coursesData)): ?>
                <div class="p-8 text-center text-xs text-slate-500">
                    <div class="w-10 h-10 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="font-medium text-slate-700">Belum ada mata kuliah konversi yang disetujui untuk periode magang ini.</p>
                    <p class="text-slate-400 text-[11px] mt-0.5">Pengumpulan berkas dapat dilakukan setelah pengajuan konversi disahkan oleh Wadek 1.</p>
                    <div class="mt-3">
                        <a href="<?= base_url('mahasiswa/konversi') ?>" class="text-blue-600 hover:underline font-semibold">
                            Lihat Pengajuan Konversi &rarr;
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                                <th class="py-3 px-5">Mata Kuliah</th>
                                <th class="py-3 px-4">Dosen Pengampu</th>
                                <th class="py-3 px-4 text-center">Status Berkas</th>
                                <th class="py-3 px-4 text-center">Nilai Akhir</th>
                                <th class="py-3 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($coursesData as $c): ?>
                                <?php 
                                    $crc   = $c['crc'];
                                    $final = $c['final_assessment'];
                                    $sub   = $c['submission'];
                                    $subStatus = $sub['status'] ?? 'BELUM';
                                    $isGraded  = $final && !empty($final['verified_status']);
                                ?>
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900 text-sm leading-snug"><?= esc($crc['course_name']) ?></div>
                                        <div class="text-[11px] text-slate-500 mt-0.5 flex items-center space-x-1.5">
                                            <span class="font-mono font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded"><?= esc($crc['course_code']) ?></span>
                                            <span>&bull;</span>
                                            <span><?= esc($crc['credits']) ?> SKS</span>
                                            <span>&bull;</span>
                                            <span class="text-slate-400"><?= $c['component_count'] ?> Komponen Penilaian</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-800"><?= esc($crc['lecturer_name'] ?? 'Belum ditentukan') ?></div>
                                        <?php if (!empty($crc['lecturer_nidn'])): ?>
                                            <div class="text-[10px] text-slate-400 font-mono">NIDN: <?= esc($crc['lecturer_nidn']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <?php if ($c['component_count'] === 0): ?>
                                            <span class="text-slate-400 text-[11px] italic">Belum ada rubrik</span>
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
                                                Perlu Perbaikan
                                            </span>
                                        <?php elseif ($subStatus === 'APPROVED'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Disetujui
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <?php if ($isGraded): ?>
                                            <div class="inline-flex items-center space-x-1.5">
                                                <span class="text-sm font-black text-slate-900"><?= number_format($final['score'], 1) ?></span>
                                                <span class="px-1.5 py-0.2 rounded font-bold text-[10px] bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <?= esc($final['final_grade']) ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-slate-300 font-bold text-sm">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                        <a href="<?= base_url('mahasiswa/penilaian/detail/' . $crc['id']) ?>" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold shadow-xs transition <?= $isGraded ? 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200' : 'bg-blue-600 hover:bg-blue-700 text-white' ?>">
                                            <span><?= $isGraded ? 'Lihat Nilai' : ($subStatus === 'BELUM' ? 'Kumpulkan' : 'Detail Tugas') ?></span>
                                            <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Modal Ajukan / Ubah PIC Instansi -->
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
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
