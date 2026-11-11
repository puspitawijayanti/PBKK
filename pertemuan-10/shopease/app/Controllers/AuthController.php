<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Registrasi, login, dan logout (FR-001, FR-002, FR-003).
 */
class AuthController extends BaseController
{
    public function login(): ResponseInterface|string
    {
        if (current_user() !== null) {
            return redirect()->to($this->homeFor(current_user()['role']));
        }

        return view('auth/login', ['title' => 'Masuk']);
    }

    public function attemptLogin(): RedirectResponse
    {
        $rules = [
            'email'    => ['label' => 'Email', 'rules' => 'required|valid_email'],
            'password' => ['label' => 'Password', 'rules' => 'required'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = model(UserModel::class)->findByEmail((string) $this->request->getPost('email'));

        // Pesan sama untuk email tidak terdaftar maupun password salah (PRD 13: Login).
        if ($user === null || ! password_verify((string) $this->request->getPost('password'), $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        if (! $user['is_active']) {
            return redirect()->back()->withInput()->with('error', 'Akun Anda dinonaktifkan. Hubungi admin toko.');
        }

        return $this->signIn($user, 'Selamat datang kembali, ' . $user['name'] . '!');
    }

    public function register(): ResponseInterface|string
    {
        if (current_user() !== null) {
            return redirect()->to($this->homeFor(current_user()['role']));
        }

        return view('auth/register', ['title' => 'Daftar Akun']);
    }

    public function attemptRegister(): RedirectResponse
    {
        $rules = [
            'name'             => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
            'email'            => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[120]|unique_in[users.email]', 'errors' => ['unique_in' => 'Email sudah terdaftar. Silakan masuk.']],
            'phone'            => ['label' => 'Nomor HP', 'rules' => 'permit_empty|regex_match[/^(\+62|0)[0-9]{8,13}$/]', 'errors' => ['regex_match' => 'Nomor HP harus diawali 0 atau +62 dan berisi 9-14 digit.']],
            'password'         => ['label' => 'Password', 'rules' => 'required|min_length[8]|max_length[72]'],
            'password_confirm' => ['label' => 'Ulangi password', 'rules' => 'required|matches[password]', 'errors' => ['matches' => 'Konfirmasi password tidak sama.']],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data  = $this->validator->getValidated();
        $users = model(UserModel::class);
        $userId = $users->insert([
            'name'     => trim($data['name']),
            'email'    => strtolower(trim($data['email'])),
            'phone'    => ($data['phone'] ?? '') !== '' ? $data['phone'] : null,
            'password' => $data['password'],
            'role'     => 'customer',
        ]);

        return $this->signIn($users->find($userId), 'Akun berhasil dibuat. Selamat berbelanja, ' . trim($data['name']) . '!');
    }

    public function logout(): RedirectResponse
    {
        session()->remove(['user_id', 'user_name', 'user_role', 'redirect_url']);
        session()->regenerate(true);

        return redirect()->to(url_to('home'))->with('success', 'Anda telah keluar.');
    }

    private function signIn(array $user, string $message): RedirectResponse
    {
        // Session ID baru setelah login untuk mencegah session fixation.
        session()->regenerate(true);
        session()->set([
            'user_id'   => (int) $user['id'],
            'user_name' => $user['name'],
            'user_role' => $user['role'],
        ]);

        $target = (string) session('redirect_url');
        session()->remove('redirect_url');

        // Hanya kembali ke URL internal yang boleh diakses role tersebut.
        $isAdminArea = str_starts_with($target, site_url('admin'));
        $allowed     = str_starts_with($target, site_url())
            && ($user['role'] === 'customer' ? ! $isAdminArea : $isAdminArea);

        return redirect()->to($allowed ? $target : $this->homeFor($user['role']))->with('success', $message);
    }

    private function homeFor(string $role): string
    {
        return match ($role) {
            'admin'    => url_to('admin.dashboard'),
            'operator' => url_to('admin.orders'),
            default    => url_to('home'),
        };
    }
}
