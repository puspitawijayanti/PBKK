<?php

namespace App\Controllers;

use App\Libraries\FileUploader;
use App\Libraries\OrderException;
use App\Libraries\OrderService;
use App\Models\OrderModel;
use App\Models\PaymentModel;

/** Sisi customer: unggah bukti pembayaran. Juga melayani tampilan bukti secara aman. */
class PaymentController extends BaseController
{
    public function upload(int $orderId)
    {
        $order = (new OrderModel())->find($orderId);
        if (! $order || (int) $order['user_id'] !== $this->userId()) {
            return $this->notFound('Pesanan tidak ditemukan.');
        }

        $cfg   = config('Shop');
        $rules = [
            'proof' => [
                'label' => 'Bukti pembayaran',
                'rules' => 'uploaded[proof]|is_image[proof]'
                    . '|mime_in[proof,' . implode(',', $cfg->imageMimes) . ']'
                    . '|ext_in[proof,' . implode(',', $cfg->imageExts) . ']'
                    . '|max_size[proof,' . $cfg->maxUploadKb . ']',
            ],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $file = $this->request->getFile('proof');
        $name = FileUploader::savePaymentProof($file); // nama acak, di luar folder public

        try {
            $old = (new OrderService())->submitPayment($orderId, $this->userId(), $name);
        } catch (OrderException $e) {
            FileUploader::deletePaymentProof($name);

            return redirect()->back()->with('error', $e->getMessage());
        }

        FileUploader::deletePaymentProof($old);

        return redirect()->to('/orders/' . $orderId)
            ->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }

    /** Tampilkan bukti pembayaran: hanya pemilik pesanan atau admin. */
    public function proof(int $orderId)
    {
        $order   = (new OrderModel())->find($orderId);
        $payment = (new PaymentModel())->where('order_id', $orderId)->first();

        if (! $order || ! $payment || empty($payment['proof_file'])) {
            return $this->notFound('Bukti pembayaran tidak ditemukan.');
        }

        $isOwner = (int) $order['user_id'] === $this->userId();
        if (! $isOwner && $this->role() !== 'admin') {
            return $this->notFound('Bukti pembayaran tidak ditemukan.');
        }

        $path = FileUploader::paymentProofPath($payment['proof_file']);
        if (! is_file($path)) {
            return $this->notFound('File bukti tidak ditemukan.');
        }

        return $this->response
            ->setContentType((new \finfo(FILEINFO_MIME_TYPE))->file($path) ?: 'application/octet-stream')
            ->setHeader('Cache-Control', 'private, max-age=0, no-store')
            ->setBody(file_get_contents($path));
    }
}
