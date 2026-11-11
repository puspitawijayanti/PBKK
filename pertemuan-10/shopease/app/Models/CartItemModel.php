<?php

namespace App\Models;

/**
 * Item keranjang (setara tabel carts + cart_items di PRD). Setiap item terikat langsung ke user_id customer.
 */
class CartItemModel extends JsonModel
{
    protected string $table = 'cart_items';

    /**
     * Item keranjang beserta data produk terkini (harga, stok, status).
     */
    public function withProducts(int $userId): array
    {
        $products = model(ProductModel::class);
        $items    = [];

        foreach (self::sortBy($this->where(['user_id' => $userId]), [['id', 'asc']]) as $item) {
            $product = $products->find($item['product_id']);

            if ($product === null) {
                continue;
            }

            $items[] = $item + [
                'name'        => $product['name'],
                'slug'        => $product['slug'],
                'price'       => (float) $product['price'],
                'stock'       => $product['stock'],
                'status'      => $product['status'],
                'icon'        => $product['icon'],
                'image'       => $product['image'],
                'category_id' => $product['category_id'],
            ];
        }

        return $items;
    }
}
