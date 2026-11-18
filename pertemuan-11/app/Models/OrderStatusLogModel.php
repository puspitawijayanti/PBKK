<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderStatusLogModel extends Model
{
    protected $table         = 'order_status_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['order_id', 'old_status', 'new_status', 'changed_by', 'note', 'created_at'];
    protected $useTimestamps = false;

    public function forOrder(int $orderId): array
    {
        return $this->select('order_status_logs.*, users.name AS actor_name')
            ->join('users', 'users.id = order_status_logs.changed_by', 'left')
            ->where('order_id', $orderId)
            ->orderBy('order_status_logs.id', 'ASC')
            ->findAll();
    }
}
