<?php

namespace App\Services;

use App\Models\CartItemModel;
use App\Models\ProductModel;
use Config\Shop;
use RuntimeException;

/**
 * Keranjang belanja customer (FR-011).
 * Status dan stok produk selalu dicek ulang di server (PRD 13: Keranjang, BR-002, BR-003).
 */
class CartService
{
    private CartItemModel $cartItems;

    public function __construct(private readonly int $userId)
    {
        $this->cartItems = model(CartItemModel::class);
    }

    public function add(int $productId, int $quantity): string
    {
        $product = model(ProductModel::class)->findActive($productId);

        if ($product === null) {
            throw new RuntimeException('Produk tidak ditemukan atau sudah tidak dijual.');
        }

        if ($quantity < 1) {
            throw new RuntimeException('Jumlah minimal 1.');
        }

        $existing = $this->cartItems->firstWhere(['user_id' => $this->userId, 'product_id' => $productId]);
        $newQty   = ($existing['quantity'] ?? 0) + $quantity;

        if ($newQty > $product['stock']) {
            throw new RuntimeException($product['stock'] === 0
                ? $product['name'] . ' sedang habis.'
                : 'Stok ' . $product['name'] . ' tersisa ' . $product['stock'] . ($existing ? ', dan ' . $existing['quantity'] . ' sudah ada di keranjang.' : '.'));
        }

        if ($existing === null) {
            $this->cartItems->insert(['user_id' => $this->userId, 'product_id' => $productId, 'quantity' => $newQty]);
        } else {
            $this->cartItems->update($existing['id'], ['quantity' => $newQty]);
        }

        return $product['name'] . ' ditambahkan ke keranjang.';
    }

    public function update(int $itemId, int $quantity): void
    {
        $item = $this->ownedItem($itemId);

        if ($quantity < 1) {
            throw new RuntimeException('Jumlah minimal 1. Gunakan tombol hapus untuk mengeluarkan produk.');
        }

        if ($quantity > $item['stock']) {
            throw new RuntimeException('Stok ' . $item['name'] . ' hanya tersisa ' . $item['stock'] . '.');
        }

        $this->cartItems->update($itemId, ['quantity' => $quantity]);
    }

    public function remove(int $itemId): void
    {
        $this->ownedItem($itemId);
        $this->cartItems->delete($itemId);
    }

    public function clear(): void
    {
        foreach ($this->cartItems->where(['user_id' => $this->userId]) as $item) {
            $this->cartItems->delete($item['id']);
        }
    }

    /**
     * Item keranjang dengan harga terkini dan penanda validitas.
     */
    public function items(): array
    {
        return array_map(static function (array $item): array {
            $item['subtotal']    = $item['price'] * $item['quantity'];
            $item['available']   = $item['status'] === 'active';
            $item['enoughStock'] = $item['quantity'] <= $item['stock'];

            return $item;
        }, $this->cartItems->withProducts($this->userId));
    }

    public function count(): int
    {
        return (int) array_sum(array_column($this->cartItems->where(['user_id' => $this->userId]), 'quantity'));
    }

    /**
     * Total dihitung di backend (BR-005). Item yang tidak valid tidak ikut dihitung.
     */
    public function totals(array $items): array
    {
        $valid    = array_filter($items, static fn (array $i): bool => $i['available'] && $i['enoughStock']);
        $subtotal = (float) array_sum(array_column($valid, 'subtotal'));
        $shipping = $subtotal > 0 ? (float) config(Shop::class)->flatShippingCost : 0.0;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total'    => $subtotal + $shipping,
            'valid'    => count($valid) === count($items) && $items !== [],
        ];
    }

    private function ownedItem(int $itemId): array
    {
        foreach ($this->cartItems->withProducts($this->userId) as $item) {
            if ($item['id'] === $itemId) {
                return $item;
            }
        }

        throw new RuntimeException('Item keranjang tidak ditemukan.');
    }
}
