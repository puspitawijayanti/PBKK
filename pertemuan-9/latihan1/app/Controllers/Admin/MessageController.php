<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MessageModel;
use CodeIgniter\HTTP\RedirectResponse;

class MessageController extends BaseController
{
    public function index(): string
    {
        $messages = model(MessageModel::class)->orderBy('created_at', 'DESC');

        return view('admin/messages/index', [
            'title'    => 'Pesan Masuk',
            'messages' => $messages->paginate(10),
            'pager'    => $messages->pager,
        ]);
    }

    public function delete(int $id): RedirectResponse
    {
        $messages = model(MessageModel::class);

        if ($messages->find($id) === null) {
            return redirect()->back()->with('error', 'Pesan tidak ditemukan.');
        }

        $messages->delete($id);

        return redirect()->to('admin/messages')->with('success', 'Pesan berhasil dihapus.');
    }
}
