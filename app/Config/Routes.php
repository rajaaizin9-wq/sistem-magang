<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', function() {
    return session()->get('isLoggedIn') ? redirect()->to('/dashboard') : redirect()->to('/login');
});

// Authentication Routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// Protected Routes (Memerlukan Filter Auth)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Modul 2 & 4: Mahasiswa (Role: mahasiswa)
    $routes->group('mahasiswa', ['filter' => 'role:mahasiswa'], static function ($routes) {
        // Pendaftaran
        $routes->get('pendaftaran', 'Mahasiswa\Pendaftaran::index');
        $routes->get('pendaftaran/baru', 'Mahasiswa\Pendaftaran::create');
        $routes->post('pendaftaran/simpan', 'Mahasiswa\Pendaftaran::store');
        $routes->get('pendaftaran/detail/(:num)', 'Mahasiswa\Pendaftaran::detail/$1');

        // Modul 4: Konversi MK Pilihan
        $routes->get('konversi', 'Mahasiswa\Konversi::index');
        $routes->get('konversi/baru', 'Mahasiswa\Konversi::create');
        $routes->post('konversi/simpan', 'Mahasiswa\Konversi::store');
        $routes->get('konversi/detail/(:num)', 'Mahasiswa\Konversi::detail/$1');

        // Modul 5: Jurnal & Logbook Aktivitas (Opsional & Pemantauan)
        $routes->get('logbook', 'Mahasiswa\Logbook::index');
        $routes->post('logbook/simpan', 'Mahasiswa\Logbook::store');
        $routes->post('logbook/hapus/(:num)', 'Mahasiswa\Logbook::delete/$1');
        $routes->get('logbook/file/(:num)', 'Mahasiswa\Logbook::serveAttachment/$1');
    });

    // Modul 2: TU Verifikasi Dokumen (Role: tu)
    $routes->group('tu', ['filter' => 'role:tu'], static function ($routes) {
        $routes->get('verifikasi-dokumen', 'Tu\VerifikasiDokumen::index');
        $routes->get('verifikasi-dokumen/detail/(:num)', 'Tu\VerifikasiDokumen::detail/$1');
        $routes->post('verifikasi-dokumen/proses/(:num)', 'Tu\VerifikasiDokumen::process/$1');
        $routes->get('verifikasi-dokumen/file/(:num)', 'Tu\VerifikasiDokumen::serveDocument/$1');
    });

    // Modul 3 & 4: Kaprodi (Role: kaprodi)
    $routes->group('kaprodi', ['filter' => 'role:kaprodi'], static function ($routes) {
        $routes->get('penentuan-pembimbing', 'Kaprodi\PenentuanPembimbing::index');
        $routes->get('penentuan-pembimbing/detail/(:num)', 'Kaprodi\PenentuanPembimbing::detail/$1');
        $routes->post('penentuan-pembimbing/assign/(:num)', 'Kaprodi\PenentuanPembimbing::assign/$1');

        // Modul 4: Monitoring Konversi
        $routes->get('konversi', 'Kaprodi\Konversi::index');
        $routes->get('konversi/detail/(:num)', 'Kaprodi\Konversi::detail/$1');
    });

    // Modul 3, 4 & 5: Dosen Pembimbing (Role: dosen / dosen_pembimbing)
    $routes->group('dosen-pembimbing', ['filter' => 'role:dosen,dosen_pembimbing'], static function ($routes) {
        $routes->get('pemantauan-logbook', 'DosenPembimbing\Logbook::index');
        $routes->get('pemantauan-logbook/detail/(:num)', 'DosenPembimbing\Logbook::detail/$1');
        $routes->post('pemantauan-logbook/catatan/(:num)', 'DosenPembimbing\Logbook::addNote/$1');
        $routes->get('pemantauan-logbook/file/(:num)', 'DosenPembimbing\Logbook::serveAttachment/$1');
        $routes->post('respon-penugasan/(:num)', 'DosenPembimbing\Logbook::responAssignment/$1');

        // Modul 4: Verifikasi Konversi Mahasiswa Bimbingan
        $routes->get('konversi', 'DosenPembimbing\Konversi::index');
        $routes->get('konversi/detail/(:num)', 'DosenPembimbing\Konversi::detail/$1');
        $routes->post('konversi/proses/(:num)', 'DosenPembimbing\Konversi::process/$1');
    });

    // Modul 4: Dosen Pengampu MK (Role: dosen / dosen_mk)
    $routes->group('dosen-mk', ['filter' => 'role:dosen,dosen_mk,dosen_pembimbing'], static function ($routes) {
        $routes->get('konversi', 'DosenMk\Konversi::index');
        $routes->get('konversi/detail/(:num)', 'DosenMk\Konversi::detail/$1');
        $routes->post('konversi/proses/(:num)', 'DosenMk\Konversi::process/$1');
    });

    // Modul 3 & 4: Wadek 1 (Role: wadek1)
    $routes->group('wadek1', ['filter' => 'role:wadek1'], static function ($routes) {
        $routes->get('pemantauan-magang', 'Wadek1\PemantauanMagang::index');
        $routes->get('pemantauan-magang/detail/(:num)', 'Wadek1\PemantauanMagang::detail/$1');

        // Modul 4: Pengesahan Konversi MK Fakultas
        $routes->get('konversi', 'Wadek1\Konversi::index');
        $routes->get('konversi/detail/(:num)', 'Wadek1\Konversi::detail/$1');
        $routes->post('konversi/proses/(:num)', 'Wadek1\Konversi::process/$1');
    });
});

