<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\AccountService;
use CodeIgniter\HTTP\RedirectResponse;
use RuntimeException;

class UserController extends BaseController
{
    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $role = (string) $this->request->getGet('role');
        $query = model(UserModel::class)->orderBy('role')->orderBy('username');

        if ($keyword !== '') {
            $query->groupStart()->like('name', $keyword)->orLike('username', $keyword)->groupEnd();
        }

        if (in_array($role, UserModel::ROLES, true)) {
            $query->where('role', $role);
        }

        return view('admin/users/index', [
            'title'   => 'Akun Pengguna',
            'users'   => $query->paginate(15),
            'pager'   => $query->pager,
            'keyword' => $keyword,
            'role'    => $role,
        ]);
    }

    public function resetPassword(int $id): RedirectResponse
    {
        try {
            $user = (new AccountService())->resetPassword($id);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('success', 'Password ' . $user['name'] . ' direset menjadi username (' . $user['username'] . ').');
    }
}
