<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userRole = session()->get('role');

        if (empty($arguments)) {
            return;
        }

        $isWadek = ($userRole === 'wadek1' || session()->get('is_wadek') === true);

        // Sinkronisasi active_role otomatis jika user adalah dosen atau wadek
        if ($userRole === 'dosen' || $isWadek) {
            if (!session()->has('active_role')) {
                session()->set('active_role', $userRole === 'wadek1' ? 'wadek1' : 'dosen_pembimbing');
            }

            $currentUri = uri_string();
            if (str_starts_with($currentUri, 'dosen-pembimbing') && session()->get('active_role') !== 'dosen_pembimbing') {
                session()->set('active_role', 'dosen_pembimbing');
            } elseif (str_starts_with($currentUri, 'dosen-mk') && session()->get('active_role') !== 'dosen_mk') {
                session()->set('active_role', 'dosen_mk');
            } elseif ($isWadek && str_starts_with($currentUri, 'wadek1') && session()->get('active_role') !== 'wadek1') {
                session()->set('active_role', 'wadek1');
            }
        }

        $activeRole = session()->get('active_role') ?? $userRole;

        // Pengecekan otorisasi role
        $hasAccess = in_array($userRole, $arguments) || in_array($activeRole, $arguments);

        // Jika user adalah wadek, akun memiliki akses dosen dan wadek 1
        if (!$hasAccess && $isWadek) {
            $allowedRoles = ['wadek1', 'dosen', 'dosen_pembimbing', 'dosen_mk'];
            if (!empty(array_intersect($arguments, $allowedRoles))) {
                $hasAccess = true;
            }
        }

        // Jika role user maupun active_role tidak ada di dalam daftar argumen role yang diizinkan
        if (!$hasAccess) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
