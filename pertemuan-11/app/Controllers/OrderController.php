<?php

namespace App\Controllers;

use App\Libraries\OrderException;
use App\Libraries\OrderService;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\OrderStatusLogModel;

/** Pesanan milik customer yang sedang login (BR-006). */
class OrderController extends BaseController
{
    public function index()
    {
        $model  = new OrderModel();
        $orders = $model->withRelations()
            ->where('orders.user_id', $this->userId())
            ->orderBy('orders.id', 'DESC')
            ->paginate(10);

        return view('frontend/orders/index', [
            'title'  => 'Riwayat Pesanan',
            'orders' => $orders,
            'pager'  => $model->pager,
        ]);
    }

    public function show(int $id)
    {
        $order = $this->ownedOrder($id);

        return view('frontend/orders/show', [
            'title' => 'Pesanan ' . $order['order_number'],
            'order' => $order,
            'items' => (new OrderItemModel())->forOrder($id),
            'logs'  => (new OrderStatusLogModel())->forOrder($id),
            'bank'  => config('Shop')->bankAccount,
        ]);
    }

    public function cancel(int $id)
    {
        $this->ownedOrder($id);

        try {
            (new OrderService())->cancel($id, $this->userId(), 'customer', 'Dibatalkan oleh customer');
        } catch (OrderException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/orders/' . $id)->with('success', 'Pesanan dibatalkan.');
    }

    public function received(int $id)
    {
        $this->ownedOrder($id);

        try {
            (new OrderService())->complete($id, $this->userId(), 'customer');
        } catch (OrderException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/orders/' . $id)->with('success', 'Terima kasih! Pesanan ditandai selesai.');
    }

    /** Ambil pesanan HANYA jika milik user login; selain itu 404. */
    private function ownedOrder(int $id): array
    {
        $order = (new OrderModel())->findDetail($id);

        if (! $order || (int) $order['user_id'] !== $this->userId()) {
            return $this->notFound('Pesanan tidak ditemukan.');
        }

        return $order;
    }
}
