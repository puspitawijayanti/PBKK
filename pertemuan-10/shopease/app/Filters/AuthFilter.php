<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Halaman privat hanya untuk pengguna yang sudah login (FR-004, NFR-003).
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('user_id') !== null) {
            return null;
        }

        // Setelah login, pengguna dikembalikan ke halaman yang tadi ingin dibuka.
        session()->set('redirect_url', $request->getMethod() === 'GET' ? (string) current_url(true) : previous_url());

        return redirect()->to(url_to('login'))->with('info', 'Silakan masuk terlebih dahulu.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
