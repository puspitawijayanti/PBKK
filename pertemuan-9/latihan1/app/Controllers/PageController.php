<?php

namespace App\Controllers;

use App\Models\MessageModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Site;

class PageController extends BaseController
{
    public function about(): string
    {
        return view('pages/about', [
            'title' => 'Tentang Kami',
            'site'  => config(Site::class),
        ]);
    }

    public function contact(): string
    {
        return view('pages/contact', [
            'title' => 'Kontak',
            'site'  => config(Site::class),
        ]);
    }

    public function sendMessage(): RedirectResponse
    {
        $messages = model(MessageModel::class);
        $data = $this->request->getPost(['name', 'email', 'subject', 'body']);

        if (! $messages->insert($data)) {
            return $this->backWithErrors($messages->errors());
        }

        return redirect()->to('contact')->with('success', 'Terima kasih, pesan Anda sudah kami terima.');
    }
}
