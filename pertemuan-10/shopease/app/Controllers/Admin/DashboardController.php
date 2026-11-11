<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;

/**
 * Ringkasan jumlah produk, pelanggan, pesanan, dan penjualan (FR-018).
 */
class DashboardController extends BaseController
{
    public function index(): string
    {
        $orders = model(OrderModel::class);

        return view('admin/dashboard', [
            'title'        => 'Dashboard',
            'stats'        => [
                'products'  => model(ProductModel::class)->count(['status' => 'active']),
                'customers' => model(UserModel::class)->count(['role' => 'customer']),
                'orders'    => $orders->count(),
                'revenue'   => $orders->revenue(),
            ],
            'statusCounts' => $orders->countByStatus(),
            'monthly'      => $orders->monthlyRevenue(6),
            'recentOrders' => array_slice($orders->search(), 0, 6),
            'bestSellers'  => model(OrderItemModel::class)->bestSellers(5),
            'lowStock'     => model(ProductModel::class)->countLowStock(),
        ]);
    }
}
