<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Membatasi rute berdasarkan role, misalnya `role:admin` atau `role:admin,operator`.
 * Request ditolak dengan 403 meskipun URL diakses langsung (PRD 13: Otorisasi).
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $allowedRoles = (array) $arguments;

        if (in_array(session('user_role'), $allowedRoles, true)) {
            return null;
        }

        return service('response')
            ->setStatusCode(403)
            ->setBody(view('errors/forbidden', ['title' => 'Akses Ditolak', 'allowedRoles' => $allowedRoles]));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
