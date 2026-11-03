<?php

namespace App\Controllers\Borrower;

use App\Controllers\BaseController;
use App\Models\LoanModel;
use App\Models\ToolModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Site;

class LoanController extends BaseController
{
    private LoanModel $loans;

    public function __construct()
    {
        $this->loans = model(LoanModel::class);
    }

    public function index(): string
    {
        $status = (string) $this->request->getGet('status');
        $query = $this->loans->search(null, $status)->where('loans.user_id', $this->currentUserId());

        return view('borrower/loans/index', [
            'title'  => 'Riwayat Peminjaman',
            'loans'  => $query->paginate(10),
            'pager'  => $query->pager,
            'status' => $status,
        ]);
    }

    public function new(int $toolId): ResponseInterface|string
    {
        $tool = model(ToolModel::class)->withCategory()->where('tools.id', $toolId)->first();

        if ($tool === null) {
            throw PageNotFoundException::forPageNotFound('Alat tidak ditemukan.');
        }

        if ((int) $tool['stock'] < 1) {
            return redirect()->to('tools/' . $tool['slug'])->with('error', 'Stok alat sedang kosong, belum dapat diajukan.');
        }

        return view('borrower/loans/form', [
            'title' => 'Ajukan Peminjaman',
            'tool'  => $tool,
            'site'  => config(Site::class),
        ]);
    }

    public function create(): RedirectResponse
    {
        $site = config(Site::class);
        $today = date('Y-m-d');
        $rules = [
            'tool_id' => [
                'rules'  => 'required|is_natural_no_zero|is_not_unique[tools.id]',
                'errors' => [
                    'required'           => 'Alat wajib dipilih.',
                    'is_natural_no_zero' => 'Alat tidak valid.',
                    'is_not_unique'      => 'Alat tidak ditemukan.',
                ],
            ],
            'quantity' => [
                'rules'  => 'required|is_natural_no_zero|less_than_equal_to[' . $site->maxLoanQuantity . ']',
                'errors' => [
                    'required'           => 'Jumlah wajib diisi.',
                    'is_natural_no_zero' => 'Jumlah minimal 1 unit.',
                    'less_than_equal_to' => 'Jumlah maksimal ' . $site->maxLoanQuantity . ' unit per pengajuan.',
                ],
            ],
            'borrow_date' => [
                'rules'  => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required'   => 'Tanggal pinjam wajib diisi.',
                    'valid_date' => 'Format tanggal pinjam tidak valid.',
                ],
            ],
            'return_date' => [
                'rules'  => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required'   => 'Tanggal kembali wajib diisi.',
                    'valid_date' => 'Format tanggal kembali tidak valid.',
                ],
            ],
            'note' => [
                'rules'  => 'required|min_length[10]|max_length[500]',
                'errors' => [
                    'required'   => 'Keperluan peminjaman wajib diisi.',
                    'min_length' => 'Keperluan minimal 10 karakter.',
                    'max_length' => 'Keperluan maksimal 500 karakter.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return $this->backWithErrors($this->validator->getErrors());
        }

        $data = $this->validator->getValidated();
        $loanDays = (strtotime($data['return_date']) - strtotime($data['borrow_date'])) / 86400;

        if ($data['borrow_date'] < $today) {
            return $this->backWithErrors(['borrow_date' => 'Tanggal pinjam tidak boleh sebelum hari ini.']);
        }

        if ($loanDays < 0) {
            return $this->backWithErrors(['return_date' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.']);
        }

        if ($loanDays > $site->maxLoanDays) {
            return $this->backWithErrors(['return_date' => 'Lama peminjaman maksimal ' . $site->maxLoanDays . ' hari.']);
        }

        $tool = model(ToolModel::class)->find($data['tool_id']);

        if ((int) $data['quantity'] > (int) $tool['stock']) {
            return $this->backWithErrors(['quantity' => 'Jumlah melebihi stok tersedia (' . $tool['stock'] . ' unit).']);
        }

        $loanId = $this->loans->insert([
            'user_id'     => $this->currentUserId(),
            'tool_id'     => (int) $data['tool_id'],
            'quantity'    => (int) $data['quantity'],
            'borrow_date' => $data['borrow_date'],
            'return_date' => $data['return_date'],
            'status'      => 'pending',
            'note'        => trim($data['note']),
        ]);

        return redirect()->to('borrower/loans/' . $loanId)->with('success', 'Pengajuan peminjaman berhasil dikirim dan menunggu persetujuan laboran.');
    }

    public function show(int $id): string
    {
        return view('borrower/loans/show', [
            'title' => 'Detail Peminjaman #' . $id,
            'loan'  => $this->findOwnedLoan($id),
        ]);
    }

    public function cancel(int $id): RedirectResponse
    {
        $loan = $this->findOwnedLoan($id);

        if ($loan['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan yang masih menunggu persetujuan yang dapat dibatalkan.');
        }

        $this->loans->where('id', $id)->where('user_id', $this->currentUserId())->where('status', 'pending')->delete();

        return redirect()->to('borrower/loans')->with('success', 'Pengajuan peminjaman ' . $loan['tool_name'] . ' dibatalkan.');
    }

    private function findOwnedLoan(int $id): array
    {
        $loan = $this->loans->findOwned($id, $this->currentUserId());

        if ($loan === null) {
            throw PageNotFoundException::forPageNotFound('Data peminjaman tidak ditemukan.');
        }

        return $loan;
    }
}
