<?php

namespace App\Models;

use CodeIgniter\Model;

class CartItemModel extends Model
{
    protected $table         = 'cart_items';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['cart_id', 'product_id', 'quantity'];
    protected $useTimestamps = true;

    /**
     * Item keranjang + data produk terbaru (harga & stok selalu dari tabel products).
     * Setiap item mendapat `line_total` = harga x kuantitas.
     */
    public function itemsForUser(int $userId): array
    {
        $rows = $this->select('cart_items.id, cart_items.cart_id, cart_items.product_id, cart_items.quantity,
                products.name, products.slug, products.price, products.stock, products.image, products.status')
            ->join('carts', 'carts.id = cart_items.cart_id')
            ->join('products', 'products.id = cart_items.product_id')
            ->where('carts.user_id', $userId)
            ->orderBy('cart_items.id', 'ASC')
            ->findAll();

        foreach ($rows as &$r) {
            $r['line_total'] = (float) $r['price'] * (int) $r['quantity'];
        }

        return $rows;
    }

    public function countForUser(int $userId): int
    {
        return $this->join('carts', 'carts.id = cart_items.cart_id')
            ->where('carts.user_id', $userId)
            ->countAllResults();
    }

    /** Cari item berdasarkan id, sekaligus pastikan milik user tersebut. */
    public function findOwned(int $itemId, int $userId): ?array
    {
        return $this->select('cart_items.*')
            ->join('carts', 'carts.id = cart_items.cart_id')
            ->where('cart_items.id', $itemId)
            ->where('carts.user_id', $userId)
            ->first();
    }
}
