<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;

/** Laporan penjualan berdasarkan periode dan status (admin). */
class ReportController extends BaseController
{
    public function index()
    {
        $from   = $this->validDate($this->request->getGet('from')) ?? date('Y-m-01');
        $to     = $this->validDate($this->request->getGet('to')) ?? date('Y-m-d');
        $status = (string) $this->request->getGet('status');

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $orders = new OrderModel();
        $query  = $orders->withRelations()
            ->where('orders.order_date >=', $from . ' 00:00:00')
            ->where('orders.order_date <=', $to . ' 23:59:59');
        if (in_array($status, OrderModel::STATUSES, true)) {
            $query->where('orders.order_status', $status);
        }
        $rows = $query->orderBy('orders.order_date', 'DESC')->findAll();

        // ringkasan dihitung dari baris yang tampil
        $salesStatuses = config('Shop')->salesStatuses;
        $summary       = ['orders' => count($rows), 'sales' => 0.0, 'byStatus' => []];
        foreach ($rows as $r) {
            $summary['byStatus'][$r['order_status']] = ($summary['byStatus'][$r['order_status']] ?? 0) + 1;
            if (in_array($r['order_status'], $salesStatuses, true)) {
                $summary['sales'] += (float) $r['total'];
            }
        }

        if ($this->request->getGet('export') === 'csv') {
            return $this->csv($rows, $from, $to);
        }

        return view('admin/reports/index', [
            'title'   => 'Laporan Penjualan',
            'rows'    => $rows,
            'daily'   => $orders->dailySales($from, $to),
            'summary' => $summary,
            'from'    => $from,
            'to'      => $to,
            'status'  => $status,
        ]);
    }

    private function validDate($value): ?string
    {
        $value = (string) $value;
        $d     = \DateTime::createFromFormat('Y-m-d', $value);

        return ($d && $d->format('Y-m-d') === $value) ? $value : null;
    }

    private function csv(array $rows, string $from, string $to)
    {
        $fh = fopen('php://temp', 'r+');
        fputcsv($fh, ['No. Pesanan', 'Tanggal', 'Customer', 'Subtotal', 'Ongkir', 'Total', 'Status Pesanan', 'Status Pembayaran']);
        foreach ($rows as $r) {
            // awali sel yang berpotensi formula dengan apostrof (CSV injection)
            $safe = static fn (string $v): string => preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v;
            fputcsv($fh, [
                $r['order_number'], $r['order_date'], $safe($r['customer_name']),
                $r['subtotal'], $r['shipping_cost'], $r['total'],
                order_status_label($r['order_status']), payment_status_label((string) $r['payment_status']),
            ]);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setContentType('text/csv')
            ->setHeader('Content-Disposition', "attachment; filename=\"laporan-penjualan-{$from}_{$to}.csv\"")
            ->setBody("\xEF\xBB\xBF" . $csv);
    }
}
