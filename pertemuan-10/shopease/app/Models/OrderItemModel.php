<?php

namespace App\Models;

/**
 * Rincian pesanan dengan snapshot nama dan harga produk (BR-004).
 */
class OrderItemModel extends JsonModel
{
    protected string $table = 'order_items';

    public function forOrder(int $orderId): array
    {
        $products = model(ProductModel::class);

        return array_map(static function (array $item) use ($products): array {
            $product = $item['product_id'] !== null ? $products->find($item['product_id']) : null;

            return $item + [
                'slug'        => $product['slug'] ?? null,
                'icon'        => $product['icon'] ?? 'bi-box',
                'image'       => $product['image'] ?? null,
                'category_id' => $product['category_id'] ?? 0,
            ];
        }, self::sortBy($this->where(['order_id' => $orderId]), [['id', 'asc']]));
    }

    /**
     * Produk terlaris berdasarkan jumlah terjual pada pesanan yang sudah dibayar.
     */
    public function bestSellers(int $limit = 5): array
    {
        $paidOrders = array_column(array_filter(
            model(OrderModel::class)->all(),
            static fn (array $o): bool => in_array($o['order_status'], OrderModel::PAID_STATUSES, true),
        ), 'id');
        $totals = [];

        foreach ($this->all() as $item) {
            if (in_array($item['order_id'], $paidOrders, true)) {
                $totals[$item['product_name']] ??= ['product_name' => $item['product_name'], 'sold' => 0, 'revenue' => 0.0];
                $totals[$item['product_name']]['sold'] += $item['quantity'];
                $totals[$item['product_name']]['revenue'] += $item['subtotal'];
            }
        }

        return array_slice(self::sortBy(array_values($totals), [['sold', 'desc']]), 0, $limit);
    }
}
