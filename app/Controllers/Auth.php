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
        session()->set([
            'userId'     => $user['id'],
            'userName'   => $user['name'],
            'userEmail'  => $user['email'],
            'role'       => $user['role'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to('/dashboard')->with('success', 'Selamat datang kembali, ' . $user['name'] . '!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
