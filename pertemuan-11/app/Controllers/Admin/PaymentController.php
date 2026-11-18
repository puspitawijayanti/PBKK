<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\OrderException;
use App\Libraries\OrderService;
use App\Models\PaymentModel;

/** Verifikasi pembayaran: khusus Administrator (BR-007). */
class PaymentController extends BaseController
{
    public function index()
    {
        $status = (string) $this->request->getGet('status');
        if ($status === '') {
            $status = 'submitted'; // default: yang perlu diverifikasi
        }

        $model = new PaymentModel();
        $query = $model->withOrder();
        if ($status !== 'all' && in_array($status, PaymentModel::STATUSES, true)) {
            $query->where('payments.payment_status', $status);
        }

        return view('admin/payments/index', [
            'title'    => 'Verifikasi Pembayaran',
            'payments' => $query->orderBy('payments.updated_at', 'DESC')->paginate(config('Shop')->adminPerPage),
            'pager'    => $model->pager,
            'status'   => $status,
        ]);
    }

    public function verify(int $orderId)
    {
        try {
            (new OrderService())->verifyPayment($orderId, $this->userId());
        } catch (OrderException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pembayaran diverifikasi. Pesanan masuk tahap diproses.');
    }

    public function reject(int $orderId)
    {
        $rules = ['reason' => ['label' => 'Alasan penolakan', 'rules' => 'required|min_length[5]|max_length[200]']];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        try {
            (new OrderService())->rejectPayment($orderId, $this->userId(), trim((string) $this->request->getPost('reason')));
        } catch (OrderException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Bukti pembayaran ditolak. Customer diminta mengunggah ulang.');
    }
}
