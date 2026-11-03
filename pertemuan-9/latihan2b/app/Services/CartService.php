<?php

namespace App\Services;

use App\Models\ProductModel;
use Config\Shop;

class CartService
{
    private const SESSION_KEY = 'cart';

    private Shop $config;

    public function __construct()
    {
        $this->config = config(Shop::class);
    }

    public function raw(): array
    {
        return session(self::SESSION_KEY) ?? [];
    }

    public function add(int $productId, int $quantity): void
    {
        $cart = $this->raw();
        $cart[$productId] = min($this->config->maxQuantityPerItem, ($cart[$productId] ?? 0) + max(1, $quantity));
        $this->save($cart);
    }

    public function set(int $productId, int $quantity): void
    {
        $cart = $this->raw();

        if ($quantity < 1) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = min($this->config->maxQuantityPerItem, $quantity);
        }

        $this->save($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        $this->save($cart);
    }

    public function clear(): void
    {
        session()->remove(self::SESSION_KEY);
    }

    public function items(): array
    {
        $cart = $this->raw();

        if ($cart === []) {
            return [];
        }

        $products = model(ProductModel::class)
            ->withCategory()
            ->whereIn('products.id', array_keys($cart))
            ->where('products.is_active', 1)
            ->findAll();

        $items = [];

        foreach ($products as $product) {
            $quantity = (int) $cart[$product['id']];
            $items[] = [
                'product'      => $product,
                'quantity'     => $quantity,
                'price'        => (int) $product['price'],
                'subtotal'     => (int) $product['price'] * $quantity,
                'enoughStock'  => $quantity <= (int) $product['stock'],
            ];
        }

        if (count($products) !== count($cart)) {
            $this->save(array_intersect_key($cart, array_flip(array_column($products, 'id'))));
        }

        return $items;
    }

    public function totals(array $items): array
    {
        $subtotal = array_sum(array_column($items, 'subtotal'));
        $shipping = $subtotal === 0 || $subtotal > $this->config->freeShippingThreshold ? 0 : $this->config->shippingCost;

        return [
            'subtotal'     => $subtotal,
            'shipping'     => $shipping,
            'total'        => $subtotal + $shipping,
            'freeShipping' => $subtotal > $this->config->freeShippingThreshold,
            'missing'      => max(0, $this->config->freeShippingThreshold + 1 - $subtotal),
        ];
    }

    private function save(array $cart): void
    {
        session()->set(self::SESSION_KEY, $cart);
    }
}
