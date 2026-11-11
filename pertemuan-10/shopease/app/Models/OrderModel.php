<?php

namespace App\Models;

/**
 * Header pesanan. Status pesanan (order_status) terpisah dari status pembayaran di tabel payments (BR-008).
 */
class OrderModel extends JsonModel
{
    /** Status pesanan PRD 9.1 */
    public const STATUSES = ['pending_payment', 'waiting_verification', 'processing', 'shipped', 'completed', 'cancelled'];

    /** Transisi status yang diizinkan; divalidasi di backend (PRD 9.2 catatan). */
    public const FLOW = [
        'pending_payment'      => ['waiting_verification', 'cancelled'],
        'waiting_verification' => ['processing', 'pending_payment', 'cancelled'],
        'processing'           => ['shipped', 'cancelled'],
        'shipped'              => ['completed'],
        'completed'            => [],
        'cancelled'            => [],
    ];

    /** Status yang dihitung sebagai penjualan (pembayaran sudah diverifikasi). */
    public const PAID_STATUSES = ['processing', 'shipped', 'completed'];

    protected string $table = 'orders';

    public static function canMove(string $from, string $to): bool
    {
        return in_array($to, self::FLOW[$from] ?? [], true);
    }

    /**
     * Pesanan beserta data customer, status pembayaran, dan pengiriman (setara JOIN orders-users-payments-shipments).
     */
    public function detailed(array $order): array
    {
        $user     = model(UserModel::class)->find($order['user_id']);
        $payment  = model(PaymentModel::class)->firstWhere(['order_id' => $order['id']]);
        $shipment = model(ShipmentModel::class)->firstWhere(['order_id' => $order['id']]);

        return $order + [
            'customer_name'   => $user['name'] ?? '-',
            'customer_email'  => $user['email'] ?? '-',
            'payment_status'  => $payment['payment_status'] ?? null,
            'proof_file'      => $payment['proof_file'] ?? null,
            'payment_notes'   => $payment['notes'] ?? null,
            'shipment_status' => $shipment['shipment_status'] ?? null,
            'courier'         => $shipment['courier'] ?? null,
            'tracking_number' => $shipment['tracking_number'] ?? null,
        ];
    }

    /**
     * Pencarian pesanan: nomor pesanan, nama customer/penerima, status pesanan, status pembayaran, rentang tanggal.
     */
    public function search(?string $keyword = null, ?string $status = null, ?string $paymentStatus = null, ?int $userId = null, ?string $from = null, ?string $to = null): array
    {
        $keyword = trim((string) $keyword);
        $rows    = [];

        foreach ($this->all() as $order) {
            if (($userId !== null && $order['user_id'] !== $userId)
                || (in_array($status, self::STATUSES, true) && $order['order_status'] !== $status)
                || ($from !== null && $order['order_date'] < $from . ' 00:00:00')
                || ($to !== null && $order['order_date'] > $to . ' 23:59:59')) {
                continue;
            }

            $order = $this->detailed($order);

            if ((in_array($paymentStatus, PaymentModel::STATUSES, true) && $order['payment_status'] !== $paymentStatus)
                || ! (self::contains($order['order_number'], $keyword) || self::contains($order['customer_name'], $keyword) || self::contains($order['recipient_name'], $keyword))) {
                continue;
            }

            $rows[] = $order;
        }

        return self::sortBy($rows, [['order_date', 'desc'], ['id', 'desc']]);
    }

    public function findDetailed(int $id): ?array
    {
        $order = $this->find($id);

        return $order === null ? null : $this->detailed($order);
    }

    /**
     * Pesanan milik customer tertentu saja (BR-006).
     */
    public function findOwnedByNumber(string $orderNumber, int $userId): ?array
    {
        $order = $this->firstWhere(['order_number' => $orderNumber, 'user_id' => $userId]);

        return $order === null ? null : $this->detailed($order);
    }

    public function countByStatus(): array
    {
        $counts = array_fill_keys(self::STATUSES, 0);

        foreach ($this->all() as $order) {
            $counts[$order['order_status']]++;
        }

        return $counts;
    }

    public function revenue(?string $from = null, ?string $to = null): float
    {
        $orders = array_filter($this->all(), static fn (array $o): bool => in_array($o['order_status'], self::PAID_STATUSES, true)
            && ($from === null || $o['order_date'] >= $from . ' 00:00:00')
            && ($to === null || $o['order_date'] <= $to . ' 23:59:59'));

        return (float) array_sum(array_column($orders, 'total'));
    }

    public function monthlyRevenue(int $months = 6): array
    {
        $start  = date('Y-m-01', strtotime('-' . ($months - 1) . ' months'));
        $result = [];

        for ($i = 0; $i < $months; $i++) {
            $result[date('Y-m', strtotime($start . ' +' . $i . ' months'))] = ['revenue' => 0.0, 'orders' => 0];
        }

        foreach ($this->all() as $order) {
            $period = substr($order['order_date'], 0, 7);

            if (isset($result[$period]) && in_array($order['order_status'], self::PAID_STATUSES, true)) {
                $result[$period]['revenue'] += (float) $order['total'];
                $result[$period]['orders']++;
            }
        }

        return $result;
    }

    public function generateNumber(): string
    {
        $prefix = 'SE-' . date('Ymd') . '-';
        $max    = 0;

        foreach ($this->all() as $order) {
            if (str_starts_with($order['order_number'], $prefix)) {
                $max = max($max, (int) substr($order['order_number'], -4));
            }
        }

        return $prefix . str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }
}
