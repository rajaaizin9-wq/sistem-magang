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

        // Jika role user tidak ada di dalam daftar argumen role yang diizinkan
        if (!in_array($userRole, $arguments)) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
