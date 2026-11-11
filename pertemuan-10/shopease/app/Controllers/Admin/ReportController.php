<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;

/**
 * Rekap penjualan berdasarkan periode dan status (FR-019).
 */
class ReportController extends BaseController
{
    public function index(): string
    {
        $from   = $this->validDate($this->request->getGet('from')) ?? date('Y-m-01');
        $to     = $this->validDate($this->request->getGet('to')) ?? date('Y-m-d');
        $status = (string) $this->request->getGet('status');

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $orders = model(OrderModel::class)->search(null, $status, null, null, $from, $to);

        $summary = array_fill_keys(OrderModel::STATUSES, ['count' => 0, 'total' => 0.0]);

        foreach ($orders as $order) {
            $summary[$order['order_status']]['count']++;
            $summary[$order['order_status']]['total'] += (float) $order['total'];
        }

        return view('admin/reports/index', [
            'title'   => 'Laporan Penjualan',
            'orders'  => $orders,
            'summary' => $summary,
            'revenue' => model(OrderModel::class)->revenue($from, $to),
            'from'    => $from,
            'to'      => $to,
            'status'  => $status,
        ]);
    }

    private function validDate(mixed $value): ?string
    {
        return is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) && strtotime($value) !== false ? $value : null;
    }
}
