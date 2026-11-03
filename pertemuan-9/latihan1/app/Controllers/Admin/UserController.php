<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LoanModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class UserController extends BaseController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = model(UserModel::class);
    }

    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $role = (string) $this->request->getGet('role');
        $query = $this->users->search($keyword, $role);

        return view('admin/users/index', [
            'title'   => 'Data Pengguna',
            'users'   => $query->paginate(10),
            'pager'   => $query->pager,
            'keyword' => $keyword,
            'role'    => $role,
        ]);
    }

    public function new(): string
    {
        return view('admin/users/form', ['title' => 'Tambah Pengguna', 'user' => null]);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validate($this->passwordRules(true))) {
            return $this->backWithErrors($this->validator->getErrors());
        }

        $data = $this->collectInput();

        if (! $this->users->insert($data)) {
            return $this->backWithErrors($this->users->errors());
        }

        return redirect()->to('admin/users')->with('success', 'Pengguna "' . $data['name'] . '" berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        return view('admin/users/form', ['title' => 'Ubah Pengguna', 'user' => $this->findUser($id)]);
    }

    public function update(int $id): RedirectResponse
    {
        $user = $this->findUser($id);

        if (! $this->validate($this->passwordRules(false))) {
            return $this->backWithErrors($this->validator->getErrors());
        }

        $data = $this->collectInput();
        $data['id'] = $id;

        if ($id === $this->currentUserId() && $data['role'] !== $user['role']) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak dapat mengubah peran akun sendiri.');
        }

        if (! $this->users->save($data)) {
            return $this->backWithErrors($this->users->errors());
        }

        if ($id === $this->currentUserId()) {
            session()->set('user_name', $data['name']);
        }

        return redirect()->to('admin/users')->with('success', 'Pengguna "' . $data['name'] . '" berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $user = $this->findUser($id);

        if ($id === $this->currentUserId()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $activeLoans = model(LoanModel::class)
            ->where('user_id', $id)
            ->whereIn('status', ['approved', 'borrowed'])
            ->countAllResults();

        if ($activeLoans > 0) {
            return redirect()->back()->with('error', 'Pengguna "' . $user['name'] . '" masih memiliki peminjaman aktif.');
        }

        $this->users->delete($id);

        return redirect()->to('admin/users')->with('success', 'Pengguna "' . $user['name'] . '" berhasil dihapus.');
    }

    private function findUser(int $id): array
    {
        $user = $this->users->find($id);

        if ($user === null) {
            throw PageNotFoundException::forPageNotFound('Pengguna tidak ditemukan.');
        }

        return $user;
    }

    private function collectInput(): array
    {
        return [
            'name'     => trim((string) $this->request->getPost('name')),
            'username' => strtolower(trim((string) $this->request->getPost('username'))),
            'role'     => $this->request->getPost('role'),
            'password' => (string) $this->request->getPost('password'),
        ];
    }

    private function passwordRules(bool $required): array
    {
        return [
            'password' => [
                'rules'  => ($required ? 'required' : 'permit_empty') . '|min_length[6]|max_length[72]',
                'errors' => [
                    'required'   => 'Password wajib diisi.',
                    'min_length' => 'Password minimal 6 karakter.',
                    'max_length' => 'Password maksimal 72 karakter.',
                ],
            ],
            'password_confirm' => [
                'rules'  => 'matches[password]',
                'errors' => ['matches' => 'Konfirmasi password tidak sama.'],
            ],
        ];
    }
}
