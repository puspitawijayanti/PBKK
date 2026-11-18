<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;

/** Antrean pengiriman: pesanan siap kirim & sedang dikirim (admin/operator). */
class ShipmentController extends BaseController
{
    public function index()
    {
        $tab = $this->request->getGet('tab') === 'shipped' ? 'shipped' : 'processing';

        $model  = new OrderModel();
        $orders = $model->withRelations()
            ->where('orders.order_status', $tab)
            ->orderBy('orders.id', 'ASC')
            ->paginate(config('Shop')->adminPerPage);

        return view('admin/shipments/index', [
            'title'    => 'Pengiriman',
            'orders'   => $orders,
            'pager'    => $model->pager,
            'tab'      => $tab,
            'couriers' => config('Shop')->couriers,
        ]);
    }
}
