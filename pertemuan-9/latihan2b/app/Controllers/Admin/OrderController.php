<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Controllers\Buyer\OrderController as BuyerOrderController;
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
    public function index(): string
    {
        $orders = model(OrderModel::class);
        $keyword = trim((string) $this->request->getGet('q'));
        $status = (string) $this->request->getGet('status');
        $counts = $orders->countByStatus();
        $query = $orders->search($keyword, $status);

        return view('admin/orders/index', [
            'title'   => 'Pesanan',
            'orders'  => $query->paginate(10),
            'pager'   => $query->pager,
            'counts'  => $counts,
            'keyword' => $keyword,
            'status'  => $status,
        ]);
    }

    public function show(int $id): string
    {
        $order = $this->findOrder($id);

        return view('admin/orders/show', [
            'title' => 'Pesanan ' . $order['code'],
            'order' => $order,
            'items' => model(OrderItemModel::class)->forOrder($id),
            'shop'  => config(Shop::class),
        ]);
    }

    public function proof(int $id): ResponseInterface
    {
        return BuyerOrderController::proofResponse($this->response, $this->findOrder($id));
    }

    public function updateStatus(int $id): RedirectResponse
    {
        $order = $this->findOrder($id);
        $target = (string) $this->request->getPost('status');
        $extra = [];

        if ($target === 'awaiting_verification') {
            return redirect()->back()->with('error', 'Status ini hanya dapat diubah oleh pembeli melalui unggah bukti transfer.');
        }

        if ($target === 'shipped') {
            $rules = [
                'courier'         => ['rules' => 'required|in_list[' . implode(',', config(Shop::class)->couriers) . ']', 'errors' => ['required' => 'Kurir wajib dipilih.', 'in_list' => 'Kurir tidak valid.']],
                'tracking_number' => ['rules' => 'required|alpha_numeric|min_length[8]|max_length[30]', 'errors' => ['required' => 'Nomor resi wajib diisi.', 'alpha_numeric' => 'Nomor resi hanya boleh huruf dan angka.', 'min_length' => 'Nomor resi minimal 8 karakter.', 'max_length' => 'Nomor resi maksimal 30 karakter.']],
            ];

            if (! $this->validate($rules)) {
                return $this->backWithErrors($this->validator->getErrors());
            }

            $extra = $this->validator->getValidated();
            $extra['tracking_number'] = strtoupper($extra['tracking_number']);
        }

        try {
            $updated = (new OrderService())->move($order, $target, $extra);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        $message = 'Status pesanan ' . $order['code'] . ' diubah menjadi "' . order_status_label($target) . '".';

        if ($target === 'cancelled') {
            $message .= ' Stok produk telah dikembalikan.';
        }

        if ($target === 'awaiting_payment' && $order['payment_proof'] !== null) {
            $path = BuyerOrderController::PROOF_DIRECTORY . $order['payment_proof'];

            if (is_file($path)) {
                unlink($path);
            }
        }

        return redirect()->to('admin/orders/' . $updated['id'])->with('success', $message);
    }

    private function findOrder(int $id): array
    {
        $order = model(OrderModel::class)->withBuyer()->where('orders.id', $id)->first();

        if ($order === null) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        }

        return $order;
    }
}
