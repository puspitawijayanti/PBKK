<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function loginForm(): ResponseInterface|string
    {
        if (session('user_role') !== null) {
            return redirect()->to(session('user_role'));
        }

        return view('auth/login', ['title' => 'Masuk Mahasiswa']);
    }

    public function adminLoginForm(): ResponseInterface|string
    {
        if (session('user_role') === 'admin') {
            return redirect()->to('admin');
        }

        return view('auth/admin_login', ['title' => 'Masuk Laboran']);
    }

    public function login(): RedirectResponse
    {
        return $this->attempt('borrower', 'login');
    }

    public function adminLogin(): RedirectResponse
    {
        return $this->attempt('admin', 'admin/login');
    }

    public function logout(): RedirectResponse
    {
        $loginPath = session('user_role') === 'admin' ? 'admin/login' : 'login';

        session()->remove(['user_id', 'user_name', 'user_role', 'redirect_url']);
        session()->regenerate(true);

        return redirect()->to($loginPath)->with('success', 'Anda telah keluar.');
    }

    private function attempt(string $role, string $loginPath): RedirectResponse
    {
        $rules = [
            'username' => ['rules' => 'required', 'errors' => ['required' => 'Username wajib diisi.']],
            'password' => ['rules' => 'required', 'errors' => ['required' => 'Password wajib diisi.']],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($loginPath)->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = model(UserModel::class)->where('username', $this->request->getPost('username'))->first();

        if ($user === null || ! password_verify((string) $this->request->getPost('password'), $user['password'])) {
            return redirect()->to($loginPath)->withInput()->with('error', 'Username atau password salah.');
        }

        if ($user['role'] !== $role) {
            $otherPage = $user['role'] === 'admin' ? 'halaman masuk laboran' : 'halaman masuk mahasiswa';

            return redirect()->to($loginPath)->withInput()->with('error', 'Akun ini bukan akun ' . role_label($role) . '. Silakan gunakan ' . $otherPage . '.');
        }

        session()->regenerate(true);
        session()->set([
            'user_id'   => (int) $user['id'],
            'user_name' => $user['name'],
            'user_role' => $user['role'],
        ]);

        $homeUrl = site_url($role);
        $target = (string) session('redirect_url');
        session()->remove('redirect_url');

        if (! str_starts_with($target, $homeUrl)) {
            $target = $homeUrl;
        }

        return redirect()->to($target)->with('success', 'Selamat datang, ' . $user['name'] . '!');
    }
}
