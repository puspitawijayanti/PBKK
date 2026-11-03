<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('user_id') !== null) {
            return null;
        }

        if ($request->getMethod() === 'GET') {
            session()->set('redirect_url', (string) $request->getUri());
        }

        $loginPath = $request->getUri()->getSegment(1) === 'admin' ? 'admin/login' : 'login';

        return redirect()->to($loginPath)->with('error', 'Silakan masuk terlebih dahulu.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
