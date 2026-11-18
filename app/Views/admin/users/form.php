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
            <label class="block text-xs font-semibold text-slate-600 mb-1"><?= $type === 'instansi' ? 'Nama Instansi / Perusahaan' : 'Nama Lengkap' ?></label>
            <input name="name" value="<?= $val('name') ?>" required class="<?= $in ?>" <?= $type === 'instansi' ? 'placeholder="Contoh: PT Telkom Indonesia"' : '' ?>>
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

        <?php if ($type === 'instansi'): ?>
            <div class="relative">
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Tautkan Mahasiswa Magang:
                </label>
                <p class="text-[11px] text-slate-500 mb-2">Cari mahasiswa untuk ditautkan ke akun instansi ini. Saat login, pembimbing instansi langsung diarahkan ke form penilaian mahasiswa yang ditautkan.</p>
                
                <div class="relative">
                    <input type="text" id="studentSearchInput" placeholder="Ketik nama mahasiswa, NIM, atau nama instansi..." autocomplete="off"
                           class="<?= $in ?> pl-8 text-xs">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <div id="searchSpinner" class="hidden absolute right-2.5 top-2.5">
                        <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Dropdown hasil pencarian -->
                <div id="searchResults" class="hidden absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl max-h-56 overflow-y-auto divide-y divide-slate-100 z-20"></div>

                <!-- Daftar mahasiswa yang dipilih -->
                <div class="mt-3">
                    <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Mahasiswa Ditautkan:</div>
                    <div id="selectedStudentsList" class="space-y-2">
                        <?php foreach ($selectedStudents as $st): ?>
                            <div class="selected-student-item flex items-center justify-between p-2.5 rounded-lg bg-blue-50/60 border border-blue-200" data-id="<?= $st['id'] ?>">
                                <input type="hidden" name="internship_ids[]" value="<?= $st['id'] ?>">
                                <div class="text-xs">
                                    <div class="font-bold text-slate-900"><?= esc($st['student_name']) ?> <span class="font-mono font-normal text-slate-500">(NIM: <?= esc($st['nim']) ?>)</span></div>
                                    <div class="text-slate-600 text-[11px] mt-0.5">
                                        Instansi: <strong class="text-slate-800"><?= esc($st['company_name']) ?></strong> &bull; <?= esc($st['position'] ?: 'Praktikan') ?>
                                    </div>
                                </div>
                                <button type="button" onclick="removeStudent(this)" class="p-1 text-slate-400 hover:text-red-600 hover:bg-white rounded transition" title="Lepaskan tautan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="emptyNotice" class="text-xs text-slate-400 italic py-2 <?= !empty($selectedStudents) ? 'hidden' : '' ?>">
                        Belum ada mahasiswa yang ditautkan. Ketik pada kolom pencarian di atas untuk menambahkan.
                    </div>
                </div>
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

<?php if ($type === 'instansi'): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('studentSearchInput');
    const searchResults = document.getElementById('searchResults');
    const searchSpinner = document.getElementById('searchSpinner');
    const selectedList = document.getElementById('selectedStudentsList');
    const emptyNotice = document.getElementById('emptyNotice');
    const nameInput = document.querySelector('input[name="name"]');

    if (!searchInput) return;

    let debounceTimer = null;

    searchInput.addEventListener('input', (e) => {
        const q = e.target.value.trim();
        clearTimeout(debounceTimer);

        if (q.length < 2) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
            searchSpinner.classList.add('hidden');
            return;
        }

        searchSpinner.classList.remove('hidden');

        debounceTimer = setTimeout(() => {
            fetch(`<?= base_url('admin/users/instansi/search-students') ?>?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => {
                    searchSpinner.classList.add('hidden');
                    searchResults.innerHTML = '';
                    if (!data || data.length === 0) {
                        searchResults.innerHTML = '<div class="p-3 text-xs text-slate-400 text-center italic">Tidak ditemukan mahasiswa dengan kata kunci tersebut.</div>';
                        searchResults.classList.remove('hidden');
                        return;
                    }

                    data.forEach(item => {
                        const isAlreadyAdded = !!selectedList.querySelector(`input[value="${item.id}"]`);
                        const row = document.createElement('div');
                        row.className = 'p-2.5 hover:bg-slate-50 flex items-center justify-between gap-2 cursor-pointer transition';
                        row.innerHTML = `
                            <div class="text-xs leading-tight">
                                <div class="font-bold text-slate-900">${escapeHtml(item.student_name)} <span class="font-mono font-normal text-slate-500">(${escapeHtml(item.nim)})</span></div>
                                <div class="text-slate-500 text-[11px] mt-0.5">${escapeHtml(item.company_name)} &bull; ${escapeHtml(item.position || 'Praktikan')}</div>
                            </div>
                            <button type="button" class="px-2.5 py-1 text-xs font-semibold rounded ${isAlreadyAdded ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white'}">
                                ${isAlreadyAdded ? 'Tertaut' : '+ Tautkan'}
                            </button>
                        `;

                        if (!isAlreadyAdded) {
                            row.addEventListener('click', () => {
                                addStudent(item);
                                searchResults.classList.add('hidden');
                                searchInput.value = '';
                            });
                        }

                        searchResults.appendChild(row);
                    });
                    searchResults.classList.remove('hidden');
                })
                .catch(() => {
                    searchSpinner.classList.add('hidden');
                });
        }, 250);
    });

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });

    function addStudent(item) {
        if (selectedList.querySelector(`input[value="${item.id}"]`)) return;

        const card = document.createElement('div');
        card.className = 'selected-student-item flex items-center justify-between p-2.5 rounded-lg bg-blue-50/60 border border-blue-200';
        card.setAttribute('data-id', item.id);
        card.innerHTML = `
            <input type="hidden" name="internship_ids[]" value="${item.id}">
            <div class="text-xs">
                <div class="font-bold text-slate-900">${escapeHtml(item.student_name)} <span class="font-mono font-normal text-slate-500">(NIM: ${escapeHtml(item.nim)})</span></div>
                <div class="text-slate-600 text-[11px] mt-0.5">
                    Instansi: <strong class="text-slate-800">${escapeHtml(item.company_name)}</strong> &bull; ${escapeHtml(item.position || 'Praktikan')}
                </div>
            </div>
            <button type="button" onclick="removeStudent(this)" class="p-1 text-slate-400 hover:text-red-600 hover:bg-white rounded transition" title="Lepaskan tautan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;
        selectedList.appendChild(card);
        emptyNotice.classList.add('hidden');

        if (nameInput && !nameInput.value.trim() && item.company_name) {
            nameInput.value = item.company_name;
        }
    }

    window.removeStudent = function(btn) {
        btn.closest('.selected-student-item').remove();
        if (selectedList.querySelectorAll('.selected-student-item').length === 0) {
            emptyNotice.classList.remove('hidden');
        }
    };

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>

