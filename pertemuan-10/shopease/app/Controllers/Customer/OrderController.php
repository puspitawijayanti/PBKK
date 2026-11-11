<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Libraries\Paginator;
use App\Models\OrderItemModel;
use App\Models\OrderLogModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ShipmentModel;
use App\Services\OrderService;
use App\Services\UploadService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Shop;
use RuntimeException;

/**
 * Riwayat dan detail pesanan customer (FR-017), unggah bukti bayar (FR-014).
 * Customer hanya dapat mengakses pesanan miliknya (BR-006).
 */
class OrderController extends BaseController
{
    public function index(): string
    {
        $status = (string) $this->request->getGet('status');
        $page   = Paginator::paginate(
            model(OrderModel::class)->search(null, $status, null, current_user()['id']),
            config(Shop::class)->adminPerPage,
        );

        return view('customer/orders/index', [
            'title'      => 'Pesanan Saya',
            'orders'     => $page['items'],
            'pagerLinks' => $page['links'],
            'status'     => $status,
        ]);
    }

    public function show(string $orderNumber): string
    {
        $order = $this->ownedOrder($orderNumber);

        return view('customer/orders/show', [
            'title'    => 'Pesanan ' . $order['order_number'],
            'order'    => $order,
            'items'    => model(OrderItemModel::class)->forOrder($order['id']),
            'payment'  => model(PaymentModel::class)->forOrder($order['id']),
            'shipment' => model(ShipmentModel::class)->forOrder($order['id']),
            'logs'     => model(OrderLogModel::class)->forOrder($order['id']),
            'shop'     => config(Shop::class),
        ]);
    }

    public function uploadPayment(string $orderNumber): RedirectResponse
    {
        $order  = $this->ownedOrder($orderNumber);
        $maxKb  = config(Shop::class)->maxProofSizeKb;
        $rules  = ['proof' => [
            'label'  => 'Bukti transfer',
            'rules'  => 'uploaded[proof]|max_size[proof,' . $maxKb . ']|is_image[proof]|mime_in[proof,image/jpeg,image/png,image/webp]|ext_in[proof,jpg,jpeg,png,webp]',
            'errors' => ['uploaded' => 'Pilih file bukti transfer.', 'max_size' => 'Ukuran file maksimal ' . ($maxKb / 1024) . ' MB.', 'is_image' => 'File harus berupa gambar.', 'mime_in' => 'Format yang diizinkan: JPG, PNG, WEBP.', 'ext_in' => 'Format yang diizinkan: JPG, PNG, WEBP.'],
        ]];

        if (! $this->validate($rules)) {
            return redirect()->to(url_to('order.show', $orderNumber))->with('errors', $this->validator->getErrors());
        }

        $fileName = UploadService::storePaymentProof($this->request->getFile('proof'));

        try {
            (new OrderService())->submitPayment($order, current_user()['id'], $fileName);
        } catch (RuntimeException $e) {
            UploadService::deletePaymentProof($fileName);

            return redirect()->to(url_to('order.show', $orderNumber))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('order.show', $orderNumber))->with('success', 'Bukti pembayaran terkirim dan menunggu verifikasi admin.');
    }

    public function proof(string $orderNumber): ResponseInterface
    {
        $order = $this->ownedOrder($orderNumber);

        return UploadService::servePaymentProof($this->response, $order['proof_file']);
    }

    public function cancel(string $orderNumber): RedirectResponse
    {
        $order = $this->ownedOrder($orderNumber);

        if ($order['order_status'] !== 'pending_payment') {
            return redirect()->to(url_to('order.show', $orderNumber))->with('error', 'Pesanan hanya dapat dibatalkan sebelum pembayaran dikirim.');
        }

        try {
            (new OrderService())->cancel($order, current_user()['id'], 'Dibatalkan oleh customer.');
        } catch (RuntimeException $e) {
            return redirect()->to(url_to('order.show', $orderNumber))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('order.show', $orderNumber))->with('success', 'Pesanan dibatalkan.');
    }

    public function complete(string $orderNumber): RedirectResponse
    {
        $order = $this->ownedOrder($orderNumber);

        try {
            (new OrderService())->complete($order, current_user()['id'], 'Pesanan diterima customer.');
        } catch (RuntimeException $e) {
            return redirect()->to(url_to('order.show', $orderNumber))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('order.show', $orderNumber))->with('success', 'Terima kasih! Pesanan ditandai selesai.');
    }

    private function ownedOrder(string $orderNumber): array
    {
        $order = model(OrderModel::class)->findOwnedByNumber($orderNumber, current_user()['id']);

        if ($order === null) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        }

        return $order;
    }
}
