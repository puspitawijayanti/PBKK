<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LoanModel;
use App\Services\LoanService;
use CodeIgniter\HTTP\RedirectResponse;
use RuntimeException;

class LoanController extends BaseController
{
    public function index(): string
    {
        $loans = model(LoanModel::class);
        $keyword = trim((string) $this->request->getGet('q'));
        $status = (string) $this->request->getGet('status');
        $counts = $loans->countByStatus();
        $query = $loans->search($keyword, $status);

        return view('admin/loans/index', [
            'title'   => 'Data Peminjaman',
            'loans'   => $query->paginate(10),
            'pager'   => $query->pager,
            'counts'  => $counts,
            'keyword' => $keyword,
            'status'  => $status,
        ]);
    }

    public function updateStatus(int $id): RedirectResponse
    {
        $targetStatus = (string) $this->request->getPost('status');

        if (! in_array($targetStatus, LoanModel::STATUSES, true)) {
            return redirect()->back()->with('error', 'Status tujuan tidak valid.');
        }

        try {
            (new LoanService())->changeStatus($id, $targetStatus);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('success', 'Status peminjaman #' . $id . ' diubah menjadi "' . loan_status_label($targetStatus) . '".');
    }
}
