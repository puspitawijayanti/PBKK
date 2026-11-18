<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController: tempat fungsi yang dipakai bersama oleh semua controller.
 * Helper (url, form, text, shopease) dimuat global lewat Config\Autoload.
 */
abstract class BaseController extends Controller
{
    /**
     * @param CLIRequest|IncomingRequest $request
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /** ID user yang sedang login (atau 0). */
    protected function userId(): int
    {
        return (int) session()->get('user_id');
    }

    protected function role(): string
    {
        return (string) session()->get('role');
    }

    /** Alihkan kembali dengan input + daftar error validasi. */
    protected function backWithErrors(?array $errors = null)
    {
        return redirect()->back()->withInput()
            ->with('errors', $errors ?? $this->validator->getErrors());
    }

    /** Kembalikan 404 yang konsisten (dipakai juga agar tidak membocorkan data milik orang lain). */
    protected function notFound(string $message = 'Halaman tidak ditemukan.')
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound($message);
    }
}
