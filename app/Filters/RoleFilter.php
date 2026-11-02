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

        // Sinkronisasi active_role otomatis jika user adalah dosen
        if ($userRole === 'dosen') {
            if (!session()->has('active_role')) {
                session()->set('active_role', 'dosen_pembimbing');
            }

            $currentUri = uri_string();
            if (str_starts_with($currentUri, 'dosen-pembimbing') && session()->get('active_role') !== 'dosen_pembimbing') {
                session()->set('active_role', 'dosen_pembimbing');
            } elseif (str_starts_with($currentUri, 'dosen-mk') && session()->get('active_role') !== 'dosen_mk') {
                session()->set('active_role', 'dosen_mk');
            }
        }

        $activeRole = session()->get('active_role') ?? $userRole;

        // Jika role user maupun active_role tidak ada di dalam daftar argumen role yang diizinkan
        if (!in_array($userRole, $arguments) && !in_array($activeRole, $arguments)) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
