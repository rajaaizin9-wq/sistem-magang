<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="w-full max-w-md bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <!-- Header Logo & Title -->
    <div class="bg-slate-900 px-6 py-6 text-center text-white">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 rounded-lg text-white font-bold text-xl mb-3 shadow">
            S
        </div>
        <h1 class="text-xl font-bold tracking-tight">SIMAGANG</h1>
        <p class="text-xs text-slate-400 mt-1">Sistem Pendaftaran & Pengelolaan Magang</p>
    </div>

    <div class="p-6">
        <!-- Flash Alert Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-4 p-3 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-xs rounded">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="<?= base_url('login') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Email</label>
                <input type="email" id="email" name="email" value="<?= old('email') ?>" required placeholder="nama@simagang.ac.id" 
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi</label>
                <input type="password" id="password" name="password" required placeholder="••••••••" 
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors">
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-medium text-sm rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-slate-900">
                Masuk ke Sistem
            </button>
        </form>

        <!-- Quick Login Demo Buttons (For Testing 6 Roles) -->
        <div class="mt-6 pt-5 border-t border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider text-center mb-3">Akun Uji Coba (Quick Demo)</p>
            <div class="grid grid-cols-2 gap-1.5 text-xs">
                <button type="button" onclick="fillDemo('mahasiswa@simagang.ac.id')" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-left transition font-medium">
                    🎓 Mahasiswa
                </button>
                <button type="button" onclick="fillDemo('tu@simagang.ac.id')" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-left transition font-medium">
                    📁 Staf TU
                </button>
                <button type="button" onclick="fillDemo('kaprodi@simagang.ac.id')" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-left transition font-medium">
                    🏛️ Kaprodi
                </button>
                <button type="button" onclick="fillDemo('dosbem@simagang.ac.id')" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-left transition font-medium">
                    👨‍🏫 Dosen (Ahmad Fauzi)
                </button>
                <button type="button" onclick="fillDemo('dosenmk@simagang.ac.id')" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-left transition font-medium">
                    👩‍🏫 Dosen (Rina W.)
                </button>
                <button type="button" onclick="fillDemo('wadek1@simagang.ac.id')" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-left transition font-medium">
                    👔 Wadek 1
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function fillDemo(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password123';
    }
</script>
<?= $this->endSection() ?>
