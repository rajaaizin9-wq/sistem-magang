<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SIMAGANG - Portal Penilaian Instansi' ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            50: '#f0f4f8',
                            100: '#d9e2ec',
                            500: '#334e68',
                            600: '#243b53',
                            700: '#102a43',
                            800: '#091e36',
                        },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col">
    <!-- Top Navigation Bar (Tanpa Sidebar) -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <!-- Brand / Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center font-black text-white shadow-sm text-base">
                    S
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-extrabold text-slate-900 tracking-tight text-base">SIMAGANG</span>
                        <span class="hidden sm:inline-block px-2 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-[10px] font-bold text-blue-700">
                            Mitra Instansi
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 hidden sm:block">Portal Penilaian Praktikan Magang</p>
                </div>
            </div>

            <!-- User Info & Logout -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <div class="text-right hidden sm:block">
                    <span class="text-xs font-bold text-slate-800 block leading-tight"><?= esc(session()->get('userName') ?? '') ?></span>
                    <span class="text-[11px] text-slate-400 block leading-tight"><?= esc(session()->get('userEmail') ?? '') ?></span>
                </div>

                <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                <a href="<?= base_url('logout') ?>" 
                   onclick="return confirm('Keluar dari portal penilaian instansi?')"
                   class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 hover:text-red-700 hover:bg-red-50 border border-red-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-6 sm:py-8">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-5 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs sm:text-sm rounded-r-xl shadow-xs flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><?= session()->getFlashdata('success') ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 text-xs sm:text-sm rounded-r-xl shadow-xs flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span><?= session()->getFlashdata('error') ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-4">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> SIMAGANG &bull; Portal Penilaian Praktikan Magang oleh Instansi Mitra.
        </div>
    </footer>
</body>
</html>

