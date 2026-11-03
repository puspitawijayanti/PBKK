<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $orders = model(OrderModel::class);
        $monthly = $orders->monthlyRevenue(6);

        return view('admin/dashboard', [
            'title'          => 'Dashboard',
            'counts'         => $orders->countByStatus(),
            'revenueTotal'   => $orders->revenue(),
            'revenueMonth'   => $orders->revenue(date('Y-m-01 00:00:00')),
            'buyers'         => model(UserModel::class)->where('role', 'buyer')->countAllResults(),
            'monthLabels'    => array_map('month_label', array_keys($monthly)),
            'monthRevenue'   => array_column($monthly, 'revenue'),
            'monthOrders'    => array_column($monthly, 'orders'),
            'bestSellers'    => model(OrderItemModel::class)->bestSellers(5),
            'lowStock'       => model(ProductModel::class)->where('is_active', 1)->where('stock <=', 5)->orderBy('stock')->findAll(6),
            'toVerify'       => $orders->withBuyer()->where('orders.status', 'awaiting_verification')->orderBy('orders.paid_at')->findAll(5),
        ]);
    }
}
