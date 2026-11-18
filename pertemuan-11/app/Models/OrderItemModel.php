<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table         = 'order_items';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['order_id', 'product_id', 'product_name', 'price', 'quantity', 'subtotal'];
    protected $useTimestamps = false;

    public function forOrder(int $orderId): array
    {
        return $this->select('order_items.*, products.image, products.slug')
            ->join('products', 'products.id = order_items.product_id', 'left')
            ->where('order_id', $orderId)
            ->orderBy('order_items.id')
            ->findAll();
    }
}
