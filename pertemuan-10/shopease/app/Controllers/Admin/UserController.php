<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Paginator;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Shop;

/**
 * Daftar pengguna dan aktivasi akun.
 */
class UserController extends BaseController
{
    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $role    = (string) $this->request->getGet('role');
        $page    = Paginator::paginate(model(UserModel::class)->search($keyword, $role), config(Shop::class)->adminPerPage);

        return view('admin/users/index', [
            'title'      => 'Pengguna',
            'users'      => $page['items'],
            'pagerLinks' => $page['links'],
            'keyword'    => $keyword,
            'role'       => $role,
        ]);
    }

    public function toggle(int $id): RedirectResponse
    {
        $users = model(UserModel::class);
        $user  = $users->find($id) ?? throw PageNotFoundException::forPageNotFound('Pengguna tidak ditemukan.');

        if ($user['id'] === current_user()['id']) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $users->update($id, ['is_active' => ! $user['is_active']]);

        return redirect()->back()->with('success', 'Akun ' . $user['name'] . ($user['is_active'] ? ' dinonaktifkan.' : ' diaktifkan.'));
    }
}
