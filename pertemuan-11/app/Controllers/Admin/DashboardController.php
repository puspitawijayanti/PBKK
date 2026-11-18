<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $orders = new OrderModel();

        // ringkasan penjualan 14 hari terakhir (isi hari kosong dengan 0 agar grafik rapi)
        $to   = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-13 days'));
        $byDay = [];
        foreach ($orders->dailySales($from, $to) as $r) {
            $byDay[$r['tanggal']] = $r;
        }
        $labels = $sales = $counts = [];
        for ($d = strtotime($from); $d <= strtotime($to); $d = strtotime('+1 day', $d)) {
            $key      = date('Y-m-d', $d);
            $labels[] = date('d M', $d);
            $sales[]  = (float) ($byDay[$key]['penjualan'] ?? 0);
            $counts[] = (int) ($byDay[$key]['jumlah'] ?? 0);
        }

        $statusDist = [];
        foreach ($orders->statusDistribution() as $r) {
            $statusDist[order_status_label($r['order_status'])] = (int) $r['jumlah'];
        }

        $data = [
            'title' => 'Dashboard',
            'stats' => [
                'products'        => (new ProductModel())->countAllResults(),
                'customers'       => (new UserModel())->where('role', 'customer')->countAllResults(),
                'orders'          => $orders->countAllResults(),
                'sales'           => $orders->totalSales(),
                'pending'         => $orders->where('order_status', 'pending_payment')->countAllResults(),
                'awaitingVerify'  => (new PaymentModel())->where('payment_status', 'submitted')->countAllResults(),
                'toShip'          => (new OrderModel())->where('order_status', 'processing')->countAllResults(),
            ],
            'chart' => [
                'labels'     => $labels,
                'sales'      => $sales,
                'counts'     => $counts,
                'statusDist' => $statusDist,
            ],
            'recentOrders' => (new OrderModel())->withRelations()->orderBy('orders.id', 'DESC')->limit(8)->findAll(),
            'topProducts'  => (new OrderModel())->topProducts(5),
            'lowStock'     => (new ProductModel())->where('status', 'active')->where('stock <=', 5)->orderBy('stock', 'ASC')->limit(6)->findAll(),
        ];

        return view('admin/dashboard', $data);
    }
}
