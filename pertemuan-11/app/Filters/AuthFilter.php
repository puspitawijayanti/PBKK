<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter autentikasi + otorisasi berbasis role.
 *
 * Pemakaian pada route:
 *   ['filter' => 'auth']                 -> harus login (role apa pun)
 *   ['filter' => 'auth:admin']           -> hanya admin
 *   ['filter' => 'auth:admin,operator']  -> admin atau operator
 *
 * Role selalu dicek ulang ke database (bukan hanya dari session), sehingga
 * akun yang dinonaktifkan / diubah role-nya langsung berlaku.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('logged_in')) {
            $this->rememberIntendedUrl($request);

            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = (new UserModel())->find((int) $session->get('user_id'));

        if (! $user || (int) $user['is_active'] !== 1) {
            $session->destroy();

            return redirect()->to('/login')->with('error', 'Akun tidak aktif atau tidak ditemukan.');
        }

        // sinkronkan data session dengan database
        $session->set(['role' => $user['role'], 'name' => $user['name']]);

        if (! empty($arguments) && ! in_array($user['role'], $arguments, true)) {
            return service('response')
                ->setStatusCode(403)
                ->setBody(view('errors/forbidden'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function rememberIntendedUrl(RequestInterface $request): void
    {
        $url = strtolower($request->getMethod()) === 'get'
            ? current_url()
            : (string) previous_url();

        session()->set('intended_url', $url);
    }
}
