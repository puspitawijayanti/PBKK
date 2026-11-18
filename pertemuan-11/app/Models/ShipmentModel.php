<?php

namespace App\Models;

use CodeIgniter\Model;

class ShipmentModel extends Model
{
    protected $table         = 'shipments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'order_id', 'courier', 'tracking_number', 'shipment_status', 'shipped_at', 'delivered_at',
    ];
    protected $useTimestamps = true;

    public function withOrder()
    {
        return $this->select('shipments.*, orders.order_number, orders.order_status, orders.shipping_address,
                users.name AS customer_name')
            ->join('orders', 'orders.id = shipments.order_id')
            ->join('users', 'users.id = orders.user_id');
    }
}
