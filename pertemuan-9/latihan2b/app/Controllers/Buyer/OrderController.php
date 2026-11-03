<?php

namespace App\Controllers\Buyer;

use App\Controllers\BaseController;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Services\OrderService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Shop;
use RuntimeException;

class OrderController extends BaseController
{
    public const PROOF_DIRECTORY = WRITEPATH . 'uploads/payments/';

    public function index(): string
    {
        $status = (string) $this->request->getGet('status');
        $orders = model(OrderModel::class)->where('user_id', $this->currentUserId())->orderBy('created_at', 'DESC');

        if (in_array($status, OrderModel::STATUSES, true)) {
            $orders->where('status', $status);
        }

        return view('buyer/orders/index', [
            'title'  => 'Pesanan Saya',
            'orders' => $orders->paginate(10),
            'pager'  => $orders->pager,
            'status' => $status,
        ]);
    }

    public function show(string $code): string
    {
        $order = $this->findOwnedOrder($code);

        return view('buyer/orders/show', [
            'title' => 'Pesanan ' . $order['code'],
            'order' => $order,
            'items' => model(OrderItemModel::class)->forOrder((int) $order['id']),
            'shop'  => config(Shop::class),
        ]);
    }

    public function proof(string $code): ResponseInterface
    {
        $order = $this->findOwnedOrder($code);

        return self::proofResponse($this->response, $order);
    }

    public function uploadPayment(string $code): RedirectResponse
    {
        $order = $this->findOwnedOrder($code);
        $rules = [
            'payment_proof' => [
                'rules'  => 'uploaded[payment_proof]|max_size[payment_proof,2048]|is_image[payment_proof]|mime_in[payment_proof,image/jpeg,image/png,image/webp]|ext_in[payment_proof,jpg,jpeg,png,webp]',
                'errors' => [
                    'uploaded' => 'Pilih file bukti transfer terlebih dahulu.',
                    'max_size' => 'Ukuran bukti transfer maksimal 2 MB.',
                    'is_image' => 'Bukti transfer harus berupa gambar.',
                    'mime_in'  => 'Bukti transfer harus berformat JPG, PNG, atau WEBP.',
                    'ext_in'   => 'Ekstensi file harus jpg, jpeg, png, atau webp.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return $this->backWithErrors($this->validator->getErrors());
        }

        $file = $this->request->getFile('payment_proof');
        $name = $file->getRandomName();
        $file->move(self::PROOF_DIRECTORY, $name);

        try {
            (new OrderService())->move($order, 'awaiting_verification', ['payment_proof' => $name]);
        } catch (RuntimeException $exception) {
            unlink(self::PROOF_DIRECTORY . $name);

            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->to('orders/' . $code)->with('success', 'Bukti transfer terkirim. Pesanan akan diproses setelah pembayaran diverifikasi admin.');
    }

    public function cancel(string $code): RedirectResponse
    {
        $order = $this->findOwnedOrder($code);

        if ($order['status'] !== 'awaiting_payment') {
            return redirect()->back()->with('error', 'Pesanan hanya dapat dibatalkan selama masih menunggu pembayaran.');
        }

        try {
            (new OrderService())->move($order, 'cancelled');
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->to('orders/' . $code)->with('success', 'Pesanan ' . $code . ' dibatalkan dan stok produk dikembalikan.');
    }

    public function complete(string $code): RedirectResponse
    {
        $order = $this->findOwnedOrder($code);

        if ($order['status'] !== 'shipped') {
            return redirect()->back()->with('error', 'Pesanan belum dikirim.');
        }

        try {
            (new OrderService())->move($order, 'completed');
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->to('orders/' . $code)->with('success', 'Terima kasih! Pesanan ' . $code . ' telah selesai.');
    }

    public static function proofResponse(ResponseInterface $response, array $order): ResponseInterface
    {
        $path = self::PROOF_DIRECTORY . $order['payment_proof'];

        if (empty($order['payment_proof']) || ! is_file($path)) {
            throw PageNotFoundException::forPageNotFound('Bukti transfer tidak ditemukan.');
        }

        return $response
            ->setHeader('Content-Type', mime_content_type($path) ?: 'application/octet-stream')
            ->setHeader('Cache-Control', 'private, max-age=600')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody((string) file_get_contents($path));
    }

    private function findOwnedOrder(string $code): array
    {
        $order = model(OrderModel::class)->findOwnedByCode($code, $this->currentUserId());

        if ($order === null) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        }

        return $order;
    }
}
