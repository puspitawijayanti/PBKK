<?php

namespace App\Controllers\Admin;

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
 * Manajemen pesanan: daftar, detail, verifikasi pembayaran (admin), pengiriman (admin/operator).
 */
class OrderController extends BaseController
{
    public function index(): string
    {
        $orders  = model(OrderModel::class);
        $keyword = trim((string) $this->request->getGet('q'));
        $status  = (string) $this->request->getGet('status');
        $payment = (string) $this->request->getGet('payment');
        $page    = Paginator::paginate($orders->search($keyword, $status, $payment), config(Shop::class)->adminPerPage);

        return view('admin/orders/index', [
            'title'        => 'Pesanan',
            'orders'       => $page['items'],
            'pagerLinks'   => $page['links'],
            'statusCounts' => $orders->countByStatus(),
            'keyword'      => $keyword,
            'status'       => $status,
            'payment'      => $payment,
        ]);
    }

    public function show(int $id): string
    {
        $order = $this->findOrder($id);

        return view('admin/orders/show', [
            'title'    => 'Pesanan ' . $order['order_number'],
            'order'    => $order,
            'items'    => model(OrderItemModel::class)->forOrder($id),
            'payment'  => model(PaymentModel::class)->forOrder($id),
            'shipment' => model(ShipmentModel::class)->forOrder($id),
            'logs'     => model(OrderLogModel::class)->forOrder($id),
            'couriers' => ShipmentModel::COURIERS,
        ]);
    }

    public function proof(int $id): ResponseInterface
    {
        return UploadService::servePaymentProof($this->response, $this->findOrder($id)['proof_file']);
    }

    public function verify(int $id): RedirectResponse
    {
        $order    = $this->findOrder($id);
        $decision = (string) $this->request->getPost('decision');

        if (! in_array($decision, ['approve', 'reject'], true)) {
            return redirect()->back()->with('error', 'Pilihan verifikasi tidak valid.');
        }

        return $this->run($id, fn () => (new OrderService())->verifyPayment(
            $order,
            current_user()['id'],
            $decision === 'approve',
            trim((string) $this->request->getPost('note')),
        ), $decision === 'approve' ? 'Pembayaran diverifikasi. Pesanan masuk tahap diproses.' : 'Bukti pembayaran ditolak. Customer dapat mengunggah ulang.');
    }

    public function ship(int $id): RedirectResponse
    {
        $order = $this->findOrder($id);
        $rules = [
            'courier'         => ['label' => 'Kurir', 'rules' => 'required|in_list[' . implode(',', ShipmentModel::COURIERS) . ']'],
            'tracking_number' => ['label' => 'Nomor resi', 'rules' => 'required|alpha_numeric|min_length[6]|max_length[60]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(url_to('admin.order.show', $id))->withInput()->with('errors', $this->validator->getErrors());
        }

        return $this->run($id, fn () => (new OrderService())->ship(
            $order,
            current_user()['id'],
            (string) $this->request->getPost('courier'),
            strtoupper((string) $this->request->getPost('tracking_number')),
        ), 'Data pengiriman disimpan. Status pesanan: Dikirim.');
    }

    public function complete(int $id): RedirectResponse
    {
        $order = $this->findOrder($id);

        return $this->run($id, fn () => (new OrderService())->complete($order, current_user()['id'], 'Pesanan ditandai selesai oleh ' . role_label(current_user()['role']) . '.'), 'Pesanan ditandai selesai.');
    }

    public function cancel(int $id): RedirectResponse
    {
        $order = $this->findOrder($id);
        $note  = trim((string) $this->request->getPost('note'));

        return $this->run($id, fn () => (new OrderService())->cancel($order, current_user()['id'], 'Dibatalkan admin' . ($note !== '' ? ': ' . $note : '.')), 'Pesanan dibatalkan dan stok dikembalikan.');
    }

    private function run(int $id, callable $action, string $success): RedirectResponse
    {
        try {
            $action();
        } catch (RuntimeException $e) {
            return redirect()->to(url_to('admin.order.show', $id))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('admin.order.show', $id))->with('success', $success);
    }

    private function findOrder(int $id): array
    {
        $order = model(OrderModel::class)->findDetailed($id);

        if ($order === null) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        }

        return $order;
    }
}
