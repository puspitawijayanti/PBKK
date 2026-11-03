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
            return redirect()->to(session('user_role') === 'admin' ? 'admin' : '/');
        }

        return view('auth/login', [
            'title'      => 'Masuk',
            'toCheckout' => str_contains((string) session('redirect_url'), 'checkout'),
        ]);
    }

    public function login(): RedirectResponse
    {
        $rules = [
            'email'    => ['rules' => 'required|valid_email', 'errors' => ['required' => 'Email wajib diisi.', 'valid_email' => 'Format email tidak valid.']],
            'password' => ['rules' => 'required', 'errors' => ['required' => 'Password wajib diisi.']],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('login')->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = model(UserModel::class)->where('email', strtolower(trim((string) $this->request->getPost('email'))))->first();

        if ($user === null || ! password_verify((string) $this->request->getPost('password'), $user['password'])) {
            return redirect()->to('login')->withInput()->with('error', 'Email atau password salah.');
        }

        return $this->signIn($user, 'Selamat datang kembali, ' . $user['name'] . '!');
    }

    public function registerForm(): ResponseInterface|string
    {
        if (session('user_role') !== null) {
            return redirect()->to('/');
        }

        return view('auth/register', ['title' => 'Daftar Akun']);
    }

    public function register(): RedirectResponse
    {
        $rules = [
            'name' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => ['required' => 'Nama wajib diisi.', 'min_length' => 'Nama minimal 3 karakter.', 'max_length' => 'Nama maksimal 100 karakter.'],
            ],
            'email' => [
                'rules'  => 'required|valid_email|max_length[120]|is_unique[users.email]',
                'errors' => ['required' => 'Email wajib diisi.', 'valid_email' => 'Format email tidak valid.', 'is_unique' => 'Email sudah terdaftar. Silakan masuk.'],
            ],
            'phone' => [
                'rules'  => 'required|regex_match[/^(\+62|0)[0-9]{8,13}$/]',
                'errors' => ['required' => 'Nomor HP wajib diisi.', 'regex_match' => 'Nomor HP harus diawali 0 atau +62 dan berisi 9-14 digit.'],
            ],
            'password' => [
                'rules'  => 'required|min_length[8]|max_length[72]',
                'errors' => ['required' => 'Password wajib diisi.', 'min_length' => 'Password minimal 8 karakter.', 'max_length' => 'Password maksimal 72 karakter.'],
            ],
            'password_confirm' => [
                'rules'  => 'required|matches[password]',
                'errors' => ['required' => 'Konfirmasi password wajib diisi.', 'matches' => 'Konfirmasi password tidak sama.'],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('register')->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->validator->getValidated();
        $users = model(UserModel::class);
        $userId = $users->insert([
            'name'     => trim($data['name']),
            'email'    => strtolower(trim($data['email'])),
            'phone'    => $data['phone'],
            'password' => $data['password'],
            'role'     => 'buyer',
        ]);

        return $this->signIn($users->find($userId), 'Akun berhasil dibuat. Selamat berbelanja, ' . $data['name'] . '!');
    }

    public function logout(): RedirectResponse
    {
        session()->remove(['user_id', 'user_name', 'user_role', 'redirect_url', 'cart']);
        session()->regenerate(true);

        return redirect()->to('login')->with('success', 'Anda telah keluar.');
    }

    private function signIn(array $user, string $message): RedirectResponse
    {
        session()->regenerate(true);
        session()->set([
            'user_id'   => (int) $user['id'],
            'user_name' => $user['name'],
            'user_role' => $user['role'],
        ]);

        $target = (string) session('redirect_url');
        session()->remove('redirect_url');

        $allowed = $user['role'] === 'admin'
            ? str_starts_with($target, site_url('admin'))
            : str_starts_with($target, site_url()) && ! str_starts_with($target, site_url('admin'));

        if (! $allowed) {
            $target = $user['role'] === 'admin' ? site_url('admin') : site_url('/');
        }

        return redirect()->to($target)->with('success', $message);
    }
}
