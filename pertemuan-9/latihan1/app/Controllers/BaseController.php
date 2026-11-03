<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;

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
}
