<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id', 'address_id', 'order_number', 'order_date',
        'subtotal', 'shipping_cost', 'total', 'order_status',
        'shipping_address', 'notes',
    ];
    protected $useTimestamps = true;

    public const STATUSES = [
        'pending_payment', 'waiting_verification', 'processing',
        'shipped', 'completed', 'cancelled',
    ];

    /**
     * Mesin status pesanan: transisi yang DIIZINKAN (divalidasi di backend).
     *   pending_payment      -> waiting_verification (customer upload bukti)
     *                        -> cancelled
     *   waiting_verification -> processing           (admin verifikasi OK)
     *                        -> pending_payment      (admin tolak bukti, customer upload ulang)
     *                        -> cancelled
     *   processing           -> shipped              (operator/admin isi resi)
     *                        -> cancelled
     *   shipped              -> completed
     *   completed, cancelled -> (final)
     */
    public const TRANSITIONS = [
        'pending_payment'      => ['waiting_verification', 'cancelled'],
        'waiting_verification' => ['processing', 'pending_payment', 'cancelled'],
        'processing'           => ['shipped', 'cancelled'],
        'shipped'              => ['completed'],
        'completed'            => [],
        'cancelled'            => [],
    ];

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    /** Nomor pesanan unik, contoh: SE-20261118-4F9A2C */
    public function generateOrderNumber(): string
    {
        do {
            $number = 'SE-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        } while ($this->where('order_number', $number)->countAllResults() > 0);

        return $number;
    }

    /** Pesanan + info pembayaran & pengiriman + nama customer (untuk daftar admin/customer). */
    public function withRelations()
    {
        return $this->select('orders.*, users.name AS customer_name, users.email AS customer_email,
                payments.payment_status, payments.proof_file, shipments.shipment_status, shipments.courier, shipments.tracking_number')
            ->join('users', 'users.id = orders.user_id')
            ->join('payments', 'payments.order_id = orders.id', 'left')
            ->join('shipments', 'shipments.order_id = orders.id', 'left');
    }

    /** Detail satu pesanan beserta relasinya (tanpa cek kepemilikan). */
    public function findDetail(int $id): ?array
    {
        return $this->withRelations()
            ->select('payments.id AS payment_id, payments.method AS payment_method, payments.amount AS payment_amount,
                payments.note AS payment_note, payments.verified_at, payments.verified_by,
                shipments.shipped_at, shipments.delivered_at')
            ->where('orders.id', $id)
            ->first();
    }

    // ------------------------------------------------------------------
    // STATISTIK (dashboard & laporan) - Query Builder
    // ------------------------------------------------------------------

    /** Total penjualan = pesanan dengan status yang dianggap penjualan. */
    public function totalSales(): float
    {
        $row = $this->selectSum('total', 'sales')
            ->whereIn('order_status', config('Shop')->salesStatuses)
            ->first();

        return (float) ($row['sales'] ?? 0);
    }

    /** Rekap penjualan per hari dalam rentang tanggal (Y-m-d). */
    public function dailySales(string $from, string $to): array
    {
        return $this->select('DATE(order_date) AS tanggal, COUNT(*) AS jumlah, SUM(total) AS penjualan')
            ->whereIn('order_status', config('Shop')->salesStatuses)
            ->where('order_date >=', $from . ' 00:00:00')
            ->where('order_date <=', $to . ' 23:59:59')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->findAll();
    }

    /** Jumlah pesanan per status (distribusi). */
    public function statusDistribution(): array
    {
        return $this->select('order_status, COUNT(*) AS jumlah')
            ->groupBy('order_status')
            ->findAll();
    }

    /** Produk terlaris berdasarkan pesanan berstatus penjualan. */
    public function topProducts(int $limit = 5): array
    {
        return $this->db->table('order_items')
            ->select('order_items.product_name, SUM(order_items.quantity) AS qty, SUM(order_items.subtotal) AS revenue')
            ->join('orders', 'orders.id = order_items.order_id')
            ->whereIn('orders.order_status', config('Shop')->salesStatuses)
            ->groupBy('order_items.product_id, order_items.product_name')
            ->orderBy('qty', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}
