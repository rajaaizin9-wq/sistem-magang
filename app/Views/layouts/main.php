<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SIMAGANG' ?></title>
    <!-- Tailwind CSS (Static / CDN fallback) -->
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
    <style>
        #sidebar::-webkit-scrollbar {
            width: 4px;
        }
        #sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        #sidebar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        #sidebar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Sidebar Navigation -->
        <aside id="sidebar" class="w-full md:w-64 bg-slate-900 text-slate-300 flex-shrink-0 md:sticky md:top-0 md:h-screen md:overflow-y-auto hidden md:flex md:flex-col border-r border-slate-800">
            <!-- Brand -->
            <div class="p-5 border-b border-slate-800 flex items-center justify-between sticky top-0 bg-slate-900 z-10 flex-shrink-0">
                <a href="<?= base_url('dashboard') ?>" class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center font-bold text-white shadow">S</div>
                    <span class="font-bold text-white tracking-tight text-lg">SIMAGANG</span>
                </a>
            </div>

            <!-- Role Badge Banner -->
            <div class="mx-4 my-3 px-3 py-2 bg-slate-800 rounded-lg text-xs flex items-center space-x-2 border border-slate-700/50 flex-shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-slate-300 capitalize font-medium">Role: <?= str_replace('_', ' ', session()->get('role')) ?></span>
            </div>

            <!-- Navigation Links -->
            <?php
            $currentUri = uri_string();
            $getNavItemClass = function(string $routePattern, bool $isDashboard = false) use ($currentUri): string {
                $isActive = false;
                if ($isDashboard) {
                    $isActive = ($currentUri === 'dashboard' || $currentUri === '' || $currentUri === '/');
                } else {
                    $isActive = url_is($routePattern . '*') || str_starts_with($currentUri, $routePattern);
                }

                if ($isActive) {
                    return 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold shadow-sm';
                }
                return 'text-slate-300 hover:bg-slate-800 hover:text-white border border-transparent transition';
            };

            $getNavIconClass = function(string $routePattern, bool $isDashboard = false) use ($currentUri): string {
                $isActive = false;
                if ($isDashboard) {
                    $isActive = ($currentUri === 'dashboard' || $currentUri === '' || $currentUri === '/');
                } else {
                    $isActive = url_is($routePattern . '*') || str_starts_with($currentUri, $routePattern);
                }

                return $isActive ? 'text-blue-400' : 'text-slate-400';
            };
            $role = session()->get('role');
            ?>
            <nav class="p-4 space-y-1 text-sm font-medium">
                <a href="<?= base_url('dashboard') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('dashboard', true) ?>">
                    <svg class="w-5 h-5 <?= $getNavIconClass('dashboard', true) ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

                <?php if ($role === 'mahasiswa'): ?>
                    <a href="<?= base_url('mahasiswa/pendaftaran') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('mahasiswa/pendaftaran') ?>">
                        <svg class="w-5 h-5 <?= $getNavIconClass('mahasiswa/pendaftaran') ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Pendaftaran Magang</span>
                    </a>
                    <a href="<?= base_url('mahasiswa/logbook') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('mahasiswa/logbook') ?>">
                        <svg class="w-5 h-5 <?= $getNavIconClass('mahasiswa/logbook') ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Jurnal & Logbook</span>
                    </a>
                    <a href="<?= base_url('mahasiswa/konversi') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('mahasiswa/konversi') ?>">
                        <svg class="w-5 h-5 <?= $getNavIconClass('mahasiswa/konversi') ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                        <span>Konversi MK Pilihan</span>
                    </a>
                <?php endif; ?>

                <?php if ($role === 'tu'): ?>
                    <a href="<?= base_url('tu/verifikasi-dokumen') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('tu/verifikasi-dokumen') ?>">
                        <svg class="w-5 h-5 <?= $getNavIconClass('tu/verifikasi-dokumen') ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Verifikasi Dokumen TU</span>
                    </a>
                <?php endif; ?>

                <?php if ($role === 'kaprodi'): ?>
                    <a href="<?= base_url('kaprodi/penentuan-pembimbing') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('kaprodi/penentuan-pembimbing') ?>">
                        <svg class="w-5 h-5 <?= $getNavIconClass('kaprodi/penentuan-pembimbing') ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Penentuan Pembimbing</span>
                    </a>
                    <a href="<?= base_url('kaprodi/konversi') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('kaprodi/konversi') ?>">
                        <svg class="w-5 h-5 <?= $getNavIconClass('kaprodi/konversi') ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                        <span>Pemantauan Konversi</span>
                    </a>
                <?php endif; ?>

                <?php if (in_array($role, ['dosen', 'dosen_pembimbing', 'dosen_mk'])): ?>
                    <!-- Section: Peran Dosen Pembimbing / Pemantau -->
                    <div class="pt-3 pb-1">
                        <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Peran: Pemantau</span>
                    </div>
                    <a href="<?= base_url('dosen-pembimbing/pemantauan-logbook') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('dosen-pembimbing/pemantauan-logbook') ?>">
                        <svg class="w-5 h-5 <?= $getNavIconClass('dosen-pembimbing/pemantauan-logbook') ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Pemantauan Logbook</span>
                    </a>
                    <a href="<?= base_url('dosen-pembimbing/konversi') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('dosen-pembimbing/konversi') ?>">
                        <svg class="w-5 h-5 <?= $getNavIconClass('dosen-pembimbing/konversi') ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Verifikasi Konversi</span>
                    </a>

                    <!-- Section: Peran Dosen Pengampu MK -->
                    <div class="pt-3 pb-1">
                        <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Peran: Pengampu MK</span>
                    </div>
                    <a href="<?= base_url('dosen-mk/konversi') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('dosen-mk/konversi') ?>">
                        <svg class="w-5 h-5 <?= $getNavIconClass('dosen-mk/konversi') ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Verifikasi MK Pengampu</span>
                    </a>
                <?php endif; ?>

                <?php if ($role === 'wadek1'): ?>
                    <a href="<?= base_url('wadek1/pemantauan-magang') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('wadek1/pemantauan-magang') ?>">
                        <svg class="w-5 h-5 <?= $getNavIconClass('wadek1/pemantauan-magang') ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Pemantauan Magang</span>
                    </a>
                    <a href="<?= base_url('wadek1/konversi') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg <?= $getNavItemClass('wadek1/konversi') ?>">
                        <svg class="w-5 h-5 <?= $getNavIconClass('wadek1/konversi') ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Validasi Konversi MK</span>
                    </a>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">
            <!-- Top Navbar -->
            <header class="bg-white border-b border-slate-200 px-6 py-3.5 flex items-center justify-between sticky top-0 z-10 shadow-sm">
                <div class="flex items-center space-x-3">
                    <button id="toggleSidebar" class="md:hidden text-slate-500 hover:text-slate-700 p-1.5 rounded-lg border border-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h2 class="text-base font-semibold text-slate-800 hidden sm:block"><?= $title ?? 'SIMAGANG' ?></h2>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button id="userMenuBtn" class="flex items-center space-x-3 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-slate-800 text-white font-bold text-xs flex items-center justify-center">
                                <?= strtoupper(substr(session()->get('userName') ?? 'U', 0, 1)) ?>
                            </div>
                            <span class="text-sm font-medium text-slate-700 hidden sm:inline-block"><?= session()->get('userName') ?></span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-lg shadow-lg py-1 text-sm text-slate-700 z-50">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="font-medium text-slate-900 truncate"><?= session()->get('userName') ?></p>
                                <p class="text-xs text-slate-500 truncate"><?= session()->get('userEmail') ?></p>
                            </div>
                            <a href="<?= base_url('logout') ?>" class="block px-4 py-2 text-red-600 hover:bg-red-50 transition">Keluar (Logout)</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="p-6 flex-1">
                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="mb-5 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-sm rounded-r-lg shadow-sm flex justify-between items-center">
                        <div><?= session()->getFlashdata('success') ?></div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 text-sm rounded-r-lg shadow-sm flex justify-between items-center">
                        <div><?= session()->getFlashdata('error') ?></div>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <!-- Vanilla JS Interactivity -->
    <script>
        // Toggle Sidebar Mobile
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        if (toggleSidebar && sidebar) {
            toggleSidebar.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
            });
        }

        // Toggle User Menu Dropdown
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenuDropdown = document.getElementById('userMenuDropdown');
        if (userMenuBtn && userMenuDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', () => {
                userMenuDropdown.classList.add('hidden');
            });
        }
    </script>
</body>
</html>
