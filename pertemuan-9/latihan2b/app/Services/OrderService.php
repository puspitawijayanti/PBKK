<?php

namespace App\Services;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use RuntimeException;
use Throwable;

class OrderService
{
    private OrderModel $orders;

    public function __construct()
    {
        $this->orders = model(OrderModel::class);
    }

    public function checkout(int $userId, array $shipping, CartService $cart): array
    {
        $items = $cart->items();

        if ($items === []) {
            throw new RuntimeException('Keranjang belanja masih kosong.');
        }

        $totals = $cart->totals($items);
        $db = db_connect();
        $db->transBegin();

        try {
            $orderId = $this->orders->insert([
                'user_id'        => $userId,
                'code'           => $this->orders->generateCode(),
                'status'         => 'awaiting_payment',
                'recipient_name' => $shipping['recipient_name'],
                'phone'          => $shipping['phone'],
                'address'        => $shipping['address'],
                'city'           => $shipping['city'],
                'postal_code'    => $shipping['postal_code'],
                'note'           => ($shipping['note'] ?? '') ?: null,
                'subtotal'       => $totals['subtotal'],
                'shipping_cost'  => $totals['shipping'],
                'total'          => $totals['total'],
            ]);

            $orderItems = model(OrderItemModel::class);

            foreach ($items as $item) {
                $product = $item['product'];

                $db->table('products')
                    ->set('stock', 'stock - ' . (int) $item['quantity'], false)
                    ->set('updated_at', date('Y-m-d H:i:s'))
                    ->where('id', $product['id'])
                    ->where('stock >=', (int) $item['quantity'])
                    ->update();

                if ($db->affectedRows() !== 1) {
                    throw new RuntimeException('Stok ' . $product['name'] . ' tidak mencukupi. Sisa stok: ' . $product['stock'] . '.');
                }

                $orderItems->insert([
                    'order_id'     => $orderId,
                    'product_id'   => $product['id'],
                    'product_name' => $product['name'] . ' (' . $product['weight_gram'] . ' g)',
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();

            throw $exception;
        }

        $cart->clear();

        return $this->orders->find($orderId);
    }

    public function move(array $order, string $target, array $extra = []): array
    {
        if (! OrderModel::canMove($order['status'], $target)) {
            throw new RuntimeException('Status pesanan tidak dapat diubah dari "' . order_status_label($order['status']) . '" ke "' . order_status_label($target) . '".');
        }

        $now = date('Y-m-d H:i:s');
        $changes = ['status' => $target, 'updated_at' => $now];

        $changes += match ($target) {
            'awaiting_verification' => ['paid_at' => $now, 'payment_proof' => $extra['payment_proof'] ?? null],
            'awaiting_payment'      => ['paid_at' => null, 'payment_proof' => null],
            'processing'            => ['verified_at' => $now],
            'shipped'               => ['shipped_at' => $now, 'courier' => $extra['courier'] ?? null, 'tracking_number' => $extra['tracking_number'] ?? null],
            'completed'             => ['completed_at' => $now],
            'cancelled'             => ['cancelled_at' => $now],
            default                 => [],
        };

        if ($target === 'awaiting_verification' && empty($changes['payment_proof'])) {
            throw new RuntimeException('Bukti transfer wajib diunggah.');
        }

        if ($target === 'shipped' && (empty($changes['tracking_number']) || empty($changes['courier']))) {
            throw new RuntimeException('Kurir dan nomor resi wajib diisi sebelum pesanan dikirim.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $db->table('orders')->where('id', $order['id'])->where('status', $order['status'])->update($changes);

            if ($db->affectedRows() !== 1) {
                throw new RuntimeException('Status pesanan sudah berubah. Muat ulang halaman.');
            }

            if ($target === 'cancelled') {
                $this->restoreStock((int) $order['id']);
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();

            throw $exception;
        }

        return $this->orders->find($order['id']);
    }

    private function restoreStock(int $orderId): void
    {
        $db = db_connect();

        foreach (model(OrderItemModel::class)->where('order_id', $orderId)->where('product_id IS NOT NULL')->findAll() as $item) {
            $db->table('products')
                ->set('stock', 'stock + ' . (int) $item['quantity'], false)
                ->set('updated_at', date('Y-m-d H:i:s'))
                ->where('id', $item['product_id'])
                ->update();
        }
    }
}
