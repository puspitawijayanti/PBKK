<?php

namespace App\Controllers;

use App\Models\LecturerModel;
use App\Models\StudentModel;
use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseController extends Controller
{
    protected function currentUserId(): int
    {
        return (int) session('user_id');
    }

    protected function backWithErrors(array $errors): RedirectResponse
    {
        return redirect()->back()->withInput()->with('errors', $errors);
    }

    protected function forbidden(string $message): ResponseInterface
    {
        return $this->response->setStatusCode(403)->setBody(view('errors/forbidden', ['message' => $message]));
    }

    protected function currentLecturer(): array
    {
        $lecturer = model(LecturerModel::class)->findByUserId($this->currentUserId());

        if ($lecturer === null) {
            throw PageNotFoundException::forPageNotFound('Profil dosen untuk akun ini belum dibuat.');
        }

        return $lecturer;
    }

    protected function currentStudent(): array
    {
        $student = model(StudentModel::class)->findByUserId($this->currentUserId());

        if ($student === null) {
            throw PageNotFoundException::forPageNotFound('Profil mahasiswa untuk akun ini belum dibuat.');
        }

        return $student;
    }
}
