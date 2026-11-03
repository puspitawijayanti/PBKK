<?php

namespace App\Controllers\Borrower;

use App\Controllers\BaseController;
use App\Models\LoanModel;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $loans = model(LoanModel::class);
        $userId = $this->currentUserId();

        return view('borrower/dashboard', [
            'title'       => 'Dashboard Mahasiswa',
            'counts'      => $loans->countByStatus($userId),
            'activeLoans' => $loans->withRelations()
                ->where('loans.user_id', $userId)
                ->whereIn('loans.status', ['pending', 'approved', 'borrowed'])
                ->orderBy('loans.return_date')
                ->findAll(),
        ]);
    }
}
