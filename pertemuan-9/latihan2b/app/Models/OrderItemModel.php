<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table         = 'order_items';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['order_id', 'product_id', 'product_name', 'price', 'quantity', 'subtotal'];
    protected $useTimestamps = true;

    public function forOrder(int $orderId): array
    {
        return $this->select('order_items.*, products.slug AS product_slug, products.photo AS product_photo')
            ->join('products', 'products.id = order_items.product_id', 'left')
            ->where('order_items.order_id', $orderId)
            ->orderBy('order_items.id')
            ->findAll();
    }

    public function bestSellers(int $limit = 5): array
    {
        return $this->db->table($this->table)
            ->select('order_items.product_name, SUM(order_items.quantity) AS sold, SUM(order_items.subtotal) AS revenue')
            ->join('orders', 'orders.id = order_items.order_id')
            ->whereIn('orders.status', OrderModel::PAID_STATUSES)
            ->groupBy('order_items.product_name')
            ->orderBy('sold', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
