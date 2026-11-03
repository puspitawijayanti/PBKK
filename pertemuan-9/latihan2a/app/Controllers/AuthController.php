<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function home(): RedirectResponse
    {
        return redirect()->to(session('user_role') ?? 'login');
    }

    public function loginForm(): ResponseInterface|string
    {
        if (session('user_role') !== null) {
            return redirect()->to(session('user_role'));
        }

        return view('auth/login', [
            'title'  => 'Masuk',
            'config' => config('Academic'),
        ]);
    }

    public function login(): RedirectResponse
    {
        $rules = [
            'username' => ['rules' => 'required', 'errors' => ['required' => 'NIM / NIDN / username wajib diisi.']],
            'password' => ['rules' => 'required', 'errors' => ['required' => 'Password wajib diisi.']],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('login')->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = model(UserModel::class)->where('username', trim((string) $this->request->getPost('username')))->first();

        if ($user === null || ! password_verify((string) $this->request->getPost('password'), $user['password'])) {
            return redirect()->to('login')->withInput()->with('error', 'Username atau password salah.');
        }

        session()->regenerate(true);
        session()->set([
            'user_id'   => (int) $user['id'],
            'user_name' => $user['name'],
            'user_role' => $user['role'],
        ]);

        $homeUrl = site_url($user['role']);
        $target = (string) session('redirect_url');
        session()->remove('redirect_url');

        if (! str_starts_with($target, $homeUrl)) {
            $target = $homeUrl;
        }

        return redirect()->to($target)->with('success', 'Selamat datang, ' . $user['name'] . '!');
    }

    public function logout(): RedirectResponse
    {
        session()->remove(['user_id', 'user_name', 'user_role', 'redirect_url']);
        session()->regenerate(true);

        return redirect()->to('login')->with('success', 'Anda telah keluar.');
    }
}
