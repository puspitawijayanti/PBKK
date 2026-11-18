<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    /** Hash dummy agar waktu respon login sama baik email ada maupun tidak. */
    private const DUMMY_HASH = '$2y$10$Wjvc3waT83jZt0CCozLkle8mqetJPuuNeq7hvHFIyn6hdfz4zcEoK';

    public function login()
    {
        if (is_logged_in()) {
            return redirect()->to($this->homeFor(current_role()));
        }

        return view('auth/login', ['title' => 'Masuk']);
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => ['label' => 'Email', 'rules' => 'required|valid_email'],
            'password' => ['label' => 'Password', 'rules' => 'required'],
        ];
        if (! $this->validate($rules)) {
            return $this->backWithErrors();
        }

        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        // batasi percobaan login (anti brute-force): 8 percobaan per menit per IP+email
        $throttler = service('throttler');
        $key       = 'login_' . md5($this->request->getIPAddress() . '|' . $email);
        if ($throttler->check($key, 8, MINUTE) === false) {
            return redirect()->back()->withInput()
                ->with('error', 'Terlalu banyak percobaan login. Coba lagi dalam ' . $throttler->getTokenTime() . ' detik.');
        }

        $user = (new UserModel())->where('email', $email)->first();

        // password_verify selalu dijalankan; pesan error tidak membocorkan apakah email terdaftar
        $valid = password_verify($password, $user['password'] ?? self::DUMMY_HASH);

        if (! $user || ! $valid || (int) $user['is_active'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        // cegah session fixation
        session()->regenerate();
        session()->set([
            'user_id'   => (int) $user['id'],
            'name'      => $user['name'],
            'role'      => $user['role'],
            'logged_in' => true,
        ]);

        $intended = session()->get('intended_url');
        session()->remove('intended_url');

        // admin/operator -> dashboard; customer -> halaman yang dituju sebelumnya
        if ($user['role'] === 'customer' && $intended && str_starts_with($intended, base_url())) {
            return redirect()->to($intended)->with('success', 'Selamat datang kembali, ' . $user['name'] . '!');
        }

        return redirect()->to($this->homeFor($user['role']))
            ->with('success', 'Selamat datang, ' . $user['name'] . '!');
    }

    public function register()
    {
        if (is_logged_in()) {
            return redirect()->to($this->homeFor(current_role()));
        }

        return view('auth/register', ['title' => 'Daftar Akun']);
    }

    public function attemptRegister()
    {
        $rules = [
            'name'             => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
            'email'            => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[100]|is_unique[users.email]'],
            'phone'            => ['label' => 'No. telepon', 'rules' => 'permit_empty|regex_match[/^[0-9+\- ]{8,20}$/]'],
            'password'         => [
                'label'  => 'Password',
                'rules'  => 'required|min_length[8]|max_length[72]|regex_match[/^(?=.*[A-Za-z])(?=.*\d).+$/]',
                'errors' => ['regex_match' => 'Password harus mengandung huruf dan angka.'],
            ],
            'password_confirm' => ['label' => 'Konfirmasi password', 'rules' => 'required|matches[password]'],
        ];

        if (! $this->validate($rules)) {
            return $this->backWithErrors();
        }

        (new UserModel())->insert([
            'name'      => trim((string) $this->request->getPost('name')),
            'email'     => strtolower(trim((string) $this->request->getPost('email'))),
            'phone'     => $this->request->getPost('phone') ?: null,
            'password'  => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'      => 'customer', // registrasi publik selalu customer
            'is_active' => 1,
        ]);

        return redirect()->to('/login')->with('success', 'Akun berhasil dibuat. Silakan login.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }

    private function homeFor(?string $role): string
    {
        return in_array($role, ['admin', 'operator'], true) ? '/admin' : '/';
    }
}
