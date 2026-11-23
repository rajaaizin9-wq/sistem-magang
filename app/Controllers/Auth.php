<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email atau password yang Anda masukkan salah.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password yang Anda masukkan salah.');
        }

        // Set Session Data
        $isWadek    = ($user['role'] === 'wadek1');
        $activeRole = ($user['role'] === 'dosen') ? 'dosen_pembimbing' : $user['role'];

        session()->set([
            'userId'      => $user['id'],
            'userName'    => $user['name'],
            'userEmail'   => $user['email'],
            'role'        => $user['role'],
            'active_role' => $activeRole,
            'is_wadek'    => $isWadek,
            'isLoggedIn'  => true,
        ]);

        return redirect()->to('/dashboard')->with('success', 'Selamat datang kembali, ' . $user['name'] . '!');
    }

    public function switchRole($targetRole)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $baseRole = session()->get('role');
        $isWadek  = ($baseRole === 'wadek1' || session()->get('is_wadek') === true);

        // Hanya dosen dan akun wadek yang dapat melakukan pergantian peran
        if ($baseRole !== 'dosen' && !$isWadek) {
            return redirect()->to('/dashboard');
        }

        $allowedRoles = ['dosen_pembimbing', 'dosen_mk'];
        if ($isWadek) {
            $allowedRoles[] = 'wadek1';
        }

        if (!in_array($targetRole, $allowedRoles)) {
            return redirect()->to('/dashboard')->with('error', 'Peran yang dipilih tidak valid.');
        }

        session()->set('active_role', $targetRole);

        $labels = [
            'wadek1'           => 'Wakil Dekan 1',
            'dosen_pembimbing' => 'Dosen Pembimbing',
            'dosen_mk'         => 'Dosen Pengampu MK',
        ];
        $roleLabel = $labels[$targetRole] ?? $targetRole;

        return redirect()->to('/dashboard')->with('success', 'Berhasil beralih ke peran ' . $roleLabel . '.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
