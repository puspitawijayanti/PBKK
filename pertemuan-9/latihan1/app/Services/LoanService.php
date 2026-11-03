<?php

namespace App\Services;

use App\Models\LoanModel;
use App\Models\ToolModel;
use RuntimeException;
use Throwable;

class LoanService
{
    private LoanModel $loans;
    private ToolModel $tools;

    public function __construct()
    {
        $this->loans = model(LoanModel::class);
        $this->tools = model(ToolModel::class);
    }

    public function changeStatus(int $loanId, string $targetStatus): array
    {
        $loan = $this->loans->find($loanId);

        if ($loan === null) {
            throw new RuntimeException('Data peminjaman tidak ditemukan.');
        }

        if (! LoanModel::canTransition($loan['status'], $targetStatus)) {
            throw new RuntimeException('Perubahan status dari "' . loan_status_label($loan['status']) . '" ke "' . loan_status_label($targetStatus) . '" tidak diizinkan.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            if ($targetStatus === 'approved' && ! $this->tools->decreaseStock((int) $loan['tool_id'], (int) $loan['quantity'])) {
                throw new RuntimeException('Stok alat tidak mencukupi untuk menyetujui peminjaman ini.');
            }

            if ($targetStatus === 'returned') {
                $this->tools->increaseStock((int) $loan['tool_id'], (int) $loan['quantity']);
            }

            $changes = [
                'status'     => $targetStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($targetStatus === 'returned') {
                $changes['returned_at'] = date('Y-m-d H:i:s');
            }

            $db->table('loans')
                ->where('id', $loanId)
                ->where('status', $loan['status'])
                ->update($changes);

            if ($db->affectedRows() !== 1) {
                throw new RuntimeException('Status peminjaman sudah diubah oleh proses lain. Muat ulang halaman.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();

            throw $exception;
        }

        return $this->loans->find($loanId);
    }
}
