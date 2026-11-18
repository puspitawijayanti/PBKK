<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\OrderException;
use App\Libraries\OrderService;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\OrderStatusLogModel;

/** Pesanan di sisi admin/operator. Hak aksi dibatasi oleh route filter + OrderService. */
class OrderController extends BaseController
{
    public function index()
    {
        $filters = [
            'status' => (string) $this->request->getGet('status'),
            'q'      => trim((string) $this->request->getGet('q')),
        ];

        $model = new OrderModel();
        $query = $model->withRelations();

        if (in_array($filters['status'], OrderModel::STATUSES, true)) {
            $query->where('orders.order_status', $filters['status']);
        }
        if ($filters['q'] !== '') {
            $query->groupStart()
                ->like('orders.order_number', $filters['q'])
                ->orLike('users.name', $filters['q'])
                ->groupEnd();
        }

        return view('admin/orders/index', [
            'title'   => 'Pesanan',
            'orders'  => $query->orderBy('orders.id', 'DESC')->paginate(config('Shop')->adminPerPage),
            'pager'   => $model->pager,
            'filters' => $filters,
        ]);
    }

    public function show(int $id)
    {
        $order = (new OrderModel())->findDetail($id);
        if (! $order) {
            return $this->notFound('Pesanan tidak ditemukan.');
        }

        return view('admin/orders/show', [
            'title'    => 'Pesanan ' . $order['order_number'],
            'order'    => $order,
            'items'    => (new OrderItemModel())->forOrder($id),
            'logs'     => (new OrderStatusLogModel())->forOrder($id),
            'couriers' => config('Shop')->couriers,
        ]);
    }

    public function ship(int $id)
    {
        $rules = [
            'courier'         => ['label' => 'Kurir', 'rules' => 'required|max_length[100]'],
            'tracking_number' => ['label' => 'Nomor resi', 'rules' => 'required|min_length[5]|max_length[100]|alpha_numeric_punct'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        try {
            (new OrderService())->ship(
                $id,
                $this->userId(),
                trim((string) $this->request->getPost('courier')),
                trim((string) $this->request->getPost('tracking_number'))
            );
        } catch (OrderException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/orders/' . $id)->with('success', 'Data pengiriman disimpan.');
    }

    public function complete(int $id)
    {
        try {
            (new OrderService())->complete($id, $this->userId(), $this->role());
        } catch (OrderException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/orders/' . $id)->with('success', 'Pesanan ditandai selesai.');
    }

    /** Admin saja (lihat routes). Tidak ada penghapusan fisik pesanan. */
    public function cancel(int $id)
    {
        $reason = trim((string) $this->request->getPost('reason')) ?: 'Dibatalkan oleh admin';

        try {
            (new OrderService())->cancel($id, $this->userId(), $this->role(), $reason);
        } catch (OrderException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/orders/' . $id)->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }
}
